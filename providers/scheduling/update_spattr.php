<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once("verify-session.php");
require_once("../../library/formdata.inc.php");
require_once("../../library/globals.inc.php");

// Super Vision Visit Categories
$spvisit_list = '';
$get_spvisit_categories = sqlStatement("SELECT title FROM list_options WHERE list_id= 'allcareConfig' AND option_id='SchedulingSuperVisionVisits'");
while($setvisit = sqlFetchArray($get_spvisit_categories)){
    $spvisit_list = $setvisit['title'];
}

// CPO Visit categories
$cpovisit_list = '';
$get_cpovisit_categories = sqlStatement("SELECT title FROM list_options WHERE list_id= 'allcareConfig' AND option_id='SchedulingSuperVisionVisits'");
while($setvisit = sqlFetchArray($get_cpovisit_categories)){
    $cpovisit_list = $setvisit['title'];
}

if($spvisit_list == "") $spvisit_list = '15, 16, 17, 18, 19, 20, 24, 25, 29, 44';
if($cpovisit_list == "") $cpovisit_list = '11, 27, 28';

$selectIsDeceased = '';
$selectIsActive = '';
if(isset($_POST['selectIsDeceased'])):
    foreach( $_POST['selectIsDeceased'] as $data ){
        if($data == '-2'):
            $selectIsDeceased .="'YES','NO',' ',";
        endif;
        if($data == 'YES'):
            $selectIsDeceased .="'YES'";
        endif;
        if($data == 'NO'):
            $selectIsDeceased .="'NO',' ',";
        endif;
    }
    $selectIsDeceased = rtrim($selectIsDeceased, ',');
endif;
if(isset($_POST['selectIsActive'])):
    foreach( $_POST['selectIsActive'] as $data ){
        if($data == '-2'):
            $selectIsActive .="'YES','NO','', 'PENDING'";
        endif;
         if($data == 'YES'):
            $selectIsActive .="'YES',";
        endif;
        if($data == 'NO'):
            $selectIsActive .="'NO',";
        endif;
        if($data == 'PENDING'):
            $selectIsActive .="'PENDING',";
        endif;
    };
    $selectIsActive = rtrim($selectIsActive, ',');
    //$selectIsActive .= "''";
endif;
$externalClause = "";
$externalClause = " AND p.deceased_stat IN(".$selectIsDeceased.")
                AND p.cpo  IN(".$selectIsActive.")";

if(isset($_POST['selectCalc']) && !empty($_POST['selectCalc'])):
    if($_POST['selectCalc'] == 4):
        $i=0;
        $log = "";
        $log = "Log recorded For CPO category on ". date('Y-m-d H:i:s') . "\nUpdate done by ".$_SESSION['authUser']. " from ipaddress: ".getRealIpAddr()."\n";
        $log .= "CPO Last Visit Dates Updated for below list of patients\n";
        $lastvisitQuery = sqlStatement("SELECT p.pid, DATE_FORMAT( MAX( fe.date ) ,  '%Y-%m-%d %H:%i:%s' ) AS last_visit
                                        FROM form_encounter fe
                                        INNER JOIN patient_data p ON p.pid = fe.pid
                                        WHERE fe.pc_catid
                                        IN ( $spvisit_list ) $externalClause
                                        GROUP BY p.pid
                                        ORDER BY p.pid");
        while($row1=sqlFetchArray($lastvisitQuery)):
            sqlStatement("UPDATE patient_data SET sp_last_visit = '".$row1['last_visit']."' WHERE pid=".$row1['pid']);
            $log .= "CPO Last visit date = ". $row1['last_visit'] . " for patient id = ". $row1['pid']."\n";
            $i++;
        endwhile;
        $log .= "Total CPO Last visit dates updated =". $i ."\n\n\n";
        file_put_contents('../../../../logs/schedulingCalcualtion'.date('Y-m-d').'.log', $log, FILE_APPEND);
        echo "CPO/SuperVision Last Visit Dates Updated<br />";
    elseif($_POST['selectCalc'] == 15):    
        $i = 0;
        $log = "";
        $log = "Log recorded For CPO category on ". date('Y-m-d H:i:s') . "\nUpdate done by ".$_SESSION['authUser']. " from ipaddress: ".getRealIpAddr()."\n";
        $log .= "CPO Calculated Next Visit Dates Updated for below list of patients\n";
        $calculatedNextQuery = sqlStatement("SELECT DISTINCT p.pid, 
                                        CASE WHEN IFNULL( (

                                        SELECT DATE_FORMAT( fe.date,  '%Y-%m-%d %H:%i:%s' ) 
                                        FROM form_encounter fe
                                        WHERE fe.pc_catid
                                        IN ( $spvisit_list ) 
                                        AND p.pid = fe.pid $externalClause
                                        ORDER BY fe.date DESC 
                                        LIMIT 1 ) ,  ''
                                        ) !=  ''
                                        THEN DATE_FORMAT( LAST_DAY(DATE_ADD( IFNULL( (

                                        SELECT DATE_FORMAT( fe.date,  '%Y-%m-%d %H:%i:%s' ) 
                                        FROM form_encounter fe
                                        WHERE fe.pc_catid
                                        IN ( $spvisit_list ) 
                                        AND p.pid = fe.pid $externalClause
                                        ORDER BY fe.date DESC 
                                        LIMIT 1 ) ,  '0000-00-00 00:00:00' ) , INTERVAL 1 MONTH )) ,  '%Y-%m-%d %H:%i:%s'
                                        )
                                        ELSE  '0000-00-00 00:00:00'
                                        END AS calculated_next_visit
                                        FROM patient_data p WHERE (p.deceased_date>NOW() OR p.deceased_date IS NULL OR p.deceased_date='0000-00-00 00:00:00')");
        while($row4=sqlFetchArray($calculatedNextQuery)):
            sqlStatement("UPDATE patient_data SET calc_next_sp = '".$row4['calculated_next_visit']."' WHERE pid=".$row4['pid']);
            $log .= "CPO Calculated Next Visit Dates = ". $row4['calculated_next_visit'] . " for patient id = ". $row4['pid']."\n";
            $i++;
        endwhile;
        
        $datesQuery = sqlStatement("SELECT pid,calc_next_sp FROM patient_data");
        while($row6 = sqlFetchArray($datesQuery)):
            if($row6['calc_next_sp'] == '-0001-11-30 00:00:00'):
                sqlStatement("UPDATE patient_data SET calc_next_sp = '0000-00-00 00:00:00' WHERE pid=".$row6['pid']);
                $log .= "CPO Calculated Next Visit Dates = '0000-00-00 00:00:00' for patient id = ". $row6['pid']."\n";
                $i++;
            endif;
        endwhile;
        $log .= "Total CPO Calculated Next Visit Dates Updated =". $i ."\n\n\n";
        file_put_contents('../../../../logs/schedulingCalcualtion'.date('Y-m-d').'.log', $log, FILE_APPEND);
        echo "Calculated Next CPO/SuperVision Visit Dates Updated";
        
    endif;
endif;

function getRealIpAddr()
{
    if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
    {
      $ip=$_SERVER['HTTP_CLIENT_IP'];
    }
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
    {
      $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    else
    {
      $ip=$_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}
?>