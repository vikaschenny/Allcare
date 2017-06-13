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

if(isset($_SESSION['portal_username']) !=''){
    $provider    = $_SESSION['portal_username'];
    $refer       = $_REQUEST['refer'];
    
    if($_REQUEST['refer']!='')
        $_SESSION['refer'] = $_REQUEST['refer'];
}else {
    $provider                    = $_REQUEST['provider'];
    $_SESSION['portal_username'] = $_REQUEST['provider'];
    //for logout
    $refer                       = $_REQUEST['refer'];
    if($_REQUEST['refer']!='')
        $_SESSION['refer'] = $_REQUEST['refer'];
}
require_once("../interface/globals.php");
require_once("$srcdir/forms.inc");
require_once("$srcdir/billing.inc");
require_once("$srcdir/patient.inc");
require_once("$srcdir/formatting.inc.php");
require_once "$srcdir/options.inc.php";
require_once "$srcdir/formdata.inc.php";
include_once("$srcdir/calendar.inc");
include_once("$srcdir/edi.inc");

// Component Element seperator
$compEleSep		= "^"; 	
$from_date      = fixDate($_REQUEST['from'], date('Y-m-d'));
$to_date        = fixDate($_REQUEST['to'], date('Y-m-d'));
$form_facility  = $_REQUEST['facility'];
$form_provider  = $_REQUEST['provider'];
$exclude_policy = $_REQUEST['removedrows'];
$X12info        = $_REQUEST['form_x12'];
$pid            = $_REQUEST['pid'];


//for logout
$refer                      = $_REQUEST['refer'];
$_SESSION['refer']          = $_REQUEST['refer'];
$_SESSION['portal_username']= $_REQUEST['provider'];
$sql = sqlStatement("SELECT id, fname, lname, specialty FROM users " .
      "WHERE active = 1 AND ( info IS NULL OR info NOT LIKE '%Inactive%' ) " .
      "AND authorized = 1 AND username='".$provider."'" .
      "ORDER BY lname, fname");
$id         = sqlFetchArray($sql); 

//Set up the sql variable binding array (this prevents sql-injection attacks)
$sqlBindArray = array();

$where  = "e.pc_pid IS NOT NULL AND e.pc_eventDate >= ?";
array_push($sqlBindArray, $from_date);

//$where .="and e.pc_eventDate = (select max(pc_eventDate) from openemr_postcalendar_events where pc_aid = d.id)";

if ($to_date) {
        $where .= " AND e.pc_eventDate <= ?";
        array_push($sqlBindArray, $to_date);
}

if($form_facility != "") {
        $where .= " AND f.id = ? ";
        array_push($sqlBindArray, $form_facility);
}

if($form_provider != "") {
        $where .= " AND d.id = ? ";
        array_push($sqlBindArray, $form_provider);
}

if($exclude_policy != ""){	
        $arrayExplode	=	explode(",", $exclude_policy);
        array_walk($arrayExplode, 'arrFormated');
        $exclude_policy = implode(",",$arrayExplode);
        $where .= " AND i.policy_number not in (".stripslashes($exclude_policy).")";
}

