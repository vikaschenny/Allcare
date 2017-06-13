<?php

/*
echo "<br>post 55 ";
        print_r($_POST);
        echo "<br>post 77 ";die;*/

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

$c = new allcare_C_FormROS1();
echo $c->default_action_process1($_POST); 
//print_r($_POST);


save_form_flag();
//@formJump();
if($_POST['provider']!=''){
    $provider=$_POST['provider'];

     echo "<script>window.close();

    window.opener.location.href = '../provider_incomplete_charts.php?provider=$provider';</script>";

}else {
     $provider=$_POST['provider1'];
    
     echo "<script>window.close();

    window.opener.location.href = '../provider_incomplete_charts.php?provider=$provider';</script>";

}


function save_form_flag(){
    
    if(!empty($_REQUEST['id']) ){
      $formid = $_REQUEST['id']; 
    }else{
        $result = mysql_query("SELECT id FROM tbl_form_allcare_ros ORDER BY id DESC LIMIT 1");
        while ($row = mysql_fetch_array($result,MYSQL_ASSOC)){
            $formid =  $row['id']; 
        } 
    }
    $logdata= array();
    $data = mysql_query("SELECT logdate from `tbl_allcare_formflag` WHERE  form_id=".$formid);
    while ($row = mysql_fetch_array($data,MYSQL_ASSOC)) {
        $array =  unserialize($row['logdate']);
        $count= count($array);
    }
    $count = isset($count)? $count: 0;
    $pending = $_POST['pending'];
    $finalized = $_POST['finalized'];
    
    $array=array();
    $ip_addr=GetIP();
    if(empty($_REQUEST['id'])):
            $array2[] = array( 'authuser' =>$_POST['provider'],'pending' => $pending,'finalized' => $finalized, 'date' => date("Y/m/d"), 'action'=>'created','ip_address'=>$ip_addr ,'count'=> $count+1);
            $logdata = array_merge_recursive($array, $array2);
            $logdata= ($logdata? serialize($logdata): serialize($array2) );
            sqlInsert("INSERT into `tbl_allcare_formflag`(`form_id`, `encounter_id`, `form_name`, `pending`,`finalized`,`logdate`) VALUES(".$formid.",".$_POST['encounter'].",'Allcare Review of Systems','".$pending."','".$finalized."','".$logdata."')");
    else: 
            $result = mysql_query("SELECT * FROM tbl_allcare_formflag WHERE `form_name` = 'Allcare Review of Systems' AND `form_id` =  ".$formid);
            if(mysql_num_rows($result) > 0){
                    $array2[] = array( 'authuser' =>$_POST['provider'],'pending' => $pending,'finalized' => $finalized, 'date' => date("Y/m/d"), 'action' => 'updated','ip_address'=>$ip_addr ,'count'=> $count+1);
                    $logdata = array_merge_recursive($array, $array2);
                    $logdata= ($logdata? serialize($logdata): serialize($array2) );
                    sqlInsert("UPDATE `tbl_allcare_formflag` SET `finalized`='$finalized',
                    `pending` = '$pending',`logdate` ='".$logdata."'  WHERE `form_name` = 'Allcare Review of Systems' AND `form_id` =  ".$formid);
            }else{ 
                    $array2[] = array( 'authuser' =>$_POST['provider'],'pending' => $pending,'finalized' => $finalized, 'date' => date("Y/m/d"), 'action'=>'created','ip_address'=>$ip_addr ,'count'=> $count+1);
                    $logdata = array_merge_recursive($array, $array2);
                    $logdata= ($logdata? serialize($logdata): serialize($array2) );
                    sqlInsert("INSERT into `tbl_allcare_formflag`(`form_id`, `encounter_id`, `form_name`, `pending`,`finalized`,`logdate`) VALUES(".$formid.",".$_POST['encounter'].",'Allcare Review of Systems','".$pending."','".$finalized."','".$logdata."')");
            }
    endif;
}

function GetIP()
{
    if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown"))
        $ip = getenv("HTTP_CLIENT_IP");
    else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown"))
        $ip = getenv("HTTP_X_FORWARDED_FOR");
    else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown"))
        $ip = getenv("REMOTE_ADDR");
    else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown"))
        $ip = $_SERVER['REMOTE_ADDR'];
    else
        $ip = "unknown";
    return($ip);
}

?>
