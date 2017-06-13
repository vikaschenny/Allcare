<?php
 // Copyright (C) 2011 Cassian LUP <cassi.lup@gmail.com>
 //
 // This program is free software; you can redistribute it and/or
 // modify it under the terms of the GNU General Public License
 // as published by the Free Software Foundation; either version 2
 // of the License, or (at your option) any later version.

    //setting the session & other config options
    session_start();

    //don't require standard openemr authorization in globals.php
    $ignoreAuth = 1;

    //SANITIZE ALL ESCAPES
    $fake_register_globals=false;

    //STOP FAKE REGISTER GLOBALS
    $sanitize_all_escapes=true;

    //For redirect if the site on session does not match
    $landingpage = "index.php?site=".$_GET['site'];

    //includes
    require_once('../interface/globals.php');

    ini_set("error_log",E_ERROR || ~E_NOTICE);

    // security measure -- will check on next page.
    $_SESSION['itsme'] = 1;
    //
  
?>
<html>
<head>
<?php html_header_show();?>
 <meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="assets/css/customizealerts.css">
<script type="text/javascript" src="assets/js/jquery.min.js"></script>
<script type="text/javascript" src="assets/js/bootstrap.min.js"></script>
<script language='JavaScript'>
function process() {   
    if (!(validate())) {
        $('#loginform fieldset').prepend('<div class="alert alert-error"><a class="close" data-dismiss="alert">&times;</a><strong>Error! </strong> Field(s) are missing!</div>');
        return false;
    }
 }
        
function validate() {
    var pass=true;            
    if (document.getElementById('uname').value == "") {
        document.getElementById('uname').style.border = "1px solid red";
        pass=false;
    }
    if (document.getElementById('pass').value == "") {
        document.getElementById('pass').style.border = "1px solid red";
        pass=false;
    }
    return pass;
}
       
</script>
<style>
body {
  background: #46a1b4;
}

</style>
</head>
<body  bgcolor="#638fd0" >

          <?php if($_REQUEST['error']=='access_denied'){   ?>
             <script type="text/javascript">
            window.location.href = "https://"+'<?php echo $_SERVER['SERVER_NAME'] ."/practice"; ?>'
           </script>
          <?php } else { include 'sso_ggl.php'; }?>
</body>
</html> 
