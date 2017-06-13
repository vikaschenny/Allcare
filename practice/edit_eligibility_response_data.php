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


$pagename = "eligibility_response"; 

require_once("$srcdir/api.inc");
require_once("$srcdir/forms.inc");
require_once("$srcdir/options.inc.php");
require_once("$srcdir/patient.inc");
require_once("$srcdir/formatting.inc.php");
require_once("$srcdir/billing.inc");
include_once("$srcdir/calendar.inc");
include_once("$srcdir/edi.inc");


//for logout
$_SESSION['portal_username']= $_REQUEST['provider'];
$sql = sqlStatement("SELECT id, fname, lname, specialty FROM users " .
      "WHERE active = 1 AND ( info IS NULL OR info NOT LIKE '%Inactive%' ) " .
      "AND authorized = 1 AND username='".$provider."'" .
      "ORDER BY lname, fname");
$id         = sqlFetchArray($sql);

$pid        = $_REQUEST['pid'];

$patient_sql = sqlStatement("SELECT CONCAT(lname,' ', fname) as patientname, dob FROM patient_data WHERE pid = $pid");
$getpatientname         = sqlFetchArray($patient_sql);
$patient_name           = $getpatientname['patientname'];
$patient_dob            = $getpatientname['dob'];
?>
<link rel="stylesheet" href="<?php echo $css_header; ?>" type="text/css">
<style type="text/css">@import url(../../../library/dynarch_calendar.css);</style>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/jquery.js"></script>
<script src="//code.jquery.com/jquery-1.12.0.min.js"></script>
<style type="text/css">@import url(../../../library/dynarch_calendar.css);</style>
<script type="text/javascript" src="../../../library/dynarch_calendar.js"></script>
<?php include_once("{$GLOBALS['srcdir']}/dynarch_calendar_en.inc.php"); ?>
<script type="text/javascript" src="../../../library/dynarch_calendar_setup.js"></script>
<html>
    <head>
        <script>
            $(document).ready(function(){
                $("#single_view").append($('.newwindow'));
                // adding on change attribute to all textarea fields in form dynamically
                $('textarea').bind("focusout", function() {
                    var field_id    = this.id;
                    var field_val   = $("#"+field_id).val();
                    var type        = $("#hiddentype"+field_id.replace("form_", "")).val();
                    var  form_id    = $("#lbf_form_id").val();
                    var pid         = "<?php echo $pid; ?>";
                    
                    // insurance data
                    var patient_bal     = $("#patient_bal").val();
                    var insurance_bal   = $("#insurance_bal").val();
                    var total_bal       = $("#total_bal").val();
                    var month           = $("#month_value").val();
                    
                    
                    var fieldname  = {
                                        pid             : pid, 
                                        type            : type, 
                                        form_id         : form_id,
                                        field_id        : field_id,
                                        field_val       : field_val,
                                        patient_bal     : patient_bal,
                                        insurance_bal   : insurance_bal,
                                        total_bal       : total_bal,
                                        month           : month
                                    };
                    ajaxcall(fieldname);
                });

                // adding on change attribute to all select fields in form dynamically
                $('select').bind("change", function() {
                    var field_id    = this.id;
                    var field_val   = $("#"+field_id).val();
                    var type        = $("#hiddentype"+field_id.replace("form_", "")).val();

                    var  form_id    = $("#lbf_form_id").val();

                    var pid             = "<?php echo $pid; ?>";
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
                    // insurance data
                    var patient_bal     = $("#patient_bal").val();
                    var insurance_bal   = $("#insurance_bal").val();
                    var total_bal       = $("#total_bal").val();
                    var month           = $("#month_value").val();
                    
                    
                    var fieldname  = {
                                        pid             : pid, 
                                        type            : type, 
                                        form_id         : form_id,
                                        field_id        : field_id,
                                        field_val       : field_val,
                                        patient_bal     : patient_bal,
                                        insurance_bal   : insurance_bal,
                                        total_bal       : total_bal,
                                        month           : month
                                    };
                    ajaxcall(fieldname); 
                });
                
                $('input').bind('focusout', function(event) {
                   // if($(this).is(":focus")){
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
                        var pid         = "<?php echo $pid; ?>";

                        // insurance data
                        var patient_bal     = $("#patient_bal").val();
                        var insurance_bal   = $("#insurance_bal").val();
                        var total_bal       = $("#total_bal").val();
                        var month           = $("#month_value").val();


                        var fieldname  = {
                                            pid             : pid, 
                                            type            : type, 
                                            form_id         : form_id,
                                            field_id        : field_id,
                                            field_val       : field_val,
                                            patient_bal     : patient_bal,
                                            insurance_bal   : insurance_bal,
                                            total_bal       : total_bal,
                                            month           : month
                                        };
            //                            alert(encounter+"="+pid+"="+type+"="+form_id+"="+string_label+"="+field_val);
                        ajaxcall(fieldname);
                    //}
                });
                // date time datatype picker

                $("input[placeholder*='date and time']").blur(function() {

                    var field_string_split    = this.id;
                    var field_id              = field_string_split.replace("form_", "");
                    var type                  = $("#hiddentype"+field_id).val();
                    var field_val             = $(this).val();
                    var  form_id    = $("#lbf_form_id").val();
                    var pid         = "<?php echo $pid; ?>";

                    // insurance data
                    var patient_bal     = $("#patient_bal").val();
                    var insurance_bal   = $("#insurance_bal").val();
                    var total_bal       = $("#total_bal").val();
                    var month           = $("#month_value").val();
                    
                    
                    var fieldname  = {
                                        pid             : pid, 
                                        type            : type, 
                                        form_id         : form_id,
                                        field_id        : field_id,
                                        field_val       : field_val,
                                        patient_bal     : patient_bal,
                                        insurance_bal   : insurance_bal,
                                        total_bal       : total_bal,
                                        month           : month
                                    };
        //                            alert(encounter+"="+pid+"="+type+"="+form_id+"="+string_label+"="+field_val);
                    ajaxcall(fieldname);
                });

                $('.newwindow a,.tabNav .current a').click(function(evt){evt.preventDefault();});
                // image of date object
                $( "img" ).click(function() {
                    var field_id    = this.id;
                    $('.calendar .daysrow .day').click(function(){
                        var field_val   = $("#"+field_id).prev().val();
//                        var field_val   = $("#"+field_id).val();
                        var type        = $("#hiddentype"+field_id.replace("img_", "")).val();
                        var field_id2   = field_id.replace("img_", "");

                        var  form_id    = $("#lbf_form_id").val();

                        var pid             = "<?php echo $pid; ?>";
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
                        // insurance data
                        var patient_bal     = $("#patient_bal").val();
                        var insurance_bal   = $("#insurance_bal").val();
                        var total_bal       = $("#total_bal").val();
                        var month           = $("#month_value").val();
                        field_id        = field_id.replace("img_","form_");

                        var fieldname  = {
                                            pid             : pid, 
                                            type            : type, 
                                            form_id         : form_id,
                                            field_id        : field_id,
                                            field_val       : field_val,
                                            patient_bal     : patient_bal,
                                            insurance_bal   : insurance_bal,
                                            total_bal       : total_bal,
                                            month           : month
                                        };
                        ajaxcall(fieldname);
                    });
                });
                var lastFocused;
                $(window).bind("beforeunload", function() { 
                   window.opener.datafromchildwindow($("#lbf_form_id").val(),'<?php echo $pid; ?>');window.close();
                });
            });
            function ajaxcall(fieldname){
             $('#savealert').html("<div>Saving...</div>");
                $.ajax({
                    type: "POST",
                    url: "save_eligibility_result_data.php",
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
        </script>
    </head>
    <body>
        <form method='post' name='theform' id='theform' >
            <?php 
                $lbf_form_id = $_REQUEST['form_id'];
            ?>
            <input type='hidden' name='month_value' id='month_value' value="<?php echo $_REQUEST['month_value']; ?>" />
            <input type='hidden' name='lbf_form_id' id='lbf_form_id' value="<?php echo $lbf_form_id; ?>" />
            <div class="section-header">
                <h4><b> <?php xl("Eligibility Data Screen", "e" )?></b></h4>
            </div>
            <div id="info">
                 <fieldset>
                    <legend>Patient Details</legend>
                    <div id="ptinfo">Patient Name: <span class="bold"><?php echo $patient_name; ?></span><br>
                    Date of birth: <span class="bold"><?php echo $patient_dob; ?></span></div>
                </fieldset>
                <fieldset>
                    <legend>Billing</legend>
                    <?php
                    $patientbalance = get_patient_balance($pid, false);
                    //Debit the patient balance from insurance balance
                    $insurancebalance = get_patient_balance($pid, true) - $patientbalance;
                    $totalbalance=$patientbalance + $insurancebalance;
                    // Show current balance and billing note, if any.
                    echo "<table border='0'><tr><td>" .
                    "<table ><tr><td><span class='bold'><font color='red'>" .
                     xlt('Patient Balance Due') .
                     " : " . text(oeFormatMoney($patientbalance)) .
                     "</font></span></td></tr>".
                       "<tr><td><span class='bold'><font color='red'>" .
                     xlt('Insurance Balance Due') .
                     " : " . text(oeFormatMoney($insurancebalance)) .
                     "</font></span></td></tr>".
                     "<tr><td><span class='bold'><font color='red'>" .
                     xlt('Total Balance Due').
                     " : " . text(oeFormatMoney($totalbalance)) .
                        "</font></span></td></td></tr>";
                      if ($result['genericname2'] == 'Billing') {
                           echo "<tr><td><span class='bold'><font color='red'>" .
                            xlt('Billing Note') . ":" .
                            text($result['genericval2']) .
                            "</font></span></td></tr>";
                      } 
                    echo "</table></td></tr></td></tr></table>";
                    ?>
                    <input type="hidden" name="patient_bal" id="patient_bal" value="<?php echo $patientbalance?>">
                    <input type="hidden" name="insurance_bal" id="insurance_bal" value="<?php echo $insurancebalance?>">
                    <input type="hidden" name="total_bal" id="total_bal" value="<?php echo $totalbalance?>">
                </fieldset>
                </div>
                <div id='f2fdiv'>
                    <div id="Face_To_Face">
                        <?php //
                        $fres = sqlStatement("SELECT DISTINCT group_name FROM layout_options WHERE form_id='ELIGIBILITY' ORDER BY group_name");

                        $last_group = '';
                        $cell_count = 0;
                        $item_count = 0;
                        $display_style = 'block';

                        while ($frow = sqlFetchArray($fres)) {
                            $this_group_header[] = $frow['group_name'];
                        }
                        if(!empty($this_group_header)){
                        for($i=0; $i< count($this_group_header); $i++){
                            echo '<ul class="tabNav">';
                                $group_header_seq  = substr($this_group_header[$i], 0, 1);
                                $group_header_name = substr($this_group_header[$i], 1);
                                $group_header_seq_esc = htmlspecialchars( $group_header_seq, ENT_QUOTES);
                                $group_header_name_show = htmlspecialchars( xl_layout_label($group_header_name), ENT_NOQUOTES);
                                echo "<li class='current'>";
                                echo "<a href='' id='$group_header_seq_esc'>$group_header_name_show</a></li>";
                            echo "</ul>";
                            ?>
                        <div class="tabContainer">							
                            <?php
                            
                                $fres = sqlStatement("SELECT * FROM layout_options WHERE form_id='ELIGIBILITY' AND group_name = '$this_group_header[$i]' ORDER BY group_name, seq");
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
                                    $group_id   = substr($this_group,1);

                                    ?> 
                                    <input type="hidden" id="hidden<?php echo $field_id; ?>" name="hidden<?php echo $field_id; ?>" value="<?php echo $data_type ; ?>" />
                                    <input type="hidden" id="hiddentype<?php echo $field_id; ?>" name="hiddentype<?php echo $field_id; ?>" value="<?php echo $group_id ; ?>" />
                                    <?php

                                    $currvalue  = '';
                                    $get_form_data = sqlStatement("SELECT `$field_id` as field_value FROM tbl_eligibility_response_data WHERE id = $lbf_form_id");
                                    $frow1  = sqlFetchArray($get_form_data);
                                    if($frow1['field_value'] != ''){
                                        $currvalue = $frow1['field_value'];
                                    }else {
                                        $currvalue  = '';
                                    }

                                // Handle a data category (group) change.
                                    if (strcmp($this_group, $last_group) != 0) {
                                        end_group();
                                        $group_seq  = substr($this_group, 0, 1);
                                        $group_name = substr($this_group, 1);
                                        $last_group = $this_group;
                                        $group_seq_esc = htmlspecialchars( $group_seq, ENT_QUOTES);
                                        echo "<div class='tab current' id='div_$group_seq_esc'>";
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
                        <?php 
                        }
                    } ?>
                    </div>
                </div> 
        </form>
        <style>
            #savealert{
                background-color: #616161;
                border-radius: 4px;
                color: #fff;
                display: none;
                height: 20px;
                left: 50%;
                margin-left: -75px;
                padding: 5px 14px 5px;
                position: fixed;
                text-align: center;
                top: 10px;
                width: 65px;
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
                min-height: auto;
                width: 100%;
            }
            
            div.tabContainer{
                width: 99%;
            }
            div.tabContainer div.tab {
                padding: 10px 0 10px 10px;
            }
            div.tab table td[class=bold] {
                padding-bottom: 0;
                padding-right: 1px;
                width: auto;
            }
            
            #info fieldset {
                display: inline;
                height: 79px;
                margin-bottom: 10px;
                min-width: 249px;
                vertical-align: top;
                border-radius: 8px;
            }
            
            #ptinfo {
                font-size: 15px;
                margin-top: 5px;
            }
            </style>
        <?php 
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
        <br>

        <script language="JavaScript">
        <?php echo $date_init; ?>
        </script>

         <div id="savealert"></div>
    </body>
</html>