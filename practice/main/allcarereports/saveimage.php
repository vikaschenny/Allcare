<?php
//SANITIZE ALL ESCAPES
$sanitize_all_escapes=true;
//

//STOP FAKE REGISTER GLOBALS
$fake_register_globals=false;
//

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


$pid            = $_REQUEST['pid'];
$new_ins_type   = $_REQUEST['insurance_type'];
$frontimage     = addslashes($_REQUEST['frontimage']);
$backimage      = addslashes($_REQUEST['backimage']);
 
$get_image = sqlStatement("SELECT * FROM tbl_patient_insurancedata_meta_data WHERE pid='$pid'  and `type` = '$new_ins_type'");
while($set_image = sqlFetchArray($get_image)){
    $get_image = sqlStatement("INSERT INTO tbl_patient_insurancedata_meta_data (`frontimage`, `backimage`,`pid`,`type`,`user`,`provider` ) VALUES ('$frontimage','$backimage','$provider','')WHERE pid='$pid'  and `type` = '$new_ins_type'");
    $inserted = 1;
}
if(!$inserted){
    $updated = sqlStatement("UPDATE tbl_patient_insurancedata_meta_data SET `frontimage`='$frontimage', `backimage`= '$backimage' ,`user`= '$provider', ,`provider`= '' WHERE pid='$pid'  and `type` = '$new_ins_type' ");
}
?>