<?php

////////////////////////////////////////////////////////////////////
// Package:	cron_email_notification
// Purpose:	to be run by cron every hour, look for appointments
//		in the pre-notification period and send an email reminder
//
// Created by:
// Updated by:	Larry Lart on 10/03/2008
////////////////////////////////////////////////////////////////////

// larry :: hack add for command line version
$_SERVER['REQUEST_URI']=$_SERVER['PHP_SELF'];
$_SERVER['SERVER_NAME']='localhost';
$backpic = "";
//print_r($GLOBALS);
// email notification
$ignoreAuth=1;
include_once("/mnt/stor10-wc2-dfw1/551939/551948/allcare.dfwprimary.com/web/content/interface/globals.php");
include_once("cron_functions.php");

$TYPE = "Email";
$CRON_TIME = 5;

// set cron time (time to event ?) - todo extra tests
$vectNotificationSettings = cron_GetNotificationSettings( );

$CRON_TIME = $vectNotificationSettings['Send_Email_Before_Hours'];
//print_r($vectNotificationSettings);
$check_date = date("Y-m-d", mktime(date("h")+$EMAIL_NOTIFICATION_HOUR, 0, 0, date("m"), date("d"), date("Y")));


// get data from automatic_notification table
$db_email_msg = cron_getNotificationData($TYPE);
//my_print_r($db_email_msg);

