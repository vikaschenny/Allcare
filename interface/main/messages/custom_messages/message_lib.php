<?php
/**
 * Copyright (C) 2010 OpenEMR Support LLC
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * 2013/02/08 Minor tweaks by EMR Direct to allow integration with Direct messaging
 * 2013-03-27 by sunsetsystems: Fixed some weirdness with assigning a message recipient,
 *   and allowing a message to be closed with a new note appended and no recipient.
 */

//SANITIZE ALL ESCAPES
$sanitize_all_escapes=true;

//STOP FAKE REGISTER GLOBALS
$fake_register_globals=false;

require_once("../../../globals.php");
require_once($GLOBALS['srcdir'].'/sql.inc');
require_once ('../../../../modules/PHPMailer/PHPMailerAutoload.php');
require_once '../../../../api/AesEncryption/GibberishAES.php';


function getPnoteById($id, $cols = "*")
{
  return sqlQuery("SELECT $cols FROM tbl_allcare_custom_messages WHERE id=? " .
    ' AND deleted != 1 '. // exclude ALL deleted notes
    'order by date DESC limit 0,1', array($id) );
}

function sendEmail($id){
    
    $sql=sqlStatement("select * from tbl_allcare_custom_messages where id=$id");
    $row=sqlFetchArray($sql);
    
    $user=sqlStatement("select c.email from users  u inner join tbl_user_custom_attr_1to1 c  on c.userid=u.id where username='".$row['user']."'");
    $row1=sqlFetchArray($user);
    if(!empty($row1)){
        $toEmails = $row1['email']; 
    }else {
        $a=sqlStatement("select * from tbl_allcare_agencyportal where portal_username='".$row['user']."'");
        $arow=sqlFetchArray($a);
        $user1=sqlStatement("select email from users  where id='".$arow['uid']."'");
        $row11=sqlFetchArray($user1);
        $toEmails = $row11['email']; 
    }
    if($toEmails!=''){
         //to get global settings
        $smtp_details  =sqlStatement("select gl_name,gl_value from globals where gl_name IN('SMTP_HOST','SMTP_PORT','SMTP_PASS','SMTP_USER') ");
        while($details=sqlFetchArray($smtp_details)){
            $arr[$details['gl_name']]=$details['gl_value'];
        }
    
        $from=$arr['SMTP_USER'];
        $mail = new PHPMailer;
        $mail->isSMTP();
        $mail->SMTPDebug = 0;
        $mail->Debugoutput = 'html';
        if(in_array('SMTP_HOST',array_flip($arr)))
        $mail->Host = $arr['SMTP_HOST'];
        if(in_array('SMTP_PORT',array_flip($arr)))
        $mail->Port = $arr['SMTP_PORT'];
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = "ssl";

        if(in_array('SMTP_USER',array_flip($arr)))
        $mail->Username = $arr['SMTP_USER'];
        if(in_array('SMTP_PASS',array_flip($arr)))
        $mail->Password = GibberishAES::dec($arr['SMTP_PASS'], 'rotcoderaclla');
        $mail->setFrom($arr['SMTP_USER'], 'Smart MBBS');

    
        if(count($toEmails)>1){
            foreach($toEmails as $eachEmail){
                $mail->addAddress($eachEmail);
            }
        }
        else
        {
            $mail->addAddress("$toEmails");
        }
        //for email content
        foreach(explode("\n",$row['body']) as $value){
            $body.=$value."<br>";
        }
        
        if($row['object_type']=='patients'){
            $psql=sqlStatement("select * from patient_data where pid=".$row['obj_id']);
            $prow=sqlFetchArray($psql);
            $obj_name = $prow['lname'];
            if ($prow['fname']) {
                $obj_name .= ", " . $prow['fname'];
            }
        }
        else if($row['object_type']=='facility'){
            $fsql=sqlStatement("select * from facility  where id=".$row['obj_id']);
            $frow=sqlFetchArray($fsql);
            $obj_name=$frow['name'];
        }
        else if($row['object_type']=='insurance'){
            $fsql=sqlStatement("select * from insurance_companies  where id=".$row['obj_id']);
            $frow=sqlFetchArray($fsql);
            $obj_name=$frow['name'];
        }
        else if($row['object_type']=='pharmacy'){
            $fsql=sqlStatement("select * from pharmacies  where id=".$row['obj_id']);
            $frow=sqlFetchArray($fsql);
            $obj_name=$frow['name'];
        }
        else if($row['object_type']=='users'){
            $fsql=sqlStatement("select * from users  where id=".$row['obj_id']);
            $frow=sqlFetchArray($fsql);
            $obj_name = $frow['lname'];
            if ($frow['fname']) {
                $obj_name .= ", " . $frow['fname'];
            }
        }else if($row['object_type']=='address_Book'){
            $fsql=sqlStatement("SELECT *
                            FROM users AS u
                            LEFT JOIN list_options AS lo ON list_id =  'abook_type'
                            AND option_id = u.abook_type where id='".$row['obj_id']."' and active=1 and authorized=1");
            $frow=sqlFetchArray($fsql);
            $obj_name = $frow['lname'];
            if ($frow['fname']) {
                $obj_name .= ", " . $frow['fname'];
            }
        }
        $body_content=explode("||",$body);  
        foreach($body_content as $val){
            $b1.=$val."<br>";
        }
        $content="<h3>Message Details:</h3>"
                . "<div><b>Message Type&nbsp;:</b>&nbsp;".$row['title']."<br>"
                . "<b>Object Type&nbsp;:</b>&nbsp;".$row['object_type']."<br>"
                . "<b>Object Value&nbsp;:</b>&nbsp;".$obj_name."<br>"
                . "<b>Status&nbsp;:</b>&nbsp;".$row['message_status']."<br>"
                . "<b>priority&nbsp;:</b>&nbsp;".$row['priority']."<br>"
                . "<b>content&nbsp;:</b>&nbsp;".$b1."</div>";
        $value= $id."_msg"; 
        $individual_link='http://'.$_SERVER['HTTP_HOST'].'/interface/login/login_frame.php?param='.GibberishAES::enc($value, 'rotcoderaclla');
        $mail->Subject = "Message has been updated.  Message Type:".$row['title'].", status:".$row['message_status'];
        $mail->msgHTML($content); 
        $mail->AltBody = 'This is a plain-text message body';
        
        //send the message, check for errors
        if (!$mail->send()) {
            echo "Mailer Error: " . $mail->ErrorInfo;
            $mstatus = false;
        } else {
            $mstatus = true;
        } 
    }
}
 
function addMessage($pid, $newtext, $authorized = '0', $activity = '1',
  $title= 'Unassigned', $assigned_to = '', $datetime = '',
  $message_status = 'New',$group, $object_type,$priority,$background_user="")
{
  if (empty($datetime)) $datetime = date('Y-m-d H:i:s');

  // make inactive if set as Done
  if ($message_status == 'Done') $activity = 0;

  $user = ($background_user!="" ? $background_user : $_SESSION['authUser']);
  $body = date('Y-m-d H:i') . ' (' . $user;
  if ($assigned_to) $body .= " to $assigned_to";
  if ($group) $body .= "(related to the group:$group)";
  $body = $body . ') ' . $newtext;

 return sqlInsert('INSERT INTO tbl_allcare_custom_messages (date, body, obj_id, user, groupname, ' .
    'authorized, activity, title, assigned_to, message_status,object_type , priority) VALUES ' .
    '(?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?,?)',
    array($datetime, $body, $pid, $user, $_SESSION['authProvider'], $authorized, $activity, $title, $assigned_to, $message_status,$object_type,$priority) );
}

function updateMessage($id, $newtext, $title, $assigned_to, $message_status = "",$group,$object_type,$priority)
{
  $row = getPnoteById($id);
  if (! $row) die("updateMessage() did not find id '".text($id)."'");
  $activity = $assigned_to ? '1' : '0'; 

  // make inactive if set as Done
  if ($message_status == "Done") $activity = 0;

  $body = $row['body'] . "||" . date('Y-m-d H:i') .
    ' (' . $_SESSION['authUser'];
  if ($assigned_to) $body .= " to $assigned_to";
  if ($group) $body .= "(related to the group:$group)";
  $body = $body . ') ' . $newtext;

  if ($message_status) {
    sqlStatement("UPDATE tbl_allcare_custom_messages SET " .
      "body = ?, activity = ?, title= ?, " .
      "assigned_to = ?, message_status = ? , priority=? WHERE id = ?",
      array($body, $activity, $title, $assigned_to, $message_status,$priority, $id) );
  }
  else {
    sqlStatement("UPDATE tbl_allcare_custom_messages SET " .
      "body = ?, activity = ?, title= ?, " .
      "assigned_to = ? , priority=? WHERE id = ?",
      array($body, $activity, $title, $assigned_to,$priority, $id) ); 
  }
  
    $sql=sqlStatement("select * from tbl_allcare_custom_messages where id=$id");
    $row=sqlFetchArray($sql);
    
   
    $user=sqlStatement("select * from users  u inner join tbl_user_custom_attr_1to1 c  on c.userid=u.id where username='".$row['user']."'");
    $row1=sqlFetchArray($user);
    
   //if ($message_status == "Done"){
       sendEmail($id);
   //}
   
}

function getMsgsByUser($activity="1",$show_all="no",$user='',$count=false,$sortby='',$sortorder='',$begin='',$listnumber='')
{

  // Set the activity part of query
  if ($activity=='1') {
    $activity_query = " u.message_status != 'done' AND u.activity = 1 AND ";
  }
  else if ($activity=='0') {
    $activity_query = " (u.message_status = 'done' OR u.activity = 0) AND ";
  }
  else { //$activity=='all'
    $activity_query = " ";
  }
  
  if ($show_all == 'yes' ) {
    $usrvar='_%';
  } else {
    $usrvar=$user;
  }

  $sql="SELECT u.id, u.user, u.obj_id,u.body,u.assigned_to, u.title, u.date,u.priority,u.object_type, u.message_status FROM tbl_allcare_custom_messages u
        LEFT JOIN users ON u.user = users.username
        WHERE $activity_query u.deleted != '1' AND u.assigned_to LIKE ?";
 
 $result = sqlStatement($sql,array($usrvar));

  // return the results
  if ($count) {
    if(sqlNumRows($result) != 0) {
        $total = sqlNumRows($result);
    }
    else {
        $total = 0;
    }
    return $total;
  }
  else {
    return $result;
  }
}

function deleteMessage($id)
{

 $sql= sqlStatement("UPDATE tbl_allcare_custom_messages SET deleted = '1' WHERE id IN ('$id')");
  if($sql==1)
  return true;
  else
  return false;    
}
?>
