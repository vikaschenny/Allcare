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
 $parentid = str_replace('https://drive.google.com/drive/folders/', '', $sel_rows['addrbk_parent_folder']);
else
 $parentid='root';   



if($sel_rows['org_folder_query']!='')
 $query_org=$sel_rows['org_folder_query'];

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

//to list  all folders from drive
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
function Create_folder($email,$userid,$folder_name,$parentid,$current_id,$parent_stat,$action,$type,$user_arr){
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
            //echo $val[0] ; echo "<br>"; echo "parent";
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
           // echo $val[0]; echo "drive";
        }
        if($val[0]!==''){
            if($action=='create' && $type=='org'){
               
                $link='https://drive.google.com/drive/folders/'.$val[0];
                
                $sql=sqlStatement("select addrbk_folder from tbl_addrbk_custom_attr_1to1 where addrbk_type_id=$userid");
                $data_fetch=sqlFetchArray($sql);
                $today = date("Y-m-d"); 
                if(!empty($data_fetch)){
                 // echo  "UPDATE `tbl_addrbk_custom_attr_1to1` SET `updated_date`='$today',`addrbk_folder`='$val[0]', addressbook_folder='$parentid' WHERE addrbk_type_id= $userid"; echo "<br>"; 
                    $update=sqlStatement("UPDATE `tbl_addrbk_custom_attr_1to1` SET `updated_date`='$today',`addrbk_folder`='$val[0]', addressbook_folder='$parentid' WHERE addrbk_type_id= $userid");
                }else {
                   // echo "insert into tbl_facility_custom_attr_1to1 (facilityid,facilityfolder,created_date)values($user_id,'".$val[0]."','$today')"; echo "<br>";
                    $ins=sqlStatement("insert into tbl_addrbk_custom_attr_1to1 (addrbk_type_id,addrbk_folder,created_date,addressbook_folder)values($userid,'".$val[0]."','$today','$parentid')");
                }
              $ins_log=sqlStatement("insert into DriveSync_log (date,user,email,encounter,patient_id,google_folder,file_name ,file_id,status,watsID)values(now(),'".$_SESSION['authUser']."','".$email."','','','$link','','','organization folder_created(auto Creation)','')");
            }else if($action=='create' && $type=='abook'){
                 $link='https://drive.google.com/drive/folders/'.$val[0];
                 foreach($user_arr as $value){
                     $sql=sqlStatement("select addressbook_folder from tbl_addrbk_custom_attr_1to1 where addrbk_type_id=$value");
                    $data_fetch=sqlFetchArray($sql);
                    $today = date("Y-m-d"); 
                    if(!empty($data_fetch)){
                 //   echo  "UPDATE `tbl_addrbk_custom_attr_1to1` SET `updated_date`='$today', addressbook_folder='".$val[0]."' WHERE addrbk_type_id= $value";  echo "<br>"; 
                       $update=sqlStatement("UPDATE `tbl_addrbk_custom_attr_1to1` SET `updated_date`='$today', addressbook_folder='".$val[0]."' WHERE addrbk_type_id= $value");
                    }else {
                       // echo "insert into tbl_facility_custom_attr_1to1 (facilityid,facilityfolder,created_date)values($user_id,'".$val[0]."','$today')"; echo "<br>";
                        $ins=sqlStatement("insert into tbl_addrbk_custom_attr_1to1 (addrbk_type_id,created_date,addressbook_folder)values($value,'$today','".$val[0]."')");
                    }
                 }
                 $ins_log=sqlStatement("insert into DriveSync_log (date,user,email,encounter,patient_id,google_folder,file_name ,file_id,status,watsID)values(now(),'".$_SESSION['authUser']."','".$email."','','','$link','','','address book folder_created(auto Creation)','')");
            }
        }    
        return $val[0];
   }
}


$addr=sqlStatement("select option_id  from list_options where list_id='abook_type' order by `seq` asc ");
while($addr_urow=sqlFetchArray($addr)){
    $abook_type1[]=$addr_urow['option_id'];
    $query1=$query_org."  where abook_type='".$addr_urow['option_id']."'  "; 
    $user=sqlStatement($query1);
   
    $i=0;
    while($urow=sqlFetchArray($user)) {
        $folder_name=str_replace(" ","_",$urow['organization']);
       // echo "select addressbook_folder,addrbk_folder from tbl_addrbk_custom_attr_1to1 where addrbk_type_id='".$urow['id']."'";
        $sel_custom=sqlStatement("select addressbook_folder,addrbk_folder from tbl_addrbk_custom_attr_1to1 where addrbk_type_id='".$urow['id']."'");
        $row12=sqlFetchArray($sel_custom);
        if($row12['addressbook_folder']=='' && $i==0){
            $abook_type[]=$addr_urow['option_id'];
        }
        if($row12['addrbk_folder']==''){
           $org[$addr_urow['option_id']][$i]= $urow['id'];
           $usr_cnt[]=$urow['id'];
           
        }
    $i++;
    }
}
if($usr_cnt<50) $to=$usr_cnt; else $to=50;
 $result='';
 
foreach($abook_type1 as $val){
     $type=0;
     //  if($val=='ord_img'){
            $query1=$query_org." where abook_type='".$val."' "; 
            $user=sqlStatement($query1);
            $row_cnt=mysql_num_rows($user);
            $folder_name='';
            $i=0;
            while($urow=sqlFetchArray($user)){
                
              $folder_name=str_replace("/","_",str_replace(" ","_",$urow['org'])); 
              $sel_custom=sqlStatement("select addressbook_folder,addrbk_folder,addrbk_type_id from tbl_addrbk_custom_attr_1to1 where addrbk_type_id='".$urow['id']."' and addrbk_folder=''");
              $row12=sqlFetchArray($sel_custom);
              if(!empty($row12)){
                    if($i<=$to){
                       if($type==0 && in_array($val,$abook_type)){
                            $result=Create_folder($row['notes'],$urow['id'],$val,$parentid,'',$parent_stat,'create','abook',$org[$val]);
                       }
                       if($row12['addrbk_folder']==''){
                           $sel_custom1=sqlStatement("select addressbook_folder,addrbk_folder from tbl_addrbk_custom_attr_1to1 where addrbk_type_id='".$urow['id']."' and  addrbk_folder=''");
                           $row121=sqlFetchArray($sel_custom1);
                           $result2=Create_folder($row['notes'],$urow['id'],$folder_name,$row121['addressbook_folder'],'',$parent_stat,'create','org','');
                            $i++;
                       }
//                   
                    }
               else {
                     
                    $msg=count($usr_cnt)-$to; 
                    if($msg==0)
                    echo $msg.":Address Book Folders Sucessfully Created";
                    else
                         echo $msg.":cont";
                    exit();
                }
              }
             
              
               $type++;
            }
       //}
   
}
if($cnt+1==$to){
        $msg=count($id)-$to; 
        if($msg==0) {
      echo $msg.":Address Book Folders Sucessfully Created";
        
        }
        else
        echo $msg.":cont";
    } 
?>  