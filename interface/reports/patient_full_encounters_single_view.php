<?php
//SANITIZE ALL ESCAPES
$sanitize_all_escapes=true;

//STOP FAKE REGISTER GLOBALS
$fake_register_globals=false;

require_once("../globals.php");
//require_once("../../library/formdata.inc.php"); 
//require_once("../../library/globals.inc.php");
//require_once("$srcdir/api.inc");
//require_once("$srcdir/forms.inc");
//require_once("$srcdir/options.inc.php");
//require_once("$srcdir/patient.inc");
//require_once("$srcdir/formdata.inc.php");
//require_once("$srcdir/formatting.inc.php");

$pid                    = $_REQUEST['pid'];
$encounter              = $_REQUEST['encounter'];
?>
<html>
    <head>
        <style>
            .section-header {
                border-bottom: 1px solid;
                margin-bottom: 5px;
                width: 100%;
            }

            .loader {
                position: absolute;
                left: 0px;
                top: 0px;
                width: 10%;
                height: 10%;
                z-index: 9999;
                margin: 200px 0px 0px 300px;
                background: url('../patient_file/img/loading.gif') 50% 50% no-repeat rgb(249,249,249);
            }
        </style>
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/jquery.1.3.2.js"></script>
    <script>
        $(document).ready(function(){
            $('#encounter_dropdown').change(function() {
                $('.calendar').remove();
                $( "#singleView_result" ).empty();
                var encounter = $('#encounter_dropdown option:selected').val();
                if(encounter != ''){
                    ajaxEncounterCall(encounter);
                }else{
                    $("#singleView_result").hide();    
                }
            });
        });
        function ajaxEncounterCall(encounter){
//            $(".xdsoft_datetimepicker").remove();
            $(".loader").show();
            var fieldname   = {
                                encounter   : encounter, 
                                pid         : '<?php echo $pid; ?>', 
                                isFromCharts: 1
                            };
//            $( "#singleView_result" ).load( "single_view_form.php?encounter="+encounter+"&pid=<?php echo $pid; ?>&isFromCharts=1", function() {
//                $(".loader").hide();
////                $("#singleView_result").show();    
////                $("#singleView_result").html(data);   
//            });
                $.ajax({
                    type: "GET",
                    url: "charts_single_view_form.php",
                    data: fieldname,
                    success: function(data, textStatus) {
                        $(".loader").hide();
                        $("#singleView_result").show();    
                        $("#singleView_result").html(data);    
                    },
                    error: function(jqXHR, exception){
                        alert("failed" + jqXHR.responseText);
                    }    
                });
        }
    </script>
    </head>
    <body class="body_top">
        <br>
            <div class="section-header">
                <span class="text"><b> <?php xl("Encounter Forms Single Page View", "e" )?></b></span>
            </div>
        <br>

        <?php
            $sql_pname = sqlStatement("SELECT CONCAT(lname,' ',fname) AS pname FROM  patient_data  WHERE pid=$pid");
            $res_row1   = sqlFetchArray($sql_pname);
            echo "<b>Patient Name: </b>".$res_row1['pname'];
        //    echo "<b>Encounter: </b>".$encounter;
        //    echo "<br /><br />";
            ?>

            <form id="patient_single_view" name="patient_single_view">
                <select id='encounter_dropdown' name='encounter_dropdown'>
                    <option value=''>Select Encounter</option>
                    <?php 

                    $get_encounter_list = sqlStatement("SELECT DATE_FORMAT(fe.date,'%Y-%m-%d') as date ,f.encounter, opc.pc_catname FROM forms f "
                                                        . "INNER JOIN form_encounter fe ON fe.pid=f.pid AND fe.id=f.form_id AND fe.encounter = f.encounter "
                                                        . "INNER JOIN openemr_postcalendar_categories opc ON opc.pc_catid = fe.pc_catid "
                                                        . "WHERE f.pid=$pid AND deleted=0 AND formdir='newpatient' ORDER BY f.id desc");
                    while($set_encounter_list = sqlFetchArray($get_encounter_list)){
                        echo "<option value='".$set_encounter_list['encounter']."' ";
                        if($encounter == $set_encounter_list['encounter']) echo "selected ";
                        echo ">".$set_encounter_list['date']."-".$set_encounter_list['pc_catname']." </option>";
                    }
                    ?>
                </select>
                <div class="loader" style="display:none"></div>
                <div id='singleView_result' name='singleView_result'>

                </div>
            </form> 
        <script type="text/javascript" src="../../library/dynarch_calendar.js"></script>
        <?php include_once("{$GLOBALS['srcdir']}/dynarch_calendar_en.inc.php"); ?>
        <script type="text/javascript" src="../../library/dynarch_calendar_setup.js"></script>
        <link rel="stylesheet" href="css/singleviewstyle.css" type="text/css"/>
        <link rel="stylesheet" href="../themes/style_oemr.css" type="text/css">

        <style type="text/css">@import url(../../library/dynarch_calendar.css);</style>
        <link rel="stylesheet" type="text/css" href="../../library/js/fancybox/jquery.fancybox-1.2.6.css" media="screen" />
</body>
</html>


