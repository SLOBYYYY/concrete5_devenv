<?php 
namespace Application\Controller\Training;
use Controller;
use User;
use UserInfo;
Use Loader;
use Core;
use View;
use Application\Controller\Training\Shopping;
class Email extends Controller {

	public static $organization_name='ACLSCourse.com';
	public static $street_address = NULL;
	public static $city_state_zip = NULL;
	public static $phone = NULL;
	public static $fax = NULL;
	public static $website = "http://www.aclscourse.com";
	public static $admin_email = 'admin@aclscourse.com';
	public static $logo = "http://www.aclscourse.com/themes/aclscourse/images/logo_email.png";
	public static $aclspals_questions = "40/50";
	public static $bls_questions = "24/30";
	public static $nrp_enabled = 1;
	public static $badge_enabled = 0;
	
	public function create_plain_text($source){
		$textVersion =  email::html2text($source);
		return $textVersion;
	}

	private function wrap_template($contents){
		$bodyHtml = "
		<html>
			<head>
			</head>
			<body>
				<table cellspacing=\"0\" cellpadding=\"15\" border=\"0\" width=\"100%\">
					<tr>
					<td bgcolor=\"#ffffff\">
						
						<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">
							<tr>
								<td width=\"50%\"><a target=\"_blank\" href=\"".self::$website."\"><img border=\"0\" alt=\"".self::$website."\" src=\"".self::$logo."\"></a></td>
								<td  width=\"50%\" align=\"right\"><b>".self::$organization_name."</b><br />
								".self::$street_address."<br />
								".self::$city_state_zip."<br />
								</td>
							</tr>
							<tr>
							<td height=\"20\"></td>
							</tr>
							<tr>
								<td colspan=\"2\">
								$contents
								";
		$bodyHtml .='<br /><br />Sincerely,<br /><br /><br /><strong>'.self::$organization_name.'</strong><br />';
		
		if(self::$street_address){
		$bodyHtml .= self::$street_address.'<br />'.self::$city_state_zip.'<br />';
		}
		
		if(self::$phone){
		$bodyHtml .='Toll-Free Support: '.self::$phone.' ';
		}
		
		if(self::$fax){
		$bodyHtml .='or Fax: '.self::$fax;
		}
		$bodyHtml .='<br />Email: <a href="mailto:'.self::$admin_email.'">'.self::$admin_email.'</a><br />Homepage: <a href="'.self::$website.'">'.self::$website.'</a>
								</td>
							</tr>
							
						</table>
						
					</td>
					</tr>
					</table>
			</body>
		</html>
		';
		
		return $bodyHtml;
		
	}
	
