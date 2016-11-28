<?php
namespace Application\Controller\Training;

use Config;
use Controller;
use Exception;
use Database;
use Core;
use User;
use UserInfo;
use View;

class General extends Controller
{
	public function printYear(){
		return "2016";
	}
}
