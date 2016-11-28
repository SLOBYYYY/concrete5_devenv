<?php 
namespace Application\Controller\SinglePage;
use Application\Controller\Training\Shopping;
use Concrete\Core\Page\Controller\PageController;
use Concrete\Core\Attribute\Key\UserKey;
use Concrete\Core\User\UserInfo;
use Application\Controller\Training\Email;
use User;
use Loader;
use Log;
use Config;

class Cart extends PageController {

public static $test_mode = 1;
public static $testID = 9; //userid that does not post to paypal
			
	public function view(){
		
		$al = \Concrete\Core\Asset\AssetList::getInstance();
		$al->register('css', 'c5cbt', 'css/c5cbt.css');
		$al->register('css', 'checkout', 'css/new_checkout.css');
        $al->register('javascript', 'crs', 'js/crs.js');
		$al->register('javascript', 'c5cbt', 'js/c5cbt.js');
		$al->register('javascript', 'checkout', 'js/new_checkout.js');
		
		$al->registerGroup('c5cbt_checkout', array(array('css', 'c5cbt'),array('css', 'checkout'),array('javascript', 'crs'),array('javascript', 'c5cbt'),array('javascript', 'checkout')));
		$this->requireAsset('c5cbt_checkout');
		
		
		
		$db=Loader::db();
		$html = Loader::Helper('html');

        
        if($_REQUEST['groupon']){
			$_REQUEST['groupon'] = trim($_REQUEST['groupon']);
            $grouponInfo = $db->getRow("SELECT * from C5CBT_groupon WHERE gCode LIKE ? AND redemptionTime IS NULL", (array)$_REQUEST['groupon']);
			if($grouponInfo) {
				$products = unserialize($grouponInfo['gProducts']);
				if (is_array($products)) {
					foreach ($products as $p) {
						$_POST['type'][] = $p;
					}
				} else {
					$_POST['type'][] = $products;
				}
				$this->set("groupon", $_REQUEST['groupon']);
				$this->set("message","Your Groupon has been loaded. Please provide student information below for certificate and record keeping only. ");
			} else {
				//groupon not exist.
				$this->set("alert",'Your Groupon code was not found, or has already been redeemed.');
				unset($_REQUEST['groupon']);
			}
        }
        
		
		if($_REQUEST['discount']){ //the user has come from a discount link, lookup the discount
			
			
			$discountInfo = $db->getRow("SELECT * from C5CBT_discounts WHERE discountCode LIKE ? AND remainingUses > 0", (array)$_REQUEST['discount']);
			if($discountInfo){
				if(!$discountInfo['discountPercentage']){
					$discountInfo['discountPercentage'] = intval(0);
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
					$discountInfo['percent_4'] = $discountInfo['discountPercentage'];
				}

				$discount = "
<script type='text/javascript'>
	function get_discount(quantity){
		var discount = 0;
		$('.exam-type').each(function(){
			var features;
			var boxes = $(this).find('.feature-options :checked');
			var features = boxes.length;
			switch (features){
				case 0:
				discount = {$discountInfo['percent_1']};
				break;
				
				case 1:
				discount = {$discountInfo['percent_2']};
				break;
				
				case 2:
				discount = {$discountInfo['percent_3']};
				break;
				
				case 3:
				discount = {$discountInfo['percent_4']};
				break;
				
				case 4:
				discount = {$discountInfo['percent_4']};
				break;
			}
			});
	return discount;
	}
	</script>";
				
				$this->addHeaderItem($discount); //add the custom discount javascript with lookup values to the page
			} else {
				$this->addHeaderItem($html->javascript('discount.js', 'C5CBT')); //add the generic discount javascript file
				$this->set("message","The Discount you Entered is no longer valid."); 
			}
		} else { //there is no discount load the generic disocunt javascript
			$this->addHeaderItem($html->javascript('discount.js', 'C5CBT'));
		}
		
		
	//import country state javascript
		
		Loader::Model('shopping', 'C5CBT');	
		Loader::Model('member', 'C5CBT');
		

		if (self::$test_mode){
			$this->set('test_mode', "System is in Test Mode");
		}

		$u = New User;
		
		if($u->isLoggedIn() && !$_POST && !$_GET) { //if the user has accessed the shopping cart while logged in but have no cart, just add in a acls_recert
			$_POST['type'][] = "cert_1";
			$_POST["{$_GET['c']}_shipping"] = "cert_1";
			$_GET['renewal'] = 1;
		}

		if(!$_REQUEST['type'] && !$_REQUEST['groupon']){
			$_POST['type'][] = "cert_1";

		}
		
		//setup user variables
		$uID = $u->getUserID();
		$ui = UserInfo::getByID($uID);
		
		$this->set('ui', $ui);
		$this->set('uID', $uID);
		
		if ($uID){ //if logged in setup user name and email for auto entry into checkout
			$first_name = $ui->getAttribute('firstName');
			$last_name = $ui->getAttribute('lastName');
			$user_name = "$first_name $last_name";
			$this->set('first_name', $first_name);
			$this->set('user_name', $user_name);
			$this->set('user_email', $ui->getUserEmail());
			$this->set('uID', $uID);
			
			if(!$_POST['cart']){ //Load user data to prefill inputs
				$data['firstName'] = $first_name;
				$data['lastName'] = $last_name;
				$data['company'] = $ui->getAttribute('company');
				$ship_address = $ui->getAttribute('address');
				$data['shipping_address1'] = $ship_address->address1;
				$data['shipping_address2'] = $ship_address->address2;
				$data['shipping_city'] = $ship_address->city;
				$data['shipping_state'] = $ship_address->state_province;
				$data['shipping_country'] = $ship_address->country;
				$data['shipping_zip'] = $ship_address->postal_code;
				$data['telephone'] = $ui->getAttribute('telephone');
			
				$data['bill_firstName'] = $ui->getAttribute('bill_firstName');
				$data['bill_lastName'] = $ui->getAttribute('bill_lastName');
				$data['bill_company'] = $ui->getAttribute('bill_company');
				$bill_address = $ui->getAttribute('bill_address');
				$data['bill_shipping_address1'] = $bill_address->address1;
				$data['bill_shipping_address2'] = $bill_address->address2;
				$data['bill_shipping_city'] = $bill_address->city;
				$data['bill_shipping_state'] = $bill_address->state_province;
				$data['bill_shipping_country'] = $bill_address->country;
				$data['bill_shipping_zip'] = $bill_address->postal_code;
				$data['bill_telephone'] = $ui->getAttribute('bill_telephone');
				$this->set('data', $data);
			}
		}
		
		Loader::model('attribute/categories/user');		
		
		if ($_POST || $_GET){ //user has posted data to this page
			//check for spr with other courses. Remove if so
			if(count($_GET['type']) > 1){
				$_GET['type'] = array_diff($_GET['type'], array('cpr_1', 'cpr_2'));
			}
			
			
			//special processing of data coming from the group orders page//
			if($_GET['cert_1']['quantity'] > 0 ){
				$_GET['type'][]='cert_1';
				$group = 1;
			}
			if($_GET['cert_2']['quantity'] > 0 ){
				$_GET['type'][]='cert_2';
				$group = 1;
			}
			}
			//end special group orders 
			
			if ($_POST['addon']){//In the event of an addon Set the users orderID
				$this->set("addon" ,$_POST['orderID']);
			}
			
			if($_POST['item']){//if this page is being displayed as a result of failed validation reload the previous cart
				$shopping['cart'] = $_POST['item'];
				unset($_POST['type']);
			}
			
			if($_POST['type'] || $_GET['type']){ //posting from front page
				if(!$_POST){
					$_POST = $_GET;
				}
				
				
				if(!is_array($_POST['type']) && $_POST['type']){ //converts urls missing array bracket on ype variable 
					$_POST['type'] = array($_POST['type']);
				}
				foreach($_POST['type'] as $type){
					$type_parts = explode("_",$type);
					$tablePrefix = $type_parts[0];
                    switch($tablePrefix){
                        case "stretching":
                        $tablePrefix = "stretch";
                        break;
                        
                        case "cycle":
                        $tablePrefix = "cycling";
                        break;
                        
                        case "sport":
                        $tablePrefix = "sportspecific";
                        break;
                        
                        case "sports":
                        $tablePrefix = "sportspecific";
                        break;
                        
                        case "speed":
                        $tablePrefix = "speedagility";
                        break;
                        
                        case "women":
                        $tablePrefix = "womens";
                        break;
                        
                        
                    }
                    
					$typeID = $type_parts[1];
					$key = rand(); // generates random line number
					$shopping['cart'][$key]['tablePrefix'] = $tablePrefix;
					$shopping['cart'][$key]['quantity'] = 1;
					if($_POST[$type]['quantity']){
						$shopping['cart'][$key]['quantity'] = $_POST[$type]['quantity'];
					}
					$shopping['cart'][$key]['typeID'] = $typeID;
					if ($_POST["{$tablePrefix}[features]"]){
						foreach($_POST["{$tablePrefix}[features]"] as $featureGroup => $featureID){
							$shopping['cart'][$key]['features'][$featureGroup] = $featureID;	
						}
					}

					foreach ($_POST as $key2=>$value2){
						if ($key2 == $tablePrefix){
							foreach ($value2 as $key3=>$value3){
								if ($key3 == "features" && is_array($value3)){
									foreach($value3 as $featureGroup=>$featureID){
										$shopping['cart'][$key]['features'][$featureGroup] = $featureID;
									}
								}
							}
						}
					}					

				}
			} // end conditional posting from front page	

        
		$order_total = shopping::get_order_total($shopping['cart'],$_REQUEST['discount'],$_REQUEST['groupon']);
        $grouponAdjustment = shopping::get_groupon_adjustment($shopping['cart'], $_REQUEST['groupon']);
        $this->set('groupon', $_REQUEST['groupon']);
        $this->set('grouponSavings', $grouponAdjustment);
		$this->set('total_cost', $order_total);
		$this->set('shopping', $shopping);
		
	} //end view
	
	
	public function checkout(){ // user is attempting to checkout
        
		if($_REQUEST['add-course']){ // just adding an addditional course
					$added_course = 1;
					$type_parts = explode("_",$_REQUEST['add-course']);
					$tablePrefix = $type_parts[0];
					$typeID = $type_parts[1];
					$key = rand(); // generates random line number
					$_POST['item'][$key]['tablePrefix'] = $tablePrefix;
					$_POST['item'][$key]['quantity'] = 1;
					$_POST['item'][$key]['typeID'] = $typeID;
		}
		
		$db = Loader::db();
		Log::addEntry('Initiating Checkout' . $_SERVER['HTTP_USER_AGENT'],'checkout_process');
		
		Loader::model('shopping', 'C5CBT');
		Loader::model('attribute/categories/user');
		$u = New User;
		$uID = $u->getUserID();
		$ui = UserInfo::getByID($uID);
		$uh = Loader::helper('concrete/user');
		$th = Loader::helper('text');
		$vsh = Loader::helper('validation/strings');
		$cvh = Loader::helper('concrete/validation');
		$e = Loader::helper('validation/error');
		
		
		if ($_POST['item']){
			foreach ($_POST['item'] as $key=>$data){
				if(!$identifier){$identifier = $key;} //setup dupe id
				if($identifier == "addon"){$identifier = "This is an addon user";}
			}
		} else {
			$e->add("There was a problem with your shopping cart. Please start over from the home page.");
			Log::addEntry('System did not detect a shopping cart on checkout' . $_SERVER['HTTP_USER_AGENT'],'checkout_process');
		}
		
		$data = $this->post();
		
		
		if($data['discount']){ //user has submitted cart with discount
			$uses = $db->getOne("SELECT remainingUses from C5CBT_discounts WHERE discountCode LIKE ?", (array)$data['discount']);
			if($uses > 0){
				$discount = $data['discount'];
			} else {
				$e->add(t('We detected a duplicate payment attempt. Please login to your account to see your course.')); // user would have been warned about the discount problem on load, so this probably is a dupe
				unset($data['discount']);
			}
		}
		
		//Handles when the admin attempts to add a product to an existing user
		if ($this->post('customer_emailaddress')){ // admin has selected an existing address
			$ui_new = UserInfo::getByEmail($this->post('customer_emailaddress'));
			if ($ui_new){
				$existing_uid = $ui_new->getUserID();
				$data['uID'] = $existing_uid;
				$admin_update = true;
			} else {
				$e->add(t('Could not find the selected user ' . $this->post('customer_emailaddress')));
			}
		}

		if (!$data['uID']){ //this user is either new or not logged in
		
			$email = trim($data['emailaddress']);
			$emailConfirm = trim($data['emailaddress_confirm']);
			if ($email != $emailConfirm) { //validate email
				$e->add(t('Email Addresses provided do not match.'));
			}
			
			if (!$vsh->email($email)) { //valifate email
				$e->add(t('Invalid email address provided.'));
			} else if (!$cvh->isUniqueEmail($email)) {
				// the user has entered an email address already in the system. Load this user and add the new order to it.
				$existing_user = true;
				$eui = UserInfo::getByEmail($email);
				$data['uID'] = $eui->getUserID();
			}
		}
		
		// password
		if(strlen($data['change_password'])) { //an existing user has entered a password, we need to change theirs later
			$passwordNew = $data['change_password'];
			$passwordNewConfirm = $data['passwordconfirm'];
			
			if ((strlen($passwordNew) < Config::get('concrete.user.password.minimum')) || (strlen($passwordNew) > Config::get('concrete.user.password.maximum'))) {
				$e->add(t('A password must be between %s and %s characters', Config::get('concrete.user.password.minimum'), Config::get('concrete.user.password.maximum')));
			}		
			
			if (strlen($passwordNew) >= Config::get('concrete.user.password.minimum') && !$cvh->password($passwordNew)) {
				$e->add(t('A password may not contain ", \', >, <, or any spaces.'));
			}
			
			if ($passwordNew) {
				if ($passwordNew != $passwordNewConfirm) {
					$e->add(t('The two passwords provided do not match.'));
				}
			}
			$data['passwordconfirm'] = $passwordNew;
			$data['change_password'] = $passwordNew;
		}
		
		
		//if (!$data['cc_tyope']) {$e->add(t('Please Select a Credit Card Type'));}
		if (!$data['cc_number']) {$e->add(t('Please Enter your Credit Card Number'));}		
		if (!$data['cc_month']) {$e->add(t('Please Select an Expiration Month'));}
		if (!$data['cc_year']) {$e->add(t('Please Select an Expiration Year'));	}
		//if (!$data['cc_cvv']) {$e->add(t('Please enter your cards CVV number'));}
		
		//if (!$data['agreement']) {$e->add(t('You must agree to the terms and conditions.'));}
		
		//address Validation
		if (!$admin_update){
			
			if (!$data['firstName']){
				$e->add(t('Please Enter your First Name'));
			}
			
			if (!$data['lastName']){
				$e->add(t('Please Enter your Last Name'));
			}
			
			
			if (!$data['shipping_address1']){
				$e->add(t('Please Enter your Shipping Address 1'));
			}
			
			if (!$data['shipping_city']){
				$e->add(t('Please Enter your Shipping City'));
			}
			
			if (!$data['shipping_state']){
				$e->add(t('Please Select a Shipping State'));
			}
			
			if (!$data['same_billing']){
				
				if (!$data['bill_firstName']){
				$e->add(t('Please Enter your First Name'));
				}
				
				if (!$data['bill_lastName']){
					$e->add(t('Please Enter your Last Name'));
				}
				
				
				if (!$data['bill_address1']){
					$e->add(t('Please Enter your billing Address 1'));
				}
				
				if (!$data['bill_city']){
					$e->add(t('Please Enter your billing City'));
				}
				
				if (!$data['bill_state']){
					$e->add(t('Please Select a billing State'));
				}
			}
		}
		//end addrress Validation
		
		//package address inputs into mutlidimensional array for use when creating user in shopping model
		$data['address']['address1'] = $data['shipping_address1'];
		$data['address']['address2'] = $data['shipping_address2'];
		$data['address']['city'] = $data['shipping_city'];
		$data['address']['state_province'] = $data['shipping_state'];
		$data['address']['country'] = $data['shipping_country'];
		$data['address']['postal_code'] = $data['shipping_zip'];
		
		$data['bill_address']['address1'] = $data['bill_address1'];
		$data['bill_address']['address2'] = $data['bill_address2'];
		$data['bill_address']['city'] = $data['bill_city'];
		$data['bill_address']['state_province'] = $data['bill_state'];
		$data['bill_address']['country'] = $data['bill_country'];
		$data['bill_address']['postal_code'] = $data['bill_zip'];

		
		if (!$e->has() && !$added_course) { //there are no validation errors. Attempt payment
			
			if($data['cc_type']){ //if the user has submitted their card type send it, otherwise dont
				$payment_fields['CREDITCARDTYPE'] = $data['cc_type'];
			}
			
			$payment_fields['ACCT'] = $data['cc_number'];
			$payment_fields['ACCT'] = preg_replace('/\D+/', '', $payment_fields['ACCT']);
			$payment_fields['CVV2'] = $data['cc_cvv'];
			$payment_fields['CURRENCYCODE'] = 'USD';
			$payment_fields['PAYMENTACTION'] = 'Sale';
			$payment_fields['IPADDRESS'] = $_SERVER['REMOTE_ADDR'];
			$payment_fields['EXPDATE'] = $data['cc_month'].''.$data['cc_year']; // MMYYYY
			if (!$data['same_billing']){
				$payment_fields['FIRSTNAME'] = $data['bill_firstName'];
				$payment_fields['LASTNAME'] =  $data['bill_lastName'];
			} else {
				$payment_fields['FIRSTNAME'] = $data['firstName'];
				$payment_fields['LASTNAME'] =  $data['lastName'];
			}
			
				$order_total = shopping::get_order_total($data['item'],$data['discount'],$data['groupon']);
				Log::addEntry('Calculated Order Total of ' .$order_total. ' . Posted order total of ' . $_POST['order_total'] . ' ' . $_SERVER['HTTP_USER_AGENT'],'checkout_process');
                if(floatval($order_total) != floatval($data['order_total'])){ //this is not quite reliable, so make note and charge the amount displayed to the user
					$order_total = $data['order_total'];
				}
			

			$payment_fields['AMT'] = $data['order_total'];
			$payment_fields['DESC'] = "Member Register";
			
			
			if($payment_fields['AMT'] < 1 && !$data['groupon']){ //user is attempting to checkout without valid dollar amount
				$e->add("The contents of your shopping cart have been lost. Please go back to the home page and try again.");
				$this->set('error', $e);
				$errors = var_export($e,true);
				Log::addEntry('Cart amount is less than 1 ' . $errors . $_SERVER['HTTP_USER_AGENT'],'checkout_process');
				Log::addEntry('Cart amount is less than 1 ' . $errors . $_SERVER['HTTP_USER_AGENT'],'checkout_error');
				$this->set('data', $data);
				$this->view(); //send them to the view
			}
			
			if ($uID == 1 && $data['cc_type'] == "admin" || self::$test_mode == true || $uID == self::$testID){ // This code allows us to fake the paypal payment in the event of an admin override
				Log::addEntry('The checkout has been overridden','Checkout Override');
				$payment_fields['ACK'] = "Success";
				$override = TRUE;
				$paypal_results_array = $payment_fields;
			} else { //this is actual payment, submit to paypal
				$db=Loader::db();
				$q = "SELECT paymentID FROM C5CBT_payments WHERE timestamp >= NOW() - INTERVAL 90 MINUTE AND details LIKE '%{$identifier}%'"; //check to ensure there are no receipts with the unique line item already in system
				if(!$db->getOne($q)){
                    if($payment_fields['AMT'] > 0){
					$paypal_results_array = $this::PayPalPost('DoDirectPayment', $payment_fields);
                    } else {
                        // This is a groupon. No credit card payment required.
                        $payment_fields['ACK'] = "Success";
                        $paypal_results_array = $payment_fields;
                        Log::addEntry('The checkout has been overridden','Groupon');
                    }
				} else {
					$e->add("You have already purchased. Login now to access your course.");
					Log::addEntry('Duplicate Checkout Intercepted ' . $_SERVER['HTTP_USER_AGENT'],'checkout_error');
					header("Location: ". THANK_YOU_URL, TRUE, 303); //forward user to thank you page.
					die();
				}
			}
			
			if ($paypal_results_array['ACK'] != "Success" &&  $paypal_results_array['ACK'] != "SuccessWithWarning"){ // There was an error
				if($paypal_results_array){
					$paypal_error = urldecode($paypal_results_array['L_LONGMESSAGE0']);
					$e->add($paypal_error);
					$paypalfail = var_export($paypal_results_array,1);
					Log::addEntry('Paypal Error ' . $paypalfail .$_SERVER['HTTP_USER_AGENT'],'paypal errors');
					//email admin the error
					Loader::model('email', 'C5CBT');
					email::paypal_error($paypal_results_array, $data, $payment_fields);
					//end new email function
					$this->set('error', $e);
				}
				$this->set('data', $data);
				$this->view();
			} else { // the payment was successful
				Loader::model('email', 'C5CBT');
				if($discount){ //decrement discount
					$db->execute("UPDATE C5CBT_discounts SET remainingUses = remainingUses - 1 WHERE discountCode LIKE '$discount' and remainingUses > 0");
				}
                
                if($data['groupon']){

                    $timeStamp =  date("Y-m-d H:i:s");
                    $db->execute("UPDATE C5CBT_groupon SET redemptionTime = '{$timeStamp}' WHERE gCode LIKE ?",array($data['groupon']));
                }
				
				if (!$data['uID']){ //create user if this one isn't logged in.

					$uID = shopping::create_user($data);
					if ($override){

						email::send_welcome($uID, 1);
					} else {

						email::send_welcome($uID);
					}
				} else { // update the user with any new values the

					$uID = shopping::create_user($data, $data['uID']);
					
				}
				
				
				$new_user = User::getByUserID($uID,true);//logs user in
				
				
				if (shopping::is_transfer_eligible($data['item'])){ // user is bulk purchaser
					//add to transfer group;
					$group1 = Group::getByName('Transfer');
					$new_user->enterGroup($group1);
				}
 
				$data['item'] = shopping::extract_courses($data['item']);

				$record_transaction = shopping::record_transaction($uID, $paypal_results_array, $order_total,$data['discount']); // store the payment

				if($record_transaction && $data['groupon']){
					$db->execute("UPDATE C5CBT_groupon SET paymentID = {$record_transaction}, uID = {$uID} WHERE gCode LIKE ?",array($data['groupon']));
					$group1 = Group::getByName('groupon');
					$new_user->enterGroup($group1);
				}

				$create_orders = shopping::create_orders_new($uID, $data['item'],$record_transaction);

				if($data['groupon']){$data['discount'] = $data['groupon'];}

				email::send_receipt($uID, $record_transaction,$data['discount']); //send receipt email
				
					if(!$data['groupon']){
						header("Location: ".Config::get('app.thanks_url'), TRUE, 303);
					} else {
						header("Location: "."http://www.personaltrainercertification.us/member", TRUE, 303); 
					}
					die();
			}
		} else { // validation errors were detected waay above
			if(!$added_course){
			$errors = var_export($e,true);
            $posted = var_export($_POST,true);
			Log::addEntry('Validation Errors Present ' . $errors . $_SERVER['HTTP_USER_AGENT'] . 'user posted:'. $posted,'checkout_process');
			$this->set('error', $e);
			} else {
			$this->set('message',"Successfully added course.");
			}
			
			
			$this->set('data', $data);
			$this->view();
		}
	}
	
	
	function PayPalPost($methodName_, $vp_nvp_array) {
        $paypal_endpoint = Config::get('app.paypal.endpoint');
        $paypal_signature = Config::get('app.paypal.signature');
        $paypal_business_api_password = Config::get('app.paypal.business_api_password');
        $paypal_business_api_user_name = Config::get('app.paypal.business_api_user_name');
        
		$version = urlencode('51.0');
		$nvpStr_ = '';
		if(is_array($vp_nvp_array)){
			foreach($vp_nvp_array AS $vl_key => $vl_value){
				$vl_value = urlencode($vl_value);
				$nvpStr_ .= "&$vl_key=$vl_value";
			} // end foreach
		} // end if is array

		// setting the curl parameters.
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $paypal_endpoint);
		curl_setopt($ch, CURLOPT_VERBOSE, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);

