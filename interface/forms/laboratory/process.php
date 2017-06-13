<?php
/** **************************************************************************
 *	LABORATORY/PROCESS.PHP
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
 *  @version 2.2
 *  @copyright Medical Technology Services
 *  @author Ron Criswell <ron.criswell@MDTechSvcs.com>
 * 
 *************************************************************************** */
require_once("../../globals.php");
require_once("{$GLOBALS['srcdir']}/options.inc.php");
require_once("{$GLOBALS['srcdir']}/lists.inc");
require_once("{$GLOBALS['srcdir']}/wmt/wmt.class.php");
require_once("{$GLOBALS['srcdir']}/wmt/wmt.include.php");
require_once("{$GLOBALS['srcdir']}/wmt/laboratory/OrderClient.php");

$document_url = $GLOBALS['web_root']."/controller.php?document&retrieve&patient_id=".$pid."&amp;document_id=";

function getCreds($id) {
	if (!$id) return;
	$query = "SELECT * FROM users WHERE id = '".$id."' LIMIT 1";
	$user = sqlQuery($query);
	return $user['npi']."^".$user['lname']."^".$user['fname']."^".$user['mname']."^^^^^NPI";
}

// for label printing (not always used!!!)
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
	
	echo "<option value=''";
	if (!$active) echo " selected='selected'";
	echo ">&nbsp;</option>\n";
	foreach ($labelers AS $rrow) {
		echo "<option value='" . $rrow['option_id'] . "'";
		if ($active == $rrow['option_id']) echo " selected='selected'";
		echo ">" . $rrow['title'];
		echo "</option>\n";
	}
}

// set processing date/time
$order_data->date_transmitted = date('Y-m-d H:i:s');

// get all AOE questions and answers
$query = "SELECT pc.procedure_code, pq.question_code, pq.question_text, pq.required, pa.answer FROM procedure_order_code pc ";
$query .= "LEFT JOIN procedure_questions pq ON pq.lab_id = ? AND pc.procedure_code = pq.procedure_code ";
$query .= "LEFT JOIN procedure_answers pa ON pa.question_code = pq.question_code AND pa.procedure_order_id = pc.procedure_order_id AND pa.procedure_order_seq = pc.procedure_order_seq ";
$query .= "WHERE pc.procedure_order_id = ? AND pq.activity = 1 ";
$query .= "ORDER BY pa.procedure_order_id, pa.procedure_order_seq, pa.answer_seq";
$values[] = $order_data->lab_id;
$values[] = $order_item->procedure_order_id;
$results = sqlStatement($query,$values);
	
$aoe_list = null;
while ($data = sqlFetchArray($results)) {
	$aoe_list[] = $data;
}

// validate aoe responses (loop)
$aoe_errors = "";
if (is_array($aoe_list)) {
	foreach ($aoe_list as $aoe_data) {
		if ($aoe_data['required'] && !$aoe_data['answer']) {
			$question = str_replace(':', '', $aoe_data['question_text']);
			$test = $aoe_data['procedure_code'];
			$aoe_errors .= "\nQuestion [$question] for test [$test] requires a valid response.";
		}
	}
}
?>
	
	<form method='post' action="" id="order_process" name="order_process" style="margin-bottom:0"> 
		<table class="bgcolor2" style="width:100%;height:100%">
			<tr>
				<td colspan="4">
					<h2 style="padding-bottom:0;margin-bottom:0">Order Processing</h2>
				</td>
			</tr>
			<tr>
				<td colspan="4" style="padding-bottom:20px">
<?php 
if ($aoe_errors) { // oh well .. have to terminate process with errors
	echo "The following errors must be corrected before submitting:";
	echo "<pre>\n";
	echo $aoe_errors;
?>
				</td>
			</tr><tr>
				<td colspan="4" style="text-align:right">
					<input type="button" class="wmtButton" onclick="doReturn('<?php echo $order_data->id ?>')" value="close" />
				</td>
			</tr>
		</table>
	</form>
<?php
	exit; 
}

echo "<pre>\n";

