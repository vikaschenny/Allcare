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

$pagename = "eligibility_response"; 

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
$encounter  = $_REQUEST['encounter'];
?>
<html>
    <head>
        <link rel='stylesheet' type='text/css' href='../interface/main/css/jquery.dataTables.css'>
        <link rel='stylesheet' type='text/css' href='../interface/main/css/dataTables.tableTools.css'>
        <link rel='stylesheet' type='text/css' href='../interface/main/css/dataTables.colVis.css'>
        <link rel='stylesheet' type='text/css' href='../interface/main/css/dataTables.colReorder.css'>
        <style>
        div.DTTT_container {
                float: none;
        }
        </style>
        <script type='text/javascript' src='../interface/main/js/jquery-1.11.1.min.js'></script>
        <script type='text/javascript' src='../interface/main/js/jquery.dataTables.min.js'></script>
        <script type='text/javascript' src='../interface/main/js/dataTables.tableTools.js'></script>
        <script type='text/javascript' src='../interface/main/js/dataTables.colReorder.js'></script>
        <script type='text/javascript' src='../interface/main/js/dataTables.colVis.js'></script>
         <script type='text/javascript'>
            $(document).ready( function () {
                $('#eligibility').DataTable( {
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
        <script type="text/javascript">
            function datafromchildwindow(id,pid) {
                setInterval(function(){location.href = 'patients_eligibility_response.php?id='+id.trim()+'&pid='+pid+"&provider=<?php echo $provider; ?>"; },1000);
            }
            function viewScreen(pid,form_id){
                window.open("view_eligibility_response_data.php?pid="+pid+"&form_id="+form_id+"&provider=<?php echo $provider; ?>", "", "width=600,height=600,top=0,scrollbars=1,resizable=1");
            }
            function editScreen(pid,form_id){
                window.open("edit_eligibility_response_data.php?pid="+pid+"&form_id="+form_id+"&provider=<?php echo $provider; ?>", "", "width=600,height=600,top=0,scrollbars=1,resizable=1");
            }
        </script>
    </head>
    <body>
       <table border='1' id='eligibility' class='display'>
            <thead>
                <tr>
                    <?php 
                    $get_fields = '';
                    $getFields = sqlStatement("SELECT field_id,title FROM layout_options WHERE form_id='ELIGIBILITY' AND uor <> 0 ORDER BY group_name, seq"); 
                    echo "<th></th>";
                    while($rowfields = sqlFetchArray($getFields)){
                        echo "<th>".$rowfields['title']."</th>";
                        $get_fields .= "`".$rowfields['field_id']."`,";
                    }
                     
                     ?>
                </tr>
            </thead>
            <tbody>
             <?php 
                //get user custom attributes
                $custom=sqlStatement("select editEligibility from tbl_user_custom_attr_1to1 where userid=".$_SESSION['portal_userid']);
                $cdata=sqlFetchArray($custom);
                $viewedit = 'viewScreen';
                $vieweditLabel = 'View';
                if(strtolower($cdata['editEligibility']) == 'yes'):
                    $viewedit = 'editScreen';
                    $vieweditLabel = 'Edit';
                endif;
                $get_fields_names = rtrim($get_fields,",");
                $sql=sqlStatement("select `id`,$get_fields_names from tbl_eligibility_response_data where pid=$pid ORDER BY updated_date DESC"); 
                while($row=sqlFetchArray($sql)){
                    echo "<tr>";
                    foreach($row as $key => $value){
                        if($key == 'id')
                            echo "<td><a href='#' onclick='return $viewedit($pid,".$row['id'].");'> $vieweditLabel </a></td>";
                        else
                            echo "<td>$value</td>";
                    }
                    echo "</tr>";
                }
            ?>
            </tbody>
        </table>
    </body>
</html>
