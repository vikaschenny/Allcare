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
 include_once("$srcdir/acl.inc");

 $line_id = $_REQUEST['lineid'];
 $info_msg = "";

 if ($issue && !acl_check('patients', 'med','','write')) die("Edit is not authorized!");
?>
<html>
<head>
<?php html_header_show();?>
<title><?php xl('Edit Diagnoses for','e');?><?php echo $line_id ?></title>
<link rel="stylesheet" href='<?php  echo $css_header ?>' type='text/css'>

<script language="JavaScript">
</script>

</head>

<body class="body_top">
<?php
 // If we are saving, then save and close the window.
 //
 if ($_POST['form_save']) {
  $query = "DELETE FROM tbl_form_physical_exam_diagnoses WHERE line_id = '$line_id'";
  sqlStatement($query);

  $form_diagnoses = $_POST['form_diagnosis'];
  $form_orderings = $_POST['form_ordering'];
  foreach ($form_diagnoses as $i => $diagnosis) {
   if ($diagnosis) {
    $ordering = $form_orderings[$i];
    $query = "INSERT INTO tbl_form_physical_exam_diagnoses ( " .
     "line_id, ordering, diagnosis " .
     ") VALUES ( " .
     "'$line_id', '$ordering', '$diagnosis' " .
     ")";
    sqlInsert($query);
   }
  }

  // Close this window and redisplay the updated encounter form.
  //
  echo "<script language='JavaScript'>\n";
  if ($info_msg) echo " alert('$info_msg');\n";
  echo " window.close();\n";
  echo " opener.location.reload();\n";
  //echo " if (opener.refreshme) opener.refreshme();\n";
  echo "</script></body></html>\n";
  exit();
 }

 $dres = sqlStatement(
  "SELECT * FROM tbl_form_physical_exam_diagnoses WHERE " .
  "line_id = '$line_id' ORDER BY ordering, diagnosis"
 );
?>
<form method='post' name='theform' action='edit_diagnoses.php?lineid=<?php  echo $line_id ?>'
 onsubmit='return top.restoreSession()'>
<center>

<table border='0' width='100%'>

 <tr>
  <td width='5%'><?php xl('Order','e'); ?></td>
  <td width='95%'><?php xl('Diagnosis','e'); ?></td>
 </tr>

<?php for ($i = 1; $drow = sqlFetchArray($dres); ++$i) { ?>
 <tr>
  <td><input type='text' size='3' maxlength='5' name='form_ordering[<?php echo $i?>]' value='<?php echo $i?>' /></td>
  <td><input type='text' size='20' maxlength='250' name='form_diagnosis[<?php echo $i?>]' value='<?php echo $drow['diagnosis'] ?>' style='width:100%' /></td>
 </tr>
<?php } ?>

<?php for ($j = 0; $j < 5; ++$j, ++$i) { ?>
 <tr>
  <td><input type='text' size='3' name='form_ordering[<?php echo $i?>]' value='<?php echo $i?>' /></td>
  <td><input type='text' size='20' name='form_diagnosis[<?php echo $i?>]' style='width:100%' /></td>
 </tr>
<?php } ?>

</table>

<p>
<input type='submit' name='form_save' value='<?php xl('Save','e'); ?>' />

&nbsp;
<input type='button' value='<?php xl('Cancel','e'); ?>' onclick='window.close()' />
</p>

</center>
</form>
</body>
</html>
