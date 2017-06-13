<?php
//  OpenEMR
//  MySQL Config

$host = 'smartmbbsdb.cklyp7uk4jgt.us-west-2.rds.amazonaws.com';
$port = '3306';
$login = 'allcaretphc';
$pass = 'Db0Em4DbDfRrP0d';
$dbase = 'allcaretphc';

//Added ability to disable
//utf8 encoding - bm 05-2009
global $disable_utf8_flag;
$disable_utf8_flag = false;

$sqlconfCentralDB = array();

global $sqlconfCentralDB;
$sqlconfCentralDB = new PDO("mysql:host=$host;dbname=$dbase", $login, $pass,array(PDO::ATTR_PERSISTENT => true));	
//$sqlconf->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


?>
