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
require_once("../library/formdata.inc.php");
require_once("../library/globals.inc.php");
?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta name="HandheldFriendly" content="true">
<title>Patient Center Batch</title>
<link rel='stylesheet' type='text/css' href='../interface/main/css/jquery.dataTables.css'>
<link rel='stylesheet' type='text/css' href='../interface/main/css/dataTables.tableTools.css'>
<link rel='stylesheet' type='text/css' href='../interface/main/css/dataTables.colVis.css'>
<link rel='stylesheet' type='text/css' href='../interface/main/css/dataTables.colReorder.css'>
<script type='text/javascript' src='../interface/main/js/jquery-1.11.1.min.js'></script>
<script type='text/javascript' src='../interface/main/js/jquery.dataTables.min.js'></script>
<script type='text/javascript' src='../interface/main/js/dataTables.tableTools.js'></script>
<script type='text/javascript' src='../interface/main/js/dataTables.colReorder.js'></script>
<script type='text/javascript' src='../interface/main/js/dataTables.colVis.js'></script>
<script type='text/javascript'>
    $(document).ready(function() {
        $('#patientload').dataTable( {
        iDisplayLength: 100,
        dom: 'T<\"clear\">lfrtip',
        tableTools: {
             "sSwfPath": "../interface/swf/copy_csv_xls_pdf.swf",
            aButtons: [
                {
                    sExtends: "xls",
                    sButtonText: "Save to Excel",
                    sFileName: $('#openemrTitle').val() + " zirmed patients "+ $('#currTime').val() +".csv"
                }
            ]
        }
        } ); 
//              var responsiveHelper;
//                            var breakpointDefinition = {
//                                tablet: 1024,
//                                phone : 480
//                            };
//                            var tableElement = $('#patientload');
//                            tableElement.dataTable({
//                                autoWidth        : false,
//                                preDrawCallback: function () {
//                                    // Initialize the responsive datatables helper once.
//                                    if (!responsiveHelper) {
//                                        responsiveHelper = new ResponsiveDatatablesHelper(tableElement, breakpointDefinition);
//                                    }
//                                },
//                                rowCallback    : function (nRow) {
//                                    responsiveHelper.createExpandIcon(nRow);
//                                },
//                                drawCallback   : function (oSettings) {
//                                    responsiveHelper.respond();
//                                }
//                                
//                            });
    });     
</script>
<style>
  #ins  ul li {
  list-style-type:decimal !important;
 // display: inline-block;
}
</style>
<!--<link rel="stylesheet" href="css/dataTables.bootstrap.css"/>
<link rel="stylesheet" href="css/datatables.responsive_bootstrap.css"/>
<script src="js/responsive_datatable/jquery.min.js"></script>
<script src="js/responsive_datatable/jquery.dataTables.min.js"></script>
<script src="js/responsive_datatable/dataTables.bootstrap.js"></script>
<script src="js/responsive_datatable/datatables.responsive.js"></script>
</head>-->
<body style="background-color:#FFFFCC;">
    <strong>Instructions:</strong>
    <ul id='ins' style="list-style-type:decimal !important; ">
        <li >Click on Save to Excel</li>
        <li>Go to Control Panel of your machine. Select "Region and Language". 
            Now select "Additional Settings" Button.</li>
        <li>In "Additional Settings" popup, you see a field called "List Separator". Change that to "|".</li>
        <li>Now open the downloaded xls file and save as "CSV" file in the same location.</li>
        <li>This file is now ready for patient batch upload in Zirmed.</li>
    </ul>
  <div style="width:1128px; height:590px; overflow: scroll;">
    <table id='patientload' cellpadding='0' cellspacing='0' border='0' class='display'>
    <thead>
        <tr>
