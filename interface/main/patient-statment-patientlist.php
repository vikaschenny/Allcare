<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once("../globals.php");
require_once("../../library/formdata.inc.php");
require_once("../../library/globals.inc.php");

$fromD = $_POST['fromD'];
$toD = $_POST['toD'];

$get_plist = sqlStatement("SELECT DISTINCT p.pid,p.fname, p.mname, p.lname from patient_data p JOIN openemr_postcalendar_events pc 
                                      ON p.pid = pc.pc_pid WHERE pc_eventDate between '$fromD' AND '$toD'");
$patient_list = array();
while($plist = sqlFetchArray($get_plist)){
    $patient_list[$plist['pid']] = $plist['fname']." ". $plist['mname'] . " " . $plist['lname'];
}

echo $plist = json_encode($patient_list);

?>