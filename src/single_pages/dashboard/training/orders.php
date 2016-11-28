<?php   defined('C5_EXECUTE') or die("Access Denied."); 
use Application\Controller\Training\Dashboard;
use Application\Controller\Training\Member;
use Application\Controller\Training\Shopping;
$h = Loader::helper('concrete/dashboard');
$nh = Loader::helper('navigation');
$currentPage = Page::getCurrentPage();

$url_base = $nh->getLinkToCollection($currentPage, true);
$db = Loader::db();
$course_name = dashboard::get_course_name($tablePrefix);
echo $h->getDashboardPaneHeaderWrapper(t("Order Details"));
?>

<input id="dialog_owner" type="hidden" name="dialog_owner" value=""/>
<div class="ccm-dashboard-header-buttons">
<form action="" method="GET" class="form-inline">
<div class="form-group">
<input type="text" class="form-control" placeholder="First Name, Last Name, or Email" id="keyword" name="keyword"/>
<input type="submit" value="Search" class="btn btn-success" name="submit"/>
</div>
</form>


<form action="" method="GET" class="form-inline">
<div class="form-group">
<select name="filter" class="form-control">
  <option value="all" <?php if ($filter == "all"){echo "SELECTED";} ?>>All Orders</option>
  <option value="complete" <?php if ($filter == "complete"){echo "SELECTED";} ?>>Completed Orders</option>
  <option value="ship" <?php if ($filter == "ship"){echo "SELECTED";} ?>>Ready for Shipping</option>
  <option value="shipnext" <?php if ($filter == "shipnext"){echo "SELECTED";} ?>>Ready for Next Day Shipping</option>
  <option value="active" <?php if ($filter == "active"){echo "SELECTED";} ?>>Awaiting Completion</option>
  <option value="activenext" <?php if ($filter == "activenext"){echo "SELECTED";} ?>>Awaiting Completion with Next Day Shipping</option>
</select>
<input type="submit" value="Filter" class="btn btn-info" name="submit"/>
</div>
</form>
</div>
<div class="alert-message" style="display:none;margin-top:15px"><p>
		<?php  echo $alert_message ?>
		</p>
      </div>
