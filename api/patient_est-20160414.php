<?php
   $data = array('ZirMedCustID'  => 101247, 
                'SequenceNo'    => 0, 
                'IsFabricated'  => "true", 
                'Patient'       => array('FirstName'   => 'Deborah',
                                         'LastName'    => 'Shetter',
                                         'Gender'      => 'F',
                                         'DateOfBirth' => '07/7/1953',
                                         'AccountNumber'=> '',
                                         'EmailAddress' =>'' ), 
                                  
                'Insurance'     => array('PayerName'               =>'Humana',
                                        'ZirMedPayerID'            =>'61101',
                                        'MemberID'                 =>'454-94-8167',
                                        'RelationshipToSubscriber' =>'Self',
                                        'OutOfNetwork'             => false,
                                        'GroupNumber'              =>''),
       
                'Encounter'     => array('ProviderName'    =>'Sumana Ketha',
                                        'ProviderNPI'      =>'1962447805',
                                        'TaxonomyID'       =>'',
                                        'DateOfService'    =>'01/13/2016',   //mm/dd/yyy
                                        'PatientTypeId'    => 4,
                                        'MedicalServiceId' =>'',
                                        'ProviderType'     =>''), 
       
                'Procedures'    => array(['Code'     =>'99350',
                                         'Quantity' =>'1',
                                         'Mod1'     =>'',
                                         'Mod2'     =>'',
                                         'Mod3'     => '',
                                         'Mod4'     => ''])
            );
   
   
   
   
   //echo "<pre>"; print_r(json_encode($arr)); echo "</pre>";
   
   $form_url = 'https://estimationapi.zirmed.com/1.0/estimate';
   $curl = curl_init();
   
   //// Set the options
   curl_setopt($curl,CURLOPT_URL, $form_url);
   
   curl_setopt_array($curl, array( CURLOPT_HTTPHEADER	=> array('HMAC 101247:ZepuzWM3VeDjOky2dCp/q4tfZT4gWTMY3azNeR7ooKo='), CURLOPT_RETURNTRANSFER	=>true, CURLOPT_VERBOSE	=> 0 ));
   //// This sets the number of fields to post
  // curl_setopt($curl,CURLOPT_POST, sizeof($data));
   echo json_encode($data);
   //// This is the fields to post in the form of an array.
   curl_setopt($curl,CURLOPT_POSTFIELDS, json_encode($data));
   ////execute the post
   //echo "<br /><br /><br />";
   //echo "<p style='color:blue;'><strong>Response Recieved From Zirmed</strong></p>";
   $result = curl_exec($curl);
   if(curl_errno($curl))
    echo 'Curl error: '.curl_error($curl);
   ////close the connection
   curl_close($curl);
?>