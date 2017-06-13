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
include_once("$srcdir/chartoutput_lib.php");
$patient=$_REQUEST['form_patient'];
$provider=$_REQUEST['provider'];
?>
<html>
<head>
<?php html_header_show();?>

<!--<link rel="stylesheet" href="<?php echo $css_header;?>" type="text/css">-->
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
       
    function group_selected(provider1,pid1){
       
       
         var chartgroupshidden = $('#chartgroups option:selected').val()+$('#chartgroups option:selected').text();
         $('#chartgroupshidden').val(chartgroupshidden);
         document.getElementById('data').style.display = 'block';
         $('#dvLoading1').show();
         $.ajax({
            url: 'chartoutput/chart_output_list.php',
            type: 'POST',
            data:  { group_name:chartgroupshidden,provider:provider1 ,pid:pid1 },
            success: function(content)
            {
                  
                  $("#data").html(content);
                  $('#dvLoading1').hide();
            }  
        });
        $.ajax({
            url: 'chartoutput/add_chart_output_check.php?provider=provider1&pid=pid1',
            type: 'POST',
            data:  { group_name:chartgroupshidden, pid:pid1 },
            success: function(content)
            {
               // $("#data2").html(content);
            }  
        });
     }   
       
</script>
</head>

<body class="body_top">
     <?php 
     if(isset($_REQUEST['form_patient'])){

         $provider=$_REQUEST['provider'];?>
          <table>
            <tr>
                <td>
                    <span style="font-family: sans-serif; font-size: 12pt; font-weight: bold; text-decoration: none;"><?php echo htmlspecialchars( xl('Chart'), ENT_NOQUOTES); ?></span>&nbsp;</td>
                <td>

                    <a class='various' data-fancybox-type='iframe' href='chartoutput/chart_output.php?pid=<?php echo  $patient ; ?>&location=provider_portal&provider=<?php echo $provider; ?>' style="background-color:#49C1DC;
                        margin-top: 20px; color: #fff; border-radius:20px; font: bold 10px arial, sans-serif;  transition: all 0.3s ease-in;  padding: 8px 10px;   border: 2px solid #fff;"><span><?php echo htmlspecialchars( xl('Add'), ENT_NOQUOTES); ?></span></a><br><br>
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
                    <select id ="chartgroups"  onchange="javascript:group_selected('<?php echo $provider; ?>','<?php echo $patient; ?>');">
<!--                                                                    <option value=""> Select </option>-->
                        <?php 
                        while ($groups2 = sqlFetchArray($groups)) {
                            echo "<option value =".substr($groups2['group_name'],0,1).">".substr($groups2['group_name'],1). "</option>";
                        }

                        ?>
                    </select>

                    <input type="hidden" id ="chartgroupshidden" name = 'chartgroupshidden'/>
                    <br><br>
                    <div id="data"  style="overflow:scroll; display:none;" ><div id="dvLoading1" style="display:none; "></div></div>
<!--                                                                <div id="data2" ></div>-->
                </div>    

     <?php 
     }
 ?>           
 <script type="text/javascript">



$(document).ready( function () {
function group_selected(provider1,pid1){
    // var chartgroupshidden =jQuery('#chartgroups').text();

     var chartgroupshidden = $('#chartgroups option:selected').val()+$('#chartgroups option:selected').text();
     // alert(chartgroupshidden);
     $('#chartgroupshidden').val(chartgroupshidden);
     document.getElementById('data').style.display = 'block';
     $('#dvLoading1').show();
     $.ajax({
        url: 'chartoutput/chart_output_list.php',
        type: 'POST',
        data:  { group_name:chartgroupshidden,provider:provider1 ,pid:pid1 },
        success: function(content)
        {

              $("#data").html(content);
              $('#dvLoading1').hide();
        }  
    });
    $.ajax({
        url: 'chartoutput/add_chart_output_check.php?provider=provider1&pid=pid1',
        type: 'POST',
        data:  { group_name:chartgroupshidden, pid:pid1 },
        success: function(content)
        {
           // $("#data2").html(content);
        }  
    });
}  
   group_selected('<?php echo $provider; ?>','<?php echo $patient; ?>');
});      
                                            
   </script>   
</body>
</html>