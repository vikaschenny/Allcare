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

$selected = $_POST['selected'];
$new_array = array();
$array_values2 = array(1 => 'Minimal', 2 => 'Low' , 3=> 'Moderate', 4 => 'High');
        
foreach($selected as $option_id){
    $get_list_fields_data2 = sqlStatement("SELECT notes FROM list_options WHERE option_id = '".substr($option_id,4)."'");
    while($set_list_fields_data2 = sqlFetchArray($get_list_fields_data2)){
        $data_value = $set_list_fields_data2['notes'];
        $array_values = array(1 => 'Minimal', 2 => 'Low' , 3=> 'Moderate', 4 => 'High');
        $key1 = array_search($data_value, $array_values );
        $new_array[$key1] = $data_value;
    }
}
krsort($new_array);
foreach($new_array as $key => $value){
    $return_array[0] = $new_array[$key];
    //$return_array[2] = $key;
    break;
}
if(empty($return_array[0]))
    $return_array[0] = '';
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
        }else{
            $return_array[1] = '';
        }
    }
//$return_array[1] = $titles_array2;
echo json_encode($return_array); 
