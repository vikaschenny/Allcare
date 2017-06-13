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
    include_once('../../interface/globals.php');  
    include_once("$srcdir/sql.inc");

function getF2FById ($id, $cols = "*")
{
  return sqlQuery("select $cols from tbl_form_facetoface_transactions where id=? " .
    "order by date_of_service DESC  limit 0,1", array($id) );
}

function getF2FByPid ($pid, $cols = "*")
{
  $res = sqlStatement("select ft.$cols,CONCAT(u.fname,' ',u.lname) AS refer_to from tbl_form_facetoface_transactions ft INNER JOIN users u on u.id=ft.refer_to where pid=? " .
    "order by date_of_service DESC ", array($pid) );
  for ($iter = 0; $row = sqlFetchArray($res); $iter++)
    $all[$iter] = $row;
  return $all;
}
function getF2FReportsByPid ($pid, $encounter,$cols = "*")
{
  $res = sqlStatement("select ft.$cols,CONCAT(u.fname,' ',u.lname) AS refer_to from tbl_form_facetoface_transactions ft INNER JOIN users u on u.id=ft.refer_to where pid=? AND encounter=?" .
   "order by date_of_service DESC", array($pid,$encounter) );
  for ($iter = 0; $row = sqlFetchArray($res); $iter++)
    $all[$iter] = $row;
  return $all;
}
function getF2FEncounterForm($form_id, $field_id ,$cols = "*")
{
   /* echo "select $cols from lbf_data where form_id=? AND field_id=? 
    , array($form_id,$field_id)";*/
  return sqlQuery("select $cols from lbf_data where form_id=? AND field_id=?" 
    , array($form_id,$field_id) );
}

function newF2F($pid, $date_of_service, $refer_to, $notes )
{
  $body = mysql_escape_string($body);
  return sqlInsert("insert into tbl_form_facetoface_transactions ( " .
    " pid, date_of_service, refer_to,  notes " .
    ") values ( " .
    "'$pid', '$date_of_service', '$refer_to', '$notes' " .
    ")");
}

function updateF2FForm($id, $new, $create)
{
   // echo "<pre>"; print_r($new); echo "</pre>";
    $formid=$new['form_id'];
  /*******************************************************************
    $real = getPatientData($pid);
    $new['DOB'] = fixDate($new['DOB']);
    while(list($key, $value) = each ($new))
        $real[$key] = $value;
    $real['date'] = "'+NOW()+'";
    $real['id'] = "";
    $sql = "insert into patient_data set ";
    while(list($key, $value) = each($real))
        $sql .= $key." = '$value', ";
    $sql = substr($sql, 0, -2);
    return sqlInsert($sql);
  *******************************************************************/

  // The above was broken, though seems intent to insert a new patient_data
  // row for each update.  A good idea, but nothing is doing that yet so
  // the code below does not yet attempt it.

  //$new['DOB'] = fixDate($new['DOB']);
   
    //for inserting log data
    $logdata= array();
        $data = sqlStatement("SELECT logdate from `tbl_allcare_formflag` WHERE  form_id=".$formid);
        while ($row = sqlFetchArray($data)) {
            $array =  unserialize($row['logdate']);
            $count= count($array);
        }
        $res = sqlStatement("SELECT * FROM `tbl_allcare_formflag` WHERE form_id = '$formid'");
        $row1 = sqlFetchArray($res);
        $count = isset($count)? $count: 0;
        $finalized='Y'; $pending='N';
  
  if ($create) {
    $sql = "INSERT INTO tbl_form_facetoface_transactions SET  created_date = NOW()";
    foreach ($new as $key => $value) {
      if ($key == 'id') continue;
      //$sql .= ", `$key` = " . pdValueOrNull($key, $value);
      $sql .= ", `$key` = " . "'$value'";
    }
    $db_id = sqlInsert($sql);
     $array2[] = array( 'authuser' =>$_SESSION["authUser"],'pending' => $pending,'finalized' => $finalized,'date' => date("Y/m/d"), 'action'=>'Created','count'=> $count+1,'formName'=>'F2F');
            $logdata = array_merge_recursive($array, $array2);
            $logdata= ($logdata? serialize($logdata): serialize($array2) );
            $query1 = "INSERT INTO tbl_allcare_formflag ( " .
                    "encounter_id,form_id, form_name,pending,finalized, logdate" .
                    ") VALUES ( " .
                    "".$new['encounter'].",".$new['form_id'].", 'Allcare Encounter Form', '$pending','$finalized', '".$logdata."' " .
                    ")";
    sqlInsert($query1); 
  }
  else {
   //echo $db_id = $new['id'];
     
    $sql = "UPDATE tbl_form_facetoface_transactions SET updated_date = NOW()";
    foreach ($new as $key => $value) {
      $sql .= ", `$key` = " . "'$value'";
     // $sql .= ", `$key` = " . pdValueOrNull($key, $value);
    }
    $sql .= " WHERE id = '$id'";
    sqlStatement($sql);
    $array2[] = array( 'authuser' =>$_SESSION["authUser"],'pending' => $pending,'finalized' => $finalized, 'date' => date("Y/m/d"), 'action' => 'Updated','ip_address'=>$ip_addr ,'count'=> $count+1);
                $logdata = array_merge_recursive($array, $array2);
                $logdata= ($logdata? serialize($logdata): serialize($array2) );
                $query2 = "INSERT INTO tbl_allcare_formflag ( " .
                "encounter_id,form_id, form_name,pending,finalized, logdate" .
                ") VALUES ( " .
                "".$new['encounter'].",".$new['form_id'].", 'Allcare Physical Exam','$pending', '$finalized', '".$logdata."' " .
                ")";
    sqlInsert($query2);
                
  }
  return $db_id;
}
?>