<?php
// Initialize cURL
$curl = curl_init();

// Define URL where the form resides
//$form_url = 'https://www.zirmed.com/Services/ServiceHandler.ashx';
//echo $_POST['claimnum']; echo $_POST['dos']; 
//$posted = "claimnum=".$_POST['claimnum']."&CustID=101246&dos=".$_POST['dos']."&reqtype=clm&version=1.0";
$posted = "claimnum=6027-10026&CustID=101246&dos=2016-01-11&reqtype=CLM&version=1.0";

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
$encoded_signature = base64_encode(hash_hmac('sha1', $sorted_string, 'f6sdnmV1ItrAoOzlR1QZRSGSnGng5HV0KQZtSR4U',true));
//$form_url='https://claimsapi.zirmed.com/1.0/Reports/POTFForm?'.$posted.'&ResponseType=HTML&Signature='.$encoded_signature;
$form_url='https://claimsapi.zirmed.com/1.0/Reports/POTFForm';
//The computed signature, with the "Signature" key, is added to the original POST data, to form the following POST data string:
// This is the data to POST to the form. The KEY of the array is the name of the field. The value is the value posted.
$data_to_post = array();
$data_to_post['claimnum	'] = '6027-10026';
$data_to_post['CustID'] = '101246';
$data_to_post['dos' ]= '2016-01-11'; 
$data_to_post['reqtype'] = 'CLM';
$data_to_post['version'] = '1.0';
//$data_to_post['Signature'] = 'N4pZQevC5OCZIw4ckI4NyN5+l6o=';
$data_to_post['Signature'] = $encoded_signature;

//
//// Set the options
curl_setopt($curl,CURLOPT_URL, $form_url);
//
//// This sets the number of fields to post
curl_setopt($curl,CURLOPT_POST, sizeof($data_to_post));

//
//// This is the fields to post in the form of an array.
curl_setopt($curl,CURLOPT_POSTFIELDS, $data_to_post);
//
////execute the post
echo $result = curl_exec($curl);

////close the connection
curl_close($curl);

?>

    