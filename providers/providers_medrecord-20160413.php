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
  // $refer=$_REQUEST['refer'];  
 $refer=$_SESSION['refer'];
}else {
  $provider=$_REQUEST['provider'];
  //for logout
  $refer=$_REQUEST['refer'];
 $_SESSION['refer']=$_REQUEST['refer'];
 $_SESSION['portal_username']=$_REQUEST['provider']; 
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
$grp=$_REQUEST['group'];
echo $grp;
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
            <link rel="stylesheet" href="css/version1.0/dataTables.bootstrap.min.css"/>
            <link rel="stylesheet" href="css/version1.0/responsive.bootstrap.min.css"/>
            <script src="js/responsive_datatable/jquery.min.js"></script>            
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
                // table for f2f
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
                
                .icon-addon {
                    position: relative;
                    color: #555;
                    display: block;
                }


                .icon-addon.addon-md .glyphicon,
                .icon-addon .glyphicon, 
                .icon-addon.addon-md .fa,
                .icon-addon .fa {
                    position: absolute;
                    z-index: 2;
                    left: 10px;
                    font-size: 14px;
                    width: 20px;
                    margin-left: 10px;
                    text-align: center;
                    padding: 10px 0;
                    top: 1px
                }
                .icon-addon.addon-md .form-control,
                .icon-addon .form-control {
                    padding-left: 30px;
                    float: left;
                    font-weight: normal;
                }
                .form-control, .input-group-addon {
                    border-radius: 4px;
                }
            
                input::-webkit-calendar-picker-indicator {
                    display: none;/*remove default arrow in Chrome*/
                }
                .ins{
                    padding-top: 6px;
                    padding-left: 0px;
                    color:red;
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
    
function DoPost(page_name, provider,refer) {
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
                        var key1='refer';
                        var hiddenField1 = document.createElement("input");
                        hiddenField1.setAttribute("type", "hidden");
                        hiddenField1.setAttribute("name", key1);
                        hiddenField1.setAttribute("value", refer);
                        form.appendChild(hiddenField1);
                        form.appendChild(hiddenField);
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
                <div class= "container-fluid">
				<div class= "row">
					<div class= "col-lg-12 col-sm-12 col-xs-12" style='padding-top:100px !important;'>
                                           <form name="userid_dropdown"  action="" method="POST">
                                                            <?php 
                                                                 
                                                                $query  = "SELECT pid, lname, fname FROM patient_data WHERE practice_status ='YES' AND (deceased_stat != 'YES'  OR deceased_date <> NULL OR deceased_date ='0000-00-00 00:00:00') ORDER BY fname, lname ";
                                                                $ures = sqlStatement($query);

                                                                echo "<div class='form-group'><div class='col-xs-5 icon-addon addon-md' > <i class='glyphicon glyphicon-search'></i><input id='form_patient' list='patientlist' class='form-control' placeholder='Search Patient'></div><div class='col-xs-7 ins'>*Please Double Click on Search Box To View Full List Of Patients OR Specify Patient Name And Hit Enter.</div></div>\n";
                                                                echo "<datalist id='patientlist'>";
                                                                while ($urow = sqlFetchArray($ures)) {
                                                                        $pid1 = $urow['pid'];
                                                                        echo "<option data-opvalue='$pid1' value='".$urow['fname'] . ", " . $urow['lname']."'";
                                                                        echo ">";                                                                        
                                                                }

                                                                echo "   </datalist>\n";

                                                            ?>
                                                        <br><br>
                                                <input type="hidden" name="provider" id="provider" value="<?php echo $provider; ?>"/>
                                                <input type="hidden" name="refer" id="refer" value="<?php echo $_SESSION['refer']; ?>"/>
                                            </form> 
                                             <script type="text/javascript">
                                                  function ajaxcall(target, url,data){
                                                        $.ajax({
                                                            type: 'POST',
                                                            url: url,	
                                                            data:data,
                                                            success: function(response)
                                                            {
                                                             target.html(response);
                                                            },
                                                            failure: function(response)
                                                            {
                                                                alert("error"); 
                                                            }		
                                                        });	
                                                    }
                                                        
                                                    $(document).ready( function () {
                                                        
                                                        var datlistop = [];
                                                        var changeval = [];
                                                        $.each($("#patientlist > option"),function(){
                                                            if(datlistop.indexOf($(this).val()) !=-1){
                                                                changeval.push($(this).val());
                                                                $(this).val($(this).val()+" ("+findindexposarray(changeval,$(this).val())+")");
                                                            }
                                                            datlistop.push($(this).val())
                                                        });

                                                        function findindexposarray(ary,searchstring){
                                                            var count = 1;
                                                            $.each(ary,function(index,value){
                                                                if(value == searchstring){
                                                                    count++;
                                                                }
                                                            });
                                                            return count;
                                                        }
                                                        
                                                        var getdefaultval = $('#patientlist > option').first();
                                                        $("#form_patient").focus().val(getdefaultval.val());
                                                        
                                                        $("#form_patient").on('input', function () {
                                                            var val = this.value;
                                                            var opvalue = "";
                                                            if($('#patientlist').find('option').filter(function(){
                                                                if(this.value.toUpperCase() === val.toUpperCase()){
                                                                     opvalue = $(this).data("opvalue");
                                                                }
                                                                return this.value.toUpperCase() === val.toUpperCase();        
                                                            }).length) {
                                                                var pid = opvalue;
                                                                //alert(pid)
                                                                if(pid){
                                                                    ajaxcall($("#uca"),"chartoutput/add_chartoutput.php",{form_patient:pid,provider:'<?php echo $provider; ?>',group:'<?php echo $grp; ?>',refer:'<?php echo $refer; ?>'});
                                                                }
                                                            }
                                                        });
                                                        ajaxcall($("#uca"),"chartoutput/add_chartoutput.php",{form_patient:getdefaultval.data('opvalue'),provider:'<?php echo $provider; ?>',group:'<?php echo $grp; ?>',refer:'<?php echo $refer; ?>'});
                                                       
                                                    });
                                                    function closefancybox(){
                                                        $('.fancybox-close').click();
                                                    }
                                                    function closeandsavefabox(group,id){
                                                       // alert(id); 
                                                      // alert("test:"+group +"sfasd")
                                                        ajaxcall($("#uca"),"chartoutput/add_chartoutput.php",{form_patient:id,provider:'<?php echo $provider; ?>',group:group,refer:'<?php echo $refer; ?>'});
                                                        closefancybox();
                                                    }

                                             </script>
                                             <div id="uca" class="col-xs-12"></div> 
				</div>
				</div>
                 <div><br><br></div>
		</section>
                <?php include 'footer.php'; ?>
		
		<script type='text/javascript'>
                 
			$(document).ready(function() {
                            jQuery.noConflict();
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
                <script src="js/responsive_datatable/version1.0/jquery.dataTables.min.js"></script>
                <script src="js/responsive_datatable/version1.0/dataTables.bootstrap.min.js"></script>
                <script src="js/responsive_datatable/version1.0/dataTables.responsive.min.js"></script>
                <script type='text/javascript' src='js/responsive_datatable/dataTables.tableTools.js'></script>
                <script type='text/javascript' src='js/responsive_datatable/dataTables.bootstrap.js'></script>
                <script src="js/responsive_datatable/version1.0/jquery-1.11.3.min.js"></script>
		<script type="text/javascript" src="assets/js/owl.carousel.min.js"></script>
		<script type="text/javascript" src="assets/js/isotope.pkgd.min.js"></script>
		<script type="text/javascript" src="assets/js/wow.min.js"></script>
		<script type="text/javascript" src="assets/js/bootstrap.min.js"></script>   
                
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
