<style type="text/css">
#details {
	font-family: verdana,arial,sans-serif;
	font-size:11px;
	color:#333333;
	border-width: 1px;
	border-color: #999999;
	border-collapse: collapse;
}
#details th {
	background-color:#c3dde0;
	border-width: 1px;
	padding: 8px;
	border-style: solid;
	border-color: #a9c6c9;
}
#details tr {
	background-color:#ffffcc;
}
#details td {
	border-width: 1px;
	padding: 8px;
	border-style: solid;
	border-color: #a9c6c9;
}
</style>
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
include_once("$srcdir/api.inc");
include_once("$srcdir/forms.inc");
include_once("$srcdir/acl.inc");


$formid=$_REQUEST['formid'];
//echo $formid;
 
echo "<div id='details'>";
if($formid!='' && $formid!='undefined'){
//echo "select  * from tbl_allcare_formflag where form_id='$formid' AND form_name='Allcare Physical Exam'";
        $res1=sqlstatement("select  * from tbl_allcare_formflag where form_id='$formid' AND form_name='Allcare Physical Exam'");
       // $rows=sqlFetchArray($res1);
       
}      
   
   echo "<table id='details' border='1'>";
   echo  "<tr>
            <th style='width:20'>UserName</th>
            <th style='width:20'>Pending</th>
            <th style='width:20'>Finalized</th>
            <th style='width:20'>Date</th>
            <th style='width:20'>Action</th>
            <th style='width:20'>IP Address</th>
          </tr>";
   while($rows=sqlFetchArray($res1))   
   {
        $array=unserialize($rows['logdate']);
        $count = count($array);
        foreach ($array as $value) {
            ?>
            <tr>
                <td style="width:20"><?php echo $value['authuser'];?></td>
                <td style="width:20"><?php echo $value['pending'];?></td>
                <td style="width:20"><?php echo $value['finalized']; ?></td>
                <td style="width:20"><?php echo $value['date']; ?></td>
                <td style="width:20"><?php echo $value['action']; ?></td>
                <td style="width:20"><?php echo $value['ip_address']; ?></td>
           </tr>
<?php } 
   }
echo "</table>";
echo "</div>";
?>