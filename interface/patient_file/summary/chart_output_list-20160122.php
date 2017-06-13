<?php

//SANITIZE ALL ESCAPES
$sanitize_all_escapes=true;
//

//STOP FAKE REGISTER GLOBALS
$fake_register_globals=false;
//

include_once("../../globals.php");
//include_once("$srcdir/transactions.inc");
include_once("$srcdir/chartoutput_lib.php");
require_once("$srcdir/options.inc.php");
$group_name =  $_REQUEST['group_name']; 
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
 location.href = 'summary/add_chartoutput.php';
}

function win1(url){
     // alert(url);
    window.open(url,'popup','width=900,height=900,scrollbars=no,resizable=yes');
}
</script>
<link rel="stylesheet" href="../../../providers/css/dataTables.bootstrap.css"/>
<link rel="stylesheet" href="../../../providers/css/datatables.responsive_bootstrap.css"/>
<script src="../../../providers/js/responsive_datatable/jquery.min.js"></script>
<script src="../../../providers/js/responsive_datatable/jquery.dataTables.min.js"></script>
 <script src="../../../providers/js/responsive_datatable/dataTables.bootstrap.js"></script>
<script src="../../../providers/js/responsive_datatable/datatables.responsive.js"></script>

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
                   <?php echo "<a href='javascript:;' onclick=win1('../interface/reports/print_charts.php?coid=$id&patient_id=$pid&group=$group_name') class='welcome-btn1' >               
                              <span>".htmlspecialchars( xl('Print'), ENT_NOQUOTES)."</span></a>"; ?>
                </td>
                <td>
                    <?php   
//                         echo "<a href='javascript:;' onclick=win1('../interface/patient_file/summary/chart_output.php?coid=".htmlspecialchars( $iter{"id"}, ENT_NOQUOTES).
//                                "&inmode=edit&group_name2=$group_name2&group_name=$group_name&location=provider_portal&provider=$provider&pid=$pid') class='welcome-btn1' >               
//                              <span>".htmlspecialchars( xl('Edit'), ENT_NOQUOTES)."</span></a>"; 
                    
                           echo "<a class='various' data-fancybox-type='iframe' href='../interface/patient_file/summary/chart_output.php?coid=".htmlspecialchars( $iter{"id"}, ENT_NOQUOTES).
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
                                        
                                echo "<a class='various' data-fancybox-type='iframe' href='../interface/patient_file/deleter.php?coid=".
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



<?php }else { ?>
<table class="showborder" cellspacing="0px" cellpadding="2px">

<?php

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
      
    }


// Print Heading .. to have better Understanding of the Listed Transactions -- starts here Dec 07,09
	 print "<tr class='showborder_head'><th style='width=10px;'>&nbsp;</th><th style='width=10px;'>&nbsp;</th><th style='width=10px;'>&nbsp;</th><th style='width=10px;'>&nbsp;</th><th style='width=10px;'>&nbsp;</th>";
	/* print 
               "<th style='width:150px;'>".htmlspecialchars( xl('From dos'), ENT_NOQUOTES)."</th>" .  
               "<th style='width:100px;'>".htmlspecialchars( xl('To dos'), ENT_NOQUOTES)."</th>
             
               </tr>\n";*/
         
         
          foreach($title as $label)
                        { 
                        echo "<th style='width:200px;'>".htmlspecialchars( xl($label), ENT_NOQUOTES)."&nbsp;</th>" ;
                        }
          echo "</tr>\n";
		// Print Heading .. to have better Understanding of the Listed Transactions   -- ends here

if ($result = getChartOutputByPid($pid, $group_name)) {	
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
                
                echo "<tr height='25'><td style='width:60px;' >";
			
                      /*  echo "<a href='print_chartoutput.php?coid=".$iter['id']."".            
                               "&patient_id=$pid"."&group=$group_name'onclick='top.restoreSession()' class='css_button_small'><span>".
                        	htmlspecialchars( xl('Print1'), ENT_NOQUOTES)."</span></a>";*/
                echo "</td><td style='width:60px;'>";
			
                /*echo "<a href='print_chart.php?coid=".$iter['id']."".            
                       "&patient_id=$pid"."&group=$group_name'onclick='top.restoreSession()' class='css_button_small'><span>".
                        htmlspecialchars( xl('Print2'), ENT_NOQUOTES)."</span></a>";*/
                echo "</td><td style='width:60px;'>";
                echo "<a href='print_chart_static.php?coid=".$iter['id']."".            
                        "&patient_id=$pid"."&group=$group_name'onclick='top.restoreSession()' class='css_button_small'><span>".
                         htmlspecialchars( xl('Print'), ENT_NOQUOTES)."</span></a>";
                echo "</td><td style='width:60px;'>";
		print "<a href='chart_output.php?coid=".htmlspecialchars( $iter{"id"}, ENT_NOQUOTES).
			"&inmode=edit&group_name2=$group_name2&group_name=$group_name' onclick='top.restoreSession()' class='css_button_small'><span>".
			htmlspecialchars( xl('Edit'), ENT_NOQUOTES)."</span></a>";
		echo "</td><td>";
		if (acl_check('admin', 'super')) {
			echo "<a href='../deleter.php?coid=".
				htmlspecialchars( $iter{"id"}, ENT_QUOTES).
				"&patient_id=$pid' onclick='top.restoreSession()' class='css_button_small'><span>".
				htmlspecialchars( xl('Delete'), ENT_NOQUOTES)."</span></a>";
		}
		else {
			echo "&nbsp;";
		}
		echo "</td>";
                
		/*echo "<td style='width:100px;'>" .
			htmlspecialchars( $date_string, ENT_NOQUOTES). "&nbsp;</td><td>" .
			htmlspecialchars( ($date_string1), ENT_NOQUOTES) . "&nbsp;</td><td>
			</tr>\n";*/
                
                 foreach($field_id as $attr)
                        { 
                         echo " <td style='width:200px;'>&nbsp;&nbsp;&nbsp;&nbsp;" .
                        htmlspecialchars( ($iter{$attr}), ENT_NOQUOTES). "&nbsp;</td>";
                        }
                        echo "</tr>\n";
		$notes_count++;
	}
}
?>
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