<?php
//ob_start();
//require_once("interface/globals.php");
require_once("dbConnect.php");

$pid=   $_REQUEST['pid'];

?>
<!DOCTYPE html>
<html lang="en">
<head>
<?php


//print_r($GLOBALS);
if(isset($_POST['btnSubmit']))
{
    
    $nextVisitDays=($_POST['radDays']!='')?$_POST['radDays']:$_POST['txtOther'];
     
$insert_form_FacetoFace_Sql ="INSERT INTO tbl_form_facetoface(pid,encounter,is_home_bound,is_hhc_needed,other_physician,
    is_house_visit_needed,medical_condition,necessary_hhs,nursing,physical_therapy,occupational_therapy,speech,support_service_reason,
    patient_homebound_reason,nurse_practitioner_signature,nurse_practitioner_signature_date,physician_signature,
    printed_name,printed_name_date,created_date)
                              VALUES(".$_POST['btnpid'].",".$_POST['txtVisitDate'].",
                                    '".$_POST['radBound']."','".$_POST['radCare']."','".$_POST['radPhysician']."',
                                    '".$_POST['radVisit']."','".$_POST['txtMedical']."','$nextVisitDays',
                                    '".$_POST['txtNursing']."','".$_POST['txtPhysical']."',
                                    '".$_POST['txtOccupational']."','".$_POST['txtSpeech']."',
                                    '".$_POST['txtTreatment']."','".$_POST['txtHomeBound']."',
                                    '".$_POST['txtNurse']."','".$_POST['txtNursePractitionerSignDate']."',
                                    '".$_POST['txtPhysicianSignature']."','".$_POST['txtPrintedName']."',
                                    '".$_POST['txtPrintedDate']."',now()
                                    )";

$insert_form_FacetoFace_Res=mysql_query($insert_form_FacetoFace_Sql);

//header("location:facetoface.php?message=y");
//echo "<h5 align=left>Data Saved Successfully</h5>";
    
?>
     <script type="text/javascript">alert("Data Saved Successfully"); history.go(-1);</script>
    

    <?php

}

  
            $getPatientName=mysql_query("SELECT fname,lname FROM patient_data WHERE pid=".$pid."");
            $resPatientName= mysql_fetch_array($getPatientName);
        
?>

    
        <meta http-equiv="content-type" content="text/html; charset=UTF-8"> 
        <title>Face-to-Face Encounter</title>
        
        
<link rel="stylesheet" href="interface/main/css/bootstrap-3.0.3.min.css" type="text/css">
<script type="text/javascript" src="interface/main/js/jquery-2.0.3.min.js"></script>
<script type="text/javascript" src="interface/main/js/bootstrap-3.0.3.min.js"></script>
<script type="text/javascript">
 
        
</script>
</head>
<body>
<form name="frmActionPlan" id="frmActionPlan" method="POST" role="form">    
<div class="container">
    
        <div class="form-group">
    <div class="row" style="text-align: center;">
    <h4>Documentation of Face-to-Face Encounter</h4>
    </div>
    
    <div class="row">
        Patient name and Identification<input type="text" name="txtPatientName" value="<?php echo $resPatientName['fname']." ".$resPatientName['lname'];?>"  class="form-control" > </div>
            
    <div class="row">I certify that this patient is under my care and that I, or a nurse practitioner or physician’s
assistant working with me, had a face-to-face encounter that meets the physician face-to-face
encounter requirements with this patient on: <input type="text" name="txtVisitDate" value="2014-03-26"> 
    </div>
    
    <div  class="container">
        <div class="row">
            <b>Is Patient Home Bound or Can't Drive</b> (Circle your choice)    
            <input name="radBound" type="radio" value="Y"> Y <input name="radBound" type="radio" value="N"> N        
        </div>
    
        <div class="row">
            <b>Is Home Health Care Needed</b> (Circle your choice)
            <input name="radCare" type="radio" value="Y"> Y <input name="radCare" type="radio" value="N"> N 
        </div>
    
        <div class="row">
            <b>Does Patient have reliable other Primary Care Physician</b> (Circle your choice) 
            <input name="radPhysician" type="radio"  value="Y"> Y <input name="radPhysician" type="radio" value="N"> N
        </div>
    
        <div class="row">
            <b>Is House Visit Needed</b> (Circle your choice)
            <input name="radVisit" type="radio" value="Y"> Y <input name="radVisit" type="radio" value="N"> N
        </div>
    
        <div class="row">
            <b>If Yes</b> (Circle Next Visit in Days approximately) 
    <input name="radDays" type="radio" value="30"> 30 <input name="radDays" type="radio" value="60"> 60
     <input type="radio" name="radDays" value="90"> 90     
    Other <input type="text" name="txtOther" value="" class="form-control" >         
        </div>
    
    <div class="row">
        The encounter with the patient was in whole or in part for the following medical condition which is the 
        primary reason for home health care and <b>HOW LONG:</b> (List medical condition)<br>
        <textarea name="txtMedical" class="form-control pull-left"></textarea>
    </div>
    
    <div class="row">        
        <b>I certify that, based on my findings, the following services are medically necessary home health
services:</b>
    </div>
        
<div class="row"><b>Nursing</b><input type="text" name="txtNursing"  value=""  class="form-control" >  </div>    
<div class="row"><b>Physical Therapy</b> <input type="text" name="txtPhysical"   value=""  class="form-control" > </div>    
<div class="row"><b>Occupational Therapy</b><input type="text" name="txtOccupational"  value=""  class="form-control" >   </div>    
<div class="row"><b>Speech-language Pathology</b><input type="text" name="txtSpeech" value=""  class="form-control" >   </div>    

<div class="row">
    To provide the following care/treatments: (Required only when the physician completing the face to face encounter documentation is different than the physician completing the plan of care):
    <br>
        <textarea name="txtTreatment" class="form-control pull-left">
</textarea>
</div>    

<div class="row">
    My clinical findings support the need for the above services because:
    <br>
        <textarea name="txtFindings" class="form-control pull-left">
</textarea>
</div>    

<div class="row">
    Further, I certify that my clinical findings support that this patient is homebound (i.e. absences from home require considerable and taxing effort and are for medical reasons or religious services or infrequently or of short duration when for other reasons) because
    <br>
        <textarea name="txtHomeBound" class="form-control pull-left">
</textarea>
</div>  

<div class="row">
  <div class="col-md-8">
      <b>Nurse Practitioner Signature</b> <input type="text" name="txtNurse" value="" class="form-control" size="60">
  </div>
  <div class="col-md-4">
      <b>Date</b> <input type="text" name="txtNursePractitionerSignDate" value="" size="30" class="form-control" value="">
  </div>
</div>

<div class="row">
    <b>Physician's Signature</b> <input type="text" name="txtPhysicianSignature" value="" size="80" class="form-control" >
</div>

<div class="row">
  <div class="col-md-8">
      <b>Printed Name</b> <input type="text" name="txtPrintedName" value="" size="80"  class="form-control">
  </div>
  <div class="col-md-4">
      <b>Date</b> <input type="text" name="txtPrintedDate" value="" size="30" class="form-control" >
  </div>
</div>

<div class="row" align="center">
    <input type="submit" name="btnSubmit" value="Submit" id="btnSubmit" class="btn" >
    <input type="button" name="btnBack" value="Back" id="btnBack" class="btn" onclick="window.history.back(); " >
    <input type="hidden" name="btnpid" value="<?php echo $pid;?>">
                </div>
            </div>
        
</div>

</form>
</body>
</html>


