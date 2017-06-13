<?php
require_once("../../globals.php");
require_once("$srcdir/acl.inc");
require_once("$srcdir/formdata.inc.php");
require_once("$srcdir/globals.inc.php");
require_once("$srcdir/user.inc");
require_once("$srcdir/classes/CouchDB.class.php");

$mode=0;
//print_r($GLOBALS);

if(strpos($_SERVER["HTTP_REFERER"],'/interface/patient_file/encounter/load_form.php'))
{
    $mode=0;
}
//else if(strpos($_SERVER["HTTP_REFERER"],'/interface/patient_file/encounter/view_form.php'))
else if(strpos($_SERVER["HTTP_REFERER"],'/interface/patient_file/encounter/forms.php') && $_REQUEST['id']!='')
{
    $mode=1;
        
    $getEncounterDetails=sqlStatement("SELECT * FROM tbl_form_lab_requisition
                                       WHERE id=".$_REQUEST['id']." 
                                         AND pid=".$GLOBALS['pid']." 
                                         AND encounter=".$GLOBALS['encounter']."");
    $resEncounterDetails=sqlFetchArray($getEncounterDetails);
    
    $getLabRequisitionTests=sqlStatement("SELECT * FROM tbl_form_lab_requisition_tests
                                       WHERE form_id=".$_REQUEST['id']." 
                                         AND pid=".$GLOBALS['pid']." 
                                         AND encounter=".$GLOBALS['encounter']."");
    $resLabRequisitionTests=sqlFetchArray($getLabRequisitionTests);
    
    
}
//print_r($GLOBALS);
//echo "prv= ".$_SERVER["HTTP_REFERER"];
//echo 'mode= '.$mode;
//echo "U=".$user_name=$GLOBALS['pc_username']."<br>";
if(isset($_POST['btnSubmit']))
{
    //$nextVisitDays=($_POST['radDays']!='')?$_POST['radDays']:$_POST['txtOther'];
        
    if(strpos($_SERVER["HTTP_REFERER"],'/interface/patient_file/encounter/load_form.php'))
    {
        // insert
        //echo "inserting";
        
//echo '<br>G='.$GLOBALS['encounter'].",".$GLOBALS['pid'].",".$user_name."<br>";
$insert_form_LabRequisition_Sql ="INSERT INTO tbl_form_lab_requisition(pid,encounter,
created_by,date_of_request,specimen_week,fasting,frail_health,is_home_bound,is_preference_home_health,
diagnosis_codes,nurse_practitioner_signature,nurse_practitioner_signature_date,physician_signature,printed_name,date_of_signature,created_date)
VALUES(".$GLOBALS['pid'].",".$GLOBALS['encounter'].",
'1','".$_POST['txtDateOfRequest']."','".$_POST['txtSpecimenWeek']."',
'".$_POST['rdFasting']."','".$_POST['rdFrailHealth']."',
'".$_POST['rdHomeBound']."','".$_POST['rdPHH']."',
'".$_POST['txtDiagnosisCodes']."',
'".$_POST['txtNursePractitionerSignature']."',
'".$_POST['txtNursePractitionerSignDate']."',
'".$_POST['txtPhysicianSignature']."','".$_POST['txtPrintedName']."',
'".$_POST['txtDateOfSignature']."',now()
)";
    
mysql_query($insert_form_LabRequisition_Sql);
        
        $lastInsertedFormId=mysql_insert_id();
                
$insert_form_LabRequisition_tests ="INSERT INTO tbl_form_lab_requisition_tests(pid,encounter,form_id,CXR,CBC,UA,KUB,CMP,urine_culture,
                                    TSH,lipid_panel,PSA,HbAC,mammogram,
                                    is_colonoscopy_required,did_patient_ARC)
                                    VALUES(".$GLOBALS['pid'].",".$GLOBALS['encounter'].",$lastInsertedFormId,
                                    '".$_POST['chk_CXR']."','".$_POST['chk_CBC']."','".$_POST['chk_UA']."',
                                    '".$_POST['chk_KUB']."','".$_POST['chk_CMP']."','".$_POST['chk_urine_culture']."',
                                    '".$_POST['chk_TSH']."','".$_POST['chk_lipid_panel']."','".$_POST['chk_PSA']."',
                                    '".$_POST['chk_HbAC']."','".$_POST['chk_mammogram']."',
                                    '".$_POST['rdColonoscopyReq']."','".$_POST['rdARC']."')";        
        
        mysql_query($insert_form_LabRequisition_tests);
        //$insert_form_LabRequisition_Res=  mysql_query($insert_form_LabRequisition_Sql);
        //$lastInsertedFormId=  mysql_insert_id();
        
        $insert_forms ="INSERT INTO forms(date,encounter,form_name,form_id,pid,user,groupname,authorized,deleted,formdir)
                        VALUES(now(),".$GLOBALS['encounter'].",'Lab Requisition',$lastInsertedFormId,".$GLOBALS['pid'].",
                               '".$GLOBALS['pc_username']."','Default',1,0,'lab_requisition')";
        
        mysql_query($insert_forms);
        
    }
    
    else if(strpos($_SERVER["HTTP_REFERER"],'/interface/patient_file/encounter/view_form.php'))
    {
        //echo "updating here";
        // update
$update_form_LabRequisition_Sql ="UPDATE tbl_form_lab_requisition
SET date_of_request='".$_POST['txtDateOfRequest']."',
    specimen_week='".$_POST['txtSpecimenWeek']."',
    fasting='".$_POST['rdFasting']."',
    frail_health='".$_POST['rdFrailHealth']."',
    is_home_bound='".$_POST['rdHomeBound']."',
    is_preference_home_health='".$_POST['rdPHH']."',
    diagnosis_codes='".$_POST['txtDiagnosisCodes']."',     
    nurse_practitioner_signature='".$_POST['txtNursePractitionerSignature']."',
    nurse_practitioner_signature_date='".$_POST['txtNursePractitionerSignDate']."',
    physician_signature='".$_POST['txtPhysicianSignature']."',
    printed_name='".$_POST['txtPrintedName']."',
    date_of_signature='".$_POST['txtDateOfSignature']."',
    updated_date=now()                                                         
WHERE id=".$_REQUEST['id']." 
AND pid=".$GLOBALS['pid']." 
AND encounter=".$GLOBALS['encounter']."";
//die;
        //$insert_form_LabRequisition_Res=sqlStatement($insert_form_LabRequisition_Sql);
sqlStatement($update_form_LabRequisition_Sql);         
        
$update_form_LabRequisition_tests_Sql ="UPDATE tbl_form_lab_requisition_tests
SET CXR='".$_POST['chk_CXR']."',CBC='".$_POST['chk_CBC']."',
     UA='".$_POST['chk_UA']."',KUB='".$_POST['chk_KUB']."',
     CMP='".$_POST['chk_CMP']."',urine_culture='".$_POST['chk_urine_culture']."',
     TSH='".$_POST['chk_TSH']."',lipid_panel='".$_POST['chk_lipid_panel']."',
     PSA='".$_POST['chk_PSA']."',HbAC='".$_POST['chk_HbAC']."',
     mammogram='".$_POST['chk_mammogram']."',     
     is_colonoscopy_required='".$_POST['rdColonoscopyReq']."',
     did_patient_ARC='".$_POST['rdARC']."'
WHERE form_id=".$_REQUEST['id']." 
AND pid=".$GLOBALS['pid']." 
AND encounter=".$GLOBALS['encounter']."";
              
sqlStatement($update_form_LabRequisition_tests_Sql);         

    }
    
    echo "<script type='text/javascript'>location.href='forms.php';</script>";
             
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8"> 
        <title>Lab Requisition</title>
                
<link rel=stylesheet href="../themes/style_oemr.css" type="text/css">
        
<link rel="stylesheet" href="../../main/css/bootstrap-3.0.3.min.css" type="text/css">
<script type="text/javascript" src="../../main/js/jquery-2.0.3.min.js"></script>
<script type="text/javascript" src="../../main/js/bootstrap-3.0.3.min.js"></script>

<style type="text/css">@import url(../../../library/dynarch_calendar.css);</style>
<script type="text/javascript" src="../../../library/dialog.js"></script>
<script type="text/javascript" src="../../../library/textformat.js"></script>
<script type="text/javascript" src="../../../library/dynarch_calendar.js"></script>
<script type="text/javascript" src="../../../library/dynarch_calendar_en.js"></script>
<script type="text/javascript" src="../../../library/dynarch_calendar_setup.js"></script>

 
    <script type="text/javascript">
    function searchCode(description)
    {        
        $.ajax({
                    type: 'POST',
                    url: 'http://<?php echo $_SERVER['HTTP_HOST']; ?>/<?php echo $GLOBALS['webroot'];?>/interface/forms/lab_requisition/search_icd_code.php',
                    data: {description:description},

                    success: function(response)
                    {
                         jQuery('#divSearchedCodes').show();                         
                         jQuery('#divSearchedCodes').html(response);                     
                    },
                    failure: function()
                    {
                            alert("error");
                    }
                });	

    }
    </script>
     <script type='text/javascript'>
    var radios='';
    </script>

    <style>
        
        b,input,textarea{margin:10px;}
        
    </style>
    
</head>
<body>
<form name="frmActionPlan" id="frmActionPlan" method="POST" role="form">
    
<div class="container">
  <div class="form-group">
    <div class="row" style="text-align: center;">
        <h4>Lab Requisition</h4>
    </div>
      
    <div class="row">
        <b>Patient name :</b>
        
        <?php 
            $getPatientInfo=sqlStatement("SELECT fname,lname,DOB,street,postal_code,city,state,country_code,ss,phone_contact
                                         FROM patient_data WHERE pid=".$GLOBALS['pid']."");
            $resPatientInfo=  sqlFetchArray($getPatientInfo);
        ?>        
        <input type="text" id="txtPatientName" name="txtPatientName" 
            value="<?php echo $resPatientInfo['fname']." ".$resPatientInfo['lname'];?>" readonly>

<!--
<input type='text' size='10' name='txtDateOfService' id='txtDateOfService' onkeyup='datekeyup(this,mypcc)' 
       onblur='dateblur(this,mypcc)' title='yyyy-mm-dd last date of this event' readonly="readonly" 
       value="<?php //echo ($mode==1) ? $resEncounterDetails['date_of_service']: '';?>" />
<img src='../../pic/show_calendar.gif' align='absbottom' width='24' height='22'
id='img_calendar_DateOfService' border='0' alt='[?]' style='cursor:pointer;'
title='Click here to choose a date'>
<script>
Calendar.setup({inputField:'txtDateOfService', ifFormat:'%Y-%m-%d', button:'img_calendar_DateOfService'});
</script>
-->
            
        <b>Date of Birth:</b>
        <input type="text" id="txtDateOfBirth" name="txtDateOfBirth" 
               value="<?php echo $resPatientInfo['DOB'];?>" readonly>
    </div> 
          
    <div class="row" style="border:1px SOLID #000">
        <div id="divPatientInformation_ByOffice">
            <h4>Patient Information(updated by Office)</h4>
            <div id="float:left;">
            <b>SSN</b> 
            <input type="text" id="txtSSN" name="txtSSN" maxlength="20" readonly
                   value="<?php echo $resPatientInfo['ss'];?>" >
  
            <b>Name</b> 
            <input type="text" id="txtName_Office" name="txtName_Office" maxlength="50"
                   value="<?php echo $resPatientInfo['fname']." ".$resPatientInfo['lname'];?>" readonly>
            </div>
            <div class="" style="border-top:1px SOLID #000;width:50%;float:left">
                <b>Address</b>
                <textarea id="txtAddress" name="txtAddress" maxlength="100" readonly
                   value="<?php echo $resPatientInfo['street'].' '.$resPatientInfo['postal_code'].' '.$resPatientInfo['city'].' '.$resPatientInfo['state'].' '.$resPatientInfo['country_code'];?>" ></textarea>
                <br>
                <b>Telephone</b> 
                <input type="text" id="txtTel" name="txtTel" maxlength="10"
                   value="<?php echo ($mode==1) ? $resEncounterDetails['phone_contact']: '';?>" readonly >              
            </div>
            
            <?php 
            $getInsuranceData=sqlStatement("SELECT tid.plan_name,tid.policy_number,tid.group_number,tic.name as insurance_company_name
                                         FROM insurance_data tid 
                                         INNER JOIN insurance_companies tic  
                                         ON tid.provider=tic.id
                                         WHERE tid.pid=".$GLOBALS['pid']." AND tid.type='primary'");
            $resInsuranceData= sqlFetchArray($getInsuranceData);
            ?>   
            
            <div class="" style="border-top:1px SOLID #000;border-left:1px SOLID #000;width:50%;float:right">
                <b>Insurance Company Name:</b>                 
                <input type="text" id="txtInsCompName" name="txtInsCompName" maxlength="50"
                   value="<?php echo $resInsuranceData['insurance_company_name']; ?>" readonly>              
                <br>
                <b>Group Name:</b>
                <input type="text" id="txtGroupName" name="txtGroupName" maxlength="20"
                   value="<?php echo $resInsuranceData['plan_name']; ?>" readonly>             
                <br>
                <b>Group #:</b> 
                <input type="text" id="txtGroupNo" name="txtGroupNo" maxlength="20"
                   value="<?php echo $resInsuranceData['group_number']; ?>" readonly>              
                <br>                
                <b>Policy ID:</b>
                <input type="text" id="txtPolicyId" name="txtPolicyId" maxlength="20"
                   value="<?php echo $resInsuranceData['policy_number'];?>" readonly>              

            </div>
        </div>
    </div>
    <br>
    <div class="row">
        <div id="divTestDetails" style="float:left;width:100%;border:1px SOLID #000;">       
            <b>Date of Request : </b>
            <input type='text' size='10' name='txtDateOfRequest' id='txtDateOfRequest' onkeyup='datekeyup(this,mypcc)' 
       onblur='dateblur(this,mypcc)' title='yyyy-mm-dd last date of this event' readonly="readonly" 
       value="<?php //echo ($mode==1) ? $resEncounterDetails['date_of_service']: '';?>" />
<img src='../../pic/show_calendar.gif' align='absbottom' width='24' height='22'
id='img_calendar_DateOfRequest' border='0' alt='[?]' style='cursor:pointer;'
title='Click here to choose a date'>
<script>
Calendar.setup({inputField:'txtDateOfRequest', ifFormat:'%Y-%m-%d', button:'img_calendar_DateOfRequest'});
</script>
<br>
    <b>Specimen to be collected the week of :</b>
    <input type="text" id="txtSpecimenWeek" name="txtSpecimenWeek" maxlength="20" 
    value="<?php echo ($mode==1) ? $resEncounterDetails['specimen_week']: '';?>" >
  
    <br>
    <b>Fasting : </b>
    <input type="radio" id="rdFastingYes" name="rdFasting" value="Y" />YES
    <input type="radio" id="rdFastingNo" name="rdFasting" value="N" />NO    
    <script type='text/javascript'>
                radios = jQuery('input:radio[name=rdFasting]');
                radios.filter('[value=<?php echo $resEncounterDetails['fasting']; ?>]').attr('checked', true);
    </script>
    <br><br>
    <b>Is Patient of Frail Health (bedridden, Wheelchair bound) :</b>
    <input type="radio" id="rdFrailHealthYes" name="rdFrailHealth" value="Y" />YES
    <input type="radio" id="rdFrailHealthNo" name="rdFrailHealth" value="N" />NO
    <script type='text/javascript'>
                radios = jQuery('input:radio[name=rdFrailHealth]');
                radios.filter('[value=<?php echo $resEncounterDetails['frail_health']; ?>]').attr('checked', true);
    </script>
    <br><br>
    <b>Is Patient Unable to drive or leaving home is a major effort :</b>
    <input type="radio" id="rdHomeBoundYes" name="rdHomeBound" value="Y" />YES
    <input type="radio" id="rdHomeBoundNo" name="rdHomeBound" value="N" />NO
    <script type='text/javascript'>
                radios = jQuery('input:radio[name=rdHomeBound]');
                radios.filter('[value=<?php echo $resEncounterDetails['is_home_bound']; ?>]').attr('checked', true);
    </script>
    <br><br>
    <b>Is there a Preference to Order through Home Health :</b>
    <input type="radio" id="rdPHHYes" name="rdPHH" value="Y" />YES
    <input type="radio" id="rdPHHNo" name="rdPHH" value="N" />NO
    <script type='text/javascript'>
                radios = jQuery('input:radio[name=rdPHH]');
                radios.filter('[value=<?php echo $resEncounterDetails['is_preference_home_health']; ?>]').attr('checked', true);
    </script>
    <br>
    (Note: UA and URINE CULTURE are ordered through Home Health Only)
    <br><br>
        DIAGNOSIS CODES (Reason for Ordering):<br>
        <input type="text" style=""
               id="txtSearchCode" name="txtSearchCode" onkeydown="javascript:searchCode(this.value);"
    value="" />
        
        <div id='divSearchedCodes' style='background:pink;height:auto;width:auto;display:none;'>
         
        </div>

    <br>
    <input type="text" id="txtDiagnosisCodes" name="txtDiagnosisCodes" 
    value="" />
  
<!--<textarea id="txtDiagnosisCodes" name="txtDiagnosisCodes" cols="50"></textarea>
-->
     
    <br><br>
    <div id='divTestRequired'style="width:50%">
    <b>TEST(s) Requested </b>
    
        <br>    
        <input type='checkbox' value='Y' id='chk_CXR' name='chk_CXR' />CXR
        <input type='checkbox' value='Y' id='chk_CBC' name='chk_CBC' />CBC 
        <input type='checkbox' value='Y' id='chk_UA' name='chk_UA' />UA 
        <br>
        <input type='checkbox' value='Y' id='chk_KUB' name='chk_KUB' />KUB 
        <input type='checkbox' value='Y' id='chk_CMP' name='chk_CMP' />CMP 
        <input type='checkbox' value='Y' id='chk_urine_culture' name='chk_urine_culture' />URINE CULTURE
        <br>
        <input type='checkbox' value='Y' id='chk_TSH' name='chk_TSH' />TSH
        <input type='checkbox' value='Y' id='chk_lipid_panel' name='chk_lipid_panel' />LIPID PANEL 
        <input type='checkbox' value='Y' id='chk_PSA' name='chk_PSA' />PSA 
        <br>
        <input type='checkbox' value='Y' id='chk_HbAC' name='chk_HbAC' />HbA,C 
        <input type='checkbox' value='Y' id='chk_mammogram' name='chk_mammogram' />MAMMOGRAM     
        
        <?php 
        if($mode==1)
        {
        ?>        
         <script>
                jQuery("#chk_CXR").attr('checked',<?php echo ($resLabRequisitionTests['CXR']=='Y')?'true':'false'; ?>);
                jQuery("#chk_CBC").attr('checked',<?php echo ($resLabRequisitionTests['CBC']=='Y')?'true':'false'; ?>);
                jQuery("#chk_UA").attr('checked',<?php echo ($resLabRequisitionTests['UA']=='Y')?'true':'false'; ?>);
                
                jQuery("#chk_KUB").attr('checked',<?php echo ($resLabRequisitionTests['KUB']=='Y')?'true':'false'; ?>);
                jQuery("#chk_CMP").attr('checked',<?php echo ($resLabRequisitionTests['CMP']=='Y')?'true':'false'; ?>);
                jQuery("#chk_urine_culture").attr('checked',<?php echo ($resLabRequisitionTests['urine_culture']=='Y')?'true':'false'; ?>);

                jQuery("#chk_TSH").attr('checked',<?php echo ($resLabRequisitionTests['TSH']=='Y')?'true':'false'; ?>);
                jQuery("#chk_lipid_panel").attr('checked',<?php echo ($resLabRequisitionTests['lipid_panel']=='Y')?'true':'false'; ?>);
                jQuery("#chk_PSA").attr('checked',<?php echo ($resLabRequisitionTests['PSA']=='Y')?'true':'false'; ?>);

                jQuery("#chk_HbAC").attr('checked',<?php echo ($resLabRequisitionTests['HbAC']=='Y')?'true':'false'; ?>);
                jQuery("#chk_mammogram").attr('checked',<?php echo ($resLabRequisitionTests['mammogram']=='Y')?'true':'false'; ?>);
           </script>
          
    <?php 
        }
    ?>   
        
    </div>
      
    <br>
 
    <b>Is Colonoscopy Required:</b>
    <input type="radio" id="rdColonoscopyReqYes" name="rdColonoscopyReq" value="Y" />YES
    <input type="radio" id="rdColonoscopyReqNo" name="rdColonoscopyReq" value="N" />NO
    <script type='text/javascript'>
                radios = jQuery('input:radio[name=rdColonoscopyReq]');
                radios.filter('[value=<?php echo $resEncounterDetails['is_colonoscopy_required']; ?>]').attr('checked', true);
            </script>
    <br>
       
    <b>Did Patient:</b>
    <input type="radio" id="rdAccept" name="rdARC" value="A" />Accept
    <input type="radio" id="rdRefuse" name="rdARC" value="R" />Refuse
    <input type="radio" id="rdComplete" name="rdARC" value="C" />Complete
    <script type='text/javascript'>
                radios = jQuery('input:radio[name=rdARC]');
                radios.filter('[value=<?php echo $resEncounterDetails['did_patient_ARC']; ?>]').attr('checked', true);
            </script>
    </div>
        </div>
          
    
    <br>
    <div class="row">  
      <b>Nurse Practitioner Signature</b> 
      <input type="text" id="txtNursePractitionerSignature" name="txtNursePractitionerSignature" maxlength="20"
             value="<?php echo ($mode==1) ? $resEncounterDetails['nurse_practitioner_signature']: '';?>" >
     
      <b>Date</b> 
<!--      <input type="text" name="txtNursePractitionerSignDate" value="" size="30" class="form-control" value="">-->
          
      <input type='text' size='10' name='txtNursePractitionerSignDate' id='txtNursePractitionerSignDate' onkeyup='datekeyup(this,mypcc)' onblur='dateblur(this,mypcc)' title='yyyy-mm-dd last date of this event' readonly="readonly"
             value="<?php echo ($mode==1) ? $resEncounterDetails['nurse_practitioner_signature_date']: '';?>" />
            <img src='../../pic/show_calendar.gif' align='absbottom' width='24' height='22'
            id='img_calendar_txtNursePractitionerSignDate' border='0' alt='[?]' style='cursor:pointer;'
            title='Click here to choose a date'>
            <script>
            Calendar.setup({inputField:'txtNursePractitionerSignDate', ifFormat:'%Y-%m-%d', button:'img_calendar_txtNursePractitionerSignDate'});
            </script>        
    </div>

    <div class="row">
        <b>Physician’s Signature</b> 
        <input type="text" name="txtPhysicianSignature" maxlength="20"
        value="<?php echo ($mode==1) ? $resEncounterDetails['physician_signature']: '';?>" >
    </div>

    <div class="row">  
    <b>Printed Name</b> 
    <input type="text" name="txtPrintedName" maxlength="50"
    value="<?php echo ($mode==1) ? $resEncounterDetails['printed_name']: '';?>" >

    <b>Date of Signature</b> 
<!--      <input type="text" name="txtPrintedDate" value="" size="30" class="form-control" >-->
    
    <input type='text' size='10' name='txtDateOfSignature' id='txtDateOfSignature' onkeyup='datekeyup(this,mypcc)' 
           value="<?php echo ($mode==1) ? $resEncounterDetails['date_of_signature']: '';?>" onblur='dateblur(this,mypcc)' title='yyyy-mm-dd last date of this event' readonly="readonly" />
<img src='../../pic/show_calendar.gif' align='absbottom' width='24' height='22'
id='img_calendar_txtDateOfSignature' border='0' alt='[?]' style='cursor:pointer;'
title='Click here to choose a date'>
<script>
Calendar.setup({inputField:'txtDateOfSignature', ifFormat:'%Y-%m-%d', button:'img_calendar_txtDateOfSignature'});
</script>
      
      
  </div>
    <br>
    <?php //print_r($GLOBALS);?>
    <b>user now==<?php echo $GLOBALS['authUser']; ?></b>
    <input type="hidden" name="loginUserName" id="loginUserName" 
           value="<?php echo $GLOBALS['authUser']; ?>" />
    <input type="hidden" name="loginUserId" id="loginUserId" 
           value="<?php echo $GLOBALS['authUserID']; ?>" />
    
    <div align="center">
        <input type="submit" name="btnSubmit" value="Submit" id="btnSubmit" >
        <input type="button" name="btnCancel" value="Cancel" id="btnCancel" onclick='javascript:history.back();' >
    </div>
   </div>

    
 </div>
    
    
</form>
</body>
</html>
