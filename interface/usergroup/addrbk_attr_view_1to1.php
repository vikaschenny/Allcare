<?php
/**
 *
 * Address book summary screen.
 *
 * LICENSE: This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 3
 * of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://opensource.org/licenses/gpl-license.php>;.
 *
 * @package OpenEMR
 * @author  Brady Miller <brady@sparmy.com>
 * @link    http://www.open-emr.org
 */

//SANITIZE ALL ESCAPES
$sanitize_all_escapes=true;
//

//STOP FAKE REGISTER GLOBALS
$fake_register_globals=false;
//

 require_once("../../interface/globals.php");
 require_once("$srcdir/options.inc.php"); 
 require_once("$srcdir/addrbk_attr.inc.php");
 
  $id = $_REQUEST['addrid'];
  //echo $id;
 //$id=1;
 $result= getAddrbkAttr($id);
 ?>
<html>

<head>
<?php html_header_show();?>
<link rel="stylesheet" href="<?php echo $css_header;?>" type="text/css">
<link rel="stylesheet" type="text/css" href="../../../library/js/fancybox/jquery.fancybox-1.2.6.css" media="screen" />
<style type="text/css">@import url(../../../library/dynarch_calendar.css);</style>
<script type="text/javascript" src="../../../library/textformat.js"></script>
<script type="text/javascript" src="../../../library/dynarch_calendar.js"></script>
<?php include_once("{$GLOBALS['srcdir']}/dynarch_calendar_en.inc.php"); ?>
<script type="text/javascript" src="../../../library/dynarch_calendar_setup.js"></script>
<script type="text/javascript" src="../../../library/dialog.js"></script>
<script type="text/javascript" src="../../../library/js/jquery-1.6.4.min.js"></script>
<script type="text/javascript" src="../../../library/js/common.js"></script>
<script type="text/javascript" src="../../../library/js/fancybox/jquery.fancybox-1.2.6.js"></script>
 

<script type="text/javascript" language="JavaScript">
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

 var mypcc = '<?php echo htmlspecialchars($GLOBALS['phone_country_code'],ENT_QUOTES); ?>';

 function oldEvt(eventid) {
  dlgopen('../../main/calendar/add_edit_event.php?eid=' + eventid, '_blank', 550, 350);
 }

 function advdirconfigure() {
   dlgopen('advancedirectives.php', '_blank', 500, 450);
  }

 function refreshme() {
  top.restoreSession();
  location.reload();
 }

</script>

<script type="text/javascript">

function toggleIndicator(target,div) {

    $mode = $(target).find(".indicator").text();
    if ( $mode == "<?php echo htmlspecialchars(xl('collapse'),ENT_QUOTES); ?>" ) {
        $(target).find(".indicator").text( "<?php echo htmlspecialchars(xl('expand'),ENT_QUOTES); ?>" );
        $("#"+div).hide();
	$.post( "../../../library/ajax/user_settings.php", { target: div, mode: 0 });
    } else {
        $(target).find(".indicator").text( "<?php echo htmlspecialchars(xl('collapse'),ENT_QUOTES); ?>" );
        $("#"+div).show();
	$.post( "../../../library/ajax/user_settings.php", { target: div, mode: 1 });
    }
}
 
    $('#dvLoading').show();
    $("#addrca").load(function(){ 
    $('#dvLoading').hide();
});

</script>



</head>

<body class="body_top">
<div id="dvLoading" style="display:none"></div>   
<div  style='margin-top:10px'> <!-- start main content div -->
<div id="addrca">
<table border="0" cellspacing="0" cellpadding="0" width="100%">
  <tr>
   <td align="left" valign="top">
    <!-- start left column div -->
    <div style='float:left; margin-right:20px'>
     <table cellspacing=0 cellpadding=0>
      <tr>
       <td>
<?php
// pharmacies expand collapse widget
$widgetTitle = xl("Address Book Custom Attributes");
$widgetLabel = "Address Book";
$widgetButtonLabel = xl("Edit");
$widgetButtonLink = "addrbk_attr_full_1to1.php?addrid1=$id";
$widgetButtonClass = "";
$linkMethod = "html";
$bodyClass = "";
$widgetAuth = acl_check('patients', 'demo', '', 'write');
$fixedWidth = true;
expand_collapse_widget($widgetTitle, $widgetLabel, $widgetButtonLabel,
  $widgetButtonLink, $widgetButtonClass, $linkMethod, $bodyClass,
  $widgetAuth, $fixedWidth);
?>
         <div id="Addrbk_custom_attributes" >
          <ul class="tabNav">
           <?php display_layout_tabs('ADDRCA', $result, $result2); ?>
          </ul>
          <div class="tabContainer" style="height:200px; overflow-y:scroll; background-color:#FFFFFF;">
           <?php display_layout_tabs_data('ADDRCA', $result, $result2); ?>
          </div>
         </div>
        </div> <!-- required for expand_collapse_widget -->
       </td>
      </tr>

   </table>
</div>
  
    <!-- end left column div -->

   
  </td>

 </tr>
</table>
</div> <!-- end main content div -->
</div>
<?php if (false && $GLOBALS['athletic_team']) { ?>
<script language='JavaScript'>
 Calendar.setup({inputField:"form_userdate1", ifFormat:"%Y-%m-%d", button:"img_userdate1"});
</script>
<?php } ?>

</body>
</html>










