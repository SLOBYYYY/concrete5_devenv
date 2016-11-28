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

class CourseDetails extends DashboardPageController {

    public function view() {
        $db = Loader::db();
        $al = \Concrete\Core\Asset\AssetList::getInstance();
		$al->register('css', 'c5cbt_dashboard', 'css/c5cbt_dashboard.css');
        $al->register('javascript', 'c5cbt_dashboard', 'js/c5cbt_dashboard.js');
		$al->registerGroup('c5cbt_dashboard_items', array(array('css', 'c5cbt_dashboard'),array('javascript', 'c5cbt_dashboard')));
		$this->requireAsset('c5cbt_dashboard_items');
        
            if($_GET['course']){ //display attributes for selected course
                $tablePrefix = $_GET['course'];
                $types = $db->getAll("SELECT * FROM C5CBT_{$tablePrefix}_types");
                $this->set('types',$types);
                $ships = $db->getAll("SELECT * FROM C5CBT_{$tablePrefix}_shippingOptions");
                $this->set('ships',$ships);
                $questions = $db->getAll("SELECT * FROM C5CBT_{$tablePrefix}_questions");
                $this->set('questions',$questions);
            }
            
			if($this->post('action')){
				switch($this->post('action')){
					case "updateType":
					self::updateType($tablePrefix);
					break;
                    case "addType":
					self::addType($tablePrefix);
					break;
                    case "deleteType":
					self::deleteType($tablePrefix);
					break;
                    case "updateShip":
					self::updateShip($tablePrefix);
					break;
                    case "addShip":
					self::addShip($tablePrefix);
					break;
                    case "deleteShip":
					self::deleteShip($tablePrefix);
					break;
                    case "updateQuestion":
					self::updateQuestion($tablePrefix);
					break;
                    case "addQuestion":
					self::addQuestion($tablePrefix);
					break;
                    case "deleteQuestion":
					self::deleteQuestion($tablePrefix);
					break;
				}
			}
		}
    public function deleteQuestion($tablePrefix){
        $db = Loader::db();
        $update = $this->post();
        if($db->execute("DELETE FROM C5CBT_{$tablePrefix}_questions WHERE questionID = ?",array($update['questionID']))){
			$data['message'] = "success";
		} else {
			$data['message'] = "failure";
		}
        self::ajaxResponse($data);
    }
    
    public function addQuestion($tablePrefix){
        $db = Loader::db();
        $update = $this->post();
        unset($update['action']);
        $update['answers'] = serialize(explode("\n", str_replace("\r", "", $update['answers'])));
        if($update['practice']){$update['practice'] = true;}
        if($update['final']){$update['final'] = true;}
        if($db->insert("C5CBT_{$tablePrefix}_questions", $update)){
			$data['message'] = "success";
		} else {
			$data['message'] = "failure";
		}
        self::ajaxResponse($data);
    }
    
    public function updateQuestion($tablePrefix){
        $db = Loader::db();
        $update = $this->post();
        unset($update['action']);
        $update['answers'] = serialize(explode("\n", str_replace("\r", "", $update['answers'])));
        if($update['practice']){$update['practice'] = true;}
        if($update['final']){$update['final'] = true;}
        if($db->update("C5CBT_{$tablePrefix}_questions", $update, array('questionID'=>$update['questionID']))){
			$data['message'] = "success";
		} else {
			$data['message'] = "failure";
		}
        self::ajaxResponse($data);
    }

        public function deleteShip($tablePrefix){
        $db = Loader::db();
        $update = $this->post();
        if($db->execute("DELETE FROM C5CBT_{$tablePrefix}_shippingOptions WHERE shippingID = ?",array($update['shippingID']))){
			$data['message'] = "success";
		} else {
			$data['message'] = "failure";
		}
        self::ajaxResponse($data);
    }
    
    public function addShip($tablePrefix){
        $db = Loader::db();
        $update = $this->post();
        unset($update['action']);
        if($db->insert("C5CBT_{$tablePrefix}_shippingOptions", $update)){
			$data['message'] = "success";
		} else {
			$data['message'] = "failure";
		}
        self::ajaxResponse($data);
    }
    
    public function updateShip($tablePrefix){
        $db = Loader::db();
        $update = $this->post();
        unset($update['action']);
        if($db->update("C5CBT_{$tablePrefix}_shippingOptions", $update, array('shippingID'=>$update['shippingID']))){
			$data['message'] = "success";
		} else {
			$data['message'] = "failure";
		}
        self::ajaxResponse($data);
    }
    
    public function deleteType($tablePrefix){
        $db = Loader::db();
        $update = $this->post();
        if($db->execute("DELETE FROM C5CBT_{$tablePrefix}_types WHERE typeID = ?",array($update['typeID']))){
			$data['message'] = "success";
		} else {
			$data['message'] = "failure";
		}
        self::ajaxResponse($data);
    }
    
    public function addType($tablePrefix){
        $db = Loader::db();
        $update = $this->post();
        unset($update['action']);
        if($db->insert("C5CBT_{$tablePrefix}_types", $update)){
			$data['message'] = "success";
		} else {
			$data['message'] = "failure";
		}
        self::ajaxResponse($data);
    }
    
    public function updateType($tablePrefix){
        $db = Loader::db();
        $update = $this->post();
        unset($update['action']);
        if($db->update("C5CBT_{$tablePrefix}_types", $update, array('typeID'=>$update['typeID']))){
			$data['message'] = "success";
		} else {
			$data['message'] = "failure";
		}
        self::ajaxResponse($data);
    }
	
	public function ajaxResponse($data){
		$json = new \Symfony\Component\HttpFoundation\JsonResponse($data);
		$json->sendContent();
        die();
	}
        
    
}