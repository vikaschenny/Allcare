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
    //exit if portal is turned off
    if ( !(isset($GLOBALS['portal_onsite_enable'])) || !($GLOBALS['portal_onsite_enable']) ) {
      echo htmlspecialchars( xl('Agency Portal is turned off'), ENT_NOQUOTES);
      exit;
    }

    // security measure -- will check on next page.
    $_SESSION['itsme'] = 1;
    //
  
?>

<html>
<head>
    <title><?php echo xlt('Provider Portal Login'); ?></title>

    <script type="text/javascript" src="../library/js/jquery-1.5.js"></script>
    <script type="text/javascript" src="../library/js/jquery.gritter.min.js"></script>

    <link rel="stylesheet" type="text/css" href="css/jquery.gritter.css" />
    <link rel="stylesheet" type="text/css" href="css/base.css" />

    <script type="text/javascript">
        function process() {
            
            if (!(validate())) {
                alert ('<?php echo addslashes( xl('Field(s) are missing!') ); ?>');
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
        function process_new_pass() {

            if (!(validate_new_pass())) {
                alert ('<?php echo addslashes( xl('Field(s) are missing!') ); ?>');
                return false;
            }
            if (document.getElementById('pass_new').value != document.getElementById('pass_new_confirm').value) {
                alert ('<?php echo addslashes( xl('The new password fields are not the same.') ); ?>');
                return false;
            }
            if (document.getElementById('pass').value == document.getElementById('pass_new').value) {
                alert ('<?php echo addslashes( xl('The new password can not be the same as the current password.') ); ?>');
                return false;
            }
        }

        function validate_new_pass() {
            var pass=true;
            if (document.getElementById('uname').value == "") {
                document.getElementById('uname').style.border = "1px solid red";
                pass=false;
            }
            if (document.getElementById('pass').value == "") {
                document.getElementById('pass').style.border = "1px solid red";
                pass=false;
            }
            if (document.getElementById('pass_new').value == "") {
                document.getElementById('pass_new').style.border = "1px solid red";
                pass=false;
            }
            if (document.getElementById('pass_new_confirm').value == "") {
                document.getElementById('pass_new_confirm').style.border = "1px solid red";
                pass=false;
            }
            return pass;
        }
    </script>
    <style type="text/css">
	body {
	    font-family: sans-serif;
	    background-color: #638fd0;
	    
	    background: -webkit-radial-gradient(circle, white, #638fd0);
	    background: -moz-radial-gradient(circle, white, #638fd0);
	}

    </style>
    
    
</head>
<body>
<br><br>
    <center>
    <div id="wrapper" class="centerwrapper">
	<h2 class="title"><?php echo htmlspecialchars( xl('Provider Portal Login'), ENT_NOQUOTES); ?></h2>
	<form action="get_provider_info.php" method="POST" onsubmit="return process()" >
	    <table>
		<tr>
		    <td class="algnRight"><?php echo htmlspecialchars( xl('User Name'), ENT_NOQUOTES); ?></td>
		    <td><input name="uname" id="uname" type="text" autocomplete="off" /></td>
		</tr>
		<tr>
		    <td class="algnRight"><?php echo htmlspecialchars( xl('Password'), ENT_NOQUOTES);?></>
		    <td>
			<input name="pass" id="pass" type="password" autocomplete="off" />
		    </td>
		</tr>


		<tr>
		    <td colspan=2><br><center><input type="submit" value="<?php echo htmlspecialchars( xl('Log In'), ENT_QUOTES);?>" /></center></td>
		</tr>
	    </table>
            <?php if (!(empty($hiddenLanguageField))) echo $hiddenLanguageField; ?>
	</form>
    
      </div>
    

    </center>

<script type="text/javascript">
      $(document).ready(function() {

<?php // if something went wrong
     if (isset($_GET['w'])) { ?>    
	var unique_id = $.gritter.add({
	    title: '<span class="red"><?php echo htmlspecialchars( xl('Oops!'), ENT_QUOTES);?></span>',
	    text: '<?php echo htmlspecialchars( xl('Something went wrong. Please try again.', ENT_QUOTES)); ?>',
	    sticky: false,
	    time: '5000',
	    class_name: 'my-nonsticky-class'
	});    
<?php } ?>

<?php // if successfully logged out
     if (isset($_GET['logout'])) { ?>    
	var unique_id = $.gritter.add({
	    title: '<span class="green"><?php echo htmlspecialchars( xl('Success'), ENT_QUOTES);?></span>',
	    text: '<?php echo htmlspecialchars( xl('You have been successfully logged out.'), ENT_QUOTES);?>',
	    sticky: false,
	    time: '5000',
	    class_name: 'my-nonsticky-class'
	});    
<?php } ?>
	return false;
    
    });
</script>

</body>
</html>
