<?php
require_once("../../globals.php");
require_once("$srcdir/sql.inc");
$select="delete from tbl_patientagency where id=".$_GET['del_id'];
$query=mysql_query($select);

?>