<?php
include_once("../../globals.php");
include_once("$srcdir/api.inc");

require ("allcare_C_FormROS.class.php");

$c = new allcare_C_FormROS();
echo $c->default_action();
?>
