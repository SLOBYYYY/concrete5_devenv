<?php
use Concrete\Core\User\User;
use Concrete\Core\User\UserInfo;
defined('C5_EXECUTE') or die("Access Denied.");
$u = new User();
$ui = UserInfo::getByID($u->getUserID());
$firstName = $ui->getAttribute('firstName');
echo "Welcome $firstName,";