<?php 
require_once("sqlQa2DB.inc");
require_once ('../../../../modules/PHPMailer/PHPMailerAutoload.php');
require_once '../../../../api/AesEncryption/GibberishAES.php';
 global $sqlconfCentralDB;

 $protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === true ? 'https://' : 'https://'; 
$qry = "select * from list_options where list_id='AllcareDriveSync' and option_id='email'";
$stmt = $sqlconfCentralDB->prepare($qry) ;
$stmt->execute();
$rs = $stmt->fetchObject();

//to get parent folders for each category
$selection = "select scan_medical_mhc from tbl_drivesync_authentication where email='$rs->notes'";
$sel_rows = $sqlconfCentralDB->prepare($selection);
$sel_rows->execute();
$es = $sel_rows->fetchObject();
$idvalue = str_replace('https://drive.google.com/drive/folders/', '', $es->scan_medical_mhc);
$folderid = $idvalue; 

 $data=$sqlconfCentralDB->prepare("select * from tbl_allcare_scanfolders where scan_folder='".$folderid."'");
 $data->execute();
 $drow=$data->fetchObject();
    
 $category='scan_medical_mhc'; 
 
//to get tkt created files
$result12=array();
$today = date("Y-m-d"); 
$list_sql = $sqlconfCentralDB->prepare("select DISTINCT(doc_links)  from tbl_pnotes_file_relation where type='$category'");
$list_sql->execute();

while($data_row = $list_sql->fetchObject()){
    $id=$data_row->doc_links;
    $result12[$i]=$id;  
    $i++;
}

$curl = curl_init();
$form_url = $protocol.$_SERVER['HTTP_HOST'].'/api/DriveSync/listall_folderid/'.$rs->notes.'/'. $folderid.'/all';
curl_setopt($curl,CURLOPT_URL, $form_url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);  
$result1 = curl_exec($curl);
$resultant1 = $result1;
curl_close($curl);
$all_folders = json_decode($resultant1, TRUE);




