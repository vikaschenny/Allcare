<!DOCTYPE html>
<html lang="en">
<?php
include_once("../../globals.php");
include_once("$srcdir/api.inc");

require ("allcare_C_FormROS_custom.class.php");

$encounter      = $_REQUEST['encounter'];
$pid1           = $_REQUEST['pid'];
$location       = $_REQUEST['location'] ? $_REQUEST['location']:0;
$provider       = $_REQUEST['provider'] ? $_REQUEST['provider']:0;
$isSingleView   = $_REQUEST['isSingleView'];
$isFromCharts   = $_REQUEST['isFromCharts'];
 //$id=$_REQUEST['id'];
 if($location == 'provider_portal'){ 
     ?>
        <meta content="width=device-width,initial-scale=1.0" name="viewport">
        <style> .body{ background-color: #F6F6F6 !important; } </style>
        <link rel="stylesheet" href="../../tableresponsive/viewcustom_responsive.css"/>
    <?php 
}
$sql_pname=sqlStatement("select CONCAT(lname,' ',fname) AS pname from  patient_data  where   pid=$pid1");
$res_row1=sqlFetchArray($sql_pname);

$dos_sql=sqlStatement("select * from form_encounter where encounter=$encounter");
$res_dos=sqlFetchArray($dos_sql);
$dos=explode(" ",$res_dos['date']);

$cat=sqlStatement("select * from openemr_postcalendar_categories where pc_catid='".$res_dos['pc_catid']."'");
$res_cat=sqlFetchArray($cat);
echo "<table>";
echo "<tr><td><b>Patient Name: </b>".$res_row1['pname']."</td><td>&nbsp;</td><td><b>Encounter: </b>".$encounter."</td></tr>";
echo "<tr><td><b>Date Of Service: </b>".$dos[0]."</td><td>&nbsp;</td><td><b>Visit Category: </b>".$res_cat['pc_catname']."</td></tr>";
echo "</table>";

$c = new allcare_C_FormROS1();
echo $c->view_action1($_REQUEST['id'],$pid1,$location,$provider,$encounter,$isSingleView,$isFromCharts);
?>
