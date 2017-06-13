<?php
require_once("{$GLOBALS['srcdir']}/sql.inc");

function getUserDatabyId ($id, $cols = "*")
{
  return sqlQuery("select $cols from tbl_patientuser where id=? " .
    "order by id DESC ", array($id) );
}

function getUserDatabyUserid ($userid, $cols = "*")
{
  $res = sqlStatement("select $cols from tbl_patientuser where userid=? " .
    "order by id DESC , userid DESC", array($userid) );
  for ($iter = 0; $row = sqlFetchArray($res); $iter++)
    $all[$iter] = $row;
  return $all;
}

function getPayrollDatabyId ($id, $cols = "*")
{
  return sqlQuery("select $cols from tbl_userpayroll where id=? " .
    "order by id DESC ", array($id) );
}

function getPayrollDatabyUserid ($userid, $cols = "*")
{
  $res = sqlStatement("select $cols from tbl_userpayroll where userid=? " .
    "order by id DESC , userid DESC", array($userid) );
  for ($iter = 0; $row = sqlFetchArray($res); $iter++)
    $all[$iter] = $row;
  return $all;
}


?>
