<?php   defined('C5_EXECUTE') or die("Access Denied."); 
use Application\Controller\Training\Dashboard\CourseDetails;
use Application\Controller\Training\Member;
use Application\Controller\Training\Shopping;
$h = Loader::helper('concrete/dashboard');
$nh = Loader::helper('navigation');
$currentPage = Page::getCurrentPage();
$db = Loader::db();
echo $h->getDashboardPaneHeaderWrapper(t("Course Details"));
?>

<input id="dialog_owner" type="hidden" name="dialog_owner" value=""/>
<div class="ccm-dashboard-header-buttons">
<select id="courseSelect" class="form-control">
<option value="">Select Course</option>
<?php
$courses = $db->getAll("SELECT * from C5CBT");
foreach ($courses as $cour){
    if($cour['tablePrefix'] == $_GET['course']){
        $selected = 'Selected="Selected"';
    } else {
        unset($selected);
    }
echo "<option value='{$cour['tablePrefix']}' {$selected}>{$cour['name']}</option>";
}
?>
</select>
</div>
<?php if($_GET['course']){?>
<fieldset>
<legend>Course Types</legend>
<table class="table">
    <thead>
    <tr>
    <th style="width:7%">ID</th><th>Name</th><th style="width:15%">Cost</th><th style="width:10%">Practice ?s</th><th style="width:10%">Final ?s</th><th style="width:10%">Passing %</th><th style="width:10%">Credits</th><th>Actions</th>
    </tr>
    </thead>
    <tbody>
<?php foreach ($types as $t){ ?>
    <tr>
    <td class="form-group">
        <input type="text" class="form-control" id="typeID" name="typeID" placeholder="" value="<?=$t['typeID']?>">
    </td>
    <td class="form-group">
        <input type="text" class="form-control" id="name" name="name" placeholder="Course Name" value="<?=$t['name']?>">
    </td>
    <td class="form-group">
        <input type="text" class="form-control" id="cost" name="cost" placeholder="Cost" value="<?=$t['cost']?>">
    </td>
    <td class="form-group">
        <input type="text" class="form-control" id="practiceQuestions" name="practiceQuestions" placeholder="" value="<?=$t['practiceQuestions']?>">
    </td>
    <td class="form-group">
        <input type="text" class="form-control" id="finalQuestions" name="finalQuestions" placeholder="" value="<?=$t['finalQuestions']?>">
    </td>
    <td class="form-group">
        <input type="text" class="form-control" id="passingGrade" name="passingGrade" placeholder="" value="<?=$t['passingGrade']?>">
    </td>
    <td class="form-group">
        <input type="text" class="form-control" id="credits" name="credits" placeholder="" value="<?=$t['credits']?>">
    </td>
    <td class="form-group">
    <div class="input-group-btn">
        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action <span class="caret"></span></button>
        <ul class="dropdown-menu dropdown-menu-right">
          <li><a href="#" class="updateType">Update</a></li>
          <li><a href="#" class="deleteType">Delete</a></li>
        </ul>
      </div><!-- /btn-group -->
    </td>
    </tr>
<?php } ?>

<tr>
    <td class="form-group">
        <input type="text" class="form-control" id="typeID" placeholder="" value="" disabled>
    </td>
    <td class="form-group">
        <input type="text" class="form-control" id="name" name="name" placeholder="Course Name" value="">
    </td>
    <td class="form-group">
        <input type="text" class="form-control" id="cost" name="cost" placeholder="Cost" value="">
    </td>
    <td class="form-group">
        <input type="text" class="form-control" id="practiceQuestions" name="practiceQuestions" placeholder="" value="">
    </td>
    <td class="form-group">
        <input type="text" class="form-control" id="finalQuestions" name="finalQuestions" placeholder="" value="">
    </td>
    <td class="form-group">
        <input type="text" class="form-control" id="passingGrade" name="passingGrade" placeholder="" value="">
    </td>
    <td class="form-group">
        <input type="text" class="form-control" id="credits" name="credits" placeholder="" value="">
    </td>
    <td class="form-group">
    <div class="input-group-btn">
        <button type="button" id="addType" class="btn btn-primary">Add</button>
      </div><!-- /btn-group -->
    </td>
    </tr>
    
</tbody>
</table>
</fieldset>

<fieldset>
<legend>Shipping Options</legend>
<table class="table">
    <thead>
    <tr>
    <th style="width:7%">ID</th><th>Name</th><th style="width:15%">Cost</th><th style="width:15%">Actions</th>
    </tr>
    </thead>
    <tbody>
<?php foreach ($ships as $t){ ?>
    <tr>
    <td class="form-group">
        <input type="text" class="form-control" id="shippingID" name="shippingID" placeholder="" value="<?=$t['shippingID']?>">
    </td>
    <td class="form-group">
        <input type="text" class="form-control" id="name" name="name" placeholder="Course Name" value="<?=$t['name']?>">
    </td>
    <td class="form-group">
        <input type="text" class="form-control" id="cost" name="cost" placeholder="Cost" value="<?=$t['cost']?>">
    </td>
        <td class="form-group">
    <div class="input-group-btn">
        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action <span class="caret"></span></button>
        <ul class="dropdown-menu dropdown-menu-right">
          <li><a href="#" class="updateShip">Update</a></li>
          <li><a href="#" class="deleteShip">Delete</a></li>
        </ul>
      </div><!-- /btn-group -->
    </td>
    </tr>
<?php } ?>

<tr>
    <td class="form-group">
        <input type="text" class="form-control" id="shippingID" placeholder="" value="" disabled>
    </td>
    <td class="form-group">
        <input type="text" class="form-control" id="name" name="name" placeholder="Shipping Name" value="">
    </td>
    <td class="form-group">
        <input type="text" class="form-control" id="cost" name="cost" placeholder="Cost" value="">
    </td>
    <td class="form-group">
    <div class="input-group-btn">
        <button type="button" id="addShip" class="btn btn-primary">Add</button>
      </div><!-- /btn-group -->
    </td>
    </tr>
    
</tbody>
</table>
</fieldset>


<fieldset>
<legend>Exam Questions</legend>
<table class="table">
    <thead>
    <tr>
    <th style="width:5%">ID</th><th>Question</th><th style="width:40%">Answers</th><th style="width:5%">Correct</th><th style="width:5%">Practice</th><th style="width:5%">Final</th><th style="width:5%">Actions</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($questions as $t){
        $answers = unserialize($t['answers']);
        $answersString = join(PHP_EOL,$answers);
        ?>
    <tr >
    <td class="form-group">
        <input type="text" class="form-control" id="questionID" name="questionID" placeholder="" value="<?=$t['questionID']?>">
    </td>
    <td class="form-group">
        <textarea style="height:6rem;" class="form-control" id="question" name="question"><?=$t['question']?></textarea>
    </td>
    <td class="form-group">
        <textarea class="form-control" style="height:6rem;" id="answers" name="answers"><?=$answersString?></textarea>
    </td>
    <td class="form-group">
        <input type="text" class="form-control" id="correct" name="correct" placeholder="" value="<?=$t['correct']?>">
    </td>
    <td class="form-group">
        <input type="checkbox" id="practice" name="practice" value="true" aria-label="Practice Exam" <?php if($t['practice'] > 0){echo 'checked="checked"'; }?>>
    </td>
    <td class="form-group">
        <input type="checkbox" id="final" name="final" value="true" aria-label="Final Exam" <?php if($t['final'] > 0){echo 'checked="checked"'; }?>>
    </td>
        <td class="form-group">
    <div class="input-group-btn">
        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action <span class="caret"></span></button>
        <ul class="dropdown-menu dropdown-menu-right">
          <li><a href="#" class="updateQuestion">Update</a></li>
          <li><a href="#" class="deleteQuestion">Delete</a></li>
        </ul>
      </div><!-- /btn-group -->
    </td>
    </tr>
    <?php } ?>
    
    <tr >
    <td class="form-group">
        <input type="text" class="form-control" id="questionID" name="questionID" placeholder="" disabled>
    </td>
    <td class="form-group">
        <textarea style="height:6rem;" class="form-control" id="question" name="question"></textarea>
    </td>
    <td class="form-group">
        <textarea class="form-control" style="height:6rem;" id="answers" name="answers"></textarea>
    </td>
    <td class="form-group">
        <input type="text" class="form-control" id="correct" name="correct" placeholder="" value="">
    </td>
    <td class="form-group">
        <input type="checkbox" id="practice" name="practice" value="true" aria-label="Practice Exam" checked="checked">
    </td>
    <td class="form-group">
        <input type="checkbox" id="final" name="final" value="true" aria-label="Final Exam" checked="checked">
    </td>
        <td class="form-group">
    <div class="input-group-btn">
        <button type="button" id="addQuestion" class="btn btn-primary">Add</button>
      </div><!-- /btn-group -->
    </td>
    </tr>
    
    </tbody>
</table>
    
    
</fieldset>
<?php 
}
echo $h->getDashboardPaneFooterWrapper(false);?>
    