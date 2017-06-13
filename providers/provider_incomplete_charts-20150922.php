<?php
/**
 * Copyright (C) 2010 OpenEMR Support LLC
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * 2013/02/08 Minor tweaks by EMR Direct to allow integration with Direct messaging
 * 2013-03-27 by sunsetsystems: Fixed some weirdness with assigning a message recipient,
 *   and allowing a message to be closed with a new note appended and no recipient.
 */
//SANITIZE ALL ESCAPES
$sanitize_all_escapes=true;

//STOP FAKE REGISTER GLOBALS
$fake_register_globals=false;

require_once("../interface/globals.php");
require_once("../library/formdata.inc.php");
require_once("../library/globals.inc.php");

echo $_POST['string']; 
$provider=$_REQUEST['provider'];
$sql=sqlStatement("SELECT id, fname, lname, specialty FROM users " .
      "WHERE active = 1 AND ( info IS NULL OR info NOT LIKE '%Inactive%' ) " .
      "AND authorized = 1 AND username='".$provider."'" .
      "ORDER BY lname, fname");
$id=sqlFetchArray($sql);

//$provider  = $_POST['form_provider'];
$patient=$_POST['form_patient'];
print_r($patient);
$audit_stat=$_POST['form_audit_stat'];
print_r($audit_stat);
$enc_stat=$_POST['form_enc_stat'];
print_r($enc_stat);
echo $from=$_POST['form_from_date'];
echo $to=$_POST['form_to_date'];

//facility
function facility_list1($selected = '', $name = 'form_facility1[]', $id='form_facility1', $allow_unspecified = true, $allow_allfacilities = true) {
  $sel_value=explode("|",$selected);
  $have_selected = false;
  $query1 = "SELECT id, name FROM facility ORDER BY name";
  $fres = sqlStatement($query1);

  $name = htmlspecialchars($name, ENT_QUOTES);
  echo "   <select name=\"$name\" multiple id=\"$id\"  >\n";

  if ($allow_allfacilities) {
    $option_value = '';
    $option_selected_attr = '';	
    foreach($sel_value as $value){
    if ($value == '') {
      $option_selected_attr = ' selected="selected"';
      $have_selected = true;
    }
    }
    $option_content = htmlspecialchars('-- ' . xl('All Facilities') . ' --', ENT_NOQUOTES);
    echo "    <option value=\"$option_value\" $option_selected_attr>$option_content</option>\n";
  } elseif ($allow_unspecified) {
  	$option_value = '0';
    $option_selected_attr = '';
    foreach($sel_value as $value){
    if ( $value == '0' ) {
      $option_selected_attr = ' selected="selected"';
      $have_selected = true;
    }
    }
    $option_content = htmlspecialchars('-- ' . xl('Unspecified') . ' --', ENT_NOQUOTES);
    echo "    <option value=\"$option_value\" $option_selected_attr>$option_content</option>\n";
    }
  
  while ($frow = sqlFetchArray($fres)) {
    $facility_id = $frow['id'];
    $option_value = htmlspecialchars($facility_id, ENT_QUOTES);
    $option_selected_attr = '';
    foreach($sel_value as $value){
    if ($value == $facility_id) {
      $option_selected_attr = ' selected="selected"';
      $have_selected = true;
    }
    }
    $option_content = htmlspecialchars($frow['name'], ENT_NOQUOTES);
    echo "    <option value=\"$option_value\" $option_selected_attr>$option_content</option>\n";
  }

  if ($allow_unspecified && $allow_allfacilities) {
    $option_value = '0';
    $option_selected_attr = '';
    foreach($sel_value as $value){
    if ( $value == '0' ) {
      $option_selected_attr = ' selected="selected"';
      $have_selected = true;
    }
    }
    $option_content = htmlspecialchars('-- ' . xl('Unspecified') . ' --', ENT_NOQUOTES);
    echo "    <option value=\"$option_value\" $option_selected_attr>$option_content</option>\n";
  }

  if (!$have_selected) {
    foreach($sel_value as $value) { 
    $option_value = htmlspecialchars($selected, ENT_QUOTES);
    $option_label = htmlspecialchars('(' . xl('Do not change') . ')', ENT_QUOTES);
    $option_content = htmlspecialchars(xl('Missing or Invalid'), ENT_NOQUOTES);
    echo "    <option value='$option_value' label='$option_label' selected='selected'>$option_content</option>\n";
    }
  }
  echo "   </select>\n";
}

