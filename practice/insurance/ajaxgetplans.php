<?php
require_once("../verify_session.php");
$mainPlans = [];
$mainPlans['insplans'] = [];
$insPlans = [];
$insid = $_POST['insid'];
$sql2   = sqlStatement("SELECT * from `tbl_patientinsurancecompany` WHERE `insuranceid` = '".$insid."'");
while($row2  = sqlFetchArray($sql2)){
    
    $insType = $row2['insurance_type'];
    
    $instype = sqlStatement("SELECT `title` from `list_options` WHERE `list_id` = 'Payer_Types' AND `option_id` = '".$insType."'");
    $instypeRes = sqlFetchArray($instype);
    
    $row2['ins_type'] = $insType;
    
    $row2['insurance_type'] = $instypeRes['title'];
    
    array_push($insPlans,$row2);
}
$mainPlans['insplans'] = $insPlans;

echo json_encode($mainPlans);

?>