		// NVPRequest for submitting to server
		$nvpreq = "METHOD=$methodName_&VERSION=$version&PWD=".$paypal_business_api_password."&USER=".$paypal_business_api_user_name."&SIGNATURE=".$paypal_signature.$nvpStr_;

		// setting the nvpreq as POST FIELD to curl
		curl_setopt($ch, CURLOPT_POSTFIELDS, $nvpreq);

		//getting response from server
		$httpResponse = curl_exec($ch);

		if(!$httpResponse) {
			$res['ok'] = 0;
			$res['msg'] = curl_error($ch).'('.curl_errno($ch).')';
			echo json_encode($res);
			die;
		}

		// Extract the RefundTransaction response details
		$httpResponseAr = explode("&", $httpResponse);
		$httpParsedResponseAr = array();
		foreach ($httpResponseAr as $i => $value) {
			$tmpAr = explode("=", $value);
			if(sizeof($tmpAr) > 1) {
				$httpParsedResponseAr[$tmpAr[0]] = $tmpAr[1];
			}
		}

		if((0 == sizeof($httpParsedResponseAr)) || !array_key_exists('ACK', $httpParsedResponseAr)) {
			$res['ok'] = 0;
			$res['msg'] = 'Invalid HTTP Response for POST request('.$nvpreq.') to '.PAYPAL_ENDPOINT.'.';
			echo json_encode($res);
			die;
		}
		return $httpParsedResponseAr;
	} // end PPHttpPost
}