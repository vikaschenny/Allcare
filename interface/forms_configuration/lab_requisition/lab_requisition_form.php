<?php
require_once("../../globals.php");
require_once("$srcdir/acl.inc");
require_once("$srcdir/formdata.inc.php");
require_once("$srcdir/globals.inc.php");
require_once("$srcdir/user.inc");
require_once("$srcdir/classes/CouchDB.class.php");

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


$mode=$_POST['mode'];
//$note_id=$_POST['note_id'];

//else if(strpos($_SERVER["HTTP_REFERER"],'/interface/patient_file/encounter/view_form.php'))

//echo "prv= ".$_SERVER["HTTP_REFERER"];
//echo 'mode= '.$mode;

$resEncounterDetails=array();
$resCheckIfExists=array();

if($mode==1)
{
   $getEncounterDetails=sqlStatement("SELECT * FROM tbl_form_lab_requisition 
                                      WHERE id=".$_POST['id']." 
                                      AND pid=".$GLOBALS['pid']." 
                                      AND encounter=".$GLOBALS['encounter']."");
    $resEncounterDetails=  sqlFetchArray($getEncounterDetails);     
}

//print_r($resCheckIfExists);
/*
if(isset($_POST['btnSubmit']))
{
    
    $nextVisitDays=($_POST['radDays']!='')?$_POST['radDays']:$_POST['txtOther'];
        
    if(strpos($_SERVER["HTTP_REFERER"],'/interface/patient_file/encounter/load_form.php'))
    {
        // insert

        $insert_form_FacetoFace_Sql ="INSERT INTO tbl_form_lab_requisition(pid,encounter,note_id,date_of_service,is_home_bound,is_hhc_needed,other_physician,
is_house_visit_needed,medical_condition,necessary_hhs,nursing,physical_therapy,occupational_therapy,speech,care_treatment,
support_service_reason,patient_homebound_reason,nurse_practitioner_signature,nurse_practitioner_signature_date,physician_signature,
printed_name,printed_name_date,created_date)
                      VALUES(".$GLOBALS['pid'].",".$GLOBALS['encounter'].",".$_POST['sel_notes'].",'".$_POST['txtDateOfService']."',
                            '".$_POST['radBound']."','".$_POST['radCare']."','".$_POST['radPhysician']."',
                            '".$_POST['radVisit']."','".$_POST['txtMedical']."','$nextVisitDays',
                            '".$_POST['txtNursing']."','".$_POST['txtPhysical']."','".$_POST['txtOccupational']."',
                            '".$_POST['txtSpeech']."','".$_POST['txtTreatment']."','".$_POST['txtFindings']."',
                            '".$_POST['txtHomeBound']."','".$_POST['txtNurse']."',
                            '".$_POST['txtNursePractitionerSignDate']."','".$_POST['txtPhysicianSignature']."',
                            '".$_POST['txtPrintedName']."','".$_POST['txtPrintedDate']."',now()
                            )";

        //$insert_form_FacetoFace_Res=sqlStatement($insert_form_FacetoFace_Sql);
        $insert_form_FacetoFace_Res=  mysql_query($insert_form_FacetoFace_Sql);
$lastInsertedFormId=  mysql_insert_id();
        $insert_forms ="INSERT INTO forms(date,encounter,form_name,form_id,pid,user,groupname,authorized,deleted,formdir)
                                      VALUES(now(),".$GLOBALS['encounter'].",'Face to Face',$lastInsertedFormId,".$GLOBALS['pid'].",
                                             '".$_SESSION['authUser']."','Default',1,0,'face_to_face')";

        $insert_forms_Res=sqlStatement($insert_forms);

    }
    else if(strpos($_SERVER["HTTP_REFERER"],'/interface/patient_file/encounter/view_form.php'))
    {
        // update

        $update_form_FacetoFace_Sql ="UPDATE tbl_form_lab_requisition
                                      SET note_id='".$_POST['sel_notes']."',
										  date_of_service='".$_POST['txtDateOfService']."',
                                          is_home_bound='".$_POST['radBound']."',
                                          is_hhc_needed='".$_POST['radCare']."',
                                          other_physician='".$_POST['radPhysician']."',
                                          is_house_visit_needed='".$_POST['radVisit']."',
                                          medical_condition='".$_POST['txtMedical']."',
                                          necessary_hhs='$nextVisitDays',
                                          nursing='".$_POST['txtNursing']."',
                                          physical_therapy='".$_POST['txtPhysical']."',
                                          occupational_therapy='".$_POST['txtOccupational']."',
                                          speech='".$_POST['txtSpeech']."',
                                          care_treatment='".$_POST['txtTreatment']."',
                                          support_service_reason='".$_POST['txtFindings']."',
                                          patient_homebound_reason='".$_POST['txtHomeBound']."',
                                          nurse_practitioner_signature='".$_POST['txtNurse']."',
                                          nurse_practitioner_signature_date='".$_POST['txtNursePractitionerSignDate']."',
                                          physician_signature='".$_POST['txtPhysicianSignature']."',
                                          printed_name='".$_POST['txtPrintedName']."',
                                          printed_name_date='".$_POST['txtPrintedDate']."',
                                          created_date=now()                            
                                      WHERE id=".$_REQUEST['id']." 
                                        AND pid=".$GLOBALS['pid']." 
                                        AND encounter=".$GLOBALS['encounter']."";

        //$insert_form_FacetoFace_Res=sqlStatement($insert_form_FacetoFace_Sql);
        $update_form_FacetoFace_Res=  mysql_query($update_form_FacetoFace_Sql);
                
    }
    
    echo "<script type='text/javascript'>location.href='forms.php';</script>";
             
}
*/

function show_value($mode,$encounter_field_value,$default_field_value)
{    
        if($mode==1 && $note_id==$encounter_note_id)
        {
            echo $encounter_field_value;
        }
        else if($mode==1 && $note_id!=$encounter_note_id)
        {
            echo $default_field_value;
        }
        else if($mode==0 && $note_id!=0)
        {
            echo $default_field_value;            
        }
        else if($mode==0 && $note_id==0)
        {
            echo '';            
        }
}
?>
 
    <div class="row">
        <b>Patient name and Identification</b>
        
        <?php 
            $getPatientName=sqlStatement("SELECT fname,lname FROM patient_data WHERE pid=".$GLOBALS['pid']."");
            $resPatientName=  sqlFetchArray($getPatientName);
        ?>        
        <input type="text" name="txtPatientName" 
            value="<?php echo $resPatientName['fname']." ".$resPatientName['lname'];?>" readonly="readonly">
    </div>
            
    <div class="row">I certify that this patient is under my care and that I, or a nurse practitioner or physician’s
assistant working with me, had a face-to-face encounter that meets the physician face-to-face
encounter requirements with this patient on: 

<input type='text' size='10' name='txtDateOfService' id='txtDateOfService' onkeyup='datekeyup(this,mypcc)' 
       onblur='dateblur(this,mypcc)' title='yyyy-mm-dd last date of this event' readonly="readonly" 
value="<?php 
echo show_value($mode,$resEncounterDetails['date_of_service'],$resCheckIfExists['date_of_service']);
//echo ($mode==1) ? $resEncounterDetails['date_of_service']: '';?>
" />
<img src='../../pic/show_calendar.gif' align='absbottom' width='24' height='22'
id='img_calendar_DateOfService' border='0' alt='[?]' style='cursor:pointer;'
title='Click here to choose a date'>
<script>
Calendar.setup({inputField:'txtDateOfService', ifFormat:'%Y-%m-%d', button:'img_calendar_DateOfService'});
</script>
            
    </div>
    
    <div class="container">
        <div class="row">
            <b>Is Patient Home Bound or Can’t Drive</b>
            <input id='radBoundYes' name="radBound" type="radio" value="Y"> Y 
            <input id='radBoundNo' name="radBound" type="radio" value="N"> N 
            <script type='text/javascript'>
                radios = jQuery('input:radio[name=radBound]');
                //radios.filter('[value=<?php echo $resEncounterDetails['is_home_bound']; ?>]').prop('checked', true);
                radios.filter('[value=<?php echo show_value($mode,$resEncounterDetails['is_home_bound'],$resCheckIfExists['is_home_bound']); ?>]').prop('checked', true);                
                
            </script>
        </div>
    
        <div class="row">
            <b>Is Home Health Care Needed</b>
            <input id='radCareYes' name="radCare" type="radio" value="Y"> Y 
            <input id='radCareNo' name="radCare" type="radio" value="N"> N 
            <script type='text/javascript'>
                radios = jQuery('input:radio[name=radCare]');
                //radios.filter('[value=<?php echo $resEncounterDetails['is_hhc_needed']; ?>]').prop('checked', true);
                radios.filter('[value=<?php echo show_value($mode,$resEncounterDetails['is_hhc_needed'],$resCheckIfExists['is_hhc_needed']); ?>]').prop('checked', true);
                
            </script>
        </div>
    
        <div class="row">
            <b>Does Patient have reliable other Primary Care Physician</b>  
            <input id='radPhysicianYes' name="radPhysician" type="radio"  value="Y"> Y 
            <input id='radPhysicianNo' name="radPhysician" type="radio" value="N"> N
            <script type='text/javascript'>
                radios = jQuery('input:radio[name=radPhysician]');
                //radios.filter('[value=<?php echo $resEncounterDetails['other_physician']; ?>]').prop('checked', true);
                radios.filter('[value=<?php echo show_value($mode,$resEncounterDetails['other_physician'],$resCheckIfExists['other_physician']); ?>]').prop('checked', true);
                
            </script>
        </div>
    
        <div class="row">
            <b>Is House Visit Needed</b>
            <input id='radVisitYes' name="radVisit" type="radio" value="Y"> Y 
            <input id='radVisitNo' name="radVisit" type="radio" value="N"> N
            <script type='text/javascript'>
                radios = jQuery('input:radio[name=radVisit]');
                //radios.filter('[value=<?php echo $resEncounterDetails['is_house_visit_needed']; ?>]').prop('checked', true);
                radios.filter('[value=<?php echo show_value($mode,$resEncounterDetails['is_house_visit_needed'],$resCheckIfExists['is_house_visit_needed']); ?>]').prop('checked', true);
                
            </script>
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
                              //radios.filter('[value=<?php echo $resEncounterDetails['necessary_hhs']; ?>]').prop('checked', true);
                              radios.filter('[value=<?php echo show_value($mode,$resEncounterDetails['necessary_hhs'],$resCheckIfExists['necessary_hhs']); ?>]').prop('checked', true);
                     <?php }
                        ?>

                    <?php if($resEncounterDetails['necessary_hhs']!=30 && $resEncounterDetails['necessary_hhs']!=60 && $resEncounterDetails['necessary_hhs']!=90)
                          {?>                
                              jQuery('#txtOther').val('<?php echo show_value($mode,$resEncounterDetails['necessary_hhs'],$resCheckIfExists['necessary_hhs']);//echo ($mode==1) ? $resEncounterDetails['necessary_hhs']: ''; ?>');
                              jQuery('#txtOther').show();
                     <?php }
                        ?>
                </script>

                <script type='text/javascript'>
                
            jQuery('input[name=radDays]').click(function()
            {
                //jQuery('#txtOther').val('');
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
          // jQuery('input:radio[name=radDays]').attr("disabled", "disabled");   
           jQuery('#divVisitDays').hide();
           jQuery('#txtOther').hide();
           
           <?php
           if($mode==1)
           { //echo 'hhs='.$resEncounterDetails['necessary_hhs'];
               //print_r($resEncounterDetails);
               if($resEncounterDetails['is_house_visit_needed']=='Y')
               {
                   ?>
                   //jQuery('input:radio[name=rdARC]').removeAttr("disabled");   
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
               //jQuery('input:radio[name=radDays]').attr("disabled", "disabled");
               jQuery('input:radio[name=radDaysOther]').prop("checked",false);
               jQuery('#txtOther').val('');
               jQuery('#divVisitDays').hide();
        });
        </script>
        
        
    
    <div class="row">
        The encounter with the patient was in whole or in part for the following medical condition which is the 
        primary reason for home health care and <b>HOW LONG:</b> (List medical condition)<br>
        <textarea name="txtMedical" class="form-control pull-left"><?php echo show_value($mode,$resEncounterDetails['medical_condition'],$resCheckIfExists['medical_condition']);  //echo ($mode==1) ? $resEncounterDetails['medical_condition']: '';?></textarea>
    </div>
    
    <div class="row">        
        <b>I certify that, based on my findings, the following services are medically necessary home health
services:</b>
    </div>
        
<div class="row"><b>Nursing</b><input type="text" name="txtNursing" 
                                      value="<?php echo show_value($mode,$resEncounterDetails['nursing'],$resCheckIfExists['nursing']);//echo ($mode==1) ? $resEncounterDetails['nursing']: '';?>"  class="form-control" >  </div>    
<div class="row"><b>Physical Therapy</b><input type="text" name="txtPhysical"   
                                                value="<?php echo show_value($mode,$resEncounterDetails['physical_therapy'],$resCheckIfExists['physical_therapy']);//echo ($mode==1) ? $resEncounterDetails['physical_therapy']: '';?>"  class="form-control" > </div>    
<div class="row"><b>Occupational Therapy</b><input type="text" name="txtOccupational"  
                                                   value="<?php echo show_value($mode,$resEncounterDetails['occupational_therapy'],$resCheckIfExists['occupational_therapy']);//echo ($mode==1) ? $resEncounterDetails['occupational_therapy']: '';?>"  class="form-control" >   </div>    
<div class="row"><b>Speech-language Pathology</b><input type="text" name="txtSpeech" 
                                                        value="<?php echo show_value($mode,$resEncounterDetails['speech'],$resCheckIfExists['speech']);//echo ($mode==1) ? $resEncounterDetails['speech']: '';?>"  class="form-control" >   </div>    

<div class="row">
    To provide the following care/treatments: (Required only when the physician completing the face to face encounter documentation is different than the physician completing the plan of care):
    <br>
        <textarea name="txtTreatment" class="form-control pull-left"><?php echo show_value($mode,$resEncounterDetails['care_treatment'],$resCheckIfExists['care_treatment']);//echo ($mode==1) ? $resEncounterDetails['care_treatment']: '';?></textarea>
</div>    

<div class="row">
    My clinical findings support the need for the above services because:
    <br>
        <textarea name="txtFindings" class="form-control pull-left"><?php echo show_value($mode,$resEncounterDetails['support_service_reason'],$resCheckIfExists['support_service_reason']);//echo ($mode==1) ? $resEncounterDetails['support_service_reason']: '';?></textarea>
</div>   

<div class="row">
    Further, I certify that my clinical findings support that this patient is homebound (i.e. absences from home require considerable and taxing effort and are for medical reasons or religious services or infrequently or of short duration when for other reasons) because
    <br>
        <textarea name="txtHomeBound" class="form-control pull-left"><?php echo show_value($mode,$resEncounterDetails['patient_homebound_reason'],$resCheckIfExists['patient_homebound_reason']);//echo ($mode==1) ? $resEncounterDetails['patient_homebound_reason']: '';?></textarea>
</div>  

<div align="center">
    <input type="submit" name="btnSubmit" value="Submit" id="btnSubmit" >
    <input type="button" name="btnCancel" value="Cancel" id="btnCancel" onclick='javascript:history.back();' >
</div>
                </div>

