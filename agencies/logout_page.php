<?php 
include("session_file.php"); 

session_destroy();
header('Location: ../agencies/index.php?site=default&logout=1'); 
?>
