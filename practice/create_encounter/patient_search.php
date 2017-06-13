<?php
$ignoreAuth=true; // ignore the standard authentication for a regular OpenEMR user

include_once('../../interface/globals.php');
 
include_once("$srcdir/api.inc");
require_once("$srcdir/patient.inc");
require_once("$srcdir/options.inc.php");
require_once("$srcdir/htmlspecialchars.inc.php");
include_once("$srcdir/acl.inc");

$patientName   = $_REQUEST['searchstring'];
$query="SELECT  pid,CONCAT(title,' ' ,fname , ' ',lname) as name
                                FROM patient_data p
                                WHERE fname like '%$patientName%'
                                OR mname like '%$patientName%'
                                OR lname like '%$patientName%'
				OR CONCAT(fname,' ',lname) like '%$patientName%'
                                OR CONCAT(fname,' ',mname) like '%$patientName%'
                                ORDER BY fname,lname limit 500";

$getquery = sqlStatement($query);
$array = array();
while($setquery = sqlFetchArray($getquery)){
    $array[$setquery['pid']] = $setquery['name'];
}
echo json_encode($array);
?>