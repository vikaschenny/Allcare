<?php
// +-----------------------------------------------------------------------------+ 
// Copyright (C) 2011 Z&H Consultancy Services Private Limited <sam@zhservices.com>
//
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
//
// A copy of the GNU General Public License is included along with this program:
// openemr/interface/login/GnuGPL.html
// For more information write to the Free Software
// Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
// 
// Author:   Eldho Chacko <eldho@zhservices.com>
//           Jacob T Paul <jacob@zhservices.com>
//           Paul Simon   <paul@zhservices.com>
//
// +------------------------------------------------------------------------------+

//SANITIZE ALL ESCAPES
$sanitize_all_escapes=true;
//

//STOP FAKE REGISTER GLOBALS
$fake_register_globals=false;
//
require_once("../../interface/globals.php");
 require_once("$srcdir/sql.inc");
 require_once("$srcdir/formdata.inc.php");
 require_once("$srcdir/classes/postmaster.php");

// Collect portalsite parameter (either off for offsite or on for onsite); only allow off or on
$portalsite = isset($_GET['portalsite']) ? $_GET['portalsite'] : $portalsite = "off";
if ($portalsite != "off" && $portalsite != "on") $portalsite = "off";
$userid=$_REQUEST['userid'];
 $row = sqlQuery("SELECT u.*,pao.portal_username,pao.portal_password,pao.portal_pwd_status FROM users AS u LEFT OUTER JOIN tbl_allcare_agencyportal AS pao ON u.id=pao.uid WHERE u.id=?",array($userid));

function generatePassword($length=6, $strength=1) {
	$consonants = 'bdghjmnpqrstvzacefiklowxy';
	$numbers = '0234561789';
	$specials = '@#$%';
	
 
	$password = '';
	$alt = time() % 2;
	for ($i = 0; $i < $length/3; $i++) {
		if ($alt == 1) {
			$password .= $consonants[(rand() % strlen($consonants))].$numbers[(rand() % strlen($numbers))].$specials[(rand() % strlen($specials))];
			$alt = 0;
		} else {
			$password .= $numbers[(rand() % strlen($numbers))].$specials[(rand() % strlen($specials))].$consonants[(rand() % strlen($consonants))];
			$alt = 1;
		}
	}
	return $password;
}

function validEmail($email){
    if(eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $email)) {
    return true;
    }
    return false;
}

function messageCreate($uname,$pass,$site){
    $protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
    $message = htmlspecialchars( xl("Agency Portal Web Address"),ENT_NOQUOTES) . ":<br>";
    if ($site == "on") {
        $site_url=$protocol.$_SERVER['HTTP_HOST'].'/agencies';
        $message .= htmlspecialchars($site_url,ENT_NOQUOTES) . "<br><br>";
    } // $site == "off"
    
    
        $message .= htmlspecialchars(xl("User Name"),ENT_NOQUOTES) . ": " .
                    htmlspecialchars($uname,ENT_NOQUOTES) . "<br><br>" .
                    htmlspecialchars(xl("Password"),ENT_NOQUOTES) . ": " .
                    htmlspecialchars($pass,ENT_NOQUOTES) . "<br><br>";
    return $message;
}

function emailLogin($userid,$message){
    $userData = sqlQuery("SELECT * FROM `users` WHERE `id`=?", array($patient_id) );
    if (empty($userData['email'])) {
        return false;
    }
    if (!(validEmail($userData['email']))) {
        return false;
    }
   

    $mail = new MyMailer();
    $pt_name=$userData['fname'].' '.$userData['lname'];
    $pt_email=$userData['email'];
    $email_subject=xl('Access Your Patient Portal');
    $email_sender=$GLOBALS['patient_reminder_sender_email'];
    $mail->AddReplyTo($email_sender, $email_sender);
    $mail->SetFrom($email_sender, $email_sender);
    $mail->AddAddress($pt_email, $pt_name);
    $mail->Subject = $email_subject;
    $mail->MsgHTML("<html><body><div class='wrapper'>".$message."</div></body></html>");
    $mail->IsHTML(true);
    $mail->AltBody = $message;
				    
    if ($mail->Send()) {
        return true;
    } else {
        $email_status = $mail->ErrorInfo;
        error_log("EMAIL ERROR: ".$email_status,0);
        return false;
    }
}

function displayLogin($userid,$message,$emailFlag){
    $userData1 = sqlQuery("SELECT * FROM `users` WHERE `id`=?", array($userid) );
    if ($emailFlag) {
        $message = "<br><br>" . 
                   htmlspecialchars(xl("Email was sent to following address"),ENT_NOQUOTES) . ": " .
                   htmlspecialchars($userData1['email'],ENT_NOQUOTES) . "<br><br>" .
                   $message;
    }
    echo "<html><body onload='window.print();'>" . $message . "</body></html>";
}

