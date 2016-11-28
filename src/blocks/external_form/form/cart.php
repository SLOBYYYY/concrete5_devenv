<?php
use Application\Controller\Training\Shopping;
if(!$data['shipping_country']){$data['shipping_country'] = "US";}
if(!$data['bill_country']){$data['bill_country'] = "US";}

$isiPad = (bool) strpos($_SERVER['HTTP_USER_AGENT'],'iPad'); //detects ios device for password field manipulation
$isiPhone = (bool) strpos($_SERVER['HTTP_USER_AGENT'],'iPhone'); //detects ios device for password field manipulation

// Start code to load course data into multi-dimensional array for display
$tablePrefixes = shopping::get_tablePrefix_array(); //get all courses loaded on the website
foreach ($tablePrefixes as $tablePrefix){
	$course_type = shopping::get_types($tablePrefix);
	foreach ($course_type as $type){
		$exams[$tablePrefix]['types'][$type['typeID']] = $type;
	}
	$features = shopping::get_features($tablePrefix);
	foreach ($features as $feature){
		$exams[$tablePrefix]['features'][$feature['featureID']] = $feature;
	}
	$shippingOptions = shopping::get_shippingOptions($tablePrefix);
	foreach ($shippingOptions as $shipping){
		$exams[$tablePrefix]['shipping'][$shipping['shippingID']] = $shipping;
	}
}
// End code that loads course data into multi-dimensional array for display

?>
<div class="row">
<div class="col-xs-12">
<?php


// Add Frame Here Rendered content begins below

		if (isset($error) && $error->has()) {
            $error->output();
        } else if (isset($message)) { ?>
            <div class="message"><?php echo $message?></div>
            <script type="text/javascript">
            $(function() {
                $("div.message").show('highlight', {}, 500);
            });
            </script>
        <?php
		} else if(isset($alert)){ ?>
			<div class="message" style="background-color:red"><?php echo $alert ?></div>
		<?php }
		
if ($test_mode){echo "<h1>PAYPAL BYPASSED</h1>";} // alert if paypal is not setup
if($_GET['renewal'] || ($_GET['u'] && $first_name)){ //display welcome back if page being displayed after clickin on reminder email
?>
<div class="alert alert-info">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
    <h2>Welcome Back <?php echo $first_name ?>!</h2>
    <p>We appreciate your business and strive to offer you the most convenient way to maintain your certification.</p>
    <p>For your convenience, we have placed your recertification course in the cart below, and have prefilled your address information from your last order. Please provide your payment information to complete the registration process.</p>
</div>
<?php } ?>

<div class='ajax-errors'></div>

<form method="post" action="" id="checkout">

<?php
if($_REQUEST['discount']){ // Loads and displays discount
	$db = Loader::db();
	$dname = $db->getOne("SELECT discountName FROM C5CBT_discounts WHERE discountCode LIKE ? AND displayName = 1",array($_REQUEST['discount']));
	if($dname){
		echo "<h2>{$dname} Discount</h2>";
	}
	echo "<input type='hidden' name='discount' value='{$_REQUEST['discount']}'/>";
}


