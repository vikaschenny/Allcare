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

$order=$_REQUEST['order'];
$provider=$_REQUEST['provider'] ? $_REQUEST['provider'] : $_SESSION['portal_username'];
$page_id=$_REQUEST['id'];



//require_once("../../globals.php");
require_once("$srcdir/acl.inc");
require_once("$srcdir/options.inc.php");
require_once("$srcdir/formatting.inc.php");
require_once("$srcdir/erx_javascript.inc.php");

 // Session pid must be right or bad things can happen when demographics are saved!
 //
 include_once("$srcdir/pid.inc");
 $set_pid = $_GET["set_pid"] ? $_GET["set_pid"] : $_GET["pid"];
 if ($set_pid && $set_pid != $_SESSION["pid"]) {
  setpid($set_pid);
 }

 include_once("$srcdir/patient.inc");

 $result = getPatientData($pid, "*, DATE_FORMAT(DOB,'%Y-%m-%d') as DOB_YMD");
 $result2 = getEmployerData($pid);

 // Check authorization.
 if ($pid) {
  if (!acl_check('patients', 'demo', '', 'write'))
   die(xl('Updating demographics is not authorized.'));
  if ($result['squad'] && ! acl_check('squads', $result['squad']))
   die(xl('You are not authorized to access this squad.'));
 } else {
  if (!acl_check('patients', 'demo', '', array('write','addonly') ))
   die(xl('Adding demographics is not authorized.'));
 }

$CPR = 4; // cells per row

// $statii = array('married','single','divorced','widowed','separated','domestic partner');
// $langi = getLanguages();
// $ethnoraciali = getEthnoRacials();
// $provideri = getProviderInfo();

$insurancei = getInsuranceProviders();


$fres = sqlStatement("SELECT * FROM layout_options " .
  "WHERE form_id = 'DEM' AND uor > 0 " .
  "ORDER BY group_name, seq");
?>
<html>
<head>
<?php html_header_show();?>

<link rel="stylesheet" href="<?php echo $css_header; ?>" type="text/css">

<style type="text/css">@import url(../../../library/dynarch_calendar.css);</style>
<link rel="stylesheet"  type="text/css" href="../../../library/popover/css/jquery.webui-popover.min.css"/>
<script type="text/javascript" src="../../../library/dialog.js"></script>
<script type="text/javascript" src="../../../library/textformat.js"></script>
<script type="text/javascript" src="../../../library/dynarch_calendar.js"></script>
<?php include_once("{$GLOBALS['srcdir']}/dynarch_calendar_en.inc.php"); ?>
<script type="text/javascript" src="../../../library/dynarch_calendar_setup.js"></script>

<script type="text/javascript" src="../../../library/js/jquery.1.3.2.js"></script>
<script type="text/javascript" src="../../../library/js/common.js"></script>

<script type="text/javascript" src="../../../library/js/fancybox/jquery.fancybox-1.2.6.js"></script>
<link rel="stylesheet" type="text/css" href="../../../library/js/fancybox/jquery.fancybox-1.2.6.css" media="screen" />
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<link rel="stylesheet" href="../../css/tabulous.css"/>
  <!--<link rel="stylesheet" href="/resources/demos/style.css">-->
<script type="text/javascript">
$(document).ready(function(){
                  
    tabbify();
    enable_modals();
    // special size for
	$(".medium_modal").fancybox({
		'overlayOpacity' : 0.0,
		'showCloseButton' : true,
		'frameHeight' : 460,
		'frameWidth' : 650
	});
   
});


var mypcc = '<?php echo $GLOBALS['phone_country_code'] ?>';

//code used from http://tech.irt.org/articles/js037/
function replace(string,text,by) {
 // Replaces text with by in string
 var strLength = string.length, txtLength = text.length;
 if ((strLength == 0) || (txtLength == 0)) return string;

 var i = string.indexOf(text);
 if ((!i) && (text != string.substring(0,txtLength))) return string;
 if (i == -1) return string;

 var newstr = string.substring(0,i) + by;

 if (i+txtLength < strLength)
  newstr += replace(string.substring(i+txtLength,strLength),text,by);

 return newstr;
}

function upperFirst(string,text) {
 return replace(string,text,text.charAt(0).toUpperCase() + text.substring(1,text.length));
}

<?php for ($i=1;$i<=3;$i++) { ?>
function auto_populate_employer_address<?php echo $i ?>(){
 var f = document.demographics_form;
 if (f.form_i<?php echo $i?>subscriber_relationship.options[f.form_i<?php echo $i?>subscriber_relationship.selectedIndex].value == "self")
 {
  f.i<?php echo $i?>subscriber_fname.value=f.form_fname.value;
  f.i<?php echo $i?>subscriber_mname.value=f.form_mname.value;
  f.i<?php echo $i?>subscriber_lname.value=f.form_lname.value;
  f.i<?php echo $i?>subscriber_street.value=f.form_street.value;
  f.i<?php echo $i?>subscriber_city.value=f.form_city.value;
  f.form_i<?php echo $i?>subscriber_state.value=f.form_state.value;
  f.i<?php echo $i?>subscriber_postal_code.value=f.form_postal_code.value;
  if (f.form_country_code)
    f.form_i<?php echo $i?>subscriber_country.value=f.form_country_code.value;
  f.i<?php echo $i?>subscriber_phone.value=f.form_phone_home.value;
  f.i<?php echo $i?>subscriber_DOB.value=f.form_DOB.value;
  if(typeof f.form_ss!="undefined")
    {
        f.i<?php echo $i?>subscriber_ss.value=f.form_ss.value;  
    }
  f.form_i<?php echo $i?>subscriber_sex.value = f.form_sex.value;
  f.i<?php echo $i?>subscriber_employer.value=f.form_em_name.value;
  f.i<?php echo $i?>subscriber_employer_street.value=f.form_em_street.value;
  f.i<?php echo $i?>subscriber_employer_city.value=f.form_em_city.value;
  f.form_i<?php echo $i?>subscriber_employer_state.value=f.form_em_state.value;
  f.i<?php echo $i?>subscriber_employer_postal_code.value=f.form_em_postal_code.value;
  if (f.form_em_country)
    f.form_i<?php echo $i?>subscriber_employer_country.value=f.form_em_country.value;
 }
}

<?php } ?>

function popUp(URL) {
 day = new Date();
 id = day.getTime();
// top.restoreSession();
 eval("page" + id + " = window.open(URL, '" + id + "', 'toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=1,width=400,height=300,left = 440,top = 362');");
}

function checkNum () {
 var re= new RegExp();
 re = /^\d*\.?\d*$/;
 str=document.demographics_form.monthly_income.value;
 if(re.exec(str))
 {
 }else{
  alert("<?php xl('Please enter a monetary amount using only numbers and a decimal point.','e'); ?>");
 }
}

// Indicates which insurance slot is being updated.
var insurance_index = 0;

// The OnClick handler for searching/adding the insurance company.
function ins_search(ins) {
	insurance_index = ins;
	return false;
}


// The ins_search.php window calls this to set the selected insurance.
function set_insurance(ins_id, ins_name) {
 var thesel = document.forms[0]['i' + insurance_index + 'provider'];
 var theopts = thesel.options; // the array of Option objects
 var i = 0;
 for (; i < theopts.length; ++i) {
  if (theopts[i].value == ins_id) {
   theopts[i].selected = true;
   return;
  }
 }
 // no matching option was found so create one, append it to the
 // end of the list, and select it.
 theopts[i] = new Option(ins_name, ins_id, false, true);
}

// This capitalizes the first letter of each word in the passed input
// element.  It also strips out extraneous spaces.
function capitalizeMe(elem) {
 var a = elem.value.split(' ');
 var s = '';
 for(var i = 0; i < a.length; ++i) {
  if (a[i].length > 0) {
   if (s.length > 0) s += ' ';
   s += a[i].charAt(0).toUpperCase() + a[i].substring(1);
  }
 }
 elem.value = s;
}

