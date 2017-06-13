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

  require_once("$srcdir/htmlspecialchars.inc.php");
  require_once("$srcdir/acl.inc");    
  require_once("$srcdir/dated_reminder_functions.php"); 
  
  
  $isAdmin =acl_check('admin', 'users'); 
?>
<?php
  /*
    -------------------  HANDLE POST ---------------------
  */

 $sql=sqlStatement("SELECT id, fname, lname, specialty FROM users " .
      "WHERE active = 1 AND ( info IS NULL OR info NOT LIKE '%Inactive%' ) " .
      "AND authorized = 1 AND username='".$provider."'" .
      "ORDER BY lname, fname");
$id=sqlFetchArray($sql);
$id1=$id['id'];
$_SESSION['authId']=$id1;
  if($_GET){
   if(!$isAdmin){ 
      if(empty($_GET['sentBy']) and empty($_GET['sentTo']))
        $_GET['sentTo'] = array(intval($_SESSION['authId'])); 
  }  
    echo '<table border="1" width="100%" cellpadding="5px" id="logTable">
            <thead>
              <tr>
                <th>'.xlt('ID').'</th>
                <th>'.xlt('Sent Date').'</th>
                <th>'.xlt('From').'</th>
                <th>'.xlt('To').'</th>
                <th>'.xlt('Patient').'</th>
                <th>'.xlt('Message').'</th>
                <th>'.xlt('Due Date').'</th>
                <th>'.xlt('Processed Date').'</th>
                <th>'.xlt('Processed By').'</th>
              </tr>
            </thead>
            <tbody>';
    $remindersArray = array();
    $TempRemindersArray = logRemindersArray(); 
    foreach($TempRemindersArray as $RA){
      $remindersArray[$RA['messageID']]['messageID'] = $RA['messageID']; 
      $remindersArray[$RA['messageID']]['ToName'] = ($remindersArray[$RA['messageID']]['ToName'] ? $remindersArray[$RA['messageID']]['ToName'].', '.$RA['ToName'] : $RA['ToName']);
      $remindersArray[$RA['messageID']]['PatientName'] = $RA['PatientName'];
      $remindersArray[$RA['messageID']]['message'] = $RA['message'];   
      $remindersArray[$RA['messageID']]['dDate'] = $RA['dDate'];       
      $remindersArray[$RA['messageID']]['sDate'] = $RA['sDate'];  
      $remindersArray[$RA['messageID']]['pDate'] = $RA['pDate'];  
      $remindersArray[$RA['messageID']]['processedByName'] = $RA['processedByName'];   
      $remindersArray[$RA['messageID']]['fromName'] = $RA['fromName']; 
    }
    foreach($remindersArray as $RA){ 
      echo '<tr class="heading">
              <td>',text($RA['messageID']),'</td>
              <td>',text($RA['sDate']),'</td>
              <td>',text($RA['fromName']),'</td>
              <td>',text($RA['ToName']),'</td>
              <td>',text($RA['PatientName']),'</td>     
              <td>',text($RA['message']),'</td>    
              <td>',text($RA['dDate']),'</td>    
              <td>',text($RA['pDate']),'</td>      
              <td>',text($RA['processedByName']),'</td>
            </tr>';
    }
    echo '</tbody></table>'; 
    
    die;
  }
?> 
<html>
  <head>                                    
    <link rel="stylesheet" href="<?php echo $css_header;?>" type="text/css"> 
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/jquery.js"></script>  
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/jquery-calendar.js"></script>   
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/jquery.grouprows.js"></script>     
    <script src="<?php echo $GLOBALS['webroot'] ?>/library/js/grouprows.js"></script> 
    <script language="JavaScript">   
      $(document).ready(function (){  
        $("#submitForm").click(function(){ 
          // top.restoreSession(); --> can't use this as it negates this ajax refresh
          $.get("dated_reminders_log.php?"+$("#logForm").serialize(), 
               function(data) {
                  $("#resultsDiv").html(data);
                  <?php
                    if(!$isAdmin){
                      echo '$("select option").removeAttr("selected");';
                    } 
                  ?>  
                	return false;
               }
             )   
          return false;
        })
      }) 
    </script> 
  </head>
  <body class="body_top"> 
