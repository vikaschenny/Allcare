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
require_once("$srcdir/patient.inc");

$date           = $_REQUEST['evtdate'];
$id             = $_REQUEST['patientid'];
$enc_value      = $_REQUEST['encounterid'];
$selectedids    = $_REQUEST['selectedids'];

$pc_aids         = '';
if(is_array($selectedids)){
    $providerslist2  = '';
    $providerslist   = '';
    foreach($selectedids as $pkey => $pvalue){
        $query2         .= " `users`  REGEXP ('".":\"".$pvalue."\"') OR ";
        $providerslist2 .= " $pvalue,";
    }
    $providerslist = rtrim($providerslist2, ",");
    if($providerslist != '')
        $pc_aids = " ope.pc_aid IN ($providerslist) AND ";
 }else{
    $query          = " `users`  REGEXP ('".":\"".$selectedids."\"') ";
    $pc_aids        = " ope.pc_aid = '$selectedids' AND ";
    $providerslist  = $selectedids;
 }

$base_url="//".$_SERVER['SERVER_NAME'].dirname($_SERVER["REQUEST_URI"].'?').'/';
?>

    <div class="col-xs-7">
        <h2 class="aptitle">Appointment Details</h2>
    </div>
    <div class="col-xs-5">
        <div align="right" id="print1">
            <a class="print-btn hidden-print btn btn-info btn-md" href="#"><span aria-hidden="true" class="glyphicon glyphicon-print"></span> Print</a>
        </div>
    </div>
    <div class = "clearfix"></div> 
    <?php 
    $getdatafilter = sqlStatement("SELECT * FROM tbl_providerportal_filters WHERE userid = '".$_SESSION['portal_userid']."' AND screen_name = 'home'");
    $setdatafilter = sqlFetchArray($getdatafilter);
    if(!empty($setdatafilter)){
        $updatefilter = sqlStatement("UPDATE tbl_providerportal_filters SET af_providers = '$providerslist', af_visittype = '', `date` = NOW() WHERE userid = '".$_SESSION['portal_userid']."' AND screen_name = 'home'");
    }else{
        $insertfilter = sqlStatement("INSERT INTO tbl_providerportal_filters (`userid`,`af_providers`,`af_visittype`, `date`,`screen_name`) VALUES ('".$_SESSION['portal_userid']."','$providerslist', '', NOW(),'home') ");
    }
    
    $sql_pdata  = sqlStatement("SELECT ope.*,pc_startTime,p.fname,p.lname ,lo.title as stat,p.*
                                FROM patient_data p INNER JOIN openemr_postcalendar_events ope ON p.pid=ope.pc_pid
                                inner join openemr_postcalendar_categories pc on pc.pc_catid=ope.pc_catid 
                                inner join list_options lo on lo.option_id=ope.pc_apptstatus
                                WHERE $pc_aids ope.pc_eventdate='$date' and p.practice_status = 'YES' AND p.deceased_stat != 'YES' AND (p.deceased_date = '' OR p.deceased_date = '0000-00-00 00:00:00' ) and ope.pc_catid IN ($enc_value)  GROUP BY  pc_eid");    
     $d1=explode("-",$date);
     $d2=$d1[0].sprintf("%02d", $d1[1]).sprintf("%02d", $d1[2]);
     $count = 0;
     $gettitle = sqlStatement("SELECT title,field_id FROM layout_options WHERE form_id = 'DEM' AND field_id IN ('Copay','Deduct_Fam_Ann','Deduct_Indiv_Ann','indoutofpocket','familyoutofpocket','Elig_Note','DOB','sex','ss','suite','street','city','state','postal_code','country_code','email','phone_home','phone_cell','last_visit','providerID','patient_facility','financial_review','living_facility','referral_source','referred_date','living_facility_org','referlink','hhagency','cs_medication')");
     while($settitle = sqlFetchArray($gettitle)){
         ${$settitle['field_id']} = $settitle['title'];
     }
     
     
     
     $arr = array();
     $j = 0;
     while($row=sqlFetchArray($sql_pdata)){
         $patientbalance = get_patient_balance($row['pid'], false); 
         $eid            = $row['pc_eid'];
         $count++;
          if(strlen($row['pc_hometext']) > 15):
                $comm = substr ($row['pc_hometext'], 0, 15);
                $comments="$comm"."...";
          else:
                $comments=$row['pc_hometext'];
          endif;
          echo "<div  class='col-lg-4 col-xs-12 col-sm-6 col-md-6 group'><div class='appointment' style='border: 2px solid #cbd1d2;  font-size:12px !important;'>";
          ?>
          <table width="100%">
                <tr>
                    <td colspan="2">
                        <div class="customgroup hidden-print btn-group">
                            <a class="btn btn-primary btncostime" data-href="patient_check_in.php?pid=<?php echo $row['pid']; ?>" data-title="Check-In" data-frameheight="450" data-modalsize='modal-lg' data-bodypadding='0' class="appname" data-toggle='modal' data-target='#modalwindow' style="cursor: pointer;">Check-In</a>
                            <a class="btn btn-primary btncostime" data-href="patient_check_out.php?pid=<?php echo $row['pid']; ?>" data-title="Check-Out" data-frameheight="450" data-modalsize='modal-lg' data-bodypadding='0' class="appname" data-toggle='modal' data-target='' style="cursor: pointer;">Check-Out</a>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>Appointment:</b>
                    </td>
                    <td style="text-align: right;">
                        <?php
                        $squery  = sqlStatement("SELECT pc_apptstatus FROM openemr_postcalendar_events WHERE pc_eid = '$eid' AND pc_pid = '".$row['pid']."'");
                        $sFetch = sqlFetchArray($squery);
                        if($sFetch['pc_apptstatus'] == '@'):
                        ?>
                        <a class='various hidden-print editapp' data-title="Arrived" data-frameheight="460" data-modalsize='modal-lg' data-bodypadding='0' data-href='mark_as_arrived.php?dos=<?php echo $d2; ?>&pc_eid=<?php echo $eid; ?>&patientid=<?php echo $row['pid']; ?>' data-toggle='modal' data-target='#modalwindow' ><img src='images/editapp_success.png' alt='mark arrived' data-toggle="tooltip" data-placement="bottom" title="Arrived" width='25px'></a>
                        <?php
                        elseif($sFetch['pc_apptstatus'] == '&#'):
                        ?>
                        <a class='various hidden-print editapp' data-title="Mark As Arrived" data-frameheight="460" data-modalsize='modal-lg' data-bodypadding='0' data-href='mark_as_arrived.php?dos=<?php echo $d2; ?>&pc_eid=<?php echo $eid; ?>&patientid=<?php echo $row['pid']; ?>' data-toggle='modal' data-target='#modalwindow' ><img src='images/editapp.png' alt='mark arrived' data-toggle="tooltip" data-placement="bottom" title="Mark As Arrived" width='25px'></a>
                        <?php
                        endif;
                        ?>
                        <a class='various hidden-print createapp' data-title="Create Appointment" data-frameheight="325" data-modalsize='modal-md' data-bodypadding='0' data-href='edit_appointment.php?date=<?php echo $d2; ?>&provider=<?php echo $id; ?>' data-toggle='modal' data-target='#modalwindow'><img src='images/createapp.png' alt='create appointment' data-toggle="tooltip" data-placement="bottom" title=" Create Appointment" width='25px' ></a>
                        <a class='various hidden-print editapp' data-title="Edit Appointment" data-frameheight="325" data-modalsize='modal-md' data-bodypadding='0' data-href='edit_appointment.php?date=<?php echo $d2; ?>&eid=<?php echo $eid; ?>&provider=<?php echo $id; ?>' data-toggle='modal' data-target='#modalwindow' ><img src='images/editapp.png' alt='edit appointment' data-toggle="tooltip" data-placement="bottom" title="Edit Appointment" width='25px'></a>
                    </td>
                </tr>
                <tr>
                    <td style="vertical-align: top">
                        Name
                    </td>
                    <td style="vertical-align: top">
                        : <a data-href="patient_file/summary/demographics.php?set_pid=<?php echo $row['pid']; ?>" data-title="Patient Information" data-frameheight="450" data-modalsize='modal-lg' data-bodypadding='0' class="appname" data-toggle='modal' data-target='#modalwindow' style="cursor: pointer;"><?php echo $row['fname'].' '.$row['lname']; ?></a>
                    </td>
                </tr>
                <tr>
                    <td style="vertical-align: top">
                        Date
                    </td>
                    <td style="vertical-align: top">
                        : <?php echo $date; ?>
                    </td>
                </tr>
                <tr>
                    <td style="vertical-align: top">
                        Time
                    </td>
                    <td style="vertical-align: top">
                        : <?php echo $row['pc_startTime']; ?>
                    </td>
                </tr>
                <tr>
                    <td style="vertical-align: top">
                        Visit Type
                    </td>
                    <td style="vertical-align: top">
                        : <?php echo $row['pc_startTime']; ?>
                    </td>
                </tr>
                <tr>
                    <td style="vertical-align: top">
                        Visit Cat
                    </td>
                    <td style="vertical-align: top">
                        : <?php echo $row['pc_title']; ?>
                    </td>
                </tr>
                <tr>
                    <td style="vertical-align: top">
                        Status
                    </td>
                    <td style="vertical-align: top">
                        : <?php echo $row['stat']; ?>
                    </td>
                </tr>
                <tr>
                    <td style="vertical-align: top">
                        Comments
                    </td>
                    <td style="vertical-align: top">
                        : <?php echo $comments; ?>
                    </td>
                </tr>
                <tr>
                    <td style="line-height:10px;" colspan=2>&nbsp;</td> 
                </tr>
                <tr>
                    <td >
                        <b>Insurance Company</b>
                    </td>
                    <td style="text-align: right;">                                                
                        <a class='various hidden-print editins' data-title="Edit Insurance" data-frameheight="500" data-modalsize='modal-lg' data-bodypadding='0' data-href='create_patient/edit_custom_insurance.php?provider=<?php echo $provider; ?>&pid=<?php echo $pid; ?>' data-toggle='modal' data-target='#modalwindow'><img src='images/ins_edit.png' alt='Edit Insurance' data-toggle="tooltip" data-placement="bottom" title=" Edit Insurance" width='25px' ></a>
                        <a class='various hidden-print Benefits' data-title="Benefits" data-frameheight="325" data-modalsize='modal-md' data-bodypadding='0' data-href='create_patient/edit_custom_insurance.php?provider=<?php echo $provider; ?>&pid=<?php echo $pid; ?>' data-toggle='modal' data-target='#modalwindow' ><img src='images/benefits.png' alt='Benefits' data-toggle="tooltip" data-placement="bottom" title=" Benefits" width='31px'></a>
                        <a class='various hidden-print eligibility' data-title="Eligibility" data-frameheight="325" data-modalsize='modal-lg' data-bodypadding='0' data-href='verify_eligibility.php' data-toggle='modal' data-target='#modalwindow' ><img src='images/eligibility.png' alt='Eligibility' data-toggle="tooltip" data-placement="bottom" title=" Eligibility" width='25px'></a>
                    </td>
                </tr>
                <?php 
                foreach (array('primary','secondary','tertiary') as $instype) {
                    $get_insurance_data = sqlStatement("SELECT (SELECT name FROM insurance_companies WHERE id = i.id) as insurancename,plan_name,policy_number FROM insurance_data i WHERE pid = '".$row['pid']."' AND type='$instype' ORDER BY i.date DESC LIMIT 0,1");
                    while($set_insurance_data = sqlFetchArray($get_insurance_data)){ ?>
                    <tr>
                        <td>
                            <?php
                                echo ucwords($instype)." Payer";
                            ?>
                        </td>
                        <td>
                            : <?php echo $set_insurance_data['insurancename']; ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <?php
                                echo "Plan Name";
                            ?>
                        </td>
                        <td>
                            : <?php echo $set_insurance_data['plan_name']; ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <?php
                                echo "Policy Number";
                            ?>
                        </td>
                        <td>
                            : <?php echo $set_insurance_data['policy_number']; ?>
                        </td>
                    </tr>
                    <?php } 
                }?>
                <tr>
                    <td style="line-height:10px;" colspan=2>&nbsp;</td> 
                </tr>    
                <tr>
                    <td>
                        <b>Collections</b>
                    </td>
                </tr>
                <tr>
                    <td style="vertical-align: top">
                        <?php
                        if($Copay){
                            echo $Copay;
                        }else{
                            echo "Copay";
                        }
                        ?>
                    </td>
                    <td style="vertical-align: top;padding-bottom: 6px;padding-top: 3px;">
                        : <?php echo $row['Copay']; ?><a class='various hidden-print copay' data-title="Copay" data-frameheight="325" data-modalsize='modal-md' data-bodypadding='0' data-href='create_patient/edit_custom_insurance.php?provider=<?php echo $provider; ?>&pid=<?php echo $pid; ?>' data-toggle='modal' data-target='#modalwindow'><img src='images/copay.png' alt='Copay' data-toggle="tooltip" data-placement="bottom" title=" Copay" width='8px' ></a>
                    </td>
                </tr>
                <tr>
                    <td style="vertical-align: top">
                        Patient Balance
                    </td>
                    <td style="vertical-align: top padding-bottom: 6px;">
                        : <?php echo $patientbalance; ?><a class='various hidden-print blance' data-title="Patient Balance" data-frameheight="325" data-modalsize='modal-md' data-bodypadding='0' data-href='create_patient/edit_custom_insurance.php?provider=<?php echo $provider; ?>&pid=<?php echo $pid; ?>' data-toggle='modal' data-target='#modalwindow'><img src='images/paitent_blance.png' alt='Patient Balance' data-toggle="tooltip" data-placement="bottom" title=" Patient Balance" width='15px' ></a>
                    </td>
                </tr>
                <tr>
                    <td style="vertical-align: top">
                        <?php
                        if($Deduct_Fam_Ann){
                            echo $Deduct_Fam_Ann;
                        }else{
                            echo "Annual Family Deductible";
                        }
                        ?>
                    </td>
                    <td style="vertical-align: top">
                        : <?php echo $row['Deduct_Fam_Ann']; ?>
                    </td>
                </tr>
                <tr>
                    <td style="vertical-align: top">
                        <?php
                        if($Deduct_Indiv_Ann){
                            echo $Deduct_Indiv_Ann;
                        }else{
                            echo "Annual Individual Deductible";
                        }
                        ?>
                    </td>
                    <td style="vertical-align: top">
                        : <?php echo $row['Deduct_Indiv_Ann']; ?>
                    </td>
                </tr>
                <tr>
                    <td style="vertical-align: top">
                        <?php
                        if($indoutofpocket){
                            echo $indoutofpocket;
                        }else{
                            echo "Individual Out Of Pocket Expense";
                        }
                        ?>
                    </td>
                    <td style="vertical-align: top">
                        : <?php echo $row['indoutofpocket']; ?>
                    </td>
                </tr>
                <tr>
                    <td style="vertical-align: top">
                        <?php
                        if($familyoutofpocket){
                            echo $familyoutofpocket;
                        }else{
                            echo "Family Out Of Pocket Expense";
                        }
                        ?>
                    </td>
                    <td style="vertical-align: top">
                        : <?php echo $row['familyoutofpocket']; ?>
                    </td>
                </tr>
                <tr>
                    <td style="vertical-align: top">
                        Preauthorization
                    </td>
                    <td style="vertical-align: top">
                        : <?php echo $row['Elig_Note']; ?>
                    </td>
                </tr>
                <tr>
                    <td style="vertical-align: top"> 
                        <?php
                        if($Elig_Note){
                            echo $Elig_Note;
                        }else{
                            echo "Eligibility Note";
                        }
                        ?>
                    </td>
                    <td style="vertical-align: top">
                        : <?php echo $row['Elig_Note']; ?>
                    </td>
                </tr>
                <tr>
                    <td style="line-height:10px;" colspan=2>&nbsp;</td> 
                </tr>
                <tr>
                    <td>
                        <b>Patient</b>
                    </td>
                    <td style="text-align: right;">                                                
                        <a class='various hidden-print demographics' data-title="Patient Edit Demographics" data-frameheight="500" data-modalsize='modal-lg' data-bodypadding='0' data-href='patient_file/summary/demographics_full.php?pid=<?php echo $row['pid']; ?>' data-toggle='modal' data-target='#modalwindow'><img src='images/demographics.png' alt='Patient Edit Demographics' data-toggle="tooltip" data-placement="bottom" title="Patient Edit Demographics" width='25px' ></a>                                                
                    </td>
                </tr>
                <tr>
                    <td style="vertical-align: top"> 
                        <?php
                        if($DOB){
                            echo $DOB;
                        }else{
                            echo "DOB";
                        }
                        ?>
                    </td>
                    <td style="vertical-align: top">
                        : <?php echo $row['DOB']; ?>
                    </td>
                </tr>
                <tr>
                    <td style="vertical-align: top"> 
                        <?php
                        if($sex){
                            echo $sex;
                        }else{
                            echo "Sex";
                        }
                        ?>
                    </td>
                    <td style="vertical-align: top">
                        : <?php echo $row['sex']; ?>
                    </td>
                </tr>
                <tr>
                    <td style="vertical-align: top"> 
                        <?php
                        if($ss){
                            echo $ss;
                        }else{
                            echo "S.S.";
                        }
                        ?>
                    </td>
                    <td style="vertical-align: top">
                        : <?php echo $row['ss']; ?>
                    </td>
                </tr>
                <tr>
                    <td style="vertical-align: top"> 
                        <?php
                        if($suite){
                            echo $suite;
                        }else{
                            echo "App/Suite/Other";
                        }
                        ?>
                    </td>
                    <td style="vertical-align: top">
                        : <?php echo $row['suite']; ?>
                    </td>
                </tr>
                <tr>
                    <td style="vertical-align: top">
                        <?php
                        if($street){
                            echo $street;
                        }else{
                            echo "Address";
                        }
                        ?>
                    </td>
                    <td style="vertical-align: top;padding-top: 3px;">
                        : <?php echo $row['street']."<a href='#'  class='address hidden-print' data-toggle='modal' data-target='#showmap' class='mapV' id='".$row['pid']."'><img src='images/map-marker-icon.png' width='20' height='20' data-toggle='tooltip' data-placement='bottom' title=' Show map'></a>"; ?>
                        
                    </td>
                </tr>
                <tr>
                    <td style="vertical-align: top">
                        <?php
                        if($city){
                            echo $city;
                        }else{
                            echo "City";
                        }
                        ?>
                    </td>
                    <td style="vertical-align: top">
                        : <?php echo $row['city']; ?>
                    </td>
                </tr>
                <tr>
                    <td style="vertical-align: top">
                        <?php
                        if($state){
                            echo $state;
                        }else{
                            echo "State";
                        }
                        ?>
                    </td>
                    <td style="vertical-align: top">
                        <?php
                        $sql_st = sqlStatement("SELECT * from list_options where list_id='state' AND option_id='".$row['state']."'");
                        $st     = sqlFetchArray($sql_st);
                        ?>
                        : <?php echo $st['title']; ?>
                    </td>
                </tr>
                <tr>
                    <td style="vertical-align: top">
                        <?php
                        if($postal_code){
                            echo $postal_code;
                        }else{
                            echo "Postal Code";
                        }
                        ?>
                    </td>
                    <td style="vertical-align: top">
                        : <?php echo $row['postal_code']; ?>
                    </td>
                </tr>
                <tr>
                    <td style="vertical-align: top">
                        <?php
                        if($country_code){
                            echo $country_code;
                        }else{
                            echo "Country";
                        }
                        ?>
                    </td>
                    <td style="vertical-align: top">
                        <?php
                        $sql_cc   = sqlStatement("SELECT * from list_options where list_id='country' AND option_id='".$row['country_code']."'");
                        $cc       = sqlFetchArray($sql_cc);
                        ?>
                        : <?php echo $cc['title']; ?>
                    </td>
                </tr>
                <tr>
                    <td style="vertical-align: top"> 
                        <?php
                        if($email){
                            echo $email;
                        }else{
                            echo "Contact Email";
                        }
                        ?>
                    </td>
                    <td style="vertical-align: top">
                        : <?php echo $row['email']; ?>
                    </td>
                </tr>
                <tr>
                    <td style="vertical-align: top">
                        <?php
                        if($phone_home){
                            echo $phone_home;
                        }else{
                            echo "Home Phone";
                        }
                        ?>
                    </td>
                    <td style="vertical-align: top">
                        : <?php echo $row['phone_home']; ?>
                    </td>
                </tr>
                <tr>
                    <td style="vertical-align: top">
                        <?php
                        if($phone_cell){
                            echo $phone_cell;
                        }else{
                            echo "Mobile Phone";
                        }
                        ?>
                    </td>
                    <td style="vertical-align: top">
                        : <?php echo $row['phone_cell']; ?>
                    </td>
                </tr>
                <tr>
                    <td style="vertical-align: top"> 
                        <?php
                        if($last_visit){
                            echo $last_visit;
                        }else{
                            echo "Last Encounter Date";
                        }
                        ?>
                    </td>
                    <td style="vertical-align: top">
                        : <?php echo $row['last_visit']; ?>
                    </td>
                </tr>
                <tr>
                    <td style="vertical-align: top"> 
                        <?php
                        if($providerID){
                            echo $providerID;
                        }else{
                            echo "Provider";
                        }
                        ?>
                    </td>
                    <td style="vertical-align: top">
                        : <?php echo $row['providerID']; ?>
                    </td>
                </tr>
                <tr>
                    <td style="vertical-align: top"> 
                        <?php
                        if($patient_facility){
                            echo $patient_facility;
                        }else{
                            echo "Current Facility";
                        }
                        ?>
                    </td>
                    <td style="vertical-align: top">
                        : <?php echo $row['patient_facility']; ?>
                    </td>
                </tr>
                <tr>
                    <td style="vertical-align: top"> 
                        <?php
                        if($financial_review){
                            echo $financial_review;
                        }else{
                            echo "Financial Review Date";
                        }
                        ?>
                    </td>
                    <td style="vertical-align: top">
                        : <?php echo $row['financial_review']; ?>
                    </td>
                </tr>
                <tr>
                    <td style="vertical-align: top"> 
                        <?php
                        if($living_facility){
                            echo $living_facility;
                        }else{
                            echo "Living Facility";
                        }
                        ?>
                    </td>
                    <td style="vertical-align: top">
                        : <?php echo $row['living_facility']; ?>
                    </td>
                </tr>
                <tr>
                    <td style="vertical-align: top"> 
                        <?php
                        if($referral_source){
                            echo $referral_source;
                        }else{
                            echo "Referral Source";
                        }
                        ?>
                    </td>
                    <td style="vertical-align: top">
                        : <?php echo $row['referral_source']; ?>
                    </td>
                </tr>
                <tr>
                    <td style="vertical-align: top"> 
                        <?php
                        if($referred_date){
                            echo $referred_date;
                        }else{
                            echo "Referral Date";
                        }
                        ?>
                    </td>
                    <td style="vertical-align: top">
                        : <?php echo $row['referred_date']; ?>
                    </td>
                </tr>
                <tr>
                    <td style="vertical-align: top"> 
                        <?php
                        if($living_facility_org){
                            echo $living_facility_org;
                        }else{
                            echo "Current Living Facility";
                        }
                        ?>
                    </td>
                    <td style="vertical-align: top">
                        : <?php echo $row['living_facility_org']; ?>
                    </td>
                </tr>
                <tr>
                    <td style="vertical-align: top"> 
                        <?php
                        if($referlink){
                            echo $referlink;
                        }else{
                            echo "Referral Organization";
                        }
                        ?>
                    </td>
                    <td style="vertical-align: top">
                        : <?php echo $row['referlink']; ?>
                    </td>
                </tr>
                <tr>
                    <td style="vertical-align: top"> 
                        <?php
                        if($hhagency){
                            echo $hhagency;
                        }else{
                            echo "Current Home Health";
                        }
                        ?>
                    </td>
                    <td style="vertical-align: top">
                        : <?php echo $row['hhagency']; ?>
                    </td>
                </tr>
                <tr>
                    <td style="vertical-align: top"> 
                        <?php
                        if($cs_medication){
                            echo $cs_medication;
                        }else{
                            echo "CS Medication";
                        }
                        ?>
                    </td>
                    <td style="vertical-align: top">
                        : <?php echo $row['cs_medication']; ?>
                    </td>
                </tr>
            </table>
          <?php
          $arr[$j] = $row['street']."$*$".$row['city']."$*$".$row['state']."$*$".$row['postal_code']."$*$".$row['country_code']."$*$".$row['latitude']."$*$".$row['longitude']."$*$".$row['pid']."$*$".$row['fname']."$$". $row['lname']."$*$".$row['phone_home']."$*$".$row['phone_biz']."$*$".$row['phone_contact']."$*$".$row['phone_cell']."$*$".$row['openAppdate'];   
          echo "</div></div>";
//          if(($count%4==0))
//            echo "<div class = 'clearfix visible-lg'></div>";
          if(($count%3==0))
            echo "<div class = 'clearfix visible-lg'></div>";
          if(($count%2==0))
            echo "<div class = 'clearfix visible-sm visible-md visible-xs'></div>";
          $j++;
       }
       $arrString = implode("||||",$arr);
       
       echo "<input type='hidden' id='hdn_$date' name='hdnAddressBack' value=\"".$arrString."\" />";
       ?>
<script>
    $(function(){
        var addr = $('#hdn_'+'<?php echo $date; ?>').val();
        addr = addr.split("#").join("~~~");
        $('.mapV').data("hdnAddressBack1",addr);   
    });
</script>