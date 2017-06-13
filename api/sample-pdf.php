<?php
// Initialize cURL
$curl = curl_init();
$form_url='http://qa2allcare.texashousecalls.com/api/DriveSync/downloadfile_web/bhavyae@smartmbbs.com/0B_4Ba3GYJzSsaWxsR2FoZnBid0k';
curl_setopt($curl,CURLOPT_URL, $form_url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curl, CURLOPT_BINARYTRANSFER, 1);
$result = curl_exec($curl);
header('Content-type: application/pdf');
echo $result;
curl_close($curl);

?>

    