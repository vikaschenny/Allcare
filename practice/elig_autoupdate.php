<?php
//
require_once("verify_session.php");
 
if(isset($_SESSION['portal_username']) !=''){
    $provider    = $_SESSION['portal_username'];
    $refer       = $_REQUEST['refer'];
    
    if($_REQUEST['refer']!='')
        $_SESSION['refer'] = $_REQUEST['refer'];
}else {
    $provider                    = $_REQUEST['provider'];
    $_SESSION['portal_username'] = $_REQUEST['provider'];
    //for logout
    $refer                       = $_REQUEST['refer'];
    if($_REQUEST['refer']!='')
        $_SESSION['refer'] = $_REQUEST['refer'];
}
 require_once("../interface/globals.php");

$pid=$_REQUEST['pid'];
$get_fields = '';
$tabledata = array();

function getAllrecords(){
    global $getFields;
    global $pid;
    global $tabledata;
    $getFields = sqlStatement("SELECT field_id,title FROM layout_options WHERE form_id='ELIGIBILITY' AND uor <> 0 ORDER BY group_name, seq"); 
    while($rowfields = sqlFetchArray($getFields)){
        $get_fields .= "`".$rowfields['field_id']."`,";
    }
    $get_fields_names = rtrim($get_fields,",");
    $sql=sqlStatement("select `id`,$get_fields_names from tbl_eligibility_response_data where pid=$pid ORDER BY updated_date DESC"); 
    while($row=sqlFetchArray($sql)){
        $rowid = $row['id'];
        array_shift($row);
        $tabledata[] = array("DT_RowId"=>"row_".$rowid)+$row;
    }
    $jsond['data']= $tabledata;
    return json_encode($jsond);
}

function geteditrecords($data,$get_fields_names,$pid,$provider){
    global $getFields;
    global $tabledata;
    
   foreach($data as $key_name =>$valuearray){
        $id = str_replace("row_","",$key_name);
        foreach ($valuearray as $key => $value) {
            $field_id       = $key;
            $field_val    = $value;
        }
    }
    $save_elig_data = sqlStatement("UPDATE tbl_eligibility_response_data SET `$field_id` = '".addslashes($field_val)."', updated_date = NOW(),`domain` = 'Provider_Portal_Eligibility_Response' , `user` = '$provider' WHERE id = $id");
    
    $getFields = sqlStatement("SELECT field_id,title,group_name FROM layout_options WHERE form_id='ELIGIBILITY' AND uor <> 0 ORDER BY group_name, seq"); 
    while($rowfields = sqlFetchArray($getFields)){
        $get_fields .= "`".$rowfields['field_id']."`,";
        if($rowfields['field_id'] == $field_id)
            $type = substr($rowfields['group_name'],1);
    }
    $get_fields_names = rtrim($get_fields,",");
    
    /* Eligibility Screen Code */
    if($type == 'STATS') {
        // insert in demographics page
        $getstatssql = sqlStatement("SELECT * FROM patient_data where pid = $pid ");
        while($statsresultset = sqlFetchArray($getstatssql)){
            $insert_field_value = sqlStatement("UPDATE patient_data SET `$field_id` = '".addslashes($field_val)."' WHERE pid = $pid");
        }
    }

    if($type == 'Insurance'){
        $get_plan_name = sqlStatement("SELECT plan_name FROM insurance_data WHERE pid = $pid AND type='primary'");
        $set_plan_name = sqlFetchArray($get_plan_name);
        if($set_plan_name){
            $update_insurance_data = sqlStatement("UPDATE insurance_data SET `$field_id` = '".addslashes($field_val)."' WHERE pid = $pid AND type='primary'");
        }else{
            $insert_insurance_data = sqlStatement("INSERT INTO insurance_data (`pid`,`type`,`accept_assignment`,`$field_id`) VALUES ($pid,'primary','YES','".addslashes($field_val)."')");
        }
//        $new_id = eligibility_table($form_id,$field_id,$field_val,$pid,$month,$username,$patient_bal,$insurance_bal,$total_bal);
    }

    if($type == 'Insurance and STATS'){
        // insurance
        $get_plan_name = sqlStatement("SELECT * FROM insurance_data WHERE pid = $pid AND type='primary'");
        $set_plan_name = sqlFetchArray($get_plan_name);
        if($set_plan_name){
            $update_insurance_data = sqlStatement("UPDATE insurance_data SET `$field_id` = '".addslashes($field_val)."' WHERE pid = $pid AND type='primary'");
        }else{
            $insert_insurance_data = sqlStatement("INSERT INTO insurance_data (`pid`,`type`,`accept_assignment`,`$field_id`) VALUES ($pid,'primary','YES','".addslashes($field_val)."')");
        }
        // insert in demographics page
        $getstatssql = sqlStatement("SELECT * FROM patient_data where pid = $pid ");
        while($statsresultset = sqlFetchArray($getstatssql)){
            $insert_field_value = sqlStatement("UPDATE patient_data SET `$field_id` = '".addslashes($field_val)."' WHERE pid = $pid");
        }
    }
    /* End of Eligibility Screen Code */ 
    
    $get_data = sqlStatement("select `id`,$get_fields_names from tbl_eligibility_response_data where id= $id");
    while($row = sqlFetchArray($get_data)){
        $rowid = $row['id'];
        array_shift($row);
        $tabledata[] = array("DT_RowId"=>"row_".$rowid)+$row;
    }
    $jsond['data']= $tabledata;
    return json_encode($jsond);
}
if($_POST['action']=='edit'){
    echo geteditrecords($_REQUEST['data'],$get_fields_names,$pid,$provider);
}else{
   echo getAllrecords();
}
