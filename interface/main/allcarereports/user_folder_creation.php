<?php
require_once("../../globals.php");
?>
<script language='JavaScript' src="../../../library/js/jquery-1.4.3.min.js"></script>
<?php
// to get configured email
$sql = sqlStatement("select * from list_options where list_id='AllcareDriveSync' and option_id='email'");
$row = sqlFetchArray($sql);

$selection = sqlStatement("select * from tbl_drivesync_authentication where email='" . $row['notes'] . "' order by id desc");
$sel_rows = sqlFetchArray($selection);
if($sel_rows['user_parent_folder']!='')
 $parentid = str_replace('https://drive.google.com/drive/folders/', '', $sel_rows['user_parent_folder']);
else
 $parentid='root';   

if($sel_rows['provider_folder']!='')
 $query=$sel_rows['provider_folder'];

//to list  all folders from drive
$curl = curl_init();
$form_url = 'http://'.$_SERVER['HTTP_HOST'].'/api/DriveSync/listall_folderid/'.$row['notes'].'/'. str_replace('https://drive.google.com/drive/folders/', '', $sel_rows['practice_parent_folder']).'/folders';
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
$form_url = 'http://'.$_SERVER['HTTP_HOST'].'/api/DriveSync/listall_folderid/'.$row['notes'].'/'.$parentid.'/folders';
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
function Create_folder($email,$user_id,$folder_name,$parentid,$current_id,$parent_stat,$action){
    $form_url=''; $form_url2='';  $val='';
    
    
   if($folder_name!='') {
       //To create folder in parent folder
        if($parentid!='' && $parent_stat=='yes'){
            //create folder api call
            $curl = curl_init();
            $form_url2 = 'http://'.$_SERVER['HTTP_HOST'].'/api/DriveSync/insert_folder_web/'.$email.'/'.$parentid.'/'.$folder_name; 
            curl_setopt($curl,CURLOPT_URL, $form_url2);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);  
            $result = curl_exec($curl);
            $resultant = $result;
            curl_close($curl);
            $val= explode(':',$resultant);
            
        }else {
             //create folder api call
            $curl = curl_init();
            $form_url = 'http://'.$_SERVER['HTTP_HOST'].'/api/DriveSync/createfolder_web/'.$email.'/'.$folder_name; 
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
                $sql=sqlStatement("select userid from tbl_user_custom_attr_1to1 where userid=$user_id");
                $data_fetch=sqlFetchArray($sql);
                $today = date("Y-m-d"); 
                if(!empty($data_fetch)){
                   //echo  "UPDATE `tbl_user_custom_attr_1to1` SET `updated_date`='$today',`drive_sync_folder`='$val[0]' WHERE userid= $user_id"; echo "<br>";
                    $update=sqlStatement("UPDATE `tbl_user_custom_attr_1to1` SET `updated_date`='$today',`drive_sync_folder`='$val[0]' WHERE userid= $user_id");
                }else {
                 // echo "insert into tbl_user_custom_attr_1to1 (userid,drive_sync_folder,created_date)values($user_id,'".$val[0]."','$today')"; echo "<br>"; 
                    $ins=sqlStatement("insert into tbl_user_custom_attr_1to1 (userid,drive_sync_folder,created_date)values($user_id,'".$val[0]."','$today')"); 
                }
                $ins_id12=$id."(user id)";
              $ins_log=sqlStatement("insert into DriveSync_log (date,user,email,encounter,patient_id,google_folder,file_name ,file_id,status,watsID)values(now(),'".$_SESSION['authUser']."','".$email."','','".$ins_id12."','$link','','','user folder_created(auto Creation)','')");
            }else if($action=='change') {
                
                //list files from old formatted folder 
                $curl = curl_init();
                $form_url = 'http://'.$_SERVER['HTTP_HOST'].'/api/DriveSync/listall_folderid/'.$email.'/'.$current_id.'/all';
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
                        $form_url = 'http://'.$_SERVER['HTTP_HOST'].'/api/DriveSync/movefile_web/'.$email.'/'.$value['id'].'/'.$val[0];
                        curl_setopt($curl,CURLOPT_URL, $form_url);
                        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);  
                        $result_files = curl_exec($curl);
                        $result_files;
                        curl_close($curl);

                    }
                     if($result_files=='sucess'){
                        //delete old folder
                        $curl = curl_init();
                        $form_url = 'http://'.$_SERVER['HTTP_HOST'].'/api/DriveSync/deletefile_web/'.$email.'/'.$current_id; 
                        curl_setopt($curl,CURLOPT_URL, $form_url);
                        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);  
                        $result_files = curl_exec($curl);
                        $result_files;
                        curl_close($curl);

                        $link='https://drive.google.com/drive/folders/'.$val[0];
                        //$ins=sqlStatement("update tbl_user_custom_attr_1to1 SET drive_sync_folder='$val[0]' where userid='$user_id'");
                        $sql=sqlStatement("select userid from tbl_user_custom_attr_1to1 where userid=$user_id");
                        $data_fetch=sqlFetchArray($sql);
                        if(!empty($data_fetch)){
                              //echo "UPDATE `tbl_user_custom_attr_1to1` SET `updated_date`='$today',`drive_sync_folder`='$val[0]' WHERE userid= $user_id";
                              $update=sqlStatement("UPDATE `tbl_user_custom_attr_1to1` SET `updated_date`='$today',`drive_sync_folder`='$val[0]' WHERE userid= $user_id");
                        }else {
                             $ins=sqlStatement("insert into tbl_user_custom_attr_1to1 (userid,drive_sync_folder,created_date)values($user_id,'".$val[0]."','$today')");
                        }
                      
                    }
                }else {
                    $curl = curl_init();
                    $form_url = 'http://'.$_SERVER['HTTP_HOST'].'/api/DriveSync/deletefile_web/'.$email.'/'.$current_id;
                    curl_setopt($curl,CURLOPT_URL, $form_url);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);  
                    $result_files = curl_exec($curl);
                    $result_files;
                    curl_close($curl);

                    $link='https://drive.google.com/drive/folders/'.$val[0];
                    //$ins=sqlStatement("update tbl_user_custom_attr_1to1 SET drive_sync_folder='$val[0]' where userid='$user_id'");
                    $sql=sqlStatement("select userid from tbl_user_custom_attr_1to1 where userid=$user_id");
                    $data_fetch=sqlFetchArray($sql);
                    if(!empty($data_fetch)){
                          //echo "UPDATE `tbl_user_custom_attr_1to1` SET `updated_date`='$today',`drive_sync_folder`='$val[0]' WHERE userid= $user_id";
                          $update=sqlStatement("UPDATE `tbl_user_custom_attr_1to1` SET `updated_date`='$today',`drive_sync_folder`='$val[0]' WHERE userid= $user_id");
                    }else {
                         $ins=sqlStatement("insert into tbl_user_custom_attr_1to1 (userid,drive_sync_folder,created_date)values($user_id,'".$val[0]."','$today')");
                    }
                    $ins_id12=$user_id."(user id)";
                     $ins_log=sqlStatement("insert into DriveSync_log (date,user,email,encounter,patient_id,google_folder,file_name ,file_id,status,watsID)values(now(),'".$_SESSION['authUser']."','".$email."','','".$ins_id12."','$link','','','user folder_created(auto Creation)','')");
                 }
                   $ins_id12=$user_id."(user id)";
                      $ins_log=sqlStatement("insert into DriveSync_log (date,user,email,encounter,patient_id,google_folder,file_name ,file_id,status,watsID)values(now(),'".$_SESSION['authUser']."','".$row['notes']."','','".$ins_id12."','$link','','','user folder_created(auto Creation)','')");
             }
        }    
        return $val[0];
   }
}



