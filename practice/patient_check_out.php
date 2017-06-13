<?php
require_once("verify_session.php");
include_once("{$GLOBALS['srcdir']}/sql.inc");
include_once("{$GLOBALS['srcdir']}/wmt/wmt.class.php");
include_once("{$GLOBALS['srcdir']}/wmt/wmt.report.php");
require_once("{$GLOBALS['srcdir']}/wmt/wmt.include.php");

$patientid              = trim($_REQUEST['patientid']) !='' ? trim($_REQUEST['patientid']) : $_REQUEST['pid'];
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
    
    $checkouttime = $_POST['input-outtime'];
    $eduhandouts = implode(",",$_POST['eduoption']);
    $prescription = mysql_real_escape_string($_POST['prescription']);
    $excuseletter = $_POST['excuseid'];
    $futureappointment = $_POST['input-appointment'];
    $patientbal = $_POST['input-patientblance'];
    $verbalins = mysql_real_escape_string($_POST['vebalins']);
    $reviewsummary = mysql_real_escape_string($_POST['reviewsummary']);
    $csssurvey = $_POST['customerss'];
    $feedback = mysql_real_escape_string($_POST['feedback']);
    
    $cancer = implode(",",$_POST['cancer']);
    $diabetes = implode(",",$_POST['diabetes']);
    $health_willness = implode(",",$_POST['health_willness']);
    $healthy_aging = implode(",",$_POST['healthy_aging']);
    $healthy_living = implode(",",$_POST['healthy_living']);
    $heart_health_stroke = implode(",",$_POST['heart_health_stroke']);
    $hospital_safety = implode(",",$_POST['hospital_safety']);
    $medications_work = implode(",",$_POST['medications_work']);
    $medication_education = implode(",",$_POST['medication_education']);
    $mental_health = implode(",",$_POST['mental_health']);
    $orthopedics = implode(",",$_POST['orthopedics']);
    $patient_safety = implode(",",$_POST['patient_safety']);
    $respiratory = implode(",",$_POST['respiratory']);
    $care_at_home = implode(",",$_POST['care_at_home']);
    
    $radiologyTxt = mysql_real_escape_string($_POST['radiologyTxt']);
    $radiologyTxtArea = mysql_real_escape_string($_POST['radiologyTxtArea']);
    $referralTxt = mysql_real_escape_string($_POST['referralTxt']);
    $referreddoctorAddrId = mysql_real_escape_string($_POST['doctorname']);
    $referralTxtArea = mysql_real_escape_string($_POST['referralTxtArea']);
    $examtype = mysql_real_escape_string($_POST['examtype']);
    
    $sql = sqlStatement("SELECT * FROM tbl_allcare_patientcheckout WHERE pid=".$patientid." AND eid=".$pc_eid);
    
    if(sqlNumRows($sql) > 0):
        if(!empty($_POST)):
            sqlStatement("UPDATE tbl_allcare_patientcheckout SET checkouttime='".$checkouttime."', cancer='".$cancer."',diabetes='".$diabetes."',
                        health_willness='".$health_willness."',healthy_aging='".mysql_real_escape_string($healthy_aging)."',healthy_living='".$healthy_living."',
                            heart_health_stroke='".$heart_health_stroke."',hospital_safety='".$hospital_safety."',medications_work='".$medications_work."',
                                medication_education='".$medication_education."',mental_health='".$mental_health."',orthopedics='".$orthopedics."',
                                    patient_safety='".$patient_safety."',respiratory='".$respiratory."',care_at_home='".$care_at_home."', prescription='".$prescription."',
                         excuseletter='".$excuseletter."',examreason='".$radiologyTxt."',examtype='".$examtype."',ordersentto='".$radiologyTxtArea."',referralreason='".$referralTxt."',
                             referreddoctorAddrId='".$referreddoctorAddrId."',doctorreferredto='".$referralTxtArea."',futureappointment='".$futureappointment."', patientbal='".$patientbal."',
                         verbalins='".$verbalins."', reviewsummary='".$reviewsummary."', csssurvey='".$csssurvey."',csssurveycomments='".$feedback."' WHERE 
                            pid=".$patientid." AND eid=".$pc_eid);
        endif;
    else:
        $healthy_aging = mysql_real_escape_string($healthy_aging);
        sqlStatement("INSERT INTO tbl_allcare_patientcheckout (pid,eid,checkouttime,cancer,diabetes,health_willness,healthy_aging,healthy_living,
                    heart_health_stroke,hospital_safety,medications_work,medication_education,mental_health,orthopedics,patient_safety,
                        respiratory,care_at_home,prescription,excuseletter,examreason,examtype,ordersentto,referralreason,referreddoctorAddrId,doctorreferredto,futureappointment,patientbal,verbalins,reviewsummary,csssurvey,csssurveycomments) VALUES
                      ($patientid,$pc_eid,'$checkouttime','$cancer','$diabetes','$health_willness','$healthy_aging','$healthy_living',
                       '$heart_health_stroke','$hospital_safety','$medications_work','$medication_education','$mental_health',
                       '$orthopedics','$patient_safety','$respiratory','$care_at_home','$prescription','$excuseletter','$radiologyTxt','$examtype','$radiologyTxtArea','$referralTxt',
                           '$referreddoctorAddrId','$referralTxtArea','$futureappointment','$patientbal',
                       '$verbalins','$reviewsummary','$csssurvey','$feedback')");
    endif;
    
    // Prepare to add educational handouts in Plan note
    $planNote = "";
    if(count($_POST['cancer']) > 0):
        $planNote .= "Cancer: ";
        foreach ($_POST['cancer'] as $value):
            $planNote .= $value.", ";
        endforeach;
        $planNote .= "; ";
    endif;  
    if(count($_POST['diabetes']) > 0):
        $planNote .= "Diabetes: ";
        foreach ($_POST['diabetes'] as $value):
            $planNote .= $value.", ";
        endforeach;
        $planNote .= "; ";
    endif;
    if(count($_POST['health_willness']) > 0):
        $planNote .= "Health and Wellness: ";
        foreach ($_POST['health_willness'] as $value):
            $planNote .= $value.", ";
        endforeach;
        $planNote .= "; ";
    endif;
    if(count($_POST['healthy_aging']) > 0):
        $planNote .= "Healthy Aging: ";
        foreach ($_POST['healthy_aging'] as $value):
            $planNote .= $value.", ";
        endforeach;
        $planNote .= "; ";
    endif;
    if(count($_POST['healthy_living']) > 0):
        $planNote .= "Healthy Living: ";
        foreach ($_POST['healthy_living'] as $value):
            $planNote .= $value.", ";
        endforeach;
        $planNote .= "; ";
    endif;
    if(count($_POST['heart_health_stroke']) > 0):
        $planNote .= "Heart Health & Stroke: ";
        foreach ($_POST['heart_health_stroke'] as $value):
            $planNote .= $value.", ";
        endforeach;
        $planNote .= "; ";
    endif;
    if(count($_POST['hospital_safety']) > 0):
        $planNote .= "Hospital Safety: ";
        foreach ($_POST['hospital_safety'] as $value):
            $planNote .= $value.", ";
        endforeach;
        $planNote .= "; ";
    endif;
    if(count($_POST['medications_work']) > 0):
        $planNote .= "How Medications Work: ";
        foreach ($_POST['medications_work'] as $value):
            $planNote .= $value.", ";
        endforeach;
        $planNote .= "; ";
    endif;
    if(count($_POST['medication_education']) > 0):
        $planNote .= "Medication Education: ";
        foreach ($_POST['medication_education'] as $value):
            $planNote .= $value.", ";
        endforeach;
        $planNote .= "; ";
    endif;
    if(count($_POST['mental_health']) > 0):
        $planNote .= "Mental Health: ";
        foreach ($_POST['mental_health'] as $value):
            $planNote .= $value.", ";
        endforeach;
        $planNote .= "; ";
    endif;
    if(count($_POST['orthopedics']) > 0):
        $planNote .= "Orthopedics: ";
        foreach ($_POST['orthopedics'] as $value):
            $planNote .= $value.", ";
        endforeach;
        $planNote .= "; ";
    endif;
    if(count($_POST['patient_safety']) > 0):
        $planNote .= "Patient Safety: ";
        foreach ($_POST['patient_safety'] as $value):
            $planNote .= $value.", ";
        endforeach;
        $planNote .= "; ";
    endif;
    if(count($_POST['respiratory']) > 0):
        $planNote .= "Respiratory: ";
        foreach ($_POST['respiratory'] as $value):
            $planNote .= $value.", ";
        endforeach;
        $planNote .= "; ";
    endif;
    if(count($_POST['care_at_home']) > 0):
        $planNote .= "Your Care at Home: ";
        foreach ($_POST['care_at_home'] as $value):
            $planNote .= $value.", ";
        endforeach;
    endif;
    
    $planNote .= $prescription;
    // check if there is any formid for this encounter
    $sql = sqlStatement("SELECT form_id 
            FROM  `forms` 
            WHERE form_name =  'Allcare Encounter Forms'
            AND formdir =  'LBF2'
            AND encounter =$pc_eid
            AND pid =$patientid ORDER BY form_id DESC LIMIT 1" );
    if(sqlNumRows($sql) > 0):
        $sqlFormRow = sqlFetchArray($sql);
        $allCareFormId = $sqlFormRow['form_id']; // AllCareEncounter Form Id
        // Check if there is plan note already for this AllCareEncounter Form
        $sqlPlan = sqlStatement("SELECT * FROM lbf_data WHERE form_id = $allCareFormId AND field_id = 'plan_note_text'");
        if($planNote != ""):
            if(sqlNumRows($sqlPlan) > 0):
                sqlStatement("UPDATE lbf_data SET  `field_value` = ? WHERE  field_id= ? AND form_id = ?",array($planNote,'plan_note_text',$allCareFormId));
            else:
                sqlInsert("INSERT INTO lbf_data (form_id,field_id,field_value) VALUES (?,?,?)", array($allCareFormId,'plan_note_text',$planNote));
            endif;
        endif;
    else:
        $sql_form=sqlStatement("select max(form_id)as new_form from forms where form_name='Allcare Encounter Forms' AND formdir='LBF2'");
        $row_form=sqlFetchArray($sql_form);
        $new_fid= $row_form['new_form'];
        $new_id1=++$new_fid;
        //echo "INSERT INTO  forms (date,encounter,form_name,form_id,pid,user,groupname,authorized,deleted,formdir) VALUES (NOW(),$ecounter1,'Allcare Encounter Forms',$new_id1,$pid1,'$_SESSION[authUser]','default',1,0,'LBF2')";
        $ins_form=sqlStatement("INSERT INTO  forms (date,encounter,form_name,form_id,pid,user,groupname,authorized,deleted,formdir) VALUES (NOW(),$pc_eid,'Allcare Encounter Forms',$new_id1,$patientid,'$_SESSION[authUser]','default',1,0,'LBF2')");
        if($planNote != ""):
            sqlInsert("INSERT INTO lbf_data (form_id,field_id,field_value) VALUES (?,?,?)", array($new_id1,'plan_note_text',$planNote));
        endif;
    endif;
    
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
  <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.0/themes/smoothness/jquery-ui.css">
  <link rel="stylesheet" media="all" type="text/css" href="css/jquery-ui-timepicker-addon.min.css" />
  <link rel="stylesheet" media="all" type="text/css" href="drive_view/driveassets/css/uploadfile.css" />
  <link rel="stylesheet" href="drive_view/driveassets/css/lity.css"/>
  <link rel="stylesheet" href="css/easy-responsive-tabs.css"/>
  <link rel="stylesheet" type="text/css" href="insurance/assets/skins/all.css">
  <link rel="stylesheet" href="./../library/customselect/css/select2.css"/>
  <link rel="stylesheet" href="./../library/customselect/css/select2-bootstrap.css"/>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js" integrity="sha256-T0Vest3yCU7pafRw9r+settMBX6JkKN06dqBnpQ8d30=" crossorigin="anonymous"></script>
  <script type="text/javascript" src="js/jquery-ui-timepicker-addon.min.js"></script>
  <script type="text/javascript" src="js/jquery-ui-sliderAccess.js"></script>
  <script type="text/javascript" src="drive_view/driveassets/js/jquery.uploadfile.js"></script>
  <script src="drive_view/driveassets/js/lity.js"></script>
  <script src="js/easy-responsive-tabs.js"></script>
  <script src="insurance/assets/js/icheck.min.js"></script>
  <script src="./../library/customselect/js/select2.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
  <style type="text/css">

    * { margin: 0; padding: 0; }

    html { height: 100%; font-size: 62.5% }

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
    .lity-iframe-container iframe{
        height: 450px;
    }
    #loader{
            background: rgba(0,0,0,0.56);
            border-radius: 4px;
            display:table;
            height: 48px;
            width: 266px;
            color: #fff;
            position:fixed;
            left: 0px;
            top:0px;
            bottom: 0px;
            right: 0px;
            margin: auto;
            display: none;
        }
        .ajax-spinner-bars {
            height: 48px;
            left: 23px;
            position: relative;
            top: 20px;
            width: 35px;
            display: table-cell;
         }
         #loadertitle {
            display: table-cell;
            font-size: 17px;
            padding-left: 14px;
            vertical-align: middle;
         }

        .ajax-spinner-bars > div {
            position: absolute;
            width: 2px;
            height: 8px;
            background-color: #fff;
            opacity: 0.05;
            animation: fadeit 0.8s linear infinite;
        }
        .ajax-spinner-bars > .bar-1 {
            transform: rotate(0deg) translate(0, -12px);
            animation-delay:0.05s;
        }
        .ajax-spinner-bars > .bar-2 {
            transform: rotate(22.5deg) translate(0, -12px);
            animation-delay:0.1s;
        }
        .ajax-spinner-bars > .bar-3 {
            transform: rotate(45deg) translate(0, -12px);
            animation-delay:0.15s;
        }
        .ajax-spinner-bars > .bar-4 {
            transform: rotate(67.5deg) translate(0, -12px);
            animation-delay:0.2s;
        }
        .ajax-spinner-bars > .bar-5 {
            transform: rotate(90deg) translate(0, -12px);
            animation-delay:0.25s;
        }
        .ajax-spinner-bars > .bar-6 {
            transform: rotate(112.5deg) translate(0, -12px);
            animation-delay:0.3s;
        }
        .ajax-spinner-bars > .bar-7 {
            transform: rotate(135deg) translate(0, -12px);
            animation-delay:0.35s;
        }
        .ajax-spinner-bars > .bar-8 {
            transform: rotate(157.5deg) translate(0, -12px);
            animation-delay:0.4s;
        }
        .ajax-spinner-bars > .bar-9 {
            transform: rotate(180deg) translate(0, -12px);
            animation-delay:0.45s;
        }
        .ajax-spinner-bars > .bar-10 {
            transform: rotate(202.5deg) translate(0, -12px);
            animation-delay:0.5s;
        }
        .ajax-spinner-bars > .bar-11 {
            transform: rotate(225deg) translate(0, -12px);
            animation-delay:0.55s;
        }
        .ajax-spinner-bars > .bar-12 {
            transform: rotate(247.5deg) translate(0, -12px);
            animation-delay:0.6s;
        }
        .ajax-spinner-bars> .bar-13 {
            transform: rotate(270deg) translate(0, -12px);
            animation-delay:0.65s;
        }
        .ajax-spinner-bars > .bar-14 {
            transform: rotate(292.5deg) translate(0, -12px);
            animation-delay:0.7s;
        }
        .ajax-spinner-bars > .bar-15 {
            transform: rotate(315deg) translate(0, -12px);
            animation-delay:0.75s;
        }
        .ajax-spinner-bars> .bar-16 {
            transform: rotate(337.5deg) translate(0, -12px);
            animation-delay:0.8s;
        }
        @keyframes fadeit{
              0%{ opacity:1; }
              100%{ opacity:0;}
        }
        
        .resp-tabs-container{
            margin-bottom: 15px;
        }
        .resp-tabs-container label{
            display: inline;
        }
        .resp-vtabs .resp-tabs-list li{
            padding: 5px 8px !important;
        }
        .resp-vtabs li.resp-tab-active{
             padding: 11px 9px !important;
        }
        .resp-vtabs ul.resp-tabs-list{
            width:17%;
        }
        .resp-vtabs .resp-tabs-container{
            width:83%;
            min-height: 400px;
        }
        .resp-tab-content{
            padding: 40px;
        }
        
        #verticalTab2 .resp-tabs-container label{display:inherit !important;}
        #verticalTab2 textarea{margin:0;}
        
        #customersslabels label{float:left; padding-right: 10px;}
        
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
    <script>
        var nofiles = true;
        var pfolderid = null;
        var hostname = window.location.hostname;
        var protocol = window.location.protocol;
        $(document).ready(function(){
            /*$( "#input-date,#input-appointment" ).datepicker({dateFormat: 'yy-mm-dd'});
            $('#input-outtime').datetimepicker({dateFormat: 'yy-mm-dd',timeFormat: 'HH:mm:ss'});*/
            /* Tabs UI config*/
            $('#verticalTab').easyResponsiveTabs({
                type: 'vertical',
                width: 'auto',
                fit: true
            });
            $('#verticalTab2').easyResponsiveTabs({
                type: 'vertical',
                width: 'auto',
                fit: true
            });
            $('#doctorname').select2({ placeholder : 'Referred to Doctor' });
            
            /* checkbox  UI config*/
            $("#verticalTab input[type=checkbox]").iCheck({
                checkboxClass: 'icheckbox_square-blue',
                radioClass: 'iradio_square-blue',
                cursor: true,
                increaseArea: '20%'
            });
            
            $.ajax({
                        url:protocol+"//"+hostname+"/api/DriveSync/getsubfolderId/"+'<?php echo $syncEmail; ?>'+"/"+'<?php echo $pfolder; ?>'+"/order/folder",
                        type:"GET",
                        async:false,
                        success:function(data){
                            pfolderid = $.parseJSON(data)[0];
                        },
                        error:function(err){console.log("folder info error")}
             });
             
            if(pfolderid== null)
                return;
            
            var filuploader = $("#excuselatter").uploadFile({
                url:protocol+"//"+hostname+"/api/DriveSync/uploadfile_web/"+'<?php echo $syncEmail; ?>'+"/practice/"+'<?php echo $_SESSION['authUser']; ?>'+"/"+pfolderid+"/patients",
                autoSubmit:false,
                fileName: "myfile",
                showCancel:false,
                showAbort:false,
                showDone:false,
                showStatusAfterSuccess:false,
                showError:false,
                showFileSize:true,
                dragDropStr:"",
                maxFileCount:1,
                statusBarWidth:"auto",
                uploadStr:"",
                showFileCounter:false,
                onLoad:function(obj)
                {
                    $('.fileupload-buttonbar-text').html("Upload your excuse letter.");
                    //setTimeout(function(){$(".ajax-file-upload").find('form span i').next().html(" Addfile...")},10)
                   
                },
                onSelect:function(files)
                {
                    nofiles = false;
                    filuploader.reset();
                    setTimeout(function(){ $('.ajax-file-upload-filename').find(".status").hide();$('.ajax-file-upload-filename').find(".filesize").hide();},10)
                    return true; //to allow file submission.
                },
                onSuccess:function(files,data,xhr,pd)
                {        
                    var excuseletterId = data.split(":");
                    $("#excuseid").val(excuseletterId[0]);
                    $("#checkoutform").submit();
                    $("#submitcheckout").button('reset'); 
                },onError: function(files,status,errMsg,pd)
                {   
                    $("#submitcheckout").button('reset');      
                    alert("file upload error")
                }
                
            })
            $("#doctorname").change(function(){
                $.ajax({
                    url:"patient_check_out_referreddoctor.php",
                    type:"POST",
                    data:{addrId:this.value},
                    success:function(result){
                        $("#referralTxtArea").val(result);
                    },
                    error:function(err){console.log("some error")}    
                });
            });
            $("#submitcheckout").click(function(evt){
                $(this).button('loading');
                if(!nofiles)
                    filuploader.startUpload();
                else{
                        $("#checkoutform").submit();
                    }
            });
            
            $("#eshowlatter").click(function(evt){
                evt.preventDefault();
                var lightbox = lity();
                lightbox(protocol+"//"+hostname+"/practice/drive_view/view_file.php?file_id="+$("#excuseid").val());
                $(document).on('click', '[data-lightbox]', lightbox);
                $('.lity').hide();
                $('.overlay').show();
                $("#loader").show();
            });
            $(document).on('lity:close', function(event, instance) {
                $(instance).remove();
            });
            
            $("#printcheckout").click(function(evt){
                evt.preventDefault();
                window.open("patient_check_out_print.php?patientid="+$("#patientid").val()+"&pc_eid="+$("#pc_eid").val(),'','width=800px,height=600px');
            });
            
        });
        
        function frameload(){
            $("#loader").hide();
            $('.overlay').hide();
            $('.lity').show();
            
        }
        function unShareFile(){
            $.ajax({url:protocol+'//'+hostname+'/api/DriveSync/delete_permission/'+'<?php echo $syncEmail; ?>'+'/'+$("#excuseid").val()+'/anyoneWithLink',type:"get",xhrFields: {withCredentials: true},data:null, crossDomain: true,error:function(e){console.log(e)},success:function(data){console.log(data);}});
        }
    </script>
