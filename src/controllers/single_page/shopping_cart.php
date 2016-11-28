<?php
namespace Application\Controller\SinglePage;
use Application\Controller\Training\Shopping;
use Concrete\Core\Page\Controller\PageController;

class ShoppingCart extends PageController {


	public function view(){
        if($_REQUEST['type']){
            $cartContents = self::generateCart();
            
            if($_REQUEST['discount']){
                self::applyDiscountCode($cartContents, $_REQUEST['discount']);
            }
        }
	}
    
    public function applyDiscountCode($cartContents, $discountCode = null){
        
        if($discountCode){
            
        } else {
            
        }
    }
    
    public function generateCart(){
        $db = \Database::connection();
        $cartContents = array();
        foreach($_REQUEST['type'] as $ci){
            $cartItem = array();
            $cartItem['sku'] = $ci;
            $courseParts = explode("_",$ci);
            $tablePrefix = h($courseParts[0]);
            $typeID = intval($courseParts[1]);
            
            $sql = "SELECT * FROM C5CBT_{$tablePrefix}_types WHERE typeID = ?";
            
            $courseData = $db->fetchAssoc($sql,array($typeID));
            $cartItem['tablePrefix'] = $tablePrefix;
            $cartItem['typeID'] = $typeID;
            foreach($courseData as $key=>$val){
                $cartItem[$key] = $val;
            }
            
            if($_REQUEST["{$ci}_quantity"]){
                $cartItem['quantiy']=intval($_REQUEST["{$ci}_quantity"]);
            } else {
                $cartItem['quantiy'] = 1;
            }
            $cartContents[] = $cartItem;
        }
        return $cartContents;
    }

}