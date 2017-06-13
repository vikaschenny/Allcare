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


$pagename = "medrec"; 
if(isset($_SESSION['portal_username']) !=''){
   $provider=$_SESSION['portal_username'];
}else {
  $provider=$_REQUEST['provider'];
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

include_once("chartoutput/chartoutput_lib.php");
$patient=$_REQUEST['form_patient'];
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
            <link rel="stylesheet" href="css/dataTables.bootstrap.css"/>
            <link rel="stylesheet" href="css/datatables.responsive_bootstrap.css"/>
            <script src="js/responsive_datatable/jquery.min.js"></script>
            <script src="js/responsive_datatable/jquery.dataTables.min.js"></script>
            <script src="js/responsive_datatable/dataTables.bootstrap.js"></script>
            <script src="js/responsive_datatable/datatables.responsive.js"></script>
            <link rel="stylesheet" href="fancybox/source/jquery.fancybox.css" type="text/css" media="screen" />
            <script type="text/javascript" src="fancybox/source/jquery.fancybox.pack.js"></script> 
            <style>
                @media screen and (max-width: 767px) {
                    main#content {
                      margin-top: 65px;
                      transition: all ease-out 0.3s;
                    }

                }
                /* drag and drop               */
                ul#slippylist{
                    width:120px;
                    height:80px;
                    padding-left: 0px !important;
                }
                ul#slippylist li {
                    user-select: none;
                    -webkit-user-select: none;
                /*    border 1px solid lightgrey;*/
                    list-style: none;
                /*    height: 25px;*/
                /*    max-width: 200px;*/
                    cursor: move;
                    margin-top: -1px;
                    margin-bottom: 0;
                    padding-right:50px;
                    padding-left:7px;
                    font-weight: bold;
                    color: black;
                    text-align:left;
                }
                    ul#slippylist li.slip-reordering {
                        box-shadow: 0 2px 10px rgba(0,0,0,0.45);
                    }


                    //buttons
                    .css_button1 {
                    background: transparent url('../interface/themes/images/bg_button_a.gif') no-repeat scroll top right !important;
                    color: #444 !important;
                    display: block !important;
                    float: left !important;
                    font: bold 10px arial, sans-serif !important;
                    height: 24px !important;
                    margin-right: 3px !important;
                    padding-right: 10px !important;
                    text-decoration: none !important;
                    }

                    .welcome-btn1 {
                    background-color:#49C1DC;
                    margin-top: 20px;
                    color: #fff;
                    border-radius:20px;
                    font: bold 10px arial, sans-serif;
                    transition: all 0.3s ease-in;
                    padding: 8px 10px;
                    border: 2px solid #fff;
                }
                //table for f2f
                .showborder {
                width:650px;
                }
                .showborder td {
                border-bottom:1px dashed #000000;
                text-align:left;
                //font-size:9pt;
                height:40px;

                }
                .showborder_head  th {
                border-bottom:1px solid #000000;
                text-align:left;
                //font-size:9pt;
                }
                .shownoborder td {
                text-align:left;
                //font-size:9pt;
                height:40px;
                }
                .showborder_long {
                width:100%;
                }
                .showborder_long tr td{
                border-bottom:1px dashed #000000;
                text-align:left;
                height:40px;
                //font-size:9pt;
                }
                #dvLoading1 {
                    background: url(../interface/pic/ajax-loader-large.gif) no-repeat center center;
                    height: 100px;
                    width: 500px;
                    position: fixed;
                    z-index: 1000;
                    left: 0%;
                    top: 50%;
                    margin: -25px 0 0 -25px;
                }
                .navbar-nav > li > .dropdown-menu{
                    margin-top: 4px !important;
                }

            </style>

 <script type='text/javascript'>
     function divclick(cb, divid) {
             var divstyle = document.getElementById(divid).style;
             if (cb.checked) {
              divstyle.display = 'block';
             } else {
              divstyle.display = 'none';
             }
             return true;
    }
    function toggle( target, div ) {
        $mode = $(target).find(".indicator").text();
        if ( $mode == "collapse" ) {
            $(target).find(".indicator").text( "expand" );
            $(div).hide();
        } else {
            $(target).find(".indicator").text( "collapse" );
            $(div).show();
        }
    }
    $(document).ready(function() {
          $("#chart_view").click( function() {
            toggle( $(this), "#chartoutput_div" );
        });

    });
    
function DoPost(page_name, provider) {
                method = "post"; // Set method to post by default if not specified.

              // alert(provider);

                var form = document.createElement("form");
                form.setAttribute("method", method);
                form.setAttribute("action", page_name);
                var key='provider';
//                for(var key in params) {
//                    if(params.hasOwnProperty(key)) {
                        var hiddenField = document.createElement("input");
                        hiddenField.setAttribute("type", "hidden");
                        hiddenField.setAttribute("name", key);
                        hiddenField.setAttribute("value", provider);

                        form.appendChild(hiddenField);
//                     }
                //}

               document.body.appendChild(form);
                form.submit();
        }
        
