<?php

require_once("../globals.php");
require_once("$srcdir/acl.inc");
require_once("$srcdir/formdata.inc.php");
require_once("$srcdir/globals.inc.php");
require_once("$srcdir/user.inc");
require_once("$srcdir/classes/CouchDB.class.php");

//echo "<br>POST==<br>";print_r($_POST);die;

$pri_diagnosis_id=$_POST['pri_diagnosis_id'];

$sqlCheckIfExists=sqlStatement("SELECT * FROM tbl_form_facetoface_configuration
                                WHERE pri_diagnosis_id='".$_POST['pri_diagnosis_id']."'");

$resCheckIfExists=sqlFetchArray($sqlCheckIfExists);
$exists=sqlNumRows($sqlCheckIfExists);

?>

<br>

<script>
    
    function saveFacetoFaceConfig()
    {
         $.ajax({
                    type: 'POST',
                    url: 'save_facetoface_config.php',                    
                    data: $("#form_facetoface_configuration").serialize(),
                    //contentType: 'multipart/form-data',

                    success: function(response)
                    {
                        alert(response);                        
                        
                    },
                    failure: function(response)
                    {
                        alert("Failed");
                    }
                });
    }
</script>

<form id='form_facetoface_configuration' name='form_facetoface_configuration' >
    
    <table border='1'>
        
        <input type='hidden' id='hdnConfigNote' name='hdnConfigNote' value='<?php echo $_POST['note_id'];?>' />
        <input type='hidden' id='hdnExists' name='hdnExists' value='<?php echo $exists;?>' />
        
        <!--
    <tr><td align='right'>Select the Note</td>
    <td>
    <?php
    $sqlGetConfigurationNotes="SELECT option_id,title 
                               FROM list_options
                               WHERE list_id='F2F_Config_Notes_Primary'";

    $resGetConfigurationNotes=sqlStatement($sqlGetConfigurationNotes);
    ?>
        
    <select id='selectConfigNote' name='selectConfigNote' onchange='showConfigByNote(this.value)'>
    <option value='0'>--Select--</option>
    <?php
    while($rowNotes=mysql_fetch_array($resGetConfigurationNotes))
    {
        echo "<option value='".$rowNotes['option_id']."'>".$rowNotes['title']."</option>";
    }
    ?>
    </select>       
    </td>
    </tr>  -->  

        
        <tr>
            <td>
                 <?php /*
    if($pri_diagnosis_id==1)
    {
    ?>
        <b>Enter the Secondary Diagnosis</b>
        
        <input type="text" id="valConfigSecDiagnosis" name="valConfigSecDiagnosis" />
                                
    <?php
    
    }   
        
    else if($pri_diagnosis_id>1)
    {
        
    $resGetConfigSecDiagnosis=sqlStatement($sqlGetConfigSecDiagnosis);
    ?>
      
          <b>Select the Secondary Diagnosis</b>
            <select id='valConfigSecDiagnosis' name='valConfigSecDiagnosis'>
                
                <option value="0">None</option>
            <?php
            while($rowDiagnosis=mysql_fetch_array($resGetConfigSecDiagnosis))
            {
                echo "<option value='".$rowDiagnosis['option_id']."'>".$rowDiagnosis['title']."</option>";
            }
            ?>
            </select>        
      
      <?php 
    }*/
    ?>

       
            </td>
        </tr>
    
                
<?php

$sqlGetFormFields="SHOW COLUMNS FROM tbl_form_facetoface_configuration 
                   WHERE Field!='id' AND Field!='pri_diagnosis_id' AND Field!='encounter' AND Field!='created_by'";

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
    if (strpos($row['Type'], 'enum') !== false)
    { ?>
        <!--
         <select id="select_<?php echo $row['Field'];?>" name="select_<?php echo $row['Field'];?>">
               <option value="Y">Yes</option>
               <option value="N">No</option>
         </select>
        -->
        
    <input id='rd_<?php echo $row['Field'];?>_Yes' name="rd_<?php echo $row['Field'];?>" type="radio" value="Y"> Yes
            <input id='rd_<?php echo $row['Field'];?>_No' name="rd_<?php echo $row['Field'];?>" type="radio" value="N"> No
            <script type='text/javascript'>
                radios = jQuery('input:radio[name=rd_<?php echo $row['Field'];?>]');
                radios.filter('[value=<?php echo $resCheckIfExists[$row['Field']]; ?>]').prop('checked', true);
            </script>        
        
<?php
    }
    else if(strpos($row['Type'], 'date') !== false)
    {       
?>
      
    <input type='text' size='10' name='date_<?php echo $row['Field'];?>' id='date_<?php echo $row['Field'];?>' 
       onkeyup='datekeyup(this,mypcc)' onblur='dateblur(this,mypcc)' 
       title='yyyy-mm-dd last date of this event' readonly="readonly" 
       value="<?php echo ($exists==1) ? $resCheckIfExists[$row['Field']] : '' ; ?>" />
<img src='../pic/show_calendar.gif' align='absbottom' width='24' height='22'
id='img_calendar_<?php echo $row['Field'];?>' border='0' alt='[?]' style='cursor:pointer;'
title='Click here to choose a date'>
<script>
Calendar.setup({inputField:'date_<?php echo $row['Field'];?>', ifFormat:'%Y-%m-%d', button:'img_calendar_<?php echo $row['Field'];?>'});
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
               value='Save' onclick='saveFacetoFaceConfig()' />
          </td>
    <td><input type="button" name="btnCancel" value="Cancel" id="btnCancel" onclick='javascript:history.back();'</td>
</tr>
<tr></tr>
</table>
    
</form>




