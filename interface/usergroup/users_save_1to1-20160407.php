<?php
include_once("../../interface/globals.php");
include_once("$srcdir/patient.inc");
include_once("$srcdir/acl.inc");
include_once("$srcdir/options.inc.php");
include_once("$srcdir/formdata.inc.php");
require_once("$srcdir/users_custom_attr.inc.php");


// Update users custom attributes  
//
$newdata = array();
$newdata['tbl_user_custom_attr_1to1']['userid'] = $_POST['db_uid'];
$fres = sqlStatement("SELECT * FROM layout_options " .
  "WHERE form_id = 'UCA' AND uor > 0 AND field_id != '' " .
  "ORDER BY group_name, seq");
while ($frow = sqlFetchArray($fres)) {
  $data_type = $frow['data_type'];
  $field_id  = $frow['field_id'];
  // $value  = '';
  $colname = $field_id;
  $table = 'tbl_user_custom_attr_1to1';
  
  // if (isset($_POST["form_$field_id"])) $value = $_POST["form_$field_id"];
  $value = get_layout_form_value($frow);

  $newdata[$table][$colname] = $value;
}

//$id=$_POST['db_id'];
$uid=$_POST['db_uid'];
$sql=sqlStatement("select * from tbl_user_custom_attr_1to1 where userid='".$uid."'");
$rowpha=sqlFetchArray($sql);
if($uid==$rowpha['userid'])
{
    updateUserCustomAttr($uid, $newdata['tbl_user_custom_attr_1to1'] ,$create=false);
}
else
{
    updateUserCustomAttr($uid, $newdata['tbl_user_custom_attr_1to1'],$create=true);

}
  

if ($GLOBALS['concurrent_layout']) {
 include "users_dropdown_1to1.php" ;
} else {
 include_once("users_full_1to1.php");
}
?>