	private function build_cart_table_new($items, $discountcode){
		Loader::model('shopping', 'C5CBT');
		$cart_table .='
		<table width="100%" border= "1" cellpadding="10" style="border-collapse:collapse">
		<thead>
		<tr>
		<th>Quantity</th>
		<th align="left">Course Details</th>
		<th align="right">Cost</th>
		</tr>
		</thead>
		<tbody>';
		
		foreach ($items as $key=>$item){
			unset($feature);
			unset($base_course_price);
			unset($base_shipping_price);
			unset($feature_cost);

			$tablePrefix = $item['tablePrefix'];
			$typeID = $item['typeID'];
			$course_title = shopping::get_course_name($tablePrefix, $typeID);
			$base_course_price = shopping::get_type_cost($tablePrefix, $typeID);

		///////////////////////////////////////////// New Code for Adding Features after ///////////////
		if ($key =="addon"){
			$course_title = "Add on BLS Card";
			$orderID = $item['addon'];
			$shipping_name = "Ships with Original Order";
			$base_course_price = 0;
			$item['cost'] = 0;
		} else {
			$shipping_name = shopping::get_shipping_name($tablePrefix, $item['shipping']);
			$base_shipping_price = shopping::get_shipping_cost($tablePrefix, $item['shipping']);
		}
		////////////////////////////////////////////////End New Code///////////////////////////////////

		$quantity = $item['quantity'];
		if ($quantity > 1){
			$multi_test_bulk = true;
		}
		$total_quantity = floatval($total_quantity) + floatval($quantity);
		
				if ($item['features']){
					foreach ($item['features'] as $i){
						$feature[] = shopping::get_feature_name($tablePrefix,$i);
						$thisfeat = shopping::get_feature_cost($tablePrefix,$i);					
						$feature_cost = $feature_cost + $thisfeat;
					}
					$discount_builder[] = "bls";
				}
		$cost = floatval(($base_course_price + $base_shipping_price + $feature_cost) * $quantity);
		$total_cost = $total_cost + $cost;			

			$cart_table .='
		<tr>
		<td align= "center" width="15%" valign="middle">'.$quantity.'</td>
		<td width="70%">
			<h3>'.$course_title.'</h3>
			<ul>';
			
			if ($feature){
				foreach ($feature as $f){ 
					$cart_table .='<li>'.$f.'</li>';
				}
			}
			$cart_table .='<li>'.$shipping_name.'</li>
			</ul>
		</td>
		<td width="15%" align="right" valign="middle">$';
			$cart_table .= number_format($cost, 2, '.', '');
			$cart_table .='</td>
		</tr>';
		$discount_builder[] = $tablePrefix;
		}
		
$type_count = count($discount_builder);
		switch ($total_quantity) {
						case $total_quantity < 5:
						if (!$multi_test_bulk){
				switch ($type_count){
					//case $type_count < 2: Remove multi-test discount
					case $type_count < 50:
					$discount = 0;
					break;
					
					case $type_count == 2:
					$discount_type = "Multi-test";
					$discount = 10;
					break;
					
					case $type_count == 3:
					$discount_type = "Multi-test";
					$discount = 15;
					break;	
			case $total_quantity < 25:
			$discount_type = "Group";
			$discount = 15;
			break;
			
			case $total_quantity < 50:
			$discount_type = "Group";
			$discount = 20;
			break;
			
			case $total_quantity < 100:
			$discount_type = "Group";
			$discount = 25;
			break;
			
			case $total_quantity >= 100:
			$discount_type = "Group";
			$discount = 30;
			break;
				}
				}
			break;
			
			case $total_quantity < 10:
			$discount_type = "Group";
			$discount = 10;
			break;
			
			case $total_quantity < 25:
			$discount_type = "Group";
			$discount = 15;
			break;
		}
		
		
		if($discountcode){
		
		$db = \Database::connection();
		if($discountcode){
		$discountInfo = $db->getRow("SELECT * from C5CBT_discounts WHERE discountCode LIKE ?", (array)$discountcode);
		}
		if($discountInfo){
		if($discountInfo['discountPercentage']){
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
		}
			foreach ($items as $discitem){
				if(count($discitem['features']) > $maxfeat){
				$maxfeat = count($discitem['features']);
				}
			}
			$total_courses = $total_quantity + $maxfeat;
			
			switch ($total_courses){
			
				case 1:
				$discount_type = $discountInfo['discountName'];
				$discount =  ($discountInfo['percent_1'] * 100);
				break;
				
				case 2:
				$discount_type = $discountInfo['discountName'];
				$discount =  ($discountInfo['percent_2'] * 100);
				break;
				
				case 3:
				$discount_type = $discountInfo['discountName'];
				$discount =  ($discountInfo['percent_3'] * 100);
				break;
				
				case 4:
				$discount_type = $discountInfo['discountName'];
				$discount =  ($discountInfo['percent_4'] * 100);
				break;
			}
		
		}
	}
					// new code for special case percentage based discounts
	
		if ($discount){
			$print_discount = intval($discount);
			$savings = $total_cost * ($discount*.01);
			$total_cost = $total_cost - $savings;
			$cart_table .='
		<tr>
			<td colspan="2" style="text-align:right">'.$discount_type .' Discount (';
			
			if ($discount_type == "Group"){ 
			$cart_table .= $total_quantity . ' Registrations ) @ ' .$discount .'% off:';
			} elseif($discount_type == "Multi-test") {
			$cart_table .= $type_count . ' Courses ) @ '.$discount.'% off:';
			} else {
			$cart_table .= $print_discount.'%) off:';
			}
			$cart_table .= '
			</td>
			<td align="right">-$';
			$cart_table .= number_format($savings, 2, '.', '');
			$cart_table .= '</td>
		</tr>';
		};
		
		$cart_table .= '<tr>
			<td colspan="2" align="right"><b>Total:</b></td>
			<td align="right"><b>$';
		
		$cart_table .= number_format($total_cost, 2, '.', '');
		$cart_table .= '
		</b></td>
		</tr>
		</tbody>
		</table>
		';
		
		return $cart_table;
	}
	
	public function send_retry_add($uID){
		
		$ui = UserInfo::getByID($uID);
		$user['firstname'] = ucfirst($ui->getAttribute('firstname'));
		$user['emailaddress'] = $ui->getUserEmail();
		
		$contents = 'Dear '.$user['firstname'].',<br /><br />
			We have added additional exam attempts to your account. You may now login and retry your final exam. If you have any questions please give us a call at '.self::$phone.', or reply directly to this email.<br /><br />  
			Once again, we thank you for choosing the '.self::$organization_name.' for your certification or recertification.';

		$fromEmail = self::$admin_email;
		$fromName = self::$organization_name;	
		$mh = Loader::helper('mail');
		$mh->from($fromEmail, $fromName);
		$mh->to($user['emailaddress']);
		$mh->setSubject("Additional Exam Attempts Added to Your Account at ".self::$organization_name);
		$wrapped = email::wrap_template($contents);
		$mh->setBodyHTML($wrapped);
		$plain = email::create_plain_text($wrapped); 
		$mh->setBody($plain);
		$mh->sendMail();
	}
	
	public function alert_profile_change($uID, $orderID){
		$ui = UserInfo::getByID($uID);
		$user['firstname'] = ucfirst($ui->getAttribute('firstname'));
		$user['lastname'] = ucfirst($ui->getAttribute('lastname'));
		$user['emailaddress'] = $ui->getUserEmail();
		$contents = "This automatic alert is to inform you that user <b> " . $user['firstname'] . " " . $user['lastname'] . "</b> has changed their profile information while their Order# <a href=\"".self::$organization_name.DIR_REL."/index.php/dashboard/C5CBT/orders/?keyword={$orderID}\">{$orderID}</a> is awaiting shipment.";
		$fromEmail = $user['emailaddress'];
		$fromName = $user['firstname'] . " " . $user['lastname'];
		$mh = Loader::helper('mail');
		$mh->from($fromEmail, $fromName);
		$mh->to(self::$admin_email);
		$mh->setSubject("Critical Profile Change : " . $user['firstname'] . " " . $user['lastname']);
		$wrapped = email::wrap_template($contents);
		$mh->setBodyHTML($wrapped);
		$plain = email::create_plain_text($wrapped); 
		$mh->setBody($plain);
		//$mh->sendMail();
	}
	
	public function alert_transfer($SourceUID, $DestinationUID, $orderID, $tablePrefix, $password=NULL){
		$ui = UserInfo::getByID($SourceUID);
		$user['firstname'] = ucfirst($ui->getAttribute('firstname'));
		$user['lastname'] = ucfirst($ui->getAttribute('lastname'));
		$user['emailaddress'] = $ui->getUserEmail();
		$ui = UserInfo::getByID($DestinationUID);
		$user['dest_firstname'] = ucfirst($ui->getAttribute('firstname'));
		$user['dest_lastname'] = ucfirst($ui->getAttribute('lastname'));
		$user['dest_emailaddress'] = $ui->getUserEmail();
		$db = \Database::connection();
		$table = "C5CBT_{$tablePrefix}_orders";
		$order_info = $db->getRow("SELECT * FROM $table WHERE orderID = $orderID");
		$table = "C5CBT_{$tablePrefix}_types";
		$course_type = $db->getOne("SELECT name from $table WHERE typeID = {$order_info['typeID']}");
		$features = unserialize($order_info['features']);
		if ($features) {
			foreach ($features as $feat) {
				$featlist .= " $feat,";
			}
			$featlist .= rtrim($featlist, ",");
		}
		$contents = "We have received and executed a request for a credit transfer from {$user['firstname']} {$user['lastname']} to {$user['dest_firstname']} {$user['dest_lastname']}. <br /><br />";
		$contents .= '
		<table width="100%">
		<tr>
			<td width="50%" valign="top">
			<fieldset>
			<legend><span>Login Information</span></legend><br />
				Email Address: <strong>'.$user['dest_emailaddress'].'</strong><br />';
		
		if ($password) {
			$contents .= 'Password: <strong>'.$password.'</strong><br /><br />';
		}
		
		$contents .= '
	</fieldset><br />
			</td>
			<td width="50%" valign="top">
					<fieldset>
			<legend><span>Course Details</span></legend>
				Course: <strong>'.$course_type.'</strong><br />
				Features: <strong>'.$featlist.'</strong><br />
				Shipping: <strong>'.$order_info['shipping'].'</strong><br />
			</fieldset><br />
			</td>
		</tr>
		</table>
		';

		
		$user['firstname'] = ucfirst($ui->getAttribute('firstName'));
		$user['lastname'] = ucfirst($ui->getAttribute('lastName'));
		$user['company'] = ucfirst($ui->getAttribute('company'));
		$address = $ui->getAttribute('address');
		$user['streetaddress'] = $address->address1;
		$user['streetaddress2'] = $address->address2;
		$user['city'] = ucfirst($address->city);
		$user['state'] = $address->state_province;
		$user['zipcode'] = $address->postal_code;
		$user['country'] = $address->country;
		$user['phonenumber'] = $ui->getAttribute('telephone');
		$user['emailaddress'] = $ui->getUserEmail();
		$user['bill_firstname'] = ucfirst($ui->getAttribute('bill_firstName'));
		$user['bill_lastname'] = ucfirst($ui->getAttribute('bill_lastName'));
		$user['bill_company'] = ucfirst($ui->getAttribute('bill_company'));
		$bill_address = $ui->getAttribute('bill_address');
		$user['bill_streetaddress'] = $bill_address->address1;
		$user['bill_streetaddress2'] = $bill_address->address2;
		$user['bill_city'] = ucfirst($bill_address->city);
		$user['bill_state'] = $bill_address->state_province;
		$user['bill_zipcode'] = $bill_address->postal_code;
		$user['bill_country'] = $bill_address->country;
		$user['bill_phonenumber'] = $ui->getAttribute('bill_telephone');
		
		$contents .= '
	<fieldset><legend>New User Information</legend>
		<table width="100%">
		<tr>
			<td width="50%" valign="top">
			<fieldset>
			<legend><span>Shipping Information</span></legend>
				First Name: <strong>'.$user['firstname'].'</strong><br />
				Last Name: <strong>'.$user['lastname'].'</strong><br />
				Company: <strong>'.$user['company'].'</strong><br />
				Street Address: <strong>'.$user['streetaddress'].' '.$user['streetaddress2'].'</strong><br />
				City: <strong>'.$user['city'].'</strong><br />
				State/Province: <strong>'.$user['state'].'</strong><br />
				Zip Code: <strong>'.$user['zipcode'].'</strong><br />
				Country: <strong>'.$user['country'].'</strong><br />
				Phone Number: <strong>'.$user['phonenumber'].'</strong><br />
			</fieldset><br />
			</td>
			<td width="50%" valign="top">
					<fieldset>
			<legend><span>Billing Information</span></legend>
				First Name: <strong>'.$user['bill_firstname'].'</strong><br />
				Last Name: <strong>'.$user['bill_lastname'].'</strong><br />
				Company: <strong>'.$user['bill_company'].'</strong><br />
				Street Address: <strong>'.$user['bill_streetaddress'].' '.$user['bill_streetaddress2'].'</strong><br />
				City: <strong>'.$user['bill_city'].'</strong><br />
				State/Province: <strong>'.$user['bill_state'].'</strong><br />
				Zip Code: <strong>'.$user['bill_zipcode'].'</strong><br />
				Country: <strong>'.$user['bill_country'].'</strong><br />
				Phone Number: <strong>'.$user['bill_phonenumber'].'</strong><br />
			</fieldset><br />
			</td>
		</tr>
		</table>
		</fieldset>
		';

		$fromEmail = self::$admin_email;
		$fromName = self::$organization_name;
		
		$mh = Loader::helper('mail');
		$mh->from($fromEmail, $fromName);
		$mh->to($user['emailaddress']);
		$mh->to($user['dest_emailaddress']);
		$mh->setSubject(self::$organization_name . " Credit Transfer Details");
		$wrapped = email::wrap_template($contents);
		$mh->setBodyHTML($wrapped);
		$plain = email::create_plain_text($wrapped); 
		$mh->setBody($plain);
		$mh->sendMail();
		/////////////// send a copy to admin////////
		$fromEmail = $user['emailaddress'];
		$fromName = $user['firstname'] . ' ' . $user['lastname'];
		$mh->setBodyHTML($wrapped);
		$mh->setSubject("Webmaster Copy | ".self::$organization_name." Credit Transfer Details");
		$mh->from($fromEmail, $fromName);
		$mh->to(self::$admin_email);
		$mh->setBody($plain);
		$mh->sendMail();
	}
	
	public function paypal_error($paypal, $post_atts, $payment_fields){
		
		$fromEmail = $post_atts['emailaddress'];
		$fromName = $post_atts['firstName'] . ' ' . $post_atts['lastName'];
		$telephone = $post_atts['akID'][27]['value'];
		$paypaldata = var_export($paypal,1);
		$payment_fields['ACCT'] = "REDACTED";
		$paypal_fields = var_export($payment_fields,1);
		unset($post_atts['c1']);
		unset($post_atts['c2']);
		unset($post_atts['c3']);
		unset($post_atts['c4']);
		unset($post_atts['c5']);
		unset($post_atts['change_password']);
		unset($post_atts['password']);
		unset($post_atts['passwordconfirm']);
		
		
		$post_data = var_export($post_atts,1);

		$paypaldata = urldecode($paypaldata);
		$paypaldata = preg_replace('/[ ]{2}/', "     ", $paypaldata);
		$paypaldata = preg_replace("/\=\>[ \n\t]+array[ ]+\(/", '=> array(', $paypaldata);
		$paypaldata = $paypaldata = preg_replace("/\n/", "<br />", $paypaldata);
		
		
		$paypal_fields = urldecode($paypal_fields);
		$paypal_fields = preg_replace('/[ ]{2}/', "     ", $paypal_fields);
		$paypal_fields = preg_replace("/\=\>[ \n\t]+array[ ]+\(/", '=> array(', $paypal_fields);
		$paypal_fields = $paypal_fields = preg_replace("/\n/", "<br />", $paypal_fields);
		
		
		$post_data = preg_replace('/[ ]{2}/', "     ", $post_data);
		$post_data = preg_replace("/\=\>[ \n\t]+array[ ]+\(/", '=> array(', $post_data);
		$post_data = $post_data = preg_replace("/\n/", "<br />", $post_data);
    
		
		$contents = "There has been a payment rejected by paypal with error code {$paypal['L_ERRORCODE0']} . <br /><br />
		Specific Information about this error can be found on the <a href='https://developer.paypal.com/docs/classic/api/errorcodes/'>Paypal Errors Page</a> . <br /><br />
		Customer Information:<br /><br />
		First Name:  {$post_atts['firstName']} <br />
		Last Name:  {$post_atts['lastName']}  <br />
		Telephone:  {$telephone}  <br />
		Email Address:  {$fromEmail }  <br /><br /><br />
		Raw Paypal Data:<br /><br /><br />
		{$paypaldata}<br /><br />
		Raw Customer Data:<br /><br />
		{$post_data}<br /><br />
		What we Sent to Paypal:<br /><br />
		{$paypal_fields}<br /><br />
		";
		
		
		$toEmail = self::$admin_email;
		//$toEmail = "guythomas@gmail.com";

		$mh = Loader::helper('mail');
		$mh->from($fromEmail, $fromName);
		$mh->to($toEmail);
		$mh->setSubject('Paypal Error: ' . $fromName . " | " . $paypal['L_ERRORCODE0']);
		$wrapped = email::wrap_template($contents);
		$mh->setBodyHTML($wrapped);
		$plain = email::create_plain_text($wrapped); 
		$mh->setBody($plain);
		$mh->sendMail();

	}
	
	public function send_forgot_password($uID, $resetURL){
		
		$ui = UserInfo::getByID($uID);
		$user['firstname'] = ucfirst($ui->getAttribute('firstname'));
		$user['emailaddress'] = $ui->getUserEmail();
		
		$contents = 'Dear '.$user['firstname'].',<br /><br />
			We have received a request to reset your password for your account at '.self::$website.'.<br /><br />
			To reset your password please visit the following URL, where you will be asked to create a new password: <br /><br />
			<a href="' . $resetURL. '">'.$resetURL.'</a> <br /><br />
			If you continue to have problems accessing your account, or have any questions about this email, feel free to call us at '.self::$phone.', or reply directly to this email.<br /><br />  
			Once again, we thank you for choosing the '.self::$organization_name.' for your renewal or certification.';
		
		$fromEmail = self::$admin_email;
		$fromName = self::$organization_name;	
		$mh = Loader::helper('mail');
		$mh->from($fromEmail, $fromName);
		$mh->to($user['emailaddress']);
		$mh->setSubject(self::$organization_name.' Password Reset Request');
		$wrapped = email::wrap_template($contents);
		$mh->setBodyHTML($wrapped);
		$plain = email::create_plain_text($wrapped); 
		$mh->setBody($plain);
		$mh->sendMail();
	}
	
	public function send_addon_bls($completionID, $tablePrefix){
		Loader::model('shopping', 'C5CBT');
		$db = \Database::connection();
		//gather the data required for the email
		$table = 'C5CBT_'.$tablePrefix.'_completions';
		$completion_details = $db->GetRow("SELECT * FROM $table WHERE completionID = $completionID");
		$table = 'C5CBT_'.$tablePrefix.'_userExams';
		$examID = $completion_details['examID'];
		$exam_details = $db->GetRow("SELECT * FROM $table WHERE examID = $examID");
		
		//determine if the user requires a BLS download as well
		$table = 'C5CBT_'.$tablePrefix.'_orders';
		$features = $db->getOne("SELECT features FROM $table WHERE orderID = {$completion_details['orderID']}");
		if (stristr($features,'BLS') ){
			$bls_download = true;
		}
		$exam_name = shopping::get_course_name($tablePrefix, $exam_details['typeID']);
		$uID = $completion_details['uID'];
		$ui = UserInfo::getByID($uID);
		$test = strtoupper($tablePrefix);
		//load user details into array
		$user['ceh'] = $ui->getAttribute('ceh');
		$user['firstname'] = ucfirst($ui->getAttribute('firstName'));
		$user['lastname'] = ucfirst($ui->getAttribute('lastName'));
		$user['company'] = ucfirst($ui->getAttribute('company'));
		$address = $ui->getAttribute('address');
		$user['streetaddress'] = $address->address1;
		$user['streetaddress2'] = $address->address2;
		$user['city'] = ucfirst($address->city);
		$user['state'] = $address->state_province;
		$user['zipcode'] = $address->postal_code;
		$user['country'] = $address->country;
		$user['phonenumber'] = $ui->getAttribute('telephone');
		$user['emailaddress'] = $ui->getUserEmail();
		$user['bill_firstname'] = ucfirst($ui->getAttribute('bill_firstName'));
		$user['bill_lastname'] = ucfirst($ui->getAttribute('bill_lastName'));
		$user['bill_company'] = ucfirst($ui->getAttribute('bill_company'));
		$bill_address = $ui->getAttribute('bill_address');
		$user['bill_streetaddress'] = $bill_address->address1;
		$user['bill_streetaddress2'] = $bill_address->address2;
		$user['bill_city'] = ucfirst($bill_address->city);
		$user['bill_state'] = $bill_address->state_province;
		$user['bill_zipcode'] = $bill_address->postal_code;
		$user['bill_country'] = $bill_address->country;
		$user['bill_phonenumber'] = $ui->getAttribute('bill_telephone');
		
		$contents = 'Dear '.$user['firstname'].',<br /><br />
		Thank you for adding a BLS card to your recent '.$exam_name.'. You may now download a PDF copy of your certification card below under "Document
Downloads".
<br /><br />
If the shipping address below is incorrect, please reply to this email immediately with the correct
address to ensure that your card arrives on time.
<br /><br />
You will be shipped a wallet copy of your card within 1 business day, and it will arrive in 7-10 business
days depending on where you are located. Your CEU/CME certificate will arrive in the mail with your
wallet card.
<br /><br />
If you have any questions please reply directly to this email or call us at '.self::$phone.'.
<br /><br />
Your PDF card download link is below:<br />
<fieldset>
		<legend><span>Document Downloads</span></legend>
				';
		if ($bls_download){
			$contents .= '
		<a href="'.self::$website.DIR_REL.'/member/document_view/?o='.$completion_details['orderID'].'&amp;t='.$tablePrefix.'&amp;u='.$uID.'&amp;d=b" target="blank">BLS Certification Card</a><br />
		';
		}
		$contents .= '
		<i>(Copy and Paste into your browser if the link is not clickable)</i><br />
		</fieldset>
		<br />
';
$contents .= '
<table width="100%">
<tr>
	<td width="50%" valign="top">
	<fieldset>
	<legend><span>Shipping Information</span></legend>
		First Name: <strong>'.$user['firstname'].'</strong><br />
		Last Name: <strong>'.$user['lastname'].'</strong><br />
		Company: <strong>'.$user['company'].'</strong><br />
		Street Address: <strong>'.$user['streetaddress'].' '.$user['streetaddress2'].'</strong><br />
		City: <strong>'.$user['city'].'</strong><br />
		State/Province: <strong>'.$user['state'].'</strong><br />
		Zip Code: <strong>'.$user['zipcode'].'</strong><br />
		Country: <strong>'.$user['country'].'</strong><br />
		Phone Number: <strong>'.$user['phonenumber'].'</strong><br />
	</fieldset><br />
	</td>
	<td width="50%" valign="top">
			<fieldset>
	<legend><span>Billing Information</span></legend>
		First Name: <strong>'.$user['bill_firstname'].'</strong><br />
		Last Name: <strong>'.$user['bill_lastname'].'</strong><br />
		Company: <strong>'.$user['bill_company'].'</strong><br />
		Street Address: <strong>'.$user['bill_streetaddress'].' '.$user['bill_streetaddress2'].'</strong><br />
		City: <strong>'.$user['bill_city'].'</strong><br />
		State/Province: <strong>'.$user['bill_state'].'</strong><br />
		Zip Code: <strong>'.$user['bill_zipcode'].'</strong><br />
		Country: <strong>'.$user['bill_country'].'</strong><br />
		Phone Number: <strong>'.$user['bill_phonenumber'].'</strong><br />
	</fieldset><br />
	</td>
</tr>
</table>
';

		$fromEmail = self::$admin_email;
		$fromName = self::$organization_name;
		$exam_email_cc = "";
		
		$mh = Loader::helper('mail');
		$mh->from($fromEmail, $fromName);
		$mh->to($user['emailaddress']);
		$mh->setSubject(self::$organization_name." BLS Card");
		$wrapped = email::wrap_template($contents);
		$mh->setBodyHTML($wrapped);
		$plain = email::create_plain_text($wrapped); 
		$mh->setBody($plain);
		$mh->sendMail();
		
		/////////////// send a copy to admin////////
		$fromEmail = $user['emailaddress'];
		$fromName = $user['firstname'] . ' ' . $user['lastname'];
		$mh->setBodyHTML($wrapped);
		$mh->setSubject("Webmaster Copy | ".self::$organization_name." BLS Card");
		$mh->from($fromEmail, $fromName);
		$mh->to(self::$admin_email);
		$mh->setBody($plain);
		$mh->sendMail();
	}
	
	public function send_exam_pass_emails($completionID, $tablePrefix){
		Loader::model('shopping', 'C5CBT');
		$db = \Database::connection();
		//gather the data required for the email
		$table = 'C5CBT_'.$tablePrefix.'_completions';
		$completion_details = $db->GetRow("SELECT * FROM $table WHERE completionID = $completionID");
		$table = 'C5CBT_'.$tablePrefix.'_userExams';
		$examID = $completion_details['examID'];
		$exam_details = $db->GetRow("SELECT * FROM $table WHERE examID = $examID");
		
		//determine if the user requires a BLS download as well
		$table = 'C5CBT_'.$tablePrefix.'_orders';
		$features = $db->getOne("SELECT features FROM $table WHERE orderID = {$completion_details['orderID']}");
		
		if (stristr($features,'BLS') ){
			$bls_download = true;
		}
		
		$exam_name = shopping::get_course_name($tablePrefix, $exam_details['typeID']);
		
		$uID = $completion_details['uID'];
		$ui = UserInfo::getByID($uID);
		$test = strtoupper($tablePrefix);
		//load user details into array
		$user['ceh'] = $ui->getAttribute('ceh');
		$user['firstname'] = ucfirst($ui->getAttribute('firstname'));
		$user['lastname'] = ucfirst($ui->getAttribute('lastname'));
		$user['company'] = ucfirst($ui->getAttribute('company'));
		$address = $ui->getAttribute('address');
		$user['streetaddress'] = $address->address1;
		$user['streetaddress2'] = $address->address2;
		$user['city'] = ucfirst($address->city);
		$user['state'] = $address->state_province;
		$user['zipcode'] = $address->postal_code;
		$user['country'] = $address->country;
		$user['phonenumber'] = $ui->getAttribute('telephone');
		$user['emailaddress'] = $ui->getUserEmail();
		$user['bill_firstname'] = ucfirst($ui->getAttribute('bill_firstName'));
		$user['bill_lastname'] = ucfirst($ui->getAttribute('bill_lastName'));
		$user['bill_company'] = ucfirst($ui->getAttribute('bill_company'));
		$bill_address = $ui->getAttribute('bill_address');
		$user['bill_streetaddress'] = $bill_address->address1;
		$user['bill_streetaddress2'] = $bill_address->address2;
		$user['bill_city'] = ucfirst($bill_address->city);
		$user['bill_state'] = $bill_address->state_province;
		$user['bill_zipcode'] = $bill_address->postal_code;
		$user['bill_country'] = $bill_address->country;
		$user['bill_phonenumber'] = $ui->getAttribute('bill_telephone');
		
		$contents = 'Congratulations, you passed your '.$test.' examination! Your exam results and a link to your digital PDF card are in this email.
<br /><br />';
if (!$bls_download && $tablePrefix != "bls"){
$contents .= '<b>Add BLS to your Order</b><br/><br/>If you need BLS certification or recertification, we can add a BLS card to your order with no exam required.<br/><br/>

You may purchase BLS here: <a href="'.self::$website.DIR_REL.'/member/addon_bls?o='.$completion_details['orderID'].'&amp;t='.$tablePrefix.'" target="blank">Order BLS</a> (you will be prompted to log in).<br />
<br />
The price for a BLS certification card is $95 and the price for a BLS recertification card is $65.
<br /><br />
We will ship your BLS card after a payment has been made, and it should arrive within 3-5 business days.
<br /><br />
';
}
if ($tablePrefix == "pals"){
$contents .= '<b>Get ACLS Certification or Recertification</b><br/><br/>If you enjoyed the format of our course and need ACLS certification or recertification, you may order it online 24 hours a day at <a href="'.self::$website.'/course-registration/">'.self::$website.'</a>.<br /><br />';
}

if ($tablePrefix == "acls"){
$contents .= '<b>Get PALS Certification or Recertification</b><br/><br/>If you enjoyed the format of our course and need PALS certification or recertification, you may order it online 24 hours a day at <a href="'.self::$website.'/course-registration/">'.self::$website.'</a>.<br /><br />';
}

if ($tablePrefix == "bls"){
$contents .= '<b>Get ACLS Certification or Recertification</b><br /><br/>If you enjoyed the format of our course and need ACLS certification or recertification, you may order it online 24 hours a day at <a href="'.self::$website.'/course-registration/">'.self::$website.'</a>.<br /><br />';
$contents .= '<b>Get PALS Certification or Recertification</b><br /><br/>If you enjoyed the format of our course and need PALS certification or recertification, you may order it online 24 hours a day at <a href="'.self::$website.'/course-registration/">'.self::$website.'</a>.<br /><br />';

}

if ($tablePrefix != "nrp" && self::$nrp_enabled){
$contents .= '<b>Get NRP Certification or Recertification</b><br/><br/>If you enjoyed the format of our course and need NRP certification or recertification, you may order it online 24 hours a day at <a href="'.self::$website.'/course-registration/">'.self::$website.'</a>.<br /><br />';
}

$contents .= '
You may print both sides of the PDF card which lists your name and is proof that you passed the '.$test.' examination.
<br /><br />
A hard copy of your '.$test.' certification card will now be automatically shipped to your mailing address and will arrive within 7 to 10 business days. The card will have your name and date that you passed the exam as well as the date that your certification expires.
<br /><br />
Cards are left unlaminated so you can sign them in the back. We recommend that you laminate your card after you sign it to help protect it.
<br /><br />
If your mailing address listed below is incorrect, please contact us as soon as possible with your correct mailing address.
<br /><br />
Please contact us if you have any questions or concerns.
<br /><br />
Here are your exam details and shipping information:<br /><br />';

		$contents .= '
		<fieldset>
		<legend><span>Exam Details</span></legend>
		<br />
		Order ID: <b>'.$completion_details['orderID'].'</b><br />
		Exam Type: <b>'.$exam_name.'</b><br />
		Exam Result: <b>Pass</b><br />
		Exam Score: <b>'.$exam_details['result'].'%</b><br />
		</fieldset>
		<br />
		<fieldset>
		<legend><span>Document Downloads</span></legend>
		<a href="'.self::$website.DIR_REL.'/member/document_view/?o='.$completion_details['orderID'].'&amp;t='.$tablePrefix.'&amp;u='.$uID.'&amp;d=p" target="blank">'.$test.' Certification Card</a><br />
		<a href="'.self::$website.DIR_REL.'/member/document_view/?o='.$completion_details['orderID'].'&amp;t='.$tablePrefix.'&amp;u='.$uID.'&amp;d=c" target="blank">'.$test.' CEH Certificate</a><br />
		';
		if ($bls_download){
			$contents .= '
		<a href="'.self::$website.DIR_REL.'/member/document_view/?o='.$completion_details['orderID'].'&amp;t='.$tablePrefix.'&amp;u='.$uID.'&amp;d=b" target="blank">BLS Certification Card</a><br />
		<a href="'.self::$website.DIR_REL.'/member/document_view/?o='.$completion_details['orderID'].'&amp;t='.$tablePrefix.'&amp;u='.$uID.'&amp;d=cb" target="blank">BLS CEH Certificate</a><br />
		';
		}
		$contents .= '
		<i>(Copy and Paste into your browser if the link is not clickable)</i><br />
		</fieldset>
		<br />
		';
		
		$contents .= '
<table width="100%">
<tr>
	<td width="50%" valign="top">
	<fieldset>
	<legend><span>Shipping Information</span></legend>
		First Name: <strong>'.$user['firstname'].'</strong><br />
		Last Name: <strong>'.$user['lastname'].'</strong><br />
		Company: <strong>'.$user['company'].'</strong><br />
		Street Address: <strong>'.$user['streetaddress'].' '.$user['streetaddress2'].'</strong><br />
		City: <strong>'.$user['city'].'</strong><br />
		State/Province: <strong>'.$user['state'].'</strong><br />
		Zip Code: <strong>'.$user['zipcode'].'</strong><br />
		Country: <strong>'.$user['country'].'</strong><br />
		Phone Number: <strong>'.$user['phonenumber'].'</strong><br />
	</fieldset><br />
	</td>
	<td width="50%" valign="top">
			<fieldset>
	<legend><span>Billing Information</span></legend>
		First Name: <strong>'.$user['bill_firstname'].'</strong><br />
		Last Name: <strong>'.$user['bill_lastname'].'</strong><br />
		Company: <strong>'.$user['bill_company'].'</strong><br />
		Street Address: <strong>'.$user['bill_streetaddress'].' '.$user['bill_streetaddress2'].'</strong><br />
		City: <strong>'.$user['bill_city'].'</strong><br />
		State/Province: <strong>'.$user['bill_state'].'</strong><br />
		Zip Code: <strong>'.$user['bill_zipcode'].'</strong><br />
		Country: <strong>'.$user['bill_country'].'</strong><br />
		Phone Number: <strong>'.$user['bill_phonenumber'].'</strong><br />
	</fieldset><br />
	</td>
</tr>
</table>
';

		$fromEmail = self::$admin_email;
		$fromName = self::$organization_name;	
		$mh = Loader::helper('mail');
		$mh->from($fromEmail, $fromName);
		$mh->to($user['emailaddress']);
		$mh->setSubject("Congratulations on passing your $test exam");
		$wrapped = email::wrap_template($contents);
		$mh->setBodyHTML($wrapped);
		$plain = email::create_plain_text($wrapped); 
		$mh->setBody($plain);
		$mh->sendMail();
		
		/////////////// send a copy to admin////////
		$fromEmail = $user['emailaddress'];
		$fromName = $user['firstname'] . ' ' . $user['lastname'];
		$mh->setBodyHTML($wrapped);
		$mh->setSubject("Webmaster Copy | Congratulations on passing your $test exam");
		$mh->from($fromEmail, $fromName);
		$mh->to(self::$admin_email);
		$mh->setBody($plain);
		$mh->sendMail();
		
		///////////////send a copy to the account owner ///////
		if($ui->getAttribute('ownerUid')){ // account has an owner
			$owner = User::getByUserID($ui->getAttribute('ownerUid'));
			$oui = UserInfo::getByID($ui->getAttribute('ownerUid'));
			$owner_email = $oui->getUserEmail();
			$get_pass_exam = $owner->config('get_pass_exam');
			if($get_pass_exam){
				$fromEmail = self::$admin_email;
				$fromName2 = self::$organization_name;
				$mh->from($fromEmail, $fromName2);
				$mh->to($owner_email);
				$mh->setSubject("$fromName passed their $test exam");
				$mh->setBodyHTML($wrapped);
				$mh->setBody($plain);
				$mh->sendMail();
			}
		}

if(self::$badge_enabled){	
	//generate and send verification badge email now..
		$fromEmail = self::$admin_email;
		$fromName = self::$organization_name;
		$mh->from($fromEmail, $fromName);
		$mh->to($user['emailaddress']);
		$mh->setSubject("Your Free ACLS Certification Trust Badge");
		$contents = '
		Dear '.$user['firstname'].', <br/><br/>
 
Congratulations on passing your final exam! Now that you are certified through ACLScertification.com, we would like to offer you a free special badge for use on your website. This is a trust badge that adds credibility to your site by authenticating your certification to your colleagues and patients. This badge is offered as a free benefit.<br/><br/>

To obtain your badge, you can login to your account at <a href="http://www.aclscertification.com/login/">http://www.aclscertification.com/login/</a> and on the right hand side under the Member Menu section click on the "Verification Badge" option. In addition, you can click the link below to take you directly to that page. Then simply provide your web address, pick your preferred size, there are three to choose from, and you will be given the code for the badge. This can then be given to your web developer to add to your website.<br/><br/>

<a href="http://www.aclscertification.com/member/badge">Click here to set up your badge now.</a>
<br/><br/>
Below is a sample of what the badge will look like. Click the image to see a sample of the custom verification page. If you have any questions or would like us to provide you with your badge code via email, please contact us at admin@ACLScertification.com.<br/><br/>

<center><a href="http://www.aclscertification.com/verify_certification/"><img src="http://www.aclscertification.com/images/acls_certified2.png" alt="ACLS Certified"/></a>
<br/<br/>
</center>
<br/><br/>
Thank you so much for certifying at www.ACLScertification.com. 
		';
		$wrapped = email::wrap_template($contents);
		$mh->setBodyHTML($wrapped);
		$plain = email::create_plain_text($wrapped); 
		$mh->setBody($plain);
		$mh->sendMail();
	}
	}

	public function send_welcome($uID, $override=false){
		
		$ui = UserInfo::getByID($uID);
		$user['firstname'] = ucfirst($ui->getAttribute('firstname'));
		$user['emailaddress'] = $ui->getUserEmail();
		
		if ($override){
		$link = email::get_forgot_password_link($uID);
		$activate = "Please activate your new account by visiting the following link: <a href=\"$link\">$link</a> <br /> <br />After setting up your new password:<br /> <br /> ";
		}
		
		$contents = 'Dear '.$user['firstname'].',<br /><br />
		
Thank you for choosing the '.self::$organization_name.' for your certification or recertification needs. Below are instructions on how to use our online program.
<br /><br />'.$activate.'
<b>Step 1.) Log into the course</b><br /><br />
You may now login at <a href="'.self::$website.DIR_REL.'/login">'.self::$website.DIR_REL.'/login</a> to access the course.
<br /><br />
Your username is your email address.
<br />
Your password is the one you selected at checkout.
<br /><br />
If you do not remember your password, visit <a href="'.self::$website.DIR_REL.'/login">'.self::$website.DIR_REL.'/login</a> and use the "Forgot Your Password?" feature on the bottom of the page.
<br /><br />
<b>Step 2.) Study for the final</b><br /><br />
Once you log into the course, you may access the training materials by clicking on "Study Guide" on the right menu. The study guide is organized into sections, and you may go through each section at your own pace. Objectives are described at the beginning of each section. You can take as much time as you would like to go through the study guide.
<br /><br />
You are free to take a practice exam or the final exam at any point. If you have already studied, you may attempt the final exam right away; you are not required to go through our training material prior to taking the final. 
<br /><br />
<b>Step 3.) Access the exams</b><br /><br />
You may access the Practice Exam by clicking on "Practice Exam" in the right menu. The practice exam is 50 questions in length and covers similar topics as the final exam. The final exam can be taken at any time by clicking on "Final Exam". The final exam is not timed, and once you submit your final exam answers you will see your results immediately. 
<br /><br />
A score of 80% is required to pass the final ACLS or PALS exam, or '.self::$aclspals_questions.'. A score of 80% is also required to pass the BLS exam, or '.self::$bls_questions.'. If you do not pass on your first attempt you will see the questions you missed, and we recommend that you go back and review the relevant sections in the study guide before attempting the exam again.
<br /><br />
<b>Step 4.) Download your card</b><br /><br />
As soon as you pass the final exam you will receive your Congratulations email which will have a link to download your card.
<br /><br />
Under the "My Documents" section you may download your ACLS, PALS or BLS card as soon as you pass the final exam. You will also receive a download link in your Congratulations email. 
<br /><br />
Your wallet ACLS, PALS or BLS card will be shipped to 1 business day after you pass the final exam, and should arrive to your shipping address within 7-10 business days. Please allow more time for international shipments.
<br /><br />
Please reply to this email or call us at '.self::$phone.' if you have any questions during the training process or if you need anything at all.
<br /><br />
Thank you for choosing the '.self::$organization_name.' for your training needs.<br />';
		
		$fromEmail = self::$admin_email;
		$fromName = self::$organization_name;
		$exam_email_cc = "";
		
		$mh = Loader::helper('mail');
		$mh->from($fromEmail, $fromName);
		$mh->to($user['emailaddress']);
		$mh->setSubject('Welcome to the '.self::$organization_name);
		$wrapped = email::wrap_template($contents);
		$mh->setBodyHTML($wrapped);
		$plain = email::create_plain_text($wrapped); 
		$mh->setBody($plain);
		$mh->sendMail();
	}

	public function send_receipt($uID, $pID, $discount = NULL){
		
		$ui = UserInfo::getByID($uID);
		$db = \Database::connection();
		$payment = $db->getRow("SELECT * FROM C5CBT_payments WHERE paymentID = $pID AND uID = $uID");
		$fromEmail = self::$admin_email;
		$fromName = self::$organization_name;
		//load user details into array
		$user['ceh'] = $ui->getAttribute('ceh');
		$user['firstname'] = ucfirst($ui->getAttribute('firstname'));
		$user['lastname'] = ucfirst($ui->getAttribute('lastname'));
		$user['company'] = ucfirst($ui->getAttribute('company'));
		$address = $ui->getAttribute('address');
		$user['streetaddress'] = $address->address1;
		$user['streetaddress2'] = $address->address2;
		$user['city'] = ucfirst($address->city);
		$user['state'] = $address->state_province;
		$user['zipcode'] = $address->postal_code;
		$user['country'] = $address->country;
		$user['phonenumber'] = $ui->getAttribute('telephone');
		$user['emailaddress'] = $ui->getUserEmail();
		$user['bill_firstname'] = ucfirst($ui->getAttribute('bill_firstName'));
		$user['bill_lastname'] = ucfirst($ui->getAttribute('bill_lastName'));
		$user['bill_company'] = ucfirst($ui->getAttribute('bill_company'));
		$bill_address = $ui->getAttribute('bill_address');
		$user['bill_streetaddress'] = $bill_address->address1;
		$user['bill_streetaddress2'] = $bill_address->address2;
		$user['bill_city'] = ucfirst($bill_address->city);
		$user['bill_state'] = $bill_address->state_province;
		$user['bill_zipcode'] = $bill_address->postal_code;
		$user['bill_country'] = $bill_address->country;
		$user['bill_phonenumber'] = $ui->getAttribute('bill_telephone');
		
		if ($discount){
		Log::addEntry("User {$user['firstname']} {$user['lastname']} checked out", "{$discount}_receipt");
		}
		
		$email_contents = ' <p>Thank you for purchasing our course, <strong>' . $user['firstname'] .'</strong>!<br /><br />Below is your receipt for your records:<br />
	
<table width="100%">
<tr>
	<td width="50%" valign="top">
	<fieldset>
	<legend><span>Shipping Information</span></legend>
		First Name: <strong>'.$user['firstname'].'</strong><br />
		Last Name: <strong>'.$user['lastname'].'</strong><br />
		Company: <strong>'.$user['company'].'</strong><br />
		Street Address: <strong>'.$user['streetaddress'].' '.$user['streetaddress2'].'</strong><br />
		City: <strong>'.$user['city'].'</strong><br />
		State/Province: <strong>'.$user['state'].'</strong><br />
		Zip Code: <strong>'.$user['zipcode'].'</strong><br />
		Country: <strong>'.$user['country'].'</strong><br />
		Phone Number: <strong>'.$user['phonenumber'].'</strong><br />
	</fieldset><br />
	</td>
	<td width="50%" valign="top">
			<fieldset>
	<legend><span>Billing Information</span></legend>
		First Name: <strong>'.$user['bill_firstname'].'</strong><br />
		Last Name: <strong>'.$user['bill_lastname'].'</strong><br />
		Company: <strong>'.$user['bill_company'].'</strong><br />
		Street Address: <strong>'.$user['bill_streetaddress'].' '.$user['bill_streetaddress2'].'</strong><br />
		City: <strong>'.$user['bill_city'].'</strong><br />
		State/Province: <strong>'.$user['bill_state'].'</strong><br />
		Zip Code: <strong>'.$user['bill_zipcode'].'</strong><br />
		Country: <strong>'.$user['bill_country'].'</strong><br />
		Phone Number: <strong>'.$user['bill_phonenumber'].'</strong><br />
	</fieldset><br />
	</td>
</tr>
</table>
<fieldset>
<legend><span>Order Information</span></legend>
<table class="address">
	<tr>
	<td class="address" nowrap="nowrap">Payment Number: <strong>'.$pID.'</strong></td>
	</tr>
	';
	if($discount){
	$email_contents .= '<tr>'.$discname.'</tr>'; 
	}
	$email_contents .= '
	</table>
	<br />';
		$cart = unserialize($payment['details']);
		$email_contents .= email::build_cart_table_new($cart, $discount);
		$email_contents .='
	</fieldset>
<br /><br />If you have any questions, comments, or concerns, please feel free to Contact Us at anytime.<br /><br />Thank you again for your purchase!</div>
			</div>
			<div class="spacer">&nbsp;</div>		
		</div>
		<br />
		</td>
	</tr>
	</table>   ';
		//setup the mail helper and send
		
		$mh = Loader::helper('mail');
		$mh->from($fromEmail, $fromName);
		$mh->to($user['emailaddress']);
		$mh->setSubject(self::$organization_name.' Purchase Receipt');
		$wrapped = email::wrap_template($email_contents);
		$mh->setBodyHTML($wrapped);
		$plain = email::create_plain_text($wrapped); 
		$mh->setBody($plain);
		$mh->sendMail();
		
		/////////////// send a copy to admin////////
		$fromEmail = $user['emailaddress'];
		$fromName = $user['firstname'] . ' ' . $user['lastname'];
		$mh->setBodyHTML($wrapped);
		$mh->setSubject("Webmaster Copy | ".self::$organization_name." Purchase Receipt");
		
		if($discount){
			$upperdisc = strtoupper($discount);
			$mh->setSubject("Webmaster Copy | ".self::$organization_name." ".$upperdisc." Purchase Receipt");
		}
		
		$mh->from($fromEmail, $fromName);
		$mh->to(self::$admin_email);
		$mh->setBody($plain);
		$mh->sendMail();
	}
	
	public function get_forgot_password_link($uID) {
		$loginData['success']=0;
		$error = Core::make('helper/validation/error');
        $vs = Core::make('helper/validation/strings');
		$ui = UserInfo::getByID($uID);
		$em = $ui->getUserEmail();
					
		$oUser = UserInfo::getByEmail($em);
                if (!$oUser) {
                    throw new \Exception(t('We have no record of that email address.'));
                }		
			 //generate hash that'll be used to authenticate user, allowing them to change their password
                $h = new \Concrete\Core\User\ValidationHash();
                $uHash = $h->add($oUser->uID, intval(UVTYPE_CHANGE_PASSWORD), true);
                $changePassURL = View::url(
                        '/login',
                        'callback',
                        'concrete',
                        'change_password',
                        $uHash);
						
			return  $changePassURL; 		
	}
    
    public function html2text( $badStr ) {
    //remove PHP if it exists
    while( substr_count( $badStr, '<'.'?' ) && substr_count( $badStr, '?'.'>' ) && strpos( $badStr, '?'.'>', strpos( $badStr, '<'.'?' ) ) > strpos( $badStr, '<'.'?' ) ) {
        $badStr = substr( $badStr, 0, strpos( $badStr, '<'.'?' ) ) . substr( $badStr, strpos( $badStr, '?'.'>', strpos( $badStr, '<'.'?' ) ) + 2 ); }
    //remove comments
    while( substr_count( $badStr, '<!--' ) && substr_count( $badStr, '-->' ) && strpos( $badStr, '-->', strpos( $badStr, '<!--' ) ) > strpos( $badStr, '<!--' ) ) {
        $badStr = substr( $badStr, 0, strpos( $badStr, '<!--' ) ) . substr( $badStr, strpos( $badStr, '-->', strpos( $badStr, '<!--' ) ) + 3 ); }
    //now make sure all HTML tags are correctly written (> not in between quotes)
    for( $x = 0, $goodStr = '', $is_open_tb = false, $is_open_sq = false, $is_open_dq = false; strlen( $chr = $badStr{$x} ); $x++ ) {
        //take each letter in turn and check if that character is permitted there
        switch( $chr ) {
            case '<':
                if( !$is_open_tb && strtolower( substr( $badStr, $x + 1, 5 ) ) == 'style' ) {
                    $badStr = substr( $badStr, 0, $x ) . substr( $badStr, strpos( strtolower( $badStr ), '</style>', $x ) + 7 ); $chr = '';
                } elseif( !$is_open_tb && strtolower( substr( $badStr, $x + 1, 6 ) ) == 'script' ) {
                    $badStr = substr( $badStr, 0, $x ) . substr( $badStr, strpos( strtolower( $badStr ), '</script>', $x ) + 8 ); $chr = '';
                } elseif( !$is_open_tb ) { $is_open_tb = true; } else { $chr = '&lt;'; }
                break;
            case '>':
                if( !$is_open_tb || $is_open_dq || $is_open_sq ) { $chr = '&gt;'; } else { $is_open_tb = false; }
                break;
            case '"':
                if( $is_open_tb && !$is_open_dq && !$is_open_sq ) { $is_open_dq = true; }
                elseif( $is_open_tb && $is_open_dq && !$is_open_sq ) { $is_open_dq = false; }
                else { $chr = '&quot;'; }
                break;
            case "'":
                if( $is_open_tb && !$is_open_dq && !$is_open_sq ) { $is_open_sq = true; }
                elseif( $is_open_tb && !$is_open_dq && $is_open_sq ) { $is_open_sq = false; }
        } $goodStr .= $chr;
    }
    //now that the page is valid (I hope) for strip_tags, strip all unwanted tags
    $goodStr = strip_tags( $goodStr, '<title><hr><h1><h2><h3><h4><h5><h6><div><p><pre><sup><ul><ol><br><dl><dt><table><caption><tr><li><dd><th><td><a><area><img><form><input><textarea><button><select><option>' );
    //strip extra whitespace except between <pre> and <textarea> tags
    $badStr = preg_split( "/<\/?pre[^>]*>/i", $goodStr );
    for( $x = 0; is_string( $badStr[$x] ); $x++ ) {
        if( $x % 2 ) { $badStr[$x] = '<pre>'.$badStr[$x].'</pre>'; } else {
            $goodStr = preg_split( "/<\/?textarea[^>]*>/i", $badStr[$x] );
            for( $z = 0; is_string( $goodStr[$z] ); $z++ ) {
                if( $z % 2 ) { $goodStr[$z] = '<textarea>'.$goodStr[$z].'</textarea>'; } else {
                    $goodStr[$z] = preg_replace( "/\s+/", ' ', $goodStr[$z] );
            } }
            $badStr[$x] = implode('',$goodStr);
    } }
    $goodStr = implode('',$badStr);
    //remove all options from select inputs
    $goodStr = preg_replace( "/<option[^>]*>[^<]*/i", '', $goodStr );
    //replace all tags with their text equivalents
    $goodStr = preg_replace( "/<(\/title|hr)[^>]*>/i", "\n          --------------------\n", $goodStr );
    $goodStr = preg_replace( "/<(h|div|p)[^>]*>/i", "\n\n", $goodStr );
    $goodStr = preg_replace( "/<sup[^>]*>/i", '^', $goodStr );
    $goodStr = preg_replace( "/<(ul|ol|br|dl|dt|table|caption|\/textarea|tr[^>]*>\s*<(td|th))[^>]*>/i", "\n", $goodStr );
    $goodStr = preg_replace( "/<li[^>]*>/i", "\n· ", $goodStr );
    $goodStr = preg_replace( "/<dd[^>]*>/i", "\n\t", $goodStr );
    $goodStr = preg_replace( "/<(th|td)[^>]*>/i", "\t", $goodStr );
    $goodStr = preg_replace( "/<a[^>]* href=(\"((?!\"|#|javascript:)[^\"#]*)(\"|#)|'((?!'|#|javascript:)[^'#]*)('|#)|((?!'|\"|>|#|javascript:)[^#\"'> ]*))[^>]*>/i", "[LINK: $2$4$6] ", $goodStr );
    $goodStr = preg_replace( "/<img[^>]* alt=(\"([^\"]+)\"|'([^']+)'|([^\"'> ]+))[^>]*>/i", "[IMAGE: $2$3$4] ", $goodStr );
    $goodStr = preg_replace( "/<form[^>]* action=(\"([^\"]+)\"|'([^']+)'|([^\"'> ]+))[^>]*>/i", "\n[FORM: $2$3$4] ", $goodStr );
    $goodStr = preg_replace( "/<(input|textarea|button|select)[^>]*>/i", "[INPUT] ", $goodStr );
    //strip all remaining tags (mostly closing tags)
    $goodStr = strip_tags( $goodStr );
    //convert HTML entities
    $goodStr = strtr( $goodStr, array_flip( get_html_translation_table( HTML_ENTITIES ) ) );
    $goodStr = preg_replace( "/&#(\d+);/me", "chr('$1')", $goodStr );
    //wordwrap
    $goodStr = wordwrap( $goodStr );
    //make sure there are no more than 3 linebreaks in a row and trim whitespace
    return preg_replace( "/^\n*|\n*$/", '', preg_replace( "/[ \t]+(\n|$)/", "$1", preg_replace( "/\n(\s*\n){2}/", "\n\n\n", preg_replace( "/\r\n?|\f/", "\n", str_replace( chr(160), ' ', $goodStr ) ) ) ) );
}
}