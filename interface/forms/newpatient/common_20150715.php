<?php
/**
 * Common script for the encounter form (new and view) scripts.
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

require_once("$srcdir/options.inc.php");
require_once("../../globals.php");
require_once("$srcdir/amc.php");
include_once("$srcdir/formdata.inc.php");
include_once("$srcdir/patient.inc");
include_once("$srcdir/acl.inc");

$months = array("01","02","03","04","05","06","07","08","09","10","11","12");
$days = array("01","02","03","04","05","06","07","08","09","10","11","12","13","14",
  "15","16","17","18","19","20","21","22","23","24","25","26","27","28","29","30","31");
$thisyear = date("Y");
$years = array($thisyear-1, $thisyear, $thisyear+1, $thisyear+2);

if ($viewmode) {
  $id = (isset($_REQUEST['id'])) ? $_REQUEST['id'] : '';
  $result = sqlQuery("SELECT * FROM form_encounter WHERE id = ?", array($id));
  $encounter = $result['encounter'];
  if ($result['sensitivity'] && !acl_check('sensitivities', $result['sensitivity'])) {
    echo "<body>\n<html>\n";
    echo "<p>" . xlt('You are not authorized to see this encounter.') . "</p>\n";
    echo "</body>\n</html>\n";
    exit();
  }
}

// Sort comparison for sensitivities by their order attribute.
function sensitivity_compare($a, $b) {
  return ($a[2] < $b[2]) ? -1 : 1;
}

// get issues
$ires = sqlStatement("SELECT id, type, title, begdate FROM lists WHERE " .
  "pid = ? AND enddate IS NULL " .
  "ORDER BY type, begdate", array($pid));
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<?php html_header_show();?>
<title><?php echo xlt('Patient Encounter'); ?></title>

<link rel="stylesheet" href="<?php echo $css_header;?>" type="text/css">
<link rel="stylesheet" href="../../themes/jquery-ui.css" type="text/css">
<link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['webroot'] ?>/library/js/fancybox-1.3.4/jquery.fancybox-1.3.4.css" media="screen" />
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/jquery-1.4.3.min.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/common.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/fancybox-1.3.4/jquery.fancybox-1.3.4.pack.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/dialog.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/overlib_mini.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/textformat.js"></script>
<script src="<?php echo $GLOBALS['webroot'] ?>/library/js/jquery-ui-1.8.13.min.js"></script>
<!-- pop up calendar -->
<style type="text/css">@import url(<?php echo $GLOBALS['webroot'] ?>/library/dynarch_calendar.css);</style>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/dynarch_calendar.js"></script>
<?php include_once("{$GLOBALS['srcdir']}/dynarch_calendar_en.inc.php"); ?>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/dynarch_calendar_setup.js"></script>
<?php include_once("{$GLOBALS['srcdir']}/ajax/facility_ajax_jav.inc.php"); ?>
<script language="JavaScript">

 var mypcc = '<?php echo $GLOBALS['phone_country_code'] ?>';

 // Process click on issue title.
 function newissue() {
  dlgopen('../../patient_file/summary/add_edit_issue.php', '_blank', 800, 600);
  return false;
 }

 // callback from add_edit_issue.php:
 function refreshIssue(issue, title) {
  var s = document.forms[0]['issues[]'];
  s.options[s.options.length] = new Option(title, issue, true, true);
 }

 function saveClicked() {
  var f = document.forms[0];

<?php if (!$GLOBALS['athletic_team']) { ?>
  var category = document.forms[0].pc_catid.value;
  if ( category == '_blank' ) {
   alert("<?php echo xls('You must select a visit category'); ?>");
   return;
  }
<?php } ?>

<?php if (false /* $GLOBALS['ippf_specific'] */) { // ippf decided not to do this ?>
  if (f['issues[]'].selectedIndex < 0) {
   if (!confirm('There is no issue selected. If this visit relates to ' +
    'contraception or abortion, click Cancel now and then select or ' +
    'create the appropriate issue. Otherwise you can click OK.'))
   {
    return;
   }
  }
<?php } ?>
  top.restoreSession();
  f.submit();
 }

