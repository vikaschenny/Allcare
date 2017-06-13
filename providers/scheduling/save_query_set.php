<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once("verify-session.php");
require_once("../../library/formdata.inc.php");
require_once("../../library/globals.inc.php");


$app_enc=$_POST['app_enc'];

$querySetName=$_POST['querySetName'];

/*        Patients due for Appointments     */
$queryName=$_POST['queryName'];
$patientStandardData=implode($_POST['patientStandardData'],',');
$patientCustomData=implode($_POST['patientCustomData'],',');

$providerId=$_POST['providerId'];
$payerId=$_POST['payerId'];
$allProviders=$_POST['allProviders'];
$allPayers=$_POST['allPayers'];

$visitCategoryId=$_POST['visitCategoryId'];
$allVisitCategories=$_POST['allVisitCategories'];
$activeVal=$_POST['active'];
$practiceIfActiveVal = $_POST['practiceIfActive'];
$deceasedVal=$_POST['deceased'];
$selectVisitTp=$_POST['selectVisitTp'];
$displayMode = $_POST['displayMode'];
$displayMode2 = $_POST['displayMode2'];

$selectPatientsL = $_POST['selectPatientsL'];
$addrvalshow = $_POST['addrvalshow'];
$patshow = $_POST['patshow'];
$encaddrvalshow = $_POST['encaddrvalshow'];
$encpatshow = $_POST['encpatshow'];
$hsmap = $_POST['hsmap'];

$provider='';
if(in_array('-2', $_POST['providerId'], true))
{
   //$provider = $allProviders;
   $provider = 'All';
}
else
{
   $provider=implode($providerId,',');
}


$payer='';
if(in_array('-2', $_POST['payerId'], true))
{
   //$payer = $allPayers;
    $payer = 'All';
}
else
{
   $payer=implode($payerId,',');
}

if(in_array('-2', $_POST['visitCategoryId'], true))
{
   //$payer = $allPayers;
   $visitCategory = 'All';
}
else
{
   $visitCategory=implode($visitCategoryId,',');
}

$active = "";
if(in_array('-2', $_POST['active'], true))
{
   //$payer = $allPayers;
   $active = 'All';
}
else
{
   $active=implode($activeVal,',');
}

$practiceIfActive = "";
if(in_array('-2', $_POST['practiceIfActive'], true))
{
   //$payer = $allPayers;
   $practiceIfActive = 'All';
}
else
{
   $practiceIfActive=implode($practiceIfActiveVal,',');
}

$deceased = "";
if(in_array('-2', $_POST['deceased'], true))
{
   //$payer = $allPayers;
   $deceased = 'All';
}
else
{
   $deceased=implode($deceasedVal,',');
}

$getQueryStatement=sqlStatement("SELECT querystring FROM tbl_allcare_query WHERE name='$queryName'");

//if(sqlNumRows($getQueryStatement)==1)
//{

$rowQuery=sqlFetchArray($getQueryStatement);

$queryStatement=$rowQuery['querystring'];
$queryStatement=str_replace("'","\'",$queryStatement);

/*********************************************/

/*        Patients due for Encounters     */

$providerIdEncounter=$_POST['providerIdEncounter'];
$allProvidersEncounter=$_POST['allProvidersEncounter'];
$date_from=$_POST['date_from'];
$date_to=$_POST['date_to'];
$visitCategoryIdEncounter=$_POST['visitCategoryIdEncounter'];
$allVisitCategoriesEncounter=$_POST['allVisitCategoriesEncounter'];
$allSelectedFields = $_POST['allSelectedFields'];
$providerEncounter='';
if(in_array('-2', $_POST['providerIdEncounter'], true))
{
   $providerEncounter = 'All';
}
else
{
   $providerEncounter=implode($providerIdEncounter,',');
}

if(in_array('-2', $_POST['visitCategoryIdEncounter'], true))
{
   $visitCategoryEncounter = 'All';
}
else
{
   $visitCategoryEncounter=implode($visitCategoryIdEncounter,',');
}
$patientfields='';
if(in_array('-2', $_POST['allSelectedFields'], true))
{
   $patientfields = '';
}
else
{
   $patientfields=implode($allSelectedFields,',');
}
/************************************************/

$sqlInsertQuerySet=''; 
if($app_enc==1 || $app_enc==3)
{
    $sqlInsertQuerySet="INSERT INTO tbl_allcare_query_sets
    (app_enc,set_name,query_name,query_statement,patient_standard_data,patient_custom_data,provider,payer,visit_category,active,deceased,selectVisitTp,display_mode,selectPatientsL,hsaddress,patshow,hsmap,practiceIfActive)
    VALUES('$app_enc','$querySetName','$queryName','$queryStatement',
           '($patientStandardData)','($patientCustomData)','($provider)','($payer)','($visitCategory)','($active)','($deceased)','$selectVisitTp','$displayMode','$selectPatientsL','$addrvalshow','$patshow','$hsmap','($practiceIfActive)')";

}
else if($app_enc==2 || $app_enc==4)
{
    $sqlInsertQuerySet="INSERT INTO tbl_allcare_query_sets
    (app_enc,set_name,provider_encounter,date_from,date_to,visit_category_encounter,patient_standard_data,selectVisitTp,display_mode2,enchsaddress,encpatshow,hsmap,practiceIfActive)
    VALUES('$app_enc','$querySetName','($providerEncounter)','$date_from','$date_to',
           '($visitCategoryEncounter)', '($patientfields)','$selectVisitTp','$displayMode2','$encaddrvalshow','$encpatshow','$hsmap','($practiceIfActive)')";
}

$insertQuerySet=sqlStatement($sqlInsertQuerySet);

//echo "Saved successfully";
//}

?>
