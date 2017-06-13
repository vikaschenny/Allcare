<?php 
/** **************************************************************************
 *	LABORATORY/COMMON.PHP
 *
 *	Copyright (c)2014 - Medical Technology Services (MDTechSvcs.com)
 *
 *	This program is licensed software: licensee is granted a limited nonexclusive
 *  license to install this Software on more than one computer system, as long as all
 *  systems are used to support a single licensee. Licensor is and remains the owner
 *  of all titles, rights, and interests in program.
 *  
 *  Licensee will not make copies of this Software or allow copies of this Software 
 *  to be made by others, unless authorized by the licensor. Licensee may make copies 
 *  of the Software for backup purposes only.
 *
 *	This program is distributed in the hope that it will be useful, but WITHOUT 
 *	ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS 
 *  FOR A PARTICULAR PURPOSE. LICENSOR IS NOT LIABLE TO LICENSEE FOR ANY DAMAGES, 
 *  INCLUDING COMPENSATORY, SPECIAL, INCIDENTAL, EXEMPLARY, PUNITIVE, OR CONSEQUENTIAL 
 *  DAMAGES, CONNECTED WITH OR RESULTING FROM THIS LICENSE AGREEMENT OR LICENSEE'S 
 *  USE OF THIS SOFTWARE.
 *
 *  @package mdts
 *  @subpackage laboratory
 *  @version 2.0
 *  @copyright Medical Technology Services
 *  @author Ron Criswell <ron.criswell@MDTechSvcs.com>
 * 
 *************************************************************************** */
require_once("../../globals.php");
require_once("{$GLOBALS['srcdir']}/options.inc.php");
require_once("{$GLOBALS['srcdir']}/lists.inc");
require_once("{$GLOBALS['srcdir']}/wmt/wmt.class.php");
require_once("{$GLOBALS['srcdir']}/wmt/wmt.include.php");

// grab inportant stuff
$id = '';
$generated = false;
$print = $_REQUEST['print'];
if ($viewmode) $id = $_REQUEST['id'];
$popup = ($popup)? $popup : $_REQUEST['pop'];

$client_id = false;
$params = sqlQuery("SELECT setting_value FROM user_settings WHERE setting_label = ?",array("wmt::client_id"));
if ($params['setting_value']) $client_id = $params['setting_value'];

$form_name = 'laboratory';
$form_title = 'Laboratory Order';
$form_table = 'form_laboratory';
$order_table = 'procedure_order';
$item_table = 'procedure_order_code';
$aoe_table = 'procedure_answers';

$save_url = $rootdir.'/forms/'.$form_name.'/save.php';
$validate_url = $rootdir.'/forms/'.$form_name.'/validate.php';
$submit_url = $rootdir.'/forms/'.$form_name.'/submit.php';
$abort_url = $rootdir.'/patient_file/summary/demographics.php';
$reload_url = $rootdir.'/patient_file/encounter/view_form.php?formname='.$form_name.'&id=';
$cancel_url = $rootdir.'/patient_file/encounter/encounter_top.php';
$document_url = $GLOBALS['web_root'].'/controller.php?document&retrieve&patient_id='.$pid.'&document_id=';

// date fix
function goodDate($date) {
	if ($date == '') $date = FALSE;
	if ($date == 0) $date = FALSE;
	if (strtotime($date) === FALSE) $date = FALSE;
	if (strtotime($date) == 0) $date = FALSE;
	if (!strtotime($date)) $date = FALSE;
	if ($date == '000-00-00 00:00:00') $date = FALSE;
	return $date;
}

/* RETRIEVE FORM DATA */
try {
	$order_date = date('Y-m-d');
	$order_data = new wmtOrder($form_name, $id);
	if ($order_data->id && goodDate($order_data->order_datetime)) $order_date = date('Y-m-d',strtotime($order_data->order_datetime));
	if ($order_data->user == 'system') $generated = true;
	if ($order_data->patient_id) $pid = $order_data->patient_id;
	if (! $pid) die ("Missing patient identifier!!");
	if (! $encounter && $order_data->encounter_id) $encounter = $order_data->encounter_id;
	if (! $encounter) die ("Missing current encounter identifier!!");
	
	$pat_data = wmtPatient::getPidPatient($pid);
	$enc_data = wmtEncounter::getEncounter($encounter);
	$ins_list = wmtInsurance::getPidInsDate($pid,$order_date);

	$lab_id = ($order_data->lab_id) ? $order_data->lab_id : $lab_id; // use order if available
	$lab_data = sqlQuery("SELECT * FROM procedure_providers WHERE ppid = ?",array($lab_id));
	$form_title = $lab_data['name'];
	
	// insurance codes (only if required)
	$ins_primary_lab = '';
	$ins_secondary_lab = '';
	if ($lab_data['npi'] == 'BIOREF') {
		$ins_primary_lab = $ins_list[0]->lab_identifier;
		$ins_secondary_lab = $ins_list[1]->lab_identifier;
	}
	
	// fetch order detail records
	$item_list = wmtOrderItem::fetchItemList($order_data->order_number);
}
catch (Exception $e) {
	die ("FATAL ERROR ENCOUNTERED: " . $e->getMessage());
	exit;
}

// get lab site id
$siteid = ListLook($enc_data->facility_id, 'Lab_Site_Identifiers');
if (!$siteid || $siteid == '* Not Found *') $siteid = $lab_data['send_fac_id'];

// set form status
$status = 'i'; // incomplete and pending
if ($order_data->id && $order_data->status) 
	$status = $order_data->status;
if ($report_data->id && $report_data->status)
	$status = $report_data->status;

// VALIDATE INSTALL
$invalid = "";
if (!$GLOBALS["wmt_lab_enable"]) $invalid .= "LabLink Interface Not Enabled\n";
if ($lab_data['protocol'] != 'INT') {
	if (!$siteid) $invalid .= "No Sending Facility Identifier\n";
	if (!$lab_data["recv_fac_id"]) $invalid .= "No Receiving Facility Identifier\n";
	if (!$lab_data["recv_app_id"]) $invalid .= "No Receiving Application Identifier\n";

	// these two are only required for protocols connecting to ext site
	if ($lab_data["protocol"] == "FSC" || $lab_data["protocol"] == "FC2" || $lab_data["protocol"] == "WS" ) {
		if (!$lab_data["login"]) $invalid .= "No Laboratory Username\n";
		if (!$lab_data["password"]) $invalid .= "No Laboratory Password\n";
		if (!$lab_data["remote_host"]) $invalid .= "No Remote Host Data\n";
	}

	if (!$lab_data["orders_path"]) $invalid .= "No Laboratory Order Path\n";
	if (!$lab_data["results_path"]) $invalid .= "No Laboratory Result Path\n";
}

if (!file_exists("{$GLOBALS["srcdir"]}/wmt")) $invalid .= "Missing WMT Library\n";
if (!file_exists("{$GLOBALS["srcdir"]}/wmt/laboratory")) $invalid .= "Missing Laboratory Library\n";
if (!file_exists("{$GLOBALS["srcdir"]}/tcpdf")) $invalid .= "Missing TCPDF Library\n";
if (!extension_loaded("xml")) $invalid .= "XML Module Not Enabled\n";
if (!extension_loaded("openssl")) $invalid .= "OPENSSL Module Not Enabled\n";

if ($invalid) { ?>
<h1>Laboratory Interface Not Available</h1>
The interface is not enabled, not properly configured, or required components are missing!!
<br/><br/>
For assistance with implementing this service contact:
<br/><br/>
<a href="http://www.williamsmedtech.com/support" target="_blank"><b>Williams Medical Technologies Support</b></a>
<br/><br/>
<table style="border:2px solid red;padding:20px"><tr><td style="white-space:pre;color:red"><h3>DEBUG OUTPUT</h3><?php echo $invalid ?></td></tr></table>
<?php
exit; 
}

$dlist = array();

// active encounter diagnoses
$sql = "SELECT 'Active' AS title, CONCAT('ICD10:',formatted_dx_code) AS code, short_desc, long_desc FROM `issue_encounter` ie ";
$sql .= "LEFT JOIN `lists` ls ON ie.`list_id` = ls.`id` AND ie.`pid` = ls.`pid` AND ls.`activity` = '1' ";
$sql .= "LEFT JOIN `icd10_dx_order_code` oc ON oc.`formatted_dx_code` = SUBSTR(ls.`diagnosis` FROM 7) AND oc.`active` = '1' ";
$sql .= "WHERE ie.`pid` = ? AND ie.`encounter` = ? AND ie.`resolved` = 0 AND short_desc IS NOT NULL ";
$sql .= "ORDER BY oc.`short_desc`";
$result = sqlStatementNoLog($sql,array($pid,$encounter));
	
while ($data = sqlFetchArray($result)) {
	// create array ('tab title','icd9 code','short title','long title')
	$dlist[] = $data;
}

// retrieve diagnosis quick list
if ($GLOBALS['wmt_lab_icd10']) {
	$query = "SELECT title, CONCAT('ICD10:',formatted_dx_code) AS code, short_desc, long_desc FROM list_options l ";
	$query .= "JOIN icd10_dx_order_code c ON c.formatted_dx_code = l.option_id AND c.active = 1 ";
	$query .= "WHERE l.list_id LIKE 'Lab\_ICD10%' ";
	$query .= "ORDER BY l.title, l.seq";
	$result = sqlStatement($query);
} else {
	$query = "SELECT title, CONCAT('ICD9:',formatted_dx_code) AS code, short_desc, long_desc FROM list_options l ";
	$query .= "JOIN icd9_dx_code c ON c.formatted_dx_code = l.option_id AND c.active = 1 ";
	$query .= "WHERE l.list_id LIKE 'Lab\_Diagnosis%' ";
	$query .= "ORDER BY l.title, l.seq";
	$result = sqlStatement($query);
}

while ($data = sqlFetchArray($result)) {
	// create array ('tab title','icd code','short title','long title')
	$dlist[] = $data;
}

// retrieve order quick list
$query = "SELECT ord.procedure_code AS code, fav.name AS title, ord.name, ord.name, fav.description FROM procedure_type fav ";
$query .= "LEFT JOIN procedure_type ord ON ord.procedure_type_id = fav.parent ";
$query .= "WHERE fav.procedure_type = 'fav' and ord.lab_id = '".$lab_id."' ";
$query .= "ORDER BY fav.name, fav.seq";
$result = sqlStatement($query);

$olist = array();
while ($data = sqlFetchArray($result)) {
	// create array ('tab title','icd9 code','code label')
	$olist[] = $data;
}

// retrieve AOE list entries
$query = "SELECT list_id, option_id, title, is_default, notes FROM list_options ";
$query .= "WHERE list_id LIKE '".$lab_data['npi']."\_%' ";
$query .= "ORDER BY list_id, seq";
$result = sqlStatement($query);

$aoe_id = '';
$aoe_options = array();
while ($data = sqlFetchArray($result)) {
	if ($data['list_id'] != $aoe_id) {
		$aoe_id = $data['list_id'];
		${$aoe_id} = array();
	}
	${$aoe_id}[$data['option_id']] = $data; // stores option array
	$aoe_options[$aoe_id] = ${$aoe_id}; // creates unique list
}

// for label printing (not always used!!)
function getLabelers($thisField) {
	$rlist= sqlStatement("SELECT * FROM list_options WHERE list_id = 'Quest_Label_Printers' ORDER BY seq, title");

	$active = '';
	$default = '';
	$labelers = array();
	while ($rrow= sqlFetchArray($rlist)) {
		if ($thisField == $rrow['option_id']) $active = $rrow['option_id'];
		if ($rrow['is_default']) $default = $rrow['option_id'];
		$labelers[] = $rrow;
	}

	if (!$active) $active = $default;

	foreach ($labelers AS $rrow) {
		echo "<option value='" . $rrow['option_id'] . "'";
		if ($active == $rrow['option_id']) echo " selected='selected'";
		echo ">" . $rrow['title'];
		echo "</option>\n";
	}
}

?>
<!DOCTYPE HTML>
<html>
	<head>
		<?php html_header_show();?>
		<title><?php echo $form_title; ?></title>

		<link rel="stylesheet" href="<?php echo $css_header;?>" type="text/css">
		<link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['webroot'] ?>/library/js/fancybox-1.3.4/jquery.fancybox-1.3.4.css" media="screen" />
		<link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['webroot'] ?>/library/wmt/wmt.default.css" media="screen" />
		<!-- link rel="stylesheet" type="text/css" href="http://code.jquery.com/ui/1.10.0/themes/base/jquery-ui.css" media="screen" / -->
		
		<script><?php include_once("{$GLOBALS['srcdir']}/restoreSession.php"); ?></script>
		<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/jquery-1.7.2.min.js"></script>
		<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/jquery-ui-1.10.0.custom.min.js"></script>
		<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/common.js"></script>
		<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/fancybox-1.3.4/jquery.fancybox-1.3.4.pack.js"></script>
		<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/dialog.js"></script>
		<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/overlib_mini.js"></script>
		<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/textformat.js"></script>
		<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/wmt/wmtstandard.js"></script>
		
		<!-- pop up calendar -->
		<style type="text/css">@import url(<?php echo $GLOBALS['webroot'] ?>/library/dynarch_calendar.css);</style>
		<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/dynarch_calendar.js"></script>
		<?php include_once("{$GLOBALS['srcdir']}/dynarch_calendar_en.inc.php"); ?>
		<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/dynarch_calendar_setup.js"></script>
	
<style>
.calendar tbody .day { border: 1px solid inherit; }
.calendar { z-index: 2000 }

