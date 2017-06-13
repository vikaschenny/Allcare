<!DOCTYPE html>
<html lang="en">
<?php
include_once("../../globals.php");
include_once("$srcdir/api.inc");

require ("allcare_C_FormROS_custom.class.php");

$encounter     = $_REQUEST['encounter'];
$pid1          = $_REQUEST['pid'];
$id            = $_REQUEST['id'];
$location      = $_REQUEST['location'];
$provider      = $_REQUEST['provider'];
$isSingleView  = $_REQUEST['isSingleView'];
$isFromCharts  = $_REQUEST['isFromCharts'];
if($location == 'provider_portal'){ 
    ?>
        <meta content="width=device-width,initial-scale=1.0" name="viewport">
        <style> .body_top { background-color: #F6F6F6 !important; } </style>
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
echo $c->default_action1($encounter,$pid1,$location,$id,$provider,$isSingleView,$isFromCharts);
?>
