<?php

//STOP FAKE REGISTER GLOBALS
$fake_register_globals=false;

//continue session
session_start();

//landing page definition -- where to go if something goes wrong
$landingpage = "../index.php?site=".$_SESSION['site_id'];	


if ( isset($_SESSION['portal_username']) ) {    
    $portal_user = $_SESSION['portal_username']; 
}else {
    session_destroy();
    header('Location: '.$landingpage.'&w');
    exit;
} 

$ignoreAuth=true; // ignore the standard authentication for a regular OpenEMR user
include_once('../../interface/globals.php');

 
 $sql_post=sqlStatement("select email from drive_users where email='".$_REQUEST['email']."' and domain='web' order by id desc");
 $post_id = sqlFetchArray($sql_post);
 if($post_id['email']!=''){
     echo "sucess";
 }else {
     echo "fail";
 }
?>