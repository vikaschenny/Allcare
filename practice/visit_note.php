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
require_once("verify_session.php");

$pagename = "Visit Note"; 

require_once("$srcdir/api.inc");
require_once("$srcdir/forms.inc");
require_once("$srcdir/options.inc.php");
require_once("$srcdir/patient.inc");
require_once("$srcdir/formatting.inc.php");
require_once("$srcdir/billing.inc");
include_once("$srcdir/calendar.inc");
include_once("$srcdir/edi.inc");


//for logout

$sql = sqlStatement("SELECT id, fname, lname, specialty FROM users " .
      "WHERE active = 1 AND ( info IS NULL OR info NOT LIKE '%Inactive%' ) " .
      "AND authorized = 1 AND username='".$provider."'" .
      "ORDER BY lname, fname");
$id         = sqlFetchArray($sql);

$pid           = $_REQUEST['pid'];
$encId         = $_REQUEST['encId'];
$visitNote         = $_REQUEST['visitNote'];

if(isset($visitNote)):
    sqlStatement("INSERT INTO tbl_allcare_frontpaymentlog (pid,encid,visitnote,updatedby,updatedon) 
                  VALUES(".$pid.",".$encId.",'".$visitNote."','".$provider."',NOW())");
endif;
?>
<html>
    <head>
        <link rel='stylesheet' type='text/css' href="assets/css/bootstrap.min.css">
        <link rel="stylesheet" href="css/version1.0/dataTables.bootstrap.min.css"/>
        <link rel="stylesheet" href="css/version1.0/responsive.bootstrap.min.css"/>
        <style>
        div.DTTT_container {
                float: none;
        }
        </style>
        <script type='text/javascript' src='../interface/main/js/jquery-1.11.1.min.js'></script>
        <script src="js/responsive_datatable/version1.0/jquery.dataTables.min.js"></script>
        <script src="js/responsive_datatable/version1.0/dataTables.bootstrap.min.js"></script>
        <script src="js/responsive_datatable/version1.0/dataTables.responsive.min.js"></script>
        <script type='text/javascript' src='js/responsive_datatable/dataTables.tableTools.js'></script>
        <script type='text/javascript' src='js/responsive_datatable/dataTables.bootstrap.js'></script>     
         <script type='text/javascript'>
            $(document).ready( function () {
                $('#visitnote').DataTable( {
                    dom: 'T<"clear">lfrtip',
                    "tableTools": {
                        "sSwfPath": "../interface/swf/copy_csv_xls_pdf.swf",
                        "aButtons": [
                            {
                                "sExtends": "xls",
                                "sButtonText": "Save to Excel"
                            }
                        ]
                    }
                } );
               
            } );
    </script>
    </head>
    <body>
        <form method="POST">
            <div class="form-group">
               <label for="visitnote">Visit Note:</label>
               <textarea class="form-control" rows="5" cols="5" name="visitNote"></textarea>
            </div>
            <input type="hidden" name="pid" value="<?php echo $pid; ?>"/>
            <input type="hidden" name="encId" value="<?php echo $encId; ?>"/>
            <input type="submit" value="Submit" class="btn btn-primary">
        </form>
            
       <table id='visitnote' class='table table-striped table-bordered dt-responsive nowrap'  cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>Patient Name</th>
                    <th>Encounter ID</th>
                    <th>Visit Note</th>
                    <th>Visit Note Updated By</th>
                    <th>Visit Note Updated On</th>
                </tr>
            </thead>
            <tbody>
             <?php 
                if(!empty($pid) && !empty($encId)):
                    $sql=sqlStatement("select p.fname,p.lname, fl.encid, fl.visitnote, fl.updatedby, fl.updatedon from tbl_allcare_frontpaymentlog fl INNER JOIN patient_data p
                                   ON fl.pid = p.pid where p.pid=$pid AND fl.encid=$encId ORDER BY updatedon DESC"); 
                    while($row=sqlFetchArray($sql)){
                        echo "<tr>";
                        echo "<td>".$row['fname']. " " . $row['lname']."</td>";
                        echo "<td>".$row['encid']."</td>";
                        echo "<td>".$row['visitnote']."</td>";
                        echo "<td>".$row['updatedby']."</td>";
                        echo "<td>".$row['updatedon']."</td>";
                        echo "</tr>";
                    }
                endif;
            ?>
            </tbody>
        </table>
    </body>
</html>
