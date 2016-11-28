<?php 
namespace Application\Controller\Training;
use Controller;
use User;
use UserInfo;
Use Loader;
use Log;
use Group;
use Application\Controller\Training\Email;
use Application\Controller\Training\Exam;
class Member extends Controller {

	public function get_tablePrefix_array() {
		$db = \Database::connection();
		$courses = $db->getAll("SELECT tablePrefix FROM C5CBT ORDER BY courseID");
		foreach ($courses as $course){
		$tablePrefix[] = $course['tablePrefix'];
		}
		return $tablePrefix;
	}
	
	public function get_last_acls_expiration($uID){
		$db = \Database::connection();
		$order = $db->getOne("SELECT orderID from C5CBT_acls_orders WHERE uID = {$uID} ORDER by timestamp DESC");
		//echo "the order is $order";
		if($order){
		$expires = $db->getOne("SELECT timestamp FROM C5CBT_acls_userExams WHERE orderID = {$order} AND final = 1 and passed = 1");
		return $expires;
		}
	}
	
	public function get_acls_add_order($uID){
		$db = \Database::connection();
		$order = $db->getRow("SELECT orderID, features from C5CBT_acls_orders WHERE uID = {$uID} ORDER by timestamp DESC");
		if($order){
		if (strpos($order['features'], "BLS")){
			return "already";
		} else {
			return $order['orderID'];
		}
		
		}else {
			return FALSE;
		}
	}
	
	public function get_pals_add_order($uID){
		$db = \Database::connection();
		$order = $db->getRow("SELECT orderID, features from C5CBT_pals_orders WHERE uID = {$uID} ORDER by timestamp DESC");
		if($order){
		if (strpos($order['features'], "BLS")){
			return "already";
		} else {
			return $order['orderID'];
		}
		
		}else {
			return FALSE;
		}
	}
	
	public function get_order_status($orderID, $tablePrefix){
		$db = \Database::connection();
		$table = "C5CBT_{$tablePrefix}_orders";
		$completionID = $db->getOne("SELECT completionID FROM $table WHERE orderID = $orderID");
		if (!is_null($completionID)){
		switch ($completionID) {
				case 0:
					$status = "Fail";
					break;
				default:
					$status = "Complete";
					break;
			}
		} else {
			$status = "In Progress";
		}
		return $status;
	}
	
	public function get_sub_accounts($uID){
		Loader::model('user_list');
		$ul = new UserList();
		$ul->filterByAttribute('ownerUid', $uID, '=');
		$users = $ul->get(1000, 0);
		return $users;
	}
	
	public function release_transfer_credits($orderID, $tablePrefix){
		$db = \Database::connection();
		$row = member::get_order_row($tablePrefix, $orderID);
		$update['uID'] = $row['OuID'];
		$update['OuID'] = 0;
		$table = "C5CBT_" . $tablePrefix . "_orders";
		//$db->AutoExecute($table,$update,'UPDATE', "orderID = $orderID");
        $db->update($table,$update,array('orderID' => $orderID));
		
	}
	
	public function get_order_info($uID, $tablePrefix, $open = TRUE){
		$db = \Database::connection();
		$table = "C5CBT_" . $tablePrefix . "_orders";
		if (!$uID){
		$u = New User;
		$uID = $u->getUserID();
		}
		
		if ($open){
		$orders = $db->getAll("SELECT * FROM $table WHERE uID = $uID AND completionID IS NULL ORDER BY orderID desc");
		} else {
		$orders = $db->getAll("SELECT * FROM $table WHERE uID = $uID ORDER BY orderID desc");
		}
		return $orders;
	
	}
	
