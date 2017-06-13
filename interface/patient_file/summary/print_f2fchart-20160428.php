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


?>
<script type='text/javascript' src='../../main/js/jquery-1.11.1.min.js'></script>
<?php  
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
   'electronically_signed_by'           => htmlspecialchars( xl('Electronically Signed by:')),
   'printed_name'                       => htmlspecialchars( xl('Printed Name:')),
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
// $date_of_service=$_GET['date_of_service'];
 $date_of_service= str_replace("_", " ",$_GET['date_of_service']);
 $id=$_REQUEST['f2fid'];
 $form_id=$_REQUEST['form_id'];
 $mode=$_REQUEST['mode'];
 
 if($mode=='print')
 {
      
      $res=sqlStatement("select field_id from lbf_data where field_id='f2f_printed' AND form_id='".$form_id."'");
      $row=sqlFetchArray($res);
      if($row['field_id']=='f2f_printed')
      {    
      $res1=sqlStatement("update lbf_data SET field_value='YES' where field_id='f2f_printed' AND form_id='".$form_id."'");
      $row1=sqlFetchArray($res);
      }
      else
      {
          $res2=sqlStatement("INSERT INTO lbf_data (form_id,field_id,field_value) VALUES (?,?,?)", array($form_id,'f2f_printed','YES'));
          $row2=sqlFetchArray($res);
      }
 }

 
