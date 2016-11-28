<?php
use Application\Controller\Training\Questions;
$page = \Page::getCurrentPage();
if(isset($alert)){ ?>
    <div class="alert">
        <?=$alert; ?>
    </div>
<?php }

if ($questions){
?>
<form method="post" class="exam" id="examform" action="">
<div class="save-buttons">
	<div class="save-test-module">
		<div class="save-buttons-message">Don't want to finish the whole test in one sitting? Save your test here and come back at anytime.</div>
    	<div class="save-buttons-group">
		    <button type="submit" class="btn" id="save-test" name="action" value="save">Save Test</button>
		    <button type="submit" class="btn" id="delete-test" name="action" value="delete">Delete Test</button>
    	</div>
	</div>
</div>
<?php } ?>

<div class="exam-directions">
<?php
		if ($questions){?>
		
		<?php echo questions::display_exam_questions($questions);?>
	<br />
	  <input type="hidden" name="exam_id" value="<?php echo $exam_id?>"/>
	  <input type="hidden" name="uID" value="<?php echo $uID?>"/>
      <button type="submit" class="btn btn-primary btn-lg" name="action" value="grade">Grade Exam</button>

</form>  
<?php
	} elseif($results) {
	if($passed){?>
	<div class="success">Congratulations, You Passed with <?php echo $percent ?>%. You answered <?php echo 
	$correct ?> of <?php echo $total ?> questions correctly.</div>

	<?php } else {?>
	<div class="failure">Unfortunately, Your score of <?php echo $percent ?>% did not meet passing requirements of <?php echo $passing_percent?>%. You answered <?php echo 
	$correct ?> of <?php echo $total ?> questions correctly.</div>
	<?php } 
	if ($percent < 100){
	?>
	
	<h3>Please review your incorrect answers below:</h3>
	
	<?php
	}
    $quesNum = 1;
	foreach ($results as $questionID => $value){
	$value['selected']++;
	if (!$value['correct']) { // Incorrect Answer
	print questions::display_incorrect_answer($tablePrefix, $questionID, $value['selected'],$quesNum);
	}
    $quesNum++;
	}
    
    if(!$passed){
        $url = \URL::to($page);
        ?>
        
        <a class="button btn-primary" href="<?=$url;?>">Try Again.</a>
        <?php
    }
	}
	if ($no_questions){
		echo "You have no test attempts";
	}
?>

</div>
<br/>
<br/>