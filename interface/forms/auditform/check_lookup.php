<?php
//SANITIZE ALL ESCAPES
$sanitize_all_escapes=true;

//STOP FAKE REGISTER GLOBALS
$fake_register_globals=false;

include_once("../../globals.php");
include_once("$srcdir/api.inc");
require_once("$srcdir/patient.inc");
require_once("$srcdir/options.inc.php");
require_once("$srcdir/htmlspecialchars.inc.php");
include_once("$srcdir/acl.inc");
//print_r($_POST);
$dataarray          = $_POST['dataarray'];
$dataarray2         = $_POST['dataarray2'];
$dataarray3         = $_POST['dataarray3'];
$dataarray4         = $_POST['dataarray4'];
$hiddenfield        = $_POST['hiddenspanfield'];
$checkvalue_uncheck = $_POST['checkvalue_uncheck'];
if(trim(substr($_POST['group'],1)) == 'Exam'){
    $get_list_fields_data = sqlStatement("SELECT list_id FROM list_options WHERE option_id = '".trim($_POST['field'])."' AND FIND_IN_SET( ".trim($_POST['field_count']).", notes ) >0");
    while($set_list_fields_data = sqlFetchArray($get_list_fields_data)){
        $return_array2[] =  $set_list_fields_data['list_id'];
    }
    $return_array[0] = $return_array2;
    if(empty($return_array2)){
        //echo "SELECT notes FROM list_options WHERE list_id = 'COMPREHENSIVE' and option_id='".trim($_POST['field'])."'";
        $get_com_checklist = sqlStatement("SELECT notes FROM list_options WHERE list_id = 'COMPREHENSIVE' and option_id='".trim($_POST['field'])."'");
        $set_com_checklist = sqlFetchArray($get_com_checklist);
        $exploded_exam = explode(';',$set_com_checklist['notes']);
        $exploded_exam2 = explode(',',$exploded_exam[1]);
        $count_numeric      = 0;
        $count_non_numeric  = 0;
        for($i = 0; $i<count($exploded_exam2); $i++){
            $exploded_inner_field = explode('(',$exploded_exam2[$i] ); 
            $inner_field_value = explode(')',$exploded_inner_field[1] );
            $array_check[$exploded_inner_field[0]] = $inner_field_value[0];
            if(is_numeric($inner_field_value[0])){
                $count_numeric = $count_numeric + 1;
            }else{
                $count_non_numeric = $count_non_numeric + 1;
            }
        }
        $count_numeric2      = 0;
        $count_non_numeric2  = 0;
        foreach($array_check as $array_key => $array_value){
            for($i=0; $i<=count($dataarray); $i += 3){
                if(trim($_POST['group']).$array_key == $dataarray[$i]){ $arr[] = $dataarray[$i]."==".$dataarray[$i+1]."==".$dataarray[$i+2];
                    if(is_numeric($array_value)){
                        if(  $dataarray[$i+1] >= $array_value ){//$arr[] ='test'.$i;
                            $count_numeric2 = $count_numeric2 + 1 ;
                        }
                    }else{
                        if($dataarray[$i+2] ==  0){//$arr[] = "hi".$i;
                            $count_non_numeric2 = $count_non_numeric2 + 1;
                        }
                    } 
                }    
            }  
        }
        if($count_non_numeric == $count_non_numeric2 && $count_numeric == $count_numeric2){
            $return_array[0]='COMPREHENSIVE';      
        }else{
            if(trim($_POST['field']) == 'e_gmse' && $count_numeric2 >= 9){
                $return_array[0]='COMPREHENSIVE'; 
            }else if($_POST['field_count'] == 0){
                $return_array[0]= '';
            }else{
                $return_array[0]= 'DETAILED';
            }
        }
    }
    $return_array[1]  = '';
//    $return_array[2]= $arr; 
//    $return_array[3]= $count_non_numeric ."==". $count_non_numeric2 ."&&". $count_numeric ."==".$_POST['field']. $count_numeric2;
}elseif(trim(substr($_POST['group'],1)) == 'Decision'){
    $values = '';
    $count = 0;
    if(trim($_POST['field']) === 'd_risk'){
        for($i=0; $i<count($dataarray2);$i += 2){
            $option_id = $dataarray2[$i];
            $data_check = $dataarray2[$i+1];
            $get_list_fields_data2 = sqlStatement("SELECT notes FROM list_options WHERE option_id = '".$option_id."'");
            while($set_list_fields_data2 = sqlFetchArray($get_list_fields_data2)){
                $data_value = $set_list_fields_data2['notes'];
            }
        }
        $array_values = array( 4 => 'High', 3=> 'Moderate', 2 => 'Low', 1 => 'Minimal' );
        $key1 = array_search($data_value, $array_values );
        $key2 = array_search($data_check, $array_values );
        if(empty($key2)) $key2 = 0;
        if($key1>$key2){
            $values4 = $data_value;
        }else{
            $values4 = $data_check;
        }
        $return_array[0] = $values4;
    }else if(trim($_POST['field']) == 'd_acd'){
        foreach($dataarray3 as $value){
            $value2 = str_replace('Amount_Complexity_of_Data','',  $value);
            $get_l_values = sqlStatement("SELECT notes FROM list_options WHERE list_id like '%Amount_Complexity_of_Data%' AND option_id ='$value2'");
            while($set_l_values = sqlFetchArray($get_l_values)){
                $count = $count + $set_l_values['notes'];
            }
        }
    }else{
        for($i=0; $i<count($dataarray3); $i += 2) { 
            $get_l_values = sqlStatement("SELECT notes FROM list_options WHERE list_id like '%Diagnosis_Management_Options%' AND option_id ='$dataarray3[$i]'");
            while($set_l_values = sqlFetchArray($get_l_values)){
                $exploded = explode(';', $set_l_values['notes']);
                $exploded2 = explode(',', $exploded[0]);
                $count = $count + ($dataarray3[$i+1] * $exploded[1]);
            }
        }
    }
    if($count== 0){
        $values = 'None-0';
        $field = '';
    }elseif($count == 1){
        $values = 'Minimal-1';
        $field = 'Minimal';
    }elseif($count == 2){
        $values = 'Limited-2';
        $field = 'Limited';
    }elseif($count == 3){
        $values = 'Moderate-3';
        $field = 'Moderate';
    }else{
        $values = 'Extensive-4';
        $field = 'Extensive';
    }
    $data = '';
    $data2 = '';
    $titles_array2 = array();
    for($i=0; $i<count($dataarray4); $i += 2){
        if(trim($_POST['field']) === $dataarray4[$i]){
            if(trim($_POST['field']) === 'd_risk'){
                $values3 = $values4;
            }else{
                $values3 = $field;
            }    
        }else{
            $values2 = explode('-',$dataarray4[$i+1]);
            $values3 = trim($values2[0]);
        }
        $get_title_names = 0;
        $get_title_names = sqlStatement("SELECT title FROM list_options WHERE option_id = '".$dataarray4[$i]."' AND FIND_IN_SET( '".$values3."', title ) >0");
        if(mysql_num_rows($get_title_names)>0){
            while($set_title_names = sqlFetchArray($get_title_names)){
                $set_title = '';
                $set_title = $set_title_names['title'];
                $titles_array2[$dataarray4[$i]] = $set_title;
            }
        }else{
            $titles_array2[$dataarray4[$i]] = '';
        }
    }
    if(trim($_POST['field']) == 'd_risk')
        $return_array[0] = $values4; 
    else 
        $return_array[0] = $values; 
    
    $titles = array();
    foreach ($titles_array2 as $value){
        $get_list_fields = sqlStatement("SELECT list_id, option_id, title FROM list_options WHERE title like '%$value%' AND list_id IN('HIGH_COMPLEX','MODERATE_COMPLEX','LOW_COMPLEX','STRAIGHT_FORWARD')");//'EXP_PROB_FOCUSED','DETAILED','COMPREHENSIVE') group by list_id");
        if(mysql_num_rows($get_list_fields)>0){
            while($set_list_fields = sqlFetchArray($get_list_fields)){
                $titles[] = $set_list_fields['list_id'];
            }
        }
    } 
    
    $names_list = array('STRAIGHT_FORWARD','LOW_COMPLEX','MODERATE_COMPLEX', 'HIGH_COMPLEX');
    foreach ($names_list as $value){
        $title = '';
        $get_list_fields = sqlStatement("SELECT list_id, option_id, title FROM list_options WHERE list_id IN ('$value')");
        while($set_list_fields = sqlFetchArray($get_list_fields)){
            $list_fields_array[$set_list_fields['option_id']] = $set_list_fields['title'];
            $title = $set_list_fields['list_id'];
        }
        $result = array_diff_assoc ($titles_array2 ,$list_fields_array);
        if(empty($result)){
            $return_array[1] = $title;
            break;
        }
    }
    if($return_array[1] == ''){
        $titles = array_unique($titles);

        if(in_array('STRAIGHT_FORWARD', $titles)){
            $return_array[1] = 'STRAIGHT_FORWARD';
        }else if(in_array('LOW_COMPLEX', $titles)){
            $return_array[1] = 'LOW_COMPLEX';
        }else if(in_array('MODERATE_COMPLEX', $titles)){
            $return_array[1] = 'MODERATE_COMPLEX';
        }else if(in_array('HIGH_COMPLEX', $titles)){
            $return_array[1] = 'HIGH_COMPLEX';
        }
    }
    if(count($return_array) == 1){
        array_push($return_array, '');
    }
}else{
    $get_list_fields2 = sqlStatement("SELECT title FROM list_options WHERE option_id = '".trim($_POST['field'])."' AND FIND_IN_SET( ".trim($_POST['field_count']).", notes ) >0");
    $return_array = array();
    while($set_list_fields2 = sqlFetchArray($get_list_fields2)){
        $return_array[0] =  $set_list_fields2['title'];
    }
    if(empty($return_array[0])) $return_array[0] = '';
//    array_push($return_array, "SELECT title FROM list_options WHERE option_id = '".trim($_POST['field'])."' AND FIND_IN_SET( ".trim($_POST['field_count']).", notes ) >0");
    $count = '';
    $listarray = array();
    $countarray = array();
    $dataarray5 = array();
    $count_hpi = 0;
    for($i=0; $i<count($dataarray); $i += 2) { 
        $countarray[$dataarray[$i]] = $dataarray[$i+1];
        if($dataarray[$i] == 'h_cc' || $dataarray[$i] == 'h_hpi'){
           $count_hpi = $count_hpi+ $dataarray[$i+1];
           $data_array5 ['h_hpi'] = $count_hpi;
        }else{
            $data_array5[$dataarray[$i]]=  $dataarray[$i+1];
        }
    }
    $history_array = array();
    foreach($data_array5 as $data_key => $data_value){
        if(substr($data_key, 0,2) == 'h_'){
            $history_array[$data_key] = $data_value;
        }
    }
    foreach($data_array5 as $data_key => $data_value){
        $countarray[$data_key] = $data_value;
        if(substr($data_key, 0,2) == 'h_'){
            $get_title_names = sqlStatement("SELECT title FROM list_options WHERE option_id = '".$data_key."' AND FIND_IN_SET( ".$data_value.", notes ) >0");
            if(mysql_num_rows($get_title_names)>0){
                while($set_title_names = sqlFetchArray($get_title_names)){
                    $set_title = '';
                    $set_title = $set_title_names['title'];
                    $titles_array[$data_key] = $set_title;
                }
            }else{
                $titles_array[$data_key] = '';
            }
        }
    }
    foreach ($titles_array as $value){
        $titles = '';
        $get_list_fields = sqlStatement("SELECT list_id, option_id, title FROM list_options WHERE title like '%$value%' AND list_id IN('COMPREHENSIVE','DETAILED','EXP_PROB_FOCUSED','PROBLEM_FOCUSED')");
        if(mysql_num_rows($get_title_names)>0){
            while($set_list_fields = sqlFetchArray($get_list_fields)){
                $list_fields_array[$set_list_fields['option_id']] = $set_list_fields['title'];
                $titles[] = $set_list_fields['list_id'];
            }
        }
    } 
    
    $names_list = array('PROBLEM_FOCUSED', 'EXP_PROB_FOCUSED','DETAILED','COMPREHENSIVE');
    foreach ($names_list as $value){
        $title = '';
        $get_list_fields = sqlStatement("SELECT list_id, option_id, title FROM list_options WHERE list_id IN ('$value')");
        if(mysql_num_rows($get_title_names)>0){
            while($set_list_fields = sqlFetchArray($get_list_fields)){
                $list_fields_array[$set_list_fields['option_id']] = $set_list_fields['title'];
                $title = $set_list_fields['list_id'];
            }
        }
        $result = array_diff_assoc ($titles_array ,$list_fields_array);
        if(empty($result)){
            array_push($return_array, $title);
            break;
        }    
    }    
    if($return_array[1] == ''){
        $titles = array_unique($titles);
        
        if(in_array('PROBLEM_FOCUSED', $titles))
            array_push($return_array, 'PROBLEM_FOCUSED');
        else if(in_array('EXP_PROB_FOCUSED', $titles))
            array_push($return_array, 'EXP_PROB_FOCUSED');
        else if(in_array('DETAILED', $titles))
            array_push($return_array, 'DETAILED');
        else if(in_array('COMPREHENSIVE', $titles))
            array_push($return_array, 'COMPREHENSIVE');
    }

    if(count($return_array) == 1){
        array_push($return_array, '');
    }
}
echo json_encode($return_array); 
?>
