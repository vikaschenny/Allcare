<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once("verify-session.php");
require_once("../../library/formdata.inc.php");
require_once("../../library/globals.inc.php");

// Scheduling Visit Categories from allcareConfig lists
$visit_list = '';
$get_visit_categories = sqlStatement("SELECT title FROM list_options WHERE list_id= 'allcareConfig' AND option_id='SchedulingVisitCategories'");
while($setvisit = sqlFetchArray($get_visit_categories)){
    $visit_list = $setvisit['title'];
}
$cpovisit_list = '';
$get_cpovisit_categories = sqlStatement("SELECT title FROM list_options WHERE list_id= 'allcareConfig' AND option_id='SchedulingSuperVisionVisits'");
while($setvisit = sqlFetchArray($get_cpovisit_categories)){
    $cpovisit_list = $setvisit['title'];
}

$get_defaulthhsdays = sqlStatement("SELECT title FROM list_options WHERE list_id= 'allcareConfig' AND option_id='defaulthhsdays'");
while($setdays = sqlFetchArray($get_defaulthhsdays)){
    $hhsdays = $setdays['title'];
}

if($visit_list == "") $visit_list = '15, 16, 17, 18, 19, 20, 24, 25, 29, 44';
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
                AND p.practice_status  IN(".$selectIsActive.")";

