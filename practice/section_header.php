<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1 maximum-scale=1, user-scalable=no">
    <?php 
    $base_url = "//".$_SERVER['SERVER_NAME'].'/practice/';
    $portal_user=$_SESSION['portal_username'];
    $sql  = sqlStatement("select * from globals where gl_name='openemr_name'");
    $row1 = sqlFetchArray($sql);

    ?>
    <title><?php echo $row1['gl_value']; ?></title>		
    <link href='//fonts.googleapis.com/css?family=Abel' rel='stylesheet' type='text/css'>
    <link href='//fonts.googleapis.com/css?family=Roboto:400,300,500' rel='stylesheet' type='text/css'>
    <link href='//fonts.googleapis.com/css?family=Dosis:300,400,500,600' rel='stylesheet' type='text/css'>   
    <link href='//fonts.googleapis.com/css?family=Roboto+Condensed:400,300' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" type="text/css" href="<?php echo $base_url; ?>assets/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo $base_url; ?>assets/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo $base_url; ?>assets/css/main.css">
    <!-- for calendar's-->
    <link rel="stylesheet" href="<?php echo $base_url; ?>/css/style-personal.css">
    <link rel="stylesheet" type="text/css" href="<?php echo $base_url; ?>assets/css/customize.css">
    <script type="text/javascript" src="https://code.jquery.com/jquery-latest.min.js" ></script>
    <script type="text/javascript" src="<?php echo $base_url; ?>js/simplecalendar.js" ></script> 
    <script type="text/javascript" src="<?php echo $base_url; ?>assets/js/jquery.min.js"></script>
    <script type="text/javascript" src="https://code.jquery.com/ui/1.9.2/jquery-ui.js"></script>
    <script type="text/javascript" src="js/jquery.fullscreen.min.js"></script>
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
        .navbar-nav > li.active > .dropdown-menu{
            margin-top: 0px !important;
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
        .userlogout{
            
        }
        .userlogout .pull-right > li > .dropdown-menu::before, .userlogout > li > .dropdown-menu.pull-right::before {
            left: auto;
            right: 12px;
        }
        .userlogout .dropdown-menu::before {
            border-bottom: 7px solid rgba(0, 0, 0, 0.2);
            border-left: 7px solid transparent;
            border-right: 7px solid transparent;
            content: "";
            display: inline-block;
            left: 9px;
            position: absolute;
            top: -7px;
        }
        .userlogout .pull-right > li > .dropdown-menu::after, .navbar .nav > li > .dropdown-menu.pull-right::after {
            left: auto;
            right: 13px;
        }
        .userlogout  li > .dropdown-menu::after {
            border-bottom: 6px solid #ffffff;
            border-left: 6px solid transparent;
            border-right: 6px solid transparent;
            content: "";
            display: inline-block;
            left: 10px;
            position: absolute;
            top: -6px;
        }
        .userlogout .pull-right > li > .dropdown-menu, .userlogout > li > .dropdown-menu.pull-right {
            left: auto;
            right: 0;
        }
        .userlogout .open > .dropdown-menu {
            display: block;
        }
        .userlogout .dropdown-menu {
            background-clip: padding-box;
            background-color: #ffffff;
            border: 1px solid rgba(0, 0, 0, 0.2);
            border-radius: 6px;
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
            display: none;
            float: left;
            left: 0;
            list-style: outside none none;
            margin: 2px 0 0;
            min-width: 160px;
            padding: 5px 0;
            position: absolute;
            top: 100%;
            z-index: 1000;
        }
        .userlogout a:hover, .userlogout a:focus{
            text-decoration: none;
        }
        .userlogout .dropdown-menu li a:hover{
            color:#fff;
            background-color: #46a1b4;
        }
        @media only screen and (min-width: 768px){
            #userprofile .modal-lg {
                width: 85%;
            }
        }
        #userprofile .modal-body{
            padding: 0px;
        }
        #userprofile .modal-body #profileframe{
             border: 0 none;
            min-height: 420px;
        }
        .linkcontent{
            margin: 18px;
        }
        
        .help-iconTextComponent-edit{
            float: right;
            position: absolute;
            right: 13px;
            top: 27%;
        }

 </style>

</head>
<section class= "navs">
    <nav class="navbar navbar-default navbar-fixed-top" role="navigation">
