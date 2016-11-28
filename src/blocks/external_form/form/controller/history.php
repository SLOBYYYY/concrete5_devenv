<?php
namespace Application\Block\ExternalForm\Form\Controller;
use Concrete\Core\Controller\AbstractController;
use Concrete\Core\User\UserInfo;
use Concrete\Core\User\User;
Use Loader;
use Application\Controller\Training\Member;


class History extends AbstractController
{

	public function view(){
			$al = \Concrete\Core\Asset\AssetList::getInstance();
			$al->register('css', 'c5cbt', 'css/c5cbt.css');
			$al->register('javascript', 'c5cbt', 'js/c5cbt.js');
			$al->registerGroup('c5cbt', array(array('css', 'c5cbt'),array('javascript', 'c5cbt')));
			$this->requireAsset('c5cbt');
			
			$u = New User;
			$uID = $u->getUserID();


			$tablePrefixes = member::get_tablePrefix_array();
			foreach ($tablePrefixes as $tablePrefix){
			$exams[$tablePrefix] = member::get_exams($uID, $tablePrefix);
			$orders[$tablePrefix] = member::get_order_info($uID, $tablePrefix, FALSE);
			$transfers[$tablePrefix] = member::get_transferred_orders($uID, $tablePrefix, FALSE);
			}
			$this->set('exams', $exams);
			$this->set("orders", $orders);
			$this->set("transfers", $transfers);

			
	}
}