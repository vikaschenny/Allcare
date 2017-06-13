<?php
$ignoreAuth=true; // ignore the standard authentication for a regular OpenEMR user

include_once('../../interface/globals.php');
 
include_once("$srcdir/api.inc");
require_once("$srcdir/patient.inc");
require_once("$srcdir/options.inc.php");
require_once("$srcdir/htmlspecialchars.inc.php");
include_once("$srcdir/acl.inc");

$key   = $_REQUEST['key'];
$query="SELECT group_name,field_id FROM layout_options WHERE form_id='DEM' AND field_id = '$key'";

$getquery = sqlStatement($query);
$array = array();
while($setquery = sqlFetchArray($getquery)){
    $array[$setquery['field_id']] = $setquery['group_name'];
}
echo json_encode($array);
?>