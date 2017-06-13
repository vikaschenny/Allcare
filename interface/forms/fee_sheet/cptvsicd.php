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
    $getICDsQuery = sqlQuery("SELECT notes
                                FROM  `list_options` 
                                WHERE  `list_id` =  'AllCareCPTvsICD'
                                AND FIND_IN_SET(  '".$selectValues[$i]."', REPLACE( title, SPACE( 1 ) ,  '' ) ) >0");
    $getICDsMapped = "TRIM('". str_replace(",","'),TRIM('",$getICDsQuery['notes']) ."')";
    $getquery = sqlStatement("SELECT DISTINCT formatted_dx_code, long_desc FROM icd10_dx_order_code WHERE formatted_dx_code IN (".$getICDsMapped.") AND active = 1");
    while($setquery = sqlFetchArray($getquery)){
        $array[$selectValues[$i] ."$$".$setquery['formatted_dx_code']][0]= $setquery['formatted_dx_code'];
        $array[$selectValues[$i] ."$$".$setquery['formatted_dx_code']][1]= $setquery['long_desc'];
    }
}
//print_r($array);
echo json_encode($array);
?>