<!--        <div style="background-color:#e7e7e7 !important; height:39px; padding-top:4px; padding-right:20px;"><div style="float:right !important;" ><?php echo $_SESSION['portal_userfullname']; echo "&nbsp;&nbsp;" ?><a  class="btn btn-default btn-sm" data-toggle="modal" data-target="#logout" style="color:black; "><span class="glyphicon glyphicon-lock"></span>  Logout</a> <a  class="btn btn-default btn-sm" id="helpop" style="color:black; "><span class="glyphicon glyphicon-question-sign"> </span>  Help</a></div></div>-->
        <div style="background-color:#e7e7e7 !important; height:39px; padding-top:4px; padding-right:20px;">
            <div class="pull-right userlogout">
                <select id="practiceid">
                    <option>Select Practice ID</option>
                </select>
                <ul class="pull-right">
                    <li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown">Welcome, <?php echo $_SESSION['portal_userfullname']; ?> <b class="caret"></b></a>
                        <ul class="dropdown-menu">
                            <li><a href="#" data-toggle="modal" data-target="#userprofile"><i class="glyphicon glyphicon-user"></i> User Profile</a></li>
                            <li><a href="#" id="helpop"><i class="glyphicon glyphicon-question-sign"></i> Help</a></li>
                            <li class="divider"></li>
                            <li><a href="<?php echo $base_url3; ?>/practice/logout_page.php?provider=<?php echo $provider ; ?>&refer=<?php echo $refer; ?>"><i class="glyphicon glyphicon-off"></i> Logout</a></li>
                        </ul>
                    </li>
                </ul>
              </div>
        </div>
            <div class="container-fluid">
                <!-- Brand and toggle get grouped for better mobile display -->
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <?php $sql  = sqlStatement("select * from globals where gl_name='openemr_name'");
                          $row1 = sqlFetchArray($sql);?>
                    <a class="navbar-brand logo" href="home.php">
                           <?php echo $row1['gl_value']; ?>
                    </a>
                </div>
                <?php  $base_url3="https://".$_SERVER['SERVER_NAME'];  ?>
                <!-- Collect the nav links, forms, and other content for toggling -->
                <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                    <?php  
                    $sql_vis    = sqlStatement("SELECT provider_menus from tbl_user_custom_attr_1to1 where userid='".$_SESSION['portal_userid']."'");
                    $row1_vis   = sqlFetchArray($sql_vis);

                    if(!empty($row1_vis)) {
                        $avail3 = explode("|",$row1_vis['provider_menus']);
                        $count = 0;
                        $lislength = count($avail3);
                        $sql12 = sqlStatement("SELECT * FROM `list_options` WHERE list_id ='AllCareProviderPortal' ORDER BY seq");?>
                        <ul class='nav navbar-nav navbar-right'>
                            <?php 
                            while($row11  = sqlFetchArray($sql12)){
                                if(in_array($row11['option_id'], $avail3)){
                                    $_SESSION['option_id']=$row11['option_id'];
                                    $mystring = $row11['option_id'];
                                    $pos = strpos($mystring, '_');
                                    if(false == $pos) {
                                        $sql_lis    = sqlStatement("SELECT * FROM  `list_options` WHERE list_id ='AllCareProviderPortal' AND option_id = '$mystring' ORDER BY seq");
                                        while($row_lis  = sqlFetchArray($sql_lis)){
                                            if(in_array($row_lis['option_id'], $avail3)){
                                             $opt_id    = $row_lis['option_id']."_";
                                             $sql_li    = sqlStatement("SELECT * FROM  `list_options` WHERE list_id ='AllCareProviderPortal' AND option_id LIKE '%$opt_id%' ORDER BY seq");
                                             $count++;
                                             $lislength = $lislength - sqlNumRows($sql_li);
                                             if(sqlNumRows($sql_li) != 0 ){ 
                                                $dropdownclass = $lislength==$count?'dropdown-menu-right': 'dropdown-menu-left';
                                                ?>                            
                                                <li class="dropdown <?php if($row11['option_id'] == $pagename){ ?>active <?php }else{ ?>underline<?php } ?>"><a class="dropdown-toggle" data-toggle="dropdown" href="javascript:DoPost('<?php echo $base_url3."/practice/".$row_lis['notes']; ?>','<?php echo $portal_user;  ?>','<?php echo $_SESSION['refer'];  ?>')"><?php echo $row_lis['title']; ?> <b class="caret"></b></a>
                                                <ul class="dropdown-menu <?php echo $dropdownclass?>">
                                                    <?php 
                                                    while($row_li = sqlFetchArray($sql_li)){ 
                                                        if(in_array($row_li['option_id'], $avail3)){  
                                                            $ex = explode("_",$row_li['option_id']); 
                                                            if(count($ex)   == 2){
                                                                $sub1    = $ex[0]."_".$ex[1];
                                                                $sql_sub = sqlStatement("SELECT * FROM  `list_options` WHERE list_id ='AllCareProviderPortal' AND option_id = '$sub1' ORDER BY seq");
                                                                $row_sub = sqlFetchArray($sql_sub);
                                                                if($row_sub['title'] == $subpage){; ?>
                                                                    <li class="sbactive"><a><span><?php  echo $row_sub['title']; ?></span></a> 
                                                                    </li>
                                                          <?php }else{ ?>
                                                                    <li><a href="javascript:DoPost('<?php echo $base_url3."/practice/".$row_sub['notes']; ?>','<?php echo $portal_user;  ?>','<?php echo $_SESSION['refer'];  ?>')"><span><?php  echo $row_sub['title']; ?></span></a> 
                                                                    </li>
                                                                
                                                                <?php 
                                                                }
                                                            }
                                                        }  
                                                    } 
                                                    ?>
                                                </ul></li>
                                                <?php 
                                                }else{
                                                    if($row11['option_id'] == $pagename){
                                                        ?>
                                                            <li class="active"><a href="javascript:DoPost('<?php echo $base_url3."/practice/".$row11['notes']; ?>','<?php echo $portal_user;  ?>','<?php echo $_SESSION['refer'];  ?>')"><span><?php echo $row11['title']; ?></span></a></li>
                                                        <?php   }else { ?>
                                                            <li class="underline"><a href="javascript:DoPost('<?php echo $base_url3."/practice/".$row11['notes']; ?>','<?php echo $portal_user;  ?>','<?php echo $_SESSION['refer'];  ?>')"><span><?php echo $row11['title']; ?></span></a></li>
                                                        <?php   
                                                    }
                                                }
                                            }
                                        }
                                    }
                                } 
                            } ?>  
                        </ul>
                        <?php   
                    } 
                    ?>
                </div>    
            </div>
    </nav>
    <script>
        function showloader(){
            $('.helploader').hide();
        };
   </script>
    <div id="help_dialog" class="help_dialog" style="display: none; right: 15px; top:100px;">
        <div id="header" class="help-header">
            <div class="help-header-row">
                <div class="lineheader">
                    <button class="help-header-previousIcon help-previousIcon"></button>
                    <h1 class="help-header-title" id="header-title">Help</h1>
                </div>
                <button aria-label="Close dialog" class="help-header-closeIcon help-closeIcon"></button>
                <button aria-label="fullscreen dialog" class="fullscreen"><i class="panel-control-icon glyphicon glyphicon-resize-full"></i></button>
            </div>
            <div class="help-header-searchRow">
                <div class="help-header-searchFormContainer">
                    <input type="text" autocomplete="off" placeholder="Search Help" class="help-header-searchBox" id="search-box" style="outline: medium none;">
                    <div class="help-header-searchIcon help-searchIcon helpv-loaded"></div>
                </div>
            </div>
        </div>
        <div id="content-container" class="help-content"  style="display: block;">
            <div id="content-view">
                <div id="helppages">
                    <h4 id="help-searchResults-title" class="help-card-title">Help Links</h4>
                    <ul class="help-card-list">
                    </ul>
                </div>
            </div>
            <div class="helploader"><div class="helpdocloader"></div></div>
        </div>
    </div>
