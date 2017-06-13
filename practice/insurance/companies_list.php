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



require_once("../verify_session.php");


//STOP FAKE REGISTER GLOBALS
$fake_register_globals=false;

//continue session
session_start();

//landing page definition -- where to go if something goes wrong
$landingpage = "../../index.php?site=".$_SESSION['site_id']; 


if ( isset($_SESSION['portal_username']) ) {    
    $portal_user = $_SESSION['portal_username']; 
}else {
    session_destroy();
    header('Location: '.$landingpage.'&w');
    exit;
} 
$subpage = "Insurance Company List";
$pagename = "insurance";
$ignoreAuth=true; // ignore the standard authentication for a regular OpenEMR user
//include_once('../../../interface/globals.php');
include '../section_header.php';


if(isset($_SESSION['portal_username']) !=''){
    $provider    = $_SESSION['portal_username'];
    $refer       = $_REQUEST['refer'];
    
    if($_REQUEST['refer']!='')
        $_SESSION['refer'] = $_REQUEST['refer']; 
}else {
   $provider                     = $_REQUEST['provider'];
    $_SESSION['portal_username'] = $_REQUEST['provider'];
    //for logout
    $refer                       = $_REQUEST['refer'];
    if($_REQUEST['refer']!='')
        $_SESSION['refer'] = $_REQUEST['refer'];
}

$sql=sqlStatement("SELECT id, fname, lname, specialty FROM users " .
      "WHERE active = 1 AND ( info IS NULL OR info NOT LIKE '%Inactive%' ) " .
      "AND authorized = 1 AND username='".$provider."'" .
      "ORDER BY lname, fname");
$id = sqlFetchArray($sql);
$id1    = $id['id'];

//get user custom attributes
$custom=sqlStatement("select benefits_delete from tbl_user_custom_attr_1to1 where userid=".$_SESSION['portal_userid']);
$cdata=sqlFetchArray($custom);
?>
<!DOCTYPE html>
<html>

	<head>
            <meta charset="UTF-8">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
	    <meta name="viewport" content="width=device-width, initial-scale=1">
            <title>All Care Central</title>
            <link href='//fonts.googleapis.com/css?family=Roboto+Condensed:400,300' rel='stylesheet' type='text/css'>
            <link rel="stylesheet" href="assets/css/version1.0/dataTables.bootstrap.min.css"/>
            <link rel="stylesheet" href="assets/css/version1.0/responsive.bootstrap.min.css"/>
            <link rel="stylesheet" href="assets/css/perfect-scrollbar.min.css"/>
            <link rel="stylesheet" href="assets/css/jquery.steps.css"/>
            <link rel="stylesheet" href="assets/css/textext.core.css"/>
            <link rel="stylesheet" href="assets/css/textext.plugin.tags.css"/>
            <link rel="stylesheet" href="assets/css/textext.plugin.focus.css"/>
            <script src="assets/js/responsive_datatable/version1.0/jquery-1.11.3.min.js"></script>
            <script src="assets/js/responsive_datatable/version1.0/jquery.dataTables.min.js"></script>
            <script src="assets/js/responsive_datatable/version1.0/dataTables.responsive.min.js"></script>
            <script type='text/javascript' src='assets/js/responsive_datatable/dataTables.tableTools.js'></script>
            <script type='text/javascript' src='assets/js/perfect-scrollbar.min.js'></script>
            <script type='text/javascript' src='assets/js/jquery.steps.min.js'></script>
            <script type='text/javascript' src='assets/js/responsive_datatable/dataTables.bootstrap.js'></script>
            
                <style>

                    @media screen and (max-width: 767px) {

                        #column{
                            width:185px !important;
                        }
                        main#content {
                          margin-top: 65px;
                          transition: all ease-out 0.3s;
                        }
                    }
                    
                    .DTTT.btn-group{
                         float: right;
                         padding-left: 13px;
                         position: relative;
                     }
                     #vnfFilter1_length{
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
                    
                    .panel-heading{
                        padding: 0;
                    }
                    a:hover{
                      text-decoration: none;
                    }
                    .panel-title{
                        display: block;
                        padding: 10px 15px;
                        text-decoration: none;
                    }
                    #services .container-fluid{
                       margin-left: 10px;
                       margin-right: 10px;
                    }
                    .form-control, .input-group-addon {
                        border-radius: 4px;
                    }
                    .input-group-addon{
                         padding: 0 6px !important;
                    }
                    .panel-heading a:after {
                        font-family:'Glyphicons Halflings';
                        content:"\e114";
                        float: right;
                        color: grey;
                    }
                    .panel-heading a.collapsed:after {
                        content:"\e080";
                    }
                    .topspacing{
                        margin-top: 15px;
                    }
                    .calendar{
                        z-index: 2;
                    }
                    .encountercell > td{
                        background: #ccffff !important;
                    }
                    .encountercell + .child >td{
                        background: #ccffff !important;
                    }
                    table.dataTable > tbody > .encountercell + tr.child:hover {
                        background: #ccffff !important;
                    }
                    .tab-pane {
                        margin-top: 10px;
                    }

                    /*sidebar*/

                    .sidebar {
                        background: #edf8f8 none repeat scroll 0 0;
                        -webkit-box-shadow: -9px 0px 10px -3px rgba(0,0,0,0.35);
                        -moz-box-shadow: -9px 0px 10px -3px rgba(0,0,0,0.35);
                        box-shadow: -9px 0px 10px -3px rgba(0,0,0,0.35);
                        box-sizing: border-box;
                        color: #000;
                        height: 100%;
                        right:-472px;
                        max-width: 462px;
                        overflow: auto;
                        position: fixed;
                        top: 0px;
                        transition: all 0.3s cubic-bezier(0.35, 0.38, 0.07, 0.83) 0s;
                        width: 100%;
                        z-index: 9990;
                        -webkit-user-select: none;  /* Chrome all / Safari all */
                        -moz-user-select: none;     /* Firefox all */
                        -ms-user-select: none;      /* IE 10+ */
                        user-select: none; 
                    }

                    @media only screen and (max-width: 600px){
                        .sidebar {
                            right:-300px;
                            max-width: 290px;
                        }
                    }

                    .sidebar .sideclose::after {
                        clear: both;
                        content: "";
                        display: inline;
                        visibility: hidden;
                    }

                    .sidebar .sideclose .btn-close,.sidebar .sidebackbtn .btn-back {
                        background: #cf2828 none repeat scroll 0 0;
                        box-shadow: none;
                        color: #fff;
                        float: left;
                        font-size: 15px;
                        padding: 5px 12px;
                        z-index: 1;
                    }

                    .sidebar.right .sideclose .btn-close {
                        float: left;
                    }
                    .sidebar .sidebackbtn .btn-back{
                        float: right;
                        display: none;
                    }
                    
                    #benefitscontent{
                        display: none;
                    }

                    .sidebar.right .sideclose a {
                        background: #99e024 none repeat scroll 0 0;
                        color: #fff;
                        display: inline-block;
                        padding: 10px 20px;
                        text-decoration: none;
                        transition: all 0.5s ease 0s;
                    }

                    .sidebar .content {
                        line-height: 1.5;
                    }

                    .sidebar .content, .sidebar .benefitscontent {
                        padding: 10px 10px 10px;
                    }

                    .plan > div {
                        display: table-cell;
                        width: 100%;
                    }
                     .plan .form-group{
                        margin-bottom:5px;
                    }

                    .sidebar .content .plan,.sidebar .benefitscontent .plan{
                        position: relative;
                        background: #e5e5e5;
                        padding: 1em;
                        border-radius: 3px;
                        padding: 7px 11px;
                        display: table;
                        margin-bottom: 5px;
                        width: 100%;
                    }

                    .panshow td{
                        background: #7bd6e9 !important;
                    }

                    .tabframe{
                        border: 0px;
                        width: 100%;
                        height: 400px;
                    }

                    #zirmedpayersinfo{
                        height: 350px;
                        width: 200px;
                        overflow: auto;
                        padding-right: 0px !important;
                    }

                    #zirmedpayersinfo > div{
                        display: block;
                        overflow: hidden;
                        text-overflow: ellipsis;
                        white-space: nowrap;
                        padding: 5px;
                        cursor: pointer;
                        background: #ececec none repeat scroll 0 0;
                        border-bottom: 1px solid #ccc;
                    }

                    #zirmedpayersinfo > div:hover{
                        background-color: #ccc;
                    }
                    .zirmedpayeractive{
                         background-color: #ccc !important;
                    }

                    #zheader{
                        display: table;
                        margin-bottom: 21px;
                        width: 100%;
                        border-bottom: 1px solid #000;
                        padding-bottom: 7px;
                    }
                    .tabcontrol > .content{
                        height: 31.5em;
                        padding-top: 0px;
                    }
                    
                    .wizard ul, .tabcontrol ul{
                        text-align: right;
                    }
                    .wizard ul > li, .tabcontrol ul > li{
                        display: inline;
                        padding: 5px;
                    }
                    
                    .tabcontrol > .content > .body{
                        width: 100%;
                    }
                    .text-label {
                        font-size: 17px;
                        font-weight: normal;
                    }
                    .aliastages{
                        font-size: 14px !important;
                        padding-bottom: 15px !important;
                    }
                    .text-core .text-wrap .text-tags .text-tag .text-button{
                        height: 27px;
                    }
                    .text-core .text-wrap .text-tags .text-tag {
                        padding: 3px;
                    }
                    
                    .tabcontrol > .content{
                        height: 27em;
                        padding-top: 0px;
                    }

                    .wizard ul, .tabcontrol ul{
                        text-align: right;
                    }
                    .wizard ul > li, .tabcontrol ul > li{
                        display: inline;
                        padding: 5px;
                    }

                    .tabcontrol > .content > .body{
                        width: 100%;
                    }

                    .tabframe{
                        border: 0px;
                        width: 100%;
                        height: 357px;
                    }
                    #generateins .modal-dialog {
                        position: absolute;
                        top: 10px;
                        right: 100px;
                        bottom: 0;
                        left: 0;
                        z-index: 10040;
                    }
                    
                    #viewinfomodal .modal-body, #newplayermodal .modal-body{
                        min-height: 400px;
                    }
                    /*loadr css*/
                    
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

                    @keyframes fadeit{
                          0%{ opacity:1; }
                          100%{ opacity:0;}
                    }
                    .penalheader{
                        display: block;
                        margin-bottom: 33px;
                    }
                    .penaltitle{
                        position: absolute;
                        text-align: center;
                        width: 100%;
                        z-index: -1;
                    }
                    
                    .modal{
                        z-index: 10000;
                    }
                    .modal-backdrop{
                        z-index: 9999;
                    }
                    
                       
                 </style>
   <script language="javascript"> 
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
	</head>
	<body><?php //include 'header_nav.php'; ?>
             <section id= "services">
                <div class= "container-fluid">
		    <div class= "row">
			<div class= "col-lg-12 col-sm-12 col-xs-12" style=''>
                            <div class="sidebar" id="sidebar-right">
                                <div class="penalheader">
                                    <div class="sideclose">
                                        <a href="#" id="sidebar-close" class="btn-close">Close</a>
                                    </div>
                                    <div class="penaltitle">
                                        <h4 style="font-weight: 400;"></h4>
                                    </div>
                                    <div class="sidebackbtn">
                                        <a href="#" id="sidebar-backbtn" class="btn-back">Back</a>
                                    </div>
                                </div>
                                <div class="content">
                                    
                                </div>
                                <div class="benefitscontent">
                                    
                                </div>
                            </div>
                       <div  style='margin-top:10px'> <!-- start main content div -->
                           <div id="dvLoading1" style="display:none"></div>
                             <div id="div_noform">
