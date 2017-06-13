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

require_once("../globals.php");
require_once("../../library/formdata.inc.php");
require_once("../../library/globals.inc.php");

$provider  = $_POST['form_provider'];
$patient=$_POST['form_patient'];
//print_r($patient);
$audit_stat=$_POST['form_audit_stat'];
$enc_stat=$_POST['form_enc_stat'];
$from=$_POST['form_from_date'];
$to=$_POST['form_to_date'];
 if (! $_POST['form_from_date']) {
	// If a specific patient, default to 2 years ago.
	$tmp = date('Y');
	$from = date("$tmp-m-d");
        $to = date("$tmp-m-d");
}

$facility  = $_POST['form_facility1'];
$visit_cat=$_POST['pc_catid'];
//function for facility
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
                                        ), form_encounter.facility, form_encounter.pid,form_encounter.facility_id, form_encounter.encounter, form_encounter.pc_catid AS visitcategory_id, DATE_FORMAT( form_encounter.date,  '%Y-%m-%d' ) AS dos, patient_data.providerID AS provider_id,f.screen_names
                                        FROM form_encounter
                                        INNER JOIN patient_data ON patient_data.pid = form_encounter.pid
                                        INNER JOIN tbl_allcare_facuservisit f ON  `facilities` REGEXP (
                                        form_encounter.facility_id
                                        )
                                        AND  `users` REGEXP (
                                        patient_data.providerID
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
                                                   $fname.="<a href='allcare_enc_forms.php?groupname=$gname&form_id1=".$resid1."&enc3=".$resenc1."&pid1=".$respid1."&inmode1=edit&file_name1=Incomplete_charts&fname1=".$resfname1."
                                                          'onclick='top.restoreSession()' ><span>".
                                                            htmlspecialchars( xl($title1), ENT_NOQUOTES)."</span></a>,";
        //                                         $groupname='"Chief Complaint"';
        //                                         $form_name='"Allcare Encounter Forms"';
        //                                         $fname.= "<a href='javascript:win1($groupname,$resid1,$resenc1,$respid1,$form_name);' onmouseover='self.status='Open A Window'; return true;'>Chief Complaint</a>".",";
                                               }else {
                                                   $fname_comp.="<a href='allcare_enc_forms.php?groupname=$gname&form_id1=".$resid1."&enc3=".$resenc1."&pid1=".$respid1."&inmode1=edit&file_name1=Incomplete_charts&fname1=".$resfname1."
                                                          'onclick='top.restoreSession()' ><span>".
                                                            htmlspecialchars( xl($title1), ENT_NOQUOTES)."</span></a>,";
                                               }
                                           } else if($priority=='Optional'){
                                               
                                                $ra = sqlStatement("select count(*) as cnt from lbf_data  where field_id LIKE  '%$field_id1%' AND form_id='".$resid1."'");
                                                 while($frowa = sqlFetchArray($ra)){
                                                    $a = $frowa['cnt'];
                                                 }
                                                 if($a==0){
                                                   $fname_opt.="<a href='allcare_enc_forms.php?groupname=$gname&form_id1=".$resid1."&enc3=".$resenc1."&pid1=".$respid1."&inmode1=edit&file_name1=Incomplete_charts&fname1=".$resfname1."
                                                          'onclick='top.restoreSession()' ><span>".
                                                            htmlspecialchars( xl($title1), ENT_NOQUOTES)."</span></a>,";
        //                                         $groupname='"Chief Complaint"';
        //                                         $form_name='"Allcare Encounter Forms"';
        //                                         $fname.= "<a href='javascript:win1($groupname,$resid1,$resenc1,$respid1,$form_name);' onmouseover='self.status='Open A Window'; return true;'>Chief Complaint</a>".",";
                                               }else {
                                                   $fname_opt.="<a href='allcare_enc_forms.php?groupname=$gname&form_id1=".$resid1."&enc3=".$resenc1."&pid1=".$respid1."&inmode1=edit&file_name1=Incomplete_charts&fname1=".$resfname1."
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
                                                 $fname.="<a href='allcare_enc_forms.php?groupname=$gname&enc3=".$resenc1."&pid1=".$respid1."&inmode1=edit&file_name1=Incomplete_charts&fname1=".$resfname1."&form_id1=''
                                                          'onclick='top.restoreSession()' ><span>".
                                                            htmlspecialchars( xl($title1), ENT_NOQUOTES)."</span></a>,";
                                            } else if($priority=='Optional'){
                                                 $fname_opt.="<a href='allcare_enc_forms.php?groupname=$gname&enc3=".$resenc1."&pid1=".$respid1."&inmode1=edit&file_name1=Incomplete_charts&fname1=".$resfname1."&form_id1=''
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
                                                 $fname.="<a href='/interface/forms/allcare_ros/new_custom.php?formname=allcare_ros&encounter=$resenc1&pid=$respid1'
                                                               ><span>".
                                                                htmlspecialchars( xl('Allcare Ros'), ENT_NOQUOTES)."</span></a>,";
                                                } else {
                                                     $fname_comp.="<a href='/interface/forms/allcare_ros/view_custom.php?formname=allcare_ros&encounter=$resenc1&pid=$respid1&id=$fid1'
                                                               ><span>".
                                                                htmlspecialchars( xl('Allcare Ros'), ENT_NOQUOTES)."</span></a>,";
                                                }
                                             }else if($priority=='Optional'){
                                                 if($ros=='NO'){
                                                 //$fname='<a href="/interface/reports/form_load.php?formname=allcare_ros&edit=custom">Allcare_ros</a>'.",";
                                                 $fname_opt.="<a href='/interface/forms/allcare_ros/new_custom.php?formname=allcare_ros&encounter=$resenc1&pid=$respid1'
                                                               ><span>".
                                                                htmlspecialchars( xl('Allcare Ros'), ENT_NOQUOTES)."</span></a>,";
                                                } else {
                                                     $fname_opt.="<a href='/interface/forms/allcare_ros/view_custom.php?formname=allcare_ros&encounter=$resenc1&pid=$respid1&id=$fid1'
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
                                                     $fname.="<a href='/interface/forms/allcare_physical_exam/new_custom.php?formname=allcare_physical_exam&edit=custom_pe&encounter=$resenc1&pid=$respid1'
                                                                   ><span>".
                                                                    htmlspecialchars( xl('Allcare Physical_exam'), ENT_NOQUOTES)."</span></a>,";
                                                      //$fname.="<a href='/interface/reports/form_load.php?formname=allcare_physical_exam&encounter=$resenc1&pid=$respid1'>Allcare Physical_exam</a>".",";
                //                                     $fname.= "<a href='javascript:win1('/interface/patient_file/encounter/load_form.php?formname=allcare_physical_exam&edit=custom_pe');' onmouseover='self.status='Open A Window'; return true;'>Chief Complaint</a>".",";
                                                }else {
                                                    $fname_comp.="<a href='/interface/forms/allcare_physical_exam/new_custom.php?formname=allcare_physical_exam&edit=custom_pe&encounter=$resenc1&pid=$respid1&id=$fid'
                                                                   ><span>".
                                                                    htmlspecialchars( xl('Allcare Physical_exam'), ENT_NOQUOTES)."</span></a>,";
                                                }
                                            }else if($priority=='Optional'){
                                                 if($pe=='NO'){
                                                     $fname_opt.="<a href='/interface/forms/allcare_physical_exam/new_custom.php?formname=allcare_physical_exam&edit=custom_pe&encounter=$resenc1&pid=$respid1'
                                                                   ><span>".
                                                                    htmlspecialchars( xl('Allcare Physical_exam'), ENT_NOQUOTES)."</span></a>,";
                                                      //$fname.="<a href='/interface/reports/form_load.php?formname=allcare_physical_exam&encounter=$resenc1&pid=$respid1'>Allcare Physical_exam</a>".",";
                //                                     $fname.= "<a href='javascript:win1('/interface/patient_file/encounter/load_form.php?formname=allcare_physical_exam&edit=custom_pe');' onmouseover='self.status='Open A Window'; return true;'>Chief Complaint</a>".",";
                                                }else {
                                                    $fname_opt.="<a href='/interface/forms/allcare_physical_exam/new_custom.php?formname=allcare_physical_exam&edit=custom_pe&encounter=$resenc1&pid=$respid1&id=$fid'
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
                                                     $fname.="<a href='/interface/forms/vitals/new_custom.php?formname=Vitals&encounter=$resenc1&pid=$respid1'
                                                                   ><span>".
                                                                    htmlspecialchars( xl('Vitals'), ENT_NOQUOTES)."</span></a>,";
                                                      //$fname.="<a href='/interface/reports/form_load.php?formname=allcare_physical_exam&encounter=$resenc1&pid=$respid1'>Allcare Physical_exam</a>".",";
                //                                     $fname.= "<a href='javascript:win1('/interface/patient_file/encounter/load_form.php?formname=allcare_physical_exam&edit=custom_pe');' onmouseover='self.status='Open A Window'; return true;'>Chief Complaint</a>".",";
                                                }else {
                                                    $fname_comp.="<a href='/interface/forms/vitals/view_custom.php?formname=Vitals&encounter=$resenc1&pid=$respid1&id=$fid1'
                                                                   ><span>".
                                                                    htmlspecialchars( xl('Vitals'), ENT_NOQUOTES)."</span></a>,";
                                                }
                                            }else if($priority=='Optional'){
                                                 if($fid1==''){
                                                     $fname_opt.="<a href='/interface/forms/vitals/new_custom.php?formname=Vitals&encounter=$resenc1&pid=$respid1'
                                                                   ><span>".
                                                                    htmlspecialchars( xl('Vitals'), ENT_NOQUOTES)."</span></a>,";
                                                      //$fname.="<a href='/interface/reports/form_load.php?formname=allcare_physical_exam&encounter=$resenc1&pid=$respid1'>Allcare Physical_exam</a>".",";
                //                                     $fname.= "<a href='javascript:win1('/interface/patient_file/encounter/load_form.php?formname=allcare_physical_exam&edit=custom_pe');' onmouseover='self.status='Open A Window'; return true;'>Chief Complaint</a>".",";
                                                }else {
                                                    $fname_opt.="<a href='/interface/forms/vitals/view_custom.php?formname=Vitals&encounter=$resenc1&pid=$respid1&id=$fid1'
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
                                                    
                                                     $fname_comp.="<a href='../patient_file/summary/stats_full_custom.php?active=all&category=$type&encounter=$resenc1&pid=$respid1&id=$fid1'
                                                                   ><span>".
                                                                    htmlspecialchars( xl($title), ENT_NOQUOTES)."</span></a>,";
                                                      //$fname.="<a href='/interface/reports/form_load.php?formname=allcare_physical_exam&encounter=$resenc1&pid=$respid1'>Allcare Physical_exam</a>".",";
                //                                     $fname.= "<a href='javascript:win1('/interface/patient_file/encounter/load_form.php?formname=allcare_physical_exam&edit=custom_pe');' onmouseover='self.status='Open A Window'; return true;'>Chief Complaint</a>".",";
                                                }else {
                                                   
                                                    $fname.="<a href='../patient_file/summary/stats_full_custom.php?active=all&category=$type&encounter=$resenc1&pid=$respid1'
                                                                   ><span>".
                                                                    htmlspecialchars( xl($title), ENT_NOQUOTES)."</span></a>,";
                                                }
                                            }else if($priority=='Optional'){
                                                 if(!empty($frow_med)){
                                                    
                                                     $fname_opt.="<a href='../patient_file/summary/stats_full_custom.php?active=all&category=$type&encounter=$resenc1&pid=$respid1&id=$fid1'
                                                                   ><span>".
                                                                    htmlspecialchars( xl($title), ENT_NOQUOTES)."</span></a>,";
                                                      //$fname.="<a href='/interface/reports/form_load.php?formname=allcare_physical_exam&encounter=$resenc1&pid=$respid1'>Allcare Physical_exam</a>".",";
                //                                     $fname.= "<a href='javascript:win1('/interface/patient_file/encounter/load_form.php?formname=allcare_physical_exam&edit=custom_pe');' onmouseover='self.status='Open A Window'; return true;'>Chief Complaint</a>".",";
                                                }else {
                                                   
                                                    $fname_opt.="<a href='../patient_file/summary/stats_full_custom.php?active=all&category=$type&encounter=$resenc1&pid=$respid1'
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
                                                     $fname_comp.="<a href='/interface/patient_file/summary/immunizations_custom.php?pid=$respid1'
                                                                   ><span>".
                                                                    htmlspecialchars( xl('Immunizations'), ENT_NOQUOTES)."</span></a>,";
                                                 }else {
                                                    $fname.="<a href='/interface/patient_file/summary/immunizations_custom.php?pid=$respid1'
                                                                   ><span>".
                                                                    htmlspecialchars( xl('Immunizations'), ENT_NOQUOTES)."</span></a>,";
                                                }
                                            }else if($priority=='Optional'){
                                                 if(!empty($frow_im)){
                                                     $fname_opt.="<a href='/interface/patient_file/summary/immunizations_custom.php?pid=$respid1'
                                                                   ><span>".
                                                                    htmlspecialchars( xl('Immunizations'), ENT_NOQUOTES)."</span></a>,";
                                                }else {
                                                    $fname_opt.="<a href='/interface/patient_file/summary/immunizations_custom.php?pid=$respid1'
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
                                                     $fname_comp.="<a href='../patient_file/history/history_custom.php?pid=$respid1'
                                                                   ><span>".
                                                                    htmlspecialchars( xl('History'), ENT_NOQUOTES)."</span></a>,";
                                                 }else {
                                                    $fname.="<a href='../patient_file/history/history_custom.php?pid=$respid1'
                                                                   ><span>".
                                                                    htmlspecialchars( xl('History'), ENT_NOQUOTES)."</span></a>,";
                                                }
                                            }else if($priority=='Optional'){
                                                 if(!empty($frow_his)){
                                                     $fname_opt.="<a href='../patient_file/history/history_custom.php?pid=$respid1'
                                                                   ><span>".
                                                                    htmlspecialchars( xl('History'), ENT_NOQUOTES)."</span></a>,";
                                                }else {
                                                    $fname_opt.="<a href='../patient_file/history/history_custom.php?pid=$respid1'
                                                                   ><span>".
                                                                    htmlspecialchars( xl('History'), ENT_NOQUOTES)."</span></a>,";
                                                }
                                            }
                                     }
                                     $i++;
                                  }
                                }
                            }        $fname1=rtrim($fname,",");
                                    $fname_comp1=rtrim($fname_comp,",");
                                    $fname_opt1=rtrim($fname_opt,",");
                                    $query="select CONCAT(p.fname,' ',p.lname) AS pname,p.sex,DATE_FORMAT(f.date, '%Y-%m-%d') as date1,f.encounter,f.elec_signedby,f.elec_signed_on,f.pid,e.pc_facility,e.pc_aid,e.pc_catid,f.pc_catid,f.facility_id,f.provider_id from form_encounter f  INNER JOIN patient_data p ON p.pid=f.pid LEFT JOIN openemr_postcalendar_events e ON e.pc_pid=f.pid and e.pc_eventDate=DATE_FORMAT(f.date, '%Y-%m-%d') AND e.pc_catid=f.pc_catid  where  f.encounter=$resenc1 AND f.pid=$respid1 ";
                                    //echo $query;
                                    $sql = sqlStatement($query);
                                    $frow3 = sqlFetchArray($sql);
                                    if(!empty($frow3)){
                                        if($frow3['elec_signedby']=='' && $frow3['elec_signed_on']==''){
                                            $frow3['finalized_val']='not_finalized';
                                        } else {
                                            $frow3['finalized_val']='finalized';
                                        }
                                        $frow3['required_complete']=$fname_comp1;
                                        $frow3['required_incomplete']=$fname1;
                                        $frow3['optional']=$fname_opt1;
                                           //echo '<pre>';print_r($frow3); echo '</pre>';
                                        $sql1=sqlStatement("select * from forms where form_name='Audit Form' AND deleted=0 AND encounter='$resenc1'");
                                        $audit_res=sqlFetchArray($sql1);
                                             if(empty($audit_res)){
                                                $frow3['audit_status']='Incomplete';
                                                //array_push($frow3,'Incomplete');
                                             } else {
                                                  $sql2=sqlStatement("select * from tbl_form_audit where id='".$audit_res['form_id']."'");
                                                  $audit_st = sqlFetchArray($sql2);
                                                  if(!empty($audit_st)){
                                                      $sql = sqlStatement("select CONCAT(p.fname,' ',p.lname) AS pname,p.sex,f.encounter ,fe.audited_status from forms f INNER JOIN patient_data p ON p.pid=f.pid  INNER JOIN  form_encounter fe ON fe.encounter=f.encounter where form_name='Audit Form' AND deleted=0 AND f.encounter='".$resenc1."'");
                                                      $frow2 = sqlFetchArray($sql);
                                                      if(!empty($frow2) && $frow2['audited_status']=='Completed'){
                                                        $frow3['audit_status']='Complete';
                                                        // array_push($frow3,'Complete');
                                                      }else {

                                                         $frow3['audit_status']='Incomplete';
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
<html>
    <meta http-equiv="cache-control" content="max-age=0" />
    <meta http-equiv="cache-control" content="no-cache" />
    <meta http-equiv="expires" content="0" />
    <meta http-equiv="expires" content="Tue, 01 Jan 1980 1:00:00 GMT" />
    <meta http-equiv="pragma" content="no-cache" />
    <head>
           <style>
        .css_button_small {
        -moz-font-feature-settings: normal;
        -moz-font-language-override: normal;
        -moz-text-decoration-color: -moz-use-text-color;
        -moz-text-decoration-line: none;
        -moz-text-decoration-style: solid;
        -x-system-font: none;
        background-attachment: scroll;
        background-clip: border-box;
        background-color: transparent;
        background-image: url("../../../images/bg_button_a_small.gif");
        background-origin: padding-box;
        background-position: right top;
        background-repeat: no-repeat;
        background-size: auto auto;
        color: #444;
        display: block;
        float: left;
        font-family: arial,sans-serif;
        font-size: 9px;
        font-size-adjust: none;
        font-stretch: normal;
        font-style: normal;
        font-variant: normal;
        font-weight: bold;
        height: 19px;
        line-height: normal;
        margin-right: 3px;
        padding-right: 10px;
        }

        .css_button_small span {
        background-attachment: scroll;
        background-clip: border-box;
        background-color: transparent;
        background-image: url("../../../images/bg_button_span_small.gif");
        background-origin: padding-box;
        background-position: 0 0;
        background-repeat: no-repeat;
        background-size: auto auto;
        display: block;
        line-height: 20px;
        padding-bottom: 0;
        padding-left: 10px;
        padding-right: 0;
        padding-top: 0;
        }
        .link1 {
         font-family: sans-serif;
         text-decoration: none;
         color: #0000cc;
         padding-left:30px;
         //font-size: 70%;
        }
        #dvLoading1
    {
        background: url(../pic/ajax-loader-large.gif) no-repeat center center;
        height: 100px;
        width: 500px;
        position: fixed;
        z-index: 1000;
        left: 0%;
        top: 50%;
        margin: -25px 0 0 -25px;
    }
    </style>
<style type="text/css">@import url(../../library/dynarch_calendar.css);</style>
<link rel=stylesheet href="<?php echo $css_header;?>" type="text/css">
<link rel='stylesheet' type='text/css' href='../main/css/jquery.dataTables.css'>
<link rel='stylesheet' type='text/css' href='../main/css/dataTables.tableTools.css'>
<link rel='stylesheet' type='text/css' href='../main/css/dataTables.colVis.css'>
<link rel='stylesheet' type='text/css' href='../main/css/dataTables.colReorder.css'>
<!--<style>
div.DTTT_container {
	float: none;

</style>-->
<script type='text/javascript' src='../main/js/jquery-1.11.1.min.js'></script>
<script type='text/javascript' src='../main/js/jquery.dataTables.min.js'></script>
<script type='text/javascript' src='../main/js/dataTables.tableTools.js'></script>
<script type='text/javascript' src='../main/js/dataTables.colReorder.js'></script>
<script type='text/javascript' src='../main/js/dataTables.colVis.js'></script>
</head>
 <body class="body_top" style="background-color:#FFFFCC;" >
      <script type='text/javascript'>
             $(document).ready( function () {
//                    $('#div_noform').show(); 
//                    $('#dvLoading1').show();
//                    $("#div_noform").load("vnencforms_reports.php" ,function(){ 
//                    $('#dvLoading1').hide();
//                  });
                   
                    $('#dvLoading1').show();
                        $('#vnfFilter1').DataTable( {   "iDisplayLength": 100
                        } );
                    $('#dvLoading1').hide();
                    
            });
            function submitme() {
               
                 var f = document.forms['dropdown_filters'];
                 f.submit();
                
            }
            
            
 function previewpost1(enc,pid,date1){
      var datefield = date1;
      <?php $mobile_sql=sqlStatement("SELECT * 
                FROM  `tbl_chartui_mapping` 
                WHERE form_id =  'CHARTOUTPUT'
                AND group_name LIKE  '%Mobile%'
                AND screen_name LIKE  '%Mobile%'");
      while($mob_row1=sqlFetchArray($mobile_sql)){
          //$mob_val[$mob_row1['field_id']]=$mob_row1['option_value']; 
          $field_id='form_'.$mob_row1['field_id'];
          $field_value=$mob_row1['option_value']; 
          $res.=$field_id.'='.$field_value.'&'; ?>
      
    <?php  }
    
?>
    //alert(datefield);    
   // alert('<?php echo $res; ?>');   
 
  var datastring='<?php echo $res; ?>'+'patientid'+'='+pid+'&'+'encounter_id'+'='+enc+'&'+'dos'+'='+date1+'&'+'chartgroupshidden'+'='+'1Mobile';
   //alert(datastring);
        //top.restoreSession();
      // location.href = '../patient_file/summary/preview_chartoutput.php?'+datastring;
       //location.href = 'preview_charts.php?'+datastring;
       window.open('preview_charts.php?'+datastring,'popup','width=900,height=900,scrollbars=no,resizable=yes');
}

function viewpost1(id,pid1,group){
   var datastring='coid'+'='+id+'&'+'patient_id'+'='+pid1+'&'+'group'+'='+group;
  
       window.open('print_charts.php?'+datastring,'popup','width=900,height=900,scrollbars=no,resizable=yes');
}

//            function win1(gname,form_id,encounter,pid,form_name) {
//                  var gname1=gname; 
//                  var form_id1=form_id;
//                  var encounter1=encounter;
//                  var pid1=pid;
//                  var form_name1=form_name;
//                 // alert("allcare_enc_forms.php?groupname="+gname1+"&form_id1="+form_id1+"&enc3='"+encounter1+"'&pid1="+pid1+"&inmode1=edit&file_name1=Incomplete_charts&fname1="+form_name1+"");
//                  window.open("allcare_enc_forms.php?groupname="+gname1+"&form_id1="+form_id1+"&enc3="+encounter1+"&pid1="+pid1+"&inmode1=edit&file_name1=Incomplete_charts&fname1="+form_name1+"", "Window2", "width=600,height=600,scrollbars=yes");
//                }

function postValue(enc1,pid1,date2){  
   // alert(enc1+"==="+pid1+"==="+date2); 
    //alert(document.getElementById('finalize_'+enc1).checked);
    if(document.getElementById('finalize_'+enc1).checked){ 
     var finalize_val=document.getElementById('finalize_'+enc1).checked;  //alert(finalize_val); 
       $.ajax({
		type: 'POST',
		url: "finalize.php",	
                data:{enc1:enc1,pid1:pid1,date2:date2},
		success: function(response)
		{
                   //alert(response);
                   location.reload();
                   
		},
		failure: function(response)
		{
                    alert("error");
		}		
	});	
       
    }  
    else {
      //var finalize_val=document.getElementById('finalize').checked; alert(finalize_val); 
    }
  }
 
//            function win1(gname,form_id,encounter,pid,form_name) {
//                  var gname1=gname; 
//                  var form_id1=form_id;
//                  var encounter1=encounter;
//                  var pid1=pid;
//                  var form_name1=form_name;
//                 // alert("allcare_enc_forms.php?groupname="+gname1+"&form_id1="+form_id1+"&enc3='"+encounter1+"'&pid1="+pid1+"&inmode1=edit&file_name1=Incomplete_charts&fname1="+form_name1+"");
//                  window.open("allcare_enc_forms.php?groupname="+gname1+"&form_id1="+form_id1+"&enc3="+encounter1+"&pid1="+pid1+"&inmode1=edit&file_name1=Incomplete_charts&fname1="+form_name1+"", "Window2", "width=600,height=600,scrollbars=yes");
//                }


    </script>
<!-- Required for the popup date selectors -->
<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>
<span class='title'><?php xl('Reports','e'); ?></span>
<!--<a href="javascript:;" class="link1" onclick="window.open( 'http://<?php echo $_SERVER[HTTP_HOST]; ?>/interface/reports/details.php', '', 'width=500, height=600')"> [Details] </a>-->
<ul class="tabNav">

<li>
<a id="tab_vnforms" onclick="javascript:
                       $('#theform_vnform').show();
                       $('.tabNav > li').removeClass('current');
                       $(this).parent('li').addClass('current');" style='cursor:pointer'>Incomplete Encounters</a>
</li>
</ul>
<?php
 if($_GET['showForm']=='snf')
 {
     echo "<script>
$('.tabNav > li').removeClass('current');
$('#tab_snf').parent('li').addClass('current');
</script>";
 }
?>
<!--Visits with No forms-->
<div id='theform_vnform'>
<!--<form  method='post' name='theform_vnform' id='theform_vnform' action=''>-->
       <?php if(!isset($_GET['showForm']) || ($_GET['showForm'])=='fnp' || ($_GET['showForm'])=='vnf2f') 
              {//echo "style='display:none;'";
           } ?>
 
             
    
<!----------- Submitted but not finalized Starts -------------->   
          
    <div id="div_visits_with_forms">
   
            <div id="vnform_report_parameters">
               <br>  <br>  <br>
                <form name="dropdown_filters" id="dropdown_filters" action="incomplete_charts.php" method="POST" >
                    <table>
                        <tr>
                            <td><?php xl('Provider','e'); ?>:</td>
                            <td>
                              <?php

                                    // Build a drop-down list of providers.
                                    //

                                    $query = "SELECT id, lname, fname FROM users WHERE ".
                                      "authorized = 1  ORDER BY lname, fname"; //(CHEMED) facility filter

                                    $ures = sqlStatement($query);

                                    echo "   <select name='form_provider[]'  multiple  id='form_provider'>\n";
                                    echo "    <option value=''"; if(!empty($provider)){ foreach($provider as $val1) { if ($val1=='') echo " selected";} }
                                    echo  "selected"; echo" >-- " . xl('All') . " --\n";

                                    while ($urow = sqlFetchArray($ures)) {
                                            $provid = $urow['id'];
                                            echo "    <option value='$provid'";
                                            if(!empty($provider)){
                                            foreach($provider as $val1){    
                                            if ($provid == $val1) echo " selected";} }
                                            echo ">" . $urow['lname'] . ", " . $urow['fname'] . "\n";
                                    }

                                    echo "   </select>\n";

                                    ?>
                            </td>
                            <td><?php xl('Patient','e'); ?>:</td>
                            <td>
                              <?php
                                     // Build a drop-down list of providers.
                                    //
                                   $query = "SELECT pid, lname, fname FROM patient_data ".
                                      " ORDER BY lname, fname"; //(CHEMED) facility filter

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
                            </td>
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

                           <td class='label'><?php xl('From','e'); ?>:</td>
                            <td><input type='text' name='form_from_date' id="form_from_date"
                                    size='10' value='<?php echo $from ?>'
                                    onkeyup='datekeyup(this,mypcc)' onblur='dateblur(this,mypcc)'
                                    title='yyyy-mm-dd'> <img src='../pic/show_calendar.gif'
                                    align='absbottom' width='24' height='22' id='img_from_date'
                                    border='0' alt='[?]' style='cursor: pointer'
                                    title='<?php xl('Click here to choose a date','e'); ?>'></td>
                            <td class='label'><?php xl('To','e'); ?>:</td>
                            <td><input type='text' name='form_to_date' id="form_to_date"
                                    size='10' value='<?php echo $to ?>'
                                    onkeyup='datekeyup(this,mypcc)' onblur='dateblur(this,mypcc)'
                                    title='yyyy-mm-dd'> <img src='../pic/show_calendar.gif'
                                    align='absbottom' width='24' height='22' id='img_to_date'
                                    border='0' alt='[?]' style='cursor: pointer'
                                    title='<?php xl('Click here to choose a date','e'); ?>'></td><td>&nbsp;</td> 

                            <td> <a href="javascript:;"  class="css_button" onclick="submitme();">
                                    <span><?php echo htmlspecialchars( xl('Submit'), ENT_NOQUOTES); ?></span>
                                 </a>
                            </td>

                            </tr>
                            <tr>
                            <td class='label'><?php xl('Facility','e'); ?>:</td>

                            <td><?php if(!empty($facility)){$facility1=implode("|",$facility); }  facility_list1(strip_escape_custom($facility1), 'form_facility1[]' ,'form_facility1',true); ?></td>
                            <td class='bold' nowrap><?php echo xlt('Visit Category:'); ?></td>
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
                              <br><br>
                 </form> 
                <!--<div id="div_noform"><div id="dvLoading1" style="display:none"></div></div>--> 
            <div  style='margin-top:10px'> <!-- start main content div -->
                   <div id="dvLoading1" style="display:none"></div>
                         <div id="div_noform">
                            <table class='display'  id='vnfFilter1' border="1">

                            <?php
                             print "<thead><tr class='showborder_head'><th style='width:800px;'>&nbsp;</th>"
                                     . "<th style='width:180px;'>".htmlspecialchars( xl('Patient Name'), ENT_NOQUOTES)."</th>"."<th style='width:180px;'>".htmlspecialchars( xl('Gender'), ENT_NOQUOTES)."</th>"."<th style='width:180px;'>".htmlspecialchars( xl('Encounter'), ENT_NOQUOTES)."</th>"."<th style='width:180px;'>".htmlspecialchars( xl('Provider'), ENT_NOQUOTES)."</th>"."<th style='width:180px;'>".htmlspecialchars( xl('Facility'), ENT_NOQUOTES)."</th>".
                                     "<th style='width:180px;'>".htmlspecialchars( xl('Visit Category'), ENT_NOQUOTES)."</th>"."<th style='width:180px;'>".htmlspecialchars( xl('Required forms complete'), ENT_NOQUOTES)."</th>".
                                              "<th style='width:180px;'>".htmlspecialchars( xl('Required forms incomplete'), ENT_NOQUOTES)."</th>"."<th style='width:180px;'>".htmlspecialchars( xl('Optional forms'), ENT_NOQUOTES)."</th>"."<th style='width:180px;'>".htmlspecialchars( xl('Audit Status'), ENT_NOQUOTES)."</th>";
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
                                                p.providerID
                                                )
                                                AND  `visit_categories` REGEXP (
                                                fe.pc_catid
                                                )
                                                INNER JOIN layout_options l ON l.group_name = f.screen_group
                                                AND l.form_id = f.form_id
                                                WHERE fe.pc_catid
                                                IN ( 15, 16, 17, 18, 19, 20, 24, 25, 29, 44 ) ";
                                      
                                      if(!empty($provider)){
                                        foreach($provider as $value){
                                            if($value!=''){
                                             $pro_val.=$value.",";
                                            }
                                        }
                                        $pro_val1=trim($pro_val,',');
                                        if($pro_val1!=''){
                                          $resid21.=  " AND fe.provider_id IN ($pro_val1)";
                                        }
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
                                                              //  echo "<pre>";print_r($result1); echo "</pre>";
                                                                echo "<tr height='25'>";
                                                                echo "<td style='width:600px;'>";
                                                                    if($result1['finalized_val']=='not_finalized'){
                                                                        $enc=$result1['encounter'];
                                                                        $pid=$result1['pid'];
                                                                        $date=$result1['date1'];
                                                                        ?>  <a href='javascript:; ' onclick="previewpost1('<?php echo $enc; ?>','<?php echo $pid; ?>','<?php echo $date; ?> ')" class='css_button_small'><span><?php echo  htmlspecialchars( xl('Preview'), ENT_NOQUOTES); ?></span></a>|  
                                                                       <?php if($result1['audit_status']=='Complete'){  ?> <input type='checkbox' name='finalize_<?php echo $enc; ?>' id='finalize_<?php echo $enc; ?>' onclick="postValue('<?php echo $enc; ?>','<?php echo $pid; ?>','<?php echo $date; ?> ')"/>Finalize | <?php } else if($result1['audit_status']=='Incomplete'){ ?>
                                                                           <input type='checkbox' name='finalize_<?php echo $enc; ?>' id='finalize_<?php echo $enc; ?>' onclick="postValue('<?php echo $enc; ?>','<?php echo $pid; ?>','<?php echo $date; ?> ')" disabled />Finalize |
                                                                       <?php } echo "<a  class='css_button_small'><span>".
                                                                                htmlspecialchars( xl('View'), ENT_NOQUOTES)."</span></a>";
                                                                     } else if($result1['finalized_val']=='finalized'){
                                                                        $enc=$result1['encounter'];
                                                                        $pid=$result1['pid'];
                                                                        $date=$result1['date1'];
                                                                        ?><a   class='css_button_small'><span><?php echo  htmlspecialchars( xl('Preview'), ENT_NOQUOTES); ?></span></a>|
                                                                        <?php 

                                                                        if($result1['audit_status']=='Complete'){ ?>
                                                                         <input type='checkbox' name='finalize_<?php echo $enc; ?>' id='finalize_<?php echo $enc; ?>' onclick="postValue('<?php echo $enc; ?>','<?php echo $pid; ?>','<?php echo $date; ?> ')"  checked disabled/>Finalize | <?php 
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
                                                                echo "<td>".$result1['pname']."</td>";
                                                                echo "<td>".$result1['sex']."</td>";
                                                                echo "<td>".$result1['encounter']."</td>";
                                                                echo "<td>"; if($result1['provider_id']!='') $pro_id=sqlStatement("select CONCAT(fname,' ',lname) AS provider from users where id='".$result1['provider_id']."' ");
                                                                                    $id_row4=sqlFetchArray($pro_id);
                                                                                    $proid=$id_row4['provider'];{ echo $proid; }echo"</td>";
                                                                echo "<td>";if($result1['facility_id']!='')  $pro_id2=sqlStatement("select name from facility where id='".$result1['facility_id']."' ");
                                                                                    $id_row5=sqlFetchArray($pro_id2);
                                                                                    $proid23=$id_row5['name'];{ echo $proid23; }echo"</td>";
                                                                echo "<td>"; if($result1['pc_catid']!='') $pro_id1=sqlStatement("select pc_catname from openemr_postcalendar_categories where pc_catid='".$result1['pc_catid']."' ");
                                                                                    $id_row41=sqlFetchArray($pro_id1);
                                                                                    $proid1=$id_row41['pc_catname'];{ echo $proid1; }echo"</td>";
                                                                echo "<td>"; echo $result1['required_complete'];   echo"</td>";
                                                                echo "<td>"; echo $result1['required_incomplete'];   echo"</td>";
                                                                echo "<td>"; echo $result1['optional'];   echo"</td>";
                                                                echo "<td>"; echo $result1['audit_status'];   echo"</td>";     
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
                                                                                 $enc=$result1['encounter'];
                                                                                 $pid=$result1['pid'];
                                                                                 $date=$result1['date1']; 
                                                                              ?><a href='javascript:; ' onclick="previewpost1('<?php echo $enc; ?>','<?php echo $pid; ?>','<?php echo $date; ?> ')" class='css_button_small'><span><?php echo  htmlspecialchars( xl('Preview'), ENT_NOQUOTES); ?></span></a>|
                                                                                   <!--<input type="checkbox" id="finalize" name="finalize"  >Finalize-->
                                                                                  <?php if($result1['audit_status']=='Complete'){  ?> <input type='checkbox' name='finalize_<?php echo $enc; ?>' id='finalize_<?php echo $enc; ?>' onclick="postValue('<?php echo $enc; ?>','<?php echo $pid; ?>','<?php echo $date; ?> ')"/>Finalize | 
                                                                                  <?php } else if($result1['audit_status']=='Incomplete') {?> <input type='checkbox' name='finalize_<?php echo $enc; ?>' id='finalize_<?php echo $enc; ?>' onclick="postValue('<?php echo $enc; ?>','<?php echo $pid; ?>','<?php echo $date; ?> ')" disabled />Finalize |  <?php } 
                                                                                  echo "<a  class='css_button_small'><span>".
                                                                                    htmlspecialchars( xl('View'), ENT_NOQUOTES)."</span></a>";

                                                                        }else if($result1['finalized_val']=='finalized'){
                                                                                $enc=$result1['encounter'];
                                                                                $pid=$result1['pid'];
                                                                                $date=$result1['date1'];
                                                                                ?><a href='javascript:void(0); ' onclick="previewpost1('<?php echo $enc; ?>','<?php echo $pid; ?>','<?php echo $date; ?> ')" class='css_button_small'><span><?php echo  htmlspecialchars( xl('Preview'), ENT_NOQUOTES); ?></span></a>|
                                                                                <?php 

                                                                                if($result1['audit_status']=='Complete'){ ?>
                                                                                <input type='checkbox' name='finalize_<?php echo $enc; ?>' id='finalize_<?php echo $enc; ?>' onclick="postValue('<?php echo $enc; ?>','<?php echo $pid; ?>','<?php echo $date; ?> ')"  checked disabled/>Finalize | <?php 
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
                                                                        echo "<td>".$result1['pname']."</td>";
                                                                        echo "<td>".$result1['sex']."</td>";
                                                                        echo "<td>".$result1['encounter']."</td>";
                                                                        echo "<td>"; if($result1['provider_id']!='') $pro_id=sqlStatement("select CONCAT(fname,' ',lname) AS provider from users where id='".$result1['provider_id']."' ");
                                                                                    $id_row4=sqlFetchArray($pro_id);
                                                                                    $proid=$id_row4['provider'];{ echo $proid; }echo"</td>";
                                                                        echo "<td>";if($result1['facility_id']!='')  $pro_id2=sqlStatement("select name from facility where id='".$result1['facility_id']."' ");
                                                                                    $id_row5=sqlFetchArray($pro_id2);
                                                                                    $proid23=$id_row5['name'];{ echo $proid23; }echo"</td>";
                                                                        echo "<td>"; if($result1['pc_catid']!='') $pro_id1=sqlStatement("select pc_catname from openemr_postcalendar_categories where pc_catid='".$result1['pc_catid']."' ");
                                                                                    $id_row41=sqlFetchArray($pro_id1);
                                                                                    $proid1=$id_row41['pc_catname'];{ echo $proid1; }echo"</td>";
                                                                        echo "<td>"; echo $result1['required_complete'];   echo"</td>";
                                                                        echo "<td>"; echo $result1['required_incomplete'];   echo"</td>";
                                                                        echo "<td>"; echo $result1['optional'];   echo"</td>";
                                                                        echo "<td>"; echo $result1['audit_status'];   echo"</td>"; 
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
</div>
</body>
<style type="text/css">
    @import url(../../library/dynarch_calendar.css);
</style>
<script type="text/javascript" src="../../library/dynarch_calendar.js"></script>
<?php include_once("{$GLOBALS['srcdir']}/dynarch_calendar_en.inc.php"); ?>
<script type="text/javascript"
	src="../../library/dynarch_calendar_setup.js"></script>
<script type="text/javascript">
 Calendar.setup({inputField:"form_from_date", ifFormat:"%Y-%m-%d", button:"img_from_date"});
 Calendar.setup({inputField:"form_to_date", ifFormat:"%Y-%m-%d", button:"img_to_date"});
</script>
</html>