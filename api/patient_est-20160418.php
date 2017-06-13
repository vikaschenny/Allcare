<?php
   $data = array('ZirMedCustID'  => 101247, 
                'SequenceNo'    => 0, 
                'IsFabricated'  => "true", 
                'Patient'       => array('FirstName'   => 'Ruben',
                                         'LastName'    => 'Enriquez',
                                         'Gender'      => 'M',
                                         'DateOfBirth' => '1955-05-05',
                                         'AccountNumber'=> '6268',
                                         'EmailAddress' =>'' ), 
                                  
                'Insurance'     => array('PayerName'               =>'United Health Care',
                                        'ZirMedPayerID'            =>'87726',
                                        'MemberID'                 =>'964149291',
                                        'RelationshipToSubscriber' =>'Self',
                                        'OutOfNetwork'             => false,
                                        'GroupNumber'              =>''),
       
                'Encounter'     => array('ProviderName'    =>'Sumana Ketha',
                                        'ProviderNPI'      =>'1962447805',
                                        'TaxonomyID'       =>'',
                                        'DateOfService'    =>'04/08/2016',   //mm/dd/yyy
                                        'PatientTypeId'    => '4',
                                        'MedicalServiceId' =>'',
                                        'ProviderType'     =>'',
                                        'Stc' => '42'), 
       
                'Procedures'    => array(['Code'     =>'99214',
                                         'Quantity' =>'1',
                                         'Mod1'     =>'',
                                         'Mod2'     =>'',
                                         'Mod3'     => '',
                                         'Mod4'     => ''])
            );
   
   
   
    $formateddata = base64_encode(json_encode($data));
       
    $custId = 101247;
    $body = '';
    
//   Set the Date header on the HTTP Request to the current UTC date/time expressed in RFC 1123 format (example: Mon, 12 Jan 2015 20:50:07 GMT)
    $date = gmdate(DATE_RFC1123) ; 
    $formatDate = date("y-M-dTH:m:s");

//  Create a “representation” of the request by concatenating the following values into a single string (in this order, with no separators):
    $posted = "POSThttps://estimationapi.zirmed.com/1.0/estimate/".$formatDate."101247".$formateddata;
    $encoded_signature = base64_encode(hash_hmac('sha256', $posted, 'f6sdnmV1ItrAoOzlR1QZRSGSnGng5HV0KQZtSR4U',true));
   
   
   //echo "<pre>"; print_r(json_encode($arr)); echo "</pre>";
   
   $form_url = 'https://estimationapi.zirmed.com/1.0/estimate';
   $curl = curl_init();
   
   //// Set the options
   curl_setopt($curl,CURLOPT_URL, $form_url);
   
   $hmacSignature = 'HMAC 101247:'.$encoded_signature;
   
   curl_setopt_array($curl, array( CURLOPT_HTTPHEADER	=> array($hmacSignature), CURLOPT_RETURNTRANSFER	=>true, CURLOPT_VERBOSE	=> 0 ));
   //// This sets the number of fields to post
  // curl_setopt($curl,CURLOPT_POST, sizeof($data));

   //// This is the fields to post in the form of an array.
   curl_setopt($curl,CURLOPT_POSTFIELDS, json_encode($data));
   ////execute the post
   //echo "<br /><br /><br />";
   //echo "<p style='color:blue;'><strong>Response Recieved From Zirmed</strong></p>";
   $result = curl_exec($curl);
   if(curl_errno($curl)){   echo 'Curl error: '.curl_error($curl);}
   ////close the connection
   curl_close($curl);
?>