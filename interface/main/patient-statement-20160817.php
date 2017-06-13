<?php
// Copyright (C) 2005-2010 Rod Roark <rod@sunsetsystems.com>
//
// Windows compatibility and statement downloading:
//     2009 Bill Cernansky and Tony McCormick [mi-squared.com]
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.

// This is the first of two pages to support posting of EOBs.
// The second is sl_eob_invoice.php.

require_once("../globals.php");
require_once("$srcdir/patient.inc");
require_once("$srcdir/sql-ledger.inc");
require_once("$srcdir/invoice_summary.inc.php");
require_once($GLOBALS['OE_SITE_DIR'] . "/statement.inc.php");
require_once("$srcdir/parse_era.inc.php");
require_once("$srcdir/sl_eob.inc.php");
require_once("$srcdir/formatting.inc.php");
require_once("$srcdir/classes/class.ezpdf.php");//for the purpose of pdf creation

$DEBUG = 0; // set to 0 for production, 1 to test

$INTEGRATED_AR = $GLOBALS['oer_config']['ws_accounting']['enabled'] === 2;

$alertmsg = '';
$where = '';
$eraname = '';
$eracount = 0;

// This is called back by parse_era() if we are processing X12 835's.
//
function era_callback(&$out) {
  global $where, $eracount, $eraname, $INTEGRATED_AR;
  // print_r($out); // debugging
  ++$eracount;
  // $eraname = $out['isa_control_number'];
  $eraname = $out['gs_date'] . '_' . ltrim($out['isa_control_number'], '0') .
    '_' . ltrim($out['payer_id'], '0');
  list($pid, $encounter, $invnumber) = slInvoiceNumber($out);

  if ($pid && $encounter) {
    if ($where) $where .= ' OR ';
    if ($INTEGRATED_AR) {
      $where .= "( f.pid = '$pid' AND f.encounter = '$encounter' )";
    } else {
      $where .= "invnumber = '$invnumber'";
    }
  }
}

function bucks($amount) {
  if ($amount) echo oeFormatMoney($amount);
}

// Upload a file to the client's browser
//
function upload_file_to_client($file_to_send) {
  header("Pragma: public");
  header("Expires: 0");
  header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
  header("Content-Type: application/force-download");
  header("Content-Length: " . filesize($file_to_send));
  header("Content-Disposition: attachment; filename=" . basename($file_to_send));
  header("Content-Description: File Transfer");
  readfile($file_to_send);
  // flush the content to the browser. If you don't do this, the text from the subsequent
  // output from this script will be in the file instead of sent to the browser.
  flush();
  exit(); //added to exit from process properly in order to stop bad html code -ehrlive
  // sleep one second to ensure there's no follow-on.
  sleep(1);
}
function upload_file_to_client_pdf($file_to_send) {
//Function reads a text file and converts to pdf.

  global $STMT_TEMP_FILE_PDF;
  $pdf =& new Cezpdf('LETTER');//pdf creation starts
  $pdf->ezSetMargins(36,0,36,0);
  $pdf->selectFont($GLOBALS['fileroot'] . "/library/fonts/Courier.afm");
  $pdf->ezSetY($pdf->ez['pageHeight'] - $pdf->ez['topMargin']);
  $countline=1;
  $file = fopen($file_to_send, "r");//this file contains the text to be converted to pdf.
  while(!feof($file))
   {
    $OneLine=fgets($file);//one line is read
	 if(stristr($OneLine, "\014") == true && !feof($file))//form feed means we should start a new page.
	  {
	    $pdf->ezNewPage();
	    $pdf->ezSetY($pdf->ez['pageHeight'] - $pdf->ez['topMargin']);
		str_replace("\014", "", $OneLine);
	  }
	
	if(stristr($OneLine, 'REMIT TO') == true || stristr($OneLine, 'Visit Date') == true)//lines are made bold when 'REMIT TO' or 'Visit Date' is there.
	 $pdf->ezText('<b>'.$OneLine.'</b>', 12, array('justification' => 'left', 'leading' => 6)); 
	else
	 $pdf->ezText($OneLine, 12, array('justification' => 'left', 'leading' => 6)); 
	 
	$countline++; 
   }
	
	$fh = @fopen($STMT_TEMP_FILE_PDF, 'w');//stored to a pdf file
    if ($fh) {
      fwrite($fh, $pdf->ezOutput());
      fclose($fh);
    }
  header("Pragma: public");//this section outputs the pdf file to browser
  header("Expires: 0");
  header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
  header("Content-Type: application/force-download");
  header("Content-Length: " . filesize($STMT_TEMP_FILE_PDF));
  header("Content-Disposition: attachment; filename=" . basename($STMT_TEMP_FILE_PDF));
  header("Content-Description: File Transfer");
  readfile($STMT_TEMP_FILE_PDF);
  // flush the content to the browser. If you don't do this, the text from the subsequent
  // output from this script will be in the file instead of sent to the browser.
  flush();
  exit(); //added to exit from process properly in order to stop bad html code -ehrlive
  // sleep one second to ensure there's no follow-on.
  sleep(1);
}

// billing facility
 $res = sqlStatement("SELECT * FROM facility WHERE billing_location = 1");

    // This loops once for billing facility details.
    //
    $practiceName = $practiceAddr = $practiceCity = $practiceState = $practiceZip = $domain_identifier = "";
    while ($row = sqlFetchArray($res)) {
        $practiceName = $row['name'];
        $practiceAddr = $row['street'];
        $practiceCity = $row['city'];
        $practiceState = $row['state'];
        $practiceZip = $row['postal_code'];
    }
    $res = sqlStatement("SELECT x12_sender_id FROM x12_partners WHERE name = 'ZIRMED'");
    while ($row = sqlFetchArray($res)) {
        $domain_identifier = $row['x12_sender_id'];
    }

$today = date("Y-m-d");

