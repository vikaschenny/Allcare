<?php
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.

require_once("../../interface/globals.php");
require_once("$srcdir/acl.inc");
require_once("$srcdir/options.inc.php");
require_once("$srcdir/formatting.inc.php");
require_once("$srcdir/erx_javascript.inc.php");
require_once("$srcdir/addrbk_attr.inc.php");
 // Session pid must be right or bad things can happen when demographics are saved!
 $addrid=$_REQUEST['addrid1'];
 //echo $addrid;
/*$id=$_REQUEST['id'];
echo $id;*/
$result  =  getAddrbkAttr($addrid);
 //$result2 = getEmployerData($pid);

 

$CPR = 4; // cells per row

// $statii = array('married','single','divorced','widowed','separated','domestic partner');
// $langi = getLanguages();
// $ethnoraciali = getEthnoRacials();
// $provideri = getProviderInfo();



$fres = sqlStatement("SELECT * FROM layout_options " .
  "WHERE form_id = 'ADDRCA' AND uor > 0 " .
  "ORDER BY group_name, seq");
?>
<html>
<head>
<?php html_header_show();?>

<link rel="stylesheet" href="<?php echo $css_header; ?>" type="text/css">

<style type="text/css">@import url(../../../library/dynarch_calendar.css);</style>

<script type="text/javascript" src="../../../library/dialog.js"></script>
<script type="text/javascript" src="../../../library/textformat.js"></script>
<script type="text/javascript" src="../../../library/dynarch_calendar.js"></script>
<?php include_once("{$GLOBALS['srcdir']}/dynarch_calendar_en.inc.php"); ?>
<script type="text/javascript" src="../../../library/dynarch_calendar_setup.js"></script>

<script type="text/javascript" src="../../../library/js/jquery.1.3.2.js"></script>
<script type="text/javascript" src="../../../library/js/common.js"></script>

<script type="text/javascript" src="../../../library/js/fancybox/jquery.fancybox-1.2.6.js"></script>
<link rel="stylesheet" type="text/css" href="../../../library/js/fancybox/jquery.fancybox-1.2.6.css" media="screen" />


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
<?php generate_layout_validation('ADDRCA'); ?>

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
 if (validate(f)) {
  top.restoreSession();
  f.submit();
 }
}



</script>
</head>

<body class="body_top">
<form action='addrbk_attr_save_1to1.php' name='addrbk_form' method='post' onsubmit='return validate(this)'>
<input type='hidden' name='mode' value='save' />
<input type='hidden' name='db_addrid' value="<?php echo $addrid?>" />
<!--<input type='hidden' name='db_id' value="<?php //echo id?>" />-->
<table cellpadding='0' cellspacing='0' border='0'>
	<tr>
		<td>
			<a href="javascript:submitme();" class='css_button'>
				<span><?php xl('Save','e'); ?></span>
			</a>
		</td>
		<td>
			<a href="addrbook_attr_dropdown_1to1.php"  class='css_button' onclick="top.restoreSession()">
			<span><?php xl('Cancel','e'); ?></span>
			</a>
		</td>
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
   <span class="text"><b> <?php xl("Address Book Custom Attributes", "e" )?></b></span>
</div>

<div id="Addressbk_custom_attr" >

	<ul class="tabNav">
	   <?php display_layout_tabs('ADDRCA', $result, $result2); ?>
	</ul>

	<div class="tabContainer"style="height: 300px;background:#FFF; overflow-y: scroll; width: 80%;">
		<?php display_layout_tabs_data_editable('ADDRCA', $result, $result2); ?>
	</div>
</div>
<br>
 </form>

<br>

<script language="JavaScript">
<?php echo $date_init; ?>
</script>

<!-- include support for the list-add selectbox feature -->
<?php include $GLOBALS['fileroot']."/library/options_listadd.inc"; ?>

</body>

</html>
