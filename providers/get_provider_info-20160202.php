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
    
    //checking whether the request comes from index.php
        if (!isset($_SESSION['itsme'])) {
                session_destroy();
		header('Location: '.$landingpage.'&w');
		exit;
	}
    //

    //some validation
        if (!isset($_POST['uname']) || empty($_POST['uname'])) {
                session_destroy();
		header('Location: '.$landingpage.'&w&c');
		exit;
	}
        if (!isset($_POST['pass']) || empty($_POST['pass'])) {
                session_destroy();
                header('Location: '.$landingpage.'&w&c');
		exit;
        }
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
  
 $_SESSION['password_update'];
   $password_update=isset($_SESSION['password_update']);
    unset($_SESSION['password_update']);
    $plain_code= $_POST['pass'];
    // set the language
    if (!empty($_POST['languageChoice'])) {
            $_SESSION['language_choice'] = $_POST['languageChoice'];
    }
    else if (empty($_SESSION['language_choice'])) {
            // just in case both are empty, then use english
            $_SESSION['language_choice'] = 1;
    }
    else {
            // keep the current session language token
    }

    $authorizedPortal=false; //flag
   
      $sql=sqlStatement("SELECT id, fname, lname, specialty FROM users " .
      "WHERE active = 1 AND ( info IS NULL OR info NOT LIKE '%Inactive%' ) " .
      "AND authorized = 1 AND username='".$_POST['uname']."'" .
      "ORDER BY lname, fname");
$id=sqlFetchArray($sql);
$id1=$id['id'];

if(empty($id)){
    session_destroy();
                header('Location: '.$landingpage.'&w&c');
		exit;
}       
        
          $_SESSION['portal_username']=$_POST['uname'];
   

   

   require_once('home.php');


?>
