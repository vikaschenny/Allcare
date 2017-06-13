<?php
 // Copyright (C) 2011 Cassian LUP <cassi.lup@gmail.com>
 // 
 // Moved out of individual get_* portal functions for re-use by
 // Kevin Yeh (kevin.y@integralemr.com) May 2013
 //
 // This program is free software; you can redistribute it and/or
 // modify it under the terms of the GNU General Public License
 // as published by the Free Software Foundation; either version 2
 // of the License, or (at your option) any later version.
 // 
 // 
 
    // All of the common intialization steps for the get_* patient portal functions are now in this single include.

    //SANITIZE ALL ESCAPES
    $sanitize_all_escapes=true;

    //STOP FAKE REGISTER GLOBALS
    $fake_register_globals=false;

    //continue session
    session_start();

    //landing page definition -- where to go if something goes wrong 
    $landingpage = "index.php?site=".$_SESSION['site_id'];	
    //

    // kick out if patient not authenticated
    //if ( isset($_SESSION['uid']) && isset($_SESSION['patient_portal_onsite']) ) {
    if ( isset($_SESSION['portal_username']) ) {    
    $provider = $_SESSION['portal_username'];
    }
    else {
            session_destroy();
    header('Location: '.$landingpage.'&w');
            exit;
    }
    //

    $ignoreAuth=true; // ignore the standard authentication for a regular OpenEMR user

include_once('../../../interface/globals.php'); 

if($_REQUEST['action']=='deleterow' && $_REQUEST['delid']!=''){
    $delid=sqlStatement("delete from tbl_form_template_editvalues where transaction_id='".$_REQUEST['delid']."'");
    exit();
}
$formdata=$_REQUEST['data'];
$formtemp=$_REQUEST['formtemp'];

$data1=explode("&",$formdata);

$sql1=sqlStatement("select * from  list_options where list_id='form_templates' and option_id=$formtemp");
$row1=sqlFetchArray($sql1);
$form_template=$row1['title'];
$list_id=str_replace(" ","",$row1['title'])."Mapping";
$i=0;
$sql=sqlStatement("select * from list_options where list_id='$list_id' and notes='E'"); 
 while ($row=sqlFetchArray($sql)) {
     if(strpos($row['title'],'radio')!='false'){
          $nam=explode("_",$row['title']);
          $editable_fields[]=$nam[0];
     }else {
          $editable_fields[]=$row['title'];
     }
    
     if($row['codes']!=''){
         if(strpos($row['title'],'radio')!='false'){
              $nam2=explode("_",$row['title']);
              $db_fields[$row['codes']."$".$i]=$nam2[0];
         }else {
             $db_fields[$row['codes']."$".$i]=$row['title'];
         }
         
         $i++;
     }
 }
$transid=''; $patient_id=''; $coid='';
foreach($data1 as $key => $value) {
    $data2=explode("=",$value);
    if(in_array($data2[0],$editable_fields)){
        $edata[$data2[0]]=$data2[1];
    }else if($data2[0]=='transid'){
        $transid=$data2[1];
    }else if($data2[0]=='coid'){
        $coid=$data2[1];
    }else if($data2[0]=='patient_id') {
      echo  $patient_id=$data2[1];
    }
  
}
if($transid!=''){
    $rowid=$transid;
}else if($coid!=''){
    $rowid=$coid;
}
echo "<pre>"; print_r($db_fields); echo "</pre>";
echo "<pre>"; print_r($edata); echo "</pre>";
foreach($edata as $key => $value){
    $sql3=sqlStatement("select * from tbl_form_template_editvalues where pid=$patient_id and transaction_id=$rowid and form_name='".$form_template."' and form_field='".$key."'");
    $row3=sqlFetchArray($sql3);
    if(!empty($row3)){
        $update_sql=sqlStatement("update tbl_form_template_editvalues set form_value='".$value."' where transaction_id=$rowid and pid=$patient_id and form_name='".$form_template."' and form_field='".$key."'");
    }else {
        $ins_sql = sqlInsert("INSERT INTO tbl_form_template_editvalues (`date`,pid,transaction_id,form_name,form_field,form_value)values(now(),$patient_id,'".$rowid."','".$form_template."','".$key."','".$value."')");
    }
}
foreach($db_fields as $key => $value) {
//    $sql4=sqlStatement("select $value from $key where pid=$patient_id ");
//    $row4=sqlFetchArray($sql4);
        $table_name=explode("$",$key);
        
        foreach($edata as $key1 => $value1){
            if($value==$key1) {
               
                 $wh=explode("[",$table_name[0]);
                 if($wh[0]!='' && $wh[1]!=''){
                     $where_clause="pid=".$patient_id." AND ".rtrim($wh[1],"]");
                     $update_sql=sqlStatement("update $wh[0] set $key1='".$value1."' where  $where_clause");
                 }else if($wh[0]!='' && $wh[1]==''){
                      $update_sql=sqlStatement("update $table_name[0] set $key1='".$value1."' where  pid=$patient_id");
                 }
            }
        }
    
}
echo $rowid;
?>