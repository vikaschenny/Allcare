<?php
require_once("{$GLOBALS['srcdir']}/sql.inc");

function getAgencyById ($id, $cols = "*")
{
  return sqlQuery("select $cols from tbl_patientagency where id=? " .
    "order by agency_admitdate DESC ,agency_dischargedate DESC limit 0,1", array($id) );
}

function getAgencyByPid ($pid, $cols = "*")
{
  $res = sqlStatement("select $cols from tbl_patientagency where patientid=? " .
    "order by agency_admitdate DESC , agency_dischargedate DESC", array($pid) );
  for ($iter = 0; $row = sqlFetchArray($res); $iter++)
    $all[$iter] = $row;
  return $all;
}

/*function newAgency($pid, $fid, $admitdate, $dischargedate ,
  $isactive, $notes ,$related_links)
{
  $body = mysql_escape_string($body);
  return sqlInsert("insert into tbl_patientagency ( " .
    " agencyid, agency_admitdate, agency_dischargedate, agency_isactive, agency_notes,agency_related_links " .
    ") values ( " .
    "'$pid', '$fid', '$admitdate', '$dischargedate', '$isactive
    ', '$notes', '$related_links' " .
    ")");
}*/
function updateAgency($id, $new, $create)
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
    $sql = "INSERT INTO tbl_patientagency SET  created_date = NOW()";
    foreach ($new as $key => $value) {
      if ($key == 'id') continue;
      $sql .= ", `$key` = " . pdValueOrNull($key, $value);
    }
    $db_id = sqlInsert($sql);
      
  }
  else {
   //echo $db_id = $new['id'];
     $rez = sqlQuery("SELECT * FROM tbl_patientagency WHERE id = '$id'");
    $sql = "UPDATE tbl_patientagency SET updated_date = NOW()";
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