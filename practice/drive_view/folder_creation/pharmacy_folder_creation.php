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
    $category='pharmacy';
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
    global $sqlconfCentralDB;
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
                
                $sql = $sqlconfCentralDB->prepare("select pharmacyid from tbl_pharmacy_custom_attributes_1to1 where pharmacyid=$user_id");
                $sql->execute();
                $data_fetch = $sql->fetchObject();
                $today = date("Y-m-d"); 
                if(!empty($data_fetch)){
                 
                   
                   $update=$sqlconfCentralDB->prepare("UPDATE `tbl_pharmacy_custom_attributes_1to1` SET `update_date`=:updated_date,`pharmacy_folder`=:pharmacy_folder WHERE pharmacyid= :pharmacyid");
                    $update->bindParam(':updated_date', $today);       
                    $update->bindParam(':pharmacy_folder', $val[0]); 
                    $update->bindParam(':pharmacyid', $user_id);
                    $update->execute(); 
                }else {
                  
                  
                   $stmt1 = $sqlconfCentralDB->prepare("insert into tbl_pharmacy_custom_attributes_1to1 (pharmacyid,pharmacy_folder,created_date)values(:pharmacyid,:pharmacy_folder,:updated_date)");
                    $stmt1->bindParam(':pharmacyid', $user_id);
                    $stmt1->bindParam(':pharmacy_folder', $val[0]); 
                    $stmt1->bindParam(':updated_date', $today);       
                    $stmt1->execute(); 
                }
               $ins_id12=$user_id."(pharmacy id)"; 
               
               log_data($today,'',$email,'',$ins_id12,$link,'','','pharmacy folder_created(auto Creation)','');
               //subfolders
               if($subfolder=='yes'){
                    $subsql = $sqlconfCentralDB->prepare("select * from list_options where list_id='AllcarePharmacySubfolders'");
                    $subsql->execute();
                    $row=$subsql->rowCount();
                  
                    $link1='';
                     if($row!=0){
                    while($subrow = $subsql->fetchObject()){
                        $subresultant=getdata($protocol.'/api/DriveSync/insert_folder_web/'.$email.'/'.$val[0].'/'.str_replace(" ","_",$subrow->title));
                        $subval= explode(':',$subresultant);
                        $link1.='https://drive.google.com/drive/folders/'.$subval[0]."||";
                    }
                     
                     log_data($today,'',$email,'',$ins_id12,$link1,'','','subfolder_created(during pharmacy Creation in EMR)','');
                     }
                }
            }
        }    
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

//To get parent folder for  pharmacy
$sel_rows = $sqlconfCentralDB->prepare("select pharmacy_parent_folder,pharmacy_folder_query,practice_parent_folder,pharmacy_sub_folder from tbl_drivesync_authentication where email='$rs->notes' order by id desc");
$sel_rows->execute();
$es = $sel_rows->fetchObject();

if($es->pharmacy_parent_folder!='')
 $parentid = str_replace('https://drive.google.com/drive/folders/', '', $es->pharmacy_parent_folder); 
else
 echo "please mention the parent folder for pharmacy";

//get query to create folder for each facility
if($es->pharmacy_folder_query!='')
    $query=$es->pharmacy_folder_query;

//check user parent folder with in the practice folder or not
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

$list_sql = $sqlconfCentralDB->prepare("select * from pharmacies order by name ");
$list_sql->execute();
while($data_row = $list_sql->fetchObject()){
    $fac_attr = $sqlconfCentralDB->prepare("select pharmacy_folder,pharmacyid from tbl_pharmacy_custom_attributes_1to1 where pharmacyid='".$data_row->id."'");
    $fac_attr->execute();
    $ds = $fac_attr->fetchObject();
    if($ds->pharmacy_folder==''){
            if($ds->pharmacyid!='')
                $id[]=$ds->pharmacyid;
            else
                $id[]=$data_row->id;
    } 
}

if(!empty($id)){
    foreach($id as $key => $val){
         $fsql = $sqlconfCentralDB->prepare($query." where  id=".$val);
        $fsql->execute();
        $frow = $fsql->fetchObject();
        $data= str_replace(" ","",$frow->ph_folder); 
        $folder_name=  str_replace(" ","-",str_replace("#","-",str_replace("/","_",$data)));
        Create_folder($rs->notes,$val,$folder_name,$parentid,'',$parent_stat,'create',$es->pharmacy_sub_folder,$protocol);
    }
}
?>