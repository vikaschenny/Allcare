<?php
//SANITIZE ALL ESCAPES
$sanitize_all_escapes=true;
//

//STOP FAKE REGISTER GLOBALS
$fake_register_globals=false;
//

require_once("../../interface/globals.php");
require_once("$srcdir/options.inc.php"); 
require_once("$srcdir/pharmacy.inc.php");
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
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    </head>

    <body class="body_top">
        <div  style='margin-top:10px'> <!-- start main content div -->
            <form name="pharmacy_dropdown"  action="" method="POST">
                <table>
                    <tr>
                        <td>
                             <span class='bold'><?php echo htmlspecialchars( xl('Pharmacies'), ENT_NOQUOTES); ?>:</span>
                            <select id="pharmacy" name="pharmacy" onchange="javascript:dropdownchange();">
                                <option value="0">--Select--</option>
                                <?php 
                                $getFinEncByDOS="SELECT d.id, d.name, a.line1, a.city, " .
                                        "p.area_code, p.prefix, p.number FROM pharmacies AS d " .
                                        "LEFT OUTER JOIN addresses AS a ON a.foreign_id = d.id " .
                                        "LEFT OUTER JOIN phone_numbers AS p ON p.foreign_id = d.id " .
                                        "AND p.type = 2 " .
                                        "ORDER BY name, area_code, prefix, number";
                                $resFinEncByDOS=sqlStatement($getFinEncByDOS);
                                while($rowFinEncByDOS=sqlFetchArray($resFinEncByDOS)){ 
                                    echo "<option value='".$rowFinEncByDOS['id']."'"; 
                                    if($_POST['pharmacy'] == $rowFinEncByDOS['id']) echo "selected"; 
                                    echo ">".$rowFinEncByDOS['name']."</option>";
                                }
                                ?>
                            </select>
                        </td>
                        <td>
                            <a href="../../controller.php?practice_settings&pharmacy&action=list"class="css_button"><span><?php echo htmlspecialchars( xl('Back'), ENT_NOQUOTES); ?></span></a>
                        </td>
                    </tr>
                </table>
                 <br><br>
            </form>        
        <script type="text/javascript">

            function dropdownchange(){
                if($( "#pharmacy option:selected" ).val() == 0){
                   //$("#pca").hide();
                }
               else if($( "#pharmacy option:selected" ).val() !== 0){
                    //$('#phid').val($( "#pharmacy option:selected" ).val());
                    var phid = $( "#pharmacy option:selected" ).val();
                    //alert(phid);
                    $('#dvLoading').show();
                    $("#pca").load("pharmacies_view_1to1.php?phid="+phid,function(){ 
             $('#dvLoading').hide();
                });
            }
         }

        </script>
     
    <div id="pca"><div id="dvLoading" style="display:none"></div></div>  
   </div> <!-- end main content div -->
   <?php if (false && $GLOBALS['athletic_team']) { ?>
   <script language='JavaScript'>
    Calendar.setup({inputField:"form_userdate1", ifFormat:"%Y-%m-%d", button:"img_userdate1"});
   </script>
   <?php } ?>

   </body>
</html>










