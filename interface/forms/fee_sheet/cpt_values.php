<?php
$ignoreAuth=true; // ignore the standard authentication for a regular OpenEMR user

require_once("../../globals.php");
 
include_once("$srcdir/api.inc");
require_once("$srcdir/patient.inc");
require_once("$srcdir/options.inc.php");
require_once("$srcdir/htmlspecialchars.inc.php");
include_once("$srcdir/acl.inc");

$selectValues   = $_REQUEST['selectValues'];
$array = array();
for($i=0; $i< count($selectValues); $i++){
    $getquery = sqlStatement("SELECT code_text FROM codes WHERE code = '".$selectValues[$i]."'");
    while($setquery = sqlFetchArray($getquery)){
        $array[$selectValues[$i]]= $setquery['code_text']; 
    }
}
//print_r($array);
echo json_encode($array);
?>