function divclick(cb, divid) {
 var divstyle = document.getElementById(divid).style;
 if (cb.checked) {
  divstyle.display = 'block';
 } else {
  divstyle.display = 'none';
 }
 return true;
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

 var msg = "";
 msg += "<?php xl('The following fields are required', 'e' ); ?>:\n\n";
 for ( var i = 0; i < errMsgs.length; i++ ) {
	msg += errMsgs[i] + "\n";
 }
 msg += "\n<?php xl('Please fill them in before continuing.', 'e'); ?>";

 if ( errMsgs.length > 0 ) {
	alert(msg);
 }
 

// Some insurance validation.
 for (var i = 1; i <= 3; ++i) {
  subprov = 'i' + i + 'provider';
  if (!f[subprov] || f[subprov].selectedIndex <= 0) continue;
  var subpfx = 'i' + i + 'subscriber_';
  var subrelat = f['form_' + subpfx + 'relationship'];
  var samename =
   f[subpfx + 'fname'].value == f.form_fname.value &&
   f[subpfx + 'mname'].value == f.form_mname.value &&
   f[subpfx + 'lname'].value == f.form_lname.value;
  var ss_regexp=/[0-9][0-9][0-9]-?[0-9][0-9]-?[0-9][0-9][0-9][0-9]/;
  var samess=true;
  var ss_valid=false;
  if(typeof f.form_ss!="undefined")
      {
        samess = f[subpfx + 'ss'].value == f.form_ss.value;
        ss_valid=ss_regexp.test(f[subpfx + 'ss'].value) && ss_regexp.test(f.form_ss.value);  
      }
  if (subrelat.options[subrelat.selectedIndex].value == "self") {
   if (!samename) {
    if (!confirm("<?php echo xls('Subscriber relationship is self but name is different! Is this really OK?'); ?>"))
     return false;
   }
   if (!samess && ss_valid) {
    if(!confirm("<?php echo xls('Subscriber relationship is self but SS number is different!')." ". xls("Is this really OK?"); ?>"))
    return false;
   }
  } // end self
  else {
   if (samename) {
    if (!confirm("<?php echo xls('Subscriber relationship is not self but name is the same! Is this really OK?'); ?>"))
     return false;
   }
   if (samess && ss_valid)  {
    if(!confirm("<?php echo xls('Subscriber relationship is not self but SS number is the same!') ." ". xls("Is this really OK?"); ?>"))
    return false;
   }
  } // end not self
 } // end for

 return errMsgs.length < 1;
}

function submitme() {
 var f = document.forms[0];
 //if (validate(f)) {
   f.submit(); 
   // Subhan - 20170206: Hidding the validation since we are not getting "demographic layout" here which is required for validations.
   // will try to fix in next release
 //}
}

// Onkeyup handler for policy number.  Allows only A-Z and 0-9.
function policykeyup(e) {
 var v = e.value.toUpperCase();
 for (var i = 0; i < v.length; ++i) {
  var c = v.charAt(i);
  if (c >= '0' && c <= '9') continue;
  if (c >= 'A' && c <= 'Z') continue;
  if (c == '*') continue;
  if (c == '-') continue;
  if (c == '_') continue;
  if (c == '(') continue;
  if (c == ')') continue;
  if (c == '#') continue;
  v = v.substring(0, i) + v.substring(i + i);
  --i;
 }
 e.value = v;
 return;
}
function search_ajax_way(index){
    var search_this=$('#i'+index+'plan_name').val();
    $.post("../../practice/get_payerplan_plans_search.php", {search_plan: search_this,ins_name:$('#i'+index+'provider').val()}, function(data){
    //alert(data);
    $("#plan_name"+index).html(data);

    })
} 
function payer(num){
    var name=document.getElementById('i'+num+'provider').value;
    document.getElementById('i'+num+'plan_name').value='';
    $("#i"+num+"plan_name").keyup(function(event){
        event.preventDefault();
        search_ajax_way(num);
    });
}


// Added 06/2009 by BM to make compatible with list_options table and functions - using jquery

$(document).ready(function() {

 <?php for ($i=1;$i<=3;$i++) { ?>
  $("#form_i<?php echo $i?>subscriber_relationship").change(function() { auto_populate_employer_address<?php echo $i?>(); });
 <?php } ?>

});