$where .= " AND (i.policy_number is not null and i.policy_number != '')";  
$query2 = sprintf("SELECT
                                               p.lname,
                                               p.fname,
                                               p.mname, 
                                               DATE_FORMAT(p.dob, '%%m/%%d/%%Y') as dob,
                                               if (p.sex = 'Female' ,'F','M' ) as sex,
                                               p.pid,
                                               p.pubpid,
                                               i.policy_number,
                                               i.provider as payer_id,
                                               i.subscriber_relationship,
                                               i.subscriber_lname,
                                               i.subscriber_fname,
                                               i.subscriber_mname,
                                               d.lname as provider_lname,
                                               d.fname as provider_fname,
                                               d.npi as provider_npi,
                                               d.upin as provider_pin,
                                               c.name as payer_name
                                    FROM openemr_postcalendar_events AS e
                                    LEFT JOIN users AS d on (e.pc_aid is not null and e.pc_aid = d.id)
                                    LEFT JOIN facility AS f on (f.id = e.pc_facility)
                                    LEFT JOIN patient_data AS p ON p.pid = e.pc_pid
                                    LEFT JOIN insurance_data AS i ON (i.id =(
                                                                            SELECT id
                                                                            FROM insurance_data AS i
                                                                            WHERE pid = p.pid AND type = 'primary'
                                                                            ORDER BY date DESC
                                                                            LIMIT 1
                                                                            )
                                                                    )
                                    LEFT JOIN insurance_companies as c ON (c.id = i.provider) 
                                    WHERE %s %s",	$where, " AND p.pid = '$pid' ORDER BY p.lname,p.fname,p.mname ASC");
        
        
$getcred = sqlStatement("SELECT title,option_id FROM list_options WHERE list_id='AllCareConfig' and option_id IN('zirmedUsername','zirmedPassword','zirmedCustID','zirmedResponseType','zirmedStcCode')");
while($setcred = sqlFetchArray($getcred)){
    ${$setcred['option_id']} = $setcred['title'];
}
$get_encounter_id = sqlStatement("SELECT encounter, DATE_FORMAT( form_encounter.date,  '%m/%d/%Y' ) AS encounter_date FROM form_encounter
                            WHERE pid=$pid 
                            AND DATE(date)='".$from_date."'");

$set_encounter_id = sqlFetchArray($get_encounter_id);
$event_date = $set_encounter_id['encounter_date'];
$encounter_id = $set_encounter_id['encounter'];

$codesarray = array();
$get_billing_data = sqlStatement("select units,code from billing where encounter = '$encounter_id' and pid='$pid' and code_type='CPT4' and activity=1 order by code_type, date ASC");
while($set_billing_data = sqlFetchArray($get_billing_data) ){
//    echo $set_billing_data['code']."=".$set_billing_data['units'];
    $codesarray[] =  ['Code'     =>$set_billing_data['code'],
                            'Quantity' =>$set_billing_data['units'],
                            'Mod1'     =>'',
                            'Mod2'     =>'',
                            'Mod3'     => '',
                            'Mod4'     => ''];
}

$get_patient_data      = sqlStatement($query2, $sqlBindArray);
$zirmedCustID  = trim($zirmedCustID);
$data = array();
//echo "<pre>";print_r($set_patient_data);echo "</pre>";
while($set_patient_data = sqlFetchArray($get_patient_data)){
//    ${$setcred['option_id']} = $setcred['title'];
//}
    $payer = explode("(",$set_patient_data['payer_name']);
    $payer_id = '';
    $payer_name = '';
    for($i=0; $i< count($payer); $i++){
        if($i == (count($payer)-1))
            $payer_id   = rtrim($payer[$i],")");
        else
            $payer_name .= $payer[$i];
    }
    $data = array('ZirMedCustID'  => $zirmedCustID,  
                'SequenceNo'    => '0', 
                'IsFabricated'  => 'false', 
                'Patient'       => array('FirstName'   => $set_patient_data['fname'],//'Frances',
                                         'LastName'    => $set_patient_data['lname'],//'Adams',
                                         'Gender'      => $set_patient_data['sex'],//'F',
                                         'DateOfBirth' => $set_patient_data['dob'],//'05/25/1947',
                                         'AccountNumber'=> '',
                                         'EmailAddress' => '' ), 
                                  
                'Insurance'     => array('PayerName'               =>$payer_name,//'Aetna',
                                        'ZirMedPayerID'            =>$payer_id,//'60054',
                                        'MemberID'                 =>$set_patient_data['policy_number'],//'MEBG9H7V',
                                        'RelationshipToSubscriber' =>$set_patient_data['subscriber_relationship'],//'Self',
                                        'OutOfNetwork'             => 'false',
                                        'GroupNumber'              =>''),
       
                'Encounter'     => array('ProviderName'    => $set_patient_data['provider_lname']." ".$set_patient_data['provider_fname'],//'Ketha Sumana',
                                        'ProviderNPI'      => $set_patient_data['provider_npi'],//'1962447805',
                                        'TaxonomyID'       => '',
                                        'DateOfService'    => $event_date,//'11/06/2015',   //mm/dd/yyy
                                        'StcCode' => $zirmedStcCode), 
       
                'Procedures'    => $codesarray,
            );
   
}   $formateddata = base64_encode(json_encode($data));
     
    
//   echo "<pre>"; print_r($data); echo "</pre>";exit();
   
//   Set the Date header on the HTTP Request to the current UTC date/time expressed in RFC 1123 format (example: Mon, 12 Jan 2015 20:50:07 GMT)
    $date = gmdate(DATE_RFC1123) ; 
    $formatDate = gmdate(DATE_ATOM);
    
    $date = substr($date, 0, -5);
    $formatDate = substr($formatDate, 0, -6);

//  Create a “representation” of the request by concatenating the following values into a single string (in this order, with no separators):
    $posted = "POSThttps://estimationapi.zirmed.com/1.0/estimate".$formatDate."$zirmedCustID".$formateddata;
    $encoded_signature = base64_encode(hash_hmac('sha256',$posted, 'f6sdnmV1ItrAoOzlR1QZRSGSnGng5HV0KQZtSR4U',true));
   
   
    
    // This is the data to POST to the form. The KEY of the array is the name of the field. The value is the value posted.
        $data_to_post = array();
        $data_to_post['posteddata']         = $formateddata;
        $data_to_post['CustID']         = $zirmedCustID;
        
    
   
   $form_url = 'https://estimationapi.zirmed.com/1.0/estimate';
   $curl = curl_init();
   
   //// Set the options
   curl_setopt($curl,CURLOPT_URL, $form_url);
   
   $hmacSignature = 'HMAC '.$zirmedCustID.':'.$encoded_signature;
   
   $headers = array('Content-Type: application/json',sprintf('Date: %s',$date),
                     sprintf('Authorization: %s', $hmacSignature));

   curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

   curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
   curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
   curl_setopt($curl, CURLOPT_POST, true);
   //// This is the fields to post in the form of an array.
   curl_setopt($curl,CURLOPT_POSTFIELDS, json_encode($data));
   curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
   ////execute the post

   $result = curl_exec($curl);
   
   $data_to_post['estID']         = $result;
   
   if(curl_errno($curl)){   echo 'Curl error: '.curl_error($curl);}
   ////close the connection
   curl_close($curl);
   
   
   $form_url = 'https://qa2allcare.dfwprimary.com/interface/main/allcarereports/verify_estimation.php';
   $curl = curl_init();

        //
        //// Set the options
        curl_setopt($curl,CURLOPT_URL, $form_url);
        //
        //  This sets the number of fields to post
        curl_setopt($curl,CURLOPT_POST, sizeof($data_to_post));
        //echo "<p style='color:blue;'><strong>Requested Data Sent to Zirmed.</strong></p>";
        //echo "<p style='color:blue;'>Parameters passed are: <ol style='color:blue;'><li>Userid</li><li>Password</li><li>CustId</li><li>X12 DATA</li><li>Input File format (X12)</li><li>Response format (HTML)</li></ol></p>";
        //print_r($data_to_post);
        //
        //  This is the fields to post in the form of an array.
        curl_setopt($curl,CURLOPT_POSTFIELDS, $data_to_post);
        //
        ////execute the post
        //echo "<br /><br /><br />";
        //echo "<p style='color:blue;'><strong>Response Recieved From Zirmed</strong></p>";
        $result = curl_exec($curl);
   
?>
