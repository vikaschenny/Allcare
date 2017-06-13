<?php
require_once("{$GLOBALS['srcdir']}/sql.inc");

function getAbookDatabyId ($id, $cols = "*")
{ 
    return sqlQuery("select $cols from tbl_user_abookcontact where id=$id" );
}


function getAbookDatabyUserid ($userid, $cols = "*")
{
  $res = sqlStatement("select $cols from tbl_user_abookcontact where userid=? " .
    "order by id DESC , userid DESC", array($userid) );
  for ($iter = 0; $row = sqlFetchArray($res); $iter++)
    $all[$iter] = $row;
  return $all;
}

function getAbookCredDatabyId ($id, $cols = "*")
{ 
    return sqlQuery("select $cols from tbl_user_cred where id=$id" );
}


function getAbookCredDatabyUserid ($userid, $cols = "*")
{
  $res = sqlStatement("select $cols from tbl_user_cred where userid=? " .
    "order by id DESC , userid DESC", array($userid) );
  for ($iter = 0; $row = sqlFetchArray($res); $iter++)
    $all[$iter] = $row;
  return $all;
}