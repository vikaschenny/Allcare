<?php
/**
 *
 * Patient summary screen.
 *
 * LICENSE: This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 3
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

//SANITIZE ALL ESCAPES
$sanitize_all_escapes=true;
//

//STOP FAKE REGISTER GLOBALS
$fake_register_globals=false;
//

 require_once("../../globals.php");
 require_once("$srcdir/patient.inc");
 require_once("$srcdir/acl.inc");
 require_once("$srcdir/classes/Address.class.php");
 require_once("$srcdir/classes/InsuranceCompany.class.php");
 require_once("$srcdir/classes/Document.class.php");
 require_once("$srcdir/options.inc.php");
 require_once("../history/history.inc.php");
 require_once("$srcdir/formatting.inc.php");
 require_once("$srcdir/edi.inc");
 require_once("$srcdir/invoice_summary.inc.php");
 require_once("$srcdir/clinical_rules.php");
  if ($GLOBALS['concurrent_layout'] && isset($_REQUEST['set_pid'])) {
  include_once("$srcdir/pid.inc");
  setpid($_REQUEST['set_pid']);
 }
 $pid=$_REQUEST['set_pid'];
  $active_reminders = false;
  if ((!isset($_SESSION['alert_notify_pid']) || ($_SESSION['alert_notify_pid'] != $pid)) && isset($_GET['set_pid']) && acl_check('patients', 'med') && $GLOBALS['enable_cdr'] && $GLOBALS['enable_cdr_crp']) {
    // showing a new patient, so check for active reminders
    $active_reminders = active_alert_summary($pid,"reminders-due");
  }


// Get patient/employer/insurance information.
//
$result  = getPatientData($pid, "*, DATE_FORMAT(DOB,'%Y-%m-%d') as DOB_YMD");
$result2 = getEmployerData($pid);
$result3 = getInsuranceData($pid, "primary", "copay, provider, DATE_FORMAT(`date`,'%Y-%m-%d') as effdate");
$insco_name = "";
if ($result3['provider']) {   // Use provider in case there is an ins record w/ unassigned insco
  $insco_name = getInsuranceProvider($result3['provider']);
}
?>
<html>

<head>
<?php html_header_show();?>
<link rel="stylesheet" href="<?php echo $css_header;?>" type="text/css">
<link rel="stylesheet" type="text/css" href="../../../library/js/fancybox/jquery.fancybox-1.2.6.css" media="screen" />
<style type="text/css">@import url(../../../library/dynarch_calendar.css);</style>
<script type="text/javascript" src="../../../library/textformat.js"></script>
<script type="text/javascript" src="../../../library/dynarch_calendar.js"></script>
<?php include_once("{$GLOBALS['srcdir']}/dynarch_calendar_en.inc.php"); ?>
<script type="text/javascript" src="../../../library/dynarch_calendar_setup.js"></script>
<script type="text/javascript" src="../../../library/dialog.js"></script>
<script type="text/javascript" src="../../../library/js/jquery-1.6.4.min.js"></script>
<script type="text/javascript" src="../../../library/js/common.js"></script>
<script type="text/javascript" src="../../../library/js/fancybox/jquery.fancybox-1.2.6.js"></script>
 

<script type="text/javascript" language="JavaScript">

 var mypcc = '<?php echo htmlspecialchars($GLOBALS['phone_country_code'],ENT_QUOTES); ?>';

 function oldEvt(eventid) {
  dlgopen('../../main/calendar/add_edit_event.php?eid=' + eventid, '_blank', 550, 350);
 }

 function advdirconfigure() {
   dlgopen('advancedirectives.php', '_blank', 500, 450);
  }

 function refreshme() {
  top.restoreSession();
  location.reload();
 }

 // Process click on Delete link.
 function deleteme() {
  dlgopen('../deleter.php?patient=<?php echo htmlspecialchars($pid,ENT_QUOTES); ?>', '_blank', 500, 450);
  return false;
 }

 // Called by the deleteme.php window on a successful delete.
 function imdeleted() {
<?php if ($GLOBALS['concurrent_layout']) { ?>
  parent.left_nav.clearPatient();
<?php } else { ?>
  top.restoreSession();
  top.location.href = '../main/main_screen.php';
<?php } ?>
 }

 function validate() {
  var f = document.forms[0];
<?php
if ($GLOBALS['athletic_team']) {
  echo "  if (f.form_userdate1.value != f.form_original_userdate1.value) {\n";
  $irow = sqlQuery("SELECT id, title FROM lists WHERE " .
    "pid = ? AND enddate IS NULL ORDER BY begdate DESC LIMIT 1", array($pid));
  if (!empty($irow)) {
?>
   if (confirm('Do you wish to also set this new return date in the issue titled "<?php echo htmlspecialchars($irow['title'],ENT_QUOTES); ?>"?')) {
    f.form_issue_id.value = '<?php echo htmlspecialchars($irow['id'],ENT_QUOTES); ?>';
   } else {
    alert('OK, you will need to manually update the return date in any affected issue(s).');
   }
<?php } else { ?>
   alert('You have changed the return date but there are no open issues. You probably need to create or modify one.');
<?php
  } // end empty $irow
  echo "  }\n";
} // end athletic team
?>
  return true;
 }

 function newEvt() {
  dlgopen('../../main/calendar/add_edit_event.php?patientid=<?php echo htmlspecialchars($pid,ENT_QUOTES); ?>', '_blank', 550, 350);
  return false;
 }

function sendimage(pid, what) {
 // alert('Not yet implemented.'); return false;
 dlgopen('../upload_dialog.php?patientid=' + pid + '&file=' + what,
  '_blank', 500, 400);
 return false;
}

</script>

<script type="text/javascript">

function toggleIndicator(target,div) {

    $mode = $(target).find(".indicator").text();
    if ( $mode == "<?php echo htmlspecialchars(xl('collapse'),ENT_QUOTES); ?>" ) {
        $(target).find(".indicator").text( "<?php echo htmlspecialchars(xl('expand'),ENT_QUOTES); ?>" );
        $("#"+div).hide();
	$.post( "../../../library/ajax/user_settings.php", { target: div, mode: 0 });
    } else {
        $(target).find(".indicator").text( "<?php echo htmlspecialchars(xl('collapse'),ENT_QUOTES); ?>" );
        $("#"+div).show();
	$.post( "../../../library/ajax/user_settings.php", { target: div, mode: 1 });
    }
}


//  Based on POS selection POS fields and recordsets will be displayed
function showMapping(POSid)
{
    //alert(POSid);
    if(POSid!=0)
    {
        var AjxURL=document.URL.split("/new.php")[0]+'/show_mapping.php?POSid='+POSid; 
        //alert(document.URL);

        $.ajax({
                     type: 'POST',
                     url: AjxURL,

                     success: function(response)
                     { 
                        //alert('success'+response);
                         $("#divResponse").html(response);
                        //      return true;
                     },
                     error :function(response,status)
                     {
                      alert('error');
                      //return false;
                     } 
                    });
    }
}
$(document).ready(function(){
  var msg_updation='';
	<?php
	if($GLOBALS['erx_enable']){
		//$soap_status=sqlQuery("select soap_import_status from patient_data where pid=?",array($pid));
		$soap_status=sqlStatement("select soap_import_status,pid from patient_data where pid=? and soap_import_status in ('1','3')",array($pid));
		while($row_soapstatus=sqlFetchArray($soap_status)){
			//if($soap_status['soap_import_status']=='1' || $soap_status['soap_import_status']=='3'){ ?>
			top.restoreSession();
			$.ajax({
				type: "POST",
				url: "../../soap_functions/soap_patientfullmedication.php",
				dataType: "html",
				data: {
					patient:<?php echo $row_soapstatus['pid']; ?>,
				},
				async: false,
				success: function(thedata){
					//alert(thedata);
					msg_updation+=thedata;
				},
				error:function(){
					alert('ajax error');
				}	
			});
			<?php
			//}	
			//elseif($soap_status['soap_import_status']=='3'){ ?>
			top.restoreSession();
			$.ajax({
				type: "POST",
				url: "../../soap_functions/soap_allergy.php",
				dataType: "html",
				data: {
					patient:<?php echo $row_soapstatus['pid']; ?>,
				},
				async: false,
				success: function(thedata){
					//alert(thedata);
					msg_updation+=thedata;
				},
				error:function(){
					alert('ajax error');
				}	
			});
			<?php
			if($GLOBALS['erx_import_status_message']){ ?>
			if(msg_updation)
			  alert(msg_updation);
			<?php
			}
			//} 
		}
	}
	?>
    // load divs
    $("#stats_div").load("stats.php", { 'embeddedScreen' : true }, function() {
	// (note need to place javascript code here also to get the dynamic link to work)
        $(".rx_modal").fancybox( {
                'overlayOpacity' : 0.0,
                'showCloseButton' : true,
                'frameHeight' : 500,
                'frameWidth' : 800,
        	'centerOnScroll' : false,
        	'callbackOnClose' : function()  {
                refreshme();
        	}
        });
    });
    $("#pnotes_ps_expand").load("pnotes_fragment.php");
    $("#disclosures_ps_expand").load("disc_fragment.php");

    <?php if ($GLOBALS['enable_cdr'] && $GLOBALS['enable_cdr_crw']) { ?>
      top.restoreSession();
      $("#clinical_reminders_ps_expand").load("clinical_reminders_fragment.php", { 'embeddedScreen' : true }, function() {
          // (note need to place javascript code here also to get the dynamic link to work)
          $(".medium_modal").fancybox( {
                  'overlayOpacity' : 0.0,
                  'showCloseButton' : true,
                  'frameHeight' : 500,
                  'frameWidth' : 800,
                  'centerOnScroll' : false,
                  'callbackOnClose' : function()  {
                  refreshme();
                  }
          });
      });
    <?php } // end crw?>

    <?php if ($GLOBALS['enable_cdr'] && $GLOBALS['enable_cdr_prw']) { ?>
      top.restoreSession();
      $("#patient_reminders_ps_expand").load("patient_reminders_fragment.php");
    <?php } // end prw?>

<?php if ($vitals_is_registered && acl_check('patients', 'med')) { ?>
    // Initialize the Vitals form if it is registered and user is authorized.
    $("#vitals_ps_expand").load("vitals_fragment.php");
<?php } ?>

<?php
  // Initialize for each applicable LBF form.
  $gfres = sqlStatement("SELECT option_id FROM list_options WHERE " .
    "list_id = 'lbfnames' AND option_value > 0 ORDER BY seq, title");
  while($gfrow = sqlFetchArray($gfres)) {
?>
    $("#<?php echo $gfrow['option_id']; ?>_ps_expand").load("lbf_fragment.php?formname=<?php echo $gfrow['option_id']; ?>");
<?php
  }
?>

    // fancy box
    enable_modals();

    tabbify();

// modal for dialog boxes
  $(".large_modal").fancybox( {
    'overlayOpacity' : 0.0,
    'showCloseButton' : true,
    'frameHeight' : 600,
    'frameWidth' : 1000,
    'centerOnScroll' : false
  });

// modal for image viewer
  $(".image_modal").fancybox( {
    'overlayOpacity' : 0.0,
    'showCloseButton' : true,
    'centerOnScroll' : false,
    'autoscale' : true
  });
  
  $(".iframe1").fancybox( {
  'left':10,
	'overlayOpacity' : 0.0,
	'showCloseButton' : true,
	'frameHeight' : 300,
	'frameWidth' : 350
  });
// special size for patient portal
  $(".small_modal").fancybox( {
	'overlayOpacity' : 0.0,
	'showCloseButton' : true,
	'frameHeight' : 200,
	'frameWidth' : 380,
            'centerOnScroll' : false
  });

  <?php if ($active_reminders) { ?>
    // show the active reminder modal
    $("#reminder_popup_link").fancybox({
      'overlayOpacity' : 0.0,
      'showCloseButton' : true,
      'frameHeight' : 500,
      'frameWidth' : 500,
      'centerOnScroll' : false
    }).trigger('click');
  <?php } ?>

});

// JavaScript stuff to do when a new patient is set.
//
function setMyPatient() {
<?php if ($GLOBALS['concurrent_layout']) { ?>
 // Avoid race conditions with loading of the left_nav or Title frame.
 if (!parent.allFramesLoaded()) {
  setTimeout("setMyPatient()", 500);
  return;
 }
<?php if (isset($_REQUEST['set_pid'])) { ?>
 parent.left_nav.setPatient(<?php echo "'" . htmlspecialchars(($result['fname']) . " " . ($result['lname']),ENT_QUOTES) .
   "'," . htmlspecialchars($pid,ENT_QUOTES) . ",'" . htmlspecialchars(($result['pubpid']),ENT_QUOTES) .
   "','', ' " . htmlspecialchars(xl('DOB') . ": " . oeFormatShortDate($result['DOB_YMD']) . " " . xl('Age') . ": " . getPatientAgeDisplay($result['DOB_YMD']), ENT_QUOTES) . "'"; ?>);
 var EncounterDateArray = new Array;
 var CalendarCategoryArray = new Array;
 var EncounterIdArray = new Array;
 var Count = 0;
<?php
  //Encounter details are stored to javacript as array.
  $result4 = sqlStatement("SELECT fe.encounter,fe.date,openemr_postcalendar_categories.pc_catname FROM form_encounter AS fe ".
    " left join openemr_postcalendar_categories on fe.pc_catid=openemr_postcalendar_categories.pc_catid  WHERE fe.pid = ? order by fe.date desc", array($pid));
  if(sqlNumRows($result4)>0) {
    while($rowresult4 = sqlFetchArray($result4)) {
?>
 EncounterIdArray[Count] = '<?php echo htmlspecialchars($rowresult4['encounter'], ENT_QUOTES); ?>';
 EncounterDateArray[Count] = '<?php echo htmlspecialchars(oeFormatShortDate(date("Y-m-d", strtotime($rowresult4['date']))), ENT_QUOTES); ?>';
 CalendarCategoryArray[Count] = '<?php echo htmlspecialchars(xl_appt_category($rowresult4['pc_catname']), ENT_QUOTES); ?>';
 Count++;
<?php
    }
  }
?>
 parent.left_nav.setPatientEncounter(EncounterIdArray,EncounterDateArray,CalendarCategoryArray);
<?php } // end setting new pid ?>
 parent.left_nav.setRadio(window.name, 'dem');
 parent.left_nav.syncRadios();
<?php } // end concurrent layout ?>
}

$(window).load(function() {
 setMyPatient();
});

</script>

<style type="css/text">
#pnotes_ps_expand {
  height:auto;
  width:100%;
}
</style>

</head>

<body class="body_top">

<?php
// Demographics expand collapse widget
$widgetTitle = xl("Demographics");
$widgetLabel = "demographics";
$widgetButtonLabel = xl("Edit");
$widgetButtonLink = "../interface/patient_file/summary/demographics_full.php?set_pid=$pid";
$widgetButtonClass = "";
$linkMethod = "html";
$bodyClass = "";
$widgetAuth = acl_check('patients', 'demo', '', 'write');
$fixedWidth = true;
expand_collapse_widget($widgetTitle, $widgetLabel, $widgetButtonLabel,
  $widgetButtonLink, $widgetButtonClass, $linkMethod, $bodyClass,
  $widgetAuth, $fixedWidth);
?>
         <div id="DEM" >
          <ul class="tabNav">
           <?php display_layout_tabs('DEM', $result, $result2); ?>
          </ul>
          <div class="tabContainer" style="height:200px; overflow-y:scroll; background-color:#FFFFFF;">
           <?php display_layout_tabs_data('DEM', $result, $result2); ?>
          </div>
         </div>
        </div> <!-- required for expand_collapse_widget -->
   
    
 <?php if (false && $GLOBALS['athletic_team']) { ?>
<script language='JavaScript'>
 Calendar.setup({inputField:"form_userdate1", ifFormat:"%Y-%m-%d", button:"img_userdate1"});
</script>
<?php } ?>   
</body>
</html>
    