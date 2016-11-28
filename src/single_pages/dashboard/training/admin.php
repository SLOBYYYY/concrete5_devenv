<?php   defined('C5_EXECUTE') or die("Access Denied."); 
use Application\Controller\Training\Dashboard\Admin;
use Application\Controller\Training\Member;
use Application\Controller\Training\Shopping;
$h = Loader::helper('concrete/dashboard');
$nh = Loader::helper('navigation');
$currentPage = Page::getCurrentPage();
$db = Loader::db();
echo $h->getDashboardPaneHeaderWrapper(t("Admin Functions"));
?>

<input id="dialog_owner" type="hidden" name="dialog_owner" value=""/>
<div class="ccm-dashboard-header-buttons">
</div>
<div class="alert-message" style="margin-top:15px"><p>Message: 
		<?php  echo $alert_message ?>
		</p>
      </div>
	  <form action="" method="post">
		<input type="hidden" name="action" value="setupUserAttributes"/>
		<input class="btn" type="submit" value="Generate User Attributes"/>
	  </form>
	  <br/>
	  <form action="" method="post">
		<input type="hidden" name="action" value="setupUserGroups"/>
		<input class="btn" type="submit" value="Generate User Groups"/>
	  </form>
	   <br/>
	  <form action="" method="post">
		<input type="hidden" name="action" value="createPages"/>
		<input class="btn" type="submit" value="Create Pages"/>
	  </form>
	  
	 
	<form action="" method="post">
	<fieldset>
	<legend>Create new Course</legend>
	<label>Full Name:<input type="text" name="courseName" id="courseName" value="" /></label><br/>
	<label>Abbreviation:<input type="text" name="tabPref" id="tabPref" value="" />No spaces or punctuation</label><br/>
	<label>Certification Card Title:<input type="text" name="cardTitle" id="cardTitle" value="" /> ex:(Certified Personal Trainer)</label><br/>
	<label>Seed cID:<input type="text" name="seedCourse" id="seedCourse" value="" /></label><br/>
	<input type="hidden" name="action" value="createCourse"/>
	<input type="submit" name="submit" value="Create Course"/>
	</fieldset>
	</form>
	
	<form action="" method="post">
	<fieldset>
	<legend>Import/Update Exam Questions</legend>
	<label>TablePrefix:<input type="text" name="tablePrefix" id="tablePrefix" value="" /></label><br/>
	<label>CSV:</label><textarea name="examCSV" id="examCSV"></textarea><br/>
	<input type="hidden" name="action" value="importExam"/>
	<input type="submit" name="submit" value="Import Exam"/>
	</fieldset>
	</form>
	


<?php


echo $h->getDashboardPaneFooterWrapper(false);
	?>