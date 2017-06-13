<?php
require_once("verify_session.php");
$payerdata = $_POST['checkdata'];
$logid = $_POST['logid'];
$user = $_SESSION['portal_username'];
//$payerdata = explode(":", $checkdata);


$valuedata = '';

if($_POST['action']=='add'){
//    $lineqry = 'payer_name,payer_id,external_payer_indentifier,apps,enroll_required,prof_electronic_secondary,outbound_format
//    ,dual_ch_allowed,claims_attachments,professional,institutional,eligibility,remits,secondary';
//    foreach ($checkdata as $each){
//        //$eachdata = explode(",", $each);
//        $valuedata.="(".$each."),";
//    }
//    $valuedata = rtrim($valuedata,",");
//    $sql ="INSERT INTO payers_list(".$lineqry.") VALUES ".$valuedata;
//    mysql_query($sql);
    
    //echo "INSERT INTO `payers_list` (payer_name,payer_id,external_payer_indentifier,apps,enroll_required,prof_electronic_secondary,outbound_format,dual_ch_allowed,claims_attachments,professional,institutional,eligibility,remits,secondary) SELECT payer_name,payer_id,external_payer_indentifier,apps,enroll_required,prof_electronic_secondary,outbound_format,dual_ch_allowed,claims_attachments,professional,institutional,eligibility,remits,secondary FROM payers_list_temp WHERE `payer_name` = '".$payerdata['payer_name']."' AND `payer_id` = '".$payerdata['payer_id']."'"; exit;
    mysql_query("INSERT INTO `payers_list` (payer_name,payer_id,external_payer_indentifier,apps,enroll_required,prof_electronic_secondary,outbound_format,dual_ch_allowed,claims_attachments,professional,institutional,eligibility,remits,secondary) SELECT payer_name,payer_id,external_payer_indentifier,apps,enroll_required,prof_electronic_secondary,outbound_format,dual_ch_allowed,claims_attachments,professional,institutional,eligibility,remits,secondary FROM payers_list_temp WHERE `payer_name` = '".htmlspecialchars($payerdata['payer_name'])."' AND `payer_id` = '".$payerdata['payer_id']."'");
    
    echo $insertId = mysql_insert_id();
    
    $new_data = json_encode($payerdata);
    
    
    $updLogQry = "INSERT INTO `update_log` (`old_data`,`new_data`,`updated_data`,`update_type`,`updated_date`,`updated_by`) VALUES ('','$new_data','','new',NOW(),'$user')";
    mysql_query($updLogQry);
    
    sqlStatement("DELETE FROM `payers_list_temp` WHERE `payer_name` = '".htmlspecialchars($payerdata['payer_name'])."' AND `payer_id` = '".$payerdata['payer_id']."'");
    
    
//    $updLog = "UPDATE `import_log` SET `new_records` = new_records + 1 WHERE id = '".$logid."'";
//    mysql_query($updLog);
}
else if($_POST['action']=='update'){
//    echo "<pre>"; print_r($_POST);
    
    $fieldsAry = array('payer_name','payer_id','external_payer_indentifier','apps','enroll_required','prof_electronic_secondary','outbound_format','dual_ch_allowed','claims_attachments','professional','institutional','eligibility','remits','secondary');
    
    $old_id = $_POST['oldid'];
    $new_id = $_POST['newid'];
    $new_data = $_POST['new_data'];
    $old_data = $_POST['old_data'];
    
    
    $result = array_diff_assoc($new_data, $old_data);
    
    unset($result['action_status']);
    
    $updateQry = 'UPDATE `payers_list` SET ';
    foreach ($result as $key=>$value) {
        $updateQry .= $key."='".$value."',";
    }
    $trimQry = rtrim($updateQry, ",");
    $updateQry =$trimQry." where id=".$old_id;

    $old_data = json_encode($old_data);
    $new_data = json_encode($new_data);
    $updated_data = json_encode($result);
    

    $updLogQry = "INSERT INTO `update_log` (`old_data`,`new_data`,`updated_data`,`update_type`,`updated_date`,`updated_by`) VALUES ('$old_data','$new_data','$updated_data','direct',NOW(),'$user')";
    sqlStatement($updLogQry);
    sqlStatement($updateQry);
    sqlStatement("DELETE FROM `payers_list_temp` WHERE `id` = '".$new_id."'");
}
else if($_POST['action']=='action_status'){
//    echo "<pre>"; print_r($_POST);
    
    $id = $_POST['id'];
    $action_item = $_POST['actionitem'];
    $action_status = $_POST['action_status'];
    
    $existQry = sqlStatement("SELECT `id` FROM `payers_action_status` WHERE `payer_id` = '".$id."'");
    $existCnt = sqlNumRows($existQry);
    
    if($existCnt > 0){
        $updQry = "UPDATE `payers_action_status` SET `$action_item` = '".$action_status."' WHERE `payer_id` = '".$id."'";
    }
    else {
        $updQry = "INSERT INTO `payers_action_status` (`payer_id`,`$action_item`) VALUES ($id,'".$action_status."')";
    }
    
    mysql_query($updQry);
    
    //id,action_status
    
}

else if($_POST['action']=='removeins'){
//    echo "<pre>"; print_r($_POST);
    
    $formid = $_POST['formid'];
//    echo "DELETE FROM `insurance_companies` WHERE `id` = '".$formid."'";
//    echo "DELETE FROM `addresses` WHERE `foreign_id` = '".$formid."'";
//    echo "DELETE FROM `tbl_inscomp_custom_attr_1to1` WHERE `insuranceid` = '".$formid."'";
    sqlStatement("DELETE FROM `insurance_companies` WHERE `id` = '".$formid."'");
    sqlStatement("DELETE FROM `addresses` WHERE `foreign_id` = '".$formid."'");
    sqlStatement("DELETE FROM `tbl_inscomp_custom_attr_1to1` WHERE `insuranceid` = '".$formid."'");
    
}
?>