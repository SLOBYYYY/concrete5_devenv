<?php 
namespace Application\Controller\Training;
use Controller;
use User;
use UserInfo;
Use Loader;
class Exam extends Controller {

	public function authorize_exam($uID, $tablePrefix, $examType=FALSE){
	$db = \Database::connection();
	$table = 'C5CBT_'.$tablePrefix.'_types';
	if ($examType){
	$typeID = $db->getOne("SELECT typeID FROM $table WHERE name LIKE '$examType'");
	$table = 'C5CBT_'.$tablePrefix.'_orders';
	return $db->getOne("SELECT orderID FROM $table WHERE UID = $uID AND typeID = $typeID AND completionID IS NULL");
	} else {
	$table = 'C5CBT_'.$tablePrefix.'_orders';
	return $db->getOne("SELECT orderID FROM $table WHERE UID = $uID AND completionID IS NULL");
	}
	}
	
	public function get_saved_exam($uID, $tablePrefix, $final=0){
	$db = \Database::connection();
	return $db->getRow("SELECT * FROM C5CBT_saved_exams WHERE uID = ? AND tablePrefix LIKE '$tablePrefix' AND final = ?",array($uID,$final));
	}
	
	public function save_exam($uID,$post_array, $tablePrefix, $final=0){
	$db = \Database::connection();
	$insert['uID'] = $uID;
	$insert['tablePrefix'] = $tablePrefix;
	$insert['final'] = $final;
	$insert['examData'] = serialize($post_array);
	$db->execute("DELETE FROM C5CBT_saved_exams WHERE uID = ? and tablePrefix LIKE '$tablePrefix' AND final = ?",array($uID,$final));
    $db->insert('C5CBT_saved_exams', $insert);
	}
	
	public function delete_exam($uID, $tablePrefix, $final = 0){
	$db = \Database::connection();
	$db->execute("DELETE FROM C5CBT_saved_exams WHERE uID = ? AND tablePrefix LIKE '$tablePrefix' AND final = ?", array($uID,$final));
	}
	
	public function grade_exam($uID,$post_array, $tablePrefix, $final){
		$num_questions = count($post_array['questions']);
		$num_correct = 0;
		foreach ($post_array['questions'] as $questionid){
			$user_answer = $post_array['question'.$questionid];
			$graded_exam[$questionid] = exam:: check_answer($questionid, $user_answer, $tablePrefix);
			if ($graded_exam[$questionid]['correct']){$num_correct++;}
		}
		$graded_exam[total_questions] = $num_questions;
		$graded_exam[total_correct] = $num_correct;
		return $graded_exam;
	}
	
	public function check_answer($questionid, $user_answer, $tablePrefix){
		$db=\Database::connection();
		$table = 'C5CBT_'.$tablePrefix.'_questions';
		$correct_answer = $db->getOne("SELECT correct FROM $table WHERE questionID = $questionid");
		$correct_answer--;
        if ($correct_answer == $user_answer){
			$return_value['correct'] = TRUE;
		} else {
			$return_value['correct'] = FALSE;
			$return_value['selected'] = $user_answer;
			$return_value['actual'] = $correct_answer;
		}
		return $return_value;
	}
	
	public function record_exam($uID, $typeID, $tablePrefix, $orderID, $final, $percent ,$passed=FALSE ){
		$db = \Database::connection();
		$insert['uID'] = $uID;
		$insert['final'] = $final;
		$insert['typeID'] = $typeID;
		$insert['orderID'] = $orderID;
		$insert['result'] = $percent;
		$insert ['passed'] = $passed;
		$table = 'C5CBT_'.$tablePrefix.'_userExams';
        $db->insert($table,$insert);
		return $db->Insert_ID();
	}
	
	
	public function record_completion($uID, $orderID, $examID, $tablePrefix, $typeID){
		$db=\Database::connection();
		$table = 'C5CBT_'.$tablePrefix.'_completions';
		$insert['uID'] = $uID;
		$insert['examID'] = $examID;
		$insert['typeID'] = $typeID;
		$insert['orderID'] = $orderID;
	    $db->insert($table,$insert);
		$completionID = $db->Insert_ID();
		$update['completionID'] = $completionID;
		$table = 'C5CBT_'.$tablePrefix.'_orders';
        $db->update($table, $update, array('orderID' => $orderID));
		//$db->AutoExecute($table, $update, 'UPDATE', "orderID = $orderID");
		return $completionID;
	}
	
