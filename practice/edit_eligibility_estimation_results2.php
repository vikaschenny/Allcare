<link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">
<?php
require_once("verify_session.php");

//require_once("../interface/globals.php");  
//require_once("../library/formdata.inc.php"); 
//require_once("../library/globals.inc.php");
//require_once("$srcdir/api.inc");
//require_once("$srcdir/forms.inc");
require_once("$srcdir/options.inc.php");
require_once("$srcdir/patient.inc"); 
require_once("$srcdir/formdata.inc.php");
require_once("$srcdir/formatting.inc.php");

$pid   = $_REQUEST['pid'];

?>
<div  class="page-header">
    <h4><?php xl("Eligibility Results Screen", "e" )?></h4>
    <a href='#' class='css_button' onclick='create_message();' style="float: right;margin: 3px;" >
        <span>
                <?php echo htmlspecialchars( xl('Add Message'), ENT_NOQUOTES); ?>
                <input type='hidden' name='create_message' id='create_message' value=''>
        </span>
    </a>
    <a href='#' class='css_button' onclick='create_patient_note();' style="float: right;margin: 3px;">
        <span>
                <?php echo htmlspecialchars( xl('Add Patient Note'), ENT_NOQUOTES); ?>
                <input type='hidden' name='patient_note' id='patient_note' value=''>
        </span>
    </a>
</div>
<br>
<!--<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/jquery.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/common.js"></script>-->
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
<!--<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/fancybox/jquery.fancybox-1.2.6.js"></script>-->
<!--<script type="text/javascript" src="../library/dialog.js"></script>-->
<!--<script type="text/javascript" src="../library/textformat.js"></script>-->
<script type="text/javascript" src="../library/dynarch_calendar.js"></script>
<?php include_once("{$GLOBALS['srcdir']}/dynarch_calendar_en.inc.php"); ?>
<script type="text/javascript" src="../library/dynarch_calendar_setup.js"></script>