if ($INTEGRATED_AR) {

  // Print or download statements if requested.
  //
  if (($_POST['form_print'] || $_POST['form_download'] || $_POST['form_pdf']) && $_POST['form_cb']) {

    $fhprint = fopen($STMT_TEMP_FILE, 'w');

    $where = "";
    foreach ($_POST['form_cb'] as $key => $value) $where .= " OR f.id = $key";
    $where = substr($where, 4);

    $res = sqlStatement("SELECT " .
      "f.id, f.date, f.pid, f.encounter, f.stmt_count, f.last_stmt_date, " .
      "p.fname, p.mname, p.lname, p.street, p.city, p.state, p.postal_code " .
      "FROM form_encounter AS f, patient_data AS p " .
      "WHERE ( $where ) AND " .
      "p.pid = f.pid " .
      "ORDER BY p.lname, p.fname, f.pid, f.date, f.encounter");

    $stmt = array();
    $stmt_count = 0;

    // This loops once for each invoice/encounter.
    //
    while ($row = sqlFetchArray($res)) {
      $svcdate = substr($row['date'], 0, 10);
      $duedate = $svcdate; // TBD?
      $duncount = $row['stmt_count'];

      // If this is a new patient then print the pending statement
      // and start a new one.  This is an associative array:
      //
      //  cid     = same as pid
      //  pid     = OpenEMR patient ID
      //  patient = patient name
      //  amount  = total amount due
      //  adjust  = adjustments (already applied to amount)
      //  duedate = due date of the oldest included invoice
      //  age     = number of days from duedate to today
      //  to      = array of addressee name/address lines
      //  lines   = array of:
      //    dos     = date of service "yyyy-mm-dd"
      //    desc    = description
      //    amount  = charge less adjustments
      //    paid    = amount paid
      //    notice  = 1 for first notice, 2 for second, etc.
      //    detail  = array of details, see invoice_summary.inc.php
      //
      if ($stmt['cid'] != $row['pid']) {
        if (!empty($stmt)) ++$stmt_count;
        fwrite($fhprint, create_statement($stmt));
        $stmt['cid'] = $row['pid'];
        $stmt['pid'] = $row['pid'];
        $stmt['patient'] = $row['fname'] . ' ' . $row['lname'];
        $stmt['to'] = array($row['fname'] . ' ' . $row['lname']);
        if ($row['street']) $stmt['to'][] = $row['street'];
        $stmt['to'][] = $row['city'] . ", " . $row['state'] . " " . $row['postal_code'];
        $stmt['lines'] = array();
        $stmt['amount'] = '0.00';
        $stmt['today'] = $today;
        $stmt['duedate'] = $duedate;
      } else {
        // Report the oldest due date.
        if ($duedate < $stmt['duedate']) {
          $stmt['duedate'] = $duedate;
        }
      }

      // Recompute age at each invoice.
      $stmt['age'] = round((strtotime($today) - strtotime($stmt['duedate'])) /
        (24 * 60 * 60));

      $invlines = ar_get_invoice_summary($row['pid'], $row['encounter'], true);
      foreach ($invlines as $key => $value) {
        $line = array();
        $line['dos']     = $svcdate;
        $line['desc']    = ($key == 'CO-PAY') ? "Patient Payment" : "Procedure $key";
        $line['amount']  = sprintf("%.2f", $value['chg']);
        $line['adjust']  = sprintf("%.2f", $value['adj']);
        $line['paid']    = sprintf("%.2f", $value['chg'] - $value['bal']);
        $line['notice']  = $duncount + 1;
        $line['detail']  = $value['dtl'];
        $stmt['lines'][] = $line;
        $stmt['amount']  = sprintf("%.2f", $stmt['amount'] + $value['bal']);
      }

      // Record that this statement was run.
      if (! $DEBUG && ! $_POST['form_without']) {
        sqlStatement("UPDATE form_encounter SET " .
          "last_stmt_date = '$today', stmt_count = stmt_count + 1 " .
          "WHERE id = " . $row['id']);
      }
    } // end for

    if (!empty($stmt)) ++$stmt_count;
    fwrite($fhprint, create_statement($stmt));
    fclose($fhprint);
    sleep(1);

    // Download or print the file, as selected
    if ($_POST['form_download']) {
      upload_file_to_client($STMT_TEMP_FILE);
    } elseif ($_POST['form_pdf']) {
      upload_file_to_client_pdf($STMT_TEMP_FILE);
    } else { // Must be print!
      if ($DEBUG) {
        $alertmsg = xl("Printing skipped; see test output in") .' '. $STMT_TEMP_FILE;
      } else {
        exec("$STMT_PRINT_CMD $STMT_TEMP_FILE");
        if ($_POST['form_without']) {
          $alertmsg = xl('Now printing') .' '. $stmt_count .' '. xl('statements; invoices will not be updated.');
        } else {
          $alertmsg = xl('Now printing') .' '. $stmt_count .' '. xl('statements and updating invoices.');
        }
      } // end not debug
    } // end not form_download
  } // end statements requested
} // end $INTEGRATED_AR
else {
  SLConnect();

  // This will be true starting with SQL-Ledger 2.8.x:
  $got_address_table = SLQueryValue("SELECT count(*) FROM pg_tables WHERE " .
    "schemaname = 'public' AND tablename = 'address'");

  // Print or download statements if requested.
  //
  if (($_POST['form_print'] || $_POST['form_download'] || $_POST['form_pdf']) && $_POST['form_cb']) {

    $fhprint = fopen($STMT_TEMP_FILE, 'w');

    $where = "";
    foreach ($_POST['form_cb'] as $key => $value) $where .= " OR ar.id = $key";
    $where = substr($where, 4);

    // Sort by patient so that multiple invoices can be
    // represented on a single statement.
    if ($got_address_table) {
      $res = SLQuery("SELECT ar.*, customer.name, " .
        "address.address1, address.address2, " .
        "address.city, address.state, address.zipcode, " .
        "substring(trim(both from customer.name) from '% #\"%#\"' for '#') AS fname, " .
        "substring(trim(both from customer.name) from '#\"%#\" %' for '#') AS lname " .
        "FROM ar, customer, address WHERE ( $where ) AND " .
        "customer.id = ar.customer_id AND " .
        "address.trans_id = ar.customer_id " .
        "ORDER BY lname, fname, ar.customer_id, ar.transdate");
    }
    else {
      $res = SLQuery("SELECT ar.*, customer.name, " .
        "customer.address1, customer.address2, " .
        "customer.city, customer.state, customer.zipcode, " .
        "substring(trim(both from customer.name) from '% #\"%#\"' for '#') AS lname, " .
        "substring(trim(both from customer.name) from '#\"%#\" %' for '#') AS fname " .
        "FROM ar, customer WHERE ( $where ) AND " .
        "customer.id = ar.customer_id " .
        "ORDER BY lname, fname, ar.customer_id, ar.transdate");
    }
    if ($sl_err) die($sl_err);

    $stmt = array();
    $stmt_count = 0;

    for ($irow = 0; $irow < SLRowCount($res); ++$irow) {
      $row = SLGetRow($res, $irow);

      // Determine the date of service.  An 8-digit encounter number is
      // presumed to be a date of service imported during conversion.
      // Otherwise look it up in the form_encounter table.
      //
      $svcdate = "";
      list($pid, $encounter) = explode(".", $row['invnumber']);
      if (strlen($encounter) == 8) {
        $svcdate = substr($encounter, 0, 4) . "-" . substr($encounter, 4, 2) .
          "-" . substr($encounter, 6, 2);
      } else if ($encounter) {
        $tmp = sqlQuery("SELECT date FROM form_encounter WHERE " .
          "encounter = $encounter");
        $svcdate = substr($tmp['date'], 0, 10);
      }

      // How many times have we dunned them for this invoice?
      $intnotes = trim($row['intnotes']);
      $duncount = substr_count(strtolower($intnotes), "statement sent");

      // If this is a new patient then print the pending statement
      // and start a new one.  This is an associative array:
      //
      //  cid     = SQL-Ledger customer ID
      //  pid     = OpenEMR patient ID
      //  patient = patient name
      //  amount  = total amount due
      //  adjust  = adjustments (already applied to amount)
      //  duedate = due date of the oldest included invoice
      //  age     = number of days from duedate to today
      //  to      = array of addressee name/address lines
      //  lines   = array of:
      //    dos     = date of service "yyyy-mm-dd"
      //    desc    = description
      //    amount  = charge less adjustments
      //    paid    = amount paid
      //    notice  = 1 for first notice, 2 for second, etc.
      //    detail  = array of details, see invoice_summary.inc.php
      //
      if ($stmt['cid'] != $row['customer_id']) {
        if (!empty($stmt)) ++$stmt_count;
        fwrite($fhprint, create_statement($stmt));
        $stmt['cid'] = $row['customer_id'];
        $stmt['pid'] = $pid;

        if ($got_address_table) {
          $stmt['patient'] = $row['fname'] . ' ' . $row['lname'];
          $stmt['to'] = array($row['fname'] . ' ' . $row['lname']);
        } else {
          $stmt['patient'] = $row['name'];
          $stmt['to'] = array($row['name']);
        }

        if ($row['address1']) $stmt['to'][] = $row['address1'];
        if ($row['address2']) $stmt['to'][] = $row['address2'];
        $stmt['to'][] = $row['city'] . ", " . $row['state'] . " " . $row['zipcode'];
        $stmt['lines'] = array();
        $stmt['amount'] = '0.00';
        $stmt['today'] = $today;
        $stmt['duedate'] = $row['duedate'];
      } else {
        // Report the oldest due date.
        if ($row['duedate'] < $stmt['duedate']) {
          $stmt['duedate'] = $row['duedate'];
        }
      }

      $stmt['age'] = round((strtotime($today) - strtotime($stmt['duedate'])) /
        (24 * 60 * 60));

      $invlines = get_invoice_summary($row['id'], true); // true added by Rod 2006-06-09
      foreach ($invlines as $key => $value) {
        $line = array();
        $line['dos']     = $svcdate;
        $line['desc']    = ($key == 'CO-PAY') ? "Patient Payment" : "Procedure $key";
        $line['amount']  = sprintf("%.2f", $value['chg']);
        $line['adjust']  = sprintf("%.2f", $value['adj']);
        $line['paid']    = sprintf("%.2f", $value['chg'] - $value['bal']);
        $line['notice']  = $duncount + 1;
        $line['detail']  = $value['dtl']; // Added by Rod 2006-06-09
        $stmt['lines'][] = $line;
        $stmt['amount']  = sprintf("%.2f", $stmt['amount'] + $value['bal']);
      }

      // Record something in ar.intnotes about this statement run.
      if ($intnotes) $intnotes .= "\n";
      $intnotes = addslashes($intnotes . "Statement sent $today");
      if (! $DEBUG && ! $_POST['form_without']) {
        SLQuery("UPDATE ar SET intnotes = '$intnotes' WHERE id = " . $row['id']);
        if ($sl_err) die($sl_err);
      }
    } // end for

    if (!empty($stmt)) ++$stmt_count;
    fwrite($fhprint, create_statement($stmt));
    fclose($fhprint);
    sleep(1);

    // Download or print the file, as selected
    if ($_POST['form_download']) {
      upload_file_to_client($STMT_TEMP_FILE);
    } elseif ($_POST['form_pdf']) {
      upload_file_to_client_pdf($STMT_TEMP_FILE);
    } else { // Must be print!
      if ($DEBUG) {
        $alertmsg = xl("Printing skipped; see test output in") .' '. $STMT_TEMP_FILE;
      } else {
        exec("$STMT_PRINT_CMD $STMT_TEMP_FILE");
        if ($_POST['form_without']) {
          $alertmsg = xl('Now printing') .' '. $stmt_count .' '. xl('statements; invoices will not be updated.');
        } else {
          $alertmsg = xl('Now printing') .' '. $stmt_count .' '. xl('statements and updating invoices.');
        }
      } // end not debug
    } // end if form_download
  } // end statements requested
} // end not $INTEGRATED_AR
?>
<html>
<head>
<?php html_header_show(); ?>
<link rel=stylesheet href="<?php echo $css_header;?>" type="text/css">
<title><?php xl('Patient Statement','e'); ?></title>
<script type="text/javascript" src="../../library/textformat.js"></script>
<link rel='stylesheet' type='text/css' href='css/jquery.dataTables.css'>
<link rel='stylesheet' type='text/css' href='css/dataTables.tableTools.css'>
<link rel='stylesheet' type='text/css' href='css/dataTables.colVis.css'>
<link rel='stylesheet' type='text/css' href='css/dataTables.colReorder.css'>
<script type='text/javascript' src='js/jquery-1.11.1.min.js'></script>
<script type='text/javascript' src='js/jquery.dataTables.min.js'></script>
<script type='text/javascript' src='js/dataTables.tableTools.js'></script>
<script type='text/javascript' src='js/dataTables.colReorder.js'></script>
<script type='text/javascript' src='js/dataTables.colVis.js'></script>
<script type='text/javascript'>
    $(document).ready(function() {
        $('#patlistselect').hide();
        $('#patientstatement').dataTable( {
        iDisplayLength: 100,
        dom: 'T<\"clear\">lfrtip',
        columnDefs: [
            {
                targets: [ 0,1,2,3,4,5 ],
                visible: false
            }
        ],
        tableTools: {
            aButtons: [
                {
                    sExtends: "xls",
                    sButtonText: "Save to Excel",
                    sFileName: $('#openemrTitle').val() + " patient statement "+ $('#currTime').val() +".csv",
                    mColumns: "visible"
                }
            ]
        }
        } ); 
        
        $("#patlist").click(function(){
            fromdate = $('#form_from_date').val();
            todate = $('#form_tol_date').val();
            
            $.ajax({
            type: 'POST',
            url: 'patient-statment-patientlist.php',
            dataType:'json',
            data: {                        
                    fromD: fromdate,
                    toD: todate
                },
            beforeSend: function(){
                $('#patlistselect').hide();
                $('#calmsg').show();
                $('#calmsg').html("Please Wait...");
              },    
            success: function(response)
            {
                $('#patlistselect').show();
                $.each(response, function(i, value) {
                    $('#patlistselect').append($('<option>').text(value).attr('value', i));
                });
                
                $('#calmsg').hide();
            },
            failure: function(response)
            {
                alert("error");
            }		
            });
        });
    });     
    
    function showpracticeaddr(){
        fnShowHide(6);
        fnShowHide(7);
        fnShowHide(8);
        fnShowHide(9);
        fnShowHide(10);
        fnShowHide(11);
        fnShowHide(12);
    }
    function showbillingaddr(){
        fnShowHide(15);
        fnShowHide(16);
        fnShowHide(17);
        fnShowHide(18);
        fnShowHide(19);
    }
    function fnShowHide( iCol )
    {
        /* Get the DataTables object again - this is not a recreation, just a get of the object */
        var oTable = $('#patientstatement').dataTable();

        var bVis = oTable.fnSettings().aoColumns[iCol].bVisible;
        oTable.fnSetColumnVis( iCol, bVis ? false : true );
    }
