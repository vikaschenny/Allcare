<?php
ob_start();
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
$sql_scrt = sqlStatement("SELECT title FROM `list_options` where list_id='AllCareSSO' and option_id='provider_secret'");
$scrt_query = sqlFetchArray($sql_scrt);
$client_secret = $scrt_query[title];

$sql2 = sqlStatement("SELECT title FROM `list_options` where list_id='AllCareSSO' and option_id = 'provider_redirect'");
$sql2_exc = sqlFetchArray($sql2);
$redirect = $sql2_exc[title];

$sql3 = sqlStatement("SELECT title FROM `list_options` where list_id='AllCareSSO' and option_id = 'provider_client_id'");
$sql3_exc = sqlFetchArray($sql3);
$client_id = $sql3_exc[title];

$sql4= sqlStatement("SELECT title FROM `list_options` where list_id='AllCareSSO' and option_id = 'provider_server'");
$sql4_exc = sqlFetchArray($sql4);
$server = $sql4_exc[title];

$sql5= sqlStatement("SELECT title FROM `list_options` where list_id='AllCareSSO' and option_id = 'provider_sender_email'");
$sql5_exc = sqlFetchArray($sql5);
$sender_email = $sql5_exc[title];

//code starts for get access token
$curl_post_data = array(
'grant_type' => 'authorization_code',
'code' => $_GET['code'],
'redirect_uri' => $redirect
);
$service_url = $server.'/oauth/token';

$curl = curl_init($service_url);
curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
curl_setopt($curl, CURLOPT_USERPWD, "$client_id:$client_secret"); //Your credentials goes here
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $curl_post_data);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // If the url has https and you don't want to verify source certificate

$curl_response = curl_exec($curl);
$response = json_decode($curl_response);
curl_close($curl);
$data = (array) $response;
$token = $data[access_token];

//code ends for access token
//using access token get the user information like id,name, email

$service_url = $server.'/oauth/me?access_token='.$token;

