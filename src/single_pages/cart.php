<?php
use Application\Controller\Training\Shopping;
?>
<section class="content">
	<div class="container">
	<div class="row">
        <div class="col-xs-12 col-sm-12 col-md-8 col-lg-8">
				<?php
				$a = new Area('Top Span');
				$a->display($c);
				$attribs = UserAttributeKey::getEditableInProfileList();
                $af = Loader::helper('form/attribute');
                $af->setAttributeObject($ui);
                foreach($attribs as $ak) {
				$form[$ak->akHandle] .= '<div class="form-fields">';
                if($ak->akHandle != "address" && $ak->akHandle != "bill_address"){
				$form[$ak->akHandle] .= $af->display($ak);
				} else {
				$form[$ak->akHandle] .= $af->display($ak, false , false);
				}
                $form[$ak->akHandle] .= '</div>';
                }
//new code below
$tablePrefixes = shopping::get_tablePrefix_array();
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
}//end new code
		//old error handling code
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
		}
		if ($test_mode){
		echo "<h1>PAYPAL BYPASSED</h1>";
		}
		if($_GET['renewal'] || ($_GET['u'] && $first_name)){
		?>
		<h2>Welcome Back <?php echo $first_name ?>!</h2>
We appreciate your business and strive to offer you the most convenient way to maintain your certification.
<br><br>
For your convenience, we have placed your ACLS recertification course in the cart below, and have prefilled your address information from your last order. Please provide your payment information to complete the registration process.
<br><br>
		<?php } ?>
