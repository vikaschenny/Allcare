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
$pagename = "eligibility"; 

$ignoreAuth=true; // ignore the standard authentication for a regular OpenEMR user
include_once($_SERVER['DOCUMENT_ROOT']."/interface/globals.php");
$base_url="//".$_SERVER['SERVER_NAME'].dirname($_SERVER["REQUEST_URI"].'?').'/';
include 'section_header.php';

//require_once("../interface/globals.php");
require_once("../library/formdata.inc.php"); 
require_once("$srcdir/api.inc");
require_once("$srcdir/forms.inc");
require_once("$srcdir/options.inc.php");
require_once("$srcdir/patient.inc");
require_once("$srcdir/formatting.inc.php");
require_once("$srcdir/billing.inc");
include_once("$srcdir/calendar.inc");
include_once("$srcdir/edi.inc");

$providers_string = array();
//print_r($_SESSION);
if(strtolower(trim($_SESSION['see_all_providers'])) == 'yes'){
    $get_providers_list = sqlStatement("SELECT id,CONCAT (fname, lname, mname) as providername,username FROM users WHERE authorized != 0 AND active = 1 AND username <> '' ORDER BY fname,lname");
    while($set_providers_list = sqlFetchArray($get_providers_list)){
        $providername[ $set_providers_list['id']]    = $set_providers_list['providername'];
        $providernames[ $set_providers_list['id']]   = $set_providers_list['username'];
    }
}else if(strtolower(trim($_SESSION['see_all_providers'])) != 'yes' && $_SESSION['isprovider'] == 1){
    $providername[$_SESSION['portal_userid']]   = $_SESSION['portal_userfullname'];
    $providernames[$_SESSION['portal_userid']]  = $_SESSION['portal_username'];
}else if(strtolower(trim($_SESSION['see_all_providers'])) != 'yes' && $_SESSION['isprovider'] != 1){
    $get_providers_list = sqlStatement("SELECT pro_refers FROM tbl_user_custom_attr_1to1 WHERE userid = '".$_SESSION['portal_userid']."' LIMIT 0,1");
    while($set_providers_list = sqlFetchArray($get_providers_list)){
        $providers_string = explode("|",$set_providers_list['pro_refers']);
    }
    foreach ($providers_string as $pkey => $pvalue){
        $get_list_providername = sqlStatement("SELECT CONCAT (fname, lname, mname) as providername, id, username FROM users WHERE id = '$pvalue' AND username <> ''");
        while($set_list_providername = sqlFetchArray($get_list_providername)){
            $providername[$pvalue] = $set_list_providername['providername'];
            $providerids[$pvalue] = $set_list_providername['id'];
            $providernames[$pvalue] = $set_list_providername['username'];
        }
    }
}
//echo "<pre>";
//print_r($providerids);
//echo "</pre>";
//echo "test";
//echo "<pre>";
//print_r($providername);
//echo "</pre>";
//for logout
//$_SESSION['portal_username']= $_REQUEST['form_users'];

$id = $_POST['form_users'];
if(isset($id)):
    $sql = sqlStatement("SELECT id, username, fname, lname, specialty FROM users " .
          "WHERE active = 1 AND ( info IS NULL OR info NOT LIKE '%Inactive%' ) " .
          "AND authorized = 1 AND id='".$id."'" .
          "ORDER BY lname, fname");
    $proQuery         = sqlFetchArray($sql);
    $providerUsername = $proQuery['username'];
    $refer = $_SESSION['portal_username'];
endif;