<!-- Required for the popup date selectors -->
<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>

             
<?php     
  $allUsers = array(); 
  $uSQL = sqlStatement('SELECT id, fname,	mname, lname  FROM  `users` WHERE  `active` = 1 AND `facility_id` > 0 AND id != ?',array(intval($_SESSION['authId'])));
  for($i=0; $uRow=sqlFetchArray($uSQL); $i++){ $allUsers[] = $uRow; } 
?>     
    <form method="get" id="logForm" onsubmit="return top.restoreSession()">         
      <h1><?php echo xlt('Dated Message Log') ?></h1>  
      <h2><?php echo xlt('filters') ?> :</h2>
      <blockquote><?php echo xlt('Date The Message Was Sent') ?><br />
<!----------------------------------------------------------------------------------------------------------------------------------------------------->  
      <?php echo xlt('Start Date') ?> : <input id="sd" type="text" name="sd" value="" onkeyup='datekeyup(this,mypcc)' onblur='dateblur(this,mypcc)' title='<?php echo xla('yyyy-mm-dd'); ?>' />   &nbsp;&nbsp;&nbsp;  
<!----------------------------------------------------------------------------------------------------------------------------------------------------->   
      <?php echo xlt('End Date') ?> : <input id="ed" type="text" name="ed" value="" onkeyup='datekeyup(this,mypcc)' onblur='dateblur(this,mypcc)' title='<?php echo xla('yyyy-mm-dd'); ?>' />   <br /><br />
<!----------------------------------------------------------------------------------------------------------------------------------------------------->   
      </blockquote>
      <table style="width:100%">
        <tr>
          <td style="width:50%">
            <?php echo xlt('Sent By, Leave Blank For All') ?> : <br />                                    
            <select style="width:100%;" id="sentBy" name="sentBy[]" multiple="multiple">
              <option value="<?php echo attr(intval($_SESSION['authId'])); ?>"><?php echo xlt('Myself') ?></option>
              <?php  
                if($isAdmin)    
                  foreach($allUsers as $user)
                    echo '<option value="',attr($user['id']),'">',text($user['fname'].' '.$user['mname'].' '.$user['lname']),'</option>'; 
              ?>
            </select>   
          </td>
          <td style="width:50%">
            <?php echo xlt('Sent To, Leave Blank For All') ?> : <br />      
            <select style="width:100%" id="sentTo" name="sentTo[]" multiple="multiple">    
              <option value="<?php echo attr(intval($_SESSION['authId'])); ?>"><?php echo xlt('Myself') ?></option>
              <?php                    
                if($isAdmin)
                  foreach($allUsers as $user) 
                    echo '<option value="',attr($user['id']),'">',text($user['fname'].' '.$user['mname'].' '.$user['lname']),'</option>';  
              ?>
            </select>  
          </td>
        </tr>
      </table>
<!-----------------------------------------------------------------------------------------------------------------------------------------------------> 
      <input type="checkbox" name="processed" id="processed"><label for="processed"><?php echo xlt('Processed') ?></label>      
<!-----------------------------------------------------------------------------------------------------------------------------------------------------> 
      <input type="checkbox" name="pending" id="pending"><label for="pending"><?php echo xlt('Pending') ?></label>          
<!----------------------------------------------------------------------------------------------------------------------------------------------------->  
      <br /><br />  
      <button value="Refresh" id="submitForm"><?php echo xlt('Refresh') ?></button>
    </form>
    
    <div id="resultsDiv"></div> 
 
  </body> 
<!-- stuff for the popup calendar -->
<style type="text/css">@import url(<?php echo $GLOBALS['webroot'] ?>/library/dynarch_calendar.css);</style>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/dynarch_calendar.js"></script>
<?php include_once("{$GLOBALS['srcdir']}/dynarch_calendar_en.inc.php"); ?>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/dynarch_calendar_setup.js"></script>
<script language="Javascript"> 
  Calendar.setup({inputField:"sd", ifFormat:"%Y-%m-%d", button:"img_begin_date", showsTime:'false'});  
  Calendar.setup({inputField:"ed", ifFormat:"%Y-%m-%d", button:"img_begin_date", showsTime:'false'}); 
</script>
</html> 
