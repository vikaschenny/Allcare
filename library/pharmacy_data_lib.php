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
function updatePharmacy1ton($id, $new, $create)
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
    $sql = "INSERT INTO tbl_patientpharmacy SET  created_date = NOW()";
    foreach ($new as $key => $value) {
      if ($key == 'id') continue;
      $sql .= ", `$key` = " . pdValueOrNull($key, $value);
    }
    $db_id = sqlInsert($sql);
  }
  else {
   //echo $db_id = $new['id'];
    $rez = sqlQuery("SELECT * FROM tbl_patientpharmacy WHERE id = '$id'");
    $sql = "UPDATE tbl_patientpharmacy SET updated_date = NOW()";
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
