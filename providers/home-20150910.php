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
		<link href='http://fonts.googleapis.com/css?family=Roboto+Condensed:400,300' rel='stylesheet' type='text/css'>
                <style>
/*                    #menu {
  text-align: center;
  transition: all ease-out 0.3s;
}*/

#menu a { color: white; }

#menu ul {
  margin: 0;
  padding: 0;
  background-color:#46A1B4;
}

#menu ul li {
  display: inline-block;
  position: relative;
}

#menu ul li > a {
  display: inline-block;
  padding: 10px;
}

#menu ul li > a > i {
  margin-left: 15px;
  transition: all ease-out 0.3s;
  -webkit-transition: all ease-out 0.1s;
}

#menu ul li ul {
  display: none;
  position: absolute;
  top: 38px;
  width: 200px;
  text-align: left;
}

#menu ul li ul li { display: block; }

#menu ul li ul li a { display: block; }

#menu ul li:hover > a { background-color: rgba(0, 0, 0, 0.3); }

#menu ul li:hover > a > i { transform: rotateZ(90deg); }

#menu ul li:hover ul { display: block; }

/*a#toggle {
  position: fixed;
  top: 10px;
  left: 10px;
  width: 40px;
  height: 40px;
  background-color: #46A1B4;
  text-align: center;
  color: white;
  display: none;
  transition: all ease-out 0.3s;
   float: right;
}

a#toggle i {
  position: relative;
  top: 50%;
  transform: translateY(-50%);
}*/
@media screen and (max-width: 767px) {

/*a#toggle { display: block; }*/

main#content {
  margin-top: 65px;
  transition: all ease-out 0.3s;
}

/*#menu {
  position: fixed;
 // width: 250px;
  height: 100%;
  top: 0;
  left: 0;
  overflow: hidden;
  overflow-y: auto;
  background-color: transparent;
  transform: translateX(-250px);
}*/

#menu ul {
  text-align: left;
  background-color: #46A1B4;
}

#menu ul li { display: block; }

#menu ul li a { display: block; }

#menu ul li a > i { float: right; }

#menu ul li ul {
  display: none;
  position: static;
  width: 100%;
 background-color: rgba(22,160,133, 0.8);
}

#menu ul li:hover > ul { display: none; }

#menu ul li:hover > a > i { transform: rotateZ(0); }

#menu ul li.open > a { background-color: rgba(0, 0, 0, 0.3); }

#menu ul li.open > a > i { transform: rotateZ(90deg); }

#menu ul li.open > ul { display: block; }

/*div#overlay {
  display: block;
  visibility: hidden;
  position: fixed;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.8);
  transition: all ease-out 0.3s;
  z-index: 1;
  opacity: 0;
}

html.open-menu { overflow: hidden; }

html.open-menu div#overlay {
  visibility: visible;
  opacity: 1;
  width: calc(-150%);
  left: 250px;
}

html.open-menu a#toggle,
 html.open-menu main#content { transform: translateX(250px); }

html.open-menu nav#menu {
  z-index: 3;
  transform: translateX(0);
}*/
}
                
                </style>
	</head>

	<body>
		<section class= "navs">
			<nav class="navbar navbar-default navbar-fixed-top" role="navigation" id="menu">
  				<div class="container-fluid">
    				<!-- Brand and toggle get grouped for better mobile display -->
    				<div class="navbar-header">
      					<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
					        <span class="sr-only">Toggle navigation</span>
					        <span class="icon-bar"></span>
					        <span class="icon-bar"></span>
					        <span class="icon-bar"></span>
	      				</button>
	      				<a class="navbar-brand logo" href="#">
	      					<span><i class="fa fa-stethoscope"></i></span>
	      					HealthCare
	      				</a>
	    			</div>
		
		    		<!-- Collect the nav links, forms, and other content for toggling -->
