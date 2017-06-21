<?php

////////////////////////////////////////////////////////////////////
// Package:	cron_email_notification
// Purpose:	to be run by cron every hour, look for appointments
//		in the pre-notification period and send an email reminder
//
// Created by:
// Updated by:	Larry Lart on 10/03/2008
////////////////////////////////////////////////////////////////////

// larry :: hack add for command line version
$_SERVER['REQUEST_URI']=$_SERVER['PHP_SELF'];
$_SERVER['SERVER_NAME']='localhost';
$backpic = "";
//print_r($GLOBALS);
// email notification
$ignoreAuth=1;
require_once("../../practice/verify_session.php");
require_once("../../library/globals.inc.php");
require_once ('../PHPMailer/PHPMailerAutoload.php');

//echo "<pre>"; print_r($GLOBALS); echo "</pre>"; exit;

$TYPE = "Email";
$CRON_TIME = 5;

//eligibility_SendMail( $prow['email'], $db_email_msg['email_subject'], 
//				$db_email_msg['message'], $db_email_msg['email_sender'] );
//echo eligibility_SendMail( 'hemasrit@smartmbbs.com', 'hema subject new from message testing 11', 'messagede', 'hhsupport@texashousecalls.com' );

$type = $_POST['note_type'];
$status = $_POST['message_status'];
$assigned_to = $_POST['assigned_to'];
$toEmails = explode(";",$assigned_to);

//$body = "Type : ".$type."<br>Status : ".$status;
/*
$smtp_details  =$sqlconfCentralDB->prepare("select gl_name, gl_value from globals where gl_name"
                                                  . " IN('SMTP_HOST','SMTP_PORT','SMTP_PASS','SMTP_USER') ");
    
$smtp_details->execute();
while($smtpid=$smtp_details->fetchObject()){
    $smtp_val[$smtpid->gl_name]=$smtpid->gl_value;
}
*/

$smtp_details = sqlStatement("select gl_name, gl_value from globals where gl_name IN('SMTP_HOST','SMTP_PORT','SMTP_PASS','SMTP_USER') ");

//echo eligibility_SendMail( 'srinus@smartmbbs.com;srinu.vitam@gmail.com', 'Eligibiliy Response On '.date("m/d/Y"), $_POST["note"], 'srinus@smartmbbs.com',$body );

while($smtpid = sqlFetchArray($smtp_details)):
    $smtp_val[$smtpid['gl_name']] = $smtpid['gl_value'];
endwhile;

if($toEmails!=''){
    /*********send email notification******/
    $mail = new PHPMailer;
    $mail->isSMTP();
    $mail->SMTPDebug = 0;
    $mail->Debugoutput = 'html';
    $mail->Host = $smtp_val['SMTP_HOST'];
    $mail->Port = $smtp_val['SMTP_PORT'];
    $mail->SMTPAuth = true;
    $mail->SMTPSecure = "ssl"; 
    $mail->Username =$smtp_val['SMTP_USER'];           
    $mail->Password = $smtp_val['SMTP_PASS'];
    $mail->setFrom($smtp_val['SMTP_USER'], 'donotreply');

    if(count($toEmails)>1){
        foreach($toEmails as $eachEmail){
            $mail->addAddress($eachEmail);
        }
    }
    else
    {
        $mail->addAddress($toEmails[0]);
    }

    $mail->Subject =  'Eligibiliy Response On '.date("m/d/Y");
    $mail->msgHTML($_POST["note"]);
    $mail->AltBody = 'This is a plain-text message body';
    //send the message, check for errors
    if (!$mail->send()) {
        echo "Mailer Error: " . $mail->ErrorInfo;
        $mstatus = false;
    } else {
        $mstatus = true;
    }
}


//echo eligibility_SendMail( $assigned_to, 'Eligibiliy Response On '.date("m/d/Y"), $_POST["note"], $smtp_val['SMTP_USER'],$body );

// create log
//cron_InsertNotificationLogEntry($TYPE,$prow,$db_email_msg);