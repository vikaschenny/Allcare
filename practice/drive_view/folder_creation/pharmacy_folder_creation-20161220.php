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
include_once('../../../interface/globals.php');
ini_set('post_max_size', '1000M');
ini_set('upload_max_filesize', '200M');
ini_set('max_execution_time', 600); 


if(isset($_SERVER['HTTPS'])){
        $protocol = ($_SERVER['HTTPS'] && $_SERVER['HTTPS'] != "off") ? "https" : "http";
}else{
        $protocol = 'http';
}
$protocol=$protocol."://" . $_SERVER['SERVER_NAME'];

// to get configured email
$sql = sqlStatement("select * from list_options where list_id='AllcareDriveSync' and option_id='email'");
$row = sqlFetchArray($sql);

$selection = sqlStatement("select * from tbl_drivesync_authentication where email='" . $row['notes'] . "' order by id desc");
$sel_rows = sqlFetchArray($selection);
if($sel_rows['user_parent_folder']!='')
 $parentid = str_replace('https://drive.google.com/drive/folders/', '', $sel_rows['pharmacy_parent_folder']);
else
 $parentid='root';   
//to get folder query
if($sel_rows['pharmacy_folder_query']!='')
 $query=$sel_rows['pharmacy_folder_query'];

//to list  all folders from drive
$curl = curl_init();
$form_url = $protocol.'/api/DriveSync/listall_folderid/'.$row['notes'].'/'. str_replace('https://drive.google.com/drive/folders/', '', $sel_rows['practice_parent_folder']).'/folders';
curl_setopt($curl,CURLOPT_URL, $form_url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);  
$result1 = curl_exec($curl);
$resultant1 = $result1;
curl_close($curl);
$all_folders = json_decode($resultant1, TRUE);
echo "<pre>";print_r($all_folders); echo"</pre>";
if(is_array($all_folders)){
   if(in_array($parentid,$all_folders)){
        $parent_stat='yes'; 
    } 
}else {
    echo "internal server error:"; 
    exit();
}

$curl = curl_init();
$form_url = $protocol.'/api/DriveSync/listall_folderid/'.$row['notes'].'/'.$parentid.'/folders';
curl_setopt($curl,CURLOPT_URL, $form_url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);  
$result2 = curl_exec($curl);
$resultant2 = $result2;
curl_close($curl);
$child_folders = json_decode($resultant2, TRUE);
if(in_array('403',$child_folders[0])){
    echo "user limit exceed";
    exit();
}