<!--                                 <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                                     <ul class='navbar-right'>
                                            <li><a href="#">Item 01</a></li>
                                            <li><a href="#">Item 02</a></li>
                                            <li><a href="#">Item 03</a><ul>
                                                <li><a href="#">Submenu 01</a></li>
                                                <li><a href="#">Submenu 02</a></li>
                                                <li><a href="#">Submenu 03</a></li>
                                              </ul></li>

                                            <li><a href="#">Item 04</a>

                                            </li>
                                            <li><a href="#">Item 05</a></li>
                                    </ul>
                                </div>-->
                                 <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                                   <?php $sql12=sqlStatement("SELECT * FROM  `list_options` WHERE list_id ='AllCareProviderPortal' ORDER BY seq");?>
                                     <ul class='navbar-right'>
                                        <?php while($row11=sqlFetchArray($sql12)){ 
                                             $mystring = $row11['option_id'];
                                             $pos = strpos($mystring, '_');
                                             if(false == $pos) {
                                                $sql_lis=sqlStatement("SELECT * FROM  `list_options` WHERE list_id ='AllCareProviderPortal' AND option_id = '$mystring' ORDER BY seq");
                                                while($row_lis=sqlFetchArray($sql_lis)){
                                                     $opt_id=$row_lis['option_id']."_";
                                                     $sql_li=sqlStatement("SELECT * FROM  `list_options` WHERE list_id ='AllCareProviderPortal' AND option_id LIKE '%$opt_id%' ORDER BY seq");
                                                     if(sqlNumRows($sql_li) != 0 ){ ?>
                                                         <li><a href="<?php echo $row_lis['notes']; ?>?provider=<?php echo $provider;  ?>"><span><?php echo $row_lis['title']; ?></span></a>
                                                         <ul>
                                                            <?php while($row_li=sqlFetchArray($sql_li)){ 
                                                                    $ex=explode("_",$row_li['option_id']); 
                                                                    if(count($ex)==2){
                                                                       $sub1=$ex[0]."_".$ex[1];
                                                                       $sql_sub=sqlStatement("SELECT * FROM  `list_options` WHERE list_id ='AllCareProviderPortal' AND option_id = '$sub1' ORDER BY seq");
                                                                       $row_sub=sqlFetchArray($sql_sub); ?>
                                                                         <li><a href="<?php echo $row_sub['notes']; ?>?provider=<?php echo $provider;  ?>"><span><?php  echo $row_sub['title']; ?></span></a> 
                                                                                   </li>    
                                                                <?php }
                                                                } ?>
                                                        </ul></li>
                                                    <?php }else{ ?>
                                                             <li><a href="<?php echo $row11['notes']; ?>?provider=<?php echo $provider;  ?>"><span><?php echo $row11['title']; ?></span></a></li>
                                                 <?php   }
                                                 }
                                             }
                                         } ?>  
                                    </ul>
                                </div>
				</div><!-- container-fluid -->
			</nav>
               </section>
		<section class= "heading-slider">
			<div id="starting-slider" class="owl-carousel owl-theme">
 
			  	<div class="item">
			  		<div class="slider-1">
			  			<div class="slider-inner">
			  				<div class="container">
			  					<div class="row">
			  						<div class="slider-inner-text">
			  							<h1>We're Professional</h1>
			  							<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Debitis illum dignissimos tempore ad, eos sint ullam </p>
			  						</div>	
				  				</div>
			  				</div>
			  			</div>
			  		</div>
			  	</div>
			  	<div class="item">
			  		<div class="slider-2">
			  			<div class="slider-inner">
			  				<div class="container">
			  					<div class="row">
			  						<div class="slider-inner-text">
			  							<h1>We're Caring</h1>
			  							<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Debitis illum dignissimos tempore ad, eos sint ullam </p>
			  						</div>	
				  				</div>
			  				</div>
			  			</div>
			  		</div>
			  	</div>
			  	<div class="item">
			  		<div class="slider-3">
			  			<div class="slider-inner">
			  				<div class="container">
			  					<div class="row">
			  						<div class="slider-inner-text">
			  							<h1>We're Responsible</h1>
			  							<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Debitis illum dignissimos tempore ad, eos sint ullam </p>
			  						</div>	
				  				</div>
			  				</div>
			  			</div>
			  		</div>
			  	</div>
			 
			</div>
		</section>

		<section class= "starting-text">
      		<div class="container">
      			<div class="row">
      				<div class="welcome">
        				<h2 class="welcome-title">Welcome To Our Health Care Clinic</h2>
        				<p class="welcome-txt">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. </p>
		        		<button class="welcome-btn">Read More</button>	
        			</div>
        		</div>
      		</div>
	  	</section>


		<section id= "services">
			<div class= "container">
				<div class= "row">
					<div class= "col-lg-12 col-sm-12 col-xs-12">
						<h2 class= "headline text-center">
							Why Choose Us
						</h2>
						<p class="sub-headline text-center">
							Lorem ipsum dolor sit amet, consectetur
						</p>
                                                
					</div>
				</div>
				
				<div class= "row">
					<div class= "col-lg-4 col-sm-6 col-xs-12">
						<div class= "hservice">
							<div class= "service-img">
								<img class= "img-responsive center-block" src="assets/img/service1.jpg" alt="">
							</div>
							<div class= "service-description text-center">
								<h4 class= "service-heading">Top Qualified Doctors</h4>
								<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Recusandae nisi nostrum sunt cum, consequuntur asperiores, impedit voluptate eveniet modi quas omnis.</p>
								<a href="#" class="rm-btn btn btn-primary">
										Read More <i class="fa fa-caret-right"></i></a>
							</div>
						</div>
						<div class="clearfix"></div>
					</div>
					<div class= "col-lg-4 col-sm-6 col-xs-12">
						<div class= "hservice">
							<div class= "service-img">
								<img class= "img-responsive center-block" src="assets/img/service2.jpg" alt="">
							</div>
							<div class= "service-description text-center">
								<h4 class= "service-heading">Online Q & A</h4>
								<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Optio nulla necessitatibus adipisci consequatur explicabo provident officiis fugit. Nihil dolorem officiis.</p>
								<a href="#" class="rm-btn btn btn-primary">
										Read More <i class="fa fa-caret-right"></i></a>
							</div>
						</div>
						<div class="clearfix"></div>
					</div>
					<div class= "col-lg-4 col-sm-6 col-xs-12">
						<div class= "hservice">
							<div class= "service-img">
								<img class= "img-responsive center-block" src="assets/img/service3.jpg" alt="">
							</div>
							<div class= "service-description text-center">
								<h4 class= "service-heading">Symptom Check</h4>
								<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Molestias ipsum natus, odio quae! Ratione tempora accusantium explicabo tenetur, porro numquam.</p>
								<a href="#" class="rm-btn btn btn-primary">
										Read More <i class="fa fa-caret-right"></i></a>
							</div>
						</div>
						<div class="clearfix"></div>
					</div>
					<div class= "col-lg-4 col-sm-6 col-xs-12">
						<div class= "hservice">
							<div class= "service-img">
								<img class= "img-responsive center-block" src="assets/img/service4.jpg" alt="">
							</div>
							<div class= "service-description text-center">
								<h4 class= "service-heading">Competitive Pricing</h4>
								<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Molestias ipsum natus, odio quae! Ratione tempora accusantium explicabo tenetur, porro numquam.</p>
								<a href="#" class="rm-btn btn btn-primary">
										Read More <i class="fa fa-caret-right"></i></a>
							</div>
						</div>
						<div class="clearfix"></div>
					</div>
					<div class= "col-lg-4 col-sm-6 col-xs-12">
						<div class= "hservice">
							<div class= "service-img">
								<img class= "img-responsive center-block" src="assets/img/service5.jpg" alt="">
							</div>
							<div class= "service-description text-center">
								<h4 class= "service-heading">Medical Counseling</h4>
								<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Recusandae nisi nostrum sunt cum, consequuntur asperiores, impedit voluptate eveniet modi quas omnis.</p>
								<a href="#" class="rm-btn btn btn-primary">
										Read More <i class="fa fa-caret-right"></i></a>
							</div>
						</div>
						<div class="clearfix"></div>
					</div>
					<div class= "col-lg-4 col-sm-6 col-xs-12">
						<div class= "hservice">
							<div class= "service-img">
								<img class= "img-responsive center-block" src="assets/img/service6.jpg" alt="">
							</div>
							<div class= "service-description text-center">
								<h4 class= "service-heading">Caring Staff</h4>
								<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Optio nulla necessitatibus adipisci consequatur explicabo provident officiis fugit. Nihil dolorem officiis.</p>
								<a href="#" class="rm-btn btn btn-primary">
										Read More <i class="fa fa-caret-right"></i></a>
							</div>
						</div>
						<div class="clearfix"></div>
					</div>
				</div>
			</div>
		</section>






		<section id= "testimonial" class="text-center">
			<div class="testimonial-wrapper">
				<div class="container">
		    		<div class="row client-content text-center">
		    			<div class="col-md-8">
		    				<div class="row">
								<h1>Testimonial</h1>
							</div>
							<div class="row">
								<div class="sub-headline">
									<p>What Our Patients Say About Us</p>
								</div>
							</div>
		    				
							<div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
		  

		  					<!-- Wrapper for slides -->
		  					<div class="carousel-inner" role="listbox">
		    					<div class="item active">
		      						<div id="client-speech">
										<div class="item">
											<div class="row">
												<div class="col-md-12">
													<i class="fa fa-quote-left"></i>
												</div>
												<div class="col-md-8 col-md-offset-2">
													<p class="client-comment text-center">
														When you form a team, why do you try to form a team? Because teamwork builds trust and trust builds speed.
													</p>
												</div>
												<div class="col-md-12">
													<i class="fa fa-quote-right"></i>
												</div>
												<div class="col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3 col-xs-6 col-xs-offset-3">
													<img class="img-circle img-responsive center-block" src="assets/img/client3.jpg" alt="Image">
												</div>	
											</div>
											<div class= "row text-center">
												<p class="client-name text-center">Julia Doe</p>
											</div>
										</div>
									</div>
		   						</div>
		    				<div class="item">
		      				<div id="client-speech">
										<div class="item">
											<div class="row">
												<div class="col-md-12">
													<i class="fa fa-quote-left"></i>
												</div>
												<div class="col-md-8 col-md-offset-2">
													<p class="client-comment text-center">
														When you form a team, why do you try to form a team? Because teamwork builds trust and trust builds speed.
													</p>
												</div>
												<div class="col-md-12">
													<i class="fa fa-quote-right"></i>
												</div>
												<div class="col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3 col-xs-6 col-xs-offset-3">
													<img class="img-circle img-responsive center-block" src="assets/img/client1.jpg" alt="Image">
												</div>	
											</div>
											<div class= "row text-center">
												<p class="client-name text-center">John Doe</p>
											</div>
										</div>
									</div>
		    					</div>
		    					<div class="item">
		      						<div id="client-speech">
										<div class="item">
											<div class="row">
												<div class="col-md-12">
													<i class="fa fa-quote-left"></i>
												</div>
												<div class="col-md-8 col-md-offset-2">
													<p class="client-comment text-center">
														When you form a team, why do you try to form a team? Because teamwork builds trust and trust builds speed.
													</p>
												</div>
												<div class="col-md-12">
													<i class="fa fa-quote-right"></i>
												</div>
												<div class="col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3 col-xs-6 col-xs-offset-3">
													<img class="img-circle img-responsive center-block" src="assets/img/client4.jpg" alt="Image">
												</div>	
											</div>
											<div class= "row text-center">
												<p class="client-name text-center">Jane Doe</p>
											</div>
										</div>
									</div>
		    					</div>
		  					</div>

  						  <a class="left carousel-control" href="#carousel-example-generic" role="button" data-slide="prev">
						    <i class="fa fa-angle-left fa-3x"></i>
						    <span class="sr-only">Previous</span>
						  </a>
						  <a class="right carousel-control" href="#carousel-example-generic" role="button" data-slide="next">
						    <i class="fa fa-angle-right fa-3x"></i>
						    <span class="sr-only">Next</span>
						  </a>
						</div>
		    			</div>
		    			<div class="col-md-4">
		    				<div class= "appointment">
							
			                    <div class="header text-center">
			                        <h2>Make an Appointment</h2>
			                       	<a href="#" class="number">
			                       		<i class="fa fa-phone fa-fw"></i>
										1-800-123-4567
			                       	</a>
			                        <span class="or">OR</span>
			                    </div>

								<!-- form of appointment -->
								<div class="row">
									<form method="post" action="#">
										<div class= "form">
											<div class="input-group margin-bottom-sm col-md-8 col-md-offset-2 col-sm-8 col-sm-offset-2 col-xs-10 col-xs-offset-1">
											 	<input class="form-control" type="text" placeholder="Full Name *" required>
											</div>
											<div class="input-group margin-bottom-sm col-md-8 col-md-offset-2 col-sm-8 col-sm-offset-2 col-xs-10 col-xs-offset-1">
											 	<input class="form-control" type="text" placeholder="Email Address *" required>
											</div>
											<div class="input-group margin-bottom-sm col-md-8 col-md-offset-2 col-sm-8 col-sm-offset-2 col-xs-10 col-xs-offset-1">
											 	<input class="form-control" type="text" placeholder="Appointment Date *" required>
											</div>
											<div class="input-group margin-bottom-sm col-md-8 col-md-offset-2 col-sm-8 col-sm-offset-2 col-xs-10 col-xs-offset-1">
											 	<input class="form-control" type="text" placeholder="Mobile Number *" required>
											</div>
										</div>
										<div class="input-group margin-bottom-sm col-md-8 col-md-offset-2 col-sm-8 col-sm-offset-2 col-xs-10 col-xs-offset-1">
											<textarea class="form-control" rows="6" placeholder="Your Problem *" required></textarea>
										</div>
										<div class="col-md-8 col-md-offset-2 col-sm-8 col-sm-offset-2 col-xs-10 col-xs-offset-1">
											<div class="captcha-container">
			                                    <label>Are you human? </label><br/>
			                                    <img src="http://inspirythemesdemo.com/healthpress/wp-content/themes/healthpress-theme/captcha/captcha.php" alt="">
			                                    <input type="text" class="captcha required" name="captcha" maxlength="5" title=" Please enter the code characters displayed in image!">
			                                </div>
											<input class="btn btn-primary send" type="submit" value="Send">
										</div>
									</form>
								</div>
								<!-- end of form -->
							</div><!-- end of appointment-->
		    			</div>
		    		</div> <!--  client-content  -->
		    	</div>
			</div>	
	    </section>	<!-- testimonial -->
