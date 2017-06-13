<?php
// Copyright (C) 2008-2010 Rod Roark <rod@sunsetsystems.com>
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.

//SANITIZE ALL ESCAPES
$sanitize_all_escapes=true;
//
//STOP FAKE REGISTER GLOBALS
$fake_register_globals=false;
//
include_once("../../globals.php");
require_once("$srcdir/options.inc.php");
include_once("$srcdir/patient.inc");
$template_file = "print.html";
require_once("$srcdir/globals.inc.php");
require_once("$srcdir/classes/CouchDB.class.php");
require_once("$srcdir/report.inc");
require_once("$srcdir/html2pdf/html2pdf.class.php");

$TEMPLATE_LABELS = array(
  'patient basic information' => htmlspecialchars( xl('Patient Basic Information')),
);
$s = '';
$fh = fopen($template_file, 'r');
while (!feof($fh)) $s .= fread($fh, 8192);
fclose($fh);

$gethomecareprovider = sqlStatement("SELECT hhagency FROM patient_data WHERE pid=".$_REQUEST['pid']);
while($sethomecareprovider = sqlFetchArray($gethomecareprovider)){
    $hhagency = $sethomecareprovider['hhagency'];
}
if($hhagency != ''){
    $gethhagencyname = sqlStatement("SELECT CONCAT(fname,' ', lname) as name FROM users WHERE id = $hhagency");
    while($sethhagencyname = sqlFetchArray($gethhagencyname)){
        $hhagency_name = $sethhagencyname['name'];
    }
}else{
    $hhagency_name = "None";
}
$getdos = sqlStatement("SELECT DATE(date) as date FROM form_encounter WHERE encounter=".$_REQUEST['encounter']);
while($setdos = sqlFetchArray($getdos)){
    $dos = $setdos['date'];
}
echo '<html>
<head>
    <table>
        <tr>
            <td>
                <p align="left"><img id="img_sign" src="../../../images/tphc.jpg" width="200" height="93" /></p> 
            </td>
            <td><p align="center">2925 Skyway Circle North, Irving, TX 75038, Tel: 972 675 7313  Fax : 972 675 7310   <br>
                 www.texashousecalls.com    email: hhsupport@texashousecalls.com </p>
            </td>
        </tr>
    </table>
</head>
<body>
<article>
    <header>
        <h3 align="center">Care Plan Oversight Log</h3>
    </header>
</article>
<div>'; 
echo "<table border='0'>";
echo "<tr><td>Patient Name: <b>".$_REQUEST['patientname']."</b></td></tr>";
echo "<tr><td>Home Care Medicare Provider: <b>".$hhagency_name."</b></td></tr>";
echo "<tr><td>Date of Service: <b>".$dos."</b></td></tr>";
echo "<tr><td>";
?>
<table  border="1" style ="border-collapse: collapse;">
    <thead>
        <tr>
           <th> Type of Oversight </th>
           <th> Date</th>
           <th> Minutes </th>
           <th> Description </th>
           <th> Reference </th>
        </tr>
    </thead>
    <tbody>
        <?php for($i=1; $i<= $_REQUEST['noofrows']; $i++){ 
            if($i == 1):
                $cpotype     = "cpotype";
                $reference   = "reference";
                $description = "description";
                $start_date  = "start_date";
                $img_start_date    = "img_start_date";
                $timeinterval    = "timeinterval";
            else:
                $cpotype     = "cpotype".$i;
                $reference   = "reference".$i;
                $description = "description".$i;
                $start_date  = "start_date".$i;
                $img_start_date    = "img_start_date".$i;
                $timeinterval    = "timeinterval".$i;
            endif;
            ?>
            <tr>
                <?php 
                $ures = sqlStatement("SELECT title FROM list_options WHERE option_id= '".$_REQUEST[$timeinterval]."' and list_id = 'Time_Interval'");
                while ($urow = sqlFetchArray($ures)) {
                    $minutes = $urow['title'];
                }
                if($minutes == ''):
                    $minutes = 0;
                endif;
                $sql = sqlStatement ("SELECT title FROM list_options WHERE option_id= '".$_REQUEST[$cpotype]."' AND list_id = 'CPO_types'");
                while ($row = sqlFetchArray($sql)) {
                    $type = $row['title'];
                }
                if($type == ''):
                    $type = 0;
                endif;
                ?>
                <td><?php echo $type; ?> </td>
                <td><?php echo $_REQUEST[$start_date]; ?> </td>
                <td><?php echo $minutes; ?> </td>
                <td><?php echo $_REQUEST[$description]; ?> </td>
                <td><?php echo $_REQUEST[$reference]; ?> </td>
            </tr>
        <?php } ?>
            <tr>
                <td colspan="5" align="right" ><b>Total: <?php echo $_REQUEST['totaltime']." minutes"; ?></b></td>
            </tr>
    </tbody>
</table>
<?php 
echo "</td></tr>";
if($_REQUEST['provider_id'] != ''){
    $resSignImg=sqlStatement("SELECT CONCAT(fname, ' ' , lname ) as providername,signature_image FROM users WHERE id=".$_REQUEST['provider_id']);
    $row=sqlFetchArray($resSignImg);
    $providername = $row['providername'];
    $newval="../../pic/user_sign/".$row['signature_image'];
    $signed_date = " </b>On <b> ".$_REQUEST['signed_date'] ;
}else{
    $providername = 'None';
    $newval = '';
    $signed_date = '';
}
echo "<tr><td>NP/Physician: Electronically Signed by: <b>".$providername .$signed_date ."</b></td></tr>";
echo "<tr><td>Physician Signature:<p align='left'><img id='img_sign' src='$newval' /></p> </td></tr>";

echo "</div></body></html>";
//echo "</body>";
$logo="../../../images/tphc.jpg";
$s = str_replace("{logo}", $logo, $s);

foreach ($TEMPLATE_LABELS as $key => $value) {
  $s = str_replace("{".$key."}", $value, $s);
}

echo $s;

?>
