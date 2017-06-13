<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once("../globals.php");
require_once("../../library/formdata.inc.php");
require_once("../../library/globals.inc.php");

if($_POST['attrmigrate'] == 1):
$deceasedQuery = sqlStatement("SELECT pid,fname,mname,lname,deceased_date,deceased_reason FROM patient_data");
    $log = "Log recorded on ". date('Y-m-d H:i:s') . "\nUpdate done by ".$_SESSION['authUser']. " from ipaddress: ".getRealIpAddr()."\n";
    $log .= "Deceased status updated as 'YES' for below list of patients\n";
    $i = 0;
    while($row1=sqlFetchArray($deceasedQuery)):
        if(($row1['deceased_date'] == '0000-00-00 00:00:00') || $row1['deceased_date'] == ""):
            sqlStatement("UPDATE patient_data SET deceased_stat = '' WHERE pid=".$row1['pid']);
        else:
            sqlStatement("UPDATE patient_data SET deceased_stat = 'YES' WHERE pid=".$row1['pid']);
            $log .= $row1['fname']." ". $row1['mname'] . " " . $row1['lname'] ."\n";
            $i++;
        endif;
    endwhile;
    $log .= "Total records updated = ". $i . "\n\n\n";
    file_put_contents('../../../../logs/attrmigration'.date('Y-m-d').'.log', $log, FILE_APPEND);
    echo "Deceased Status Updated <br />";
elseif($_POST['attrmigrate'] == 2):
$activeQuery = sqlStatement("SELECT p.pid,p.fname,p.mname,p.lname,pn.status FROM tbl_allcare_patients1ton pn INNER JOIN patient_data p ON pn.pid=p.pid WHERE p.deceased_date>NOW() OR p.deceased_date IS NULL OR p.deceased_date='0000-00-00 00:00:00'");
    $log2 = "Log recorded on ". date('Y-m-d H:i:s') . "\nUpdate done by ".$_SESSION['authUser']. " from ipaddress: ".getRealIpAddr()."\n";
    $log2 .= "Practice status updated as 'NO' for below list of patients\n";
    $j = 0;
    while($row2=sqlFetchArray($activeQuery)):
        if($row2['status']!="" || $row2['status']!="NULL"):
            sqlStatement("UPDATE patient_data SET practice_status = 'NO' WHERE pid=".$row2['pid']);
            $log2 .= $row2['fname']." ". $row2['mname'] . " " . $row2['lname'] ."\n";
        endif;
        $j++;
    endwhile;
    $log2 .= "Total records updated = ". $j . "\n\n\n";
    file_put_contents('../../../../logs/attrmigration'.date('Y-m-d').'.log', $log2, FILE_APPEND);
    echo "Active With Practice Updated";  
elseif($_POST['attrmigrate'] == 3):     
$insuranceIDQuery  = sqlStatement("SELECT p.pid as pid,p.fname,p.mname,p.lname,i.provider from insurance_data as i INNER JOIN patient_data as p ON i.pid=p.pid WHERE i.type='primary'");
    $log3 = "Log recorded on ". date('Y-m-d H:i:s') . "\nUpdate done by ".$_SESSION['authUser']. " from ipaddress: ".getRealIpAddr()."\n";
    $log3 .= "Insurance ID updated for below list of patients\n";
    $k = 0;
    while($row8=sqlFetchArray($insuranceIDQuery)):
        if($row8['provider'] != ""):
            sqlStatement("UPDATE patient_data SET insuranceID = ".$row8['provider']." WHERE pid=".$row8['pid']);
            $log3 .= $row8['fname']." ". $row8['mname'] . " " . $row8['lname'] ."\n";
        endif;
        $k++;
    endwhile;
    $log3 .= "Total records updated = ". $k . "\n\n\n";
    file_put_contents('../../../../logs/attrmigration'.date('Y-m-d').'.log', $log3, FILE_APPEND);
    echo "Insurance ID Updated";
elseif($_POST['attrmigrate'] == 4):     
$kareoQuery  = sqlStatement("SELECT p.pid,p.fname,p.mname,p.lname,Kareo_Text FROM tbl_allcare_patients1to1 AS p1 INNER JOIN patient_data AS p ON p1.pid = p.pid WHERE p1.pos_id = 1 GROUP BY p1.pid");
    $log4 = "Log recorded on ". date('Y-m-d H:i:s') . "\nUpdate done by ".$_SESSION['authUser']. " from ipaddress: ".getRealIpAddr()."\n";
    $log4 .= "Kareo Text updated for below list of patients\n";
    $l = 0;
    while($row9=sqlFetchArray($kareoQuery)):
        if($row9['Kareo_Text'] != ""):
            //sqlStatement("UPDATE patient_data SET kareo = '".$row9['Kareo_Text']."' WHERE pid=".$row9['pid']);
            sqlStatement("UPDATE patient_data SET kareo = ? WHERE pid=?",array($row9['Kareo_Text'],$row9['pid']));
            $log4 .= $row9['fname']." ". $row9['mname'] . " " . $row9['lname'] ."\n";
        else:
            sqlStatement("UPDATE patient_data SET kareo = ? WHERE pid=?",array('',$row9['pid']));
        endif;
        $l++;
    endwhile;
    $log4 .= "Total records updated = ". $l . "\n\n\n";
    file_put_contents('../../../../logs/attrmigration'.date('Y-m-d').'.log', $log4, FILE_APPEND);
    echo "Kareo Text Updated";    
