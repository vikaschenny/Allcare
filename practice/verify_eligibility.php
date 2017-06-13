<?php /*$ignoreAuth=true; */
require_once("verify_session.php");

if(isset($_SESSION['portal_username']) !=''){
$provider=$_SESSION['portal_username'];
$refer=$_SESSION['refer']; 
}else {
 $provider=$_REQUEST['provider'];
 $refer=$_REQUEST['refer'];  
 $_SESSION['portal_username']=$_REQUEST['provider'];
 $_SESSION['refer']=$_REQUEST['refer'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Verify Eligibility</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
  <style>
      
      #screens1, #screens2{
          overflow: hidden;
      }
      @media only screen and (min-device-width: 320px) and (max-device-width: 480px)and (-webkit-min-device-pixel-ratio: 2){
        .nav>li>a{
          position: relative;
          display: block;
          padding: 10px 10px;
        }
        .nav-tabs>li>a{
            font-size: 12px
        }
        input[type="text"], textarea, select{
            width: 100%;
        }
        #screens1>div{
            overflow: visible;
            overflow-y: scroll;
            -webkit-overflow-scrolling: touch;
            overflow-x: hidden;
        }
        #screens2>div{
            overflow: visible;
            overflow-y: scroll;
            -webkit-overflow-scrolling: touch;
        }
       
    }
    
    @media only screen and (min-device-width: 320px) and (max-device-width: 568px)and (-webkit-min-device-pixel-ratio: 2){
        .nav>li>a{
          position: relative;
          display: block;
          padding: 10px 10px;
        }
        .nav-tabs>li>a{
            font-size: 12px
        }
        input[type="text"], textarea, select{
            width: 100%;
        }
        #screens1>div{
            overflow: visible;
            overflow-y: scroll;
            -webkit-overflow-scrolling: touch;
        }
        #screens2>div{
            overflow: visible;
            overflow-y: scroll;
            -webkit-overflow-scrolling: touch;
            overflow-x: hidden;
        }
       
    }
    
    @media only screen and (min-device-width: 375px) and (max-device-width: 667px)and (-webkit-min-device-pixel-ratio: 2){
        .nav>li>a{
          position: relative;
          display: block;
          padding: 10px 10px;
        }
        .nav-tabs>li>a{
            font-size: 12px
        }
        input[type="text"], textarea, select{
            width: 100%;
        }
        #screens1>div{
            overflow: visible;
            overflow-y: scroll;
            -webkit-overflow-scrolling: touch;
        }
        #screens2>div{
            overflow: visible;
            overflow-y: scroll;
            -webkit-overflow-scrolling: touch;
            overflow-x: hidden;
        }
       
    }
    
     @media only screen and (min-device-width: 414px) and (max-device-width: 736px)and (-webkit-min-device-pixel-ratio: 2){
        .nav>li>a{
          position: relative;
          display: block;
          padding: 10px 10px;
        }
        .nav-tabs>li>a{
            font-size: 12px
        }
        input[type="text"], textarea, select{
            width: 100%;
        }
        #screens1>div{
            overflow: visible;
            overflow-y: scroll;
            -webkit-overflow-scrolling: touch;
        }
        #screens2>div{
            overflow: visible;
            overflow-y: scroll;
            -webkit-overflow-scrolling: touch;
            overflow-x: hidden;
        }
       
    }

  </style>
  <script>
       var eligibility = JSON.parse(window.localStorage.getItem("provider_eligibility"));
       eligibility[0].provider = '<?php echo $provider; ?>';
       eligibility[0].refer = '<?php echo $refer; ?>';
       eligibility[1].provider = '<?php echo $provider; ?>';
       eligibility[1].refer = '<?php echo $refer; ?>';
      console.log("eligibility: " + JSON.stringify(eligibility) + "url[0]: "+$.param(eligibility[0])+ "url[1]: "+$.param(eligibility[1]))
	function myFunction() {
            if(window.localStorage.getItem("mobile_sso") == "mobile"){
                <?php  unset($_SESSION['access_token']);
                      unset($_SESSION['gplusuer']);
                ?>
            }
            localStorage.removeItem("mobile_sso");    
            localStorage.removeItem("provider_eligibility");      
        }
	
	$(function(){
		var navtabs = "";
		var tbcontent = "";
			if(eligibility[0].frame=="show"){
                            var url = eligibility[0].page+".php?"+$.param(eligibility[0]);
                            navtabs += '<li class="active"><a data-toggle="tab" href="#screens1">'+eligibility[0].pagename+'</a></li>';
                            $('#frame1').attr("src",url);
			}
			
			if(eligibility[1].frame=="show"){
				var activeclass = eligibility[0].frame=="hide"?"active":"";
				var activeclass1 = eligibility[0].frame=="hide"?"tab-pane fade in active":"tab-pane fade";
				var url = eligibility[1].page+".php?"+$.param(eligibility[1]); 
				navtabs += '<li class="'+activeclass+'"><a data-toggle="tab" href="#screens2">'+eligibility[1].pagename+'</a></li>';
				$('.tab-content').append('<div id="screens2" class="'+activeclass1+'"><div><iframe id="frame2" src="'+url+'" width="100%" height="200" frameborder="no"/></div></div>');
			}
		if(eligibility[0].frame=="hide")
                    $('#screens1').remove()
			
		$('.nav-tabs').append(navtabs);
		$('.tab-content iframe').height($(window).height() - ($('iframe').eq(0).position().top+20))
                $('.tab-content iframe').parent().height($(window).height() - ($('iframe').eq(0).position().top+10))
                var countstring = "in";
                $('.nav-tabs a').on('shown.bs.tab', function(event){
                    var screen1 = $('#screens1 div').scrollTop();
                    var screen2 = $('#screens2 div').scrollTop();
                    
                    if(countstring == "in"){
                        $('.tab-content iframe').height($('.tab-content iframe').height() + 1);
                        countstring = "out";
                    }else{
                        $('.tab-content iframe').height($('.tab-content iframe').height() - 1);
                         countstring = "in";
                    }
                   // alert(screen1 + " : " + screen2)
                    $('#screens1 div').scrollTop(screen1);
                    $('#screens2 div').scrollTop(screen2);
                });
                
                 $(window).bind("beforeunload", function() { 
                    var framedata = document.getElementById("frame2").contentWindow.getframedata();
                    window.opener.datafromchildwindow(framedata.lbfid,framedata.monthval,framedata.pid,framedata.verify_type,framedata.payer_id, framedata.provider_id, framedata.dos);window.close();
                });

	})
	
  </script>
</head>
<body onunload="myFunction()">

<div class="container-fluid">
  <h2>Verify Eligibility</h2>
  <ul class="nav nav-tabs">
  </ul>
  <div class="tab-content">
	<div id="screens1" class="tab-pane fade in active">
      <div><iframe src="individual_patient_eligibility.php" id="frame1" width="100%" frameborder="no"/></div>
    </div>
  </div>
</div>

</body>
</html>

