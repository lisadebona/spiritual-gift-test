<?php
require '../inc/functions.php'; 
require '../inc/dompdf/autoload.inc.php';
require '../inc/PHPMailer/src/Exception.php';
require '../inc/PHPMailer/src/PHPMailer.php';
require '../inc/PHPMailer/src/SMTP.php';

global $dompdf, $config, $mail, $data;
use Dompdf\Dompdf;
$dompdf = new DOMPDF();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;
$mail = new PHPMailer;

// $absolute_path = realpath("submit.php");
// echo $absolute_path;

$data = new SpiritualGifts();
$info = $data->records();
$config = $data->config();
$site_url = $config['site_url'];


/* Member Login Submission */
if( isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strcasecmp($_SERVER['HTTP_X_REQUESTED_WITH'], 'xmlhttprequest') == 0 ) {
	if( isset($_POST['memberlogin']) && $_POST['memberlogin'] ) {
		session_start();
		global $data;
		$config = $data->config();
		$site_url = $config['site_url'];
		$form_data = ( isset($_POST) ) ? $_POST : '';
		$required_fields = array('fullname','phone','email');
		$response['ok'] = '';
		$response['redirect'] = $site_url;
		$response['errors'] = '';
		$errors = array();
		foreach($required_fields as $field) {
			$val = ( isset($form_data[$field]) && $form_data[$field] ) ? $form_data[$field] : '';
			$val = preg_replace('/\s+/', '', $val);
			if(empty($val)) {
				$errors[] = $field;
			}
		}

		if($errors) {
			$response['errors'] = $errors;
		} else {
			$formResult = $data->submit_member($form_data);
			$response['ok'] = true;
		}
		echo json_encode($response);
		die();
	}
}

/* Spiritual Gift Test Submission */
if( isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strcasecmp($_SERVER['HTTP_X_REQUESTED_WITH'], 'xmlhttprequest') == 0 ) {
	if( isset($_POST['testform']) && $_POST['testform'] ) {
		$formData = ( isset($_POST) ) ? $_POST : '';
		$categories = ( isset($info['categories']) && $info['categories'] ) ? $info['categories']:'';
		$questions = ( isset($info['questions']) && $info['questions'] ) ? $info['questions']:'';
		$groupedBy = array();
		$results = array();
		$highest = 4;
		$is_saved = false;
		$user_answers = array();
		global $dompdf;

		$member_name = ( isset($formData['fullname']) && $formData['fullname'] ) ? $formData['fullname'] : '';
		$member_phone = ( isset($formData['phone']) && $formData['phone'] ) ? $formData['phone'] : '';
		$member_email = ( isset($formData['email']) && $formData['email'] ) ? $formData['email'] : '';
		$result_page = ( isset($formData['result']) && $formData['result'] ) ? $formData['result'] : '';
		$test_started = ( isset($formData['spiritual_gift_test_started']) && $formData['spiritual_gift_test_started'] ) ? $formData['spiritual_gift_test_started'] : '';
		$top_three_definitions = array();

		$response['result'] = '';
		$response['message'] = 'An error occurred. Please try again.';
		$response['memberinfo'] = '';
		$response['result_page'] = '';
		$response['answers'] = '';
		$response['completedURL'] = $site_url.'?completed=1';

		if($categories && $questions) {

			foreach($categories as $type=>$a) {
				$arrs_questions = $a['questions'];
				$count = ($a['questions']) ? count($a['questions']) : 0;
				$perfect_score = $count * $highest;
				$categories[$type]['perfect_score'] = $perfect_score;
			}

			foreach($questions as $id => $q) {
				$field = 'score_for_question_' . $id;
				$score = ( isset($formData[$field][0]) && $formData[$field][0] ) ? $formData[$field][0] : 0;
				foreach($categories as $type=>$a) {
					$arrs_questions = $a['questions'];
					if( in_array($id,$arrs_questions) ) {
						$groupedBy[$type][$id] = $score;
						$user_answers[$type][] = array('id'=>$id,'question'=>$q,'score'=>$score);
					}
				}
			}

			$response['answers'] = $user_answers;

			if($groupedBy) {
				foreach($groupedBy as $k=>$scores) {
					$total = array_sum($scores);
					$results[$k] = $total;
				}
			}

			

			
			arsort($results); /* Highest to Lowest */
			//asort($results); /* Lowest to Highest */

			if($results) {
				$m=1; foreach($results as $cat=>$totalScore) {
					if( $m<=3 ) {
						$definition = $categories[$cat]['definition'];
						$catName = $categories[$cat]['name'];
						$top_three_definitions[$cat] = array('name'=>$catName,'definition'=>$definition);
					}
					$m++;
				}
			}

	 		$member_data = array(
	 			'member_name'=>$member_name,
	 			'member_phone'=>$member_phone,
	 			'member_email'=>$member_email,
	 			'answers'=>$user_answers,
	 			'test_started'=>$test_started,
	 			'test_result'=>$results,
	 			'top_three'=>$top_three_definitions
	 		);

	 		$memberDataJson = json_encode($member_data, JSON_PRETTY_PRINT);

	 		$mName = preg_replace('/\s+/', '', $member_name);
	 		$mPhone = preg_replace('/\s+/', '', $member_phone);
	 		$mPhone = str_replace('-', '', $mPhone);
	 		$file = $mName.'_'.$mPhone.'.json';

	 		$fileName = '../data/members/' . $file;

	 		/* Save Data into a File */
			$memberFile = fopen($fileName, "w") or die("Unable to open file!");
			$txt = $memberDataJson;
			$r = fwrite($memberFile, $txt);
			fclose($memberFile);
			if( file_exists($fileName) ) {
				$file_title = str_replace('@','_at_',$member_email);
				$file_title = sanitize_title($file_title);
				$pdfFileName = 'resultfor_' . $file_title . '.pdf';

				$is_saved = true;
				$info['pdf_filename'] = $pdfFileName;
				$response['result'] = $is_sent;
				$response['memberinfo'] = $member_data;
				$response['result_page'] = $result_page;

				$is_sent = create_email($member_data,$info);
				if($is_sent) {
					$response['result'] = $is_sent;
					$response['memberinfo'] = $member_data;
					$response['result_page'] = $result_page;
					//unlink($fileName);
					$pdf_file_path = '../_temp/'.$pdfFileName;
					unlink($pdf_file_path);
				}

				//header("Location: ".$site_url.'result.php');
			}
		}
		
		echo json_encode($response);
		die();
	}
}