function VisitsWithNoForms($resid1,$respid1,$resenc1,$resfname1,$ros,$pe,$allcare,$audit_stat)
       {
                               
//                               $fuv_sql=sqlStatement("SELECT DISTINCT (
//                                        form_encounter.encounter
//                                        ), form_encounter.facility, form_encounter.pid,form_encounter.facility_id, form_encounter.encounter, form_encounter.pc_catid AS visitcategory_id, DATE_FORMAT( form_encounter.date,  '%Y-%m-%d' ) AS dos, patient_data.providerID AS provider_id,f.screen_group, l.title AS screen_name,f.screen_group, l.description AS screen_link,l.field_id, l.title,l.uor,f.screen_names
//                                        FROM form_encounter
//                                        INNER JOIN patient_data ON patient_data.pid = form_encounter.pid
//                                        INNER JOIN tbl_allcare_facuservisit f ON  `facilities` REGEXP (
//                                        form_encounter.facility_id
//                                        )
//                                        AND  `users` REGEXP (
//                                        patient_data.providerID
//                                        )
//                                        AND  `visit_categories` REGEXP (
//                                        form_encounter.pc_catid
//                                        )
//                                        INNER JOIN layout_options l ON l.group_name = f.screen_group
//                                        AND l.form_id = f.form_id
//                                        WHERE pc_catid
//                                        IN ( 15, 16, 17, 18, 19, 20, 24, 25, 29, 44 )  AND form_encounter.encounter=$resenc1"); 
                            $fuv_sql=sqlStatement("SELECT DISTINCT (
                                        form_encounter.encounter
                                        ), form_encounter.facility, form_encounter.pid,form_encounter.facility_id, form_encounter.encounter, form_encounter.pc_catid AS visitcategory_id, DATE_FORMAT( form_encounter.date,  '%Y-%m-%d' ) AS dos, form_encounter.provider_id AS provider_id,f.screen_names
                                        FROM form_encounter
                                        INNER JOIN patient_data ON patient_data.pid = form_encounter.pid
                                        INNER JOIN tbl_allcare_facuservisit f ON  `facilities` REGEXP (
                                        form_encounter.facility_id
                                        )
                                        AND  `users` REGEXP (
                                        form_encounter.provider_id
                                        )
                                        AND  `visit_categories` REGEXP (
                                        form_encounter.pc_catid
                                        )
                                        INNER JOIN layout_options l ON l.group_name = f.screen_group
                                        AND l.form_id = f.form_id
                                        WHERE pc_catid
                                        IN ( 15, 16, 17, 18, 19, 20, 24, 25, 29, 44 )  AND form_encounter.encounter=$resenc1"); 
                                $i=0;
                                while ($fuv_row1=sqlFetchArray($fuv_sql)){
                                    $result=unserialize($fuv_row1['screen_names']);
                                    foreach($result as $val) {
                                       if (stripos($val, "Unused") == false) {
                                          $scr_val=explode("$$",$val);
                                          $field_id=$scr_val[2];
                                          $priority=$scr_val[1];
                                      
                                      if($resfname1=='Allcare Encounter Forms' && $allcare=='YES' ){
                                       if($field_id=='chief_complaint' || $field_id=='hpi' || $field_id=='assessment_note' || $field_id=='progress_note' || $field_id=='plan_note' || $field_id=='face2face'){
                                             if($field_id=='chief_complaint'){
                                               $field_id1='chief_complaint_';   
                                               $gname='Chief Complaint';
                                               $title1='Chief Complaint';
                                            } else if($field_id=='hpi'){
                                                $field_id1='hpi_';
                                                $gname='History of Present illness';
                                                $title1='History of Present illness';
                                            } else if($field_id=='assessment_note'){
                                                $field_id1='assessment_note_';
                                                $gname='Assessment Note';
                                                $title1='Assessment Note';
                                            } else if($field_id=='progress_note'){
                                                $field_id1='progress_note_';
                                                $gname='Progress Note';
                                                $title1='Progress Note';
                                            } else if($field_id=='plan_note'){
                                                $field_id1='plan_note_';
                                                $gname='Plan Note';
                                                $title1='Plan Note';
                                            } else if($field_id=='face2face'){
                                                $field_id1='f2f_';
                                                $gname='Face to Face HH Plan';
                                                $title1='Face to Face HH Plan';
                                            }
                                            if($priority=='Required'){
                                                //echo "select count(*) as cnt from lbf_data  where field_id LIKE  '%chief_complaint_%' AND form_id='".$resid1."'";
                                                $ra = sqlStatement("select count(*) as cnt from lbf_data  where field_id LIKE  '%$field_id1%' AND form_id='".$resid1."'");
                                                 while($frowa = sqlFetchArray($ra)){
                                                    $a = $frowa['cnt'];
                                                 }
                                                 if($a==0){
                                                   $fname.="<a href='../interface/reports/allcare_enc_forms.php?groupname=$gname&form_id1=".$resid1."&enc3=".$resenc1."&pid1=".$respid1."&inmode1=edit&file_name1=Incomplete_charts&fname1=".$resfname1."
                                                          'onclick='top.restoreSession()' ><span>".
                                                            htmlspecialchars( xl($title1), ENT_NOQUOTES)."</span></a>,";
        //                                         $groupname='"Chief Complaint"';
        //                                         $form_name='"Allcare Encounter Forms"';
        //                                         $fname.= "<a href='javascript:win1($groupname,$resid1,$resenc1,$respid1,$form_name);' onmouseover='self.status='Open A Window'; return true;'>Chief Complaint</a>".",";
                                               }else {
                                                   $fname_comp.="<a href='../interface/reports/allcare_enc_forms.php?groupname=$gname&form_id1=".$resid1."&enc3=".$resenc1."&pid1=".$respid1."&inmode1=edit&file_name1=Incomplete_charts&fname1=".$resfname1."
                                                          'onclick='top.restoreSession()' ><span>".
                                                            htmlspecialchars( xl($title1), ENT_NOQUOTES)."</span></a>,";
                                               }
                                           } else if($priority=='Optional'){
                                               
                                                $ra = sqlStatement("select count(*) as cnt from lbf_data  where field_id LIKE  '%$field_id1%' AND form_id='".$resid1."'");
                                                 while($frowa = sqlFetchArray($ra)){
                                                    $a = $frowa['cnt'];
                                                 }
                                                 if($a==0){
                                                   $fname_opt.="<a href='../interface/reports/allcare_enc_forms.php?groupname=$gname&form_id1=".$resid1."&enc3=".$resenc1."&pid1=".$respid1."&inmode1=edit&file_name1=Incomplete_charts&fname1=".$resfname1."
                                                          'onclick='top.restoreSession()' ><span>".
                                                            htmlspecialchars( xl($title1), ENT_NOQUOTES)."</span></a>,";
        //                                         $groupname='"Chief Complaint"';
        //                                         $form_name='"Allcare Encounter Forms"';
        //                                         $fname.= "<a href='javascript:win1($groupname,$resid1,$resenc1,$respid1,$form_name);' onmouseover='self.status='Open A Window'; return true;'>Chief Complaint</a>".",";
                                               }else {
                                                   $fname_opt.="<a href='../interface/reports/allcare_enc_forms.php?groupname=$gname&form_id1=".$resid1."&enc3=".$resenc1."&pid1=".$respid1."&inmode1=edit&file_name1=Incomplete_charts&fname1=".$resfname1."
                                                          'onclick='top.restoreSession()' ><span>".
                                                            htmlspecialchars( xl($title1), ENT_NOQUOTES)."</span></a>,";
                                               }
                                               
                                           } 
                                        }

                                      }else if($resfname1=='New Patient Encounter' && $allcare=='NO') {
                                        
                                         if($field_id=='chief_complaint' || $field_id=='hpi' || $field_id=='assessment_note' || $field_id=='progress_note' || $field_id=='plan_note' || $field_id=='face2face'){
                                            if($field_id=='chief_complaint'){
                                               $gname='Chief Complaint';
                                               $title1='Chief Complaint';
                                            } else if($field_id=='hpi'){
                                                $gname='History of Present illness';
                                                $title1='History of Present illness';
                                            } else if($field_id=='assessment_note'){
                                                $gname='Assessment Note';
                                                $title1='Assessment Note';
                                            } else if($field_id=='progress_note'){
                                                $gname='Progress Note';
                                                $title1='Progress Note';
                                            } else if($field_id=='plan_note'){
                                                $gname='Plan Note';
                                                $title1='Plan Note';
                                            } else if($field_id=='face2face'){
                                                $gname='Face to Face HH Plan';
                                                $title1='Face to Face HH Plan';
                                            }
                                            if($priority=='Required'){
                                                 $fname.="<a href='../interface/reports/allcare_enc_forms.php?groupname=$gname&enc3=".$resenc1."&pid1=".$respid1."&inmode1=edit&file_name1=Incomplete_charts&fname1=".$resfname1."&form_id1=''
                                                          'onclick='top.restoreSession()' ><span>".
                                                            htmlspecialchars( xl($title1), ENT_NOQUOTES)."</span></a>,";
                                            } else if($priority=='Optional'){
                                                 $fname_opt.="<a href='../interface/reports/allcare_enc_forms.php?groupname=$gname&enc3=".$resenc1."&pid1=".$respid1."&inmode1=edit&file_name1=Incomplete_charts&fname1=".$resfname1."&form_id1=''
                                                          'onclick='top.restoreSession()' ><span>".
                                                            htmlspecialchars( xl($title1), ENT_NOQUOTES)."</span></a>,";
                                            } 
                                        }
                                  }
                                       if($field_id=='ros'){
                                             if($priority=='Required'){
                                                $resid_ros=sqlStatement("select *  from forms where form_name  ='Allcare Review Of Systems' AND encounter=$resenc1 AND pid=$respid1  AND deleted=0 ORDER BY id DESC");  
                                                $frow_ros = sqlFetchArray($resid_ros); 
                                                $fid1=$frow_ros['form_id'];
                                                if($ros=='NO'){
                                                 //$fname='<a href="/interface/reports/form_load.php?formname=allcare_ros&edit=custom">Allcare_ros</a>'.",";
                                                 $fname.="<a href='../interface/forms/allcare_ros/new_custom.php?formname=allcare_ros&encounter=$resenc1&pid=$respid1&location=provider_portal'
                                                               ><span>".
                                                                htmlspecialchars( xl('Allcare Ros'), ENT_NOQUOTES)."</span></a>,";
                                                } else {
                                                     $fname_comp.="<a href='../interface/forms/allcare_ros/view_custom.php?formname=allcare_ros&encounter=$resenc1&pid=$respid1&id=$fid1&location=provider_portal'
                                                               ><span>".
                                                                htmlspecialchars( xl('Allcare Ros'), ENT_NOQUOTES)."</span></a>,";
                                                }
                                             }else if($priority=='Optional'){
                                                 if($ros=='NO'){
                                                 //$fname='<a href="/interface/reports/form_load.php?formname=allcare_ros&edit=custom">Allcare_ros</a>'.",";
                                                 $fname_opt.="<a href='../interface/forms/allcare_ros/new_custom.php?formname=allcare_ros&encounter=$resenc1&pid=$respid1&location=provider_portal'
                                                               ><span>".
                                                                htmlspecialchars( xl('Allcare Ros'), ENT_NOQUOTES)."</span></a>,";
                                                } else {
                                                     $fname_opt.="<a href='../interface/forms/allcare_ros/view_custom.php?formname=allcare_ros&encounter=$resenc1&pid=$respid1&id=$fid1&location=provider_portal'
                                                               ><span>".
                                                                htmlspecialchars( xl('Allcare Ros'), ENT_NOQUOTES)."</span></a>,";
                                                }
                                             }  
                                        }else if($field_id=='physical_exam'){
                                             $resid_pe=sqlStatement("select *  from forms where form_name  ='Allcare Physical Exam' AND encounter=$resenc1 AND pid=$respid1  AND deleted=0 ORDER BY id DESC");  
                                             $frow_pe = sqlFetchArray($resid_pe);
                                             $fid=$frow_pe['form_id'];
                                             if($priority=='Required'){ 
                                                if($pe=='NO'){
                                                     $fname.="<a href='../interface/forms/allcare_physical_exam/new_custom.php?formname=allcare_physical_exam&edit=custom_pe&encounter=$resenc1&pid=$respid1&location=provider_portal'
                                                                   ><span>".
                                                                    htmlspecialchars( xl('Allcare Physical_exam'), ENT_NOQUOTES)."</span></a>,";
                                                      //$fname.="<a href='/interface/reports/form_load.php?formname=allcare_physical_exam&encounter=$resenc1&pid=$respid1'>Allcare Physical_exam</a>".",";
                //                                     $fname.= "<a href='javascript:win1('/interface/patient_file/encounter/load_form.php?formname=allcare_physical_exam&edit=custom_pe');' onmouseover='self.status='Open A Window'; return true;'>Chief Complaint</a>".",";
                                                }else {
                                                    $fname_comp.="<a href='../interface/forms/allcare_physical_exam/new_custom.php?formname=allcare_physical_exam&edit=custom_pe&encounter=$resenc1&pid=$respid1&id=$fid&location=provider_portal'
                                                                   ><span>".
                                                                    htmlspecialchars( xl('Allcare Physical_exam'), ENT_NOQUOTES)."</span></a>,";
                                                }
                                            }else if($priority=='Optional'){
                                                 if($pe=='NO'){
                                                     $fname_opt.="<a href='../interface/forms/allcare_physical_exam/new_custom.php?formname=allcare_physical_exam&edit=custom_pe&encounter=$resenc1&pid=$respid1&location=provider_portal'
                                                                   ><span>".
                                                                    htmlspecialchars( xl('Allcare Physical_exam'), ENT_NOQUOTES)."</span></a>,";
                                                      //$fname.="<a href='/interface/reports/form_load.php?formname=allcare_physical_exam&encounter=$resenc1&pid=$respid1'>Allcare Physical_exam</a>".",";
                //                                     $fname.= "<a href='javascript:win1('/interface/patient_file/encounter/load_form.php?formname=allcare_physical_exam&edit=custom_pe');' onmouseover='self.status='Open A Window'; return true;'>Chief Complaint</a>".",";
                                                }else {
                                                    $fname_opt.="<a href='../interface/forms/allcare_physical_exam/new_custom.php?formname=allcare_physical_exam&edit=custom_pe&encounter=$resenc1&pid=$respid1&id=$fid&location=provider_portal'
                                                                   ><span>".
                                                                    htmlspecialchars( xl('Allcare Physical_exam'), ENT_NOQUOTES)."</span></a>,";
                                                }
                                            }
                                        }
                                     //vitals 
                                     else if($field_id=='vitals'){
                                          $resid_vt=sqlStatement("select *  from forms where form_name  ='Vitals' AND encounter=$resenc1 AND pid=$respid1  AND deleted=0 ORDER BY id DESC");  
                                             $frow_vt = sqlFetchArray($resid_vt);
                                             $fid1=$frow_vt['form_id'];
                                             if($priority=='Required'){ 
                                                if($fid1==''){
                                                     $fname.="<a href='../interface/forms/vitals/new_custom.php?formname=Vitals&encounter=$resenc1&pid=$respid1'
                                                                   ><span>".
                                                                    htmlspecialchars( xl('Vitals'), ENT_NOQUOTES)."</span></a>,";
                                                      //$fname.="<a href='/interface/reports/form_load.php?formname=allcare_physical_exam&encounter=$resenc1&pid=$respid1'>Allcare Physical_exam</a>".",";
                //                                     $fname.= "<a href='javascript:win1('/interface/patient_file/encounter/load_form.php?formname=allcare_physical_exam&edit=custom_pe');' onmouseover='self.status='Open A Window'; return true;'>Chief Complaint</a>".",";
                                                }else {
                                                    $fname_comp.="<a href='../interface/forms/vitals/view_custom.php?formname=Vitals&encounter=$resenc1&pid=$respid1&id=$fid1'
                                                                   ><span>".
                                                                    htmlspecialchars( xl('Vitals'), ENT_NOQUOTES)."</span></a>,";
                                                }
                                            }else if($priority=='Optional'){
                                                 if($fid1==''){
                                                     $fname_opt.="<a href='../interface/forms/vitals/new_custom.php?formname=Vitals&encounter=$resenc1&pid=$respid1'
                                                                   ><span>".
                                                                    htmlspecialchars( xl('Vitals'), ENT_NOQUOTES)."</span></a>,";
                                                      //$fname.="<a href='/interface/reports/form_load.php?formname=allcare_physical_exam&encounter=$resenc1&pid=$respid1'>Allcare Physical_exam</a>".",";
                //                                     $fname.= "<a href='javascript:win1('/interface/patient_file/encounter/load_form.php?formname=allcare_physical_exam&edit=custom_pe');' onmouseover='self.status='Open A Window'; return true;'>Chief Complaint</a>".",";
                                                }else {
                                                    $fname_opt.="<a href='../interface/forms/vitals/view_custom.php?formname=Vitals&encounter=$resenc1&pid=$respid1&id=$fid1'
                                                                   ><span>".
                                                                    htmlspecialchars( xl('Vitals'), ENT_NOQUOTES)."</span></a>,";
                                                }
                                            }
                                      } 
                                       //issues
                                      else if($field_id=='medical_problem' || $field_id=='allergies' || $field_id=='medication' || $field_id=='surgeries' || $field_id=='dental_problems'){
                                           if($field_id=='medical_problem'){
                                               $type='medical_problem';
                                               $title='Medical Problems';
                                           }else if($field_id=='allergies'){
                                                $type='allergy';
                                                $title='Allergies';
                                           } else if($field_id=='medication'){
                                                $type='medication';
                                                $title='Medications';
                                           }else if($field_id=='surgeries'){
                                                $type='surgery';
                                                $title='Surgeries';
                                           }else if($field_id=='dental_problems'){
                                                $type='dental';
                                                $title='Dental Issues';
                                           }
                                           $resid_med=sqlStatement("select *  from lists li INNER JOIN issue_encounter ie ON ie.pid=li.pid AND ie.list_id=li.id where type='$type' AND ie.encounter=$resenc1 AND li.pid=$respid1");  
                                             $frow_med = sqlFetchArray($resid_med);
                                            // $fid2=$frow_alr['id'];
                                             if($priority=='Required'){ 
                                                if(!empty($frow_med)){
                                                    
                                                     $fname_comp.="<a href='../interface/patient_file/summary/stats_full_custom.php?active=all&category=$type&encounter=$resenc1&pid=$respid1&id=$fid1&location=provider_portal'
                                                                   ><span>".
                                                                    htmlspecialchars( xl($title), ENT_NOQUOTES)."</span></a>,";
                                                      //$fname.="<a href='/interface/reports/form_load.php?formname=allcare_physical_exam&encounter=$resenc1&pid=$respid1'>Allcare Physical_exam</a>".",";
                //                                     $fname.= "<a href='javascript:win1('/interface/patient_file/encounter/load_form.php?formname=allcare_physical_exam&edit=custom_pe');' onmouseover='self.status='Open A Window'; return true;'>Chief Complaint</a>".",";
                                                }else {
                                                   
                                                    $fname.="<a href='../interface/patient_file/summary/stats_full_custom.php?active=all&category=$type&encounter=$resenc1&pid=$respid1&location=provider_portal'
                                                                   ><span>".
                                                                    htmlspecialchars( xl($title), ENT_NOQUOTES)."</span></a>,";
                                                }
                                            }else if($priority=='Optional'){
                                                 if(!empty($frow_med)){
                                                    
                                                     $fname_opt.="<a href='../interface/patient_file/summary/stats_full_custom.php?active=all&category=$type&encounter=$resenc1&pid=$respid1&id=$fid1&location=provider_portal'
                                                                   ><span>".
                                                                    htmlspecialchars( xl($title), ENT_NOQUOTES)."</span></a>,";
                                                      //$fname.="<a href='/interface/reports/form_load.php?formname=allcare_physical_exam&encounter=$resenc1&pid=$respid1'>Allcare Physical_exam</a>".",";
                //                                     $fname.= "<a href='javascript:win1('/interface/patient_file/encounter/load_form.php?formname=allcare_physical_exam&edit=custom_pe');' onmouseover='self.status='Open A Window'; return true;'>Chief Complaint</a>".",";
                                                }else {
                                                   
                                                    $fname_opt.="<a href='../interface/patient_file/summary/stats_full_custom.php?active=all&category=$type&encounter=$resenc1&pid=$respid1&location=provider_portal'
                                                                   ><span>".
                                                                    htmlspecialchars( xl($title), ENT_NOQUOTES)."</span></a>,";
                                                }
                                            }
                                      }
                                      //Immunizations
                                     else if($field_id=='immunization'){
                                         $resid_im=sqlStatement("select *  from immunizations where  patient_id=$respid1  ");  
                                             $frow_im = sqlFetchArray($resid_im);
                                            // $fid1=$frow_im['form_id'];
                                             if($priority=='Required'){ 
                                                if(!empty($frow_im)){
                                                     $fname_comp.="<a href='../interface/patient_file/summary/immunizations_custom.php?pid=$respid1'
                                                                   ><span>".
                                                                    htmlspecialchars( xl('Immunizations'), ENT_NOQUOTES)."</span></a>,";
                                                 }else {
                                                    $fname.="<a href='../interface/patient_file/summary/immunizations_custom.php?pid=$respid1'
                                                                   ><span>".
                                                                    htmlspecialchars( xl('Immunizations'), ENT_NOQUOTES)."</span></a>,";
                                                }
                                            }else if($priority=='Optional'){
                                                 if(!empty($frow_im)){
                                                     $fname_opt.="<a href='../interface/patient_file/summary/immunizations_custom.php?pid=$respid1'
                                                                   ><span>".
                                                                    htmlspecialchars( xl('Immunizations'), ENT_NOQUOTES)."</span></a>,";
                                                }else {
                                                    $fname_opt.="<a href='../interface/patient_file/summary/immunizations_custom.php?pid=$respid1'
                                                                   ><span>".
                                                                    htmlspecialchars( xl('Immunizations'), ENT_NOQUOTES)."</span></a>,";
                                                }
                                            }
                                     } 
                                     //History
                                     else if($field_id=='history_past' || $field_id=='family_history' || $field_id=='family_med_con' || $field_id=='family_exam_test' || field_id=='history_social'){
                                       
                                        if($i==0) {
                                         $resid_his=sqlStatement("select *  from history_data where  pid=$respid1  ");  
                                             $frow_his = sqlFetchArray($resid_his);
                                            // $fid1=$frow_im['form_id'];
                                             if($priority=='Required'){ 
                                                if(!empty($frow_his)){
                                                     $fname_comp.="<a href='../interface/patient_file/history/history_custom.php?pid=$respid1'
                                                                   ><span>".
                                                                    htmlspecialchars( xl('History'), ENT_NOQUOTES)."</span></a>,";
                                                 }else {
                                                    $fname.="<a href='../interface/patient_file/history/history_custom.php?pid=$respid1'
                                                                   ><span>".
                                                                    htmlspecialchars( xl('History'), ENT_NOQUOTES)."</span></a>,";
                                                }
                                            }else if($priority=='Optional'){
                                                 if(!empty($frow_his)){
                                                     $fname_opt.="<a href='../interface/patient_file/history/history_custom.php?pid=$respid1'
                                                                   ><span>".
                                                                    htmlspecialchars( xl('History'), ENT_NOQUOTES)."</span></a>,";
                                                }else {
                                                    $fname_opt.="<a href='../interface/patient_file/history/history_custom.php?pid=$respid1'
                                                                   ><span>".
                                                                    htmlspecialchars( xl('History'), ENT_NOQUOTES)."</span></a>,";
                                                }
                                            }
                                     }
                                     $i++;
                                  }
                                }
                            }      
                         }     
                            
                                   $fname1=rtrim($fname,",");
                                   $fname_comp1=rtrim($fname_comp,",");
                                    $fname_opt1=rtrim($fname_opt,",");
                                    $query="select CONCAT(p.fname,' ',p.lname) AS Patient_Name,p.sex AS Gender,DATE_FORMAT(f.date, '%Y-%m-%d') as date1,f.encounter AS Encounter,f.elec_signedby,f.elec_signed_on,f.pid,e.pc_facility,e.pc_aid,e.pc_catid,f.pc_catid,f.facility_id AS Facility_Name ,f.provider_id from form_encounter f  INNER JOIN patient_data p ON p.pid=f.pid LEFT JOIN openemr_postcalendar_events e ON e.pc_pid=f.pid and e.pc_eventDate=DATE_FORMAT(f.date, '%Y-%m-%d') AND e.pc_catid=f.pc_catid  where  f.encounter=$resenc1 AND f.pid=$respid1 ";
                                    //echo $query;
                                    $sql = sqlStatement($query);
                                    $frow3 = sqlFetchArray($sql);
                                    if(!empty($frow3)){
                                        if($frow3['elec_signedby']=='' && $frow3['elec_signed_on']==''){
                                            $frow3['finalized_val']='not_finalized';
                                        } else {
                                            $frow3['finalized_val']='finalized';
                                        }
                                        $frow3['Required_Forms_Complete']=$fname_comp1;
                                        $frow3['Required_Forms_Incomplete']=$fname1;
                                        $frow3['Optional_Forms']=$fname_opt1;
                                           //echo '<pre>';print_r($frow3); echo '</pre>';
                                        $sql1=sqlStatement("select * from forms where form_name='Audit Form' AND deleted=0 AND encounter='$resenc1'");
                                        $audit_res=sqlFetchArray($sql1);
                                             if(empty($audit_res)){
                                                $frow3['Audit_Status']='Incomplete';
                                                //array_push($frow3,'Incomplete');
                                             } else {
                                                  $sql2=sqlStatement("select * from tbl_form_audit where id='".$audit_res['form_id']."'");
                                                  $audit_st = sqlFetchArray($sql2);
                                                  if(!empty($audit_st)){
                                                      $sql = sqlStatement("select CONCAT(p.fname,' ',p.lname) AS Patient_Name,p.sex AS Gender,f.encounter AS Encounter,fe.audited_status from forms f INNER JOIN patient_data p ON p.pid=f.pid  INNER JOIN  form_encounter fe ON fe.encounter=f.encounter where form_name='Audit Form' AND deleted=0 AND f.encounter='".$resenc1."'");
                                                      $frow2 = sqlFetchArray($sql);
                                                      if(!empty($frow2) && $frow2['audited_status']=='Completed'){
                                                        $frow3['Audit_Status']='Complete';
                                                        // array_push($frow3,'Complete');
                                                      }else {

                                                         $frow3['Audit_Status']='Incomplete';
                                                          //array_push($frow3,'Incomplete');
                                                      }
                                                  }
                                             }   
                                    }         
                                 
                                      if(!empty($audit_stat)){
                                        foreach($audit_stat as $value5){
                                           if($value5!=''){   
                                               if($value5=='1'){
                                                   if(!in_array('Incomplete',$frow3)){
                                                       $frow31=$frow3;
                                                   }
                                               }
                                               if($value5=='0'){
                                                  if(!in_array('Complete',$frow3)){
                                                       $frow31=$frow3;
                                                   }
                                               }
                                           }else {
                                               return $frow3;
                                           }
                                        }
                                       return $frow31;
                                    }else {
                                         return $frow3;
                                    }
            }
