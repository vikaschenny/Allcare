<?php
//SANITIZE ALL ESCAPES
$sanitize_all_escapes=true;
//

//STOP FAKE REGISTER GLOBALS
$fake_register_globals=false;
//

require_once("../globals.php");
require_once("$srcdir/options.inc.php"); 
include_once("$srcdir/abook_data_lib.php");
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
                    <tr>
                        <td>
                            <span class='bold'><?php echo htmlspecialchars( xl('Address Book'), ENT_NOQUOTES); ?>:</span>
                            <select id="abookuserid" name="abookuserid" onchange="javascript:dropdownchange();">
                                <option value="0">--Select--</option>
                                <?php 
                                $getuserdata="SELECT id,fname,lname,organization,
                                                CASE
                                                WHEN abook_type != '' THEN (
                                                            SELECT title
                                                            FROM list_options
                                                            WHERE list_id = 'abook_type'
                                                            AND option_id = abook_type
                                                            )
                                                WHEN abook_type = '' THEN ''
                                                END AS type 
                                                FROM users";
                                $resuserdata=sqlStatement($getuserdata);
                                while($rowuserdata=sqlFetchArray($resuserdata)){ 
                                    echo "<option value='".$rowuserdata['id']."'"; 
                                    if($_REQUEST['abookuserid'] == $rowuserdata['id']) echo "selected"; 
                                    echo ">Name: ".$rowuserdata['lname'].' '.$rowuserdata['fname'].", Org: ".$rowuserdata['organization'].", Type: ".$rowuserdata['type']."</option>";
                                }
                                ?>
                            </select>
                        </td>
                        <td>
                              <a href="addrbook_list.php"class="css_button"><span><?php echo htmlspecialchars( xl('Back'), ENT_NOQUOTES); ?></span></a>
                        </td>
                    </tr>
                </table>
                  <br><br>
            </form>        
        <script type="text/javascript">

            function dropdownchange(){
                if($( "#abookuserid option:selected" ).val() == 0){
                   //$("#pca").hide();
                }else if($( "#abookuserid option:selected" ).val() !== 0){
                    var abookuserid = $( "#abookuserid option:selected" ).val();
                    $("#pca").load("add_abook_cred.php?abookuserid="+abookuserid);
                }
               
            }

        </script>
        
     <?php 
     if(isset($_REQUEST['abookuserid'])){
        ?>
        <script type="text/javascript">
            jQuery(document).ready(function(){
                $("#pca").load("add_abook_cred.php?abookuserid="+<?php echo $_REQUEST['abookuserid']; ?>);
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










