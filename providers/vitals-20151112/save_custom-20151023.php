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
$c = new C_FormVitals_custom();
echo $c->default_action_process_custom($_POST);
//print_r($_POST);

if(!empty($_POST['finalized']) && !empty($_POST['pending'])){
    $finalized=$_POST['finalized'];
    $pending=$_POST['pending'];
    $value=$finalized[0].'|'. $pending[0];
   
}else if(!empty($_POST['finalized'])){
    $finalized=$_POST['finalized'];
    $value=$finalized[0];
   
}else if(!empty($_POST['pending'])){
    $pending=$_POST['pending'];
    $value=$pending[0];
   
   
}
$resenc1=$_POST['encounter1'];
$respid1=$_POST['pid2'];
 $res12=sqlstatement("select form_id  from forms where form_name ='Allcare Encounter Forms' AND encounter='$resenc1' AND pid='$respid1' AND deleted=0 order by id desc");
 $frow_res = sqlFetchArray($res12);
 $formid=$frow_res['form_id'];
 $res1=sqlstatement("select * from lbf_data lb INNER JOIN layout_options l ON l.field_id=lb.field_id where lb.form_id='$formid' AND l.form_id='LBF2' AND l.group_name LIKE '%Vitals%' AND lb.field_id LIKE '%_stat%' order by seq");
 $res_row1=sqlFetchArray($res1);
 if(!empty($res_row1)){
     $update=sqlStatement("UPDATE lbf_data SET  `field_value` = ? WHERE  field_id= ? AND form_id = ?",array($value,'vitals_stat',$formid));
 }else{
     sqlInsert("INSERT INTO lbf_data (form_id,field_id,field_value) VALUES (?,?,?)", array($formid,'vitals_stat',$value));
 }
//@formJump();
$provider=$_POST['provider'];
if($_POST['location']=='provider_portal'){
    echo "<script>window.close();

    window.opener.location.href = '../../../providers/provider_incomplete_charts.php?provider=$provider';</script>";
}else{
    echo '<script>  window.location.href = "../../reports/incomplete_charts.php"; </script>';
}

?>
