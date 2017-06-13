<?php
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
require_once("../../verify_session.php");
$pagename = "plist";
if(isset($_SESSION['portal_username']) !=''){
   $provider=$_SESSION['portal_username'];
}else {
   $provider=$_REQUEST['provider'];
   $refer=$_REQUEST['refer']; 
   $_SESSION['refer']=$_REQUEST['refer'];
   $_SESSION['portal_username']=$_REQUEST['provider'];
} 

$base_url="//".$_SERVER['SERVER_NAME'].dirname($_SERVER["REQUEST_URI"].'?').'/';

 $sql=sqlStatement("SELECT id, fname, lname, specialty FROM users " .
      "WHERE active = 1 AND ( info IS NULL OR info NOT LIKE '%Inactive%' ) " .
      "AND authorized = 1 AND username='".$provider."'" .
      "ORDER BY lname, fname");
$id=sqlFetchArray($sql);
$id1=$id['id'];

$pid        = $_REQUEST['pid'];

$order=$_REQUEST['order'];
$provider=$_REQUEST['provider'] ? $_REQUEST['provider'] : $_SESSION['portal_username'];
$page_id=$_REQUEST['id'];

?>
<html>
<head>
<link href="http://netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.min.css" rel="stylesheet">
<link href="../../assets/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../../../library/customselect/css/select2.css"/>
<link rel="stylesheet" href="../../../library/customselect/css/select2-bootstrap.css"/>
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="../../../library/customselect/js/select2.js"></script>
<style>
    #loader{
        background: rgba(0,0,0,0.56);
        border-radius: 4px;
        display:table;
        height: 48px;
        width: 242px;
        color: #fff;
        position: absolute;
        left: 0px;
        top:0px;
        bottom: 0px;
        right: 0px;
        margin: auto;
        display: none;
    }
    .ajax-spinner-bars {
        height: 48px;
        left: 23px;
        position: relative;
        top: 20px;
        width: 35px;
        display: table-cell;
     }
     #loadertitle {
        display: table-cell;
        font-size: 17px;
        padding-left: 14px;
        vertical-align: middle;
     }

    .ajax-spinner-bars > div {
        position: absolute;
        width: 2px;
        height: 8px;
        background-color: #fff;
        opacity: 0.05;
        animation: fadeit 0.8s linear infinite;
    }
    .ajax-spinner-bars > .bar-1 {
        transform: rotate(0deg) translate(0, -12px);
        animation-delay:0.05s;
    }
    .ajax-spinner-bars > .bar-2 {
        transform: rotate(22.5deg) translate(0, -12px);
        animation-delay:0.1s;
    }
    .ajax-spinner-bars > .bar-3 {
        transform: rotate(45deg) translate(0, -12px);
        animation-delay:0.15s;
    }
    .ajax-spinner-bars > .bar-4 {
        transform: rotate(67.5deg) translate(0, -12px);
        animation-delay:0.2s;
    }
    .ajax-spinner-bars > .bar-5 {
        transform: rotate(90deg) translate(0, -12px);
        animation-delay:0.25s;
    }
    .ajax-spinner-bars > .bar-6 {
        transform: rotate(112.5deg) translate(0, -12px);
        animation-delay:0.3s;
    }
    .ajax-spinner-bars > .bar-7 {
        transform: rotate(135deg) translate(0, -12px);
        animation-delay:0.35s;
    }
    .ajax-spinner-bars > .bar-8 {
        transform: rotate(157.5deg) translate(0, -12px);
        animation-delay:0.4s;
    }
    .ajax-spinner-bars > .bar-9 {
        transform: rotate(180deg) translate(0, -12px);
        animation-delay:0.45s;
    }
    .ajax-spinner-bars > .bar-10 {
        transform: rotate(202.5deg) translate(0, -12px);
        animation-delay:0.5s;
    }
    .ajax-spinner-bars > .bar-11 {
        transform: rotate(225deg) translate(0, -12px);
        animation-delay:0.55s;
    }
    .ajax-spinner-bars > .bar-12 {
        transform: rotate(247.5deg) translate(0, -12px);
        animation-delay:0.6s;
    }
    .ajax-spinner-bars> .bar-13 {
        transform: rotate(270deg) translate(0, -12px);
        animation-delay:0.65s;
    }
    .ajax-spinner-bars > .bar-14 {
        transform: rotate(292.5deg) translate(0, -12px);
        animation-delay:0.7s;
    }
    .ajax-spinner-bars > .bar-15 {
        transform: rotate(315deg) translate(0, -12px);
        animation-delay:0.75s;
    }
    .ajax-spinner-bars> .bar-16 {
        transform: rotate(337.5deg) translate(0, -12px);
        animation-delay:0.8s;
    }

    @keyframes fadeit{
          0%{ opacity:1; }
          100%{ opacity:0;}
    }
    
    .select2{
        width: 100%;
    }
</style>
<script>
    $(function(){
        $('#form_patient_dropdown.select2').select2({ placeholder : 'Select Patient' });
        $("#form_patient_dropdown").change(function(){
            var pid = $(this).val();
            if(pid){
                $('#loader').show();
                $("#pcontenar").html("<iframe style='width: 100%;height: 86%; border:none;' id='pinsurace' src='insurancedata_full_patientselected.php?pid="+pid+"' onload='showloader()'></iframe>");
            }else{
               
            }
        });
    });
    function showloader(){
        $('#loader').hide();
    }
</script>
</head>
<body bgcolor='#FFFFFF'>
    <section id= "services">
        <div class= "container-fluid">
            <div class= "row">
                <div class= "col-lg-12 col-sm-12 col-xs-12">
                    <?php
                        $query  = "SELECT pid, lname, fname FROM patient_data ORDER BY lname, fname ";
                        $ures   = sqlStatement($query);
                        echo "<div class='form-group'>";
                        echo "<div class='row'><div class='col-xs-4 icon-addon addon-md col-sm-offset-4' style='margin-top: 15px;'><select id='form_patient_dropdown' class='select2'><option value=''></option>";
                        while ($urow = sqlFetchArray($ures)) {
                            echo "<option value='".$urow['pid']."'>".$urow['fname'] . ", " . $urow['lname']."</option>";
                        }
                        echo "</select></div></div></div>";
                    ?>
                    <div id="pcontenar">
                        
                    </div>
                    <div id="loader">
                        <div class="ajax-spinner-bars">
                            <div class='bar-1'></div><div class='bar-2'></div><div class='bar-3'></div><div class='bar-4'></div><div class='bar-5'></div><div class='bar-6'></div><div class='bar-7'></div><div class='bar-8'></div><div class='bar-9'></div><div class='bar-10'></div><div class='bar-11'></div><div class='bar-12'></div><div class='bar-13'></div><div class='bar-14'></div><div class='bar-15'></div><div class='bar-16'></div>                </div>
                        <div id="loadertitle">Patient Insurance Loading...</div>
                    </div>
                </div>
            </div>
        </div>    
        
    </section>
</body>

</html>