<?php defined('C5_EXECUTE') or die('Access denied.');
$form = Core::make('helper/form');
/** @var Concrete\Core\Form\Service\Form $form */
?>

<form class="login-form" method="post" action="<?php echo URL::to('/login', 'authenticate', $this->getAuthenticationTypeHandle()) ?>">

  <fieldset>
    <legend>User Account</legend>

    <label for="uName"> Email Address </label>
    <div>
      <input type="text" name="uName" id="uName" class="ccm-input-text" placeholder="<?php echo Config::get('concrete.user.registration.email_registration') ? t('Email Address') : t('Username')?>" autofocus="autofocus">
    </div>

    <label for="uPassword">Password</label>

    <div>
      <input type="password" name="uPassword" id="uPassword" class="ccm-input-text" placeholder="<?php echo t('Password')?>">
    </div>

  </fieldset>

  <fieldset>

    <legend>Options</legend>

    <label for="uMaintainLogin">Remember Me</label>
    <div>
      <ul class="ul--plain">
        <li>
          <label>
            <input type="checkbox" class="ccm-input-checkbox" name="uMaintainLogin" id="uMaintainLogin" value="1"> <span>Remain logged in to website.</span></label>
        </li>
      </ul>
    </div>

    <input type="hidden" name="rcID" value="">
    <?php Core::make('helper/validation/token')->output('login_' . $this->getAuthenticationTypeHandle()); ?>
  </fieldset>

  <div class="actions">
    <button class="button--large">
      <?php echo t('Sign in') ?>
    </button>
  </div>
</form>

<h2>Forgot Your Password?</h2>
<p>Enter your email address below. We will send you instructions to reset your password.</p>
<a name="forgot_password"></a>
<div class="ccm-message">
  <?php echo isset($intro_msg) ? $intro_msg : '' ?>
</div>

<form class="login-form" method="post" action="<?php echo URL::to('/login', 'callback', $this->getAuthenticationTypeHandle(), 'forgot_password') ?>">
  <fieldset>
    <input type="hidden" name="rcID" value="" />

    <label for="uEmail">Email Address</label>

    <input type="text" name="uEmail" value="" class="ccm-input-text" placeholder="<?php echo t('Email Address') ?>">

    <div class="actions">
      <button name="resetPassword" class="button--large">
        <?php echo t('Reset and Email Password') ?>
      </button>
    </div>
  </fieldset>

</form>