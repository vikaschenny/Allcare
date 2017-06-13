<?php
$ignoreAuth=true; // ignore the standard authentication for a regular OpenEMR user

require_once("../../globals.php");
 
include_once("$srcdir/api.inc");
require_once("$srcdir/patient.inc");
require_once("$srcdir/options.inc.php");
require_once("$srcdir/htmlspecialchars.inc.php");
include_once("$srcdir/acl.inc");

$icdValue   = $_REQUEST['value'];
$icdValue = explode("$$",$icdValue);
$checked = $_REQUEST['ischecked'];
if($checked == 1){
    sqlStatement("UPDATE lists SET enddate = NULL WHERE id = ".$icdValue[1]);
}
else{
    sqlStatement("UPDATE lists SET enddate = NOW() WHERE id = ".$icdValue[1]);
}
?>