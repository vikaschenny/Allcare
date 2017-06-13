<?php

//SANITIZE ALL ESCAPES
$sanitize_all_escapes=true;

//STOP FAKE REGISTER GLOBALS
$fake_register_globals=false;

include_once("../../globals.php");
include_once("$srcdir/api.inc");
require_once("$srcdir/patient.inc");
require_once("$srcdir/options.inc.php");
require_once("$srcdir/htmlspecialchars.inc.php");
include_once("$srcdir/acl.inc");
formHeader("Form:Audit Form");
$returnurl = $GLOBALS['concurrent_layout'] ? 'encounter_top.php' : 'patient_encounter.php'; 
$formid = 0 + (isset($_GET['id']) ? $_GET['id'] : '');
$obj = $formid ? formFetch("tbl_form_audit", $formid) : array();
//$audit_data = unserialize($obj{"audit_data"});
$audit_data2 = preg_replace_callback ( '!s:(\d+):"(.*?)";!',
    function($match) {
        return ($match[1] == strlen($match[2])) ? $match[0] : 's:' . strlen($match[2]) . ':"' . $match[2] . '";';
    },
$obj{"audit_data"} );
    
$audit_data = unserialize($audit_data2);
$pid = $_SESSION['pid'];
//echo "<pre>"; print_r($audit_data); echo "</pre>";
?>
<html>
<head> 
<?php html_header_show();?>
<script type="text/javascript" src="../../../library/dialog.js"></script>
<!-- pop up calendar -->
<style type="text/css">@import url(<?php echo $GLOBALS['webroot'] ?>/library/dynarch_calendar.css);</style>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/dynarch_calendar.js"></script>
<?php include_once("{$GLOBALS['srcdir']}/dynarch_calendar_en.inc.php"); ?>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/dynarch_calendar_setup.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/textformat.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/dialog.js"></script>
<link rel="stylesheet" href="<?php echo $css_header;?>" type="text/css">
<style>#history_unobtainable{background-color: #CCC; border: 1px solid #000; width: 50%; height: auto; top: 70%; left:25%;position:absolute;}</style>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
<script language="javascript">
    $.noConflict();
    function checkbox_selected(field, group, buttonvalue){
        if(buttonvalue === '9' && field.trim() === 'h_hpi' ){
            if ( jQuery("#Audit_HPI_Options9").is(':checked') && jQuery("#checkbox1History").is(':checked') === true  ){
                jQuery("#history_unobtainable").show();
                jQuery("#hidden1History1History3").show();
                jQuery("#hidden1History1History").hide();
            }else{
                jQuery("#history_unobtainable").hide();
                jQuery("#hidden1History1History").show();
                jQuery("#hidden1History1History3").hide();
            }
        }else if(field === 'history_unobtainable_radio'){
            jQuery("#hidden1History1History").hide();
            jQuery("#hidden1History1History3").show();
            jQuery("#hidden1History1History3").html(jQuery('input[name=history_unobtainable_radio]:radio:checked').val().replace(/_/g, ' '));
        }else{
            var hiddenspanfield = '';
            var checkvalue_uncheck = 0;
            if(jQuery("#"+field  +" input:checkbox:checked").prop('checked') == true)
                checkvalue_uncheck = 1;
            hiddenspanfield = jQuery("#hidden"+group.trim()+group.trim()).html();
            if(field.trim() === 'h_cc'){
                var cc_val = 0;
                if(jQuery('[name=Audit_CC_Optionsradio]:checked').length){
                    cc_val = jQuery('input:radio[name=Audit_CC_Optionsradio]:checked').val();
                }
                var field_count = cc_val;
                var group_count = jQuery("#"+group+" input:checkbox:checked").length;
            }else if(field.trim() === 'h_pfsh'){
                var count_pfsh = 0;
                jQuery('div',"#h_pfsh").each(function(){
                    if(jQuery('#'+jQuery(this).attr('id') +" input:checkbox:checked").length>=1)
                        count_pfsh = count_pfsh +1;
                });
                var field_count = count_pfsh;
                var group_count = count_pfsh;
            }else{
                var field_count = jQuery("#"+field+" input:checkbox:checked").length;
                var group_count = jQuery("#"+group+" input:checkbox:checked").length;
            }
            var dataarray = [];
            var dataarray2 = [];
            var dataarray3 = [];
            var dataarray4 = [];
            var countdivs = 0;
            var grp = group.substring(1).trim();
            if(grp === 'Exam'){
                jQuery('div',"#"+group).each(function(){
                    dataarray.push(jQuery(this).attr('id')); 
                    var divid = '#'+jQuery(this).attr('id');
                    if(jQuery(this).parent().attr('id').substring(1).startsWith("_") === true){
                        dataarray.push(jQuery( '#'+jQuery(this).attr('id') +" input:checkbox:checked").length);
                        var checkbox = jQuery( '#'+jQuery(this).attr('id') +" input:checkbox").length;
                        var checked_checkbox = jQuery( '#'+jQuery(this).attr('id') +" input:checkbox:checked").length;
                        var unchecked = checkbox - checked_checkbox;
                        dataarray.push(unchecked);
                        //var numberNotChecked = jQuery('input:checkbox:not(":checked")').length;
                    }else{
                        dataarray.push(jQuery('#'+jQuery(this).attr('id') +" input:checkbox:checked").length);
                        var checkbox = jQuery('#'+jQuery(this).attr('id') +" input:checkbox").length;
                        var checked_checkbox = jQuery('#'+jQuery(this).attr('id') +" input:checkbox:checked").length;
                        var unchecked = checkbox - checked_checkbox;
                        dataarray.push(unchecked);
                        //var numberNotChecked = $('input:checkbox:not(":checked")').length
                    }    
                    countdivs  = countdivs + 1;
                });
            }else if(grp === 'Decision'){
                dataarray2.push(buttonvalue);
                var htmlcontent = jQuery('#hidden'+field).text();
                dataarray2.push(htmlcontent);
                jQuery('div',"#"+group).each(function(){
                    if(jQuery(this).attr('id').substring(1).startsWith("_") === true){
                        dataarray4.push(jQuery(this).attr('id'));
                        dataarray4.push(jQuery('#hidden'+jQuery(this).attr('id')).text());
                        if(jQuery(this).attr('id') === 'd_risk'){
                            var divid = jQuery(this).attr('id');
                            dataarray.push(divid); 
                            dataarray.push(jQuery('#hiddend_risk').text());
                        }else if(jQuery(this).attr('id') === 'd_acd'){
                            jQuery('#d_acd input:checked').each(function() {
                                dataarray3.push(this.id);
                            });
//                            jQuery("#d_acd").children('input:checked').attr('checked', true); 
                        }else{
                            jQuery('#d_dmo select').each(function () {                                  
                               dataarray3.push(jQuery(this).attr('id')); 
                               dataarray3.push(jQuery(this).prop("selectedIndex"));
                            });
                            //dataarray4.push(jQuery('#'+jQuery(this).attr('id') +" input:checkbox:checked").val);
                        }
                    }   
                    countdivs  = countdivs + 1;
                });
            }else{
                jQuery('div',"#"+group).each(function(){
                    var cc_value = 0;
                    dataarray.push(jQuery(this).attr('id')); 
                    var divid = '#'+jQuery(this).attr('id');
                    if(jQuery(this).attr('id') === 'h_cc'){
                        if(jQuery('[name=Audit_CC_Optionsradio]:checked').length){
                            cc_value = jQuery('input:radio[name=Audit_CC_Optionsradio]:checked').val();
                        }
                        dataarray.push(cc_value);
                    }else if(jQuery(this).attr('id') === 'h_pfsh'){
                        var count_pfsh = 0;
                        jQuery('div',"#h_pfsh").each(function(){
                            if(jQuery('#'+jQuery(this).attr('id') +" input:checkbox:checked").length>=1)
                                count_pfsh = count_pfsh +1;
                        });
                        //dataarray.push(jQuery(this).attr('id')); 
                        dataarray.push(count_pfsh);
                    }else{
                        dataarray.push(jQuery('#'+jQuery(this).attr('id') +" input:checkbox:checked").length);
                    }
                    countdivs  = countdivs + 1;
                });
            }
            //alert(dataarray);
            var fieldname  = {hiddenspanfield:hiddenspanfield,field:field ,group:group ,field_count:field_count, group_count:group_count,dataarray:dataarray, countdivs:countdivs,dataarray2:dataarray2,dataarray3:dataarray3,dataarray4:dataarray4,checkvalue_uncheck:checkvalue_uncheck};
            jQuery.ajax({
                type: "POST",
                url: "/interface/forms/auditform/check_lookup.php",
                data: fieldname,
                dataType : "json",
                success: function(data) {
                    var dataresult = data +' ';
                    var res = dataresult.split(',');
                    jQuery('#hidden'+field).html(res[0].replace(/_/g, ' '));
                    if ( jQuery("#Audit_HPI_Options9").not(':checked')  && group.trim() === '1History'){
                        jQuery('#hidden1History1History').html(res[1].replace(/_/g, ' '));
                        jQuery("#hidden1History1History").show();
                    }else{
                        jQuery('#hidden'+group.trim()+group.trim()).html(res[1].replace(/_/g, ' '));
                    }
                    jQuery('#hidden'+field.trim()+"2").val(res[0].replace(/_/g, ' '));
                    var check = jQuery('#hidden'+group.trim()+"2").val(res[1].replace(/_/g, ' '));
                }
            });
        }
    }
    function calculatecpotime(){
        var timeval = 0;
        var defaultcpo = jQuery("#defaultcpo").val();
        var cpolog = jQuery("#cpolog").html();
        if(defaultcpo != '')
            timeval = defaultcpo - cpolog;
        jQuery("#totalcpotime").html(timeval);
    }
    function calculateccmtime(){
        var timeval = 0;
        var defaultccm = jQuery("#defaultccm").val();
        var ccmlog = jQuery("#ccmlog").html();
        if(defaultccm != '')
            timeval = defaultccm - ccmlog;
        jQuery("#totalccmtime").html(timeval);
    }
    function checkbox_disable(){
        jQuery('#Audit_CC_Optionscheckbox').change(function() {
            if (this.checked) {
                jQuery('#Audit_CC_Optionstextarea').attr('readonly', false);
                jQuery('#Audit_CC_Optionstextarea').css("background-color", "#FFFFFF");
            }else{
                jQuery('#Audit_CC_Optionstextarea').attr('readonly', true);
                jQuery('#Audit_CC_Optionstextarea').css("background-color", "#D8D8D8");
            }
        });
    }
   function showDiv (fieldname) { 
        if(jQuery("#checkbox"+fieldname).is(':checked')){ 
            jQuery("#"+fieldname).show();  
        }else{ 
            jQuery("#"+fieldname).hide(); 
        }   
        if(jQuery("#Audit_HPI_Options9").is(':checked') === true && jQuery("#checkbox1History").is(':checked') === true ){
            jQuery("#history_unobtainable").show();
        }else{
            jQuery("#history_unobtainable").hide();
        }
    }
    function myFunction() {
        var txt;
        var r = confirm("Please Confirm Audit Code..!");
        if (r == true) {
            return true;
        } else {
            return false;
        }
    }
    function audit_form_check(){
        var hiddengroup = jQuery("#hiddengroup").val();
        var exploded_group = hiddengroup.split(',');
        var group_data;
        var exam_check = [];
        var history_data;
        var risk_data;
        for(var $i=0; $i<exploded_group.length-1; $i++){
            if(exploded_group[$i].substring(1).trim() === 'Exam'){
                jQuery('div',"#"+exploded_group[$i]).each(function(){
                    var check_condition = jQuery(this).attr('id').substring(1).startsWith("_");
                    if(check_condition === true){
                        if(jQuery('#hidden'+jQuery(this).attr('id')).text() === 'PROBLEM FOCUSED'){
                            exam_check.push(1);
                        }else if(jQuery('#hidden'+jQuery(this).attr('id')).text() === 'EXP PROB FOCUSED'){
                            exam_check.push(2);
                        }else if(jQuery('#hidden'+jQuery(this).attr('id')).text() === 'DETAILED'){
                            exam_check.push(3);
                        }else if(jQuery('#hidden'+jQuery(this).attr('id')).text() === 'COMPREHENSIVE'){
                            exam_check.push(4);
                        }
                    }
                });
            }else{
                if ( jQuery("#Audit_HPI_Options9").is(':checked') === true && exploded_group[$i] === '1History'){
                    var hgroup = exploded_group[$i]+exploded_group[$i]+"3";
                }else {
                    var hgroup = exploded_group[$i]+exploded_group[$i];
                }
                if(hgroup === '1History1History' || hgroup === '1History1History3'){
                    if(jQuery('#hidden'+hgroup).text().trim() === 'PROBLEM FOCUSED' || jQuery('#hidden'+hgroup).text().trim() === 'Problem Focused'){
                        history_data = 'PF';
                    }else if(jQuery('#hidden'+hgroup).text().trim() === 'EXP PROB FOCUSED' || jQuery('#hidden'+hgroup).text().trim() === 'Expanded Problem Focused'){
                        history_data = 'EPF';
                    }else if(jQuery('#hidden'+hgroup).text().trim() === 'DETAILED' || jQuery('#hidden'+hgroup).text().trim() === 'Detailed'){
                        history_data = 'D';
                    }else if(jQuery('#hidden'+hgroup).text().trim() === 'COMPREHENSIVE' || jQuery('#hidden'+hgroup).text().trim() === 'Comprehensive'){
                        history_data = 'C';
                    }else{
                        history_data = '';
                    }
                }
                if(hgroup === '3Decision3Decision'){
                    if(jQuery('#hidden'+exploded_group[$i]+exploded_group[$i]).text().trim() === 'STRAIGHT FORWARD'){
                        risk_data = 'SF';
                    }else if(jQuery('#hidden'+exploded_group[$i]+exploded_group[$i]).text().trim() === 'LOW COMPLEX'){
                        risk_data = 'L';
                    }else if(jQuery('#hidden'+exploded_group[$i]+exploded_group[$i]).text().trim() === 'MODERATE COMPLEX'){
                        risk_data = 'M';
                    }else if(jQuery('#hidden'+exploded_group[$i]+exploded_group[$i]).text().trim() === 'HIGH COMPLEX'){
                        risk_data = 'H';
                    }else{
                        risk_data = '';
                    }
                }
            }    
        }
        
        var exam_group_value ;
        exam_check.reverse();
        
        if(exam_check[0] === 1){
            exam_group_value = 'PF';
        }else if(exam_check[0] === 2){
            exam_group_value = 'EPF';
        }else if(exam_check[0] === 3){
            exam_group_value = 'D';
        }else if(exam_check[0] === 4){
            exam_group_value = 'C';
        }else{
            exam_group_value = '';
        }
        var audit_time = jQuery('#audit_time').val();
//        if(audit_time === '') audit_time = 0;
        var selected_data       = '';
        var check_audit_time    = "T:"+audit_time;
        
        selected_data   = 'H:'+history_data+';E:'+exam_group_value+';D:'+risk_data;
        
        var title       = jQuery('#cpt_data option:selected').val();
        var encounter   = jQuery('#encounter').val();
        var pid         = <?php echo $pid; ?>;
        jQuery.ajax({
            type: "POST",
            url: "/interface/forms/auditform/check_audit.php",
            data: {selected_data:selected_data,posted_title:title,check_audit_time:check_audit_time,encounter:encounter,pid:pid},
            dataType : "json",
            success: function(data) {
                var dataresult = data +' ';
                var res = dataresult.split(',');
                jQuery("#hiddenaudit" ).html("CPT Code:"+res[0]);
                jQuery("#hiddenaudit2" ).val("CPT Code:"+res[0]);
                alert(res[0]);
            }
        });
    }

    jQuery(document).ready(function() {
        calculatecpotime();
        calculateccmtime();
        jQuery(':checkbox').change(function() {
            if ( ! this.checked) {
                if(jQuery(this).parent().parent().attr('id') == 'd_risk'){
                    jQuery("#hidden"+jQuery(this).parent().parent().attr('id') ).html('');
                    var selected = [];
                    jQuery('input:checked').each(function() {
                        selected.push(jQuery(this).attr('id'));
                    });
                    var group = jQuery(this).parent().parent().parent().attr('id');
                    var dataarray = [];
                    jQuery('div',"#"+group).each(function(){
                        var check_variable = jQuery(this).attr('id').substring(1).startsWith("_");
                        if(check_variable === true){
                            var divid = jQuery(this).attr('id');
                            dataarray.push(divid); 
                            dataarray.push(jQuery('#hidden'+divid).text());
                        }   
                    });

                    jQuery.ajax({
                        type: "POST",
                        url: "/interface/forms/auditform/check_risk.php",
                        data: {selected:selected},
                        dataType : "json",
                        success: function(data) {
                            var dataresult = data +' ';
                            var res = dataresult.split(',');
                            jQuery("#hiddend_risk" ).html(res[0].replace(/_/g, ' '));
                            //alert(res[0]);
                            jQuery("#hidden3Decision3Decision").html(res[1].replace(/_/g, ' '));
                            jQuery("#hiddend_risk2" ).val(res[0].replace(/_/g, ' '));
                            jQuery("#hidden3Decision2").val(res[1].replace(/_/g, ' '));
                        }
                    });
                }
                return false;
            }
        });
    });
    function getQdiv(encounter){
        var visitT = jQuery('#cpt_data option:selected').val();
        jQuery.ajax({
            type: "POST",
            url: "/interface/forms/auditform/check_questions_list.php",
            data: {visitT:visitT,encounter:encounter},
            dataType : "json",
            success: function(data) {
                var dataresult = data +' ';
                var res = dataresult.split(',');
                var result = res[0].split(',')
                if(res[0].trim() === 'None'){
                    jQuery("#incident_to" ).hide();
                    jQuery("#interactive_complexity" ).hide();
                }else{
                    if(res[0] === 'Interactive Complexity' || res[1].trim() === 'Interactive Complexity'){
                        jQuery("#interactive_complexity" ).show();
                    }
                    if(res[1].trim() === 'Incident To' || res[0].trim() === 'Incident To'){
                        jQuery("#incident_to" ).show();
                    }
                }
                
            }
        });
    }
    
