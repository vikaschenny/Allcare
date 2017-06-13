<?php

include_once("../../globals.php");
include_once($GLOBALS["srcdir"]."/api.inc");

$pid=$_REQUEST['pid'];
$encounter=$_REQUEST['encounter'];
$id=$_REQUEST['id'];



function auditform_report( $pid, $encounter, $cols, $id) {
    $data = formFetch("tbl_form_audit", $id);
    if ($data) {
        foreach($data as $key => $value) {
            if ($key == "id" || $key == "pid" || $key == "user" || $key == "count" || $key == "authorized" || $key == "activity" || $key == "date" || $value == "" || $value == "0000-00-00 00:00:00") {
                    continue;
            }
            
            if($key == 'audit_data'){
                $audit_data = unserialize($value);     
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


$resid=sqlStatement("select CONCAT(fname,'',lname) as name  from patient_data where pid=$pid");  
$frow = sqlFetchArray($resid);


echo "<b>Patient Name:</b>".$frow['name']."<br>";
echo "<b>Encounter:</b>".$encounter."<br>";

echo "<h4>Audit Form View: </h4>";
auditform_report($pid,$encounter, '*' ,$id);
if($id==0){
    echo "<h5 style='color:red'>Not Started</h5>";
}
echo "<br>";
echo $note=$_POST['form_audit_note'];

if($note!=''){
 
   $sql=sqlStatement("UPDATE `form_encounter` SET `audit_note`='$note' WHERE encounter='$encounter' AND pid='$pid'");
}
$login_userid=$_SESSION['authId'];
?> 
<html>
    <head>
        
    </head>
    <body>
        <form id="audit_note" name="audit_note" action="" method="post">
            <?php   $resid_ros=sqlStatement("select audit_note  from form_encounter where encounter  =$encounter AND pid=$pid");  
                    $frow_ros = sqlFetchArray($resid_ros);
                    if($frow_ros!=''){
                        $audit_note=$frow_ros['audit_note'];
                    }else{
                         $audit_note='';
                    }
                    $sql_vis=sqlStatement("SELECT provider_incomp from tbl_user_custom_attr_1to1 where userid=$login_userid");
                    $row1_vis=sqlFetchArray($sql_vis);
                    if(!empty($row1_vis)){
                         $avail3=explode("|",$row1_vis['provider_incomp']);
                         if(in_array('audit_note',$avail3)){
                              $sql=sqlStatement("SELECT id, fname, lname, specialty FROM users " .
                                  "WHERE active = 1 AND ( info IS NULL OR info NOT LIKE '%Inactive%' ) " .
                                  "AND authorized = 1 AND username='".$login_userid."'" .
                                  "ORDER BY lname, fname");
                            $idval=sqlFetchArray($sql);
                            if(!empty($idval)){ ?>
                                 <b>Audit Note:</b><textarea name="form_audit_note" id="form_audit_note" title="" cols="60" rows="10" disabled><?php echo $audit_note; ?></textarea>
                                    <input type="submit" name="submit" value="save" disabled/>
                           <?php }else { ?>
                                 <b>Audit Note:</b><textarea name="form_audit_note" id="form_audit_note" title="" cols="60" rows="10"><?php echo $audit_note; ?></textarea>
                                 <input type="submit" name="submit" value="save" />
                           <?php }
                             
                         }
                    }
?>
           
        </form>
    </body>
</html>
