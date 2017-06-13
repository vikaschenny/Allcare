<?php
 require_once("../../interface/globals.php");
 
$uid=$_POST['userid'];
$emailid=$_POST['email'];


function test_input($data) {
   $data = trim($data);
   $data = stripslashes($data);
   $data = htmlspecialchars($data);
   return $data;
}
if($emailid!=''){
    $email = test_input($emailid);
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $data = 'invalid'; 
}else {
    $sql=sqlStatement("select * from tbl_user_custom_attr_1to1 where userid='".$uid."'");
    $rowpha=sqlFetchArray($sql);
    if($uid==$rowpha['userid'])
    {

         $checkemail=sqlStatement("select * from tbl_user_custom_attr_1to1 where email='".$email."' and userid not in($uid)");
         $row_em = sqlFetchArray($checkemail);

        if(!empty($row_em)){
            $data='Exists';
        }else {
           $data='Doesnot_Exists';
        } 
    }
    else
    {
        $checkemail=sqlStatement("select * from tbl_user_custom_attr_1to1 where email='".$email."'");
        $row_email1= sqlFetchArray($checkemail);

        if(!empty($row_email1)){ 
          $data='Exists';
        }else {
          $data='Doesnot_Exists';
        }


    }
}
echo $data."|";
}
//if($emailid!=''){
// $sql=sqlStatement("select * from tbl_user_custom_attr_1to1 where userid='".$uid."'");
//    $rowpha=sqlFetchArray($sql);
//    if($uid==$rowpha['userid'])
//    {
//
//         $checkemail=sqlStatement("select * from tbl_user_custom_attr_1to1 where email='".$emailid."' and userid not in($uid)");
//         $row_em = sqlFetchArray($checkemail);
//
//        if(!empty($row_em)){
//            echo $data='0';
//        }else {
//            echo $data='1';
//        } 
//    }
//    else
//    {
//        $checkemail=sqlStatement("select * from tbl_user_custom_attr_1to1 where email='".$emailid."'");
//        $row_email1= sqlFetchArray($checkemail);
//
//        if(!empty($row_email1)){ 
//          echo $data='0';
//        }else {
//            echo $data='1';
//        }
//
//
//    }
//}
?>    