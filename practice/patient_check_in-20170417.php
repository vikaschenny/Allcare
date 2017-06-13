<?php
require_once("verify_session.php");
$pid        = $_REQUEST['pid'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
  <style>
      body{
          overflow: hidden;
      }
       .page-content > .row{
            margin-left:0px;
            margin-right:0px;
            min-height: 100%;
            margin-bottom: -45px;
        }

        #wrapper {
            padding-left: 53px;
            transition: all .4s ease 0s;
            height: 100%;
            height: 100%
        }

        #sidebar-wrapper {
            margin-left: -245px;
            left: 53px;
            width: 245px;
            background: #46a1b4;//#222;
            position: fixed;
            height: 100%;
            z-index: 1;
            transition: all .4s ease 0s;
        }

        .sidebar-nav {
            display: block;
            float: left;
            width: 245px;
            list-style: none;
            margin: 0;
            padding: 0;
        }
        .sidebar-nav li{
            display: inline;
        }
        .page-content{
            min-height: 100%;
        }
        #page-content-wrapper {
            padding-left: 0;
            margin-left: 0;
            width: 100%;
            min-height: 100%;
            height: auto;
            margin-bottom: -45px;
        }
        #wrapper.active {
            padding-left: 245px;
        }
        #wrapper.active #sidebar-wrapper {
            left: 245px;
        }

        #page-content-wrapper {
          width: 100%;
          min-height: 100%;
        }
        #sidebar {
            height: 100%;
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
        }


        #sidebar_menu li a, .sidebar-nav li a {
            color: #fff; //#999;
            display: block;
            float: left;
            text-decoration: none;
            width: 245px;
            background: #46a1b4;//#252525;
            border-top: 1px solid #54afc2;//#373737;
            border-bottom: 1px solid #3893a6; //#1A1A1A;
            -webkit-transition: background .5s;
            -moz-transition: background .5s;
            -o-transition: background .5s;
            -ms-transition: background .5s;
            transition: background .5s;
        }
        .sidebar_name {
            padding-top: 25px;
            color: #fff;
            opacity: .7;
        }

        .sidebar-nav li {
          line-height: 40px;
          text-indent: 15px;
        }

        .sidebar-nav li a {
          color: #fff;//#999999;
          display: block;
          text-decoration: none;
        }

        .sidebar-nav li a:hover {
          color: #fff;
          background: rgba(255,255,255,0.2);
          text-decoration: none;
          cursor: pointer;
        }

        .sidebar-nav li a.active {
            background-color: rgba(76, 214, 245, 0.62);
            cursor: default;
        }

        .sidebar-nav li a:active,
        .sidebar-nav li a:focus {
          text-decoration: none;
        }

        .sidebar-nav > .sidebar-brand {
          height: 45px;
          line-height: 45px;
          font-size: 18px;
        }

        .sidebar-nav > .sidebar-brand a {
          color: #999999;
        }

        .sidebar-nav > .sidebar-brand a:hover {
          color: #fff;
          background: none;
        }

        #main_icon
        {
           float:right;
           padding-right: 20px;
           padding-top:13px;
        }
        .sub_icon
        {
            float:right;
           padding-right: 20px;
           padding-top:10px;
        }
        .content-header {
          height: 65px;
          line-height: 65px;
        }

        .content-header h1 {
          margin: 0;
          margin-left: 20px;
          line-height: 65px;
          display: inline-block;
        }

        @media (max-width:767px) {
            #wrapper {
                padding-left: 53px;
                transition: all .4s ease 0s;
            }
            #sidebar-wrapper {
                left: 53px;
            }
            #wrapper.active {
                padding-left: 53px;
            }
            #wrapper.active #sidebar-wrapper {
                left: 245px;
                width: 245px;
                transition: all .4s ease 0s;
            }
        }
        #content {
            position: relative;
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
                
        #iframecontenar{
            border: 0px;
        }
        #loadertitle{
            text-transform: capitalize;
        }
  </style>
  <script>
        $(document).ready(function() {
            if($(window).width() > 767)
              $("#wrapper").addClass("active");        

            $("#menu-toggle").click(function(e) {
                e.preventDefault();
                $("#wrapper").toggleClass("active");
            });
            $('#loader').show();
            $('#loader #loadertitle').html("Demographics  Loading...")
            $("#iframecontenar").attr("height",450);
            //alert(window.top.$("iframe").height());
        });
       function getpatientcheckinfo(element,url,event) {
           event.preventDefault();
           if($(element).hasClass("active"))
              return;
           $('#loader').show();
           $('#loader #loadertitle').html(url+" Loading..."); 
           $('#sidebar li a').removeClass("active");
           $(element).addClass("active");
            if(url == 'demographics'){
                 var postedurl = "patient_file/summary/demographics_full.php?pid=<?php echo $pid; ?>";
                 var data      = '';
            }
            if(url == 'insurance'){
                //var postedurl = "create_patient/insurance_edit.php?provider=<?php echo $provider; ?>&pid=<?php echo $pid; ?>&refer=<?php echo $refer; ?>";
                var postedurl = "patient_file/summary/insurancedata_full.php";
                var data      = '';
            }
            if(url == 'benefits'){
                var postedurl = "main/allcarereports/get_plan_benefits.php?plan_id=<?php echo $plan_id; ?>&payer_id=<?php echo $payer_id; ?>";
                var data      = '';
            }
            if(url == 'eligibility'){
                var postedurl = "verify_eligibility.php";
                var data      = '';
            }
            if(url == 'patientbalance'){
//                var postedurl = 'billing/patient-statement.php?form_pid=<?php echo $pid; ?>';
                var postedurl = 'billing/patient_custom_balance.php?form_pid=<?php echo $pid; ?>';
                var data      = '';
            }
            if(url == 'patientnotebook'){
                <?php
                $patient_notebook_link = '';
                $get_patient_notebook = sqlStatement("SELECT title FROM list_options WHERE list_id = 'allcareConfig' AND option_id = 'patient_notebook_link'");
                while($set_patient_notebook = sqlFetchArray($get_patient_notebook)){
                    $patient_notebook_link = $set_patient_notebook['title'];
                }
                ?>
                var postedurl       = '<?php echo $patient_notebook_link; ?>';
//                var postedurl = 'billing/patient-statement.php?form_pid=<?php echo $pid; ?>';
                var data        = '';
            }
            if(url == 'payment'){
                var postedurl = 'patient_file/front_payment.php';
                var data      = '';
            }
            $("#iframecontenar").attr("src",postedurl);
            
        }
        function showloader(){
            $('#loader').hide();
        }
  </script>