?>

<!DOCTYPE html>

<html>

	<head>
		<meta charset="UTF-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
	    <meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="shortcut icon" href="img/season-change.jpg" type="image/x-icon">
		<title>HealthCare</title>

		
	    <link href='http://fonts.googleapis.com/css?family=Abel' rel='stylesheet' type='text/css'>
	    <!-- <link href='http://fonts.googleapis.com/css?family=Pontano+Sans' rel='stylesheet' type='text/css'>
	    <link href='http://fonts.googleapis.com/css?family=Alegreya+Sans:300,400,500,700' rel='stylesheet' type='text/css'> -->
	    <link href='http://fonts.googleapis.com/css?family=Roboto:400,300,500' rel='stylesheet' type='text/css'>
	    <link href='http://fonts.googleapis.com/css?family=Dosis:300,400,500,600' rel='stylesheet' type='text/css'>
	    
	    
		<link rel="stylesheet" type="text/css" href="assets/css/animate.css">
		<link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">
		<link rel="stylesheet" type="text/css" href="assets/css/owl.carousel.css">
		<link rel="stylesheet" type="text/css" href="assets/css/owl.theme.css">
		<link rel="stylesheet" type="text/css" href="assets/css/owl.transitions.css">
		<link rel="stylesheet" type="text/css" href="assets/css/font-awesome.min.css">
		<link rel="stylesheet" type="text/css" href="assets/css/main.css">
		<link href='http://fonts.googleapis.com/css?family=Roboto+Condensed:400,300' rel='stylesheet' type='text/css'>
                <!--  datatable-->
