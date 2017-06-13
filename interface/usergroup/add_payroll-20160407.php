<?php

//SANITIZE ALL ESCAPES
$sanitize_all_escapes=true;
//

//STOP FAKE REGISTER GLOBALS
$fake_register_globals=false;
//

$userid = $_REQUEST['userid'];
include_once("../globals.php");
//include_once("$srcdir/transactions.inc");
include_once("$srcdir/extra_usersdata_lib.php");

?>
<html>
<head>
<?php html_header_show();?>

<link rel="stylesheet" href="<?php echo $css_header;?>" type="text/css">
<link rel="stylesheet" type="text/css" href="../../library/js/fancybox/jquery.fancybox-1.2.6.css" media="screen" />
<script type="text/javascript" src="../../library/textformat.js"></script>
<script type="text/javascript" src="../../library/dialog.js"></script>
<script type="text/javascript" src="../../library/js/jquery.1.3.2.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/jquery.js"></script>
<script type="text/javascript" src="../../library/js/common.js"></script>
<script type="text/javascript" src="../../library/js/fancybox/jquery.fancybox-1.2.6.js"></script>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
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
    
        // load transaction divs
        var userid = <?php echo $_REQUEST['userid']; ?> ;
        jQuery("#userdata_div").load("payroll_data_list.php?userid="+userid,function(){
             $('#dvLoading').hide();
        });
    
        
</script>
</head>

<body class="body_top">
    <table>
    <tr>
        <td>
            <span class="title"><?php echo htmlspecialchars( xl('Payroll'), ENT_NOQUOTES); ?></span>&nbsp;</td>
        <td>
            <input type="hidden" name="userid" value="<?php echo $userid; ?>">
            <!-- Define CSS Buttons -->
            <a href="payroll_data.php?userid=<?php echo $userid; ?>" <?php if (!$GLOBALS['concurrent_layout']) echo "target='Main'"; ?> class="css_button" onclick="top.restoreSession()">
            <span><?php echo htmlspecialchars( xl('Add'), ENT_NOQUOTES); ?></span></a>
        </td>
    </tr>
    </table>

    <div style='margin-left:10px' class='text'>
    <?php //if ($result = getTransByPid($pid)) { 
    if ($result = getPayrollDatabyUserid($userid)) {
    ?>  <div id='userdata_div'></div> 
        <div id="dvLoading"></div> <?php 
    } 
    else { ?>
        <span class="text"><?php echo htmlspecialchars( xl('No Payroll Data.'), ENT_NOQUOTES); ?></span>
    <?php } ?>
    </div>
</body>
</html>