<?php 

//STOP FAKE REGISTER GLOBALS
$fake_register_globals=false;

//continue session
session_start();

//landing page definition -- where to go if something goes wrong
$landingpage = "../index.php?site=".$_SESSION['site_id'];	


if ( isset($_SESSION['portal_username']) ) {    
    $portal_user = $_SESSION['portal_username']; 
}else {
    session_destroy();
    header('Location: '.$landingpage.'&w');
    exit;
} 

$ignoreAuth=true; // ignore the standard authentication for a regular OpenEMR user
include_once('../../../interface/globals.php');
 
 $protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === true ? 'https://' : 'https://'; 
 $category_arr= array(
        array('title' =>'patients','fieldid'=>'parent_folder'),
        array('title' =>'users','fieldid'=>'user_parent_folder'),
        array('title' =>'insurance','fieldid'=>'ins_parent_folder'),
        array('title' =>'pharmacy','fieldid'=>'pharmacy_parent_folder'),
        array('title' =>'address_Book','fieldid'=>'addrbk_parent_folder'),
        array('title' =>'facility','fieldid'=>'facility_parent_folder')
);
 
 $category=$_REQUEST['category'];
 
 //to get configured email
 $sql = sqlStatement("select * from list_options where list_id='AllcareDriveSync' and option_id='email'");
 $row = sqlFetchArray($sql);
 
 //to get parent folders for each category
 $selection = sqlStatement("select * from tbl_drivesync_authentication where email='" . $row['notes'] . "'");
 $sel_rows = sqlFetchArray($selection);
 
foreach ($category_arr as $key => $item) {
    $idvalue = str_replace('https://drive.google.com/drive/folders/', '', $sel_rows[$item['fieldid']]);
    if($category_arr[$key]['title']==$category){
     $folderid = $idvalue;
    }
}

$curl = curl_init();
$form_url = $protocol.$_SERVER['HTTP_HOST'].'/api/DriveSync/listall_folderid/'.$row['notes'].'/'. $folderid.'/all';
curl_setopt($curl,CURLOPT_URL, $form_url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);  
$result1 = curl_exec($curl);
$resultant1 = $result1;
curl_close($curl);
$all_folders = json_decode($resultant1, TRUE);

$i=0; $k=0; 

$result12=array();
$list_sql=sqlStatement("select DISTINCT(doc_links)  from tbl_pnotes_file_relation where type='$category'");
while($data_row=sqlFetchArray($list_sql)){
    $id=str_replace('https://drive.google.com/drive/folders/','',$data_row['doc_links']);
    $result12[$i]=$id;  
    $i++;
}

    $tktid=array_diff($all_folders,$result12);
if(count($tktid)<50) $frm=count($tktid); else $frm=50;
foreach($tktid as $val){
    if($k<$frm){
       // echo $k;
   
    $view_link='https://drive.google.com/drive/folders/'.$val;

    $ser=explode(",",unserialize($sel_rows['email_to_users']));
    $myArray = array_filter( $ser );
 foreach($myArray as $val){
   $user_cus1=sqlStatement("select email from tbl_user_custom_attr_1to1 where userid='".$val."'");
   $user_row1=sqlFetchArray($user_cus1);
   $user_cus2=sqlStatement("select username from users where id='".$val."' and username!=''");
   $user_row2=sqlFetchArray($user_cus2);
   $user_nam=$user_row2['username'];
   $email_id=$user_row1['email'];
   
    //send emr msg
    $data3=sqlStatement("INSERT INTO pnotes (date, body, pid, user, groupname, activity, authorized, title, assigned_to, message_status)
    values (NOW(), CONCAT(date_format(now(), '%Y-%m-%d %H:%i'),'".' '."(downloaded files in $category)".' '." $view_link'), 0, '$user_nam', 'Default', 1, 1, 'downloaded files in $category', '".$user_nam."', 'new')");
      
    $lastid=sqlStatement("select max(id) as lastid from pnotes"); 
    $lid=sqlFetchArray($lastid);
            
    $data31=sqlStatement("INSERT INTO tbl_pnotes_file_relation (date, mid, doc_links, type)
            values (NOW(),'".$lid['lastid']."','$view_link','$category')");    
    
     //to send email
    $mail = new PHPMailer;
    $mail->isSMTP();
    $mail->SMTPDebug = 0;
    $mail->Debugoutput = 'html';
    $mail->Host = "smtp.gmail.com";
    $mail->Port = 465;
    $mail->SMTPAuth = true;
    $mail->SMTPSecure = "ssl";
    $mail->Username = "srinus@smartmbbs.com";
    $mail->Password = "srinucnu@#"; 
    $mail->setFrom($from, 'Smart MBBS');

    //    $toEmails = explode(";",$to);
        $toEmails = $email_id;
        if(count($toEmails)>1){
            foreach($toEmails as $eachEmail){
                $mail->addAddress($eachEmail);
            }
        }
        else
        {
            $mail->addAddress($toEmails);
        }

        $mail->Subject =  "Emr Message created";
        $mail->msgHTML("$view_link");
        $mail->AltBody = 'This is a plain-text message body';

        //send the message, check for errors
        if (!$mail->send()) {
            echo "Mailer Error: " . $mail->ErrorInfo;
            $mstatus = false;
        } else {
            $mstatus = true;
        }
    }
    $cnt=$k;
    }else {
       
      $cnt=$k;
        
        if($cnt==$frm){
            $msg=count($tktid)-$frm; 
            if($msg==0)
            echo $msg.":Emr messages sucessfully  created for $category";
            else
                 echo $msg.":cont";
        }
        exit();
    }
    $k++;
}

if($cnt+1==$frm){
    $msg=count($tktid)-$frm; 
        if($msg==0) {
        echo $msg.":Emr messages sucessfully  created for $category";
        
        }
        else
        echo $msg.":cont";
        
}else {
    echo "fail";
}

?>