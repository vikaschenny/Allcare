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
include_once($GLOBALS["srcdir"]."/api.inc");
include_once ($GLOBALS['fileroot']."/library/patient.inc");


function US_weight($pounds,$mode=1)
{

    if($mode==1)
    {
        return $pounds . " " . xl('lb') ;
    }
    else
    {
        $pounds_int=floor($pounds);
        $ounces=round(($pounds-$pounds_int)*16);
        return $pounds_int . " " . xl('lb') . " " . $ounces . " " . xl('oz');
    }
}

function vitals_report( $pid, $encounter, $cols, $id, $print = true) {
  $count = 0;
  $data = formFetch("form_vitals", $id);
  $patient_data = getPatientData($GLOBALS['pid']);
  $patient_age = getPatientAge($patient_data['DOB']);

  $vitals="";
  if ($data) {
    $vitals .= "<table><tr>";

    foreach($data as $key => $value) {

      if ($key == "id" || $key == "pid" || 
          $key == "user" || $key == "groupname" || 
          $key == "authorized" || $key == "activity" || 
          $key == "date" || $value == "" || 
          $value == "0000-00-00 00:00:00" || $value == "0.0" )
      {
        // skip certain data
        continue;
      }

      if ($value == "on") { $value = "yes"; } 

      $key = ucwords(str_replace("_"," ",$key));

      //modified by BM 06-2009 for required translation
      if ($key == "Temp Method" || $key == "BMI Status") { 
        if ($key == "BMI Status") {
          if ($patient_age <= 20 || (preg_match('/month/', $patient_age))) { 
            $value = "See Growth-Chart"; 
          }
        }
        $vitals .= "<td><span class=bold>" . xl($key) . ": </span><span class=text>" . xl($value) . "</span></td>"; 
      } 
      elseif ($key == "Bps") {
        $bps = $value;
        if ($bpd) {
          $vitals .= "<td><span class=bold>" . xl('Blood Pressure') . ": </span><span class=text>" . $bps . "/". $bpd  . "</span></td>";
        }
        else {
          continue;   
        }
      }
      elseif ($key == "Bpd") {
        $bpd = $value;
        if ($bps) {
          $vitals .= "<td><span class=bold>" . xl('Blood Pressure') . ": </span><span class=text>" . $bps . "/". $bpd  . "</span></td>";
        }
        else {
          continue;   
        }
      }
      elseif ($key == "Weight") {
        $convValue = number_format($value*0.45359237,2);
        $vitals.="<td><span class=bold>" . xl($key) . ": </span><span class=text>";
        // show appropriate units
        $mode=$GLOBALS['us_weight_format'];
        if ($GLOBALS['units_of_measurement'] == 2) {
          $vitals .=  $convValue . " " . xl('kg') . " (" . US_weight($value,$mode) . ")";
        }
        elseif ($GLOBALS['units_of_measurement'] == 3) {
          $vitals .=  US_weight($value,$mode) ;
        }
        elseif ($GLOBALS['units_of_measurement'] == 4) {
          $vitals .= $convValue . " " . xl('kg') ;
        }
        else { // = 1 or not set
          $vitals .= US_weight($value,$mode) . " (" . $convValue . " " . xl('kg')  . ")";
        }
        $vitals.= "</span></td>";
      }
      elseif ($key == "Height" || $key == "Waist Circ"  || $key == "Head Circ") {
        $convValue = number_format($value*2.54,2);
        // show appropriate units
        if ($GLOBALS['units_of_measurement'] == 2) { 
          $vitals .= "<td><span class=bold>" . xl($key) . ": </span><span class=text>" . $convValue . " " . xl('cm') . " (" . $value . " " . xl('in')  . ")</span></td>";
        }
        elseif ($GLOBALS['units_of_measurement'] == 3) {
          $vitals .= "<td><span class=bold>" . xl($key) . ": </span><span class=text>" . $value . " " . xl('in') . "</span></td>";
        }
        elseif ($GLOBALS['units_of_measurement'] == 4) {
          $vitals .= "<td><span class=bold>" . xl($key) . ": </span><span class=text>" . $convValue . " " . xl('cm') . "</span></td>";
        }
        else { // = 1 or not set
          $vitals .= "<td><span class=bold>" . xl($key) . ": </span><span class=text>" . $value . " " . xl('in') . " (" . $convValue . " " . xl('cm')  . ")</span></td>";
        }
      }
      elseif ($key == "Temperature") {
        $convValue = number_format((($value-32)*0.5556),2);
        // show appropriate units
        if ($GLOBALS['units_of_measurement'] == 2) {
          $vitals .= "<td><span class=bold>" . xl($key) . ": </span><span class=text>" . $convValue . " " . xl('C') . " (" . $value . " " . xl('F')  . ")</span></td>";
        }
        elseif ($GLOBALS['units_of_measurement'] == 3) {
          $vitals .= "<td><span class=bold>" . xl($key) . ": </span><span class=text>" . $value . " " . xl('F') . "</span></td>";
        }
        elseif ($GLOBALS['units_of_measurement'] == 4) {
          $vitals .= "<td><span class=bold>" . xl($key) . ": </span><span class=text>" . $convValue . " " . xl('C') . "</span></td>";
        }
        else { // = 1 or not set
          $vitals .= "<td><span class=bold>" . xl($key) . ": </span><span class=text>" . $value . " " . xl('F') . " (" . $convValue . " " . xl('C')  . ")</span></td>";
        }
      }

      elseif ($key == "Pulse" || $key == "Respiration"  || $key == "Oxygen Saturation" || $key == "BMI") {
        $value = number_format($value,0);
        if ($key == "Oxygen Saturation") {
          $vitals .= "<td><span class=bold>" . xl($key) . ": </span><span class=text>" . $value . " " . xl('%') . "</span></td>";
        }
        elseif ($key == "BMI") {
          $vitals .= "<td><span class=bold>" . xl($key) . ": </span><span class=text>" . $value . " " . xl('kg/m^2') . "</span></td>";
        }
        else { //pulse and respirations
          $vitals .= "<td><span class=bold>" . xl($key) . ": </span><span class=text>" . $value . " " . xl('per min') . "</span></td>";
        }
      }
      else { 
        $vitals .= "<td><span class=bold>" . xl($key) . ": </span><span class=text>$value</span></td>"; 
      } 

      $count++;

      if ($count == $cols) {
          $count = 0;
          $vitals .= "</tr><tr>\n";
      }
    }

    $vitals .= "</tr></table>";
  }
  if ($print) echo $vitals ;
  else return $vitals;
}
?>
