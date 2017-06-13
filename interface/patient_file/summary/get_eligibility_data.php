<?php
//SANITIZE ALL ESCAPES
$sanitize_all_escapes=true;
//

//STOP FAKE REGISTER GLOBALS
$fake_register_globals=false;
//

require_once("../../globals.php");
require_once("$srcdir/globals.inc.php");
require_once("$srcdir/api.inc");
require_once("$srcdir/forms.inc");
require_once("$srcdir/options.inc.php");
require_once("$srcdir/patient.inc");
require_once("$srcdir/formdata.inc.php");
require_once("$srcdir/formatting.inc.php");

$pid         = $_REQUEST['pid'];
$id          = $_REQUEST['id'];
$set_values  = array();
$getFields = sqlStatement("SELECT field_id,title FROM layout_options WHERE form_id='ELIGIBILITY' AND uor <> 0 ORDER BY group_name, seq"); 
while($rowfields = sqlFetchArray($getFields)){
    $get_fields .= "`".$rowfields['field_id']."`,";
}

$get_fields_names = rtrim($get_fields,",");

$sql=sqlStatement("select `id`,$get_fields_names from tbl_eligibility_response_data where pid=$pid AND id= $id ORDER BY updated_date DESC"); 
while($row=sqlFetchArray($sql)){
    foreach($row as $key => $value){
        $set_values[] = $value;
    }
}
echo json_encode($set_values);