<script>
    $(document).ready(function(){
//        $("#single_view").append($('.newwindow'));
        // adding on change attribute to all textarea fields in form dynamically
//        $('textarea').bind("blur", function() {
//            var field_id    = this.id;
//            var field_val   = $("#"+field_id).val();
//            var type        = $("#hiddentype"+field_id.replace("form_", "")).val();
//            var  form_id    = $("#lbf_form_id").val();
//            var pid         = "<?php echo $pid; ?>";
//            
//            var fieldname  = {
//                                pid         : pid, 
//                                type        : type, 
//                                form_id     : form_id,
//                                field_id    : field_id,
//                                field_val   : field_val
//                            };
//            ajaxcall(fieldname);
//        });
//        $('.newwindow a').click(function(event){event.preventDefault();});
        // adding on change attribute to all select fields in form dynamically
//        $('select').bind("change", function() {
//            var field_id    = this.id;
//            if(field_id != 'template_from'){
//                var field_val   = $("#"+field_id).val();
//                var type        = $("#hiddentype"+field_id.replace("form_", "")).val();
//
//                var  form_id    = $("#lbf_form_id").val();
//
//                var pid             = "<?php echo $pid; ?>";
//                var datatype_new_val    = $("#hidden"+field_id.replace("form_", "")).val();
//                if(datatype_new_val == 40){
//                    // $_POST["form_$field_id"] is an array of dropdown and its keys
//                    // must be concatenated into a |-separated string.
//
//                    var value_string = field_val+"";
//                    field_val        = value_string.replace(/,/g , "|");
//                }else if(datatype_new_val == 28 || datatype_new_val == 32){
//                    // $_POST["form_$field_id"] is an date text fields with companion
//                    // radio buttons to be imploded into "notes|type|date".
//
//                    var field_id2   = field_id.replace("form_", "");
//                    var restype = $("input[name='radio_"+field_id2+"']:checked").val();
//                    if (restype == '') restype = '0';
//                    var resdate = $("input[name='date_"+field_id2+"']").val();
//                    var resnote = $("#form_"+field_id2).val();
//
//                    if(datatype_new_val == 32){
//                        //VicarePlus :: Smoking status data is imploded into "note|type|date|list".
//                        var reslist = $("#form_"+field_id2).val();
//                        var res_text_note = $("input[name='form_text_"+field_id2+"']").val();
//                        var field_val = res_text_note+"|"+restype+"|"+resdate+"|"+reslist;
//                    }else{
//                        var field_val = resnote+"|"+restype+"|"+resdate;
//                    }
//                }else{
//                    var field_id2   = field_id.replace("form_", "");
//                    var field_val   = $("#form_"+field_id2).val();
//                    var field_id    = field_id2;
//                }
//                var fieldname   = {
//                                    pid         : pid, 
//                                    type        : type, 
//                                    form_id     : form_id,
//                                    field_id    : field_id,
//                                    field_val   : field_val
//                                };
//                ajaxcall(fieldname); 
//            }
//        });
//        
//        // adding on change attribute to all input fields in form dynamically
//       
//        $("input").bind("change", function() {
//            var field_id        = this.id;
//            var field_string    = field_id.split('[');
//            var val             = '';
//            var string_label    = field_string[0].trim();
//            var type            = '';
//            var type2            = '';
//            if(string_label.indexOf("radio_") == 0){
//                var datatype_new_val    = $("#hidden"+string_label.replace("radio_", "")).val();
//            }else if(string_label.indexOf("form_") == 0){
//                if(string_label.indexOf("form_text_") == 0)
//                    var datatype_new_val    = $("#hidden"+string_label.replace("form_text_", "")).val();
//                else
//                    var datatype_new_val    = $("#hidden"+string_label.replace("form_", "")).val();
//            }else if(string_label.indexOf("date_") == 0){
//                var datatype_new_val    = $("#hidden"+string_label.replace("date_", "")).val();
//            } 
//            $("input[name^='"+string_label+"[']:checked").each(function(){
//
//                var field_string_split    = this.id;//alert(field_id);
//                var field_string_split2   = field_string_split.split('[');
//                string_label              = field_string_split2[0].replace("form_", "");
//                var string_label2         = field_string_split2[1].replace("]", "");
//
//                if(datatype_new_val == 21){
//                    type2        = $("#hiddentype"+string_label).val();
//                    // $_POST["form_$field_id"] is an array of checkboxes and its keys
//                    // must be concatenated into a |-separated string.
//                    val += string_label2+"|"; 
//                }
//
//            });
//            $("input[name^='"+string_label+"[']:not(:checked)").each(function(){
//                var numberOfChecked = $(this).find(':checkbox:checked').length;
//                if(datatype_new_val == 21 && $(this).attr('type') == 'checkbox' && numberOfChecked == 0){
//                    var field_string_split    = this.id;
//                    var field_string_split2   = field_string_split.split('[')
//                    string_label              = field_string_split2[0].replace("form_", "");
//                    type2                     = $("#hiddentype"+string_label).val();
//                    val = '';
//                }
//            });
//            if(datatype_new_val != 21){
//                $("input[name^='"+string_label+"[']").each(function(){
//                    var field_string_split      = this.id;
//                    var field_string_split2     = field_string_split.split('[');
//                    var string_label_split      = field_string_split2[0].replace("form_", "");
//                    var string_label2           = field_string_split2[1].replace("]", "");
//                    if(datatype_new_val == 22) {
//                        // $_POST["form_$field_id"] is an array of text fields to be imploded
//                        // into "key:value|key:value|...".
//                        type2                    = $("#hiddentype"+string_label_split).val();
//                        var text_val            = $(this).val();
//
//                        val += string_label2+":"+text_val+'|';
//                        string_label = string_label_split.replace("form_", "");
//                    }else if(datatype_new_val == 23){
//                        // $_POST["form_$field_id"] is an array of text fields with companion
//                        // radio buttons to be imploded into "key:n:notes|key:n:notes|...".
//
//                        type2        = $("#hiddentype"+string_label).val();
//
//                        var string_val          = $("input[name='"+field_string_split.replace("form_", "radio_")+"']:checked").val();
//                        if(!string_val)
//                            string_val          = 0;
//
//                        var text_val            = $("input[name='"+field_string_split.replace("radio_", "form_")+"']").val();
//                        if(!text_val)
//                            text_val            = '';
//
//                        var checkstring = string_label2+":"+string_val+":"+text_val+'|';
//                        if(val.indexOf(checkstring) == -1 ){
//                            val += checkstring;
//                        }
//
//                        var string_label_split  = field_string_split.split('[');
//                        var string_label_split2 = string_label_split[0];
//
//                        string_label2 = string_label_split2.replace("form_", "");
//                        string_label  = string_label2.replace("radio_", "");
//
//                    }else if(datatype_new_val == 25){
//                        // $_POST["form_$field_id"] is an array of text fields with companion
//                        // checkboxes to be imploded into "key:n:notes|key:n:notes|...".
//
//                        type2        = $("#hiddentype"+field_id.replace("form_", "")).val();
//
//                        var string_val          = $("input[name='"+field_string_split.replace("form_", "check_")+"']:checked").val();
//                        if(!string_val)
//                            string_val          = 0;
//
//                        var text_val            = $("input[name='"+field_string_split.replace("check_", "form_")+"']").val();
//                        if(!text_val)
//                            text_val            = '';
//
//                        val += string_label2+":"+string_val+":"+text_val+'|';
//
//                        var string_label_split  = field_string_split.split('[');
//                        var string_label_split2 = string_label_split[0];
//
//                        string_label2 = string_label_split2.replace("form_", "");
//                        string_label  = string_label2.replace("check_", "");
//
//                    }else if(datatype_new_val == 28 || datatype_new_val == 32){
//                        // $_POST["form_$field_id"] is an date text fields with companion
//                        // radio buttons to be imploded into "notes|type|date".
//
//                        type2        = $("#hiddentype"+field_id.replace("form_", "")).val();
//
//                        var field_id2   = field_id.replace("form_", "");
//                        var restype = $("input[name='radio_"+field_id2+"']:checked").val();
//                        if (restype == '') restype = '0';
//                        var resdate = $("input[name='date_"+field_id2+"']").val();
//                        var resnote = $("#form_"+field_id2).val();
//
//                        if(datatype_new_val == 32){
//                            //VicarePlus :: Smoking status data is imploded into "note|type|date|list".
//                            var reslist = $("#form_"+field_id2).val();
//                            var res_text_note = $("input[name='form_text_"+field_id2+"']").val();
//                            var field_val = res_text_note+"|"+restype+"|"+resdate+"|"+reslist;
//                        }
//                        else{
//                            var field_val = resnote+"|"+restype+"|"+resdate;
//                        }
//                    }else{
//                        var field_id2   = field_id.replace("form_", "");
//                        var field_val   = $("#form_"+field_id2).val();
//                        var field_id    = field_id2;
//                    }
//                });
//                $("input[name='"+string_label+"']").each(function(){
//                    var field_string_split      = this.id;
//                    var field_string_split2     = field_string_split.split('[');
//
//                    if(field_string_split2[1])
//                        var string_label2           = field_string_split2[1].replace("]", "");
//                    else
//                        var string_label2 = '';
//                    if(datatype_new_val == 22) {
//                        // $_POST["form_$field_id"] is an array of text fields to be imploded
//                        // into "key:value|key:value|...".
//                        if(field_string_split2[0])
//                            var string_label_split      = field_string_split2[0].replace("form_", "");
//                        type2                    = $("#hiddentype"+string_label_split).val();
//                        var text_val            = $(this).val();
//
//                        val += string_label2+":"+text_val+'|';
//                        string_label = string_label_split.replace("form_", "");
//                    }else if(datatype_new_val == 23){
//                        // $_POST["form_$field_id"] is an array of text fields with companion
//                        // radio buttons to be imploded into "key:n:notes|key:n:notes|...".
//
//                        type2        = $("#hiddentype"+field_id.replace("form_", "")).val();
//
//                        var string_val          = $("input[name='"+field_string_split.replace("form_", "radio_")+"']:checked").val();
//                        if(!string_val)
//                            string_val          = 0;
//
//                        var text_val            = $("input[name='"+field_string_split.replace("radio_", "form_")+"']").val();
//                        if(!text_val)
//                            text_val            = '';
//
//                        var checkstring = string_label2+":"+string_val+":"+text_val+'|';
//                        if(val.indexOf(checkstring) == -1 ){
//                            val += checkstring;
//                        }
//
//                        var string_label_split  = field_string_split.split('[');
//                        var string_label_split2 = string_label_split[0];
//
//                        string_label2 = string_label_split2.replace("form_", "");
//                        string_label  = string_label2.replace("radio_", "");
//
//                    }else if(datatype_new_val == 25){
//                        // $_POST["form_$field_id"] is an array of text fields with companion
//                        // checkboxes to be imploded into "key:n:notes|key:n:notes|...".
//                        type2        = $("#hiddentype"+field_id.replace("form_", "")).val();
//
//                        var string_val          = $("input[name='"+field_string_split.replace("form_", "check_")+"']:checked").val();
//                        if(!string_val)
//                            string_val          = 0;
//
//                        var text_val            = $("input[name='"+field_string_split.replace("check_", "form_")+"']").val();
//                        if(!text_val)
//                            text_val            = '';
//
//                        val += string_label2+":"+string_val+":"+text_val+'|';
//
//                        var string_label_split  = field_string_split.split('[');
//                        var string_label_split2 = string_label_split[0];
//
//                        string_label2 = string_label_split2.replace("form_", "");
//                        string_label  = string_label2.replace("check_", "");
//
//                    }else if(datatype_new_val == 28 || datatype_new_val == 32){
//                        // $_POST["form_$field_id"] is an date text fields with companion
//                        // radio buttons to be imploded into "notes|type|date".
//                        if(field_string_split.indexOf("form_") == 0){
//                            if(field_string_split.indexOf("form_text_") == 0){
//                                type2           = $("#hiddentype"+field_string_split2[0].replace("form_text_", "")).val();
//                                var field_id2   = field_string_split2[0].replace("form_text_", "");
//                            }else{
//                                type2           = $("#hiddentype"+field_string_split2[0].replace("form_", "")).val();
//                                var field_id2   = field_string_split2[0].replace("form_", "");
//                            }
//                        }else if(field_string_split.indexOf("radio_") == 0){
//                            type2           = $("#hiddentype"+field_string_split2[0].replace("radio_", "")).val();
//                            var field_id2   = field_string_split2[0].replace("radio_", "");
//                        }else if(field_string_split.indexOf("date_") == 0){
//                            type2           = $("#hiddentype"+field_string_split2[0].replace("date_", "")).val();
//                            var field_id2   = field_string_split2[0].replace("date_", "");
//                        }
//                        var restype = '';
//                        var restype = $("input[name='radio_"+field_id2+"']:checked").val();
//                        if (restype == '' || restype == 'undefined' ) restype = '0';
//                        var resdate = $("input[name='date_"+field_id2+"']").val();
//                        var resnote = $("#form_"+field_id2).val();
//
//                        if(datatype_new_val == 32){
//                            //VicarePlus :: Smoking status data is imploded into "note|type|date|list".
//                            var reslist = $("#form_"+field_id2).val();
//                            var res_text_note = $("input[name='form_text_"+field_id2+"']").val();
//                            var val2 = res_text_note+"|"+restype+"|"+resdate+"|"+reslist;
//                        }
//                        else{
//                            var val2 = resnote+"|"+restype+"|"+resdate;
//                        }
////                        alert(val2);
//                        var string_label_split  = field_string_split.split('[');
//                        var string_label_split2 = string_label_split[0];
//
//                        if(field_string_split.indexOf("form_") == 0){
//                            if(field_string_split.indexOf("form_text_") == 0)
//                                string_label2 = field_string_split2[0].replace("form_text_", "");
//                            else
//                                string_label2 = field_string_split2[0].replace("form_text_", "");
//                        }else if(field_string_split.indexOf("radio_") == 0){
//                            string_label2 = field_string_split2[0].replace("radio_", "");
//                        }else if(field_string_split.indexOf("date_") == 0){
//                            string_label2 = field_string_split2[0].replace("date_", "");
//                        }    
//                        string_label  = string_label2.replace("check_", "");
//                    }else{
//                        type2           = $("#hiddentype"+field_string_split2[0].replace("form_", "")).val();
//                        var field_id2   = field_string_split2[0].replace("form_", "");
//                        var val2        = $("#form_"+field_id2).val();
//                    }
//                    val = val2;
//                });
//            }
//
//            var field_val   = val;
//            
//            type            = type2;
//            var  form_id    = $("#lbf_form_id").val();
//            var pid         = "<?php echo $pid; ?>";
//            
//            var fieldname   = {
//                                pid         : pid, 
//                                type        : type, 
//                                form_id     : form_id,
//                                field_id    : string_label,
//                                field_val   : field_val
//                            };
////                            alert(encounter+"="+pid+"="+type+"="+form_id+"="+string_label+"="+field_val);
//            ajaxcall(fieldname);
//        });
//        // date time datatype picker
//        
//        $("input[placeholder*='date and time']").blur(function() {
//
//            var field_string_split        = this.id;
//            var string_label          = field_string_split.replace("form_", "");
//            var type                  = $("#hiddentype"+string_label).val();
//            var field_val             = $(this).val();
//            var  form_id    = $("#lbf_form_id").val();
//            var pid         = "<?php echo $pid; ?>";
//
//            var fieldname   = {
//                                pid         : pid, 
//                                type        : type, 
//                                form_id     : form_id,
//                                field_id    : string_label,
//                                field_val   : field_val
//                            };
////                            alert(encounter+"="+pid+"="+type+"="+form_id+"="+string_label+"="+field_val);
//            ajaxcall(fieldname);
//        });
//        
//        // image of date object
//         $( "img" ).click(function() {
//            var field_id    = this.id;
//            
//            $('.calendar .day').click(function(){
//                var field_val   = $("#"+field_id).val();
//                var type        = $("#hiddentype"+field_id.replace("img_", "")).val();
//                var field_id2   = field_id.replace("img_", "");
//
//                var  form_id    = $("#lbf_form_id").val();
//
//                var pid             = "<?php echo $pid; ?>";
//                var datatype_new_val    = $("#hidden"+field_id.replace("img_", "")).val();
//
//                if(datatype_new_val == 40){
//                    // $_POST["form_$field_id"] is an array of dropdown and its keys
//                    // must be concatenated into a |-separated string.
//
//                    var value_string = field_val+"";
//                    field_val        = value_string.replace(/,/g , "|");
//
//                }else if(datatype_new_val == 28 || datatype_new_val == 32){
//                    // $_POST["form_$field_id"] is an date text fields with companion
//                    // radio buttons to be imploded into "notes|type|date".
//
//                    var restype = $("input[name='radio_"+field_id2+"']:checked").val();//alert(restype+"="+"input[name='radio_"+field_id2+"']");
//                    if (restype == '') restype = '0';
//                    var resdate = $("input[name='date_"+field_id2+"']").val();//alert(resdate+"="+"input[name='date_"+field_id2+"']");
//                    var resnote = $("#form_"+field_id2).val();//alert(resnote+"="+"#form_"+field_id2);
//
//                    if(datatype_new_val == 32){
//                        //VicarePlus :: Smoking status data is imploded into "note|type|date|list".
//                        var reslist = $("#form_"+field_id2).val();//alert(reslist+"="+"#form_"+field_id2);
//                        var res_text_note = $("input[name='form_text_"+field_id2+"']").val();//alert(res_text_note+"="+"input[name='form_text_"+field_id2+"']");
//                        var field_val = res_text_note+"|"+restype+"|"+resdate+"|"+reslist;
//                    }else{
//                        var field_val = resnote+"|"+restype+"|"+resdate;
//                    }
//                }
//                var fieldname   = {
//                                    pid         : pid, 
//                                    type        : type, 
//                                    form_id     : form_id,
//                                    field_id    : field_id2,
//                                    field_val   : field_val
//                                };
//                ajaxcall(fieldname);
//            });
//        });
        $(".deductable_for").change(function(){
            deductable_change();
        });
    });
    function deductable_change(bal){
        var selected_deduct = document.querySelector('input[name="deductable_for"]:checked').value;
    }