<!-- Modal -->
<div class="modal fade" id="userprofile" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title" id="myModalLabel">User Profile</h4>
      </div>
      <div class="modal-body">
         <iframe id="profileframe" src="userprofile.php" onload="showloader()" height="100%" width="100%"></iframe>
         <div class="helploader"><div class="helpdocloader"></div></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
</section>
<script>
    var linkurl     = linkurl || "helplinks.php";
    var uiinsert    = uiinsert || "in";
    var isdrage     =  isdrage || false;
    var prsetting   = prsetting || "practiceload.php";
    var userprofile = userprofile || "userprofile.php";
    var gethelp_dialogcss = null;
    $(function(){
        function ajaxcall(url,data,type,callback,errorcallback){
            $.ajax({url:url,type:type,xhrFields: {withCredentials: true},data:data, crossDomain: true,error:errorcallback,success:callback});
        }
        var ajaxdefaulterrorcallback = function(xhr, ajaxOptions, thrownError){
                console.log(xhr.status);
                console.log(thrownError);
                console.log(xhr.responseText);
            };
        ajaxcall(prsetting,{uemail:'<?php echo $_SESSION['portal_useremail'];?>',type:'uemail'},"post",function(data){
                    var dt = $.parseJSON(data);
                    var dtarr = [];
                    var datahtml = "<option>Select Practice ID</option>";
                    console.log(data);
                    $.each(dt,function(index,value){
                        dtarr.push(value)
                        //console.log("indx: " +index + " : value: " + value)
                        optval = dtarr[0]['userid']+ "|" + dtarr[0]['uemail'] + "|" + dtarr[0]['username'] + "|" + dtarr[0]['practice'];
                        if($('#pracid').val()==dtarr[0]['practice']){
                            datahtml += "<option value="+optval+" selected>"+dtarr[0]['practice']+"</option>";
                        }   
                        else
                        {
                            datahtml += "<option value="+optval+">"+dtarr[0]['practice']+"</option>";
                        }
                        dtarr = [];

                    })
                     $("#practiceid").html(datahtml);

            },ajaxdefaulterrorcallback);
        $("#practiceid").change(function(event){
            var practice = $(this).val();
            var prac = practice.split("|");
            var pracUrl = '';
            ajaxcall(prsetting,{practiceid:prac[3],type:'practiceid'},"post",function(data){
                 pracUrl = data;
                 //window.open(pracUrl+'?uname='+prac[2]);
                 $('#changeUrl').attr('action',pracUrl+"/get_provider_info.php");
                 $('#unameid').val(prac[2]);
                 $('#passid').val("UmlzZTEyMyM=");
                 $('#changeUrl').submit();
                 
            },ajaxdefaulterrorcallback);
        });
        // show help popup
        $("#helpop").click(function(event){
            event.preventDefault();
            //$("#help_dialog").draggable({ handle:'#header'});
            $('#help_dialog').show();
            $('.help-header-searchRow').show();
            $('.help_dialog').find("#content-container").height(sethightofcontent(100,48,$('.help_dialog').find("#header").height()));
            $(".helploader").show();
            getlinks(linkurl);
        });
        
        // trigger when close help box
        $('.help-header-closeIcon').click(function(){
            $('#help_dialog').hide();
            $('.help_dialog').find("#content-container").height(0);
            $('#helpdocs').remove();
            $('.help-previousIcon').hide();
            $.fullscreen.exit();
            return false;
        });
        
        // trigger when click htlp link
        $(document).on("click",".help-iconTextComponent",function(evt){
           evt.preventDefault();
           if($(this).data('href').slice(0,7) == 'content'){
              var divid = "#"+$(this).data('href');
              $(divid).show();
              $('.help-card-list').html("");
              $('.help-header-searchRow').hide();
              $('#content-view').hide();
              $('.help_dialog').find("#content-container").height(sethightofcontent(100,48,$('.help_dialog').find("#header").height()));
           }
           else{
            $('<iframe src="" id="helpdocs" class="help-contentFrame" onload="showloader()" height="100%"></iframe>').insertAfter('#content-view')
            $('.help-card-list').html("");
            $('.help-header-searchRow').hide();
            $(".helploader").show();
            $('.help_dialog').find("#content-container").height(sethightofcontent(100,48,$('.help_dialog').find("#header").height()));
            $('#helpdocs').prop("src",$(this).data('href')).show();
           } 
           $('.help-previousIcon').show();
           
        });
        
        // this trigger when click prev button 
        $('.help-previousIcon').click(function(){
            $('.help-header-searchRow').show();
            $('#helpdocs').remove();
            $('.linkcontent').remove();
            $('#content-view').show();
            $(this).hide();
            $(".helploader").show();
            $('.help_dialog').find("#content-container").height(sethightofcontent(100,48,$('.help_dialog').find("#header").height()));
            getlinks(linkurl);
        });
        
        // fullscreen code
        
        // open in fullscreen
        $('#help_dialog .fullscreen').click(function() {
            // if we currently in fullscreen mode
            if(!$.fullscreen.isNativelySupported()){
                alert("This browser not support fullscreen mode.");
                return;
            }
            
            if($(this).children('i').hasClass('glyphicon-resize-full')){
                $('#help_dialog').fullscreen();
                return false;
            }else{
                $.fullscreen.exit();
                return false;
            }
        });
        
        $(document).bind('fscreenchange', function(e, state, elem) {            
            if ($.fullscreen.isFullScreen()) {
                $('#help_dialog .fullscreen > i').removeClass("glyphicon-resize-full");
                $('#help_dialog .fullscreen > i').addClass("glyphicon-resize-small");
                $('#help_dialog').attr("style");
                gethelp_dialogcss = $('#help_dialog').css({left:0, top:0, right:0});
                 $('#help_dialog').find('#content-container').css("height","100%");
            }else {
                $('#help_dialog .fullscreen > i').removeClass("glyphicon-resize-small");
                $('#help_dialog .fullscreen > i').addClass("glyphicon-resize-full");
                $('.help_dialog').find("#content-container").height(sethightofcontent(100,48,$('.help_dialog').find("#header").height()));
                $('#help_dialog').attr("style",gethelp_dialogcss);
            }
        });
        
        
        // this code for get conter form db
        function getlinks(linkurl){
            ajaxcall(linkurl,null,"post",function(data){
                var dataval = JSON.parse(data);
                var helpcards = ""
                var divcontent = "";
                var i = 1;
                var idhref = null;
                $.each(dataval,function(index,value){
                   if(value.content){
                       idhref = 'content'+i;
                       divcontent += '<div id="'+idhref+'" class="linkcontent" style="display:none">'+value.content+'</div>'; 
                       $(divcontent).insertAfter('#content-view');
                       i++;
                   }else{
                       idhref = value.helplink;
                   } 
                   helpcards +='<li class="help-card-listItem">\n\
                                    <a class="help-iconTextComponent" data-href="'+idhref+'">\n\
                                        <span class="help-iconTextComponent-icon help-articleIcon"></span>\n\
                                        <span class="help-iconTextComponent-label">'+value.title+'</span>\n';
                   if(value.edit == '1') helpcards +='<span class="help-iconTextComponent-edit" data-urllink="'+value.contexturl+'" data-userid="'+value.userid+'"\n\
                                                       data-practiceid="'+value.practiceid+'" data-machineid="'+value.machineid+'" data-articleid="'+value.articleid+'"  data-contenttype="'+value.contenttype+'">Edit</span>\n';
                   helpcards +='</a>\n\</li>';                     
                });

                $('.help-card-list').html(helpcards);
                $(".helploader").hide();
            },function(error){
                $(".helploader").hide();
            });
        }
        
        // this for search box code
        $("#search-box").on("keyup",function(){
            var inputval = $(this).val().trim();
            $('.help-iconTextComponent').hide();
            $('.help-iconTextComponent').filter(function(){
                var patt = new RegExp(inputval,"i");
                var res = patt.test($(this).find('.help-iconTextComponent-label').text());
                return res;
            }).show();

        });

        function sethightofcontent(topmargin,footermargin,penalheaderhight){                    
            var contentcontenarheight = (window.innerHeight - (topmargin + footermargin + penalheaderhight));
            return contentcontenarheight;
        }
        
        $('#userprofile').on('show.bs.modal', function(event){
            var target = $(event.relatedTarget);
            var modal = $(this);
            modal.find('.modal-header').show();
            document.getElementById('profileframe').contentWindow.activemenu();
        });
        
        $(document).on('click','.help-iconTextComponent-edit',function(event){
            event.stopPropagation();
            window.open($(this).data('urllink')+"admin.php?action=contextPracticeEdit&userid="+$(this).data('userid')+"&practiceid="+$(this).data('practiceid')+"&machineid="+$(this).data('machineid')+"&articleId="+$(this).data('articleid')+"&contenttype="+$(this).data('contenttype'));
        });
        $("#profileframe").attr("src",userprofile);
        
        
    });
  </script>
  <body>
      <?php
      $practiceId = '';
        $query = sqlStatement("SELECT title FROM list_options WHERE list_id= 'allcareConfig' AND option_id='practiceID'");
        while($row = sqlFetchArray($query)){
            $practiceId = $row['title'];
        }
        
      ?>
      <section style="padding-top:100px !important; min-height: 100%; margin-bottom: -40px;" class='container-fluid'>
          <form id="changeUrl" action="" name="changeUrl" method="POST">
              <input type="hidden" id="unameid" name="uname" value=""/>
              <input type="hidden" id="passid" name="pass" value=""/>
              <input type="hidden" id="pracid" name="pracid" value="<?php echo $practiceId; ?>"/>
          </form>
              