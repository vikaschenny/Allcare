<?php 

    // Copyright (C) 2011 by following authors:
    //   - Cassian LUP <cassi.lup@gmail.com>
    //
    // This program is free software; you can redistribute it and/or
    // modify it under the terms of the GNU General Public License
    // as published by the Free Software Foundation; either version 2
    // of the License, or (at your option) any later version.

    //SANITIZE ALL ESCAPES
    // (note this is already completed by the script that includes this
    //    get_patient_info.php )

    //STOP FAKE REGISTER GLOBALS
    // (note this is already completed by the script that includes this
    //    get_patient_info.php )

    //continue session
    session_start();

    //landing page definition -- where to go if something goes wrong
    $landingpage = "index.php?site=".$_SESSION['site_id'];
    //
    require_once("../interface/globals.php");

    // kick out if patient not authenticated
    if ( isset($_SESSION['pid']) && isset($_SESSION['patient_portal_onsite']) ) {
      $pid = $_SESSION['pid'];
    }else {
      session_destroy();
      header('Location: '.$landingpage.'&w');
      exit;
    }
    //
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
  
    $ignoreAuth = true;
    global $ignoreAuth;

     
     require_once("$srcdir/patient.inc");
     require_once("$srcdir/acl.inc");
     require_once("$srcdir/classes/Address.class.php");
     require_once("$srcdir/classes/InsuranceCompany.class.php");
     require_once("$srcdir/classes/Document.class.php");
     require_once("$srcdir/options.inc.php");
     require_once("../interface/patient_file/history/history.inc.php");
     require_once("$srcdir/formatting.inc.php");
     require_once("$srcdir/edi.inc");
     include_once("$srcdir/lists.inc");
 
    function showtitleBlock($title,$print=false,$img=""){
        echo "<div class='title'>";
        if($img!="")
          echo"<img class='icon' src=$img alt=''>";
        echo "<h1>$title</h1>";
        
        if($print){
            echo "<div id='assistiveicons'>";
            echo "<a id='printerfriendlylink' href='#' target='_blank' onclick='return false;'><img class='assistiveicon' src='images/print.png' alt='Printer friendly page--New window will open' title='Printer friendly page'></a>";
            echo "<a href='#' onclick='return false;' target='_blank'><img class='assistiveicon' src='images/help.png' alt='Help--New window will open' title='Help'></a>";
            echo "</div>";
        }
        echo "</div>";
    }
    
    function showoption1($title){
        echo "<p class='noprint'>Click on <span class='bold'>About This $title</span> to see additional information regarding a medication.</p>";
        echo "<p class='noprint'>You may want to <a href='#'>request a renewal</a>.</p>";
    }
    $result = getPatientData($pid);
    $_SESSION['password_update']=1;
?>

