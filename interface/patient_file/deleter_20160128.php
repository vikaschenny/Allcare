
<?php
/**
 * delete tool, for logging and removing patient data.
 *
 * Called from many different pages.
 *
 *  Copyright (C) 2005-2013 Rod Roark <rod@sunsetsystems.com>
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * @package OpenEMR
 * @author  Rod Roark <rod@sunsetsystems.com>
 * @link    http://www.open-emr.org
 */

require_once('../globals.php');
require_once($GLOBALS['srcdir'].'/log.inc');
require_once($GLOBALS['srcdir'].'/acl.inc');
require_once($GLOBALS['srcdir'].'/sl_eob.inc.php');

 $patient     = $_REQUEST['patient'];
 $encounterid = $_REQUEST['encounterid'];
 $formid      = $_REQUEST['formid'];
 $issue       = $_REQUEST['issue'];
 $document    = $_REQUEST['document'];
 $payment     = $_REQUEST['payment'];
 $billing     = $_REQUEST['billing'];
 $transaction = $_REQUEST['transaction'];
 $service_facility = $_REQUEST['id'];
 $agency            = $_REQUEST['aid'];
 $pharmacyid        = $_REQUEST['pharmacyid'];
 $insuid            = $_REQUEST['insuid'];
 $providercredid    = $_REQUEST['providercredid'];
 $userid            = $_REQUEST['userid'];
 $abookcontactid    = $_REQUEST['abookcontactid'];
 $abookcredid       = $_REQUEST['abookcredid'];
 $payrollid         = $_REQUEST['payrollid'];
 $f2f               = $_REQUEST['f2fid'];
 $chart             = $_REQUEST['coid'];
 $chart_pid         = $_REQUEST['patient_id'];
 
 $form_name=$_REQUEST['form'];
 $f2f_encounter_id = $_REQUEST['hdnF2FEncId'];
 $chart_output_encounter_id = $_REQUEST['hdnChartOutputEncId'];
 $trans_form_id=$_REQUEST['hdnTransFormId'];
 $f2f_trans_pid=$_REQUEST['hdnPid'];
 $chart_output_trans_pid=$_REQUEST['hdnPid'];
//for provider_portal
 $location=$_REQUEST['location'];
 $provider=$_REQUEST['provider'];
 $patient1=$_REQUEST['patient_id'];
 $trans=$_REQUEST['trans'];
 $info_msg = "";

 // Delete rows, with logging, for the specified table using the
 // specified WHERE clause.
 //
 function row_delete($table, $where) {
     
  $tres = sqlStatement("SELECT * FROM $table WHERE $where");
  $count = 0;
  
  while ($trow = sqlFetchArray($tres)) {
      
   $logstring = "";
   foreach ($trow as $key => $value) {
    if (! $value || $value == '0000-00-00 00:00:00') continue;
    if ($logstring) $logstring .= " ";
    $logstring .= $key . "='" . addslashes($value) . "'";
   }
   newEvent("delete", $_SESSION['authUser'], $_SESSION['authProvider'], 1, "$table: $logstring");
   ++$count;
  }
  if ($count) {
   $query = "DELETE FROM $table WHERE $where";
   //echo $query . "<br>\n";
   sqlStatement($query);
  } 
  header("Refresh:0;url=transaction/transactions.php");
 }
 
 

 // Deactivate rows, with logging, for the specified table using the
 // specified SET and WHERE clauses.
 //
 function row_modify($table, $set, $where) {
  if (sqlQuery("SELECT * FROM $table WHERE $where")) {
   newEvent("deactivate", $_SESSION['authUser'], $_SESSION['authProvider'], 1, "$table: $where");
   $query = "UPDATE $table SET $set WHERE $where";
   echo $query . "<br>\n";
   sqlStatement($query);
  }
 }