.wmtMainContainer { min-width: 880px }
.wmtMainContainer table { font-size: 12px;border-collapse:collapse; }
.wmtMainContainer fieldset { margin-top: 0; border:1px solid #aaa }

</style>

		<script>
			var mypcc = '<?php echo $GLOBALS['phone_country_code'] ?>';

			// store all of the AOE list options
<?php
			foreach ($aoe_options AS $key => $list) {
				echo "var ".$key." = '';\n";
				foreach ($list AS $option) {
					echo $key." += '<option value=\"".$option['option_id']."\">".$option['title']."</option>'\n";
				}
			}
?>					

			// validate data and submit form
			function saveClicked() {
				var f = document.forms[0];
				var resp = true;
<?php if ($order_data->status == 'i') { // has not been submitted yet ?>
				resp = confirm("Your order will be saved but will NOT be submitted.\n\nClick 'OK' to save and exit.");
<?php } ?>
				if (resp) {
					restoreSession();
					f.submit();
				}
 			}

			function submitClicked() {
				// minimum validation
				var notice = '';
				if ($('.code').length < 1) notice += "\n- At least one diagnosis code required.";
				if ($('.test').length < 1) notice += "\n- At least one profile / test code required.";
<?php if ($lab_data['npi'] == 'CERNER') { ?>
				if ($('#specimen_source').val() == '' || $('#specimen_source').val() == '_blank') notice += "\n- An order specimen source is required.";
<?php } ?>
				if ($('#provider_id').val() == '' || $('#provider_id').val() == '_blank') notice += "\n- An ordering physician is required.";
				if ($('#request_billing').val() == '') notice += "\n- A billing type must be specified.";
				if ($('#request_account').val() == '') notice += "\n- A billing account must be specified.";
				
				if (notice) {
					notice = "PLEASE CORRECT THE FOLLOWING:\n" + notice;
					alert(notice);
					return;
				}

				$.fancybox.showActivity();
				
				$('#process').val('1'); // flag doing submit
				
				$.ajax ({
					type: "POST",
					url: "<?php echo $save_url ?>",
					data: $("#<?php echo $form_name; ?>").serialize(),
					success: function(data) {
			            $.fancybox({
			                'content' 				: data,
							'overlayOpacity' 		: 0.6,
							'showCloseButton' 		: false,
							'width'					: 'auto',
							'height' 				: 'auto',
							'centerOnScroll' 		: false,
							'autoScale'				: false,
							'autoDimensions'		: true,
							'hideOnOverlayClick' 	: false
						});
					}
				});
			}

 			function printClicked() {
 	 			// do save before print
				var f = document.forms[0];
				$('#print').val('1'); // flag doing print
				var prnwin = window.open('','print','width=735px,height=575px,status=no,scrollbars=yes');
				prnwin.focus();
				$('#<?php echo $form_name ?>').attr('target','print');
				restoreSession();
				f.submit();
 			}

			function doClose() {
				<?php if ($popup) { ?>
				window.close();
				<?php } else { ?>
				top.restoreSession();
				window.location='<?php echo $cancel_url ?>';
				<?php } ?>
			}
			
			function doReturn(id) {
				<?php if ($popup) { ?>
				window.close();
				<?php } else { ?>
				top.restoreSession();
				window.location= '<?php echo $reload_url?>'+id;
				<?php } ?>
			}
			
 			 // define ajax error handler
			$(function() {
			    $.ajaxSetup({
			        error: function(jqXHR, exception) {
			            if (jqXHR.status === 0) {
			                alert('Not connected to network.');
			            } else if (jqXHR.status == 404) {
			                alert('Requested page not found. [404]');
			            } else if (jqXHR.status == 500) {
			                alert('Internal Server Error [500].');
			            } else if (exception === 'parsererror') {
			                alert('Requested JSON parse failed.');
			            } else if (exception === 'timeout') {
			                alert('Time out error.');
			            } else if (exception === 'abort') {
			                alert('Ajax request aborted.');
			            } else {
			                alert('Uncaught Error.\n' + jqXHR.responseText);
			            }
			        }
			    });

			    return false;
			});

			// search for the provided icd code
			function searchDiagnosis() {
				var output = '';
				var f = document.forms[0];
				var code = f.searchIcd.value;
				if ( code == '' ) { 
					alert('You must enter a diagnosis search code.');
					return;
				}
				
				// retrieve the diagnosis array
				$.ajax ({
					type: "POST",
					url: "<?php echo $GLOBALS['webroot'] ?>/library/wmt/laboratory/OrderAjax.php",
					dataType: "json",
					data: {
						type: 'icd9',
						code: code
					},
					success: function(data) {
				    	$.each(data, function(key, val) {
					    	id = val.code.replace('.','_');
					    	code = val.code.replace('ICD10:','');
				    		output += "<tr><td style='white-space:nowrap;width:60px'><input class='wmtCheck' type='checkbox' name='check_"+id+"' code='"+val.code+"' desc='"+val.long_desc+"'/> <b>"+code+"</b> - </td><td style='padding-top:3px'>"+val.short_desc+"<br/></td>\n";
						});
					},
					async:   false
				});

				if (output == '') {
					output = '<table><tr><td><h4>NO MATCHES</h4></td></tr></table>';
				}
				else{
					output = '<table>' + output + '</table>';
				}
				
				$('#dc_Search').html(output);
				$("#dc_tabs").tabs( "option", "active", 0 );	
				f.searchIcd.value = '';
			}

			function addCodes() {
				var count = 0;
				$('#dc_tabs').tabs('option','active');
				$("#dc_tabs div[aria-hidden='false'] input:checked").each(function() {
					success = addCodeRow($(this).attr('code'), $(this).attr('desc'));
					$(this).attr('checked',false);
					if (success) count++;
				});
			}
			
			function addCodeRow(code,text) {
				$('#codeEmptyRow').remove();

				id = code.replace('.','_');
				id = id.replace('ICD9:','');
				id = id.replace('ICD10:','');
				if ($('#code_'+id).length) {
					alert("Code "+code+" has already been added.");
					return false;
				}

				if ($('#codeTable tr').length > 10) {
					alert("Maximum number of diagnosis codes exceeded.");
					return false;
				}
				
				var newRow = "<tr id='code_" +id + "'>";
				newRow += "<td><input type='button' class='wmtButton' value='remove' style='width:60px' onclick=\"removeCodeRow('code_"+id+"')\" /></td>\n";
				newRow += "<td class='wmtLabel' style='width:80px'><input name='dx_code[]' class='wmtFullInput code' style='font-weight:bold' readonly value='";
				newRow += code;
				newRow += "'/></td><td class='wmtLabel'><input name='dx_text[]' class='wmtFullInput name' readonly value='";
				newRow += text;
				newRow += "'/></td></tr>\n";
				
				$('#codeTable').append(newRow);

				return true;
			}

			function removeCodeRow(id) {
				$('#'+id).remove();
				// there is always the header and the "empty" row
				if ($('#codeTable tr').length == 1) {
					$('#codeTable').append('<tr id="CodeEmptyRow"><td colspan="3"><b>NO DIAGNOSIS CODES SELECTED</b></td></tr>');
				}
			}

			// search for the provided test code
			function searchTest() {
				var output = '';
				var f = document.forms[0];
				var code = f.searchCode.value;
				if ( code == '' ) { 
					alert('You must enter a profile or lab test search code.');
					return;
				}
				
				// retrieve the test array
				$.ajax ({
					type: "POST",
					url: "<?php echo $GLOBALS['webroot'] ?>/library/wmt/laboratory/OrderAjax.php",
					dataType: "json",
					data: {
						type: 'code',
						code: code,
						lab_id: '<?php echo $lab_id ?>'
					},
					success: function(data) {
						// data = array('id','code','type','title','description','provider');
						$.each(data, function(key, val) {
					    	var id = val.code.replace('.','_');
							var text = val.description;
							if (!text) text = val.title;
				    		output += "<tr><td style='white-space:nowrap;width:60px'><input class='wmtCheck' type='checkbox' name='check_"+val.id+"' code='"+val.code+"' desc='"+text+"' prof='"+val.type+"' /> ";
				    		if (val.type == 'pro') {
					    		output += "<span style='font-weight:bold;color:#c00'>"+val.code+"</span>";
				    		}
				    		else { 	
					    		output += "<span style='font-weight:bold'>"+val.code+"</span>";
				    		}
				    		output += " - </td><td style='width:auto;text-align:left;padding-top:3px'>"+val.title+"<br/></td>\n";
				    	});
					},
					async:   false
				});

				if (output == '') {
					output = '<table><tr><td><h4>NO MATCHES</h4></td></tr></table>';
				}
				else{
					output = '<table>' + output + '</table>';
				}
				
				$('#oc_Search').html(output);
				$("#oc_tabs").tabs( "option", "active", 0 );	
				f.searchCode.value = '';
			}

			// search for the provided test code
			function fetchDetails(code) {
				var output = '';
				
				// retrieve the test details
				$.ajax ({
					type: "POST",
					url: "<?php echo $GLOBALS['webroot'] ?>/library/wmt/laboratory/OrderAjax.php",
					dataType: "json",
					data: {
						type: 'details',
						code: code,
						lab_id: '<?php echo $lab_id ?>'
					},
					success: function(data) {
						output = data; // process later
					},
					async:   false
				});

				return output;
			}

			function addTests() {
				var count = 0;
				var errors = 0;
				$('#oc_tabs').tabs('option','active');
				$("#oc_tabs div[aria-hidden='false'] input:checked").each(function() {
					success = addTestRow($(this).attr('code'),$(this).attr('desc'),$(this).attr('prof'));
					$(this).attr('checked',false);
					if (success) {
						count++;
					}
					else {
						errors++;
					}
				});
				if (count) {
					if (errors) {
						alert("Some items were not added to order.");
					}
				}
			}
			
			function addTestRow(code,text,flag) {
				$('#orderEmptyRow').remove();

				id = code.replace('.','_');
				if ($('#test_'+id).length) {
					alert("Test "+code+" has already been added.");
					return false;
				}

				if ($('#order_table tr').length > 35) {
					alert("Maximum number of profile/test requests exceeded.");
					return false;
				}

				var data = fetchDetails(code);
				var type = data.type; // json data from ajax
				var specimen = data.specimen; // json data
				var aoe = data.aoe; // json data from ajax
				var profile = data.profile; // json data from ajax

				
<?php if ($lab_data['npi'] != '1548208440' && $lab_data['npi'] != 'INTERPATH') { // interpath does not provide specimen types ?>
				if (specimen != '') {
					current = $('#specimen_type').val();
					if (current == '') {
						$('#specimen_type').val(specimen);
					}
					else if (current != specimen) {
						alert("ORDER TYPE MISMATCH: "+current+" and "+specimen+"\n\nTest ["+code+"] requires different processing and must be entered on a separate request.");
						return false;
					}
				}
<?php } ?>

				var success = true;
				$('.component').each(function() {
					if ($(this).attr('unit') == code && success) {
						alert("Test "+code+" has already been added as profile component.");
						success = false;
					} 
				});

				if (!success) return false;

				var newRow = "<tr id='test_" +id + "'>";
				newRow += "<td style='vertical-align:top'><input type='button' class='wmtButton' value='remove' style='width:60px' onclick=\"removeTestRow('test_"+id+"')\" /> ";
				newRow += "<input type='button' class='wmtButton' value='details' style='width:60px' onclick=\"testOverview('"+id+"')\" /></td>\n";
				newRow += "<td class='wmtLabel' style='vertical-align:top;padding-top:5px;width:80px'><input name='test_code[]' class='wmtFullInput test' readonly value='"+code+"' ";
				if (flag == 'pro') { // profile test
					newRow += "style='font-weight:bold;color:#c00' /><input type='hidden' name='test_profile[]' value='pro' />";
				}
				else {
					newRow += "style='font-weight:bold' /><input type='hidden' name='test_profile[]' value='ord' />";
				} 
 				newRow += "</td><td colspan='2' class='wmtLabel' style='text-align:right;vertical-align:top;padding-top:5px'><input name='test_text[]' class='wmtFullInput component' readonly unit='"+code+"' value='"+text+"'/>\n";
				  				
				// add profile tests if necessary
				success = true;
				for (var key in profile) {
					var obj = profile[key];

					$('.component').each(function() {
						if ($(this).attr('unit') == obj.component) {
							alert("Component of test "+code+" has already been added.");
							success = false;
						} 					
					});
						
					if (obj.description)  newRow += "<input class='wmtFullInput component' style='margin-top:5px' readonly unit='"+obj.component+"' value='"+obj.component+" - "+obj.description+"'/>\n";
					
					// add component AOE questions if necessary
					var aoe2 = obj.aoe;
					for (var key2 in aoe2) {
						var obj2 = aoe2[key2];
					   
						var test_code = obj2.code;
						var test_unit = obj2.unit;
						var test_require = obj2.required;
						var question = obj2.question.replace(':','');
						if (obj2.description) question = obj2.description.replace(':',''); // use longer if available
						if (test_require) question = '*' + question;
						var prompt = obj2.prompt;
						if (test_code) {
							newRow += '<input type="hidden" name="aoe'+id+'_label[]" value="'+question+'" />'+"\n";
							newRow += "<input type='hidden' name='aoe"+id+"_code[]' value='"+test_code+"' />\n";
					   		newRow += "<input type='hidden' name='aoe"+id+"_unit[]' value='"+test_unit+"' />\n";
					   		newRow += "<input type='hidden' name='aoe"+id+"_require[]' value='"+test_require+"' />\n";
					   		newRow += "<div style='margin-top:5px'>" + question + ": <input name='aoe"+id+"_text[]' title='" + test_code + ": " + prompt + "' class='wmtFullInput aoe' value='' style='width:300px' /></div>\n";
						}	
					}
				}

				if (!success) return false;
				
				// add order AOE questions if necessary
				for (var key in aoe) {
					var obj = aoe[key];
				   
					var test_code = obj.code;
					var test_require = obj.required;
					var question = obj.question.replace(':','');
					if (obj.description) question = obj.description.replace(':',''); // use longer if available
					if (test_require == 1) question = '*' + question;
					var prompt = obj.prompt;

					if (test_code) {
						newRow += '<input type="hidden" name="aoe'+id+'_label[]" value="'+question+'" />'+"\n";
						newRow += "<input type='hidden' name='aoe"+id+"_code[]' value='"+test_code+"' />\n";
					   	newRow += "<input type='hidden' name='aoe"+id+"_require[]' value='"+test_require+"' />\n";
						newRow += "<div style='margin-top:5px'>" + question + ": ";
						if (obj.field == 'L') {
							newRow += "<select name='aoe"+id+"_text[]' title='" + prompt + "' class='wmtFullInput aoe' value='' style='width:300px'>\n";
							newRow += "<option>"+window[obj.options]+"</option>\n";
							newRow += "</select>\n";

						}	
						else {
							newRow += "<input name='aoe"+id+"_text[]' title='" + prompt + "' class='wmtFullInput aoe' value='' style='width:300px' />";
						}
						newRow += "</div>\n";
					}	
				}

				newRow += "</td></tr>\n"; // finish up order row

				$('#order_table').append(newRow);

				return true;
			}

			function removeTestRow(id) {
				$('#'+id).remove();
				// there is always the header and the "empty" row
				if ($('#order_table tr').length == 1) {
					$('#specimen_type').val("");
					$('#order_table').append('<tr id="orderEmptyRow"><td colspan="3"><b>NO PROFILES / TESTS SELECTED</b></td></tr>');
				}
			}

			// display test overview pop up
			function testOverview(code) {
				$.fancybox.showActivity();
				
				// retrieve the overview details
<?php if ($lab_data['npi'] == '1548208440' || $lab_data['npi'] == 'INTERPATH') { // screen scrap for interpath ?>
				$.ajax ({
					type: "POST",
					url: "<?php echo $GLOBALS['webroot'] ?>/library/wmt/laboratory/OrderAjax.php",
					dataType: "html",
					data: {
						type: 'dynamic',
						code: code
					},
					success: function(data) {
			            $.fancybox({
			                'content' 				: data,
							'overlayOpacity' 		: 0.6,
							'showCloseButton' 		: true,
							'width'					: '700',
							'height' 				: '400',
							'centerOnScroll' 		: false,
							'autoScale'				: false,
							'autoDimensions'		: false,
							'hideOnOverlayClick' 	: true,
							'scrolling'				: 'auto'
						});
										},
					async:   false
				});

				return;
			}

<?php } else { // start normal details ?>
				$.ajax ({
					type: "POST",
					url: "<?php echo $GLOBALS['webroot'] ?>/library/wmt/laboratory/OrderAjax.php",
					dataType: "html",
					data: {
						type: 'overview',
						code: code
					},
					success: function(data) {
			            $.fancybox({
			                'content' 				: data,
							'overlayOpacity' 		: 0.6,
							'showCloseButton' 		: true,
							'width'					: '500',
							'height' 				: '400',
							'centerOnScroll' 		: false,
							'autoScale'				: false,
							'autoDimensions'		: false,
							'hideOnOverlayClick' 	: true,
							'scrolling'				: 'auto'
						});
										},
					async:   false
				});

				return;
			}
			
<?php } ?>
			// print labels
			function printLabels(item) {
				var f = document.forms[0];
				var fl = document.forms[item];
				var printer = fl.labeler.value;
//				var printer = 'file';
				if ( printer == '' ) { 
					alert('Unable to determine default label printer.\nPlease select a label printer.');
					return;
				}

				var count = fl.count.value;
				var order = f.order_number.value;
				var patient = "<?php echo $pat_data->lname; ?>, <?php echo $pat_data->fname; ?> <?php echo $pat_data->mname; ?>";
				var pid = "<?php echo $pat_data->pid  ?>";
				
				// retrieve the label
				$.ajax ({
					type: "POST",
					url: "<?php echo $GLOBALS['webroot'] ?>/library/wmt/laboratory/OrderAjax.php",
					dataType: "text",
					data: {
						type: 'label',
						printer: printer,
						count: count,
						siteid: '<?php echo $lab_data['send_fac_id'] ?>',
						order: order,
						patient: patient,
						pid: pid
					},
					success: function(data) {
						if (printer == 'file') {
							window.open(data,"_blank");
						}
						else {
							alert(data);
						}
					},
					async:   false
				});

			}

			// setup jquery processes
			$(document).ready(function(){
				$('#dc_tabs').tabs().addClass('ui-tabs-vertical ui-helper-clearfix');
				$('#oc_tabs').tabs().addClass('ui-tabs-vertical ui-helper-clearfix');

				$("#searchIcd").keyup(function(event){
				    if(event.keyCode == 13){
				        searchDiagnosis();
				    }
				});

				$("#searchCode").keyup(function(event){
				    if(event.keyCode == 13){
				        searchTest();
				    }
				});
				
				$("#order_psc").change(function(){
					$("#sample_data").show();
					$("#ship_data").show();
					$("#psc_data").hide();
					
				    if ($(this).attr("checked")) {
						$("#sample_data").hide();
						$("#ship_data").hide();
						$("#psc_data").show();
				    }
				});
				
				$("#work_flag").change(function(){
					$("#work_data").hide();
					
				    if ($(this).attr("checked")) {
						$("#work_data").show();
				    }
				});

				// setup fancybox
				$(".inline").fancybox( {
					'overlayOpacity' : 0.0,
					'showCloseButton' : true,
					'autoDimensions' : false,
					'height' : 280,
					'width' : 650,
					'type' : 'inline'
				});

				$(".inline").click(function() {
					var key = $(this).attr('key');
					var code = $('#result_code_'+key).val();
					var text = $('#result_text_'+key).val();
					var notes = $('#result_notes_'+key).val();
					var status = $('#result_status_'+key).val();
					var date = $('#result_date_'+key).val();
					var clinician = $('#result_clinician_'+key).val();

                    $('#edit_key').val(key);
                    $('#edit_code').val(code);
                    $('#edit_text').val(text);
                    $('#edit_data').val(notes);
                    if (!status) status = 'final';
                    $('#edit_status').val(status);
                    if (!date) date = '<?php echo date('Y-m-d') ?>';
                    $('#edit_date').val(date);
                    if (!clinician || clinician == 0) clinician = '<?php echo $_SESSION['authId'] ?>';
                    $('#edit_clinician').val(clinician);
				});
				
<?php if ($status != 'i') { // disable if not incomplete ?> 
				$("#orderEntry :input").attr("disabled", true);
				$("#orderReview :input").attr("disabled", true);
				$("#orderSubmission :input").attr("disabled", true);
				$(".nolock").attr("disabled", false);
<?php } ?>

			});
				
 			function saveResult() {
				var key = $('#edit_key').val();
 				var code = $('#edit_code').val();
				var text = $('#edit_text').val();
				var notes = $('#edit_data').val();
				var status = $('#edit_status').val();
				var date = $('#edit_date').val();
				var clinician = $('#edit_clinician').val();
				var cname = $( "#edit_clinician option:selected" ).text();
				var sname = $( "#edit_status option:selected" ).text();
				
				$('#result_notes_'+key).val(notes);
				$('#result_status_'+key).val(status);
				$('#result_sname_'+key).val(sname);
				$('#result_date_'+key).val(date);
				$('#result_clinician_'+key).val(clinician);
				$('#result_cname_'+key).val(cname);
				$('#result_'+key).show();
				
				$.fancybox.close();
 			}

			
		</script>
	</head>

	<body class="body_top">

		<!-- Required for the popup date selectors -->
		<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>
		
		<form method='post' action="<?php echo $save_url ?>" id='<?php echo $form_name; ?>' name='<?php echo $form_name; ?>' > 
			<input type='hidden' name='process' id='process' value='' />
			<input type='hidden' name='print' id='print' value='' />
			<input type='hidden' name='pop' id='pop' value='<?php if ($popup) echo '1' ?>' />
			<input type='hidden' name='patient_id' id='patient_id' value='<?php echo $pid ?>' />
			<input type='hidden' name='facility_id' id='facility_id' value='<?php echo $enc_data->facility_id ?>' />
			<div class="wmtTitle">