<table class="table table-condensed">
	<thead>
		<th style="width:35%">Member Name</th>
		<th  style="width:15%">
		<?php if ($filter == "ship") {
		echo "Complete Date";
		} else {
		echo "Order Date";
		}?>
		</th>
		<th style="width:20%">Course</th>
		<th style="width:5%">ID</th>
		<th style="width:5%">Sts</th>
		<th style="width:20%">Actions</th>
	</thead>
	<tbody>
	<?php
	if ($orders){
	foreach ($orders as $order){
		unset($completion_info);
		unset($action_options);
		$ui = UserInfo::getByID($order['uID']);
		if ($ui){
		$first = $ui->getAttribute('firstName');
		$last = $ui->getAttribute('lastName');
		$email = $ui->getUserEmail();
		$address = $ui->getAttribute('address');
		$ceh = $ui->getAttribute('ceh');
		}
		$features = unserialize($order['features']);
	
	$exam_type = shopping::get_course_name($order['course'], $order['typeID']);
	//check status
	if (is_null($order['completionID'])){
	//awaiting user to pass test or exhaust attempts
		$status = "new";
	} else {
	//user has either passed or failed on all attempts
		if ($order['completionID'] != 0){
		//user has a valid completion ID Now determine if the completion has been fulfilled
		$completion_info = member::get_completion_info($order['completionID'], $order['course']);
			if ($completion_info['fulfilled']){ //check if the order has been marked as fulfilled
				$status = "shipped";
			/////////////check is user is on reshipment list
				if($db->getOne("SELECT reshipmentID from C5CBT_reshipments WHERE tablePrefix LIKE '{$order['course']}' AND orderID ={$order['orderID']}")){
				$status = "reship";
				}
			}else{ //awaiting fulfillment
				$status = "waiting";
			}
		} else {
		//user has failed on all attempts
		$status = "fail";
		}
	}

	
	?>
	<tr class="<?php echo $order['course'] ?> order_row">
		<td><a href = "<?php echo DIR_REL ?>/dashboard/users/search/view/<?php echo $order['uID'] ?>">
		<?php echo $last . "," . $first; ?>
		</a><br />
		<?php echo $email ?>
		</td>
		<td><?php 
		if ($filter == "ship"){
		echo date("m/d/Y", strtotime($order['fulfilledTimestamp']));
		} else {
		echo date("m/d/Y", strtotime($order['timestamp']));
		}
		?></td>
		<?php if ($exam_type){?>
		<td><?php echo $exam_type ?></td>
		<?php } else {?>
		<td><?php echo $order['course'] ?></td>
		<?php } ?>
		<td><?php echo $order['orderID'] . $order['typeID'] ;?></td>
		<td>
			<?php if ($status == "waiting"){
				$action_options .= "<option value=\"fulfill\">Mark as Shipped</option>";
                $action_options .= "<option value=\"change_date\">Change Completion Date</option>";
			?>
			<span title="Waiting for Shipment" class="label label-warning"><i class="icon-exclamation-sign icon-white"></i></span>
			
			<?php }
			
			if ($status == "new"){
			$action_options .= "<option value=\"pass\">Pass User</option>";
            $action_options .= "<option value=\"swap_course\">Swap Course</option>";
			?>
			<span  title="Waiting for User to Complete Test" class="label label-info"><i class="icon-user icon-white"></i></span>
			<?php }
			
			if ($status == "fail"){ ?>
			<span  title="User exceeded retry attempts" class="label label-warning"><i class="icon-remove icon-white"></i></span>
			<?php }
			
			////////			
			if ($status == "shipped"){ 
			$action_options .= "<option value=\"unfulfill\">Mark as Pending</option>";
			$action_options .= "<option value=\"reship\">Reship this Order</option>";
            $action_options .= "<option value=\"change_date\">Change Completion Date</option>";
			?>
			<span  title="Order Shipped" class="label label-success"><i class="icon-ok icon-white"></i></span>
			<?php }
			
			if ($status == "reship"){ 
			$action_options .= "<option value=\"unreship\">Remove from Reship List</option>";
            $action_options .= "<option value=\"change_date\">Change Completion Date</option>";
			?>
			<span  title="Awaiting Reshipment" class="label label-warning" style="padding:3px 5px 5px"><i class="icon-ok icon-white"></i></span>
			<?php }
			
			$table = "C5CBT_{$order['course']}_features";
			$possible_features = $db->getAll("SELECT * FROM $table WHERE featureGroup = 1");
				if ($possible_features){
					foreach ($possible_features as $f){
						$action_options .= "<option value=\"add_{$f['featureID']}\">Add {$f['name']}</option>";
					}
				}
			$action_options .= "<option value=\"delete\">Delete</option>";
			$action_options .= "<option value=\"refund\">Refund</option>";
            $action_options .= "<option value=\"add\">Add New</option>";
			?>
			
			</td>
		<td>				
					
		<form action="" method="GET" class="form-inline" id="<?php echo $order['orderID'];?>">
        <div class="form-group">
		<input type="hidden" name="aid" value="<?php echo $order['orderID'];?>" />
		<input type="hidden" name="c" value ="<?php echo $order['course'] ?>" />
		<input type="hidden" name="o" value ="<?php echo $offset ?>" />
		<input type="hidden" name="l" value ="<?php echo $limit ?>" />
		<input type="hidden" name="filter" value ="<?php echo $filter ?>" />
		<input type="hidden" name="keyword" value ="<?php echo $keyword ?>" />
		<select id="actionSelect" name="a" style="width:79%!important" class="actionSelect span2 form-control">
                <option>Actions</option>
		<?php echo $action_options ; ?>
              </select>
              <a href="#" class="show_orders btn small info btn-default" style="padding: 5px"><i class="icon-chevron-down"></i></a>
              </div>
		</form>
		
		</td>
	</tr>
	<tr class="order-row" style="display:none;">
		<td colspan=6>
		<table class="table table-condensed table-bordered">
			<thead>
				<th>Address</th>
				<th>Shipping</th>
				<?php if ($features) { ?>
				<th>Features</th>
				<?php  } ?>
				<?php if($completion_info){ ?>
				<th>
				<?php if ($filter == "ship") {
				echo "Order Date";
				} else {
				echo "Completed";
				}?>
				</th>
				<th>Documents</th>
				<?php } ?>
			</thead>
			<tbody>
				<tr>
					<td>
					<?php echo $first . " " . $last; ?><br/>
					<?php if ($address){
					echo $address->getAddress1(); ?><br/>
					<?php if($address->getAddress2()){
					echo $address->getAddress2(); ?><br/>
					<?php }
					echo $address->getCity(); ?>, 
					<?php
					echo $address->getStateProvince(); ?> <?php echo $address->getPostalCode();
					
					if ($address->getCountry() != "US") {
					echo "<br/> " . $address->getCountry();
					}
					}
					?>
					
					</td>
					<td><?php echo $order['shipping'] ?></td>
					<?php if ($features) { ?>
					<td><?php if ($features) {
						foreach ($features as $feat) {
						echo "$feat,";
						}
				}?></td>
				<?php } ?>
					<?php if($completion_info){ ?>
					<td><?php 
					if ($filter == "ship"){
					echo date("m/d/Y", strtotime($order['timestamp']));
					} else {
					echo date("m/d/Y", strtotime($completion_info['fulfilledTimestamp']));
					}
					?>
					</td>
					<td>
					<?php 
						$test = strtoupper($order['course']);
						echo '<a href="'.DIR_REL.'/member/document_view/?o='.$order['orderID'].'&t='.$order['course'].'&u='.$order['uID'].'&d=p" target="blank">'.$test.' Card</a><br/>';
		
						if (stristr($order['features'],'BLS') ){
						echo '<a href="'.DIR_REL.'/member/document_view/?o='.$order['orderID'].'&t='.$order['course'].'&u='.$order['uID'].'&d=b" target="blank">BLS Card</a><br/>';
						echo '<a href="'.DIR_REL.'/member/document_view/?o='.$order['orderID'].'&t='.$order['course'].'&u='.$order['uID'].'&d=cb" target="blank">BLS CEH</a><br/>';
						}
						
						echo '<a href="'.DIR_REL.'/member/document_view/?o='.$order['orderID'].'&t='.$order['course'].'&u='.$order['uID'].'&d=l" target="blank">Thank You Letter</a><br/>';
						//if ($ceh){ //displaying ceh download for all users now.
						echo '<a href="'.DIR_REL.'/member/document_view/?o='.$order['orderID'].'&t='.$order['course'].'&u='.$order['uID'].'&d=c" target="blank">'.$test.' CEH</a><br/>';
						//}
					?>
					
					</td>

					
					<?php } ?>
				</tr>
			</tbody>
		</table>
		</td>
	</tr>

	<?php }
		} else {
	echo "There are no orders that match your criteria";
	}?>
	
	
	</tbody>