if ($shopping['cart']){ //user already has items in cart. Show them.
	foreach ($shopping['cart'] as $key=>$item){ //iterate over the items in the shopping cart
	$alreadyInCart[] = $item['tablePrefix'];
	$input_name = "item[{$key}]";
	if(!is_numeric($item['quantity'])){
		$item['quantity'] = 1;
	}
?>
	<div class="exam-type" id="<?php echo $key ?>">

<div class="shopping-cart-table-container">
	<table class="shopping-cart-table">
		<tr>
			<th scope="col" style="width:5%;">Qty.</th>
			<th scope="col" style="width:60%;">Course Names</th>
			<th scope="col"  style="width:15%;" class="right-justify">Amount</th>
			<th scope="col"  style="width:15%;" class="right-justify">Total</th>
			<th scope="col"  style="width:10%;" class="right-justify">Delete</th>
		</tr>
	<tbody class="type-title">
		<tr>
			<td><input type="text" class="quantity" name="<?php echo $input_name ?>[quantity]" value="<?php echo $item['quantity'] ?>"/></td>
			<td><span class="type-name"><?php echo $exams[$item['tablePrefix']]['types'][$item['typeID']]['name'] ?> Course</span></td>
			<td class="right-justify"><span class='shopping-cart-table-cost-label'> $<span class="cost" ><?php echo $exams[$item['tablePrefix']]['types'][$item['typeID']]['cost']?></span></span></td>
			<td class="right-justify"><div class="price-column">$<?php echo $exams[$item['tablePrefix']]['types'][$item['typeID']]['cost']?></div></td>
			<td class="right-justify"><div class="remove"></div></td>
		</tr>
		<tr>
		<td></td>
			<td class="added" colspan="3"><ul class="added_courses"></ul></td>
		<td></td>
		</tr>
	</tbody>
	</table>
</div>
	
	<input type="hidden" name="<?php echo $input_name ?>[tablePrefix]" value="<?php echo $item['tablePrefix'] ?>" />
	<input type="hidden" name="<?php echo $input_name ?>[typeID]" value="<?php echo $item['typeID'] ?>" />
	<input type="hidden" class="cost" name="<?php echo $input_name ?>[cost]" value="<?php echo $exams[$item['tablePrefix']]['types'][$item['typeID']]['cost']?>" />

	<?php
	$cme = $cme + shopping::get_cme_credits($item['tablePrefix'], $item['typeID']);
	//set text for display above features depending on whether there is an addon
	$course_line = "Course Includes";
	if ($item['features'][2]){ 
	$course_line = "Each Course Includes:";
	}



	if ($item['typeID'] == 1){$cme = "8";$cert_recert="Certification";} else {$cme = "4";$cert_recert="Recertification";}?>
	<div class="exam-includes">
	<h2><?php echo $course_line ?></h2>
	<ul class="cta-interior__list--red">
        <li>Comprehensive Online Study Guide Included</li>
        <li>Online <?php echo $cert_recert; ?> Exam with Instant Grading</li>
        <li>Free Unlimited Unique Practice Exams</li>
        <li>Unlimited Free Exam Retakes</li>
        <li>24/7 Instant <?php echo $cert_recert; ?></li>
        <li>Free Instant Email Certificate After Exam Completion</li>
<!--        <li>Free Shipping for Your --><?php //echo $exams[$item['tablePrefix']]['types'][$item['typeID']]['name'] ?><!-- Hard Copy Card</li>-->
        <li><span id="credit-count" class="count"><?php echo $cme; ?></span> Continuing Education Credits</li>
        
        </ul>
	</div>




	<?php if (is_array($exams[$item['tablePrefix']]['shipping'])){ // show shipping items for items that have shipping options ?>
		<div class="exam-shipping" style="display:none">
		<h2 class="shipping-title">Shipping Options</h2>
		<div class="shipping-options">
		<?php
		$ship_first = true;
		foreach ($exams[$item['tablePrefix']]['shipping'] as $ship){ //itterate and display shipping options
			$ship_val = $ship['shippingID'];
			$ship_name = "shipping";
			if($item[$ship_name]){ //if the shopping cart holds shipping info for this line item check it against iterating shipment list
				if ($item[$ship_name] == $ship_val){
					$checked = "checked='checked'";
				} else {
					$checked = "";
				}
			} else { //if there is no shipping data attached to this line item simply check the first option (free shipping)
				if ($ship_first){
					$checked = "checked='checked'";
				} else {
					$checked = "";
				}
			}
			//render the shipping option
			?>

<input class="exclusive" type="checkbox" id="ship_<?php echo $ship_val ?>" name="<?php echo $input_name ?>[shipping]" value="<?php echo $ship_val ?>" <?php echo $checked ?>/>
<label for="ship_<?php echo $ship_val ?>"><div class="feature-name"><?php echo $ship['name'] ?> <div class="cost-label"> $<span class="cost" ><?php echo $ship['cost'] ?></span></div>
<div class="price-column hidden">$<?php echo $ship['cost']?></div></div></label>

<?php	unset($ship_first);
		} // end shipping list iteration ?>
		
		</div>
		</div>
		<?php  } //end rendering shipping items for items that have shipping options
		
		if (is_array($exams[$item['tablePrefix']]['features']) && !$addon){ //if the course we are currently iterating over has features and is not an addon show the features
		unset($featGroup);?>
		
		<?php   //end the conditional show features section ?>
			<div class="exam-features">
		
		
		<?php foreach ($exams[$item['tablePrefix']]['features'] as $feat){ // iterate over all feature items available for current course
			$feat_val = $feat['featureID'];
			$feat_group = $feat['featureGroup'];

			if ($item['features'][$feat_group] == $feat_val){ // check to see if the cart has this feature in it
				$checked = "checked='checked'"; //check the box if it does
			} else {
				$checked = "";
			}
			
			$featGroup[$feat_group][]= '<input class="exclusive" id = "feat_'. $feat_val.'" type="checkbox" name="'. $input_name .'[features]['. $feat_group .']" value="'. $feat_val.'" '. $checked .'/>
			<label for="feat_'. $feat_val.'"><div class="feature-name"><span style="padding-left:.5em; color:#cc0000">New! </span><span class="feat-name_span">'. $feat['name'] .' </span><span class="cost-label"> $<span class="cost" >'. $feat['cost'].'</span></span>
			<div class="price-column hidden">$'. $feat['cost'].'</div></div></label>';
			} //end feature iteration
			
			ksort($featGroup); //sort the feature groups according to key so they are in the correct order for display
			foreach ($featGroup as $group => $feature){ //iterate over the display feature array
				if($group != 4){ //render all feature groups except "4" which should be rendered separately below this will need to be manually edited
					echo "<div class='featureGroup'>";
					foreach ($feature as $input){echo $input;}
					echo "</div>";
				}
			} ?>
		
	
		<h2 class="feature-title">Lifetime Access</h2>
		<div class="feature-options">
		<?php foreach ($featGroup as $group => $feature){ // now display feature "4" which is the special lifetime access feature group
			if($group == 4){
			echo "<div class='featureGroup'>";
				foreach ($feature as $input){
				echo $input;
				}
			echo "</div>";
			}
			} ?>
		</div>
		</div>
		
        

	<?php } // end iterating over the contents of the shopping cart
    echo "</div>";
	} //end the conditional statement requiring items in shopping cart
	}//display the total box
	
