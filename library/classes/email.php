<?php
require_once ("library/postmaster.php");
$mail = new MyMailer();
$mail->From = 'abhishekp@riseglobal.net';
$mail->FromName = 'Abhishek';
$mail->Body = 'about openemr';
$mail->Subject = 'this is sample emr mail';
$mail->AddAddress('abhishekp@riseglobal.net', 'abhishek', 'P');
if(!$mail->Send()) {
	error_log("There has been a mail error sending to " . $firstNameDestination ." " . $mail->ErrorInfo);
}
else
{
echo  'email was sent';
}
?>