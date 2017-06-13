<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once("../globals.php");
require_once("$srcdir/acl.inc");
require_once("$srcdir/formdata.inc.php");
require_once("$srcdir/globals.inc.php");
require_once("$srcdir/user.inc");
require_once("$srcdir/classes/CouchDB.class.php");

//print_r($_POST);die;

$sqlCheckIfExists=sqlStatement("SELECT * FROM tbl_form_lab_requisition_configuration");

$resCheckIfExists=sqlFetchArray($sqlCheckIfExists);
$exists=sqlNumRows($sqlCheckIfExists);

?>

<br>


<form id='form_lab_requisition_configuration' name='form_lab_requisition_configuration' >
    
    <table border='1'>
        
<!--        <input type='hidden' id='hdnConfigNote' name='hdnConfigNote' value='<?php echo $_POST['note_id'];?>' />-->
        <input type='hidden' id='hdnExists' name='hdnExists' value='<?php echo $exists;?>' />
                
<?php

$sqlGetFormFields="SHOW COLUMNS FROM tbl_form_lab_requisition_configuration 
WHERE Field NOT IN('id','note_id','encounter','created_by','other1','other2','other3',
'diagnosis_codes','patient_has','nurse_practitioner_signature','nurse_practitioner_signature_date' ,
'physician_signature' ,'printed_name','printed_name_date','date_of_signature','created_date','updated_date',
'lab1','lab2','lab3','lab4','lab5')";

$resSqlStatement=sqlStatement($sqlGetFormFields);

while($row=mysql_fetch_array($resSqlStatement))
{     
?>
    <tr>
    <td align='right'><?php echo str_replace('_',' ',ucfirst($row['Field']));?></td>
    <td>
<?php   
    //echo 'Field='.($row['Field']).'<br>Type='.$row['Type'];
    
    //if(strpos($row['Type'],'enum'))

    if($row['Field']=='tests')
    {                
        
        echo "
<!--    <input type='checkbox' value='CXR' id='chk_CXR' name='chkTests[]' />CXR-->        
        <input type='checkbox' value='CBC' id='chk_CBC' name='chkTests[]' />CBC 
        <input type='checkbox' value='WDiff' id='chk_WDiff' name='chkTests[]' />W/Diff
        <input type='checkbox' value='UA' id='chk_UA' name='chkTests[]' />UA 
        <br>        
<!--    <input type='checkbox' value='KUB' id='chk_KUB' name='chkTests[]' />KUB -->
        <input type='checkbox' value='CMP' id='chk_CMP' name='chkTests[]' />CMP 
        <input type='checkbox' value='Urine Culture' id='chk_urine_culture' name='chkTests[]' />URINE CULTURE
        <br>
        <input type='checkbox' value='TSH' id='chk_TSH' name='chkTests[]' />TSH
        <input type='checkbox' value='Lipid Panel' id='chk_lipid_panel' name='chkTests[]' />LIPID PANEL 
        <input type='checkbox' value='PSA' id='chk_PSA' name='chkTests[]' />PSA 
        <br>
        <input type='checkbox' value='HbAC' id='chk_HbAC' name='chkTests[]' />HbA,C
        <input type='checkbox' value='Mammogram' id='chk_mammogram' name='chkTests[]' />Mammogram
        <br>
        <input type='checkbox' value='Vitamin D' id='chk_VitaminD' name='chkTests[]' />Vitamin D
        <input type='checkbox' value='PT_INR' id='chk_PT_INR' name='chkTests[]' />PT/INR
        <input type='checkbox' value='XRay' id='chk_XRay' name='chkTests[]' />X-Ray
        ";
        
        $default_tests=explode(',',$resCheckIfExists['tests']);
        foreach ($default_tests as $test_name)
        { ?>
            <script>
                //jQuery("#chk_<?php echo $test_name;?>").attr('checked',true);
                jQuery('input[value="<?php echo $test_name;?>"]').attr('checked',true);
            </script>
        <?php
        
        }
                
    }
    else if($row['Field']=='patient_has')
    {                
        ?>
            <!--
            <script type="text/javascript">
                
if(jQuery("#rd_is_colonoscopy_required_Yes").is(':checked'))
                {
                    jQuery("input[name:rdARC]").show();                    
                    
                }
                else
                {
                    jQuery("input[name:rdARC]").hide();     
                }
                
            </script>-->
            <input type="radio" id="rdAccept" name="rdARC" value="Accepted" />Accepted
        <input type="radio" id="rdRefuse" name="rdARC" value="Refused" />Refused
        <input type="radio" id="rdComplete" name="rdARC" value="Completed" />Completed
                        
    <?php 
    
    }
    
    else if (strpos($row['Type'], 'enum') !== false)
    { ?>
        <!--
         <select id="select_<?php echo $row['Field'];?>" name="select_<?php echo $row['Field'];?>">
               <option value="Y">Yes</option>
               <option value="N">No</option>
         </select>
        -->
        
    <input id="rd_<?php echo $row['Field'];?>_Yes" name="rd_<?php echo $row['Field'];?>" type="radio" value="Y"> Yes
            <input id="rd_<?php echo $row['Field'];?>_No" name="rd_<?php echo $row['Field'];?>" type="radio" value="N"> No
            <script type='text/javascript'>
                radios = jQuery("input:radio[name=rd_<?php echo $row['Field'];?>]");
                radios.filter("[value=<?php echo $resCheckIfExists[$row['Field']]; ?>]").prop('checked', true);                
            </script>        
        
<?php
    }
    else if(strpos($row['Type'], 'date') !== false)
    {       
?>
      
    <input type='text' size='10' name="date_<?php echo $row['Field'];?>" id="date_<?php echo $row['Field'];?>" 
       onkeyup='datekeyup(this,mypcc)' onblur='dateblur(this,mypcc)' 
       title='yyyy-mm-dd last date of this event' readonly="readonly" 
       value="<?php echo ($exists==1) ? $resCheckIfExists[$row['Field']] : '' ; ?>" />
<img src='../pic/show_calendar.gif' align='absbottom' width='24' height='22'
id="img_calendar_<?php echo $row['Field'];?>" border='0' alt='[?]' style='cursor:pointer;'
title='Click here to choose a date'>
<script>
Calendar.setup({inputField:"date_<?php echo $row['Field'];?>", ifFormat:'%Y-%m-%d', button:"img_calendar_<?php echo $row['Field'];?>"});
</script>
    
<?php    
    }
    else
    {
    ?>
<!--     <input type="text" id="txt_<?php echo $row['Field'];?>" name="txt_<?php echo $row['Field'];?>" maxlength="50"
            value="<?php echo ($exists==1) ? $resCheckIfExists[$row['Field']] : '' ; ?>" />
     -->
     
     <textarea id="txt_<?php echo $row['Field'];?>" name="txt_<?php echo $row['Field'];?>" cols="100"
            ><?php echo ($exists==1) ? $resCheckIfExists[$row['Field']] : '' ; ?></textarea>
     
     
<?php   
    } 
    ?>
     
          </td>                    
    </tr>  
    <?php    
    }
    ?>
<tr></tr>
<tr><td align='right'><input type='button' id='btnSaveFormConfig' name='btnSaveFormConfig' 
               value='Save' onclick='javascript:saveLabRequestConfig();' />
          </td>
    <td><input type="button" name="btnCancel" value="Cancel" id="btnCancel" onclick='javascript:history.back();'</td>
</tr>
<tr></tr>
</table>
    
</form>