if(isset($_REQUEST['form_save']) && $_REQUEST['form_save']=='SUBMIT'){
    require_once("$srcdir/authentication/common_operations.php");    

    $clear_pass=$_REQUEST['pwd'];
    
    $res = sqlStatement("SELECT * FROM tbl_allcare_agencyportal WHERE uid=?",array($userid));
    $query_parameters=array($_REQUEST['uname']);
    $salt_clause="";
    if($portalsite=='on')
    {
        // For onsite portal create a blowfish based hash and salt.
        $new_salt = oemr_password_salt();
        $salt_clause = ",portal_salt=? ";
        array_push($query_parameters,oemr_password_hash($clear_pass,$new_salt),$new_salt);
    }
    else
    {
        // For offsite portal still create and SHA1 hashed password
        // When offsite portal is updated to handle blowfish, then both portals can use the same execution path.
        array_push($query_parameters,SHA1($clear_pass));
    }
    array_push($query_parameters,$userid);
    if(sqlNumRows($res)){
    sqlStatement("UPDATE tbl_allcare_agencyportal SET portal_username=?,portal_password=?,portal_pwd_status=0 " . $salt_clause . " WHERE uid=?",$query_parameters);
    }
    else{
    sqlStatement("INSERT INTO tbl_allcare_agencyportal SET portal_username=?,portal_password=?,portal_pwd_status=0" . $salt_clause . " ,uid=?",$query_parameters);
    }
   
    // Create the message
    $message = messageCreate($_REQUEST['uname'],$clear_pass,$portalsite);
    // Email and display/print the message
    if ( emailLogin($userid,$message) ) {
        // email was sent
        displayLogin($userid,$message,true);
    }
    else {
        // email wasn't sent
        displayLogin($userid,$message,false);
    }
    exit;
} ?>

<html>
<head>
<link rel="stylesheet" href="<?php echo $css_header;?>" type="text/css">

<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/jquery-1.6.4.min.js"></script>
<script type="text/javascript">
function transmit(){
                parent.settorest(<?php echo $userid ?>);
                // get a public key to encrypt the password info and send   
                document.getElementById('form_save').value='SUBMIT';
                document.forms[0].submit();
}
</script>
</head>
<body class="body_top">
    <form name="portallogin" action="" method="POST">
    <table align="center" style="margin-top:10px">
        <tr class="text">
            <th colspan="5" align="center"><?php echo htmlspecialchars(xl("Generate Username And Password For")." ".$row['fname'],ENT_QUOTES);?></th>
        </tr>
	<?php
		if($portalsite == 'off'){
	?>
        <tr class="text">
            <td><?php echo htmlspecialchars(xl('Provider Id').':',ENT_QUOTES);?></td>
            <td><span><?php echo htmlspecialchars($GLOBALS['portal_offsite_providerid'],ENT_QUOTES);?></span></td>
        </tr>			
	<?php		
		}
	?>
        <tr class="text">
            <td><?php echo htmlspecialchars(xl('User Name').':',ENT_QUOTES);?></td>
            <td><input type="text" name="uname" value="<?php if($row['portal_username']) echo htmlspecialchars($row['portal_username'],ENT_QUOTES); else echo htmlspecialchars($row['fname'].$row['id'],ENT_QUOTES);?>" size="10" readonly></td>
        </tr>
        <tr class="text">
            <td><?php echo htmlspecialchars(xl('Password').':',ENT_QUOTES);?></td>
            <?php
            $pwd = generatePassword();
            ?>
            <td><input type="text" name="pwd" id="pwd" value="<?php echo htmlspecialchars($pwd,ENT_QUOTES);?>" size="10"/>
            </td>
            <td><a href="#" class="css_button" onclick="top.restoreSession(); javascript:document.location.reload()"><span><?php echo htmlspecialchars(xl('Change'),ENT_QUOTES);?></span></a></td>
        </tr>
        <tr class="text">
            <td><input type="hidden" name="form_save" id="form_save"></td>
            <td colspan="5" align="center">
                <a href="#" class="css_button" onclick="return transmit()"><span><?php echo htmlspecialchars(xl('Save'),ENT_QUOTES);?></span></a>
                <input type="hidden" name="form_cancel" id="form_cancel">
                <a href="#" class="css_button" onclick="top.restoreSession(); parent.$j.fn.fancybox.close();"><span><?php echo htmlspecialchars(xl('Cancel'),ENT_QUOTES);?></span></a>
            </td>
        </tr>
    </table>
    </form>
</body>