</script>

<script language="JavaScript">

var mypcc = '1';

function checkAll(checked) {
 var f = document.forms[0];
 for (var i = 0; i < f.elements.length; ++i) {
  var ename = f.elements[i].name;
  if (ename.indexOf('form_cb[') == 0)
   f.elements[i].checked = checked;
 }
}

function npopup(pid) {
 window.open('sl_eob_patient_note.php?patient_id=' + pid, '_blank', 'width=500,height=250,resizable=1');
 return false;
}

</script>

</head>

<body style="background-color:#FFFFCC;">
<center>
<form method='post' action='patient-statement.php' enctype='multipart/form-data'>
<table border='0' cellpadding='5' cellspacing='0'>
 <tr>

<?php
if ($INTEGRATED_AR) {
  $payerID = $_POST['form_payer_id'];
  $insuredID = $_POST['insuredID'];
  $defaultFacility = $_POST['defaultFacility'];
  $livingFacility = $_POST['livingFacility'];
  $patlistselect = $_POST['patlistselect'];
  $livingFacility = $_POST['livingFacility'];
  
  if($payerID):
      foreach ($payerID as $value):
        $payerIDarray[]=$value;
      endforeach;
  endif;
  $payerIdStr = "";
  if(count($payerIDarray)>0):
     $payerIdStr = implode(',', $payerIDarray);
  endif;
  
  if($insuredID):
      foreach ($insuredID as $value):
        $insuredIDarray[]=$value;
      endforeach;
  endif;
  $insuredIdStr = "";
  if(count($insuredIDarray)>0):
     $insuredIdStr = implode("','", $insuredIDarray);
  endif;
  if($defaultFacility):
      foreach ($defaultFacility as $value):
        $defaultFacilityarray[]=$value;
      endforeach;
  endif;
  $defaultFacilityStr = "";
  if(count($defaultFacilityarray)>0):
     $defaultFacilityStr = implode(',', $defaultFacilityarray);
  endif;
  
  if($livingFacility):
      foreach ($livingFacility as $value):
        $livingFacilityarray[]=$value;
      endforeach;
  endif;
  $livingFacilityStr = "";
  if(count($livingFacilityarray)>0):
     $livingFacilityStr = implode("','", $livingFacilityarray);
  endif;
  
  if($patlistselect):
      foreach ($patlistselect as $value):
        $patlistselectarray[]=$value;
      endforeach;
  endif;
  $patlistselectStr = "";
  if(count($patlistselectarray)>0):
     $patlistselectStr = implode(',', $patlistselectarray);
  endif;
  
    $from=$_POST['form_from_date'];
    $to=$_POST['form_tol_date'];
    if (! $_POST['form_from_date']) {
        // If a specific patient, default to 2 years ago.
        $tmp = date('Y');
        $from = date("$tmp-m-d");
        $to = date("$tmp-m-d");
    }
  
  
  // Identify the payer to support resumable posting sessions.
  echo "  <td>\n";
  echo "   " . xl('Payer') . ":\n";
  echo "  </td>\n";
  echo "  <td>\n";
  $insurancei = getInsuranceProviders();
  echo "   <select name='form_payer_id[]' multiple>\n";
  echo "    <option value='0'>-- " . xl('Patient') . " --</option>\n";
  foreach ($insurancei as $iid => $iname) {
    echo "<option value='$iid'";
    if (in_array($iid, $payerIDarray)) echo " selected";
    echo ">" . $iname . "</option>\n";
  }
  echo "   </select>\n";
  ?>
     <br /><input type="checkbox" name="payerck" onchange="fnShowHide(0)">Show/hide Payer column
  <?php   
  echo "  </td>\n";
}
?>

  <td>
   <?php xl('Source:','e'); ?>
  
   <input type='text' name='form_source' size='10' value='<?php echo $_POST['form_source']; ?>'
    title='<?php xl("A check number or claim number to identify the payment","e"); ?>'>
  
   <?php xl('Pay Date:','e'); ?>
  
   <input type='text' name='form_paydate' size='10' value='<?php echo $_POST['form_paydate']; ?>'
    onkeyup='datekeyup(this,mypcc)' onblur='dateblur(this,mypcc)'
    title='<?php xl("Date of payment yyyy-mm-dd","e"); ?>'>
  

