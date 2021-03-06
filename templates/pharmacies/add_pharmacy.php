<?php

//SANITIZE ALL ESCAPES
$sanitize_all_escapes=true;
//

//STOP FAKE REGISTER GLOBALS
$fake_register_globals=false;
//

$pharmaid = $_REQUEST['pharmaid'];
include_once("../../interface/globals.php");
include_once("$srcdir/transactions.inc");
include_once("$srcdir/pharmacy_data_lib.php");
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
    var pharmaid = <?php echo $_REQUEST['pharmaid']; ?> ;
    $("#pharmacydata_div").load("/templates/pharmacies/pharmacy_data_list.php?pharmaid="+pharmaid,function(){
             $('#dvLoading').hide();
    });
        
</script>
</head>

<body class="body_top">
    <table>
    <tr>
        <td>
            <span class="title"><?php echo htmlspecialchars( xl('Pharmacy Preferences'), ENT_NOQUOTES); ?></span>&nbsp;</td>
        <td>
            <!-- Define CSS Buttons -->
            <a href="pharmacy_data.php?pharmaid=<?php echo $_REQUEST['pharmaid']; ?>" <?php if (!$GLOBALS['concurrent_layout']) echo "target='Main'"; ?> class="css_button" onclick="phrmacytable(event,this)">
            <span><?php echo htmlspecialchars( xl('Add'), ENT_NOQUOTES); ?></span></a>
            
        </td>
    </tr>
    </table>

    <div style='margin-left:10px' class='text'>
    <?php 
    if ($result = getPhaDatabyPid($pharmaid)) {
    ?>
        <div id='pharmacydata_div'></div>
        <div id="dvLoading"></div>
    <?php } 
    else { ?>
        <div id='pharmacydata_div' style="display: none;"></div>
        <span id="nodatamessage" class="text"><?php echo htmlspecialchars( xl('No Pharmacy Preferences Data.'), ENT_NOQUOTES); ?></span>
    <?php } ?>
    </div>
</body>
</html>