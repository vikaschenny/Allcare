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
//for refer login 
$refer=$_REQUEST['refer'];
if($refer!=''){
    $_SESSION['refer']=$refer;
}

?>

<html>
<head>
<?php html_header_show();?>

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
    <link rel="stylesheet" href="css/version1.0/dataTables.bootstrap.min.css"/>
    <link rel="stylesheet" href="css/version1.0/responsive.bootstrap.min.css"/>
    <!--<link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/themes/redmond/jquery-ui.css" /> 
    <link rel="stylesheet" href="css/pqselect.min.css"/>-->
    <script src="js/responsive_datatable/version1.0/jquery-1.11.3.min.js"></script>
    <!--<script src="https://code.jquery.com/ui/1.10.2/jquery-ui.js"></script>
    <script src="js/pqselect.min.js"></script>-->
    <script src="js/responsive_datatable/version1.0/jquery.dataTables.min.js"></script>
    <script src="js/responsive_datatable/version1.0/dataTables.bootstrap.min.js"></script>
    <script src="js/responsive_datatable/version1.0/dataTables.responsive.min.js"></script>
    <script type='text/javascript' src='js/responsive_datatable/dataTables.tableTools.js'></script>
    <script type='text/javascript' src='js/responsive_datatable/dataTables.bootstrap.js'></script>
    <script type="text/javascript" src="assets/js/bootstrap.min.js"></script>
    <style>
        #chart_data td ul li{
           display: block;
        }
        .DTTT.btn-group{
            float: right;
            padding-left: 13px;
            position: relative;
        }
        #chart_data_length{
            float:left;
        }
        @media only screen and (max-width: 620px){
            .DTTT.btn-group{
                float: none;
                margin-bottom: 6px;
                padding-left: 40%;
                position: relative;
            }
            #chart_data_length{
                float:none;
            }
            
        }
    </style>
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
      
    }
     //for who & notes
    $labels3 = sqlStatement("SELECT * FROM layout_options " . 
     "WHERE form_id = 'F2F' AND uor > 0 AND data_type!=36 " .
     "ORDER BY group_name, seq" );
    while ($labels_d = sqlFetchArray($labels3)) {
         $field_id_f[]  = $labels_d['field_id'];
          $title_f[] = $labels_d['title'];

   }   
    ?>

<table  id='chart_data' cellpadding='0' class='table table-striped table-bordered dt-responsive nowrap'  width='100%'>
    <thead>
        <tr><th data-class='expand'>Print</th><th>Edit</th><th>Delete</th>
            <?php  foreach($title as $label) { ?>
                     <th data-hide='phone' data-name='<?php echo $label ; ?>'><?php echo $label; ?></th>
            <?php }
            echo "<th style='width:200px;'>".htmlspecialchars( xl('Who'), ENT_NOQUOTES)."&nbsp;</th>" ;
            echo "<th style='width:200px;'>".htmlspecialchars( xl('Provider'), ENT_NOQUOTES)."&nbsp;</th>" ;
            echo "<th style='width:200px;'>".htmlspecialchars( xl('Facility'), ENT_NOQUOTES)."&nbsp;</th>" ;
            echo "<th style='width:200px;'>".htmlspecialchars( xl('Pharmacy'), ENT_NOQUOTES)."&nbsp;</th>" ;
            echo "<th style='width:200px;'>".htmlspecialchars( xl('Payer'), ENT_NOQUOTES)."&nbsp;</th>" ;
            echo "<th style='width:200px;'>".htmlspecialchars( xl('Notes'), ENT_NOQUOTES)."&nbsp;</th>" ;
             ?>
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
                        echo "<a class='various' data-fancybox-type='iframe' href='chartoutput/chart_output.php?coid=".htmlspecialchars( $iter{"id"}, ENT_NOQUOTES).
                            "&inmode=edit&group_name2=$group_name2&group_name=$group_name&location=provider_portal&provider=$provider&pid=$pid&refer=$refer' style='background-color:#49C1DC;
                             margin-top: 20px; color: #fff; border-radius:20px; font: bold 10px arial, sans-serif;  transition: all 0.3s ease-in;  padding: 8px 10px;   border: 2px solid #fff;'>".htmlspecialchars( xl('Edit'), ENT_NOQUOTES)."</a>";
                    ?>
                </td>
                <td>
                    <?php 
                        echo "<a class='various' data-fancybox-type='iframe' href='chartoutput/deleter.php?coid=".
                             htmlspecialchars( $iter{"id"}, ENT_QUOTES).
                             "&patient_id=$pid&location=provider_portal&provider=$provider&refer=$refer&trans=medrec' style='background-color:#49C1DC;
                             margin-top: 20px; color: #fff; border-radius:20px; font: bold 10px arial, sans-serif;  transition: all 0.3s ease-in;  padding: 8px 10px;   border: 2px solid #fff;'>".htmlspecialchars( xl('Delete'), ENT_NOQUOTES)."</a>";
                    ?>
                </td>
                <?php  foreach($field_id as $attr)
                        { 
                         echo " <td>" .
                        htmlspecialchars( ($iter{$attr}), ENT_NOQUOTES). "&nbsp;</td>";
                        }
                        if($iter{'refer_to'}!='null' && $iter{'refer_to'}!=''){
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
                        echo "</tr>\n"; ?>
            </tr>
    
    
    <?php } 
    
        } ?>
</table>



<?php } ?>

<script type='text/javascript'>
        $(document).ready(function() {
            var tableElement = $('#chart_data');
            tableElement.dataTable({
                "iDisplayLength": 25,
             dom: 'T<\"clear\">lfrtip',
           tableTools: {
                 "sSwfPath": "../interface/swf/copy_csv_xls_pdf.swf",
                aButtons: [
                    {
                        sExtends: "xls",
                        sButtonText: "Save to Excel",
                    }       
                ]
            }
        });
    });
</script>
</body>  
</html>