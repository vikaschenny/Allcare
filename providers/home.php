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


// $ignoreAuth=true; // ignore the standard authentication for a regular OpenEMR user
//include_once('../interface/globals.php');
$pagename = "home"; 
if(isset($_SESSION['portal_username']) !=''){
$provider=$_SESSION['portal_username'];
$refer=$_SESSION['refer']; 
}else {
 $provider=$_REQUEST['provider'];
 $refer=$_REQUEST['refer'];  
 $_SESSION['portal_username']=$_REQUEST['provider'];
 $_SESSION['refer']=$_REQUEST['refer'];
}


$sql=sqlStatement("SELECT id, fname, lname, specialty FROM users " .
      "WHERE active = 1 AND ( info IS NULL OR info NOT LIKE '%Inactive%' ) " .
      "AND authorized = 1 AND username='".$provider."'" .
      "ORDER BY lname, fname");
$id=sqlFetchArray($sql);
$id1=$id['id'];

if(empty($id)){
    $_SESSION['providerloginfailure']=1;
    header('Location: ../providers/index.php?site=default');
}

//for messages
$showall=$_REQUEST['show_all'];
$form_active=$_REQUEST['form_active'];
$form_inactive=$_REQUEST['form_inactive'];
//for new messsages
$show_all=$_REQUEST['showall'];
$sortby=$_REQUEST['sortby'];
$sortorder=$_REQUEST['sortorder'];
$begin=$_REQUEST['begin'];
$task=$_REQUEST['task'];
$noteid=$_REQUEST['noteid'];

//for add
 $note = $_REQUEST['note'];
 $noteid = $_REQUEST['noteid'];
 $form_note_type = str_replace(" ","_",$_REQUEST['form_note_type']);
 $form_message_status = $_REQUEST['form_message_status'];
 $reply_to = $_REQUEST['reply_to'];
 $assigned_to_list = $_REQUEST['assigned_to'];

//for delete
 $delete_id = $_REQUEST['delete_id'];

 
?>