$(document).ready(function(){
  enable_big_modals();
});
function bill_loc(){
var pid=<?php echo attr($pid);?>;
var dte=document.getElementById('form_date').value;
var facility=document.forms[0].facility_id.value;
ajax_bill_loc(pid,dte,facility);
}

// Handler for Cancel clicked when creating a new encounter.
// Show demographics or encounters list depending on what frame we're in.
function cancelClicked() {
 if (window.name == 'RBot') {
  parent.left_nav.setRadio(window.name, 'ens');
  parent.left_nav.loadFrame('ens1', window.name, 'patient_file/history/encounters.php');
 }
 else {
  parent.left_nav.setRadio(window.name, 'dem');
  parent.left_nav.loadFrame('dem1', window.name, 'patient_file/summary/demographics.php');
 }
 return false;
}

function showforms()
{
  var copied_to='<?php  echo $_REQUEST['id']; ?>';
  //alert(copied_to);
var encounter=jQuery('#dos').val();
        //alert(encounter); 
        //alert('<?php echo $rootdir ?>/forms/newpatient/copy_template.php');
	$.ajax({
		type: 'POST',
		url: "<?php echo $rootdir ?>/forms/newpatient/copy_template.php",
                
		data: { 
                        encounter:encounter,
                        copied_to:copied_to
                },

		success: function(response)
		{
                    
                var result='';
                //alert(response);
                if( $('#template1').html(response)) {
                     
                   var answer = $("#template1").dialog()
                                    .find(':checkbox').unbind('change').bind('change', function(e){
                                         if(this.checked)  {  if($(this).val()!="undefined" && $(this).val()!=''){ result+=$(this).val()+","; }}
                                         else {  }
                                        //var result=$(this).val();
                                               // alert(result);
                                         });     
                                          
                                        $("#ok").click(function(e) {
                                             //alert(result);
                                             var formdetails=result.split(','); 
                                             for(var i=0;i<formdetails.length;i++)
                                               {
                                                 //  alert(formdetails[i]);
                                               
//                                            var result1=result;
//                                           //alert("Form Submitted Successfully......"+result);
                                           var copy_to_fname=formdetails[i].split('--');
                                           var copy_to_fname1=copy_to_fname[1];
                                           var form_name1=copy_to_fname[0].split('-');
                                           var copy_from_id= form_name1[0];
                                           var form_name=form_name1[1];
                                           //alert(copy_to_fname1+"=="+copy_from_id+"=="+form_name);
                                              $('#template1').dialog('close');
                                              
                                           $.ajax({
                                                            type: 'POST',
                                                            url: "<?php echo $rootdir ?>/forms/newpatient/copy_template_query.php",	
                                                            data: { //queryName:queryName,  
                                                                    copy_to_fname1:copy_to_fname1,
                                                                    copy_from_id:copy_from_id,form_name:form_name

                                                            },

                                                            success: function(response)
                                                            {
                                                             

                                                            },
                                                            failure: function(response)
                                                            {
                                                                    alert("error");
                                                            }		
                                                    });
                                                 }
                                                   alert('Copied Sucessfully...');
                                             });
                                           
//                                     if(this.checked) alert('checked box');
//                                    else alert('unchecked box');
                               // });
                            }
                             
//                  var formdetails=response.split(',');     
//                     for(var i=0;i<formdetails.length;i++)
//                       {      
//                         var copy_to_fname=formdetails[i].split('--');
//                         var copy_to_fname1=copy_to_fname[1];
//                         var form_name1=copy_to_fname[0].split('-');
//                        // alert(form_name1[1]);
//                             var copy_from_id= form_name1[0];
//                             var copy_from_fname=form_name1[1];
//                            
//                             if(form_name1[1]=='Allcare Encounter Forms'){
//                               <?php $layout_forms=sqlStatement("SELECT DISTINCT(group_name) FROM layout_options " .
                                                        "WHERE form_id = 'LBF2' AND uor > 0 AND field_id != ''   AND group_name IN ('2Chief Complaint','4Progress Note','5Assessment Note','6Plan Note','7Face to Face HH Plan','3History of Present illness')" .
                                                        "ORDER BY  seq");
                                     while($res=sqlFetchArray($layout_forms)) {?>//
//                                       var group_name= '<?php echo substr( $res['group_name'], 1);?>';  
//                                     
//                                         if(confirm('<?php echo substr( $res['group_name'], 1);?>')){
//                                           //alert(copy_to_fname1+"=="+copy_from_id);
//                                            $.ajax({
//                                                            type: 'POST',
//                                                            url: "<?php echo $rootdir ?>/forms/newpatient/copy_template_query.php",	
//                                                            data: { //queryName:queryName,  
//                                                                    copy_to_fname1:copy_to_fname1,
//                                                                    copy_from_id:copy_from_id,group_name:group_name
//
//                                                            },
//
//                                                            success: function(response)
//                                                            {
//                                                               alert(response);
//
//                                                            },
//                                                            failure: function(response)
//                                                            {
//                                                                    alert("error");
//                                                            }		
//                                                    });
//                                       }
//                                  <?php } ?>
//                             }else if(form_name1[1]=='Allcare Physical Exam'){
//                                
//                                 if(confirm(form_name1[1])){
//                                           //alert(copy_to_fname1+"=="+copy_from_id);
//                                           var form_name=form_name1[1];
//                                            $.ajax({
//                                                            type: 'POST',
//                                                            url: "<?php echo $rootdir ?>/forms/newpatient/copy_template_query.php",	
//                                                            data: { //queryName:queryName,  
//                                                                    copy_to_fname1:copy_to_fname1,
//                                                                    copy_from_id:copy_from_id,form_name:form_name
//
//                                                            },
//
//                                                            success: function(response)
//                                                            {
//                                                               alert(response);
//
//                                                            },
//                                                            failure: function(response)
//                                                            {
//                                                                    alert("error");
//                                                            }		
//                                                    });
//                                       }
//                           
//                           } else if(form_name1[1]=='Allcare Review Of Systems'){
//                                
//                                 if(confirm(form_name1[1])){
//                                           //alert(copy_to_fname1+"=="+copy_from_id);
//                                           var form_name=form_name1[1];
//                                            $.ajax({
//                                                            type: 'POST',
//                                                            url: "<?php echo $rootdir ?>/forms/newpatient/copy_template_query.php",	
//                                                            data: { //queryName:queryName,  
//                                                                    copy_to_fname1:copy_to_fname1,
//                                                                    copy_from_id:copy_from_id,form_name:form_name
//
//                                                            },
//
//                                                            success: function(response)
//                                                            {
//                                                               alert(response);
//
//                                                            },
//                                                            failure: function(response)
//                                                            {
//                                                                    alert("error");
//                                                            }		
//                                                    });
//                                       }
//                           
//                           } else if(form_name1[1]=='CPO'){
//                                
//                                 if(confirm(form_name1[1])){
//                                           //alert(copy_to_fname1+"=="+copy_from_id);
//                                           var form_name=form_name1[1];
//                                            $.ajax({
//                                                            type: 'POST',
//                                                            url: "<?php echo $rootdir ?>/forms/newpatient/copy_template_query.php",	
//                                                            data: { //queryName:queryName,  
//                                                                    copy_to_fname1:copy_to_fname1,
//                                                                    copy_from_id:copy_from_id,form_name:form_name
//
//                                                            },
//
//                                                            success: function(response)
//                                                            {
//                                                               alert(response);
//
//                                                            },
//                                                            failure: function(response)
//                                                            {
//                                                                    alert("error");
//                                                            }		
//                                                    });
//                                       }
//                           
//                           }
//                       }
		},
		failure: function(response)
		{
			alert("error");
		}		
	});	       
}
</script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script>
    $.noConflict();
    $(document).ready(function(){
        $('#form_audited_status').change(
            function() {
                var val1 = $('#form_audited_status option:selected').val();
                if(val1 === 'Completed'){
                    $("#form_elec_signedby").removeAttr('disabled');
                    $("#form_elec_signed_on").removeAttr('disabled');
                }else{
                    $("#form_elec_signedby").attr('disabled','disabled'); 
                    $("#form_elec_signed_on").attr('disabled','disabled'); 
                }
            }
        );
    });
