<?php 
namespace Application\Controller\Training;
use Application\Controller\Training\Fpdf;
use Controller;
use User;
use UserInfo;
class Documents extends Controller {

	public static $organization_name='ACLS Certification Institute';
	public static $organization_id = "32633";
	public static $address = NULL;
	public static $address1 = NULL;
	public static $address2 = NULL;
	public static $phone = NULL;
	public static $website = "http://www.aclscourse.com";
	public static $instructor = "Jaimison Baker, MD";
	public static $instructor_id = "0101242651";
	public static $provider_id = "0846248429";
	
public function get_receipt($paymentID, $uID){
		$db=\Database::connection();
		$vals[]=$paymentID;
		$vals[]=$uID;
		$receipt_data = $db->getRow("SELECT * FROM C5CBT_payments WHERE paymentID = ? AND uID = ?",$vals);
		$paydate = date('m/d/Y',strtotime($receipt_data['timestamp']));
		$paypal = unserialize($receipt_data['paypalInfo']);
		$actual_cost = urldecode($paypal['AMT']);
		Log::addEntry("Actual Cost is ${$actual_cost}",'receipt');
		if (!$actual_cost){
			$actual_cost = $receipt_data['total'];
		}		
		$ui = UserInfo::getByID($uID);
		$todays_date = date("F jS, Y");
		$first_name = ucfirst(iconv('UTF-8', 'windows-1252', $ui->getAttribute('firstName')));
		$last_name =ucfirst( iconv('UTF-8', 'windows-1252', $ui->getAttribute('lastName')));
		$full_name = $first_name . " " . $last_name;

		$cert_name = strtoupper($tablePrefix);
		$address = $ui->getAttribute('address');
		$street_address = iconv('UTF-8', 'windows-1252', $address->address1);
		$street_address2 = iconv('UTF-8', 'windows-1252', $address->address2);
		$city = ucfirst(iconv('UTF-8', 'windows-1252', $address->city));
		$state = iconv('UTF-8', 'windows-1252', $address->state_province);
		$zipcode = iconv('UTF-8', 'windows-1252', $address->postal_code);
		$country = iconv('UTF-8', 'windows-1252', $address->getFullCountry());
		
		
		
		$pdf = new FPDF('P','in','Letter');
		$pdf->AddPage();
		$pdf->SetMargins(1,1);
		$pdf->SetFont('Arial','',10);
		$pdf->SetY(2.5);
		$pdf->Image($_SERVER['DOCUMENT_ROOT'] . DIR_REL .'/packages/C5CBT/images/big_logo.png',1,.7,2);
		$pdf->SetY(1);
		$pdf->SetFont('Arial','B',16);
		$pdf->Cell(0,0,"Customer Receipt",0,0,'R');
		$pdf->Ln(.3);
		$pdf->SetFont('Arial','',10);
		$pdf->Cell(0,0,self::$organization_name,0,0,'R');
		$pdf->Ln(.15);
		$pdf->Cell(0,0,self::$address1,0,0,'R');
		$pdf->Ln(.15);
		$pdf->Cell(0,0,self::$address2,0,0,'R');
		$pdf->Ln(.15);
		$pdf->Cell(0,0,self::$phone,0,0,'R');
		$pdf->Ln(.15);
		
		$pdf->SetY(2.5);
		$pdf->Cell(0,0,"Payment Date: {$paydate}",0,0,'L');
		$pdf->Ln(.25);
		$pdf->SetFont('Arial','B',10);
		$pdf->Cell(0,0,"Customer Information:",0,0,'L');
		$pdf->SetFont('Arial','',10);
		$pdf->Ln(.15);
		$pdf->Cell(0,0,$full_name ,0,0,'L');
		$pdf->Ln(.15);
		$pdf->Cell(0,0,$street_address ,0,0,'L');
		$pdf->Ln(.15);
		if ($street_address2){
		$pdf->Cell(0,0,$street_address2 ,0,0,'L');
		$pdf->Ln(.15);
		}
		$pdf->Cell(0,0,$city . ", " . $state . " " . $zipcode ,0,0,'L');
		$pdf->Ln(.15);
		//only print country line if not usa
		if ($country != "United States"){
			$pdf->Cell(0,0,$country ,0,0,'L');
					$pdf->Ln(.25);
		}
		
		$pdf->SetFont('Arial','B',10);
		$pdf->SetY(3.7);
		$pdf->Cell(0,0,"Payment Details" ,0,0,'L');
		$pdf->Line(1,3.9,7.5,3.9);
		$pdf->SetY(4);
		$pdf->SetX(1);
		$pdf->Cell(0,0,"Quantity",0,0,'L');
		$pdf->SetX(2);
		$pdf->Cell(0,0,"Course",0,0,'L');
		$pdf->SetX(7);
		$pdf->Cell(0,0,"Cost",0,0,'R');
		$pdf->Line(1,4.1,7.5,4.1);
		$pdf->SetY(4.3);
		$cart = unserialize($receipt_data['details']);
		if($cart){
		foreach ($cart as $linenum=>$course){
		unset($featname);
		unset($shipping_name);
			$course_name = $db->getOne("SELECT name from C5CBT_{$course['tablePrefix']}_types WHERE typeID = {$course['typeID']}");
			$shipping_name = $db->getOne("SELECT name from C5CBT_{$course['tablePrefix']}_shippingOptions WHERE shippingID = {$course['shipping']}");
			$shipping_cost = $db->getOne("SELECT cost from C5CBT_{$course['tablePrefix']}_shippingOptions WHERE shippingID = {$course['shipping']}");
			if($course['features']){
				foreach($course['features'] as $featID){
				if($linenum != "addon"){
				
				$featname[] = $db->getRow("SELECT name, cost from C5CBT_{$course['tablePrefix']}_features WHERE featureID = $featID");
				} else {
				$addon = true;
				$course_name = $db->getOne("SELECT name from C5CBT_{$course['tablePrefix']}_features WHERE featureID = $featID");
				}
				}
			}
			$pdf->SetFont('Arial','',10);
			$pdf->SetX(1);
			$pdf->Cell(0,0,$course['quantity'],0,0,'L');
			$pdf->SetX(2);
			$pdf->SetFont('Arial','B',10);
			$pdf->Cell(0,0,$course_name,0,0,'L');
			$pdf->SetFont('Arial','',10);
			$pdf->SetX(7);
			$total_cost = $total_cost + $course['cost'];
			if($linenum != "addon"){
			$cost = "$" . number_format($course['cost'],2,'.','');
			} else {
			$cost = "$" . number_format($actual_cost,2,'.','');
			}
			$pdf->Cell(0,0,$cost,0,0,'R');
			$pdf->Ln(.15);
			$pdf->SetX(2);
			$pdf->Cell(0,0,$shipping_name,0,0,'L');
			if($shipping_cost > 0){
			$pdf->SetX(7);
				$total_cost = $total_cost +$shipping_cost;
				$scost = "$" . number_format($shipping_cost,2,'.','');
				$pdf->Cell(0,0,$scost,0,0,'R');
				}
			if($featname){
				
				foreach($featname as $feat){
				$pdf->Ln(.15);
				$pdf->SetX(2);
				$pdf->Cell(0,0,$feat['name'],0,0,'L');
				$pdf->SetX(7);
				$total_cost = $total_cost +$feat['cost'];
				$fcost = "$" . number_format($feat['cost'],2,'.','');
				$pdf->Cell(0,0,$fcost,0,0,'R');
				}
			}
			$pdf->Ln(.3);
			$pdf->SetX(1);
		}
		} else {
		return "A Downloadable PDF Receipt is not available for this order";
		}
		
		if($actual_cost){
		$discount = $total_cost - $actual_cost;
		} else {
		$actual_cost = $total_cost;
		}
		
		if($discount > 0 && !$addon){
		$discount = "-$" . number_format($discount,2,'.','');
		$subtotal = "$" . number_format($total_cost,2,'.','');
		$pdf->SetX(6);
		$pdf->Cell(0,0,"Sub-Total:",0,0,'L');
		$pdf->SetX(7);
		$pdf->Cell(0,0,$subtotal ,0,0,'R');
		$pdf->Ln(.15);
		$pdf->SetX(6);
		$pdf->Cell(0,0,"Discount:",0,0,'L');
		$pdf->SetX(7);
		$pdf->Cell(0,0,$discount ,0,0,'R');
		}
		$actual_cost = "$" . number_format($actual_cost,2,'.','');
		
		$pdf->Ln(.15);
		$pdf->SetX(6);
		$pdf->SetFont('Arial','B',10);
		$pdf->Cell(0,0,"Total:",0,0,'L');
		$pdf->SetX(7);
		$pdf->Cell(0,0,$actual_cost ,0,0,'R');
		$pdf->Ln(.15);
		$pdf->SetFont('Arial','',10);
		
		//var_dump($cart);
		
		// write footer
		$pdf->SetY(-1.5);
		$pdf->SetFont('Arial','B',8);
		$pdf->Cell(0,0,self::$organization_name,0,0,'C');
		$pdf->Ln(.125);
		$pdf->SetFont('Arial','',8);
		$pdf->Cell(0,0,self::$address,0,0,'C');
		$pdf->Ln(.125);
		$pdf->SetFont('Arial','',8);
		$pdf->Cell(0,0,self::$phone,0,0,'C');
		$pdf->Ln(.125);
		// Then put a blue underlined link
		$pdf->SetTextColor(0,0,255);
		$pdf->Cell(0,0,self::$website,0,0,'C');
		
		return $pdf->Output("receipt{$paymentID}.pdf", "D");

	}
	
	
	public function get_document($orderID,$tablePrefix,$docType, $uID = NULL){
		if (!$uID){
			$u = New User;
			$uID = $u->getUserID();
		}
		
		$db = \Database::connection();
		$table = "C5CBT_".$tablePrefix."_orders";
		$order_info = $db->getRow("SELECT * from $table WHERE orderID = $orderID and uID = $uID");
		
		if ($order_info){ //Order seems legit
			if ($order_info['completionID']) { // order is valid and closed
				
				if ($docType == "p"){
					documents::print_cert_card($uID, $tablePrefix, $order_info);
				}
				
				if ($docType == "b"){
					if (stristr($order_info['features'],'BLS') ){
						documents::print_cert_card($uID, $tablePrefix, $order_info, TRUE);
					} else {
						echo "You have provided incorrect credentials";
					}
				}
				
				if ($docType == "l"){
					documents::print_welcome_letter($uID, $tablePrefix);
				}
				
				if ($docType == "c"){
					documents::print_ceh_certificate($uID, $tablePrefix, $order_info);
				}
				
				if ($docType == "cb"){
					if (stristr($order_info['features'],'BLS') ){
					documents::print_ceh_certificate($uID, $tablePrefix, $order_info, TRUE);
					} else {
						echo "You have provided incorrect credentials";
					}
				}
				
				
				
				
			} else { // order is valid, but not complete
				echo "Your documents will not be available until you pass the final exam.";
			}
		} else { // No matching Order
			echo "You have provided incorrect credentials";
		}
		
		
	}
	public function get_expiration_date($cert_date, $tablePrefix, $format = "m/d/Y"){
		
		$db = \Database::connection();
		$expireMonths = $db->getOne("SELECT expireMonths FROM C5CBT WHERE tablePrefix LIKE '$tablePrefix'");
		$expiration_date = date($format, strtotime($cert_date ." +{$expireMonths} months" ) );
		return $expiration_date;
	}
	
