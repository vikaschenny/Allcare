<?php
//SANITIZE ALL ESCAPES
$sanitize_all_escapes=true;

//STOP FAKE REGISTER GLOBALS
$fake_register_globals=false; 

require_once("../globals.php");
require_once("../../library/formdata.inc.php"); 
require_once("../../library/globals.inc.php");
require_once("$srcdir/api.inc");
require_once("$srcdir/forms.inc");
require_once("$srcdir/options.inc.php");
require_once("$srcdir/patient.inc");
require_once("$srcdir/formdata.inc.php");
require_once("$srcdir/formatting.inc.php");

$encounter              = $_REQUEST['encounter'];
$pid                    = $_REQUEST['pid'];
//$_SESSION['encounter']  = $encounter;
//$_SESSION['pid']        = $pid;
//echo "<pre>"; print_r($_REQUEST); echo "</pre>";
?>
<br>
    <div class="section-header">
        <span class="text"><b> <?php xl("Encounter Forms Single Page View", "e" )?></b></span>
    </div>
<br>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/jquery.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/common.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/jquery.1.3.2.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/fancybox/jquery.fancybox-1.2.6.js"></script>
<script type="text/javascript" src="../../../library/dialog.js"></script>
<script type="text/javascript" src="../../../library/textformat.js"></script>
<script type="text/javascript" src="../../../library/dynarch_calendar.js"></script>
<?php include_once("{$GLOBALS['srcdir']}/dynarch_calendar_en.inc.php"); ?>
<script type="text/javascript" src="../../../library/dynarch_calendar_setup.js"></script>
<script>
    $(document).ready(function(){
        $("#single_view").append($('.newwindow'));
        // adding on change attribute to all textarea fields in form dynamically
        $('textarea').bind("blur", function() {
            var field_id    = this.id;
            var field_val   = $("#"+field_id).val();
            var type        = $("#hiddentype"+field_id.replace("form_", "")).val();
            var  form_id    = $("#lbf_form_id").val();
//            var form_id;
//            if(type == 'LBF2')
//                form_id     = $("#lbf_form_id").val();
//            if(type == 'HIS')
//                form_id     = 0;
            var encounter   = "<?php echo $encounter; ?>";
            var pid         = "<?php echo $pid; ?>";
            
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
        
        // adding on change attribute to all select fields in form dynamically
        $('select').bind("change", function() {
            var field_id    = this.id;
            var field_val   = $("#"+field_id).val();
            var type        = $("#hiddentype"+field_id.replace("form_", "")).val();
            
//            var form_id;
//            if(type == 'LBF2')
            var  form_id     = $("#lbf_form_id").val();
//            if(type == 'HIS')
//                form_id     = 0;
            
            var encounter       = "<?php echo $encounter; ?>";
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
            var fieldname   = {
                                encounter   : encounter, 
                                pid         : pid, 
                                type        : type, 
                                form_id     : form_id,
                                field_id    : field_id,
                                field_val   : field_val
                            };
            ajaxcall(fieldname);
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
            
//            var form_id;
//            if(type == 'LBF2')
//                form_id     = $("#lbf_form_id").val();
//            if(type == 'HIS')
//                form_id     = 0;
            var  form_id     = $("#lbf_form_id").val();
            
            var encounter   = "<?php echo $encounter; ?>";
            var pid         = "<?php echo $pid; ?>";
            
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
//            alert($(this).val());
                var field_string_split        = this.id;
                var string_label          = field_string_split.replace("form_", "");
                var type                  = $("#hiddentype"+string_label).val();
                var field_val             = $(this).val();
//                var form_id;
//                if(type == 'LBF2')
//                    form_id     = $("#lbf_form_id").val();
//                if(type == 'HIS')
//                    form_id     = 0;
                var  form_id     = $("#lbf_form_id").val();
                var encounter   = "<?php echo $encounter; ?>";
                var pid         = "<?php echo $pid; ?>";

                var fieldname   = {
                                    encounter   : encounter, 
                                    pid         : pid, 
                                    type        : type, 
                                    form_id     : form_id,
                                    field_id    : string_label,
                                    field_val   : field_val
                                };
//                                alert(encounter+"="+pid+"="+type+"="+form_id+"="+string_label+"="+field_val);
                ajaxcall(fieldname);
        });
        // image of date object
         $( "img" ).click(function() {
            var field_id    = this.id;
            
            $('.calendar .day').click(function(){
                
                var field_val   = $("#"+field_id).val();
                var type        = $("#hiddentype"+field_id.replace("img_", "")).val();
                var field_id2   = field_id.replace("img_", "");

//                var form_id;
//                if(type == 'LBF2')
//                    form_id     = $("#lbf_form_id").val();
//                if(type == 'HIS')
//                    form_id     = 0;
                var  form_id     = $("#lbf_form_id").val();

                var encounter       = "<?php echo $encounter; ?>";
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
            window.opener.location.href = 'incomplete_charts.php?encounter=<?php echo $encounter; ?>';
        }
    }
    var isChrome = /Chrome/.test(navigator.userAgent) && /Google Inc/.test(navigator.vendor);
    isChrome?window.onunload = RefreshParent:window.onbeforeunload = RefreshParent; 
    
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
        $codegrpname    = substr($fuv_row1['screen_group'],1);
        $result         = unserialize($fuv_row1['screen_names']);

        foreach($result as $key => $val) {
            if (stripos($val, "Unused") == false) {
                $sequence[$val] = $val;
            }
        }
        sort($sequence);
        $j++;
    }
    // Required fields to be displayed from LBF2 forms
    $required_fields            = '';
    $get_required_fields = sqlStatement("SELECT option_id FROM list_options WHERE list_id='Single_View_LBF2_Req_fields'");
    while ($set_required_fields = sqlFetchArray($get_required_fields)) {
        $required_fields    .= "'".$set_required_fields['option_id']."',";
    }
    
    // Required fields to be displayed from History forms
    $history_required_fields    = '';
    $get_history_required_fields = sqlStatement("SELECT option_id FROM list_options WHERE list_id='Single_View_HIS_Req_fields'");
    while ($set_history_required_fields = sqlFetchArray($get_history_required_fields)) {
        $history_required_fields    .= "'".$set_history_required_fields['option_id']."',";
    }
    
    $required_fields            = rtrim($required_fields,",");
    $history_required_fields    = rtrim($history_required_fields,",");
    
    // to get lbf2 form id 
    $lbf_form_id     = 0;
    $get_lbf_form_id = sqlStatement("SELECT form_id FROM forms WHERE deleted=0 AND formdir='LBF2' AND encounter = '$encounter' AND pid = '$pid' ORDER by date asc LIMIT 0,1");
    while($set_lbf_form_id = sqlFetchArray($get_lbf_form_id)){
        $lbf_form_id = $set_lbf_form_id['form_id'];
    }
    $sql_pname = sqlStatement("SELECT CONCAT(lname,' ',fname) AS pname FROM  patient_data  WHERE pid=$pid");
    $res_row1   = sqlFetchArray($sql_pname);
    $dos_sql=sqlStatement("select * from form_encounter where encounter='".$_REQUEST['encounter']."'");
    $res_dos=sqlFetchArray($dos_sql);
    $dos=explode(" ",$res_dos['date']);

    $cat=sqlStatement("select * from openemr_postcalendar_categories where pc_catid='".$res_dos['pc_catid']."'");
    $res_cat=sqlFetchArray($cat);
    echo "<table style='border:0 !important'>";
    echo "<tr><td style='border:0 !important'><b>Patient Name: </b>".$res_row1['pname']."</td><td>&nbsp;</td><td style='border:0 !important'><b>Encounter: </b>".$_REQUEST['encounter']."</td></tr>";
    echo "<tr><td style='border:0 !important'><b>Date Of Service: </b>".$dos[0]."</td><td>&nbsp;</td><td style='border:0 !important'><b>Visit Category: </b>".$res_cat['pc_catname']."</td></tr>";
    echo "</table><br>";
    ?>

    <form id="single_view" name="single_view">
       
        <input type='hidden' name='lbf_form_id' id='lbf_form_id' value="<?php echo $lbf_form_id; ?>" />
    <?php
    for($j=0; $j< count($sequence); $j++){
        $scr_val    = explode("$$",$sequence[$j]);
        $group   = $scr_val[2]; 
        $priority   = $scr_val[1];
        $order1     = $scr_val[0];
        if($group == 'quality_of_care' || $group == 'hpi' || $group == 'chief_complaint' || $group == 'progress_note' || $group == 'plan_note' || $group == 'assessment_note' || $group == 'cert_recert' || $group == 'face2face'){
            if($group == 'hpi')
                $groupname = 'History of Present illness';
            else if($group == 'face2face')
                $groupname = 'Face to Face HH Plan';
            else
                $groupname = $group;
                ?>
                <div id='f2fdiv'>
                    <div id="Face_To_Face">
                        <ul class="tabNav">
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
                        //if($group_seq==6)	
                          echo "<li class='current'>";
                        //else				echo "<li class=''>";
                        $group_seq_esc = htmlspecialchars( $group_seq, ENT_QUOTES);
                        $group_name_show = htmlspecialchars( xl_layout_label($group_name), ENT_NOQUOTES);
                        echo "<a href='' id='div_$group_seq_esc'>".
                            "$group_name_show</a></li>";
                    }
                    ++$item_count;
                }
                ?>
                </ul>
                <div class="tabContainer">							
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
                    </div></div>
                </div> <?php
        }else if($group == 'dme' || $group == 'surgeries' || $group == 'dental_problems' || $group == 'allergies' || $group == 'medication' || $group == 'medical_problem'){
            if($group == 'surgeries')
                $grouptitle = 'surgery';
            else if($group == 'dental_problems')
                $grouptitle = 'dental';
            else if($group == 'allergies' )
                $grouptitle = 'allergy';
            else
                $grouptitle = $group;
                
            echo "<div class='newwindow'><a href='javascript:;' onclick=win1('../patient_file/summary/stats_full_custom.php?active=all&category=$grouptitle&encounter=$encounter&pid=$pid&formid=$lbf_form_id&isSingleView=1') ><span>". ucwords(str_replace("_"," ",$group))."</span></a></div>";
            
        }else if($group == 'family_history' || $group == 'family_med_con' || $group == 'family_exam_test' || $group == 'history_past' || $group == 'history_social'){
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
            <div id='f2fdiv'>
                <div id="Face_To_Face">
                    <ul class="tabNav">
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
                          //if($group_seq==6)	
                            echo "<li class='current'>";
                          //else				echo "<li class=''>";
                          $group_seq_esc = htmlspecialchars( $group_seq, ENT_QUOTES);
                          $group_name_show = htmlspecialchars( xl_layout_label($group_name), ENT_NOQUOTES);
                          echo "<a href='' id='div_$group_seq_esc'>".
                              "$group_name_show</a></li>";
                    }
                    ++$item_count;
                }
                ?>
                </ul>
                <div class="tabContainer">							
                <?php
                $check_istest = '';
                
                if($group == 'family_exam_test'){
                    $check_istest  = " OR (group_name LIKE '%Family History Exam Test') ";
                }
                
                $fres = sqlStatement("SELECT * FROM layout_options WHERE form_id IN('HIS','LBF2') AND field_id IN($history_required_fields,$required_fields) AND ((group_name = '$groupname') $check_istest OR (group_name LIKE '%$group'))  ORDER BY group_name, seq");
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
                        $res = sqlstatement("select `$field_id` as field_value from history_data where pid = $pid ORDER BY id DESC LIMIT 1 ");
                    else
                        $res    = sqlstatement("select * from lbf_data where field_id='$field_id' AND form_id='".$lbf_form_id."'");
                    
                    //$res=sqlstatement("select `$field_id` as field_value from $table_name where pid = $pid ORDER BY id DESC LIMIT 1 ");
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
            </div> 
        <?php    
        
        }else if($group == 'cpo' || $group == 'ccm' || $group == 'auditform' || $group == 'procedure'){
            if($group == 'procedure')
                $group = "procedure_order";
            $static_form_id = 0;
            
            /* 
             * To get ccm form / cpo form / auditform / procedure form id 
             */
            $get_static_form_id = sqlStatement("SELECT form_id FROM forms WHERE encounter = $encounter AND pid = $pid AND deleted = 0 AND formdir = '$group' ORDER BY DATE ASC LIMIT 1");
            while($set_static_form_id = sqlFetchArray($get_static_form_id)){
                $static_form_id = $set_static_form_id['form_id'];
            }
            
            if($group != 'auditform')
                $checkstring = "&formid=$lbf_form_id";
            else
                $checkstring = '';
            
            echo "<div class='newwindow'><a href='javascript:;' onclick=win1('/interface/forms/$group/new_custom.php?encounter=$encounter&pid=$pid&id=$static_form_id".$checkstring."&isSingleView=1') ><span>". ucwords(str_replace("_"," ",$group))."</span></a></div>";
            
        }else if($group == 'codes'){
            echo "<div class='newwindow'><a href='javascript:;' onclick=win1('../forms/fee_sheet/feesheet_custom.php?pid=$pid&encounter=$encounter&isSingleView=1') ><span>". ucwords(str_replace("_"," ",$group))."</span></a></div>";
        }else if($group == 'ros'){
            $group = "allcare_ros";
            $form_name = 'Allcare Review of Systems';
            $ros_form_id = 0;
            
            /* 
             * To get Allcare Review of Systems form id 
             */
            $get_ros_form_id = sqlStatement("SELECT form_id FROM forms WHERE encounter = $encounter AND pid = $pid AND deleted = 0 AND formdir = '$group' ORDER BY DATE ASC LIMIT 1");
            while($set_ros_form_id = sqlFetchArray($get_ros_form_id)){
                $ros_form_id = $set_ros_form_id['form_id'];
            }
            
            echo "<div class='newwindow'><a href='javascript:;' onclick=win1('/interface/forms/$group/view_custom.php?formname=$group&encounter=$encounter&pid=$pid&id=$ros_form_id&isSingleView=1') ><span>".$form_name."</span></a></div>";
        }else if($group == 'physical_exam'){
            $group = "allcare_physical_exam";
            $allcare_physical_exam_form_id = 0;
            
            /* 
             * To get Allcare Physical Exam form id 
             */
            $get_allcare_physical_exam_form_id = sqlStatement("SELECT form_id FROM forms WHERE encounter = $encounter AND pid = $pid AND deleted = 0 AND formdir = '$group' ORDER BY DATE ASC LIMIT 1");
            while($set_allcare_physical_exam_form_id = sqlFetchArray($get_allcare_physical_exam_form_id)){
                $allcare_physical_exam_form_id = $set_allcare_physical_exam_form_id['form_id'];
            }
            
            echo "<div class='newwindow'><a href='javascript:;' onclick=win1('/interface/forms/$group/new_custom.php?formname=$group&edit=custom_pe&encounter=$encounter&pid=$pid&id=$allcare_physical_exam_form_id&menu_val=&isSingleView=1') ><span>". ucwords(str_replace("_"," ",$group))."</span></a></div>";
        }else if($group == 'vitals'){
            $formname = "Vitals";
            $vitals_form_id = 0;
            
            /* 
             * To get Vitals form id 
             */
            $get_vitals_form_id = sqlStatement("SELECT form_id FROM forms WHERE encounter = $encounter AND pid = $pid AND deleted = 0 AND formdir = '$group' ORDER BY DATE ASC LIMIT 1");
            while($set_vitals_form_id = sqlFetchArray($get_vitals_form_id)){
                $vitals_form_id = $set_vitals_form_id['form_id'];
            }
            
            /*
             *  To get Vitals form status (Finalized/ Pending)
             */
            $res12      = sqlStatement("SELECT form_id FROM forms where form_name ='Allcare Encounter Forms' AND encounter='$encounter' AND pid='$pid' AND deleted=0 order by id desc");
            $frow_res   = sqlFetchArray($res12);
            $formid     = $frow_res['form_id'];
            $res1       = sqlStatement("SELECT * FROM lbf_data lb "
                            . "INNER JOIN layout_options l ON l.field_id=lb.field_id "
                            . "where lb.form_id='$formid' AND l.form_id='LBF2' AND l.group_name LIKE '%Vitals%' AND lb.field_id LIKE '%_stat%' order by seq");
            $res_row1   = sqlFetchArray($res1);
            $status     = $res_row1['field_value'];
                    
            echo "<div class='newwindow'><a href='javascript:;' onclick=win1('/interface/forms/$group/view_custom.php?formname=$formname&encounter=$encounter&pid=$pid&id=$vitals_form_id&&status=$status&isSingleView=1') ><span>". ucwords(str_replace("_"," ",$group))."</span></a></div>";
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

<style type="text/css">@import url(../../../library/dynarch_calendar.css);</style>

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
    min-height: 180px;
    width: 100%;
}
</style>
</head>

<body class="body_top">
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

<!-- include support for the list-add selectbox feature -->
<?php include $GLOBALS['fileroot']."/library/options_listadd.inc"; ?>
 <div id="savealert"></div>
</body>

</html>
