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


////SANITIZE ALL ESCAPES
//$sanitize_all_escapes=true;
//
////STOP FAKE REGISTER GLOBALS
//$fake_register_globals=false; 


require_once("../interface/globals.php");
require_once("../library/formdata.inc.php"); 
require_once("../library/globals.inc.php");
require_once("$srcdir/api.inc");
require_once("$srcdir/forms.inc");
require_once("$srcdir/options.inc.php");
require_once("$srcdir/patient.inc");
require_once("$srcdir/formdata.inc.php");
require_once("$srcdir/formatting.inc.php");

$encounter      = $_POST['encounter'];
$pid            = $_POST['pid'];
$type           = $_POST['type'];
$form_id        = $_POST['form_id'];
$field_id       = str_replace("form_", "" ,$_POST['field_id']);
$field_val      = rtrim($_POST['field_val'], "|");
$username       = $_SESSION['portal_username'];

//print_r( $_POST);
// CODE FOR LBF2 FORMS to save fields

if($type == 'LBF2'){
    if($form_id != 0){
        save_LBF_data($form_id,$field_id,$field_val);
        $new_id[] = $form_id;
    }else{
        $new_lbf    = 0;
        $lastformid = sqlStatement("SELECT MAX(form_id) as forms FROM forms WHERE formdir='LBF2'");
        while($new_form_id = sqlFetchArray($lastformid)){
            $new_lbf = $new_form_id['forms'] + 1;
        }
        $get_new_form_id = sqlStatement("INSERT INTO forms (date,encounter, form_name, form_id, pid, user, groupname, authorized, deleted, formdir) VALUES (NOW(), $encounter, 'Allcare Encounter Forms', $new_lbf, $pid, '$username', 'Default', 1, 0, 'LBF2' )");
        if($get_new_form_id){
            save_LBF_data($new_lbf,$field_id,$field_val);
        }
        $new_id[] = $new_lbf;
    }
}
if(empty($new_id))
    $new_id[] = $form_id;
echo json_encode($new_id);

function save_LBF_data($form_id,$field_id,$field_val){
    $get_lbf_data = sqlStatement("SELECT * FROM lbf_data WHERE form_id = '$form_id' AND field_id = '$field_id'");
    $set_lbf_data = sqlFetchArray($get_lbf_data);        
    if(empty($set_lbf_data)) {
        if($field_val != '')
            $insert_lbf = sqlStatement("INSERT into lbf_data (form_id, field_id, field_value) VALUES ('$form_id','$field_id','".addslashes($field_val)."')");
    } else {
       $insert_lbf = sqlStatement("UPDATE lbf_data SET field_value = '".addslashes($field_val)."' WHERE field_id ='$field_id'  AND form_id = '$form_id'");
    }
}

// END OF LBF2 FORMS CODE

/* CODE FOR HISTORY FORM SAVE */

if($type == 'HIS') {
    $nameslist2     = '';
    $namesvalues2   = '';
    $checkname      = array();
    
    $gethistorydatasql = sqlStatement("SELECT * FROM history_data where pid = '$pid' ORDER BY id DESC LIMIT 1 ");
    $hisresultset = sqlFetchArray($gethistorydatasql);
    
    if(date("Y-m-d", strtotime($hisresultset['date'])) == date("Y-m-d")){
        $insert_history = sqlStatement("UPDATE history_data SET `$field_id` = '".addslashes($field_val)."' WHERE id = ".$hisresultset['id']." AND pid= '$pid'" );
//        echo "UPDATE history_data SET `$field_id` = '".addslashes($field_val)."' WHERE id = ".$hisresultset['id']." AND pid= $pid";
    }else{
        if(!empty($hisresultset)){ 
            foreach($hisresultset as $key => $his){
                if($key == 'id'){

                }else if($key == $field_id){
                    $namesvalues2 .= "'".addslashes($field_val)."',";
                }else if($key == 'date'){
                    $namesvalues2 .= "NOW(),";
                }else{     
                    $namesvalues2 .= "'".addslashes($his)."',";
                }
            }
        }else{ 
            $columnnames      = sqlStatement("select COLUMN_NAME from information_schema.columns where table_name='history_data'");
            while($columnnamesset  = sqlFetchArray($columnnames)){
                if($columnnamesset['COLUMN_NAME'] == 'id' ){
                    
                }else if($columnnamesset['COLUMN_NAME'] == $field_id){
                    $namesvalues2 .= "'".addslashes($field_val)."',";
                }else if($columnnamesset['COLUMN_NAME'] == 'date'){
                    $namesvalues2 .= "NOW(),";
                }else if($columnnamesset['COLUMN_NAME'] == 'pid'){
                    $namesvalues2 .= "$pid,";
                }else{     
                    $namesvalues2 .= "'',";
                }
            }
        }
        $columnsql      = sqlStatement("select COLUMN_NAME from information_schema.columns where table_name='history_data'");
        while($dataresultset  = sqlFetchArray($columnsql)){
            if($dataresultset['COLUMN_NAME'] != 'id' )
                $nameslist2 .=  "`".$dataresultset['COLUMN_NAME']."`,";
        }
        $nameslist = rtrim($nameslist2, ',');
        $namesvalues = rtrim($namesvalues2, ',');

        $insert_history = sqlStatement("INSERT INTO history_data ($nameslist) VALUES($namesvalues)"); 
    }
}
 
 /* END OF HISTORY FORM CODE */


?>