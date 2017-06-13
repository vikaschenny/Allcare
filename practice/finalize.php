<?php
/**
 * Copyright (C) 2010 OpenEMR Support LLC
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * 2013/02/08 Minor tweaks by EMR Direct to allow integration with Direct messaging
 * 2013-03-27 by sunsetsystems: Fixed some weirdness with assigning a message recipient,
 *   and allowing a message to be closed with a new note appended and no recipient.
 */



require_once("verify_session.php");
require_once("../library/formdata.inc.php");
require_once("../library/globals.inc.php");


 
if(isset($_SESSION['portal_username']) !=''){
   $provider=$_SESSION['portal_username'];
}else {
  echo $provider=$_REQUEST['provider'];
}



 $sql=sqlStatement("SELECT id, fname, lname, specialty FROM users " .
      "WHERE active = 1 AND ( info IS NULL OR info NOT LIKE '%Inactive%' ) " .
      "AND authorized = 1 AND username='".$provider."'" .
      "ORDER BY lname, fname");
$id=sqlFetchArray($sql);
$id1=$id['id'];


$encounter=$_POST['enc1'];
$patient_id=$_POST['pid1'];
$dos=$_POST['date2'];

$sel=sqlStatement("select provider_id ,DATE_FORMAT(date, '%Y-%m-%d') as date from form_encounter where pid=$patient_id AND encounter=$encounter AND DATE( date )='$dos'");
$provider_id = sqlFetchArray($sel);


$update=sqlStatement("UPDATE  form_encounter SET elec_signedby=".$provider_id['provider_id']." ,elec_signed_on=DATE_FORMAT( NOW(), '%Y-%m-%d')  where pid=$patient_id AND encounter=$encounter AND DATE( date )='$dos' ");

 $lsql=sqlStatement("SELECT * FROM layout_options WHERE form_id = 'CHARTOUTPUT' AND uor > 0 AND field_id != '' AND group_name='2General' ORDER BY seq");
 while($lrow1=sqlFetchArray($lsql)){
     $layout_field[]=$lrow1['field_id'];
 }

//create transactions
$mobile_sql=sqlStatement("SELECT * 
                FROM  `tbl_chartui_mapping` 
                WHERE form_id =  'CHARTOUTPUT'
                AND group_name LIKE  '%General%'
                AND screen_name LIKE  '%General%'");
      while($mob_row1=sqlFetchArray($mobile_sql)){
          if(in_array($mob_row1['field_id'],$layout_field)){
              $field_id.=$mob_row1['field_id'].",";
              $field_value.="'".$mob_row1['option_value']."'".","; 
          }
          
       }
        $field_id1=rtrim($field_id,",");
        $field_value1=rtrim($field_value,",");

$create_transaction=sqlStatement("INSERT INTO tbl_form_chartoutput_transactions (pid,encounter,date_of_service,created_date,$field_id1) VALUES($patient_id,$encounter,'$dos',NOW(),$field_value1)");
                           



?>