function create_email($data,$info) {
	global $config;
	$sender_info = $info;
	$member_name = $data['member_name'];
	$member_phone = $data['member_phone'];
	$member_email = $data['member_email'];
	$answers = $data['answers'];
	$test_started = $data['test_started'];
	$test_result = $data['test_result'];
	$top_three_result = $data['top_three'];

	$pdf_filename = ( isset($info['pdf_filename']) && $info['pdf_filename'] ) ? $info['pdf_filename']:'';
	$highest = 4;
	$file_source = null;
	$email_content = '';
	$pdf_content = '';
	ob_start();
	include('../templates/email_template.php');
	$email_content = ob_get_contents();
	ob_end_clean();

	ob_start();
	include('../templates/pdf_template.php');
	$pdf_content = ob_get_contents();
	ob_end_clean();

	// $replacement = './logo.png';
	// $pattern = "http://sermonguide.idahograce.com/wp-content/uploads/2020/06/idaho-grace-1.png";
	// $pdf_content = str_replace($pattern, $replacement, $email_content);

	$dompdf = new DOMPDF();

	/* Create PDF */
	if($pdf_content && $pdf_filename) {
		// $file_title = str_replace('@','_at_',$member_email);
		// $file_title = sanitize_title($file_title);
		// $fileName = 'resultfor_' . $file_title . '.pdf';
		$fileName = $pdf_filename;
		$dompdf->load_html($pdf_content);
		$dompdf->setPaper('A4', 'Portrait');
		$dompdf->render();
		$output = $dompdf->output();
		$file_source = '../_temp/'.$fileName;
	  $ok = file_put_contents($file_source, $output);
	}

  $sent = send_email_action($email_content,$member_email,$file_source);
  
	return $sent;
}

