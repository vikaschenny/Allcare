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
$patient  =$_REQUEST['form_patient'];
$provider =$_REQUEST['provider'];
$grp      =$_REQUEST['group'];

//for refer login 
$refer=$_REQUEST['refer'];
$_SESSION['refer']=$refer;
?>
<html>
<head>
<?php html_header_show();?>
<script type="text/javascript">
    function group_selected(provider1,pid1){
         var chartgroupshidden = $('#chartgroups option:selected').val()+$('#chartgroups option:selected').text();
         $('#chartgroupshidden').val(chartgroupshidden);
         document.getElementById('data').style.display = 'block';
         $('#dvLoading1').show();
         $.ajax({
            url: 'chartoutput/chart_output_list.php',
            type: 'POST',
            data:  { group_name:chartgroupshidden,provider:provider1 ,pid:pid1 ,refer:'<?php echo $refer; ?>'},
            success: function(content)
            {
                  $("#data").html(content);
                  $('#dvLoading1').hide();
            }  
        });
        
     }   
     $(document).ready(function(){

        $("#chart_view").click( function() {
            toggle( $(this), "#chartoutput_div" );
        });
        
     });
    
        var group =$('#chartgroups option:selected').val()+$('#chartgroups option:selected').text();
         document.getElementById('data').style.display = 'block';
        if($('#chartgroups option:selected').val()!=''){
            $('#dvLoading1').show();    
            $("#data").load("chartoutput/chart_output_list.php?group_name="+group +'&provider='+'<?php echo $provider; ?>' +'&pid='+'<?php echo $patient; ?>'+'&refer='+'<?php echo $refer; ?>' ,function(){
            $('#dvLoading1').hide();
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
                <td></td>
                <td>
                    <a class="btn icon-btn btn-info various" data-title="Add Chart" data-frameheight="420" data-modalsize='modal-lg' data-bodypadding='0' data-href='chartoutput/chart_output.php?pid=<?php echo  $patient ; ?>&location=provider_portal&provider=<?php echo $provider; ?>&refer=<?php echo $refer; ?>' data-toggle='modal' data-target='#modalwindow'><span class="glyphicon btn-glyphicon glyphicon-plus img-circle text-info"></span><span style="font-weight:bold">Add Chart</span></a>
                    <br><br>
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
                        <?php 
                        $post_grp=substr($_REQUEST['group'],1);
                        while ($groups2 = sqlFetchArray($groups)) {
                            $gval=substr($groups2['group_name'],0,1);
                            echo "<option value='$gval'";
                            if(substr($groups2['group_name'],1)==$post_grp) { echo "selected"; }
                            echo ">".substr($groups2['group_name'],1)."</option>";
                        }
                            
                        ?>
                    </select>
                    <input type="hidden" id ="chartgroupshidden" name = 'chartgroupshidden'/>
                    <br><br>
                    <div id="data"   ><div id="dvLoading1" style="display:none; "></div></div>
                </div>    

     <?php 
     }
 ?>           

</body>
</html>  