<!--                                 <a class="add_new_plan btn btn-primary btn-sm" href="import_csv.php" data-payername="1-888-OHIOCOMP" data-payer="13162" href="javascript:void(0);" style="float: right; margin-left: 5px;">&nbsp;Upload</a>-->
                                 <a class="generate_ins btn btn-primary btn-sm" data-state="manual" href="javascript:void(0);" data-toggle="modal" data-target="#generateins" style="float: right; margin-left: 5px;"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span>&nbsp;Add Manual Insurance</a>                                 
                            <table class='table table-striped table-bordered dt-responsive nowrap' id='vnfFilter1' cellspacing="0" width="100%">
                                <thead>
                                    <tr>
<!--                                        <th>Parent Company Name</th>-->
                                        <th>Insurance Name</th>
<!--                                        <th>Insurance ID</th>-->
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $sql1   = sqlStatement("SELECT `id`,`name` FROM `insurance_companies` LIMIT 0,1000");
                                        //echo "<pre>"; print_r($row1); exit;
                                        $dbPayers = [];
                                        while($row1  = sqlFetchArray($sql1)){
                                            
                                            $uniqueIDQry = sqlStatement("SELECT `uniqueid` FROM `tbl_inscomp_custom_attr_1to1` WHERE `insuranceid` = '".$row1['id']."'");
                                            $uniqueIDRes = sqlFetchArray($uniqueIDQry);
                                            
                                            $manualLabel = '';
                                            
                                            if($uniqueIDRes['uniqueid'] == 'manual'){
                                                $manualLabel = '&nbsp;&nbsp;&nbsp;&nbsp;<span class="label label-warning">Manual</span>';
                                            }
                                            ?>
                                    
                                            <tr>
                                                <td><?php echo $row1['name']."".$manualLabel; ?></td>
<!--                                                <td><?php //echo $row1['payer_id']; ?></td>-->
                                                <td>
                                                    <a class="edit_btn btn btn-primary btn-sm" data-toggle="modal"  data-uniqueid="<?php echo $uniqueIDRes['uniqueid']; ?>" data-state="edit" data-target="#generateins" data-id="<?php echo $row1['id']; ?>" href="javascript:void(0);"><span class="glyphicon glyphicon-edit" aria-hidden="true"></span>&nbsp;Edit</a>
                                                    <a class="add_new_plan btn btn-primary btn-sm plansidepan" href="javascript:void(0);" data-id="<?php echo $row1['id']; ?>"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span>&nbsp;Plans</a>
                                                    <a class="add_new_plan btn btn-primary btn-sm" href="javascript:void(0);" data-toggle="modal" data-target="#newplayermodal" data-modaltitle="Add New Plan" data-frameurl="insurance_companies/insurance_plan_form.php?insid=<?php echo $row1['id']; ?>&formaction=add" data-id="<?php echo $row1['id']; ?>" data-loaderinfo="Plan Info Loading..."><span class="glyphicon glyphicon-plus" aria-hidden="true"></span>&nbsp;Add New Plan</a>
                                                    <a class="addalias btn btn-primary btn-sm" data-id="<?php echo $row1['id']; ?>" href="javascript:void(0);" data-toggle="modal" data-target="#aliasmodal">Add Alias</a>
                                                </td>
                                            </tr>
                                    <?php    }
                                    ?>
                                </tbody>
                            </table>
                        </div> <!-- end main content div -->
                    </div> <!-- end report_parameters --> 
                         </div>
                </div>
                 <br><br>
                </div>
		</section>
          
                 <?php include '../section_footer.php'; ?> 