<!--                <link rel="stylesheet" href="http://netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css"/>
                <link rel="stylesheet" href="http://netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap-theme.min.css"/>
-->             <link rel="stylesheet" href="css/dataTables.bootstrap.css"/>
                <link rel="stylesheet" href="css/datatables.responsive_bootstrap.css"/>
                <script src="js/responsive_datatable/jquery.min.js"></script>
                <script src="js/responsive_datatable/jquery.dataTables.min.js"></script>
                <script src="js/responsive_datatable/dataTables.bootstrap.js"></script>
                <script src="js/responsive_datatable/datatables.responsive.js"></script>

                <style>
/*                    #menu {
  text-align: center;
  transition: all ease-out 0.3s;
}*/

#menu a { color: white; }

#menu ul {
  margin: 0;
  padding: 0;
  background-color:#46A1B4;
}

#menu ul li {
  display: inline-block;
  position: relative;
}

#menu ul li > a {
  display: inline-block;
  padding: 10px;
}
#menu ul li.active > a {
/*  background: #ffffff;*/
  color: #000000;
  background-color:#ffffff;
  border-top:6px ridge #ffffff;
}
#menu ul li > a > i {
  margin-left: 15px;
  transition: all ease-out 0.3s;
  -webkit-transition: all ease-out 0.1s;
}

#menu ul li ul {
  display: none;
  position: absolute;
  top: 38px;
  width: 200px;
  text-align: left;
}

#menu ul li ul li { display: block; }

#menu ul li ul li a { display: block; }

#menu ul li:hover > a { background-color: rgba(0, 0, 0, 0.3); }

#menu ul li:hover > a > i { transform: rotateZ(90deg); }

#menu ul li:hover ul { display: block; }

/*a#toggle {
  position: fixed;
  top: 10px;
  left: 10px;
  width: 40px;
  height: 40px;
  background-color: #46A1B4;
  text-align: center;
  color: white;
  display: none;
  transition: all ease-out 0.3s;
   float: right;
}

a#toggle i {
  position: relative;
  top: 50%;
  transform: translateY(-50%);
}*/
@media screen and (max-width: 767px) {

/*a#toggle { display: block; }*/

main#content {
  margin-top: 65px;
  transition: all ease-out 0.3s;
}

/*#menu {
  position: fixed;
 // width: 250px;
  height: 100%;
  top: 0;
  left: 0;
  overflow: hidden;
  overflow-y: auto;
  background-color: transparent;
  transform: translateX(-250px);
}*/

#menu ul {
  text-align: left;
  background-color: #46A1B4;
}

#menu ul li { display: block; }

#menu ul li a { display: block; }

#menu ul li a > i { float: right; }

#menu ul li ul {
  display: none;
  position: static;
  width: 100%;
 background-color: rgba(22,160,133, 0.8);
}

#menu ul li:hover > ul { display: none; }

#menu ul li:hover > a > i { transform: rotateZ(0); }

#menu ul li.open > a { background-color: rgba(0, 0, 0, 0.3); }

#menu ul li.open > a > i { transform: rotateZ(90deg); }

#menu ul li.open > ul { display: block; }

/*div#overlay {
  display: block;
  visibility: hidden;
  position: fixed;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.8);
  transition: all ease-out 0.3s;
  z-index: 1;
  opacity: 0;
}

html.open-menu { overflow: hidden; }

html.open-menu div#overlay {
  visibility: visible;
  opacity: 1;
  width: calc(-150%);
  left: 250px;
}

html.open-menu a#toggle,
 html.open-menu main#content { transform: translateX(250px); }

html.open-menu nav#menu {
  z-index: 3;
  transform: translateX(0);
}*/
}
/* drag and drop               */
ul#slippylist{
    width:120px;
    height:80px;
    padding-left: 0px !important;
}
ul#slippylist li {
    user-select: none;
    -webkit-user-select: none;
/*    border 1px solid lightgrey;*/
    list-style: none;
/*    height: 25px;*/
/*    max-width: 200px;*/
    cursor: move;
    margin-top: -1px;
    margin-bottom: 0;
    padding-right:50px;
    padding-left:7px;
    font-weight: bold;
    color: black;
    text-align:left;
}
    ul#slippylist li.slip-reordering {
        box-shadow: 0 2px 10px rgba(0,0,0,0.45);
    }

  </style>
  <script>
function divclick(cb, divid) {
     var divstyle = document.getElementById(divid).style;
     if (cb.checked) {
      divstyle.display = 'block';
     } else {
      divstyle.display = 'none';
     }
     return true;
 }
  </script> 
   <script language="javascript"> 

           function DoPost(page_name, provider) {
                method = "post"; // Set method to post by default if not specified.

               //alert(provider);

                var form = document.createElement("form");
                form.setAttribute("method", method);
                form.setAttribute("action", page_name);
                var key='provider';
//                for(var key in params) {
//                    if(params.hasOwnProperty(key)) {
                        var hiddenField = document.createElement("input");
                        hiddenField.setAttribute("type", "hidden");
                        hiddenField.setAttribute("name", key);
                        hiddenField.setAttribute("value", provider);

                        form.appendChild(hiddenField);
//                     }
                //}

               document.body.appendChild(form);
                form.submit();
        }
        </script>       

	</head>

	<body>
           <script type='text/javascript'>
             function submitme() {
               
                 var f = document.forms['dropdown_filters'];
                 f.submit();
                
            }
            </script>
		<section class= "navs">
			<nav class="navbar navbar-default navbar-fixed-top" role="navigation" id="menu">
                             <div style="background-color:#e7e7e7 !important; height:20px; padding-right:20px;"><a style="color:black; float:right;" href="logout_page.php">Logout</a></div>
  				<div class="container-fluid">
    				<!-- Brand and toggle get grouped for better mobile display -->
    				<div class="navbar-header">
      					<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
					        <span class="sr-only">Toggle navigation</span>
					        <span class="icon-bar"></span>
					        <span class="icon-bar"></span>
					        <span class="icon-bar"></span>
	      				</button>
	      				<a class="navbar-brand logo" href="#">
	      					<span><i class="fa fa-stethoscope"></i></span>
	      					HealthCare
	      				</a>
	    			</div>
		
		    		<!-- Collect the nav links, forms, and other content for toggling -->
