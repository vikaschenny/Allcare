<?php 
//SANITIZE ALL ESCAPES
$sanitize_all_escapes=true;

//STOP FAKE REGISTER GLOBALS
$fake_register_globals=false;

//continue session
session_start();

//landing page definition -- where to go if something goes wrong
$landingpage = "../index.php?site=".$_SESSION['site_id'];	
//

if ( isset($_SESSION['portal_username']) ) {    
    $provider = $_SESSION['portal_username'];
}
else {
        session_destroy();
        header('Location: '.$landingpage.'&w');
        exit;
}
//

$ignoreAuth=true; // ignore the standard authentication for a regular OpenEMR user
include_once("../../interface/globals.php");

// to get configured email
$sql = sqlStatement("select * from list_options where list_id='AllcareDriveSync' and option_id='email'");
$row = sqlFetchArray($sql);

$fileid=$_REQUEST['file_id'];
//$fileid='1EY3ThLr5-uSEq48QmsD0YqdPjPQpEGJPrzDnvLm6CtA';

//set file permissions
$curl = curl_init();
$form_url = 'http://'.$_SERVER['HTTP_HOST'].'/api/DriveSync/sharefilePermission/'.$row['notes'].'/'.$fileid;
curl_setopt($curl,CURLOPT_URL, $form_url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);  
$result3 = curl_exec($curl);
$resultant3 = $result3;
curl_close($curl);


//get file metadata
$curl = curl_init();
$form_url = 'http://'.$_SERVER['HTTP_HOST'].'/api/DriveSync/getfileinfo_web/'.$row['notes'].'/'.$fileid;
curl_setopt($curl,CURLOPT_URL, $form_url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);  
$result = curl_exec($curl);
$resultant = $result;
curl_close($curl);
$folderinfo = json_decode($resultant, TRUE);


//get recent accesstoken
$curl = curl_init();
$form_url = 'http://'.$_SERVER['HTTP_HOST'].'/api/DriveSync/getaccesstoken_web/'.$row['notes'];
curl_setopt($curl,CURLOPT_URL, $form_url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);  
$result = curl_exec($curl);
$resultant = $result;
curl_close($curl);

//Urls to download files 
if($_REQUEST['ggl']=='1'){
    $getUrl=$folderinfo['webViewLink'];
}else {
    $getUrl='https://docs.google.com/a/smartmbbs.com/file/d/'.$fileid.'/preview';
   
}


  //  $authHeader = 'Authorization: Bearer ' . $resultant ; 
header('Authorization: Bearer ' . $resultant);
header('Location: ' . $getUrl );

?>