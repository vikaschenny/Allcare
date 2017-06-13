<?php

$curl = curl_init();
$form_url=$_REQUEST['url'];
curl_setopt($curl,CURLOPT_URL, $form_url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curl, CURLOPT_BINARYTRANSFER, 1);
$result = curl_exec($curl);
header('Content-type: '.$_REQUEST['file-type']);
echo $result;
curl_close($curl);
?> 