<?php
// Copyright (C) 2008-2010 Rod Roark <rod@sunsetsystems.com>
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//SANITIZE ALL ESCAPES
$sanitize_all_escapes=true;
//
//STOP FAKE REGISTER GLOBALS
$fake_register_globals=false;
//
include_once("../../globals.php");
//require_once("$srcdir/transactions.inc");
require_once("$srcdir/options.inc.php");
include_once("$srcdir/patient.inc");
$template_file = $GLOBALS['OE_SITE_DIR'] . "/facetoface_template.html";

//require_once("$srcdir/acl.inc");
//require_once("$srcdir/formdata.inc.php");
require_once("$srcdir/globals.inc.php");
//require_once("$srcdir/user.inc");
require_once("$srcdir/classes/CouchDB.class.php");


 $field_id=array();
 $title=array();
 $fres = sqlStatement("SELECT * FROM layout_options " .
    "WHERE form_id = 'LBF2' AND uor > 0 AND field_id != '' AND group_name ='7Face to Face HH Plan'" .
    "ORDER BY  seq");
  while ($frow = sqlFetchArray($fres)) {
    $field_id[]  = $frow['field_id'];
    $title[] = $frow['title'];
    $seq[]=$frow['seq'];
  }
  //to diagnosis code statically
  /*array_push($seq,diagnosis_code);
  array_push($seq,patient_name);
  array_push($title,'diagnosis_code');
  array_push($title,'Patient name and Identification');*/
  $templates=array();
  $templates = array_combine($seq, $title);
   
 
 $TEMPLATE_LABELS = array(   
   'patient_name and identification'    => htmlspecialchars( xl('Patient name and Identification:')), 
   'diagnosis_code'                     => htmlspecialchars( xl('Diagnosis Code:')),   
   'date1'                              => htmlspecialchars( xl('')), 
   'electronically_signed_by'          => htmlspecialchars( xl('Electronically Signed by:')),
   'printed_name'                      => htmlspecialchars( xl('Printed Name:')),
   'date_of_sign'                       => htmlspecialchars( xl('Date of Signature:')),
   );

$mode=1;
$pri_diagnosis_id=$_POST['pri_diagnosis_id'];
$resCheckIfExists=array();
//print_r($_GET);
//print_r($_SESSION);
//print_r($_POST);
 $patient_id=$_GET['patient_id'];
 $encounter_id=$_REQUEST['encounter_id'];
 $user_id=$_SESSION['authUserID'];
 $date_of_service=$_GET['date_of_service'];
 
