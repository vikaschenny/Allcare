<?php
require_once("verify_session.php");

$uniqueid = $_POST['uniqueid'];
$mainPayers = [];

$sql2   = sqlStatement("SELECT * from `payers_list` WHERE `id` = '".$uniqueid."'");
$row2  = sqlFetchArray($sql2);
unset($row2['id']);
//echo "<pre>"; print_r($row2);
$mainPayers['zirmed'] = $row2;

$sql1   = sqlStatement("SELECT ic.* from `insurance_companies` as ic, `tbl_inscomp_custom_attr_1to1` as ca WHERE ca.`uniqueid` = '".$uniqueid."' AND ca.`insuranceid` = ic.`id`");
$row1 = sqlFetchArray($sql1);
unset($row1['id']);
//echo "<pre>"; print_r($row1);
$mainPayers['central'] = $row1;

$sql3   = sqlStatement("SELECT ca.* from `insurance_companies` as ic, `tbl_inscomp_custom_attr_1to1` as ca WHERE ca.`uniqueid` = '".$uniqueid."' AND ca.`insuranceid` = ic.`id`");
$row3 = sqlFetchArray($sql3);

$row3['alias'] = json_decode($row3['aliases']);

unset($row3['id']);
unset($row3['insuranceid']);
unset($row3['created_date']);
unset($row3['updated_date']);
unset($row3['aliases']);
//echo "<pre>"; print_r($row3);
$mainPayers['custom'] = $row3;

echo json_encode($mainPayers);
?>