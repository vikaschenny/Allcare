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
    header('Location: ../practice/index.php?site=default');
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
            <title>HealthCare</title>
	    <link href='https://fonts.googleapis.com/css?family=Abel' rel='stylesheet' type='text/css'>
	    <link href='https://fonts.googleapis.com/css?family=Roboto:400,300,500' rel='stylesheet' type='text/css'>
	    <link href='https://fonts.googleapis.com/css?family=Dosis:300,400,500,600' rel='stylesheet' type='text/css'>
            <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">
            <link rel="stylesheet" type="text/css" href="assets/css/font-awesome.min.css">
            <link rel="stylesheet" type="text/css" href="assets/css/main.css">
            <link rel="stylesheet" type="text/css" href="assets/css/customize.css">
            <link href='https://fonts.googleapis.com/css?family=Roboto+Condensed:400,300' rel='stylesheet' type='text/css'>      
            <link rel="stylesheet" href="css/version1.0/dataTables.bootstrap.min.css"/>
            <link rel="stylesheet" href="css/version1.0/responsive.bootstrap.min.css"/>
            <link rel="stylesheet" href="./../library/customselect/css/select2.css"/>
            <link rel="stylesheet" href="./../library/customselect/css/select2-bootstrap.css"/>
            <script src="js/responsive_datatable/jquery.min.js"></script>            
            <script type="text/javascript" src="https://code.jquery.com/ui/1.9.2/jquery-ui.js"></script>
            <style>
                @media screen and (max-width: 767px) {
                    main#content {
                      margin-top: 65px;
                      transition: all ease-out 0.3s;
                    }

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
                .btn-glyphicon { 
                    float:right; 
                    padding:8px; 
                    background:#ffffff; 
                    margin-left:4px; 
                }
                .icon-btn { 
                    padding: 1px 2px 3px 6px; 
                    border-radius:50px; 
                    float:left; 
                    line-height:2em; 
                    margin-left:3px;
                }
                #services {
                    margin-bottom: -45px;
                }
                .select2{
                    width: 100%;
                }

            </style>

 <script type='text/javascript'>
    $(document).ready(function() {
         $("#services").css("min-height",window.innerHeight+"px");
          $("#chart_view").click( function() {
            toggle( $(this), "#chartoutput_div" );
        });
    });
    
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

    function win1(url,event){
        event.preventDefault();
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

                                                                echo "<div class='form-group'><div class='row'><div class='col-xs-5 icon-addon addon-md' ><select id='form_patient' class='select2'><option value=''></option>\n";
                                                                while ($urow = sqlFetchArray($ures)) {
                                                                        $pid1 = $urow['pid'];
                                                                        echo "<option value='$pid1'>".$urow['fname'].", ".$urow['lname']."</option>";  
                                                                }
                                                                echo "</select></div></div></div>"
                                                            ?>
                                                        <br><br>
                                                <input type="hidden" name="provider" id="provider" value="<?php echo $provider; ?>"/>
                                                <input type="hidden" name="refer" id="refer" value="<?php echo $_SESSION['refer']; ?>"/>
                                            </form> 
                                             <script type="text/javascript">
                                                 $("#help_dialog").draggable({ handle:'#header'});
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
                                                        var fromPatient = '<?php echo $patient ?>';
                                                        if(fromPatient !="")
                                                            $('#form_patient option[value="' + fromPatient + '"]').prop("selected",true);
                                                        
                                                        $('#form_patient.select2').select2({ placeholder : 'Select Patient' });
                                                        $("#form_patient").change(function(){
                                                            var pid = $(this).val();
                                                            if(pid){
                                                                ajaxcall($("#uca"),"chartoutput/add_chartoutput.php",{form_patient:pid,provider:'<?php echo $provider; ?>',group:'<?php echo $grp; ?>',refer:'<?php echo $refer; ?>'});
                                                            }
                                                        })
                                                        if(fromPatient !="")
                                                            ajaxcall($("#uca"),"chartoutput/add_chartoutput.php",{form_patient:$('#form_patient').val(),provider:'<?php echo $provider; ?>',group:'<?php echo $grp; ?>',refer:'<?php echo $refer; ?>'});
                                                        
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
                                                            modal.find(".modal-body").html("<iframe src='"+url+"' style='border:none; width:100%; height:"+frameheight+"px;'></iframe>");       
                                                        });
                                                       
                                                    });
                                                    function closefancybox(){
                                                        $('#modalwindow').modal('hide')
                                                    }
                                                    function closeandsavefabox(group,id){
                                                        ajaxcall($("#uca"),"chartoutput/add_chartoutput.php",{form_patient:id,provider:'<?php echo $provider; ?>',group:group,refer:'<?php echo $refer; ?>'});
                                                        closefancybox();
                                                    }
                                                    
                                                    
                    

                                             </script>
                                             <div class="row">
                                                <div id="uca" class="col-xs-12"></div> 
                                             </div>
				</div>
				</div>
                 <div><br><br></div>
		</section>
                <?php include 'footer.php'; ?>
                <script src="js/responsive_datatable/version1.0/jquery.dataTables.min.js"></script>
                <script src="js/responsive_datatable/version1.0/dataTables.responsive.min.js"></script>
                <script type='text/javascript' src='js/responsive_datatable/dataTables.tableTools.js'></script>
                <script type='text/javascript' src='js/responsive_datatable/dataTables.bootstrap.js'></script>
		<script type="text/javascript" src="assets/js/bootstrap.min.js"></script>
                <script src="./../library/customselect/js/select2.js"></script>
                <div class="modal fade" name = "modalwindow" id="modalwindow" tabindex="-1" role="dialog" aria-hidden="true" >
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
</html>
