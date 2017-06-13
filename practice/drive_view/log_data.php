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
$sql=sqlStatement("select email,date,user,patient_id,google_folder,file_name,status,category from DriveSync_log where email='".$_REQUEST['email']."' and user='".$_REQUEST['user']."' order by id desc");
while($data_row=sqlFetchArray($sql)){
    $result=is_numeric($data_row['patient_id']);
    if($result==1){
        $oid=$data_row['patient_id'];
        if($data_row['category']=='patient'){
            $data_row['patient_id']=patient_details($oid);
        }else if($data_row['category']=='user'){
            $data_row['patient_id']=user_details($oid);
        }else if($data_row['category']=='insurance'){
            $data_row['patient_id']=insurance_details($oid);
        }else if($data_row['category']=='pharmacy'){
            $data_row['patient_id']=pharmacy_details($oid);
        }else if($data_row['category']=='Agency'){
            $data_row['patient_id']=agency_details($oid);
        }else if($data_row['category']=='facility'){
            $data_row['patient_id']=facility_details($oid);
        }
    }
    if(strpos($data_row['google_folder'],'<!')!=false){
        $data_row['google_folder']='error';
    }
    if(strpos($data_row['google_folder'],'404')!=false){
         $data_row['google_folder']='not found';
    }
    $data.=json_encode($data_row).",";
} 
echo '{
"data":['.trim($data,",").'] }';
?> 