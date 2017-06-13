<?php
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
require_once("../../verify_session.php");

$pid = $_POST['pid'];
$insStep = $_POST['name'];
$insid = $_POST['insid'];
$cardside = $_POST['cardside'];

if(isset($_FILES['myfile']))
{
    $errors=array();
    $allowed_ext= array('jpg','jpeg','png','gif');
    $file_name =$_FILES['myfile']['name'];
    $file_ext = strtolower( end(explode('.',$file_name)));
    $file_size=$_FILES['myfile']['size'];
    $file_tmp= $_FILES['myfile']['tmp_name'];
    $type = pathinfo($file_name, PATHINFO_EXTENSION);
    echo "Subhan".$type;
    $data = file_get_contents($file_tmp);
    $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
    //echo $base64;
    if(in_array($file_ext,$allowed_ext) === false)
    {
        $errors[]='Extension not allowed';
    }
    //print_r($errors);

}
// Check if insurance data exists for this patient
$row = sqlStatement("SELECT * FROM insurance_data WHERE pid=".$pid." AND provider = ". $insid . " AND type='".$insStep."'");
$count = sqlNumRows($row);

if($count > 0):
    if($cardside == 'front'):
        echo "UPDATE insurance_data SET frontimage = '".addslashes($data)."', frontimageType='".$type."' WHERE pid=".$pid." AND provider = ". $insid . " AND type='".$insStep."'";
        sqlStatement("UPDATE insurance_data SET frontimage = '".addslashes($data)."', frontimageType='".$type."' WHERE pid=".$pid." AND provider = ". $insid . " AND type='".$insStep."'");
    endif;
    if($cardside == 'back'):
        sqlStatement("UPDATE insurance_data SET backimage = '".addslashes($data)."', backimageType='".$type."' WHERE pid=".$pid." AND provider = ". $insid . " AND type='".$insStep."'");
    endif;
endif;

?>