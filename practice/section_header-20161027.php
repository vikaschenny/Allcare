<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1 maximum-scale=1, user-scalable=no">
    <?php 
    $base_url = "//".$_SERVER['SERVER_NAME'].'/practice/';
    
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
    <script type="text/javascript" src="//code.jquery.com/jquery-latest.min.js" ></script>
    <script type="text/javascript" src="<?php echo $base_url; ?>js/simplecalendar.js" ></script> 
    <script type="text/javascript" src="<?php echo $base_url; ?>assets/js/jquery.min.js"></script>
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
<section class= "navs">
    <nav class="navbar navbar-default navbar-fixed-top" role="navigation">
        <div style="background-color:#e7e7e7 !important; height:39px; padding-top:4px; padding-right:20px;"><div style="float:right !important;" ><?php echo $_SESSION['portal_userfullname']; echo "&nbsp;&nbsp;" ?><a  class="btn btn-default btn-sm" data-toggle="modal" data-target="#logout" style="color:black; "><span class="glyphicon glyphicon-lock"></span>  Logout</a> <a  class="btn btn-default btn-sm" id="helpop" style="color:black; "><span class="glyphicon glyphicon-question-sign"> </span>  Help</a></div></div>
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
                                                                $row_sub = sqlFetchArray($sql_sub); ?>
                                                                <li><a href="javascript:DoPost('<?php echo $base_url3."/practice/".$row_sub['notes']; ?>','<?php echo $portal_user;  ?>','<?php echo $_SESSION['refer'];  ?>')"><span><?php  echo $row_sub['title']; ?></span></a> 
                                                                </li>    
                                                                <?php 
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
    <div id="help_dialog" class="help_dialog" style="display: none;">
        <div id="header" class="help-header">
            <div class="help-header-row">
                <div class="lineheader">
                    <button class="help-header-previousIcon help-previousIcon"></button>
                    <h1 class="help-header-title" id="header-title">Help</h1>
                </div>
                <button aria-label="Close dialog" class="help-header-closeIcon help-closeIcon"></button>
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
    <div class="modal fade" id="logout" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header"><h4>Logout <i class="fa fa-lock"></i></h4></div>
                <div class="modal-body"><i class="fa fa-question-circle"></i> Are you sure you want to log-off?</div>
                <div class="modal-footer"><a href="<?php echo $base_url3; ?>/practice/logout_page.php?provider=<?php echo $provider ; ?>&refer=<?php echo $refer; ?>" class="btn btn-primary btn-block" >Logout</a><a href="javascript:void" class="btn btn-primary btn-block" data-dismiss="modal">Cancel</a></div>
            </div>
        </div>
    </div>
</section>
<script>
    var linkurl     = linkurl || "helplinks.php";
    var uiinsert    = uiinsert || "in";
    var isdrage     =  isdrage || true;
    $(function(){
        function ajaxcall(url,data,type,callback,errorcallback){
            $.ajax({url:url,type:type,xhrFields: {withCredentials: true},data:data, crossDomain: true,error:errorcallback,success:callback});
        }

        $("#helpop").click(function(event){
            //$("#help_dialog").draggable({ handle:'#header'});
            $('#help_dialog').show();
            $('.help-header-searchRow').show();
            $('.help_dialog').find("#content-container").height(sethightofcontent(100,48,$('.help_dialog').find("#header").height()));
            $(".helploader").show();
            ajaxcall(linkurl,null,"post",function(data){
                console.log(data);
                var $data = JSON.parse(data);
                var helpcards = "";
                $.each($data,function(index,value){
                   helpcards +='<li class="help-card-listItem">\n\
                                    <a class="help-iconTextComponent" data-href="'+value.helplink+'">\n\
                                        <span class="help-iconTextComponent-icon help-articleIcon"></span>\n\
                                        <span class="help-iconTextComponent-label">'+value.title+'</span>\n\
                                    </a>\n\
                                </li>';
                });

                $('.help-card-list').html(helpcards);

                $(".helploader").hide();
            },function(error){
                $(".helploader").hide();
            });
        });
        $('.help-header-closeIcon').click(function(){
            $('#help_dialog').hide();
            $('.help_dialog').find("#content-container").height(0);
            $('#helpdocs').remove();
            $('.help-previousIcon').hide();
        });
        $(document).on("click",".help-iconTextComponent",function(evt){
           evt.preventDefault();
           $('<iframe src="" id="helpdocs" class="help-contentFrame" onload="showloader()" height="100%"></iframe>').insertAfter('#content-view')
           $('.help-card-list').html("");
           $('.help-header-searchRow').hide();
           $(".helploader").show();
           $('.help_dialog').find("#content-container").height(sethightofcontent(100,48,$('.help_dialog').find("#header").height()));
           $('#helpdocs').prop("src",$(this).data('href')).show();
           $('.help-previousIcon').show();
        });
        $('.help-previousIcon').click(function(){
            $('.help-header-searchRow').show();
            $('#helpdocs').remove();
            $(this).hide();
            $(".helploader").show();
            $('.help_dialog').find("#content-container").height(sethightofcontent(100,48,$('.help_dialog').find("#header").height()));
             ajaxcall(linkurl,null,"post",function(data){
                var $data = JSON.parse(data);
                var helpcards = "";
                $.each($data,function(index,value){
                   helpcards +='<li class="help-card-listItem">\n\
                                    <a class="help-iconTextComponent" data-href="'+value.helplink+'">\n\
                                        <span class="help-iconTextComponent-icon help-articleIcon"></span>\n\
                                        <span class="help-iconTextComponent-label">'+value.title+'</span>\n\
                                    </a>\n\
                                </li>';
                });

                $('.help-card-list').html(helpcards);

                $(".helploader").hide();
            },function(error){
                $(".helploader").hide();
            });
        });

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
        function reposition() {
            var modal = $(this),
                dialog = modal.find('.modal-dialog');
                modal.css('display', 'block');

            // Dividing by two centers the modal exactly, but dividing by three 
            // or four works better for larger screens.
            /*alert($(window).height() + " " + window.innerHeight + " " + self.innerHeight + " " + parent.innerHeight + " " + top.innerHeight);*/
            dialog.css("margin-top", Math.max(0, (window.innerHeight - dialog.height()) / 2));
        }
        // Reposition when a modal is shown
        $('#logout').on('show.bs.modal', reposition);
        // Reposition when the window is resized
        $(window).on('resize', function() {
            $('#logout:visible').each(reposition);
        });

    });
  </script>
  <body>
      <section style="padding-top:100px !important; min-height: 100%; margin-bottom: -45px;" class='container-fluid'>