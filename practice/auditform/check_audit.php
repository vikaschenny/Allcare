<?php
//SANITIZE ALL ESCAPES
$sanitize_all_escapes=true;

//STOP FAKE REGISTER GLOBALS
$fake_register_globals=false;

include_once('../verify_session.php');
include_once("$srcdir/api.inc");
require_once("$srcdir/patient.inc");
require_once("$srcdir/options.inc.php");
require_once("$srcdir/htmlspecialchars.inc.php");
include_once("$srcdir/acl.inc");

$selected_data      = $_POST['selected_data'];
$title              = $_POST['posted_title'];
$encounter          = $_POST['encounter'];
$pid                = $_POST['pid'];
$established        = 0;
$new                = 0;
$check_audit_time   = $_POST['check_audit_time'];

if(stripos($title,'Certification') === false || stripos($title,'Certification') === false){
    if(trim($check_audit_time) == "T:0" || trim($check_audit_time) == "T:"){
        if(stripos($title,'_Estab') !== false || stripos($title,'subseq_') !== false || stripos($title,'FU_') !== false){
            $established = 1;
        }

        if(stripos($title,'_new') !== false || stripos($title,'Int_') !== false || stripos($title,'Obser_') !== false){
            $new = 1;
        }
        $get_title_names = sqlStatement("SELECT notes,option_id FROM list_options WHERE list_id = 'Level_Of_Service' AND title='$title'");
        while($set_title_names = sqlFetchArray($get_title_names)){
            $tobe_check_string[]    = $set_title_names['notes'];
            $check_code[]           = $set_title_names['option_id'];
        } 
        $actualstring_array = explode(";",$selected_data);
        for($i=0; $i< count($tobe_check_string); $i++){
            $string_final_to_array = explode(",",$tobe_check_string[$i]);
            for($k = 0; $k< count($string_final_to_array); $k++){
                $checkcount = 0;
                $string_final_to_check = explode("T:",$string_final_to_array[$k]);
                for($j=0; $j< count($actualstring_array); $j++){
                    $checkstring_for_code = $string_final_to_check[0];
                    if(strpos($checkstring_for_code,$actualstring_array[$j] ) !== false){
                        $codes_array[]  = $check_code[$i]; 
                        $checkcount     = $checkcount + 1;
                    }
                }
                if($established == 1){
                    if($checkcount >= 2){
                        $get_title_names = sqlStatement("SELECT option_id FROM list_options WHERE list_id = 'Level_Of_Service' AND title='$title' and notes = '".$tobe_check_string[$i]."'");
                        while($set_title_names = sqlFetchArray($get_title_names)){
                            $return_array[0] = $set_title_names['option_id'];
                        }
                        break;
                    }
                }
                if($new == 1){
                    if($checkcount >= 3){
                        $get_title_names = sqlStatement("SELECT option_id FROM list_options WHERE list_id = 'Level_Of_Service' AND title='$title' and notes = '".$tobe_check_string[$i]."'");
                        while($set_title_names = sqlFetchArray($get_title_names)){
                            $return_array[0] = $set_title_names['option_id'];
                        }
                        break;
                    }
                }
            }
        }
        if(empty($return_array[0])){
            sort($codes_array);
            $codes_array2 = array_values(array_unique($codes_array));
            if($established == 1)
                $return_array[0] = $codes_array2[1];
            if($new == 1)
                $return_array[0] = $codes_array2[0];
        }
    }else{
        $get_title_names = sqlStatement("SELECT notes FROM list_options WHERE list_id = 'Level_Of_Service' AND title='$title'");
        while($set_title_names = sqlFetchArray($get_title_names)){
            $tobe_check_string[] = $set_title_names['notes'];
        }
        for($i=0; $i< count($tobe_check_string); $i++){
            $string_final_to_array = explode(",",$tobe_check_string[$i]);
            for($k = 0; $k< count($string_final_to_array); $k++){
                $time_check_success = 0;
                $string_final_to_check = explode("T:",$string_final_to_array[$k]);
                if(trim($string_final_to_check[1]) === str_replace("T:","",$check_audit_time)){
                    $time_check_success = 1;
                }
                if($time_check_success == 1){
                    $get_title_names = sqlStatement("SELECT option_id FROM list_options WHERE list_id = 'Level_Of_Service' AND title='$title' and notes LIKE '%".$string_final_to_array[$k]."%'");
                    while($set_title_names = sqlFetchArray($get_title_names)){
                        $return_array[0] = $set_title_names['option_id'];
                    }
                    break;
                }
            }
        }
    }
}else{
    $get_form_id = sqlStatement("SELECT form_id from forms where encounter = $encounter and pid = $pid and formdir='LBF2' AND deleted = 0 order by date desc limit 0,1 ");
    while($set_form_id = sqlFetchArray($get_form_id)){
        $form_id = $set_form_id['form_id'];
    }
    if(!empty($form_id)){
        $get_form_data = sqlStatement("SELECT field_value FROM lbf_data WHERE form_id = '$form_id' AND field_id = 'cert_recert_doc_link' ");
        while($set_form_data = sqlFetchArray($get_form_data)){
            if($set_form_data != ''){
                $get_title_names = sqlStatement("SELECT option_id FROM list_options WHERE list_id = 'Level_Of_Service' AND title='$title' ");
                while($set_title_names = sqlFetchArray($get_title_names)){
                    $return_array[0] = $set_title_names['option_id'];
                }
            }
        }
    }
}
if(empty($return_array[0])) $return_array[0] = 'None';
echo json_encode($return_array); 
?>