</script>
<link href="http://netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.2.0/jquery-confirm.min.css">
<link rel="stylesheet" href="../../drive_view/driveassets/css/uploadfile.css">
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.0/jquery-ui.js"></script>
<script src="../../../library/popover/js/jquery.webui-popover.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.2.0/jquery-confirm.min.js"></script>
<script src="../../js/tabulous.js"></script>
<script src="../../drive_view/driveassets/js/jquery.uploadfile.js"></script>
  <style>
    .body{
        height: 100%;
    }
  .project-description {
    margin: 0;
    padding: 0;
    font-size:10px
  }
  </style>
  <style>
  .custom-combobox {
    position: relative;
    display: inline-block;
    height: 20px; 
  }
  .custom-combobox-toggle {
    position: absolute;
    top: 0;
    bottom: 0;
    margin-left: -1px;
    padding: 0;
    height: 20px; 
    
  }
  .custom-combobox-input {
    margin: 0;
    padding: 5px 10px;
    height: 20px; 
    width: 190px;
  }
  .zirmedsmallsize{
      font-size: 10px;
  }
  #showeditdemographics.modal-body{
      padding: 0px !important;
  }
  
  .livesearch-contenar{
      position: relative;
      max-width: 417px;
  }
  
  .searchinsfield{
        border-radius: 8px;
        background-color:#fff;
        float:left;
        border:1px solid #ccc;
        height:30px;
        padding:1px 12px;
        font-size:15px;
        line-height: 1.42857;
        color:#000;
        width:420px;
    }
    
    .textbox-clr{
	cursor:pointer;
	background:#fff;
	width:20px; 
	height:42px;
	float:left;
	border:none;
	margin-top:13px; 
        text-align:center;
    }
    
    .livesearch{
        /*overflow: auto;*/
        position: absolute;
        display: table;
        box-shadow: 0px 0px 8px rgba(0, 0, 0, 0.24);
        border-radius: 3px;
        width: 100%;
        border: 1px solid #ccc;
        top:51px;
        background: #fff;
        padding: 8px;
        box-sizing: border-box;
        display: none;
        
    }
    
    #livesearchfilds{
        max-height: 260px;
        min-height: 100px;
        overflow: auto;
    }
    
    #livesearchfilds > div{
        display: table-cell;
        width: 50%;
    }
    #livesearchfilds ul{
        list-style: none;
        padding-left: 10px;
        font-size: 15px;
    }
    #livesearchfilds ul li{
        padding: 3px 3px 3px 6px;
    }
    #livesearchfilds ul li:hover{
        color: #000;
        cursor: pointer;
        background: #ccc;
    }
    
    .selected{
        color: #000;
        cursor: pointer;
        background: #ccc;
    }
    
    .livesearch:after{
        content: "";
        position: absolute;
        border-left: 10px solid transparent;
        border-right: 9px solid transparent;
        border-bottom: 10px solid #ffffff;
        /*border-top:10px solid white;*/
        top: initial;
        top: -9px;
        left: 50%;
        margin-left: -10px;
    }
    .searchitems:nth-child(1){
        padding-right: 5px;
        border-right: 1px solid;
    }
    .searchitems:nth-child(2){
        padding-left: 5px;
    }
    
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
    #loader2{
        background: rgba(0,0,0,0.56);
        border-radius: 4px;
        display:table;
        height: 48px;
        width: 245px;
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

    #planbox{
        position: absolute;
        left:0;
        top:0;
        background:rgba(0,0,0,0.5);
        width: 100%;
        height: 100%;
        z-index: 99999;
        display: none;
    }

    .modalbox{
        position: relative;
        margin: auto;
        top:5%;
        border: 1px solid rgba(0,0,0,.2);
        width: 80%;
        height:90%;
        background: #fff;
        border-radius: 6px;
        -moz-box-shadow:0 5px 15px rgba(0,0,0,.5);
        box-shadow: 0 5px 15px rgba(0,0,0,.5);
    }
    .modalbox header{
        min-height: 16.43px;
        padding: 15px;
        border-bottom: 1px solid #e5e5e5;
    }
    .modalbox section.modal-body{
        position: relative;
        padding: 15px;
        height: 67%;
        overflow-y: scroll;
    }
    .modalbox footer{
        padding: 8px 20px;
        text-align: right;
        border-top: 1px solid #e5e5e5;
    }

    .modalbox header .close {
        float: right;
        font-size: 21px;
        font-weight: 700;
        line-height: 1;
        color: #000;
        text-shadow: 0 1px 0 #fff;
        filter: alpha(opacity=20);
        opacity: .2;
    }

    .modalbox header button.close {
        -webkit-appearance: none;
        padding: 0;
        cursor: pointer;
        background: 0 0;
        border: 0;
    }
    .modalbox header .close{
        margin-top: -2px;
    }
    .accordion {
        background-color: #eee;
        color: #444;
        cursor: pointer;
        padding: 10px;
        text-align: left;
        border: none;
        outline: none;
        transition: 0.4s;
        position: relative;
    }
    div.panel > .accordion {
        background-color: #e9e2f8;
    }
    .accordion.active,.accordion:hover {
        background-color: #ddd;
    }
    div.panel > .accordion.active,div.panel > .accordion:hover {
        background-color: #d5c7f3;
    }
    div.panel {
        padding: 0 18px;
        background-color: white;
        height: auto;
        background: #fff;
        position: relative;
        display: none;
    }
    .accordion:after {
        content: '\002B';
        color: #777;
        font-weight: bold;
        float: right;
        margin-left: 5px;
    }

    .accordion.active:after {
        content: "\2212";
    }

    .btn {
        display: inline-block;
        padding: 6px 12px;
        margin-bottom: 0;
        font-size: 14px;
        font-weight: 400;
        line-height: 1.42857143;
        text-align: center;
        white-space: nowrap;
        vertical-align: middle;
        -ms-touch-action: manipulation;
        touch-action: manipulation;
        cursor: pointer;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
        background-image: none;
        border: 1px solid transparent;
        border-radius: 4px;
    }

    .btn-primary {
        color: #fff;
        background-color: #337ab7;
        border-color: #2e6da4;
    }
    .btn:disabled{
        opacity: 0.6;
    }

    /*insturance card button css*/
    a.insbutton:visited
    {
        color: #fff;
    }
    .insbutton {

        display: inline-block;
        height: 40px;
        line-height: 40px;
        padding-right: 10px;
        padding-left: 70px;
        position: relative;
        background-color:rgb(0,0,0);
        color:rgb(255,255,255);
        text-decoration: none;
        text-transform: lowercase;
        letter-spacing: 1px;
        text-shadow:0px 1px 0px rgba(0,0,0,0.5);
      -ms-filter:"progid:DXImageTransform.Microsoft.dropshadow(OffX=0,OffY=1,Color=#ff123852,Positive=true)";zoom:1;
      filter:progid:DXImageTransform.Microsoft.dropshadow(OffX=0,OffY=1,Color=#ff123852,Positive=true);

        -moz-box-shadow:0px 2px 2px rgba(0,0,0,0.2);
        -webkit-box-shadow:0px 2px 2px rgba(0,0,0,0.2);
        box-shadow:0px 2px 2px rgba(0,0,0,0.2);
        -ms-filter:"progid:DXImageTransform.Microsoft.dropshadow(OffX=0,OffY=2,Color=#33000000,Positive=true)";
      filter:progid:DXImageTransform.Microsoft.dropshadow(OffX=0,OffY=2,Color=#33000000,Positive=true);
      }

      .insbutton:hover{
        text-decoration: none;
        color: #eeeaee !important;
      }

      .insbutton p{font-size: 18px; margin: 0;}
      .insbutton span {
        position: absolute;
        left: 0;
        width: 60px;
        font-size:30px;
        -webkit-border-top-left-radius: 5px;
        -webkit-border-bottom-left-radius: 5px;
        -moz-border-radius-topleft: 5px;
        -moz-border-radius-bottomleft: 5px;
        border-top-left-radius: 5px;
        border-bottom-left-radius: 5px;
        border-right: 1px solid  rgba(0,0,0,0.15);
        text-decoration: none;
        padding: 2px;
      }
      .insbutton.insturancebtn{
        background: #25a0ca;
      }
      .insurancecard{
          text-align: center;
          margin-top: -10px;
      }
      .jconfirm .jconfirm-box{
          width: 40%;
      }
      @media only screen and (max-width: 768px) {
        .jconfirm .jconfirm-box{
          width: 80%;
        }
      }
      #insuracecardview{
        position: relative;
        width: 100%;
        overflow: hidden;
      }
      #frontcard, #backcard{
          border: 2px solid #000000;
          border-radius: 3px;
          position: relative;
          left: 0;
          top: 0;
          cursor: pointer;
      }
      #frontcard{
          float: left;
      }
      #backcard{
          float: right;
      }
      #frontcard:hover .cardins, #backcard:hover .cardins{
          display: block;
      }
      .cardins{
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.2);
        display: none;
        
      }
      .cardins > div{
        font-size: 22px;
        text-align: center;
        padding: 10px;
        box-sizing: border-box;
        color: #fff;
        position: absolute;
        top:50%;
        -webkit-transform: translateY(-50%);
        -moz-transform:translateY(-50%);
        transform: translateY(-50%);
      }
      .ajax-file-upload-statusbar{
          border: none;
          width: 100%;
          margin: 0;
          padding: 0;
      }
      .ajax-file-upload-progress{
        position: absolute;
        left: 0;
        top: 45%;
        margin: auto;
        right: 0;
      }
      .ajax-file-upload{
        position: absolute !important;
        cursor: default;
        width: 100%;
        left: 0;
        top: 0;
        height: 100%;
        opacity: 0;
      }
      .ajax-file-upload-progress{
          border-color: #000;
          border-radius: 11px;
      }
      .ajax-file-upload-progress > .ajax-file-upload-bar{
          border-radius: 11px;
          height: 13px;
      }
           
  </style>
  <script>
      $(document).ready(function(){
          var ajaxcallcompleted = false;
          var searchdata = null;
          $.fn.liveserarch = function(){
              var self = this;
              self.find("#livesearch").hide();
              $('body').click(function(evt){
                 if(!$(evt.target).closest(self).length)
                    self.find("#livesearch").hide();
              });
            
             self.find("#searchins").on("input",function(){
                 fiterdata(window.searchdata,$(this).val(),self.find('.livesearch').find("#practice"),self.find('.livesearch').find("#central"));
             });
             self.find("#searchins").focus(function(){
                 var _self = $(this);
                 self.find("#livesearch").show();
                 if(!window.ajaxcallcompleted){
                     self.find("#loader").show();
                     $.ajax({url:"getajaxpayers.php",type:"post",data:null,dataType: 'json',success: function (data, textStatus, jqXHR) {
                             self.find("#loader").hide();
                             window.searchdata = data;
                             window.ajaxcallcompleted = true;
                             //fiterdata(searchdata,"",_slef.next('.livesearch').find("#practice"),_slef.next('.livesearch').find("#central"));
                             fiterdata(window.searchdata,_self.val(),_self.next('.livesearch').find("#practice"),_self.next('.livesearch').find("#central"));
                        }, error: function (jqXHR, textStatus, errorThrown){
                            
                        }
                    })
                 }else{
                     fiterdata(window.searchdata,_self.val(),self.find('.livesearch').find("#practice"),self.find('.livesearch').find("#central"));
                 }
             });
             self.find("#searchins").blur(function(evt){
                 evt.preventDefault();
             });
             
             function fiterdata(sourse,key,practicecontenar, centeralcontenar){
                 var practicehtml = "<ul data-payertype='practice'>";
                 var centeralhtml= "<ul  data-payertype='central'>";
                 var practice = $.grep(sourse['practice'],function(item){
                    if(item['name'].toLowerCase().indexOf(key.trim().toLowerCase()) != -1)
                       return true;
                 });
                 var central = $.grep(sourse['central'],function(item){
                     if(item['name'].toLowerCase().indexOf(key.trim().toLowerCase()) != -1)
                       return true;
                 });
                 
                 $.each(practice,function(index,value){
                     practicehtml += "<li data-pid='"+value['id']+"'><div>"+value['name']+"</div></li>"
                 });
                 
                 $.each(central,function(index,value){
                     pracIds = value['relatedpractice'].split("|");
                     currentpracId = $("#thispracticeid").val();
                     notEnrolled = "";
                     if(pracIds.indexOf(currentpracId) < 0){
                         notEnrolled = " -- Not Enrolled with this Practice";
                     }
                     centeralhtml += "<li data-pid='"+value['insuranceid']+"'><div class='listval'>"+value['name']+"</div><span style='font-size:9px;color:red;'>"+notEnrolled+"</span></li>";
                 });
                 
                 practicehtml += "</ul>";
                 centeralhtml += "</ul>";
                if(!practice.length)
                     practicehtml = "<div>No mached found!!</div>";
                 
                if(!central.length)
                     centeralhtml = "<div>No mached found!!</div>";
                 
                 practicecontenar.find("div").html(practicehtml);
                 centeralcontenar.find("div").html(centeralhtml);
                 
             }
             
             $(self).on("click","#livesearch #practice ul li,#livesearch #central ul li",function(evt){
                
                var getdata = $(this).data("pid");
                var ispractice = $(this).parent('ul').data("payertype") == 'practice'?true:false;
                self.find("#searchins").val($(this).children('div').text());
                $(this).parent('ul').find("li").removeClass("selected");
                $(this).addClass("selected");
                $(this).parents("#livesearch").find('.hiddenfiled').val(getdata);
                $(self).next('.insurancecard').data("insuranceid",getdata);
                $(this).parents("#livesearch").hide();
                $("#planbox").data("providervalue",$(this).parents("#livesearch").find('.hiddenfiled').attr("id"));
                $("#loader2").show().find("#loadertitle").text("Plans Loading...");
                //console.log(ispractice)
                $(".modal-title").html("Select Plan Name");
                  $.ajax({url:ispractice==true?"ajaxgetplans.php":"getinsviacentral.php",type:"POST",data:{insid:getdata},success: function (data, textStatus, jqXHR) {
                        var jsondata = $.parseJSON(data);
                       $("#loader2").hide();
                        var planshtml = "";
                        $("#planbox").show();
                        $("#planbox footer").show();
                        $("#planbox .modal-body").css({height:"67%","overflow-y":"scroll"});
                        $("#planbox").data("planfield",self.data("planfield"));
                        $("#planbox").data("planid",self.data("planid"));
                        $("#planbox").data("ispractice",ispractice);
                        $("#planbox").data("getdata",getdata);
                        $('body').css("overflow","hidden");
                        $('body').scrollTop(0);
                        //console.log(jsondata);
                        $(".modal-title").html(self.find(".searchinsfield").val());
                        planshtml += "<p style='color: green;'>Please select the below plan</p>"
                        var counter = 0;
                        $.each(jsondata['insplans'],function(index,value){
                            counter++;
                             planshtml += '<label for="plan'+counter+'"><div class="accordion" style="margin-top:2px;"><input type="radio" name="plane" id="plan'+counter+'" data-planname="'+value['planname']+'" data-planid="'+value['id']+'">'+value['planname']+"</div></label>";
                             planshtml += '<div class="panel">';
                             var benfit = 0;
                             //console.log(value['benefits'][value['id']])
                             $.each(value['benefits'][value['id']],function(i,v){
                                  benfit++;
                                  var tbname = value['id']+""+benfit;
                                  planshtml += '<div class="accordion" style="margin-top:5px;">Benefit '+benfit+ '</div>';
                                  planshtml +=  '<div class="panel">\n\
                                        <div class="tabs">\n\
                                                <ul>';
                                  var tabcount = 0;              
                                                $.each(v,function(j,b){
                                                    tabcount++;
                                                    planshtml += '<li><a href="#'+tbname+'tabs-'+tabcount+'" title="">'+j.replace(/_/g,' ')+'</a></li>';
                                                });  
                                  planshtml +=  '</ul>';
                                  planshtml +=  '<div class="tabs_container">';
                                  tabcount = 0;  
                                                $.each(v,function(j,b){
                                                    tabcount++;
                                                    planshtml += '<div id="'+tbname+'tabs-'+tabcount+'">'
                                                    planshtml += '<table width="100%">'
                                                                    $.each(b,function(k,l){
                                                                        var benifitval = l != ""?l:"--"
                                                                         planshtml += '<tr>\n\
                                                                                        <td width="50%"><b>'+k.replace(/_/g,' ')+'</b></td>\n\
                                                                                        <td width="50%">'+benifitval+'</td>\n\
                                                                                       </tr>';
                                                                                       
                                                                    });
                                                     planshtml += '</table>\n\
                                                               </div>';
                                                });
                                   planshtml +=  '</div>\n\
                                        </div>\n\
                                    </div>';
                             });
                             if(!value['benefits'][value['id']].length)
                                planshtml += "No Benefits this Plan.<br/>"; 
                             planshtml += '</div>';           
                        });
                        if(!jsondata['insplans'].length)
                           planshtml ="<h4 style='text-align:center;'>No Plans this insurance.</h4>";
                        $("#planbox .modalbox").find("section").html(planshtml);
                         $("#planbox #planselected").prop("disabled",true);
                        $('.tabs').tabulous({
                            effect: 'scale'
                        });
                    },error: function (jqXHR, textStatus, errorThrown) {
                        $("#loader2").hide();
                    }
                })
             });
             
          }
          
           $.fn.showinscard = function(useroptions){
               
               var _defaults = {name:"ins"};
               var opts = $.extend({},_defaults,useroptions);
               var element = this;
               return element.each(function(){
                   //console.log(this);
                    var $emt = $(this);
                    $emt.find('.insturancebtn').click(function(){
                        //console.log($emt.data('insuranceid') + " : " + $emt.data('name'))
                        if($emt.data('insuranceid') == ""){
                             $.alert({
                                  title: 'Alert!',
                                  content: 'You should enter the insurance field.',
                              });
                         return;
                        }

                         var pid = $emt.data("pid");
                         var name = $emt.data("name");
                         var insid = $emt.data("insuranceid");

                         opts.loader.show().find("#loadertitle").text("Insurance Card Loading...");
                         $.ajax({url:opts.carddownloadurl, data:{"pid":pid,"name":name,"insid":insid}, type:"POST", 
                             success: function (data, textStatus, jqXHR) {
                                 var images = $.parseJSON(data);
                                 opts.loader.hide();
                                 console.log(images);
                                 $("#planbox footer").hide();
                                 $("#planbox").show();
                                 $(".modal-title").html("Your Insurance Card");
                                 $("#planbox .modal-body").css({height:"80%","overflow-y":"auto"});
                                 $('body').css("overflow","hidden");
                                 $('body').scrollTop(0);
                                 var cardhtml = "";
                                 var placeholder = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAV4AAADgCAYAAABLnA6rAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAABXBpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuNi1jMDY3IDc5LjE1Nzc0NywgMjAxNS8wMy8zMC0yMzo0MDo0MiAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczpkYz0iaHR0cDovL3B1cmwub3JnL2RjL2VsZW1lbnRzLzEuMS8iIHhtbG5zOnhtcE1NPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvbW0vIiB4bWxuczpzdEV2dD0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL3NUeXBlL1Jlc291cmNlRXZlbnQjIiB4bWxuczpzdFJlZj0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL3NUeXBlL1Jlc291cmNlUmVmIyIgeG1wOkNyZWF0b3JUb29sPSJBZG9iZSBQaG90b3Nob3AgQ0MgMjAxNSAoV2luZG93cykiIHhtcDpDcmVhdGVEYXRlPSIyMDE3LTAzLTMxVDE4OjI2OjMwKzA1OjMwIiB4bXA6TW9kaWZ5RGF0ZT0iMjAxNy0wMy0zMVQxODo0Nzo1NyswNTozMCIgeG1wOk1ldGFkYXRhRGF0ZT0iMjAxNy0wMy0zMVQxODo0Nzo1NyswNTozMCIgZGM6Zm9ybWF0PSJpbWFnZS9wbmciIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6NzRGMEQ2OEIxNjE0MTFFNzk0ODhDNTI2Rjk2NEVDNUQiIHhtcE1NOkRvY3VtZW50SUQ9Inht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";
                                 cardhtml +="<div id='insuracecardview'>\n\
                                                 <div id='frontcard'>\n\
                                                 </div>\n\
                                                 <div id='backcard'><div id='dm'></div>\n\
                                                 </div>\n\
                                         </div>"; 
                                 $("#planbox .modalbox").find("section").html(cardhtml);
                                 var frontimages = images['front']==""?placeholder:images['front'];
                                 var backimages = images['back']==""?placeholder:images['back'];    
                                 $("#frontcard").uploadFile({
                                     url:opts.carduplodurl,
                                     fileName:"myfile",
                                     acceptFiles:"image/gif, image/jpeg, image/png",
                                     showPreview:true,
                                     previewWidth: "350px",
                                     multiple:false,
                                     dragDrop:false,
                                     showCancel:false,
                                     showAbort:false,
                                     showFileCounter:false,
                                     showFileSize:false,
                                     showQueueDiv:"frontcard",
                                     dynamicFormData:function(){
                                         var data ={"pid":pid,"name":name,"insid":insid,cardside:"front"};
                                         return data;

                                     },
                                     customProgressBar: function(obj,s){
                                             this.statusbar = $("<div class='ajax-file-upload-statusbar'></div>");
                                             this.preview = $("<img class='ajax-file-upload-preview' />").width(s.previewWidth).height(s.previewHeight).appendTo(this.statusbar).hide();
                                             this.filename = $("<div class='ajax-file-upload-filename'></div>").appendTo(this.statusbar).hide();
                                             this.progressDiv = $("<div class='ajax-file-upload-progress'>").appendTo(this.statusbar).hide();
                                             this.progressbar = $("<div class='ajax-file-upload-bar'></div>").appendTo(this.progressDiv);
                                             this.abort = $("<div>" + s.abortStr + "</div>").appendTo(this.statusbar).hide();
                                             this.cancel = $("<div>" + s.cancelStr + "</div>").appendTo(this.statusbar).hide();
                                             this.done = $("<div>" + s.doneStr + "</div>").appendTo(this.statusbar).hide();
                                             this.download = $("<div>" + s.downloadStr + "</div>").appendTo(this.statusbar).hide();
                                             this.del = $("<div>" + s.deletelStr + "</div>").appendTo(this.statusbar).hide();
                                             this.abort.addClass("ajax-file-upload-red");
                                             this.done.addClass("ajax-file-upload-green");
                                             this.download.addClass("ajax-file-upload-green");            
                                             this.cancel.addClass("ajax-file-upload-red");
                                             this.del.addClass("ajax-file-upload-red");
                                         return this;
                                     },onLoad:function(obj){
                                         //console.log(obj);
                                         obj.prepend("<div class='ajax-file-upload-statusbar'><img src='"+frontimages+"' alt='Insturance Card Front Side' width='350px'/></div>");
                                         $("<div class='cardins'><div><span>Click here to upload front image of your card.</span></div></div>").insertAfter('#frontcard .ajax-file-upload-statusbar');
                                     },onSelect:function(obj)
                                     {
                                         $('#frontcard .ajax-file-upload-statusbar').remove();

                                     },onSuccess:function(files,data,xhr,pd)
                                     {
                                         $(pd.progressDiv).hide();
                                         //files: list of files
                                         //data: response from server
                                         //xhr : jquer xhr object
                                         //console.log(pd)
                                     }

                                 });

                                 $("#dm").uploadFile({
                                     url:opts.carduplodurl,
                                     fileName:"myfile",
                                     acceptFiles:"image/gif, image/jpeg, image/png",
                                     showPreview:true,
                                     previewWidth: "350px",
                                     multiple:false,
                                     dragDrop:false,
                                     showCancel:false,
                                     showAbort:false,
                                     showFileCounter:false,
                                     showFileSize:false,
                                     dynamicFormData:function(){
                                         var data ={"pid":pid,"name":name,"insid":insid,cardside:"back"};
                                         return data;
                                     },
                                     showQueueDiv:"dm",
                                     customProgressBar: function(obj,s){
                                         this.statusbar = $("<div class='ajax-file-upload-statusbar'></div>");
                                         this.preview = $("<img class='ajax-file-upload-preview' />").width(s.previewWidth).height(s.previewHeight).appendTo(this.statusbar).hide();
                                         this.filename = $("<div class='ajax-file-upload-filename'></div>").appendTo(this.statusbar).hide();
                                         this.progressDiv = $("<div class='ajax-file-upload-progress'>").appendTo(this.statusbar).hide();
                                         this.progressbar = $("<div class='ajax-file-upload-bar'></div>").appendTo(this.progressDiv);
                                         this.abort = $("<div>" + s.abortStr + "</div>").appendTo(this.statusbar).hide();
                                         this.cancel = $("<div>" + s.cancelStr + "</div>").appendTo(this.statusbar).hide();
                                         this.done = $("<div>" + s.doneStr + "</div>").appendTo(this.statusbar).hide();
                                         this.download = $("<div>" + s.downloadStr + "</div>").appendTo(this.statusbar).hide();
                                         this.del = $("<div>" + s.deletelStr + "</div>").appendTo(this.statusbar).hide();
                                         this.abort.addClass("ajax-file-upload-red");
                                         this.done.addClass("ajax-file-upload-green");
                                         this.download.addClass("ajax-file-upload-green");            
                                         this.cancel.addClass("ajax-file-upload-red");
                                         this.del.addClass("ajax-file-upload-red");
                                         return this;
                                     },onLoad:function(obj){
                                         //console.log(obj);
                                             obj.prepend("<div class='ajax-file-upload-statusbar'><img src='"+backimages+"' alt='Insturance Card Back Side' width='350px'/></div>");
                                             $("<div class='cardins'><div><span>Click here to upload back image of your card.</span></div></div>").insertAfter('#dm .ajax-file-upload-statusbar');
                                     },onSelect:function(obj)
                                     {
                                         $('#dm .ajax-file-upload-statusbar').remove();

                                     },onSuccess:function(files,data,xhr,pd)
                                     {
                                         $(pd.progressDiv).hide();
                                         //files: list of files
                                         //data: response from server
                                         //xhr : jquer xhr object
                                         //console.log(pd)
                                     }

                                 });
                                 $('.fileupload-buttonbar-text').text("");
                             }, 
                             error: function (jqXHR, textStatus, errorThrown) {
                                 alert("Insurace Card loading Error");
                             }
                         });
                         console.log("clicked insbtn");
                    });
               });
               //console.log(opts);
               
           };
          
          $(".livesearch-contenar").eq(0).liveserarch();
          $(".livesearch-contenar").eq(1).liveserarch();
          $(".livesearch-contenar").eq(2).liveserarch();
          $('.insurancecard').showinscard({carduplodurl:"cardUpload.php",loader:$("#loader2"),carddownloadurl:"getInsCardImage.php"});
          
          $("#planbox .close").click(function(){
            $("#planbox").hide();
            $('body').css("overflow","auto");
          });
          
          $("#planbox #planselected").click(function(){
              if($("#planbox").data("ispractice")){
                $("#planbox").hide();
                $('body').css("overflow","auto");
                var checkedval = $("#planbox").find("input[type=radio]:checked");
                $('input[name="'+$("#planbox").data("planfield")+'"]').val(checkedval.data("planname"));
                $('input[name="'+$("#planbox").data("planid")+'"]').val(checkedval.data("planid"));
             }else{
                 $.confirm({
                    title: '',
                    escapeKey: false, // close the modal when escape is pressed.
                    content: 'Confirm to Add this Insurance',
                    backgroundDismiss: true, // for escapeKey to work, backgroundDismiss should be enabled.
                    buttons: {
                        okay: {
                            keys: [
                                'enter'
                            ],
                            action: function () {
                                $.ajax({url:"addinsviacentral.php",data:{insid:$("#planbox").data("getdata")},type:"post",success: function (data, textStatus, jqXHR) {
                                    var data = $.parseJSON(data);
                                    $("#"+$("#planbox").data("providervalue")).val(data['insId']);
                                    $("#planbox").hide();
                                    $('body').css("overflow","auto");
                                    var checkedval = $("#planbox").find("input[type=radio]:checked");
                                    $('input[name="'+$("#planbox").data("planfield")+'"]').val(checkedval.data("planname"));
                                    $('input[name="'+$("#planbox").data("planid")+'"]').val(checkedval.data("planid"));
                                },error: function (jqXHR, textStatus, errorThrown) {
                                    alert("error to save data!!");
                                }
                               })
                               
                            }
                        },
                        cancel: {
                            keys: [
                                'ctrl',
                                'shift'
                            ],
                            action: function () {
                                $("#planbox").hide();
                                $('body').css("overflow","auto");
                                $("#searchins").val("");
                            }
                        }
                    }
                });
             }
          });
          
          $("#planbox").on("click",".accordion",function(evt){
              evt.preventDefault();
              $("#planbox #planselected").prop("disabled",false);
              $(this).find('input[type=radio]').prop("checked",true);
              $(this).parent().next(".panel").slideToggle();
              $(this).next(".panel").slideToggle();
              $(this).toggleClass("active");
          });
          $("#history").click(function(){
                $("#planbox footer").hide();
                $("#planbox").show();
                $(".modal-title").html("History");
                $("#planbox .modal-body").css({height:"80%","overflow-y":"auto"});
                $('body').css("overflow","hidden");
                $("#planbox .modalbox").find("section").html("<iframe src='../../patient_insurance_history.php' width='100%' height='100%' style='border:none;'></iframe>");
          });
      })
      function removedefault(evt){
        evt.preventDefault();
    }
  </script>
  
