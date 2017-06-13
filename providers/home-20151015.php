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
if(isset($_SESSION['portal_username']) !=''){
   $provider=$_SESSION['portal_username'];
}else {
 echo  $provider=$_REQUEST['provider'];
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
?>

<!DOCTYPE html>
<html>

	<head>
            <meta charset="UTF-8">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <link rel="shortcut icon" href="img/season-change.jpg" type="image/x-icon">
            <title>HealthCare</title>		
	    <link href='http://fonts.googleapis.com/css?family=Abel' rel='stylesheet' type='text/css'>
	    <!-- <link href='http://fonts.googleapis.com/css?family=Pontano+Sans' rel='stylesheet' type='text/css'>
	    <link href='http://fonts.googleapis.com/css?family=Alegreya+Sans:300,400,500,700' rel='stylesheet' type='text/css'> -->
	    <link href='http://fonts.googleapis.com/css?family=Roboto:400,300,500' rel='stylesheet' type='text/css'>
	    <link href='http://fonts.googleapis.com/css?family=Dosis:300,400,500,600' rel='stylesheet' type='text/css'>   
            <link rel="stylesheet" type="text/css" href="assets/css/animate.css">
            <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">
            <link rel="stylesheet" type="text/css" href="assets/css/owl.carousel.css">
            <link rel="stylesheet" type="text/css" href="assets/css/owl.theme.css">
            <link rel="stylesheet" type="text/css" href="assets/css/owl.transitions.css">
            <link rel="stylesheet" type="text/css" href="assets/css/font-awesome.min.css">
            <link rel="stylesheet" type="text/css" href="assets/css/main.css">
            <link rel="stylesheet" type="text/css" href="assets/css/customize.css">
            <link href='http://fonts.googleapis.com/css?family=Roboto+Condensed:400,300' rel='stylesheet' type='text/css'>
            
            <!-- for calendar's-->
            <link rel="stylesheet" href="css/style-personal.css">
<!--            <link rel="stylesheet" href="http://netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">-->
            <script src="http://code.jquery.com/jquery-latest.min.js" type="text/javascript"></script>
            <script src="js/simplecalendar.js" type="text/javascript"></script>
            
            <script type="text/javascript" src="assets/js/jquery.min.js"></script>
            <link rel="stylesheet" href="fancybox/source/jquery.fancybox.css" type="text/css" media="screen" />
            <script type="text/javascript" src="fancybox/source/jquery.fancybox.pack.js"></script> 
            <script language="javascript"> 
               function DoPost(page_name, provider) {
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
                    document.body.appendChild(form);
                    form.submit();
                }
            </script>  
            <style>
                .navbar-nav > li > .dropdown-menu{
                    margin-top: 4px !important;
                }
            </style>
	</head>
        <body> <?php include 'header_nav.php'; ?>
		<section style="padding-top:100px !important" >
      		  <div class="container1">
   
    <div class="row">
     <div class= "col-lg-4 col-sm-6 col-xs-12">
        <div class="calendar hidden-print">
          <header style="width:400px!important">
            <h4 class="month"></h4>
            <a class="btn-prev fontawesome-angle-left" href="#"><i class="fa fa-angle-left"></i></a>
            <a class="btn-next fontawesome-angle-right" href="#"><i class="fa fa-angle-right"></i></a>
          </header>
          <table style="width:400px !important; margin-left:20px !important;">
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
      <div class= "col-lg-4 col-sm-6 col-xs-12">
           <label class="print pull-right">
            <span class="print-btn hidden-print">Print your list!</span>
           </label>
        <div class="list1"> 
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
                      
                      $sql12=sqlStatement("SELECT ope.*
                                FROM patient_data p INNER JOIN openemr_postcalendar_events ope ON p.pid=ope.pc_pid
                                inner join openemr_postcalendar_categories pc on pc.pc_catid=ope.pc_catid 
                                inner join list_options lo on lo.option_id=ope.pc_apptstatus
                                WHERE ope.pc_aid=$id  and p.practice_status = 'YES' AND p.deceased_stat != 'YES' AND (p.deceased_date = '' OR p.deceased_date = '0000-00-00 00:00:00' ) and ope.pc_catid IN ($enc_value) group by ope.pc_eventDate");
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
                                    if($starttimeh < 7 && $startampm='AM'){
                                        $allow='NO';
                                    }
                                    
                              
                               if($allow=='YES' && $date!=''){
                                 $dateSrc =  $date; 
                                 $dateTime1 = date_create( $dateSrc);
                                 $app=$dateTime1->format("Y_n_j");
                                 $app_date=explode("_","$app");
                                ?>
                                <div class="day-event"  date-day="<?php echo $app_date[2] ; ?>" date-month="<?php echo $app_date[1] ; ?>" date-year="<?php echo $app_date[0] ; ?>"  data-number="1">
                                <a href="#" class="close fontawesome-remove"></a>
                               <h2>Appointment Details</h2>
                               <p><?php $sql_pdata=sqlStatement("SELECT  ope.*,pc_startTime,p.fname,p.lname ,lo.title
                                         FROM patient_data p INNER JOIN openemr_postcalendar_events ope ON p.pid=ope.pc_pid
                                         inner join openemr_postcalendar_categories pc on pc.pc_catid=ope.pc_catid 
                                         inner join list_options lo on lo.option_id=ope.pc_apptstatus
                                         WHERE ope.pc_aid=$id  AND ope.pc_eventdate='$date' and p.practice_status = 'YES' AND p.deceased_stat != 'YES' AND (p.deceased_date = '' OR p.deceased_date = '0000-00-00 00:00:00' ) and ope.pc_catid IN ($enc_value)  ");    
                                $d1=explode("-",$date);
                                $d2=$d1[0].$d1[1].$d1[2];
                                while($row=sqlFetchArray($sql_pdata)){
                                    $eid=$row['pc_eid'];
                                    
                                     echo "<div style='border: 2px solid #cbd1d2;'>";
                                     echo "<b>Patient Name:</b>". $row['fname'].' '.$row['lname']; echo "<br>";
                                     echo "<b>Event Date:</b>".$date; echo "<br>";
                                     echo "<b>Category:</b>".$row['pc_title'];  echo "<br>";
                                     echo "<b>Status:</b>".$row['title'];  echo "<br>";
                                     echo "<a class='various hidden-print' data-fancybox-type='iframe' href='appointments/edit_appointment.php?date=$d2&eid=$eid&provider=$id'>More Details <i class='fa fa-caret-right'></i></a>";
				     echo "</div><br>";
                                  } echo "<br>"; ?></p>
                             </div>
                           <?php 
                           
                          }
                      
                      }     
                    ?>
            </div>
      </div>
        
    </div>
  </div>
                    
</section>
            <?php include 'footer.php'; ?>
            <script type="text/javascript" src="assets/js/owl.carousel.min.js"></script>
            <script type="text/javascript" src="assets/js/isotope.pkgd.min.js"></script>
            <script type="text/javascript" src="assets/js/wow.min.js"></script>
            <script type="text/javascript" src="assets/js/bootstrap.min.js"></script>
            <script>
                new WOW().init();
                $(document).ready(function() {
                    $("#starting-slider").owlCarousel({
                            autoPlay: 3000,
                    navigation : false, // Show next and prev buttons
                    slideSpeed : 700,
                    paginationSpeed : 1000,
                    singleItem:true
                    });
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