<?php if ($viewmode) { ?>
				<input type=hidden name='mode' value='update' />
				<input type=hidden name='id' value='<?php echo $_GET["id"] ?>' />
				<span class=title><?php echo $form_title; ?> <?php echo (!goodDate($order_data->date_transmitted))? 'Update' : '&amp; Results View'; ?></span>
<?php } else { ?>
				<input type='hidden' name='mode' value='new' />
				<span class='title'>New <?php echo $form_title; ?></span>
<?php } ?>
			</div>

<!-- BEGIN ORDER -->
<?php 
	$info_border = "border-bottom:none";
	$info_arrow = "$webroot/library/wmt/fill-270.png";
	$info_hide = "display:none";
if ($client_id == "walsh" || $client_id == "uimda") {
		$info_border = "";
		$info_arrow = "$webroot/library/wmt/fill-090.png";
		$info_hide = "";
	}
?>
		<!-- Client Information -->
			<div class="wmtMainContainer wmtColorMain" id="clientData" style="width:99%">
				<div class="wmtCollapseBar wmtColorBar" id="ReviewCollapseBar" style="<?php echo $info_border ?>" onclick="togglePanel('ReviewBox','ReviewImageL','ReviewImageR','ReviewCollapseBar')">
					<table style="width:100%">	
						<tr>
							<td>
								<img id="ReviewImageL" align="left" src="<?php echo $info_arrow;?>" border="0" alt="Show/Hide" title="Show/Hide" />
							</td>
							<td class="wmtChapter" style="text-align: center">
								Patient Information
							</td>
							<td style="text-align: right">
								<img id="ReviewImageR" src="<?php echo $info_arrow;?>" border="0" alt="Show/Hide" title="Show/Hide" />
							</td>
						</tr>
					</table>
				</div>
				
				<div class="wmtCollapseBox" id="ReviewBox" style="<?php echo $info_hide ?>">
					<table style="width:100%">	
						<tr>
							<!-- Left Side -->
							<td style="width:50%" class="wmtInnerLeft">
								<table style="width:99%">
							        <tr>
										<td style="width:20%" class="wmtLabel">
											Patient First
											<input name="pat_fname" type="text" class="wmtFullInput" readonly value="<?php echo ($completed)?$order_data->pat_fname:$pat_data->fname; ?>">
											<input name="pat_race" type="hidden" value="<?php echo $pat_data->race; ?>">
											<input name="pricelevel" type="hidden" value="<?php echo $pat_data->pricelevel; ?>">
											<input name="pid" type="hidden" value="<?php echo $pat_data->pid; ?>">
											<input name="pubpid" type="hidden" value="<?php echo $pat_data->pubpid; ?>">
											<input name="encounter" type="hidden" value="<?php echo $encounter; ?>">
										</td>
										<td style="width:10%" class="wmtLabel">
											Middle
											<input name="pat_mname" type="text" class="wmtFullInput" readonly value="<?php echo ($completed)?$order_data->pat_mname:$pat_data->mname; ?>">
										</td>
										<td class="wmtLabel">
											Last Name
											<input name="pat_lname" type="text" class="wmtFullInput" readonly value="<?php echo ($completed)?$order_data->pat_lname:$pat_data->lname; ?>">
										</td>
										<td style="width:20%" class="wmtLabel">
											Patient Id
											<input name="pat_pubpid" type="text" class="wmtFullInput" readonly value="<?php echo ($completed)?$order_data->pat_pubpid:$pat_data->pubpid; ?>">
										</td>
										<td colspan="2" style="width:20%" class="wmtLabel">
											Social Security
											<input name="pat_ss" type"text" class="wmtFullInput" readonly value="<?php echo ($completed)?$order_data->pat_ss:$pat_data->ss ?>">
										</td>
									</tr>

									<tr>
										<td colspan="3" class="wmtLabel">Email Address<input name="pat_email" type="text" class="wmtFullInput" readonly value="<?php echo ($completed)?$order_data->pat_email:$pat_data->email; ?>"></td>
										<td style="width:20%" class="wmtLabel">
											Birth Date
											<input name="pat_DOB" type="text" class="wmtFullInput" readonly value="<?php echo $pat_data->birth_date; ?>">
										</td>
										<td style="width:5%" class="wmtLabel">
											Age
											<input name="pat_age" type="text" class="wmtFullInput" readonly value="<?php echo $pat_data->age; ?>">
										</td>
										<td style="width:15%" class="wmtLabel">
											Gender
											<input name="pat_sex" type="hidden" value="<?php echo $pat_data->sex ?>" />
											<input type="text" class="wmtFullInput" readonly value="<?php echo ListLook($pat_data->sex, 'sex') ?>">
										</td>
									</tr>

									<tr>
										<td colspan="3" class="wmtLabel">
											Primary Address
											<input name="pat_street" type="text" class="wmtFullInput" readonly value="<?php echo $pat_data->street; ?>">
										</td>
										<td class="wmtLabel">Mobile Phone<input name="pat_mobile" id="ex_phone_mobile" type="text" class="wmtFullInput" readonly value="<?php echo $pat_data->phone_cell; ?>"></td>
										<td colspan="2" class="wmtLabel">Home Phone<input name="pat_phone" type="text" class="wmtFullInput" readonly value="<?php echo $pat_data->phone_home; ?>"></td>
									</tr>

									<tr>
										<td colspan="3" class="wmtLabel" style="width:50%">
											City
											<input name="pat_city" type="text" class="wmtFullInput" readonly value="<?php echo $pat_data->city; ?>">
										</td>
										<td class="wmtLabel">
											State
											<input type="text" class="wmtFullInput" readonly value="<?php echo ListLook($pat_data->state, 'state'); ?>">
											<input type="hidden" name="pat_state" value="<?php echo $pat_data->state ?>" />
										</td>
										<td colspan="2" class="wmtLabel">
											Postal Code
											<input name="pat_zip" type="text" class="wmtFullInput" readonly value="<?php echo $pat_data->postal_code; ?>">
										</td>
									</tr>
								</table>
							</td>
							
							<!-- Right Side -->
							<td style="width:50%" class="wmtInnerRight">
								<table style="width:99%">
									<tr>
										<td style="width:20%" class="wmtLabel">
											Insured First
											<input name="ins_fname" type="text" class="wmtFullInput" readonly value="<?php echo $ins_list[0]->subscriber_fname; ?>">
										</td>
										<td style="width:10%" class="wmtLabel">
											Middle
											<input name="ins_mname" type"text" class="wmtFullInput" readonly value="<?php echo $ins_list[0]->subscriber_mname; ?>">
										</td>
										<td class="wmtLabel">
											Last Name
											<input name="ins_lname" type"text" class="wmtFullInput" readonly value="<?php echo $ins_list[0]->subscriber_lname; ?>">
										</td>
										<td style="width:20%" class="wmtLabel">
											Birth Date
											<input name="ins_DOB" type="text" class="wmtFullInput" readonly value="<?php echo $ins_list[0]->subscriber_birth_date; ?>">
										</td>
										<td style="width:20%" class="wmtLabel">
											Relationship
											<input name="ins_relation" type="text" class="wmtFullInput" readonly value="<?php echo ListLook($ins_list[0]->subscriber_relationship, 'sub_relation'); ?>">
											<input name="ins_ss" type="hidden" value="<?php echo $ins_list[0]->subscriber_ss ?>" />
											<input name="ins_sex" type="hidden" value="<?php echo $ins_list[0]->subscriber_sex ?>" />
										</td>
									</tr>
									<tr>
										<td colspan="3" class="wmtLabel">
											Primary Insurance
											<input type="text" class="wmtFullInput" readonly value="<?php echo ($ins_list[0]->company_name)?$ins_list[0]->company_name:'No Insurance'; ?>">
											<input id="ins_primary" name="ins_primary" type="hidden" value="<?php echo $ins_list[0]->id ?>"/>
											<input id="ins_primary_lab" name="ins_primary_lab" type="hidden" value="<?php echo $ins_primary_lab ?>"/>
										</td>
										<td class="wmtLabel">Policy #<input name="ins_primary_policy" type="text" class="wmtFullInput" readonly value="<?php echo $ins_list[0]->policy_number; ?>"></td>
										<td class="wmtLabel">Group #<input name="ins_primary_group" type="text" class="wmtFullInput" readonly value="<?php echo $ins_list[0]->group_number; ?>"></td>
									</tr>
									<tr>
										<td colspan="3" class="wmtLabel">
											Secondary Insurance
											<input type="text" class="wmtFullInput" readonly value="<?php echo $ins_list[1]->company_name; ?>">
											<input id="ins_secondary" name="ins_secondary" type="hidden" value="<?php echo $ins_list[1]->id ?>"/>
											<input id="ins_secondary_lab" name="ins_secondary_lab" type="hidden" value="<?php echo $ins_secondary_lab ?>"/>
										</td>
										<td class="wmtLabel">Policy #<input name="ins_secondary_policy" type="text" class="wmtFullInput" readonly value="<?php echo $ins_list[1]->policy_number; ?>"></td>
										<td class="wmtLabel">Group #<input name="ins_secondary_group" type="text" class="wmtFullInput" readonly value="<?php echo $ins_list[1]->group_number; ?>"></td>
									</tr>
									<tr>
										<td style="width:20%" class="wmtLabel">
											Guarantor First
											<input name="guarantor_fname" type="text" class="wmtFullInput" readonly value="<?php echo ($ins_list[0]->subscriber_lname)?$ins_list[0]->subscriber_fname:$pat_data->fname; ?>">
											<input name="guarantor_phone" type="hidden" value="<?php echo ($ins_list[0]->subscriber_phone)?$ins_list[0]->subscriber_phone:$pat_data->phone_home ?>" />
											<input name="guarantor_street" type="hidden" value="<?php echo ($ins_list[0]->subscriber_street)?$ins_list[0]->subscriber_street:$pat_data->street ?>" />
											<input name="guarantor_city" type="hidden" value="<?php echo ($ins_list[0]->subscriber_street)?$ins_list[0]->subscriber_city:$pat_data->city ?>" />
											<input name="guarantor_state" type="hidden" value="<?php echo ($ins_list[0]->subscriber_street)?$ins_list[0]->subscriber_state:$pat_data->state ?>" />
											<input name="guarantor_zip" type="hidden" value="<?php echo ($ins_list[0]->subscriber_street)?$ins_list[0]->subscriber_postal_code:$pat_data->postal_code ?>" />
										</td>
										<td style="width:10%" class="wmtLabel">
											Middle
											<input name="guarantor_mname" type="text" class="wmtFullInput" readonly value="<?php echo ($ins_list[0]->subscriber_lname)?$ins_list[0]->subscriber_mname:$pat_data->mname; ?>">
										</td>
										<td style="width:20%" class="wmtLabel">
											Last Name
											<input name="guarantor_lname" type="text" class="wmtFullInput" readonly value="<?php echo ($ins_list[0]->subscriber_lname)?$ins_list[0]->subscriber_lname:$pat_data->lname; ?>">
										</td>
										<td class="wmtLabel">SS#<input name="guarantor_ss" type="text" class="wmtFullInput" readonly value="<?php echo ($ins_list[0]->subscriber_ss)?$ins_list[0]->subscriber_ss:$pat_data->ss; ?>"></td>
										<td class="wmtLabel">
											Relationship
											<input name="guarantor_relation" type="text" class="wmtFullInput" readonly value="<?php echo ($ins_list[0]->subscriber_relationship)?ListLook($ins_list[0]->subscriber_relationship, 'sub_relation'):'Self'; ?>">
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				</div>
			</div>
			<!-- End Client Information -->
		
			<!--  Order Entry -->
			<div class="wmtMainContainer wmtColorMain" id="orderEntry" style="width:99%;<?php if ($status != 'i') echo 'display:none'; ?>">
				<div class="wmtCollapseBar wmtColorBar" id="EntryCollapseBar" onclick="togglePanel('EntryBox','EntryImageL','EntryImageR','EntryCollapseBar');">
					<table style="width:100%">	 
						<tr>
							<td>
								<img id="EntryImageL" align="left" src="<?php echo $webroot;?>/library/wmt/fill-090.png" border="0" alt="Show/Hide" title="Show/Hide" />
							</td>
							<td class="wmtChapter" style="text-align: center">
								Order Entry
							</td>
							<td style="text-align: right">
								<img id="EntryImageR" src="<?php echo $webroot;?>/library/wmt/fill-090.png" border="0" alt="Show/Hide" title="Show/Hide" />
							</td>
						</tr>
					</table>
				</div>
				
				<div class="wmtCollapseBox" id="EntryBox">
					<table style="width:100%;height:310px">
						<tr>
							<!-- Left Side -->
							<td class="wmtInnerLeft" style='width:49%;padding-left:5px;padding-right:6px'>
								<table class="wmtLabBox wmtColorBox">
							        <tr>
										<td class="wmtLabHeader wmtColorHeader">
											<div class="wmtLabTitle">
												CLINICAL DIAGNOSIS CODES&nbsp;
											</div>
											<div style="float:left;vertical-align:bottom;">
												<input class="wmtButton" type="button" style="margin-top:2px" onclick="addCodes()" value="add selected"/>
											</div>
											<div style="float:right">
												<input class="wmtInput" style="background-color:white" type="text" name="searchIcd" id="searchIcd" />
												<input class="wmtButton" type="button" value="search" onclick="searchDiagnosis()" />
											</div>
										</td>
									</tr>
									<tr>
										<td class="wmtLabBody">
											<div id="dc_tabs">
												<div class="wmtLabMenu wmtColorMenu">
													<ul style="margin:0;padding:0">