<?php if ($INTEGRATED_AR) { // include deposit date ?>
  
   <?php xl('Deposit Date:','e'); ?>
  
   <input type='text' name='form_deposit_date' size='10' value='<?php echo $_POST['form_deposit_date']; ?>'
    onkeyup='datekeyup(this,mypcc)' onblur='dateblur(this,mypcc)'
    title='<?php xl("Date of bank deposit yyyy-mm-dd","e"); ?>'>
  
<?php } ?>

  
   <?php xl('Amount:','e'); ?>
  
   <input type='text' name='form_amount' size='10' value='<?php echo $_POST['form_amount']; ?>'
    title='<?php xl("Paid amount that you will allocate","e"); ?>'>
  </td>
  
 </tr>
 <tr><td>Insured Type</td>
     <td>
         <select name="insuredID[]" multiple>
            <?php
            $insuredQuery = sqlStatement("SELECT option_id, title FROM list_options WHERE list_id='userlist1'");
            while($insuredRow = sqlFetchArray($insuredQuery)){
                ?>
                <option value="<?php echo $insuredRow['option_id']; ?>" <?php if(in_array($insuredRow['option_id'],$insuredID)): ?> selected <?php endif; ?>><?php echo $insuredRow['title']; ?></option>
                <?php
            }
            ?>
         </select> <br /> <input type="checkbox" name="insuredck" onchange="fnShowHide(1)">Show/hide Insured Type column  
     </td>
     <td>Default Facility
         <select name="defaultFacility[]" multiple>
            <?php
            $facilityQuery = sqlStatement("SELECT id, name FROM facility");
            while($facilityRow = sqlFetchArray($facilityQuery)){
                ?>
                <option value="<?php echo $facilityRow['id']; ?>" <?php if(in_array($facilityRow['id'],$defaultFacility)): ?> selected <?php endif; ?>><?php echo $facilityRow['name']; ?></option>
                <?php
            }
            ?>
         </select> <br /> <input type="checkbox" name="facilityck" onchange="fnShowHide(2)">Show/hide Default Facility column 
     </td>
 </tr>
 <tr><td></td>
     <td><fieldset><legend>Patients by Appointments</legend><?php xl('From','e'); ?>:<input type='text' name='form_from_date' id="form_from_date"
                                    size='10' value='<?php echo $from ?>'
                                    onkeyup='datekeyup(this,mypcc)' onblur='dateblur(this,mypcc)'
                                    title='yyyy-mm-dd'> <img src='../pic/show_calendar.gif'
                                    align='absbottom' width='24' height='22' id='img_from_date'
                                    border='0' alt='[?]' style='cursor: pointer'
                                    title='<?php xl('Click here to choose a date','e'); ?>'>
                                    &nbsp; <?php xl('To','e'); ?>:<input type='text' name='form_tol_date' id="form_tol_date"
                                    size='10' value='<?php echo $to ?>'
                                    onkeyup='datekeyup(this,mypcc)' onblur='dateblur(this,mypcc)'
                                    title='yyyy-mm-dd'> <img src='../pic/show_calendar.gif'
                                    align='absbottom' width='24' height='22' id='img_to_date'
                                    border='0' alt='[?]' style='cursor: pointer'
                                    title='<?php xl('Click here to choose a date','e'); ?>'>
                                    <input type="button" name="patlist" id="patlist" value="Get Patients"><br /><span id="calmsg"></span>
                                    <br /><select name="patlistselect[]" id="patlistselect" multiple></select><br /><input type="checkbox" onchange="fnShowHide(4)">Show/hide Appointment Date
         </fieldset></td>
         <td>Living Facility
         <select name="livingFacility[]" multiple>
            <?php
            $livingfacilityQuery = sqlStatement("SELECT option_id, title FROM list_options WHERE list_id='Living_Facility'");
            while($livingfacilityRow = sqlFetchArray($livingfacilityQuery)){
                ?>
                <option value="<?php echo $livingfacilityRow['option_id']; ?>" <?php if(in_array($livingfacilityRow['option_id'],$livingFacility)): ?> selected <?php endif; ?>><?php echo $livingfacilityRow['title']; ?></option>
                <?php
            }
            ?>
         </select> <br /> <input type="checkbox" name="livingfacilityck" onchange="fnShowHide(3)">Show/hide Living Facility column 
        </td>
 </tr> 
 <tr><td></td>
     <td><input type="checkbox" onchange="fnShowHide(5)">Show/hide Billing Note<br />
     <input type="checkbox" onchange="showpracticeaddr()" checked>Show/hide Practice Details<br/>
     <input type="checkbox" onchange="showbillingaddr()" checked>Show/hide Billing Address</td>
 </tr>
</table>

<table border='0' cellpadding='5' cellspacing='0'>

 <tr bgcolor='#ddddff'>
  <td>
   <?php xl('Name:','e'); ?>
  </td>
  <td>
   <input type='text' name='form_name' size='10' value='<?php echo $_POST['form_name']; ?>'
    title='<?php xl("Any part of the patient name, or \"last,first\", or \"X-Y\"","e"); ?>'>
  </td>
  <td>
   <?php xl('Chart ID:','e'); ?>
  </td>
  <td>
   <input type='text' name='form_pid' size='10' value='<?php echo $_POST['form_pid']; ?>'
    title='<?php xl("Patient chart ID","e"); ?>'>
  </td>
  <td>
   <?php xl('Encounter:','e'); ?>
  </td>
  <td>
   <input type='text' name='form_encounter' size='10' value='<?php echo $_POST['form_encounter']; ?>'
    title='<?php xl("Encounter number","e"); ?>'>
  </td>
  <td>
   <?php xl('Svc Date:','e'); ?>
  </td>
  <td>
   <input type='text' name='form_date' size='10' value='<?php echo $_POST['form_date']; ?>'
    title='<?php xl("Date of service mm/dd/yyyy","e"); ?>'>
  </td>
  <td>
   <?php xl('To:','e'); ?>
  </td>
  <td>
   <input type='text' name='form_to_date' size='10' value='<?php echo $_POST['form_to_date']; ?>'
    title='<?php xl("Ending DOS mm/dd/yyyy if you wish to enter a range","e"); ?>'>
  </td>
  <td>
   <select name='form_category'>
<?php
 foreach (array(xl('Open'), xl('All'), xl('Due Pt'), xl('Due Ins')) as $value) {
  echo "    <option value='$value'";
  if ($_POST['form_category'] == $value) echo " selected";
  echo ">$value</option>\n";
 }
?>
   </select>
  </td>
  
 </tr>
 <tr>
    <td class='label'>
       <?php xl('Age By','e') ?>:
    </td>
    <td>
       <select name='form_ageby'>
    <?php
     foreach (array('Service Date', 'Last Activity Date') as $value) {
      echo "    <option value='$value'";
      if ($_POST['form_ageby'] == $value) echo " selected";
      echo ">" . xl($value) . "</option>\n";
     }
    ?>
       </select>
    </td>
    <td class='label'>
       <?php xl('Aging Columns:','e') ?>
    </td>
    <td>
       <input type='text' name='form_age_cols' size='2' value='<?php echo $form_age_cols; ?>' />
    </td>
    <td class='label'>
       <?php xl('Days/Col:','e') ?>
    </td>
    <td>
       <input type='text' name='form_age_inc' size='3' value='<?php echo $form_age_inc; ?>' />
    </td>
</tr>
<tr>
    <td>
        <input type='submit' name='form_search' value='<?php xl("Search","e"); ?>'>
    </td>
</tr>

 <tr>
  <td height="1" colspan="10">
  </td>
 </tr>

