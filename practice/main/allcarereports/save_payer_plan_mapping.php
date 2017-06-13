<?php
require_once("../../verify_session.php");

$pagename = "plist"; 
if(isset($_SESSION['portal_username']) !=''){
   $provider=$_SESSION['portal_username'];
}else {
   $provider=$_REQUEST['provider'];
   $refer=$_REQUEST['refer']; 
   $_SESSION['refer']=$_REQUEST['refer'];
   $_SESSION['portal_username']=$_REQUEST['provider'];
} 

$base_url="//".$_SERVER['SERVER_NAME'].dirname($_SERVER["REQUEST_URI"].'?').'/';

 $sql=sqlStatement("SELECT id, fname, lname, specialty FROM users " .
      "WHERE active = 1 AND ( info IS NULL OR info NOT LIKE '%Inactive%' ) " .
      "AND authorized = 1 AND username='".$provider."'" .
      "ORDER BY lname, fname");
$id=sqlFetchArray($sql);
$id1=$id['id'];


$set_check_table    = 1;
$set_log_table      = 1;

//$mapping_id                 = $_REQUEST['mapping_id'];
$emr_payer_id               = $_REQUEST['emr_payer_id'];
$payerplan_payer_id         = $_REQUEST['payerplan_payer_id'];
$elig_payerplan_payer_id    = $_REQUEST['elig_payerplan_payer_id'];
$emr_payer_name             = $_REQUEST['emr_payer_name'];
$payer_plan_payer_name      = $_REQUEST['payer_plan_payer_name'];
$elig_payer_plan_payer_name = $_REQUEST['elig_payer_plan_payer_name'];

// for tbl_payerplan_emrpayerplan_mapping_log table
$check_log_table = sqlStatement("SELECT count(*)
                                FROM information_schema.tables
                                WHERE table_name =  'tbl_payerplan_emrpayerplan_mapping_log'
                                LIMIT 1");
while($get_check_log_table = sqlFetchArray($check_log_table)){
    $set_log_table = $get_check_log_table['count'];
}
if($set_log_table == 0){
    $create_payerplan_meta_table = sqlStatement("CREATE TABLE IF NOT EXISTS `tbl_payerplan_emrpayerplan_mapping_log` (
                                                    `id` int(15) NOT NULL AUTO_INCREMENT,
                                                    `query` longtext NOT NULL,
                                                    `user` int(15) NOT NULL,
                                                    `date` date NOT NULL,
                                                    `type` varchar(255) NOT NULL,
                                                    PRIMARY KEY (`id`)
                                                  ) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;");
}

// for tbl_payerplan_emrpayerplan_mapping table
$check_table = sqlStatement("SELECT count(*) as count
                                FROM information_schema.tables
                                WHERE table_name =  'tbl_payerplan_emrpayerplan_mapping'
                                LIMIT 1");
while($get_check_table = sqlFetchArray($check_table)){
    $set_check_table = $get_check_table['count'];
}
if($set_check_table == 0){
    $create_table = "CREATE TABLE IF NOT EXISTS `tbl_payerplan_emrpayerplan_mapping` (
                        `id` int(15) NOT NULL AUTO_INCREMENT,
                        `emr_payer_id` int(15) NOT NULL,
                        `emr_payer_name` varchar(255) NOT NULL,
                        `payerplan_payer_id` varchar(255) NOT NULL,
                        `payerplan_payer_name` longtext NOT NULL,
                        `elig_payer_id` varchar(200) NOT NULL,
                        `elig_payer_name` longtext NOT NULL,
                        `user` int(15) NOT NULL,
                        `date` date NOT NULL,
                        PRIMARY KEY (`id`)
                      ) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ; ";
    
    $create_payer_table = sqlStatement($create_table);
    
    $create_table_log = sqlStatement("INSERT INTO tbl_payerplan_emrpayerplan_mapping_log(`query`, `user`, `date`, `type`) VALUES ('".addslashes($create_table)."','".$_SESSION['authID']."',NOW(),'Create Table')");
}

$check_emr_id = sqlStatement("SELECT id FROM tbl_payerplan_emrpayerplan_mapping WHERE emr_payer_id = '$emr_payer_id'");

$mapping_id = 0; 

while($set_check_emr_id = sqlFetchArray($check_emr_id)){
    $mapping_id = $set_check_emr_id['id'];
}
$new_id = 0; 

if($mapping_id == 0){
    // to insert into table
    if(isset($payer_plan_payer_name) && isset($payerplan_payer_id))
        $insert = "INSERT INTO tbl_payerplan_emrpayerplan_mapping (`emr_payer_id`,`emr_payer_name`,`payerplan_payer_id`,`payerplan_payer_name`,`user`,`date`) VALUES ('$emr_payer_id','".addslashes($emr_payer_name)."','$payerplan_payer_id','".addslashes($payer_plan_payer_name)."','".$_SESSION['authID']."',NOW())";
    if(isset($elig_payer_plan_payer_name) && isset($elig_payerplan_payer_id))
        $insert = "INSERT INTO tbl_payerplan_emrpayerplan_mapping (`emr_payer_id`,`emr_payer_name`,`elig_payer_id`,`elig_payer_name`,`user`,`date`) VALUES ('$emr_payer_id','".addslashes($emr_payer_name)."','$elig_payerplan_payer_id','".addslashes($elig_payer_plan_payer_name)."','".$_SESSION['authID']."',NOW())";
    
    $set_practice = sqlStatement($insert);
    
    // get inserted id
    $get_inserted_id = sqlStatement("SELECT max(id) as id FROM tbl_payerplan_emrpayerplan_mapping LIMIT 0,1");
    while($set_inserted_id = sqlFetchArray($get_inserted_id)){
        $new_id = $set_inserted_id['id'];
    }

    // to insert mapping data log in table
    $get_practice_id_log = sqlStatement("INSERT INTO tbl_payerplan_emrpayerplan_mapping_log(`query`, `user`, `date`, `type`) VALUES ('".addslashes($insert)."','".$_SESSION['authId']."',NOW(),'INSERT')");
} else {
    // to update into table
    if(isset($payer_plan_payer_name) && isset($payerplan_payer_id))
        $update = "UPDATE tbl_payerplan_emrpayerplan_mapping SET `emr_payer_id` = '$emr_payer_id', `emr_payer_name` = '".addslashes($emr_payer_name)."', `payerplan_payer_id` ='$payerplan_payer_id', `payerplan_payer_name` ='".addslashes($payer_plan_payer_name)."', `user` ='".$_SESSION['authID']."', `date` =NOW() WHERE id = '$mapping_id'";
    if(isset($elig_payer_plan_payer_name) && isset($elig_payerplan_payer_id))
        $update = "UPDATE tbl_payerplan_emrpayerplan_mapping SET `emr_payer_id` = '$emr_payer_id', `emr_payer_name` = '".addslashes($emr_payer_name)."', `elig_payer_id` ='$elig_payerplan_payer_id', `elig_payer_name` ='".addslashes($elig_payer_plan_payer_name)."', `user` ='".$_SESSION['authID']."', `date` =NOW() WHERE id = '$mapping_id'";
    $set_practice = sqlStatement($update);
    
    $new_id = $mapping_id;
    
    // to update mapping data log in table
    $get_practice_id_log = sqlStatement("INSERT INTO tbl_payerplan_emrpayerplan_mapping_log(`query`, `user`, `date`, `type`) VALUES ('".addslashes($update)."','".$_SESSION['authId']."',NOW(),'UPDATE')");
}
echo $new_id;
?>