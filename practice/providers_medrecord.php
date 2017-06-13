<?php
//STOP FAKE REGISTER GLOBALS
$fake_register_globals=false;

//continue session
session_start();

//landing page definition -- where to go if something goes wrong
$landingpage = "../index.php?site=".$_SESSION['site_id'];	

$pagename = "medrec";

$ignoreAuth=true; // ignore the standard authentication for a regular OpenEMR user
include_once($_SERVER['DOCUMENT_ROOT']."/interface/globals.php");
include_once("chartoutput/chartoutput_lib.php");
include 'section_header.php';

if ( isset($_SESSION['portal_username']) ) {    
    $portal_user = $_SESSION['portal_username']; 
}else {
    session_destroy();
    header('Location: '.$landingpage.'&w');
    exit;
}  


$patient=$_REQUEST['form_patient'];
$grp=$_REQUEST['group'];
?>
<!DOCTYPE html>
<html>
    <head> 
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Medical Record</title>
        <link rel="stylesheet" href="css/version1.0/dataTables.bootstrap.min.css"/>
        <link rel="stylesheet" href="css/version1.0/responsive.bootstrap.min.css"/>
        <link rel="stylesheet" href="./../library/customselect/css/select2.css"/>
        <link rel="stylesheet" href="./../library/customselect/css/select2-bootstrap.css"/>
        <script src="js/responsive_datatable/jquery.min.js"></script>  
        <style>
            @media screen and (max-width: 767px) {
                main#content {
                  margin-top: 65px;
                  transition: all ease-out 0.3s;
                }

            }
            
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
            
            .showborder {
            width:650px;
            }
            .showborder td {
            border-bottom:1px dashed #000000;
            text-align:left;
            height:40px;

            }
            .showborder_head  th {
            border-bottom:1px solid #000000;
            text-align:left;
            }
            .shownoborder td {
            text-align:left;
            height:40px;
            }
            .showborder_long {
            width:100%;
            }
            .showborder_long tr td{
            border-bottom:1px dashed #000000;
            text-align:left;
            height:40px;
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

            function win1(url,event){
                event.preventDefault();
                window.open(url,'popup','width=900,height=900,scrollbars=no,resizable=yes');
            }
        </script>
    </head>
    <body>
        <section id= "services">
            <div class= "container-fluid">
                <div class= "row">
                    <div class= "col-lg-12 col-sm-12 col-xs-12" style='padding-top:10px !important;'>
                        <form name="userid_dropdown" id="userid_dropdown"  action="" method="POST">
                            <?php 
                                
                                if(strtolower(trim($_SESSION['see_all_providers'])) == 'yes'){
                                    $get_providers_list = sqlStatement("SELECT id,CONCAT (fname, lname, mname) as providername,username FROM users WHERE authorized != 0 AND active = 1 AND username <> '' ORDER BY fname,lname");
                                    while($set_providers_list = sqlFetchArray($get_providers_list)){
                                        $providername[ $set_providers_list['username']]    = $set_providers_list['providername'];
                                        $providernames[ $set_providers_list['username']]   = $set_providers_list['username'];
                                    }
                                }else if(strtolower(trim($_SESSION['see_all_providers'])) != 'yes' && $_SESSION['isprovider'] == 1){
                                    $providername[$_SESSION['portal_username']]   = $_SESSION['portal_userfullname'];
                                    $providernames[$_SESSION['portal_username']]  = $_SESSION['portal_username'];
                                }else if(strtolower(trim($_SESSION['see_all_providers'])) != 'yes' && $_SESSION['isprovider'] != 1){
                                    $get_providers_list = sqlStatement("SELECT pro_refers FROM tbl_user_custom_attr_1to1 WHERE userid = '".$_SESSION['portal_userid']."' LIMIT 0,1");
                                    while($set_providers_list = sqlFetchArray($get_providers_list)){
                                        $providers_string = explode("|",$set_providers_list['pro_refers']);
                                    }
                                    foreach ($providers_string as $pkey => $pvalue){
                                        $get_list_providername = sqlStatement("SELECT CONCAT (fname, lname, mname) as providername, username FROM users WHERE id = '$pvalue' AND username <> ''");
                                        while($set_list_providername = sqlFetchArray($get_list_providername)){
                                            $providername[$set_list_providername['username']] = $set_list_providername['providername'];
                                            $providernames[$set_list_providername['username']] = $set_list_providername['username'];
                                        }
                                    }
                                }
                                $rendprovider = $_POST['rendering_provider'];
                                $provider = $_POST['rendering_provider'];
                                $query  = "SELECT pid, lname, fname FROM patient_data WHERE practice_status ='YES' AND (deceased_stat != 'YES'  OR deceased_date <> NULL OR deceased_date ='0000-00-00 00:00:00') ORDER BY fname, lname ";
                                $ures = sqlStatement($query);
                                echo "<div class='input-group-sm'><label for='pro' style='float:left;'>Provider:</label>
                                                    <select name='rendering_provider'  id='rendering_provider' class='form-control' style='width:150px;margin: 0 75px 10px 75px;'>";
                                                        if(!empty($providername)){
                                                            foreach($providername as $provider_idsub => $providername_sub){
                                                                echo "<option value ='$provider_idsub' ";
                                                                if($provider_idsub == $rendprovider) echo " selected ";
                                                                echo "> $providername_sub </option>";  
                                                            }
                                                        }
                                echo "</select></div>";
                                echo "<div class='form-group'>";
                                    echo"<div class='row'>";
                                        echo "<div class='col-xs-5 icon-addon addon-md' >";
                                            echo "<select id='form_patient' class='select2'>"
                                            . "<option value=''></option>\n";
                                            while ($urow = sqlFetchArray($ures)) {
                                                    $pid1 = $urow['pid'];
                                                    echo "<option value='$pid1'>".$urow['fname'].", ".$urow['lname']."</option>";  
                                            }
                                           echo "</select>";
                                        echo "</div>";
                                    echo"</div>";
                                echo"</div>";
                            ?>
                            <input type="hidden" name="provider" id="provider" value="<?php echo $provider; ?>"/>
                            <script type="text/javascript">
                              //  $("#help_dialog").draggable({ handle:'#header'});
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
                                   
                                   $("#rendering_provider").change(function(){
                                       $("#userid_dropdown").submit();
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
                        </form>
                    </div>
                </div>
            </div>
        </section>
        <?php include 'section_footer.php'; ?>
        <script src="js/responsive_datatable/version1.0/jquery.dataTables.min.js"></script>
        <script src="js/responsive_datatable/version1.0/dataTables.responsive.min.js"></script>
        <script type='text/javascript' src='js/responsive_datatable/dataTables.tableTools.js'></script>
        <script type='text/javascript' src='js/responsive_datatable/dataTables.bootstrap.js'></script>
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