</table>
<?php
//if ($_POST['form_search'] || $_POST['form_print']) {
  $form_name      = trim($_POST['form_name']);
  $form_pid       = trim($_POST['form_pid']);
  $form_encounter = trim($_POST['form_encounter']);
  $form_date      = fixDate($_POST['form_date'], "");
  $form_to_date   = fixDate($_POST['form_to_date'], "");
  
  // New Ageing fields added
  $is_ageby_lad   = strpos($_POST['form_ageby'], 'Last') !== false;
  $form_age_cols = (int) $_POST['form_age_cols'];
  $form_age_inc  = (int) $_POST['form_age_inc'];
  if ($form_age_cols > 0 && $form_age_cols < 50) {
    if ($form_age_inc <= 0) $form_age_inc = 30;
  } else {
    $form_age_cols = 0;
    $form_age_inc  = 0;
  }

  $where = "";

  // Handle X12 835 file upload.
  //
  if ($_FILES['form_erafile']['size']) {
    $tmp_name = $_FILES['form_erafile']['tmp_name'];

    // Handle .zip extension if present.  Probably won't work on Windows.
    if (strtolower(substr($_FILES['form_erafile']['name'], -4)) == '.zip') {
      rename($tmp_name, "$tmp_name.zip");
      exec("unzip -p $tmp_name.zip > $tmp_name");
      unlink("$tmp_name.zip");
    }

    echo "<!-- Notes from ERA upload processing:\n";
    $alertmsg .= parse_era($tmp_name, 'era_callback');
    echo "-->\n";
    $erafullname = $GLOBALS['OE_SITE_DIR'] . "/era/$eraname.edi";

    if (is_file($erafullname)) {
      $alertmsg .= "Warning: Set $eraname was already uploaded ";
      if (is_file($GLOBALS['OE_SITE_DIR'] . "/era/$eraname.html"))
        $alertmsg .= "and processed. ";
      else
        $alertmsg .= "but not yet processed. ";
    }
    rename($tmp_name, $erafullname);
  } // End 835 upload

  if ($INTEGRATED_AR) {
    if ($eracount) {
      // Note that parse_era() modified $eracount and $where.
      if (! $where) $where = '1 = 2';
    }
    else {
      if ($form_name) {
        if ($where) $where .= " AND ";
        // Allow the last name to be followed by a comma and some part of a first name.
        if (preg_match('/^(.*\S)\s*,\s*(.*)/', $form_name, $matches)) {
          $where .= "p.lname LIKE '" . $matches[1] . "%' AND p.fname LIKE '" . $matches[2] . "%'";
        // Allow a filter like "A-C" on the first character of the last name.
        } else if (preg_match('/^(\S)\s*-\s*(\S)$/', $form_name, $matches)) {
          $tmp = '1 = 2';
          while (ord($matches[1]) <= ord($matches[2])) {
            $tmp .= " OR p.lname LIKE '" . $matches[1] . "%'";
            $matches[1] = chr(ord($matches[1]) + 1);
          }
          $where .= "( $tmp ) ";
        } else {
          $where .= "p.lname LIKE '%$form_name%'";
        }
      }
      if ($form_pid) {
        if ($where) $where .= " AND ";
        $where .= "f.pid = '$form_pid'";
      }
      if ($form_encounter) {
        if ($where) $where .= " AND ";
        $where .= "f.encounter = '$form_encounter'";
      }
      if ($form_date) {
        if ($where) $where .= " AND ";
        if ($form_to_date) {
          $where .= "f.date >= '$form_date' AND f.date <= '$form_to_date'";
        }
        else {
          $where .= "f.date = '$form_date'";
        }
      }
      if (! $where) {
        if ($_POST['form_category'] == 'All') {
          die(xl("At least one search parameter is required if you select All."));
        } else {
          $where = "1 = 1";
        }
      }
    }
    
    //Subhan
    $formPayerId = $payerIdStr;
    $payerClause = "";
    if($formPayerId != ''):
        $payerClause = " AND bb.payer_id IN (".$formPayerId.")";
    endif;
    
    $insuranceClause = $faciltyClause = $patlistselectClause = $livingfaciltyClause = "";
    if($insuredIdStr != ''):
        $insuranceClause = " AND p.insured_Type IN ('". $insuredIdStr ."')";
    endif;
    if($defaultFacilityStr != ''):
        $faciltyClause = " AND p.patient_facility IN (". $defaultFacilityStr.")";
    endif;
    
    if($livingFacilityStr != ''):
        $livingfaciltyClause = " AND p.living_facility IN ('". $livingFacilityStr."')";
    endif;
    
    if($patlistselectStr != ''):
        $patlistselectClause = " AND p.pid IN (". $patlistselectStr.")";
    endif;
    

    // Notes that as of release 4.1.1 the copays are stored
    // in the ar_activity table marked with a PCP in the account_code column.
    $query = "SELECT DISTINCT f.encounter, f.id, f.pid, f.date,f.billing_note, " .
      "f.last_level_billed, f.last_level_closed, f.last_stmt_date, f.stmt_count, " .
      "p.fname, p.mname, p.lname,p.street, p.city, p.state, p.postal_code, p.genericname2, p.genericval2, " .
      "( SELECT SUM(b.fee) FROM billing AS b WHERE " .
      "b.pid = f.pid AND b.encounter = f.encounter AND " .
      "b.activity = 1 AND b.code_type != 'COPAY' ) AS charges, " .
      "( SELECT SUM(a.pay_amount) FROM ar_activity AS a WHERE " .
      "a.pid = f.pid AND a.encounter = f.encounter AND a.payer_type = 0 AND a.account_code = 'PCP')*-1 AS copays, " .
      "( SELECT SUM(a.pay_amount) FROM ar_activity AS a WHERE " .
      "a.pid = f.pid AND a.encounter = f.encounter AND a.account_code != 'PCP') AS payments, " .
      "( SELECT SUM(a.adj_amount) FROM ar_activity AS a WHERE " .
      "a.pid = f.pid AND a.encounter = f.encounter ) AS adjustments, " .
           
      "(SELECT name FROM insurance_companies ins WHERE ins.id = bb.payer_id) as payername,".      
            
      "(SELECT title FROM  list_options liop WHERE  liop.list_id =  'userlist1' AND liop.option_id = p.insured_Type) as insuredType, ".
            
      "(SELECT title FROM  list_options lif WHERE  lif.list_id =  'Living_Facility' AND lif.option_id = p.living_facility) as livingFacility, ".
            
      "(SELECT name FROM  facility fl WHERE  fl.id = p.patient_facility) as defaultfacility ". 
            
      "FROM form_encounter AS f " .
      "JOIN billing AS bb ON f.encounter = bb.encounter " .      
      "JOIN patient_data AS p ON p.pid = f.pid " .
      "WHERE $where $payerClause $insuranceClause $faciltyClause $livingfaciltyClause $patlistselectClause AND bb.provider_id !=0 AND bb.payer_id !='' " .
      "ORDER BY p.lname, p.fname, p.mname, f.pid, f.encounter";
    // Note that unlike the SQL-Ledger case, this query does not weed
    // out encounters that are paid up.  Also the use of sub-selects
    // will require MySQL 4.1 or greater.

    // echo "<!-- $query -->\n"; // debugging
    
    $t_res = sqlStatement($query);

    $num_invoices = mysql_num_rows($t_res);
    if ($eracount && $num_invoices != $eracount) {
      $alertmsg .= "Of $eracount remittances, there are $num_invoices " .
        "matching encounters in OpenEMR. ";
    }
  } // end $INTEGRATED_AR
  else {
    if ($eracount) {
      // Note that parse_era() modified $eracount and $where.
      if (! $where) $where = '1 = 2';
    }
    else {
      if ($form_name) {
        if ($where) $where .= " AND ";
        // Allow the last name to be followed by a comma and some part of a first name.
        if (preg_match('/^(.*\S)\s*,\s*(.*)/', $form_name, $matches)) {
          $where .= "customer.name ILIKE '" . $matches[2] . '% ' . $matches[1] . "%'";
        // Allow a filter like "A-C" on the first character of the last name.
        } else if (preg_match('/^(\S)\s*-\s*(\S)$/', $form_name, $matches)) {
          $tmp = '1 = 2';
          while (ord($matches[1]) <= ord($matches[2])) {
            // $tmp .= " OR customer.name ILIKE '% " . $matches[1] . "%'";
            // Fixing the above which was also matching on middle names:
            $tmp .= " OR customer.name ~* ' " . $matches[1] . "[A-Z]*$'";
            $matches[1] = chr(ord($matches[1]) + 1);
          }
          $where .= "( $tmp ) ";
        } else {
          $where .= "customer.name ILIKE '%$form_name%'";
        }
      }
      if ($form_pid && $form_encounter) {
        if ($where) $where .= " AND ";
        $where .= "ar.invnumber = '$form_pid.$form_encounter'";
      }
      else if ($form_pid) {
        if ($where) $where .= " AND ";
        $where .= "ar.invnumber LIKE '$form_pid.%'";
      }
      else if ($form_encounter) {
        if ($where) $where .= " AND ";
        $where .= "ar.invnumber like '%.$form_encounter'";
      }

      if ($form_date) {
        if ($where) $where .= " AND ";
        $date1 = substr($form_date, 0, 4) . substr($form_date, 5, 2) .
          substr($form_date, 8, 2);
        if ($form_to_date) {
          $date2 = substr($form_to_date, 0, 4) . substr($form_to_date, 5, 2) .
            substr($form_to_date, 8, 2);
          $where .= "((CAST (substring(ar.invnumber from position('.' in ar.invnumber) + 1 for 8) AS integer) " .
            "BETWEEN '$date1' AND '$date2')";
          $tmp = "date >= '$form_date' AND date <= '$form_to_date'";
        }
        else {
          // This catches old converted invoices where we have no encounters:
          $where .= "(ar.invnumber LIKE '%.$date1'";
          $tmp = "date = '$form_date'";
        }
        // Pick out the encounters from MySQL with the desired DOS:
        $rez = sqlStatement("SELECT pid, encounter FROM form_encounter WHERE $tmp");
        while ($row = sqlFetchArray($rez)) {
          $where .= " OR ar.invnumber = '" . $row['pid'] . "." . $row['encounter'] . "'";
        }
        $where .= ")";
      }

      if (! $where) {
        if ($_POST['form_category'] == 'All') {
          die(xl("At least one search parameter is required if you select All."));
        } else {
          $where = "1 = 1";
        }
      }
    }

    $query = "SELECT ar.id, ar.invnumber, ar.duedate, ar.amount, ar.paid, " .
      "ar.intnotes, ar.notes, ar.shipvia, customer.name, customer.id AS custid, ";
    if ($got_address_table) $query .=
      "substring(trim(both from customer.name) from '#\"%#\" %' for '#') AS lname, " .
      "substring(trim(both from customer.name) from '% #\"%#\"' for '#') AS fname, ";
    else $query .=
      "substring(trim(both from customer.name) from '% #\"%#\"' for '#') AS lname, " .
      "substring(trim(both from customer.name) from '#\"%#\" %' for '#') AS fname, ";
    $query .=
      "(SELECT SUM(invoice.sellprice * invoice.qty) FROM invoice WHERE " .
      "invoice.trans_id = ar.id AND invoice.sellprice > 0) AS charges, " .
      "(SELECT SUM(invoice.sellprice * invoice.qty) FROM invoice WHERE " .
      "invoice.trans_id = ar.id AND invoice.sellprice < 0) AS adjustments " .
      "FROM ar, customer WHERE ( $where ) AND customer.id = ar.customer_id ";
    if ($_POST['form_category'] != 'All' && !$eracount) {
      $query .= "AND ar.amount != ar.paid ";
      // if ($_POST['form_category'] == 'Due') {
      //   $query .= "AND ar.duedate <= CURRENT_DATE ";
      // }
    }
    $query .= "ORDER BY lname, fname, ar.invnumber";

    // echo "<!-- $query -->\n"; // debugging
    
    $t_res = SLQuery($query);
    if ($sl_err) die($sl_err);

    $num_invoices = SLRowCount($t_res);
    if ($eracount && $num_invoices != $eracount) {
      $alertmsg .= "Of $eracount remittances, there are $num_invoices " .
        "matching claims in OpenEMR. ";
    }

  } // end not $INTEGRATED_AR
