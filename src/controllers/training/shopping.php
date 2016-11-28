<?php 
namespace Application\Controller\Training;
use Application\Controller\Training\Member;
use Concrete\Core\User\UserInfo;
use Controller;
use Group;
use User;
use Log;
use Loader;

class Shopping extends Controller {

	public function get_feature_group($tablePrefix, $featureID){
			$db = \Database::connection();
			$table = "C5CBT_".$tablePrefix."_features";
			return $db->getOne("SELECT featureGroup FROM $table WHERE featureID = $featureID");
	}
	
	public function is_transfer_eligible($items){
	$quantity = 0;
	$types = 0;
	foreach ($items as $key=>$item){
		$type[$item['tablePrefix']] = 1;
		$quantity = $quantity + $item['quantity'];
	}	
	if ($quantity > count($type)){
		return true;
	} else {
		return false;
	}
	
	}

	public function extract_courses($cart){
	$cart1 = var_export($cart,1);
		Log::addEntry("initial:{$cart1}", "extraction");
		foreach ($cart as $key=>$item){
		unset($bls_added);
		//echo "starting cart:";
		//var_dump($cart);
			$newcart[$key] = $item;
			if ($key != "addon") {
				if ($item['features']){
					
					if (is_array($item['features'])){
					
						foreach ($item['features'] as $featureGroup=>$featureID){
							$feature_name = shopping::get_feature_name($item['tablePrefix'], $featureID);
							$name_array = explode(' ',trim($feature_name));
							$feature_tablePrefix = strtolower($name_array[0]);
							$feature_price = shopping::get_feature_cost($item['tablePrefix'], $featureID);
							
							if ($feature_tablePrefix == "acls" || $feature_tablePrefix == "pals" || $feature_tablePrefix == "nrp"){
							//create new cart item 
								Log::addEntry("found feature:{$feature_tablePrefix}", "extraction");
								$newcourse = shopping::get_equivilant_course_type($feature_price, $feature_tablePrefix);
								if ($newcourse['typeID']){
								unset($newcart[$key]['features'][$featureGroup]);
								$newkey = rand(); // generates random line number
								$newcart[$newkey] = $item;
								
								if ($item['addon']){
										unset($newcart[$newkey]['addon']);
										$newcart[$newkey]['features']="";
								}
									unset($newcart[$newkey]['features']);	
									if ($item['tablePrefix'] == "bls" && !$bls_added){
									$bls_added = true;
									Log::addEntry("found features parent was bls", "extraction");
									
									//parent is a bls course re-add it as a feature
										if ($item['typeID'] == 1){ //this is a bls cert
										$newcart[$newkey]['features'][1] = 10;
										}
										if ($item['typeID'] == 2){ //this is a bls recert
										$newcart[$newkey]['features'][1] = 11;
										}
										unset($newcart[$key]);
									}
									
									
										
									$newcart[$newkey]['tablePrefix'] = $feature_tablePrefix;
									$newcart[$newkey]['typeID'] = $newcourse['typeID'];
									$newcart[$newkey]['cost'] = $newcourse['cost'];
									$shipping_cost = 0;
									$shippingID = shopping::get_equivilant_shipping($shipping_cost, $feature_tablePrefix);
									$newcart[$newkey]['shipping'] = $shippingID;
								}
							}
						}
					
					}
				}
			}
		}
		$cart1 = var_export($newcart,1);
		Log::addEntry("newcart:{$cart1}", "extraction");
		return $newcart;
	}
	
	
	public function get_equivilant_course_type($source_cost, $dest_tablePrefix){
		$db = \Database::connection();
		$table = "C5CBT_".$dest_tablePrefix."_types";
		$statement = "SELECT * FROM $table WHERE cost = $source_cost";
		return $db->getRow($statement);
	}
	
	
	 public function get_groupon_adjustment($cart, $groupon=NULL, $expired=FALSE){

        if($groupon){
            $db = Loader::db();
			if($expired){
				$grouponInfo = $db->getRow("SELECT * from C5CBT_groupon WHERE gCode LIKE ?", (array)$groupon);
			}else {
				$grouponInfo = $db->getRow("SELECT * from C5CBT_groupon WHERE gCode LIKE ? AND redemptionTime IS NULL", (array)$groupon);
			}
            if($grouponInfo){

				Log::addEntry('found it.', "Groupon Testing");

            $products = unserialize($grouponInfo['gProducts']);
                if (!is_array($products)){
                    $products[] = $products;
                }
            } else { // groupon Invalid
                return 0;
            }


			Log::addEntry('checking cart', "Groupon Testing");
            if ($cart){
                foreach ($cart as $key=>$item){
                    
                    $itemString = "{$item['tablePrefix']}_{$item['typeID']}";
            if(in_array($itemString,$products)){
                
                $tmp = array_count_values($products);
                $grouponQuantity = $tmp[$itemString];
                
                if($item['quantity'] <= $grouponQuantity){
                $grouponAdjust = $grouponAdjust + (shopping::get_type_cost($item['tablePrefix'], $item['typeID']) * $item['quantity']);
                
                    $removed = 0;
                    foreach($products as $key=>$value){
                        if($value == $itemString && $removed <= $item['quantity']){
                        unset($products[$key]);
                        $removed++;
                        }
                    }

                } else {
                //user is ordering more than the groupon allows for. Wipe them all out of groupon
                $grouponAdjust = $grouponAdjust + (shopping::get_type_cost($item['tablePrefix'], $item['typeID']) * $grouponQuantity);
                
                $removed = 0;
                    foreach($products as $key=>$value){
                        if($value == $itemString && $removed <= $grouponQuantity){
                        unset($products[$key]);
                        $removed++;
                        }
                    }
                }
                
                    
                }
                }
                return $grouponAdjust;
            } else {
                return 0;
            }
            
        } else {
            return 0;
        }
    }
    
    
	public function get_order_total($cart, $discountcode=NULL, $groupon=NULL){
        
        if($groupon){
            $grouponAdjust = shopping::get_groupon_adjustment($cart, $groupon);
        }
        
		$grand_total = 0;
		$quantity = 0;
        
		if ($cart){
		foreach ($cart as $key=>$item){
            $type[$item['tablePrefix']]++;
			$base_price = shopping::get_type_cost($item['tablePrefix'], $item['typeID']);
			$shipping_price = shopping::get_shipping_cost($item['tablePrefix'], $item['shipping']);
			if ($item['features']){
				$feat_price = 0;
				if (is_array($item['features'])){
				foreach ($item['features'] as $featureGroup=>$featureID){
					$feature_price = shopping::get_feature_cost($item['tablePrefix'], $featureID);
					$feat_price = $feat_price + $feature_price;
				}
				}
			}
			$quantity = $quantity + $item['quantity'];
			$exam_total = $item['quantity'] * ($base_price + $shipping_price + $feat_price);
			
			if ($key == "addon"){
			$exam_total = $item['quantity'] * ($shipping_price + $feat_price);
			}
			
			$grand_total = $grand_total + $exam_total;
		}
		
		$discount_percentage = shopping::get_group_discount_package($cart, $discountcode);
		
		
        if(!$discount_percentage){
            $typeCount = count($type);
            $bundleBase = $typeCount * 195;
            Log::addEntry("We counted $typeCount types in the cart",'tampering');
        switch($typeCount){
            case 0:
            $bundleDisc = $bundleBase;
            break;
            case 1:
            $bundleDisc = $bundleBase - 195;
            break;
            default:
            $bundleDisc = $bundleBase - 205;
        } 
            $discount = $bundleDisc;
            Log::addEntry("bundle discount was $discount",'tampering');
        } else {
           $discount = $grand_total * $discount_percentage; 
        }
        
        if(!$discount){
		//$discount_percentage = shopping::get_discount_percentage($quantity);
        //$discount = $grand_total * $discount_percentage;
		}
		Log::addEntry("Intial Grand Total is $grand_total",'tampering');
		$grand_total = $grand_total - $discount;
        if($grouponAdjust){
            $grand_total = $grand_total - $grouponAdjust;
        }
        Log::addEntry("Adjusted grandtotal is $grand_total",'tampering');
		return $grand_total;
		}
		return 0;
	}
	
