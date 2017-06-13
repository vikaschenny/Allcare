<?php
require_once("{$GLOBALS['srcdir']}/sql.inc");
require_once(dirname(__FILE__) . "/classes/WSWrapper.class.php");
require_once("{$GLOBALS['srcdir']}/formdata.inc.php");

function getInsDatabyId ($id, $cols = "*")
{  
  $sql=sqlQuery("select $cols from tbl_patientinsurancecompany WHERE id=? " .
    "order by id DESC ,insuranceid DESC limit 0,1", array($id) );
 
  /*return sqlQuery("select $cols from tbl_patientinsurancecompany WHERE id=? " .
    "order by id DESC ,insuranceid DESC limit 0,1", array($id) );*/
  return $sql;
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
   $sql= sqlQuery("select $cols from tbl_patientprovidercredentials WHERE id=? " .
    "order by id DESC ,insuranceid DESC limit 0,1", array($id) );
  /*return sqlQuery("select $cols from tbl_patientprovidercredentials WHERE id=? " .
    "order by id DESC ,insuranceid DESC limit 0,1", array($id) );*/
   return $sql;
}

function getProvDatabyPid ($insuranceid, $cols = "*")
{
  $res = sqlStatement("select $cols from tbl_patientprovidercredentials WHERE insuranceid=? " .
    "order by id DESC , insuranceid DESC", array($insuranceid) );
  for ($iter = 0; $row = sqlFetchArray($res); $iter++)
    $all[$iter] = $row;
  return $all;
}
function updateInsurance1ton($id, $new, $create)
{
  /*******************************************************************
    $real = getPatientData($pid);
    $new['DOB'] = fixDate($new['DOB']);
    while(list($key, $value) = each ($new))
        $real[$key] = $value;
    $real['date'] = "'+NOW()+'";
    $real['id'] = "";
    $sql = "insert into patient_data set ";
    while(list($key, $value) = each($real))
        $sql .= $key." = '$value', ";
    $sql = substr($sql, 0, -2);
    return sqlInsert($sql);
  *******************************************************************/

  // The above was broken, though seems intent to insert a new patient_data
  // row for each update.  A good idea, but nothing is doing that yet so
  // the code below does not yet attempt it.

  //$new['DOB'] = fixDate($new['DOB']);
 
  if ($create) {
    $sql = "INSERT INTO tbl_patientinsurancecompany SET  created_date = NOW()";
    foreach ($new as $key => $value) {
      if ($key == 'id') continue;
      $sql .= ", `$key` = " . pdValueOrNull($key, $value);
    }
    $db_id = sqlInsert($sql);
  }
  else {
   //echo $db_id = $new['id'];
    $rez = sqlQuery("SELECT * FROM tbl_patientinsurancecompany WHERE id = '$id'");
    $sql = "UPDATE tbl_patientinsurancecompany SET updated_date = NOW()";
    foreach ($new as $key => $value) {
      $sql .= ", `$key` = " . pdValueOrNull($key, $value);
    }
    $sql .= " WHERE id = '$id'";
    sqlStatement($sql);
  }

  //$rez = sqlQuery("SELECT * FROM tbl_form_pharmacy_custom_attributes WHERE pharmacyid = '$id'");
  //sync_patient($db_id,$rez['is_active'],$rez['address']);

  return $db_id;
}
function updateProvcred1ton($id, $new, $create)
{
  /*******************************************************************
    $real = getPatientData($pid);
    $new['DOB'] = fixDate($new['DOB']);
    while(list($key, $value) = each ($new))
        $real[$key] = $value;
    $real['date'] = "'+NOW()+'";
    $real['id'] = "";
    $sql = "insert into patient_data set ";
    while(list($key, $value) = each($real))
        $sql .= $key." = '$value', ";
    $sql = substr($sql, 0, -2);
    return sqlInsert($sql);
  *******************************************************************/

  // The above was broken, though seems intent to insert a new patient_data
  // row for each update.  A good idea, but nothing is doing that yet so
  // the code below does not yet attempt it.

  //$new['DOB'] = fixDate($new['DOB']);
  
  if ($create) {
    $sql = "INSERT INTO tbl_patientprovidercredentials SET  created_date = NOW()";
    foreach ($new as $key => $value) {
      if ($key == 'id') continue;
      $sql .= ", `$key` = " . pdValueOrNull($key, $value);
    }
    $db_id = sqlInsert($sql);
      
  }
  else {
   //echo $db_id = $new['id'];
     $rez = sqlQuery("SELECT * FROM tbl_patientprovidercredentials WHERE id = '$id'");
    $sql = "UPDATE tbl_patientprovidercredentials SET updated_date = NOW()";
    foreach ($new as $key => $value) {
      $sql .= ", `$key` = " . pdValueOrNull($key, $value);
    }
    $sql .= " WHERE id = '$id'";
    sqlStatement($sql);
  }

  //$rez = sqlQuery("SELECT * FROM tbl_form_pharmacy_custom_attributes WHERE pharmacyid = '$id'");
  //sync_patient($db_id,$rez['is_active'],$rez['address']);
  
  return $db_id;
}
?>