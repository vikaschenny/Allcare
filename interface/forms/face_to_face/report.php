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

function face_to_face_report($pid, $encounter, $cols, $id) {
 $cols = 1; // force always 1 column
 $count = 0;
 //$data = formFetch("tbl_form_facetoface", $id);
 
 //$pri_diagnosis_id='';
 
 $pri_diagnosis_ids='';
 
 
 $getFaceToFacedetails=sqlStatement("SELECT * FROM tbl_form_facetoface WHERE id=$id AND pid=$pid AND encounter=$encounter");
 $data = sqlFetchArray($getFaceToFacedetails);
 
 if ($data) {
  print "<table><tr>";
  foreach($data as $key => $value) {
   if ($key == "id" || $key == "pid" || $key == "user" || $key == "groupname" || $key == "authorized" || $key == "activity" || $key == "date" 
  //     || $key == "nurse_practitioner_signature" || $key == "nurse_practitioner_signature_date" || $key == "physician_signature" || $key == "printed_name" || $key == "printed_name_date"    
       || $value == "" || $value == "0000-00-00 00:00:00" || $value == "0000-00-00") {
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
   
   if($key=='created_by' || $key=='updated_by')
   {
		
        $getCreatorName=sqlStatement("SELECT CONCAT(fname,' ',lname) as creator_name FROM users WHERE id='$value'");
        $resCreatorName=sqlFetchArray($getCreatorName);
        $value=$resCreatorName['creator_name'];
		
   }
   
   /*
   if($key=='pri_diagnosis_id')
   {
       $key='Primary_Diagnosis';
       $pri_diagnosis_id=$value;
		//$getDiagnosisValue=sqlStatement("SELECT title FROM list_options WHERE list_id='FaceToFace_Configuration_Diagnosis' AND option_id='$value' ");;
       $getDiagnosisValue=sqlStatement("SELECT title FROM list_options WHERE list_id='F2F_Config_Diagnosis_Primary' AND option_id='$value' ");;
       $resDiagnosis=sqlFetchArray($getDiagnosisValue);
       $value=($resDiagnosis['title']!='') ? $resDiagnosis['title'] : '-';              	       
   }    
    */
   
   if($key=='pri_diagnosis_ids')
   {
       $key='Primary_Diagnosis';
       $pri_diagnosis_id=$value;
		//$getDiagnosisValue=sqlStatement("SELECT title FROM list_options WHERE list_id='FaceToFace_Configuration_Diagnosis' AND option_id='$value' ");;
       //$getDiagnosisValue=sqlStatement("SELECT title FROM list_options WHERE list_id='F2F_Config_Diagnosis_Primary' AND option_id='$value' ");;
       
       //echo "<br>SELECT title FROM list_options WHERE list_id='F2F_Diagnosis_Categories' AND option_id IN($value) <br>";
       $getDiagnosisValue=sqlStatement("SELECT title FROM list_options WHERE list_id='F2F_Diagnosis_Categories' AND option_id IN($value) ");
       $primary_diagnosis_titles='';
       while($resDiagnosis=sqlFetchArray($getDiagnosisValue))
       {            
            if($resDiagnosis['title']!='')
            {
                $primary_diagnosis_titles.=$resDiagnosis['title'].",";
            }
       }
       
       $value=rtrim($primary_diagnosis_titles,",");
   }
   
   if($key=='sec_diagnosis_id')
   {
       $key='Secondary_Diagnosis';
       $list_id_name='';
       switch($pri_diagnosis_id)
       {
           case 1:break;
           case 2:$list_id_name='F2F_Config_Diagnosis_Pain';break;
           case 3:$list_id_name='F2F_Config_Diagnosis_Mental';break;
           default:break;
           
       }
       
       if($value>0)
       {
           $getDiagnosisValue=sqlStatement("SELECT title FROM list_options 
               WHERE list_id='$list_id_name' AND option_id='$value' ");
       
           $resDiagnosis=sqlFetchArray($getDiagnosisValue);
           $value=$resDiagnosis['title'];
       }
       else
       {
           $value='-';
       }
   }  
   
   if($key=='txt_sec_diagnosis')
   {
       $key='Secondary_Diagnosis_Text';
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
}
?>