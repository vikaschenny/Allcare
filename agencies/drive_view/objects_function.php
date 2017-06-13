<?php
include("../session_file.php"); 

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

/************************For messaging screen ********************************************/
//for assigned to refer user_details function

//for message priority
function message_priority($prid){
    $sqlpr=sqlStatement("select * from list_options where list_id='AllcareCustomMsgPriority' and option_id='$prid'");
    $pdata=sqlFetchArray($sqlpr);
    return $pdata['title'];
}

//for message status
function message_status($msid){
    $ssql=sqlStatement("select * from list_options where list_id='AllcareCustomMsgStatus' and option_id='$msid'");
    $sdata=sqlFetchArray($ssql);
    return $sdata['option_id'];
}

//for object type
function object_type($otid){
    $osql=sqlStatement("select * from list_options where list_id='AllcareObjects' and option_id='$otid'");
    $odata=sqlFetchArray($osql);
    return $odata['title'];
}
//for message assignment
function message_assign($uname){
    $assign_user=sqlStatement("select * from users where username='$uname'");
    $assign_row=sqlFetchArray($assign_user);
    $assign_name = $assign_row['lname'];
    if ($assign_row['fname']) {
        $assign_name .= ", " . $assign_row['fname'];
    }
    return $assign_name;
}
?>