</head>
<body>

<div class="container">
    <form  action="" method="post" enctype=multipart/form-data" name="checkoutform" id="checkoutform">
		<fieldset>
			<legend>Patient Information:</legend>                        
                        <div class="row">
                            <div class="col-sm-2">
                                <label class="biglabel">Patient Name:</label>
                            </div> 
                            <div class="col-sm-3">
                                <input class="inp-text" name="input-one-name" readonly id="input-one" type="text" size="30" value="<?php echo $sqlArr['fname']; ?>" />
                                <label for="input-one" class="float" for="input-one-name"><strong>First Name</strong></label>
                            </div>        
                            <div class="col-sm-3">
                                <input class="inp-text" name="input-two-name" readonly id="input-two" type="text" size="30" value="<?php echo $sqlArr['mname']; ?>"/>
                                <label for="input-two" class="float" for="input-two-name" ><strong>Middle Name</strong></label>
                            </div>        
                            <div class="col-sm-3">    
                                <input class="inp-text" name="input-three-name" readonly id="input-two" type="text" size="30" value="<?php echo $sqlArr['lname']; ?>"/>    
                                <label for="input-two" for="input-three-name" class="float"><strong>Last Name</strong></label>
                            </div>
                        </div>  
                        <div class="row">
                            <label class="biglabel col-sm-2" for="input-one-date"><strong>Date of Birth:&nbsp;</strong></label>
                            <div class="col-sm-3">
                                <input class="inp-text" name="input-one-date" readonly id="input-date" type="text" placeholder="click hear enter date" value="<?php echo $sqlArr['DOB']; ?>"/>
                            </div>        
                        </div>
                        <div class="row">
                            <label class="biglabel col-sm-2" for="input-email"><strong>E-mail:&nbsp;</strong></label>
                            <div class="col-sm-6">
                                <input class="inp-text" name="input-email" readonly id="input-email" type="text" size="30" value="<?php echo $sqlArr['email']; ?>" />
                            </div>        
                        </div>
                        
                        <div class="row">
                            <label class="biglabel col-sm-2"><strong>Address:&nbsp;</strong></label>
                            <div class="col-sm-6">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <input class="inp-text" name="input-address1" readonly id="address1" type="text" size="30" value="<?php echo $sqlArr['street']; ?>"/>
                                        <label for="input-two" class="float" for="input-address1"><strong>Street Address</strong></label>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <input class="inp-text" name="input-address2" readonly id="input-address2" type="text" size="30" value="<?php echo $sqlArr['street_addr']; ?>"/>
                                        <label for="input-two" for="input-address2" class="float"><strong>Street Address Line 2</strong></label>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <input class="inp-text" name="input-city" readonly id="input-city" type="text" size="30" value="<?php echo $sqlArr['city']; ?>" />
                                        <label for="input-two" for= "input-city" class="float"><strong>City</strong></label>
                                    </div>
                                    <div class="col-sm-6">
                                        <input class="inp-text" name="input-state" readonly id="input-state" type="text" size="30" value="<?php echo $sqlArr['state']; ?>"/>
                                        <label for="input-two" for="input-state" class="float"><strong>State / Province</strong></label>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <input class="inp-text" name="input-postal" id="input-postal" type="text" size="30" value="<?php echo $sqlArr['postal_code']; ?>"/>
                                        <label for="input-two" for="input-postal" class="float"><strong>Postal / Zip Code</strong></label>
                                    </div>
                                    <div class="col-sm-6">
                                            <input class="inp-text" name="country" readonly id="country" type="text" size="30" value="<?php echo $sqlArr['country_code']; ?>"/>
