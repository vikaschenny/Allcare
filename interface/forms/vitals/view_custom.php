<?php
include_once("../../globals.php");
include_once("$srcdir/api.inc");

require ("C_FormVitals_custom.class.php");

 
$encounter      = $_REQUEST['encounter'];
$pid1           = $_REQUEST['pid'];
$id             = $_REQUEST['id'];
$provider       = $_REQUEST['provider'] ? $_REQUEST['provider'] :0;
$location       = $_REQUEST['location'] ? $_REQUEST['provider'] :0;
$isSingleView   = $_REQUEST['isSingleView'];
$isFromCharts   = $_REQUEST['isFromCharts'];
$stat           = explode("|",$_REQUEST['status']);

if(in_array('pending',$stat)){
    $pending='pending';
}
if(in_array('finalized',$stat)){
    $finalized='finalized';    
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
                
$c = new C_FormVitals_custom();
echo $c->default_action_custom($id,$pid1,$encounter,$provider,$location,$pending,$finalized,$isSingleView,$isFromCharts);
?>

