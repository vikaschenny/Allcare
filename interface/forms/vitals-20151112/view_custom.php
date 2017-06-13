<!DOCTYPE html>
<html lang="en">
    <head>
        <meta content="width=device-width,initial-scale=1.0" name="viewport">
    </head>
<?php
include_once("../../globals.php");
include_once("$srcdir/api.inc");

require ("C_FormVitals_custom.class.php");


$encounter=$_REQUEST['encounter'];
$pid1=$_REQUEST['pid'];
$id=$_REQUEST['id'];
$provider=$_REQUEST['provider'] ? $_REQUEST['provider'] :0;
$location=$_REQUEST['location'] ? $_REQUEST['provider'] :0;
$stat=explode("|",$_REQUEST['status']);

if(in_array('pending',$stat)){
$pending='pending';
}
if(in_array('finalized',$stat)){
 $finalized='finalized';    
}
$sql_pname=sqlStatement("select CONCAT(lname,' ',fname) AS pname from  patient_data  where   pid=$pid1");
                $res_row1=sqlFetchArray($sql_pname);
                echo "<b>Patient Name: </b>".$res_row1['pname']."<br>";
                echo "<b>Encounter: </b>".$encounter;
                
                
$c = new C_FormVitals_custom();
echo $c->default_action_custom($id,$pid1,$encounter,$provider,$location,$pending,$finalized);
?>

