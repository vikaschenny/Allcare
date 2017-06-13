<?php
include_once("../../globals.php");
include_once("$srcdir/api.inc");

require ("allcare_C_FormROS_custom.class.php");

 $encounter=$_REQUEST['encounter'];
 $pid1=$_REQUEST['pid'];
 $id=$_REQUEST['id'];
 $location=$_REQUEST['location'];
 $provider=$_REQUEST['provider'];
 if($location=='provider_portal'){ ?>
<style> .body_top { background-color: #F6F6F6 !important; } </style>
<?php }
$sql_pname=sqlStatement("select CONCAT(lname,' ',fname) AS pname from  patient_data  where   pid=$pid1");
                $res_row1=sqlFetchArray($sql_pname);
                echo "<b>Patient Name: </b>".$res_row1['pname']."<br>";
                echo "<b>Encounter: </b>".$encounter;
$c = new allcare_C_FormROS1();
echo $c->default_action1($encounter,$pid1,$location,$id,$provider);
?>
