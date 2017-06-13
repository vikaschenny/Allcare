<?php

//STOP FAKE REGISTER GLOBALS
$fake_register_globals=false;
//continue session
session_start();

$landingpage = "../../../index.php?site=".$_SESSION['site_id'];

if ( isset($_SESSION['portal_username']) ) {    
    $portal_user = $_SESSION['portal_username']; 
}else {
    session_destroy();
    header('Location: '.$landingpage.'&w');
    exit;
}

$ignoreAuth=true; // ignore the standard authentication for a regular OpenEMR user

include_once("../../../interface/globals.php");

//echo "<pre>"; print_r($_POST); exit;
include_once("$srcdir/patient.inc");
include_once("$srcdir/acl.inc");
include_once("$srcdir/options.inc.php");
include_once("$srcdir/formdata.inc.php");
require_once("$srcdir/insurance.inc.php");

?>
<?php
// Update insurance companies custom attributes
//
$newdata = array();

$newdata['tbl_inscomp_custom_attr_1to1']['insuranceid'] = $_POST['formid'];
$fres = sqlStatement("SELECT * FROM layout_options " .
  "WHERE form_id = 'INSCA' AND uor > 0 AND field_id != '' " .
  "ORDER BY group_name, seq");
while ($frow = sqlFetchArray($fres)) {
  $data_type = $frow['data_type'];
  $field_id  = $frow['field_id'];
  // $value  = ''; 
  $colname = $field_id;
  $table = 'tbl_inscomp_custom_attr_1to1';
  
  // if (isset($_POST["form_$field_id"])) $value = $_POST["form_$field_id"];
  $value = get_layout_form_value($frow);

  $newdata[$table][$colname] = $value;

}

//$id=$_POST['db_id'];
$insid=$_POST['formid'];
$sql=sqlStatement("select * from tbl_inscomp_custom_attr_1to1 where insuranceid='".$insid."'");
$rowpha=sqlFetchArray($sql);
if($insid==$rowpha['insuranceid'])
{
   updateInsurance($insid, $newdata['tbl_inscomp_custom_attr_1to1'] ,$create=false);
}
else
{
    updateInsurance($insid, $newdata['tbl_inscomp_custom_attr_1to1'],$create=true);
}
?>
