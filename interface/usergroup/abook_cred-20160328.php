<?php
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.

// add_transaction is a misnomer, as this script will now also edit
// existing transactions.
 
//SANITIZE ALL ESCAPES
$sanitize_all_escapes=true;
//

//STOP FAKE REGISTER GLOBALS
$fake_register_globals=false;
//

require_once("../globals.php");
include_once("$srcdir/abook_data_lib.php");
require_once("$srcdir/options.inc.php");
require_once("$srcdir/amc.php");
include_once("$srcdir/formdata.inc.php");
include_once("$srcdir/patient.inc");
include_once("$srcdir/acl.inc");
$abookuserid = $_REQUEST['abookuserid'];


$id = empty($_REQUEST['id']) ? 0 : $_REQUEST['id'] + 0;
$mode    = empty($_POST['mode' ]) ? '' : $_POST['mode' ];
$inmode    = $_GET['inmode'];
$body_onload_code="";

/* $form_name=$_GET['form_name'];
 $patient_id=$_GET['patient_id'];*/

if ($mode) {   
  /**use sql placemaker**/
    
  /*$sqlBindArray=array();
  $fres = sqlStatement("SELECT * FROM layout_options " .
    "WHERE form_id = 'ABOOKCRED' AND uor > 0 AND field_id != '' " .
    "ORDER BY group_name, seq");

  while ($frow = sqlFetchArray($fres)) {
     
    $data_type = $frow['data_type'];
    $field_id  = $frow['field_id'];
    $value = $_POST["form_$field_id"];
    $sets .=  add_escape_custom($field_id) . " = ?"."," ;
    array_push($sqlBindArray,$value);
  }
   
   if ($id) {       
    //use sql placemaker
    array_push($sqlBindArray,$id);
    $sets1= rtrim($sets,',');
    sqlStatement("UPDATE tbl_user_cred SET $sets1 WHERE id = ?", $sqlBindArray);
    }
    else {
    //use sql placemaker
     
    array_push($sqlBindArray,$abookuserid);
    $sets .= " userid = ?";
    $id = sqlInsert("INSERT INTO tbl_user_cred SET $sets", $sqlBindArray);
  
  }  */
$newdata = array();
$newdata['tbl_user_cred']['userid'] = $abookuserid;
$fres = sqlStatement("SELECT * FROM layout_options " .
  "WHERE form_id = 'ABOOKCRED' AND uor > 0 AND field_id != '' " .
  "ORDER BY group_name, seq");
while ($frow = sqlFetchArray($fres)) {
  $data_type = $frow['data_type'];
  $field_id  = $frow['field_id'];
  // $value  = '';
  $colname = $field_id;
  $table = 'tbl_user_cred';
  
  // if (isset($_POST["form_$field_id"])) $value = $_POST["form_$field_id"];
  $value = get_layout_form_value($frow);

  $newdata[$table][$colname] = $value;
}

//$id=$_POST['db_id'];
//$insid=$_POST['db_insid'];
//$sql=sqlStatement("select * from tbl_patientinsurancecompany where id='".$id."'");
//$rowpha=sqlFetchArray($sql);
if($id)
{ 
  updateAbookCred1ton($id, $newdata['tbl_user_cred'] ,$create=false);
}
else
{  
    updateAbookCred1ton($id, $newdata['tbl_user_cred'],$create=true);

}
   
if ($GLOBALS['concurrent_layout'])
    $body_onload_code = "javascript:location.href='abook_cred_1ton.php?abookuserid=$abookuserid';";
  else
    $body_onload_code = "javascript:parent.New_group__service.location.href='abook_cred_1ton.php?abookuserid=$abookuserid';";
}


$CPR = 4; // cells per row

function end_cell() {
  global $item_count, $cell_count;
  if ($item_count > 0) {
    echo "</td>";
    $item_count = 0;
  }
}

function end_row() {
  global $cell_count, $CPR;
  end_cell();
  if ($cell_count > 0) {
    for (; $cell_count < $CPR; ++$cell_count) echo "<td></td>";
    echo "</tr>\n";
    $cell_count = 0;
  }
}

function end_group() {
  global $last_group;
  if (strlen($last_group) > 0) {
    end_row();
    echo " </table>\n";
    echo "</div>\n";
  }
}

// If we are editing a facility, get its ID and data.
$trow = $id ? getAbookCredDatabyId($id) : array();

?>
<html>
<head>
<?php html_header_show(); ?>

