<?php

require_once 'google-api-php-client v3/src/Google/autoload.php';
error_reporting(0);
session_start();

$client = new Google_Client();
$client->setAuthConfigFile('ggl_conf.json');
$client->setRedirectUri('http://' . $_SERVER['HTTP_HOST'] . '/api/DriveSync/oauth2callback_web.php');
$client->setAccessType('offline');
$client->addScope(Google_Service_Drive::DRIVE);


if (! isset($_GET['code'])) {
  $auth_url = $client->createAuthUrl();
  header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
} else {
  $client->authenticate($_GET['code']);
  $_SESSION['access_token'] = $client->getAccessToken();
  $_SESSION['refresh_token'] = $client->getRefreshToken();
   $db = new mysqli('smartmbbsdb.cklyp7uk4jgt.us-west-2.rds.amazonaws.com','allcaretphc','Db0Em4DbDfRrP0d','allcaretphc');
	if ($db->connect_error) {
	  trigger_error('Database connection failed: '  . $db->connect_error, E_USER_ERROR);
	}
	$sql="INSERT INTO drive_users (username,email,accesstoken,refresh_token,status,domain) VALUES ('DriveUser','".$_SESSION['useremail']."','". $client->getAccessToken()."','".$client->getRefreshToken()."','open','web')";
	if($db->query($sql) === false) {
				trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $db->error, E_USER_ERROR);
} else {
  $last_inserted_id = $db->insert_id;
  $affected_rows = $db->affected_rows;
}
 $db->commit();
 ?>
<script>
    
if(window.opener.location.href.indexOf('providers/drive_view/')!=-1) {
   
     window.opener.location.reload(); window.close(); 
      
}else {
   window.location.href='<?php echo "https://" . $_SERVER['HTTP_HOST'] . "/interface/main/allcarereports/drivesync_auth.php?status=1&email=".$_SESSION['useremail']; ?>';
   
//        $redirect_uri = "http://" . $_SERVER['HTTP_HOST'] . "/interface/main/allcarereports/drivesync_auth.php?status=1&email=".$_SESSION['useremail'];
//      header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
     
}
</script>
<?php
}
?>