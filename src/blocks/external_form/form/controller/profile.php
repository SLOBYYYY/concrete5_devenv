<?php
namespace Application\Block\ExternalForm\Form\Controller;
use Concrete\Core\Controller\AbstractController;
use Concrete\Core\User\UserInfo;
use Concrete\Core\User\User;
Use Loader;


class Profile extends AbstractController
{

    public function action_update($bID = false)
    {
        Loader::model('attribute/categories/user');
		$u = New User;
		$uID = $u->getUserID();
		$ui = UserInfo::getByID($uID);
		$uh = Loader::helper('concrete/user');
		$th = Loader::helper('text');
		$vsh = Loader::helper('validation/strings');
		$cvh = Loader::helper('concrete/validation');
		$e = Loader::helper('validation/error');
		
		$data = $_POST;
		
		//can't change the username on this page, so load up stored values
		$data['firstName'] = $ui->getAttribute('firstName');
		$data['lastName'] = $ui->getAttribute('lastName');
		
		$email = trim($data['emailaddress']);

			if (!$vsh->email($email)) { //validate email
				$e->add(t('Invalid email address provided.'));
			} else if (!$cvh->isUniqueEmail($email)) {
				// the user has entered an email address already in the system. Load this user and add the new order to it.
				$existing_user = true;
				$eui = UserInfo::getByEmail($email);
				if($eui->getUserID() != $uID){ //email given matches a different user..
					$e->add(t('A User with the email already exists.'));
				}
			}
			
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
		//end address Validation
		
		//package address inputs into mutltidimensional array for use when creating user in shopping model
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
		
		if (!$e->has() ) { //there are no validation errors. Update User
		$this->set('data',$data);
		
		
		$update['uEmail'] = $email;		
		$update['uName'] = $email;
			
		$ui->update($update);
			
		
		//now set user attributes

		$ui->setAttribute('firstName', ucfirst($data['firstName']));
		$ui->setAttribute('lastName', ucfirst($data['lastName']));
		$ui->setAttribute('company', ucfirst($data['company']));
		$ship_address['address1'] = $data['address']['address1'];
		$ship_address['address2'] = $data['address']['address2'];
		$ship_address['city'] = ucfirst($data['address']['city']);
		$ship_address['state_province'] = $data['address']['state_province'];
		$ship_address['country'] = $data['address']['country'];
		$ship_address['postal_code'] = $data['address']['postal_code'];
		$ui->setAttribute('address', $ship_address);
		$ui->setAttribute('telephone', $data['telephone']);
		
		if (!$data['same_billing']){ // user did not select to use same info as shipping
			$ui->setAttribute('bill_firstName', ucfirst($data['bill_firstName']));
			$ui->setAttribute('bill_lastName', ucfirst($data['bill_lastName']));
			$ui->setAttribute('bill_company', ucfirst($data['bill_company']));
			$bill_address['address1'] = $data['bill_address']['address1'];
			$bill_address['address2'] = $data['bill_address']['address2'];
			$bill_address['city'] = ucfirst($data['bill_address']['city']);
			$bill_address['state_province'] = $data['bill_address']['state_province'];
			$bill_address['country'] = $data['bill_address']['country'];
			$bill_address['postal_code'] = $data['bill_address']['postal_code'];
			$ui->setAttribute('bill_address', $bill_address);
			$ui->setAttribute('bill_telephone', $data['bill_telephone']);
		} else { // user selected to use same data as shipping
			$ui->setAttribute('bill_firstName', ucfirst($data['firstName']));
			$ui->setAttribute('bill_lastName', ucfirst($data['lastName']));
			$ui->setAttribute('bill_company', ucfirst($data['company']));
			$bill_address['address1'] = $data['address']['address1'];
			$bill_address['address2'] = $data['address']['address2'];
			$bill_address['city'] = ucfirst($data['address']['city']);
			$bill_address['state_province'] = $data['address']['state_province'];
			$bill_address['country'] = $data['address']['country'];
			$bill_address['postal_code'] = $data['address']['postal_code'];
			$ui->setAttribute('bill_address', $bill_address);
			$ui->setAttribute('bill_telephone', $data['telephone']);
		}
		
		
		
		$this->set('message',"Successfully saved new profile details.");
		
		return true;
		} else { //failed validation
			$this->set('error',$e);
			$this->set('data',$data);
		return false;
		}
		
    }

    public function view()
    {
		$al = \Concrete\Core\Asset\AssetList::getInstance();
		$al->register('css', 'c5cbt', 'css/c5cbt.css');
		$al->register('css', 'checkout', 'css/new_checkout.css');
        $al->register('javascript', 'crs', 'js/crs.js');
		$al->register('javascript', 'c5cbt', 'js/c5cbt.js');
		$al->register('javascript', 'checkout', 'js/new_checkout.js');
		
		$al->registerGroup('c5cbt_checkout', array(array('css', 'c5cbt'),array('css', 'checkout'),array('javascript', 'crs'),array('javascript', 'c5cbt'),array('javascript', 'checkout')));
		$this->requireAsset('c5cbt_checkout');
		
	
		switch($_POST['action']){
			case "Update Details":
				self::action_update();
				$update = 1;
			break;
			default:
		}
	
		
		$u = new User();
		$ui = UserInfo::getByID($u->getUserID());
		
	
		
		$data['emailaddress'] = $ui->getUserEmail();
		$data['firstName'] = $ui->getAttribute('firstName');
		$data['lastName'] = $ui->getAttribute('lastName');
		
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
				$data['bill_address1'] = $bill_address->address1;
				$data['bill_address2'] = $bill_address->address2;
				$data['bill_city'] = $bill_address->city;
				$data['bill_state'] = $bill_address->state_province;
				$data['bill_country'] = $bill_address->country;
				$data['bill_zip'] = $bill_address->postal_code;
				$data['bill_telephone'] = $ui->getAttribute('bill_telephone');

		if(!$update){
		$this->set('data',$data);
		}
		
    }
}
