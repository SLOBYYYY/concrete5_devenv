<?php  
namespace Application\Controller\SinglePage\Dashboard\Training;
use \Concrete\Core\Page\Controller\DashboardPageController;
use Loader,UserInfo,Page;
use Application\Controller\Training\Dashboard;
use Application\Controller\Training\Member;
class Orders extends DashboardPageController {
        
        public function view() {
		$al = \Concrete\Core\Asset\AssetList::getInstance();
		$al->register('css', 'c5cbt_dashboard', 'css/c5cbt_dashboard.css');
        $al->register('javascript', 'c5cbt_dashboard', 'js/c5cbt_dashboard.js');
		$al->registerGroup('c5cbt_dashboard_items', array(array('css', 'c5cbt_dashboard'),array('javascript', 'c5cbt_dashboard')));
		$this->requireAsset('c5cbt_dashboard_items');
        
		$prefixArray = member::get_tablePrefix_array();
		if ($_GET['l']){
			$limit = $_GET['l'];
		} else {
			$limit = 25;
		}
		if ($_GET['o']){
		$offset = $_GET['o'];
		} else {
		$offset = 0;
		}
		$keyword = $_GET['keyword'];

		
		if ($_GET['filter']){
			switch ($_GET['filter']) {
				case "all":
				$filter = "all";
				break;
				
				case "complete":
				$filter = "complete";
				break;
				
				case "ship":
				$filter = "ship";
				break;
				
				case "active":
				$filter = "active";
				break;
				
				case "activenext":
				$filter = "activenext";
				break;
				
				case "fail":
				$filter = "fail";
				break;
				
				case "shipnext":
				$filter = "shipnext";
				break;
			}
		}
		
		if (!$filter) {
			$filter = "all";
		}
		
		
		if ($_GET['a']){
            
			switch ($_GET['a']) {
          
            case "swap_course":
            $db = Loader::db();
            $selectQuery = "SELECT * FROM C5CBT_{$_GET['c']}_orders WHERE orderID = ?";
            $outgoingOrder = $db->getRow($selectQuery,array($_GET['aid']));
            member::delete_order($_GET['c'], $_GET['aid']);
            $outgoingOrder['orderID'] = null;
            $outgoingOrder['completionID'] = null;
            $table = "C5CBT_".$_GET['new_course']."_orders";
			$db->insert($table,$outgoingOrder);
			member::rebuild_group_subscription($outgoingOrder['uID'], FALSE);
            $this->set('alert_message', "Swapped Courses Successfully");
			break;
            
            case "add":
            $db = Loader::db();
            $selectQuery = "SELECT * FROM C5CBT_{$_GET['c']}_orders WHERE orderID = ?";
            $outgoingOrder = $db->getRow($selectQuery,array($_GET['aid']));
            $outgoingOrder['orderID'] = null;
            $outgoingOrder['completionID'] = null;
            $table = "C5CBT_".$_GET['new_course']."_orders";
			$db->insert($table,$outgoingOrder);
			member::rebuild_group_subscription($outgoingOrder['uID'], FALSE);
            $this->set('alert_message', "Added New Courses Successfully");
			break;

			case "fulfill":
					member::fulfill_order($_GET['c'], $_GET['aid']);
					$this->set('alert_message', "Order Fulfilled");
				break;
			case "unfulfill":
					member::unfulfill_order($_GET['c'], $_GET['aid']);
					$this->set('alert_message', "Order is now awaiting shipping");
				break;
			
			case "delete":
				member::delete_order($_GET['c'], $_GET['aid']);
				$this->set('alert_message', "Order has been deleted.");
				break;
			
			
			case "pass":
				member::manually_pass($_GET['c'], $_GET['aid'], $_GET['send_email']);
				$this->set('alert_message', "Order has been Manually Passed.");
				break;
				
			case "refund":
			member::refund_order($_GET['c'], $_GET['aid'], $_GET['disable_all_orders'], $_GET['disable_order'], $_GET['refund_amount'], $_GET['refund_contact'], $_GET['refund_reason']);
			$this->set('alert_message', "Order has been Marked as Refunded.");
			break;
			
			case "change_date":
			member::change_date($_GET['c'], $_GET['aid'], $_GET['pass_date']);
			$this->set('alert_message', "Pass Date has been updated.");
			break;
			
			case "add_feat":
			member::add_feature($_GET['c'], $_GET['aid'], $featureID);
			$this->set('alert_message', "Order has been appended with new feature.");
			break;
			
			case "reship":
			$db = Loader::db();
			$insert['tablePrefix'] = $_GET['c'];
			$insert['orderID'] = $_GET['aid'];
			$db->insert("C5CBT_reshipments", $insert);
			$this->set('alert_message', "Order has been added to the reshipment que.");
			break;
			
			case "unreship":
			$db = Loader::db();
			$db->Execute("DELETE FROM C5CBT_reshipments WHERE tablePrefix LIKE '{$_GET['c']}' AND orderID ={$_GET['aid']} ");
			$this->set('alert_message', "Order has been removed from the reshipment que.");
			break;
			
            
            }
		}
		
		$paged_orders = dashboard::get_all_orders($prefixArray, $keyword, $limit, $offset, $filter);
		$this->set('orders', $paged_orders['orders']);
		$this->set('total', $paged_orders['total']);
		$this->set('limit', $paged_orders['limit']);
		$this->set('offset', $paged_orders['offset']);
		$this->set('filter', $filter);
		$this->set('keyword', $keyword);
		}
        
}