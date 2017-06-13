<?php
require_once("verify_session.php");

session_start(); 

$pagename = "save eligibility response"; 

require_once("$srcdir/api.inc");
require_once("$srcdir/forms.inc");
require_once("$srcdir/options.inc.php");
require_once("$srcdir/patient.inc");
require_once("$srcdir/formatting.inc.php");
require_once("$srcdir/billing.inc");
include_once("$srcdir/calendar.inc");
include_once("$srcdir/edi.inc");


//for logout
$encounter              = $_REQUEST['encounter'];
$pid                    = $_REQUEST['pid'];
$payer_id               = $_REQUEST['payer_id'];
$verify_type            = $_REQUEST['verify_type'];
$provider_id            = $_REQUEST['provider_id'];
$dos                    = $_REQUEST['dos'];


    // Search for Medicare
    $sql = sqlStatement("SELECT * FROM insurance_companies WHERE id = '".$payer_id."' AND name LIKE 'Medicare%'");
    $insCount  = sqlNumRows($sql);

    $sql = sqlStatement("SELECT username,id, fname, lname, specialty FROM users " .
      "WHERE active = 1 AND ( info IS NULL OR info NOT LIKE '%Inactive%' ) " .
      "AND authorized = 1 AND id='".$provider_id."'" .
      "ORDER BY lname, fname");
    $provIdFetch = sqlFetchArray($sql);
    $provider = $provIdFetch['username'];


//for logout
$refer                      = isset($_REQUEST['refer'])     ? $_REQUEST['refer']    : $_SESSION['refer'];
$_SESSION['refer']          = isset($_REQUEST['refer'])     ? $_REQUEST['refer']    : $_SESSION['refer'];
//$_SESSION['portal_username']= isset($_REQUEST['provider'])  ? $_REQUEST['provider'] : $_SESSION['provider'];

$lbf_form_id = $_REQUEST['form_id'];
if($lbf_form_id == 0){
    $get_form_id = sqlStatement("SELECT max(id) as id FROM tbl_eligibility_response_data"); 
    while($set_form_id = sqlFetchArray($get_form_id)){
        $new_id = $set_form_id['id']+ 1;
    }
}else{
    $new_id = $lbf_form_id;
}
$lbf_form_id = $new_id;

$patient_sql = sqlStatement("SELECT CONCAT(lname,' ', fname) as patientname, dob FROM patient_data WHERE pid = $pid");
$getpatientname         = sqlFetchArray($patient_sql);
$patient_name           = $getpatientname['patientname'];
$patient_dob            = $getpatientname['dob'];
?>
<link rel="stylesheet" href="<?php echo $css_header; ?>" type="text/css">
<style type="text/css">@import url(../../../library/dynarch_calendar.css);</style>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/jquery.js"></script>
<script src="//code.jquery.com/jquery-1.12.0.min.js"></script>
<style type="text/css">@import url(../library/dynarch_calendar.css);</style>
<script type="text/javascript" src="../library/dynarch_calendar.js"></script>
<?php include_once("{$GLOBALS['srcdir']}/dynarch_calendar_en.inc.php"); ?>
<script type="text/javascript" src="../library/dynarch_calendar_setup.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
 
<!-- hema -->
<script src="<?php echo $GLOBALS['webroot'] ?>/library/js/jquery-ui-1.8.13.min.js"></script>
<link rel="stylesheet" href="../themes/jquery-ui.css" type="text/css">
<link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['webroot'] ?>/library/js/fancybox-1.3.4/jquery.fancybox-1.3.4.css" media="screen" />
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/fancybox-1.3.4/jquery.fancybox-1.3.4.pack.js"></script>
 
<style>
.body_top {
    margin-bottom: 0px !important;
}
.ui-widget {
  font-size: 1.0em !important;
/*  border: 1px solid #FFFFFF !important;*/
}
   
.ui-widget-content {
  border: 0px solid  !important;
  background: #FFf !important;
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
  display: none;
}
.calendar{
    z-index: 9999 !important;
}
.lightbox{
    background-color: #000;
    width: 100%;
    height: 100%;
    position: absolute;
    top:0px;
    left:0px;
    z-index: 998 !important;
    opacity: 0.5;
    -ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity=50)";
}
.ui-dialog{
    border: 3px solid #000 !important;
    border-radius: 5px;
    position: fixed;
    width: 80% !important;
    left:0px !important;
    right:0px !important;
    margin: auto;
}
 
