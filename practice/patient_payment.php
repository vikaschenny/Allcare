<?php
// Copyright (C) 2006-2010 Rod Roark <rod@sunsetsystems.com>
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.

$fake_register_globals=false;
$sanitize_all_escapes=true;

//require_once("../globals.php");
require_once("verify_session.php");
require_once("$srcdir/acl.inc");
require_once("$srcdir/patient.inc");
require_once("$srcdir/billing.inc");
require_once("$srcdir/payment.inc.php");
require_once("$srcdir/forms.inc");
require_once("$srcdir/sl_eob.inc.php");
require_once("$srcdir/invoice_summary.inc.php");
require_once("../custom/code_types.inc.php");
require_once("$srcdir/formatting.inc.php");
require_once("$srcdir/options.inc.php");
require_once("$srcdir/encounter_events.inc.php");
$pid = $_REQUEST['hidden_patient_code'] > 0 ? $_REQUEST['hidden_patient_code'] : $pid;

$INTEGRATED_AR = $GLOBALS['oer_config']['ws_accounting']['enabled'] === 2;

$pid           = $_REQUEST['pid'];
$encId         = $_REQUEST['encId'];

?>
<html>
<head>
<?php html_header_show();?>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"/>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
  <style>
      #loader1,#loader2,#loader3,#loader4{
            background: rgba(0,0,0,0.56);
            border-radius: 4px;
            display:table;
            height: 48px;
            width: 260px;
            color: #fff;
            position: absolute;
            left: 0px;
            top:0px;
            bottom: 0px;
            right: 0px;
            margin: auto;
            display: block;
        }
        .ajax-spinner-bars {
            height: 48px;
            left: 23px;
            position: relative;
            top: 20px;
            width: 35px;
            display: table-cell;
         }
         #loadertitle1,#loadertitle2,#loadertitle3,#loadertitle4 {
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
        .editapp,.eligibility,.demographics{
            cursor: pointer;
            position: relative;
        }

        .createapp,.editins,.Benefits,.copay,.blance{
            cursor: pointer;
        }
        .copay,.blance,.address{
            position: absolute;
            right: 30px;
            text-align: right;
        }

        @keyframes fadeit{
              0%{ opacity:1; }
              100%{ opacity:0;}
        }
  </style>
  <script>
      var object = parent.document.getElementById("loader");
      $(object).hide();
  </script>
</head>
<body>
<div id="info">
    <fieldset style="margin-left: 20px;">
    <legend>Billing</legend>
    <?php
    $patientbalance = get_patient_balance($pid, false);
    //Debit the patient balance from insurance balance
    $insurancebalance = get_patient_balance($pid, true) - $patientbalance;
    $totalbalance=$patientbalance + $insurancebalance;
    // Show current balance and billing note, if any.
    echo "<table border='0'><tr><td>" .
    "<table ><tr><td><span class='bold'><font color='red'>" .
     xlt('Patient Balance Due') .
     " : " . text(oeFormatMoney($patientbalance)) .
     "</font></span></td></tr>".
       "<tr><td><span class='bold'><font color='red'>" .
     xlt('Insurance Balance Due') .
     " : " . text(oeFormatMoney($insurancebalance)) .
     "</font></span></td></tr>".
     "<tr><td><span class='bold'><font color='red'>" .
     xlt('Total Balance Due').
     " : " . text(oeFormatMoney($totalbalance)) .
        "</font></span></td></td></tr>";
      if ($result['genericname2'] == 'Billing') {
           echo "<tr><td><span class='bold'><font color='red'>" .
            xlt('Billing Note') . ":" .
            text($result['genericval2']) .
            "</font></span></td></tr>";
      } 
    echo "</table></td></tr></td></tr></table>";
    ?>
    <input type="hidden" name="patient_bal" id="patient_bal" value="<?php echo $patientbalance?>">
    <input type="hidden" name="insurance_bal" id="insurance_bal" value="<?php echo $insurancebalance?>">
    <input type="hidden" name="total_bal" id="total_bal" value="<?php echo $totalbalance?>">