$value=array();
$field_id1=array();
$fval = sqlStatement("SELECT l.field_id, lb.field_value
                FROM layout_options l
                INNER JOIN lbf_data lb ON lb.field_id = l.field_id
                INNER JOIN forms f ON f.form_id = lb.form_id
                INNER JOIN form_encounter fe ON fe.encounter = f.encounter
                INNER JOIN tbl_form_chartoutput_transactions ff ON ff.date_of_service=fe.date  and transaction=2
                WHERE l.form_id =  'LBF2' AND f.deleted=0
                AND uor >0
                AND l.field_id !=  ''
                AND group_name LIKE  '%Face to Face HH Plan%'
                AND fe.encounter =".$encounter_id."
                AND fe.pid =".$patient_id."
                AND f.formdir = 'LBF2'
                AND fe.date = '".$date_of_service."' AND lb.form_id IN(select form_id from lbf_data where field_id='f2f_stat' AND field_value='finalized') 
                ORDER BY seq");
  while ($frow1 = sqlFetchArray($fval)) {
    $field_id1[]  = $frow1['field_id'];
    $value[] = $frow1['field_value']; 
  }
  

//diagnosis code 
/*$getFeeSheetCodes="SELECT * FROM billing 
WHERE pid='$patient_id'
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
$pri_diagnosis_ids=implode($pri_diagnosis_id_array,",");*/
  
  
 $getFeeSheetCodes="SELECT  `diagnosis` 
            FROM  `lists` l
            INNER JOIN issue_encounter ie ON ie.list_id = l.id
            AND ie.pid = l.pid
            WHERE l.pid ='$patient_id'
            AND ie.encounter = '$encounter_id'
            AND type =  'medical_problem'
            AND enddate IS NULL "; // AND date(date)=date(now())
$resFeeSheetCodes=sqlStatement($getFeeSheetCodes);
$feesheet_codes_array=array();
while($rowFeeSheetCodes=sqlFetchArray($resFeeSheetCodes))
{
    array_push($feesheet_codes_array,str_replace('ICD9:','',$rowFeeSheetCodes["diagnosis"]));   
}
 // print_r($feesheet_codes_array);



$s = '';
$fh = fopen($template_file, 'r');
while (!feof($fh)) $s .= fread($fh, 8192);
fclose($fh);

//Date of Service
$sqlGetCurrentEncDate="SELECT DATE_FORMAT(date,'%m-%d-%Y') as DOS FROM form_encounter WHERE encounter='".$encounter_id."'";
    $resGetCurrentEncDate=sqlStatement($sqlGetCurrentEncDate);
    $current_enc_date=sqlFetchArray($resGetCurrentEncDate);
    $s= str_replace("{date1}", $current_enc_date['DOS'], $s);
    

//diagnosis code 
$code1=array();
$code=array();
foreach($feesheet_codes_array as $fscode)
{
    if($fscode!='')
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
    
}
//print_r($code1);
foreach($code as $diagnosiscode)
{
    $diag .=$diagnosiscode."<br>";
    
    
}

//print_r($diag);
$s = str_replace("{diagnosis_id}", $diag, $s);

//patient_name

$getPatientName=sqlStatement("SELECT title ,CONCAT(fname,' ',lname) AS pname ,sex,DATE_FORMAT(DOB,'%m-%d-%Y') as DOB FROM patient_data WHERE pid=".$patient_id."");
$resPatientName=sqlFetchArray($getPatientName);

$name=$resPatientName['title']." ".$resPatientName['pname'].", ".$resPatientName['sex']." , DOB: ".$resPatientName['DOB'];
$s= str_replace("{identification}",$name , $s);

//Support Service reason
//foreach($code1 as $ssr)
// {
//    if($ssr!='')
//    {
//        //echo "select notes ,title from list_options where list_id ='F2F_Clinical_Finding' AND codes LIKE '%$ssr%'";    
//         $ssr1=sqlStatement("select notes ,title from list_options where list_id ='F2F_Clinical_Finding' AND codes LIKE '%$ssr%'");
//         $res=sqlFetchArray($ssr1);
//         if($res['title']=='Pain')
//         {
//             $clinical1=$res['notes']; 
//         }
//         else if($res['title']=='Mental')
//         {
//             $clinical2=$res['notes'];
//         }
//    }
// }
// $clinical=$clinical1."<br>".$clinical2;
//$s = str_replace("{f2f_findings}", $clinical, $s);
 
 //Home bound reason
// foreach($code1 as $hbr)
// {
//     if($hbr!='')
//     {    
//         $hbr1=sqlStatement("select notes,title from list_options where list_id ='F2F_Homebound' AND codes LIKE '%$hbr%'");
//         $res=sqlFetchArray($hbr1);
//         //$home_bound.=$res['notes']."<br>";
//         if($res['title']=='Pain')
//         {
//             $home_bound1=$res['notes']; 
//         }
//         else if($res['title']=='Mental')
//         {
//             $home_bound2=$res['notes'];
//         }
//     }
// }
// $home_bound=$home_bound1."<br>".$home_bound2;
// $s = str_replace("{home_bound}", $home_bound, $s);
 
 //physician plan of care
 /*$care=sqlStatement("select notes from tbl_form_facetoface_transactions where pid='".$GLOBALS['pid']."' AND id='".$id."' AND encounter='".$encounter_id."' AND date_of_service='".$date_of_service."'");
 $res_care=sqlFetchArray($care);
 $notes=$res_care['notes'];
 $s = str_replace("{notes}", $notes, $s);*/
 
 //electrically signed by
 
 
//dynamically populating labels and values

 $f2f=array();
 $f2f = array_combine($field_id1, $value);
  // print_r($f2f);
    
//printed name and sign of physician
    foreach ($f2f as $key => $value) {  
          
          
           if($key=='f2f_np') 
           {    
                $getname="SELECT CONCAT(fname,' ',lname) AS uname FROM users
                WHERE id='$value'";
                $resName=sqlStatement($getname);
                $rowName=sqlFetchArray($resName);
                $np_name=$rowName['uname'];
                
              
                $getcred1="SELECT provider_credentials AS pcred FROM tbl_patientuser
                WHERE userid='$value' AND provider_credentials!=''"; 
                $resCred1=sqlStatement($getcred1);
                $rowCred1=sqlFetchArray($resCred1);
                $credential1= $rowCred1['pcred'];
                $np_cred1 = substr ($credential1, 0, 15);
                if(strlen($np_cred1) > 0) $np_cred1 = ", ".$np_cred1;
                if(strlen($credential1) > 15):
                    $np_cred="$np_cred1"."...";
                else:
                    $np_cred=$credential1;
                endif;
                
           } 
           if($key=='f2f_np_on')
           {
               $esign = explode(" ",$value);
               $esigned = '';
               if($esign[1] != '00:00' && $esign[1] != '')
                   $esigned = " at ".$esign[1];
               $date_sign_np= $esign[0].$esigned;
           }
            if($key=='f2f_ps') 
           {    
                $getname="SELECT CONCAT(fname,' ',lname) AS uname FROM users
                WHERE id='$value'";
                $resName=sqlStatement($getname);
                $rowName=sqlFetchArray($resName);
                $ps_name=$rowName['uname'];
                
                
                $getcred="SELECT provider_credentials AS pscred FROM tbl_patientuser
                WHERE userid='$value' AND provider_credentials!=''";
                $resCred=sqlStatement($getcred);
                $rowCred=sqlFetchArray($resCred);
                $credential=$rowCred['pscred'];
                $ps_cred1 = substr($credential, 0, 15);
                if(strlen($ps_cred1) > 0) $ps_cred1 = ", ".$ps_cred1;
                if(strlen($credential) > 15):
                    $ps_cred="$ps_cred1"."...";
                else:
                    $ps_cred=$credential;
                endif;
                
                
           } 
           if($key=='f2f_ps_on')
           {    
               $date_sign = '';
               $esign = explode(" ",$value);
               $esigned = '';
               if($esign[1] != '00:00' && $esign[1] != '')
                   $esigned = " at ".$esign[1];
               $date_sign =$esign[0].$esigned;
               
           }
    }
         $s = str_replace("{np_name}", $np_name, $s);
         $s = str_replace("{date_sign_np}", $date_sign_np, $s);
         $s = str_replace("{np_cred}", $np_cred, $s);
         $s = str_replace("{ps_name}", $ps_name, $s);
         $s = str_replace("{ps_cred}", $ps_cred, $s);
         $s = str_replace("{date_sign}", $date_sign , $s);
         
    //f2f report   datetimestamp
         $report_ts=sqlStatement("select created_date from tbl_form_chartoutput_transactions where date_of_service='".$date_of_service."'");
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
              
               $s = str_replace("{".$value."}", '', $s); 
        }
         foreach ($f2f as $key => $value) {  
             if($key =='f2f_med')
             {
                 $med_cond= str_replace( "|" , " , " ,$value);
                 $s = str_replace("{".$key."}",$med_cond, $s);

             }
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
            if($key =='f2f_med')
             {
                 $med_cond= str_replace( "|" , " , " ,$value);
                 $s = str_replace("{".$key."}",$med_cond, $s);

             }  
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

$sql7=sqlStatement("select * from tbl_form_chartoutput_transactions where  pid=$patient_id and date_of_service='$date_of_service' and transaction=2 order by id desc");
$sql_res=sqlFetchArray($sql7);

$who_type            = $sql_res['who_type'];
$form_notes          = $sql_res['notes'];
if($who_type=='provider'){
    $provider= $sql_res['provider'];
    $pro=sqlStatement("SELECT id, fname, lname, specialty,fax, email,street,city,phone,phonecell,phonew1 FROM users " .
            "WHERE active = 1 AND ( info IS NULL OR info NOT LIKE '%Inactive%' ) " .
            "AND authorized = 1 AND id=$provider " .
            "ORDER BY lname, fname");
    $pro_res=sqlFetchArray($pro);
   
   
    if(!empty($pro_res ['fname']) || !empty($pro_res ['lname']) || !empty($pro_res ['mname'])){
        $name=$pro_res ['fname']." ". $pro_res ['mname']." ".$pro_res ['lname'];
        $s = str_replace("{proname}", $name , $s);
    }
    if($pro_res['specialty']!='') {  
        $s = str_replace("{specialty}", $pro_res['specialty'] , $s); 
    } 
    if($pro_res['phone']!='') { 
        $s = str_replace("{pro_phone}", $pro_res['phone'] , $s); 
    } 
    if($pro_res['phonecell']!='') { 
        $s = str_replace("{pro_cell}", $pro_res['phonecell'] , $s); 
    } 
    if($pro_res['phonew1']!='') {
        $s = str_replace("{pro_phonew1}", $pro_res['phonew1'] , $s); 
    }
    if($pro_res['email']!='') { 
        $s = str_replace("{pro_email}", $pro_res['email'] , $s); 
    } 
    if($pro_res['fax']!='') {  
        $s = str_replace("{pro_fax}", $pro_res['email'] , $s); 
    } 
    if($pro_res['street']!='') { 
        $s = str_replace("{pro_street}", $pro_res['street'] , $s);
    } 
    if($pro_res['city']!='') { 
        $s = str_replace("{pro_street}", $pro_res['city'] , $s);
    } 
}else if($who_type=='facility'){
    $facility=$sql_res['facility'];
    $query =sqlStatement( "SELECT name,phone,fax,street, city,state,postal_code,country_code,email FROM facility where id=$facility ORDER BY name");
    $fres=sqlFetchArray($query);
    
    if($fres['name']!='')   {
        $s = str_replace("{fac_name}", $fres['name'] , $s);  
    }
    if($fres['phone']!='')  { 
        $s = str_replace("{fac_phone}", $fres['phone'] , $s);  
    } 
    if($fres['email']!='')  {
        $s = str_replace("{fac_email}", $fres['email'] , $s); 
    } 
    if($fres['fax']!='')    { 
        $s = str_replace("{fac_fax}", $fres['fax'] , $s);
    } 
    if($fres['street']!='') { 
        $s = str_replace("{fac_street}",$fres['street'] , $s); 
    } 
    if($fres['city']!='')   { 
        $s = str_replace("{fac_city}",$fres['city'] , $s);  
    } 
    if($fres['state']!='')  { 
        $s = str_replace("{fac_state}", $fres['state'], $s);
    } 
    if($fres['postal_code']!='') { 
        $s = str_replace("{fac_postal_code}", $fres['postal_code'], $s);  
    } 
    if($fres['country_code']!=''){  
        $s = str_replace("{fac_country_code}", $fres['country_code'], $s);  
    } 
   
}else if($who_type=='pharmacy') {
    $pharmacy=$sql_res['pharmacy'];
   
    $pres =sqlStatement("SELECT d.id, d.name, d.email,a.line1, a.city, a.state,a.zip,a.country, " .
             "p.area_code, p.prefix, p.number FROM pharmacies AS d " .
             "LEFT OUTER JOIN addresses AS a ON a.foreign_id = d.id " .
             "LEFT OUTER JOIN phone_numbers AS p ON p.foreign_id = d.id " .
             "AND p.type = 2 where d.id=$pharmacy " .
             "ORDER BY name, area_code, prefix, number");
    $phres=sqlFetchArray($pres);
    
    if($phres['name']!='')      $s = str_replace("{phr_name}", $phres['name'] , $s); 
    if($phres['email']!='')     $s = str_replace("{phr_email}", $phres['email'] , $s); 
    if($phres['city']!='')      $s = str_replace("{phr_city}", $phres['city'] , $s); 
    if($phres['state']!='')     $s = str_replace("{phr_state}", $phres['state'] , $s);  
    if($phres['zip']!='')       $s = str_replace("{phr_zip}", $phres['zip'] , $s);   
    if($phres['country']!='')   $s = str_replace("{phr_country}", $phres['country'] , $s);  
    
}else if($who_type=='payer') {
    $payer=$sql_res['payer'];
    $query1 = sqlStatement("SELECT id, name ,x12_default_partner_id FROM insurance_companies where id=$payer");
    $ires = sqlFetchArray($query1);
    if($ires['name']!='') { $s = str_replace("{payer_name}", $ires['name'] , $s); }
    if($ires['x12_default_partner_id']!='') {  
        $query2 = sqlStatement("SELECT name FROM x12_partners where id='".$ires['x12_default_partner_id']."' ");
        $xres = sqlFetchArray($query2);
        $s = str_replace("{payer_partner}", $xres['name'] , $s);  
    } 
   
}else if($who_type!='' && $who_type!='0'){
    $org=$sql_res['refer_to'];
    $addressbook_data    = sqlStatement("SELECT abook_type,organization,assistant,title,fname,lname,mname,phone,phonecell,phonew1,phonew2,fax,email,street,city FROM users WHERE id = '$org'");
    $addressbook_info    = sqlFetchArray($addressbook_data);

    $type_sql_row = sqlQuery("SELECT `title` FROM `list_options` WHERE `list_id` = 'abook_type' AND `option_id` = '". $addressbook_info ['abook_type']."'");  
    if(!empty($type_sql_row)){
        $s = str_replace("{addr_title}", $type_sql_row['title'] , $s);
    }
    if(!empty($addressbook_info ['organization'])){ 
        $s = str_replace("{addr_org}", $addressbook_info ['organization'] , $s);
    }
    if(!empty($addressbook_info ['fname']) || !empty($addressbook_info ['lname']) || !empty($addressbook_info ['mname'])){
        $name=$addressbook_info ['fname']." ". $addressbook_info ['mname']." ".$addressbook_info ['lname'];
        $s = str_replace("{name}", $name , $s);
    }
    if(!empty($addressbook_info ['phone'])){
        $s = str_replace("{phone}", $addressbook_info ['phone'] , $s);
    }
    if(!empty($addressbook_info ['phonecell'])){
        $s = str_replace("{phonecell}", $addressbook_info ['phonecell'] , $s);
    }
    if(!empty($addressbook_info ['phonew1'])) {
        $s = str_replace("{phonew1}", $addressbook_info ['phonew1'] , $s);
    }
    if(!empty($addressbook_info ['email'])){
        $s = str_replace("{email}", $addressbook_info ['email'] , $s);
    }
    if(!empty($addressbook_info ['fax'])){
        $s = str_replace("{fax}", $addressbook_info ['fax'] , $s);
    }
    if(!empty($addressbook_info ['street'])){
        $s = str_replace("{street}", $addressbook_info ['street'] , $s);
    }
    if(!empty($addressbook_info ['city'])) {
        $s = str_replace("{city}", $addressbook_info ['city'] , $s);
    }
    if(!empty($addressbook_info ['fax'])){
        $s = str_replace("{faxno}", $addressbook_info ['fax'] , $s);
    }

}
if(!empty($form_notes)){
    $s = str_replace("{fnotes}", $form_notes , $s);
}
echo $s;

//for TO section in print
if($who_type=='provider'){
    echo "<script type='text/javascript'>document.getElementById('provider').style.display='block';</script>";
    echo "<script type='text/javascript'>document.getElementById('addr_bk').style.display='none';</script>";
    echo "<script type='text/javascript'>document.getElementById('facility').style.display='none';</script>";
    echo "<script type='text/javascript'>document.getElementById('pharmacy').style.display='none';</script>";
    echo "<script type='text/javascript'>document.getElementById('payer').style.display='none';</script>"; 
    
    if(!empty($pro_res ['fname']) || !empty($pro_res ['lname']) || !empty($pro_res ['mname'])){
       
         echo "<script type='text/javascript'>document.getElementById('proname').style.display='block';</script>";
    }
    if($pro_res['specialty']!='') {  
       
         echo "<script type='text/javascript'>document.getElementById('specialty').style.display='block';</script>";
    } 
    if($pro_res['phone']!='') { 
        
         echo "<script type='text/javascript'>document.getElementById('pro_phone').style.display='block';</script>";
    } 
    if($pro_res['phonecell']!='') { 
        
         echo "<script type='text/javascript'>document.getElementById('pro_cell').style.display='block';</script>";
    } 
    if($pro_res['phonew1']!='') {
        
         echo "<script type='text/javascript'>document.getElementById('pro_phonew1').style.display='block';</script>";
    }
    if($pro_res['email']!='') { 
        
         echo "<script type='text/javascript'>document.getElementById('pro_email').style.display='block';</script>";
    } 
    if($pro_res['fax']!='') {  
       
         echo "<script type='text/javascript'>document.getElementById('pro_fax').style.display='block';</script>";
    } 
    if($pro_res['street']!='') { 
       
         echo "<script type='text/javascript'>document.getElementById('pro_street').style.display='block';</script>";
    } 
    if($pro_res['city']!='') { 
       
        echo "<script type='text/javascript'>document.getElementById('pro_city').style.display='block';</script>";
    } 
}else if($who_type=='facility'){
    echo "<script type='text/javascript'>document.getElementById('facility').style.display='block';</script>";
    echo "<script type='text/javascript'>document.getElementById('provider').style.display='none';</script>";
    echo "<script type='text/javascript'>document.getElementById('addr_bk').style.display='none';</script>";
    echo "<script type='text/javascript'>document.getElementById('pharmacy').style.display='none';</script>";
    echo "<script type='text/javascript'>document.getElementById('payer').style.display='none';</script>";
      
    if($fres['name']!='') 
        echo "<script type='text/javascript'>document.getElementById('fac_name').style.display='block';</script>";
    if($fres['phone']!='')
        echo "<script type='text/javascript'>document.getElementById('fac_phone').style.display='block';</script>";
    if($fres['email']!='')
        echo "<script type='text/javascript'>document.getElementById('fac_email').style.display='block';</script>";
    if($fres['fax']!='') 
        echo "<script type='text/javascript'>document.getElementById('fac_fax').style.display='block';</script>";
    if($fres['street']!='')
        echo "<script type='text/javascript'>document.getElementById('fac_street').style.display='block';</script>";
    if($fres['city']!='')
        echo "<script type='text/javascript'>document.getElementById('fac_city').style.display='block';</script>";
    if($fres['state']!='')
       echo "<script type='text/javascript'>document.getElementById('fac_state').style.display='block';</script>"; 
    if($fres['postal_code']!='')  
       echo "<script type='text/javascript'>document.getElementById('postal_code').style.display='block';</script>";
    if($fres['country_code']!='')  
        echo "<script type='text/javascript'>document.getElementById('country_code').style.display='block';</script>";
     
}else if($who_type=='pharmacy') {
    echo "<script type='text/javascript'>document.getElementById('pharmacy').style.display='block';</script>";
    echo "<script type='text/javascript'>document.getElementById('facility').style.display='none';</script>";
    echo "<script type='text/javascript'>document.getElementById('provider').style.display='none';</script>";
    echo "<script type='text/javascript'>document.getElementById('addr_bk').style.display='none';</script>";
    echo "<script type='text/javascript'>document.getElementById('payer').style.display='none';</script>";
    
    if($phres['name']!='')      echo "<script type='text/javascript'>document.getElementById('phr_name').style.display='block';</script>";
    if($phres['email']!='')     echo "<script type='text/javascript'>document.getElementById('phr_email').style.display='block';</script>";
    if($phres['city']!='')      echo "<script type='text/javascript'>document.getElementById('phr_city').style.display='block';</script>";
    if($phres['state']!='')     echo "<script type='text/javascript'>document.getElementById('phr_state').style.display='block';</script>";
    if($phres['zip']!='')       echo "<script type='text/javascript'>document.getElementById('phr_zip').style.display='block';</script>";
    if($phres['country']!='')   echo "<script type='text/javascript'>document.getElementById('phr_country').style.display='block';</script>";
    
}else if($who_type=='payer') {
   
    echo "<script type='text/javascript'>document.getElementById('payer').style.display='block';</script>";
    echo "<script type='text/javascript'>document.getElementById('pharmacy').style.display='none';</script>";
    echo "<script type='text/javascript'>document.getElementById('facility').style.display='none';</script>";
    echo "<script type='text/javascript'>document.getElementById('provider').style.display='none';</script>";
    echo "<script type='text/javascript'>document.getElementById('addr_bk').style.display='none';</script>";
    if($ires['name']!='') {  echo "<script type='text/javascript'>document.getElementById('payer_name').style.display='block';</script>"; }
    if($ires['x12_default_partner_id']!='') {  
       echo "<script type='text/javascript'>document.getElementById('payer_partner').style.display='block';</script>";
      
    } 
   
}else if($who_type!='' && $who_type!='0'){
    if(!empty($type_sql_row)){
        echo "<script type='text/javascript'>document.getElementById('tit').style.display='block';</script>";
    }
    if(!empty($addressbook_info ['organization'])){ 
        echo "<script>document.getElementById('org').style.display='block'</script>";
    }
    if(!empty($addressbook_info ['fname']) || !empty($addressbook_info ['lname']) || !empty($addressbook_info ['mname'])){
        echo "<script>document.getElementById('nam').style.display='block'</script>";
    }
    if(!empty($addressbook_info ['phone'])){
        echo "<script>document.getElementById('hph').style.display='block'</script>";
    }
    if(!empty($addressbook_info ['phonecell'])){
        echo "<script>document.getElementById('mob').style.display='block'</script>";
    }
    if(!empty($addressbook_info ['phonew1'])) {
        echo "<script>document.getElementById('wkph').style.display='block'</script>";
    }
    if(!empty($addressbook_info ['email'])){
        echo "<script>document.getElementById('em').style.display='block'</script>";
    }
    if(!empty($addressbook_info ['fax'])){
        echo "<script>document.getElementById('fax').style.display='block'</script>";
    }
    if(!empty($addressbook_info ['street'])){
        echo "<script>document.getElementById('st').style.display='block'</script>";
    }
    if(!empty($addressbook_info ['city'])) {
        echo "<script>document.getElementById('city').style.display='block'</script>";
    }
    if(!empty($addressbook_info ['fax'])){
        echo "<script>document.getElementById('faxno').style.display='block'</script>";
         echo "<script>document.getElementById('faxno1').style.display='block'</script>";
    }

}
if(!empty($form_notes)){
    echo "<script>document.getElementById('notes').style.display='block'</script>";
    echo "<script>document.getElementById('notes1').style.display='block'</script>";
}
?>
