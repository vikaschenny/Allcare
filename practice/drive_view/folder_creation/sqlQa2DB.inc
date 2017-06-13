<?php
//  OpenEMR
//  MySQL Config

$host	= 'mysql51-140.wc2.dfw1.stabletransit.com';
$port	= '3306';
$login	= '551948_qa2allcr';
$pass	= '<)rSg3q=)64Rd=z~';
$dbase	= '551948_qa2allcr';

//Added ability to disable
//utf8 encoding - bm 05-2009
global $disable_utf8_flag;
$disable_utf8_flag = false;

$sqlconfCentralDB = array();

global $sqlconfCentralDB;
$sqlconfCentralDB = new PDO("mysql:host=$host;dbname=$dbase", $login, $pass,array(PDO::ATTR_PERSISTENT => true));	
//$sqlconf->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


?>
