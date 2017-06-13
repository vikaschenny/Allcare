<?php
require_once("{$GLOBALS['srcdir']}/sql.inc");
require_once(dirname(__FILE__) . "/classes/WSWrapper.class.php");
require_once("{$GLOBALS['srcdir']}/formdata.inc.php");

function getFacilityCustomAttr($id , $given="*") {
    $sql = "select $given from tbl_facility_custom_attr_1to1 where facilityid=?  limit 0,1";
    return sqlQuery($sql, array($id) );
}

function updateFacilityCustomAttr($id, $new, $create)
{
   if ($create) {
    $sql = "INSERT INTO tbl_facility_custom_attr_1to1 SET  created_date = NOW()";
    foreach ($new as $key => $value) {
      if ($key == 'id') continue;
      $sql .= ", `$key` = " . pdValueOrNull($key, $value);
    }
    $db_id = sqlInsert($sql);
  }
  else {
   //echo $db_id = $new['id'];
    $rez = sqlQuery("SELECT * FROM tbl_facility_custom_attr_1to1 WHERE facilityid = '$id'");
    $sql = "UPDATE tbl_facility_custom_attr_1to1 SET updated_date = NOW()";
    foreach ($new as $key => $value) {
      $sql .= ", `$key` = " . pdValueOrNull($key, $value);
    }
    $sql .= " WHERE facilityid = '$id'";
    sqlStatement($sql);
  }


  return $db_id;
}

?>