	public function get_credits($exam_type, $tablePrefix){
		$db = \Database::connection();
		$table = "C5CBT_{$tablePrefix}_types";
		$credits = $db->getOne("SELECT credits FROM $table WHERE typeID = $exam_type");
		$string = documents::spell($credits);
		return "$string ({$credits})";
	}
	
	public function spell($number) {
    if ($number >= 0 && $number < 13 && (is_int($number) || ctype_digit($number))) {
        $numbers = array("zero", "one", "two", "three", "four", "five", "six", "seven", "eight", "nine", "ten", "eleven", "twelve");
        return $numbers[$number];
    } else return $number;
	}
	
	public function get_exam_date($completionID, $tablePrefix, $format = "m/d/Y"){
		$db = \Database::connection();
		$table = "C5CBT_{$tablePrefix}_completions";
		$examID = $db->getOne("SELECT examID FROM $table WHERE completionID = $completionID");
		if ($examID){
			$table = "C5CBT_{$tablePrefix}_userExams";
			$timestamp = $db->getOne("SELECT timestamp FROM $table WHERE examID = $examID");
			return date($format, strtotime($timestamp));
		} else {
			return "unable to get exam date";
		}
	}
	
	public function print_welcome_letter($uID, $tablePrefix){
	
		$ui = UserInfo::getByID($uID);
		$todays_date = date("F jS, Y");
		$first_name = ucfirst(iconv('UTF-8', 'windows-1252', $ui->getAttribute('firstName')));
		$last_name =ucfirst( iconv('UTF-8', 'windows-1252', $ui->getAttribute('lastName')));
		$full_name = $first_name . " " . $last_name;

		$cert_name = strtoupper($tablePrefix);
		$address = $ui->getAttribute('address');
		$street_address = iconv('UTF-8', 'windows-1252', $address->address1);
		$street_address2 = iconv('UTF-8', 'windows-1252', $address->address2);
		$city = ucfirst(iconv('UTF-8', 'windows-1252', $address->city));
		$state = iconv('UTF-8', 'windows-1252', $address->state_province);
		$zipcode = iconv('UTF-8', 'windows-1252', $address->postal_code);
		$country = iconv('UTF-8', 'windows-1252', $address->getFullCountry());
		
		
		
		
		$letter_body = "Dear $first_name,
		\n

Thank you for choosing the " . self::$organization_name . " for your ACLS, PALS or BLS training needs. We hope that our course met all of your needs and expectations, and that it made your certification or renewal requirement as convenient as possible for you.\n

Your continuing education certificate has been included in this envelope along with your hard copy ACLS, PALS or BLS card.\n

If you passed the ACLS or PALS examination and would like to receive a BLS card, please give us a call at " . self::$phone . " to add a BLS card to your order for 15% off the normal price.\n

If you need a replacement ACLS PALS or BLS card you may also call us at " . self::$phone . " to request a replacement. One replacement is provided for free, and any additional replacement cards cost $14.99 per card.\n

Again, we appreciate you choosing the " . self::$organization_name . " for your training needs, and we look forward to seeing you again in 2 years.\n\n

Sincerely,\n
";
		
		
		$pdf = new FPDF('P','in','Letter');
		$pdf->AddPage();
		$pdf->SetMargins(1,1);
		$pdf->SetFont('Arial','',10);
		$pdf->Image($_SERVER['DOCUMENT_ROOT'] . DIR_REL .'/packages/C5CBT/images/big_logo.png',2.75,.7,3);
		$pdf->SetY(2.5);
		$pdf->Cell(0,0,$todays_date,0,0,'R');
		$pdf->Ln(.3);
		$pdf->Cell(0,0,$full_name ,0,0,'L');
		$pdf->Ln(.15);
		$pdf->Cell(0,0,$street_address ,0,0,'L');
		$pdf->Ln(.15);
		if ($street_address2){
		$pdf->Cell(0,0,$street_address2 ,0,0,'L');
		$pdf->Ln(.15);
		}
		$pdf->Cell(0,0,$city . ", " . $state . " " . $zipcode ,0,0,'L');
		$pdf->Ln(.15);
		//only print country line if not usa
		if ($country != "United States"){
			$pdf->Cell(0,0,$country ,0,0,'L');
		}
		
		$pdf->Ln(.5);
		$pdf->Write(.15, $letter_body);
		$pdf->SetFont('Arial','B',10);
		$pdf->Ln(.15);
		$pdf->Cell(0,0,self::$organization_name ,0,0,'L');
		
		
		// write footer
		$pdf->SetY(-1.5);
		$pdf->SetFont('Arial','B',8);
		$pdf->Cell(0,0,self::$organization_name,0,0,'C');
		$pdf->Ln(.125);
		$pdf->SetFont('Arial','',8);
		$pdf->Cell(0,0,self::$address,0,0,'C');
		$pdf->Ln(.125);
		$pdf->SetFont('Arial','',8);
		$pdf->Cell(0,0,self::$phone,0,0,'C');
		$pdf->Ln(.125);
		// Then put a blue underlined link
		$pdf->SetTextColor(0,0,255);
		$pdf->Cell(0,0,self::$website,0,0,'C');
		
		return $pdf->Output("thank_you.pdf", "D");
		
		
	}
	
