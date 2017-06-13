<?php

include_once("../../globals.php");
include_once($GLOBALS["srcdir"]."/api.inc");
function auditform_report( $pid, $encounter, $cols, $id) {
    $data = formFetch("tbl_form_audit", $id);
    if ($data) {
        foreach($data as $key => $value) {
            if ($key == "id" || $key == "pid" || $key == "user" || $key == "count" || $key == "authorized" || $key == "activity" || $key == "date" || $value == "" || $value == "0000-00-00 00:00:00") {
                    continue;
            }
            
            if($key == 'audit_data'){
//                $audit_data = unserialize($value);     
                $audit_data2 = preg_replace_callback ( '!s:(\d+):"(.*?)";!',
                    function($match) {
                        return ($match[1] == strlen($match[2])) ? $match[0] : 's:' . strlen($match[2]) . ':"' . $match[2] . '";';
                    },
                $value );

                $audit_data = unserialize($audit_data2);
                $k = 0;
                $j = 0;
                foreach ($audit_data as $key1 => $value1) {
                    if (strpos($key1,'hidden') === false || $key1 == 'hiddenaudit') {
                        echo "<ul style='list-style-type: none;margin: 0;padding:0;'> ";
                        if(is_array($value1)){
                            echo "<u><b>".substr($key1,1)."</b></u>";
                            if(trim($audit_data['hidden'.$key1]) != '' )
                                echo "<span class='text' >(".trim($audit_data['hidden'.$key1]).")</span>";
                            $new_key_check = '';
                            $i=0;
                            echo "<br>";
                            if(substr($key1,1) == 'History' && $k == 0){
                                echo "<u><span class='bold' style='margin-left:10px;'>Chief Complaint:</span></u> &nbsp";
                                echo "<span class='text'>".$audit_data['Audit_CC_Optionstextarea']."</span><br>";
                                $k = 1;
                            }
                            if(substr($key1,1) == 'History' && $j == 0 && $audit_data[$key1]['Audit_CC_Optionsradio'] != 0){
                                echo "<u><span class='bold' style='margin-left:10px;'>Chronic Conditions:</span></u> &nbsp";
                                echo "<span class='text'>";
                                if($audit_data[$key1]['Audit_CC_Optionsradio'] == 3)
                                    echo "More than ";
                                echo $audit_data[$key1]['Audit_CC_Optionsradio']."</span><br>";
                                $k = 1;
                            }
                            foreach($value1 as $array_key => $array_value){
                                if(strpos($array_key,'Diagnosis_Management_Options') === false){
                                    $new_key = str_replace($array_value, '', $array_key);
                                    $get_key_name = sqlStatement("SELECT title FROM list_options WHERE option_id='$array_value' AND list_id='$new_key'");
                                    if(mysql_num_rows($get_key_name)>0){
                                        if($new_key != $new_key_check){
                                            $get_field_name = sqlStatement("SELECT title FROM layout_options WHERE list_id='$new_key' and form_id='AUDITFORM'");
                                            if(mysql_num_rows($get_field_name)>0){
                                                while($set_field_name = sqlFetchArray($get_field_name)){
                                                    echo "<u><span class='bold' style='margin-left:10px;'>".$set_field_name['title'].":</span></u><br>";
                                                }
                                            }
                                        }    
                                        while($set_key_name = sqlFetchArray($get_key_name)){
                                            echo "<li style='margin-left:20px;'><span class='text'>".$set_key_name['title']."</span></li>";
                                        }
                                    }
                                    $new_key_check = $new_key;
                                }else{
                                    $new_value = str_replace('Diagnosis_Management_Options', '', $array_key);
                                    $get_key_name2 = sqlStatement("SELECT title FROM list_options WHERE option_id='$new_value' AND list_id='Diagnosis_Management_Options'");
                                    if(mysql_num_rows($get_key_name2)>0){
                                        while($set_key_name2 = sqlFetchArray($get_key_name2)){
                                            if(!empty($array_value)){
                                                if($i==0){
                                                    echo "<u><span class='bold'>Diagnosis_Management_Options</span><br></u>";
                                                    $i++;
                                                }
                                                echo "<li><span class='text'>".$set_key_name2['title'].":   $array_value</span></li>";
                                            }    
                                        }
                                    }    
                                }
                            }
                        }else{
                            $key_name = '';
                            if($key1 == 'cpt_data')
                                $key_name = 'Visit Category';
                            else if($key1 == 'audit_time')
                                $key_name = ' Time';
                            else if($key1 == 'hiddenaudit')
                                $key_name = 'CPT Code';
                            else if($key1 == 'defaultcpo')
                                $key_name = 'Default CPO Minutes';
                            else if($key1 == 'defaultccm')
                                $key_name = 'Default CCM Minutes';
                            else if(strpos($key1, 'ic',0) !== false || strpos($key1, 'it',0) !== false || strpos($key1, 'history_unobtainable_radio',0) !== false || strpos($key1, 'history_unobtainable_textarea',0) !== false || strpos($key1, 'Audit_CC_Optionstextarea',0) !== false)
                                 $key_name = 1;
                            if($key_name != 1 && !empty($value1))
                                echo " <u><span class='bold'>$key_name</span></u>   :<span class='text'>".  str_replace("CPT Code:","",$value1)."</span><br>";
                        }
                    }
                    echo "</ul>";
                }
            }
        }
    }
}

?> 
