<?php

//SANITIZE ALL ESCAPES
$sanitize_all_escapes=true;
//

//STOP FAKE REGISTER GLOBALS
$fake_register_globals=false;
//

include_once("../../globals.php");
include_once("$srcdir/chartoutput_lib.php");
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

        $("#chart_view").click( function() {
            toggle( $(this), "#chartoutput_div" );
        });

        // load transaction divs
                //$("#chartoutput_div").load("chart_output_list.php?group_name=patient");
        
    });
       
     function group_selected(){
         
         var chartgroupshidden = $('#chartgroups option:selected').val()+$('#chartgroups option:selected').text();
         $('#chartgroupshidden').val(chartgroupshidden);
         $.ajax({
            url: 'chart_output_list.php',
            type: 'POST',
            data:  { group_name:chartgroupshidden },
            success: function(content)
            {
                $("#data").html(content);
            }  
        });
        $.ajax({
            url: 'add_chart_output_check.php',
            type: 'POST',
            data:  { group_name:chartgroupshidden, pid:<?php echo $pid; ?> },
            success: function(content)
            {
                $("#data2").html(content);
            }  
        });
     }  
       
</script>
</head>

<body class="body_top">
    <table>
    <tr>
        <td>
            <span class="title"><?php echo htmlspecialchars( xl('Chart'), ENT_NOQUOTES); ?></span>&nbsp;</td>
        <td>
            <!-- Define CSS Buttons -->
            <a href="chart_output.php" <?php if (!$GLOBALS['concurrent_layout']) echo "target='Main'"; ?> class="css_button" onclick="top.restoreSession()">
            <span><?php echo htmlspecialchars( xl('Add'), ENT_NOQUOTES); ?></span></a>
        </td>
    </tr>
    </table>
    <?php 
$groups = sqlStatement("SELECT DISTINCT(group_name ) as group_name FROM layout_options " .
              "WHERE form_id = 'CHARTOUTPUT' AND uor > 0 " .
              "ORDER BY group_name");
?>
<div id='chartdiv'>
    <br>
    <label>Group</label>
    <select id ="chartgroups"  onchange="javascript:group_selected();">
        <option value=""> Select </option>
        <?php 
        while ($groups2 = sqlFetchArray($groups)) {
            echo "<option value =".substr($groups2['group_name'],0,1).">".substr($groups2['group_name'],1). "</option>";
        }
        
        ?>
    </select>
    
    <input type="hidden" id ="chartgroupshidden" name = 'chartgroupshidden'/>
    <br><br>
    <div id="data"></div>
    <div id="data2"></div>
    </div>
</body>
</html>