<!--                                        <select class="form-control" id="contry" name="contry" style="border-radius: 0px; height: 29px; padding: 4px 8px; margin-bottom: 4px;">
                                            <option value="">Please Select</option>
                                            <option value="india">Inida</option>
                                            <option value="usa">Usa</option>
                                        </select>-->
                                        <label for="input-two" for="contry" class="float"><strong>Country</strong></label>
                                    </div>
                                </div>
                                
                            </div>          
                        </div>
                        <div class="row">
                            <label class="biglabel col-sm-2"><strong>Phone Number:&nbsp;</strong></label>
                            <div class="col-sm-6">
                                <div class="row">
                                    <div class="col-sm-5">
                                        <input class="inp-text" name="input-phonenumber" readonly id="input-phonenumber" type="text" size="30" value="<?php echo $sqlArr['phone_cell']; ?>"/>
                                        <label for="input-two" for="input-phonenumber" class="float"><strong>Phone Number</strong></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
		</fieldset>

		<!-- ============================== Fieldset 2 ============================== -->
		<fieldset>
			<legend>Educational Handouts:</legend>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div id="verticalTab">
                                            <ul class="resp-tabs-list">
                                                <li>Cancer</li>
                                                <li>Diabetes</li>
                                                <li>Health and Wellness</li>
                                                <li>Healthy Aging</li>
                                                <li>Healthy Living</li>
                                                <li>Heart Health & Stroke</li>
                                                <li>Hospital Safety</li>
                                                <li>How Medications Work</li>
                                                <li>Medication Education</li>
                                                <li>Mental Health</li>
                                                <li>Orthopedics</li>
                                                <li>Patient Safety</li> 
                                                <li>Respiratory</li> 
                                                <li>Your Care at Home</li> 
                                                </ul>
                                            <div class="resp-tabs-container">
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
                                                <div>
                                                    <div class="form-group"><input type='checkbox' name='cancer[]' id='cancer' <?php if(in_array("Breast Cancer: New Reasons for Hope",$cancerArr)): echo "checked"; endif;?> value='Breast Cancer: New Reasons for Hope'>&nbsp;&nbsp;<label for='cancer'>Breast Cancer: New Reasons for Hope</label></div>
                                                    <div class="form-group"><input type='checkbox' name='cancer[]' id='cancer1' <?php if(in_array("Cancer and Nutrition",$cancerArr)): echo "checked"; endif;?> value='Cancer and Nutrition'>&nbsp;&nbsp;<label for='cancer1'>Cancer and Nutrition</label></div>
                                                    <div class="form-group"><input type='checkbox' name='cancer[]' id='cancer2' <?php if(in_array("Cancer Related Fatigue",$cancerArr)): echo "checked"; endif;?> value='Cancer Related Fatigue'>&nbsp;&nbsp;<label for='cancer2'>Cancer Related Fatigue</label></div>
                                                    <div class="form-group"><input type='checkbox' name='cancer[]' id='cancer3' <?php if(in_array("Living with Cancer",$cancerArr)): echo "checked"; endif;?> value='Living with Cancer'>&nbsp;&nbsp;<label for='cancer3'>Living with Cancer</label></div>
                                                    <div class="form-group"><input type='checkbox' name='cancer[]' id='cancer4' <?php if(in_array("Living with Prostate Cancer",$cancerArr)): echo "checked"; endif;?> value='Living with Prostate Cancer'>&nbsp;&nbsp;<label for='cancer4'>Living with Prostate Cancer</label></div>
                                                    <div class="form-group"><input type='checkbox' name='cancer[]' id='cancer5' <?php if(in_array("Lung Cancer: Improving Survival",$cancerArr)): echo "checked"; endif;?> value='Lung Cancer: Improving Survival'>&nbsp;&nbsp;<label for='cancer5'>Lung Cancer: Improving Survival</label></div>
                                                    <div class="form-group"><input type='checkbox' name='cancer[]' id='cancer6' <?php if(in_array("Preventing Colon Cancer",$cancerArr)): echo "checked"; endif;?> value='Preventing Colon Cancer'>&nbsp;&nbsp;<label for='cancer6'>Preventing Colon Cancer</label></div>
                                                </div>

                                                <div>
                                                    <div class="row">
                                                        <div class="col-sm-6">
                                                            <div class="form-group"><input type='checkbox' name='diabetes[]' id='diabetes' <?php if(in_array("Diabetes: Avoiding Complications",$diabetesArr)): echo "checked"; endif;?> value='Diabetes: Avoiding Complications'>&nbsp;&nbsp;<label for='diabetes'>Diabetes: Avoiding Complications</label></div>
                                                            <div class="form-group"><input type='checkbox' name='diabetes[]' id='diabetes1' <?php if(in_array("Diabetes: Prevention",$diabetesArr)): echo "checked"; endif;?> value='Diabetes: Prevention'>&nbsp;&nbsp;<label for='diabetes1'>Diabetes: Prevention</label></div>
                                                            <div class="form-group"><input type='checkbox' name='diabetes[]' id='diabetes2' <?php if(in_array("Diabetes: Treatments",$diabetesArr)): echo "checked"; endif;?> value='Diabetes: Treatments'>&nbsp;&nbsp;<label for='diabetes2'>Diabetes: Treatments</label></div>
                                                            <div class="form-group"><input type='checkbox' name='diabetes[]' id='diabetes3' <?php if(in_array("Foot Inspection",$diabetesArr)): echo "checked"; endif;?> value='Foot Inspection'>&nbsp;&nbsp;<label for='diabetes3'>Foot Inspection</label></div>
                                                            <div class="form-group"><input type='checkbox' name='diabetes[]' id='diabetes4' <?php if(in_array("Giving Yourself Insulin",$diabetesArr)): echo "checked"; endif;?> value='Giving Yourself Insulin'>&nbsp;&nbsp;<label for='diabetes4'>Giving Yourself Insulin</label></div>
                                                            <div class="form-group"><input type='checkbox' name='diabetes[]' id='diabetes5' <?php if(in_array("How to Prepare for Your Diabetes Doctor Visit",$diabetesArr)): echo "checked"; endif;?> value='How to Prepare for Your Diabetes Doctor Visit'>&nbsp;&nbsp;<label for='diabetes5'>How to Prepare for Your Diabetes Doctor Visit</label></div>
                                                            <div class="form-group"><input type='checkbox' name='diabetes[]' id='diabetes6' <?php if(in_array("How to Use Your Insulin Pen",$diabetesArr)): echo "checked"; endif;?> value='How to Use Your Insulin Pen'>&nbsp;&nbsp;<label for='diabetes6'>How to Use Your Insulin Pen</label></div>
                                                            <div class="form-group"><input type='checkbox' name='diabetes[]' id='diabetes7' <?php if(in_array("Hypoglycemia",$diabetesArr)): echo "checked"; endif;?> value='Hypoglycemia'>&nbsp;&nbsp;<label for='diabetes7'>Hypoglycemia</label></div>
                                                            <div class="form-group"><input type='checkbox' name='diabetes[]' id='diabetes8' <?php if(in_array("Managing Your Diabetes",$diabetesArr)): echo "checked"; endif;?> value='Managing Your Diabetes'>&nbsp;&nbsp;<label for='diabetes8'>Managing Your Diabetes</label></div>
                                                            <div class="form-group"><input type='checkbox' name='diabetes[]' id='diabetes9' <?php if(in_array("Prediabetes and Proper Diet",$diabetesArr)): echo "checked"; endif;?> value='Prediabetes and Proper Diet'>&nbsp;&nbsp;<label for='diabetes9'>Prediabetes and Proper Diet</label></div>                                                    
                                                        </div>
                                                        <div class="col-sm-6">                                                    
                                                            <div class="form-group"><input type='checkbox' name='diabetes[]' id='diabetes10' <?php if(in_array("Prediabetes: An Overview",$diabetes)): echo "checked"; endif;?> value='Prediabetes: An Overview'>&nbsp;&nbsp;<label for='diabetes10'>Prediabetes: An Overview</label></div>
                                                            <div class="form-group"><input type='checkbox' name='diabetes[]' id='diabetes11' <?php if(in_array("Prediabetes: Increasing Activity",$diabetes)): echo "checked"; endif;?> value='Prediabetes: Increasing Activity'>&nbsp;&nbsp;<label for='diabetes11'>Prediabetes: Increasing Activity</label></div>
                                                        </div>
                                                    </div>    
                                                </div>

                                                <div>
                                                    <div class="form-group"><input type='checkbox' name='health_willness[]' id='health_willness' <?php if(in_array("Caring for a Loved One",$healthwillnessArr)): echo "checked"; endif;?> value='Caring for a Loved One'>&nbsp;&nbsp;<label for='health_willness'>Caring for a Loved One</label></div>
                                                    <div class="form-group"><input type='checkbox' name='health_willness[]' id='health_willness1' <?php if(in_array("Considering Other Choices",$healthwillnessArr)): echo "checked"; endif;?> value='Considering Other Choices'>&nbsp;&nbsp;<label for='health_willness1'>Considering Other Choices</label></div>
                                                </div>

                                                <div>
                                                    <div class="form-group"><input type='checkbox' name='healthy_aging[]' id='healthy_aging' <?php if(in_array("Advance Directives: Making Family Health Decisions",$healthyagingArr)): echo "checked"; endif;?> value='Advance Directives: Making Family Health Decisions'>&nbsp;&nbsp;<label for='healthy_aging'>Advance Directives: Making Family Health Decisions</label></div>
                                                    <div class="form-group"><input type='checkbox' name='healthy_aging[]' id='healthy_aging1' <?php if(in_array("Alzheimer's Disease: Hope and Help",$healthyagingArr)): echo "checked"; endif;?> value="Alzheimer's Disease: Hope and Help">&nbsp;&nbsp;<label for='healthy_aging1'>Alzheimer's Disease: Hope and Help</label></div>
                                                    <div class="form-group"><input type='checkbox' name='healthy_aging[]' id='healthy_aging2' <?php if(in_array("Healthy Aging",$healthyagingArr)): echo "checked"; endif;?> value='Healthy Aging'>&nbsp;&nbsp;<label for='healthy_aging2'>Healthy Aging</label></div>
                                                    <div class="form-group"><input type='checkbox' name='healthy_aging[]' id='healthy_aging3' <?php if(in_array("Men's Health: Advice to Baby Boomers",$healthyagingArr)): echo "checked"; endif;?> value="Men's Health: Advice to Baby Boomers">&nbsp;&nbsp;<label for='healthy_aging3'>Men's Health: Advice to Baby Boomers</label></div>
                                                    <div class="form-group"><input type='checkbox' name='healthy_aging[]' id='healthy_aging4' <?php if(in_array("Osteoporosis: Strength for Life",$healthyagingArr)): echo "checked"; endif;?> value='Osteoporosis: Strength for Life'>&nbsp;&nbsp;<label for='healthy_aging4'>Osteoporosis: Strength for Life</label></div>
                                                    <div class="form-group"><input type='checkbox' name='healthy_aging[]' id='healthy_aging5' <?php if(in_array("Women's Health: Advice to Baby Boomers",$healthyagingArr)): echo "checked"; endif;?> value="Women's Health: Advice to Baby Boomers">&nbsp;&nbsp;<label for='healthy_aging5'>Women's Health: Advice to Baby Boomers</label></div>                                                  
                                                </div>

                                                <div>
                                                    <div class="form-group"><input type='checkbox' name='healthy_living[]' id='healthy_living' <?php if(in_array("Alcohol & Drug Addiction",$healthylivingArr)): echo "checked"; endif;?> value='Alcohol & Drug Addiction'>&nbsp;&nbsp;<label for='healthy_living'>Alcohol & Drug Addiction</label></div>
                                                    <div class="form-group"><input type='checkbox' name='healthy_living[]' id='healthy_living1' <?php if(in_array("Controlling Stomach Acid Reflux",$healthylivingArr)): echo "checked"; endif;?> value='Controlling Stomach Acid Reflux'>&nbsp;&nbsp;<label for='healthy_living1'>Controlling Stomach Acid Reflux</label></div>
                                                    <div class="form-group"><input type='checkbox' name='healthy_living[]' id='healthy_living2' <?php if(in_array("Managing Chronic Pain",$healthylivingArr)): echo "checked"; endif;?> value='Managing Chronic Pain'>&nbsp;&nbsp;<label for='healthy_living2'>Managing Chronic Pain</label></div>
                                                    <div class="form-group"><input type='checkbox' name='healthy_living[]' id='healthy_living3' <?php if(in_array("Nutritional Needs: Prescription for Health",$healthylivingArr)): echo "checked"; endif;?> value='Nutritional Needs: Prescription for Health'>&nbsp;&nbsp;<label for='healthy_living3'>Nutritional Needs: Prescription for Health</label></div>
                                                    <div class="form-group"><input type='checkbox' name='healthy_living[]' id='healthy_living4' <?php if(in_array("Physical Activity: Improving Your Health",$healthylivingArr)): echo "checked"; endif;?> value='Physical Activity: Improving Your Health'>&nbsp;&nbsp;<label for='healthy_living4'>Physical Activity: Improving Your Health</label></div>
                                                    <div class="form-group"><input type='checkbox' name='healthy_living[]' id='healthy_living5' <?php if(in_array("Stop Smoking Today",$healthylivingArr)): echo "checked"; endif;?> value='Stop Smoking Today'>&nbsp;&nbsp;<label for='healthy_living5'>Stop Smoking Today</label></div>
                                                    <div class="form-group"><input type='checkbox' name='healthy_living[]' id='healthy_living6' <?php if(in_array("Tests That Can Save Your Life",$healthylivingArr)): echo "checked"; endif;?> value='Tests That Can Save Your Life'>&nbsp;&nbsp;<label for='healthy_living6'>Tests That Can Save Your Life</label></div>
                                                    <div class="form-group"><input type='checkbox' name='healthy_living[]' id='healthy_living7' <?php if(in_array("Understanding Obesity: The Key to Effective Weight Loss",$healthylivingArr)): echo "checked"; endif;?> value='Understanding Obesity: The Key to Effective Weight Loss'>&nbsp;&nbsp;<label for='healthy_living7'>Understanding Obesity: The Key to Effective Weight Loss</label></div>                                                  
                                                </div>

                                                <div>
                                                    <div class="row">
                                                        <div class="col-sm-6">
                                                            <div class="form-group"><input type='checkbox' name='heart_health_stroke[]' id='heart_health_stroke' <?php if(in_array("AFib Hospital Discharge",$hearthealthstrokeArr)): echo "checked"; endif;?> value='AFib Hospital Discharge'>&nbsp;&nbsp;<label for='heart_health_stroke'>AFib Hospital Discharge</label></div>
                                                            <div class="form-group"><input type='checkbox' name='heart_health_stroke[]' id='heart_health_stroke1' <?php if(in_array("After a Heart Attack: Preparing for Your First Doctor\'s Visit After Hospital Discharge",$hearthealthstrokeArr)): echo "checked"; endif;?> value='After a Heart Attack: Preparing for Your First Doctor\'s Visit After Hospital Discharge'>&nbsp;&nbsp;<label for='heart_health_stroke1'>After a Heart Attack: Preparing for Your First Doctor's Visit After Hospital Discharge</label></div>
                                                            <div class="form-group"><input type='checkbox' name='heart_health_stroke[]' id='heart_health_stroke2' <?php if(in_array("Avoiding Hospital Readmissions: Heart Attack",$hearthealthstrokeArr)): echo "checked"; endif;?> value='Avoiding Hospital Readmissions: Heart Attack'>&nbsp;&nbsp;<label for='heart_health_stroke2'>Avoiding Hospital Readmissions: Heart Attack</label></div>
                                                            <div class="form-group"><input type='checkbox' name='heart_health_stroke[]' id='heart_health_stroke3' <?php if(in_array("Avoiding Hospital Readmissions: Heart Failure",$hearthealthstrokeArr)): echo "checked"; endif;?> value='Avoiding Hospital Readmissions: Heart Failure'>&nbsp;&nbsp;<label for='heart_health_stroke3'>Avoiding Hospital Readmissions: Heart Failure</label></div>
                                                            <div class="form-group"><input type='checkbox' name='heart_health_stroke[]' id='heart_health_stroke4' <?php if(in_array("Basic Facts About Atrial Fibrillation",$hearthealthstrokeArr)): echo "checked"; endif;?> value='Basic Facts About Atrial Fibrillation'>&nbsp;&nbsp;<label for='heart_health_stroke4'>Basic Facts About Atrial Fibrillation</label></div>
                                                            <div class="form-group"><input type='checkbox' name='heart_health_stroke[]' id='heart_health_stroke5' <?php if(in_array("Controlling High Blood Pressure",$hearthealthstrokeArr)): echo "checked"; endif;?> value='Controlling High Blood Pressure'>&nbsp;&nbsp;<label for='heart_health_stroke5'>Controlling High Blood Pressure</label></div>
                                                            <div class="form-group"><input type='checkbox' name='heart_health_stroke[]' id='heart_health_stroke6' <?php if(in_array("Deep Vein Thrombosis: Are you at Risk",$hearthealthstrokeArr)): echo "checked"; endif;?> value='Deep Vein Thrombosis: Are you at Risk'>&nbsp;&nbsp;<label for='heart_health_stroke6'>Deep Vein Thrombosis: Are you at Risk</label></div>
                                                            <div class="form-group"><input type='checkbox' name='heart_health_stroke[]' id='heart_health_stroke7' <?php if(in_array("Healthy Living After a Heart Attack",$hearthealthstrokeArr)): echo "checked"; endif;?> value='Healthy Living After a Heart Attack'>&nbsp;&nbsp;<label for='heart_health_stroke7'>Healthy Living After a Heart Attack</label></div>
                                                            <div class="form-group"><input type='checkbox' name='heart_health_stroke[]' id='heart_health_stroke8' <?php if(in_array("Heart Disease: Women at Risk",$hearthealthstrokeArr)): echo "checked"; endif;?> value='Heart Disease: Women at Risk'>&nbsp;&nbsp;<label for='heart_health_stroke8'>Heart Disease: Women at Risk</label></div>
                                                            <div class="form-group"><input type='checkbox' name='heart_health_stroke[]' id='heart_health_stroke9' <?php if(in_array("Heart Failure: Beating the Odds",$hearthealthstrokeArr)): echo "checked"; endif;?> value='Heart Failure: Beating the Odds'>&nbsp;&nbsp;<label for='heart_health_stroke9'>Heart Failure: Beating the Odds</label></div>                                                                                                                                                           
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <div class="form-group"><input type='checkbox' name='heart_health_stroke[]' id='heart_health_stroke10' <?php if(in_array("Improving Your Cholesterol",$hearthealthstrokeArr)): echo "checked"; endif;?> value='Improving Your Cholesterol'>&nbsp;&nbsp;<label for='heart_health_stroke10'>Improving Your Cholesterol</label></div> 
                                                            <div class="form-group"><input type='checkbox' name='heart_health_stroke[]' id='heart_health_stroke11' <?php if(in_array("Irregular Heartbeats: Restoring the Rhythm",$hearthealthstrokeArr)): echo "checked"; endif;?> value='Irregular Heartbeats: Restoring the Rhythm'>&nbsp;&nbsp;<label for='heart_health_stroke11'>Irregular Heartbeats: Restoring the Rhythm</label></div>
                                                            <div class="form-group"><input type='checkbox' name='heart_health_stroke[]' id='heart_health_stroke12' <?php if(in_array("Living With Heart Disease",$hearthealthstrokeArr)): echo "checked"; endif;?> value='Living With Heart Disease'>&nbsp;&nbsp;<label for='heart_health_stroke12'>Living With Heart Disease</label></div>
                                                            <div class="form-group"><input type='checkbox' name='heart_health_stroke[]' id='heart_health_stroke13' <?php if(in_array("Medications After a Heart Attack",$hearthealthstrokeArr)): echo "checked"; endif;?> value='Medications After a Heart Attack'>&nbsp;&nbsp;<label for='heart_health_stroke13'>Medications After a Heart Attack</label></div>
                                                            <div class="form-group"><input type='checkbox' name='heart_health_stroke[]' id='heart_health_stroke14' <?php if(in_array("Medications that Help Prevent Blood Clots",$hearthealthstrokeArr)): echo "checked"; endif;?> value='Medications that Help Prevent Blood Clots'>&nbsp;&nbsp;<label for='heart_health_stroke14'>Medications that Help Prevent Blood Clots</label></div>
                                                            <div class="form-group"><input type='checkbox' name='heart_health_stroke[]' id='heart_health_stroke15' <?php if(in_array("Stroke Care: Every Minute Counts",$hearthealthstrokeArr)): echo "checked"; endif;?> value='Stroke Care: Every Minute Counts'>&nbsp;&nbsp;<label for='heart_health_stroke15'>Stroke Care: Every Minute Counts</label></div>
                                                            <div class="form-group"><input type='checkbox' name='heart_health_stroke[]' id='heart_health_stroke16' <?php if(in_array("Stroke Recovery",$hearthealthstrokeArr)): echo "checked"; endif;?> value='Stroke Recovery'>&nbsp;&nbsp;<label for='heart_health_stroke16'>Stroke Recovery</label></div>
                                                            <div class="form-group"><input type='checkbox' name='heart_health_stroke[]' id='heart_health_stroke17' <?php if(in_array("Stroke: The Road to Recovery",$hearthealthstrokeArr)): echo "checked"; endif;?> value='Stroke: The Road to Recovery'>&nbsp;&nbsp;<label for='heart_health_stroke17'>Stroke: The Road to Recovery</label></div>
                                                            <div class="form-group"><input type='checkbox' name='heart_health_stroke[]' id='heart_health_stroke18' <?php if(in_array("Taking Your Own Blood Pressure",$hearthealthstrokeArr)): echo "checked"; endif;?> value='Taking Your Own Blood Pressure'>&nbsp;&nbsp;<label for='heart_health_stroke18'>Taking Your Own Blood Pressure</label></div>
                                                            <div class="form-group"><input type='checkbox' name='heart_health_stroke[]' id='heart_health_stroke19' <?php if(in_array("VTE Signs and Symptoms",$hearthealthstrokeArr)): echo "checked"; endif;?> value='VTE Signs and Symptoms'>&nbsp;&nbsp;<label for='heart_health_stroke19'>VTE Signs and Symptoms</label></div>
                                                            <div class="form-group"><input type='checkbox' name='heart_health_stroke[]' id='heart_health_stroke20' <?php if(in_array("VTE Treatment",$hearthealthstrokeArr)): echo "checked"; endif;?> value='VTE Treatment'>&nbsp;&nbsp;<label for='heart_health_stroke20'>VTE Treatment</label></div>
                                                        </div>
                                                    </div>  
                                                </div>

                                                <div>
                                                    <div class="form-group"><input type='checkbox' name='hospital_safety[]' id='hospital_safety' <?php if(in_array("Hospital Acquired Infections: What You Need to Know",$hospitalsafetyArr)): echo "checked"; endif;?> value='Hospital Acquired Infections: What You Need to Know'>&nbsp;&nbsp;<label for='hospital_safety'>Hospital Acquired Infections: What You Need to Know</label></div>
                                                    <div class="form-group"><input type='checkbox' name='hospital_safety[]' id='hospital_safety1' <?php if(in_array("Patient Safety: Protecting Yourself in the Hospital",$hospitalsafetyArr)): echo "checked"; endif;?> value='Patient Safety: Protecting Yourself in the Hospital'>&nbsp;&nbsp;<label for='hospital_safety1'>Patient Safety: Protecting Yourself in the Hospital</label></div>
                                                    <div class="form-group"><input type='checkbox' name='hospital_safety[]' id='hospital_safety2' <?php if(in_array("Put Your Hands Together",$hospitalsafetyArr)): echo "checked"; endif;?> value='Put Your Hands Together'>&nbsp;&nbsp;<label for='hospital_safety2'>Put Your Hands Together</label></div>
                                                    <div class="form-group"><input type='checkbox' name='hospital_safety[]' id='hospital_safety3' <?php if(in_array("Speak Up: Patient Safety & Advocacy",$hospitalsafetyArr)): echo "checked"; endif;?> value='Speak Up: Patient Safety & Advocacy'>&nbsp;&nbsp;<label for='hospital_safety3'>Speak Up: Patient Safety & Advocacy</label></div>
                                                    <div class="form-group"><input type='checkbox' name='hospital_safety[]' id='hospital_safety4' <?php if(in_array("Staying Safe in the Hospital",$hospitalsafetyArr)): echo "checked"; endif;?> value='Staying Safe in the Hospital'>&nbsp;&nbsp;<label for='hospital_safety4'>Staying Safe in the Hospital</label></div>
                                                    <div class="form-group"><input type='checkbox' name='hospital_safety[]' id='hospital_safety5' <?php if(in_array("Proper Hand Hygiene",$hospitalsafetyArr)): echo "checked"; endif;?> value='Proper Hand Hygiene'>&nbsp;&nbsp;<label for='hospital_safety5'>Proper Hand Hygiene</label></div>
                                                    <div class="form-group"><input type='checkbox' name='hospital_safety[]' id='hospital_safety6' <?php if(in_array("Your Surgery: Before During and After",$hospitalsafetyArr)): echo "checked"; endif;?> value='Your Surgery: Before During and After'>&nbsp;&nbsp;<label for='hospital_safety6'>Your Surgery: Before During and After</label></div>                                            
                                                </div>

                                                <div>
                                                    <div class="row">
                                                        <div class="col-sm-6">
                                                            <div class="form-group"><input type='checkbox' name='medications_work[]' id='medications_work' <?php if(in_array("How Medications Work: Beta blocker",$medicationsworkArr)): echo "checked"; endif;?> value='How Medications Work: Beta blocker'>&nbsp;&nbsp;<label for='medications_work'>How Medications Work: Beta blocker</label></div>
                                                            <div class="form-group"><input type='checkbox' name='medications_work[]' id='medications_work1' <?php if(in_array("How Medications Work: Cholesterol Absorption Inhibitor",$medicationsworkArr)): echo "checked"; endif;?> value='How Medications Work: Cholesterol Absorption Inhibitor'>&nbsp;&nbsp;<label for='medications_work1'>How Medications Work: Cholesterol Absorption Inhibitor</label></div>
                                                            <div class="form-group"><input type='checkbox' name='medications_work[]' id='medications_work2' <?php if(in_array("How Medications Work: Diabetes Drugs that Get Insulin Up and Moving",$medicationsworkArr)): echo "checked"; endif;?> value='How Medications Work: Diabetes Drugs that Get Insulin Up and Moving'>&nbsp;&nbsp;<label for='medications_work2'>How Medications Work: Diabetes Drugs that Get Insulin Up and Moving</label></div>
                                                            <div class="form-group"><input type='checkbox' name='medications_work[]' id='medications_work3' <?php if(in_array("How Medications Work: Diuretic",$medicationsworkArr)): echo "checked"; endif;?> value='How Medications Work: Diuretic'>&nbsp;&nbsp;<label for='medications_work3'>How Medications Work: Diuretic</label></div>
                                                            <div class="form-group"><input type='checkbox' name='medications_work[]' id='medications_work4' <?php if(in_array("How Medications Work: Fat-Regulating Agent",$medicationsworkArr)): echo "checked"; endif;?> value='How Medications Work: Fat-Regulating Agent'>&nbsp;&nbsp;<label for='medications_work4'>How Medications Work: Fat-Regulating Agent</label></div>
                                                            <div class="form-group"><input type='checkbox' name='medications_work[]' id='medications_work5' <?php if(in_array("How Medications Work: Fibrate",$medicationsworkArr)): echo "checked"; endif;?> value='How Medications Work: Fibrate'>&nbsp;&nbsp;<label for='medications_work5'>How Medications Work: Fibrate</label></div>
                                                            <div class="form-group"><input type='checkbox' name='medications_work[]' id='medications_work6' <?php if(in_array("How Medications Work: Insulin Introduction",$medicationsworkArr)): echo "checked"; endif;?> value='How Medications Work: Insulin Introduction'>&nbsp;&nbsp;<label for='medications_work6'>How Medications Work: Insulin Introduction</label></div>
                                                            <div class="form-group"><input type='checkbox' name='medications_work[]' id='medications_work7' <?php if(in_array("How Medications Work: Metformin for Blood Sugar Control",$medicationsworkArr)): echo "checked"; endif;?> value='How Medications Work: Metformin for Blood Sugar Control'>&nbsp;&nbsp;<label for='medications_work7'>How Medications Work: Metformin for Blood Sugar Control</label></div>
                                                            <div class="form-group"><input type='checkbox' name='medications_work[]' id='medications_work8' <?php if(in_array("How Medications Work: Prescription Vitamin B",$medicationsworkArr)): echo "checked"; endif;?> value='How Medications Work: Prescription Vitamin B'>&nbsp;&nbsp;<label for='medications_work8'>How Medications Work: Prescription Vitamin B</label></div>
                                                            <div class="form-group"><input type='checkbox' name='medications_work[]' id='medications_work9' <?php if(in_array("How Medications Work: Resin",$medicationsworkArr)): echo "checked"; endif;?> value='How Medications Work: Resin'>&nbsp;&nbsp;<label for='medications_work9'>How Medications Work: Resin</label></div>                                                  
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <div class="form-group"><input type='checkbox' name='medications_work[]' id='medications_work10' <?php if(in_array("How Medications Work: Starch Blockers for Type 2 Diabetes",$medicationsworkArr)): echo "checked"; endif;?> value='How Medications Work: Starch Blockers for Type 2 Diabetes'>&nbsp;&nbsp;<label for='medications_work10'>How Medications Work: Starch Blockers for Type 2 Diabetes</label></div>
                                                            <div class="form-group"><input type='checkbox' name='medications_work[]' id='medications_work11' <?php if(in_array("How Medications Work: Statin",$medicationsworkArr)): echo "checked"; endif;?> value='How Medications Work: Statin'>&nbsp;&nbsp;<label for='medications_work11'>How Medications Work: Statin</label></div>
                                                            <div class="form-group"><input type='checkbox' name='medications_work[]' id='medications_work12' <?php if(in_array("How Medications Work: TZD Drugs for Type 2 Diabetes",$medicationsworkArr)): echo "checked"; endif;?> value='How Medications Work: TZD Drugs for Type 2 Diabetes'>&nbsp;&nbsp;<label for='medications_work12'>How Medications Work: TZD Drugs for Type 2 Diabetes</label></div>
                                                            <div class="form-group"><input type='checkbox' name='medications_work[]' id='medications_work13' <?php if(in_array("Speak Up: Antibiotics - Know the Facts",$medicationsworkArr)): echo "checked"; endif;?> value='Speak Up: Antibiotics - Know the Facts'>&nbsp;&nbsp;<label for='medications_work13'>Speak Up: Antibiotics - Know the Facts</label></div>                                                    
                                                        </div>
                                                    </div>
                                                </div>

                                                <div>
                                                    <div class="form-group"><input type='checkbox' name='medication_education[]' id='medication_education' <?php if(in_array("Going Home On Blood Thinners",$medicationeducationArr)): echo "checked"; endif;?> value='Going Home On Blood Thinners'>&nbsp;&nbsp;<label for='medication_education'>Going Home On Blood Thinners</label></div>
                                                    <div class="form-group"><input type='checkbox' name='medication_education[]' id='medication_education1' <?php if(in_array("Managing Side Effects of Anti-Inflammatory Medications",$medicationeducationArr)): echo "checked"; endif;?> value='Managing Side Effects of Anti-Inflammatory Medications'>&nbsp;&nbsp;<label for='medication_education1'>Managing Side Effects of Anti-Inflammatory Medications</label></div>
                                                    <div class="form-group"><input type='checkbox' name='medication_education[]' id='medication_education2' <?php if(in_array("Taking Antibiotics Properly",$medicationeducationArr)): echo "checked"; endif;?> value='Taking Antibiotics Properly'>&nbsp;&nbsp;<label for='medication_education2'>Taking Antibiotics Properly</label></div>
                                                    <div class="form-group"><input type='checkbox' name='medication_education[]' id='medication_education3' <?php if(in_array("Taking Medications Safely",$medicationeducationArr)): echo "checked"; endif;?> value='Taking Medications Safely'>&nbsp;&nbsp;<label for='medication_education3'>Taking Medications Safely</label></div>
                                                    <div class="form-group"><input type='checkbox' name='medication_education[]' id='medication_education4' <?php if(in_array("Taking Opioid Medication: Oral Dosage",$medicationeducationArr)): echo "checked"; endif;?> value='Taking Opioid Medication: Oral Dosage'>&nbsp;&nbsp;<label for='medication_education4'>Taking Opioid Medication: Oral Dosage</label></div>                                            
                                                </div>

                                                <div>
                                                    <div class="form-group"><input type='checkbox' name='mental_health[]' id='menta_health'  <?php if(in_array("Depression: Treatments That Work",$mentalhealthArr)): echo "checked"; endif;?> value='Depression: Treatments That Work'>&nbsp;&nbsp;<label for='menta_health'>Depression: Treatments That Work</label></div>
                                                    <div class="form-group"><input type='checkbox' name='mental_health[]' id='menta_health1' <?php if(in_array("Treating Depression",$mentalhealthArr)): echo "checked"; endif;?> value='Treating Depression'>&nbsp;&nbsp;<label for='menta_health1'>Treating Depression</label></div>
                                                </div>

                                                <div>
                                                    <div class="form-group"><input type='checkbox' name='orthopedics[]' id='orthopedics' <?php if(in_array("Your Joint Replacement Journey",$orthopedicsArr)): echo "checked"; endif;?> value='Your Joint Replacement Journey'>&nbsp;&nbsp;<label for='orthopedics'>Your Joint Replacement Journey</label></div>
                                                </div>

                                                <div>
                                                    <div class="form-group"><input type='checkbox' name='patient_safety[]' id='patient_safety' <?php if(in_array("Questions Are the Answer",$patientsafetyArr)): echo "checked"; endif;?> value='Questions Are the Answer'>&nbsp;&nbsp;<label for='patient_safety'>Questions Are the Answer</label></div>
                                                </div>

                                                <div>
                                                    <div class="form-group"><input type='checkbox' name='respiratory[]' id='respiratory' <?php if(in_array("Asthma: One Breath at a Time",$respiratoryArr)): echo "checked"; endif;?> value='Asthma: One Breath at a Time'>&nbsp;&nbsp;<label for='respiratory'>Asthma: One Breath at a Time</label></div>
                                                    <div class="form-group"><input type='checkbox' name='respiratory[]' id='respiratory1' <?php if(in_array("COPD: Coming Up for Air",$respiratoryArr)): echo "checked"; endif;?> value='COPD: Coming Up for Air'>&nbsp;&nbsp;<label for='respiratory1'>COPD: Coming Up for Air</label></div>
                                                    <div class="form-group"><input type='checkbox' name='respiratory[]' id='respiratory2' <?php if(in_array("How to Use a Powder Inhaler (Disc Style)",$respiratoryArr)): echo "checked"; endif;?> value='How to Use a Powder Inhaler (Disc Style)'>&nbsp;&nbsp;<label for='respiratory2'>How to Use a Powder Inhaler (Disc Style)</label></div>
                                                    <div class="form-group"><input type='checkbox' name='respiratory[]' id='respiratory3' <?php if(in_array("How to Use a Powder Inhaler (Egg Style)",$respiratoryArr)): echo "checked"; endif;?> value='How to Use a Powder Inhaler (Egg Style)'>&nbsp;&nbsp;<label for='respiratory3'>How to Use a Powder Inhaler (Egg Style)</label></div>
                                                    <div class="form-group"><input type='checkbox' name='respiratory[]' id='respiratory4' <?php if(in_array("How to Use an Inhaler With a Spacer",$respiratoryArr)): echo "checked"; endif;?> value='How to Use an Inhaler With a Spacer'>&nbsp;&nbsp;<label for='respiratory4'>How to Use an Inhaler With a Spacer</label></div>
                                                    <div class="form-group"><input type='checkbox' name='respiratory[]' id='respiratory5' <?php if(in_array("How to Use an Inhaler Without a Spacer",$respiratoryArr)): echo "checked"; endif;?> value='How to Use an Inhaler Without a Spacer'>&nbsp;&nbsp;<label for='respiratory5'>How to Use an Inhaler Without a Spacer</label></div>
                                                    <div class="form-group"><input type='checkbox' name='respiratory[]' id='respiratory6' <?php if(in_array("Living Well with COPD",$respiratoryArr)): echo "checked"; endif;?> value='Living Well with COPD'>&nbsp;&nbsp;<label for='respiratory6'>Living Well with COPD</label></div>
                                                    <div class="form-group"><input type='checkbox' name='respiratory[]' id='respiratory7' <?php if(in_array("Nasal Congestion and Controlling Your Allergies",$respiratoryArr)): echo "checked"; endif;?> value='Nasal Congestion and Controlling Your Allergies'>&nbsp;&nbsp;<label for='respiratory7'>Nasal Congestion and Controlling Your Allergies</label></div>
                                                    <div class="form-group"><input type='checkbox' name='respiratory[]' id='respiratory8' <?php if(in_array("Preventing Flu and Pneumonia",$respiratoryArr)): echo "checked"; endif;?> value='Preventing Flu and Pneumonia'>&nbsp;&nbsp;<label for='respiratory8'>Preventing Flu and Pneumonia</label></div>                                                                                             
                                                </div>

                                                <div>
                                                    <div class="row">
                                                        <div class="col-sm-6">
                                                            <div class="form-group"><input type='checkbox' name='care_at_home[]' id='care_at_home' <?php if(in_array("Your Care at Home: After a Heart Attack",$careathomeArr)): echo "checked"; endif;?> value='Your Care at Home: After a Heart Attack'>&nbsp;&nbsp;<label for='care_at_home'>Your Care at Home: After a Heart Attack</label></div>
                                                            <div class="form-group"><input type='checkbox' name='care_at_home[]' id='care_at_home1' <?php if(in_array("Your Care at Home: After Cardiac Catheterization",$careathomeArr)): echo "checked"; endif;?> value='Your Care at Home: After Cardiac Catheterization'>&nbsp;&nbsp;<label for='care_at_home1'>Your Care at Home: After Cardiac Catheterization</label></div>
                                                            <div class="form-group"><input type='checkbox' name='care_at_home[]' id='care_at_home2' <?php if(in_array("Your Care at Home: After Surgery",$careathomeArr)): echo "checked"; endif;?> value='Your Care at Home: After Surgery'>&nbsp;&nbsp;<label for='care_at_home2'>Your Care at Home: After Surgery</label></div>
                                                            <div class="form-group"><input type='checkbox' name='care_at_home[]' id='care_at_home3' <?php if(in_array("Your Care at Home: Caring for Your Urinary Catheter",$careathomeArr)): echo "checked"; endif;?> value='Your Care at Home: Caring for Your Urinary Catheter'>&nbsp;&nbsp;<label for='care_at_home3'>Your Care at Home: Caring for Your Urinary Catheter</label></div>
                                                            <div class="form-group"><input type='checkbox' name='care_at_home[]' id='care_at_home4' <?php if(in_array("Your Care at Home: Checking Blood Sugar",$careathomeArr)): echo "checked"; endif;?> value='Your Care at Home: Checking Blood Sugar'>&nbsp;&nbsp;<label for='care_at_home4'>Your Care at Home: Checking Blood Sugar</label></div>
                                                            <div class="form-group"><input type='checkbox' name='care_at_home[]' id='care_at_home5' <?php if(in_array("Your Care at Home: Colostomy Care",$careathomeArr)): echo "checked"; endif;?> value='Your Care at Home: Colostomy Care'>&nbsp;&nbsp;<label for='care_at_home5'>Your Care at Home: Colostomy Care</label></div>
                                                            <div class="form-group"><input type='checkbox' name='care_at_home[]' id='care_at_home6' <?php if(in_array("Your Care at Home: Colostomy Care",$careathomeArr)): echo "checked"; endif;?> value='Your Care at Home: Colostomy Care'>&nbsp;&nbsp;<label for='care_at_home6'>Your Care at Home: Ileostomy Care</label></div>
                                                            <div class="form-group"><input type='checkbox' name='care_at_home[]' id='care_at_home7' <?php if(in_array("Your Care at Home: Infection Control",$careathomeArr)): echo "checked"; endif;?> value='Your Care at Home: Infection Control'>&nbsp;&nbsp;<label for='care_at_home7'>Your Care at Home: Infection Control</label></div>
                                                            <div class="form-group"><input type='checkbox' name='care_at_home[]' id='care_at_home8' <?php if(in_array("Your Care at Home: Managing Heart Failure",$careathomeArr)): echo "checked"; endif;?> value='Your Care at Home: Managing Heart Failure'>&nbsp;&nbsp;<label for='care_at_home8'>Your Care at Home: Managing Heart Failure</label></div>                                                    
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <div class="form-group"><input type='checkbox' name='care_at_home[]' id='care_at_home9' <?php if(in_array("Your Care at Home: Managing Your Medicine",$careathomeArr)): echo "checked"; endif;?> value='Your Care at Home: Managing Your Medicine'>&nbsp;&nbsp;<label for='care_at_home9'>Your Care at Home: Managing Your Medicine</label></div>                                                  
                                                            <div class="form-group"><input type='checkbox' name='care_at_home[]' id='care_at_home10' <?php if(in_array("Your Care at Home: Pneumonia",$careathomeArr)): echo "checked"; endif;?> value='Your Care at Home: Pneumonia'>&nbsp;&nbsp;<label for='care_at_home10'>Your Care at Home: Pneumonia</label></div>
                                                            <div class="form-group"><input type='checkbox' name='care_at_home[]' id='care_at_home11' <?php if(in_array("Your Care at Home: Preventing Falls",$careathomeArr)): echo "checked"; endif;?> value='Your Care at Home: Preventing Falls'>&nbsp;&nbsp;<label for='care_at_home11'>Your Care at Home: Preventing Falls</label></div>
                                                            <div class="form-group"><input type='checkbox' name='care_at_home[]' id='care_at_home12' <?php if(in_array("Your Care at Home: Taking Blood Thinners",$careathomeArr)): echo "checked"; endif;?> value='Your Care at Home: Taking Blood Thinners'>&nbsp;&nbsp;<label for='care_at_home12'>Your Care at Home: Taking Blood Thinners</label></div>
                                                            <div class="form-group"><input type='checkbox' name='care_at_home[]' id='care_at_home13' <?php if(in_array("Your Care at Home: Taking Insulin",$careathomeArr)): echo "checked"; endif;?> value='Your Care at Home: Taking Insulin'>&nbsp;&nbsp;<label for='care_at_home13'>Your Care at Home: Taking Insulin</label></div>                                                    
                                                            <div class="form-group"><input type='checkbox' name='care_at_home[]' id='care_at_home14' <?php if(in_array("Your Care at Home: Urostomy Care",$careathomeArr)): echo "checked"; endif;?> value='Your Care at Home: Urostomy Care'>&nbsp;&nbsp;<label for='care_at_home14'>Your Care at Home: Urostomy Care</label></div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                        
                                <div class="row">
                                <label class="biglabel col-sm-2" for="prescription"><strong>Prescriptions:&nbsp;</strong></label>
                                <div class="col-sm-3">
                                    <textarea name="prescription" id="prescription"><?php echo $sqlRows['prescription']; ?></textarea>
                                </div> 
                                </div>
                                
                                <div class="row">
                                    <label class="biglabel col-sm-2" for="excuselatter">Excuse Letter : </label>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <div id="excuselatter"></div>
                                       </div>
                                        <div class="form-group">
                                            <a href="#" class="btn btn-primary" id="eshowlatter"><span class="glyphicon glyphicon-eye-open"></span>&nbsp;Show excuse letter</a>
                                        </div>
                                       
                                    </div>
                                </div>
		</fieldset>
		<!-- ============================== Fieldset 2 end ============================== -->


		<!-- ============================== Fieldset 3 ============================== -->
		<fieldset>
			<legend>Procedures:</legend>
                        <div class="row">
                                    <div class="col-sm-12">
                                        <div id="verticalTab2">
                                            <ul class="resp-tabs-list">
                                                <li>Lab Order</li>
                                                <li>Radiology Order</li>
                                                <li>Referral Order</li>
                                            </ul>
                                            <div class="resp-tabs-container">
                                                <div>
                                                    <div class="form-group"> 
                                                        <?php
                                                            if($sqlLab['form_id'] != ""):
                                                                labcorp_report($patientid,$pc_eid,"",$sqlLab['form_id']);
                                                            else:
                                                                echo "No Lab Orders";
                                                            endif;
                                                        ?>
                                                    </div>
                                                </div>
                                                <div>
                                                        <div class="row">
                                                        <div class="col-sm-6">
                                                            <input type="text" name="radiologyTxt" id="radiologyTxt" size="30" value="<?php echo $sqlRows['examreason']; ?>"/>
                                                            <label for="radiology" for="radiology" class="float"><strong>Reason for Exam</strong></label>
                                                        </div>
                                                        </div> 
                                                        <div class="row">
                                                        <div class="col-sm-6">
                                                            <input type="text" name="examtype" id="examtype" size="30" value="<?php echo $sqlRows['examtype']; ?>"/>
                                                            <label for="examtype" for="examtype" class="float"><strong>Type of Exam</strong></label>
                                                        </div>
                                                        </div>
                                                        <div class="row">
                                                        <div class="col-sm-6">
                                                            <textarea name="radiologyTxtArea" id="radiologyTxtArea"><?php echo $sqlRows['ordersentto']; ?></textarea>
                                                            <label for="radiologydetail" for="radiologydetail" class="float"><strong>Order sent to</strong></label>
                                                        </div>
                                                        </div>    
                                                </div>
                                                <div>
                                                        <div class="row">
                                                        <div class="col-sm-6">
                                                            <input type="text" name="referralTxt" id="radiologyTxt" size="30" value="<?php echo $sqlRows['referralreason']; ?>"/>
                                                            <label for="referral" for="referral" class="float"><strong>Reason for Referral</strong></label>
                                                        </div>
                                                        </div> 
                                                        <div class="row">
                                                        <div class="col-sm-6">
                                                            <select id="doctorname" name="doctorname">
                                                                <option value=0>--Referred to Doctor--</option>
                                                            <?php 
                                                                $sqlProvider = sqlStatement("SELECT id,name FROM tbl_allcare_referralproviders");
                                                                while($sqlProv = sqlFetchArray($sqlProvider)):
                                                                    $sqlProvSelected = "";
                                                                    if($sqlRows['referreddoctorAddrId'] == $sqlProv['id']):
                                                                        $sqlProvSelected = "selected";
                                                                    endif;
                                                                    echo "<option value=".$sqlProv['id']." $sqlProvSelected >".$sqlProv['name']."</option>";
                                                                endwhile;
                                                            ?>
                                                            </select>
                                                            <label for="doctorname" for="doctorname" class="float"><strong>Referred to Doctor</strong></label>
                                                        </div>
                                                        </div> 
                                                        <div class="row">
                                                        <div class="col-sm-6">
                                                            <textarea name="referralTxtArea" id="referralTxtArea"><?php echo $sqlRows['doctorreferredto']; ?></textarea>
                                                            <label for="referraldetail" for="referraldetail" class="float"><strong>Doctor Address</strong></label>
                                                        </div>
                                                        </div> 
                                                </div>
                                            </div> 
                                             </div>
                                         </div>
                             </div>
			<div class="row">
                            <label class="biglabel col-sm-2" for="input-appointment">Future Appointment:</label>
                            <div class="col-sm-3">
                                <input class="inp-text" name="input-appointment" id="input-appointment" type="text" readonly placeholder="click hear enter date"  value="<?php echo $sqlArr['openAppdate']; ?>"/>
                            </div>
                        </div>
                        <div class="row">
                            <label class="biglabel col-sm-2" for="input-patientblance">Patient Balance: </label>
                            <div class="col-sm-4">
                                <input class="inp-text" name="input-patientblance" id="input-patientblance" type="number"  value="<?php echo $sqlRows['patientbal']; ?>"/>
                            </div>
                        </div>
                        <div class="row">
                            <label class="biglabel col-sm-2" for="vebalins">Verbal Instructions: </label>
                            <div class="col-sm-4">
                                <textarea id="vebalins" name="vebalins"><?php echo $sqlRows['verbalins']; ?></textarea>
                            </div>
                        </div>
                        <div class="row">
                            <label class="biglabel col-sm-2" for="reviewsummary">Review Summary:&nbsp;</label>
                            <div class="col-sm-4">
                                <textarea id="reviewsummary" name="reviewsummary"><?php echo $sqlRows['reviewsummary']; ?></textarea>
                            </div>
                        </div>
                        <div class="row">
                            <label class="biglabel col-sm-2" for="excuselatter">Customer service survey:&nbsp;</label>
                            <div class="col-sm-6" id="customersslabels">
                                <label for="customerss1"><input class="choose" name="customerss" id="customerss1" type="radio" value="Very Good" <?php if($sqlRows['csssurvey']=='Very Good'): echo "checked"; endif; ?> />Very Good</label>
				<label for="customerss2"><input class="choose" name="customerss" id="customerss2" type="radio" value="Good" <?php if($sqlRows['csssurvey']=='Good'): echo "checked"; endif; ?> />Good</label>
				<label for="customerss3"><input class="choose" name="customerss" id="customerss3" type="radio" value="Satisfactory" <?php if($sqlRows['csssurvey']=='Satisfactory'): echo "checked"; endif; ?> />Satisfactory</label>
				<label for="customerss4"><input class="choose" name="customerss" id="customerss4" type="radio" value="Average" <?php if($sqlRows['csssurvey']=='Average'): echo "checked"; endif; ?> />Average</label>
                                <label for="customerss4"><input class="choose" name="customerss" id="customerss4" type="radio" value="Poor" <?php if($sqlRows['csssurvey']=='Poor'): echo "checked"; endif; ?> />Poor</label>
                                
                                <div class="row">
                                    <div class="col-sm-6">
                                        <textarea id="feedback" name="feedback"><?php echo $sqlRows['csssurveycomments']; ?></textarea>
                                        <label for="feedback" class="float">Detailed feedback</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <br/>
                        <div class="row">
                            <label class="biglabel col-sm-2" for="input-outtime"><strong>Check-Out Time:&nbsp;</strong></label>
                            <div class="col-sm-3">
                                <input class="inp-text" name="input-outtime" readonly id="input-outtime" type="text" placeholder="click hear enter date & time" value="<?php echo date('Y-m-d H:i:s'); ?>" />
                            </div>        
                        </div>
		</fieldset>
		<!-- ============================== Fieldset 3 end ============================== -->
                <div class="row">
                    <div class="col-sm-12 text-center">
                        <button id="submitcheckout" class="btn btn-primary" type="button" name="Submit" data-loading-text="Processing...">SUBMIT</button>
                        <button id="printcheckout" class="btn btn-primary" type="button" name="print">PRINT</button>
                    </div>
                    
                </div>
                <input type="hidden" id="patientid" name="patientid" value="<?php echo $patientid; ?>" />
                <input type="hidden" id="pc_eid" name="pc_eid" value="<?php echo $pc_eid; ?>" />
                <input type="hidden" id="excuseid" name="excuseid" value="<?php echo $sqlRows['excuseletter']; ?>" />
	</form>
    <div id="loader" style="display: none;">
        <div class="ajax-spinner-bars">
            <div class="bar-1"></div><div class="bar-2"></div><div class="bar-3"></div><div class="bar-4"></div><div class="bar-5"></div><div class="bar-6"></div><div class="bar-7"></div><div class="bar-8"></div><div class="bar-9"></div><div class="bar-10"></div><div class="bar-11"></div><div class="bar-12"></div><div class="bar-13"></div><div class="bar-14"></div><div class="bar-15"></div><div class="bar-16"></div></div>
        <div id="loadertitle">Excuse Letter Loading...</div>
    </div>
</div>

</body>
</html>

