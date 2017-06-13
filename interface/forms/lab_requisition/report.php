<?php
// Copyright (C) 2005 Rod Roark <rod@sunsetsystems.com>
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.

include_once("../../globals.php");
include_once($GLOBALS["srcdir"] . "/api.inc");
include_once("lines.php");

function lab_requisition_report($pid, $encounter, $cols, $id)
{
 $cols = 1; // force always 1 column
 $count = 0;
 //$data = formFetch("tbl_form_facetoface", $id);
 
 $getLabRequisitionDetails=sqlStatement("SELECT * FROM tbl_form_lab_requisition WHERE id=$id AND pid=$pid AND encounter=$encounter");
 $data = sqlFetchArray($getLabRequisitionDetails);
 
 if ($data) {
  print "<table><tr>";
  foreach($data as $key => $value) {
   if ($key == "id" || $key == "pid" || $key == "user" || $key == "groupname" || $key == "authorized" || $key == "activity" || $key == "date" 
       || $key == "nurse_practitioner_signature" || $key == "nurse_practitioner_signature_date" || $key == "physician_signature" || $key == "printed_name" || $key == "printed_name_date"    
       || $value == "" || $value == "0000-00-00 00:00:00" || $value == "0000-00-00" ) {
       
    continue;
   }
   if ($value == "on") {
    $value = "yes";
   }
   
   if ($value == "Y") {
    $value = "Yes";
   }
   
   if ($value == "N") {
    $value = "No";
   }
   
   if($key=='created_by')
   {
		
        $getCreatorName=sqlStatement("SELECT CONCAT(fname,' ',lname) AS creator_name FROM users WHERE id='$value'");
        $resCreatorName=  sqlFetchArray($getCreatorName);
        $value=$resCreatorName['creator_name'];
		
   }
   if($key=='lab1' || $key=='lab2' || $key=='lab3' || $key=='lab4' ||$key=='lab5')
   {      
       $val_array=explode(';',$value);
       $value='<br><b>Name-</b> '.$val_array[0].'<br><b>Notes-</b>'.$val_array[1].'<br><b>Status-</b>'.$val_array[2];
   }   
  
   $key=ucwords(str_replace("_"," ",$key));
   print "<td><span class=bold>" . xl($key) . ": </span><span class=text>$value</span></td>";
   $count++;
   if ($count == $cols) {
    $count = 0;
    print "</tr><tr>\n";
   }
  }
 }
 print "</tr></table>";
 /*
    $cols = 1; // force always 1 column
 $count = 0;
$getLabRequisitionTestsDetails=sqlStatement("SELECT 
    CXR,CBC,UA,KUB,CMP,urine_culture,TSH,lipid_panel,PSA,HbAC,
mammogram,is_colonoscopy_required,did_patient_ARC
FROM tbl_form_lab_requisition_tests WHERE form_id=$id AND pid=$pid AND encounter=$encounter");
 $data_tests = sqlFetchArray($getLabRequisitionTestsDetails);
 
 if ($data_tests) {
  print "<br><table><tr>";
  foreach($data_tests as $key => $value) {
   if ($key == "id" || $key == "pid" || $key == "user" || $key == "groupname" || $key == "authorized" || $key == "activity" || $key == "date" || $value == "" || $value == "0000-00-00 00:00:00") {
    continue;
   }
   if ($value == "on") {
    $value = "yes";
   }
   $key=ucwords(str_replace("_"," ",$key));
   print "<td><span class=bold>" . xl($key) . ": </span><span class=text>$value</span></td>";
   $count++;
   if ($count == $cols) {
    $count = 0;
    print "</tr><tr>\n";
   }
  }
 }
 print "</tr></table>";
 */
   
}


?>