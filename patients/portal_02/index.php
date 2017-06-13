<?php 
    function showtitleBlock($title,$print=false,$img=""){
        echo "<div class='title'>";
        if($img!="")
          echo"<img class='icon' src=$img alt=''>";
        echo "<h1>$title</h1>";
        echo "</div>";
    }
    function showoption1($title){
        echo "<article class='content-text'><p class='noprint'>Click on <span class='bold'>About This $title</span> to see additional information regarding a medication.</p>";
        echo "<p class='noprint'>You may want to <a href='#'>request a renewal</a>.</p></article>";
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="content-type" content="text/html; charset=windows-1252">
	<meta http-equiv="X-UA-Compatible" content="IE=9">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>All care</title>
        <link rel="stylesheet" type="text/css" href="css/reset.css">
        <link rel="stylesheet" type="text/css" href="css/font-awesome.css">
        <link rel="stylesheet" type="text/css" href="css/style.css">
        <script type="text/javascript" src="js/jquery.js"></script>
        <script type="text/javascript" src="js/customscript.js"></script>
    </head>
<body>
    <div class="bodyblock">
        <div id="wrapper">
            <!-- noscript tage for check javascript enabled or not -->
            <noscript>
                <div class="noscript">Javascript must be enabled your browser</div>
            </noscript>
            <section class="container">
                <div class="clt"></div>
                <div class="ct"></div>
                <div class="crt"></div>
                <div class="cc">
                    <div class="cc2">
                        <div class="cl"></div>
                        <div class="cc3">
                            <header class="header">
                                <!-- logo -->
                                <div class="logo">
                                    <img alt="Health Portal Logo" onclick="location.href='#';" src="images/banner.png">
				</div>
                            </header>
                            <section class="container-content">
                                <!-- columetwo -->
                                <section id="columetwo">
                                    <div class="tc01">
                                        <div class="tc02">
                                            <!-- content -->
                                            <section id="content" style="padding-top:63px;padding-right:15px;padding-left:15px;">
                                            <?php
                                                switch ($_GET['page']){
                                                    case "lab_reports":
                                                        showtitleBlock("Lab / Diagnostic Records",true,"images/prescriptions.png");
                                                        showoption1("Lab / Diagnostic Records");
                                                        break;
                                                    case "visitSummary":
                                                        showtitleBlock("Visit Summary",true,"images/prescriptions.png");
                                                        showoption1("Visit Summary");
                                                        break;
                                                    case "phrRequest":
                                                        showtitleBlock("Personal Health Record",true,"images/prescriptions.png");
                                                        showoption1("Personal Health Record");
                                                        break;
                                                    case "eHealthReport":
                                                        showtitleBlock("PHR-View",true,"images/prescriptions.png");
                                                        showoption1("PHR-View");
                                                        break;
                                                    case "referral":
                                                        showtitleBlock("Referrals",true,"images/prescriptions.png");
                                                        showoption1("Referrals");
                                                        break;
                                                    case "IMMHISTORY":
                                                        showtitleBlock("Immunization History",true,"images/prescriptions.png");
                                                        showoption1("Immunization History");
                                                        break;
                                                    case "gChart":
                                                        showtitleBlock("Growth Chart",true,"images/prescriptions.png");
                                                        showoption1("Growth Chart");
                                                        break;
                                                    case "personalInfo_New":
                                                        showtitleBlock("Personal Information",true,"images/prescriptions.png");
                                                        showoption1("Personal Information");
                                                        break;
                                                    case "additionalInfo_New":
                                                        showtitleBlock("Additional Information",true,"images/prescriptions.png");
                                                        showoption1("Additional Information");
                                                        break;
                                                    case "resetPassword":
                                                        showtitleBlock("Reset Password",true,"images/prescriptions.png");
                                                        showoption1("Reset Password");
                                                        break;
                                                    case "eStatement":
                                                        showtitleBlock("Latest Statement",true,"images/prescriptions.png");
                                                        showoption1("Latest Statement");
                                                        break;
                                                     case "past_statements":
                                                        showtitleBlock("Past Statement",true,"images/prescriptions.png");
                                                        showoption1("Past Statement");
                                                        break;
                                                    case "healthRecordShareLog":
                                                        showtitleBlock("Transmit logs",true,"images/prescriptions.png");
                                                        showoption1("Transmit logs");
                                                        break;
                                                    case "messages":
                                                        showtitleBlock("Inbox",true,"images/prescriptions.png");
                                                        showoption1("Inbox");
                                                        break;
                                                     case "medication":
                                                        showtitleBlock("Refill Requests",true,"images/prescriptions.png");
                                                        showoption1("Refill Requests");
                                                        break;
                                                    case "referralrequest":
                                                        showtitleBlock("Referral Request",true,"images/prescriptions.png");
                                                        showoption1("Referral Request");
                                                        break;
                                                    case "askdoctor":
                                                        showtitleBlock("Ask Doctor",true,"images/prescriptions.png");
                                                        showoption1("Ask Doctor");
                                                        break;
                                                     case "appoint_view":
                                                        showtitleBlock("Upcoming Appointments",true,"images/prescriptions.png");
                                                        showoption1("Upcoming Appointments");
                                                        break;
                                                    case "new_appointment":
                                                        showtitleBlock("New Appointment",true,"images/prescriptions.png");
                                                        showoption1("New Appointment");
                                                        break;
                                                    case "prev_appnt_view":
                                                        showtitleBlock("Historical Appointments",true,"images/prescriptions.png");
                                                        showoption1("Historical Appointments");
                                                        break;
                                                     case "Questionnarie":
                                                        showtitleBlock("Medical History",true,"images/prescriptions.png");
                                                        showoption1("Medical History");
                                                        break;
                                                    case "SHandAllergiesDt":
                                                        showtitleBlock("Surgical and Allergies",true,"images/prescriptions.png");
                                                        showoption1("Surgical and Allergies");
                                                        break;
                                                    case "genericImmunization":
                                                        showtitleBlock("Immunizations",true,"images/prescriptions.png");
                                                        showoption1("Immunizations");
                                                        break;
                                                    case "prev_appnt_view":
                                                        showtitleBlock("Historical Appointments",true,"images/prescriptions.png");
                                                        showoption1("Historical Appointments");
                                                        break;
                                                     case "reminders_cdss":
                                                        showtitleBlock("Reminders",true,"images/prescriptions.png");
                                                        showoption1("Reminders");
                                                        break;
                                                    case "cdss":
                                                        showtitleBlock("CDSS Alerts",true,"images/prescriptions.png");
                                                        showoption1("CDSS Alerts");
                                                        break;
                                                    default:
                                                        showtitleBlock("Home",false,"images/prescriptions.png");
                                                        showoption1("Home");
                                                }

                                            ?>
                                            </section>
                                        </div>	
                                    </div>
                                </section>
                                <!-- columetwo end -->
                                
                                <!-- sidebar -->
                                <aside id="sidebar">
                                    <nav class="box">
                                        <!-- expand -->
                                        <header class="expand expandCustom">
                                            <a href="#"><span>Expand ALL</span><em>Collapse ALL</em></a>
                                        </header>
                                        <!-- add nav -->
                                        <ul class="add-nav" id="menulist">
                                            <li id="home"><a href="?page=home" class=""><span><i class="fa fa-home fa-lg"></i>Home</span></a></li>
                                            <li id="MedRecords"><a href="#" onclick="return false;" class="opener records"><span><i class="fa fa-clipboard fa-lg"></i>Medical records</span></a>
                                                <div style="display: none;" class="block" id="MedRecordsBlk">
                                                    <ul>
                                                        <li id="lab_reports" class="opener">
                                                            <a href="?page=lab_reports" class="ico-labs"><span style="background: none;"><span style="background: none;">Lab / Diagnostic R...</span></span></a>
                                                        </li>
                                                        <li id="visitSummary" class="opener">
                                                            <a href="?page=visitSummary"><span style="background: none;"><span style="background: none;">Visit Summary</span></span></a>
                                                        </li>
                                                        <li id="phrRequest" class="opener">
                                                            <a href="?page=phrRequest"><span style="background: none;"><span style="background: none;">Personal Health Record</span></span></a>
                                                        </li>
                                                        <li id="eHealthReport" class="opener">
                                                            <a href="?page=eHealthReport" class="ico-medical"><span style="background: none;"><span style="background: none;">PHR-View</span></span></a>
                                                        </li>
                                                        <li id="referral" class="opener">
                                                            <a href="?page=referral" class="ico-referrals"><span style="background: none;"><span style="background: none;">Referrals</span></span></a>
                                                        </li>
                                                        <li id="IMMHISTORY" class="opener">
                                                            <a href="?page=IMMHISTORY" class="ico-allergies"><span style="background: none;"><span style="background: none;">Immunization History</span></span></a>
                                                        </li>
                                                        <li id="gChart" class="opener">
                                                            <a href="?page=gChart" class=""><span style="background: none;"><span style="background: none;">Growth Chart</span></span></a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </li>
                                            <li id="Account"><a href="#" onclick="return false;" class="opener account"><span><i class="fa fa-user-md fa-lg"></i>My Account</span></a>
                                                <div style="display: none;" class="block" id="AccountBlk">
                                                    <ul>
                                                        <li id="personalInfo_New" class="opener">
                                                            <a href="?page=personalInfo_New" class="ico-personal"><span style="background: none;"><span style="background: none;">Personal Information</span></span></a>
                                                        </li>
                                                        <li id="additionalInfo_New" class="opener">
                                                            <a href="?page=additionalInfo_New" class="ico-personal"><span style="background: none;"><span style="background: none;">Additional Information</span></span></a>
                                                        </li>
                                                        <li id="resetPassword" class="opener">
                                                            <a href="?page=resetPassword" class="ico-password"><span style="background: none;"><span style="background: none;">Reset Password</span></span></a>
                                                        </li>
                                                        <li id="eStatement" class="opener">
                                                            <a href="?page=eStatement" class="ico-personal"><span style="background: none;"><span style="background: none;">Latest Statement</span></span></a>
                                                        </li>
                                                        <li id="past_statements" class="opener">
                                                            <a href="?page=past_statements" class="ico-personal"><span style="background: none;"><span style="background: none;">Past Statement</span></span></a>
                                                        </li>
                                                        <li id="healthRecordShareLog">
                                                            <a href="?page=healthRecordShareLog" class="" ><span>Transmit logs</span></a>
                                                        </li>	
                                                    </ul>
                                                </div>
                                            </li>
                                            <li id="MessageNav"><a href="#" onclick="return false;" class="opener messages"><span><i class="fa fa-envelope-o fa-lg"></i>Messages</span></a>
                                                <div style="display: none;" class="block" id="MessageNavBlk">
                                                    <ul>
                                                        <li id="messages" class="opener">
                                                            <a href="?page=messages" class="ico-inbox"><span style="background: none;"><span style="background: none;">Inbox</span></span></a>
                                                        </li>
                                                        <li id="medication" class="opener">
                                                            <a href="?page=medication" class="ico-medications"> <span style="background: none;"><span style="background: none;">Refill Requests</span></span></a>
                                                        </li>
                                                        <li id="referral" class="opener">
                                                            <a href="?page=referralrequest" class="ico-referrals"><span style="background: none;"><span style="background: none;">Referral Request</span></span></a>
                                                        </li>
                                                        <li id="askdoctor" class="opener">
                                                            <a href="?page=askdoctor" class="ico-password"><span style="background: none;"><span style="background: none;white-space:nowrap">Ask Doctor</span></span></a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </li>
                                            <li id="Appnt"><a href="#" onclick="return false;" class="opener appointments"><span><i class="fa fa-calendar fa-lg"></i>Appointments</span></a>
                                                <div style="display: none;" class="block" id="AppntBlk">
                                                    <ul>
                                                        <li id="appoint_view" class="opener">
                                                            <a href="?page=appoint_view" class="ico-appointments">
                                                                <span style="background: none;"><span style="background: none;white-space: nowrap;">Upcoming Appointments</span></span>
                                                            </a>
                                                        </li>
                                                        <li id="new_appointment" class="opener">
                                                            <a href="?page=new_appointment" class="ico-request"><span style="background: none;"><span style="background: none;">New Appointment</span></span></a>
                                                        </li>
                                                        <li id="prev_appnt_view" class="opener">
                                                            <a href="?page=prev_appnt_view" class="ico-history">
                                                                <span style="background: none;"><span style="background: none;">Historical Appoint...</span></span>
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </li>
                                            <li id="QuestionnaireNav"><a href="#" onclick="return false;" class="opener questionnaires"><span><i class="fa fa-file-text-o fa-lg"></i>Questionnaires</span></a>
                                                <div style="display: none;" class="block" id="QuestionnaireNavBlk">
                                                    <ul>
                                                        <li id="Questionnarie" class="opener">
                                                            <a href="?page=Questionnarie" class="ico-history"><span style="background: none;"><span style="background: none;">Medical History</span></span></a>
                                                        </li>
                                                        <li id="SHandAllergiesDt" class="opener">
                                                            <a href="?page=SHandAllergiesDt" class="ico-surgical"><span style="background: none;"><span style="background: none;">Surgical and Allergies</span></span></a>
                                                        </li>
                                                        <li id="genericImmunization" class="opener">
                                                            <a href="?page=genericImmunization" class="ico-allergies"><span style="background: none;"><span style="background: none;">Immunizations</span></span></a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </li>
                                            <li id="Alerts"><a href="#" onclick="return false;" class="opener tracker"><span><i class="fa fa-thumbs-up fa-lg"></i>Health Tracker</span></a>
                                                <div style="display: none;" class="block" id="AlertsBlk">
                                                    <ul>
                                                        <li id="reminders_cdss" class="opener">
                                                            <a href="?page=reminders_cdss" class="ico-visit"><span style="background: none;"><span style="background: none;">Reminders</span></span></a>
                                                        </li>
                                                        <li id="cdss" class="opener">
                                                            <a href="?page=cdss" class="ico-visit"><span style="background: none;"><span style="background: none;">CDSS Alerts</span></span></a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </li>
                                        </ul>
                                    </nav>
                                </aside>
                            </section>
                        </div>
                        <div class="cr"></div>
                    </div>
                </div>
                <div class="clb"></div>
                <div class="cb"></div>
                <div class="crb"></div>
            </section>
            <!-- footer -->
            <footer id="footer">
                <section class="holder">
                    <article class="frame">
                        <p>Copyright  &copy;2015 RiseCorp.</p>
                    </article>
                </section>
            </footer>	
        </div>
    </div>
</body>
</html>