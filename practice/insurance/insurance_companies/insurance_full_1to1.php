<?php

//STOP FAKE REGISTER GLOBALS
$fake_register_globals=false;
//continue session
session_start();

$landingpage = "../../../index.php?site=".$_SESSION['site_id'];

if ( isset($_SESSION['portal_username']) ) {    
    $portal_user = $_SESSION['portal_username']; 
}else {
    session_destroy();
    header('Location: '.$landingpage.'&w');
    exit;
}


$ignoreAuth=true; // ignore the standard authentication for a regular OpenEMR user
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.

require_once("../../../interface/globals.php");
require_once("$srcdir/acl.inc");
require_once("$srcdir/options.inc.php");
require_once("$srcdir/formatting.inc.php");
require_once("$srcdir/erx_javascript.inc.php");
require_once("$srcdir/insurance.inc.php");

$insid = $_GET['uniqueid'];


 // Session pid must be right or bad things can happen when demographics are saved!
// $insid=$_REQUEST['insid'];
 //echo $insid;
/*$id=$_REQUEST['id'];
echo $id;*/
$result  = getInsurance($insid);
 //$result2 = getEmployerData($pid);


$CPR = 4; // cells per row

// $statii = array('married','single','divorced','widowed','separated','domestic partner');
// $langi = getLanguages();
// $ethnoraciali = getEthnoRacials();
// $provideri = getProviderInfo();



$fres = sqlStatement("SELECT * FROM layout_options " .
  "WHERE form_id = 'INSCA' AND uor > 0 " .
  "ORDER BY group_name, seq");
?>
<html>
<head>
<?php //html_header_show();?>

<link rel="stylesheet" href="<?php echo $css_header; ?>" type="text/css">

<style type="text/css">@import url(../../../../library/dynarch_calendar.css);</style>

<script type="text/javascript" src="../../../../library/dialog.js"></script>
<script type="text/javascript" src="../../../../library/textformat.js"></script>
<script type="text/javascript" src="../../../../library/dynarch_calendar.js"></script>
<?php include_once("{$GLOBALS['srcdir']}/dynarch_calendar_en.inc.php"); ?>
<script type="text/javascript" src="../../../../library/dynarch_calendar_setup.js"></script>

<script type="text/javascript" src="../../../../library/js/jquery.1.3.2.js"></script>
<script type="text/javascript" src="../../../../library/js/common.js"></script>

<script type="text/javascript" src="../../../../library/js/fancybox/jquery.fancybox-1.2.6.js"></script>
<link rel="stylesheet" type="text/css" href="../../../../library/js/fancybox/jquery.fancybox-1.2.6.css" media="screen" />


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
        $("#cancelbtn").click(function(event){
            event.preventDefault();
            event.stopPropagation();
            window.parent.hidepopover();
        })
   
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


function popUp(URL) {
 day = new Date();
 id = day.getTime();
 top.restoreSession();
 eval("page" + id + " = window.open(URL, '" + id + "', 'toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=1,width=400,height=300,left = 440,top = 362');");
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
<?php generate_layout_validation('INSCA'); ?>

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

function submitme() {
 var f = document.forms[0];
 document.getElementById("formid").value = $(parent.document.getElementById("inscop")).attr("data-formid");
 if (validate(f)) {
  //f.submit(); 
    $.ajax({url:"insurance_save_1to1.php",data:$('form').serializeArray(),type:"post",success: function (data, textStatus, jqXHR) {                 
        },error: function (jqXHR, textStatus, errorThrown) {

        }
    });
  }
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
</script>
<style>
    body{
        overflow:auto;
        overflow-x:hidden;
        margin: 8px 0px 8px 3px !important;
    }
    .tabContainer{
        width: 100% !important;
        overflow-y: auto !important;
    }
    div.tabContainer div.tab{
        box-sizing: border-box !important;
    }
     
</style>

</head>

<body class="body_top">
<form action='insurance_save_1to1.php' name='insurance_form' method='post' onsubmit='return validate(this)'>
    <input type="hidden" name="hid_uniqueid" id="hid_uniqueid" value="<?php echo $_GET['uniqueid']; ?>">
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
   <span class="text"><b> <?php xl("Insurance Companies Custom Attributes", "e" )?></b></span>
</div>

<div id="insurance_custom_attr" >

	<ul class="tabNav">
	   <?php display_layout_tabs('INSCA', $result, $result2); ?>
	</ul>

	<div class="tabContainer"style="height: 300px;background:#FFF; overflow-y: scroll; width: 80%;">
		<?php display_layout_tabs_data_editable('INSCA', $result, $result2); ?>
	</div>
</div>
<br>
<input type="hidden" name="formid" id="formid" value="<?php echo $_GET['formid']; ?>"/>
 </form>

<br>

<script language="JavaScript">
<?php echo $date_init; ?>
</script>

<!-- include support for the list-add selectbox feature -->
<?php include $GLOBALS['fileroot']."/library/options_listadd.inc"; ?>

</body>

</html>