<?php 
$title = 'Search';
echo "<li><a href='#dc_Search'>Search</a></li>\n";
foreach ($dlist as $data) {
	if ($data['title'] != $title) {
		$title = $data['title']; // new tab
		$link = strtolower(str_replace(' ', '_', $title));
		echo "<li><a href='#dc_".$link."'>".$title."</a></li>\n";
	}
}
?>
													</ul>
												</div>
												
<?php 
$title = 'Search';
echo "<div class='wmtQuick' id='dc_Search' style='display:none'><table width='100%'><tr><td style='text-align:center;padding-top:30px'><h3>Select profile at left or<br/>search using search box at top.</h3></tr></td>\n";
foreach ($dlist as $data) {
	if ($data['title'] != $title) {
		if ($title) echo "</table></div>\n"; // end previous section
		$title = $data['title']; // new section
		$link = strtolower(str_replace(' ', '_', $title));
		echo "<div class='wmtQuick' id='dc_".$link."' style='display:none'><table>\n";
	}
	$text = ($data['notes']) ? $data['notes'] : $data['short_desc'];
	$code = str_replace('ICD9:', '', $data['code']);
	$code = str_replace('ICD10:', '', $code);
	$id = str_replace('.', '_', $code);
	echo "<tr><td style='white-space:nowrap;width:60px'><input class='wmtCheck' type='checkbox' id='check_".$id."' code='".$data['code']."' desc='".htmlspecialchars($text)."' > <b>".$code."</b></input> - </td><td style='padding-top:4px'>".$text."</td></tr>\n";
}
if ($title) echo "</table></div>\n"; // end if at least one section
?>
											</div>
										</td>
									</tr>
								</table>
							</td>
							
							<!-- Right Side -->
							<td  class="wmtInnerRight" style='width:49%;padding-left:10px;padding-right:3px'>
								<table class="wmtLabBox wmtColorBox">
							        <tr>
										<td class="wmtLabHeader wmtColorHeader">
											<div class="wmtLabTitle">
												<?php echo strtoupper($lab_data['name'])?> CODES&nbsp;
											</div>
											<div style="float:left">
												<input class="wmtButton" type="button" style='vertical-align:top' onclick="addTests()" value="add selected"/>
											</div>
											<div style="float:right">
												<input class="wmtInput" style="background-color:white;vertical-align:top;" type="text" name="searchCode" id="searchCode" />
												<input class="wmtButton" type="button" style='vertical-align:top' value="search" onclick="searchTest()" />
											</div>
										</td>
									</tr>
									<tr>
										<td class="wmtLabBody">
											<div id="oc_tabs">
												<div class="wmtLabMenu wmtColorMenu">
													<ul style="margin:0;padding:0">
<?php 
$title = 'Search';
echo "<li><a href='#oc_Search'>Search</a></li>\n";
foreach ($olist as $data) {
	if ($data['title'] != $title) {
		$title = $data['title']; // new tab
		$link = strtolower(str_replace(' ', '_', $title));
		echo "<li><a href='#oc_".$link."'>".$title."</a></li>\n";
	}
}
?>
													</ul>
												</div>
												
<?php 
$title = 'Search';
echo "<div class='wmtQuick' id='oc_Search' style='display:none'><table width='100%'><tr><td style='text-align:center;padding-top:30px'><h3>Select profile at left or<br/>search using search box at top.</h3>\n";
foreach ($olist as $data) {
	if ($data['title'] != $title) {
		if ($title) echo "</table></div>\n"; // end previous section
		$title = $data['title']; // new section
		$link = strtolower(str_replace(' ', '_', $title));
		echo "<div class='wmtQuick' id='oc_".$link."' style='display:none'><table>\n";
	}
	$text = ($data['description']) ? $data['description'] : $data['name'];
	$id = str_replace('.', '_', $data['code']);
	echo "<tr><td style='white-space:nowrap;width:60px'><input class='wmtCheck' type='checkbox' id='mark_".$id."' code='".$data['code']."' desc='".htmlspecialchars($text)."' > <b>".$data['code']."</b></input> - </td><td style='padding-top:6px'>".$text."</td></tr>\n";
}
if ($title) echo "</table></div>\n"; // end if at least one section
?>
											</div>
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				</div>
			</div>
			<!-- End Order Entry -->
								
			<!--  Order Review -->
			<div class="wmtMainContainer wmtColorMain" id="orderReview" style="width:99%">
				<div class="wmtCollapseBar wmtColorBar" id="OrderCollapseBar" style="<?php if ($status != 'i') echo "border-bottom:none" ?>" onclick="togglePanel('OrderBox','OrderImageL','OrderImageR','OrderCollapseBar')">
					<table style="width:100%">	
						<tr>
							<td>
								<img id="OrderImageL" align="left" src="<?php echo $webroot;?>/library/wmt/fill-<?php echo ($status != 'i')? "270" : "090" ?>.png" border="0" alt="Show/Hide" title="Show/Hide" />
							</td>
							<td class="wmtChapter" style="text-align: center">
								Order Review
							</td>
							<td style="text-align: right">
								<img id="OrderImageR" src="<?php echo $webroot;?>/library/wmt/fill-<?php echo ($status != 'i')? "270" : "090" ?>.png" border="0" alt="Show/Hide" title="Show/Hide" />
							</td>
						</tr>
					</table>
				</div>
				
				<div class="wmtCollapseBox" id="OrderBox" style="<?php if ($status != 'i') echo 'display:none' ?>">
					<table style="width:100%">
						<tr>
							<td>
								<fieldset>
									<legend>Diagnosis Codes</legend>

									<table id="codeTable" style="width:100%">
										<tr>
											<th class="wmtHeader" style="width:60px">Action</th>
											<th class="wmtHeader" style="width:120px">Diagnosis</th>
											<th class="wmtHeader">Description</th>
										</tr>

<?php 
// load the existing diagnosis codes
$newRow = '';
$diag_array = array();
if ($order_data->diagnoses)
	$diag_array = explode("|", $order_data->diagnoses); // code & text

foreach ($diag_array AS $diag) {
	list($code,$text) = explode("^", $diag);
	if (empty($code)) continue;
	if (strpos($code,":") !== false)	
		list($dx_type,$code) = explode(":", $code);

	if (!$dx_type) $dx_type = 'ICD9';
	
	$key = str_replace('.', '_', $code);
	$code = $dx_type.":".$code;
	
	// add new row
	$newRow .= "<tr id='code_".$key."'>";
	$newRow .= "<td><input type='button' class='wmtButton' value='remove' style='width:60px' onclick=\"removeCodeRow('code_".$key."')\" /></td>\n";
	$newRow .= "<td class='wmtLabel'><input name='dx_code[]' class='wmtFullInput code' style='font-weight:bold' readonly value='".$code."'/>\n";
	$newRow .= "</td><td class='wmtLabel'><input name='dx_text[]' class='wmtFullInput name' readonly value='".$text."'/>\n";
	$newRow .= "</td></tr>\n";
}

// anything found
if ($newRow) {
	echo $newRow;
}
else { // create empty row
?>
										<tr id="codeEmptyRow">
											<td colspan="3">
												<b>NO DIAGNOSIS CODES SELECTED</b>
											</td>
										</tr>
<?php } ?>
									</table>
								</fieldset>
							
							</td>
						</tr>

<?php 
// create unique identifier for order number
if ($viewmode) {
	$ordnum = $order_data->order_number;
}
else {
	$ordnum = $GLOBALS['adodb']['db']->GenID('order_seq');
	
	// duplicate checking
	$dupchk = sqlQuery("SELECT procedure_order_id AS id FROM procedure_order WHERE procedure_order_id = ?",array($ordnum));
	while($dupchk['id']) {
		$ordnum = $GLOBALS['adodb']['db']->GenID('order_seq');
		$dupchk = sqlQuery("SELECT procedure_order_id AS id FROM procedure_order WHERE procedure_order_id = ?",array($ordnum));
	} 
}
?>
						<tr>
							<td>
								<fieldset>
									<legend>Order Requisition - <?php echo $ordnum ?></legend>
									<input type="hidden" name="order_number" value="<?php echo $ordnum ?>" />
									
									<table style="width:100%;margin-bottom:20px">
										<tr>
											<td class="wmtHeader">
												LABORATORY PROCESSOR
											</td>
										</tr><tr>
											<td class="wmtOutput" style="font-weight:bold">
												<input type="hidden" name="lab_id" value="<?php echo $lab_id ?>" />
												<?php echo $lab_data['name'] ?>
											</td>
										</tr>
									</table>
									
									<hr style="border-color: #f0f0f0" />

									<input type="hidden" id="specimen_type" name="specimen_type" value = "<?php echo $order_data->specimen_type ?>" />
									
