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

$base_url="//".$_SERVER['SERVER_NAME'].dirname($_SERVER["REQUEST_URI"].'?').'/';
include 'section_header.php';

$encounter      = isset($_REQUEST['checkencounter'])? $_REQUEST['checkencounter'] : $_REQUEST['encounter']; 
/* hema */

$providers_string = array();
//print_r($_SESSION);
if(strtolower(trim($_SESSION['see_all_providers'])) == 'yes'){
    $get_providers_list = sqlStatement("SELECT id,CONCAT (fname, lname, mname) as providername,username FROM users WHERE authorized != 0 AND active = 1 AND username <> '' ORDER BY fname,lname");
    while($set_providers_list = sqlFetchArray($get_providers_list)){
        $providername[ $set_providers_list['id']]    = $set_providers_list['providername'];
        $providernames[ $set_providers_list['id']]   = $set_providers_list['username'];
    }
}else if(strtolower(trim($_SESSION['see_all_providers'])) != 'yes' && $_SESSION['isprovider'] == 1){
    $providername[$_SESSION['portal_userid']]   = $_SESSION['portal_userfullname'];
    $providernames[$_SESSION['portal_userid']]  = $_SESSION['portal_username'];
}else if(strtolower(trim($_SESSION['see_all_providers'])) != 'yes' && $_SESSION['isprovider'] != 1){
    $get_providers_list = sqlStatement("SELECT pro_refers FROM tbl_user_custom_attr_1to1 WHERE userid = '".$_SESSION['portal_userid']."' LIMIT 0,1");
    while($set_providers_list = sqlFetchArray($get_providers_list)){
        $providers_string = explode("|",$set_providers_list['pro_refers']);
    }
    foreach ($providers_string as $pkey => $pvalue){
        $get_list_providername = sqlStatement("SELECT CONCAT (fname, lname, mname) as providername, username FROM users WHERE id = '$pvalue' AND username <> ''");
        while($set_list_providername = sqlFetchArray($get_list_providername)){
            $providername[$pvalue] = $set_list_providername['providername'];
            $providernames[$pvalue] = $set_list_providername['username'];
        }
    }
}


$countOf = count($providernames);
if($countOf > 3)
    $countOf = 5;

/*
* af_providers for provider filter
* af_visittype for visit type filter 
*/

$af_visittype = '';
$af_providers = '';

$getdatafilter = sqlStatement("SELECT af_providers,af_visittype FROM tbl_providerportal_filters WHERE userid = '".$_SESSION['portal_userid']."' and screen_name = 'home'");
while($setdatafilter = sqlFetchArray($getdatafilter)){
   $af_visittype = $setdatafilter['af_visittype'];
   $af_providers = $setdatafilter['af_providers'];
}

//$af_providers = '18';
$af_visittype2 = explode(",",$af_visittype);
$af_providers2 = explode(",",$af_providers);

//echo "<pre>";
//print_r($af_providers);
//echo "</pre>";


/* hema code end here */

$sql=sqlStatement("SELECT id, fname, lname, specialty FROM users " .
      "WHERE active = 1 AND ( info IS NULL OR info NOT LIKE '%Inactive%' ) " .
      "AND authorized = 1 AND username='".$provider."'" .
      "ORDER BY lname, fname");

$id = sqlFetchArray($sql);
$id1    = $id['id'];

/* If the logined in user is not provider, then we have to get rendering provider value from incomplete chart filter
 * table for this non provider user
 */
if(empty($id['id'])){
    /* In practice portal we have to get the default rendering provider for a user (openemr user) */
    $check_query       = sqlStatement("select * from incomplete_charts_filter where loginuser='$provider' order by id desc LIMIT 1");
    $set_query        = sqlFetchArray($check_query);
    if($_POST['rendering_provider'] != $set_query['rendering_provider'] && !empty($_POST['rendering_provider'])):
        $id['id'] = $_POST['rendering_provider'];
        $id1 = $_POST['rendering_provider'];
    else:
        $id['id'] = $set_query['rendering_provider'];
        $id1 = $set_query['rendering_provider'];
    endif;
}


//echo "subhan".$id['id'];


//echo "Subhan".$id1;

//to save columns order
$order  = $_POST['col'];

$menu   = $_REQUEST['menu_val'];
if($_SESSION['portal_username']!='' && $order!='' && $menu!=''){
    $sql1   = sqlStatement("SELECT * from tbl_allcare_providers_fieldsorder where username='".$_SESSION['portal_username']."' AND menu='$menu'");
    $row1   = sqlFetchArray($sql1);
    if(empty($row1)){
        $sql = sqlStatement("INSERT INTO `tbl_allcare_providers_fieldsorder`(`username`, `menu`, `order_of_columns`) VALUES ('".$_SESSION['portal_username']."','$menu','$order') ");
    }else{
        $sql = sqlStatement("UPDATE `tbl_allcare_providers_fieldsorder` SET `order_of_columns`='$order' WHERE username='".$_SESSION['portal_username']."' AND menu='$menu'");
    }
}

$limit_from = $_POST['lfrom'] ? $_POST['lfrom'] : 0;
$limit_to   = $_POST['lto']   ? $_POST['lto']   : 25 ;
//print_r($_POST);
if (! $_POST['form_from_date']) {
    $from   = date("Y-m-d", strtotime("-1 months"));  
    $to     = date("Y-m-d");
}
 
if($_POST['def']=='Default'){
    $from   = date("Y-m-d", strtotime("-1 months"));  
    $to         = date("Y-m-d");
    $enc_stat   = array([0]=>0); 
    $facility   = array([0]=>'');
        
    $del=sqlStatement("delete from tbl_allcare_providers_fieldsorder where username='".$_SESSION['portal_username']."' AND menu='incomplete_encouner'");
}else if($_POST['mode1']=='add'){ //echo "add";
  
    $patient    = $_POST['form_patient'];
    $audit_stat = $_POST['form_audit_stat'];
    $enc_stat   = $_POST['form_enc_stat'];
    $from       = $_POST['form_from_date'];
    $to         = $_POST['form_to_date'];
    $facility   = $_POST['form_facility1'];
    $visit_cat  = $_POST['pc_catid'];
    $assigned_by= $_POST['assign_user'];
    
    $feeprovider   = $_REQUEST['fee_provider']; // fee provider
    $rendprovider  = $_REQUEST['rendering_provider']; // rendering provider

    foreach($patient as $key1=>$val1){
        if($val1!=''){
            $patient_val .= $val1."|";
        } 
        $patient_val1 = rtrim($patient_val,"|");
    }
    foreach($audit_stat as $key2=>$val2){
        if($val2!=''){
            $audit_val = $val2."|";
        }
        $audit_val1 = rtrim($audit_val,"|");
    }
    foreach($enc_stat as $key3=>$val3){
        if($val3!=''){
            $enc_val .= $val3."|";
        }
        $enc_val1=rtrim($enc_val,"|");
    }
    foreach($facility as $key4=>$val4){
        if($val4!=''){
           $fac_vals .= $val4."|";
        }
        $fac_val1 = rtrim($fac_vals,"|");
    }
    foreach($visit_cat as $key5=>$val5){
        if($val5!=''){
            $vc_val .= $val5."|";
        }
        $vc_val1 = rtrim($vc_val,"|");
    }
    foreach($assigned_by as $key7=>$val7){
        if($val7!=''){
             $assign_val .= $val7."|";
        }
        $assign_val1 = rtrim($assign_val,"|");
    }

    $check_query       = sqlStatement("select * from incomplete_charts_filter where loginuser='$provider' order by id desc");
    $set_query        = sqlFetchArray($check_query);
    if(empty($set_query)){
        $sql = sqlStatement("INSERT INTO incomplete_charts_filter (provider,patient,audit_status,encounter_status,`from`,`to`,facility,visit_category,loginuser,assigned_by,fee_provider,rendering_provider) values('$id1','$patient_val1','$audit_val1','$enc_val1','$from','$to','$fac_val1','$vc_val1','$provider','$assign_val1','$feeprovider','$rendprovider')");
    }else{
        //echo "UPDATE incomplete_charts_filter SET patient= '$patient_val1',audit_status ='$audit_val1',encounter_status = '$enc_val1',`from`='$from',`to`='$to',facility='$fac_val1',visit_category= '$vc_val1',loginuser='$provider',assigned_by='$assign_val1',fee_provider='$feeprovider',rendering_provider='$rendprovider' WHERE id = '".$set_query['id']."' ";
        $sql = sqlStatement("UPDATE incomplete_charts_filter SET patient= '$patient_val1',audit_status ='$audit_val1',encounter_status = '$enc_val1',`from`='$from',`to`='$to',facility='$fac_val1',visit_category= '$vc_val1',loginuser='$provider',assigned_by='$assign_val1',fee_provider='$feeprovider',rendering_provider='$rendprovider' WHERE id = '".$set_query['id']."' ");
    }
} else{ //echo "add else";
    $selectpatient = $_REQUEST['form_patient'] ? $_REQUEST['form_patient'] : '';
    if($selectpatient != ""){ //echo "if";
        $patient = $_REQUEST['form_patient'];
        //$tmp = date('m')-1;
        if($_REQUEST['form_to_date']!=''){
            $to = $_REQUEST['form_to_date'];
        }else {
            $to = date("Y-m-d");
        }
        
	//$from = date("Y-$tmp-d");
        $from       = date("Y-m-d", strtotime("-1 months"));
        $enc_stat   =array([0] => 0);
        $facility   =array([0] => '');
    }else{ //echo "else";
        $sql1       = sqlStatement("select * from incomplete_charts_filter where loginuser='".$_SESSION['portal_username']."' order by id desc");
        $row        = sqlFetchArray($sql1);
        $patient    = explode("|",$row['patient']);
        $audit_stat = explode("|",$row['audit_status']);
        $enc_stat   = explode("|",$row['encounter_status']);
        
        $feeprovider   = $row['fee_provider']; // fee provider
        $rendprovider  = $row['rendering_provider']; // rendering provider

        if($row['from']=='' && $row['to']==''){
           // $tmp = date('m')-1;
            //$from = date("Y-$tmp-d");
            $sql=sqlStatement("select * from list_options where list_id='IncompleteChartFilters' and title='from_date'");
            $row1=sqlFetchArray($sql);
            if(!empty($row1['notes']))
                $from   = $row1['notes'];
            else
                $from   = date("Y-m-d", strtotime("-1 months"));   
            $to     = date("Y-m-d");
        }else{
            $from   = $row['from'];
            
            $to     = $row['to'];
        }

        $facility   = explode("|",$row['facility']); 

        $visit_cat  = explode("|",$row['visit_category']);
    }
    
}