$curl = curl_init($service_url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // If the url has https and you don't want to verify source certificate

$curl_response = curl_exec($curl);

$response = json_decode($curl_response);
curl_close($curl);

$userdetails = (array) $response; 
//echo "<pre>"; print_r($userdetails); echo "</pre>";  
$userName = $userdetails[user_login];
$name = explode(" ",$userdetails[display_name]);
if($name[0]!='' && $name[1]!=''){
    $fname = $name[0];
    $lname = $name[1];
}else if($name[0]!='' && $name[1]=='') {
    $fname = $name[0];
    $lname = $name[0];
}else if($name[1]!='' && $name[0]=='') {
    $fname = $name[1];
    $lname = $name[1]; 
}



if($userdetails[email]!=''){
    $email = $userdetails[email] ;
}else{
     $email = $userdetails[user_email] ;
}



$rl=explode(",",$userdetails[rolename]);
$role=$rl[0];
$pwd   = $userdetails[user_login].$userdetails[ID];

require_once("../library/authentication/common_operations.php"); 

//check user exist or not in address book users table

$sql = sqlStatement("SELECT COUNT(*) as num_rows FROM `users` where username='".$userName."' AND active = 1 AND ( info IS NULL OR info NOT LIKE '%Inactive%' ) AND authorized = 1");
$row_query = sqlFetchArray($sql);

if($row_query[num_rows]==0){  //if user not exist in users table then insert in users table 	
       
        $sql_rl = sqlStatement("SELECT emr_role,provider FROM `tbl_allcare_roles_mapping` where wp_role='".$role."' and active='1' and provider='1'");
        $sql2_excrl = sqlFetchArray($sql_rl);
        if(!empty($sql2_excrl)){
           
            $sql_us = sqlStatement("SELECT COUNT(*) as num_rows FROM `users` where username='".$userName."'");
            $row_us = sqlFetchArray($sql_us);
            if($row_us[num_rows]==0){
                $checkemail=sqlStatement("select * from tbl_user_custom_attr_1to1 where email='".$email."'");
                $row_email1= sqlFetchArray($checkemail);
                if(empty($row_email1)){
                    $insert_query = sqlStatement("INSERT into users(username,password,fname,lname,email,facility,facility_id,see_auth,active,cal_ui,taxonomy) values('".$userName."','NoLongerUsed','".$fname."','".$lname."','".$email."','Texas Physician House Call PLLC',3,1,1,3,'207Q00000X')");
	
                    $sql = sqlStatement("SELECT id,username FROM `users` where fname='".$fname."' and lname='".$lname."' and email='".$email."'");
                    $row_id = sqlFetchArray($sql);
                    $id = $row_id[id];
                    $user = $row_id[username];
                    $insert_query = sqlStatement("INSERT into tbl_user_custom_attr_1to1(created_date,userid,email) values(NOW(),$id,'".$email."')");
                    //sending email
                    $to = $email;
                    $subject = "Provider Portal Credenials";

                    $message = "
                    <p>Username:$user</p>
                    <p>Password:$pwd</p>
                    ";

                    // Always set content-type when sending HTML email
                    $headers = "MIME-Version: 1.0" . "\r\n";
                    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

                    // More headers
                    $headers .= 'From: <'.$sender_email.'>' . "\r\n";
                   // $headers .= 'Cc: hemasrit@riseglobal.net' . "\r\n";

                    mail($to,$subject,$message,$headers);      
                    insertProviderPortal($id,$user,$pwd,$fname,$lname,$role);
                    callProviderPortal($user,$pwd,$role,$email);
                }else {
                    $_SESSION['email_exists']=1;
                    $pwd='';
                    callProviderPortal($user,$pwd,$role,$email);
                }
                
            }else if($row_us[num_rows]==1){
                $sql = sqlStatement("SELECT id,username FROM `users` where username='".$userName."'");
                $row_id = sqlFetchArray($sql);
                $id = $row_id[id];
                
                $user=$userName;
                updateProviderPortal($id,$user,$fname,$lname,$role);
		$pwd= 'UmlzZTEyMyM=';
                
		callProviderPortal($user,$pwd,$role,$email);
            }
            	
        }else {
              $sql_rl1 = sqlStatement("SELECT emr_role,provider FROM `tbl_allcare_roles_mapping` where wp_role='".$role."' and active='1'");
                 $sql2_excrl1 = sqlFetchArray($sql_rl1);
                 if(empty($sql2_excrl1)){ 
                     echo  $_SESSION['allow']=1; 
                 }else {
                     $sql_pro = sqlStatement("SELECT emr_role,provider FROM `tbl_allcare_roles_mapping` where wp_role='".$role."' and provider='1'");
                     $sql2_excpro = sqlFetchArray($sql_pro);
                     if(empty($sql2_excpro)){
                          $_SESSION['provideracess']=1;
                     }
                 }
             $pwd='';
             callProviderPortal($user,$pwd,$role,$email);	
        }
	
}
if($row_query[num_rows]==1){ //if username is exist in users table then will need to check in openEmr related tables. He/she existed or not
       
        $sql_23 = sqlStatement("SELECT u.id FROM `users` u INNER JOIN tbl_user_custom_attr_1to1 us ON u.id=us.userid where username='".$userName."' AND us.email='".$email."' AND active = 1 AND ( info IS NULL OR info NOT LIKE '%Inactive%' ) AND authorized = 1");
        $sql2_exc23 = sqlFetchArray($sql_23);
        if(!empty($sql2_exc23)){
            $sql_1 = sqlStatement("SELECT id FROM `users_secure` where username='".$userName."' and password !='' and salt !=''");
            $row_1 = sqlFetchArray($sql_1);
            $id = $row_1[id];
            $user = $userName; 


            if($id==''){	
                    $sql_2 = sqlStatement("SELECT id FROM `users` where username='".$userName."'");
                    $sql2_exc = sqlFetchArray($sql_2);
                    $uid = $sql2_exc[id];
                     $checkemail=sqlStatement("select * from tbl_user_custom_attr_1to1 where email='".$email."' and userid not in($uid)");
                    $row_em = sqlFetchArray($checkemail);
                    if(empty($row_em)){
                        $update_query = sqlStatement("UPDATE users SET facility = 'Texas Physician House Call PLLC', facility_id = 3, see_auth = 1, active = 1, cal_ui = 3, taxonomy = '207Q00000X' where id = $uid");		
                        $id = $uid;
                        $user = $userName;

                        $sql_email = sqlStatement("SELECT * FROM `tbl_user_custom_attr_1to1` where userid=$id ");
                        $row_email = sqlFetchArray($sql_email);
                        if(!empty($row_email)){
                            $update_query = sqlStatement("UPDATE tbl_user_custom_attr_1to1 SET updated_date=NOW() , email = '".$email."' where userid = $id");	
                        }else {
                            $insert_query = sqlStatement("INSERT into tbl_user_custom_attr_1to1(created_date,userid,email) values(NOW(),$id,'".$email."')");
                        }
                        //insertProviderPortal($id,$user,$pwd,$fname,$lname,$role);
                        $plain_code=$pwd;
                        $new_salt=oemr_password_salt();
                        $new_hash=oemr_password_hash($plain_code,$new_salt);
                        $ins1 = sqlStatement("INSERT into users_secure(id,username,password,salt) values($id,'".$user."','".$new_hash."','".$new_salt."')");
                        updateProviderPortal($id,$user,$fname,$lname,$role);
                        callProviderPortal($user,$pwd,$role,$email);	
                    }else {
                        $_SESSION['email_exists']=1;
                        $pwd='';
                        callProviderPortal($user,$pwd,$role,$email);
                    }

            }else{
                    $checkemail=sqlStatement("select * from tbl_user_custom_attr_1to1 where email='".$email."' and userid not in($id)");
                    $row_em = sqlFetchArray($checkemail);
                    if(empty($row_em)){
                        $sql_email = sqlStatement("SELECT * FROM `tbl_user_custom_attr_1to1` where userid=$id ");
                        $row_email = sqlFetchArray($sql_email);
                        if(!empty($row_email)){
                            $update_query = sqlStatement("UPDATE tbl_user_custom_attr_1to1 SET updated_date=NOW() , email = '".$email."' where userid = $id");	
                        }else {
                            $insert_query = sqlStatement("INSERT into tbl_user_custom_attr_1to1(created_date,userid,email) values(NOW(),$id,'".$email."')");
                        }
                        updateProviderPortal($id,$user,$fname,$lname,$role);
                        $pwd= 'UmlzZTEyMyM=';

                        callProviderPortal($user,$pwd,$role,$email);
                    }else {
                         $_SESSION['email_exists']=1;
                         $pwd='';
                        callProviderPortal($user,$pwd,$role,$email);
                    }

            }
            
        }else {
                       $_SESSION['unmatched']=1;
                        $pwd='';
                        callProviderPortal($user,$pwd,$role,$email);
        }
	
}
function insertProviderPortal($id,$user,$pwd,$fname,$lname,$role){
	$plain_code=$pwd;
	$new_salt=oemr_password_salt();
	$new_hash=oemr_password_hash($plain_code,$new_salt);
	$ins1 = sqlStatement("INSERT into users_secure(id,username,password,salt) values($id,'".$user."','".$new_hash."','".$new_salt."')");
	$ins2 = sqlStatement("INSERT into groups(name,user) values('Default','".$user."')");
	$sql_3 = sqlStatement("SELECT id FROM gacl_aro_seq");
	$sql3_exc = sqlFetchArray($sql_3);
	$mid = $sql3_exc[id]+1;
        $update = sqlStatement("UPDATE gacl_aro_seq set id = $mid");
	$ins3 = sqlStatement("INSERT into gacl_aro(id,section_value,value,order_value,name) values($mid,'users','".$user."',10,'".$fname." ".$lname."')");
        
	if($role!=''){
            
//            foreach($role as $key => $value){
//                $rolename=$key;
//             }
            $sql_rl = sqlStatement("SELECT emr_role,provider FROM `tbl_allcare_roles_mapping` where wp_role='".$role."' and active='1' and provider='1'");
            $rowcount=mysql_num_rows($sql_rl);
            if($rowcount!='0'){
            $sql2_excrl = sqlFetchArray($sql_rl);
             $update_query = sqlStatement("UPDATE users SET authorized = '".$sql2_excrl['provider']."',calendar=1  where id = $id");	
            $mystring = $sql2_excrl['emr_role'];
            $findme   = ',';
            $pos = strpos($mystring, $findme);
            if($pos==''){
                 $sql_rl2 = sqlStatement("SELECT id FROM `gacl_aro_groups` where name='".$mystring."' ");
                 $sql2_excrl2 = sqlFetchArray($sql_rl2);
                 $ins4 = sqlStatement("INSERT into gacl_groups_aro_map(group_id,aro_id) values('".$sql2_excrl2['id']."',$mid)");
	     }else if($pos!=''){
                $rval=explode(",",$mystring);
                foreach($rval as $val){
                 $sql_rl2 = sqlStatement("SELECT id FROM `gacl_aro_groups` where name='".$val."' ");
                 $sql2_excrl2 = sqlFetchArray($sql_rl2);
                 $ins4 = sqlStatement("INSERT into gacl_groups_aro_map(group_id,aro_id) values('".$sql2_excrl2['id']."',$mid)");
	        }
            }
        } else {
                $sql_rl = sqlStatement("SELECT emr_role,provider FROM `tbl_allcare_roles_mapping` where wp_role='".$role."' and active='1'");
                 $sql2_excrl = sqlFetchArray($sql_rl);
                 if(empty($sql2_excrl)){
                      $_SESSION['allow']=1;
                 }else {
                     $sql_pro = sqlStatement("SELECT emr_role,provider FROM `tbl_allcare_roles_mapping` where wp_role='".$role."' and provider='1'");
                     $sql2_excpro = sqlFetchArray($sql_pro);
                     if(empty($sql2_excpro)){
                          $_SESSION['provideracess']=1;
                     }
                 }
        }
       
     }else {
         $_SESSION['allow']=1;
     }   
}
function updateProviderPortal($id,$user,$fname,$lname,$role){
	
	if($role!=''){
//            foreach($role as $key => $value){
//                $rolename=$key;
//            }
            
            $sql_rl = sqlStatement("SELECT emr_role,provider FROM `tbl_allcare_roles_mapping` where wp_role='".$role."' and active='1' and provider=1");
            $rowcount=mysql_num_rows($sql_rl);
            if($rowcount!='0'){
                $sql2_excrl = sqlFetchArray($sql_rl);
                $update_query = sqlStatement("UPDATE users SET authorized = '".$sql2_excrl['provider']."',calendar=1  where id = $id");	
                $mystring = $sql2_excrl['emr_role'];
                $findme   = ',';
                $pos = strpos($mystring, $findme);
                if($pos==''){
                    $sql_gacl=sqlStatement("select id from gacl_aro where value='".$user."'");
                    $aroid = sqlFetchArray($sql_gacl);
                    $sql_aro=sqlStatement("select * from gacl_groups_aro_map where aro_id='".$aroid['id']."'");
                    $rowcount=mysql_num_rows($sql_aro);
                    if($rowcount!=0){
                       while($acl = sqlFetchArray($sql_aro)){
                           $aclid[]=$acl['group_id'];
                       }
                    }
                     $sql_rl2 = sqlStatement("SELECT id FROM `gacl_aro_groups` where name='".$mystring."' ");
                     $sql2_excrl2 = sqlFetchArray($sql_rl2);
                     if(!in_array($sql2_excrl2['id'],$aclid)){
                         $ins4 = sqlStatement("INSERT into gacl_groups_aro_map(group_id,aro_id) values('".$sql2_excrl2['id']."','".$aroid['id']."')");
                     }
                     $result = array_diff( $aclid,$sql2_excrl2);
                     foreach($result as $grpid){
                          $del4 = sqlStatement("Delete from gacl_groups_aro_map  where group_id=$grpid and aro_id='".$aroid['id']."'");
                     }
                   
                 }else if($pos!=''){
                     $sql_gacl=sqlStatement("select id from gacl_aro where value='".$user."'");
                    $aroid = sqlFetchArray($sql_gacl);
                    $sql_aro=sqlStatement("select * from gacl_groups_aro_map where aro_id='".$aroid['id']."'");
                    $rowcount=mysql_num_rows($sql_aro);
                    if($rowcount!=0){
                       while($acl = sqlFetchArray($sql_aro)){
                           $aclid[]=$acl['group_id'];
                       }
                    }
                    $rval=explode(",",$mystring);
                    // print_r($rval);
                    foreach($rval as $val){
                     $sql_rl2 = sqlStatement("SELECT id FROM `gacl_aro_groups` where name='".$val."' ");
                     $sql2_excrl2 = sqlFetchArray($sql_rl2);
                     $extid[]=$sql2_excrl2['id'];
                      if(!in_array($sql2_excrl2['id'],$aclid)){
                           $ins4 = sqlStatement("INSERT into gacl_groups_aro_map(group_id,aro_id) values('".$sql2_excrl2['id']."','".$aroid['id']."')");
                      }
                    
                    }
                     $result = array_diff( $aclid,$extid);
                     foreach($result as $grpid){
                          $del4 = sqlStatement("Delete from gacl_groups_aro_map  where group_id=$grpid and aro_id='".$aroid['id']."'");
                     }
                }
            }else {
                 $sql_rl1 = sqlStatement("SELECT emr_role,provider FROM `tbl_allcare_roles_mapping` where wp_role='".$role."' and active='1'");
                 $sql2_excrl1 = sqlFetchArray($sql_rl1);
                 if(empty($sql2_excrl1)){ 
                     echo  $_SESSION['allow']=1; 
                 }else {
                     $sql_pro = sqlStatement("SELECT emr_role,provider FROM `tbl_allcare_roles_mapping` where wp_role='".$role."' and provider='1'");
                     $sql2_excpro = sqlFetchArray($sql_pro);
                     if(empty($sql2_excpro)){
                          $_SESSION['provideracess']=1;
                     }
                 }
                 
            }

            
        }else {
            $_SESSION['allow']=1;
        }
       
}
function callProviderPortal($user,$pwd,$role,$email){ 
	$sql5 = sqlStatement("SELECT title FROM `list_options` where list_id='AllCareSSO' and option_id = 'provider_landing'");
	$sql5_exc = sqlFetchArray($sql5);
	$landing  = $sql5_exc[title];
	if(empty($landing)){
		$landing = 'get_provider_info.php';
	}
       
        if($_SESSION['allow']==1 && $_SESSION['allow']!=''){
         
            $pwd='';
         
        }
        if($_SESSION['provideracess']==1 && $_SESSION['provideracess']!=''){
           // echo "<script> document.getElementById('access').value = '0';</script>";
            $pwd='';
         
        }
         if($_SESSION['email_exists']==1 && $_SESSION['email_exists']!=''){
            
          /// $_SESSION['email_id']=$email;
           $pwd='';
         
        }
        if($_SESSION['unmatched']==1 && $_SESSION['unmatched']!=''){
            
           $_SESSION['unmatched_em']=$email;
           $pwd='';
         
        }
?>	
	<form name='fr' action='<?php echo $landing; ?>?site=<?php echo attr($_SESSION['site_id']); ?>' method='POST' >
	     <input type='hidden' name='uname' value='<?php echo $user; ?>'>
             <input type='hidden' name='pass' value='<?php echo $pwd; ?>'>
             <?php  if($_SESSION['allow']==1 && $_SESSION['allow']!=''){ ?>
     
                 <input type='hidden' id="role" name='role' value='<?php echo $role;?>'>
             <?php }     ?>
             <?php  if($_SESSION['provideracess']==1 && $_SESSION['provideracess']!=''){ ?>
     
                 <input type='hidden' id="access" name='access' value='1'>
             <?php }     ?>
             <?php  if($_SESSION['email_exists']==1 && $_SESSION['email_exists']!=''){ ?>
     
                 <input type='hidden' id="email_id" name='email_id' value='<?php echo $email; ?>'>
             <?php }     ?>
                 <?php  if($_SESSION['unmatched']==1 && $_SESSION['unmatched']!=''){ ?>
     
                 <input type='hidden' id="unmatched_em" name='unmatched_em' value='<?php echo $email; ?>'>
             <?php }     ?>
            
        </form>

	<script type='text/javascript'>

	document.fr.submit();
	</script>
<?
	}
?>
 

