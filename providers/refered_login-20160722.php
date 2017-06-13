<?php
 // Copyright (C) 2011 Cassian LUP <cassi.lup@gmail.com>
 //
 // This program is free software; you can redistribute it and/or
 // modify it under the terms of the GNU General Public License
 // as published by the Free Software Foundation; either version 2
 // of the License, or (at your option) any later version.

//starting the PHP session (also regenerating the session id to avoid session fixation attacks)
    session_start();
    session_regenerate_id(true);
//

//landing page definition -- where to go if something goes wrong
    $landingpage = "index.php?site=".$_SESSION['site_id'];
//



//

//SANITIZE ALL ESCAPES
$fake_register_globals=false;

//STOP FAKE REGISTER GLOBALS
$sanitize_all_escapes=true;

//Settings that will override globals.php
    $ignoreAuth = 1;
//

//Authentication (and language setting)
require_once('../interface/globals.php');
require_once("$srcdir/authentication/common_operations.php");        
require_once("$srcdir/authentication/login_operations.php");   

    //checking whether the request comes from index.php
    if (!isset($_SESSION['itsme'])) {
            session_destroy();
            header('Location: '.$landingpage.'&w');
            exit;
    }

$authorizedPortal=false; //flag


   
if($_POST['pro_sel']=='1') {
     
        $_SESSION['portal_username']=$_POST['provider'];
        $_SESSION['refer']=$_POST['refer']; 
        $date=date('Y/m/d H:i:s');
        $ins=sqlStatement("insert into tbl_provider_portal_logs (date,provider,refers,action) values ('$date','".$_POST['provider']."','".$_POST['refer']."','login')");
        require_once('home.php'); 
     
}   
?>
