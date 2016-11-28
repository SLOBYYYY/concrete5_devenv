<?php 
namespace Application\Controller\SinglePage;
use Application\Controller\Training\General;
use Concrete\Core\Page\Controller\PageController;
class Date extends PageController {
	
	public function view(){
		$year = General::printYear();
		echo "The year is $year";
	}
	
}