</fieldset>
    <div style="margin: 10px 0 0px 20px;">
        <ul class="nav nav-tabs">
            <li class="active"><a data-toggle="tab" href="#home">Patient Statement</a></li>
            <li><a data-toggle="tab" href="#menu1">Patient Payment</a></li>
            <li><a data-toggle="tab" href="#menu2">InstaMed</a></li>
            <li><a data-toggle="tab" href="#menu3">Visit Note</a></li>
        </ul>

        <div class="tab-content">
            <div id="home" class="tab-pane fade in active">
              <div id="loader1">
                    <div class="ajax-spinner-bars">
                        <div class="bar-1"></div>
                        <div class="bar-2"></div>
                        <div class="bar-3"></div>
                        <div class="bar-4"></div>
                        <div class="bar-5"></div>
                        <div class="bar-6"></div>
                        <div class="bar-7"></div>
                        <div class="bar-8"></div>
                        <div class="bar-9"></div>
                        <div class="bar-10"></div>
                        <div class="bar-11"></div>
                        <div class="bar-12"></div>
                        <div class="bar-13"></div>
                        <div class="bar-14"></div>
                        <div class="bar-15"></div>
                        <div class="bar-16"></div>
                    </div>
                    <div id="loadertitle1">Patient Statement Loading...</div>
                </div>
                <iframe id="iframecontenar" style="display:none; border:0px" class="help-contentFrame" src="patient-enc-statement.php?pid=<?php echo $pid;?>&encId=<?php echo $encId; ?>" onload="showloader(this,$('#loader1'))" height="100%" width="100%"></iframe>
            </div>
            
            <div id="menu1" class="tab-pane fade">
              <div id="loader2">
                    <div class="ajax-spinner-bars">
                        <div class="bar-1"></div>
                        <div class="bar-2"></div>
                        <div class="bar-3"></div>
                        <div class="bar-4"></div>
                        <div class="bar-5"></div>
                        <div class="bar-6"></div>
                        <div class="bar-7"></div>
                        <div class="bar-8"></div>
                        <div class="bar-9"></div>
                        <div class="bar-10"></div>
                        <div class="bar-11"></div>
                        <div class="bar-12"></div>
                        <div class="bar-13"></div>
                        <div class="bar-14"></div>
                        <div class="bar-15"></div>
                        <div class="bar-16"></div>
                    </div>
                    <div id="loadertitle2">Patient Payment Loading...</div>
                </div>
                <iframe id="iframecontenar2" style="display:none; border:0px" class="help-contentFrame" src="patient_file/patient_front_payment.php?pid=<?php echo $pid;?>&encId=<?php echo $encId; ?>" onload="showloader(this,$('#loader2'))" height="100%" width="100%"></iframe>
            </div>
            
            <div id="menu2" class="tab-pane fade">
              <div id="loader3">
                    <div class="ajax-spinner-bars">
                        <div class="bar-1"></div>
                        <div class="bar-2"></div>
                        <div class="bar-3"></div>
                        <div class="bar-4"></div>
                        <div class="bar-5"></div>
                        <div class="bar-6"></div>
                        <div class="bar-7"></div>
                        <div class="bar-8"></div>
                        <div class="bar-9"></div>
                        <div class="bar-10"></div>
                        <div class="bar-11"></div>
                        <div class="bar-12"></div>
                        <div class="bar-13"></div>
                        <div class="bar-14"></div>
                        <div class="bar-15"></div>
                        <div class="bar-16"></div>
                    </div>
                    <div id="loadertitle3">InstaMed Loading...</div>
                </div>
                <?php
                $practiceId = '';
                $query = sqlStatement("SELECT title FROM list_options WHERE list_id= 'allcareConfig' AND option_id='instaMedPayment'");
                while($row = sqlFetchArray($query)){
                    $instaMedUrl = $row['title'];
                }
                
                ?>
                <iframe id="iframecontenar3" style="display:none; border:0px" class="help-contentFrame" src="<?php echo $instaMedUrl; ?>" onload="showloader(this,$('#loader3'))" height="100%" width="100%"></iframe>
            </div>
            <div id="menu3" class="tab-pane fade">
              <div id="loader4">
                    <div class="ajax-spinner-bars">
                        <div class="bar-1"></div>
                        <div class="bar-2"></div>
                        <div class="bar-3"></div>
                        <div class="bar-4"></div>
                        <div class="bar-5"></div>
                        <div class="bar-6"></div>
                        <div class="bar-7"></div>
                        <div class="bar-8"></div>
                        <div class="bar-9"></div>
                        <div class="bar-10"></div>
                        <div class="bar-11"></div>
                        <div class="bar-12"></div>
                        <div class="bar-13"></div>
                        <div class="bar-14"></div>
                        <div class="bar-15"></div>
                        <div class="bar-16"></div>
                    </div>
                    <div id="loadertitle4">Visit Note Loading...</div>
                </div>
                <iframe id="iframecontenar4" style="display:none; border:0px" class="help-contentFrame" src="visit_note.php?pid=<?php echo $pid;?>&encId=<?php echo $encId; ?>" onload="showloader(this,$('#loader4'))" height="100%" width="100%"></iframe>
            </div>
        </div>
    </div>
</div>
    <script>
        function showloader(element,loader){
            $(element).show();
            loader.hide();
        }
    </script>
</body>
</html>