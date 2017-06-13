<?php
ini_set('max_execution_time', 300);
// Copyright (C) 2005-2006 Rod Roark <rod@sunsetsystems.com>
//
// Windows compatibility mods 2009 Bill Cernansky [mi-squared.com]
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//
// Updated by Medical Information Integration, LLC to support download
//  and multi OS use - tony@mi-squared..com 12-2009

//////////////////////////////////////////////////////////////////////
// This is a template for printing patient statements and collection
// letters.  You must customize it to suit your practice.  If your
// needs are simple then you do not need programming experience to do
// this - just read the comments and make appropriate substitutions.
// All you really need to do is replace the [strings in brackets].
//////////////////////////////////////////////////////////////////////

// The location/name of a temporary file to hold printable statements.
//

$STMT_TEMP_FILE = $GLOBALS['temporary_files_dir'] . "/openemr_statements.txt";
$STMT_TEMP_FILE_PDF = $GLOBALS['temporary_files_dir'] . "/openemr_statements.pdf";

$STMT_PRINT_CMD = $GLOBALS['print_command']; 

// This function builds a printable statement or collection letter from
// an associative array having the following keys:
//
//  today   = statement date yyyy-mm-dd
//  pid     = patient ID
//  patient = patient name
//  amount  = total amount due
//  to      = array of addressee name/address lines
//  lines   = array of lines, each with the following keys:
//    dos     = date of service yyyy-mm-dd
//    desc    = description
//    amount  = charge less adjustments
//    paid    = amount paid
//    notice  = 1 for first notice, 2 for second, etc.
//    detail  = associative array of details
//
// Each detail array is keyed on a string beginning with a date in
// yyyy-mm-dd format, or blanks in the case of the original charge
// items.  Its values are associative arrays like this:
//
//  pmt - payment amount as a positive number, only for payments
//  src - check number or other source, only for payments
//  chg - invoice line item amount amount, only for charges or
//        adjustments (adjustments may be zero)
//  rsn - adjustment reason, only for adjustments
//
// The returned value is a string that can be sent to a printer.
// This example is plain text, but if you are a hotshot programmer
// then you could make a PDF or PostScript or whatever peels your
// banana.  These strings are sent in succession, so append a form
// feed if that is appropriate.
//

// A sample of the text based format follows:

//[Your Clinic Name]             Patient Name          2009-12-29
//[Your Clinic Address]          Chart Number: 1848
//[City, State Zip]              Insurance information on file
//
//
//ADDRESSEE                      REMIT TO
//Patient Name                     [Your Clinic Name]
//patient address                  [Your Clinic Address]
//city, state zipcode              [City, State Zip]
//                                 If paying by VISA/MC/AMEX/Dis
//
//Card_____________________  Exp______ Signature___________________
//                     Return above part with your payment
//-----------------------------------------------------------------
//
//_______________________ STATEMENT SUMMARY _______________________
//
//Visit Date  Description                                    Amount
//
//2009-08-20  Procedure 99345                                198.90
//            Paid 2009-12-15:                               -51.50
//... more details ...
//...
//...
// skipping blanks in example
//
//
//Name: Patient Name              Date: 2009-12-29     Due:   147.40
//_________________________________________________________________
//
//Please call if any of the above information is incorrect
//We appreciate prompt payment of balances due
//
//[Your billing contact name]
//  Billing Department
//  [Your billing dept phone]

