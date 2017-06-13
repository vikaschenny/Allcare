<?php  
include_once("../../globals.php");
include_once("$srcdir/chartoutput_lib.php");
?>
<div style='margin-left:10px' class='text'>
    <?php 
    if ($result = getChartOutputByPid($_REQUEST['pid'], $_REQUEST['group_name'])) {
    ?>
        <div id='chartoutput_div'></div>
    <?php }
    else { ?>
        <span class="text"><?php echo htmlspecialchars( xl('There is no data for respective group to this patient.'), ENT_NOQUOTES); ?></span>
    <?php } ?>
</div>