	public function print_cert_card($uID, $tablePrefix, $order_info, $bls_override = FALSE){
		
		$ui = UserInfo::getByID($uID);
		$todays_date = date("F jS, Y");
		$first_name = ucfirst($ui->getAttribute('firstName'));
		$last_name = ucfirst($ui->getAttribute('lastName'));
		$address = $ui->getAttribute('address');
		$street_address = iconv('UTF-8', 'windows-1252', $address->address1);
		$city = ucfirst(iconv('UTF-8', 'windows-1252', $address->city));
		$state = iconv('UTF-8', 'windows-1252', $address->state_province);
		$zipcode = iconv('UTF-8', 'windows-1252', $address->postal_code);
		$tc_info = "$city, $state $zipcode";
		
		
		$full_name = $first_name . " " . $last_name;
		$coded_name = iconv('UTF-8', 'windows-1252', $full_name);
		$exam_date = documents::get_exam_date($order_info['completionID'], $tablePrefix);
		$expiration_date =  documents::get_expiration_date($exam_date, $tablePrefix);
		if ($bls_override){
			$tablePrefix = "bls";
		}
		
		$cert_name = strtoupper($tablePrefix);
		
		
		
		
		$pdf = new FPDF('P','mm','Letter');
		$pdf->AddPage();
		$pdf->SetMargins(71,110, 73);
		$pdf->SetFont('Arial','B',7);
		$pdf->Image($_SERVER['DOCUMENT_ROOT'] . DIR_REL .'/packages/C5CBT/images/card_front_'.$tablePrefix.'.png',60,110,94);
		$pdf->SetY(144);
		$pdf->SetFont('Arial','B',12);
		$pdf->Cell(0,0,$coded_name,0,0,'C');
		$pdf->SetY(156);
		$pdf->SetFont('Arial','B',8);
		$pdf->Cell(36,0,"$exam_date",0,0,'C');
		$pdf->Cell(0,0,"$expiration_date",0,0,'C');
		$pdf->Ln(9);

		
		
		$pdf->AddPage();
		$pdf->SetMargins(71,110, 73);
		$pdf->Image($_SERVER['DOCUMENT_ROOT'] . DIR_REL . '/packages/C5CBT/images/card_back_'.$tablePrefix.'.png',60,110,94);
		$pdf->SetY(129);
		$pdf->SetX(83);
		$pdf->SetFont('Arial','',6);
		$pdf->Cell(40,0,self::$organization_name,0,0);
		$pdf->Cell(0,0,self::$organization_id,0,0);
		$pdf->Ln(9);
		$pdf->SetX(83);
		$pdf->Cell(40,0,"$tc_info",0,0);
		$pdf->Cell(0,0,self::$provider_id,0,0);
		$pdf->Ln(8.5);
		$pdf->SetX(83);
		$pdf->Cell(40,0,self::$instructor,0,0);
		$pdf->Cell(0,0,self::$instructor_id,0,0);
		
		
		
		return $pdf->Output("certification_card.pdf", "D");
		
	}
	
