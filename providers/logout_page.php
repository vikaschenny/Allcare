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

    
    if ( isset($_SESSION['portal_username']) ) {    
        $provider = $_SESSION['portal_username'];
    }
    else {
           $provider=$_REQUEST['provider'];
           $refer=$_REQUEST['refer'];
           $_SESSION['portal_username']=$_REQUEST['provider'];
           $_SESSION['refer']=$_REQUEST['refer'];
    }
    //

 $ignoreAuth=true;
 require_once('../interface/globals.php');
$_REQUEST['provider'];
if($_SESSION['refer']!=''){
    $ref=$_SESSION['refer'];
}else {
    $ref=$_REQUEST['provider'];
}
$date=date('Y/m/d H:i:s');
$ins=sqlStatement("insert into tbl_provider_portal_logs (date,provider,refers,action) values ('$date','".$_REQUEST['provider']."','".$ref."','logout')");
session_destroy();
header('Location: ../providers/index.php?site=default'); 



?>