$encounter      = isset($_REQUEST['checkencounter'])? $_REQUEST['checkencounter'] : $_REQUEST['encounter']; 
$audit_pid      = $_POST['pid'];  
$note           = $_POST['form_audit_note_'.$encounter];
if($encounter!='' && $audit_pid!='' && $note!=''){
    $sql = sqlStatement("UPDATE `form_encounter` SET `audit_note`='$note' WHERE encounter='$encounter' AND pid='$audit_pid'");
}
if($_POST['elec_date'] != ''){
    $encounter     = $_POST['enc1']; 
    $patient_id    = $_POST['pid1']; 
    $dos           = $_POST['date2'];  
    if($_POST['grp']==''){
        $grp    =substr($_POST['cht_grp'],1);
        $grp1   =$_POST['cht_grp'];
    }else{
        $grp    =substr($_POST['grp'],1);
        $grp1   = $_POST['grp'];
    }
    $elec_dt    = trim(str_replace(" ", "",$_POST['elec_date']));
    $elec_dt    = $elec_dt. " 00:00";
    $who_type   =$_REQUEST['who_type'];
    $refer_to   =$_REQUEST['refer_to'];
    $feeprovider   =$_REQUEST['fee_provider']; // fee provider
    $rendprovider   =$_REQUEST['rendering_provider']; // rendering provider
    $facility   =$_REQUEST['form_facility'];
    $pharmacy   =$_REQUEST['form_pharmacy'];
    $payer=$_REQUEST['form_payer'];
    //echo "select provider_id ,DATE_FORMAT(date, '%Y-%m-%d') as date from form_encounter where pid=$patient_id AND encounter=$encounter AND DATE( date )='$dos'";
    $sel = sqlStatement("select provider_id ,DATE_FORMAT(date, '%Y-%m-%d') as date ,rendering_provider from form_encounter where pid=$patient_id AND encounter=$encounter AND DATE( date )='$dos'");
    $provider_id = sqlFetchArray($sel);
    if($_REQUEST['refer_to']!=''){
         $refer_to  = $_REQUEST['refer_to']; 
    }else {
        if($provider_id['provider_id']!='0' && $provider_id['provider_id']!='')
            $refer_to=$provider_id['provider_id']; 
        else if($provider_id['rendering_provider']!='0' && $provider_id['rendering_provider']!='') 
            $refer_to=$provider_id['rendering_provider'];
    }
    //echo "Refer --> ".$refer_to;
    if($refer_to!='0' && $refer_to!='' ) {
        $update = sqlStatement("UPDATE  form_encounter SET elec_signedby=".$refer_to." ,elec_signed_on= '$elec_dt' where pid=$patient_id AND encounter=$encounter ");

        //create transactions
        $lsql = sqlStatement("SELECT * FROM layout_options WHERE form_id = 'CHARTOUTPUT' AND uor > 0 AND field_id != '' AND group_name like '%$grp%' ORDER BY seq");
        while($lrow1 = sqlFetchArray($lsql)){
            $layout_field[] = $lrow1['field_id'];
        }


        $mobile_sql = sqlStatement("SELECT *  
                        FROM  `tbl_chartui_mapping` 
                        WHERE form_id =  'CHARTOUTPUT' 
                        AND group_name LIKE  '%$grp%'
                        AND screen_name LIKE  '%$grp%'");
        while($mob_row1 = sqlFetchArray($mobile_sql)){
            if(in_array($mob_row1['field_id'],$layout_field)){
                $field_id .= $mob_row1['field_id'].",";
                 if($mob_row1['field_id'] == 'homehealth_demographics' ||$mob_row1['field_id'] == 'mobile_demographics'||$mob_row1['field_id'] == 'payeraudit_demographics'||$mob_row1['field_id'] == 'referral_demographics' || $mob_row1['field_id']=='appeal_demographics' ){
                    $field_value.="'".YES."'".",";
                 }else {
                     $field_value .= "'".$mob_row1['option_value']."'".",";
                 }
            }
          }
        $field_id1 = rtrim($field_id,","); 
        $field_value1 = rtrim($field_value,","); 

        $notes      = "Transaction created on ".date("Y/m/d").".";
        $create_transaction = sqlStatement("INSERT INTO tbl_form_chartoutput_transactions (pid,encounter,date_of_service,created_date,chart_group,refer_to,notes,provider,facility,pharmacy,payer,who_type,transaction,trans_type,$field_id1) VALUES($patient_id,$encounter,'$dos',NOW(),'$grp1','$refer_to','$notes','".$_SESSION['portal_username']."','$facility','$pharmacy','$payer','$who_type',1,'Patient Encounter Specific',$field_value1)");

        $get_form_id = sqlStatement("SELECT form_id FROM forms WHERE pid = $patient_id AND encounter = $encounter and formdir='newpatient'");
        while ($get_form_Value = mysql_fetch_array($get_form_id)) {
            $formid = $get_form_Value['form_id'];
        }
        if($formid != 0 || !empty($formid)){
            $logdata = $array = array();
            $data = mysql_query("SELECT logdate from `tbl_allcare_formflag` WHERE  form_id=".$formid." AND form_name = 'Patient Encounter'");
            while ($row = mysql_fetch_array($data,MYSQL_ASSOC)) {
                $array =  unserialize($row['logdate']);
                $count= count($array);
            }
            $count = isset($count)? $count: 0;
            $status = 'Completed';
        //    $pending = $_POST['pending'];
        //    $finalized = $_POST['finalized'];

            $ip_addr=GetIP();
            if(empty($array)):
                $array2[] = array( 'authuser' =>$_SESSION['portal_username'],'Status' => $status,'date' => date("Y/m/d"), 'action'=>'created','ip_address'=>$ip_addr."(provider portal)" ,'count'=> $count+1);
                $logdata = array_merge_recursive($array, $array2);
                $logdata= ($logdata? serialize($logdata): serialize($array2) );
                sqlInsert("INSERT into `tbl_allcare_formflag`(`form_id`, `encounter_id`, `form_name`, `logdate`) VALUES(".$formid.",".$encounter.",'Patient Encounter','".$logdata."')");
            else: 
                $result = mysql_query("SELECT * FROM tbl_allcare_formflag WHERE `form_name` = 'Patient Encounter' AND `form_id` =  ".$formid);
                if(mysql_num_rows($result) > 0){
                    $array2[] = array( 'authuser' =>$_SESSION["portal_username"],'Status' => $status, 'date' => date("Y/m/d"), 'action' => 'updated','ip_address'=>$ip_addr."(provider portal)" ,'count'=> $count+1);
                    $logdata = array_merge_recursive($array, $array2);
                    $logdata= ($logdata? serialize($logdata): serialize($array2) );
                    sqlInsert("UPDATE `tbl_allcare_formflag` SET `logdate` ='".$logdata."'  WHERE `form_name` = 'Patient Encounter' AND `form_id` =  ".$formid);
                }else{ 
                    $array2[] = array( 'authuser' =>$_SESSION["portal_username"],'Status' => $status, 'date' => date("Y/m/d"), 'action'=>'created','ip_address'=>$ip_addr."(provider portal)" ,'count'=> $count+1);
                    $logdata = array_merge_recursive($array, $array2);
                    $logdata= ($logdata? serialize($logdata): serialize($array2) );
                    sqlInsert("INSERT into `tbl_allcare_formflag`(`form_id`, `encounter_id`, `form_name`, `logdate`) VALUES(".$formid.",".$encounter.",'Patient Encounter','".$logdata."')");
                }
            endif;
        }
    }else {
        echo "<script>alert('Please fill provider name in encounter');</script>";
    }
}
//facility
function facility_list1($selected = '', $name = 'form_facility1[]', $id='form_facility1', $allow_unspecified = true, $allow_allfacilities = true,$af_providers) {
  $sel_value=explode("|",$selected);
  $have_selected = false;
  $query1 = "SELECT id, name FROM facility ORDER BY name";
  $fres = sqlStatement($query1);

  $name = htmlspecialchars($name, ENT_QUOTES);
  echo "   <select name=\"$name\" multiple id=\"$id\"  class='form-control'>\n";

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



function VisitsWithNoForms($resid1,$respid1,$resenc1,$resfname1,$ros,$pe,$allcare,$audit_stat,$provider,$menu,$id1,$refer,$af_providers,$rendprovider,$feeprovider){            
    
    // Get rendering provider based on encounter id
    $renQ = sqlStatement("SELECT username FROM users u INNER JOIN form_encounter fen ON u.id = fen.rendering_provider WHERE fen.encounter = $resenc1");
    $renFetch = sqlFetchArray($renQ);
    $rendprovider = $renFetch['username'];
    
    $fuvStr = "SELECT DISTINCT (
        form_encounter.encounter
        ),form_encounter.rendering_provider, form_encounter.facility, f.screen_group, form_encounter.pid,form_encounter.facility_id, form_encounter.encounter, form_encounter.pc_catid AS visitcategory_id, DATE_FORMAT( form_encounter.date,  '%Y-%m-%d' ) AS dos, form_encounter.provider_id AS provider_id,f.screen_names
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
        WHERE  form_encounter.encounter=".$resenc1;
    
    $check_query       = sqlStatement("select * from incomplete_charts_filter where loginuser='$provider' order by id desc");
    $set_query        = sqlFetchArray($check_query);
    if(empty($set_query)){
        $fuvStr .= " AND (form_encounter.provider_id IN (".$af_providers.")  OR form_encounter.rendering_provider IN (".$af_providers.")) GROUP BY f.screen_group ORDER BY f.id DESC";
    }
    else{
        $fuvStr .= " AND (form_encounter.provider_id  = '$feeprovider' OR form_encounter.rendering_provider = '$rendprovider') GROUP BY f.screen_group ORDER BY f.id DESC";
    }
    //echo $fuvStr; echo "<br /><br />";
    $fuv_sql = sqlStatement($fuvStr); 
    
    $i=0;
    while ($fuv_row1=sqlFetchArray($fuv_sql)){ 
        if(substr($fuv_row1['screen_group'],1)=='Dictation'){
            $codegrpname=substr($fuv_row1['screen_group'],1);
            $result=unserialize($fuv_row1['screen_names']);

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
//            echo "<pre>";
//            echo "Subhan 1";
//            print_r($sequence);
//            echo "</pre>";
            foreach($sequence as $key => $value){
                $scr_val=explode("$$",$value);
                $field_id=$scr_val[2];
                $priority=$scr_val[1];
                $order2=$scr_val[0];
                if($order2!='') $order=$order2."$";
                else  $order=$order2;
                if($resfname1=='Allcare Encounter Forms' && $allcare=='YES' ){ 
                    if($field_id=='chief_complaint' || $field_id=='hpi' || $field_id=='assessment_note' || $field_id=='progress_note' || $field_id=='plan_note' ){
                        if($field_id=='chief_complaint'){
                            $field_id1='chief_complaint_stat';  
                            $dup_fid='chief_complaint_';
                            $gname='Chief_Complaint';
                            $title1='Chief Complaint';
                        } else if($field_id=='hpi'){
                            $field_id1='hpi_stat';
                            $dup_fid='hpi_';
                            $gname='History_of_Present_illness';
                            $title1='History of Present illness';
                        } else if($field_id=='assessment_note'){
                            $field_id1='assessment_note_stat';
                            $dup_fid='assessment_note_';
                            $gname='Assessment_Note';
                            $title1='Assessment Note';
                        } else if($field_id=='progress_note'){
                           $field_id1='progress_note_stat';
                           $dup_fid='progress_note_';
                           $gname='Progress_Note';
                           $title1='Progress Note';
                        } else if($field_id=='plan_note'){
                           $field_id1='plan_note_stat';
                           $dup_fid='plan_note_';
                           $gname='Plan_Note';
                           $title1='Plan Note';
                        }
                        $resfname3=str_replace(" ","_",$resfname1);
                        //echo "Subhan SA ". $provider;
                        if($priority=='Required'){

                            $ra = sqlStatement("select field_value from lbf_data  where field_id LIKE  '%$field_id1%' AND form_id='".$resid1."'");
                            $a='';
                            while($frowa = sqlFetchArray($ra)){
                              $a=explode("|",$frowa['field_value']);
                            }
                            if(!empty($a[0])){
                                if(in_array('pending',$a) && in_array('finalized',$a)){
                                    $fname_comp.=$order."<a href='javascript:;' onclick=win1('allcare_encounter_forms/allcare_enc_forms.php?groupname=$gname&form_id1=$resid1&enc3=$resenc1&pid1=$respid1&inmode1=edit&file_name1=Incomplete_charts&provider=$provider&location=provider_portal&fname1=$resfname3') ><span>".htmlspecialchars( xl($title1), ENT_NOQUOTES)."</span></a>,";
                                }
                                elseif(in_array('pending',$a) ){
                                 $fname.=$order."<a href='javascript:;' onclick=win1('allcare_encounter_forms/allcare_enc_forms.php?groupname=$gname&form_id1=$resid1&enc3=$resenc1&pid1=$respid1&inmode1=edit&file_name1=Incomplete_charts&provider=$provider&location=provider_portal&fname1=$resfname3') ><span>".htmlspecialchars( xl($title1), ENT_NOQUOTES)."</span></a>,";
                                }elseif(in_array('finalized',$a)) {
                                  $fname_comp.=$order."<a href='javascript:;' onclick=win1('allcare_encounter_forms/allcare_enc_forms.php?groupname=$gname&form_id1=$resid1&enc3=$resenc1&pid1=$respid1&inmode1=edit&file_name1=Incomplete_charts&provider=$provider&location=provider_portal&fname1=$resfname3') ><span>".htmlspecialchars( xl($title1), ENT_NOQUOTES)."</span></a>,";
                                }
                            }else{
                              $req_not_started.=$order."<a href='javascript:;' onclick=win1('allcare_encounter_forms/allcare_enc_forms.php?groupname=$gname&form_id1=$resid1&enc3=$resenc1&pid1=$respid1&inmode1=edit&file_name1=Incomplete_charts&provider=$provider&location=provider_portal&fname1=$resfname3') ><span>".htmlspecialchars( xl($title1), ENT_NOQUOTES)."</span></a>,";
                            }

                        } else if($priority=='Optional'){

                            $ra = sqlStatement("select field_value from lbf_data  where field_id LIKE  '%$field_id1%'  AND form_id='".$resid1."'");
                              $a='';
                            while($frowa = sqlFetchArray($ra)){

                               $a=explode("|",$frowa['field_value']);
                            }
                            if(!empty($a[0])){
                                    if(in_array('pending',$a) && in_array('finalized',$a)){

                                         $fname_opt_comp.=$order."<a href='javascript:;' onclick=win1('allcare_encounter_forms/allcare_enc_forms.php?groupname=$gname&form_id1=$resid1&enc3=$resenc1&pid1=$respid1&inmode1=edit&file_name1=Incomplete_charts&provider=$provider&location=provider_portal&fname1=$resfname3') ><span>".htmlspecialchars( xl($title1), ENT_NOQUOTES)."</span></a>,";
                                    } 
                                    elseif(in_array('pending',$a)){

                                         $fname_opt.=$order."<a href='javascript:;' onclick=win1('allcare_encounter_forms/allcare_enc_forms.php?groupname=$gname&form_id1=$resid1&enc3=$resenc1&pid1=$respid1&inmode1=edit&file_name1=Incomplete_charts&provider=$provider&location=provider_portal&fname1=$resfname3') ><span>".htmlspecialchars( xl($title1), ENT_NOQUOTES)."</span></a>,";                                        
                                   }elseif(in_array('finalized',$a)){

                                        $fname_opt_comp.=$order."<a href='javascript:;' onclick=win1('allcare_encounter_forms/allcare_enc_forms.php?groupname=$gname&form_id1=$resid1&enc3=$resenc1&pid1=$respid1&inmode1=edit&file_name1=Incomplete_charts&provider=$provider&location=provider_portal&fname1=$resfname3') ><span>".htmlspecialchars( xl($title1), ENT_NOQUOTES)."</span></a>,";
                                   }
                            }else {

                              $opt_not_started.=$order."<a href='javascript:;' onclick=win1('allcare_encounter_forms/allcare_enc_forms.php?groupname=$gname&form_id1=$resid1&enc3=$resenc1&pid1=$respid1&inmode1=edit&file_name1=Incomplete_charts&provider=$provider&location=provider_portal&fname1=$resfname3') ><span>".htmlspecialchars( xl($title1), ENT_NOQUOTES)."</span></a>,";
                            }

                        } 
                    }
                    //duplicate Entries
                    $sql1 = sqlStatement("select count(encounter) as count from  forms where deleted=0 and form_name='$resfname1' and encounter=$resenc1 and pid=$respid1");
                    $row1=sqlFetchArray($sql1);
                    if($row1['count']>1){
                        $sql2=sqlStatement("select * from forms where form_name='$resfname1' and encounter=$resenc1 and pid=$respid1");
                        $f=0; $cnt=0;
                        while ($row2=sqlFetchArray($sql2)) {

                            $sql3 = sqlStatement("select * from lbf_data  where field_id LIKE  '%$dup_fid%'  AND form_id='".$row2['form_id']."'");
                            $rowcount=mysql_num_rows($sql3); 
                            if($rowcount!=0){
                                $cnt+=1;
                                if($cnt==2){
                                    if($f==0){
                                     $duplicate.=$order.$title1.",";
                                     $f++;
                                    }
                                }
                            }
                        }
                    }
                }else if($resfname1=='New Patient Encounter' && $allcare=='NO' ){ 
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
                              $req_not_started.=$order."<a href='javascript:;' onclick=win1('allcare_encounter_forms/allcare_enc_forms.php?groupname=$gname&form_id1=0&enc3=$resenc1&pid1=$respid1&inmode1=edit&file_name1=Incomplete_charts&provider=$provider&location=provider_portal&fname1=$resfname3') ><span>".htmlspecialchars( xl($title1), ENT_NOQUOTES)."</span></a>,";
                        } else if($priority=='Optional'){
                              $opt_not_started.=$order."<a href='javascript:;' onclick=win1('allcare_encounter_forms/allcare_enc_forms.php?groupname=$gname&form_id1=0&enc3=$resenc1&pid1=$respid1&inmode1=edit&file_name1=Incomplete_charts&provider=$provider&location=provider_portal&fname1=$resfname3') ><span>".htmlspecialchars( xl($title1), ENT_NOQUOTES)."</span></a>,";
                        } 
                    }

                }

            }

            if($fname!=''){
                $fname2.=$fname;
            }
            if($fname_opt!=''){
                $fname_opt2.=$fname_opt;
            }
            if($fname_opt_comp!=''){
                $fname_opt_comp2.=$fname_opt_comp;
            }
            if($fname_comp!=''){
                $fname_comp2.=$fname_comp;
            }
            if($req_not_started!=''){
                $req_not_started2.=$req_not_started;
            }
            if($opt_not_started!=''){
                $opt_not_started2.=$opt_not_started;
            }

        }else if(substr($fuv_row1['screen_group'],1)=='Form Data'){

            $codegrpname1=substr($fuv_row1['screen_group'],1);
            $result1=unserialize($fuv_row1['screen_names']);

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
                if($order5!='')
                    $order=$order5."$";
                else 
                    $order=$order5;
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
                        if(!empty($field_val1[0])){ 
                           if(in_array('finalized',$field_val1) && in_array('pending',$field_val1)){
                               $fname_comp_his.=$order."<a href='javascript:;' onclick=win1('history/history_custom.php?pid=$respid1&provider=$provider&location=provider_portal&grpname=$grpname&form_id=$formid12&grp_stat=$grp_stat&encounter=$resenc1') ><span>".htmlspecialchars( xl($his_title), ENT_NOQUOTES)."</span></a>,";
                           }
                          else if(in_array('finalized',$field_val1)){

                                $fname_comp_his.=$order."<a href='javascript:;' onclick=win1('history/history_custom.php?pid=$respid1&provider=$provider&location=provider_portal&grpname=$grpname&form_id=$formid12&grp_stat=$grp_stat&encounter=$resenc1') ><span>".htmlspecialchars( xl($his_title), ENT_NOQUOTES)."</span></a>,";
                           }elseif(in_array('pending',$field_val1) ) {

                               $fname_his.=$order."<a href='javascript:;' onclick=win1('history/history_custom.php?pid=$respid1&provider=$provider&location=provider_portal&grpname=$grpname&form_id=$formid12&grp_stat=$grp_stat&encounter=$resenc1') ><span>".htmlspecialchars( xl($his_title), ENT_NOQUOTES)."</span></a>,";
                          }
                         }else {
                              $req_not_started_his.=$order."<a href='javascript:;' onclick=win1('history/history_custom.php?pid=$respid1&provider=$provider&location=provider_portal&grpname=$grpname&form_id=$formid12&grp_stat=$grp_stat&encounter=$resenc1') ><span>".htmlspecialchars( xl($his_title), ENT_NOQUOTES)."</span></a>,";
                          }
                    }else if($priority5=='Optional'){
                        if(!empty($field_val1[0])){
                           if(in_array('finalized',$field_val1) && in_array('pending',$field_val1) ){
                               $fname_opt_his_comp.=$order."<a href='javascript:;' onclick=win1('history/history_custom.php?pid=$respid1&provider=$provider&location=provider_portal&grpname=$grpname&form_id=$formid12&grp_stat=$grp_stat&encounter=$resenc1') ><span>".htmlspecialchars( xl($his_title), ENT_NOQUOTES)."</span></a>,";
                           }
                           else if(in_array('finalized',$field_val1)){

                                 $fname_opt_his_comp.=$order."<a href='javascript:;' onclick=win1('history/history_custom.php?pid=$respid1&provider=$provider&location=provider_portal&grpname=$grpname&form_id=$formid12&grp_stat=$grp_stat&encounter=$resenc1') ><span>".htmlspecialchars( xl($his_title), ENT_NOQUOTES)."</span></a>,";
                           }else if(in_array('pending',$field_val1) ) {

                               $fname_opt_his.=$order."<a href='javascript:;' onclick=win1('history/history_custom.php?pid=$respid1&provider=$provider&location=provider_portal&grpname=$grpname&form_id=$formid12&grp_stat=$grp_stat&encounter=$resenc1') ><span>".htmlspecialchars( xl($his_title), ENT_NOQUOTES)."</span></a>,";
                           }
                        }else{
                           $opt_not_started_his.=$order."<a href='javascript:;' onclick=win1('history/history_custom.php?pid=$respid1&provider=$provider&location=provider_portal&grpname=$grpname&form_id=$formid12&grp_stat=$grp_stat&encounter=$resenc1') ><span>".htmlspecialchars( xl($his_title), ENT_NOQUOTES)."</span></a>,";
                       }
                    }
                }
                else if($field_id5=='face2face'){
                    if($resfname1=='Allcare Encounter Forms' && $allcare=='YES' ){ 
                        $field_id1='f2f_stat';
                        $dup_fid='f2f_';
                        $gname='Face_to_Face_HH_Plan';
                        $title1='Face to Face HH Plan';

                        $resfname3=str_replace(" ","_",$resfname1);
                        if($priority5=='Required'){

                            $ra = sqlStatement("select field_value from lbf_data  where field_id LIKE  '%$field_id1%'  AND form_id='".$resid1."'");
                            while($frowa = sqlFetchArray($ra)){
                               //$a = $frowa['field_value'];

                               $a=explode("|",$frowa['field_value']);
                            }
                            if(!empty($a[0])){
                                if(in_array('pending',$a) && in_array('finalized',$a)){
                                    $fname_comp_his.=$order."<a href='javascript:;' onclick=win1('allcare_encounter_forms/allcare_enc_forms.php?groupname=$gname&form_id1=$resid1&enc3=$resenc1&pid1=$respid1&inmode1=edit&file_name1=Incomplete_charts&provider=$provider&location=provider_portal&fname1=$resfname3') ><span>".htmlspecialchars( xl($title1), ENT_NOQUOTES)."</span></a>,";
                                } 
                                elseif(in_array('pending',$a) ){

                                  $fname_his.=$order."<a href='javascript:;' onclick=win1('allcare_encounter_forms/allcare_enc_forms.php?groupname=$gname&form_id1=$resid1&enc3=$resenc1&pid1=$respid1&inmode1=edit&file_name1=Incomplete_charts&provider=$provider&location=provider_portal&fname1=$resfname3') ><span>".htmlspecialchars( xl($title1), ENT_NOQUOTES)."</span></a>,";
                                }elseif(in_array('finalized',$a)) {

                                    $fname_comp_his.=$order."<a href='javascript:;' onclick=win1('allcare_encounter_forms/allcare_enc_forms.php?groupname=$gname&form_id1=$resid1&enc3=$resenc1&pid1=$respid1&inmode1=edit&file_name1=Incomplete_charts&provider=$provider&location=provider_portal&fname1=$resfname3') ><span>".htmlspecialchars( xl($title1), ENT_NOQUOTES)."</span></a>,";
                                }
                            }else {
                               $req_not_started_his.=$order."<a href='javascript:;' onclick=win1('allcare_encounter_forms/allcare_enc_forms.php?groupname=$gname&form_id1=$resid1&enc3=$resenc1&pid1=$respid1&inmode1=edit&file_name1=Incomplete_charts&provider=$provider&location=provider_portal&fname1=$resfname3') ><span>".htmlspecialchars( xl($title1), ENT_NOQUOTES)."</span></a>,";
                            }
                        } else if($priority5=='Optional'){

                            $ra = sqlStatement("select field_value from lbf_data  where field_id LIKE  '%$field_id1%'  AND form_id='".$resid1."'");
                            while($frowa = sqlFetchArray($ra)){
                              $a=explode("|",$frowa['field_value']);
                            }
                            if(!empty($a[0])){
                                if(in_array('pending',$a) && in_array('finalized',$a)){
                                    $fname_opt_his_comp.=$order."<a href='javascript:;' onclick=win1('allcare_encounter_forms/allcare_enc_forms.php?groupname=$gname&form_id1=$resid1&enc3=$resenc1&pid1=$respid1&inmode1=edit&file_name1=Incomplete_charts&provider=$provider&location=provider_portal&fname1=$resfname3') ><span>".htmlspecialchars( xl($title1), ENT_NOQUOTES)."</span></a>,";
                                }
                                elseif(in_array('pending',$a)){
                                    $fname_opt_his.=$order."<a href='javascript:;' onclick=win1('allcare_encounter_forms/allcare_enc_forms.php?groupname=$gname&form_id1=$resid1&enc3=$resenc1&pid1=$respid1&inmode1=edit&file_name1=Incomplete_charts&provider=$provider&location=provider_portal&fname1=$resfname3') ><span>".htmlspecialchars( xl($title1), ENT_NOQUOTES)."</span></a>,";                                        
                                }
                                elseif(in_array('finalized',$a)) {
                                     $fname_opt_his_comp.=$order."<a href='javascript:;' onclick=win1('allcare_encounter_forms/allcare_enc_forms.php?groupname=$gname&form_id1=$resid1&enc3=$resenc1&pid1=$respid1&inmode1=edit&file_name1=Incomplete_charts&provider=$provider&location=provider_portal&fname1=$resfname3') ><span>".htmlspecialchars( xl($title1), ENT_NOQUOTES)."</span></a>,";
                                }
                            }else{
                               $opt_not_started_his.=$order."<a href='javascript:;' onclick=win1('allcare_encounter_forms/allcare_enc_forms.php?groupname=$gname&form_id1=$resid1&enc3=$resenc1&pid1=$respid1&inmode1=edit&file_name1=Incomplete_charts&provider=$provider&location=provider_portal&fname1=$resfname3') ><span>".htmlspecialchars( xl($title1), ENT_NOQUOTES)."</span></a>,";
                            }
                        } 
                        //duplicate Entries
                        $sql1 = sqlStatement("select count(encounter) as count from  forms where deleted=0 and form_name='$resfname1' and encounter=$resenc1 and pid=$respid1");
                        $row1=sqlFetchArray($sql1);
                        if($row1['count']>1){
                            $sql2=sqlStatement("select * from forms where form_name='$resfname1' and encounter=$resenc1 and pid=$respid1");
                            $f=0; $cnt=0;
                            while ($row2=sqlFetchArray($sql2)) {
                                $sql3 = sqlStatement("select * from lbf_data  where field_id LIKE  '%$dup_fid%'  AND form_id='".$row2['form_id']."'");
                                $rowcount=mysql_num_rows($sql3); 
                                if($rowcount!=0){
                                    $cnt+=1;
                                    if($cnt==2){
                                         if($f==0){
                                         $duplicate.=$order.$title1.",";
                                         $f++;
                                        }
                                    }

                                }
                            }
                        }
                    }
                    else  if($resfname1=='New Patient Encounter' && $allcare=='NO' ){ 
                        $field_id1='f2f_stat';
                        $gname='Face_to_Face_HH_Plan';
                        $title1='Face to Face HH Plan';

                        $resfname3=str_replace(" ","_",$resfname1);
                        if($priority5=='Required'){
                            $req_not_started_his.=$order."<a href='javascript:;' onclick=win1('allcare_encounter_forms/allcare_enc_forms.php?groupname=$gname&form_id1=0&enc3=$resenc1&pid1=$respid1&inmode1=edit&file_name1=Incomplete_charts&provider=$provider&location=provider_portal&fname1=$resfname3') ><span>".htmlspecialchars( xl($title1), ENT_NOQUOTES)."</span></a>,";
                        } else if($priority5=='Optional'){
                                $opt_not_started_his.=$order."<a href='javascript:;' onclick=win1('allcare_encounter_forms/allcare_enc_forms.php?groupname=$gname&form_id1=0&enc3=$resenc1&pid1=$respid1&inmode1=edit&file_name1=Incomplete_charts&provider=$provider&location=provider_portal&fname1=$resfname3') ><span>".htmlspecialchars( xl($title1), ENT_NOQUOTES)."</span></a>,";
                        } 
                    }
                }
                else if($field_id5=='procedure'){
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
                            if(!empty($field_val[0])){    
                                if(in_array('pending',$field_val) && in_array('finalized',$field_val)){
                                    $fname_comp_his.=$order."<a href='javascript:;' onclick=win1('procedure_order/new.php?encounter=$resenc1&pid=$respid1&id=$fid1&provider=$rendprovider&location=provider_portal&formid=$formid1') ><span>".htmlspecialchars( xl('Procedure Order'), ENT_NOQUOTES)."</span></a>,";
                                }
                                elseif(in_array('pending',$field_val) ){
                                    $fname_his.=$order."<a href='javascript:;' onclick=win1('procedure_order/new.php?encounter=$resenc1&pid=$respid1&provider=$rendprovider&location=provider_portal&formid=$formid1&id=$fid1') ><span>".htmlspecialchars( xl('Procedure Order'), ENT_NOQUOTES)."</span></a>,";

                                }else if(in_array('finalized',$field_val)) {
                                    $fname_comp_his.=$order."<a href='javascript:;' onclick=win1('procedure_order/new.php?encounter=$resenc1&pid=$respid1&id=$fid1&provider=$rendprovider&location=provider_portal&formid=$formid1') ><span>".htmlspecialchars( xl('Procedure Order'), ENT_NOQUOTES)."</span></a>,";
                                }
                            }else {
                                   $req_not_started_his.=$order."<a href='javascript:;' onclick=win1('procedure_order/new.php?encounter=$resenc1&pid=$respid1&id=$fid1&provider=$rendprovider&location=provider_portal&formid=$formid1') ><span>".htmlspecialchars( xl('Procedure Order'), ENT_NOQUOTES)."</span></a>,";
                            }
                        }
                        else if($priority5=='Optional'){
                               if(!empty($field_val[0])){ 
                                    if(in_array('pending',$field_val) && in_array('finalized',$field_val) ){
                                         $fname_opt_his_comp.=$order."<a href='javascript:;' onclick=win1('procedure_order/new.php?encounter=$resenc1&pid=$respid1&provider=$rendprovider&location=provider_portal&formid=$formid1&id=$fid1') ><span>".htmlspecialchars( xl('Procedure Order'), ENT_NOQUOTES)."</span></a>,";
                                    }
                                    elseif(in_array('pending',$field_val)){

                                         $fname_opt_his_comp.=$order."<a href='javascript:;' onclick=win1('procedure_order/new.php?encounter=$resenc1&pid=$respid1&provider=$rendprovider&location=provider_portal&formid=$formid1&id=$fid1') ><span>".htmlspecialchars( xl('Procedure Order'), ENT_NOQUOTES)."</span></a>,";

                                   }else if(in_array('finalized',$field_val)){

                                        $fname_opt_his.=$order."<a href='javascript:;' onclick=win1('procedure_order/new.php?encounter=$resenc1&pid=$respid1&id=$fid1&provider=$rendprovider&location=provider_portal&formid=$formid1') ><span>".htmlspecialchars( xl('Procedure Order'), ENT_NOQUOTES)."</span></a>,";
                                   }
                               }else{
                                   $opt_not_started_his.=$order."<a href='javascript:;' onclick=win1('procedure_order/new.php?encounter=$resenc1&pid=$respid1&id=$fid1&provider=$rendprovider&location=provider_portal&formid=$formid1') ><span>".htmlspecialchars( xl('Procedure Order'), ENT_NOQUOTES)."</span></a>,";
                               }
                        }
                    }else {
                        if($priority5=='Required'){
                           $req_not_started_his.=$order."<a href='javascript:;' onclick=win1('procedure_order/new.php?encounter=$resenc1&pid=$respid1&id=0&provider=$rendprovider&location=provider_portal&formid=$formid1') ><span>".htmlspecialchars( xl('Procedure Order'), ENT_NOQUOTES)."</span></a>,";

                        }else if($priority5=='Optional'){
                            $opt_not_started_his.=$order."<a href='javascript:;' onclick=win1('procedure_order/new.php?encounter=$resenc1&pid=$respid1&id=0&provider=$rendprovider&location=provider_portal&formid=$formid1') ><span>".htmlspecialchars( xl('Procedure Order'), ENT_NOQUOTES)."</span></a>,";

                        }
                    }
                     //duplicate Entries
                    $sql1 = sqlStatement("select count(encounter) as count from  forms where deleted=0 and form_name='Procedure Order' and encounter=$resenc1 and pid=$respid1");
                    $row1=sqlFetchArray($sql1);
                    if($row1['count']>1){
                        $duplicate.=$order.'Procedure Order,';
                    }  
                } 
                else if($field_id5=='quality_of_care'){
                    $resid_his=sqlStatement("select form_id  from forms where  pid=$respid1 AND encounter=$resenc1 AND deleted = 0 and formdir='LBf2' ");  
                    $frow_his = sqlFetchArray($resid_his);
                    $gname = 'Quality_Of_Care';
                    if(!empty($frow_his)){
                        $qa_stat=sqlStatement("select field_value  from lbf_data WHERE form_id = '".$frow_his['form_id'] ."' AND field_id = 'quality_of_care_stat' ");  
                        $qaform_id = $frow_his['form_id'] ;
                        $qa_stat_res = sqlFetchArray($qa_stat);
                        if($priority5=='Required'){ 
                            if( strpos('finalized',$qa_stat_res['field_value']) !== false  ){
                                $fname_comp_his.=$order."<a href='javascript:;' onclick=win1('quality_of_care/new.php?pid=$respid1&encounter=$resenc1&provider=$provider&location=provider_portal&form_id=$qaform_id') ><span>".htmlspecialchars( xl('Quality Of Care'), ENT_NOQUOTES)."</span></a>,";
                            }else if( strpos('pending',$qa_stat_res['field_value']) !== false  ) {

                                $fname_his.=$order."<a href='javascript:;' onclick=win1('quality_of_care/new.php?pid=$respid1&encounter=$resenc1&provider=$provider&location=provider_portal&form_id=$qaform_id') ><span>".htmlspecialchars( xl('Quality Of Care'), ENT_NOQUOTES)."</span></a>,";
                            }else {
                                $req_not_started_his.=$order."<a href='javascript:;' onclick=win1('quality_of_care/new.php?groupname=$gname&pid=$respid1&encounter=$resenc1&provider=$provider&location=provider_portal&form_id=$qaform_id') ><span>".htmlspecialchars( xl('Quality Of Care'), ENT_NOQUOTES)."</span></a>,";
                            }
                        }else if($priority5=='Optional'){
                            if(strpos('finalized',$qa_stat_res['field_value']) !== false){
                                $fname_opt_his_comp.=$order."<a href='javascript:;' onclick=win1('quality_of_care/new.php?groupname=$gname&pid=$respid1&encounter=$resenc1&provider=$provider&location=provider_portal&form_id=$qaform_id') ><span>".htmlspecialchars( xl('Quality Of Care'), ENT_NOQUOTES)."</span></a>,";
                            }else if( strpos('pending',$qa_stat_res['field_value']) !== false  ){
                                $fname_opt_his.=$order."<a href='javascript:;' onclick=win1('quality_of_care/new.php?groupname=$gname&pid=$respid1&encounter=$resenc1&provider=$provider&location=provider_portal&form_id=$qaform_id') ><span>".htmlspecialchars( xl('Quality Of Care'), ENT_NOQUOTES)."</span></a>,";
                            }else {
                                $opt_not_started_his.=$order."<a href='javascript:;' onclick=win1('quality_of_care/new.php?groupname=$gname&pid=$respid1&encounter=$resenc1&provider=$provider&location=provider_portal&form_id=$qaform_id') ><span>".htmlspecialchars( xl('Quality Of Care'), ENT_NOQUOTES)."</span></a>,";
                            }
                        }
                    }else{
                        if($priority5=='Required'){   
                            $req_not_started_his.=$order."<a href='javascript:;' onclick=win1('quality_of_care/new.php?groupname=$gname&pid=$respid1&encounter=$resenc1&provider=$provider&location=provider_portal&form_id=0') ><span>".htmlspecialchars( xl('Quality Of Care'), ENT_NOQUOTES)."</span></a>,";

                        }else if($priority5=='Optional'){
                            $opt_not_started_his.=$order."<a href='javascript:;' onclick=win1('quality_of_care/new.php?groupname=$gname&pid=$respid1&encounter=$resenc1&provider=$provider&location=provider_portal&form_id=0') ><span>".htmlspecialchars( xl('Quality Of Care'), ENT_NOQUOTES)."</span></a>,";

                        }
                    }
                    //duplicate Entries
                    $sql1 = sqlStatement("select count(encounter) as count from  forms where deleted=0 and form_name='Allcare Encounter Forms' and encounter=$resenc1 and pid=$respid1");
                    $row1=sqlFetchArray($sql1);
                    if($row1['count']>1){
                        $sql2=sqlStatement("select * from forms where form_name='Allcare Encounter Forms' and encounter=$resenc1 and pid=$respid1");
                        $f=0; $cnt=0;
                        while ($row2=sqlFetchArray($sql2)) {
                           $sql3 = sqlStatement("select * from lbf_data  where field_id LIKE  '%quality_of_care%'  AND form_id='".$row2['form_id']."'");
                            $rowcount=mysql_num_rows($sql3); 
                            if($rowcount!=0){
                                $cnt+=1;
                                if($cnt==2){
                                    if($f==0){
                                     $duplicate.=$order.'Quality Of Care'.",";
                                     $f++;
                                    }
                                }
                            }
                        }
                    }
                }    
            }
            if($fname_his!=''){
                $fname2.=$fname_his;
            }
            if($fname_opt_his!=''){
                $fname_opt2.=$fname_opt_his;
            }
            if($fname_comp_his!=''){
                $fname_comp2.=$fname_comp_his;
            }
            if($fname_opt_his_comp!=''){
                $fname_opt_comp2.=$fname_opt_his_comp;
            }
             if($opt_not_started_his!=''){
                $opt_not_started2.=$opt_not_started_his;
            }
            if($req_not_started_his!=''){
                $req_not_started2.=$req_not_started_his;
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
                if($order5!='') 
                    $order=$order5."$";
                else  
                    $order=$order5;

                if($field_id5=='ros'){
                    $resid_ros=sqlStatement("select *  from forms where form_name  ='Allcare Review Of Systems' AND encounter=$resenc1 AND pid=$respid1  AND deleted=0 ORDER BY id DESC");  
                    $frow_ros = sqlFetchArray($resid_ros); 
                    $fid1=$frow_ros['form_id'];
                    if($fid1!=''){
                         if($priority5=='Required'){


                                 $resid_ros1=sqlStatement("select finalized,pending  from tbl_form_allcare_ros where id  =$fid1  AND pid=$respid1  ORDER BY id DESC");  
                                 $frow_ros1 = sqlFetchArray($resid_ros1); 
                                 if($frow_ros1['finalized']=='YES' && $frow_ros1['pending']=='YES'){
                                     $fname_comp_pre.=$order."<a href='javascript:;' onclick=win1('allcare_ros/view_custom.php?formname=allcare_ros&encounter=$resenc1&pid=$respid1&id=$fid1&location=provider_portal&provider=$provider') ><span>".htmlspecialchars( xl('Allcare Ros'), ENT_NOQUOTES)."</span></a>,";
                                 }
                                 elseif($ros='YES'  && $frow_ros1['pending']=='YES'){

                                 $fname_pre.=$order."<a href='javascript:;' onclick=win1('allcare_ros/view_custom.php?formname=allcare_ros&encounter=$resenc1&pid=$respid1&location=provider_portal&provider=$provider&id=$fid1') ><span>".htmlspecialchars( xl('Allcare Ros'), ENT_NOQUOTES)."</span></a>,";
                                } elseif($ros='YES' && $frow_ros1['finalized']=='YES') {

                                     $fname_comp_pre.=$order."<a href='javascript:;' onclick=win1('allcare_ros/view_custom.php?formname=allcare_ros&encounter=$resenc1&pid=$respid1&id=$fid1&location=provider_portal&provider=$provider') ><span>".htmlspecialchars( xl('Allcare Ros'), ENT_NOQUOTES)."</span></a>,";
                                }else {
                                    $req_not_started_pre.=$order."<a href='javascript:;' onclick=win1('allcare_ros/view_custom.php?formname=allcare_ros&encounter=$resenc1&pid=$respid1&id=$fid1&location=provider_portal&provider=$provider') ><span>".htmlspecialchars( xl('Allcare Ros'), ENT_NOQUOTES)."</span></a>,";
                                }

                        }
                        else if($priority5=='Optional'){
                            $resid_ros1=sqlStatement("select finalized,pending  from tbl_form_allcare_ros where id  =$fid1  AND pid=$respid1  ORDER BY id DESC");  
                            $frow_ros1 = sqlFetchArray($resid_ros1);
                            if($frow_ros1['pending']=='YES' && $frow_ros1['finalized']=='YES'){
                                $fname_opt_pre_comp.=$order."<a href='javascript:;' onclick=win1('allcare_ros/view_custom.php?formname=allcare_ros&encounter=$resenc1&pid=$respid1&id=$fid1&location=provider_portal&provider=$provider') ><span>".htmlspecialchars( xl('Allcare Ros'), ENT_NOQUOTES)."</span></a>,";
                            }
                            elseif($frow_ros1['pending']=='YES'){
                                $fname_opt_pre.=$order."<a href='javascript:;' onclick=win1('allcare_ros/view_custom.php?formname=allcare_ros&encounter=$resenc1&pid=$respid1&location=provider_portal&provider=$provider&id=$fid1') ><span>".htmlspecialchars( xl('Allcare Ros'), ENT_NOQUOTES)."</span></a>,";
                            }elseif($frow_ros1['finalized']=='YES'){

                                 $fname_opt_pre_comp.=$order."<a href='javascript:;' onclick=win1('allcare_ros/view_custom.php?formname=allcare_ros&encounter=$resenc1&pid=$respid1&id=$fid1&location=provider_portal&provider=$provider') ><span>".htmlspecialchars( xl('Allcare Ros'), ENT_NOQUOTES)."</span></a>,";
                            }else {
                                $opt_not_started_pre.=$order."<a href='javascript:;' onclick=win1('allcare_ros/view_custom.php?formname=allcare_ros&encounter=$resenc1&pid=$respid1&id=$fid1&location=provider_portal&provider=$provider') ><span>".htmlspecialchars( xl('Allcare Ros'), ENT_NOQUOTES)."</span></a>,";
                            }
                        }
                    }
                    else {
                        if($priority5=='Required'){
                            $req_not_started_pre.=$order."<a href='javascript:;' onclick=win1('allcare_ros/new_custom.php?formname=allcare_ros&encounter=$resenc1&pid=$respid1&location=provider_portal&provider=$provider') ><span>".htmlspecialchars( xl('Allcare Ros'), ENT_NOQUOTES)."</span></a>,";
                        }
                        else if($priority5=='Optional'){
                            $opt_not_started_pre.=$order."<a href='javascript:;' onclick=win1('allcare_ros/new_custom.php?formname=allcare_ros&encounter=$resenc1&pid=$respid1&location=provider_portal&provider=$provider') ><span>".htmlspecialchars( xl('Allcare Ros'), ENT_NOQUOTES)."</span></a>,";
                        }
                    }  

                    //duplicate Entries
                    $sql1 = sqlStatement("select count(encounter) as count from  forms where deleted=0 and form_name='Allcare Review Of Systems' and encounter=$resenc1 and pid=$respid1");
                    $row1=sqlFetchArray($sql1);
                    if($row1['count']>1){
                        $duplicate.=$order.'Allcare Ros,';

                    } 
                }
                else if($field_id5=='physical_exam'){

                    $resid_pe=sqlStatement("select *  from forms where form_name  ='Allcare Physical Exam' AND encounter=$resenc1 AND pid=$respid1  AND deleted=0 ORDER BY id DESC");  
                    $frow_pe = sqlFetchArray($resid_pe);
                    $fid=$frow_pe['form_id'];
                         if($fid!=''){
                              $resid_pe1=sqlStatement("select *  from tbl_allcare_formflag where form_name  ='Allcare Physical Exam' AND encounter_id=$resenc1 and form_id=$fid ORDER BY id DESC");  
                              $frow_pe1 = sqlFetchArray($resid_pe1);
                              if($priority5=='Required'){
                                    if( $frow_pe1['finalized']=='Y' && $frow_pe1['pending']=='Y'){

                                         $fname_comp_pre.=$order."<a href='javascript:;' onclick=win1('allcare_physical_exam/new_custom.php?formname=allcare_physical_exam&edit=custom_pe&encounter=$resenc1&pid=$respid1&id=$fid&location=provider_portal&provider=$provider&menu_val=$menu') ><span>".htmlspecialchars( xl('Allcare Physical_exam'), ENT_NOQUOTES)."</span></a>,";
                                    }      
                                   else if($pe='YES' && $frow_pe1['pending']=='Y'){


                                         $fname_pre.=$order."<a href='javascript:;' onclick=win1('allcare_physical_exam/new_custom.php?formname=allcare_physical_exam&edit=custom_pe&encounter=$resenc1&pid=$respid1&location=provider_portal&provider=$provider&menu_val=$menu&id=$fid') ><span>".htmlspecialchars( xl('Allcare Physical_exam'), ENT_NOQUOTES)."</span></a>,";

                                    }elseif($pe='YES' && $frow_pe1['finalized']=='Y') {

                                         $fname_comp_pre.=$order."<a href='javascript:;' onclick=win1('allcare_physical_exam/new_custom.php?formname=allcare_physical_exam&edit=custom_pe&encounter=$resenc1&pid=$respid1&id=$fid&location=provider_portal&provider=$provider&menu_val=$menu') ><span>".htmlspecialchars( xl('Allcare Physical_exam'), ENT_NOQUOTES)."</span></a>,";
                                    }else{
                                        $req_not_started_pre.=$order."<a href='javascript:;' onclick=win1('allcare_physical_exam/new_custom.php?formname=allcare_physical_exam&edit=custom_pe&encounter=$resenc1&pid=$respid1&id=$fid&location=provider_portal&provider=$provider&menu_val=$menu') ><span>".htmlspecialchars( xl('Allcare Physical_exam'), ENT_NOQUOTES)."</span></a>,";
                                    }
                                }else if($priority5=='Optional'){
                                    if($frow_pe1['finalized']=='Y' && $frow_pe1['pending']=='Y'){
                                         $fname_opt_pre_comp.=$order."<a href='javascript:;' onclick=win1('allcare_physical_exam/new_custom.php?formname=allcare_physical_exam&edit=custom_pe&encounter=$resenc1&pid=$respid1&id=$fid&location=provider_portal&provider=$provider&menu_val=$menu') ><span>".htmlspecialchars( xl('Allcare Physical_exam'), ENT_NOQUOTES)."</span></a>,";
                                    }
                                    else if($pe='YES' && $frow_pe1['pending']=='Y'){

                                         $fname_opt_pre.=$order."<a href='javascript:;' onclick=win1('allcare_physical_exam/new_custom.php?formname=allcare_physical_exam&edit=custom_pe&encounter=$resenc1&pid=$respid1&location=provider_portal&provider=$provider&menu_val=$menu&id=$fid') ><span>".htmlspecialchars( xl('Allcare Physical_exam'), ENT_NOQUOTES)."</span></a>,";
                                    }elseif($pe='YES' && $frow_pe1['finalized']=='Y') {

                                        $fname_opt_pre_comp.=$order."<a href='javascript:;' onclick=win1('allcare_physical_exam/new_custom.php?formname=allcare_physical_exam&edit=custom_pe&encounter=$resenc1&pid=$respid1&id=$fid&location=provider_portal&provider=$provider&menu_val=$menu') ><span>".htmlspecialchars( xl('Allcare Physical_exam'), ENT_NOQUOTES)."</span></a>,";
                                    }else{
                                        $opt_not_started_pre.=$order."<a href='javascript:;' onclick=win1('allcare_physical_exam/new_custom.php?formname=allcare_physical_exam&edit=custom_pe&encounter=$resenc1&pid=$respid1&id=$fid&location=provider_portal&provider=$provider&menu_val=$menu') ><span>".htmlspecialchars( xl('Allcare Physical_exam'), ENT_NOQUOTES)."</span></a>,";
                                    }
                                }
                        }
                        else{
                            if($priority5=='Required'){
                                $req_not_started_pre.=$order."<a href='javascript:;' onclick=win1('allcare_physical_exam/new_custom.php?formname=allcare_physical_exam&edit=custom_pe&encounter=$resenc1&pid=$respid1&location=provider_portal&provider=$provider&menu_val=$menu') ><span>".htmlspecialchars( xl('Allcare Physical_exam'), ENT_NOQUOTES)."</span></a>,";
                            }
                            else if($priority5=='Optional'){
                                $opt_not_started_pre.=$order."<a href='javascript:;' onclick=win1('allcare_physical_exam/new_custom.php?formname=allcare_physical_exam&edit=custom_pe&encounter=$resenc1&pid=$respid1&location=provider_portal&provider=$provider&menu_val=$menu') ><span>".htmlspecialchars( xl('Allcare Physical_exam'), ENT_NOQUOTES)."</span></a>,";

                            }
                        }
                        //duplicate Entries
                        $sql1 = sqlStatement("select count(encounter) as count from  forms where deleted=0 and form_name='Allcare Physical Exam' and encounter=$resenc1 and pid=$respid1");
                        $row1=sqlFetchArray($sql1);
                        if($row1['count']>1){
                            $duplicate.=$order.'Allcare Physical_exam,';
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
                            if(!empty($field_val2[0])){
                                if(in_array('pending',$field_val2)  && in_array('finalized',$field_val2)){
                                     $fname_comp_pre.=$order."<a href='javascript:;' onclick=win1('vitals/view_custom.php?formname=Vitals&encounter=$resenc1&pid=$respid1&id=$fid1&provider=$provider&location=provider_portal&status=$status') ><span>".htmlspecialchars( xl('Vitals'), ENT_NOQUOTES)."</span></a>,";
                                }
                                elseif(in_array('pending',$field_val2) ){
                                    $fname_pre.=$order."<a href='javascript:;' onclick=win1('vitals/view_custom.php?formname=Vitals&encounter=$resenc1&pid=$respid1&provider=$provider&location=provider_portal&id=$fid1&status=$status') ><span>".htmlspecialchars( xl('Vitals'), ENT_NOQUOTES)."</span></a>,";
                                }
                                elseif(in_array('finalized',$field_val2)){
                                    $fname_comp_pre.=$order."<a href='javascript:;' onclick=win1('vitals/view_custom.php?formname=Vitals&encounter=$resenc1&pid=$respid1&id=$fid1&provider=$provider&location=provider_portal&status=$status') ><span>".htmlspecialchars( xl('Vitals'), ENT_NOQUOTES)."</span></a>,";
                                }
                            }else{
                               $req_not_started_pre.=$order."<a href='javascript:;' onclick=win1('vitals/view_custom.php?formname=Vitals&encounter=$resenc1&pid=$respid1&id=$fid1&provider=$provider&location=provider_portal&status=$status') ><span>".htmlspecialchars( xl('Vitals'), ENT_NOQUOTES)."</span></a>,";
                            }
                        }
                        else if($priority5=='Optional'){
                            if(!empty($field_val2[0])){
                                if(in_array('pending',$field_val2)  && in_array('finalized',$field_val2)){
                                     $fname_opt_pre_comp.=$order."<a href='javascript:;' onclick=win1('vitals/view_custom.php?formname=Vitals&encounter=$resenc1&pid=$respid1&id=$fid1&provider=$provider&location=provider_portal&status=$status') ><span>".htmlspecialchars( xl('Vitals'), ENT_NOQUOTES)."</span></a>,";
                                }
                                elseif(in_array('pending',$field_val2) ){
                                      $fname_opt_pre.=$order."<a href='javascript:;' onclick=win1('vitals/view_custom.php?formname=Vitals&encounter=$resenc1&pid=$respid1&provider=$provider&location=provider_portal&id=$fid1&status=$status') ><span>".htmlspecialchars( xl('Vitals'), ENT_NOQUOTES)."</span></a>,";

                                }elseif(in_array('finalized',$field_val2)) {
                                     $fname_opt_pre_comp.=$order."<a href='javascript:;' onclick=win1('vitals/view_custom.php?formname=Vitals&encounter=$resenc1&pid=$respid1&id=$fid1&provider=$provider&location=provider_portal&status=$status') ><span>".htmlspecialchars( xl('Vitals'), ENT_NOQUOTES)."</span></a>,";
                                }
                            }else{
                                $opt_not_started_pre.=$order."<a href='javascript:;' onclick=win1('vitals/view_custom.php?formname=Vitals&encounter=$resenc1&pid=$respid1&id=$fid1&provider=$provider&location=provider_portal&status=$status') ><span>".htmlspecialchars( xl('Vitals'), ENT_NOQUOTES)."</span></a>,";
                            }
                        }
                    }else {
                        if($priority5=='Required'){
                            $req_not_started_pre.=$order."<a href='javascript:;' onclick=win1('vitals/new_custom.php?formname=Vitals&encounter=$resenc1&pid=$respid1&id=$fid1&provider=$provider&location=provider_portal&status=$status') ><span>".htmlspecialchars( xl('Vitals'), ENT_NOQUOTES)."</span></a>,";
                        }else if($priority5=='Optional'){
                            $opt_not_started_pre.=$order."<a href='javascript:;' onclick=win1('vitals/new_custom.php?formname=Vitals&encounter=$resenc1&pid=$respid1&id=$fid1&provider=$provider&location=provider_portal&status=$status') ><span>".htmlspecialchars( xl('Vitals'), ENT_NOQUOTES)."</span></a>,";
                        }
                    }
                    //duplicate Entries
                   $sql1 = sqlStatement("select count(encounter) as count from  forms where deleted=0 and form_name='Vitals' and encounter=$resenc1 and pid=$respid1");
                   $row1=sqlFetchArray($sql1);
                   if($row1['count']>1){
                       $duplicate.=$order.'Vitals,';

                   }  
                } 
            }

            if($fname_pre!=''){
                $fname2.=$fname_pre;
            }
            if($fname_opt_pre!=''){
                $fname_opt2.=$fname_opt_pre;
            }
            if($fname_comp_pre!=''){
                $fname_comp2.=$fname_comp_pre;
            }
            if($fname_opt_pre_comp!=''){
                $fname_opt_comp2.=$fname_opt_pre_comp;
            }
            if($opt_not_started_pre!=''){
                $opt_not_started2.=$opt_not_started_pre;
            }
            if($req_not_started_pre!=''){
                $req_not_started2.=$req_not_started_pre;
            }
        }
        else if(substr($fuv_row1['screen_group'],1)=='Codes'){
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

            foreach($sequence4 as $keys2 => $values2){
                $scr_val5=explode("$$",$values2);
                $field_id5=$scr_val5[2];
                $priority5=$scr_val5[1];
                $order5=$scr_val5[0];
                if($order5!='') 
                    $order=$order5."$";
                else  
                    $order=$order5;
                if($field_id5=='medical_problem' || $field_id5=='allergies' || $field_id5=='medication' || $field_id5=='surgeries' || $field_id5=='dental_problems' || $field_id5=='dme'){
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
                    }else if($field_id5=='dme'){
                          $type='DME';
                          $title='DME';
                          $gname='DME';
                          $fieldid='dme_stat';
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
                        if(!empty($field_val[0])){
                            if(in_array('finalized',$field_val) && in_array('pending',$field_val) ){
                                 $fname_comp_issue.=$order."<a href='javascript:;' onclick=win1('Issues/stats_full_custom.php?active=all&category=$type&encounter=$resenc1&pid=$respid1&id=$fid1&location=provider_portal&provider=$provider&formid=$formid') ><span>".htmlspecialchars( xl($title), ENT_NOQUOTES)."</span></a>,";
                            }
                            elseif(in_array('finalized',$field_val)){
                                $fname_comp_issue.=$order."<a href='javascript:;' onclick=win1('Issues/stats_full_custom.php?active=all&category=$type&encounter=$resenc1&pid=$respid1&id=$fid1&location=provider_portal&provider=$provider&formid=$formid') ><span>".htmlspecialchars( xl($title), ENT_NOQUOTES)."</span></a>,";
                            }
                            else if(in_array('pending',$field_val) ) {
                                $fname_issue.=$order."<a href='javascript:;' onclick=win1('Issues/stats_full_custom.php?active=all&category=$type&encounter=$resenc1&pid=$respid1&location=provider_portal&provider=$provider&formid=$formid') ><span>".htmlspecialchars( xl($title), ENT_NOQUOTES)."</span></a>,";
                            }
                        }else {
                            $req_not_started_issue.=$order."<a href='javascript:;' onclick=win1('Issues/stats_full_custom.php?active=all&category=$type&encounter=$resenc1&pid=$respid1&location=provider_portal&provider=$provider&formid=$formid') ><span>".htmlspecialchars( xl($title), ENT_NOQUOTES)."</span></a>,";
                        }
                    }else if($priority5=='Optional'){
                        if(!empty($field_val[0])){
                           if(in_array('finalized',$field_val) && in_array('pending',$field_val) ){
                               $fname_opt_issue_comp.=$order."<a href='javascript:;' onclick=win1('Issues/stats_full_custom.php?active=all&category=$type&encounter=$resenc1&pid=$respid1&id=$fid1&location=provider_portal&provider=$provider&formid=$formid') ><span>".htmlspecialchars( xl($title), ENT_NOQUOTES)."</span></a>,"; 
                           }
                           else if(in_array('finalized',$field_val)){

                                 $fname_opt_issue_comp.=$order."<a href='javascript:;' onclick=win1('Issues/stats_full_custom.php?active=all&category=$type&encounter=$resenc1&pid=$respid1&id=$fid1&location=provider_portal&provider=$provider&formid=$formid') ><span>".htmlspecialchars( xl($title), ENT_NOQUOTES)."</span></a>,"; 
                           }else if(in_array('pending',$field_val) ){


                                $fname_opt_issue.=$order."<a href='javascript:;' onclick=win1('Issues/stats_full_custom.php?active=all&category=$type&encounter=$resenc1&pid=$respid1&location=provider_portal&provider=$provider&formid=$formid') ><span>".htmlspecialchars( xl($title), ENT_NOQUOTES)."</span></a>,";
                           }
                        }else{
                           $opt_not_started_issue.=$order."<a href='javascript:;' onclick=win1('Issues/stats_full_custom.php?active=all&category=$type&encounter=$resenc1&pid=$respid1&location=provider_portal&provider=$provider&formid=$formid') ><span>".htmlspecialchars( xl($title), ENT_NOQUOTES)."</span></a>,";
                       }
                   }
                }
                else if($field_id5=='codes'){
                    $resid_his=sqlStatement("select *  from billing where  pid=$respid1 AND encounter=$resenc1 AND activity = 1 ");  
                    $frow_his = sqlFetchArray($resid_his);

                    if(!empty($frow_his)){
                        if($priority5=='Required'){ 
                            if(!empty($frow_his) && $frow_his['billed']==1){
                                  $fname_comp_issue.=$order."<a href='javascript:;' onclick=win1('codes/feesheet_custom.php?pid=$respid1&encounter=$resenc1&provider=$provider&location=provider_portal') ><span>".htmlspecialchars( xl('Codes'), ENT_NOQUOTES)."</span></a>,";
                            }else {
                                 $fname_issue.=$order."<a href='javascript:;' onclick=win1('codes/feesheet_custom.php?pid=$respid1&encounter=$resenc1&provider=$provider&location=provider_portal') ><span>".htmlspecialchars( xl('Codes'), ENT_NOQUOTES)."</span></a>,";
                            }
                        }else if($priority5=='Optional'){
                            if(!empty($frow_his) && $frow_his['billed']==1){
                                  $fname_opt_issue_comp.=$order."<a href='javascript:;' onclick=win1('codes/feesheet_custom.php?pid=$respid1&encounter=$resenc1&provider=$provider&location=provider_portal') ><span>".htmlspecialchars( xl('Codes'), ENT_NOQUOTES)."</span></a>,";
                            }else {
                                $fname_opt_issue.=$order."<a href='javascript:;' onclick=win1('codes/feesheet_custom.php?pid=$respid1&encounter=$resenc1&provider=$provider&location=provider_portal') ><span>".htmlspecialchars( xl('Codes'), ENT_NOQUOTES)."</span></a>,";
                            }
                        }
                    }else{
                        if($priority5=='Required'){ 
                            $req_not_started_issue.=$order."<a href='javascript:;' onclick=win1('codes/feesheet_custom.php?pid=$respid1&encounter=$resenc1&provider=$provider&location=provider_portal') ><span>".htmlspecialchars( xl('Codes'), ENT_NOQUOTES)."</span></a>,";
                        }else if($priority5=='Optional'){
                            $opt_not_started_issue.=$order."<a href='javascript:;' onclick=win1('codes/feesheet_custom.php?pid=$respid1&encounter=$resenc1&provider=$provider&location=provider_portal') ><span>".htmlspecialchars( xl('Codes'), ENT_NOQUOTES)."</span></a>,";
                        }
                    }
                }
                else if($field_id5=='auditform'){
                    $resid_vt1=sqlStatement("select *  from forms where form_name  ='Audit Form' AND encounter=$resenc1 AND pid=$respid1  AND deleted=0 ORDER BY id DESC");  
                    $frow_vt1 = sqlFetchArray($resid_vt1);
                    $fid1=$frow_vt1['form_id'];
                    $resid_enc=sqlStatement("select *  from form_encounter where  encounter=$resenc1 AND pid=$respid1  ");  
                    $frow_enc = sqlFetchArray($resid_enc);

                    if($fid1!=''){
                       if($priority5=='Required'){ 
                           if(($frow_enc['elec_signedby']!='' && $frow_enc['elec_signed_on']!='' ) && $frow_enc['audited_status'] == 'Completed' ){
                               $fname_comp_issue.=$order."<a href='javascript:;' onclick=win1('auditform/new_custom.php?encounter=$resenc1&pid=$respid1&id=$fid1&provider=$rendprovider&location=provider_portal') ><span>".htmlspecialchars( xl('Audit Form'), ENT_NOQUOTES)."</span></a>,";
                           }else  {
                                 $fname_issue.=$order."<a href='javascript:;' onclick=win1('auditform/new_custom.php?encounter=$resenc1&pid=$respid1&provider=$rendprovider&location=provider_portal&id=$fid1') ><span>".htmlspecialchars( xl('Audit Form'), ENT_NOQUOTES)."</span></a>,";
                           }
                       }else if($priority5=='Optional'){
                           if(($frow_enc['elec_signedby']!='' && $frow_enc['elec_signed_on']!='' ) && $frow_enc['audited_status'] == 'Completed' ){
                               $fname_opt_issue_comp.=$order."<a href='javascript:;' onclick=win1('auditform/new_custom.php?encounter=$resenc1&pid=$respid1&id=$fid1&provider=$rendprovider&location=provider_portal') ><span>".htmlspecialchars( xl('Audit Form'), ENT_NOQUOTES)."</span></a>,";
                           }else {
                               $fname_opt_issue.=$order."<a href='javascript:;' onclick=win1('auditform/new_custom.php?encounter=$resenc1&pid=$respid1&provider=$rendprovider&location=provider_portal&id=$fid1') ><span>".htmlspecialchars( xl('Audit Form'), ENT_NOQUOTES)."</span></a>,";
                           }
                       }
                    }else{
                        if($priority5=='Required'){ 
                            $fid1=0;
                            $req_not_started_issue.=$order."<a href='javascript:;' onclick=win1('auditform/new_custom.php?encounter=$resenc1&pid=$respid1&id=$fid1&provider=$rendprovider&location=provider_portal') ><span>".htmlspecialchars( xl('Audit Form'), ENT_NOQUOTES)."</span></a>,";
                       }else if($priority5=='Optional'){
                            $fid1=0;
                            $opt_not_started_issue.=$order."<a href='javascript:;' onclick=win1('auditform/new_custom.php?encounter=$resenc1&pid=$respid1&id=$fid1&provider=$rendprovider&location=provider_portal') ><span>".htmlspecialchars( xl('Audit Form'), ENT_NOQUOTES)."</span></a>,";
                       }
                    }

                    //duplicate Entries
                   $sql1 = sqlStatement("select count(encounter) as count from  forms where deleted=0 and form_name='Audit Form' and encounter=$resenc1 and pid=$respid1");
                   $row1=sqlFetchArray($sql1);
                   if($row1['count']>1){
                       $duplicate.=$order.'Audit Form,';
                   } 
                } 
                else if($field_id5=='cpo'){

                    $resid_vt1=sqlStatement("select *  from forms where form_name  ='CPO' AND encounter=$resenc1 AND pid=$respid1  AND deleted=0 ORDER BY id DESC");  
                    $frow_vt1 = sqlFetchArray($resid_vt1);
                    $fid1=$frow_vt1['form_id'];
                    $res2=sqlstatement("select form_id  from forms where form_name ='Allcare Encounter Forms' AND encounter='$resenc1' AND pid='$respid1' AND deleted=0 order by id desc");
                    $frow_res2 = sqlFetchArray($res2);

                    if(!empty($frow_res2)){
                       $formids=$frow_res2['form_id'];
                    }else {
                        $formids=0;
                    }

                    $res1=sqlstatement("select field_value from lbf_data lb INNER JOIN layout_options l ON l.field_id=lb.field_id where lb.form_id='$formids' AND l.form_id='LBF2' AND l.group_name LIKE '%CPO%' AND lb.field_id LIKE '%_stat%' order by seq");
                    $res_row1=sqlFetchArray($res1);
                    $field_val6=explode("|",$res_row1['field_value']);

                    if($fid1!=''){

                       if($priority5=='Required'){

                            if(!empty($field_val6[0])){
                                if(in_array('pending',$field_val6) && in_array('finalized',$field_val6)){
                                     $fname_comp_issue.=$order."<a href='javascript:;' onclick=win1('/practice/cpo/new_custom.php?encounter=$resenc1&pid=$respid1&id=$fid1&location=provider_portal&formid=$formids') ><span>".htmlspecialchars( xl('CPO'), ENT_NOQUOTES)."</span></a>,";
                                }
                                elseif(in_array('pending',$field_val6) ){
                                    $fname_issue.=$order."<a href='javascript:;' onclick=win1('/practice/cpo/new_custom.php?encounter=$resenc1&pid=$respid1&id=$fid1&location=provider_portal&formid=$formids') ><span>".htmlspecialchars( xl('CPO'), ENT_NOQUOTES)."</span></a>,";
                                }else if(in_array('finalized',$field_val6)) {
                                    $fname_comp_issue.=$order."<a href='javascript:;' onclick=win1('/practice/cpo/new_custom.php?encounter=$resenc1&pid=$respid1&id=$fid1&location=provider_portal&formid=$formids') ><span>".htmlspecialchars( xl('CPO'), ENT_NOQUOTES)."</span></a>,";
                                }
                            }
                            else {
                                $req_not_started_issue.=$order."<a href='javascript:;' onclick=win1('/practice/cpo/new_custom.php?encounter=$resenc1&pid=$respid1&id=$fid1&location=provider_portal&formid=$formids') ><span>".htmlspecialchars( xl('CPO'), ENT_NOQUOTES)."</span></a>,";
                            }
                        }else if($priority5=='Optional'){
                            if(!empty($field_val6[0])){
                                if(in_array('finalized',$field_val6) && in_array('pending',$field_val6)){
                                     $fname_opt_issue_comp.=$order."<a href='javascript:;' onclick=win1('/practice/cpo/new_custom.php?encounter=$resenc1&pid=$respid1&id=$fid1&location=provider_portal&formid=$formids') ><span>".htmlspecialchars( xl('CPO'), ENT_NOQUOTES)."</span></a>,"; 
                                }
                                elseif(in_array('finalized',$field_val6)){
                                     $fname_opt_issue_comp.=$order."<a href='javascript:;' onclick=win1('/practice/cpo/new_custom.php?encounter=$resenc1&pid=$respid1&id=$fid1&location=provider_portal&formid=$formids') ><span>".htmlspecialchars( xl('CPO'), ENT_NOQUOTES)."</span></a>,";

                               }else if(in_array('pending',$field_val6)){
                                    $fname_opt_issue.=$order."<a href='javascript:;' onclick=win1('/practice/cpo/new_custom.php?encounter=$resenc1&pid=$respid1&id=$fid1&location=provider_portal&formid=$formids') ><span>".htmlspecialchars( xl('CPO'), ENT_NOQUOTES)."</span></a>,";
                               }
                            }
                            else {
                               $opt_not_started_issue.=$order."<a href='javascript:;' onclick=win1('/practice/cpo/new_custom.php?encounter=$resenc1&pid=$respid1&id=$fid1&location=provider_portal&formid=$formids') ><span>".htmlspecialchars( xl('CPO'), ENT_NOQUOTES)."</span></a>,";
                            }
                        }
                    }else {
                        if($priority5=='Required'){
                            $req_not_started_issue.=$order."<a href='javascript:;' onclick=win1('/practice/cpo/new_custom.php?encounter=$resenc1&pid=$respid1&id=0&location=provider_portal&formid=$formids') ><span>".htmlspecialchars( xl('CPO'), ENT_NOQUOTES)."</span></a>,";
                        }else if($priority5=='Optional'){
                            $opt_not_started_issue.=$order."<a href='javascript:;' onclick=win1('/practice/cpo/new_custom.php?encounter=$resenc1&pid=$respid1&id=0&location=provider_portal&formid=$formids') ><span>".htmlspecialchars( xl('CPO'), ENT_NOQUOTES)."</span></a>,";
                        }
                    }
                     //duplicate Entries
                   $sql1 = sqlStatement("select count(encounter) as count from  forms where deleted=0 and form_name='CPO' and encounter=$resenc1 and pid=$respid1");
                   $row1=sqlFetchArray($sql1);
                   if($row1['count']>1){
                       $duplicate.=$order.'CPO,';
                   }
                }
                else if($field_id5=='ccm'){

                    $resid_ccm=sqlStatement("select *  from forms where form_name  ='CCM' AND encounter=$resenc1 AND pid=$respid1  AND deleted=0 ORDER BY id DESC");  
                    $frow_ccm = sqlFetchArray($resid_ccm);
                    $fid_ccm=$frow_ccm['form_id'];
                    $res_ccm=sqlstatement("select form_id  from forms where form_name ='Allcare Encounter Forms' AND encounter='$resenc1' AND pid='$respid1' AND deleted=0 order by id desc");
                    $frow_res_ccm = sqlFetchArray($res_ccm);

                    if(!empty($frow_res_ccm)){
                       $formid_ccm=$frow_res_ccm['form_id'];
                    }else {
                        $formid_ccm=0;
                    }

                    $res1=sqlstatement("select field_value from lbf_data lb INNER JOIN layout_options l ON l.field_id=lb.field_id where lb.form_id='$formid_ccm' AND l.form_id='LBF2' AND l.group_name LIKE '%CCM%' AND lb.field_id LIKE '%_stat%' order by seq");
                    $res_row1=sqlFetchArray($res1);
                    $field_val_ccm=explode("|",$res_row1['field_value']);

                    if($fid_ccm!=''){

                        if($priority5=='Required'){

                             if(!empty($field_val_ccm[0])){
                                  if(in_array('pending',$field_val_ccm) && in_array('finalized',$field_val_ccm)){
                                       $fname_comp_issue.=$order."<a href='javascript:;' onclick=win1('/practice/ccm/new_custom.php?encounter=$resenc1&pid=$respid1&id=$fid_ccm&location=provider_portal&formid=$formid_ccm') ><span>".htmlspecialchars( xl('CCM'), ENT_NOQUOTES)."</span></a>,";
                                  }
                                  elseif(in_array('pending',$field_val_ccm) ){
                                        $fname_issue.=$order."<a href='javascript:;' onclick=win1('/practice/ccm/new_custom.php?encounter=$resenc1&pid=$respid1&id=$fid_ccm&location=provider_portal&formid=$formid_ccm') ><span>".htmlspecialchars( xl('CCM'), ENT_NOQUOTES)."</span></a>,";
                                  }else if(in_array('finalized',$field_val_ccm)) {
                                      $fname_comp_issue.=$order."<a href='javascript:;' onclick=win1('/practice/ccm/new_custom.php?encounter=$resenc1&pid=$respid1&id=$fid_ccm&location=provider_portal&formid=$formid_ccm') ><span>".htmlspecialchars( xl('CCM'), ENT_NOQUOTES)."</span></a>,";
                                  }
                             }
                             else {
                                 $req_not_started_issue.=$order."<a href='javascript:;' onclick=win1('/practice/ccm/new_custom.php?encounter=$resenc1&pid=$respid1&id=$fid_ccm&location=provider_portal&formid=$formid_ccm') ><span>".htmlspecialchars( xl('CCM'), ENT_NOQUOTES)."</span></a>,";
                             }
                        }else if($priority5=='Optional'){

                            if(!empty($field_val_ccm[0])){
                                 if(in_array('finalized',$field_val_ccm) && in_array('pending',$field_val_ccm)){
                                      $fname_opt_issue_comp.=$order."<a href='javascript:;' onclick=win1('/practice/ccm/new_custom.php?encounter=$resenc1&pid=$respid1&id=$fid_ccm&location=provider_portal&formid=$formid_ccm') ><span>".htmlspecialchars( xl('CCM'), ENT_NOQUOTES)."</span></a>,"; 
                                }
                                elseif(in_array('finalized',$field_val_ccm)){
                                      $fname_opt_issue_comp.=$order."<a href='javascript:;' onclick=win1('/practice/ccm/new_custom.php?encounter=$resenc1&pid=$respid1&id=$fid_ccm&location=provider_portal&formid=$formid_ccm') ><span>".htmlspecialchars( xl('CCM'), ENT_NOQUOTES)."</span></a>,";

                                }else if(in_array('pending',$field_val_ccm)){
                                     $fname_opt_issue.=$order."<a href='javascript:;' onclick=win1('/practice/ccm/new_custom.php?encounter=$resenc1&pid=$respid1&id=$fid_ccm&location=provider_portal&formid=$formid_ccm') ><span>".htmlspecialchars( xl('CCM'), ENT_NOQUOTES)."</span></a>,";
                                }
                            }
                            else {
                                $opt_not_started_issue.=$order."<a href='javascript:;' onclick=win1('/practice/ccm/new_custom.php?encounter=$resenc1&pid=$respid1&id=$fid_ccm&location=provider_portal&formid=$formid_ccm') ><span>".htmlspecialchars( xl('CCM'), ENT_NOQUOTES)."</span></a>,";
                            }
                        }
                    }else {
                        if($priority5=='Required'){
                            $req_not_started_issue.=$order."<a href='javascript:;' onclick=win1('/practice/ccm/new_custom.php?encounter=$resenc1&pid=$respid1&id=0&location=provider_portal&formid=$formid_ccm') ><span>".htmlspecialchars( xl('CCM'), ENT_NOQUOTES)."</span></a>,";

                        }else if($priority5=='Optional'){
                           $opt_not_started_issue.=$order."<a href='javascript:;' onclick=win1('/practice/ccm/new_custom.php?encounter=$resenc1&pid=$respid1&id=0&location=provider_portal&formid=$formid_ccm') ><span>".htmlspecialchars( xl('CCM'), ENT_NOQUOTES)."</span></a>,";
                        }
                    }

                    //duplicate Entries
                    $sql1 = sqlStatement("select count(encounter) as count from  forms where deleted=0 and form_name='CCM' and encounter=$resenc1 and pid=$respid1");
                    $row1=sqlFetchArray($sql1);
                    if($row1['count']>1){
                        $duplicate.=$order.'CCM,';
                    }
                }
                else if($field_id5=='cert_recert'){ 
                    $resid_his=sqlStatement("select form_id  from forms where  pid=$respid1 AND encounter=$resenc1 AND deleted = 0 and formdir='LBF2' order by id DESC ");  
                    $frow_his = sqlFetchArray($resid_his);
                    $gname = 'Certification_Recertification';
                    if(!empty($frow_his)){ 
                       $qa_stat=sqlStatement("select field_value  from lbf_data WHERE form_id = '".$frow_his['form_id'] ."' AND field_id = 'cert_recert_stat' ");  
                       $qaform_id = $frow_his['form_id'] ;
                       $qa_stat_res = sqlFetchArray($qa_stat);
                       if($priority5=='Required'){ 
                            if( strpos('finalized',$qa_stat_res['field_value']) !== false  ){
                                $fname_comp_issue.=$order."<a href='javascript:;' onclick=win1('allcare_encounter_forms/allcare_enc_forms.php?groupname=$gname&pid1=$respid1&enc3=$resenc1&provider=$provider&inmode1=edit&file_name1=Incomplete_charts&form_id1=$qaform_id&location=provider_portal&fname1=$resfname3') ><span>".htmlspecialchars( xl('Certification/Recertification'), ENT_NOQUOTES)."</span></a>,";
                            }else if( strpos('pending',$qa_stat_res['field_value']) !== false  ) {
                                $fname_issue.=$order."<a href='javascript:;' onclick=win1('allcare_encounter_forms/allcare_enc_forms.php?groupname=$gname&pid1=$respid1&enc3=$resenc1&provider=$provider&inmode1=edit&file_name1=Incomplete_charts&form_id1=$qaform_id&location=provider_portal&fname1=$resfname3') ><span>".htmlspecialchars( xl('Certification/Recertification'), ENT_NOQUOTES)."</span></a>,";
                            }else{
                                $fname_comp_issue.=$order."<a href='javascript:;' onclick=win1('allcare_encounter_forms/allcare_enc_forms.php?groupname=$gname&pid1=$respid1&enc3=$resenc1&provider=$provider&inmode1=edit&file_name1=Incomplete_charts&form_id1=$qaform_id&location=provider_portal&fname1=$resfname3') ><span>".htmlspecialchars( xl('Certification/Recertification'), ENT_NOQUOTES)."</span></a>,";
                            }
                       }else if($priority5=='Optional'){
                            if(strpos('finalized',$qa_stat_res['field_value']) !== false){
                                $fname_opt_issue_comp.=$order."<a href='javascript:;' onclick=win1('allcare_encounter_forms/allcare_enc_forms.php?groupname=$gname&pid1=$respid1&enc3=$resenc1&provider=$provider&inmode1=edit&file_name1=Incomplete_charts&form_id1=$qaform_id&location=provider_portal&fname1=$resfname3') ><span>".htmlspecialchars( xl('Certification/Recertification'), ENT_NOQUOTES)."</span></a>,";
                            }else if( strpos('pending',$qa_stat_res['field_value']) !== false  ) {
                                $fname_opt_issue.=$order."<a href='javascript:;' onclick=win1('allcare_encounter_forms/allcare_enc_forms.php?groupname=$gname&pid1=$respid1&enc3=$resenc1&provider=$provider&inmode1=edit&file_name1=Incomplete_charts&form_id1=$qaform_id&location=provider_portal&fname1=$resfname3') ><span>".htmlspecialchars( xl('Certification/Recertification'), ENT_NOQUOTES)."</span></a>,";
                            }else{
                                $opt_not_started_issue.=$order."<a href='javascript:;' onclick=win1('allcare_encounter_forms/allcare_enc_forms.php?groupname=$gname&pid1=$respid1&enc3=$resenc1&provider=$provider&inmode1=edit&file_name1=Incomplete_charts&form_id1=$qaform_id&location=provider_portal&fname1=$resfname3') ><span>".htmlspecialchars( xl('Certification/Recertification'), ENT_NOQUOTES)."</span></a>,";
                            }
                       }
                    }else{
                        if($priority5=='Required'){ 
                            $opt_not_started_issue.=$order."<a href='javascript:;' onclick=win1('allcare_encounter_forms/allcare_enc_forms.php?groupname=$gname&pid1=$respid1&enc3=$resenc1&provider=$provider&inmode1=edit&file_name1=Incomplete_charts&form_id1=0&location=provider_portal&fname1=$resfname3') ><span>".htmlspecialchars( xl('Certification/Recertification'), ENT_NOQUOTES)."</span></a>,";

                        }else if($priority5=='Optional'){ 
                            $opt_not_started_issue.=$order."<a href='javascript:;' onclick=win1('allcare_encounter_forms/allcare_enc_forms.php?groupname=$gname&pid1=$respid1&enc3=$resenc1&provider=$provider&inmode1=edit&file_name1=Incomplete_charts&form_id1=0&location=provider_portal&fname1=$resfname3') ><span>".htmlspecialchars( xl('Certification/Recertification'), ENT_NOQUOTES)."</span></a>,";
                        }
                    }

                    //duplicate Entries
                    $sql1 = sqlStatement("select count(encounter) as count from  forms where deleted=0 and form_name='Allcare Encounter Forms' and encounter=$resenc1 and pid=$respid1");
                    $row1=sqlFetchArray($sql1);
                    if($row1['count']>1){
                        $sql2=sqlStatement("select * from forms where form_name='Allcare Encounter Forms' and encounter=$resenc1 and pid=$respid1");
                        $f=0; $cnt=0;
                        while ($row2=sqlFetchArray($sql2)) {
                           $sql3 = sqlStatement("select * from lbf_data  where field_id LIKE  'cert_recert%'  AND form_id='".$row2['form_id']."'");
                            $rowcount=mysql_num_rows($sql3); 
                            if($rowcount!=0){
                                $cnt+=1;
                                if($cnt==2){
                                    if($f==0){
                                     $duplicate.=$order.'Certification/Recertification'.",";
                                     $f++;
                                    }
                                }
                            }
                        }
                    }
                }
            }
            if($fname_issue!=''){
                $fname2.=$fname_issue;
            }
            if($fname_opt_issue!=''){
                $fname_opt2.=$fname_opt_issue;
            }
            if($fname_comp_issue!=''){
                $fname_comp2.=$fname_comp_issue;
            }
            if($fname_opt_issue_comp!=''){
                $fname_opt_comp2.=$fname_opt_issue_comp;
            }
            if($opt_not_started_issue!=''){
                $opt_not_started2.=$opt_not_started_issue;
            }
            if($req_not_started_issue!=''){
                $req_not_started2.=$req_not_started_issue;
            }
        }
    }


    $fname1=rtrim($fname2,",");
    $fname_comp1=rtrim($fname_comp2,",");
    $fname_opt1=rtrim($fname_opt2,",");
    $fname_opt_comp1=rtrim($fname_opt_comp2,",");
    $req_not_started1=rtrim($req_not_started2,",");
    $opt_not_started1=rtrim($opt_not_started2,",");


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
                }else if($field13=='rend_provider'){
                    $field_val.="f.rendering_provider AS ".$field13.",";
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
                }elseif($field13=='assign_by'){
                     $field_val.=" f.audited_by ,";
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
            $frow2[$req_comp]=$fname_comp1;
            $frow2[$req_incomp]=$fname1;
            $frow2[$opt]=$fname_opt1;
            $frow2[$opt_comp]=$fname_opt_comp1;
            $frow2[$req_notstarted]=$req_not_started1;
            $frow2[$opt_notstarted]=$opt_not_started1;
            $frow2['dup_entry']=$duplicate;


            foreach($frow2 as $key => $svalue){
                if($key=='req_forms_notstarted'){
                    $sort=explode(",",$svalue);
                    foreach($sort as $key1 => $svalue1){
                        $sort1=explode("$",$svalue1);
                        if(is_numeric($sort1[0])){
                             $fnam_sort[$svalue1]=$sort1[0];
                        }else{
                             $fnam_sort[$svalue1]=0;
                        }

                     }
                    asort($fnam_sort);
                    foreach($fnam_sort as $key2 => $svalue2){
                        $req_not_started4.=str_replace("$",".",$key2)."<br>";
                    } 

                   $frow3[$req_notstarted]=$req_not_started4;
                }else if($key=='opt_forms_notstarted'){
                    $sort_notstarted=explode(",",$svalue);
                    foreach($sort_notstarted as $key9 => $svalue9){
                        $sort9=explode("$",$svalue9);
                        if(is_numeric($sort9[0])){
                             $opt_not[$svalue9]=$sort9[0];
                        }else {
                            $opt_not[$svalue9]=0;
                        }

                     }
                    asort($opt_not);
                    foreach($opt_not as $key_opt => $svalue_opt){
                        $opt_not_started4.=str_replace("$",".",$key_opt)."<br>";
                    } 

                    $frow3[$opt_notstarted]=$opt_not_started4;
                }else if($key=='optional_forms_comp'){
                    $sort_forms_comp=explode(",",$svalue);
                    foreach($sort_forms_comp as $key8 => $svalue8){
                        $sort8=explode("$",$svalue8);
                        if(is_numeric($sort8[0])){
                            $opt_forms_com[$svalue8]=$sort8[0];
                        }else {
                            $opt_forms_com[$svalue8]=0;
                        }

                     }
                    asort($opt_forms_com);
                    foreach($opt_forms_com as $key_optcom => $svalue_optcom){
                        $fname_opt_comp4.=str_replace("$",".",$key_optcom)."<br>";
                    } 

                    $frow3[$opt_comp]=$fname_opt_comp4;
                }else if($key=='optional_forms'){
                    $sort_optional_forms=explode(",",$svalue);
                    foreach($sort_optional_forms as $key7 => $svalue7){
                        $sort7=explode("$",$svalue7);
                        if(is_numeric($sort7[0])){
                            $opt_forms_com7[$svalue7]=$sort7[0];
                        }else {
                            $opt_forms_com7[$svalue7]=0;
                        }

                     }
                    asort($opt_forms_com7);
                    foreach($opt_forms_com7 as $key_optincom => $svalue_optincom){
                        $fname_opt4.=str_replace("$",".",$key_optincom)."<br>";
                    } 

                    $frow3[$opt]=$fname_opt4;
                }else if($key=='req_forms_incomplete'){
                    $sort_forms_incomplete=explode(",",$svalue);
                    foreach($sort_forms_incomplete as $key6 => $svalue6){
                        $sort6=explode("$",$svalue6);
                        if(is_numeric($sort6[0])){
                            $req_forms_com6[$svalue6]=$sort6[0];
                        }else {
                            $req_forms_com6[$svalue6]=0;
                        }

                     }
                    asort($req_forms_com6);
                    foreach($req_forms_com6 as $key_reqincom => $svalue_reqincom){
                        $fname4.=str_replace("$",".",$key_reqincom)."<br>";
                    } 

                    $frow3[$req_incomp]=$fname4;
                }else if($key=='req_forms_comp'){
                    $sort_req_comp=explode(",",$svalue);
                    foreach($sort_req_comp as $key5 => $svalue5){
                        $sort5=explode("$",$svalue5);
                        if(is_numeric($sort5[0])){
                             $req_forms_com5[$svalue5]=$sort5[0];
                        }else {
                             $req_forms_com5[$svalue5]=0;
                        }

                     }
                    asort($req_forms_com5);
                    foreach($req_forms_com5 as $key_reqcom => $svalue_reqcom){
                        $fname_comp4.=str_replace("$",".",$key_reqcom)."<br>";
                    } 

                    $frow3[$req_comp]=$fname_comp4;
                }else if($key=='dup_entry'){
                    $sort_dup_entry=explode(",",$svalue);
                    foreach($sort_dup_entry as $key4 => $svalue4){
                        $sort4=explode("$",$svalue4);
                        if(is_numeric($sort4[0])){
                            $dup_entry4[$svalue4]=$sort4[0];
                        }else {
                            $dup_entry4[$svalue4]=0;
                        }

                     }
                    asort($dup_entry4);

                    foreach($dup_entry4 as $key_dup => $svalue_dup){
                        $duplicate4.=str_replace("$",".",$key_dup)."<br>";
                    } 

                    $frow3['dup_entry']=$duplicate4;
                }
            }
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
    else{
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
            }elseif($field13=='assign_by'){
                 $field_val.=" f.audited_by ,";
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
            $frow2[$req_comp]=$fname_comp1;
            $frow2[$req_incomp]=$fname1;
            $frow2[$opt]=$fname_opt1;
            $frow2[$opt_comp]=$fname_opt_comp1;
            $frow2[$req_notstarted]=$req_not_started1;
            $frow2[$opt_notstarted]=$opt_not_started1;
            $frow2['dup_entry']=$duplicate;


            foreach($frow2 as $key => $svalue){
                if($key=='req_forms_notstarted'){
                     $sort=explode(",",$svalue);
                     foreach($sort as $key1 => $svalue1){
                         $sort1=explode("$",$svalue1);
                         if(is_numeric($sort1[0])){
                              $fnam_sort[$svalue1]=$sort1[0];
                         }else{
                              $fnam_sort[$svalue1]=0;
                         }

                      }
                     asort($fnam_sort);
                     foreach($fnam_sort as $key2 => $svalue2){
                         $req_not_started4.=str_replace("$",".",$key2)."<br>";
                     } 

                   $frow3[$req_notstarted]=$req_not_started4;
                }else if($key=='opt_forms_notstarted'){
                     $sort_notstarted=explode(",",$svalue);
                     foreach($sort_notstarted as $key9 => $svalue9){
                         $sort9=explode("$",$svalue9);
                         if(is_numeric($sort9[0])){
                              $opt_not[$svalue9]=$sort9[0];
                         }else {
                             $opt_not[$svalue9]=0;
                         }

                      }
                     asort($opt_not);
                     foreach($opt_not as $key_opt => $svalue_opt){
                         $opt_not_started4.=str_replace("$",".",$key_opt)."<br>";
                     } 

                    $frow3[$opt_notstarted]=$opt_not_started4;
                }else if($key=='optional_forms_comp'){
                     $sort_forms_comp=explode(",",$svalue);
                     foreach($sort_forms_comp as $key8 => $svalue8){
                         $sort8=explode("$",$svalue8);
                         if(is_numeric($sort8[0])){
                             $opt_forms_com[$svalue8]=$sort8[0];
                         }else {
                             $opt_forms_com[$svalue8]=0;
                         }

                      }
                     asort($opt_forms_com);
                     foreach($opt_forms_com as $key_optcom => $svalue_optcom){
                         $fname_opt_comp4.=str_replace("$",".",$key_optcom)."<br>";
                     } 

                    $frow3[$opt_comp]=$fname_opt_comp4;
                }else if($key=='optional_forms'){
                     $sort_optional_forms=explode(",",$svalue);
                     foreach($sort_optional_forms as $key7 => $svalue7){
                         $sort7=explode("$",$svalue7);
                         if(is_numeric($sort7[0])){
                             $opt_forms_com7[$svalue7]=$sort7[0];
                         }else {
                             $opt_forms_com7[$svalue7]=0;
                         }

                      }
                     asort($opt_forms_com7);
                     foreach($opt_forms_com7 as $key_optincom => $svalue_optincom){
                         $fname_opt4.=str_replace("$",".",$key_optincom)."<br>";
                     } 

                    $frow3[$opt]=$fname_opt4;
                }else if($key=='req_forms_incomplete'){
                     $sort_forms_incomplete=explode(",",$svalue);
                     foreach($sort_forms_incomplete as $key6 => $svalue6){
                         $sort6=explode("$",$svalue6);
                         if(is_numeric($sort6[0])){
                             $req_forms_com6[$svalue6]=$sort6[0];
                         }else {
                             $req_forms_com6[$svalue6]=0;
                         }

                      }
                     asort($req_forms_com6);
                     foreach($req_forms_com6 as $key_reqincom => $svalue_reqincom){
                         $fname4.=str_replace("$",".",$key_reqincom)."<br>";
                     } 

                    $frow3[$req_incomp]=$fname4;
                }else if($key=='req_forms_comp'){
                     $sort_req_comp=explode(",",$svalue);
                     foreach($sort_req_comp as $key5 => $svalue5){
                         $sort5=explode("$",$svalue5);
                         if(is_numeric($sort5[0])){
                              $req_forms_com5[$svalue5]=$sort5[0];
                         }else {
                              $req_forms_com5[$svalue5]=0;
                         }

                      }
                     asort($req_forms_com5);
                     foreach($req_forms_com5 as $key_reqcom => $svalue_reqcom){
                         $fname_comp4.=str_replace("$",".",$key_reqcom)."<br>";
                     } 

                    $frow3[$req_comp]=$fname_comp4;
                }else if($key=='dup_entry'){
                     $sort_dup_entry=explode(",",$svalue);
                     foreach($sort_dup_entry as $key4 => $svalue4){
                         $sort4=explode("$",$svalue4);
                         if(is_numeric($sort4[0])){
                             $dup_entry4[$svalue4]=$sort4[0];
                         }else {
                             $dup_entry4[$svalue4]=0;
                         }

                      }
                     asort($dup_entry4);

                     foreach($dup_entry4 as $key_dup => $svalue_dup){
                         $duplicate4.=str_replace("$",".",$key_dup)."<br>";
                     } 

                    $frow3['dup_entry']=$duplicate4;
                }
           }
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
                if($value5=='Completed'){
                    if(in_array('Complete',$frow3)){
                        $frow31=$frow3;
                    }
                }
                if($value5=='Incomplete'){
                    if(in_array('Incomplete',$frow3)){
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
            <link rel="stylesheet" href="css/version1.0/dataTables.bootstrap.min.css"/>
            <link rel="stylesheet" href="css/version1.0/responsive.bootstrap.min.css"/>
            <link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/themes/redmond/jquery-ui.css" /> 
            <link rel="stylesheet" href="css/pqselect.min.css"/>
            <link rel="stylesheet" href="fancybox/source/jquery.fancybox.css" type="text/css" media="screen" />
            <script src="js/responsive_datatable/version1.0/jquery-1.11.3.min.js"></script>
            <script src="https://code.jquery.com/ui/1.10.2/jquery-ui.js"></script>
            <script src="js/pqselect.min.js"></script>
            <script src="js/responsive_datatable/version1.0/jquery.dataTables.min.js"></script>
            <script src="js/responsive_datatable/version1.0/dataTables.bootstrap.min.js"></script>
            <script src="js/responsive_datatable/version1.0/dataTables.responsive.min.js"></script>
            <script type='text/javascript' src='js/responsive_datatable/dataTables.tableTools.js'></script>
            <script type='text/javascript' src='js/responsive_datatable/dataTables.bootstrap.js'></script>         
            <script type="text/javascript" src="fancybox/source/jquery.fancybox.pack.js"></script>
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
                        .panel-heading{
                            padding: 0;
                        }
                        a:hover{
                          text-decoration: none;
                        }
                        .panel-title{
                            display: block;
                            padding: 10px 15px;
                            text-decoration: none;
                        }
                        .form-control, .input-group-addon {
                            border-radius: 4px;
                        }
                        .input-group-addon{
                             padding: 0 6px !important;
                        }
                        .panel-heading a:after {
                            font-family:'Glyphicons Halflings';
                            content:"\e114";
                            float: right;
                            color: grey;
                        }
                        .panel-heading a.collapsed:after {
                            content:"\e080";
                        }
                        .topspacing{
                            margin-top: 15px;
                        }
                        .calendar{
                            z-index: 2;
                        }
                        .encountercell > td{
                            background: #ccffff !important;
                        }
                        .encountercell + .child >td{
                            background: #ccffff !important;
                        }
                        table.dataTable > tbody > .encountercell + tr.child:hover {
                            background: #ccffff !important;
                        }
                        #services {
                            margin-bottom: -45px;
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

        function DoPost(page_name, provider,refer) {
            method = "post"; // Set method to post by default if not specified.

            var form = document.createElement("form");
            form.setAttribute("method", method);
            form.setAttribute("action", page_name);
            var key='provider';
            var hiddenField = document.createElement("input");
            hiddenField.setAttribute("type", "hidden");
            hiddenField.setAttribute("name", key);
            hiddenField.setAttribute("value", provider);
            form.appendChild(hiddenField);

            var key1='refer';
            var hiddenField1 = document.createElement("input");
            hiddenField1.setAttribute("type", "hidden");
            hiddenField1.setAttribute("name", key1);
            hiddenField1.setAttribute("value", refer);
            form.appendChild(hiddenField1);

           document.body.appendChild(form);
            form.submit();
        } 
        </script>       

	</head>

	<body>
            <?php //include 'header_nav.php'; ?>
           <script type='text/javascript'>
                function reset(){
                    $("#dropdown_filters").reset();
                }
                function submitme2() {
                   var string1='';
                   $( ".order" ).each(function( index ) {
                     console.log( index + ": " + $( this ).text() );

                     string1 += index + ":" +$( this ).text()+",";
                    });

                    var string= string1.substring(0,string1.lastIndexOf(","));
                    document.getElementById("col").value = string;  
                    document.getElementById("menu_val").value = 'incomplete_encouner';  
                    var f = document.forms['dropdown_filters'];
                    f.submit();

               }
            
           
            function previewpost1(enc,pid,date1){
                var datefield = date1.trim();
                <?php $mobile_sql=sqlStatement("SELECT * 
                          FROM  `tbl_chartui_mapping` 
                          WHERE form_id =  'CHARTOUTPUT'
                          AND group_name LIKE  '%General%'
                          AND screen_name LIKE  '%General%'");
                while($mob_row1=sqlFetchArray($mobile_sql)){

                    $field_id='form_'.$mob_row1['field_id'];
                    $field_value=$mob_row1['option_value']; 
                    $res.=$field_id.'='.$field_value.'&'; ?>

                <?php  } ?>

                var datastring='<?php echo $res; ?>'+'patientid'+'='+pid+'&'+'encounter_id'+'='+enc+'&'+'dos'+'='+date1+'&'+'chartgroupshidden'+'='+'2General';
                window.open('chartoutput/preview_charts.php?'+datastring,'popup','width=900,height=900,scrollbars=no,resizable=yes');
            }
        function obj_type(enc,pid){
          // alert(enc+"=="+pid);
            var value = $("#who_type").val();

            $.ajax({
                type: 'POST',
                url: "chartoutput/who_type.php",	
                data:{type:value,fid:'incomplete',enc:enc,pid:pid},
                success: function(response)
                {

                 $('#who').html(response);

                },
                failure: function(response)
                {
                    alert("error"); 
                }		
            });	
        }
        function postValue(enc1,pid1,date2,grp,abook,finalize_dt){  
            date2 = date2.trim();
                
            <?php
                // get duration count
                $duration_count = 0;
                $getduration = sqlStatement("SELECT title FROM list_options WHERE list_id='allcareConfig' and option_id='rangeduration'");
                while ($getduration_value = mysql_fetch_array($getduration)) {
                    $duration_count = $getduration_value['title'];
                }
                $is_blocked = 'no';
                // checking if the finalize has to be blocked as per duration or not
                $blockfinalize = sqlStatement("SELECT title FROM list_options WHERE list_id='allcareConfig' and option_id='blockfinalizenotwithinrange'");
                while ($blockfinalize_value = mysql_fetch_array($blockfinalize)) {
                    $is_blocked = $blockfinalize_value['title'];
                }


                $grp_arr=array();
                $groups = sqlStatement("SELECT DISTINCT(group_name ) as group_name FROM layout_options " .
                                       "WHERE form_id = 'CHARTOUTPUT' AND uor > 0 " .
                                       "ORDER BY group_name");
                while ($res_grp = sqlFetchArray($groups)) {
                    $gvalue=$res_grp['group_name'];
                    $gtitle=substr($res_grp['group_name'],1);
                    $grp_arr[$gvalue]=$gtitle;
                }

                 $ures = sqlStatement("SELECT * 
                         FROM  `list_options` 
                         WHERE list_id =  'abook_type'
                         ");
                ?>

                var days = <?php echo $duration_count ; ?>; 
                var actualDate = new Date(date2);
                var newDate = new Date(actualDate.getFullYear(), actualDate.getMonth(), actualDate.getDate()+days);
                
                var getdatevalue;
                if(newDate.getDate()<10){
                    getdatevalue = '0'+newDate.getDate();
                } else{
                    getdatevalue = newDate.getDate();
                }
                
                var getmonthvalue;
                 var mnth=newDate.getMonth()+1;
                if(mnth<10){
                    getmonthvalue = '0'+mnth;
                } else{
                    getmonthvalue = mnth;
                }
                
                var maxdate =  newDate.getFullYear() + "-" + (getmonthvalue) + "-" + getdatevalue;
                
                var isblock = "<?php echo trim($is_blocked); ?>" ;

                var displaydata = '';
                if(isblock == 'yes' || isblock == 'YES' || isblock == 'Yes'){
                    displaydata = "<p style='font-size:100%;font-family:verdana;'>You can select the date within range only. Please select from "+ date2 + " to "+ maxdate +".<br/></p>";
                }else{
                    displaydata = "<p style='font-size:100%;font-family:verdana;'>Please select date to finalize the encounter.<br/></p>";
                }
                if(document.getElementById('finalize_'+enc1).checked){ 
                    var finalize_val = document.getElementById('finalize_'+enc1).checked; 
                    var htmlStr = "<form action='' method='post'>"+displaydata+"Date: <input type='date' name='elec_date' id='elec_date' min='"+date2+"'";
                    
                    if(isblock == 'yes' || isblock == 'YES' || isblock == 'Yes'){
                        htmlStr += "max='"+maxdate+"'";
                    }
                    
                    htmlStr += "value="+finalize_dt+" required /><input type='hidden' name='enc1' id='enc1' value='"+enc1+"' /><input type='hidden' name='date2' id='date2' value='"+date2+"' /><input type='hidden' name='pid1' id='pid1' value='"+pid1+"' /><input type='hidden' name='grp' id='grp' value='"+grp+"' /><br /><br>"; 
                    htmlStr += "Who Type:<select id='who_type' name = 'who_type'  onchange='obj_type("+enc1+","+pid1+");'><option value=''>Select</option>";
                    htmlStr += "<optgroup label='Address Book Type'>";
                    <?php 
                         while ($ures21=sqlFetchArray($ures)) {
                            ?>  htmlStr += "<option value='<?php echo $ures21['option_id']; ?>'";
                                
                                 var checkabook = '<?php echo $ures21['option_id']; ?>';
                                 if(checkabook == abook)
                                     htmlStr += "selected";
                               htmlStr += " > <?php echo  $ures21['title']; ?></option> ";<?php
                        }
                        ?>
                    htmlStr +="</optgroup><option value='provider'>Provider</option><option value='facility'>Facility</option><option value='pharmacy'>Pharmacy</option><option value='payer'>Payer</option>";
                    htmlStr += "</select><br/><br /> <div id='who'></div><br />";
                    if(grp==''){
                        htmlStr += "Group: <select id='cht_grp' name='cht_grp'  ><option value=''>Select</option>";
                        <?php 
                        foreach($grp_arr as $gid => $gval){
                            ?>  htmlStr += "<option value='<?php echo $gid; ?>'> <?php echo $gval; ?></option> ";<?php
                        }
                        ?>
                        htmlStr += "</select><br /> <br />";   
                    }
                    
                    htmlStr += "<input type='submit' /></form>";
                    
                    $.fancybox(htmlStr, {
                        'width': 950,
                        'height': 1500,
                        'autoScale': false,
                        'transitionIn': 'none',
                        'transitionOut': 'none',
                        'hideOnOverlayClick':false,
                        'helpers'     : { 
                            overlay : {closeClick: false} // prevents closing when clicking OUTSIDE fancybox
                        }
                        });
                            var value =jQuery('#who_type').val(); 
                            if(value!='') {
                                $.ajax({
                                    type: 'POST',
                                    url: "chartoutput/who_type.php",	
                                    data:{type:value,fid:'incomplete',enc:enc1,pid:pid1},
                                    success: function(response)
                                    {
                                     alert(response);    
                                     $('#who').html(response);

                                    },
                                    failure: function(response)
                                    {
                                        alert("error"); 
                                    }		
                               });	
                            }   
                }  
                
                else {
                  //var finalize_val=document.getElementById('finalize').checked; alert(finalize_val); 
                }
            }

            function viewpost1(id,pid1,group){
                var datastring='coid'+'='+id+'&'+'pid'+'='+pid1+'&'+'group'+'='+group;
                window.open('chartoutput/print_charts.php?'+datastring,'popup','width=900,height=900,scrollbars=no,resizable=yes');
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
                OpenWindowWithPost(res[0], "width=1000, height=600, left=0, top=100, resizable=yes, scrollbars=yes", "NewFile", dict);
            }
   </script>
    <section id= "services">
        <div class= "container-fluid">
            <div class= "row">
<!--                <div class= "col-lg-12 col-sm-12 col-xs-12" style='padding-top:100px !important;'>-->
                <div class= "col-lg-12 col-sm-12 col-xs-12">    
                <?php 
                $display_style='block' ;
                                    
                $resid22="SELECT DISTINCT fe.encounter,fe.pid, fe.audited_status
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
                    
                    if(!empty($id)){
                        $resid22 .=  " AND ( fe.provider_id ='".$id['id']."' OR fe.rendering_provider='".$id['id']."')";
                    }
//                    if(!empty($af_providers)){
//                        $resid22.=  " AND ( fe.provider_id IN (".$af_providers.") OR fe.rendering_provider IN (".$af_providers."))";
//                    }
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
                                 $resid22.=  " AND ((fe.elec_signedby ='' AND fe.elec_signed_on ='') or (fe.elec_signedby !='' AND fe.elec_signed_on ='') or (fe.elec_signedby ='' AND fe.elec_signed_on !=''))";
                               }
                           }
                        }
                    }

                    if($from!='' && $to!=''){
                       $resid22.=  " AND DATE_FORMAT(fe.date, '%Y-%m-%d') BETWEEN CAST('$from' AS DATE) AND CAST('$to' AS DATE)  ";
                    }
                    //facility
                    if(empty($provider)){
                        $get_provider_name = sqlStatement("SELECT username FROM users WHERE id = '$id1'");
                        while($set_provider_name = sqlFetchArray($get_provider_name)){
                            $provider = $set_provider_name['username'];
                        }
                    }
                    $get_facilitysql      = sqlStatement("select facility from incomplete_charts_filter where loginuser='$provider' order by id desc");
                    $set_facilitysql      = sqlFetchArray($get_facilitysql);
                    $facility           = explode("|",$set_facilitysql['facility']);
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

                    //audit_status
                    if(!empty($audit_stat)){
                       foreach($audit_stat as $value10){
                           if($value10!=''){
                            $audit_vall0.="'".$value10."'".",";
                           }
                       }
                       $audit_val110=trim($audit_vall0,',');
                       if($audit_val110!=''){
                           if(strpos($audit_val110, 'Incomplete')==true){
                                $resid22.=  " AND fe.audited_status IN ($audit_val110,'','New','In Process','Pending')";
                            }else{
                                $resid22.=  " AND fe.audited_status IN ($audit_val110)";
                            }
                       }
                    }
                     //Assigned by
                    if(!empty($assigned_by)){
                        $assign_val='';
                        foreach($assigned_by as $value56){
                            if($value56!=''){
                             $assign_val.=$value56.",";
                            }
                        }
                       $assign_val1=trim($assign_val,',');
                        if($assign_val1!=''){
                            $resid22.=  " AND fe.audited_by IN ($assign_val1)";
                        }
                    }

                    //echo $resid22;
                    $resid23=sqlStatement($resid22);
                    $rowcount=mysql_num_rows($resid23);     
                    while ($au = sqlFetchArray($resid23)) {
                        if($au['audited_status']=='Completed'){
                           $enc=$au['encounter'];
                           $sql12=sqlStatement("select * from forms where form_name='Audit Form' AND deleted=0 AND encounter='$enc'");
                            $audit_res2=sqlFetchArray($sql12);
                            if(empty($audit_res2)){
                                $rowcount=$rowcount-1;
                            }
                        }
                    }
                    ?>
                    <div class="panel panel-info">
                        <div class="panel-heading"><a data-toggle="collapse" href="#collapse1" class="panel-title"><b>Filters</b></a></div>
                        <div class=" panel-collapse collapse" id="collapse1">
                            <div class="panel-body">
                                <form name="dropdown_filters" id="dropdown_filters" role="form" class="form-horizontal" action="provider_incomplete_charts.php" method="POST" >
                                    <input type='hidden'  id='mode1' name='mode1' value='add'/>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label" for="fee_provider"><?php xl('Fee Provider','e'); ?>:</label>
                                                <div class="col-sm-4 input-group-sm">
                                                    <select name='fee_provider'  id='fee_provider' class='form-control'>
                                                        <?php
    //                                                        while ($urow = sqlFetchArray($af_providers2)) {
    //                                                               if($urow['lname']!='' && $urow['fname']!=''){
    //                                                                    $provid = $urow['id'];
    //                                                                    echo "    <option value='$provid'";
    //                                                                    echo " selected";
    //                                                                    echo ">" . $urow['lname'] . ", " . $urow['fname'] . "\n";
    //                                                               }
    //                                                         }
                                                        if(!empty($providername)){
                                                            foreach($providername as $provider_idsub => $providername_sub){
                                                                echo "<option value ='$provider_idsub' ";
                                                                //if( in_array ( $provider_idsub, $af_providers2) == true )
                                                                if($provider_idsub == $feeprovider) echo " selected ";
                                                                echo "> $providername_sub </option>";  
                                                            }
                                                        }
                                                    ?>
                                                    </select>
                                                </div>
                                                <label class="col-sm-2 control-label" for="rendering_provider"><?php xl('Rendering Provider','e'); ?>:</label>
                                                <div class="col-sm-4 input-group-sm">
                                                    <select name='rendering_provider'  id='rendering_provider' class='form-control'>
                                                             <?php
    //                                                        while ($urow1 = sqlFetchArray($ures1)) {
    //                                                            if($urow1['lname']!='' && $urow1['fname']!=''){
    //                                                                
    //                                                                $provid1 = $urow1['id'];
    //                                                                echo "    <option value='$provid1'";
    //                                                                echo " selected";
    //                                                                echo ">" . $urow1['lname'] . ", " . $urow1['fname'] . "\n";
    //                                                            } 
    //                                                        }
                                                        if(!empty($providername)){
                                                            foreach($providername as $provider_idsub => $providername_sub){
                                                                echo "<option value ='$provider_idsub' ";
                                                                //if( in_array ( $provider_idsub, $af_providers2) == true )
                                                                if($provider_idsub == $rendprovider) echo " selected ";
                                                                echo "> $providername_sub </option>";  
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label" for="form_patient"><?php xl('Patient','e'); ?>:</label>
                                                <div class="col-sm-4 input-group-sm"> 
                                                    <?php
                                                        /*
                                                        $query= "SELECT fe.pid, lname, fname from form_encounter fe  INNER JOIN patient_data p ON p.pid=fe.pid   "
                                                                                  . "  where fe.rendering_provider ='".$id['id']."' group by fe.pid  ORDER BY lname, fname ";
                                                         * 
                                                         */
                                                        // Subhan: Since we are now getting patient records not by a single provider, we have to remove provider clause
                                                        $query= "SELECT fe.pid, lname, fname from form_encounter fe  INNER JOIN patient_data p ON p.pid=fe.pid   "
                                                                                  . " group by fe.pid  ORDER BY lname, fname ";
                                                        $ures = sqlStatement($query);

                                                        echo "<select name='form_patient[]'  multiple  id='form_patient' class='form-control'>\n";
                                                        echo "<option value=''"; if(!empty($patient)){ foreach($patient as $val2) { if ($val2=='') echo " selected";}  }
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
                                                </div>
                                                <label class="col-sm-2 control-label" for="form_audit_stat"><?php xl('Audit Status','e'); ?>:</label>
                                                <div class="col-sm-4 input-group-sm"> 
                                                    <?php
                                                        echo "<select name='form_audit_stat[]'  multiple  id='form_audit_stat' class='form-control'>\n";
                                                        echo "    <option value=''"; if(!empty($audit_stat)){ foreach($audit_stat as $val8){if($val8=='') echo "selected";  } }
                                                        echo" >-- " . xl('All') . " --\n";
                                                        echo "<option value='Completed'"; if(!empty($audit_stat)){ foreach($audit_stat as $val8){if($val8=='Completed') echo "selected"; } } echo ">Complete</option>";     
                                                        echo "<option value='Incomplete'"; if(!empty($audit_stat)){ foreach($audit_stat as $val8){if($val8=='Incomplete') echo "selected"; } } echo">Incomplete</option>";
                                                        echo "   </select>\n";
                                                    ?>
                                                    </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label" for="form_enc_stat"><?php xl('Encounter Status','e'); ?>:</label>
                                                <div class="col-sm-4 input-group-sm">  
                                                    <?php
                                                          echo "   <select name='form_enc_stat[]'  multiple  id='form_enc_stat' class='form-control'>\n";
                                                          echo "    <option value=''";  if(!empty($enc_stat)){ foreach($enc_stat as $val2){if($val2=='') echo "selected"; } }
                                                          echo" >-- " . xl('All') . " --\n";
                                                          echo "<option value='1'"; if(!empty($enc_stat)){ foreach($enc_stat as $val2){if($val2=='1') echo "selected"; } } echo ">Complete</option>";     
                                                          echo "<option value='0'"; if(!empty($enc_stat)){ foreach($enc_stat as $val2){if($val2=='0') echo "selected"; } }else { echo  "selected"; }echo">Incomplete</option>";
                                                          echo "   </select>\n";
                                                    ?>
                                                </div>
                                                <label class="col-sm-2 control-label" for="form_from_date"><?php xl('From','e'); ?>:</label>
                                                <div class="col-sm-4">
                                                    <div class="input-group input-group-sm">
                                                        <input type='text' name='form_from_date' class="form-control" aria-describedby="fromadion" id="form_from_date"
                                                        size='10' value='<?php echo $from ?>'
                                                        onkeyup='datekeyup(this,mypcc)' onblur='dateblur(this,mypcc)'
                                                        title='yyyy-mm-dd'><span class="input-group-addon" id="fromadion"><img src='../interface/pic/show_calendar.gif'
                                                        align='absbottom' width='24' height='22' id='img_from_date'
                                                        border='0' alt='[?]' style='cursor: pointer'
                                                        title='<?php xl('Click here to choose a date','e'); ?>'></span>
                                                    </div>
                                                </div>
                                                <br/>
                                                <label class="col-sm-2 control-label" for="form_to_date" style="margin-top: 10px;"><?php xl('To','e'); ?>:</label>
                                                <div class="col-sm-4" style="margin-top: 10px;">
                                                    <div class="input-group input-group-sm">
                                                            <input type='text' class="form-control" aria-describedby="fromadion1" name='form_to_date' id="form_to_date"
                                                        size='10' value='<?php echo $to ?>'
                                                        onkeyup='datekeyup(this,mypcc)' onblur='dateblur(this,mypcc)'
                                                        title='yyyy-mm-dd'><span class="input-group-addon" id="fromadion1"><img src='../interface/pic/show_calendar.gif'
                                                        align='absbottom' width='24' height='22' id='img_to_date'
                                                        border='0' alt='[?]' style='cursor: pointer'
                                                        title='<?php xl('Click here to choose a date','e'); ?>'></span>
                                                    </div>
                                                </div>
                                            </div>         
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label" for="form_facility1"><?php xl('Facility','e'); ?>:</label>
                                                <div class="col-sm-4 input-group-sm">
                                                    <?php if(!empty($facility)){$facility1=implode("|",$facility); }  facility_list1(strip_escape_custom($facility1), 'form_facility1[]' ,'form_facility1',true,$af_providers2); ?>
                                                </div>
                                                <label class="col-sm-2 control-label" for="pc_catid"><?php echo xlt('Visit Category:'); ?></label>
                                                <div class="col-sm-4 input-group-sm">                                                            
                                                    <select name='pc_catid[]' multiple id='pc_catid' class="form-control">
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
                                                </div>
                                            </div> 
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label" for="inputEmail3"><?php xl('Column Names:','e'); ?></label>
                                                <div class="col-sm-4">
                                                    <div  id='column' style='min-height: 200px; width:200px; overflow-y: scroll; border: 1px solid black;' >
                                                        <ul id="slippylist">
                                                            <?php 
                                                                $sql_list=sqlStatement("SELECT * FROM `list_options`  where list_id='AllCareProviderIncomplete' order by seq");
                                                                while($row_list=sqlFetchArray($sql_list)){
                                                                    $lists[]=$row_list['option_id'];
                                                                }
                                                                $sql_vis=sqlStatement("SELECT provider_incomp from tbl_user_custom_attr_1to1 where userid='".$_SESSION['portal_userid']."'");
                                                                $row1_vis=sqlFetchArray($sql_vis);
                                                                if(!empty($row1_vis)) {
                                                                    $avail3=explode("|",$row1_vis['provider_incomp']);
                                                                    if(empty($provider)){
                                                                        $get_provider_name = sqlStatement("SELECT username FROM users WHERE id = '$id1'");
                                                                        while($set_provider_name = sqlFetchArray($get_provider_name)){
                                                                            $provider = $set_provider_name['username'];
                                                                        }
                                                                    }
                                                                    foreach($avail3 as $val6){
                                                                        if(in_array($val6, $lists)){
                                                                            $available1[]=$val6;
                                                                        }
                                                                    }
                                                                    $sql1=sqlStatement("SELECT * from tbl_allcare_providers_fieldsorder where username='".$_SESSION['portal_username']."' AND menu='incomplete_encouner'");
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
                                                                                if(in_array($row['option_id'], $available1) && $row['option_id']!='provider' && $row['option_id']!='rend_provider'){ ?>
                                                                                <li class='order'><?php echo str_replace(" ", "_",$field[1]); ?></li>
                                                                                <?php } }
                                                                                $diff=array_diff($avail3,$available);

                                                                    }else{
                                                                        $sql=sqlStatement("select * from list_options where list_id='AllCareProviderIncomplete'  order by seq"); 
                                                                        while($row=sqlFetchArray($sql)){ 
                                                                            if(in_array($row['option_id'], $available1) && $row['option_id']!='provider'&& $row['option_id']!='rend_provider'){ ?>
                                                                                <li class='order'><?php echo str_replace(" ", "_",$row['title']); ?></li>
                                                                                 <?php 
                                                                            } 
                                                                        }
                                                                    } 

                                                                    if(!empty($diff)){
                                                                        foreach($diff as $diffval){
                                                                            if(in_array($diffval, $available1) && $diffval!='provider' && $diffval!='rend_provider' ){ 
                                                                               $sql23=sqlStatement("select * from list_options where list_id='AllCareProviderIncomplete' AND option_id='$diffval' order by seq"); 
                                                                                 $row23=sqlFetchArray($sql23);   ?>
                                                                              <li class='order' style="color:red;" ><?php echo str_replace(" ", "_",$row23['title']); ?></li>
                                                                            <?php }

                                                                         }
                                                                    }
                                                                }  
                                                            ?>
                                                        </ul>
                                                    </div>
                                                </div>
                                                <label class="col-sm-2 control-label" for="assign_by"><?php echo xlt('Assigned By:'); ?></label>
                                                <div class="col-sm-4 input-group-sm">                                                            
                                                    <?php 
                                                        $user = sqlStatement("SELECT id, username, fname, lname
                                                                FROM users
                                                                WHERE username !=  ''
                                                                AND active =  '1'
                                                                ORDER BY username");
                                                        echo "<select name='assign_user[]' multiple  id='assign_user' >";
                                                        echo "<option value=''>" . htmlspecialchars(xl('-- Select ---'), ENT_NOQUOTES) . "</option>";
                                                        while ($urow = sqlFetchArray($user)) {
                                                            $uname = htmlspecialchars( $urow['fname'] . ' ' . $urow['lname'], ENT_NOQUOTES);
                                                            $optionId = htmlspecialchars( $urow['id'], ENT_QUOTES);
                                                            echo "<option value='$optionId'";
                                                            if(!empty($assigned_by)){
                                                                foreach($assigned_by as $currvalue){
                                                                    if ($urow['id'] == $currvalue) echo " selected"; 
                                                                }
                                                            }
                                                            echo ">$uname</option>";
                                                        }
                                                        echo "</select>"; 
                                                    ?>
                                                </div>    
                                        </div>
                                            <div class="form-group">
                                                <div class="col-sm-6">
                                                    <div class="page-header">
                                                        <h3>Showing Records:</h3>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-sm-12">
                                                            <div class="form-group">
                                                                <label class="col-sm-2 control-label" for="lfrom">From:</label>
                                                                <div class="col-sm-3 input-group-sm">
                                                                  <input type="text" id="lfrom" name="lfrom" class="form-control" value="<?php echo $limit_from; ?>" />                                                                      
                                                                </div>
                                                                <label class="col-sm-2 control-label" for="lfrom">To: </label>
                                                                <div class="col-sm-3 input-group-sm">
                                                                  <input type="text" id="lto" name="lto" value="<?php echo $limit_to; ?>" class="form-control"/>                                                                   
                                                                </div>
                                                            </div>
                                                            <div class="text-center"><?php echo "<b>Total Records:</b>$rowcount" ; ?></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>    
                                            <script src="js/slip.js"></script>
                                            <script>
                                                var list = document.getElementById('slippylist');
                                                new Slip(list);
                                                list.addEventListener('slip:reorder', function(e) {
                                                e.target.parentNode.insertBefore(e.target, e.detail.insertBefore);
                                                });
                                            </script>                                                 
                                        </div>
                                    </div>
                                    <input type='hidden' id='provider' name='provider' value='<?php echo $_REQUEST['provider']; ?>' />
                                    <input type='hidden' id='col' name='col' value='' />
                                    <input type='hidden' id='menu_val' name='menu_val' value='' />
                                    <input type='hidden' id='refer' name='refer' value='<?php echo $refer; ?>' />
                                    <div align='center' id="buttons"> 
                                    <a href="javascript:;"  class="btn btn-submit btn-sm" onclick="submitme2();"><span><?php echo htmlspecialchars( xl('Submit'), ENT_NOQUOTES); ?></span></a>
                                    <input type="submit" id='def' name='def' value="Default" class="btn btn-submit btn-sm"/></div>
                                </form>
                            </div>
                        </div>
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
                                echo "<thead><tr><th>&nbsp;</th>";
                                $sql_list1 = sqlStatement("SELECT * FROM `list_options`  where list_id='AllCareProviderIncomplete' order by seq");
                                while($row_list1 = sqlFetchArray($sql_list1)){
                                    $lists1[] = $row_list1['option_id'];
                                }
                                $sql_vis1  = sqlStatement("SELECT provider_incomp from tbl_user_custom_attr_1to1 where userid='".$_SESSION['portal_userid']."'");
                                $row1_vis1 = sqlFetchArray($sql_vis1);
                                if(!empty($row1_vis1)) {
                                    $avail4 = explode("|",$row1_vis1['provider_incomp']);
                                    foreach($avail4 as $val7){
                                        if(in_array($val7, $lists1)){
                                            $available6[] = $val7;
                                        }

                                    }
                                    if(empty($provider)){
                                        $get_provider_name = sqlStatement("SELECT username FROM users WHERE id = '$id1'");
                                        while($set_provider_name = sqlFetchArray($get_provider_name)){
                                            $provider = $set_provider_name['username'];
                                        }
                                    }
                                    $sql12 = sqlStatement("SELECT * from tbl_allcare_providers_fieldsorder where username='".$_SESSION['portal_username']."' AND menu='incomplete_encouner'");
                                    $row12 = sqlFetchArray($sql12);
                                    if(!empty($row12)){
                                        $orders1 = explode(",",$row12['order_of_columns']);
                                        $field12 = '';  
                                        $fields  = array();
                                        foreach($orders1 as $value21){
                                            $field12    = explode(":",$value21); 
                                            $title      = str_replace("_"," ",$field12[1]);
                                            $sql = sqlStatement("select * from list_options where list_id='AllCareProviderIncomplete' AND title='$title' order by seq"); 
                                            $row = sqlFetchArray($sql);
                                            if(($row['option_id']=='patient_name' && in_array($row['option_id'], $available6) && $row['option_id']!='provider' && $row['option_id']!='rend_provider') || $enc == $_REQUEST['checkencounter']){
                                                echo "<th  data-class='expand'>".htmlspecialchars( xl($title), ENT_NOQUOTES)."</th>";
                                            }elseif(in_array($row['option_id'], $available6) && $row['option_id']!='provider'){
                                                echo "<th data-hide='phone' data-name='$title'>".htmlspecialchars( xl($title), ENT_NOQUOTES)."</th>";
                                            }
                                         }

                                    }else{
                                        $sql = sqlStatement("select * from list_options where list_id='AllCareProviderIncomplete' order by seq"); 
                                        while($row = sqlFetchArray($sql)){
                                            if(($row['option_id']=='patient_name' && in_array($row['option_id'], $available6) && $row['option_id']!='provider' && $row['option_id']!='rend_provider') || $enc == $_REQUEST['checkencounter']){
                                                echo "<th style='width:180px;' data-class='expand'>".htmlspecialchars( xl($row['title']), ENT_NOQUOTES)."</th>";
                                            }elseif(in_array($row['option_id'], $available6) && $row['option_id']!='provider' && $row['option_id']!='rend_provider'){
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
                                      
                                if(!empty($id)){
                                    $resid21 .=  " AND ( fe.provider_id ='".$id['id']."' OR fe.rendering_provider='".$id['id']."')";
                                }
                                //echo $resid21;
                                $pid_val    = ''; 
                                $pid_val1   = '';
                                if(is_array($patient)){
                                    if(!empty($patient)){
                                        foreach($patient as $value1){
                                            if($value1 != ''){
                                             $pid_val .= $value1.",";
                                            }
                                        }
                                        $pid_val1=trim($pid_val,',');
                                        if($pid_val1!=''){
                                            $resid21 .=  " AND p.pid IN ($pid_val1)";
                                        }

                                    }
                                }else {
                                    if($patient!=''){
                                         $resid21 .=  " AND p.pid = $patient";
                                    }
                                }
                                    
                                if(!empty($enc_stat)){
                                     foreach($enc_stat as $value2){
                                       if($value2 != ''){   
                                           if($value2=='1'){
                                               $resid21.=  " AND fe.elec_signedby !='' AND fe.elec_signed_on !=''";
                                           }
                                           if($value2=='0'){
                                               $resid21.=  " AND ((fe.elec_signedby ='' AND fe.elec_signed_on ='') or (fe.elec_signedby !='' AND fe.elec_signed_on ='') or (fe.elec_signedby ='' AND fe.elec_signed_on !=''))";
                                           }
                                       }
                                    }
                                }

                                if($from!='' && $to!=''){
                                   $resid21 .=  " AND DATE_FORMAT(fe.date, '%Y-%m-%d') BETWEEN CAST('$from' AS DATE) AND CAST('$to' AS DATE)  ";
                                }
                                //facility
                                    
                                $fac_val=''; $fac_val21='';
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
                                $cat_val=''; $cat_val1='';
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

                                 //audit_status
                                 if(!empty($audit_stat)){
                                    foreach($audit_stat as $value99){
                                        if($value99!=''){
                                         $audit_vall.="'".$value99."'".",";
                                        }
                                    }
                                    $audit_val11=trim($audit_vall,',');
                                    if($audit_val11!=''){
                                        if(strpos($audit_val11, 'Incomplete')==true){
                                            $resid21.=  " AND fe.audited_status IN ($audit_val11,'','New','In Process','Pending')";
                                        }else{
                                            $resid21.=  " AND fe.audited_status IN ($audit_val11)";
                                        }
                                    }
                                 }

                                  //Assigned by
                                if(!empty($assigned_by)){
                                    foreach($assigned_by as $value66){
                                        if($value66!=''){
                                         $assign_vall.=$value66.",";
                                        }
                                    }
                                    $assign_val11=trim($assign_vall,',');
                                    if($assign_val11!=''){
                                        $resid21.=  " AND fe.audited_by IN ($assign_val11)";
                                    }
                                }
                                if($limit_from==0){
                                     $resid21.=' LIMIT '. $limit_from ." , ".$limit_to ;
                                }else {
                                    $limit=$limit_to-$limit_from;
                                    $resid21.=' LIMIT '. $limit ." OFFSET ".$limit_from ;
                                }
                                //echo $resid21;
                                $resid2=sqlStatement($resid21);

                                while ($resid_row1=sqlFetchArray($resid2)){
                                    $f2fenc_Y[]=$resid_row1['encounter'];
                                }
                                $uni_Y=array_unique($f2fenc_Y);
                                foreach($uni_Y as $value) {
                              
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

                                        if ($result1 = VisitsWithNoForms($resid_row3['form_id'],$resid_row3['pid'],$resid_row3['encounter'],$resid_row3['form_name'],$ros,$pe,$allcare,$audit_stat,$_SESSION['portal_username'],$menu,$id1,$refer,$af_providers,$rendprovider,$feeprovider)) {
                                        // echo "<pre>";print_r($result1); echo "</pre>";
//                                                              
//                                                                 echo "<tr height='25'>";
                                        if($result1['encounter'] == $_REQUEST['checkencounter']){ 
                                            echo "<tr class='encountercell'  style='height=25'>";
                                        }else{
                                            echo "<tr height='25'>";
                                        }
                                        echo "<td style='width:600px;'>";
                                        if($result1['finalized_val']=='not_finalized'){
                                                $enc=$result1['encounter'];
                                                $pid=$result1['pid'];
                                                $date=$result1['dos']; 
                                                $enc=$result1['encounter'];
                                                $pid=$result1['pid'];
                                                $date=$result1['dos']; 
                                                $visit=$result1['visit_category'];
                                                $grpres['chart_group'];
                                                $grpm=sqlStatement("select * from tbl_visitcat_chartgrp_mapping where visit_category=$visit");
                                                $grpres=sqlFetchArray($grpm);
                                                if($grpres['chart_group']!=''){ 
                                                    $chgrp=substr($grpres['chart_group'],1);
                                                    $mobile_sql=sqlStatement("SELECT * 
                                                           FROM  `tbl_chartui_mapping` 
                                                           WHERE form_id =  'CHARTOUTPUT'
                                                           AND group_name LIKE  '%$chgrp%'
                                                           AND screen_name LIKE  '%$chgrp%'");
                                                while($mob_row1=sqlFetchArray($mobile_sql)){

                                                    $field_id='form_'.$mob_row1['field_id'];
                                                    $field_value=$mob_row1['option_value']; 
                                                    $res.=$field_id.'='.$field_value.'&'; 
                                                   }
                                                  $datastring=$res.'patientid'.'='.$pid.'&'.'encounter_id'.'='.$enc.'&'.'dos'.'='.$date.'&'.'chartgroupshidden'.'='.$grpres['chart_group']; ?>
                                                  <a href='javascript:; ' onclick="window.open('chartoutput/preview_charts.php?'+'<?php echo $datastring; ?>','popup','width=900,height=900,scrollbars=no,resizable=yes');" title='<?php echo substr($grpres['chart_group'],1); ?>' class='css_button_small'><span><?php echo  htmlspecialchars( xl('Preview'), ENT_NOQUOTES); ?></span></a> |
                                               <?php }else { ?>
                                                    <a class="various" data-fancybox-type="iframe" href="chartoutput/preview_group.php?enc=<?php echo $enc; ?>&pid=<?php echo $pid; ?>&date=<?php echo $date; ?>&grp=0"  ><span><?php echo  htmlspecialchars( xl('Preview'), ENT_NOQUOTES); ?></span></a>|
                                               <?php } ?>
<!--                                                                                <a href='javascript:; ' onclick="previewpost1('<?php echo $enc; ?>','<?php echo $pid; ?>','<?php echo $date; ?>')" class='css_button_small'><span><?php echo  htmlspecialchars( xl('Preview'), ENT_NOQUOTES); ?></span></a> -->


                                                <?php  
                                                //echo "select rendering_provider,provider_id from form_encounter where pid=$pid and encounter=$enc";
                                                $sql34=sqlStatement("select rendering_provider,provider_id from form_encounter where pid=$pid and encounter=$enc");
                                                $res34=sqlFetchArray($sql34);
                                                if($res34['rendering_provider']!=''){
                                                   $to=$res34['rendering_provider']; 
                                                }else {
                                                   $to=$res34['provider_id']; 
                                                }
                                                $ures = sqlStatement("SELECT id, fname, lname, organization, username,abook_type FROM users " .
                                                                    "WHERE active = 1 AND ( info IS NULL OR info NOT LIKE '%Inactive%' ) " .
                                                                    " AND ( username = '' OR authorized = 1 ) AND id='".$to."' ORDER BY organization, lname, fname"); 
                                                $urow = sqlFetchArray($ures);
                                                if($result1['audit_status']=='Complete'){  ?>
                                                      <input type='checkbox' name='finalize_<?php echo $enc; ?>' id='finalize_<?php echo $enc; ?>' onclick="postValue('<?php echo $enc; ?>','<?php echo $pid; ?>','<?php echo $date; ?> ','<?php echo $grpres['chart_group']; ?>','<?php echo $urow['abook_type'];?>')"/>Finalize | 

                                                <?php } else if($result1['audit_status']=='Incomplete') {?> <input type='checkbox' name='finalize_<?php echo $enc; ?>' id='finalize_<?php echo $enc; ?>' onclick="postValue('<?php echo $enc; ?>','<?php echo $pid; ?>','<?php echo $date; ?> ','','<?php echo $urow['abook_type'];?>')" disabled />Finalize |  <?php } 
                                                $gr=substr($grpres['chart_group'],1);
                                                echo "<a  class='css_button_small' title='$gr' ><span>".
                                                htmlspecialchars( xl('View'), ENT_NOQUOTES)."</span></a>";

                                            }
                                            else if($result1['finalized_val']=='finalized'){
                                                $enc=$result1['encounter'];
                                                $pid=$result1['pid'];
                                                $date   = $result1['dos'];
                                                $visit=$result1['visit_category'];
                                                $grpm=sqlStatement("select * from tbl_visitcat_chartgrp_mapping where visit_category=$visit");
                                                $grpres=sqlFetchArray($grpm);
                                                if($grpres['chart_group']!=''){  $chgrp=substr($grpres['chart_group'],1);
                                                $mobile_sql=sqlStatement("SELECT * 
                                                            FROM  `tbl_chartui_mapping` 
                                                            WHERE form_id =  'CHARTOUTPUT'
                                                            AND group_name LIKE  '%$chgrp%'
                                                            AND screen_name LIKE  '%$chgrp%'");
                                                while($mob_row1=sqlFetchArray($mobile_sql)){

                                                    $field_id='form_'.$mob_row1['field_id'];
                                                    $field_value=$mob_row1['option_value']; 
                                                    $res.=$field_id.'='.$field_value.'&'; 
                                                }
                                                $datastring=$res.'patientid'.'='.$pid.'&'.'encounter_id'.'='.$enc.'&'.'dos'.'='.$date.'&'.'chartgroupshidden'.'='.$grpres['chart_group']; ?>
                                                <a href='javascript:; ' onclick=" window.open('chartoutput/preview_charts.php?'+'<?php echo $datastring; ?>','popup','width=900,height=900,scrollbars=no,resizable=yes');" title='<?php echo  substr($grpres['chart_group'],1); ?>' class='css_button_small'><span><?php echo  htmlspecialchars( xl('Preview'), ENT_NOQUOTES); ?></span></a> |
                                                <?php }else { ?>
                                                     <a class="various css_button_small" data-fancybox-type="iframe" href="chartoutput/preview_group.php?enc=<?php echo $enc; ?>&pid=<?php echo $pid; ?>&date=<?php echo $date; ?>&grp=0"  ><span><?php echo  htmlspecialchars( xl('Preview'), ENT_NOQUOTES); ?></span></a>|
                                                <?php } 
                                                $sql34=sqlStatement("select rendering_provider,provider_id from form_encounter where pid=$pid and encounter=$enc");
                                                $res34=sqlFetchArray($sql34);
                                                if($res34['rendering_provider']!=''){
                                                   $to=$res34['rendering_provider']; 
                                                }else {
                                                   $to=$res34['provider_id']; 
                                                }
                                                $ures = sqlStatement("SELECT id, fname, lname, organization, username,abook_type FROM users " .
                                                                                      "WHERE active = 1 AND ( info IS NULL OR info NOT LIKE '%Inactive%' ) " .
                                                                                      " AND ( username = '' OR authorized = 1 ) AND id='".$to."' ORDER BY organization, lname, fname"); 
                                                $urow = sqlFetchArray($ures);
                                                if($result1['audit_status']=='Complete'){
                                                    $form_enc=sqlStatement("select * from form_encounter where encounter=$enc"); 
                                                    $frow_enc=sqlFetchArray($form_enc); 
                                                    $enc_date=explode(" ",$frow_enc['date']);
                                                    $trans=sqlStatement("select * from tbl_form_chartoutput_transactions where pid='".$frow_enc['pid']."' and transaction=1 and date_of_service='".$enc_date[0]."'"); 
                                                    $trans_row=sqlFetchArray($trans); 

                                                    if(!empty($trans_row)){ ?>
                                                        <input type='checkbox' name='finalize_<?php echo $enc; ?>' id='finalize_<?php echo $enc; ?>' onclick="postValue('<?php echo $enc; ?>','<?php echo $pid; ?>','<?php echo $date; ?> ','','<?php echo $urow['abook_type'];?>')"  checked disabled/>Finalize | <?php 
                                                    }else { ?>
                                                       <input type='checkbox' name='finalize_<?php echo $enc; ?>' id='finalize_<?php echo $enc; ?>' onclick="postValue('<?php echo $enc; ?>','<?php echo $pid; ?>','<?php echo $date; ?> ','','<?php echo $urow['abook_type'];?>','<?php echo $frow_enc['elec_signed_on']; ?>')" />Finalize | <?php   
                                                    }
                                                    $sql_id=sqlStatement("select * from tbl_form_chartoutput_transactions where pid=$pid and encounter=$enc order by id desc");
                                                    $id_row3=sqlFetchArray($sql_id);
                                                    $id=$id_row3['id'];
                                                    $group=$id_row3['chart_group'];
//                                                  ?>
                                                    <a href='javascript:; ' onclick="viewpost1('<?php echo $id; ?>','<?php echo $pid; ?>','<?php echo $group; ?> ')" title='<?php echo  substr($group,1); ?>' class='css_button_small'><span><?php echo  htmlspecialchars( xl('View'), ENT_NOQUOTES); ?></span></a>
                                                    <?php 
                                                         
                                                }else { ?>
                                                    <input type='checkbox' name='finalize_<?php echo $enc; ?>' id='finalize_<?php echo $enc; ?>' onclick="postValue('<?php echo $enc; ?>','<?php echo $pid; ?>','<?php echo $date; ?> ','','<?php echo $urow['abook_type'];?>')"   disabled/>Finalize | <?php 
                                                    $gr1=substr($grpres['chart_group'],1);
                                                    echo "<a  class='css_button_small' title='$gr1' ><span>".htmlspecialchars( xl('View'), ENT_NOQUOTES)."</span></a>";

                                                }
                                            }
                                            echo "</td>";

                                            $sql_vis=sqlStatement("SELECT provider_incomp from tbl_user_custom_attr_1to1 where userid='".$_SESSION['portal_userid']."'");
                                            $row1_vis=sqlFetchArray($sql_vis);
                                            $enc=$result1['encounter'];
                                            $pid=$result1['pid'];
                                            if(!empty($row1_vis)) {

                                                $avail3=explode("|",$row1_vis['provider_incomp']);
                                                $sql13=sqlStatement("SELECT * from tbl_allcare_providers_fieldsorder where username='".$_SESSION['portal_username']."' AND menu='incomplete_encouner'");
                                                $row13=sqlFetchArray($sql13);
                                                if(!empty($row13)){
                                                    $orders1=explode(",",$row13['order_of_columns']);
                                                    $field12='';  
                                                    foreach($orders1 as $value22){
                                                        $value23=explode(":",$value22); 
                                                        $title=str_replace("_"," ",$value23[1]);
                                                        $sqlId=sqlStatement("select * from list_options where list_id='AllCareProviderIncomplete' AND title='$title' order by seq");
                                                        while($rowId=sqlFetchArray($sqlId)){
                                                            $field=$rowId['option_id'];
                                                            if($field=='facility' && in_array($field, $avail3)){
                                                                echo "<td>";
                                                                if($result1[$field]!='')  $pro_id2=sqlStatement("select name from facility where id='".$result1[$field]."' ");
                                                                $id_row5=sqlFetchArray($pro_id2);
                                                                $proid23=$id_row5['name'];{ echo $proid23; }
                                                                echo "</td>";
                                                            }
                                                            else if($field=='visit_category' && in_array($field, $avail3)){
                                                                echo "<td>"; 
                                                                if($result1[$field]!='') $pro_id1=sqlStatement("select pc_catname from openemr_postcalendar_categories where pc_catid='".$result1[$field]."' ");
                                                                    $id_row41=sqlFetchArray($pro_id1);
                                                                    $proid1=$id_row41['pc_catname'];{ echo $proid1; }
                                                                    echo "</td>";
                                                            }
                                                            elseif($field=='audit_note' && in_array($field, $avail3)){
                                                                echo "<td>"; echo "<form id='audit_note_$enc' name='audit_note_$enc' action='provider_incomplete_charts.php?checkencounter=$enc' method='post'> <textarea name='form_audit_note_$enc' id='form_audit_note_$enc' title='' cols='15' rows='1' ";
                                                                //if($_SESSION['refer'] == '')
                                                                      //echo " disabled ";
                                                                echo ">$result1[$field]</textarea>"
                                                                        . "  <input type='hidden' name='encounter' id='encounter' value='$enc' /> <input type='hidden' name='pid' id='pid' value='$pid' />  ";
                                                                //if($_SESSION['refer'] != '')
                                                                  echo "<input type='submit' name='submit' value='save' /> "; 
                                                                echo "</form>"; 
                                                                echo "</td>";
                                                            }
                                                            elseif($field=='assign_by' && in_array($field, $avail3)){
                                                                echo "<td>";
                                                                if($result1['audited_by']!=''){ $user_ids=sqlStatement("select CONCAT(fname,' ',lname) AS assigned_by from users where id='".$result1['audited_by']."' ");
                                                                    $id_usr=sqlFetchArray($user_ids);
                                                                    $assid1=$id_usr['assigned_by'];
                                                                    echo $assid1; 
                                                                }else { 
                                                                    echo "nothing"; 
                                                                }
                                                                echo "</td>";
                                                            }
                                                            elseif($field=='rend_provider' && in_array($field, $avail3)){
                                                                echo "<td>"; 
                                                                if($result1['rend_provider']!='') $pro_id1=sqlStatement("select CONCAT(fname,' ',lname) AS rendering_provider from users where id='".$result1['rend_provider']."' ");
                                                                    $id_row5=sqlFetchArray($pro_id1);
                                                                    $proid1=$id_row5['rendering_provider'];{ echo $proid1; }
                                                                    echo "</td>";
                                                            }
                                                            elseif($field=='single_view' && in_array($field, $avail3)){
                                                                echo "<td>"; echo "<a href='javascript:;' onclick=win1('single_view_form.php?encounter=$enc&pid=$pid') ><span>Charts</span></a>";
                                                                echo "</td>";
                                                            }
                                                            elseif(in_array($field, $avail3) && $field!='provider' ){
                                                                echo "<td>".$result1[$field]."</td>";
                                                            }

                                                        }
                                                    }
                                                }
                                                else{
                                                    $sql=sqlStatement("select * from list_options where list_id='AllCareProviderIncomplete' order by seq"); 
                                                    while($row=sqlFetchArray($sql)){
                                                        $field=$row['option_id'];
                                                        if($field=='facility' && in_array($field, $avail3)){
                                                            echo "<td>";
                                                            if($result1[$field]!='')  
                                                                $pro_id2=sqlStatement("select name from facility where id='".$result1[$field]."' ");
                                                            $id_row5=sqlFetchArray($pro_id2);
                                                            $proid23=$id_row5['name'];
                                                            { echo $proid23; }
                                                            echo "</td>";
                                                        }
                                                        else if($field=='visit_category' && in_array($field, $avail3)){
                                                            echo "<td>"; 
                                                            if($result1[$field]!='') 
                                                                $pro_id1=sqlStatement("select pc_catname from openemr_postcalendar_categories where pc_catid='".$result1[$field]."' ");
                                                                $id_row41=sqlFetchArray($pro_id1);
                                                                $proid1=$id_row41['pc_catname'];
                                                            { echo $proid1; }
                                                            echo "</td>";
                                                        }
                                                        elseif($field=='audit_note' && in_array($field, $avail3)){
//                                                                                         echo "<td>"; echo "<form id='audit_note_$enc' name='audit_note_$enc' action='' method='post'> <textarea name='form_audit_note_$enc' id='form_audit_note_$enc' title='' cols='5' rows='10' disabled>$result1[$field]</textarea>"
//                                                                                                      . "  <input type='hidden' name='encounter' id='encounter' value='$enc' /> <input type='hidden' name='pid' id='pid' value='$pid' /> </form>"; echo "</td>";
                                                        }
                                                        elseif($field=='rend_provider' && in_array($field, $avail3)){
                                                            echo "<td>"; 
                                                            if($result1['rendering_provider']!='') 
                                                                $pro_id1=sqlStatement("select CONCAT(fname,' ',lname) AS rendering_provider from users where id='".$result1['rendering_provider']."' ");
                                                                $id_row5=sqlFetchArray($pro_id1);
                                                                $proid1=$id_row5['rendering_provider'];
                                                                { echo $proid1; }
                                                            echo "</td>";
                                                        }
                                                        elseif($field=='assign_by' && in_array($field, $avail3)){
                                                            echo "<td>"; 
                                                            if($result1['audited_by']!='')  { 
                                                                $user_ids=sqlStatement("select CONCAT(fname,' ',lname) AS assigned_by from users where id='".$result1['audited_by']."' ");
                                                                $id_usr=sqlFetchArray($user_ids);
                                                                $assid1=$id_usr['assigned_by'];

                                                                    echo $assid1; 

                                                            }
                                                            else { echo "nothing"; } 
                                                            echo "</td>";
                                                       }elseif(in_array($field, $avail3) && $field!='provider' ){
                                                            echo "<td>".$result1[$field]."</td>";
                                                       }
                                                    }
                                                }
                                            }    
                                            echo "</tr>\n";
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
    <?php include 'section_footer.php'; ?>
<!--		<script type="text/javascript" src="assets/js/jquery.min.js"></script>
		<script type="text/javascript" src="assets/js/bootstrap.min.js"></script>-->

        <script type='text/javascript'>
                var opttext = new Array();
                $(document).ready(function() {
                    //jQuery.noConflict();
                    $("#help_dialog").draggable({ handle:'#header'});
                    $("#services").css("min-height",window.innerHeight+"px");
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
                    function getUrlVars()
                    {
                        var vars = [], hash;
                        var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
                        for(var i = 0; i < hashes.length; i++)
                        {
                            hash = hashes[i].split('=');
                            vars.push(hash[0]);
                            vars[hash[0]] = hash[1];
                        }
                        return vars;
                    }
                    param = getUrlVars()["checkencounter"];
                    if(typeof param !== 'undefined'){
                        $('.encountercell').children('.sorting_1').click();
                        $('html, body').animate({scrollTop:(($('.encountercell').position().top)-130)}, 500);
                    }
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

                   $(".fancybox").fancybox({
                        openEffect  : 'none',
                        closeEffect : 'none',
                        iframe : {
                                preload: false
                        }
                    });

                    $(".various").fancybox({
                            maxWidth	: 200,
                            maxHeight	: 200,
                            fitToView	: false,
                            width		: '70%',
                            height		: '70%',
                            autoSize	: false,
                            closeClick	: false,
                            openEffect	: 'none',
                            closeEffect	: 'none'

                    });

                   $('.fancybox-media').fancybox({
                            openEffect  : 'none',
                            closeEffect : 'none',
                            helpers : {
                                    media : {}
                            }
                    });

                });

        </script>
        <script>
            (function($){

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
<script type="text/javascript" src="../library/dynarch_calendar_setup.js"></script>
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
      ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'https://www') + '.google-analytics.com/ga.js';
      var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
    })();
</script>
</html>
<?php
function GetIP()
{
    if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown"))
        $ip = getenv("HTTP_CLIENT_IP");
    else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown"))
        $ip = getenv("HTTP_X_FORWARDED_FOR");
    else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown"))
        $ip = getenv("REMOTE_ADDR");
    else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown"))
        $ip = $_SERVER['REMOTE_ADDR'];
    else
        $ip = "unknown";
    return($ip);
}
?> 