//function to create folder
function Create_folder($email,$user_id,$folder_name,$parentid,$current_id,$parent_stat,$action,$subfolder,$protocol){
    $form_url=''; $form_url2='';  $val='';
    if($folder_name!='') {
       //To create folder in parent folder
        if($parentid!='' && $parent_stat=='yes'){
            //create folder api call
            $curl = curl_init();
            $form_url2 = $protocol.'/api/DriveSync/insert_folder_web/'.$email.'/'.$parentid.'/'.trim($folder_name,"-");
            curl_setopt($curl,CURLOPT_URL, $form_url2);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);  
            $result = curl_exec($curl);
            $resultant = $result;
            curl_close($curl);
            $val= explode(':',$resultant);
            
        }else {
             //create folder api call
            $curl = curl_init();
            $form_url = $protocol.'/api/DriveSync/createfolder_web/'.$email.'/'.trim($folder_name,"-"); 
            curl_setopt($curl,CURLOPT_URL, $form_url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);  
            $result = curl_exec($curl);
            $resultant = $result;
            curl_close($curl);
            $val= explode(':',$resultant); 
           
        }
      
        if($val[0]!==''){
            if($action=='create'){
                $link='https://drive.google.com/drive/folders/'.$val[0];
                $sql=sqlStatement("select pharmacyid from tbl_pharmacy_custom_attributes_1to1 where pharmacyid=$user_id");
                $data_fetch=sqlFetchArray($sql);
                $today = date("Y-m-d"); 
                if(!empty($data_fetch)){
                 
                   $update=sqlStatement("UPDATE `tbl_pharmacy_custom_attributes_1to1` SET `update_date`='$today',`pharmacy_folder`='$val[0]' WHERE pharmacyid= $user_id");
                }else {
                  
                  $ins=sqlStatement("insert into tbl_pharmacy_custom_attributes_1to1 (pharmacyid,pharmacy_folder,created_date)values($user_id,'".$val[0]."','$today')");
                }
               $ins_id12=$user_id."(pharmacy id)"; 
               $ins_log=sqlStatement("insert into DriveSync_log (date,user,email,encounter,patient_id,google_folder,file_name ,file_id,status,watsID)values(now(),'".$_SESSION['authUser']."','".$email."','','".$ins_id12."','$link','','','pharmacy folder_created(auto Creation)','')");
               //subfolders
               if($subfolder=='yes'){
                    $subsql = sqlStatement("select * from list_options where list_id='AllcarePharmacySubfolders'");
                     $row=mysql_num_rows($subsql);
                    $link1='';
                     if($row!=0){
                    while($subrow = sqlFetchArray($subsql)){
                        $curl = curl_init();
                        $form_url2 = $protocol.'/api/DriveSync/insert_folder_web/'.$email.'/'.$val[0].'/'.str_replace(" ","_",$subrow['title']);
                        curl_setopt($curl,CURLOPT_URL, $form_url2);
                        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);  
                        $result = curl_exec($curl);
                        $subresultant = $result;
                        curl_close($curl);
                        $subval= explode(':',$subresultant);
                        $link1.='https://drive.google.com/drive/folders/'.$subval[0]."||";
                    }
                     $ins_log=sqlStatement("insert into DriveSync_log (date,user,email,encounter,patient_id,google_folder,file_name,file_id,status,watsID)values(now(),'".$_SESSION['authUser']."','".$row['notes']."','','".$newpid."','$link1','','','subfolder_created(during patient Creation in EMR)','')");
                     }
                }
            }else if($action=='change') {
                //list files from old formatted folder 
                $curl = curl_init();
                $form_url = $protocol.'/api/DriveSync/listall_folderid/'.$email.'/'.$current_id.'/all';
                curl_setopt($curl,CURLOPT_URL, $form_url);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);  
                $result_files = curl_exec($curl);
                $resultantfiles = $result_files;
                curl_close($curl);
                $folderfiles = json_decode($resultantfiles, TRUE);
                if(!empty($folderfiles)) { 
                     foreach($folderfiles as  $value){
                       // print_r($val);
                        $curl = curl_init();
                        $form_url = $protocol.'/api/DriveSync/movefile_web/'.$email.'/'.$value['id'].'/'.$val[0];
                        curl_setopt($curl,CURLOPT_URL, $form_url);
                        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);  
                        $result_files = curl_exec($curl);
                        $result_files;
                        curl_close($curl);

                    }
                     if($result_files=='sucess'){
                        //delete old folder
                        $curl = curl_init();
                        $form_url = $protocol.'/api/DriveSync/deletefile_web/'.$email.'/'.$current_id; 
                        curl_setopt($curl,CURLOPT_URL, $form_url);
                        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);  
                        $result_files = curl_exec($curl);
                        $result_files;
                        curl_close($curl);

                        $link='https://drive.google.com/drive/folders/'.$val[0];
                       
                        $sql=sqlStatement("select pharmacyid from tbl_pharmacy_custom_attributes_1to1 where pharmacyid=$user_id");
                        $data_fetch=sqlFetchArray($sql);
                        if(!empty($data_fetch)){
                            
                              $update=sqlStatement("UPDATE `tbl_pharmacy_custom_attributes_1to1` SET `update_date`='$today',`pharmacy_folder`='$val[0]' WHERE pharmacyid= $user_id");
                        }else {
                            
                             $ins=sqlStatement("insert into tbl_pharmacy_custom_attributes_1to1 (pharmacyid,pharmacy_folder,created_date)values($user_id,'".$val[0]."','$today')");
                        }
                      
                    }
                }else {
                    $curl = curl_init();
                    $form_url = $protocol.'/api/DriveSync/deletefile_web/'.$email.'/'.$current_id;
                    curl_setopt($curl,CURLOPT_URL, $form_url);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);  
                    $result_files = curl_exec($curl);
                    $result_files;
                    curl_close($curl);

                    $link='https://drive.google.com/drive/folders/'.$val[0];
                    
                    $sql=sqlStatement("select pharmacyid from tbl_pharmacy_custom_attributes_1to1 where pharmacyid=$user_id");
                    $data_fetch=sqlFetchArray($sql);
                    if(!empty($data_fetch)){
                          
                          $update=sqlStatement("UPDATE `tbl_pharmacy_custom_attributes_1to1` SET `update_date`='$today',`pharmacy_folder`='$val[0]' WHERE pharmacyid= $user_id");
                    }else {
                         $ins=sqlStatement("insert into tbl_pharmacy_custom_attributes_1to1 (pharmacyid,pharmacy_folder,created_date)values($user_id,'".$val[0]."','$today')");
                    }
                 }
                 $ins_id12=$user_id."(pharmacy id)";
                 $ins_log=sqlStatement("insert into DriveSync_log (date,user,email,encounter,patient_id,google_folder,file_name ,file_id,status,watsID)values(now(),'".$_SESSION['authUser']."','".$email."','','".$ins_id12."','$link','','','pharmacy folder_created(auto Creation)','')");
             }
        }    
   }
}

$fac=sqlStatement("select * from pharmacies order by name ");
while($row1=sqlFetchArray($fac)){
   
    $sel_custom=sqlStatement("select pharmacy_folder,pharmacyid from tbl_pharmacy_custom_attributes_1to1 where pharmacyid='".$row1['id']."'");
    $row12=sqlFetchArray($sel_custom);
    if($row12['pharmacy_folder']==''){
        if($row12['pharmacyid']!='')
        $id[]=$row12['pharmacyid'];
        else
         $id[]=$row1['id'];
    } 
    
}
if(count($id)<50) $to=count($id); else $to=50;
foreach($id as $key => $val){
    if($key<$to){
        $query1=$query." where  id=".$val;
        $fsql=sqlStatement("$query1"); 
        $frow=sqlFetchArray($fsql);
        $data = str_replace("\t", "", $frow['ph_folder']);
        $folder_name=  str_replace(" ","-",str_replace("#","-",str_replace("/","_",$data)));
        Create_folder($row['notes'],$val,$folder_name,$parentid,'',$parent_stat,'create',$sel_rows['pharmacy_sub_folder'],$protocol);
        $cnt=$key;
    }else {
        $cnt=$key;
        if($cnt==$to){
            $msg=count($id)-$to;
            if($msg==0) 
             echo $msg.":Pharmacy Folders Sucessfully Created";
            else {
                echo $msg.":cont";
            }
        }
        exit();
    }
  
}
if($cnt+1==$to){
        $msg=count($id)-$to; 
        if($msg==0) {
       echo $msg.":Pharmacy Folders Sucessfully Created";
        
        }
        else
        echo $msg.":cont";
    } 
?> 