	public function get_group_discount_package($cart, $discount){
	//Log::addEntry("Discounting with $discount",'tampering');
	$db = Loader::db();
	if($discount){
	$discountInfo = $db->getRow("SELECT * from C5CBT_discounts WHERE discountCode LIKE ?", (array)$discount);
	}
	if($discountInfo){
	//Log::addEntry("Found discount data for $discount",'tampering');
		if(!$discountInfo['discountPercentage']){
		$discountInfo['discountPercentage'] = 0;
		}
			if(!$discountInfo['percent_1']){
				$discountInfo['percent_1'] = $discountInfo['discountPercentage'];
			}
			if(!$discountInfo['percent_2']){
				$discountInfo['percent_2'] = $discountInfo['discountPercentage'];
			}
			if(!$discountInfo['percent_3']){
				$discountInfo['percent_3'] = $discountInfo['discountPercentage'];
			}
			if(!$discountInfo['percent_4']){
				$percentage1 = $discountInfo['discountPercentage'];
			}
			$maxfeat = 0;
			foreach ($cart as $key=>$item){
				if(count($item['features']) > $maxfeat){
					$maxfeat = count($item['features']);
				}
			}
			Log::addEntry("features were counted as $maxfeat",'tampering');

			switch ($maxfeat){
			
				case 0:
				return $discountInfo['percent_1'];
				break;
				
				case 1:
				return $discountInfo['percent_2'];
				break;
				
				case 2:
				return $discountInfo['percent_3'];
				break;
				
				case 3:
				return $discountInfo['percent_4'];
				break;
			}
	} else {
        return false;
	//Log::addEntry("found no data for $discount",'tampering');
	}
	}
	
	
	public function get_discount_percentage($quantity){
		$discount = 0;
		switch ($quantity){
			case $quantity >= 5:
			$discount = .1;
			break;
			
			case $quantity >= 10:
			$discount = .15;
			break;
			
			case $quantity >= 25:
			$discount = .2;
			break;
			
			case $quantity >= 50:
			$discount = .25;
			break;
			
			case $quantity >= 100:
			$discount = .3;
			break;
		}
		return $discount;
	}

