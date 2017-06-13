<?php
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

require_once("../verify_session.php");

//echo "<pre>"; print_r($_POST); exit;

if($_REQUEST['action'] == 'assign'){
    
    $ins_type = $_POST['insurance_type'];
    $fields = json_encode($_POST['fieldid']);
    
    sqlStatement("DELETE FROM `tbl_benefit_fields_map` WHERE `ins_type` = '".$ins_type."'");
    
    $sqlInsMap = "INSERT INTO `tbl_benefit_fields_map` (`ins_type`,`fields`,`created_date`) VALUES ('".$ins_type."','".$fields."',NOW())";
    sqlStatement($sqlInsMap);
    
}
else if($_REQUEST['action'] == 'change'){
    
    $ins_type = $_POST['ins_type'];
    
    $sqlBenefitFields = sqlStatement("SELECT `fields` FROM `tbl_benefit_fields_map` WHERE `ins_type` = '".$ins_type."'");
    $resBenefitFields = sqlFetchArray($sqlBenefitFields);
    
    echo json_encode(json_decode($resBenefitFields['fields']));
    
}


?>