<!--                                 <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                                     <ul class='navbar-right'>
                                            <li><a href="#">Item 01</a></li>
                                            <li><a href="#">Item 02</a></li>
                                            <li><a href="#">Item 03</a><ul>
                                                <li><a href="#">Submenu 01</a></li>
                                                <li><a href="#">Submenu 02</a></li>
                                                <li><a href="#">Submenu 03</a></li>
                                              </ul></li>

                                            <li><a href="#">Item 04</a>

                                            </li>
                                            <li><a href="#">Item 05</a></li>
                                    </ul>
                                </div>-->
                                 <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                                   <?php $sql12=sqlStatement("SELECT * FROM  `list_options` WHERE list_id ='AllCareProviderPortal' ORDER BY seq");?>
                                     <ul class='navbar-right'>
                                        <?php while($row11=sqlFetchArray($sql12)){ 
                                             $mystring = $row11['option_id'];
                                             $pos = strpos($mystring, '_');
                                             if(false == $pos) {
                                                $sql_lis=sqlStatement("SELECT * FROM  `list_options` WHERE list_id ='AllCareProviderPortal' AND option_id = '$mystring' ORDER BY seq");
                                                while($row_lis=sqlFetchArray($sql_lis)){
                                                     $opt_id=$row_lis['option_id']."_";
                                                     $sql_li=sqlStatement("SELECT * FROM  `list_options` WHERE list_id ='AllCareProviderPortal' AND option_id LIKE '%$opt_id%' ORDER BY seq");
                                                     if(sqlNumRows($sql_li) != 0 ){ ?>
                                                         <li <?php if($row11['option_id']=='incomp'){ ?> class='active' <?php } ?>><a href="javascript:DoPost('<?php echo $row_sub['notes']; ?>','<?php echo $provider;  ?>')"><span><?php echo $row_lis['title']; ?></span></a>
                                                         <ul>
                                                            <?php while($row_li=sqlFetchArray($sql_li)){ 
                                                                    $ex=explode("_",$row_li['option_id']); 
                                                                    if(count($ex)==2){
                                                                       $sub1=$ex[0]."_".$ex[1];
                                                                       $sql_sub=sqlStatement("SELECT * FROM  `list_options` WHERE list_id ='AllCareProviderPortal' AND option_id = '$sub1' ORDER BY seq");
                                                                       $row_sub=sqlFetchArray($sql_sub); ?>
                                                                         <li <?php if($row_sub['option_id']=='incomp_enc'){ ?> class='active' <?php } ?>><a href="javascript:DoPost('<?php echo $row_sub['notes']; ?>','<?php echo $provider;  ?>')"><span><?php  echo $row_sub['title']; ?></span></a> 
                                                                                   </li>    
                                                                <?php }
                                                                } ?>
                                                        </ul></li>
                                                    <?php }else{ if($row11['option_id']=='incomp'){?>
                                                             <li class="active"><a href="javascript:DoPost('<?php echo $row11['notes']; ?>','<?php echo $provider;  ?>')"><span><?php echo $row11['title']; ?></span></a></li>
                                                    <?php }else{ ?>
                                                             <li><a href="javascript:DoPost('<?php echo $row11['notes']; ?>','<?php echo $provider;  ?>')"><span><?php echo $row11['title']; ?></span></a></li>
                                                   <?php }
                                                    }
                                                 }
                                             }
                                         } ?>  
                                    </ul>
                                </div>
				</div><!-- container-fluid -->
			</nav>
               </section>
             <section id= "services">
                <div class= "container">
		    <div class= "row">
			<div class= "col-lg-12 col-sm-12 col-xs-12">
                            <h2 class= "headline text-center">
                            </h2>
                            <?php $display_style='none' ?>

                            <input type='checkbox' name='filter' id='filter' value='1' onclick='return divclick(this,"filters");'   <?php if ($display_style == 'block') echo " checked"; ?>><b>Filters</b>
                            <div id='filters' class="appointment1" style='display:<?php echo $display_style; ?>'>
                              <form name="dropdown_filters" id="dropdown_filters" action="provider_incomplete_charts.php?provider=<?php echo $provider; ?>" method="POST" >
                                  <table>
                                      <tr style='border-spacing:5em !important;'>
                                        <td><?php xl('Patient','e'); ?>:</td>
                                        <td>
                                          <?php
                                                 // Build a drop-down list of providers.
                                                //
