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
//    if ( !(isset($GLOBALS['portal_onsite_enable'])) || !($GLOBALS['portal_onsite_enable']) ) {
//      echo htmlspecialchars( xl('Agency Portal is turned off'), ENT_NOQUOTES);
//      exit;
//    }

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
@import url(//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.min.css);
body {
  background: #46a1b4;
}
.omb_hrOr {
    background-color: #707070;
    height: 1px;
    margin-bottom: 8px !important;
    margin-top: 25px !important;
}

.omb_spanOr {
    background-color: #deffff;
    display: block;
    left: 50%;
    margin-left: -1.5em;
    position: absolute;
    text-align: center;
    top: 1.2em;
    width: 3em;
}
.btn-social{position:relative;padding-left:44px;text-align:left;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}.btn-social :first-child{position:absolute;left:0;top:0;bottom:0;width:32px;line-height:34px;font-size:1.6em;text-align:center;border-right:1px solid rgba(0,0,0,0.2)}
.btn-social.btn-lg{padding-left:61px}.btn-social.btn-lg :first-child{line-height:45px;width:45px;font-size:1.8em}
.btn-social.btn-sm{padding-left:38px}.btn-social.btn-sm :first-child{line-height:28px;width:28px;font-size:1.4em}
.btn-social.btn-xs{padding-left:30px}.btn-social.btn-xs :first-child{line-height:20px;width:20px;font-size:1.2em}
.btn-social-icon{position:relative;padding-left:44px;text-align:left;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;height:34px;width:34px;padding-left:0;padding-right:0}.btn-social-icon :first-child{position:absolute;left:0;top:0;bottom:0;width:32px;line-height:34px;font-size:1.6em;text-align:center;border-right:1px solid rgba(0,0,0,0.2)}
.btn-social-icon.btn-lg{padding-left:61px}.btn-social-icon.btn-lg :first-child{line-height:45px;width:45px;font-size:1.8em}
.btn-social-icon.btn-sm{padding-left:38px}.btn-social-icon.btn-sm :first-child{line-height:28px;width:28px;font-size:1.4em}
.btn-social-icon.btn-xs{padding-left:30px}.btn-social-icon.btn-xs :first-child{line-height:20px;width:20px;font-size:1.2em}
.btn-social-icon :first-child{border:none;text-align:center;width:100% !important}
.btn-social-icon.btn-lg{height:45px;width:45px;padding-left:0;padding-right:0}
.btn-social-icon.btn-sm{height:30px;width:30px;padding-left:0;padding-right:0}
.btn-social-icon.btn-xs{height:22px;width:22px;padding-left:0;padding-right:0}
.btn-google-plus{color:#fff;background-color:#dd4b39;border-color:rgba(0,0,0,0.2)}.btn-google-plus:hover,.btn-google-plus:focus,.btn-google-plus:active,.btn-google-plus.active,.open .dropdown-toggle.btn-google-plus{color:#fff;background-color:#ca3523;border-color:rgba(0,0,0,0.2)}
.btn-google-plus:active,.btn-google-plus.active,.open .dropdown-toggle.btn-google-plus{background-image:none}
.btn-google-plus.disabled,.btn-google-plus[disabled],fieldset[disabled] .btn-google-plus,.btn-google-plus.disabled:hover,.btn-google-plus[disabled]:hover,fieldset[disabled] .btn-google-plus:hover,.btn-google-plus.disabled:focus,.btn-google-plus[disabled]:focus,fieldset[disabled] .btn-google-plus:focus,.btn-google-plus.disabled:active,.btn-google-plus[disabled]:active,fieldset[disabled] .btn-google-plus:active,.btn-google-plus.disabled.active,.btn-google-plus[disabled].active,fieldset[disabled] .btn-google-plus.active{background-color:#dd4b39;border-color:rgba(0,0,0,0.2)}
</style>
</head>
<body onload="javascript:document.login_form1.uname.focus();" bgcolor="#638fd0" >
    <div class="container">
        <div class="row">
            <div class="col-sm-6 col-sm-offset-3 col-lg-4 col-lg-offset-4">
                <div class="panel panel-default">
                    <div class="panel-heading panel-heading-custom"><h3 class="panel-title">Practice Portal Login</h3></div>
                    <div class="panel-body">
                        <form method="POST" action="get_provider_info.php" target="_top" name="login_form1" id="loginform" accept-charset="UTF-8" role="form" onsubmit="return process()">
                            <input type="hidden" name="param" id="param" value="<?php echo $_REQUEST['param']; ?>" />
                            <fieldset>
                                <?php if (isset($_GET['w'])): if(isset($_GET['em'])){ ?>
                                        <div class="alert alert-error">
                                            <a class="close" data-dismiss="alert" >&times;</a>
                                        <strong>Oops! </strong> <?php echo xlt("This Email Id Doesn't Exists!"); ?>.
                                    </div>
                                   <?php  }else if(isset($_GET['cnt'])){
                                           ?>
                                        <div class="alert alert-error">
                                            <a class="close" data-dismiss="alert" >&times;</a>
                                           <strong>Oops! </strong> <?php echo xlt($_GET['cnt']. " is already exists"); ?>.
                                        </div>
                                           <?php  }else if(isset($_GET['rl'])){
                                           ?>
                                        <div class="alert alert-error">
                                            <a class="close" data-dismiss="alert" >&times;</a>
                                           <strong>Oops! </strong> <?php echo xlt(ucfirst($_GET['rl']).' role is not allowed'); ?>.
                                        </div>
                                           <?php  }else if(isset($_GET['acc'])){ ?>
                                        <div class="alert alert-error">
                                            <a class="close" data-dismiss="alert" >&times;</a>
                                           <strong>Oops! </strong> <?php echo xlt("This User Doesn`t Have Provider Access!"); ?>.
                                        </div>
                                    <?php }else if(isset($_GET['eml'])){ ?>
                                        <div class="alert alert-error">
                                            <a class="close" data-dismiss="alert" >&times;</a>
                                           <strong>Oops! </strong> <?php echo xlt($_GET['eml']. " is already exists"); ?>.
                                        </div>
                                    <?php } else if(isset($_GET['unem'])){ ?>
                                        <div class="alert alert-error">
                                            <a class="close" data-dismiss="alert" >&times;</a>
                                           <strong>Oops! </strong> <?php echo xlt(" Username already exists"); ?>.
                                        </div>
                                    <?php } else { ?>
                                    <div class="alert alert-error">
                                        <a class="close" data-dismiss="alert" >&times;</a>
                                        <strong>Oops! </strong> <?php echo xlt('Something went wrong. Please try again.'); ?>.
                                    </div>
                                <?php } endif; ?>                               
                                <div class="form-group left-inner-addon">
                                    <i class="glyphicon glyphicon-user"></i>
                                    <input class="form-control" placeholder="<?php echo xlt('Username'); ?>" name="uname" autocomplete="off" id="uname" type="text">
                                </div>
                                <div class="inner-addon left-inner-addon">
                                    <i class="glyphicon glyphicon-lock"></i>
                                    <input class="form-control" placeholder="<?php echo xlt('Password'); ?>" name="pass" id="pass"  type="password" value="">
                                </div>
                                <div id="submitbutton">
                                    <input class="btn btn-md btn-success btn-block" type="submit" value="<?php echo xla('Login');?>">
                                </div>
                                <div class="row omb_row-sm-offset-3 omb_loginOr">
                                        <div class="col-xs-12">
                                                <hr class="omb_hrOr">
                                                <span class="omb_spanOr">or</span>
                                        </div>
                                </div>
                            </fieldset>
                        </form>
                        <center><?php 
                        if($_SESSION['ggllogin']==1) {  
                            $_SESSION['logout']=1;
                            echo"<script>location.reload();</script>";
                        } 
                        include('sso_ggl.php');
                        //include('sso_page_op.php');?></center>
                    </div>
                </div>
            </div>
        </div>
    </div>
        
</body>
</html>
 