</script>

<style>
/* .ui-widget-header {
  border: 0px solid #0b3e6f !important; 
  background: #FFDAB9 !important; 
  color: #f6f6f6 !important; 
  font-weight: bold;
}   */
    
.ui-widget {
  font-size: 1.0em !important;
/*  border: 1px solid #FFFFFF !important;*/
}
   
.ui-widget-content {
  border: 0px solid  !important;
  background: #FFDAB9 !important;
  //color: #d9d9d9 !important;
  color: #000000 !important;
}
.ui-dialog .ui-dialog-titlebar-close {
  position: absolute;
  top: -12px !important;
  right: -15px !important;
  height: 30px !important;
  width: 30px !important;
  cursor: pointer !important;
  z-index: 181 !important;
  background: url('../../../library/js/fancybox/fancy_closebox.png') top left no-repeat !important;
  //display: none !important;
}

/*.ui-dialog .ui-dialog-titlebar-close {
  position: absolute;
  top: -12px !important;
  right: -15px !important;
  height: 30px !important;
  width: 30px !important;
  background: url('../../../library/js/fancybox/fancy_closebox.png') top left no-repeat !important;
  cursor: pointer !important;
  z-index: 181 !important;
  display: none !important;
}*/
</style>
</head>

<?php if ($viewmode) { ?>
<body class="body_top">
<?php } else { ?>
<body class="body_top" onload="javascript:document.new_encounter.reason.focus();">
<?php } ?>

