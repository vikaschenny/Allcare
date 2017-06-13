<?php
//
require_once("verify_session.php");
 
////SANITIZE ALL ESCAPES
//$sanitize_all_escapes=true;
//
////STOP FAKE REGISTER GLOBALS
//$fake_register_globals=false; 

require_once("../library/globals.inc.php");
require_once("$srcdir/api.inc");
require_once("$srcdir/forms.inc");
require_once("$srcdir/log.inc");
require_once("$srcdir/options.inc.php");
require_once("$srcdir/patient.inc");
require_once("$srcdir/formdata.inc.php");
require_once("$srcdir/formatting.inc.php");


$pid                = $_REQUEST['pid'];
$month              = $_REQUEST['month'];
$type               = $_REQUEST['type'];
$form_id            = trim($_REQUEST['form_id']);
$field_id           = str_replace("form_", "" ,$_REQUEST['field_id']);
$field_val          = rtrim($_REQUEST['field_val'], "|");
$username           = $_SESSION['portal_username'];
$patient_bal        = $_REQUEST['patient_bal']; 
$insurance_bal      = $_REQUEST['insurance_bal']; 
$total_bal          = $_REQUEST['total_bal']; 
$payerid            = $_REQUEST['payer']; 
$preauth_id         = $_REQUEST['preauth_id'];
$new_lbf_form_id    = $_REQUEST['new_id'];

/* Eligibility Screen Code */
if($type == 'STATS') {
    // insert in demographics page
    $getstatssql = sqlStatement("SELECT * FROM patient_data where pid = $pid ");
    while($statsresultset = sqlFetchArray($getstatssql)){
        $insert_field_value = sqlStatement("UPDATE patient_data SET `$field_id` = '".addslashes($field_val)."' WHERE pid = $pid");
        $comments = "UPDATE patient_data SET `$field_id` = '".addslashes($field_val)."' WHERE pid = $pid";
        newEvent("update", $_SESSION['portal_username'], 0, 1, "UPDATING STATS FROM Eligibility -->".$comments,$pid);
    }
    $new_id = eligibility_table($form_id,$field_id,$field_val,$pid,$month,$username,$patient_bal,$insurance_bal,$total_bal,$payerid,$preauth_id,$new_lbf_form_id);
}

if($type == 'Insurance'){
    $get_plan_name = sqlStatement("SELECT plan_name FROM insurance_data WHERE pid = $pid AND type='primary'");
    $set_plan_name = sqlFetchArray($get_plan_name);
    if($set_plan_name){
        $update_insurance_data = sqlStatement("UPDATE insurance_data SET `$field_id` = '".addslashes($field_val)."' WHERE pid = $pid AND type='primary'");
        $comments = "UPDATE insurance_data SET `$field_id` = '".addslashes($field_val)."' WHERE pid = $pid AND type='primary'";
        newEvent("update", $_SESSION['portal_username'], 0, 1, "UPDATING Insurance FROM Eligibility -->".$comments,$pid);
    }else{
        $insert_insurance_data = sqlStatement("INSERT INTO insurance_data (`pid`,`type`,`accept_assignment`,`$field_id`) VALUES ($pid,'primary','YES','".addslashes($field_val)."')");
        $comments = "INSERT INTO insurance_data (`pid`,`type`,`accept_assignment`,`$field_id`) VALUES ($pid,'primary','YES','".addslashes($field_val)."')";
        newEvent("insert", $_SESSION['portal_username'], 0, 1, "INSERTING Insurance FROM Eligibility -->".$comments,$pid);
    }
    $new_id = eligibility_table($form_id,$field_id,$field_val,$pid,$month,$username,$patient_bal,$insurance_bal,$total_bal,$payerid,$preauth_id,$new_lbf_form_id);
}

