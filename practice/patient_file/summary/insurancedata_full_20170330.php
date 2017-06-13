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

//code used from https://tech.irt.org/articles/js037/
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
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.0/jquery-ui.js"></script>
<script src="../../../library/popover/js/jquery.webui-popover.js"></script>
<script src="../../js/tabulous.js"></script>
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
        margin-top:13px;
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
            padding: 15px;
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
                 var practicehtml = "<ul>";
                 var centeralhtml= "<ul>";
                 var practice = $.grep(sourse['practice'],function(item){
                    /*var patt = new RegExp(key.trim(),"i");
                    var res = patt.test(item['payer_name']);
                    return res;*/
                    if(item['name'].toLowerCase().indexOf(key.trim().toLowerCase()) != -1)
                       return true;
                 });
                 var central = $.grep(sourse['central'],function(item){
                     /*var patt = new RegExp(key.trim(),"i");
                     var res = patt.test(item['payer_name']);
                     
                     return res;*/
                     if(item['name'].toLowerCase().indexOf(key.trim().toLowerCase()) != -1)
                       return true;
                 });
                 
                 $.each(practice,function(index,value){
                     practicehtml += "<li data-pid='"+value['id']+"'>"+value['name']+"</li>"
                 });
                 
                 $.each(central,function(index,value){
                     pracIds = value['relatedpractice'].split("|");
                     currentpracId = $("#thispracticeid").val();
                     notEnrolled = "";
                     if(pracIds.indexOf(currentpracId) < 0){
                         notEnrolled = " -- Not Enrolled with this Practice";
                     }
                     centeralhtml += "<li data-pid='"+value['id']+"'>"+value['name']+"<span style='font-size:9px;color:red;'>"+notEnrolled+"</span></li>";
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
             
             $(self).on("click","#livesearch #practice ul li",function(evt){
                 var getdata = $(this).data("pid");
                  self.find("#searchins").val($(this).text());
                  $(this).parent('ul').find("li").removeClass("selected");
                  $(this).addClass("selected");
                  $(this).parents("#livesearch").find('.hiddenfiled').val(getdata);
                  $(this).parents("#livesearch").hide();
                  $.ajax({url:"ajaxgetplans.php",type:"POST",data:{insid:getdata},success: function (data, textStatus, jqXHR) {
                        var jsondata = $.parseJSON(data);
                        var planshtml = "";
                        $("#planbox").show();
                        $("#planbox").data("planfield",self.data("planfield"));
                        $("#planbox").data("planid",self.data("planid"));
                        $('body').css("overflow","hidden");
                        $('body').scrollTop(0);
                        console.log(jsondata);
                        $(".modal-title").html(self.find(".searchinsfield").val());
                        planshtml += "<p style='color: green;'>Please select the below plan</p>"
                        var counter = 0;
                        $.each(jsondata['insplans'],function(index,value){
                            counter++;
                             planshtml += '<label for="plan'+counter+'"><div class="accordion" style="margin-top:2px;"><input type="radio" name="plane" id="plan'+counter+'" data-planname="'+value['planname']+'" data-planid="'+value['id']+'">'+value['planname']+"</div></label>";
                             planshtml += '<div class="panel">';
                             var benfit = 0;
                             console.log(value['benefits'][value['id']])
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
                        $('.tabs').tabulous({
                            effect: 'scale'
                        });
                    },error: function (jqXHR, textStatus, errorThrown) {
                        
                    }
                })
             });
             
          }
          $(".livesearch-contenar").eq(0).liveserarch();
          $(".livesearch-contenar").eq(1).liveserarch();
          $(".livesearch-contenar").eq(2).liveserarch();
          $("#planbox .close").click(function(){
            $("#planbox").hide();
            $('body').css("overflow","auto");
          })
          $("#planbox #planselected").click(function(){
            $("#planbox").hide();
            $('body').css("overflow","auto");
            var checkedval = $("#planbox").find("input[type=radio]:checked");
            $('input[name="'+$("#planbox").data("planfield")+'"]').val(checkedval.data("planname"));
            $('input[name="'+$("#planbox").data("planid")+'"]').val(checkedval.data("planid"))
          });
          
          $("#planbox").on("click",".accordion",function(evt){
              evt.preventDefault();
              $(this).find('input[type=radio]').prop("checked",true);
              $(this).parent().next(".panel").slideToggle();
               $(this).next(".panel").slideToggle();
              $(this).toggleClass("active");
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
 if (! $GLOBALS['simplified_demographics']) {

	  $insurance_headings = array(xl("Primary Insurance Provider"), xl("Secondary Insurance Provider"), xl("Tertiary Insurance provider"));
	  $insurance_info = array();
	  $insurance_info[1] = getInsuranceData($pid,"primary");
	  $insurance_info[2] = getInsuranceData($pid,"secondary");
	  $insurance_info[3] = getInsuranceData($pid,"tertiary");

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
                            <input type="hidden" id="hidden<?php echo $i; ?>" name="i<?php echo $i?>provider" class="hiddenfiled" value="<?php echo $saveProvId; ?>"/>
                            <input type="hidden" id="planid<?php echo $i; ?>" name="i<?php echo $i?>plan_id" class="hiddenfiled1" value=""/>
                        </div>
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
            <button class="btn btn-primary" id="planselected">OK</button>
        </footer>
    </div>
</div>
</body>

</html>
