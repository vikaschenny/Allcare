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
      echo htmlspecialchars( xl('Patient Portal is turned off'), ENT_NOQUOTES);
      exit;
    }

    // security measure -- will check on next page.
    $_SESSION['itsme'] = 1;
    //

    //
    // Deal with language selection
    //
    // collect default language id (skip this if this is a password update)
    if (!(isset($_SESSION['password_update']))) {
      $res2 = sqlStatement("select * from lang_languages where lang_description = ?", array($GLOBALS['language_default']) );
      for ($iter = 0;$row = sqlFetchArray($res2);$iter++) {
        $result2[$iter] = $row;
      }
      if (count($result2) == 1) {
        $defaultLangID = $result2[0]{"lang_id"};
        $defaultLangName = $result2[0]{"lang_description"};
      }
      else {
        //default to english if any problems
        $defaultLangID = 1;
        $defaultLangName = "English";
      }
      // set session variable to default so login information appears in default language
      $_SESSION['language_choice'] = $defaultLangID;
      // collect languages if showing language menu
      if ($GLOBALS['language_menu_login']) {
        // sorting order of language titles depends on language translation options.
        $mainLangID = empty($_SESSION['language_choice']) ? '1' : $_SESSION['language_choice'];
        if ($mainLangID == '1' && !empty($GLOBALS['skip_english_translation'])) {
          $sql = "SELECT * FROM lang_languages ORDER BY lang_description, lang_id";
          $res3=SqlStatement($sql);
        }
        else {
          // Use and sort by the translated language name.
          $sql = "SELECT ll.lang_id, " .
                 "IF(LENGTH(ld.definition),ld.definition,ll.lang_description) AS trans_lang_description, " .
                 "ll.lang_description " .
                 "FROM lang_languages AS ll " .
                 "LEFT JOIN lang_constants AS lc ON lc.constant_name = ll.lang_description " .
                 "LEFT JOIN lang_definitions AS ld ON ld.cons_id = lc.cons_id AND " .
                 "ld.lang_id = ? " .
                 "ORDER BY IF(LENGTH(ld.definition),ld.definition,ll.lang_description), ll.lang_id";
          $res3=SqlStatement($sql, array($mainLangID) );
        }
        for ($iter = 0;$row = sqlFetchArray($res3);$iter++) {
          $result3[$iter] = $row;
        }
        if (count($result3) == 1) {
          //default to english if only return one language
          $hiddenLanguageField = "<input type='hidden' name='languageChoice' value='1' />\n";
        }
      }
      else {
        $hiddenLanguageField = "<input type='hidden' name='languageChoice' value='".htmlspecialchars($defaultLangID,ENT_QUOTES)."' />\n";
      }
    }
    echo $_SESSION['password_update'];
    
    $patientenrol = '';
    $query = sqlStatement("SELECT title FROM list_options WHERE list_id= 'allcareConfig' AND option_id='patientenroll'");
    while($row = sqlFetchArray($query)){
        $patientenrol = $row['title'];
    }
