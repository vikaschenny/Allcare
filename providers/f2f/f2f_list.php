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
    include_once("f2f_lib.php");



$provider=$_REQUEST['provider'];
$refer=$_REQUEST['refer'];
$_SESSION['refer']=$refer;
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
 location.href = 'add_face_to_face.php';
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
    "WHERE form_id = 'F2F' AND uor > 0 AND field_id != '' " .
    "ORDER BY group_name, seq");
  while ($frow = sqlFetchArray($fres)) {
    $data_type = $frow['data_type'];
    $field_id[]  = $frow['field_id'];
    $value = $_POST["form_$field_id"];
    $sets .=  add_escape_custom($field_id);
    $title[] = $frow['title'];
      
  }?>
<table  id='f2f_data'cellpadding='0' cellspacing='0' border='0' class='table table-bordered table-striped'style=' word-wrap: break-word ; table-layout:fixed !important;  width: 100% !important;'>
    <thead>
        <tr> <th data-class='expand'>Print</th><th>Edit</th><th>Delete</th><?php  foreach($title as $label) { ?>
               <th data-hide='phone' data-name=<?php echo $label ; ?>><?php echo $label; ?></th>
            <?php } ?>
        </tr>
    </thead>
    <?php if ($result = getF2FByPid($pid)) {	
	foreach ($result as $iter) { 
             $id=$iter['id'];
             $encounter=$iter['encounter'];
             $dos=str_replace(" ", "_",$iter['date_of_service']);?>
             <tr>
                <td><?php   print "<a href='javascript:;' onclick=win1('f2f/print_f2f.php?f2fid=$id&encounter_id=$encounter&patient_id=$pid&date_of_service=$dos') class='welcome-btn1' >            
                           <span>".htmlspecialchars( xl('Print'), ENT_NOQUOTES)."</span></a>"; ?>
                </td>
                <td><?php 
//                     print "<a href='javascript:;' onclick=win1('../interface/patient_file/summary/face_to_face.php?f2fid=".htmlspecialchars( $iter{"id"}, ENT_NOQUOTES).
//                            "&inmode=edit&patient_id=$pid&provider=$provider&location=provider_portal') class='welcome-btn1' >            
//                           <span>".htmlspecialchars( xl('Edit'), ENT_NOQUOTES)."</span></a>"; 
                      echo "<a class='various' data-fancybox-type='iframe' href='f2f/face_to_face.php?f2fid=".htmlspecialchars( $iter{"id"}, ENT_NOQUOTES).
                            "&inmode=edit&patient_id=$pid&provider=$provider&refer=$refer&location=provider_portal' style='background-color:#49C1DC;
                              margin-top: 20px; color: #fff; border-radius:20px; font: bold 10px arial, sans-serif;  transition: all 0.3s ease-in;  padding: 8px 10px;   border: 2px solid #fff;'>".htmlspecialchars( xl('Edit'), ENT_NOQUOTES)."</a>";
                     ?>
                </td>
                <td><?php   //if (acl_check('admin', 'super')) {
//                        print "<a href='javascript:;' onclick=win1('../interface/patient_file/deleter.php?f2fid=".
//                                    htmlspecialchars( $iter{"id"}, ENT_QUOTES).
//                                    "') class='welcome-btn1' >            
//                           <span>".htmlspecialchars( xl('Delete'), ENT_NOQUOTES)."</span></a>";
                           echo "<a class='various' data-fancybox-type='iframe' href='chartoutput/deleter.php?f2fid=".
                                    htmlspecialchars( $iter{"id"}, ENT_QUOTES).
                                   "&location=provider_portal&patient_id=$pid&provider=$provider&refer=$refer&trans=f2f' style='background-color:#49C1DC;
                                     margin-top: 20px; color: #fff; border-radius:20px; font: bold 10px arial, sans-serif;  transition: all 0.3s ease-in;  padding: 8px 10px;   border: 2px solid #fff;'>".htmlspecialchars( xl('Delete'), ENT_NOQUOTES)."</a>";
//                    }
//                    else {
//                           echo "not authorized";
                  //}
                  ?>
                    
                </td>
                <?php  foreach($field_id as $attr)
                        { 
                         echo " <td style='width:150px;'>" .
                        htmlspecialchars( ($iter{$attr}), ENT_NOQUOTES). "&nbsp;</td>";
                        }?>
               
             </tr>
    <?php } } ?>         
 </table>
<?php }?>

<script type='text/javascript'>
                 
			$(document).ready(function() {
                           
                          
   
                             //datatable
                             var responsiveHelper;
                            var breakpointDefinition = {
                                tablet: 1024,
                                phone : 480
                            };
                            var tableElement = $('#f2f_data');
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