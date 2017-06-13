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



require_once("verify_session.php");


$pagename = "incomp"; 
if(isset($_SESSION['portal_username']) !=''){
   $provider=$_SESSION['portal_username'];
}else {
  echo $provider=$_REQUEST['provider'];
}



 $sql=sqlStatement("SELECT id, fname, lname, specialty FROM users " .
      "WHERE active = 1 AND ( info IS NULL OR info NOT LIKE '%Inactive%' ) " .
      "AND authorized = 1 AND username='".$provider."'" .
      "ORDER BY lname, fname");
$id=sqlFetchArray($sql);
$id1=$id['id'];





//to save columns order
$order=$_POST['col'];

$menu=$_REQUEST['menu_val'];
if($provider!='' && $order!='' && $menu!=''){
$sql1=sqlStatement("SELECT * from tbl_allcare_providers_fieldsorder where username='$provider' AND menu='$menu'");
$row1=sqlFetchArray($sql1);
if(empty($row1)){
$sql=sqlStatement("INSERT INTO `tbl_allcare_providers_fieldsorder`(`username`, `menu`, `order_of_columns`) VALUES ('$provider','$menu','$order') ");
}else{
    
    $sql=sqlStatement("UPDATE `tbl_allcare_providers_fieldsorder` SET `order_of_columns`='$order' WHERE username='$provider' AND menu='$menu'");
}
}

$limit_from=$_POST['lfrom'] ? $_POST['lfrom'] :0;
$limit_to=$_POST['lto'] ? $_POST['lto'] : 25 ;
if (! $_POST['form_from_date']) {
	// If a specific patient, default to 2 years ago.
//	$tmp = date('Y')-2;
//	$from = date("$tmp-m-d");
//        $to = date("$tmp-m-d");
        $tmp = date('m')-1;
	$from = date("Y-$tmp-d");
        $to = date("Y-m-d");
}

if($_POST['def']=='Default'){
        $tmp = date('m')-1;
	$from = date("Y-$tmp-d");
        $to = date("Y-m-d");
        $enc_stat=array([0]=>0);
        $facility=array([0]=>'');
        $del=sqlStatement("delete from tbl_allcare_providers_fieldsorder where username='$provider' AND menu='incomplete_encouner'");
}else if($_POST['mode1']=='add'){
    //$provider  = $_POST['form_provider'];
 $patient=$_POST['form_patient'];
//print_r($patient);
$audit_stat=$_POST['form_audit_stat'];
//print_r($audit_stat);
 $enc_stat=$_POST['form_enc_stat'];

$from=$_POST['form_from_date'];
$to=$_POST['form_to_date'];

$facility  = $_POST['form_facility1'];
$visit_cat=$_POST['pc_catid'];

foreach($patient as $key1=>$val1){
    $patient_val.=$val1."|";
   $patient_val1= rtrim($patient_val,"|");
}
foreach($audit_stat as $key2=>$val2){
    $audit_val=$val2."|";
    $audit_val1=rtrim($audit_val,"|");
}
foreach($enc_stat as $key3=>$val3){
    $enc_val.=$val3."|";
    $enc_val1=rtrim($enc_val,"|");
}
foreach($facility as $key4=>$val4){
    $fac_vals.=$val4."|";
    $fac_val1=rtrim($fac_vals,"|");
}
foreach($visit_cat as $key5=>$val5){
    $vc_val.=$val5."|";
    $vc_val1=rtrim($vc_val,"|");
    
}

$sql=sqlStatement("insert into incomplete_charts_filter (provider,patient,audit_status,encounter_status,`from`,`to`,facility,visit_category,loginuser) values('$id1','$patient_val1','$audit_val1','$enc_val1','$from','$to','$fac_val1','$vc_val1','$provider')");
 
} else{
     $selectpatient=$_REQUEST['form_patient'] ? $_REQUEST['form_patient'] : '';
    if($selectpatient!=''){
       
        $patient=$_REQUEST['form_patient'];
        $tmp = date('m')-1;
        if($_REQUEST['form_to_date']!=''){
             $to = $_REQUEST['form_to_date'];
        }else {
             $to = date("Y-m-d");
        }
	 $from = date("Y-$tmp-d");
      
        $enc_stat=array([0]=>0);
        $facility=array([0]=>'');
    }else{
        $sql1=sqlStatement("select * from incomplete_charts_filter where loginuser='$provider' order by id desc");
        $row=sqlFetchArray($sql1);
        $patient=explode("|",$row['patient']);
        $audit_stat=explode("|",$row['audit_status']);
        $enc_stat=explode("|",$row['encounter_status']);

        if($row['from']=='' && $row['to']==''){
            $tmp = date('m')-1;
            $from = date("Y-$tmp-d");
            $to = date("Y-m-d");
        }else{
            $from=$row['from'];
            $to=$row['to'];
        }

        $facility=explode("|",$row['facility']); 

        $visit_cat=explode("|",$row['visit_category']);
    }
    
}

$encounter=$_POST['encounter'];
$audit_pid= $_POST['pid'];  
$note=$_POST['form_audit_note_'.$encounter];
if($encounter!='' && $audit_pid!='' && $note!=''){
    
     $sql=sqlStatement("UPDATE `form_encounter` SET `audit_note`='$note' WHERE encounter='$encounter' AND pid='$audit_pid'");
}
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



