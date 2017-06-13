<?php
require_once("../../globals.php");
?>
<script language='JavaScript' src="../../../library/js/jquery-1.4.3.min.js"></script>
<?php 
//Get the Client_id, Client Secret key, redirect url and server url 

//$sql1= sqlStatement("SELECT title FROM `list_options` where list_id='AllCareSSO' and option_id='google_sso'");
//$sql1_exc = sqlFetchArray($sql1);
//$res = $sql1_exc[title];
//if($res == 'enabled'){
if($_REQUEST['status'] !=1){
        
        $sql_scrt = sqlStatement("SELECT title FROM `list_options` where list_id='AllcareDriveSync' and option_id='client_secret'");
	$scrt_query = sqlFetchArray($sql_scrt);
	$client_secret = $scrt_query[title];

	$sql2 = sqlStatement("SELECT title FROM `list_options` where list_id='AllcareDriveSync' and option_id = 'client_redirect'");
	$sql2_exc = sqlFetchArray($sql2);
	$redirect = $sql2_exc[title];

	$sql3 = sqlStatement("SELECT title FROM `list_options` where list_id='AllcareDriveSync' and option_id = 'client_id'");
	$sql3_exc = sqlFetchArray($sql3);
	$client_id = $sql3_exc[title];

	
        
//	$client_secret="eVgSGy_cfbCIDZb_vwFcN167";
//        $redirect ="https://qa2allcare.texashousecalls.com/interface/main/allcarereports/drivesync_config.php";
//        $client_id="898982805961-ffc1qu0l8v3lku8kc3a7jl0jcsusledb.apps.googleusercontent.com";
        $text = 'Login With Google';
	
	if(!empty($redirect) && !empty($client_id) && !empty($client_secret)){
		/*
		 * Copyright 2011 Google Inc.
		 *
		 * Licensed under the Apache License, Version 2.0 (the "License");
		 * you may not use this file except in compliance with the License.
		 * You may obtain a copy of the License at
		 *
		 *     https://www.apache.org/licenses/LICENSE-2.0
		 *
		 * Unless required by applicable law or agreed to in writing, software
		 * distributed under the License is distributed on an "AS IS" BASIS,
		 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
		 * See the License for the specific language governing permissions and
		 * limitations under the License.
		 */
		require_once '../../login/src/Google_Client.php'; // include the required calss files for google login
		require_once '../../login/src/contrib/Google_PlusService.php';
		require_once '../../login/src/contrib/Google_Oauth2Service.php';
		session_start();
		$client = new Google_Client();
		$client->setApplicationName("Open Emr"); // Set your applicatio name
		$client->setScopes(array('https://www.googleapis.com/auth/userinfo.email', 'https://www.googleapis.com/auth/plus.me')); // set scope during user login
		$client->setClientId($client_id); // paste the client id which you get from google API Console
		$client->setClientSecret($client_secret); // set the client secret
		$client->setRedirectUri($redirect); // paste the redirect URI where you given in APi Console. You will get the Access Token here during login success

		$plus 		= new Google_PlusService($client);
		$oauth2 	= new Google_Oauth2Service($client); // Call the OAuth2 class for get email address
		if(isset($_GET['code'])) {
			$client->authenticate(); // Authenticate
			$_SESSION['access_token'] = $client->getAccessToken(); // get the access token here
			header('Location: //' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']);
		}

		if(isset($_SESSION['access_token'])) {
			$client->setAccessToken($_SESSION['access_token']);
		}

		if ($client->getAccessToken()) {
		  $user 		= $oauth2->userinfo->get();
		  $me 			= $plus->people->get('me');
		  $optParams 	= array('maxResults' => 100);
		  $activities 	= $plus->activities->listActivities('me', 'public',$optParams);
		  // The access token may have been updated lazily.
		  $_SESSION['access_token'] 		= $client->getAccessToken();
		  $email= filter_var($user['email'], FILTER_SANITIZE_EMAIL); // get the USER EMAIL ADDRESS using OAuth2
		} else {
			$authUrl = $client->createAuthUrl();
		}

		if(isset($me)){ 
			$_SESSION['gplusuer'] = $me; // start the session
		}

		if(isset($_GET['logout'])) {
		  unset($_SESSION['access_token']);
		  unset($_SESSION['gplusuer']);
		  session_destroy();
		  header('Location://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']); // it will simply destroy the current seesion which you started before
		  #header('Location: https://www.google.com/accounts/Logout?continue=https://appengine.google.com/_ah/logout?continue=https://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']);
		  
		  /*NOTE: for logout and clear all the session direct goole jus un comment the above line an comment the first header function */
		}
		
		if(isset($authUrl)) {
                       $sql=sqlStatement("select * from list_options where list_id='AllcareDriveSync' and option_id='email'");
                       $row=sqlFetchArray($sql);
                       if($row['notes']!=''){ 
                            $user_custom=sqlStatement("select * from tbl_user_custom_attr_1to1 where email='".$row['notes']."'");
                               $cus=sqlFetchArray($user_custom);
                               if(!empty($cus)){
                                   $pfolder=sqlStatement("select * from tbl_drivesync_authentication where email='".$row['notes']."' order by id desc");
                                   $prow=sqlFetchArray($pfolder);

                                    $sql_post12=sqlStatement("select email from drive_users where email='".$row['notes']."' and domain='web' order by id desc");
                                    $post_id1 = sqlFetchArray($sql_post12);

                                    if(empty($post_id1)){
                                       echo "<a class='login' href='$authUrl' target='_parent' >Authenticate</a>";
                                    }
                                    else {
                                       $email_id=$row['notes'];
                                    ?>   
                                    <script type="text/javascript">

                                        window.location.href ="//"+'<?php echo $_SERVER['HTTP_HOST']; ?>'+"/interface/main/allcarereports/drivesync_auth.php?status=1&email=<?php echo $email_id; ?>";

                                     </script>
                              <?php  }
                               }else {
                                   echo "you are not a EMR User";
                               }
                       }else { 
                           echo "Please enter Email id in configuration list";
                       }
                      
                       
                }
		if(isset($_SESSION['gplusuer'])){ 
                    // print_r($me);   // $me gives the all details about user like name, email
                    $email_id = $user['email'];
                    $_session['email_id']=$email_id;
                    $sql=sqlStatement("select * from list_options where list_id='AllcareDriveSync' and option_id='email'");
                    $row=sqlFetchArray($sql);   
                    if($row['notes']==$email_id){
                       $sql=sqlStatement("insert into tbl_drivesync_authentication (date,email,status,user)values(now(),'$email_id','Authenticated','".$_SESSION['authUser']."')");
                       

                         $sql_post=sqlStatement("select email from drive_users where email='".$email_id."' and domain='web' order by id desc");
                         $post_id = sqlFetchArray($sql_post);
                         $auth_id=$post_id['email'];
                         
                     ?>
                     <script type="text/javascript">
                         var auth='<?php echo $auth_id; ?>';
                         if(auth==''){
                              window.location.href = "https://"+'<?php echo $_SERVER['HTTP_HOST']; ?>'+"/api/DriveSync/oauth2/"+'<?php echo $email_id; ?>'+'/emr';
                         }else {
                            window.location.href ="//"+'<?php echo $_SERVER['HTTP_HOST']; ?>'+"/interface/main/allcarereports/drivesync_auth.php?status=1&email=<?php echo $email_id; ?>";
                         }
                        
                     </script>
                   <?php }else { 
                        // $sql=sqlStatement("insert into tbl_drivesync_authentication (date,email,status,user)values(now(),'$email_id','Not Authenticated','".$_SESSION['authUser']."')");
                         unset($_SESSION['access_token']);
                         unset($_SESSION['gplusuer']);
                         
                       ?> <script type="text/javascript">
                             window.location.href = "//"+'<?php echo $_SERVER['HTTP_HOST']; ?>'+"/interface/main/allcarereports/drivesync_config.php?error=1";
                    </script><?php
                        
                    }
				
                                
		} /// END $_SESSION['gplusuer'])
	}
//}
        if($_REQUEST['error']==1){
            echo "<br>"."Please Sign in with Configured Email Id";
        }
}
 
?>	