?>

<div class="total-container">
<div class="sub-total-line hidden">Subtotal: $<span class="subtotal">0.00</span></div>
<?php if($grouponSavings){ ?>
<div class="groupon-line hidden" style="color:#cc0000"><?php echo $dname ?> Groupon Savings: <span class="grouponSavings"><?php echo number_format($grouponSavings, 2, '.', '') ?></span></div>
<?php } ?>
<div class="discount-line hidden" style="color:#cc0000"><?php echo $dname ?> Discount (<span class="discpercent"></span>% Off): <span class="savings">0.00</span></div>




<div class="total-line sidebar-headline">Total: $<span class="grand_total">0</span></div>
</div>
<?php if($grouponSavings){ ?>
<input type="hidden" name="grouponSavings" value="<?php echo number_format($grouponSavings, 2, '.', '') ?>" />
<input type="hidden" name="groupon" value="<?php echo $groupon ?>" />
<?php } ?>
<input type="hidden" name="order_total" value="<?php echo number_format($total_cost, 2, '.', '') ?>" />
<input type="hidden" name="cart" value='<?php echo serialize($shopping['cart'])?>' />

<?php	if ($total_quantity > $type_count){ //set the transfer flag to true if the user is ordering more than one of a particular course ?>
<input type="hidden" name="transfer" value="1" />
<?php } ?>

<h2 class="feature-title">Other Courses You May Be Interested In</h2>
<div class="other-courses-container">
<div class="more-courses-head">
	<span class='more-name-head'>Course</span>
	<span class='more-cert-head'><span style="width:50%;display:inline-block">Certification</span><span style="width:50%;display:inline-block">Recertification</span></span>
	
</div>
<div class="other-courses-scroll">
<?php
$db = Loader::db();
$allCourses = $db->getAll("SELECT * FROM C5CBT ORDER BY courseID ASC");

if($_REQUEST['discount']){
		$discountCode = "%".$_REQUEST['discount']."%";
	
	$discount = $db->getRow("SELECT * FROM C5CBT_discounts WHERE discountCode Like ? AND remainingUses > 0",(array)$discountCode);
	if($discount){
		if(!$discount['percent1'] && $discount['discountPercentage'] > 0){
			$discount['percent1'] = $discount['discountPercentage'];
		}
	}
	}
	
