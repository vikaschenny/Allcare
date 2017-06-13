<?php
/**
 * Patient selector screen.
 *
 * LICENSE: This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://opensource.org/licenses/gpl-license.php>;.
 *
 * @package OpenEMR
 * @author  Brady Miller <brady@sparmy.com>
 * @link    http://www.open-emr.org
 */

//SANITIZE ALL ESCAPES
$sanitize_all_escapes=true;
//

//STOP FAKE REGISTER GLOBALS
$fake_register_globals=false;
//

require_once("../../globals.php");
require_once("$srcdir/patient.inc");
require_once("$srcdir/formdata.inc.php");

 $fstart = isset($_REQUEST['fstart']) ? $_REQUEST['fstart'] : 0;
 $popup  = empty($_REQUEST['popup']) ? 0 : 1;
 $message = isset($_GET['message']) ? $_GET['message'] : "";
?>

<html>
<head>
<?php html_header_show();?>

<link rel=stylesheet href="<?php echo $css_header;?>" type="text/css">
<style>
form {
    padding: 0px;
    margin: 0px;
}
#searchCriteria {
    text-align: center;
    width: 100%;
    font-size: 0.8em;
    background-color: #ddddff;
    font-weight: bold;
    padding: 3px;
}
#searchResultsHeader { 
    width: 100%;
    background-color: lightgrey;
}
#searchResultsHeader table { 
    width: 96%;  /* not 100% because the 'searchResults' table has a scrollbar */
    border-collapse: collapse;
}
#searchResultsHeader th {
    font-size: 0.7em;
}
#searchResults {
    width: 100%;
    height: 80%;
    overflow: auto;
}

.patient_id { width: 11%; }
.patient_id1 { width: 11%; }
.agenName { width: 11%; }
.agenName1 { width: 11%; }
.agen_admit,.agen_admit1{ width: 9%; }
.agen_discharge,.agen_discharge1 { width: 9%; }
.agen_isactive,.agen_isactive1 { width: 7%; }
.agen_notes,.agen_notes1 { width: 9%; }
.agen_links,.agen_links1 { width: 9%; }


#searchResults table {
    width: 100%;
    border-collapse: collapse;
    background-color: white;
}
#searchResults tr {
    cursor: hand;
    cursor: pointer;
}
#searchResults td {
    font-size: 0.7em;
    border-bottom: 1px solid #eee;
}
.oneResult { }
.billing { color: red; font-weight: bold; }
.highlight { 
    background-color: #336699;
    color: white;
}
</style>

<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/jquery-1.2.2.min.js"></script>

<script language="JavaScript">

// This is called when forward or backward paging is done.
//
function submitList(offset) {
 var f = document.forms[0];
 var i = parseInt(f.fstart.value) + offset;
 if (i < 0) i = 0;
 f.fstart.value = i;
 top.restoreSession();
 f.submit();
}

</script>

</head>
<body class="body_top">

<form method='post' action='agency_select.php' name='theform' onsubmit='return top.restoreSession()'>
<input type='hidden' name='fstart'  value='<?php echo htmlspecialchars( $fstart, ENT_QUOTES); ?>' />

<?php
$MAXSHOW = 100; // maximum number of results to display at once

//the maximum number of patient records to display:
$sqllimit = $MAXSHOW;
$given = "*";
$orderby = "agency_admitdate ASC, agency_dischargedate ASC";

$search_service_code = trim($_POST['search_service_code']);
echo "<input type='hidden' name='search_service_code' value='" .
  htmlspecialchars($search_service_code, ENT_QUOTES) . "' />\n";

if ($popup) {
  echo "<input type='hidden' name='popup' value='1' />\n";

  // Construct WHERE clause and save search parameters as form fields.
  $sqlBindArray = array();
  $where = "1 = 1";
  $fres = sqlStatement("SELECT * FROM layout_options " .
    "WHERE form_id = 'AGENCY' AND uor > 0 AND field_id != '' " .
    "ORDER BY group_name, seq");
  while ($frow = sqlFetchArray($fres)) {
    $field_id  = $frow['field_id'];
    $data_type = $frow['data_type'];
    if (!empty($_REQUEST[$field_id])) {
     $value = trim($_REQUEST[$field_id]);
      if ($field_id == 'agency_admitdate') {
      echo  $where .= " AND $field_id LIKE ?";
        array_push($sqlBindArray,$value);
      }
      else if ($field_id == 'agency_dischargedate') {
        $where .= " AND $field_id LIKE ?";
        array_push($sqlBindArray,$value);
      }
      else {
        $where .= " AND $field_id LIKE ?";
        array_push($sqlBindArray,$value."%");
      }
      echo "<input type='hidden' name='" . htmlspecialchars( $field_id, ENT_QUOTES) .
        "' value='" . htmlspecialchars( $value, ENT_QUOTES) . "' />\n";
    }
  }

 $sql = "SELECT $given FROM tbl_patientagency " .
    "WHERE $where ORDER BY $orderby LIMIT $fstart, $sqllimit";
  $rez = sqlStatement($sql,$sqlBindArray);
  $result = array();
  while ($row = sqlFetchArray($rez)) $result[] = $row;
  //_set_patient_inc_count($sqllimit, count($result), $where, $sqlBindArray);
}
?>

