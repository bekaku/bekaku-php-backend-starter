<?php
require 'src/Exception.php';
require 'src/OAuth.php';
require 'src/PHPMailer.php';
require 'src/POP3.php';
require 'src/SMTP.php';
// Import PHPMailer classes into the global namespace
// These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader
//require 'vendor/autoload.php';

$mail = new PHPMailer(true);                              // Passing `true` enables exceptions
try {
    //Server settings
	/**/
    //$mail->SMTPDebug = 2;                                 // Enable verbose debug output
    $mail->isSMTP();                                      // Set mailer to use SMTP
	$mail->CharSet="UTF-8";
	$mail->isSMTP();
	$mail->Host = 'smtp.googlemail.com';  //gmail SMTP server
	$mail->SMTPAuth = true;
	$mail->SMTPOptions = array(
		'ssl' => array(
			'verify_peer' => false,
			'verify_peer_name' => false,
			'allow_self_signed' => true
			));
    $mail->Username = 'wct.thaioil@gmail.com';                 // SMTP username
    $mail->Password = 'fFas251@';                           // SMTP password
	$mail->SMTPSecure = 'ssl';
	$mail->Port = 465;                    //SMTP port

	
	
	
    //Recipients
    $mail->setFrom('wct.thaioil@gmail.com', 'Warehouse Contents Tracking');
	$mail->addReplyTo('wct.thaioil@gmail.com', 'No Reply');
    $mail->addAddress('chana_vb@yahoo.com', 'Chanavee');     // Add a recipient
    $mail->addAddress('baekaku@gmail.com');               // Name is optional
    //$mail->addCC('cc@example.com');
    //$mail->addBCC('bcc@example.com');

    //Attachments
    //$mail->addAttachment('files/2.rar');         // Add attachments
    //$mail->addAttachment('files/1.png', 'new.jpg');    // Optional name

    //Content
    $mail->isHTML(true);                                  // Set email format to HTML
    $mail->Subject = 'Here is the subject ทดสอบการส่งเมล์นะ';
    $mail->Body    = 'This is the HTML message body <b>in bold!</b> ทดสอบการส่งเมล์นะ';
    $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

    $mail->send();
    echo 'Message has been sent';
} catch (Exception $e) {
    echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
}