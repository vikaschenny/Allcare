<?php
$ignoreAuth=1;
include_once("../../interface/globals.php");
include_once("../sms_email_reminder/cron_functions.php");
include_once("../sms_email_reminder/smsnotification.php");
require_once("{$GLOBALS['srcdir']}/sqlCentralDB.inc"); // This is to connect central db to insert/update patient data in central db

$type = $_POST['type'];
switch($type):
    case 'user':
        echo welcomeuser(); // send welcome email to users who are grouped and also not grouped
        break;
endswitch;

function welcomeuser(){
    global $sqlconfCentralDB; // This is declared in central db connection
    // Get practice ID
    $practiceId = '';
    $query = sqlStatement("SELECT title FROM list_options WHERE list_id= 'allcareConfig' AND option_id='practiceID'");
    while($row = sqlFetchArray($query)){
        $practiceId = $row['title'];
    }
    // Find if user is allowed to get welcome email
    $globalallowemail = 0;
    $query = sqlStatement("SELECT title FROM list_options WHERE list_id= 'allcareConfig' AND option_id='allowuseremail'");
    while($row = sqlFetchArray($query)){
        $globalallowemail = $row['title'];
    }
    // Find if user is allowed to get welcome sms
    $globalallowsms = 0;
    $query = sqlStatement("SELECT title FROM list_options WHERE list_id= 'allcareConfig' AND option_id='allowusersms'");
    while($row = sqlFetchArray($query)){
        $globalallowsms = $row['title'];
    }
    
    // Get Practice central instance URL
    $praccentralinst = '';
    $query = sqlStatement("SELECT title FROM list_options WHERE list_id= 'allcareConfig' AND option_id='practicecentralinstance'");
    while($row = sqlFetchArray($query)){
        $praccentralinst = $row['title'];
    }
    
    $str = "SELECT id,username FROM users WHERE username!=''";
    $query = sqlStatement($str);
    $usr = array();
    while($row = sqlFetchArray($query)):
        $usr[] = $row['id'];
    endwhile;
    $userids = implode(",",$usr);
    
    //Get practice urls based on practice id from central instance
    
    $str = "SELECT option_id,title FROM list_options WHERE list_id= 'AllCarePractices'";
    $stmt_prac = $sqlconfCentralDB->prepare($str) ;
    $stmt_prac->execute();
    $prac = $stmt_prac->fetchAll(PDO::FETCH_ASSOC);
    $allpractices = array();
    foreach($prac as $p):
        $allpractices[$p['option_id']] = $p['title'];
    endforeach;
    
    
    $str = "SELECT * FROM tbl_allcare_user_notification WHERE notification_id=1";
    $query = sqlStatement($str);
    $email_msg = "";
    while($row = sqlFetchArray($query)):
        $email_msg = $row['message'];
    endwhile;
    
    $str = "SELECT * FROM tbl_allcare_user_notification WHERE notification_id=3";
    $query = sqlStatement($str);
    $sms_msg = "";
    while($row = sqlFetchArray($query)):
        $sms_msg = $row['message'];
    endwhile;
    $practiceids = "PracticeIDs to which you are grouped are:\n";
    
    //Fetch user details from central DB. So that we know which all users are grouped here
    $str = "SELECT userid,fname, mname, lname, username, uemail, cangroup, practiceId FROM allcareobjects WHERE userid IN (".$userids.") AND objecttype='user' AND uemail!=''";
    $stmt_user = $sqlconfCentralDB->prepare($str) ;
    $stmt_user->execute();
    $user = $stmt_user->fetchAll(PDO::FETCH_ASSOC); 
    
    // SMS global config
    $twilio_account = $twilio_token = $twilio_from = "";
    
    $str = "SELECT gl_value FROM globals WHERE gl_name = 'TWILIO_ACCOUNT_SID'";
    $query = sqlStatement($str);
    $row = sqlFetchArray($query);
    $twilio_account = $row['gl_value'];
    
    $str = "SELECT gl_value FROM globals WHERE gl_name = 'TWILIO_AUTHTOKEN'";
    $query = sqlStatement($str);
    $row = sqlFetchArray($query);
    $twilio_token = $row['gl_value'];
    
    $str = "SELECT gl_value FROM globals WHERE gl_name = 'TWILIO_FROM'";
    $query = sqlStatement($str);
    $row = sqlFetchArray($query);
    $twilio_from = $row['gl_value'];
    
    foreach($user as $u):
        $userfullname = $u['fname'] . " " . $u['mname'] . " " . $u['lname'];
        $username = $u['username'];
        
        // Find if user allows to get welcome email via email and sms
        $str = "SELECT allowuseremail,allowusersms FROM tbl_user_custom_attr_1to1 WHERE userid='". $u['userid'] ."'";
        $query = sqlStatement($str);
        $allowuseremail = $allowusersms = '';
        while($row = sqlFetchArray($query)):
            $allowuseremail = $row['allowuseremail'];
            $allowusersms = $row['allowusersms'];
        endwhile;
        
        //Now check other users with same emailid
        $str = "SELECT cangroup, practiceId FROM allcareobjects WHERE objecttype='user' AND uemail='".$u['uemail']."'";
        $stmt_user = $sqlconfCentralDB->prepare($str) ;
        $stmt_user->execute();
        $usergroup = $stmt_user->fetchAll(PDO::FETCH_ASSOC);
        
        $practiceidsgroup = "";
        foreach($usergroup as $usg):
            if($usg['cangroup']=='YES'):
                $practiceidsgroup .= $usg['practiceId']."<br/>";
            endif;
        endforeach;
        $practiceids = "Practice IDs to which you are grouped are:<br />". $practiceidsgroup;
        if($u['cangroup']=='NO' || $u['cangroup']==''):
            $praccentral = $allpractices[$u['practiceId']];
            $practiceids = "Please provide below practice ID:<br />".$u['practiceId'];
        else:
            $praccentral = $praccentralinst;
        endif;
        $landingpage = $praccentral;
        $find_array = array("***USERFULLNAME***","***USERNAME***","***LANDINGPAGE***","***PRACTICESIDS***");
        $replace_array = array($userfullname,$username,$landingpage,$practiceids);
        
        $mobile_find_array = array("***USERFULLNAME***","***USERNAME***","***LANDINGPAGE***");
        $mobile_replace_array = array($userfullname,$username,"Test by Subhan");
        
        $message .= str_replace($find_array,$replace_array,$email_msg). "\n";
        $indMessage = str_replace($find_array,$replace_array,$email_msg);
        $mobileMessage = str_replace($mobile_find_array,$mobile_replace_array,$sms_msg);
        if($u['userid']==27):
            if($globalallowemail == 1 && $allowuseremail == 'YES'):
                cron_SendMail("subhansa@smartmbbs.com","test",$indMessage,"bhavyae@smartmbbs.com");
            endif;
            if($globalallowsms == 1 && $allowusersms == 'YES'):
                // Ringcentral sms api is under process. Stopped since we do not have sandbox password to test the apis
                // Twilio Account sms functionality
                $AccountSid = $twilio_account;
                $AuthToken = $twilio_token;
                $from = $twilio_from;
                $people = array(
                    "+919848560262" => "Subhan Sayyed"
                );
                $body = $mobileMessage;
                sendtsms($AccountSid,$AuthToken,$from,$people,$body);
            endif;
        endif;
    endforeach;
    return $message;
}
?>