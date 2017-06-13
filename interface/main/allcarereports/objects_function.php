<?php
/**
 * Copyright (C) 2010 OpenEMR Support LLC 
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * 2013/02/08 Minor tweaks by EMR Direct to allow integration with Direct messaging
 * 2013-03-27 by sunsetsystems: Fixed some weirdness with assigning a message recipient,
 *   and allowing a message to be closed with a new note appended and no recipient.
 */
////SANITIZE ALL ESCAPES
//$sanitize_all_escapes=true;
//
////STOP FAKE REGISTER GLOBALS
//$fake_register_globals=false;
//
//require_once("../../globals.php");

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
function user_byname($uname){
    $assign_user=sqlStatement("select * from users where username='$uname'");
    $assign_row=sqlFetchArray($assign_user);
    $assign_name = $assign_row['lname'];
    if ($assign_row['fname']) {
        $assign_name .= ", " . $assign_row['fname'];
    }
    return $assign_name;
}
?>