<?php
require_once("../../../globals.php");

// to get configured email
$esql = sqlStatement("select * from list_options where list_id='AllcareDriveSync' and option_id='email'");
$erow = sqlFetchArray($esql);
$objid=$_REQUEST['obj_id'];
$category=$_REQUEST['category'];

if($category=='patients'){
    $fid=sqlStatement("select patient_folder,pid from patient_data where pid=".$objid);
    $frow=sqlFetchArray($fid);
}
 $parentid = str_replace('https://drive.google.com/drive/folders/', '', $frow['patient_folder']); 
$curl = curl_init();
$form_url = $protocol.$_SERVER['HTTP_HOST'].'/api/DriveSync/listallfiles_web/'.$erow['notes'].'/'. $parentid.'/folders';
curl_setopt($curl,CURLOPT_URL, $form_url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);  
$result1 = curl_exec($curl);
curl_close($curl);
 $result2=json_decode($result1, TRUE);
 $result2['email']=$erow['notes'];
echo json_encode($result2, TRUE);
?>