	public function increment_attempt($tablePrefix, $orderID) {
		$db=\Database::connection();
		$table = 'C5CBT_'.$tablePrefix.'_orders';
		$db->execute("UPDATE $table SET attempts = attempts + 1 WHERE orderID=$orderID");
		return $db->getOne("SELECT attempts FROM $table WHERE orderID=$orderID");
	}
	
	public function send_exam_pass_emails($completionID, $tablePrefix){
		
		$db = \Database::connection();
		$table = 'C5CBT_'.$tablePrefix.'_completions';
		
		$completion_details = $db->GetRow("SELECT * FROM $table WHERE completionID = $completionID");

		$uID = $completion_details['uID'];
		$ui = UserInfo::getByID($uID);

		$fromEmail = "guythomas@gmail.com";
		$fromName = "ACLS Guy";
		$exam_email_cc = "";
		$signature = "Signature Here";
		$copyright = "Copyright Here";


		$table = 'C5CBT_'.$tablePrefix.'_userExams';
		$test = $db->getRow("SELECT * FROM $table WHERE examID = " . $completion_details['examID']);
		
		$table = 'C5CBT_'.$tablePrefix.'_orders';
		$order = $db->getRow("SELECT * FROM $table WHERE orderID = " . $completion_details['orderID']);
		
		$table = 'C5CBT_'.$tablePrefix.'_types';
		$test['examType'] = $db->getOne("SELECT name FROM $table WHERE typeID = " . $order['typeID']);

		
		
		//load user details into array
		$user['ceh'] = $ui->getAttribute('ceh');
		$user['firstname'] = $ui->getAttribute('firstname');
		$user['lastname'] = $ui->getAttribute('lastname');
		$user['company'] = $ui->getAttribute('company');
		$address = $ui->getAttribute('address');
		$user['streetaddress'] = $address->address1;
		$user['streetaddress2'] = $address->address2;
		$user['city'] = $address->city;
		$user['state'] = $address->state_province;
		$user['zipcode'] = $address->postal_code;
		$user['country'] = $address->country;
		$user['phonenumber'] = $ui->getAttribute('telephone');
		$user['emailaddress'] = $ui->getUserEmail();
		$user['bill_firstname'] = $ui->getAttribute('bill_firstName');
		$user['bill_lastname'] = $ui->getAttribute('bill_lastName');
		$user['bill_company'] = $ui->getAttribute('bill_company');
		$bill_address = $ui->getAttribute('bill_address');
		$user['bill_streetaddress'] = $bill_address->address1;
		$user['bill_streetaddress2'] = $bill_address->address2;
		$user['bill_city'] = $bill_address->city;
		$user['bill_state'] = $bill_address->state_province;
		$user['bill_zipcode'] = $bill_address->postal_code;
		$user['bill_country'] = $bill_address->country;
		$user['bill_phonenumber'] = $ui->getAttribute('bill_telephone');
		
		//setup the mail helper and send
		$mh = Loader::helper('mail');
		$mh->from($fromEmail);
		$mh->to($user['emailaddress']);
		$mh->addParameter('user', $user);
		$mh->addParameter('test', $test);
		$mh->addParameter('order', $order);
		$mh->load("C5CBT_".$tablePrefix."_examCongrats", "C5CBT");
		$mh->sendMail();
			
	}
	

}