?>

<table border='0' cellpadding='1' cellspacing='2' width='98%' id="patientstatement" class="display">
    <thead>
 <tr>
  <th>
   <?php xl('Payer','e'); ?>
  </th>
  <th>
   <?php xl('Insured Type','e'); ?>
  </th>
  <th>
   <?php xl('Default Facility','e'); ?>
  </th>
  <th>
   <?php xl('Living Facility','e'); ?>
  </th>
  <th>
   <?php xl('Appointment Date','e'); ?>
  </th>
  <th>
   <?php xl('Billing Note','e'); ?>
  </th>
  <th>
   <?php xl('Zirmed Id','e'); ?>
  </th>
  <th>
   <?php xl('Practice Name','e'); ?>
  </th>
  <th>
   <?php xl('Practice Address','e'); ?>
  </th>
  <th>
   <?php xl('Practice City','e'); ?>
  </th>
  <th>
   <?php xl('Practice State','e'); ?>
  </th>
  <th>
   <?php xl('Practice Zip','e'); ?>
  </th>
  <th>
   <?php xl('Statement Date','e'); ?>
  </th>
  <th>
   <?php xl('Invoice','e'); ?>
  </th>
  <th>
   <?php xl('Account Number','e'); ?>
  </th>
  <th>
   <?php xl('Bill To','e'); ?>
  </th>
  <th>
   <?php xl('Bill To Address','e'); ?>
  </th>
  <th>
   <?php xl('Bill To City','e'); ?>
  </th>
  <th>
   <?php xl('Bill To State','e'); ?>
  </th>
  <th>
   <?php xl('Bill To Zip','e'); ?>
  </th>
  <th>
   <?php xl('DOS','e'); ?>
  </th>
  <th>
   <?php xl('Procedure','e'); ?>
  </th>
  <th>
   <?php xl('Description','e'); ?>
  </th>
  <th>
   <?php xl('DX Code','e'); ?>
  </th>
  <!--<th>
   <?php xl($INTEGRATED_AR ? 'Last Stmt' : 'Due Date','e'); ?>
  </th>-->
  <th align="right">
   <?php xl('Charge','e'); ?>
  </th>
  <th align="right">
   <?php xl('Paid','e'); ?>
  </th>
  <th align="right">
   <?php xl('Adjust','e'); ?>
  </th> 
  <th align="right">
   <?php xl('Balance','e'); ?>
  </th>
    <?php
    // Generate aging headers if appropriate, else balance header.
    if ($form_age_cols) {
      for ($c = 0; $c < $form_age_cols;) {
        echo "  <th class='dehead' align='right'>";
        echo $form_age_inc * $c;
        if (++$c < $form_age_cols) {
          echo "-" . ($form_age_inc * $c - 1);
        } else {
          echo "+";
        }
        echo "</th>\n";
      }
    }
    ?>
</tr>
 </thead>