foreach ($allCourses as $ac){
	if(!in_array($ac['tablePrefix'],$alreadyInCart)){
	
	$certInfo = $db->getRow("SELECT * from C5CBT_{$ac['tablePrefix']}_types WHERE typeID = 1");
	$recertInfo = $db->getRow("SELECT * from C5CBT_{$ac['tablePrefix']}_types WHERE typeID = 2");
	
	if($discount){
	$certInfo['cost']= number_format(($certInfo['cost'] * (1 - $discount['percent1'])),2);
	$recertInfo['cost']= number_format(($recertInfo['cost'] * (1 - $discount['percent1'])),2);
	} 
	
	echo "<div class='more-courses'>
	<span class='more-name'>{$ac['name']}</span>
		<div class='cert-titles'>
<span class='cert-title'>Certification</span><span class='cert-title'>Recertification</span>
</div>
	<span class='more-cert'><a href='#' class='button add add-cert' id='{$ac['tablePrefix']}_1'>+ $".$certInfo['cost']."</a>
    <a href='#' class='button add add-cert' id='{$ac['tablePrefix']}_2'>+ $".$recertInfo['cost']."</a>
    </span>

	</div>";
	}
} ?>
</div>
</div>

<script>
$( document ).ready(function() {
    $('.add-cert').click(function(){
		event.preventDefault();
		var value = $(this).attr("id");
		$('<input>').attr({
		type: 'hidden',
		value: value,
		name: 'add-course'
		}).appendTo('form#checkout');
		
		$("input[name='submit']").click();
		
	});
});
</script>
<?php 


if ($uID == 1){ //add special toolset for admin ?>
<fieldset class="existing-account">
	<legend>Add Course to an Existing Account</legend>
	<p>Enter the email address of an existing user below to upgrade an account.</p>
    <div class="form-group">
        <label>Customers Email address</label>
        <input type="email" class="form-control" name="customer_emailaddress" id="customer_emailaddress" placeholder="Email" value="<?php echo $_POST['customer_emailaddress'];?>">
    </div>
    <p>If updating an account DO NOT add any information below</p>
</fieldset>
<?php } //end special toolset for admin ?>

<fieldset class="account-info">
	<legend>Account Login Information</legend>
<?php if ($uID && $uID != 1){ //if the user is logged in display their current info?>
	<div>
	Customer Name: <b><?php echo $user_name ?> </b><br/>
	Customer Email: <b><?php echo $user_email ?></b><br/><br/>
	</div>
	<?php
	if($_GET['renewal'] || ($_GET['u'] && $first_name)){ //if page is being displayed due to reminder email click show password notice
	echo "<span style='color:red'>Note: If you can't remember your old password, please just enter a new password you would like to use.</span><br/><br/>";
	}?>
	
    
    <div class="form-group">
        <label>Password</label>
        <input type="<?php if($isiPad || $isiPhone){echo "text"; } else {echo "password";}?>" class="form-control" name="change_password" id="change_password" value="<?php echo $_POST['change_password'];?>">
    </div>
    
    <div class="form-group">
        <label>Confirm Password</label>
        <input type="<?php if($isiPad || $isiPhone){echo "text"; } else {echo "password";}?>" class="form-control" name="passwordconfirm" id="passwordconfirm" value="<?php echo $_POST['passwordconfirm'];?>">
    </div>
	<input type="hidden" name="uID" value="<?php echo $uID ?>" />
	<?php } else { //end special username password for logged in users?>

	<p>The email address you enter below will be the username that you use to login to our site.</p>

    <div class="row">
            <div class="form-group required col-xs-12 col-sm-6">
                <label>Email Address</label>
                <input type="email" class="form-control" name="emailaddress" id="emailaddress" placeholder="Email" value="<?php echo $_POST['emailaddress'];?>">
            </div>
            
            <div class="form-group required col-xs-12 col-sm-6">
                <label>Confirm Email</label>
                <input type="email" class="form-control" name="emailaddress_confirm" id="emailaddress_confirm" placeholder="Email Again" value="<?php echo $_POST['emailaddress_confirm'];?>">
            </div>

            <div class="form-group required col-xs-12 col-sm-6">
                <label>Password</label>
                <input type="<?php if($isiPad || $isiPhone){echo "text"; } else {echo "password";}?>" class="form-control" name="change_password" id="change_password" value="<?php echo $_POST['change_password'];?>">
            </div>
        
            <div class="form-group required col-xs-12 col-sm-6">
                <label>Confirm Password</label>
                <input type="<?php if($isiPad || $isiPhone){echo "text"; } else {echo "password";}?>" class="form-control" name="passwordconfirm" id="passwordconfirm" value="<?php echo $_POST['passwordconfirm'];?>">
            </div>
    </div>
<?php } //end username and password fields for new customers?>
</fieldset>

