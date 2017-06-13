<?php
//SANITIZE ALL ESCAPES
$sanitize_all_escapes=true;
//

//STOP FAKE REGISTER GLOBALS
$fake_register_globals=false;
//

require_once("../../globals.php");
require_once("$srcdir/globals.inc.php");
require_once("$srcdir/api.inc");
require_once("$srcdir/forms.inc");
require_once("$srcdir/options.inc.php");
require_once("$srcdir/patient.inc");
require_once("$srcdir/formdata.inc.php");
require_once("$srcdir/formatting.inc.php");

$pid            = $_REQUEST['pid'];
$month          = $_REQUEST['month'];
$type           = $_REQUEST['type'];
$form_id        = trim($_REQUEST['form_id']);
$field_id       = str_replace("form_", "" ,$_REQUEST['field_id']);
$field_val      = rtrim($_REQUEST['field_val'], "|");
$username       = $_SESSION['authUser'];
$patient_bal    = $_REQUEST['patient_bal']; 
$insurance_bal  = $_REQUEST['insurance_bal']; 
$total_bal      = $_REQUEST['total_bal']; 

/* Eligibility Screen Code */
if($type == 'STATS') {
    // insert in demographics page
    $getstatssql = sqlStatement("SELECT * FROM patient_data where pid = $pid ");
    while($statsresultset = sqlFetchArray($getstatssql)){
        $insert_field_value = sqlStatement("UPDATE patient_data SET `$field_id` = '".addslashes($field_val)."' WHERE pid = $pid");
    }
    $new_id = eligibility_table($form_id,$field_id,$field_val,$pid,$month,$username,$patient_bal,$insurance_bal,$total_bal);
}

if($type == 'Insurance'){
    $get_plan_name = sqlStatement("SELECT plan_name FROM insurance_data WHERE pid = $pid AND type='primary'");
    $set_plan_name = sqlFetchArray($get_plan_name);
    if($set_plan_name){
        $update_insurance_data = sqlStatement("UPDATE insurance_data SET `$field_id` = '".addslashes($field_val)."' WHERE pid = $pid AND type='primary'");
    }else{
        $insert_insurance_data = sqlStatement("INSERT INTO insurance_data (`pid`,`type`,`accept_assignment`,`$field_id`) VALUES ($pid,'primary','YES','".addslashes($field_val)."')");
    }
    $new_id = eligibility_table($form_id,$field_id,$field_val,$pid,$month,$username,$patient_bal,$insurance_bal,$total_bal);
}

if($type == 'Insurance and STATS'){
    // to insert in stats section
    $getstatssql = sqlStatement("SELECT * FROM patient_data where pid = $pid ");
    while($statsresultset = sqlFetchArray($getstatssql)){
        $insert_field_value = sqlStatement("UPDATE patient_data SET `$field_id` = '".addslashes($field_val)."' WHERE pid = $pid");
    }
    $new_id = eligibility_table($form_id,$field_id,$field_val,$pid,$month,$username,$patient_bal,$insurance_bal,$total_bal);
    // insurance
    $get_plan_name = sqlStatement("SELECT * FROM insurance_data WHERE pid = $pid AND type='primary'");
    $set_plan_name = sqlFetchArray($get_plan_name);
    if($set_plan_name){
        $update_insurance_data = sqlStatement("UPDATE insurance_data SET `$field_id` = '".addslashes($field_val)."' WHERE pid = $pid AND type='primary'");
    }else{
        $insert_insurance_data = sqlStatement("INSERT INTO insurance_data (`pid`,`type`,`accept_assignment`,`$field_id`) VALUES ($pid,'primary','YES','".addslashes($field_val)."')");
    }
}
// insert into eligibility table
function eligibility_table($form_id,$field_id,$field_val,$pid,$month,$username,$patient_bal,$insurance_bal,$total_bal){
    if($form_id == 0){
        $insert_elig_table = sqlStatement("INSERT INTO tbl_eligibility_response_data (pid,month, created_date, updated_date, user, domain, patient_bal, insurance_bal, total_bal,`$field_id`) VALUES($pid,'$month', NOW(), NOW(), '$username', 'EMR_Eligibility','$patient_bal','$insurance_bal','$total_bal','".addslashes($field_val)."')");
        $get_form_id = sqlStatement("SELECT max(id) as id FROM tbl_eligibility_response_data");
        while($set_form_id = sqlFetchArray($get_form_id)){
            $new_id = $set_form_id['id'];
        }
    }else{
        $update_elig_table = sqlStatement("UPDATE tbl_eligibility_response_data SET `$field_id` = '".addslashes($field_val)."', updated_date = NOW(), patient_bal = '$patient_bal', insurance_bal = '$insurance_bal', total_bal = '$total_bal' WHERE id = $form_id ");
    }
    return $new_id;
}

/* End of Eligibility Screen Code */ 
if(empty($new_id))
    $new_id[] = $form_id;
echo json_encode($new_id);
?>