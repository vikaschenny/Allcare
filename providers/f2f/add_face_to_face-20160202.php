<?php
 // Copyright (C) 2011 Cassian LUP <cassi.lup@gmail.com>
 // 
 // Moved out of individual get_* portal functions for re-use by
 // Kevin Yeh (kevin.y@integralemr.com) May 2013
 //
 // This program is free software; you can redistribute it and/or
 // modify it under the terms of the GNU General Public License
 // as published by the Free Software Foundation; either version 2
 // of the License, or (at your option) any later version.
 // 
 // 
 
    // All of the common intialization steps for the get_* patient portal functions are now in this single include.

    //SANITIZE ALL ESCAPES
    $sanitize_all_escapes=true;

    //STOP FAKE REGISTER GLOBALS
    $fake_register_globals=false;

    //continue session
    session_start();

    //landing page definition -- where to go if something goes wrong
    $landingpage = "index.php?site=".$_SESSION['site_id'];	
    //

    // kick out if patient not authenticated
    //if ( isset($_SESSION['uid']) && isset($_SESSION['patient_portal_onsite']) ) {
    if ( isset($_SESSION['portal_username']) ) {    
    $provider = $_SESSION['portal_username'];
    }
    else {
            session_destroy();
    header('Location: '.$landingpage.'&w');
            exit;
    }
    //

    $ignoreAuth=true; // ignore the standard authentication for a regular OpenEMR user
    include_once('../../interface/globals.php');
    include_once("f2f_lib.php");

$pid1=$_REQUEST['form_patient'] ? $_REQUEST['form_patient'] :$pid;
?>
<html>
<head>
<?php html_header_show();?>
<!--
<link rel="stylesheet" href="<?php echo $css_header;?>" type="text/css">-->
<link rel="stylesheet" type="text/css" href="../../../library/js/fancybox/jquery.fancybox-1.2.6.css" media="screen" />
<script type="text/javascript" src="../../../library/textformat.js"></script>
<script type="text/javascript" src="../../../library/dialog.js"></script>
<script type="text/javascript" src="../../../library/js/jquery.1.3.2.js"></script>
<script type="text/javascript" src="../../../library/js/common.js"></script>
<script type="text/javascript" src="../../../library/js/fancybox/jquery.fancybox-1.2.6.js"></script>

<script type="text/javascript">
    function toggle( target, div ) {
        $mode = $(target).find(".indicator").text();
        if ( $mode == "collapse" ) {
            $(target).find(".indicator").text( "expand" );
            $(div).hide();
        } else {
            $(target).find(".indicator").text( "collapse" );
            $(div).show();
        }
    }

    $(document).ready(function(){

       $("#f2f_view").click( function() {
            toggle( $(this), "#f2f_div" );
        });

         // alert('<?php echo $provider; ?>'+'<?php echo $patient; ?>');
          $('#dvLoading').show();

           $("#f2f_div").load("f2f/f2f_list.php?provider=<?php echo $provider; ?>&pid=<?php echo $pid1; ?>&location=provider_portal",function(){ 
                    $('#dvLoading').hide();
                });
                
    });
        
</script>
</head>

<body class="body_top">
<?php if(isset($_REQUEST['form_patient'])){
         //echo "nothing"; echo $provider=$_REQUEST['provider']; echo $_REQUEST['form_patient'];  ?>
          <table>
            <tr>
                <td>
                    <span style="font-family: sans-serif; font-size: 12pt; font-weight: bold; text-decoration: none;"><?php echo htmlspecialchars( xl('Face To Face'), ENT_NOQUOTES); ?></span>&nbsp;</td>
                <td>
                 
                    <a class='various' data-fancybox-type='iframe' href='f2f/face_to_face.php?patient_id=<?php echo  $pid1 ; ?>&location=provider_portal&provider=<?php echo $provider; ?>' style="background-color:#49C1DC;
                        margin-top: 20px; color: #fff; border-radius:20px; font: bold 10px arial, sans-serif;  transition: all 0.3s ease-in;  padding: 8px 10px;   border: 2px solid #fff;"><span><?php echo htmlspecialchars( xl('Add'), ENT_NOQUOTES); ?></span></a><br><br>
                </td>
            </tr>
            </table>
           <div>
            <?php 
            if ($result = getF2FByPid($pid1)) {
            ?>
                <div id='f2f_div'><div id="dvLoading" style="display:none; overflow:none;"></div></div>
            <?php }
            else { ?>
                <span class="text"><?php echo htmlspecialchars( xl('There are no Face_to_face on file for this patient.'), ENT_NOQUOTES); ?></span>
            <?php } ?>
            </div>

     <?php 
     }
 ?>              
    
</body>
</html>