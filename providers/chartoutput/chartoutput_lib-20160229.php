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
//require_once("{$GLOBALS['srcdir']}/sql.inc");

function getChartOutputById ($id, $group_name, $cols = "*")
{
    $labels = sqlStatement("select field_id from layout_options where group_name  = '$group_name' and form_id = 'CHARTOUTPUT'" );
    while ($labels2 = sqlFetchArray($labels)) {
        $titles .= $labels2['field_id'].',' ; 
        $columncheck .= $labels2['field_id']." <> '' OR ";
  }
  $titles2 = rtrim($titles ,',');
  $columncheck2 = rtrim($columncheck, ' OR ');
  return sqlQuery("select id,$titles2 from tbl_form_chartoutput_transactions where id=$id AND ($columncheck2 )" .
    "order by id DESC  limit 0,1");
}
//for who & notes
function getF2FById ($id,$cols = "*")
{
    $labels = sqlStatement("select field_id from layout_options where form_id = 'F2F' AND data_type!=36" );
    while ($labels2 = sqlFetchArray($labels)) {
        $titles .= $labels2['field_id'].',' ; 
        $columncheck .= $labels2['field_id']." <> '' OR ";
  }
  $titles2 = rtrim($titles ,',');
  $columncheck2 = rtrim($columncheck, ' OR ');
  return sqlQuery("select id,$titles2 from tbl_form_chartoutput_transactions where id=$id AND ($columncheck2 )" .
    "order by id DESC  limit 0,1");
}
function getChartOutputByPid ($pid, $group_name, $cols = "*")
{
  $labels = sqlStatement("select field_id from layout_options where group_name  = '$group_name' and form_id = 'CHARTOUTPUT'" );
  while ($labels2 = sqlFetchArray($labels)) {
        $titles .= $labels2['field_id'].',' ; 
         $columncheck .= $labels2['field_id']." <> '' OR ";
  }
  $titles2 = rtrim($titles ,',');
  $columncheck2 = rtrim($columncheck, ' OR ');
  //return $allstring;  
   
  //$all = '';
  if(!empty($group_name)){
//      if($pid == 0):
//          $pid = $_SESSION['pid'];
//      endif;
     
  $res = sqlStatement("select id,refer_to,provider,facility,pharmacy,payer,notes,$titles2 from tbl_form_chartoutput_transactions where pid=$pid AND ($columncheck2)
    order by id DESC ");
   
  for ($iter = 0; $row = sqlFetchArray($res); $iter++)
    $all[$iter] = $row;
  return $all;
  }
  
}

function newchartOutput($pid, $from_dos, $to_dos, $patientinfo )
{
  $body = mysql_escape_string($body);
  return sqlInsert("insert into tbl_form_chartoutput_transactions ( " .
    " pid, from_dos, to_dos,  patientinfo " .
    ") values ( " .
    "'$pid', '$from_dos', '$to_dos', '$patientinfo' " .
    ")");
}
?>