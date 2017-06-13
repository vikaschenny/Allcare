<?php
 include("../session_file.php");
 echo "fgfd"; exit();
$sql=sqlStatement("select email,date,user,patient_id,google_folder,file_name,status,watsID from DriveSync_log where email='".$_REQUEST['email']."' and user='".$_REQUEST['user']."' order by id desc");
while($data_row=sqlFetchArray($sql)){
    $data.=json_encode($data_row).",";
}
//echo '{
//"data":['.trim($data,",").'] }';
echo "select email,date,user,patient_id,google_folder,file_name,status,watsID from DriveSync_log where email='".$_REQUEST['email']."' and user='".$_REQUEST['user']."' order by id desc";
?>