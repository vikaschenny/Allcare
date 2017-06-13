<?php
//SANITIZE ALL ESCAPES
$sanitize_all_escapes=true;

//STOP FAKE REGISTER GLOBALS
$fake_register_globals=false;

include_once("../../globals.php");
include_once("$srcdir/api.inc");
require_once("$srcdir/patient.inc");
require_once("$srcdir/options.inc.php");
require_once("$srcdir/htmlspecialchars.inc.php");
include_once("$srcdir/acl.inc");

$encounter = $_POST['encounter'];
$visitT = $_POST['visitT'];
$visitC = sqlStatement("SELECT codes FROM list_options 
                    WHERE list_id='Level_Of_Service' and title='$visitT' ");
$visitCR = "";
while($visitCRow = sqlFetchArray($visitC)){
    $return_array[0] = $visitCRow['codes'];
}

if(empty($return_array[0])) $return_array[0] = 'None';
echo json_encode($return_array); 
?>