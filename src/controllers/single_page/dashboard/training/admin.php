<?php  
namespace Application\Controller\SinglePage\Dashboard\Training;
use \Concrete\Core\Page\Controller\DashboardPageController;
use Loader,UserInfo,Page;
use Application\Controller\Training\Dashboard;
use Application\Controller\Training\Member;
use \Concrete\Core\Attribute\Key\Category as AttributeCategory;
use \Concrete\Core\Attribute\Key\UserKey as UserKey;
use \Concrete\Core\Attribute\Type as AttributeType;
use AttributeSet;
use Core;
use Config;

class Admin extends DashboardPageController {
        
        public function view() {
			if($this->post('action')){
				switch($this->post('action')){
					case "setupUserAttributes":
					self::InstallUserAttributes();
					break;
					case "setupUserGroups":
					self::InstallUserGroups();
					break;
					case "createPages":
					self::createPages();
					break;
					case "createCourse":
					self::createCourse();
					break;
					case "importExam":
					self::importExam();
					break;
				}
			}
		}
		
	
		public function createPages(){
		}
		
		public function importExam(){
			$db = Loader::db();
			$tablePrefix = $_POST['tablePrefix'];
			$examCSV = $_POST['examCSV'];
			$csv = self::parse_csv($examCSV);
						
			array_walk($csv, function(&$a) use ($csv) {
			  $a = array_combine($csv[0], $a);
			});
			array_shift($csv); # remove column header
			
			foreach($csv as $key=>$q){
				$csv[$key]['answers'] = serialize(explode("|",$q['answers']));
			}
			
			foreach ($csv as $q){
				if($q['questionID']){//update
				$db->update("C5CBT_{$tablePrefix}_questions",$q,array('questionID'=>$q['questionID']));
				} else {//addnew
				$db->insert("C5CBT_{$tablePrefix}_questions",$q);
				}
			}
		}
		
		function parse_csv ($csv_string, $delimiter = ",", $skip_empty_lines = true, $trim_fields = true)
		{
			return array_map(
				function ($line) use ($delimiter, $trim_fields) {
					return array_map(
						function ($field) {
							return str_replace('!!Q!!', '"', utf8_decode(urldecode($field)));
						},
						$trim_fields ? array_map('trim', explode($delimiter, $line)) : explode($delimiter, $line)
					);
				},
				preg_split(
					$skip_empty_lines ? ($trim_fields ? '/( *\R)+/s' : '/\R+/s') : '/\R/s',
					preg_replace_callback(
						'/"(.*?)"/s',
						function ($field) {
							return urlencode(utf8_encode($field[1]));
						},
						$enc = preg_replace('/(?<!")""/', '!!Q!!', $csv_string)
					)
				)
			);
		}

		
		