<link rel='stylesheet' href="<?php echo $css_header;?>" type="text/css">
<link rel="stylesheet" type="text/css" href="../library/js/fancybox/jquery.fancybox-1.2.6.css" media="screen" />

<style type="text/css">@import url(../../library/dynarch_calendar.css);</style>
<script type="text/javascript" src="../../library/dynarch_calendar.js"></script>
<?php include_once("{$GLOBALS['srcdir']}/dynarch_calendar_en.inc.php"); ?>
<script type="text/javascript" src="../../library/dynarch_calendar_setup.js"></script>
<script type="text/javascript" src="../../library/textformat.js"></script>
<script type="text/javascript" src="../../library/dialog.js"></script>

<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/common.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/jquery.1.3.2.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/fancybox/jquery.fancybox-1.2.6.js"></script>
<script type="text/javascript">
$(document).ready(function(){
    tabbify();
    enable_modals();
});
</script>
<script language="JavaScript">

function divclick(cb, divid) {
 var divstyle = document.getElementById(divid).style;
 if (cb.checked) {
  divstyle.display = 'block';
 } else {
  divstyle.display = 'none';
 }
 return true;
}

// Process click on Delete link.
function deleteme() {
// onclick='return deleteme()'
 dlgopen('../patient_file/deleter.php?abookuserid=<?php echo htmlspecialchars( $id, ENT_QUOTES); ?>', '_blank', 500, 450);
 return false;
}

// Called by the deleteme.php window on a successful delete.
function imdeleted() {
 top.restoreSession();
 location.href = 'usergroup/add_abook_cred.php';
}

// Compute the length of a string without leading and trailing spaces.
function trimlen(s) {
 var i = 0;
 var j = s.length - 1;
 for (; i <= j && s.charAt(i) == ' '; ++i);
 for (; i <= j && s.charAt(j) == ' '; --j);
 if (i > j) return 0;
 return j + 1 - i;
}

// Validation logic for form submission.
function validate(f) {
    
 var errCount = 0;
 var errMsgs = new Array();

 
    <?php generate_layout_validation('ABOOKCRED'); ?>


 var msg = "";
 msg += "<?php echo htmlspecialchars( xl('The following fields are required'), ENT_QUOTES); ?>:\n\n";
 for ( var i = 0; i < errMsgs.length; i++ ) {
	msg += errMsgs[i] + "\n";
 }
 msg += "\n<?php echo htmlspecialchars( xl('Please fill them in before continuing.'), ENT_QUOTES); ?>";

 if ( errMsgs.length > 0 ) {
	alert(msg);
 }
 return errMsgs.length < 1;
}

function submitme() {
 var f = document.forms['new_userdata'];
 if (validate(f)) {
  top.restoreSession();
  f.submit();
 }
}


</script>


<style type="text/css">
div.tab {
	height: auto;
	width: auto;
}
</style>

</head>
<body class="body_top" onload="<?php echo $body_onload_code; ?>" >
    <form name='new_userdata' method='post' action='abook_cred.php?id=<?php echo htmlspecialchars( $id, ENT_QUOTES); ?>' onsubmit='return validate(this)'>
<input type='hidden' id="hdnmode" name='mode' value='add'>
<input type="hidden" name="abookuserid" value="<?php echo $_REQUEST['abookuserid']; ?>" >

	<table>
	    <tr>
            <td>
                  <a href="javascript:;"  <?php if (!$GLOBALS['concurrent_layout']) echo "target='Main'"; ?> class="css_button" onclick="submitme();">
                    <span><?php echo htmlspecialchars( xl('Save'), ENT_NOQUOTES); ?></span>
                 </a>
             </td>
             <td>
                 <a href="abook_cred_1ton.php?abookuserid=<?php echo $abookuserid; ?>"  <?php if (!$GLOBALS['concurrent_layout']) echo "target='Main'"; ?> class="css_button" >
                    <span><?php echo htmlspecialchars( xl('Cancel'), ENT_NOQUOTES); ?></span>
                </a>
            </td>
        </tr>
	</table>
       
	
<div id='userdatadiv'>
   

<div id="user_data">
        <ul class="tabNav">
<?php
$fres = sqlStatement("SELECT * FROM layout_options " .
  "WHERE form_id = 'ABOOKCRED' AND uor > 0 " .
  "ORDER BY group_name, seq");
$last_group = '';
$cell_count = 0;
$item_count = 0;
$display_style = 'block';

