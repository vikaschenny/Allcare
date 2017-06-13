<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

require_once("../globals.php");
require_once("../../library/formdata.inc.php");
require_once("../../library/globals.inc.php");
?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta name="HandheldFriendly" content="true">
<title>Patient Center Batch</title>
<link rel='stylesheet' type='text/css' href='css/jquery.dataTables.css'>
<link rel='stylesheet' type='text/css' href='css/dataTables.tableTools.css'>
<link rel='stylesheet' type='text/css' href='css/dataTables.colVis.css'>
<link rel='stylesheet' type='text/css' href='css/dataTables.colReorder.css'>
<script type='text/javascript' src='js/jquery-1.11.1.min.js'></script>
<script type='text/javascript' src='js/jquery.dataTables.min.js'></script>
<script type='text/javascript' src='js/dataTables.tableTools.js'></script>
<script type='text/javascript' src='js/dataTables.colReorder.js'></script>
<script type='text/javascript' src='js/dataTables.colVis.js'></script>
<script type='text/javascript'>
    $(document).ready(function() {
        $('#patientload').dataTable( {
        iDisplayLength: 100,
        dom: 'T<\"clear\">lfrtip',
        tableTools: {
            aButtons: [
                {
                    sExtends: "xls",
                    sButtonText: "Save to Excel",
                    sFileName: $('#openemrTitle').val() + " zirmed patients "+ $('#currTime').val() +".csv"
                }
            ]
        }
        } ); 
    });     
</script>
</head>
<body style="background-color:#FFFFCC;">
    <strong>Instructions:</strong>
    <ul style="list-style-type:decimal;">
        <li>Click on Save to Excel</li>
        <li>Go to Control Panel of your machine. Select "Region and Language". 
            Now select "Additional Settings" Button.</li>
        <li>In "Additional Settings" popup, you see a field called "List Separator". Change that to "|".</li>
        <li>Now open the downloaded xls file and save as "CSV" file in the same location.</li>
        <li>This file is now ready for patient batch upload in Zirmed.</li>
    </ul>
    <div style="width:1350px; height:590px; overflow: scroll;">
    <table id='patientload' class='display' cellspacing='0' width='100%'>
    <thead>
        <tr>
            <th>Record Type</th>
            <th>First Name</th>
            <th>Middle Name</th>
            <th>Last Name</th>
            <th>Suffix</th>
            <th>Gender</th>
            <th>DOB</th>
            <th>SSN</th>
            <th>Drivers License State</th>
            <th>Drivers License Number</th>
            <th>Address1</th>
            <th>Address2</th>
            <th>City</th>
            <th>State</th>
            <th>Zip</th>
            <th>Email</th>
            <th>Home Phone</th>
            <th>Cell Phone</th>
            <th>Work Phone</th>
            <th>Account Number</th>
            <th>Account Balance</th>
            <th>Balance Date</th>
            <th>Marital Status</th>
            <th>IsActive</th>
        </tr>
    </thead>
    
        <?php
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
                $city=(isset($rowTemp['city'])) ? $rowTemp['city'] : '';
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