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
            <form name="user_dropdown"  action="" method="POST">
                <table>
                    <tr>
                        <td>
                            <span class='bold'><?php echo htmlspecialchars( xl('Users'), ENT_NOQUOTES); ?>:</span>
                            <select id="users" name="users" onchange="javascript:dropdownchange();">
                                <option value="0">--Select--</option>
                                <?php 
                                $getUsers="SELECT id,CONCAT( fname, lname ) AS name FROM users WHERE fname <>  '' AND lname <>  ''";
                                $resUsers=sqlStatement($getUsers);
                                while($rowUsers=sqlFetchArray($resUsers)){ 
                                    echo "<option value='".$rowUsers['id']."'"; 
                                    if($_POST['users'] == $rowUsers['id']) echo "selected"; 
                                    echo ">".$rowUsers['name']."</option>";
                                }
                                ?>
                            </select>
                        </td>
                        <td>
                            <td><a href="usergroup_admin.php" class="css_button"><span><?php echo htmlspecialchars( xl('Back'), ENT_NOQUOTES); ?></span></a>
                        </td>
                    </tr>
                </table>
                  <br><br>
            </form>        
        <script type="text/javascript">

            function dropdownchange(){
                if($( "#users option:selected" ).val() == 0){
                   //$("#pca").hide();
                }
               else if($( "#users option:selected" ).val() !== 0){
                    //$('#phid').val($( "#pharmacy option:selected" ).val());
                    var uid = $( "#users option:selected" ).val();
                    //alert(phid);
                    $('#dvLoading').show();
                    $("#uca").load("users_view_1to1.php?uid="+uid,function(){ 
                    $('#dvLoading').hide();
                });
            }
         }


        </script>
    <div id="uca"><div id="dvLoading" style="display:none"></div></div>  
   </div> <!-- end main content div -->

   <?php if (false && $GLOBALS['athletic_team']) { ?>
   <script language='JavaScript'>
    Calendar.setup({inputField:"form_userdate1", ifFormat:"%Y-%m-%d", button:"img_userdate1"});
   </script>
   <?php } ?>

   </body>
</html>










