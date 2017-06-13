<?php

//SANITIZE ALL ESCAPES
$sanitize_all_escapes=true;
//

//STOP FAKE REGISTER GLOBALS
$fake_register_globals=false;
//

include_once("../globals.php");
include_once("$srcdir/f2f_lib.php");
$grp=$_REQUEST['group'];
$encounter=$_REQUEST['enc']; 
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
        $('#dvLoading').show();
        var group =$('#chartgroups option:selected').val()+$('#chartgroups option:selected').text();
        
        if(group!=''){
        $("#f2f_div").load("f2f_reports.php?enc="+<?php echo $encounter; ?>+"&group_name="+group ,function(){
            $('#dvLoading').hide();
        });
       }
        
         group_selected();
    });
        function group_selected(){
         if($('#chartgroups option:selected').val()!=''){
             var chartgroupshidden =$('#chartgroups option:selected').val()+$('#chartgroups option:selected').text();
            
         }else {
             var chartgroupshidden =$('#chartgroups option:selected').val();
         }
        
         $('#chartgroupshidden').val(chartgroupshidden);
         $.ajax({
            url: 'f2f_reports.php',
            type: 'POST',
            data:  { group_name:chartgroupshidden,pid:<?php echo $pid; ?>,enc:<?php echo $encounter; ?> },
            success: function(content)
            {
                
                $("#data").html(content);
            }  
        });

     }  
</script>
</head>

<body class="body_top">
    <table>
    <tr>
        <td>
            <span class="title"><?php echo htmlspecialchars( xl('Face To Face'), ENT_NOQUOTES); ?></span>&nbsp;</td>
        <td>
            <!-- Define CSS Buttons -->
            <a href="f2f_form.php?&mode1=add&pid=<?php echo $pid; ?>&enc=<?php echo $encounter; ?>" <?php if (!$GLOBALS['concurrent_layout']) echo "target='Main'"; ?> class="css_button" onclick="top.restoreSession()">
            <span><?php echo htmlspecialchars( xl('Add'), ENT_NOQUOTES); ?></span></a>
            <a href="f2f_encounters_report.php"class="css_button"><span><?php echo htmlspecialchars( xl('Back'), ENT_NOQUOTES); ?></span></a>
            <!--<a href="javascript:;" class="button" onclick="window.open( 'http://<?php echo $_SERVER[HTTP_HOST]; ?>/interface/reports/f2f_form.php?mode1=add', '', 'width=500, height=600')"> Details </a>-->
            
        </td>
    </tr>
    </table>

<!--    <div style='margin-left:10px' class='text'>
    <?php 
    if ($result = getF2FByPid($pid)) {
    ?>
        <div id='f2f_div'></div>
    <?php }
    else { ?>
        <span class="text"><?php echo htmlspecialchars( xl('There are no Face_to_face on file for this patient.'), ENT_NOQUOTES); ?></span>
    <?php } ?>
    </div>-->
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
             $gval=substr($groups2['group_name'],0,1);
             echo "<option value='$gval'";
                if($groups2['group_name']==$_REQUEST['group']) { echo "selected"; }
		echo ">".substr($groups2['group_name'],1) . "</option>";
        }
        
        ?>
    </select>
    
    <input type="hidden" id ="chartgroupshidden" name = 'chartgroupshidden'/>
    <br><br>
    <div id="data"><div id="dvLoading" style="display:none; "></div>
    
    </div>
</body>
</html>