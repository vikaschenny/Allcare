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
  
 if($_POST['pro_sel']!='1'){
     
    //checking whether the request comes from index.php
    if (!isset($_SESSION['itsme'])) {
            session_destroy();
            header('Location: '.$landingpage.'&w');
            exit;
    }
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
}else if($_POST['pro_sel']=='1') {
    $_SESSION['portal_username']=$_POST['provider'];
    $_SESSION['refer']=$_POST['refer']; 
    $date=date('Y/m/d H:i:s');
    $ins=sqlStatement("insert into tbl_provider_portal_logs (date,provider,refers,action) values ('$date','".$_POST['provider']."','".$_POST['refer']."','login')");
    require_once('home.php'); 
}   

?>
<script src="js/responsive_datatable/version1.0/jquery-1.11.3.min.js"></script>
<link rel="stylesheet" href="fancybox/source/jquery.fancybox.css" type="text/css" media="screen" />
<script type="text/javascript" src="fancybox/source/jquery.fancybox.pack.js"></script>
<?php
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
    $sql2=sqlStatement("SELECT id, username, fname, lname
                        FROM users
                        WHERE username !=  ''
                        AND active =  '1' and username='".$_POST['uname']."'
                        ORDER BY username");
    $id2=sqlFetchArray($sql2);
    $usr=$id2['id']; 
    $ref_uname=$id2['username'];
    if(!empty($usr)){
        $sql3=sqlStatement("select pro_refers from tbl_user_custom_attr_1to1 where userid=$usr");
        $ref=sqlFetchArray($sql3);
        if($ref['pro_refers']!=''){ 
            $refers=explode('|',$ref['pro_refers']); 
            foreach($refers as $gid => $gval){
                $sql4=sqlStatement("SELECT id, username, fname, lname
                        FROM users
                        WHERE username !=  ''
                        AND active =  '1' and id=$gval
                       ");
                $id4=sqlFetchArray($sql4);
                $pro_name[$id4['username']]=$id4['fname']." ".$id4['lname'];
            }
?>
<style>
    .fancybox-inner {
        background-color:rgba(133, 227, 245, 0.62) !important;
        width:auto !important;
        height:130px !important;
      }
      .fancybox-skin {
           height:130px !important;
      }
</style>
            <script type="text/javascript"> 
                  $(document).ready(function() {
                      var htmlStr ="<form action='' method='post'><table style='padding-top:30px; padding-left:5px !important; padding-right:5px !important;'><tr><td> Login As: </td><td><select id='provider' name='provider' onchange='this.form.submit();'><option>select</option>"; 
                      <?php foreach($pro_name as $gid => $gval){
                            ?>  htmlStr += "<option value='<?php echo $gid; ?>'> <?php echo $gval; ?></option> ";<?php
                        } ?>
                        htmlStr +="</select><input type='hidden' name='refer' id='refer' value='<?php echo $ref_uname; ?>' /><input type='hidden' name='pro_sel' id='pro_sel' value='1' /></td></tr></table></form>";        
                       $.fancybox(htmlStr, {
                            'width': 1000,
                            'height': 2000,
                            'autoScale': false,
                            'transitionIn': 'none',
                            'transitionOut': 'none',
                            'hideOnOverlayClick':false,
                            'helpers'     : { 
                                overlay : {closeClick: false} // prevents closing when clicking OUTSIDE fancybox
                            }
                        }); 
                    }); </script>
       <?php  }else {
            echo "Access Denied";
            session_destroy();
            header('Location: '.$landingpage.'&w&c'); 
            exit;
        }
    }else {
        echo "Access Denied";
        session_destroy();
        header('Location: '.$landingpage.'&w&c');
        exit;
    }
}else {
    $_SESSION['portal_username']=$_POST['uname'];
     $date=date('Y/m/d H:i:s');
    $ins=sqlStatement("insert into tbl_provider_portal_logs (date,provider,refers,action) values ('$date','".$_POST['uname']."','".$_POST['uname']."','login')");
    require_once('home.php');
}       
?>
