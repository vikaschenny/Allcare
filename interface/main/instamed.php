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
<title>Patient Center Batch InstaMed Data</title>
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
                    sFileName: $('#openemrTitle').val()+"_InstaMed Patients Batch Upload File_"+$('#currDate').val()+"_"+$('#currTime').val()+".csv",
                    bHeader: false
                }
            ]
        }
        } ); 
    });     
</script>
</head>
<body style="background-color:#FFFFCC;">
    <strong>InstaMed Patient Batch:</strong>
    <div style="width:1350px; height:590px; overflow: scroll;  padding:10px;">
    <table id='patientload' class='display' cellspacing='0' width='100%'>
    <thead>
        <tr>
			<th style='display:none;'>Record ID</th>
	        <th>Account Number</th>
	        <th>Last Name</th>
	        <th>First Name</th>
	        <th>Middle Name</th>
	        <th style='display:none;'>Prefix</th>
	        <th style='display:none;'>Suffix</th>
	        <th>Date Of Birth (YYYYMMDD)</th>
	        <th>Gender</th>
	        <th>Address</th>
	        <th style='display:none;'>Address 2</th>
	        <th>City</th>
	        <th>State</th>
	        <th>Zipcode</th>
	        <th style='display:none;'>Zipcode 2</th>
	        <th>Phone Number</th>
	        <th style='display:none;'>Insurence Rank</th>
	        <th style='display:none;'>Insurance Name</th>
	        <th style='display:none;'>Insurance IDQualifer</th>
	        <th style='display:none;'>Insurance ID</th>
	        <th style='display:none;'>Insurance Type</th>
	        <th style='display:none;'>Insurance Filing Indicator</th>
	        <th style='display:none;'>Insurance Street1</th>
	        <th style='display:none;'>Insurance Street2</th>
	        <th style='display:none;'>Insurance City</th>
	        <th style='display:none;'>Insurance State</th>
	        <th style='display:none;'>Group ID</th>
	        <th style='display:none;'>Insurance Zip1</th>
	        <th style='display:none;'>Insurance Phone Number</th>
	        <th style='display:none;'>Relationship To Patient</th>
	        <th style='display:none;'>Policy Number</th>
	        <th style='display:none;'>Group Number</th>
	        <th style='display:none;'>Subscriber Last Name</th>
	        <th style='display:none;'>Subscriber First Name</th>
	        <th style='display:none;'>Subscriber Middle Name</th>
	        <th style='display:none;'>Subscriber Prefix</th>
	        <th style='display:none;'>Subscriber Suffix</th>
	        <th style='display:none;'>Subscriber Street1</th>
	        <th style='display:none;'>Subscriber Street2</th>
	        <th style='display:none;'>Subscriber City</th>
	        <th style='display:none;'>Subscriber State</th>
	        <th style='display:none;'>Subscriber Zip1</th>
	        <th style='display:none;'>Subscriber Zip2</th>
	        <th style='display:none;'>Subscriber PhoneNumber</th>
	        <th>IsActive</th>
	        <th style='display:none;'>Patient Balance Due</th>
	        <th style='display:none;'>Patient Balance Due Effective Date</th>
	        <th style='display:none;'>Medical Record Number</th>
	        <th>Email</th> 
        </tr>
    </thead>
    
        <?php
            /**
				     * Retriving data from patient table.
				     */
	                $getTemp="SELECT fname,mname,lname,title,sex,DOB,street,city,state,postal_code,email,phone_home,phone_cell,phone_biz,pid,status FROM patient_data";
					$showTemp=sqlStatement($getTemp);
					/**
				     * Fetching data form $showTemp and display as HTML table .
				     */
					$i=0;
				    while($rowTemp=sqlFetchArray($showTemp))
				    {
				        $fname=(isset($rowTemp['fname'])) ? $rowTemp['fname'] : '';
				        $mname=(isset($rowTemp['mname'])) ? $rowTemp['mname'] : '';
				        $lname=(isset($rowTemp['lname'])) ? $rowTemp['lname'] : '';
				        // $title=(isset($rowTemp['title'])) ? $rowTemp['title'] : '';
				        $sex=(isset($rowTemp['sex'])) ? $rowTemp['sex'] : '';
				        $dob=(isset($rowTemp['DOB'])) ? $rowTemp['DOB'] : '';
				        $street=(isset($rowTemp['street'])) ? $rowTemp['street'] : '';
				        $street = str_replace(",", "", $street);
				        $city=(isset($rowTemp['city'])) ? $rowTemp['city'] : '';
				        $city = str_replace(",", "", $city);
				        $state=((isset($rowTemp['state'])) && (strlen($rowTemp['state'])==2)) ? $rowTemp['state'] : '';
				        $postal_code=((isset($rowTemp['postal_code'])) && (strlen($rowTemp['postal_code'])==5)) ? $rowTemp['postal_code'] : '';
				        $email=(isset($rowTemp['email'])) ? $rowTemp['email'] : '';
				        $phone_home = isset($rowTemp['phone_home']) ? $rowTemp['phone_home'] : '';
				        $pid=(isset($rowTemp['pid'])) ? $rowTemp['pid'] : '';
				        $formatedDate = date('Ymd',strtotime($dob));
				        if($sex == 'Male'){
				        	$sex = "M";
				        }else if($sex == 'Female'){
				        	$sex = "F";
				        }else{
				        	$sex = "U";
				        }
				        /**
					     * For removing - and + and '' form phone number.
					     */
				        if($phone_home != ''){

				        	$phone_home = str_replace('-', '', $phone_home);
				        	$phone_home = str_replace('+', '', $phone_home);
				        	$phone_home = str_replace(' ', '', $phone_home);
				        	$phone_home=(strlen($phone_home)==10) ? $phone_home : '';
				        }           
				        /** 
									        *While fetching if it is very first record of data make it as table head
									        *Else make it as table data.
							                */
							                if($i=0){
							                	echo "<thead><tr>";
								                //echo "<td>$domain_identifier</td><td>Patient</td><td>$fname</td><td>$mname</td><td>$lname</td><td>$title</td>";
								                echo "<td style='display:none;'>IMPAT11</td><td>$pid</td><td>$lname</td><td>$fname</td><td>$mname</td>";
								                echo "<td style='display:none;'></td><td style='display:none;'></td><td>$formatedDate</td><td>$sex</td>";
								                echo "<td>$street</td><td style='display:none;'></td><td>$city</td><td>$state</td><td>$postal_code</td><td style='display:none;'></td>";
								                echo "<td>$phone_home</td><td style='display:none;'></td><td style='display:none;'></td><td style='display:none;'></td><td style='display:none;'></td><td style='display:none;'></td><td style='display:none;'></td><td style='display:none;'></td><td style='display:none;'></td><td style='display:none;'></td><td style='display:none;'></td><td style='display:none;'></td><td style='display:none;'></td><td style='display:none;'></td><td style='display:none;'></td><td style='display:none;'></td><td style='display:none;'></td><td style='display:none;'></td><td style='display:none;'></td><td style='display:none;'></td><td style='display:none;'></td><td style='display:none;'></td><td style='display:none;'></td><td style='display:none;'></td><td style='display:none;'></td><td style='display:none;'></td><td style='display:none;'></td><td style='display:none;'></td><td style='display:none;'></td>";
								                echo "<td>Y</td><td style='display:none;'></td><td style='display:none;'></td><td style='display:none;'></td><td>$email</td>";
								                echo "</tr></thead>";
							                }else{
								                echo "<tr>";
								                //echo "<td>$domain_identifier</td><td>Patient</td><td>$fname</td><td>$mname</td><td>$lname</td><td>$title</td>";
								                echo "<td style='display:none;'>IMPAT11</td><td>$pid</td><td>$lname</td><td>$fname</td><td>$mname</td>";
								                echo "<td style='display:none;'></td><td style='display:none;'></td><td>$formatedDate</td><td>$sex</td>";
								                echo "<td>$street</td><td style='display:none;'></td><td>$city</td><td>$state</td><td>$postal_code</td><td style='display:none;'></td>";
								                echo "<td>$phone_home</td><td style='display:none;'></td><td style='display:none;'></td><td style='display:none;'></td><td style='display:none;'></td><td style='display:none;'></td><td style='display:none;'></td><td style='display:none;'></td><td style='display:none;'></td><td style='display:none;'></td><td style='display:none;'></td><td style='display:none;'></td><td style='display:none;'></td><td style='display:none;'></td><td style='display:none;'></td><td style='display:none;'></td><td style='display:none;'></td><td style='display:none;'></td><td style='display:none;'></td><td style='display:none;'></td><td style='display:none;'></td><td style='display:none;'></td><td style='display:none;'></td><td style='display:none;'></td><td style='display:none;'></td><td style='display:none;'></td><td style='display:none;'></td><td style='display:none;'></td><td style='display:none;'></td>";
								                echo "<td>Y</td><td style='display:none;'></td><td style='display:none;'></td><td style='display:none;'></td><td>$email</td>";
								                echo "</tr>";
							                }$i++;     
					}       
        ?>
    
    </table>
   
    <input type='hidden' id='openemrTitle' value='<?php echo text($openemr_name); ?>' />
    <input type='hidden' id='currTime' value='<?php ?>' />
    <input type='hidden' id='currDate' value='<?php $today = getdate();$tdate=substr($today['month'], 0, 3).'_'.$today['mday'].'_'.$today['year']; echo $tdate; ?>' />

    </div>
</body>
</html>