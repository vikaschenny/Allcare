<?php
require_once("../interface/globals.php");
$order=$_POST['string'];
$provider=$_POST['provider'];
$menu=$_POST['menu'];

$sql1=sqlStatement("SELECT * from tbl_allcare_providers_fieldsorder where username='$provider' AND menu='$menu'");
$row1=sqlFetchArray($sql1);
if(empty($row1)){
$sql=sqlStatement("INSERT INTO `tbl_allcare_providers_fieldsorder`(`username`, `menu`, `order_of_columns`) VALUES ('$provider','$menu','$order') ");
}else{
    $sql=sqlStatement("UPDATE `tbl_allcare_providers_fieldsorder` SET `order_of_columns`='$order' WHERE username='$provider' AND menu='$menu'");
}
?>
