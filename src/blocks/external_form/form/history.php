<?php
use Application\Controller\Training\Member;
$currentPage = Page::getCurrentPage();
Loader::helper('navigation');
$db=Loader::db();
?>

<div class="my-history">
<?php
if(count($exams) < 1){
foreach ($exams as $tablePrefix=>$exam_grp){
if ($exam_grp){
?>
<fieldset>
<legend><?php echo strtoupper($tablePrefix);?> Exam History</legend>
<?php?>
<div class="table-responsive">
<table class="table table-bordered">
<thead>
	<th>Exam Date</th>
	<th>Exam Type</th>
	<th>Score</th>
	<th>Passed</th>
</thead>
<tbody>
	<?php foreach ($exam_grp as $exam){
		$date  = date("n/j/Y g:i a", strtotime($exam['timestamp']) + 3600);
		if ($exam['final']) {
			$type = "Final";
		} else {
			$type = "Practice";
		}
		if ($exam['passed']) {
			$passed = "Y";
		} else {
			$passed = "N";
		}
	?>
	<tr>
		<td><?php echo $date ?></td>
		<td><?php echo $type ?></td>
		<td><?php echo $exam['result'] ?>%</td>
		<td><?php echo $passed ?></td>
	</tr>
	<?php } ?>
</tbody>
</table>
</div>
</fieldset>
<?php
}
} 
} else {
    echo "<h3>No Exam History</h3>";
}
if(count($orders) < 1){
foreach ($orders as $tablePrefix=>$order_grp){
if ($order_grp){
?>
<fieldset>
<legend><span><?php echo strtoupper($tablePrefix);?> Order History</span></legend>

<?php foreach ($order_grp as $order){
		$date  = date("n/j/Y g:i a", strtotime($order['timestamp']) + 3600);
		$type = member::get_type_name($order['typeID'], $tablePrefix);
		$features = unserialize($order['features']);
		if ($order['completionID']){
		$completion_info = member::get_completion_info($order['completionID'], $tablePrefix);
			if ($completion_info['fulfilled']){
			$completed = date("n/j/Y g:i a", strtotime($completion_info['fulfilledTimestamp']) + 3600);

			} else {
			$completed = "Processing";
			}
			$docs = TRUE;
		} else {
		$docs = FALSE;
		$completed = "No";
		}
	?>
<div class="table-responsive">
<table class="table table-bordered">
<thead>
	<th>Order</th>
	<th>Date</th>
	<th>Exam Type</th>
	<th align="center">Completed</th>
	<th>Shipping</th>

</thead>
<tbody>

	<tr>
		<td><?php echo $order['orderID']?></td>
		<td><?php echo $date ?></td>
		<td><?php echo $type ?></td>
		<td align="center"><?php echo $completed ?></td>
		<td><?php echo $order['shipping'] ?></td>
	</tr>
	<tr>
	<td class="historylabel" colspan="3"><b>Features</b></td>
	<td class="historylabel" colspan="2" align="center"><b>Downloads</b></td>
	</tr>
	<tr class=>
	<td colspan="3" class="ccm-ui">
	<?php if ($features) {
	echo "<ul>";
		foreach ($features as $feat) {
		echo "<li>$feat</li>";
		}
	echo "</ul>";
	}?>
	</td>
	<td colspan="2" align="center">
	<?php if ($docs){
	$test = strtoupper($tablePrefix);
	echo '<a href="'.DIR_REL.'/member/document_view/?o='.$order['orderID'].'&t='.$tablePrefix.'&u='.$uID.'&d=p" target="blank">'.$test.' Certification Card</a><br/>';
	echo '<a href="'.DIR_REL.'/member/document_view/?o='.$order['orderID'].'&t='.$tablePrefix.'&u='.$uID.'&d=c" target="blank">'.$test.' CEH Certificate</a><br/>';
	} else {
	echo "None";
	} ?>
	</td>
	</tr>

</tbody>
</table>
</div>
<?php } ?>
</fieldset>
<?php
}
}
} else {
    echo "<h3>No Order History</h3>";
}

