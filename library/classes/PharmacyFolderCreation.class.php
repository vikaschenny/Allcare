<?php
require_once("ORDataObject.class.php");
/**
 * class FolderCreation
 *
 */
class PharmacyFolderCreation extends ORDataObject{ 
    function curlQuery1($id) {
        $protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === true ? 'https://' : 'https://'; 
        $sql = sqlStatement("select * from list_options where list_id='AllcareDriveSync' and option_id='email'");
        $row = sqlFetchArray($sql);

        $selection = sqlStatement("select * from tbl_drivesync_authentication where email='" . $row['notes'] . "' order by id desc");
        $sel_rows = sqlFetchArray($selection);
        if($sel_rows['pharmacy_folder_trigger']=='yes'){
                if($sel_rows['pharmacy_parent_folder']!='')
                    $parentid = str_replace('https://drive.google.com/drive/folders/', '', $sel_rows['pharmacy_parent_folder']);
                else
                    $parentid='root';  
                if($sel_rows['pharmacy_folder_query']!=''){
                    $query = $sel_rows['pharmacy_folder_query'] . " where id=" . $id ;
                    $fsql = sqlStatement("$query");
                    $frow = sqlFetchArray($fsql);
                    $folder_name=  str_replace(" ","-",str_replace("#","-",str_replace("/","_",$frow['ph_folder'])));
                }
                $folderid='0';
                if($folder_name!=''){
                    $form_url2 = $protocol.$_SERVER['HTTP_HOST'].'/api/DriveSync/insert_folder_web/'.$row['notes'].'/'.$parentid.'/'.$folder_name;
                    $curl = curl_init();
                    curl_setopt($curl,CURLOPT_URL, $form_url2);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);  
                    $result = curl_exec($curl);
                    $resultant = $result;
                    curl_close($curl);
                    $val= explode(':',$resultant);
                    $folderid=$val[0];
                    if($val[0]!=''){
                             $link='https://drive.google.com/drive/folders/'.$val[0];
                             $sql=sqlStatement("select pharmacyid from tbl_pharmacy_custom_attributes_1to1 where pharmacyid=$id");
                            $data_fetch=sqlFetchArray($sql);
                            if(!empty($data_fetch)){

                                  $update=sqlStatement("UPDATE `tbl_pharmacy_custom_attributes_1to1` SET `update_date`='$today',`pharmacy_folder`='$val[0]' WHERE pharmacyid= $id");
                            }else {
                                 $ins=sqlStatement("insert into tbl_pharmacy_custom_attributes_1to1 (pharmacyid,pharmacy_folder,created_date)values($id,'".$val[0]."','$today')");
                            }
                             $ph_id12=$id;
                             $ins_log=sqlStatement("insert into DriveSync_log (date,user,email,encounter,patient_id,google_folder,file_name,file_id,status,watsID,category)values(now(),'".$_SESSION['authUser']."','".$row['notes']."','','".$ph_id12."','$link','','','folder_created(during Pharmacy Creation in EMR)','','pharmacy')");
                             //subfolders
                            if($sel_rows['pharmacy_sub_folder']=='yes'){
                                $subsql = sqlStatement("select * from list_options where list_id='AllcarePharmacySubfolders'");
                                $link1='';
                                 $srow=mysql_num_rows($subsql);
                                 if($srow!=0){
                                while($subrow = sqlFetchArray($subsql)){
                                    $curl = curl_init();
                                    $form_url2 = $protocol.$_SERVER['HTTP_HOST'].'/api/DriveSync/insert_folder_web/'.$row['notes'].'/'.$val[0].'/'.str_replace(" ","_",$subrow['title']);
                                    curl_setopt($curl,CURLOPT_URL, $form_url2);
                                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);  
                                    $result = curl_exec($curl);
                                    $subresultant = $result;
                                    curl_close($curl);
                                    $subval= explode(':',$subresultant);
                                    $link1.='https://drive.google.com/drive/folders/'.$subval[0]."||";
                                }
                                 $ins_log=sqlStatement("insert into DriveSync_log (date,user,email,encounter,patient_id,google_folder,file_name,file_id,status,watsID,category)values(now(),'".$_SESSION['authUser']."','".$row['notes']."','','".$ph_id12."','$link1','','','subfolder_created(during Pharmacy Creation in EMR)','','pharmacy')");
                                 }
                            }

                    }
                }
                
    }    
        return $folderid;
    }
}
 

//$object1 = new FolderCreation;
//$object1->url = "http://api.twitter.com/1/statuses/user_timeline.json?include_entities=true&include_rts=true&screen_name=twitterapi&count=2";
//$object1->curlQuery($object1->url);
//$object1->set_folder($object1->url);

?>