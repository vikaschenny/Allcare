<?php
include_once("../../globals.php");
include_once("$srcdir/api.inc");

require ("C_FormVitals_custom.class.php");


$encounter=$_REQUEST['encounter'];
$pid1=$_REQUEST['pid'];
$id=$_REQUEST['id'];
$provider=$_REQUEST['provider'];
$location=$_REQUEST['location'];
$sql_pname=sqlStatement("select CONCAT(lname,' ',fname) AS pname from  patient_data  where   pid=$pid1");
                $res_row1=sqlFetchArray($sql_pname);
                echo "<b>Patient Name: </b>".$res_row1['pname']."<br>";
                echo "<b>Encounter: </b>".$encounter;
$c = new C_FormVitals_custom();
#echo $c->view_action(0);
echo $c->default_action_custom(0,$pid1,$encounter,$provider,$location);
?>
