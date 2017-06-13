<?php
require_once("../globals.php");
require_once("../../library/formdata.inc.php");
require_once("../../library/globals.inc.php");

echo $encounter=$_POST['enc1'];
echo $patient_id=$_POST['pid1'];
echo $dos=$_POST['date2'];

$sel=sqlStatement("select provider_id ,DATE_FORMAT(date, '%Y-%m-%d') as date from form_encounter where pid=$patient_id AND encounter=$encounter AND DATE( date )='$dos'");
$provider_id = sqlFetchArray($sel);
//update electrically signedby and signed_on
//echo "UPDATE  form_encounter SET elec_signedby=".$provider_id['provider_id']." ,elec_signed_on=DATE_FORMAT( NOW(), '%Y-%m-%d') where pid=$patient_id AND encounter=$encounter AND DATE( date )='$dos' ";

$update=sqlStatement("UPDATE  form_encounter SET elec_signedby=".$provider_id['provider_id']." ,elec_signed_on=DATE_FORMAT( NOW(), '%Y-%m-%d')  where pid=$patient_id AND encounter=$encounter AND DATE( date )='$dos' ");

//create transactions
$mobile_sql=sqlStatement("SELECT * 
                FROM  `tbl_chartui_mapping` 
                WHERE form_id =  'CHARTOUTPUT'
                AND group_name LIKE  '%Mobile%'
                AND screen_name LIKE  '%Mobile%'");
      while($mob_row1=sqlFetchArray($mobile_sql)){
          $field_id.=$mob_row1['field_id'].",";
          $field_value.="'".$mob_row1['option_value']."'".","; 
       }
        $field_id1=rtrim($field_id,",");
        $field_value1=rtrim($field_value,",");
//wecho "INSERT INTO tbl_form_chartoutput_transactions (pid,encounter,date_of_service,$field_id1) VALUES($patient_id,$encounter,'$dos',$field_value1)";
$create_transaction=sqlStatement("INSERT INTO tbl_form_chartoutput_transactions (pid,encounter,date_of_service,created_date,$field_id1) VALUES($patient_id,$encounter,'$dos',NOW(),$field_value1)");
                           



?>
