<?php
require_once("../../../globals.php");


$category=$_REQUEST['category'];
$objid=$_REQUEST['obj_id'];
$folderid=$_REQUEST['folderid'];


// to get configured email
$esql = sqlStatement("select * from list_options where list_id='AllcareDriveSync' and option_id='email'");
$erow = sqlFetchArray($esql);


$link=sqlStatement("select * from tbl_pnotes_file_relation where mid=".$_REQUEST['msg_id']);
$rowlink=sqlFetchArray($link);

if($category=='patients'){
    $fid=sqlStatement("select patient_folder,pid from patient_data where pid=".$objid);
    $frow=sqlFetchArray($fid);
}

$protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === true ? 'https://' : 'http://';
$curl = curl_init();
$form_url=$protocol.$_SERVER['HTTP_HOST'].'/api/DriveSync/movefile_web1/'.$erow['notes'].'/'.trim($rowlink['doc_links']," ").'/'.$folderid.'/'. $_SESSION['authUser'];
curl_setopt($curl,CURLOPT_URL, $form_url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curl, CURLOPT_BINARYTRANSFER, 1);
$result = curl_exec($curl);
//echo "kjk".$result."jn";
if(trim($result," ")==''){
    echo "success";  
}else { 
    echo "fail";
}
curl_close($curl);

 
?>