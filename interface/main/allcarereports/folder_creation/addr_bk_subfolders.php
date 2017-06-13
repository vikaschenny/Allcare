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

//To get parent folder for  Addressbook
$sel_rows = $sqlconfCentralDB->prepare("select addrbk_parent_folder,org_folder_query,practice_parent_folder,addrbk_sub_folder from tbl_drivesync_authentication where email='$rs->notes' order by id desc");
$sel_rows->execute();
$es = $sel_rows->fetchObject();

if($es->addrbk_parent_folder!='')
 $parentid = str_replace('https://drive.google.com/drive/folders/', '', $es->addrbk_parent_folder); 
else
 echo "please mention the parent folder for Address Book";

//get query to create folder for each Organisation
if($es->org_folder_query!='')
    $query_org=$es->org_folder_query;

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
    $category='agency';              
    global $sqlconfCentralDB;
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

function Create_folder($email,$userid,$folder_name,$parentid,$current_id,$parent_stat,$action,$type,$user_arr,$subfolder,$protocol){
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
            if($action=='create' && $type=='abook'){
                $link='https://drive.google.com/drive/folders/'.$val[0];
                $today = date("Y-m-d"); 
                    $ins_id12=$folder_name."(Agency Type)";
                    $status="Agency type folder_created(auto Creation)";
                    log_data($today,'',$email,'',$ins_id12,$link,'','',$status,'');
            }else if($action=='create' && $type=='org'){
                $link='https://drive.google.com/drive/folders/'.$val[0];
                
                $sql=$sqlconfCentralDB->prepare("select addrbk_folder from tbl_addrbk_custom_attr_1to1 where addrbk_type_id=$userid");
                $sql->execute();
                $data_fetch= $sql->fetchObject();
                $today = date("Y-m-d"); 
                if(!empty($data_fetch)){
                    $update=$sqlconfCentralDB->prepare("UPDATE `tbl_addrbk_custom_attr_1to1` SET `updated_date`=:date,`addrbk_folder`=:addrbk_folder, addressbook_folder=:addressbook_folder WHERE addrbk_type_id= :uid");
                    $update->bindParam(':date', $today);       
                    $update->bindParam(':addrbk_folder', $val[0]); 
                    $update->bindParam(':addressbook_folder', $parentid); 
                    $update->bindParam(':uid', $userid);
                    $update->execute(); 
                }else {
                    $ins=$sqlconfCentralDB->prepare("insert into tbl_addrbk_custom_attr_1to1 (addrbk_type_id,addrbk_folder,created_date,addressbook_folder)values(:addrbk_type_id,:ffolder,:date,:parentid)");
                    $ins->bindParam(':addrbk_type_id', $userid);       
                    $ins->bindParam(':ffolder', $val[0]); 
                    $ins->bindParam(':date', $today);
                    $ins->bindParam(':parentid', $parentid);
                    $ins->execute(); 
                }
                $ins_id12=$userid;
                log_data($today,'',$email,'',$ins_id12,$link,'','',"organisation folder_created(auto Creation)",'');
                if($subfolder=='yes'){
                    $subsql = $sqlconfCentralDB->prepare("select * from list_options where list_id='AllcareAddrbkSubfolders'");
                    $subsql->execute();
                    $link1='';
                    $row=$subsql->rowCount();
                    
                    if($row!=0){
                    while($subrow = $subsql->fetchObject()){
                        $subresultant=getdata($protocol.'/api/DriveSync/insert_folder_web/'.$email.'/'.$val[0].'/'.str_replace(" ","_",$subrow->title));
                        $subval= explode(':',$subresultant);
                        $link1.='https://drive.google.com/drive/folders/'.$subval[0]."||";
                    }
                    log_data($today,'',$email,'',$ins_id12,$link1,'','','subfolder_created(during agency Creation in EMR)','');
                    
                    }
                }
            }
        }    
        return $val[0];
   }
}

//check Agency parent folder with in the practice folder or not

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
 $today = date("Y-m-d"); 
//get list of address book
$list_sql = $sqlconfCentralDB->prepare("select option_id ,title from list_options where list_id='abook_type' and option_id='hha' order by `seq` asc ");
$list_sql->execute();
while($data_row = $list_sql->fetchObject()){
    $abook_type1[$data_row->option_id]=str_replace(" ","_",$data_row->title);
    $query1=$query_org."  where abook_type='".$data_row->option_id."'  "; 
    $user= $sqlconfCentralDB->prepare($query1);
    $user->execute();
    $i=0;
    while($urow=$user->fetchObject()) {
        if($urow->org!=''){
             $folder_name=str_replace(" ","_",$urow->org);
            //echo "select addressbook_folder,addrbk_folder from tbl_addrbk_custom_attr_1to1 where addrbk_type_id='".$urow->id."'";
            $sel_custom=$sqlconfCentralDB->prepare("select addressbook_folder,addrbk_folder from tbl_addrbk_custom_attr_1to1 where addrbk_type_id='".$urow->id."' and addrbk_type_id NOT IN ('134','135','150')");
            $sel_custom->execute();
            $row12=$sel_custom->fetchObject();
//            echo $row12->addressbook_folder."==".$row12->addrbk_folder;
//            echo "<br>";
            if($es->addrbk_sub_folder=='yes'){
                $subsql = $sqlconfCentralDB->prepare("select * from list_options where list_id='AllcareAddrbkSubfolders'");
                $subsql->execute();
                $link1='';
                $row=$subsql->rowCount();

                if($row!=0){
                while($subrow = $subsql->fetchObject()){
                    $subresultant=getdata($protocol.'/api/DriveSync/insert_folder_web/'.$rs->notes.'/'.$row12->addrbk_folder.'/'.str_replace(" ","_",$subrow->title));
                    $subval= explode(':',$subresultant);
                    $link1.='https://drive.google.com/drive/folders/'.$subval[0]."||";
                }
                log_data($today,'',$rs->notes,'',$urow->id,$link1,'','','subfolder_created(during agency Creation in EMR)','');

                }
            }
        }
        $i++;
    }
}
//echo $i;
//if(!empty($org)){
//    foreach($org as $key => $val){
//        $n=explode("*",$key);
//       //if($n[0]=='Imaging_Service'){
//         if(in_array($n[0],$abook_type)){
//              $result=Create_folder($rs->notes,'',$key,$parentid,'',$parent_stat,'create','abook','','',$protocol);
//            }else{
//                 $result=$n[1];
//            }
//            foreach($val as $k1 => $v1){
//               $result2=Create_folder($rs->notes,$k1,$v1,$result,'',$parent_stat,'create','org','',$es->addrbk_sub_folder,$protocol);
//            }
//        //}
//    }
//}


?>