	public function  get_transferred_orders($uID, $tablePrefix, $open = TRUE){
		$db = \Database::connection();
		$table = "C5CBT_" . $tablePrefix . "_orders";
		if (!$uID){
		$u = New User;
		$uID = $u->getUserID();
		}
		
		if ($open){
		$orders = $db->getAll("SELECT * FROM $table WHERE OuID = $uID AND completionID IS NULL ORDER BY orderID desc");
		} else {
		$orders = $db->getAll("SELECT * FROM $table WHERE OuID = $uID ORDER BY orderID desc");
		}
		return $orders;
	
	}
	
	
	public function get_order($uID, $tablePrefix, $typeID) {
		$db = \Database::connection();
		//$db->setDebug(1);
		$table = "C5CBT_" . $tablePrefix . "_orders";
		if (!$uID){
		$u = New User;
		$uID = $u->getUserID();
		}
		
		$orderID = $db->getOne("SELECT orderID FROM $table WHERE uID = ? AND completionID IS NULL ORDER BY orderID desc",array($uID));
		return $orderID;
	}
	
	public function kill_order($uID, $tablePrefix, $orderID){
		$db = \Database::connection();
		$table = "C5CBT_" . $tablePrefix . "_orders";
		if (!$uID){
		$u = New User;
		$uID = $u->getUserID();
		}
		$update['completionID'] = "0";
		//$db->AutoExecute($table,$update,'UPDATE', "orderID = $orderID");
        $db->update($table,$update,array('orderID' => $orderID));
	
	}
	
	public function get_exam_type($uID, $tablePrefix){
		if (!$uID){
			$u = New User;
			$uID = $u->getUserID();
		}
		$db = \Database::connection();
		$table = "C5CBT_" . $tablePrefix . "_orders";
		$examType = $db->getOne("SELECT typeID FROM $table WHERE uID = $uID AND completionID IS NULL ORDER BY orderID desc");
		return $examType;
	}
	
	public function get_exams($uID, $tablePrefix){
		$db = \Database::connection();
		$table = "C5CBT_" . $tablePrefix . "_userExams";
		if (!$uID){
		$u = New User;
		$uID = $u->getUserID();
		}
		$orders = $db->getAll("SELECT * FROM $table WHERE uID = $uID ORDER BY examID desc");
		return $orders;
	}
	
	public function get_type_name($typeID, $tablePrefix){
		$db = \Database::connection();
		$table = "C5CBT_" . $tablePrefix . "_types";
		return $db->getOne("SELECT name from $table WHERE typeID = $typeID");
		}		
	
	public function get_max_retakes($typeID, $tablePrefix){
		
		$db = \Database::connection();
		$table = "C5CBT_" . $tablePrefix . "_types";
		$sql = "SELECT retakes from $table WHERE typeID = $typeID";
		$retakes = $db->getOne($sql);
		return $retakes;
	}
	
	public function get_remaining_retakes($orderID ,$typeID, $tablePrefix){
	$max_retakes = member::get_max_retakes($typeID, $tablePrefix);
	$table = "C5CBT_" . $tablePrefix . "_orders";
	$db = \Database::connection();
	$attempts = $db->getOne("SELECT attempts FROM $table WHERE orderID = $orderID");
	return $max_retakes - $attempts;
	}
	
	public function get_completion_info($completionID, $tablePrefix){
		$db = \Database::connection();
		$table = "C5CBT_" . $tablePrefix . "_completions";
		return $db->getRow("SELECT * FROM $table WHERE completionID = $completionID");
	}
	
	public function get_types($tablePrefix){
		$db = \Database::connection();
		$table = "C5CBT_" . $tablePrefix . "_types";
		return $db->getAll("SELECT * from $table");
	}
	
	public function get_features($tablePrefix){
		$db = \Database::connection();
		$table = "C5CBT_" . $tablePrefix . "_features";
		return $db->getAll("SELECT * from $table");
	}
	
	public function get_shipping($tablePrefix){
		$db = \Database::connection();
		$table = "C5CBT_" . $tablePrefix . "_shippingOptions";
		return $db->getAll("SELECT * from $table");
	}
	
	public function remove_from_group($uID, $tablePrefix){
		member::rebuild_group_subscription($uID);
	}
	