//    function ajaxcall(fieldname){
//     $('#savealert').html("<div>Saving...</div>");
//        $.ajax({
//            type: "POST",
//            url: "save_single_view_data.php",
//            data: fieldname,
//            dataType : "json",
//            success: function(data) {
//                var dataresult = data +' ';
//                var res = dataresult.split(',');
//                $("#lbf_form_id").val(res[0]);
//                $('#savealert').html("<div>Saved.</div>").fadeIn(500,function(){$(this).fadeOut()});
//            },
//            error: function(jqXHR, exception){
//                alert("failed" + jqXHR.responseText);
//            }    
//        });
//    }
//    function win1(url){
//        var popup = window.open(url,'_blank','width=900,height=500,scrollbars=yes,resizable=yes');
//        $("#single_view :input").attr("disabled", true);
//        if (popup) {
//            popup.onbeforeunload = function () { refreshAndClose(); }
//        }
//    }
//    function refreshAndClose() {
//        window.opener($("#single_view :input").attr("disabled", false));
//    }
//    function RefreshParent() {
//        if (window.opener != null && !window.opener.closed) {
////            window.opener.location.reload();
//            window.opener.location.href = 'provider_incomplete_charts.php?checkencounter=';
//        }
//    }
//    var isChrome = /Chrome/.test(navigator.userAgent) && /Google Inc/.test(navigator.vendor);
//    isChrome?window.onunload = RefreshParent:window.onbeforeunload = RefreshParent; 
//    
//    function showforms()
//    {
//         $('body').css("overflow","hidden");
//        var copied_to   = '<?php  echo $_REQUEST['encounter']; ?>';
//        var encounter   = jQuery('#template_from').val();
//        if(encounter != ' '){
//            $.ajax({
//                    type: 'POST',
//                    url: "copy_template.php",
//
//                    data: { 
//                        encounter:encounter,
//                        copied_to:copied_to
//                    },
//
//                    success: function(response)
//                    {  
//                        var result='';
//                        if( $('#template1').html(response)) {
//
//                            var answer = $("#template1").dialog({resizable: false,position:"top",close: function() {$(".lightbox").fadeOut(function(){$('body').css("overflow","auto");$(this).remove();});}})
//                                        .find(':checkbox').unbind('change').bind('change', function(e){
//                                if(this.checked)  {
//                                    if($(this).val()!="undefined" && $(this).val()!=''){ result+=$(this).val()+","; }
//                                }
//                            });
//                            if(!$('.ui-dialog').next().hasClass('lightbox')){
//                                $( "<div class='lightbox'></div>" ).insertAfter( ".ui-dialog" );
//                                $('.lightbox').css("height",$('.body_top').height()+"px");
//                            }
//                            var gettopdialogpos =  parseInt(($('.ui-dialog').css("top")).replace("px",""));
//                            var getwindowgeight = ((window.innerHeight/2) - ($('.ui-dialog').height()/2));
//                            var centerdilog = ((gettopdialogpos + getwindowgeight));
//                            $('.ui-dialog').css("top",centerdilog +"px");
//                            $(".lightbox").fadeIn();
//                            $('.ui-dialog').fadeIn();
//                            $('.lightbox').click(function(){
//                                $(this).fadeOut(function(){$('body').css("overflow","auto");$(this).remove();});
//                                $('.ui-dialog').fadeOut();
//                            });
//
//                            $("#ok").click(function(e) {
//                                
//                                var formdetails     = result.split(','); 
//                                for(var i=0; i<formdetails.length; i++){
//                                    
//                                    var copy_to_fname   = formdetails[i].split('--');
//                                    if(typeof copy_to_fname !='undefined' && copy_to_fname  != ''  && copy_to_fname != null ){
////                                        alert("hema");
//                                        var copy_to_fname1  = copy_to_fname[1];
//                                        var form_name1      = copy_to_fname[0].split('-');
//                                        var copy_from_id    = form_name1[0];
//                                        var form_name       = form_name1[1];
//                                        $('#template1').dialog('close');
//                                        $('.lightbox').fadeOut();
//                                        //alert(copy_to_fname1+"+"+copy_from_id+"="+form_name+"="+'<?php echo $pid; ?>');
//                                        if(form_name != '' && typeof form_name != 'undefined'){
//    //                                        alert("="+form_name+"=");
//                                            $.ajax({
//                                                type: 'POST',
//                                                url: "copy_template_query.php",	
//                                                data: { 
//                                                    copy_to_fname1  : copy_to_fname1,
//                                                    copy_from_id    : copy_from_id,
//                                                    form_name       : form_name,
//                                                    pid             : '<?php echo $pid; ?>'
//                                                },
//                                                success:function(data){
////                                                    alert(data);
//                                                    var newformid_array = data.split('-'); 
//                                                    var newformid       = newformid_array[1];
//                                                    if(newformid_array[1] == 'LBF2'){
//                                                        $("#lbf_form_id").val(newformid);
//                                                    }
//    //                                                updateallids(newformid);
//
//                                                    return false;
//                                                },
//                                                failure: function(response)
//                                                {
//                                                    alert("error");
//                                                }		
//                                            });
//                                        }
//                                    }
//                                }
//                                location.reload();
//                                return false;
//                            });
//                        }
//                        
//                    },
//                    failure: function(response)
//                    {
//                        alert("error");
//                    }		
//            });	
//        }
//    }
    function create_message(){
        window.resizeBy(-300,0);
        window.moveTo(0,0);
        window.open("messages/eligibility_messages.php?showall=no&sortby=users.lname&sortorder=asc&begin=0&task=addnew&form_active=1","","height=600,top=0,scrollbars=1,resizable=1");
    } 
    function create_patient_note(){
        window.resizeBy(-300,0);
        window.moveTo(0,0);
        window.open("messages/eligibility_messages.php?showall=no&sortby=users.lname&sortorder=asc&begin=0&task=addnew&form_active=1","","height=600,top=0,scrollbars=1,resizable=1");
    }
    