$date=date("Y-m-d h:i:sa");            
$status=$drow->tkt_status;
$title=$category;
$obj_type='scan';
$grp='Default';
$vid=0;
$aa=1;
$priority=$drow->tkt_priority;
$assigned_to=$drow->tkt_owner;
$user=$drow->tkt_owner; 
if(is_array($all_folders)){
    $tktid=array_diff($all_folders,$result12);
    
    foreach($tktid as $val1){
        $msg_view='http://'.$_SERVER['HTTP_HOST'].'/interface/main/allcarereports/view_file.php?file_id='.$val1;
        $body="<a href=$msg_view target=".'_blank'.">Document link</a>";
        //to get file name
        $curl = curl_init();
        $file_info = $protocol.'/api/DriveSync/getfileinfo/'.$rs->notes.'/'.$val1;
        curl_setopt($curl,CURLOPT_URL, $file_info);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);  
        $fname = curl_exec($curl);
        $filename =json_decode($fname, TRUE);
        curl_close($curl);
        
       // $body=$val1; 
        $stmt1 = $sqlconfCentralDB->prepare("INSERT INTO tbl_allcare_custom_messages 
                                            (date,body,obj_id,user, 
                                            groupname,activity,authorized, 
                                            title,assigned_to,message_status,
                                            object_type,priority,deleted)
                                            VALUES (:date, :body, :obj_id, 
                                            :user, :groupname,:activity,
                                            :authorized,:title,:assigned_to,
                                            :message_status,:obj_type,:priority,
                                            :deleted)");
        $stmt1->bindParam(':date', $date);       
        $stmt1->bindParam(':body', $body); 
        $stmt1->bindParam(':obj_id', $vid);
        // use PARAM_STR although a number  
        $stmt1->bindParam(':user', $user); 
        $stmt1->bindParam(':groupname', $grp);   
        $stmt1->bindParam(':activity', $aa); 
        $stmt1->bindParam(':authorized', $aa);   
        $stmt1->bindParam(':title',$title ); 
        $stmt1->bindParam(':assigned_to', $assigned_to);   
        $stmt1->bindParam(':message_status', $status); 
        $stmt1->bindParam(':obj_type', $obj_type);   
        $stmt1->bindParam(':priority', $priority);
        $stmt1->bindParam(':deleted', $vid); 

        $stmt1->execute(); 
        
        $lastid=$sqlconfCentralDB->prepare("select max(id) as lastid from tbl_allcare_custom_messages");
        $lastid->execute();
        $lid=$lastid->fetchObject();

        $data31=$sqlconfCentralDB->prepare("INSERT INTO tbl_pnotes_file_relation (date, mid, doc_links, type, file_name)
            values (:date,:mid,:doc_links,:type, :name)");  

        $data31->bindParam(':date', $date, PDO::PARAM_STR);   
        $data31->bindParam(':mid', $lid->lastid, PDO::PARAM_STR); 
        $data31->bindParam(':doc_links', $val1, PDO::PARAM_STR);   
        $data31->bindParam(':type', $category, PDO::PARAM_STR);
        $data31->bindParam(':name', $filename['name'], PDO::PARAM_STR);
        $data31->execute();
        
        //email Notification
        $smtp_details  =$sqlconfCentralDB->prepare("select gl_name, gl_value from globals where gl_name"
                                                      . " IN('SMTP_HOST','SMTP_PORT','SMTP_PASS','SMTP_USER') ");
        $smtp_details->execute();
        while($smtpid=$smtp_details->fetchObject()){
            $smtp_val[$smtpid->gl_name]=$smtpid->gl_value;
        }
        
        // get to email 
        $user_details  = $sqlconfCentralDB->prepare("select uc.email from users u inner join tbl_user_custom_attr_1to1 uc on u.id=uc.userid where username='$user'");
        $user_details->execute();
        $uemail=$user_details->fetchObject();
		
		$body_content=explode("||",$body);  
        foreach($body_content as $val){
            $b1.=$val."<br>";
        }
        
        
        $content="<h3>Message Details:</h3>"
                . "<div><b>Message Type&nbsp;:</b>&nbsp;".$title."<br>"
                . "<b>Object Type&nbsp;:</b>&nbsp;".$category."<br>"
                . "<b>Status&nbsp;:</b>&nbsp;".$drow->tkt_status."<br>"
                . "<b>priority&nbsp;:</b>&nbsp;".$drow->tkt_priority."<br>"
                . "<b>content&nbsp;:</b>&nbsp;".$b1."</div>";
		$toEmails = $uemail->email;
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
            $mail->Password =GibberishAES::dec($smtp_val['SMTP_PASS'], 'rotcoderaclla');
            $mail->setFrom($smtp_val['SMTP_USER'], 'Smart MBBS');

        
            
            if(count($toEmails)>1){
                foreach($toEmails as $eachEmail){
                    $mail->addAddress($eachEmail);
                }
            }
            else
            {
                $mail->addAddress($toEmails);
            }
            
            $mail->Subject =  'Message Created from '.$title;
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
        
        
        
        
        
        
        //drive sync log
       
        $enc='';
        $logstatus="scan medical correspondence message created";
        $wats='';
        $file_name=''; $ins_id12='';
        $stmt2 = $sqlconfCentralDB->prepare("insert into DriveSync_log (date,user,email,encounter,patient_id,google_folder,file_name ,file_id,status,watsID,category)"
            . "values(:date,:user,:email,:encounter,:id,:google_folder,:file_name,:file_id,:status,:watsID,:category)");
        $stmt2->bindParam(':date', $today);       
        $stmt2->bindParam(':user', $user); 
        $stmt2->bindParam(':email', $rs->notes);
        $stmt2->bindParam(':encounter', $enc);
        $stmt2->bindParam(':id', $ins_id12);
        $stmt2->bindParam(':google_folder', $val1);
        $stmt2->bindParam(':file_name', $file_name);
        $stmt2->bindParam(':file_id', $val1);
        $stmt2->bindParam(':status', $logstatus);
        $stmt2->bindParam(':watsID', $wats);
        $stmt2->bindParam(':category', $category);
        $stmt2->execute();
    }
} 

?>