function VisitsWithNoForms($resid1,$respid1,$resenc1,$resfname1,$ros,$pe,$allcare,$audit_stat,$provider,$menu,$id1)
       {
                       
                        $fuv_sql=sqlStatement("SELECT DISTINCT (
                                        form_encounter.encounter
                                        ), form_encounter.facility, f.screen_group, form_encounter.pid,form_encounter.facility_id, form_encounter.encounter, form_encounter.pc_catid AS visitcategory_id, DATE_FORMAT( form_encounter.date,  '%Y-%m-%d' ) AS dos, form_encounter.provider_id AS provider_id,f.screen_names
                                        FROM form_encounter
                                        INNER JOIN patient_data ON patient_data.pid = form_encounter.pid
                                        INNER JOIN tbl_allcare_facuservisit f ON  `facilities` REGEXP (
                                        form_encounter.facility_id
                                        )
                                        AND  `users` REGEXP (
                                        form_encounter.rendering_provider
                                        )
                                        AND  `visit_categories` REGEXP (
                                        form_encounter.pc_catid
                                        )
                                        INNER JOIN layout_options l ON l.group_name = f.screen_group
                                        AND l.form_id = f.form_id
                                        WHERE  form_encounter.encounter=$resenc1 AND (form_encounter.provider_id=$id1 OR form_encounter.rendering_provider=$id1) GROUP BY f.screen_group ORDER BY f.id DESC"); 
                                $i=0;
                                 while ($fuv_row1=sqlFetchArray($fuv_sql)){ 
                                     //echo "<pre>"; print_r($fuv_row1); echo "</pre>";
                                    
                                     if(substr($fuv_row1['screen_group'],1)=='Dictation'){
                                          $codegrpname=substr($fuv_row1['screen_group'],1);
                                          $result=unserialize($fuv_row1['screen_names']);
                                          //echo "<pre>"; print_r($result); echo "</pre>";
                                         foreach($result as $key => $val) {
                                              if (stripos($val, "Unused") == false) {
                                                   $scr_val=explode("$$",$val);
                                                   $field_id=$scr_val[2];
                                                   $priority=$scr_val[1];
                                                   $order1=$scr_val[0];
                                                   if($order1!='') { 
                                                       $sequence[$order1]=$val;
                                                   }else { 
                                                      $sequence[$val]=$val;

                                                   }
                                              }
                                         }
                                         ksort($sequence);
                                         //echo "<pre>"; print_r($sequence); echo "</pre>";
                                         foreach($sequence as $key => $value){
                                                      $scr_val=explode("$$",$value);
                                                      $field_id=$scr_val[2];
                                                      $priority=$scr_val[1];
                                                      $order2=$scr_val[0];
                                                      if($order2!='') $order=$order2.".";
                                                      else  $order=$order2;
                                                  
                                                  if($resfname1=='Allcare Encounter Forms' && $allcare=='YES' ){ 
                                                   if($field_id=='chief_complaint' || $field_id=='hpi' || $field_id=='assessment_note' || $field_id=='progress_note' || $field_id=='plan_note' ){
                                                         if($field_id=='chief_complaint'){
                                                           $field_id1='chief_complaint_stat';   
                                                           $gname='Chief_Complaint';
                                                           $title1='Chief Complaint';
                                                        } else if($field_id=='hpi'){
                                                            $field_id1='hpi_stat';
                                                            $gname='History_of_Present_illness';
                                                            $title1='History of Present illness';
                                                        } else if($field_id=='assessment_note'){
                                                            $field_id1='assessment_note_stat';
                                                            $gname='Assessment_Note';
                                                            $title1='Assessment Note';
                                                        } else if($field_id=='progress_note'){
                                                            $field_id1='progress_note_stat';
                                                            $gname='Progress_Note';
                                                            $title1='Progress Note';
                                                        } else if($field_id=='plan_note'){
                                                            $field_id1='plan_note_stat';
                                                            $gname='Plan_Note';
                                                            $title1='Plan Note';
                                                        } 
                                                        $resfname3=str_replace(" ","_",$resfname1);
                                                        if($priority=='Required'){
                                                          // echo "select count(*) as cnt from lbf_data  where field_id LIKE  '%$field_id1%' AND field_value='finalized' AND form_id='".$resid1."'";
                                                            $ra = sqlStatement("select field_value from lbf_data  where field_id LIKE  '%$field_id1%' AND form_id='".$resid1."'");
                                                               $a='';
                                                            while($frowa = sqlFetchArray($ra)){
                                                               $a=explode("|",$frowa['field_value']);
                                                             }
                                                             if(in_array('pending',$a) && in_array('finalized',$a)){
                                                                 $fname_comp.=$order."<a href='javascript:;' onclick=win1('allcare_encounter_forms/allcare_enc_forms.php?groupname=$gname&form_id1=$resid1&enc3=$resenc1&pid1=$respid1&inmode1=edit&file_name1=Incomplete_charts&provider=$provider&location=provider_portal&fname1=$resfname3') ><span>".htmlspecialchars( xl($title1), ENT_NOQUOTES)."</span></a><br>";
                                                             }
                                                            elseif(in_array('pending',$a) ){
                                                
                                                              $fname.=$order."<a href='javascript:;' onclick=win1('allcare_encounter_forms/allcare_enc_forms.php?groupname=$gname&form_id1=$resid1&enc3=$resenc1&pid1=$respid1&inmode1=edit&file_name1=Incomplete_charts&provider=$provider&location=provider_portal&fname1=$resfname3') ><span>".htmlspecialchars( xl($title1), ENT_NOQUOTES)."</span></a><br>";
                                                           }elseif(in_array('finalized',$a)) {
                                                              
                                                               $fname_comp.=$order."<a href='javascript:;' onclick=win1('allcare_encounter_forms/allcare_enc_forms.php?groupname=$gname&form_id1=$resid1&enc3=$resenc1&pid1=$respid1&inmode1=edit&file_name1=Incomplete_charts&provider=$provider&location=provider_portal&fname1=$resfname3') ><span>".htmlspecialchars( xl($title1), ENT_NOQUOTES)."</span></a><br>";
                                                           }else{
                                                               $req_not_started.=$order."<a href='javascript:;' onclick=win1('allcare_encounter_forms/allcare_enc_forms.php?groupname=$gname&form_id1=$resid1&enc3=$resenc1&pid1=$respid1&inmode1=edit&file_name1=Incomplete_charts&provider=$provider&location=provider_portal&fname1=$resfname3') ><span>".htmlspecialchars( xl($title1), ENT_NOQUOTES)."</span></a><br>";
                                                           }
                                                       } else if($priority=='Optional'){
                                                          
                                                            $ra = sqlStatement("select field_value from lbf_data  where field_id LIKE  '%$field_id1%'  AND form_id='".$resid1."'");
                                                              $a='';
                                                            while($frowa = sqlFetchArray($ra)){
                                                               
                                                               $a=explode("|",$frowa['field_value']);
                                                             }
                                                            
                                                            if(in_array('pending',$a) && in_array('finalized',$a)){
                                                              
                                                                 $fname_opt_comp.=$order."<a href='javascript:;' onclick=win1('allcare_encounter_forms/allcare_enc_forms.php?groupname=$gname&form_id1=$resid1&enc3=$resenc1&pid1=$respid1&inmode1=edit&file_name1=Incomplete_charts&provider=$provider&location=provider_portal&fname1=$resfname3') ><span>".htmlspecialchars( xl($title1), ENT_NOQUOTES)."</span></a><br>";
                                                            } 
                                                            elseif(in_array('pending',$a)){
                                                                   
                                                                 $fname_opt.=$order."<a href='javascript:;' onclick=win1('allcare_encounter_forms/allcare_enc_forms.php?groupname=$gname&form_id1=$resid1&enc3=$resenc1&pid1=$respid1&inmode1=edit&file_name1=Incomplete_charts&provider=$provider&location=provider_portal&fname1=$resfname3') ><span>".htmlspecialchars( xl($title1), ENT_NOQUOTES)."</span></a><br>";                                        
                                                           }elseif(in_array('finalized',$a)){
                                                                   
                                                                $fname_opt_comp.=$order."<a href='javascript:;' onclick=win1('allcare_encounter_forms/allcare_enc_forms.php?groupname=$gname&form_id1=$resid1&enc3=$resenc1&pid1=$respid1&inmode1=edit&file_name1=Incomplete_charts&provider=$provider&location=provider_portal&fname1=$resfname3') ><span>".htmlspecialchars( xl($title1), ENT_NOQUOTES)."</span></a><br>";
                                                           }else {
                                                               
                                                               $opt_not_started.=$order."<a href='javascript:;' onclick=win1('allcare_encounter_forms/allcare_enc_forms.php?groupname=$gname&form_id1=$resid1&enc3=$resenc1&pid1=$respid1&inmode1=edit&file_name1=Incomplete_charts&provider=$provider&location=provider_portal&fname1=$resfname3') ><span>".htmlspecialchars( xl($title1), ENT_NOQUOTES)."</span></a><br>";
                                                           }
                                                           
                                                       } 
                                                    }
            
                                                  }
                                                 
                                         }
                                        
                                         if($fname!=''){
                                             $fname2.="<b>".$codegrpname.":</b><br>".$fname;
                                         }
                                         if($fname_opt!=''){
                                             $fname_opt2.="<b>".$codegrpname.":</b><br>".$fname_opt;
                                         }
                                         if($fname_opt_comp!=''){
                                             $fname_opt_comp2.="<b>".$codegrpname.":</b><br>".$fname_opt_comp;
                                         }
                                         if($fname_comp!=''){
                                             $fname_comp2.="<b>".$codegrpname.":</b><br>".$fname_comp;
                                         }
                                         if($req_not_started!=''){
                                             $req_not_started2.="<b>".$codegrpname.":</b><br>".$req_not_started;
                                         }
                                         if($opt_not_started!=''){
                                             $opt_not_started2.="<b>".$codegrpname.":</b><br>".$opt_not_started;
                                         }
                                         
                                     }else if(substr($fuv_row1['screen_group'],1)=='Form Data'){
                                        
                                         $codegrpname1=substr($fuv_row1['screen_group'],1);
                                         $result1=unserialize($fuv_row1['screen_names']);
                                         // echo "<pre>"; print_r($result1); echo "</pre>";
                                         foreach($result1 as $key1 => $val1) {
                                              if (stripos($val1, "Unused") == false) {
                                                   $scr_val1=explode("$$",$val1);
                                                   $field_id2=$scr_val1[2];
                                                   $priority2=$scr_val1[1];
                                                   $order2=$scr_val1[0];
                                                   if($order2!='') { 
                                                       $sequence1[$order2]=$val1;
                                                   }else { 
                                                      $sequence1[$val1]=$val1;

                                                   }
                                              }
                                         }
                                         ksort($sequence1);
                                         
                                         foreach($sequence1 as $key2 => $val2){
                                                $scr_val5=explode("$$",$val2);
                                                $field_id5=$scr_val5[2];
                                                $priority5=$scr_val5[1];
                                                $order5=$scr_val5[0];
                                                if($order5!='') $order=$order5.".";
                                                      else  $order=$order5;
                                            if($field_id5=='history_past' || $field_id5=='family_history' || $field_id5=='family_med_con' || $field_id5=='family_exam_test' || $field_id5=='history_social'){
                                                if($field_id5=='history_past'){
                                                    $grpname='Past_Medical_History';
                                                    $his_title='Past Medical History';
                                                    $grp_stat='History_Past';
                                                }else if($field_id5=='family_history'){
                                                    $grpname='Family_History';
                                                    $his_title='Family History';
                                                    $grp_stat='BFamily_History';
                                                }else if($field_id5=='family_med_con'){
                                                    $grpname='Primary_Family_Med_Conditions';
                                                    $his_title='Primary Family Med Conditions';
                                                    $grp_stat='CFamily_History_Medical_Conditi';
                                                }else if($field_id5=='family_exam_test'){
                                                    $grpname='Tests_and_Exams';
                                                    $his_title='Tests and Exams';
                                                    $grp_stat='Family_History_Exam_Test';
                                                }else if($field_id5=='history_social'){
                                                    $grpname='Social_History';
                                                    $his_title='Social History';
                                                    $grp_stat='History_Social';
                                                }        
                                                
                                                     $resid_his=sqlStatement("select *  from history_data where  pid=$respid1  ");  
                                                     $frow_his = sqlFetchArray($resid_his);

                                                     $res12=sqlstatement("select form_id  from forms where form_name ='Allcare Encounter Forms' AND encounter='$resenc1' AND pid='$respid1' AND deleted=0 order by id desc");
                                                     $frow_res = sqlFetchArray($res12);
                                                     if(!empty($frow_res)){
                                                             $formid12=$frow_res['form_id']; 
                                                         }else {
                                                             $formid12='0';
                                                         }
                                                     $res1=sqlstatement("select * from lbf_data lb INNER JOIN layout_options l ON l.field_id=lb.field_id where lb.form_id='$formid12' AND l.form_id='LBF2' AND l.group_name LIKE '%$grp_stat%' AND lb.field_id LIKE '%_stat%' order by seq");
                                                     $res_row1=sqlFetchArray($res1);
                                                     $field_val1=explode("|",$res_row1['field_value']);
                                                     if($priority5=='Required'){ 
                                                         if(in_array('finalized',$field_val1) && in_array('pending',$field_val1)){
                                                             $fname_comp_his.=$order."<a href='javascript:;' onclick=win1('history/history_custom.php?pid=$respid1&provider=$provider&location=provider_portal&grpname=$grpname&form_id=$formid12&grp_stat=$grp_stat&encounter=$resenc1') ><span>".htmlspecialchars( xl($his_title), ENT_NOQUOTES)."</span></a><br>";
                                                         }
                                                        else if(in_array('finalized',$field_val1)){
                                                            
                                                              $fname_comp_his.=$order."<a href='javascript:;' onclick=win1('history/history_custom.php?pid=$respid1&provider=$provider&location=provider_portal&grpname=$grpname&form_id=$formid12&grp_stat=$grp_stat&encounter=$resenc1') ><span>".htmlspecialchars( xl($his_title), ENT_NOQUOTES)."</span></a><br>";
                                                         }elseif(in_array('pending',$field_val1) ) {

                                                             $fname_his.=$order."<a href='javascript:;' onclick=win1('history/history_custom.php?pid=$respid1&provider=$provider&location=provider_portal&grpname=$grpname&form_id=$formid12&grp_stat=$grp_stat&encounter=$resenc1') ><span>".htmlspecialchars( xl($his_title), ENT_NOQUOTES)."</span></a><br>";
                                                        }else {
                                                            $req_not_started_his.=$order."<a href='javascript:;' onclick=win1('history/history_custom.php?pid=$respid1&provider=$provider&location=provider_portal&grpname=$grpname&form_id=$formid12&grp_stat=$grp_stat&encounter=$resenc1') ><span>".htmlspecialchars( xl($his_title), ENT_NOQUOTES)."</span></a><br>";
                                                        }
                                                    }else if($priority5=='Optional'){
                                                        if(in_array('finalized',$field_val1) && in_array('pending',$field_val1) ){
                                                            $fname_opt_his_comp.=$order."<a href='javascript:;' onclick=win1('history/history_custom.php?pid=$respid1&provider=$provider&location=provider_portal&grpname=$grpname&form_id=$formid12&grp_stat=$grp_stat&encounter=$resenc1') ><span>".htmlspecialchars( xl($his_title), ENT_NOQUOTES)."</span></a><br>";
                                                        }
                                                        else if(in_array('finalized',$field_val1)){

                                                              $fname_opt_his_comp.=$order."<a href='javascript:;' onclick=win1('history/history_custom.php?pid=$respid1&provider=$provider&location=provider_portal&grpname=$grpname&form_id=$formid12&grp_stat=$grp_stat&encounter=$resenc1') ><span>".htmlspecialchars( xl($his_title), ENT_NOQUOTES)."</span></a><br>";
                                                        }else if(in_array('pending',$field_val1) ) {

                                                            $fname_opt_his.=$order."<a href='javascript:;' onclick=win1('history/history_custom.php?pid=$respid1&provider=$provider&location=provider_portal&grpname=$grpname&form_id=$formid12&grp_stat=$grp_stat&encounter=$resenc1') ><span>".htmlspecialchars( xl($his_title), ENT_NOQUOTES)."</span></a><br>";
                                                        }else{
                                                            $opt_not_started_his.=$order."<a href='javascript:;' onclick=win1('history/history_custom.php?pid=$respid1&provider=$provider&location=provider_portal&grpname=$grpname&form_id=$formid12&grp_stat=$grp_stat&encounter=$resenc1') ><span>".htmlspecialchars( xl($his_title), ENT_NOQUOTES)."</span></a><br>";
                                                        }
                                                    }
                                                 }
                                                
                                              else if($field_id5=='face2face'){
                                                if($resfname1=='Allcare Encounter Forms' && $allcare=='YES' ){ 
                                                    $field_id1='f2f_stat';
                                                    $gname='Face_to_Face_HH_Plan';
                                                    $title1='Face to Face HH Plan';
                                                       
                                                    $resfname3=str_replace(" ","_",$resfname1);
                                                        if($priority5=='Required'){
                                                           
                                                            $ra = sqlStatement("select field_value from lbf_data  where field_id LIKE  '%$field_id1%'  AND form_id='".$resid1."'");
                                                             while($frowa = sqlFetchArray($ra)){
                                                                //$a = $frowa['field_value'];
                                                                
                                                                $a=explode("|",$frowa['field_value']);
                                                             }
                                                            if(in_array('pending',$a) && in_array('finalized',$a)){
                                                                $fname_comp_his.=$order."<a href='javascript:;' onclick=win1('allcare_encounter_forms/allcare_enc_forms.php?groupname=$gname&form_id1=$resid1&enc3=$resenc1&pid1=$respid1&inmode1=edit&file_name1=Incomplete_charts&provider=$provider&location=provider_portal&fname1=$resfname3') ><span>".htmlspecialchars( xl($title1), ENT_NOQUOTES)."</span></a><br>";
                                                            } 
                                                            elseif(in_array('pending',$a) ){
                                                
                                                              $fname_his.=$order."<a href='javascript:;' onclick=win1('allcare_encounter_forms/allcare_enc_forms.php?groupname=$gname&form_id1=$resid1&enc3=$resenc1&pid1=$respid1&inmode1=edit&file_name1=Incomplete_charts&provider=$provider&location=provider_portal&fname1=$resfname3') ><span>".htmlspecialchars( xl($title1), ENT_NOQUOTES)."</span></a><br>";
                                                           }elseif(in_array('finalized',$a)) {
                                                               
                                                               $fname_comp_his.=$order."<a href='javascript:;' onclick=win1('allcare_encounter_forms/allcare_enc_forms.php?groupname=$gname&form_id1=$resid1&enc3=$resenc1&pid1=$respid1&inmode1=edit&file_name1=Incomplete_charts&provider=$provider&location=provider_portal&fname1=$resfname3') ><span>".htmlspecialchars( xl($title1), ENT_NOQUOTES)."</span></a><br>";
                                                           }else {
                                                               $req_not_started_his.=$order."<a href='javascript:;' onclick=win1('allcare_encounter_forms/allcare_enc_forms.php?groupname=$gname&form_id1=$resid1&enc3=$resenc1&pid1=$respid1&inmode1=edit&file_name1=Incomplete_charts&provider=$provider&location=provider_portal&fname1=$resfname3') ><span>".htmlspecialchars( xl($title1), ENT_NOQUOTES)."</span></a><br>";
                                                           }
                                                       } else if($priority5=='Optional'){
                                                           
                                                            $ra = sqlStatement("select field_value from lbf_data  where field_id LIKE  '%$field_id1%'  AND form_id='".$resid1."'");
                                                             while($frowa = sqlFetchArray($ra)){
                                                               $a=explode("|",$frowa['field_value']);
                                                             }
                                                             if(in_array('pending',$a) && in_array('finalized',$a)){
                                                                 $fname_opt_his_comp.=$order."<a href='javascript:;' onclick=win1('allcare_encounter_forms/allcare_enc_forms.php?groupname=$gname&form_id1=$resid1&enc3=$resenc1&pid1=$respid1&inmode1=edit&file_name1=Incomplete_charts&provider=$provider&location=provider_portal&fname1=$resfname3') ><span>".htmlspecialchars( xl($title1), ENT_NOQUOTES)."</span></a><br>";
                                                             }
                                                             elseif(in_array('pending',$a)){
                                                     
                                                                 $fname_opt_his.=$order."<a href='javascript:;' onclick=win1('allcare_encounter_forms/allcare_enc_forms.php?groupname=$gname&form_id1=$resid1&enc3=$resenc1&pid1=$respid1&inmode1=edit&file_name1=Incomplete_charts&provider=$provider&location=provider_portal&fname1=$resfname3') ><span>".htmlspecialchars( xl($title1), ENT_NOQUOTES)."</span></a><br>";                                        
                                                           }elseif(in_array('finalized',$a)) {
                                                                
                                                                $fname_opt_his_comp.=$order."<a href='javascript:;' onclick=win1('allcare_encounter_forms/allcare_enc_forms.php?groupname=$gname&form_id1=$resid1&enc3=$resenc1&pid1=$respid1&inmode1=edit&file_name1=Incomplete_charts&provider=$provider&location=provider_portal&fname1=$resfname3') ><span>".htmlspecialchars( xl($title1), ENT_NOQUOTES)."</span></a><br>";
                                                           }else{
                                                               $opt_not_started_his.=$order."<a href='javascript:;' onclick=win1('allcare_encounter_forms/allcare_enc_forms.php?groupname=$gname&form_id1=$resid1&enc3=$resenc1&pid1=$respid1&inmode1=edit&file_name1=Incomplete_charts&provider=$provider&location=provider_portal&fname1=$resfname3') ><span>".htmlspecialchars( xl($title1), ENT_NOQUOTES)."</span></a><br>";
                                                           }
                                                           
                                                       } 
                                                   
            
                                                }
                                              }
                                         }
                                         if($fname_his!=''){
                                             $fname2.="<b>".$codegrpname1.":</b><br>".$fname_his;
                                         }
                                         if($fname_opt_his!=''){
                                             $fname_opt2.="<b>".$codegrpname1.":</b><br>".$fname_opt_his;
                                         }
                                         if($fname_comp_his!=''){
                                             $fname_comp2.="<b>".$codegrpname1.":</b><br>".$fname_comp_his;
                                         }
                                         if($fname_opt_his_comp!=''){
                                             $fname_opt_comp2.="<b>".$codegrpname1.":</b><br>".$fname_opt_his_comp;
                                         }
                                          if($opt_not_started_his!=''){
                                             $opt_not_started2.="<b>".$codegrpname1.":</b><br>".$opt_not_started_his;
                                         }
                                         if($req_not_started_his!=''){
                                             $req_not_started2.="<b>".$codegrpname1.":</b><br>".$req_not_started_his;
                                         }
                                     }else if(substr($fuv_row1['screen_group'],1)=='Predefined Forms'){
                                         $codegrpname3=substr($fuv_row1['screen_group'],1);
                                          $result3=unserialize($fuv_row1['screen_names']);
                                          //echo "<pre>"; print_r($result); echo "</pre>";
                                         foreach($result3 as $key3 => $val3) {
                                              if (stripos($val3, "Unused") == false) {
                                                   $scr_val=explode("$$",$val3);
                                                   $field_id=$scr_val[2];
                                                   $priority=$scr_val[1];
                                                   $order1=$scr_val[0];
                                                   if($order1!='') { 
                                                       $sequence3[$order1]=$val3;
                                                   }else { 
                                                      $sequence3[$val3]=$val3;

                                                   }
                                              }
                                         }
                                         ksort($sequence3);
                                         foreach($sequence3 as $keys => $values){
                                             $scr_val5=explode("$$",$values);
                                                $field_id5=$scr_val5[2];
                                                $priority5=$scr_val5[1];
                                                $order5=$scr_val5[0];
                                                if($order5!='') $order=$order5.".";
                                                      else  $order=$order5;
                                             
                                             if($field_id5=='ros'){
                                                     if($priority5=='Required'){
                                                        $resid_ros=sqlStatement("select *  from forms where form_name  ='Allcare Review Of Systems' AND encounter=$resenc1 AND pid=$respid1  AND deleted=0 ORDER BY id DESC");  
                                                        $frow_ros = sqlFetchArray($resid_ros); 
                                                        $fid1=$frow_ros['form_id'];
                                                        if($fid1!=''){
                                                             $resid_ros1=sqlStatement("select finalized,pending  from tbl_form_allcare_ros where id  =$fid1  AND pid=$respid1  ORDER BY id DESC");  
                                                             $frow_ros1 = sqlFetchArray($resid_ros1); 
                                                             if($frow_ros1['finalized']=='YES' && $frow_ros1['pending']=='YES'){
                                                                 $fname_comp_pre.=$order."<a href='javascript:;' onclick=win1('allcare_ros/view_custom.php?formname=allcare_ros&encounter=$resenc1&pid=$respid1&id=$fid1&location=provider_portal&provider=$provider') ><span>".htmlspecialchars( xl('Allcare Ros'), ENT_NOQUOTES)."</span></a><br>";
                                                             }
                                                             elseif($ros='YES'  && $frow_ros1['pending']=='YES'){
        
                                                             $fname_pre.=$order."<a href='javascript:;' onclick=win1('allcare_ros/view_custom.php?formname=allcare_ros&encounter=$resenc1&pid=$respid1&location=provider_portal&provider=$provider&id=$fid1') ><span>".htmlspecialchars( xl('Allcare Ros'), ENT_NOQUOTES)."</span></a><br>";
                                                            } elseif($ros='YES' && $frow_ros1['finalized']=='YES') {
                                                                 
                                                                 $fname_comp_pre.=$order."<a href='javascript:;' onclick=win1('allcare_ros/view_custom.php?formname=allcare_ros&encounter=$resenc1&pid=$respid1&id=$fid1&location=provider_portal&provider=$provider') ><span>".htmlspecialchars( xl('Allcare Ros'), ENT_NOQUOTES)."</span></a><br>";
                                                            }else {
                                                                $req_not_started_pre.=$order."<a href='javascript:;' onclick=win1('allcare_ros/view_custom.php?formname=allcare_ros&encounter=$resenc1&pid=$respid1&id=$fid1&location=provider_portal&provider=$provider') ><span>".htmlspecialchars( xl('Allcare Ros'), ENT_NOQUOTES)."</span></a><br>";
                                                            }
                                                        }
                                                    }else if($priority5=='Optional'){
                                                        $resid_ros=sqlStatement("select *  from forms where form_name  ='Allcare Review Of Systems' AND encounter=$resenc1 AND pid=$respid1  AND deleted=0 ORDER BY id DESC");  
                                                        $frow_ros = sqlFetchArray($resid_ros); 
                                                        $fid1=$frow_ros['form_id'];
                                                        if($fid1!=''){
                                                             $resid_ros1=sqlStatement("select finalized,pending  from tbl_form_allcare_ros where id  =$fid1  AND pid=$respid1  ORDER BY id DESC");  
                                                             $frow_ros1 = sqlFetchArray($resid_ros1);
                                                             if($frow_ros1['pending']=='YES' && $frow_ros1['finalized']=='YES'){
                                                                 $fname_opt_pre_comp.=$order."<a href='javascript:;' onclick=win1('allcare_ros/view_custom.php?formname=allcare_ros&encounter=$resenc1&pid=$respid1&id=$fid1&location=provider_portal&provider=$provider') ><span>".htmlspecialchars( xl('Allcare Ros'), ENT_NOQUOTES)."</span></a><br>";
                                                             }
                                                            elseif($frow_ros1['pending']=='YES'){
                                                                 
                                                             $fname_opt_pre.=$order."<a href='javascript:;' onclick=win1('allcare_ros/view_custom.php?formname=allcare_ros&encounter=$resenc1&pid=$respid1&location=provider_portal&provider=$provider&id=$fid1') ><span>".htmlspecialchars( xl('Allcare Ros'), ENT_NOQUOTES)."</span></a><br>";
                                                            }elseif($frow_ros1['finalized']=='YES'){
                                                                 
                                                                 $fname_opt_pre_comp.=$order."<a href='javascript:;' onclick=win1('allcare_ros/view_custom.php?formname=allcare_ros&encounter=$resenc1&pid=$respid1&id=$fid1&location=provider_portal&provider=$provider') ><span>".htmlspecialchars( xl('Allcare Ros'), ENT_NOQUOTES)."</span></a><br>";
                                                            }else {
                                                                $opt_not_started_pre.=$order."<a href='javascript:;' onclick=win1('allcare_ros/view_custom.php?formname=allcare_ros&encounter=$resenc1&pid=$respid1&id=$fid1&location=provider_portal&provider=$provider') ><span>".htmlspecialchars( xl('Allcare Ros'), ENT_NOQUOTES)."</span></a><br>";
                                                            }
                                                        }
                                                       
                                                        
                                                     }  
                                                }else if($field_id5=='physical_exam'){
                                                    
                                                     $resid_pe=sqlStatement("select *  from forms where form_name  ='Allcare Physical Exam' AND encounter=$resenc1 AND pid=$respid1  AND deleted=0 ORDER BY id DESC");  
                                                     $frow_pe = sqlFetchArray($resid_pe);
                                                     $fid=$frow_pe['form_id'];
                                                     if($fid!=''){
                                                          $resid_pe1=sqlStatement("select *  from tbl_allcare_formflag where form_name  ='Allcare Physical Exam' AND encounter_id=$resenc1 and form_id=$fid ORDER BY id DESC");  
                                                          $frow_pe1 = sqlFetchArray($resid_pe1);
                                                          if($priority5=='Required'){
                                                        if( $frow_pe1['finalized']=='Y' && $frow_pe1['pending']=='Y'){
                                                             
                                                             $fname_comp_pre.=$order."<a href='javascript:;' onclick=win1('allcare_physical_exam/new_custom.php?formname=allcare_physical_exam&edit=custom_pe&encounter=$resenc1&pid=$respid1&id=$fid&location=provider_portal&provider=$provider&menu_val=$menu') ><span>".htmlspecialchars( xl('Allcare Physical_exam'), ENT_NOQUOTES)."</span></a><br>";
                                                        }      
                                                       else if($pe='YES' && $frow_pe1['pending']=='Y'){
      
                                                             
                                                             $fname_pre.=$order."<a href='javascript:;' onclick=win1('allcare_physical_exam/new_custom.php?formname=allcare_physical_exam&edit=custom_pe&encounter=$resenc1&pid=$respid1&location=provider_portal&provider=$provider&menu_val=$menu&id=$fid') ><span>".htmlspecialchars( xl('Allcare Physical_exam'), ENT_NOQUOTES)."</span></a><br>";
                                                             
                                                        }elseif($pe='YES' && $frow_pe1['finalized']=='Y') {
                                                             
                                                             $fname_comp_pre.=$order."<a href='javascript:;' onclick=win1('allcare_physical_exam/new_custom.php?formname=allcare_physical_exam&edit=custom_pe&encounter=$resenc1&pid=$respid1&id=$fid&location=provider_portal&provider=$provider&menu_val=$menu') ><span>".htmlspecialchars( xl('Allcare Physical_exam'), ENT_NOQUOTES)."</span></a><br>";
                                                        }else{
                                                            $req_not_started_pre.=$order."<a href='javascript:;' onclick=win1('allcare_physical_exam/new_custom.php?formname=allcare_physical_exam&edit=custom_pe&encounter=$resenc1&pid=$respid1&id=$fid&location=provider_portal&provider=$provider&menu_val=$menu') ><span>".htmlspecialchars( xl('Allcare Physical_exam'), ENT_NOQUOTES)."</span></a><br>";
                                                        }
                                                    }else if($priority5=='Optional'){
                                                        if($frow_pe1['finalized']=='Y' && $frow_pe1['pending']=='Y'){
                                                             $fname_opt_pre_comp.=$order."<a href='javascript:;' onclick=win1('allcare_physical_exam/new_custom.php?formname=allcare_physical_exam&edit=custom_pe&encounter=$resenc1&pid=$respid1&id=$fid&location=provider_portal&provider=$provider&menu_val=$menu') ><span>".htmlspecialchars( xl('Allcare Physical_exam'), ENT_NOQUOTES)."</span></a><br>";
                                                        }
                                                        else if($pe='YES' && $frow_pe1['pending']=='Y'){
                                                         
                                                             $fname_opt_pre.=$order."<a href='javascript:;' onclick=win1('allcare_physical_exam/new_custom.php?formname=allcare_physical_exam&edit=custom_pe&encounter=$resenc1&pid=$respid1&location=provider_portal&provider=$provider&menu_val=$menu&id=$fid') ><span>".htmlspecialchars( xl('Allcare Physical_exam'), ENT_NOQUOTES)."</span></a><br>";
                                                        }elseif($pe='YES' && $frow_pe1['finalized']=='Y') {
                                                             
                                                            $fname_opt_pre_comp.=$order."<a href='javascript:;' onclick=win1('allcare_physical_exam/new_custom.php?formname=allcare_physical_exam&edit=custom_pe&encounter=$resenc1&pid=$respid1&id=$fid&location=provider_portal&provider=$provider&menu_val=$menu') ><span>".htmlspecialchars( xl('Allcare Physical_exam'), ENT_NOQUOTES)."</span></a><br>";
                                                        }else{
                                                            $opt_not_started_pre.=$order."<a href='javascript:;' onclick=win1('allcare_physical_exam/new_custom.php?formname=allcare_physical_exam&edit=custom_pe&encounter=$resenc1&pid=$respid1&id=$fid&location=provider_portal&provider=$provider&menu_val=$menu') ><span>".htmlspecialchars( xl('Allcare Physical_exam'), ENT_NOQUOTES)."</span></a><br>";
                                                        }
                                                    }
                                                     }
                                                    
                                                     
                                                }
                                             //vitals 
                                             else if($field_id5=='vitals'){
                                                  $resid_vt=sqlStatement("select *  from forms where form_name  ='Vitals' AND encounter=$resenc1 AND pid=$respid1  AND deleted=0 ORDER BY id DESC");  
                                                     $frow_vt = sqlFetchArray($resid_vt);
                                                     $fid1=$frow_vt['form_id'];
                                                     $res12=sqlstatement("select form_id  from forms where form_name ='Allcare Encounter Forms' AND encounter='$resenc1' AND pid='$respid1' AND deleted=0 order by id desc");
                                                     $frow_res = sqlFetchArray($res12);
                                                     $formid=$frow_res['form_id'];
                                                     $res1=sqlstatement("select * from lbf_data lb INNER JOIN layout_options l ON l.field_id=lb.field_id where lb.form_id='$formid' AND l.form_id='LBF2' AND l.group_name LIKE '%Vitals%' AND lb.field_id LIKE '%_stat%' order by seq");
                                                     $res_row1=sqlFetchArray($res1);
                                                     $status=$res_row1['field_value'];
                                                     $field_val2=explode("|",$res_row1['field_value']);
                                                     if($fid1!=''){
                                                        if($priority5=='Required'){
                                                            if(in_array('pending',$field_val2)  && in_array('finalized',$field_val2)){
                                                                 $fname_comp_pre.=$order."<a href='javascript:;' onclick=win1('vitals/view_custom.php?formname=Vitals&encounter=$resenc1&pid=$respid1&id=$fid1&provider=$provider&location=provider_portal&status=$status') ><span>".htmlspecialchars( xl('Vitals'), ENT_NOQUOTES)."</span></a><br>";
                                                            }
                                                            elseif(in_array('pending',$field_val2) ){

                                                                  $fname_pre.=$order."<a href='javascript:;' onclick=win1('vitals/view_custom.php?formname=Vitals&encounter=$resenc1&pid=$respid1&provider=$provider&location=provider_portal&id=$fid1&status=$status') ><span>".htmlspecialchars( xl('Vitals'), ENT_NOQUOTES)."</span></a><br>";

                                                            }elseif(in_array('finalized',$field_val2)){

                                                                
                                                                $fname_comp_pre.=$order."<a href='javascript:;' onclick=win1('vitals/view_custom.php?formname=Vitals&encounter=$resenc1&pid=$respid1&id=$fid1&provider=$provider&location=provider_portal&status=$status') ><span>".htmlspecialchars( xl('Vitals'), ENT_NOQUOTES)."</span></a><br>";
                                                            }else{
                                                                $req_not_started_pre.=$order."<a href='javascript:;' onclick=win1('vitals/view_custom.php?formname=Vitals&encounter=$resenc1&pid=$respid1&id=$fid1&provider=$provider&location=provider_portal&status=$status') ><span>".htmlspecialchars( xl('Vitals'), ENT_NOQUOTES)."</span></a><br>";
                                                            }
                                                        }else if($priority5=='Optional'){
                                                            if(in_array('pending',$field_val2)  && in_array('finalized',$field_val2)){
                                                                 $fname_opt_pre_comp.=$order."<a href='javascript:;' onclick=win1('vitals/view_custom.php?formname=Vitals&encounter=$resenc1&pid=$respid1&id=$fid1&provider=$provider&location=provider_portal&status=$status') ><span>".htmlspecialchars( xl('Vitals'), ENT_NOQUOTES)."</span></a><br>";
                                                            }
                                                             elseif(in_array('pending',$field_val2) ){

                                                                  $fname_opt_pre.=$order."<a href='javascript:;' onclick=win1('vitals/view_custom.php?formname=Vitals&encounter=$resenc1&pid=$respid1&provider=$provider&location=provider_portal&id=$fid1&status=$status') ><span>".htmlspecialchars( xl('Vitals'), ENT_NOQUOTES)."</span></a><br>";

                                                            }elseif(in_array('finalized',$field_val2)) {
                                                                 
                                                                 $fname_opt_pre_comp.=$order."<a href='javascript:;' onclick=win1('vitals/view_custom.php?formname=Vitals&encounter=$resenc1&pid=$respid1&id=$fid1&provider=$provider&location=provider_portal&status=$status') ><span>".htmlspecialchars( xl('Vitals'), ENT_NOQUOTES)."</span></a><br>";
                                                            }else{
                                                                $opt_not_started_pre.=$order."<a href='javascript:;' onclick=win1('vitals/view_custom.php?formname=Vitals&encounter=$resenc1&pid=$respid1&id=$fid1&provider=$provider&location=provider_portal&status=$status') ><span>".htmlspecialchars( xl('Vitals'), ENT_NOQUOTES)."</span></a><br>";
                                                            }
                                                        }
                                                     }
                                                     
                                              } 
                                         }
                                         if($fname_pre!=''){
                                             $fname2.="<b>".$codegrpname3.":</b><br>".$fname_pre;
                                         }
                                         if($fname_opt_pre!=''){
                                             $fname_opt2.="<b>".$codegrpname3.":</b><br>".$fname_opt_pre;
                                         }
                                         if($fname_comp_pre!=''){
                                             $fname_comp2.="<b>".$codegrpname3.":</b><br>".$fname_comp_pre;
                                         }
                                         if($fname_opt_pre_comp!=''){
                                             $fname_opt_comp2.="<b>".$codegrpname3.":</b><br>".$fname_opt_pre_comp;
                                         }
                                         if($opt_not_started_pre!=''){
                                             $opt_not_started2.="<b>".$codegrpname3.":</b><br>".$opt_not_started_pre;
                                         }
                                         if($req_not_started_pre!=''){
                                             $req_not_started2.="<b>".$codegrpname3.":</b><br>".$req_not_started_pre;
                                         }
                                     }else if(substr($fuv_row1['screen_group'],1)=='Codes'){
                                          $codegrpname4=substr($fuv_row1['screen_group'],1);
                                          $result4=unserialize($fuv_row1['screen_names']);
                                        
                                         foreach($result4 as $key4 => $val4) {
                                              if (stripos($val4, "Unused") == false) {
                                                   $scr_val=explode("$$",$val4);
                                                   $field_id=$scr_val[2];
                                                   $priority=$scr_val[1];
                                                   $order1=$scr_val[0];
                                                   if($order1!='') { 
                                                       $sequence4[$order1]=$val4;
                                                   }
                                                   else { 
                                                      $sequence4[$val4]=$val4;

                                                   }
                                              }
                                         }
                                         ksort($sequence4);
                                        // echo "<pre>"; print_r($sequence4); echo "</pre>";
                                         foreach($sequence4 as $keys2 => $values2){
                                                $scr_val5=explode("$$",$values2);
                                                $field_id5=$scr_val5[2];
                                                $priority5=$scr_val5[1];
                                                $order5=$scr_val5[0];
                                                if($order5!='') $order=$order5.".";
                                                      else  $order=$order5;
                                            if($field_id5=='medical_problem' || $field_id5=='allergies' || $field_id5=='medication' || $field_id5=='surgeries' || $field_id5=='dental_problems'){
                                                   if($field_id5=='medical_problem'){
                                                       $type='medical_problem';
                                                       $title='Medical Problems';
                                                       $fieldid='medical_problem_stat';
                                                        $gname='Medical Problems';
                                                   }else if($field_id5=='allergies'){
                                                        $type='allergy';
                                                        $title='Allergies';
                                                        $gname='Allergies';
                                                         $fieldid='allergies_stat';
                                                   } else if($field_id5=='medication'){
                                                        $type='medication';
                                                        $title='Medications';
                                                        $fieldid='medication_stat';
                                                        $gname='Medication';
                                                   }else if($field_id5=='surgeries'){
                                                        $type='surgery';
                                                        $title='Surgeries';
                                                        $fieldid='surgeries_stat';
                                                        $gname='surgeries';
                                                   }else if($field_id5=='dental_problems'){
                                                        $type='dental';
                                                        $title='Dental Issues';
                                                          $gname='Dental';
                                                          $fieldid='dental_problems_stat';
                                                   }
                                                  
                                                     $resid_med=sqlStatement("select *  from lists li INNER JOIN issue_encounter ie ON ie.pid=li.pid AND ie.list_id=li.id where type='$type' AND ie.encounter=$resenc1 AND li.pid=$respid1");  
                                                     $frow_med = sqlFetchArray($resid_med);
                                                     $res12=sqlstatement("select form_id  from forms where form_name ='Allcare Encounter Forms' AND encounter='$resenc1' AND pid='$respid1' AND deleted=0 order by id desc");
                                                     $frow_res = sqlFetchArray($res12);
                                                      if(!empty($frow_res)){
                                                         $formid=$frow_res['form_id'];
                                                      }else {
                                                          $formid=0;
                                                      }  
                                                     $res1=sqlstatement("select * from lbf_data lb INNER JOIN layout_options l ON l.field_id=lb.field_id where lb.form_id='$formid' AND l.form_id='LBF2' AND l.group_name LIKE '%$gname%' AND lb.field_id LIKE '%$fieldid%' order by seq");
                                                     $res_row1=sqlFetchArray($res1);
                                                     $field_val=explode("|",$res_row1['field_value']);
                                                     if($priority5=='Required'){
                                                         if(in_array('finalized',$field_val) && in_array('pending',$field_val) ){
                                                              $fname_comp_issue.=$order."<a href='javascript:;' onclick=win1('Issues/stats_full_custom.php?active=all&category=$type&encounter=$resenc1&pid=$respid1&id=$fid1&location=provider_portal&provider=$provider&formid=$formid') ><span>".htmlspecialchars( xl($title), ENT_NOQUOTES)."</span></a><br>";
                                                         }
                                                        elseif(in_array('finalized',$field_val)){
                                                            
                                                             
                                                              $fname_comp_issue.=$order."<a href='javascript:;' onclick=win1('Issues/stats_full_custom.php?active=all&category=$type&encounter=$resenc1&pid=$respid1&id=$fid1&location=provider_portal&provider=$provider&formid=$formid') ><span>".htmlspecialchars( xl($title), ENT_NOQUOTES)."</span></a><br>";
                                                              
                                                        }else if(in_array('pending',$field_val) ) {
                                                           
        
                                                             $fname_issue.=$order."<a href='javascript:;' onclick=win1('Issues/stats_full_custom.php?active=all&category=$type&encounter=$resenc1&pid=$respid1&location=provider_portal&provider=$provider&formid=$formid') ><span>".htmlspecialchars( xl($title), ENT_NOQUOTES)."</span></a><br>";
                                                        }else {
                                                            $req_not_started_issue.=$order."<a href='javascript:;' onclick=win1('Issues/stats_full_custom.php?active=all&category=$type&encounter=$resenc1&pid=$respid1&location=provider_portal&provider=$provider&formid=$formid') ><span>".htmlspecialchars( xl($title), ENT_NOQUOTES)."</span></a><br>";
                                                        }
                                                    }else if($priority5=='Optional'){
                                                        if(in_array('finalized',$field_val) && in_array('pending',$field_val) ){
                                                            $fname_opt_issue_comp.=$order."<a href='javascript:;' onclick=win1('Issues/stats_full_custom.php?active=all&category=$type&encounter=$resenc1&pid=$respid1&id=$fid1&location=provider_portal&provider=$provider&formid=$formid') ><span>".htmlspecialchars( xl($title), ENT_NOQUOTES)."</span></a><br>"; 
                                                        }
                                                        else if(in_array('finalized',$field_val)){
                                                            
                                                              $fname_opt_issue_comp.=$order."<a href='javascript:;' onclick=win1('Issues/stats_full_custom.php?active=all&category=$type&encounter=$resenc1&pid=$respid1&id=$fid1&location=provider_portal&provider=$provider&formid=$formid') ><span>".htmlspecialchars( xl($title), ENT_NOQUOTES)."</span></a><br>"; 
                                                        }else if(in_array('pending',$field_val) ){
                                                           
        
                                                             $fname_opt_issue.=$order."<a href='javascript:;' onclick=win1('Issues/stats_full_custom.php?active=all&category=$type&encounter=$resenc1&pid=$respid1&location=provider_portal&provider=$provider&formid=$formid') ><span>".htmlspecialchars( xl($title), ENT_NOQUOTES)."</span></a><br>";
                                                        }else{
                                                            $opt_not_started_issue.=$order."<a href='javascript:;' onclick=win1('Issues/stats_full_custom.php?active=all&category=$type&encounter=$resenc1&pid=$respid1&location=provider_portal&provider=$provider&formid=$formid') ><span>".htmlspecialchars( xl($title), ENT_NOQUOTES)."</span></a><br>";
                                                        }
                                                    }
                                              }else if($field_id5=='immunization'){
                                                     $resid_im=sqlStatement("select *  from immunizations where  patient_id=$respid1  ");  
                                                     $frow_im = sqlFetchArray($resid_im);
                                                     $res2=sqlstatement("select form_id  from forms where form_name ='Allcare Encounter Forms' AND encounter='$resenc1' AND pid='$respid1' AND deleted=0 order by id desc");
                                                     $frow_res2 = sqlFetchArray($res2);
                                                     if(!empty($frow_res2)){
                                                          $formid1=$frow_res2['form_id'];
                                                     }else {
                                                         $formid1=0;
                                                     }
                                                     $res1=sqlstatement("select * from lbf_data lb INNER JOIN layout_options l ON l.field_id=lb.field_id where lb.form_id='$formid1' AND l.form_id='LBF2' AND l.group_name LIKE '%Immunization%' AND lb.field_id LIKE 'immunization_stat' order by seq");
                                                     $res_row1=sqlFetchArray($res1);
                                                     $field_val=explode("|",$res_row1['field_value']);
                                                     if($priority5=='Required'){ 
                                                         if(in_array('finalized',$field_val) && in_array('pending',$field_val)){
                                                             $fname_comp_issue.=$order."<a href='javascript:;' onclick=win1('Immunizations/immunizations_custom.php?pid=$respid1&provider=$provider&location=provider_portal&form_id=$formid1&encounter=$resenc1') ><span>".htmlspecialchars( xl('Immunizations'), ENT_NOQUOTES)."</span></a><br>";
                                                         }
                                                        elseif(in_array('finalized',$field_val)){
                                                            
                                                             $fname_comp_issue.=$order."<a href='javascript:;' onclick=win1('Immunizations/immunizations_custom.php?pid=$respid1&provider=$provider&location=provider_portal&form_id=$formid1&encounter=$resenc1') ><span>".htmlspecialchars( xl('Immunizations'), ENT_NOQUOTES)."</span></a><br>";
                                                         }else if(in_array('pending',$field_val) ){
        
                                                            $fname_issue.=$order."<a href='javascript:;' onclick=win1('Immunizations/immunizations_custom.php?pid=$respid1&provider=$provider&location=provider_portal&form_id=$formid1&encounter=$resenc1') ><span>".htmlspecialchars( xl('Immunizations'), ENT_NOQUOTES)."</span></a><br>";
                                                        }else {
                                                            $req_not_started_issue.=$order."<a href='javascript:;' onclick=win1('Immunizations/immunizations_custom.php?pid=$respid1&provider=$provider&location=provider_portal&form_id=$formid1&encounter=$resenc1') ><span>".htmlspecialchars( xl('Immunizations'), ENT_NOQUOTES)."</span></a><br>";
                                                        }
                                                    }else if($priority5=='Optional'){
                                                        if(in_array('finalized',$field_val) && in_array('pending',$field_val)){
                                                             $fname_opt_issue_comp.=$order."<a href='javascript:;' onclick=win1('Immunizations/immunizations_custom.php?pid=$respid1&provider=$provider&location=provider_portal&form_id=$formid1&encounter=$resenc1') ><span>".htmlspecialchars( xl('Immunizations'), ENT_NOQUOTES)."</span></a><br>";
                                                        }
                                                         elseif(in_array('finalized',$field_val)){
                                                               
                                                              $fname_opt_issue_comp.=$order."<a href='javascript:;' onclick=win1('Immunizations/immunizations_custom.php?pid=$respid1&provider=$provider&location=provider_portal&form_id=$formid1&encounter=$resenc1') ><span>".htmlspecialchars( xl('Immunizations'), ENT_NOQUOTES)."</span></a><br>";
                                                        }else if(in_array('pending',$field_val) ){
        
                                                             $fname_opt_issue.=$order."<a href='javascript:;' onclick=win1('Immunizations/immunizations_custom.php?pid=$respid1&provider=$provider&location=provider_portal&form_id=$formid1&encounter=$resenc1') ><span>".htmlspecialchars( xl('Immunizations'), ENT_NOQUOTES)."</span></a><br>";
                                                        }else{
                                                            $opt_not_started_issue.=$order."<a href='javascript:;' onclick=win1('Immunizations/immunizations_custom.php?pid=$respid1&provider=$provider&location=provider_portal&form_id=$formid1&encounter=$resenc1') ><span>".htmlspecialchars( xl('Immunizations'), ENT_NOQUOTES)."</span></a><br>";
                                                        }
                                                    }
                                             } else if($field_id5=='codes'){
                                            
                                                //if($i==0) {
                                                 $resid_his=sqlStatement("select *  from billing where  pid=$respid1 AND encounter=$resenc1 AND activity = 1 ");  
                                                     $frow_his = sqlFetchArray($resid_his);
                                                    // $fid1=$frow_im['form_id'];
                                                     if(!empty($frow_his)){
                                                        if($priority5=='Required'){ 
                                                                if(!empty($frow_his) && $frow_his['billed']==1){

                                                                      $fname_comp_issue.=$order."<a href='javascript:;' onclick=win1('codes/feesheet_custom.php?pid=$respid1&encounter=$resenc1&provider=$provider&location=provider_portal') ><span>".htmlspecialchars( xl('Codes'), ENT_NOQUOTES)."</span></a><br>";
                                                                 }else {

                                                                     $fname_issue.=$order."<a href='javascript:;' onclick=win1('codes/feesheet_custom.php?pid=$respid1&encounter=$resenc1&provider=$provider&location=provider_portal') ><span>".htmlspecialchars( xl('Codes'), ENT_NOQUOTES)."</span></a><br>";
                                                                }
                                                        }else if($priority5=='Optional'){
                                                                 if(!empty($frow_his) && $frow_his['billed']==1){

                                                                      $fname_opt_issue_comp.=$order."<a href='javascript:;' onclick=win1('codes/feesheet_custom.php?pid=$respid1&encounter=$resenc1&provider=$provider&location=provider_portal') ><span>".htmlspecialchars( xl('Codes'), ENT_NOQUOTES)."</span></a><br>";
                                                                }else {

                                                                    $fname_opt_issue.=$order."<a href='javascript:;' onclick=win1('codes/feesheet_custom.php?pid=$respid1&encounter=$resenc1&provider=$provider&location=provider_portal') ><span>".htmlspecialchars( xl('Codes'), ENT_NOQUOTES)."</span></a><br>";
                                                                }
                                                        }
                                                     }else{
                                                         if($priority5=='Required'){ 
                                                                

                                                                     $req_not_started_issue.=$order."<a href='javascript:;' onclick=win1('codes/feesheet_custom.php?pid=$respid1&encounter=$resenc1&provider=$provider&location=provider_portal') ><span>".htmlspecialchars( xl('Codes'), ENT_NOQUOTES)."</span></a><br>";
                                                               
                                                        }else if($priority5=='Optional'){
                                                                

                                                                    $opt_not_started_issue.=$order."<a href='javascript:;' onclick=win1('codes/feesheet_custom.php?pid=$respid1&encounter=$resenc1&provider=$provider&location=provider_portal') ><span>".htmlspecialchars( xl('Codes'), ENT_NOQUOTES)."</span></a><br>";
                                                               
                                                        }
                                                     }
                                                    
//                                             }
//                                             $i++;
                                          } else if($field_id5=='auditform'){
                                                     $resid_vt1=sqlStatement("select *  from forms where form_name  ='Audit Form' AND encounter=$resenc1 AND pid=$respid1  AND deleted=0 ORDER BY id DESC");  
                                                     $frow_vt1 = sqlFetchArray($resid_vt1);
                                                     $fid1=$frow_vt1['form_id'];
                                                     $resid_enc=sqlStatement("select *  from form_encounter where  encounter=$resenc1 AND pid=$respid1  ");  
                                                     $frow_enc = sqlFetchArray($resid_enc);
                                                    
                                                     if($fid1!=''){
                                                        if($priority5=='Required'){ 
                                                            if($frow_enc['elec_signedby']=='' || $frow_enc['elec_signed_on']==''){

                                                                  $fname_issue.=$order."<a href='javascript:;' onclick=win1('auditform/report.php?encounter=$resenc1&pid=$respid1&provider=$provider&location=provider_portal&id=$fid1') ><span>".htmlspecialchars( xl('Audit Form'), ENT_NOQUOTES)."</span></a><br>";

                                                            }else if($frow_enc['elec_signedby']!='' || $frow_enc['elec_signed_on']!=''){

                                                                
                                                                $fname_comp_issue.=$order."<a href='javascript:;' onclick=win1('auditform/report.php?encounter=$resenc1&pid=$respid1&id=$fid1&provider=$provider&location=provider_portal') ><span>".htmlspecialchars( xl('Audit Form'), ENT_NOQUOTES)."</span></a><br>";
                                                            }
                                                        }else if($priority5=='Optional'){
                                                              if($frow_enc['elec_signedby']=='' || $frow_enc['elec_signed_on']==''){

                                                                  $fname_opt_issue_comp.=$order."<a href='javascript:;' onclick=win1('auditform/report.php?encounter=$resenc1&pid=$respid1&provider=$provider&location=provider_portal&id=$fid1') ><span>".htmlspecialchars( xl('Audit Form'), ENT_NOQUOTES)."</span></a><br>";

                                                            }else if($frow_enc['elec_signedby']!='' || $frow_enc['elec_signed_on']!=''){
                                                                 
                                                                 $fname_opt_issue.=$order."<a href='javascript:;' onclick=win1('auditform/report.php?encounter=$resenc1&pid=$respid1&id=$fid1&provider=$provider&location=provider_portal') ><span>".htmlspecialchars( xl('Audit Form'), ENT_NOQUOTES)."</span></a><br>";
                                                            }
                                                        }
                                                     }else{
                                                         if($priority5=='Required'){ 
                                                            
                                                                $fid1=0;
                                                                
                                                                $req_not_started_issue.=$order."<a href='javascript:;' onclick=win1('auditform/report.php?encounter=$resenc1&pid=$respid1&id=$fid1&provider=$provider&location=provider_portal') ><span>".htmlspecialchars( xl('Audit Form'), ENT_NOQUOTES)."</span></a><br>";
                                                            
                                                        }else if($priority5=='Optional'){
                                                                $fid1=0;
                                                                 
                                                                 $opt_not_started_issue.=$order."<a href='javascript:;' onclick=win1('auditform/report.php?encounter=$resenc1&pid=$respid1&id=$fid1&provider=$provider&location=provider_portal') ><span>".htmlspecialchars( xl('Audit Form'), ENT_NOQUOTES)."</span></a><br>";
                                                          
                                                        }
                                                     }
                                                     
                                          }else if($field_id5=='cpo'){
                                               $resid_vt1=sqlStatement("select *  from forms where form_name  ='Procedure Order' AND encounter=$resenc1 AND pid=$respid1  AND deleted=0 ORDER BY id DESC");  
                                                     $frow_vt1 = sqlFetchArray($resid_vt1);
                                                     $fid1=$frow_vt1['form_id'];
                                                     $res2=sqlstatement("select form_id  from forms where form_name ='Allcare Encounter Forms' AND encounter='$resenc1' AND pid='$respid1' AND deleted=0 order by id desc");
                                                     $frow_res2 = sqlFetchArray($res2);
                                                      if(!empty($frow_res2)){
                                                         $formid1=$frow_res2['form_id'];
                                                         
                                                     }else {
                                                         $formid1=0;
                                                     }
                                                     $res1=sqlstatement("select * from lbf_data lb INNER JOIN layout_options l ON l.field_id=lb.field_id where lb.form_id='$formid1' AND l.form_id='LBF2' AND l.group_name LIKE '%PProcedure%' AND lb.field_id LIKE 'procedure_stat' order by seq");
                                                     $res_row1=sqlFetchArray($res1);
                                                     $field_val=explode("|",$res_row1['field_value']);
                                                     if($fid1!=''){
                                                        
                                                        if($priority5=='Required'){
                                                            if(in_array('pending',$field_val) && in_array('finalized',$field_val)){
                                                                 $fname_comp_issue.=$order."<a href='javascript:;' onclick=win1('procedure_order/new.php?encounter=$resenc1&pid=$respid1&id=$fid1&provider=$provider&location=provider_portal&formid=$formid1') ><span>".htmlspecialchars( xl('Procedure Order'), ENT_NOQUOTES)."</span></a><br>";
                                                            }
                                                            elseif(in_array('pending',$field_val) ){

                                                                  $fname_issue.=$order."<a href='javascript:;' onclick=win1('procedure_order/new.php?encounter=$resenc1&pid=$respid1&provider=$provider&location=provider_portal&formid=$formid1') ><span>".htmlspecialchars( xl('Procedure Order'), ENT_NOQUOTES)."</span></a><br>";

                                                            }else if(in_array('finalized',$field_val)) {

                                                               
                                                                $fname_comp_issue.=$order."<a href='javascript:;' onclick=win1('procedure_order/new.php?encounter=$resenc1&pid=$respid1&id=$fid1&provider=$provider&location=provider_portal&formid=$formid1') ><span>".htmlspecialchars( xl('Procedure Order'), ENT_NOQUOTES)."</span></a><br>";
                                                            }else {
                                                                $req_not_started_issue.=$order."<a href='javascript:;' onclick=win1('procedure_order/new.php?encounter=$resenc1&pid=$respid1&id=$fid1&provider=$provider&location=provider_portal&formid=$formid1') ><span>".htmlspecialchars( xl('Procedure Order'), ENT_NOQUOTES)."</span></a><br>";
                                                            }
                                                        }else if($priority5=='Optional'){
                                                             if(in_array('pending',$field_val) && in_array('finalized',$field_val) ){
                                                                  $fname_opt_issue_comp.=$order."<a href='javascript:;' onclick=win1('procedure_order/new.php?encounter=$resenc1&pid=$respid1&provider=$provider&location=provider_portal&formid=$formid1') ><span>".htmlspecialchars( xl('Procedure Order'), ENT_NOQUOTES)."</span></a><br>";
                                                             }
                                                             elseif(in_array('pending',$field_val)){

                                                                  $fname_opt_issue_comp.=$order."<a href='javascript:;' onclick=win1('procedure_order/new.php?encounter=$resenc1&pid=$respid1&provider=$provider&location=provider_portal&formid=$formid1') ><span>".htmlspecialchars( xl('Procedure Order'), ENT_NOQUOTES)."</span></a><br>";

                                                            }else if(in_array('finalized',$field_val)){
                                                                 
                                                                 $fname_opt_issue.=$order."<a href='javascript:;' onclick=win1('procedure_order/new.php?encounter=$resenc1&pid=$respid1&id=$fid1&provider=$provider&location=provider_portal&formid=$formid1') ><span>".htmlspecialchars( xl('Procedure Order'), ENT_NOQUOTES)."</span></a><br>";
                                                            }else{
                                                                $opt_not_started_issue.=$order."<a href='javascript:;' onclick=win1('procedure_order/new.php?encounter=$resenc1&pid=$respid1&id=$fid1&provider=$provider&location=provider_portal&formid=$formid1') ><span>".htmlspecialchars( xl('Procedure Order'), ENT_NOQUOTES)."</span></a><br>";
                                                            }
                                                        }
                                                     }
                                                    
                                          }                  
                                         }
                                         if($fname_issue!=''){
                                             $fname2.="<b>".$codegrpname4.":</b><br>".$fname_issue;
                                         }
                                         if($fname_opt_issue!=''){
                                             $fname_opt2.="<b>".$codegrpname4.":</b><br>".$fname_opt_issue;
                                         }
                                         if($fname_comp_issue!=''){
                                             $fname_comp2.="<b>".$codegrpname4.":</b><br>".$fname_comp_issue;
                                         }
                                         if($fname_opt_issue_comp!=''){
                                             $fname_opt_comp2.="<b>".$codegrpname4.":</b><br>".$fname_opt_issue_comp;
                                         }
                                         if($opt_not_started_issue!=''){
                                             $opt_not_started2.="<b>".$codegrpname4.":</b><br>".$opt_not_started_issue;
                                         }
                                         if($req_not_started_issue!=''){
                                             $req_not_started2.="<b>".$codegrpname4.":</b><br>".$req_not_started_issue;
                                         }
                                     }
//                                     else if(substr($fuv_row1['screen_group'],1)=='Hyperlink'){
//                                         
//                                         
//                                     }
                                    
                                 }
                               
                            
                                   $fname1=rtrim($fname2,",");
                                   $fname_comp1=rtrim($fname_comp2,",");
                                   $fname_opt1=rtrim($fname_opt2,",");
                                   $fname_opt_comp1=rtrim($fname_opt_comp2,",");
                                   $req_not_started1=rtrim($req_not_started2,",");
                                   $opt_not_started1=rtrim($opt_not_started2,",");
                                   
//                                    $query="select CONCAT(p.fname,' ',p.lname) AS Patient_Name,p.sex AS Gender,DATE_FORMAT(f.date, '%Y-%m-%d') as date1,f.encounter AS Encounter,f.elec_signedby,f.elec_signed_on,f.pid,e.pc_facility,e.pc_aid,e.pc_catid,f.pc_catid,f.facility_id AS Facility_Name ,f.provider_id from form_encounter f  INNER JOIN patient_data p ON p.pid=f.pid LEFT JOIN openemr_postcalendar_events e ON e.pc_pid=f.pid and e.pc_eventDate=DATE_FORMAT(f.date, '%Y-%m-%d') AND e.pc_catid=f.pc_catid  where  f.encounter=$resenc1 AND f.pid=$respid1 ";
                                    $sqlres=sqlStatement("SELECT * from tbl_allcare_providers_fieldsorder where username='$provider' AND menu='incomplete_encouner'");
                                    $rowres=sqlFetchArray($sqlres);
                                    if(!empty($rowres)){
                                         $field_val=''; $req_comp=''; $req_incomp=''; $opt=''; $audit='';
                                         $orders=explode(",",$rowres['order_of_columns']);
                                        
                                           $field1='';  $fields=array();
                                            foreach($orders as $value2){
                                                $field=explode(":",$value2);
                                                 $title=str_replace("_"," ",$field[1]);
                                                 
                                                 $sqlId=sqlStatement("select * from list_options where list_id='AllCareProviderIncomplete' AND title='$title' order by seq");
                                               
                                                 while($rowId=sqlFetchArray($sqlId)){
                                                     $col_names=$rowId;
                                                      $field13=$rowId['option_id'];
                                                     if($field13=='patient_name'){
                                                         $field_val.="CONCAT(p.fname,' ',p.lname) AS ".$field13.",";
                                                     }else if($field13=='gender'){
                                                         $field_val.="p.sex AS ".$field13.",";
                                                     }else if($field13=='facility'){
                                                         $field_val.=" f.facility_id AS ".$field13.",";
                                                     }else if($field13=='visit_category'){
                                                         $field_val.=" f.pc_catid as ".$field13.",";
                                                     }else if($field13=='encounter'){
                                                          $field_val.=" f.encounter  as ".$field13.",";
                                                     }elseif($field13=='dos'){
                                                          $field_val.=" DATE_FORMAT(f.date, '%Y-%m-%d') as ".$field13.",";
                                                     }elseif($field13=='pid'){
                                                          $field_val.=" f.pid as ".$field13.",";
                                                     }else if($field13=='req_forms_comp'){
                                                         $req_comp='req_forms_comp';
                                                     }else if($field13=='req_forms_incomplete'){
                                                         $req_incomp='req_forms_incomplete';
                                                     }else if($field13=='optional_forms'){
                                                         $opt='optional_forms';
                                                     }else if($field13=='audit_status'){
                                                          $audit='audit_status';
                                                     }else if($field13=='optional_forms_comp'){
                                                         $opt_comp='optional_forms_comp';
                                                     }else if($field13=='audit_note'){
                                                          $audit_note='audit_note';
                                                     }else if($field13=='req_forms_notstarted'){
                                                          $req_notstarted='req_forms_notstarted';
                                                     }else if($field13=='opt_forms_notstarted'){
                                                          $opt_notstarted='opt_forms_notstarted';
                                                     }
                                                 }
                                                
                                             }
                                            
                                             $field_val1=rtrim($field_val,","); 
                                            $query="select ".$field_val1.", f.elec_signedby,f.elec_signed_on,f.pid,e.pc_facility,e.pc_aid,e.pc_catid,f.provider_id from form_encounter f  INNER JOIN patient_data p ON p.pid=f.pid LEFT JOIN openemr_postcalendar_events e ON e.pc_pid=f.pid and e.pc_eventDate=DATE_FORMAT(f.date, '%Y-%m-%d') AND e.pc_catid=f.pc_catid  where  f.encounter=$resenc1 AND f.pid=$respid1 ";
                                             $sql = sqlStatement($query);
                                    $frow3 = sqlFetchArray($sql);
                                    if(!empty($frow3)){
                                        if($frow3['elec_signedby']=='' && $frow3['elec_signed_on']==''){
                                            $frow3['finalized_val']='not_finalized';
                                        } else {
                                            $frow3['finalized_val']='finalized';
                                        }
                                        $frow3[$req_comp]=$fname_comp1;
                                        $frow3[$req_incomp]=$fname1;
                                        $frow3[$opt]=$fname_opt1;
                                        $frow3[$opt_comp]=$fname_opt_comp1;
                                        $frow3[$req_notstarted]=$req_not_started1;
                                        $frow3[$opt_notstarted]=$opt_not_started1;
                                        
                                           //echo '<pre>';print_r($frow3); echo '</pre>';
                                        $sql1=sqlStatement("select * from forms where form_name='Audit Form' AND deleted=0 AND encounter='$resenc1'");
                                        $audit_res=sqlFetchArray($sql1);
                                             if(empty($audit_res)){
                                              $frow3[$audit]='Incomplete';
                                               $sql_note = sqlStatement("select fe.audit_note from patient_data p INNER JOIN  form_encounter fe ON fe.pid=p.pid where  fe.encounter='".$resenc1."'");
                                                        $frow2_note = sqlFetchArray($sql_note);
                                                        $frow3[$audit_note]=$frow2_note['audit_note'];
                                             } else {
                                                  $sql2=sqlStatement("select * from tbl_form_audit where id='".$audit_res['form_id']."'");
                                                  $audit_st = sqlFetchArray($sql2);
                                                  if(!empty($audit_st)){
                                                      $sql = sqlStatement("select CONCAT(p.fname,' ',p.lname) AS Patient_Name,p.sex AS Gender,f.encounter AS Encounter,fe.audited_status,fe.audit_note from forms f INNER JOIN patient_data p ON p.pid=f.pid  INNER JOIN  form_encounter fe ON fe.encounter=f.encounter where form_name='Audit Form' AND deleted=0 AND f.encounter='".$resenc1."'");
                                                      $frow2 = sqlFetchArray($sql);
                                                      if(!empty($frow2) && $frow2['audited_status']=='Completed'){
                                                        $frow3[$audit]='Complete';
                                                        $frow3[$audit_note]=$frow2['audit_note'];
                                                      }else {

                                                         $frow3[$audit]='Incomplete';
                                                         $frow3[$audit_note]=$frow2['audit_note'];
                                                      }
                                                  }
                                             }   
                                    }         
                                    }else{
                                        $field_val='';
                                         $sqlId=sqlStatement("select * from list_options where list_id='AllCareProviderIncomplete'  order by seq");
                                                
                                                 while($rowId=sqlFetchArray($sqlId)){
                                                     $field13=$rowId['option_id'];
                                                     if($field13=='patient_name'){
                                                         $field_val.="CONCAT(p.fname,' ',p.lname) AS ".$field13.",";
                                                     }else if($field13=='gender'){
                                                         $field_val.="p.sex AS ".$field13.",";
                                                     }else if($field13=='facility'){
                                                         $field_val.=" f.facility_id AS ".$field13.",";
                                                     }else if($field13=='visit_category'){
                                                         $field_val.=" f.pc_catid as ".$field13.",";
                                                     }else if($field13=='encounter'){
                                                          $field_val.=" f.encounter  as ".$field13.",";
                                                     }elseif($field13=='dos'){
                                                          $field_val.=" DATE_FORMAT(f.date, '%Y-%m-%d') as ".$field13.",";
                                                     }elseif($field13=='pid'){
                                                          $field_val.=" f.pid as ".$field13.",";
                                                     }else if($field13=='req_forms_comp'){
                                                         $req_comp='req_forms_comp';
                                                     }else if($field13=='req_forms_incomplete'){
                                                         $req_incomp='req_forms_incomplete';
                                                     }else if($field13=='optional_forms'){
                                                         $opt='optional_forms';
                                                     }else if($field13=='audit_status'){
                                                          $audit='audit_status';
                                                     }else if($field13=='optional_forms_comp'){
                                                         $opt_comp='optional_forms_comp';
                                                     }else if($field13=='req_forms_notstarted'){
                                                          $req_notstarted='req_forms_notstarted';
                                                     }else if($field13=='opt_forms_notstarted'){
                                                          $opt_notstarted='opt_forms_notstarted';
                                                     }
                                                 }
                                        $field_val1=rtrim($field_val,","); 
                                        $query="select ".$field_val1." ,f.elec_signedby,f.elec_signed_on,e.pc_facility,e.pc_aid,e.pc_catid,f.provider_id from form_encounter f  INNER JOIN patient_data p ON p.pid=f.pid LEFT JOIN openemr_postcalendar_events e ON e.pc_pid=f.pid and e.pc_eventDate=DATE_FORMAT(f.date, '%Y-%m-%d') AND e.pc_catid=f.pc_catid  where  f.encounter=$resenc1 AND f.pid=$respid1 ";
                                        $sql = sqlStatement($query);
                                            $frow3 = sqlFetchArray($sql);
                                            if(!empty($frow3)){
                                                if($frow3['elec_signedby']=='' && $frow3['elec_signed_on']==''){
                                                    $frow3['finalized_val']='not_finalized';
                                                } else {
                                                    $frow3['finalized_val']='finalized';
                                                }
                                                $frow3[$req_comp]=$fname_comp1;
                                                $frow3[$req_incomp]=$fname1;
                                                $frow3[$opt]=$fname_opt1;
                                                $frow3[$opt_comp]=$fname_opt_comp1;
                                                $frow3[$req_notstarted]=$req_not_started1;
                                                $frow3[$opt_notstarted]=$opt_not_started1;
                                                
                                                   //echo '<pre>';print_r($frow3); echo '</pre>';
                                                $sql1=sqlStatement("select * from forms where form_name='Audit Form' AND deleted=0 AND encounter='$resenc1'");
                                                $audit_res=sqlFetchArray($sql1);
                                                     if(empty($audit_res)){
                                                        $frow3[$audit]='Incomplete';
                                                        $sql_note = sqlStatement("select fe.audit_note from patient_data p INNER JOIN  form_encounter fe ON fe.pid=p.pid where  fe.encounter='".$resenc1."'");
                                                        $frow2_note = sqlFetchArray($sql_note);
                                                        $frow3[$audit_note]=$frow2_note['audit_note'];
                                                     } else {
                                                          $sql2=sqlStatement("select * from tbl_form_audit where id='".$audit_res['form_id']."'");
                                                          $audit_st = sqlFetchArray($sql2);
                                                          if(!empty($audit_st)){
                                                              $sql = sqlStatement("select CONCAT(p.fname,' ',p.lname) AS Patient_Name,p.sex AS Gender,f.encounter AS Encounter,fe.audited_status,fe.audit_note from forms f INNER JOIN patient_data p ON p.pid=f.pid  INNER JOIN  form_encounter fe ON fe.encounter=f.encounter where form_name='Audit Form' AND deleted=0 AND f.encounter='".$resenc1."'");
                                                              $frow2 = sqlFetchArray($sql);
                                                              if(!empty($frow2) && $frow2['audited_status']=='Completed'){
                                                                $frow3[$audit]='Complete';
                                                                $frow3[$audit_note]=$frow2['audit_note'];
                                                              }else {

                                                                 $frow3[$audit]='Incomplete';
                                                                $frow3[$audit_note]=$frow2['audit_note'];
                                                              }
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
                <link rel="stylesheet" type="text/css" href="assets/css/customize.css">
		<link href='http://fonts.googleapis.com/css?family=Roboto+Condensed:400,300' rel='stylesheet' type='text/css'>
                <link rel="stylesheet" href="css/version1.0/dataTables.bootstrap.min.css"/>
                <link rel="stylesheet" href="css/version1.0/responsive.bootstrap.min.css"/>
                <link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/themes/redmond/jquery-ui.css" /> 
                <link rel="stylesheet" href="css/pqselect.min.css"/>
                <script src="js/responsive_datatable/version1.0/jquery-1.11.3.min.js"></script>
                <script src="https://code.jquery.com/ui/1.10.2/jquery-ui.js"></script>
                <script src="js/pqselect.min.js"></script>
                <script src="js/responsive_datatable/version1.0/jquery.dataTables.min.js"></script>
                <script src="js/responsive_datatable/version1.0/dataTables.bootstrap.min.js"></script>
                <script src="js/responsive_datatable/version1.0/dataTables.responsive.min.js"></script>
                <script type='text/javascript' src='js/responsive_datatable/dataTables.tableTools.js'></script>
                <script type='text/javascript' src='js/responsive_datatable/dataTables.bootstrap.js'></script>
                <style>

                    @media screen and (max-width: 767px) {

                          #column{
                            width:185px !important;
                        }
                        /*a#toggle { display: block; }*/

                        main#content {
                          margin-top: 65px;
                          transition: all ease-out 0.3s;
                        }
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
                        //font-weight: bold;
                        color: black;
                        text-align:left;
                    }
                        ul#slippylist li.slip-reordering {
                            box-shadow: 0 2px 10px rgba(0,0,0,0.45);
                        }
                       /* .costmizecolumns{
                            display: block !important;
                            margin: auto !important;
                            padding-bottom: 10px !important;
                            width: 200px !important;
                            position: relative;
                            top: 31px;
                            z-index: 1;
                        }
                        @media only screen and (max-width: 768px) {
                            .costmizecolumns{
                                top:0px;
                            }
                        }*/
                       .DTTT.btn-group{
                            float: right;
                            padding-left: 13px;
                            position: relative;
                        }
                        #vnfFilter1_length{
                            float:left;
                        }
                       .costmizecolumns {
                            margin-bottom: 7px;
                            margin-top: 13px;
                            text-align: center;
                            width:220px;
                            margin-left: 35%;

                        }
                        @media only screen and (max-width: 1024px){
                            .costmizecolumns {
                                margin-left: 28%;
                            }
                        }
                       @media only screen and (min-width: 800px){
                            .costmizecolumns {
                                position: relative;
                                top:33px;
                                margin-right: 113px;
                            }
                        }

                        @media only screen and (max-width: 768px){
                            .DTTT.btn-group{
                                float: none;
                                margin-bottom: 6px;
                                padding-left: 40%;
                                position: relative;
                            }
                            #vnfFilter1_length{
                                float:none;
                            }
                            .costmizecolumns {
                                margin-bottom: 7px;
                                margin-top: 13px;
                                text-align: center;
                                width:auto;
                                margin-left: 0;
                            }
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
                var hiddenField = document.createElement("input");
                hiddenField.setAttribute("type", "hidden");
                hiddenField.setAttribute("name", key);
                hiddenField.setAttribute("value", provider);

                form.appendChild(hiddenField);

               document.body.appendChild(form);
                form.submit();
        }
        </script>       

	</head>

	<body><?php include 'header_nav.php'; ?>
           <script type='text/javascript'>
               function reset(){
                   $("#dropdown_filters").reset();
               }
             function submitme2() {
          
                            
                var string1='';
                $( ".order" ).each(function( index ) {
                  console.log( index + ": " + $( this ).text() );
                  //order =+index + ": " + $( this ).text() + ",";
                  string1 += index + ":" +$( this ).text()+",";
                 });

                // alert(string1);
                            //var string=string1.trim(",");
                 var string= string1.substring(0,string1.lastIndexOf(","));
                 
                 document.getElementById("col").value = string;  
                 document.getElementById("menu_val").value = 'incomplete_encouner';  
//                 var  provider1=document.getElementById('menu_val').value;
//                 alert(provider1);
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
       window.open('chartoutput/preview_charts.php?'+datastring,'popup','width=900,height=900,scrollbars=no,resizable=yes');
}

