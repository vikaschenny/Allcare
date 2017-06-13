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
$category=$_REQUEST['category'];
$parentid=$_REQUEST['parentid'];
if($category=='patients'){
        $sql=sqlStatement("select * from patient_data where patient_folder LIKE '%$parentid%'");
        $sd=sqlFetchArray($sql);
        $obj_name = $sd['lname'];
        if ($sd['fname']) {
            $obj_name .= ", " . $sd['fname'];
        }
        $obj_id=$obj_name;
        $obj_id1=$sd['pid'];
    }else if($category=='users'){
        $sql=sqlStatement("select username from tbl_user_custom_attr_1to1 uc inner join users u on u.id=uc.userid where drive_sync_folder LIKE '%$parentid%'");
        $sd=sqlFetchArray($sql);
        $obj_id=$sd['username'];
        $obj_id1=$sd['userid'];
    }else if($category=='insurance'){
        $sql=sqlStatement("select name from tbl_inscomp_custom_attr_1to1 ic inner join  insurance_companies i on i.id=ic.insuranceid where payer_folder LIKE '%$parentid%'");
        $sd=sqlFetchArray($sql);
        $obj_id=$sd['name'];
        $obj_id1=$sd['insuranceid'];
    }else if($category=='pharmacy'){
        $sql=sqlStatement("select name from tbl_pharmacy_custom_attributes_1to1 pc inner join pharmacies p on p.id=pc.pharmacyid where pharmacy_folder LIKE '%$parentid%'");
         $sd=sqlFetchArray($sql);
        $obj_id=$sd['name'];
        $obj_id1=$sd['pharmacyid'];
    }else if($category=='address_Book'){
        $sql=sqlStatement("select addrbk_type_id from tbl_addrbk_custom_attr_1to1 where addrbk_folder LIKE '%$parentid%'");
        $sd=sqlFetchArray($sql);
        $obj_id=$sd['addrbk_type_id'];
        $obj_id1=$sd['pid'];
    }else if($category=='facility'){
        $sql=sqlStatement("select f.name from tbl_facility_custom_attr_1to1 fc inner join facility f on f.id=fc.facilityid where facilityfolder LIKE '%$parentid%'");
        $sd=sqlFetchArray($sql);
        $obj_id=$sd['name'];
        $obj_id1=$sd['facilityid'];
    }   
        if($obj_id=='') $obj_id=$parentid;
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
//get extension of the file
$ext=$mime_arr[$type]; 
$pos = strpos($name, '.');
 
if($pos===false)
 $file_name = $name.'.'.$ext; 
else  
  $file_name = $name; 
 
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


//log
$id= GibberishAES::enc($fileid, 'rotcoderaclla'); 
$individual_link='http://'.$_SERVER['SERVER_NAME'].'/agencies/index.php?param='.$id;
$view_link =  "<a href=$individual_link target=".'_blank'.">Document link</a>";
$ins_log=sqlStatement("insert into DriveSync_log (date,user,email,encounter,patient_id,google_folder,file_name ,file_id,status,watsID,category)values(now(),'".$_SESSION['authUser']."','".$row['notes']."','','$obj_id','$individual_link','$file_name','$fileid','files downloaded in $category','','$category')");

//to get global settings
$smtp_details  =sqlStatement("select gl_name,gl_value from globals where gl_name IN('SMTP_HOST','SMTP_PORT','SMTP_PASS','SMTP_USER') ");
while($details=sqlFetchArray($smtp_details)){
    $arr[$details['gl_name']]=$details['gl_value'];
}
//to get message id using document id 
$rel=sqlStatement("select * from tbl_pnotes_file_relation where doc_links='$fileid'");
$relrow=sqlFetchArray($rel);
if($relrow['mid']!=''){ 
    //to get assigned user
    $msg=sqlStatement("select assigned_to from tbl_allcare_custom_messages where id='".$relrow['mid']."'");
    $msrow=sqlFetchArray($msg);
    if($msrow['assigned_to']!=''){
        $user=sqlStatement("select id from users where username='".$msrow['assigned_to']."'");
        $user_row=sqlFetchArray($user);
        $cus=sqlStatement("select email from tbl_user_custom_attr_1to1 where userid='".$user_row['id']."'");
        $rcus=sqlFetchArray($cus);
        if($rcus['email']!=''){
            //to send email
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
                $mail->Password = $arr['SMTP_PASS'];
            $mail->setFrom($arr['SMTP_USER'], 'Smart MBBS');
            $toEmails = $rcus['email'];
            if(count($toEmails)>1){
                foreach($toEmails as $eachEmail){
                    $mail->addAddress($eachEmail);
                }
            }else
            {
                $mail->addAddress($toEmails);
            }
            $mail->Subject =  'File downloaded from agency portal';
            $mail->msgHTML('File downloaded from agency portal'."<br>".$view_link);
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
}
?>