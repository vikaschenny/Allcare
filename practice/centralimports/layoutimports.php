 <?php
/**
 * Copyright (C) 2010 OpenEMR Support LLC
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * 2013/02/08 Minor tweaks by EMR Direct to allow integration with Direct messaging
 * 2013-03-27 by sunsetsystems: Fixed some weirdness with assigning a message recipient,
 *   and allowing a message to be closed with a new note appended and no recipient.
 */



require_once("../verify_session.php");


//STOP FAKE REGISTER GLOBALS
$fake_register_globals=false;

//continue session
session_start();

//landing page definition -- where to go if something goes wrong
$landingpage = "../../index.php?site=".$_SESSION['site_id']; 


if ( isset($_SESSION['portal_username']) ) {    
    $portal_user = $_SESSION['portal_username']; 
}else {
    session_destroy();
    header('Location: '.$landingpage.'&w');
    exit;
} 
$pagename = "insurance";
$subpage = "Layout Imports";
$ignoreAuth=true; // ignore the standard authentication for a regular OpenEMR user
//include_once('../../../interface/globals.php');
include '../section_header.php';


if(isset($_SESSION['portal_username']) !=''){
    $provider    = $_SESSION['portal_username'];
    $refer       = $_REQUEST['refer'];
    
    if($_REQUEST['refer']!='')
        $_SESSION['refer'] = $_REQUEST['refer']; 
}else {
   $provider                     = $_REQUEST['provider'];
    $_SESSION['portal_username'] = $_REQUEST['provider'];
    //for logout
    $refer                       = $_REQUEST['refer'];
    if($_REQUEST['refer']!='')
        $_SESSION['refer'] = $_REQUEST['refer'];
}

?>
<!DOCTYPE html>
<html>

	<head>
            <meta charset="UTF-8">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
	    <meta name="viewport" content="width=device-width, initial-scale=1">
            <title>layout Imports</title>
            <link rel="stylesheet" href="../insurance/assets/css/version1.0/dataTables.bootstrap.min.css"/>
            <link rel="stylesheet" href="../insurance/assets/css/version1.0/responsive.bootstrap.min.css"/>
            <link rel="stylesheet" href="../insurance/assets/css/perfect-scrollbar.min.css"/>
            <link rel="stylesheet" href="../insurance/assets/css/jquery.steps.css"/>
            <link rel="stylesheet" href="../insurance/assets/css/textext.core.css"/>
            <link rel="stylesheet" href="../insurance/assets/css/textext.plugin.tags.css"/>
            <link rel="stylesheet" href="../insurance/assets/css/textext.plugin.focus.css"/>
            <script src="../insurance/assets/js/responsive_datatable/version1.0/jquery-1.11.3.min.js"></script>
            <script src="../insurance/assets/js/responsive_datatable/version1.0/jquery.dataTables.min.js"></script>
            <script src="../insurance/assets/js/responsive_datatable/version1.0/dataTables.responsive.min.js"></script>
            <script type='text/javascript' src='../insurance/assets/js/responsive_datatable/dataTables.tableTools.js'></script>
            <script type='text/javascript' src='../insurance/assets/js/perfect-scrollbar.min.js'></script>
            <script type='text/javascript' src='../insurance/assets/js/jquery.steps.min.js'></script>
            <script type='text/javascript' src='../insurance/assets/js/responsive_datatable/dataTables.bootstrap.js'></script>
            <script>
               var linkurl= "../helplinks.php";
               var prsetting = "../practiceload.php";
               var userprofile = "../userprofile.php"; 
               $(document).ready(function(){
                   $("#sync").click(function(){
                       var confm = confirm("Are you sure");
                       if(confm){
                           var insopt = $("input[type=radio]:checked").val();
                                $.ajax({url:'ajaxgetinsattr.php',type:'POST',data:{insoptions:insopt},success:function(data){
                                        console.log(data);
                                },
                                error:function(err){console.log(err)}
                           })
                       }
                   });
               });
            </script>    
        </head>    
        <body>   
            
            <form method="POST" name="layoutimport">
                <fieldset><legend>Insurance Custom Attributes</legend>
                <input type="radio" name="insoptions" value="INSCA|tbl_inscomp_custom_attr_1to1"><label for="INSCA">Custom Insurance Attributes</label> <br/>
                <input type="radio" name="insoptions" value="INSUCOMP|tbl_patientinsurancecompany"><label for="INSUCOMP">Insurance Plans</label> <br/>
                <input type="radio" name="insoptions" value="BENEFITS|tbl_inscomp_benefits"><label for="BENEFITS">Benefits</label> <br/><br/>
                <input type="button" id="sync" name="sync" value="Sync Central to Practice"/>
                </fieldset>
            </form>    
                
            
            
<?php include '../section_footer.php'; ?> 
	</body>

</html>