</head>

<body bgcolor='#FFFFFF'>
<form action='insurancedata_save.php' name='demographics_form' method='post' onsubmit='return validate(this)'>
<input type='hidden' name='mode' id='mode' value='save' />
<input type='hidden' name='db_id' value="<?php echo $result['id']?>" />

<table cellpadding='0' cellspacing='0' border='0' >
	<tr>
		<td>
			<?php if ($GLOBALS['concurrent_layout']) { ?>
			<a href="demographics.php">
			<?php } else { ?>
			<a href="patient_summary.php" target="Main">
			<?php } ?>
			<font class=title><?php xl('Current Patient','e'); ?></font>
			</a>
			&nbsp;&nbsp;
		</td>
		<td>
                    <script>
                        window.onload = function () { document.getElementById("demo_save_button").style.visibility = "visible"; }
                    </script>
                    <a href="javascript:submitme();" class='css_button' name='demo_save_button' id='demo_save_button' style="visibility:hidden">
				<span><?php xl('Save','e'); ?></span>
			</a>
		</td>
<!--		<td>
			<?php //if ($GLOBALS['concurrent_layout']) { ?>
			<a class="css_button" href="demographics.php" onclick="top.restoreSession()">
			<?php  // } else { ?>
			<a href="patient_summary.php" target="Main" onclick="top.restoreSession()">
			<?php //} ?>
			<span><?php // xl('Cancel','e'); ?></span>
			</a>
		</td>-->
	</tr>
