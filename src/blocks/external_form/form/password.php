<?php 
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
<fieldset>
    <div class="row">
            <div class="form-group required col-xs-12 col-sm-6">
                <label>Password</label>
                <input type="password" class="form-control" name="change_password" id="change_password" value="">
            </div>
        
            <div class="form-group required col-xs-12 col-sm-6">
                <label>Confirm Password</label>
                <input type="password" class="form-control" name="passwordconfirm" id="passwordconfirm" value="">
            </div>
    </div>
</fieldset>

    <div class="row">
            <div class="form-group required col-xs-12 col-sm-6">
                <input type="submit" class="btn btn-primary button btn-block" name="action" value="Change Password" />
            </div>
            <div class="form-group required col-xs-12 col-sm-6">
                <a class="btn btn-success btn-block" href="/member/my-profile">Back To Account Details</a>
            </div>
    </div>
    


</form>