//                                               $query = "SELECT pid, lname, fname FROM patient_data ".
//                                                  " ORDER BY lname, fname"; //(CHEMED) facility filter
                                                $query="SELECT fe.pid, lname, fname from form_encounter fe  INNER JOIN patient_data p ON p.pid=fe.pid  INNER JOIN openemr_postcalendar_events o ON o.pc_pid = fe.pid "
                                                                  . " AND o.pc_catid = fe.pc_catid AND fe.facility_id = o.pc_facility
                                                                      AND o.pc_eventDate = DATE_FORMAT( fe.date,  '%Y-%m-%d' )where o.pc_aid ='".$id['id']."' group by fe.pid  ORDER BY lname, fname ";
                                                $ures = sqlStatement($query);

                                                echo "   <select name='form_patient[]'  multiple  id='form_patient'>\n";
                                                echo "    <option value=''"; if(!empty($patient)){ foreach($patient as $val2) { if ($val2=='') echo " selected";}  }
                                                echo  "selected"; echo" >-- " . xl('All') . " --\n";

                                                while ($urow = sqlFetchArray($ures)) {
                                                        $pid1 = $urow['pid'];
                                                        echo "    <option value='$pid1'";
                                                        if(!empty($patient)){
                                                        foreach($patient as $val2){    
                                                        if ($pid1 == $val2) echo " selected";} }
                                                        echo ">" . $urow['fname'] . ", " . $urow['lname'] . "\n";
                                                }

                                                echo "   </select>\n";

                                                ?>
                                        </td></tr>
                                      
                                        <tr>
                                        <td><?php xl('Audit Status','e'); ?>:</td>
                                        <td>
                                          <?php
                                                echo "   <select name='form_audit_stat[]'  multiple  id='form_audit_stat'>\n";
                                                echo "    <option value=''"; if(!empty($audit_stat)){ foreach($audit_stat as $val8){if($val8=='') echo "selected";  } }
                                                echo  "selected"; echo" >-- " . xl('All') . " --\n";
                                                echo "<option value='1'"; if(!empty($audit_stat)){ foreach($audit_stat as $val8){if($val8=='1') echo "selected"; } } echo ">Complete</option>";     
                                                echo "<option value='0'"; if(!empty($audit_stat)){ foreach($audit_stat as $val8){if($val8=='0') echo "selected"; } } echo">Incomplete</option>";
                                                echo "   </select>\n";

                                                ?>
                                            </td>
                                      </tr>
                                          <tr>
                                            <td><?php xl('Encounter Status','e'); ?>:</td>
                                            <td>
                                              <?php
                                                    echo "   <select name='form_enc_stat[]'  multiple  id='form_enc_stat'>\n";
                                                    echo "    <option value=''";  if(!empty($enc_stat)){ foreach($enc_stat as $val2){if($val2=='') echo "selected"; } }
                                                    echo  "selected"; echo" >-- " . xl('All') . " --\n";
                                                    echo "<option value='1'"; if(!empty($enc_stat)){ foreach($enc_stat as $val2){if($val2=='1') echo "selected"; } } echo ">Complete</option>";     
                                                    echo "<option value='0'"; if(!empty($enc_stat)){ foreach($enc_stat as $val2){if($val2=='0') echo "selected"; } } echo">Incomplete</option>";
                                                    echo "   </select>\n";

                                                    ?>
                                            </td>
                                      </tr>
                                          <tr>
                                            <td><?php xl('From','e'); ?>:</td>
                                            <td><input type='text' name='form_from_date' id="form_from_date"
                                                    size='10' value='<?php echo $from ?>'
                                                    onkeyup='datekeyup(this,mypcc)' onblur='dateblur(this,mypcc)'
                                                    title='yyyy-mm-dd'> <img src='../interface/pic/show_calendar.gif'
                                                    align='absbottom' width='24' height='22' id='img_from_date'
                                                    border='0' alt='[?]' style='cursor: pointer'
                                                    title='<?php xl('Click here to choose a date','e'); ?>'></td></tr>
                                            <tr><td><?php xl('To','e'); ?>:</td>
                                            <td><input type='text' name='form_to_date' id="form_to_date"
                                                    size='10' value='<?php echo $to ?>'
                                                    onkeyup='datekeyup(this,mypcc)' onblur='dateblur(this,mypcc)'
                                                    title='yyyy-mm-dd'> <img src='../interface/pic/show_calendar.gif'
                                                    align='absbottom' width='24' height='22' id='img_to_date'
                                                    border='0' alt='[?]' style='cursor: pointer'
                                                    title='<?php xl('Click here to choose a date','e'); ?>'></td><td>&nbsp;</td>
                                      </tr>
                                      <tr>
                            <td ><?php xl('Facility','e'); ?>:</td>

                            <td><?php if(!empty($facility)){$facility1=implode("|",$facility); }  facility_list1(strip_escape_custom($facility1), 'form_facility1[]' ,'form_facility1',true); ?></td></tr>
                            <tr><td class='bold' nowrap><?php echo xlt('Visit Category:'); ?></td>
                             <td class='text'>
                              <select name='pc_catid[]' multiple id='pc_catid'>
                                <option value='' selected >-- <?php echo xlt('ALL'); ?> --</option>
                                    <?php
                                     $cres = sqlStatement("SELECT pc_catid, pc_catname " .
                                      "FROM openemr_postcalendar_categories where pc_catid IN ( 15, 16, 17, 18, 19, 20, 24, 25, 29, 44 ) ORDER BY pc_catname");
                                     while ($crow = sqlFetchArray($cres)) {
                                      $catid = $crow['pc_catid'];
                                      if ($catid < 9 && $catid != 5) continue;
                                      echo "       <option value='" . attr($catid) . "'";
                                      if(!empty($visit_cat)){foreach($visit_cat as $value9) { if ($crow['pc_catid'] == $value9) echo " selected"; }}
                                      echo ">" . text(xl_appt_category($crow['pc_catname'])) . "</option>\n";
                                     }
                                    ?>
                              </select>
                             </td>
                            </tr>
                                  </table>
                                  <div align='right'> <a href="javascript:;"  class="btn btn-default" onclick="submitme();">
                                    <span><?php echo htmlspecialchars( xl('Submit'), ENT_NOQUOTES); ?></span>
                                 </a></div>
                             </form>
                           </div>
                                          <div  style='margin-top:10px'> <!-- start main content div -->
                   <div id="dvLoading1" style="display:none"></div>
                         <div id="div_noform">
                            <table class='display'  id='vnfFilter1' border="1">

                            <?php
                             print "<thead><tr class='showborder_head'><th style='width:800px;'>&nbsp;</th>"
                                     . "<th style='width:180px;' data-class='expand'>".htmlspecialchars( xl('Patient Name'), ENT_NOQUOTES)."</th>"."<th style='width:180px;' data-hide='phone' data-name='Gender'>".htmlspecialchars( xl('Gender'), ENT_NOQUOTES)."</th>"."<th style='width:180px;' data-hide='phone' data-name='Encounter'>".htmlspecialchars( xl('Encounter'), ENT_NOQUOTES)."</th>"."<th style='width:180px;' data-hide='phone' data-name='Provider' >".htmlspecialchars( xl('Provider'), ENT_NOQUOTES)."</th>"."<th style='width:180px;' data-hide='phone' data-name='Facility'>".htmlspecialchars( xl('Facility'), ENT_NOQUOTES)."</th>".
                                     "<th style='width:180px;' data-hide='phone' data-name='Visit Category'>".htmlspecialchars( xl('Visit Category'), ENT_NOQUOTES)."</th>"."<th style='width:180px;' data-hide='phone' data-name='Required forms complete'>".htmlspecialchars( xl('Required forms complete'), ENT_NOQUOTES)."</th>".
                                              "<th style='width:180px;' data-hide='phone' data-name='Required forms incomplete'>".htmlspecialchars( xl('Required forms incomplete'), ENT_NOQUOTES)."</th>"."<th style='width:180px;' data-hide='phone' data-name='Optional forms'>".htmlspecialchars( xl('Optional forms'), ENT_NOQUOTES)."</th>"."<th style='width:180px;' data-hide='phone' data-name='Audit Status'>".htmlspecialchars( xl('Audit Status'), ENT_NOQUOTES)."</th>";
                                      echo "</tr></thead>\n";
                                     //$resid1=sqlStatement("select DISTINCT(f.form_id),f.pid,f.encounter from forms f INNER JOIN patient_data p ON p.pid=f.pid  INNER JOIN form_encounter fe ON fe.pid=f.pid AND fe.encounter=f.encounter where deleted=0 AND form_name='Allcare Encounter Forms' AND formdir='LBF2' AND p.cpo='YES' AND fe.pc_catid IN (15, 16, 17, 18, 19, 20, 24, 25, 29, 44 )");
                            //          $resid2=sqlStatement("SELECT DISTINCT (
                            //                                f.form_id
                            //                                ), f.pid, f.encounter,f.form_name
                            //                                FROM forms f
                            //                                INNER JOIN patient_data p ON p.pid = f.pid
                            //                                INNER JOIN form_encounter fe ON fe.pid = f.pid
                            //                                AND fe.encounter = f.encounter
                            //                                WHERE deleted =0
                            //                                AND p.cpo =  'YES'
                            //                                AND fe.pc_catid
                            //                                IN ( 15, 16, 17, 18, 19, 20, 24, 25, 29, 44 ) AND f.form_name IN ('Allcare Encounter Forms')  AND deleted=0");  

//                                      $resid21="SELECT DISTINCT fe.pid, fe.encounter
//                                                FROM form_encounter fe                               
//                                                INNER JOIN patient_data p ON p.pid = fe.pid
//                                                WHERE fe.pc_catid
//                                                IN ( 15, 16, 17, 18, 19, 20, 24, 25, 29, 44 ) "; 
                                      $resid21="SELECT DISTINCT fe.pid, fe.encounter, l.field_id
                                                FROM form_encounter fe
                                                INNER JOIN patient_data p ON p.pid = fe.pid
                                                INNER JOIN tbl_allcare_facuservisit f ON  `facilities` REGEXP (
                                                fe.facility_id
                                                )
                                                AND  `users` REGEXP (
                                                fe.provider_id
                                                )
                                                AND  `visit_categories` REGEXP (
                                                fe.pc_catid
                                                )
                                                INNER JOIN layout_options l ON l.group_name = f.screen_group
                                                AND l.form_id = f.form_id
                                                WHERE fe.pc_catid
                                                IN ( 15, 16, 17, 18, 19, 20, 24, 25, 29, 44 ) ";
                                      
