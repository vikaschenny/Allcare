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
 $parentid = str_replace('https://drive.google.com/drive/folders/', '', $sel_rows['ins_parent_folder']);
else
 $parentid='root';   

if($sel_rows['insurance_folder_query']!='')
 $query=$sel_rows['insurance_folder_query'];

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
    echo "user limit exceed:";
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
                $sql=sqlStatement("select insuranceid from tbl_inscomp_custom_attr_1to1 where insuranceid=$user_id");
                $data_fetch=sqlFetchArray($sql);
                $today = date("Y-m-d"); 
                if(!empty($data_fetch)){
                   //echo  "UPDATE `tbl_inscomp_custom_attr_1to1` SET `updated_date`='$today',`payer_folder`='$val[0]' WHERE insuranceid= $user_id"; echo "<br>";
                    $update=sqlStatement("UPDATE `tbl_inscomp_custom_attr_1to1` SET `updated_date`='$today',`payer_folder`='$val[0]' WHERE insuranceid= $user_id");
                }else {
                   // echo "insert into tbl_inscomp_custom_attr_1to1 (insuranceid,payer_folder,created_date)values($user_id,'".$val[0]."','$today')"; echo "<br>";
                    $ins=sqlStatement("insert into tbl_inscomp_custom_attr_1to1 (insuranceid,payer_folder,created_date)values($user_id,'".$val[0]."','$today')");
                }
                $ins_id12=$user_id."(insurance id)";
                $ins_log=sqlStatement("insert into DriveSync_log (date,user,email,encounter,patient_id,google_folder,file_name ,file_id,status,watsID)values(now(),'".$_SESSION['authUser']."','".$email."','','".$ins_id12."','$link','','','insurance folder_created(auto Creation)','')");
                // $ins_log=sqlStatement("insert into DriveSync_log (date,user,email,encounter,patient_id,google_folder,file_name ,file_id,status,watsID)values(now(),'".$_SESSION['authUser']."','".$row['notes']."','".$_REQUEST['encounter']."',$patient_id,'$link','','','folder_created','')");
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
                        //$ins=sqlStatement("update tbl_inscomp_custom_attr_1to1 SET payer_folder='$val[0]' where insuranceid='$user_id'");
                        $sql=sqlStatement("select insuranceid from tbl_inscomp_custom_attr_1to1 where insuranceid=$user_id");
                        $data_fetch=sqlFetchArray($sql);
                        if(!empty($data_fetch)){
                              //echo "UPDATE `tbl_inscomp_custom_attr_1to1` SET `updated_date`='$today',`payer_folder`='$val[0]' WHERE insuranceid= $user_id";
                              $update=sqlStatement("UPDATE `tbl_inscomp_custom_attr_1to1` SET `updated_date`='$today',`payer_folder`='$val[0]' WHERE insuranceid= $user_id");
                        }else {
                             $ins=sqlStatement("insert into tbl_inscomp_custom_attr_1to1 (insuranceid,payer_folder,created_date)values($user_id,'".$val[0]."','$today')");
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
                    //$ins=sqlStatement("update tbl_inscomp_custom_attr_1to1 SET payer_folder='$val[0]' where insuranceid='$user_id'");
                    $sql=sqlStatement("select insuranceid from tbl_inscomp_custom_attr_1to1 where insuranceid=$user_id");
                    $data_fetch=sqlFetchArray($sql);
                    if(!empty($data_fetch)){
                          //echo "UPDATE `tbl_inscomp_custom_attr_1to1` SET `updated_date`='$today',`payer_folder`='$val[0]' WHERE insuranceid= $user_id";
                          $update=sqlStatement("UPDATE `tbl_inscomp_custom_attr_1to1` SET `updated_date`='$today',`payer_folder`='$val[0]' WHERE insuranceid= $user_id");
                    }else {
                         $ins=sqlStatement("insert into tbl_inscomp_custom_attr_1to1 (insuranceid,payer_folder,created_date)values($user_id,'".$val[0]."','$today')");
                    }
                 }
                  $ins_id12=$user_id."(insurance id)";
                        $ins_log=sqlStatement("insert into DriveSync_log (date,user,email,encounter,patient_id,google_folder,file_name ,file_id,status,watsID)values(now(),'".$_SESSION['authUser']."','".$email."','','".$ins_id12."','$link','','','insurance folder_created(auto Creation)','')");
             }
        }    
        return $val[0];
   }
}

$i=0;

$fac=sqlStatement("select * from insurance_companies order by name ");
while($row1=sqlFetchArray($fac)){
   
    $sel_custom=sqlStatement("select payer_folder,insuranceid from tbl_inscomp_custom_attr_1to1 where insuranceid='".$row1['id']."'");
    $row12=sqlFetchArray($sel_custom);
    if($row12['payer_folder']==''){
        if($row12['insuranceid']!='')
        $id[]=$row12['insuranceid'];
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
        $folder_name= str_replace("/","_",str_replace(" ","",$frow['ins_folder'])); 
        Create_folder($row['notes'],$val,$folder_name,$parentid,'',$parent_stat,'create');
        $cnt=$key;
    }else {
        $cnt=$key;
        if($cnt==$to){
            $msg=count($id)-$to;
            if($msg==0)
            echo $msg.":Insurance Companies Folders Sucessfully Created";
            else
                 echo "$msg:cont";
        }
        exit();
    }
  
}

 if($cnt+1==$to){
        $msg=count($id)-$to; 
        if($msg==0) {
        echo $msg.":Insurance Companies Folders Sucessfully Created"; 
        
        }
        else
        echo $msg.":cont";
    } 
?>