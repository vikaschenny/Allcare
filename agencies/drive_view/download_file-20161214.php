<?php
 // Copyright (C) 2011 Cassian LUP <cassi.lup@gmail.com>
 //
 // This program is free software; you can redistribute it and/or
 // modify it under the terms of the GNU General Public License
 // as published by the Free Software Foundation; either version 2
 // of the License, or (at your option) any later version.

//setting the session & other config options
session_start();

//don't require standard openemr authorization in globals.php
$ignoreAuth = 1;

//SANITIZE ALL ESCAPES
$fake_register_globals=false;

//STOP FAKE REGISTER GLOBALS
$sanitize_all_escapes=true;

//For redirect if the site on session does not match
$landingpage = "index.php?site=".$_GET['site'];

$webserver_root = dirname(dirname(__FILE__));

//includes
require_once('../../interface/globals.php');
    

include_once ('../../modules/PHPMailer/PHPMailerAutoload.php');
require_once '../../api/AesEncryption/GibberishAES.php';

// to get configured email
$sql = sqlStatement("select * from list_options where list_id='AllcareDriveSync' and option_id='email'");
$row = sqlFetchArray($sql);

$fileid=$_REQUEST['file_id']; 
$type=$_REQUEST['type'];
$name=$_REQUEST['name']; 

$mime_types= array(
                "xls" =>'application/vnd.ms-excel',
                "xlsx" =>'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                "xml" =>'text/xml',
                "ods"=>'application/vnd.oasis.opendocument.spreadsheet',
                "csv"=>'text/csv',
                "tmpl"=>'text/plain',
                "pdf"=> 'application/pdf',
                "php"=>'application/x-httpd-php',
                "jpg"=>'image/jpeg',
                "png"=>'image/png',
                "gif"=>'image/gif',
                "bmp"=>'image/bmp',
                "txt"=>'text/plain',
                "doc"=>'application/msword',
                "js"=>'text/js',
                "swf"=>'application/x-shockwave-flash',
                "mp3"=>'audio/mpeg',
                "zip"=>'application/zip',
                "rar"=>'application/rar',
                "tar"=>'application/tar',
                "arj"=>'application/arj',
                "cab"=>'application/cab',
                "html"=>'text/html',
                "htm"=>'text/html',
                "default"=>'application/octet-stream',
                "folder"=>'application/vnd.google-apps.folder',
                "xlsx"=>'application/vnd.google-apps.spreadsheet',
                "doc"=>'application/vnd.google-apps.document'
        );
$mime_arr=array_flip($mime_types);

//get file metadata
//$curl = curl_init();
//$form_url = 'http://'.$_SERVER['HTTP_HOST'].'/api/DriveSync/getfileinfo_web/'.$row['notes'].'/'.$fileid;
//curl_setopt($curl,CURLOPT_URL, $form_url);
//curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);  
//$result = curl_exec($curl);
//$resultant = $result;
//curl_close($curl);
//$folderinfo = json_decode($resultant, TRUE);

//get extension of the file
$ext=$mime_arr[$type]; 
$pos = strpos($name, '.');
 
if($pos===false)
 $file_name = $name.'.'.$ext; 
else  
  $file_name = $name; 
 
//if($type=="application/vnd.google-apps.spreadsheet"){ 
//    $type="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet";
//}


//get recent accesstoken
$curl = curl_init();
$form_url = 'http://'.$_SERVER['HTTP_HOST'].'/api/DriveSync/getaccesstoken_web/'.$row['notes'];
curl_setopt($curl,CURLOPT_URL, $form_url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);  
$result = curl_exec($curl);
$resultant = $result;
curl_close($curl);

//Urls to download files 
if($ext=='xlsx' || $ext=='doc'){ 
    if($ext=='xlsx')
     $getUrl='https://docs.google.com/spreadsheets/d/'.$fileid.'/export?format='.$ext;  
    else if($ext=='doc')
       $getUrl='https://docs.google.com/document/d/'.$fileid.'/export?format='.$ext;
    else 
      $getUrl='https://drive.google.com/uc?export=download&id='.$fileid;

    $authHeader = 'Authorization: Bearer ' . $resultant ; 

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $getUrl);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        $authHeader ,
    ]);
    header('Content-type: application/octet-stream');
    header('Content-Disposition: attachment; filename='.$file_name);
    $data = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch); 
}else { 
    $url  = 'http://'.$_SERVER['HTTP_HOST'].'/api/DriveSync/downloadfile_web/'.$row['notes'].'/'.$fileid;
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_FILE, $fp);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    
   
