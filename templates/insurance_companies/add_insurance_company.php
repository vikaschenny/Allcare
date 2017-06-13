<?php

//SANITIZE ALL ESCAPES
$sanitize_all_escapes=true;
//

//STOP FAKE REGISTER GLOBALS
$fake_register_globals=false;
//

include_once("../../interface/globals.php");
include_once("$srcdir/transactions.inc");
include_once("$srcdir/insurancecompanies_data_lib.php");


$insuranceid = $_REQUEST['insuranceid']; 
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

    var insuranceid = <?php echo $_REQUEST['insuranceid']; ?> ;
    $("#insurancecompanydata_div").load("/templates/insurance_companies/insurance_company_list.php?insuranceid="+insuranceid,function(){
             $('#dvLoading').hide();
        });
        
</script>

</head>

<body class="body_top">
    <table>
    <tr>
        <td>
            <span class="title"><?php echo htmlspecialchars( xl('Insurance Plans'), ENT_NOQUOTES); ?></span>&nbsp;</td>
        <td>
            <!-- Define CSS Buttons -->
            <a href="insurance_company.php?insuranceid=<?php echo $_REQUEST['insuranceid']; ?>" <?php if (!$GLOBALS['concurrent_layout']) echo "target='Main'"; ?> class="css_button" onclick="insurancetable(event,this,'Insurance Plans')">
            <span><?php echo htmlspecialchars( xl('Add'), ENT_NOQUOTES); ?></span></a>
            

            
           
           
        </td>
    </tr>
    </table>

    <div style='margin-left:10px' class='text'>
    <?php //if ($result = getTransByPid($pid)) { 
    if ($result = getInsDatabyPid($insuranceid)) {
    ?>
        <div id='insurancecompanydata_div'></div>
       <div id="dvLoading"></div> 
    <?php } 
    
    
    else { ?>
       <div id='insurancecompanydata_div' style="display: none;"></div>
        <span class="text"><?php echo htmlspecialchars( xl('No Insurance Plans Data.'), ENT_NOQUOTES); ?></span>
    <?php } ?>
    </div>
    

</body>
</html> 