<?php if ($lab_data['type'] == 'laboratory') { ?>
									<table style="margin-bottom:25px;width:100%">
										<tr>
											<td colspan="10">
<?php if ($lab_data['npi'] == 'CERNER') { ?>
												<input type="hidden" name="order_psc" value="1" />
												<label class="wmtLabel" style="vertical-align:middle">Specimen Not Collected [ PSC Hold Order ]</label>
<?php } elseif ($lab_data['npi'] == 'BIOREF') { ?>
												<input type="hidden" name="order_psc" value="0" />
												<span class="wmtHeader">Specimen Information</span>
<?php } else { ?>
												<input type="checkbox" class="wmtCheck" id="order_psc" name="order_psc" value="1" <?php if ($order_data->order_psc || (!$viewmode && $GLOBALS['wmt_lab_psc'])) echo "checked" ?> />
												<label class="wmtLabel" style="vertical-align:middle">Specimen Not Collected [ PSC Hold Order ]</label>
<?php } ?>
											</td>
										</tr>
										<tr id="sample_data" style="<?php if ($order_data->order_psc || (!$viewmode && $GLOBALS['wmt_lab_psc']) || $lab_data['npi'] == 'CERNER') echo "display:none" ?>">
											<td style='min-width:70px'>
												<label class="wmtLabel">Collection: </label>
											</td><td style="white-space:nowrap">
												<input class="wmtInput" type='text' style='width:80px' name='date_collected' id='date_collected' 
													value='<?php echo $viewmode ? (!goodDate($order_data->date_collected))? '' : date('Y-m-d',strtotime($order_data->date_collected)) : date('Y-m-d'); ?>'
													title='<?php xl('yyyy-mm-dd Date sample taken','e'); ?>'
													onkeyup='datekeyup(this,mypcc)' onblur='dateblur(this,mypcc)' />
												<img src='../../pic/show_calendar.gif' align='absbottom' width='24' height='22'
													id='img_date_collected' border='0' alt='[?]' style='cursor:pointer;cursor:hand;'
													title='<?php xl('Click here to choose a date','e'); ?>'>
											</td>
											<td style='text-align:right;min-width:45px;white-space:nowrap'>
												<label class="wmtLabel">Time: </label>
											</td><td style="white-space:nowrap">
												<input type="text" class="wmtInput" style="width:50px" name='time_collected' id='time_collected' 
												value='<?php echo $viewmode ? (!goodDate($order_data->date_collected))? '' : date('H:i',strtotime($order_data->date_collected)) : date('H:i'); ?>' /> <small>( 24hr )</small>
											</td>
<?php if ($lab_data['npi'] != 'BIOREF') { ?>
<?php if ($lab_data['npi'] != 'CERNER') { ?>
											<td style="text-align:right;min-width:60px">
												<label class="wmtLabel">Source: </label>
											</td><td style="white-space:nowrap">
												<input type="text" class="wmtInput" style="min-width:95px" name='specimen_source' id='specimen_source'
												value='<?php echo $order_data->specimen_source; ?>' />
											</td>
<?php } // end CERNER exclusion ?>
											<td style="text-align:right;min-width:65px">
												<label class="wmtLabel">Volume: </label>
											</td><td style="white-space:nowrap">
												<input type="text" class="wmtInput" style="width:70px" name='specimen_volume' id='specimen_volume'
												value='<?php echo $order_data->specimen_volume; ?>' /> <small>( ml )</small>
											</td>
											<td style="text-align:right;min-width:70px">
												<label class="wmtLabel" style="vertical-align:middle">Fasting: </label>
											</td><td style="white-space:nowrap">
												<select name='specimen_fasting' class='wmtSelect'>
													<?php ListSel($order_data->specimen_fasting,'yesno') ?>
												</select>
											</td>
<?php } // end BIOREF exclusion ?>
											<td style="width:80%"></td>
										</tr>
										
										<tr id="psc_data" style="<?php if (!$order_data->order_psc && !$GLOBALS['wmt_lab_psc']) echo "display:none" ?>">
											<td style="min-width:100px">
												<label class="wmtLabel" >Scheduled Date: </label>
											</td><td style="width:100%">
												<input class="wmtInput" type='text' style='width:80px' name='date_pending' id='date_pending' 
													value='<?php echo $viewmode ? (!goodDate($order_data->date_pending))? '' : date('Y-m-d',strtotime($order_data->date_pending)) : ''; ?>'
													title='<?php xl('yyyy-mm-dd Date sample scheduled','e'); ?>'
													onkeyup='datekeyup(this,mypcc)' onblur='dateblur(this,mypcc)' />
												<img src='../../pic/show_calendar.gif' align='absbottom' width='24' height='22'
													id='img_date_pending' border='0' alt='[?]' style='cursor:pointer;cursor:hand'
													title='<?php xl('Click here to choose a date','e'); ?>'>
											</td>
<?php if ($lab_data['npi'] == 'CERNER') { ?>
											<td style="text-align:right;width:95px">
												<label class="wmtLabel">Source: </label>
											</td><td style="white-space:nowrap">
												<select class="wmtSelect" name='specimen_source' id='specimen_source' style="min-width:150px">
													<option value='_blank'>-- select --</option>
<?php 
	$slist= sqlStatement("SELECT * FROM list_options WHERE list_id = 'Lab_Source' ORDER BY seq");
	while ($srow= sqlFetchArray($slist)) {
    	echo "<option value='" . $srow['option_id'] . "'";
		if ($order_data->specimen_source == $srow['option_id']) echo " selected";
		if (!$order_data->specimen_source && $srow['is_default']) echo " selected";
		echo ">" . $srow['title'] . "</option>";
  	}
?>
												</select>
											</td>
<?php } // end CERNER only ?>
										</tr>
									</table>
									
									<hr style="border-color: #f0f0f0" />
<?php } // end laboratory only ?>									
									<table id="order_table" style="width:100%;margin-bottom:5px">
										<tr>
											<th class="wmtHeader" style="width:120px;padding-left:9px">Actions</th>
											<th class="wmtHeader" style="width:100px">Profile / Test</th>
											<th class="wmtHeader">General Description</th>
											<!-- th class="wmtHeader" style="width:300px">Order Entry Questions</th -->
										</tr>
<?php 
// load the existing requisition codes
$newRow = '';
if (! empty($item_list)) foreach ($item_list as $order_item) { // $item = array of objects
	if (!$order_item->procedure_code) continue;
	$key = str_replace('.', '_', $order_item->procedure_code);

	// generate test row
	$newRow .= "<tr id='test_".$key."'>\n";
	$newRow .= "<td style='vertical-align:top'>\n";
	$newRow .= "<input type='button' class='wmtButton' value='remove' style='width:60px' onclick=\"removeTestRow('test_".$key."')\" /> \n";
	$newRow .= "<input type='button' class='wmtButton' value='details' style='width:60px' onclick=\"testOverview('".$order_item->procedure_code."')\" /></td>\n";
	$newRow .= "</td>\n";
	$newRow .= "<td class='wmtLabel' style='vertical-align:top;padding-top:5px;font-weight:bold' />";
	$newRow .= "<input name='test_code[]' type='input' class='wmtFullInput test' readonly='readonly' value='".$order_item->procedure_code."' ";
	if ($order_item->procedure_type == 'pro') {
		$newRow .= "style='font-weight:bold;color:#c00' /><input type='hidden' name='test_profile[]' value='pro' />";
	}
	else {
		$newRow .= "style='font-weight:bold' /><input type='hidden' name='test_profile[]' value='ord' />";
	} 
	//$newRow .= "</td><td colspan='2' class='wmtLabel' style='text-align:right;vertical-align:top;padding-top:5px'><input name='test_text[]' class='wmtFullInput' readonly value='".$order_item->procedure_name."'/>\n";
	$newRow .= "</td><td colspan='2' class='wmtLabel' style='text-align:right;vertical-align:top;padding-top:5px'><input name='test_text[]' class='wmtFullInput' readonly value='".$order_item->procedure_name."'/>\n";
 	$newRow .= "<input type='hidden' name='test_type[]' value='".$order_item->procedure_type."' />\n";

		// add profile tests if necessary
	if ($order_item->procedure_type == 'pro') {
		// retrieve all component test if profile
		$codes = $comps = "";
		$profile = array();
		$record = sqlQuery("SELECT related_code AS components FROM procedure_type WHERE procedure_code = ? AND lab_id = ? AND procedure_type = 'pro' ",
				array($order_item->procedure_code, $lab_id));
		if ($record['components']) {
			$list = explode("^", $record['components']);
			if (!is_array($list)) $list = array($list); // convert to array if necessary
			foreach ($list AS $comp) $comps[$comp] = "'$comp'";
			$codes = implode(",", $comps);
		}
		
		// component codes found
		if ($codes) {
			$query = "SELECT procedure_type_id AS id, procedure_code AS component, description, name AS title FROM procedure_type ";
			$query .= "WHERE activity = 1 AND lab_id = ? AND procedure_type = 'ord' ";
			$query .= "AND procedure_code IN ( ".$codes." ) ";
			$query .= "GROUP BY procedure_code ORDER BY procedure_code ";
			$result = sqlStatement($query,array($lab_id));
		
			while ($profile = sqlFetchArray($result)) {
				$description = ($profile['description'])? $profile['description'] : $profile['title'];
				$newRow .= "<input class='wmtFullInput component' style='margin-top:5px' readonly unit='".$profile['component']."' value='".$profile['component']." - ".$description."'/>\n";
			}
		}
	}
	
	// add AOE questions if necessary
	$result = sqlStatement("SELECT aoe.procedure_code, aoe.question_code AS code, aoe.question_text, aoe.section, aoe.maxsize, aoe.fldtype, aoe.options, aoe.tips, answer, aoe.required FROM procedure_questions aoe ".
		"LEFT JOIN procedure_answers ans ON aoe.question_code = ans.question_code AND ans.procedure_order_id = ? AND ans.procedure_order_seq = ? AND ans.procedure_code = ? ".
		"WHERE aoe.lab_id = ? AND aoe.procedure_code = ? AND aoe.activity = 1 ORDER BY aoe.seq",
			array($order_item->procedure_order_id, $order_item->procedure_order_seq, $order_item->procedure_code, $lab_id, $order_item->procedure_code ));
		
	$aoe_count = 0;
	while ($aoe = sqlFetchArray($result)) {
		$test_code = $aoe['code'];
		$prompt = $aoe['tips'];
		$field = $aoe['fldtype'];
		$options = $aoe['options'];
		$question = str_replace(':','',$aoe['question_text']);
		if ($aoe['required'] == 1) $question = '*'.$question;
		
		if ($test_code) {
			$newRow .= "<input type='hidden' name='aoe".$key."_label[]' value='".$question."' />\n";
			$newRow .= "<input type='hidden' name='aoe".$key."_code[]' value='".$test_code."' />\n";
			$newRow .= "<input type='hidden' name='aoe".$key."_section[]' value='".$section."' />\n";
			$newRow .= "<div style='margin-top:5px'>".$question.": ";
			if ($field == 'L') {
				$newRow .= "<select name='aoe".$key."_text[]' title='".$prompt."' class='wmtFullInput aoe' value='' style='width:300px'>\n";
				$newRow .= "<option value='_blank' ";
				if (!$aoe['code']) $newRow .= " selected "; // if field value empty
				$newRow .= "> </option>\n";
				foreach ($aoe_options[$options] AS $option) {
					$newRow .= "<option value='".$option['option_id']."' ";
					if ($aoe['answer'] == $option['option_id']) $newRow .= " selected "; // field value = option value
					$newRow .= ">".$option['title']."</option>\n";
				}
				$newRow .= "</select>\n";
			}
			else {
				$newRow .= "<input name='aoe".$key."_text[]' title='".$prompt."' class='wmtFullInput aoe' value='".$aoe['answer']."' style='width:300px' />";
			}
			$newRow .= "</div>\n";
			
		}
	}
	
	$newRow .= "</td></tr>\n"; // finish up order row
}
	
// anything found
if ($newRow) {
	echo $newRow;
}
else { // create empty row
?>
										
										<tr id="orderEmptyRow">
											<td colspan="3">
												<b>NO PROFILES / TESTS SELECTED</b>
											</td>
										</tr>
<?php } ?>										
									</table>
									
									<hr style="border-color: #f0f0f0" />
						
									<table style="width:100%">
										<tr>
											<td>
												<label class="wmtLabel">Order Comments:</label>
												<textarea id="clinical_hx" name="clinical_hx" rows="2" class="wmtFullInput"><?php echo htmlspecialchars($order_data->clinical_hx) ?></textarea>	
											</td>
										</tr>
										<tr>
											<td>
												<label class="wmtLabel">Patient Instructions:</label>
												<textarea id="patient_instructions" name="patient_instructions" rows="2" class="wmtFullInput"><?php echo htmlspecialchars($order_data->patient_instructions) ?></textarea>	
											</td>
										</tr>
									</table>
								</fieldset>
							</td>
						</tr>
						
					</table>
				</div>
			</div>
			<!-- End Order Review -->
			
			<!-- Order Submission -->
			<div class="wmtMainContainer wmtColorMain" id="orderSubmission" style="width:99%;">
				<div class="wmtCollapseBar wmtColorBar" id="InfoCollapseBar" style="<?php if ($status != 'i') echo "border-bottom:none" ?>" onclick="togglePanel('InfoBox','InfoImageL','InfoImageR','InfoCollapseBar')">
					<table style="width:100%">
						<tr>
							<td style="text-align:left">
								<img id="InfoImageL" src="<?php echo $webroot;?>/library/wmt/fill-<?php echo ($status != 'i')? "270" : "090" ?>.png" border="0" alt="Show/Hide" title="Show/Hide" />
							</td>
							<td class="wmtChapter" style="text-align:center">
								Order Submission
							</td>
							<td style="text-align:right">
								<img id="InfoImageR" src="<?php echo $webroot;?>/library/wmt/fill-<?php echo ($status != 'i')? "270" : "090" ?>.png" border="0" alt="Show/Hide" title="Show/Hide" />
							</td>
						</tr>
					</table>
				</div>
				
				<div class="wmtCollapseBox" id="InfoBox" style="<?php if ($status != 'i') echo "display:none" ?>" >
					<table style="width:100%">
						<tr>
							<td style="width:50%">
								<table style="width:100%">
									<tr>
										<td class="wmtLabel" nowrap style="text-align:right">Order Date: </td>
										<td nowrap>
											<input class="wmtInput" type='text' size='10' name='date_ordered' id='date_ordered' 
												value='<?php echo $viewmode ? (!goodDate($order_data->date_ordered))? '' : date('Y-m-d',strtotime($order_data->date_ordered)) : date('Y-m-d'); ?>'
												title='<?php xl('yyyy-mm-dd Date of order','e'); ?>'
												onkeyup='datekeyup(this,mypcc)' onblur='dateblur(this,mypcc)' />
											<img src='../../pic/show_calendar.gif' align='absbottom' width='24' height='22'
												id='img_date_ordered' border='0' alt='[?]' style='cursor:pointer;cursor:hand;<?php if ($status != 'i') echo "display:none" ?>'
												title='<?php xl('Click here to choose a date','e'); ?>'>
										</td>
										
										<td class="wmtLabel" nowrap style="text-align:right">Physician: </td>
										<td>
											<select class="wmtSelect" name='provider_id' id='provider_id' style="min-width:150px;max-width:200px">
												<option value=''>-- select --</option>
<?php 
	$rlist= sqlStatement("SELECT * FROM users WHERE authorized=1 AND active=1 AND npi != '' ORDER BY lname");
	while ($rrow= sqlFetchArray($rlist)) {
    	echo "<option value='" . $rrow['id'] . "'";
		if ($order_data->provider_id == $rrow['id']) echo " selected";
		if (!$order_data->provider_id && $_SESSION['authUserID'] == $rrow['id']) echo " selected";
		echo ">" . $rrow['lname'].', '.$rrow['fname'].' '.$rrow['mname'];
    	echo "</option>";
  	}
?>
											</select>
										</td>
									</tr>

									<tr>		
										<td class="wmtLabel" nowrap style="text-align:right">Order Status: </td>
										<td nowrap>
											<input class="wmtInput" readonly style="width:150px" value="<?php echo ListLook($status, 'Lab_Form_Status') ?>" />
										</td>