</script>
<?php
    // to get lbf2 form id 
    $lbf_form_id     = 0;
    $primary = $secondary = $tertiary = '';
    $providerID = $ref_providerID = '';
    foreach (array('primary','secondary','tertiary') as $instype) {
        $get_insurance_data = sqlStatement("SELECT (SELECT name FROM insurance_companies WHERE id = i.provider) as insurancename,plan_name,policy_number FROM insurance_data i WHERE pid = '$pid' AND type='$instype' ORDER BY i.date DESC LIMIT 0,1");
        while($set_insurance_data = sqlFetchArray($get_insurance_data)){
            $instypeplan        = $instype.'_plan';
            ${$instype}         = "<span style='color:green; '> ".$set_insurance_data['insurancename']."</span>";
            ${$instypeplan}     = '<span style="color:green; ">' .$set_insurance_data['plan_name'].'</span>';
            if($set_insurance_data['insurancename'] =='')
                ${$instype} = '<span style="color:red; ">No Payer</span>';
            if($set_insurance_data['plan_name'] == '')
               ${$instypeplan} = '<span style="color:red; ">No plan name</span>';
        }
    }
    $get_demographics_data = sqlStatement("SELECT ( SELECT CONCAT(fname, ' ', lname) as pname FROM users WHERE id = providerID )as providername,( SELECT CONCAT(fname, ' ', lname) as pname FROM users WHERE id = ref_providerID ) as refProvidername FROM patient_data WHERE pid = '$pid'");
    while($set_demographics_data = sqlFetchArray($get_demographics_data)){
        $providerID     = $set_demographics_data['providername'];
        $ref_providerID = $set_demographics_data['refProvidername'];
    }
    ?>
