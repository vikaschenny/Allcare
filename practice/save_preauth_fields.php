<?php
//SANITIZE ALL ESCAPES
//$sanitize_all_escapes=true;
////
//
////STOP FAKE REGISTER GLOBALS
//$fake_register_globals=false;
//
//session_start();
require_once("verify_session.php");

//$provider       = $_SESSION['portal_username'];
//$refer          = $_SESSION['refer'];
 
if(isset($_SESSION['portal_username']) !=''){
    $provider    = $_SESSION['portal_username'];
    $refer       = $_REQUEST['refer'];
    
    if($_REQUEST['refer']!='')
        $_SESSION['refer'] = $_REQUEST['refer'];
}else {
    $provider                    = $_REQUEST['provider'];
    $_SESSION['portal_username'] = $_REQUEST['provider'];
    //for logout
    $refer                       = $_REQUEST['refer'];
    if($_REQUEST['refer']!='')
        $_SESSION['refer'] = $_REQUEST['refer'];
}


require_once("../interface/globals.php");
require_once("$srcdir/globals.inc.php");
require_once("$srcdir/api.inc");
require_once("$srcdir/forms.inc");
require_once("$srcdir/options.inc.php");
require_once("$srcdir/patient.inc");
require_once("$srcdir/formdata.inc.php");
require_once("$srcdir/formatting.inc.php"); 

$preauth_array  = $_REQUEST['preauthdata'];

$pid            = $_REQUEST['pid'];
$provider       = $_REQUEST['provider'];
$payerid        = $_REQUEST['payer'];  
$form_id        = $_REQUEST['form_id'];  
$lbf_form_id    = $_REQUEST['lbf_form_id']; 
$username       = $_SESSION['portal_username'];
$patient_bal    = $_REQUEST['patient_bal']; 
$insurance_bal  = $_REQUEST['insurance_bal']; 
$total_bal      = $_REQUEST['total_bal']; 
$month          = $_REQUEST['month'];
$dos            = $_REQUEST['dos'];

$new_elig_id = 0;

$get_mysql_data = sqlStatement("SELECT id FROM tbl_eligibility_response_data WHERE id= '$lbf_form_id'");
$set_mysql_data = sqlFetchArray($get_mysql_data);
if(empty($set_mysql_data)){
    $insert_elig_table = sqlStatement("INSERT INTO tbl_eligibility_response_data (id,pid,month,payerid, created_date, updated_date, user, domain, patient_bal, insurance_bal, total_bal) VALUES($lbf_form_id, $pid,'$month','$payerid', NOW(), NOW(), '$username', 'Provider_Portal','$patient_bal','$insurance_bal','$total_bal')");
    $get_elig_form_id = sqlStatement("SELECT max(id) as id FROM tbl_eligibility_response_data");
    while($set_elig_form_id = sqlFetchArray($get_elig_form_id)){
        $new_elig_id = $set_elig_form_id['id'];
    }
}

if($form_id == 0){
    
    $preauth_fields = $preauth_values = '';

    foreach ($preauth_array as $prekey => $prevalue){
        $preauth_fields .= ",`".str_replace("form_", "" ,$prevalue['name'])."`"; 
        $preauth_values .= ",'".addslashes($prevalue['value'])."'";
    }
    $insert_insurance_data = sqlStatement("INSERT INTO tbl_patientuser (`pid`, `payer_id`,`userid`,`created_date`,`updated_date`,`elig_response_id` $preauth_fields) VALUES ('$pid','$payerid','$provider',NOW(),NOW(),'$new_elig_id' $preauth_values)");
    $get_form_id = sqlStatement("SELECT max(id) as id FROM tbl_patientuser");
    while($set_form_id = sqlFetchArray($get_form_id)){
        $new_id = $set_form_id['id'];
    }
}else{
    $new_id = $form_id;
    
    $preauth_values = '';

    foreach ($preauth_array as $prekey => $prevalue){
        $preauth_values .= ",`".str_replace("form_", "" ,$prevalue['name'])."` = '".addslashes($prevalue['value'])."'";
    }
    $insert_field_value = sqlStatement("UPDATE tbl_patientuser SET `updated_date` = NOW() $preauth_values  WHERE pid = '$pid' AND payer_id = '$payerid' AND userid = '$provider' AND id = '$form_id'");
}

// check preauth id and create reminder

$get_preauth_id = sqlStatement("SELECT * FROM tbl_patientuser WHERE preauth_id <> '' AND id = $new_id");
$set_preauth_id = sqlFetchArray($get_preauth_id);
if(empty($set_preauth_id)){
    
    // get provider id 
    $get_provider_id = sqlStatement("SELECT id FROM users WHERE username = '".$_SESSION['refer']."'");
    while($set_provider_id = sqlFetchArray($get_provider_id)){
        $provider_id = $set_provider_id['id'];
    }
    
    $from           = $provider_id;
    $message        = "Preauthorization for date of service ($dos) is Pending to this patient.";
//    $due_date       = $reminderArray['data'][0]['due_date'];
    $date = strtotime("+7 day");
    $due_date = date('Y-m-d', $date);
    $msg_status     = 0 ; 
    $msg_priority   = 3;  //  default - high priority


    $save_reminder= sqlStatement("INSERT INTO dated_reminders(dr_from_ID,dr_message_text,dr_message_sent_date,dr_message_due_date,pid,message_priority,message_processed,processed_date,dr_processed_by)
            VALUES('$from','$message',NOW(),'$due_date','$pid','$msg_priority','$msg_status','',0)");
    $get_reminder_id = sqlStatement("SELECT max(dr_id) as id FROM dated_reminders");
    while($set_reminder_id = sqlFetchArray($get_reminder_id)){
        $new_reminder_id = $set_reminder_id['id'];
        $save_reminder_link = sqlStatement("INSERT INTO dated_reminders_link (dr_id,to_id) VALUES ($new_reminder_id, $from)");
    }
    
    
  }

echo json_encode($new_id);
?>