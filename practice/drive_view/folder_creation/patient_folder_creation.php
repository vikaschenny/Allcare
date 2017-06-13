<?php 
require_once("sqlQa2DB.inc");

function getdata($furl){
    $curl = curl_init(); 
    curl_setopt($curl,CURLOPT_URL,$furl);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);  
    $result = curl_exec($curl);
    $resultant = $result;
    curl_close($curl);
    return $resultant;
}
function log_data($today,$user,$email,$enc,$ins_id12,$link,$fname,$fid,$status,$id){
                  
global $sqlconfCentralDB;
$category='patient';
    $stmt2 = $sqlconfCentralDB->prepare("insert into DriveSync_log (date,user,email,encounter,patient_id,google_folder,file_name ,file_id,status,watsID,category)"
            . "values(:date,:user,:email,:encounter,:id,:google_folder,:file_name,:file_id,:status,:watsID,:category)");
    $stmt2->bindParam(':date', $today);       
    $stmt2->bindParam(':user', $user); 
    $stmt2->bindParam(':email', $email);
    $stmt2->bindParam(':encounter', $enc);
    $stmt2->bindParam(':id', $ins_id12);
    $stmt2->bindParam(':google_folder', $link);
    $stmt2->bindParam(':file_name', $fname);
    $stmt2->bindParam(':file_id', $fid);
    $stmt2->bindParam(':status', $status);
    $stmt2->bindParam(':watsID', $id);
    $stmt2->bindParam(':category', $category);
    $stmt2->execute(); 
}
//function to create folder
function Create_folder($email,$user_id,$folder_name,$parentid,$current_id,$parent_stat,$action,$subfolder,$protocol){
    if($folder_name!='') {
       //To create folder in parent folder
        if($parentid!='' && $parent_stat=='yes'){
            //create folder api call
             $presultant= getdata($protocol.'/api/DriveSync/insert_folder_web/'.$email.'/'.$parentid.'/'.$folder_name);
             $val= explode(':',$presultant);
        }
        else {
             //create folder api call
            $presultant=getdata($protocol.'/api/DriveSync/createfolder_web/'.$email.'/'.$folder_name);
            $val= explode(':',$presultant); 
        }
        if($val[0]!==''){
            if($action=='create'){
                global $sqlconfCentralDB;
                $link='https://drive.google.com/drive/folders/'.$val[0];
                $sql = $sqlconfCentralDB->prepare("select patient_folder from patient_data where pid=:pid");
                $sql->bindParam(':pid',$user_id); 
                $sql->execute();
                $data_fetch = $sql->fetchObject();
                $today = date("Y-m-d"); 
                if(!empty($data_fetch)){
                    $update=$sqlconfCentralDB->prepare("UPDATE `patient_data` SET `patient_folder`=:patient_folder WHERE pid= :pid");
                    //$update->bindParam(':date', $date);       
                    $update->bindParam(':patient_folder', $val[0]); 
                    $update->bindParam(':pid', $user_id);
                    $update->execute(); 
                }else {
                    $stmt1 = $sqlconfCentralDB->prepare("insert into patient_data (pid,patient_folder)values(:patient_folder,:pid)");
                    $stmt1->bindParam(':patient_folder', $val[0]);       
                    $stmt1->bindParam(':pid', $user_id); 
                    $stmt1->execute(); 
                }
                log_data($today,'',$email,'',$user_id,$link,'','','patient folder_created(auto Creation)','');
                //subfolders
                if($subfolder=='yes'){
                    $subsql = $sqlconfCentralDB->prepare("select * from list_options where list_id='AllcarePatientSubfolders'");
                    $subsql->execute();
                    $row=$subsql->rowCount();
                    $link1='';
                    if($row!=0){
                        while($subrow = $subsql->fetchObject()){
                            $subresultant=getdata($protocol.'/api/DriveSync/insert_folder_web/'.$email.'/'.$val[0].'/'.str_replace(" ","_",$subrow->title));
                            $subval= explode(':',$subresultant);
                            $link1.='https://drive.google.com/drive/folders/'.$subval[0]."||";
                        }
                        log_data($today,'',$email,'',$user_id,$link1,'','','subfolder_created(during patient Creation in EMR)','');
                    }
                }
            }
        }    
       // return $val[0];
    }
}

//To get url
$ustmt = $sqlconfCentralDB->prepare("select * from list_options where list_id='AllcareDriveSync' and option_id='url'") ;
$ustmt->execute();
$urs = $ustmt->fetchObject();
$protocol = $urs->title;

//To get configured mail id 
$stmt = $sqlconfCentralDB->prepare("select * from list_options where list_id='AllcareDriveSync' and option_id='email'") ;
$stmt->execute();
$rs = $stmt->fetchObject();

//To get parent folder for  patient
$sel_rows = $sqlconfCentralDB->prepare("select parent_folder,patient_folder,practice_parent_folder,patient_sub_folder from tbl_drivesync_authentication where email='$rs->notes' order by id desc");
$sel_rows->execute();
$es = $sel_rows->fetchObject();

if($es->parent_folder!='')
 $parentid = str_replace('https://drive.google.com/drive/folders/', '', $es->parent_folder); 
else
 echo "please mention the parent folder for patient";

//get query to create folder for each facility
if($es->patient_folder!='')
    $query=$es->patient_folder;

//check patient parent folder with in the practice folder or not
$list_data=getdata($protocol.'/api/DriveSync/listall_folderid/'.$rs->notes.'/'. str_replace('https://drive.google.com/drive/folders/', '', $es->practice_parent_folder).'/folders');
$all_folders = json_decode($list_data, TRUE);

if(is_array($all_folders)){
   if(in_array($parentid,$all_folders)){
      $parent_stat='yes'; 
    } 
}else {
    echo "internal server error:"; 
    exit();
}

$list_sql = $sqlconfCentralDB->prepare("select patient_folder,pid from patient_data where patient_folder='' order by id desc limit 0,600");
$list_sql->execute();
while($data_row = $list_sql->fetchObject()){
    
    $id[]=$data_row->pid;
    //Create_folder($rs->notes,$data_row->pid,$folder_name,$parentid,'',$parent_stat,'create',$es->patient_sub_folder,$protocol);
}
if(!empty($id)){
    foreach($id as $key => $val1){
        $fsql = $sqlconfCentralDB->prepare($query." where  pid=".$val1);
        $fsql->execute();
        $frow = $fsql->fetchObject();
        $folder_name= str_replace(" ","",$frow->Patient_folder);  
        Create_folder($rs->notes,$val1,$folder_name,$parentid,'',$parent_stat,'create',$es->patient_sub_folder,$protocol);
    }
}

?>