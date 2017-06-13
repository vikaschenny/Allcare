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

$pagename = "plist"; 
if(isset($_SESSION['portal_username']) !=''){
   $provider=$_SESSION['portal_username'];
}else {
   $provider=$_REQUEST['provider'];
}


 $sql=sqlStatement("SELECT id, fname, lname, specialty FROM users " .
      "WHERE active = 1 AND ( info IS NULL OR info NOT LIKE '%Inactive%' ) " .
      "AND authorized = 1 AND username='".$provider."'" .
      "ORDER BY lname, fname");
$id=sqlFetchArray($sql);
$id1=$id['id'];

?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta name="HandheldFriendly" content="true">
<link href='http://fonts.googleapis.com/css?family=Abel' rel='stylesheet' type='text/css'>
	    <!-- <link href='http://fonts.googleapis.com/css?family=Pontano+Sans' rel='stylesheet' type='text/css'>
	    <link href='http://fonts.googleapis.com/css?family=Alegreya+Sans:300,400,500,700' rel='stylesheet' type='text/css'> -->
	    <link href='http://fonts.googleapis.com/css?family=Roboto:400,300,500' rel='stylesheet' type='text/css'>
	    <link href='http://fonts.googleapis.com/css?family=Dosis:300,400,500,600' rel='stylesheet' type='text/css'>
            <link rel="stylesheet" type="text/css" href="assets/css/animate.css">
            <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">
            <link rel="stylesheet" type="text/css" href="assets/css/owl.carousel.css">
            <link rel="stylesheet" type="text/css" href="assets/css/owl.theme.css">
            <link rel="stylesheet" type="text/css" href="assets/css/owl.transitions.css">
            <link rel="stylesheet" type="text/css" href="assets/css/font-awesome.min.css">
            <link rel="stylesheet" type="text/css" href="assets/css/main.css">
            <link rel="stylesheet" type="text/css" href="css/scollypay.css">
            <link rel="stylesheet" type="text/css" href="assets/css/customize.css">
            <link href='http://fonts.googleapis.com/css?family=Roboto+Condensed:400,300' rel='stylesheet' type='text/css'>

<link rel="stylesheet" href="css/version1.0/dataTables.bootstrap.min.css"/>
<link rel="stylesheet" href="css/version1.0/responsive.bootstrap.min.css"/>
<link rel='stylesheet' type='text/css' href='../interface/main/css/dataTables.colVis.css'>
<link rel='stylesheet' type='text/css' href='../interface/main/css/dataTables.colReorder.css'>
<link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/themes/redmond/jquery-ui.css" /> 
<link rel="stylesheet" href="css/pqselect.min.css"/>
<script src="js/responsive_datatable/version1.0/jquery-1.11.3.min.js"></script>
<script src="https://code.jquery.com/ui/1.10.2/jquery-ui.js"></script>
<script src="js/pqselect.min.js"></script>
<script src="js/responsive_datatable/version1.0/jquery.dataTables.min.js"></script>
<script src="js/responsive_datatable/version1.0/dataTables.bootstrap.min.js"></script>
<script src="js/responsive_datatable/version1.0/dataTables.responsive.min.js"></script>
<script type='text/javascript' src='../interface/main/js/dataTables.colReorder.js'></script>
<script type='text/javascript' src='../interface/main/js/dataTables.colVis.js'></script>
<script type='text/javascript' src='js/responsive_datatable/dataTables.tableTools.js'></script>
<script type='text/javascript' src='js/responsive_datatable/dataTables.bootstrap.js'></script>
<script type='text/javascript'>
    $(document).ready(function() {
        var opttext=[];
        windowresize();
        function windowresize(){
            $('#showhidecolumns').empty();
           $('#patientload thead tr th').each(function(index,elm){
                var optiontext = index==0?"Preview and View":$(elm).text();
                opttext.push(optiontext);
                $('#showhidecolumns').append("<option data-column='"+index+"'>"+optiontext+"</option>");
            });
        }
        
        var table = $('#patientload').DataTable({
        "iDisplayLength": 25,
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
    });
    selectedoptions();
    intlizeselectbox();
    function selectedoptions(){
       $('#patientload thead tr th').each(function(index,elm){
            var selectedcolm = table.column(index);
            if(selectedcolm.visible()==true){
               $('#showhidecolumns option').eq(index).attr("selected","selected")
           }
        })
    }

     function intlizeselectbox(){
        $("#showhidecolumns").pqSelect({
            multiplePlaceholder: 'Show / Hide Columns',
            checkbox: true, //adds checkbox to options    
            maxDisplay: 0,
            search: false,
            displayText: "columns {0} of {1} selected"
        }).on("change", function(evt) {
            var val = $(this).val();
            $.each(opttext,function(index,elm){
                var column = table.column(index);
                if(val.indexOf(elm) !=-1)
                    column.visible(true);
                else
                    column.visible(false);
            })
        });

    }

    });     
     function DoPost(page_name, provider) {
                    method = "post"; // Set method to post by default if not specified.
                    var form = document.createElement("form");
                    form.setAttribute("method", method);
                    form.setAttribute("action", page_name);
                    var key='provider';
                    var hiddenField = document.createElement("input");
                    hiddenField.setAttribute("type", "hidden");
                    hiddenField.setAttribute("name", key);
                    hiddenField.setAttribute("value", provider);

                    form.appendChild(hiddenField);
                    document.body.appendChild(form);
                    form.submit();
                }
                 $(function () {
                    setNavigation();
                });
                function setNavigation() {
                    var currentparms = (window.location.href.split("/"));
                    var path = currentparms[currentparms.length-1];
                    $("#sidenave li a").each(function () {
                        var href = $(this).attr('href');
                        console.log(path + " " + href)
                        if (path === href) {
                            $(this).closest('li').addClass('active');
                            $(this).removeAttr("href");
                        }
                    });
                }