</table>
<a id="history" style="float: right;margin-top: -19px;margin-right: 23px;" href="javascript:void()" title="History"><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADIAAAAyCAYAAAAeP4ixAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAA+tpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuNi1jMDY3IDc5LjE1Nzc0NywgMjAxNS8wMy8zMC0yMzo0MDo0MiAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczpkYz0iaHR0cDovL3B1cmwub3JnL2RjL2VsZW1lbnRzLzEuMS8iIHhtbG5zOnhtcE1NPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvbW0vIiB4bWxuczpzdFJlZj0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL3NUeXBlL1Jlc291cmNlUmVmIyIgeG1wOkNyZWF0b3JUb29sPSJBZG9iZSBQaG90b3Nob3AgQ0MgMjAxNSAoV2luZG93cykiIHhtcDpDcmVhdGVEYXRlPSIyMDE3LTA0LTA3VDE4OjU1OjI5KzA1OjMwIiB4bXA6TW9kaWZ5RGF0ZT0iMjAxNy0wNC0wN1QyMDowNTowMyswNTozMCIgeG1wOk1ldGFkYXRhRGF0ZT0iMjAxNy0wNC0wN1QyMDowNTowMyswNTozMCIgZGM6Zm9ybWF0PSJpbWFnZS9wbmciIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6NjJENkZBREUxQjlGMTFFN0FDREM5RkU2QkZCMTdFNTQiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6NjJENkZBREYxQjlGMTFFN0FDREM5RkU2QkZCMTdFNTQiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RS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"></a>
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
<div class="section-header">
    <span class="text"><b><?php xl("Insurance", "e" )?></b></span>
