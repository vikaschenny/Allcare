<?php

//STOP FAKE REGISTER GLOBALS
$fake_register_globals=false;
//continue session
session_start();

$landingpage = "../../../index.php?site=".$_SESSION['site_id'];

if ( isset($_SESSION['portal_username']) ) {    
    $portal_user = $_SESSION['portal_username']; 
}else {
    session_destroy();
    header('Location: '.$landingpage.'&w');
    exit;
}

$ignoreAuth=true; // ignore the standard authentication for a regular OpenEMR user

include_once("../../../interface/globals.php");

$user = $_SESSION['portal_username'];

extract($_POST);
//echo "<pre>"; print_r($_POST); exit;

if($hid_state == 'manual'){
    $insID = generate_id();
    $sqlInsurance = "INSERT INTO `insurance_companies` (`id`,`name`,`attn`,`cms_id`,`freeb_type`,`x12_receiver_id`,`x12_default_partner_id`) VALUES ('".$insID."','".$name."','".$attn."','".$cmsid."','".$payertype."','".$x12partner."','".$x12partner."')";
    $insID = sqlInsert($sqlInsurance);
    $sqlInsAddr = "INSERT INTO `addresses` (`id`,`line1`,`line2`,`city`,`state`,`zip`,`country`,`foreign_id`) VALUES ('".generate_id()."','".$address1."','".$address2."','".$city."','".$state."','".$zip."','USA','".$insID."')";
    sqlStatement($sqlInsAddr);
    $sqlInsAttr = "INSERT INTO `tbl_inscomp_custom_attr_1to1` (`insuranceid`,`uniqueid`) VALUES ('".$insID."','manual')";
    sqlStatement($sqlInsAttr);


    $new_data = $_POST;

    unset($new_data['hid_uniqueid']);
    $new_data = json_encode($new_data);
    $updLogQry = "INSERT INTO `update_log` (`old_data`,`new_data`,`updated_data`,`update_type`,`updated_date`,`updated_by`) VALUES ('','$new_data','','new_ins_manual',NOW(),'$user')";
    sqlStatement($updLogQry);
}


else if($hid_uniqueid != ''){

    $insComExist = sqlStatement("SELECT `insuranceid` FROM `tbl_inscomp_custom_attr_1to1` WHERE `insuranceid` = '".$hid_uniqueid."'");
    $existCnt = sqlNumRows($insComExist);

    if($existCnt > 0){
        $insComRes = sqlFetchArray($insComExist);
        $insId = $insComRes['insuranceid'];
        $updInsurance = "UPDATE `insurance_companies` SET `name`='".$name."',`cms_id`='".$cmsid."',`attn`='".$attn."',`freeb_type`='".$payertype."',`x12_receiver_id`='".$x12partner."',`x12_default_partner_id`='".$x12partner."' WHERE `id` = '".$insId."'";
        sqlStatement($updInsurance);
        $updInsAddr = "UPDATE `addresses` SET `line1`='".$address1."',`line2`='".$address2."',`city`='".$city."',`state`='".$state."',`zip`='".$zip."',`country`='USA' WHERE `foreign_id`='".$insId."'";
        sqlStatement($updInsAddr);
        echo $insId;
    }
    else{
        $sqlInsurance = "INSERT INTO `insurance_companies` (`name`,`attn`,`cms_id`,`freeb_type`,`x12_receiver_id`,`x12_default_partner_id`) VALUES ('".$name."','".$attn."','".$cmsid."','".$payertype."','".$x12partner."','".$x12partner."')";
        mysql_query($sqlInsurance);
        echo $insID = mysql_insert_id();
        $sqlInsAddr = "INSERT INTO `addresses` (`id`,`line1`,`line2`,`city`,`state`,`zip`,`country`,`foreign_id`) VALUES ('".$insID."','".$address1."','".$address2."','".$city."','".$state."','".$zip."','USA','".$insID."')";
        sqlStatement($sqlInsAddr);
        $sqlInsAttr = "INSERT INTO `tbl_inscomp_custom_attr_1to1` (`insuranceid`,`uniqueid`) VALUES ('".$insID."','".$hid_uniqueid."')";
        sqlStatement($sqlInsAttr);


        $new_data = $_POST;

        unset($new_data['hid_uniqueid']);
        $new_data = json_encode($new_data);
        $updLogQry = "INSERT INTO `update_log` (`old_data`,`new_data`,`updated_data`,`update_type`,`updated_date`,`updated_by`) VALUES ('','$new_data','','new_ins',NOW(),'$user')";
        sqlStatement($updLogQry);

    }
}
//else if($hid_uniqueid == ''){
//    $sqlInsurance = "INSERT INTO `insurance_companies` (`name`,`attn`,`cms_id`,`freeb_type`,`x12_receiver_id`,`x12_default_partner_id`) VALUES ('".$name."','".$attn."','".$cmsid."','".$payertype."','".$x12partner."','".$x12partner."')";
//    mysql_query($sqlInsurance);
//    echo $insID = mysql_insert_id();
//    $sqlInsAddr = "INSERT INTO `addresses` (`id`,`line1`,`line2`,`city`,`state`,`zip`,`country`,`foreign_id`) VALUES ('".$insID."','".$address1."','".$address2."','".$city."','".$state."','".$zip."','USA','".$insID."')";
//    sqlStatement($sqlInsAddr);
//    $sqlInsAttr = "INSERT INTO `tbl_inscomp_custom_attr_1to1` (`insuranceid`,`uniqueid`) VALUES ('".$insID."','manual')";
//    sqlStatement($sqlInsAttr);
//
//
//    $new_data = $_POST;
//
//    unset($new_data['hid_uniqueid']);
//    $new_data = json_encode($new_data);
//    $updLogQry = "INSERT INTO `update_log` (`old_data`,`new_data`,`updated_data`,`update_type`,`updated_date`,`updated_by`) VALUES ('','$new_data','','new_ins_manual',NOW(),'$user')";
//    sqlStatement($updLogQry);
//}
