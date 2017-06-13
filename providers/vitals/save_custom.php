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
$c->default_action_process_custom($_POST);
//print_r($_POST);

if(!empty($_POST['finalized']) && !empty($_POST['pending'])){
    $finalized  = $_POST['finalized'];
    $pending    = $_POST['pending'];
    $value      = $finalized[0].'|'. $pending[0];
   
}else if(!empty($_POST['finalized'])){
    $finalized  = $_POST['finalized'];
    $value      = $finalized[0];
   
}else if(!empty($_POST['pending'])){
    $pending    = $_POST['pending'];
    $value      = $pending[0];
   
   
}
$provider       = $_POST['provider'];
echo $resenc1        = $_REQUEST['encounter1']; 
$respid1        = $_POST['pid2'];
$isSingleView   = $_REQUEST['isSingleView'];
$isFromCharts   = $_REQUEST['isFromCharts'];

$result     = mysql_query("SELECT form_id FROM forms WHERE `form_name` = 'Vitals' AND `pid` =  ".$respid1 ." AND encounter = $resenc1 AND deleted = 0");
if(mysql_num_rows($result) > 0){
    $result1 = sqlFetchArray($result);
    $newformid = $result1['form_id'];
}

 $res12 = sqlstatement("select form_id  from forms where form_name ='Allcare Encounter Forms' AND encounter='$resenc1' AND pid='$respid1' AND deleted=0 order by id desc");
 $frow_res = sqlFetchArray($res12);
 if(!empty($frow_res)){
    $formid    = $frow_res['form_id'];
    $res1  = sqlstatement("select * from lbf_data lb INNER JOIN layout_options l ON l.field_id=lb.field_id where lb.form_id='$formid' AND l.form_id='LBF2' AND l.group_name LIKE '%Vitals%' AND lb.field_id LIKE '%_stat%' order by seq");
    $res_row1 = sqlFetchArray($res1);
    if(!empty($res_row1)){
       $update = sqlStatement("UPDATE lbf_data SET  `field_value` = ? WHERE  field_id= ? AND form_id = ?",array($value,'vitals_stat',$formid));
    }else{
       sqlInsert("INSERT INTO lbf_data (form_id,field_id,field_value) VALUES (?,?,?)", array($formid,'vitals_stat',$value));
    }
 }else{
    if($_POST['pending']!='' || $_POST['finalized']!=''){
        $sql_form   = sqlStatement("select max(form_id)as new_form from forms where form_name='Allcare Encounter Forms' AND formdir='LBF2'");
        $row_form   = sqlFetchArray($sql_form);
        $new_fid    = $row_form['new_form'];
        $new_id1    = ++$new_fid;
        //echo "INSERT INTO  forms (date,encounter,form_name,form_id,pid,user,groupname,authorized,deleted,formdir) VALUES (NOW(),$ecounter1,'Allcare Encounter Forms',$new_id1,$pid1,'$_SESSION[authUser]','default',1,0,'LBF2')";
        $ins_form   = sqlStatement("INSERT INTO  forms (date,encounter,form_name,form_id,pid,user,groupname,authorized,deleted,formdir) VALUES (NOW(),$resenc1,'Allcare Encounter Forms',$new_id1,$respid1,'$provider','default',1,0,'LBF2')");
        $row1_form  = sqlFetchArray($ins_form);
        $res1       = sqlstatement("select * from lbf_data lb INNER JOIN layout_options l ON l.field_id=lb.field_id where lb.form_id='$new_id1' AND l.form_id='LBF2' AND l.group_name LIKE '%Vitals%' AND lb.field_id LIKE '%_stat%' order by seq");
        $res_row1   = sqlFetchArray($res1);
        if(!empty($res_row1)){
            $update    = sqlStatement("UPDATE lbf_data SET  `field_value` = ? WHERE  field_id= ? AND form_id = ?",array("$value",'vitals_stat',$new_id1));
        }else{
            sqlInsert("INSERT INTO lbf_data (form_id,field_id,field_value) VALUES (?,?,?)", array($new_id1,'vitals_stat',"$value"));
       }
    }
 }
//@formJump();
if($formid == 0 || $formid == '')
    $formid = $new_id1;

if($_POST['location']=='provider_portal'){
    if($isSingleView == 1 && $isFromCharts == 0)
        echo "<script> window.close();window.opener.location.href = '../single_view_form.php?encounter=$resenc1&pid=$respid1';</script>";
    else if($isSingleView == 1 && $isFromCharts == 1)
        echo "<script>window.opener.datafromchildwindow($newformid,$formid,'$value');window.close();</script>";
    else
        echo "<script>window.close();
        window.opener.location.href = '../../../providers/provider_incomplete_charts.php?checkencounter=$resenc1';</script>";
}
?>
