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
    include_once("chartoutput_lib.php");

$group_name =  $_REQUEST['group_name'] ; 
$group_name2 = substr($group_name,0, 1);
//print_r($_GET);
//print_r($_POST);
//print_r($_SESSION);
$provider=$_REQUEST['provider'];
$pid=$_REQUEST['pid'] ? $_REQUEST['pid'] :$pid;
?>

<html>
<head>
<?php html_header_show();?>
 <?php if($provider==''){ ?>
        <link rel="stylesheet" href="<?php echo $css_header;?>" type="text/css">
 <?php }?>
<script language="javascript">
// Called by the deleteme.php window on a successful delete.
function imdeleted() {
 top.restoreSession();
 location.href = 'add_chartoutput.php';
}

function win1(url){
     // alert(url);
    window.open(url,'popup','width=900,height=900,scrollbars=no,resizable=yes');
}
</script>
<link rel="stylesheet" href="css/dataTables.bootstrap.css"/>
<link rel="stylesheet" href="css/datatables.responsive_bootstrap.css"/>
<script src="js/responsive_datatable/jquery.min.js"></script>
<script src="js/responsive_datatable/jquery.dataTables.min.js"></script>
 <script src="js/responsive_datatable/dataTables.bootstrap.js"></script>
<script src="js/responsive_datatable/datatables.responsive.js"></script>

</head>
<body class="body_top">

<br>
<?php if($provider!=''){ 
    $field_id=array();
 $title=array();
 $fres = sqlStatement("SELECT * FROM layout_options " .
    "WHERE form_id = 'CHARTOUTPUT' AND uor > 0 AND field_id != ''  AND group_name = '$group_name'" .
    "ORDER BY group_name, seq");
  while ($frow = sqlFetchArray($fres)) {
    $data_type = $frow['data_type'];
    $field_id[]  = $frow['field_id'];
    $value = $_POST["form_$field_id"];
    $title[] = $frow['title'];
    $sets .=  add_escape_custom($field_id);
      
    }?>
<table  id='chart_data'cellpadding='0' cellspacing='0' border='0' class='table table-bordered table-striped'>
    <thead>
        <tr><th data-class='expand'>Print</th><th>Edit</th><th>Delete</th>
            <?php  foreach($title as $label) { ?>
                     <th data-hide='phone' data-name='<?php echo $label ; ?>'><?php echo $label; ?></th>
            <?php }?>
        </tr>
    </thead>
    <?php if ($result = getChartOutputByPid($pid, $group_name)) {
       
	foreach ($result as $iter) {
            if (getdate() == strtotime($iter{"from_dos"})) {
			$date_string = "Today, " . date( "D F dS" ,strtotime($iter{"from_dos"}));
		} else {
			$date_string = date( "D F dS" ,strtotime($iter{"from_dos"}));
		}
                
                 if (getdate() == strtotime($iter{"to_dos"})) {
			$date_string = "Today, " . date( "D F dS" ,strtotime($iter{"to_dos"}));
		} else {
			$date_string1 = date( "D F dS" ,strtotime($iter{"to_dos"}));
		}
                 $id=$iter['id'];
   ?>
            <tr>
                <td>
                   <?php echo "<a href='javascript:;' onclick=win1('chartoutput/print_charts.php?coid=$id&pid=$pid&group=$group_name') class='welcome-btn1' >               
                              <span>".htmlspecialchars( xl('Print'), ENT_NOQUOTES)."</span></a>"; ?>
                </td>
                <td>
                    <?php   
//                         echo "<a href='javascript:;' onclick=win1('../interface/patient_file/summary/chart_output.php?coid=".htmlspecialchars( $iter{"id"}, ENT_NOQUOTES).
//                                "&inmode=edit&group_name2=$group_name2&group_name=$group_name&location=provider_portal&provider=$provider&pid=$pid') class='welcome-btn1' >               
//                              <span>".htmlspecialchars( xl('Edit'), ENT_NOQUOTES)."</span></a>"; 
                    
                           echo "<a class='various' data-fancybox-type='iframe' href='chartoutput/chart_output.php?coid=".htmlspecialchars( $iter{"id"}, ENT_NOQUOTES).
                                "&inmode=edit&group_name2=$group_name2&group_name=$group_name&location=provider_portal&provider=$provider&pid=$pid' style='background-color:#49C1DC;
                              margin-top: 20px; color: #fff; border-radius:20px; font: bold 10px arial, sans-serif;  transition: all 0.3s ease-in;  padding: 8px 10px;   border: 2px solid #fff;'>".htmlspecialchars( xl('Edit'), ENT_NOQUOTES)."</a>";
                    ?>
                </td>
                <td>
                    <?php //if (acl_check('admin', 'super')) {
                               
//                                echo "<a href='javascript:;' onclick=win1('../interface/patient_file/deleter.php?coid=".
//                                        htmlspecialchars( $iter{"id"}, ENT_QUOTES).
//                                        "&patient_id=$pid') class='welcome-btn1' >               
//                              <span>".htmlspecialchars( xl('Delete'), ENT_NOQUOTES)."</span></a>";         
                                        
                                echo "<a class='various' data-fancybox-type='iframe' href='chartoutput/deleter.php?coid=".
                                        htmlspecialchars( $iter{"id"}, ENT_QUOTES).
                                        "&patient_id=$pid&location=provider_portal&provider=$provider&trans=medrec' style='background-color:#49C1DC;
                                     margin-top: 20px; color: #fff; border-radius:20px; font: bold 10px arial, sans-serif;  transition: all 0.3s ease-in;  padding: 8px 10px;   border: 2px solid #fff;'>".htmlspecialchars( xl('Delete'), ENT_NOQUOTES)."</a>";
//                        }
//                        else {
                                //echo "not authorized";
                        //}
                        ?>
                </td>
                <?php  foreach($field_id as $attr)
                        { 
                         echo " <td>" .
                        htmlspecialchars( ($iter{$attr}), ENT_NOQUOTES). "&nbsp;</td>";
                        }
                        echo "</tr>\n"; ?>
            </tr>
    
    
    <?php } 
    
        } ?>
</table>



<?php } ?>

<script type='text/javascript'>
                 
			$(document).ready(function() {
                           
                          
   
                             //datatable
                             var responsiveHelper;
                            var breakpointDefinition = {
                                tablet: 1024,
                                phone : 480
                            };
                            var tableElement = $('#chart_data');
                            tableElement.dataTable({
                                iDisplayLength: 100,
                                autoWidth        : false,
                                preDrawCallback: function () {
                                    // Initialize the responsive datatables helper once.
                                    if (!responsiveHelper) {
                                        responsiveHelper = new ResponsiveDatatablesHelper(tableElement, breakpointDefinition);
                                    }
                                },
                                rowCallback    : function (nRow) {
                                    responsiveHelper.createExpandIcon(nRow);
                                },
                                drawCallback   : function (oSettings) {
                                    responsiveHelper.respond();
                                }
                            });
                             
                     });      
                        
                        
                    
                        
		</script>
</body>  
</html>