<?php 
    function showtitleBlock($title,$print=false,$img=""){
        echo "<div class='title'>";
        if($img!="")
          echo"<img class='icon' src=$img alt=''>";
        echo "<h1>$title</h1>";
        
        if($print){
            echo "<div id='assistiveicons'>";
            echo "<a id='printerfriendlylink' href='#' target='_blank' onclick='return false;'><img class='assistiveicon' src='images/print.png' alt='Printer friendly page--New window will open' title='Printer friendly page'></a>";
            echo "<a href='#' onclick='return false;' target='_blank'><img class='assistiveicon' src='images/help.png' alt='Help--New window will open' title='Help'></a>";
            echo "</div>";
        }
        echo "</div>";
    }
    
    function showoption1($title){
        echo "<p class='noprint'>Click on <span class='bold'>About This $title</span> to see additional information regarding a medication.</p>";
        echo "<p class='noprint'>You may want to <a href='#'>request a renewal</a>.</p>";
    }
?>
<!DOCTYPE html>
<html>
    <head>
	<title>All care</title>
	<meta http-equiv="content-type" content="text/html; charset=windows-1252">
	<meta http-equiv="X-UA-Compatible" content="IE=9">
	<link type="text/css" rel="stylesheet" href="css/col.css"/>
	<link type="text/css" rel="stylesheet" href="css/theme.css"/>
	<link type="text/css" rel="stylesheet" href="css/ourside.css"/>
	<link type="text/css" rel="stylesheet" href="css/replace.css"/>
        <link type="text/css" rel="stylesheet" href="css/customize.css"/>
        <script type="text/javascript" src="js/jquery-1.7.1.js"></script>
        <script type="text/javascript" src="js/customize.js"></script>   
    </head>