function create_statement($stmt) {
 if (! $stmt['pid']) return ""; // get out if no data

 // These are your clinics return address, contact etc.  Edit them.
 // TBD: read this from the facility table
 
 // Facility (service location)
  $atres = sqlStatement("select f.name,f.street,f.city,f.state,f.postal_code from facility f " .
    " left join users u on f.id=u.facility_id " .
    " left join  billing b on b.provider_id=u.id and b.pid = '".$stmt['pid']."' " .
    " where  service_location=1");
  $row = sqlFetchArray($atres);
 
 // Facility (service location)
 
 $clinic_name = "{$row['name']}";
 $clinic_addr = "{$row['street']}";
 $clinic_csz = "{$row['city']}, {$row['state']}, {$row['postal_code']}";
 
 
 // Billing location
 $remit_name = $clinic_name;
 $remit_addr = $clinic_addr;
 $remit_csz = $clinic_csz;
 
 // Contacts
  $atres = sqlStatement("select f.attn,f.phone from facility f " .
    " left join users u on f.id=u.facility_id " .
    " left join  billing b on b.provider_id=u.id and b.pid = '".$stmt['pid']."'  " .
    " where billing_location=1");
  $row = sqlFetchArray($atres);
 $billing_contact = "{$row['attn']}";
 $billing_phone = "{$row['phone']}";
 $label_dept = xl('Billing Department');
 // Text only labels
 
 $label_addressee = xl('ADDRESSEE');
 $label_remitto = xl('REMIT TO');
 $label_chartnum = xl('Chart Number');
 $label_insinfo = xl('Insurance information on file');
 $label_totaldue = xl('Total amount due');
 $label_payby = xl('If paying by');
 $label_cards = xl('VISA/MC/AMEX/Dis');  
 $label_cardnum = xl('Card');
 $label_expiry = xl('Exp');
 $label_sign = xl('Signature');
 $label_retpay = xl('Return above part with your payment');
 $label_pgbrk = xl('STATEMENT SUMMARY');
 $label_visit = xl('Visit Date');
 $label_desc = xl('Description');
 $label_amt = xl('Amount');

 // This is the text for the top part of the page, up to but not
 // including the detail lines.  Some examples of variable fields are:
 //  %s    = string with no minimum width
 //  %9s   = right-justified string of 9 characters padded with spaces
 //  %-25s = left-justified string of 25 characters padded with spaces
 // Note that "\n" is a line feed (new line) character.
 // reformatted to handle i8n by tony
 
$out  = sprintf("%-30s %-23s %-s\n",$clinic_name,$stmt['patient'],$stmt['today']);
$out .= sprintf("%-30s %s: %-s\n",$clinic_addr,$label_chartnum,$stmt['pid']);
$out .= sprintf("%-30s %-s\n",$clinic_csz,$label_insinfo);
$out .= sprintf("%-30s %s: %-s\n",null,$label_totaldue,null);
$out .= "\n\n";
$out .= sprintf("%-30s %-s\n",$label_addressee,$label_remitto);
$out .= sprintf("%-32s %s\n",$stmt['to'][0],$remit_name);
$out .= sprintf("%-32s %s\n",$stmt['to'][1],$remit_addr);
$out .= sprintf("%-32s %s\n",$stmt['to'][2],$remit_csz);

if($stmt['to'][3]!='')//to avoid double blank lines the if condition is put.
 	$out .= sprintf("   %-32s\n",$stmt['to'][3]);
$out .= sprintf("_________________________________________________________________\n");
$out .= "\n";
$out .= sprintf("%-32s\n",$label_payby.' '.$label_cards);
$out .= "\n";
$out .= sprintf("%s_____________________  %s______ %s___________________\n",
                $label_cardnum,$label_expiry,$label_sign);
$out .= sprintf("%-20s %s\n",null,$label_retpay);
$out .= sprintf("-----------------------------------------------------------------\n");
$out .= "\n";
$out .= sprintf("_______________________ %s _______________________\n",$label_pgbrk);
$out .= "\n";
$out .= sprintf("%-11s %-46s %s\n",$label_visit,$label_desc,$label_amt);
$out .= "\n";
 
 // This must be set to the number of lines generated above.
 //
 $count = 21;

 // This generates the detail lines.  Again, note that the values must
 // be specified in the order used.
 //
 foreach ($stmt['lines'] as $line) {
  $description = $line['desc'];
  $tmp = substr($description, 0, 14);
  if ($tmp == 'Procedure 9920' || $tmp == 'Procedure 9921')
   $description = xl('Office Visit');

  $dos = $line['dos'];
  ksort($line['detail']);

  foreach ($line['detail'] as $dkey => $ddata) {
   $ddate = substr($dkey, 0, 10);
   if (preg_match('/^(\d\d\d\d)(\d\d)(\d\d)\s*$/', $ddate, $matches)) {
    $ddate = $matches[1] . '-' . $matches[2] . '-' . $matches[3];
   }
   $amount = '';

   if ($ddata['pmt']) {
    $amount = sprintf("%.2f", 0 - $ddata['pmt']);
    $desc = xl('Paid') .' '. $ddate .': '. $ddata['src'].' '. $ddata['insurance_company'];
   } else if ($ddata['rsn']) {
    if ($ddata['chg']) {
     $amount = sprintf("%.2f", $ddata['chg']);
     $desc = xl('Adj') .' '.  $ddate .': ' . $ddata['rsn'].' '. $ddata['insurance_company'];
    } else {
     $desc = xl('Note') .' '. $ddate .': '. $ddata['rsn'].' '. $ddata['insurance_company'];
    }
   } else if ($ddata['chg'] < 0) {
    $amount = sprintf("%.2f", $ddata['chg']);
    $desc = xl('Patient Payment');
   } else {
    $amount = sprintf("%.2f", $ddata['chg']);
    $desc = $description;
   }

   $out .= sprintf("%-10s  %-45s%8s\n", $dos, $desc, $amount);
   $dos = '';
   ++$count;
  }
 }

 // This generates blank lines until we are at line 42.
 //
 while ($count++ < 42) $out .= "\n";

 // Fixed text labels
 $label_ptname = xl('Name');
 $label_today = xl('Date');
 $label_due = xl('Due');
 $label_thanks = xl('Thank you for choosing');
 $label_call = xl('Please call if any of the above information is incorrect');
 $label_prompt = xl('We appreciate prompt payment of balances due');
 $label_dept = xl('Billing Department');
 
 // This is the bottom portion of the page.
 
 $out .= sprintf("%-s: %-25s %-s: %-14s %-s: %8s\n",$label_ptname,$stmt['patient'],
                 $label_today,$stmt['today'],$label_due,$stmt['amount']);
 $out .= sprintf("__________________________________________________________________\n");
 $out .= "\n";
 $out .= sprintf("%-s\n",$label_call);
 $out .= sprintf("%-s\n",$label_prompt);
 $out .= "\n";
 $out .= sprintf("%-s\n",$billing_contact);
 $out .= sprintf("  %-s\n",$label_dept);
 $out .= sprintf("  %-s\n",$billing_phone);
 $out .= "\014"; // this is a form feed

 
 return $out;
}
function create_statementpdf($stmt) {
	global $pdf;
 if (! $stmt['pid']) return ""; // get out if no data

 // These are your clinics return address, contact etc.  Edit them.
 // TBD: read this from the facility table
 
 // Facility (service location)
  $atres = sqlStatement("select f.name,f.street,f.city,f.state,f.postal_code from facility f " .
    " left join users u on f.id=u.facility_id " .
    " left join  billing b on b.provider_id=u.id and b.pid = '".$stmt['pid']."' " .
    " where  service_location=1");
  $row = sqlFetchArray($atres);
 
 // Facility (service location)
 
 $clinic_name = "{$row['name']}";
 $clinic_addr = "{$row['street']}";
 $clinic_csz = "{$row['city']}, {$row['state']}, {$row['postal_code']}";
 
 
 // Billing location
 $remit_name = $clinic_name;
 $remit_addr = $clinic_addr;
 $remit_csz = $clinic_csz;
 
 // Contacts
  $atres = sqlStatement("select f.attn,f.phone from facility f " .
    " left join users u on f.id=u.facility_id " .
    " left join  billing b on b.provider_id=u.id and b.pid = '".$stmt['pid']."'  " .
    " where billing_location=1");
  $row = sqlFetchArray($atres);
 $billing_contact = "{$row['attn']}";
 $billing_phone = "{$row['phone']}";
 $label_dept = xl('Billing Department');
 // Text only labels
 
 $label_addressee = xl('ADDRESSEE');
 $label_remitto = xl('REMIT TO');
 $label_chartnum = xl('Chart Number');
 $label_insinfo = xl('Insurance information on file');
 $label_totaldue = xl('Total amount due');
 $label_payby = xl('If paying by');
 $label_cards = xl('VISA/MC/AMEX/Dis');  
 $label_cardnum = xl('Card');
 $label_expiry = xl('Exp');
 $label_sign = xl('Signature');
 $label_retpay = xl('Return above part with your payment');
 $label_pgbrk = xl('STATEMENT SUMMARY');
 $label_visit = xl('Visit Date');
 $label_desc = xl('Description');
 $label_amt = xl('Amount');

 // This is the text for the top part of the page, up to but not
 // including the detail lines.  Some examples of variable fields are:
 //  %s    = string with no minimum width
 //  %9s   = right-justified string of 9 characters padded with spaces
 //  %-25s = left-justified string of 25 characters padded with spaces
 // Note that "\n" is a line feed (new line) character.
 // reformatted to handle i8n by tony
 
 

$out  = sprintf("%-30s %-23s %-s\n",$clinic_name,$stmt['patient'],$stmt['today']);
$out .= sprintf("%-30s %s: %-s\n",$clinic_addr,$label_chartnum,$stmt['pid']);
$out .= sprintf("%-30s %-s\n",$clinic_csz,$label_insinfo);
$out .= sprintf("%-30s %s: %-s\n",null,$label_totaldue,null);
$out .= "\n\n";
$out .= sprintf("%-30s %-s\n",$label_addressee,$label_remitto);
$out .= sprintf("%-32s %s\n",$stmt['to'][0],$remit_name);
$out .= sprintf("%-32s %s\n",$stmt['to'][1],$remit_addr);
$out .= sprintf("%-32s %s\n",$stmt['to'][2],$remit_csz);

if($stmt['to'][3]!='')//to avoid double blank lines the if condition is put.
 	$out .= sprintf("   %-32s\n",$stmt['to'][3]);
$out .= sprintf("_________________________________________________________________\n");
$out .= "\n";
$out .= sprintf("%-32s\n",$label_payby.' '.$label_cards);
$out .= "\n";
$out .= sprintf("%s_____________________  %s______ %s___________________\n",
                $label_cardnum,$label_expiry,$label_sign);
$out .= sprintf("%-20s %s\n",null,$label_retpay);
$out .= sprintf("-----------------------------------------------------------------\n");
$out .= "\n";
$out .= sprintf("_______________________ %s _______________________\n",$label_pgbrk);
$out .= "\n";
$out .= sprintf("%-11s %-46s %s\n",$label_visit,$label_desc,$label_amt);
$out .= "\n";
 
 // This must be set to the number of lines generated above.
 //
 $count = 21;
 $statements= "";
 // This generates the detail lines.  Again, note that the values must
 // be specified in the order used.
 //
 foreach ($stmt['lines'] as $line) {
  $description = $line['desc'];
  $tmp = substr($description, 0, 14);
  if ($tmp == 'Procedure 9920' || $tmp == 'Procedure 9921')
   $description = xl('Office Visit');

  $dos = $line['dos'];
  ksort($line['detail']);

  foreach ($line['detail'] as $dkey => $ddata) {
   $ddate = substr($dkey, 0, 10);
   if (preg_match('/^(\d\d\d\d)(\d\d)(\d\d)\s*$/', $ddate, $matches)) {
    $ddate = $matches[1] . '-' . $matches[2] . '-' . $matches[3];
   }
   $amount = '';

   if ($ddata['pmt']) {
    $amount = sprintf("%.2f", 0 - $ddata['pmt']);
    $desc = xl('Paid') .' '. $ddate .': '. $ddata['src'].' '. $ddata['insurance_company'];
   } else if ($ddata['rsn']) {
    if ($ddata['chg']) {
     $amount = sprintf("%.2f", $ddata['chg']);
     $desc = xl('Adj') .' '.  $ddate .': ' . $ddata['rsn'].' '. $ddata['insurance_company'];
    } else {
     $desc = xl('Note') .' '. $ddate .': '. $ddata['rsn'].' '. $ddata['insurance_company'];
    }
   } else if ($ddata['chg'] < 0) {
    $amount = sprintf("%.2f", $ddata['chg']);
    $desc = xl('Patient Payment');
   } else {
    $amount = sprintf("%.2f", $ddata['chg']);
    $desc = $description;
   }
   $statements .="<tr><td>$dos</td><td>$desc</td><td align=\"right\">$amount</td></tr>";
   $out .= sprintf("%-10s  %-45s%8s\n", $dos, $desc, $amount);
   $dos = '';
   ++$count;
  }
 }

 // This generates blank lines until we are at line 42.
 //
 while ($count++ < 42) $out .= "\n";

 // Fixed text labels
 $label_ptname = xl('Name');
 $label_today = xl('Date');
 $label_due = xl('Due');
 $label_thanks = xl('Thank you for choosing');
 $label_call = xl('Please call if any of the above information is incorrect');
 $label_prompt = xl('We appreciate prompt payment of balances due');
 $label_dept = xl('Billing Department');
 
 // This is the bottom portion of the page.
 
 $out .= sprintf("%-s: %-25s %-s: %-14s %-s: %8s\n",$label_ptname,$stmt['patient'],
                 $label_today,$stmt['today'],$label_due,$stmt['amount']);
 $out .= sprintf("__________________________________________________________________\n");
 $out .= "\n";
 $out .= sprintf("%-s\n",$label_call);
 $out .= sprintf("%-s\n",$label_prompt);
 $out .= "\n";
 $out .= sprintf("%-s\n",$billing_contact);
 $out .= sprintf("  %-s\n",$label_dept);
 $out .= sprintf("  %-s\n",$billing_phone);
 $out .= "\014"; // this is a form feed
 $statementdate = $stmt['today'];
 $dueamout = $stmt['amount'];
 $patientid = $stmt['pid'];
 $address1 = $stmt['to'][0];
 $address2 = $stmt['to'][1];
 $address3 = $stmt['to'][2];

 // set font
$pdf->SetFont('times', '', 8);
// add a page
$pdf->AddPage();
// add a page

$getheight = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set margins
$getheight->SetMargins(8, 0, 16);
//$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$getheight->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$getheight->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
// set image scale factor
$getheight->setImageScale(PDF_IMAGE_SCALE_RATIO);
$getheight->SetFont('times', '', 16);
$getheight->AddPage();

$dublicate = <<<EOF
	<style>
		.checklabel{
			font-size:8px;
			font-weight:normal;
			color:#3f56b2;
			font-family:"times";
		}
		.retinfo{
			font-size:8px;
			font-weight:normal;
			color:#3f56b2;
			font-family:"times";
			text-align:center;
		}
		.symbles{
			font-size:15px;
			font-family:"zapfdingbats";
			color:#3f56b2;
		}
		.statement{
			background-color:#334d7f;
			text-align:center;
			font-weight:900;
			font-size:15px;
			color:#fff;
			letter-spacing: 3px;
		}
		.setbg{
			background-color:#e4e4ed;
		}
		.setheight{
			height:100%;
		}
		.statmenttables th{
			border: 1px solid black;
			text-align:center;
			font-size:14px;
			font-family: "times";
		}
		.statmenttables td{
			border-left: 1px solid black;
			border-right: 1px solid black;
			font-family: "times";
			font-size:14px;
			font-weight:900;
		}
		.borderbot td{
			border-bottom:1px solid black;
		}
		.setfont{
			font-size:14px;
			font-family: "times";
		}
	</style>
	<table border="0" cellpadding="0">
		<tr>
			<td width="2%"><span class="symbles">r</span></td>
			<td width="32%" class="checklabel">Please check box if above address is incorrect or insurance
information has changed, and indicate change(s) on reverse side.</td>
			<td width="7%"></td>
			<td width="22%" class="statement">STATEMENT</td>
			<td width="7%"></td>
			<td width="30%" class="retinfo">PLEASE DETACH AND RETURN TOP PORTION WITH
YOUR PAYMENT IN ENCLOSED ENVELOPE</td>
		</tr>
		<tr><td colspan="6" height="5"></td></tr>
		<tr class="setbg">
			<td width="4%"></td><td colspan="4" width="92%">
				<table border="0" cellpadding="0">
					<tr>
						<td colspan="2" height="30"></td>
					</tr>
					<tr>
						<td class="setfont">&nbsp;&nbsp;<b>Patient Name:</b> $address1 </td>
						<td class="setfont"><b>Account Number:</b> $patientid </td>
					</tr>
					<tr>
						<td colspan="2">
							<table border="0" cellpadding="3" class="statmenttables">
							<thead>
								<tr>
									<th width="20%"><b>Visit Date</b></th>
									<th width="60%"><b>Description</b></th>
									<th width="20%"><b>Amount</b></th>
								</tr>
							</thead>
							$statements
							</table>
						</td>						
					</tr>
				</table>
			</td><td width="4%"></td>
		</tr>
		
	</table>
EOF;

$getheight->writeHTML($dublicate, true, false, true, false, '');
$tableheight = $getheight->GetY();

$html = <<<EOF
<style>
.stinfo {
	text-align:center;
	color:red;
	font-size:7px;
	line-height:10px;
}
.stheader th{
	background-color:#c9cadb;
	color:#333c7f;
	text-align:center;
	font-weight:600;
	font-size:9px;
	border:1px solid blank;
}
.stdatiles td{
	text-align:center;
	border:1px solid blank;
	line-height:25px;
}
.addborder{
	border:1px solid blank;
}
.setfont{
	font-size:13px;
	font-family: "times";
}
.amoutinfo{
	color:#333c7f;
	font-size:9px;
	font-weight:600;
}
.dollar{
	color:#333c7f;
	font-size:20px;
	font-weight:bold;
}
</style>
<body>
<table  border="0" cellpadding="2">
<tr>
<td class="setfont">$clinic_name<br/>$clinic_addr<br/>$clinic_csz<br/><br/>RETURN SERVICE REQUESTED</td>
<td><img src="../images/pay.jpg" height="103" width="450" /></td>
</tr>
<tr>
	<td class="setfont" >$billing_contact<br/>$label_dept<br/>$billing_phone</td>
	<td>
		<table border="0" cellpadding="2">
			<tr class="stheader">
				<th>STATEMENT DATE</th>
				<th>PAY THIS AMOUNT</th>
				<th>ACCOUNT NO.</th>
			</tr>
			<tr class="stdatiles">
				<td>$statementdate</td>
				<td>$dueamout</td>
				<td>$patientid</td>
			</tr>
			<tr>
				<td width="54%"><div class="stinfo">CHARGES AND CREDITS MADE AFTER STATEMENT DATE WILL APPEAR ON NEXT STATEMENT.</div></td>
				<td width="46%" class="addborder">
					<table border="0">
						<tr>
							<td width="54%"><div class="amoutinfo">SHOW AMOUNT PAID HERE</div></td>
							<td><div class="dollar">$</div></td>
							<td></td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</td>
</tr>
</table>
</body>
EOF;
$pdf->writeHTML($html, true, false, true, false, '');

$html1 = <<<EOF
	<style>
		.heading{
			background-color: #d1d1d4;
			font-size:13px;
			font-weight:bold;
			font-family:"courier";
		}
		.contenta{
			font-size:13px;
			font-family:"courier";
		}
	</style>
	<table border="0" cellpadding="2">
		<tr>
			<td class="heading" width="40%">&nbsp;&nbsp;$label_addressee</td>
			<td width="10%" ></td>
			<td class="heading" width="50%">&nbsp;&nbsp;MAKE CHECKS PAYABLE / REMIT TO</td>
		</tr>
		<tr>
			<td class="contenta" width="40%">&nbsp;&nbsp;&nbsp;&nbsp;$address1<br/>&nbsp;&nbsp;&nbsp;&nbsp;$address2<br/>&nbsp;&nbsp;&nbsp;&nbsp;$address3</td>
			<td width="10%"></td>
			<td class="contenta" width="50%">&nbsp;&nbsp;&nbsp;&nbsp;$remit_name<br/>&nbsp;&nbsp;&nbsp;&nbsp;$remit_addr<br/>&nbsp;&nbsp;&nbsp;&nbsp;$remit_csz</td>
		</tr>
		<tr>
			<td colspan="3" class="contenta">---------------------------------------------------------------------------------------</td>
		</tr>
	</table>
EOF;

$pdf->writeHTML($html1, true, false, true, false, '');
$tablestartpoint = $pdf->GetY();
$setheight = (268 - ($tablestartpoint + $tableheight + 45))*3.5;
$html2 = <<<EOF
	<style>
		.checklabel{
			font-size:8px;
			font-weight:normal;
			color:#3f56b2;
			font-family:"times";
		}
		.retinfo{
			font-size:8px;
			font-weight:normal;
			color:#3f56b2;
			font-family:"times";
			text-align:center;
		}
		.symbles{
			font-size:15px;
			font-family:"zapfdingbats";
			color:#3f56b2;
		}
		.statement{
			background-color:#334d7f;
			text-align:center;
			font-weight:900;
			font-size:15px;
			color:#fff;
			letter-spacing: 3px;
		}
		.setbg{
			background-color:#e4e4ed;
		}
		.setheight{
			height:100%;
		}
		.statmenttables th{
			border: 1px solid black;
			text-align:center;
			font-size:12px;
			font-family: "times";
		}
		.statmenttables td{
			border-left: 1px solid black;
			border-right: 1px solid black;
			font-family: "times";
			font-size:13px;
			font-weight:900;
		}
		.borderbot td{
			border-bottom:1px solid black;
		}
		.setfont{
			font-size:13px;
			font-family: "times";
		}
		.daystable{
			font-family: "times";
			font-size:11px;
			text-align:center;
		}
		.brownbg{
			background-color:#fcd6c9;
			font-size:9px;
			text-align:left;
		}
		.footer1{
			font-weight:900;
			font-size:15px;
			color:#334d7f;
		}
		.footer2{
			font-weight:600;
			font-size:14px;
			color:#334d7f;
		}
	</style>
	<table border="0" cellpadding="0">
		<tr>
			<td width="2%"><span class="symbles">r</span></td>
			<td width="32%" class="checklabel">Please check box if above address is incorrect or insurance
information has changed, and indicate change(s) on reverse side.</td>
			<td width="7%"></td>
			<td width="22%" class="statement">STATEMENT</td>
			<td width="7%"></td>
			<td width="30%" class="retinfo">PLEASE DETACH AND RETURN TOP PORTION WITH
YOUR PAYMENT IN ENCLOSED ENVELOPE</td>
		</tr>
		<tr><td colspan="6" height="5"></td></tr>
		<tr class="setbg">
			<td width="4%"></td><td colspan="4" width="92%">
				<table border="0" cellpadding="0">
					<tr>
						<td colspan="2" height="30"></td>
					</tr>
					<tr>
						<td class="setfont">&nbsp;&nbsp;<b>Patient Name:</b> $address1 </td>
						<td class="setfont"><b>Account Number:</b> $patientid </td>
					</tr>
					<tr>
						<td colspan="2">
							<table border="0" cellpadding="3" class="statmenttables">
							<tr>
								<th width="20%"><b>VISIT DATE</b></th>
								<th width="60%"><b>DESCRIPTION</b></th>
								<th width="20%"><b>AMOUNT</b></th>
							</tr>
							$statements
							<tr class="borderbot"><td height="$setheight"></td><td></td><td></td></tr>
							</table>
						</td>						
					</tr>
					<tr>
						<td colspan="2" height="30"></td>
					</tr>
					<tr>
						<td width="75%">
							<table border="1" cellpadding="3">
							<tr>
								<td class="daystable"><b>0-29 DAYS</b></td>
								<td class="daystable"><b>30-59 DAYS</b></td>
								<td class="daystable"><b>60-89 DAYS</b></td>
								<td class="daystable"><b>90-119 DAYS</b></td>
								<td class="daystable"><b>OVER 120 DAYS</b></td>
							</tr>
							<tr>
								<td class="daystable"><b>$0.00</b></td>
								<td class="daystable"><b>$0.00</b></td>
								<td class="daystable"><b>$0.00</b></td>
								<td class="daystable"><b>$0.00</b></td>
								<td class="daystable"><b>$dueamout</b></td>
							</tr>
							</table>
						</td>
						<td width="7%"></td>
						<td width="18%">
							<table border="1" cellpadding="3">
								<tr>
									<td class="daystable"><b>AMOUNT DUE</b></td>
								</tr>
								<tr>
									<td class="daystable"><b>$dueamout</b></td>
								</tr>
							</table>
						</td>
					</tr>
					<tr><td colspan="6"></td></tr>
					<tr>
						<td colspan="3">
							<table border="0" cellpadding="7">	
								<tr>
									<td class="brownbg" width="55%"><b>Please process your payment securely at www.dfwprimary.com and select patient > Payment menu. The balance is due after your insurance has processed its portion of services. If you choose to receive email statements, please setup your online login and make necessary selection to stop print statements.</b></td>
									<td style="font-size:12px;">$remit_name<br/>$remit_addr<br/>$remit_csz</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</td><td width="4%"></td>
		</tr>
		<tr><td colspan="6" align="center" class="footer1"><b>STATEMENT</b></td></tr>
		<tr><td colspan="6" align="center" class="footer2">SEE REVERSE SIDE FOR IMPORTANT BILLING INFORMATION</td></tr>
	</table>
EOF;

$pdf->writeHTML($html2, true, false, true, false, '');

 return $out;
}
?>
