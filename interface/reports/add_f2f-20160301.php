<?php

//SANITIZE ALL ESCAPES
$sanitize_all_escapes=true;
//

//STOP FAKE REGISTER GLOBALS
$fake_register_globals=false;
//

include_once("../globals.php");
include_once("$srcdir/f2f_lib.php");
$form_id = $_REQUEST['form_id'];
$patient_id = $_REQUEST['pid'];
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
        //$("#f2f_div").load("f2f_reports.php");
        // load transaction divs
        var form_id = <?php echo $form_id; ?> ;
        var pid = <?php echo $patient_id ; ?> ;
        jQuery("#f2f_div").load("f2f_reports.php?form_id="+form_id+"&pid="+pid,function(){
             
        });
    
    });
        
</script>
</head>
<?php  $encounter=$_GLOBALS['encounter']; ?>
<body class="body_top">
    <table>
    <tr>
        <td>
            <span class="title"><?php echo htmlspecialchars( xl('Face To Face'), ENT_NOQUOTES); ?></span>&nbsp;</td>
        <td>
            <!-- Define CSS Buttons -->
            <a href="f2f_form.php?form_id=<?php echo $form_id; ?>&pid=<?php echo $patient_id; ?>&mode1=add" <?php if (!$GLOBALS['concurrent_layout']) echo "target='Main'"; ?> class="css_button" onclick="top.restoreSession()">
            <span><?php echo htmlspecialchars( xl('Add'), ENT_NOQUOTES); ?></span></a>
            <a href="f2f_encounters_report.php"class="css_button"><span><?php echo htmlspecialchars( xl('Back'), ENT_NOQUOTES); ?></span></a>
            <!--<a href="javascript:;" class="button" onclick="window.open( 'http://<?php echo $_SERVER[HTTP_HOST]; ?>/interface/reports/f2f_form.php?mode1=add', '', 'width=500, height=600')"> Details </a>-->
            
        </td>
    </tr>
    </table>

    <div style='margin-left:10px' class='text'>
    <?php 
    if ($result = getF2FByPid($pid)) {
    ?>
        <div id='f2f_div'></div>
    <?php }
    else { ?>
        <span class="text"><?php echo htmlspecialchars( xl('There are no Face_to_face on file for this patient.'), ENT_NOQUOTES); ?></span>
    <?php } ?>
    </div>
</body>
</html>