<?php

 //SANITIZE ALL ESCAPES
 $sanitize_all_escapes=$_POST['true'];

 //STOP FAKE REGISTER GLOBALS
 $fake_register_globals=$_POST['false']; 

include_once("../../globals.php");
include_once("$srcdir/api.inc");
include_once("$srcdir/forms.inc");
require_once("$srcdir/formdata.inc.php");

$encounter = $_REQUEST['encounter'];

if (!$encounter ) {// comes from globals.php
 die(xl("Internal error: we do not seem to be in an encounter!"));
}
//echo "<pre>"; print_r($_POST); echo "</pre>";
$groupdata = array();
$id = 0 + (isset($_GET['id']) ? $_GET['id'] : '');
$i= 0;
$group_array = array();
$get_group_names = sqlStatement("SELECT DISTINCT group_name FROM layout_options WHERE form_id='AUDITFORM' ORDER BY group_name");
while($set_group_names = sqlFetchArray($get_group_names)){
    $get_field_names = sqlStatement("SELECT field_id, list_id, title FROM layout_options WHERE form_id='AUDITFORM' and group_name='".$set_group_names['group_name']."' order by seq");
    $group_array['hidden'.$set_group_names['group_name']] = $_POST['hidden'.$set_group_names['group_name']."2"];
    while ($set_field_names = sqlFetchArray($get_field_names)){
        $getchecklist = sqlStatement("SELECT title, option_id FROM list_options WHERE list_id = '".$set_field_names['list_id']."'  and option_id LIKE '%\_%' order by seq");
        $group_array['hidden'.$set_field_names['field_id']] = $_POST['hidden'.$set_field_names['field_id']."2"];
        if(mysql_num_rows($getchecklist) > 0){ 
            $get_list_fields = sqlStatement("SELECT title, option_id FROM list_options WHERE list_id = '".$set_field_names['list_id']."'  and option_id NOT LIKE '%\_%' order by seq");
            while($set_list_fields = sqlFetchArray($get_list_fields)){
                $get_list_fields_value = sqlStatement("SELECT notes, option_id FROM list_options WHERE list_id = '".$set_field_names['list_id']."'  and option_id LIKE '".$set_list_fields['option_id']."\_%' order by seq");
                while($set_list_fields_value = sqlFetchArray($get_list_fields_value)){
                    //$groupdata[$set_group_names['group_name']][] =  $set_field_names['list_id'].$set_list_fields['option_id'].$set_list_fields_value['option_id'];
                    $groupdata[$set_group_names['group_name']][] =  $set_field_names['list_id'].$set_list_fields_value['option_id'];
                }
            }
        }else{ 
            $get_list_fields = sqlStatement("SELECT title, option_id,list_id, notes FROM list_options WHERE list_id = '".$set_field_names['list_id']."' order by seq");
            
            while($set_list_fields = sqlFetchArray($get_list_fields)){ 
                if($set_field_names['list_id'] == 'Audit_CC_Options'){
                    if($i==0){
                        $groupdata[$set_group_names['group_name']][] = 'Audit_CC_Optionsradio';
                        $i++;
                    }    
                }else{
                    $groupdata[$set_group_names['group_name']][] = $set_field_names['list_id'].$set_list_fields['option_id'];
                }    
            } 
        }    
    }   
}
$posted_group = $_POST;
$postdata = array(); 
foreach($posted_group as $postedkey => $postedvalue){
    $postdata[$postedkey] = $postedvalue;
}
$form = array();
$lbf_formid = $_POST['lbf_formid'];
foreach($_POST as $formkey => $formvalue){
    if(strpos($formkey,'form_' ) !== false){
        $updatenamelist2 .= str_replace("form_","",$formkey)."='".$formvalue."',";
    }
}
$updatenamelist = rtrim($updatenamelist2, ',');

$sqllbf = sqlStatement("UPDATE form_encounter SET  $updatenamelist  WHERE id = $lbf_formid");
if($sqllbf)
    save_form_flag($lbf_formid);

foreach($groupdata as $groupkey =>$groupVal){
   foreach($groupVal as $val){
        if(isset($postdata[$val])){ 
           $group_array[$groupkey][$val] = $postdata[$val];
        }    
   }    
}   
$get_ic_table_data = sqlStatement("SELECT option_id FROM list_options WHERE list_id='Interactive_Complexity'");
while($set_ic_table_data = sqlFetchArray($get_ic_table_data)){
    $group_array["ic".$set_ic_table_data['option_id']] = $_POST["ic".$set_ic_table_data['option_id']];
}
$get_it_table_data = sqlStatement("SELECT option_id FROM list_options WHERE list_id='Incident_To'");
while($set_it_table_data = sqlFetchArray($get_it_table_data)){
    $group_array["it".$set_it_table_data['option_id']] = $_POST["it".$set_it_table_data['option_id']];
}
$group_array['cpt_data'] = $_POST['cpt_data'];
$group_array['audit_time'] = $_POST['audit_time'];
$group_array['hiddenaudit'] = $_POST['hiddenaudit2'];
$group_array['Audit_CC_Optionstextarea'] = $_POST['Audit_CC_Optionstextarea'];
$group_array['history_unobtainable_textarea'] = $_POST['history_unobtainable_textarea'];
$group_array['history_unobtainable_radio'] = $_POST['history_unobtainable_radio'];
$group_array['defaultccm'] = $_POST['defaultccm'];
$group_array['defaultcpo'] = $_POST['defaultcpo'];
$auditform_comments = $_POST['auditform_comments'];
//echo "<pre>";print_r($group_array); echo "</pre>";

