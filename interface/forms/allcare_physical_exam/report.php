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