<div class='ajax-errors'></div>
<form method="post" class="acls-shopping-cart" action="http://www.aclscourse.com/cart/checkout" id="checkout">
<h1>My Shopping Cart</h1>
<?php
	if ($shopping['cart']){
	foreach ($shopping['cart'] as $key=>$item){
	$input_name = "item[{$key}]";
?>
	<div class="exam-type" id="<?php echo $key ?>" <?php if($_REQUEST['faa'] || $_REQUEST['paa']){echo " style='display:none'";} ?>>
	<?php if (!$addon) { ?>
<div class="table-container">
	<table class="shopping-cart-table">
		<tr>
			<th scope="col">Qty.</th>
			<th scope="col">Course Names</th>
			<th scope="col" class="right-justify">Amount</th>
			<th scope="col" class="right-justify">Total</th>
			<th scope="col" class="right-justify">Delete</th>
		</tr>
	<tbody class="type-title">
		<tr>
			<td><input type="text" class="quantity" name="<?php echo $input_name ?>[quantity]" value="<?php echo $item['quantity'] ?>"/></td>
			<td><span class="type-name"><?php echo $exams[$item['tablePrefix']]['types'][$item['typeID']]['name'] ?> Course</span></td>
			<td class="right-justify"><span class='shopping-cart-table-cost-label'> $<span class="cost" ><?php echo $exams[$item['tablePrefix']]['types'][$item['typeID']]['cost']?></span></td>
			<td class="right-justify"><div class="price-column">$<?php echo $exams[$item['tablePrefix']]['types'][$item['typeID']]['cost']?></div></td>
			<td class="right-justify"><div class="remove"></div></td>
		</tr>
		<tr>
		<td></td>
			<td class="added" colspan="2"><ul class="added_courses"></ul></td>
		<td></td>
		<td></td>
		</tr>
	</tbody>
	</table>
</div>
	<?php  } else {
	$input_name = "item[addon]";
	foreach ($item['features'] as $featGroup => $featID){
	}
	?>
	<div class="table-container">
	<table class="shopping-cart-table">
		<tr>
			<th scope="col">Qty.</th>
			<th scope="col">Course Names</th>
			<th scope="col">Amount</th>
			<th scope="col">Total</th>
			<th scope="col">Delete</th>
		</tr>
	<tbody class="type-title">
	<input type="hidden" name="<?php echo $input_name ?>[addon]" value="<?php echo $addon ?>" />
	<?php
	echo '<input type="hidden" name="addon" value="1">';
	echo '<input type="hidden" name="orderID" value="'.$addon.'">';
	echo '<input type="hidden" name="'.$input_name.'[features]['.$featGroup.']" value="'.$featID.'">';
	echo '<input type="hidden" name="type[]" value="'.$item['tablePrefix'].'_'.$item['typeID'].'">';
	?>
		<tr>
			<td><input type="text" class="quantity" name="<?php echo $input_name ?>[quantity]" value="1"/></td>
			<td><span class="type-name">Add On BLS Card</span></td>
			<td><span class='shopping-cart-table-cost-label'> $<span class="cost" ><?php echo $exams[$item['tablePrefix']]['features'][$featID]['cost']?></span></td>
			<td><div class="price-column">$<?php echo $total_cost ?></div></td>
			<td><div class="remove"></div></td>
		</tr>
		<tr>
		<td></td>
			<td class="added" colspan="2"><ul class="added_courses"></ul></td>
		<td></td>
		<td></td>
		</tr>
	</tbody>
	</table>
</div>
	<?php } ?>
	<input type="hidden" name="<?php echo $input_name ?>[tablePrefix]" value="<?php echo $item['tablePrefix'] ?>" />
	<input type="hidden" name="<?php echo $input_name ?>[typeID]" value="<?php echo $item['typeID'] ?>" />
	<input type="hidden" class="cost" name="<?php echo $input_name ?>[cost]" value="<?php echo $exams[$item['tablePrefix']]['types'][$item['typeID']]['cost']?>" />
	<?php
	if (!$addon){
	$cme = $cme + shopping::get_cme_credits($item['tablePrefix'], $item['typeID']);
	$course_line = "Course Includes";
	if ($item['features'][2]){
	$course_line = "Each Course Includes:";
	}
	switch($item['tablePrefix']){
	case "acls":
	if ($item['typeID'] == 1){$cme = "8";$cert_recert="Certification";} else {$cme = "4";$cert_recert="Recertification";}
	?>
	<div class="exam-includes">
	<h2><?php echo $course_line ?></h2>
	<ul class="course-list">
		<li>Comprehensive Online ACLS Training (no manual needed)</li>
		<li>Online <?php echo $cert_recert ?> Exam with Instant Grading</li>
		<li>Free Unlimited Unique Practice Exams</li>
		<li>Unlimited Free Exam Retakes</li>
		<li>24/7 Instant Certification</li>
		<li>Free Instant Email Certificate After Exam Completion</li>
		<li>Free Shipping for Your ACLS Certification Hard Copy Card</li>
		<li><span id="credit-count" class="count"><?php echo $cme ?></span> Continuing Education Credits</li>
	</ul>
	</div>
	<?php
	break;
	case "pals":
	if ($item['typeID'] == 1){$cme = "8";$cert_recert="Certification";} else {$cme = "4";$cert_recert="Recertification";}
	?>
	<div class="exam-includes">
	<h2><?php echo $course_line ?></h2>
	<ul class="course-list">
		<li>Comprehensive Online PALS Training (no manual needed)</li>
		<li>Online <?php echo $cert_recert ?> Exam with Instant Grading</li>
		<li>Free Unlimited Unique Practice Exams</li>
		<li>Unlimited Free Exam Retakes</li>
		<li>24/7 Instant Certification</li>
		<li>Free Instant Email Certificate After Exam Completion</li>
		<li>Free Shipping for Your PALS Certification Hard Copy Card</li>
		<li><span id="credit-count" class="count"><?php echo $cme ?></span> Continuing Education Credits</li>
	</ul> </div>
	<?php
	break;
	case "bls":
	if ($item['typeID'] == 1){$cme = "4";$cert_recert="Certification";} else {$cme = "2";$cert_recert="Recertification";}
	?>
	<div class="exam-includes">
	<h2><?php echo $course_line ?></h2>
	<ul class="course-list">
		<li>Comprehensive Online BLS Training (no manual needed)</li>
		<li>Online <?php echo $cert_recert ?> Exam with Instant Grading</li>
		<li>Free Unlimited Unique Practice Exams</li>
		<li>Unlimited Free Exam Retakes</li>
		<li>24/7 Instant Certification</li>
		<li>Free Instant Email Certificate After Exam Completion</li>
		<li>Free Shipping for Your BLS Certification Hard Copy Card</li>
		<li><span id="credit-count" class="count"><?php echo $cme ?></span> Continuing Education Credits</li>
	</ul> </div>
	<?php
	break;
	}
	}
	?>
	<?php if (is_array($exams[$item['tablePrefix']]['shipping'])){?>
		<div class="exam-shipping">
		<h2 class="shipping-title">Shipping Options</h2>
		<div class="shipping-options">
		<?php
		$ship_first = true;
		foreach ($exams[$item['tablePrefix']]['shipping'] as $ship){
			$ship_val = $ship['shippingID'];
			$ship_name = "shipping";
			if($item[$ship_name]){
				if ($item[$ship_name] == $ship_val){
					$checked = "checked='checked'";
				} else {
					$checked = "";
				}
			} else {
				if ($ship_first){
					$checked = "checked='checked'";
				} else {
					$checked = "";
				}
			}
			?>
				<input class="exclusive" type="checkbox" id="ship_<?php echo $ship_val ?>" name="<?php echo $input_name ?>[shipping]" value="<?php echo $ship_val ?>" <?php echo $checked ?>/>
				<label for="ship_<?php echo $ship_val ?>">
				<div class="feature-name"><?php echo $ship['name'] ?> <div class="cost-label"> $<span class="cost" ><?php echo $ship['cost'] ?></span></div>
				<div class="price-column hidden">$<?php echo $ship['cost']?></div></div></label>
			<?php
			unset($ship_first);
		}?>
		</div>
		</div>
		<?php  } ?>
			<?php
			if (is_array($exams[$item['tablePrefix']]['features']) && !$addon){
			unset($featGroup);
			?>
		<div class="exam-features">
		<h2 class="feature-title"><?php echo $exams[$item['tablePrefix']]['name'] ?>Additional Courses</h2>
		<div class="feature-options">
		<?php foreach ($exams[$item['tablePrefix']]['features'] as $feat){
			$feat_val = $feat['featureID'];
			$feat_group = $feat['featureGroup'];
			//echo "feat_val=$feat_val, feat_group=$feat_group, and the item =";
			//var_dump($item['features']);
			if ($item['features'][$feat_group] == $feat_val){
				$checked = "checked='checked'";
			} else {
				$checked = "";
			}
			$featGroup[$feat_group][]= '<input class="exclusive" id = "feat_'. $feat_val.'" type="checkbox" name="'. $input_name .'[features]['. $feat_group .']" value="'. $feat_val.'" '. $checked .'/>
			<label for="feat_'. $feat_val.'"><div class="feature-name"><span class="text-alert">New! </span><span class="feat-name_span">'. $feat['name'] .' </span><span class="cost-label"> $<span class="cost" >'. $feat['cost'].'</span></span>
			<div class="price-column hidden">$'. $feat['cost'].'</div></div></label>';
			?>
			<?php }
			ksort($featGroup);
			foreach ($featGroup as $group => $feature){
			if($group != 4){
			echo "<div class='featureGroup'>";
				foreach ($feature as $input){
				echo $input;
				}
			echo "</div>";
			}
			}
			?>
		</div>
	</div>
		<div class="exam-features">
		<h2 class="feature-title">Lifetime Access</h2>
		<div class="feature-options">
		<?php foreach ($featGroup as $group => $feature){
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
		<?php  } ?>
	</div>
	<?php }
	$first = true;
	foreach ($shopping['cart'] as $key=>$item){
	$input_name = "item[{$key}]";
	if (!$addon){
		$title_bar .= '<span class="type-title"><input type="text" class="quantity" name="'. $input_name .'[quantity]" value="'. $item['quantity'] .'"/>'. $exams[$item['tablePrefix']]['types'][$item['typeID']]['name'].' Course - $<span class="cost" >' . $exams[$item['tablePrefix']]['types'][$item['typeID']]['cost'] . '</span><div class="remove"></div><div class="price-column">$' . $exams[$item['tablePrefix']]['types'][$item['typeID']]['cost'] . '</div></span>';
	} else {
		$input_name = "item[addon]";
		$hidden_inputs .= '<input type="hidden" name="addon" value="1">';
		$hidden_inputs .= '<input type="hidden" name="orderID" value="'.$addon.'">';
		$hidden_inputs .= '<input type="hidden" name="'.$item['tablePrefix'].'[features]" value="'.$_POST[$item['tablePrefix']]['features'].'">';
		$hidden_inputs .=  '<input type="hidden" name="type[]" value="'.$item['tablePrefix'].'_'.$item['typeID'].'">';
		$title_bar .= '<span class="type-title"><input type="text" class="quantity" name="'. $input_name. '[quantity]" value="1"/>Add On BLS Card <span class="cost" ></span><div class="remove"></div><div class="price-column"></div></span>';
		$hidden_inputs .='<input type="hidden" name="'. $input_name .'[addon]" value="'. $addon .'" />';
		}
		$hidden_inputs .= '<input type="hidden" name="'. $input_name .'[tablePrefix]" value="'. $item['tablePrefix'] .'" />';
		$hidden_inputs .= '<input type="hidden" name="'. $input_name .'[typeID]" value="'. $item['typeID'] .'" />';
		$hidden_inputs .= '<input type="hidden" class="cost" name="'. $input_name .'[cost]" value="'. $exams[$item['tablePrefix']]['types'][$item['typeID']]['cost'].'" />';
		$cme = $cme + shopping::get_cme_credits($item['tablePrefix'], $item['typeID']);
		if ($first){$hidden = '';} else {$hidden = 'hidden';}
		if (is_array($exams[$item['tablePrefix']]['shipping'])){
		$shipping .= '<div class="exam-shipping '. $hidden .'">
		<span class="shipping-title">Shipping Options</span>
		<div class="shippingOptions">';
		$ship_first = true;
		foreach ($exams[$item['tablePrefix']]['shipping'] as $ship){
			$ship_val = $ship['shippingID'];
			$ship_name = "shipping";
			if($item[$ship_name]){
				if ($item[$ship_name] == $ship_val){
					$checked = "checked='checked'";
				} else {
					$checked = "";
				}
			} else {
				if ($ship_first){
					$checked = "checked='checked'";
				} else {
					$checked = "";
				}
			}
			$shipping .= '<label><input class="exclusive" type="checkbox" name="' .$input_name .'[shipping]" value="'. $ship_val .'" '. $checked .'/><span class="feature-name">'. $ship['name'] .' - $<span class="cost" >'. $ship['cost'] .'</span></label><div class="price-column hidden">$'. $ship['cost'].'</div></span>';
			unset($ship_first);
		}
		$shipping .= '</div></div>';
		}
	}
	$course_includes .= '<div class="exam-includes">
	<span class="feature-title"><b>Course Includes</b></span><br/><br/>
	<ul class="course-list">
		<li>Free Online Provider Manual</li>
		<li>24/7 Instant Grading</li>
		<li>Free Shipping for Wallet Card</li>
		<li>'. $cme .' CEUs/CME credits</li>
		<li>Free Practice Examinations</li>
		<li>Unlimited Final Exam Attempts</li>
		<li>Free Instant Email PDF Card</li>
	</ul> </div>';
	}
	if($_REQUEST['discount']){
	echo "<input type='hidden' name='discount' value='{$_REQUEST['discount']}'/>";
	} ?>
<div class="total-container">
<div class="sub-total-line hidden">Subtotal: $<span class="subtotal">0.00</span></div>
<div class="discount-line hidden" style="color:red">Group Discount (<span class="discpercent"></span>% Off): <span class="savings">0.00</span></div>
<div class="total-line sidebar-headline">Total: $<span class="grand_total">0</span></div>
</div>
<!-- old code here -->
<input type="hidden" name="order_total" value="<?php echo number_format($total_cost, 2, '.', '') ?>" />
		<input type="hidden" name="cart" value='<?php echo serialize($shopping['cart'])?>' />
		<?php
		if ($total_quantity > $type_count){ ?>
		<input type="hidden" name="transfer" value="1" />
		<?php }
		if ($uID == 1){ ?>
		<fieldset class="existing-account">
  <legend>Add Course to an Existing Account</legend>
		<p>Enter the email address of an existing user below to upgrade an account.</p>
	<div class="entry-fields">
	  <div class="ccm-profile-attribute">
	  <div class="control-group">
	  <label>Customers Email Address</label>
	  <div class="controls">
	  <input type="text" name="customer_emailaddress" id="customer_emailaddress" class="form-control" value="<?php echo $_POST['customer_emailaddress'];?>">
	  </div>
		</div>
		</div>
  		<p>If updating an account DO NOT add any information below</p>
 </div><!-- end .entry-fields -->
  </fieldset>
		<?php } ?>
	<fieldset class="account-info">
  <legend>Account Login Information</legend>
	<?php if ($uID && $uID != 1){ ?>
	<div>
	Customer Name: <b><?php echo $user_name ?> </b><br/>
	Customer Email: <b><?php echo $user_email ?></b><br/><br/>
	</div>
	<?php
	if($_GET['renewal'] || ($_GET['u'] && $first_name)){
	echo "<span style='color:red'>Note: If you can't remember your old password, please just enter a new password you would like to use.</span><br/><br/>";
	}
	?>
	<div class="ccm-profile-attribute">
	<div class="control-group">
	<label>Password</label>
	<div class="controls">
	<input  style="display:inline-block;width:98%" type="password" name="change_password" id="change_password" value="<?php echo $_POST['change_password'];?>" class="form-control">
	</div>
	</div>
	</div>
	  <div class="ccm-profile-attribute">
	<div class="control-group">
	<label>Confirm Password</label>
	<div class="controls">
	<input  style="display:inline-block;width:98%" type="password" name="passwordconfirm" id="passwordconfirm" value="<?php echo $_POST['passwordconfirm'];?>" class="form-control">
	</div></div></div>
	<input type="hidden" name="uID" value="<?php echo $uID ?>" />
	<?php } else { ?>
	<p>The email address you enter below will be the username that you use to login to our site.</p>
  <div class="entry-fields">
	<div class="ccm-profile-attribute">
	<div class="control-group">
	<label>Email Address</label>
	<div class="controls">
	<input  style="display:inline-block;width:98%" type="text" name="emailaddress" id="emailaddress" class="form-control" value="<?php echo $_POST['emailaddress'];?>">
	</div>
	</div>
	</div>
	<div class="ccm-profile-attribute">
	<div class="control-group">
	<label>Confirm Email</label>
	<div class="controls">
	<input style="display:inline-block;width:98%" type="text" name="emailaddress_confirm" id="emailaddress_confirm" class="form-control" value="<?php echo $_POST['emailaddress_confirm'];?>">
	</div>
	</div>
	</div>
	<div class="ccm-profile-attribute">
	<div class="control-group">
	<label>Password</label>
	<div class="controls">
	<input style="display:inline-block;width:98%" type="password" name="change_password" id="change_password" value="<?php echo $_POST['change_password'];?>" class="form-control">
	</div>
	</div>
	</div>
	  <div class="ccm-profile-attribute">
	<div class="control-group">
	<label>Confirm Password</label>
	<div class="controls">
	<input style="display:inline-block;width:98%" type="password" name="passwordconfirm" id="passwordconfirm" value="<?php echo $_POST['passwordconfirm'];?>" class="form-control">
	</div></div></div>
</div><!-- end .entry-fields -->
  <?php } ?>
</fieldset>
<?php if ($uID && $uID != 1){ ?>
	<h2>Please Confirm your Information Below</h2>
	<?php }?>
<fieldset class="account-info">
  <legend>Shipping Address</legend>
  <p>Enter your shipping address and information here.</p>
  <div class="entry-fields">
		<?php echo $form['firstName'] ?>
		<?php echo $form['lastName'] ?>
		<?php echo $form['company'] ?>
		<?php echo $form['address'] ?>
		<?php echo $form['telephone'] ?>
</div><!-- end .entry-fields -->
</fieldset>
	<fieldset class="account-info">
	  <legend>Billing Address</legend>
	  <p>Enter your billing address and information here.</p>
	  <div class="entry-fields">
	  <label><input type="checkbox" name="same_billing" id="same_billing"
	  <?php if($_POST['same_billing']){?>
	  checked="checked"
	  <?php } ?>
	  /> Use My Shipping Information</label>
	  <div class="billing-address"
	  <?php if($_POST['same_billing']){?>
	  style = "display:none;"
	  <?php } ?>>
		<?php echo $form['bill_firstName'] ?>
		<?php echo $form['bill_lastName'] ?>
		<?php echo $form['bill_company'] ?>
		<?php echo $form['bill_address'] ?>
		<?php echo $form['bill_telephone'] ?>
  </div><!-- end .billing-address -->
</div><!-- end .entry-fields -->
</fieldset>
	  <fieldset class="card-info">
       <legend><span>Payment Method</span></legend>
       <div class="entry-fields">
      <div id="totalpreview">
        Order Total: <span class="order_pricing">$<?php echo number_format($total_cost, 2, '.', '') ?></span>
      </div><!-- end .totalpreview -->
      <div id="card_type_box">
        <div id="cards"><strong>Card Type:</strong> <select class="select-card" name="c1" autocomplete="off">
          <?php if ($uID == 1){
		  $_POST["c2"] = "123456789";
		  $_POST['c3'] = "02";
		  $_POST['c4'] = "2032";
		  $_POST['c5'] = "123";
		  ?>
		  <option value="admin">Admin Override</option>
		  <?php } ?>
		  <option value="">-- Select Credit Card --</option>
		  <option value="Visa" <?php if($_POST['c1'] == "Visa"){echo "SELECTED"; } ?>>Visa
          </option><option value="MasterCard" <?php if($_POST['c1'] == "MasterCard"){echo "SELECTED"; } ?>>MasterCard
          </option><option value="discover" <?php if($_POST['c1'] == "discover"){echo "SELECTED"; } ?>>Discover
          </option><option value="Amex" <?php if($_POST['c1'] == "Amex"){echo "SELECTED"; } ?>>American Express
        </option></select><span class="text-alert">*</span><img class="credit-cards" src="<?php  echo $this->getThemePath(); ?>/images/credit-cards.png">
    	</div><!-- end #cards -->
	</div><!-- end #card_type_box -->
 <fieldset class="card-details">
          <label>Card Number:</label>
			<div class="card-entry">
			<input type="text" name="c2" autocomplete="off" class="form-control" value="<?php echo $_POST["c2"] ?>"><span class="text-alert">*</span> <span class="no-dashes">(no dashes)</span>
			</div><!-- end .card-entry -->
			<div class="select-dates">
          <label>Expiration Date:</label>
          <select name="c3" autocomplete="off">
		  <option selected="" value="">month</option><option value="01" <?php if($_POST['c3'] == "01"){echo "SELECTED"; } ?>>01 - January </option>
		  <option value="02" <?php if($_POST['c3'] == "02"){echo "SELECTED"; } ?>>02 - February </option>
		  <option value="03" <?php if($_POST['c3'] == "03"){echo "SELECTED"; } ?>>03 - March </option>
		  <option value="04" <?php if($_POST['c3'] == "04"){echo "SELECTED"; } ?>>04 - April </option>
		  <option value="05" <?php if($_POST['c3'] == "05"){echo "SELECTED"; } ?>>05 - May </option>
		  <option value="06" <?php if($_POST['c3'] == "06"){echo "SELECTED"; } ?>>06 - June </option>
		  <option value="07" <?php if($_POST['c3'] == "07"){echo "SELECTED"; } ?>>07 - July </option>
		  <option value="08" <?php if($_POST['c3'] == "08"){echo "SELECTED"; } ?>>08 - August </option>
		  <option value="09" <?php if($_POST['c3'] == "09"){echo "SELECTED"; } ?>>09 - September </option>
		  <option value="10" <?php if($_POST['c3'] == "10"){echo "SELECTED"; } ?>>10 - October </option>
		  <option value="11" <?php if($_POST['c3'] == "11"){echo "SELECTED"; } ?>>11 - November </option>
		  <option value="12" <?php if($_POST['c3'] == "12"){echo "SELECTED"; } ?>>12 - December</option>
		  </select>
            <select name="c4"><option selected="" value="" autocomplete="off">year
                            </option>
                            <option value="2016" <?php if($_POST['c4'] == "2016"){echo "SELECTED"; } ?>>2016</option>
                            <option value="2017" <?php if($_POST['c4'] == "2017"){echo "SELECTED"; } ?>>2017</option>
                            <option value="2018" <?php if($_POST['c4'] == "2018"){echo "SELECTED"; } ?>>2018</option>
                            <option value="2019" <?php if($_POST['c4'] == "2019"){echo "SELECTED"; } ?>>2019</option>
                            <option value="2020" <?php if($_POST['c4'] == "2020"){echo "SELECTED"; } ?>>2020</option>
                            <option value="2021" <?php if($_POST['c4'] == "2021"){echo "SELECTED"; } ?>>2021</option>
                            <option value="2022" <?php if($_POST['c4'] == "2022"){echo "SELECTED"; } ?>>2022</option>
                            <option value="2023" <?php if($_POST['c4'] == "2023"){echo "SELECTED"; } ?>>2023</option>
                            <option value="2024" <?php if($_POST['c4'] == "2024"){echo "SELECTED"; } ?>>2024</option>
                            <option value="2025" <?php if($_POST['c4'] == "2025"){echo "SELECTED"; } ?>>2025</option>
                            <option value="2026" <?php if($_POST['c4'] == "2026"){echo "SELECTED"; } ?>>2026</option>
                            <option value="2027" <?php if($_POST['c4'] == "2027"){echo "SELECTED"; } ?>>2027</option>
                            <option value="2028" <?php if($_POST['c4'] == "2028"){echo "SELECTED"; } ?>>2028</option>
                            <option value="2029" <?php if($_POST['c4'] == "2029"){echo "SELECTED"; } ?>>2029</option>
                            <option value="2030" <?php if($_POST['c4'] == "2030"){echo "SELECTED"; } ?>>2030</option>
                            <option value="2031" <?php if($_POST['c4'] == "2031"){echo "SELECTED"; } ?>>2031</option>
                            <option value="2032" <?php if($_POST['c4'] == "2032"){echo "SELECTED"; } ?>>2032</option>
                        </select><span class="text-alert">*</span>
</div><!-- end .select-dates -->
          <label>CVV Code:</label>
			<div class="cvv-code">
				<input type="text" class="forminputs form-control" name="c5" autocomplete="off" value="<?php echo $_POST['c5'];?>" ><a onclick="window.open('/csv.htm','information','width=400,height=300,left=100,top=300,scrollbars=YES,menubar=NO')" href="#"> Whats this?</a></td>
			</div>
</div>
				<p><input type="submit" value="Submit Payment"  name="submit" class="btn btn-orange" /></p>
</fieldset>
<div class="bg-warning">
<strong><span class="text-alert">IMPORTANT:</span> If you are experiencing any difficulties with checkout, please send an email to <a target="_blank" href="mailto:admin@aclscourse.com">admin@aclscourse.com</a>
</strong>
</div>

</form>
</div><!-- end .col-->
<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
			<?php
			$a = new Area('Sidebar');
			$a->display($c);
			?>
		</div>

</div><!-- end .row-->
</div><!-- end .container -->
<!-- end old code -->
<div id="checkout_modal">
	<h2>Submitting Your Payment.</h2>
	<?php if ($isiPhone || $isiPad){ ?>
	<p style="font-size:8px;text-align:center"><a href="http://www.aclscertification.com/problem/">My Payment Wont Process</a></p><?php }?>
</div>
</section><!-- end section -->