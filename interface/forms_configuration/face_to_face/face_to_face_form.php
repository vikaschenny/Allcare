<?php


require_once("../../globals.php");
require_once("$srcdir/acl.inc");
require_once("$srcdir/formdata.inc.php");
require_once("$srcdir/globals.inc.php");
require_once("$srcdir/user.inc");
require_once("$srcdir/classes/CouchDB.class.php");

$pri_diagnosis_id=$_POST['pri_diagnosis_id'];

$resEncounterDetails=array();
$resCheckIfExists=array();

//print_r($_SESSION);
//print_r($_POST);
$patient_id=$_SESSION['pid'];
$encounter_id=$_SESSION['encounter'];

if($mode=='1' && $_POST['f2f_report']=='Y')
{ 
    $patient_id=$_POST['pid'];
    $encounter_id=$_POST['encounter'];
}

$user_id=$_SESSION['authUserID'];
$e_id=$_POST['id'];

$aclsql = "select gag.value as value,grm.aro_id, gacl_aro.value as username from gacl_aro_groups as gag join gacl_groups_aro_map
				as grm on gag.id = grm.group_id join gacl_aro 
				on grm.aro_id = gacl_aro.id where gacl_aro.value='".$_SESSION['authUser']."'";
$aclresult = sqlStatement($aclsql);
$aclrs = sqlFetchArray($aclresult);

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
$sec_diagnosis_array=array();
while($rowCodeCategory=sqlFetchArray($resCodeCategory))
{
    $f2f_codes_array=explode(';',$rowCodeCategory['codes']);
    //echo "<br>f2f_codes";
    //print_r($f2f_codes_array);
    //echo "<br>f2f_codes";
    foreach($feesheet_codes_array as $feesheet_code)
    {
        if(in_array($feesheet_code,$f2f_codes_array))
        {
            if(!in_array($rowCodeCategory['option_id'],$pri_diagnosis_id_array))
            {
                array_push($pri_diagnosis_id_array,$rowCodeCategory['option_id']);
            }
			array_push($sec_diagnosis_array,$feesheet_code);
        }
    }
}

//print_r($pri_diagnosis_id_array);

$pri_diagnosis_ids=implode($pri_diagnosis_id_array,",");
//echo "<br>ARR=".$pri_diagnosis_ids;

