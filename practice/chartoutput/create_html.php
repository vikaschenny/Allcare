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
include_once('../../interface/globals.php');

    $patient_id=$_REQUEST['pid'];
    $data=$_REQUEST['data'];
    $transid=$_REQUEST['transid'];
    
    //To Get email id 
    $sql=sqlStatement("select * from list_options where list_id='AllcareDriveSync' and option_id='email'");
    $row=sqlFetchArray($sql);
    $email=$row['notes'];
    
    $pfolder1=sqlStatement("select * from tbl_drivesync_authentication where email='".$row['notes']."' order by id desc");
    $prow=sqlFetchArray($pfolder1);
    
    //check whether the patient has google drive folder id 
    $folder=sqlStatement("select patient_folder as folder from patient_data where pid=$patient_id");
    $row1=sqlFetchArray($folder);
    $patient_folderid=$row1['folder'];
    
    $transsql=sqlStatement("select * from tbl_form_chartoutput_transactions where pid=$patient_id and id=$transid");
    $transrow=sqlFetchArray($transsql);
    
    $protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'https://';
    //to save medical record in respective who type ex:pharmacy,addressbook
    $category_arr= array(
        'patients'=>'parent_folder,',
        'provider'=>'user_parent_folder,provider_folder',
        'payer'=>'ins_parent_folder,insurance_folder_query',
        'pharmacy'=>'pharmacy_parent_folder,pharmacy_folder_query',
        'address_Book'=>'addrbk_parent_folder,org_folder_query',
        'facility'=>'facility_parent_folder,facility_folder_query'
    );
    
    
    
    function create_folder($folder_name,$email){ 
        $curl = curl_init();
        $form_url = $protocol.$_SERVER['HTTP_HOST'].'/api/DriveSync/createfolder_web/'.$email.'/'.$folder_name;
        curl_setopt($curl,CURLOPT_URL, $form_url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);  
        $result = curl_exec($curl);
        $resultant = $result;
        curl_close($curl);
        //$pos = strpos($resultant, 'Log Updated');
        $val= explode(':',$resultant);
        return $val[0];
    }
    function folder_check($email,$id){ 
        //to get folder name
        $curl = curl_init();
        $form_url = $protocol.$_SERVER['HTTP_HOST'].'/api/DriveSync/getfileinfo_web/'.$email.'/'.$id;
        curl_setopt($curl,CURLOPT_URL, $form_url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);  
        $result = curl_exec($curl);
        $resultant = $result;
        curl_close($curl);
        $folderinfo = json_decode($resultant, TRUE);
     
        $curl = curl_init();
        $form_url2 = $protocol.$_SERVER['HTTP_HOST'].'/api/DriveSync/isfolder/'.$email.'/'.$folderinfo['name'].'/folders'; 
        curl_setopt($curl,CURLOPT_URL, $form_url2);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);  
        $fex = curl_exec($curl);
        curl_close($curl);
//        return $data1 = json_decode($fex, TRUE);
        return $fex;
    }
    function insert_folder($parent,$folder_name,$email){
        
//        $data=folder_check($email,$parent);
//        
//        $data1=json_decode($data,TRUE); 
//     
//        if(in_array($parent,$data1)){ 
           
            //create folder in parent folder
            $curl = curl_init();
            $form_url2 = $protocol.$_SERVER['HTTP_HOST'].'/api/DriveSync/insert_folder_web/'.$email.'/'.$parent.'/'.$folder_name; 
            curl_setopt($curl,CURLOPT_URL, $form_url2);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);  
            $result = curl_exec($curl);
            $resultant = $result;
            curl_close($curl);
            $val1= explode(':',$resultant);
            return $val1[0];