<!--            <th>Zirmed Id</th>-->
            <th data-hide='phone' data-name='Record Type'>Record Type</th>
            <th data-class='expand'>First Name</th>
            <th data-hide='phone' data-name='Middle Name'>Middle Name</th>
            <th data-hide='phone' data-name='Last Name'>Last Name</th>
            <th data-hide='phone' data-name='Suffix'>Suffix</th>
            <th data-hide='phone' data-name='Gender'>Gender</th>
            <th data-hide='phone' data-name='DOB'>DOB</th>
            <th data-hide='phone' data-name='SSN'>SSN</th>
            <th data-hide='phone' data-name='Drivers License State'>Drivers License State</th>
            <th data-hide='phone' data-name='Drivers License Number'>Drivers License Number</th>
            <th data-hide='phone' data-name='Address1' >Address1</th>
            <th data-hide='phone' data-name='Address2'>Address2</th>
            <th data-hide='phone' data-name='City'>City</th>
            <th data-hide='phone' data-name='State'>State</th>
            <th data-hide='phone' data-name='Zip'>Zip</th>
            <th data-hide='phone' data-name='Email'>Email</th>
            <th data-hide='phone' data-name='Home Phone'>Home Phone</th>
            <th data-hide='phone' data-name='Cell Phone'>Cell Phone</th>
            <th data-hide='phone' data-name='Work Phone'>Work Phone</th>
            <th data-hide='phone' data-name='Account Number'>Account Number</th>
            <th data-hide='phone' data-name='Account Balance' >Account Balance</th>
            <th data-hide='phone' data-name='Balance Date'>Balance Date</th>
            <th data-hide='phone' data-name='Marital Status'>Marital Status</th>
            <th data-hide='phone' data-name='IsActive'>IsActive</th>
        </tr>
    </thead>
    
        <?php
            $res = sqlStatement("SELECT x12_sender_id FROM x12_partners WHERE name = 'ZIRMED'");
            while ($row = sqlFetchArray($res)) {
                $domain_identifier = $row['x12_sender_id'];
            }
            $getTemp="SELECT fname,mname,lname,title,sex,DOB,street,city,state,postal_code,
                      email,phone_home,phone_cell,phone_biz,pid,status FROM patient_data";
            $showTemp=sqlStatement($getTemp);
            while($rowTemp=sqlFetchArray($showTemp))
            {
                $fname=(isset($rowTemp['fname'])) ? $rowTemp['fname'] : '';
                $mname=(isset($rowTemp['mname'])) ? $rowTemp['mname'] : '';
                $lname=(isset($rowTemp['lname'])) ? $rowTemp['lname'] : '';
                $title=(isset($rowTemp['title'])) ? $rowTemp['title'] : '';
                $sex=(isset($rowTemp['sex'])) ? $rowTemp['sex'] : '';
                $dob=(isset($rowTemp['DOB'])) ? $rowTemp['DOB'] : '';
                $street=(isset($rowTemp['street'])) ? $rowTemp['street'] : '';
                $street = str_replace(",", "", $street);
                $city=(isset($rowTemp['city'])) ? $rowTemp['city'] : '';
                $city = str_replace(",", "", $city);
                $state=(isset($rowTemp['state'])) ? $rowTemp['state'] : '';
                $postal_code=(isset($rowTemp['postal_code'])) ? $rowTemp['postal_code'] : '';
                $email=(isset($rowTemp['email'])) ? $rowTemp['email'] : '';
                $phone_home=(isset($rowTemp['phone_home'])) ? $rowTemp['phone_home'] : '';
                $phone_cell=(isset($rowTemp['phone_cell'])) ? $rowTemp['phone_cell'] : '';
                $phone_biz=(isset($rowTemp['phone_biz'])) ? $rowTemp['phone_biz'] : '';
                $pid=(isset($rowTemp['pid'])) ? $rowTemp['pid'] : '';
                $status=(isset($rowTemp['status'])) ? $rowTemp['status'] : '';
                
                if($sex == 'Male') $sex = 1;
                if($sex == 'Female') $sex = 2;
                if($sex == '') $sex = 0;
                
                $formatedDate = date('m-d-Y',strtotime($dob));
                
                if(strtolower($status) == 'single') $status = 1;
                if(strtolower($status) == 'married') $status = 2;
                if(strtolower($status) == 'separated') $status = 3;
                if(strtolower($status) == 'divorced') $status = 4;
                if(strtolower($status) == 'widowed') $status = 5;
                                
                echo "<tr>";
                //echo "<td>$domain_identifier</td><td>Patient</td><td>$fname</td><td>$mname</td><td>$lname</td><td>$title</td>";
                echo "<td>Patient</td><td>$fname</td><td>$mname</td><td>$lname</td><td>$title</td>";
                echo "<td>$sex</td><td>$formatedDate</td><td></td><td></td><td></td>";
                echo "<td>$street</td><td></td><td>$city</td><td>$state</td><td>$postal_code</td>";
                echo "<td>$email</td><td>$phone_home</td><td>$phone_cell</td><td>$phone_biz</td>";
                echo "<td>$pid</td><td></td><td></td><td>$status</td><td>True</td>";
                echo "</tr>";
            }    
        ?>
    
    </table>
    <input type='hidden' id='openemrTitle' value='<?php echo text($openemr_name); ?>' />
    <input type='hidden' id='currTime' value='<?php echo time(); ?>' />
    </div>

</body>
</html>