	public function rebuild_group_subscription($uID, $relogin=TRUE){
	Log::addEntry('Rebuilding User '.$uID,'groups');
	$prefixes = member::get_tablePrefix_array();
		if ($uID){
			$u = User::getByUserID($uID);
		} else {
			$u = New User;
		}
		foreach ($prefixes as $tablePrefix){
		$g = Group::getByName($tablePrefix);
		$ag = Group::getByName($tablePrefix . "_alumni");
			if(member::get_order_info($uID, $tablePrefix, TRUE)){
				$u->enterGroup($g);
				if($u->inGroup($ag)){
					$u->exitGroup($ag);
				}
			} else {
				$u->exitGroup($g);
				if(!$u->inGroup($ag) && member::get_order_info($uID, $tablePrefix, FALSE)){
				$u->enterGroup($ag);
				}
			}
		}
		
		if ($relogin){
		User::loginByUserID($uID);
		}
	}
	
	
	public function fulfill_order($tablePrefix, $orderID){
		$db = \Database::connection();
		$table = "C5CBT_" . $tablePrefix . "_orders";
		$completionID = $db->getOne("SELECT completionID from $table WHERE orderID = $orderID");
		$table = "C5CBT_" . $tablePrefix . "_completions";
		$update['fulfilled'] = 1;
		//$db->Autoexecute($table, $update, 'UPDATE', "completionID = $completionID");
        $db->update($table,$update,array('completionID' => $completionID));
	}
	
	public function add_feature($tablePrefix, $orderID, $featureID){
		$db = \Database::connection();
		$table = "C5CBT_{$tablePrefix}_features";
		$feat_name[] = $db->getOne("SELECT name FROM $table WHERE featureID = $featureID");
		$update['features'] = serialize($feat_name);
		$table = "C5CBT_" . $tablePrefix . "_orders";
		//$db->Autoexecute($table, $update, 'UPDATE', "orderID = $orderID");
        $db->update($table,$update,array('orderID' => $orderID));
		$completionID = $db->getOne("SELECT completionID FROM $table WHERE orderID = $orderID");
		if ($completionID){ //user has already passed email bls card
			Loader::model('email', 'C5CBT');
			email::send_addon_bls($completionID, $tablePrefix);
			$table = "C5CBT_" . $tablePrefix . "_completions";
			$fulfilled = $db->getOne("SELECT fulfilled FROM $table WHERE orderID = $orderID");
				if($fulfilled){
					$insert['tablePrefix']= $tablePrefix;
					$insert['orderID']= $orderID;
					$insert['bls_only']= 1;
					//$db->AutoExecute("C5CBT_reshipments", $insert, "INSERT");
                    $db->insert("C5CBT_reshipments",$insert);
				}
			//$comp_update['fulfilled'] = 0;
			//$db->Autoexecute($table, $comp_update, 'UPDATE', "completionID = $completionID");
		}
	$order = member::get_order_row($tablePrefix, $orderID);	
	member::rebuild_group_subscription($order['uID'], FALSE);
	}
	
	public function add_retry($tablePrefix, $orderID, $email=FALSE){
		$db = \Database::connection();
		$order = member::get_order_row($tablePrefix, $orderID);
		$table = "C5CBT_" . $tablePrefix . "_orders";
		$table2 = "C5CBT_" . $tablePrefix . "_completions";
		$update_order = $db->execute("UPDATE $table  SET attempts=attempts-1, completionID = NULL WHERE orderID = $orderID");
		member::rebuild_group_subscription($order['uID'], FALSE);
		if ($email){
			Loader::model('email', 'C5CBT');
			email::send_retry_add($order['uID']);
		}
		Log::addEntry("{$tablePrefix} order #{$orderID} added additional retry from the dashboard.", "Dashboard Overrides");
	}
	