<?php if ($lab_data['type'] == 'internal') { ?>
									</tr>
<?php } else { ?>
										<td class="wmtLabel" nowrap style="text-align:right">Account: </td>
										<td nowrap>
											<select class="wmtSelect" id="request_account" name="request_account" style="max-width:200px" />
<?php 
	$rlist= sqlStatement("SELECT * FROM list_options WHERE list_id = 'Lab_Accounts' ORDER BY seq");
	if (sqlNumRows($rlist) == 0) {
		echo "<option value='". $siteid ."' selected>".$siteid."</option>";
	}
	else {
		while ($rrow= sqlFetchArray($rlist)) {
	    	echo "<option value='" . $rrow['option_id'] . "'";
	    	if ($order_data->request_account == $rrow['option_id']) echo " selected";
			elseif (!$order_data->request_account && $rrow['is_default']) echo " selected";
			echo ">" . $rrow['title'];
	    	echo "</option>";
	    }
  	}
?>
											</select>
										</td>
									</tr>

<?php 
if ($GLOBALS['wmt::lab_ins_pick']) { // special processing for sfa ?>
									<tr>		
										<td class="wmtLabel" nowrap style="text-align:right">Billing Method: </td>
										<td colspan="3" nowrap>
											<input type="hidden" id="request_handling" name="request_handling" value=""/>
											<select class="wmtSelect" name="request_billing" id="request_billing" style="width:140px">
<?php 
	if (!$order_data->request_billing) echo "<option value=''>--select--</option>";
		
	$rlist= sqlStatement("SELECT * FROM list_options WHERE list_id = 'Lab_Billing' ORDER BY seq");
	while ($rrow = sqlFetchArray($rlist)) {
		if ($rrow['option_id'] == 'T') continue; // third-party not an option here
		echo "<option value='" . $rrow['option_id'] . "'";
		if ($order_data->request_billing == $rrow['option_id']) echo " selected";
		echo ">" . $rrow['title'] . "</option>";
  	}
	  	
	if ($status == 'i') { // still incomplete so they can change billing

	  	foreach ($ins_list AS $ins) {
	  		echo "<option value='" . $ins->id . "'";
	  		if ($order_data->request_billing == $ins->id) echo " selected";
	  		echo ">" . $ins->company_name . "</option>";
	  	}
	  	
	} elseif ( is_numeric($order_data->request_billing) ) { // order submitted, no edits allowed

		$ins = new wmtInsurance($order_data->request_billing);
		echo "<option value='" . $order_data->request_billing . "' selected >";
		echo ($ins->company_name) ? $ins->company_name : "INSURANCE MISSING"; 
		echo "</option>";
	
	}
?>
											</select>
										</td>
									</tr>
<?php 
} else { ?>

									<tr>		
										<td class="wmtLabel" nowrap style="text-align:right">Handling: </td>
										<td nowrap>
											<select class="wmtSelect" id="request_handling" name="request_handling" style="width:150px" />
												<option value=''></option>
<?php 
	$hlist= sqlStatement("SELECT * FROM list_options WHERE list_id = 'Lab_Handling' ORDER BY seq");
	while ($hrow= sqlFetchArray($hlist)) {
    	echo "<option value='" . $hrow['option_id'] . "'";
    	if ($order_data->request_handling == $hrow['option_id']) echo " selected";
		elseif (!$order_data->request_handling && $hrow['is_default']) echo " selected";
		echo ">" . $hrow['title'];
    	echo "</option>";
  	}
?>
											</select>
										</td>
									
										<td class="wmtLabel" nowrap style="text-align:right">Billing Method: </td>
										<td nowrap>
											<select class="wmtSelect" name="request_billing" id="request_billing" style="width:140px">
<?php 
	$iflag = false; // assume no insurance
	if (($order_data->ins_primary && $order_data->ins_primary != 'No Insurance') || 
		($order_data->ins_secondary && $order_data->ins_secondary != 'No Insurance') || 
			$ins_list[0]->company_name || $ins_list[1]->company_name) { // insurance available
				$iflag = true; // has insurance
				if (!$order_data->request_billing) $order_data->request_billing = 'T'; // third-party default
	}

	$rlist= sqlStatement("SELECT * FROM list_options WHERE list_id = 'Lab_Billing' ORDER BY seq");
	while ($rrow = sqlFetchArray($rlist)) {
		if ($rrow['option_id'] == 'T' && !$iflag) continue; // third-party not an option without insurance
		echo "<option value='" . $rrow['option_id'] . "'";
		if ($order_data->request_billing == $rrow['option_id']) echo " selected";
		elseif (!$order_data->request_billing && $rrow['is_default']) echo " selected";
		echo ">" . $rrow['title'] . "</option>";
  	}
?>
											</select>
										</td>
									</tr>
<?php 
	} // end internal 
} ?>
								</table>
							</td>
							
							<td>
								<table style="width:100%">
									<tr>
										<td class="wmtLabel" colspan="3">
											Clinic Notes:  <small style='font-weight:normal;padding-left:20px'>[ Not sent to lab or printed on requisition ]</small>
											<textarea name="order_notes" id="order_notes" class="wmtFullInput" rows="4"><?php echo htmlspecialchars($order_data->order_notes) ?></textarea>
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				</div>
			</div>
			<!-- End Order Submission -->
<!-- END OF ORDER -->
			
			
<!-- START OF RESULTS -->

			<!--  Internal Result Entry -->
			<div class="wmtMainContainer wmtColorMain" id="resultEntry" style="width:99%;<?php if ($status == 'i' || $lab_data['protocol'] != 'INT') echo "display:none" ?>">
				<div class="wmtCollapseBar wmtColorBar" id="InternalCollapseBar" onclick="togglePanel('InternalBox','InternalImageL','InternalImageR','InternalCollapseBar')">
					<table style="width:100%">	
						<tr>
							<td>
								<img id="InternalImageL" align="left" src="<?php echo $webroot;?>/library/wmt/fill-090.png" border="0" alt="Show/Hide" title="Show/Hide" />
							</td>
							<td class="wmtChapter" style="text-align: center">
								Internal Results
							</td>
							<td style="text-align: right">
								<img id="InternalImageR" src="<?php echo $webroot;?>/library/wmt/fill-090.png" border="0" alt="Show/Hide" title="Show/Hide" />
							</td>
						</tr>
					</table>
				</div>
				
				<div class="wmtCollapseBox" id="InternalBox">
					<table style="width:100%">
						<tr>
							<td>
								<fieldset>
									<legend>Observation Results - <?php echo $order_data->order_number ?></legend>
									<table style="width:100%;margin-bottom:12px">
										<tr>
											<td class="wmtHeader">
												LABORATORY PROCESSOR
											</td>
										</tr><tr>
											<td class="wmtOutput" style="font-weight:bold">
												<input type="hidden" name="lab_id" value="<?php echo $lab_id ?>" />
												<?php echo $lab_data['name'] ?>
											</td>
										</tr>
									</table>
											
									<table id="internal_table" style="width:100%">
										<tr>
											<th class="wmtHeader" style="width:70px;padding-left:18px">Actions</th>
											<th class="wmtHeader" style="width:100px">Profile / Test</th>
											<th class="wmtHeader">General Description</th>
										</tr>
<?php 									
/*	<a style="margin-left:20px" href="<?php echo $webroot;?>/library/wmt/edit_issue.php?type=medical_problem&pid=<?php echo $pid ?>&enc=<?php echo $_SESSION['encounter'] ?>" class="css_button_small link_submit iframe"><span>Add Item</span></a> */
	$newRow = '';
	if (!empty($item_list)) foreach ($item_list as $order_item) { // $item = array of objects
		if (!$order_item->procedure_code) continue;
		$key = $order_item->procedure_order_seq;
		if ($newRow) $newRow .= "<tr><td colspan='4'><hr style='border-color:#eee'/></td><tr>\n";
		$newRow .= "<tr id='test_".$key."'>";
		$newRow .= "<td style='vertical-align:top'><input type='button' class='wmtButton nolock inline' value='update' style='width:80px' href='#inline' key='".$key."' /><input type='hidden' name='result_key[]' value='".$key."' /></td>\n";
		$newRow .= "<td class='wmtLabel' style='vertical-align:top;padding-top:5px'><input name='result_code_".$key."' id='result_code_".$key."' class='wmtFullInput result_code' readonly value='".htmlspecialchars($order_item->procedure_code)."' style='font-weight:bold' /></td>\n";
		$newRow .= "<td colspan='2' class='wmtLabel' style='text-align:right;vertical-align:top;padding-top:5px'><input name='result_text_".$key."' id='result_text_".$key."' class='wmtFullInput result_text' readonly value='".$order_item->procedure_name."'/></td>\n";
		$newRow .= "</tr>\n"; // finish up order row

		/* loop through any result objects -- NOT IMPLEMENTED YET
		$details = false;
		for ($x = 1; $x < count($item); $x++) {
			$details = true;
			$result_item = $item[$x];			
			if ($x == 1) {
				$newRow .= "<tr><td></td><td class='wmtLabel' style='text-align:right;vertical-align:top;padding-top:5px'>Results: </td>";
				$newRow .= "<td><table><tr><th>Label</th><th>Value</th><th>Units</th><th>Abnormal</th><th>Range</th></tr>";
			}
			$newRow .= "<tr><td>".$result_item->result_title."</td>";
			$newRow .= "<td><input type='input' readonly class='wmtFullInput test_value' value='".$result_item->observation_value."' /></td>\n";
			$newRow .= "<td><input type='input' readonly class='wmtFullInput test_value' value='".$result_item->observation_value."' /></td>\n";
			$newRow .= "<td><input type='input' readonly class='wmtFullInput test_value' value='".$result_item->observation_value."' /></td>\n";
			$newRow .= "<td><input type='input' readonly class='wmtFullInput test_value' value='".$result_item->observation_value."' /></td>\n";
			$newRow .= "</tr>";
		}
		if ($details) $newRow .= "</table>"; 
		*/
		//
		$show = '';
		$result_item = wmtResult::fetchResult($order_data->order_number, $key);
		if (!$result_item->procedure_report_id) $show = 'display:none';
		$result_date = (goodDate($result_item->date_report))? date('Y-m-d',strtotime($result_item->date_report)): '';
		$newRow .= "<tr id='result_".$key."' style='".$show."'><td></td><td colspan='2' style='padding:0'><table>\n";
		$newRow .= "<tr><td class='wmtLabel' style='text-align:right;vertical-align:top;padding-top:5px;width:100px'>Comments: </td><td colspan='3'><textarea readonly class='wmtFullInput test_notes' id='result_notes_".$key."' name='result_notes_".$key."'>".$result_item->report_notes."</textarea></td></tr>\n";
		$newRow .= "<tr><td class='wmtLabel' style='text-align:right'>Status:</td><td><input readonly class='wmtInput test_status' id='result_sname_".$key."' value='".ListLook($result_item->report_status, 'proc_res_status')."' />";
		$newRow .= "<input type='hidden' id='result_status_".$key."' name='result_status_".$key."' value='".$result_item->report_status."' /></td>";
		$newRow .= "<td class='wmtLabel'>Performed: <input readonly class='wmtInput test_date' id='result_date_".$key."' name='result_date_".$key."' value='".$result_date."' /></td>";
		$newRow .= "<td class='wmtLabel'>Clinician: <input readonly class='wmtInput test_user' id='result_cname_".$key."' value='".UserIdLook($result_item->source)."' />\n";
		$newRow .= "<input type='hidden' id='result_clinician_".$key."' name='result_clinician_".$key."' value='".$result_item->source."' /></td></tr>\n";
		$newRow .= "</table></td></tr>\n";	
	}
	
	echo $newRow;
?>
									</table>
								</fieldset>
							</td>
						</tr>
					</table>
				</div>
			</div>
			<!-- End Internal Result Entry -->
	
			
			<!--  External Results -->
			<div class="wmtMainContainer wmtColorMain" id="resultEntry" style="width:99%;<?php if ($status == 'i' || $lab_data['protocol'] == 'INT') echo "display:none" ?>">
				<div class="wmtCollapseBar wmtColorBar" id="ExternalCollapseBar" onclick="togglePanel('ExternalBox','ExternalImageL','ExternalImageR','ExternalCollapseBar')">
					<table style="width:100%">	
						<tr>
							<td>
								<img id="ExternalImageL" align="left" src="<?php echo $webroot;?>/library/wmt/fill-090.png" border="0" alt="Show/Hide" title="Show/Hide" />
							</td>
							<td class="wmtChapter" style="text-align: center">
								External Results
							</td>
							<td style="text-align: right">
								<img id="ExternalImageR" src="<?php echo $webroot;?>/library/wmt/fill-090.png" border="0" alt="Show/Hide" title="Show/Hide" />
							</td>
						</tr>
					</table>
				</div>
				
				<div class="wmtCollapseBox" id="ExternalBox">
					<table style="width:100%">
						<tr>
							<td>
								<fieldset>
									<legend>Observation Results - <?php echo $order_data->order_number ?></legend>
									<table style="width:100%">
										<tr>
											<td class="wmtHeader">
												EXTERNAL PROCESSOR
											</td>
										</tr><tr>
											<td class="wmtOutput" style="font-weight:bold">
												<input type="hidden" name="lab_id" value="<?php echo $lab_id ?>" />
												<?php echo $lab_data['name'] ?>
											</td>
										</tr>
									</table>
