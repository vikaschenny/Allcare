<!DOCTYPE html>
<html lang="en">
<?php
 // Copyright (C) 2011 Cassian LUP <cassi.lup@gmail.com>
 // 
 // Moved out of individual get_* portal functions for re-use by
 // Kevin Yeh (kevin.y@integralemr.com) May 2013
 //
 // This program is free software; you can redistribute it and/or
 // modify it under the terms of the GNU General Public License
 // as published by the Free Software Foundation; either version 2
 // of the License, or (at your option) any later version.
 // 
 // 
 
    // All of the common intialization steps for the get_* patient portal functions are now in this single include.

    //SANITIZE ALL ESCAPES
    $sanitize_all_escapes=true;

    //STOP FAKE REGISTER GLOBALS
    $fake_register_globals=false;

    //continue session
    session_start();

    //landing page definition -- where to go if something goes wrong
    $landingpage = "index.php?site=".$_SESSION['site_id'];	
    //

    // kick out if patient not authenticated
    //if ( isset($_SESSION['uid']) && isset($_SESSION['patient_portal_onsite']) ) {
    if ( isset($_SESSION['portal_username']) ) {    
    $provider = $_SESSION['portal_username'];
    }
    else {
            session_destroy();
    header('Location: '.$landingpage.'&w');
            exit;
    }
    //

    $ignoreAuth=true; // ignore the standard authentication for a regular OpenEMR user
include_once('../../interface/globals.php');
include_once("$srcdir/api.inc");

require ("allcare_C_FormROS_custom.class.php");

$encounter=$_REQUEST['encounter'];
$pid1=$_REQUEST['pid'];
$location=$_REQUEST['location'] ? $_REQUEST['location']:0;
$provider=$_REQUEST['provider'] ? $_REQUEST['provider']:0;
 //$id=$_REQUEST['id'];
 if($location=='provider_portal'){ ?>
<meta content="width=device-width,initial-scale=1.0" name="viewport">
<style> .body{ background-color: #F6F6F6 !important; } </style>
<link rel="stylesheet" href="../../tableresponsive/viewcustom_responsive.css"/>
<?php }
$sql_pname=sqlStatement("select CONCAT(lname,' ',fname) AS pname from  patient_data  where   pid=$pid1");
                $res_row1=sqlFetchArray($sql_pname);
                echo "<b>Patient Name: </b>".$res_row1['pname']."<br>";
                echo "<b>Encounter: </b>".$encounter;
                
$c = new allcare_C_FormROS1();
echo $c->view_action1($_REQUEST['id'],$pid1,$location,$provider);
?>
