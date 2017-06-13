<?php
// Initialize cURL
$curl = curl_init();

$login_url = 'https://www.zirmed.com/private/vallogin.aspx';

$data_to_login = array();
$data_to_login['LoginName'] =  'sketha';
$data_to_login['Password']   = 'skyway2925';
$data_to_login['URLRedirect'] = '';

//
//// Set the options
curl_setopt($curl,CURLOPT_URL, $login_url);
//
//// This sets the number of fields to post
curl_setopt($curl,CURLOPT_POST, sizeof($data_to_login));
//
//// This is the fields to post in the form of an array.
curl_setopt($curl,CURLOPT_POSTFIELDS, $data_to_login);
//
////execute the post
//echo "<br /><br /><br />";
//echo "<p style='color:blue;' ><strong>Response Recieved From Zirmed</strong></p>";
$result = curl_exec($curl);
//
////close the connection
curl_close($curl);
?>