//                                      if(!empty($provider)){
//                                        foreach($provider as $value){
//                                            if($value!=''){
//                                             $pro_val.=$value.",";
//                                            }
//                                        }
//                                        $pro_val1=trim($pro_val,',');
//                                        if($pro_val1!=''){
//                                          $resid21.=  " AND fe.provider_id IN ($pro_val1)";
//                                        }
//                                    }
                                      
                                     
                                      
                                        if(!empty($id)){
                                          $resid21.=  " AND fe.provider_id ='".$id['id']."'";
                                        }
                                    
                                    
                                    if(!empty($patient)){
                                        foreach($patient as $value1){
                                            if($value1!=''){
                                             $pid_val.=$value1.",";
                                            }
                                        }
                                        $pid_val1=trim($pid_val,',');
                                        if($pid_val1!=''){
                                            $resid21.=  " AND p.pid IN ($pid_val1)";
                                        }
                                        
                                    }
                                    
                                    if(!empty($enc_stat)){
                                         foreach($enc_stat as $value2){
                                           if($value2!=''){   
                                               if($value2=='1'){
                                                   $resid21.=  " AND fe.elec_signedby !='' AND fe.elec_signed_on !=''";
                                               }
                                               if($value2=='0'){
                                                   $resid21.=  " AND fe.elec_signedby ='' AND fe.elec_signed_on =''";
                                               }
                                           }
                                        }
                                    }
                                    
                                    if($from!='' && $to!=''){
                                       $resid21.=  " AND DATE_FORMAT(fe.date, '%Y-%m-%d') BETWEEN CAST('$from' AS DATE) AND CAST('$to' AS DATE)  ";
                                    }
                                    
                                    //facility
                                    if(!empty($facility)){
                                        foreach($facility as $value3){
                                            if($value3!=''){
                                             $fac_val.=$value3.",";
                                            }
                                        }
                                       $fac_val1=trim($fac_val,',');
                                        if($fac_val1!=''){
                                            $resid21.=  " AND fe.facility_id IN ($fac_val1)";
                                        }
                                        
                                    }
                                    //visit_category
                                    if(!empty($visit_cat)){
                                        foreach($visit_cat as $value4){
                                            if($value4!=''){
                                             $cat_val.=$value4.",";
                                            }
                                        }
                                       $cat_val1=trim($cat_val,',');
                                        if($cat_val1!=''){
                                            $resid21.=  " AND fe.pc_catid IN ($cat_val1)";
                                        }
                                    }
                                   // echo $resid21;
                                      $resid2=sqlStatement($resid21);
                                    
                                      while ($resid_row1=sqlFetchArray($resid2)){
                                          $f2fenc_Y[]=$resid_row1['encounter'];
                                       }
                                     $uni_Y=array_unique($f2fenc_Y);
                                     //echo "<pre>"; print_r($uni_Y); echo "</pre>";
                                            // $uni_N=array_unique($f2fenc_N);
                                      foreach($uni_Y as $value) {
                                           $resid1=sqlStatement("select count(*) as count from forms where form_name  IN ('Allcare Review Of Systems','Allcare Physical Exam') AND encounter='$value' AND deleted=0 ");  
                                          while ($resid_row1=sqlFetchArray($resid1)){
                                              if($resid_row1['count']==0) {
                                                  $pe='NO'; $ros='NO';
                                                  $resid_enc=sqlStatement("select count(*) as count1 from forms where form_name  IN ('Allcare Encounter Forms') AND encounter='$value' AND deleted=0 "); 
                                                  $resenc_row1=sqlFetchArray($resid_enc);
                                                  if($resenc_row1['count1']==0){
                                                      $allcare='NO';
                                                      $resid3=sqlStatement("select *  from forms  where  encounter='$value' AND form_name IN ('New Patient Encounter') AND deleted=0");
                                                  }else {
                                                       $allcare='YES';
                                                      $resid3=sqlStatement("select *  from forms  where  encounter='$value' AND form_name IN ('Allcare Encounter Forms') AND deleted=0 order by id DESC LIMIT 0,1");  
                                                  }
                                                      while ($resid_row3=sqlFetchArray($resid3)) {

                                                             if ($result1 = VisitsWithNoForms($resid_row3['form_id'],$resid_row3['pid'],$resid_row3['encounter'],$resid_row3['form_name'],$ros,$pe,$allcare,$audit_stat)) {
                                                                //echo "<pre>";print_r($result1); echo "</pre>";
                                                                echo "<tr height='25'>";
                                                                echo "<td style='width:600px;'>";
                                                                    if($result1['finalized_val']=='not_finalized'){
                                                                        $enc=$result1['Encounter'];
                                                                        $pid=$result1['pid'];
                                                                        $date=$result1['date1'];
                                                                        ?>  <a href='javascript:; ' onclick="previewpost1('<?php echo $enc; ?>','<?php echo $pid; ?>','<?php echo $date; ?> ')" class='css_button_small'><span><?php echo  htmlspecialchars( xl('Preview'), ENT_NOQUOTES); ?></span></a>|  
                                                                       <?php if($result1['Audit_Status']=='Complete'){  ?> <input type='checkbox' name='finalize_<?php echo $enc; ?>' id='finalize_<?php echo $enc; ?>' onclick="postValue('<?php echo $enc; ?>','<?php echo $pid; ?>','<?php echo $date; ?> ')"/>Finalize | <?php } else if($result1['Audit_Status']=='Incomplete'){ ?>
                                                                           <input type='checkbox' name='finalize_<?php echo $enc; ?>' id='finalize_<?php echo $enc; ?>' onclick="postValue('<?php echo $enc; ?>','<?php echo $pid; ?>','<?php echo $date; ?> ')" disabled />Finalize |
                                                                       <?php } echo "<a  class='css_button_small'><span>".
                                                                                htmlspecialchars( xl('View'), ENT_NOQUOTES)."</span></a>";
                                                                     } else if($result1['finalized_val']=='finalized'){
                                                                        $enc=$result1['Encounter'];
                                                                        $pid=$result1['pid'];
                                                                        $date=$result1['date1'];
                                                                        ?><a   class='css_button_small'><span><?php echo  htmlspecialchars( xl('Preview'), ENT_NOQUOTES); ?></span></a>|
                                                                        <?php 

                                                                        if($result1['Audit_Status']=='Complete'){ ?>
                                                                         <input type='checkbox' name='finalize_<?php echo $enc; ?>' id='finalize_<?php echo $enc; ?>' onclick="postValue('<?php echo $enc; ?>','<?php echo $pid; ?>','<?php echo $date; ?> ')"  checked disabled/>Finalize | <?php 
                                                                                    //echo "select * from tbl_form_chartoutput_transactions where pid=$pid and encounter=$enc and date_of_service='$date' order by id desc";
                                                                                    $sql_id=sqlStatement("select * from tbl_form_chartoutput_transactions where pid=$pid and encounter=$enc and date_of_service='$date' order by id desc");
                                                                                    $id_row3=sqlFetchArray($sql_id);
                                                                                    $id=$id_row3['id']; 
                                                                                    $group='1Mobile';
                            //                                                        echo "<a href='print_charts.php?coid=".$id."".            
                            //                                                        "&patient_id=$pid&group=1Mobile 'onclick='' class='css_button_small'><span>".
                            //                                                        htmlspecialchars( xl('View'), ENT_NOQUOTES)."</span></a>"; ?>
                                                                                    <a href='javascript:; ' onclick="viewpost1('<?php echo $id; ?>','<?php echo $pid; ?>','<?php echo $group; ?> ')" class='css_button_small'><span><?php echo  htmlspecialchars( xl('View'), ENT_NOQUOTES); ?></span></a>

                                                                                <?php }else { ?>
                                                                                    <input type='checkbox' name='finalize_<?php echo $enc; ?>' id='finalize_<?php echo $enc; ?>' onclick="postValue('<?php echo $enc; ?>','<?php echo $pid; ?>','<?php echo $date; ?> ')"   disabled/>Finalize |
                                                                                  <?php  echo "<a  class='css_button_small'><span>".
                                                                                    htmlspecialchars( xl('View'), ENT_NOQUOTES)."</span></a>";

                                                                                }
                                                                     }
                                                                echo "</td>";
                                                                echo "<td>".$result1['Patient_Name']."</td>";
                                                                echo "<td>".$result1['Gender']."</td>";
                                                                echo "<td>".$result1['Encounter']."</td>";
                                                                echo "<td>"; if($result1['provider_id']!='') $pro_id=sqlStatement("select CONCAT(fname,' ',lname) AS provider from users where id='".$result1['provider_id']."' ");
                                                                                    $id_row4=sqlFetchArray($pro_id);
                                                                                    $proid=$id_row4['provider'];{ echo $proid; }echo"</td>";
                                                                echo "<td>";if($result1['facility_id']!='')  $pro_id2=sqlStatement("select name from facility where id='".$result1['facility_id']."' ");
                                                                                    $id_row5=sqlFetchArray($pro_id2);
                                                                                    $proid23=$id_row5['name'];{ echo $proid23; }echo"</td>";
                                                                echo "<td>"; if($result1['pc_catid']!='') $pro_id1=sqlStatement("select pc_catname from openemr_postcalendar_categories where pc_catid='".$result1['pc_catid']."' ");
                                                                                    $id_row41=sqlFetchArray($pro_id1);
                                                                                    $proid1=$id_row41['pc_catname'];{ echo $proid1; }echo"</td>";
                                                                echo "<td>"; echo $result1['Required_Forms_Complete'];   echo"</td>";
                                                                echo "<td>"; echo $result1['Required_Forms_Incomplete'];   echo"</td>";
                                                                echo "<td>"; echo $result1['Optional_Forms'];   echo"</td>";
                                                                echo "<td>"; echo $result1['Audit_Status'];   echo"</td>";     
                                                                echo "</tr>\n";
                                                             }
                                                       }
                                                 } else if($resid_row1['count']!=0){
                                                      $ros_sql=sqlStatement("select *  from forms  where  encounter='$value' AND form_name IN ('Allcare Review Of Systems') AND deleted=0");
                                                      $ros_row3=sqlFetchArray($ros_sql);
                                                      if(!empty($ros_row3)){
                                                          $ros='YES';
                                                      }else 
                                                           $ros='NO';
                                                      $pe_sql=sqlStatement("select *  from forms  where  encounter='$value' AND form_name IN ('Allcare Physical Exam') AND deleted=0");
                                                      $pe_row3=sqlFetchArray($pe_sql);
                                                      if(!empty($pe_row3)){
                                                          $pe='YES';
                                                      }else 
                                                           $pe='NO';
                                                      $resid_enc=sqlStatement("select count(*) as count1 from forms where form_name  IN ('Allcare Encounter Forms') AND encounter='$value' AND deleted=0 "); 
                                                      $resenc_row1=sqlFetchArray($resid_enc);
                                                      if($resenc_row1['count1']==0){
                                                          $allcare='NO';
                                                          $resid3=sqlStatement("select *  from forms  where  encounter='$value' AND form_name IN ('New Patient Encounter') AND deleted=0");
                                                      }else {
                                                           $allcare='YES';
                                                          $resid3=sqlStatement("select *  from forms  where  encounter='$value' AND form_name IN ('Allcare Encounter Forms') AND deleted=0 order by id DESC LIMIT 0,1");  
                                                      }
                                                      while ($resid_row3=sqlFetchArray($resid3)) {

                                                             if ($result1 = VisitsWithNoForms($resid_row3['form_id'],$resid_row3['pid'],$resid_row3['encounter'],$resid_row3['form_name'],$ros,$pe,$allcare,$audit_stat)) {
                                                               //echo "<pre>";print_r($result1); echo "</pre>";
                                                                 echo "<tr height='25'>";
                                                                        echo "<td style='width:600px;'>";
                                                                        if($result1['finalized_val']=='not_finalized'){
                                                                                 $enc=$result1['Encounter'];
                                                                                 $pid=$result1['pid'];
                                                                                 $date=$result1['date1']; 
                                                                              ?><a href='javascript:; ' onclick="previewpost1('<?php echo $enc; ?>','<?php echo $pid; ?>','<?php echo $date; ?> ')" class='css_button_small'><span><?php echo  htmlspecialchars( xl('Preview'), ENT_NOQUOTES); ?></span></a>|
                                                                                   <!--<input type="checkbox" id="finalize" name="finalize"  >Finalize-->
                                                                                  <?php if($result1['Audit_Status']=='Complete'){  ?> <input type='checkbox' name='finalize_<?php echo $enc; ?>' id='finalize_<?php echo $enc; ?>' onclick="postValue('<?php echo $enc; ?>','<?php echo $pid; ?>','<?php echo $date; ?> ')"/>Finalize | 
                                                                                  <?php } else if($result1['Audit_Status']=='Incomplete') {?> <input type='checkbox' name='finalize_<?php echo $enc; ?>' id='finalize_<?php echo $enc; ?>' onclick="postValue('<?php echo $enc; ?>','<?php echo $pid; ?>','<?php echo $date; ?> ')" disabled />Finalize |  <?php } 
                                                                                  echo "<a  class='css_button_small'><span>".
                                                                                    htmlspecialchars( xl('View'), ENT_NOQUOTES)."</span></a>";

                                                                        }else if($result1['finalized_val']=='finalized'){
                                                                                $enc=$result1['Encounter'];
                                                                                $pid=$result1['pid'];
                                                                                $date=$result1['date1'];
                                                                                ?><a href='javascript:void(0); ' onclick="previewpost1('<?php echo $enc; ?>','<?php echo $pid; ?>','<?php echo $date; ?> ')" class='css_button_small'><span><?php echo  htmlspecialchars( xl('Preview'), ENT_NOQUOTES); ?></span></a>|
                                                                                <?php 

                                                                                if($result1['Audit_Status']=='Complete'){ ?>
                                                                                <input type='checkbox' name='finalize_<?php echo $enc; ?>' id='finalize_<?php echo $enc; ?>' onclick="postValue('<?php echo $enc; ?>','<?php echo $pid; ?>','<?php echo $date; ?> ')"  checked disabled/>Finalize | <?php 
                                                                                   // echo "select * from tbl_form_chartoutput_transactions where pid=$pid and encounter=$enc and date_of_service='$date' order by id desc";
                                                                                    $sql_id=sqlStatement("select * from tbl_form_chartoutput_transactions where pid=$pid and encounter=$enc and date_of_service='$date' order by id desc");
                                                                                    $id_row3=sqlFetchArray($sql_id);
                                                                                    $id=$id_row3['id'];
                                                                                    $group='1Mobile';
                            //                                                        echo "<a href='print_charts.php?coid=".$id."".            
                            //                                                        "&patient_id=$pid&group=1Mobile 'onclick='' class='css_button_small'><span>".
                            //                                                        htmlspecialchars( xl('View'), ENT_NOQUOTES)."</span></a>"; ?>
                                                                                 <a href='javascript:; ' onclick="viewpost1('<?php echo $id; ?>','<?php echo $pid; ?>','<?php echo $group; ?> ')" class='css_button_small'><span><?php echo  htmlspecialchars( xl('View'), ENT_NOQUOTES); ?></span></a>
                                                                               <?php }else { ?>
                                                                                     <input type='checkbox' name='finalize_<?php echo $enc; ?>' id='finalize_<?php echo $enc; ?>' onclick="postValue('<?php echo $enc; ?>','<?php echo $pid; ?>','<?php echo $date; ?> ')"   disabled/>Finalize | <?php 
                                                                                    echo "<a  class='css_button_small'><span>".
                                                                                    htmlspecialchars( xl('View'), ENT_NOQUOTES)."</span></a>";

                                                                                }
                                                                       }
                                                                        echo "</td>";
                                                                        echo "<td>".$result1['Patient_Name']."</td>";
                                                                        echo "<td>".$result1['Gender']."</td>";
                                                                        echo "<td>".$result1['Encounter']."</td>";
                                                                        echo "<td>"; if($result1['provider_id']!='') $pro_id=sqlStatement("select CONCAT(fname,' ',lname) AS provider from users where id='".$result1['provider_id']."' ");
                                                                                    $id_row4=sqlFetchArray($pro_id);
                                                                                    $proid=$id_row4['provider'];{ echo $proid; }echo"</td>";
                                                                        echo "<td>";if($result1['facility_id']!='')  $pro_id2=sqlStatement("select name from facility where id='".$result1['facility_id']."' ");
                                                                                    $id_row5=sqlFetchArray($pro_id2);
                                                                                    $proid23=$id_row5['name'];{ echo $proid23; }echo"</td>";
                                                                        echo "<td>"; if($result1['pc_catid']!='') $pro_id1=sqlStatement("select pc_catname from openemr_postcalendar_categories where pc_catid='".$result1['pc_catid']."' ");
                                                                                    $id_row41=sqlFetchArray($pro_id1);
                                                                                    $proid1=$id_row41['pc_catname'];{ echo $proid1; }echo"</td>";
                                                                        echo "<td>"; echo $result1['Required_Forms_Complete'];   echo"</td>";
                                                                        echo "<td>"; echo $result1['Required_Forms_Incomplete'];   echo"</td>";
                                                                        echo "<td>"; echo $result1['Optional_Forms'];   echo"</td>";
                                                                        echo "<td>"; echo $result1['Audit_Status'];   echo"</td>"; 
                                                                 echo "</tr>\n";
                                                             }
                                                       }
                                                 }
                                             }
                                     }   
                            ?>
                            </table>
                        </div> <!-- end main content div -->
                    </div> <!-- end report_parameters --> 
                         </div>
		    </div>
                 <br><br>
                </div>
		</section>
          
                <section id="footer">
			<div class="container">
				<div class="row">
					<div class="col-md-6 col-md-offset-3">
						<ul class="text-center contact">
				        	<li class= "socials-icons">
								<a href="#" data-toggle="tooltip" title="Share in Facebook" class="facebook"><i class="fa fa-facebook"></i></a>
							</li>
							<li class= "socials-icons">
								<a href="#" data-toggle="tooltip" title="Share in Twitter" class="twitter"><i class="fa fa-twitter"></i></a>
							</li>
							<li class= "socials-icons">
								<a href="#" data-toggle="tooltip" title="Share in Google +" class="google-plus"><i class="fa fa-google-plus"></i></a>
							</li>
							<li class= "socials-icons">
								<a href="#" data-toggle="tooltip" title="Share in Instagram" class="instagram"><i class="fa fa-instagram"></i></a>
							</li>
							<li class= "socials-icons">
								<a href="#" data-toggle="tooltip" title="Share in Pinterest" class="pinterest"><i class="fa fa-pinterest"></i></a>
							</li>
							<li class= "socials-icons">
								<a href="#" data-toggle="tooltip" title="Connect with Skype" class="skype"><i class="fa fa-skype"></i></a>
							</li>
				      	</ul>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="copy-right-text text-center">
							&copy; Copyright 2014, Your Website Link. Theme by <a href="https://themewagon.com/">ThemeWagon</a>
						</div>
					</div>
				</div>
			</div>
		</section>

		<script type="text/javascript" src="assets/js/jquery.min.js"></script>
		<script type="text/javascript" src="assets/js/owl.carousel.min.js"></script>
		<script type="text/javascript" src="assets/js/isotope.pkgd.min.js"></script>
		<script type="text/javascript" src="assets/js/wow.min.js"></script>
		<script type="text/javascript" src="assets/js/bootstrap.min.js"></script>

		<script>
      		new WOW().init();
		</script>

		<script type='text/javascript'>
                 
			$(document).ready(function() {
                            jQuery.noConflict();
                           jQuery("#starting-slider").owlCarousel({
  					autoPlay: 3000,
      				navigation : false, // Show next and prev buttons
      				slideSpeed : 700,
      				paginationSpeed : 1000,
      				singleItem:true
  				});
   
                             //datatable
                             var responsiveHelper;
                            var breakpointDefinition = {
                                tablet: 1024,
                                phone : 480
                            };
                            var tableElement = $('#vnfFilter1');
                            tableElement.dataTable({
                                autoWidth        : false,
                                preDrawCallback: function () {
                                    // Initialize the responsive datatables helper once.
                                    if (!responsiveHelper) {
                                        responsiveHelper = new ResponsiveDatatablesHelper(tableElement, breakpointDefinition);
                                    }
                                },
                                rowCallback    : function (nRow) {
                                    responsiveHelper.createExpandIcon(nRow);
                                },
                                drawCallback   : function (oSettings) {
                                    responsiveHelper.respond();
                                }
                            });
                             
                           
                            
			});
                        
		</script>


		<script>
			jQuery( function() {
				  // init Isotope
			  	var $container = jQuery('.isotope').isotope
			  	({
				    itemSelector: '.element-item',
				    layoutMode: 'fitRows'
			  	});


  				// bind filter button click
  				jQuery('#filters').on( 'click', 'button', function() 
  				{
				    var filterValue = $( this ).attr('data-filter');
				    // use filterFn if matches value
				    $container.isotope({ filter: filterValue });
				 });
  
			  // change is-checked class on buttons
			  	jQuery('.button-group').each( function( i, buttonGroup ) 
			  	{
			    	var $buttonGroup = $( buttonGroup );
			    	$buttonGroup.on( 'click', 'button', function() 
			    	{
			      		$buttonGroup.find('.is-checked').removeClass('is-checked');
			      		jQuery( this ).addClass('is-checked');
			    	});
			  	});
			  
			});
		</script>