if(isset($_POST['selectCalc']) && !empty($_POST['selectCalc'])):
    if($_POST['selectCalc'] == 1):
        $i=0;
        $log = "";
        $log = "Log recorded For New/Estd category on ". date('Y-m-d H:i:s') . "\nUpdate done by ".$_SESSION['authUser']. " from ipaddress: ".getRealIpAddr()."\n";
        $log .= "New/Established Last Visit Dates Updated for below list of patients\n";
        $lastvisitQuery = sqlStatement("SELECT p.pid, DATE_FORMAT( MAX( fe.date ) ,  '%Y-%m-%d %H:%i:%s' ) AS last_visit
                                        FROM form_encounter fe
                                        INNER JOIN patient_data p ON p.pid = fe.pid
                                        WHERE fe.pc_catid
                                        IN ( $visit_list ) $externalClause
                                        GROUP BY p.pid
                                        ORDER BY p.pid");
        while($row1=sqlFetchArray($lastvisitQuery)):
            sqlStatement("UPDATE patient_data SET last_visit = '".$row1['last_visit']."' WHERE pid=".$row1['pid']);
            $log .= "Last visit date = ". $row1['last_visit'] . " for patient id = ". $row1['pid']."\n";
            $i++;
        endwhile;
        $log .= "Total Last visit dates updated =". $i ."\n\n\n";
        file_put_contents('../../../../logs/schedulingCalcualtion'.date('Y-m-d').'.log', $log, FILE_APPEND);
        echo "New/Established Last Visit Dates Updated<br />";
     elseif($_POST['selectCalc'] == 8):   
        $certendQuery = sqlStatement("SELECT p.pid, 
                                        CASE WHEN IFNULL( (

                                        SELECT DATE_FORMAT( fe.date,  '%Y-%m-%d %H:%i:%s' ) 
                                        FROM form_encounter fe
                                        WHERE fe.pc_catid
                                        IN ( $visit_list ) 
                                        AND p.pid = fe.pid $externalClause
                                        ORDER BY fe.date DESC 
                                        LIMIT 1 ) ,  ''
                                        ) <>  ''
                                        THEN ( SELECT DATE_FORMAT( fe.date,  '%Y-%m-%d %H:%i:%s' ) 
                                        FROM form_encounter fe
                                        WHERE fe.pc_catid
                                        IN ( $visit_list ) 
                                        AND p.pid = fe.pid $externalClause
                                        ORDER BY fe.date DESC 
                                        LIMIT 1)
                                        WHEN IFNULL( (

                                        SELECT DATE_FORMAT( fe.date,  '%Y-%m-%d %H:%i:%s' ) 
                                        FROM form_encounter fe
                                        WHERE fe.pc_catid
                                        IN ( $visit_list ) 
                                        AND p.pid = fe.pid $externalClause
                                        ORDER BY fe.date DESC 
                                        LIMIT 1 ) ,  ''
                                        ) =  ''
                                        THEN DATE_FORMAT( DATE_ADD( IFNULL( (

                                        SELECT DATE_FORMAT( fe.date,  '%Y-%m-%d %H:%i:%s' ) 
                                        FROM form_encounter fe
                                        WHERE fe.pc_catid
                                        IN ( $cpovisit_list ) 
                                        AND p.pid = fe.pid $externalClause
                                        ORDER BY fe.date DESC 
                                        LIMIT 1 ) ,  '0000-00-00 00:00:00' ) , INTERVAL 60 
                                        DAY ) ,  '%Y-%m-%d %H:%i:%s'
                                        )
                                        ELSE  '0000-00-00 00:00:00'
                                        END AS certification_end_date
                                        FROM patient_data p
                                        WHERE (
                                        p.deceased_date > NOW( ) 
                                        OR p.deceased_date IS NULL 
                                        OR p.deceased_date =  '0000-00-00 00:00:00'
                                        )");
        $i = 0;
        $log = "";
        $log = "Log recorded For New/Estd category on ". date('Y-m-d H:i:s') . "\nUpdate done by ".$_SESSION['authUser']. " from ipaddress: ".getRealIpAddr()."\n";
        $log .= "New/Established Certification Ends Updated for below list of patients\n";
        while($row2=sqlFetchArray($certendQuery)):
            sqlStatement("UPDATE patient_data SET cert_end = '".$row2['certification_end_date']."' WHERE pid=".$row2['pid']);
            $log .= "certification end date = ". $row2['certification_end_date'] . " for patient id = ". $row2['pid']."\n";
            $i++;
        endwhile;
        $log .= "Total Certification Ends updated =". $i ."\n\n\n";
        file_put_contents('../../../../logs/schedulingCalcualtion'.date('Y-m-d').'.log', $log, FILE_APPEND);
        echo "New/Established Certification Ends Updated<br />";    
     elseif($_POST['selectCalc'] == 9):   
        $patientid = array();
        $lastvisitQuery = sqlStatement("SELECT lfd.field_id, lfd.field_value, f.pid, p.dur_manual
                                FROM lbf_data lfd, forms f, patient_data p
                                WHERE lfd.field_id =  'f2f_duration'
                                AND lfd.form_id
                                IN (
                                    SELECT f.form_id
                                    FROM forms f
                                    WHERE f.formdir =  'LBF2'
                                    AND f.DATE = ( 
                                    SELECT MAX( DATE ) 
                                    FROM forms
                                    WHERE pid = f.pid
                                    AND formdir =  'LBF2'
                                    AND deleted =0
                                    GROUP BY pid )
                                    )
                                AND lfd.form_id = f.form_id
                                AND f.pid=p.pid");
        $i = 0;
        $log = "";
        $log = "Log recorded For New/Estd category on ". date('Y-m-d H:i:s') . "\nUpdate done by ".$_SESSION['authUser']. " from ipaddress: ".getRealIpAddr()."\n";
        $log .= "New/Established Next Home Visit Days Updated for below list of patients\n";
        while($row3=sqlFetchArray($lastvisitQuery)):
            if($row3['dur_manual'] != 'YES'):
                sqlStatement("UPDATE patient_data SET hhs_days = '".$row3['field_value']."' WHERE pid=".$row3['pid']);
                $log .= "Next Home Visit Days = ". $row3['field_value'] . " for patient id = ". $row3['pid']."\n";
                $i++;
                array_push($patientid, $row3['pid']);
            endif;
        endwhile;
        $p = implode(",",$patientid);
        if($p != ""):
            sqlStatement("UPDATE patient_data SET hhs_days=".$hhsdays." WHERE pid NOT IN (".$p.") AND dur_manual!='YES'");
        else:    
            sqlStatement("UPDATE patient_data SET hhs_days=".$hhsdays." WHERE dur_manual!='YES'");
        endif;
        
        $log .= "UPDATE patient_data SET hhs_days=".$hhsdays." WHERE pid NOT IN (".$p.") AND dur_manual!='YES'";
        $log .= "Total Next Home Visit Days updated =". $i ."\n\n\n";
        file_put_contents('../../../../logs/schedulingCalcualtion'.date('Y-m-d').'.log', $log, FILE_APPEND);
        echo "New/Established Next Home Visit Days Updated<br />"; 
      elseif($_POST['selectCalc'] == 10):  
        $i = 0;
        $log = "";
        $log = "Log recorded For New/Estd category on ". date('Y-m-d H:i:s') . "\nUpdate done by ".$_SESSION['authUser']. " from ipaddress: ".getRealIpAddr()."\n";
        $log .= "New/Established Calculated Next Visit Dates Updated for below list of patients\n";
        $calculatedNextQuery = sqlStatement("SELECT DISTINCT p.pid, 
                                        CASE WHEN IFNULL( (

                                        SELECT DATE_FORMAT( fe.date,  '%Y-%m-%d %H:%i:%s' ) 
                                        FROM form_encounter fe
                                        WHERE fe.pc_catid
                                        IN ( $visit_list ) 
                                        AND p.pid = fe.pid $externalClause
                                        ORDER BY fe.date DESC 
                                        LIMIT 1 ) ,  ''
                                        ) !=  ''
                                        THEN DATE_FORMAT( DATE_ADD( IFNULL( (

                                        SELECT DATE_FORMAT( fe.date,  '%Y-%m-%d %H:%i:%s' ) 
                                        FROM form_encounter fe
                                        WHERE fe.pc_catid
                                        IN ( $visit_list ) 
                                        AND p.pid = fe.pid $externalClause
                                        ORDER BY fe.date DESC 
                                        LIMIT 1 ) ,  '0000-00-00 00:00:00' ) , INTERVAL ( p.hhs_days                                    
                                        ) 
                                        DAY ) ,  '%Y-%m-%d %H:%i:%s'
                                        )
                                        ELSE  '0000-00-00 00:00:00'
                                        END AS calculated_next_visit
                                        FROM patient_data p WHERE (p.deceased_date>NOW() OR p.deceased_date IS NULL OR p.deceased_date='0000-00-00 00:00:00')");
        while($row4=sqlFetchArray($calculatedNextQuery)):
            sqlStatement("UPDATE patient_data SET calc_next_visit = '".$row4['calculated_next_visit']."' WHERE pid=".$row4['pid']);
            $log .= "New/Established Calculated Next Visit Dates = ". $row4['calculated_next_visit'] . " for patient id = ". $row4['pid']."\n";
            $i++;
        endwhile;
        
        $datesQuery = sqlStatement("SELECT pid,cpo,calc_next_visit,cert_end,refill_due FROM patient_data p WHERE (p.deceased_date>NOW() OR p.deceased_date IS NULL OR p.deceased_date='0000-00-00 00:00:00')");
        while($row5 = sqlFetchArray($datesQuery)):
            $nextV = $lastV = $refillD = "0000-00-00 00:00:00";
        
            $nextV = strtotime($row5['calc_next_visit']);
            $certV = strtotime($row5['cert_end']);
            $refillD = strtotime($row5['refill_due']);
            if($row5['cpo'] == 'YES'):
                if($refillD == 0):
                    $minimum = min($nextV,$certV);
                endif;
                if($certV == 0):
                    $minimum = min($nextV,$refillD);
                endif;
                if($nextV == 0):
                    $minimum = min($refillD,$certV);
                endif;
                if($refillD != 0 && $certV != 0 && $nextV != 0):
                    $minimum = min($nextV,$refillD,$certV);
                endif;
            else:
                if($refillD == 0):
                    $minimum = $nextV;
                endif;
                if($nextV == 0):
                    $minimum = $refillD;
                endif;
                if($refillD != 0 && $nextV != 0):
                    $minimum = min($nextV,$refillD);
                endif;
            endif;
            
            $minDate = date('Y-m-d H:i:s',$minimum);
            if($minimum == 0) $minDate = "0000-00-00 00:00:00";
            sqlStatement("UPDATE patient_data SET calc_next_visit = '".$minDate."' WHERE pid=".$row5['pid']);
            $log .= "New/Established Calculated Next Visit Dates = ". $minDate . " for patient id = ". $row5['pid']."\n";
            $i++;
        endwhile;
        $datesQuery = sqlStatement("SELECT pid,calc_next_visit FROM patient_data");
        while($row6 = sqlFetchArray($datesQuery)):
            if($row6['calc_next_visit'] == '-0001-11-30 00:00:00'):
                sqlStatement("UPDATE patient_data SET calc_next_visit = '0000-00-00 00:00:00' WHERE pid=".$row6['pid']);
                $log .= "New/Established Calculated Next Visit Dates = '0000-00-00 00:00:00' for patient id = ". $row6['pid']."\n";
                $i++;
            endif;
        endwhile;
        $log .= "Total New/Established Calculated Next Visit Dates Updated =". $i ."\n\n\n";
        file_put_contents('../../../../logs/schedulingCalcualtion'.date('Y-m-d').'.log', $log, FILE_APPEND);
        echo "New/Established Calculated Next Visit Dates Updated";     
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