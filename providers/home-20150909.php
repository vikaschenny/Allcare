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
//SANITIZE ALL ESCAPES
$sanitize_all_escapes=true;

//STOP FAKE REGISTER GLOBALS
$fake_register_globals=false;

require_once("../interface/globals.php");
require_once("../library/formdata.inc.php");
require_once("../library/globals.inc.php");

$provider=$_POST['authUser'];
$_SESSION['provider']=$provider;
if(isset($_POST['authUser'])!='' && isset($_POST['clearPass'])!='') {
    $sql=sqlStatement("SELECT id, fname, lname, specialty FROM users " .
      "WHERE active = 1 AND ( info IS NULL OR info NOT LIKE '%Inactive%' ) " .
      "AND authorized = 1 AND username='".$_POST['authUser']."'" .
      "ORDER BY lname, fname");
$id=sqlFetchArray($sql);
if(empty($id)){
     header('Location: ../providers/index.php?site=default');
}
?>
<!DOCTYPE HTML>
<html>
	<head>
		<title>Medical Website Template | Home :: W3layouts</title>
		<link href="css/style.css" rel="stylesheet" type="text/css"  media="all" />
		<link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
		<link rel="stylesheet" href="css/responsiveslides.css">
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
		<script src="js/responsiveslides.min.js"></script>
		  <script>
		    // You can also use "$(window).load(function() {"
			    $(function () {
			      // Slideshow 1
			      $("#slider1").responsiveSlides({
			        maxwidth: 2500,
			        speed: 600
			      });
			});
		  </script>
	</head>
	<body>
		<!---start-wrap---->
		
			<!---start-header---->
			<div class="header">
<!--					<div class="top-header">
						<div class="wrap">
						<div class="top-header-left">
							<p>+800-020-12345</p>
						</div>
						<div class="right-left">
							<ul>
								<li class="login"><a href="#">Login</a></li>
								<li class="sign"><a href="#">Sign up</a></li>
							</ul>
						</div>
						<div class="clear"> </div>
					</div>
				</div>-->
					<div class="main-header">
						<div class="wrap">
							<div class="logo">
								<a href="index.html"><img src="images/logo.png" title="logo" /></a> 
							</div>
							<div class="social-links">
								<ul>
<!--									<li><a href="#"><img src="images/facebook.png" title="facebook" /></a></li>
									<li><a href="#"><img src="images/twitter.png" title="twitter" /></a></li>
									<li><a href="#"><img src="images/feed.png" title="Rss" /></a></li>-->
                                                                    <li class="login"><a href="logout_page.php">Logout</a></li>
<!--								<li class="sign"><a href="#">Sign up</a></li>-->
									<div class="clear"> </div>
								</ul>
							</div>
							<div class="clear"> </div>
						</div>
					</div>
					<div class="clear"> </div>
<!--					<div class="top-nav">
						<div class="wrap">
							<ul>
								<li class="active"><a href="index.html">Home</a></li>
								<li><a href="about.html">About</a></li>
								<li><a href="services.html">Services</a></li>
								<li><a href="news.html">News</a></li>
								<li><a href="contact.html">Contact</a></li>
								<div class="clear"> </div>
							</ul>
						</div>
					</div>-->
                                        <div id='cssmenu1'>
                                            <?php $sql12=sqlStatement("SELECT * FROM  `list_options` WHERE list_id ='AllCareProviderPortal' ORDER BY seq");?>
                                              <ul>   
                                                 <?php while($row11=sqlFetchArray($sql12)){ 
                                                        $mystring = $row11['option_id'];
                                                        $pos = strpos($mystring, '_');
                                                        if(false == $pos) {
                                                                $sql_lis=sqlStatement("SELECT * FROM  `list_options` WHERE list_id ='AllCareProviderPortal' AND option_id = '$mystring' ORDER BY seq");
                                                                while($row_lis=sqlFetchArray($sql_lis)){
                                                                $opt_id=$row_lis['option_id']."_";
                                                                $sql_li=sqlStatement("SELECT * FROM  `list_options` WHERE list_id ='AllCareProviderPortal' AND option_id LIKE '%$opt_id%' ORDER BY seq");
                                                                if(sqlNumRows($sql_li) != 0 ){ ?>
                                                                     <li class='has-sub'><a href="<?php echo $row_lis['notes']; ?>?provider=<?php echo $provider;  ?>"><span><?php echo $row_lis['title']; ?></span></a>
                                                                     <ul>
                                                                 <?php while($row_li=sqlFetchArray($sql_li)){ 
                                                                             $ex=explode("_",$row_li['option_id']); 
                                                                             if(count($ex)==2){
                                                                                   $sub1=$ex[0]."_".$ex[1];
                                                                                   $sql_sub=sqlStatement("SELECT * FROM  `list_options` WHERE list_id ='AllCareProviderPortal' AND option_id = '$sub1' ORDER BY seq");
                                                                                   $row_sub=sqlFetchArray($sql_sub);
                                                                                   ?>
                                                                                    <li class=last'><a href="<?php echo $row_sub['notes']; ?>?provider=<?php echo $provider;  ?>"><span><?php  echo $row_sub['title']; ?></span></a> 
                                                                                   </li>   
                                                                            <?php   } ?>
                                                                             
                                                                    <?php } ?> </ul></li>
                                                                <?php }else { if($row11['option_id']=='home'){?>
                                                                     <li class='active'><a href="<?php echo $row11['notes']; ?>?provider=<?php echo $provider;  ?>"><span><?php echo $row11['title']; ?></span></a></li>
                                                                <?php  }else { ?>
                                                                     <li><a href="<?php echo $row11['notes']; ?>?provider=<?php echo $provider;  ?>"><span><?php echo $row11['title']; ?></span></a></li>
                                                               <?php }
                                                                
                                                                }
                                                               
                                                            }    
                                                         }
                                                           else { ?>
                                                           
                                                       <?php }?>
                                                     
                                                 <?php } ?>
                                              </ul>      
                                        </div>
<!--                                        <div id='cssmenu'>
                                         <ul>
                                               <li class='active'><a href='index.html'><span>Home</span></a></li>
                                                <li><a href='providers_patient.php'><span>Patients</span></a></li>
                                               <li class='has-sub'><a href='services.html'><span>Incomplete</span></a>
                                                  <ul>
                                                     <li class='has-sub'><a href='news.html'><span>Product 1</span></a>
                                                        <ul>
                                                           <li><a href='#'><span>Sub Product</span></a></li>
                                                           <li class='last'><a href='#'><span>Sub Product</span></a></li>
                                                        </ul>
                                                     </li>
                                                     <li class='has-sub'><a href='#'><span>Product 2</span></a>
                                                        <ul>
                                                           <li><a href='#'><span>Sub Product</span></a></li>
                                                           <li class='last'><a href='#'><span>Sub Product</span></a></li>
                                                        </ul>
                                                     </li>
                                                  </ul>
                                               </li>
                                               <li><a href='#'><span>About</span></a></li>
                                               <li class='last'><a href='contact.html'><span>Contact</span></a></li>
                                            </ul>
                                        </div>-->
			</div>
			<!---End-header---->
			<!---start-images-slider---->
			<div class="image-slider">
						<!-- Slideshow 1 -->
					    <ul class="rslides rslides1" id="slider1" style="max-width: 2500px;">
					      <li id="rslides1_s0" class="" style="display: block; float: none; position: absolute; opacity: 0; z-index: 1; -webkit-transition: opacity 600ms ease-in-out; transition: opacity 600ms ease-in-out;">
					      	<img src="images/slider3.png" alt="">
					      	<div class="slider-info">
					      		<p>Medica the best Hospital website</p>
					      		<span>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. </span>
					      		<a href="#">ReadMore</a>
					      	</div>
					      </li>
					      <li id="rslides1_s1" class="" style="float: none; position: absolute; opacity: 0; z-index: 1; display: list-item; -webkit-transition: opacity 600ms ease-in-out; transition: opacity 600ms ease-in-out;"><img src="images/slider2.png" alt="">
					      	<div class="slider-info">
					      		<p>Medica the best Hospital website</p>
					      		<span>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. </span>
					      		<a href="#">ReadMore</a>
					      	</div>
					      </li>
					      <li id="rslides1_s2" class="rslides1_on" style="float: left; position: relative; opacity: 1; z-index: 2; display: list-item; -webkit-transition: opacity 600ms ease-in-out; transition: opacity 600ms ease-in-out;"><img src="images/slider2.png" alt="">
					      	<div class="slider-info">
					      		<p>Medica the best Hospital website</p>
					      		<span>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. </span>
					      		<a href="#">ReadMore</a>
					      	</div>
					      </li>
					    </ul>
						 <!-- Slideshow 2 -->
					</div>
			<!---End-images-slider---->
			<!----start-content----->
<!--			<div class="content">
				<div class="content-top-grids">
					<div class="wrap">
						<div class="content-top-grid">
							<div class="content-top-grid-header">
								<div class="content-top-grid-pic">
									<a href="#"><img src="images/timer.png" title="image-name" /></a>
								</div>
								<div class="content-top-grid-title">
									<h3>24x7 Servives</h3>
								</div>
								<div class="clear"> </div>
							</div>
								<p>Lorem ipsum dolor sit amet conse ctetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud.</p>
								<a class="grid-button" href="#">Read More</a>
								<div class="clear"> </div>
						</div>
						<div class="content-top-grid">
							<div class="content-top-grid-header">
								<div class="content-top-grid-pic">
									<a href="#"><img src="images/tool.png" title="image-name" /></a>
								</div>
								<div class="content-top-grid-title">
									<h3>CARE ADVICES</h3>
								</div>
								<div class="clear"> </div>
							</div>
								<p>Lorem ipsum dolor sit amet conse ctetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud.</p>
								<a class="grid-button" href="#">Read More</a>
								<div class="clear"> </div>
						</div>
						<div class="content-top-grid">
							<div class="content-top-grid-header">
								<div class="content-top-grid-pic">
									<a href="#"><img src="images/inject.png" title="image-name" /></a>
								</div>
								<div class="content-top-grid-title">
									<h3>EMERGENCY</h3>
								</div>
								<div class="clear"> </div>
							</div>
								<p>Lorem ipsum dolor sit amet conse ctetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud.</p>
								<a class="grid-button" href="#">Read More</a>
								<div class="clear"> </div>
						</div>
						<div class="clear"> </div>
					</div>
				</div>
				<div class="clear"> </div>
				<div class="boxs">
					<div class="wrap">
						<div class="section group">
							<div class="grid_1_of_3 images_1_of_3">
								  <h3>WELCOME!</h3>
								  <span>Lorem ipsum dolor sit amet conse ctetur adipisicing elit, sed do eiusmod.</span>
								  <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed in reprehenderit adipisicing elit, sed in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.Sed ut nim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit,  pariatur?</p>
							     <p>perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo e</p>
							    <p>dolor sit amet, consectetur adipisicing elit, sed in reprehenderit adipisicing elit, sed in reprehenderit in voluptate velit esse cillum </p>
							     <div class="button"><span><a href="#">Read More</a></span></div>
							</div>
							<div class="grid_1_of_3 images_1_of_3">
								  <h3>ABOUT US</h3>
								  <span>Lorem ipsum dolor sit amet conse ctetur adipisicing elit,</span>
								  <p>in voluptate Lorem ipsum, in voluptate velit esse cillum dolore eu fugiat amet conse ctetur adipisicing elit nulla pariatur.</p>
								  <span>Lorem ipsum dolor sit, fugiat nulla pariatur</span>
								  <p>fugiat nulla Lorem ipsum dolor sit amet, consectetur adipisicing elitamet conse ctetur adipisicing elit, fugiat nulla pariatur.</p>
								  <span>Lorem ipsum dolor sit amet cons,</span>
								  <p>consectetur Lorem ipsum dolor sit amet, consectetur adipisicing elit, in voluptate velit esse cillu.</p>
								  <span>Lorem ipsum dolor sit amet conse ctetur adipisicing elit,</span>
								  <p>Lorem ipsum dolor sit amet, consectetur adipisorem ipsum dolor sit amet, consectetur adipiicing elit, in voluptate.</p>
								  
							     <div class="button"><span><a href="#">Read More</a></span></div>
							</div>
							<div class="grid_1_of_3 images_1_of_3">
								  <h3>OUR SERVICES</h3>
								  <ul>
								  	<li><a href="#">Lorem ipsum dolor sit amet</a></li>
								  	<li><a href="#">Conse ctetur adipisicing</a></li>
								  	<li><a href="#">Elit sed do eiusmod tempor</a></li>
								  	<li><a href="#">Incididunt ut labore</a></li>
								  	<li><a href="#">Et dolore magna aliqua</a></li>
								  	<li><a href="#">Ut enim ad minim veniam</a></li>
								  	<li><a href="#">Quis nostrud exercitation</a></li>
								  	<li><a href="#">Ullamco laboris nisi</a></li>
								  	<li><a href="#">Ut aliquip ex ea commodo</a></li>
								  </ul>
							     <div class="button"><span><a href="#">Read More</a></span></div>
							</div>
						</div>
					</div>
					</div>
			--End-content---
		</div>-->
		<!---End-wrap---->
		<!---start-footer---->
<!--		<div class="footer">
			<div class="wrap">
				<div class="footer-grids">
					<div class="footer-grid">
						<h3>OUR Profile</h3>
						 <ul>
							<li><a href="#">Lorem ipsum dolor sit amet</a></li>
							<li><a href="#">Conse ctetur adipisicing</a></li>
							<li><a href="#">Elit sed do eiusmod tempor</a></li>
							<li><a href="#">Incididunt ut labore</a></li>
							<li><a href="#">Et dolore magna aliqua</a></li>
							<li><a href="#">Ut enim ad minim veniam</a></li>
						</ul>
					</div>
					<div class="footer-grid">
						<h3>Our Services</h3>
						 <ul>
							<li><a href="#">Et dolore magna aliqua</a></li>
							<li><a href="#">Ut enim ad minim veniam</a></li>
							<li><a href="#">Quis nostrud exercitation</a></li>
							<li><a href="#">Ullamco laboris nisi</a></li>
							<li><a href="#">Ut aliquip ex ea commodo</a></li>
						</ul>
					</div>
					<div class="footer-grid">
						<h3>OUR FLEET</h3>
						 <ul>
							<li><a href="#">Lorem ipsum dolor sit amet</a></li>
							<li><a href="#">Conse ctetur adipisicing</a></li>
							<li><a href="#">Elit sed do eiusmod tempor</a></li>
							<li><a href="#">Incididunt ut labore</a></li>
							<li><a href="#">Et dolore magna aliqua</a></li>
							<li><a href="#">Ut enim ad minim veniam</a></li>
						</ul>
					</div>
					<div class="footer-grid">
						<h3>CONTACTS</h3>
						 <p>Lorem ipsum dolor sit amet ,</p>
						 <p>Conse ctetur adip .</p>
						 <p>ut labore Usa.</p>
						 <span>(202)1234-56789</span>
					</div>
					<div class="clear"> </div>
				</div>
				<div class="clear"> </div>
				-start-copy-right---
				<div class="copy-tight">
					<p>Design by <a href="http://w3layouts.com/">W3layouts</a></p>
				</div>
				-End-copy-right---
			</div>
		</div>-->
		<!---End-footer---->
	</body>
</html>

<?php } else {
header('Location: ../providers/index.php?site=default'); } ?>