</head>
<body>

<div id="wrapper">
    <div id="sidebar-wrapper">
        <ul id="sidebar_menu" class="sidebar-nav">
           <li class="sidebar-brand"><a id="menu-toggle" href="#">Menu<span id="main_icon" class="glyphicon glyphicon-align-justify"></span></a></li>
        </ul>
        <ul class="sidebar-nav" id="sidebar">
            <li><a class="active" onclick = 'getpatientcheckinfo(this,"demographics",event)'>Demographics<span class="sub_icon glyphicon glyphicon-link"></span></a></li>
            <li><a onclick = 'getpatientcheckinfo(this,"insurance",event)' >Insurance<span class="sub_icon glyphicon glyphicon-link"></span></a></li>
<!--            <li><a onclick = 'getpatientcheckinfo(this,"benefits",event)'>Benefits<span class="sub_icon glyphicon glyphicon-link"></span></a></li>
            <li><a onclick = 'getpatientcheckinfo(this,"eligibility",event)'>Eligibility<span class="sub_icon glyphicon glyphicon-link"></span></a></li>
            <li><a onclick = 'getpatientcheckinfo(this,"payment",event)'>Patient Balance & Payments<span class="sub_icon glyphicon glyphicon-link"></span></a></li>-->
<!--            <li><a onclick = 'getpatientcheckinfo(this,"patientnotebook",event)'>Payment Notebook<span class="sub_icon glyphicon glyphicon-link"></span></a></li>
            <li><a onclick = 'getpatientcheckinfo(this,"payment",event)'>Payment<span class="sub_icon glyphicon glyphicon-link"></span></a></li>-->
        </ul>
    </div>
    <div id="page-content-wrapper">
        <div class="page-content inset">
                <div class="row">
                        <div id="content">
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
                                <div id="loadertitle">Demographics Loading...</div>
                            </div>
                            <iframe id="iframecontenar" class="help-contentFrame" src="patient_file/summary/demographics_full.php?pid=<?php echo $pid; ?>" onload="showloader()" height="100%" width="100%"></iframe>
                        </div>
                </div>
        </div>
    </div>        
</div>

</body>
</html>