<?php
  $orow = -1;

  if ($INTEGRATED_AR) {
    while ($row = sqlFetchArray($t_res)) {
      $balance = sprintf("%.2f", $row['charges'] + $row['copays'] - $row['payments'] - $row['adjustments']);

      if ($_POST['form_category'] != 'All' && $eracount == 0 && $balance == 0) continue;

      // $duncount was originally supposed to be the number of times that
      // the patient was sent a statement for this invoice.
      //
      $duncount = $row['stmt_count'];

      // But if we have not yet billed the patient, then compute $duncount as a
      // negative count of the number of insurance plans for which we have not
      // yet closed out insurance.
      //
      if (! $duncount) {
        for ($i = 1; $i <= 3 && arGetPayerID($row['pid'], $row['date'], $i); ++$i) ;
        $duncount = $row['last_level_closed'] + 1 - $i;
      }

      $isdueany = ($balance > 0);

      // An invoice is now due from the patient if money is owed and we are
      // not waiting for insurance to pay.
      //
      $isduept = ($duncount >= 0 && $isdueany) ? " checked" : "";

      // Skip invoices not in the desired "Due..." category.
      //
      if (substr($_POST['form_category'], 0, 3) == 'Due' && !$isdueany) continue;
      if ($_POST['form_category'] == 'Due Ins' && ($duncount >= 0 || !$isdueany)) continue;
      if ($_POST['form_category'] == 'Due Pt'  && ($duncount <  0 || !$isdueany)) continue;

      $bgcolor = ((++$orow & 1) ? "#ffdddd" : "#ddddff");

      $svcdate = substr($row['date'], 0, 10);
      $last_stmt_date = empty($row['last_stmt_date']) ? '' : $row['last_stmt_date'];

      // Determine if customer is in collections.
      //
      $billnote = ($row['genericname2'] == 'Billing') ? $row['genericval2'] : '';
      $in_collections = stristr($billnote, 'IN COLLECTIONS') !== false;
      
      if ($INTEGRATED_AR) {
        // Get invoice charge details.
        $codes = ar_get_invoice_summary($row['pid'], $row['encounter'], true);
      }
      else {
        // Get invoice charge details.
        $codes = get_invoice_summary($row['id'], true);
      }
      $dispcode = "";
      $dxCode = $payerId = $payerName = "";
      $dxCodeArr1 = $dxCodeArr2 = "";
      foreach ($codes as $code => $cdata) {
          $dispcode = $code;
          $dxQuery = sqlStatement("SELECT justify,payer_id FROM billing WHERE code = '".$code."' AND pid = ".$row['pid']." AND encounter =".$row['encounter']);
          while($rowTemp=sqlFetchArray($dxQuery)):
              $dxCodeArr1 = explode(":",$rowTemp['justify']);
              $dxCodeArr2 = explode("|",$dxCodeArr1[0]);
              $dxCode = $dxCodeArr2[1];
              $payerId = $rowTemp['payer_id'];
          endwhile;
      }
      
      $payId = arGetPayerID($row['pid'], $svcdate, 1);
      if($payId):
        $payerQuery = sqlQuery("SELECT name FROM insurance_companies WHERE id = ".$payId);
        $payerName = $payerQuery['name'];
      endif;  
      
      
      //$payerFilteredName  = empty($row['payername']) ? '' : $row['payername'];
      $payerFilteredName  = $payerName;
      $insuredType  = empty($row['insuredType']) ? '' : $row['insuredType'];
      $facility  = empty($row['defaultfacility']) ? '' : $row['defaultfacility'];
      $livfacility  = empty($row['livingFacility']) ? '' : $row['livingFacility'];
      $appdate  = empty($row['date']) ? '' : $row['date'];
      $formatappdate = date("Y-m-d", strtotime($appdate));
      $bnote  = empty($row['billing_note']) ? '' : $row['billing_note'];
     
?>
 <?php
    $mname = "";
    if($row['mname'] != ""):
        $mname = " ".$row['mname'];
    endif;
    
    $patient = $row['fname'].$mname.", ".$row['lname'];
    if(count($codes) > 1):
        foreach ($codes as $code => $cdata):
            $eachCharge = "";
            foreach($cdata['dtl'] as $c):
                $eachCharge = abs($c['chg']);
                // Get Actual Charge which comes in top of the loop and break
                break;
            endforeach;
            
//            foreach($cdata['dtl'] as $c):
//                echo "<pre>";
//                print_r ($c);
//                echo "</pre>";
//            endforeach;
            $balance = $cdata['bal'];
            if ($form_age_cols) {
              $agedate = $is_ageby_lad ? $last_stmt_date : $svcdate;
              $agetime = mktime(0, 0, 0, substr($agedate, 5, 2),substr($agedate, 8, 2), substr($agedate, 0, 4));
              $days = floor((time() - $agetime) / (60 * 60 * 24));
              $agecolno = min($form_age_cols - 1, max(0, floor($days / $form_age_inc)));
              $cdata['agedbal'][$agecolno] = $balance;
            }
            $bal = oeFormatMoney($balance);
            if($bal == 0):
                continue;
            endif;
        ?>
            <tr>
            <td>
             <?php echo $payerFilteredName; ?>
            </td>
            <td>
             <?php echo $insuredType; ?>
            </td>
            <td>
             <?php echo $facility; ?>
            </td>
            <td>
             <?php echo $livfacility; ?>   
            </td>
            <td>
             <?php echo $formatappdate; ?>
            </td>
            <td>
             <?php echo $bnote; ?>
            </td>
            <td>
             <?php echo $domain_identifier; ?>
            </td>
            <!--<td>
             <?php echo $payerName; ?>
            </td>-->
            <td>
             <?php echo $practiceName; ?>
            </td>
            <td>
             <?php echo $practiceAddr; ?>
            </td>
            <td>
             <?php echo $practiceCity; ?>
            </td>
            <td>
             <?php echo $practiceState; ?>
            </td>
            <td>
             <?php echo $practiceZip ?>
            </td>
            <td>
             <?php echo date('m/d/Y'); ?>
            </td>
            <td>
             <a href="../billing/sl_eob_invoice.php?id=<?php echo $row['id'] ?>"
              target="_blank"><?php echo $row['pid'] . '.' . $row['encounter']; ?></a>
            </td>
            <td>
             <?php echo $row['pid']; ?>
            </td>
            <td>
             <?php echo $patient; ?>
            </td>
            <td>
             <?php echo $row['street']; ?>
            </td>
            <td>
             <?php echo $row['city']; ?>
            </td>
            <td>
             <?php echo $row['state']; ?>
            </td>
            <td>
             <?php echo $row['postal_code']; ?>
            </td>
            <td>
             <?php echo oeFormatShortDate($svcdate) ?>
            </td>
            <td>
             <?php echo $code; ?>
            </td>
            <td>
             <?php echo $cdata['code_text']; ?>
            </td>
            <td>
             <?php echo $dxCode; ?>
            </td>
            <!--<td>
             <?php echo oeFormatShortDate($last_stmt_date) ?>
            </td>-->
            <?php
            // Compute invoice balance and aging column number, and accumulate aging.
            //echo "Last: ".$last_stmt_date." --- DOS: ". $svcdate."<br />";
            $balance = $cdata['bal'];
            if ($form_age_cols) {
              $agedate = $is_ageby_lad ? $last_stmt_date : $svcdate;
              $agetime = mktime(0, 0, 0, substr($agedate, 5, 2),substr($agedate, 8, 2), substr($agedate, 0, 4));
              $days = floor((time() - $agetime) / (60 * 60 * 24));
              $agecolno = min($form_age_cols - 1, max(0, floor($days / $form_age_inc)));
              $cdata['agedbal'][$agecolno] = $balance;
            }
//            echo substr($agedate, 5, 2) .",".substr($agedate, 8, 2).",".substr($agedate, 0, 4)."<br />";
//            echo "AgeDate: ". $agedate. "<br />";
//            echo "Days: ". $days."<br />";
//            echo "If Agecolno: ".$agecolno."<br />";
            ?>
            <td align="right">
             <?php echo $eachCharge; ?>
            </td>
            <td align="right">
             <?php echo $eachCharge - $cdata['adj'] - $cdata['bal']; ?>
            </td>
            <td align="right">
             <?php echo $cdata['adj']; ?>
            </td>
            <td class="detail" align="right"><?php bucks($balance) ?></td>
            <?php
            if ($form_age_cols) {
            for ($c = 0; $c < $form_age_cols; ++$c) {
            echo "<td class='detail' align='right'>";
            if ($c == $agecolno) {
            bucks($cdata['agedbal'][$agecolno]);
            }
            echo "</td>";
            }
            }
            ?>
           </tr>
        <?php
        endforeach;
    else:    
        //echo "Last: ".$last_stmt_date." --- DOS: ". $svcdate."<br />";
        $balance = $cdata['bal'];
            if ($form_age_cols) {
              $agedate = $is_ageby_lad ? $last_stmt_date : $svcdate;
              $agetime = mktime(0, 0, 0, substr($agedate, 5, 2),substr($agedate, 8, 2), substr($agedate, 0, 4));
              $days = floor((time() - $agetime) / (60 * 60 * 24));
              $agecolno = min($form_age_cols - 1, max(0, floor($days / $form_age_inc)));
              $cdata['agedbal'][$agecolno] = $balance;
            }
            $bal = oeFormatMoney($balance);
            if($bal == 0):
                continue;
            endif;
        ?>
            <tr>
            <td>
             <?php echo $payerFilteredName; ?>
            </td>
            <td>
             <?php echo $insuredType; ?>
            </td>
            <td>
             <?php echo $facility; ?>
            </td>
            <td>
             <?php echo $livfacility; ?>   
            </td>
            <td>
             <?php echo $formatappdate; ?>
            </td>
            <td>
             <?php echo $bnote; ?>
            </td>
            <td>
             <?php echo $domain_identifier; ?>
            </td>
            <!--<td>
             <?php echo $payerName; ?>
            </td>-->
            <td>
             <?php echo $practiceName; ?>
            </td>
            <td>
             <?php echo $practiceAddr; ?>
            </td>
            <td>
             <?php echo $practiceCity; ?>
            </td>
            <td>
             <?php echo $practiceState; ?>
            </td>
            <td>
             <?php echo $practiceZip ?>
            </td>
            <td>
             <?php echo date('m/d/Y'); ?>
            </td>
            <td>
             <a href="../billing/sl_eob_invoice.php?id=<?php echo $row['id'] ?>"
              target="_blank"><?php echo $row['pid'] . '.' . $row['encounter']; ?></a>
            </td>
            <td>
             <?php echo $row['pid']; ?>
            </td>
            <td>
             <?php echo $patient; ?>
            </td>
            <td>
             <?php echo $row['street']; ?>
            </td>
            <td>
             <?php echo $row['city']; ?>
            </td>
            <td>
             <?php echo $row['state']; ?>
            </td>
            <td>
             <?php echo $row['postal_code']; ?>
            </td>
            <td>
             <?php echo oeFormatShortDate($svcdate) ?>
            </td>
            <td>
             <?php echo $dispcode; ?>
            </td>
            <td>
             <?php echo $cdata['code_text']; ?>
            </td>
            <td>
             <?php echo $dxCode; ?>
            </td>
            <!--<td>
             <a href="../billing/sl_eob_invoice.php?id=<?php echo $row['id'] ?>"
              target="_blank"><?php echo $row['pid'] . '.' . $row['encounter']; ?></a>
            </td>
            <td>
             <?php echo oeFormatShortDate($last_stmt_date) ?>
            </td>-->
            <?php
            // Compute invoice balance and aging column number, and accumulate aging.
            //$balance = bucks($balance);
            if ($form_age_cols) {
              $agedate = $is_ageby_lad ? $last_stmt_date : $svcdate;
              $agetime = mktime(0, 0, 0, substr($agedate, 5, 2),substr($agedate, 8, 2), substr($agedate, 0, 4));
              $days = floor((time() - $agetime) / (60 * 60 * 24));
              $agecolno = min($form_age_cols - 1, max(0, floor($days / $form_age_inc)));
              $row['agedbal'][$agecolno] = $balance;
            }
//            echo "AgeDate: ". $agedate. "<br />";
//            echo substr($agedate, 5, 2) .",".substr($agedate, 8, 2).",".substr($agedate, 0, 4)."<br />";
//            echo "Days: ". $days."<br />";
//            echo "Else Agecolno: ".$agecolno."<br />";
            ?>
            <td align="right">
             <?php bucks($row['charges']) ?>
            </td>
            <td align="right">
             <?php bucks($row['payments'] - $row['copays']); ?>
            </td>
            <td align="right">
             <?php bucks($row['adjustments']) ?>
            </td>
            <td class="detail" align="right"><?php bucks($balance) ?></td>
            <?php
            if ($form_age_cols) {
            for ($c = 0; $c < $form_age_cols; ++$c) {
            echo "<td class='detail' align='right'>";
            if ($c == $agecolno) {
            bucks($row['agedbal'][$agecolno]);
            }
            echo "</td>";
            }
            }
            ?>
           </tr>
        <?php
    endif;
 ?>
 
<?php
    } // end while
  } // end $INTEGRATED_AR

  else { // not $INTEGRATED_AR
    for ($irow = 0; $irow < $num_invoices; ++$irow) {
      $row = SLGetRow($t_res, $irow);

      // $duncount was originally supposed to be the number of times that
      // the patient was sent a statement for this invoice.
      //
      $duncount = substr_count(strtolower($row['intnotes']), "statement sent");

      // But if we have not yet billed the patient, then compute $duncount as a
      // negative count of the number of insurance plans for which we have not
      // yet closed out insurance.
      //
      if (! $duncount) {
        $insgot = strtolower($row['notes']);
        $inseobs = strtolower($row['shipvia']);
        foreach (array('ins1', 'ins2', 'ins3') as $value) {
          if (strpos($insgot, $value) !== false &&
              strpos($inseobs, $value) === false)
            --$duncount;
        }
      }

//    $isdue = ($row['duedate'] <= $today && $row['amount'] > $row['paid']) ? " checked" : "";

      $isdueany = sprintf("%.2f",$row['amount']) > sprintf("%.2f",$row['paid']);

      // An invoice is now due from the patient if money is owed and we are
      // not waiting for insurance to pay.  We no longer look at the due date
      // for this.
      //
      $isduept = ($duncount >= 0 && $isdueany) ? " checked" : "";

      // Skip invoices not in the desired "Due..." category.
      //
      if (substr($_POST['form_category'], 0, 3) == 'Due' && !$isdueany) continue;
      if ($_POST['form_category'] == 'Due Ins' && ($duncount >= 0 || !$isdueany)) continue;
      if ($_POST['form_category'] == 'Due Pt'  && ($duncount <  0 || !$isdueany)) continue;

      $bgcolor = ((++$orow & 1) ? "#ffdddd" : "#ddddff");

      // Determine the date of service.  If this was a search parameter
      // then we already know it.  Or an 8-digit encounter number is
      // presumed to be a date of service imported during conversion.
      // Otherwise look it up in the form_encounter table.
      //
      $svcdate = "";
      list($pid, $encounter) = explode(".", $row['invnumber']);
      // if ($form_date) {
      //   $svcdate = $form_date;
      // } else
      if (strlen($encounter) == 8) {
        $svcdate = substr($encounter, 0, 4) . "-" . substr($encounter, 4, 2) .
          "-" . substr($encounter, 6, 2);
      }
      else if ($encounter) {
        $tmp = sqlQuery("SELECT date FROM form_encounter WHERE " .
          "encounter = $encounter");
        $svcdate = substr($tmp['date'], 0, 10);
      }

      // Get billing note to determine if customer is in collections.
      //
      $pdrow = sqlQuery("SELECT pd.genericname2, pd.genericval2 FROM " .
        "integration_mapping AS im, patient_data AS pd WHERE " .
        "im.foreign_id = " . $row['custid'] . " AND " .
        "im.foreign_table = 'customer' AND " .
        "pd.id = im.local_id");
      $row['billnote'] = ($pdrow['genericname2'] == 'Billing') ? $pdrow['genericval2'] : '';
      $in_collections = stristr($row['billnote'], 'IN COLLECTIONS') !== false;
?>
 <tr bgcolor='<?php echo $bgcolor ?>'>
  <td>
   <a href="" onclick="return npopup(<?php echo $pid ?>)"
   ><?php echo $row['lname'] . ', ' . $row['fname']; ?></a>
  </td>
  <td>
   <a href="sl_eob_invoice.php?id=<?php echo $row['id'] ?>"
    target="_blank"><?php echo $row['invnumber'] ?></a>
  </td>
  <td>
   <?php echo oeFormatShortDate($svcdate) ?>
  </td>
  <td>
   <?php echo oeFormatShortDate($row['duedate']) ?>
  </td>
  <td align="right">
   <?php bucks($row['charges']) ?>
  </td>
  <td align="right">
   <?php bucks($row['adjustments']) ?>
  </td>
  <td align="right">
   <?php bucks($row['paid']) ?>
  </td>
  <td align="right">
   <?php bucks($row['charges'] + $row['adjustments'] - $row['paid']) ?>
  </td>
  <td align="center">
   <?php echo $duncount ? $duncount : "" ?>
  </td>
<?php if (!$eracount) { ?>
  <td align="left">
   <input type='checkbox' name='form_cb[<?php echo($row['id']) ?>]'<?php echo $isduept ?> />
   <?php if ($in_collections) echo "<b><font color='red'>IC</font></b>"; ?>
  </td>
<?php } ?>
 </tr>
<?php

    } // end for
  } // end not $INTEGRATED_AR
