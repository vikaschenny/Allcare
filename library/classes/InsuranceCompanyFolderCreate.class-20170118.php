<?php
require_once("ORDataObject.class.php");
/**
 * class FolderCreation
 *
 */
class InsuranceFolderCreation extends ORDataObject{ 
    function curlQuery($id) {
        $protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === true ? 'https://' : 'http://'; 
        $sql = sqlStatement("select * from list_options where list_id='AllcareDriveSync' and option_id='email'");
        $row = sqlFetchArray($sql);

        $selection = sqlStatement("select * from tbl_drivesync_authentication where email='" . $row['notes'] . "' order by id desc");
        $sel_rows = sqlFetchArray($selection);
        if($sel_rows['ins_folder_trigger']=='yes'){
                if($sel_rows['ins_parent_folder']!='')
                    $parentid = str_replace('https://drive.google.com/drive/folders/', '', $sel_rows['ins_parent_folder']);
                else
                    $parentid='root';  
                if($sel_rows['insurance_folder_query']!=''){
                    $query = $sel_rows['insurance_folder_query'] . " where id=" . $id ; 
                    $fsql = sqlStatement("$query");
                    $frow = sqlFetchArray($fsql);
                    $folder_name = str_replace(" ", "_", $frow['ins_folder']);  
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
                             $sql=sqlStatement("select insuranceid from tbl_inscomp_custom_attr_1to1 where insuranceid=$id");
                            $data_fetch=sqlFetchArray($sql);
                            $today = date("Y-m-d"); 
                            if(!empty($data_fetch)){
                               //echo  "UPDATE `tbl_inscomp_custom_attr_1to1` SET `updated_date`='$today',`payer_folder`='$val[0]' WHERE insuranceid= $user_id"; echo "<br>";
                                $update=sqlStatement("UPDATE `tbl_inscomp_custom_attr_1to1` SET `updated_date`='$today',`payer_folder`='$val[0]' WHERE insuranceid= $id");
                            }else {
                               // echo "insert into tbl_inscomp_custom_attr_1to1 (insuranceid,payer_folder,created_date)values($user_id,'".$val[0]."','$today')"; echo "<br>";
                                $ins=sqlStatement("insert into tbl_inscomp_custom_attr_1to1 (insuranceid,payer_folder,created_date)values($id,'".$val[0]."','$today')");
                            }
                             $ins_id12=$id;
                             $ins_log=sqlStatement("insert into DriveSync_log (date,user,email,encounter,patient_id,google_folder,file_name,file_id,status,watsID,category)values(now(),'".$_SESSION['authUser']."','".$row['notes']."','','".$ins_id12."','$link','','','folder_created(during insurance Creation in EMR)','','insurance')");
                              //subfolders
                            if($sel_rows['insurance_sub_folder']=='yes'){
                                $subsql = sqlStatement("select * from list_options where list_id='AllcareInsuranceSubfolders'");
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
                                 $ins_log=sqlStatement("insert into DriveSync_log (date,user,email,encounter,patient_id,google_folder,file_name,file_id,status,watsID,category)values(now(),'".$_SESSION['authUser']."','".$row['notes']."','','".$ins_id12."','$link1','','','subfolder_created(during Insurance Creation in EMR)','','insurance')");
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