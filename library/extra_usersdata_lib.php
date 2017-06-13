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
function updateUserCred1ton($id, $new, $create)
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
    $sql = "INSERT INTO tbl_patientuser SET  created_date = NOW()";
    foreach ($new as $key => $value) {
      if ($key == 'id') continue;
      $sql .= ", `$key` = " . pdValueOrNull($key, $value);
    }
    $db_id = sqlInsert($sql);
      
  }
  else {
   //echo $db_id = $new['id'];
     $rez = sqlQuery("SELECT * FROM tbl_patientuser WHERE id = '$id'");
    $sql = "UPDATE tbl_patientuser SET updated_date = NOW()";
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

function updatePayroll1ton($id, $new, $create)
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
    $sql = "INSERT INTO tbl_userpayroll SET  created_date = NOW()";
    foreach ($new as $key => $value) {
      if ($key == 'id') continue;
      $sql .= ", `$key` = " . pdValueOrNull($key, $value);
    }
    $db_id = sqlInsert($sql);
      
  }
  else {
   //echo $db_id = $new['id'];
     $rez = sqlQuery("SELECT * FROM tbl_userpayroll WHERE id = '$id'");
    $sql = "UPDATE tbl_userpayroll SET updated_date = NOW()";
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
