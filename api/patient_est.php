<?php
   $data = array('ZirMedCustID'  => '101247', 
                'SequenceNo'    => '0', 
                'IsFabricated'  => 'false', 
                'Patient'       => array('FirstName'   => 'Frances',
                                         'LastName'    => 'Adams',
                                         'Gender'      => 'F',
                                         'DateOfBirth' => '05/25/1947',
                                         'AccountNumber'=> '',
                                         'EmailAddress' => '' ), 
                                  
                'Insurance'     => array('PayerName'               =>'Aetna',
                                        'ZirMedPayerID'            =>'60054',
                                        'MemberID'                 =>'MEBG9H7V',
                                        'RelationshipToSubscriber' =>'Self',
                                        'OutOfNetwork'             => 'false',
                                        'GroupNumber'              =>''),
       
                'Encounter'     => array('ProviderName'    => 'Ketha Sumana',
                                        'ProviderNPI'      => '1962447805',
                                        'TaxonomyID'       => '',
                                        'DateOfService'    =>'11/06/2015',   //mm/dd/yyy
                                        'StcCode' => '98'), 
       
                'Procedures'    => array(['Code'     =>'99214',
                                         'Quantity' =>'1',
                                         'Mod1'     =>'',
                                         'Mod2'     =>'',
                                         'Mod3'     => '',
                                         'Mod4'     => ''])
            );
   
   
   
    $formateddata = base64_encode(json_encode($data));
     
    
//   Set the Date header on the HTTP Request to the current UTC date/time expressed in RFC 1123 format (example: Mon, 12 Jan 2015 20:50:07 GMT)
    $date = gmdate(DATE_RFC1123) ; echo "<br />";
    $formatDate = gmdate(DATE_ATOM);
    
    $date = substr($date, 0, -5);
    echo $formatDate = substr($formatDate, 0, -6);

//  Create a “representation” of the request by concatenating the following values into a single string (in this order, with no separators):
    $posted = "POSThttps://estimationapi.zirmed.com/1.0/estimate".$formatDate."101247".$formateddata;
    $encoded_signature = base64_encode(hash_hmac('sha256',$posted, 'f6sdnmV1ItrAoOzlR1QZRSGSnGng5HV0KQZtSR4U',true));
   
   
   //echo "<pre>"; print_r(json_encode($arr)); echo "</pre>";
   
   $form_url = 'https://estimationapi.zirmed.com/1.0/estimate';
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
   curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
   curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
   curl_setopt($curl, CURLOPT_POST, true);
   //// This is the fields to post in the form of an array.
   curl_setopt($curl,CURLOPT_POSTFIELDS, json_encode($data));
   curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
   ////execute the post

   echo $result = curl_exec($curl);
   if(curl_errno($curl)){   echo 'Curl error: '.curl_error($curl);}
   ////close the connection
   curl_close($curl);
?> 