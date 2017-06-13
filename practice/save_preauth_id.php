<?php
//SANITIZE ALL ESCAPES
$sanitize_all_escapes=true;
//

//STOP FAKE REGISTER GLOBALS
$fake_register_globals=false;
//

require_once("../interface/globals.php");
require_once("$srcdir/globals.inc.php");
require_once("$srcdir/api.inc");
require_once("$srcdir/forms.inc");
require_once("$srcdir/options.inc.php");
require_once("$srcdir/patient.inc");
require_once("$srcdir/formdata.inc.php");
require_once("$srcdir/formatting.inc.php"); 

$lbf_form_id    = $_REQUEST['lbf_form_id'];

$pid            = $_REQUEST['pid'];
$provider       = $_REQUEST['provider'];
$payerid        = $_REQUEST['payer'];  
$form_id        = $_REQUEST['form_id'];  

if($lbf_form_id == 0){

    $insert_insurance_data = sqlStatement("INSERT INTO tbl_patientuser (`pid`, `payer_id`,`userid`,`created_date`,`updated_date` $preauth_fields) VALUES ('$pid','$payerid','$provider',NOW(),NOW() $preauth_values)");
    $get_form_id = sqlStatement("SELECT max(id) as id FROM tbl_patientuser");
        while($set_form_id = sqlFetchArray($get_form_id)){
            $new_id = $set_form_id['id']+1;
        }
}else{
    $new_id = $form_id;
    
    $insert_field_value = sqlStatement("UPDATE tbl_patientuser SET `updated_date` = NOW() ,elig_response_id = '$form_id' WHERE id = '$lbf_form_id' ");
}

echo json_encode($new_id);
?>