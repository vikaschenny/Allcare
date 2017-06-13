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
require_once($GLOBALS['srcdir'].'/patient.inc');
require_once($GLOBALS['srcdir'].'/csv_like_join.php');
require_once($GLOBALS['fileroot'].'/custom/code_types.inc.php');

$info_msg = "";
$codetype = $_REQUEST['codetype'];
if (isset($codetype)) {
	$allowed_codes = split_csv_line($codetype);
}

$form_code_type = $_POST['form_code_type'];

// This variable is used to store the html element
// of the target script where the selected code
// will be stored in.
$target_element = $_GET['target_element'];
?>
<html>
<head>
<?php html_header_show(); ?>
<title><?php echo xlt('Code Finder'); ?></title>
<link rel="stylesheet" href='<?php echo attr($css_header) ?>' type='text/css'>

<style>
td { font-size:10pt; }
</style>

<script language="JavaScript">

 // Standard function
 function selcode(codetype, code, selector, codedesc) {
  if (opener.closed || ! opener.set_related)
   alert('<?php echo addslashes( xl('The destination form was closed; I cannot act on your selection.') ); ?>');
  else
   opener.set_related(codetype, code, selector, codedesc);
  window.close();
  return false;
 }

 // Standard function with additional parameter to select which
 // element on the target page to place the selected code into.
 function selcode_target(codetype, code, selector, codedesc, target_element) {
  if (opener.closed || ! opener.set_related_target)
   alert('<?php echo addslashes( xl('The destination form was closed; I cannot act on your selection.') ); ?>');
  else
   opener.set_related_target(codetype, code, selector, codedesc, target_element);
  window.close();
  return false;
 }

</script>

</head>

<body class="body_top" OnLoad="document.theform.search_term.focus();">

<?php
$string_target_element = "";
if (!empty($target_element)) {
$string_target_element = "?target_element=".attr($target_element)."&";
}
else {
$string_target_element = "?";
}
?>
<?php if (isset($allowed_codes)) { ?>
  <form method='post' name='theform' action='find_code_popup.php<?php echo $string_target_element ?>codetype=<?php echo attr($codetype) ?>'>
<?php } else { ?>
  <form method='post' name='theform' action='find_code_popup.php<?php echo $string_target_element ?>'>
<?php } ?>

<center>

<table border='0' cellpadding='5' cellspacing='0'>

 <tr>
  <td height="1">
  </td>
 </tr>

 <tr bgcolor='#ddddff'>
  <td>
   <b>

<?php
if (isset($allowed_codes)) {
	if (count($allowed_codes) === 1) {
  echo "<input type='text' name='form_code_type' value='" . attr($codetype) . "' size='5' readonly>\n";
	} else {
?>
   <select name='form_code_type'>
<?php
		foreach ($allowed_codes as $code) {
			$selected_attr = ($form_code_type == $code) ? " selected='selected'" : '';
?>
   	<option value='<?php echo attr($code) ?>'<?php echo $selected_attr?>><?php echo xlt($code_types[$code]['label']) ?></option>
<?php
		}
?>
   </select>
<?php
	}
}
else {
  echo "   <select name='form_code_type'";
  echo ">\n";
  foreach ($code_types as $key => $value) {
    echo "    <option value='" . attr($key) . "'";
    if ($codetype == $key || $form_code_type == $key) echo " selected";
    echo ">" . xlt($value['label']) . "</option>\n";
  }
  echo "    <option value='PROD'";
  if ($codetype == 'PROD' || $form_code_type == 'PROD') echo " selected";
  echo ">" . xlt("Product") . "</option>\n";
  echo "   </select>&nbsp;&nbsp;\n";
}
?>

 <?php echo xlt('Search for:'); ?>
   <input type='text' name='search_term' size='12' value='<?php echo attr($_REQUEST['search_term']); ?>'
    title='<?php echo xla('Any part of the desired code or its description'); ?>' />
   &nbsp;
   <input type='submit' name='bn_search' value='<?php echo xla('Search'); ?>' />
   &nbsp;&nbsp;&nbsp;
   <?php if (!empty($target_element)) { ?>
     <input type='button' value='<?php echo xla('Erase'); ?>' onclick="selcode_target('', '', '', '', '<?php echo attr(addslashes($target_element)); ?>')" />
   <?php } else { ?>
     <input type='button' value='<?php echo xla('Erase'); ?>' onclick="selcode('', '', '', '')" />
   <?php } ?>
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
  $search_term = $_REQUEST['search_term'];
  $res = main_code_set_search($form_code_type,$search_term);

  if ($form_code_type == 'PROD') { // Special case that displays search for products/drugs
    while ($row = sqlFetchArray($res)) {
      $drug_id = $row['drug_id'];
      $selector = $row['selector'];
      $desc = $row['name'];
      $anchor = "<a href='' " .
        "onclick='return selcode(\"PROD\", \"" . attr(addslashes($drug_id)) . "\", \"" . attr(addslashes($selector)) . "\", \"" . attr(addslashes($desc)) . "\")'>";
      echo " <tr>";
      echo "  <td>$anchor" . text($drug_id.":".$selector) . "</a></td>\n";
      echo "  <td>$anchor" . text($desc) . "</a></td>\n";
      echo " </tr>";
    }
  }
  else {
    while ($row = sqlFetchArray($res)) { // Display normal search
      $itercode = $row['code'];
      $itertext = trim($row['code_text']);
      if (!empty($target_element)) {
        // add a 5th parameter to function to select the target element on the form for placing the code.
        $anchor = "<a href='' " .
          "onclick='return selcode_target(\"" . attr(addslashes($form_code_type)) . "\", \"" . attr(addslashes($itercode)) . "\", \"\", \"" . attr(addslashes($itertext)) . "\", \"" . attr(addslashes($target_element)) . "\")'>";
      }
      else {
        $anchor = "<a href='' " .
          "onclick='return selcode(\"" . attr(addslashes($form_code_type)) . "\", \"" . attr(addslashes($itercode)) . "\", \"\", \"" . attr(addslashes($itertext)) . "\")'>";
      }
      echo " <tr>";
      echo "  <td>$anchor" . text($itercode) . "</a></td>\n";
      echo "  <td>$anchor" . text($itertext) . "</a></td>\n";
      echo " </tr>";
    }
  }
?>
</table>

<?php } ?>

</center>
</form>
</body>
</html>