<body class="inside menuontop color1 md_medslist">
    <div id="skipnavigation">
        <a id="skiplink" href="#maintop">Skip navigation to main content</a>
    </div>
    <div id="wrap"><!-- Begin Site Wrapper -->
        <div id="banner" role="banner"><!-- Begin Banner -->
            <a href="#" title="MyChart">
                <img src="images/banner.png" alt="MyChart">
            </a>
            &nbsp;
            <div id="bannertext">
                    <span class="welcome">Welcome,</span>
                    <br>
                    Suresh Ketha
            </div>
            <div class="blockbgclicks" role="navigation">
                <a id="btnLogout" class="gradientbutton" href="#" onclick="return false;">Log Out</a>
            </div>
        </div>
        <ul id="proxyTabs" class="noprint">
                <li class="color1 curr">
                    <a href="index.php" title="Select to access the record of Suresh">
                        <img id="photoImg_t0" src="images/ProxySilhouette.png" class="photo color1 disabled" alt="">
                        <span class="tabname">Suresh</span>
                    </a>
                </li>
                <li id="configIconTab" class="config">
                    <a id="wrenchLink" href="?page=Personalize">
                        <span><img title="Edit Personalization Options" alt="Edit Personalization Options" src="images/wrench.png"></span>
                    </a>
                </li>
        </ul>
        <div style="visibility: visible;" id="menu" role="navigation" class="collapsible"><!-- Begin Menu -->
            <div class="menugroup home"><span class="mnutitle"><a href="?page=home"><img src="images/menuhome.png" alt="">Home</a></span>
            </div>
            <div class="menugroup topmenu">
                <span class="mnutitle"><a href="#" onclick="return false;"><img src="images/medical_record_menu.png" alt="">My Medical Reports</a></span>
                <ul>
                    <li><a href="?page=option1">option1</a></li>
                    <li><a href="?page=option2">option2</a></li>
                </ul>
            </div>
            
            <div class="menugroup topmenu">
                <span class="mnutitle"><a href="#" onclick="return false;"><img src="images/inbox_menu.png" alt="">Lab Test Results</a></span>
                <ul>
                    <li><a href="?page=Results1">Results1</a></li>
                    <li><a href="?page=Results2">Results2</a></li>
                </ul>
            </div>
            <div class="menugroup moremenu topmenu">
                <span class="mnutitle"><a href="#" onclick="return false;"><img src="images/appointments_menu.png" alt="">Appointments</a></span>

                <div class="hiddenlist">

                    <div class="menugroup">
                        <span class="mnutitle"><a href="#" onclick="return false;"><img src="images/appointments_menu.png" alt="">Appointments</a></span>

                        <ul>
                            <li><a href="?page=UpcomingAppointments">Upcoming <abbr title="Appointments">Appts</abbr></a></li>
                            <li><a href="?page=CancelAppointments">Cancel an <abbr title="Appointments">Appt</abbr></a></li>
                            <li><a href="?page=RequestAppointments">Request an <abbr title="Appointment">Appt</abbr></a></li>
                            <li><a href="?page=ScheduleAppointments">Schedule an <abbr title="Appointment">Appt</abbr></a></li>
                        </ul>
                    </div>
                    <div class="menugroup">
                        <span class="mnutitle"><a href="#" onclick="return false;"><img src="images/appointments_past_menu.png" alt="">Visit History</a></span>
                        <ul>
                            <li><a href="?page=PastAppointments">Past Appts</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="menugroup topmenu">
                <span class="mnutitle"><a href="#" onclick="return false;"><img src="images/billing_menu.png" alt="">Billing and Insurance</a></span>
                <ul>
                    <li><a href="?page=BillingAcctSummary">Billing Acct Summary</a></li>
                    <li><a href="?page=CoverageDetails">Coverage Details</a></li>
                </ul>
        </div>
        <div class="menugroup topmenu">
                <span class="mnutitle"><a href="#" onclick="return false;"><img src="images/preferences_menu.png" alt="">My Account</a></span>
                <ul>
                    <li><a href="?page=ChangePassword">Change Password</a></li>
                    <li class="mnuFtr"><a href="?page=Personalize">Personalize</a></li>
                </ul>
        </div>
        <div class="separator"></div>	<a class="hidden" href="#menu">Menu Top</a>
    </div>    <!-- End Menu -->
    
    <div style="min-height: 305px;" id="main" role="main"><!-- Begin Main -->
	<?php
            switch ($_GET['page']){
                case "option1":
                    showtitleBlock("option1",true,"images/prescriptions.png");
                    showoption1("option1");
                    break;
                case "option2":
                    showtitleBlock("option2",true,"images/prescriptions.png");
                    showoption1("option2");
                    break;
                case "Results1":
                    showtitleBlock("Results1",true,"images/prescriptions.png");
                    showoption1("Results1");
                    break;
                case "Results2":
                    showtitleBlock("Results2",true,"images/prescriptions.png");
                    showoption1("Results2");
                    break;
                case "UpcomingAppointments":
                    showtitleBlock("UpcomingAppointments",true,"images/prescriptions.png");
                    showoption1("UpcomingAppointments");
                    break;
                case "CancelAppointments":
                    showtitleBlock("CancelAppointments",true,"images/prescriptions.png");
                    showoption1("CancelAppointments");
                    break;
                case "RequestAppointments":
                    showtitleBlock("RequestAppointments",true,"images/prescriptions.png");
                    showoption1("RequestAppointments");
                    break;
                case "ScheduleAppointments":
                    showtitleBlock("ScheduleAppointments",true,"images/prescriptions.png");
                    showoption1("ScheduleAppointments");
                    break;
                case "PastAppointments":
                    showtitleBlock("PastAppointments",true,"images/prescriptions.png");
                    showoption1("PastAppointments");
                    break;
                case "BillingAcctSummary":
                    showtitleBlock("BillingAcctSummary",true,"images/prescriptions.png");
                    showoption1("BillingAcctSummary");
                    break;
                case "CoverageDetails":
                    showtitleBlock("CoverageDetails",true,"images/prescriptions.png");
                    showoption1("CoverageDetails");
                    break;
                 case "ChangePassword":
                    showtitleBlock("ChangePassword",true,"images/prescriptions.png");
                    showoption1("ChangePassword");
                    break;
                case "Personalize":
                    showtitleBlock("Personalize",true,"images/prescriptions.png");
                    showoption1("Personalize");
                    break;
                default:
                    showtitleBlock("Home",false,"images/prescriptions.png");
                    showoption1("Home");
            }
             
        ?>
	<div class="back">
            <a class="gradientbutton" href="index.php" title="Back">Back to the Home Page</a>
	</div>
            
</div><!-- End Main -->
<div class="" id="blftwrapper" role="contentinfo"><!-- Begin Footer -->
    <div id="footer">
        <p>Copyright  &copy;2015 RiseCorp.</p>
    </div>
</div><!-- End Footer -->
</body>
</html>