</form>

<table border='0' cellpadding='5' cellspacing='0' width='100%'>
 <tr>
  <td class='text'>
   <a href="" target=_new onclick='top.restoreSession()'>[<?php echo htmlspecialchars( xl('Help'), ENT_NOQUOTES); ?>]&nbsp</a>
  </td>
  <td class='text' align='center'>
<?php if ($message) echo "<font color='red'><b>".htmlspecialchars( $message, ENT_NOQUOTES)."</b></font>\n"; ?>
  </td>
 </tr>
</table>

 
    
<div id="searchResultsHeader">
<table>
<tr>
<th class="patient_id"><?php echo htmlspecialchars( xl('patientid'), ENT_NOQUOTES);?></th>    
<th class="agenName"><?php echo htmlspecialchars( xl('agency name'), ENT_NOQUOTES);?></th>
<th class="agen_admit"><?php echo htmlspecialchars( xl('Admit Date'), ENT_NOQUOTES);?></th>
<th class="agen_discharge"><?php echo htmlspecialchars( xl('Discharge Date'), ENT_NOQUOTES);?></th>
<th class="agen_isactive"><?php echo htmlspecialchars( xl('Is Active'), ENT_NOQUOTES);?></th>
<th class="agen_notes"><?php echo htmlspecialchars( xl('Notes'), ENT_NOQUOTES);?></th>
<th class="agen_links"><?php echo htmlspecialchars( xl('Related Links'), ENT_NOQUOTES);?></th>

</tr>
</table>
</div>

<div id="searchResults">

<table>
<tr>
<?php
if ($result) {
    foreach ($result as $iter) {
        echo "<tr class='oneresult' id='".htmlspecialchars( $iter['patientid'], ENT_QUOTES)."'>";
         echo  "<td class='patient_id1'>" . htmlspecialchars($iter['patientid'] ) . "</td>\n";
        echo  "<td class='agenName1'>" . htmlspecialchars($iter['agencyid'] ) . "</td>\n";
      
        echo "<td class='agen_admit1'>" .
	    htmlspecialchars( $iter['agency_admitdate'], ENT_NOQUOTES) . "</td>\n";
        
        echo "<td class='agen_discharge1'>" . htmlspecialchars( $iter['agency_dischargedate'], ENT_NOQUOTES) . "</td>";
        
            echo "<td class='agen_isactive1'>" . htmlspecialchars( $iter['agency_isactive'], ENT_NOQUOTES) . "</td>";
        
           
        
        echo "<td class='agen_notes1'>" . htmlspecialchars( $iter['agency_notes'], ENT_NOQUOTES) . "</td>"; 
        echo "<td class='agen_links1'>" . htmlspecialchars( $iter['agency_related_links'], ENT_NOQUOTES) . "</td>"; 
        echo "</tr>";
    }
}
?>
</tr>    
</table>
</div>  <!-- end searchResults DIV -->
  

<script language="javascript">

// jQuery stuff to make the page a little easier to use

$(document).ready(function(){
    // $("#searchparm").focus();
    $(".oneresult").mouseover(function() { $(this).addClass("highlight"); });
    $(".oneresult").mouseout(function() { $(this).removeClass("highlight"); });
    $(".oneresult").click(function() { SelectPatient(this); });
    // $(".event").dblclick(function() { EditEvent(this); });
});

var SelectPatient = function (eObj) {
<?php 
// For the old layout we load a frameset that also sets up the new pid.
// The new layout loads just the demographics frame here, which in turn
// will set the pid and load all the other frames.
if ($GLOBALS['concurrent_layout']) {
    $newPage = "../../patient_file/summary/demographics.php?set_pid=";
    $target = "document";
}
else {
    $newPage = "../../patient_file/patient_file.php?set_pid=";
    $target = "top";
}
?>
    objID = eObj.id;
    var parts = objID.split("~");
    <?php if (!$popup) echo "top.restoreSession();\n"; ?>
    <?php if ($popup) echo "opener."; echo $target; ?>.location.href = '<?php echo $newPage; ?>' + parts[0];
    <?php if ($popup) echo "window.close();\n"; ?>
    return true;
}

</script>

</body>
</html>

