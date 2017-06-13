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


$patient_data=$_POST['data'];
// print_r($patient_data);
$provider=$_REQUEST['provider'];
$patient=$_REQUEST['set_pid'];
$sql=sqlStatement("SELECT id, fname, lname, specialty FROM users " .
      "WHERE active = 1 AND ( info IS NULL OR info NOT LIKE '%Inactive%' ) " .
      "AND authorized = 1 AND username='".$provider."'" .
      "ORDER BY lname, fname");
$id=sqlFetchArray($sql);
if(!empty($patient_data)){
   foreach($patient_data as $key => $value){
       //echo $key."=============================".$value;
       if($value==''  && $key!='age'){
           $fields.=$key."="."''".",";
       }elseif($key!='age'){
           $fields.=$key."="."'$value'".",";
       }
     }
    $fields1=rtrim($fields,",");
    //echo $patient_data['pid'];
    //echo "UPDATE `patient_data` SET $fields1 WHERE pid=".$patient_data['pid']."";
    $update=sqlStatement("UPDATE `patient_data` SET $fields1 WHERE pid=".$patient."");
    echo "<script> window.parent.location.href = '../../../practice/providers_patient.php?provider=$provider'; parent.$.fancybox.close();  </script>"; 
}

?>

<!DOCTYPE html>

<html>

	<head>
		<meta charset="UTF-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
	    <meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="shortcut icon" href="img/season-change.jpg" type="image/x-icon">
		<title>HealthCare</title>

		
	    <link href='https://fonts.googleapis.com/css?family=Abel' rel='stylesheet' type='text/css'>
	    <!-- <link href='https://fonts.googleapis.com/css?family=Pontano+Sans' rel='stylesheet' type='text/css'>
	    <link href='https://fonts.googleapis.com/css?family=Alegreya+Sans:300,400,500,700' rel='stylesheet' type='text/css'> -->
	    <link href='https://fonts.googleapis.com/css?family=Roboto:400,300,500' rel='stylesheet' type='text/css'>
	    <link href='https://fonts.googleapis.com/css?family=Dosis:300,400,500,600' rel='stylesheet' type='text/css'>
	    
	    
		<link rel="stylesheet" type="text/css" href="assets/css/animate.css">
<!--		<link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">-->
		<link rel="stylesheet" type="text/css" href="assets/css/owl.carousel.css">
		<link rel="stylesheet" type="text/css" href="assets/css/owl.theme.css">
		<link rel="stylesheet" type="text/css" href="assets/css/owl.transitions.css">
		<link rel="stylesheet" type="text/css" href="assets/css/font-awesome.min.css">
		<link rel="stylesheet" type="text/css" href="assets/css/main.css">
		<link href='https://fonts.googleapis.com/css?family=Roboto+Condensed:400,300' rel='stylesheet' type='text/css'>


 
<script type='text/javascript'>
function win1(url){
     // alert(url);
    window.open(url,'popup','width=900,height=900,scrollbars=yes,resizable=yes');
} 
function submitme() {
 var f = document.forms['patient_edit'];
 if (validate(f)) {
  //top.restoreSession();
  f.submit();

 }
 
 }

