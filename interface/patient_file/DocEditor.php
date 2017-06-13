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
include_once($GLOBALS['srcdir'] . "/patient.inc");
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
//Set Document id in the session
if(isset($_REQUEST['document'])) {
	$_SESSION['docid'] = $_REQUEST['document'];
	echo "<!--SESSION['docid']".$_SESSION['docid']."-->";
	}
/*echo "SELECT " .
		"p.fname, p.mname, p.lname, p.pubpid, p.DOB, " .
		"p.street, p.city, p.state, p.postal_code " .
		"FROM patient_data AS p " .
		"WHERE p.pid = ".$pid." LIMIT 1";*/
$patdata = sqlQuery("SELECT " .
		"p.fname, p.mname, p.lname, p.pubpid, p.DOB, " .
		"p.street, p.city, p.state, p.postal_code " .
		"FROM patient_data AS p " .
		"WHERE p.pid = ".$pid." LIMIT 1");

echo '<p>Patient Name: '.$patdata['fname']. ' ' .$patdata['lname'].'</p>';
//echo '<p>Document ID: '.$_REQUEST['document'].'</p>';
//Get Document details
//echo "<!--SELECT doc.id, doc.type, doc.url, doc.mimetype, doc.foreign_id, doc.list_id, doc.status from documents as doc where doc.id = ".$_REQUEST['document']."-->";
$docData = sqlQuery("SELECT doc.id, doc.type, doc.url, doc.mimetype, doc.foreign_id, doc.list_id, doc.status from documents as doc where doc.id = ".$_REQUEST['document']);
//echo '<pre>';print_r($docData);echo '</pre>';

$url_parsed = parse_url($docData['url']);
parse_str($url_parsed['query'], $url_parts);
//echo "<p>File ID: ".$url_parts['id']."</p>";
if($docData['type'] == 'web_url')
	//echo '<embed src="http://'.$_SERVER['SERVER_NAME'].'/DocumentViewer_02/WebContent/web/viewer.html?fileId='.$url_parts['id'].'&as_file=false&patient='.$pid.'" height="800" width="100%"></embed>';
	echo '<embed src="http://emrsb.risecorp.com/DocumentViewer_02/WebContent/web/viewer.html?fileId='.$url_parts['id'].'&as_file=false&patient='.$pid.'&docid='.$_REQUEST['document'].'" height="800" width="100%" id="doceditor"></embed>';
elseif($docData['type'] == 'file_url') {
	echo '<div class="error">Document is saved on Server</div>';
	echo '<iframe src="http://emrsb.risecorp.com/controller.php?document&retrieve&patient_id='.$pid.'&document_id='.$_REQUEST['document'].'&as_file=false" frameborder="0" width="500px" height="500px"></iframe>';
	}
?>