</div>

<div id="DEM" >
<?php
 if(isset($_REQUEST['pid'])) $pid = $_REQUEST['pid'];
 if (! $GLOBALS['simplified_demographics']) {

	  $insurance_headings = array(xl("Primary Insurance Provider"), xl("Secondary Insurance Provider"), xl("Tertiary Insurance provider"));
	  $insurance_info = array();
	  $insurance_info[1] = getInsuranceData($pid,"primary");
	  $insurance_info[2] = getInsuranceData($pid,"secondary");
	  $insurance_info[3] = getInsuranceData($pid,"tertiary");
          $names = array('primary','secondary','tertiary');
	?>
	<div id="INSURANCE" >
		<ul class="tabNav">
		<?php
		foreach (array('primary','secondary','tertiary') as $instype) {
			?><li <?php echo $instype == 'primary' ? 'class="current"' : '' ?>><a href="/play/javascript-tabbed-navigation/"><?php $CapInstype=ucfirst($instype); xl($CapInstype,'e'); ?></a></li><?php
		}
		?>
		</ul>

	<div class="tabContainer">

	<?php
        $practiceId = '';
        $query = sqlStatement("SELECT title FROM list_options WHERE list_id= 'allcareConfig' AND option_id='practiceID'");
        while($row = sqlFetchArray($query)){
            $practiceId = $row['title'];
        }
           
           for($i=1;$i<=3;$i++) {
	   $result3 = $insurance_info[$i];
	?>

		<div class="tab <?php echo $i == 1 ? 'current': '' ?>" style='height:auto;width:auto'>		<!---display icky, fix to auto-->
                    <label>Insurance: </label>
                    <div class="livesearch-contenar" data-planfield="i<?php echo $i?>plan_name" data-planid="i<?php echo $i?>plan_id">
                        <?php
                        $insSaved = "";
                        foreach ($insurancei as $iid => $iname) {
                            if (strtolower($iid) == strtolower($result3{"provider"})){
                                $insSaved = strtolower($result3{"provider"});
                            }
                        }    
                        $saveProvName = $saveProvId = "";
                        if(!empty($insSaved)):
                            $provQuery = sqlStatement("SELECT * FROM insurance_companies WHERE id=".$insSaved);
                            $provName = sqlFetchArray($provQuery);
                            $saveProvName = $provName['name'];
                            $saveProvId = $provName['id'];
                        endif;    
                        ?>
                        <input type="text" autocomplete="off" id="searchins" class="searchinsfield" placeholder="Please Enter <?php echo $insurance_headings[$i -1]?>" tabindex="1" value="<?php echo $saveProvName; ?>">
                        <div id="livesearch" class="livesearch">
                            <div id="livesearchfilds">
                                <div id="practice" class="searchitems"><span style="text-align: center; display: block;"><b>Practice Specific Payers</b></span> <div></div></div>
                                <div id="central"  class="searchitems"><span style="text-align: center; display: block;"><b>Central Specific Payers</b></span> <div></div></div>
                            </div>
                            <div id="loader">
                                <div class="ajax-spinner-bars">
                                    <div class='bar-1'></div><div class='bar-2'></div><div class='bar-3'></div><div class='bar-4'></div><div class='bar-5'></div><div class='bar-6'></div><div class='bar-7'></div><div class='bar-8'></div><div class='bar-9'></div><div class='bar-10'></div><div class='bar-11'></div><div class='bar-12'></div><div class='bar-13'></div><div class='bar-14'></div><div class='bar-15'></div><div class='bar-16'></div>                </div>
                                <div id="loadertitle">Data Loading...</div>
                            </div>
                            
<!--                            <input type="hidden" id="hidden<?php echo $i; ?>" name="<?php echo str_replace(" ","_",$insurance_headings[$i -1]) ?>" class="hiddenfiled" value=""/>-->
                            <?php 
                                /*
                                 * patient id = $pid
                                 * insurance id for primary is i1provider. send front and back
                                 * plan id for primary is i1plan_id.
                                 */
                            ?>
                            <input type="hidden" id="hidden<?php echo $i; ?>" name="i<?php echo $i?>provider" class="hiddenfiled" value="<?php echo $saveProvId; ?>"/>
                            <input type="hidden" id="planid<?php echo $i; ?>" name="i<?php echo $i?>plan_id" class="hiddenfiled1" value=""/>
                        </div>
                    </div>
                    <div class="insurancecard" data-pid="<?php echo $pid; ?>" data-name="<?php echo $names[$i-1]; ?>" data-insuranceid="<?php echo $saveProvId; ?>" data-planid="">
                        <a href="javascript:void(0)" class="insbutton insturancebtn" ><span><i><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADMAAAAjCAYAAAA5dzKxAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAA+tpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuNi1jMDY3IDc5LjE1Nzc0NywgMjAxNS8wMy8zMC0yMzo0MDo0MiAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczpkYz0iaHR0cDovL3B1cmwub3JnL2RjL2VsZW1lbnRzLzEuMS8iIHhtbG5zOnhtcE1NPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvbW0vIiB4bWxuczpzdFJlZj0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL3NUeXBlL1Jlc291cmNlUmVmIyIgeG1wOkNyZWF0b3JUb29sPSJBZG9iZSBQaG90b3Nob3AgQ0MgMjAxNSAoV2luZG93cykiIHhtcDpDcmVhdGVEYXRlPSIyMDE3LTAzLTMwVDE1OjIzOjU2KzA1OjMwIiB4bXA6TW9kaWZ5RGF0ZT0iMjAxNy0wMy0zMFQxNTo0Mjo0NSswNTozMCIgeG1wOk1ldGFkYXRhRGF0ZT0iMjAxNy0wMy0zMFQxNTo0Mjo0NSswNTozMCIgZGM6Zm9ybWF0PSJpbWFnZS9wbmciIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6NkIxOTAzMTExNTMxMTFFN0I4OTU5RTJEMzMzQUQyMEIiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6NkIxOTAzMTIxNTMxMTFFN0I4OTU5RTJEMzMzQUQyMEIiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDo2QjE5MDMwRjE1MzExMUU3Qjg5NTlFMkQzMzNBRDIwQiIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDo2QjE5MDMxMDE1MzExMUU3Qjg5NTlFMkQzMzNBRDIwQiIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/PsTH4AYAAAPfSURBVHja7JlbSFRBGMfPeslLXovSlLKMTCvLIKObCClYdDHqwQIfinoONMoikooofBAregoki+ghIkpKy5AyKqOkDIrMhzSz7OqFyjQv2//T/4nhsMd1XXVd6YMf5+zMOYf5zzffzDezFqvVqo0Xs4xHMXPADhAOet2o/R6gGVwCz0RMAG4ug7Vu7JRHIEPE3MDNOhZ+Bx+BpxsI6AETQTR/v9YgpsvabyXAQ4adm5HF9lvFM3+gyhskgBd2eiMIxIEoEArk3UZQC9650EvtwE+UdVLYEhPl3iAbvLIObL9AIYhzgXdadM90QJUPWMlA0s0P7AOHlbJKcBe8YXz5g1kgEWwEE/jcLZALno6SZ9r6Ro2IYc+uUJSmgW8srwe7QMggeigFFCveOg08R8EzbQN55gl7ez/IM+mNI5znD9moWwruMMYiOUNq9GKAE2uZzLKfwBdHPFPBMmMPzAf3QQzfqwUJvEYbni3nNyKUsudW5y3PzDMeJj3Qxes0pSwWVIEkUAg+g9/gDDMIiacZhpgTU/OliGGIj1CzCi87L4qYNJAJUpTyVbyqjZ/K6bkUXAG+Nr53Esx1cpiVDFWM9O5WCqnj2JcZLJgrr3iwntdWxoSeFvnY+N6JkZzSzMToQ+MtWKMI2wYWgMecikVYNQP+PLjKNEMzTPOjYl6DfC6KHngJjoMysJoNPwe+MlltHWhMw3JAjBPDTGK8GFx3RIyHwUMNoIAJaRrTnnmcEkXoFqbi+YZEsG+boZTtAVOcdECvo2I0Q0NEVDbYy5ioUmarOMZVlUmHqPZhGMQ0OzrM6pQZqsnQ2zIb3WSsSIPTTbYMscq2QrfNIFDx2lDCoslRMQ/ATrDeRib9gzNZA1f4MNBheGYymARqQKeNThqxbbOtdCaYwdyhDCfVJPjLeT/TRvp/DBzk0CwwiPR1cp2Rdv10JJ0RzrJ8t430IR+8l70QOGCo81VSDz9D3cNhSGeOOprOaOxVSVdOgUVKeTrTl1wugin0jqak/2JZfF+12cMwmsKGss6IK5cz0KuZRVdxP7OYQzCkb+/dvwZpXDSTwTWmLkYrAgtBtxMTQIVp7QDDTCdDcfEGlsWDcJAEloFAcI/PVLpgp9mmp/j2xAipjA+xCyBSqZONWzfrilx0qPFPjH4GkGjnhSBQpnjpIqhRfme68ISmxeiZ+EG+uAlUGzZLIS4+bmrXxejDpxT4OPCB6WPk3CyH7e+VRbOR+3SN2W/9IE40O7kP9+dKb3HBWVk3MxA9beoRMak8fHB3267/C5DMND7AsGcf8/9icGG+Lfscy/8/m8ao/RVgAE5NPGEBfmVCAAAAAElFTkSuQmCC"/></i></span><p style="text-transform: capitalize">Show Insurance Card</p></a>
                    </div>
		<table border="0">

		 <tr>
		  <td valign=top width="430">
		   <table border="0">

			 <tr>
<!--			  <td valign='top'>
			   <span class='required'><?php echo $insurance_headings[$i -1]."&nbsp;"?></span>
			  </td>
			  <td class='required'>:</td>-->
			  <td>
                              
<!--                           <a href="../../practicesearch/ins_search.php" class="iframe medium_modal css_button" onclick="ins_search(<?php echo $i?>)">
				<span><?php echo xl('Search/Add') ?></span>
        			</a>
				<select name="i<?php echo $i?>provider">
				<option value=""><?php xl('Unassigned','e'); ?></option>
				<?php
				 foreach ($insurancei as $iid => $iname) {
				  echo "<option value='" . $iid . "'";
				  if (strtolower($iid) == strtolower($result3{"provider"}))
				   echo " selected";
				  echo ">" . $iname . "</option>\n";
				 }
				?>
			   </select>-->
<!--                              <div class="livesearch-contenar">
                                  <input type="text" autocomplete="off" name="searchins" id="searchins" class="searchinsfield" placeholder="Search with insurance key." tabindex="1">
                                  <button type="button" class="textbox-clr" id="textbox-clr" onClick="lightbg_clr()()"></button>
                                  <div id="livesearch" class="livesearch"></div>
                              </div>-->
                              
			  </td>
			 </tr>

			<tr>
			 <td>
			  <span class='required'><?php xl('Plan Name','e'); ?> </span>
			 </td>
			 <td class='required'>:</td>
			 <td>
			  <input type='entry' size='20' name='i<?php echo $i?>plan_name' value="<?php echo $result3{"plan_name"} ?>"
			   onchange="capitalizeMe(this);" />&nbsp;&nbsp;
			 </td>
			</tr>

			<tr>
			 <td>
			  <span class='required'><?php xl('Effective Date','e'); ?></span>
			 </td>
			 <td class='required'>:</td>
			 <td>
			  <input type='entry' size='16' id='i<?php echo $i ?>effective_date' name='i<?php echo $i ?>effective_date'
			   value='<?php echo $result3['date'] ?>'
			   onkeyup='datekeyup(this,mypcc)' onblur='dateblur(this,mypcc)'
			   title='yyyy-mm-dd' />
                          <img src='../../pic/show_calendar.gif' align='absbottom' width='24' height='22' id='img_i<?php echo $i ?>effective_date' border='0' alt='[?]' style='cursor:pointer' title='<?php xl('Click here to choose a date','e'); ?>'>
			 </td>
			</tr>

			<tr>
			 <td><span class=required><?php xl('Policy Number','e'); ?></span></td>
			 <td class='required'>:</td>
			 <td><input type='entry' size='16' name='i<?php echo $i?>policy_number' value="<?php echo $result3{"policy_number"}?>"
			  onkeyup='policykeyup(this)'></td>
			</tr>

			<tr>
			 <td><span class=required><?php xl('Group Number','e'); ?></span></td>
			 <td class='required'>:</td>
			 <td><input type=entry size=16 name=i<?php echo $i?>group_number value="<?php echo $result3{"group_number"}?>" onkeyup='policykeyup(this)'></td>
			</tr>

			<tr<?php if ($GLOBALS['omit_employers']) echo " style='display:none'"; ?>>
			 <td class='required'><?php xl('Subscriber Employer (SE)','e'); ?><br><span style='font-weight:normal'>
			  (<?php xl('if unemployed enter Student','e'); ?>,<br><?php xl('PT Student, or leave blank','e'); ?>) </span></td>
			  <td class='required'>:</td>
			 <td><input type=entry size=25 name=i<?php echo $i?>subscriber_employer
			  value="<?php echo $result3{"subscriber_employer"}?>"
			   onchange="capitalizeMe(this);" /></td>
			</tr>

			<tr<?php if ($GLOBALS['omit_employers']) echo " style='display:none'"; ?>>
			 <td><span class=required><?php xl('SE Address','e'); ?></span></td>
			 <td class='required'>:</td>
			 <td><input type=entry size=25 name=i<?php echo $i?>subscriber_employer_street
			  value="<?php echo $result3{"subscriber_employer_street"}?>"
			   onchange="capitalizeMe(this);" /></td>
			</tr>

			<tr<?php if ($GLOBALS['omit_employers']) echo " style='display:none'"; ?>>
			 <td colspan="3">
			  <table>
			   <tr>
				<td><span class=required><?php xl('SE City','e'); ?>: </span></td>
				<td><input type=entry size=15 name=i<?php echo $i?>subscriber_employer_city
				 value="<?php echo $result3{"subscriber_employer_city"}?>"
				  onchange="capitalizeMe(this);" /></td>
				<td><span class=required><?php echo ($GLOBALS['phone_country_code'] == '1') ? xl('SE State','e') : xl('SE Locality','e') ?>: </span></td>
			<td>
				 <?php
				  // Modified 7/2009 by BM to incorporate data types
			  generate_form_field(array('data_type'=>$GLOBALS['state_data_type'],'field_id'=>('i'.$i.'subscriber_employer_state'),'list_id'=>$GLOBALS['state_list'],'fld_length'=>'15','max_length'=>'63','edit_options'=>'C'), $result3['subscriber_employer_state']);
				 ?>
				</td>
			   </tr>
			   <tr>
				<td><span class=required><?php echo ($GLOBALS['phone_country_code'] == '1') ? xl('SE Zip Code','e') : xl('SE Postal Code','e') ?>: </span></td>
				<td><input type=entry size=15 name=i<?php echo $i?>subscriber_employer_postal_code value="<?php echo $result3{"subscriber_employer_postal_code"}?>"></td>
				<td><span class=required><?php xl('SE Country','e'); ?>: </span></td>
			<td>
				 <?php
				  // Modified 7/2009 by BM to incorporate data types
			  generate_form_field(array('data_type'=>$GLOBALS['country_data_type'],'field_id'=>('i'.$i.'subscriber_employer_country'),'list_id'=>$GLOBALS['country_list'],'fld_length'=>'10','max_length'=>'63','edit_options'=>'C'), $result3['subscriber_employer_country']);
				 ?>
			</td>
			   </tr>
			  </table>
			 </td>
			</tr>

		   </table>
		  </td>

		  <td valign=top>
		<table border="0">
			<tr>
				<td><span class=required><?php xl('Relationship','e'); ?></span></td>
				<td class=required>:</td>
				<td colspan=3><?php
					// Modified 6/2009 by BM to use list_options and function
					generate_form_field(array('data_type'=>1,'field_id'=>('i'.$i.'subscriber_relationship'),'list_id'=>'sub_relation','empty_title'=>' '), $result3['subscriber_relationship']);
					?>

				<a href="javascript:popUp('browse.php?browsenum=<?php echo $i?>')" class=text>(<?php xl('Browse','e'); ?>)</a></td>
				<td></td><td></td><td></td><td></td>
			</tr>
                        <tr>
				<td width=120><span class=required><?php xl('Subscriber','e'); ?> </span></td>
				<td class=required>:</td>
				<td colspan=3><input type=entry size=10 name=i<?php echo $i?>subscriber_fname	value="<?php echo $result3{"subscriber_fname"}?>" onchange="capitalizeMe(this);" />
				<input type=entry size=3 name=i<?php echo $i?>subscriber_mname value="<?php echo $result3{"subscriber_mname"}?>" onchange="capitalizeMe(this);" />
				<input type=entry size=10 name=i<?php echo $i?>subscriber_lname value="<?php echo $result3{"subscriber_lname"}?>" onchange="capitalizeMe(this);" /></td>
				<td></td><td></td><td></td><td></td>
			</tr>
			<tr>
				<td><span class=bold><?php xl('D.O.B.','e'); ?> </span></td>
				<td class=required>:</td>
				<td><input type='entry' size='11' id='i<?php echo $i?>subscriber_DOB' name='i<?php echo $i?>subscriber_DOB' value='<?php echo $result3['subscriber_DOB'] ?>' onkeyup='datekeyup(this,mypcc)' onblur='dateblur(this,mypcc)' title='yyyy-mm-dd' /><img src='../../pic/show_calendar.gif' align='absbottom' width='24' height='22' id='img_i<?php echo $i; ?>dob_date' border='0' alt='[?]' style='cursor:pointer' title='<?php xl('Click here to choose a date','e'); ?>'></td>

				<td><span class=bold><?php xl('Sex','e'); ?>: </span></td>
				<td><?php
					// Modified 6/2009 by BM to use list_options and function
					generate_form_field(array('data_type'=>1,'field_id'=>('i'.$i.'subscriber_sex'),'list_id'=>'sex'), $result3['subscriber_sex']);
					?>
				</td>
				<td></td><td></td> <td></td><td></td>
			</tr>
			<tr>
				<td class=leftborder><span class=bold><?php xl('S.S.','e'); ?> </span></td>
				<td class=required>:</td>
				<td><input type=entry size=11 name=i<?php echo $i?>subscriber_ss value="<?php echo trim($result3{"subscriber_ss"})?>"></td>
			</tr>

			<tr>
				<td><span class=required><?php xl('Subscriber Address','e'); ?> </span></td>
				<td class=required>:</td>
				<td><input type=entry size=20 name=i<?php echo $i?>subscriber_street value="<?php echo $result3{"subscriber_street"}?>" onchange="capitalizeMe(this);" /></td>

				<td><span class=required><?php echo ($GLOBALS['phone_country_code'] == '1') ? xl('State','e') : xl('Locality','e') ?>: </span></td>
				<td>
					<?php
					// Modified 7/2009 by BM to incorporate data types
					generate_form_field(array('data_type'=>$GLOBALS['state_data_type'],'field_id'=>('i'.$i.'subscriber_state'),'list_id'=>$GLOBALS['state_list'],'fld_length'=>'15','max_length'=>'63','edit_options'=>'C'), $result3['subscriber_state']);
				?>
				</td>
			</tr>
			<tr>
				<td class=leftborder><span class=required><?php xl('City','e'); ?></span></td>
				<td class=required>:</td>
				<td><input type=entry size=11 name=i<?php echo $i?>subscriber_city value="<?php echo $result3{"subscriber_city"}?>" onchange="capitalizeMe(this);" /></td><td class=leftborder><span class='required'<?php if ($GLOBALS['omit_employers']) echo " style='display:none'"; ?>><?php xl('Country','e'); ?>: </span></td><td>
					<?php
					// Modified 7/2009 by BM to incorporate data types
					generate_form_field(array('data_type'=>$GLOBALS['country_data_type'],'field_id'=>('i'.$i.'subscriber_country'),'list_id'=>$GLOBALS['country_list'],'fld_length'=>'10','max_length'=>'63','edit_options'=>'C'), $result3['subscriber_country']);
					?>
				</td>
</tr>
			<tr>
				<td><span class=required><?php echo ($GLOBALS['phone_country_code'] == '1') ? xl('Zip Code','e') : xl('Postal Code','e') ?> </span></td><td class=required>:</td><td><input type=entry size=10 name=i<?php echo $i?>subscriber_postal_code value="<?php echo $result3{"subscriber_postal_code"}?>"></td>

				<td colspan=2>
				</td><td></td>
			</tr>
			<tr>
				<td><span class=bold><?php xl('Subscriber Phone','e'); ?></span></td>
				<td class=required>:</td>
				<td><input type='text' size='20' name='i<?php echo $i?>subscriber_phone' value='<?php echo $result3["subscriber_phone"] ?>' onkeyup='phonekeyup(this,mypcc)' /></td>
				<td colspan=2><span class=bold><?php xl('CoPay','e'); ?>: <input type=text size="6" name=i<?php echo $i?>copay value="<?php echo $result3{"copay"}?>"></span></td>
				<td colspan=2>
				</td><td></td><td></td>
			</tr>
			<tr>
				<td colspan=0><span class='required'><?php xl('Accept Assignment','e'); ?></span></td>
				<td class=required>:</td>
				<td colspan=2>
					<select name=i<?php echo $i?>accept_assignment>
						<option value="TRUE" <?php if (strtoupper($result3{"accept_assignment"}) == "TRUE") echo "selected"?>><?php xl('YES','e'); ?></option>
						<option value="FALSE" <?php if (strtoupper($result3{"accept_assignment"}) == "FALSE") echo "selected"?>><?php xl('NO','e'); ?></option>
					</select>
				</td>
				<td></td><td></td>
				<td colspan=2>
				</td><td></td>
			</tr>
      <tr>
        <td><span class='bold'><?php xl('Secondary Medicare Type','e'); ?></span></td>
        <td class='bold'>:</td>
        <td colspan='6'>
          <select name=i<?php echo $i?>policy_type>
<?php
  foreach ($policy_types AS $key => $value) {
    echo "            <option value ='$key'";
    if ($key == $result3['policy_type']) echo " selected";
    echo ">" . htmlspecialchars($value) . "</option>\n";
  }
?>
          </select>
        </td>
      </tr>
		</table>

		  </td>
		 </tr>
		</table>

		</div>

	<?php } //end insurer for loop ?>
        <input type="hidden" id="thispracticeid" value="<?php echo $practiceId; ?>">
	</div>
</div>

<?php } // end of "if not simplified_demographics" ?>
</div>

