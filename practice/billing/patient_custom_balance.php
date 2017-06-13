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

require_once("../verify_session.php");
require_once("$srcdir/patient.inc");

$pid             = $_REQUEST['pid'];

?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<ul class="tabNav" id="patientbalancebar">
    <li><a onclick = 'getpatientcheckinfo(this,"visitcollection",event)'>Visit Collection</a></li>
    <li><a onclick = 'getpatientcheckinfo(this,"patientbalancesummary",event)'>Patient Balance Summary</a></li>
</ul>