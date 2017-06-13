<?php
include_once('../../globals.php');
require_once("objects_function.php");

//get log details
$sql=sqlStatement("select email,date,user,patient_id,google_folder,file_name,status,watsID,category from DriveSync_log where email='".$_REQUEST['email']."' and user='".$_REQUEST['user']."' order by id desc ");
while($data_row=sqlFetchArray($sql)){
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
    $data.=json_encode($data_row).",";   
}

echo '{
"data":['.trim($data,",").'] }';
?> 