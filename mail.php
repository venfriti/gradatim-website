<?php
// Include PHPMailer (adjust path if needed)
require 'PHPMailer-master/src/Exception.php';
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// ===== SMTP CONFIGURATION - UPDATE WITH YOUR WORKING SETTINGS =====
$smtp_config = [
    'host' => 'smtp.hostinger.com',           // Same as your test
    'port' => 587,                        // Same as your test
    'username' => 'hello@gradatimconcept.com', // Same as your test
    'password' => '5tc2putu]Vj5',    // Same as your test
    'encryption' => 'tls',                // Same as your test
    'from_name' => 'Gradatim Concept'      // Display name
];
// ================================================================

$method = $_SERVER['REQUEST_METHOD'];
//Script Foreach
$c = true;
$message = ""; // Initialize message variable

if ( $method === 'POST' ) {
	$project_name = trim($_POST["project_name"]);
	$admin_email  = trim($_POST["admin_email"]);
	$form_subject = trim($_POST["form_subject"]);
	foreach ( $_POST as $key => $value ) {
		if ( $value != "" && $key != "project_name" && $key != "admin_email" && $key != "form_subject" ) {
			$message .= "
			" . ( ($c = !$c) ? '<tr>':'<tr style="background-color: #f8f8f8;">' ) . "
				<td style='padding: 10px; border: #e9e9e9 1px solid;'><b>$key</b></td>
				<td style='padding: 10px; border: #e9e9e9 1px solid;'>$value</td>
			</tr>
			";
		}
	}
} else if ( $method === 'GET' ) {
	$project_name = trim($_GET["project_name"]);
	$admin_email  = trim($_GET["admin_email"]);
	$form_subject = trim($_GET["form_subject"]);
	foreach ( $_GET as $key => $value ) {
		if ( $value != "" && $key != "project_name" && $key != "admin_email" && $key != "form_subject" ) {
			$message .= "
			" . ( ($c = !$c) ? '<tr>':'<tr style="background-color: #f8f8f8;">' ) . "
				<td style='padding: 10px; border: #e9e9e9 1px solid;'><b>$key</b></td>
				<td style='padding: 10px; border: #e9e9e9 1px solid;'>$value</td>
			</tr>
			";
		}
	}
}

$message = "<table style='width: 100%;'>$message</table>";

function adopt($text) {
	return '=?UTF-8?B?'.Base64_encode($text).'?=';
}

// Replace the mail() function with PHPMailer SMTP
try {
    // Create PHPMailer instance
    $mail = new PHPMailer(true);
    
    // Server settings
    $mail->isSMTP();
    $mail->Host = $smtp_config['host'];
    $mail->SMTPAuth = true;
    $mail->Username = $smtp_config['username'];
    $mail->Password = $smtp_config['password'];
    $mail->SMTPSecure = $smtp_config['encryption'];
    $mail->Port = $smtp_config['port'];
    $mail->CharSet = 'UTF-8';
    
    // Disable SSL verification (same as your test)
    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );
    
    // Recipients
    $mail->setFrom($smtp_config['username'], $smtp_config['from_name']);
    $mail->addAddress($admin_email);
    
    // Set reply-to from form if available
    if (isset($_POST['E-mail']) && !empty($_POST['E-mail'])) {
        $mail->addReplyTo($_POST['E-mail'], $_POST['Name'] ?? '');
    } elseif (isset($_GET['E-mail']) && !empty($_GET['E-mail'])) {
        $mail->addReplyTo($_GET['E-mail'], $_GET['Name'] ?? '');
    } else {
        $mail->addReplyTo($admin_email);
    }
    
    // Content
    $mail->isHTML(true);
    $mail->Subject = adopt($form_subject);
    $mail->Body = $message;
    
    // Create plain text version
    $alt_message = strip_tags(str_replace(['<tr>', '</tr>', '<td>', '</td>', '<b>', '</b>'], ["\n", '', '', ': ', '', ''], $message));
    $mail->AltBody = $alt_message;
    
    // Send the email
    $mail->send();
    
    // Success response for your template JavaScript
    echo 'success';
    
} catch (Exception $e) {
    // Log error for debugging
    error_log("Contact form error: " . $e->getMessage());
    
    // Error response for your template JavaScript
    http_response_code(400);
    echo 'error';
}
?>