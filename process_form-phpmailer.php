<?php
/*
Make sure to configure creds.php (should live outside of your webroot directory) with correct values for:
$smtp_server
$smtp_username
$smtp_password

Also, make sure to install PHPMailer using Composer by running the following command in your terminal:

composer require phpmailer/phpmailer
This will install the PHPMailer library and its dependencies.

Note that you may need to adjust the SMTP settings to match your specific SMT account configuration. You can find 
more information about your host's SMTP settings in their documentation.
*/

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/phpmailer/phpmailer/src/Exception.php';
require 'vendor/phpmailer/phpmailer/src/PHPMailer.php';
require 'vendor/phpmailer/phpmailer/src/SMTP.php';
require '../../creds.php';

$mail = new PHPMailer;
$disclaimer = $_REQUEST['disclaimer'] === "true" ? "True" : "False";

$mail->isSMTP(); // Set mailer to use SMTP
$mail->Host = $smtp_server; // Specify main and backup SMTP servers
$mail->SMTPAuth = true; // Enable SMTP authentication
$mail->Username = $smtp_username; // SMTP username
$mail->Password = $smtp_password; // SMTP password
$mail->SMTPSecure = 'tls'; // Enable TLS encryption, `ssl` also accepted
$mail->Port = 587; // TCP port to connect to

$mail->setFrom($smtp_username, 'FolkFest Photo');
$mail->addAddress($_REQUEST['email'], $_REQUEST['name']); // Add a recipient
$mail->addAddress($_REQUEST['crew_email']);

$mail->addReplyTo($_REQUEST['crew_email'], 'FolkFest Photo');

$mail->isHTML(true); // Set email format to HTML

$mail->Subject = 'Winnipeg Folk Festival Photo Release';
$mail->Body = 'Name: ' . $_REQUEST['name'] . '<br>Email: ' . $_REQUEST['email'] . '<br>Date: ' . $_REQUEST['date'] . '<br>Photo Number: ' . $_REQUEST['photo_number'] . '<br>Photo Use Permission: ' . $disclaimer;
$mail->AltBody = 'Name: ' . $_REQUEST['name'] . '<br>Email: ' . $_REQUEST['email'] . '<br>Date: ' . $_REQUEST['date'] . '<br>Photo Number: ' . $_REQUEST['photo_number'] . '<br>Photo Use Permission: ' . $disclaimer;

if(!$mail->send()) {
    echo 'Message could not be sent.';
    echo 'Mailer Error: ' . $mail->ErrorInfo;
} else {
    echo '200';
}

?>