<?php
require_once("verify_session.php");
include_once("{$GLOBALS['srcdir']}/sql.inc");
include_once("{$GLOBALS['srcdir']}/wmt/wmt.class.php");
include_once("{$GLOBALS['srcdir']}/wmt/wmt.report.php");
require_once("{$GLOBALS['srcdir']}/wmt/wmt.include.php");

$patientid              = trim($_REQUEST['patientid']);
$pc_eid                 = $_REQUEST['pc_eid'];
$sql = sqlStatement("SELECT form_id FROM forms WHERE pid = $patientid AND encounter = $pc_eid AND formdir = 'laboratory'");
$sqlLab = sqlFetchArray($sql);

function labcorp_report($pid, $encounter, $cols, $id) {
	$form_name = 'laboratory';
	$form_table = 'form_order';

	/* RETRIEVE FORM DATA */
	try {
		$order_data = new wmtOrder($form_name, $id);
		$pat_data = wmtPatient::getPidPatient($pid);
		$enc_data = wmtEncounter::getEncounter($encounter);
		$ins_list = wmtInsurance::getPidInsDate($pid,$order_data->date_ordered);
	
		$lab_id = ($order_data->lab_id) ? $order_data->lab_id : $_REQUEST['lab_id'];
		$lab_data = sqlQuery("SELECT * FROM procedure_providers WHERE ppid = ?",array($lab_id));
		$item_list = wmtOrderItem::fetchItemList($order_data->order_number);
			
		$aoe_list = array();
		if ($order_data->procedure_order_id) {
			$query = "SELECT procedure_code, question_code, answer FROM procedure_answers WHERE procedure_order_id = ? ORDER BY procedure_code, answer_seq";
			$result = sqlStatement($query,array($order_data->procedure_order_id)); // labcorp stores by order not item
	
			$code = '';
			$aoe_items = array();
			while ($row = sqlFetchArray($result)) {
				if ($code && $code != $row['procedure_code']) {
					$aoe_list[$code] = $aoe_items;
					$aoe_items = array();
				}
				$aoe_items[$row['question_code']] = $row['answer'];
				$code = $row['procedure_code'];
			}
			if ($aoe_items && $code) $aoe_list[$code] = $aoe_items;
		}
	}
	catch (Exception $e) {
		die ("FATAL ERROR ENCOUNTERED: " . $e->getMessage());
		exit;
	}

	// Custom style information
	print '<link rel="stylesheet" type="text/css" href="'.$GLOBALS['webroot'].'/library/wmt/wmt.default.css" />';
	
	// Report outter frame
	print "\n\n<div class='wmtReport'>\n";
	print "<table class='wmtFrame' cellspacing='0' cellpadding='3'>\n";

	// Status header
	$content = "";
	$status = 'Incomplete';
	if ($order_data->status) $status = ListLook($order_data->status, 'Lab_Form_Status');
	$content .= "<tr><td colspan='4'>\n";
	$content .= "<table class='wmtStatus' style='margin-bottom:10px'><tr>";
	$content .= "<td class='wmtLabel' style='width:50px;min-width:50px'>Status:</td>";
	$content .= "<td class='wmtOutput' style='white-space:nowrap'>" . $status . "</td>";
	$content .= "</tr></table></td></tr>\n";
	if ($content) print $content;
	
	// Order summary
	$content = "<tr><td style='width:140px'></td><td style='width:250px'></td><td style='width:100px'></td><td></td></tr>\n";
	$ordered = date('Y-m-d',strtotime($order_data->date_ordered));
	$processed = date('Y-m-d h:i A',strtotime($order_data->date_transmitted));
	if (strpos($processed, '1969-12-31') !== false) $processed = date('Y-m-d h:i A',strtotime($order_data->date_ordered));
	$content .= do_columns($ordered,'Order Date',$processed,'Processed Date');
	$content .= do_columns($order_data->order_number,'Requisition',$lab_data['name'],'Processing Vendor');
	$ordby = UserIdLook($order_data->provider_id);
	if (!$ordby) $ordby = "UNKNOWN";
	$content .= do_columns($ordby,'Ordering Provider',$order_data->request_account,'Billing Account');
	$entby = UserLook($order_data->user);
	if ($ordby == "UNKNOWN" || $ordby == $entby) $entby = "";
	
	// SFA SPECIFIC
	if ($GLOBALS['wmt::lab_ins_pick']) {
		$billing = ListLook($order_data->request_handling, 'Lab_Billing');
		if ( ($billing == '' || $billing == '*Not Found*') && is_numeric($order_data->request_billing) ) {
			$ins = new wmtInsurance($order_data->request_billing);
			$billing = ($ins['name']) ? $ins['name'] : "INSURANCE MISSING";
		}
		$content .= do_columns($entby,'Entering Clinician',$billing,'Billing Method');
	} else {
		$content .= do_columns($entby,'Entering Clinician',ListLook($order_data->request_handling,'Lab_Handling'),'Special Handling');
	}
	$notes = ($order_data->order_notes)? "<div style='white-space:pre-wrap'>".$order_data->order_notes."</div>" : "";
	$content .= do_line($notes,'Clinic Notes');
	do_section($content, 'Order Summary');
	
	// Loop through diagnosis
	$content = "<tr><td style='width:140px'></td><td style='width:80px'></td><td style='width:100px'></td><td></td></tr>\n";
	$diag_array = array();
	if ($order_data->diagnoses) {
		$diag_array = explode("|", $order_data->diagnoses); // code & text

		foreach ($diag_array AS $diag) {
			list($code,$text) = explode("^", $diag);
			if (empty($code)) continue;
			if (strpos($code,":") !== false)	
				list($dx_type,$dx_code) = explode(":", $code);
	
			if (!$dx_type) $dx_type = 'ICD9';
	 
			$content .= do_columns($dx_code, $dx_type.' Code',$text, 'Description');
		}	
	
		do_section($content, 'Order Diagnosis');
	}
	
	/* Order specimen
	$content = "<td style='width:120px'></td><td style='width:40px'></td><td style='width:120px'></td><td></td>\n";
	$collected = ($order_data->order_datetime)?date('Y-m-d h:i A',strtotime($order_data->order_datetime)):null;
	$pending = ($order_data->order_pending)?date('Y-m-d h:i A',strtotime($order_data->order_pending)):null;
	
	if ($order_data->order_psc) {
		$content .= do_line('Yes','PSC Hold Order');
	}
	else {
		$content .= do_columns('Yes','Sample Collected',$collected,'Collection Date');
		$content .= do_columns(ListLook($order_data->order_fasting,'LabCorp_Yes_No'),'Patient Fasting',$order_data->order_volume,'Specimen Volume');
	}
	$content .= do_break();
	*/
	
	// loop through requisitions
	$content = "<tr><td style='width:140px'></td><td style='width:80px'></td><td style='width:100px'></td><td></td></tr>\n";
	foreach ($item_list AS $order_item) {
		$need_blank = false;
		
		// Test section
		$type = ($order_item->procedure_type == 'pro')? "Profile " : "Test ";
		$content .= do_columns($order_item->procedure_code,$type.'Code',$order_item->procedure_name,'Description');

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
				$query .= "WHERE activity = 1 AND lab_id = ".$lab_id." AND procedure_type = 'ord' ";
				$query .= "AND procedure_code IN ( ".$codes." ) ";
				$query .= "GROUP BY procedure_code ORDER BY procedure_code ";
				$result = sqlStatement($query);
		
				while ($profile = sqlFetchArray($result)) {
					$description = ($profile['description'])? $profile['description'] : $profile['title'];
					$content .= do_columns("","",$profile['component']." - ".$description,"Component",true);
					$need_blank = true;
				}
			}
		}

		// add AOE questions & answers if necessary
		$query = "SELECT pc.*, pq.question_code AS field, pq.question_text, pq.fldtype, pq.options, pq.tips, pq.section, pa.answer_seq, pa.answer FROM procedure_order_code pc ";
		$query .= "LEFT JOIN procedure_questions pq ON pq.lab_id = ? AND pc.procedure_code = pq.procedure_code ";
		$query .= "LEFT JOIN procedure_answers pa ON pa.question_code = pq.question_code AND pa.procedure_order_id = pc.procedure_order_id AND pa.procedure_order_seq = pc.procedure_order_seq ";
		$query .= "WHERE pc.procedure_order_id = ? AND pc.procedure_order_seq = ? AND pq.activity = 1 AND pa.answer != '' ";
		$query .= "ORDER BY pa.procedure_order_id, pa.procedure_order_seq, pa.answer_seq";
		$params = array($lab_id, $order_item->procedure_order_id, $order_item->procedure_order_seq);
		$result = sqlStatement($query,$params);
			
		$aoe_out = '';
		while ($aoe = sqlFetchArray($result)) {
			$question = str_replace(':','',$aoe['question_text']);
			if ($question) {
				$aoe_out .= "<tr><td class='wmtLabel' style='width:350px;white-space:nowrap'>".$question.": </td>\n";
//				$aoe_out .= "<td class='wmtOutput' style='white-space:nowrap'>".$aoe['answer']."</td></tr>\n";
				$answer = $aoe['answer'];
				if ($aoe['fldtype'] == 'L') // need list lookup
					$answer = ListLook($answer,$aoe['options']);
				$aoe_out .= "<td class='wmtOutput' style='white-space:nowrap'>".$answer."</td></tr>\n";
				$need_blank = true;
			}
		}
		if ($aoe_out) {
			$content .= "<tr><td></td><td colspan=3><table>$aoe_out</table></td></tr>";
//			$content .= do_columns('','','<table>'.$aoe_out.'</table>','',true);
		}

		if ($need_blank) $content .= do_blank(); // skip first time
	}
	// lab notes
	if ($order_data->clinical_hx || $order_data->patient_instructions)
		$content .= do_break();
	
	if ($order_data->clinical_hx) {
		$content .= "<tr><td class='wmtLabel'>Order Comments: </td><td class='wmtOutput' colspan='3' style='white-space:pre-wrap'>".$order_data->clinical_hx."</td></tr>";
	}
	
	// patient instructions
	if ($order_data->patient_instructions) {
		$content .= "<tr><td class='wmtLabel'>Patient Instructions: </td><td class='wmtOutput' colspan='3' style='white-space:pre-wrap'>".$order_data->patient_instructions."</td></tr>";
	}
	
	do_section($content, 'Order Requisition - '.$order_data->order_number);

	// loop through observations
	if ($order_data->status != 'i' && $order_data->status != 's' && $order_data->status != 'p' ) { // skip until we have a result
?>
		
 		
<?php 
		$content = "<tr><td style='width:140px'></td><td style='width:200px'></td><td style='width:100px'></td><td></td></tr>\n";
		if ($order_data->reviewed_id) {
			$content .= do_columns(UserIdLook($order_data->reviewed_id),'Reviewing Provider',date('Y-m-d',strtotime($order_data->reviewed_datetime)),'Reviewed Date');
		}
		if ($order_data->notified_id || $order_data->portal_flag) {
			$portal = ($order_data->portal_flag)? 'YES' : 'NO';
			$content .= do_columns(UserIdLook($order_data->notified_id),'Notification By',date('Y-m-d',strtotime($order_data->notified_datetime)),'Notified Date');
			$content .= do_columns($order_data->notified_person, 'Person Notified',$portal, 'Portal Release');
		}
		$notes = ($order_data->review_notes)? "<div style='white-space:pre-wrap'>".htmlspecialchars_decode($order_data->review_notes)."</div>" : "";
		//$content .= do_line($notes,'Review Notes');
	
		//do_section($content, 'Review Information');
	
	} // end results