try { // catch any processing errors
	// get a handle to processor
	$client = new OrderClient($lab_id);

	// create request message
	$client->buildRequest($order_data);

	// determine third-party payment information
	if ($order_data->work_flag) { // workers comp claim
		$order_data->request_billing = 'T';

		// build workers comp insurance record
		$ins_data = new wmtInsurance($work_insurance);
		$ins_data->plan_name = "Workers Comp"; // IN1.08
		$ins_data->group_number = ""; // IN1.08
		$ins_data->work_flag = "Y"; // IN1.31
		$ins_data->policy_number = $order_data->work_case; // IN1.36

		// create hl7 segment
		$client->addInsurance(1, $ins_data);
	}
	else { // normal insurance
		if ($GLOBALS['wmt::lab_ins_pick']) { // special SFA processing 
			if ( is_numeric($order_data->request_billing) ) { // we have specific insurance
				$ins_primary = $order_data->request_billing;
				$order_data->request_billing = 'T';
				$ins_secondary = '';
			}
		}

		// create insurance records
		if ( $order_data->request_billing != 'C' && !$ins_primary )
			$order_data->request_billing = 'P'; // if not client bill and no insurance must be patient bill
	
		if ($order_data->request_billing == 'T' && $ins_primary ) { // only add insurance for third-party bill with insurance
			$client->addInsurance(1, $ins_primary);
			if ($ins_secondary)
				$client->addInsurance(2, $ins_secondary);
		}
		
		// special "self" and "clinic" for BioRef Lab
		if ( $lab_data['npi'] == 'BIOREF' && $order_data->request_billing != 'T' ) {
			// key (C or P) indicates special processing 
			$client->addInsurance(1, $order_data->request_billing);
		}
		
	}

	// add guarantor
	$client->addGuarantor($order_data->pid);

	// create orders (loop)
	$seq = 1;
	$test_list = array(); // for requisition
	foreach ($item_list as $item_data) {
		$client->addOrder($seq++, $order_data, $item_data, $aoe_list);
		$test_list[] = array('code'=>$item_data->procedure_code,'name'=>$item_data->procedure_name);
	}

	// generate requisition
	$doc_data = $client->getOrderDocument($order_data,$test_list,$aoe_list);

	if ($doc_data) { // got a document so suceess
		
		// DO THE SUBMIT !!
		if ($lab_data['protocol'] != 'INT' && $lab_data['protocol'] != 'internal') { // don't send internal anywhere
			$client->submitOrder($order_data);
		}

		// never gets here if there is a processing error
		$order_data->status = 's'; // submitted
		$order_data->order_req_id = $doc_data->get_id();
		$order_data->order_status = 'processed';
		$order_data->update();	
	}
	else {
		die("FATAL ERROR: failed to generate requisition document!!");
	}
	
	// SFA Automatic lab billing module!!
	if ($GLOBALS['wmt::auto_lab_bill'] && $order_data->request_billing == 'C') {
		$drg_array = array();
		// loop back through the ordered items
		foreach ($item_list as $item_data) {
			// collect diagnoses
			$drg_string = '';
			if ($item_data->diagnoses) {
				$items = explode('|',$item_data->diagnoses);
				foreach ($items AS $item) {
					list($code,$text) = explode("^",$item);
					if (!$code || !$text) continue;
					$drg = str_replace('ICD10:', '', $code);
					$drg_array[$drg] = $text;
					$drg_string .= "ICD10|" . $drg . ":";
				}
			}
			
			// collect codes CPT4(type 100) 
			// WHERE codes(id) = prices(pr_id) & prices(pr_level) = patient_data(pricelevel) || "standard"
			$binds = array($item_data->procedure_code, $order_data->lab_id);
			$sql = "SELECT `standard_code` FROM `procedure_type` WHERE ";
			$sql .= "`procedure_code` = ? AND `lab_id` = ? AND (`procedure_type` = 'ord' OR `procedure_type` = 'pro')";
			$res = sqlQuery($sql,$binds);
			$cpt4 = str_replace("CPT4:", '', $res['standard_code']);

			$binds = array($pat_data->pricelevel, $item_data->procedure_code);
			$sql =  "SELECT ct.`ct_id`, co.`id` AS code_id, pr1.`pr_price` AS std_price, pr2.`pr_price` AS the_price, pr2.`pr_id` ";
			$sql .= "FROM `code_types` ct, `codes` co ";
			$sql .= "LEFT JOIN `prices` pr1 ON co.`id` = pr1.`pr_id` AND pr1.`pr_level` LIKE 'standard' ";
			$sql .= "LEFT JOIN `prices` pr2 ON co.`id` = pr2.`pr_id` AND pr2.`pr_level` LIKE ? ";
			$sql .= "WHERE ct.`ct_key` LIKE 'CPT4' AND ct.`ct_id` = co.`code_type` AND co.`code` LIKE ?";
			$res = sqlQuery($sql,$binds);
			
			$fee = 0; // default
			if ($res && $res['ct_id'] && $res['code_id']) {
				$fee = $res['std_price']; // assume standard
				if ($res['pr_id']) $fee = $res['the_price']; // found custom price
			}
			
			// build billing record
			$values = array();
			$sql = "INSERT INTO `billing` SET ";
			$sql .= "`date` = ?, ";
			$values[] = $order_data->date_ordered;
			$sql .= "`code_type` = ?, ";
			$values[] = 'CPT4';
			$sql .= "`code` = ?, ";
			$values[] = $cpt4;
			$sql .= "`pid` = ?, ";
			$values[] = $order_data->patient_id;
			$sql .= "`provider_id` = ?, ";
			$values[] = $order_data->provider_id;
			$sql .= "`user` = ?, ";
			$values[] = $_SESSION['authId'];
			$sql .= "`groupname` = ?, ";
			$values[] = 'Default';
			$sql .= "`authorized` = ?, ";
			$values[] = '1';
			$sql .= "`encounter` = ?, ";
			$values[] = $order_data->encounter_id;
			$sql .= "`code_text` = ?, ";
			$values[] = $item_data->procedure_name;
			$sql .= "`units` = ?, ";
			$values[] = '1';
			$sql .= "`activity` = ?, ";
			$values[] = '1';
			$sql .= "`justify` = ?, ";
			$values[] = $drg_string;
			$sql .= "`fee` = ? ";
			$values[] = $fee;
				
			// save record
			sqlInsert($sql,$values);
		}
		
		// save the diagnosis codes
		foreach ($drg_array AS $key => $text) {
			$binds = array($key, $order_data->patient_id, $order_data->encounter_id);
			$record = sqlQuery("SELECT `id` FROM `billing` WHERE `code_type` = 'ICD10' AND `code` = ? AND `pid` = ? AND `encounter` = ?", $binds);
			if (!$record || !$record['id']) {
				// build billing record
				$values = array();
				$sql = "INSERT INTO `billing` SET ";
				$sql .= "`date` = ?, ";
				$values[] = $order_data->date_ordered;
				$sql .= "`code_type` = ?, ";
				$values[] = 'ICD10';
				$sql .= "`code` = ?, ";
				$values[] = $key;
				$sql .= "`pid` = ?, ";
				$values[] = $order_data->patient_id;
				$sql .= "`provider_id` = ?, ";
				$values[] = $order_data->provider_id;
				$sql .= "`user` = ?, ";
				$values[] = $_SESSION['authId'];
				$sql .= "`groupname` = ?, ";
				$values[] = 'Default';
				$sql .= "`authorized` = ?, ";
				$values[] = '1';
				$sql .= "`encounter` = ?, ";
				$values[] = $order_data->encounter_id;
				$sql .= "`code_text` = ?, ";
				$values[] = $text;
				$sql .= "`activity` = ?, ";
				$values[] = '1';
				$sql .= "`units` = ? ";
				$values[] = '1';
			
				// save record
				sqlInsert($sql,$values);
			}
		}
	}
}
catch (Exception $e) {
	die ("FATAL ERROR: ".$e->getMessage());
}
?>
					</pre>
				</td>
			</tr>
