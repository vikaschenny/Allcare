<?php
include("../session_file.php"); 
include_once ('../../modules/PHPMailer/PHPMailerAutoload.php');
require_once '../../api/AesEncryption/GibberishAES.php';

extract($_POST);
$sql=sqlStatement("select * from tbl_allcare_document_mapping where tkt_type='".$_POST['Message_type']."'");
$row=sqlFetchArray($sql);
//
//$rsql=sqlStatement("select * from tbl_pnotes_file_relation where doc_links='".$_POST['fileid']."' and type='".$_POST['obj_type']."'");
//$rrow=sqlFetchArray($rsql);
$id= GibberishAES::enc($fileid, 'rotcoderaclla'); 
//for emails
$email_view='http://'.$_SERVER['SERVER_NAME'].'/agencies/index.php?param='.$id;
$elink="<a href=$email_view target=".'_blank'.">Document link</a>";
// for emr messages
$individual_link='http://'.$_SERVER['HTTP_HOST'].'/interface/main/allcarereports/view_file.php?file_id='.$fileid;
$msglink="<a href=$individual_link target=".'_blank'.">Document link</a>";

$body=$_POST['content']." ".$msglink;
$msql=sqlStatement("select * from tbl_allcare_custom_messages where id=".$_POST['messageid']);
$mrow=sqlFetchArray($msql);
if(!empty($mrow)){
    $update=sqlStatement("update tbl_allcare_custom_messages set date=date_format(now(), '%Y-%m-%d %H:%i'),body='".$body."',obj_id='".$_POST['linkto']."',user='".$_POST['username']."',title='".$_POST['Message_type']."',assigned_to='".$row['tkt_owner']."',message_status='".$row['tkt_status']."',object_type='".$_POST['obj_type']."',priority='".$row['tkt_priority']."' where id=".$_POST['messageid']);
}else{
$ins=sqlStatement("INSERT INTO tbl_allcare_custom_messages (date, body, obj_id, user, groupname, activity, authorized, title, assigned_to, message_status,object_type , priority)
                            values (date_format(now(), '%Y-%m-%d %H:%i'),'".$body."', '".$_POST['linkto']."', '', 'Default', 1, 1, '".$_POST['Message_type']."', '".$row['tkt_owner']."', '".$row['tkt_status']."','".$_POST['obj_type']."','".$row['tkt_priority']."')");
}
//to get global settings
$smtp_details  =sqlStatement("select gl_name,gl_value from globals where gl_name IN('SMTP_HOST','SMTP_PORT','SMTP_PASS','SMTP_USER') ");
while($details=sqlFetchArray($smtp_details)){
    $arr[$details['gl_name']]=$details['gl_value'];
}
        $user=sqlStatement("select id from users where username='".$row['tkt_owner']."'");
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
            $mail->Subject =  'File uploaded from agency portal';
            $mail->msgHTML('File uploaded from agency portal'."<br>".$elink);
            $mail->AltBody = 'This is a plain-text message body';
             //send the message, check for errors
            if (!$mail->send()) {
                echo "Mailer Error: " . $mail->ErrorInfo;
                $mstatus = false;
            } else {
                $mstatus = true;
            }
        }            
?>