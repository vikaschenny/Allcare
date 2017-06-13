<?php
include_once("../../globals.php");
include_once("$srcdir/api.inc");

require ("allcare_C_FormROS_custom.class.php");

$encounter=$_REQUEST['encounter'];
$pid1=$_REQUEST['pid'];
$location=$_REQUEST['location'] ? $_REQUEST['location']:0;
$provider=$_REQUEST['provider'] ? $_REQUEST['provider']:0;
 //$id=$_REQUEST['id'];
 if($location=='provider_portal'){ ?>
<style> .body{ background-color: #F6F6F6 !important; } </style>
<?php }
$sql_pname=sqlStatement("select CONCAT(lname,' ',fname) AS pname from  patient_data  where   pid=$pid1");
                $res_row1=sqlFetchArray($sql_pname);
                echo "<b>Patient Name: </b>".$res_row1['pname']."<br>";
                echo "<b>Encounter: </b>".$encounter;
                
$c = new allcare_C_FormROS1();
echo $c->view_action1($_REQUEST['id'],$pid1,$location,$provider);
?>
