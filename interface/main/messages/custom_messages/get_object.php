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

//SANITIZE ALL ESCAPES
$sanitize_all_escapes=true;

//STOP FAKE REGISTER GLOBALS
$fake_register_globals=false;

require_once("../../../globals.php");

$type=$_REQUEST['type'];
if($type=='patients'){
//    echo "<label>Patient:</label><input type='text' name='patient' id='patient' value='Click to select' onclick='sel_obj(this);'/>";
    $sql=sqlStatement("select * from patient_data");
    while($urow=sqlFetchArray($sql)) {
        $arr=''; $arr=array(); $str='';
        $id=$urow['pid'];
       
        $arr_user[$id]=$urow['fname'].",".$urow['lname'];
    }
    echo json_encode($arr_user);
    
    
}elseif($type=='facility'){
    $sql=sqlStatement("select * from facility");
    while($urow=sqlFetchArray($sql)) {
        $arr=''; $arr=array(); $str='';
        $id=$urow['id'];
       
        $arr_user[$id]=$urow['name'];
    }
    echo json_encode($arr_user);
    //echo "<label>Facility:</label><input type='text' name='facility' id='facility' value='Click to select' onclick='sel_obj(this);'/>";
}elseif($type=='insurance'){
    $sql=sqlStatement("select * from insurance_companies");
    while($urow=sqlFetchArray($sql)) {
        $arr=''; $arr=array(); $str='';
        $id=$urow['id'];
       
        $arr_user[$id]=$urow['name'];
    }
    echo json_encode($arr_user);
   // echo "<label>Insurance:</label><input type='text' name='insurance' id='insurance' value='Click to select' onclick='sel_obj(this);'/>";
}elseif($type=='pharmacy'){
    $sql=sqlStatement("select * from pharmacies");
    while($urow=sqlFetchArray($sql)) {
        $arr=''; $arr=array(); $str='';
        $id=$urow['id'];
       
        $arr_user[$id]=$urow['name'];
    }
    echo json_encode($arr_user);
   // echo "<label>Pharmacy:</label><input type='text' name='pharmacy' id='pharmacy' value='Click to select' onclick='sel_obj(this);'/>";
}elseif($type=='users'){
    $sql=sqlStatement("select * from users");
    while($urow=sqlFetchArray($sql)) {
        $arr=''; $arr=array(); $str='';
        $id=$urow['id'];
       
        $arr_user[$id]=$urow['fname'].",".$urow['lname'];
    }
    echo json_encode($arr_user);
    //echo "<label>Users:</label><input type='text' name='users' id='users' value='Click to select' onclick='sel_obj(this);'/>";
}elseif($type=='address_Book'){
    $fsql=sqlStatement("SELECT u.*
                        FROM users AS u
                        LEFT JOIN list_options AS lo ON list_id =  'abook_type'
                        AND option_id = u.abook_type where  active=1 and authorized=1 and fname!='' and lname!=''");
                       
    while($urow=sqlFetchArray($fsql)) {
        $arr=''; $arr=array(); $str='';
        $id=$urow['id'];
       
        $arr_user[$id]=$urow['fname'].",".$urow['lname'];
    }
    echo json_encode($arr_user);
    
   // echo "<label>Agencies:</label><input type='text' name='address_Book' id='address_Book' value='Click to select' onclick='sel_obj(this);'/>";
}
      