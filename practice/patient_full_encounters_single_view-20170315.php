<?php
require_once("verify_session.php");
 
if(isset($_SESSION['portal_username']) !=''){
    $provider    = $_SESSION['portal_username'];
    $refer       = $_REQUEST['refer'];
    
    if($_REQUEST['refer']!='')
        $_SESSION['refer'] = $_REQUEST['refer'];
}else {
    $provider                    = $_REQUEST['provider'];
    $_SESSION['portal_username'] = $_REQUEST['provider'];
    //for logout
    $refer                       = $_REQUEST['refer'];
    if($_REQUEST['refer']!='')
        $_SESSION['refer'] = $_REQUEST['refer'];
}

require_once("../interface/globals.php");


$pid                    = $_REQUEST['pid'];
$encounter              = $_REQUEST['encounter'];

?>
<!--    <div class="section-header">
        <span class="text"><b> <?php //xl("Encounter Forms", "e" )?></b></span>
    </div>-->
<style>

.section-header {
    border-bottom: 1px solid;
    margin-bottom: 5px;
    width: 100%;
}
div.tab {
    background: #ffffff none repeat scroll 0 0;
    margin-bottom: 10px;
    min-height: 180px;
    width: 100%;
}
@keyframes spinner {
to {transform: rotate(360deg);}
}

@-webkit-keyframes spinner {
to {-webkit-transform: rotate(360deg);}
}

.spinner {
    min-width: 24px;
    min-height: 24px;
}

.spinner:before {
    content: 'Loading…';
    position: absolute;
    width: 70px;
    height: 70px;
    margin-top: -10px;
    margin-left: -10px;
}

.spinner:not(:required):before {
    content: '';
    border-radius: 50%;
    border: 4px solid transparent;
    border-top-color: #03ade0;
    border-bottom-color: #03ade0;
    animation: spinner .8s ease infinite;
    -webkit-animation: spinner .8s ease infinite;
}
#mySpinner {
    left: 48%;
    position: absolute;
    top: 25px;
    display: none;
}
#mySpinner > div {
    margin-left: -4px;
    padding-top: 14px;
}
</style>
<script>
    $(document).ready(function(){
        var encounter_check = '<?php echo $encounter; ?>';
        if(encounter_check)
            ajaxEncounterCall(encounter_check);
        $('#encounter_dropdown').bind("change", function() {
            var encounter = $('#encounter_dropdown option:selected').val();
            $('.xdsoft_datetimepicker').css("display","none");
            //var popup = window.open("single_view_form.php?encounter="+encounter+"&pid="+"<?php echo $pid; ?>"+"&isFromCharts=1",'singleviewpop','width=900,height=500,scrollbars=yes,resizable=yes');
            ajaxEncounterCall(encounter);
        });
            
    });
    function ajaxEncounterCall(encounter){
        $("#singleView_result").html("");    
        if(encounter != ''){
            $("#mySpinner").show();
            var fieldname   = {encounter:encounter,pid:'<?php echo $pid; ?>',isFromCharts: 1,refer:'<?php echo $_SESSION['refer']; ?>'};
            $.ajax({
                type: "GET",
                url: "charts_single_view_form.php",
                data: fieldname,
                success: function(data, textStatus) {
                    $("#mySpinner").hide();
                    $("#singleView_result").show();    
                    $("#singleView_result").html(data);    
                },
                error: function(jqXHR, exception){
                    alert("failed" + jqXHR.responseText);
                }    
            });
        }else{
            $("#singleView_result").hide();    
        }
    }
</script>
<?php
    $sql_pname = sqlStatement("SELECT CONCAT(lname,' ',fname) AS pname,DATE_FORMAT(DOB,'%m-%d-%Y') as DOB,DATE_FORMAT(FROM_DAYS(DATEDIFF(CURDATE(),DOB)), '%Y')+0 AS age,sex FROM  patient_data  WHERE pid=$pid");
    $res_row1   = sqlFetchArray($sql_pname);
    echo '<span style="font-size:15px"><b>DOB:</b>'.$res_row1['DOB']."&nbsp;&nbsp;<b>AGE:</b>".$res_row1['age']."&nbsp;&nbsp;<b>GENDER:</b>".$res_row1['sex']."</span><br>";
    echo "<br />";
    ?>

    <form id="patient_single_view" name="patient_single_view">
        <label> Encounter:  </label>
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
        <div id="mySpinner" class="spinner"><div>Loading…</div></div>
        <div id='singleView_result' name='singleView_result'>
            
        </div>
    </form> 
<script type="text/javascript" src="../library/dynarch_calendar.js"></script>
<?php include_once("{$GLOBALS['srcdir']}/dynarch_calendar_en.inc.php"); ?>
<script type="text/javascript" src="../library/dynarch_calendar_setup.js"></script>

<!-- include support for the list-add selectbox feature -->
<?php include $GLOBALS['fileroot']."/library/options_listadd.inc"; ?>
</body>

</html>