//        }else{
//           return create_folder($folder_name,$email);
//        }
    }
    function movefile($email,$id,$parent,$filename,$foldername){
       
        $curl = curl_init();
        $form_url = $protocol.$_SERVER['HTTP_HOST'].'/api/DriveSync/movefile_web1/'.$email.'/'.$id.'/'.$parent.'/'.$_REQUEST['user'];
        curl_setopt($curl,CURLOPT_URL, $form_url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);  
        $result_files = curl_exec($curl);
        curl_close($curl);
    }
    
    function update_log($type,$email,$id,$fid,$move){ 
        if(strpos($fid,'<!')===false){
            $link='https://drive.google.com/drive/folders/'.$fid;
        }
        $today = date("Y-m-d");
        if($type=='address_Book' ){
          
            $fsql=sqlStatement("UPDATE `tbl_addrbk_custom_attr_1to1` SET `updated_date`='$today',`addrbk_folder`='$fid' WHERE addrbk_type_id= '".$id."'");
            $text="organisation folder created in addressbook";
        }else if($type=='address_Book1'){
            $sel=sqlStatement("select * from tbl_addrbk_custom_attr_1to1 where addrbk_type_id= '".$id."'");
            $srow=sqlFetchArray($sel);
            if(!empty($srow['id'])){
                $fsql=sqlStatement("update tbl_addrbk_custom_attr_1to1 set addressbook_folder='".$fid."',updated_date='$today' where addrbk_type_id='".$id."' ");
                $text="abook folder created ";
            }else{
                $fsql=sqlStatement("insert into tbl_addrbk_custom_attr_1to1 (addrbk_type_id,addressbook_folder,created_date)values('".$id."','".$fid."','$today')");
                $text="abook folder created ";
            }
            
        }else if($type=='facility'){
             $sql=sqlStatement("select facilityid from tbl_facility_custom_attr_1to1 where facilityid=$id");
             $data_fetch=sqlFetchArray($sql);
            if(!empty($data_fetch)){
               //echo  "UPDATE `tbl_facility_custom_attr_1to1` SET `updated_date`='$today',`facilityfolder`='$val[0]' WHERE facilityid= $user_id"; echo "<br>";
                $update=sqlStatement("UPDATE `tbl_facility_custom_attr_1to1` SET `updated_date`='$today',`facilityfolder`='$fid' WHERE facilityid= $id");
            }else {
               // echo "insert into tbl_facility_custom_attr_1to1 (facilityid,facilityfolder,created_date)values($user_id,'".$val[0]."','$today')"; echo "<br>";
                $ins=sqlStatement("insert into tbl_facility_custom_attr_1to1 (facilityid,facilityfolder,created_date)values($id,'".$fid."','$today')");
            }
             $text="facility folder created ";
        }else if($type=='pharmacy'){
            $sql=sqlStatement("select pharmacyid from tbl_pharmacy_custom_attributes_1to1 where pharmacyid=$id");
                $data_fetch=sqlFetchArray($sql);
                $today = date("Y-m-d"); 
                if(!empty($data_fetch)){
                 
                   $update=sqlStatement("UPDATE `tbl_pharmacy_custom_attributes_1to1` SET `update_date`='$today',`pharmacy_folder`='$fid' WHERE pharmacyid= $id");
                }else {
                  
                  $ins=sqlStatement("insert into tbl_pharmacy_custom_attributes_1to1 (pharmacyid,pharmacy_folder,created_date)values($id,'".$fid."','$today')");
                }
                $text="pharmacy folder created ";
        }else if($type=='payer'){
         
                $sql=sqlStatement("select insuranceid from tbl_inscomp_custom_attr_1to1 where insuranceid=$id");
                $data_fetch=sqlFetchArray($sql);
                if(!empty($data_fetch)){
                      //echo "UPDATE `tbl_inscomp_custom_attr_1to1` SET `updated_date`='$today',`payer_folder`='$val[0]' WHERE insuranceid= $user_id";
                      $update=sqlStatement("UPDATE `tbl_inscomp_custom_attr_1to1` SET `updated_date`='$today',`payer_folder`='$fid' WHERE insuranceid= $id");
                }else {
                     $ins=sqlStatement("insert into tbl_inscomp_custom_attr_1to1 (insuranceid,payer_folder,created_date)values($id,'".$fid."','$today')");
                }          
                $text="insurance folder created ";
        }else if($type=='provider'){
            $sql=sqlStatement("select userid from tbl_user_custom_attr_1to1 where userid=$id");
                $data_fetch=sqlFetchArray($sql);
                $today = date("Y-m-d"); 
                if(!empty($data_fetch)){
                   //echo  "UPDATE `tbl_user_custom_attr_1to1` SET `updated_date`='$today',`drive_sync_folder`='$val[0]' WHERE userid= $user_id"; echo "<br>";
                    $update=sqlStatement("UPDATE `tbl_user_custom_attr_1to1` SET `updated_date`='$today',`drive_sync_folder`='$fid' WHERE userid= $id");
                }else {
                 // echo "insert into tbl_user_custom_attr_1to1 (userid,drive_sync_folder,created_date)values($user_id,'".$val[0]."','$today')"; echo "<br>"; 
                    $ins=sqlStatement("insert into tbl_user_custom_attr_1to1 (userid,drive_sync_folder,created_date)values($id,'".$fid."','$today')"); 
                }
        }
        if($move!=''){
            $text="folder_created(files moved from $id)";
        }
         $ins_log=sqlStatement("insert into DriveSync_log (date,user,email,encounter,patient_id,google_folder,file_name,file_id,status,watsID,category)values(now(),'".$_REQUEST['user']."','".$email."','','$id','$link','','','$text','','$type')");
    }
    
    function object_query($who_type,$query,$condition,$name,$field,$query_id,$email){ 
            $value= explode(",",$field);
            $query1=$query[$value[1]].$condition; 
            if($query[$value[1]]!=''){
                $fsql=sqlStatement("$query1");
                $frow=sqlFetchArray($fsql);
                $objpfolder=str_replace('https://drive.google.com/drive/folders/','',$query[$value[0]]);
                if($name=='org'){
                    $object_name=str_replace("/","_",str_replace(" ","_",$frow[$name])); 
                    $sel_custom=sqlStatement("select addressbook_folder,addrbk_folder from tbl_addrbk_custom_attr_1to1 where addrbk_type_id='".$frow['id']."'");
                    $row12=sqlFetchArray($sel_custom);
                    if($row12['addressbook_folder']==''){
                        $atype=sqlStatement("select option_id ,title from list_options where list_id='abook_type' and option_id='$who_type' order by `seq` asc ");
                        $atrow=sqlFetchArray($atype);
                        $fid2= insert_folder($objpfolder,str_replace(" ","_",$atrow['title']),$email);
                        $objpfolder=$fid2; 
                        if(strpos($fid2,'<!')===false){
                         $link='https://drive.google.com/drive/folders/'.$fid2;
                        }
                        
                         $today = date("Y-m-d");
                         update_log("address_Book1",$email,$frow['id'],$fid2,'');
                      //  update_log("insert into tbl_addrbk_custom_attr_1to1 (addrbk_type_id,addressbook_folder,created_date)values('".$frow['id']."','".$fid2."','$today')", $condition,$link,$email,'address_book folder created',$who_type,$fid2,0);
                       $who_type=$frow['id'];
                        
                    }else{
                        $objpfolder=$row12['addressbook_folder'];
                    }
                }else if($name=='ins_folder')
                    $object_name=str_replace("/","_",str_replace(" ","",$frow['ins_folder']));
                else if($name=='ph_folder') {
                     $data = str_replace("\t", "", $frow['ph_folder']);
                     $object_name=  str_replace(" ","-",str_replace("#","-",str_replace("/","_",$data)));
                }else    
                    $object_name=str_replace(" ","_",$frow[$name]);
                
                
               
                $sel_custom=sqlStatement($query_id);
                $row12=sqlFetchArray($sel_custom);
                if($row12['folder']==''){
                    $objfolderid='no';
                }else { 
                    $objfolderid=$row12['folder'];
                } 
                
            }
           
        return     $object_name.",".$objpfolder.",".$objfolderid.",".$who_type;
           
    }
    function upload_record($email,$filename,$id,$url,$fullname,$rowid,$patient_id,$transid,$type){
        $protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'https://';
        $curl = curl_init();
        $form_url = $protocol.$_SERVER['HTTP_HOST'].'/api/DriveSync/uploadHTMLWeb/'.$email.'/'.$filename.'/'.$id.'/'.$url; 
        curl_setopt($curl,CURLOPT_URL, $form_url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);  
        $result = curl_exec($curl);
        $resultant = $result; 
        curl_close($curl);
        $trans=sqlStatement("update tbl_form_chartoutput_transactions set pdf_file='$resultant' where pid=$patient_id and id=$transid");
        $link='https://drive.google.com/file/d/'.$resultant;
       
        unlink($url);//to delete an empty file that tempnam creates
        $l=explode(".",$url);
        unlink($l[0]);//to delete your file   
        $ins_log1=sqlStatement("insert into DriveSync_log (date,user,email,encounter,patient_id,google_folder,file_name,file_id,status,watsID,category)values(now(),'".$_REQUEST['user']."','".$email."','','$rowid','".$link."','".$fullname."','$resultant','file Created','','$type')");
    } 
    function delete_file($email,$instance,$category,$fileid,$objid){
        $protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'https://';
        $curl = curl_init();
        $form_url = $protocol.$_SERVER['HTTP_HOST'].'/api/DriveSync/deletefile_web/'.$email.'/'.$instance.'/'.$_REQUEST['user'].'/'.$category.'/'.$fileid.'/'.$objid;
        curl_setopt($curl,CURLOPT_URL, $form_url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);  
        $result_files = curl_exec($curl);
        curl_close($curl);
    }
    function upload_patientRec($patient_id,$med_folder,$email,$pagename,$url,$fullname1,$transid){
        $folder1=sqlStatement("select patient_folder as folder1 from patient_data where pid=$patient_id");
        $row2=sqlFetchArray($folder1);
       
        if($med_folder!=''){
            $curl = curl_init();
            $form_url = 'https://'.$_SERVER['HTTP_HOST'].'/api/DriveSync/uploadHTMLWeb/'.$email.'/'.$fullname1.'/'.trim($med_folder," ").'/'.$url; 
            curl_setopt($curl,CURLOPT_URL, $form_url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);  
            $upload_res = curl_exec($curl);
            $ures= trim($upload_res," ");
            curl_close($curl);
            $trans=sqlStatement("update tbl_form_chartoutput_transactions set pdf_link='$ures' where pid=$patient_id and id=$transid");
        }
        $view='https://'.$_SERVER['HTTP_HOST'].'/practice/chartoutput/drive_view.php?folder='.str_replace('https://drive.google.com/drive/folders/','',$row2['folder1']).'&category=patients&action=view';
        $ins_log1=sqlStatement("insert into DriveSync_log (date,user,email,encounter,patient_id,google_folder,file_name,file_id,status,watsID,category)values(now(),'".$_REQUEST['user']."','".$email."','".$_REQUEST['encounter']."','$patient_id','".$row2['folder1']."','".$pagename."','$ures','file Created','','patient')");
       return $view."@".trim($ures," ");
    }
     
    
    if($transrow['who_type']!=''){
         
        if($transrow['who_type']=='pharmacy'){ 
            $objid= object_query($transrow[$transrow['who_type']],$prow," where id=".$transrow[$transrow['who_type']],'ph_folder',$category_arr[$transrow['who_type']],"select pharmacy_folder as folder  from tbl_pharmacy_custom_attributes_1to1 where pharmacyid=".$transrow[$transrow['who_type']],$email);
            $obj_type=$transrow['who_type'];
        }else if($transrow['who_type']=='payer'){
            $objid= object_query($transrow[$transrow['who_type']],$prow," where id=".$transrow[$transrow['who_type']],'ins_folder',$category_arr[$transrow['who_type']],"select payer_folder as folder ,insuranceid from tbl_inscomp_custom_attr_1to1 where insuranceid=".$transrow[$transrow['who_type']],$email);
            $obj_type=$transrow['who_type'];
        }else if($transrow['who_type']=='facility'){
            $objid= object_query($transrow[$transrow['who_type']],$prow," where id=".$transrow[$transrow['who_type']],'fac_folder',$category_arr[$transrow['who_type']],"select facilityfolder as folder from tbl_facility_custom_attr_1to1 where facilityid=".$transrow[$transrow['who_type']],$email);
            $obj_type=$transrow['who_type'];
        }else if($transrow['who_type']=='provider'){
            $objid= object_query($transrow[$transrow['who_type']],$prow," and id=".$transrow[$transrow['who_type']],'provider_folder',$category_arr[$transrow['who_type']],"select drive_sync_folder as folder,userid from tbl_user_custom_attr_1to1 where userid=".$transrow[$transrow['who_type']],$email);
             $obj_type=$transrow['who_type'];
        }else{
          
           $objid= object_query($transrow['who_type'],$prow," where id='".$transrow['refer_to']."'  and  abook_type="."'".$transrow['who_type']."'",'org',$category_arr['address_Book'],"select addressbook_folder,addrbk_folder as folder ,addrbk_type_id from tbl_addrbk_custom_attr_1to1 where addrbk_type_id=".$transrow['refer_to']." order by id desc",$email);
           $obj_type='address_Book';
        }
    }
    
   
    //upload html file to the patient_folder
    
