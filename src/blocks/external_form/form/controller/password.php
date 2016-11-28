<?php
namespace Application\Block\ExternalForm\Form\Controller;
use Concrete\Core\Controller\AbstractController;
use Concrete\Core\User\UserInfo;
use Concrete\Core\User\User;
Use Loader;


class Password extends AbstractController
{

    public function action_update($bID = false)
    {
		$cvh = Loader::helper('concrete/validation');
		$e = Loader::helper('validation/error');
		
	$u = new User();
		$ui = UserInfo::getByID($u->getUserID());

	// password
$data = $_POST;
			$passwordNew = $data['password'];
			$passwordNewConfirm = $data['passwordconfirm'];
			$data['uPassword'] = $passwordNew;
	
			
			if (!$cvh->password($passwordNew)) {
				$e->add(t('A password may not contain ", \', >, <, or any spaces.'));
				unset($data['uPassword']);
			}
			
			if ($passwordNew) {
				if ($passwordNew != $passwordNewConfirm) {
					$e->add(t('The two passwords provided do not match.'));
					unset($data['uPassword']);
				}
			}
			
			if (!$e->has() ) { //there are no validation errors. Update User
			$ui->changePassword($data['uPassword']);
			$this->set('message',"Successfully updated your password.");
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
		$al->register('javascript', 'c5cbt', 'js/c5cbt.js');
		$al->register('javascript', 'checkout', 'js/new_checkout.js');
		
		$al->registerGroup('c5cbt_checkout', array(array('css', 'c5cbt'),array('css', 'checkout'),array('javascript', 'c5cbt'),array('javascript', 'checkout')));
		$this->requireAsset('c5cbt_checkout');
		
	
		switch($_POST['action']){
			case "Change Password":
				self::action_update();
			break;
			default:
		}

	}
}