	public function get_tablePrefix_array() {
		$db = \Database::connection();
		$courses = $db->getAll("SELECT tablePrefix FROM C5CBT ORDER BY courseID");
		foreach ($courses as $course){
			$tablePrefix[] = $course['tablePrefix'];
		}
		return $tablePrefix;
	}
	
	public function get_types($tablePrefix){
		$db = \Database::connection();
		$table = 'C5CBT_'.$tablePrefix.'_types';
		return $db->GetAll("SELECT * FROM $table");
	}
	
	public function get_type_cost($tablePrefix, $typeID){
		if ($tablePrefix && $typeID){
			$db = \Database::connection();
			$table = "C5CBT_".$tablePrefix."_types";
			return $db->getOne("SELECT cost FROM $table WHERE typeID = $typeID");
		}
	}
	
	public function get_features($tablePrefix){
		$db = \Database::connection();
		$table = 'C5CBT_'.$tablePrefix.'_features';
		return $db->GetAll("SELECT * FROM $table ORDER BY featureID");
	}
	
	
	public function get_feature_cost($tablePrefix, $featureID){
		if ($tablePrefix && is_array($featureID)){
			$db = \Database::connection();
			$table = "C5CBT_".$tablePrefix."_features";
			return $db->getOne("SELECT cost FROM $table WHERE featureID = {$featureID[1]}");
		}
		if ($tablePrefix && $featureID){
			$db = \Database::connection();
			$table = "C5CBT_".$tablePrefix."_features";
			return $db->getOne("SELECT cost FROM $table WHERE featureID = $featureID");
		}
	}
	
