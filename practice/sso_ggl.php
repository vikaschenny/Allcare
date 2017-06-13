<?php
 // Copyright (C) 2011 Cassian LUP <cassi.lup@gmail.com>
 //
 // This program is free software; you can redistribute it and/or
 // modify it under the terms of the GNU General Public License
 // as published by the Free Software Foundation; either version 2
 // of the License, or (at your option) any later version.

    //setting the session & other config options
    session_start();

    //don't require standard openemr authorization in globals.php
    $ignoreAuth = 1;

    //SANITIZE ALL ESCAPES
    $fake_register_globals=false;

    //STOP FAKE REGISTER GLOBALS
    $sanitize_all_escapes=true;

    //For redirect if the site on session does not match
    $landingpage = "index.php?site=".$_GET['site'];

    //includes
    require_once('../interface/globals.php');
    

//Get the Client_id, Client Secret key, redirect url and server url 

$sql1= sqlStatement("SELECT title FROM `list_options` where list_id='AllCareSSO' and option_id='practice_google_sso'");
$sql1_exc = sqlFetchArray($sql1);
$res = $sql1_exc[title];
if($res == 'enabled'){
	$sql_scrt = sqlStatement("SELECT title FROM `list_options` where list_id='AllCareSSO' and option_id='practice_google_secret'");
	$scrt_query = sqlFetchArray($sql_scrt);
	$client_secret = $scrt_query[title];

	$sql2 = sqlStatement("SELECT title FROM `list_options` where list_id='AllCareSSO' and option_id = 'practice_google_redirect'");
	$sql2_exc = sqlFetchArray($sql2);
	$redirect = $sql2_exc[title];

	$sql3 = sqlStatement("SELECT title FROM `list_options` where list_id='AllCareSSO' and option_id = 'practice_google_client_id'");
	$sql3_exc = sqlFetchArray($sql3);
	$client_id = $sql3_exc[title];

//	$sql5 = sqlStatement("SELECT title FROM `list_options` where list_id='AllCareSSO' and option_id = 'google_sso_text'");
//	$sql5_exc = sqlFetchArray($sql5);
//	$text  = $sql5_exc[title];
//	if(empty($text)){
//		$text = 'Login With Google';
//	}
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
		require_once 'src/Google_Client.php'; // include the required calss files for google login
		require_once 'src/contrib/Google_PlusService.php';
		require_once 'src/contrib/Google_Oauth2Service.php';
		session_start();
		$client = new Google_Client();
		$client->setApplicationName("Practice Portal"); // Set your applicatio name
		$client->setScopes(array('https://www.googleapis.com/auth/userinfo.email', 'https://www.googleapis.com/auth/plus.me')); // set scope during user login
		$client->setClientId($client_id); // paste the client id which you get from google API Console
		$client->setClientSecret($client_secret); // set the client secret
		$client->setRedirectUri($redirect); // paste the redirect URI where you given in APi Console. You will get the Access Token here during login success

		$plus 		= new Google_PlusService($client);
		$oauth2 	= new Google_Oauth2Service($client); // Call the OAuth2 class for get email address
		if(isset($_GET['code'])) {
                    
			$client->authenticate(); // Authenticate
			$_SESSION['access_token'] = $client->getAccessToken(); // get the access token here
			header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']);
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
		  $email 				= filter_var($user['email'], FILTER_SANITIZE_EMAIL); // get the USER EMAIL ADDRESS using OAuth2
		} else {
			 $authUrl = $client->createAuthUrl();
		}

		if(isset($me)){ 
			$_SESSION['gplusuer'] = $me; // start the session
		}

		if($_SESSION['logout']==1) {
		  unset($_SESSION['access_token']);
		  unset($_SESSION['gplusuer']);
		  session_destroy();
		  header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']); // it will simply destroy the current seesion which you started before
		  #header('Location: https://www.google.com/accounts/Logout?continue=https://appengine.google.com/_ah/logout?continue=https://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']);
		  
		  /*NOTE: for logout and clear all the session direct goole jus un comment the above line an comment the first header function */
		}
		if(isset($authUrl)) {
			echo "<a class='btn btn-social btn-google-plus' href='$authUrl' target='_parent' ><i class='fa fa-google-plus'></i> Sign in with Google</a>";
                      
			//} else {
			//echo "<a class='logout' href='index.php?logout'>Logout</a>";
		}
		if(isset($_SESSION['gplusuer'])){ 
				// print_r($me);   // $me gives the all details about user like name, email
				$email_id = $user['email'];
				
				$query_one = sqlStatement("SELECT id,username FROM users where username ='".$email_id."' AND active = 1 AND ( info IS NULL OR info NOT LIKE '%Inactive%' ) AND authorized = 1");
				
                                    $query_one_exc = sqlFetchArray($query_one);
                                    $id_user = $query_one_exc[id]; 
                                    $name_user = $query_one_exc[username];

                                    if(!empty($id_user) && !empty($name_user)){
                                            callProvider($id_user,$name_user);
                                    }else{ 
                                            $query_two = sqlStatement("SELECT userid FROM tbl_user_custom_attr_1to1 where email='".$email_id."'");
                                            $usercount=mysql_num_rows($query_two); 
                                            $query_two_exc = sqlFetchArray($query_two);
                                            $user_id = $query_two_exc[userid]; 
                                            if($usercount==1){
                                              
                                                if($user_id!=''){
                                                        $query_three = sqlStatement("SELECT username FROM users where id=$user_id AND active = 1 AND ( info IS NULL OR info NOT LIKE '%Inactive%' ) AND authorized = 1");
                                                        $query_three_exc = sqlFetchArray($query_three);
                                                        if(!empty($query_three_exc)){
                                                            $user_name = $query_three_exc['username'];
                                                            callProvider($user_id,$user_name);
                                                        }else {
                                                           $query_three = sqlStatement("SELECT username FROM users where id=$user_id AND active = 1");
                                                            $query_three_exc = sqlFetchArray($query_three);
                                                            checkProvider($user_id,$query_three_exc['username']);
                                                        }
                                                        
                                                }else{
                                                        $username='';
                                                        callProvider($user_id,$username);
                                                }
                                            }elseif($usercount>1) {
                                                checkUserCnt($email_id);
                                                
                                            }elseif($usercount==0){
                                                $username='';
                                                callProvider($user_id,$username);
                                            }
                                            
                                    }	
                                 
                                
		} /// END $_SESSION['gplusuer'])
	}
}
function callProvider($id_user,$username){
    $sql5 = sqlStatement("SELECT title FROM `list_options` where list_id='AllCareSSO' and option_id = 'practice_google_landing'");
	$sql5_exc = sqlFetchArray($sql5);
	$landing  = $sql5_exc[title];
	if(empty($landing)){
		$landing = 'get_provider_info.php';
	}
        if($_SESSION['logout']!=1){
        if($username!=''){
            $pwd = 'UmlzZTEyMyM=';
        ?>	
	<form name='fr' action='<?php echo $landing; ?>?site=<?php echo attr($_SESSION['site_id']); ?>' method='POST' >
	     <input type='hidden' name='uname' value='<?php echo $username; ?>'>
             <input type='hidden' name='pass' value='<?php echo $pwd; ?>'>
             <input type='hidden' name='ggllogin' value='1'>
	</form>
	<script type='text/javascript'>
        document.fr.submit();
	</script>
        <?php
        }else {
         ?>
         <form name='fr' action='<?php echo $landing; ?>?site=<?php echo attr($_SESSION['site_id']); ?>' method='POST' >
              <input type='hidden' name='ggllogin' value='1'>
	  </form>
	<script type='text/javascript'>
        document.fr.submit();
	</script>  
       <?php }
        }
      
}
        
 function checkUserCnt() {
         if($_SESSION['logout']!=1){
        $pwd='';
        $sql5 = sqlStatement("SELECT title FROM `list_options` where list_id='AllCareSSO' and option_id = 'practice_google_landing'");
	$sql5_exc = sqlFetchArray($sql5);
	$landing  = $sql5_exc[title];
	if(empty($landing)){
		$landing = 'get_provider_info.php';
	}
          //$_SESSION['usercnt']=1; ?>
         <form name='fr2' action='<?php echo $landing; ?>?site=<?php echo attr($_SESSION['site_id']); ?>' method='POST' >
              <input type='hidden' name='usercnt' value='<?php echo $email_id; ?>'>
	  </form>
	<script type='text/javascript'>

	document.fr2.submit();
	</script>  
 <?php  }      }
 
 
  function checkProvider($uid,$username) {
         if($_SESSION['logout']!=1){
        $pwd='UmlzZTEyMyM=';
        $sql5 = sqlStatement("SELECT title FROM `list_options` where list_id='AllCareSSO' and option_id = 'practice_google_landing'");
	$sql5_exc = sqlFetchArray($sql5);
	$landing  = $sql5_exc[title];
	if(empty($landing)){
		$landing = 'get_provider_info.php';
	}
          //$_SESSION['usercnt']=1; ?>
         <form name='fr1' action='<?php echo $landing; ?>?site=<?php echo attr($_SESSION['site_id']); ?>' method='POST' >
               <input type='hidden' name='refer_login' value='1'>
               <input type='hidden' name='uname' value='<?php echo $username; ?>'>
               <input type='hidden' name='pass' value='<?php echo $pwd; ?>'>
	  </form>
	<script type='text/javascript'>
           
	document.fr1.submit();
	</script>  
 <?php  }      }
?>	   