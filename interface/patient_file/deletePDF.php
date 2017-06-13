<?php
/*
 * Desc: Program to delete the signed file which was created after a signature was added by the User, then uploaded to Drive.
 * 		Delete the signed pdf file on the server after updating it on Google Drive using googleAuth.js file trigged by the 'Save' functionality in the Viewer.
 * Author: Mahesh Ravva
 * Date: 5th August, 2013 
 */

 if(!@file_exists('../globals.php') ) {
  error_log('Could not include globals.php file. Hence the database operation of updating signed file will not work.');
} else {
   include_once('../globals.php');
}
session_start();
$val = null;
$result = null;
echo '<!-- <pre>';
print_r($_SESSION);
echo 'Request Array<br>';
print_r($_REQUEST);
echo '</pre> -->';
$path = $_REQUEST['path'];
$val = explode('/', $path);
//echo $val[(sizeof($val))-1];
if(isset($val)) {
	//echo 'File to be deleted: '.$path;
	if(file_exists($val[(sizeof($val))-1])){
		//echo 'Signed File found';
		if(unlink($val[(sizeof($val))-1]))
			$result .= 'File deleted from the Server';
		else
			$result .= 'Could not delete file from the Server';
	}
	else {
		$result .= 'File not found';
		//return false;
	}
}
//If signed document url is provided, updated the 'documents' table 
//Also log the Signature Event 
if(isset($_REQUEST['signedDocUrl']) && isset($_SESSION['docid'])) {
	//Update 'documents' table
	/*Step 1: Update
	 a) existing 'url' to 'processed_doc_url'
	b) 'status' field value to 'Signed'
	Step 2: Update 'url' value with $_REQUEST['signedDocUrl']
	Step 3: Log the signed file operation	
	*/		
	$statussql = "UPDATE documents set processed_doc_url = url, status='Signed' where id = ".$_SESSION['docid'];
	$urlsql = "UPDATE documents set url = '".$_REQUEST['signedDocUrl']."' where id = ".$_SESSION['docid'];
	error_log($statussql);
	error_log($urlsql);
	$docOpStatusRes = sqlStatement($statussql);
	/*print_r(sqlNumRows($docOpStatusRes));
	if(sqlNumRows($docOpStatusRes) > 0) {
		$result .= 'Document Status details updated in db';
		error_log('Document Status details updated in db');
	}
	else {
		$result .= 'Could not update document status';
		error_log('Could not update document status');
	}*/
	$docOpUrlRes = sqlStatement($urlsql);
	/*print_r(sqlNumRows($docOpUrlRes));
	if(sqlNumRows($docOpUrlRes) > 0) {
		$result .= 'Document URL details updated in db';
		error_log('Document URL details updated in db');
	}
	else {
		$result .= 'Could not finish db operation';
		error_log('Could not update url details');
		}*/
	//Log the signature event
	newEvent("Sign-Document", $_SESSION['authUser'], $_SESSION['authProvider'], 1, "Document Signed and updated(File ID: ".$_REQUEST['signedDocUrl']);
}
else
{
error_log('Could not update database after signature operation. Document id in session: '.$_SESSION['docid']);
}
print_r($result);
return $result;