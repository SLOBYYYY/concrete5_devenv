<?php
namespace Application\Block\ExternalForm\Form\Controller;
use Concrete\Core\Controller\AbstractController;
use Concrete\Core\User\User;
use Application\Controller\Training\Questions;
use Application\Controller\Training\Exam as exm;
use Application\Controller\Training\Email;
use Application\Controller\Training\Member;

class Exam extends AbstractController
{
    
    public function action_submit($bID = false)
    {
       
    }

    public function action_grade($bID = false)
    {
        unset($_POST['action']);
        $page = \Page::getCurrentPage();
        $tablePrefix = $page->getAttribute('tablePrefix');
		$this->set('tablePrefix',$tablePrefix);
		if ($tablePrefix){
		$final = $page->getAttribute('finalExam');
		        
        if($final){
			$examType = member::get_exam_type($uID, $tablePrefix);
            if(!$examType){
            $examType=1;
            }
		} else {
			$examType = 1;
		}
		$this->set('final', $final);
		}
		
		if ($examType){
		$exam_info = questions::get_number_of_questions($tablePrefix, $examType);
		$typeID = $exam_info['typeID'];
		$column = ($final ? 'finalQuestions' : 'practiceQuestions');
		$numberofquestions = $exam_info[$column];
		$answerstopass = intval(($exam_info['passingGrade'] * .01)*$numberofquestions);
		} else {
		$no_questions = true;
		}
        
            $u = new User();
            $uID = $u->getUserID();
            $exam_id = intval($_POST['exam_id']); // set unique timestamp	
			unset($_POST['exam_id']);
			$prev_id =  $u->config('exam'); // get the ID of the last test the user submitted for grading
			
			if ($prev_id != $exam_id){ // The user hasn't refreshed the results page (causing a second grading) so give them a new test
				$u->saveConfig('exam', $exam_id); //attach the exam to the user
				exm::delete_exam($uID, $tablePrefix, $final);
				$results = exm::grade_exam($uID,$_POST, $tablePrefix, $final); //grade the exam
				//caluclate percentages
				$per1 = $results['total_correct'] / $results['total_questions'];
				$percent = number_format($per1 * 100, 0);
				$per2 = $answerstopass / $results['total_questions'];
				$passing_percent = number_format($per2 * 100, 0);
				$passing_percent = $exam_info['passingGrade'];
				if ($percent >= $passing_percent){
					$passed = TRUE; 
				}
				
				// set variables for display to the user
				
				$this->set('correct', $results['total_correct']);
				$this->set('total', $results['total_questions']);
				$this->set('passed', $passed);
				$this->set('percent', $percent);
				$this->set('passing_percent', $passing_percent);
				
				//record the exam
				//die("checking on $uID, $tablePrefix, $typeID");
				$orderID = member::get_order($uID, $tablePrefix, $typeID);
				if($orderID){
				$examID = exm::record_exam($uID, $typeID, $tablePrefix, $orderID, $final, $percent ,$passed);
                } else {
                    $this->set('alert','Order ID did not come back');
                    self::view();
                }
				//remove the oddball vars added to the results so results can easily be looped in the view
				unset($results['total_questions']);
				unset($results['total_correct']);
				
				// now send the questions with answers to the view for display
				$this->set('results', $results);
				
				
				if ($final){ //test was a final exam
					if ($passed){
                          if($orderID && $examID){
						$completionID = exm::record_completion($uID, $orderID, $examID, $tablePrefix, $typeID);
						//send emails
						email::send_exam_pass_emails($completionID, $tablePrefix);
						//remove from group
						member::remove_from_group($uID, $tablePrefix);
						User::loginByUserID($uID);
                         } else {
                           $this->set('alert','Order ID and Exam ID were not returned');
							self::view();  
                         }
					}
				}
			}
    }

    public function action_delete($bID = false)
    {
				unset($_POST['action']);
                $page = \Page::getCurrentPage();
                $u = new User();
                $uID = $u->getUserID();
                $tablePrefix = $page->getAttribute('tablePrefix');
                $final = $page->getAttribute('finalExam');


                exm::delete_exam($uID, $tablePrefix, $final);
                $this->set('alert','Your exam was deleted and reset with new questions.');
				$this->view(); 
    }
    
        public function action_save($bID = false)
    {
				unset($_POST['action']);
                $page = \Page::getCurrentPage();
                $u = new User();
                $uID = $u->getUserID();
                $tablePrefix = $page->getAttribute('tablePrefix');
                $final = $page->getAttribute('finalExam');
                exm::save_exam($uID,$_POST, $tablePrefix, $final);
				$_POST['save_test'] = false;
                $this->set('alert','Your exam has been saved.');
				$this->view();
    }
    
    public function view()
    {
        $al = \Concrete\Core\Asset\AssetList::getInstance();
        $al->register('css', 'exam', 'css/exam.css');
        $this->requireAsset('css','exam');
          
        $page = \Page::getCurrentPage();
        $tablePrefix = $page->getAttribute('tablePrefix');
        
        if($tablePrefix){
		$this->set('tablePrefix',$tablePrefix);
		$final = $page->getAttribute('finalExam');
		$this->set('final', $final);
		$u = new User();
        $uID = $u->getUserID();
        $this->set('uID',$uID);
        if($final){
			$examType = member::get_exam_type($uID, $tablePrefix);
		} else {
			$examType = 1;
		}
        
        $exam_info = questions::get_number_of_questions($tablePrefix, $examType);
		$typeID = $exam_info['typeID'];
		$column = ($final ? 'finalQuestions' : 'practiceQuestions');
		$numberofquestions = $exam_info[$column];
		$answerstopass = intval(($exam_info['passingGrade'] * .01)*$numberofquestions);
		
		 switch($_POST['action']){
            case "save":
            self::action_save();
            break;
            case "delete":
            self::action_delete();
            break;
            case "grade":
            self::action_grade();
			$graded = true;
            break;
            default:
        }
		
		if(!$graded){
			// print the exam
			$stamp = time(); //generate unique timestamp
			$this->set('exam_id', $stamp);
			$saved_exam = exm::get_saved_exam($uID, $tablePrefix, $final);
			if ($saved_exam){
				//Load up the saved exam
				$questions = questions::get_saved_test_questions($tablePrefix, $saved_exam);
			} else {
			//create a new exam
			$questions = questions::get_test_questions($tablePrefix, $final, $numberofquestions);
			}
			$this->set('questions', $questions);
		}
			
    } else {
        echo "Page Requires TablePrefix Attribute for Display";
    }
	}
}
