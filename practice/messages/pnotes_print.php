<?php
 // Copyright (C) 2011 Cassian LUP <cassi.lup@gmail.com>
 // 
 // Moved out of individual get_* portal functions for re-use by
 // Kevin Yeh (kevin.y@integralemr.com) May 2013
 //
 // This program is free software; you can redistribute it and/or
 // modify it under the terms of the GNU General Public License
 // as published by the Free Software Foundation; either version 2
 // of the License, or (at your option) any later version.
 // 
 // 
 
    // All of the common intialization steps for the get_* patient portal functions are now in this single include.

    //SANITIZE ALL ESCAPES
    $sanitize_all_escapes=true;

    //STOP FAKE REGISTER GLOBALS
    $fake_register_globals=false;

    //continue session
    session_start();

    //landing page definition -- where to go if something goes wrong
    $landingpage = "index.php?site=".$_SESSION['site_id'];	
    //

    // kick out if patient not authenticated
    //if ( isset($_SESSION['uid']) && isset($_SESSION['patient_portal_onsite']) ) {
    if ( isset($_SESSION['portal_username']) ) {    
    $provider = $_SESSION['portal_username'];
    }
    else {
            session_destroy();
    header('Location: '.$landingpage.'&w');
            exit;
    }
    //

    $ignoreAuth=true; // ignore the standard authentication for a regular OpenEMR user
 include_once('../../interface/globals.php');
 require_once("$srcdir/patient.inc");
 require_once("$srcdir/acl.inc");
 require_once("$srcdir/options.inc.php");
 require_once("$srcdir/pnotes.inc");

 $prow = getPatientData($pid, "squad, title, fname, mname, lname");

 // Check authorization.
// $thisauth = acl_check('patients', 'notes');
// if (!$thisauth)
//  die(htmlspecialchars( xl('Not authorized'), ENT_NOQUOTES));
// if ($prow['squad'] && ! acl_check('squads', $prow['squad']))
//  die(htmlspecialchars( xl('Not authorized for this squad.'), ENT_NOQUOTES));

$noteid = $_REQUEST['noteid'];

$ptname = $prow['title'] . ' ' . $prow['fname'] . ' ' . $prow['mname'] .
  ' ' . $prow['lname'];

$title       = '';
$assigned_to = '';
$body        = '';
$activity    = 0;
if ($noteid) {
  $nrow = getPnoteById($noteid, 'title,assigned_to,activity,body');
  $title = $nrow['title'];
  $assigned_to = $nrow['assigned_to'];
  $activity = $nrow['activity'];
  $body = $nrow['body'];
}
?>
<html>
<head>
<?php html_header_show();?>
<link rel='stylesheet' href="<?php echo $css_header;?>" type="text/css">
</head>

<body class="body_top">

<p><?php echo "<b>" .
  generate_display_field(array('data_type'=>'1','list_id'=>'note_type'), $title) .
  "</b>" . htmlspecialchars( xl('for','',' ',' '), ENT_NOQUOTES) .
  "<b>" . htmlspecialchars( $ptname, ENT_NOQUOTES) . "</b>"; ?></p>

<p><?php echo htmlspecialchars( xl('Assigned To'), ENT_NOQUOTES); ?>: <?php echo htmlspecialchars( $assigned_to, ENT_NOQUOTES); ?></p>

<p><?php echo htmlspecialchars( xl('Active'), ENT_NOQUOTES); ?>: <?php echo htmlspecialchars( ($activity ? xl('Yes') : xl('No')), ENT_NOQUOTES); ?></p>

<p><?php echo nl2br(htmlspecialchars( $body, ENT_NOQUOTES)); ?></p>

<script language='JavaScript'>
window.print();
</script>

</body>
</html>