<!-- Required for the popup date selectors -->
<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>

<form method='post' action="<?php echo $rootdir ?>/forms/newpatient/save.php" name='new_encounter'
 <?php if (!$GLOBALS['concurrent_layout']) echo "target='Main'"; ?>>

<div style = 'float:left'>
<?php if ($viewmode) { ?>
<input type=hidden name='mode' value='update'>
<input type=hidden name='id' value='<?php echo (isset($_GET["id"])) ? attr($_GET["id"]) : '' ?>'>
<span class=title><?php echo xlt('Patient Encounter Form'); ?></span>
<?php } else { ?>
<input type='hidden' name='mode' value='new'>
<span class='title'><?php echo xlt('New Encounter Form'); ?></span>
<?php } ?>
</div>

<div>
    <div style = 'float:left; margin-left:8px;margin-top:-3px'>
      <a href="javascript:saveClicked();" class="css_button link_submit"><span><?php echo xlt('Save'); ?></span></a>
      <?php if ($viewmode || !isset($_GET["autoloaded"]) || $_GET["autoloaded"] != "1") { ?>
    </div>

    <div style = 'float:left; margin-top:-3px'>
  <?php if ($GLOBALS['concurrent_layout']) { ?>
      <a href="<?php echo "$rootdir/patient_file/encounter/encounter_top.php"; ?>"
        class="css_button link_submit" onClick="top.restoreSession()"><span><?php echo xlt('Cancel'); ?></span></a>
  <?php } else { ?>
      <a href="<?php echo "$rootdir/patient_file/encounter/patient_encounter.php"; ?>"
        class="css_button link_submit" target='Main' onClick="top.restoreSession()">
      <span><?php echo xlt('Cancel'); ?>]</span></a>
  <?php } // end not concurrent layout ?>
  <?php } else if ($GLOBALS['concurrent_layout']) { // not $viewmode ?>
      <a href="" class="css_button link_submit" onClick="return cancelClicked()">
      <span><?php echo xlt('Cancel'); ?></span></a>
  <?php } // end not $viewmode ?>
    </div>
 </div>

<br> <br>

