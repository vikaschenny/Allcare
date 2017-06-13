
<?php
require_once("../globals.php");
require_once("../../library/acl.inc");
require_once("$srcdir/sha1.js");
require_once("$srcdir/sql.inc");
require_once("$srcdir/formdata.inc.php");
require_once("$srcdir/options.inc.php");
require_once(dirname(__FILE__) . "/../../library/classes/WSProvider.class.php");
require_once("$srcdir/erx_javascript.inc.php");

$alertmsg = '';

?>
<html>
<head>

<link rel="stylesheet" href="<?php echo $css_header;?>" type="text/css">
<link rel="stylesheet" href="<?php echo $css_header;?>" type="text/css">
<link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['webroot'] ?>/library/js/fancybox/jquery.fancybox-1.2.6.css" media="screen" />
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/dialog.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/jquery.1.3.2.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/common.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/fancybox/jquery.fancybox-1.2.6.js"></script>

<script src="checkpwd_validation.js" type="text/javascript"></script>

<script language="JavaScript">
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

function submitform() {
    if (document.forms[0].rumple.value.length>0 && document.forms[0].stiltskin.value.length>0 && document.getElementById('fname').value.length >0 && document.getElementById('lname').value.length >0) {
       top.restoreSession();

       //Checking if secure password is enabled or disabled.
       //If it is enabled and entered password is a weak password, alert the user to enter strong password.
       if(document.new_user.secure_pwd.value == 1){
          var password = trim(document.new_user.stiltskin.value);
          if(password != "") {
             var pwdresult = passwordvalidate(password);
             if(pwdresult == 0){
                alert("<?php echo xl('The password must be at least eight characters, and should'); echo '\n'; echo xl('contain at least three of the four following items:'); echo '\n'; echo xl('A number'); echo '\n'; echo xl('A lowercase letter'); echo '\n'; echo xl('An uppercase letter'); echo '\n'; echo xl('A special character');echo '('; echo xl('not a letter or number'); echo ').'; echo '\n'; echo xl('For example:'); echo ' healthCare@09'; ?>");
                return false;
             }
          }
       } //secure_pwd if ends here

       <?php if($GLOBALS['erx_enable']){ ?>
       alertMsg='';
       f=document.forms[0];
       for(i=0;i<f.length;i++){
          if(f[i].type=='text' && f[i].value)
          {
             if(f[i].name == 'rumple')
             {
                alertMsg += checkLength(f[i].name,f[i].value,35);
                alertMsg += checkUsername(f[i].name,f[i].value);
             }
             else if(f[i].name == 'fname' || f[i].name == 'mname' || f[i].name == 'lname')
             {
                alertMsg += checkLength(f[i].name,f[i].value,35);
                alertMsg += checkUsername(f[i].name,f[i].value);
             }
             else if(f[i].name == 'federaltaxid')
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
                alertMsg += checkLength(f[i].name,f[i].value,35);
                alertMsg += checkTaxNpiDea(f[i].name,f[i].value);
             }
             else if(f[i].name == 'federaldrugid')
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
       <?php } // End erx_enable only include block?>

        document.forms[0].submit();
        parent.$.fn.fancybox.close(); 

    } else {
       if (document.forms[0].rumple.value.length<=0)
       {
          document.forms[0].rumple.style.backgroundColor="red";
          alert("<?php xl('Required field missing: Please enter the User Name','e');?>");
          document.forms[0].rumple.focus();
          return false;
       }
       if (document.forms[0].stiltskin.value.length<=0)
       {
          document.forms[0].stiltskin.style.backgroundColor="red";
          alert("<?php echo xl('Please enter the password'); ?>");
          document.forms[0].stiltskin.focus();
          return false;
       }
       if(trimAll(document.getElementById('fname').value) == ""){
          document.getElementById('fname').style.backgroundColor="red";
          alert("<?php xl('Required field missing: Please enter the First name','e');?>");
          document.getElementById('fname').focus();
          return false;
       }
       if(trimAll(document.getElementById('lname').value) == ""){
          document.getElementById('lname').style.backgroundColor="red";
          alert("<?php xl('Required field missing: Please enter the Last name','e');?>");
          document.getElementById('lname').focus();
          return false;
       }
    }
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

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

</head>
<body class="body_top">
<table><tr><td>
<span class="title"><?php xl('Add User','e'); ?></span>&nbsp;</td>
<td>
<a class="css_button" name='form_save' id='form_save' href='#' onclick="return submitform()">
	<span><?php xl('Save','e');?></span></a>
<a class="css_button large_button" id='cancel' href='#'>
	<span class='css_button_span large_button_span'><?php xl('Cancel','e');?></span>
</a>
</td></tr></table>
<br>

<table border=0>
    <tr>
        <td colspan='4'>
             <ul class="tabNav">

<li class='current'>
<a onclick="javascript:jQuery('#table_provider_attributes').hide();
					   jQuery('#table_provider_signature').hide();
                       jQuery('#table_user').show();
                       jQuery('.tabNav > li').removeClass('current');
                       jQuery(this).parent('li').addClass('current');" style='cursor:pointer' id="tabUserDetails"> User Details</a>
</li>
<!--<li>
<a onclick="javascript:jQuery('#table_user').hide();
					   jQuery('#table_provider_signature').hide();
                       jQuery('#table_provider_attributes').show();
                       jQuery('.tabNav > li').removeClass('current');
                       jQuery(this).parent('li').addClass('current');" style='cursor:pointer' id="tabProviderAttributes"> User Attributes</a>
</li>-->
<li>
<a onclick="javascript:jQuery('#table_user').hide();
                       jQuery('#table_provider_attributes').hide();
					   jQuery('#table_provider_signature').show();
                       jQuery('.tabNav > li').removeClass('current');
                       jQuery(this).parent('li').addClass('current');" style='cursor:pointer' id="tabProviderSignature"> User Signature</a>
</li>
</ul>    
        </td>
    </tr>
<tr><td valign=top>
   
    <br>
<form name='new_user' method='post'  target="_parent" action="usergroup_admin.php"
 onsubmit='return top.restoreSession()' enctype = "multipart/form-data">
 
    
<input type='hidden' name='mode' value='new_user'>
<input type='hidden' name='secure_pwd' value="<?php echo $GLOBALS['secure_password']; ?>">
     
    <!--<table border='0' width='100%' bgcolor='#DDDDDD' id='table_provider_attributes' align="center"> 
       
    </table>-->
	<table border='0' width='100%' id='table_provider_signature' align="center"> 
		<tr>
			<td><span class=text><?php xl('Signature Image','e'); ?>:</span></td>
			<td><input type="file" id="imgSignature" name="imgSignature" multiple accept="image/*"
			onchange="javascript:readURL(this,'img_sign','');" />
      
			</td>
      
		</tr>
		<tr>
			<td></td>
			<td>
				<div style="height:300px;width:300px">
					<img id="img_sign" style="height:100%;width:100%"/>
					<script>
					  
					function readURL(input,imageID,hdnImageID)
					{
						if (input.files && input.files[0])
						{
						 var reader = new FileReader();
						 reader.onload = function (e){
							$('#'+imageID).attr('src',e.target.result);

							//$('#'+hdnImageID).attr('value',e.target.result);

						 };
						 reader.readAsDataURL(input.files[0]);
						 }
					}
			 
					jQuery('#img_sign').attr('src',jQuery('#imgSignature').val());
					</script>
				</div>      
			</td>
		</tr>
    </table>

<span class="bold">&nbsp;</span>
</td><td>
<table border='0' cellpadding='0' cellspacing='0' id="table_user">
<tr>
<td style="width:150px;"><span class="text"><?php xl('Username','e'); ?>: </span></td><td  style="width:220px;"><input type=entry name=rumple style="width:120px;"> <span class="mandatory">&nbsp;*</span></td>
<td style="width:150px;"><span class="text"><?php xl('Password','e'); ?>: </span></td><td style="width:250px;"><input type="entry" style="width:120px;" name=stiltskin><span class="mandatory">&nbsp;*</span></td>
</tr>
<tr>
    <td style="width:150px;"></td><td  style="width:220px;"></span></td>
    <TD style="width:200px;"><span class=text><?php xl('Your Password','e'); ?>: </span></TD>
    <TD class='text' style="width:280px;"><input type='password' name=adminPass style="width:120px;"  value="" autocomplete='off'><font class="mandatory">*</font></TD>

</tr>
<tr>
<td><span class="text"<?php if ($GLOBALS['disable_non_default_groups']) echo " style='display:none'"; ?>><?php xl('Groupname','e'); ?>: </span></td>
<td>
<select name=groupname<?php if ($GLOBALS['disable_non_default_groups']) echo " style='display:none'"; ?>>
<?php
$res = sqlStatement("select distinct name from groups");
$result2 = array();
for ($iter = 0;$row = sqlFetchArray($res);$iter++)
  $result2[$iter] = $row;
foreach ($result2 as $iter) {
  print "<option value='".$iter{"name"}."'>" . $iter{"name"} . "</option>\n";
}
?>
</select></td>
<td><span class="text"><?php xl('Provider','e'); ?>: </span></td><td>
 <input type='checkbox' name='authorized' value='1' id='authorized'
        onclick='authorized_clicked();
                 //provider_clicked(this.id);
                 ' />
 &nbsp;&nbsp;<span class='text'><?php xl('Calendar','e'); ?>:
 <input type='checkbox' name='calendar' disabled />
</td>
</tr>
<tr>
<td><span class="text"><?php xl('First Name','e'); ?>: </span></td><td><input type=entry name='fname' id='fname' style="width:120px;"><span class="mandatory">&nbsp;*</span></td>
<td><span class="text"><?php xl('Middle Name','e'); ?>: </span></td><td><input type=entry name='mname' style="width:120px;"></td>
</tr>
<tr>
<td><span class="text"><?php xl('Last Name','e'); ?>: </span></td><td><input type=entry name='lname' id='lname' style="width:120px;"><span class="mandatory">&nbsp;*</span></td>
<td><span class="text"><?php xl('Default Facility','e'); ?>: </span></td><td><select style="width:120px;" name=facility_id>
<?php
$fres = sqlStatement("select * from facility where service_location != 0 order by name");
if ($fres) {
  for ($iter = 0;$frow = sqlFetchArray($fres);$iter++)
    $result[$iter] = $frow;
  foreach($result as $iter) {
?>
<option value="<?php echo $iter{'id'};?>"><?php echo $iter{'name'};?></option>
<?php
  }
}
?>
</select></td>
</tr>
<tr>
<td><span class="text"><?php xl('Federal Tax ID','e'); ?>: </span></td><td><input type=entry name='federaltaxid' style="width:120px;"></td>
<td><span class="text"><?php xl('Federal Drug ID','e'); ?>: </span></td><td><input type=entry name='federaldrugid' style="width:120px;"></td>
</tr>
<tr>
<td><span class="text"><?php xl('UPIN','e'); ?>: </span></td><td><input type="entry" name="upin" style="width:120px;"></td>
<td class='text'><?php xl('See Authorizations','e'); ?>: </td>
<td><select name="see_auth" style="width:120px;">
<?php
 foreach (array(1 => xl('None'), 2 => xl('Only Mine'), 3 => xl('All')) as $key => $value)
 {
  echo " <option value='$key'";
  echo ">$value</option>\n";
 }
?>
</select></td>

<tr>
<td><span class="text"><?php xl('NPI','e'); ?>: </span></td><td><input type="entry" name="npi" style="width:120px;"></td>
<td><span class="text"><?php xl('Job Description','e'); ?>: </span></td><td><input type="entry" name="specialty" style="width:120px;"></td>
</tr>

<!-- (CHEMED) Calendar UI preference -->
<tr>
<td><span class="text"><?php xl('Taxonomy','e'); ?>: </span></td>
<td><input type="entry" name="taxonomy" style="width:120px;" value="207Q00000X"></td>
<td><span class="text"><?php xl('Calendar UI','e'); ?>: </span></td><td><select name="cal_ui" style="width:120px;">
<?php
 foreach (array(3 => xl('Outlook'), 1 => xl('Original'), 2 => xl('Fancy')) as $key => $value)
 {
  echo " <option value='$key'>$value</option>\n";
 }
?>
</select></td>
</tr>
<!-- END (CHEMED) Calendar UI preference -->

<tr>
<td><span class="text"><?php xl('State License Number','e'); ?>: </span></td>
<td><input type="text" name="state_license_number" style="width:120px;"></td>
<td class='text'><?php xl('NewCrop eRX Role','e'); ?>:</td>
<td>
  <?php echo generate_select_list("erxrole", "newcrop_erx_role", '','','--Select Role--','','','',array('style'=>'width:120px')); ?>  
</td>
</tr>

<?php if ($GLOBALS['inhouse_pharmacy']) { ?>
<tr>
 <td class="text"><?php xl('Default Warehouse','e'); ?>: </td>
 <td class='text'>
<?php
echo generate_select_list('default_warehouse', 'warehouse',
  '', '');
?>
 </td>
 <td class="text"><?php xl('Invoice Refno Pool','e'); ?>: </td>
 <td class='text'>
<?php
echo generate_select_list('irnpool', 'irnpool', '',
  xl('Invoice reference number pool, if used'));
?>
 </td>
</tr>
<?php } ?>

<?php
 // List the access control groups if phpgacl installed
 if (isset($phpgacl_location) && acl_check('admin', 'acl')) {
?>
  <tr>
  <td class='text'><?php xl('Access Control','e'); ?>:</td>
  <td><select name="access_group[]" multiple style="width:120px;">
  <?php
   $list_acl_groups = acl_get_group_title_list();
   $default_acl_group = 'Administrators';
   foreach ($list_acl_groups as $value) {
    if ($default_acl_group == $value) {
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
  <td><span class="text"><?php xl('Additional Info','e'); ?>: </span></td>
  <td><textarea name=info style="width:120px;" cols=27 rows=4 wrap=auto></textarea></td>

  </tr>
  
  <!--<tr>
      <td><span class=text><?php xl('Signature Image','e'); ?>:</span></td>
      <td><input type="file" id="imgSignature" name="imgSignature" multiple accept="image/*"
           onchange="javascript:readURL(this,'img_sign','');" />
      
      </td>
      
  </tr>
  
  <tr>
      <td></td>
      <td>
          <div style="height:300px;width:300px">
              <img id="img_sign" style="height:100%;width:100%"/>
              <script>
                  
		 function readURL(input,imageID,hdnImageID)
		 {
                    if (input.files && input.files[0])
                    {
                     var reader = new FileReader();
                     reader.onload = function (e){
                        $('#'+imageID).attr('src',e.target.result);

                        //$('#'+hdnImageID).attr('value',e.target.result);

                     };
                     reader.readAsDataURL(input.files[0]);
                     }
		 }
		 
                jQuery('#img_sign').attr('src',jQuery('#imgSignature').val());
              </script>
          </div>      
      </td>
  </tr>-->
  
  <tr height="25"><td colspan="4">&nbsp;</td></tr>
<?php
 }
?>

</table>

<br>
<input type="hidden" name="newauthPass">

</td>

</form>
</tr>

<tr<?php if ($GLOBALS['disable_non_default_groups']) echo " style='display:none'"; ?>>

<td valign=top>
<form name='new_group' method='post' action="usergroup_admin.php"
 onsubmit='return top.restoreSession()' enctype = "multipart/form-data">
<br>
<input type=hidden name=mode value=new_group>
<span class="bold"><?php xl('New Group','e'); ?>:</span>

<span class="text"><?php xl('Groupname','e'); ?>: </span><input type=entry name=groupname size=10>
&nbsp;&nbsp;&nbsp;
<span class="text"><?php xl('Initial User','e'); ?>: </span>
<select name=rumple>
<?php
$res = sqlStatement("select distinct username from users where username != ''");
for ($iter = 0;$row = sqlFetchArray($res);$iter++)
  $result[$iter] = $row;
foreach ($result as $iter) {
  print "<option value='".$iter{"username"}."'>" . $iter{"username"} . "</option>\n";
}
?>
</select>
&nbsp;&nbsp;&nbsp;
<input type="submit" value=<?php xl('Save','e'); ?>>
</form>
</td>

</tr>

<tr <?php if ($GLOBALS['disable_non_default_groups']) echo " style='display:none'"; ?>>

<td valign=top>
<form name='new_group' method='post' action="usergroup_admin.php"
 onsubmit='return top.restoreSession()' enctype = "multipart/form-data">
<input type=hidden name=mode value=new_group>
<span class="bold"><?php xl('Add User To Group','e'); ?>:</span>
</td><td>
<span class="text">
<?php xl('User','e'); ?>
: </span>
<select name=rumple>
<?php
$res = sqlStatement("select distinct username from users where username != ''");
for ($iter = 0;$row = sqlFetchArray($res);$iter++)
  $result3[$iter] = $row;
foreach ($result3 as $iter) {
  print "<option value='".$iter{"username"}."'>" . $iter{"username"} . "</option>\n";
}
?>
</select>
&nbsp;&nbsp;&nbsp;
<span class="text"><?php xl('Groupname','e'); ?>: </span>
<select name=groupname>
<?php
$res = sqlStatement("select distinct name from groups");
$result2 = array();
for ($iter = 0;$row = sqlFetchArray($res);$iter++)
  $result2[$iter] = $row;
foreach ($result2 as $iter) {
  print "<option value='".$iter{"name"}."'>" . $iter{"name"} . "</option>\n";
}
?>
</select>
&nbsp;&nbsp;&nbsp;
<input type="submit" value=<?php xl('Add User To Group','e'); ?>>
</form>
</td>
</tr>

</table>

<?php
if (empty($GLOBALS['disable_non_default_groups'])) {
  $res = sqlStatement("select * from groups order by name");
  for ($iter = 0;$row = sqlFetchArray($res);$iter++)
    $result5[$iter] = $row;

  foreach ($result5 as $iter) {
    $grouplist{$iter{"name"}} .= $iter{"user"} .
      "(<a class='link_submit' href='usergroup_admin.php?mode=delete_group&id=" .
      $iter{"id"} . "' onclick='top.restoreSession()'>Remove</a>), ";
  }

  foreach ($grouplist as $groupname => $list) {
    print "<span class='bold'>" . $groupname . "</span><br>\n<span class='text'>" .
      substr($list,0,strlen($list)-2) . "</span><br>\n";
  }
}
?>

<script language="JavaScript">
<?php
  if ($alertmsg = trim($alertmsg)) {
    echo "alert('$alertmsg');\n";
  }
?>
$(document).ready(function(){
    $("#cancel").click(function() {
		  parent.$.fn.fancybox.close();
	 });
                    
        
        //jQuery('#tabProviderAttributes').hide();

        jQuery('#table_provider_attributes').hide();
		jQuery('#table_provider_signature').hide();
        
        $.ajax({
                type: 'POST',
                url: 'display_provider_attributes.php',	
                
                success: function(response)
                {       
                    jQuery('#table_provider_attributes').html(response);
                },
                failure: function(response)
                {
                        alert("error");
                }		
               });
        
});

        function provider_clicked(provider_check)
        {
           if(jQuery('#'+provider_check).prop("checked"))
           {
               $('#tabProviderAttributes').show();
              
                  $.ajax({
                type: 'POST',
                url: 'display_provider_attributes.php',	
                
                success: function(response)
                {       
                    jQuery('#table_provider_attributes').html(response);
                },
                failure: function(response)
                {
                        alert("error");
                }		
               });
               
           }
           if(!jQuery('#'+provider_check).prop("checked"))
           {
               jQuery('#table_provider_attributes').hide();   
               jQuery('#table_provider_attributes').html('');
               $('#tabProviderAttributes').hide();
               
           }
        }
     
</script>
<table>

</table>

</body>
</html>
