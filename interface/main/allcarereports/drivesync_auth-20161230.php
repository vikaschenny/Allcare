<?php
 require_once("../../globals.php");

 $email=$_REQUEST['email'];
 if($_REQUEST['status']==1){
   
     echo $email. " Sucessfully Authenticated<br/>";
     
 }
?>

