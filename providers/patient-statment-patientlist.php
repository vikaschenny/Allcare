<?php
/**
 * Copyright (C) 2010 OpenEMR Support LLC
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * 2013/02/08 Minor tweaks by EMR Direct to allow integration with Direct messaging
 * 2013-03-27 by sunsetsystems: Fixed some weirdness with assigning a message recipient,
 *   and allowing a message to be closed with a new note appended and no recipient.
 */

require_once("verify_session.php");
require_once("../library/formdata.inc.php");
require_once("../library/globals.inc.php");

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