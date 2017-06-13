<?php
require_once("{$GLOBALS['srcdir']}/sql.inc");

function getPhaDatabyId ($id, $cols = "*")
{
  return sqlQuery("select $cols from tbl_patientpharmacy where id=? " .
    "order by id DESC ,pharmacyid DESC limit 0,1", array($id) );
}

function getPhaDatabyPid ($pharmacyid, $cols = "*")
{
  $res = sqlStatement("select $cols from tbl_patientpharmacy where pharmacyid=? " .
    "order by id DESC , pharmacyid DESC", array($pharmacyid) );
  
  for ($iter = 0; $row = sqlFetchArray($res); $iter++)
    $all[$iter] = $row;
  return $all;
}

/*function newPharmacy($pid, $fid, $admitdate, $dischargedate ,
  $isactive, $notes ,$related_links)
{
  $body = mysql_escape_string($body);
  return sqlInsert("insert into tbl_patientpharmacy ( pid, pharmacy, isActive, startDate, endDate )values ( '$pid', '$pharmacy', '$isActive', '$startDate', '$endDate' )");
}
*/

?>
