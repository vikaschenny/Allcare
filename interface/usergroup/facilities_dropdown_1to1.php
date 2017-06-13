<?php
//SANITIZE ALL ESCAPES
$sanitize_all_escapes=true;
//

//STOP FAKE REGISTER GLOBALS
$fake_register_globals=false;
//

require_once("../../interface/globals.php");
require_once("$srcdir/options.inc.php"); 
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
    </head>

    <body class="body_top">
        <div  style='margin-top:10px'> <!-- start main content div -->
            <form name="facility_dropdown"  action="" method="POST">
                <table>
                    <tr>
                        <td>
                            <span class='bold'><?php echo htmlspecialchars( xl('Facilities'), ENT_NOQUOTES); ?>:</span>
                            <select id="facilities" name="facilities" onchange="javascript:dropdownchange();">
                                <option value="0">--Select--</option>
                                <?php 
                                $getFacilities="SELECT id,name FROM facility";
                                $resFacilities=sqlStatement($getFacilities);
                                while($rowFacilities=sqlFetchArray($resFacilities)){ 
                                    echo "<option value='".$rowFacilities['id']."'"; 
                                    if($_POST['facilities'] == $rowFacilities['id']) echo "selected"; 
                                    echo ">".$rowFacilities['name']."</option>";
                                }
                                ?>
                            </select>
                        </td>
                        <td>
                            <td><a href="facilities.php"class="css_button"><span><?php echo htmlspecialchars( xl('Back'), ENT_NOQUOTES); ?></span></a>
                        </td>
                    </tr>
                </table>
                  <br><br>
            </form>        
        <script type="text/javascript">

            function dropdownchange(){
                if($( "#facilities option:selected" ).val() == 0){
                   //$("#pca").hide();
                }
               else if($( "#facilities option:selected" ).val() !== 0){
                    //$('#phid').val($( "#pharmacy option:selected" ).val());
                    var facilityid = $( "#facilities option:selected" ).val();
                    //alert(phid);
                    $('#dvLoading').show();
                    $("#facilityca").load("facilities_view_1to1.php?facilityid="+facilityid,function(){ 
                    $('#dvLoading').hide();
                });
            }
         }


        </script>
    <div id="facilityca"><div id="dvLoading" style="display:none"></div></div>  
   </div> <!-- end main content div -->

   <?php if (false && $GLOBALS['athletic_team']) { ?>
   <script language='JavaScript'>
    Calendar.setup({inputField:"form_userdate1", ifFormat:"%Y-%m-%d", button:"img_userdate1"});
   </script>
   <?php } ?>

   </body>
</html>