<div id='main_results_div'>
    <div class='elig_results_class' id='elig_result_response'>
    <form id="single_view" name="single_view">
        <?php
        echo "<table style='border:0 !important; width:100%; border-collapse: separate; border-spacing: 13px; !important;font-size:11px' >";
            echo "<tr>";
                echo "<td style='border:0 !important'><b>".$primary."</b><span style='float:right'>(Primary) </span></td><td>- ".$primary_plan."<span style='float:right'>(Plan) </span></td>";
            echo "</tr><tr>";
                echo "<td style='border:0 !important'><b>".$secondary."</b><span style='float:right'>(Secondary)</span></td><td>- ".$secondary_plan."<span style='float:right'>(Plan) </span></td>";
            echo "</tr><tr>";    
                echo "<td style='border:0 !important'><b>".$tertiary."</b><span style='float:right'>(Teritary)</span></td><td>- ".$tertiary_plan."<span style='float:right'>(Plan) </span></td>";
            echo "</tr><tr>";    
                echo "<td><b>Primary Care Physician Assigned to Patient:</b></td><td>".$providerID."</td>"; 
            echo "</tr><tr>";
                echo "<td><b>Rendering Physician Assigned to Patient:</b></td><td>".$ref_providerID."</td>"; 
            echo "</tr><tr>";    
                echo "<td><b>Date of Eligibility for:</b></td><td>";
                ?>
                <input type='text' size='10' name='form_date' id='form_date' value='<?php echo attr($date) ?>' title='<?php echo xla('yyyy-mm-dd event date or starting date'); ?>'
                onkeyup='datekeyup(this,mypcc)' onblur='dateblur(this,mypcc)' onchange='dateChanged()' />
               <img src='../interface/pic/show_calendar.gif' align='absbottom' width='24' height='22'
                id='img_date' border='0' alt='[?]' style='cursor:pointer;cursor:hand'
                title='<?php echo xla('Click here to choose a date'); ?>'>
                <?php
                echo "</td>"; 
            echo "</tr><tr>";    
                echo "<td><b>Insurance:</b></td><td><input type='checkbox' value='' name='insurancecheckbox'>Check mark ( if it is same as above) <input type='text' value='' name='insurancetext'>".$res_row1['sex']."</td>";
            echo "</tr><tr>";    
                echo "<td><b>Plan:</b></td><td><input type='checkbox' value='' name='insurancecheckbox'>Check mark ( if it is same as above) <input type='text' value='' name='insurancetext'>".$res_row1['sex']."</td>";
            echo "</tr><tr>";    
                echo "<td><b>Primary Care Physician:</b></td><td><input type='checkbox' value='' name='insurancecheckbox'>Check mark ( if it is same as above) <input type='text' value='' name='insurancetext'>".$res_row1['sex']."</td>";
            echo "</tr><tr>";    
                echo "<td><b>In Network:</b></td><td>";
                echo "<select name='in_network'> ";
                echo "<option value='yes' > Yes </option>";
                echo "<option value='no' > No </option>";
                echo "</select>";
                echo "</td>"; 
            echo "</tr><tr>";    
                echo "<td><b>Allowed to See Patient:</b></td><td>";
                echo "<select name='can_see_patient'> ";
                echo "<option value='yes' > Yes </option>";
                echo "<option value='cashpay' > Cash Pay </option>";
                echo "</select>";
                echo "</td>"; 
            echo "</tr><tr>";    
                echo "<td><b>Patient Balance Due:</b></td><td>";
                echo "<span name='patient_balance_due'> </span>";
                echo "<input type='hidden' name='hidden_patient_balance_due' value=''>";
                echo "</td>"; 
            echo "</tr><tr>";
            echo "<td><b>Deductable For:</b></td><td>";
            ?>
            <input type='radio' name='deductable_for' id='deductable_for' onclick='deductable_change(this);' value='Individual' >Individual
            <input type='radio' name='deductable_for' id='deductable_for' onclick='deductable_change(this);' value='Family' checked>Family 
            <?php
            echo "</td></tr>";
            echo "<tr><td colspan='2' style='border:0 !important'>";
                ?>
                <input type='hidden' name='lbf_form_id' id='lbf_form_id' value="<?php echo $lbf_form_id; ?>" />
                <!-- Required for the popup date selectors -->
                <div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>
                <div class="panel panel-default">
                    <?php
                    $get_group_names = sqlStatement("SELECT DISTINCT group_name as gp_name FROM layout_options WHERE form_id='ELIGRESULTS' ORDER BY group_name ") ;
                    while ($set_group_names = sqlFetchArray($get_group_names)) {
                        $groupname = $set_group_names['gp_name'];
                        $fres = sqlStatement("SELECT * FROM layout_options WHERE form_id='ELIGRESULTS' AND group_name LIKE '%$groupname'");

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
                                    echo "<a data-toggle='collapse' class='panel-title' data-parent='#accordion' href='#div_$titleid'>$group_name_show</a>";
                                echo "</div>";
                                echo "<div id='div_$titleid' class='panel-collapse collapse in'>";
                                echo "<div class='panel-body' style='font-size: 11px;padding-bottom: 1px;padding-top: 1px;'>";
                            }
                            ++$item_count;
                        }
                        ?>							
                        <?php
                        $fres = sqlStatement("SELECT * FROM layout_options WHERE form_id='ELIGRESULTS' AND group_name LIKE '%$groupname' ORDER BY seq");
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
                            $group_id   = $frow['form_id'];

                            ?> 
                            <input type="hidden" id="hidden<?php echo $field_id; ?>" name="hidden<?php echo $field_id; ?>" value="<?php echo $data_type ; ?>" />
                            <input type="hidden" id="hiddentype<?php echo $field_id; ?>" name="hiddentype<?php echo $field_id; ?>" value="<?php echo $group_id ; ?>" />
                            <?php

                            $currvalue  = '';
                            $res    = sqlstatement("select `$field_id` as field_value from tbl_elig_est_results where pid = '$pid' ORDER BY id DESC LIMIT 1");
                            $frow1  = sqlFetchArray($res);
                            $currvalue = $frow1['field_value'];


                        // Handle a data category (group) change.
                          if (strcmp($this_group, $last_group) != 0) {
                                end_group();
                                $group_seq  = substr($this_group, 0, 1);
                                $group_name = substr($this_group, 1);
                                $last_group = $this_group;
                                $group_seq_esc = htmlspecialchars( $group_seq, ENT_QUOTES);
                                echo "<div class='tab current' id='div_$group_seq_esc'>";
                                echo " <table border='0' cellpadding='0' style='font-size:11px' >\n";
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
                            echo "<td width='200' valign='top' colspan='$titlecols_esc'";
                            echo ($frow['uor'] == 2) ? " class='required'" : " class='bold'";
                            if ($cell_count == 2) echo " style='padding-left:2pt'";
                            echo ">";
                            $cell_count += $titlecols;
                          }
                          ++$item_count;

                          echo "<b>";

                          // Modified 6-09 by BM - Translate if applicable
                          if ($frow['title']) echo (htmlspecialchars( xl_layout_label($frow['title']), ENT_NOQUOTES) . ":"); else echo "&nbsp;";

                          echo "</b></td><td>";

                          // Handle starting of a new data cell.
                          if ($datacols > 0) {
                            end_cell();
                            $datacols_esc = htmlspecialchars( $datacols, ENT_QUOTES);
                            echo "<td valign='top' colspan='$datacols_esc' class='text'";
                            if ($cell_count > 0) echo " style='padding-left:2pt'";
                            echo ">";
                            $cell_count += $datacols;
                          }

                          ++$item_count;
                          generate_form_field($frow, $currvalue);
                          echo "</div>";

                          }
                        end_group();
                        echo "</div>";
                        echo "</div>";
                    }
    //                echo "<br>";
    //                echo "<br>";
                    ?>
                </div> 
                <?php
            echo "</td></tr><tr>";
                /* current encounter deductable calculation 
                 * 1.Visit allowed amount is more than total deductible remaining  amount.
                 * Then the total allowed amount is current visit deductible 
                 * 2.If the deductible remaining  amount is less than the current visit allowed amount. 
                 * Then the remaining deductible amount is current visit deductible 
                 */
                echo "<td style='border:0 !important'><b>Deductible for the Current Encounter: </b></td><td>".$current_encounter_deductable."</td>";
            echo "</tr><tr>";
                echo "<td style='border:0 !important'><b>Allowed Amount: </b></td><td>".$allowed_amount."</td>";
            echo "</tr><tr>";
                /* Total Patient Responsibility Caluctaion =
                 * sum of applicable copay + deductible for the current encounter based on remaining for individual or family whichever is higher
                 */
                echo "<td style='border:0 !important'><b>Total Patient Responsibility: </b></td><td>".$dos[0]."</td>";
            echo "</tr><tr>";
                echo "<td colspan='2' style='border:0 !important'>";
                echo "<div class='panel panel-default'><div class='panel-heading'>";
                    echo "<a data-toggle='collapse' class='panel-title' data-parent='#accordion' href='#div_authorization'>Authorization</a>";
                echo "</div>";
                echo "<div id='div_authorization' class='panel-collapse collapse in'>";
                echo "<div class='panel-body' style='font-size: 11px;padding-bottom: 1px;padding-top: 1px;'>";
                    $groupname = $set_group_names['gp_name'];
                    $fres = sqlStatement("SELECT * FROM layout_options " .
                        "WHERE form_id = 'USERS' AND uor > 0 AND group_name LIKE '%Preauth'" .
                        "ORDER BY  seq");

                    $last_group = '';
                    $cell_count = 0;
                    $item_count = 0;
                    $display_style = 'block';

                    $fres = sqlStatement("SELECT * FROM layout_options " .
                        "WHERE form_id = 'USERS' AND uor > 0 AND group_name LIKE '%Preauth'" .
                        "ORDER BY  seq");
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
                        $group_id   = $frow['form_id'];

                        ?> 
                        <input type="hidden" id="hidden<?php echo $field_id; ?>" name="hidden<?php echo $field_id; ?>" value="<?php echo $data_type ; ?>" />
                        <input type="hidden" id="hiddentype<?php echo $field_id; ?>" name="hiddentype<?php echo $field_id; ?>" value="<?php echo $group_id ; ?>" />
                        <?php

                        $currvalue  = '';
                        $res    = sqlstatement("select * from tbl_patientuser where id='".$form_id."'");
                        $frow1  = sqlFetchArray($res);
                        $currvalue = $frow1['field_value'];


                    // Handle a data category (group) change.
                      if (strcmp($this_group, $last_group) != 0) {
                            end_group();
                            $group_seq  = substr($this_group, 0, 1);
                            $group_name = substr($this_group, 1);
                            $last_group = $this_group;
                            $group_seq_esc = htmlspecialchars( $group_seq, ENT_QUOTES);
                            echo "<div class='tab current' id='div_$group_seq_esc'>";
                            echo " <table border='0' cellpadding='0' style='font-size:11px' >\n";
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
                        echo "<td width='200' valign='top' colspan='$titlecols_esc'";
                        echo ($frow['uor'] == 2) ? " class='required'" : " class='bold'";
                        if ($cell_count == 2) echo " style='padding-left:2pt'";
                        echo ">";
                        $cell_count += $titlecols;
                      }
                      ++$item_count;

                      echo "<b>";

                      // Modified 6-09 by BM - Translate if applicable
                      if ($frow['title']) echo (htmlspecialchars( xl_layout_label($frow['title']), ENT_NOQUOTES) . ":"); else echo "&nbsp;";

                      echo "</b></td><td>";

                      // Handle starting of a new data cell.
                      if ($datacols > 0) {
                        end_cell();
                        $datacols_esc = htmlspecialchars( $datacols, ENT_QUOTES);
                        echo "<td valign='top' colspan='$datacols_esc' class='text'";
                        if ($cell_count > 0) echo " style='padding-left:2pt'";
                        echo ">";
                        $cell_count += $datacols;
                      }

                      ++$item_count;
                      generate_form_field($frow, $currvalue);
                      echo "</div>";

                      }
                    end_group();
                    echo "</div>";
                    echo "</div>";
                    echo "</div>";
                echo "</td>";
            echo "</tr><tr>";
                echo "<td style='border:0 !important'><b>Eligibility Note: </b></td><td><textarea name='eligibility_note' cols='50' rows='4'></textarea></td>";
            echo "</tr>";
        echo "</table>";
        echo "<br>";


    $CPR = 4; // cells per row

    ?>
    </div>
    <div class='elig_results_class' id='elig_result_html'>
        <!-- Display html of eligibility/estimation results here -->
        <?php 
        $get_elig_html = sqlStatement("SELECT html_data FROM tbl_eligibility_html_data WHERE pid = '$pid' ORDER BY date LIMIT 0,1");
        while($set_elig_html = sqlFetchArray($get_elig_html)){
            $html_data = $set_elig_html['html_data'];
        }   
        echo base64_decode($html_data);
        ?>   
        
    </div>