function closeme(){
    window.close();
}
</script>
</head>

	<body onload="<?php echo $body_onload_code; ?>">
           <div >
		  <?php
                          function display_db_query($query_string,$provider,$patient) {
                                // perform the database query
                                $result_id = mysql_query($query_string)
                                or die("display_db_query:" . mysql_error());
                                // find out the number of columns in result
                                $column_count = mysql_num_fields($result_id)
                                or die("display_db_query:" . mysql_error());
                                // Here the table attributes from the $table_params variable are added
                                echo "<br/>"; ?>
                               <center><h2>Patient Details</h2><form name="patient_edit" action="providers_patient_edit.php" method="post" >
                                              <table cellspacinf="5px !important;" style=" border-collapse:initial !important;">  
                                               <?php  while($row = mysql_fetch_row($result_id)) {
                                                        for($column_num = 0; $column_num < $column_count; $column_num++) {
                                                            $field_name = mysql_field_name($result_id, $column_num);
                                                             $sql1=sqlStatement("SELECT title,field_id from layout_options where field_id='$field_name' AND form_id='DEM'");
                                                             $row1=sqlFetchArray($sql1);
                                                             if($field_name!='pid' && $field_name!='age'){     ?>
                                                               <tr><td><?php if($row1['title']!=''){ echo  $row1['title']; }else { echo  $row1['field_id']; }?>:</td><td> <input type="text" name="data[<?php echo $field_name; ?>]" id="<?php echo $field_name; ?>" value="<?php echo $row[$column_num]; ?>"/></td></tr>
                                                             
                                                             <?php }else{
                                                                 if($field_name=='pid'){ ?>
                                                                      <tr><td> Patient Id:</td><td> <input type="text" name="data[<?php echo $field_name; ?>]" id="<?php echo $field_name; ?>" value="<?php echo $row[$column_num]; ?>" disabled/></td></tr>
                                                                <?php  }elseif($field_name=='Age'){ ?>
                                                                     <tr><td> Age:</td><td> <input type="text" name="data[<?php echo $field_name; ?>]" id="<?php echo $field_name; ?>" value="<?php echo $row[$column_num]; ?>"/></td></tr>
                                                                <?php }
                                                                 
                                                             }
                                                             
                                                            }
                                                           echo "<br>";
                                                        } ?>
                                               </table>  
                                              <input type="hidden" name="provider" id="provider" value="<?php echo $provider; ?>"/>
                                              <input type="hidden" name="set_pid" id="set_pid" value="<?php echo $patient; ?>"/>
                                              <input type="hidden" name="mode" id="mode" value="update"/>
                                              <input type="submit" name="submit" value="update" onclick="submitme();"/>
                                              <input type="submit" name="cancel" value="cancel" onclick="closeme();"/>
                                </form></center>
                        <?php }
                        function display_db_table($provider,$patient) {
                            //echo "select id from users where username='".$provider."'";
                              $sql=sqlStatement("select id from users where username='".$provider."'");
                              $row=sqlFetchArray($sql);
                              $id=$row['id'];
                              $sql_vis=sqlStatement("SELECT provider_plist from tbl_user_custom_attr_1to1 where userid='$id'");
                              $row1_vis=sqlFetchArray($sql_vis);
                              if(!empty($row1_vis)) {
                                $avail3=explode("|",$row1_vis['provider_plist']);
                                     //echo "<pre>"; print_r($avail3); echo "</pre>";

                              $sql1=sqlStatement("SELECT * from tbl_allcare_providers_fieldsorder where username='$provider' AND menu='plist'");
                              $row1=sqlFetchArray($sql1);
                              if(!empty($row1)){
                                  $orders=explode(",",$row1['order_of_columns']);
                                  //echo "<pre>"; print_r($orders); echo "</pre>";
                                  $field1='';  $fields=array();
                                  foreach($orders as $value2){
                                      $field=explode(":",$value2);

                                      if($field[1]!='Patient_Id' && $field[1]!='Patient_Name' && $field[1]!='Age'){
                                          $title=str_replace("_"," ",$field[1]);
                                          $sql2=sqlStatement("SELECT field_id from layout_options where form_id='DEM' AND title='$title' order by seq, group_name");
                                           $row2=sqlFetchArray($sql2);
                                          if (in_array($row2['field_id'], $avail3)){
                                                $field1.=$row2['field_id'].",";
                                          }


                                      }elseif($field[1]=='Patient_Id'){
                                           if (in_array($field[1], $avail3)){
                                                 $field1.="fe.pid,";
                                           }
                                      }elseif($field[1]=='Patient_Name'){
                                          if (in_array($field[1], $avail3)){
                                            $field1.="fname,lname,";
                                          }
                                      }elseif($field[1]=='Age'){
                                           if (in_array($field[1], $avail3)){
                                             $field1.="DATE_FORMAT(FROM_DAYS(DATEDIFF(CURDATE(),DOB)), '%Y')+0 AS age,";
                                           }
                                      }    
                                   }
                                        $field2=rtrim($field1,",");
                                        $sql12="SELECT $field2 from form_encounter fe  INNER JOIN patient_data p ON p.pid=fe.pid  INNER JOIN openemr_postcalendar_events o ON o.pc_pid = fe.pid "
                                      . " AND o.pc_catid = fe.pc_catid AND fe.facility_id = o.pc_facility
                                          AND o.pc_eventDate = DATE_FORMAT( fe.date,  '%Y-%m-%d' )where o.pc_aid =$id AND p.pid= $patient group by fe.pid";
                              }
                              else {
                                     $avail=explode("|",$row1_vis['provider_plist']);

                                     foreach($avail as $val2){
                                         if($val2=='Patient_Id'){
                                             $avail_field.="fe.pid,";
                                         }elseif($val2=='Patient_Name'){
                                             //$avail_field.="CONCAT(title ,'',fname,',',lname) as Name,";
                                              $avail_field.="fname,lname,";
                                         }elseif($val2=='age'){
                                             $avail_field.="DATE_FORMAT(FROM_DAYS(DATEDIFF(CURDATE(),DOB)), '%Y')+0 AS age,";
                                         }else{
                                             $avail_field.=$val2.",";
                                         }

                                     }
                                     $avail_field2=rtrim($avail_field,",");
//                                                                 $sql12="SELECT fe.pid,CONCAT(title ,'',fname,',',lname) as Name, DATE_FORMAT(DOB,'%m-%d-%Y') AS DOB,DATE_FORMAT(FROM_DAYS(DATEDIFF(CURDATE(),DOB)), '%Y')+0 AS age,if (sex = 'Female' ,'F','M' ) as sex,street,city,state,country_code,postal_code,phone_home,phone_biz,phone_cell,contact_relationship , phone_contact,email from form_encounter fe  INNER JOIN patient_data p ON p.pid=fe.pid  INNER JOIN openemr_postcalendar_events o ON o.pc_pid = fe.pid "
//                                                                  . " AND o.pc_catid = fe.pc_catid AND fe.facility_id = o.pc_facility
//                                                                      AND o.pc_eventDate = DATE_FORMAT( fe.date,  '%Y-%m-%d' )where o.pc_aid =$id group by fe.pid";
                                      $sql12="SELECT $avail_field2 from form_encounter fe  INNER JOIN patient_data p ON p.pid=fe.pid  INNER JOIN openemr_postcalendar_events o ON o.pc_pid = fe.pid "
                                      . " AND o.pc_catid = fe.pc_catid AND fe.facility_id = o.pc_facility
                                          AND o.pc_eventDate = DATE_FORMAT( fe.date,  '%Y-%m-%d' )where o.pc_aid =$id AND p.pid= $patient group by fe.pid";
                              }
                                display_db_query($sql12,$provider,$patient);
                           }    
                        }
                    display_db_table($provider,$patient); ?>
                </div>
	        <script type="text/javascript" src="assets/js/jquery.min.js"></script>
		<script type="text/javascript" src="assets/js/owl.carousel.min.js"></script>
		<script type="text/javascript" src="assets/js/isotope.pkgd.min.js"></script>
		<script type="text/javascript" src="assets/js/wow.min.js"></script>
		<script type="text/javascript" src="assets/js/bootstrap.min.js"></script>

		<script>
      		new WOW().init();
		</script>

		
	</body>

</html>