<?php if ($uID && $uID != 1){ //display notice if user logged in?>
	<h2>Please Confirm your Information Below</h2>
<?php }?>
<div class="row">
<fieldset class="account-info">
  <legend>Certification Address</legend>
  <p>Enter name and physical address of certifying individual.</p>
        <div class="form-group required col-xs-12 col-sm-6"><label>First Name</label><input type="text" class="form-control" name="firstName" id="firstName" placeholder="First Name" value="<?= $data['firstName']; ?>"></div>
        <div class="form-group required col-xs-12 col-sm-6"><label>Last Name</label><input type="text" class="form-control" name="lastName" id="lastName" placeholder="Last Name" value="<?= $data['lastName']; ?>"></div>
        <div class="form-group col-xs-12"><label>Company</label><input type="text" class="form-control" name="company" id="company" placeholder="Company" value="<?= $data['company']; ?>"></div>
        <div class="form-group required col-xs-12"><label>Address 1</label><input type="text" class="form-control" name="shipping_address1" id="shipping_address1" placeholder="Address 1" value="<?= $data['shipping_address1']; ?>"></div>
        <div class="form-group col-xs-12"><label>Address 2</label><input type="text" class="form-control" name="shipping_address2" id="shipping_address2" placeholder="Address 2" value="<?= $data['shipping_address2']; ?>"></div>
        <div class="form-group required col-xs-12 col-sm-4"><label>City</label><input type="text" class="form-control" name="shipping_city" id="shipping_city" placeholder="City" value="<?= $data['shipping_city']; ?>"></div>
        <div class="form-group required col-xs-12 col-sm-4"><label>State/Province</label><select name="shipping_state" id="shipping_state" class="form-control" data-blank-option="Choose State/Province" data-default-value="<?php echo $data['shipping_state']; ?>"></select></div>
        <div class="form-group required col-xs-12 col-sm-4"><label>Country</label><select name="shipping_country" id="shipping_country" class="crs-country form-control" data-region-id="shipping_state" data-value="2-char" data-default-value="<?php echo $data['shipping_country']; ?>"></select></div>
        <div class="form-group required col-xs-12 col-sm-6"><label>Postal Code</label><input type="text" class="form-control" name="shipping_zip" id="shipping_zip" placeholder="Postal Code" value="<?= $data['shipping_zip']; ?>"></div>
        <div class="form-group col-xs-12 col-sm-6"><label>Telephone</label><input type="text" class="form-control" name="telephone" id="telephone" placeholder="Telephone" value="<?= $data['telephone']; ?>"></div>
</fieldset>

<fieldset class="account-info">
	<legend>Billing Address</legend>
	<p>Enter your billing address and information here.</p>
        <div class="form-group required col-xs-12 col-sm-6"><label>First Name</label><input type="text" class="form-control" name="bill_firstName" id="bill_firstName" placeholder="First Name" value="<?= $data['bill_firstName']; ?>"></div>
        <div class="form-group required col-xs-12 col-sm-6"><label>Last Name</label><input type="text" class="form-control" name="bill_lastName" id="bill_lastName" placeholder="Last Name" value="<?= $data['bill_lastName']; ?>"></div>
        <div class="form-group col-xs-12"><label>Company</label><input type="text" class="form-control" name="bill_company" id="bill_company" placeholder="Company" value="<?= $data['bill_company']; ?>"></div>
        <div class="form-group required col-xs-12"><label>Address 1</label><input type="text" class="form-control" name="bill_address1" id="bill_address1" placeholder="Address 1" value="<?= $data['bill_address1']; ?>"></div>
        <div class="form-group col-xs-12"><label>Address 2</label><input type="text" class="form-control" name="bill_address2" id="bill_address2" placeholder="Address 2" value="<?= $data['bill_address2']; ?>"></div>
        <div class="form-group required col-xs-12 col-sm-4"><label>City</label><input type="text" class="form-control" name="bill_city" id="bill_city" placeholder="City" value="<?= $data['bill_city']; ?>"></div>
        <div class="form-group required col-xs-12 col-sm-4"><label>State/Province</label><select name="bill_state" id="bill_state" class="form-control" data-blank-option="Choose State/Province" data-default-value="<?php echo $data['bill_state']; ?>"></select></div>
        <div class="form-group required col-xs-12 col-sm-4"><label>Country</label><select name="bill_country" id="bill_country" class="crs-country form-control" data-region-id="bill_state" data-value="2-char" data-default-value="<?php echo $data['bill_country']; ?>"></select></div>
        <div class="form-group required col-xs-12 col-sm-6"><label>Postal Code</label><input type="text" class="form-control" name="bill_zip" id="bill_zip" placeholder="Postal Code" value="<?= $data['bill_zip']; ?>"></div>
        <div class="form-group col-xs-12 col-sm-6"><label>Telephone</label><input type="text" class="form-control" name="bill_telephone" id="bill_telephone" placeholder="Telephone" value="<?= $data['bill_telephone']; ?>"></div>
