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

$pagename = "home"; 
if(isset($_SESSION['portal_username']) != ''){
    $portal_user    = $_SESSION['portal_username'];
}else {
    $portal_user    = $_REQUEST['provider'];
    $_SESSION['portal_username']=$_REQUEST['provider'];
}

$base_url="//".$_SERVER['SERVER_NAME'].dirname($_SERVER["REQUEST_URI"].'?').'/';
?>

<!DOCTYPE html>
<html>

	<head>
            <meta charset="UTF-8">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1 maximum-scale=1, user-scalable=no">
             <?php 
             $sql   = sqlStatement("select * from globals where gl_name='openemr_name'");
              $row1 = sqlFetchArray($sql);?>
            <title><?php echo $row1['gl_value']; ?></title>		
	    <link href='//fonts.googleapis.com/css?family=Abel' rel='stylesheet' type='text/css'>
	    <link href='//fonts.googleapis.com/css?family=Roboto:400,300,500' rel='stylesheet' type='text/css'>
	    <link href='//fonts.googleapis.com/css?family=Dosis:300,400,500,600' rel='stylesheet' type='text/css'>   
            <link href='//fonts.googleapis.com/css?family=Roboto+Condensed:400,300' rel='stylesheet' type='text/css'>
            <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">
            <link rel="stylesheet" type="text/css" href="assets/css/font-awesome.min.css">
            <link rel="stylesheet" type="text/css" href="assets/css/main.css">
            
            <!-- for calendar's-->
            <link rel="stylesheet" href="css/style-personal.css">
            <link rel="stylesheet" type="text/css" href="assets/css/customize.css">
            <script src="//code.jquery.com/jquery-latest.min.js" type="text/javascript"></script>
            <script src="js/simplecalendar.js" type="text/javascript"></script> 
            <script type="text/javascript" src="assets/js/jquery.min.js"></script>
            <script type="text/javascript" src="https://code.jquery.com/ui/1.9.2/jquery-ui.js"></script>
            <script language="javascript">
                var modaltarget = null;
                function DoPost(page_name, provider,refer) {
                    method      = "post"; // Set method to post by default if not specified.
                    var form    = document.createElement("form");
                    form.setAttribute("method", method);
                    form.setAttribute("action", page_name);
                    var key     = 'provider';
                    var hiddenField = document.createElement("input");
                    hiddenField.setAttribute("type", "hidden");
                    hiddenField.setAttribute("name", key);
                    hiddenField.setAttribute("value", provider);
                    form.appendChild(hiddenField);
                    var key1    = 'refer';
                    var hiddenField1 = document.createElement("input");
                    hiddenField1.setAttribute("type", "hidden");
                    hiddenField1.setAttribute("name", key1);
                    hiddenField1.setAttribute("value", refer);
                    form.appendChild(hiddenField1);
                    document.body.appendChild(form);
                    form.submit();
                }
                
                //Eligibility verification
                function validate_elig(){ 

                    //pid,form_id,month_value,payer,provider_id,dos
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

                        provider    = '<?php echo $portal_user; ?>';
                        refer       = '<?php echo $refer; ?>';

                        if(form_id === '' || form_id === '0'){
                            form_id     = 0;
                        }
                        var eligibility = [
                            {from:from,to:to,facility:facility,provider:provider,providerid:providerid,refer:refer,removedrows:removedrows,form_x12:form_x12,pid:pid,frame:"show"},
                            {pid:pid,form_id:form_id,month_value:month_value,verify_type:"patient_eligibility",payer_id:payer,provider:provider,provider_id:provider_id,refer:refer,dos:dos,frame:"show"}
                        ];
                        if(pid != ''){
                            // checkbox condition
                            var class_name_checkbox = "checkbox"+month_value+"-p"+pid;

                            var viewportwidth   = document.documentElement.clientWidth;
                            var viewportheight  = document.documentElement.clientHeight;
                            window.resizeBy(-300,0);
                            window.moveTo(0,0);


                            if ($("."+class_name_checkbox).is(':checked') == false) {
                                //window.open("elig-verify.php?from="+from+"&to="+to+"&facility="+facility+"&provider="+provider+"&removedrows="+removedrows+"&form_x12="+form_x12+"&pid="+pid, "", "width=700, height=600,scrollbars=1,resizable=1");
                                //window.open("save_eligibility_response_data.php?pid="+pid+"&form_id="+form_id+"&month_value="+month_value+"&verify_type=patient_eligibility&payer_id="+payer+"&provider_id="+provider_id+"&dos="+dos, "", "width=600,left="+(viewportwidth-100)+",height=600,top=0,scrollbars=1,resizable=1");

                                eligibility[0].page = "elig-verify";
    //                                eligibility[0].page = "save_eligibility_response_data";
                                eligibility[1].page = "save_eligibility_response_data";
                                eligibility[0].pagename = "Eligibility Response";
                                eligibility[1].pagename = "Eligibility Data Screen";

                                window.localStorage.setItem("provider_eligibility",JSON.stringify(eligibility))
                                var url = "verify_eligibility.php";
                                window.open(url,"","width=1000, height=600,scrollbars=1,resizable=1");

                            }else{
                                eligibility[0].frame="hide";
                                eligibility[1].page = "save_eligibility_response_data";
                                eligibility[1].pagename = "Eligibility Data Screen";
                                window.localStorage.setItem("provider_eligibility",JSON.stringify(eligibility))
                                var url = "verify_eligibility.php";
                                window.open(url,"","width=1000, height=600,scrollbars=1,resizable=1");
                              //window.open("save_eligibility_response_data.php?pid="+pid+"&form_id="+form_id+"&month_value="+month_value+"&verify_type=patient_eligibility&payer_id="+payer+"&provider_id="+provider_id+"&dos="+dos, "","width=1000, height=600,scrollbars=1,resizable=1");  
                            }
                        }else{
                            eligibility[1].frame="hide";
                            eligibility[0].page = "elig-verify";
    //                            eligibility[0].page = "save_eligibility_response_data";
                            eligibility[0].pagename = "Eligibility Response";
                            window.localStorage.setItem("provider_eligibility",JSON.stringify(eligibility));
                            var url = "verify_eligibility.php";
                            window.open(url,"","width=1000, height=600,scrollbars=1,resizable=1");
                           //window.open("elig-verify.php?from="+from+"&to="+to+"&facility="+facility+"&provider="+provider+"&removedrows="+removedrows+"&form_x12="+form_x12+"&pid="+pid, "", "width=880, height=600,scrollbars=1,resizable=1");
                        }
                    }
                }
            </script>  
            <style>
                html,body{height: 100%}
                .navbar-nav > li > .dropdown-menu{
                    margin-top: 4px !important;
                }
                .headerstyle{
                    display: none;
                }
                @media print {
                    .appointment {
                         border: 1px solid black !important ; padding: 5px !important;
                         margin:5px !important;
                         width:100%;
                         border-radius: 5px;
                         display: table;
                     }
                     #ourevent,.day-event{
                         width:900px;
                     }
                     .headerstyle{
                        display: block;
                        margin: 0px;
                        padding: 0px;
                        margin-top: -100px;
                    }
                    .day-event{
                        display: inline-block;
                    }
                    .group{
                        display: inline-block;
                        width: 50% !important;
                        page-break-inside: avoid;
                        counter-reset: page 1;
                    }
                    .appname{
                        font-size: 20px;
                        font-weight: bold;
                    }
                    #pageFooter {
                        position: fixed;
                        right: 0;
                        bottom: 5px;
                    }

                    #pageFooter:after {
                        counter-increment: page;
                        content:  "Page " counter(page);
                    }
                    
                }
                
                #oppinments .appointment {
                    border-radius: 5px;
                }
                .print-btn {
                    padding: 3px 14px;
                }
                #homecal td{
                    width: 14.27%;
                }
                .day-event1{
                    width: auto;
                    margin-bottom:0px;
                }
                .print{
                    margin-top: 13px;
                }
                .calendar tbody td:hover{
                  box-sizing: border-box;  
                }
                .day-event1,.day-event{
                    min-height: 400px;
                }
                #oppinments{
                    min-height: 400px;
                }
                .appointment td:last-child {
                    text-indent: -7px;
                }
                .appointment td[colspan="2"]{
                    text-indent: 0px;
                }
                
                .appointment table td th{
                    vertical-align: top !important;
                }
                .day-event {
                    background-color: #f2f2f2;
                    display: none;
                    margin-bottom: 50px;
                    padding: 5px 10px 0;
                    width: auto !important;
                    margin-right: 0px;
                }
                .day-event h2 {
                    margin-top: 0;
                }
                #print1,.aptitle{
                    margin-top: 12px !important;
                }
                .noappointments{
                    display: none;
                }
                #showmap .modal-body {
                    padding:0px !important;
                    height: 500px;
                }
                #loader{
                    background: rgba(0,0,0,0.56);
                    border-radius: 4px;
                    display:table;
                    height: 48px;
                    width: 242px;
                    color: #fff;
                    position: absolute;
                    left: 0px;
                    top:0px;
                    bottom: 0px;
                    right: 0px;
                    margin: auto;
                    display: none;
                }
                .ajax-spinner-bars {
                    height: 48px;
                    left: 23px;
                    position: relative;
                    top: 20px;
                    width: 35px;
                    display: table-cell;
                 }
                 #loadertitle {
                    display: table-cell;
                    font-size: 17px;
                    padding-left: 14px;
                    vertical-align: middle;
                 }

                .ajax-spinner-bars > div {
                    position: absolute;
                    width: 2px;
                    height: 8px;
                    background-color: #fff;
                    opacity: 0.05;
                    animation: fadeit 0.8s linear infinite;
                }
                .ajax-spinner-bars > .bar-1 {
                transform: rotate(0deg) translate(0, -12px);
                animation-delay:0.05s;
                }
                .ajax-spinner-bars > .bar-2 {
                transform: rotate(22.5deg) translate(0, -12px);
                animation-delay:0.1s;
                }
                .ajax-spinner-bars > .bar-3 {
                transform: rotate(45deg) translate(0, -12px);
                animation-delay:0.15s;
                }
                .ajax-spinner-bars > .bar-4 {
                transform: rotate(67.5deg) translate(0, -12px);
                animation-delay:0.2s;
                }
                .ajax-spinner-bars > .bar-5 {
                transform: rotate(90deg) translate(0, -12px);
                animation-delay:0.25s;
                }
                .ajax-spinner-bars > .bar-6 {
                transform: rotate(112.5deg) translate(0, -12px);
                animation-delay:0.3s;
                }
                .ajax-spinner-bars > .bar-7 {
                transform: rotate(135deg) translate(0, -12px);
                animation-delay:0.35s;
                }
                .ajax-spinner-bars > .bar-8 {
                transform: rotate(157.5deg) translate(0, -12px);
                animation-delay:0.4s;
                }
                .ajax-spinner-bars > .bar-9 {
                transform: rotate(180deg) translate(0, -12px);
                animation-delay:0.45s;
                }
                .ajax-spinner-bars > .bar-10 {
                transform: rotate(202.5deg) translate(0, -12px);
                animation-delay:0.5s;
                }
                .ajax-spinner-bars > .bar-11 {
                transform: rotate(225deg) translate(0, -12px);
                animation-delay:0.55s;
                }
                .ajax-spinner-bars > .bar-12 {
                transform: rotate(247.5deg) translate(0, -12px);
                animation-delay:0.6s;
                }
                .ajax-spinner-bars> .bar-13 {
                transform: rotate(270deg) translate(0, -12px);
                animation-delay:0.65s;
                }
                .ajax-spinner-bars > .bar-14 {
                transform: rotate(292.5deg) translate(0, -12px);
                animation-delay:0.7s;
                }
                .ajax-spinner-bars > .bar-15 {
                transform: rotate(315deg) translate(0, -12px);
                animation-delay:0.75s;
                }
                .ajax-spinner-bars> .bar-16 {
                transform: rotate(337.5deg) translate(0, -12px);
                animation-delay:0.8s;
                }
                .editapp,.eligibility,.demographics{
                    cursor: pointer;
                    position: relative;
                }
                
                .createapp,.editins,.Benefits,.copay,.blance{
                    cursor: pointer;
                }
                .copay,.blance,.address{
                    position: absolute;
                    right: 30px;
                    text-align: right;
                }
                
                @keyframes fadeit{
                      0%{ opacity:1; }
                      100%{ opacity:0;}
                }
                .divider{
                    display: none;
                }
                    
                @media only screen and (max-width: 767px) {
                    .day-event{
                        margin-right: -15px;
                    }
                }
                @media only screen and (max-width: 479px) {
                    .divider{
                        display: block;
                        margin-top: 10px;
                    }
                }
                @media screen and (min-width: 992px) {
                    #modalwindow .modal-lg {
                     width: 90%; 
                   }
                }
                .addapp{
                    padding-bottom: 8px;
                }
                
                .customgroup{
                    border-bottom: 1px solid;
                    margin-bottom: 7px;
                    padding-bottom: 6px;
                    padding-top: 4px;
                    width: 100%;
                }
                .customgroup a{
                    width: 50%;
                }
                .customgroup a:last-child{
                    border-left: 1px solid #1c65a4;
                }
                .btncostime{
                    background-color:#46a1b4;
                    border-color: #59b4c7;
                }
                .btncostime:hover,.btncostime:focus,.btncostime:active{
                    background-color:#55b0c3;
                    border-color: #59b4c7;
                }
                .btn-primary:active, .btn-primary.active{
                    background-color:#1b7689;
                    border-color: #59b4c7;
                    color: #fff;
                    box-shadow: none;
                }
                
         </style>
         
	</head>
        
        <body> <?php include 'header_nav.php'; ?>
                <section style="padding-top:100px !important; min-height: 100%; margin-bottom: -45px;" class='container-fluid'>
                    <div class="row">
                     <div class= "hidden-print col-lg-3 col-sm-4 col-xs-12 text-center">
                         <div class="text-left hidden-print">
                             <h4>Providers</h4>
                                <?php
                                $get_refer_id = sqlStatement("SELECT id FROM users WHERE username = '$portal_user'");
                                while($set_refer_id = sqlFetchArray($get_refer_id)){
                                    $referid = $set_refer_id['id'] ;
                                }
                                $providers_string = array();
                                $get_providers_list = sqlStatement("SELECT pro_refers FROM tbl_user_custom_attr_1to1 WHERE userid = '$referid' LIMIT 0,1");
                                while($set_providers_list = sqlFetchArray($get_providers_list)){
                                    $providers_string = explode("|",$set_providers_list['pro_refers']);
                                }
                                foreach ($providers_string as $pkey => $pvalue){
                                    $get_list_providername = sqlStatement("SELECT CONCAT (fname, lname, mname) as providername, username FROM users WHERE id = '$pvalue' AND username <> ''");
                                    while($set_list_providername = sqlFetchArray($get_list_providername)){
                                        $providername[$pvalue] = $set_list_providername['providername'];
                                        $providernames[$pvalue] = $set_list_providername['username'];
                                    }
                                }
                               ?>
                             <select id="providersmenu" name="providersmenu" class="form-control" title="Select Provider to get appointments">     
                                <?php 
                                if(!empty($providername)){
                                    foreach($providername as $provider_idsub => $providername_sub){
                                        echo "<option value ='$provider_idsub' ";
                                        if( $providernames[$provider_idsub] == $provider )
                                            echo " selected ";
                                        echo "> $providername_sub </option>";  
                                    }
                                }
                                ?>
                            </select> <br> 
                         </div>
                         <div class="text-left hidden-print">
                             <h4>Visit Types</h4>
                             <select id="pc_visittype" class="form-control" multiple="multiple" name="pc_visittype">
                                <?php
                                  $get_visittype = sqlStatement("SELECT option_id,title FROM list_options WHERE list_id='Appointment_Visit_Types' ORDER BY title");
                                  while($set_visittype = sqlFetchArray($get_visittype)){
                                      echo "<option value = '".$set_visittype['option_id']."'> ".$set_visittype['title']."</option>";
                                  }
                                ?>
                             </select><br> 
                         </div>
                         <br>
                         <div class="calendar hidden-print" id="homecal">
                          <header>
                            <h4 class="month"></h4>
                            <a class="btn-prev fontawesome-angle-left" href="#"><i class="fa fa-angle-left"></i></a>
                            <a class="btn-next fontawesome-angle-right" href="#"><i class="fa fa-angle-right"></i></a>
                          </header>
                          <table style="width:100%;">
                            <thead class="event-days">
                              <tr></tr>
                            </thead>
                            <tbody class="event-calendar">
                              <tr class="1"></tr>
                              <tr class="2"></tr>
                              <tr class="3"></tr>
                              <tr class="4"></tr>
                              <tr class="5"></tr>
                            </tbody>
                          </table>
                         </div>
                         <div style="text-align: left;">
                             <ul style="list-style-type: none;">
                                <li><a data-modalsize='modal-lg' data-title="View Schedules" data-frameheight="450" data-bodypadding='0' data-href='calendar/index.php?module=PostCalendar&viewtype=day&func=view&pc_username=<?php $refer; ?>&framewidth=1212' data-toggle='modal' data-target='#modalwindow' title="Click to see calendar schedule" data-backdrop="static" data-keyboard="false" class="btn btn-default btn-sm">View Schedules</a></li>
                                <li><a href="javascript:void()" data-title="Search Patient Appointments" data-frameheight="450" data-modalsize='modal-lg' data-bodypadding='0' data-frameheight="315" data-href='calendar/index.php?module=PostCalendar&func=search' data-toggle='modal' data-target='#modalwindow' title="Click to search patient appointments" data-backdrop="static" data-keyboard="false" class="btn btn-default btn-sm">Search Patient Appointments</a></li>
                                <li><a href="javascript:void()" data-title="Search Provider Schedule" data-frameheight="450" data-modalsize='modal-lg' data-bodypadding='0' data-frameheight="315" data-href='calendar/providerschedules/view_provider_schedule_page.php' data-toggle='modal' data-target='#modalwindow' title="Click to search provider calendar schedule" data-backdrop="static" data-keyboard="false" class="btn btn-default btn-sm">Search Provider Schedule</a></li>
                             </ul>
                         </div>
                         <div class="well well-lg text-left hidden-print">
                             <div class="row">
                                <?php
                                $get_calendar = sqlStatement("SELECT pc_eventDate, CONCAT(fname, ' ', lname) as pname,phone_home,pc_hometext FROM openemr_postcalendar_events INNER JOIN patient_data ON pc_pid = pid WHERE pc_eventDate > NOW() AND pc_aid = '18'");
                                while($set_calendar = sqlFetchArray($get_calendar)){
                                    echo "<div class ='col-sm-3'>";
                                    echo $set_calendar['pc_eventDate'] ;
                                    echo "</div>";
                                    echo "<div class ='col-sm-3'>";
                                    echo $set_calendar['pname'] ;
                                    echo "</div>";
                                    echo "<div class ='col-sm-3'>";
                                    echo $set_calendar['phone_home'] ;
                                    echo "</div>";
                                    echo "<div class ='col-sm-3'>";
                                    echo "+";
                                    echo "</div>";
                                    echo "<div class='clearfix'></div>";
                                    echo "<br>";
                                }
                                ?>
                             </div>
                         </div>
                         <br>
                         <br>
                      </div>

                        <div id="oppinments" class= "col-lg-9 col-sm-8 col-xs-12 ">
                            <div id="pageFooter"></div>
                            <div class='noappointments' style='padding:10px 10px 0px 0px; background-color: #F2F2F2; height:400px;'>
                                <div style="padding-top:140px;"><center><h2>No Appointments</h2></center></div>
                            </div>
                            <div class='noappointments1' style='padding:10px 10px 0px 0px; background-color: #F2F2F2; height:400px;'>
                            </div>
                            <div id="loader">
                                <div class="ajax-spinner-bars">
                                    <div class="bar-1"></div>
                                    <div class="bar-2"></div>
                                    <div class="bar-3"></div>
                                    <div class="bar-4"></div>
                                    <div class="bar-5"></div>
                                    <div class="bar-6"></div>
                                    <div class="bar-7"></div>
                                    <div class="bar-8"></div>
                                    <div class="bar-9"></div>
                                    <div class="bar-10"></div>
                                    <div class="bar-11"></div>
                                    <div class="bar-12"></div>
                                    <div class="bar-13"></div>
                                    <div class="bar-14"></div>
                                    <div class="bar-15"></div>
                                    <div class="bar-16"></div>
                                </div>
                                <div id="loadertitle">Appointments Loading...</div>
                            </div>
                            <div style="width:900px;border:1px solid #000;clear:both; padding:0px 20px;" class="headerstyle">
                                <table>
                                    <tbody>
                                        <?php
                                            $provider_credentials = '';
                                            $providerid           = ''; 
                                            $get_provider_id = sqlStatement("SELECT id FROM users WHERE username = '$portal_user'");
                                            while($set_provider_id = sqlFetchArray($get_provider_id)){
                                                $providerid = $set_provider_id['id'] ;
                                            }
                                            $get_provider_cred = sqlStatement("SELECT provider_credentials FROM tbl_patientuser WHERE id = '$providerid'");
                                            while($set_provider_cred = sqlFetchArray($get_provider_cred)){
                                                if(!empty($set_provider_cred['provider_credentials']))
                                                    $provider_credentials = ", ".$set_provider_cred['provider_credentials'];
                                            }
                                        
                                        ?>
                                        <tr>
                                            <td width="45%" align="left" height="" class="firsttd"><h3><?php echo "<span style='font-size:16px;' >Rendering Provider: </span>".$id['fname']." ".$id['lname']."<span style='font-size:13px;'>$provider_credentials </span>" ; ?></h3></td>
                                            <td width="100%"><span><b>Texas Physician House Calls (H)</b><br><font size="4">2925 Skyway Circle North, Irving, TX, USA, 75038-3510<br>www.texashousecalls.com, Phone:(972) 675-7313, Fax:(972) 675-7310, Email:hhsupport@texashousecalls.com</font></span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div id="ourevent"></div>
                        </div>
                    </div>
                </section>  
            <?php include 'footer.php'; ?>
            <script type="text/javascript" src="assets/js/bootstrap.min.js"></script>
            <script>
                $(document).ready(function() {
                    $("#help_dialog").draggable({ handle:'#header'});
                    var selectedprovider = $('#providersmenu').val();
                    var selectedvisitType = $('#pc_visittype').val();
                    var providerstates = "loading";
                    get_calendar_icons(selectedprovider,selectedvisitType);
                    $("#providersmenu,#pc_visittype").change(function(){
                        $('.noappointments1').show();
                        var enc_value = $('#hiddenenc_value').val();
                        var selectedids = $('#providersmenu').val();
                        var selectedvisitType = $('#pc_visittype').val();
                        get_calendar_icons(selectedids,selectedvisitType);
                        ajaxcall($("tbody.event-calendar .active"),<?php echo $id; ?>,enc_value,selectedvisitType);
                    });      
                    
                                        
                    function ajaxcall(targetobject,pid,encid,visitType){
                        var year = targetobject.attr('date-year');
                        var month = targetobject.attr('date-month');
                        var day = targetobject.attr('date-day');

                        var selectedids = $('#providersmenu :selected').val();

                        var eventdate = (year+"-"+month + "-" + day)
                        if($('.day-event[date-year="' + year + '"][date-month="' + month + '"][date-day="' + day + '"]').is(":empty"))
                            $('#loader').show();
                        
                        $.ajax({
                            url:"appointment_details.php",
                            method:"POST",
                            data:{evtdate:eventdate,patientid:pid,encounterid:encid,selectedids:selectedids,visittype:visitType},
                            success:function(result){
                               $('.day-event[date-year="' + year + '"][date-month="' + month + '"][date-day="' + day + '"]').html(result);
                               if(providerstates == "loaded"){
                                    $('.noappointments1').hide();
                                    $('#loader').hide();
                                }
                               $('[data-toggle="tooltip"]').tooltip()
                            },
                            error:function(event, jqxhr, settings, thrownError){
                                alert(jqxhr);
                                $('.loader').hide();
                            }
                        });
                    }
                    $(document).on("click",".event-calendar td",function(){
                        if($(this).hasClass('event')){
                             $('.noappointments').hide();
                             $j('tbody.event-calendar td').removeClass('active');
                             $j(this).addClass('active');
                             var enc_value = $('#hiddenenc_value').val();
                             ajaxcall($(this),<?php echo $id; ?>,enc_value);
                        }else{
                            $('.noappointments').show();
                        }
                    });
                    
                    $('#showmap').on("show.bs.modal", function(event){
                        var target = $(event.relatedTarget);
                        var modal = $(this);
                        modal.find('.modal-header').show();
                        var url = "map.php?hdnAddressBack1="+target.data("hdnAddressBack1")+"&pid="+target.attr("id");
                        modal.find(".modal-body").html("<iframe src='"+url+"' style='border:none; width:100%; height:100%'></iframe>");       
                    });
                    $('#modalwindow').on("show.bs.modal", function(event){
                        var target = $(event.relatedTarget);
                        var modal = $(this);
                        var url = target.data("href");
                        var modalclass = target.data("modalsize");
                        var frameheight = target.data("frameheight");
                        var modalbodypadding = target.data("bodypadding");
                        var title = target.data("title"); 
                        target.addClass("active");
                        modal.find('.modal-header').show();
                        modal.find('.modal-header #myModalLabel').html(title).css("font-weight","500");
                        modal.children("div").removeClass();
                        modal.children("div").addClass("modal-dialog "+modalclass);
                        modal.find(".modal-body").css("padding",modalbodypadding+"px");
                        modal.find(".modal-body").css("margin-bottom","-5px");
                        modal.find(".modal-body").html("<iframe src='"+url+"' style='border:none; width:100%; height:"+frameheight+"px;'></iframe>");
                        modaltarget = target;
                    });
                    
                     $('#modalwindow').on("hidden.bs.modal", function(event){
                         if(modaltarget.hasClass("active"))
                            modaltarget.removeClass("active"); 
                     });
                     
                     function get_calendar_icons(selectedprovider,visitType){
                        calendar.init();
                        $('#ourevent').html("");
                        $('#loader').show();
                        providerstates = "loading";
                        $.ajax({
                            url:"get_calendar_appointments.php",
                            method:"POST",
                            data:{selectedprovider:selectedprovider},
                            success:function(result){
                               providerstates = "loaded";
                               $('#ourevent').html(result);
                               $('#loader').hide();
                               calendar.init();
                               displayEvent1();
                               $('.noappointments1').hide();
                               if($(".current-day").hasClass('event')){
                                    var enc_value = $('#hiddenenc_value').val();
                                    ajaxcall($(".current-day"),<?php echo $id; ?>,enc_value,visitType);
                               }
                            },
                            error:function(event, jqxhr, settings, thrownError){
                                alert(jqxhr);
                                $('.loader').hide();
                            }
                        });
                    }
                    
                });

                function closeModalWindow(){
                    $('#modalwindow').modal('hide');
                }               
            </script>
            <div class="modal fade" name = "showmap" id="showmap" tabindex="-1" role="dialog">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
                             <h4 class="modal-title" id="myModalLabel">Appointments</h4>
                        </div>
                        <div class="modal-body">
                            
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" name = "newdemographics" id="modalwindow" tabindex="-1" role="dialog" aria-hidden="true" >
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header" style="background-color:#46a1b4; border-radius: 5px 5px 0px 0px;">
                            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="myModalLabel" >Add New Patient</h4>
                        </div>
                        <div class="modal-body">

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </body>
<script type="text/javascript">
    var _gaq = _gaq || [];
    _gaq.push(['_setAccount', 'UA-36251023-1']);
    _gaq.push(['_setDomainName', 'jqueryscript.net']);
    _gaq.push(['_trackPageview']);
    (function() {
      var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
      ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'https://www') + '.google-analytics.com/ga.js';
      var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
    })();
</script>

</html>

