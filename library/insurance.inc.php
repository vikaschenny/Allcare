<?php
require_once("{$GLOBALS['srcdir']}/sql.inc");
require_once(dirname(__FILE__) . "/classes/WSWrapper.class.php");
require_once("{$GLOBALS['srcdir']}/formdata.inc.php");

function getInsurance($id , $given="*") {
    $sql = "select $given from tbl_inscomp_custom_attr_1to1 where insuranceid=?  limit 0,1";
    return sqlQuery($sql, array($id) );
}

// -- Subhan code starts --

function getPlans($id , $given="*") {
    $sql = "select $given from tbl_patientinsurancecompany where id=?  limit 0,1";
    return sqlQuery($sql, array($id) );
}

function getBenefits($id , $given="*") {
    $sql = "select $given from tbl_inscomp_benefits where id=?  limit 0,1";
    return sqlQuery($sql, array($id) );
}

// -- Subhan code ends--

function updateInsurance($id, $new, $create)
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
    $sql = "INSERT INTO tbl_inscomp_custom_attr_1to1 SET  created_date = NOW()";
    foreach ($new as $key => $value) {
      if ($key == 'id') continue;
      $sql .= ", `$key` = " . pdValueOrNull($key, $value);
    }
    $db_id = sqlInsert($sql);
  }
  else {
   //echo $db_id = $new['id'];
    $rez = sqlQuery("SELECT * FROM tbl_inscomp_custom_attr_1to1 WHERE insuranceid = '$id'");
    $sql = "UPDATE tbl_inscomp_custom_attr_1to1 SET updated_date = NOW()";
    foreach ($new as $key => $value) {
      $sql .= ", `$key` = " . pdValueOrNull($key, $value);
    }
    $sql .= " WHERE insuranceid = '$id'";
    sqlStatement($sql);
  }

  //$rez = sqlQuery("SELECT * FROM tbl_form_pharmacy_custom_attributes WHERE pharmacyid = '$id'");
  //sync_patient($db_id,$rez['is_active'],$rez['address']);

  return $db_id;
}

// -- Subhan code starts --

function updatePlans($id, $new, $create)
{
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

function updateBenefits($id, $new, $create)
{
  if ($create) {
    $sql = "INSERT INTO tbl_inscomp_benefits SET  created_date = NOW()";
    foreach ($new as $key => $value) {
      if ($key == 'id') continue;
      $sql .= ", `$key` = " . pdValueOrNull($key,  mysql_escape_string($value));
    }
    $db_id = sqlInsert($sql);
  }
  else {
   //echo $db_id = $new['id'];
    $rez = sqlQuery("SELECT * FROM tbl_inscomp_benefits WHERE id = '$id'");
    $sql = "UPDATE tbl_inscomp_benefits SET updated_date = NOW()";
    foreach ($new as $key => $value) {
      $sql .= ", `$key` = " . pdValueOrNull($key,  mysql_escape_string($value));
    }
    $sql .= " WHERE id = '$id'";
    sqlStatement($sql);
  }

  //$rez = sqlQuery("SELECT * FROM tbl_form_pharmacy_custom_attributes WHERE pharmacyid = '$id'");
  //sync_patient($db_id,$rez['is_active'],$rez['address']);

  return $db_id;
}

// -- Subhan code ends --

?>