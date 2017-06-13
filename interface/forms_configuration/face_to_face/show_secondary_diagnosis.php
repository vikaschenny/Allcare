<?php

require_once("../../globals.php");
require_once("$srcdir/acl.inc");
require_once("$srcdir/formdata.inc.php");
require_once("$srcdir/globals.inc.php");
require_once("$srcdir/user.inc");
require_once("$srcdir/classes/CouchDB.class.php");

//print_r($_SESSION);
//print_r($_POST);die;

?>

<?php

$patient_id=$_SESSION['pid'];
$encounter_id=$_SESSION['encounter'];
$user_id=$_SESSION['authUserID'];

$mode=$_POST['mode'];
$pri_diagnosis_id=$_POST['pri_diagnosis_id'];

/*$sqlGetConfigSecDiagnosis='';

if($pri_diagnosis_id==2)
{
    $sqlGetConfigSecDiagnosis="SELECT option_id,title 
                            FROM list_options
                            WHERE list_id='F2F_Config_Diagnosis_Pain'
                            ORDER BY option_id";
}
else if($pri_diagnosis_id==3)
{
    $sqlGetConfigSecDiagnosis="SELECT option_id,title 
                            FROM list_options
                            WHERE list_id='F2F_Config_Diagnosis_Mental'
                            ORDER BY option_id";
}  
*/


$getFeeSheetCodes="SELECT * FROM billing 
WHERE pid='$patient_id'
AND user='$user_id'
AND encounter='$encounter_id'
AND code_type IN ('ICD9','ICD10') 
AND authorized='1'
AND activity='1'"; // AND date(date)=date(now())
//echo $getFeeSheetCodes; 

$resFeeSheetCodes=sqlStatement($getFeeSheetCodes);

//echo "<br>NUM=".$numFeeSheetCodes=  sqlNumRows($resFeeSheetCodes);
//print_r($rowFeeSheetCodes);

$feesheet_codes_array=array();
while($rowFeeSheetCodes=sqlFetchArray($resFeeSheetCodes))
{    
    array_push($feesheet_codes_array,$rowFeeSheetCodes['code']);   
}

//print_r($feesheet_codes_array);

$getCodeCategory="SELECT option_id,title,notes,codes
                  FROM list_options 
                  WHERE list_id='F2F_Diagnosis_Categories'";

$resCodeCategory=sqlStatement($getCodeCategory);

$f2f_codes_array=array();
$pri_diagnosis_id_array=array();
while($rowCodeCategory=sqlFetchArray($resCodeCategory))
{
    $f2f_codes_array=explode(',',$rowCodeCategory['codes']);
    
    foreach($feesheet_codes_array as $feesheet_code)
    {
        if(in_array($feesheet_code,$f2f_codes_array))
        {
            if(!in_array($pri_diagnosis_id_array,$rowCodeCategory['option_id']))
            {
                array_push($pri_diagnosis_id_array,$rowCodeCategory['option_id']);
            }            
        }
    }    
}

//print_r($pri_diagnosis_id_array);

$pri_diagnosis_ids=implode($pri_diagnosis_id_array,",");


$note_name='';

if($pri_diagnosis_id==2)
{
    $note_name='Pain';            
}
else if($pri_diagnosis_id==3)
{
    $note_name='Mental';
}

$sqlGetConfigSecDiagnosis="SELECT option_id,title FROM list_options
                           WHERE list_id='F2F_Diagnosis_Sub_Categories'
                           AND notes='$note_name'
                           ORDER BY notes,option_id ASC";

?>
    <div class="row">
        
    <?php
                                  
        $getSecDiagnosis=sqlStatement("SELECT sec_diagnosis_id,txt_sec_diagnosis
                                        FROM tbl_form_facetoface_configuration
                                        WHERE pri_diagnosis_id='$pri_diagnosis_id'");
        $resSecDiagnosis=sqlFetchArray($getSecDiagnosis); 
        
        /*
    if($pri_diagnosis_id==1)
    {
    ?>
           <b>Secondary Diagnosis</b>
        <input type="text" id="valConfigSecDiagnosis" name="valConfigSecDiagnosis"
               value="<?php echo ($resSecDiagnosis['txt_sec_diagnosis']!='') ? $resSecDiagnosis['txt_sec_diagnosis'] : '' ; ?>" />
            
    <?php
    
    }

    else if($pri_diagnosis_id>1)
    {

        if($resSecDiagnosis['sec_diagnosis_id']=='0' && $resSecDiagnosis['txt_sec_diagnosis']!='')
        {
            echo "<script>jQuery('#valConfigSecDiagnosis').val('".$resSecDiagnosis['txt_sec_diagnosis']."');</script>";
        }
        else if($resSecDiagnosis['sec_diagnosis_id']!='0' &&  $resSecDiagnosis['txt_sec_diagnosis']=='')
        {
            echo "<script>jQuery('#valConfigSecDiagnosis').val('".$resSecDiagnosis['sec_diagnosis_id']."');</script>";
        }

        
    $resGetConfigSecDiagnosis=sqlStatement($sqlGetConfigSecDiagnosis);
    ?>
           <b>Secondary Diagnosis</b>
            
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
    </div>

    <?php
    
    if($mode==1)
    {
        $getEncounterDetails=sqlStatement("SELECT sec_diagnosis_ids,txt_sec_diagnosis
                                           FROM tbl_form_facetoface 
                                           WHERE id=".$_POST['id']." 
                                           AND pid=".$GLOBALS['pid']." 
                                           AND encounter=".$GLOBALS['encounter']."
                                           AND pri_diagnosis_ids='$pri_diagnosis_id'");
        $resEncounterDetails=sqlFetchArray($getEncounterDetails); 
        
        if($resEncounterDetails['sec_diagnosis_id']=='0' && $resEncounterDetails['txt_sec_diagnosis']!='')
        {
            echo "<script>jQuery('#valConfigSecDiagnosis').val('".$resEncounterDetails['txt_sec_diagnosis']."');</script>";
        }
        else if($resEncounterDetails['sec_diagnosis_id']!='0' &&  $resEncounterDetails['txt_sec_diagnosis']=='')
        {
            echo "<script>jQuery('#valConfigSecDiagnosis').val('".$resEncounterDetails['sec_diagnosis_id']."');</script>";
        }        
        
    }
    
    ?>


<input type='hidden' id='hdnDignosisCategory' name='hdnDignosisCategory' value='<?php echo $pri_diagnosis_ids;?>' />

      