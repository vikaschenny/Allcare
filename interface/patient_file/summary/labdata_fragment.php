<?php
/*******************************************************************************\
 * Copyright (C) 2014 Joe Slam (joe@produnis.de)                                *
 *                                                                              *
 * This program is free software; you can redistribute it and/or                *
 * modify it under the terms of the GNU General Public License                  *
 * as published by the Free Software Foundation; either version 2               *
 * of the License, or (at your option) any later version.                       *
 *                                                                              *
 * This program is distributed in the hope that it will be useful,              *
 * but WITHOUT ANY WARRANTY; without even the implied warranty of               *
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the                *
 * GNU General Public License for more details.                                 *
 *                                                                              *
 * You should have received a copy of the GNU General Public License            *
 * along with this program; if not,                                             *
 * see <http://opensource.org/licenses/gpl-license.php>                          *
 ********************************************************************************
 * @package OpenEMR
 * @author Joe Slam <joe@produnis.de>
 * @link http://www.open-emr.org
 * */

//SANITIZE ALL ESCAPES
$sanitize_all_escapes=true;
//

//STOP FAKE REGISTER GLOBALS
$fake_register_globals=false;
//

require_once("../../globals.php");

?>
<div id='labdata' style='margin-top: 3px; margin-left: 10px; margin-right: 10px'><!--outer div-->
<br>
<?php
//retrieve most recent set of labdata.
$spell = "SELECT DISTINCT rpt.date_report AS thedate, " . 
			      "ocd.procedure_name AS theprocedure, " .
				  "ord.encounter_id AS theencounter " . 
			"FROM procedure_report rpt " .
			"JOIN procedure_result res ON res.procedure_report_id = rpt.procedure_report_id " .
			"JOIN procedure_order ord ON  ord.procedure_order_id = rpt.procedure_order_id " . 
			"JOIN procedure_order_code ocd ON ord.procedure_order_id = ocd.procedure_order_id " . 
			"JOIN procedure_providers pvr ON pvr.ppid = ord.lab_id " .
			"WHERE ord.patient_id = ? AND ord.encounter_id > 0 AND (pvr.type = 'internal' OR pvr.type = 'labcorp' OR pvr.type = 'quest' OR pvr.type = 'laboratory') " . 
			"ORDER BY rpt.date_report DESC ";
$res=sqlStatement($spell, array($pid) );

	$order_date = '';
	$order_enc = '';
	while ($result = sqlFetchArray($res)) {
		if (!$order_enc) {
			$order_date = $result['thedate'];
			$order_enc = $result['theencounter'];
?>
  			<span class='text'><b>
  			Most recent lab data:
  			<a href="#" onclick="parent.left_nav.loadFrame2('ens1', 'RBot', 'patient_file/encounter/encounter_top.php?set_encounter=<?php echo attr($order_enc) ?>')">
  			<?php echo date('Y-m-d',strtotime($order_date)); ?>
  			</a></b><br/><br/>
<?php 
		}
	  	if ($order_enc != $result['theencounter']) break;
	  	if ($order_date != $result['thedate']) break;
	  	echo xlt('Procedure') . ": " . text($result['theprocedure']) . "<br>";
	  	
	} // end while
	
	if (!$order_date) {
  		echo "<span class='text'>".htmlspecialchars(xl("No lab data documented."),ENT_NOQUOTES)."</span>\n";
	}
	else {
  ?>
  <br />
  </span><span class='text'>
  <a href='../../reports/laboratory/lab_analysis.php' onclick='top.restoreSession()'><?php echo htmlspecialchars(xl('Click here to view and graph all lab data.'),ENT_NOQUOTES);?></a>
  </span><?php
	}?>
<br />
<br />
</div>
