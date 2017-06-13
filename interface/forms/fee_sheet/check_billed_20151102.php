<?php
$ignoreAuth=true; // ignore the standard authentication for a regular OpenEMR user

include_once('../../globals.php');
 
include_once("$srcdir/api.inc");
require_once("$srcdir/patient.inc");
require_once("$srcdir/options.inc.php");
require_once("$srcdir/htmlspecialchars.inc.php");
include_once("$srcdir/acl.inc");

$encounterid = $_REQUEST['encounterid'];
$pid   = $_REQUEST['pid'];

$query = "SELECT billed FROM billing WHERE encounter = $encounterid AND pid = $pid";
$getquery = sqlStatement($query);
$array = array();
$array['billed'] = 0;
while($setquery = sqlFetchArray($getquery)){
    $array['billed'] = $setquery['billed'];
}
echo json_encode($array);
?>