while ($frow = sqlFetchArray($fres)) {
  $this_group = $frow['group_name'];
  $titlecols  = $frow['titlecols'];
  $datacols   = $frow['datacols'];
  $data_type  = $frow['data_type'];
  $field_id   = $frow['field_id'];
  $list_id    = $frow['list_id'];

  

  // Handle a data category (group) change.
  if (strcmp($this_group, $last_group) != 0) {
    $group_seq  = substr($this_group, 0, 1);
    $group_name = substr($this_group, 1);
    $last_group = $this_group;
	if($group_seq==1)	echo "<li class='current'>";
	else				echo "<li class=''>";
        $group_seq_esc = htmlspecialchars( $group_seq, ENT_QUOTES);
        $group_name_show = htmlspecialchars( xl_layout_label($group_name), ENT_NOQUOTES);
	echo "<a href='' id='div_$group_seq_esc'>".
	    "$group_name_show</a></li>";
  }
  ++$item_count;
}
?>
</ul>
<div class="tabContainer">

                <?php
$fres = sqlStatement("SELECT * FROM layout_options " .
  "WHERE form_id = 'ABOOKCRED' AND uor > 0 " .
  "ORDER BY group_name, seq");
$last_group = '';
$cell_count = 0;
$item_count = 0;
$display_style = 'block';

while ($frow = sqlFetchArray($fres)) {
  $this_group = $frow['group_name'];
  $titlecols  = $frow['titlecols'];
  $datacols   = $frow['datacols'];
  $data_type  = $frow['data_type'];
  $field_id   = $frow['field_id'];
  $list_id    = $frow['list_id'];

  
 $currvalue  = '';
  if (isset($trow[$field_id])) $currvalue = $trow[$field_id];
  // Handle a data category (group) change.
  
// Handle a data category (group) change.
  if (strcmp($this_group, $last_group) != 0) {
    end_group();
   $group_seq  = substr($this_group, 0, 1);
   $group_name = substr($this_group, 1);
   $last_group = $this_group;
   $group_seq_esc = htmlspecialchars( $group_seq, ENT_QUOTES);
	if($group_seq==1)	echo "<div class='tab current' id='div_$group_seq_esc'>";
	else				echo "<div class='tab' id='div_$group_seq_esc'>";
    echo " <table border='0' cellpadding='0'>\n";
    $display_style = 'none';
  }
  // Handle starting of a new row.
  if (($titlecols > 0 && $cell_count >= $CPR) || $cell_count == 0) {
    end_row();
    echo " <tr>";
  }

  if ($item_count == 0 && $titlecols == 0) $titlecols = 1;

  // Handle starting of a new label cell.
  if ($titlecols > 0) {
    end_cell();
    $titlecols_esc = htmlspecialchars( $titlecols, ENT_QUOTES);
    echo "<td width='70' valign='top' colspan='$titlecols_esc'";
    echo ($frow['uor'] == 2) ? " class='required'" : " class='bold'";
    if ($cell_count == 2) echo " style='padding-left:10pt'";
    echo ">";
    $cell_count += $titlecols;
  }
  ++$item_count;

  echo "<b>";

  // Modified 6-09 by BM - Translate if applicable
  if ($frow['title']) echo (htmlspecialchars( xl_layout_label($frow['title']), ENT_NOQUOTES) . ":"); else echo "&nbsp;";

  echo "</b>";

  // Handle starting of a new data cell.
  if ($datacols > 0) {
    end_cell();
    $datacols_esc = htmlspecialchars( $datacols, ENT_QUOTES);
    echo "<td valign='top' colspan='$datacols_esc' class='text'";
    if ($cell_count > 0) echo " style='padding-left:5pt'";
    echo ">";
    $cell_count += $datacols;
  }

  ++$item_count;
  generate_form_field($frow, $currvalue);
  echo "</div>";
}

end_group();

?>
</div></div>
</div>

<p>
<div id='otherdiv' style='display:none'>
<span class='bold'><?php echo htmlspecialchars( xl('Details'), ENT_NOQUOTES); ?>:</span><br>
<textarea name='body' rows='6' cols='40' wrap='virtual'><?php echo htmlspecialchars( $body, ENT_NOQUOTES); ?>
</textarea>
</div>
</form>
</p>

<!-- include support for the list-add selectbox feature -->
<?php include $GLOBALS['fileroot']."/library/options_listadd.inc"; ?>
</body>
<script language="JavaScript">
<?php echo $date_init; ?>
</script>
</html>