foreach ($transfers as $tablePrefix=>$order_grp){
if ($order_grp){
?>
<fieldset>
<legend><span><?php echo strtoupper($tablePrefix);?> Transfer History</span></legend>

<?php foreach ($order_grp as $order){
		$date  = date("n/j/Y g:i a", strtotime($order['timestamp']) + 3600);
		$transfer_date  = date("n/j/Y g:i a", strtotime($order['transferDate']) + 3600);
		$type = member::get_type_name($order['typeID'], $tablePrefix);
		$TuID = $order['uID'];
		$u = User::getByUserID($TuID);
		$sub_email = $u->getUserName();
		$features = unserialize($order['features']);
		if ($order['completionID']){
		$completion_info = member::get_completion_info($order['completionID'], $tablePrefix);
			if ($completion_info['fulfilled']){
			$completed = date("n/j/Y g:i a", strtotime($completion_info['fulfilledTimestamp'])+3600);

			} else {
			$completed = "Processing";
			}
			$docs = TRUE;
		} else {
		$docs = FALSE;
		$completed = "No";
		}
	?>
<div class="table-responsive">
<table class="table table-bordered">
<thead>
	<th>Order</th>
	<th>Transfer Date</th>
	<th>Exam Type</th>
	<th align="center">Completed</th>
	<th>Shipping</th>

</thead>
<tbody>

	<tr>
		<td><?php echo $order['orderID']?></td>
		<td><?php echo $transfer_date ?></td>
		<td><?php echo $type ?></td>
		<td align="center"><?php echo $completed ?></td>
		<td><?php echo $order['shipping'] ?></td>
	</tr>
	<tr>
	<td class="histoylabel" colspan="1"><b>User</b></td>
	<td class="historylabel" colspan="2"><b>Features</b></td>
	<td class="historylabel" colspan="2" align="center"><b>Downloads</b></td>
	</tr>
	<tr>
	<td>
	<?php echo $sub_email ?>
	</td>
	<td colspan="2">
	<?php if ($features) {
	echo "<ul>";
		foreach ($features as $feat) {
		echo "<li>$feat</li>";
		}
	echo "</ul>";
	} else {
	$table = "C5CBT_{$tablePrefix}_features";
			$possible_features = $db->getAll("SELECT * FROM $table");
				if ($possible_features){
					foreach ($possible_features as $f){
						$bls = strpos($f['name'], "BLS");

						if ($bls === 0){

						?>
						<form method="POST" action="<?php echo SECURE_BASE_URL.DIR_REL ?>/shopping_cart">
						<input type="hidden" name="addon" value="1"/>
						<input type="hidden" name="type[]" value="<?php echo $tablePrefix . "_" . $order['typeID']?>"/>
						<input type="hidden" name="orderID" value="<?php echo $order['orderID']?>"/>
						<input type="hidden" name="<?php echo "{$tablePrefix}[features][1]"; ?>" value="<?php echo $f['featureID'] ?>"/>
						<button class="btn btn-danger btn-block" type="submit" style="font-size:10px;float:left;clear:both;margin:3px 0">Add <?php echo $f['name'] ?> - $<?php echo $f['cost'] ?></button>
						</form>

					<?php }
					}
				}
	}?>
	</td>
	<td colspan="2" align="center">
	<?php if ($docs){
	$test = strtoupper($tablePrefix);
	echo '<a href="'.DIR_REL.'/member/document_view/?o='.$order['orderID'].'&t='.$tablePrefix.'&u='.$uID.'&d=p" target="blank">'.$test.' Certification Card</a><br/>';

		if (stristr($order['features'],'BLS') ){
		echo '<a href="'.DIR_REL.'/member/document_view/?o='.$order['orderID'].'&t='.$tablePrefix.'&u='.$uID.'&d=b" target="blank">BLS Certification Card</a><br/>';
		}
	} else {
	echo "None";
	} ?>
	</td>
	</tr>

</tbody>
</table>
</div>
<?php } ?>
</fieldset>
<?php
}
}?>

</div>
