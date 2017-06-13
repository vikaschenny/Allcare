<?php
require_once("{$GLOBALS['srcdir']}/sql.inc");

function getInsDatabyId ($id, $cols = "*")
{
  return sqlQuery("select $cols from tbl_patientinsurancecompany WHERE id=? " .
    "order by id DESC ,insuranceid DESC limit 0,1", array($id) );
}

function getInsDatabyPid ($insuranceid, $cols = "*")
{
  $res = sqlStatement("select $cols from tbl_patientinsurancecompany WHERE insuranceid=? " .
    "order by id DESC , insuranceid DESC", array($insuranceid) );
  for ($iter = 0; $row = sqlFetchArray($res); $iter++)
    $all[$iter] = $row;
  return $all;
}

function getProvDatabyId ($id, $cols = "*")
{
  return sqlQuery("select $cols from tbl_patientprovidercredentials WHERE id=? " .
    "order by id DESC ,insuranceid DESC limit 0,1", array($id) );
}

function getProvDatabyPid ($insuranceid, $cols = "*")
{
  $res = sqlStatement("select $cols from tbl_patientprovidercredentials WHERE insuranceid=? " .
    "order by id DESC , insuranceid DESC", array($insuranceid) );
  for ($iter = 0; $row = sqlFetchArray($res); $iter++)
    $all[$iter] = $row;
  return $all;
}
?>