<!--            <section id="footer1">
                <div class="container">
                    testing........      responsive menu
                    <a id="toggle" href="#"><i class="fa fa-bars"></i></a>
                    <div id="overlay" ></div>
                    <nav id="menu">
                        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1"><ul class='navbar-right'>
                        <li><a href="#">Item 01</a></li>
                        <li><a href="#">Item 02</a></li>
                        <li><a href="#">Item 03</a><ul>
                            <li><a href="#">Submenu 01</a></li>
                            <li><a href="#">Submenu 02</a></li>
                            <li><a href="#">Submenu 03</a></li>
                          </ul></li>

                        <li><a href="#">Item 04</a>

                        </li>
                        <li><a href="#">Item 05</a></li>
                            </ul></div>
                    </nav>
                </div>
            </section>-->

		<section id="footer">
			<div class="container">
				<div class="row">
					<div class="col-md-6 col-md-offset-3">
						<ul class="text-center contact">
				        	<li class= "socials-icons">
								<a href="#" data-toggle="tooltip" title="Share in Facebook" class="facebook"><i class="fa fa-facebook"></i></a>
							</li>
							<li class= "socials-icons">
								<a href="#" data-toggle="tooltip" title="Share in Twitter" class="twitter"><i class="fa fa-twitter"></i></a>
							</li>
							<li class= "socials-icons">
								<a href="#" data-toggle="tooltip" title="Share in Google +" class="google-plus"><i class="fa fa-google-plus"></i></a>
							</li>
							<li class= "socials-icons">
								<a href="#" data-toggle="tooltip" title="Share in Instagram" class="instagram"><i class="fa fa-instagram"></i></a>
							</li>
							<li class= "socials-icons">
								<a href="#" data-toggle="tooltip" title="Share in Pinterest" class="pinterest"><i class="fa fa-pinterest"></i></a>
							</li>
							<li class= "socials-icons">
								<a href="#" data-toggle="tooltip" title="Connect with Skype" class="skype"><i class="fa fa-skype"></i></a>
							</li>
				      	</ul>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="copy-right-text text-center">
							&copy; Copyright 2014, Your Website Link. Theme by <a href="https://themewagon.com/">ThemeWagon</a>
						</div>
					</div>
				</div>
			</div>
		</section>

		<script type="text/javascript" src="assets/js/jquery.min.js"></script>
		<script type="text/javascript" src="assets/js/owl.carousel.min.js"></script>
		<script type="text/javascript" src="assets/js/isotope.pkgd.min.js"></script>
		<script type="text/javascript" src="assets/js/wow.min.js"></script>
		<script type="text/javascript" src="assets/js/bootstrap.min.js"></script>

		<script>
      		new WOW().init();
		</script>

		<script>
			$(document).ready(function() {
  				$("#starting-slider").owlCarousel({
  					autoPlay: 3000,
      				navigation : false, // Show next and prev buttons
      				slideSpeed : 700,
      				paginationSpeed : 1000,
      				singleItem:true
  				});
			});
		</script>


		<script>
			$( function() {
				  // init Isotope
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

<?php } else {
header('Location: ../providers/index.php?site=default'); } ?>