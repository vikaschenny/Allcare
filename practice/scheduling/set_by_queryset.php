<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once("verify-session.php");
require_once("../../library/formdata.inc.php");
require_once("../../library/globals.inc.php");

$querySetName=$_POST['querySetName'];
$app_enc=$_POST['app_enc'];

if($app_enc==1)
{
    $getQuerySet=sqlStatement("SELECT query_name,patient_standard_data,patient_custom_data,provider,payer,visit_category,active,deceased,selectVisitTp,display_mode,selectPatientsL,hsaddress,patshow,hsmap,practiceIfActive
                           FROM tbl_allcare_query_sets
                           WHERE set_name='$querySetName' AND app_enc='1'");
}
elseif($app_enc==2)
{
    $getQuerySet=sqlStatement("SELECT provider_encounter,date_from,date_to,visit_category_encounter,patient_standard_data,display_mode2,enchsaddress,encpatshow,hsmap
                           FROM tbl_allcare_query_sets
                           WHERE set_name='$querySetName' AND app_enc='2'");
}elseif($app_enc==3)
{
     $getQuerySet=sqlStatement("SELECT query_name,patient_standard_data,patient_custom_data,provider,payer,visit_category,active,deceased
                           FROM tbl_allcare_query_sets
                           WHERE set_name='$querySetName' AND app_enc='3'");
}elseif($app_enc==4)
{
    $getQuerySet=sqlStatement("SELECT provider_encounter,date_from,date_to,visit_category_encounter,patient_standard_data
                           FROM tbl_allcare_query_sets
                           WHERE set_name='$querySetName' AND app_enc='4'");
}

//if(sqlNumRows($getQueryStatement)==1)
//{

if(sqlNumRows($getQuerySet)==1)
{
   $rowQuerySet=sqlFetchArray($getQuerySet); 
   echo implode($rowQuerySet,'|');
}

//}

?>