function send_email_action($email_content,$toEmail,$attachment) {
	global $mail, $config;
	$smtp = $config['smtp'];
	$transport_type = ( isset($smtp['smtp_transport']) && $smtp['smtp_transport'] ) ? $smtp['smtp_transport'] : PHPMailer::ENCRYPTION_STARTTLS;
	$from_email = $smtp['from_email'];
	$from_name = $smtp['from_name'];
	$cc_email = $smtp['cc_email'];
	$subject = $config['email_subject'];
	$is_email_sent = false;

	// $email_content = '<h1>Email body text...</h1>';
	// $toEmail = 'lisaqdebona@gmail.com';
	// $attachment = '../_temp/result-test-101.pdf';

	$mail->isSMTP();
	//$mail->SMTPDebug = SMTP::DEBUG_SERVER;
	$mail->SMTPDebug = false;
	$mail->Host = $smtp['smtp_host'];
	$mail->Port = $smtp['smtp_port'];
	//$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
	$mail->SMTPSecure  = $transport_type;
	$mail->SMTPAuth = true;
	$mail->Username = $smtp['smtp_email'];
	$mail->Password = $smtp['smtp_pass'];
	$mail->setFrom($from_email,$from_name);
	// $mail->From        = $from_email;
	// $mail->FromName    = $from_name;

	//Set who the message is to be sent to
	$mail->addAddress($toEmail, '');
	$mail->AddCC($cc_email, '');
	$mail->Subject = $subject;
	$mail->msgHTML($email_content);
	
	if($attachment) {
		$mail->addAttachment($attachment);
	}
	
	if (!$mail->send()) {
	  //echo 'Mailer Error: '. $mail->ErrorInfo;
	  $is_email_sent = false;
	} else {
	  $is_email_sent = true;
	}

	return $is_email_sent;

}

function do_send_mail($email_content,$toEmail,$attachment=null) {
	global $config;
	$smtp = $config['smtp'];

	$is_sent = false;
	$num = md5(time());

	$from_email = $smtp['from_email'];
	$from_name = $smtp['from_name'];
	$cc_email = $smtp['cc_email'];
	$to      = $toEmail .','.$cc_email;
	$subject = $config['email_subject'];
	$message = $email_content;

	$headers  = "From: ".$from_name." <".$from_email.">\n";
	//$headers .= "Cc: ".$cc_email. "\r\n";
	$headers  .= "MIME-Version: 1.0\r\n";
	
	if($attachment) {
		$file_type = mime_content_type($attachment);
		$file_size = filesize($attachment);
		$file_name = basename($attachment);

		$fp = fopen($attachment, "r");
		$file = fread($fp, $file_size);
		$encoded = chunk_split(base64_encode($file));
		fclose($fp);
		
		$headers  .= "Content-Type: multipart/mixed; ";
		$headers  .= "boundary=".$num."\r\n";
		$headers  .= "--$num\r\n";

		// This two steps to help avoid spam
		// $headers .= "Message-ID: <".gettimeofday()." TheSystem@".$_SERVER['SERVER_NAME'].">\r\n";
		// $headers .= "X-Mailer: PHP v".phpversion()."\r\n";

		// With message
		$headers .= "Content-Type: text/html; charset=iso-8859-1\r\n";
		$headers .= "Content-Transfer-Encoding: 7bit\r\n";
		$headers .= "".$message."\n";
		$headers .= "--".$num."\n";

		// Attachment headers
		$headers  .= "Content-Type: ".$file_type." ";
		$headers  .= "name=\"".$file_name."\"r\n";
		$headers  .= "Content-Transfer-Encoding: base64\r\n";
		$headers  .= "Content-Disposition: attachment; filename=".$file_name."\r\n";
		$headers  .= "".$encoded."\r\n";
		$header .= "--".$num."--";
		
	} else {

		$headers .= "Message-ID: <".gettimeofday()." TheSystem@".$_SERVER['SERVER_NAME'].">\r\n";
		$headers .= "X-Mailer: PHP v".phpversion()."\r\n";
		$headers .= "Content-Type: text/html; charset=iso-8859-1\r\n";

	}
	
	$is_sent = @mail($to, $subject, $message, $headers);
	return $is_sent;

}



function sanitize_title($string) {
  $url = $string;
  $url = preg_replace('~[^\\pL0-9_]+~u', '-', $url); // substitutes anything but letters, numbers and '_' with separator
  $url = trim($url, "-");
  $url = iconv("utf-8", "us-ascii//TRANSLIT", $url); // TRANSLIT does the whole job
  $url = strtolower($url);
  $url = preg_replace('~[^-a-z0-9_]+~', '', $url); // keep only letters, numbers, '_' and separator
  return $url;
}