// We use this to put dashes, colons, etc. back into a timestamp.
//
function decorateString($fmt, $str) {
  $res = '';
  while ($fmt) {
    $fc = substr($fmt, 0, 1);
    $fmt = substr($fmt, 1);
    if ($fc == '.') {
      $res .= substr($str, 0, 1);
      $str = substr($str, 1);
    } else {
      $res .= $fc;
    }
  }
  return $res;
}

// Delete and undo product sales for a given patient or visit.
// This is special because it has to replace the inventory.
//
function delete_drug_sales($patient_id, $encounter_id=0) {
  $where = $encounter_id ? "ds.encounter = '$encounter_id'" :
    "ds.pid = '$patient_id' AND ds.encounter != 0";
  sqlStatement("UPDATE drug_sales AS ds, drug_inventory AS di " .
    "SET di.on_hand = di.on_hand + ds.quantity " .
    "WHERE $where AND di.inventory_id = ds.inventory_id");
  if ($encounter_id) {
    row_delete("drug_sales", "encounter = '$encounter_id'");
  }
  else {
    row_delete("drug_sales", "pid = '$patient_id'");
  }
}

// Delete a form's data from its form-specific table.
//
function form_delete($formdir, $formid) {
  $formdir = ($formdir == 'newpatient') ? 'encounter' : $formdir;
  if (substr($formdir,0,3) == 'LBF') {
    row_delete("lbf_data", "form_id = '$formid'");
  }
  else if ($formdir == 'procedure_order') {
    $tres = sqlStatement("SELECT procedure_report_id FROM procedure_report " .
      "WHERE procedure_order_id = ?", array($formid));
    while ($trow = sqlFetchArray($tres)) {
      $reportid = 0 + $trow['procedure_report_id'];
      row_delete("procedure_result", "procedure_report_id = '$reportid'");
    }
    row_delete("procedure_report", "procedure_order_id = '$formid'");
    row_delete("procedure_order_code", "procedure_order_id = '$formid'");
    row_delete("procedure_order", "procedure_order_id = '$formid'");
  }
  else if ($formdir == 'physical_exam') {
    row_delete("form_$formdir", "forms_id = '$formid'");
  }
  else if ($formdir == 'allcare_physical_exam') {
    row_delete("tbl_form_physical_exam", "forms_id = '$formid'");
  }
  else if ($formdir == 'allcare_ros') {
    row_delete("tbl_form_allcare_ros", "id = '$formid'");
  }
  else if ($formdir == 'cpo') {
    row_delete("tbl_form_cpo", "id = '$formid'");
  }
  else if ($formdir == 'ccm') {
    row_delete("tbl_form_ccm", "id = '$formid'");
  }
  else {
    row_delete("form_$formdir", "id = '$formid'");
  }
}

// Delete a specified document including its associated relations and file.
//
function delete_document($document) {
  $trow = sqlQuery("SELECT url FROM documents WHERE id = '$document'");
  $url = $trow['url'];
  row_delete("categories_to_documents", "document_id = '$document'");
  row_delete("documents", "id = '$document'");
  row_delete("gprelations", "type1 = 1 AND id1 = '$document'");
  if (substr($url, 0, 7) == 'file://') {
    @unlink(substr($url, 7));
  }
}
?>
<html>
<head>
<?php html_header_show();?>
<title><?php xl('Delete Patient, Encounter, Form, Issue, Document, Payment, Billing or Transaction ,service_facility','e'); ?></title>
<link rel="stylesheet" href='<?php echo $css_header ?>' type='text/css'>

<style>
td { font-size:10pt; }
</style>

<script language="javascript">
function submit_form()
{
document.deletefrm.submit();

}
// Java script function for closing the popup
function popup_close() {
	if(parent.$==undefined) {
	  	window.close();
	 }
	 else {
	  	parent.$.fn.fancybox.close(); 
	 }	  
}
</script>
</head>

