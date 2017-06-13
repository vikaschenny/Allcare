<?php
include_once("../../interface/globals.php");
include_once("$srcdir/patient.inc");
include_once("$srcdir/acl.inc");
include_once("$srcdir/options.inc.php");
include_once("$srcdir/formdata.inc.php");
require_once("$srcdir/pharmacy.inc.php");


// Update patient_data and employer_data:
//
$newdata = array();
$newdata['tbl_pharmacy_custom_attributes_1to1']['pharmacyid'] = $_POST['db_phid'];
$fres = sqlStatement("SELECT * FROM layout_options " .
  "WHERE form_id = 'PCA' AND uor > 0 AND field_id != '' " .
  "ORDER BY group_name, seq");
while ($frow = sqlFetchArray($fres)) {
  $data_type = $frow['data_type'];
  $field_id  = $frow['field_id'];
  // $value  = '';
  $colname = $field_id;
  $table = 'tbl_pharmacy_custom_attributes_1to1';
  
  // if (isset($_POST["form_$field_id"])) $value = $_POST["form_$field_id"];
  $value = get_layout_form_value($frow);

  $newdata[$table][$colname] = $value;
}

//$id=$_POST['db_id'];
$phid=$_POST['db_phid'];
$sql=sqlStatement("select * from tbl_pharmacy_custom_attributes_1to1 where pharmacyid='".$phid."'");
$rowpha=sqlFetchArray($sql);
if($phid==$rowpha['pharmacyid'])
{
    updatePharmacy($phid, $newdata['tbl_pharmacy_custom_attributes_1to1'] ,$create=false);
}
else
{
    updatePharmacy($phid, $newdata['tbl_pharmacy_custom_attributes_1to1'],$create=true);

}
  

if ($GLOBALS['concurrent_layout']) {
 include "pharmacy_dropdown_1to1.php" ;
} else {
 include_once("pharmacy_full_1to1.php");
}
?>