</fieldset>
</div>
<div class="row">
<fieldset class="col-xs-12 col-sm-3 col-sm-push-9">
    <legend>Order Total</legend>
    <div id="totalpreview">
			<span class="order_pricing">$<?php echo number_format($total_cost, 2, '.', '') ?></span>
		</div><!-- end .totalpreview -->
</fieldset>
<fieldset class="col-xs-12 col-sm-9 col-sm-pull-3 card-info">
	<legend>Payment Method</legend>
            <?php if($grouponSavings){ ?>
            <div class="groupon-payment">Groupon Applied: <span class="grpCode"><?php echo $groupon ?></span></div>
            <?php } ?>
    <div class="row">
    <div class="form-group required col-xs-12 col-sm-7">
                <label>Card Type: <img class="credit-cards" src="/application/css/images/credit-cards.png"></label>
                <select id="card_type" class="form-control select-card" name="cc_type" autocomplete="off">
				<?php if ($uID == 1){ // add in special default info for admin
				  $_POST["cc_number"] = "123456789";
				  $_POST['cc_month'] = "02";
				  $_POST['cc_year'] = "2032";
				  $_POST['cc_cvv'] = "123";
				  ?>
					<option value="admin">Admin Override</option>
				  <?php } ?>
					<option value="">-- Select Credit Card --</option>
					<option value="Visa" <?php if($_POST['cc_type'] == "Visa"){echo "SELECTED"; } ?>>Visa</option>
					<option value="MasterCard" <?php if($_POST['cc_type'] == "MasterCard"){echo "SELECTED"; } ?>>MasterCard</option>
					<option value="discover" <?php if($_POST['cc_type'] == "discover"){echo "SELECTED"; } ?>>Discover</option>
					<option value="Amex" <?php if($_POST['cc_type'] == "Amex"){echo "SELECTED"; } ?>>American Express</option>
				</select>
            </div>
    <div class="form-group required col-xs-12 col-sm-5">
        <label>Card Number</label>
        <input type="text" class="form-control" name="cc_number" id="cc_number" placeholder="Credit Card Number" value="<?php echo $data['cc_number'];?>">
    </div>
    </div>
    <div class="row">
    <div class="form-group required col-xs-12 col-sm-4">
        <label>Expiration Date</label>
       <select class="form-control select-month" id="id_cc_exp_month" name="cc_month" autocomplete="off">
				<option selected="" value="">month</option><option value="01" <?php if($_POST['cc_month'] == "01"){echo "SELECTED"; } ?>>01 - January </option>
				<option value="02" <?php if($_POST['cc_month'] == "02"){echo "SELECTED"; } ?>>02 - February </option>
				<option value="03" <?php if($_POST['cc_month'] == "03"){echo "SELECTED"; } ?>>03 - March </option>
				<option value="04" <?php if($_POST['cc_month'] == "04"){echo "SELECTED"; } ?>>04 - April </option>
				<option value="05" <?php if($_POST['cc_month'] == "05"){echo "SELECTED"; } ?>>05 - May </option>
				<option value="06" <?php if($_POST['cc_month'] == "06"){echo "SELECTED"; } ?>>06 - June </option>
				<option value="07" <?php if($_POST['cc_month'] == "07"){echo "SELECTED"; } ?>>07 - July </option>
				<option value="08" <?php if($_POST['cc_month'] == "08"){echo "SELECTED"; } ?>>08 - August </option>
				<option value="09" <?php if($_POST['cc_month'] == "09"){echo "SELECTED"; } ?>>09 - September </option>
				<option value="10" <?php if($_POST['cc_month'] == "10"){echo "SELECTED"; } ?>>10 - October </option>
				<option value="11" <?php if($_POST['cc_month'] == "11"){echo "SELECTED"; } ?>>11 - November </option>
				<option value="12" <?php if($_POST['cc_month'] == "12"){echo "SELECTED"; } ?>>12 - December</option>
			</select>
    </div>
    
    <div class="form-group col-xs-12 col-sm-4">
        <label>Year</label>
            <select class="form-control select-year" name="cc_year" id="id_cc_exp_year"><option selected="" value="" autocomplete="off">year</option>
				<option value="2016" <?php if($_POST['cc_year'] == "2016"){echo "SELECTED"; } ?>>2016</option>
				<option value="2017" <?php if($_POST['cc_year'] == "2017"){echo "SELECTED"; } ?>>2017</option>
				<option value="2018" <?php if($_POST['cc_year'] == "2018"){echo "SELECTED"; } ?>>2018</option>
				<option value="2019" <?php if($_POST['cc_year'] == "2019"){echo "SELECTED"; } ?>>2019</option>
				<option value="2020" <?php if($_POST['cc_year'] == "2020"){echo "SELECTED"; } ?>>2020</option>
				<option value="2021" <?php if($_POST['cc_year'] == "2021"){echo "SELECTED"; } ?>>2021</option>
				<option value="2022" <?php if($_POST['cc_year'] == "2022"){echo "SELECTED"; } ?>>2022</option>
				<option value="2023" <?php if($_POST['cc_year'] == "2023"){echo "SELECTED"; } ?>>2023</option>
				<option value="2024" <?php if($_POST['cc_year'] == "2024"){echo "SELECTED"; } ?>>2024</option>
				<option value="2025" <?php if($_POST['cc_year'] == "2025"){echo "SELECTED"; } ?>>2025</option>
				<option value="2026" <?php if($_POST['cc_year'] == "2026"){echo "SELECTED"; } ?>>2026</option>
				<option value="2027" <?php if($_POST['cc_year'] == "2027"){echo "SELECTED"; } ?>>2027</option>
				<option value="2028" <?php if($_POST['cc_year'] == "2028"){echo "SELECTED"; } ?>>2028</option>
				<option value="2029" <?php if($_POST['cc_year'] == "2029"){echo "SELECTED"; } ?>>2029</option>
				<option value="2030" <?php if($_POST['cc_year'] == "2030"){echo "SELECTED"; } ?>>2030</option>
				<option value="2031" <?php if($_POST['cc_year'] == "2031"){echo "SELECTED"; } ?>>2031</option>
				<option value="2032" <?php if($_POST['cc_year'] == "2032"){echo "SELECTED"; } ?>>2032</option>
				<option value="2033" <?php if($_POST['cc_year'] == "2033"){echo "SELECTED"; } ?>>2033</option>
				<option value="2034" <?php if($_POST['cc_year'] == "2034"){echo "SELECTED"; } ?>>2034</option>
				<option value="2035" <?php if($_POST['cc_year'] == "2035"){echo "SELECTED"; } ?>>2035</option>
			</select>
    </div>
    
    <div class="form-group required col-xs-12 col-sm-4">
        <label>CVV Code <a onclick="window.open('/csv.htm','information','width=400,height=300,left=100,top=300,scrollbars=YES,menubar=NO')" href="#"> Whats this?</a></label>
        <input type="text" class="form-control" name="cc_cvv" id="cc_cvv" placeholder="CVV" value="<?php echo $data['cc_cvv'];?>">
    </div>
   </div>
<input type="submit" value="Submit Payment"  name="submit" class="button button--large" />
</div>


<div class="message-important">
<strong><span style="color:#cc0000">IMPORTANT:</span> If you are experiencing any difficulties with checkout, please call us at <?php  echo Config::get('app.business_phone');?> for assistance or send an email to: <a target="_blank" href="mailto:<?php echo C5CBT_BUSINESS_EMAIL;?>"><?php echo C5CBT_BUSINESS_EMAIL;?></a></strong>
</div>
</form>

<div id="checkout_modal" class="box">
	<h2>Submitting Your Payment.</h2>
</div>

</div><!--End Col Wrap -->
</div><!--End Row Wrap -->