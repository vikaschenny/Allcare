<?php
$ignoreAuth=true; // ignore the standard authentication for a regular OpenEMR user

require_once("../../globals.php");
 
include_once("$srcdir/api.inc");
require_once("$srcdir/patient.inc");
require_once("$srcdir/options.inc.php");
require_once("$srcdir/htmlspecialchars.inc.php");
include_once("$srcdir/acl.inc");

$deletestring   = $_REQUEST['deletestring'];
$pid   = $_REQUEST['pid'];
$encounterid   = $_REQUEST['encounterid'];
$stringid = $_REQUEST['stringid'];
//echo "UPDATE  billing SET activity= 0 WHERE code_type='CPT4' AND code = '".$deletestring."' AND pid = $pid AND encounter = $encounterid AND id= $stringid";
$getquery = sqlStatement("UPDATE  billing SET activity= 0 WHERE code_type='CPT4' AND code = '".$deletestring."' AND pid = $pid AND encounter = $encounterid AND id= $stringid");
echo 1;
?>