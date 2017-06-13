<?php

require_once("../globals.php");
require_once("$srcdir/acl.inc");
require_once("$srcdir/formdata.inc.php");
require_once("$srcdir/globals.inc.php");
require_once("$srcdir/user.inc");
require_once("$srcdir/classes/CouchDB.class.php");

//echo "<script>alert('HE=='+".$_POST['hdnExists'].");</script>";

$val_sec_diagnosis_id='';
$val_txt_sec_diagnosis='';
if($_POST['hdnConfigDiagnosis']==1)
{
    $val_txt_sec_diagnosis=$_POST['valConfigSecDiagnosis'];
}
else if($_POST['hdnConfigDiagnosis']>1)
{
    $val_sec_diagnosis_id=$_POST['valConfigSecDiagnosis'];
}


if($_POST['hdnExists']==1)
{                    
    $update_FacetoFace_Config_Sql= "UPDATE tbl_form_facetoface_configuration
                                    SET 
                                    sec_diagnosis_id='$val_sec_diagnosis_id',
                                    txt_sec_diagnosis='$val_txt_sec_diagnosis',
                                date_of_service='".$_POST['date_date_of_service']."',
                                        is_home_bound='".$_POST['rd_is_home_bound']."',
                                        is_hhc_needed='".$_POST['rd_is_hhc_needed']."',
                                        other_physician='".$_POST['rd_other_physician']."',
                                        is_house_visit_needed='".$_POST['rd_is_house_visit_needed']."',
                                        medical_condition='".$_POST['txt_medical_condition']."',
                                        necessary_hhs='".$_POST['txt_necessary_hhs']."',
                                        nursing='".$_POST['txt_nursing']."',
                                        physical_therapy='".$_POST['txt_physical_therapy']."',
                                        occupational_therapy='".$_POST['txt_occupational_therapy']."',
                                        speech='".$_POST['txt_speech']."',
                                        care_treatment='".$_POST['txt_care_treatment']."',
                                        support_service_reason='".$_POST['txt_support_service_reason']."',
                                        patient_homebound_reason='".$_POST['txt_patient_homebound_reason']."',
                                        nurse_practitioner_signature='".$_POST['txt_nurse_practitioner_signature']."',
                                        nurse_practitioner_signature_date='".$_POST['date_nurse_practitioner_signature_date']."',
                                        physician_signature='".$_POST['txt_physician_signature']."',
                                        printed_name='".$_POST['txt_printed_name']."',
                                        printed_name_date='".$_POST['date_printed_name_date']."',
                                        updated_date=now()
                                   WHERE pri_diagnosis_id='".$_POST['hdnConfigDiagnosis']."'";
    
    mysql_query($update_FacetoFace_Config_Sql) or die(mysql_error());
    
    echo "Updated successfully";
    
}

else if($_POST['hdnExists']==0)
{
            
    $insert_FacetoFace_Config_Sql="INSERT INTO tbl_form_facetoface_configuration(pri_diagnosis_id,
                                    sec_diagnosis_id,txt_sec_diagnosis,
date_of_service,is_home_bound,is_hhc_needed,other_physician,
is_house_visit_needed,medical_condition,necessary_hhs,nursing,physical_therapy,occupational_therapy,speech,care_treatment,
support_service_reason,patient_homebound_reason,nurse_practitioner_signature,nurse_practitioner_signature_date,physician_signature,
printed_name,printed_name_date,created_date)
                      VALUES('".$_POST['hdnConfigDiagnosis']."',
                          '$val_sec_diagnosis_id','$val_txt_sec_diagnosis',

                            '".$_POST['date_date_of_service']."','".$_POST['rd_is_home_bound']."',
                            '".$_POST['rd_is_hhc_needed']."','".$_POST['rd_other_physician']."',
                            '".$_POST['rd_is_house_visit_needed']."','".$_POST['txt_medical_condition']."',
                            '".$_POST['txt_necessary_hhs']."','".$_POST['txt_nursing']."',
                            '".$_POST['txt_physical_therapy']."','".$_POST['txt_occupational_therapy']."',
                            '".$_POST['txt_speech']."','".$_POST['txt_care_treatment']."',
                            '".$_POST['txt_support_service_reason']."','".$_POST['txt_patient_homebound_reason']."',
                            '".$_POST['txt_nurse_practitioner_signature']."','".$_POST['date_nurse_practitioner_signature_date']."',
                            '".$_POST['txt_physician_signature']."','".$_POST['txt_printed_name']."',
                            '".$_POST['date_printed_name_date']."',
                            now())";

     mysql_query($insert_FacetoFace_Config_Sql) or die(mysql_error());
     
     echo "Inserted successfully";
    
}

?>
