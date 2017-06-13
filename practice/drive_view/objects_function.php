<?php

//STOP FAKE REGISTER GLOBALS
$fake_register_globals=false;

$ignoreAuth=true; // ignore the standard authentication for a regular OpenEMR user
include_once('../../interface/globals.php');
//for patient
function patient_details($id){
    $psql=sqlStatement("select * from patient_data where pid=".$id);
    $prow=sqlFetchArray($psql);
    $obj_name = $prow['lname'];
    if ($prow['fname']) {
        $obj_name .= ", " . $prow['fname'];
    }
    return  $obj_name;   
}

//for user
function user_details($uid){
    $assign_user=sqlStatement("select * from users where id=$uid");
    $assign_row=sqlFetchArray($assign_user);
    $assign_name = $assign_row['lname'];
    if ($assign_row['fname']) {
        $assign_name .= ", " . $assign_row['fname'];
    }
    return $assign_name;
}

//for insurance
function insurance_details($iid){
    $isql=sqlStatement("select * from insurance_companies  where id=".$iid);
    $irow=sqlFetchArray($isql);
    return $irow['name'];
}

//for pharmacy
function pharmacy_details($phid){
    $phsql=sqlStatement("select * from pharmacies  where id=".$phid);
    $phrow=sqlFetchArray($phsql);
    return $phrow['name'];
}

//for agency
function agency_details($aid){
    $asql=sqlStatement("SELECT *
                        FROM users AS u
                        LEFT JOIN list_options AS lo ON list_id =  'abook_type'
                        AND option_id = u.abook_type where id=$aid and active=1 and authorized=1");
    $arow=sqlFetchArray($asql);
    $obj_name = $arow['lname'];
    if ($arow['fname']) {
        $obj_name .= ", " . $arow['fname'];
    }
    return $obj_name;
}

//for facility
function facility_details($fid){
    $facsql=sqlStatement("select * from facility  where id=".$fid);
    $facrow=sqlFetchArray($facsql);
    return $facrow['name'];
}
?>