elseif($_POST['attrmigrate'] == 5):     
$streetQuery  = sqlStatement("SELECT p.pid,p.street FROM patient_data AS p");
    $log5 = "Log recorded on ". date('Y-m-d H:i:s') . "\nUpdate done by ".$_SESSION['authUser']. " from ipaddress: ".getRealIpAddr()."\n";
    $log5 .= "Patient Street Address updated for below list of patients\n";
    $m = 0;
    while($row10=sqlFetchArray($streetQuery)):
        //sqlStatement("UPDATE patient_data SET kareo = '".$row9['Kareo_Text']."' WHERE pid=".$row9['pid']);
            $streetFilter = $street = $apt = "";
            $street = $row10['street'];
            if(strpos($row10['street'],"#") !== false ):
                $streetFilter = explode("#",$row10['street']);
                $street = trim($streetFilter[0]);
                $apt = trim($streetFilter[1]);
            endif;
            if(strpos($row10['street'],"Apt ") !== false ):
                $streetFilter = explode("Apt ",$row10['street']);
                $street = trim($streetFilter[0]);
                $apt = trim($streetFilter[1]);
            endif;
            if(strpos($row10['street'],"Apt #") !== false ):
                $streetFilter = explode("Apt #",$row10['street']);
                $street = trim($streetFilter[0]);
                $apt = trim($streetFilter[1]);
            endif;
            
            sqlStatement("UPDATE patient_data SET street_addr = ?,suite = ? WHERE pid=?",array($street,$apt,$row10['pid']));
            $log5 .= $row10['pid']." ". $street . " " . $apt. "\n";
        $m++;
    endwhile;
    $log5 .= "Total records updated = ". $m . "\n\n\n";
    file_put_contents('../../../../logs/attrmigration'.date('Y-m-d').'.log', $log5, FILE_APPEND);
    echo "Patient Street Address updated<br>";       
    echo "Total records updated = ". $m . "\n\n\n";
elseif($_POST['attrmigrate'] == 6):    
    $encQuery  = sqlStatement("SELECT fe.id,f.form_id,fe.encounter,fe.pid 
                                FROM  `form_encounter` fe
                                INNER JOIN forms f ON fe.encounter = f.encounter
                                WHERE f.formdir =  'newpatient'
                                AND f.deleted =0
                                AND fe.id <> f.form_id");
    $log6 = "Log recorded on ". date('Y-m-d H:i:s') . "\nUpdate done by ".$_SESSION['authUser']. " from ipaddress: ".getRealIpAddr()."\n";
    $log6 .= "Patient Encounter mismatch issue resolved for below list of patients\n";
    $m = 0;
    while($row11=sqlFetchArray($encQuery)):
            sqlStatement("UPDATE forms SET form_id = ? WHERE pid=? AND encounter = ? AND form_id = ? AND formdir = 'newpatient'",array($row11['id'],$row11['pid'],$row11['encounter'],$row11['form_id']));
            $log6 .= "For patient id = ". $row11['pid']." and Encounter ID = ". $row11['encounter'] . "; Form ID updated from". $row11['form_id'] . " to " . $row11['id']. "\n";
        $m++;
    endwhile;
    $log6 .= "Total records updated = ". $m . "\n\n\n";
    file_put_contents('../../../../logs/attrmigration'.date('Y-m-d').'.log', $log6, FILE_APPEND);
    echo "Total records updated = ". $m . "\n\n\n";
endif;

if($_POST['practice'] != "0"):
    if($_POST['from'] != "" && $_POST['to'] != ""):
        $where = " WHERE code BETWEEN ". $_POST['from'] ." AND ". $_POST['to'];
    endif;
    if($_POST['incodes'] != ""):
        $where = " WHERE code IN (".$_POST['incodes'].")";
    endif;
    $Query  = sqlStatement("SELECT DISTINCT pid FROM billing AS b ". $where);
    $log5 = "Log recorded on ". date('Y-m-d H:i:s') . "\nUpdate done by ".$_SESSION['authUser']. " from ipaddress: ".getRealIpAddr()."\n";
    $log5 .= "Patient Practice Updated\n";
    $m = 0;
    $patientArray = array();
    while($row=sqlFetchArray($Query)):
        $patientArray[] = $row['pid'];
        $m++;
    endwhile;
    $patientlist = implode(",",$patientArray);
    sqlStatement("UPDATE patient_data SET practice_status = '".$_POST['practice']."' WHERE pid IN (".$patientlist.")");
    $log5 .= "Practice SET to ".$_POST['practice']." for patient ids in (".$patientlist.")\n";
    $log5 .= "Total records updated = ". $m . "\n\n\n";
    file_put_contents('../../../../logs/attrmigration'.date('Y-m-d').'.log', $log5, FILE_APPEND);
    echo "Patient Practice Updated<br>";       
    echo "Total records updated = ". $m . "\n\n\n";
    
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