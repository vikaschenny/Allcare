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


$meta_id                 = str_replace("$", " ",$_REQUEST['meta_id']);
$meta_key                = $_REQUEST['meta_key'];

$create_table =  sqlStatement("CREATE TABLE IF NOT EXISTS `tbl_insurance_payerplan_meta_mapping` (
                    `id` int(255) NOT NULL AUTO_INCREMENT,
                    `emr_meta_key` varchar(255) NOT NULL,
                    `payerplan_meta_key` varchar(255) NOT NULL,
                    `created_date` date NOT NULL,
                    `updated_log` longtext NOT NULL,
                    PRIMARY KEY (`id`)
                  ) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ");
$get_table_data = sqlStatement("SELECT * FROM tbl_insurance_payerplan_meta_mapping WHERE emr_meta_key = '".trim($meta_key)."' LIMIT 0,1");

$ip_addr        = GetIP();
$ip_addr        = $ip_addr ."(EMR)";

$logdata        = array();
$array          = array();
$isdata_existed = 0;
// update if existed
while($set_table_data = sqlFetchArray($get_table_data)){
    $new_id         = $set_table_data['id'];
    $array          =  unserialize($set_table_data['updated_log']);
    $count          = count($array);
    $isdata_existed = 1;

    $count      = isset($count)? $count: 0;
    $array2[]   = array( 'authuser' =>$_SESSION["authUser"],'meta_key' => $meta_key,'meta' => $meta_id, 'date' => date("Y/m/d"), 'action'=>'updated','ip_address'=>$ip_addr ,'count'=> $count+1);
    $logdata    = array_merge_recursive($array, $array2);
    $logdata    = ($logdata? serialize($logdata): serialize($array2) );
    // update
    $update_data = sqlStatement("UPDATE tbl_insurance_payerplan_meta_mapping SET `payerplan_meta_key` = '".trim($meta_id)."', `updated_log` ='".addslashes($logdata)."' WHERE `emr_meta_key` = '$meta_key' AND id ='".$new_id."' ");
    
}

if($isdata_existed == 0){
    $count = 0;
    $array2[]   = array( 'authuser' =>$_SESSION["authUser"],'meta_key' => $meta_key,'meta' => $meta_id, 'date' => date("Y/m/d"), 'action'=>'created','ip_address'=>$ip_addr ,'count'=> $count+1);
    $logdata    = array_merge_recursive($array, $array2);
    $logdata    = ($logdata? serialize($logdata): serialize($array2) );
    // insert
    $insert_data = sqlStatement("INSERT INTO tbl_insurance_payerplan_meta_mapping (`emr_meta_key`,`payerplan_meta_key`,`created_date`,`updated_log`) VALUES('".trim($meta_key)."','".trim($meta_id)."',NOW(),'".addslashes($logdata)."')");
    
    // get inserted id
    $get_inserted_id = sqlStatement("SELECT max(id) as id FROM tbl_insurance_payerplan_meta_mapping LIMIT 0,1");
    while($set_inserted_id = sqlFetchArray($get_inserted_id)){
        $new_id = $set_inserted_id['id'];
    }

}
echo $new_id;

function GetIP()
{
    if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown"))
        $ip = getenv("HTTP_CLIENT_IP");
    else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown"))
        $ip = getenv("HTTP_X_FORWARDED_FOR");
    else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown"))
        $ip = getenv("REMOTE_ADDR");
    else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown"))
        $ip = $_SERVER['REMOTE_ADDR'];
    else
        $ip = "unknown";
    return($ip);
}
?>