	public function get_shippingOptions($tablePrefix){
		$db = \Database::connection();
		$table = 'C5CBT_'.$tablePrefix.'_shippingOptions';
		return $db->GetAll("SELECT * FROM $table");
	}
	
	public function get_shipping_cost($tablePrefix, $shippingID){
		if ($tablePrefix && $shippingID){
			$db = \Database::connection();
			$table = "C5CBT_".$tablePrefix."_shippingOptions";
			return $db->getOne("SELECT cost FROM $table WHERE shippingID = $shippingID");
		}
	}
	
	public function get_cme_credits($tablePrefix, $typeID){
		if ($tablePrefix && $typeID){
		$db = \Database::connection();
		$table = "C5CBT_".$tablePrefix."_types";
		return $db->getOne("SELECT credits FROM $table WHERE typeID = $typeID");
		}
	
	}
	
	
	public function get_equivilant_feature($source_cost, $dest_tablePrefix){
		$db = \Database::connection();
		$table = "C5CBT_".$dest_tablePrefix."_features";
		$statement = "SELECT * FROM $table WHERE cost = $source_cost";
		return $db->getRow($statement);
	}
	
	public function get_equivilant_shipping($source_cost, $dest_tablePrefix){
		$db = \Database::connection();
		$table = "C5CBT_".$dest_tablePrefix."_shippingOptions";
		$statement = "SELECT shippingID FROM $table WHERE cost = $source_cost";
		return $db->getOne($statement);
	}

	public function get_course_name($tablePrefix, $typeID){
		if ($tablePrefix && $typeID){
			$db = \Database::connection();
			$table = "C5CBT_".$tablePrefix."_types";
			if ($name = $db->getOne("SELECT name FROM $table WHERE typeID = $typeID")){
				return $name;
			} else {
				return false;
			}
		}
		
	}
	
	public function get_feature_name($tablePrefix, $featureID){
		if ($tablePrefix && $featureID){
			$db = \Database::connection();
			$table = "C5CBT_".$tablePrefix."_features";
			return $db->getOne("SELECT name FROM $table WHERE featureID = $featureID");
		}
		
	}
	
	public function get_shipping_name($tablePrefix, $shippingID = 1){
		if ($tablePrefix && $shippingID){
			$db = \Database::connection();
			$table = "C5CBT_".$tablePrefix."_shippingOptions";
			return $db->getOne("SELECT name FROM $table WHERE shippingID = $shippingID");
		}
		
	}
	
	
	public function record_transaction($uID, $paypal_response, $order_total, $discount=NULL){

		$insert['uID'] = $uID;
		$insert['paypalInfo'] = serialize($paypal_response);
		$insert['total'] = $order_total;
		$insert['details'] = serialize($_POST['item']);
		
		$db = \Database::connection();
		//$db->AutoExecute('C5CBT_payments', $insert, 'INSERT');
        $db->insert('C5CBT_payments', $insert);
		return $db->Insert_ID();
	}
	
