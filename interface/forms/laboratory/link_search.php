<?php
/** **************************************************************************
 *	LABORATORY/LINK_PROCESS.PHP
 *
 *	Copyright (c)2014 - Medical Technology Services (MDTechSvcs.com)
 *
 *	This program is licensed software: licensee is granted a limited nonexclusive
 *  license to install this Software on more than one computer system, as long as all
 *  systems are used to support a single licensee. Licensor is and remains the owner
 *  of all titles, rights, and interests in program.
 *  
 *  Licensee will not make copies of this Software or allow copies of this Software 
 *  to be made by others, unless authorized by the licensor. Licensee may make copies 
 *  of the Software for backup purposes only.
 *
 *	This program is distributed in the hope that it will be useful, but WITHOUT 
 *	ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS 
 *  FOR A PARTICULAR PURPOSE. LICENSOR IS NOT LIABLE TO LICENSEE FOR ANY DAMAGES, 
 *  INCLUDING COMPENSATORY, SPECIAL, INCIDENTAL, EXEMPLARY, PUNITIVE, OR CONSEQUENTIAL 
 *  DAMAGES, CONNECTED WITH OR RESULTING FROM THIS LICENSE AGREEMENT OR LICENSEE'S 
 *  USE OF THIS SOFTWARE.
 *
 *  @package laboratory
 *  @subpackage generic
 *  @version 2.0
 *  @copyright Medical Technology Services
 *  @author Ron Criswell <ron.criswell@MDTechSvcs.com>
 * 
 *************************************************************************** */
//SANITIZE ALL ESCAPES
$sanitize_all_escapes=true;

//STOP FAKE REGISTER GLOBALS
$fake_register_globals=false;

require_once("../../globals.php");
require_once("$srcdir/options.inc.php");
require_once("$srcdir/patient.inc");

$form_title = 'Laboratory Link Search';
$form_name = 'link_search';
$form_id = $_REQUEST['id'];

$info_msg = "";

 // If we are searching, search.
 //
if ($_REQUEST['searchby'] && $_REQUEST['searchparm']) {
  $searchby = $_REQUEST['searchby'];
  $searchparm = trim($_REQUEST['searchparm']);

  if ($searchby == "Last") {
    $result = getPatientLnames("$searchparm","*");
  } elseif ($searchby == "Phone") {                  //(CHEMED) Search by phone number
    $result = getPatientPhone("$searchparm","*");
  } elseif ($searchby == "ID") {
    $result = getPatientId("$searchparm","*");
  } elseif ($searchby == "DOB") {
    $result = getPatientDOB("$searchparm","*");
  } elseif ($searchby == "SSN") {
    $result = getPatientSSN("$searchparm","*");
  }
}
?>

<html>
<head>
<?php html_header_show();?>
<title><?php echo htmlspecialchars( xl('Patient Finder'), ENT_NOQUOTES); ?></title>
<link rel="stylesheet" href='<?php echo $css_header ?>' type='text/css'>

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
    padding: 3px 0 3px 0;
}
#searchResultsHeader { 
    width: 100%;
    background-color: lightgrey;
}
#searchResultsHeader table { 
    width: 100%;  
    text-align: left;
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

/* search results column widths */
.srName { width: 30%; padding-left:6px }
.srPhone { width: 21%; }
.srSS { width: 17%; }
.srDOB { width: 17%; }
.srID { width: 15%; }

#searchResults table {
    width: 100%;
    text-align: left;
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

