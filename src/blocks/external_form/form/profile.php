<?php 
if(!$data['shipping_country']){$data['shipping_country'] = "US";}
if(!$data['bill_country']){$data['bill_country'] = "US";}

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
?>

<form method="post" action="">
<fieldset class="account-info">
	<legend>Account Login Information</legend>
    <p>Update Your Email Address:</p>
    <div class="row">
            <div class="form-group required col-xs-12 col-sm-6">
                <label>Email Address</label>
                <input type="email" class="form-control" name="emailaddress" id="emailaddress" placeholder="Email" value="<?php echo $data['emailaddress'];?>">
            </div>
    </div>
</fieldset>


<fieldset class="account-info">
  <legend>Certification Address</legend>
  <p>Enter name and physical address of certifying individual.</p>
  <div class="row">
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
    </div>
</fieldset>

<fieldset class="account-info">
	<legend>Billing Address</legend>
	<p>Enter your billing address and information here.</p>
    <div class="row">
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
    </div>
</fieldset>
    <div class="row">
            <div class="form-group required col-xs-12 col-sm-6">
                <input type="submit" class="btn btn-primary button btn-block" name="action" value="Update Details" />
            </div>
            <div class="form-group required col-xs-12 col-sm-6">
                <a class="btn btn-success btn-block" href="/member/my-profile/change-password">Change Your Password</a>
            </div>
    </div>
</form>