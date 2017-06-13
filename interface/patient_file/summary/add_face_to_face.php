<?php

//SANITIZE ALL ESCAPES
$sanitize_all_escapes=true;
//

//STOP FAKE REGISTER GLOBALS
$fake_register_globals=false;
//

include_once("../../globals.php");
include_once("$srcdir/f2f_lib.php");

$pid1=$_REQUEST['pid'] ? $_REQUEST['pid'] :$pid;
?>
<html>
<head>
<?php html_header_show();?>

<link rel="stylesheet" href="<?php echo $css_header;?>" type="text/css">
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

        // load transaction divs
        $("#f2f_div").load("f2f_list.php");
    });
        
</script>
</head>

<body class="body_top">
    <table>
    <tr>
        <td>
            <span class="title"><?php echo htmlspecialchars( xl('Face To Face'), ENT_NOQUOTES); ?></span>&nbsp;</td>
        <td>
            <!-- Define CSS Buttons -->
            <a href="face_to_face.php" <?php if (!$GLOBALS['concurrent_layout']) echo "target='Main'"; ?> class="css_button" onclick="top.restoreSession()">
            <span><?php echo htmlspecialchars( xl('Add'), ENT_NOQUOTES); ?></span></a>
        </td>
    </tr>
    </table>

    <div style='margin-left:10px' class='text'>
    <?php 
    if ($result = getF2FByPid($pid1)) {
    ?>
        <div id='f2f_div'></div>
    <?php }
    else { ?>
        <span class="text"><?php echo htmlspecialchars( xl('There are no Face_to_face on file for this patient.'), ENT_NOQUOTES); ?></span>
    <?php } ?>
    </div>
</body>
</html>