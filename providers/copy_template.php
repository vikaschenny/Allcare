<?php
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

$encounter          = $_REQUEST['encounter'];
$copied_to_enc1     = $_REQUEST['copied_to'];
$enc_val            = explode("_",$encounter);

?>
<html>
    <body>
       <?php
       $form_sql   = sqlStatement("SELECT DISTINCT form_name, form_id, formdir,encounter
                               FROM forms
                               WHERE encounter =$enc_val[0]
                               AND deleted =0
                               GROUP BY form_name
                               ORDER BY id DESC ");
        ?><form name="template_forms" id="template_forms" method="POST" action="">   <?php     
            while($row_form = sqlFetchArray($form_sql)){
                $fname  =   $row_form['form_name'];

                if($fname   == 'Allcare Physical Exam' || $fname == 'Allcare Review Of Systems' || $fname == 'Allcare Encounter Forms' || $fname == 'CPO'){
                    if($fname=='Allcare Encounter Forms'){    
                        $layout_forms   = sqlStatement("SELECT DISTINCT(group_name) FROM layout_options " .
                                                                   "WHERE form_id = 'LBF2' AND uor > 0 AND field_id != ''   AND group_name IN ('2Chief Complaint','4Progress Note','5Assessment Note','6Plan Note','7Face to Face HH Plan','3History of Present illness')" .
                                                                   "ORDER BY  seq");
                        while($res  = sqlFetchArray($layout_forms)) {
                            $group_name=substr( $res['group_name'], 1);

                            if($group_name == 'Chief Complaint'){
                                $field_id_txt = 'chief_complaint';
                            } else if($group_name == 'History of Present illness'){
                                $field_id_txt = 'hpi';
                            } else if($group_name == 'Progress Note') {
                                $field_id_txt = 'progress_note';
                            } else if($group_name == 'Assessment Note'){
                                $field_id_txt = 'assessment_note';
                            } else if($group_name == 'Plan Note'){
                                $field_id_txt = 'plan_note';
                            }  else if($group_name == 'Face to Face HH Plan'){
                                $field_id_txt = 'f2f';
                            } 
                            $r1 = sqlStatement("SELECT lb.*
                                    FROM lbf_data lb
                                    INNER JOIN layout_options l ON lb.field_id = l.field_id
                                    INNER JOIN form_encounter f
                                    WHERE lb.form_id ='".$row_form['form_id']."'  AND f.encounter=".$row_form['encounter']."
                                    AND l.group_name LIKE '%$group_name%' AND lb.field_id LIKE  '%$field_id_txt%'
                                    ORDER BY seq");
                            $frow2 = sqlFetchArray( $r1);
                            if(!empty($frow2)){
                                $formname = $row_form['form_id']."-".$group_name."--".$copied_to_enc1;
                                echo "<label><input type='checkbox' id=$fname class='chk' value='$formname'>$group_name</label>";
                                echo "<br>";
                            }
                       }   
                   }else {
                        $formname = $row_form['form_id']."-".$fname."--".$copied_to_enc1;
                        echo "<label><input type='checkbox' id=$fname class='chk' value='$formname'>$fname</label>";
                        echo "<br>";
                   }
                }
            }
           ?>
           <center><input class="round-button" type='submit' id='ok' value='OK'></center>
       </form>          
    </body>
</html>   
