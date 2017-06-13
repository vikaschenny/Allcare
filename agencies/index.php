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

?>
<html>
<head>
    <title><?php echo xlt('Agency Portal Login'); ?></title>
    <link href="./css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="./css/toastr.css">
    <script src="./js/jquery.min.js"></script>
    <script src="./js/toastr.js"></script>
    <script src="./js/bootstrap.min.js"></script>

    <script type="text/javascript">
        $(document).ready(function(){
            toastr.options = {
                "closeButton": false,
                "debug": false,
                "newestOnTop": false,
                "progressBar": false,
                "positionClass": "toast-top-right",
                "preventDuplicates": false,
                "onclick": null,
                "showDuration": "300",
                "hideDuration": "1000",
                "timeOut": "2000",
                "extendedTimeOut": "1000",
                "showEasing": "swing",
                "hideEasing": "linear",
                "showMethod": "fadeIn",
                "hideMethod": "fadeOut"
            }
            <?php // if something went wrong
                if (isset($_GET['w'])) { ?>
                    toastr.error('', 'Oops! Something went wrong. Please try again.');    
            <?php } ?>

           <?php // if successfully logged out
                if (isset($_GET['logout'])) { ?>
                   toastr.success('', 'You have been successfully logged out.');   
            <?php } ?>
            return false;
        })
        function process() {
            
            if (!(validate())) {
                toastr.error('', 'Field(s) are missing!');
                return false;
            }
        }
	function validate() {
            var pass=true;            
	    if (document.getElementById('uname').value == "") {
		document.getElementById('uname').style.border = "1px solid red";
                pass=false;
	    }else{
                document.getElementById('uname').style.border = "1px solid #e4e6e8";
            }
	    if (document.getElementById('pass').value == "") {
		document.getElementById('pass').style.border = "1px solid red";
                pass=false;
	    }else{
                document.getElementById('uname').style.border = "1px solid #e4e6e8";
            }
            return pass;
	}
        function process_new_pass() {

            if (!(validate_new_pass())) {
                toastr.error('', 'Field(s) are missing!');
                return false;
            }
            if (document.getElementById('pass_new').value != document.getElementById('pass_new_confirm').value) {
                toastr.error('', 'The new password fields are not the same.');
                return false;
            }
            if (document.getElementById('pass').value == document.getElementById('pass_new').value) {
                toastr.error('', 'The new password can not be the same as the current password.');                
                return false;
            }
        }

        function validate_new_pass() {
            var pass=true;
            if (document.getElementById('uname').value == "") {
                document.getElementById('uname').style.border = "1px solid red";
                pass=false;
            }else{
                document.getElementById('uname').style.border = "1px solid #e4e6e8";
            }
            if (document.getElementById('pass').value == "") {
                document.getElementById('pass').style.border = "1px solid red";
                pass=false;
            }else{
                document.getElementById('pass').style.border = "1px solid #e4e6e8";
            }
            if (document.getElementById('pass_new').value == "") {
                document.getElementById('pass_new').style.border = "1px solid red";
                pass=false;
            }else{
                document.getElementById('pass_new').style.border = "1px solid #e4e6e8";
            }
            if (document.getElementById('pass_new_confirm').value == "") {
                document.getElementById('pass_new_confirm').style.border = "1px solid red";
                pass=false;
            }else{
                document.getElementById('pass_new_confirm').style.border = "1px solid #e4e6e8";
            }
            return pass;
        }
    </script>
    <style type="text/css">
	body{margin:0;padding:0;font-family:Segoe UI,sans-serif;background:#f1f1f1;overflow-x:hidden!important}p,ul,li,form,h1,h2,h3,h4,h5,h6,button{padding:0;margin:0}a,button{outline:none}li{list-style-type:none}a{text-decoration:none}.left{float:left}.right{float:right}.container{width:1150px;margin:0 auto}.fix{clear:both}#header{height:100px;background:#fff url("http://yavuz-selim.com/css/../img/header-top.png") top left no-repeat;border-bottom:1px soild #d3d5d7;box-shadow:0 0 5px 0 #e2e3e4}#cForm input,#cForm textarea{text-transform:none!important}a.logo{display:block;color:#e74c3c;font-size:30px;font-weight:700;transition:color .5s;}a.logo:hover{color:#2f3c4e}a.logo,ul#menu button{font-family:"Lato",sans-serif}ul#menu{font-size:15px}ul#menu li{float:left}ul#menu li a{height:100px;line-height:100px;color:#2f3c4e;margin-right:20px;transition:color .5s;text-shadow:0 0 .1px #a9aeb5}ul#menu li a:hover{color:#e74c3c}ul#menu li:last-of-type a{margin-right:0}ul#menu li.active a{color:#e74c3c}ul#menu li a i{position:relative;top:1px}ul#menu li button{color:#a9aeb5;cursor:pointer;height:35px;margin-top:31.5px;background-color:#f6f6f6;border:1px solid #e4e6e8;text-align:center;border-radius:2px;transition:color 0.5s,background 0.5s,border-color .5s}ul#menu li.f{margin:0 10px}ul#menu li.f:last-of-type{margin:0}ul#menu li.f button{width:35px}ul#menu li.f:last-of-type button{width:100px}ul#menu li.f button.ac,ul#menu li.f button:hover{background:#2f3c4e;border-color:#2f3c4e;color:#f6f6f6}ul#social{background:#2f3c4e;padding:0;display:none;position:fixed;z-index:3;right:88px}ul#social li{display:block;float:none;height:35px;line-height:35px;width:200px;font-family:Segoe UI,sans-serif;margin:20px 0}ul#social li a{display:block;height:35px;width:145px;padding:0 10px;line-height:35px;font-size:14px;margin:0 auto;border-radius:2px;color:#fff}ul#social li a i{margin-right:10px;font-size:14px}ul#social li.facebook a{background:#5d82d1}ul#social li.twitter a{background:#40bff5}ul#social li.youtube a{background:#ef4e41}ul#social li.google a{background:#eb5e4c}ul#social li.rss a{background:#ff9900}#content{width:750px}.content{background-color:#fff;border-bottom:1px solid #d3d5d7;-webkit-box-shadow:0 0 5px 0 #e2e3e4;-moz-box-shadow:0 0 5px 0 #e2e3e4;box-shadow:0 0 5px 0 #e2e3e4;margin-bottom:50px;word-wrap:break-word;overflow:hidden}.content h1{font-size:30px;font-weight:400}.content h1 a{color:#2f3c4e;font-family:"Roboto Slab",sans-serif;text-transform:uppercase;display:block;margin-bottom:10px;transition:color .5s}i.fa-heart{color:#e74c3c}.content ul.banner li{float:left}.content ul.banner li a,.content a.read{display:block;color:#a9aeb5;height:35px;line-height:35px;background-color:#f6f6f6;border:1px solid #e4e6e8;text-align:center;border-radius:2px;padding:0 10px;margin-right:10px;font-size:13px;text-transform:uppercase;font-family:Open Sans}.content a.read{background:#e74c3c;color:#fff;border-color:#e74c3c;border-radius:2px;transition:border-color 0.5s,background .5s}.content h1 a:hover{color:#e74c3c}.content a.read:hover{background:#2f3c4e;border-color:#2f3c4e}.content ul.banner li:last-of-type a{margin-right:0}.content ul.banner li a span{color:#e74c3c}.content .c-content{padding:10px 0;color:#6d7683;font-size:13px;letter-spacing:.5px;border-bottom:1px solid #e2e3e4;margin-bottom:15px}.c{padding:30px}.content img{max-width:690px;height:auto;border-radius:2px;display:block;margin:0 auto}.sidebar{width:360px}#profile{background:#e74c3c;color:#fff;padding:20px 10px;border-bottom:1px solid #d3d5d7;-webkit-box-shadow:0 0 5px 0 #e2e3e4;-moz-box-shadow:0 0 5px 0 #e2e3e4;box-shadow:0 0 5px 0 #e2e3e4}#profile p:first-of-type{text-transform:uppercase!important}#profile img{display:block;margin:10px auto;color:#fff;height:100px;width:100px;line-height:100px;border-radius:50%;-moz-border-radius:50%;-webkit-border-radius:50%;text-align:center;font-weight:600;font-style:italic;border:5px solid #d82425}#profile p{text-align:center;color:#fff;display:block;margin:20px auto;font-family:Open Sans,sans-serif}#profile .p-social a{display:inline-block;text-align:center;width:25px;height:25px;line-height:25px;border:1px solid rgba(255,255,255,0.2);color:#fff;margin-right:10px;transition:border-color 0.5s,background .5s}#profile .p-social a:last-of-type{margin-right:0}#profile .p-social a:hover{background:#2f3c4e;border-color:#2f3c4e}#profile .p-social{width:175px;margin:0 auto}.sid{border-bottom:1px solid #d3d5d7;-webkit-box-shadow:0 0 5px 0 #e2e3e4;-moz-box-shadow:0 0 5px 0 #e2e3e4;box-shadow:0 0 5px 0 #e2e3e4;background:#fff;padding:30px;margin:50px 0 0;word-wrap:break-word!important;overflow:hidden}.sid h1,.table h1,h3.f-title,h1#results,h1#contact{font-size:16px;font-weight:700;padding-bottom:10px;border-bottom:2px solid #ecedee;margin-bottom:20px;line-height:28px;position:relative;font-family:"Roboto Slab",Segoe UI;color:#2f3c4e}.sid h1 i,.table h1 i,h3.f-title i,h1#results i,h1#contact i{background-color:#fff;height:28px;width:28px;line-height:28px;font-size:14px;text-align:center;margin-right:10px;color:#000}.search_results ul li a{display:block;color:#2f3c4e;height:30px;line-height:30px;transition:background 0.5s,color .5s;padding:0 10px}.search_results ul li a:hover{background:#e74c3c;color:#fff}.sid h1:before,.table h1:before,h3.f-title:before,h1#results:before,h1#contact:before{content:"";position:absolute;height:2px;width:28px;background-color:#2f3c4e;bottom:-2px;left:0}.sid input{text-transform:none!important;font-family:Open Sans}.sid input,.c form input,.c form textarea,#registerForm input,#contactForm input,#contactForm textarea,#registerForm textarea{width:100%;padding:10px;background-color:#f6f6f6;border:1px solid #e4e6e8;margin-bottom:20px;font-size:16px;text-transform:uppercase;-moz-box-sizing:border-box;-webkit-box-sizing:border-box;box-sizing:border-box}.sid button:focus,.sid input:focus,.c input:focus,.c textarea:focus,.c button:focus,#contactForm input:focus,#registerForm input:focus,#contactForm button:focus,#registerForm button:focus,#contactForm textarea:focus,#registerForm textarea:focus{outline:none}.sid button,.c button,#registerForm button,#contactForm button{border-radius:2px;color:#fff;background:#3fbbc0;padding:10px;font-size:13px;text-transform:uppercase;margin:0;font-weight:400;text-align:center;border:none;cursor:pointer;width:100%;transition:background .5s}#m-menu{display:none;margin:50px auto -20px auto;width:455px;border:1px solid #e9e8e8;padding:0 10px;background-color:#f8f7f7;cursor:pointer;height:35px;line-height:35px;font-size:14px;transition:color .5s}#m-menu:hover{color:#e74c3c}ul.m-menu{display:none;width:475px;margin:20px auto -20px auto}ul.m-menu li a{display:block;height:25px;line-height:25px;background:#2f3c4e;color:#fff;padding:0 10px;border-bottom:1px solid #f8f7f7}#contactForm *,#registerForm *,.c form *{font-family:Open Sans,Segoe UI!important}.c input,.c button{display:block;width:40%!important}#registerForm input,#contactForm input{width:47%!important}#registerForm button,#contactForm button{width:100%!important}ul.m-menu li:last-of-type a{border-bottom:0}.sid button:hover,.c button:hover,#contactForm button:hover{background:#09858a}.sid ul.cat li a{display:block;margin-bottom:10px;padding-bottom:10px;border-bottom:1px solid #ecedee;font-size:14px;color:#4b525c;font-family:Open Sans,Segoe UI,sans-serif;transition:color .5s}.sid ul.cat li a:hover{color:#e74c3c}.sid ul.cat li:last-of-type a{border-bottom:0}.sid ul.li li{display:block;margin-bottom:10px;padding-bottom:10px;border-bottom:1px solid #ecedee;font-size:14px;color:#4b525c;font-family:Open Sans,Segoe UI,sans-serif}.sid ul.li li a{color:#4b525c;transition:color .5s}.sid ul.li li:last-of-type{border-bottom:0;margin-bottom:0;padding-bottom:0;}.sid ul.li li a:hover{color:#e74c3c}.sid ul.li li span{display:block;font-weight:600;margin:10px 0 0}.sid a.tagCloud,.content a.tagCloud{background-color:#f6f6f6;border:1px solid #e4e6e8;margin:0 3px 3px 0;display:block;float:left;padding:6px 10px;font-size:12px!important;color:#a3a9b0;transition:background 0.5s,border-color 0.5s,color .5s}.sid a.tagCloud:hover,.content a.tagCloud:hover{background:#e74c3c;border-color:#e74c3c;color:#fff}#sign{font-size:13px;margin-top:60px;background:#fff;border-top:1px solid #d3d5d7;-webkit-box-shadow:0 0 5px 0 #e2e3e4;-moz-box-shadow:0 0 5px 0 #e2e3e4;box-shadow:0 0 5px 0 #e2e3e4;font-family:Open Sans,Segoe UI,sans-serif;padding:10px}#registerForm input,#contactForm input{text-transform:none}#registerForm textarea,#contactForm textarea{text-transform:none}#up{background: #e74c3c;color: #fff;position: fixed;width: 20%;height: 30px;line-height: 30px;font-size: 15px;bottom: 0;cursor: pointer;text-align: center;z-index: 9999;display: none;margin: 0 auto;left: 50%;margin-left: -10%;border-top-left-radius: 100px;border-top-right-radius: 100px;}#pages a{color:#a3a9b0;padding:10px;text-transform:uppercase;font-family:Open Sans,Segoe UI,sans-serif;background:#fff;border-bottom:1px solid #d3d5d7;-webkit-box-shadow:0 0 5px 0 #e2e3e4;-moz-box-shadow:0 0 5px 0 #e2e3e4;box-shadow:0 0 5px 0 #e2e3e4;transition:color .5s}#pages a:nth-child(1){border-left:1px solid #d8dadc}#pages a:nth-child(2){border-right:1px solid #d8dadc}#pages a:hover{color:#2f3c4e}#footer{margin-top:0;background:#293545;height:100px;line-height:100px;color:#F1F1F1;font-size:13px;font-family:Open Sans,Segoe UI,sans-serif;word-wrap:break-word;overflow:hidden}#footer .di{text-align:center}#footer .di a{color:#e74c3c}.di:last-of-type{display:none}.alert{background:#e74c3c;border-bottom:1px solid #d3d5d7;-webkit-box-shadow:0 0 5px 0 #e2e3e4;-moz-box-shadow:0 0 5px 0 #e2e3e4;box-shadow:0 0 5px 0 #e2e3e4;font-family:Open Sans,Segoe UI,sans-serif;padding:20px}.alert p{color:#fff;font-size:14px;line-height:28px;font-style:italic;margin-bottom:0}.alert i{color:#d82425;font-size:24px;margin-bottom:20px}.detail a{color: #e74c3c;}.detail{font-family:Open Sans,Segoe UI,sans-serif;font-size:14px!important}.d-box{border-bottom:1px solid #d3d5d7;-webkit-box-shadow:0 0 5px 0 #e2e3e4;-moz-box-shadow:0 0 5px 0 #e2e3e4;box-shadow:0 0 5px 0 #e2e3e4;font-family:Open Sans,Segoe UI,sans-serif;background:#fff;color:#2f3c4e}.comment{border-bottom:1px solid #ecedee;margin-bottom:20px;padding-bottom:20px;font-family:Open Sans,Segoe UI,sans-serif}.comment:last-of-type{border-bottom:0}h3.comment-author{color:#e74c3c;font-size:13px;display:inline-block;margin-bottom:5px;text-transform:uppercase}.comment span{font-size:13px;text-transform:uppercase;display:block;color:#6d7683}p.comment-con{line-height:22px;font-size:13px;color:#6d7683;margin:10px 0 0}a.ex{color:#e74c3c;outline:none}#loading{display:none;background:rgba(255,255,255,.9) url("http://yavuz-selim.com/css/../img/load.gif") no-repeat center;position:fixed;top:0;left:0;width:100%;height:100%;z-index:9998}.conf{cursor:pointer}.conf span.sp{color:#2f3c4e}.conf_out{display:inline-block;position:relative;padding-left:20px;height:20px;line-height:20px}.conf_out:before{border-radius:10px;content:"?";display:block;position:absolute;left:0;top:5px;width:20px;margin-right:10px;text-align:center;background:silver;}.conf_in:before{content:"?";color:#fff;background:green}a[data-target]{display:block;width:36%;border-radius:2px;color:#fff;background:#2f3c4e;padding:10px;font-size:13px;text-transform:uppercase;margin:0;font-weight:400;text-align:center;border:none;cursor:pointer}.la{display:none!important;}.mi2{display:none!important;}.mi{display:none!important;}.cdel{color:#e74c3c;outline:none;}.banner728x90{width:728px;height:90px;margin:0 auto;}.banner300x250{width:300px;height:250px;}.fixed{position: fixed!important; bottom: 50px!important; right: 100px!important;}.loading{background:#fff;width:750px;border-bottom: 1px solid #d3d5d7;text-align:center;-webkit-box-shadow: 0 0 5px 0 #e2e3e4;-moz-box-shadow: 0 0 5px 0 #e2e3e4;box-shadow: 0 0 5px 0 #e2e3e4;margin-bottom: 50px;}.loadi{display:none;position:fixed;bottom:0;}i.slogan{display:inline-block;font-family:Raleway,sans-serif;font-size:13px;}i.slogan i{color:#e74c3c}.header2{padding:20px 0px;}.table{padding: 30px;background: #fff;border-bottom: 1px solid #d3d5d7;-webkit-box-shadow: 0 0 5px 0 #e2e3e4;-moz-box-shadow: 0 0 5px 0 #e2e3e4;box-shadow: 0 0 5px 0 #e2e3e4;}
        .table ul.OnMenu li{ font-weight: 600; color: #1a1a1a; padding-bottom: 10px; margin-bottom: 10px; border-bottom: 2px solid #d3d5d7 }
        .table ul.OnMenu li span{ display: block; float: left; width: 33%; }
        .table ul:last-of-type li{ display: block; padding-bottom: 10px; margin-bottom: 10px; border-bottom: 1px solid #ddd; }
        .table ul:last-of-type li:last-of-type{ padding-bottom: 0; margin-bottom: 0; border-bottom: 0; }
        .table ul:last-of-type li span.col{
            display: block;
                float: left;
                width: 33%;
        }
        a#m{
                display: block;
                text-align: center;
                color: #2f3c4e;
                font-family: 'Lato', Raleway;
                font-size: 15px;
                padding: 15px 0;
                font-weight: 600;
                border-bottom-left-radius: 4px;
                border-bottom-right-radius: 4px;
        }
        a#m.active{ background: #e74c3c; color: #fff; }
        div.m{ display: none; font-size: 14px; font-family: Open Sans; }
        div.m p{ text-align: center; padding: 10px 0; border-top: 1px solid #ddd; }
        div.m p:first-of-type{ border-top: 0; }
        div.m p:last-of-type{ margin-bottom: 0; }
        div.m a, div.m i{ color: #2f3c4e; }
        div.menu-mobil{ display: none!important; }
        #registerForm label{ color: #2f3c4e; font-family: 'Lato', Raleway!important; font-size: 15px;  }
        .cForm *:focus{ outline: none; }
        .cForm button{ width: 100%!important; }
        .comment-con input,.cForm select{ width:100%;padding:10px;background-color:#f6f6f6;border:1px solid #e4e6e8;margin-bottom:20px;font-size:12px;text-transform:uppercase;-moz-box-sizing:border-box;-webkit-box-sizing:border-box;box-sizing:border-box; outline: none; }
        .comment-con input{ width: 100%!important; }
        .cForm input{ width: 47%!important; text-transform: none!important; }
        .cForm textarea{ text-transform: none!important; }
        #profile{ position: relative; }
        .cForm img{
                width: 200px!important;
                height: auto!important;
                padding: 3px!important;
                border: 2px solid #e74c3c!important;
                margin-bottom: 20px;
        }
        .sid ul.cat li:last-of-type a{ padding-bottom: 0; margin-bottom: 0; }
        .p-social{ width: 193px!important; text-align: center; }
        #members{
                background: #f1f1f1;
                color: #1a1a1a;
                padding: 0;
                font-size: 14px;
                font-family: Open Sans;
        }
        #members ul li a{
                display: inline-block;
                padding: 15px 0;
            color: #2f3c4e;
            margin-right: 30px;
            transition: color .5s;
            text-shadow: 0 0 .1px #a9aeb5;
        }
        #members ul li:first-of-type a{ margin-right: 0 }
        #members ul li i{ color: #2f3c4e; margin-right: 5px }
        #members ul li a:hover{ color: #e74c3c; }
        .search_results ul li a{ overflow: hidden; }
        .soc input{ display: inline-block!important; width: 64%!important; }
        .soc span{ display: inline-block; width: 35%; color: #2f3c4e; font-size: 15px; font-family: 'Lato', Raleway; }
        label{ position: relative; top: 5px; cursor: pointer; color: #2f3c4e; }
        label input[type=checkbox]{
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                opacity: 0;
                filter: alpha(opacity=0);
                cursor: pointer;
        }
        label span{
                display: inline-block;
            position: relative;
            height: 20px;
            margin-right: 20px;
            line-height: 20px;
        }
        label span:before{
                content: "?";
            display: block;
            position: absolute;
            left: 0px;
            top: 5px;
            width: 20px;
            margin-right: 10px;
            text-align: center;
            background: silver;
            border-radius: 10px;
        }
        label span.choosec:before{
                content: "?";
            color: rgb(255, 255, 255);
            background: green;
        }
        input[name=comment]{ text-transform: none!important; }
        .ch{ background: green!important; color: #fff; }
        .vis{ position: absolute; cursor: help; z-index: 2; right: 30px; top: 10px; }
        .status{ position: absolute; cursor: help; z-index: 2; right: 10px; top: 10px; }
        .tool{ position: absolute; top: 0; left: 0; z-index: 3; color: #e74c3c; background: #fff; padding: 10px; border: 1px solid #e74c3c; border-radius: 4px; width: 150px; text-align: center; }
        h3.comment-author a{ color: #e74c3c; }

    </style>
    
    
</head>
<body>
    
    <div class="container-fluid">
	<div class="row">
            <div class="col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3">
                    <div class="sid wow slideInRight" style="visibility: visible; animation-name: slideInRight;">
                    <?php if (isset($_SESSION['password_update'])||isset($_GET['password_update'])) { 
                        $_SESSION['password_update']=1;
                    ?>    
                        <h1><i class="glyphicon glyphicon-lock"></i><?php echo htmlspecialchars( xl('Please Enter a New Password'), ENT_NOQUOTES); ?></h1>
                        <form  action="get_agencyinfo.php" method="POST" id="loginForm" onsubmit="return process_new_pass()">
                            <input name="uname" id="uname" type="text" autocomplete="off" value="<?php echo attr($_SESSION['portal_username']); ?>" placeholder="UserName">
                            <input name="pass" id="pass" type="password" autocomplete="off" placeholder="Password" style="margin-bottom:10px" value="">
                            <input name="pass_new" id="pass_new" type="password" autocomplete="off" placeholder="New Password" style="margin-bottom:10px" value="">
                            <input name="pass_new_confirm" id="pass_new_confirm" type="password" autocomplete="off" placeholder="Confirm Password" style="margin-bottom:10px" value="">
                            <input type="hidden" name="param" id="param" value="<?php echo $_REQUEST['param']; ?>" />
                            <button type="submit" style="margin-top:20px">Log In</button>
                        </form>
                    <?php } else { ?>
                        <h1><i class="glyphicon glyphicon-lock"></i> <?php echo htmlspecialchars( xl('Agency Portal Login'), ENT_NOQUOTES); ?></h1>
                        <form  action="get_agencyinfo.php" method="POST" id="loginForm" onsubmit="return process()">
                            <input name="uname" id="uname" type="text" autocomplete="off" placeholder="UserName" value="">
                            <input name="pass" id="pass" type="password" autocomplete="off" placeholder="Password" style="margin-bottom:10px" value="">
                            <input type="hidden" name="param" id="param" value="<?php echo $_REQUEST['param']; ?>" />
                            <button type="submit" style="margin-top:20px">Log In</button>
                            <?php if (!(empty($hiddenLanguageField))) echo $hiddenLanguageField; ?>
                        </form>
                    <?php } ?>
                    </div>
            </div>
        </div>
    </div>
<!--<br><br>
    <center>

    <?php if (isset($_SESSION['password_update'])||isset($_GET['password_update'])) { 
        $_SESSION['password_update']=1;
        ?>
      <div id="wrapper" class="centerwrapper">
        <h2 class="title"><?php echo htmlspecialchars( xl('Please Enter a New Password'), ENT_NOQUOTES); ?></h2>
        <form action="get_agencyinfo.php" method="POST" onsubmit="return process_new_pass()" >
            <table>
                <tr>
                    <td class="algnRight"><?php echo htmlspecialchars( xl('User Name'), ENT_NOQUOTES); ?></td>
                    <td><input name="uname" id="uname" type="text" autocomplete="off" value="<?php echo attr($_SESSION['portal_username']); ?>"/></td>
                </tr>
                <tr>
                    <td class="algnRight"><?php echo htmlspecialchars( xl('Current Password'), ENT_NOQUOTES);?></>
                    <td>
                        <input name="pass" id="pass" type="password" autocomplete="off" />
                    </td>
                </tr>
                <tr>
                    <td class="algnRight"><?php echo htmlspecialchars( xl('New Password'), ENT_NOQUOTES);?></>
                    <td>
                        <input name="pass_new" id="pass_new" type="password" />
                    </td>
                </tr>
                <tr>
                    <td class="algnRight"><?php echo htmlspecialchars( xl('Confirm New Password'), ENT_NOQUOTES);?></>
                    <td>
                        <input name="pass_new_confirm" id="pass_new_confirm" type="password" />
                    </td>
                </tr>
                <tr>
                    <td colspan=2><br><center><input type="submit" value="<?php echo htmlspecialchars( xl('Log In'), ENT_QUOTES);?>" /> 
                    </center></td>
                </tr>
            </table>
        </form>

        <div class="copyright"><?php echo htmlspecialchars( xl('Powered by'), ENT_NOQUOTES);?> OpenEMR</div>
      </div>
    <?php } else { ?>
      <div id="wrapper" class="centerwrapper">
	<h2 class="title"><?php echo htmlspecialchars( xl('Agency Portal Login'), ENT_NOQUOTES); ?></h2>
	<form action="get_agencyinfo.php" method="POST" onsubmit="return process()" >
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

                <?php if ($GLOBALS['language_menu_login']) { ?>
                 <?php if (count($result3) != 1) { ?>
                  <tr>
                    <td><span class="text"><?php echo htmlspecialchars( xl('Language'), ENT_NOQUOTES); ?></span></td>
                    <td>
                        <select name=languageChoice size="1">
                            <?php
                            echo "<option selected='selected' value='".htmlspecialchars($defaultLangID,ENT_QUOTES)."'>" . htmlspecialchars( xl('Default') . " - " . xl($defaultLangName), ENT_NOQUOTES) . "</option>\n";
                            foreach ($result3 as $iter) {
                                if ($GLOBALS['language_menu_showall']) {
                                    if ( !$GLOBALS['allow_debug_language'] && $iter[lang_description] == 'dummy') continue; // skip the dummy language
                                    echo "<option value='".htmlspecialchars($iter[lang_id],ENT_QUOTES)."'>".htmlspecialchars($iter[trans_lang_description],ENT_NOQUOTES)."</option>\n";
                                }
                                else {
                                    if (in_array($iter[lang_description], $GLOBALS['language_menu_show'])) {
                                        if ( !$GLOBALS['allow_debug_language'] && $iter[lang_description] == 'dummy') continue; // skip the dummy language
                                        echo "<option value='".htmlspecialchars($iter[lang_id],ENT_QUOTES)."'>".htmlspecialchars($iter[trans_lang_description],ENT_NOQUOTES)."</option>\n";
                                    }
                                }
                            }
                            ?>
                        </select>
                    </td>
                  </tr>
                <?php }} ?>

		<tr>
		    <td colspan=2><br><center><input type="submit" value="<?php echo htmlspecialchars( xl('Log In'), ENT_QUOTES);?>" /></center></td>
		</tr>
	    </table>
            <?php if (!(empty($hiddenLanguageField))) echo $hiddenLanguageField; ?>
	</form>
    
        <div class="copyright"><?php echo htmlspecialchars( xl('Powered by'), ENT_NOQUOTES);?> OpenEMR</div>
      </div>
    <?php } ?>

    </center>-->

<script type="text/javascript">
      /*$(document).ready(function() {

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
    
    });*/
</script>

</body>
</html>