?>
<?php 
	print "</table> <!-- frame -->\n</div> <!-- report -->";
	
}
    
    $sql = sqlStatement("SELECT * FROM tbl_allcare_patientcheckout WHERE pid=".$patientid." AND eid=".$pc_eid);
    $sqlRows = sqlFetchArray($sql);
    
    $sql = sqlStatement("select notes from list_options where list_id='AllcareDriveSync' and option_id = 'email'");
    $sqlFetch = sqlFetchArray($sql);
    $syncEmail = $sqlFetch['notes'];
    
    $sql = sqlStatement("select patient_folder from patient_data where pid=".$patientid);
    $sqlFetch = sqlFetchArray($sql);
    $pfolder = $sqlFetch['patient_folder'];
    
    $get_visit_details = sqlStatement("SELECT pc_eventDate,pc_catid,pc_facility,pc_billing_location,pc_aid FROM openemr_postcalendar_events WHERE pc_eid = '$pc_eid' AND pc_pid = '$patientid'");
    while($set_visit_details = sqlFetchArray($get_visit_details)):
        // New encounter Creation
        $dos                = $set_visit_details['pc_eventDate'];
        $visit_category     = $set_visit_details['pc_catid'];
        $facility           = $set_visit_details['pc_facility'];
        $billing_facility   = $set_visit_details['pc_billing_location'];
        $rendering_provider = trim($set_visit_details['pc_aid']);

        $getfacilityname = sqlStatement("SELECT name FROM facility where id = $facility");
        $facility_name = '';
        if(!empty($getfacilityname)){
            while($setfacilityname = sqlFetchArray($getfacilityname)){
                $facility_name = $setfacilityname['name'];
            }
        }
        $encquery  = sqlStatement("SELECT encounter FROM form_encounter WHERE date='$dos' AND facility_id='$facility' AND pc_catid = '$visit_category' AND billing_facility='$billing_facility' AND rendering_provider='$rendering_provider'");
        $encFetch = sqlFetchArray($encquery);
        $encounter = $encFetch['encounter'];
    endwhile;  
    
       $sql = sqlStatement("SELECT * FROM patient_data WHERE pid = ".$patientid);
       $sqlArr = sqlFetchArray($sql);
       
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">  
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
  <style type="text/css">

    * { margin: 0; padding: 0; }

    html { height: 100%; font-size: 62.5% }
    body {-webkit-print-color-adjust: exact;color-adjust: exact;}
    body { height: 100%; background-color: #FFFFFF; font: 1.2em Verdana, Arial, Helvetica, sans-serif; }


    /* ==================== Form style sheet ==================== */

    form { 
           margin: 25px 0 0 0;
           width: 100%; 
           padding-bottom: 30px; 
    }

    fieldset { margin: 0 0 22px 0; border: 1px solid #095D92; padding: 12px 17px; background-color: #DFF3FF; }
    legend { font-size: 1.1em; background-color: #095D92; color: #FFFFFF; font-weight: bold; padding: 4px 8px; }
    label { display: block; width: auto; margin: 0 0 10px 0; }
    label.spam-protection { display: inline; width: auto; margin: 0; }

    input.inp-text, textarea, input.choose, input.answer { border: 1px solid #909090; padding: 3px; }
    input.inp-text { width: 100%; margin: 0 0 8px 0; }
    textarea { width: 400px; height: 150px; margin: 0 0 12px 0; display: block; }

    input.choose { margin: 0 2px 0 0; }
    input.answer { width: 40px; margin: 0 0 0 10px; }
    input.submit-button { font: 1.4em Georgia, "Times New Roman", Times, serif; letter-spacing: 1px; display: block; margin: 23px 0 0 0; }

    form br { display: none; }
    .biglabel{font-size:15px;}
    .ajax-file-upload-container{margin: 0; padding: 0;}
    .borderhr,header{display: none;}
    @media print {
        @page {
            margin: 0.4in 0px 0.4in 0px;
            size:8.27in 11.69in; 
        }
        fieldset {
            page-break-inside: avoid;
            margin:15px .5in;
        }
        .logo{
            float: left;
        }
        .headeraddress{
            float:right;
            font-size: 1.2em;
            color:#0073d2;
        }
        .borderhr,header{display: block;}
        header{
            padding-bottom: 20px;
            margin-bottom: 10px;
        }
        hr{
            clear: both;
            display: block;
            height: 2px;
            border: 0;
            border-top: 2px solid #0067ab;
            margin: 3em 0;
            padding: 0; 
        }
        
    }

    /* ==================== Form style sheet END ==================== */

    </style>
    <!--[if IE]>
    <style type="text/css">

    /* ==================== Form style sheet for IE ==================== */

    fieldset { padding: 22px 17px 12px 17px; position: relative; margin: 12px 0 34px 0; }
    legend { position: absolute; top: -12px; left: 10px; }
    label.float { margin: 5px 0 0 0; }
    label { margin: 0 0 5px 0; }
    label.spam-protection { display: inline; width: auto; position: relative; top: -3px; }
    input.choose { border: 0; margin: 0; }
    input.submit-button { margin: -10px 0 0 0; }

    /* ==================== Form style sheet for IE end ==================== */

    </style>
    <![endif]-->
  
</head>
<body>

<div class="container">
    <?php
    if($_SERVER['SERVER_NAME'] == 'allcare.dfwprimary.com'):
    ?>
    <header>
        <div class="logo"><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAaIAAACVCAIAAACVYWmZAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAA+tpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuNi1jMDY3IDc5LjE1Nzc0NywgMjAxNS8wMy8zMC0yMzo0MDo0MiAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczpkYz0iaHR0cDovL3B1cmwub3JnL2RjL2VsZW1lbnRzLzEuMS8iIHhtbG5zOnhtcE1NPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvbW0vIiB4bWxuczpzdFJlZj0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL3NUeXBlL1Jlc291cmNlUmVmIyIgeG1wOkNyZWF0b3JUb29sPSJBZG9iZSBQaG90b3Nob3AgQ0MgMjAxNSAoV2luZG93cykiIHhtcDpDcmVhdGVEYXRlPSIyMDE3LTA1LTMxVDE5OjQ0OjAzKzA1OjMwIiB4bXA6TW9kaWZ5RGF0ZT0iMjAxNy0wNS0zMVQxOTo0NDoyNCswNTozMCIgeG1wOk1ldGFkYXRhRGF0ZT0iMjAxNy0wNS0zMVQxOTo0NDoyNCswNTozMCIgZGM6Zm9ybWF0PSJpbWFnZS9wbmciIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6NzMxMTI0Q0M0NjBCMTFFN0FBQzNBMjJCNkMyMTU5REYiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6NzMxMTI0Q0Q0NjBCMTFFN0FBQzNBMjJCNkMyMTU5REYiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RS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" width="250px"></div>
        <div class="headeraddress" style="color:#0073d2 !important;">2925 Skyway Circle North,<br/>Irving, Texas, 75038.<br/>Tel: 972 639 5838<br/>Fax: 972 791 8211<br/>Email: Office@dfwprimary.com</div>
    </header>
    <hr class="borderhr">
    <?php
    endif;
    ?>
                    <fieldset style="clear: both">
			<legend>Patient Information:</legend>
                        <div class="row" style="margin-left:15px; margin-bottom: 8px;">
                            <div class="col-sm-4" style="display: inline"><div style="display: inline-block"><strong>Patient Name:&nbsp;</strong></div><div style="display: inline-block"><?php echo $sqlArr['fname']; ?>&nbsp;<?php echo $sqlArr['lname']; ?></div></div>
                        </div>
                        <div class="row" style="margin-left:15px; margin-bottom: 8px;">
                            <div class="col-sm-12" style="display: inline"><div style="display: inline-block"><strong>Date of Birth:&nbsp;</strong></div><div style="display: inline-block"><?php echo $sqlArr['DOB']; ?></div></div>
                        </div>
                        <div class="row" style="margin-left:15px; margin-bottom: 8px;">
                            <div class="col-sm-12" style="display: inline"><div style="display: inline-block"><strong>E-mail:&nbsp;</strong></div><div style="display: inline-block"><?php echo $sqlArr['email']; ?></div></div>
                        </div>
                        <div class="row" style="margin-left:15px; margin-bottom: 8px;">
                            <div class="col-sm-12"><div style="float: left"><strong>Address:&nbsp;</strong></div><div style="float:left;">
                                <?php echo $sqlArr['street']; ?><br />
                                <?php echo $sqlArr['street_addr']; ?><br />
                                    <?php echo $sqlArr['city']; ?><br />
                                        <?php echo $sqlArr['state']; ?><br />
                                            <?php echo $sqlArr['postal_code']; ?><br />
                                                <?php echo $sqlArr['country_code']; ?></div>
                            </div>
                        </div>
                        <div class="row" style="margin-left:15px; margin-bottom: 8px;">
                            <div class="col-sm-12" style="display: inline"><div style="display: inline-block"><strong>Phone Number:&nbsp;</strong></div><div style="display: inline-block"><?php echo $sqlArr['phone_cell']; ?></div></div>
                        </div>
		</fieldset>
		<!-- ============================== Fieldset 2 ============================== -->
		<fieldset>
			<legend>Educational Handouts:</legend>
                        <?php
                            $cancerArr = explode(",",$sqlRows['cancer']);
                            $diabetesArr = explode(",",$sqlRows['diabetes']);
                            $healthwillnessArr = explode(",",$sqlRows['health_willness']);
                            $healthyagingArr = explode(",",$sqlRows['healthy_aging']);
                            $healthylivingArr = explode(",",$sqlRows['healthy_living']);
                            $hearthealthstrokeArr = explode(",",$sqlRows['heart_health_stroke']);
                            $hospitalsafetyArr = explode(",",$sqlRows['hospital_safety']);
                            $medicationsworkArr = explode(",",$sqlRows['medications_work']);
                            $medicationeducationArr = explode(",",$sqlRows['medication_education']);
                            $mentalhealthArr = explode(",",$sqlRows['mental_health']);
                            $orthopedicsArr = explode(",",$sqlRows['orthopedics']);
                            $patientsafetyArr = explode(",",$sqlRows['patient_safety']);
                            $respiratoryArr = explode(",",$sqlRows['respiratory']);
                            $careathomeArr = explode(",",$sqlRows['care_at_home']);
                            
                        ?>
                        <?php if($sqlRows['cancer'] != ""): ?>
                        <div class="row" style="margin-left:15px; margin-bottom: 8px;">
                            <div class="col-sm-12" style="display: inline;" ><div style="float:left;"><strong>Cancer:&nbsp;</strong></div><div style="float:left;">
                                    <?php
                                        foreach ($cancerArr as $value) {
                                            echo $value."<br/>";
                                        }
                                    ?>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                        <?php if($sqlRows['diabetes'] != ""): ?>
                        <div class="row" style="margin-left:15px; margin-bottom: 8px;">
                            <div class="col-sm-12" style="display: inline"><div style="float: left;"><strong>Diabetes:&nbsp;</strong></div><div style="float: left;">
                                <?php 
                                        foreach ($diabetesArr as $value) {
                                            echo $value."<br/>";
                                        }
                                 ?>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                        <?php if($sqlRows['health_willness'] != ""): ?>
                        <div class="row" style="margin-left:15px; margin-bottom: 8px;">    
                            <div class="col-sm-12" style="display: inline"><div style="float: left;"><strong>Health and Wellness:&nbsp;</strong></div><div style="float: left;">
                                <?php 
                                        foreach ($healthwillnessArr as $value) {
                                            echo $value."<br/>";
                                        }
                                ?>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                        <?php if($sqlRows['healthy_aging'] != ""): ?>
                        <div class="row" style="margin-left:15px; margin-bottom: 8px;">    
                            <div class="col-sm-12" style="display: inline"><div style="float: left;"><strong>Healthy Aging:&nbsp;</strong></div><div style="float: left;">
                                <?php 
                                        foreach ($healthyagingArr as $value) {
                                            echo $value."<br/>";
                                        } ?>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                        <?php if($sqlRows['healthy_living'] != ""): ?>
                        <div class="row" style="margin-left:15px; margin-bottom: 8px;">    
                            <div class="col-sm-12" style="display: inline"><div style="float: left;"><strong>Healthy Living:&nbsp;</strong></div><div style="float: left;">
                                <?php 
                                        foreach ($healthylivingArr as $value) {
                                            echo $value."<br/>";
                                        } 
                                ?>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                        <?php if($sqlRows['heart_health_stroke'] != ""): ?>
                        <div class="row" style="margin-left:15px; margin-bottom: 8px;">    
                            <div class="col-sm-12" style="display: inline"><div style="float: left;"><strong>Heart Health & Stroke:&nbsp;</strong></div><div style="float: left;">
                                <?php 
                                        foreach ($hearthealthstrokeArr as $value) {
                                            echo $value."<br/>";
                                        } 
                                ?>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                        <?php if($sqlRows['hospital_safety'] != ""): ?>
                        <div class="row" style="margin-left:15px; margin-bottom: 8px;">    
                            <div class="col-sm-12" style="display: inline"><div style="float: left;"><strong>Hospital Safety:&nbsp;</strong></div><div style="float: left;">
                                <?php 
                                        foreach ($hospitalsafetyArr as $value) {
                                            echo $value."<br/>";
                                        }  
                                ?>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                        <?php if($sqlRows['medications_work'] != ""): ?>
                        <div class="row" style="margin-left:15px; margin-bottom: 8px;">    
                            <div class="col-sm-12" style="display: inline"><div style="float: left;"><strong>How Medications Work:&nbsp;</strong></div><div style="float: left;">
                                <?php 
                                        foreach ($medicationsworkArr as $value) {
                                            echo $value."<br/>";
                                        } 
                                ?>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                        <?php if($sqlRows['medication_education'] != ""): ?>
                        <div class="row" style="margin-left:15px; margin-bottom: 8px;">    
                            <div class="col-sm-12" style="display: inline"><div style="float: left;"><strong>Medication Education:&nbsp;</strong></div><div style="float: left;">
                                <?php 
                                        foreach ($medicationeducationArr as $value) {
                                            echo $value."<br/>";
                                        }  
                                ?>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                        <?php if($sqlRows['mental_health'] != ""): ?>
                        <div class="row" style="margin-left:15px; margin-bottom: 8px;">    
                            <div class="col-sm-12" style="display: inline"><div style="float: left;"><strong>Mental Health:&nbsp;</strong></div><div style="float: left;">
                                <?php 
                                        foreach ($mentalhealthArr as $value) {
                                            echo $value."<br/>";
                                        } 
                                ?>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                        <?php if($sqlRows['orthopedics'] != ""): ?>
                        <div class="row" style="margin-left:15px; margin-bottom: 8px;">    
                            <div class="col-sm-12" style="display: inline"><div style="float: left;"><strong>Orthopedics:&nbsp;</strong></div><div style="float: left;">
                                <?php 
                                        foreach ($orthopedicsArr as $value) {
                                            echo $value."<br/>";
                                        }  
                                ?>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                        <?php if($sqlRows['patient_safety'] != ""): ?>
                        <div class="row" style="margin-left:15px; margin-bottom: 8px;">    
                            <div class="col-sm-12" style="display: inline"><div style="float: left;"><strong>Patient Safety:&nbsp;</strong></div><div style="float: left;">
                                <?php 
                                        foreach ($patientsafetyArr as $value) {
                                            echo $value."<br/>";
                                        } 
                                    ?>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                        <?php if($sqlRows['respiratory'] != ""): ?>
                        <div class="row" style="margin-left:15px; margin-bottom: 8px;">    
                            <div class="col-sm-12" style="display: inline"><div style="float: left;"><strong>Respiratory:&nbsp;</strong></div><div style="float: left;">
                                <?php 
                                        foreach ($respiratoryArr as $value) {
                                            echo $value."<br/>";
                                        }  
                                ?>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                        <?php if($sqlRows['care_at_home'] != ""): ?>
                        <div class="row" style="margin-left:15px; margin-bottom: 8px;">    
                            <div class="col-sm-12" style="display: inline"><div style="float: left;"><strong>Your Care at Home:&nbsp;</strong></div><div style="float: left;">
                                <?php 
                                        foreach ($careathomeArr as $value) {
                                            echo $value."<br/>";
                                        }  
                                ?>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                        <?php if($sqlRows['prescription'] != ""): ?>
                        <div class="row" style="margin-left:15px; margin-bottom: 8px;">    
                            <div class="col-sm-12" style="display: inline"><div style="display: inline-block"><strong>Prescription:&nbsp;</strong></div><div style="display: inline-block"><?php echo $sqlRows['prescription']; ?></div></div>
                        </div>
                        <?php endif; ?>
                </fieldset>
                <fieldset>
			<legend>Procedure:</legend>
                        <?php
                            if($sqlLab['form_id'] != ""):
                                ?>
                                <div class="col-sm-12" style="display: inline"><div style="display: inline-block"><strong>Lab Orders:&nbsp;</strong></div></div>
                                <div class="row" style="margin-left:15px; margin-bottom: 8px;">   
                                <?php
                                    labcorp_report($patientid,$pc_eid,"",$sqlLab['form_id']);
                                ?>
                                </div>   
                                <?php    
                            endif;
                        ?>
                        <?php
                            if($sqlRows['ordersentto'] != ""):
                        ?>
                        <label class="biglabel">Radiology Order:</label>        
                        <div class="row" style="margin-left:15px; margin-bottom: 8px;">    
                            <div class="col-sm-12" style="display: inline"><div style="display: inline-block"><strong>Reason for Exam:&nbsp;</strong></div><div style="display: inline-block"><?php echo $sqlRows['examreason']; ?></div></div>
                        </div>
                        <div class="row" style="margin-left:15px; margin-bottom: 8px;">  
                            <div class="col-sm-12" style="display: inline"><div style="display: inline-block"><strong>Type of Exam:&nbsp;</strong></div><div style="display: inline-block"><?php echo $sqlRows['examtype']; ?></div></div>
                        </div>
                        <div class="row" style="margin-left:15px; margin-bottom: 8px;">      
                            <div class="col-sm-12" style="display: inline"><div style="display: inline-block"><strong>Order sent to:&nbsp;</strong></div><div style="display: inline-block"><?php echo $sqlRows['ordersentto']; ?></div></div>
                        </div> 
                        <?php
                            endif;
                        ?>
                        <?php
                            if($sqlRows['doctorreferredto'] != ""):
                        ?>
                        <label class="biglabel">Referral Order:</label>        
                        <div class="row" style="margin-left:15px; margin-bottom: 8px;">    
                            <div class="col-sm-12" style="display: inline"><div style="display: inline-block"><strong>Reason for Referral:&nbsp;</strong></div><div style="display: inline-block"><?php echo $sqlRows['referralreason']; ?></div></div>
                        </div>
                        <div class="row" style="margin-left:15px; margin-bottom: 8px;">  
                            <div class="col-sm-12"><div style="float: left"><strong>Referred to Doctor:&nbsp;</strong></div><div style="float: left;">
                                <?php 
                                        foreach (explode(",",$sqlRows['doctorreferredto']) as $value) {
                                            echo $value."<br/>";
                                        }; ?>
                                </div>
                            </div>
                        </div>
                        <?php
                            endif;
                        ?>
               </fieldset>
                <fieldset>
                        <div class="row" style="margin-left:15px; margin-bottom: 8px;">    
                            <div class="col-sm-12" style="display: inline"><div style="display: inline-block"><strong>Future Appointment:&nbsp;</strong></div><div style="display: inline-block"><?php echo $sqlArr['openAppdate']; ?></div></div>
                        </div>
                        <div class="row" style="margin-left:15px; margin-bottom: 8px;">    
                            <div class="col-sm-12" style="display: inline"><div style="display: inline-block"><strong>Patient Balance:&nbsp;</strong></div><div style="display: inline-block"><?php echo $sqlRows['patientbal']; ?></div></div>
                        </div>
                        <div class="row" style="margin-left:15px; margin-bottom: 8px;">    
                            <div class="col-sm-12" style="display: inline"><div style="display: inline-block"><strong>Verbal Instructions:&nbsp;</strong></div><div style="display: inline-block"><?php echo $sqlRows['verbalins']; ?></div></div>
                        </div>
                        <div class="row" style="margin-left:15px; margin-bottom: 8px;">    
                            <div class="col-sm-12" style="display: inline"><div style="display: inline-block"><strong>Review Summary:&nbsp;</strong></div><div style="display: inline-block"><?php echo $sqlRows['reviewsummary']; ?></div></div>
                        </div>
                        <div class="row" style="margin-left:15px; margin-bottom: 8px;">    
                            <div class="col-sm-12" style="display: inline"><div style="display: inline-block"><strong>Customer service survey:&nbsp;</strong></div><div style="display: inline-block"><?php echo $sqlRows['csssurvey'].". ". $sqlRows['csssurveycomments']; ?></div></div>
                        </div>
                        <div class="row" style="margin-left:15px; margin-bottom: 8px;">
                            <div class="col-sm-12" style="display: inline"><div style="display: inline-block"><strong>Check-Out Time:&nbsp;</strong></div><div style="display: inline-block"><?php echo date('Y-m-d H:i:s'); ?></div></div>
                        </div>
               </fieldset>
		<!-- ============================== Fieldset 3 end ============================== -->
                <input type="hidden" id="patientid" name="patientid" value="<?php echo $patientid; ?>" />
                <input type="hidden" id="pc_eid" name="pc_eid" value="<?php echo $pc_eid; ?>" />
                <input type="hidden" id="excuseid" name="excuseid" value="<?php echo $sqlRows['excuseletter']; ?>" />
</div>
<script type="text/javascript">    
window.print();
</script>
</body>
</html>

