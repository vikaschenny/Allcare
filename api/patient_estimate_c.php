 <?php
 /* Creates the full url string for the purpose of creating the data representation */

   // $form_url = 'https://estimationapi.zirmed.com/1.0/estimate/101247';
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
                                        'PatientTypeId'    => 4,
                                        'MedicalServiceId' =>'',
                                        'ProviderType'     =>'',
                                        'Stc' => 42), 
       
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

//  Create a “representation” of the request by concatenating the following values into a single string (in this order, with no separators):
    echo $posted = "POSThttps://estimationapi.zirmed.com/1.0/estimate/".$date."101247".$formateddata;
    echo "<br /><br />";
    

echo $encoded_signature = base64_encode(hash_hmac('sha256', $posted, 'f6sdnmV1ItrAoOzlR1QZRSGSnGng5HV0KQZtSR4U',true));
//  $representation = sprintf("{0}{1}{2}{3}{4}",
//
//       'GET',
//
//       $form_url,
//
//       $formated_date,
//
//       $custId,
//
//       base64_encode($body));

//    $representation = sprintf('GET'.$form_url.$formated_date.$custId.base64_encode($body));


/* Create the HMAC hash based off of the supplied key and representation */

//  HMACSHA256 hmac = new HMACSHA256();
//
//  hmac.Key = Encoding.UTF8.GetBytes(API_KEY);
//
//  $authHash = Convert.ToBase64String(hmac.ComputeHash(Encoding.UTF8.GetBytes(representation)));

//  $s = hash_hmac('sha256', 'Message', 'secret', true);
//  echo base64_encode($s);
//
//    $s = hash_hmac('sha256', $representation, 'secret', true);
//    $authHash =  base64_encode($s);
//    /* Build the auth string */
//
//    $authString = sprintf("$custId:$authHash");
//
//    echo $authString;

//  return new Base64 ("HMAC", authString);

?>
