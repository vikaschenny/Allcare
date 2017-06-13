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

require_once("../../verify_session.php");
require_once("../../../library/sqlCentralDB.inc");
global $sqlconfCentralDB;

$mainPayers = [];
//$mainPayers['insplans'] = [];
$centralPayers = [];
$practicePayers = [];

$sql = "SELECT * from `insurance_companies` inscomp INNER JOIN tbl_inscomp_custom_attr_1to1 inscompattr ON inscomp.id=inscompattr.insuranceid";
$stmt = $sqlconfCentralDB->prepare($sql) ;
$stmt->execute(); 
while ($row = $stmt->fetch()):
    array_push($centralPayers,$row);
endwhile;
$mainPayers['central'] = $centralPayers;

$sql   = sqlStatement("SELECT * from `insurance_companies`");
while($row  = sqlFetchArray($sql)){
    
    array_push($practicePayers,$row);
}
$mainPayers['practice'] = $practicePayers;

echo json_encode($mainPayers);
?>
