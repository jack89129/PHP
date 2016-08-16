<?php

$name = "test";
$email = 'jamespiao03@gmail.com';
$mail_addr = "jamespiao03@gmail.com";
$mail_subject = "Test eMail From your Market";
if ( $name )
	$mail_content = "Name : " . $name . "\r\neMail Address : " . $email . "\r\n";
else 
	$mail_content = "eMail Address : " . $email . "\r\n";

mail($mail_addr, $mail_subject, $mail_content,
"From: admin@takemetoangelave.co.uk\r\n" .
"Reply-To: webmaster@{$_SERVER['SERVER_NAME']}\r\n" .
"X-Mailer: PHP/" . phpversion());

?>