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
require_once("verify_session.php");

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
$pagename = "eligibility_response"; 

require_once("../interface/globals.php");
require_once("../library/formdata.inc.php"); 
require_once("$srcdir/api.inc");
require_once("$srcdir/forms.inc");
require_once("$srcdir/options.inc.php");
require_once("$srcdir/patient.inc");
require_once("$srcdir/formatting.inc.php");
require_once("$srcdir/billing.inc");
include_once("$srcdir/calendar.inc");
include_once("$srcdir/edi.inc");


//for logout
$refer                      = $_REQUEST['refer'];
$_SESSION['refer']          = $_REQUEST['refer'];
$_SESSION['portal_username']= $_REQUEST['provider'];
$sql = sqlStatement("SELECT id, fname, lname, specialty FROM users " .
      "WHERE active = 1 AND ( info IS NULL OR info NOT LIKE '%Inactive%' ) " .
      "AND authorized = 1 AND username='".$provider."'" .
      "ORDER BY lname, fname");
$id         = sqlFetchArray($sql);

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
        $insert_elig_table = sqlStatement("INSERT INTO tbl_eligibility_response_data (pid,month, created_date, updated_date, user, domain, patient_bal, insurance_bal, total_bal,`$field_id`) VALUES($pid,'$month', NOW(), NOW(), '$username', 'Provider_Portal_Eligibility_Response','$patient_bal','$insurance_bal','$total_bal','".addslashes($field_val)."')");
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