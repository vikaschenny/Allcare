<?php

 //SANITIZE ALL ESCAPES
 $sanitize_all_escapes=$_POST['true'];

 //STOP FAKE REGISTER GLOBALS
 $fake_register_globals=$_POST['false'];

include_once("../../globals.php");
include_once("$srcdir/api.inc");
include_once("$srcdir/forms.inc");
require_once("$srcdir/formdata.inc.php");

if (! $encounter) { // comes from globals.php
 die(xl("Internal error: we do not seem to be in an encounter!"));
}
$id = 0 + (isset($_GET['id']) ? $_GET['id'] : '');

for($i=1; $i<=$_REQUEST['noofrows']; $i++){
    if($i == 1):
        $ccmtypeval  = "ccmtype";
        $reference    = "reference";
        $description = "description";
        $start_date  = "start_date";
        $timeinterval    = "timeinterval";
        $location    = "location";
        $users    = "users";
    else:
        $ccmtypeval  = "ccmtype".$i;
        $reference    = "reference".$i;
        $description = "description".$i;
        $start_date  = "start_date".$i;
        $timeinterval    = "timeinterval".$i;
        $location    = "location".$i;
        $users    = "users".$i;
    endif;
    $array2[] = array(  'ccmtype' => $_REQUEST[$ccmtypeval],
                        'start_date' =>  $_REQUEST[$start_date], 
                        'timeinterval' => $_REQUEST[$timeinterval],
                        'users' => $_REQUEST[$users],
                        'location' => addslashes(htmlspecialchars($_REQUEST[$location])),
                        'description' => addslashes(htmlspecialchars($_REQUEST[$description])),
                        'reference' => addslashes(htmlspecialchars($_REQUEST[$reference])));
}
$ccm_data= ( serialize($array2) );
$sets = "pid = {$_SESSION["pid"]},
    authProvider = '" . $_SESSION["authProvider"] . "',
    user = '" . $_SESSION["authUser"] . "',
    authorized = $userauthorized, 
    activity=1, 
    date = NOW(),
    provider_id  =  '" .add_escape_custom($_POST["provider_id"]) . "',
    ccm_data = '" .$ccm_data. "',
    signed_date =    '" .$_POST["signed_date"] . "',
    count =    '" .add_escape_custom($_POST["noofrows"]) . "'";

  
if (empty($id)) {
    $newid = sqlInsert("INSERT INTO tbl_form_ccm SET $sets");
    addForm($encounter, "CCM", $newid, "ccm", $pid, $userauthorized);
}
else {
    sqlStatement("UPDATE tbl_form_ccm SET $sets WHERE id = '". add_escape_custom("$id"). "'");
   
}

$_SESSION["encounter"] = $encounter;
formHeader("Redirecting....");
formJump();
formFooter();
?>