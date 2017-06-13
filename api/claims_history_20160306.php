<?php


// Define URL where the form resides
$form_url = 'https://www.zirmed.com/Services/ServiceHandler.ashx';


//To Create Signature
$posted="CustID=101246&DOS=10/26/2015&ClaimNum=3179-8507&ReqType=CLM&Version=1.0";
// remove delimiters "&" and "="
$exploded_string = explode("&", $posted);

$posted_array = array();

foreach($exploded_string as $ekey => $evalue){
    $value = explode("=", $evalue);
    $posted_array[trim($value[0])] = $value[1];
}

$sorted_array = array();
 
//remove empty key/value pairs
$filterd_array   = array_filter($posted_array);

// sort key and value 
ksort($filterd_array,SORT_STRING | SORT_FLAG_CASE);


$sorted_string = '';
foreach($filterd_array as $skey => $salue){
    $sorted_string .= $skey.$salue;
}

// base64_encode for signature creation
//$encoded_signature = base64_encode(hash_hmac('sha1', $sorted_string, 'AbCdEfGhIjKlMnOpQrStUvWxYz0123456789AbCd'));
$encoded_signature = base64_encode(hash_hmac('sha1', $sorted_string, 'f6sdnmV1ItrAoOzlR1QZRSGSnGng5HV0KQZtSR4U'));
//The computed signature, with the "Signature" key, is added to the original POST data, to form the following POST data string:
//echo $posted."&Signature=".$encoded_signature;

// This is the data to POST to the form. The KEY of the array is the name of the field. The value is the value posted.
$data_to_post = array();
//$data_to_post['UserID'] =  'sketha';
//$data_to_post['Password']   = 'skyway2925';
$data_to_post['CustID'] = '101246';
$data_to_post['DOS' ]= '10/26/2015';

$data_to_post['ClaimNum	'] = '3179-8507'; 
$data_to_post['ReqType'] = 'CLM';
$data_to_post['Version'] = '1.0';
$data_to_post['Signature'] = $encoded_signature;
// Initialize cURL
$curl = curl_init();
//
//// Set the options
curl_setopt($curl,CURLOPT_URL, $form_url);
//
//// This sets the number of fields to post
curl_setopt($curl,CURLOPT_POST, sizeof($data_to_post));
//echo "<p style='color:blue;' ><strong>Requested Data Sent to Zirmed.</strong></p>";
//echo "<p style='color:blue;' >Parameters passed are: <ol style='color:blue;' ><li>Userid</li><li>Password</li><li>CustId</li><li>DOS</li><li>ClaimNum</li><li>ReqType</li><li>Version</li><li>Signature</li></ol></p>";
//print_r($data_to_post);
//
//// This is the fields to post in the form of an array.
curl_setopt($curl,CURLOPT_POSTFIELDS, $data_to_post);
//
////execute the post
//echo "<br /><br /><br />";
//echo "<p style='color:blue;' ><strong>Response Recieved From Zirmed</strong></p>";
$result = curl_exec($curl);
//
////close the connection
curl_close($curl);

?>

    