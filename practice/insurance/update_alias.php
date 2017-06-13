<?php
require_once("../verify_session.php");
//echo "<pre>"; print_r($_POST);

 extract($_POST);
 
 if($type == 'all'){
     $qry = sqlStatement("SELECT `aliases` FROM `tbl_inscomp_custom_attr_1to1` WHERE `insuranceid` = '".$aliasid."'");
     $res = sqlFetchArray($qry);
     echo $res['aliases'];
     
 }
 else if($type == 'update'){
     $aliasnames = json_encode($aliasnames);
     sqlStatement("UPDATE `tbl_inscomp_custom_attr_1to1` SET `aliases` = '".$aliasnames."' WHERE `insuranceid` = '".$aliasid."'");
 }

?>