$audit_data= ( serialize($group_array) );
$sets = "pid = {$_SESSION["pid"]},
    authProvider = '" . $_SESSION["authProvider"] . "',
    user = '" . $_SESSION["authUser"] . "',
    authorized = $userauthorized, 
    activity=1, 
    date = NOW(),
    audit_data = '" .$audit_data. "'";

if (empty($id)) {
    $newid = sqlInsert("INSERT INTO tbl_form_audit SET $sets");
    addForm($encounter, "Audit Form", $newid, "auditform", $pid, $userauthorized);
    $id = $newid;
}
else {
    sqlStatement("UPDATE tbl_form_audit SET $sets WHERE id = '". add_escape_custom("$id"). "'");
}

if(!empty($auditform_comments)){
    $get_layout_comments = sqlStatement("SELECT l.field_value FROM forms f INNER JOIN lbf_data l ON l.form_id = f.form_id WHERE f.formdir =  'LBF2' AND deleted = 0 AND l.field_id = 'auditform_comments' AND f.encounter = $encounter");
    if(mysql_num_rows($get_layout_comments)){
        while($set_comments_text = sqlFetchArray($get_layout_comments)){
            $comments_text = $set_comments_text['field_value'];
        }
        sqlStatement("UPDATE lbf_data SET field_value = '$auditform_comments' WHERE form_id = '$form_id' AND field_id = 'auditform_comments'");
    }else{
        $form_id = sqlInsert("INSERT INTO forms  (pid, encounter,DATE, form_name,formdir,deleted,authorized,user) VALUES($pid, $encounter, NOW(),'Allcare Encounter Forms', 'LBF2',0,$userauthorized,'".$_SESSION["authUser"]."')");
//        addForm($encounter, "LBF2", $newid, "auditform", $pid, $userauthorized);
        $newauditcomment = sqlInsert("INSERT INTO lbf_data (field_value,form_id,field_id) values('$auditform_comments','$form_id','auditform_comments')");
    }
}

$_SESSION["encounter"] = $encounter;

echo "<script>window.close();

    window.opener.location.href = '../../reports/incomplete_charts.php?encounter=".$encounter."#".$encounter."';</script>";

function save_form_flag($lbf_formid){
    
    if($lbf_formid != 0 ){
      $formid = $lbf_formid; 
    }else{
        $result = mysql_query("SELECT id FROM form_encounter ORDER BY id DESC LIMIT 1");
        while ($row = mysql_fetch_array($result,MYSQL_ASSOC)){
            $formid =  $row['id']; 
        } 
    }
    $logdata= array();
    $data = mysql_query("SELECT logdate from `tbl_allcare_formflag` WHERE  form_id=".$formid." AND form_name = 'Patient Encounter'");
    while ($row = mysql_fetch_array($data,MYSQL_ASSOC)) {
        $array =  unserialize($row['logdate']);
        $count= count($array);
    }
    $count = isset($count)? $count: 0;
    $status = $_REQUEST['form_audited_status'];
//    $pending = $_POST['pending'];
//    $finalized = $_POST['finalized'];
    
    $ip_addr=GetIP();
    if($lbf_formid == 0):
            $array2[] = array( 'authuser' =>$_SESSION["authUser"],'Status' => $status,'date' => date("Y/m/d"), 'action'=>'updated','ip_address'=>$ip_addr.'(Incomplete Charts Audit Form)' ,'count'=> $count+1);
            $logdata = array_merge_recursive($array, $array2);
            $logdata= ($logdata? serialize($logdata): serialize($array2) );
            sqlInsert("INSERT into `tbl_allcare_formflag`(`form_id`, `encounter_id`, `form_name`, `logdate`) VALUES(".$formid.",".$GLOBALS['encounter'].",'Patient Encounter','".$logdata."')");
    else: 
            $result = mysql_query("SELECT * FROM tbl_allcare_formflag WHERE `form_name` = 'Patient Encounter' AND `form_id` =  ".$formid);
            if(mysql_num_rows($result) > 0){
                    $array2[] = array( 'authuser' =>$_SESSION["authUser"],'Status' => $status, 'date' => date("Y/m/d"), 'action' => 'updated','ip_address'=>$ip_addr.'(Incomplete Charts Audit Form)' ,'count'=> $count+1);
                    $logdata = array_merge_recursive($array, $array2);
                    $logdata= ($logdata? serialize($logdata): serialize($array2) );
                    sqlInsert("UPDATE `tbl_allcare_formflag` SET `logdate` ='".$logdata."'  WHERE `form_name` = 'Patient Encounter' AND `form_id` =  ".$formid);
            }else{ 
                    $array2[] = array( 'authuser' =>$_SESSION["authUser"],'Status' => $status, 'date' => date("Y/m/d"), 'action'=>'updated','ip_address'=>$ip_addr.'(Incomplete Charts Audit Form)' ,'count'=> $count+1);
                    $logdata = array_merge_recursive($array, $array2);
                    $logdata= ($logdata? serialize($logdata): serialize($array2) );
                    sqlInsert("INSERT into `tbl_allcare_formflag`(`form_id`, `encounter_id`, `form_name`, `logdate`) VALUES(".$formid.",".$GLOBALS['encounter'].",'Patient Encounter','".$logdata."')");
            }
    endif;
}

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