<!DOCTYPE html>
<html>
    <head>
	<title>Patient Portal</title>
        <?php html_header_show(); ?>
	<meta http-equiv="content-type" content="text/html; charset=windows-1252">
	<meta http-equiv="X-UA-Compatible" content="IE=9">
        <link rel="stylesheet" href="<?php echo $css_header;?>" type="text/css">
        <link rel="stylesheet" type="text/css" href="<?php echo $web_root; ?>/library/dynarch_calendar.css">
        <script type="text/javascript" src="<?php echo $web_root; ?>/library/textformat.js"></script>
        <script type="text/javascript" src="<?php echo $web_root; ?>/library/dynarch_calendar.js"></script>
        <?php include_once("{$GLOBALS['srcdir']}/dynarch_calendar_en.inc.php"); ?>
        <script type="text/javascript" src="<?php echo $web_root; ?>/library/dynarch_calendar_setup.js"></script>
        <script type="text/javascript" src="<?php echo $web_root; ?>/library/dialog.js"></script>
        <script type="text/javascript" src="<?php echo $web_root; ?>/library/js/common.js"></script>
        <link rel="stylesheet" href="css/base.css" type="text/css"/>
        <link rel="stylesheet" href="css/tables.css" type="text/css"/>
	<link type="text/css" rel="stylesheet" href="css/col.css"/>
	<link type="text/css" rel="stylesheet" href="css/theme.css"/>
	<link type="text/css" rel="stylesheet" href="css/ourside.css"/>
	<link type="text/css" rel="stylesheet" href="css/replace.css"/>
        <link type="text/css" rel="stylesheet" href="css/customize.css"/>
        <script type="text/javascript" src="js/jquery-1.7.1.js"></script>
        <script type="text/javascript" src="js/customize.js"></script>
        <script>
            // this function will call when menu options click
            function activemenu(target,contentid,title,file){
                $('#menu div').removeClass('selected');
                $('#menu div a').removeClass('selected');
                $('#menu div a[name='+contentid+']').addClass('selected');
                $('#menu div a[name='+contentid+']').parents('.topmenu').addClass('selected');
                $('#bodycontent > div').hide();
                $('#'+contentid).show();
                $('.title h1').html(title);
                if(file != undefined)
                  $('#'+contentid).load(file);
                
            }

            function refreshme() {
              location.reload();
             }
            function show_date_fun(){
              if(document.getElementById('show_date').checked == true){
                document.getElementById('date_div').style.display = '';
              }else{
                document.getElementById('date_div').style.display = 'none';
              }
              return;
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
        
            $(function(){
                //load labresult
                $("#labresults").load("get_lab_results.php");
                $("#problemlist").load("get_problems.php");
                $("#medicationlist").load("get_medications.php");
                $("#malist").load("get_allergies.php");
                $("#appointments").load("get_appointments.php");
                
                $(".generateCCR").click(function() {
                   if(document.getElementById('show_date').checked == true){
                       if(document.getElementById('Start').value == '' || document.getElementById('End').value == ''){
                           alert('<?php echo addslashes( xl('Please select a start date and end date')) ?>');
                           return false;
                       }
                   }
                   var ccrAction = document.getElementsByName('ccrAction');
                   ccrAction[0].value = 'generate';
                   var raw = document.getElementsByName('raw');
                   raw[0].value = 'no';
                   ccr_form.setAttribute("target", "_blank");
                   $("#ccr_form").submit();
                   ccr_form.setAttribute("target", "");
                });
                $(".generateCCR_raw").click(function() {
                   var ccrAction = document.getElementsByName('ccrAction');
                   ccrAction[0].value = 'generate';
                   var raw = document.getElementsByName('raw');
                   raw[0].value = 'yes';
                   ccr_form.setAttribute("target", "_blank");
                   $("#ccr_form").submit();
                   ccr_form.setAttribute("target", "");
                });
                $(".generateCCR_download_h").click(function() {
                   var ccrAction = document.getElementsByName('ccrAction');
                   ccrAction[0].value = 'generate';
                   var raw = document.getElementsByName('raw');
                   raw[0].value = 'hybrid';
                   $("#ccr_form").submit();
                });
                $(".generateCCR_download_p").click(function() {
                   if(document.getElementById('show_date').checked == true){
                       if(document.getElementById('Start').value == '' || document.getElementById('End').value == ''){
                            alert('<?php echo addslashes( xl('Please select a start date and end date')) ?>');
                           return false;
                       }
                   }
                   var ccrAction = document.getElementsByName('ccrAction');
                   ccrAction[0].value = 'generate';
                   var raw = document.getElementsByName('raw');
                   raw[0].value = 'pure';
                   $("#ccr_form").submit();
                });
                $(".viewCCD").click(function() {
                   var ccrAction = document.getElementsByName('ccrAction');
                   ccrAction[0].value = 'viewccd';
                   var raw = document.getElementsByName('raw');
                   raw[0].value = 'no';
                   ccr_form.setAttribute("target", "_blank");
                   $("#ccr_form").submit();
                   ccr_form.setAttribute("target", "");
                });
                $(".viewCCD_raw").click(function() {
                   var ccrAction = document.getElementsByName('ccrAction');
                   ccrAction[0].value = 'viewccd';
                   var raw = document.getElementsByName('raw');
                   raw[0].value = 'yes';
                   ccr_form.setAttribute("target", "_blank");
                   $("#ccr_form").submit();
                   ccr_form.setAttribute("target", "");
                });
                $(".viewCCD_download").click(function() {
                   var ccrAction = document.getElementsByName('ccrAction');
                   ccrAction[0].value = 'viewccd';
                   var raw = document.getElementsByName('raw');
                   raw[0].value = 'pure';
                   $("#ccr_form").submit();
                });
                <?php if ($GLOBALS['phimail_enable']==true && $GLOBALS['phimail_ccr_enable']==true) { ?>
                    $(".viewCCR_send_dialog").click(function() {
                        $("#ccr_send_dialog").toggle();
                    });
                    $(".viewCCR_transmit").click(function() {
                        $(".viewCCR_transmit").attr('disabled','disabled');
                        var ccrAction = document.getElementsByName('ccrAction');
                        ccrAction[0].value = 'generate';
                        var ccrRecipient = $("#ccr_send_to").val();
                        var raw = document.getElementsByName('raw');
                        raw[0].value = 'send '+ccrRecipient;
                        if(ccrRecipient=="") {
                            $("#ccr_send_message").html("<?php echo htmlspecialchars(xl('Please enter a valid Direct Address above.'), ENT_QUOTES);?>");
                            $("#ccr_send_result").show();
                        }else {
                            $(".viewCCR_transmit").attr('disabled','disabled');
                            $("#ccr_send_message").html("<?php echo htmlspecialchars(xl('Working... this may take a minute.'), ENT_QUOTES);?>");
                            $("#ccr_send_result").show();
                            var action=$("#ccr_form").attr('action');
                          $.post(action, {ccrAction:'generate',raw:'send '+ccrRecipient,requested_by:'patient'},
                            function(data) {
                                if(data=="SUCCESS") {
                                    $("#ccr_send_message").html("<?php echo htmlspecialchars(xl('Your message was submitted for delivery to'), ENT_QUOTES); ?> "+ccrRecipient);
                                    $("#ccr_send_to").val("");
                                } else {
                                    $("#ccr_send_message").html(data);
                                }
                                $(".viewCCR_transmit").removeAttr('disabled');
                            });
                        }
                    });
           <?php }
           if ($GLOBALS['phimail_enable']==true && $GLOBALS['phimail_ccd_enable']==true) { ?>
                $(".viewCCD_send_dialog").click(function() {
                    $("#ccd_send_dialog").toggle();
                });
                $(".viewCCD_transmit").click(function() {
                    $(".viewCCD_transmit").attr('disabled','disabled');
                    var ccrAction = document.getElementsByName('ccrAction');
                    ccrAction[0].value = 'viewccd';
                    var ccdRecipient = $("#ccd_send_to").val();
                    var raw = document.getElementsByName('raw');
                    raw[0].value = 'send '+ccdRecipient;
                    if(ccdRecipient=="") {
                        $("#ccd_send_message").html("<?php
                        echo htmlspecialchars(xl('Please enter a valid Direct Address above.'), ENT_QUOTES);?>");
                        $("#ccd_send_result").show();
                    } else {
                        $(".viewCCD_transmit").attr('disabled','disabled');
                        $("#ccd_send_message").html("<?php echo htmlspecialchars(xl('Working... this may take a minute.'), ENT_QUOTES);?>");
                        $("#ccd_send_result").show();
                        var action=$("#ccr_form").attr('action');
                        $.post(action, {ccrAction:'viewccd',raw:'send '+ccdRecipient,requested_by:'patient'},
                            function(data) {
                                if(data=="SUCCESS") {
                                    $("#ccd_send_message").html("<?php echo htmlspecialchars(xl('Your message was submitted for delivery to'), ENT_QUOTES); ?> "+ccdRecipient);
                                    $("#ccd_send_to").val("");
                                } else {
                                    $("#ccd_send_message").html(data);
                                }
                                $(".viewCCD_transmit").removeAttr('disabled');
                            }
                        );
                    }
                });
    <?php } ?>
 });
            
        </script>
    </head>
<body class="inside menuontop color1 md_medslist">
    <div id="skipnavigation">
        <a id="skiplink" href="#maintop">Skip navigation to main content</a>
    </div>
    <div id="wrap"><!-- Begin Site Wrapper -->
        <div id="banner" role="banner"><!-- Begin Banner -->
            <a href="#" title="MyChart">
                <img src="images/banner.png" alt="logo">
            </a>
            &nbsp;
            <div id="bannertext">
                    <span class="welcome">Welcome,</span>
                    <br>
                    <?php echo htmlspecialchars($result['fname']." ".$result['lname'],ENT_NOQUOTES); ?>
            </div>
            <div class="blockbgclicks" role="navigation">
                <a id="btnLogout" class="gradientbutton" href="#" onclick="window.location='logout.php'">Log Out</a>
            </div>
        </div>
        <ul id="proxyTabs" class="noprint">
                <li class="color1 curr">
                    <a href="#" onclick="activemenu(this,'home','Home'); return false;" title="Select to access the record of Suresh">
                        <img id="photoImg_t0" src="images/ProxySilhouette.png" class="photo color1 disabled" alt="">
                        <span class="tabname"><?php echo htmlspecialchars($result['fname']); ?></span>
                    </a>
                </li>
                <li id="configIconTab" class="config">
                    <a id="wrenchLink" href="#" onclick="activemenu(this,'changepassword','Change Password'); return false;">
                        <span><img title="Edit Personalization Options" alt="Edit Personalization Options" src="images/wrench.png"></span>
                    </a>
                </li>
        </ul>
        <div style="visibility: visible;" id="menu" role="navigation" class="collapsible"><!-- Begin Menu -->
            <div class="menugroup home topmenu selected"><span class="mnutitle"><a href="#" name ="home" onclick="activemenu(this,'home','Home'); return false;"><img src="images/menuhome.png" alt="">Home</a></span>
            </div>
            <div class="menugroup topmenu">
                <span class="mnutitle"><a href="#" onclick="return false;"><img src="images/medical_record_menu.png" alt="">My Reports</a></span>
                <ul>
                    <li><a href="#" name ="reports" onclick="activemenu(this,'reports','Reports'); return false;">My Reports</a></li>
                </ul>
            </div>
            
            <div class="menugroup topmenu">
                <span class="mnutitle"><a href="#" onclick="return false;"><img src="images/inbox_menu.png" alt="">Lab Test Results</a></span>
                <ul>
                    <li><a href="#" name ="labresults" onclick="activemenu(this,'labresults','Lab Test Results','get_lab_results.php'); return false;">Lab Test Results</a></li>
                </ul>
            </div>
            <div class="menugroup topmenu">
                <span class="mnutitle"><a href="#" onclick="return false;"><img src="images/billing_menu.png" alt="">Issues</a></span>
                <ul>
                    <li><a href="#" name ="problemlist" onclick="activemenu(this,'problemlist','Problem List','get_problems.php'); return false;">Problem List</a></li>
                    <li><a href="#" name ="medicationlist" onclick="activemenu(this,'medicationlist','Medication List','get_medications.php'); return false;">Medication List</a></li>
                    <li><a href="#" name ="malist" onclick="activemenu(this,'malist','Medication Allergy List','get_allergies.php'); return false;">Medication Allergy List</a></li>
                </ul>
            </div>
            
            <div class="menugroup moremenu topmenu">
                <span class="mnutitle"><a href="#" onclick="return false;"><img src="images/appointments_menu.png" alt="">Appointments</a></span>
                 <ul>
                    <li><a href="#" name ="appointments" onclick="activemenu(this,'appointments','Appointments','get_appointments.php'); return false;">Appointments</a></li>
                </ul>
                <!--<div class="hiddenlist">

                    <div class="menugroup">
                        <span class="mnutitle"><a href="#" onclick="return false;"><img src="images/appointments_menu.png" alt="">Appointments</a></span>

                        <ul>
                            <li><a href="#" onclick="activemenu(this)">Upcoming <abbr title="Appointments">Appts</abbr></a></li>
                            <li><a href="#" onclick="activemenu(this)">Cancel an <abbr title="Appointments">Appt</abbr></a></li>
                            <li><a href="#" onclick="activemenu(this)">Request an <abbr title="Appointment">Appt</abbr></a></li>
                            <li><a href="#" onclick="activemenu(this)">Schedule an <abbr title="Appointment">Appt</abbr></a></li>
                        </ul>
                    </div>
                    <div class="menugroup">
                        <span class="mnutitle"><a href="#" onclick="return false;"><img src="images/appointments_past_menu.png" alt="">Visit History</a></span>
                        <ul>
                            <li><a href="#" onclick="activemenu(this)">Past Appts</a></li>
                        </ul>
                    </div>
                </div>-->
            </div>
            <div class="menugroup topmenu">
                    <span class="mnutitle"><a href="#" onclick="return false;"><img src="images/preferences_menu.png" alt="">My Account</a></span>
                    <ul>
                        <li><a href="#" name ="changepassword" onclick="activemenu(this,'changepassword','Change Password'); return false;">Change Password</a></li>
                    </ul>
            </div>
        <div class="separator"></div>	<a class="hidden" href="#menu">Menu Top</a>
    </div>    <!-- End Menu -->
    
    <div style="min-height: 305px;" id="main" role="main"><!-- Begin Main -->
        <?php showtitleBlock("Home",false,"images/prescriptions.png"); ?>
        <div id="bodycontent">
            <div id="reports"><!-- begin Reports -->
                <div style='padding:1em;' class='text'>
                   <div id="ccr_report">
                    <form name='ccr_form' id='ccr_form' method='post' action='../ccr/createCCR.php?portal_auth=1'>
                    <span class='text'><b><?php echo htmlspecialchars( xl('Continuity of Care Record (CCR)'), ENT_NOQUOTES); ?></b></span>&nbsp;&nbsp;
                    <br/>
                    <span class='text'>(<?php echo htmlspecialchars( xl('Pop ups need to be enabled to see these reports'), ENT_NOQUOTES); ?>)</span>
                    <br/>
                    <br/>
                    <input type='hidden' name='ccrAction'>
                    <input type='hidden' name='raw'>
                    <input type="checkbox" name="show_date" id="show_date" onchange="show_date_fun();" ><span class='text'><?php echo htmlspecialchars( xl('Use Date Range'), ENT_NOQUOTES); ?>
                    <br>
                    <div id="date_div" style="display:none" >
                     <br>
                     <table border="0" cellpadding="0" cellspacing="0" >
                      <tr>
                       <td>
                        <span class='bold'><?php echo htmlspecialchars( xl('Start Date'), ENT_NOQUOTES);?>: </span>
                       </td>
                       <td>
                        <input type='text' size='10' name='Start' id='Start'
                        onkeyup='datekeyup(this,mypcc)' onblur='dateblur(this,mypcc)'
                        title='<?php echo htmlspecialchars( xl('yyyy-mm-dd'), ENT_QUOTES); ?>' />
                        <img src='../interface/pic/show_calendar.gif' align='absbottom' width='24' height='22'
                        id='img_start' border='0' alt='[?]' style='cursor:pointer'
                        title='<?php echo htmlspecialchars( xl('Click here to choose a date'), ENT_QUOTES); ?>' >
                        <script LANGUAGE="JavaScript">
                         Calendar.setup({inputField:"Start", ifFormat:"%Y-%m-%d", button:"img_start"});
                        </script>
                       </td>
                       <td>
                        &nbsp;
                        <span class='bold'><?php echo htmlspecialchars( xl('End Date'), ENT_NOQUOTES);?>: </span>
                       </td>
                       <td>
                        <input type='text' size='10' name='End' id='End'
                        onkeyup='datekeyup(this,mypcc)' onblur='dateblur(this,mypcc)'
                        title='<?php echo htmlspecialchars( xl('yyyy-mm-dd'), ENT_QUOTES); ?>' />
                        <img src='../interface/pic/show_calendar.gif' align='absbottom' width='24' height='22'
                        id='img_end' border='0' alt='[?]' style='cursor:pointer'
                        title='<?php echo htmlspecialchars( xl('Click here to choose a date'), ENT_QUOTES); ?>' >
                        <script LANGUAGE="JavaScript">
                         Calendar.setup({inputField:"End", ifFormat:"%Y-%m-%d", button:"img_end"});
                        </script>
                       </td>
                      </tr>
                     </table>
                    </div>
                    <br>
                    <input type="button" class="generateCCR" value="<?php echo htmlspecialchars( xl('View/Print'), ENT_QUOTES); ?>" />
                    <!-- <input type="button" class="generateCCR_download_h" value="<?php echo htmlspecialchars( xl('Download'), ENT_QUOTES); ?>" /> -->
                    <input type="button" class="generateCCR_download_p" value="<?php echo htmlspecialchars( xl('Download'), ENT_QUOTES); ?>" />
                    <!-- <input type="button" class="generateCCR_raw" value="<?php echo htmlspecialchars( xl('Raw Report'), ENT_QUOTES); ?>" /> -->
       <?php if ($GLOBALS['phimail_enable']==true && $GLOBALS['phimail_ccr_enable']==true) { ?>
                    <input type="button" class="viewCCR_send_dialog" value="<?php echo htmlspecialchars( xl('Transmit', ENT_QUOTES)); ?>" />
                    <br>
                    <div id="ccr_send_dialog" style="display:none" >
                     <br>
                     <table border="0" cellpadding="0" cellspacing="0" >
                      <tr>
                       <td>
                        <span class='bold'><?php echo htmlspecialchars( xl('Enter Recipient\'s Direct Address'), ENT_NOQUOTES);?>: </span>
                       <input type="text" size="64" name="ccr_send_to" id="ccr_send_to" value="">
                       <input type="button" class="viewCCR_transmit" value="<?php echo htmlspecialchars( xl('Send', ENT_QUOTES)); ?>" />
                       <div id="ccr_send_result" style="display:none" >
                        <span class="text" id="ccr_send_message"></span>
                       </div>
                       </td>
                     </tr>
                     </table>
                    </div>
       <?php } ?>   <br/><br/>
                    <span class='text'><b><?php echo htmlspecialchars( xl('Continuity of Care Document (CCD)'), ENT_NOQUOTES); ?></b></span>&nbsp;&nbsp;
                    <br/>
                    <span class='text'>(<?php echo htmlspecialchars( xl('Pop ups need to be enabled to see these reports'), ENT_NOQUOTES); ?>)</span>
                    <br/>
                    <br/>
                    <input type="button" class="viewCCD" value="<?php echo htmlspecialchars( xl('View/Print', ENT_QUOTES)); ?>" />
                    <input type="button" class="viewCCD_download" value="<?php echo htmlspecialchars( xl('Download', ENT_QUOTES)); ?>" />
                    <!-- <input type="button" class="viewCCD_raw" value="<?php echo htmlspecialchars( xl('Raw Report', ENT_QUOTES)); ?>" /> -->
       <?php if ($GLOBALS['phimail_enable']==true && $GLOBALS['phimail_ccd_enable']==true) { ?>
                    <input type="button" class="viewCCD_send_dialog" value="<?php echo htmlspecialchars( xl('Transmit', ENT_QUOTES)); ?>" />
                    <br>
                    <div id="ccd_send_dialog" style="display:none" >
                     <br>
                     <table border="0" cellpadding="0" cellspacing="0" >
                      <tr>
                       <td>
                        <span class='bold'><?php echo htmlspecialchars( xl('Enter Recipient\'s Direct Address'), ENT_NOQUOTES);?>: </span>
                       <input type="text" size="64" name="ccd_send_to" id="ccd_send_to" value="">
                       <input type="button" class="viewCCD_transmit" value="<?php echo htmlspecialchars( xl('Send', ENT_QUOTES)); ?>" />
                       <div id="ccd_send_result" style="display:none" >
                        <span class="text" id="ccd_send_message"></span>
                       </div>
                       </td>
                     </tr>
                     </table>
                    </div>
       <?php } ?>
                   </form>
                  </div>
                 </div>
            </div><!--end Reports -->
            <div id="home"><!-- begin home-->
                <p>Home page content</p>
            </div><!-- End  Home -->
            <div id="labresults"><!-- begin lab Results -->
            </div><!-- end lab Results -->
            <div id="problemlist"><!-- begin Problem List -->
            </div><!-- End Problem List -->
            <div id="medicationlist"><!-- begin Medication List -->
            </div><!-- End Medication List -->
            <div id="malist"><!-- begin Medication Allergy List-->
            </div><!-- End Medication Allergy List -->
            <div id="appointments"><!-- begin Appointments List-->
            </div><!-- End  Appointments List -->
            <div id="changepassword"><!-- begin Change password-->
                <form action="get_patient_info.php?password=updated" method="POST" onsubmit="return process_new_pass()" >
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
                            <td colspan=2><br><center><input type="submit" value="<?php echo htmlspecialchars( xl('Save'), ENT_QUOTES);?>" /></center></td>
                        </tr>
                    </table>
                </form>
            </div><!-- End  Change password -->
        </div>
        
	<div class="back">
            <a class="gradientbutton" href="#" onclick="activemenu(this,'home','Home'); return false;" title="Back">Back to the Home Page</a>
	</div>
            
</div><!-- End Main -->
<div class="" id="blftwrapper" role="contentinfo"><!-- Begin Footer -->
    <div id="footer">
        <p>Copyright  &copy;2015 SmartMBBS.</p>
    </div>
</div><!-- End Footer -->
</body>
</html>