<?php
require_once("verify_session.php");

$addrId = $_POST['addrId'];
$sqlProvider = sqlStatement("SELECT address FROM tbl_allcare_referralproviders WHERE id=".$addrId);
$sqlProv = sqlFetchArray($sqlProvider);
echo $sqlProv['address'];
?>