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
include_once($GLOBALS["srcdir"] . "/api.inc");
include_once("lines.php");

function allcare_physical_exam_report($pid, $encounter, $cols, $id) {
 global $pelines;

 $rows = array();
 $res = sqlStatement("SELECT * FROM tbl_form_physical_exam WHERE forms_id = '$id'");
 while ($row = sqlFetchArray($res)) {
  $rows[$row['line_id']] = $row;
 }

 echo "<table cellpadding='0' cellspacing='0'>\n";

 foreach ($pelines as $sysname => $sysarray) {
  $sysnamedisp = xl($sysname);
  foreach ($sysarray as $line_id => $description) {
   $linedbrow = $rows[$line_id];
   if (!($linedbrow['wnl'] || $linedbrow['abn'] || $linedbrow['diagnosis'] ||
    $linedbrow['comments'])) continue;
   if ($sysname != '*') { // observation line
    echo " <tr>\n";
    echo "  <td class='text' align='center'>" . ($linedbrow['wnl'] ? "WNL" : "") . "&nbsp;&nbsp;</td>\n";
    echo "  <td class='text' align='center'>" . ($linedbrow['abn'] ? "ABN1" : "") . "&nbsp;&nbsp;</td>\n";
    echo "  <td class='text' nowrap>$sysnamedisp&nbsp;&nbsp;</td>\n";
    echo "  <td class='text' wrap>$description&nbsp;&nbsp;</td>\n";
    echo "  <td class='text'>" . $linedbrow['diagnosis'] . "&nbsp;&nbsp;</td>\n";
    echo "  <td class='text'>" . htmlentities($linedbrow['comments']) . "</td>\n";
    echo " </tr>\n";
   } else { // treatment line
    echo " <tr>\n";
    echo "  <td class='text' align='center'>" . ($linedbrow['wnl'] ? "Y" : "") . "&nbsp;&nbsp;</td>\n";
    echo "  <td class='text' align='center'>&nbsp;&nbsp;</td>\n";
    echo "  <td class='text' colspan='2' wrap>$description&nbsp;&nbsp;</td>\n";
    echo "  <td class='text' colspan='2'>" . htmlentities($linedbrow['comments']) . "</td>\n";
    echo " </tr>\n";
   }
   $sysnamedisp = '';
  } // end of line
 } // end of system name

 echo "</table>\n";
 $resFinalized = sqlStatement("SELECT DISTINCT f.finalized ,f.pending
                      FROM `tbl_allcare_formflag` f INNER JOIN tbl_form_physical_exam e ON e.forms_id=f.form_id
                      WHERE form_id = '$id' ORDER BY f.id DESC LIMIT 0,1;");
 $rowFinalized = sqlFetchArray($resFinalized);
  
 echo "<br>Finalized : ".$rowFinalized['finalized'];
 echo "<br>Pending : ".$rowFinalized['pending'];
 
}
?> 