	public function print_ceh_certificate($uID, $tablePrefix, $order_info, $bls=false){
		Loader::Model('member', 'C5CBT');
		$ui = UserInfo::getByID($uID);
		$exam_date = documents::get_exam_date($order_info['completionID'], $tablePrefix, "jS \d\a\y \of F, Y");
		$first_name = ucfirst(iconv('UTF-8', 'windows-1252', $ui->getAttribute('firstName')));
		$last_name = ucfirst(iconv('UTF-8', 'windows-1252', $ui->getAttribute('lastName')));
		$full_name = $first_name . " " . $last_name;
		$expiration_date = documents::get_expiration_date($exam_date, $tablePrefix);
		
	if ($bls){
		if (stristr($order_info['features'],'BLS Recertification')){
		$order_info['typeID'] = 2;
		$tablePrefix = "bls";
		}
		
		if (stristr($order_info['features'],'BLS Certification')){
		$order_info['typeID'] = 1;
		$tablePrefix = "bls";
		}
	}
		
		
		$test_name = member::get_type_name($order_info['typeID'], $tablePrefix);
		
		$credits = documents::get_credits($order_info['typeID'], $tablePrefix);
		$small_text = "The person who is listed on this certificate has completed the cognitive examination administered by the " . self::$organization_name . " which is based on the latest AHA and ECC guidelines. This $test_name Course is approved to provide Continuing Education Credit by the National Board for Emergency Continuing Medical Education. The Board awards $credits CEH Advanced Credits for the completion of the $test_name course administered by the " . self::$organization_name . ".";
		
		
		$pdf = new FPDF('P','in','Letter');
		$pdf->AddPage();
		$pdf->SetMargins(1,1);
		$pdf->SetFont('Times','B',22);
		$pdf->Image($_SERVER['DOCUMENT_ROOT'] . DIR_REL .'/packages/C5CBT/images/big_logo.png',3,.7,2.5);
		$pdf->SetY(2.5);
		$pdf->Cell(0,0,'Certificate of Course Completion',0,0,'C');
		$pdf->Ln(.35);
		$pdf->SetFont('Arial','',10);
		$pdf->Cell(0,0,'This is to certify that',0,0,'C');
		$pdf->Ln(.35);
		$pdf->SetFont('Arial','BU',18);
		$pdf->Cell(0,0,$full_name,0,0,'C');
		$pdf->Ln(.35);
		$pdf->SetFont('Arial','',10);
		$pdf->Cell(0,0,'Has completed the course',0,0,'C');
		$pdf->Ln(.35);
		$pdf->SetFont('Arial','B',14);
		$pdf->Cell(0,0,$test_name,0,0,'C');
		$pdf->Ln(.5);
		$pdf->SetFont('Arial','',12);
		$pdf->Cell(0,0,"On this $exam_date",0,0,'C');
		$pdf->Ln(.4);
		$pdf->SetFont('Arial','',12);
		$pdf->Cell(0,0,"Administered by the ". self::$organization_name,0,0,'C');
		$pdf->Ln(.4);
		$pdf->SetFont('Arial','',12);
		$pdf->SetTextColor(43,43,149);
		$pdf->Cell(0,0,self::$website,0,0,'C');
		$pdf->SetTextColor(0,0,0);
		$pdf->Ln(.4);
		$pdf->SetFont('Arial','BU',12);
		$pdf->Cell(0,0,$full_name,0,0,'C');
		$pdf->Ln(.25);
		$pdf->SetFont('Arial','',12);
		$pdf->Cell(0,0,"Is awarded $credits CEH Advanced Credits for completing this course by the",0,0,'C');
		$pdf->Ln(.4);
		$pdf->SetFont('Arial','B',14);
		$pdf->Cell(0,0,"National Board for Emergency Continuing Medical Education",0,0,'C');
		$pdf->Ln(.4);
		$pdf->SetFont('Arial','',12);
		$pdf->Cell(0,0,"Virginia Commonwealth University",0,0,'C');
		$pdf->Ln(.2);
		$pdf->Cell(0,0,"Department of Anesthesiology",0,0,'C');
		$pdf->Ln(.2);
		$pdf->Cell(0,0,"1250 East Marshall Street",0,0,'C');
		$pdf->Ln(.2);
		$pdf->Cell(0,0,"Richmond, VA 23298",0,0,'C');
		$pdf->Ln(.4);
		$pdf->SetFont('Arial','',8);
		$pdf->Write(.15, $small_text);
		$pdf->SetFont('Arial','',10);
		$pdf->Image($_SERVER['DOCUMENT_ROOT'] .DIR_REL . '/packages/C5CBT/images/sig-melissa.jpg',1.5,8.5,1.25);
		$pdf->SetY(8.9);
		$pdf->SetX(1.5);
		$pdf->Cell(2,.3,"Melissa Milan, M.D., M.S.",0,1,'L');
		$pdf->SetX(1.5);
		$pdf->Cell(2,0,"Licensed Physician",0,0,'L');
		$pdf->Image($_SERVER['DOCUMENT_ROOT'] .DIR_REL . '/packages/C5CBT/images/sig-jaimison.jpg',5.5,8.5,1.25);
		$pdf->SetY(8.9);
		$pdf->SetX(5.5);
		$pdf->Cell(2,0,"Jaimison Baker, M.D.",0,0,'L');
		$pdf->Ln(.15);
		$pdf->SetX(5.5);
		$pdf->Cell(2,0,"Licensed Physician",0,0,'L');
		$pdf->Ln(.15);
		$pdf->SetX(5.5);
		$pdf->Cell(2,0,"Board-eligible Anesthesiologist",0,0,'L');
		return $pdf->Output("CEH_certificate.pdf", "D");
	}
	
	
	
}