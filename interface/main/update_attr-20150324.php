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
                                        IN ( 15, 16, 17, 18, 19, 20, 24, 25, 29 )
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
                                        IN ( 15, 16, 17, 18, 19, 20, 24, 25, 29 ) 
                                        AND p.pid = fe.pid
                                        ORDER BY fe.date DESC 
                                        LIMIT 1 ) ,  ''
                                        ) =  ''
                                        THEN DATE_FORMAT( DATE_ADD( IFNULL( (

                                        SELECT DATE_FORMAT( fe.date,  '%Y-%m-%d %H:%i:%s' ) 
                                        FROM form_encounter fe
                                        WHERE fe.pc_catid
                                        IN ( 11, 27) 
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
        $patientQuery = sqlStatement("SELECT p.pid,p.last_visit FROM patient_data p WHERE (p.deceased_date>NOW() OR p.deceased_date IS NULL OR p.deceased_date='0000-00-00 00:00:00')");
        while($rowp=sqlFetchArray($patientQuery)):
            $lastvisitQuery = sqlStatement("SELECT tf2f.necessary_hhs,count(*) as count
                                            FROM tbl_form_facetoface tf2f
                                            WHERE tf2f.date_of_service = '". $rowp['last_visit'] ."' AND tf2f.pid = ".$rowp['pid']);
            while($row3=sqlFetchArray($lastvisitQuery)):
                if($row3['tf2f.necessary_hhs'] == 0 || $row3['tf2f.necessary_hhs'] == ''):
                    sqlStatement("UPDATE patient_data SET hhs_days = '31' WHERE pid=".$rowp['pid']);
                elseif($row3['count'] == 0):
                    sqlStatement("UPDATE patient_data SET hhs_days = '31' WHERE pid=".$rowp['pid']);
                else:
                    sqlStatement("UPDATE patient_data SET hhs_days = '".$row3['necessary_hhs']."' WHERE pid=".$rowp['pid']);
                endif;
            endwhile;
        endwhile;    
        echo "Next Home Visit Days Updated"; 
    elseif($_POST['selectCalc'] == 4):  
        $calculatedNextQuery = sqlStatement("SELECT DISTINCT p.pid, 
                                        CASE WHEN IFNULL( (

                                        SELECT DATE_FORMAT( fe.date,  '%Y-%m-%d %H:%i:%s' ) 
                                        FROM form_encounter fe
                                        WHERE fe.pc_catid
                                        IN ( 15, 16, 17, 18, 19, 20, 24, 25, 29 ) 
                                        AND p.pid = fe.pid
                                        ORDER BY fe.date DESC 
                                        LIMIT 1 ) ,  ''
                                        ) !=  ''
                                        THEN DATE_FORMAT( DATE_ADD( IFNULL( (

                                        SELECT DATE_FORMAT( fe.date,  '%Y-%m-%d %H:%i:%s' ) 
                                        FROM form_encounter fe
                                        WHERE fe.pc_catid
                                        IN ( 15, 16, 17, 18, 19, 20, 24, 25, 29 ) 
                                        AND p.pid = fe.pid
                                        ORDER BY fe.date DESC 
                                        LIMIT 1 ) ,  '0000-00-00 00:00:00' ) , INTERVAL IFNULL( (

                                        SELECT tf2f.necessary_hhs
                                        FROM tbl_form_facetoface tf2f
                                        WHERE tf2f.pid = p.pid
                                        AND tf2f.date_of_service = p.last_visit LIMIT 1
                                        ), 31 ) 
                                        DAY ) ,  '%Y-%m-%d %H:%i:%s'
                                        )
                                        ELSE  '0000-00-00 00:00:00'
                                        END AS calculated_next_visit
                                        FROM patient_data p WHERE (p.deceased_date>NOW() OR p.deceased_date IS NULL OR p.deceased_date='0000-00-00 00:00:00')");
        while($row4=sqlFetchArray($calculatedNextQuery)):
            sqlStatement("UPDATE patient_data SET calc_next_visit = '".$row4['calculated_next_visit']."' WHERE pid=".$row4['pid']);
        endwhile;
        echo "Calculated Next Visit Dates Updated";     
    endif;
endif;


?>