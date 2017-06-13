<?php
//echo "here";
require_once("dbConnect.php");

$nextVisitDays=($_POST['radDays']!='')?$_POST['radDays']:$_POST['txtOther'];
     
$insert_form_FacetoFace_Sql ="INSERT INTO tbl_form_facetoface(pid,encounter,is_home_bound,is_hhc_needed,other_physician,
    is_house_visit_needed,medical_condition,necessary_hhs,nursing,physical_therapy,occupational_therapy,speech,support_service_reason,
    patient_homebound_reason,nurse_practitioner_signature,nurse_practitioner_signature_date,physician_signature,
    printed_name,printed_name_date,created_date,date_of_service)
                              VALUES(".$_POST['btnpid'].",".$_POST['txtVisitDate'].",
                                    '".$_POST['radBound']."','".$_POST['radCare']."','".$_POST['radPhysician']."',
                                    '".$_POST['radVisit']."','".$_POST['txtMedical']."','$nextVisitDays',
                                    '".$_POST['txtNursing']."','".$_POST['txtPhysical']."',
                                    '".$_POST['txtOccupational']."','".$_POST['txtSpeech']."',
                                    '".$_POST['txtTreatment']."','".$_POST['txtHomeBound']."',
                                    '".$_POST['txtNurse']."','".$_POST['txtNursePractitionerSignDate']."',
                                    '".$_POST['txtPhysicianSignature']."','".$_POST['txtPrintedName']."',
                                    '".$_POST['txtPrintedDate']."',now(),".$_POST['txtVisitDate']."
                                    )";

$insert_form_FacetoFace_Res=mysql_query($insert_form_FacetoFace_Sql);

echo "Data Saved Successfully";
?>