</form>

<br>

<script language="JavaScript">

 // fix inconsistently formatted phone numbers from the database
 var f = document.forms[0];
 if (f.form_phone_contact) phonekeyup(f.form_phone_contact,mypcc);
 if (f.form_phone_home   ) phonekeyup(f.form_phone_home   ,mypcc);
 if (f.form_phone_biz    ) phonekeyup(f.form_phone_biz    ,mypcc);
 if (f.form_phone_cell   ) phonekeyup(f.form_phone_cell   ,mypcc);

<?php if (! $GLOBALS['simplified_demographics']) { ?>
 phonekeyup(f.i1subscriber_phone,mypcc);
 phonekeyup(f.i2subscriber_phone,mypcc);
 phonekeyup(f.i3subscriber_phone,mypcc);
<?php } ?>

<?php if ($GLOBALS['concurrent_layout'] && $set_pid) { ?>
 parent.left_nav.setPatient(<?php echo "'" . addslashes($result['fname']) . " " . addslashes($result['lname']) . "',$pid,'" . addslashes($result['pubpid']) . "','', ' " . xl('DOB') . ": " . oeFormatShortDate($result['DOB_YMD']) . " " . xl('Age') . ": " . getPatientAgeDisplay($result['DOB_YMD']) . "'"; ?>);
 parent.left_nav.setRadio(window.name, 'dem');
<?php } ?>