	public function create_user($posted_vars, $override_uid = false){
		

		if (!$override_uid){ // create a user
		$email = trim($posted_vars['emailaddress']);
			$data = array('uName' => $email, 'uPassword' => $posted_vars['change_password'], 'uEmail' => $email);
			$uo = UserInfo::add($data);
			if (is_object($uo)) {
				$g = Group::getByName('Customers');
				$uo->updateGroups(array($g->getGroupID()));
				Log::addEntry('Successfully Created new user ' . $email, "Customer Creation");
			} else {
				Log::addEntry('User Creation Failed for '. $email, "C5CBT Errors" );
			}
		} else {//update an existing user
			$u = User::getByUserID($override_uid);
			$uo = UserInfo::getByID($override_uid);
			if ($posted_vars['firstName']){
				Log::addEntry('Existing User ' . $posted_vars['firstName'] . ' ' . $posted_vars['lastName'] . ' has purchased new products', "Order Creation");
				if ($posted_vars['change_password']){
				$uo->changePassword($posted_vars['change_password']);
				}
			} else {
				$email = $uo->getUserEmail();
				Log::addEntry('Admin updated Existing User ' . $email . ' with new products', "Order Creation");
			}
		}
		
		//now set user attributes
		if ($posted_vars['firstName']){
		$uo->setAttribute('firstName', ucfirst($posted_vars['firstName']));
		$uo->setAttribute('lastName', ucfirst($posted_vars['lastName']));
		$uo->setAttribute('company', ucfirst($posted_vars['company']));
		$ship_address['address1'] = $posted_vars['address']['address1'];
		$ship_address['address2'] = $posted_vars['address']['address2'];
		$ship_address['city'] = ucfirst($posted_vars['address']['city']);
		$ship_address['state_province'] = $posted_vars['address']['state_province'];
		$ship_address['country'] = $posted_vars['address']['country'];
		$ship_address['postal_code'] = $posted_vars['address']['postal_code'];
		$uo->setAttribute('address', $ship_address);
		$uo->setAttribute('telephone', $posted_vars['telephone']);
		
		if (!$posted_vars['same_billing']){ // user did not select to use same info as shipping
			$uo->setAttribute('bill_firstName', ucfirst($posted_vars['bill_firstName']));
			$uo->setAttribute('bill_lastName', ucfirst($posted_vars['bill_lastName']));
			$uo->setAttribute('bill_company', ucfirst($posted_vars['bill_company']));
			$bill_address['address1'] = $posted_vars['bill_address']['address1'];
			$bill_address['address2'] = $posted_vars['bill_address']['address2'];
			$bill_address['city'] = ucfirst($posted_vars['bill_address']['city']);
			$bill_address['state_province'] = $posted_vars['bill_address']['state_province'];
			$bill_address['country'] = $posted_vars['bill_address']['country'];
			$bill_address['postal_code'] = $posted_vars['bill_address']['postal_code'];
			$uo->setAttribute('bill_address', $bill_address);
			$uo->setAttribute('bill_telephone', $posted_vars['bill_telephone']);
		} else { // user selected to use same data as shipping
			$uo->setAttribute('bill_firstName', ucfirst($posted_vars['firstName']));
			$uo->setAttribute('bill_lastName', ucfirst($posted_vars['lastName']));
			$uo->setAttribute('bill_company', ucfirst($posted_vars['company']));
			$bill_address['address1'] = $posted_vars['address']['address1'];
			$bill_address['address2'] = $posted_vars['address']['address2'];
			$bill_address['city'] = ucfirst($posted_vars['address']['city']);
			$bill_address['state_province'] = $posted_vars['address']['state_province'];
			$bill_address['country'] = $posted_vars['address']['country'];
			$bill_address['postal_code'] = $posted_vars['address']['postal_code'];
			$uo->setAttribute('bill_address', $bill_address);
			$uo->setAttribute('bill_telephone', $posted_vars['telephone']);
		}
			
			if ($posted_vars['ownerUid']){
				
				$uo->setAttribute('ownerUid', $posted_vars['ownerUid']);
			}
		}
		
		// now snag the c5 uID for the newly created user
		
		$u = $uo->getUserObject();
		
		return $u->getUserID();
	}
	
	
	