function viewpost1(id,pid1,group){
   var datastring='coid'+'='+id+'&'+'patient_id'+'='+pid1+'&'+'group'+'='+group;
  
       window.open('../interface/reports/print_charts.php?'+datastring,'popup','width=900,height=900,scrollbars=no,resizable=yes');
}

function postValue(enc1,pid1,date2){  
   // alert(enc1+"==="+pid1+"==="+date2); 
    //alert(document.getElementById('finalize_'+enc1).checked);
    if(document.getElementById('finalize_'+enc1).checked){ 
     var finalize_val=document.getElementById('finalize_'+enc1).checked;  //alert(finalize_val); 
       $.ajax({
		type: 'POST',
		url: "../interface/reports/finalize.php",	
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
  
function OpenWindowWithPost(url, windowoption, name, params)
{
 var form = document.createElement("form");
 form.setAttribute("method", "post");
 form.setAttribute("action", url);
 form.setAttribute("target", name);
 for (var i in params){
    
     var input = document.createElement('input');
     input.type = 'hidden';
     input.name = i;
     input.value = params[i];
     form.appendChild(input);
 }
 document.body.appendChild(form);

 window.open("post.htm", name, windowoption);
 form.submit();
 document.body.removeChild(form);
}

function win1(relativeUrl)
{
     var res = relativeUrl.split("?");
     var param2=res[1].split("&");
     var dict = new Array();
     for(var i=0; i<param2.length; i++){
            var param1=param2[i].split("=");
            dict[ param1[0] ] = param1[1];
           
       }
 OpenWindowWithPost(res[0], "width=1000, height=600, left=100, top=100, resizable=yes, scrollbars=yes", "NewFile", dict);
}

  </script>
             <section id= "services">
                <div class= "container-fluid">
		    <div class= "row">
			<div class= "col-lg-12 col-sm-12 col-xs-12" style='padding-top:100px !important;'>

                            <?php $display_style='block' ;
                                    
                                  $resid22="SELECT DISTINCT fe.encounter,fe.pid
                                                FROM form_encounter fe
                                                INNER JOIN patient_data p ON p.pid = fe.pid
                                                INNER JOIN tbl_allcare_facuservisit f ON  `facilities` REGEXP (
                                                fe.facility_id
                                                )
                                                AND  `users` REGEXP (
                                                fe.rendering_provider
                                                )
                                                AND  `visit_categories` REGEXP (
                                                fe.pc_catid
                                                )
                                                INNER JOIN layout_options l ON l.group_name = f.screen_group
                                                AND l.form_id = f.form_id
                                                 ";
                                      
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
                                          $resid22.=  " AND ( fe.provider_id ='".$id['id']."' OR fe.rendering_provider='".$id['id']."')";
                                        }
                                    
                                    if(is_array($patient)){
                                        if(!empty($patient)){
                                        foreach($patient as $value1){
                                            if($value1!=''){
                                             $pid_val4.=$value1.",";
                                            }
                                        }
                                        $pid_val2=trim($pid_val4,',');
                                        if($pid_val2!=''){
                                            $resid22.=  " AND p.pid IN ($pid_val2)";
                                        }
                                        
                                     }
                                    }else {
                                       
                                         if($patient!=''){
                                             $resid22.=  " AND p.pid = $patient";
                                        }
                                        
                                    }
                                    
                                    
                                    if(!empty($enc_stat)){
                                         foreach($enc_stat as $value2){
                                           if($value2!=''){   
                                               if($value2=='1'){
                                                   $resid22.=  " AND fe.elec_signedby !='' AND fe.elec_signed_on !=''";
                                               }
                                               if($value2=='0'){
                                                   $resid22.=  " AND fe.elec_signedby ='' AND fe.elec_signed_on =''";
                                               }
                                           }
                                        }
                                    }
                                    
                                    if($from!='' && $to!=''){
                                       $resid22.=  " AND DATE_FORMAT(fe.date, '%Y-%m-%d') BETWEEN CAST('$from' AS DATE) AND CAST('$to' AS DATE)  ";
                                    }
                                    
                                    //facility
                                    
                                    
                                    if(!empty($facility)){
                                        foreach($facility as $value3){
                                            if($value3!=''){
                                             $fac_val.=$value3.",";
                                            }
                                        }
                                        
                                       $fac_val21=trim($fac_val,',');
                                      
                                        if($fac_val21!=''){
                                            $resid22.=  " AND fe.facility_id IN ($fac_val21)";
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
                                            $resid22.=  " AND fe.pc_catid IN ($cat_val1)";
                                        }
                                    }
                                   

                                  
                                   // echo $resid22;
                                    $resid23=sqlStatement($resid22);
                                    $rowcount=mysql_num_rows($resid23);     
                                    ?>

                            <input type='checkbox' name='filter' id='filter' value='1' onclick='return divclick(this,"filters");'   <?php if ($display_style == 'block') echo " checked"; ?>><b>Filters</b>
                            <div id='filters' class="appointment1" style='display:<?php echo $display_style; ?>'>
                              <form name="dropdown_filters" id="dropdown_filters" action="provider_incomplete_charts.php" method="POST" >
                                  <input type='hidden'  id='mode1' name='mode1' value='add'/>
                                  <table cellspacing='5' cellpadding='5'>
                                      <tr style='border-spacing:5em !important;'>
                                       <td id="title1"><?php xl('Fee Provider','e'); ?>:</td>
                                        <td id="field1">
                                          <?php

                                                // Build a drop-down list of providers.
                                                //

                                                $query = "SELECT id, lname, fname FROM users WHERE ".
                                                  "authorized = 1 AND id=$id1 ORDER BY lname, fname"; //(CHEMED) facility filter

                                                $ures = sqlStatement($query);

                                                echo "   <select name='form_provider'  id='form_provider'>\n";
                                                while ($urow = sqlFetchArray($ures)) {
                                                        $provid = $urow['id'];
                                                        echo "    <option value='$provid'";
                                                        echo " selected";
                                                        echo ">" . $urow['lname'] . ", " . $urow['fname'] . "\n";
                                                }

                                                echo "   </select>\n";

                                                ?>
                                        </td>
                                        <td id="title1"><?php xl('Rendering Provider','e'); ?>:</td>
                                        <td id="field1">
                                          <?php

                                                // Build a drop-down list of providers.
                                                //

                                                $query = "SELECT id, lname, fname FROM users WHERE ".
                                                  "authorized = 1 AND id=$id1 ORDER BY lname, fname"; //(CHEMED) facility filter

                                                $ures = sqlStatement($query);

                                                echo "   <select name='form_provider'  id='form_provider'>\n";
                                                while ($urow = sqlFetchArray($ures)) {
                                                        $provid = $urow['id'];
                                                        echo "    <option value='$provid'";
                                                        echo " selected";
                                                        echo ">" . $urow['lname'] . ", " . $urow['fname'] . "\n";
                                                }

                                                echo "   </select>\n";

                                                ?>
                                        </td>
                                        <td id="title1"><?php xl('Patient','e'); ?>:</td>
                                        <td id="field1">
                                          <?php
                                                 
                                                $query="SELECT fe.pid, lname, fname from form_encounter fe  INNER JOIN patient_data p ON p.pid=fe.pid   "
                                                                  . "  where fe.rendering_provider ='".$id['id']."' group by fe.pid  ORDER BY lname, fname ";
                                                                     
                                                $ures = sqlStatement($query);
                                                
                                                echo "   <select name='form_patient[]'  multiple  id='form_patient'>\n";
                                                echo "    <option value=''"; if(!empty($patient)){ foreach($patient as $val2) { if ($val2=='') echo " selected";}  }
                                                echo  "selected"; echo" >-- " . xl('All') . " --\n";

                                                while ($urow = sqlFetchArray($ures)) {
                                                        $pid1 = $urow['pid'];
                                                        echo "    <option value='$pid1'";
                                                        if(!empty($patient) && is_array($patient)){
                                                        foreach($patient as $val2){    
                                                        if ($pid1 == $val2) echo " selected";} }
                                                        else{
                                                             if ($pid1 == $patient) echo " selected";
                                                        }
                                                        echo ">" . $urow['fname'] . ", " . $urow['lname'] . "\n";
                                                }

                                                echo "   </select>\n";

                                                ?>
                                        </td>
                                      
                                       
                                        <td id="title2"><?php xl('Audit Status','e'); ?>:</td>
                                        <td id="field2">
                                          <?php
                                                echo "<select name='form_audit_stat[]'  multiple  id='form_audit_stat'>\n";
                                                echo "    <option value=''"; if(!empty($audit_stat)){ foreach($audit_stat as $val8){if($val8=='') echo "selected";  } }
                                                echo  "selected"; echo" >-- " . xl('All') . " --\n";
                                                echo "<option value='1'"; if(!empty($audit_stat)){ foreach($audit_stat as $val8){if($val8=='1') echo "selected"; } } echo ">Complete</option>";     
                                                echo "<option value='0'"; if(!empty($audit_stat)){ foreach($audit_stat as $val8){if($val8=='0') echo "selected"; } } echo">Incomplete</option>";
                                                echo "   </select>\n";

                                                ?>
                                            </td>
                                      </tr><tr><td>&nbsp;&nbsp;&nbsp;&nbsp;</td></tr><tr>
                                            <td id="title3"><?php xl('Encounter Status','e'); ?>:</td>
                                            <td id="field3">
                                              <?php
                                                    echo "   <select name='form_enc_stat[]'  multiple  id='form_enc_stat'>\n";
                                                    echo "    <option value=''";  if(!empty($enc_stat)){ foreach($enc_stat as $val2){if($val2=='') echo "selected"; } }
                                                     echo" >-- " . xl('All') . " --\n";
                                                    echo "<option value='1'"; if(!empty($enc_stat)){ foreach($enc_stat as $val2){if($val2=='1') echo "selected"; } } echo ">Complete</option>";     
                                                    echo "<option value='0'"; if(!empty($enc_stat)){ foreach($enc_stat as $val2){if($val2=='0') echo "selected"; } }else { echo  "selected"; }echo">Incomplete</option>";
                                                    echo "   </select>\n";

                                                    ?>
                                            </td>
                                      
                                          
                                              <td id="title4"><?php xl('From','e'); ?>:</td>
                                              <td id="field4"><input type='text' name='form_from_date' id="form_from_date"
                                                    size='10' value='<?php echo $from ?>'
                                                    onkeyup='datekeyup(this,mypcc)' onblur='dateblur(this,mypcc)'
                                                    title='yyyy-mm-dd'> <img src='../interface/pic/show_calendar.gif'
                                                    align='absbottom' width='24' height='22' id='img_from_date'
                                                    border='0' alt='[?]' style='cursor: pointer'
                                                    title='<?php xl('Click here to choose a date','e'); ?>'></td>
                                              <td id="title5"><?php xl('To','e'); ?>:</td>
                                              <td id="field5"><input type='text' name='form_to_date' id="form_to_date"
                                                    size='10' value='<?php echo $to ?>'
                                                    onkeyup='datekeyup(this,mypcc)' onblur='dateblur(this,mypcc)'
                                                    title='yyyy-mm-dd'> <img src='../interface/pic/show_calendar.gif'
                                                    align='absbottom' width='24' height='22' id='img_to_date'
                                                    border='0' alt='[?]' style='cursor: pointer'
                                                    title='<?php xl('Click here to choose a date','e'); ?>'></td></tr>
                                              <td id="title6"><?php xl('Facility','e'); ?>:</td>
                                              
                                              <td id="field6"><?php if(!empty($facility)){$facility1=implode("|",$facility); }  facility_list1(strip_escape_custom($facility1), 'form_facility1[]' ,'form_facility1',true); ?></td>
                                      
                                      <td class='bold' nowrap id="title7"><?php echo xlt('Visit Category:'); ?></td>
                                      <td class='text' id="field7">
                              <select name='pc_catid[]' multiple id='pc_catid'>
                                <option value='' selected >-- <?php echo xlt('ALL'); ?> --</option>
                                    <?php
                                     $cres = sqlStatement("SELECT pc_catid, pc_catname " .
                                      "FROM openemr_postcalendar_categories  ORDER BY pc_catname");
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
                            
                             <td id="title8">
                                     <?php xl('Column Names:','e'); ?></td><td id="field8"><div  id='column' style='min-height: 200px; width:200px; overflow-y: scroll; border: 1px solid black;' >
                                      <ul id="slippylist">
                                     <?php 
                                      $sql_list=sqlStatement("SELECT * FROM `list_options`  where list_id='AllCareProviderIncomplete' order by seq");
                                          while($row_list=sqlFetchArray($sql_list)){
                                              $lists[]=$row_list['option_id'];
                                          }
                                           $sql_vis=sqlStatement("SELECT provider_incomp from tbl_user_custom_attr_1to1 where userid='".$id['id']."'");
                                           $row1_vis=sqlFetchArray($sql_vis);
                                            if(!empty($row1_vis)) {
                                                  $avail3=explode("|",$row1_vis['provider_incomp']);
                                                 //echo "<pre>"; print_r($avail3); echo "</pre>";
                                                 foreach($avail3 as $val6){
                                                        if(in_array($val6, $lists)){
                                                            $available1[]=$val6;
                                                        }

                                                    }
                                                    
                                                  $sql1=sqlStatement("SELECT * from tbl_allcare_providers_fieldsorder where username='$provider' AND menu='incomplete_encouner'");
                                                  $row1=sqlFetchArray($sql1);
                                                  if(!empty($row1)){
                                                       $orders=explode(",",$row1['order_of_columns']);
                                                       $field1='';  $fields=array();
                                                        foreach($orders as $value2){
                                                            $field=explode(":",$value2); 
                                                            $title=str_replace("_"," ",$field[1]);
                                                             $sql=sqlStatement("select * from list_options where list_id='AllCareProviderIncomplete' AND title='$title' order by seq"); 
                                                             $row=sqlFetchArray($sql);
                                                             $available[]=$row[option_id];
                                                            if(in_array($row['option_id'], $available1) && $row['option_id']!='provider'){ ?>
                                                            <li class='order'><?php echo str_replace(" ", "_",$field[1]); ?></li>
                                                            <?php } }
                                                            $diff=array_diff($avail3,$available);
                                                            
                                                 }else{
                                                      $sql=sqlStatement("select * from list_options where list_id='AllCareProviderIncomplete'  order by seq"); 
                                                  while($row=sqlFetchArray($sql)){ 
                                                       if(in_array($row['option_id'], $available1) && $row['option_id']!='provider'){ ?>
                                                      <li class='order'><?php echo str_replace(" ", "_",$row['title']); ?></li>
                                                       <?php } }
                                                  } 
                                                  
                                                if(!empty($diff)){
                                                    foreach($diff as $diffval){
                                                        if(in_array($diffval, $available1) && $diffval!='provider' ){ 
                                                           $sql23=sqlStatement("select * from list_options where list_id='AllCareProviderIncomplete' AND option_id='$diffval' order by seq"); 
                                                             $row23=sqlFetchArray($sql23);   ?>
                                                          <li class='order' style="color:red;" ><?php echo str_replace(" ", "_",$row23['title']); ?></li>
                                                        <?php }

                                                     }
                                                }
                                          }  
                                                  
                                           ?>
                                   
                                    
                                   </ul></div>
                                   <script src="js/slip.js"></script>
                                  <script>
                                    var list = document.getElementById('slippylist');
                                    new Slip(list);
                                    list.addEventListener('slip:reorder', function(e) {
                                    e.target.parentNode.insertBefore(e.target, e.detail.insertBefore);
                                  });
                                  </script></td></tr>
                                  <tr><td>&nbsp;</td>
                                         <td><fieldset Style="width:350px;">
                                                 <legend><b>Showing Records:</b></legend>
                                         <b>From:</b> <input type="text" id="lfrom" name="lfrom" value="<?php echo $limit_from; ?>" style="width:50px;" />
                                         <b>To: </b> <input type="text" id="lto" name="lto" value="<?php echo $limit_to; ?>" style="width:50px;"/><?php echo "<b>/Total Records:</b>$rowcount" ; ?>
                                     </fieldset>
                                         </td></tr>
                                  </table>
                                  <input type='hidden' id='provider' name='provider' value='<?php echo $_REQUEST['provider']; ?>' />
                                  <input type='hidden' id='col' name='col' value='' />
                                  <input type='hidden' id='menu_val' name='menu_val' value='' />
                                  <div align='center' id="buttons"> <a href="javascript:;"  class="btn btn-submit btn-sm" onclick="submitme2();">
                                    <span><?php echo htmlspecialchars( xl('Submit'), ENT_NOQUOTES); ?></span>
                                 </a>
                                  <input type="submit" id='def' name='def' value="Default" class="btn btn-submit btn-sm"/></div>
                             </form>
                           </div>
                           
                            
                       <div  style='margin-top:10px'> <!-- start main content div -->
                           <div id="dvLoading1" style="display:none"></div>
                             <div id="div_noform">
                                 <div class="costmizecolumns">
                                     <select id="showhidecolumns" multiple=multiple style="width:220px;">              
                                    </select>
                                </div>
                            <table class='table table-striped table-bordered dt-responsive nowrap'  id='vnfFilter1' cellspacing="0" width="100%">
    
   

                            <?php
//                           
                                     echo "<thead><tr><th>&nbsp;</th>";
                                     $sql_list1=sqlStatement("SELECT * FROM `list_options`  where list_id='AllCareProviderIncomplete' order by seq");
                                          while($row_list1=sqlFetchArray($sql_list1)){
                                              $lists1[]=$row_list1['option_id'];
                                          }
                                     $sql_vis1=sqlStatement("SELECT provider_incomp from tbl_user_custom_attr_1to1 where userid='".$id['id']."'");
                                     $row1_vis1=sqlFetchArray($sql_vis1);
                                     if(!empty($row1_vis1)) {
                                            $avail4=explode("|",$row1_vis1['provider_incomp']);
                                            foreach($avail4 as $val7){
                                                        if(in_array($val7, $lists1)){
                                                            $available6[]=$val7;
                                                        }

                                                    }
                                      $sql12=sqlStatement("SELECT * from tbl_allcare_providers_fieldsorder where username='$provider' AND menu='incomplete_encouner'");
                                      $row12=sqlFetchArray($sql12);
                                      if(!empty($row12)){
                                           $orders1=explode(",",$row12['order_of_columns']);
                                           $field12='';  $fields=array();
                                           foreach($orders as $value21){
                                                 $field12=explode(":",$value21); 
                                                  $title=str_replace("_"," ",$field12[1]);
                                                 $sql=sqlStatement("select * from list_options where list_id='AllCareProviderIncomplete' AND title='$title' order by seq"); 
                                                 $row=sqlFetchArray($sql);
                                                   if($row['option_id']=='patient_name' && in_array($row['option_id'], $available6) && $row['option_id']!='provider'){
                                                           echo "<th  data-class='expand'>".htmlspecialchars( xl($title), ENT_NOQUOTES)."</th>";
                                                   }elseif(in_array($row['option_id'], $available6) && $row['option_id']!='provider'){
                                                           echo "<th data-hide='phone' data-name='$title'>".htmlspecialchars( xl($title), ENT_NOQUOTES)."</th>";
                                                   }
                                            }
                                          
                                      }else{
                                          $sql=sqlStatement("select * from list_options where list_id='AllCareProviderIncomplete' order by seq"); 
                                          while($row=sqlFetchArray($sql)){
                                              if($row['option_id']=='patient_name' && in_array($row['option_id'], $available6) && $row['option_id']!='provider'){
                                                   echo "<th style='width:180px;' data-class='expand'>".htmlspecialchars( xl($row['title']), ENT_NOQUOTES)."</th>";
                                              }elseif(in_array($row['option_id'], $available6) && $row['option_id']!='provider'){
                                                   echo "<th style='width:180px;' data-hide='phone' data-name='$field12[1]'>".htmlspecialchars( xl($row['title']), ENT_NOQUOTES)."</th>";
                                              }
                                          }
                                      }
                                    }
                                      echo "</tr></thead>\n";
                                      $resid21="SELECT DISTINCT fe.encounter,  fe.pid
                                                FROM form_encounter fe
                                                INNER JOIN patient_data p ON p.pid = fe.pid
                                                INNER JOIN tbl_allcare_facuservisit f ON  `facilities` REGEXP (
                                                fe.facility_id
                                                )
                                                AND  `users` REGEXP (
                                                fe.rendering_provider
                                                )
                                                AND  `visit_categories` REGEXP (
                                                fe.pc_catid
                                                )
                                                INNER JOIN layout_options l ON l.group_name = f.screen_group
                                                AND l.form_id = f.form_id
                                                 ";
                                      
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
                                          $resid21.=  " AND ( fe.provider_id ='".$id['id']."' OR fe.rendering_provider='".$id['id']."')";
                                        }
                                    
                                    if(is_array($patient)){
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
                                    }else {
                                        
                                            if($patient!=''){
                                             $resid21.=  " AND p.pid = $patient";
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
                                        
                                       $fac_val21=trim($fac_val,',');
                                      
                                        if($fac_val21!=''){
                                            $resid21.=  " AND fe.facility_id IN ($fac_val21)";
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
                                   
                                    if($limit_from==0){
                                         $resid21.=' LIMIT '. $limit_from ." , ".$limit_to ;
                                    }else {
                                        $limit=$limit_to-$limit_from;
                                         $resid21.=' LIMIT '. $limit ." OFFSET ".$limit_from ;
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
//                                           $resid1=sqlStatement("select count(*) as count from forms where form_name  IN ('Allcare Review Of Systems','Allcare Physical Exam') AND encounter='$value' AND deleted=0 ");  
//                                          while ($resid_row1=sqlFetchArray($resid1)){
                                               
                                                 //if($resid_row1['count']!=0){
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
                                                      $k=0;
                                                      while ($resid_row3=sqlFetchArray($resid3)) {
                                                            
                                                             if ($result1 = VisitsWithNoForms($resid_row3['form_id'],$resid_row3['pid'],$resid_row3['encounter'],$resid_row3['form_name'],$ros,$pe,$allcare,$audit_stat,$provider,$menu,$id1)) {
                                                         //echo "<pre>";print_r($result1); echo "</pre>";
//                                                              
                                                                 echo "<tr height='25'>";
                                                                        echo "<td style='width:600px;'>";
                                                                        if($result1['finalized_val']=='not_finalized'){
                                                                                 $enc=$result1['encounter'];
                                                                                 $pid=$result1['pid'];
                                                                                 $date=$result1['dos']; 
                                                                              ?><a href='javascript:; ' onclick="previewpost1('<?php echo $enc; ?>','<?php echo $pid; ?>','<?php echo $date; ?>')" class='css_button_small'><span><?php echo  htmlspecialchars( xl('Preview'), ENT_NOQUOTES); ?></span></a>|
                                                                                   <!--<input type="checkbox" id="finalize" name="finalize"  >Finalize-->
                                                                                  <?php if($result1['audit_status']=='Complete'){  ?> <input type='checkbox' name='finalize_<?php echo $enc; ?>' id='finalize_<?php echo $enc; ?>' onclick="postValue('<?php echo $enc; ?>','<?php echo $pid; ?>','<?php echo $date; ?> ')"/>Finalize | 
                                                                                  <?php } else if($result1['audit_status']=='Incomplete') {?> <input type='checkbox' name='finalize_<?php echo $enc; ?>' id='finalize_<?php echo $enc; ?>' onclick="postValue('<?php echo $enc; ?>','<?php echo $pid; ?>','<?php echo $date; ?> ')" disabled />Finalize |  <?php } 
                                                                                  echo "<a  class='css_button_small'><span>".
                                                                                    htmlspecialchars( xl('View'), ENT_NOQUOTES)."</span></a>";

                                                                        }else if($result1['finalized_val']=='finalized'){
                                                                                $enc=$result1['encounter'];
                                                                                $pid=$result1['pid'];
                                                                                $date=$result1['dos'];
                                                                                ?><a href='javascript:void(0); ' onclick="previewpost1('<?php echo $enc; ?>','<?php echo $pid; ?>','<?php echo $date; ?>')" class='css_button_small'><span><?php echo  htmlspecialchars( xl('Preview'), ENT_NOQUOTES); ?></span></a>|
                                                                                <?php 

                                                                                if($result1['audit_status']=='Complete'){ ?>
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
                                                                         $sql_vis=sqlStatement("SELECT provider_incomp from tbl_user_custom_attr_1to1 where userid='".$id['id']."'");
                                                                         $row1_vis=sqlFetchArray($sql_vis);
                                                                         $enc=$result1['encounter'];
                                                                          $pid=$result1['pid'];
                                                                    if(!empty($row1_vis)) {
                                                                            $avail3=explode("|",$row1_vis['provider_incomp']);
                                                                        $sql13=sqlStatement("SELECT * from tbl_allcare_providers_fieldsorder where username='$provider' AND menu='incomplete_encouner'");
                                                                        $row13=sqlFetchArray($sql13);
                                                                        if(!empty($row13)){
                                                                               $orders1=explode(",",$row13['order_of_columns']);
                                                                               $field12='';  
                                                                               foreach($orders as $value22){
                                                                                     $value23=explode(":",$value22); 
                                                                                     $title=str_replace("_"," ",$value23[1]);
                                                                                     $sqlId=sqlStatement("select * from list_options where list_id='AllCareProviderIncomplete' AND title='$title' order by seq");
                                                                                     while($rowId=sqlFetchArray($sqlId)){
                                                                                         $field=$rowId['option_id'];
                                                                                         if($field=='facility' && in_array($field, $avail3)){
                                                                                             echo "<td>";if($result1[$field]!='')  $pro_id2=sqlStatement("select name from facility where id='".$result1[$field]."' ");
                                                                                                            $id_row5=sqlFetchArray($pro_id2);
                                                                                                            $proid23=$id_row5['name'];{ echo $proid23; }echo"</td>";
                                                                                         }else if($field=='visit_category' && in_array($field, $avail3)){
                                                                                             echo "<td>"; if($result1[$field]!='') $pro_id1=sqlStatement("select pc_catname from openemr_postcalendar_categories where pc_catid='".$result1[$field]."' ");
                                                                                                            $id_row41=sqlFetchArray($pro_id1);
                                                                                                            $proid1=$id_row41['pc_catname'];{ echo $proid1; }echo"</td>";
                                                                                         }elseif($field=='audit_note' && in_array($field, $avail3)){
                                                                                              echo "<td>"; echo "<form id='audit_note_$enc' name='audit_note_$enc' action='' method='post'> <textarea name='form_audit_note_$enc' id='form_audit_note_$enc' title='' cols='5' rows='10' disabled>$result1[$field]</textarea>"
                                                                                                      . "  <input type='hidden' name='encounter' id='encounter' value='$enc' /> <input type='hidden' name='pid' id='pid' value='$pid' /> </form>"; echo "</td>";
                                                                                         }elseif(in_array($field, $avail3) && $field!='provider'){
                                                                                               echo "<td>".$result1[$field]."</td>";
                                                                                         }
                                                                                       
                                                                                     }
                                                                               }
                                                                        }else{
                                                                            $sql=sqlStatement("select * from list_options where list_id='AllCareProviderIncomplete' order by seq"); 
                                                                              while($row=sqlFetchArray($sql)){
                                                                                   $field=$row['option_id'];
                                                                                 if($field=='facility' && in_array($field, $avail3)){
                                                                                  echo "<td>";if($result1[$field]!='')  $pro_id2=sqlStatement("select name from facility where id='".$result1[$field]."' ");
                                                                                                    $id_row5=sqlFetchArray($pro_id2);
                                                                                                    $proid23=$id_row5['name'];{ echo $proid23; }echo"</td>";
                                                                                 }else if($field=='visit_category' && in_array($field, $avail3)){
                                                                                     echo "<td>"; if($result1[$field]!='') $pro_id1=sqlStatement("select pc_catname from openemr_postcalendar_categories where pc_catid='".$result1[$field]."' ");
                                                                                                    $id_row41=sqlFetchArray($pro_id1);
                                                                                                    $proid1=$id_row41['pc_catname'];{ echo $proid1; }echo"</td>";
                                                                                 }elseif($field=='audit_note' && in_array($field, $avail3)){
                                                                                         echo "<td>"; echo "<form id='audit_note_$enc' name='audit_note_$enc' action='' method='post'> <textarea name='form_audit_note_$enc' id='form_audit_note_$enc' title='' cols='5' rows='10' disabled>$result1[$field]</textarea>"
                                                                                                      . "  <input type='hidden' name='encounter' id='encounter' value='$enc' /> <input type='hidden' name='pid' id='pid' value='$pid' /> </form>"; echo "</td>";
                                                                                 }elseif(in_array($field, $avail3) && $field!='provider'){
                                                                                       echo "<td>".$result1[$field]."</td>";
                                                                                 }
                                                                              }
                                                                        }
                                                                    }    
                                                                    
                                                                 echo "</tr>\n";
                                                             }
                                                       }
                                                // }
                                            // }
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
          
                 <?php include 'footer.php'; ?> 
		<script type="text/javascript" src="assets/js/jquery.min.js"></script>
		<script type="text/javascript" src="assets/js/owl.carousel.min.js"></script>
		<script type="text/javascript" src="assets/js/isotope.pkgd.min.js"></script>
		<script type="text/javascript" src="assets/js/wow.min.js"></script>
		<script type="text/javascript" src="assets/js/bootstrap.min.js"></script>

		<script>
      		new WOW().init();
		</script>

		<script type='text/javascript'>
                        var opttext = new Array();
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
                            windowresize();
                            function windowresize(){
                                $('#showhidecolumns').empty();
                                $('#vnfFilter1 thead tr th').each(function(index,elm){
                                    var optiontext = index==0?"Preview and View":$(elm).text();
                                    opttext.push(optiontext);
                                    $('#showhidecolumns').append("<option data-column='"+index+"'>"+optiontext+"</option>");
                                });
                            }
                            
                            
                            var table = $('#vnfFilter1').DataTable({
                                 dom: 'T<\"clear\">lfrtip',
                                 "iDisplayLength": 100,
//                                 columnDefs: [
//                                    {
//                                        targets: [0],
//                                        visible: false
//                                    }
//                                ],
                               tableTools: {
                                     "sSwfPath": "../interface/swf/copy_csv_xls_pdf.swf",
                                    aButtons: [
                                        {
                                            sExtends: "xls",
                                            sButtonText: "Save to Excel",
                                            //sFileName: $('#openemrTitle').val() + " zirmed patients "+ $('#currTime').val() +".csv"
                                             mColumns: [1, 2, 3, 4,5,6,7,8,9,10,11,12,13,14,15,16,17]
                                        }
                                    ]
                                }
                            });
                            selectedoptions();
                            intlizeselectbox();
                            function selectedoptions(){
                                $('#vnfFilter1 thead tr th').each(function(index,elm){
                                    var selectedcolm = table.column(index);
                                    if(selectedcolm.visible()==true){
                                       $('#showhidecolumns option').eq(index).attr("selected","selected")
                                   }
                                })
                                
                            }
                            $(window).resize(function() {
                                $('#showhidecolumns option').removeAttr("selected");
                                selectedoptions();
                                intlizeselectbox();
                            })
                            function intlizeselectbox(){
                                $("#showhidecolumns").pqSelect({
                                    multiplePlaceholder: 'Show / Hide Columns',
                                    checkbox: true, //adds checkbox to options    
                                    maxDisplay: 0,
                                    search: false,
                                    displayText: "columns {0} of {1} selected"
                                }).on("change", function(evt) {
                                    var val = $(this).val();
                                    $.each(opttext,function(index,elm){
                                        var column = table.column(index);
                                        if(val.indexOf(elm) !=-1)
                                            column.visible(true);
                                        else
                                            column.visible(false);
                                    })
                                });
                               
                            }
                             
                           
                            
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
                  
                   function reposition() {
                        var modal = $(this),
                            dialog = modal.find('.modal-dialog');
                        modal.css('display', 'block');

                        // Dividing by two centers the modal exactly, but dividing by three 
                        // or four works better for larger screens.
                        dialog.css("margin-top", Math.max(0, ($(window).height() - dialog.height()) / 2));
                    }
                    // Reposition when a modal is shown
                    $('.modal').on('show.bs.modal', reposition);
                    // Reposition when the window is resized
                    $(window).on('resize', function() {
                        $('.modal:visible').each(reposition);
                    });

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
