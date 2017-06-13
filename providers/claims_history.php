<?php
// Initialize cURL
$curl = curl_init();

// Define URL where the form resides
//$form_url = 'https://www.zirmed.com/Services/ServiceHandler.ashx';
//echo $_POST['claimnum']; echo $_POST['dos']; 
$posted = "claimnum=".$_GET['claimnum']."&CustID=".$_GET['custid']."&dos=".$_GET['dos']."&reqtype=clm&version=1.0";
//$posted = "claimnum=5021-11024&CustID=101246&dos=04/06/2016&reqtype=clm&version=1.0";

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
$form_url='https://www.zirmed.com/Services/ServiceHandler.ashx?'.$posted.'&ResponseType=HTML&Signature='.$encoded_signature;
//The computed signature, with the "Signature" key, is added to the original POST data, to form the following POST data string:
// This is the data to POST to the form. The KEY of the array is the name of the field. The value is the value posted.
//$data_to_post = array();
//$data_to_post['claimnum	'] = '5703-9852';
//$data_to_post['CustID'] = '101246';
//$data_to_post['dos' ]= '2016-01-05'; 
//$data_to_post['reqtype'] = 'clm';
//$data_to_post['version'] = '1.0';
//$data_to_post['Signature'] = 'N4pZQevC5OCZIw4ckI4NyN5+l6o=';

//
//// Set the options

curl_setopt($curl, CURLOPT_URL, $form_url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($curl, CURLOPT_TIMEOUT, 10); 
curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $timeout );
curl_setopt($curl, CURLOPT_COOKIEJAR, $cookie);
curl_setopt($curl, CURLOPT_COOKIEFILE, $cookie);
//curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Encoding: none','Content-Type: application/pdf')); 

//header('Content-type: application/html');
//
//// This sets the number of fields to post
//curl_setopt($curl,CURLOPT_POST, sizeof($data_to_post));

//
//// This is the fields to post in the form of an array.
//curl_setopt($curl,CURLOPT_POSTFIELDS, $data_to_post);
//
////execute the post
$result = curl_exec($curl);

//

//$data = $result;
//$dom = new DOMDocument();
//$doc->loadHTML( $result );
////close the connection
curl_close($curl);

echo $result;

?>

    