</div>
<html>
<head>
<?php html_header_show();?>

<link rel="stylesheet" href="<?php echo $css_header; ?>" type="text/css">

<style type="text/css">@import url(../library/dynarch_calendar.css);</style>
<!--<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>-->
<?php include_once("{$GLOBALS['srcdir']}/ajax/facility_ajax_jav.inc.php"); ?>
<!--<link rel="stylesheet" href="../interface/themes/jquery-ui.css" type="text/css">-->
<!--<link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['webroot'] ?>/library/js/fancybox-1.3.4/jquery.fancybox-1.3.4.css" media="screen" />-->
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
    margin-bottom: 5px;
    min-height: 50px;
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
    padding-bottom: 10px;
}
.panel-title {
    display: block;
    font-size: 14px;
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
.panel-body{
    font-size: 11px;
    padding-bottom: 1px;
    padding-top: 1px;
}
.panel-body:before, .panel-body:after, .modal-footer:before, .modal-footer:after{display: block;}
.bold {
    font-size: 11px;
}
.elig_results_class{
    position: relative;
    display: inline-block;
    vertical-align: top;
    border:1px solid black;
    border-collapse: collapse;
    overflow: scroll;
    width: 50%;
}
.main_results_div{
    width: 100%;
    position: relative;
}
#elig_result_html{
    overflow: scroll;
    display: inline-block;
    position: absolute;
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
    Calendar.setup({inputField:"form_date", ifFormat:"%Y-%m-%d", button:"img_date"});
</script>

</body>

</html>