if($mode==1)
{
   
   $getEncounterDetails=sqlStatement("SELECT * FROM tbl_form_facetoface 
                                      WHERE id=".$_POST['id']." 
                                      AND pid=".$GLOBALS['pid']." 
                                      AND encounter=".$GLOBALS['encounter']."");
   $resEncounterDetails=sqlFetchArray($getEncounterDetails);     
}

if($mode=='1' && $_POST['f2f_report']=='Y')
{
    $getEncounterDetails=sqlStatement("SELECT * FROM tbl_form_facetoface 
                                      WHERE id=".$_POST['id']." 
                                      AND pid=".$_POST['pid']." 
                                      AND encounter=".$_POST['encounter']."");
   $resEncounterDetails=sqlFetchArray($getEncounterDetails);

}

function show_value($mode,$pri_diagnosis_ids,$encounter_diagnosis_ids,
                    $encounter_field_value,$field_name)
{    
    $return_field_value='';
    $default_field_value_str='';
    $pri_diagnosis_ids=explode(",",$pri_diagnosis_ids);
    $encounter_diagnosis_ids=explode(",",$encounter_diagnosis_ids);
    foreach($pri_diagnosis_ids as $pri_diagnosis_id)
    {
        $sqlCheckIfExists=sqlStatement("SELECT * FROM tbl_form_facetoface_configuration
                                    WHERE pri_diagnosis_id='".$pri_diagnosis_id."'");

        $resCheckIfExists=sqlFetchArray($sqlCheckIfExists); 
        
        if($mode==1 && in_array($pri_diagnosis_id,$encounter_diagnosis_ids))
        {            
            $return_field_value=$encounter_field_value;
        }
        else if($mode==1 && !in_array($pri_diagnosis_id,$encounter_diagnosis_ids))
        {
            //echo $default_field_value;
            $return_field_value=$resCheckIfExists[$field_name];
        }
        else if($mode==0 && $pri_diagnosis_id!=0)
        {
            //echo "<br>mode=".$mode."<br>field=".$field_name;
            $default_field_value_str.=$resCheckIfExists[$field_name]." , ";
            $return_field_value=$default_field_value_str;
        }
        else if($mode==0 && $pri_diagnosis_id==0)
        {
            $return_field_value='';
        }
    }
    
    return rtrim($return_field_value," , ");
}

?>


<?php 
if($mode==1 && $resEncounterDetails['finalized']=='Y')
{  
   ?>
	
    <div class="row">
        <b> Date Stamp (Updated on):</b>
        <label><?php        
        if($resEncounterDetails['updated_date']!=NULL)
        {
            echo $resEncounterDetails['updated_date'];
        }
        else
        {
            echo $resEncounterDetails['created_date'];
        }
        ?></label>
    </div>

<?php

}

?>


<div class="row">
    <b> User Name:</b>
<?php 
    $uname='';
    if($mode==1)
    { 
        $getCreatorDetails="SELECT CONCAT(fname,' ',lname) AS uname FROM users 
                         WHERE id=(SELECT created_by FROM tbl_form_facetoface
                                   WHERE id='$e_id' AND encounter='$encounter_id' AND pid='$patient_id')";
        $resCreatorDetails=sqlStatement($getCreatorDetails);  
        $rowCreatorDetails=sqlFetchArray($resCreatorDetails);
        $uname=$rowCreatorDetails['uname'];
    }
    else
    {
        $getUserDetails="SELECT CONCAT(fname,' ',lname) AS uname FROM users 
                         WHERE id='$user_id'";
        $resUserDetails=sqlStatement($getUserDetails);
        $rowUserDetails=sqlFetchArray($resUserDetails);
        $uname=$rowUserDetails['uname'];
    }
    ?>
    <label><?php echo $uname;?></label>
</div>



<div class="row">
         
        <input type="hidden" id="pri_diagnosis_cat" name="pri_diagnosis_cat"
               value="<?php echo $pri_diagnosis_ids; ?>" />
                
        <b>Patient name and Identification:</b>
        
        <?php 
            $getPatientName=sqlStatement("SELECT CONCAT(fname,' ',lname) AS pname FROM patient_data WHERE pid=".$GLOBALS['pid']."");
            $resPatientName=sqlFetchArray($getPatientName);
        ?>  
<!--        <input type="text" name="txtPatientName" 
 value="<?php echo $resPatientName['pname'];?>" readonly="readonly">
       --> 
       <label><?php echo $resPatientName['pname'];?></label>
</div>

<div class="row">
    <b> Diagnosis Code:</b>
<?php 
   
    foreach($feesheet_codes_array as $fscode)
    {
        $getCodeDesc="SELECT dx_code,formatted_dx_code,short_desc,long_desc FROM icd9_dx_code 
                      WHERE formatted_dx_code='$fscode'";
        $resCodeDesc=sqlStatement($getCodeDesc);  
        $rowCodeDesc=sqlFetchArray($resCodeDesc);        
        echo "<br>$fscode - ".$rowCodeDesc['long_desc'];               
    }
    
    ?>
    
</div>

<br>
            
    <div class="row">I certify that this patient is under my care and that I, or a nurse practitioner or physician’s
assistant working with me, had a face-to-face encounter that meets the physician face-to-face
encounter requirements with this patient on: 

<input type='text' size='10' name='txtDateOfService' id='txtDateOfService' onkeyup='datekeyup(this,mypcc)' 
       onblur='dateblur(this,mypcc)' title='yyyy-mm-dd last date of this event' readonly="readonly" 
value="<?php 
//echo show_value($mode,$pri_diagnosis_ids,$resEncounterDetails['pri_diagnosis_ids'],$resEncounterDetails['date_of_service'],$resCheckIfExists['date_of_service']);
//echo show_value($mode,$pri_diagnosis_ids,$resEncounterDetails['pri_diagnosis_ids'],$resEncounterDetails['date_of_service'],'date_of_service');

$sqlGetCurrentEncDate="SELECT DATE(`date`) AS DOS FROM form_encounter WHERE encounter='".$encounter_id."'";
$resGetCurrentEncDate=sqlStatement($sqlGetCurrentEncDate);
$current_enc_date=sqlFetchArray($resGetCurrentEncDate);
echo $current_enc_date['DOS'];

?>
" />
       
    </div>
    <!-- div added to disable all form fields on finalized -->
    <div class="container" id="frmComplete">
        <div class="row">
            <?php
            $chkHB=show_value($mode,$pri_diagnosis_ids,$resEncounterDetails['pri_diagnosis_ids'],$resEncounterDetails['is_home_bound'],'is_home_bound');
            ?>
            <b>Is Patient Home Bound or Can’t Drive</b>
            <input id='radBoundYes' name="radBound" type="radio" value="Y" <?php echo ($chkHB=='Y')?'checked':''; ?>> Y 
            <input id='radBoundNo' name="radBound" type="radio" value="N" <?php echo ($chkHB=='N')?'checked':''; ?>> N 
            
        </div>
    
        <div class="row">
            <?php
            $chkHCC=show_value($mode,$pri_diagnosis_ids,$resEncounterDetails['pri_diagnosis_ids'],$resEncounterDetails['is_hhc_needed'],'is_hhc_needed');
            ?>
            <b>Is Home Health Care Needed</b>
            <input id='radCareYes' name="radCare" type="radio" value="Y" <?php echo ($chkHCC=='Y')?'checked':''; ?>> Y 
            <input id='radCareNo' name="radCare" type="radio" value="N" <?php echo ($chkHCC=='N')?'checked':''; ?>> N 
            
        </div>
    
        <div class="row">
            <?php
            $chkPhy=show_value($mode,$pri_diagnosis_ids,$resEncounterDetails['pri_diagnosis_ids'],$resEncounterDetails['other_physician'],'other_physician');
            ?>
            <b>Does Patient have reliable other Primary Care Physician</b>  
            <input id='radPhysicianYes' name="radPhysician" type="radio" value="Y" <?php echo ($chkPhy=='Y')?'checked':''; ?>> Y 
            <input id='radPhysicianNo' name="radPhysician" type="radio" value="N" <?php echo ($chkPhy=='N')?'checked':''; ?>> N
            
        </div>
    
        <div class="row">
            <?php
            $chkHVN=show_value($mode,$pri_diagnosis_ids,$resEncounterDetails['pri_diagnosis_ids'],$resEncounterDetails['is_house_visit_needed'],'is_house_visit_needed');
            ?>
            <b>Is House Visit Needed</b>
            <input id='radVisitYes' name="radVisit" type="radio" value="Y" <?php echo ($chkHVN=='Y')?'checked':''; ?>> Y 
            <input id='radVisitNo' name="radVisit" type="radio" value="N" <?php echo ($chkHVN=='N')?'checked':''; ?>> N
            
        </div>
    
        <div id="divVisitDays">
            <div class="row">
                <b>If Yes</b> (Circle Next Visit in Days approximately) 
                <input id='radDays30' name="radDays" type="radio" value="30"> 30 
                <input id='radDays60' name="radDays" type="radio" value="60"> 60
                <input id='radDays90' name="radDays" type="radio" value="90"> 90     
                <input id='radDaysOther' name="radDaysOther" type="radio" value="other">
          Other <input type="text" id='txtOther' name="txtOther" value="" class="form-control">   
                <script type='text/javascript'>
                    radios = jQuery('input:radio[name=radDays]');
                    <?php if($resEncounterDetails['necessary_hhs']==30 || $resEncounterDetails['necessary_hhs']==60 || $resEncounterDetails['necessary_hhs']==90)
                          {?>                                           
                              //radios.filter('[value=<?php echo show_value($mode,$pri_diagnosis_ids,$resEncounterDetails['pri_diagnosis_ids'],$resEncounterDetails['necessary_hhs'],$resCheckIfExists['necessary_hhs']); ?>]').prop('checked', true);
                              radios.filter('[value=<?php echo show_value($mode,$pri_diagnosis_ids,$resEncounterDetails['pri_diagnosis_ids'],$resEncounterDetails['necessary_hhs'],'necessary_hhs'); ?>]').prop('checked', true);
                    <?php }
                        ?>

                    <?php if($resEncounterDetails['necessary_hhs']!=30 && $resEncounterDetails['necessary_hhs']!=60 && $resEncounterDetails['necessary_hhs']!=90)
                          {?>                
                              //jQuery('#txtOther').val('<?php echo show_value($mode,$pri_diagnosis_ids,$resEncounterDetails['pri_diagnosis_ids'],$resEncounterDetails['necessary_hhs'],$resCheckIfExists['necessary_hhs']);//echo ($mode==1) ? $resEncounterDetails['necessary_hhs']: ''; ?>');
                              jQuery('#txtOther').val('<?php echo show_value($mode,$pri_diagnosis_ids,$resEncounterDetails['pri_diagnosis_ids'],$resEncounterDetails['necessary_hhs'],'necessary_hhs');//echo ($mode==1) ? $resEncounterDetails['necessary_hhs']: ''; ?>');
                              jQuery('#txtOther').show();
                    <?php }
                        ?>
                </script>

                <script type='text/javascript'>
                
            jQuery('input[name=radDays]').click(function()
            {
                jQuery('#txtOther').prop("disabled", "disabled");
                jQuery('#radDaysOther').attr('checked',false);
                jQuery('#txtOther').val('');
                jQuery('#txtOther').hide();
            });
                                
            jQuery('#radDaysOther').click(function()
            {
                jQuery('#txtOther').show();
                jQuery('#txtOther').removeAttr("disabled"); 
                jQuery('input:radio[name=radDays]').attr('checked',false);
            });
            
            if(jQuery('#txtOther').val()!=='')
            {
                jQuery('#radDaysOther').attr('checked',true);
                jQuery('input:radio[name=radDays]').attr('checked',false);
            }
                  
                </script>
                        
            </div>
        </div>
        
        <script type="text/javascript">
            
            $(document).ready(function() {
            jQuery('#divVisitDays').hide();
            jQuery('#txtOther').hide();
			
            <?php
				if($mode==1 && $resEncounterDetails['finalized']=='Y' && $aclrs["value"] != "admin")
				{  
			?>
					$("#frmComplete :input").attr("disabled", true);
					$("#btnCancel").attr("disabled", false);
			<?php
				}
		   ?>
           <?php
           if($mode==1)
           {
                if($resEncounterDetails['is_house_visit_needed']=='Y')
                {
                   ?>
                   
                   jQuery('#divVisitDays').show();
           <?php
                   if($resEncounterDetails['necessary_hhs']!=30 && $resEncounterDetails['necessary_hhs']!=60 && $resEncounterDetails['necessary_hhs']!=90)
                   {?>
                       jQuery('#txtOther').show();                       
              <?php                    
                   }
               }
           }
           ?>
           
         });
                                 
               jQuery('#radVisitYes').click(function(){               
               jQuery('input:radio[name=radVisit]').removeAttr("disabled");     
               jQuery('#divVisitDays').show();
               jQuery('#txtOther').prop("disabled", "disabled");
               jQuery('#txtOther').val('');
               jQuery('#txtOther').hide();
        });
        
               jQuery('#radVisitNo').click(function(){
               jQuery('input:radio[name=radDays]').prop("checked",false);
               jQuery('input:radio[name=radDaysOther]').prop("checked",false);
               jQuery('#txtOther').val('');
               jQuery('#divVisitDays').hide();
        });
		
        function validateForm()
        {
            if($('input:radio[name=radBound]').is(':checked')) { 
               
            }else{
                alert("Select Is Patient Home Bound");
                return false;
            }
            if($('input:radio[name=radCare]').is(':checked')) { 
                
            }else{
                alert("Select Is Home Health Care Needed"); 
                return false;
            }
            if($('input:radio[name=radPhysician]').is(':checked')) {
                
            }else{
                alert("Select Primary Care Physician"); 
                return false;
            }
            if($('input:radio[name=radVisit]').is(':checked')) { 
            }else{    
                alert("Select Is House Visit Needed"); 
                return false;
            }
            if(($('input:radio[name=radVisit]').is(':checked')) && ($("input:radio[name='radVisit']:checked").val()=='Y') && ((!$('input:radio[name=radDays]').is(':checked')) && (!$('input:radio[name=radDaysOther]').is(':checked')))) { alert("Select Next Visit in Days"); return false;}
            if(($('input:radio[name=radDaysOther]').is(':checked')) && ($('#txtOther').val()=='')) {alert("Enter Next Visit in Days"); return false;}
            return true;
        }
				
        </script>
                    
    <div class="row">
        The encounter with the patient was in whole or in part for the following medical condition which is the primary reason for home health care and <b>HOW LONG:</b> (List medical condition)<br>
        <textarea id="txtMedical" name="txtMedical" class="form-control pull-left"><?php 
        //echo show_value($mode,$pri_diagnosis_ids,$resEncounterDetails['pri_diagnosis_ids'],$resEncounterDetails['medical_condition'],$resCheckIfExists['medical_condition']);  //echo ($mode==1) ? $resEncounterDetails['medical_condition']: '';
        echo show_value($mode,$pri_diagnosis_ids,$resEncounterDetails['pri_diagnosis_ids'],$resEncounterDetails['medical_condition'],'medical_condition');  //echo ($mode==1) ? $resEncounterDetails['medical_condition']: '';?></textarea>
    </div>
    
    <div class="row"><b>I certify that, based on my findings, the following services are medically necessary home health services:</b></div>
    <div class="row"><b>Nursing</b><input type="text" name="txtNursing"  value="<?php echo show_value($mode,$pri_diagnosis_ids,$resEncounterDetails['pri_diagnosis_ids'],$resEncounterDetails['nursing'],'nursing');//echo ($mode==1) ? $resEncounterDetails['nursing']: '';?>"  class="form-control" >  </div>    
    <div class="row"><b>Physical Therapy</b><input type="text" name="txtPhysical"   value="<?php echo show_value($mode,$pri_diagnosis_ids,$resEncounterDetails['pri_diagnosis_ids'],$resEncounterDetails['physical_therapy'],'physical_therapy');?>"  class="form-control" > </div>    
    <div class="row"><b>Occupational Therapy</b><input type="text" name="txtOccupational"  value="<?php echo show_value($mode,$pri_diagnosis_ids,$resEncounterDetails['pri_diagnosis_ids'],$resEncounterDetails['occupational_therapy'],'occupational_therapy');?>"  class="form-control" >   </div>    
    <div class="row"><b>Speech-language Pathology</b><input type="text" name="txtSpeech" value="<?php echo show_value($mode,$pri_diagnosis_ids,$resEncounterDetails['pri_diagnosis_ids'],$resEncounterDetails['speech'],'speech'); ?>"  class="form-control" >   </div>    

    <div class="row">
        To provide the following care/treatments: (Required only when the physician completing the face to face encounter documentation is different than the physician completing the plan of care):<br>
        <textarea name="txtTreatment" class="form-control pull-left"><?php echo show_value($mode,$pri_diagnosis_ids,$resEncounterDetails['pri_diagnosis_ids'],$resEncounterDetails['care_treatment'],'care_treatment');?></textarea>
    </div>

    <div class="row">
        My clinical findings support the need for the above services because:<br>
        <textarea name="txtFindings" class="form-control pull-left"><?php echo show_value($mode,$pri_diagnosis_ids,$resEncounterDetails['pri_diagnosis_ids'],$resEncounterDetails['support_service_reason'],'support_service_reason');?>
        </textarea>
    </div>   

    <div class="row">
        Further, I certify that my clinical findings support that this patient is homebound (i.e. absences from home require considerable and taxing effort and are for medical reasons or religious services or infrequently or of short duration when for other reasons) because<br>
        <textarea name="txtHomeBound" class="form-control pull-left"><?php echo show_value($mode,$pri_diagnosis_ids,$resEncounterDetails['pri_diagnosis_ids'],$resEncounterDetails['patient_homebound_reason'],'patient_homebound_reason');?></textarea>
    </div>
            
        <br>
    <div class="row">
        <b>Additional Notes/Comments</b><br>
        <textarea name="txtComments" class="form-control pull-left"><?php echo show_value($mode,$pri_diagnosis_ids,$resEncounterDetails['pri_diagnosis_ids'],$resEncounterDetails['comments'],'comments');?></textarea>
    </div>
        
    <br>
    <div class="row"><b>Additional Assessments</b><br>
    <textarea name="txtAssessments" class="form-control pull-left"><?php echo show_value($mode,$pri_diagnosis_ids,$resEncounterDetails['pri_diagnosis_ids'],$resEncounterDetails['assessments'],'assessments');?></textarea>
    </div>

    <br>
    <div class="row"><b>Treatment Plan</b><br>
        <textarea name="txtTreatmentPlan" class="form-control pull-left"><?php  echo show_value($mode,$pri_diagnosis_ids,$resEncounterDetails['pri_diagnosis_ids'],$resEncounterDetails['treatment_plan'],'treatment_plan');?></textarea>
    </div>
        
    <br>
    <div class="row">
        <b>Date</b>
        <input type='text' size='10' name='txtSignDate' id='txtSignDate' onkeyup='datekeyup(this,mypcc)' onblur='dateblur(this,mypcc)' title='yyyy-mm-dd last date of this event'  value="<?php echo ($mode==1) ? $resEncounterDetails[''] : date("Y-m-d");?>" />
        <img src='../../pic/show_calendar.gif' align='absbottom' width='24' height='22' id='img_calendar_txtSignDate' border='0' alt='[?]' style='cursor:pointer;' title='Click here to choose a date'>
    <script>
    Calendar.setup({inputField:'txtSignDate', ifFormat:'%Y-%m-%d', button:'img_calendar_txtSignDate'});
    </script>
    </div>
       
<br>

        

<div align="center"><?php
 $formflag =  acl_get_section_acos('formflag');
 $array= array();
 
 if($mode==1)
    {
    
        $encounterId = ($GLOBALS['encounter']? $GLOBALS['encounter'] :$_POST['encounter']);
        $formflags = array(); 
        $result = sqlStatement("SELECT * FROM form_encounter WHERE encounter = ". $encounterId);
        $form = sqlStatement("SELECT * from `tbl_allcare_formflag` WHERE form_name='Face to Face' AND form_id=".$resEncounterDetails['id']);
        $formflags=sqlFetchArray($result);   
        while ($result2 = sqlFetchArray($form)) {
            $array =  unserialize($result2['logdate']);
            if($formflags['sensitivity']!='finalized'){
                foreach ($formflag as $value) { 
                    if (acl_check('formflag', $value[1])) {
                        if($result2[xlt(xlt($value[1]))]=='N' && $result2['finalized']=='N' ){
                           ?><input type="submit" id="<?php echo xlt(xlt($value[1])); ?>" name="<?php echo xlt(xlt($value[1])); ?>" value="<?php echo xlt(xlt($value[3])); ?>" /> <?php
                        }elseif($result2['save']=='Y' && $result2['finalized']=='N'  && $value[1]=='save'){
                            ?><input type="submit" id="<?php echo xlt(xlt($value[1])); ?>" name="<?php echo xlt(xlt($value[1])); ?>" value="<?php echo xlt(xlt($value[3])); ?>" /> <?php
                        }elseif($result2['finalized']=='Y'  && $value[1]=='finalized' ){
                            ?><input type="submit" disabled value="<?php echo xlt(xlt($value[3])); ?>" /><?php
                        }
                    }

                }
            }else{
                ?><input type="submit" disabled value="<?php echo 'Finalized' ?>" /><?php
            }
        }
        ?><input type="button" name="btnCancel" value="Cancel" id="btnCancel" onclick='javascript:history.go(-1);' ><br><br>
        <label><?php 

        $count = count($array);
        $username = sqlStatement("SELECT * from `users` WHERE username='".$array[$count-1]['authuser']."'");
        while ($row = mysql_fetch_object($username)) {
            $name = $row->fname." ".$row->lname;
        }
        if ($formflags['sensitivity']=='finalized') {
            echo "<i>Sensitivity Finalized </i>" ; 
        }else{
            echo "<i>Last Revised by <u>".$name."</u> as <u>".$array[$count-1]['status']."</u></i>" ; 
        }
          ?></label><?php
    }else{
        $current_user_groups = acl_get_group_titles($_SESSION['authUser']);
        
        foreach ($formflag as $value) {
            if($value[1]=='save' && $current_user_groups[0]=='Physicians'){
                ?><input type="submit" id="<?php echo xlt(xlt($value[1])); ?>" name="<?php echo xlt(xlt('finalized')); ?>" value="<?php echo xlt(xlt($value[3])); ?>" onclick='javascript:return validateForm();'/> <?php
            }else{
               ?><input type="submit" id="<?php echo xlt(xlt($value[1])); ?>" name="<?php echo xlt(xlt($value[1])); ?>" value="<?php echo xlt(xlt($value[3])); ?>"  onclick='javascript:return validateForm();'/> <?php
            }
        }
    
?>
 <input  type="button" id="btnCancel" name="btnCancel" value="<?php echo xlt('Cancel'); ?>" onclick="javascript:top.restoreSession();history.go(-1);">
     <?php } ?>
</div>
        
</div>

