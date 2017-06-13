<?php
//SANITIZE ALL ESCAPES
$sanitize_all_escapes=true;
//

//STOP FAKE REGISTER GLOBALS
$fake_register_globals=false;
//

require_once("../globals.php");
require_once("$srcdir/options.inc.php"); 
require_once("$srcdir/extra_usersdata_lib.php");
?>
<html>
    <head>
        <?php html_header_show();?>
        <link rel="stylesheet" href="<?php echo $css_header;?>" type="text/css">
        <link rel="stylesheet" type="text/css" href="../../library/js/fancybox/jquery.fancybox-1.2.6.css" media="screen" />
        <style type="text/css">@import url(../../library/dynarch_calendar.css);</style>
        <script type="text/javascript" src="../../library/textformat.js"></script>
        <script type="text/javascript" src="../../library/dynarch_calendar.js"></script>
        <?php include_once("{$GLOBALS['srcdir']}/dynarch_calendar_en.inc.php"); ?>
        <script type="text/javascript" src="../../library/dynarch_calendar_setup.js"></script>
        <script type="text/javascript" src="../../library/dialog.js"></script>
        <script type="text/javascript" src="../../library/js/jquery-1.6.4.min.js"></script>
        <script type="text/javascript" src="../../library/js/common.js"></script>
        <script type="text/javascript" src="../../library/js/fancybox/jquery.fancybox-1.2.6.js"></script>
    </head>

    <body class="body_top">
        <div  style='margin-top:10px'> <!-- start main content div -->
            <form name="userid_dropdown"  action="" method="POST">
                <table>
                   <tr><td>
                        <span class='bold'><?php echo htmlspecialchars( xl('Users'), ENT_NOQUOTES); ?>:</span>
                        <select id="userid" name="userid" onchange="javascript:dropdownchange();">
                            <option value="0">--Select--</option>
                            <?php 
                            $getFinEncByDOS="SELECT * from users ORDER by id";
                            $resFinEncByDOS=sqlStatement($getFinEncByDOS);
                            while($rowFinEncByDOS=sqlFetchArray($resFinEncByDOS)){ 
                                echo "<option value='".$rowFinEncByDOS['id']."'"; 
                                if($_REQUEST['userid'] == $rowFinEncByDOS['id']) echo "selected"; 
                                echo ">".$rowFinEncByDOS['lname'].' '.$rowFinEncByDOS['fname']."</option>";
                            }
                            ?>
                        </select>
                       </td>
                       <td>
                           <a href="usergroup_admin.php"class="css_button"><span><?php echo htmlspecialchars( xl('Back'), ENT_NOQUOTES); ?></span></a>
                       </td>
                   </tr>
                </table>
                        <br><br>
            </form>        
        <script type="text/javascript">

            function dropdownchange(){
                if($( "#userid option:selected" ).val() == 0){
                   //$("#pca").hide();
                }else if($( "#userid option:selected" ).val() !== 0){
                    $('#phid').val($( "#userid option:selected" ).val());
                    var userid = $( "#userid option:selected" ).val();
                    $("#pca").load("add_payroll.php?userid="+userid);
                }
               
            }

        </script>
        
     <?php 
     if(isset($_REQUEST['userid'])){
        ?>
        <script type="text/javascript">
            jQuery(document).ready(function(){
                $("#pca").load("add_payroll.php?userid="+<?php echo $_REQUEST['userid']; ?>);
            });
         </script>
     <?php 
     }
    ?>
    <div id="pca"></div>  
   
   </div> <!-- end main content div -->

   <?php if (false && $GLOBALS['athletic_team']) { ?>
   <script language='JavaScript'>
    Calendar.setup({inputField:"form_userdate1", ifFormat:"%Y-%m-%d", button:"img_userdate1"});
   </script>
   <?php } ?>

   </body>
</html>