</script>
<style>
  #ins  ul li {
  list-style-type:decimal !important;
 // display: inline-block;
}
 .bs-docs-sidenav .active a:hover {
            background-color: #4ac2dc;
        }
        #sidenave .active {
           background-color: #4ac2dc;
           cursor: default;
        }

        #sidenave .active a {
            color:#fff !important;
            font-weight:bold;
            text-decoration: none;
        }
        #content table ul li{
           display: block;
        }
        .bs-docs-sidenav.affix {
            top: 94px;
        }
        #content {
            padding-bottom: 16px;
            overflow-x: visible;
            overflow-y: hidden;
        }
</style>
<!--<link rel="stylesheet" href="css/dataTables.bootstrap.css"/>
<link rel="stylesheet" href="css/datatables.responsive_bootstrap.css"/>
<script src="js/responsive_datatable/jquery.min.js"></script>
<script src="js/responsive_datatable/jquery.dataTables.min.js"></script>
<script src="js/responsive_datatable/dataTables.bootstrap.js"></script>
<script src="js/responsive_datatable/datatables.responsive.js"></script>
</head>-->
<body ><?php include 'header_nav.php'; ?>
    <section id= "services">
    <div class= "container">
	<div class= "row">
            <div id="contents">
                <div id="sidenave" class="col-sm-3">
                     <ul class="nav nav-list bs-docs-sidenav affix">
                        <li class=""><a href="<?php echo $base_url ?>patient_data.php?order=<?php echo $order; ?>&provider=<?php echo $provider; ?>&id=my_patients">My Patients</a></li>
                        <li class=""><a href="<?php echo $base_url ?>patient_data.php?order=<?php echo $order; ?>&provider=<?php echo $provider; ?>&id=all_patients">All Patients</a></li>
                        <li class=""><a href="<?php echo $base_url ?>patient_data.php?order=<?php echo $order; ?>&provider=<?php echo $provider; ?>&id=by_facility">Patients By Facility</a></li>
                        <li class=""><a href="<?php echo $base_url ?>patient_data.php?order=<?php echo $order; ?>&provider=<?php echo $provider; ?>&id=by_appointment">Patients By Appointments</a></li>
                        <li class=""><a href="<?php echo $base_url ?>patient-center-batch.php">Patient Center Batch</a></li>
                        <li class=""><a href="<?php echo $base_url ?>patient-statement.php">Patient Statement Batch</a></li>
                      </ul>
                </div>
		<div id="content" class="col-sm-9"> 
                    <h3>Patient Center batch</h3>
    <strong>Instructions:</strong>
    <ul id='ins' style="list-style-type:decimal !important; ">
        <li >Click on Save to Excel</li>
        <li>Go to Control Panel of your machine. Select "Region and Language". 
            Now select "Additional Settings" Button.</li>
        <li>In "Additional Settings" popup, you see a field called "List Separator". Change that to "|".</li>
        <li>Now open the downloaded xls file and save as "CSV" file in the same location.</li>
        <li>This file is now ready for patient batch upload in Zirmed.</li>
    </ul>
  <div style="">
       <div class="costmizecolumns">
            <select id="showhidecolumns" multiple=multiple style="width:220px;"></select>
        </div>
    <table id='patientload' cellpadding='0' cellspacing='0' border='0' class='table table-striped table-bordered dt-responsive nowrap' width="100%">
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
             </div>   
                </div>
         </div>                                 
    </div>                                        
</section>                                           
 <?php include 'footer.php'; ?>    
</body>
</html>