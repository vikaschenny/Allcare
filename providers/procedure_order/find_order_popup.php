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

$order =  0 + $_GET['order'];
$labid =  0 + $_GET['labid'];

//////////////////////////////////////////////////////////////////////
// The form was submitted with the selected code type.
if (isset($_GET['typeid'])) {
  $typeid = $_GET['typeid'] + 0;
  $name = '';
  if ($typeid) {
    $ptrow = sqlQuery("SELECT name FROM procedure_type WHERE " .
      "procedure_type_id = '$typeid'");
    $name = addslashes($ptrow['name']);
  }
?>
<script language="JavaScript">
if (opener.closed || !opener.set_proc_type) {
 alert('<?php xl('The destination form was closed; I cannot act on your selection.','e'); ?>');
}
else {
 opener.set_proc_type(<?php echo "$typeid, '$name'"; ?>);
<?php
// This is to generate the "Questions at Order Entry" for the Procedure Order form.
// GET parms needed for this are: formid, formseq.
if (isset($_GET['formid'])) {
  if ($typeid) {
    require_once("qoe.inc.php");
    $qoe_init_javascript = '';
    echo ' opener.set_proc_html("';
    echo generate_qoe_html($typeid, intval($_GET['formid']), 0, intval($_GET['formseq']));
    echo '", "' . $qoe_init_javascript .  '");' . "\n";
  }
  else {
    echo ' opener.set_proc_html("", "");' . "\n";
  }
}
?>
}
window.close();
</script>
<?php
  exit();
}
// End Submission.
//////////////////////////////////////////////////////////////////////

?>
<html>
<head>
<?php html_header_show(); ?>
<title><?php echo xlt('Procedure Picker'); ?></title>
<link rel="stylesheet" href='<?php echo attr($css_header) ?>' type='text/css'>

<style>
td { font-size:10pt; }
</style>

<script language="JavaScript">

// Reload the script with the select procedure type ID.
function selcode(typeid) {
 location.href = 'find_order_popup.php<?php
echo "?order=$order&labid=$labid";
if (isset($_GET['formid' ])) echo '&formid='  . $_GET['formid'];
if (isset($_GET['formseq'])) echo '&formseq=' . $_GET['formseq'];
?>&typeid=' + typeid;
 return false;
}

</script>

</head>

<body class="body_top">

<form method='post' name='theform' action='find_order_popup.php<?php
echo "?order=$order&labid=$labid";
if (isset($_GET['formid' ])) echo '&formid='  . $_GET['formid'];
if (isset($_GET['formseq'])) echo '&formseq=' . $_GET['formseq'];
?>'>

<center>

<table border='0' cellpadding='5' cellspacing='0'>

 <tr>
  <td height="1">
  </td>
 </tr>

 <tr bgcolor='#ddddff'>
  <td>
   <b>

 <?php echo xlt('Search for:'); ?>
   <input type='text' name='search_term' size='12' value='<?php echo attr($_REQUEST['search_term']); ?>'
    title='<?php echo xla('Any part of the desired code or its description'); ?>' />
   &nbsp;
   <input type='submit' name='bn_search' value='<?php echo xla('Search'); ?>' />
   &nbsp;&nbsp;&nbsp;
   <input type='button' value='<?php echo xla('Erase'); ?>' onclick="selcode(0)" />
   </b>
  </td>
 </tr>

 <tr>
  <td height="1">
  </td>
 </tr>

</table>

<?php if ($_REQUEST['bn_search']) { ?>

<table border='0'>
 <tr>
  <td><b><?php echo xlt('Code'); ?></b></td>
  <td><b><?php echo xlt('Description'); ?></b></td>
 </tr>
<?php
  $search_term = '%' . $_REQUEST['search_term'] . '%';

  $query = "SELECT procedure_type_id, procedure_code, name " .
    "FROM procedure_type WHERE " .
    "lab_id = ? AND " .
    "procedure_type LIKE 'ord' AND " .
    "activity = 1 AND " .
    "(procedure_code LIKE ? OR name LIKE ?) " .
    "ORDER BY seq, procedure_code";

  // echo "<!-- $query $labid $search_term -->\n"; // debugging

  $res = sqlStatement($query, array($labid, $search_term, $search_term));

  while ($row = sqlFetchArray($res)) {
    $itertypeid = $row['procedure_type_id'];
    $itercode = $row['procedure_code'];
    $itertext = trim($row['name']);
    $anchor = "<a href='' onclick='return selcode(" .
      "\"" . $itertypeid . "\")'>";
    echo " <tr>";
    echo "  <td>$anchor" . text($itercode) . "</a></td>\n";
    echo "  <td>$anchor" . text($itertext) . "</a></td>\n";
    echo " </tr>";
  }
?>
</table>

<?php } ?>

</center>
</form>
</body>
</html>
