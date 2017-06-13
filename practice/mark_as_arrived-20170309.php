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
//require_once("$srcdir/patient.inc");
require_once("../interface/globals.php");  
require_once("../library/formdata.inc.php"); 
require_once("../library/globals.inc.php");
require_once("$srcdir/api.inc");
require_once("$srcdir/forms.inc");
require_once("$srcdir/options.inc.php");
require_once("$srcdir/patient.inc"); 
require_once("$srcdir/formdata.inc.php");
require_once("$srcdir/formatting.inc.php");

$patientid              = trim($_REQUEST['patientid']);
$pc_eid                 = $_REQUEST['pc_eid'];


// mark as arrived
$set_arrived = sqlStatement("UPDATE openemr_postcalendar_events SET pc_apptstatus = '@' WHERE pc_eid = '$pc_eid' AND pc_pid = '$patientid'");

$get_visit_details = sqlStatement("SELECT pc_eventDate,pc_catid,pc_facility,pc_billing_location,pc_aid FROM openemr_postcalendar_events WHERE pc_eid = '$pc_eid' AND pc_pid = '$patientid'");
while($set_visit_details = sqlFetchArray($get_visit_details)){
    // New encounter Creation
    $dos                = $set_visit_details['pc_eventDate'];
    $visit_category     = $set_visit_details['pc_catid'];
    $facility           = $set_visit_details['pc_facility'];
    $billing_facility   = $set_visit_details['pc_billing_location'];
    $rendering_provider = trim($set_visit_details['pc_aid']);

    $getfacilityname = sqlStatement("SELECT name FROM facility where id = $facility");
    $facility_name = '';
    if(!empty($getfacilityname)){
        while($setfacilityname = sqlFetchArray($getfacilityname)){
            $facility_name = $setfacilityname['name'];
        }
    }
    $query  = sqlStatement("SELECT id as max_encounter FROM sequences");
    $array = array();
    while($setquery = sqlFetchArray($query)){
        $encounter = $setquery['max_encounter'] + 1;
        $queryseq = sqlStatement("UPDATE sequences SET id = $encounter ");
        $insert_encounter = sqlStatement("INSERT INTO form_encounter (date, facility, facility_id, pid, encounter, pc_catid, provider_id, billing_facility,rendering_provider)
            VALUES ('$dos', '$facility_name',$facility,$patientid,$encounter,$visit_category,$rendering_provider,$billing_facility,'$rendering_provider')");
        $sqlLastEncounter = sqlStatement("SELECT MAX(encounter) as encounter, form_encounter.id
            FROM form_encounter 
            WHERE pid=$patientid AND form_encounter.rendering_provider=$rendering_provider AND form_encounter.encounter = $encounter");
        $sqlGetLastEncounter = sqlFetchArray($sqlLastEncounter);
        if(!empty($sqlGetLastEncounter)){
            $insertform = sqlStatement("INSERT INTO forms (date, encounter, form_name, form_id, pid, user, groupname, authorized, deleted, formdir)
                VALUES(NOW(),".$sqlGetLastEncounter['encounter'].",'New Patient Encounter',".$sqlGetLastEncounter['id'].",$patientid,'".$_SESSION['portal_username']."','Default',1, 0,'newpatient')");

             // log data
            $logdata= array(); 
            $data = sqlStatement("SELECT logdate from `tbl_allcare_formflag` WHERE  form_id='".$sqlGetLastEncounter['id'] . "' AND encounter_id = '".$sqlGetLastEncounter['encounter']."' AND form_name = 'Patient Encounter'");
            while($datalog = sqlFetchArray($data)){
                    $array =  unserialize($datalog['logdate']);
                    $count= count($array);
            }
            $username       = $_SESSION['portal_username']; 

            $res = sqlStatement("SELECT * FROM `tbl_allcare_formflag` WHERE  form_id='".$sqlGetLastEncounter['id'] . "' AND encounter_id = '".$sqlGetLastEncounter['encounter']."' AND form_name = 'Patient Encounter'");
            if(empty($row1_res1)){
                $count = 0;

                $array2[] = array( 'authuser' =>$username,'Status' => 'Incomplete', 'date' => date("Y/m/d"), 'action'=>'created', 'ip_address'=>'Provider Portal','count'=> $count+1);
                $logdata=  serialize($array2);
                $query1 = sqlStatement("INSERT INTO tbl_allcare_formflag ( encounter_id,form_id, form_name,pending,finalized, logdate" .
                        ") VALUES ( '".$sqlGetLastEncounter['encounter']."','".$sqlGetLastEncounter['id'] ."', 'Patient Encounter',NULL, NULL, '".$logdata."' )");

            }else{
                $count = isset($count)? $count: 0;

                $array2[] = array( 'authuser' =>$username,'Status' => 'Incomplete', 'date' => date("Y/m/d"), 'action'=>'updated' ,'ip_address'=>'Provider Portal','count'=> $count+1);
                $logdata = array_merge_recursive($array, $array2);
                $logdata= ($logdata? serialize($logdata): serialize($array2) );
                $query1 = sqlStatement("UPDATE tbl_allcare_formflag SET logdate=  '".$logdata."' WHERE encounter_id ='".$sqlGetLastEncounter['encounter']."' and form_id = '".$sqlGetLastEncounter['id'] . "' and form_name = 'Patient Encounter'"); 
            }
        }
    }
    
    function end_cell() {
      global $item_count, $cell_count;
      if ($item_count > 0) {
        echo "</td>";
        $item_count = 0;
      }
    }

    function end_row() {
      global $cell_count, $CPR;
      end_cell();
      if ($cell_count > 0) {
        for (; $cell_count < $CPR; ++$cell_count) echo "<td></td>";
        echo "</tr>\n";
        $cell_count = 0;
      }
    }

    function end_group() {
      global $last_group;
      if (strlen($last_group) > 0) {
        end_row();
        echo " </table>\n";
        echo "</div>\n";
      }
    }

    $last_group = '';
    $cell_count = 0;
    $item_count = 0;
    $display_style = 'block';

    $group_seq=0; // this gives the DIV blocks unique IDs

   ?>
    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">
    <?php
//    require_once("verify_session.php");
    ?>
        <div  class="page-header">
            <h3><?php xl("Encounter Forms Single Page View", "e" )?></h3>
        </div>
    <br>
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/jquery.js"></script>
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/common.js"></script>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/fancybox/jquery.fancybox-1.2.6.js"></script>
    <script type="text/javascript" src="../library/dialog.js"></script>
    <script type="text/javascript" src="../library/textformat.js"></script>
    <script type="text/javascript" src="../library/dynarch_calendar.js"></script>
    <?php include_once("{$GLOBALS['srcdir']}/dynarch_calendar_en.inc.php"); ?>
    <script type="text/javascript" src="../library/dynarch_calendar_setup.js"></script>
    <script>
        $(document).ready(function(){
            $("#single_view").append($('.newwindow'));
            // adding on change attribute to all textarea fields in form dynamically
            $('textarea').bind("blur", function() {
                var field_id    = this.id;
                var field_val   = $("#"+field_id).val();
                var type        = $("#hiddentype"+field_id.replace("form_", "")).val();
                var  form_id    = $("#lbf_form_id").val();
                var encounter   = "<?php echo $encounter; ?>";
                var pid         = "<?php echo $patientid; ?>";

                var fieldname  = {
                                    encounter   : encounter, 
                                    pid         : pid, 
                                    type        : type, 
                                    form_id     : form_id,
                                    field_id    : field_id,
                                    field_val   : field_val
                                };
                ajaxcall(fieldname);
            });
            $('.newwindow a').click(function(event){event.preventDefault();});
            // adding on change attribute to all select fields in form dynamically
            $('select').bind("change", function() {
                var field_id    = this.id;
                if(field_id != 'template_from'){
                    var field_val   = $("#"+field_id).val();
                    var type        = $("#hiddentype"+field_id.replace("form_", "")).val();

                    var  form_id    = $("#lbf_form_id").val();

                    var encounter       = "<?php echo $encounter; ?>";
                    var pid             = "<?php echo $patientid; ?>";
                    var datatype_new_val    = $("#hidden"+field_id.replace("form_", "")).val();
                    if(datatype_new_val == 40){
                        // $_POST["form_$field_id"] is an array of dropdown and its keys
                        // must be concatenated into a |-separated string.

                        var value_string = field_val+"";
                        field_val        = value_string.replace(/,/g , "|");
                    }else if(datatype_new_val == 28 || datatype_new_val == 32){
                        // $_POST["form_$field_id"] is an date text fields with companion
                        // radio buttons to be imploded into "notes|type|date".

                        var field_id2   = field_id.replace("form_", "");
                        var restype = $("input[name='radio_"+field_id2+"']:checked").val();
                        if (restype == '') restype = '0';
                        var resdate = $("input[name='date_"+field_id2+"']").val();
                        var resnote = $("#form_"+field_id2).val();

                        if(datatype_new_val == 32){
                            //VicarePlus :: Smoking status data is imploded into "note|type|date|list".
                            var reslist = $("#form_"+field_id2).val();
                            var res_text_note = $("input[name='form_text_"+field_id2+"']").val();
                            var field_val = res_text_note+"|"+restype+"|"+resdate+"|"+reslist;
                        }else{
                            var field_val = resnote+"|"+restype+"|"+resdate;
                        }
                    }else{
                        var field_id2   = field_id.replace("form_", "");
                        var field_val   = $("#form_"+field_id2).val();
                        var field_id    = field_id2;
                    }
                    var fieldname   = {
                                        encounter   : encounter, 
                                        pid         : pid, 
                                        type        : type, 
                                        form_id     : form_id,
                                        field_id    : field_id,
                                        field_val   : field_val
                                    };
                    ajaxcall(fieldname); 
                }
            });

            // adding on change attribute to all input fields in form dynamically

            $("input").bind("change", function() {
                var field_id        = this.id;
                var field_string    = field_id.split('[');
                var val             = '';
                var string_label    = field_string[0].trim();
                var type            = '';
                var type2            = '';
                if(string_label.indexOf("radio_") == 0){
                    var datatype_new_val    = $("#hidden"+string_label.replace("radio_", "")).val();
                }else if(string_label.indexOf("form_") == 0){
                    if(string_label.indexOf("form_text_") == 0)
                        var datatype_new_val    = $("#hidden"+string_label.replace("form_text_", "")).val();
                    else
                        var datatype_new_val    = $("#hidden"+string_label.replace("form_", "")).val();
                }else if(string_label.indexOf("date_") == 0){
                    var datatype_new_val    = $("#hidden"+string_label.replace("date_", "")).val();
                } 
                $("input[name^='"+string_label+"[']:checked").each(function(){

                    var field_string_split    = this.id;//alert(field_id);
                    var field_string_split2   = field_string_split.split('[');
                    string_label              = field_string_split2[0].replace("form_", "");
                    var string_label2         = field_string_split2[1].replace("]", "");

                    if(datatype_new_val == 21){
                        type2        = $("#hiddentype"+string_label).val();
                        // $_POST["form_$field_id"] is an array of checkboxes and its keys
                        // must be concatenated into a |-separated string.
                        val += string_label2+"|"; 
                    }

                });
                $("input[name^='"+string_label+"[']:not(:checked)").each(function(){
                    var numberOfChecked = $(this).find(':checkbox:checked').length;
                    if(datatype_new_val == 21 && $(this).attr('type') == 'checkbox' && numberOfChecked == 0){
                        var field_string_split    = this.id;
                        var field_string_split2   = field_string_split.split('[')
                        string_label              = field_string_split2[0].replace("form_", "");
                        type2                     = $("#hiddentype"+string_label).val();
                        val = '';
                    }
                });
                if(datatype_new_val != 21){
                    $("input[name^='"+string_label+"[']").each(function(){
                        var field_string_split      = this.id;
                        var field_string_split2     = field_string_split.split('[');
                        var string_label_split      = field_string_split2[0].replace("form_", "");
                        var string_label2           = field_string_split2[1].replace("]", "");
                        if(datatype_new_val == 22) {
                            // $_POST["form_$field_id"] is an array of text fields to be imploded
                            // into "key:value|key:value|...".
                            type2                    = $("#hiddentype"+string_label_split).val();
                            var text_val            = $(this).val();

                            val += string_label2+":"+text_val+'|';
                            string_label = string_label_split.replace("form_", "");
                        }else if(datatype_new_val == 23){
                            // $_POST["form_$field_id"] is an array of text fields with companion
                            // radio buttons to be imploded into "key:n:notes|key:n:notes|...".

                            type2        = $("#hiddentype"+string_label).val();

                            var string_val          = $("input[name='"+field_string_split.replace("form_", "radio_")+"']:checked").val();
                            if(!string_val)
                                string_val          = 0;

                            var text_val            = $("input[name='"+field_string_split.replace("radio_", "form_")+"']").val();
                            if(!text_val)
                                text_val            = '';

                            var checkstring = string_label2+":"+string_val+":"+text_val+'|';
                            if(val.indexOf(checkstring) == -1 ){
                                val += checkstring;
                            }

                            var string_label_split  = field_string_split.split('[');
                            var string_label_split2 = string_label_split[0];

                            string_label2 = string_label_split2.replace("form_", "");
                            string_label  = string_label2.replace("radio_", "");

                        }else if(datatype_new_val == 25){
                            // $_POST["form_$field_id"] is an array of text fields with companion
                            // checkboxes to be imploded into "key:n:notes|key:n:notes|...".

                            type2        = $("#hiddentype"+field_id.replace("form_", "")).val();

                            var string_val          = $("input[name='"+field_string_split.replace("form_", "check_")+"']:checked").val();
                            if(!string_val)
                                string_val          = 0;

                            var text_val            = $("input[name='"+field_string_split.replace("check_", "form_")+"']").val();
                            if(!text_val)
                                text_val            = '';

                            val += string_label2+":"+string_val+":"+text_val+'|';

                            var string_label_split  = field_string_split.split('[');
                            var string_label_split2 = string_label_split[0];

                            string_label2 = string_label_split2.replace("form_", "");
                            string_label  = string_label2.replace("check_", "");

                        }else if(datatype_new_val == 28 || datatype_new_val == 32){
                            // $_POST["form_$field_id"] is an date text fields with companion
                            // radio buttons to be imploded into "notes|type|date".

                            type2        = $("#hiddentype"+field_id.replace("form_", "")).val();

                            var field_id2   = field_id.replace("form_", "");
                            var restype = $("input[name='radio_"+field_id2+"']:checked").val();
                            if (restype == '') restype = '0';
                            var resdate = $("input[name='date_"+field_id2+"']").val();
                            var resnote = $("#form_"+field_id2).val();

                            if(datatype_new_val == 32){
                                //VicarePlus :: Smoking status data is imploded into "note|type|date|list".
                                var reslist = $("#form_"+field_id2).val();
                                var res_text_note = $("input[name='form_text_"+field_id2+"']").val();
                                var field_val = res_text_note+"|"+restype+"|"+resdate+"|"+reslist;
                            }
                            else{
                                var field_val = resnote+"|"+restype+"|"+resdate;
                            }
                        }else{
                            var field_id2   = field_id.replace("form_", "");
                            var field_val   = $("#form_"+field_id2).val();
                            var field_id    = field_id2;
                        }
                    });
                    $("input[name='"+string_label+"']").each(function(){
                        var field_string_split      = this.id;
                        var field_string_split2     = field_string_split.split('[');

                        if(field_string_split2[1])
                            var string_label2           = field_string_split2[1].replace("]", "");
                        else
                            var string_label2 = '';
                        if(datatype_new_val == 22) {
                            // $_POST["form_$field_id"] is an array of text fields to be imploded
                            // into "key:value|key:value|...".
                            if(field_string_split2[0])
                                var string_label_split      = field_string_split2[0].replace("form_", "");
                            type2                    = $("#hiddentype"+string_label_split).val();
                            var text_val            = $(this).val();

                            val += string_label2+":"+text_val+'|';
                            string_label = string_label_split.replace("form_", "");
                        }else if(datatype_new_val == 23){
                            // $_POST["form_$field_id"] is an array of text fields with companion
                            // radio buttons to be imploded into "key:n:notes|key:n:notes|...".

                            type2        = $("#hiddentype"+field_id.replace("form_", "")).val();

                            var string_val          = $("input[name='"+field_string_split.replace("form_", "radio_")+"']:checked").val();
                            if(!string_val)
                                string_val          = 0;

                            var text_val            = $("input[name='"+field_string_split.replace("radio_", "form_")+"']").val();
                            if(!text_val)
                                text_val            = '';

                            var checkstring = string_label2+":"+string_val+":"+text_val+'|';
                            if(val.indexOf(checkstring) == -1 ){
                                val += checkstring;
                            }

                            var string_label_split  = field_string_split.split('[');
                            var string_label_split2 = string_label_split[0];

                            string_label2 = string_label_split2.replace("form_", "");
                            string_label  = string_label2.replace("radio_", "");

                        }else if(datatype_new_val == 25){
                            // $_POST["form_$field_id"] is an array of text fields with companion
                            // checkboxes to be imploded into "key:n:notes|key:n:notes|...".
                            type2        = $("#hiddentype"+field_id.replace("form_", "")).val();

                            var string_val          = $("input[name='"+field_string_split.replace("form_", "check_")+"']:checked").val();
                            if(!string_val)
                                string_val          = 0;

                            var text_val            = $("input[name='"+field_string_split.replace("check_", "form_")+"']").val();
                            if(!text_val)
                                text_val            = '';

                            val += string_label2+":"+string_val+":"+text_val+'|';

                            var string_label_split  = field_string_split.split('[');
                            var string_label_split2 = string_label_split[0];

                            string_label2 = string_label_split2.replace("form_", "");
                            string_label  = string_label2.replace("check_", "");

                        }else if(datatype_new_val == 28 || datatype_new_val == 32){
                            // $_POST["form_$field_id"] is an date text fields with companion
                            // radio buttons to be imploded into "notes|type|date".
                            if(field_string_split.indexOf("form_") == 0){
                                if(field_string_split.indexOf("form_text_") == 0){
                                    type2           = $("#hiddentype"+field_string_split2[0].replace("form_text_", "")).val();
                                    var field_id2   = field_string_split2[0].replace("form_text_", "");
                                }else{
                                    type2           = $("#hiddentype"+field_string_split2[0].replace("form_", "")).val();
                                    var field_id2   = field_string_split2[0].replace("form_", "");
                                }
                            }else if(field_string_split.indexOf("radio_") == 0){
                                type2           = $("#hiddentype"+field_string_split2[0].replace("radio_", "")).val();
                                var field_id2   = field_string_split2[0].replace("radio_", "");
                            }else if(field_string_split.indexOf("date_") == 0){
                                type2           = $("#hiddentype"+field_string_split2[0].replace("date_", "")).val();
                                var field_id2   = field_string_split2[0].replace("date_", "");
                            }
                            var restype = '';
                            var restype = $("input[name='radio_"+field_id2+"']:checked").val();
                            if (restype == '' || restype == 'undefined' ) restype = '0';
                            var resdate = $("input[name='date_"+field_id2+"']").val();
                            var resnote = $("#form_"+field_id2).val();

                            if(datatype_new_val == 32){
                                //VicarePlus :: Smoking status data is imploded into "note|type|date|list".
                                var reslist = $("#form_"+field_id2).val();
                                var res_text_note = $("input[name='form_text_"+field_id2+"']").val();
                                var val2 = res_text_note+"|"+restype+"|"+resdate+"|"+reslist;
                            }
                            else{
                                var val2 = resnote+"|"+restype+"|"+resdate;
                            }
    //                        alert(val2);
                            var string_label_split  = field_string_split.split('[');
                            var string_label_split2 = string_label_split[0];

                            if(field_string_split.indexOf("form_") == 0){
                                if(field_string_split.indexOf("form_text_") == 0)
                                    string_label2 = field_string_split2[0].replace("form_text_", "");
                                else
                                    string_label2 = field_string_split2[0].replace("form_text_", "");
                            }else if(field_string_split.indexOf("radio_") == 0){
                                string_label2 = field_string_split2[0].replace("radio_", "");
                            }else if(field_string_split.indexOf("date_") == 0){
                                string_label2 = field_string_split2[0].replace("date_", "");
                            }    
                            string_label  = string_label2.replace("check_", "");
                        }else{
                            type2           = $("#hiddentype"+field_string_split2[0].replace("form_", "")).val();
                            var field_id2   = field_string_split2[0].replace("form_", "");
                            var val2        = $("#form_"+field_id2).val();
                        }
                        val = val2;
                    });
                }

                var field_val   = val;

                type            = type2;
                var  form_id    = $("#lbf_form_id").val();
                var encounter   = "<?php echo $encounter; ?>";
                var pid         = "<?php echo $patientid; ?>";

                var fieldname   = {
                                    encounter   : encounter, 
                                    pid         : pid, 
                                    type        : type, 
                                    form_id     : form_id,
                                    field_id    : string_label,
                                    field_val   : field_val
                                };
    //                            alert(encounter+"="+pid+"="+type+"="+form_id+"="+string_label+"="+field_val);
                ajaxcall(fieldname);
            });
            // date time datatype picker

            $("input[placeholder*='date and time']").blur(function() {

                var field_string_split        = this.id;
                var string_label          = field_string_split.replace("form_", "");
                var type                  = $("#hiddentype"+string_label).val();
                var field_val             = $(this).val();
                var  form_id    = $("#lbf_form_id").val();
                var encounter   = "<?php echo $encounter; ?>";
                var pid         = "<?php echo $patientid; ?>";

                var fieldname   = {
                                    encounter   : encounter, 
                                    pid         : pid, 
                                    type        : type, 
                                    form_id     : form_id,
                                    field_id    : string_label,
                                    field_val   : field_val
                                };
    //                            alert(encounter+"="+pid+"="+type+"="+form_id+"="+string_label+"="+field_val);
                ajaxcall(fieldname);
            });

            // image of date object
             $( "img" ).click(function() {
                var field_id    = this.id;

                $('.calendar .day').click(function(){
                    var field_val   = $("#"+field_id).val();
                    var type        = $("#hiddentype"+field_id.replace("img_", "")).val();
                    var field_id2   = field_id.replace("img_", "");

                    var  form_id    = $("#lbf_form_id").val();

                    var encounter       = "<?php echo $encounter; ?>";
                    var pid             = "<?php echo $patientid; ?>";
                    var datatype_new_val    = $("#hidden"+field_id.replace("img_", "")).val();

                    if(datatype_new_val == 40){
                        // $_POST["form_$field_id"] is an array of dropdown and its keys
                        // must be concatenated into a |-separated string.

                        var value_string = field_val+"";
                        field_val        = value_string.replace(/,/g , "|");

                    }else if(datatype_new_val == 28 || datatype_new_val == 32){
                        // $_POST["form_$field_id"] is an date text fields with companion
                        // radio buttons to be imploded into "notes|type|date".

                        var restype = $("input[name='radio_"+field_id2+"']:checked").val();//alert(restype+"="+"input[name='radio_"+field_id2+"']");
                        if (restype == '') restype = '0';
                        var resdate = $("input[name='date_"+field_id2+"']").val();//alert(resdate+"="+"input[name='date_"+field_id2+"']");
                        var resnote = $("#form_"+field_id2).val();//alert(resnote+"="+"#form_"+field_id2);

                        if(datatype_new_val == 32){
                            //VicarePlus :: Smoking status data is imploded into "note|type|date|list".
                            var reslist = $("#form_"+field_id2).val();//alert(reslist+"="+"#form_"+field_id2);
                            var res_text_note = $("input[name='form_text_"+field_id2+"']").val();//alert(res_text_note+"="+"input[name='form_text_"+field_id2+"']");
                            var field_val = res_text_note+"|"+restype+"|"+resdate+"|"+reslist;
                        }else{
                            var field_val = resnote+"|"+restype+"|"+resdate;
                        }
                    }
                    var fieldname   = {
                                        encounter   : encounter, 
                                        pid         : pid, 
                                        type        : type, 
                                        form_id     : form_id,
                                        field_id    : field_id2,
                                        field_val   : field_val
                                    };
                    ajaxcall(fieldname);
                });
            });

        });
        function ajaxcall(fieldname){
         $('#savealert').html("<div>Saving...</div>");
            $.ajax({
                type: "POST",
                url: "save_single_view_data.php",
                data: fieldname,
                dataType : "json",
                success: function(data) {
                    var dataresult = data +' ';
                    var res = dataresult.split(',');
                    $("#lbf_form_id").val(res[0]);
                    $('#savealert').html("<div>Saved.</div>").fadeIn(500,function(){$(this).fadeOut()});
                },
                error: function(jqXHR, exception){
                    alert("failed" + jqXHR.responseText);
                }    
            });
        }
        function win1(url){
            var popup = window.open(url,'_blank','width=900,height=500,scrollbars=yes,resizable=yes');
            $("#single_view :input").attr("disabled", true);
            if (popup) {
                popup.onbeforeunload = function () { refreshAndClose(); }
            }
        }
        function refreshAndClose() {
            window.opener($("#single_view :input").attr("disabled", false));
        }
        function RefreshParent() {
            if (window.opener != null && !window.opener.closed) {
    //            window.opener.location.reload();
                window.opener.location.href = 'provider_incomplete_charts.php?checkencounter=<?php echo $encounter; ?>';
            }
        }
        var isChrome = /Chrome/.test(navigator.userAgent) && /Google Inc/.test(navigator.vendor);
        isChrome?window.onunload = RefreshParent:window.onbeforeunload = RefreshParent; 

        function showforms()
        {
             $('body').css("overflow","hidden");
            var copied_to   = '<?php  echo $encounter; ?>';
            var encounter   = jQuery('#template_from').val();
            if(encounter != ' '){
                $.ajax({
                        type: 'POST',
                        url: "copy_template.php",

                        data: { 
                            encounter:encounter,
                            copied_to:copied_to
                        },

                        success: function(response)
                        {  
                            var result='';
                            if( $('#template1').html(response)) {

                                var answer = $("#template1").dialog({resizable: false,position:"top",close: function() {$(".lightbox").fadeOut(function(){$('body').css("overflow","auto");$(this).remove();});}})
                                            .find(':checkbox').unbind('change').bind('change', function(e){
                                    if(this.checked)  {
                                        if($(this).val()!="undefined" && $(this).val()!=''){ result+=$(this).val()+","; }
                                    }
                                });
                                if(!$('.ui-dialog').next().hasClass('lightbox')){
                                    $( "<div class='lightbox'></div>" ).insertAfter( ".ui-dialog" );
                                    $('.lightbox').css("height",$('.body_top').height()+"px");
                                }
                                var gettopdialogpos =  parseInt(($('.ui-dialog').css("top")).replace("px",""));
                                var getwindowgeight = ((window.innerHeight/2) - ($('.ui-dialog').height()/2));
                                var centerdilog = ((gettopdialogpos + getwindowgeight));
                                $('.ui-dialog').css("top",centerdilog +"px");
                                $(".lightbox").fadeIn();
                                $('.ui-dialog').fadeIn();
                                $('.lightbox').click(function(){
                                    $(this).fadeOut(function(){$('body').css("overflow","auto");$(this).remove();});
                                    $('.ui-dialog').fadeOut();
                                });

                                $("#ok").click(function(e) {

                                    var formdetails     = result.split(','); 
                                    for(var i=0; i<formdetails.length; i++){

                                        var copy_to_fname   = formdetails[i].split('--');
                                        if(typeof copy_to_fname !='undefined' && copy_to_fname  != ''  && copy_to_fname != null ){
    //                                        alert("hema");
                                            var copy_to_fname1  = copy_to_fname[1];
                                            var form_name1      = copy_to_fname[0].split('-');
                                            var copy_from_id    = form_name1[0];
                                            var form_name       = form_name1[1];
                                            $('#template1').dialog('close');
                                            $('.lightbox').fadeOut();
                                            //alert(copy_to_fname1+"+"+copy_from_id+"="+form_name+"="+'<?php echo $patientid; ?>');
                                            if(form_name != '' && typeof form_name != 'undefined'){
        //                                        alert("="+form_name+"=");
                                                $.ajax({
                                                    type: 'POST',
                                                    url: "copy_template_query.php",	
                                                    data: { 
                                                        copy_to_fname1  : copy_to_fname1,
                                                        copy_from_id    : copy_from_id,
                                                        form_name       : form_name,
                                                        pid             : '<?php echo $patientid; ?>'
                                                    },
                                                    success:function(data){
    //                                                    alert(data);
                                                        var newformid_array = data.split('-'); 
                                                        var newformid       = newformid_array[1];
                                                        if(newformid_array[1] == 'LBF2'){
                                                            $("#lbf_form_id").val(newformid);
                                                        }
        //                                                updateallids(newformid);

                                                        return false;
                                                    },
                                                    failure: function(response)
                                                    {
                                                        alert("error");
                                                    }		
                                                });
                                            }
                                        }
                                    }
                                    location.reload();
                                    return false;
                                });
                            }

                        },
                        failure: function(response)
                        {
                            alert("error");
                        }		
                });	
            }
        }

    </script>
    <?php
    $fuv_sql    = sqlStatement("SET SQL_BIG_SELECTS=1");
    $fuv_sql    = sqlStatement("SELECT DISTINCT (
                        form_encounter.encounter
                        ), form_encounter.facility, f.screen_group, form_encounter.pid,form_encounter.facility_id, form_encounter.encounter, form_encounter.pc_catid AS visitcategory_id, DATE_FORMAT( form_encounter.date,  '%Y-%m-%d' ) AS dos, form_encounter.provider_id AS provider_id,f.screen_names
                        FROM form_encounter
                        INNER JOIN patient_data ON patient_data.pid = form_encounter.pid
                        INNER JOIN tbl_allcare_facuservisit f ON  `facilities` REGEXP (
                        form_encounter.facility_id
                        )
                        AND  `users` REGEXP (
                        form_encounter.rendering_provider
                        )
                        AND  `visit_categories` REGEXP (
                        form_encounter.pc_catid
                        )
                        INNER JOIN layout_options l ON l.group_name = f.screen_group
                        AND l.form_id = f.form_id
                        WHERE  form_encounter.encounter='$encounter'  GROUP BY f.screen_group ORDER BY f.id DESC"); 
        $i=0;
        $j = 0;
        while ($fuv_row1 = sqlFetchArray($fuv_sql)){ 
    //          echo "<pre>";print_r($fuv_row1); echo "</pre>";
    //        if(substr($fuv_row1['screen_group'],1) == 'Dictation'){
                $codegrpname    = substr($fuv_row1['screen_group'],1);
                $result         = unserialize($fuv_row1['screen_names']);

                foreach($result as $key => $val) {
                    if (stripos($val, "Unused") == false) {
                        $sequence[$val] = $val;
                    }
                }
                sort($sequence);
                $j++;
    //        }
        }
        // Required fields to be displayed from LBF2 forms
        $required_fields            = '';
        $get_required_fields = sqlStatement("SELECT option_id FROM list_options WHERE list_id='Provider_Single_View_LBF2_Req_'");
        while ($set_required_fields = sqlFetchArray($get_required_fields)) {
            $required_fields    .= "'".$set_required_fields['option_id']."',";
        }

        // Required fields to be displayed from History forms
        $history_required_fields    = '';
        $get_history_required_fields = sqlStatement("SELECT option_id FROM list_options WHERE list_id='Provider_Single_View_HIS_Req_'");
        while ($set_history_required_fields = sqlFetchArray($get_history_required_fields)) {
            $history_required_fields    .= "'".$set_history_required_fields['option_id']."',";
        }

        $required_fields            = rtrim($required_fields,",");
        $history_required_fields    = rtrim($history_required_fields,",");

        // to get lbf2 form id 
        $lbf_form_id     = 0;
        //echo "SELECT form_id FROM forms WHERE deleted=0 AND formdir='LBF2' AND encounter = '$encounter' AND pid = '$patientid' ORDER by date asc LIMIT 0,1";
        $get_lbf_form_id = sqlStatement("SELECT form_id FROM forms WHERE deleted=0 AND formdir='LBF2' AND encounter = '$encounter' AND pid = '$patientid' ORDER by date asc LIMIT 0,1");
        while($set_lbf_form_id = sqlFetchArray($get_lbf_form_id)){
            $lbf_form_id = $set_lbf_form_id['form_id'];
        }

        $sql_pname = sqlStatement("SELECT CONCAT(lname,' ',fname) AS pname,DATE_FORMAT(DOB,'%m-%d-%Y') as DOB,DATE_FORMAT(FROM_DAYS(DATEDIFF(CURDATE(),DOB)), '%Y')+0 AS age,sex FROM  patient_data  WHERE pid=$patientid");
        $res_row1   = sqlFetchArray($sql_pname);
    //    echo "<b>Patient Name: </b>".$res_row1['pname'].'&nbsp;&nbsp;&nbsp;<span style="font-size:15px"><b>DOB:</b>'.$res_row1['DOB']."&nbsp;&nbsp;<b>AGE:</b>".$res_row1['age']."&nbsp;&nbsp;<b>GENDER:</b>".$res_row1['sex']."</span><br>";
    //    echo "<b>Encounter: </b>".$encounter;
    //    echo "<br /><br />";
//        $dos_sql        = sqlStatement("select * from form_encounter where encounter='".$encounter."'");
//        $res_dos        = sqlFetchArray($dos_sql);
//        $dos            = explode(" ",$res_dos['date']);
//        $dos  = $dos; //$dos;

        $cat        = sqlStatement("select * from openemr_postcalendar_categories where pc_catid='".$visit_category."'");
        $res_cat    = sqlFetchArray($cat);
        echo "<table style='border:0 !important; width:100%; border-collapse: separate; border-spacing: 13px; !important;' >";
        echo "<tr><td style='border:0 !important'><b>Patient Name: </b>".$res_row1['pname']."</td><td><b>DOB:</b>".$res_row1['DOB']."</td><td><b>AGE:</b>".$res_row1['age']."</td><td><b>GENDER:</b>".$res_row1['sex']."</td></tr>";
        echo "<tr><td style='border:0 !important'><b>Encounter: </b>".$encounter."</td><td style='border:0 !important'><b>Date Of Service: </b>".$dos."</td><td style='border:0 !important'><b>Visit Category: </b>".$res_cat['pc_catname']."</td></tr>";
        echo "</table><br>";

//        $patientid_sql    = sqlStatement("select pid from forms where encounter=$encounter AND form_name='New Patient Encounter' AND formdir='newpatient'");
//        $patientid_row    = sqlFetchArray($patientid_sql);
    //    $dos        = sqlStatement("SELECT DISTINCT fe.encounter as encounter, DATE_FORMAT( fe.date,  '%Y-%m-%d' ) AS date, openemr_postcalendar_categories.pc_catname
    //            FROM form_encounter AS fe
    //            LEFT JOIN openemr_postcalendar_categories ON fe.pc_catid = openemr_postcalendar_categories.pc_catid
    //            WHERE fe.pid = ?
    //            AND (
    //
    //            SELECT COUNT( f.encounter ) 
    //            FROM forms f
    //            WHERE fe.encounter = f.encounter and deleted = 0
    //            ) >1 order by fe.date desc", array($patientid_row['pid']));



         $date_of_service        = sqlStatement("SELECT DISTINCT fe.encounter as encounter, DATE_FORMAT( fe.date,  '%Y-%m-%d' ) AS date, openemr_postcalendar_categories.pc_catname
                FROM form_encounter AS fe
                LEFT JOIN openemr_postcalendar_categories ON fe.pc_catid = openemr_postcalendar_categories.pc_catid
                WHERE fe.pid = '$patientid'
                order by fe.date desc");

    //    $dos        = sqlStatement("SELECT fe.encounter, DATE_FORMAT(fe.date, '%Y-%m-%d') as date,openemr_postcalendar_categories.pc_catname FROM form_encounter AS fe ".
    //          " left join openemr_postcalendar_categories on fe.pc_catid=openemr_postcalendar_categories.pc_catid  WHERE fe.pid = ? order by fe.date desc", array($patientid_row['pid']));
        $gettemplate_old    = sqlStatement("SELECT copy_from_enc FROM tbl_allcare_template WHERE copy_to_enc = $encounter order by id desc limit 0,1");
        $template_old       = '';
        while ($settemplate_old = sqlFetchArray($gettemplate_old)) { 
            $template_old = $settemplate_old['copy_from_enc'];
        }
        ?>
        <b>Template From: </b>        
            <select id="template_from" name = "template_from" onchange="showforms();">
                <option value =" ">Select</option>
                <?php 
                while ($dos2 = sqlFetchArray($date_of_service)) { 
                    echo "<option value =".$dos2['encounter']."_".$dos2['date'];
                    if($template_old == $dos2['encounter'])
                        echo " selected ";
                    echo ">".$dos2['date']."-".$dos2['pc_catname'] ."</option>";
                }
               ?>
            </select>
        <br><br>
        <?php 
        $grpm   = sqlStatement("select * from tbl_visitcat_chartgrp_mapping where visit_category='".$visit_category."'");
        $grpres = sqlFetchArray($grpm);
        
        if($grpres['chart_group'] != ''){ 
            $chgrp  = substr($grpres['chart_group'],1);
            $mobile_sql     = sqlStatement("SELECT * 
                                    FROM  `tbl_chartui_mapping` 
                                    WHERE form_id =  'CHARTOUTPUT'
                                    AND group_name LIKE  '%$chgrp%'
                                    AND screen_name LIKE  '%$chgrp%'");
            while($mob_row1 = sqlFetchArray($mobile_sql)){
                $field_id       = 'form_'.$mob_row1['field_id'];
                $field_value    = $mob_row1['option_value']; 
                $res           .= $field_id .'='. $field_value.'&'; 
            }
            $datastring     = $res.'patientid'.'='.$patientid.'&'.'encounter_id'.'='.$encounter.'&'.'dos'.'='.$dos.'&'.'chartgroupshidden'.'='.$grpres['chart_group']; ?>
            <a href='javascript:; ' onclick="window.open('chartoutput/preview_charts.php?'+'<?php echo $datastring; ?>','popup','width=900,height=900,scrollbars=no,resizable=yes');" title='<?php echo substr($grpres['chart_group'],1); ?>' class='css_button_small'><span>Preview</span></a> 
           <?php 
        }else { 
            echo '<div style="text-align:center;margin-bottom: 11px;"><a class="btn btn-primary btn-md cssbtn" data-toggle="modal" data-target="#preview" data-urlhref = "chartoutput/preview_group.php?enc='.$encounter.'&pid='.$patientid.'&date='.$dos.'&grp=0" href="#"  ><span>Preview</span></a></div>';
        } ?>
        <form id="single_view" name="single_view">

            <input type='hidden' name='lbf_form_id' id='lbf_form_id' value="<?php echo $lbf_form_id; ?>" />
            <!-- Required for the popup date selectors -->
            <div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>
            <div id="template1"></div>
        <?php
        for($j=0; $j< count($sequence); $j++){
            $scr_val    = explode("$$",$sequence[$j]);
            $group      = $scr_val[2]; 
            $priority   = $scr_val[1];
            $order1     = $scr_val[0];
            if($group == 'quality_of_care' || $group == 'hpi' || $group == 'chief_complaint' || $group == 'progress_note' || $group == 'plan_note' || $group == 'assessment_note' || $group == 'cert_recert' || $group == 'face2face'){
                if($group == 'hpi')
                    $groupname = 'History of Present illness';
                else if($group == 'face2face')
                    $groupname = 'Face to Face HH Plan';
                else if($group == 'cert_recert')
                    $groupname = 'Certification_Recertification';
                else
                    $groupname = $group;
                    ?>
                    <div class="panel panel-default">
                    <?php
                    $fres = sqlStatement("SELECT * FROM layout_options WHERE form_id='LBF2' AND field_id IN($required_fields) AND group_name LIKE '%$groupname'");

                    $last_group = '';
                    $cell_count = 0;
                    $item_count = 0;
                    $display_style = 'block';

                    while ($frow = sqlFetchArray($fres)) {
                        $this_group = $frow['group_name'];
                        $titlecols  = $frow['titlecols'];
                        $datacols   = $frow['datacols'];
                        $data_type  = $frow['data_type'];
                        $field_id   = $frow['field_id'];
                        $list_id    = $frow['list_id'];

                        // Handle a data category (group) change.
                        if (strcmp($this_group, $last_group) != 0) {
                            $group_seq  = substr($this_group, 0, 1);
                            $group_name = substr($this_group, 1);
                            $last_group = $this_group;                            
                              echo "<div class='panel-heading'>";
                            $group_seq_esc = htmlspecialchars( $group_seq, ENT_QUOTES);
                            $group_name_show = htmlspecialchars( xl_layout_label($group_name), ENT_NOQUOTES);
                            $titleid = str_replace(" ","_",$group_name_show);
                            echo "<a data-toggle='collapse' class='panel-title' data-parent='#accordion' href='#div_$titleid'>$group_name_show</a></div><div id='div_$titleid' class='panel-collapse collapse in'><div class='panel-body'>";
                        }
                        ++$item_count;
                    }
                    ?>							
                    <?php
                    $fres = sqlStatement("SELECT * FROM layout_options WHERE form_id='LBF2' AND field_id IN($required_fields) AND group_name LIKE '%$groupname' ORDER BY seq");
                    $last_group = '';
                    $cell_count = 0;
                    $item_count = 0;
                    $display_style = 'block';

                    while ($frow = sqlFetchArray($fres)) {

                        $this_group = $frow['group_name'];
                        $titlecols  = $frow['titlecols'];
                        $datacols   = $frow['datacols'];
                        $data_type  = $frow['data_type'];
                        $field_id   = $frow['field_id'];
                        $list_id    = $frow['list_id'];
                        $group_id    = $frow['form_id'];

                        ?> 
                        <input type="hidden" id="hidden<?php echo $field_id; ?>" name="hidden<?php echo $field_id; ?>" value="<?php echo $data_type ; ?>" />
                        <input type="hidden" id="hiddentype<?php echo $field_id; ?>" name="hiddentype<?php echo $field_id; ?>" value="<?php echo $group_id ; ?>" />
                        <?php

                        $currvalue  = '';
                        //echo "select * from lbf_data where field_id='$field_id' AND form_id='".$lbf_form_id."'";
                        $res    = sqlstatement("select * from lbf_data where field_id='$field_id' AND form_id='".$lbf_form_id."'");
                        $frow1  = sqlFetchArray($res);
                        if($frow1['field_value'] != ''){
                            $currvalue = $frow1['field_value'];
                        }else {
                            if($data_type == 3){
                                $sql       = sqlStatement("select * from list_options where list_id='AllCareEncFormsAutoText' AND option_id='$field_id' order by seq");
                                $def       = sqlFetchArray($sql);
                                $currvalue = $def['notes']; 
                            }else {
                                $currvalue  = '';
                            }
                        }

                    // Handle a data category (group) change.
                      if (strcmp($this_group, $last_group) != 0) {
                       end_group();
                       $group_seq  = substr($this_group, 0, 1);
                       $group_name = substr($this_group, 1);
                       $last_group = $this_group;
                       $group_seq_esc = htmlspecialchars( $group_seq, ENT_QUOTES);
                        echo " <table border='0' cellpadding='0'>\n";
                        $display_style = 'none';
                      }
                      // Handle starting of a new row.
                      if (($titlecols > 0 && $cell_count >= $CPR) || $cell_count == 0) {
                        end_row();
                        echo " <tr>";
                      }

                      if ($item_count == 0 && $titlecols == 0) $titlecols = 1;

                      // Handle starting of a new label cell.
                      if ($titlecols > 0) {
                        end_cell();
                        $titlecols_esc = htmlspecialchars( $titlecols, ENT_QUOTES);
                        echo "<td width='70' valign='top' colspan='$titlecols_esc'";
                        echo ($frow['uor'] == 2) ? " class='required'" : " class='bold'";
                        if ($cell_count == 2) echo " style='padding-left:10pt'";
                        echo ">";
                        $cell_count += $titlecols;
                      }
                      ++$item_count;

                      echo "<b>";

                      // Modified 6-09 by BM - Translate if applicable
                      if ($frow['title']) echo (htmlspecialchars( xl_layout_label($frow['title']), ENT_NOQUOTES) . ":"); else echo "&nbsp;";

                      echo "</b>";

                      // Handle starting of a new data cell.
                      if ($datacols > 0) {
                        end_cell();
                        $datacols_esc = htmlspecialchars( $datacols, ENT_QUOTES);
                        echo "<td valign='top' colspan='$datacols_esc' class='text'";
                        if ($cell_count > 0) echo " style='padding-left:5pt'";
                        echo ">";
                        $cell_count += $datacols;
                      }

                      ++$item_count;
                     generate_form_field($frow, $currvalue);
                      echo "</div>";

                      }
                    end_group();

                    ?>
                    </div>
                </div>
            </div> <?php
            }
            else if($group == 'dme' || $group == 'surgeries' || $group == 'dental_problems' || $group == 'allergies' || $group == 'medication' || $group == 'medical_problem'){
                if($group == 'surgeries')
                    $grouptitle = 'surgery';
                else if($group == 'dental_problems')
                    $grouptitle = 'dental';
                else if($group == 'allergies' )
                    $grouptitle = 'allergy';
                else if($group == 'dme' )
                    $grouptitle = 'DME';
                else
                    $grouptitle = $group;

    //            echo "<div class='newwindow'><a href='javascript:;' onclick=win1('Issues/stats_full_custom.php?active=all&category=$grouptitle&encounter=$encounter&pid=$patientid&formid=$lbf_form_id&location=provider_portal&provider=$provider&isSingleView=1') ><span>".str_replace("_"," ", ucwords($group))."</span></a></div>";
                echo "<div class='newwindow' id='$grouptitle'><a href='Issues/stats_full_custom.php?active=all&category=$grouptitle&encounter=$encounter&pid=$patientid&formid=$lbf_form_id&location=provider_portal&provider=$provider&isSingleView=1&isFromCharts=$isFromCharts' onclick=win1(this) ><span>". ucwords(str_replace("_"," ",$group))."</span></a></div>";
            }
            else if($group == 'family_history' || $group == 'family_med_con' || $group == 'family_exam_test' || $group == 'history_past' || $group == 'history_social'){
                if($group == 'history_past'){
                    $groupname  = 'Past_Medical_History';
                }else if($group == 'family_history'){
                    $groupname  = 'Family_History';
                }else if($group == 'family_med_con'){
                    $group      = 'Family History Medical Conditi';
                    $groupname  = 'Primary_Family_Med_Conditions';
                }else if($group == 'family_exam_test'){
                    $groupname  = 'Tests_and_Exams';
                }else if($group == 'history_social'){
                    $groupname  = 'Social_History';
                }   

                $get_seq_fields = sqlStatement("SELECT group_name FROM layout_options WHERE form_id='HIS' AND group_name LIKE '%$groupname'");
                while ($set_seq_fields = sqlFetchArray($get_seq_fields)) {
                    $groupname = $set_seq_fields['group_name'];
                }

                ?>
                <div class="panel panel-default">
                    <?php
                    $fres = sqlStatement("SELECT * FROM layout_options WHERE form_id='HIS' AND field_id IN($history_required_fields) AND group_name LIKE '%$groupname' ORDER BY seq");

                    $last_group = '';
                    $cell_count = 0;
                    $item_count = 0;
                    $display_style = 'block';

                    while ($frow = sqlFetchArray($fres)) {
                        $this_group = $frow['group_name'];
                        $titlecols  = $frow['titlecols'];
                        $datacols   = $frow['datacols'];
                        $data_type  = $frow['data_type'];
                        $field_id   = $frow['field_id'];
                        $list_id    = $frow['list_id'];

                        // Handle a data category (group) change.
                        if (strcmp($this_group, $last_group) != 0) {
                          $group_seq  = substr($this_group, 0, 1);
                          $group_name = substr($this_group, 1);
                          $last_group = $this_group;
                              echo "<div class='panel-heading'>";
                              $group_seq_esc = htmlspecialchars( $group_seq, ENT_QUOTES);
                              $group_name_show = htmlspecialchars( xl_layout_label($group_name), ENT_NOQUOTES);
                              $titleid = str_replace(" ","_",$group_name_show);
                            echo "<a class='panel-title' data-toggle='collapse' data-parent='#accordion' href='#div_$titleid'>$group_name_show</a></div><div id='div_$titleid' class='panel-collapse collapse in'><div class='panel-body'>";
                        }
                        ++$item_count;
                    }
                    ?>							
                    <?php

                    $check_istest = '';

                    if($group == 'family_exam_test'){
                        $check_istest  = " OR (group_name LIKE '%Family History Exam Test') ";
                    }

                    $fres = sqlStatement("SELECT * FROM layout_options WHERE form_id IN('HIS','LBF2') AND field_id IN($history_required_fields,$required_fields) AND ((group_name LIKE '%$groupname') $check_istest OR (group_name LIKE '%$group')) ORDER BY group_name, seq");
                    $last_group = '';
                    $cell_count = 0;
                    $item_count = 0;
                    $display_style = 'block';

                    while ($frow = sqlFetchArray($fres)) {

                        $this_group = $groupname;
                        $titlecols  = $frow['titlecols'];
                        $datacols   = $frow['datacols'];
                        $data_type  = $frow['data_type'];
                        $field_id   = $frow['field_id'];
                        $list_id    = $frow['list_id'];
                        $group_id    = $frow['form_id'];
                        ?> 
                        <input type="hidden" id="hidden<?php echo $field_id; ?>" name="hidden<?php echo $field_id; ?>" value="<?php echo $data_type ; ?>" />
                        <input type="hidden" id="hiddentype<?php echo $field_id; ?>" name="hiddentype<?php echo $field_id; ?>" value="<?php echo $group_id ; ?>" />
                        <?php

                        $currvalue= '';
                        if($group_id == 'HIS')
                            $res = sqlstatement("select `$field_id` as field_value from history_data where pid = $patientid ORDER BY id DESC LIMIT 1 ");
                        else
                            $res    = sqlstatement("select * from lbf_data where field_id='$field_id' AND form_id='".$lbf_form_id."'");

                        //$res=sqlstatement("select `$field_id` as field_value from $table_name where pid = $patientid ORDER BY id DESC LIMIT 1 ");
                        $frow1 = sqlFetchArray($res);
                        if($frow1['field_value']!=''){
                             $currvalue=$frow1['field_value'];
                        }else {
                            if($data_type==3){
                                 $sql=sqlStatement("select * from list_options where list_id='AllCareEncFormsAutoText' AND option_id='$field_id' order by seq");
                                 $def=sqlFetchArray($sql);
                                 $currvalue=$def['notes']; 
                            }else {
                                $currvalue= '';
                            }
                        }

                      // Handle a data category (group) change.
                      //print_r($currvalue);

                    // Handle a data category (group) change.
                      if (strcmp($this_group, $last_group) != 0) {
                        end_group();
                       $group_seq  = substr($this_group, 0, 1);
                       $group_name = substr($this_group, 1);
                       $last_group = $this_group;
                       $group_seq_esc = htmlspecialchars( $group_seq, ENT_QUOTES);
                            //if($group_seq==6)	
                                echo "<div class='tab current' id='div_$group_seq_esc'>";
                            //else				
                                ///echo "<div class='tab' id='div_$group_seq_esc'>";
                        echo " <table border='0' cellpadding='0'>\n";
                        $display_style = 'none';
                      }
                      // Handle starting of a new row.
                      if (($titlecols > 0 && $cell_count >= $CPR) || $cell_count == 0) {
                        end_row();
                        echo " <tr>";
                      }

                      if ($item_count == 0 && $titlecols == 0) $titlecols = 1;

                      // Handle starting of a new label cell.
                      if ($titlecols > 0) {
                        end_cell();
                        $titlecols_esc = htmlspecialchars( $titlecols, ENT_QUOTES);
                        echo "<td width='70' valign='top' colspan='$titlecols_esc'";
                        echo ($frow['uor'] == 2) ? " class='required'" : " class='bold'";
                        if ($cell_count == 2) echo " style='padding-left:10pt'";
                        echo ">";
                        $cell_count += $titlecols;
                      }
                      ++$item_count;

                      echo "<b>";

                      // Modified 6-09 by BM - Translate if applicable
                      if ($frow['title']) echo (htmlspecialchars( xl_layout_label($frow['title']), ENT_NOQUOTES) . ":"); else echo "&nbsp;";

                      echo "</b>";

                      // Handle starting of a new data cell.
                      if ($datacols > 0) {
                        end_cell();
                        $datacols_esc = htmlspecialchars( $datacols, ENT_QUOTES);
                        echo "<td valign='top' colspan='$datacols_esc' class='text'";
                        if ($cell_count > 0) echo " style='padding-left:5pt'";
                        echo ">";
                        $cell_count += $datacols;
                      }

                      ++$item_count;
                     generate_form_field($frow, $currvalue);
                      echo "</div>";
                    }

                    end_group();
                    ?>
                    </div>
                </div>
            </div> <?php

            }
            else if($group == 'cpo' || $group == 'ccm' || $group == 'auditform' ){
                $static_form_id = 0;

                /* 
                 * To get ccm form / cpo form / auditform  
                 */
                $get_static_form_id = sqlStatement("SELECT form_id FROM forms WHERE encounter = $encounter AND pid = $patientid AND deleted = 0 AND formdir = '$group' ORDER BY DATE ASC LIMIT 1");
                while($set_static_form_id = sqlFetchArray($get_static_form_id)){
                    $static_form_id = $set_static_form_id['form_id'];
                }
                if($group != 'auditform')
                    $checkstring = "&formid=$lbf_form_id";
                else
                    $checkstring = '';
                //echo "<div class='newwindow'><a href='javascript:;' onclick=win1('$group/new_custom.php?encounter=$encounter&pid=$patientid&id=$static_form_id".$checkstring."&provider=$provider&location=provider_portal&isSingleView=1') ><span>".str_replace("_"," ", ucwords($group))."</span></a></div>";
                echo "<div class='newwindow' id='$group'><a href='$group/new_custom.php?encounter=$encounter&pid=$patientid&id=$static_form_id".$checkstring."&provider=".$_SESSION['portal_username']."&location=provider_portal&isSingleView=1&isFromCharts=$isFromCharts' onclick=win1(this) ><span>". ucwords(str_replace("_"," ",$group))."</span></a></div>";
            }
            else if( $group == 'procedure'){
                if($group == 'procedure')
                    $group = "procedure_order";
                $static_form_id = 0;

                /* 
                 * To get procedure form id 
                 */
                $get_static_form_id = sqlStatement("SELECT form_id FROM forms WHERE encounter = $encounter AND pid = $patientid AND deleted = 0 AND formdir = '$group' ORDER BY DATE ASC LIMIT 1");
                while($set_static_form_id = sqlFetchArray($get_static_form_id)){
                    $static_form_id = $set_static_form_id['form_id'];
                }

    //            echo "<div class='newwindow'><a href='javascript:;' onclick=win1('$group/new.php?encounter=$encounter&pid=$patientid&id=$static_form_id&formid=$lbf_form_id&provider=$provider&location=provider_portal&isSingleView=1') ><span>".str_replace("_"," ", ucwords($group))."</span></a></div>";
                echo "<div class='newwindow' id='$group'><a href='$group/new.php?encounter=$encounter&pid=$patientid&id=$static_form_id&formid=$lbf_form_id&provider=".$_SESSION['portal_username']."&location=provider_portal&isSingleView=1&isFromCharts=$isFromCharts' onclick=win1(this) ><span>". ucwords(str_replace("_"," ",$group))."</span></a></div>";

            }
            else if($group == 'codes'){
    //            echo "<div class='newwindow'><a href='javascript:;' onclick=win1('codes/feesheet_custom.php?pid=$patientid&encounter=$encounter&provider=$provider&location=provider_portal&isSingleView=1') ><span>".str_replace("_"," ", ucwords($group))."</span></a></div>";
                echo "<div class='newwindow' id='$group'><a href='codes/feesheet_custom.php?pid=$patientid&encounter=$encounter&provider=".$_SESSION['portal_username']."&location=provider_portal&isSingleView=1&isFromCharts=$isFromCharts' onclick=win1(this) ><span>". ucwords(str_replace("_"," ",$group))."</span></a></div>";
            }
            else if($group == 'ros'){
                $group = "allcare_ros";
                $form_name = 'Allcare Review of Systems';
                $ros_form_id = 0;

                /* 
                 * To get Allcare Review of Systems form id 
                 */
                $get_ros_form_id = sqlStatement("SELECT form_id FROM forms WHERE encounter = $encounter AND pid = $patientid AND deleted = 0 AND formdir = '$group' ORDER BY DATE ASC LIMIT 1");
                while($set_ros_form_id = sqlFetchArray($get_ros_form_id)){
                    $ros_form_id = $set_ros_form_id['form_id'];
                }

    //            echo "<div class='newwindow'><a href='javascript:;' onclick=win1('$group/view_custom.php?formname=$group&encounter=$encounter&pid=$patientid&id=$ros_form_id&provider=$provider&location=provider_portal&isSingleView=1') ><span>".$form_name."</span></a></div>";
                echo "<div class='newwindow' id='$group'><a href='$group/view_custom.php?formname=$group&encounter=$encounter&pid=$patientid&id=$ros_form_id&provider=".$_SESSION['portal_username']."&location=provider_portal&isSingleView=1&isFromCharts=$isFromCharts' onclick=win1(this) ><span>".$form_name."</span></a></div>";
            }
            else if($group == 'physical_exam'){
                $group = "allcare_physical_exam";
                $allcare_physical_exam_form_id = 0;

                /* 
                 * To get Allcare Physical Exam form id 
                 */
                $get_allcare_physical_exam_form_id = sqlStatement("SELECT form_id FROM forms WHERE encounter = $encounter AND pid = $patientid AND deleted = 0 AND formdir = '$group' ORDER BY DATE ASC LIMIT 1");
                while($set_allcare_physical_exam_form_id = sqlFetchArray($get_allcare_physical_exam_form_id)){
                    $allcare_physical_exam_form_id = $set_allcare_physical_exam_form_id['form_id'];
                }

                //echo "<div class='newwindow'><a href='javascript:;' onclick=win1('$group/new_custom.php?formname=$group&edit=custom_pe&encounter=$encounter&pid=$patientid&id=$allcare_physical_exam_form_id&menu_val=&provider=$provider&location=provider_portal&isSingleView=1') ><span>".str_replace("_"," ", ucwords($group))."</span></a></div>";
                echo "<div class='newwindow' id='$group'><a href='$group/new_custom.php?formname=$group&edit=custom_pe&encounter=$encounter&pid=$patientid&id=$allcare_physical_exam_form_id&menu_val=&provider=".$_SESSION['portal_username']."&location=provider_portal&isSingleView=1&isFromCharts=$isFromCharts' onclick=win1(this) ><span>". ucwords(str_replace("_"," ",$group))."</span></a></div>";
            }
            else if($group == 'vitals'){
                $formname = "Vitals";
                $vitals_form_id = 0;

                /* 
                 * To get Vitals form id 
                 */
                $get_vitals_form_id = sqlStatement("SELECT form_id FROM forms WHERE encounter = $encounter AND pid = $patientid AND deleted = 0 AND formdir = '$group' ORDER BY DATE ASC LIMIT 1");
                while($set_vitals_form_id = sqlFetchArray($get_vitals_form_id)){
                    $vitals_form_id = $set_vitals_form_id['form_id'];
                }

                /*
                 *  To get Vitals form status (Finalized/ Pending)
                 */
                $res12      = sqlStatement("SELECT form_id FROM forms where form_name ='Allcare Encounter Forms' AND encounter='$encounter' AND pid='$patientid' AND deleted=0 order by id desc");
                $frow_res   = sqlFetchArray($res12);
                $formid     = $frow_res['form_id'];
                $res1       = sqlStatement("SELECT * FROM lbf_data lb "
                                . "INNER JOIN layout_options l ON l.field_id=lb.field_id "
                                . "where lb.form_id='$formid' AND l.form_id='LBF2' AND l.group_name LIKE '%Vitals%' AND lb.field_id LIKE '%_stat%' order by seq");
                $res_row1   = sqlFetchArray($res1);
                $status     = $res_row1['field_value'];

    //            echo "<div class='newwindow'><a href='javascript:;' onclick=win1('$group/view_custom.php?formname=$formname&encounter=$encounter&pid=$patientid&id=$vitals_form_id&&status=$status&provider=$provider&location=provider_portal&isSingleView=1') ><span>".str_replace("_"," ", ucwords($group))."</span></a></div>";
                echo "<div class='newwindow' id='$group'><a href='$group/view_custom.php?formname=$formname&encounter=$encounter&pid=$patientid&id=$vitals_form_id&&status=$status&provider=".$_SESSION['portal_username']."&location=provider_portal&isSingleView=1&isFromCharts=$isFromCharts' onclick=win1(this) ><span>". ucwords(str_replace("_"," ",$group))."</span></a></div>";
            }
    //        else{
    //            echo $order1."==".$priority."==".$group."<br>";
    //        }
        }
        ?> </form> <?php


    $CPR = 4; // cells per row

    ?>
    <html>
    <head>
    <?php html_header_show();?>

    <link rel="stylesheet" href="<?php echo $css_header; ?>" type="text/css">

    <style type="text/css">@import url(../library/dynarch_calendar.css);</style>
    <!--<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>-->
    <?php include_once("{$GLOBALS['srcdir']}/ajax/facility_ajax_jav.inc.php"); ?>
    <link rel="stylesheet" href="../interface/themes/jquery-ui.css" type="text/css">
    <link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['webroot'] ?>/library/js/fancybox-1.3.4/jquery.fancybox-1.3.4.css" media="screen" />
    <script type="text/javascript">
    $(document).ready(function(){

        tabbify();
        enable_modals();
        // special size for
            $(".medium_modal").fancybox( {
                    'overlayOpacity' : 0.0,
                    'showCloseButton' : true,
                    'frameHeight' : 460,
                    'frameWidth' : 650
            });

    });

    function popUp(URL) {
     day = new Date();
     id = day.getTime();
     top.restoreSession();
     eval("page" + id + " = window.open(URL, '" + id + "', 'toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=1,width=400,height=300,left = 440,top = 362');");
    }


    // Compute the length of a string without leading and trailing spaces.
    function trimlen(s) {
     var i = 0;
     var j = s.length - 1;
     for (; i <= j && s.charAt(i) == ' '; ++i);
     for (; i <= j && s.charAt(j) == ' '; --j);
     if (i > j) return 0;
     return j + 1 - i;
    }

    function validate(f) {
     var errCount = 0;
     var errMsgs = new Array();
    <?php generate_layout_validation('UCA'); ?>

     var msg = "";
     msg += "<?php xl('The following fields are required', 'e' ); ?>:\n\n";
     for ( var i = 0; i < errMsgs.length; i++ ) {
            msg += errMsgs[i] + "\n";
     }
     msg += "\n<?php xl('Please fill them in before continuing.', 'e'); ?>";

     if ( errMsgs.length > 0 ) {
            alert(msg);
     }
     return errMsgs.length < 1;
    }


    </script>
    <style>

    body{
        font-size: 1.7em;
        background: #fff !important;
    }
    input[type="radio"], input[type="checkbox"] {
        line-height: normal;
        margin: 4px 5px 0;
    }
    .page-header{margin: 0px;}
    #savealert{
        background-color: #616161;
        border-radius: 4px;
        color: #fff;
        display: none;
        left: 50%;
        margin-left: -75px;
        padding: 2px 11px 5px;
        position: fixed;
        text-align: center;
        top: 10px;
        display: none;

    }
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

    .ui-widget {
      font-size: 1.0em !important;
    /*  border: 1px solid #FFFFFF !important;*/
    }

    .ui-widget-content {
      border: 0px solid  !important;
      background: #FFDAB9 !important;
      color: #000000 !important;
    }
    .ui-dialog .ui-dialog-titlebar-close {
      position: absolute;
      top: -12px !important;
      right: -15px !important;
      height: 30px !important;
      width: 30px !important;
      cursor: pointer !important;
      z-index: 999 !important;
      background: url('../library/js/fancybox/fancy_closebox.png') top left no-repeat !important;
    }
    .lightbox{
        background-color: #000;
        width: 100%;
        position: absolute;
        top:0px;
        left:0px;
        z-index: 998 !important;
        opacity: 0.60;
        -ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity=60)";
    }
    .ui-dialog{
        border: 3px solid #000 !important;
        border-radius: 5px;
        z-index: 999 !important;
    }

    .round-button {
        display:block;
        width:40px;
        height:40px;
        line-height:35px;
        border: 2px solid #f5f5f5;
        border-radius: 50%;
        color:#f5f5f5;
        text-align:center;
        text-decoration:none;
        background: #464646;
        box-shadow: 0 0 3px gray;
        font-size:20px;
        font-weight:bold;
        margin-top: 16px;
        cursor: pointer;
    }
    .round-button:hover {
        background: #262626;
    }

    .ui-dialog .ui-dialog-titlebar-close {
      position: absolute;
      top: -12px !important;
      right: -15px !important;
      height: 30px !important;
      width: 30px !important;
      background: url('../library/js/fancybox/fancy_closebox.png') top left no-repeat !important;
      cursor: pointer !important;
      z-index: 181 !important;
      border: 0px;
    }

    .ui-dialog .ui-dialog-titlebar-close .ui-button-text{display: none;}
    .cssbtn{
        color: #fff !important;
    }
    #f2fdiv > div {
        padding-bottom: 41px;
    }
    .panel-title {
        display: block;
        padding: 7px 15px;
        text-decoration: none;
    }

    .panel-heading {
        padding: 0;
    }

    .panel-heading a:after {
        font-family:'Glyphicons Halflings';
        content:"\e114";
        float: right;
        color: grey;
    }
    .panel-heading a.collapsed:after {
        content:"\e080";
    }
    .panel-title:hover,.panel-title:active,.panel-title:focus{
        text-decoration: none;
        color: #333;
    }

    .panel-default > .panel-heading {
        background-color: #d9edf7;
        border-color: #bce8f1;
        color: #333;
    }

    </style>
    </head>

    <body class="body_top">

    


    <br>

    <script language="JavaScript">
    <?php echo $date_init; ?>
    </script>

    <!-- include support for the list-add selectbox feature -->
    <?php include $GLOBALS['fileroot']."/library/options_listadd.inc"; ?>
    <div id="savealert"></div>
    <div class="modal fade" name = "preview" id="preview" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="vertical-alignment-helper">
            <div class="modal-dialog vertical-align-center modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
                         <h4 class="modal-title" id="myModalLabel">Preview</h4>
                    </div>
                    <div class="modal-body">
                        <div id="frame">show</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/fancybox-1.3.4/jquery.fancybox-1.3.4.pack.js"></script>
    <script src="//code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
    <script type="text/javascript" src="../library/overlib_mini.js"></script>
    <script type="text/javascript" src="assets/js/bootstrap.min.js"></script>
    <script>
        var modalwindow = null;
        $(function(){

            $(document).on('show.bs.modal',"#preview", function (event) {
                    var target = $(event.relatedTarget);
                    modalwindow = $(this);
                    var modal = $(this);
                    var frameurl = target.data('urlhref');
                    modal.find('#frame').html('<iframe src="'+frameurl+'" frameborder="0" width="100%"></iframe> ')
            });
        })
        function hidemodal(){
            modalwindow.modal('hide');
        }
        window.onload = function(){
            if( $('.panel.panel-default').first().children().length == 0)
                $('.panel.panel-default').first().remove();
        }
    </script>
    </body>

    </html>

<?php } ?>