<?php
if ($doc_data && $lab_data['npi'] == 'BBPL' || $lab_data['npi'] == 'BIOREF') { // labels for some labs
?>
			<tr>
				<td class="wmtLabel" colspan="2" style="padding-bottom:10px;padding-left:8px">
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

					<input class="nolock" type="button" tabindex="-1" onclick="printLabels(1)" value="Print Labels" />
				</td>
			</tr>
<?php 
} // end of failed test
?>				
			<tr>
				<td>
<?php if ($order_data->order_abn_id) { ?>
					<input type="button" class="wmtButton" onclick="location.href='<?php echo $document_url . $order_data->order_abn_id ?>';return false" value="ABN print" />
<?php } ?>				
<?php if ($order_data->order_req_id) { ?>
					<input type="button" class="wmtButton" onclick="location.href='<?php echo $document_url . $order_data->order_req_id ?>';return false" value="REQ print" />
<?php } ?>
				</td>
				<td style="text-align:right;min-width:120px">
<?php if (!$abn_needed) { ?>
					<input type="button" class="wmtButton" onclick="doClose()" value="close" />
<?php } ?>
					<input type="button" class="wmtButton" onclick="doReturn(<?php echo $order_data->id ?>)" value="return" />
				</td>
			</tr>
		</table>
	</form>