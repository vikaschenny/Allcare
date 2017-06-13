<?php

require_once 'google-api-php-client v3/src/Google/autoload.php'; 
error_reporting(0);
session_start();

$client = new Google_Client();
$client->setAuthConfigFile('conf.json');
$client->setRedirectUri('http://' . $_SERVER['HTTP_HOST'] . '/api/DriveSync/oauth2callback.php');
$client->setAccessType('offline');
$client->addScope(Google_Service_Drive::DRIVE);

if (! isset($_GET['code'])) {
  $auth_url = $client->createAuthUrl();
  header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
} else {
  $client->authenticate($_GET['code']);
  $_SESSION['access_token'] = $client->getAccessToken();
  $_SESSION['refresh_token'] = $client->getRefreshToken();
    $db = new mysqli("mariadb-130.wc2.dfw3.stabletransit.com","551948_qa3all","M8qXUOLV4","551948_qa3all");
	if ($db->connect_error) {
	  trigger_error('Database connection failed: '  . $db->connect_error, E_USER_ERROR);
	}
//        if($_SESSION['refresh_token'])
            $sql="INSERT INTO drive_users (username,email,accesstoken,refresh_token,status) VALUES ('DriveUser','".$_SESSION['useremail']."','". $client->getAccessToken()."','".$client->getRefreshToken()."','open')";
	if($db->query($sql) === false) {
				trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $db->error, E_USER_ERROR);
} else {
  $last_inserted_id = $db->insert_id;
  $affected_rows = $db->affected_rows;
}
 $db->commit();
 
  $redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . '/api/DriveSync/saveCode';
  header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
}

?>