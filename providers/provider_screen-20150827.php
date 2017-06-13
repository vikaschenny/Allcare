<?php
/**
 * The outside frame that holds all of the OpenEMR User Interface.
 *
 * LICENSE: This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://opensource.org/licenses/gpl-license.php>;.
 *
 * @package OpenEMR
 * @author  Brady Miller <brady@sparmy.com>
 * @link    http://www.open-emr.org
 */

$fake_register_globals=false;
$sanitize_all_escapes=true;

/* Include our required headers */
require_once('../interface/globals.php');
require_once("$srcdir/formdata.inc.php");

// Creates a new session id when load this outer frame
// (allows creations of separate OpenEMR frames to view patients concurrently
//  on different browser frame/windows)
// This session id is used below in the restoreSession.php include to create a
// session cookie for this specific OpenEMR instance that is then maintained
// within the OpenEMR instance by calling top.restoreSession() whenever
// refreshing or starting a new script.
//echo $_POST['authUser'];
//echo $_POST['clearPass'];
if(isset($_POST['authUser'])!='' && isset($_POST['clearPass'])!='') {
    $sql=sqlStatement("SELECT id, fname, lname, specialty FROM users " .
      "WHERE active = 1 AND ( info IS NULL OR info NOT LIKE '%Inactive%' ) " .
      "AND authorized = 1 AND username='".$_POST['authUser']."'" .
      "ORDER BY lname, fname");
$id=sqlFetchArray($sql);
if(empty($id)){
     header('Location: ../providers/login_test.php?site=default');
}
if (isset($_POST['new_login_session_management'])) {
  // This is a new login, so create a new session id and remove the old session
  session_regenerate_id(true);
}
else {
  // This is not a new login, so create a new session id and do NOT remove the old session
  session_regenerate_id(false);
}

$_SESSION["encounter"] = '';

// Fetch the password expiration date
$is_expired=false;
if($GLOBALS['password_expiration_days'] != 0){
  $is_expired=false;
  $q= (isset($_POST['authUser'])) ? $_POST['authUser'] : '';
  $result = sqlStatement("select pwd_expiration_date from users where username = ?", array($q));
  $current_date = date('Y-m-d');
  $pwd_expires_date = $current_date;
  if($row = sqlFetchArray($result)) {
    $pwd_expires_date = $row['pwd_expiration_date'];
  }

  // Display the password expiration message (starting from 7 days before the password gets expired)
  $pwd_alert_date = date('Y-m-d', strtotime($pwd_expires_date . '-7 days'));

  if (strtotime($pwd_alert_date) != '' &&
      strtotime($current_date) >= strtotime($pwd_alert_date) &&
      (!isset($_SESSION['expiration_msg'])
      or $_SESSION['expiration_msg'] == 0)) {
    $is_expired = true;
    $_SESSION['expiration_msg'] = 1; // only show the expired message once
  }
}

if ($is_expired) {
  //display the php file containing the password expiration message.
  $frame1url = "pwd_expires_alert.php";
}
else if (!empty($_POST['patientID'])) {
  $patientID = 0 + $_POST['patientID'];
  $frame1url = "../interface/patient_file/summary/demographics.php?set_pid=".attr($patientID);
}
else if ($GLOBALS['athletic_team']) {
  $frame1url = "../interface/reports/players_report.php?embed=1";
}
else if (isset($_GET['mode']) && $_GET['mode'] == "loadcalendar") {
  $frame1url = "../interface/main/calendar/index.php?pid=" . attr($_GET['pid']);
  if (isset($_GET['date'])) $frame1url .= "&date=" . attr($_GET['date']);
}
else if ($GLOBALS['concurrent_layout']) {
  // new layout
  if ($GLOBALS['default_top_pane']) {
    $frame1url=attr($GLOBALS['default_top_pane']);
  } else {
    $frame1url = "../interface/main/main_info.php";
  }
}
else {
  // old layout
   // echo "hi"; exit;
  $frame1url = "../interface/main/main.php?mode=" . attr($_GET['mode']);
}

$nav_area_width = $GLOBALS['athletic_team'] ? '230' : '130';
if (!empty($GLOBALS['gbl_nav_area_width'])) $nav_area_width = $GLOBALS['gbl_nav_area_width'];
?>
<html>
<head>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
<script type="text/javascript">
    $('#frameset').load( function() {
        alert("entered");
    $('#iframe').contents().find("head").append($("<style type='text/css'>  .body_top{background-color:none !important;}  </style>"));
    alert("leaved");
});

</script>
<title>
<?php echo text($openemr_name) ?>
</title>
<script type="text/javascript" src="../library/topdialog.js"></script>

<script language='JavaScript'>
<?php require($GLOBALS['srcdir'] . "/restoreSession.php"); ?>

// This counts the number of frames that have reported themselves as loaded.
// Currently only left_nav and Title do this, so the maximum will be 2.
// This is used to determine when those frames are all loaded.
var loadedFrameCount = 0;

function allFramesLoaded() {
 // Change this number if more frames participate in reporting.
 return loadedFrameCount >= 2;
}
</script>
<style>
    .center {
    margin: auto;
    width: 80%;
    padding: 10px;
} 
</style>
</head>

<?php

// Please keep in mind that border (mozilla) and framespacing (ie) are the
// same thing. use both.
// frameborder specifies a 3d look, not whether there are borders.

if ($GLOBALS['concurrent_layout']) {
  // start new layout
  if (empty($GLOBALS['gbl_tall_nav_area'])) {
    // not tall nav area ?>
<frameset rows='20%,78%'  frameborder='1' border='1' framespacing='1' onunload='imclosing()'>
 <frame src='provider_menu_list.php?provider_id=<?php echo $_POST['authUser']; ?>' name='Daemon' scrolling='no' frameborder='0'
    border='0' framespacing='0' />
<frame src="home.php" id="iframe"   name="content"/>
</frameset>

<?php } 
 } 
//else { // start old layout ?> 

</html>
<?php } else {
header('Location: ../providers/index.php?site=default'); }
?>