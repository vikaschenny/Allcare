<?php
/**
 * Copyright (C) 2010 OpenEMR Support LLC
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * 2013/02/08 Minor tweaks by EMR Direct to allow integration with Direct messaging
 * 2013-03-27 by sunsetsystems: Fixed some weirdness with assigning a message recipient,
 *   and allowing a message to be closed with a new note appended and no recipient.
 */
require_once("verify_session.php");

$pagename = "Estimation"; 

require_once("$srcdir/api.inc");
require_once("$srcdir/forms.inc");
require_once("$srcdir/options.inc.php");
require_once("$srcdir/patient.inc");
require_once("$srcdir/formatting.inc.php");
require_once("$srcdir/billing.inc");
include_once("$srcdir/calendar.inc");
include_once("$srcdir/edi.inc");

?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>HealthCare</title>
<link href='http://fonts.googleapis.com/css?family=Abel' rel='stylesheet' type='text/css'>
<link href='http://fonts.googleapis.com/css?family=Roboto:400,300,500' rel='stylesheet' type='text/css'>
<link href='http://fonts.googleapis.com/css?family=Dosis:300,400,500,600' rel='stylesheet' type='text/css'>
<link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="assets/css/font-awesome.min.css">
<link href='http://fonts.googleapis.com/css?family=Roboto+Condensed:400,300' rel='stylesheet' type='text/css'>
<script type="text/javascript" src="assets/js/jquery.min.js"></script>
<title><?php echo htmlspecialchars( xl('Eligibility 270 Inquiry Batch'), ENT_NOQUOTES); ?></title>
<style>
    .contenar{
        width: 100%;
        height: 100%;
        position: relative;
        
    }
    .contenar-piechart{
        position: relative;
        left: 0;
        top:0;
    }
    .piechart{
        position: relative;
        width: 880px;
        height: 362px;
        overflow: hidden;
        left: 0;
        right: 0;
        margin: auto;
        margin-bottom: 50px;
    }
    .currency{
        text-align: right;
        position: absolute;
        right: 0;
        top: 0;
    }
    .large{
        font-weight: bold;
        font-size: 19px;
    }
    #t_responsibility{
        right: 715px;
        top: 121px;
    }
    #ins_responsibility{
        right: 715px;
        top: 280px;
    }
    #estimate{
        right: 420px;
        top: 206px;
        text-align: center;
        font-weight: bold;
        font-size: 23px;
        color: #1ead1e !important;
    }
    .small{
        font-weight: bold;
        font-size: 13px;
    }
    #copay{
        right: 8px;
        top: 194px;
    }
    #deductible{
        right: 8px;
        top: 214px;
    }
    #coinsturance{
        right: 8px;
        top: 233px;
    }
    #etitle{
        text-align: right;
        margin: 6px 30px 25px auto;
        font-size: 18px;
    }
    #header{
        display: table;
        width: 95%;
        margin: auto;
        margin-bottom: 30px;
    }
    #header > div{
        display: table-cell;
        height: 40px;
        background: #e6e6e6 !important;
        margin: 10px;
        border: 1px double #fff;
        padding-left: 15px;
        vertical-align: middle;        
    }
    #header > div:nth-child(1){
        width: 30%;
    }
    #header > div:nth-child(2){
        width: 14%;
    }
    #header > div:nth-child(3){
        width: 22%;
    }
    #header > div:nth-child(4){
        width: 28%;
    }
    .tcard{
        display: table;
        width: 95%;
        margin: auto;
        margin-bottom: 25px;
    }
    .trowcard{
        display: table-row;
    }
    .trowcard > div{
        display: table-cell;
    }
    #footer{
        border-top: 1px solid #ccc;
    }
    @media print{
        body {-webkit-print-color-adjust: exact;color-adjust: exact;}
        #print_sepr{display: none;}
        #footer{
            position:fixed;
            bottom: 0px;
            color:#ccc !important;
        }
        .tcard{
            width: 100%;
        }
    }
