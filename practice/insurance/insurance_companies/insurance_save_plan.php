<?php

//echo "<pre>"; print_r($_POST); exit;

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

$newdata['tbl_patientinsurancecompany']['insuranceid'] = $_POST['insid'];
$fres = sqlStatement("SELECT * FROM layout_options " .
  "WHERE form_id = 'INSUCOMP' AND uor > 0 AND field_id != '' " .
  "ORDER BY group_name, seq");
while ($frow = sqlFetchArray($fres)) {
  $data_type = $frow['data_type'];
  $field_id  = $frow['field_id'];
  // $value  = ''; 
  $colname = $field_id;
  $table = 'tbl_patientinsurancecompany';
  
  // if (isset($_POST["form_$field_id"])) $value = $_POST["form_$field_id"];
  $value = get_layout_form_value($frow);

  $newdata[$table][$colname] = $value;

}

//$id=$_POST['db_id'];
$id=$_POST['formid'];
//$sql=sqlStatement("select * from tbl_patientinsurancecompany where id='".$id."'");
//$rowpha=sqlFetchArray($sql);
if($_POST['formaction']=='add')
{
   updatePlans($id, $newdata['tbl_patientinsurancecompany'] ,$create=true);
   $sql2   = sqlStatement("SELECT * from `tbl_patientinsurancecompany` WHERE `id` = '".$id."'");
   $planRes = sqlFetchArray($sql2);
   echo json_encode($planRes);
}
else
{
    //echo "testsubhan<pre>"; print_r($newdata['tbl_patientinsurancecompany']); exit;
    updatePlans($id, $newdata['tbl_patientinsurancecompany'],$create=false);
    
    $sql2   = sqlStatement("SELECT * from `tbl_patientinsurancecompany` WHERE `id` = '".$id."'");
    $planRes = sqlFetchArray($sql2);
    
    $instype = sqlStatement("SELECT `title` from `list_options` WHERE `list_id` = 'Payer_Types' AND `option_id` = '".$planRes['insurance_type']."'");
    $instypeRes = sqlFetchArray($instype);
    
    $planRes['ins_type'] = $planRes['insurance_type'];
    $planRes['insurance_type'] = $instypeRes['title'];
    
    echo json_encode($planRes);

}
?>

<script>parent.hidemodal('<?php echo json_encode($planRes) ?>',"plans");</script>