//    $fp = fopen($file_name, 'w'); 
//    $ch = curl_init($url);
//    curl_setopt($ch, CURLOPT_FILE, $fp);
//    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
//    header("Content-Length: " . filesize($file_name));
    header('Content-Type:application/octet-stream');
    header('Content-Disposition: attachment; filename='.$file_name);
    $data = curl_exec($ch);
    readfile($file_name);
    curl_close($ch);
    if (file_exists($file_name)){
            if (unlink($file_name)) {   
                //echo "success";
            }   
        } 
    fclose($fp);
}
$category=$_REQUEST['category'];
 //log
 //$link.='https://drive.google.com/file/d/'.$fileid."||";
  
 $id= GibberishAES::enc($fileid, 'rotcoderaclla'); 
 $individual_link='http://'.$_SERVER['SERVER_NAME'].'/providers/index.php?param='.$id;
  $view_link =  "<a href=$individual_link target=".'_blank'.">Document link</a>";
 $ins_log=sqlStatement("insert into DriveSync_log (date,user,email,encounter,patient_id,google_folder,file_name ,file_id,status,watsID)values(now(),'".$_SESSION['authUser']."','".$row['notes']."','','','$individual_link','$file_name','','files downloaded in $category','')");

 $user_cus=sqlStatement("select download_settings from tbl_drivesync_authentication where email='".$row['notes']."'  order by id desc");  
 $user_row=sqlFetchArray($user_cus);
$ser1=unserialize($user_row['download_settings']);      

 $ser=explode(",",unserialize($ser1['user']));
 $myArray = array_filter( $ser );
 foreach($myArray as $val){
//   $user_cus1=sqlStatement("select email from 	tbl_addrbk_custom_attr_1to1 where addrbk_type_id='".$val."' order by id desc");
//   $user_row1=sqlFetchArray($user_cus1);
   $user_cus2=sqlStatement("select username from users where id='".$val."' and username!=''");
   $user_row2=sqlFetchArray($user_cus2);
   $user_nam=$user_row2['username'];
//   $email_id=$user_row1['email'];
   
    //send emr msg
    $data3=sqlStatement("INSERT INTO tbl_allcare_custom_messages (date, body, obj_id, user, groupname, activity, authorized, title, assigned_to, message_status,object_type , priority)
    values (NOW(), CONCAT(date_format(now(), '%Y-%m-%d %H:%i'),'".' '."(downloaded files in $category)".' '." $view_link'), 4, '$user_nam', 'Default', 1, 1, 'downloaded files in $category', '".$user_nam."', 'new','$category','high')");
//   $data3=sqlStatement("INSERT INTO tbl_allcare_custom_messages (date, body, obj_id, user, groupname, 
//                                        activity, authorized, title,assigned_to, message_status,object_type , priority)
//                                        values (date_format(now(), '%Y-%m-%d %H:%i'),'".$body."', "
//                                         . "'".$message_array['linkto']."', '$user_nam', 'Default', 1, 1, "
//                                        . "'".$message_array['Message_type']."', '".$message_array['assigned_to']."', "
//                                        . "'".$message_array['Status']."','$category','".$message_array['priority']."')");
                                 
//     //to send email
//    $mail = new PHPMailer; 
//    $mail->isSMTP();
//    $mail->SMTPDebug = 0;
//    $mail->Debugoutput = 'html';
//    $mail->Host = "smtp.gmail.com";
//    $mail->Port = 465;
//    $mail->SMTPAuth = true;
//    $mail->SMTPSecure = "ssl";
//    $mail->Username = "srinus@smartmbbs.com";
//    $mail->Password = "srinucnu@#"; 
//    $mail->setFrom($from, 'Smart MBBS');
//
//    //    $toEmails = explode(";",$to);
//        $toEmails = $email_id;
//        if(count($toEmails)>1){
//            foreach($toEmails as $eachEmail){
//                $mail->addAddress($eachEmail);
//            }
//        }
//        else
//        {
//            $mail->addAddress($toEmails);
//        }
//
//        $mail->Subject = $ser1['text'];
//        $mail->msgHTML($ser1['text']."<br>".$individual_link);
//        $mail->AltBody = 'This is a plain-text message body';
//
//        //send the message, check for errors
//        if (!$mail->send()) {
//            echo "Mailer Error: " . $mail->ErrorInfo;
//            $mstatus = false;
//        } else {
//            $mstatus = true;
//        }
    }

?>