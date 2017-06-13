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
set_time_limit(0);
$ignoreAuth=true; // ignore the standard authentication for a regular OpenEMR user
include_once('../../../interface/globals.php');
// to get configured email
$sql = sqlStatement("select * from list_options where list_id='AllcareDriveSync' and option_id='email'");
$row = sqlFetchArray($sql);

$selection = sqlStatement("select * from tbl_drivesync_authentication where email='" . $row['notes'] . "' order by id desc");
$sel_rows = sqlFetchArray($selection);
if($sel_rows['user_parent_folder']!='')
  $parentid = str_replace('https://drive.google.com/drive/folders/', '', $sel_rows['parent_folder']); 
else
  $parentid='root';   

if($sel_rows['patient_folder']!='')
 $query=$sel_rows['patient_folder'];
$protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === true ? 'https://' : 'https://';
//to list  all folders from drive
$curl = curl_init();
$form_url = $protocol.$_SERVER['HTTP_HOST'].'/api/DriveSync/listall_folderid/'.$row['notes'].'/'. str_replace('https://drive.google.com/drive/folders/', '', $sel_rows['practice_parent_folder']).'/folders';
curl_setopt($curl,CURLOPT_URL, $form_url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);  
$result1 = curl_exec($curl); 
$resultant1 = $result1;
curl_close($curl);
$all_folders = json_decode($resultant1, TRUE);