</style>
</head>
<body>
<?php
    // Get copay, coinsurance and remaining Deductible of a patient
    $query = sqlStatement("SELECT * FROM patient_data WHERE pid=".$pid);
    $row = sqlFetchArray($query);
    $copay = $coinsurance = $rmgdec = 0;
    $copay = $row['Copay'];
    $coinsurance = $row['CoInsurance'];
    $remainingDeductible = $row['remainingDeductible'];
    
    $pid    = $_REQUEST['pid'];
    $catid  = $_REQUEST['catid'];
    $cpt    = $_REQUEST['cpt'];    
    $insname= $_REQUEST['insname'];
    $plname = $_REQUEST['plname'];
    $aid    = $_REQUEST['aid'];
    $audType= $_REQUEST['audType'];
    $pname  = $_REQUEST['pname'];
    $dos    = $_REQUEST['dos'];
    
    $dosFormat = date_create($dos);
    $moddos    = date_format($dosFormat,'m/d/Y');
    
    $currentdate = date('m/d/Y h:i A', time());
    
    $query = sqlStatement("SELECT * FROM tbl_allcare_contractrates 
                           WHERE Insurance = '".$insname."' AND Insurance_Plan='".$plname."' AND CPT_Code='".$cpt."'");
    $row = sqlFetchArray($query);
    
    $allowedrate = $row['Allowed_Rate'];
    $chargedAmount = $row['Standard_Rate'];
    
    // insurance id
    $query = sqlStatement("SELECT id,freeb_type FROM insurance_companies WHERE name = '".$insname."'");
    $row2  = sqlFetchArray($query);
    $insId = $row2['id'];
    $insType = $row2['freeb_type'];
    
    // If allowedrate is empty then it means data related to this is not uploaded through contract sheet
    // So, lets check in plan attributes for contract rates
    if(empty($allowedrate)):
        $query = sqlStatement("SELECT contractRates FROM tbl_patientinsurancecompany 
                               WHERE insuranceid = '".$insId."' AND planname = '".$plname."'");
        $row3  = sqlFetchArray($query);
        $contractRatesArr = explode(",",$row3['contractRates']);
        $searchword = $cpt.":";
        $matches = array();
        foreach($contractRatesArr as $k=>$v) {
            if(preg_match("/\b$searchword\b/i", $v)) {
                $matches[$k] = $v;
                $allowedRateStr = $v;
            }
        }
        $allowedRateArr = explode(":",$allowedRateStr);
        // output: Array ( [0] => 99347 [1] => 57.45 )
        $allowedrate = $allowedRateArr[1];
    endif;
    //echo "remainingDeductible = ". $remainingDeductible." <br />";
    //echo "allowedrate = ". $allowedrate." <br />";
    
    $contrDisc = $chargedAmount - $allowedrate; // Contractual Discount
    
    // Get user type role based on the user id so that we decide allowed rate percentage
    $query = sqlStatement("SELECT usertype_role FROM tbl_user_custom_attr_1to1 WHERE userid = ".$aid);
    $row   = sqlFetchArray($query);
    $userRole = $row['usertype_role'];
    
    // We will calculate MD/NP/PA allowed amount only when the Insurance Type is Medicare
    // So, we are going to hardcode Medicare Part B insurance Type ID here which is 2
    //echo $insType;
    if($insType == 2):
        $percentage = 1;
        if($userRole == 'MD'):
            $percentage = 0.8;
            $allowedrate = $allowedrate * $percentage;
        endif;
        if($userRole == 'NP' || $userRole == 'PA'):
            // percentage is 85% of that of MD. So, it is 0.85 * 0.8 = 0.68
            $percentage = 0.68;
            $allowedrate = $allowedrate * $percentage;
        endif;
    endif;    

    
    $calculatedAllowedRate = 0;
    if($remainingDeductible > $allowedrate):
        $calculatedAllowedRate = $allowedrate;
    else:
        $calculatedAllowedRate = $remainingDeductible;
    endif;
    
    $insRespon = $calculatedAllowedRate - $remainingDeductible;
    
    $estimation = $copay + $coinsurance + $calculatedAllowedRate;
    
    $catQuery = sqlStatement("SELECT pc_catname FROM openemr_postcalendar_categories WHERE pc_catid = ?",array($catid));
    $catArr = sqlFetchArray($catQuery);
        
?>  

    <div class="contenar">
        <div class="contenar-piechart">
            <div><div style="position: absolute;margin-left: 25px;"><a href="#" class="btn btn-primary" id="print_sepr" onclick="window.print();"><span aria-hidden="true" class="glyphicon glyphicon-print"></span> Print</a></div><div id="etitle"><span>SUMMARY OF ESTIMATED PATIENT RESPONSIBILITY</span></div></div>
            <div id="header">
                <div id="pname">Paitent: <span style="font-weight:bold;"><?php echo $pname; ?></span></div>
                <div id="Account">Account #: <span style="font-weight:bold;"><?php echo $pid; ?></span></div>
                <div id="dos">Date of Service: <span style="font-weight:bold;"><?php echo $moddos; ?></span></div>
                <div id="dos">Visit Category: <span style="font-weight:bold;"><?php echo $catArr['pc_catname']; ?></span></div>
            </div>
            <div class="piechart">
                <img src="images/piechart.png" id="chartimage" alt="PieChart" width="100%" height="100%"/>
                <div id="t_responsibility" class="currency large"><?php setlocale(LC_MONETARY, 'en_US'); echo money_format('%.2n',$chargedAmount); ?></div>
                 <div id="ins_responsibility" class="currency large"><?php setlocale(LC_MONETARY, 'en_US'); echo money_format('%.2n',$insRespon); ?></div>
                 <div id="estimate" class="currency large"><?php setlocale(LC_MONETARY, 'en_US'); echo money_format('%.2n',$estimation) ?></div>
                 <div id="copay" class="currency small"><?php setlocale(LC_MONETARY, 'en_US'); echo money_format('%.2n',$copay) ?></div>
                 <div id="deductible" class="currency small"><?php setlocale(LC_MONETARY, 'en_US'); echo money_format('%.2n',$calculatedAllowedRate) ?></div>
                 <div id="coinsturance" class="currency small"><?php setlocale(LC_MONETARY, 'en_US'); echo money_format('%.2n',$coinsurance) ?></div>
            </div>
            <div id="products" class="tcard">
                <div class="trowcard">
                    <div id="pname" style="font-size: 20px;color: #356e83 !important;width: 75%; padding-left: 10px; ">PROCEDURES</div>
                    <div id="Account" style="font-size: 15px;font-weight: bold;text-align: right; padding-right: 10px;">Amount</div>
                </div>
                <div class="trowcard" style="background: #e6e6e6 !important;height: 28px;">
                    <div id="pname" style="font-size: 15px;font-weight: bold;vertical-align: middle;padding: 0px 0 0 15px;"><?php echo str_replace("_"," ",$audType); ?></div>
                    <div id="Account" style="text-align: right;font-size: 16px;font-weight: bold;padding-right: 10px;vertical-align: middle;"><?php setlocale(LC_MONETARY, 'en_US'); echo money_format('%.2n',$chargedAmount); ?></div>
                </div>
                <div class="trowcard" style="-webkit-print-color-adjust: exact;background: #0082b4 !important;color: #fff !important;height: 38px;">
                    <div id="pname" style="font-size: 15px;font-weight: bold;vertical-align: middle;padding: 0px 0 0 15px;">Procedures Total Charge</div>
                    <div id="Account" style="text-align: right;font-size: 16px;font-weight: bold;padding-right: 10px;vertical-align: middle;"><?php setlocale(LC_MONETARY, 'en_US'); echo money_format('%.2n',$chargedAmount); ?></div>
                </div>
            </div>
            <div id="procedurestc" class="tcard">
                <div class="trowcard">
                    <div id="pname" style="font-size: 20px;color: #356e83 !important;width: 75%; padding-left: 10px; ">INSURANCE DISCOUNT</div>
                    <div id="Account" style="font-size: 15px;font-weight: bold;text-align: right; padding-right: 10px;">Amount</div>
                </div>
                <div class="trowcard" style="background: #e6e6e6 !important;height: 28px;">
                    <div id="pname" style="font-size: 15px;font-weight: bold;vertical-align: middle;padding: 0px 0 0 15px;">Contractual Discount</div>
                    <div id="Account" style="text-align: right;font-size: 16px;font-weight: bold;padding-right: 10px;vertical-align: middle;"><?php setlocale(LC_MONETARY, 'en_US'); echo money_format('%.2n',$contrDisc); ?></div>
                </div>
                <div class="trowcard" style="-webkit-print-color-adjust: exact;background: #0082b4 !important;color: #fff !important;height: 38px;">
                    <div id="pname" style="font-size: 15px;font-weight: bold;vertical-align: middle;padding: 0px 0 0 15px;">Total Charge after Insurance Discount</div>
                    <div id="Account" style="text-align: right;font-size: 16px;font-weight: bold;padding-right: 10px;vertical-align: middle;"><?php setlocale(LC_MONETARY, 'en_US'); echo money_format('%.2n',$allowedrate); ?></div>
                </div>
            </div>
            <div id="insurancerb" class="tcard">
                <div class="trowcard">
                    <div id="pname" style="font-size: 20px;color: #356e83 !important;width: 75%; padding-left: 10px; ">INSURANCE RESPONSIBILITY</div>
                    <div id="Account" style="font-size: 15px;font-weight: bold;text-align: right; padding-right: 10px;">Amount</div>
                </div>
                <div class="trowcard" style="background: #e6e6e6 !important;height: 28px;">
                    <div id="pname" style="font-size: 15px;font-weight: bold;vertical-align: middle;padding: 0px 0 0 15px;">Insurance Responsibility</div>
                    <div id="Account" style="text-align: right;font-size: 16px;font-weight: bold;padding-right: 10px;vertical-align: middle;"><?php setlocale(LC_MONETARY, 'en_US'); echo money_format('%.2n',$insRespon); ?></div>
                </div>
                <div class="trowcard" style="-webkit-print-color-adjust: exact;background: #0082b4 !important;color: #fff !important;height: 38px;">
                    <div id="pname" style="font-size: 15px;font-weight: bold;vertical-align: middle;padding: 0px 0 0 15px;">Total Estimated Amount Owed (After Insurance)</div>
                    <div id="Account" style="text-align: right;font-size: 16px;font-weight: bold;padding-right: 10px;vertical-align: middle;"><?php setlocale(LC_MONETARY, 'en_US'); echo money_format('%.2n',$estimation); ?></div>
                </div>
            </div>
        </div>
        <div id="footer" class="tcard">
                <div class="trowcard">
                    <div id="pname" style="width: 83%; font-size: 13px;"><em>THIS IS AN <b>ESTIMATE</b>. Please note that this is an <b>estimate</b> of the charges for exam(s) ordered. Additional charges will apply should the
order change or if additional studies are performed. In addition, this <u><b>may not</b></u> include ALL charges for material, ancillary procedures (i.e.
injections, isotopes, tray, etc) or Professional Interpretation. You will be billed separately for these items where applicable. Thank you.</em></div>
                    <div id="Account" style="font-size: 13px;text-align: right; ">Estimate Date:<br/><?php echo $currentdate; ?></div>
                </div>
            </div>
    </div>

</body>
</html>