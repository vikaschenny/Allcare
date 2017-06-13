<?php
require_once("../verify_session.php");
extract($_REQUEST);
$sql=sqlStatement("update  tbl_inscomp_benefits set deleted=1 where id=$id");
if($sql){
    echo "success";
}else {
    echo "fail";
}
?>