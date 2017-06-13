<?php 
 require_once("../../globals.php");
 require '../../../api/AesEncryption/GibberishAES.php';
 $sql = sqlStatement("select * from list_options where list_id='AllcareDriveSync' and option_id='email'");
 $row = sqlFetchArray($sql);
 
 $patientfolder='';
 $result=explode("&", $_REQUEST['params']);
  $email=$_REQUEST['email'];
 foreach($result as $val1){
     $result1=explode("*", $val1);
     if($result1[0]=='patient-folder'){
         $patientfolder=$result1[1];
     }else if($result1[0]=='provider-folder'){
         $providerfolder=$result1[1];
     }else if($result1[0]=='patient-file'){
         $patientfile=$result1[1];
     }else if($result1[0]=='insurance-folder'){
         $insfolder=$result1[1];
     }else if($result1[0]=='pharmacy-folder'){
         $phfolder=$result1[1];
     }else if($result1[0]=='facility-folder'){
         $facfolder=$result1[1];
     }else if($result1[0]=='org-folder'){
         $orgfolder=$result1[1]; 
     }else if($result1[0]=='parent-folder'){
         $parfolder=$result1[1];
     }else if($result1[0]=='user-parent-folder'){
         $usrparfolder=$result1[1];
     }else if($result1[0]=='ins-parent-folder'){
         $insparfolder=$result1[1];
     }else if($result1[0]=='pharmacy-parent-folder'){
         $phparfolder=$result1[1];
     }else if($result1[0]=='facility-parent-folder'){
         $facparfolder=$result1[1];
     }else if($result1[0]=='addrbk-parent-folder'){
         $addrparfolder=$result1[1];
     }else if($result1[0]=='auto-trigger'){
         $autotriggerr=$result1[1];
     }else if($result1[0]=='ins-auto-trigger'){
         $insautotriggerr=$result1[1];
     }else if($result1[0]=='ph-auto-trigger'){
         $phautotriggerr=$result1[1];
     }else if($result1[0]=='addr-auto-trigger'){
         $addrautotriggerr=$result1[1];
     }else if($result1[0]=='user-auto-trigger'){
         $userautotriggerr=$result1[1];
     }else if($result1[0]=='facility-folder-trigger'){
         $facautotriggerr=$result1[1];
     }else if($result1[0]=='instance-parent-folder'){
         $practice=$result1[1];
     }else if($result1[0]=='psubfolder'){
         $psubfolder=$result1[1];
     }
     else if($result1[0]=='provider-sub-folder'){
         $prosubfolder=$result1[1];
     }
     else if($result1[0]=='insurance-sub-folder'){
         $inssubfolder=$result1[1];
     }
     else if($result1[0]=='pharmacy-sub-folder'){
         $phsubfolder=$result1[1];
     }
     else if($result1[0]=='addrbk-sub-folder'){
         $addrsubfolder=$result1[1];
     }
     else if($result1[0]=='facility-sub-folder'){
         $facsubfolder=$result1[1];
     }else if($result1[0]=='scan-parent-folder'){
         $scan_folder=$result1[1];
     }else if($result1[0]=='scan-medical-charts'){
         $scan_charts=$result1[1];
     }else if($result1[0]=='scan-medical-payer'){
         $scan_cpo=$result1[1];
     }else if($result1[0]=='scan-medical-payment'){
         $scan_ac=$result1[1];
     }else if($result1[0]=='scan-medical-mhtb'){
         $scan_mhtb=$result1[1];
     }else if($result1[0]=='scan-medical-mhc'){
         $scan_mhc=$result1[1];
     }
 }


 $patient_query='';
 if($patientfolder!=''){
     $val=trim($patientfolder,'_');
     $attr=explode("_",$val);
     foreach($attr as $value){
         $col.="$value".",";
     }
     $col1=trim('"_"'.",".$col,',');
     
     $patient_query="select CONCAT_WS($col1) as Patient_folder from patient_data";
     
 }
 $provider_query='';
 if($providerfolder!=''){
     $pval=trim($providerfolder,'_');
     $pattr=explode("_",$pval);
     foreach($pattr as $pvalue){
         $pcol.="$pvalue".",";
     }
     $pcol1=trim('"_"'.','.$pcol,',');
     $provider_query="select CONCAT_WS($pcol1) as provider_folder from users where (fname<>'' or lname<>'') and username<>''";
 }
 if($patientfile!=''){ 
     $pfval=trim($patientfile,'_');
     $pfattr=explode("_",$pfval);
     $dos=0;
     foreach($pfattr as $pfvalue){
         if($pfvalue=='dos')  { $dos=1;  $pfcol.="REPLACE(REPLACE(c.date_of_service,'-',''),',','_')".','."'_'".','; 
         
         }else if($pfvalue=='pid'){ 
             $pfcol.="p."."$pfvalue".',';
         }else{
              $pfcol.="$pfvalue".',';
         }
     }
     $pfcol1=trim("'_'".','.$pfcol,',');
     if($dos==1){
         $patient_file_query='"'."select CONCAT_WS($pfcol1) as patient_file from patient_data p INNER JOIN  tbl_form_chartoutput_transactions c  ON c.pid=p.pid".'"';
     }else{
         $patient_file_query='"'."select CONCAT_WS($pfcol1) as patient_file from patient_data p".'"';
     }
     
 }
 $ins_query='';
 if($insfolder!=''){
//     $pval=trim($_POST['ins-parent-folder'],'_');
//     $pattr=explode("_",$pval);
//     foreach($pattr as $pvalue){
//         $pcol.="$pvalue".",".'"_"'.",";
//     }
   $pcol1=$insfolder;
//     $provider_query="select CONCAT($pcol1) as provider_folder from users where fname<>'' and lname<>'' and username<>''";
       $ins_query="select $pcol1 as ins_folder from insurance_companies";
     
 }
  $ph_query='';
 if($phfolder!=''){
    $phcol1=$phfolder;
    $ph_query="select $phcol1 as ph_folder from pharmacies";
 }
  $fac_query='';
 if($facfolder!=''){
    $faccol1=$facfolder;
    $fac_query="select $faccol1 as fac_folder from facility";
 }

  $org_query='';
 if($orgfolder!=''){
    $orgcol1=$orgfolder;
    $org_query="select $orgcol1 as org ,id from users";
 }
 
 