</table>

<div id="unfulfill_dialog" class="ccm-ui" style="display:none">
<p> Are you sure you want to place this order into the "awaiting shipment" status?</p>
<div class="dialog-buttons">
	<a href="javascript:void(0)" onclick="jQuery.fn.dialog.closeTop();" class="ccm-button-left btn btn-danger"><?php echo t('Cancel')?></a>
	<a href="javascript:void(0)" class="btn primary ccm-button-right" onclick="orderaction()"><?php echo t('Confirm')?></a>
</div>
</div>

<div id="delete_dialog" class="ccm-ui" style="display:none">
<p> Are you sure you want to completely delete this order?</p>
<div class="dialog-buttons">
	<a href="javascript:void(0)" onclick="jQuery.fn.dialog.closeTop();" class="ccm-button-left btn btn-danger"><?php echo t('Cancel')?></a>
	<a href="javascript:void(0)" class="btn danger ccm-button-right" onclick="orderaction()"><?php echo t('Delete Order')?></a>
</div>
</div>

<div id="pass_dialog" class="ccm-ui" style="display:none">
<p> Are you sure you want to manually pass this user?</p>
<label><input type="checkbox" name="send_email" id="send_email" onchange="add_form_element(this)" /> Send Pass Emails</label><br /><br />
<div class="dialog-buttons" style="clear:both">
	<a href="javascript:void(0)" onclick="jQuery.fn.dialog.closeTop();" class="ccm-button-left btn btn-danger"><?php echo t('Cancel')?></a>
	<a href="javascript:void(0)" class="btn btn-success ccm-button-right" onclick="orderaction()"><?php echo t('Pass User')?></a>
</div>
</div>

<div id="retry_dialog" class="ccm-ui" style="display:none">
<p> Are you sure you want to add an additional exam attempt?</p>
<label><input style="float:left" type="checkbox" name="send_email" id="send_email" onchange="add_form_element(this)" /> Send Retry Email</label><br /><br />
<div class="dialog-buttons" style="clear:both">
	<a href="javascript:void(0)" onclick="jQuery.fn.dialog.closeTop();" class="ccm-button-left btn btn-danger"><?php echo t('Cancel')?></a>
	<a href="javascript:void(0)" class="btn btn-success ccm-button-right" onclick="orderaction()"><?php echo t('Pass User')?></a>