	public function transfer_order($orderID, $tablePrefix, $sourceUID, $destinationUID){
		$u = User::getByUserID($destinationUID);
		$db = \Database::connection();
		$table = "C5CBT_{$tablePrefix}_orders";
		$update['uID'] = $destinationUID;
		$update['OuID'] = $sourceUID;
		$update['transferDate'] = date('YmdHis');
		if($db->AutoExecute($table,$update,'UPDATE', "uID = $sourceUID AND orderID = $orderID")){
			Log::addEntry("Credit successfully transfered from Source User #{$sourceUID} to destination User #{$destinationUID} on $tablePrefix Order #{$orderID}", "Credit Transfers" );
			$g = Group::getByName($tablePrefix);
			$u->enterGroup($g);
			return True;
		} else { //transfer Failed
			Log::addEntry("Credit Transfer Failed from Source User #{$sourceUID} to destination User #{$destinationUID} on $tablePrefix Order #{$orderID}", "C5CBT Errors");
			return False;
		}
		
	}
	

	
	public function create_orders($uID, $cart, $paymentID){
		$u = User::getByUserID($uID);
		
		foreach ($cart as $key=>$c){ //cycle through each line of the cart
			unset($features);
			unset($quantity);
			$item = unserialize($c['details']);
			$type = explode("_",$item['type']);
			$tablePrefix = $type[0];
			$typeID = $type[1];
			
			$ship = explode("_", $item[$tablePrefix."_shipping"]);
			$shipping_name = shopping::get_shipping_name($tablePrefix, $ship[1]);
			if ($c['quantity']){
				$quantity = $c['quantity'];
			} else {
				$quantity = 1;
			}
			foreach ($item as $k=>$feature){ // cycle though all elements to identify features
				$keyexplode = explode("_", $k);
				if (strpos($keyexplode[1],"feature")=== 0 && $keyexplode[0] == $tablePrefix){
					if (is_array($feature)){
						foreach ($feature as $i){
							$itemexplode = explode("_",$i);
							$features[] = shopping::get_feature_name($tablePrefix,$itemexplode[1]);
						}
					} else {
						$itemexplode = explode("_",$feature);
						$features[] = shopping::get_feature_name($tablePrefix,$itemexplode[1]);
					}
				}
			}
			
			if (!shopping::get_course_name($tablePrefix, $typeID)){ //typeID does not load must be add on feature
				Loader::model('member','C5CBT');
				return member::add_feature($tablePrefix, $typeID, $itemexplode[1]);
			}
			
			
			$x = 0;
			$db = \Database::connection();
			while ($x < $quantity){ 
				$insert['uID'] = $uID;
				$insert['paymentID'] = $paymentID;
				$insert['typeID'] = $typeID;
				$insert['features'] = serialize($features);
				$insert['shipping'] = $shipping_name;
				$table = "C5CBT_".$tablePrefix."_orders";
				$db->AutoExecute($table,$insert,'INSERT');
				// add user to access group
				Loader::model('member','C5CBT');
				member::rebuild_group_subscription($uID, FALSE);
				
				//$g = Group::getByName($tablePrefix);
				//$u->enterGroup($g);
				
				$x++;
			}
		}
		//$u->logout();
	}
	
	
	public function create_orders_new($uID, $items, $paymentID){
		$u = User::getByUserID($uID);
		
		foreach ($items as $key=>$item){ //cycle through each line of the cart
			unset($features);
			unset($quantity);
			$tablePrefix = $item['tablePrefix'];
			$typeID = $item['typeID'];
			
			$shipping_name = shopping::get_shipping_name($tablePrefix, $item['shipping']);
			$quantity = $item['quantity'];
			
		if ($item['features']){
			if (is_array($item['features'])){
				foreach ($item['features'] as $i){
					$features[] = shopping::get_feature_name($tablePrefix,$i);
				}
			}
		}
				
			if ($key == "addon"){ //typeID does not load must be add on feature
				Loader::model('member','C5CBT');
				$addonit = member::add_feature($tablePrefix, $item['addon'], $i);
			} else {

			$x = 0;
			$db = \Database::connection();
			while ($x < $quantity){ 
				$insert['uID'] = $uID;
				$insert['paymentID'] = $paymentID;
				$insert['typeID'] = $typeID;
				$insert['features'] = serialize($features);
				$insert['shipping'] = $shipping_name;
				$table = "C5CBT_".$tablePrefix."_orders";
				$db->insert($table,$insert);
				// add user to access group
				Loader::model('member','C5CBT');
				member::rebuild_group_subscription($uID, FALSE);
				$x++;
			}
			}
		}
	}
	
}