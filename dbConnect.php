<?php

$host="mysql51-023.wc2.dfw1.stabletransit.com";
$username="551948_alcrdfw";
$psw="SXhVnU!/5~3/=jbe";
$db_live="551948_alcrdfw";
/*
$host="localhost";
$username="root";
$psw="";
$db_live="openemr";
*/


        

$link=  mysql_connect($host,$username,$psw) or die(mysql_error());
$db = mysql_select_db($db_live,$link);


?>
