<?php 
require_once("sqlQa2DB.inc");

//$protocol = '/usr/bin/php /mnt/stor10-wc2-dfw1/551939/551948/qa2allcare.texashousecalls.com/web/content';
//To get url
$ustmt = $sqlconfCentralDB->prepare("select * from list_options where list_id='AllcareDriveSync' and option_id='url'") ;
$ustmt->execute();
$urs = $ustmt->fetchObject();
$protocol = $urs->title;

//To get configured mail id 
$stmt = $sqlconfCentralDB->prepare("select * from list_options where list_id='AllcareDriveSync' and option_id='email'") ;
$stmt->execute();
$rs = $stmt->fetchObject();

//To get parent folder for  facility
$sel_rows = $sqlconfCentralDB->prepare("select facility_parent_folder,facility_folder_query,practice_parent_folder,facility_sub_folder from tbl_drivesync_authentication where email='$rs->notes' order by id desc");
$sel_rows->execute();
$es = $sel_rows->fetchObject();

if($es->facility_parent_folder!='')
 $parentid = str_replace('https://drive.google.com/drive/folders/', '', $es->facility_parent_folder); 
else
 echo "please mention the parent folder for facility";

//get query to create folder for each facility
if($es->facility_folder_query!='')
    $query=$es->facility_folder_query;

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
    $category='facility';
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
function Create_folder($email,$fac_id,$folder_name,$parentid,$current_id,$parent_stat,$action,$subfolder,$protocol){
    
    if($folder_name!='') {
       //To create folder in parent folder
        if($parentid!='' && $parent_stat=='yes'){
            //create folder api call
            $presultant= getdata($protocol.'/api/DriveSync/insert_folder_web/'.$email.'/'.$parentid.'/'.$folder_name);
            $val= explode(':',$presultant);
        }else {
            //create folder api call
            $presultant=getdata($protocol.'/api/DriveSync/createfolder_web/'.$email.'/'.$folder_name);
            $val= explode(':',$presultant); 
        }
        if($val[0]!==''){
          if($action=='create'){
                $link='https://drive.google.com/drive/folders/'.$val[0];
                global $sqlconfCentralDB;

                $sql = $sqlconfCentralDB->prepare("select facilityid from tbl_facility_custom_attr_1to1 where facilityid=$fac_id");
                $sql->execute();
                $data_fetch = $sql->fetchObject();
                $today = date("Y-m-d"); 
                if(!empty($data_fetch)){
                  
                    $update=$sqlconfCentralDB->prepare("UPDATE `tbl_facility_custom_attr_1to1` SET `updated_date`=:date,`facilityfolder`=:facilityfolder WHERE facilityid= :fid");
                    $update->bindParam(':date', $date);       
                    $update->bindParam(':facilityfolder', $val[0]); 
                    $update->bindParam(':fid', $fac_id);
                    $update->execute(); 
                }else {
                    $stmt1 = $sqlconfCentralDB->prepare("insert into tbl_facility_custom_attr_1to1 (facilityid,facilityfolder,created_date)values(:user,:ffolder,:date)");
                    $stmt1->bindParam(':user', $fac_id);       
                    $stmt1->bindParam(':ffolder', $val[0]); 
                    $stmt1->bindParam(':date', $today);
                    $stmt1->execute(); 
                }
                $ins_id12=$fac_id;
                $status="facility folder_created(auto Creation)";
                log_data($today,'',$email,'',$ins_id12,$link,'','',$status,'');
                
                //subfolders
               if($subfolder=='yes'){
                    $sub_stat="subfolder_created(during facility Creation in EMR)";
                    $subsql = $sqlconfCentralDB->prepare("select * from list_options where list_id='AllcareFacilitySubfolders'");
                    $subsql->execute();
                    $row=$subsql->rowCount();
                    $link1='';
                    if($row!=0){
                        while($subrow =$subsql->fetchObject()){
                            $subresultant=getdata($protocol.'/api/DriveSync/insert_folder_web/'.$email.'/'.$val[0].'/'.str_replace(" ","_",$subrow->title));
                            $subval= explode(':',$subresultant);
                            $link1.='https://drive.google.com/drive/folders/'.$subval[0]."||";
                        }
                        log_data($today,'',$email,'',$ins_id12,$link1,'','',$sub_stat,'');
                    }
                }
         }
      }    
       // return $val[0];
   }
}


//check facility parent folder with in the practice folder or not
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

//Get the list of all facility folders
//$list_folder=getdata($protocol.'/api/DriveSync/listall_folderid/'.$rs->notes.'/'.$parentid.'/folders');
//$child_folders = json_decode($chid_folders, TRUE);
//if(in_array('403',$child_folders)){
//    echo "user limit exceed:";
//    exit();
//}

////get list of facilities
$list_sql = $sqlconfCentralDB->prepare("select * from facility order by name");
$list_sql->execute();
while($data_row = $list_sql->fetchObject()){
    $fac_attr = $sqlconfCentralDB->prepare("select facilityfolder,facilityid from tbl_facility_custom_attr_1to1 where facilityid='$data_row->id'");
    $fac_attr->execute();
    $ds = $fac_attr->fetchObject();
    if($ds->facilityfolder==''){
        if($ds->facilityid!='')
            $id[]=$ds->facilityid;
        else
            $id[]=$data_row->id;
    } 
}
if(!empty($id)){
    foreach($id as $key => $val){
        $fsql = $sqlconfCentralDB->prepare($query." where  id=".$val);
        $fsql->execute();
        $frow = $fsql->fetchObject();
        $folder_name= str_replace(" ","_",$frow->fac_folder); 
        Create_folder($rs->notes,$val,$folder_name,$parentid,'',$parent_stat,'create',$es->facility_sub_folder,$protocol);
    }
}

?>