// get patient data for send alert
$db_patient = cron_getAlertpatientData($TYPE); //echo "<br>".print_r($db_patient)."<br>";
echo "<br>Total ".count($db_patient)." Records Found\n<br>";
for($p=0;$p<count($db_patient);$p++)
{  //echo "<br>p=$p<br>";
	$prow =$db_patient[$p];
	
	//echo '<br/>eid==='.$prow['pc_eid'];
	
	//my_print_r($prow);
	/*
	if($prow['pc_eventDate'] < $check_date)
	{
		$app_date = date("Y-m-d")." ".$prow['pc_startTime'];
	}else{
		$app_date = $prow['pc_eventDate']." ".$prow['pc_startTime'];
	}
	*/
	echo '<br/>app_date==='.$app_date = $prow['pc_eventDate']." ".$prow['pc_startTime'];
	$app_time = strtotime($app_date);
	//echo "Subhan App Time ".$app_time;
	$app_time_hour = round($app_time/3600);
	$curr_total_hour = round(time()/3600);
	
	//echo '<br/>$EMAIL_NOTIFICATION_HOUR=='.$EMAIL_NOTIFICATION_HOUR;
	
	$remaining_app_hour = round($app_time_hour - $curr_total_hour);
	$remain_hour = round($remaining_app_hour - $EMAIL_NOTIFICATION_HOUR);
	
	/****************************/
	$custom_notification_hour=round($remaining_app_hour - $email_notification1_hours);
	/****************************/
	
	//echo $strMsg = "\n========================".$TYPE." || ".date("Y-m-d H:i:s")."=========================";
	//echo $strMsg .= "\nSEND NOTIFICATION BEFORE:".$EMAIL_NOTIFICATION_HOUR." || CRONJOB RUN EVERY:".$CRON_TIME." || APPDATETIME:".$app_date." || REMAINING APP HOUR:".($remaining_app_hour)." || SEND ALERT AFTER:".($remain_hour);
	
	
	
	echo '<br/><br/>email_notification1_hours=='.$email_notification1_hours;
	echo '<br/><br/>email_notification2_hours=='.$email_notification2_hours;
	echo '<br/><br/>email_notification3_hours=='.$email_notification3_hours;
	
	
	echo '<br/>app_time_hour=='.$app_time_hour = round($app_time/3600);
	echo '<br/>curr_total_hour=='.$curr_total_hour = round(time()/3600);
	//echo "Subhan App Test ". $app_time_hour ."-". $curr_total_hour;
	echo '<br>remaining_app_hour==='.$remaining_app_hour = round($app_time_hour - $curr_total_hour);
	
	$remain_email_hour1=0;$remain_email_hour2=0;$remain_email_hour3=0;
        //echo "Subhan Test - ".$remaining_app_hour ."-". $email_notification1_hours;
	if($email_notification1_hours!='' && is_numeric($email_notification1_hours)
		&& $email_notification1_hours>0)
	{
	echo '<br>remain_hour1==='.$remain_email_hour1 = round($remaining_app_hour - $email_notification1_hours);
	}
	if($email_notification2_hours!='' && is_numeric($email_notification2_hours)
		&& $email_notification2_hours>0)
	{
	echo '<br>remain_hour2==='.$remain_email_hour2 = round($remaining_app_hour - $email_notification2_hours);
	}
	if($email_notification3_hours!='' && is_numeric($email_notification3_hours)
		&& $email_notification3_hours>0)
	{
	echo '<br>remain_hour3==='.$remain_email_hour3 = round($remaining_app_hour - $email_notification3_hours);
	}
	
	echo '<br/>';
	
	
	//echo '<br/><br/>'.'SELECT COUNT(*) as notification_sent FROM openemr_postcalendar_events WHERE pc_eid='.$prow['pc_eid'].' AND (pc_apptstatus!="e*" || pc_apptstatus!="e**" || pc_apptstatus!="e***")';
	
	$check_email_notification=mysql_query('SELECT COUNT(*) as notification_sent FROM openemr_postcalendar_events WHERE pc_eid='.$prow['pc_eid'].' AND (pc_apptstatus="e*" || pc_apptstatus="e**" || pc_apptstatus="e***")');
	$res_check_notification=mysql_fetch_array($check_email_notification);
	echo '<br/>email_count==='.$email_count=$res_check_notification['notification_sent'];
	
	// email notification1 hours
	if($remain_email_hour1 > 0 && $email_count ==0)
	{
	// insert entry in notification_log table
		cron_InsertNotificationLogEntry($TYPE,$prow,$db_email_msg);

		//set message 
		$db_email_msg['message'] = cron_setmessage( $prow, $db_email_msg );
		//echo "prow email==".$prow['email']; 
		// send mail to patinet
		cron_SendMail( $prow['email'], $db_email_msg['email_subject'], 
				$db_email_msg['message'], $db_email_msg['email_sender'] );
		
		//update entry >> pc_sendalertemail='Yes'
		//cron_updateentry($TYPE,$prow['pid'],$prow['pc_eid']);
		
		echo $strMsg .= " || ALERT SENT SUCCESSFULLY TO ".$prow['email'];
		echo $strMsg .= "\n".$patient_info."\n".$smsgateway_info."\n".$data_info."\n".$db_email_msg['message'];
	
		echo '<br/>'.$query="UPDATE openemr_postcalendar_events SET pc_apptstatus='e*' WHERE pc_eid=".$prow['pc_eid'];
		sqlStatement($query);
                
                $remain_email_hour2=0;
                $remain_email_hour3=0;
	}
	
	// email notification2 hours
	if($remain_email_hour2 > 0 && $email_count ==0)
	{
	// insert entry in notification_log table
		cron_InsertNotificationLogEntry($TYPE,$prow,$db_email_msg);

		//set message 
		$db_email_msg['message'] = cron_setmessage( $prow, $db_email_msg );
		//echo "prow email==".$prow['email']; 
		// send mail to patinet
		cron_SendMail( $prow['email'], $db_email_msg['email_subject'], 
				$db_email_msg['message'], $db_email_msg['email_sender'] );
		
		//update entry >> pc_sendalertemail='Yes'
		//cron_updateentry($TYPE,$prow['pid'],$prow['pc_eid']);
		
		echo $strMsg .= " || ALERT SENT SUCCESSFULLY TO ".$prow['email'];
		echo $strMsg .= "\n".$patient_info."\n".$smsgateway_info."\n".$data_info."\n".$db_email_msg['message'];
	
		echo '<br/>'.$query="UPDATE openemr_postcalendar_events SET pc_apptstatus='e**' WHERE pc_eid=".$prow['pc_eid'];
		sqlStatement($query);
                
                $remain_email_hour3=0;
	}
	
	// email notification3 hours
	if($remain_email_hour3 > 0 && $email_count ==0)
	{
	// insert entry in notification_log table
		cron_InsertNotificationLogEntry($TYPE,$prow,$db_email_msg);

		//set message 
		$db_email_msg['message'] = cron_setmessage( $prow, $db_email_msg );
		//echo "prow email==".$prow['email']; 
		// send mail to patinet
		cron_SendMail( $prow['email'], $db_email_msg['email_subject'], 
				$db_email_msg['message'], $db_email_msg['email_sender'] );
		
		//update entry >> pc_sendalertemail='Yes'
		//cron_updateentry($TYPE,$prow['pid'],$prow['pc_eid']);
		
		echo $strMsg .= " || ALERT SENT SUCCESSFULLY TO ".$prow['email'];
		echo $strMsg .= "\n".$patient_info."\n".$smsgateway_info."\n".$data_info."\n".$db_email_msg['message'];
	
		echo '<br/>'.$query="UPDATE openemr_postcalendar_events SET pc_apptstatus='e***' WHERE pc_eid=".$prow['pc_eid'];
		sqlStatement($query);
	}
	//echo '<br>rh= '.$remain_hour.'<br>ct= '.$CRON_TIME."<br>";
       /* echo "<script>
			alert('app date=".$app_date." timezone = ".date_default_timezone_get()."');
			alert('app time=".$app_time."');			
			alert('app time hr=".$app_time_hour."');
			alert('curr total hr=".$curr_total_hour."');
			alert('RAH=".$remaining_app_hour."');
			alert('E_N_H=".$EMAIL_NOTIFICATION_HOUR."');
			alert('RH=".$remain_hour."');
			alert('CT=".$CRON_TIME."');
			alert('ENOTE1=".$email_notification1_hours."');
			alert('current time=".date('H:i:s')."');
			alert('app time hms=".date('H:i:s',strtotime($app_date))."');
			
	      </script>";*/
        
	/*function transformTime($min)
	{
	echo '<br/>-----------transform time===='.$min;
	
	//$test=new DateTime();
	
	$ctime = DateTime::createFromFormat('i', $min);
	print_r($ctime);
	$ntime= $ctime->format('H:i');
	
	echo '<br/>-----------transform time===='.$ntime;
	return $ntime;
	}

	
	if($email_notification1_hours!='')
	{
		
		echo '<br/>-----'.date('H:i', strtotime($email_notification1_hours*60));
		echo '<br/><br/>minutes==='.$minutes1=($email_notification1_hours*60);

		
		
		echo '<br/>current time=='.$current_time=date('H:i');
		echo '<br/>app time=='.$app_time=date('H:i',$app_time);
		
		echo '<br/>notification hour=='.$email_notification1_hours=transformTime($minutes1);
		
		
		$time_diff=(strtotime($app_time)-strtotime($email_notification1_hours));
		
		$date1 = new DateTime($app_time);
		$datediff = $date1->diff(new DateTime($email_notification1_hours));
		$hrs=$datediff->h;
		$mins=$datediff->i;
		
		$time_diff=$hrs.':'.$mins;
		echo '<br/>'.$time_diff1=date('H:i',strtotime($time_diff));
		
		$flag1=0;
		if($time_diff1==$current_time)
		{
			$flag1=1;
			
		}
		
	}
	if($email_notification2_hours!='')
	{
		echo '<br/><br/>minutes==='.$minutes1=($email_notification2_hours*60);

		echo '<br/>current time=='.$current_time=date('H:i');
		echo '<br/>app time=='.$app_time=date('H:i',$app_time);
		
		echo '<br/>notification hour=='.$email_notification2_hours=transformTime($minutes1);
		
		
		$time_diff=(strtotime($app_time)-strtotime($email_notification2_hours));
		
		$date1 = new DateTime($app_time);
		$datediff = $date1->diff(new DateTime($email_notification2_hours));
		$hrs=$datediff->h;
		$mins=$datediff->i;
		
		$time_diff=$hrs.':'.$mins;
		$time_diff2=date('H:i',strtotime($time_diff));
		
		$flag2=0;
		if($time_diff2==$current_time)
		{
			$flag2=1;
		}
		
	}
	echo '-------------------------'.$email_notification3_hours;
	
	if($email_notification3_hours!='')
	{
		echo '<br/><br/>minutes==='.$minutes1=($email_notification3_hours*60);

		echo '<br/>current time=='.$current_time=date('H:i');
		echo '<br/>app time=='.$app_time=date('H:i',$app_time);
		
		echo '<br/>notification hour=='.$email_notification3_hours=transformTime($minutes1);
		
		
		$time_diff=(strtotime($app_time)-strtotime($email_notification3_hours));
		
		$date1 = new DateTime($app_time);
		$datediff = $date1->diff(new DateTime($email_notification3_hours));
		$hrs=$datediff->h;
		$mins=$datediff->i;
		
		$time_diff=$hrs.':'.$mins;
		echo '<br/>'.$time_diff3=date('H:i',strtotime($time_diff));
		
		$flag3=0;
		
		if($time_diff3==$current_time)
		{
			$flag3=1;
		}
		
	}
	
	
	if($flag1!='' || $flag2!='' || $flag3!='')
	{
	
	
	// insert entry in notification_log table
		cron_InsertNotificationLogEntry($TYPE,$prow,$db_email_msg);

		//set message 
		$db_email_msg['message'] = cron_setmessage( $prow, $db_email_msg );
		//echo "prow email==".$prow['email']; 
		// send mail to patinet
		cron_SendMail( $prow['email'], $db_email_msg['email_subject'], 
				$db_email_msg['message'], $db_email_msg['email_sender'] );
		
		//update entry >> pc_sendalertemail='Yes'
		//cron_updateentry($TYPE,$prow['pid'],$prow['pc_eid']);
		
		echo $strMsg .= " || ALERT SENT SUCCESSFULLY TO ".$prow['email'];
		echo $strMsg .= "\n".$patient_info."\n".$smsgateway_info."\n".$data_info."\n".$db_email_msg['message'];
	
	
	//echo '<br/>decimal==='.date('H:i:s',mktime(0,0,$email_notification1_hours));
	
	}
	
*/
		
	/*	
	echo '<br/><br/>transfer time=='.$notification_hours=transformTime($minutes1); // "Prints" 01:00:00
	*/
	
	
	
	
	/*$time1 = strtotime(date('H:i:s',strtotime($app_date)));
	$time2 = strtotime(date('H:i:s',strtotime($notification_hours)));
	$diff = $time2 - $time1;
	echo '<br/><br/>========Time 1: '.date('H:i:s', $time1).'<br>';
	echo 'Time 2: '.date('H:i:s', $time2).'<br>';

	if($diff){
		echo 'Diff: '.date('H:i:s', $diff);
	}else{
		echo 'No Diff.';
	}*/
	
	//echo '<br/>app minutes=='.date('i',strtotime($app_date));
	/*echo '<br/> app time=='.date('H:i:s',strtotime($app_date));
	
	echo '<br/><br/>notification_hours=='.date('H:i:s',strtotime($notification_hours));
	
	echo '<br/>strtime1==='.strtotime($app_date);
	echo '<br/>strtotime2=='.strtotime($notification_hours);
	
	echo '<br/>==================='.date('H:i:s',strtotime(strtotime($app_date)-strtotime($notification_hours)));
	
	
	echo '<br/>not1==='.$not1 = strtotime(date('H:i:s',(strtotime($app_date - $notification_hours))));
	//echo '<br/>not1==='.$not1 = date('H:i:s',strtotime(date('H:i:s',strtotime($app_date)) - date('H:i:s',strtotime($notification_hours))));
	echo '<br/>not1==='. date('H:i:s',$not1);
	*/
	
	
	/*if($custom_notification_hour >= -($CRON_TIME) &&  $custom_notification_hour <= $CRON_TIME)
	//date('H',strtotime($app_date))
	{
		// insert entry in notification_log table
		cron_InsertNotificationLogEntry($TYPE,$prow,$db_email_msg);

		//set message 
		$db_email_msg['message'] = cron_setmessage( $prow, $db_email_msg );
		//echo "prow email==".$prow['email']; 
		// send mail to patinet
		cron_SendMail( $prow['email'], $db_email_msg['email_subject'], 
				$db_email_msg['message'], $db_email_msg['email_sender'] );
		
		//update entry >> pc_sendalertemail='Yes'
		//cron_updateentry($TYPE,$prow['pid'],$prow['pc_eid']);
		
		echo $strMsg .= " || ALERT SENT SUCCESSFULLY TO ".$prow['email'];
		echo $strMsg .= "\n".$patient_info."\n".$smsgateway_info."\n".$data_info."\n".$db_email_msg['message'];
	}
	
	if($remain_hour<=5 && $remain_hour>=0)
	{
		// insert entry in notification_log table
		cron_InsertNotificationLogEntry($TYPE,$prow,$db_email_msg);

		//set message 
		$db_email_msg['message'] = cron_setmessage( $prow, $db_email_msg );
		//echo "prow email==".$prow['email']; 
		// send mail to patinet
		cron_SendMail( $prow['email'], $db_email_msg['email_subject'], 
				$db_email_msg['message'], $db_email_msg['email_sender'] );
		
		//update entry >> pc_sendalertemail='Yes'
		//cron_updateentry($TYPE,$prow['pid'],$prow['pc_eid']);
		
		echo $strMsg .= " || ALERT SENT SUCCESSFULLY TO ".$prow['email'];
		echo $strMsg .= "\n".$patient_info."\n".$smsgateway_info."\n".$data_info."\n".$db_email_msg['message'];
	}*/
	
	WriteLog( $strMsg );

	// larry :: get notification data again - since was updated by cron_updateentry 
	// todo :: instead fix not to modify the template aka $db_email_msg
	//$db_email_msg = cron_getNotificationData($TYPE);
}

sqlClose();
?>

<html>
<head>
<title>Conrjob - Email Notification</title>
</head>
<body>
	<center>Testing Email alert from OpenEMR
	</center>
</body>
</html>
