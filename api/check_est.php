<?php
//SANITIZE ALL ESCAPES
$sanitize_all_escapes=true;
//

//STOP FAKE REGISTER GLOBALS
$fake_register_globals=false;
//

require_once("../../globals.php"); 
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
//echo "<pre>";print_r($set_patient_data);echo "</pre>";
$set_patient_data = sqlFetchArray($get_patient_data);
if($set_patient_data){
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
                                        'DateOfService'    => '11/06/2015',   //mm/dd/yyy
                                        'StcCode' => $zirmedStcCode), 
       
                'Procedures'    => $codesarray,
            );
   
   
}
      
    $formateddata = base64_encode(json_encode($data));
     
    
//   Set the Date header on the HTTP Request to the current UTC date/time expressed in RFC 1123 format (example: Mon, 12 Jan 2015 20:50:07 GMT)
    $date = gmdate(DATE_RFC1123) ; echo "<br />";
    $formatDate = gmdate(DATE_ATOM);
    
    $date = substr($date, 0, -5);
    $formatDate = substr($formatDate, 0, -6);
    

//  Create a “representation” of the request by concatenating the following values into a single string (in this order, with no separators):
    $posted = "GEThttps://estimationapi.zirmed.com/1.0/document/226040".$formatDate."101247";
    $encoded_signature = base64_encode(hash_hmac('sha256',$posted, 'f6sdnmV1ItrAoOzlR1QZRSGSnGng5HV0KQZtSR4U',true));
   
   
   //echo "<pre>"; print_r(json_encode($arr)); echo "</pre>";
   
   $form_url = 'https://estimationapi.zirmed.com/1.0/document/226040';
   $curl = curl_init();
   
   //// Set the options
   curl_setopt($curl,CURLOPT_URL, $form_url);
   
   $hmacSignature = 'HMAC 101247:'.$encoded_signature;
   
   $headers = array('Content-Type: application/json',sprintf('Date: %s',$date),
                     sprintf('Authorization: %s', $hmacSignature));
   
//   $request_headers = array();
//   $request_headers[] = 'Content-type:application/json';
//   $request_headers[] = 'Authorization:'.$hmacSignature;
   curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
   //   
   //curl_setopt_array($curl, array( CURLOPT_HTTPHEADER=>array($hmacSignature), CURLOPT_RETURNTRANSFER=>true, CURLOPT_VERBOSE=>1 ));
   //// This sets the number of fields to post
  // curl_setopt($curl,CURLOPT_POST, sizeof($data));
   //curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
   curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
   //// This is the fields to post in the form of an array.
   //curl_setopt($curl,CURLOPT_POSTFIELDS, json_encode($data));
   //curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
   ////execute the post

   $result = curl_exec($curl);
   if(curl_errno($curl)){   echo 'Curl error: '.curl_error($curl);}
   ////close the connection
   curl_close($curl);
   
   
    $destination = dirname(__FILE__) . '/estdownload.pdf';
    $file = fopen($destination, "w+");
    fputs($file, $result);
    fclose($file);
    
?> 

<embed src="<?php echo 'http://'. $_SERVER['HTTP_HOST'].'/api/estdownload.pdf'; ?>" width="800" height="600" alt="pdf">