if($_REQUEST['authorize']=='true'){
   $view_folder='';
    //create html file in server
    $file = tempnam(sys_get_temp_dir(), 'med_rec');
    file_put_contents($file.'.html', $data);
    $page=explode('.',$_REQUEST['page_name']);
    $url=base64_encode($file.'.html');
    //to get patient folder name from config
    if($prow['patient_folder']!=''){
        $query=$prow['patient_folder']." where pid=".$patient_id;
        $fsql=sqlStatement("$query");
        $frow=sqlFetchArray($fsql);
        $folder_name=str_replace(" ","",$frow['Patient_folder']);
        $pfolder=str_replace('https://drive.google.com/drive/folders/','',$prow['parent_folder']);
    }
    $med_folder='';
     //if there is no patient folder then create
    if($patient_folderid==''){
        if($pfolder!=''){
            
            $fid= insert_folder($pfolder,$folder_name,$email);
            if($prow['patient_sub_folder']=='yes'){
                $sub=sqlStatement("select * from list_options where list_id='AllcarePatientSubfolders'");
                while($subrow=sqlFetchArray($sub)){
                    $subresultant=insert_folder($fid,str_replace(" ","_",$subrow['title']),$email);
                    $sublink1.='https://drive.google.com/drive/folders/'.$subresultant."||";
                    if($subrow['title']=='Charts'){
                        $med_folder=$subresultant;
                    }
                }
                $ins_log=sqlStatement("insert into DriveSync_log (date,user,email,encounter,patient_id,google_folder,file_name,file_id,status,watsID,category)values(now(),'".$_REQUEST['user']."','".$row['notes']."','".$_REQUEST['encounter']."','$patient_id','$sublink1','','','subfolder_created(during patient Creation in EMR)','','patient')");
            }
        }else {
            $fid= create_folder($folder_name,$row['notes']);
            if($prow['patient_sub_folder']=='yes'){
                $sub=sqlStatement("select * from list_options where list_id='AllcarePatientSubfolders'");
                while($subrow=sqlFetchArray($sub)){
                    $subresultant=create_folder(str_replace(" ","_",$subrow['title']),$email);
                    $sublink1.='https://drive.google.com/drive/folders/'.$subresultant."||";
                    if($subrow['title']=='Charts'){
                        $med_folder=$subresultant;
                    }
                }
                $ins_log=sqlStatement("insert into DriveSync_log (date,user,email,encounter,patient_id,google_folder,file_name,file_id,status,watsID,category)values(now(),'".$_REQUEST['user']."','".$row['notes']."','".$_REQUEST['encounter']."','$patient_id','$sublink1','','','subfolder_created(during patient Creation in EMR)','','patient')");
            }
        }
        if(strpos($fid,'<!')===false){
             $link='https://drive.google.com/drive/folders/'.$fid;
             $ins=sqlStatement("update patient_data SET patient_folder='$link' where pid=$patient_id");
             $ins_log=sqlStatement("insert into DriveSync_log (date,user,email,encounter,patient_id,google_folder,file_name,file_id,status,watsID,category)values(now(),'".$_REQUEST['user']."','".$row['notes']."','".$_REQUEST['encounter']."','$patient_id','$link','','','folder_created','','patient')");
        }
        if(trim($med_folder," ")=='') {  $med_folder=$fid; }
       
        $view_folder=upload_patientRec($patient_id,$med_folder,$row['notes'],$_REQUEST['page_name'],$url,$page[0],$transid);
    }
    else{
        $id=str_replace('https://drive.google.com/drive/folders/','',$patient_folderid);
        $curl = curl_init();
        $form_url = $protocol.$_SERVER['HTTP_HOST'].'/api/DriveSync/listAllFoldersmed/'.$row['notes'].'/'.$id.'/folder/Charts'; exit();
        curl_setopt($curl,CURLOPT_URL, $form_url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);  
        $subid = curl_exec($curl);
        curl_close($curl);  
        $med_folder=$subid;
       
         if(trim($med_folder," ")=='') {  $med_folder=$id; }
         
        if($transrow['pdf_link']!=''){ 
            
            $fileid=trim($transrow['pdf_link']," ");
           
            //to get folder info
            $curl1 = curl_init();
            $form_url1 = $protocol.$_SERVER['HTTP_HOST'].'/api/DriveSync/getfileinfo_web/'.$row['notes'].'/'.$fileid;
            curl_setopt($curl1,CURLOPT_URL, $form_url1);
            curl_setopt($curl1, CURLOPT_RETURNTRANSFER, true);  
            $meta_data = curl_exec($curl1);
            $folderinfo1 = json_decode($meta_data);
            curl_close($curl1);
           
           if($folderinfo1->name==$page[0].'.pdf'){ 
                
                if($_REQUEST['modify']==''){
                    echo '$'."Do you want to replace the file"; exit();
                }else  if($_REQUEST['modify']==1){
                   $res= delete_file($email,'emr','patient',$fileid,$patient_id);
                   $del_log1=sqlStatement("insert into DriveSync_log (date,user,email,encounter,patient_id,google_folder,file_name,file_id,status,watsID,category)values(now(),'".$_REQUEST['user']."','".$row['notes']."','".$_REQUEST['encounter']."','$patient_id','".$fileid."','".$folderinfo1->name."','$resultant','file deleted to update data','','patient')");
                   $view_folder=upload_patientRec($patient_id,$med_folder,$row['notes'],$_REQUEST['page_name'],$url,$page[0],$transid);
                }else if($_REQUEST['modify']==0){
                    
                    $view_folder=upload_patientRec($patient_id,$med_folder,$row['notes'],$_REQUEST['page_name'],$url,$page[0],$transid);
                }
                
            }else{
                 $view_folder=upload_patientRec($patient_id,$med_folder,$row['notes'],$_REQUEST['page_name'],$url,$page[0],$transid);
            } 
        }else{
           
            $view_folder=upload_patientRec($patient_id,$med_folder,$row['notes'],$_REQUEST['page_name'],$url,$page[0],$transid);
        }
    }
    
    
    //upload_patientRec($patient_id,$med_folder,$row['notes'],$_REQUEST['page_name'],$url,$page[0],$transid);
    
    /***************** save medical record into selected object folder in google drive  ******************************/
     /*** $details[1]--parent folder id ,$details[2]--folder id, $details[0]-- folder name **/
    $details=explode(",",$objid);
    
    if($details[2]=='no'){
        if($details[1]!=''){
            $ofid= insert_folder($details[1],$details[0],$email);
        }else {
            $ofid= create_folder($details[0],$email);
        }
        upload_record($email,$page[0],$ofid,$url,$_REQUEST['page_name'],$details[3],$patient_id,$transid,$transrow['who_type']);
        
            //update_log($log_query,$link,$email,$log_text,$details[5],$ofid,1);
        update_log($obj_type,$email,$details[3],$ofid,'');
    }else{
         if($transrow['pdf_file']!=''){
            //to get folder info
            $curl1 = curl_init();
            $form_url1 = $protocol.$_SERVER['HTTP_HOST'].'/api/DriveSync/getfileinfo_web/'.$row['notes'].'/'.trim($transrow['pdf_file']," ");
            curl_setopt($curl1,CURLOPT_URL, $form_url1);
            curl_setopt($curl1, CURLOPT_RETURNTRANSFER, true);  
            $meta_data = curl_exec($curl1);
            $folderinfo1 = json_decode($meta_data);
            curl_close($curl1);
            if($folderinfo1->name==$page[0].'.pdf'){
                if($_REQUEST['modify']==''){
                    echo '$'."Do you want to replace the file"; exit();
                }else  if($_REQUEST['modify']==1){
                   $res= delete_file($email,'emr',$transrow['who_type'],trim($transrow['pdf_file']," "),$patient_id);
                   $del_log1=sqlStatement("insert into DriveSync_log (date,user,email,encounter,patient_id,google_folder,file_name,file_id,status,watsID,category)values(now(),'".$_REQUEST['user']."','".$row['notes']."','".$_REQUEST['encounter']."','$patient_id','".$transrow['pdf_file']."','".$folderinfo1->name."','$resultant','file deleted to update data','','".$transrow['who_type']."')");
                }
                
            }
            upload_record($email,$page[0],$details[2],$url,$_REQUEST['page_name'],$details[3],$patient_id,$transid,$transrow['who_type']);
            update_log($obj_type,$email,$details[3],$details[2],'');
        }else{
             upload_record($email,$page[0],$details[2],$url,$_REQUEST['page_name'],$details[3],$patient_id,$transid,$transrow['who_type']);
             update_log($obj_type,$email,$details[3],$details[2],'');
        }
    }
    echo $view_folder;
}
else if($_REQUEST['authorize']=='false'){
    
    $curl = curl_init();
    $form_url =$protocol.$_SERVER['HTTP_HOST'].'/api/DriveSync/oauth2/'.$email;
    curl_setopt($curl,CURLOPT_URL, $form_url);
    $result = curl_exec($curl);
    echo   $resultant = $result;
    curl_close($curl);
}
?>
 