<!--		<script type="text/javascript" src="assets/js/bootstrap.min.js"></script>-->
                <script type='text/javascript' src='assets/js/responsive-tabs.js'></script>
                <script type='text/javascript' src='assets/js/textext.core.js'></script>
                <script type='text/javascript' src='assets/js/textext.plugin.tags.js'></script>
                <script type='text/javascript' src='assets/js/textext.plugin.focus.js'></script>
		<script type='text/javascript'>   
                    var targetindex = 0;
                    var user_custom='<?php echo $cdata['benefits_delete']; ?>'
                    function restoreSession() {
                        var ca = document.cookie.split('; ');
                        for (var i = 0; i < ca.length; ++i) {
                         var c = ca[i].split('=');
                         if (c[0] == oemr_session_name && c[1] != oemr_session_id) {
                          document.cookie = oemr_session_name + '=' + oemr_session_id + '; path=/';
                         }
                        }
                        return true;
                    }
                   var linkurl= "../helplinks.php";
                   var prsetting = "../practiceload.php";
                   var userprofile = "../userprofile.php"; 
                   $(document).ready(function(){
                       
                       var currentpan = null;
                     $( document ).on("click","ul.nav.nav-tabs a", function ( e ) {
                        e.preventDefault();
                        //if($(this).data("gotab") == "active")
                            $( this ).tab( 'show' );
                     });
                    fakewaffle.responsiveTabs( [ 'xs', 'sm' ] );                 
                    
                    $('#generateins').on('shown.bs.modal', function(event) {
                         var target = $(event.relatedTarget);
                         var modal  = $(this);
                         var infohtml = "";
                         modal.data("state",target.data("state"));
                         var urltab2 = "insurance_companies/general_edit.php?formid="+target.data("id")+"&uniqueid="+target.data("id")+"&state="+target.data("state");
                         
                         target.data("action")== "manual"?modal.find(".modal-title").html("Add Manual Insurance"):modal.find(".modal-title").html("Edit Insurance");
                            modal.find(".modal-body").html('<div id="wizard">\n\
                                <!--<h2>Zirmed Info</h2>\n\
                                <section>\n\
                                   <div id="playerblock" class="row">\n\
                                         <div id="playerinfo" class="col-xs-8">\n\
                                         </div>\n\
                                   </div>\n\
                                </section> -->\n\
                                <h2>Insurance Info</h2>\n\
                                <section>\n\
                                </section>\n\
                                <h2>Insurance Attributes</h2>\n\
                                <section>\n\
                                </section>\n\
                            </div>'); 
                                $('#generateins').data("fnished","none");
                                $("#wizard").steps({
                                headerTag: "h2",
                                bodyTag: "section",
                                transitionEffect: "none",
                                enableFinishButton: true,
                                enablePagination: true,
                                enableAllSteps: false,
                                titleTemplate: "#title#",
                                cssClass: "tabcontrol",
                                autoFocus: true,
                                onStepChanging: function (event, currentIndex, newIndex){
                                    /*if(currentIndex == 0){
                                        return true;  
                                    }*/if(currentIndex == 0){
                                        if($('#inscop').contents().find('.required-field-block input').val() == ""){
                                            document.getElementById('inscop').contentWindow.submitform();
                                            return false;
                                        }else{
                                            document.getElementById('inscop').contentWindow.submitform();
                                        }    
                                        return true;
                                    }else{
                                        return true;
                                    }
                                },
                                onStepChanged: function (event, currentIndex, priorIndex){
                                    if(currentIndex == 0 && $("#wizard-p-0").children().length == 0){
                                        //$("#wizard-p-1").html('<iframe src="'+urltab2+'" class="tabframe" id="inscop"></iframe>')
                                    }else if(currentIndex == 1 && $("#wizard-p-1").children().length == 0){
                                        var url = "insurance_companies/insurance_full_1to1.php?uniqueid="+target.data("id")+"&state="+target.data("state");
                                        $("#wizard-p-1").html('<iframe id="inscop2" src="'+url+'" class="tabframe"></iframe>')
                                    }
                                  return false;							
                                },
                                onFinishing: function (event, currentIndex){
                                  return true;
                                },
                                onFinished: function (event, currentIndex){
                                    document.getElementById('inscop2').contentWindow.submitme();
                                    $('#generateins').data("fnished","fnish");
                                    $('#generateins').modal('hide');
                                     if(target.data("state")== "manual"){
                                        adddatatotable($("#inscop").data("formdata"),$("#inscop").data("formid"));
                                    }else{
                                       var name = (function(data,needfield){
                                            var fieldval = "";
                                            $.each(data,function(index,value){
                                                if(needfield == value["name"]){
                                                    fieldval = value["value"];
                                                    return false;
                                                }
                                            });
                                            return fieldval;
                                        })($("#inscop").data("formdata"),"name");
                                        
                                       var tdata = table.row( target.parents('tr') ).data();
                                       tdata[0] = target.data("uniqueid")== "manual"? name+'&nbsp;&nbsp;&nbsp;&nbsp;<span class="label label-warning">Manual</span>':name
                                       table.row( target.parents('tr') ).data(tdata).draw();
                                    }
                                }
                            });
                            $("#wizard-p-0").html('<iframe src="'+urltab2+'" class="tabframe" id="inscop"></iframe>')
                            $('.actions ul li a').addClass("btn btn-primary");
                         
                    });
                    
                    var table = $('#vnfFilter1').DataTable({
                            dom: 'T<\"clear\">lfrtip',
                            "iDisplayLength": 25,
                          tableTools: {
                               aButtons: []
                           }
                    });
                       
                    function adddatatotable(data,formid){
                        var name = (function(data,needfield){
                            var fieldval = "";
                            $.each(data,function(index,value){
                                if(needfield == value["name"]){
                                    fieldval = value["value"];
                                    return false;
                                }
                            });
                            return fieldval;
                        })(data,"name");
                        
                        table.row.add( [
                           name+'&nbsp;&nbsp;&nbsp;&nbsp;<span class="label label-warning">Manual</span>',
                           '<a class="edit_btn btn btn-primary btn-sm" data-toggle="modal"  data-uniqueid="manual" data-state="edit" data-target="#generateins" data-id="'+formid+'"><span class="glyphicon glyphicon-edit" aria-hidden="true"></span> Edit</a>\n\
                            <a class="add_new_plan btn btn-primary btn-sm plansidepan" href="javascript:void(0);" data-id="'+formid+'"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span>&nbsp;Plans</a>\n\
                            <a class="add_new_plan btn btn-primary btn-sm" href="javascript:void(0);" data-toggle="modal" data-target="#newplayermodal" data-modaltitle="Add New Plan" data-frameurl="insurance_companies/insurance_plan_form.php?insid='+formid+'&formaction=add" data-id="'+formid+'" data-loaderinfo="Plan Info Loading..."><span class="glyphicon glyphicon-plus" aria-hidden="true"></span>&nbsp;Add New Plan</a>\n\
                            <a class="addalias btn btn-primary btn-sm" data-id="'+formid+'" href="javascript:void(0);" data-toggle="modal" data-target="#aliasmodal">Add Alias</a>'
                        ]).draw( false );
                        
                    }
                            
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
                            Ps.initialize(document.querySelector('.sidebar'));
                            $(".sideclose").click(function(event){
                                event.preventDefault();
                                currentpan.parents("tr").removeClass("panshow");
                                $("#sidebar-right").css("right","-"+($("#sidebar-right").width()+10)+"px");
                            });
                            $(document).on("click",".plansidepan",function(evt){
                                if($(this).parents("tbody").find(".panshow").length != 0){
                                    return;
                                }
                                currentpan = $(this);
                                $(".benefitscontent").hide();
                                $("#sidebar-backbtn").hide();
                                $("#sidebar-right .content").show();
                                $(".penaltitle > h4").html($(this).parent().prev().html())
                                $(this).parents("tr").addClass("panshow");
                                $("#sidebar-right .content").html("");
                                $(".penalheader").css("padding-bottom","0px");
                                var planid = $(this).data("id");
                                $.ajax({url:"ajaxgetplans.php",method:"POST",data:{insid:planid},
                                    success: function (data, textStatus, jqXHR) {
                                        $("#sidebar-right .content").html('')
                                        var plandata =  $.parseJSON(data);
                                        var planhtml = "";
                                        $.each(plandata["insplans"],function(index, value){
                                            //console.log("index: " + index + " : value : " + JSON.stringify(value))
                                            planhtml += '<div class="plan">\n\
                                                            <div class="plancontent">\n\
                                                               <div style="display:table; width:100%;">\n\
                                                                   <div style="display:table-row;"><div style="display:table-cell; width:50%;">Plan Name </div><div style="display:table-cell; width:50%;">:&nbsp;&nbsp;'+value['planname']+'</div>\n\</div>\n\
                                                                   <div style="display:table-row;"><div style="display:table-cell; width:50%;">Plan Summary </div><div style="display:table-cell; width:50%;">:&nbsp;&nbsp;'+value['plan_summary']+'</div>\n\</div>\n\
                                                                   <div style="display:table-row;"><div style="display:table-cell; width:50%;">Insurance Type </div><div style="display:table-cell; width:50%;">:&nbsp;&nbsp;'+value['insurance_type']+'</div></div>\n\
                                                               </div>\n\
                                                            </div>\n\
                                                            <div class="planbtns">\n\
                                                                <div class="form-group"><a class="btn btn-primary btn-sm" data-toggle="modal" data-target="#newplayermodal" data-modaltitle="Edit Plan" data-loaderinfo="Plan Info Loading..." data-frameurl="insurance_companies/insurance_plan_form.php?insid='+value['insuranceid']+'&formaction=edit&planid='+value['id']+'" >&nbsp;Edit</a></div>\n\
                                                                <div class="form-group"><a class="btn btn-primary btn-sm benefitspage" data-plenid="'+value['id']+'" data-planname="'+value['planname']+'">&nbsp;Benefits</a></div>\n\
                                                                <div class="form-group"><a class="btn btn-primary btn-sm" data-toggle="modal" data-target="#newplayermodal" data-modaltitle="Add New Benefit" data-loaderinfo="Benefit Info Loading..." data-frameurl="insurance_companies/insurance_benefit_form.php?formaction=add&planid='+value['id']+'&instype='+value['ins_type']+'">&nbsp;Add Benefit</a></div>\n\
                                                            </div>\n\
                                                        </div>';
                                                        
                                        });
                                        if(plandata["insplans"].length ==0)
                                            planhtml = "<div style='text-algin:center'>No Plans!</div>"
                                        $("#sidebar-right .content").html(planhtml)
                                        
                        
                                    },error: function (jqXHR, textStatus, errorThrown) {
                                        
                                    }
                                });
                                $("#sidebar-right").css("right","0px");
                            });
                            
                             $(document).on("click",".benefitspage",function(evt){
                                 $("#sidebar-right .content").hide();
                                 $("#sidebar-backbtn").show();
                                 $(".benefitscontent").show();
                                 $(".benefitscontent").html("");
                                 $(".penalheader").css("padding-bottom","10px");
                                 $(".penaltitle > h4").append('<br/><small>'+$(this).data("planname")+'</small>')
                                // planname
                                 $.ajax({url:"ajaxgetbenefits.php",method:"POST",data:{planid:$(this).data("plenid")},
                                    success: function (data, textStatus, jqXHR) {
                                        var plandata =  $.parseJSON(data);
                                        //console.log(plandata)
                                        //console.log(user_custom);
                                        var planhtml = "";
                                        $.each(plandata["insbenefits"],function(index, value){
                                            //console.log("index: " + index + " : value : " + JSON.stringify(value))
                                            planhtml += '<div class="plan">\n\
                                                            <div class="plancontent">\n\
                                                               <div style="display:table; width:100%;">\n\
                                                                   <div style="display:table-row;"><div style="display:table-cell; width:50%;">Period To </div><div style="display:table-cell; width:50%;">:&nbsp;&nbsp;'+value['period_to']+'</div>\n\</div>\n\
                                                                   <div style="display:table-row;"><div style="display:table-cell; width:50%;">Period From </div><div style="display:table-cell; width:50%;">:&nbsp;&nbsp;'+value['period_from']+'</div>\n\</div>\n\
                                                                   <div style="display:table-row;"><div style="display:table-cell; width:50%;">Coverage For</div><div style="display:table-cell; width:50%;">:&nbsp;&nbsp;'+value['coverage_for']+'</div></div>\n\
                                                               </div>\n\
                                                            </div>\n\
                                                            <div class="planbtns">\n\
                                                                <div class="form-group"><a class="btn btn-primary btn-sm" data-toggle="modal" data-modaltitle="Edit Benefit" data-target="#newplayermodal" data-loaderinfo="Benefit Info Loading..." data-frameurl="insurance_companies/insurance_benefit_form.php?formaction=edit&planid='+value['planid']+'&benefitid='+value['id']+'&instype='+value['ins_type']+'">&nbsp;Edit</a></div>\n\
                                                                <div class="form-group"><a class="btn btn-primary btn-sm" data-toggle="modal" data-target="#benefitspreview" data-benefitid="'+value['id']+'">&nbsp;Preview</a></div>';
                                                                if(user_custom.toLocaleLowerCase() == "yes")
                                                                    planhtml += '<div class="form-group"><a class="btn btn-primary btn-sm" data-toggle="modal" data-target="#deleteconform" data-action="delete_benifit.php?id='+value['id']+'" data-backdrop="static" data-parent=".benefitscontent .plan" data-benefitid="'+value['id']+'">&nbsp;Delete</a></div>';
                                                             planhtml += '</div>\n\
                                                        </div>';
                                                        
                                        });
                                        if(plandata["insbenefits"].length ==0)
                                            planhtml = "<div style='text-algin:center'>No Benefits!</div>"
                                        $("#sidebar-right .benefitscontent").html(planhtml)
                                        
                        
                                    },error: function (jqXHR, textStatus, errorThrown) {
                                        
                                    }
                                });
                             });
                             $("#sidebar-backbtn").click(function(evt){
                                 evt.preventDefault();
                                 $(this).hide();
                                 $("#sidebar-right .benefitscontent").hide();
                                 $("#sidebar-right .content").show();
                                 $(".penalheader").css("padding-bottom","0px");
                                 $(".penaltitle > h4 > small,.penaltitle > h4 > br").remove();
                             });
                            
                            $('.custom-search-form').hide();
                            $(document).on("click",".player-search",function(evt){
                                evt.preventDefault();
                                $('.custom-search-form').slideToggle();
                            });
                            $(document).on("click","#clearbtn",function(evt){
                                $(this).parent().parent().find("input").val("");
                                 $('#zirmedpayersinfo div').show();
                            });
                            $(document).on("click","#zirmedpayersinfo div",function(){
                                var infohtml = "";
                                $('#zirmedpayersinfo div').removeClass("zirmedpayeractive");
                                $(this).addClass("zirmedpayeractive");
                                var data = $(this).data("items");
                                delete data.id;
                                $.each(data,function(index,value){
                                    infohtml += '<div class="row">\n\
                                                    <label class="col-sm-6 col-xs-4 control-label" style="text-transform: capitalize;">'+index.replace(/_/g," ")+'</label>\n\
                                                    <div class="col-sm-6 col-xs-8">'+value+'\n\
                                                    </div>\n\
                                                </div>';
                                });
                                $("#playerinfo").html(infohtml);
                            });                        
                            
                            $('#generateins').on('hide.bs.modal', function(e){
                                
                                if($('#generateins').data("fnished") != "fnish" && $(this).data("state")== "manual")
                                 var r = confirm("Do you want to close this popup?");
                                else
                                 var r = true;
                                
                                var formid = $('#inscop').data("formid");
                                 if (r == true) {
                                     if(formid != undefined && $('#generateins').data("fnished") != "fnish"){
                                        $.ajax({url:"updatedata.php",type:"post",data:{action:'removeins',formid:formid},
                                            success: function (data, textStatus, jqXHR) {
                                                
                                            },error: function (jqXHR, textStatus, errorThrown) {

                                            }
                                        });
                                    }
                                 } else {
                                   e.preventDefault();
                                   e.stopImmediatePropagation();
                                   return false; 
                                 } 

                            });
                
                            $('.actions ul li a').addClass("btn btn-primary");                            
                            $("#aliasmodal").on("show.bs.modal", function(event){
                                
                                var target = $(event.relatedTarget);
                                var modal = $(this);
                                if(target.data("id") != modal.data("currentid")){
                                    modal.data("currentid",target.data("id"))
                                    modal.find(".modal-body").html('<textarea id="textarea" class="aliastages" rows="1" placeholder="Enter Alias Name"></textarea>');                                
                                    $.fn.textext.css = '.text-core{position:relative;background:#fff;}\n.text-core .text-wrap{position:absolute;}\n.text-core .text-wrap textarea,.text-core .text-wrap input{-webkit-box-sizing:border-box;-moz-box-sizing:border-box;box-sizing:border-box;-webkit-border-radius:0;-moz-border-radius:0;border-radius:0;border:1px solid #CCC;outline:none;resize:none;position:absolute;z-index:1;background:none;overflow:hidden;margin:0;padding:4px;white-space:nowrap;font:13px "lucida grande",tahoma,verdana,arial,sans-serif;line-height:18px;height:auto}\n.text-core .text-wrap .text-arrow{-webkit-box-sizing:border-box;-moz-box-sizing:border-box;box-sizing:border-box;position:absolute;top:0;right:0;width:22px;height:23px;background:url("data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAkAAAAOAQMAAADHWqTrAAAAA3NCSVQICAjb4U/gAAAABlBMVEX///8yXJnt8Ns4AAAACXBIWXMAAAsSAAALEgHS3X78AAAAHHRFWHRTb2Z0d2FyZQBBZG9iZSBGaXJld29ya3MgQ1MzmNZGAwAAABpJREFUCJljYEAF/xsY6hkY7BgYZBgYOFBkADkdAmFDagYFAAAAAElFTkSuQmCC") 50% 50% no-repeat;cursor:pointer;z-index:2}\n.text-core .text-wrap .text-dropdown{-webkit-box-sizing:border-box;-moz-box-sizing:border-box;box-sizing:border-box;padding:0;position:absolute;z-index:3;background:#fff;border:1px solid #CCC;width:100%;max-height:100px;padding:1px;font:13px "lucida grande",tahoma,verdana,arial,sans-serif;display:none;overflow-x:hidden;overflow-y:auto;}\n.text-core .text-wrap .text-dropdown.text-position-below{margin-top:1px}\n.text-core .text-wrap .text-dropdown.text-position-above{margin-bottom:1px}\n.text-core .text-wrap .text-dropdown .text-list .text-suggestion{padding:4px;cursor:pointer;}\n.text-core .text-wrap .text-dropdown .text-list .text-suggestion em{font-style:normal;text-decoration:underline}\n.text-core .text-wrap .text-dropdown .text-list .text-suggestion.text-selected{color:#fff;background:#6d84b4}\n.text-core .text-wrap .text-focus{-webkit-box-shadow:0 0 6px #6d84b4;-moz-box-shadow:0 0 6px #6d84b4;box-shadow:0 0 6px #6d84b4;position:absolute;width:100%;height:auto;display:none;}\n.text-core .text-wrap .text-focus.text-show-focus{display:block}\n.text-core .text-wrap .text-prompt{-webkit-box-sizing:border-box;-moz-box-sizing:border-box;box-sizing:border-box;position:absolute;width:100%;height:auto;margin:1px 0 0 2px;font:13px "lucida grande",tahoma,verdana,arial,sans-serif;color:#c0c0c0;overflow:hidden;white-space:pre;}\n.text-core .text-wrap .text-prompt.text-hide-prompt{display:none}\n.text-core .text-wrap .text-tags{-webkit-box-sizing:border-box;-moz-box-sizing:border-box;box-sizing:border-box;position:absolute;width:100%;height:auto;padding:3px 35px 3px 3px;cursor:text;}\n.text-core .text-wrap .text-tags.text-tags-on-top{z-index:2}\n.text-core .text-wrap .text-tags .text-tag{float:left;}\n.text-core .text-wrap .text-tags .text-tag .text-button{-webkit-border-radius:2px;-moz-border-radius:2px;border-radius:2px;-webkit-box-sizing:border-box;-moz-box-sizing:border-box;box-sizing:border-box;position:relative;float:left;border:1px solid #CCC;background:#FEFEFE;color:#000;padding:1px 22px 0 8px;margin:0 2px 2px 0;cursor:pointer;height:26px;font:13px "lucida grande",tahoma,verdana,arial,sans-serif;}\n.text-core .text-wrap .text-tags .text-tag .text-button a.text-remove{position:absolute;right:3px;top:4px;display:block;width:11px;height:11px;background:url("data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAsAAAAhCAYAAAAPm1F2AAAABHNCSVQICAgIfAhkiAAAAAlwSFlzAAALEgAACxIB0t1+/AAAAB50RVh0U29mdHdhcmUAQWRvYmUgRmlyZXdvcmtzIENTNS4xqx9I6wAAAQ5JREFUOI2dlD0WwiAQhCc8L6HHgAPoASwtSYvX8BrQxtIyveYA8RppLO1jE+LwE8lzms2yH8MCj1QoaBzH+VuUYNYMS213UlvDRamtUbXb5ZyPHuDoxwGgip3ipfvGuGzPz+vZ/coDONdzFuYCO6ramQQG0DJIE1oPBBvM6e9LqaS2FwD7FWwnVoIAsOc2Xn1jDlyd8pfPBRVOBHA8cc/3yCmQqt0jcY4LuTyAF3pOYS6wI48LAm4MUrx5JthgSQJAt5LtNgAUgEMBBIC3AL2xgo58dEPfhE9wygef89FtCeC49UwltR1pQrK2qr9vNr7uRTCBF3pOYS6wI4/zdQ8MUpxPI9hgSQL0Xyio/QBt54DzsHQx6gAAAABJRU5ErkJggg==") 0 0 no-repeat;}\n.text-core .text-wrap .text-tags .text-tag .text-button a.text-remove:hover{background-position:0 -11px}\n.text-core .text-wrap .text-tags .text-tag .text-button a.text-remove:active{background-position:0 -22px}';
                                    $.ajax({
                                        url:"update_alias.php",
                                        type: 'POST',
                                        data: {type:"all",aliasid:target.data("id")},
                                        success: function (data, textStatus, jqXHR) {
                                           var listitems = data !=""?JSON.parse(data):[];
                                           showaliasfield($('#textarea'),listitems,target.data("id")); 
                                        },
                                        error: function (jqXHR, textStatus, errorThrown) {
                        
                                        }
                                    });
                                }
                            });
                            
                            function showaliasfield($element,aliasnames,aliasid){
                            
                                $element.textext({
                                    plugins: 'tags',
                                    tags: {
                                        items: aliasnames
                                    }
                                }).bind('isTagAllowed', function(e, data)
                                {
                                    var formData = $(e.target).textext()[0].tags()._formData,
                                     list = eval(formData);
                                    // duplicate checking
                                    if (formData.length && list.indexOf(data.tag) >= 0) {
                                           var message = [ data.tag, 'is already listed.' ].join(' ');
                                           alert(message);
                                           data.result = false;
                                    }
                                }).on({
                                    enterKeyPress: function(e) {                                          
                                        $.ajax({
                                            url:"update_alias.php",
                                            type: 'POST',
                                            data: {type:"update",aliasid:aliasid,aliasnames:$(e.target).textext()[0].tags()._formData},
                                            success: function (data, textStatus, jqXHR) {
                                               
                                            },
                                            error: function (jqXHR, textStatus, errorThrown) {
                                                console.log("Error");
                                            }
                                        });
                                    }
                                }).bind('tagClick', function(e, tag, value, callback)
                                {
                                    var newValue = window.prompt('New value', value);
                                    var formData = $(e.target).textext()[0].tags()._formData,
                                     list = eval(formData);
                                    // duplicate checking
                                    if (formData.length && list.indexOf(newValue) >= 0) {
                                           var message = [ newValue, 'is already listed.' ].join(' ');
                                           alert(message);

                                    }else{
                                        if(newValue)
                                            callback(newValue, true);
                                        $.ajax({
                                            url:"update_alias.php",
                                            type: 'POST',
                                            data: {type:"update",aliasid:aliasid,aliasnames:$(e.target).textext()[0].tags()._formData},
                                            success: function (data, textStatus, jqXHR) {
                                               
                                            },
                                            error: function (jqXHR, textStatus, errorThrown) {
                                                console.log("Error");
                                            }
                                        });
                                    }

                                }).bind('tagRemove', function(e,tag,value)
                                {
                                    $.ajax({
                                        url:"update_alias.php",
                                        type: 'POST',
                                        data: {type:"update",aliasid:aliasid,aliasnames:$(e.target).textext()[0].tags()._formData},
                                        success: function (data, textStatus, jqXHR) {
                                            
                                        },
                                        error: function (jqXHR, textStatus, errorThrown) {
                                            console.log("Error");
                                        }
                                    });
                                });
                                            
                            }
                            
                            $("#newplayermodal").on('shown.bs.modal', function(event) {
                                var target = $(event.relatedTarget);
                                var modal  = $(this);
                                var frameurl = target.data("frameurl");
                                var loaderinfo = target.data("loaderinfo");
                                var getmodaltitle = target.data("modaltitle");
                                modal.find(".modal-title").html(getmodaltitle);
                                modal.css("min-height","400px");
                                targetindex = target.parents('.plan').index();
                                var loaderhtml = '<div id="loader">\n\
                                    <div class="ajax-spinner-bars">\n\
                                        <div class=\'bar-1\'>\n\
                                        </div><div class=\'bar-2\'>\n\
                                        </div><div class=\'bar-3\'></div>\n\
                                        <div class=\'bar-4\'></div>\n\
                                        <div class=\'bar-5\'></div>\n\
                                        <div class=\'bar-6\'></div>\n\
                                        <div class=\'bar-7\'></div>\n\
                                        <div class=\'bar-8\'></div>\n\
                                        <div class=\'bar-9\'></div>\n\
                                        <div class=\'bar-10\'></div>\n\
                                        <div class=\'bar-11\'></div>\n\
                                        <div class=\'bar-12\'></div>\n\
                                        <div class=\'bar-13\'></div>\n\
                                        <div class=\'bar-14\'></div>\n\
                                        <div class=\'bar-15\'></div>\n\
                                        <div class=\'bar-16\'></div>\n\
                                    </div>\n\
                                    <div id="loadertitle">'+loaderinfo+'</div>\n\
                                </div>'
                                modal.find(".modal-body").html(loaderhtml+'<iframe onload="planloaded()" src="'+frameurl+'" width="100%" height="400" style="border: 0"></iframe>')
                                $("#loader").show();
                            });
                            
                            $("#benefitspreview").on('shown.bs.modal', function(event) {
                                var target = $(event.relatedTarget);
                                var modal  = $(this);
                                var loaderhtml = '<div id="loader">\n\
                                    <div class="ajax-spinner-bars">\n\
                                        <div class=\'bar-1\'>\n\
                                        </div><div class=\'bar-2\'>\n\
                                        </div><div class=\'bar-3\'></div>\n\
                                        <div class=\'bar-4\'></div>\n\
                                        <div class=\'bar-5\'></div>\n\
                                        <div class=\'bar-6\'></div>\n\
                                        <div class=\'bar-7\'></div>\n\
                                        <div class=\'bar-8\'></div>\n\
                                        <div class=\'bar-9\'></div>\n\
                                        <div class=\'bar-10\'></div>\n\
                                        <div class=\'bar-11\'></div>\n\
                                        <div class=\'bar-12\'></div>\n\
                                        <div class=\'bar-13\'></div>\n\
                                        <div class=\'bar-14\'></div>\n\
                                        <div class=\'bar-15\'></div>\n\
                                        <div class=\'bar-16\'></div>\n\
                                    </div>\n\
                                    <div id="loadertitle">Loading Data...</div>\n\
                                </div>';
                                modal.find(".modal-body").html(loaderhtml);
                                 $("#loader").show();
                                 $.ajax({url:"ajaxgetbenefitpreview.php",data:{benefitid:target.data("benefitid")},type:"post",success: function (data, textStatus, jqXHR) {
                                        var previewdata = JSON.parse(data)
                                        var tabcontent = '<div class="tab-content responsive">';
                                        var tabhtml = '<ul class="nav nav-tabs responsive" id="myTab">';
                                        var count =0;
                                        $.each(previewdata,function(index,value){
                                            count++;
                                            if(count==1){
                                                tabhtml += '<li class="active"><a href="#'+index.replace(/ /g,"_")+'">'+index.replace(/_/g," ")+'</a></li>';
                                                tabcontent += '<div class="tab-pane active" id="'+index.replace(/ /g,"_")+'" data-target="'+index+'">';
                                            }else{
                                                tabhtml += '<li><a href="#'+index.replace(/ /g,"_")+'">'+index.replace(/_/g," ")+'</a></li>';
                                                tabcontent += '<div class="tab-pane" id="'+index.replace(/ /g,"_")+'" data-target="'+index+'">';
                                            }
                                            $.each(value,function(vindex,vvalue){
                                                vvalue = vvalue==""?"--":vvalue;
                                                tabcontent += '<div class="row">\n\
                                                    <label class="col-sm-4 col-xs-4 control-label text-capitalize">'+vindex.replace(/_/g," ")+'</label>\n\
                                                        <div class="col-sm-8 col-xs-8">'+vvalue+'</div>\n\
                                                   </div>';
                                            })
                                            tabcontent += '</div>';
                                        });
                                        tabhtml += "</ul>";
                                        tabcontent += '</div>';
                                        modal.find(".modal-body").html(tabhtml+tabcontent);
                                    fakewaffle.responsiveTabs( [ 'xs', 'sm' ] );
                       
                                    },error: function (jqXHR, textStatus, errorThrown) {
                                        
                                    }
                                })
                                
                            });
                            
                             $('#deleteconform').on('shown.bs.modal', function(event) {
                                var target = $(event.relatedTarget);
                                var modal  = $(this);
                                modal.data("actionurl",target.data("action"));
                                modal.data("planbox",target.data("parent"));
                                modal.data("targetindex",target.parents('.plan').index());
                                modal.find(".modal-body").html("Do you want to delete this Benefit?");
                             });
                             
                             $('#deleteconformbtn').on("click",function(evt){
                                 $this = $(this);
                                 $this.button('loading');                                 
                                 $.ajax({url:$(this).parents('.modal').data('actionurl'),type:"get",success: function (data, textStatus, jqXHR) {
                                         $this.button('reset');
                                         $($this.parents('.modal').data('planbox')).eq($this.parents('.modal').data('targetindex')).remove()
                                         $('#deleteconform').modal('hide');
                                    },error: function (jqXHR, textStatus, errorThrown) {
                                        $this.button('reset');                                       
                                        alert("textStatus")
                                    }
                                })
                             });
                             
                            $('#viewinfomodal').on('shown.bs.modal', function(event) {
                                var target = $(event.relatedTarget);
                                var modal  = $(this);
                                modal.find("#myTab li").show();
                                modal.find("#myTab li").removeClass("active");
                                modal.find(".tab-content > div").removeClass("active");
                                modal.find("#myTab li").first().addClass("active"); 
                                modal.find(".tab-content > div").first().addClass("active"); 
                                $.ajax({url:"ajaxpreviewins.php",type: 'POST',data:{uniqueid:target.data("id")},success: function (data, textStatus, jqXHR) {
                                        var modaldata = $.parseJSON(data);
                                        modal.find(".tab-content > div[data-target]").each(function(index,value){
                                            //console.log($(this).data("target"))
                                            var tbcontenar = $(this);
                                            var tbhtml = "";
                                            if(modaldata[$(this).data("target")].constructor.toString().indexOf("Object") != -1){
                                                $.each(modaldata[$(this).data("target")],function(i,v){
                                                    if(v == '') v = "--";
                                                    if(v.constructor.toString().indexOf("Array") !=-1) v = v.join(",");
                                                    tbhtml += '<div class="row">\n\
                                                                <label class="col-sm-6 col-xs-4 control-label text-capitalize">'+i.replace(/_/g," ")+'</label>\n\
                                                                    <div class="col-sm-6 col-xs-8">'+v+'</div>\n\
                                                               </div>';
                                                });
                                                tbcontenar.html(tbhtml);
                                            }else{
                                                tbcontenar.parent().prev().find("li").eq(tbcontenar.index()).hide();
                                                if(tbcontenar.hasClass("active")){
                                                    tbcontenar.next().addClass("active");
                                                    tbcontenar.parent().prev().find("li").eq(tbcontenar.next().index()).addClass("active");
                                                }
                                            }
                                            
                                        })
                                        
                                    },
                                    error: function (jqXHR, textStatus, errorThrown) {
                        
                                    }
                                })
                                
                            });
                            
                        }) ; 
                        
                        function planloaded(){
                            var loader = document.getElementById("loader");
                            loader.style.display = "none";
                        }
                        function hidemodal(data,penaltype){
                            
                            if(penaltype == "plans"){
                                var value = $.parseJSON(data);
                                var planhtml = "";
                                planhtml += '<div class="plan">\n\
                                                <div class="plancontent">\n\
                                                   <div style="display:table; width:100%;">\n\
                                                       <div style="display:table-row;"><div style="display:table-cell; width:50%;">Plan Name </div><div style="display:table-cell; width:50%;">:&nbsp;&nbsp;'+value['planname']+'</div>\n\</div>\n\
                                                       <div style="display:table-row;"><div style="display:table-cell; width:50%;">Plan Summary </div><div style="display:table-cell; width:50%;">:&nbsp;&nbsp;'+value['plan_summary']+'</div>\n\</div>\n\
                                                       <div style="display:table-row;"><div style="display:table-cell; width:50%;">Insurance Type </div><div style="display:table-cell; width:50%;">:&nbsp;&nbsp;'+value['insurance_type']+'</div></div>\n\
                                                   </div>\n\
                                                </div>\n\
                                                <div class="planbtns">\n\
                                                    <div class="form-group"><a class="btn btn-primary btn-sm" data-toggle="modal" data-target="#newplayermodal" data-modaltitle="Edit Plan" data-loaderinfo="Plan Info Loading..." data-frameurl="insurance_companies/insurance_plan_form.php?insid='+value['insuranceid']+'&formaction=edit&planid='+value['id']+'" >&nbsp;Edit</a></div>\n\
                                                    <div class="form-group"><a class="btn btn-primary btn-sm benefitspage" data-plenid="'+value['id']+'" data-planname="'+value['planname']+'">&nbsp;Benefits</a></div>\n\
                                                    <div class="form-group"><a class="btn btn-primary btn-sm" data-toggle="modal" data-target="#newplayermodal" data-modaltitle="Add New Benefit" data-loaderinfo="Benefit Info Loading..." data-frameurl="insurance_companies/insurance_benefit_form.php?formaction=add&planid='+value['id']+'&instype='+value['ins_type']+'">&nbsp;Add Benefit</a></div>\n\
                                                </div>\n\
                                            </div>';
                                
                                $(".content .plan").eq(targetindex).replaceWith(planhtml);
                            }else if(penaltype == "benefits"){
                                var value = $.parseJSON(data);
                                console.log(value);
                                var planhtml = "";
                                planhtml += '<div class="plan">\n\
                                                <div class="plancontent">\n\
                                                   <div style="display:table; width:100%;">\n\
                                                       <div style="display:table-row;"><div style="display:table-cell; width:50%;">Period To </div><div style="display:table-cell; width:50%;">:&nbsp;&nbsp;'+value['period_to']+'</div>\n\</div>\n\
                                                       <div style="display:table-row;"><div style="display:table-cell; width:50%;">Period From </div><div style="display:table-cell; width:50%;">:&nbsp;&nbsp;'+value['period_from']+'</div>\n\</div>\n\
                                                       <div style="display:table-row;"><div style="display:table-cell; width:50%;">Coverage For</div><div style="display:table-cell; width:50%;">:&nbsp;&nbsp;'+value['coverage_for']+'</div></div>\n\
                                                   </div>\n\
                                                </div>\n\
                                                <div class="planbtns">\n\
                                                    <div class="form-group"><a class="btn btn-primary btn-sm" data-toggle="modal" data-modaltitle="Edit Benefit" data-target="#newplayermodal" data-loaderinfo="Benefit Info Loading..." data-frameurl="insurance_companies/insurance_benefit_form.php?formaction=edit&planid='+value['planid']+'&benefitid='+value['id']+'&instype='+value['ins_type']+'">&nbsp;Edit</a></div>\n\
                                                    <div class="form-group"><a class="btn btn-primary btn-sm" data-toggle="modal" data-target="#benefitspreview" data-benefitid="'+value['id']+'">&nbsp;Preview</a></div>\n\
                                                </div>\n\
                                            </div>';
                                 $(".benefitscontent .plan").eq(targetindex).replaceWith(planhtml);            
                            }
                            
                            $("#newplayermodal").modal('hide');
                        }
                            
		</script>                               
                <!--  alias popup -->
                <div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="aliasmodal" class="modal fade">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button aria-hidden="true" data-dismiss="modal" class="close" type="button"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title">Add Alias</h4>
                            </div>
                            <div class="modal-body" style="height:200px;">
                                
                            </div>
                        </div><!-- /.modal-content -->
                    </div><!-- /.modal-dialog -->
                </div>               
                
                <div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="generateins" class="modal fade">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button aria-hidden="true" data-dismiss="modal" class="close" type="button"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title">Generate Insurance Company</h4>
                            </div>
                            <div class="modal-body" id="disabledtabs">

                            </div>
                        </div><!-- /.modal-content -->
                    </div><!-- /.modal-dialog -->
                </div>
                                
                <!-- Modal -->
                <div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="viewinfomodal" class="modal fade">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button aria-hidden="true" data-dismiss="modal" class="close" type="button"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title">Preview</h4>
                            </div>
                            <div class="modal-body">
                                <ul class="nav nav-tabs responsive" id="myTab">
                                 <li class="active"><a href="#zirmed">Zirmed</a></li>
                                 <li><a href="#central">Central</a></li>
                                 <li><a href="#insattr">Insurance Attributes</a></li>
                               </ul>

                               <div class="tab-content responsive">
                                   <div class="tab-pane active" id="zirmed" data-target="zirmed">
                                   </div>
                                   <div class="tab-pane" id="central" data-target="central">
                                   </div>
                                   <div class="tab-pane" id="insattr" data-target="custom">
                                   </div>
                               </div>
                            </div>
                        </div><!-- /.modal-content -->
                    </div><!-- /.modal-dialog -->
                </div><!-- /.modal -->
                
                <!-- Modal -->
                <div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="benefitspreview" class="modal fade">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button aria-hidden="true" data-dismiss="modal" class="close" type="button"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title">Preview</h4>
                            </div>
                            <div class="modal-body" style="min-height:400px;">
                            </div>
                        </div><!-- /.modal-content -->
                    </div><!-- /.modal-dialog -->
                </div><!-- /.modal -->
                                  
                <!-- Modal -->
                <div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="planmodal" class="modal fade">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button aria-hidden="true" data-dismiss="modal" class="close" type="button"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title">Plans</h4>
                            </div>
                            <div class="modal-body">
                                    
                            </div>
                        </div><!-- /.modal-content -->
                    </div><!-- /.modal-dialog -->
                </div><!-- /.modal -->
                                                     
                <!-- Modal -->
                <div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="newplayermodal" class="modal fade">
                    <div class="modal-dialog  modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button aria-hidden="true" data-dismiss="modal" class="close" type="button"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title">Add New Plan</h4>
                            </div>
                            <div class="modal-body">

                            </div>
                        </div><!-- /.modal-content -->
                    </div><!-- /.modal-dialog -->
                </div><!-- /.modal -->
                
                
                <!-- Modal -->
                <div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="deleteconform" class="modal fade">
                    <div class="modal-dialog  modal-sm">
                        <div class="modal-content">
                            <div class="modal-header">                                
                                <h4 class="modal-title">Delete</h4>
                            </div>
                            <div class="modal-body">
                                
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" id="deleteconformbtn" data-loading-text="Processing...">Delete</button>
                                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                            </div>
                        </div><!-- /.modal-content -->
                    </div><!-- /.modal-dialog -->
                </div><!-- /.modal -->
                                                          
	</body>

</html>