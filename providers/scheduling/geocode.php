<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once("verify-session.php");
require_once("../../library/formdata.inc.php");
require_once("../../library/globals.inc.php");
$pid = $_GET['pid'];
$lat = $_GET['lat'];
$lon = $_GET['lon'];

sqlStatement("UPDATE patient_data SET latitude = ?, longitude=? WHERE pid=?",array($lat,$lon,$pid));

?>