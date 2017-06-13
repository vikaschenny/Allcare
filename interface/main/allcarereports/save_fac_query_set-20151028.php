<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once("../../globals.php");
require_once("../../../library/formdata.inc.php");
require_once("../../../library/globals.inc.php");

 
//$app_enc=$_POST['app_enc'];
//
 $querySetName=$_POST['querySetName'];
//print_r($_REQUEST);
 $screen=$_POST['screen'];
if($screen=='appt'){
    
 $facility=$_POST['facility_val'];
 $facility1=rtrim($facility," ");
 
 $provider=$_POST['provider_val'];
 $provider1=rtrim($provider," ");

 $status=$_POST['status_val'];
 $status1=rtrim($status," ");
 
 $category=$_POST['category_val'];
 $category1=rtrim($category," ");
 
 $appt_from_dt=$_POST['appt_from_dt'];
 $appt_to_dt=$_POST['appt_to_dt'];
 $avail_slot=$_POST['aval_slot'];
 $without_provider=$_POST['without_provider1'];
 $without_facility=$_POST['without_facility1'];
 
 $sel_fields=$_POST['selected_fields1'];
 $sel_fields1=rtrim($sel_fields," ");
 
 $appt_fid=$_POST['filter_id'];
 $appt_fval=$_POST['filter_val'];
 
 foreach($appt_fval as $key => $val){
     $appt_val1.=$val.",";
 }
 
$apptval2=trim($appt_val1,",");

 $sqlInsertQuerySet="INSERT INTO tbl_allcarereports_querysets
    (screen,setname,facility,provider,	status,categories,from_date,to_date,available_slots,without_provider,wihout_facility,selected_fields)
    VALUES('$screen','$querySetName', '$facility1','$provider1','$status1','$category1','$appt_from_dt','$appt_to_dt','$avail_slot','$without_provider','$without_facility','$sel_fields1')";
          
$insertQuerySet=sqlStatement($sqlInsertQuerySet);
}
else if($screen=='appt_enc_report') {
    
   $facility_enc=$_POST['facility_enc'];
   $facility1_enc=rtrim($facility_enc," ");
   $from_dt=$_POST['from_date'];
   $to_date=$_POST['to_date'];
   $details=$_POST['details'];
 
   $appt_selected_fields=$_POST['selected_fields1'];
   $appt_selected_fields1=rtrim($appt_selected_fields," ");
   
    $sqlInsertQuerySet="INSERT INTO tbl_allcarereports_querysets
    (screen,setname,facility,from_date,to_date,details,selected_fields)
    VALUES('$screen','$querySetName', '$facility1_enc','$from_dt','$to_date','$details','$appt_selected_fields1')";
          
   $insertQuerySet=sqlStatement($sqlInsertQuerySet);
   
 } else if($screen=='enc_report') {
     
   $facility_encounter=$_POST['facility_encounter'];
   $facility_encounter1=rtrim($facility_encounter," ");
  
   $provider_encounter=$_POST['provider_encounter'];
   $provider_encounter1=rtrim($provider_encounter," ");
   
   $enc_from_dt=$_POST['enc_from_date'];
   $enc_to_dt=$_POST['enc_to_date'];
   $enc_new=$_POST['enc_new'];
   $enc_details=$_POST['enc_details'];
   
   $selected_fields=$_POST['enc_selected_fields1'];
   $selected_fields1=rtrim($selected_fields," ");
   
   $sqlInsertQuerySet="INSERT INTO tbl_allcarereports_querysets
    (screen,setname,facility,provider,from_date,to_date,details,new,selected_fields)
    VALUES('$screen','$querySetName', '$facility_encounter1','$provider_encounter1','$enc_from_dt','$enc_to_dt','$enc_new','$enc_details','$selected_fields1')";
          
   $insertQuerySet=sqlStatement($sqlInsertQuerySet);
 } 
 

//echo "Saved successfully";
//}

?>
