<?php

$posted = "CustID=00000&TS=01/01/2012 12:01:01 AM&TransID=012345&Amount=20.00&CardNumber=&ExpirationDate=12/12"; //$_REQUEST['parameters'];
//$posted="CustID=101246&DOS=10/26/2015&ClaimNum=3179-8507&ReqType=CLM&Version=1.0";
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
echo $sorted_string;echo "<br>";
//echo hash_hmac('sha1', $sorted_string, 'f6sdnmV1ItrAoOzlR1QZRSGSnGng5HV0KQZtSR4U',true);
// base64_encode for signature creation
//echo $encoded_signature = base64_encode(hash_hmac('sha1', 'Amount20.00CustID00000ExpirationDate12/12TransID012345TS01/01/2012 12:01:01 AM', 'AbCdEfGhIjKlMnOpQrStUvWxYz0123456789AbCd',true));
echo $encoded_signature = base64_encode(hash_hmac('sha1', $sorted_string, 'AbCdEfGhIjKlMnOpQrStUvWxYz0123456789AbCd',true));
//The computed signature, with the "Signature" key, is added to the original POST data, to form the following POST data string:
//echo $posted."&Signature=".$encoded_signature;
//echo "<br>";
//echo base64_decode($encoded_signature);
//echo $cipher = hash_hmac('sha1', $encoded_signature, 'f6sdnmV1ItrAoOzlR1QZRSGSnGng5HV0KQZtSR4U',true);

   
?>