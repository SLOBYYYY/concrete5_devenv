<h1>Shopping Cart</h1>
<table class="table">
<thead>
<tr>
<th>Qty.</th><th>Course</th><th>Price</th><th>Delete</th>
</tr>
</thead>
<tbody>

<?php
$db = \Database::connection();

	foreach($cart as $c){
		$courseParts = explode("_",$c);
		$tablePrefix = $courseParts[0];
		$typeID = $courseParts[1];
		$sql = "SELECT * FROM C5CBT_{$tablePrefix}_types WHERE typeID = ?";
		$courseData = $db->fetchAssoc($sql,array($typeID));
		
	if($_REQUEST["{$c}_quantity"]){
		$quantiy =$_REQUEST["{$c}_quantity"];
	} else {
		$quantity = 1;
	}
	
	
	
echo "<tr><td><input type='number' name='{$c}' value='{$quantity}'/></td><td>{$courseData['name']}</td><td>{$courseData['cost']}</td><td>D</td></tr>";	
	}
	?>

</tbody>
</table>