.round-button {
    display:block;
    height:40px;
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
 
div.tabContainer div.tab {
    padding: 10px 0 10px 4px !important;
}
.ui-widget-content a {
    color: #000;
}
/*:not(input[onkeyup])*/
 @media only screen and (min-device-width: 320px) and (max-device-width: 480px)and (-webkit-min-device-pixel-ratio: 2){
      input[type="text"], textarea, select{
            width: 100%;
      }
      .ui-widget-content table,.ui-widget-content table tr{
            display: block;
        }
        .ui-widget-content table tr td:nth-child(3),.ui-widget-content table tr td:nth-child(4){
            display: block;
        }
        .ui-widget-content table tr td:nth-child(3),.ui-widget-content table tr td:nth-child(4){
            display:inline-table;
        }
        .ui-widget-content table tr td:nth-child(3){
            padding-left: 0 !important;
        }
 
 }
 
.ui-dialog .current {
    font-weight: bold;
    list-style: outside none none;
    text-align: center;
    
}
.ui-dialog .current a{
    cursor: default;
}
</style>
<!-- * ** -->
 
<!DOCTYPE html>
<html>
    <head>
        <script>
            var form_id = '<?php echo $form_id; ?>';
            $(document).ready(function(){
                $("#single_view").append($('.newwindow'));
                if ($("#preauth").length > 0) {
                    check_preauth();
                }
                // adding on change attribute to all textarea fields in form dynamically
                $('textarea').bind("focusout", function() {
                    var field_id    = this.id;
                    var field_val   = $("#"+field_id).val();
                    var type        = $("#hiddentype"+field_id.replace("form_", "")).val();
                    var form_id     = $("#lbf_form_id").val();
                    var encounter   = "<?php echo $encounter; ?>";
                    var pid         = "<?php echo $pid; ?>";
                    
                    // insurance data
                    var patient_bal     = $("#patient_bal").val();
                    var insurance_bal   = $("#insurance_bal").val();
                    var total_bal       = $("#total_bal").val();
                    var month           = $("#month_value").val();
                    var payer           = $("#payer_id").val();;
                    var preauth_id      = $("#preauth_form_id").val();
                    var new_id          = $("#new_form_id").val();
                    
                    var fieldname  = {
                                        encounter       : encounter, 
                                        pid             : pid, 
                                        type            : type, 
                                        form_id         : form_id,
                                        field_id        : field_id,
                                        field_val       : field_val,
                                        patient_bal     : patient_bal,
                                        insurance_bal   : insurance_bal,
                                        total_bal       : total_bal,
                                        month           : month,
                                        payer           : payer,
                                        preauth_id      : preauth_id,
                                        new_id          : new_id
                                    };
                    ajaxcall(fieldname);
                });
                
                // adding on change attribute to all select fields in form dynamically
                $('select').bind("change", function() {
                    var field_id    = this.id;
                    var field_val   = $("#"+field_id).val();
                    var type        = $("#hiddentype"+field_id.replace("form_", "")).val();

                    var  form_id    = $("#lbf_form_id").val();

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
                    // insurance data
                    var patient_bal     = $("#patient_bal").val();
                    var insurance_bal   = $("#insurance_bal").val();
                    var total_bal       = $("#total_bal").val();
                    var month           = $("#month_value").val();
                    var payer           = $("#payer_id").val();;
                    var preauth_id      = $("#preauth_form_id").val();
                    var new_id          = $("#new_form_id").val();
                    
                    var fieldname  = {
                                        encounter       : encounter, 
                                        pid             : pid, 
                                        type            : type, 
                                        form_id         : form_id,
                                        field_id        : field_id,
                                        field_val       : field_val,
                                        patient_bal     : patient_bal,
                                        insurance_bal   : insurance_bal,
                                        total_bal       : total_bal,
                                        month           : month,
                                        payer           : payer,
                                        preauth_id      : preauth_id,
                                        new_id          : new_id
                                    };
                    ajaxcall(fieldname); 
                });
                
                $('input').bind('focusout', function(event) {
                    //if($(this).is(":focus")){
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
                        var encounter   = "<?php echo $encounter; ?>";
                        var pid         = "<?php echo $pid; ?>";

                        // insurance data
                        var patient_bal     = $("#patient_bal").val();
                        var insurance_bal   = $("#insurance_bal").val();
                        var total_bal       = $("#total_bal").val();
                        var month           = $("#month_value").val();
                        var payer           = $("#payer_id").val();
                        var preauth_id      = $("#preauth_form_id").val();
                        var new_id          = $("#new_form_id").val();

                        var fieldname  = {
                                            encounter       : encounter, 
                                            pid             : pid, 
                                            type            : type, 
                                            form_id         : form_id,
                                            field_id        : field_id,
                                            field_val       : field_val,
                                            patient_bal     : patient_bal,
                                            insurance_bal   : insurance_bal,
                                            total_bal       : total_bal,
                                            month           : month,
                                            payer           : payer,
                                            preauth_id      : preauth_id,
                                            new_id          : new_id
                                        };
                        $("#lbf_form_id").val('<?php echo $new_id ; ?>');
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
                    var encounter   = "<?php echo $encounter; ?>";
                    var pid         = "<?php echo $pid; ?>";

                    // insurance data
                    var patient_bal     = $("#patient_bal").val();
                    var insurance_bal   = $("#insurance_bal").val();
                    var total_bal       = $("#total_bal").val();
                    var month           = $("#month_value").val();
                    var payer           = $("#payer_id").val();
                    var preauth_id      = $("#preauth_form_id").val();
                    var new_id          = $("#new_form_id").val();
                    
                    var fieldname  = {
                                        encounter       : encounter, 
                                        pid             : pid, 
                                        type            : type, 
                                        form_id         : form_id,
                                        field_id        : field_id,
                                        field_val       : field_val,
                                        patient_bal     : patient_bal,
                                        insurance_bal   : insurance_bal,
                                        total_bal       : total_bal,
                                        month           : month,
                                        payer           : payer,
                                        preauth_id      : preauth_id,
                                        new_id          : new_id
                                    };
                    $("#lbf_form_id").val('<?php echo $new_id ; ?>');
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
                        // insurance data
                        var patient_bal     = $("#patient_bal").val();
                        var insurance_bal   = $("#insurance_bal").val();
                        var total_bal       = $("#total_bal").val();
                        var month           = $("#month_value").val();
                        field_id            = field_id.replace("img_","form_");
                        var payer           = $("#payer_id").val();
                        var preauth_id      = $("#preauth_form_id").val();
                        var new_id          = $("#new_form_id").val();
                        
                        var fieldname  = {
                                            encounter       : encounter, 
                                            pid             : pid, 
                                            type            : type, 
                                            form_id         : form_id,
                                            field_id        : field_id,
                                            field_val       : field_val,
                                            patient_bal     : patient_bal,
                                            insurance_bal   : insurance_bal,
                                            total_bal       : total_bal,
                                            month           : month,
                                            payer           : payer,
                                            preauth_id      : preauth_id,
                                            new_id          : new_id
                                        };
                        ajaxcall(fieldname);
                    });
                });
                var lastFocused;
                $(window).bind("beforeunload", function() { 
//                   window.opener.datafromchildwindow($("#lbf_form_id").val(),$("#month_value").val(),'<?php echo $pid; ?>');window.close();
                    window.opener.datafromchildwindow($("#lbf_form_id").val(),$("#month_value").val(),'<?php echo $pid; ?>','<?php echo $verify_type; ?>','<?php echo $payer_id;?>', '<?php echo $provider_id; ?>', '<?php echo $dos; ?>');window.close();
                });
            });
            function getframedata(){
                var data = {lbfid:$("#lbf_form_id").val(),monthval:$("#month_value").val(),pid:'<?php echo $pid; ?>',verify_type:'<?php echo $verify_type; ?>',payer_id:'<?php echo $payer_id;?>',provider_id:'<?php echo $provider_id; ?>',dos:'<?php echo $dos; ?>'}
                return data;
            }
            
            function priauth(){
                if(document.getElementById("preauth").checked === true){
                    var form_id     = $("#preauth_form_id").val();
                    var lbf_form_id = $("#lbf_form_id").val();
                    $.ajax({
                        type: 'POST',
                        url: "show_priauth_fields.php",
 
                        data: { 
                                form_id:form_id
                        },
 
                        success: function(response)
                        {  
                            var result='';

                            //alert(response);
                            if( $('#template1').html(response)) { 
                                $("#template1").dialog({
                                        resizable: false,
                                        close: function() {
                                            $(".lightbox").fadeOut();
                                        }
                                });

                                if(!$('.ui-dialog').next().hasClass('lightbox')){
                                   $( "<div class='lightbox'></div>" ).insertAfter( ".ui-dialog" );
                                   $('.lightbox').css("height",$('.body_top').height()+23+"px");
                                }

                                $(".lightbox").fadeIn();
                                $('.ui-dialog').fadeIn();
                                    $(".ui-dialog .current a").click(function(evt){
                                            evt.preventDefault();
                                    });
                                        $('#template1 .required').next('td').children().attr("required", "true");
                                        $("#ok").click(function(e) {
                                            
                                             var form = $('#template1 form');
                                             var isValid = form[0].checkValidity();
                                             if(false === isValid && window.localStorage.getItem("mobile_sso") == null){
                                                //allow the browser's default submit event behavior 
                                                return true;
                                            }
                                                                                       
                                            var required = $('[required="required"]');
                                            var error = false;
                                            for(var i = 0; i <= (required.length - 1);i++)
                                            {
                                                if(required[i].value == '') 
                                                {
                                                    required[i].style.backgroundColor = 'rgb(255,155,155)';
                                                    error = true; // if any inputs fail validation then the error variable will be set to true;     
                                                }
                                            }
 
                                            if(error) // if error is true;
                                            {
                                                return false; // stop the form from being submitted.
                                            }
                                            e.preventDefault();
                                            var payer           = $("#payer_id").val();;
                                            var patient_bal     = $("#patient_bal").val();
                                            var insurance_bal   = $("#insurance_bal").val();
                                            var total_bal       = $("#total_bal").val();
                                            var month           = $("#month_value").val();
                                            var lbf_form_id     = $("#lbf_form_id").val();
                                            var dos             = '<?php echo $dos; ?>';
                                            $.ajax({
                                                     type: 'POST',
                                                     url: "save_preauth_fields.php",  
                                                     data: { 
                                                        preauthdata     : $("#preauth_forms").serializeArray(),
                                                        payer           : payer,
                                                        pid             : '<?php echo $pid; ?>',
                                                        provider        : '<?php echo $provider_id; ?>',
                                                        form_id         : form_id,
                                                        lbf_form_id     : lbf_form_id,
                                                        patient_bal     : patient_bal,
                                                        insurance_bal   : insurance_bal,
                                                        total_bal       : total_bal,
                                                        month           : month,
                                                        dos             : dos
                                                     },
                                                     success: function(response)
                                                     {
                                                         form_id = $.parseJSON(response);
                                                         $("#preauth_form_id").val(form_id);
                                                         $('#checklink').wrapInner('<a href="javascript:void(0)" onclick = priauth(); />');
                                                         check_preauth();
                                                         $('#template1').dialog('close');
                                                         $('.lightbox').fadeOut();
  
                                                     },
                                                     failure: function(response)
                                                     {
                                                        alert("error");
                                                        $('#preauth').attr('checked', false); 
                                                     }      
                                             });  
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
            function check_preauth(){
                if(document.getElementById("preauth").checked === false){
 
                    $('#theform').find('input, textarea, button, select, img').attr('disabled','disabled');
                    $('#theform').find('img').hide();
                }
                if(document.getElementById("preauth").checked === true){
 
                    $('#theform').find('input, textarea, button, select, img').removeAttr('disabled');
                    $('#theform').find('img').show();
                }
            }
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
            
            <input type='hidden' name='month_value' id='month_value' value="<?php echo $_REQUEST['month_value']; ?>" />
            <input type='hidden' name='lbf_form_id' id='lbf_form_id' value="<?php echo $lbf_form_id; ?>" />
            <input type='hidden' name='new_form_id' id='new_form_id' value="<?php echo $new_id; ?>" />
            <input type='hidden' name='payer_id'    id='payer_id'    value="<?php echo $payer_id; ?>" />
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
                            /*echo '<ul class="tabNav">';
                                $group_header_seq  = substr($this_group_header[$i], 0, 1);
                                $group_header_name = substr($this_group_header[$i], 1);
                                $group_header_seq_esc = htmlspecialchars( $group_header_seq, ENT_QUOTES);
                                $group_header_name_show = htmlspecialchars( xl_layout_label($group_header_name), ENT_NOQUOTES);
                                echo "<li class='current'>";
                                echo "<a href='' id='$group_header_seq_esc'>$group_header_name_show</a></li>";
                            echo "</ul>";*/
                            ?>
                        <!--<div class="tabContainer">-->							
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
                                    $get_form_data = sqlStatement("SELECT `$field_id` as field_value FROM tbl_eligibility_response_data WHERE id = '$lbf_form_id'");
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
                                    // Subhan: Hardcoding since team needs these populated
                                    if($frow['title'] == 'Financial Review' && $currvalue == ''):
                                        $currvalue = date('Y-m-d');
                                    endif;
                                    if($frow['title'] == 'Current Primary Care Provider' && $insCount > 0):
                                        $currvalue = 'Not Required';
                                    endif;
                                    generate_form_field($frow, $currvalue);
                                    echo "</div>";
                                }
                                end_group();
                            ?>
                        <!--</div>-->
                        <?php 
                        }
                    } ?>
                    </div>
                </div> 
            <input type="hidden" name="provider" id="provider" value="<?php echo $provider; ?>"/>
            <input type="hidden" name="refer" id="refer" value="<?php echo $refer; ?>" />
        </form>
        <form>
            <?php 
                $get_provider_priauth = sqlStatement("SELECT pre_auth_check FROM tbl_user_custom_attr_1to1 WHERE userid = '$provider_id' ");
                $explode_payers = array();
                while($set_provider_priauth = sqlFetchArray($get_provider_priauth)){
                    $explode_payers = explode('|', $set_provider_priauth['pre_auth_check']);
                }
                $form_id = 0;
                
//                $get_form_id = sqlStatement("SELECT id FROM tbl_patientuser WHERE payer_id='$payer_id' AND userid='$provider_id' AND pid='$pid' AND preauth_fromdate <= '$dos' AND preauth_todate >= '$dos'");
                $get_form_id = sqlStatement("SELECT p.id FROM tbl_patientuser p
                    INNER JOIN tbl_eligibility_response_data e ON e.id = p.elig_response_id 
                    WHERE e.id='$lbf_form_id' AND p.elig_response_id <> 0");
                while($set_form_id = sqlFetchArray($get_form_id)){
                    $form_id =  $set_form_id['id'];
                }
                ?>
                <input type="hidden" value="<?php echo $form_id; ?>" name='preauth_form_id' id='preauth_form_id'>
                <?php
                if(in_array($payer_id,$explode_payers)){
                    ?>
                <input type='checkbox' name='preauth' id='preauth' onchange="priauth();" <?php if($form_id > 0) echo " checked "; ?> > <span id='checklink' ><?php if($form_id > 0)  echo '<a href="javascript:void(0);" onclick="priauth();">Pre Authorization</a>'; else  echo 'Pre Authorization'; ?></span>
                    <div id="template1"></div>
                    <?php
                }
                ?>
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
/*            .section-header {
                border-bottom: 1px solid;
                margin-bottom: 5px;
                width: 100%;
            }*/
            div.tab {
                background: #ffffff none repeat scroll 0 0;
                margin-bottom: 0px;
                min-height: auto;
                width: 100%;
            }
/*            
            div.tabContainer{
                width: 99%;
            }
            div.tabContainer div.tab {
                padding: 10px 0 10px 10px;
            }*/
            div.tab table td[class=bold] {
                padding-bottom: 0;
                padding-right: 1px;
                width: 300px;
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