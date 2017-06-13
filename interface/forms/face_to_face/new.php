<?php
require_once("../../globals.php");
require_once("$srcdir/acl.inc");
require_once("$srcdir/formdata.inc.php");
require_once("$srcdir/globals.inc.php");
require_once("$srcdir/user.inc");
require_once("$srcdir/classes/CouchDB.class.php");

$mode=0;
$pri_diagnosis_id=0;

if(strpos($_SERVER["HTTP_REFERER"],'/interface/patient_file/encounter/load_form.php'))
{
    $mode=0;
}
//else if(strpos($_SERVER["HTTP_REFERER"],'/interface/patient_file/encounter/view_form.php'))
else if(strpos($_SERVER["HTTP_REFERER"],'/interface/patient_file/encounter/forms.php') && $_REQUEST['id']!='')
{
    $mode=1;
    
echo "<script src='../../main/jquery-latest.min.js' type='text/javascript'></script>";
    
    $getEncounterDetails=sqlStatement("SELECT * FROM tbl_form_facetoface 
                                       WHERE id=".$_REQUEST['id']." 
                                        AND pid=".$GLOBALS['pid']." 
                                        AND encounter=".$GLOBALS['encounter']."");
    $resEncounterDetails=sqlFetchArray($getEncounterDetails);
    
    $pri_diagnosis_id=$resEncounterDetails['pri_diagnosis_ids'];
    
    $sec_diagnosis_id=$resEncounterDetails['sec_diagnosis_id'];
}

if(isset($_POST['save'])|| isset($_POST['pending'])|| isset($_POST['finalized']))// hema
{
    
    $nextVisitDays=($_POST['radDays']!='')?$_POST['radDays']:$_POST['txtOther'];
    /* hema */
    $logdata= array();
    if(isset($_POST['save'])):
        $status = $_POST['save'];
    elseif(isset($_POST['pending'])):
        $status = $_POST['pending'];
    elseif(isset($_POST['finalized'])):
        $status = $_POST['finalized'];
    endif;
    $formidcheck = ($newid? $newid: $_GET["id"]);
    $data = mysql_query("SELECT logdate from `tbl_allcare_formflag` WHERE  form_id=".$formidcheck);
    while ($row = mysql_fetch_array($data,MYSQL_ASSOC)) {
        $array =  unserialize($row['logdate']);
        $count= count($array);
    }
    $count = isset($count)? $count: 0;
    $array2[] = array( 'authuser' =>$_SESSION["authUser"],'status' => $status,'time' => time(),'count'=> $count+1);
    $logdata = array_merge_recursive($array, $array2);


    $logdata= ($logdata? serialize($logdata): serialize($array2) );
   /* ===================== */ 
    if(strpos($_SERVER["HTTP_REFERER"],'/interface/patient_file/encounter/load_form.php'))
    {
        // insert 
        // hema
        
        if(isset($_POST['save'])):
            $finalized ='N';
            $pending = 'N';
            $save = 'Y';
        elseif (isset($_POST['pending'])):
            $finalized ='N';
            $pending = 'Y';
            $save = 'Y';
        elseif (isset($_POST['finalized'])):
            $finalized ='Y';
            $pending = 'Y';
            $save = 'Y';
        endif;
        /* ====================================== */
        $insert_form_FacetoFace_Sql ="INSERT INTO tbl_form_facetoface(pid,encounter,
                                            pri_diagnosis_ids,sec_diagnosis_ids,txt_sec_diagnosis,date_of_service,is_home_bound,
                                            is_hhc_needed,other_physician,is_house_visit_needed,medical_condition,necessary_hhs,nursing,
                                            physical_therapy,occupational_therapy,speech,care_treatment,support_service_reason,
                                            patient_homebound_reason,comments,assessments,treatment_plan,finalized,created_by,created_date
                                    )VALUES(".$GLOBALS['pid'].",".$GLOBALS['encounter'].",'".$_POST['pri_diagnosis_cat']."',"
                                        . "". "'".$val_sec_diagnosis_id."','".$val_txt_sec_diagnosis."','".$_POST['txtDateOfService']."',
                                        '".$_POST['radBound']."','".$_POST['radCare']."','".$_POST['radPhysician']."',
                                        '".$_POST['radVisit']."','".$_POST['txtMedical']."','$nextVisitDays',
                                        '".$_POST['txtNursing']."','".$_POST['txtPhysical']."','".$_POST['txtOccupational']."',
                                        '".$_POST['txtSpeech']."','".$_POST['txtTreatment']."','".$_POST['txtFindings']."',
                                        '".$_POST['txtHomeBound']."','".$_POST['txtComments']."','".$_POST['txtAssessments']."',"
                                        . "'".$_POST['txtTreatmentPlan']."','','".$_SESSION['authUserID']."',now()
                                    )";        
        
        //$insert_form_FacetoFace_Res=sqlStatement($insert_form_FacetoFace_Sql);
        $insert_form_FacetoFace_Res=mysql_query($insert_form_FacetoFace_Sql) or die(mysql_error());
        $lastInsertedFormId=  mysql_insert_id();
       // hema
        sqlInsert("INSERT into `tbl_allcare_formflag`(`form_id`, `encounter_id`, `form_name`, `pending`, `save`,`finalized`,`logdate`) VALUES(".$lastInsertedFormId.",".$GLOBALS['encounter'].",'Face to Face','".$pending."','".$save."','".$finalized."','".$logdata."')");
        // ====================================== 
        $insert_forms ="INSERT INTO forms(date,encounter,form_name,form_id,pid,user,groupname,authorized,deleted,formdir)
                       VALUES(now(),".$GLOBALS['encounter'].",'Face to Face',$lastInsertedFormId,".$GLOBALS['pid'].",
                                             '".$_SESSION['authUser']."','Default',1,0,'face_to_face')";

        $insert_forms_Res=sqlStatement($insert_forms);

    }
    else if(strpos($_SERVER["HTTP_REFERER"],'/interface/patient_file/encounter/view_form.php'))
    {
        // update        
               
        $update_form_FacetoFace_Sql ="UPDATE tbl_form_facetoface
                                      SET pri_diagnosis_ids='".$_POST['pri_diagnosis_cat']."',
                                        sec_diagnosis_ids='$val_sec_diagnosis_id',
                                        txt_sec_diagnosis='$val_txt_sec_diagnosis',  
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
                                        comments='".$_POST['txtComments']."',
                                        assessments='".$_POST['txtAssessments']."',
                                        treatment_plan='".$_POST['txtTreatmentPlan']."',
                                        finalized='',
                                        updated_by='".$_SESSION['authUserID']."',
                                        updated_date=now()                            
                                      WHERE id=".$_REQUEST['id']." 
                                        AND pid=".$GLOBALS['pid']." 
                                        AND encounter=".$GLOBALS['encounter']."";

        //$insert_form_FacetoFace_Res=sqlStatement($insert_form_FacetoFace_Sql);
        
        // Hema
        if(isset($_POST['save'])):
           sqlInsert("UPDATE `tbl_allcare_formflag` SET `save` = 'Y',`logdate` ='".$logdata."' WHERE `form_name` = 'Face to Face' AND `form_id` =  ".$_REQUEST['id']);
        elseif (isset($_POST['pending'])):
            sqlInsert("UPDATE `tbl_allcare_formflag` SET 
            `pending` = 'Y',`logdate` ='".$logdata."'  WHERE `form_name` = 'Face to Face' AND `form_id` =  ".$_REQUEST['id']);
        elseif (isset($_POST['finalized'])):
           sqlInsert("UPDATE `tbl_allcare_formflag` SET  `save` = 'Y',`finalized`='Y',
            `pending` = 'Y',`logdate` ='".$logdata."'  WHERE `form_name` = 'Face to Face' AND `form_id` =  ".$_REQUEST['id']);
        endif;
        // ===================================
        $update_form_FacetoFace_Res=mysql_query($update_form_FacetoFace_Sql);
    }
    
    echo "<script type='text/javascript'>location.href='forms.php';</script>";
             
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8"> 
        <title>Face-to-Face Encounter</title>
                
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

<script type='text/javascript'>
    var radios='';
</script>

<script type='text/javascript'>
    
    function showConfigByDiagnosis(pri_diagnosis_id,mode,id)
    {//alert('diagnosis--'+diagnosis_id);
        $.ajax({
                    type: 'POST',                    
                    url: '../../forms_configuration/face_to_face/face_to_face_form.php',                    
                    data: {pri_diagnosis_id:pri_diagnosis_id,mode:mode,id:id},

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
    
    function showSecondaryDiagnosis(pri_diagnosis_id,mode,id)
    {
        $.ajax({
                    type: 'POST',                    
                    url: '../../forms_configuration/face_to_face/show_secondary_diagnosis.php',                    
                    data: {pri_diagnosis_id:pri_diagnosis_id,mode:mode,id:id},

                    success: function(response)
                    {
                        
                        $("#div_secondary_diagnosis").html(response);
                        //location.reload();                        
                    },
                    failure: function(response)
                    {
                        alert("Failed");
                    }
                });
    }
    
</script>

</head>
<body style="background-color: #FEFDCF;" onload="javascript:showConfigByDiagnosis('<?php echo $pri_diagnosis_id;?>','<?php echo $mode;?>','<?php echo $_REQUEST['id'];?>');
      showSecondaryDiagnosis('<?php echo $pri_diagnosis_id;?>','<?php echo $mode;?>','<?php echo $_REQUEST['id'];?>');">
<form name="frmActionPlan" id="frmActionPlan" method="POST" role="form">
<div class="container">
  <div class="form-group">
    <div class="row" style="text-align: center;">
        <h4>Face-to-Face Encounter</h4>
    </div>
    
      
      <!--
    <div class="row">
        <b>Primary Diagnosis</b>
        
        <?php 
			
            //$getDiagnosis=sqlStatement("SELECT option_id,title FROM list_options WHERE list_id='FaceToFace_Configuration_Diagnosis'");
            
            $getDiagnosis=sqlStatement("SELECT option_id,title FROM list_options WHERE list_id='F2F_Config_Diagnosis_Primary'");
            			
        ?>        
        <select id="sel_diagnosis" name="sel_diagnosis" 
                onchange="javascript:showSecondaryDiagnosis(this.value,'<?php echo $mode;?>','<?php echo $_REQUEST['id'];?>');
                                     showConfigByDiagnosis(this.value,'<?php echo $mode;?>','<?php echo $_REQUEST['id'];?>');">
			<?php 
			
			echo '<option value="0">Select Diagnosis</option>';	
			while($resDiagnosis=  sqlFetchArray($getDiagnosis))
			{
				if($resDiagnosis['option_id']==$resEncounterDetails['pri_diagnosis_id'])
				{
					$selected='selected';
				}
				else
				{
					$selected='';
				}
				echo '<option value='.$resDiagnosis['option_id'].' '.$selected.'>'.$resDiagnosis['title'].'</option>';
			}
			?>
						
	</select>
    </div>
      -->
      
      <br>
    <div id="div_secondary_diagnosis"></div>    
      <br>
    <div id="div_form_facetoface"></div>  
            
   </div>
      
</div>
      
    <input type="hidden" name="texens" id="texens" value="" >
</form>
</body>
</html>
