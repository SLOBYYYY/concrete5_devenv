<?php 
namespace Application\Controller\Training;
use Controller;
use User;
use UserInfo;

class Questions extends Controller {

		
	public function get_number_of_questions($tablePrefix, $exam_type=1) {
	$table = 'C5CBT_'.$tablePrefix.'_types';
    if(!$exam_type){
       $exam_type=1; 
    }
	$sql = "SELECT * FROM $table WHERE typeID = $exam_type";
	$db = \Database::connection();
	
	return $db->getRow($sql);
	}

	public function get_saved_test_questions($tablePrefix, $saved_exam){
	$examData = unserialize($saved_exam['examData']);
	$db = \Database::connection();
	$table = 'C5CBT_'.$tablePrefix.'_questions';
	
	foreach ($examData as $key=>$value){
		$questionID = explode('question', $key);
		
		if ($questionID[1] && !is_array($value)){
		$answers[$questionID[1]] = $value;
		}
	
	}
	
	foreach ($examData['questions'] as $questionID){
	if (isset($answers[$questionID])){
	$selected = $answers[$questionID];
	$sql = "SELECT * , $selected AS selected FROM $table where questionID = $questionID";
	$questions[] = $db->getRow($sql);
	} else {
	$sql = "SELECT * , 'no' AS selected FROM $table where questionID = $questionID";
	$questions[] = $db->getRow($sql);
	}
	}
		return $questions;
	}
	
	public function get_test_questions($tablePrefix, $final, $number){
		$db = \Database::connection();
		$table = 'C5CBT_'.$tablePrefix.'_questions';
		if ($final){
		$sql = "SELECT * FROM $table where final is TRUE order by RAND() limit $number";
		} else {
		$sql = "SELECT * FROM $table where practice is TRUE order by RAND() limit $number";
		}
		$questions = $db->getAll($sql);
		return $questions;
	}
	

	public function display_exam_questions($questions){
	$qnum = 0;
        $alphabet = array('A', 'B', 'C', 'D', 'E',
                       'F', 'G', 'H', 'I', 'J',
                       'K', 'l', 'm', 'n', 'o',
                       'p', 'q', 'r', 's', 't',
                       'u', 'v', 'w', 'x', 'y',
                       'z'
                       );
	foreach($questions as $question) {
		$qnum++;
	    $output.= "\n".'<h4 class="questionblock" id="questionblock'.$question['questionID'].'">'.$qnum.'. ' .$question['question'].'</h4>'."\n";
	    $answer = unserialize($question['answers']);
		$output .= '<div class="question-area">';
		foreach($answer as $aID=>$answertext) {
            $output.= '<label><input type="radio" value="'.$aID.'" name="question'.$question['questionID'] . '" ';
			if (isset($question['selected'])){
			if ($question['selected'] == $aID && $question['selected'] != 'no'){
			$output .= "checked=\"checked\"";
			}
			}
		$output .= '><div class="answer-text">'.$alphabet[$aID].". ".$answertext.'</div></label><br>';
		} 
		$questionlist .= $question['questionID'].',';
		$output.= '<input type="hidden" name="questions[]" value="'.$question['questionID'].'" />';
		$output .= '</div>';
	  }
	return $output;
	}
	
	public function display_incorrect_answer($tablePrefix, $questionID, $selected, $quesNum=Null){
	$db=\Database::connection();
	$table = 'C5CBT_'.$tablePrefix.'_questions';
	$question = $db->getRow("SELECT * FROM $table WHERE questionID = $questionID");
	$output.= "\n".'<h4 class="questionblock" id="questionblock'.$question['questionID'].'">'.$quesNum.". ".$question['question'].'</h4>'."\n";
	    $answer = unserialize($question['answers']);
    $alphabet = array( '','A', 'B', 'C', 'D', 'E',
                       'F', 'G', 'H', 'I', 'J',
                       'K', 'l', 'm', 'n', 'o',
                       'p', 'q', 'r', 's', 't',
                       'u', 'v', 'w', 'x', 'y',
                       'z'
                       );
		foreach($answer as $aID=>$answertext) {
            $aID++;
            
		$class = "answer-text";

		  if ($aID == $selected && $selected != NULL ){
			$class = "answer-text incorrect";
		  }
		  if ($aID == $question['correct']){
			$class = "answer-text correct";
		  }
          $output.= '<div style="display:inline-block" class="'.$class.'">'.$alphabet[$aID].". ".$answertext.'</div><br>';

	    }
	
	return $output;
	}
	
	
	public function display_incorrect_answer_old($questionid, $selected){
		$db=\Database::connection();
		$question_info = $db->getRow("SELECT * FROM ffquestions WHERE questionid = $questionid");
		$output = "<div class=\"question\">" .$question_info['question_text'] ."</div>
	";
		for($i=1;$i<=6;$i++) {
			$icon = "<div class=\"blank\">&nbsp;</div>";

			if ($question_info['answer' . $i]){
				if ($i == $selected){$icon = "<img  class=\"blank\" width=\"15\" src=\"/themes/acls/images/check_no.gif\">";}
				if ($i == $question_info['correct']){$icon = "<img class=\"blank\" width=\"15\" height=\"15\" src=\"/themes/acls/images/check.gif\">";}
				$output.="
			<div class=\"answer\">$icon <div class=\"text\">" . $question_info['answer' . $i] . "</div></div>
			";
			}	
		}
		return $output;
	}
	
	public function grade_exam($uID, $post_array, $exam_number, $type ){
		$num_questions = count($post_array['questions']);
		$num_correct = 0;
		foreach ($post_array['questions'] as $questionid){
			$user_answer = $post_array['question'.$questionid];
			$graded_exam[$questionid] = testsystem:: check_answer($questionid, $user_answer);
			if ($graded_exam[$questionid]['correct']){$num_correct++;}
		}
		$graded_exam[total_questions] = $num_questions;
		$graded_exam[total_correct] = $num_correct;
		return $graded_exam;
	}

	
	public function update_profile($uID=NULL, $details){
		
		//now set user attributes
		if ($uID){
			$uo = UserInfo::getByID($uID);
			
			$uo->setAttribute('firstname', $details['firstname']);
			$uo->setAttribute('lastname', $details['lastname']);
			$uo->setAttribute('company', $details['company']);
			$ship_address['address1'] = $details['streetaddress'];
			$ship_address['city'] = $details['city'];
			$ship_address['state_province'] = $details['state'];
			$ship_address['country'] = $details['country'];
			$ship_address['postal_code'] = $details['zipcode'];
			$uo->setAttribute('address', $ship_address);
			$uo->setAttribute('phonenumber', $details['phonenumber']);
			
			$uo->setAttribute('bill_firstname', $details['bill_firstname']);
			$uo->setAttribute('bill_lastname', $details['bill_lastname']);
			$uo->setAttribute('bill_company', $details['bill_company']);
			$bill_address['address1'] = $details['bill_streetaddress'];
			$bill_address['city'] = $details['bill_city'];
			$bill_address['state_province'] = $details['bill_state'];
			$bill_address['country'] = $details['bill_country'];
			$bill_address['postal_code'] = $details['bill_zipcode'];
			$uo->setAttribute('bill_address', $bill_address);
			$uo->setAttribute('bill_phonenumber', $details['bill_phonenumber']);
		}
	}
}