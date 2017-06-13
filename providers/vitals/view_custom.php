<!DOCTYPE html>
<html lang="en">
    <head>
        <meta content="width=device-width,initial-scale=1.0" name="viewport">
    </head>
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

require ("C_FormVitals_custom.class.php");


$encounter      = $_REQUEST['encounter'];
$pid1           = $_REQUEST['pid'];
$id             = $_REQUEST['id'];
$provider       = $_REQUEST['provider'];
$location       = $_REQUEST['location'];
$isSingleView   = $_REQUEST['isSingleView'];
$isFromCharts   = $_REQUEST['isFromCharts'];
$stat           = explode("|",$_REQUEST['status']);

if(in_array('pending',$stat)){
    $pending='pending';
}
if(in_array('finalized',$stat)){
    $finalized='finalized';    
}
$sql_pname=sqlStatement("select CONCAT(lname,' ',fname) AS pname from  patient_data  where   pid=$pid1");
$res_row1=sqlFetchArray($sql_pname);
$dos_sql=sqlStatement("select * from form_encounter where encounter=$encounter");
$res_dos=sqlFetchArray($dos_sql);
$dos=explode(" ",$res_dos['date']);

$cat=sqlStatement("select * from openemr_postcalendar_categories where pc_catid='".$res_dos['pc_catid']."'");
$res_cat=sqlFetchArray($cat);
echo "<table>";
echo "<tr><td><b>Patient Name: </b>".$res_row1['pname']."</td><td>&nbsp;</td><td><b>Encounter: </b>".$encounter."</td></tr>";
echo "<tr><td><b>Date Of Service: </b>".$dos[0]."</td><td>&nbsp;</td><td><b>Visit Category: </b>".$res_cat['pc_catname']."</td></tr>";
echo "</table>";
                
$c = new C_FormVitals_custom();
echo $c->default_action_custom($id,$pid1,$encounter,$provider,$location,$pending,$finalized,$isSingleView,$isFromCharts);
?>