<!--                <script src="http://code.jquery.com/jquery-1.11.3.min.js"></script> -->
                <script>
                (function($){
                  var ico = $('<i class="fa fa-caret-right"></i>');
                  $('nav#menu li:has(ul) > a').append(ico);

                  $('nav#menu li:has(ul)').on('click',function(){
                    $(this).toggleClass('open');
                  });

                  $('a#toggle').on('click',function(e){
                    $('html').toggleClass('open-menu');
                    return false;
                  });


                  $('div#overlay').on('click',function(){
                    $('html').removeClass('open-menu');
                  })

                })(jQuery)
                </script>
                
	</body>
        <style type="text/css">
    @import url(../library/dynarch_calendar.css);
</style>
<script type="text/javascript" src="../library/dynarch_calendar.js"></script>
<?php include_once("{$GLOBALS['srcdir']}/dynarch_calendar_en.inc.php"); ?>
<script type="text/javascript"
	src="../library/dynarch_calendar_setup.js"></script>
<script type="text/javascript">
 Calendar.setup({inputField:"form_from_date", ifFormat:"%Y-%m-%d", button:"img_from_date"});
 Calendar.setup({inputField:"form_to_date", ifFormat:"%Y-%m-%d", button:"img_to_date"});
</script>
        <script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-36251023-1']);
  _gaq.push(['_setDomainName', 'jqueryscript.net']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
</html>