	public function unfulfill_order($tablePrefix, $orderID){
		$db = \Database::connection();
		$table = "C5CBT_" . $tablePrefix . "_orders";
		$completionID = $db->getOne("SELECT completionID from $table WHERE orderID = $orderID");
		$table = "C5CBT_" . $tablePrefix . "_completions";
		$update['fulfilled'] = 0;
		//$db->Autoexecute($table, $update, 'UPDATE', "completionID = $completionID");
        $db->update($table,$update,array('completionID' => $completionID));
	}
	
	public function delete_order($tablePrefix, $orderID){
		$db = \Database::connection();
		$order = member::get_order_row($tablePrefix, $orderID);
		$table = "C5CBT_" . $tablePrefix . "_orders";
		$table2 = "C5CBT_" . $tablePrefix . "_completions";
		$delete_order = $db->execute("DELETE from $table WHERE orderID = $orderID");
		$delete_order = $db->execute("DELETE from $table2 WHERE orderID = $orderID");
		member::rebuild_group_subscription($order['uID'], FALSE);
		Log::addEntry("{$tablePrefix} order #{$orderID} was manually deleted from the dashboard.", "Dashboard Overrides");
	}
	
	public function manually_pass($tablePrefix, $orderID, $email=FALSE){
	Loader::model('exam', 'C5CBT');
	$order = member::get_order_row($tablePrefix, $orderID);
	$examID = exam::record_exam($order['uID'], $order['typeID'], $tablePrefix, $orderID, TRUE, 100 ,TRUE );
	$completionID = exam::record_completion($order['uID'], $orderID, $examID, $tablePrefix, $order['typeID']);
		if ($email) {
			Loader::model('email', 'C5CBT');
			email::send_exam_pass_emails($completionID, $tablePrefix);
		}
	member::rebuild_group_subscription($order['uID'], FALSE);
	Log::addEntry("{$tablePrefix} order #{$orderID} was manually PASSED from the dashboard.", "Dashboard Overrides");
	}
	
	public function get_order_row($tablePrefix, $orderID){
	$db = \Database::connection();
	$table = "C5CBT_" . $tablePrefix . "_orders";
	$order_row = $db->getRow("SELECT *  FROM $table WHERE orderID = $orderID");
	return $order_row;
	}
	
	public function refund_order($tablePrefix, $orderID, $disable_all=FALSE, $disable_this=FALSE, $amount, $contact, $reason){
	$db = \Database::connection();
	$order = member::get_order_row($tablePrefix, $orderID);
	$insert['paymentID'] = $order['paymentID'];
	$insert['amount'] = $amount;
	$insert['admin_contact'] = $contact;
	$insert['reason'] = $reason;
	$db->insert('C5CBT_refunds', $insert);
	
	if ($disable_this) {
		$table = "C5CBT_" . $tablePrefix . "_orders";
		$update['completionID'] = 0;
		//$db->Autoexecute($table, $update, 'UPDATE', "orderID = $orderID");
        $db->update($table,$update,array('orderID' => $orderID));
	}
	
	if ($disable_all) {
		$orders = member::get_payment_orders($order['paymentID']);
		foreach ($orders as $tablePrefix=>$order){
			foreach ($order as $o){
			$table = "C5CBT_" . $tablePrefix . "_orders";
			$update['completionID'] = 0;
			//$db->Autoexecute($table, $update, 'UPDATE', "orderID = $orderID");
            $db->update($table,$update,array('orderID' => $orderID));
			}
		}
	}
	member::rebuild_group_subscription($order['uID'], FALSE);
	Log::addEntry("Payment #{$order['paymentID']}  was recorded as refunded in the amount of ${$amount}.", "Refunds");
	}
	
	public function get_payment_orders($paymentID) {
	$db = \Database::connection();
	$prefixes = member::get_tablePrefix_array();
	foreach ($prefixes as $tablePrefix){
		$table = "C5CBT_{$tablePrefix}_orders";
		$orders[$tablePrefix] = $db->getAll("SELECT * from $table WHERE paymentID = $paymentID");
	}
	return $orders;
	}
	
	
	
}