<table width='96%'>
 <tr>
  <td width='33%' nowrap class='bold'><?php echo xlt('Consultation Brief Description'); ?>:</td>
  <td width='34%' rowspan='2' align='center' valign='center' class='text'>
  
   <table>
    <tr> 
       <?php $enc_form=$_REQUEST['id'];
        if($enc_form!=''){
            $pid_sql=sqlStatement("select pid from forms where form_id=$enc_form AND form_name='New Patient Encounter' AND formdir='newpatient'");
            $pid_row = sqlFetchArray($pid_sql);
            $dos = sqlStatement("SELECT fe.encounter, DATE_FORMAT(fe.date, '%Y-%m-%d') as date,openemr_postcalendar_categories.pc_catname FROM form_encounter AS fe ".
              " left join openemr_postcalendar_categories on fe.pc_catid=openemr_postcalendar_categories.pc_catid  WHERE fe.pid = ? order by fe.date desc", array($pid_row['pid']));
        
?>
    <td width='33%' nowrap class='bold'><?php echo xlt('Template From'); ?>:</td> <td>        
    <form id="dos" name="dos" method="post" action="<?php echo $rootdir ?>/forms/newpatient/copy_template.php">
     <select id="dos" name = "dos" onchange="javascript:showforms();">
          <!--<select id="dos" name = "dos" >-->
        <option value =" ">Select</option>
        <?php 
        while ($dos2 = sqlFetchArray($dos)) { 
              echo "<option value =".$dos2['encounter']."_".$dos2['date'].">".$dos2['date']."-".$dos2['pc_catname'] ."</option>";
          }
        ?>
    </select>
          <!--<td> <input type="submit" name="go" value="go"></td>-->
        <td></td>
    </form>
        </td></tr>   <div id="template1"></div> <?php } ?>
  <tr<?php if ($GLOBALS['athletic_team']) echo " style='visibility:hidden;'"; ?>>
     <td class='bold' nowrap><?php echo xlt('Visit Category:'); ?></td>
     <td class='text'>
      <select name='pc_catid' id='pc_catid'>
	<option value='_blank'>-- <?php echo xlt('Select One'); ?> --</option>
<?php
 $cres = sqlStatement("SELECT pc_catid, pc_catname " .
  "FROM openemr_postcalendar_categories ORDER BY pc_catname");
 while ($crow = sqlFetchArray($cres)) {
  $catid = $crow['pc_catid'];
  if ($catid < 9 && $catid != 5) continue;
  echo "       <option value='" . attr($catid) . "'";
  if ($viewmode && $crow['pc_catid'] == $result['pc_catid']) echo " selected";
  echo ">" . text(xl_appt_category($crow['pc_catname'])) . "</option>\n";
 }
?>
      </select>
     </td>
    </tr>

    <tr>
     <td class='bold' nowrap><?php echo xlt('Facility:'); ?></td>
     <td class='text'>
      <select name='facility_id' onChange="bill_loc()">
<?php

if ($viewmode) {
  $def_facility = $result['facility_id'];
} else {
  $dres = sqlStatement("select facility_id from users where username = ?", array($_SESSION['authUser']));
  $drow = sqlFetchArray($dres);
  $def_facility = $drow['facility_id'];
}
$fres = sqlStatement("select * from facility where service_location != 0 order by name");
if ($fres) {
  $fresult = array();
  for ($iter = 0; $frow = sqlFetchArray($fres); $iter++)
    $fresult[$iter] = $frow;
  foreach($fresult as $iter) {
?>
       <option value="<?php echo attr($iter['id']); ?>" <?php if ($def_facility == $iter['id']) echo "selected";?>><?php echo text($iter['name']); ?></option>
<?php
  }
 }
?>
      </select>
     </td>
    </tr>
	<tr>
		<td class='bold' nowrap><?php echo xlt('Billing Facility'); ?>:</td>
		<td class='text'>
			<div id="ajaxdiv">
			<?php
			billing_facility('billing_facility',$result['billing_facility']);
			?>
			</div>
		</td>
     </tr>
    <tr>
<?php
 $sensitivities = acl_get_sensitivities();
 if ($sensitivities && count($sensitivities)) {
  usort($sensitivities, "sensitivity_compare");
?>
     <td class='bold' nowrap><?php echo xlt('Sensitivity:'); ?></td>
     <td class='text'>
      <select name='form_sensitivity'>
<?php
  foreach ($sensitivities as $value) {
   // Omit sensitivities to which this user does not have access.
   if (acl_check('sensitivities', $value[1])) {
    echo "       <option value='" . attr($value[1]) . "'";
    if ($viewmode && $result['sensitivity'] == $value[1]) echo " selected";
    echo ">" . xlt($value[3]) . "</option>\n";
   }
  }
  echo "       <option value=''";
  if ($viewmode && !$result['sensitivity']) echo " selected";
  echo ">" . xlt('None'). "</option>\n";
?>
      </select>
     </td>
<?php
 } else {
?>
     <td colspan='2'><!-- sensitivities not used --></td>
<?php
 }
