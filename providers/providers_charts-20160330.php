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

require_once("verify_session.php");

if(isset($_SESSION['portal_username']) !=''){
    $provider    = $_SESSION['portal_username'];
    $refer       = $_REQUEST['refer'];
    
    if($_REQUEST['refer']!='')
        $_SESSION['refer'] = $_REQUEST['refer'];
}else {
    $provider                    = $_REQUEST['provider'];
    $_SESSION['portal_username'] = $_REQUEST['provider'];
    //for logout
    $refer                       = $_REQUEST['refer'];
    if($_REQUEST['refer']!='')
        $_SESSION['refer'] = $_REQUEST['refer'];
}
$pagename = "charts"; 

require_once("../interface/globals.php");
require_once("../library/formdata.inc.php"); 
require_once("../library/globals.inc.php");
require_once("$srcdir/api.inc");
require_once("$srcdir/forms.inc");
require_once("$srcdir/options.inc.php");
require_once("$srcdir/patient.inc");
require_once("$srcdir/formdata.inc.php");
require_once("$srcdir/formatting.inc.php");


//for logout
$refer                      = $_REQUEST['refer'];
$_SESSION['refer']          = $_REQUEST['refer'];
$_SESSION['portal_username']= $_REQUEST['provider'];
$sql = sqlStatement("SELECT id, fname, lname, specialty FROM users " .
      "WHERE active = 1 AND ( info IS NULL OR info NOT LIKE '%Inactive%' ) " .
      "AND authorized = 1 AND username='".$provider."'" .
      "ORDER BY lname, fname");
$id         = sqlFetchArray($sql);

$pid        = $_REQUEST['pid'];
$encounter  = $_REQUEST['encounter'];
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="shortcut icon" href="img/season-change.jpg" type="image/x-icon">
        <title>HealthCare</title>
        <link href='http://fonts.googleapis.com/css?family=Abel' rel='stylesheet' type='text/css'>
        <link href='http://fonts.googleapis.com/css?family=Roboto:400,300,500' rel='stylesheet' type='text/css'>
        <link href='http://fonts.googleapis.com/css?family=Dosis:300,400,500,600' rel='stylesheet' type='text/css'>
        <link rel="stylesheet" type="text/css" href="assets/css/animate.css">
        <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="assets/css/owl.carousel.css">
        <link rel="stylesheet" type="text/css" href="assets/css/owl.theme.css">
        <link rel="stylesheet" type="text/css" href="assets/css/owl.transitions.css">
        <link rel="stylesheet" type="text/css" href="assets/css/font-awesome.min.css">
        <link rel="stylesheet" type="text/css" href="assets/css/main.css">
        <link rel="stylesheet" type="text/css" href="assets/css/customize.css">
        <link href='http://fonts.googleapis.com/css?family=Roboto+Condensed:400,300' rel='stylesheet' type='text/css'>
        <link rel="stylesheet" href="css/dataTables.bootstrap.css"/>
        <link rel="stylesheet" href="css/datatables.responsive_bootstrap.css"/>
        <script type="text/javascript" src="assets/js/jquery.min.js"></script>
        <style>
            @media screen and (max-width: 767px) {

                main#content {
                  margin-top: 65px;
                  transition: all ease-out 0.3s;
                }

            }
            .section-header{
                padding: 0px;
                font-size: 1.5em;
                border: 0px;
                border-bottom:0px !important;
                margin-bottom:0px;
            }
            #patient-info,#single_view {
                margin-left: 14px;
            }
            #single_view{
                min-height: 150px;
            }
            .tabNav {
                color: #000000 !important;
                font-size: 20px;
                padding: 0;
            }
            .tabContainer {
                margin-left: 13px;
            }
        </style>
        <script type='text/javascript'>
            function toggle( target, div ) {
                $mode = $(target).find(".indicator").text();
                if ( $mode == "collapse" ) {
                    $(target).find(".indicator").text( "expand" );
                    $(div).hide();
                } else {
                    $(target).find(".indicator").text( "collapse" );
                    $(div).show();
                }
            }
            function DoPost(page_name, provider,refer) {
                method = "post"; // Set method to post by default if not specified.
                var form = document.createElement("form");
                form.setAttribute("method", method);
                form.setAttribute("action", page_name);
                var key='provider';
                var hiddenField = document.createElement("input");
                hiddenField.setAttribute("type", "hidden");
                hiddenField.setAttribute("name", key);
                hiddenField.setAttribute("value", provider);
                form.appendChild(hiddenField);
                var key1='refer';
                var hiddenField1 = document.createElement("input");
                hiddenField1.setAttribute("type", "hidden");
                hiddenField1.setAttribute("name", key1);
                hiddenField1.setAttribute("value", refer);
                form.appendChild(hiddenField1);
                document.body.appendChild(form);
                form.submit();
            }

//        $.noConflict();
        $(document).ready(function(){
            $('#form_patient_dropdown').change(function(){
                var pid = $("#form_patient_dropdown option:selected").val();
                if(pid){
                    $("#Encounter_result").hide();  
                    $('.xdsoft_datetimepicker').remove();
                    $.ajax({
                        type: "GET",
                        url: "patient_full_encounters_single_view.php",
                        data: {pid:pid,refer:'<?php echo $_SESSION['refer']; ?>'},
                        success: function(data, textStatus) {
            //                $(".loader").hide();
                            $("#Encounter_result").show();    
                            $("#Encounter_result").html(data);    
                        },
                        error: function(jqXHR, exception){
                            alert("failed" + jqXHR.responseText);
                        }    
                    });
                }else{
                    $("#Encounter_result").hide();  
                    $('.xdsoft_datetimepicker').remove();
                }
            });
        });

        </script>
    </head>
    <body>
        <?php include 'header_nav.php'; ?>
        <section id= "services">
            <div class= "container-fluid">
                <div class= "row">
                    <div class= "col-lg-12 col-sm-12 col-xs-12" style='padding-top:100px !important;'>
                        <?php
                            $query  = "SELECT pid, lname, fname FROM patient_data WHERE practice_status ='YES' AND (deceased_stat != 'YES'  OR deceased_date <> NULL OR deceased_date ='0000-00-00 00:00:00') ORDER BY lname, fname ";
                            $ures   = sqlStatement($query);
                            echo "<label>Select Patient: </label>";
                            echo " <select name='form_patient_dropdown' id='form_patient_dropdown' >";
                            echo "<option value=''> Select </option>";
                            while ($urow = sqlFetchArray($ures)) {
                                echo "    <option value='".$urow['pid']."'";
                                if ($urow['pid'] == $pid) echo " selected";
                                echo ">" . $urow['fname'] . ", " . $urow['lname'] . "\n";
                            }
                            echo "</select>\n";
                            echo "<div id='Encounter_result' name='Encounter_result' > </div>";
                        ?>

                    </div>
                </div>
            </div>    
            <div></div>
        </section>
        <?php include 'footer.php'; ?>
        <script type="text/javascript" src="assets/js/jquery.min.js"></script>
        <script type="text/javascript" src="assets/js/bootstrap.min.js"></script>

        <script>
        (function($){
            var ico = $('<i class="fa fa-caret-right"></i>');
            $('nav#menu li:has(ul) > a').append(ico);

            $('nav#menu li:has(ul)').on('click',function(){
                $(this).toggleClass('open');
            });

            $('a#toggle').on('click',function(e){
                $('html').toggleClass('open-menu');
                return false;
            });
            $('div#overlay').on('click',function(){
                $('html').removeClass('open-menu');
            })

        })(jQuery);
        </script>
    </body>
</html>