function win1(url){
     // alert(url);
    window.open(url,'popup','width=900,height=900,scrollbars=no,resizable=yes');
}    

</script>
                 
	</head>

	<body><?php include 'header_nav.php'; ?>
             <section id= "services">
                <div class= "container">
				<div class= "row">
					<div class= "col-lg-12 col-sm-12 col-xs-12" style='padding-top:100px !important;'>
						
                                           <form name="userid_dropdown"  action="" method="POST">
                                                <table>
                                                    <tr><td> <span class='bold'><?php echo htmlspecialchars( xl('Patient Name'), ENT_NOQUOTES); ?>:</span>
                                                            <?php
                                                                 // Build a drop-down list of providers.
                     
                                                                $query="SELECT fe.pid, lname, fname from form_encounter fe  INNER JOIN patient_data p ON p.pid=fe.pid  INNER JOIN openemr_postcalendar_events o ON o.pc_pid = fe.pid "
                                                                                  . " AND o.pc_catid = fe.pc_catid AND fe.facility_id = o.pc_facility
                                                                                      AND o.pc_eventDate = DATE_FORMAT( fe.date,  '%Y-%m-%d' )where o.pc_aid ='".$id['id']."' group by fe.pid  ORDER BY lname, fname ";
                                                                $ures = sqlStatement($query);

                                                                echo "   <select name='form_patient'   id='form_patient' onchange='javascript:dropdownchange();'>\n";
//                                                                echo "    <option value=''";   if ($patient=='') echo " selected";  
//                                                                echo  "selected"; echo" >-- " . xl('Select') . " --\n";

                                                                while ($urow = sqlFetchArray($ures)) {
                                                                        $pid1 = $urow['pid'];
                                                                        echo "    <option value='$pid1'";
                                                                      
                                                                        
                                                                        if ($pid1 == $patient) echo " selected";
                                                                        echo ">" . $urow['fname'] . ", " . $urow['lname'] . "\n";
                                                                }

                                                                echo "   </select>\n";

                                                            ?>
                                                        </td>

                                                    </tr>
                                                </table>  <br><br>
                                                <input type="hidden" name="provider" id="provider" value="<?php echo $provider; ?>"/>
                                            </form> 
                                             <script type="text/javascript">

                                            $(document).ready( function () {
                                            function auto_submit(){
                                                var form_patient =jQuery('#form_patient').val();
                                              
                                                       $("#uca").load("chartoutput/add_chartoutput.php?form_patient="+form_patient+ "&provider=" +'<?php echo $provider; ?>' ,function(){ 
                                                   
                                                 });
                                            }
                                               auto_submit();
                                            });      
                                             </script>
                                            <div id="uca"></div> 
                                             
				</div>
				
				
				</div>
                 <div><br><br></div>
		</section>
                <?php include 'footer.php'; ?>
		<script type="text/javascript" src="assets/js/jquery.min.js"></script>
		<script type="text/javascript" src="assets/js/owl.carousel.min.js"></script>
		<script type="text/javascript" src="assets/js/isotope.pkgd.min.js"></script>
		<script type="text/javascript" src="assets/js/wow.min.js"></script>
		<script type="text/javascript" src="assets/js/bootstrap.min.js"></script>

		<script>
      		new WOW().init();
		</script>

		<script type='text/javascript'>
                 
			$(document).ready(function() {
                            jQuery.noConflict();
                           jQuery("#starting-slider").owlCarousel({
  					autoPlay: 3000,
      				navigation : false, // Show next and prev buttons
      				slideSpeed : 700,
      				paginationSpeed : 1000,
      				singleItem:true
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
                           });
                        
		</script>


		<script>
			jQuery( function() {
				  // init Isotope
			  	var $container = jQuery('.isotope').isotope
			  	({
				    itemSelector: '.element-item',
				    layoutMode: 'fitRows'
			  	});


  				// bind filter button click
  				jQuery('#filters').on( 'click', 'button', function() 
  				{
				    var filterValue = $( this ).attr('data-filter');
				    // use filterFn if matches value
				    $container.isotope({ filter: filterValue });
				 });
  
			  // change is-checked class on buttons
			  	jQuery('.button-group').each( function( i, buttonGroup ) 
			  	{
			    	var $buttonGroup = $( buttonGroup );
			    	$buttonGroup.on( 'click', 'button', function() 
			    	{
			      		$buttonGroup.find('.is-checked').removeClass('is-checked');
			      		jQuery( this ).addClass('is-checked');
			    	});
			  	});
                            
			});
		</script>
<!--                <script src="http://code.jquery.com/jquery-1.11.3.min.js"></script> -->
                <script>
                (function($){
                  var ico = $('<i class="fa fa-caret-right"></i>');
                  $('nav#menu li:has(ul) > a').append(ico);

                  $('nav#menu li:has(ul)').on('click',function(){
                    $(this).toggleClass('open');
                  });

                  $('a#toggle').on('click',function(e){
                    $('html').toggleClass('open-menu');
                    return false;
                  });


                  $('div#overlay').on('click',function(){
                    $('html').removeClass('open-menu');
                  })

                })(jQuery)
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