?>
    </tr>

    <tr<?php if (!$GLOBALS['gbl_visit_referral_source']) echo " style='visibility:hidden;'"; ?>>
     <td class='bold' nowrap><?php echo xlt('Referral Source'); ?>:</td>
     <td class='text'>
<?php
  echo generate_select_list('form_referral_source', 'refsource', $viewmode ? $result['referral_source'] : '', '');
?>
     </td>
    </tr>

    <tr>
     <td class='bold' nowrap><?php echo xlt('Date of Service:'); ?></td>
     <td class='text' nowrap>
      <input type='text' size='10' name='form_date' id='form_date' <?php echo $disabled ?>
       value='<?php echo $viewmode ? substr($result['date'], 0, 10) : date('Y-m-d'); ?>'
       title='<?php echo xla('yyyy-mm-dd Date of service'); ?>'
       onkeyup='datekeyup(this,mypcc)' onblur='dateblur(this,mypcc)' />
        <img src='../../pic/show_calendar.gif' align='absbottom' width='24' height='22'
        id='img_form_date' border='0' alt='[?]' style='cursor:pointer;cursor:hand'
        title='<?php echo xla('Click here to choose a date'); ?>'>
     </td>
    </tr>

    <tr<?php if ($GLOBALS['ippf_specific'] || $GLOBALS['athletic_team']) echo " style='visibility:hidden;'"; ?>>
     <td class='bold' nowrap><?php echo xlt('Onset/hosp. date:'); ?></td>
     <td class='text' nowrap><!-- default is blank so that while generating claim the date is blank. -->
      <input type='text' size='10' name='form_onset_date' id='form_onset_date'
       value='<?php echo $viewmode && $result['onset_date']!='0000-00-00 00:00:00' ? substr($result['onset_date'], 0, 10) : ''; ?>' 
       title='<?php echo xla('yyyy-mm-dd Date of onset or hospitalization'); ?>'
       onkeyup='datekeyup(this,mypcc)' onblur='dateblur(this,mypcc)' />
        <img src='../../pic/show_calendar.gif' align='absbottom' width='24' height='22'
        id='img_form_onset_date' border='0' alt='[?]' style='cursor:pointer;cursor:hand'
        title='<?php echo xla('Click here to choose a date'); ?>'>
     </td>
    </tr>

    <tr>
     <td class='text' colspan='2' style='padding-top:1em'>
<?php if ($GLOBALS['athletic_team']) { ?>
      <p><i>Click [Add Issue] to add a new issue if:<br />
      New injury likely to miss &gt; 1 day<br />
      New significant illness/medical<br />
      New allergy - only if nil exist</i></p>
<?php } ?>
     </td>
    </tr>
    
   </table>

  </td>

  <td class='bold' width='33%' nowrap>
    <div style='float:left'>
   <?php echo xlt('Issues (Injuries/Medical/Allergy)'); ?>
    </div>
    <div style='float:left;margin-left:8px;margin-top:-3px'>
<?php if ($GLOBALS['athletic_team']) { // they want the old-style popup window ?>
      <a href="#" class="css_button_small link_submit"
       onclick="return newissue()"><span><?php echo xlt('Add'); ?></span></a>
<?php } else { ?>
      <a href="../../patient_file/summary/add_edit_issue.php" class="css_button_small link_submit iframe"
       onclick="top.restoreSession()"><span><?php echo xlt('Add'); ?></span></a>
<?php } ?>
    </div>
  </td>
 </tr>

 <tr>
  <td class='text' valign='top'>
   <textarea name='reason' cols='40' rows='12' wrap='virtual' style='width:96%'
    ><?php echo $viewmode ? text($result['reason']) : text($GLOBALS['default_chief_complaint']); ?></textarea>
  </td>
  <td class='text' valign='top'>
   <select multiple name='issues[]' size='8' style='width:100%'
    title='<?php echo xla('Hold down [Ctrl] for multiple selections or to unselect'); ?>'>