<body class="body_top">
<?php
 // If the delete is confirmed...
 //
 if ($_POST['form_submit']) {

  if ($patient) {
   if (!acl_check('admin', 'super')) die("Not authorized!");
   row_modify("billing"       , "activity = 0", "pid = '$patient'");
   row_modify("pnotes"        , "deleted = 1" , "pid = '$patient'");
   // row_modify("prescriptions" , "active = 0"  , "patient_id = '$patient'");
   row_delete("prescriptions"  , "patient_id = '$patient'");
   row_delete("claims"         , "patient_id = '$patient'");
   delete_drug_sales($patient);
   row_delete("payments"       , "pid = '$patient'");
   row_delete("ar_activity"    , "pid = '$patient'");
   row_delete("openemr_postcalendar_events", "pc_pid = '$patient'");
   row_delete("immunizations"  , "patient_id = '$patient'");
   row_delete("issue_encounter", "pid = '$patient'");
   row_delete("lists"          , "pid = '$patient'");
   row_delete("transactions"   , "pid = '$patient'");
   row_delete("employer_data"  , "pid = '$patient'");
   row_delete("history_data"   , "pid = '$patient'");
   row_delete("insurance_data" , "pid = '$patient'");

   $res = sqlStatement("SELECT * FROM forms WHERE pid = '$patient'");
   while ($row = sqlFetchArray($res)) {
    form_delete($row['formdir'], $row['form_id']);
   }
   row_delete("forms", "pid = '$patient'");

   // integration_mapping is used for sql-ledger and is virtually obsolete now.
   $row = sqlQuery("SELECT id FROM patient_data WHERE pid = '$patient'");
   row_delete("integration_mapping", "local_table = 'patient_data' AND " .
    "local_id = '" . $row['id'] . "'");

   // Delete all documents for the patient.
   $res = sqlStatement("SELECT id FROM documents WHERE foreign_id = '$patient'");
   while ($row = sqlFetchArray($res)) {
    delete_document($row['id']);
   }

   // This table exists only for athletic teams.
   $tmp = sqlQuery("SHOW TABLES LIKE 'daily_fitness'");
   if (!empty($tmp)) {
    row_delete("daily_fitness", "pid = '$patient'");
   }

   row_delete("patient_data", "pid = '$patient'");
  }
  else if ($encounterid) {
   if (!acl_check('admin', 'super')) die("Not authorized!");
   row_modify("billing", "activity = 0", "encounter = '$encounterid'");
   delete_drug_sales(0, $encounterid);
   row_delete("ar_activity", "encounter = '$encounterid'");
   row_delete("claims", "encounter_id = '$encounterid'");
   row_delete("issue_encounter", "encounter = '$encounterid'");
   $res = sqlStatement("SELECT * FROM forms WHERE encounter = '$encounterid'");
   while ($row = sqlFetchArray($res)) {
    form_delete($row['formdir'], $row['form_id']);
   }
   row_delete("forms", "encounter = '$encounterid'");
  }
  else if ($formid) {
   if (!acl_check('admin', 'super')) die("Not authorized!");
   $row = sqlQuery("SELECT * FROM forms WHERE id = '$formid'");
   $formdir = $row['formdir'];
   if (! $formdir) die("There is no form with id '$formid'");
   form_delete($formdir, $row['form_id']);
   row_delete("forms", "id = '$formid'");
  }
  else if ($issue) {
   if (!acl_check('admin', 'super')) die("Not authorized!");
   row_delete("issue_encounter", "list_id = '$issue'");
   row_delete("lists", "id = '$issue'");
  }
  else if ($document) {
   if (!acl_check('admin', 'super')) die("Not authorized!");
   delete_document($document);
  }
  else if ($service_facility) {
   if (!acl_check('admin', 'super')) die("Not authorized!");
   row_delete("tbl_patientfacility", "id = '$service_facility'");
  }
  else if ($agency) {
   if (!acl_check('admin', 'super')) die("Not authorized!");
   row_delete("tbl_patientagency", "id = '$agency'");
  } 
  else if ($userid) {
   if (!acl_check('admin', 'super')) die("Not authorized!");
   row_delete("tbl_patientuser", "id = '$userid'");
  }
   else if ($pharmacyid) {
   if (!acl_check('admin', 'super')) die("Not authorized!");
   row_delete("tbl_patientpharmacy", "id = '$pharmacyid'");
  }
  else if ($insuid) {
   if (!acl_check('admin', 'super')) die("Not authorized!");
   row_delete("tbl_patientinsurancecompany", "id = '$insuid'");
  }
  else if ($providercredid) {
   if (!acl_check('admin', 'super')) die("Not authorized!");
   row_delete("tbl_patientprovidercredentials", "id = '$providercredid'");
  }
  else if ($payrollid) {
   if (!acl_check('admin', 'super')) die("Not authorized!");
   row_delete("tbl_userpayroll", "id = '$payrollid'");
  }
  else if ($abookcontactid) {
   if (!acl_check('admin', 'super')) die("Not authorized!");
   row_delete("tbl_user_abookcontact", "id = '$abookcontactid'");
  }
  else if ($abookcredid) {
   if (!acl_check('admin', 'super')) die("Not authorized!");
   row_delete("tbl_user_cred", "id = '$abookcredid'");
  }
  else if ($payment) {
   if (!acl_check('admin', 'super')) die("Not authorized!");
    list($patient_id, $timestamp, $ref_id) = explode(".", $payment);
    // if (empty($ref_id)) $ref_id = -1;
    $timestamp = decorateString('....-..-.. ..:..:..', $timestamp);
    $payres = sqlStatement("SELECT * FROM payments WHERE " .
      "pid = '$patient_id' AND dtime = '$timestamp'");
    while ($payrow = sqlFetchArray($payres)) {
      if ($payrow['encounter']) {
        $ref_id = -1;
        // The session ID passed in is useless. Look for the most recent
        // patient payment session with pay total matching pay amount and with
        // no adjustments. The resulting session ID may be 0 (no session) which
        // is why we start with -1.
        $tpmt = $payrow['amount1'] + $payrow['amount2'];
        $seres = sqlStatement("SELECT " .
          "SUM(pay_amount) AS pay_amount, session_id " .
          "FROM ar_activity WHERE " .
          "pid = '$patient_id' AND " .
          "encounter = '" . $payrow['encounter'] . "' AND " .
          "payer_type = 0 AND " .
          "adj_amount = 0.00 " .
          "GROUP BY session_id ORDER BY session_id DESC");
        while ($serow = sqlFetchArray($seres)) {
          if (sprintf("%01.2f", $serow['adj_amount']) != 0.00) continue;
          if (sprintf("%01.2f", $serow['pay_amount'] - $tpmt) == 0.00) {
            $ref_id = $serow['session_id'];
            break;
          }
        }
        if ($ref_id == -1) {
          die(xlt('Unable to match this payment in ar_activity') . ": $tpmt");
        }
        // Delete the payment.
        row_delete("ar_activity",
          "pid = '$patient_id' AND " .
          "encounter = '" . $payrow['encounter'] . "' AND " .
          "payer_type = 0 AND " .
          "pay_amount != 0.00 AND " .
          "adj_amount = 0.00 AND " .
          "session_id = '$ref_id'");
        if ($ref_id) {
          row_delete("ar_session",
            "patient_id = '$patient_id' AND " .
            "session_id = '$ref_id'");
        }
      }
      else {
        // Encounter is 0! Seems this happens for pre-payments.
        $tpmt = sprintf("%01.2f", $payrow['amount1'] + $payrow['amount2']);
        row_delete("ar_session",
          "patient_id = '$patient_id' AND " .
          "payer_id = 0 AND " .
          "reference = '" . add_escape_custom($payrow['source']) . "' AND " .
          "pay_total = '$tpmt' AND " .
          "(SELECT COUNT(*) FROM ar_activity where ar_activity.session_id = ar_session.session_id) = 0 " .
          "ORDER BY session_id DESC LIMIT 1");
      }
      row_delete("payments", "id = '" . $payrow['id'] . "'");
    }
  }
  else if ($billing) {
    if (!acl_check('acct','disc')) die("Not authorized!");
    list($patient_id, $encounter_id) = explode(".", $billing);
    if ($GLOBALS['oer_config']['ws_accounting']['enabled'] === 2) {
      sqlStatement("DELETE FROM ar_activity WHERE " .
        "pid = '$patient_id' AND encounter = '$encounter_id'");
      sqlStatement("DELETE ar_session FROM ar_session LEFT JOIN " .
        "ar_activity ON ar_session.session_id = ar_activity.session_id " .
        "WHERE ar_activity.session_id IS NULL");
      row_modify("billing", "activity = 0",
        "pid = '$patient_id' AND " .
        "encounter = '$encounter_id' AND " .
        "code_type = 'COPAY' AND " .
        "activity = 1");
      sqlStatement("UPDATE form_encounter SET last_level_billed = 0, " .
        "last_level_closed = 0, stmt_count = 0, last_stmt_date = NULL " .
        "WHERE pid = '$patient_id' AND encounter = '$encounter_id'");
    }
    else {
      slInitialize();
      $trans_id = SLQueryValue("SELECT id FROM ar WHERE ar.invnumber = '$billing' LIMIT 1");
      if ($trans_id) {
        newEvent("delete", $_SESSION['authUser'], $_SESSION['authProvider'], 1, "Invoice $billing from SQL-Ledger");
        SLQuery("DELETE FROM acc_trans WHERE trans_id = '$trans_id'");
        if ($sl_err) die($sl_err);
        SLQuery("DELETE FROM invoice WHERE trans_id = '$trans_id'");
        if ($sl_err) die($sl_err);
        SLQuery("DELETE FROM ar WHERE id = '$trans_id'");
        if ($sl_err) die($sl_err);
      } else {
        $info_msg .= "Invoice '$billing' not found!";
      }
      SLClose();
    }
    sqlStatement("UPDATE drug_sales SET billed = 0 WHERE " .
      "pid = '$patient_id' AND encounter = '$encounter_id'");
    updateClaim(true, $patient_id, $encounter_id, -1, -1, 1, 0, ''); // clears for rebilling
  }
  else if ($transaction && !$f2f_encounter_id && !$chart_output_encounter_id) {echo "<script>alert('2424');</script>";
   if (!acl_check('admin', 'super')) die("Not authorized!");
   row_delete("transactions", "id = '$transaction'");
  }
  else if (($form_name=='facetoface' && $trans_form_id)
          || $f2f_encounter_id) { 
   if (!acl_check('admin', 'super')) die("Not authorized!");
   
   $getPDFfile="SELECT pdf_file FROM tbl_form_facetoface_transactions
   WHERE encounter = '$f2f_encounter_id' AND pid='$f2f_trans_pid' AND id='$trans_form_id'";
   $resPDFfile=sqlStatement($getPDFfile);
   $rowPDFfile=sqlFetchArray($resPDFfile);
   
   unlink('transaction/transactions_pdf/facetoface/'.$rowPDFfile['pdf_file']);
   
   row_delete("tbl_form_facetoface_transactions", 
              "encounter = '$f2f_encounter_id' 
               AND pid='$f2f_trans_pid' 
               AND id='$trans_form_id'");
  }  
  else if (($form_name=='chartoutput' && $trans_form_id)
          || $chart_output_encounter_id) {
   if (!acl_check('admin', 'super')) die("Not authorized!");
   
   $getPDFfile="SELECT pdf_file FROM tbl_form_chartoutput_transactions
   WHERE pid='$chart_output_trans_pid' AND id='$trans_form_id'";
   $resPDFfile=sqlStatement($getPDFfile);
   $rowPDFfile=sqlFetchArray($resPDFfile);
   
   unlink('transaction/transactions_pdf/chart_output/'.$rowPDFfile['pdf_file']);
   
   row_delete("tbl_form_chartoutput_transactions",
   "pid='$chart_output_trans_pid' AND id='$trans_form_id'");
  } 
  else if ($f2f) {
   if (!acl_check('admin', 'super')) die("Not authorized!");
   row_delete("tbl_form_facetoface_transactions", "id = '$f2f'");
  } 
  else if ($chart) {
   if (!acl_check('admin', 'super')) die("Not authorized!");
   row_delete("tbl_form_chartoutput_transactions",
   "pid='$chart_pid' AND id='$chart'");
  } 
  else {
   die("Nothing was recognized to delete!");
  }

  if (! $info_msg) $info_msg = xl('Delete successful.');

  // Close this window and tell our opener that it's done.
  //
  echo "<script language='JavaScript'>\n";
  if ($info_msg) echo " alert('$info_msg');\n";
  if($location=='provider_portal'){
      if($trans=='f2f'){
          echo "window.parent.location.href = '../../../providers/providers_f2f.php?provider=$provider&form_patient=$patient1'; parent.$.fancybox.close(); "; 
      }else{
           echo "window.parent.location.href = '../../../providers/providers_medrecord.php?provider=$provider&form_patient=$patient1'; parent.$.fancybox.close(); ";
      }
     
  }
  if ($encounterid) //this code need to be same as 'parent.imdeleted($encounterid)' when the popup is div like
  {
    echo "window.opener.imdeleted($encounterid);\n";
  }
  else
  {
    echo " if (opener && opener.imdeleted) opener.imdeleted(); else parent.imdeleted();\n";
  }
  echo " window.close();\n";
  echo "</script></body></html>\n";
  exit();
 }
?>

<!--<form method='post' name="deletefrm" action='deleter.php?patient=<?php echo $patient ?>&encounterid=<?php echo $encounterid ?>&formid=<?php echo $formid ?>&issue=<?php echo $issue ?>&document=<?php echo $document ?>&payment=<?php echo $payment ?>&billing=<?php echo $billing ?>&transaction=<?php echo $transaction ?>' onsubmit="javascript:alert('1');document.deleform.submit();">-->
<form method='post' name="deletefrm" action='deleter.php?patient=<?php echo $patient ?>&encounterid=<?php echo $encounterid ?>&formid=<?php echo $formid ?>&issue=<?php echo $issue ?>&document=<?php echo $document ?>&payment=<?php echo $payment ?>&billing=<?php echo $billing ?>&transaction=<?php echo $transaction ?>
&form=<?php echo $form_name?>
&f2f_encounter_id=<?php echo $_REQUEST['hdnF2FEncId']; ?>
&chart_output_encounter_id=<?php echo $_REQUEST['hdnChartOutputEncId']; ?>
&trans_form_id=<?php echo $_REQUEST['hdnTransFormId']; ?>
&f2f_trans_pid=<?php echo $_REQUEST['hdnPid'];?>
&chart_output_trans_pid=<?php echo $_REQUEST['hdnPid'];?>&abookcontactid=<?php echo $abookcontactid; ?>&abookcredid=<?php echo $abookcredid; ?>&id=<?php echo $service_facility; ?>&payrollid=<?php echo $payrollid; ?>&aid=<?php echo $agency; ?>&pharmacyid=<?php echo $pharmacyid; ?>&providercredid=<?php echo $providercredid; ?>&insuid=<?php echo $insuid; ?>&userid=<?php echo $userid; ?>&f2fid=<?php echo $f2f; ?>&coid=<?php echo $chart; ?>&patient_id=<?php echo $chart_pid; ?>&location=<?php echo $location; ?>&provider=<?php echo $provider; ?>&patient_id=<?php echo $patient1; ?>' onsubmit="javascript:alert('1');document.deleform.submit();">

    
<input type="hidden" id="hdnF2FEncId" name="hdnF2FEncId" value="<?php echo $_REQUEST['f2f_encounter_id']; ?>" />    
<input type="hidden" id="hdnChartOutputEncId" name="hdnChartOutputEncId" value="<?php echo $_REQUEST['chart_output_encounter_id']; ?>" />
<input type="hidden" id="hdnPid" name="hdnPid" value="<?php echo $_REQUEST['patient_id']; ?>" />
<input type="hidden" id="hdnTransFormId" name="hdnTransFormId" value="<?php echo $_REQUEST['trans_form_id']; ?>" />
<input type="hidden" id="trans" name="trans" value="<?php echo $trans; ?>" />
    
    
<p class="text">&nbsp;<br><?php xl('Do you really want to delete','e'); ?>

<?php
 if ($patient) {
  echo xl('patient') . " $patient";
 } else if ($encounterid) {
  echo xl('encounter') . " $encounterid";
 } else if ($formid) {
  echo xl('form') . " $formid";
 } else if ($issue) {
  echo xl('issue') . " $issue";
 } else if ($document) {
  echo xl('document') . " $document";
 } else if ($payment) {
  echo xl('payment') . " $payment";
 } else if ($billing) {
  echo xl('invoice') . " $billing";
 } else if ($transaction) {
  echo xl('transaction') . " $transaction";
 }
 else if ($service_facility ) {
  echo xl('service facility') . " $service_facility ";
 }
 else if ($agency ) {
  echo xl('Agency') . " $agency";
 }
 else if ($pharmacyid ) {
  echo xl('pharmacy_id') . " $pharmacyid ";
 }
 else if ($insuid ) {
  echo xl('insurance') . " $insuid";
 }
 else if ($userid ) {
  echo xl('user attribute') . " $userid";
 }
  else if ($f2f ) {
  echo xl('Face To Face') . " $f2f";
 }
 else if ($chart ) {
  echo xl('Chart Output') . " $chart";
 }
 else if($providercredid){
    echo xl('Provider Credentials') . " $providercredid";
 }  
 else if($payrollid){
    echo xl('Payroll') . " $payrollid";
 }  
 else if($abookcontactid){
    echo xl('Addressbook Contacts') . " $abookcontactid";
 }  
 else if($abookcredid){
    echo xl('Addressbook Credentials') . " $abookcredid";
 }  
?> <?php xl('and all subordinate data? This action will be logged','e'); ?>!</p>

<center>

<p class="text">&nbsp;<br>
<a href="#" onclick="submit_form()" class="css_button"><span><?php xl('Yes, Delete and Log','e'); ?></span></a>
<input type='hidden' name='form_submit' value=<?php xl('Yes, Delete and Log','e','\'','\''); ?>/>
<?php if($location=='provider_portal') { 
if($trans=='f2f'){ ?>
        
          <a href='#' class="css_button" onclick="window.parent.location.href = '../../../providers/providers_f2f.php?provider=<?php echo $provider; ?>&form_patient=<?php echo $patient1; ?>'; parent.$.fancybox.close();"><span><?php echo xl('No, Cancel');?></span></a>
     <?php  }else{ ?>
         
            <a href='#' class="css_button" onclick="window.parent.location.href = '../../../providers/providers_medrecord.php?provider=<?php echo $provider; ?>&form_patient=<?php echo $patient1; ?>'; parent.$.fancybox.close();"><span><?php echo xl('No, Cancel');?></span></a>
     <?php  }?>
     
<?php }else{ ?>
    <a href='#' class="css_button" onclick=popup_close();><span><?php echo xl('No, Cancel');?></span></a>
<?php }
?>

</p>

</center>
</form>
</body>
</html>