//} // end search/print logic

if (!$INTEGRATED_AR) SLClose();
?>

</table>

    <input type='hidden' id='openemrTitle' value='<?php echo text($openemr_name); ?>' />
    <input type='hidden' id='currTime' value='<?php echo time(); ?>' />
</form>
</center>
<script language="JavaScript">
 function processERA() {
  var f = document.forms[0];
  var debug = f.form_without.checked ? '1' : '0';
  var paydate = f.form_paydate.value;
  window.open('sl_eob_process.php?eraname=<?php echo $eraname ?>&debug=' + debug + '&paydate=' + paydate + '&original=original', '_blank');
  return false;
 }
<?php
if ($alertmsg) {
  echo "alert('" . htmlentities($alertmsg) . "');\n";
}

?>
</script>
</body>
<style type="text/css">
    @import url(../../library/dynarch_calendar.css);
</style>
<script type="text/javascript" src="../../library/dynarch_calendar.js"></script>
<?php include_once("{$GLOBALS['srcdir']}/dynarch_calendar_en.inc.php"); ?>
<script type="text/javascript"
	src="../../library/dynarch_calendar_setup.js"></script>
<script type="text/javascript">
 Calendar.setup({inputField:"form_from_date", ifFormat:"%Y-%m-%d", button:"img_from_date"});
 Calendar.setup({inputField:"form_tol_date", ifFormat:"%Y-%m-%d", button:"img_to_date"});
</script>
</html>
