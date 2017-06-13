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
<link rel="stylesheet" href="<?php echo $css_header;?>" type="text/css">
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
	foreach($title as $label)
                        { 
                        echo "<th style='width:200px;'>".htmlspecialchars( xl($label), ENT_NOQUOTES)."&nbsp;</th>" ;
                        }
                         
                        echo "<th style='width:200px;'>".htmlspecialchars( xl('Who'), ENT_NOQUOTES)."&nbsp;</th>" ;
                        echo "<th style='width:200px;'>".htmlspecialchars( xl('Provider'), ENT_NOQUOTES)."&nbsp;</th>" ;
                        echo "<th style='width:200px;'>".htmlspecialchars( xl('Facility'), ENT_NOQUOTES)."&nbsp;</th>" ;
                        echo "<th style='width:200px;'>".htmlspecialchars( xl('Pharmacy'), ENT_NOQUOTES)."&nbsp;</th>" ;
                        echo "<th style='width:200px;'>".htmlspecialchars( xl('Payer'), ENT_NOQUOTES)."&nbsp;</th>" ;
                        echo "<th style='width:200px;'>".htmlspecialchars( xl('Notes'), ENT_NOQUOTES)."&nbsp;</th>" ;
                        
                       
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
              
                
                 foreach($field_id as $attr)
                        { 
                            echo " <td style='width:200px;'>&nbsp;&nbsp;&nbsp;&nbsp;" .
                            htmlspecialchars( ($iter{$attr}), ENT_NOQUOTES). "&nbsp;</td>";
                        }
                    
                        if($iter{'refer_to'}!='' && $iter{'refer_to'}!='null'){
                            $users1=sqlStatement("select organization from users where id='".$iter{'refer_to'}."'");
                            $res2=sqlFetchArray($users1);
                            if(!empty($res2)){
                                  echo " <td style='width:200px;'>&nbsp;&nbsp;&nbsp;&nbsp;" .
                                   htmlspecialchars( ($res2{organization}), ENT_NOQUOTES). "&nbsp;</td>";
                            }else {
                                  $users=sqlStatement("select concat(lname,'',fname) as name from users where id='".$iter{'refer_to'}."'");
                                  $res=sqlFetchArray($users);
                                   echo " <td style='width:200px;'>&nbsp;&nbsp;&nbsp;&nbsp;" .
                                   htmlspecialchars( ($res{name}), ENT_NOQUOTES). "&nbsp;</td>";
                            }
                               
                        }else {
                             echo " <td style='width:200px;'>&nbsp;</td>";
                        }
                        
                        if($iter{'provider'}!='' && $iter{'provider'}!='null'){
                            $users2=sqlStatement("select concat(lname,' ',fname) as name from users where id='".$iter{'provider'}."'");
                            $res3=sqlFetchArray($users2);
                            echo " <td style='width:200px;'>&nbsp;&nbsp;&nbsp;&nbsp;" .
                            htmlspecialchars( ($res3{name}), ENT_NOQUOTES). "&nbsp;</td>";
                        }else {
                             echo " <td style='width:200px;'>&nbsp;</td>";
                        }
                        
                        if($iter{'facility'}!='' && $iter{'facility'}!='null'){
                            
                            $fac2=sqlStatement("SELECT name FROM facility where id='".$iter{'facility'}."'");
                            $res4=sqlFetchArray($fac2);
                            echo " <td style='width:200px;'>&nbsp;&nbsp;&nbsp;&nbsp;" .
                            htmlspecialchars( ($res4{name}), ENT_NOQUOTES). "&nbsp;</td>";
                        }else {
                            echo " <td style='width:200px;'>&nbsp;</td>";
                        }
                        
                        if($iter{'pharmacy'}!='' && $iter{'pharmacy'}!='null'){
                            $ph2=sqlStatement("SELECT name FROM pharmacies where id='".$iter{'pharmacy'}."'");
                            $res5=sqlFetchArray($ph2);
                            echo " <td style='width:200px;'>&nbsp;&nbsp;&nbsp;&nbsp;" .
                            htmlspecialchars( ($res5{name}), ENT_NOQUOTES). "&nbsp;</td>";
                        }else {
                            echo " <td style='width:200px;'>&nbsp;</td>";
                        }
                        if($iter{'payer'}!='' && $iter{'payer'}!='null'){
                            $pay2=sqlStatement("SELECT name FROM insurance_companies where id='".$iter{'payer'}."'");
                            $res6=sqlFetchArray($pay2);
                            echo " <td style='width:200px;'>&nbsp;&nbsp;&nbsp;&nbsp;" .
                            htmlspecialchars( ($res6{name}), ENT_NOQUOTES). "&nbsp;</td>";
                        }else {
                            echo " <td style='width:200px;'>&nbsp;</td>";
                        }
                        
                        echo " <td style='width:200px;'>&nbsp;&nbsp;&nbsp;&nbsp;" .
                            htmlspecialchars( ($iter{'notes'}), ENT_NOQUOTES). "&nbsp;</td>";
                            
                     echo "</tr>\n";
		$notes_count++;
	}
}
?>
</table>

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