<?php
while ($irow = sqlFetchArray($ires)) {
  $list_id = $irow['id'];
  $tcode = $irow['type'];
  if ($ISSUE_TYPES[$tcode]) $tcode = $ISSUE_TYPES[$tcode][2];
  echo "    <option value='" . attr($list_id) . "'";
  if ($viewmode) {
    $perow = sqlQuery("SELECT count(*) AS count FROM issue_encounter WHERE " .
      "pid = ? AND encounter = ? AND list_id = ?", array($pid,$encounter,$list_id));
    if ($perow['count']) echo " selected";
  }
  else {
    // For new encounters the invoker may pass an issue ID.
    if (!empty($_REQUEST['issue']) && $_REQUEST['issue'] == $list_id) echo " selected";
  }
  echo ">" . text($tcode) . ": " . text($irow['begdate']) . " " .
    text(substr($irow['title'], 0, 40)) . "</option>\n";
}
?>
   </select>

   <p><i><?php echo xlt('To link this encounter/consult to an existing issue, click the '
   . 'desired issue above to highlight it and then click [Save]. '
   . 'Hold down [Ctrl] button to select multiple issues.'); ?></i></p>

  </td>
 </tr>

</table>
<table>
    <tr>
    <?php          
        $trow = $id ? formDataid($id) : '';
        $fres = sqlStatement("SELECT * FROM layout_options " .
          "WHERE form_id = 'ESIGN' AND uor > 0 " .
          "ORDER BY group_name, seq");
        $last_group = '';
        $cell_count = 0;
        $item_count = 0;
        $display_style = 'block';

        while ($frow = sqlFetchArray($fres)) {
          $this_group = $frow['group_name'];
          $titlecols  = $frow['titlecols'];
          $datacols   = $frow['datacols'];
          $data_type  = $frow['data_type'];
          $field_id   = $frow['field_id'];
          $list_id    = $frow['list_id'];


         $currvalue  = '';
          if (isset($trow[$field_id])) $currvalue = $trow[$field_id];
          // Handle a data category (group) change.

        // Handle a data category (group) change.
          if (strcmp($this_group, $last_group) != 0) {
            end_group();
           $group_seq  = substr($this_group, 0, 1);
           $group_name = substr($this_group, 1);
           $last_group = $this_group;
           $group_seq_esc = htmlspecialchars( $group_seq, ENT_QUOTES);
                if($group_seq==1):	//echo "<div class='tab current' id='div_$group_seq_esc'>";
                else:			echo "<div class='tab' id='div_$group_seq_esc'>";
                endif;
            echo " <table border='0' cellpadding='0'>\n";
            $display_style = 'none';
          }
          // Handle starting of a new row.
          if (($titlecols > 0 && $cell_count >= $CPR) || $cell_count == 0) {
            end_row();
            echo " <tr>";
          }

          if ($item_count == 0 && $titlecols == 0) $titlecols = 1;

          // Handle starting of a new label cell.
          if ($titlecols > 0) {
            end_cell();
            $titlecols_esc = htmlspecialchars( $titlecols, ENT_QUOTES);
            echo "<td width='70' valign='top' colspan='$titlecols_esc'";
            echo ($frow['uor'] == 2) ? " class='required'" : " class='bold'";
            if ($cell_count == 2) echo " style='padding-left:10pt'";
            echo ">";
            $cell_count += $titlecols;
          }
          ++$item_count;

          echo "<b>";

          // Modified 6-09 by BM - Translate if applicable
          if ($frow['title']) echo (htmlspecialchars( xl_layout_label($frow['title']), ENT_NOQUOTES) . ":"); else echo "&nbsp;";

          echo "</b>";

          // Handle starting of a new data cell.
          if ($datacols > 0) {
            end_cell();
            $datacols_esc = htmlspecialchars( $datacols, ENT_QUOTES);
            echo "<td valign='top' colspan='$datacols_esc' class='text'";
            if ($cell_count > 0) echo " style='padding-left:5pt'";
            echo ">";
            $cell_count += $datacols;
          }

          ++$item_count;
          generate_form_field($frow, $currvalue);
          echo "</div>";
        }

        end_group();
        ?>
        
    </tr> 
