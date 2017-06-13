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

$pagename = "patient_insurance_history"; 

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
//$pid        = $_REQUEST['pid'];

?>
<html>
    <head>
        <link rel='stylesheet' type='text/css' href="assets/css/bootstrap.min.css">
        <link rel='stylesheet' type='text/css' href="css/version1.0/dataTables.bootstrap.min.css">
        <link rel='stylesheet' type='text/css' href="css/version1.0/responsive.bootstrap.min.css">
        <style>
            .DTTT.btn-group{
                float: right;
                padding-left: 13px;
                position: relative;
            }
            #insHistory_length{
                float:left;
            }
           .costmizecolumns {
                margin-bottom: 7px;
                margin-top: 13px;
                text-align: center;
                width:220px;
                margin-left: 35%;

            }
            @media only screen and (max-width: 1024px){
                .costmizecolumns {
                    margin-left: 28%;
                }
            }
           @media only screen and (min-width: 800px){
                .costmizecolumns {
                    position: relative;
                    top:33px;
                    margin-right: 113px;
                }
            }

            @media only screen and (max-width: 768px){
                .DTTT.btn-group{
                    float: none;
                    margin-bottom: 6px;
                    padding-left: 40%;
                    position: relative;
                }
                #vnfFilter1_length{
                    float:none;
                }
                .costmizecolumns {
                    margin-bottom: 7px;
                    margin-top: 13px;
                    text-align: center;
                    width:auto;
                    margin-left: 0;
                }
            }
            body{overflow-x: hidden;}
        </style>
        <script type='text/javascript' src='js/responsive_datatable/version1.0/jquery-1.11.3.min.js'></script>
        <script type='text/javascript' src='js/responsive_datatable/version1.0/jquery.dataTables.min.js'></script>
        <script type='text/javascript' src='js/responsive_datatable/version1.0/dataTables.bootstrap.min.js'></script>
        <script type='text/javascript' src='js/responsive_datatable/version1.0/dataTables.responsive.min.js'></script>
        <script type='text/javascript' src='js/responsive_datatable/dataTables.tableTools.js'></script>
        <script type='text/javascript' src='js/responsive_datatable/dataTables.bootstrap.js'></script>
         <script type='text/javascript'>
            $(document).ready( function () {
//                $('#insHistory').DataTable( {
//                    dom: 'T<"clear">lfrtip',
//                    "tableTools": {
//                        "sSwfPath": "../interface/swf/copy_csv_xls_pdf.swf",
//                        "aButtons": [
//                            {
//                                "sExtends": "xls",
//                                "sButtonText": "Save to Excel"
//                            }
//                        ]
//                    }
//                } );
                var table = $('#insHistory').DataTable({
                         dom: 'T<\"clear\">lfrtip',
                         "iDisplayLength": 10,
                       tableTools: {
                            "sSwfPath": "../interface/swf/copy_csv_xls_pdf.swf",
                            aButtons: [
                                {
                                    sExtends: "xls",
                                    sButtonText: "Save to Excel",
                                }
                            ]
                        }
                    });
               
            } );
    </script>
    </head>
    <body>
       <table border='1' id='insHistory' class='table table-striped table-bordered dt-responsive nowrap' cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>Insurance Company</th>
                    <th>Type</th>
                    <th>Policy Number</th>
                    <th>Plan Name</th>
                    <th>Patient Name</th>
                    <th>Effective Date</th>
                    <th>Created Date</th>
                    <th>Updated By</th>
                </tr>
            </thead>
            <tbody>
             <?php 
                $query = sqlStatement("SELECT * FROM insurance_companies ins 
                                       INNER JOIN tbl_patient_insurance_history insht ON ins.id = insht.emr_payer_id 
                                       INNER JOIN patient_data pd ON pd.pid = insht.pid WHERE insht.pid=".$pid);
                        
                while($row = sqlFetchArray($query)):
             ?>
                    <tr>
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo $row['type']; ?></td>
                    <td><?php echo $row['policy_number']; ?></td>
                    <td><?php echo $row['plan_name']; ?></td>
                    <td><?php echo $row['fname'] . " " . $row['lname']; ?></td>
                    <td><?php echo $row['effective_date']; ?></td>
                    <td><?php echo $row['created_date']; ?></td>
                    <td><?php echo $row['user']; ?></td>
                    </tr>
             <?php
                endwhile;
             ?>
            </tbody>
        </table>
    </body>
</html>
