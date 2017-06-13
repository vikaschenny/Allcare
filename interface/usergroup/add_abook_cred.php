<?php

//SANITIZE ALL ESCAPES
$sanitize_all_escapes=true;
//

//STOP FAKE REGISTER GLOBALS
$fake_register_globals=false;
//

$abookuserid = $_REQUEST['abookuserid'];
include_once("../globals.php");
//include_once("$srcdir/transactions.inc");
include_once("$srcdir/abook_data_lib.php");

?>
<html>
<head>
<?php html_header_show();?>
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
        var abookuserid = <?php echo $abookuserid; ?> ;
        jQuery("#userdata_div").load("abook_cred_list.php?abookuserid="+abookuserid,function(){
             $('#dvLoading').hide();
        });
    
        
</script>
</head>

<body class="body_top">
    <table>
    <tr>
        <td>
            <span class="title"><?php echo htmlspecialchars( xl('Address Book Credentials'), ENT_NOQUOTES); ?></span>&nbsp;</td>
        <td>
            <input type="hidden" name="abookuserid" value="<?php echo $abookuserid; ?>">
            <!-- Define CSS Buttons -->
            <a href="abook_cred.php?abookuserid=<?php echo $abookuserid; ?> " <?php if (!$GLOBALS['concurrent_layout']) echo "target='Main'"; ?> class="css_button" onclick="addresbooktable(event,this,'Address Book Credentials')">
            <span><?php echo htmlspecialchars( xl('Add'), ENT_NOQUOTES); ?></span></a>
        </td>
    </tr>
    </table>

    <div style='margin-left:10px' class='text'>
    <?php //if ($result = getTransByPid($pid)) { 
    if ($result = getAbookCredDatabyUserid($abookuserid)) {
    ?>  <div id='userdata_div'></div> 
        <div id="dvLoading"></div> <?php 
    } 
    else { ?>
        <div id='userdata_div' style="display: none"></div>
        <span class="text"><?php echo htmlspecialchars( xl('No Address Book Credentials Data.'), ENT_NOQUOTES); ?></span>
    <?php } ?>
    </div>
</body>
</html>