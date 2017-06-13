<?php
// Copyright (C) 2007-2011 Rod Roark <rod@sunsetsystems.com>
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.

// Undo magic quotes and do not allow fake register globals.
$sanitize_all_escapes  = true;
$fake_register_globals = false;
include_once("../globals.php");
$pid = null;
if(!isset($_REQUEST['pid']))
{
	if(isset($_SESSION['pid']))
		$pid = $_SESSION['pid'];
}
elseif(isset($_REQUEST['pid']))
	$pid = $_REQUEST['pid'];

if($pid == null) {
	echo '<div class="error">Insufficient information provided to edit the Document.</div>';
	exit();
}
$patdata = sqlQuery("SELECT " .
		"p.fname, p.mname, p.lname, p.pubpid, p.DOB, " .
		"p.street, p.city, p.state, p.postal_code " .
		"FROM patient_data AS p " .
		"WHERE p.pid = '$pid' LIMIT 1");

echo '<p><b>Patient Name: </b>'.$patdata['fname']. ' ' .$patdata['lname'].'</p>';
//echo '<p>Document ID: '.$_REQUEST['document'].'</p>';
//Get Document Details
$docdata = sqlQuery("SELECT d.id, d.type, d.size, d.date, d.url, d.mimetype, d.list_id FROM documents as d where d.id = ".$_REQUEST['document']);
if(isset($docdata)) {
	echo '<!--Document URL: '.$docdata['url'].'<br>-->';
	$path = parse_url($docdata['url'], PHP_URL_PATH);
	$val = explode('/', $path);
	/*echo '<pre>';
	print_r($val);
	echo '</pre>';*/
	if($docdata['type'] == 'web_url'){		
		$fileid = str_replace('&export=download', '', $docdata['url']);
		$pattern = '/id=/';
		preg_match($pattern, $fileid, $matches, PREG_OFFSET_CAPTURE);		
		$pos = intval($matches[0][1]);
		$pos += 3;	//add 'id=' to the position
		echo '<iframe src="https://docs.google.com/a/risecorp.com/file/d/'.substr($fileid, $pos).'/preview" width="640" height="480"></iframe>';			
	}
	else if ($docdata['type'] == 'file_url')
	{
		echo '<iframe type="image/jpeg" src="http://'.$_SERVER['SERVER_NAME'].'/controller.php?document&retrieve&patient_id='.$pid.'&document_id='.$_REQUEST['document'].'&as_file=false" frameborder="0" width="500px" height="500px"></iframe>';
	}
}
else
	echo 'Insufficient information provided';
?>