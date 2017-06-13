<?php

require_once("../globals.php");
require_once("$srcdir/acl.inc");
require_once("$srcdir/formdata.inc.php");
require_once("$srcdir/globals.inc.php");
require_once("$srcdir/user.inc");
require_once("$srcdir/classes/CouchDB.class.php");

//print_r($_POST);die;
$pri_diagnosis_id=$_POST['pri_diagnosis_id'];

//$sqlGetConfigSecDiagnosis='';

$note_name='';

if($pri_diagnosis_id==2)
{
    /*$sqlGetConfigSecDiagnosis="SELECT option_id,title 
                            FROM list_options
                            WHERE list_id='F2F_Config_Diagnosis_Pain'
                            ORDER BY option_id";*/
    $note_name='Pain';            
}
else if($pri_diagnosis_id==3)
{    
    /*$sqlGetConfigSecDiagnosis="SELECT option_id,title 
                            FROM list_options
                            WHERE list_id='F2F_Config_Diagnosis_Mental'
                            ORDER BY option_id";*/
    $note_name='Mental';
}

/*$sqlGetConfigSecDiagnosis="SELECT option_id,title,codes FROM list_options
                           WHERE list_id='F2F_Diagnosis_Sub_Categories'
                           AND notes='$note_name'
                           ORDER BY notes,option_id ASC";*/
$sqlGetConfigSecDiagnosis=sqlStatement("SELECT codes FROM list_options WHERE list_id='F2F_Diagnosis_Categories' AND title='$note_name'");
$resGetConfigSecDiagnosis=sqlFetchArray($sqlGetConfigSecDiagnosis);

$secdiagcodes = str_replace(';',',',$resGetConfigSecDiagnosis['codes']);
$arysecdiagcodes = explode(",",$secdiagcodes);
$sqlGetCodeLongDesc = sqlStatement("SELECT long_desc from icd9_dx_code WHERE formatted_dx_code IN ($secdiagcodes)");


$sqlCheckIfExists=sqlStatement("SELECT * FROM tbl_form_facetoface_configuration
                                WHERE pri_diagnosis_id='".$_POST['pri_diagnosis_id']."'");
//echo "<script>alert('PDI=='+".$_POST['pri_diagnosis_id'].");</script>";
$resCheckIfExists=sqlFetchArray($sqlCheckIfExists);
$exists=sqlNumRows($sqlCheckIfExists);
//echo "<script>alert('exists=='+".$exists.");</script>";
?>

<br>

<form id='form_facetoface_configuration' name='form_facetoface_configuration' >
    
    <table border='1'>
        
        <input type='hidden' id='hdnConfigDiagnosis' name='hdnConfigDiagnosis' value='<?php echo $_POST['pri_diagnosis_id'];?>' />
        <input type='hidden' id='hdnExists' name='hdnExists' value='<?php echo $exists;?>' />
             <script>//alert('existsPP=='+jQuery('#hdnExists').val());</script>  
        <tr>
            <td align='right'>
        Diagnosis Sub-Categories
            </td>
            
                 <?php 
    if($pri_diagnosis_id==1)
    {
    ?>
            
            <td>
        <input type="text" id="valConfigSecDiagnosis" name="valConfigSecDiagnosis"
               value="<?php echo ($exists==1) ? $resCheckIfExists['txt_sec_diagnosis'] : '' ; ?>" />
            </td>
    <?php
    
    }
        
    else if($pri_diagnosis_id>1)
    {
        
        $getSecDiagnosis=sqlStatement("SELECT sec_diagnosis_id
                                           FROM tbl_form_facetoface_configuration
                                           WHERE pri_diagnosis_id='$pri_diagnosis_id'");
        $resSecDiagnosis=sqlFetchArray($getSecDiagnosis); 
        
        if($resSecDiagnosis['sec_diagnosis_id']=='0' && $resSecDiagnosis['txt_sec_diagnosis']!='')
        {
            echo "<script>jQuery('#valConfigSecDiagnosis').val('".$resSecDiagnosis['txt_sec_diagnosis']."');</script>";
        }
        else if($resSecDiagnosis['sec_diagnosis_id']!='0' &&  $resSecDiagnosis['txt_sec_diagnosis']=='')
        {
            echo "<script>jQuery('#valConfigSecDiagnosis').val('".$resSecDiagnosis['sec_diagnosis_id']."');</script>";
        }
                                           
    //$resGetConfigSecDiagnosis=sqlStatement($sqlGetConfigSecDiagnosis);
    ?>
            
            <td>
               
            <?php
            
            $subcategories='';
            /*while($rowDiagnosis=sqlFetchArray($resGetConfigSecDiagnosis))
            {
                
                $subcategories.=$rowDiagnosis['title']."-".$rowDiagnosis['codes'].", ";
            }*/
			$i=0;
			while($resGetCodeLongDesc = sqlFetchArray($sqlGetCodeLongDesc))
			{
				$subcategories.=$arysecdiagcodes[$i]."-".$resGetCodeLongDesc['long_desc']."; ";
				$i++;
			}
            echo rtrim($subcategories,'; ');
            ?>
              
              
            </td>  
      <?php 
    }
    ?>

        </tr>
        
<?php

/*
$sqlGetFormFields="SHOW COLUMNS FROM tbl_form_facetoface_configuration 
WHERE Field NOT IN ('id','pri_diagnosis_id','sec_diagnosis_id','txt_sec_diagnosis','encounter','created_by',				
'nurse_practitioner_signature','nurse_practitioner_signature_date','physician_signature','printed_name',
'printed_name_date','created_date','updated_date')";
*/

$sqlGetFormFields="SHOW COLUMNS FROM tbl_form_facetoface_configuration 
WHERE Field IN ('support_service_reason','patient_homebound_reason')";


$resSqlStatement=sqlStatement($sqlGetFormFields);

while($row=mysql_fetch_array($resSqlStatement))
{
?>
    <tr>
    <td align='right'><?php echo str_replace('_',' ',ucfirst($row['Field']));?></td>
    <td>
<?php   
    
    if (strpos($row['Type'], 'enum') !== false)
    { ?>
        
        
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
               value='Save' onclick='javascript:saveFacetoFaceConfig();' />
          </td>
    <td><input type="button" name="btnCancel" value="Cancel" id="btnCancel" onclick='javascript:history.back();'</td>
</tr>
<tr></tr>
</table>
    
</form>

