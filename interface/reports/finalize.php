<?php
require_once("../globals.php");
require_once("../../library/formdata.inc.php");
require_once("../../library/globals.inc.php");

 $encounter=$_POST['enc1']; echo "<br>";
 $patient_id=$_POST['pid1']; echo "<br>";
 $dos=$_POST['date2'];  echo "<br>";

$sel=sqlStatement("select provider_id ,DATE_FORMAT(date, '%Y-%m-%d') as date from form_encounter where pid=$patient_id AND encounter=$encounter AND DATE( date )='$dos'");
$provider_id = sqlFetchArray($sel);
//update electrically signedby and signed_on
//echo "UPDATE  form_encounter SET elec_signedby=".$provider_id['provider_id']." ,elec_signed_on=DATE_FORMAT( NOW(), '%Y-%m-%d') where pid=$patient_id AND encounter=$encounter AND DATE( date )='$dos' ";

$update=sqlStatement("UPDATE  form_encounter SET elec_signedby=".$provider_id['provider_id']." ,elec_signed_on=DATE_FORMAT( NOW(), '%Y-%m-%d')  where pid=$patient_id AND encounter=$encounter AND DATE( date )='$dos' ");

//create transactions
 $lsql=sqlStatement("SELECT * FROM layout_options WHERE form_id = 'CHARTOUTPUT' AND uor > 0 AND field_id != '' AND group_name='2General' ORDER BY seq");
 while($lrow1=sqlFetchArray($lsql)){
     $layout_field[]=$lrow1['field_id'];
 }

 
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
      $field_id1=rtrim($field_id,","); echo "<br>";
      $field_value1=rtrim($field_value,","); echo "<br>";

$create_transaction=sqlStatement("INSERT INTO tbl_form_chartoutput_transactions (pid,encounter,date_of_service,created_date,$field_id1) VALUES($patient_id,$encounter,'$dos',NOW(),$field_value1)");
                           



?>