/* for search results or 'searching' notification */
#searchstatus {
	width: 100%;
    font-size: 0.8em;
    font-weight: bold;
    padding: 3px 0 10px 0;
    font-style: italic;
    color: black;
    text-align: center;
}
.noResults { background-color: #ccc; }
.tooManyResults { background-color: #fc0; }
.howManyResults { background-color: #9f6; }
#searchspinner { 
    display: inline;
    visibility: hidden;
}

/* highlight for the mouse-over */
.highlight {
    background-color: #336699;
    color: white;
}
</style>

<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/jquery-1.2.2.min.js"></script>
<!-- ViSolve: Verify the noresult parameter -->
<?php
if(isset($_GET["res"])){
echo '
<script language="Javascript">
			// Pass the variable to parent hidden type and submit
			opener.document.theform.resname.value = "noresult";
			opener.document.theform.submit();
			// Close the window
			window.self.close();
</script>';
}
?>
<!-- ViSolve: Verify the noresult parameter -->

<script language="JavaScript">

 function selpid(pid, lname, fname, dob) {
  if (opener.closed || ! opener.setpatient)
   alert("<?php echo htmlspecialchars( xl('The destination form was closed; I cannot act on your selection.'), ENT_QUOTES); ?>");
  else
   opener.setpatient(pid, lname, fname, dob);
  window.close();
  return false;
 }

</script>

</head>

<body class="body_top">

<div id="searchCriteria">
	<!-- form method='post' name='theform' id="theform" action='find_patient_popup.php?<?php if(isset($_GET['pflag'])) echo "pflag=0"; ?>' -->
	<form action='link_search.php' name='theform' method='post' onsubmit='return validate(this)'>
		<input type="hidden" name="id" value="<?php echo $form_id ?>" />

   <?php echo htmlspecialchars( xl('Search by:'), ENT_NOQUOTES); ?>
   <select name='searchby'>
    <option value="Last"><?php echo htmlspecialchars( xl('Name'), ENT_NOQUOTES); ?></option>
    <!-- (CHEMED) Search by phone number -->
    <option value="Phone"<?php if ($searchby == 'Phone') echo ' selected' ?>><?php echo htmlspecialchars( xl('Phone'), ENT_NOQUOTES); ?></option>
    <option value="ID"<?php if ($searchby == 'ID') echo ' selected' ?>><?php echo htmlspecialchars( xl('ID'), ENT_NOQUOTES); ?></option>
    <option value="SSN"<?php if ($searchby == 'SSN') echo ' selected' ?>><?php echo htmlspecialchars( xl('SSN'), ENT_NOQUOTES); ?></option>
    <option value="DOB"<?php if ($searchby == 'DOB') echo ' selected' ?>><?php echo htmlspecialchars( xl('DOB'), ENT_NOQUOTES); ?></option>
   </select>
 <?php echo htmlspecialchars( xl('for:'), ENT_NOQUOTES); ?>
   <input type='text' id='searchparm' name='searchparm' size='12' value='<?php echo htmlspecialchars( $_REQUEST['searchparm'], ENT_QUOTES); ?>'
    title='<?php echo htmlspecialchars( xl('If name, any part of lastname or lastname,firstname'), ENT_QUOTES); ?>'>
   &nbsp;
   <input type='submit' id="submitbtn" value='<?php echo htmlspecialchars( xl('Search'), ENT_QUOTES); ?>'>
   <!-- &nbsp; <input type='button' value='<?php echo htmlspecialchars( xl('Close'), ENT_QUOTES); ?>' onclick='window.close()' /> -->
   <div id="searchspinner"><img src="<?php echo $GLOBALS['webroot'] ?>/interface/pic/ajax-loader.gif"></div>
</form>
</div>


<?php if (! isset($_REQUEST['searchparm'])): ?>
<div id="searchstatus"><?php echo htmlspecialchars( xl('Enter your search criteria above'), ENT_NOQUOTES); ?></div>
<?php elseif (count($result) == 0): ?>
<div id="searchstatus" class="noResults"><?php echo htmlspecialchars( xl('No records found. Please expand your search criteria.'), ENT_NOQUOTES); ?>
<br>
</div>
<?php elseif (count($result)>=100): ?>
<div id="searchstatus" class="tooManyResults"><?php echo htmlspecialchars( xl('More than 100 records found. Please narrow your search criteria.'), ENT_NOQUOTES); ?></div>
<?php elseif (count($result)<100): ?>
<div id="searchstatus" class="howManyResults"><?php echo htmlspecialchars( count($result), ENT_NOQUOTES); ?> <?php echo htmlspecialchars( xl('records found.'), ENT_NOQUOTES); ?></div>
<?php endif; ?>

<?php if (isset($result)): ?>

<div id="searchResultsHeader">
<table>
 <tr>
  <th class="srName"><?php echo htmlspecialchars( xl('Name'), ENT_NOQUOTES); ?></th>
  <th class="srPhone"><?php echo htmlspecialchars( xl('Phone'), ENT_NOQUOTES); ?></th> <!-- (CHEMED) Search by phone number -->
  <th class="srSS"><?php echo htmlspecialchars( xl('SS'), ENT_NOQUOTES); ?></th>
  <th class="srDOB"><?php echo htmlspecialchars( xl('DOB'), ENT_NOQUOTES); ?></th>
  <th class="srID"><?php echo htmlspecialchars( xl('ID'), ENT_NOQUOTES); ?></th>
 </tr>
</table> 
</div>

<div id="searchResults">
<table> 
<?php
foreach ($result as $iter) {
    $iterpid   = $iter['pid'];
    $iterlname = $iter['lname'];
    $iterfname = $iter['fname'];
    $itermname = $iter['mname'];
    $iterdob   = $iter['DOB'];

    // the special genericname2 of 'Billing' means something, but I'm not sure
    // what, regardless it gets special coloring and an extra line of output
    // in the 'name' column -- JRM
    $trClass = "oneresult";
    if ($iter['genericname2'] == 'Billing') { $trClass .= " billing"; }

    echo " <tr class='".$trClass."' id='" .
        htmlspecialchars( $iterpid."~".$iterlname."~".$iterfname."~".$iterdob, ENT_QUOTES) . "'>";
    echo "  <td class='srName'>" . htmlspecialchars( $iterlname.", ".$iterfname." ".$itermname, ENT_NOQUOTES);
    if ($iter['genericname2'] == 'Billing') { echo "<br>" . htmlspecialchars( $iter['genericval2'], ENT_NOQUOTES); }
    echo "</td>\n";
    echo "  <td class='srPhone'>" . htmlspecialchars( $iter['phone_home'], ENT_NOQUOTES) . "</td>\n"; //(CHEMED) Search by phone number
    echo "  <td class='srSS'>" . htmlspecialchars( $iter['ss'], ENT_NOQUOTES) . "</td>\n";
    echo "  <td class='srDOB'>" . htmlspecialchars( $iter['DOB'], ENT_NOQUOTES) . "</td>\n";
    echo "  <td class='srID'>" . htmlspecialchars( $iter['pubpid'], ENT_NOQUOTES) . "</td>\n";
    echo " </tr>";
}
?>
</table>
</div>
<?php endif; ?>

<script language="javascript">

// jQuery stuff to make the page a little easier to use

$(document).ready(function(){
    $("#searchparm").focus();
    $(".oneresult").mouseover(function() { $(this).toggleClass("highlight"); });
    $(".oneresult").mouseout(function() { $(this).toggleClass("highlight"); });
    $(".oneresult").click(function() { SelectPatient(this); });
    //ViSolve 
    $(".noresult").click(function () { SubmitForm(this);});

    //$(".event").dblclick(function() { EditEvent(this); });
    $("#theform").submit(function() { SubmitForm(this); });

});

// show the 'searching...' status and submit the form
var SubmitForm = function(eObj) {
    $("#submitbtn").css("disabled", "true");
    $("#searchspinner").css("visibility", "visible");
    return true;
}


var SelectPatient = function (eObj) {
	<?php 
	$newPage = "link_process.php?id=".$form_id."&pid=";
	?>
	objID = eObj.id;
	var parts = objID.split("~");
	location.href = '<?php echo $newPage; ?>' + parts[0];
    return true;
}

</script>

</body>
</html>