<?php echo $date_init; ?>
<?php if (! $GLOBALS['simplified_demographics']) { for ($i=1; $i<=3; $i++): ?>
 Calendar.setup({inputField:"i<?php echo $i?>effective_date", ifFormat:"%Y-%m-%d", button:"img_i<?php echo $i?>effective_date"});
 Calendar.setup({inputField:"i<?php echo $i?>subscriber_DOB", ifFormat:"%Y-%m-%d", button:"img_i<?php echo $i?>dob_date"});
<?php endfor; } ?>
</script>

<!-- include support for the list-add selectbox feature -->
<?php include $GLOBALS['fileroot']."/library/options_listadd.inc"; ?>

<div id="planbox">
    <div class="modalbox">
        <header>
            <button type="button" class="close" data-dismiss="modal" aria-label=""><span>&times;</span></button>
            <h4 class="modal-title" style="text-align:center; margin: 0;">Select Plan Name</h4>
        </header>
        <section class="modal-body">
            ...
        </section>
        <footer>
            <button class="btn btn-primary" id="planselected" disabled="">OK</button>
        </footer>
    </div>
</div>
<div id="loader2">
    <div class="ajax-spinner-bars">
        <div class='bar-1'></div><div class='bar-2'></div><div class='bar-3'></div><div class='bar-4'></div><div class='bar-5'></div><div class='bar-6'></div><div class='bar-7'></div><div class='bar-8'></div><div class='bar-9'></div><div class='bar-10'></div><div class='bar-11'></div><div class='bar-12'></div><div class='bar-13'></div><div class='bar-14'></div><div class='bar-15'></div><div class='bar-16'></div>                </div>
    <div id="loadertitle">Plans Loading...</div>
</div>
</body>

</html>