<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once("../../globals.php");
require_once("../../../library/formdata.inc.php");
require_once("../../../library/globals.inc.php");

 $querySetName=$_POST['querySetName'];
 $screen=$_POST['screen'];

if($screen=='appt'){
     $getQuerySet=sqlStatement("SELECT facility,provider,status,categories,from_date,to_date,available_slots,
                           without_provider,wihout_facility,selected_fields
                           FROM tbl_allcarereports_querysets
                           WHERE setname='$querySetName' AND screen='$screen'");
}
else if($screen=='appt_enc_report') {
    
    $getQuerySet=sqlStatement("SELECT facility,from_date,to_date,details,selected_fields,visit_type,patient_list,due_appt,rend_provider,appt_stat,practitioner
                           FROM tbl_allcarereports_querysets
                           WHERE setname='$querySetName' AND screen='$screen'");
}else if($screen=='enc_report') {
    $getQuerySet=sqlStatement("SELECT facility,provider,from_date,to_date,details,new,selected_fields
                           FROM tbl_allcarereports_querysets
                           WHERE setname='$querySetName' AND screen='$screen'");
}

if(sqlNumRows($getQuerySet)==1)
{
   $rowQuerySet=sqlFetchArray($getQuerySet); 
   echo implode($rowQuerySet,'|');
}

//}

?>