</div>
</div>

<div id="feature_dialog" class="ccm-ui" style="display:none">
<p> Are you sure you want to add this feature?</p>
<div class="dialog-buttons" style="clear:both">
	<a href="javascript:void(0)" onclick="jQuery.fn.dialog.closeTop();" class="ccm-button-left btn btn-danger"><?php echo t('Cancel')?></a>
	<a href="javascript:void(0)" class="btn btn-success ccm-button-right" onclick="orderaction()"><?php echo t('Add Feature')?></a>
</div>
</div>

<div id="refund_dialog" class="ccm-ui" style="display:none">
<label style="width:300px; float:none; text-align: left;">Refund Amount: $<input type="text" name="refund_amount" id="refund_amount" onchange="add_form_element(this)" /></label><br /><br />
<label  style="width:300px; float:none; text-align: left;">Refund Reason: <input type="text" name="refund_reason" id="refund_reason" onchange="add_form_element(this)" /></label><br /><br />
<label  style="width:300px; float:none; text-align: left;">Administrative Contact: <input type="text" name="refund_contact" id="refund_contact" onchange="add_form_element(this)" /></label><br /><br />
<label  style="width:300px; float:none; text-align: left;"><input type="checkbox" name="disable_order" id="disable_order" onchange="add_form_element(this)" /> Disable This Order</label><br /><br />
<label  style="width:300px; float:none; text-align: left;"><input type="checkbox" name="disable_all_orders" id="disable_all_orders" onchange="add_form_element(this)" /> Disable All Orders Associated with this Payment</label><br /><br />
<div class="dialog-buttons" style="clear:both">
	<a href="javascript:void(0)" onclick="jQuery.fn.dialog.closeTop();" class="ccm-button-left btn btn-danger"><?php echo t('Cancel')?></a>
	<a href="javascript:void(0)" class="btn btn-success ccm-button-right" onclick="orderaction()"><?php echo t('Refund Order')?></a>
</div>
</div>

<div id="change_date_dialog" class="ccm-ui" style="display:none">
<label style="width:300px; float:none; text-align: left;">New Pass Date: <input type="text" name="pass_date" id="pass_date" onchange="add_form_element(this)" /></label><br /><br />
<div class="dialog-buttons" style="clear:both">
	<a href="javascript:void(0)" onclick="jQuery.fn.dialog.closeTop();" class="ccm-button-left btn btn-danger"><?php echo t('Cancel')?></a>
	<a href="javascript:void(0)" class="btn btn-success ccm-button-right" onclick="orderaction()"><?php echo t('Change Pass Date')?></a>
</div>
</div>

<div id="swap_dialog" class="ccm-ui" style="display:none">
<label style="float:none; text-align: left;">Change To:<select name="new_course" id="new_course" onchange="copy_to_hidden(this)"><option value="">Select a Course</option>
<?php 
$prefixes = $db->execute("SELECT * FROM C5CBT");
foreach ($prefixes as $p){
    echo "<option value='{$p['tablePrefix']}'>{$p['name']}</option>";
}
?>
</select></label><br /><br />
<div class="dialog-buttons" style="clear:both">
	<a href="javascript:void(0)" onclick="jQuery.fn.dialog.closeTop();" class="ccm-button-left btn btn-danger"><?php echo t('Cancel')?></a>
	<a href="javascript:void(0)" class="btn btn-success ccm-button-right" onclick="orderaction()"><?php echo t('Swap Course')?></a>
</div>
</div>

<div id="add_dialog" class="ccm-ui" style="display:none">
<label style=" float:none; text-align: left;">Add New:<select name="new_course" id="new_course" onchange="copy_to_hidden(this)"><option value="">Select a Course</option>
<?php 
$prefixes = $db->execute("SELECT * FROM C5CBT");
foreach ($prefixes as $p){
    echo "<option value='{$p['tablePrefix']}'>{$p['name']}</option>";
}
?>
</select></label><br /><br />
<div class="dialog-buttons" style="clear:both">
	<a href="javascript:void(0)" onclick="jQuery.fn.dialog.closeTop();" class="ccm-button-left btn btn-danger"><?php echo t('Cancel')?></a>
	<a href="javascript:void(0)" class="btn btn-success ccm-button-right" onclick="orderaction()"><?php echo t('Add Course')?></a>
</div>
</div>


<?php
echo  dashboard::print_pagination($url_base , $limit, $offset, $total, $filter);

echo $h->getDashboardPaneFooterWrapper(false);
	?>