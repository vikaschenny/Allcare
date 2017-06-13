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

//

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

function geteditrecords($data,$get_fields_names){
    global $getFields;
    global $tabledata;
    
   foreach($data as $key_name =>$valuearray){
        $id = str_replace("row_","",$key_name);
        foreach ($valuearray as $key => $value) {
            $field_id       = $key;
            $field_value    = $value;
        }
    }
    $save_elig_data = sqlStatement("UPDATE tbl_eligibility_response_data SET `$field_id` = '".addslashes($field_value)."' WHERE id = $id");
    
    $getFields = sqlStatement("SELECT field_id,title FROM layout_options WHERE form_id='ELIGIBILITY' AND uor <> 0 ORDER BY group_name, seq"); 
    while($rowfields = sqlFetchArray($getFields)){
        $get_fields .= "`".$rowfields['field_id']."`,";
    }
    $get_fields_names = rtrim($get_fields,",");
    
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
    echo geteditrecords($_REQUEST['data'],$get_fields_names);
}else{
   echo getAllrecords();
}
