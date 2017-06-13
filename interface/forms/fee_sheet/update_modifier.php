<?php
$ignoreAuth=true; // ignore the standard authentication for a regular OpenEMR user

require_once("../../globals.php");
 
include_once("$srcdir/api.inc");
require_once("$srcdir/patient.inc");
require_once("$srcdir/options.inc.php");
require_once("$srcdir/htmlspecialchars.inc.php");
include_once("$srcdir/acl.inc");

$id   = $_REQUEST['id'];
$value   = $_REQUEST['value'];
$getquery = sqlStatement("UPDATE  billing SET modifier= '$value' WHERE id= $id");
echo 1;
?>