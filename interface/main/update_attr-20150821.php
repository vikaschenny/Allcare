<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once("../globals.php");
require_once("../../library/formdata.inc.php");
require_once("../../library/globals.inc.php");
if(isset($_POST['selectCalc']) && !empty($_POST['selectCalc'])):
    if($_POST['selectCalc'] == 1):  
        $lastvisitQuery = sqlStatement("SELECT p.pid, DATE_FORMAT( MAX( fe.date ) ,  '%Y-%m-%d %H:%i:%s' ) AS last_visit
                                        FROM form_encounter fe
                                        INNER JOIN patient_data p ON p.pid = fe.pid
                                        WHERE fe.pc_catid
                                        IN ( 15, 16, 17, 18, 19, 20, 24, 25, 29, 44 )
                                        GROUP BY p.pid
                                        ORDER BY p.pid");
        while($row1=sqlFetchArray($lastvisitQuery)):
            sqlStatement("UPDATE patient_data SET last_visit = '".$row1['last_visit']."' WHERE pid=".$row1['pid']);
        endwhile;
        echo "Last Visit Dates Updated";
    elseif($_POST['selectCalc'] == 2):  
        $certendQuery = sqlStatement("SELECT p.pid, 
                                        CASE WHEN IFNULL( (

                                        SELECT DATE_FORMAT( fe.date,  '%Y-%m-%d %H:%i:%s' ) 
                                        FROM form_encounter fe
                                        WHERE fe.pc_catid
                                        IN ( 15, 16, 17, 18, 19, 20, 24, 25, 29, 44 ) 
                                        AND p.pid = fe.pid
                                        ORDER BY fe.date DESC 
                                        LIMIT 1 ) ,  ''
                                        ) <>  ''
                                        THEN ( SELECT DATE_FORMAT( fe.date,  '%Y-%m-%d %H:%i:%s' ) 
                                        FROM form_encounter fe
                                        WHERE fe.pc_catid
                                        IN ( 15, 16, 17, 18, 19, 20, 24, 25, 29, 44 ) 
                                        AND p.pid = fe.pid
                                        ORDER BY fe.date DESC 
                                        LIMIT 1)
                                        WHEN IFNULL( (

                                        SELECT DATE_FORMAT( fe.date,  '%Y-%m-%d %H:%i:%s' ) 
                                        FROM form_encounter fe
                                        WHERE fe.pc_catid
                                        IN ( 15, 16, 17, 18, 19, 20, 24, 25, 29, 44 ) 
                                        AND p.pid = fe.pid
                                        ORDER BY fe.date DESC 
                                        LIMIT 1 ) ,  ''
                                        ) =  ''
                                        THEN DATE_FORMAT( DATE_ADD( IFNULL( (

                                        SELECT DATE_FORMAT( fe.date,  '%Y-%m-%d %H:%i:%s' ) 
                                        FROM form_encounter fe
                                        WHERE fe.pc_catid
                                        IN ( 11, 27, 28 ) 
                                        AND p.pid = fe.pid
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
        while($row2=sqlFetchArray($certendQuery)):
            sqlStatement("UPDATE patient_data SET cert_end = '".$row2['certification_end_date']."' WHERE pid=".$row2['pid']);
        endwhile;
        echo "Certification Ends Updated";    
    elseif($_POST['selectCalc'] == 3): 
        $patientid = array();
        $lastvisitQuery = sqlStatement("SELECT lfd.field_id, lfd.field_value, f.pid, pd.dur_manual
                                FROM lbf_data lfd, forms f, patient_data pd
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
                                AND f.pid=pd.pid");
        while($row3=sqlFetchArray($lastvisitQuery)):
            if($row3['dur_manual'] != 'YES'):
                sqlStatement("UPDATE patient_data SET hhs_days = ".$row3['field_value']." WHERE pid=".$row3['pid']);
                array_push($patientid, $row3['pid']);
            endif;
        endwhile;
        $p = implode(",",$patientid);
        sqlStatement("UPDATE patient_data SET hhs_days=31 WHERE pid NOT IN (".$p.") AND dur_manual!='YES'");
        echo "Next Home Visit Days Updated"; 
    elseif($_POST['selectCalc'] == 4):  
        $calculatedNextQuery = sqlStatement("SELECT DISTINCT p.pid, 
                                        CASE WHEN IFNULL( (

                                        SELECT DATE_FORMAT( fe.date,  '%Y-%m-%d %H:%i:%s' ) 
                                        FROM form_encounter fe
                                        WHERE fe.pc_catid
                                        IN ( 15, 16, 17, 18, 19, 20, 24, 25, 29, 44 ) 
                                        AND p.pid = fe.pid
                                        ORDER BY fe.date DESC 
                                        LIMIT 1 ) ,  ''
                                        ) !=  ''
                                        THEN DATE_FORMAT( DATE_ADD( IFNULL( (

                                        SELECT DATE_FORMAT( fe.date,  '%Y-%m-%d %H:%i:%s' ) 
                                        FROM form_encounter fe
                                        WHERE fe.pc_catid
                                        IN ( 15, 16, 17, 18, 19, 20, 24, 25, 29, 44 ) 
                                        AND p.pid = fe.pid
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
            
        endwhile;
        $datesQuery = sqlStatement("SELECT pid,calc_next_visit FROM patient_data");
        while($row6 = sqlFetchArray($datesQuery)):
            if($row6['calc_next_visit'] == '-0001-11-30 00:00:00'):
                sqlStatement("UPDATE patient_data SET calc_next_visit = '0000-00-00 00:00:00' WHERE pid=".$row6['pid']);
            endif;
        endwhile;
        echo "Calculated Next Visit Dates Updated";     
    endif;
endif;


?>