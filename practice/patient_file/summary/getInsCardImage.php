<?php
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
require_once("../../verify_session.php");

$pid = $_POST['pid'];
$insStep = $_POST['name'];
$insid = $_POST['insid'];


$frontImage = $backImage = "";

$query = sqlStatement("SELECT frontimage,frontimageType,backimage,backimageType FROM insurance_data WHERE pid=".$pid." AND provider = ". $insid . " AND type='".$insStep."'");
$row = sqlFetchArray($query);
$frontImage = 'data:image/' . $row['frontimageType'] . ';base64,' . base64_encode($row['frontimage']);
$backImage = 'data:image/' . $row['backimageType'] . ';base64,' . base64_encode($row['backimage']);

if(empty($row['frontimage'])):
    $frontImage = "";
endif;
if(empty($row['backimage'])):
    $backImage = "";
endif;

$imageArr = array('front'=>$frontImage,'back'=>$backImage);
echo json_encode($imageArr);
