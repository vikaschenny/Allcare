<?php
require_once("../verify_session.php");
$mainBenefits = [];
$mainBenefits['insbenefits'] = [];
$insBenefits = [];
$planid = $_REQUEST['planid'];

$sql2   = sqlStatement("SELECT * from `tbl_inscomp_benefits` WHERE `planid` = '".$planid."'");
while($row2  = sqlFetchArray($sql2)){
    
    $row2['ins_type'] = '';
        $insTypeQry   = sqlStatement("SELECT `insurance_type` from `tbl_patientinsurancecompany` WHERE `id` = '".$planid."'");
        $insTypeRes = sqlFetchArray($insTypeQry);
        
        
    $row2['ins_type'] = $insTypeRes['insurance_type'];
//    
//    $instype = sqlStatement("SELECT `title` from `list_options` WHERE `list_id` = 'Payer_Types' AND `option_id` = '".$insType."'");
//    $instypeRes = sqlFetchArray($instype);
//    
//    $row2['insurance_type'] = $instypeRes['title'];
    
    array_push($insBenefits,$row2);
}
$mainBenefits['insbenefits'] = $insBenefits;

echo json_encode($mainBenefits);
?>