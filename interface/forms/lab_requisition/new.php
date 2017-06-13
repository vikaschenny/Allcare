<?php
require_once("../../globals.php");
require_once("$srcdir/acl.inc");
require_once("$srcdir/formdata.inc.php");
require_once("$srcdir/globals.inc.php");
require_once("$srcdir/user.inc");
require_once("$srcdir/classes/CouchDB.class.php");

$mode=0;
//print_r($GLOBALS);die;
$resEncounterDetails=array();
$resCheckIfExists=array();
//echo $_SERVER["HTTP_REFERER"];
if((strpos($_SERVER["HTTP_REFERER"],'/interface/patient_file/encounter/forms.php') || strpos($_SERVER["HTTP_REFERER"],'/interface/patient_file/encounter/load_form.php')) && $_REQUEST['id']=='')
{
    $mode=0;
    $sqlCheckIfExists=sqlStatement("SELECT * FROM tbl_form_lab_requisition_configuration");

    $resCheckIfExists=sqlFetchArray($sqlCheckIfExists);
    $exists=sqlNumRows($sqlCheckIfExists);
}
//else if(strpos($_SERVER["HTTP_REFERER"],'/interface/patient_file/encounter/view_form.php'))
else if(strpos($_SERVER["HTTP_REFERER"],'/interface/patient_file/encounter/forms.php') && $_REQUEST['id']!='')
{
    $mode=1;
        
    $getEncounterDetails=sqlStatement("SELECT * FROM tbl_form_lab_requisition
                                       WHERE id=".$_REQUEST['id']." 
                                       AND pid=".$GLOBALS['pid']." 
                                       AND encounter=".$_SESSION['encounter']."");
    $resEncounterDetails=sqlFetchArray($getEncounterDetails);
    /*
    $getLabRequisitionTests=sqlStatement("SELECT * FROM tbl_form_lab_requisition_tests
                                          WHERE form_id=".$_REQUEST['id']." 
                                          AND pid=".$GLOBALS['pid']." 
                                          AND encounter=".$_SESSION['encounter']."");
    $resLabRequisitionTests=sqlFetchArray($getLabRequisitionTests);
    */
    
}

if($mode==1)
{
   
   $getEncounterDetails=sqlStatement("SELECT * FROM tbl_form_lab_requisition
                                      WHERE id=".$GLOBALS['id']." 
                                      AND pid=".$GLOBALS['pid']." 
                                      AND encounter=".$GLOBALS['encounter']."");
   $resEncounterDetails=sqlFetchArray($getEncounterDetails);     
}


if(isset($_POST['btnSubmit']))
{
    //$nextVisitDays=($_POST['radDays']!='')?$_POST['radDays']:$_POST['txtOther'];
    $cnt=0;    
    $lab_details=array();
    for($cnt=0;$cnt<5;$cnt++)
    {
        $labName=$_POST['txtLabName'.$cnt];
        $labNotes=$_POST['txtLabNotes'.$cnt];
        $labStatus=$_POST['txtLabStatus'.$cnt];
        
        if($labName!='')
        {
            array_push($lab_details,$labName.';'.$labNotes.';'.$labStatus);                
        }
        else
        {
            array_push($lab_details,'');
        }
    }        
    //print_r($lab_details);
              
    if(strpos($_SERVER["HTTP_REFERER"],'/interface/patient_file/encounter/load_form.php'))
    {     
        // insert
        
        $test=implode(',',$_POST['chkTests']);
        $finalized=($_POST['chkFinalized']=='on')?'Y':'N';
//echo '<br>G='.$_SESSION['encounter'].",".$GLOBALS['pid'].",".$user_name."<br>";
$insert_form_LabRequisition_Sql ="INSERT INTO tbl_form_lab_requisition(pid,encounter,
created_by,date_of_request,stat,specimen_week,fasting,frail_health,is_home_bound,is_preference_home_health,
diagnosis_codes,tests,other1,other2,other3,is_colonoscopy_required,patient_has,
lab1,lab2,lab3,lab4,lab5,
nurse_practitioner_signature,nurse_practitioner_signature_date,physician_signature,printed_name,date_of_signature,created_date,

finalized

)
VALUES(".$GLOBALS['pid'].",".$_SESSION['encounter'].",
'".$_POST['loginUserId']."','".$_POST['txtDateOfRequest']."',
'".$_POST['rdStat']."','".$_POST['txtSpecimenWeek']."',
'".$_POST['rdFasting']."','".$_POST['rdFrailHealth']."',
'".$_POST['rdHomeBound']."','".$_POST['rdPHH']."',
'".$_POST['txtDiagnosisCodes']."',
    
'".$test."','".$_POST['txtOther1']."','".$_POST['txtOther2']."','".$_POST['txtOther3']."',
'".$_POST['rdColonoscopyReq']."',
'".$_POST['rdARC']."',
'".$lab_details[0]."','".$lab_details[1]."','".$lab_details[2]."',
'".$lab_details[3]."','".$lab_details[4]."',
    
'".$_POST['txtNursePractitionerSignature']."',
'".$_POST['txtNursePractitionerSignDate']."',
'".$_POST['txtPhysicianSignature']."','".$_POST['txtPrintedName']."',
'".$_POST['txtDateOfSignature']."',now(),
    
'".$finalized."'
    
)";
    
mysql_query($insert_form_LabRequisition_Sql);
        
        $lastInsertedFormId=mysql_insert_id();                
        
        $insert_forms ="INSERT INTO forms(date,encounter,form_name,form_id,pid,user,groupname,authorized,deleted,formdir)
                        VALUES(now(),".$_SESSION['encounter'].",'Lab Requisition',$lastInsertedFormId,".$GLOBALS['pid'].",
                               '".$_SESSION['authUser']."','Default',1,0,'lab_requisition')";
        
        mysql_query($insert_forms);
        
    }
    
    else if(strpos($_SERVER["HTTP_REFERER"],'/interface/patient_file/encounter/view_form.php'))
    {        
        //print_r($_POST);
        // update
        //print_r($_POST['chkTests']);
        $test=implode(',',$_POST['chkTests']);
        $finalized=($_POST['chkFinalized']=='on')?'Y':'N';
        //echo 'F='.$finalized;die;
$update_form_LabRequisition_Sql ="UPDATE tbl_form_lab_requisition
SET date_of_request='".$_POST['txtDateOfRequest']."',
    stat='".$_POST['rdStat']."',
    specimen_week='".$_POST['txtSpecimenWeek']."',
    fasting='".$_POST['rdFasting']."',
    frail_health='".$_POST['rdFrailHealth']."',
    is_home_bound='".$_POST['rdHomeBound']."',
    is_preference_home_health='".$_POST['rdPHH']."',
    diagnosis_codes='".$_POST['txtDiagnosisCodes']."', 
    tests='".$test."',other1='".$_POST['txtOther1']."',other2='".$_POST['txtOther2']."',other3='".$_POST['txtOther3']."',
    is_colonoscopy_required='".$_POST['rdColonoscopyReq']."',
    patient_has='".$_POST['rdARC']."',
        
lab1='$lab_details[0]',lab2='$lab_details[1]',lab3='$lab_details[2]',
lab4='$lab_details[3]',lab5='$lab_details[4]',
        
    nurse_practitioner_signature='".$_POST['txtNursePractitionerSignature']."',
    nurse_practitioner_signature_date='".$_POST['txtNursePractitionerSignDate']."',
    physician_signature='".$_POST['txtPhysicianSignature']."',
    printed_name='".$_POST['txtPrintedName']."',
    date_of_signature='".$_POST['txtDateOfSignature']."',
        
    updated_date=now(),
    
    finalized='".$finalized."'
WHERE id=".$_REQUEST['id']." 
AND pid=".$GLOBALS['pid']." 
AND encounter=".$_SESSION['encounter']."";
//die;
        //$insert_form_LabRequisition_Res=sqlStatement($insert_form_LabRequisition_Sql);
sqlStatement($update_form_LabRequisition_Sql);         
       
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
     <script type="text/javascript">
    function selectedCodeDesc(desc)
    {
       jQuery('#txtDiagnosisCodes').val(desc);
       jQuery('#tblSearchResult').hide();
    }
    </script>
     <script type='text/javascript'>
    var radios='';
    </script>


    <script type='text/javascript'>

        function showConfigByNote(note_id,mode,id)
        {//alert('note--'+note_id);
            $.ajax({
                        type: 'POST',                    
                        url: '../../forms_configuration/lab_requisition/lab_requisition_form.php',                    
                        data: {note_id:note_id,mode:mode,id:id},

                        success: function(response)
                        {

                            $("#div_form_facetoface").html(response);
                            //location.reload();                        
                        },
                        failure: function(response)
                        {
                            alert("Failed");
                        }
                    });
        }

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
          
    <br>
    <div class="row">
        <div id="divTestDetails" style="float:left;width:100%;border:1px SOLID #000;">       
            
        <?php 
            $getPatientInfo=sqlStatement("SELECT CONCAT(fname,' ',lname)
                                         FROM patient_data WHERE pid=".$GLOBALS['pid']."");
            $resPatientInfo=sqlFetchArray($getPatientInfo);
        ?>
            
            
        <?php 
        if($mode==1)
        {  
           ?>

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
        <?php

        }

        ?>
                <br>
            <b>User Name : </b>
            <?php 
    $uname='';
    if($mode==1)
    {
        $user_column=($resEncounterDetails['updated_by']!=0)?$resEncounterDetails['updated_by']:$resEncounterDetails['created_by'];
        $getCreatorDetails="SELECT CONCAT(fname,' ',lname) AS uname FROM users 
                         WHERE id=(SELECT $user_column FROM tbl_form_lab_requisition
                                   WHERE id='".$_REQUEST['id']."' 
                                   AND encounter='".$GLOBALS['encounter']."' 
                                   AND pid='".$GLOBALS['pid']."')";
        $resCreatorDetails=sqlStatement($getCreatorDetails);  
        $rowCreatorDetails=sqlFetchArray($resCreatorDetails);
        $uname=$rowCreatorDetails['uname'];
    }
    else
    {
        $getUserDetails="SELECT CONCAT(fname,' ',lname) AS uname FROM users 
                         WHERE id='".$_SESSION['authUserID']."'";
        
        $resUserDetails=sqlStatement($getUserDetails);
        $rowUserDetails=sqlFetchArray($resUserDetails);
        $uname=$rowUserDetails['uname'];
    }
    ?>
    <label><?php echo $uname;?></label>
            <br>
            
            <b>Patient Name : </b>
            <?php 
            $getPatientName=sqlStatement("SELECT CONCAT(fname,' ',lname) AS pname FROM patient_data WHERE pid=".$GLOBALS['pid']."");
            $resPatientName=sqlFetchArray($getPatientName);
        ?>  
 
       <label><?php echo $resPatientName['pname'];?></label>            
       <br>
       
            <b>Date of Request : </b>
            <input type='text' size='10' name='txtDateOfRequest' id='txtDateOfRequest' onkeyup='datekeyup(this,mypcc)' 
       onblur='dateblur(this,mypcc)' title='yyyy-mm-dd last date of this event' readonly="readonly" 
       value="<?php echo ($mode==1) ? $resEncounterDetails['date_of_request']: $resCheckIfExists['date_of_request'];?>" />
<img src='../../pic/show_calendar.gif' align='absbottom' width='24' height='22'
id='img_calendar_DateOfRequest' border='0' alt='[?]' style='cursor:pointer;'
title='Click here to choose a date'>
<script>
Calendar.setup({inputField:'txtDateOfRequest', ifFormat:'%Y-%m-%d', button:'img_calendar_DateOfRequest'});
</script>
            &nbsp;&nbsp;&nbsp;
            <b>Stat: </b>
            <input type="radio" id="rdStatYes" name="rdStat" value="Y" />YES
    <input type="radio" id="rdStatNo" name="rdStat" value="N" />NO    
    <script type='text/javascript'>
                radios = jQuery('input:radio[name=rdStat]');
                radios.filter('[value=<?php echo ($mode==1) ? $resEncounterDetails['stat']: $resCheckIfExists['stat']; ?>]').attr('checked', true);
    </script>

<br>
    <b>Specimen to be collected the week of :</b>
    <input type="text" id="txtSpecimenWeek" name="txtSpecimenWeek" maxlength="20" 
    value="<?php echo ($mode==1) ? $resEncounterDetails['specimen_week']: $resCheckIfExists['specimen_week'];?>" >
  
    <br>
    <b>Fasting : </b>
    <input type="radio" id="rdFastingYes" name="rdFasting" value="Y" />YES
    <input type="radio" id="rdFastingNo" name="rdFasting" value="N" />NO    
    <script type='text/javascript'>
                radios = jQuery('input:radio[name=rdFasting]');
                radios.filter('[value=<?php echo ($mode==1) ? $resEncounterDetails['fasting']: $resCheckIfExists['fasting']; ?>]').attr('checked', true);
    </script>
    <br><br>
    <b>Is Patient of Frail Health (bedridden, Wheelchair bound) :</b>
    <input type="radio" id="rdFrailHealthYes" name="rdFrailHealth" value="Y" />YES
    <input type="radio" id="rdFrailHealthNo" name="rdFrailHealth" value="N" />NO
    <script type='text/javascript'>
                radios = jQuery('input:radio[name=rdFrailHealth]');
                radios.filter('[value=<?php echo ($mode==1) ? $resEncounterDetails['frail_health']: $resCheckIfExists['frail_health']; ?>]').attr('checked', true);
    </script>
    <br><br>
    <b>Is Patient Unable to drive or leaving home is a major effort :</b>
    <input type="radio" id="rdHomeBoundYes" name="rdHomeBound" value="Y" />YES
    <input type="radio" id="rdHomeBoundNo" name="rdHomeBound" value="N" />NO
    <script type='text/javascript'>
                radios = jQuery('input:radio[name=rdHomeBound]');
                radios.filter('[value=<?php echo ($mode==1) ? $resEncounterDetails['is_home_bound']: $resCheckIfExists['is_home_bound']; ?>]').attr('checked', true);
    </script>
    <br><br>
    <b>Is there a Preference to Order through Home Health :</b>
    <input type="radio" id="rdPHHYes" name="rdPHH" value="Y" />YES
    <input type="radio" id="rdPHHNo" name="rdPHH" value="N" />NO
    <script type='text/javascript'>
                radios = jQuery('input:radio[name=rdPHH]');
                radios.filter('[value=<?php echo ($mode==1) ? $resEncounterDetails['is_preference_home_health']: $resCheckIfExists['is_preference_home_health']; ?>]').attr('checked', true);
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
    value="<?php echo ($mode==1) ? $resEncounterDetails['diagnosis_codes']: $resCheckIfExists['diagnosis_codes']; ?>" />
  
<!--<textarea id="txtDiagnosisCodes" name="txtDiagnosisCodes" cols="50"></textarea>
-->
     
    <br><br>
    <div id='divTestRequired'style="width:50%">
    <b>TEST(s) Requested </b>
    
        <br>
        
<!--        <input type='checkbox' value='CXR' id='chk_CXR' name='chkTests[]' />CXR-->        
        <input type='checkbox' value='CBC' id='chk_CBC' name='chkTests[]' />CBC 
        <input type='checkbox' value='WDiff' id='chk_WDiff' name='chkTests[]' />W/Diff
        <input type='checkbox' value='UA' id='chk_UA' name='chkTests[]' />UA 
        <br>        
<!--        <input type='checkbox' value='KUB' id='chk_KUB' name='chkTests[]' />KUB -->
        <input type='checkbox' value='CMP' id='chk_CMP' name='chkTests[]' />CMP 
        <input type='checkbox' value='Urine Culture' id='chk_urine_culture' name='chkTests[]' />URINE CULTURE
        <br>
        <input type='checkbox' value='TSH' id='chk_TSH' name='chkTests[]' />TSH
        <input type='checkbox' value='Lipid Panel' id='chk_lipid_panel' name='chkTests[]' />LIPID PANEL 
        <input type='checkbox' value='PSA' id='chk_PSA' name='chkTests[]' />PSA 
        <br>
        <input type='checkbox' value='HbAC' id='chk_HbAC' name='chkTests[]' />HbA,C
        <input type='checkbox' value='Mammogram' id='chk_mammogram' name='chkTests[]' />MAMMOGRAM
        <br>
        <input type='checkbox' value='Vitamin D' id='chk_VitaminD' name='chkTests[]' />Vitamin D
        <input type='checkbox' value='PT_INR' id='chk_PT_INR' name='chkTests[]' />PT/INR
        <input type='checkbox' value='XRay' id='chk_XRay' name='chkTests[]' />X-Ray<br>
        Other 1<input type='text' value="<?php echo ($mode==1) ? $resEncounterDetails['other1']: $resCheckIfExists['other1']; ?>" id='txtOther1' name='txtOther1' /><br>
        Other 2<input type='text' value="<?php echo ($mode==1) ? $resEncounterDetails['other2']: $resCheckIfExists['other2']; ?>" id='txtOther2' name='txtOther2' /><br>
        Other 3<input type='text' value="<?php echo ($mode==1) ? $resEncounterDetails['other3']: $resCheckIfExists['other3']; ?>" id='txtOther3' name='txtOther3' />
                
        <?php 
        if($mode==1)
        {
            $tests_elements=explode(',',$resEncounterDetails['tests']);  
        }
        else
        {
            $tests_elements=explode(',',$resCheckIfExists['tests']);
        }
         //print_r($tests_elements);
        foreach($tests_elements as $test)
        {
            echo "<script>
            //jQuery('#chk_$test').attr('checked',true);
            jQuery('input[value=\'$test\']').attr('checked',true);
            </script>";
        }?>   

        
        
    </div>
      
    <br>
 
    <b>Is Colonoscopy Required:</b>
    <input type="radio" id="rdColonoscopyReqYes" name="rdColonoscopyReq" value="Y" />YES
    <input type="radio" id="rdColonoscopyReqNo" name="rdColonoscopyReq" value="N" />NO
    <script type='text/javascript'>
                radios = jQuery('input:radio[name=rdColonoscopyReq]');
                radios.filter('[value=<?php echo ($mode==1) ? $resEncounterDetails['is_colonoscopy_required']: $resCheckIfExists['is_colonoscopy_required']; ?>]').attr('checked', true);
    </script>
    <br>
       
    <div id="divDidPatient">
        <b>Did Patient:</b>
        <input type="radio" id="rdAccept" name="rdARC" value="Accepted" />Accept
        <input type="radio" id="rdRefuse" name="rdARC" value="Refused" />Refuse
        <input type="radio" id="rdComplete" name="rdARC" value="Completed" />Complete
    </div>
    <script type='text/javascript'>
                radios = jQuery('input:radio[name=rdARC]');
                radios.filter('[value=<?php echo $resEncounterDetails['patient_has']; ?>]').attr('checked', true);
    </script>
    
    <script type="text/javascript">
        $(document).ready(function() {
           jQuery('input:radio[name=rdARC]').attr("disabled", "disabled");   
           jQuery('#divDidPatient').hide();
           
           <?php           
           if($mode==1)
           {
               if($resEncounterDetails['is_colonoscopy_required']=='Y')
               {
                   ?>
                   jQuery('input:radio[name=rdARC]').removeAttr("disabled");   
                   jQuery('#divDidPatient').show();
           <?php
           
               }
           }
           ?>
           if(jQuery('#rdColonoscopyReqYes').is(":checked"))
           {
                jQuery('#divDidPatient').show();
                jQuery('input:radio[name=rdARC]').removeAttr("disabled");               
           }
         });
         
          /*jQuery('input:radio[name=rdColonoscopyReq]').click(function(){            
               jQuery('input:radio[name=rdARC]').removeAttr("disabled");            
        });*/
        
    
        jQuery('#rdColonoscopyReqYes').click(function(){
               jQuery('input:radio[name=rdARC]').removeAttr("disabled");     
               jQuery('#divDidPatient').show();
        });
    
        jQuery('#rdColonoscopyReqNo').click(function(){
               jQuery('input:radio[name=rdARC]').prop("checked",false); 
               jQuery('input:radio[name=rdARC]').attr("disabled", "disabled");
               jQuery('#divDidPatient').hide();
        });
        
    </script>
            
    <br>
    
    <div id="div_lab">        
        <?php
                if($mode==1)
                {
                    $cnt=0;
                    $labs=array();
                    for($cnt=0;$cnt<5;$cnt++)
                    {
                        //print_r($resEncounterDetails['lab'.($cnt+1)]);
                       $labs[$cnt]=explode(';',$resEncounterDetails['lab'.($cnt+1)]);
                    }                    
                }
        ?>
                
        <b>Lab details</b>
        <table border='2'>
            <tr>
                <th>Lab Name</th><th>Notes</th><th>Status</th>
            </tr>
            <?php
            $cnt=0;
            for($cnt=0;$cnt<5;$cnt++)
            {                   
                ?>                                                
                <tr>
                    <td><input type='text' id='txtLabName<?php echo $cnt;?>' name='txtLabName<?php echo $cnt;?>'
                         value="<?php echo ($mode==1) ? $labs[$cnt][0] : '';?>" /></td>
                    <td><input type='text' id='txtLabNotes<?php echo $cnt;?>' name='txtLabNotes<?php echo $cnt;?>'
                         value="<?php echo ($mode==1) ? $labs[$cnt][1] : '';?>" /></td>
                    <td>
                        <select id='txtLabStatus<?php echo $cnt;?>' name='txtLabStatus<?php echo $cnt;?>' 
                                onchange=''>
                            <option value='none'>none</option>
                            <option value='ordered'>Ordered</option>                        
                            <option value='cancelled'>Canceled</option>                        
                            <option value='received_result'>Received Results</option>
                            <option value='patient_no_response'>Patient No Response</option>                            
                            
                            <?php                             
                            if($mode==1)                                
                            {
                            ?>
                <script>                    
                    jQuery("#txtLabStatus<?php echo $cnt;?>").val('<?php echo $labs[$cnt][2];?>');                    
                </script>                                                        
                            <?php                            
                            }                            
                            ?>                                                                                    
                                                                                    
                        </select>                                                                                       
                    </td>                                              
                </tr>                                                   
        
           <?php 
            }
            ?>
            
        </table>
                                               
    </div>
    
    <br>
    <b>Finalized</b>
    <input type="checkbox" id="chkFinalized" name="chkFinalized" class="" 
      />
                        
    <?php 
    
    if($mode==1 && $resEncounterDetails['finalized']=='Y')
    {
        
       echo "<script>
            jQuery('#chkFinalized').prop('checked','checked');
            
            $('#frmActionPlan :input').attr('disabled', true);            
            $('#frmActionPlan :select').attr('disabled', true);
            $('#frmActionPlan :textarea').attr('disabled', true);


            $(':submit').attr('disabled', true);
                jQuery('#btnCancel').prop('disabled','');
                
             </script>";

    }    
    ?>
        
    </div>            
        
    </div>        
    
    <br>
    <!--
    <div class="row">  
      <b>Nurse Practitioner Signature</b>
      <input type="text" id="txtNursePractitionerSignature" name="txtNursePractitionerSignature" maxlength="20"
             value="<?php echo ($mode==1) ? $resEncounterDetails['nurse_practitioner_signature']: '';?>" >
     
      <b>Date</b> 
          
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
    
    <input type='text' size='10' name='txtDateOfSignature' id='txtDateOfSignature' onkeyup='datekeyup(this,mypcc)' 
           value="<?php echo ($mode==1) ? $resEncounterDetails['date_of_signature']: '';?>" onblur='dateblur(this,mypcc)' title='yyyy-mm-dd last date of this event' readonly="readonly" />
<img src='../../pic/show_calendar.gif' align='absbottom' width='24' height='22'
id='img_calendar_txtDateOfSignature' border='0' alt='[?]' style='cursor:pointer;'
title='Click here to choose a date'>
<script>
Calendar.setup({inputField:'txtDateOfSignature', ifFormat:'%Y-%m-%d', button:'img_calendar_txtDateOfSignature'});
</script>
      
      
  </div>
    -->
    <br>
    <?php //print_r($GLOBALS); ?>
    <?php //echo 'AU == '. $_SESSION['authUser']; ?>
    <input type="hidden" name="loginUserName" id="loginUserName" 
           value="<?php echo $_SESSION['authUser']; ?>" />
    <input type="hidden" name="loginUserId" id="loginUserId" 
           value="<?php echo $_SESSION['authUserID']; ?>" />
    
    <div align="center">
        <input type="submit" name="btnSubmit" value="Submit" id="btnSubmit" >
        <input type="button" name="btnCancel" value="Cancel" id="btnCancel" onclick='javascript:history.back();' >
    </div>
   </div>
    
 </div>
    
    
</form>
</body>
</html>