if($type == 'Insurance and STATS'){
    // to insert in stats section
    $getstatssql = sqlStatement("SELECT * FROM patient_data where pid = $pid ");
    while($statsresultset = sqlFetchArray($getstatssql)){
        $insert_field_value = sqlStatement("UPDATE patient_data SET `$field_id` = '".addslashes($field_val)."' WHERE pid = $pid");
        $comments = "UPDATE patient_data SET `$field_id` = '".addslashes($field_val)."' WHERE pid = $pid";
        newEvent("update", $_SESSION['portal_username'], 0, 1, "UPDATING Insurance and STATS FROM Eligibility -->".$comments,$pid);
    }
    $new_id = eligibility_table($form_id,$field_id,$field_val,$pid,$month,$username,$patient_bal,$insurance_bal,$total_bal,$payerid,$preauth_id,$new_lbf_form_id);
    // insurance
    $get_plan_name = sqlStatement("SELECT * FROM insurance_data WHERE pid = $pid AND type='primary'");
    $set_plan_name = sqlFetchArray($get_plan_name);
    if($set_plan_name){
        // Subhan: We do not want copay in Insurance to get updated
        if($field_id != 'Copay'):
            $update_insurance_data = sqlStatement("UPDATE insurance_data SET `$field_id` = '".addslashes($field_val)."' WHERE pid = $pid AND type='primary'");
            $comments = "UPDATE insurance_data SET `$field_id` = '".addslashes($field_val)."' WHERE pid = $pid AND type='primary'";
            newEvent("update", $_SESSION['portal_username'], 0, 1, "UPDATING Copay FROM Eligibility -->".$comments,$pid);
        endif;
        
    }else{
        $insert_insurance_data = sqlStatement("INSERT INTO insurance_data (`pid`,`type`,`accept_assignment`,`$field_id`) VALUES ($pid,'primary','YES','".addslashes($field_val)."')");
        $comments = "INSERT INTO insurance_data (`pid`,`type`,`accept_assignment`,`$field_id`) VALUES ($pid,'primary','YES','".addslashes($field_val)."')";
        newEvent("insert", $_SESSION['portal_username'], 0, 1, "INSERTING Insurance FROM Eligibility -->".$comments,$pid);
    }
}
// insert into eligibility table
function eligibility_table($form_id,$field_id,$field_val,$pid,$month,$username,$patient_bal,$insurance_bal,$total_bal,$payerid,$preauth_id,$new_lbf_form_id){
//    if($form_id == 0){
    // CHECK MYSQL data exists in table or not
    $get_mysql_data = sqlStatement("SELECT id FROM tbl_eligibility_response_data WHERE id= '$form_id'");
    $set_mysql_data = sqlFetchArray($get_mysql_data);
    if(empty($set_mysql_data)){
        $insert_elig_table = sqlStatement("INSERT INTO tbl_eligibility_response_data (pid,month,payerid, created_date, updated_date, user, domain, patient_bal, insurance_bal, total_bal,`$field_id`) VALUES( $pid,'$month','$payerid', NOW(), NOW(), '$username', 'Provider_Portal','$patient_bal','$insurance_bal','$total_bal','".addslashes($field_val)."')");
        $comments = "INSERT INTO tbl_eligibility_response_data (pid,month,payerid, created_date, updated_date, user, domain, patient_bal, insurance_bal, total_bal,`$field_id`) VALUES( $pid,'$month','$payerid', NOW(), NOW(), '$username', 'Provider_Portal','$patient_bal','$insurance_bal','$total_bal','".addslashes($field_val)."')";
        newEvent("insert", $_SESSION['portal_username'], 0, 1, "INSERTING EligibilityResponse FROM Eligibility -->".$comments,$pid);
    }else{
        $update_elig_table = sqlStatement("UPDATE tbl_eligibility_response_data SET `$field_id` = '".addslashes($field_val)."', updated_date = NOW(), patient_bal = '$patient_bal', insurance_bal = '$insurance_bal', total_bal = '$total_bal' WHERE id =  '$form_id'");
        $comments = "UPDATE tbl_eligibility_response_data SET `$field_id` = '".addslashes($field_val)."', updated_date = NOW(), patient_bal = '$patient_bal', insurance_bal = '$insurance_bal', total_bal = '$total_bal' WHERE id =  '$form_id'";
        newEvent("update", $_SESSION['portal_username'], 0, 1, "UPDATING EligibilityResponse FROM Eligibility -->".$comments,$pid);
    }
    $new_id = $form_id;
        
//    }else{
//        $update_elig_table = sqlStatement("UPDATE tbl_eligibility_response_data SET `$field_id` = '".addslashes($field_val)."', updated_date = NOW(), patient_bal = '$patient_bal', insurance_bal = '$insurance_bal', total_bal = '$total_bal' WHERE id = $form_id ");
//    }
    $update_elig_id = sqlStatement("UPDATE tbl_patientuser SET elig_response_id = '$form_id' WHERE id = '$preauth_id'");
    return $new_id;
}

/* End of Eligibility Screen Code */ 
if(empty($new_id))
    $new_id[] = $form_id;
echo json_encode($new_id);
?>