</table>
<?php
// Check for audit code
$auditQ = sqlStatement("SELECT audit_data FROM tbl_form_audit fa, forms f 
                        WHERE fa.id = f.form_id AND f.encounter = ".$encounter." AND f.formdir = 'auditform'");
while($auditRow = sqlFetchArray($auditQ)):
    $auditR = $auditRow['audit_data'];
endwhile;

$unserAuditR = unserialize($auditR);
$auditStr =  explode(':',$unserAuditR['hiddenaudit']);
$auditN = $auditStr[1];
// Check for fee sheet code
$feeQ = sqlStatement("SELECT code from billing
                     WHERE encounter = ".$encounter." AND code_type = 'CPT4' AND activity = 1 order by id ASC LIMIT 1");
while($feeRow = sqlFetchArray($feeQ)):
    $feeR = $feeRow['code'];
endwhile;

//echo "Audit code: ". $auditN . " --- " . "Fee Sheet code: ". $feeR."<br />";
$providercheck = sqlStatement("SELECT processproviders  FROM tbl_user_custom_attr_1to1 WHERE userid= ".$_SESSION['authUserID']);
while($providercheck2 = sqlFetchArray($providercheck)){
    $authcheck = $providercheck2['processproviders'];
}
$get_audit_dropdown = sqlStatement("SELECT audited_status FROM form_encounter WHERE encounter = $encounter");
while($set_audit_dropdown = sqlFetchArray($get_audit_dropdown)){
    $audit_dropdown = $set_audit_dropdown['audited_status'];
}
if($authcheck == 'YES' && ($audit_dropdown == 'Completed')):
    ?>
    <script>
        $("#form_elec_signedby").removeAttr('disabled');
        $("#form_elec_signed_on").removeAttr('disabled');
    </script>
    <?php
else:    
    ?>
    <script>
        $("#form_elec_signedby").attr('disabled','disabled');
        $("#form_elec_signed_on").attr('disabled','disabled');
    </script>
    <?php
endif;

if($auditN != "" || $feeR != ""):
    if(trim($auditN) == $feeR):
        //echo "Audit Passed";
    ?>
    <script>
        $("#form_audited_status").removeAttr('disabled');
    </script>
    <?php
    else:
        //echo "Audit Failed";
    ?>
    <script>
        $("#form_audited_status").attr('disabled','disabled');
        $('#form_audited_status option[value="Incomplete"]').attr("selected", "selected");
    </script>
    <?php
    endif;
endif;

?>
<a href="javascript:;" class="link" onclick="window.open( 'http://<?php echo $_SERVER[HTTP_HOST]; ?>/interface/forms/newpatient/details_page.php?id=<?php echo $id; ?>', '', 'width=500, height=600')"> Details </a>
</form>

</body>

<script language="javascript">
/* required for popup calendar */
Calendar.setup({inputField:"form_date", ifFormat:"%Y-%m-%d", button:"img_form_date"});
Calendar.setup({inputField:"form_onset_date", ifFormat:"%Y-%m-%d", button:"img_form_onset_date"});
<?php
if (!$viewmode) {
  $erow = sqlQuery("SELECT count(*) AS count " .
    "FROM form_encounter AS fe, forms AS f WHERE " .
    "fe.pid = ? AND fe.date = ? AND " .
    "f.formdir = 'newpatient' AND f.form_id = fe.id AND f.deleted = 0", array($pid,date('Y-m-d 00:00:00')));
  if ($erow['count'] > 0) {
    echo "alert('" . xls('Warning: A visit was already created for this patient today!') . "');\n";
  }
}
?>
</script>
<?php include $GLOBALS['fileroot']."/library/options_listadd.inc"; ?>
</body>
<script language="JavaScript">
<?php echo $date_init; ?>
</script>
</html>
<?php
$CPR = 4; // cells per row

function end_cell() {
  global $item_count, $cell_count;
  if ($item_count > 0) {
    echo "</td>";
    $item_count = 0;
  }
}

function end_row() {
  global $cell_count, $CPR;
  end_cell();
  if ($cell_count > 0) {
    for (; $cell_count < $CPR; ++$cell_count) echo "<td></td>";
    echo "</tr>\n";
    $cell_count = 0;
  }
}

function end_group() {
  global $last_group;
  if (strlen($last_group) > 0) {
    end_row();
    echo " </table>\n";
    echo "</div>\n";
  }
}


function formDataid ($id, $cols = "*")
{
  return sqlQuery("select $cols from form_encounter where id=? " .
    "order by id DESC limit 0,1", array($id) );
}