if(is_array($all_folders)){
   if(in_array($parentid,$all_folders)){
        $parent_stat='yes'; 
    } 
}else {
    echo "internal server error:"; 
    exit();
}
$curl = curl_init();
$form_url = $protocol.$_SERVER['HTTP_HOST'].'/api/DriveSync/listall_folderid/'.$row['notes'].'/'.$parentid.'/folders';
curl_setopt($curl,CURLOPT_URL, $form_url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);  
$result2 = curl_exec($curl);
$resultant2 = $result2;
curl_close($curl);
$child_folders = json_decode($resultant2, TRUE);
if(in_array('403',$child_folders[0])){
    echo "user limit exceed:";
    exit();
}
//function to create folder
function Create_folder($email,$user_id,$folder_name,$parentid,$current_id,$parent_stat,$action,$subfolder){
    $form_url=''; $form_url2='';  $val='';
    
    
   if($folder_name!='') {
       //To create folder in parent folder
        if($parentid!='' && $parent_stat=='yes'){
            //create folder api call
            $curl = curl_init();
            $form_url2 = $protocol.$_SERVER['HTTP_HOST'].'/api/DriveSync/insert_folder_web/'.$email.'/'.$parentid.'/'.$folder_name; 
            curl_setopt($curl,CURLOPT_URL, $form_url2);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);  
            $result = curl_exec($curl);
            $resultant = $result;
            curl_close($curl);
            $val= explode(':',$resultant);
            //echo $val[0] ; echo "<br>"; echo "parent";
        }else {
             //create folder api call
            $curl = curl_init();
            $form_url = $protocol.$_SERVER['HTTP_HOST'].'/api/DriveSync/createfolder_web/'.$email.'/'.$folder_name; 
            curl_setopt($curl,CURLOPT_URL, $form_url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);  
            $result = curl_exec($curl);
            $resultant = $result;
            curl_close($curl);
            $val= explode(':',$resultant); 
           // echo $val[0]; echo "drive";
        }
        if($val[0]!==''){
            if($action=='create'){
                $link='https://drive.google.com/drive/folders/'.$val[0];
                $sql=sqlStatement("select patient_folder from patient_data where pid=$user_id");
                $data_fetch=sqlFetchArray($sql);
                $today = date("Y-m-d"); 
                if(!empty($data_fetch)){
                   
                    $update=sqlStatement("UPDATE `patient_data` SET `patient_folder`='$val[0]' WHERE pid= $user_id");
                }else {
                  
                    $ins=sqlStatement("insert into patient_data (pid,patient_folder)values($user_id,'".$val[0]."')");
                }
               
               $ins_log=sqlStatement("insert into DriveSync_log (date,user,email,encounter,patient_id,google_folder,file_name ,file_id,status,watsID)values(now(),'".$_SESSION['authUser']."','".$email."','','$user_id','$link','','','patient folder_created(auto Creation)','')");
               //subfolders
               if($subfolder=='yes'){
                    $subsql = sqlStatement("select * from list_options where list_id='AllcarePatientSubfolders'");
                    $link1='';
                    $row=mysql_num_rows($subsql);
                    if($row!=0){
                    while($subrow = sqlFetchArray($subsql)){
                        $curl = curl_init();
                        $form_url2 = $protocol.$_SERVER['HTTP_HOST'].'/api/DriveSync/insert_folder_web/'.$email.'/'.$val[0].'/'.str_replace(" ","_",$subrow['title']);
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
                $form_url = $protocol.$_SERVER['HTTP_HOST'].'/api/DriveSync/listall_folderid/'.$email.'/'.$current_id.'/all';
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
                        $form_url = $protocol.$_SERVER['HTTP_HOST'].'/api/DriveSync/movefile_web/'.$email.'/'.$value['id'].'/'.$val[0];
                        curl_setopt($curl,CURLOPT_URL, $form_url);
                        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);  
                        $result_files = curl_exec($curl);
                        $result_files;
                        curl_close($curl);

                    }
                     if($result_files=='sucess'){
                        //delete old folder
                        $curl = curl_init();
                        $form_url = $protocol.$_SERVER['HTTP_HOST'].'/api/DriveSync/deletefile_web/'.$email.'/'.$current_id; 
                        curl_setopt($curl,CURLOPT_URL, $form_url);
                        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);  
                        $result_files = curl_exec($curl);
                        $result_files;
                        curl_close($curl);

                        $link='https://drive.google.com/drive/folders/'.$val[0];
                       
                        $sql=sqlStatement("select patient_folder from patient_data where pid=$user_id");
                        $data_fetch=sqlFetchArray($sql);
                        if(!empty($data_fetch)){
                             
                              $update=sqlStatement("UPDATE `patient_data` SET `patient_folder`='$val[0]' WHERE pid= $user_id");
                        }else {
                             $ins=sqlStatement("insert into patient_data (pid,patient_folder)values($user_id,'".$val[0]."')");
                        }
                     $ins_log=sqlStatement("insert into DriveSync_log (date,user,email,encounter,patient_id,google_folder,file_name ,file_id,status,watsID)values(now(),'".$_SESSION['authUser']."','".$email."','','','$link','','','patient folder_created(auto Creation)','')");
                    }
                }else {
                    $curl = curl_init();
                    $form_url = $protocol.$_SERVER['HTTP_HOST'].'/api/DriveSync/deletefile_web/'.$email.'/'.$current_id;
                    curl_setopt($curl,CURLOPT_URL, $form_url);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);  
                    $result_files = curl_exec($curl);
                    $result_files;
                    curl_close($curl);

                    $link='https://drive.google.com/drive/folders/'.$val[0];
                    
                    $sql=sqlStatement("select patient_folder from patient_data where pid=$user_id");
                    $data_fetch=sqlFetchArray($sql);
                    if(!empty($data_fetch)){
                         
                         $update=sqlStatement("UPDATE `patient_data` SET `patient_folder`='$val[0]' WHERE pid= $user_id");
                    }else {
                         $ins=sqlStatement("insert into patient_data (pid,patient_folder)values($user_id,'".$val[0]."')");
                    }
                 }
             }
        }    
        return $val[0];
   }
}
 


 
$fac=sqlStatement("select patient_folder,pid from patient_data where patient_folder='' order by id desc");
$num=mysql_num_rows($fac);
if($num<500) $to=$num; else $to=50;
$i=0;
while($row1=sqlFetchArray($fac)){
   if($i<$to){
       $query1=$query." where  pid=".$row1['pid'];
        $fsql=sqlStatement("$query1"); 
        $frow=sqlFetchArray($fsql); 
      $folder_name= str_replace(" ","",$frow['Patient_folder']);  
        Create_folder($row['notes'],$row1['pid'],$folder_name,$parentid,'',$parent_stat,'create',$sel_rows['patient_sub_folder']);
         $cnt=$i;
   }else {
        $cnt=$i;
            if($i==$to){
                $msg=$num-$to;
                if($msg==0)
                 echo $msg.":Patient Folders Sucessfully Created";
                else
                     echo $msg.":cont";
            }
            exit();
   }
   $i++;
}
if($cnt+1==$to){
        $msg=$num-$to; 
        if($msg==0)
        echo $msg.":Patient Folders  Sucessfully Created";
        else
        echo $msg.":cont";
    }  
?>