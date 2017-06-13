<?php include("session_file.php"); 
    if($_SESSION['portal_username']=='' || $_SESSION['uid']==''){
         header('Location:index.php?w');
    }
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="">

        <title>Agency Portal</title>

        <!-- css -->
        <link href="css/bootstrap.min.css" rel="stylesheet" type="text/css">
        <link href="font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <link href="css/owl.carousel.css" rel="stylesheet" media="screen" />
        <link href="css/style.css" rel="stylesheet">

        <!-- boxed bg -->
        <link id="bodybg" href="bodybg/bg1.css" rel="stylesheet" type="text/css" />
        <!-- template skin -->
        <link id="t-colors" href="color/default.css" rel="stylesheet">
        <style>
            .sub-footer{
                margin-top: 0px;
            }
        </style>
    </head>
    <body id="page-top"  data-target=".navbar-custom">
        <div id="wrapper">
            <?php $page_id="home"; include("header_section.php"); ?>
            <!-- Section: intro -->
            <section id="intro" class="intro">
                <div class="intro-content">
                    <div class="container-fluid">
                        <div class="row">
                                <div id="jssor_1" style="position: relative; margin: 0 auto; top: 0px; left: 0px; width: 1300px; height: 500px; overflow: hidden; visibility: hidden;">
                                    <!-- Loading Screen -->
                                    <div data-u="loading" style="position: absolute; top: 0px; left: 0px;">
                                        <div style="filter: alpha(opacity=70); opacity: 0.7; position: absolute; display: block; top: 0px; left: 0px; width: 100%; height: 100%;"></div>
                                        <div style="position:absolute;display:block;background:url('img/loading.gif') no-repeat center center;top:0px;left:0px;width:100%;height:100%;"></div>
                                    </div>
                                    <div data-u="slides" style="cursor: default; position: relative; top: 0px; left: 0px; width: 1300px; height: 500px; overflow: hidden;">
                                        <div data-p="225.00">
                                            <img data-u="image" src="img/01.png" />
                                        </div>
                                        <div data-p="225.00" style="display: none;">
                                            <img data-u="image" src="img/02.png" />
                                        </div>
                                        <div data-p="225.00" data-po="80% 55%" style="display: none;">
                                            <img data-u="image" src="img/03.png" />
                                        </div>
                                        <a data-u="any" href="#" style="display:none">Full Width Slider</a>
                                    </div>
                                    <!-- Bullet Navigator -->
                                    <div data-u="navigator" class="jssorb05" style="bottom:16px;right:16px;" data-autocenter="1">
                                        <!-- bullet navigator item prototype -->
                                        <div data-u="prototype" style="width:16px;height:16px;"></div>
                                    </div>
                                    <!-- Arrow Navigator -->
                                    <span data-u="arrowleft" class="jssora22l" style="top:0px;left:0px;width:40px;height:58px;" data-autocenter="2"></span>
                                    <span data-u="arrowright" class="jssora22r" style="top:0px;right:0px;width:40px;height:58px;" data-autocenter="2"></span>
                                </div>
                        </div>		
                    </div>
                </div>		
            </section>
            <!-- /Section: intro -->
            <?php include("footer_section.php"); ?>
        </div>
        <a href="#" class="scrollup"><i class="fa fa-angle-up active"></i></a>
        <!-- Core JavaScript Files -->
        <script src="js/jquery.min.js"></script>
        <script src="js/jssor.slider-21.1.6.mini.js"></script>
        <script src="js/bootstrap.min.js"></script>
        <script src="js/jquery.easing.min.js"></script>
        <script src="js/jquery.appear.js"></script>
        <script>
        
            $(document).ready(function(){
                
                 var jssor_1_SlideoTransitions = [
                    [{b:-1,d:1,o:-1},{b:0,d:1000,o:1}],
                    [{b:1900,d:2000,x:-379,e:{x:7}}],
                    [{b:1900,d:2000,x:-379,e:{x:7}}],
                    [{b:-1,d:1,o:-1,r:288,sX:9,sY:9},{b:1000,d:900,x:-1400,y:-660,o:1,r:-288,sX:-9,sY:-9,e:{r:6}},{b:1900,d:1600,x:-200,o:-1,e:{x:16}}]
                  ];

                  var jssor_1_options = {
                    $AutoPlay: true,
                    $SlideDuration: 800,
                    $SlideEasing: $Jease$.$OutQuint,
                    $CaptionSliderOptions: {
                      $Class: $JssorCaptionSlideo$,
                      $Transitions: jssor_1_SlideoTransitions
                    },
                    $ArrowNavigatorOptions: {
                      $Class: $JssorArrowNavigator$
                    },
                    $BulletNavigatorOptions: {
                      $Class: $JssorBulletNavigator$
                    }
                  };

                  var jssor_1_slider = new $JssorSlider$("jssor_1", jssor_1_options);

                  /*responsive code begin*/
                  /*you can remove responsive code if you don't want the slider scales while window resizing*/
                  function ScaleSlider() {
                      var refSize = jssor_1_slider.$Elmt.parentNode.clientWidth;
                      if (refSize) {
                          refSize = Math.min(refSize, 1920);
                          jssor_1_slider.$ScaleWidth(refSize);
                      }
                      else {
                          window.setTimeout(ScaleSlider, 30);
                      }
                  }
                  ScaleSlider();
                  $(window).bind("load", ScaleSlider);
                  $(window).bind("resize", ScaleSlider);
                  $(window).bind("orientationchange", ScaleSlider);
            });
            
        </script>
    </body>
</html>
