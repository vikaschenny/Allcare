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

//print_r($_POST);//die;
$tests=implode(',',$_POST['chkTests']);

if($_POST['hdnExists']==1)
{        

    $update_LabRequest_Config_Sql= "UPDATE tbl_form_lab_requisition_configuration
    SET date_of_request='".$_POST['date_date_of_request']."',
        stat='".$_POST['rd_stat']."',
        specimen_week='".$_POST['txt_specimen_week']."',
        fasting='".$_POST['rd_fasting']."',
        frail_health='".$_POST['rd_frail_health']."',
        is_home_bound='".$_POST['rd_is_home_bound']."',
        is_preference_home_health='".$_POST['rd_is_preference_home_health']."',
        diagnosis_codes='".$_POST['txt_diagnosis_codes']."',
        tests='".$tests."',
        is_colonoscopy_required='".$_POST['rd_is_colonoscopy_required']."',
        patient_has='".$_POST['rdARC']."',
        updated_date=now()";
    
    mysql_query($update_LabRequest_Config_Sql) or die(mysql_error());
    
    echo "Updated successfully";
    
}

else if($_POST['hdnExists']==0)
{
    
    $insert_LabRequest_Config_Sql="INSERT INTO tbl_form_lab_requisition_configuration(date_of_request,stat,
specimen_week,fasting,frail_health,is_home_bound,is_preference_home_health,
diagnosis_codes,tests,is_colonoscopy_required,patient_has,created_date)

    VALUES('".$_POST['date_date_of_request']."','".$_POST['rd_stat']."',
           '".$_POST['txt_specimen_week']."','".$_POST['rd_fasting']."','".$_POST['rd_frail_health']."',
          '".$_POST['rd_is_home_bound']."',
          '".$_POST['rd_is_preference_home_health']."',
          '".$_POST['txt_diagnosis_codes']."','".$tests."',
          '".$_POST['rd_is_colonoscopy_required']."',
          '".$_POST['rdARC']."',now())";

     mysql_query($insert_LabRequest_Config_Sql) or die(mysql_error());
     
     echo "Inserted successfully";
    
}

?>
