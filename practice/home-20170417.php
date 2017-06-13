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

$base_url="//".$_SERVER['SERVER_NAME'].dirname($_SERVER["REQUEST_URI"].'?').'/';
include 'section_header.php';
?>
<style>
    #hbtngroup {
        font-size: 15px;
        text-align: left;
    }
    button.accordion {
    background-color: #eee;
    color: #444;
    cursor: pointer;
    width: 100%;
    border: none;
    text-align: left;
    outline: none;
    transition: 0.4s;
    }

    button.accordion.active, button.accordion:hover {
        background-color: #ddd; 
    }

    div.panel {
        padding: 0 18px;
        display: none;
        background-color: white;
    }
    span.eventdate{padding-right: 10px;}
    span.pname{padding-right: 50px;}
    
</style>
    <div class="row" style="font-size: 75%;">
     <div class= "hidden-print col-lg-3 col-sm-4 col-xs-12 text-center">
         <div class="text-left hidden-print">
             <h5>Providers</h5>
                <?php
                $providers_string = array();

                if(strtolower(trim($_SESSION['see_all_providers'])) == 'yes' && $_SESSION['isprovider'] == 1 ){
                    $get_providers_list = sqlStatement("SELECT id,CONCAT (fname, lname, mname) as providername,username FROM users WHERE authorized != 0 AND active = 1 AND username <> '' ORDER BY fname,lname");
                    while($set_providers_list = sqlFetchArray($get_providers_list)){
                        $providername[ $set_list_providername['id']] = $set_list_providername['providername'];
                        $providernames[ $set_list_providername['id']] = $set_list_providername['username'];
                    }
                }else if(strtolower(trim($_SESSION['see_all_providers'])) != 'yes' && $_SESSION['isprovider'] == 1 ){
                    $providername[$_SESSION['portal_userid']] = $_SESSION['portal_userfullname'];
                    $providernames[$_SESSION['portal_userid']] = $_SESSION['portal_username'];
                }else{
                    $get_providers_list = sqlStatement("SELECT pro_refers FROM tbl_user_custom_attr_1to1 WHERE userid = '".$_SESSION['portal_userid']."' LIMIT 0,1");
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
                }
                
                
                $countOf = count($providernames);
                if($countOf > 3)
                    $countOf = 5;
                
                /*
                 * af_providers for provider filter
                 * af_visittype for visit type filter 
                 */
                
                $af_visittype = '';
                $af_providers = '';
                
                $getdatafilter = sqlStatement("SELECT af_providers,af_visittype FROM tbl_providerportal_filters WHERE userid = '".$_SESSION['portal_userid']."' and screen_name = 'home'");
                while($setdatafilter = sqlFetchArray($getdatafilter)){
                    $af_visittype = $setdatafilter['af_visittype'];
                    $af_providers = $setdatafilter['af_providers'];
                }

                $af_visittype2 = explode(",",$af_visittype);
                $af_providers2 = explode(",",$af_providers);

               ?>
                <select id="providersmenu" name="providersmenu" class="form-control" title="Select Provider to get appointments" multiple="multiple" size="<?php echo $countOf; ?>">     
                    <?php 
                    if(!empty($providername)){
                        foreach($providername as $provider_idsub => $providername_sub){
                            echo "<option value ='$provider_idsub' ";
                            if( in_array ( $provider_idsub, $af_providers2) == true )
                                echo " selected ";
                            echo "> $providername_sub </option>";  
                        }
                    }
                    $set_visitTypes = array();
                    $get_visittype = sqlStatement("SELECT option_id,title FROM list_options WHERE list_id='AllcareVisitTypes' ORDER BY title");
                    while($set_visittype = sqlFetchArray($get_visittype)){
                        $set_visitTypes[$set_visittype['option_id']] = $set_visittype['title'];
                    }

                    $countOfVisit = count($set_visitTypes);
                    if($countOfVisit > 3)
                       $countOfVisit = 5;
                       //echo "<option value = '".$set_visittype['option_id']."'> ".$set_visittype['title']."</option>";
                    ?>
                </select> <br> 
         </div>
         <div class="text-left hidden-print">
             <h5>Visit Types</h5>
             <select id="pc_visittype" class="form-control" multiple="multiple" name="pc_visittype" size="<?php echo $countOfVisit; ?>" >
                <?php
                   foreach($set_visitTypes as $vkey => $vvalue)
                      echo "<option value = '$vkey' selected> $vvalue</option>";

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
         <div class="row">
             <div class="col-xs-12">
                 <a class="btn btn-primary btn-block" href="#" role="button" href="javascript:void()" data-modalsize='modal-lg' data-title="Practice Schedules" data-frameheight="450" data-bodypadding='0' data-href='calendar/index.php?module=PostCalendar&viewtype=day&func=view&pc_username=<?php $refer; ?>&framewidth=1212' data-toggle='modal' data-target='#modalwindow' title="Click to see calendar schedule" data-backdrop="static" data-keyboard="false">Practice Schedules</a>
                 <a class="btn btn-primary btn-block" href="javascript:void()" data-title="Search Patient Appointments" data-frameheight="450" data-modalsize='modal-lg' data-bodypadding='0' data-frameheight="315" data-href='calendar/patientappointments/view_patient_appointments_page.php' data-toggle='modal' data-target='#modalwindow' title="Click to search patient appointments" data-backdrop="static" data-keyboard="false">Search Patient Appointments</a>
                 <a class="btn btn-primary btn-block" href="javascript:void()" data-title="Maintain Provider Schedule" data-frameheight="450" data-modalsize='modal-lg' data-bodypadding='0' data-frameheight="315" data-href='calendar/providerschedules/view_provider_schedule_page.php' data-toggle='modal' data-target='#modalwindow' title="Click to search provider calendar schedule" data-backdrop="static" data-keyboard="false">Maintain Provider Schedule</a>
             </div>
         </div>
         
         <div class="well well-lg text-left hidden-print" style="margin-top:8px;">
             <div class="row">
                <?php
                $get_calendar = sqlStatement("SELECT pe.pc_eventDate, CONCAT(pd.fname, ' ', pd.lname) as pname,pd.phone_home,pe.pc_title,pe.pc_hometext,CONCAT(u.fname,' ', u.lname) as uname FROM openemr_postcalendar_events pe INNER JOIN patient_data pd ON pe.pc_pid = pd.pid
                                              INNER JOIN users u ON u.id=pe.pc_aid WHERE pc_eventDate > NOW() LIMIT 10");
                while($set_calendar = sqlFetchArray($get_calendar)){
                    echo "<button class='accordion'><span class='eventdate'>".$set_calendar['pc_eventDate']."</span><span class='pname'> " . $set_calendar['pname'] . "</span><span class='phome'>" . $set_calendar['phone_home'] ."</span></button>";
                    echo "<div class='panel'><span><b>Visit Category: </b> ".$set_calendar['pc_title']."</span><br />
                                            <span><b>Provider: </b>".$set_calendar['uname']."</span>
                          </div>";
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
                    <?php
                    for($i = 1; $i <= 16; $i++ ){
                        echo "<div class='bar-$i'></div>";
                    }
                    ?>
                </div>
                <div id="loadertitle">Appointments Loading...</div>
            </div>
            <div style="width:900px;border:1px solid #000;clear:both; padding:0px 20px;" class="headerstyle">
                <table>
                    <tbody>
                        <?php
                            $provider_credentials = '';
                            $providerid           = ''; 
                            $get_provider_id = sqlStatement("SELECT id FROM users WHERE username = '".$_SESSION['portal_userid']."'");
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
    <?php include 'section_footer.php'; ?>
    
    <script>
        $(document).ready(function() {
            calendar.init();
            $("#help_dialog").draggable({ handle:'#header'});
            var numberOfOptions = $('#providersmenu option').length;
            if(numberOfOptions > 1){
                var selectedprovider = [];
                $('#providersmenu :selected').each(function(i, selected){
                    selectedprovider[i] = $(selected).val();
                });
            }else{
                 var selectedprovider = $('#providersmenu :selected').val();
            }
            var selectedvisitType = $('#pc_visittype').val();
            var providerstates = "loading";
            get_calendar_icons(selectedprovider,selectedvisitType);
            $("#providersmenu,#pc_visittype").change(function(){
                $('.noappointments1').show();
                var enc_value = $('#hiddenenc_value').val();
                //alert(enc_value);
                var numberOfOptions = $('#providersmenu option').length;
                if(numberOfOptions > 1){
                    var selectedids = [];
                    $('#providersmenu :selected').each(function(i, selected){
                        selectedids[i] = $(selected).val();
                    });
                }else{
                    var selectedids = $('#providersmenu :selected').val();
                }
                var selectedvisitType = $('#pc_visittype').val();
                get_calendar_icons(selectedids,selectedvisitType);
                ajaxcall($("tbody.event-calendar .active"),<?php echo $_SESSION['portal_userid']; ?>,enc_value,selectedvisitType);
            });      


            function ajaxcall(targetobject,pid,encid,visitType){
                var year = targetobject.attr('date-year');
                var month = targetobject.attr('date-month');
                var day = targetobject.attr('date-day');

                var numberOfOptions = $('#providersmenu option').length;
                if(numberOfOptions > 1){
                    var selectedids = [];
                    $('#providersmenu :selected').each(function(i, selected){
                        selectedids[i] = $(selected).val();
                    });
                }else{
                    var selectedids = $('#providersmenu :selected').val();
                }

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
                            $('.noappointments').hide();
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
                     ajaxcall($(this),<?php echo $_SESSION['portal_userid']; ?>,enc_value);
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
               // calendar.init();
                $('#ourevent').html("");
                $('#loader').show();
                providerstates = "loading";
//                alert(selectedprovider);
                $.ajax({
                    url:"get_calendar_appointments.php",
                    method:"POST",
                    data:{
                        selectedprovider:selectedprovider,
                        visitType: visitType
                    },
                    
                    success:function(result){
                       providerstates = "loaded";
                       $('#ourevent').html(result);
                       $('#loader').hide();
                       //calendar.init();
                       //console.log("call")
                       displayEvent1();
                       $('.day-event').each(function(i) {
                            //console.log("call")
                            var eventMonth = $(this).attr('date-month');
                            var eventDay = $(this).attr('date-day');
                            var eventYear = $(this).attr('date-year');
                            var eventClass = $(this).attr('event-class');
                            if (eventClass === undefined) eventClass = 'event';
                            else eventClass = 'event ' + eventClass;
                            var yearNumber = (new Date).getFullYear();
                            if (parseInt(eventYear) === yearNumber) {
                              $('tbody.event-calendar tr td[date-year="' + eventYear + '"][date-month="' + eventMonth + '"][date-day="' + eventDay + '"]').addClass(eventClass);
                            }
                        });
      
                       
                       $('.noappointments1').hide();
                       if($(".current-day").hasClass('event')){
                            var enc_value = $('#hiddenenc_value').val();
                            ajaxcall($(".current-day"),<?php echo $_SESSION['portal_userid']; ?>,enc_value,visitType);
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
        
        var acc = document.getElementsByClassName("accordion");
        var i;

        for (i = 0; i < acc.length; i++) {
            acc[i].onclick = function(){
                this.classList.toggle("active");
                var panel = this.nextElementSibling;
                if (panel.style.display === "block") {
                    panel.style.display = "none";
                } else {
                    panel.style.display = "block";
                }
            }
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

