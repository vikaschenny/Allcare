<?php
/*******************************************************************************\
 * Copyright (C) Visolve (vicareplus_engg@visolve.com)                          *
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
 * along with this program; if not, write to the Free Software                  *
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.  *
 ********************************************************************************/

require_once("../../verify_session.php");
require_once("$srcdir/sql.inc");

?>
<?php
/**
 * Retrieve the recent 'N' disclosures.
 * @param $pid   -  patient id.
 * @param $limit -  certain limit up to which the disclosures are to be displyed.
 */
function getDisclosureByDate($pid,$limit)
{
	$r1=sqlStatement("select event,recipient,description,date from extended_log where patient_id=? AND event in (select option_id from list_options where list_id='disclosure_type') order by date desc limit 0,$limit", array($pid) );
	$result2 = array();
	for ($iter = 0;$frow = sqlFetchArray($r1);$iter++)
		$result2[$iter] = $frow;
	return $result2;
}
?>
<div id='pnotes' style='margin-top: 3px; margin-left: 10px; margin-right: 10px'><!--outer div-->
<br>
<table width='100%'>
<?php
//display all the disclosures for the day, as well as others from previous dates, up to a certain number, $N
$N=3;
//$has_variable is set to 1 if there are disclosures recorded.
$has_disclosure=0;
//retrieve all the disclosures.
$result=getDisclosureByDate($pid,$N);
if ($result != null){
	$disclosure_count = 0;//number of disclosures so far displayed
	foreach ($result as $iter)
	{
		$has_disclosure = 1;
		$app_event=$iter{"event"};
		$event=split("-",$app_event);
		$description=nl2br(htmlspecialchars($iter{"description"},ENT_NOQUOTES));//for line breaks.
		//listing the disclosures 
		echo "<tr style='border-bottom:1px dashed' class='text'>";
			echo "<td valign='top' class='text'>";
			if($event[1]=='healthcareoperations'){ echo "<b>";echo htmlspecialchars(xl('health care operations'),ENT_NOQUOTES);echo "</b>"; } else echo "<b>".htmlspecialchars($event[1],ENT_NOQUOTES)."</b>";
			echo "</td>";
			echo "<td  valign='top'class='text'>";
			echo htmlspecialchars($iter{"date"}." (".xl('Recipient').":".$iter{"recipient"}.")",ENT_NOQUOTES);
	                echo " ".$description;
			echo "</td>";
		echo "</tr>";

	}
}
?>
</table>
<?php
if ( $has_disclosure == 0 ) //If there are no disclosures recorded
{ ?>
	<span class='text'> <?php echo htmlspecialchars(xl("There are no disclosures recorded for this patient."),ENT_NOQUOTES);
	echo " "; echo htmlspecialchars(xl("To record disclosures, please click"),ENT_NOQUOTES); echo " ";echo "<a href='disclosure_full.php'>"; echo htmlspecialchars(xl("here"),ENT_NOQUOTES);echo "</a>."; 
?>
	</span> 
<?php 
} else
{
?> 
	<br />
	<span class='text'> <?php  
	echo htmlspecialchars(xl('Displaying the following number of most recent disclosures:'),ENT_NOQUOTES);?><b><?php echo " ".htmlspecialchars($N,ENT_NOQUOTES);?></b><br>
	<a href='disclosure_full.php'><?php echo htmlspecialchars(xl('Click here to view them all.'),ENT_NOQUOTES);?></a>
	</span><?php
} ?>
<br />
<br />
</div>