		public function createCourse(){
			$db = Loader::db();
			if ($_POST['courseName']){ // create new course
			
				
				$tp = strtolower($_POST['tabPref']);
				$db = Loader::db();
				if($_POST['seedCourse']){
				$original = \Page::getByID($_POST['seedCourse']);
				} else {
				$original = \Page::getByPath('/member/certification-name');
				}
				
				$parent = \Page::getByPath('/member');
				$newPage = $original->duplicateAll($parent);
				$data['cName'] = $_POST['courseName'];
				$txt = Core::make('helper/text');
				$data['cHandle'] = $txt->urlify($data['cName']);
				$data['cHandle'] = str_replace('-', Config::get('concrete.seo.page_path_separator'), $data['cHandle']);
				$newPage->update($data);
				$newPath = $newPage->generatePagePath();
				$newPage->setCanonicalPagePath($newPath);
				$newChildren = $newPage->getCollectionChildren();
				foreach($newChildren as $newChild){
					$newChild->setAttribute('tablePrefix', $tp);
					$newChild->rescanCollectionPath();
				}
				

				$createTables[] = "CREATE TABLE C5CBT_{$tp}_completions LIKE C5CBT_cert_completions";
				$createTables[] = "CREATE TABLE C5CBT_{$tp}_features LIKE C5CBT_cert_features";
				$createTables[] = "INSERT INTO C5CBT_{$tp}_features SELECT * FROM C5CBT_cert_features";
				$createTables[] = "CREATE TABLE C5CBT_{$tp}_orders LIKE C5CBT_cert_orders";
				$createTables[] = "CREATE TABLE C5CBT_{$tp}_questions LIKE C5CBT_cert_questions";
				$createTables[] = "CREATE TABLE C5CBT_{$tp}_shippingOptions LIKE C5CBT_cert_shippingOptions";
				$createTables[] = "INSERT INTO C5CBT_{$tp}_shippingOptions SELECT * FROM C5CBT_cert_shippingOptions";
				$createTables[] = "CREATE TABLE C5CBT_{$tp}_types LIKE C5CBT_cert_types";
				$createTables[] = "CREATE TABLE C5CBT_{$tp}_userExams LIKE C5CBT_cert_userExams";
				foreach($createTables as $sql){
					$db->execute($sql);
				}
				$insert['name'] = $_POST['courseName'];
				$insert['tablePrefix'] = $tp;
				$insert['expireMonths'] = 24;
				$insert['fontSize'] = 16;
				$insert['cardTitle'] = $_POST['cardTitle'];
				$db->insert("C5CBT",$insert);
			}
		}
		
		public function InstallUserGroups(){
			
			$groups = array(
			'Customers' => 'Users who have purchased a course.',
			'Transfer' => 'Users who administrate other users.',
			'groupon' => 'Users who purchased via Groupon.',
            'lifetime' => 'Users who purchased Lifetime option.',
			'acls' => 'Users who purchased ACLS Course.',
			'acls_alumni' => 'Users who passed ACLS Course.',	
            'bls' => 'Users who purchased BLS Course.',
			'bls_alumni' => 'Users who passed BLS Course.',	
            'pals' => 'Users who purchased PALS Course.',
			'pals_alumni' => 'Users who passed PALS Course.',	
            'nrp' => 'Users who purchased NRP Course.',
			'nrp_alumni' => 'Users who passed NRP Course.',	            
			);
			
			foreach($groups as $name=>$description){
				$group = \Concrete\Core\User\Group\Group::getByName($name);
				if (!is_object($group)) {
					$newgroup = \Group::add($name, $description);
				}
			}	
		}
		
		Public Function InstallUserAttributes(){
			$userCat = AttributeCategory::getByHandle('user');
					$userCat->setAllowAttributeSets(AttributeCategory::ASET_ALLOW_MULTIPLE);
					$set = AttributeSet::getByHandle('customer_details');
					if (!is_object($set)) {
						$set = $userCat->addSet('customer_details',t('Customer Details')); 
					}
					$attributes = array(
					'firstName'=>array('type'=>'text','name'=>'First Name'),'lastName'=>array('type'=>'text','name'=>'Last Name'),
					'company'=>array('type'=>'text','name'=>'Company'),
					'address'=>array('type'=>'address','name'=>'Address'),
					'telephone'=>array('type'=>'text','name'=>'Telephone'),
					'ownerUid'=>array('type'=>'text','name'=>'Owners User ID'),
					'bill_firstName'=>array('type'=>'text','name'=>'Billing First Name'),
					'bill_lastName'=>array('type'=>'text','name'=>'Billing Last Name'),
					'bill_company'=>array('type'=>'text','name'=>'Billing Company'),
					'bill_address'=>array('type'=>'address','name'=>'Billing Address'),
					'bill_telephone'=>array('type'=>'text','name'=>'Billing Telephone')
					);
					
					foreach($attributes as $key=>$data){
						$at = AttributeType::getByHandle($data['type']);
						$existing = UserKey::getByHandle($key);
						if (!is_object($existing)) {
						$atData = array(
							'akHandle' => $key,
							'akName' => t($data['name']),
							'akIsSearchable' => true,
							'akDefaultCountry' => 'US'
						);
						UserKey::add($at,$atData)->setAttributeSet($set);
					}
					}
		}
        
}