</script>
</head>
<body class="body_top">
    <p><span class="forms-title"><b><?php echo xlt('Audit Form'); ?></b></span></p>
    <?php
    // Define Visit Type
    $visitQ = sqlStatement("SELECT DISTINCT(lp.title) as title FROM list_options lp 
                            INNER JOIN billing b ON lp.option_id = b.code
                            WHERE lp.list_id='Level_Of_Service' AND b.code_type = 'CPT4' AND b.encounter =".$encounter);
    $visitT = "";
    while($visitRow = sqlFetchArray($visitQ)){
        $visitT = $visitRow['title'];
    }
    echo "<form method='post' name='my_form' id='my_form' action='$rootdir/forms/auditform/save.php?id=" . attr($formid) ."'>\n";
    $get_cpt_table_data = sqlStatement("SELECT DISTINCT(title) as title FROM list_options WHERE list_id='Level_Of_Service'");
    echo "<b>&nbsp;&nbsp;&nbsp;&nbsp;    Audit Visit category:  </b>";
    ?><select id='cpt_data' name='cpt_data' required onchange='getQdiv("<?php echo $encounter; ?>");'> <?php
    echo "<option value=''></option>";
    while($set_cpt_table_data = sqlFetchArray($get_cpt_table_data)){
        echo "<option value='".$set_cpt_table_data['title']."' ";
        if(count($audit_data) == 1):
            if($visitT == $set_cpt_table_data['title']):
               echo 'selected'; 
            endif;
        else:    
            if($audit_data['cpt_data'] === $set_cpt_table_data['title'])
                echo 'selected';
        endif;
        echo " >" . $set_cpt_table_data['title']."</option>";
    }
    echo "</select>";
    echo "<input type='hidden' name='hiddenaudit2' id='hiddenaudit2' value='".$audit_data['hiddenaudit']."' />";
    echo "<input type='hidden' name='encounter' id='encounter' value='".$_SESSION['encounter']."' />";
    echo  "&nbsp;&nbsp;&nbsp;&nbsp;<h3><span style='color:red' align='right' id='hiddenaudit' name='hiddenaudit' >". $audit_data['hiddenaudit']."</span></h3>";
    ?> 
    <div id="interactive_complexity" name='interactive_complexity' style="padding-left: 20px;display: none" >
        <label> <b><u>Interactive Complexity </u></b></label>
        <table>
            <?php 
            $get_ic_table_data = sqlStatement("SELECT DISTINCT(title) as title, option_id FROM list_options WHERE list_id='Interactive_Complexity'");
            while($set_ic_table_data = sqlFetchArray($get_ic_table_data)){
                echo "<tr>";
                echo "<td width='70%'>".$set_ic_table_data['title']."</td>";
                echo "<td width='30%'><input type='radio' name ='ic".$set_ic_table_data['option_id']."' value='Y'"; 
                if($audit_data["ic".$set_ic_table_data['option_id']] == 'Y' ) 
                   echo " checked";   
                echo " > Yes" ;
                echo "<input type='radio' name ='ic".$set_ic_table_data['option_id']."' value='N'";
                if($audit_data["ic".$set_ic_table_data['option_id']] == 'N' ) 
                   echo " checked";  
                echo " > No </td>";
                echo "</tr>";
            }
            ?>
        </table>
    </div>
    <div id="incident_to" name='incident_to' style="padding-left: 20px; display: none" >
        <label> <b><u>Incident To </u></b></label>
        <table>
            <?php 
            $get_it_table_data = sqlStatement("SELECT DISTINCT(title) as title, option_id FROM list_options WHERE list_id='Incident_To'");
            while($set_it_table_data = sqlFetchArray($get_it_table_data)){
                echo "<tr>";
                echo "<td width='70%'>".$set_it_table_data['title']."</td>";
                echo "<td width='30%'><input type='radio' name ='it".$set_it_table_data['option_id']."' value='Y'"; 
                if($audit_data["it".$set_it_table_data['option_id']] == 'Y' ) 
                   echo " checked";   
                echo " > Yes" ;
                echo "<input type='radio' name ='it".$set_it_table_data['option_id']."' value='N'";
                if($audit_data["it".$set_it_table_data['option_id']] == 'N' ) 
                   echo " checked";  
                echo " > No </td>";
                echo "</tr>";
            }
            ?>
        </table>
    </div>
    <div id="history_unobtainable" name ="history_unobtainable"  style="display:none;" align="center">
        <label> Reason For Unobtainable History<br> </label> 
        <textarea  cols='70' id='history_unobtainable_textarea' name='history_unobtainable_textarea' ><?php echo $audit_data["history_unobtainable_textarea"] ; ?></textarea><br>
        <div align = 'left'>
        <label><input type='radio' id='history_unobtainable_radio' name='history_unobtainable_radio' value= 'Problem_Focused' onclick='checkbox_selected("history_unobtainable_radio");' 
        <?php if($audit_data["history_unobtainable_radio"] == 'Problem_Focused' ) 
            echo " checked";  
        ?>
        > Problem Focused</label><br>
        <label><input type='radio' id='history_unobtainable_radio' name='history_unobtainable_radio' value= 'Expanded_Problem_Focused' onclick='checkbox_selected("history_unobtainable_radio");' 
        <?php if($audit_data["history_unobtainable_radio"] == 'Expanded_Problem_Focused' ) 
            echo " checked";  
        ?>
        > Expanded Problem Focused</label><br>
        <label><input type='radio' id='history_unobtainable_radio' name='history_unobtainable_radio' value= 'Detailed' onclick='checkbox_selected("history_unobtainable_radio");'
        <?php if($audit_data["history_unobtainable_radio"] == 'Detailed' ) 
            echo " checked";  
        ?>> Detailed</label><br>
        <label><input type='radio' id='history_unobtainable_radio' name='history_unobtainable_radio' value= 'Comprehensive' onclick='checkbox_selected("history_unobtainable_radio");' 
        <?php if($audit_data["history_unobtainable_radio"] == 'Comprehensive' ) 
            echo " checked";  
        ?>
        > Comprehensive</label><br>
        </div>
    </div>
    <table border ="1" style="border-collapse: collapse;">
        <?php
        $hiddengroup = '';
        // group names
        $get_group_names = sqlStatement("SELECT DISTINCT group_name FROM layout_options WHERE form_id='AUDITFORM' ORDER BY group_name");
        while($set_group_names = sqlFetchArray($get_group_names)){
            $groupname_checkbox = $set_group_names["group_name"];
            //echo "<input type='checkbox' id='checkbox".$groupname_checkbox."' onclick= 'showDiv();'><b>".substr($groupname_checkbox,1)."</b>";
            ?><label><input type='checkbox' id='checkbox<?php echo $set_group_names["group_name"]; ?>' onclick= "showDiv('<?php echo $groupname_checkbox; ?>')" ><b> <?php echo substr($groupname_checkbox,1); ?> </b></label><?php
            echo "<input type='hidden' name='hidden".$groupname_checkbox."2' id='hidden".$groupname_checkbox."2' value='".$audit_data['hidden'.$groupname_checkbox]."'>";
            echo "&nbsp;&nbsp;&nbsp;<b><span id='hidden".$set_group_names['group_name'].$groupname_checkbox."' name='hidden".$groupname_checkbox."' style='color:red'>";
            if(!empty($audit_data['hidden'.$groupname_checkbox]))
                echo $audit_data['hidden'.$groupname_checkbox];
            else
                echo " ";
            echo "</b></span>";
            if($groupname_checkbox == '1History' ){
                echo "<label><input type='hidden' name='hidden".$groupname_checkbox."4' id='hidden".$groupname_checkbox."4' value='".$audit_data['hidden'.$groupname_checkbox]."3'></label>";
                echo "&nbsp;&nbsp;&nbsp;<b><span id='hidden".$set_group_names['group_name'].$groupname_checkbox."3' name='hidden".$groupname_checkbox."3' style='color:red; display:none;'>";
                if(!empty($audit_data['hidden'.$groupname_checkbox]))
                    echo $audit_data['hidden'.$groupname_checkbox];
                else
                    echo " ";
                echo " </b></span>";
            }
                    
            $group_id = $groupname_checkbox;
            $hiddengroup .= $group_id.",";
            echo "<div id='".$groupname_checkbox."' name = '".$groupname_checkbox."' style='border:1px solid black;margin: 0px 20px 5px 20px;display:none;'>";
                $get_field_names = sqlStatement("SELECT field_id, list_id, title FROM layout_options WHERE form_id='AUDITFORM' and group_name='".$groupname_checkbox."' order by seq");
                while ($set_field_names = sqlFetchArray($get_field_names)){
                    ?><label><input type='checkbox' id='checkbox<?php echo $set_field_names["field_id"]; ?>'  onclick= "showDiv('<?php echo $set_field_names["field_id"]; ?>')" ><?php
                    echo "<b>".$set_field_names['title']."</label></b>";
                    echo "<label><input type='hidden' name='hidden".$set_field_names['field_id']."2' id='hidden".$set_field_names['field_id']."2' value='".$audit_data['hidden'.$set_field_names['field_id']]."' >";
                    echo "&nbsp;&nbsp;&nbsp;<span id='hidden".$set_field_names['field_id']."'  name='hidden".$set_field_names['field_id']."' style='color:red'>";
                    if(!empty($audit_data['hidden'.$set_field_names['field_id']]))
                            echo $audit_data['hidden'.$set_field_names['field_id']];
                    else
                        echo " ";
                    echo "</span></label>";
                    $field_id = $set_field_names['field_id'];
                    $list_id = $set_field_names['list_id'];
                    echo "<div id='".$set_field_names['field_id']."' name = '".$set_field_names['field_id']."' style='border:1px solid black;margin: 0px 20px 10px 20px;'>";
                        $getchecklist = sqlStatement("SELECT title, option_id FROM list_options WHERE list_id = '".$list_id."'  and option_id LIKE '%\_%' order by seq");
                        if(mysql_num_rows($getchecklist) > 0){ 
                            $get_list_fields = sqlStatement("SELECT title, option_id FROM list_options WHERE list_id = '".$list_id."'  and option_id NOT LIKE '%\_%' order by seq");
                            while($set_list_fields = sqlFetchArray($get_list_fields)){
                                echo "<b>".$set_list_fields['title']."</b>";
                                $option_id = $set_list_fields['option_id'];
                                $list_title = $set_list_fields['title']; 
                                echo "<div id='".$groupname_checkbox.$option_id."' name='".$groupname_checkbox.$option_id."' style='border:1px solid black;margin: 0px 20px 5px 20px;'>";
                                $get_list_fields_value = sqlStatement("SELECT title, option_id FROM list_options WHERE list_id = '".$list_id."'  and option_id LIKE '".$option_id."\_%' order by seq");
                                while($set_list_fields_value = sqlFetchArray($get_list_fields_value)){
                                    ?><!--<input type='checkbox' id= '<?php //echo $list_id.$set_list_fields_value['option_id']; ?>' name='<?php //echo $list_id.$set_list_fields_value['option_id']; ?>' value= '<?php //echo $set_list_fields_value['option_id']; ?>' onclick='checkbox_selected("<?php //echo $field_id ?> ", "<?php //echo $group_id; ?> " , "<?php //echo $set_list_fields_value['option_id']; ?>");' <?php //if($set_list_fields_value['option_id'] == $audit_data[$set_group_names['group_name']][$list_id.$set_list_fields_value['option_id']]) echo checked; ?>>--><?php //echo $set_list_fields_value['title']."</br>";
                                    ?><label><input type='checkbox' id= '<?php echo $list_id.$set_list_fields_value['option_id']; ?>'  name='<?php echo $list_id.$set_list_fields_value['option_id']; ?>' value= '<?php echo $set_list_fields_value['option_id']; ?>' onclick='checkbox_selected("<?php echo $field_id ?> ", "<?php echo $group_id; ?> " , "<?php echo $set_list_fields_value['option_id']; ?>");' <?php if($set_list_fields_value['option_id'] == $audit_data[$set_group_names['group_name']][$list_id.$set_list_fields_value['option_id']]) echo checked; ?>> <?php echo $set_list_fields_value['title']."</label></br>";
                                }
                                echo "</div>";
                            }
                        }else{ 
                            $get_list_fields = sqlStatement("SELECT title, option_id,list_id, notes FROM list_options WHERE list_id = '".$list_id."' order by seq");
                            while($set_list_fields = sqlFetchArray($get_list_fields)){
                                $option_id = $set_list_fields['option_id'];
                                $list_title = $set_list_fields['title']; 
                                if( $list_id == 'Diagnosis_Management_Options'){
                                    echo "&nbsp;&nbsp;"
                                    ?> 
                                    <select id='<?php echo $option_id; ?>' name='<?php echo $list_id.$option_id; ?>'  onchange='checkbox_selected("<?php echo $field_id ?> ", "<?php echo $group_id; ?> ", "" );'>
                                        <?php
                                            $explodenotes1 = explode(';', $set_list_fields['notes']);
                                            $explodenotes = explode(',', $explodenotes1[0]);
                                            echo "<option value=''> </option>";
                                            for($i=0; $i<count($explodenotes); $i++){
                                                ?><option value='<?php echo $explodenotes[$i]; ?>' <?php if($explodenotes[$i] == $audit_data[$groupname_checkbox][$list_id.$option_id]) echo selected; ?>><?php echo $explodenotes[$i]; ?> </option><?php
                                            }
                                        ?>
                                    </select>
                                    <?php  
                                    echo $list_title."<br><br>";
                                }else{    
                                    if( $list_id == 'Audit_CC_Options'){
                                        $check_chronic_count = 0;
                                        if($option_id != '5'){
                                            if($formid == 0){
                                                $get_cc_count = sqlStatement("SELECT chronicICD10 FROM history_data where pid = $pid ORDER BY DATE DESC LIMIT 0,1");
                                                if(mysql_num_rows($get_cc_count)){
                                                    while($set_cc_count2 = sqlFetchArray($get_cc_count)){
                                                        $set_cc_count = explode("|",$set_cc_count2['chronicICD10']);
                                                        if(!empty($set_cc_count)){
                                                            if(count($set_cc_count)>=4)
                                                                $check_chronic_count = 4;
                                                            else
                                                                $check_chronic_count = count($set_cc_count);
                                                        }
                                                    }
                                                }
                                            }else{
                                                $check_chronic_count = $audit_data[$groupname_checkbox]['Audit_CC_Optionsradio'];
                                            }
                                            ?><label><input type='radio' id='<?php echo $list_id.'radio'; ?>' name='<?php echo $list_id.'radio'; ?>' value= '<?php echo $option_id; ?>' onclick='checkbox_selected("<?php echo $field_id ?> ", "<?php echo $group_id; ?> " , "<?php echo $option_id; ?>");' <?php if($option_id == $check_chronic_count) echo checked; ?> > <?php echo $list_title."</label></br>";
                                        }else{
                                            ?><label><input 
                                                type      = 'checkbox' 
                                                id        = '<?php echo $list_id.'checkbox'; ?>' 
                                                name      = '<?php echo $list_id.'checkbox'; ?>' 
                                                value     = '<?php echo $option_id; ?>' 
                                                onchange  = 'checkbox_disable();'
                                                 <?php if($option_id == $audit_data[$groupname_checkbox][$list_id.$option_id]) 
                                                          echo checked; ?> 
                                                ><?php  
                                              echo $list_title."</label><br>&nbsp;&nbsp;"; 
                                              $cc_text = '';
                                              ?>
                                            <textarea cols ='75'  id='<?php echo $list_id.'textarea'; ?>' name='<?php echo $list_id.'textarea'; ?>' readonly="readonly" style="background-color:#D8D8D8"><?php 
                                              if($formid == 0){
                                                $get_cc_text = sqlStatement("SELECT l.field_value FROM forms f INNER JOIN lbf_data l ON l.form_id = f.form_id WHERE f.formdir =  'LBF2' AND deleted = 0 AND l.field_id = 'chief_complaint_text' AND f.encounter = $encounter  ");
                                                if(mysql_num_rows($get_cc_text)){
                                                    while($set_cc_text = sqlFetchArray($get_cc_text)){
                                                       $cc_text = $set_cc_text['field_value'];
                                                    } 
                                                    echo $cc_text ;
                                                }
                                              }else{    
                                                echo $audit_data[$list_id.'textarea']; 
                                              }
                                              ?></textarea><br><?php 
                                        }
                                    }else{
                                        if($list_id == 'Audit_HPI_Options'){
                                            $data = $audit_data[$groupname_checkbox][$list_id.$option_id];
                                            display_hpi($formid,$groupname_checkbox,$list_id,$option_id,$field_id,$list_title,$data,$encounter);
                                        }else if($list_id == 'Audit_ROS_Options'){
                                            $data = $audit_data[$groupname_checkbox][$list_id.$option_id];
                                            display_ros($formid,$groupname_checkbox,$list_id,$option_id,$field_id,$list_title,$data,$encounter);
                                        }else{
                                            $data = $audit_data[$groupname_checkbox][$list_id.$option_id];
                                            ?><label><input type='checkbox' id='<?php echo $list_id.$option_id; ?>' 
                                                name='<?php echo $list_id.$option_id; ?>' 
                                                value= '<?php echo $option_id; ?>' 
                                                onclick='checkbox_selected("<?php echo $field_id ?> ", "<?php echo $groupname_checkbox; ?> ", "<?php $option_id; ?>");'
                                                 <?php if($option_id == $data) 
                                                         echo checked; ?>  
                                            ><?php echo $list_title."</label></br>"; 
                                        }
                                    }
                                }    
                            }
                        }    
                    echo "</div></br>";
                }
            echo "</div></br>";
        }
        if(!empty($audit_data["history_unobtainable_radio"]) && !empty($audit_data['1History']['Audit_HPI_Options9'] )): ?>
            <script>
                jQuery.noConflict();
                jQuery("#hidden1History1History3").html(jQuery('input[name=history_unobtainable_radio]:radio:checked').val().replace(/_/g, ' '));
                jQuery("#hidden1History1History3").show();
                jQuery("#hidden1History1History").hide();
            </script>        
        <?php endif; ?>
        <script>
            jQuery.noConflict();
            jQuery(document).ready(function(){
                jQuery( "#my_form" ).each(function() {
                    jQuery('div').each(function(){
                        if(jQuery(this).attr('id') === '1History' || jQuery(this).attr('id') === '2Exam' ||jQuery(this).parent().attr('id')=== '2Exam' ||jQuery(this).attr('id') === '3Decision'){
                            jQuery("#"+jQuery(this).attr('id')).hide();
                            checkbox_selected(jQuery(this).attr('id'), jQuery(this).parent().attr('id') , "" );
                        }
                        if(jQuery(this).parent().attr('id') === '1History' || jQuery(this).parent().attr('id') === '3Decision'){
                            jQuery('#checkbox'+jQuery(this).attr('id')).prop('checked', true);
                            checkbox_selected(jQuery(this).attr('id'), jQuery(this).parent().attr('id') , "" );
                        }
                        
                   });
                });
                getQdiv(<?php echo $encounter; ?>);
                var myVar=setInterval(function () {getSpan()}, 1000);
            });
            function getSpan(){
                if(jQuery("#Audit_HPI_Options9").is(':checked') === true  && jQuery("#checkbox1History").is(':checked') === true){
                    jQuery("#hidden1History1History3").show();
                    jQuery("#history_unobtainable").show();
                    jQuery("#hidden1History1History").hide();
                }else if(jQuery("#Audit_HPI_Options9").is(':checked') === true  && jQuery("#checkbox1History").is(':checked') === false){
                    jQuery("#hidden1History1History3").show();
                    jQuery("#history_unobtainable").hide();
                    jQuery("#hidden1History1History").hide();
                }
            }
        </script>  
        <?php //   if(!empty($audit_data["history_unobtainable_radio"]) && !empty($audit_data['1History']['Audit_HPI_Options9'] )): ?>
                    <!--style="display:block;"--> 
<!--                    <script>alert("hi");
                        jQuery("#history_unobtainable").show();alert("bye");
                    </script>-->
        <?php //   else:   ?>
<!--                    <script>
                        jQuery("#history_unobtainable").hide();
                    </script>-->
        <?php //   endif;    ?>
    <input type='hidden' value='<?php echo $hiddengroup; ?>' name='hiddengroup' id='hiddengroup' />     
    <?php 

    $get_cpo_form_id = sqlStatement("SELECT form_id FROM forms WHERE formdir='cpo' AND encounter= $encounter AND pid= $pid AND deleted= 0 order by date desc limit 0,1");
    $cpo_form_id = 0;
    while($set_cpo_form_id = sqlFetchArray($get_cpo_form_id)){
        $cpo_form_id = $set_cpo_form_id['form_id'];
    }
    
    $get_cpo_logged_mins = sqlStatement("SELECT cpo_data FROM tbl_form_cpo WHERE id = $cpo_form_id");
    while($set_cpo_logged_mins = sqlFetchArray($get_cpo_logged_mins)){
        $get_cpo_data = $set_cpo_logged_mins['cpo_data'];
    }
    $cpo_data2 = preg_replace_callback ( '!s:(\d+):"(.*?)";!',
        function($match) {
            return ($match[1] == strlen($match[2])) ? $match[0] : 's:' . strlen($match[2]) . ':"' . $match[2] . '";';
        },
    $get_cpo_data );
    
    $cpo_data = unserialize($cpo_data2);
    $timeinterval_val2 = 0;
    for($i=1; $i<= count($cpo_data); $i++){
        if(!empty($cpo_data[$i-1])){
            foreach ($cpo_data[$i-1] as $cpokey => $cpovalue) {
                if($cpokey == 'timeinterval' && $cpovalue != ''):
                    $ures = sqlStatement("SELECT title FROM list_options WHERE option_id= '$cpovalue' and list_id = 'Time_Interval'");
                    while ($urow = sqlFetchArray($ures)) {
                        $cpotitle = $urow['title'];
                    }
                    if($cpotitle == ''):
                        $cpotitle = 0;
                    endif;
                    $timeinterval_val2 = $timeinterval_val2 + $cpotitle;
                endif;
            }
        }
    }

    $get_cpo_ccm_stat = sqlStatement("SELECT cpo,ccm FROM patient_data WHERE pid = $pid");
    while($set_cpo_ccm_stat = sqlFetchArray($get_cpo_ccm_stat)){
        $cpo_yes = $set_cpo_ccm_stat['cpo'];
        $ccm_yes = $set_cpo_ccm_stat['ccm'];
    }
    if($audit_data['defaultcpo'] == ''){
        if($cpo_yes == 'YES'){
            $cpo_default_val = 30;
        }else{
            $cpo_default_val = '';
        }
    }else{
        $cpo_default_val = $audit_data['defaultcpo'];
    }
    if($audit_data['defaultccm'] == ''){
        if($ccm_yes == 'YES'){
            $ccm_default_val = 20;
        }else{
            $ccm_default_val = '';
        }
    }else{
        $ccm_default_val = $audit_data['defaultccm'];
    }

    ?>
    <div>
        <table></table>
    </div>
    <label><input type='checkbox' id='checkboxcpo'  onclick= "showDiv('cpo')" /><b> CPO</b></label>
    <div id='cpo' style='border:1px solid black;margin: 0px 20px 10px 20px; display: none;clear:both;'>
        <table id="tablecpo">
            <tr><td>Default CPO minutes:</td> <td><input type='number' id='defaultcpo' name ='defaultcpo' value='<?php echo $cpo_default_val; ?>' onchange="calculatecpotime();"/></td></tr>
            <tr><td>Logged CPO minutes: </td> <td> <span id='cpolog' name='cpolog' ><?php if($timeinterval_val2 == '') echo 0; else echo $timeinterval_val2; ?></span></td></tr>
            <tr><td>Remaining Minutes: </td> <td><span id='totalcpotime' name='totalcpotime' ></span></td></tr>
        </table>
    </div>
    <?php 
    
    $get_ccm_form_id = sqlStatement("SELECT form_id FROM forms WHERE formdir='ccm' AND encounter= $encounter AND pid= $pid AND deleted= 0 order by date desc limit 0,1");
    $ccm_form_id = 0;
    while($set_ccm_form_id = sqlFetchArray($get_ccm_form_id)){
        $ccm_form_id = $set_ccm_form_id['form_id'];
    }
    
    $get_ccm_logged_mins = sqlStatement("SELECT ccm_data FROM tbl_form_ccm WHERE id = $ccm_form_id");
    while($set_ccm_logged_mins = sqlFetchArray($get_ccm_logged_mins)){
        $get_ccm_data = $set_ccm_logged_mins['ccm_data'];
    }
    $ccm_data2 = preg_replace_callback ( '!s:(\d+):"(.*?)";!',
        function($match) {
            return ($match[1] == strlen($match[2])) ? $match[0] : 's:' . strlen($match[2]) . ':"' . $match[2] . '";';
        },
    $get_ccm_data );
    
    $ccm_data = unserialize($ccm_data2);
    $timeinterval_val_ccm2 = 0;
    for($i=1; $i<= count($ccm_data); $i++){
        if(!empty($ccm_data[$i-1])){
            foreach ($ccm_data[$i-1] as $ccmkey => $ccmvalue) {
                if($ccmkey == 'timeinterval' && $ccmvalue != ''):
                    $ures = sqlStatement("SELECT title FROM list_options WHERE option_id= '$ccmvalue' and list_id = 'Time_Interval'");
                    while ($urow = sqlFetchArray($ures)) {
                        $ccmtitle = $urow['title'];
                    }
                    if($ccmtitle == ''):
                        $ccmtitle = 0;
                    endif;
                    $timeinterval_val_ccm2 = $timeinterval_val_ccm2 + $ccmtitle;
                endif;
            }
        }
    }
    
    ?>
    <br />
    <label><input type='checkbox' id='checkboxccm'  onclick= "showDiv('ccm')" ><b> CCM </b></label>
    <div id='ccm' style='border:1px solid black;margin: 0px 20px 10px 20px; display: none;clear:both;'>
        <table id='tableccm'>
            <tr><td>Default CCM minutes:</td> <td><input type='number' id='defaultccm' name ='defaultccm' value='<?php echo $ccm_default_val; ?>' onchange="calculateccmtime();"/></td></tr>
            <tr><td>Logged CCM minutes: </td> <td> <span id='ccmlog' name='ccmlog' ><?php if($timeinterval_val_ccm2 == '') echo 0; else echo $timeinterval_val_ccm2; ?></span></td></tr>
            <tr><td>Remaining Minutes: </td> <td><span id='totalccmtime' name='totalccmtime' ></span></td></tr>
        </table>
    </div>
    <br />
    <?php 
        $get_cert_recert = sqlStatement("SELECT form_id FROM forms WHERE formdir='LBF2' AND encounter= $encounter AND pid= $pid AND deleted= 0 order by date desc limit 0,1");
        $lbf_form_id = 0;
        while($set_cert_recert = sqlFetchArray($get_cert_recert)){
            $lbf_form_id = $set_cert_recert['form_id'];
        }
        $get_cert_doc_name = sqlStatement("SELECT lbf.*,l.title FROM lbf_data lbf   
                                                             INNER JOIN layout_options l ON lbf.field_id = l.field_id 
                                                             WHERE lbf.form_id = $lbf_form_id AND l.field_id IN ( 'cert_recert_doc_link',  'cert_recert_doc_name','cert_recert_process'
                                                                ) ORDER by l.seq" );

        $datachecking = 0;
        ?>
        <div id='displaycert' style='display: none;clear:both;'>
        <label><input type='checkbox' id='checkboxcert'  onclick= "showDiv('cert')" ><b> Certification/Recertification </b></label>
        <div id='cert' style='border:1px solid black;margin: 0px 20px 10px 20px; display: none;clear:both;'>
            <table id='tablecert'>
                <tr>
                    <td>
                    <?php 
                    while($set_cert_doc_name = sqlFetchArray($get_cert_doc_name)){
                        echo "<tr><td>";
                        if(!empty($set_cert_doc_name['field_value'])){
                            if(!empty($set_cert_doc_name['title'])){
                                echo $set_cert_doc_name['title'];
                                $datachecking = 1;
                            }    
                        }
                        echo "</td><td>";
                        if(!empty($set_cert_doc_name['field_value'])){
                            if($set_cert_doc_name['field_id'] == 'cert_recert_doc_link')
                                echo  " <a href= '".$set_cert_doc_name['field_value']."' target='_blank'>";
                            echo $set_cert_doc_name['field_value'];
                            if($set_cert_doc_name['field_id'] == 'cert_recert_doc_link')
                                echo "</a>";
                            $datachecking = 1;
                        }
                        echo "</td></tr>";
                        if($datachecking == 1){
                            ?>
                            <script>
                                jQuery("#displaycert").show();
                            </script>
                            <?php
                        }
                    }
                    ?>
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <br />
    </table>
    <br>
    <label> Time:</label> <input type='number'  min="0" max="150" step='5' size='5' value='<?php echo $audit_data['audit_time']; ?>' name='audit_time' id='audit_time'  /> minutes<br><br>
    <input type='button'  value='<?php echo xlt('Audit');?>' class="button-css" onclick ='audit_form_check();'>&nbsp;
    <input type='submit'  value='<?php echo xlt('Save');?>' class="button-css" onclick="return myFunction();">&nbsp;
    <input type='button' class="button-css" value='<?php echo xlt('Cancel');?>' onclick="top.restoreSession();location='<?php echo "$rootdir/patient_file/encounter/$returnurl" ?>'" />
    <?php
    echo "</form>";
?>
</body>  
<?php
formFooter();
function display_hpi($formid,$groupname_checkbox,$list_id,$option_id,$field_id,$list_title,$data,$encounter){
    if($formid == 0){
        // auto population from hpi field in allcare encounters section
        ?><label><input type='checkbox' id='<?php echo $list_id.$option_id; ?>' 
              name='<?php echo $list_id.$option_id; ?>' 
              value= '<?php echo $option_id; ?>' 
              onclick='checkbox_selected("<?php echo $field_id ?> ", "<?php echo $groupname_checkbox; ?> ", "<?php echo $option_id; ?>");'
               <?php 
        // get hpi text box data
        $get_hpi_text = sqlStatement("SELECT l.field_value FROM forms f INNER JOIN lbf_data l ON l.form_id = f.form_id WHERE f.formdir =  'LBF2' AND deleted = 0 AND l.field_id = 'hpi_text' AND f.encounter = $encounter  ");
        if(mysql_num_rows($get_hpi_text)){
            while($set_hpi_text = sqlFetchArray($get_hpi_text)){
                $hpi_text = $set_hpi_text['field_value'];
            }
            // populate data if form id is zero
            if(stripos($hpi_text, $list_title .":") !== false ){
                echo checked; 
            }
        } ?>
        ><?php echo $list_title."</label></br>"; 
    }else{
        // for already existed form
        ?><label><input type='checkbox' id='<?php echo $list_id.$option_id; ?>' 
              name='<?php echo $list_id.$option_id; ?>' 
              value= '<?php echo $option_id; ?>' 
              onclick='checkbox_selected("<?php echo $field_id ?> ", "<?php echo $groupname_checkbox; ?> ", "<?php echo $option_id; ?>");'
               <?php if($option_id == $data) 
                       echo checked; ?> 
          ><?php echo $list_title."</label></br>"; 
    }
}
function display_ros($formid,$groupname_checkbox,$list_id,$option_id,$field_id,$list_title,$data,$encounter){
    if($formid == 0){
        // auto population from hpi field in allcare encounters section
        $ros_text = '';
        // get ros data 
         // populate data if form id is zero
        ?><label><input type='checkbox' id='<?php echo $list_id.$option_id; ?>' 
          name='<?php echo $list_id.$option_id; ?>' 
          value= '<?php echo $option_id; ?>' 
          onclick='checkbox_selected("<?php echo $field_id ?> ", "<?php echo $groupname_checkbox; ?> ", "<?php echo $option_id; ?>");'
           <?php 
           // check for dynamic population
           
        // constitutional
        $get_con_text = sqlStatement("SELECT * FROM forms f INNER JOIN tbl_form_allcare_ros  ON f.form_id = tbl_form_allcare_ros.id WHERE f.encounter = $encounter AND deleted = 0 AND formdir = 'allcare_ros' AND (weight_change ='NO' OR  weakness ='NO' OR  fatigue ='NO' OR  anorexia ='NO' OR  fever ='NO' OR  chills ='NO' OR  night_sweats ='NO' OR  insomnia ='NO' OR  irritability ='NO' OR  heat_or_cold ='NO' OR  intolerance ='NO' OR  change_in_appetite ='NO' OR weight_change ='YES' OR  weakness ='YES' OR  fatigue ='YES' OR  anorexia ='YES' OR  fever ='YES' OR  chills ='YES' OR  night_sweats ='YES' OR  insomnia ='YES' OR  irritability ='YES' OR  heat_or_cold ='YES' OR  intolerance ='YES' OR  change_in_appetite ='YES') ");
        if(mysql_num_rows($get_con_text)>0){
           if(stripos(  $list_title,'Constitutional') !== false)
                echo checked." "; 
         }
         // eyes 
        $get_eyes_text = sqlStatement("SELECT * FROM forms f INNER JOIN tbl_form_allcare_ros  ON f.form_id = tbl_form_allcare_ros.id WHERE f.encounter = $encounter AND deleted = 0 AND formdir = 'allcare_ros' AND (change_in_vision = 'YES' OR glaucoma_history = 'YES' OR eye_pain = 'YES' OR irritation = 'YES' OR redness = 'YES' OR excessive_tearing = 'YES' OR double_vision = 'YES' OR blind_spots = 'YES' OR photophobia = 'YES' OR glaucoma = 'YES' OR cataract = 'YES' OR injury = 'YES' OR ha = 'YES' OR coryza = 'YES' OR obstruction = 'YES' OR change_in_vision = 'NO' OR glaucoma_history = 'NO' OR eye_pain = 'NO' OR irritation = 'NO' OR redness = 'NO' OR excessive_tearing = 'NO' OR double_vision = 'NO' OR blind_spots = 'NO' OR photophobia = 'NO' OR glaucoma = 'NO' OR cataract = 'NO' OR injury = 'NO' OR ha = 'NO' OR coryza = 'NO' OR obstruction = 'NO') ");
        if(mysql_num_rows($get_eyes_text)>0){
            if(stripos(  $list_title,'Eyes') !== false)
                 echo checked." "; 
        }
        // ears
        $get_ears_text = sqlStatement("SELECT * FROM forms f INNER JOIN tbl_form_allcare_ros  ON f.form_id = tbl_form_allcare_ros.id WHERE f.encounter = $encounter AND deleted = 0 AND formdir = 'allcare_ros' AND (hearing_loss = 'NO' OR  discharge = 'NO' OR  pain = 'NO' OR  vertigo = 'NO' OR  tinnitus = 'NO' OR  frequent_colds = 'NO' OR  sore_throat = 'NO' OR  sinus_problems = 'NO' OR  post_nasal_drip = 'NO' OR  nosebleed = 'NO' OR  snoring = 'NO' OR  apnea = 'NO' OR  bleeding_gums = 'NO' OR hoarseness = 'NO' OR  dental_difficulties = 'NO' OR  use_of_dentures = 'NO' OR  bleeding = 'NO' OR hearing_loss = 'YES' OR  discharge = 'YES' OR  pain = 'YES' OR  vertigo = 'YES' OR  tinnitus = 'YES' OR  frequent_colds = 'YES' OR  sore_throat = 'YES' OR  sinus_problems = 'YES' OR  post_nasal_drip = 'YES' OR  nosebleed = 'YES' OR  snoring = 'YES' OR  apnea = 'YES' OR  bleeding_gums = 'YES' OR hoarseness = 'YES' OR  dental_difficulties = 'YES' OR  use_of_dentures = 'YES' OR  bleeding = 'YES') ");
        if(mysql_num_rows($get_ears_text)>0){
            if(stripos(  $list_title,'Ears') !== false)
                 echo checked." "; 
        }
       // breast
        $get_breast_text = sqlStatement("SELECT * FROM forms f INNER JOIN tbl_form_allcare_ros  ON f.form_id = tbl_form_allcare_ros.id WHERE f.encounter = $encounter AND deleted = 0 AND formdir = 'allcare_ros' AND (breast_mass = 'NO' OR  breast_discharge = 'NO' OR  biopsy = 'NO' OR  abnormal_mammogram = 'NO' OR breast_mass = 'YES' OR  breast_discharge = 'YES' OR  biopsy = 'YES' OR  abnormal_mammogram = 'YES') ");
        if(mysql_num_rows($get_breast_text)>0){  
            if(stripos(  $list_title,'breast') !== false)
                 echo checked." "; 
        }
        // respiratory
        $get_respiratory_text = sqlStatement("SELECT * FROM forms f INNER JOIN tbl_form_allcare_ros  ON f.form_id = tbl_form_allcare_ros.id WHERE f.encounter = $encounter AND deleted = 0 AND formdir = 'allcare_ros' AND ( cough = 'YES' OR  sputum = 'YES' OR  shortness_of_breath = 'YES' OR  wheezing = 'YES' OR  hemoptsyis = 'YES' OR  asthma = 'YES' OR  copd = 'YES' OR cough = 'NO' OR  sputum = 'NO' OR  shortness_of_breath = 'NO' OR  wheezing = 'NO' OR  hemoptsyis = 'NO' OR  asthma = 'NO' OR  copd = 'NO') ");
        if(mysql_num_rows($get_respiratory_text)>0){
            if(stripos(  $list_title,'respiratory') !== false)
                 echo checked." "; 
        }
        // cardiovascular
        $get_cardiovascular_text = sqlStatement("SELECT * FROM forms f INNER JOIN tbl_form_allcare_ros  ON f.form_id = tbl_form_allcare_ros.id WHERE f.encounter = $encounter AND deleted = 0 AND formdir = 'allcare_ros' AND ( chest_pain = 'NO' OR  palpitation = 'NO' OR  syncope = 'NO' OR  pnd = 'NO' OR  doe = 'NO' OR  orthopnea = 'NO' OR  peripheal = 'NO' OR  edema = 'NO' OR  legpain_cramping = 'NO' OR  history_murmur = 'NO' OR  arrythmia = 'NO' OR  heart_problem= 'NO' OR  chest_pain = 'YES' OR  palpitation = 'YES' OR  syncope = 'YES' OR  pnd = 'YES' OR  doe = 'YES' OR  orthopnea = 'YES' OR  peripheal = 'YES' OR  edema = 'YES' OR  legpain_cramping = 'YES' OR  history_murmur = 'YES' OR  arrythmia = 'YES' OR  heart_problem= 'YES') ");
        if(mysql_num_rows($get_cardiovascular_text)>0){
            if(stripos(  $list_title,'cardiovascular') !== false)
                 echo checked." "; 
        }
        // Gastrointestnal
        $get_gastrointestinal_text = sqlStatement("SELECT * FROM forms f INNER JOIN tbl_form_allcare_ros  ON f.form_id = tbl_form_allcare_ros.id WHERE f.encounter = $encounter AND deleted = 0 AND formdir = 'allcare_ros' AND (  dysphagia = 'YES' OR  heartburn = 'YES' OR  bloating = 'YES' OR  belching = 'YES' OR  flatulence = 'YES' OR  nausea = 'YES' OR  vomiting = 'YES' OR  hematemesis = 'YES' OR  gastro_pain = 'YES' OR  food_intolerance = 'YES' OR  hepatitis = 'YES' OR  jaundice = 'YES' OR  hematochezia = 'YES' OR changed_bowel = 'YES' OR  diarrhea = 'YES' OR  constipation = 'YES' OR  blood_in_stool= 'YES' OR  dysphagia = 'NO' OR  heartburn = 'NO' OR  bloating = 'NO' OR  belching = 'NO' OR  flatulence = 'NO' OR  nausea = 'NO' OR  vomiting = 'NO' OR  hematemesis = 'NO' OR  gastro_pain = 'NO' OR  food_intolerance = 'NO' OR  hepatitis = 'NO' OR  jaundice = 'NO' OR  hematochezia = 'NO' OR changed_bowel = 'NO' OR  diarrhea = 'NO' OR  constipation = 'NO' OR  blood_in_stool= 'NO') ");
        if(mysql_num_rows($get_gastrointestinal_text)>0){
            if(stripos(  $list_title,'Gastrointestnal') !== false)
                 echo checked." "; 
        }
        // genitourinary
        $get_genitourinary_text = sqlStatement("SELECT * FROM forms f INNER JOIN tbl_form_allcare_ros  ON f.form_id = tbl_form_allcare_ros.id WHERE f.encounter = $encounter AND deleted = 0 AND formdir = 'allcare_ros' AND ( polyuria = 'NO' OR  polydypsia = 'NO' OR  dysuria = 'NO' OR  hematuria = 'NO' OR  frequency = 'NO' OR  urgency = 'NO' OR  incontinence = 'NO' OR  renal_stones = 'NO' OR  utis = 'NO' OR  blood_in_urine = 'NO' OR  urinary_retention = 'NO' OR  change_in_nature_of_urine= 'NO' OR  hesitancy = 'NO' OR  dribbling = 'NO' OR  stream = 'NO' OR  nocturia = 'NO' OR  erections = 'NO' OR  ejaculations = 'NO' OR  g = 'NO' OR  p = 'NO' OR  ap = 'NO' OR  lc = 'NO' OR  mearche = 'NO' OR  menopause = 'NO' OR  lmp = 'NO' OR  f_frequency = 'NO' OR  f_flow = 'NO' OR  f_symptoms = 'NO' OR  abnormal_hair_growth = 'NO' OR  f_hirsutism = 'NO'  OR polyuria = 'YES' OR  polydypsia = 'YES' OR  dysuria = 'YES' OR  hematuria = 'YES' OR  frequency = 'YES' OR  urgency = 'YES' OR  incontinence = 'YES' OR  renal_stones = 'YES' OR  utis = 'YES' OR  blood_in_urine = 'YES' OR  urinary_retention = 'YES' OR  change_in_nature_of_urine= 'YES' OR  hesitancy = 'YES' OR  dribbling = 'YES' OR  stream = 'YES' OR  nocturia = 'YES' OR  erections = 'YES' OR  ejaculations = 'YES' OR  g = 'YES' OR  p = 'YES' OR  ap = 'YES' OR  lc = 'YES' OR  mearche = 'YES' OR  menopause = 'YES' OR  lmp = 'YES' OR  f_frequency = 'YES' OR  f_flow = 'YES' OR  f_symptoms = 'YES' OR  abnormal_hair_growth = 'YES' OR  f_hirsutism = 'YES' ) ");
        if(mysql_num_rows($get_genitourinary_text)>0){
            if(stripos(  $list_title,'genitourinary') !== false)
                 echo checked." "; 
        }
        //musculoskeletal
        $get_musculoskeletal_text = sqlStatement("SELECT * FROM forms f INNER JOIN tbl_form_allcare_ros  ON f.form_id = tbl_form_allcare_ros.id WHERE f.encounter = $encounter AND deleted = 0 AND formdir = 'allcare_ros' AND (  joint_pain = 'YES' OR  swelling = 'YES' OR  m_redness = 'YES' OR  m_warm = 'YES' OR  m_stiffness = 'YES' OR  m_aches = 'YES' OR  fms = 'YES' OR  arthritis = 'YES' OR  gout = 'YES' OR  back_pain = 'YES' OR  paresthesia = 'YES' OR  muscle_pain = 'YES' OR limitation_in_range_of_motion = 'YES' OR joint_pain = 'NO' OR  swelling = 'NO' OR  m_redness = 'NO' OR  m_warm = 'NO' OR  m_stiffness = 'NO' OR  m_aches = 'NO' OR  fms = 'NO' OR  arthritis = 'NO' OR  gout = 'NO' OR  back_pain = 'NO' OR  paresthesia = 'NO' OR  muscle_pain = 'NO' OR limitation_in_range_of_motion = 'NO') ");
        if(mysql_num_rows($get_musculoskeletal_text)>0){
            if(stripos(  $list_title,'musculoskeletal') !== false)
                 echo checked." "; 
        }
        // extremities
        $get_extremities_text = sqlStatement("SELECT * FROM forms f INNER JOIN tbl_form_allcare_ros  ON f.form_id = tbl_form_allcare_ros.id WHERE f.encounter = $encounter AND deleted = 0 AND formdir = 'allcare_ros' AND ( spasms = 'YES' OR  extreme_tremors= 'YES' OR  spasms = 'NO' OR  extreme_tremors= 'NO') ");
        if(mysql_num_rows($get_extremities_text)>0){
            if(stripos(  $list_title,'extremities') !== false)
                 echo checked." "; 
        }
        //neurologic
        $get_neurologic_text = sqlStatement("SELECT * FROM forms f INNER JOIN tbl_form_allcare_ros  ON f.form_id = tbl_form_allcare_ros.id WHERE f.encounter = $encounter AND deleted = 0 AND formdir = 'allcare_ros' AND ( loc = 'YES' OR  seizures = 'YES' OR   stroke  = 'YES' OR  tia = 'YES' OR  n_numbness = 'YES' OR  n_weakness = 'YES' OR  paralysis = 'YES' OR  intellectual_decline = 'YES' OR  memory_problems = 'YES' OR  dementia = 'YES' OR  n_headache = 'YES' OR  dizziness_vertigo = 'YES' OR  slurred_speech = 'YES' OR tremors = 'YES' OR  migraines = 'YES' OR  changes_in_mentation = 'YES' OR  tingling = 'YES'  OR loc = 'NO' OR  seizures = 'NO' OR   stroke  = 'NO' OR  tia = 'NO' OR  n_numbness = 'NO' OR  n_weakness = 'NO' OR  paralysis = 'NO' OR  intellectual_decline = 'NO' OR  memory_problems = 'NO' OR  dementia = 'NO' OR  n_headache = 'NO' OR  dizziness_vertigo = 'NO' OR  slurred_speech = 'NO' OR tremors = 'NO' OR  migraines = 'NO' OR  changes_in_mentation = 'NO' OR  tingling = 'NO') ");
        if(mysql_num_rows($get_neurologic_text)>0){
            if(stripos(  $list_title,'neurologic') !== false)
                 echo checked." "; 
        }
        //skin
        $get_skin_text = sqlStatement("SELECT * FROM forms f INNER JOIN tbl_form_allcare_ros  ON f.form_id = tbl_form_allcare_ros.id WHERE f.encounter = $encounter AND deleted = 0 AND formdir = 'allcare_ros' AND ( s_cancer = 'NO' OR  psoriasis = 'NO' OR  s_acne = 'NO' OR  s_other = 'NO' OR  s_disease = 'NO' OR  rashes = 'NO' OR  dryness = 'NO' OR  itching = 'NO' OR  lesions = 'NO' OR  sores= 'NO' OR s_cancer = 'YES' OR  psoriasis = 'YES' OR  s_acne = 'YES' OR  s_other = 'YES' OR  s_disease = 'YES' OR  rashes = 'YES' OR  dryness = 'YES' OR  itching = 'YES' OR  lesions = 'YES' OR  sores= 'YES') ");
        if(mysql_num_rows($get_skin_text)>0){
            if(stripos(  $list_title,'skin') !== false)
                 echo checked." "; 
        }
        // psychiatric
        $get_psychiatric_text = sqlStatement("SELECT * FROM forms f INNER JOIN tbl_form_allcare_ros  ON f.form_id = tbl_form_allcare_ros.id WHERE f.encounter = $encounter AND deleted = 0 AND formdir = 'allcare_ros' AND ( p_diagnosis = 'NO' OR  p_medication = 'NO' OR  depression = 'NO' OR  anxiety = 'NO' OR  social_difficulties = 'NO' OR  alcohol_drug_dependence = 'NO' OR  suicide_thoughts = 'NO' OR  use_of_antideprassants = 'NO' OR  thought_content = 'NO' OR  changes_in_sleep_habits= 'NO' OR  p_diagnosis = 'YES' OR  p_medication = 'YES' OR  depression = 'YES' OR  anxiety = 'YES' OR  social_difficulties = 'YES' OR  alcohol_drug_dependence = 'YES' OR  suicide_thoughts = 'YES' OR  use_of_antideprassants = 'YES' OR  thought_content = 'YES' OR  changes_in_sleep_habits= 'YES') ");
        if(mysql_num_rows($get_psychiatric_text)>0){
            if(stripos(  $list_title,'psychiatric') !== false)
                 echo checked." "; 
        }
        // endocrine
        $get_endocrine_text = sqlStatement("SELECT * FROM forms f INNER JOIN tbl_form_allcare_ros  ON f.form_id = tbl_form_allcare_ros.id WHERE f.encounter = $encounter AND deleted = 0 AND formdir = 'allcare_ros' AND (thyroid_problems = 'NO' OR  diabetes = 'NO' OR  abnormal_blood = 'NO' OR  goiter = 'NO' OR  heat_intolerence = 'NO' OR  cold_intolerence = 'NO' OR  increased_thirst = 'NO' OR  excessive_sweating = 'NO' OR  excessive_hunger = 'NO' OR  polyphagia= 'NO' OR  thyroid_problems = 'YES' OR  diabetes = 'YES' OR  abnormal_blood = 'YES' OR  goiter = 'YES' OR  heat_intolerence = 'YES' OR  cold_intolerence = 'YES' OR  increased_thirst = 'YES' OR  excessive_sweating = 'YES' OR  excessive_hunger = 'YES' OR  polyphagia= 'YES') ");
        if(mysql_num_rows($get_endocrine_text)>0){
            if(stripos(  $list_title,'endocrine') !== false)
                 echo checked." "; 
        }
        // hai
        $get_hai_text = sqlStatement("SELECT * FROM forms f INNER JOIN tbl_form_allcare_ros  ON f.form_id = tbl_form_allcare_ros.id WHERE f.encounter = $encounter AND deleted = 0 AND formdir = 'allcare_ros' AND ( anemia = 'NO' OR  fh_blood_problems = 'NO' OR  bleeding_problems = 'NO' OR  allergies = 'NO' OR  frequent_illness = 'NO' OR  hiv = 'NO' OR  hai_status = 'NO' OR  hay_fever = 'NO' OR  positive_ppd = 'NO' OR  anemia = 'YES' OR  fh_blood_problems = 'YES' OR  bleeding_problems = 'YES' OR  allergies = 'YES' OR  frequent_illness = 'YES' OR  hiv = 'YES' OR  hai_status = 'YES' OR  hay_fever = 'YES' OR  positive_ppd = 'YES') ");
        if(mysql_num_rows($get_hai_text)>0){
            if(stripos(  $list_title,'Hematologic') !== false  || stripos(  $list_title,'Lymphatic') !== false || stripos(  $list_title,'immunologic') !== false  || stripos(  $list_title,'Allergic') !== false)
                 echo checked." "; 
        }
        // neck
        $get_neck_text = sqlStatement("SELECT * FROM forms f INNER JOIN tbl_form_allcare_ros  ON f.form_id = tbl_form_allcare_ros.id WHERE f.encounter = $encounter AND deleted = 0 AND formdir = 'allcare_ros' AND (stiffness = 'NO' OR  neck_pain = 'NO' OR  masses = 'NO' OR  tenderness= 'NO' OR  stiffness = 'YES' OR  neck_pain = 'YES' OR  masses = 'YES' OR  tenderness= 'YES') ");
        if(mysql_num_rows($get_neck_text)>0){
            if(stripos(  $list_title,'neck') !== false  )
                 echo checked." "; 
        }
        // end of checkbox
        ?>   ><?php echo $list_title."<label></br>"; 
        
        
    }else{
        // for already existed form
        ?><label><input type='checkbox' id='<?php echo $list_id.$option_id; ?>' 
              name='<?php echo $list_id.$option_id; ?>' 
              value= '<?php echo $option_id; ?>' 
              onclick='checkbox_selected("<?php echo $field_id ?> ", "<?php echo $groupname_checkbox; ?> ", "<?php $option_id; ?>");'
               <?php if($option_id == $data) 
                       echo checked; ?>  
          ><?php echo $list_title."</label></br>"; 
    }
}

?>