<!DOCTYPE html>
<html>

	<head>
            <meta charset="UTF-8">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <link rel="shortcut icon" href="img/season-change.jpg" type="image/x-icon">
             <?php $sql=sqlStatement("select * from globals where gl_name='openemr_name'");
              $row1=sqlFetchArray($sql);?>
            <title><?php echo $row1['gl_value']; ?></title>		
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
            <link href='//fonts.googleapis.com/css?family=Roboto+Condensed:400,300' rel='stylesheet' type='text/css'>
            <!-- for calendar's-->
            <link rel="stylesheet" href="css/style-personal.css">
            <link rel="stylesheet" type="text/css" href="assets/css/customize.css">
            <script src="//code.jquery.com/jquery-latest.min.js" type="text/javascript"></script>
            <script src="js/simplecalendar.js" type="text/javascript"></script> 
            <script type="text/javascript" src="assets/js/jquery.min.js"></script>
            <link rel="stylesheet" href="fancybox/source/jquery.fancybox.css" type="text/css" media="screen" />
            <script type="text/javascript" src="fancybox/source/jquery.fancybox.pack.js"></script> 
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
            <style>
                .navbar-nav > li > .dropdown-menu{
                    margin-top: 4px !important;
                }
                @media print {
                    .appointment {
                         border: 1px solid black !important ; padding: 5px !important; width: 300px !important;
                         margin:5px !important;
                     }
                }
                .print-btn {
                    padding: 3px 14px;
                }
                #homecal td{
                    width: 14.27%;
                }
                .btn-next {
                    right: 25px;
                }
                .day-event1{
                    width: auto;
                    margin-bottom:0px;
                }
                .print {
                    margin-top: 13px;
                }
                .calendar tbody td:hover{
                  box-sizing: border-box;  
                }
                .day-event1,.day-event{
                    min-height: 300px;
                }
                #oppinments{
                    background-color: #f2f2f2;
                    min-height: 300px;
                }
                .day-event {
                    background-color: #f2f2f2;
                    display: none;
                    margin-bottom: 50px;
                    padding: 5px 10px 0;
                    width: auto !important;
                }
                .day-event h2 {
                    margin-top: 0;
                }
                #print1 {
                    margin-top: 12px;
                }
                .noappointments{
                    display: none;
                }
         </style>
	</head>
        
        <body > <?php include 'header_nav.php'; ?>
		<section style="padding-top:100px !important" class='container'>
                    <div class="row">
                     <div class= "col-lg-4 col-sm-5 col-xs-12 text-center">
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
                      </div>
                     
                    <div id="oppinments" class= "col-lg-8 col-sm-7 col-xs-12 ">
                        <div align='right' id='print1' ><label>
                          <button type="button" class="print-btn hidden-print btn btn-info btn-md"><span class='glyphicon glyphicon-print' aria-hidden='true'></span> Print</button>
                          </label>
                        </div>
                        <div class='noappointments' style='padding-top:60px; background-color: #F2F2F2; height:297px;'>
                            <center><h2>No Appointments</h2></center>
                        </div>
                        <?php 
                            $sql=sqlStatement("select id from users where username='".$provider."'");
                              $row=sqlFetchArray($sql);
                              $id=$row['id'];
                                 $sql_apppatients3=sqlStatement("SELECT visit_categories FROM tbl_allcare_facuservisit WHERE  `users`  REGEXP ('".":\"".$id."\"')");
                                  while($row_apppatients3=sqlFetchArray($sql_apppatients3)){
                                       $array[]=unserialize($row_apppatients3['visit_categories']);
                                  }
                                  $dataArray = array();
                                    for($j = 0; $j<count($array); $j++){
                                        foreach($array[$j] as $arraykey){
                                             $dataArray[] = $arraykey;
                                        }
                                    }
                                    $enc_val = '';
                                    $dataarray = array_unique($dataArray);
                                    foreach($dataarray as $arrayval){
                                        $enc_val .= $arrayval.",";
                                    }
                                    $enc_value = rtrim($enc_val,",");
                                     $sql12='';
                                     $today1 = date("Y-m-d");
                                     $today_event1=explode("-","$today");
                                  if($enc_value!=''){
                                       $sql13=sqlStatement("SELECT ope.pc_eid
                                            FROM patient_data p INNER JOIN openemr_postcalendar_events ope ON p.pid=ope.pc_pid
                                            inner join openemr_postcalendar_categories pc on pc.pc_catid=ope.pc_catid 
                                            inner join list_options lo on lo.option_id=ope.pc_apptstatus
                                            WHERE ope.pc_aid=$id  and p.practice_status = 'YES' AND ope.pc_eventDate='$today1' AND p.deceased_stat != 'YES' AND (p.deceased_date = '' OR p.deceased_date = '0000-00-00 00:00:00' ) and ope.pc_catid IN ($enc_value) group by ope.pc_eventDate");   
                                  $row_app31=sqlFetchArray($sql13);
                                  if(empty($row_app31)){
                                    echo " <div class='day-event1 row row-eq-height' id='app'  date-day=$today_event1[2] date-month=$today_event1[1] date-year=$today_event1[0] data-number='1' style='padding-top:60px; background-color: #F2F2F2; height:250px; display:block;'>";
                                    echo "<center><h2>No Appointments</h2></center>";
                                    echo "</div>";
                                    $no_app=1;
                                  }
                                  
                                  $sql12=sqlStatement("SELECT ope.pc_eventDate,ope.pc_startTime
                                            FROM patient_data p INNER JOIN openemr_postcalendar_events ope ON p.pid=ope.pc_pid
                                            inner join openemr_postcalendar_categories pc on pc.pc_catid=ope.pc_catid 
                                            inner join list_options lo on lo.option_id=ope.pc_apptstatus
                                            WHERE ope.pc_aid=$id  and p.practice_status = 'YES' AND p.deceased_stat != 'YES' AND (p.deceased_date = '' OR p.deceased_date = '0000-00-00 00:00:00' ) and ope.pc_catid IN ($enc_value) group by ope.pc_eventDate");
                                  $lab_cnt=0;
                                   while($row_app3=sqlFetchArray($sql12)){
                                            $date=$row_app3['pc_eventDate'];
                                            $time=$row_app3['pc_startTime'];
                                               $starttimeh = substr($time, 0, 2) + 0;
                                               $starttimem = substr($time, 3, 2);
                                               $startampm = 'AM';
                                                if ($starttimeh >= 12) { // p.m. starts at noon and not 12:01
                                                  $startampm = 'PM';
                                                 if ($starttimeh > 12) $starttimeh -= 12;
                                                }
                                                $allow='YES';
//                                                if($starttimeh < 7 && $startampm='AM'){
//                                                    $allow='NO';
//                                                }


                                           if($allow=='YES' && $date!=''){
                                             $dateSrc =  $date; 
                                             $dateTime1 = date_create( $dateSrc);
                                             $app=$dateTime1->format("Y_n_j");
                                             $app_date=explode("_","$app");
                                             $today = date("Y-m-d");
                                             $today_event=explode("-","$today");
                                             ?>
                                             <div class="day-event row row-eq-height"  date-day="<?php echo $app_date[2] ; ?>" date-month="<?php echo $app_date[1] ; ?>" date-year="<?php echo $app_date[0] ; ?>"  data-number="1"></div>
                                       <?php 
                                   }else if($allow=='NO' && $date==date("Y-m-d") && $no_app!=1){
                                       if($lab_cnt==0){ 
                                        echo "<div class='day-event1 row row-eq-height' id='app'  date-day=$app_date[2]  date-month=$app_date[1] date-year=$app_date[0] data-number='1' style='padding-top:60px; background-color: #F2F2F2; height:297px; display:block;'>";
                                        echo "<center><h2>No Appointments</h2></center>";
                                        echo "</div>";
                                        $lab_cnt++;
                                       }
                                   }
                                  }
                                  }
                                ?>
                  </div>
            </div>
<!--      for messages              -->
    <div id='div1' class="hidden-print"></div><br>
    <p class="hidden-print"><b>Reminders</b></p>
    <div id='div2' class="hidden-print"></div>

</section>
               
            <?php include 'footer.php'; ?>
            <script type="text/javascript" src="assets/js/isotope.pkgd.min.js"></script>
            <script type="text/javascript" src="assets/js/wow.min.js"></script>
            <script type="text/javascript" src="assets/js/bootstrap.min.js"></script>
            
            <script>
                new WOW().init();
                $(document).ready(function() {
                    displayEvent1();
                    if($(".current-day").hasClass('event')){
                        ajaxcall($(".current-day"),<?php echo $id; ?>,'<?php echo $enc_value; ?>');
                    }
                    function ajaxcall(targetobject,pid,encid){
                        var year = targetobject.attr('date-year');
                        var month = targetobject.attr('date-month');
                        var day = targetobject.attr('date-day');
                        var eventdate = (year+"-"+month + "-" + day)
                        $.ajax({
                            url:"appointment_details.php",
                            method:"POST",
                            data:{evtdate:eventdate,patientid:pid,encounterid:encid},
                            success:function(result){
                               $('.day-event[date-year="' + year + '"][date-month="' + month + '"][date-day="' + day + '"]').html(result);
                               $('.loader').hide();
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
                             $('#print1').show();
                             ajaxcall($(this),<?php echo $id; ?>,'<?php echo $enc_value; ?>');
                        }else{
                            $('.noappointments').show();
                            $('#print1').hide();
                        }
                    });
                    
                    var showall='<?php echo $showall ?>';
                    var form_active='<?php echo $form_active ?>';
                    var form_inactive='<?php echo $form_inactive ?>';
                    var show_all='<?php echo $show_all ?>';
                    var sortby='<?php echo $sortby ?>';
                    var sortorder='<?php echo $sortorder ?>';
                    var begin='<?php echo $begin ?>';
                    var task='<?php echo $task ?>';
                    var noteid='<?php echo $noteid; ?>';
                   
                    if(showall!='' && task==''){
                       $("#div1").load("messages/messages.php?show_all=showall&form_active=form_active&form_inactive=form_inactive&provider=<?php echo $provider; ?>&refer=<?php echo $refer; ?>");
                    }else if(task!='' && noteid=='' && task!='add' ){
                         $("#div1").load("messages/messages.php?showall=show_all&sortby=sortby&sortorder=sortorder&begin=begin&task=<?php echo $task ?>&show_all=showall&form_active=form_active&form_inactive=form_inactive&provider=<?php echo $provider; ?>&refer=<?php echo $refer; ?>");
                    }else if(task!=''){
                        if(task=='add'){
                            $("#div1").load("messages/messages.php?showall=show_all&task=<?php echo $task ?>&show_all=showall&form_active=form_active&form_inactive=form_inactive&noteid=<?php echo $noteid ; ?>&note='<?php echo $note;  ?>'&form_note_type=<?php echo $form_note_type; ?>&form_message_status=<?php echo $form_message_status; ?>&reply_to=<?php echo $reply_to; ?>&assigned_to=<?php echo $assigned_to_list; ?>&provider=<?php echo $provider; ?>&refer=<?php echo $refer; ?>");
                        }else{
                            $("#div1").load("messages/messages.php?showall=show_all&sortby=sortby&sortorder=sortorder&begin=begin&task=<?php echo $task ?>&show_all=showall&form_active=form_active&form_inactive=form_inactive&noteid=<?php echo $noteid ; ?>&provider=<?php echo $provider; ?>&refer=<?php echo $refer; ?>");
                        } 
                    }else if(form_active!='' && task==''){
                        $("#div1").load("messages/messages.php?form_active=form_active&provider=<?php echo $provider; ?>&refer=<?php echo $refer; ?>");
                    }else if(form_inactive!='' && task==''){
                         $("#div1").load("messages/messages.php?form_inactive=form_inactive&provider=<?php echo $provider; ?>&refer=<?php echo $refer; ?>");
                    }else{
                        $("#div1").load("messages/messages.php?form_active=1&provider=<?php echo $provider; ?>&refer=<?php echo $refer; ?>");
                    }                    
                     $("#div2").load("dated_reminders/dated_reminders.php?provider=<?php echo $provider; ?>&refer=<?php echo $refer; ?>");
                });

                var $container = $('.isotope').isotope
                ({
                    itemSelector: '.element-item',
                    layoutMode: 'fitRows'
                });
                // bind filter button click
                $('#filters').on( 'click', 'button', function() 
                {
                    var filterValue = $( this ).attr('data-filter');
                    // use filterFn if matches value
                    $container.isotope({ filter: filterValue });
                 });
            // change is-checked class on buttons
                $('.button-group').each( function( i, buttonGroup ) 
                {
                    var $buttonGroup = $( buttonGroup );
                    $buttonGroup.on( 'click', 'button', function() 
                    {
                        $buttonGroup.find('.is-checked').removeClass('is-checked');
                        $( this ).addClass('is-checked');
                    });
                });
                 $(".fancybox").fancybox({
                    openEffect  : 'none',
                    closeEffect : 'none',
                    iframe : {
                            preload: false
                    }
                });
                $(".various").fancybox({
                        maxWidth	: 800,
                        maxHeight	: 600,
                        fitToView	: false,
                        width		: '70%',
                        height		: '70%',
                        autoSize	: false,
                        closeClick	: false,
                        openEffect	: 'none',
                        closeEffect	: 'none'

                });
                $('.fancybox-media').fancybox({
                        openEffect  : 'none',
                        closeEffect : 'none',
                        helpers : {
                           media : {}
                        }
                });                      
            </script>
            
	</body>
<script type="text/javascript">
    var _gaq = _gaq || [];
    _gaq.push(['_setAccount', 'UA-36251023-1']);
    _gaq.push(['_setDomainName', 'jqueryscript.net']);
    _gaq.push(['_trackPageview']);
    (function() {
      var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
      ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
      var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
    })();
</script>
</html>