//$e_id=$_GET['id'];
$id=$_REQUEST['f2fid'];

 
$value=array();
$field_id1=array();
$fval = sqlStatement("SELECT l.field_id, lb.field_value
                FROM layout_options l
                INNER JOIN lbf_data lb ON lb.field_id = l.field_id
                INNER JOIN forms f ON f.form_id = lb.form_id
                INNER JOIN form_encounter fe ON fe.encounter = f.encounter
                INNER JOIN tbl_form_facetoface_transactions ff ON ff.date_of_service=f.date
                WHERE l.form_id =  'LBF2'
                AND uor >0
                AND l.field_id !=  ''
                AND group_name =  '7Face to Face HH Plan'
                AND fe.encounter =".$encounter_id."
                AND f.pid =".$patient_id."
                AND f.date = '".$date_of_service."' AND lb.form_id IN(select form_id from lbf_data where field_id='f2f_stat' AND field_value='finalized') 
                ORDER BY seq");
  while ($frow1 = sqlFetchArray($fval)) {
    $field_id1[]  = $frow1['field_id'];
    $value[] = $frow1['field_value']; 
  }
  

//diagnosis code 
$getFeeSheetCodes="SELECT * FROM billing 
WHERE pid='$patient_id'
AND user='$user_id'
AND encounter='$encounter_id'
AND code_type IN ('ICD9','ICD10') 
AND authorized='1'
AND activity='1'"; // AND date(date)=date(now())
$resFeeSheetCodes=sqlStatement($getFeeSheetCodes);
$feesheet_codes_array=array();
while($rowFeeSheetCodes=sqlFetchArray($resFeeSheetCodes))
{
    array_push($feesheet_codes_array,$rowFeeSheetCodes['code']);   
}
$getCodeCategory="SELECT option_id,title,notes,codes
                  FROM list_options 
                  WHERE list_id='F2F_Diagnosis_Categories'";
$resCodeCategory=sqlStatement($getCodeCategory);
$f2f_codes_array=array();
$pri_diagnosis_id_array=array();
$sec_diagnosis_array=array();
while($rowCodeCategory=sqlFetchArray($resCodeCategory))
{
    $f2f_codes_array=explode(';',$rowCodeCategory['codes']);
    foreach($feesheet_codes_array as $feesheet_code)
    {
        if(in_array($feesheet_code,$f2f_codes_array))
        {
            if(!in_array($rowCodeCategory['option_id'],$pri_diagnosis_id_array))
            {
                array_push($pri_diagnosis_id_array,$rowCodeCategory['option_id']);
            }
	    array_push($sec_diagnosis_array,$feesheet_code);
        }
    }
}
$pri_diagnosis_ids=implode($pri_diagnosis_id_array,",");



$s = '';
$fh = fopen($template_file, 'r');
while (!feof($fh)) $s .= fread($fh, 8192);
fclose($fh);

//Date of Service
$sqlGetCurrentEncDate="SELECT DATE(`date`) AS DOS FROM form_encounter WHERE encounter='".$encounter_id."'";
    $resGetCurrentEncDate=sqlStatement($sqlGetCurrentEncDate);
    $current_enc_date=sqlFetchArray($resGetCurrentEncDate);
    $s= str_replace("{date1}", $current_enc_date['DOS'], $s);
    

//diagnosis code 
$code1=array();
$code=array();
foreach($feesheet_codes_array as $fscode)
{
    
    $getCodeDesc="SELECT dx_code,formatted_dx_code,short_desc,long_desc FROM icd9_dx_code 
                  WHERE formatted_dx_code='$fscode'";
    $resCodeDesc=sqlStatement($getCodeDesc);  
    $rowCodeDesc=sqlFetchArray($resCodeDesc);       
    $longdesc_text=$rowCodeDesc['long_desc'];
    //echo $fscode."==".$longdesc_text;
    $code1[]=$fscode;
    $code[]= $fscode." - ".$longdesc_text;    
    
}
foreach($code as $diagnosiscode)
{
    $diag .=$diagnosiscode."<br>";
    
    
}

//print_r($diag);
$s = str_replace("{diagnosis_id}", $diag, $s);

//patient_name

$getPatientName=sqlStatement("SELECT title ,CONCAT(fname,' ',lname) AS pname ,sex,DOB FROM patient_data WHERE pid=".$GLOBALS['pid']."");
$resPatientName=sqlFetchArray($getPatientName);
$name=$resPatientName['title']." ".$resPatientName['pname'].", ".$resPatientName['sex']." ,".$resPatientName['DOB'];
$s= str_replace("{identification}",$name , $s);

//Support Service reason
 foreach($code1 as $ssr)
 {
 //echo "select notes from list_options where list_id ='F2F_Clinical_Finding' AND codes LIKE '$ssr'";    
 $ssr1=sqlStatement("select notes from list_options where list_id ='F2F_Clinical_Finding' AND codes LIKE '%$ssr%'");
 $res=sqlFetchArray($ssr1);
 $clinical.=$res['notes']."<br>";
 }
 //$s = str_replace("{f2f_findings}", $clinical, $s);
 
 //Home bound reason
 foreach($code1 as $hbr)
 {
 $hbr1=sqlStatement("select notes from list_options where list_id ='F2F_Homebound' AND codes LIKE '%$hbr%'");
 $res=sqlFetchArray($hbr1);
 $home_bound.=$res['notes']."<br>";
 }
 $s = str_replace("{home_bound}", $home_bound, $s);
 
 //physician plan of care
 $care=sqlStatement("select notes from tbl_form_facetoface_transactions where pid='".$GLOBALS['pid']."' AND id='".$id."' AND encounter='".$encounter_id."' AND date_of_service='".$date_of_service."'");
 $res_care=sqlFetchArray($care);
 $notes=$res_care['notes'];
 $s = str_replace("{notes}", $notes, $s);
 
 //electrically signed by
 
 
//dynamically populating labels and values

 $f2f=array();
 $f2f = array_combine($field_id1, $value);
   // print_r($f2f);
    
//printed name and sign of physician
    foreach ($f2f as $key => $value) {  
          
           if($key=='f2f_ps') 
           {    
                $getname="SELECT CONCAT(fname,' ',lname) AS uname FROM users
                WHERE id='$value'";
                $resName=sqlStatement($getname);
                $rowName=sqlFetchArray($resName);
                $ps_name=$rowName['uname'];
           } 
           if($key=='f2f_ps_on')
           {
               $date_sign=$value;
           }
    }
         
         $s = str_replace("{ps_name}", $ps_name, $s);
         $s = str_replace("{date_sign}", $date_sign, $s);
         
    //f2f report   datetimestamp
         $report_ts=sqlStatement("select created_date from tbl_form_facetoface_transactions where date_of_service='".$date_of_service."'");
         $row_ts=sqlFetchArray($report_ts);
         $ts=$row_ts['created_date'];
         $s = str_replace("{time_stamp}", $ts, $s);
         
         //print_r($field_id);
         // print_r($field_id1);
    
    //if any field is not filled then it is replaced with space
    if($res1=array_diff($field_id,$field_id1))
    {    
          foreach ($res1 as $value) {
              if($value=='f2f_findings')
              {
                  $s = str_replace("{".$value."}", $clinical, $s);
              }
            else {
                 $s = str_replace("{".$value."}", '', $s); 
             }
        }
         foreach ($f2f as $key => $value) {  
           if($key=='f2f_np' || $key=='f2f_ps') 
           {   
                $getSignImg="SELECT signature_image FROM users
                WHERE id='$value'";
                $resSignImg=sqlStatement($getSignImg);
                $rowSignImg=sqlFetchArray($resSignImg);
                $newval="../../pic/user_sign/".$rowSignImg['signature_image'];
               // f2f['f2f_np']=$newval;
                $s = str_replace("{".$key."}", $newval, $s);
                
           } 
           else {
                  $s = str_replace("{".$key."}", $value, $s);
             }
           
        }
    }
    else 
    {
         foreach ($f2f as $key => $value) {
           if($key=='f2f_np' || $key=='f2f_ps') 
           {   
                $getSignImg="SELECT signature_image FROM users
                WHERE id='$value'";
                $resSignImg=sqlStatement($getSignImg);
                $rowSignImg=sqlFetchArray($resSignImg);
                $newval="../../pic/user_sign/".$rowSignImg['signature_image'];
               // $f2f['f2f_np']=$newval;
                
               
                $s = str_replace("{".$key."}", $newval, $s);
           } 
           else {
                  $s = str_replace("{".$key."}", $value, $s);
             }
           
        }
    }

$logo="../../../images/tphc.jpg";
$s = str_replace("{logo}", $logo, $s);

foreach ($templates as $key => $value) {
  $s = str_replace("{".$key."}", $value.":", $s);
}
foreach ($TEMPLATE_LABELS as $key => $value) {
  $s = str_replace("{".$key."}", $value, $s);
}


echo $s;

?>