$pid        = $_REQUEST['pid'];
$encounter  = $_REQUEST['encounter'];
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="shortcut icon" href="img/season-change.jpg" type="image/x-icon">
        <title>HealthCare</title>
        <link href='//fonts.googleapis.com/css?family=Abel' rel='stylesheet' type='text/css'>
        <link href='//fonts.googleapis.com/css?family=Roboto:400,300,500' rel='stylesheet' type='text/css'>
        <link href='//fonts.googleapis.com/css?family=Dosis:300,400,500,600' rel='stylesheet' type='text/css'>
        <link rel="stylesheet" type="text/css" href="assets/css/animate.css">
        <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="assets/css/owl.carousel.css">
        <link rel="stylesheet" type="text/css" href="assets/css/owl.theme.css">
        <link rel="stylesheet" type="text/css" href="assets/css/owl.transitions.css">
        <link rel="stylesheet" type="text/css" href="assets/css/font-awesome.min.css">
        <link rel="stylesheet" type="text/css" href="assets/css/main.css">
        <link rel="stylesheet" type="text/css" href="assets/css/customize.css">
        <link href='//fonts.googleapis.com/css?family=Roboto+Condensed:400,300' rel='stylesheet' type='text/css'>
        <link rel="stylesheet" href="css/dataTables.bootstrap.css"/>
        <link rel="stylesheet" href="css/datatables.responsive_bootstrap.css"/>
        <script type="text/javascript" src="assets/js/jquery.min.js"></script>
        <style>
            @media screen and (max-width: 767px) {

                main#content {
                  margin-top: 65px;
                  transition: all ease-out 0.3s;
                }

            }
            .section-header{
                padding: 0px;
                font-size: 1.5em;
                border: 0px;
                border-bottom:0px !important;
                margin-bottom:0px;
            }
            #patient-info,#single_view {
                margin-left: 14px;
            }
            #single_view{
                min-height: 150px;
            }
            .tabNav {
                color: #000000 !important;
                font-size: 20px;
                padding: 0;
            }
            .tabContainer {
                margin-left: 13px; 
            }
        </style>
        <script type='text/javascript'>
            function datafromchildwindow(id,month,pid) {
                var class_name_check = "check"+month+"-p"+pid;
                var getvrclass = document.getElementsByClassName(class_name_check);
                for(var i=0; i<getvrclass.length; i++){
                    var oldattr = getvrclass[i].getAttribute('onclick');
                    getvrclass[i].setAttribute( "onClick", " return validate_elig('"+pid+"','"+id.trim()+"','"+month+"')" );
                }
                 if(id != '0' || id != 0){
                    var class_name_checkbox = "checkbox"+month+"-p"+pid;
                    $("."+class_name_checkbox).prop('checked', true);
                }
            }
            function toggle( target, div ) {
                $mode = $(target).find(".indicator").text();
                if ( $mode == "collapse" ) {
                    $(target).find(".indicator").text( "expand" );
                    $(div).hide();
                } else {
                    $(target).find(".indicator").text( "collapse" );
                    $(div).show();
                }
            }
            function DoPost(page_name, provider,refer) {
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
                var key1='refer';
                var hiddenField1 = document.createElement("input");
                hiddenField1.setAttribute("type", "hidden");
                hiddenField1.setAttribute("name", key1);
                hiddenField1.setAttribute("value", refer);
                form.appendChild(hiddenField1);
                document.body.appendChild(form);
                form.submit();
            }
            
        </script>
        <?php html_header_show();?>

        <title><?php echo htmlspecialchars( xl('Eligibility 270 Inquiry Batch'), ENT_NOQUOTES); ?></title>

        <style type="text/css">@import url(../library/dynarch_calendar.css);</style>

        <style type="text/css">

            /* specifically include & exclude from printing */
            @media print {
                #report_parameters {
                    visibility: hidden;
                    display: none;
                }
                #report_parameters_daterange {
                    visibility: visible;
                    display: inline;
                }
                #report_results table {
                   margin-top: 0px;
                }
            }

            /* specifically exclude some from the screen */
            @media screen {
                #report_parameters_daterange {
                    visibility: hidden;
                    display: none;
                }
            }
            .css_button {
                background: transparent url( '../images/bg_button_a.gif' ) no-repeat scroll top right;
                color: #444;
                display: block;
                float: left;
                font: bold 10px arial, sans-serif;
                height: 24px;
                margin-right: 3px;
                padding-right: 10px; /* sliding doors padding */
                text-decoration: none;
            }
            .css_button span {
                background: transparent url( '../images/bg_button_span.gif' ) no-repeat;
                display: block;
                line-height: 14px;
                padding: 5px 0 5px 10px;
            }
            #report_results table {
               border-top: 1px solid black;
               border-bottom: 1px solid black;
               border-left: 1px solid black;
               border-right: 1px solid black;
               width: 100%;
               border-collapse: collapse;
               margin-top: 1px;
            }
            #report_results table thead {
                padding: 5px;
                display: table-header-group;
                background-color: #32AAC3;
                text-align:left;
                font-weight: bold;
                font-size: 1.0em;
            }
            #report_results table th {
                border-bottom: 1px solid black;
                padding: 5px;
            }
            #report_results table td {
                padding: 5px;
                border-bottom: 1px dashed;
                font-size: 0.9em;
            }
            .report_totals td {
                background-color: #77ff77;
                font-weight: bold;
            }

        </style>

        <script type="text/javascript" src="../library/textformat.js"></script>
        <script type="text/javascript" src="../library/dialog.js"></script>
        <script type="text/javascript" src="../library/dynarch_calendar.js"></script>
        <?php include_once("{$GLOBALS['srcdir']}/dynarch_calendar_en.inc.php"); ?>
        <script type="text/javascript" src="../library/dynarch_calendar_setup.js"></script>
        <script type="text/javascript" src="../library/js/jquery.1.3.2.js"></script>

        <script type="text/javascript">

                var mypcc = "<?php echo htmlspecialchars( $GLOBALS['phone_country_code'], ENT_QUOTES); ?>";
                var stringDelete = "<?php echo htmlspecialchars( xl('Do you want to remove this record?'), ENT_QUOTES); ?>?";
                var stringBatch	 = "<?php echo htmlspecialchars( xl('Please select X12 partner, required to create the 270 batch'), ENT_QUOTES); ?>";
                var stringverify = "<?php echo htmlspecialchars( xl('Please select X12 partner, required to verify the 270 batch'), ENT_QUOTES); ?>";

                // for form refresh 

                function refreshme() {
                        document.forms[0].submit();
                }

                //  To delete the row from the reports section 
                function deletetherow(id){
                        var suredelete = confirm(stringDelete);
                        if(suredelete == true){
                                document.getElementById('PR'+id).style.display="none";
                                if(document.getElementById('removedrows').value == ""){
                                        document.getElementById('removedrows').value = "'" + id + "'"; 
                                }else{
                                        document.getElementById('removedrows').value = document.getElementById('removedrows').value + ",'" + id + "'"; 

                                }
                        }

                }

                //  To validate the batch file generation - for the required field [clearing house/x12 partner] 
                function validate_batch()
                {
                        if(document.getElementById('form_x12').value=='')
                        {
                                alert(stringBatch);
                                return false;
                        }
                        else
                        {
                                document.getElementById('form_savefile').value = "true";
                                document.theform.submit();

                        }


                }

                //Eligibility verification
                function validate_elig(pid,form_id,month_value)
                {
                    if(document.getElementById('form_x12').value=='')
                    {
                        alert(stringverify);
                        return false;
                    }
                    else
                    {
                        from        = document.getElementById('form_from_date').value;
                        to          = document.getElementById('form_to_date').value;
                        facility    = document.getElementById('form_facility').value;
                        providerid  = document.getElementById('form_users').value;
                        removedrows = document.getElementById('removedrows').value;
                        form_x12    = document.getElementById('form_x12').value;
                        provider    = '<?php echo $providerUsername; ?>';
                        refer       = '<?php echo $refer; ?>';
                        if(form_id == '' || form_id == '0' || form_id == 0){
                            form_id     = 0;
                        }
                        
                        if(pid != ''){
                            // checkbox condition
                            var class_name_checkbox = "checkbox"+month_value+"-p"+pid;
                            
                            var viewportwidth   = document.documentElement.clientWidth;
                            var viewportheight  = document.documentElement.clientHeight;
                            window.resizeBy(-300,0);
                            window.moveTo(0,0);
                            
                            if ($("."+class_name_checkbox).is(':checked') == false) {
                                window.open("elig-verify.php?from="+from+"&to="+to+"&facility="+facility+"&providerid="+providerid+"&removedrows="+removedrows+"&form_x12="+form_x12+"&provider="+provider+"&refer="+refer+"&pid="+pid, "", "width=700, height=600,scrollbars=1,resizable=1");
                            }
                            window.open("save_eligibility_response_data.php?pid="+pid+"&provider="+provider+"&refer="+refer+"&form_id="+form_id+"&month_value="+month_value, "", "width=600,left="+(viewportwidth-100)+",height=600,top=0,scrollbars=1,resizable=1");
                        }else{
                            window.open("elig-verify.php?from="+from+"&to="+to+"&facility="+facility+"&providerid="+providerid+"&removedrows="+removedrows+"&form_x12="+form_x12+"&provider="+provider+"&refer="+refer+"&pid="+pid, "", "width=880, height=600,scrollbars=1,resizable=1");
                        }
                    }
                }

                // To Clear the hidden input field 

                function validate_policy()
                {
                    document.getElementById('removedrows').value = "";
                    document.getElementById('form_savefile').value = "";
                    return true;
                }

                // To toggle the clearing house empty validation message 
                function toggleMessage(id,x12){

                    var spanstyle = new String();

                    spanstyle		= document.getElementById(id).style.visibility;
                    selectoption	= document.getElementById(x12).value;

                    if(selectoption != '')
                    {
                        document.getElementById(id).style.visibility = "hidden";
                    }
                    else
                    {
                        document.getElementById(id).style.visibility = "visible";
                        document.getElementById(id).style.display = "inline";
                    }
                    return true;
                }

        </script>
    </head>
    <body>
        <?php include 'header_nav.php'; ?>
        <section id= "services">
            <div class= "container-fluid">
                <div class= "row">
                    <div class= "col-lg-12 col-sm-12 col-xs-12">
                        <?php
                        // Element data seperator		
                            $eleDataSep		= "*";
 
                            // Segment Terminator	
                            $segTer			= "~"; 	

                            // Component Element seperator
                            $compEleSep		= "^"; 	

                            // filter conditions for the report and batch creation 

                            $from_date		= fixDate($_POST['form_from_date'], date('Y-m-d'));
                            $to_date		= fixDate($_POST['form_to_date'], date('Y-m-d'));
                            $form_facility	= $_POST['form_facility'] ? $_POST['form_facility'] : '';
                            $form_provider	= $_POST['form_users'] ? $_POST['form_users'] : '';
                            $exclude_policy     = $_POST['removedrows'] ? $_POST['removedrows'] : '';
                            $X12info		= $_POST['form_x12'] ? explode("|",$_POST['form_x12']) : '';
                            $appt_status        = !empty($_POST['form_apptstatus'])?$_POST['form_apptstatus']:array('!n','&#'); 
                            //Set up the sql variable binding array (this prevents sql-injection attacks)
                            $sqlBindArray = array();

                            $where  = "e.pc_pid IS NOT NULL AND e.pc_eventDate >= ?";
                            array_push($sqlBindArray, $from_date);

                            //$where .="and e.pc_eventDate = (select max(pc_eventDate) from openemr_postcalendar_events where pc_aid = d.id)";

                            if ($to_date) {
                                    $where .= " AND e.pc_eventDate <= ?";
                                    array_push($sqlBindArray, $to_date);
                            }

                            if($form_facility != "") {
                                    $where .= " AND f.id = ? ";
                                    array_push($sqlBindArray, $form_facility);
                            }

                            if($form_provider != "") {
                                    $where .= " AND d.id = ? ";
                                    array_push($sqlBindArray, $form_provider);
                            }
                            
                            if(!empty($appt_status)){
                                $status1='';
                                foreach($appt_status as $status){
                                    if(!empty($status)){
                                    $status1.="'$status'".",";
                                    $status2= rtrim($status1,",");
                                     $filter_appstatus = " AND e.pc_apptstatus IN  ($status2)";
                                    }

                                }

                                $where .= $filter_appstatus;
                            }

                            if($exclude_policy != ""){	$arrayExplode	=	explode(",", $exclude_policy);
                                                                                    array_walk($arrayExplode, 'arrFormated');
                                                                                    $exclude_policy = implode(",",$arrayExplode);
                                                                                    $where .= " AND i.policy_number not in (".stripslashes($exclude_policy).")";
                                                                            }

                            $where .= " AND (i.policy_number is not null and i.policy_number != '')";  
                            // Subhan: This query is used to loop 270 records based on each patient
                            $query2 = sprintf("		SELECT  DISTINCT p.pid,p.fname,p.lname,p.mname 
                                                                            FROM openemr_postcalendar_events AS e
                                                                            LEFT JOIN users AS d on (e.pc_aid is not null and e.pc_aid = d.id)
                                                                            LEFT JOIN facility AS f on (f.id = e.pc_facility)
                                                                            LEFT JOIN patient_data AS p ON p.pid = e.pc_pid
                                                                            LEFT JOIN insurance_data AS i ON (i.id =(
                                                                                                                    SELECT id
                                                                                                                    FROM insurance_data AS i
                                                                                                                    WHERE pid = p.pid AND type = 'primary'
                                                                                                                    ORDER BY date DESC
                                                                                                                    LIMIT 1
                                                                                                                    )
                                                                                                            )
                                                                            LEFT JOIN insurance_companies as c ON (c.id = i.provider)
                                                                            WHERE %s ",	$where );
                            // Subhan: This query is used to DISPLAY records with patient name in ASC order
                            /*$query3 = sprintf("SELECT DATE_FORMAT(e.pc_eventDate, '%%Y%%m%%d') as pc_eventDate,
                                                                   e.pc_facility,
                                                                   p.lname,
                                                                   p.fname,
                                                                   p.mname, 
                                                                   DATE_FORMAT(p.dob, '%%Y%%m%%d') as dob,
                                                                   p.ss,
                                                                   p.sex,
                                                                   p.pid,
                                                                   p.pubpid,
                                                                   i.policy_number,
                                                                   i.provider as payer_id,
                                                                   i.subscriber_relationship,
                                                                   i.subscriber_lname,
                                                                   i.subscriber_fname,
                                                                   i.subscriber_mname,
                                                                   DATE_FORMAT(i.subscriber_dob, '%%m/%%d/%%Y') as subscriber_dob,
                                                                   i.subscriber_ss,
                                                                   i.subscriber_sex,
                                                                   DATE_FORMAT(i.date,'%%Y%%m%%d') as date,
                                                                   d.lname as provider_lname,
                                                                   d.fname as provider_fname,
                                                                   d.npi as provider_npi,
                                                                   d.upin as provider_pin,
                                                                   f.federal_ein,
                                                                   f.facility_npi,
                                                                   f.name as facility_name,
                                                                   c.name as payer_name
                                                        FROM openemr_postcalendar_events AS e
                                                        LEFT JOIN users AS d on (e.pc_aid is not null and e.pc_aid = d.id)
                                                        LEFT JOIN facility AS f on (f.id = e.pc_facility)
                                                        LEFT JOIN patient_data AS p ON p.pid = e.pc_pid
                                                        LEFT JOIN insurance_data AS i ON (i.id =(
                                                                                                SELECT id
                                                                                                FROM insurance_data AS i
                                                                                                WHERE pid = p.pid AND type = 'primary'
                                                                                                ORDER BY date DESC
                                                                                                LIMIT 1
                                                                                                )
                                                                                        )
                                                        LEFT JOIN insurance_companies as c ON (c.id = i.provider)
                                                        WHERE %s %s",	$where, " ORDER BY p.lname,p.fname,p.mname ASC");*/
                            // to get month based checkbox along with patients data
                            $query3 = sprintf("SELECT DATE_FORMAT(e.pc_eventDate, '%%Y%%m%%d') as pc_eventDate,
                                                                   e.pc_facility,
                                                                   p.lname,
                                                                   p.fname,
                                                                   p.mname, 
                                                                   DATE_FORMAT(p.dob, '%%Y%%m%%d') as dob,
                                                                   p.ss,
                                                                   p.sex,
                                                                   p.pid,
                                                                   p.pubpid,
                                                                   i.policy_number,
                                                                   i.provider as payer_id,
                                                                   i.subscriber_relationship,
                                                                   i.subscriber_lname,
                                                                   i.subscriber_fname,
                                                                   i.subscriber_mname,
                                                                   DATE_FORMAT(i.subscriber_dob, '%%m/%%d/%%Y') as subscriber_dob,
                                                                   i.subscriber_ss,
                                                                   i.subscriber_sex,
                                                                   DATE_FORMAT(i.date,'%%Y%%m%%d') as date,
                                                                   d.lname as provider_lname,
                                                                   d.fname as provider_fname,
                                                                   d.npi as provider_npi,
                                                                   d.upin as provider_pin,
                                                                   f.federal_ein,
                                                                   f.facility_npi,
                                                                   f.name as facility_name,
                                                                   c.name as payer_name,(SELECT ter.id FROM tbl_eligibility_response_data ter WHERE ter.pid= p.pid AND ter.month = DATE_FORMAT(e.pc_eventDate, '%%m-%%Y') LIMIT 1) as month_check,
                                                                   DATE_FORMAT(e.pc_eventDate, '%%m-%%Y') as month_value
                                                        FROM openemr_postcalendar_events AS e
                                                        LEFT JOIN users AS d on (e.pc_aid is not null and e.pc_aid = d.id)
                                                        LEFT JOIN facility AS f on (f.id = e.pc_facility)
                                                        LEFT JOIN patient_data AS p ON p.pid = e.pc_pid
                                                        LEFT JOIN insurance_data AS i ON (i.id =(
                                                                                                SELECT id
                                                                                                FROM insurance_data AS i
                                                                                                WHERE pid = p.pid AND type = 'primary'
                                                                                                ORDER BY date DESC
                                                                                                LIMIT 1
                                                                                                )
                                                                                        )
                                                        LEFT JOIN insurance_companies as c ON (c.id = i.provider)
                                                        WHERE %s %s",	$where, " ORDER BY p.lname,p.fname,p.mname ASC");

                            // Run the query 
//                            echo $query2."<br>";
//                            echo $query3."<br>";
                            $res2                   = sqlStatement($query2, $sqlBindArray);
                            $res3                   = sqlStatement($query3, $sqlBindArray);

                            // Get the facilities information 
                            $facilities		= getUserFacilities($_SESSION['authId']);

                            // Get the Providers information 
                            $providers		= getUsernames();

                            //Get the x12 partners information 
                            $clearinghouses	= getX12Partner();

                            if (isset($_POST['form_savefile']) && !empty($_POST['form_savefile'])) {
                            // Subhan: We use ZipAchive which is (PHP 5 >= 5.2.0, PECL zip >= 1.1.0) feature    
                            $zip = new ZipArchive();
                            $filename = sprintf('elig-270-%s-%s.zip',
                                                $from_date,
                                                $to_date);

                                    if ($zip->open($filename, ZipArchive::CREATE)!==TRUE) {
                                        exit("cannot open <$filename>\n");
                                    }
                            // Subhan: Here we loop results based on patient ID. So that we create files of each patient        
                            while($rows = sqlFetchArray($res2)):
                                $clause  = " AND p.pid = ". $rows['pid']." ORDER BY p.lname,p.fname,p.mname ASC";

                                $query = sprintf("SELECT DATE_FORMAT(e.pc_eventDate, '%%Y%%m%%d') as pc_eventDate,
                                                                   e.pc_facility,
                                                                   p.lname,
                                                                   p.fname,
                                                                   p.mname, 
                                                                   DATE_FORMAT(p.dob, '%%Y%%m%%d') as dob,
                                                                   p.ss,
                                                                   p.sex,
                                                                   p.pid,
                                                                   p.pubpid,
                                                                   i.policy_number,
                                                                   i.provider as payer_id,
                                                                   i.subscriber_relationship,
                                                                   i.subscriber_lname,
                                                                   i.subscriber_fname,
                                                                   i.subscriber_mname,
                                                                   DATE_FORMAT(i.subscriber_dob, '%%m/%%d/%%Y') as subscriber_dob,
                                                                   i.subscriber_ss,
                                                                   i.subscriber_sex,
                                                                   DATE_FORMAT(i.date,'%%Y%%m%%d') as date,
                                                                   d.lname as provider_lname,
                                                                   d.fname as provider_fname,
                                                                   d.npi as provider_npi,
                                                                   d.upin as provider_pin,
                                                                   f.federal_ein,
                                                                   f.facility_npi,
                                                                   f.name as facility_name,
                                                                   c.name as payer_name,(SELECT ter.id FROM tbl_eligibility_response_data ter WHERE ter.pid= p.pid AND ter.month = DATE_FORMAT(e.pc_eventDate, '%%m-%%Y') LIMIT 1) as month_check,
                                                                   DATE_FORMAT(e.pc_eventDate, '%%m-%%Y') as month_value
                                                        FROM openemr_postcalendar_events AS e
                                                        LEFT JOIN users AS d on (e.pc_aid is not null and e.pc_aid = d.id)
                                                        LEFT JOIN facility AS f on (f.id = e.pc_facility)
                                                        LEFT JOIN patient_data AS p ON p.pid = e.pc_pid
                                                        LEFT JOIN insurance_data AS i ON (i.id =(
                                                                                                SELECT id
                                                                                                FROM insurance_data AS i
                                                                                                WHERE pid = p.pid AND type = 'primary'
                                                                                                ORDER BY date DESC
                                                                                                LIMIT 1
                                                                                                )
                                                                                        )
                                                        LEFT JOIN insurance_companies as c ON (c.id = i.provider)
                                                        WHERE %s %s",	$where,$clause );
                                $res = sqlStatement($query, $sqlBindArray);
                                // Subhan: /library/edi.inc file uses "print_elig" method. But if we use this method here then
                                // we get values of file displayed in screen itself. So, we added a new method "allCare_print_elig"
                                // which returns the result instead of echo
                                $printData = allCare_print_elig($res,$X12info,$segTer,$compEleSep);
                                $zip->addFromString("elig-270-".$rows['lname']."-".$rows['fname'].".elg", $printData);
                            endwhile;
                            $zip->close();
                            $length = filesize($filename);
                            header('Content-Type: application/zip');
                            header('Content-Length: ' . $length);
                            header('Content-Disposition: attachment; filename="'.$filename.'"');
                            readfile("$filename");
                            unlink($filename);
                            exit;
                            }
                            
                                //generate form_field
                            function generate_form_field1($frow, $currvalue) {
                              global $rootdir, $date_init;

                              $sel_value=explode("|",$currvalue);

                              $currescaped = htmlspecialchars($currvalue, ENT_QUOTES);

                              $data_type   = $frow['data_type'];
                              $field_id    = $frow['field_id'];
                              $list_id     = $frow['list_id'];
                              // escaped variables to use in html
                              $field_id_esc= htmlspecialchars( $field_id, ENT_QUOTES);
                              $list_id_esc = htmlspecialchars( $list_id, ENT_QUOTES);

                              // Added 5-09 by BM - Translate description if applicable  
                              $description = (isset($frow['description']) ? htmlspecialchars(xl_layout_label($frow['description']), ENT_QUOTES) : '');

                              // added 5-2009 by BM to allow modification of the 'empty' text title field.
                              //  Can pass $frow['empty_title'] with this variable, otherwise
                              //  will default to 'Unassigned'.
                              // modified 6-2009 by BM to allow complete skipping of the 'empty' text title
                              //  if make $frow['empty_title'] equal to 'SKIP'
                              $showEmpty = true;
                              if (isset($frow['empty_title'])) {
                               if ($frow['empty_title'] == "SKIP") {
                                //do not display an 'empty' choice
                                $showEmpty = false;
                                $empty_title = "Unassigned";
                               }
                               else {     
                                $empty_title = $frow['empty_title'];
                               }
                              }
                              else {
                               $empty_title = "Unassigned";   
                              }

                              // generic single-selection list
                              if ($data_type == 1) {
                                echo generate_select_list1("form_$field_id", $list_id, $sel_value,
                                  $description, $showEmpty ? $empty_title : '');
                              }
                        }

                            // Function to generate a drop-list.
                            //
                            function generate_select_list1($tag_name, $list_id, $sel_value, $title,
                              $empty_name=' ', $class='', $onchange='', $tag_id = '', $custom_attributes = null )
                            {
                              //print_r($sel_value);   
                              $s = '';
                              $tag_name_esc = htmlspecialchars( $tag_name, ENT_QUOTES);
                              $tag_name_esc1=$tag_name_esc."[]";
                              $s .= "<select name='$tag_name_esc1' multiple ";
                              $tag_id_esc = $tag_name_esc;
                              if ( $tag_id != '' ) {
                                  $tag_id_esc = htmlspecialchars( $tag_id, ENT_QUOTES);
                              }   
                              $s .=  " id='$tag_id_esc'";
                              if ($class) $s .= " class='$class'";
                              if ($onchange) $s .= " onchange='$onchange'";
                              if ( $custom_attributes != null && is_array($custom_attributes) ) {
                                  foreach ( $custom_attributes as $attr => $val ) {
                                      if ( isset($custom_attributes[$attr] ) ) {
                                          $s .= " ".htmlspecialchars( $attr, ENT_QUOTES)."='".htmlspecialchars( $val, ENT_QUOTES)."'";
                                      }
                                  }
                              }
                              $selectTitle = htmlspecialchars( $title, ENT_QUOTES);
                              $s .= " title='$selectTitle'>";
                              $selectEmptyName = htmlspecialchars( xl($empty_name), ENT_NOQUOTES);

                              if ($empty_name) $s .= "<option value=''";   if($sel_value[0]=='') { $s .= " selected";
                              $got_selected = TRUE; }  
                              $s .= ">" . $selectEmptyName . "</option>";

                              $lres = sqlStatement("SELECT * FROM list_options " .
                                "WHERE list_id = ? ORDER BY seq, title", array($list_id) );
                              $got_selected = FALSE;
                              while ($lrow = sqlFetchArray($lres)) {
                                $optionValue = htmlspecialchars( $lrow['option_id'], ENT_QUOTES);
                                $s .= "<option value='$optionValue'";

                                    foreach($sel_value as $value) {
                                        if ((strlen($value) == 0 && $lrow['is_default']) ||
                                            (strlen($value)  > 0 && $lrow['option_id'] == $value))
                                        {
                                          $s .= " selected";
                                          $got_selected = TRUE;
                                        }

                                    }


                                $optionLabel = htmlspecialchars( xl_list_label($lrow['title']), ENT_NOQUOTES);
                                $s .= ">$optionLabel</option>\n";
                              }
                              foreach($sel_value as $value){
                              if (!$got_selected && strlen($value) > 0) {
                                $currescaped = htmlspecialchars($value, ENT_QUOTES);
                                $s .= "<option value='$currescaped' selected>* $currescaped *</option>";
                                $s .= "</select>";
                                $fontTitle = htmlspecialchars( xl('Please choose a valid selection from the list.'), ENT_QUOTES);
                                $fontText = htmlspecialchars( xl('Fix this'), ENT_NOQUOTES);
                                $s .= " <font color='red' title='$fontTitle'>$fontText!</font>";
                              }
                              else {
                                $s .= "</select>";
                              }
                              }
                              return $s;
                        }
                    ?>

                        <!-- Required for the popup date selectors -->
                        <div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>

                        <span><label><?php echo htmlspecialchars( xl('Report'), ENT_NOQUOTES); ?> - <?php echo htmlspecialchars( xl('Eligibility 270 Inquiry Batch'), ENT_NOQUOTES); ?></label></span>
                        <br>
                        <div id="report_parameters_daterange">
                                <?php echo htmlspecialchars( date("d F Y", strtotime($form_from_date)), ENT_NOQUOTES) .
                                        " &nbsp; " . htmlspecialchars( xl('to'), ENT_NOQUOTES) . 
                                        "&nbsp; ". htmlspecialchars( date("d F Y", strtotime($form_to_date)), ENT_NOQUOTES); ?>
                        </div>

                        <form method='post' name='theform' id='theform' action='patients_eligibility.php'>
                            <input type="hidden" name="removedrows" id="removedrows" value="">
                            <div id="report_parameters">
                                <table>
                                    <tr>
                                        <td width='550px'>
                                            <div style='float:left'>
                                                <table class='text'>
                                                    <tr>
                                                        <td>
                                                           <?php  //xl('From','e'); ?>
                                                           <label> From: </label>
                                                        </td>
                                                        <td>
                                                           <input type='text' name='form_from_date' id="form_from_date" size='10' value='<?php echo htmlspecialchars( $from_date, ENT_QUOTES) ?>' onkeyup='datekeyup(this,mypcc)' onblur='dateblur(this,mypcc)' title='yyyy-mm-dd'>
                                                           <img src='../interface/pic/show_calendar.gif' align='absbottom' width='24' height='22'
                                                                id='img_from_date' border='0' alt='[?]' style='cursor:pointer'
                                                                title='<?php echo htmlspecialchars( xl('Click here to choose a date'), ENT_QUOTES); ?>'>
                                                        </td>
                                                        <td>
                                                           <?php //echo htmlspecialchars( xl('To'), ENT_NOQUOTES); ?>
                                                            <label> To: </label>
                                                        </td>
                                                        <td>
                                                           <input type='text' name='form_to_date' id="form_to_date" size='10' value='<?php echo htmlspecialchars( $to_date, ENT_QUOTES) ?>'
                                                                onkeyup='datekeyup(this,mypcc)' onblur='dateblur(this,mypcc)' title='yyyy-mm-dd'>
                                                           <img src='../interface/pic/show_calendar.gif' align='absbottom' width='24' height='22'
                                                                id='img_to_date' border='0' alt='[?]' style='cursor:pointer'
                                                                title='<?php echo htmlspecialchars( xl('Click here to choose a date'), ENT_QUOTES); ?>'>
                                                        </td>
                                                        <td>&nbsp;</td>
                                                    </tr>

                                                    <tr>
                                                        <td>
                                                            <label> Facility: </label>
                                                        </td>
                                                        <td>
                                                            <?php dropdown_facility($form_facility,'form_facility',false);	?>
                                                        </td>
                                                        <td>
                                                           <?php //echo htmlspecialchars( xl('Provider'), ENT_NOQUOTES); ?>
                                                           <label> Provider: </label>
                                                        </td>
                                                        <td>
                                                            <select name='form_users' id="form_users" onchange='form.submit();'>
                                                                <option value=''>-- <?php echo htmlspecialchars( xl('Select Provider'), ENT_NOQUOTES); ?> --</option>
                                                                <?php foreach($providerids as $user): ?>
                                                                    
                                                                        <option value='<?php echo htmlspecialchars( $user, ENT_QUOTES); ?>' <?php if($id == $user){ ?> selected <?php } ?> >
                                                                            <?php echo htmlspecialchars( $providername[$user], ENT_NOQUOTES); ?>
                                                                        </option>
                                                                    
                                                                <?php endforeach; ?>
                                                            </select>
                                                        </td>
                                                        <td>&nbsp;
                                                        </td>
                                                    </tr>

                                                    <tr>
                                                        <td>
                                                            <label> X12 Partner: </label>
                                                        </td>
                                                        <td colspan='5'>
                                                            <select name='form_x12' id='form_x12' onchange='return toggleMessage("emptyVald","form_x12");' >
<!--                                                                <option value=''>--<?php echo htmlspecialchars( xl('select'), ENT_NOQUOTES); ?>--</option>-->
                                                                <?php 
                                                                    if(isset($clearinghouses) && !empty($clearinghouses))
                                                                    {
                                                                        foreach($clearinghouses as $clearinghouse): ?>
                                                                            <option value='<?php echo htmlspecialchars( $clearinghouse['id']."|".$clearinghouse['id_number']."|".$clearinghouse['x12_sender_id']."|".$clearinghouse['x12_receiver_id']."|".$clearinghouse['x12_version']."|".$clearinghouse['processing_format'], ENT_QUOTES); ?>'
                                                                                    <?php echo $clearinghouse['id'] == $X12info[0] ? " selected " : null; ?>
                                                                            ><?php echo htmlspecialchars( $clearinghouse['name'], ENT_NOQUOTES); ?></option>
                                                                <?php	endforeach; 
                                                                    }

                                                                ?>
                                                            </select> 
                                                            <span id='emptyVald' style='color:red;font-size:12px;'> * <?php echo htmlspecialchars( xl('Clearing house info required for EDI 270 batch creation.'), ENT_NOQUOTES); ?></span>
                                                        </td>
                                                        <td class='label'><?php xl('Status','e'); ?>:</td>
                                                        <td>
                                                            <?php 
                                                                  $appt_status    = !empty($_POST['form_apptstatus'])?$_POST['form_apptstatus']:array('!n','&#'); 
                                                                  $appt_status1=implode("|",$appt_status); 

                                                                  generate_form_field1(array('data_type'=>1,'field_id'=>'apptstatus','list_id'=>'apptstat','empty_title'=>'All'),$appt_status1);
                                                            ?>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </td>
                                        <td align='left' valign='middle' height="100%">
                                            <table style='border-left:1px solid; width:100%; height:100%' >
                                                <tr>
                                                    <td>
                                                        <div style='margin-left:15px'>
                                                            <a href='#' class='css_button' onclick='validate_policy(); $("#theform").submit();'>
                                                                <span>
                                                                    Refresh <?php //echo htmlspecialchars( xl('Refresh'), ENT_NOQUOTES); ?>
                                                                </span>
                                                            </a>
                                                            <a href='#' class='css_button' onclick='return validate_elig("");'>
                                                                <span>
                                                                    Eligibility Verification<?php //echo htmlspecialchars( xl('Eligibility Verification'), ENT_NOQUOTES); ?>
                                                                    <input type='hidden' name='form_eligverify' id='form_eligverify' value=''></input>
                                                                </span>
                                                            </a>										
                                                            <a href='#' class='css_button' onclick='return validate_batch();'>
                                                                <span>
                                                                    Create batch<?php //echo htmlspecialchars( xl('Create batch'), ENT_NOQUOTES); ?>
                                                                    <input type='hidden' name='form_savefile' id='form_savefile' value=''></input>
                                                                </span>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </div> 

                            <div class='text'>
                                    <?php echo htmlspecialchars( xl('Please choose date range criteria above, and click Refresh to view results.'), ENT_NOQUOTES); ?>
                            </div>
                            <input type="hidden" name="provider" id="provider" value="<?php echo $providerUsername; ?>"/>
                            <input type="hidden" name="refer" id="refer" value="<?php echo $refer; ?>" />
                        </form>

                        <?php
                                if ($res3){
                                        show_elig($res3,$X12info,$segTer,$compEleSep);
                                }
                        ?>
                    </div>
                </div>
            </div>    
            <div></div>
        </section>
        <?php include 'footer.php'; ?>
        <script type="text/javascript" src="assets/js/jquery.min.js"></script>
        <script type="text/javascript" src="assets/js/bootstrap.min.js"></script>

        <script>
        (function($){
            var ico = $('<i class="fa fa-caret-right"></i>');
            $('nav#menu li:has(ul) > a').append(ico);

            $('nav#menu li:has(ul)').on('click',function(){
                $(this).toggleClass('open');
            });

            $('a#toggle').on('click',function(e){
                $('html').toggleClass('open-menu');
                return false;
            });
            $('div#overlay').on('click',function(){
                $('html').removeClass('open-menu');
            })

        })(jQuery);
         Calendar.setup({inputField:"form_from_date", ifFormat:"%Y-%m-%d", button:"img_from_date"});
            Calendar.setup({inputField:"form_to_date", ifFormat:"%Y-%m-%d", button:"img_to_date"});
            <?php if ($alertmsg) { echo " alert('$alertmsg');\n"; } ?>
            $(document).ready(function(){
                // update image path without touching code in edi library
                $('.detail img').attr('src',$('.detail img').attr('src').replace('../../images', '../../../images'));
                $('input[type="checkbox"]').click(function(){
                    var className = $(this).attr('class');
                    if($("."+className).prop("checked") == true){
                        $("."+className).prop('checked', true);
                    }
                    else if($("."+className).prop("checked") == false){
                        $("."+className).prop('checked', false);
                    }
                });
                $(document).on('click', '.detail>a.css_button', function(e) {
                    e.preventDefault(); 
                });
            });  
            
        </script>
        
    </body>
</html>