if($providerfolder!='' || $patientfolder!='' || $patientfile!='' || $insfolder!='' || $facfolder!='' || $phfolder!=''){
      $sel=sqlStatement("select * from tbl_drivesync_authentication where email='".$row['notes']."'");
      $sel_row=sqlFetchArray($sel);
      if(empty($sel_row)){
          
            //base64_encode($imap_pwd1);
            $sql=sqlStatement("insert into tbl_drivesync_authentication (date,email,status,user,patient_folder,
                                provider_folder,patient_folder_format,provider_folder_format,patient_file,patient_file_format,
                                parent_folder,user_parent_folder,ins_parent_folder,insurance_folder_format,insurance_folder_query,
                                pharmacy_parent_folder,pharmacy_folder_format,pharmacy_folder_query,facility_parent_folder,facility_folder_format,
                                facility_folder_query,addrbk_parent_folder,org_folder_format,org_folder_query,patient_folder_trigger,practice_parent_folder,
                                ins_folder_trigger,pharmacy_folder_trigger,addrbk_folder_trigger,email_parent_folder,
                                facility_folder_trigger,provider_folder_trigger,patient_sub_folder,provider_sub_folder,insurance_sub_folder,pharmacy_sub_folder,addrbk_sub_folder,
                                facility_sub_folder,scan_parent_folder,scan_medical_charts,scan_medical_payer,scan_medical_payment,scan_medical_mhtb,scan_medical_mhc)
                                values(now(),'$email','Authenticated','".$_SESSION['authUser']."','".$patient_query."','".addslashes($provider_query)."','".$patientfolder."',"
                    . "         '".$providerfolder."',".$patient_file_query.",'".$patientfile."','".$parfolder."','".$usrparfolder."','".$insparfolder."','".$insfolder."',"
                    . "         '".$ins_query."','".$phparfolder."','".$phfolder."','".$ph_query."','".$facparfolder."','".$facfolder."','".$fac_query."','".$addrparfolder."',"
                    . "         '".$orgfolder."','".$org_query."','".$autotriggerr."','".$practice."','$insautotriggerr','$phautotriggerr','$addrautotriggerr','$email_attach',"
                    . "         '$facautotriggerr','$userautotriggerr','$psubfolder','$prosubfolder','$inssubfolder','$phsubfolder',"
                    . "         '$addrsubfolder','$facsubfolder','$scan_folder','$scan_charts','$scan_cpo','$scan_ac','$scan_mhtb','$scan_mhc')");
      }else {
          if($sel_row['facility_parent_folder']!=$facparfolder){
           
              $sql=sqlStatement("update  `tbl_facility_custom_attr_1to1` set facilityfolder=''");
          }
          if($sel_row['user_parent_folder']!=$usrparfolder){
               $sql=sqlStatement("UPDATE  `tbl_user_custom_attr_1to1` SET drive_sync_folder =  ''");
          }
          if($sel_row['pharmacy_parent_folder']!=$phparfolder){
              $sql=sqlStatement("update `tbl_pharmacy_custom_attributes_1to1` set pharmacy_folder=''");
          }
          if($sel_row['parent_folder']!=$parfolder){
               $sql=sqlStatement("UPDATE  `patient_data` SET patient_folder =  ''");
          }
           if($sel_row['ins_parent_folder']!=$insparfolder){
               $sql=sqlStatement("UPDATE  `tbl_inscomp_custom_attr_1to1` SET payer_folder =  ''");
          }
           if($sel_row['addrbk_parent_folder']!=$addrparfolder){
               $sql=sqlStatement("UPDATE  `tbl_addrbk_custom_attr_1to1` SET addressbook_folder =  '',addrbk_folder =  ''");
          }
          
         //echo "update tbl_drivesync_authentication set date=now(),status='updated',user='".$_SESSION['authUser']."',patient_folder='".$patient_query."',provider_folder='".addslashes($provider_query)."',patient_folder_format='".$patientfolder."',provider_folder_format='".$providerfolder."' ,patient_file=".$patient_file_query.", patient_file_format='".$patientfile."' , parent_folder='".$parfolder."',user_parent_folder='".$usrparfolder."' ,ins_parent_folder='".$insparfolder."', insurance_folder_format='".$insfolder."', insurance_folder_query='".$ins_query."',pharmacy_parent_folder='".$phparfolder."',pharmacy_folder_format='".$phfolder."',pharmacy_folder_query='".$ph_query."',facility_parent_folder='".$facparfolder."',facility_folder_format='".$facfolder."',facility_folder_query='".$fac_query."',addrbk_parent_folder='".$addrparfolder."',org_folder_format='".$orgfolder."',org_folder_query='".$org_query."',patient_folder_trigger='".$autotriggerr."',practice_parent_folder='".$practice."',ins_folder_trigger='$insautotriggerr',pharmacy_folder_trigger='$phautotriggerr',addrbk_folder_trigger='$addrautotriggerr',email_parent_folder='$email_attach',imap_email='$imap_user',imap_pwd='$imap_pwd',pwd_encrypt='1' where email='".$row['notes']."'";
            $sql=sqlStatement("update tbl_drivesync_authentication set "
                    . "date=now(),status='updated',user='".$_SESSION['authUser']."',"
                    . "patient_folder='".$patient_query."',provider_folder='".addslashes($provider_query)."',"
                    . "patient_folder_format='".$patientfolder."',provider_folder_format='".$providerfolder."' ,"
                    . "patient_file=".$patient_file_query.", patient_file_format='".$patientfile."' , "
                    . "parent_folder='".$parfolder."',user_parent_folder='".$usrparfolder."' ,"
                    . "ins_parent_folder='".$insparfolder."', insurance_folder_format='".$insfolder."', "
                    . "insurance_folder_query='".$ins_query."',pharmacy_parent_folder='".$phparfolder."',"
                    . "pharmacy_folder_format='".$phfolder."',pharmacy_folder_query='".$ph_query."',"
                    . "facility_parent_folder='".$facparfolder."',facility_folder_format='".$facfolder."',"
                    . "facility_folder_query='".$fac_query."',addrbk_parent_folder='".$addrparfolder."',"
                    . "org_folder_format='".$orgfolder."',org_folder_query='".$org_query."',"
                    . "patient_folder_trigger='".$autotriggerr."',practice_parent_folder='".$practice."',"
                    . "ins_folder_trigger='$insautotriggerr',pharmacy_folder_trigger='$phautotriggerr',"
                    . "addrbk_folder_trigger='$addrautotriggerr',"
                    . "provider_folder_trigger='$userautotriggerr',facility_folder_trigger='$facautotriggerr',"
                    . "patient_sub_folder='$psubfolder',provider_sub_folder='$prosubfolder', insurance_sub_folder='$inssubfolder',"
                    . "pharmacy_sub_folder='$phsubfolder',addrbk_sub_folder='$addrsubfolder',facility_sub_folder='$facsubfolder',"
                    . "scan_parent_folder='$scan_folder',scan_medical_charts='$scan_charts',scan_medical_payer='$scan_cpo',"
                    . "scan_medical_payment='$scan_ac',scan_medical_mhtb='$scan_mhtb',scan_medical_mhc='$scan_mhc'"
                    . "where email='".$row['notes']."'");
      }
 }

?>