<?php 
	if (goodDate($order_data->result_datetime)) { // results available
?>
									<hr style="border-color:#eee;margin-top:15px;margin-bottom:15px"/>
		
									<table id="sample_table" border="0" cellspacing="0" cellpadding="2" style="width:auto">
										<tr>
											<td colspan=7 class="wmtHeader" style="padding-bottom:10px">
												OBSERVATION SUMMARY
											</td>
										</tr>
<?php 
	if ($order_data->control_id) {
?>
										<tr>
											<td style="padding-bottom:10px;width:120px">
												<label class="wmtLabel" style="vertical-align:middle">Accession Number:</label>
											</td><td style="padding-bottom:10px" colspan='6'>
												<input type="text" class="wmtInput" readonly style="width:150px" value="<?php echo $order_data->control_id ?>" />
											</td>
										</tr>
<?php 
	}
?>										<tr>
											<td style='width:100px'>
												<label class="wmtLabel">Ordered Date: </label>
											</td><td>
												<input class="wmtInput" type='text' size='10' readonly value='<?php echo (!goodDate($order_data->date_ordered))? '' : date('Y-m-d',strtotime($order_data->date_ordered)); ?>' />
											</td>
											<td style='text-align:right;width:80px'>
												<label class="wmtLabel">Time: </label>
											</td><td>
												<input type="input" class="wmtInput" style="width:65px" readonly value='<?php echo (!goodDate($order_data->date_ordered))? '' : date('h:ia',strtotime($order_data->date_ordered)); ?>' />
											</td>
										</tr>
<?php if ($lab_data['type'] == 'laboratory') { ?>
										<tr>
											<td style='width:100px'>
												<label class="wmtLabel">Collection Date: </label>
											</td><td>
												<input class="wmtInput" type='text' size='10' readonly value='<?php echo (!goodDate($order_data->date_collected))? '' : date('Y-m-d',strtotime($order_data->date_collected)); ?>' />
											</td>
											<td style='text-align:right'>
												<label class="wmtLabel">Time: </label>
											</td><td>
												<input type="input" class="wmtInput" style="width:65px" readonly value='<?php echo (!goodDate($order_data->date_collected))? '' : date('h:ia',strtotime($order_data->date_collected)); ?>' />
											</td>
										</tr>
<?php } // end laboratory only ?>
										<tr>
											<td style='width:100px'>
												<label class="wmtLabel">Reported Date: </label>
											</td><td>
												<input class="wmtInput" type='text' size='10' readonly value='<?php echo (!goodDate($order_data->result_datetime))? '' : date('Y-m-d',strtotime($order_data->result_datetime)); ?>' />
											</td>
											<td style='text-align:right'>
												<label class="wmtLabel">Time: </label>
											</td><td>
												<input type="input" class="wmtInput" style="width:65px" readonly value='<?php echo (!goodDate($order_data->result_datetime))? '' : date('h:ia',strtotime($order_data->result_datetime)); ?>' />
											</td>
											<td style='text-align:right;width:120px'>
												<label class="wmtLabel">Status: </label>
											</td><td>
												<input type="input" class="wmtInput" style="width:150px" readonly value='<?php echo ListLook($order_data->status,'Lab_Form_Status'); ?>' />
											</td>
										</tr>
<?php if ($order_data->lab_notes) { ?>
										<tr>
											<td style='width:100px;vertical-align:top'>
												<label class="wmtLabel">Processor Comments: </label>
											</td><td colspan=5>
												<textarea class="wmtInput" style="width:100%" readonly rows=2><?php echo $order_data->lab_notes ?></textarea>
											</td>
										</tr>
<?php } ?>
									</table>
									
									<hr style="border-color:#eee;margin-top:15px;margin-bottom:15px"/>
																		
									<table id="result_table" style="min-width:900px;width:100%">
										<tr>
											<td colspan='10' class="wmtHeader">
												RESULT DETAIL INFORMATION
											</td>
										</tr>
<?php
		// loop through each ordered item
		$last_code = "FIRST";
		foreach ($item_list as $order_item) {
			$report_data = wmtResult::fetchResult($order_item->procedure_order_id, $order_item->procedure_order_seq);
			if (!$report_data) continue; // no results yet

			$reflex_data = '';
			if ($order_item->reflex_code) {
				$reflex_data = wmtResult::fetchReflex($report_data->procedure_order_id, $order_item->reflex_code, $order_item->reflex_set);
			}
?>
										<tr>
											<td colspan="10" class="wmtLabel" style="text-align:left;font-size:1.1em">
<?php 		if ($last_code != "FIRST") echo "<br/><br/>";
			echo $order_item->procedure_name;
			if ($order_item->reflex_code) echo "<br/>&nbsp;&nbsp;&nbsp;Reflex test triggered by: ".$reflex_data->result_code."&nbsp;&nbsp;".$reflex_data->result;
//			if ($report_data->date_report) echo " [".date('Y-m-d H:i:s',strtotime($report_data->date_report))."]";
			if ($report_data->report_status == 'Rejected') echo " [REJECTED]";
?>														
											</td>
										</tr>
<?php 
			$last_code = $order_item->procedure_code;
			
			if ($report_data->report_notes) {
?>
										<tr style="font-size:9px;font-weight:bold;">
											<td style="min-width:20px;width:20px">&nbsp;</td>
											<td colspan="9" style="text-align:left;width:85%">
												PROCESSOR COMMENTS
											</td>
										</tr>
										<tr style="font-weight:bold;font-family:monospace;font-size:11px" >
											<td>&nbsp;</td>
											<td colspan="9" class="wmtOutput" style="text-align:left;width:85%;padding-bottom:10px">
												<?php echo nl2br($report_data->report_notes) ?>
											</td>
										</tr>
<?php
	 		}

	 		$specimen_list = wmtSpecimenItem::fetchItemList($report_data->procedure_report_id);
			if ($specimen_list) {
?>					
										<tr><td colspan="10" style="padding:0 0 10px 0">
											<table>
												<tr style="font-size:9px;font-weight:bold">
													<td style="min-width:20px;width:20px">&nbsp;</td>
													<td style="text-align:left;width:14%">
														SPECIMEN
													</td>
													<td style="text-align:center;width:20%">
														SAMPLE COLLECTED
													</td>
													<td style="text-align:center;width:30%">
														SAMPLE RECEIVED
													</td>
													<td style="text-align:left;width:35%">
														ADDITIONAL INFORMATION
													</td>
												</tr>
<?php 
				foreach ($specimen_list AS $specimen_data) {
					// add in details as notes if necessary
					$notes = '';
					if (count($specimen_data->details) > 0) { // need to process details
						foreach ($specimen_data->details AS $detail) {
							// merge details into a single note field
							if ($notes) $notes .= "<br/>\n";
							$note = $detail->observation_id[1]; // text
							$obvalue = $detail->observation_value;
							if (is_array($obvalue)) $obvalue = $obvalue[0]; // save text portion
							$note .= ": " . $obvalue . " " . $detail->observation_units;
							$notes .= htmlentities($note);
						}
					}
?>					
												<tr style="font-family:monospace;font-size:11px" >
													<td>&nbsp;</td>
													<td class="wmtOutput" style="text-align:left;vertical-align:top">
														<?php echo $specimen_data->specimen_number ?>
													</td>
													<td class="wmtOutput" style="text-align:center;vertical-align:top">
														<?php echo $specimen_data->collected_datetime ?>
													</td>
													<td class="wmtOutput" style="text-align:center;vertical-align:top">
														<?php echo $specimen_data->received_datetime ?>
													</td>
													<td class="wmtOutput" style="text-align:left;vertical-align:top">
														Type: 
														<?php echo ($specimen_data->specimen_type) ? $specimen_data->specimen_type : "UNKNOWN"; ?>
														<?php if ($specimen_data->type_modifier) echo "<br/>Modifier: $specimen_data->type_modifier"; ?>		
														<?php if ($specimen_data->specimen_additive) echo "<br/>Additive: $specimen_data->specimen_additive"; ?>		
														<?php if ($specimen_data->collection_method) echo "<br/>Method: $specimen_data->collection_method"; ?>		
														<?php if ($specimen_data->source_site) {
															echo "<br/>Source: $specimen_data->source_site"; 
															if ($specimen_data->source_quantifier && $specimen_data->source_site != $specimen_data->source_quantifier) 
																echo "( $specimen_data->source_quantifier )"; }
														?>		
														<?php if ($specimen_data->specimen_volume) echo "<br/>Volume: $specimen_data->specimen_volume"; ?>		
														<?php if ($specimen_data->specimen_condition) echo "<br/>Condition: $specimen_data->specimen_condition"; ?>		
														<?php if ($specimen_data->specimen_rejected) echo "<br/>Rejected: $specimen_data->specimen_rejected"; ?>		
														<?php if ($notes) echo "<br/>$notes"; ?>		
													</td>	
												</tr>	
<?php
	 			} // end specimens
?>
						 					</table>
						 				</td></tr>
<?php 
			} // end if specimens
?>	
										
<?php 
			$result_list = wmtResultItem::fetchItemList($report_data->procedure_report_id);
			if (! $result_list) continue; // no details yet

			// process each observation
			$first = true;
			foreach ($result_list AS $result_data) {
				// collect facility information
				if ($result_data->facility && !$facility_list[$result_data->facility]) {
					$facility = sqlQuery("SELECT * FROM procedure_facility WHERE code = ?",array($result_data->facility));
					if ($facility) $facility_list[$facility['code']] = $facility;
				}
				
				// do we need a header?
				if ($first) { // changed test code
					$first = false;
?>
										<tr style="font-size:9px;font-weight:bold">
											<td style="min-width:20px;width:20px">&nbsp;</td>
<?php if ($lab_data['type'] == 'laboratory') { ?>
											<td>
												RESULT
											</td>
											<td>
												DESCRIPTION
											</td>
<?php } else { ?>
											<td colspan=2>
												RESULT DESCRIPTION
											</td>
<?php } ?>
											<td style="width:8%">
												<?php if ($lab_data['type'] == 'laboratory') echo "VALUE"; ?>
											</td>
											<td style="width:11%">
												<?php if ($lab_data['type'] == 'laboratory') echo "UNITS"; ?>
											</td>
											<td style="padding-left:10px;width:11%">
												<?php if ($lab_data['type'] == 'laboratory') echo "REFERENCE"; ?>
											</td>
											<td style="text-align:center;width:8%">
												<?php if ($lab_data['type'] == 'laboratory') echo "FLAG"; ?>
											</td>
											<td style="text-align:center;width:16%">
												OBSERVATION
											</td>
											<td style="text-align:center;width:7%">
												STATUS
											</td>
											<td style="text-align:center;width:5%">
												FACILITY
											</td>
											<td></td>
										</tr>
<?php 
					$last_code = $result_data->result_code;
				}
	
				$abnormal = $result_data->abnormal; // in case they sneak in a new status
				if ($result_data->abnormal == 'H') $abnormal = 'High';
				if ($result_data->abnormal == 'L') $abnormal = 'Low';
				if ($result_data->abnormal == 'HH') $abnormal = 'Alert High';
				if ($result_data->abnormal == 'LL') $abnormal = 'Alert Low';
				if ($result_data->abnormal == '>') $abnormal = 'Panic High';
				if ($result_data->abnormal == '<') $abnormal = 'Panic Low';
				if ($result_data->abnormal == 'A') $abnormal = 'Abnormal';
				if ($result_data->abnormal == 'AA') $abnormal = 'Critical';
				if ($result_data->abnormal == 'S') $abnormal = 'Susceptible';
				if ($result_data->abnormal == 'R') $abnormal = 'Resistant';
				if ($result_data->abnormal == 'I') $abnormal = 'Intermediate';
				if ($result_data->abnormal == 'NEG') $abnormal = 'Negative';
				if ($result_data->abnormal == 'POS') $abnormal = 'Positive';
?>
										<tr style="vertical-align:baseline;font-family:monospace;<?php if ($abnormal) echo 'font-weight:bold;color:#bb0000'?>">
											<td>&nbsp;</td>
<?php if ($lab_data['type'] == 'laboratory') { ?>
											<td>
												<?php echo $result_data->result_code ?>
											</td>
											<td>
												<?php echo $result_data->result_text ?>
											</td>
<?php } else { ?>
											<td colspan=2 style="white-space:nowrap">
												<?php echo ($result_data->result_data_type == 'RP')? $result_data->result_code : $result_data->result_text ?>
											</td>
<?php } ?>
<?php 
				if ($result_data->result_data_type) { // there is an observation
					if ($result_data->result_data_type == 'TX' && $lab_data['type'] != 'radiology' && $lab_data['npi'] != 'BBPL') { // put TEXT on next line
?>
										</tr><tr style="line-height:15px;vertical-align:baseline;<?php if ($abnormal) echo 'font-weight:bold;color:#bb0000' ?>">
											<td></td>
<?php 				
					} 
					if ($result_data->units || $result_data->range || $abnormal) {
?>
											<td style="font-family:monospace">
												<?php if ($result_data->result != "." && $result_data->result_data_type != 'FT') echo htmlspecialchars($result_data->result) ?>
											</td>
											<td style="font-family:monospace">
												<?php echo htmlspecialchars($result_data->units) ?>
											</td>
											<td style="font-family:monospace;padding-left:10px">
												<?php echo htmlspecialchars($result_data->range) ?>
											</td>
											<td style="font-family:monospace;text-align:center">
												<?php echo $abnormal ?>
											</td>
<?php 
					} else {
?>
											<td colspan='4' style="font-family:monospace;text-align:left">
												<?php if ($result_data->result != "." && $result_data->result_data_type != 'FT' && $lab_data['type'] != 'radiology') echo nl2br($result_data->result) ?>
												<?php if ($result_data->result_data_type == 'RP') echo '<a href="' . $result_data->result . '" target="_blank">IMAGE LINK</a>'; ?>
											</td>
<?php 
					}
?>
											<td style="font-family:monospace;text-align:center">
												<?php echo (!goodDate($result_data->date))? '' : date('Y-m-d H:i',strtotime($result_data->date)) ?>
											</td>
											<td style="font-family:monospace;text-align:center">
												<?php echo htmlspecialchars($result_data->result_status) ?>
											</td>
											<td style="font-family:monospace;text-align:center">
												<?php 	if (!$result_data->facility) 
															echo htmlspecialchars($lab_data['npi']);
														else
															echo htmlspecialchars($result_data->facility); ?>
											</td>
											<td></td>
										</tr>
<?php
					if ($result_data->result_data_type == 'FT') { // put formatted text below test line
?>
										<tr <?php if ($abnormal) echo 'style="font-weight:bold;color:#bb0000"'?>>
											<td colspan="1">&nbsp;</td>
											<td colspan="8" style="padding-left:150px;text-align:left;font-family:monospace;">
												<pre><?php echo str_replace('<br/>', '', $result_data->result); ?></pre>
												
											</td>
											<td></td>
										</tr>
<?php 
					} // end if formatted text
					
					if ($result_data->comments) { // put comments below test line
?>
										<tr <?php if ($abnormal) echo 'style="font-weight:bold;color:#bb0000"'?>>
											<td colspan="1">&nbsp;</td>
											<td colspan="8" style="padding-left:150px;text-align:left;font-family:monospace;">
												<pre><?php echo $result_data->comments; ?></pre>
												
											</td>
											<td></td>
										</tr>
<?php 
					} // end if comments
					
					if ($lab_data['type'] != 'laboratory' && $result_data->result_data_type != 'RP') { // put comments below test line
?>
										<tr <?php if ($abnormal) echo 'style="font-weight:bold;color:#bb0000"'?>>
											<td colspan="1">&nbsp;</td>
											<td colspan="8" style="padding-left:150px;text-align:left;font-family:monospace;">
												<?php echo nl2br($result_data->result) ?>
												
											</td>
											<td></td>
										</tr>
<?php 
					} // end if comments
				} // end if obser value
				else { 
?>
											<td colspan="6" style="padding-left:120px;text-align:left;font-family:monospace">
												<pre><?php echo $result_data->comments; ?></pre>
											</td>
											<td style="font-family:monospace;text-align:center;width:10%">
												<?php 	if (!$result_data->facility) 
															echo htmlspecialchars($lab_data['npi']);
														else
															echo htmlspecialchars($result_data->facility); ?>
											</td>
											<td></td>
										</tr>
<?php
				} // end if observ 
			} // end result foreach
		} // end foreach ordered item
		
		// do we need a facility box at all?
		if (count($facility_list) > 0) {
?>
										<tr><td colspan="10" style="padding:10px 0 0 0">
											<hr style="border-color:#eee;margin-top:15px;margin-bottom:15px"/>
											<table style="width:100%">
												<tr style="font-size:9px;font-weight:bold">
													<td style="min-width:20px;width:20px">&nbsp;</td>
													<td style="text-align:left;width:10%">
														FACILITY
													</td>
													<td style="width:25%">
														FACILITY TITLE
													</td>
													<td style="width:35%">
														CONTACT INFORMATION
													</td>
													<td style="width:20%">
														FACILITY DIRECTOR
													</td>
													<td></td>
												</tr>
<?php 
				foreach ($facility_list AS $facility_data) {
					if ($facility['phone']) {
						$phone = preg_replace('~.*(\d{3})[^\d]*(\d{3})[^\d]*(\d{4}).*~', '($1) $2-$3', $facility['phone']);
					}
					
					$director = $facility['director'];
					if ($facility['npi']) $director .= "<br/>NPI: ".$facility['npi']; // identifier

					$address = '';
					if ($facility['street']) $address .= $facility['street']."<br/>";
					if ($facility['street2']) $address .= $facility['street2']."<br/>";
					if ($facility['city']) $address .= $facility['city'].", ";
					$address .= $facility['state']."&nbsp;&nbsp;";
					if ($facility['zip'] > 5) $address .= preg_replace('~.*(\d{5})(\d{4}).*~', '$1-$2', $facility['zip']);
					else $address .= $facility['zip'];
?>					
												<tr style="font-family:monospace;vertical-align:baseline" >
													<td>&nbsp;</td>
													<td class="wmtOutput">
														<?php echo $facility['code'] ?>
													</td>
													<td class="wmtOutput">
														<?php echo $facility['name'] ?>
													</td>
													<td class="wmtOutput">
														<?php echo $address ?>
													</td>
													<td class="wmtOutput">
														<?php echo $director ?>
													</td>
												</tr>	
												<tr><td colspan="5">&nbsp;</td></tr>	
<?php
			} // end facility foreach
 		} // end facilities
?>
						 					</table>
						 				</td></tr>
<?php 		
	} // end if results
	else { 
?>
									<table>
										<tr><td style="font-weight:bold"><br/>NO RESULTS HAVE BEEN RECEIVED</td></tr>
									</table>
<?php 
	} // end result else
