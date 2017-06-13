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


$pid = $_REQUEST['pid'];
$posted_id = $_REQUEST['posted_id'];
$get_elig_data = sqlStatement("SELECT elig_est_data,html_data FROM tbl_eligibility_html_data WHERE pid = '$pid' AND id = '$posted_id'");
while($set_elig_data = sqlFetchArray($get_elig_data)){
    echo  base64_decode ($set_elig_data['html_data']);
//     echo ' <img src="data:image/jpeg;base64,' . base64_decode ($set_elig_data['html_data']) . '">';
}
?>