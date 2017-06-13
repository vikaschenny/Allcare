<?php
/**
 * Copyright (C) 2010 OpenEMR Support LLC
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * 2013/02/08 Minor tweaks by EMR Direct to allow integration with Direct messaging
 * 2013-03-27 by sunsetsystems: Fixed some weirdness with assigning a message recipient,
 *   and allowing a message to be closed with a new note appended and no recipient.
 */
//SANITIZE ALL ESCAPES
$sanitize_all_escapes=true;

//STOP FAKE REGISTER GLOBALS
$fake_register_globals=false;

require_once("../../globals.php");
?>
<html>
<head>

<?php html_header_show();?>
<link rel="stylesheet" href="<?php echo $css_header;?>" type="text/css">
<script type="text/javascript" src="../../../library/dialog.js"></script>
<script type="text/javascript" src="../../../library/textformat.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/jquery.js"></script>
</head>
<body class="body_top">
<span class="title"><?php echo xlt('Reports'); ?></span>
<br /><br />
<span class="title"><a href="../appt-enc-report.php" target="_blank"><?php echo xlt('Patient Appointments and Encounters'); ?></a></span>
<br /><br />
<span class="title"><a href="patient1to1.php" target="_blank"><?php echo xlt('Patient 1to1'); ?></a></span>
<br /><br />
<span class="title"><a href="patient1ton.php" target="_blank"><?php echo xlt('Patient 1ton'); ?></a></span>
<br /><br />
<span class="title"><a href="patient_enc_list.php" target="_blank"><?php echo xlt('Patient Encounter'); ?></a></span>
<br /><br />
<span class="title"><a href="patientappt.php" target="_blank"><?php echo xlt('Patient Due Appointments'); ?></a></span>
<br /><br />
<span class="title"><a href="patientapptment.php" target="_blank"><?php echo xlt('Patient Appointments'); ?></a></span>
<br /><br />
<span class="title"><a href="patientencforms.php" target="_blank"><?php echo xlt('Patient Encounter Forms'); ?></a></span>
<br /><br />
<span class="title"><a href="masterdata.php" target="_blank"><?php echo xlt('Master Data'); ?></a></span>
<br /><br />
<span class="title"><a href="settingsdata.php" target="_blank"><?php echo xlt('Settings'); ?></a></span>
<br /><br />
</body>
</html>