?>
									</table>
								</fieldset>
							</td>
						</tr>
					</table>
				</div>
			</div>
			<!-- End External Display -->
			
			<!--  Result Review -->
			<div class="wmtMainContainer wmtColorMain" id="resultReview" style="width:99%;<?php if ($status == 'i' || $status == 's') echo "display:none" ?>">
				<div class="wmtCollapseBar wmtColorBar" id="ResultCollapseBar" onclick="togglePanel('ResultBox','ResultImageL','ResultImageR','ResultCollapseBar')">
					<table style="width:100%">
						<tr>
							<td style="text-align:left">
								<img id="ResultImageL" src="<?php echo $webroot;?>/library/wmt/fill-090.png" border="0" alt="Show/Hide" title="Show/Hide" />
							</td>
							<td class="wmtChapter" style="text-align:center">
								Review Information
							</td>
							<td style="text-align:right">
								<img id="ResultImageR" src="<?php echo $webroot;?>/library/wmt/fill-090.png" border="0" alt="Show/Hide" title="Show/Hide" />
							</td>
						</tr>
					</table>
				</div>
				
				<div class="wmtCollapseBox" id="ResultBox">
					<table style="width:100%">
						<tr>
							<td style="width:50%">
								<table style="width:100%">
									<tr>
										<td class="wmtLabel" nowrap style="text-align:right">Reviewed By: </td>
										<td>
<?php if ($order_data->reviewed_id) { ?>
											<input type="hidden" name="reviewed_id" value="<?php echo $order_data->reviewed_id ?>"/>
											<input type="text" class="wmtInput nolock" style="min-width:200px" readonly 
<?php 
	$rrow= sqlQuery("SELECT * FROM users WHERE id = ?",array($order_data->reviewed_id));
	if ($rrow['lname']) echo 'value="' . $rrow['lname'].', '.$rrow['fname'].' '.$rrow['mname'] . '"'
?>
											/>
<?php } else { ?>
											<select class="wmtInput nolock" name='reviewed_id' id='reviewed_id' style="min-width:200px" onchange="$('#date_reviewed').val('<?php echo date('Y-m-d H:i') ?>')">
												<option value='_blank'>-- select --</option>
<?php 
	$rlist= sqlStatement("SELECT * FROM users WHERE authorized=1 AND active=1 AND npi != '' ORDER BY lname");
	while ($rrow= sqlFetchArray($rlist)) {
    	echo "<option value='" . $rrow['id'] . "'";
		if ($order_data->reviewed_id == $rrow['id']) echo " selected";
		echo ">" . $rrow['lname'].', '.$rrow['fname'].' '.$rrow['mname'];
    	echo "</option>";
  	}
?>
											</select>
<?php } ?>
										</td>
										<td class="wmtLabel" nowrap style="text-align:right">Reviewed Date: </td>
										<td nowrap>
											<input class="wmtInput nolock" type='text' size='16' name='reviewed_date' id='date_reviewed' readonly
												value='<?php echo (!goodDate($order_data->reviewed_datetime))? '' : date('Y-m-d H:i',strtotime($order_data->reviewed_datetime)); ?>' />
										</td>
									</tr>

									<tr>
										<td class="wmtLabel" nowrap style="text-align:right">Notified By: </td>
										<td>
<?php if ($order_data->notified_id) { ?>
											<input type="hidden" name="notified_id" value="<?php echo $order_data->notified_id ?>"/>
											<input type="text" class="wmtInput nolock" style="min-width:200px" readonly 
<?php 
	$rrow = sqlQuery("SELECT * FROM users WHERE id = ?",array($order_data->notified_id));
	if ($rrow['lname']) echo 'value="' . $rrow['lname'].', '.$rrow['fname'].' '.$rrow['mname'] . '"'
?>
											/>
<?php } else { ?>
											<select class="wmtInput nolock" name='notified_id' id='notified_id' style="min-width:200px" onchange="$('#date_notified').val('<?php echo date('Y-m-d H:i') ?>')">
												<option value='_blank'>-- select --</option>
<?php 
	$rlist= sqlStatement("SELECT * FROM users WHERE active=1 AND facility_id > 0 ORDER BY lname");
	while ($rrow= sqlFetchArray($rlist)) {
    	echo "<option value='" . $rrow['id'] . "'";
		if ($order_data->notified_id == $rrow['id']) echo " selected";
		echo ">" . $rrow['lname'].', '.$rrow['fname'].' '.$rrow['mname'];
    	echo "</option>";
  	}
?>
											</select>
<?php } ?>
										</td>
										<td class="wmtLabel" nowrap style="text-align:right">Notified Date: </td>
										<td nowrap>
											<input class="wmtInput nolock" type='text' size='16' name='notified_date' id='date_notified' readonly 
												value='<?php echo (!goodDate($order_data->notified_datetime))? '' : date('Y-m-d H:i',strtotime($order_data->notified_datetime)); ?>' />
										</td>
									</tr>
									<tr>
										<td class="wmtLabel" nowrap style="text-align:right">Person Contacted: </td>
										<td>
											<input type='text' id='notified_person' name='notified_person' class="wmtFullInput" value="<?php echo $order_data->notified_person ?>" 
<?php if ($order_data->notified_id) echo "readonly" ?>
											/>
										</td>
										<td colspan="2" class="wmtLabel" nowrap style="text-align:center">
<?php if ($GLOBALS['wmt::portal_enable'] == 'true') {?>										
										Release to Patient Portal:&nbsp;
											<input type='checkbox' id='portal_flag' name='portal_flag' class="wmtCheck" value="1" <?php if ($order_data->portal_flag) echo 'checked' ?> />
<?php } ?>
										</td>
									</tr>
									
								</table>
							</td>
							
							<td>
								<table style="width:100%">
									<tr>
										<td class="wmtLabel" colspan="3">
											Review Notes: 
											<textarea name="review_notes" id="review_notes" class="wmtFullInput nolock" rows="4"
<?php if ($order_data->reviewed_id) echo "readonly" ?>
											><?php echo htmlspecialchars($order_data->review_notes) ?></textarea>
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				</div>
			</div>
			<!-- End Result Review -->

<!-- END RESULTS -->

			<br/>

			<!-- Start of Buttons -->
			<table style="width:99%">

<?php if ( ($lab_data['npi'] == 'BBPL' || $lab_data['npi'] == 'BIOREF') && $viewmode && $order_data->status != 'i') { ?>
				<tr>
					<td class="wmtLabel" colspan="4" style="padding-bottom:10px;padding-left:8px">
						Label Printer: 
						<select class="nolock" id="labeler" name="labeler" style="margin-right:10px">
							<?php getLabelers($_SERVER['REMOTE_ADDR'])?>
						</select>
						Quantity:
						<select class="nolock" name="count" style="margin-right:10px">
							<option value="1"> 1 </option>
							<option value="2"> 2 </option>
							<option value="3"> 3 </option>
							<option value="4"> 4 </option>
							<option value="5"> 5 </option>
						</select>

						<input class="nolock" type="button" tabindex="-1" onclick="printLabels(0)" value="Print Labels" />

					</td>
				</tr>
<?php } ?>

				<tr>
					<td class="wmtLabel" style="vertical-align:top;float:left;width:80px">
						<a class="css_button" tabindex="-1" href="javascript:saveClicked()"><span>Save Work</span></a>
					</td>
<?php if ($status == 'i') { ?>	
					<td class="wmtLabel" style="vertical-align:top;float:left">
						<a class="css_button" tabindex="-1" href="javascript:submitClicked()"><span>Submit Order</span></a>
					</td>
<?php } elseif ($viewmode && ($lab_data['npi'] == '1548208440' || $lab_data['npi'] == 'INTERPATH')) { ?>
				<td class="wmtLabel" style="padding-left:8px;width:130px">
					Label Quantity:
					<select class="nolock" name="count" style="margin-right:5px">
						<option value="1"> 1 </option>
						<option value="2"> 2 </option>
						<option value="3"> 3 </option>
						<option value="4"> 4 </option>
						<option value="5"> 5 </option>
					</select>
				</td><td>
					<a class="css_button nolock" tabindex="-1" href="javascript:printLabels(0)"><span>Print Labels</span></a>
				</td>
<?php } ?>
					<td class="wmtLabel">
						<a class="css_button" tabindex="-1" href="javascript:printClicked()"><span>Printable Form</span></a>
					</td>
<?php if ($order_data->order_abn_id) { ?>
					<td class="wmtLabel">
						<a class="css_button" tabindex="-1" href="<?php echo $document_url . $order_data->order_abn_id ?>"><span>ABN Document</span></a>
					</td>
<?php } if ($order_data->order_req_id) { ?>
					<td class="wmtLabel">
						<a class="css_button" tabindex="-1" href="<?php echo $document_url . $order_data->order_req_id ?>"><span>Order Document</span></a>
					</td>
<?php } if ($order_data->result_doc_id) { ?>
					<td class="wmtLabel">
						<a class="css_button" tabindex="-1" href="<?php echo $document_url . $order_data->result_doc_id ?>"><span>Result Document</span></a>
					</td>
<?php } ?>
					<td class="wmtLabel" style="vertical-align:top;float:right">
<?php if (!$locked) { ?>
						<a class="css_button" tabindex="-1" href="javascript:doClose()"><span>Don't Save</span></a>
<?php } else { ?>
						<a class="css_button" tabindex="-1" href="javascript:doClose()"><span>Cancel</span></a>
<?php } ?>
					</td>
				</tr>
			</table>
			<!-- End of Buttons -->
			
			<input type="hidden" name="status" value="<?php echo ($order_data->status)?$order_data->status:'i' ?>" />
			<input type="hidden" name="priority" value="<?php echo ($order_data->priority)?$order_data->priority:'n' ?>" />
			
		<!--  MODAL WINDOW DEFINITION  -->
			<div style="display:none">
				<div id="inline" class="bgcolor2" style="padding:10px;height:260px" >
					<table style="margin:10px auto;width:100%;">
						<tr>
							<td class="wmtLabel" colspan="4" style="font-size:16px;">
								<u>Add / Edit Internal Results</u>
								<input type='hidden' id='edit_key' value='' />
							</td>
						</tr>
						<tr style="height:10px"><td colspan="4"></td></tr>
						<tr>
							<td class="wmtLabel" style="width:80px">Test:</td>
							<td class="wmtData" style="width:100px">
								<input  style="width:100px;background:transparent;" class="wmtInput" name="edit_code" id="edit_code" value="" readonly />
							</td>
							<td class="wmtLabel">Description:</td>
							<td colspan='3' class="wmtData" style="width:100%">
								<input style="background:transparent;" class="wmtFullInput" name="edit_text" id="edit_text" value="" readonly />
							</td>
						</tr>
						<tr>
							<td class="wmtLabel" style="width:80px">Status:</td>
							<td class="wmtData" style="width:30%">
								<select class="wmtInput" name="edit_status" id="edit_status">
									<?php echo ListSel('','proc_res_status') ?>
								</select>
							</td>
							<td class="wmtLabel">Performed:</td>
							<td class="wmtData" style="width:30%;white-space:nowrap">
								<input class="wmtInput" type='text' size='10' name='edit_date' id='edit_date'
									value='' onkeyup='datekeyup(this,mypcc)' onblur='dateblur(this,mypcc)'
									title='<?php echo xla('yyyy-mm-dd date order processed'); ?>' />
								<img src='<?php echo $GLOBALS['webroot'] ?>/interface/pic/show_calendar.gif' align='absbottom' width='24' height='22'
									id='img_edit_date' border='0' alt='[?]' style='cursor:pointer'
		    						title='<?php echo xla('Click here to choose a date'); ?>' />
							</td>
							<td class="wmtLabel" style="width:80px">Clinician:</td>
							<td class="wmtData" style="width:30%;padding-right:0">
								<select class="wmtSelect" name='edit_clinician' id='edit_clinician' style="min-width:160px;max-width:160px">
									<option value='_blank'>-- select --</option>
<?php 
	$rlist= sqlStatement("SELECT * FROM users WHERE active=1 AND facility_id > 0 ORDER BY lname");
	while ($rrow= sqlFetchArray($rlist)) {
    	echo "<option value='" . $rrow['id'] . "'";
		echo ">" . $rrow['lname'].', '.$rrow['fname'].' '.$rrow['mname'];
    	echo "</option>";
  	}
?>
								</select>
							</td>
						</tr>

						<tr>
							<td class="wmtLabel" colspan="6">
								Result Description:<br/>
								<textarea class="wmtFullInput" rows="4" name="edit_data" id="edit_data" style="resize:none"></textarea>
							</td>
						</tr>
					</table>
				
					<center>
						<input type='button' name='result_save' value='<?php echo xla('Save'); ?>' onclick='saveResult()' />
<?php if ($issue && acl_check('admin', 'super')) { ?>
						&nbsp;
						<input type='button' value='<?php echo xla('Delete'); ?>' style='color:red' onclick='deleteme()' />
<?php } ?>
						&nbsp;
						<input type='button' value='<?php echo xla('Cancel'); ?>' onclick='$.fancybox.close();' />
					</center>
					
				</div>
			</div>	
		<!--  END OF MODAL WINDOW  -->
			
			
		</form>
		
	</body>

	<script>
		/* required for popup calendar */
		Calendar.setup({inputField:"edit_date", ifFormat:"%Y-%m-%d", button:"img_edit_date"});
		Calendar.setup({inputField:"date_ordered", ifFormat:"%Y-%m-%d", button:"img_date_ordered"});
<?php if ($lab_data['type'] == 'laboratory') { ?>
		Calendar.setup({inputField:"date_pending", ifFormat:"%Y-%m-%d", button:"img_date_pending"});
		Calendar.setup({inputField:"date_collected", ifFormat:"%Y-%m-%d", button:"img_date_collected"});
<?php } ?>
	</script>

</html>
