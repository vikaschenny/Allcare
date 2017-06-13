<?php
require_once("{$GLOBALS['srcdir']}/sql.inc");
require_once(dirname(__FILE__) . "/classes/WSWrapper.class.php");
require_once("{$GLOBALS['srcdir']}/formdata.inc.php");

function getUserCustomAttr($id , $given="*") {
    $sql = "select $given from tbl_user_custom_attr_1to1 where userid=?  limit 0,1";
    return sqlQuery($sql, array($id) );
}

function updateUserCustomAttr($id, $new, $create)
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
    $sql = "INSERT INTO tbl_user_custom_attr_1to1 SET  created_date = NOW()";
    foreach ($new as $key => $value) {
      if ($key == 'id') continue;
      $sql .= ", `$key` = " . pdValueOrNull($key, $value);
    }
    $db_id = sqlInsert($sql);
  }
  else {
   //echo $db_id = $new['id'];
    $rez = sqlQuery("SELECT * FROM tbl_user_custom_attr_1to1 WHERE userid = '$id'");
    $sql = "UPDATE tbl_user_custom_attr_1to1 SET updated_date = NOW()";
    foreach ($new as $key => $value) {
      $sql .= ", `$key` = " . pdValueOrNull($key, $value);
    }
    $sql .= " WHERE userid = '$id'";
    sqlStatement($sql);
  }

  //$rez = sqlQuery("SELECT * FROM tbl_form_pharmacy_custom_attributes WHERE pharmacyid = '$id'");
  //sync_patient($db_id,$rez['is_active'],$rez['address']);

  return $db_id;
}

?>