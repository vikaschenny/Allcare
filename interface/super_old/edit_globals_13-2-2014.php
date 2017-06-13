<?php
// Copyright (C) 2010 Rod Roark <rod@sunsetsystems.com>
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.

require_once("../globals.php");
require_once("$srcdir/acl.inc");
require_once("$srcdir/formdata.inc.php");
require_once("$srcdir/globals.inc.php");
require_once("$srcdir/user.inc");
require_once("$srcdir/classes/CouchDB.class.php");

if ($_GET['mode'] != "user") {
  // Check authorization.
  $thisauth = acl_check('admin', 'super');
  if (!$thisauth) die(xl('Not authorized'));
}

function checkCreateCDB(){
  $globalsres = sqlStatement("SELECT gl_name, gl_index, gl_value FROM globals WHERE gl_name IN 
  ('couchdb_host','couchdb_user','couchdb_pass','couchdb_port','couchdb_dbase','document_storage_method')");
    $options = array();
    while($globalsrow = sqlFetchArray($globalsres)){
      $GLOBALS[$globalsrow['gl_name']] = $globalsrow['gl_value'];
    }
    $directory_created = false;
  if($GLOBALS['document_storage_method'] != 0){
    // /documents/temp/ folder is required for CouchDB
    if(!is_dir($GLOBALS['OE_SITE_DIR'] . '/documents/temp/')){
      $directory_created = mkdir($GLOBALS['OE_SITE_DIR'] . '/documents/temp/',0777,true);      
      if(!$directory_created){
	echo htmlspecialchars( xl("Failed to create temporary folder. CouchDB will not work."),ENT_NOQUOTES);
      }
    }
        $couch = new CouchDB();
    if(!$couch->check_connection()) {
      echo "<script type='text/javascript'>alert('".addslashes(xl("CouchDB Connection Failed."))."');</script>";
      return;
    }
    if($GLOBALS['couchdb_host'] || $GLOBALS['couchdb_port'] || $GLOBALS['couchdb_dbase']){
      $couch->createDB($GLOBALS['couchdb_dbase']);
      $couch->createView($GLOBALS['couchdb_dbase']);
    }
  }
  return true;
}

/**
 * Update background_services table for a specific service following globals save.
 * @author EMR Direct
 */
function updateBackgroundService($name,$active,$interval) {
   //order important here: next_run change dependent on _old_ value of execute_interval so it comes first
   $sql = 'UPDATE background_services SET active=?, '
	. 'next_run = next_run + INTERVAL (? - execute_interval) MINUTE, execute_interval=? WHERE name=?';
   return sqlStatement($sql,array($active,$interval,$interval,$name));
}

/**
 * Make any necessary changes to background_services table when globals are saved.
 * To prevent an unexpected service call during startup or shutdown, follow these rules:
 * 1. Any "startup" operations should occur _before_ the updateBackgroundService() call.
 * 2. Any "shutdown" operations should occur _after_ the updateBackgroundService() call. If these operations
 * would cause errors in a running service call, it would be best to make the shutdown function itself
 * a background service that is activated here, does nothing if active=1 or running=1 for the
 * parent service, then deactivates itself by setting active=0 when it is done shutting the parent service
 * down. This will prevent nonresponsiveness to the user by waiting for a service to finish.
 * 3. If any "previous" values for globals are required for startup/shutdown logic, they need to be
 * copied to a temp variable before the while($globalsrow...) loop.
 * @author EMR Direct
 */
function checkBackgroundServices(){
  //load up any necessary globals
  $bgservices = sqlStatement("SELECT gl_name, gl_index, gl_value FROM globals WHERE gl_name IN
  ('phimail_enable','phimail_interval')");
    while($globalsrow = sqlFetchArray($bgservices)){
      $GLOBALS[$globalsrow['gl_name']] = $globalsrow['gl_value'];
    }

   //Set up phimail service
   $phimail_active = $GLOBALS['phimail_enable'] ? '1' : '0';
   $phimail_interval = max(0,(int)$GLOBALS['phimail_interval']);
   updateBackgroundService('phimail',$phimail_active,$phimail_interval);
}
?>

<html>

<head>
<?php

html_header_show();

// If we are saving user_specific globals.
//
if ($_POST['form_save'] && $_GET['mode'] == "user") {
  $i = 0;
  foreach ($GLOBALS_METADATA as $grpname => $grparr) {
    if (in_array($grpname, $USER_SPECIFIC_TABS)) {
      foreach ($grparr as $fldid => $fldarr) {
        if (in_array($fldid, $USER_SPECIFIC_GLOBALS)) {
          list($fldname, $fldtype, $flddef, $flddesc) = $fldarr;
          $label = "global:".$fldid;
          $fldvalue = trim(strip_escape_custom($_POST["form_$i"]));
          setUserSetting($label,$fldvalue,$_SESSION['authId'],FALSE);
          if ( $_POST["toggle_$i"] == "YES" ) {
            removeUserSetting($label);
          }
          ++$i;
        }
      }
    }
  }
  echo "<script type='text/javascript'>";
  echo "parent.left_nav.location.reload();";
  echo "parent.Title.location.reload();";
  echo "if(self.name=='RTop'){";
  echo "parent.RBot.location.reload();";
  echo "}else{";
  echo "parent.RTop.location.reload();";
  echo "}";
  echo "self.location.href='edit_globals.php?mode=user&unique=yes';";
  echo "</script>";
}

// If we are saving main globals.
//
if ($_POST['form_save'] && $_GET['mode'] != "user") {

  $i = 0;
  foreach ($GLOBALS_METADATA as $grpname => $grparr) {
    foreach ($grparr as $fldid => $fldarr) {
      list($fldname, $fldtype, $flddef, $flddesc) = $fldarr;
	  if($fldtype == 'pwd'){
	  $pass = sqlQuery("SELECT gl_value FROM globals WHERE gl_name = '$fldid'");
	  $fldvalueold = $pass['gl_value'];
	  }
      sqlStatement("DELETE FROM globals WHERE gl_name = '$fldid'");

      if (substr($fldtype, 0, 2) == 'm_') {
        if (isset($_POST["form_$i"])) {
          $fldindex = 0;
          foreach ($_POST["form_$i"] as $fldvalue) {
            $fldvalue = formDataCore($fldvalue, true);
            sqlStatement("INSERT INTO globals ( gl_name, gl_index, gl_value ) " .
              "VALUES ( '$fldid', '$fldindex', '$fldvalue' )");
            ++$fldindex;
          }
        }
      }
      else {
        if (isset($_POST["form_$i"])) {
          $fldvalue = formData("form_$i", "P", true);
        }
        else {
          $fldvalue = "";
        }
        if($fldtype=='pwd')
          $fldvalue = $fldvalue ? SHA1($fldvalue) : $fldvalueold;
		  if(fldvalue){
		  sqlStatement("INSERT INTO globals ( gl_name, gl_index, gl_value ) " .
          "VALUES ( '$fldid', '0', '$fldvalue' )");
		  }
      }

      ++$i;
    }
  }
  checkCreateCDB();
  checkBackgroundServices();
  echo "<script type='text/javascript'>";
  echo "parent.left_nav.location.reload();";
  echo "parent.Title.location.reload();";
  echo "if(self.name=='RTop'){";
  echo "parent.RBot.location.reload();";
  echo "}else{";
  echo "parent.RTop.location.reload();";
  echo "}";
  echo "self.location.href='edit_globals.php?unique=yes';";
  echo "</script>";
}
?>

<!-- supporting javascript code -->
<script type="text/javascript" src="../../library/dialog.js"></script>
<script type="text/javascript" src="../../library/js/jquery.1.3.2.js"></script>
<script type="text/javascript" src="../../library/js/common.js"></script>
<script type="text/javascript" src="../../library/js/fancybox/jquery.fancybox-1.2.6.js"></script>
<link rel="stylesheet" type="text/css" href="../../library/js/fancybox/jquery.fancybox-1.2.6.css" media="screen" />

<link rel="stylesheet" href='<?php  echo $css_header ?>' type='text/css'>
<?php if ($_GET['mode'] == "user") { ?>
  <title><?php  xl('User Settings','e'); ?></title>
<?php } else { ?>
  <title><?php  xl('Global Settings','e'); ?></title>
<?php } ?>

<style>
tr.head   { font-size:10pt; background-color:#cccccc; text-align:center; }
tr.detail { font-size:10pt; }
td        { font-size:10pt; }
input     { font-size:10pt; }
</style>

</head>

<body class="body_top">

<?php if ($_GET['mode'] == "user") { ?>
  <form method='post' name='theform' id='theform' action='edit_globals.php?mode=user' onsubmit='return top.restoreSession()'>
<?php } else { ?>
  <form method='post' name='theform' id='theform' action='edit_globals.php' onsubmit='return top.restoreSession()'>
<?php } ?>

<?php if ($_GET['mode'] == "user") { ?>
  <p><b><?php xl('Edit User Settings','e'); ?></b>
<?php } else { ?>
  <p><b><?php xl('Edit Global Settings','e'); ?></b>
<?php } ?>

<ul class="tabNav">
<?php
$i = 0;
foreach ($GLOBALS_METADATA as $grpname => $grparr) {
  if ( $_GET['mode'] != "user" || ($_GET['mode'] == "user" && in_array($grpname, $USER_SPECIFIC_TABS)) ) {
    echo " <li" . ($i ? "" : " class='current'") .
      "><a href='/play/javascript-tabbed-navigation/'>" .
      xl($grpname) . "</a></li>\n";
    ++$i;
  }
}
?>
</ul>

<div class="tabContainer">
<?php
$i = 0;
foreach ($GLOBALS_METADATA as $grpname => $grparr) {
 if ( $_GET['mode'] != "user" || ($_GET['mode'] == "user" && in_array($grpname, $USER_SPECIFIC_TABS)) ) {
  echo " <div class='tab" . ($i ? "" : " current") .
    "' style='height:auto;width:97%;'>\n";

  echo " <table>";

  if ($_GET['mode'] == "user") {
   echo "<tr>";
   echo "<th>&nbsp</th>";
   echo "<th>" . htmlspecialchars( xl('User Specific Setting'), ENT_NOQUOTES) . "</th>";
   echo "<th>" . htmlspecialchars( xl('Default Setting'), ENT_NOQUOTES) . "</th>";
   echo "<th>&nbsp</th>";
   echo "<th>" . htmlspecialchars( xl('Set to Default'), ENT_NOQUOTES) . "</th>";
   echo "</tr>";
  }

  foreach ($grparr as $fldid => $fldarr) {
   if ( $_GET['mode'] != "user" || ($_GET['mode'] == "user" && in_array($fldid, $USER_SPECIFIC_GLOBALS)) ) {
    list($fldname, $fldtype, $flddef, $flddesc) = $fldarr;

    // Most parameters will have a single value, but some will be arrays.
    // Here we cater to both possibilities.
    $glres = sqlStatement("SELECT gl_index, gl_value FROM globals WHERE " .
      "gl_name = '$fldid' ORDER BY gl_index");
    $glarr = array();
    while ($glrow = sqlFetchArray($glres)) $glarr[] = $glrow;

    // $fldvalue is meaningful only for the single-value cases.
    $fldvalue = count($glarr) ? $glarr[0]['gl_value'] : $flddef;

    // Collect user specific setting if mode set to user
    $userSetting = "";
    $settingDefault = "checked='checked'";
    if ($_GET['mode'] == "user") {
      $userSettingArray = sqlQuery("SELECT * FROM user_settings WHERE setting_user=? AND setting_label=?",array($_SESSION['authId'],"global:".$fldid));
      $userSetting = $userSettingArray['setting_value'];
      $globalValue = $fldvalue;
      if (!empty($userSettingArray)) {
        $fldvalue = $userSetting;
        $settingDefault = "";
      }
    }

    echo " <tr title='$flddesc'><td valign='top'><b>$fldname </b></td><td valign='top'>\n";

    if (is_array($fldtype)) {
      echo "  <select name='form_$i' id='form_$i'>\n";
      foreach ($fldtype as $key => $value) {
        if ($_GET['mode'] == "user") {
          if ($globalValue == $key) $globalTitle = $value;
        }
        echo "   <option value='$key'";
        if ($key == $fldvalue) echo " selected";
        echo ">";
        echo $value;
        echo "</option>\n";
      }
      echo "  </select>\n";
    }

    else if ($fldtype == 'bool') {
      if ($_GET['mode'] == "user") {
        if ($globalValue == 1) {
          $globalTitle = htmlspecialchars( xl('Checked'), ENT_NOQUOTES);
        }
        else {
          $globalTitle = htmlspecialchars( xl('Not Checked'), ENT_NOQUOTES);
        }
      }
      echo "  <input type='checkbox' name='form_$i' id='form_$i' value='1'";
      if ($fldvalue) echo " checked";
      echo " />\n";
    }

    else if ($fldtype == 'num') {
      if ($_GET['mode'] == "user") {
        $globalTitle = $globalValue;
      }
      echo "  <input type='text' name='form_$i' id='form_$i' " .
        "size='6' maxlength='15' value='$fldvalue' />\n";
    }

    else if ($fldtype == 'text') {
      if ($_GET['mode'] == "user") {
        $globalTitle = $globalValue;
      }
      echo "  <input type='text' name='form_$i' id='form_$i' " .
        "size='50' maxlength='255' value='$fldvalue' />\n";
    }
    else if ($fldtype == 'pwd') {
	  if ($_GET['mode'] == "user") {
        $globalTitle = $globalValue;
      }
      echo "  <input type='password' name='form_$i' " .
        "size='50' maxlength='255' value='' />\n";
    }

    else if ($fldtype == 'pass') {
	  if ($_GET['mode'] == "user") {
        $globalTitle = $globalValue;
      }
      echo "  <input type='password' name='form_$i' " .
        "size='50' maxlength='255' value='$fldvalue' />\n";
    }

    else if ($fldtype == 'lang') {
      $res = sqlStatement("SELECT * FROM lang_languages ORDER BY lang_description");
      echo "  <select name='form_$i' id='form_$i'>\n";
      while ($row = sqlFetchArray($res)) {
        echo "   <option value='" . $row['lang_description'] . "'";
        if ($row['lang_description'] == $fldvalue) echo " selected";
        echo ">";
        echo xl($row['lang_description']);
        echo "</option>\n";
      }
      echo "  </select>\n";
    }

    else if ($fldtype == 'm_lang') {
      $res = sqlStatement("SELECT * FROM lang_languages  ORDER BY lang_description");
      echo "  <select multiple name='form_{$i}[]' id='form_{$i}[]' size='3'>\n";
      while ($row = sqlFetchArray($res)) {
        echo "   <option value='" . $row['lang_description'] . "'";
        foreach ($glarr as $glrow) {
          if ($glrow['gl_value'] == $row['lang_description']) {
            echo " selected";
            break;
          }
        }
        echo ">";
        echo xl($row['lang_description']);
        echo "</option>\n";
      }
      echo "  </select>\n";
    }

    else if ($fldtype == 'css') {
      if ($_GET['mode'] == "user") {
        $globalTitle = $globalValue;
      }
      $themedir = "$webserver_root/interface/themes";
      $dh = opendir($themedir);
      if ($dh) {
        echo "  <select name='form_$i' id='form_$i'>\n";
        while (false !== ($tfname = readdir($dh))) {
          // Only show files that contain style_ as options
          //  Skip style_blue.css since this is used for
          //  lone scripts such as setup.php
          //  Also skip style_pdf.css which is for PDFs and not screen output
          if (!preg_match("/^style_.*\.css$/", $tfname) ||
            $tfname == 'style_blue.css' || $tfname == 'style_pdf.css')
            continue;
          echo "<option value='$tfname'";
          if ($tfname == $fldvalue) echo " selected";
          echo ">";
          echo $tfname;
          echo "</option>\n";
        }
        closedir($dh);
        echo "  </select>\n";
      }
    }

    else if ($fldtype == 'hour') {
      if ($_GET['mode'] == "user") {
        $globalTitle = $globalValue;
      }
      echo "  <select name='form_$i' id='form_$i'>\n";
      for ($h = 0; $h < 24; ++$h) {
        echo "<option value='$h'";
        if ($h == $fldvalue) echo " selected";
        echo ">";
        if      ($h ==  0) echo "12 AM";
        else if ($h <  12) echo "$h AM";
        else if ($h == 12) echo "12 PM";
        else echo ($h - 12) . " PM";
        echo "</option>\n";
      }
      echo "  </select>\n";
    }
    
    
    else if ($fldtype == 'pos_data_grid') {
      if ($_GET['mode'] == "user") {
        $globalTitle = $globalValue;
      }
      
      
    if(isset($_POST['chk_fields']))
    {
        foreach($_POST['check_list'] as $check) 
        {
          echo $check; 
        }
    }            
      
      echo "<table id='tbl_AllCare_Patients' name='tbl_AllCare_Patients'>
                <tr title='1 to 1' border='1'>
                    <td valign='top' colspan='2'><b>AllCare_Patients1to1 </b></td>
                    <td valign='top'><input id='chk1to1' type='checkbox' value='1' name='chk1to1'></td>
                </tr>
                <tr title='1 to n' border='1'>
                    <td valign='top' colspan='2'><b>AllCare_Patients1ton </b></td>
                    <td valign='top'><input id='chk1ton' type='checkbox' value='1' name='chk1ton'></td>
                </tr>
            </table>
            <br>
            <table border='1' id='tbl_grid' name='tbl_grid'>                
                <tr>
                    <th><input type='checkbox' id='check_All' name='check_All' onclick='checkAll(this.checked);' /></th>
                    <th>Related Table</th>
                    <th>Field Name</th>
                    <th>Data Type</th>
                    <th>Data Length</th>
                    <th>Reqd (Y/N)</th>
                    <th>Default Value</th>
                    <th>View/Edit</th>
                    <th></th>
                </tr>";
        
      $res = sqlStatement('SHOW TABLES');  
      $table_list='';
      while ($row = sqlFetchArray($res)) 
      {
        $table_list .= "<option value='" . $row['Tables_in_551948_newemr'] . "'>".$row['Tables_in_551948_newemr']."</option>";
      }  
      
/*      $dataTypes = sqlStatement('SELECT title FROM tbl_data_types');  
      $dataTypes_list='';
      while ($row = sqlFetchArray($res)) 
      {
        $dataTypes_list .= "<option value='" . $row['title'] . "'>".$row['title']."</option>";
      }  */
      
      for($cnt=0;$cnt<20;$cnt++)
      {
               echo "<tr>
                    <td align='center'><input type='checkbox' id='checkClear".$cnt."' name='checkClear' /></td>";
                                           
      echo "<td><select id='related_tables".$cnt."' name='related_tables' onchange='javascript:return newEvt(this.id);' style='width:125px'>          
            <option value='none'>none</option>
                $table_list
            </select></td>
            <td><input type='text' id='txtFieldName".$cnt."' name='txtFieldName' style='width:125px' /></td>";

            //echo "<td><input type='text' id='txtFieldType".$cnt."' name='txtFieldType[]' style='width:125px' /></td>";
            
            echo "<td>
                    <select id='data_types".$cnt."' name='data_types'>
                        <option value='none'>none</option>                                       
                        <option value='tinyint'>tinyint</option>
                        <option value='smallint'>smallint</option>
                        <option value='mediumint'>mediumint</option>
                        <option value='int'>int</option>
                        <option value='bigint'>bigint</option>
                        <option value='decimal'>decimal</option>
                        <option value='float'>float</option>
                        <option value='double'>double</option>
                        <option value='real'>real</option>
                        <option value='bit'>bit</option>
                        <option value='boolean'>boolean</option>
                        <option value='serial'>serial</option>
                        <option value='date'>date</option>
                        <option value='datetime'>datetime</option>
                        <option value='timestamp'>timestamp</option>
                        <option value='time'>time</option>
                        <option value='year'>year</option>
                        <option value='char'>char</option>
                        <option value='varchar'>varchar</option>
                        <option value='tinytext'>tinytext</option>
                        <option value='text'>text</option>
                        <option value='mediumtext'>mediumtext</option>
                        <option value='longtext'>longtext</option>
                        <option value='binary'>binary</option>
                        <option value='varbinary'>varbinary</option>
                        <option value='tinyblob'>tinyblob</option>
                        <option value='mediumblob'>mediumblob</option>
                        <option value='blob'>blob</option>
                        <option value='longblob'>longblob</option>
                        <option value='enum'>enum</option>
                        <option value='set'>set</option>
                        <option value='geometry'>geometry</option>
                        <option value='point'>point</option>
                        <option value='linestring'>linestring</option>
                        <option value='polygon'>polygon</option>
                        <option value='multipoint'>multipoint</option>
                        <option value='multilinestring'>multilinestring</option>
                        <option value='multipolygon'>multipolygon</option>
                        <option value='geometrycollection'>geometrycollection</option>

                    </select></td>";
            
                                  
            echo "<td><input type='text' id='txtDataLength".$cnt."' name='txtDataLength' style='width:125px' /></td>
            <td align='center'>
                <input type='checkbox' id='boolFieldRequired".$cnt."' name='boolFieldRequired' />
            </td>
            <td><input type='text' id='txtDefaultValue".$cnt."' name='txtDefaultValue' style='width:125px' /></td>
            
            <td>
                <select id='view_edit".$cnt."' name='view_edit' style='width:100px'>
                    <option value='N'>View</option>
                    <option value='Y'>Edit</option>
                </select>
            </td>
        </tr>";
      } 
      echo "</table>";           
        
    $postyperes = sqlStatement("SELECT s.id,s.code,s.title FROM `tbl_pos_types` s group by title order by code asc ");
    $postyperesoptions = array();
    while($postyperesrows = sqlFetchArray($postyperes))
    {
      $postypeoptionvalue .="<option value=".$postyperesrows['code'].">".$postyperesrows['code']." :" .$postyperesrows['title']."</option>";
    }
        
?>

          <table id="tbl_step2" name="tbl_step2" style="display:none;">
            <tr>
           <td>POS Types</td>
            <td><select id='lstpostype' name='lstpostype'><?php echo $postypeoptionvalue;?></select></td>
            </tr>
            <tr>
                <td>AllCare_Patients1to1</td>
                <td><input type="radio" id="rd1to1" name="radallcare" value="1to1" 
                           onchange="javascript:jQuery('#lblRecordset').hide();
                                                jQuery('#lblGroup').show();  showFileds();"></td>
            </tr>
            <tr>
                <td>AllCare_Patients1ton</td>
                <td><input type="radio" id="rd1ton" name="radallcare" value="1ton" 
                           onchange="javascript:jQuery('#lblRecordset').show();
                                                jQuery('#lblGroup').hide();  showFileds();"></td>
            </tr>
            
            <tr>
                <td><label id='lblGroup'>Group Name</label>
                    <label id='lblRecordset' style='display:none;'>Recordset</label></td>
                <td>
                    <input type="text" id='txtGroupRecordset' name="txtGroupRecordset" value="">
                </td>
            </tr>
            <tr>
                <td>Field Selected</td>
                <td>
                    <div id="showFields" name="showFields">
                <?php
               /*                                 
    $postyperes = sqlStatement("SHOW COLUMNS FROM tbl_allcare_patients1ton 
                                 WHERE Field!='id' AND Field!='pid' AND Field!='pos_id'");
    $allcarerows = array();
    
    while($allcarerows = sqlFetchArray($postyperes))
    {
             echo  $allcarerows['Field'] ."<input type='checkbox' name='chkFields[]' value='$allcarerows[Field]'>";
    }
    */
                ?>             
                    </div> 
                </td>
            </tr>
       
		
    <tr><td colspan="2" align="center">      

<!--            <input type='submit' id='btnSave' name='btnSave' value='Save' />-->
            <input type='button' id='btnSave' name='btnSave' value='Save' 
                   onclick='javascript:if(validateStep2()){insert_in_mapping();}' />
            <input type='button' id='btnCancel' name='btnCancel' value='Cancel'
                   onclick='javascript:location.href="edit_globals.php";'/>
    </td></tr>
 </table>

    
<?php
        echo"<br>
        <center>       
            
            <input type='button' id='btnNext' name='btnNext' value='Next' 
                   onclick='javascript:if(validateStep1())
                                       {
                                            insertFields();
                                       }' />                                
            <input type='button' id='btnClear' name='btnClear' value='Clear' onclick='javascript:clearSelected();' />
            <input type='button' id='btn_Cancel' name='btn_Cancel' value='Cancel' 
                   onclick='javascript:location.href=\"edit_globals.php\";' />
        </center>";
    }
?>
    
    <?php
    if ($_GET['mode'] == "user") {
      echo " </td>\n";
      echo "<td align='center' style='color:red;'>" . $globalTitle . "</td>\n";
      echo "<td>&nbsp</td>";
      echo "<td align='center'><input type='checkbox' value='YES' name='toggle_" . $i . "' id='toggle_" . $i . "' " . $settingDefault . "/></td>\n";
      echo "<input type='hidden' id='globaldefault_" . $i . "' value='" . $globalValue . "'>\n";
      echo "</tr>\n";
    }
    else {
      echo " </td></tr>\n";
    }
    ++$i;
   }
  }
  echo " </table>\n";
  echo " </div>\n";
 }
}
?>
    
    
    
</div>

<p>
 <input type='submit' name='form_save' value='<?php xl('Save','e'); ?>' />
</p>
</center>



</form>

</body>

<script language="JavaScript">

$(document).ready(function(){
  tabbify();
  enable_modals();

  // Use the counter ($i) to make the form user friendly for user-specific globals use
  <?php if ($_GET['mode'] == "user") { ?>
    <?php for ($j = 0; $j <= $i; $j++) { ?>
      $("#form_<?php echo $j ?>").change(function() {
        $("#toggle_<?php echo $j ?>").attr('checked',false);
      });
      $("#toggle_<?php echo $j ?>").change(function() {
        if ($('#toggle_<?php echo $j ?>').attr('checked')) {
          var defaultGlobal = $("#globaldefault_<?php echo $j ?>").val();
          $("#form_<?php echo $j ?>").val(defaultGlobal);
        }
      });
    <?php } ?>
  <?php } ?>

});


function newEvt(selectedBox)
{
     if(document.getElementById(selectedBox).value!=='none')
         {
  dlgopen('getTableFields.php?table_name='+document.getElementById(selectedBox).value,
   '_blank', 550, 270);
         }
  return false;
}

var totalFields=0;
function receiveFromChild(tableName,checkedFields,checkedFieldsType)
{            
    //alert(checkedFieldsType);
    for(var i=totalFields;i<(totalFields+checkedFields.length);i++)
    {              
        var split0='',split1='',split2='';
        //alert('i = '+i);
        if(checkedFieldsType[i-totalFields].indexOf('(') !== -1)  // datatype contains '('  e.g. varchar(100)
        {
            split0 = checkedFieldsType[i-totalFields].split("(");
            split1=split0[0];
            split2 = split0[1].split(")");
        }
        else
        {
            split1 = checkedFieldsType[i-totalFields];   
            split2='-';
        }
        
        jQuery('#related_tables'+i).val(tableName);
        jQuery('#txtFieldName'+i).val(checkedFields[i-totalFields]);                    
        jQuery('#data_types'+i).val(split1);    
        jQuery('#txtDataLength'+i).val(split2[0]);
       
    }
    totalFields=totalFields+checkedFields.length;
}
         	
function addRow(numRow,tableName,checkFieldName,checkFieldType,checkFieldSize)
{	          
        for(var i=0;i<numRow;i++)
        {
            jQuery('#related_tables'+numRow).val(tableName);
            jQuery('#txtFieldName'+numRow).val(checkFieldName);
            //jQuery('#txtFieldType'+numRow).val(checkFieldType);
            jQuery('#txtDataLength'+numRow).val(checkFieldSize);
            jQuery('#data_types'+numRow).val(checkFieldType);
            //jQuery('#txtDefaultValue'+numRow).val(tableName);                                
        }
}

function skip_none(array_name)
{
    var new_array=new Array();
    var j=0;
    for(var i=0;i<20;i++)
    {
        if(array_name[i]!=='none')
        {
           new_array[j]= array_name[i];
           j++;
        }
    }
    return new_array;
    
}

function skip_blank(array_name)
{
    var new_array=new Array();
    var j=0;
    for(var i=0;i<20;i++)
    {
        if(array_name[i]!=='')
        {
           new_array[j]= array_name[i];
           j++;
        }
    }
    return new_array;
    
}

function validateStep1()
{
    if(!(jQuery('#chk1to1').is(':checked')) && !(jQuery('#chk1ton').is(':checked')))
    {
        alert("Please check the table AllCare_Patients1to1 or AllCare_Patients1ton");
        return false;
    }
        
    var relTables=document.getElementsByName("related_tables");        
    var flag=0;
    for(j=0;j<relTables.length;j++)
    {
        if(relTables[j].value==='none')
        {
            flag=0;
        }
        else
        {
            flag=1;
            break;
        }
    }
    
    if(flag===0)
    {
        alert("Please select the fields from at least one related table.");
        return false;
    }
    
    return true;
    
}

function validateStep2()
{
    if(!(jQuery('#rd1to1').is(':checked')) && !(jQuery('#rd1ton').is(':checked')))
    {
        alert("Please check the table AllCare_Patients1to1 or AllCare_Patients1ton");
        return false;
    }
    
    else if(jQuery('#txtGroupRecordset').val()==='')
    {
        alert("Please enter the Group/Recordset name");
        return false;
    }
    
    else if(!jQuery('#comboFields').val())
    {
        alert("Please select the field/s");
        return false;
    }
    return true;
    
}

function checkIfFieldAlreadyExists()
{
    
    var FieldName='';

    var chk1to1=(jQuery('#chk1to1').is(':checked'))?1:0;
    var chk1ton=(jQuery('#chk1ton').is(':checked'))?1:0;
    
    for(var i=0;i<20;i++)
    {            
        if(jQuery('#txtFieldName'+i).val()!=='')
        {
            FieldName=jQuery('#txtFieldName'+i).val();    

            $.ajax({
                type: 'POST',
                url: 'checkIfFieldAlreadyExists.php',	
                async:true,
                data: {FieldName:FieldName,chk1to1:chk1to1,chk1ton:chk1ton},

             //data:relatedTables,
             success: function(response)
             {               
                 if(response!=='11')
                 {
                     alert(response);     
                     return false;
                 }
                 else
                 {
                     return true;
                 }
             },
             failure: function(response)
             {
                     alert("error");
             }		
            });	            
          }       
    }
    return true;
}

function insertFields()
{
        var finalURL='addNewFields.php';  
        
        var chk1to1=(jQuery('#chk1to1').is(':checked'))?1:0;
        var chk1ton=(jQuery('#chk1ton').is(':checked'))?1:0;
        //var related_tables=new Array();   
        var relatedTables=new Array();  
        //var relatedTables=[];
        var FieldName=new Array();   
        var FieldType=new Array();   
        var DataLength=new Array();   
        var FieldRequired=new Array();   
        var DefaultValue=new Array();                  
        var FieldViewEdit=new Array();
                
        for(var i=0;i<20;i++)
        {
            relatedTables[i]=jQuery('#related_tables'+i).val();
            //relatedTables.push(jQuery('#related_tables'+i).val());
            FieldName[i]=jQuery('#txtFieldName'+i).val();                                    
            FieldType[i]=jQuery('#data_types'+i).val();
            DataLength[i]=jQuery('#txtDataLength'+i).val();
            FieldRequired[i]=(jQuery('#boolFieldRequired'+i).is(':checked'))?1:0;
            DefaultValue[i]=jQuery('#txtDefaultValue'+i).val();            
            FieldViewEdit[i]=jQuery('#view_edit'+i).val();                                    	
            
        }                       
                
        relatedTables=skip_none(relatedTables);                
        relatedTables=relatedTables.toString();
        //alert("rt= "+relatedTables);
        
        FieldName=skip_blank(FieldName);
        FieldName=FieldName.toString();
        
        FieldType=skip_none(FieldType);
        FieldType=FieldType.toString();
        
        DataLength=skip_blank(DataLength);
        DataLength=DataLength.toString();
        
        FieldRequired=FieldRequired.toString();
        
        DefaultValue=DefaultValue.toString();
        
        FieldViewEdit=FieldViewEdit.toString();
                            
        $.ajax({
                type: 'POST',
		url: finalURL,	
		data: {chk1to1:chk1to1,chk1ton:chk1ton,relatedTables:relatedTables,FieldName:FieldName,
                       FieldType:FieldType,DataLength:DataLength,FieldRequired:FieldRequired,DefaultValue:DefaultValue,
                       FieldViewEdit:FieldViewEdit},
		
                //data:relatedTables,
		success: function(response)
		{                         
                     alert(response);     
                     jQuery("#tbl_grid").hide();     
                     jQuery("#tbl_AllCare_Patients").hide();     
                     jQuery("#btnNext").hide();                     
                     jQuery("#btnClear").hide();  
                     jQuery("#btn_Cancel").hide();  
                     jQuery("#tbl_step2").show();
                     
		},
		failure: function(response)
		{
			alert("error");
		}		
        });		        
}

function showFileds()
{                 
        var finalURL='show_fields.php';  
        var table_name=jQuery("#rd1to1").is(":checked")?'tbl_allcare_patients1to1':'tbl_allcare_patients1ton';
                
        $.ajax({
                    type: 'POST',
                    url: finalURL,	
                    data: {table_name:table_name},

                    //data:relatedTables,
                    success: function(response)
                    {                         
                         jQuery("#showFields").html(response);                     
                    },
                    failure: function(response)
                    {
                            alert("error");
                    }		
               });		
}

function insert_in_mapping()
{
    var finalURL='insertInMapping.php';  
                
    var posType=jQuery('#lstpostype').val();
    
    var mappingTableName=(jQuery('#rd1to1').is(':checked'))?'tbl_allcare_patients1to1_fieldmapping':'tbl_allcare_patients1ton_fieldmapping';    
    
    var groupRecordsetName=jQuery('#txtGroupRecordset').val();

    var selectedFields=jQuery('#comboFields').val();
   
    selectedFields=selectedFields.toString();
//    alert('SF== '+selectedFields);
    /*
        var FieldViewEdit=new Array();
        for(var i=0;i<20;i++)
        {
            relatedTables[i]=jQuery('#related_tables'+i).val();
            //relatedTables.push(jQuery('#related_tables'+i).val());
            FieldName[i]=jQuery('#txtFieldName'+i).val();
            FieldType[i]=jQuery('#data_types'+i).val();
            DataLength[i]=jQuery('#txtDataLength'+i).val();
            FieldRequired[i]=(jQuery('#boolFieldRequired'+i).is(':checked'))?1:0;
            DefaultValue[i]=jQuery('#txtDefaultValue'+i).val();            
            FieldViewEdit[i]=jQuery('#view_edit'+i).val();           
        }      
        */
    $.ajax({
            type: 'POST',
            url: finalURL,	
            data: {posType:posType,mappingTableName:mappingTableName,groupRecordsetName:groupRecordsetName,
                   selectedFields:selectedFields},

            //data:relatedTables,
            success: function(response)
            {                         
                 alert(response);     

            },
            failure: function(response)
            {
                    alert("error");
            }		
           });		
         
}

function checkAll(checkboxstatus)
{            
    var delList=document.getElementsByName("checkClear");        

    for(i=0;i<delList.length;i++)
    {
            //if(delList[i].checked==false)
            //{
                    delList[i].checked = checkboxstatus;//(chkBtn.checked)? true : false;
            //}
    }        
}

function clearSelected()
{    
    var delList=document.getElementsByName("checkClear");        

    for(i=0;i<delList.length;i++)
    {
        if(delList[i].checked===true)
        {
            jQuery('#related_tables'+i).val('none');
            jQuery('#txtFieldName'+i).val('');
            jQuery('#data_types'+i).val('none');
            jQuery('#txtDataLength'+i).val('');
            jQuery('#boolFieldRequired'+i).attr('checked', false);            
            jQuery('#txtDefaultValue'+i).val('');
            jQuery('#view_edit'+i).val('N');            
            delList[i].checked=false;
        }
        jQuery('#check_All').attr('checked', false);   
    }        
    
}

</script>

</html>
