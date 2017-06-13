<?php
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
require_once("../globals.php");
require_once("../../library/acl.inc");
require_once("$srcdir/sql.inc");
require_once("$srcdir/calendar.inc");
require_once("$srcdir/formdata.inc.php");
require_once("$srcdir/options.inc.php");
require_once(dirname(__FILE__) . "/../../library/classes/WSProvider.class.php");
require_once("$srcdir/erx_javascript.inc.php");

if (!$_GET["id"] || !acl_check('admin', 'users'))
  exit();

if ($_GET["mode"] == "update") {
  if ($_GET["username"]) {
    // $tqvar = addslashes(trim($_GET["username"]));
    $tqvar = trim(formData('username','G'));
    $user_data = mysql_fetch_array(sqlStatement("select * from users where id={$_GET["id"]}"));
    sqlStatement("update users set username='$tqvar' where id={$_GET["id"]}");
    sqlStatement("update groups set user='$tqvar' where user='". $user_data["username"]  ."'");
    //echo "query was: " ."update groups set user='$tqvar' where user='". $user_data["username"]  ."'" ;
  }
  if ($_GET["taxid"]) {
    $tqvar = formData('taxid','G');
    sqlStatement("update users set federaltaxid='$tqvar' where id={$_GET["id"]}");
  }
  if ($_GET["drugid"]) {
    $tqvar = formData('drugid','G');
    sqlStatement("update users set federaldrugid='$tqvar' where id={$_GET["id"]}");
  }
  if ($_GET["upin"]) {
    $tqvar = formData('upin','G');
    sqlStatement("update users set upin='$tqvar' where id={$_GET["id"]}");
  }
  if ($_GET["npi"]) {
    $tqvar = formData('npi','G');
    sqlStatement("update users set npi='$tqvar' where id={$_GET["id"]}");
  }
  if ($_GET["taxonomy"]) {
    $tqvar = formData('taxonomy','G');
    sqlStatement("update users set taxonomy = '$tqvar' where id= {$_GET["id"]}");
  }
  if ($_GET["lname"]) {
    $tqvar = formData('lname','G');
    sqlStatement("update users set lname='$tqvar' where id={$_GET["id"]}");
  }
  if ($_GET["job"]) {
    $tqvar = formData('job','G');
    sqlStatement("update users set specialty='$tqvar' where id={$_GET["id"]}");
  }
  if ($_GET["mname"]) {
          $tqvar = formData('mname','G');
          sqlStatement("update users set mname='$tqvar' where id={$_GET["id"]}");
  }
  if ($_GET["facility_id"]) {
          $tqvar = formData('facility_id','G');
          sqlStatement("update users set facility_id = '$tqvar' where id = {$_GET["id"]}");
          //(CHEMED) Update facility name when changing the id
          sqlStatement("UPDATE users, facility SET users.facility = facility.name WHERE facility.id = '$tqvar' AND users.id = {$_GET["id"]}");
          //END (CHEMED)
  }
  if ($GLOBALS['restrict_user_facility'] && $_GET["schedule_facility"]) {
	  sqlStatement("delete from users_facility
	    where tablename='users'
	    and table_id={$_GET["id"]}
	    and facility_id not in (" . implode(",", $_GET['schedule_facility']) . ")");
	  foreach($_GET["schedule_facility"] as $tqvar) {
      sqlStatement("replace into users_facility set
		    facility_id = '$tqvar',
		    tablename='users',
		    table_id = {$_GET["id"]}");
    }
  }
  if ($_GET["fname"]) {
          $tqvar = formData('fname','G');
          sqlStatement("update users set fname='$tqvar' where id={$_GET["id"]}");
  }
  //(CHEMED) Calendar UI preference
  if ($_GET["cal_ui"]) {
          $tqvar = formData('cal_ui','G');
          sqlStatement("update users set cal_ui = '$tqvar' where id = {$_GET["id"]}");

          // added by bgm to set this session variable if the current user has edited
	  //   their own settings
	  if ($_SESSION['authId'] == $_GET["id"]) {
	    $_SESSION['cal_ui'] = $tqvar;
	  }
  }
  //END (CHEMED) Calendar UI preference

  if (isset($_GET['default_warehouse'])) {
    sqlStatement("UPDATE users SET default_warehouse = '" .
      formData('default_warehouse','G') .
      "' WHERE id = '" . formData('id','G') . "'");
  }

  if (isset($_GET['irnpool'])) {
    sqlStatement("UPDATE users SET irnpool = '" .
      formData('irnpool','G') .
      "' WHERE id = '" . formData('id','G') . "'");
  }

  if ($_GET["newauthPass"] && $_GET["newauthPass"] != "d41d8cd98f00b204e9800998ecf8427e") { // account for empty
    $tqvar = formData('newauthPass','G');
    sqlStatement("update users set password='$tqvar' where id={$_GET["id"]}");
  }

  // for relay health single sign-on
  if ($_GET["ssi_relayhealth"]) {
    $tqvar = formData('ssi_relayhealth','G');
    sqlStatement("update users set ssi_relayhealth = '$tqvar' where id = {$_GET["id"]}");
  }

  $tqvar  = $_GET["authorized"] ? 1 : 0;
  $actvar = $_GET["active"]     ? 1 : 0;
  $calvar = $_GET["calendar"]   ? 1 : 0;

  sqlStatement("UPDATE users SET authorized = $tqvar, active = $actvar, " .
    "calendar = $calvar, see_auth = '" . $_GET['see_auth'] . "' WHERE " .
    "id = {$_GET["id"]}");

  if ($_GET["comments"]) {
    $tqvar = formData('comments','G');
    sqlStatement("update users set info = '$tqvar' where id = {$_GET["id"]}");
  }

  if (isset($phpgacl_location) && acl_check('admin', 'acl')) {
    // Set the access control group of user
    $user_data = mysql_fetch_array(sqlStatement("select username from users where id={$_GET["id"]}"));
    set_user_aro($_GET['access_group'], $user_data["username"],
      formData('fname','G'), formData('mname','G'), formData('lname','G'));
  }

  $ws = new WSProvider($_GET['id']);

  /*Dont move usergroup_admin (1).php just close window
  // On a successful update, return to the users list.
  include("usergroup_admin.php");
  exit(0);
  */  	echo '
<script type="text/javascript">
<!--
parent.$.fn.fancybox.close();
//-->
</script>

	';
}

$res = sqlStatement("select * from users where id=?",array($_GET["id"]));
for ($iter = 0;$row = sqlFetchArray($res);$iter++)
                $result[$iter] = $row;
$iter = $result[0];

///
if (isset($_POST["mode"])) {
  	echo '
<script type="text/javascript">
<!--
parent.$.fn.fancybox.close();
//-->
</script>

	';
}
///

$userid=$_GET["id"];


?>

<html>
<head>

<link rel="stylesheet" href="<?php echo $css_header; ?>" type="text/css">
<script type="text/javascript" src="../../library/dialog.js"></script>
<script type="text/javascript" src="../../library/js/jquery.1.3.2.js"></script>
<script type="text/javascript" src="../../library/js/common.js"></script>

<script src="checkpwd_validation.js" type="text/javascript"></script>

<script language="JavaScript">
function checkChange()
{
  alert("<?php echo addslashes(xl('If you change e-RX Role for ePrescription, it may affect the ePrescription workflow. If you face any difficulty, contact your ePrescription vendor.'));?>");
}
function submitform() {
	top.restoreSession();
	var flag=0;
	function trimAll(sString)
	{
		while (sString.substring(0,1) == ' ')
		{
			sString = sString.substring(1, sString.length);
		}
		while (sString.substring(sString.length-1, sString.length) == ' ')
		{
			sString = sString.substring(0,sString.length-1);
		}
		return sString;
	}
	if(trimAll(document.getElementById('fname').value) == ""){
		alert("<?php xl('Required field missing: Please enter the First name','e');?>");
		document.getElementById('fname').style.backgroundColor="red";
		document.getElementById('fname').focus();
		return false;
	}
	if(trimAll(document.getElementById('lname').value) == ""){
		alert("<?php xl('Required field missing: Please enter the Last name','e');?>");
		document.getElementById('lname').style.backgroundColor="red";
		document.getElementById('lname').focus();
		return false;
	}
	if(document.forms[0].clearPass.value!="")
	{
		//Checking for the strong password if the 'secure password' feature is enabled
		if(document.forms[0].secure_pwd.value == 1)
		{
                    var pwdresult = passwordvalidate(document.forms[0].clearPass.value);
                    if(pwdresult == 0) {
                            flag=1;
                            alert("<?php echo xl('The password must be at least eight characters, and should'); echo '\n'; echo xl('contain at least three of the four following items:'); echo '\n'; echo xl('A number'); echo '\n'; echo xl('A lowercase letter'); echo '\n'; echo xl('An uppercase letter'); echo '\n'; echo xl('A special character');echo '('; echo xl('not a letter or number'); echo ').'; echo '\n'; echo xl('For example:'); echo ' healthCare@09'; ?>");
                            return false;
                    }
		}

	}//If pwd null ends here
	//Request to reset the user password if the user was deactived once the password expired.
	if((document.forms[0].pwd_expires.value != 0) && (document.forms[0].clearPass.value == "")) {
		if((document.forms[0].user_type.value != "Emergency Login") && (document.forms[0].pre_active.value == 0) && (document.forms[0].active.checked == 1) && (document.forms[0].grace_time.value != "") && (document.forms[0].current_date.value) > (document.forms[0].grace_time.value))
		{
			flag=1;
			document.getElementById('error_message').innerHTML="<?php xl('Please reset the password.','e') ?>";
		}
	}

  if (document.forms[0].access_group_id) {
    var sel = getSelected(document.forms[0].access_group_id.options);
    for (var item in sel) {
      if (sel[item].value == "Emergency Login") {
        document.forms[0].check_acl.value = 1;
      }
    }
  }

	  <?php if($GLOBALS['erx_enable']){ ?>
	alertMsg='';
	f=document.forms[0];
	for(i=0;i<f.length;i++){
	  if(f[i].type=='text' && f[i].value)
	  {
	    if(f[i].name == 'fname' || f[i].name == 'mname' || f[i].name == 'lname')
	    {
	      alertMsg += checkLength(f[i].name,f[i].value,35);
	      alertMsg += checkUsername(f[i].name,f[i].value);
	    }
	    else if(f[i].name == 'taxid')
	    {
	      alertMsg += checkLength(f[i].name,f[i].value,10);
	      alertMsg += checkFederalEin(f[i].name,f[i].value);
	    }
	    else if(f[i].name == 'state_license_number')
	    {
	      alertMsg += checkLength(f[i].name,f[i].value,10);
	      alertMsg += checkStateLicenseNumber(f[i].name,f[i].value);
	    }
	    else if(f[i].name == 'npi')
	    {
	      alertMsg += checkLength(f[i].name,f[i].value,10);
	      alertMsg += checkTaxNpiDea(f[i].name,f[i].value);
	    }
	    else if(f[i].name == 'drugid')
	    {
	      alertMsg += checkLength(f[i].name,f[i].value,30);
	      alertMsg += checkAlphaNumeric(f[i].name,f[i].value);
	    }
	  }
	}
	if(alertMsg)
	{
	  alert(alertMsg);
	  return false;
	}
	<?php } ?>
	if(flag == 0){
                    document.forms[0].submit();
                    parent.$.fn.fancybox.close(); 
	}
}
//Getting the list of selected item in ACL
function getSelected(opt) {
         var selected = new Array();
            var index = 0;
            for (var intLoop = 0; intLoop < opt.length; intLoop++) {
               if ((opt[intLoop].selected) ||
                   (opt[intLoop].checked)) {
                  index = selected.length;
                  selected[index] = new Object;
                  selected[index].value = opt[intLoop].value;
                  selected[index].index = intLoop;
               }
            }
            return selected;
         }

function authorized_clicked() {
 var f = document.forms[0];
 f.calendar.disabled = !f.authorized.checked;
 f.calendar.checked  =  f.authorized.checked;
}

</script>

<script>
    
    function isNumber(evt)
{
    var charCode = (evt.which) ? evt.which : event.keyCode
    return !(charCode > 31 && (charCode < 48 || charCode > 57));
}

    var i = 1;
function addtablerow(tblid,recid) 
{
    var prevValue = parseInt(document.getElementById('hiddenaddcount['+recid+']').value);   
    document.getElementById('hiddenaddcount['+recid+']').value=prevValue+1;
    
   
    
  $("#"+tblid+" tr:last").clone().find("input").each(function() {
    $(this).attr({
      'id': function(_, id) { return id},
      'name': function(_, name) { return name }
      /*,
      'value': function(_, value) { return value }               */
    });
  }).end().appendTo("#"+tblid);
  
  i++;
  
  
}
function deleteRow(el,recid) { 

  // while there are parents, keep going until reach TR 
  while (el.parentNode && el.tagName.toLowerCase() != 'tr') {
    el = el.parentNode;
  }

  // If el has a parentNode it must be a TR, so delete it
  // Don't delte if only 3 rows left in table
  if (el.parentNode && el.parentNode.rows.length > 2) 
  {
      var prevValue = parseInt(document.getElementById('hiddenaddcount['+recid+']').value);
                    //alert(prevValue);
                if(confirm("Are you sure to delete this row?"))
                {
                    el.parentNode.removeChild(el);
                    document.getElementById('hiddenaddcount['+recid+']').value=prevValue-1;
                }
  }
  else
  {
      
      alert("Row can not be deleted");
  }
}

</script>

</head>
<body class="body_top">
<table><tr><td>
<span class="title"><?php xl('Edit User','e'); ?></span>&nbsp;
</td><td>
    <a class="css_button" name='form_save' id='form_save' href='#' onclick='return submitform()'> <span><?php xl('Save','e');?></span> </a>
	<a class="css_button" id='cancel' href='#'><span><?php xl('Cancel','e');?></span></a>
</td></tr>
</table>
<br>
<FORM NAME="user_form" METHOD="POST" ACTION="usergroup_admin.php" target="_parent" onsubmit='return top.restoreSession()'>


<ul class="tabNav">

<li class='current'>
<a onclick="javascript:jQuery('#table_provider_attributes_edit').hide();
                       jQuery('#table_user_edit').show();
                       jQuery('.tabNav > li').removeClass('current');
                       jQuery(this).parent('li').addClass('current');" style='cursor:pointer' id="tabUserDetails_edit"> User Details</a>
</li>
<li>
<a onclick="javascript:jQuery('#table_user_edit').hide();
                       jQuery('#table_provider_attributes_edit').show();
                       jQuery('.tabNav > li').removeClass('current');
                       jQuery(this).parent('li').addClass('current');" style='cursor:pointer' id="tabProviderAttributes_edit"> Provider Attributes</a>
</li>
</ul>    
    <br>    
    
<input type=hidden name="pwd_expires" value="<?php echo $GLOBALS['password_expiration_days']; ?>" >
<input type=hidden name="pre_active" value="<?php echo $iter["active"]; ?>" >
<input type=hidden name="exp_date" value="<?php echo $iter["pwd_expiration_date"]; ?>" >
<input type=hidden name="get_admin_id" value="<?php echo $GLOBALS['Emergency_Login_email']; ?>" >
<input type=hidden name="admin_id" value="<?php echo $GLOBALS['Emergency_Login_email_id']; ?>" >
<input type=hidden name="check_acl" value="">
<?php 
//Calculating the grace time 
$current_date = date("Y-m-d");
$password_exp=$iter["pwd_expiration_date"];
if($password_exp != "0000-00-00")
  {
    $grace_time1 = date("Y-m-d", strtotime($password_exp . "+".$GLOBALS['password_grace_time'] ."days"));
  }
?>
<input type=hidden name="current_date" value="<?php echo strtotime($current_date); ?>" >
<input type=hidden name="grace_time" value="<?php echo strtotime($grace_time1); ?>" >
<!--  Get the list ACL for the user -->
<?php
$acl_name=acl_get_group_titles($iter["username"]);
$bg_name='';
$bg_count=count($acl_name);
   for($i=0;$i<$bg_count;$i++){
      if($acl_name[$i] == "Emergency Login")
       $bg_name=$acl_name[$i];
      }
?>
<input type=hidden name="user_type" value="<?php echo $bg_name; ?>" >

<TABLE border=0 cellpadding=0 cellspacing=0 id="table_user_edit">
<TR>
    <TD style="width:180px;"><span class=text><?php xl('Username','e'); ?>: </span></TD>
    <TD style="width:270px;"><input type=entry name=username style="width:150px;" value="<?php echo $iter["username"]; ?>" disabled></td>
    <TD style="width:200px;"><span class=text><?php xl('Your Password','e'); ?>: </span></TD>
    <TD class='text' style="width:280px;"><input type='password' name=adminPass style="width:150px;"  value="" autocomplete='off'><font class="mandatory">*</font></TD>
</TR>
<TR>
    <TD style="width:180px;"><span class=text></span></TD>
    <TD style="width:270px;"></td>
    <TD style="width:200px;"><span class=text><?php xl('User\'s New Password','e'); ?>: </span></TD>
    <TD class='text' style="width:280px;">    <input type=text name=clearPass style="width:150px;"  value=""><font class="mandatory">*</font></td>
</TR>


<TR height="30" style="valign:middle;">
<td><span class="text">&nbsp;</span></td><td>&nbsp;</td>
<td colspan="2"><span class=text><?php xl('Provider','e'); ?>:
 <input type="checkbox" name="authorized" id="authorized"
        onclick="authorized_clicked();provider_clicked(this.id);"<?php
  if ($iter["authorized"]) echo " checked"; ?> />
 &nbsp;&nbsp;<span class='text'><?php xl('Calendar','e'); ?>:
 <input type="checkbox" name="calendar"<?php
  if ($iter["calendar"]) echo " checked";
  if (!$iter["authorized"]) echo " disabled"; ?> />
 &nbsp;&nbsp;<span class='text'><?php xl('Active','e'); ?>:
 <input type="checkbox" name="active"<?php if ($iter["active"]) echo " checked"; ?> />
</TD>
</TR>

<TR>
<TD><span class=text><?php xl('First Name','e'); ?>: </span></TD>
<TD><input type=entry name=fname id=fname style="width:150px;" value="<?php echo $iter["fname"]; ?>"><span class="mandatory">&nbsp;*</span></td>
<td><span class=text><?php xl('Middle Name','e'); ?>: </span></TD><td><input type=entry name=mname style="width:150px;"  value="<?php echo $iter["mname"]; ?>"></td>
</TR>

<TR>
<td><span class=text><?php xl('Last Name','e'); ?>: </span></td><td><input type=entry name=lname id=lname style="width:150px;"  value="<?php echo $iter["lname"]; ?>"><span class="mandatory">&nbsp;*</span></td>
<td><span class=text><?php xl('Default Facility','e'); ?>: </span></td><td><select name=facility_id style="width:150px;" >
<?php
$fres = sqlStatement("select * from facility where service_location != 0 order by name");
if ($fres) {
for ($iter2 = 0; $frow = sqlFetchArray($fres); $iter2++)
                $result[$iter2] = $frow;
foreach($result as $iter2) {
?>
  <option value="<?php echo $iter2['id']; ?>" <?php if ($iter['facility_id'] == $iter2['id']) echo "selected"; ?>><?php echo htmlspecialchars($iter2['name']); ?></option>
<?php
}
}
?>
</select></td>
</tr>

<?php if ($GLOBALS['restrict_user_facility']) { ?>
<tr>
 <td colspan=2>&nbsp;</td>
 <td><span class=text><?php xl('Schedule Facilities:', 'e');?></td>
 <td>
  <select name="schedule_facility[]" multiple style="width:150px;" >
<?php
  $userFacilities = getUserFacilities($_GET['id']);
  $ufid = array();
  foreach($userFacilities as $uf)
    $ufid[] = $uf['id'];
  $fres = sqlStatement("select * from facility where service_location != 0 order by name");
  if ($fres) {
    while($frow = sqlFetchArray($fres)):
?>
   <option <?php echo in_array($frow['id'], $ufid) || $frow['id'] == $iter['facility_id'] ? "selected" : null ?>
      value="<?php echo $frow['id'] ?>"><?php echo htmlspecialchars($frow['name']) ?></option>
<?php
  endwhile;
}
?>
  </select>
 </td>
</tr>
<?php } ?>

<TR>
<TD><span class=text><?php xl('Federal Tax ID','e'); ?>: </span></TD><TD><input type=text name=taxid style="width:150px;"  value="<?php echo $iter["federaltaxid"]?>"></td>
<TD><span class=text><?php xl('Federal Drug ID','e'); ?>: </span></TD><TD><input type=text name=drugid style="width:150px;"  value="<?php echo $iter["federaldrugid"]?>"></td>
</TR>

<tr>
<td><span class="text"><?php xl('UPIN','e'); ?>: </span></td><td><input type="text" name="upin" style="width:150px;" value="<?php echo $iter["upin"]?>"></td>
<td class='text'><?php xl('See Authorizations','e'); ?>: </td>
<td><select name="see_auth" style="width:150px;" >
<?php
 foreach (array(1 => xl('None'), 2 => xl('Only Mine'), 3 => xl('All')) as $key => $value)
 {
  echo " <option value='$key'";
  if ($key == $iter['see_auth']) echo " selected";
  echo ">$value</option>\n";
 }
?>
</select></td>
</tr>

<tr>
<td><span class="text"><?php xl('NPI','e'); ?>: </span></td><td><input type="text" name="npi" style="width:150px;"  value="<?php echo $iter["npi"]?>"></td>
<td><span class="text"><?php xl('Job Description','e'); ?>: </span></td><td><input type="text" name="job" style="width:150px;"  value="<?php echo $iter["specialty"]?>"></td>
</tr>

<?php if (!empty($GLOBALS['ssi']['rh'])) { ?>
<tr>
<td><span class="text"><?php xl('Relay Health ID', 'e'); ?>: </span></td>
<td><input type="password" name="ssi_relayhealth" style="width:150px;"  value="<?php echo $iter["ssi_relayhealth"]; ?>"></td>
</tr>
<?php } ?>

<!-- (CHEMED) Calendar UI preference -->
<tr>
<td><span class="text"><?php xl('Taxonomy','e'); ?>: </span></td>
<td><input type="text" name="taxonomy" style="width:150px;"  value="<?php echo $iter["taxonomy"]?>"></td>
<td><span class="text"><?php xl('Calendar UI','e'); ?>: </span></td><td><select name="cal_ui" style="width:150px;" >
<?php
 foreach (array(3 => xl('Outlook'), 1 => xl('Original'), 2 => xl('Fancy')) as $key => $value)
 {
  echo " <option value='$key'";
  if ($key == $iter['cal_ui']) echo " selected";
  echo ">$value</option>\n";
 }
?>
</select></td>
</tr>
<!-- END (CHEMED) Calendar UI preference -->

<tr>
<td><span class="text"><?php xl('State License Number','e'); ?>: </span></td>
<td><input type="text" name="state_license_number" style="width:150px;"  value="<?php echo $iter["state_license_number"]?>"></td>
<td class='text'><?php xl('NewCrop eRX Role','e'); ?>:</td>
<td>
  <?php echo generate_select_list("erxrole", "newcrop_erx_role", $iter['newcrop_user_role'],'','--Select Role--','','','',array('style'=>'width:150px')); ?>
</td>
</tr>

<?php if ($GLOBALS['inhouse_pharmacy']) { ?>
<tr>
 <td class="text"><?php xl('Default Warehouse','e'); ?>: </td>
 <td class='text'>
<?php
echo generate_select_list('default_warehouse', 'warehouse',
  $iter['default_warehouse'], '');
?>
 </td>
 <td class="text"><?php xl('Invoice Refno Pool','e'); ?>: </td>
 <td class='text'>
<?php
echo generate_select_list('irnpool', 'irnpool', $iter['irnpool'],
  xl('Invoice reference number pool, if used'));
?>
 </td>
</tr>
<?php } ?>

<?php
 // Collect the access control group of user
 if (isset($phpgacl_location) && acl_check('admin', 'acl')) {
?>
  <tr>
  <td class='text'><?php xl('Access Control','e'); ?>:</td>
  <td><select id="access_group_id" name="access_group[]" multiple style="width:150px;" >
  <?php
   $list_acl_groups = acl_get_group_title_list();
   $username_acl_groups = acl_get_group_titles($iter["username"]);
   foreach ($list_acl_groups as $value) {
    if (($username_acl_groups) && in_array($value,$username_acl_groups)) {
     // Modified 6-2009 by BM - Translate group name if applicable
     echo " <option value='$value' selected>" . xl_gacl_group($value) . "</option>\n";
    }
    else {
     // Modified 6-2009 by BM - Translate group name if applicable
     echo " <option value='$value'>" . xl_gacl_group($value) . "</option>\n";
    }
   }
  ?>
  </select></td>
  <td><span class=text><?php xl('Additional Info','e'); ?>:</span></td>
  <td><textarea style="width:150px;" name="comments" wrap=auto rows=4 cols=25><?php echo $iter["info"];?></textarea></td>

  </tr>
  <tr height="20" valign="bottom">
  <td colspan="4" class="text">
  <font class="mandatory">*</font> <?php xl('You must enter your own password to change user passwords. Leave blank to keep password unchanged.','e'); ?>
<!--
Display red alert if entered password matched one of last three passwords/Display red alert if user password was expired and the user was inactivated previously
-->
  <div class="redtext" id="error_message">&nbsp;</div>
  </td>
  </tr>
<?php
 }
?>
</table>

<INPUT TYPE="HIDDEN" NAME="id" VALUE="<?php echo $_GET["id"]; ?>">
<INPUT TYPE="HIDDEN" NAME="mode" VALUE="update">
<INPUT TYPE="HIDDEN" NAME="privatemode" VALUE="user_admin">

<INPUT TYPE="HIDDEN" NAME="secure_pwd" VALUE="<?php echo $GLOBALS['secure_password']; ?>">
               
    <!--------------------------------------->
     
  

       
    <table border='0' width='100%' bgcolor='#DDDDDD' id='table_provider_attributes_edit' style='display:none;'> 
 
        <tr><td valign="top"><b><?php echo xlt('Groups'); ?>:</b></td>
        <td>
                   <table border="0" width="100%"
                       
                <?php

                $getGroups=sqlStatement("SELECT DISTINCT mg.Grouping_Name
       FROM tbl_allcare_provider1to1_fieldmapping mg
       INNER JOIN tbl_allcare_tablemeta_provider addr 
       ON addr.Field_ID=mg.Field_ID AND mg.Table_ID=1
       ORDER BY mg.Grouping_Name, addr.field_Name");
       $cnt=0;       
       $sqlGroupRows_provider_edit = sqlNumRows($getGroups);
              
       if($sqlGroupRows_provider_edit>0)
       {

                while($rowGroup=mysql_fetch_array($getGroups))
                {

                    echo ($cnt==0 ? "" : "<tr><td colspan='2'><hr/></td></tr>");
                    echo "<tr><td colspan=2><b>".$rowGroup['Grouping_Name']."</b></td></tr>";
                    $cnt++;

                    $getGroupFields=sqlStatement("SELECT mg.Grouping_ID,addr.field_Name
                                               FROM tbl_allcare_provider1to1_fieldmapping mg
                                               INNER JOIN tbl_allcare_tablemeta_provider addr 
                                               ON addr.Field_ID=mg.Field_ID
                                               AND mg.Table_ID=1 AND mg.Grouping_Name='".$rowGroup['Grouping_Name']."'
                                               ORDER BY mg.Grouping_Name, addr.field_Name");

                    while($rowGroupFields=mysql_fetch_array($getGroupFields))
                    {
                        $columnTypeqry1 =  sqlStatement("SHOW COLUMNS FROM tbl_allcare_provider1to1 where Field='".$rowGroupFields['field_Name']."'");
                           while($columnTypeRe = sqlFetchArray($columnTypeqry1)) 
                           {
                              $validationpart1='';
                              $defaultValue = ($columnTypeRe['Default']!='' ? $columnTypeRe['Default'] : '');
                              $first3letters = substr($columnTypeRe['Type'], 0, 3) ; 
                              if( ($first3letters=='int'  || $first3letters=='flo' || $first3letters =='boo'  || $first3letters=='big' ) )
                              {
                                    $validationpart1= "onkeypress='return isNumber(event);'" ;

                              }

                               echo "<tr><td width='25%' align=left><b>".$rowGroupFields['field_Name'].":</b></td>";

                               $columnvalueqry1Rows=0;
                               
                                if ($userid) 
                                {
                                    //echo "<br>select ".$rowGroupFields['field_Name']." from tbl_allcare_provider1to1 where provider_id=".$userid;
                                    $columnvalueqry1 =  sqlStatement("select ".$rowGroupFields['field_Name']." from tbl_allcare_provider1to1 where provider_id=$userid");
                                    $columnvalueqry1Rows = sqlNumRows($columnvalueqry1);    
                                }
                                if($columnvalueqry1Rows>0)
                                {
                                   while($columnvalueRes = sqlFetchArray($columnvalueqry1)) 
                                   {
                                        echo "<td align='left'>";
                                        $_POST[$rowGroupFields['field_Name']]=$columnvalueRes[$rowGroupFields['field_Name']];
                                        echo "<input type='text' size='40'  value='".$columnvalueRes[$rowGroupFields['field_Name']]."' name='".$rowGroupFields['field_Name']."' maxlength='20' $validationpart1 style='width:100%' class='inputtext'
                                                     onchange='' />";
                                        $_POST[$rowGroupFields['field_Name']]=$columnvalueRes[$rowGroupFields['field_Name']];
                                        
                                        echo "</td></tr>";
                                   }

                                }
                                else
                                {
                                   echo "<td align='left'>"
                                       . "<input type='text' size='40'  value='$defaultValue' name='".$rowGroupFields['field_Name']."' maxlength='20' $validationpart1 style='width:100%' class='inputtext' />"
                                       . "</td></tr>";

                                }

                               $GroupFieldNames_provider_edit .= $rowGroupFields['field_Name'].',';
                           }
                    }

                }

               $GroupFieldNames_provider_edit = substr($GroupFieldNames_provider_edit,0,strlen($GroupFieldNames_provider_edit)-1);
         }
         
       
                ?>
                </table>
        </td>
        </tr>
        <tr><td colspan='2'>&nbsp;</td></tr>
        <tr><td valign="top"><b><?php echo xlt('Recordsets'); ?>:</b></td>
        <td valign="top">
            <table border="0" width="100%">
                
         <?php
         
         $getRecordSet = sqlStatement("SELECT DISTINCT mg.Recordset_Name,mg.Recordset_ID
                                       FROM tbl_allcare_provider1ton_fieldmapping mg
                                       INNER JOIN tbl_allcare_tablemeta_provider addr 
                                       ON addr.Field_ID=mg.Recordset_ID 
                                       AND mg.Table_ID=2
                                       ORDER BY mg.Recordset_Name, addr.field_Name");
        $sqlRecordsetRows = sqlNumRows($getRecordSet);

        if($sqlRecordsetRows>0)
        {
         
         $cnt=0;
         while($rowRecordSet=mysql_fetch_array($getRecordSet))
         {
              
              //echo ($cnt==0 ? "" : "<tr><td colspan='2'><hr/></td></tr>");
             echo "<tr><td><b>".$rowRecordSet['Recordset_Name']."</b></td>";
             echo "<td align='right'>";?>
             <a href='javascript:void(0);' onclick="javascript:addtablerow('tbl<?php echo $rowRecordSet['Recordset_ID'];?>','<?php echo $rowRecordSet['Recordset_ID'];?>');">Add</a>
             <?php
             echo "</td></tr>";
            //$cnt++;
             echo "<tr><td colspan=2>";
             echo "<table border='1' cellpadding='1' cellspacing='0' width='100%' id='tbl".$rowRecordSet['Recordset_ID']."' ><tr>";
             
           
             $getRecordsetSql ="SELECT mg.Recordset_Name,mg.Recordset_ID,addr.field_Name
                                FROM tbl_allcare_provider1ton_fieldmapping mg
                                INNER JOIN tbl_allcare_tablemeta_provider addr 
                                ON addr.Field_ID=mg.Field_ID 
                                AND mg.Table_ID=2 AND mg.Recordset_Name='".$rowRecordSet['Recordset_Name']."'
                                ORDER BY mg.Recordset_Name, addr.field_Name";
             $getRecordsetFields1=sqlStatement($getRecordsetSql);
             $getRecordsetFields=sqlStatement($getRecordsetSql);
             $colcount=0;
             while($rowRecordsetFields1=mysql_fetch_array($getRecordsetFields1))
             {
                 
                         echo "<td width='25%' align=left><b>".$rowRecordsetFields1['field_Name'].":</b></td>";
                          $RecordsetFieldNames .= $rowRecordsetFields1['field_Name'].',';
                          $colcount++;
                     
             }
             echo "<td>&nbsp;</td></tr>";
             
              $RecordsetFieldNames = substr($RecordsetFieldNames,0,strlen($RecordsetFieldNames)-1);
            echo "<input type='hidden' name='hiddenrecid[$rowRecordSet[Recordset_ID]]' value='$RecordsetFieldNames' />";
            
            
            $updateRecorsetRows=0;
            if($userid)
            {
            $updateRecorsetSql = "select $RecordsetFieldNames from tbl_allcare_provider1ton where Recordset_ID=$rowRecordSet[Recordset_ID] and provider_id=".$userid;
            
            $updateRecorsetqry=sqlStatement($updateRecorsetSql);
            $updateRecorsetRows = sqlNumRows($updateRecorsetqry);
            }
            if($updateRecorsetRows>0)
            {
                echo "<input type='hidden' name='hiddenaddcount[$rowRecordSet[Recordset_ID]]' id='hiddenaddcount[$rowRecordSet[Recordset_ID]]' value='".$updateRecorsetRows."' />";
                $cnt1=0;
                while($updateRecorsetRes=mysql_fetch_array($updateRecorsetqry))
                {
                   // echo "<pre>";print_r($updateRecorsetRes);echo "</pre>";
                      echo "<tr>";
                      for ($cnt2=0;$cnt2<$colcount;$cnt2++)
                      {
                          
                          $splitNames = explode(",", $RecordsetFieldNames);
                          
                          $columnTypeqry2 =  sqlStatement("SHOW COLUMNS FROM tbl_allcare_provider1ton where Field='".$splitNames[$cnt2]."'");
                           while($columnTypeRes = sqlFetchArray($columnTypeqry2)) 
                           {    
                                 $validationpart1='';
                                 $first3letters = substr($columnTypeRes['Type'], 0, 3) ; 
                                      if( ($first3letters=='int'  || $first3letters=='flo' || $first3letters =='boo'  || $first3letters=='big' ) )
                                      {
                                            $validationpart1= "onkeypress='return isNumber(event);'" ;

                                      }
                                 
                            
                               echo "<td>";
                                echo "<input type='text' value='".$updateRecorsetRes[$cnt1++]."' name='".$rowRecordSet['Recordset_ID']."[]' maxlength='20' $validationpart1 style='width:80%' class='inputtext' />";
                                echo "</td>";
                                
                           }
                           
                      }
                         echo '<td><input type="button" value="Remove" onclick="deleteRow(this,'.$rowRecordSet['Recordset_ID'].');" /></td>';     
                        $cnt1=0;
                        
                        echo "</tr>";
                    }
                    
                    echo "</table>";     
            
            }
            else
            {
                echo "<input type='hidden' name='hiddenaddcount[$rowRecordSet[Recordset_ID]]' id='hiddenaddcount[$rowRecordSet[Recordset_ID]]' value='1' />";
                
                echo "<tr>";
                    while($rowRecordsetFields=mysql_fetch_array($getRecordsetFields))
                    {

                        $columnTypeqry2 =  sqlStatement("SHOW COLUMNS FROM tbl_allcare_provider1ton where Field='".$rowRecordsetFields['field_Name']."'");
                           while($columnTypeRes = sqlFetchArray($columnTypeqry2)) 
                           {
                                       $validationpart1='';
                                      $defaultValue = ($columnTypeRes['Default']!='' ? $columnTypeRes['Default'] : '');
                                      $first3letters = substr($columnTypeRes['Type'], 0, 3) ; 
                                      if( ($first3letters=='int'  || $first3letters=='flo' || $first3letters =='boo'  ) )
                                      {
                                            $validationpart1= "onkeypress='return isNumber(event);'" ;

                                      }

                                 echo "<td>";
                                 echo "<input type='text' value='$defaultValue' name='".$rowRecordsetFields['Recordset_ID']."[]' maxlength='20' $validationpart1 style='width:80%' class='inputtext' />";

                                 echo "</td>";
                               
                            }
                    }
                    
                    echo '<td><input type="button" value="Remove" onclick="deleteRow(this,'.$rowRecordSet['Recordset_ID'].');" /></td>';     
                    echo "</tr></table>";     
            
            }
            
            
$RecordsetFieldNames='';

             echo "</td></tr>";
             
            
         } //==
         
        
   }
                          
         ?>
            
            </table>
             </td>
        </tr>
  
</table>

</div>
    

<input type="hidden" name="hiddensqlGroupRows_provider_edit" value="<?php echo $sqlGroupRows_provider_edit;?>" >
<input type="hidden" name="hiddenarrayGroup_provider_edit" value="<?php echo $GroupFieldNames_provider_edit;?>" >

    
    <!--------------------------------------->
    

</FORM>


<script language="JavaScript">
$(document).ready(function(){
    $("#cancel").click(function() {
		  parent.$.fn.fancybox.close();
	 });

           if(!$('#authorized').attr("checked"))
           {
               
                $('#tabProviderAttributes_edit').hide();
                $('#table_provider_attributes_edit').hide();

           }


});


        function provider_clicked(provider_check)
        {
           if(jQuery('#'+provider_check).attr("checked"))
           {
               $('#tabProviderAttributes_edit').show();                                
           }
           if(!jQuery('#'+provider_check).attr("checked"))
           {
               jQuery('#table_provider_attributes_edit').hide();                  
               $('#tabProviderAttributes_edit').hide();
               
           }
        }
     

</script>

</table>
</BODY>

</HTML>

<?php
//  d41d8cd98f00b204e9800998ecf8427e == blank
?>
