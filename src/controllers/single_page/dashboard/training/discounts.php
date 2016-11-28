<?php  
namespace Application\Controller\SinglePage\Dashboard\Training;
use \Concrete\Core\Page\Controller\DashboardPageController;
use Loader,UserInfo,Page;
use Application\Controller\Training\Dashboard;
use Application\Controller\Training\Member;
use \Concrete\Core\Attribute\Key\Category as AttributeCategory;
use \Concrete\Core\Attribute\Key\UserKey as UserKey;
use \Concrete\Core\Attribute\Type as AttributeType;
use AttributeSet;
use Core;
use Config;

class Discounts extends DashboardPageController {

    public function view() {
        $al = \Concrete\Core\Asset\AssetList::getInstance();
		$al->register('css', 'c5cbt_dashboard', 'css/c5cbt_dashboard.css');
        $al->register('javascript', 'c5cbt_dashboard', 'js/c5cbt_dashboard.js');
		$al->registerGroup('c5cbt_dashboard_items', array(array('css', 'c5cbt_dashboard'),array('javascript', 'c5cbt_dashboard')));
		$this->requireAsset('c5cbt_dashboard_items');
        $db = \Database::connection();
        
        
        switch($_REQUEST['action']){
            case "add":
                self::addNew();
            break;
         
            default:
            break;
        }
        
        
        
        
        $tablePrefixes = $db->fetchAll('SELECT tablePrefix FROM C5CBT');
        $courses = array();
            foreach($tablePrefixes as $t){
                $table = "C5CBT_{$t['tablePrefix']}_types";
            $courses = array_merge($courses,$db->fetchAll("SELECT *, '{$t['tablePrefix']}' AS tablePrefix FROM {$table}"));
            }
            $this->set('courses',$courses);
    }
    
    public function addNew(){
        $db = \Database::connection();
        if($_REQUEST['dCourses']){
            $insert['dCourses'] = serialize($_REQUEST['dCourses']);
            }
            if(is_array($_REQUEST['quantityDisc'])){
                foreach($_REQUEST['quantityDisc'] as $key=>$disc){
                        $discount['comparator'] = h($_REQUEST['comparison'][$key]);
                        $discount['quanitity'] = h($_REQUEST['quantityVal'][$key]);
                        $discount['amount'] = h($disc);
                        $discounts[] = $discount;
                }
           
            $insert['dDisc'] = serialize($discounts);
            }
            $insert['dCode'] = h($_REQUEST['dCode']);
            $insert['dName'] = h($_REQUEST['dName']);
            $insert['dType'] = h($_REQUEST['dType']);
            $insert['dRemain'] = intval($_REQUEST['dRemain']);
            $insert['dUsed'] = intval($_REQUEST['dUsed']);
            
        $db->insert("C5CBT_discount_adv",$insert);
    }
}