$fac=sqlStatement("select * from users where username!='' and (fname!='' or lname !='')  order by username");
while($row1=sqlFetchArray($fac)){
   
    $sel_custom=sqlStatement("select drive_sync_folder,userid from tbl_user_custom_attr_1to1 where userid='".$row1['id']."'");
    $row12=sqlFetchArray($sel_custom);
    if($_REQUEST['action']=='change'){
        if($row12['drive_sync_folder']!=''){
            if($row12['userid']!='')
            $id[]=$row12['userid']."$".$row12['drive_sync_folder'];
            
        } 
    }else {
        if($row12['drive_sync_folder']==''){
            if($row12['userid']!='')
            $id[]=$row12['userid'];
            else
             $id[]=$row1['id'];
        } 
    }
    
    
}


if(count($id)<50) $to=count($id); else $to=50;

if($_REQUEST['action']=='create'){
    

foreach($id as $key => $val){
    if($key<$to){
        $query1=$query." and  id=".$val;
        $fsql=sqlStatement("$query1"); 
        $frow=sqlFetchArray($fsql);
        $folder_name= str_replace(" ","",$frow['provider_folder']);  
       
        Create_folder($row['notes'],$val,$folder_name,$parentid,'',$parent_stat,'create');
         $cnt=$key;
     }else {
        
        $cnt=$key;
        if($cnt==$to){
            $msg=count($id)-$to; 
            if($msg==0)
                echo $msg.":User Folders Sucessfully Created";
            else
                 echo $msg.":cont";
        }
        exit();
    }
  
}
if($cnt+1==$to){
    $msg=count($id)-$to; 
    if($msg==0)
    echo $msg.":User Folders Sucessfully Created";
    else
    echo $msg.":cont";
}  
}elseif($_REQUEST['action']=='change'){

    foreach($id as $key => $val){
        $res1=explode("$",$val);
     
        if($key<$to){
            $query1=$query." and  id=".$res1[0];
            $fsql=sqlStatement("$query1"); 
            $frow=sqlFetchArray($fsql);
            $folder_name= str_replace(" ","",$frow['provider_folder']); 
            if(in_array($res1[1],$child_folders)){

                     //to get folder info
                    $curl = curl_init();
                    $form_url = 'http://'.$_SERVER['HTTP_HOST'].'/api/DriveSync/getfileinfo_web/'.$row['notes'].'/'.$res1[1];
                    curl_setopt($curl,CURLOPT_URL, $form_url);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);  
                    $result = curl_exec($curl);
                    $resultant = $result;
                    curl_close($curl);
                    $folderinfo = json_decode($resultant, TRUE);
    //                echo $folderinfo['name'];
                    //check folderformats are same or not
                    if($folder_name!=$folderinfo['name']){

                       Create_folder($row['notes'],$res1[0],$folder_name,$parentid,$res1[1],$parent_stat,'change');

                    }
                }else {

                     Create_folder($row['notes'],$res1[0],$folder_name,$parentid,'',$parent_stat,'create');
                }
                 $cnt=$key;
        }
        else {

            $cnt=$key;
            $to;
            if($cnt==$to){
               $msg=count($id)-$to; 
                if($msg==0) {
                echo $msg.":User Folders Sucessfully updated"; 
                $sql=sqlStatement("update tbl_drivesync_authentication set folder_updation_status=1 where email='".$row['notes']."'");
                echo $msg.":User Folders Sucessfully updated"; 
                $cont="user:1";
                $sel=sqlStatement("select folder_updation_status from tbl_drivesync_authentication where email='".$row['notes']."'");
                $se11=sqlFetchArray($fsql);
                if($se11['folder_updation_status']!=''){
                     $ex=explode(",",$se11['folder_updation_status']);
                    $pos=strpos($ex[0],"users");
                    if($pos=='') $stat=$ex[0].",".$cont; 
                    else $stat=$ex[1].",".$cont; 
                }else{
                    $stat=$cont.",";
                }
                 $sql=sqlStatement("update tbl_drivesync_authentication set folder_updation_status='$stat' where email='".$row['notes']."'");
                }
                else
                     echo $msg.":cont";
            }
            exit();
        }
    }    
    if($cnt+1==$to){
        $msg=count($id)-$to; 
        if($msg==0) {
        echo $msg.":User Folders Sucessfully updated"; 
        $cont="user:1";
        $sel=sqlStatement("select folder_updation_status from tbl_drivesync_authentication where email='".$row['notes']."'");
        $se11=sqlFetchArray($fsql);
        if($se11['folder_updation_status']!=''){
            $ex=explode(",",$se11['folder_updation_status']);
            $pos=strpos($ex[0],"users");
            if($pos=='') $stat=$ex[0].",".$cont; 
            else $stat=$ex[1].",".$cont; 
        }else{
            $stat=$cont.",";
        }
        //echo "update tbl_drivesync_authentication set folder_updation_status='$stat' where email='".$row['notes']."'";
         $sql=sqlStatement("update tbl_drivesync_authentication set folder_updation_status='$stat' where email='".$row['notes']."'");
        }
        else
        echo $msg.":cont";
    }  
}
?>