?>
<html>
<head>
<?php html_header_show();?>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo xlt('Patient Portal Login'); ?></title>
    <link rel="stylesheet" type="text/css" href="../providers/assets/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/customizealerts.css">
    <script type="text/javascript" src="../providers/assets/js/jquery.min.js"></script>
    <script type="text/javascript" src="../providers/assets/js/bootstrap.min.js"></script>
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
        function process_new_pass() {

            if (!(validate_new_pass())) {
                $('#changepasswordform fieldset').prepend('<div class="alert alert-error"><a class="close" data-dismiss="alert">&times;</a><strong>Error! </strong> Field(s) are missing!</div>');
                return false;
            }
            if (document.getElementById('pass_new').value != document.getElementById('pass_new_confirm').value) {
                $('#changepasswordform fieldset').prepend('<div class="alert alert-error"><a class="close" data-dismiss="alert">&times;</a><strong>Error! </strong> The new password fields are not the same.</div>');
                return false;
            }
            if (document.getElementById('pass').value == document.getElementById('pass_new').value) {                
                $('#changepasswordform fieldset').prepend('<div class="alert alert-error"><a class="close" data-dismiss="alert">&times;</a><strong>Error! </strong> The new password can not be the same as the current password.</div>');
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
    $(function(){
        /* center modal */
        function centerModals($element) {
            var windowheight = $(window).height();
            var penalheight = $element.height();
            var margintop = ((windowheight/2) - penalheight/2);
            $element.css({marginTop:margintop});
          }
          centerModals($('.panel'));
          $(window).on('resize',function(){
              centerModals($('.panel'));
          });

    })
    </script>
<style>
body {
  background: #46a1b4;
}
</style>
</head>
<body onload="javascript:document.login_form1.uname.focus();" bgcolor="#638fd0" >
    <?php if (isset($_SESSION['password_update'])||isset($_GET['password_update'])) { 
                $_SESSION['password_update']=1;
    ?>
    <div class="container">
        <div class="row">
            <div class="col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3">
                <div class="panel panel-default">
                    <div class="panel-heading panel-heading-custom"><h3 class="panel-title"><?php echo htmlspecialchars( xl('Patient Portal Login'), ENT_NOQUOTES); ?></h3></div>
                    <div class="panel-body">
                        <form method="POST" action="get_patient_info.php" target="_top" id="changepasswordform" class="form-horizontal" name="login_form1" accept-charset="UTF-8" role="form" onsubmit="return process_new_pass()">                            
                            <fieldset>
                                <?php if (isset($_GET['w'])): ?>
                                        <div class="alert alert-error">
                                            <a class="close" data-dismiss="alert">&times;</a>
                                            <strong>Oops! </strong> <?php echo xlt('Something went wrong. Please try again.'); ?>
                                        </div>
                                    <?php endif; ?>
                                <div class="form-group">
                                    <label for="uname" class="control-label col-xs-4">User Name</label>
                                    <div class="col-xs-8">
                                        <input class="form-control" name="uname" id="uname"  type="text" value="<?php echo attr($_SESSION['portal_username']); ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="pass" class="control-label col-xs-4">Current Password</label>
                                    <div class="col-xs-8">
                                        <input class="form-control" name="pass" id="pass" type="password">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="pass_new" class="control-label col-xs-4">New Password</label>
                                    <div class="col-xs-8">
                                        <input class="form-control" name="pass_new" id="pass_new" type="password">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="pass_new_confirm" class="control-label col-xs-4">Confirm New Password</label>
                                    <div class="col-xs-8">
                                        <input class="form-control" name="pass_new_confirm" id="pass_new_confirm" type="password">
                                    </div>
                                </div>
                                <div id="submitbutton">
                                    <input class="btn btn-md btn-success btn-block" type="submit" value="<?php echo htmlspecialchars( xl('Log In'), ENT_QUOTES);?>">
                                </div>
                            </fieldset>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php } else { ?>
        <div class="container">
            <div class="row">
                <div class="col-sm-6 col-sm-offset-3 col-lg-4 col-lg-offset-4">
                    <div class="panel panel-default">
                        <div class="panel-heading panel-heading-custom"><h3 class="panel-title"><?php echo htmlspecialchars( xl('Patient Portal Login'), ENT_NOQUOTES); ?></h3></div>
                        <div class="panel-body">
                            <form method="POST" action="get_patient_info.php" target="_top" id="loginform" name="login_form1" accept-charset="UTF-8" role="form" onsubmit="return process()">                            
                                <fieldset>
                                    <?php if (isset($_GET['w'])): ?>
                                        <div class="alert alert-error">
                                            <a class="close" data-dismiss="alert">&times;</a>
                                            <strong>Oops! </strong> <?php echo xlt('Something went wrong. Please try again.'); ?>
                                        </div>
                                    <?php endif; ?>
                                    <div class="form-group left-inner-addon">
                                        <i class="glyphicon glyphicon-user"></i>
                                        <input class="form-control" placeholder="<?php echo xlt('Username'); ?>" name="uname" id="uname" type="text">
                                    </div>
                                    <div class="inner-addon left-inner-addon">
                                        <i class="glyphicon glyphicon-lock"></i>
                                        <input class="form-control" placeholder="<?php echo xlt('Password'); ?>" name="pass" id="pass"  type="password" value="">
                                    </div>
                                    <div id="submitbutton">
                                        <input class="btn btn-md btn-success btn-block" type="submit" value="<?php echo htmlspecialchars( xl('Log In'), ENT_QUOTES);?>">
                                    </div>
                                </fieldset>
                            </form>
                        <?php } ?>
<!--                            <div>
                                <a href="<?php echo $patientenrol; ?>" target="_blank">New Patient Enrollment</a>
                            </div>-->
                        </div>
                        
                    </div>
                </div>
            </div>
            
        </div>
        
</body>
</html>
