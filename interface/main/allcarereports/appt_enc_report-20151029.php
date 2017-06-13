<?php
 // Copyright (C) 2005-2010 Rod Roark <rod@sunsetsystems.com>
 //
 // This program is free software; you can redistribute it and/or
 // modify it under the terms of the GNU General Public License
 // as published by the Free Software Foundation; either version 2
 // of the License, or (at your option) any later version.

 // This report cross-references appointments with encounters.
 // For a given date, show a line for each appointment with the
 // matching encounter, and also for each encounter that has no
 // matching appointment.  This helps to catch these errors:
 //
 // * Appointments with no encounter
 // * Encounters with no appointment
 // * Codes not justified
 // * Codes not authorized
 // * Procedure codes without a fee
 // * Fees assigned to diagnoses (instead of procedures)
 // * Encounters not billed
 //
 // For decent performance the following indexes are highly recommended:
 //   openemr_postcalendar_events.pc_eventDate
 //   forms.encounter
 //   billing.pid_encounter

require_once("../../globals.php");
require_once("$srcdir/patient.inc");
require_once("$srcdir/formatting.inc.php");
require_once("../../../custom/code_types.inc.php");
require_once("$srcdir/billing.inc");

 $errmsg  = "";
 $alertmsg = ''; // not used yet but maybe later
 $grand_total_charges    = 0;
 $grand_total_copays     = 0;
 $grand_total_encounters = 0;

function postError($msg) {
  global $errmsg;
  if ($errmsg) $errmsg .= '<br />';
  $errmsg .= $msg;
}

 function bucks($amount) {
  if ($amount) echo oeFormatMoney($amount);
 }

 function endDoctor(&$docrow,&$col_count) {

  
 global $grand_total_charges, $grand_total_copays, $grand_total_encounters;
  if (!$docrow['Practitioner']) return;
  
  echo " <tr class='report_totals'>\n";
  echo "  <td >\n";
  echo "   &nbsp;" . xl('Totals for','','',' ') . $docrow['Practitioner'] . "\n";
  echo "  </td>\n";
  echo "  <td align='right'>\n";
  echo "   &nbsp;" . $docrow['encounters'] . "&nbsp;\n";
  echo "  </td>\n";
  echo "  <td align='right'>\n";
  echo "   &nbsp;"; bucks($docrow['charges']); echo "&nbsp;\n";
  echo "  </td>\n";
  echo "  <td align='right'>\n";
  echo "   &nbsp;"; bucks($docrow['copays']); echo "&nbsp;\n";
  echo "  </td>\n";
  echo " </tr>\n";

  $grand_total_charges     += $docrow['charges'];
  $grand_total_copays      += $docrow['copays'];
  $grand_total_encounters  += $docrow['encounters'];

  $docrow['charges']     = 0;
  $docrow['copays']      = 0;
  $docrow['encounters']  = 0;
 }
 
//filters
 $form_facility  = isset($_POST['form_facility']) ? $_POST['form_facility'] : '';
 $form_from_date = fixDate($_POST['form_from_date'], date('Y-m-d'));
 $form_to_date = fixDate($_POST['form_to_date'], date('Y-m-d'));
 $selected_fields=$_POST['selectEncColmsData'];
 $patientVisitType = $_POST['selectVisitTp'];
 $patientlist=$_POST['form_plist'];
 $patientapp=$_POST['selectPatientsL'];
 $rendering_provider=$_POST['rendering_provider'];
// echo "<pre>"; print_r($rendering_provider); echo "</pre>";
 $appt_stat=$_POST['appt_stat']; 
// echo "<pre>"; print_r($appt_stat);echo "</pre>";
 $practitioner=$_POST['practitioner']; 
// echo "<pre>"; print_r($practitioner); echo "</pre>";
 
 
 
 
 
// Scheduling Visit Categories from allcareConfig lists
$visit_list = '';
$get_visit_categories = sqlStatement("SELECT title FROM list_options WHERE list_id= 'allcareConfig' AND option_id='SchedulingVisitCategories'");
while($setvisit = sqlFetchArray($get_visit_categories)){
    $visit_list = $setvisit['title'];
}

// AWV Visit Categories
$awvvisit_list = '';
$get_awvvisit_categories = sqlStatement("SELECT title FROM list_options WHERE list_id= 'allcareConfig' AND option_id='SchedulingAWVVisits'");
while($setvisit = sqlFetchArray($get_awvvisit_categories)){
    $awvvisit_list = $setvisit['title'];
}

// H&P Visit Categories
$hpvisit_list = '';
$get_hpvisit_categories = sqlStatement("SELECT title FROM list_options WHERE list_id= 'allcareConfig' AND option_id='SchedulingHPVisits'");
while($setvisit = sqlFetchArray($get_hpvisit_categories)){
    $hpvisit_list = $setvisit['title'];
}

// Super Vision Visit Categories
$spvisit_list = '';
$get_spvisit_categories = sqlStatement("SELECT title FROM list_options WHERE list_id= 'allcareConfig' AND option_id='SchedulingSuperVisionVisits'");
while($setvisit = sqlFetchArray($get_spvisit_categories)){
    $spvisit_list = $setvisit['title'];
}

// Cert Visit Categories
$ctvisit_list = '';
$get_ctvisit_categories = sqlStatement("SELECT title FROM list_options WHERE list_id= 'allcareConfig' AND option_id='SchedulingCertVisits'");
while($setvisit = sqlFetchArray($get_ctvisit_categories)){
    $ctvisit_list = $setvisit['title'];
}

// CCM Visit Categories
$ccmvisit_list = '';
$get_ccmvisit_categories = sqlStatement("SELECT title FROM list_options WHERE list_id= 'allcareConfig' AND option_id='SchedulingCCMVisits'");
while($setvisit = sqlFetchArray($get_ccmvisit_categories)){
    $ccmvisit_list = $setvisit['title'];
}

// Sudo Visit Categories
$sudovisit_list = '';
$get_sudovisit_categories = sqlStatement("SELECT title FROM list_options WHERE list_id= 'allcareConfig' AND option_id='SchedulingSudoVisits'");
while($setvisit = sqlFetchArray($get_sudovisit_categories)){
    $sudovisit_list = $setvisit['title'];
}


if($patientVisitType == 1):
    $currentVisitCategories = $visit_list;
    if($visit_list == ""):
        ?>
        <script> 
            alert("Please configure New/Establised Visit Categories"); </script>
        <?php
    endif;
elseif($patientVisitType == 2):
    $currentVisitCategories = $awvvisit_list;
    if($awvvisit_list == ""):
        ?>
        <script> alert("Please configure AWV Visit Categories"); </script>
        <?php
    endif;    
elseif($patientVisitType == 3):
    $currentVisitCategories = $hpvisit_list;
    if($hpvisit_list == ""):
        ?>
        <script> alert("Please configure H & P Visit Categories"); </script>
        <?php
    endif; 
elseif($patientVisitType == 4):
    $currentVisitCategories = $spvisit_list;
    if($spvisit_list == ""):
        ?>
        <script> alert("Please configure CPO/Supervision Visit Categories"); </script>
        <?php
    endif;
elseif($patientVisitType == 5):
    $currentVisitCategories = $ctvisit_list;
    if($ctvisit_list == ""):
        ?>
        <script> alert("Please configure Cert Visit Categories"); </script>
        <?php
    endif;
elseif($patientVisitType == 6):
    $currentVisitCategories = $ccmvisit_list;
    if($ccmvisit_list == ""):
        ?>
        <script> alert("Please configure CCM Visit Categories"); </script>
        <?php
    endif;
elseif($patientVisitType == 7):    
    $currentVisitCategories = $sudovisit_list;
    if($sudovisit_list == ""):
        ?>
        <script> alert("Please configure Sudo Visit Categories"); </script>
        <?php
    endif;
elseif($patientVisitType == 0):    
    $currentVisitCategories=0;
 endif;

?>
<html>
<head>
<?php html_header_show();?>
<link rel="stylesheet" href="<?php echo $css_header;?>" type="text/css">
<script type="text/javascript">

function getSetNames()
{
    
     var QuerySet='';
     QuerySet=jQuery('#selectQuerySet'); 
     var screen='appt_enc_report';
      $.ajax({
		type: 'POST',
		url: "get_querysets.php",	
                data:{screen:screen},
		success: function(response)
		{
                    var setsArray=response.split("|");
                    //jQuery('#selectQuerySet').find('option').remove();
                    QuerySet.find('option').remove();
                    //jQuery("#selectQuerySet").append("<option value='-1'>--Select--</option>");                        
                    QuerySet.append("<option value='-1'>--Select--</option>");
                    for(var i=0;i<setsArray.length;i++)
                    {
                        //jQuery("#selectQuerySet").append("<option value='"+ setsArray[i] +"'>"+ setsArray[i] +"</option>");                        
                        QuerySet.append("<option value='"+ setsArray[i] +"'>"+ setsArray[i] +"</option>");
                        

                    }

		},
		failure: function(response)
		{
                    alert("error");
		}		
	});	 
}
function saveSelection()
{
   var querySetName=jQuery('#txtQuerySet').val(); 
   var facility=jQuery('#form_facility').val();
   var from_date=jQuery('#form_from_date').val();
   var to_date=jQuery('#form_to_date').val();
  
  var details_val= $("#form_details").attr("checked") ? "Checked" : "Unchecked";
  if(details_val=="Checked")
       details='1';
   else if(details_val=="Unchecked")
      details='0';
   var facility_enc=facility+" ";
   var selected_fields=jQuery('#selectEncColmsData').val();
   var selected_fields1=selected_fields+" ";
   var visit_type=jQuery('#selectVisitTp').val();
   var patient_list=jQuery('#form_plist').val();
   var due_appt=jQuery('#selectPatientsL').val();
   var rend_provider1=jQuery('#rendering_provider').val();
   var rend_provider=rend_provider1+" ";
   var appt_stat1=jQuery('#appt_stat').val();
   var appt_stat=appt_stat1+" ";
   var practitioner1=jQuery('#practitioner').val();
   var practitioner=practitioner1+" ";
   var screen='appt_enc_report';
  
    $.ajax({
            type: 'POST',
            url: "save_fac_query_set.php",	
            data:{
                   screen:screen,querySetName:querySetName,facility_enc:facility_enc,
                   from_date:from_date,to_date:to_date,details:details,selected_fields1:selected_fields1,visit_type:visit_type,
                   patient_list:patient_list,due_appt:due_appt,rend_provider:rend_provider,appt_stat:appt_stat,practitioner:practitioner
             },
 
            success: function(response)
            {
               //alert(response);
                jQuery('#txtQuerySet').val(''); 
                jQuery('#txtQuerySet').hide(); 
                jQuery('#btnSaveSelection').hide();
                getSetNames();    
            },
            failure: function(response)
            {
                    alert("error");
            }		
    });
}

function setByQuerySet()
{
    
  var querySetName=jQuery('#selectQuerySet').val(); 
  var screen='appt_enc_report';
  if(querySetName==='-1')
    {
        //("select option").prop("selected", false);
    }
    $.ajax({
            type: 'POST',
            url: "set_by_querysets.php",	
            data:{screen:screen,querySetName:querySetName},	

            success: function(response)
            {     
                
                var setArray=response.split("|");
                var facilityData=setArray[0];
                var from_dt=setArray[1];
                var  to_dt =setArray[2];
                var details=setArray[3];
                var sel_fields=setArray[4];
                var sel_fields1=sel_fields.trim();
                var visit_type=setArray[5];
                var patient_list=setArray[6];
                var due_appt=setArray[7];
                var rend_provider=setArray[8];
                var appt_stat=setArray[9];
                var practitioner=setArray[10];
               
            //facility
                jQuery('#form_facility').val('');
                  var facilityDataArray=facilityData.split(',');     
                     for(var i=0;i<facilityDataArray.length;i++)
                       {      
                           $('#form_facility').find('option').each(function(){
                                if($(this).val()==facilityDataArray[i]){
                                    $(this).attr('selected','selected');
                                }
                             });
                       }
                
            //from_date
                 jQuery('#form_from_date').val(from_dt);
            //to_date      
                jQuery('#form_to_date').val(to_dt);  
            //details
                if(details==1){
                  $('#form_details').attr('checked', 1);
                }
                //selected_fields
                jQuery('#selectEncColmsData').val('');
                  var sel_fieldsArray=sel_fields1.split(',');     
                     for(var i=0;i<sel_fieldsArray.length;i++)
                       {   
                           $('#selectEncColmsData').find('option').each(function(){
                                if($(this).val()==sel_fieldsArray[i]){
                                    $(this).attr('selected','selected');
                                }
                             });
                       }
               //visit type
                jQuery('#selectVisitTp').val('');
                $('#selectVisitTp').find('option').each(function(){
                    if($(this).val()== visit_type){
                        $(this).attr('selected','selected');
                    }
                 });
                             
                 //patient list
                jQuery('#form_plist').val('');
                $('#form_plist').find('option').each(function(){
                    if($(this).val()== patient_list){
                        $(this).attr('selected','selected');
                    }
                 });
                 
                 //due appt
                 jQuery('#selectPatientsL').val('');
                $('#selectPatientsL').find('option').each(function(){
                    if($(this).val()== due_appt){
                        $(this).attr('selected','selected');
                    }
                 });   
                 //rendering provider
               
                jQuery('#rendering_provider').val('');
                  var rend_providerArray=rend_provider.split(','); 
                  //alert(rend_providerArray);
                     for(var i=0;i<rend_providerArray.length;i++)
                       {      
                           $('#rendering_provider').find('option').each(function(){
                                if($(this).val()==rend_providerArray[i]){
                                    $(this).attr('selected','selected');
                                }
                             });
                       }
                //appt Status
                jQuery('#appt_stat').val('');
                  var appt_statArray=appt_stat.split(',');     
                     for(var i=0;i<appt_statArray.length;i++)
                       {      
                           $('#appt_stat').find('option').each(function(){
                                if($(this).val()==appt_statArray[i]){
                                    $(this).attr('selected','selected');
                                }
                             });
                       }
                //practitioner
                jQuery('#practitioner').val('');
                  var practitionerArray=practitioner.split(',');     
                     for(var i=0;i<practitionerArray.length;i++)
                       {      
                           $('#practitioner').find('option').each(function(){
                                if($(this).val()==practitionerArray[i]){
                                    $(this).attr('selected','selected');
                                }
                             });
                       }
            },
            failure: function(response)
            {
                    alert("error");
            }		
    });
}


function deleteQuerySet()
{
     querySet=jQuery('#selectQuerySet').val(); 
     var screen='appt_enc_report';
     $.ajax({
		type: 'POST',
		url: "delete_querysets.php",	
                data:{screen:screen,querySet:querySet},
		success: function()
		{
                   getSetNames();


		},
		failure: function(response)
		{
                    alert("error");
		}		
	});
      getSetNames();
    
}

</script>
<style type="text/css">

/* specifically include & exclude from printing */
@media print {
    #report_parameters {
        visibility: hidden;
        display: none;
    }
    #report_parameters_daterange {
        visibility: visible;
        display: inline;
    }
    #report_results table {
       margin-top: 0px;
    }
}

/* specifically exclude some from the screen */
@media screen {
    #report_parameters_daterange {
        visibility: hidden;
        display: none;
    }
}

</style>
<link rel='stylesheet' type='text/css' href='../css/jquery.dataTables.css'>
<link rel='stylesheet' type='text/css' href='../css/dataTables.tableTools.css'>
<link rel='stylesheet' type='text/css' href='../css/dataTables.colVis.css'>
<link rel='stylesheet' type='text/css' href='../css/dataTables.colReorder.css'>
<style>
div.DTTT_container {
	float: none;
}
</style>
<script type='text/javascript' src='../js/jquery-1.11.1.min.js'></script>
<!--<script type='text/javascript' src='../js/jquery.dataTables.min.js'></script>-->
<script type='text/javascript' src='../js/jquery.dataTables-1.10.7.min.js'></script>
<script type='text/javascript' src='../js/dataTables.tableTools.js'></script>
<script type='text/javascript' src='../js/dataTables.colReorder.js'></script>
<script type='text/javascript' src='../js/dataTables.colVis.js'></script>
<script type="text/javascript" src="jquery.tablesorter.min.js"></script>
<title><?php  xl('Appointments and Encounters','e'); ?></title>
</head>

<body class="body_top">

<span class='title'><?php xl('Report','e'); ?> - <?php xl('Appointments and Encounters','e'); ?></span>

<div id="report_parameters_daterange">
<?php echo date("d F Y", strtotime($form_from_date)) ." &nbsp; to &nbsp; ". date("d F Y", strtotime($form_to_date)); ?>
</div>

<form method='post' id='theform' name='theform' action='appt_enc_report.php'>

<div id="report_parameters">

<table>
 <tr>
  <td width='630px'>
	<div style='float:left'>

	<table class='text'>
		<tr>
			<td class='label'>
				<?php xl('Facility','e'); ?>:
			</td>
			<td>
				<?php
				 // Build a drop-down list of facilities.
				 //
				 $query = "SELECT id, name FROM facility ORDER BY name";
				 $fres = sqlStatement($query);
				 echo "   <select name='form_facility[]' multiple id='form_facility'>\n";
				 echo "    <option value=''>-- " . xl('All Facilities', 'e') . " --\n";
				 while ($frow = sqlFetchArray($fres)) {
				  $facid = $frow['id'];
				  echo "    <option value='$facid'";
                                  foreach($form_facility as $fac2){
                                  if ($facid == $fac2) echo " selected"; }
				  echo ">" . htmlspecialchars($frow['name']) . "\n";
				 }
				 echo "    <option value='0'";
                                 foreach($form_facility as $fac2) {
                                 if ($fac2 === '0') echo " selected"; }
				 echo ">-- " . xl('Unspecified') . " --\n";
				 echo "   </select>\n";
				?>
			</td>
                         <td> &nbsp;</td>
                        <td>
                            <fieldset style="width:360px">
                                <legend>DOS:</legend>
                                    <?php xl('From','e'); ?>:
                                     <input type='text' name='form_from_date' id="form_from_date" size='10' value='<?php  echo $form_from_date; ?>'
                                    title='Date of appointments mm/dd/yyyy' > <img src='../../pic/show_calendar.gif' align='absbottom' width='24' height='22'
                                    id='img_from_date' border='0' alt='[?]' style='cursor:pointer'
                                    title='<?php xl('Click here to choose a date','e'); ?>'>
                                    
                                    <?php xl('To','e'); ?>:
                                     <input type='text' name='form_to_date' id="form_to_date" size='10' value='<?php  echo $form_to_date; ?>'
                                    title='Optional end date mm/dd/yyyy' >
                                    <img src='../../pic/show_calendar.gif' align='absbottom' width='24' height='22'
                                    id='img_to_date' border='0' alt='[?]' style='cursor:pointer'
                                    title='<?php xl('Click here to choose a date','e'); ?>'>
                            </fieldset>
                        </td>
                       <td> &nbsp;</td>
			
                        
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>
                            <input type='checkbox' name='form_details' id='form_details' 
                            value='1'  <?php if ($_POST['form_details']) echo " checked";?>><?php xl('Details','e') ?>
                             
			</td>
                        <td>&nbsp;</td>
                        <td>
                         <fieldset style="width:230px">
                                <legend>Open/Due appt</legend>   
                        Visit Type:<select id='selectVisitTp' name="selectVisitTp">
                                <option value='0' <?php if($patientVisitType==0) echo "selected"; ?>>Select</option>
                                <option value='1' <?php if($patientVisitType==1) echo "selected"; ?>>New/Established</option>
                                <option value="2" <?php if($patientVisitType==2) echo "selected"; ?>>AWV</option>
                                <option value="3" <?php if($patientVisitType==3) echo "selected"; ?>>H&P</option>
                                <option value="4" <?php if($patientVisitType==4) echo "selected"; ?>>CPO/Supervision</option>
                                <option value="5" <?php if($patientVisitType==5) echo "selected"; ?>>HH Certification</option>
                                <option value="6" <?php if($patientVisitType==6) echo "selected"; ?>>CCM</option>
                                <option value="7" <?php if($patientVisitType==7) echo "selected"; ?>>Sudo</option>
                            </select>
                        <?php echo "<br><br>"; ?>
                        Open/due Appointments:<select id='selectPatientsL' name="selectPatientsL" style="text-align:left;" >
                                <option value='0'>All</option>
                                <option value="1" <?php if($patientapp==1) echo "selected"; ?>>Patients Due For Appointments</option>
                                <option value="2" <?php if($patientapp==2) echo "selected"; ?>>Patients With Open Appointments</option>
                            </select>  
                        </fieldset>
                        </td>
                     </tr>
                     <tr>
                         
                        <td>Patient List:</td>
                        <td>
                            <?php
				 // Build a drop-down list of facilities.
				 //
				 $query_list = "select * from list_options where list_id='Mobile_All_Patients_Filters' order by seq";
				 $fres_list = sqlStatement($query_list);
				?>
                            <select id="form_plist" name="form_plist">
                                <option value="">select</option>
                               <?php  while($frow = sqlFetchArray($fres_list)){ ?>
                                <option value="<?php echo $frow['notes']; ?>" <?php if($patientlist==$frow['notes']) echo "selected"; ?>><?php echo $frow['title']; ?></option>
                               <?php  } ?>
                            </select>    
                        </td>
                        <td>Rendering Provider:</td>
                        <td>
                            <?php 
                            $ures = sqlStatement("SELECT id, fname, lname, specialty FROM users " .
                              "WHERE active = 1 AND ( info IS NULL OR info NOT LIKE '%Inactive%' ) " .
                              "AND authorized = 1 " .
                              "ORDER BY lname, fname");
                                echo "<select name='rendering_provider[]' multiple id='rendering_provider' title='$description'>";
                                        echo "<option value='0' selected>" . htmlspecialchars(xl(Select), ENT_NOQUOTES) . "</option>";
                                        while ($urow = sqlFetchArray($ures)) {
                                          if($urow['fname']!='' && $urow['lname']!=''){  
                                              $uname = htmlspecialchars( $urow['fname'] . ' ' . $urow['lname'], ENT_NOQUOTES);
                                              $optionId = htmlspecialchars( $urow['id'], ENT_QUOTES);
                                              echo "<option value='$optionId'";
                                              foreach($rendering_provider as $val8){
                                              if ($urow['id'] == $val8) echo " selected"; }
                                              echo ">$uname</option>";
                                          }
                                        }
                                        echo "</select>";
                                        ?>
                        </td>
                        <td>Appointment Status:</td>
                        <td>
                            <?php 
                            $ures1 = sqlStatement("SELECT * 
                                                    FROM  `list_options` 
                                                    WHERE list_id =  'apptstat'
                                                    ORDER BY seq");
                                echo "<select name='appt_stat[]' multiple id='appt_stat' >";
                                        echo "<option value='0' selected >" . htmlspecialchars(xl(Select), ENT_NOQUOTES) . "</option>";
                                        while ($urow1 = sqlFetchArray($ures1)) {
                                          $title = htmlspecialchars( $urow1['title'], ENT_NOQUOTES);
                                          $optionId = htmlspecialchars( $urow1['option_id'], ENT_QUOTES);
                                          echo "<option value='$optionId'";
                                          foreach($appt_stat as $val9) { if ($urow1['option_id'] == $val9) echo " selected"; }
                                          echo ">$title</option>";
                                        }
                                        echo "</select>";
                                        ?>
                        </td>
                        
                                    <div id="divselectEncColmsData">
                                         <?php   $query1= "SELECT field_id,list_id,title FROM layout_options " .
                                                    "WHERE form_id = 'DEM' AND uor > 0 AND field_id != '' AND data_type='1' AND group_name='5Misc' " .
                                                    "ORDER BY  seq";
                                                 $ures1 = sqlStatement($query1); 
                                                 while($row1 = sqlFetchArray($ures1)){ 
                                                     //$field_key= ucwords($row1['field_id']);
                                                     $title=str_replace(" ","_",$row1['title']);
                                                     $title1= "`".$title."`";
                                                     $field_key=$row1['field_id']." "."AS"." ".$title1;
                                                     $filterfields[$field_key]=$row1['title'];
                                                 }
                                                 $fields_array = array(
                                                   
//                                                    'e.pc_eventDate AS Date'=> 'Date',
//                                                    'fe.encounter AS Encounter'=> 'Encounter', 
                                                    'Facility'=>'Facility',
                                                    'visit_cat'=>'Visit Category',
                                                    'patient'=>'Patient',
                                                    'p.pubpid AS ID'=> 'ID',
                                                    'cpo_log' => 'CPO Log',
                                                    'ccm_log'=> 'CCM Log',
                                                    'billing_note'=>'Billing Note',
                                                    'fe.rendering_provider AS Rendering_Provider'=>'Rendering Provider' ,
                                                     'e.pc_apptstatus AS Appointment_Status' => 'Appointment_Status'
                                                     );
                                                 $static=array('billed'=> 'Billed',
                                                                'error'=> 'Error',);
                                                  $fields_dy=array_merge($fields_array,$filterfields);
                                                  $fields=array_merge($fields_dy,$static);
                                                  ?>
                                        <tr>
                                            <td>Practitioner:</td>
                                             <td>
                                                <?php 
                                                $ures_prc = sqlStatement("SELECT id, fname, lname, specialty FROM users " .
                                                  "WHERE active = 1 AND ( info IS NULL OR info NOT LIKE '%Inactive%' ) " .
                                                  "AND authorized = 1 " .
                                                  "ORDER BY lname, fname");
                                                    echo "<select name='practitioner[]' multiple id='practitioner' >";
                                                            echo "<option value='0' selected>" . htmlspecialchars(xl(Select), ENT_NOQUOTES) . "</option>";
                                                            while ($urow_prac = sqlFetchArray($ures_prc)) {
                                                                 if($urow_prac['fname']!='' && $urow_prac['lname']!=''){   
                                                                      $uname_prc = htmlspecialchars( $urow_prac['fname'] . ' ' . $urow_prac['lname'], ENT_NOQUOTES);
                                                                      $optionId = htmlspecialchars( $urow_prac['id'], ENT_QUOTES);
                                                                      echo "<option value='$optionId'";
                                                                      foreach($practitioner as $val9){
                                                                      if ($urow_prac['id'] == $val9) echo " selected"; }
                                                                      echo ">$uname_prc</option>";
                                                                 }
                                                            }
                                                            echo "</select>";
                                                            ?>
                                            </td>
                                             <td>Columns:</td><td>
                                            <div class='divSelect'>
                                                <select id="selectEncColmsData" name="selectEncColmsData[]" multiple="multiple" style="height:180px;" >
                                                    <option value="-1">--Select the query--</option>
                                                <?php
                                             
                                                /*
                                                $getPatientFields=sqlStatement("SELECT DISTINCT COLUMN_NAME FROM information_schema.columns
                                                                                WHERE Table_Name='patient_data'
                                                                                AND (COLUMN_NAME!='id' AND COLUMN_NAME!='pid') ORDER BY COLUMN_NAME");
                                                    */  
                                                
                                                 
                                                foreach($fields as $key => $value){
                                                       $option_val=str_replace('.', '-',str_replace(' ', '--', $key));
                                                     ?><option value="<?php echo  str_replace('.', '-',str_replace(' ', '--', $key));  ?>" <?php foreach($selected_fields as $val) { if($val==$option_val) echo "selected"; }?> > <?php echo $value; ?></option><?php 
                                                }
                                                echo "</select>";
                                                echo "<br><br>";
                                                
                                $fields1 = array( 
                                                  'practitioner'=> 'Practitioner',
                                                  'e.pc_eventDate AS Date'=> 'Date',
                                                  'fe.encounter AS Encounter'=> 'Encounter', 
//                                                  'Facility'=>'Facility',
//                                                  'visit_cat'=>'Visit_Category',
                                                  'p.pid AS Chart'=>'Chart',
                                                  'e.pc_startTime' => '',
                                                  'fe.date AS encdate'=>'',
                                                  'f.authorized'=>'',
                                                 
                                                  );
                              
                                foreach($fields1 as $key => $value){
                                    if($key=='practitioner') {
                                          $fieldset3 .= 'CONCAT( u.lname, "," , u.fname ) AS Practitioner,';
                                    }else {
                                     $fieldset3 .= $key.",";
                                    }
                                     
                                }   
                                $fieldsets = rtrim($fieldset3, ',');
                                $fieldparamters1 = $fieldsets; 
                                foreach($fields as $key1 => $value1){
                                   if($key1=='patient'){
                                          $fieldset2.='CONCAT(p.fname,",", p.lname) AS Patient,';
                                     }else if($key1=='billing_note'){
                                         $fieldset2.="fe.billing_note AS Billing_Note,";
                                     }else if($key1=='cpo_log'){
                                           $fieldset2.="(select cpo_data from tbl_form_cpo cpo inner join forms fo where cpo.id = fo.form_id and fo.deleted = 0 and fo.encounter = f.encounter and fo.form_name='CPO' order by cpo.id desc limit 0,1) as cpo_data,";
                                     }else if($key1=='ccm_log'){
                                             $fieldset2.="(select ccm_data from tbl_form_ccm ccm inner join forms fo where ccm.id = fo.form_id and fo.deleted = 0 and fo.encounter = f.encounter and fo.form_name='CCM' order by ccm.id desc limit 0,1) as ccm_data,";
                                     }else if($key1=='Facility'){
                                            $fieldset2 .= 'fe.facility AS Facility,';
                                     }else if($key1=='visit_cat'){
                                            $fieldset2 .= 'fe.pc_catid AS Visit_category,';
                                     }else if($key1!='charges' && $key1!='copays' && $key1!='billed' &&  $key1!=='error') {
                                          $fieldset2 .= $key1.",";
                                     }
                                   
                                    }       
                                
                                $fieldset1 = rtrim($fieldset2, ',');
                                $fieldparamters = $fieldset1; 
                                
                             ?>
                                 
                                </div>

                            </div>
                        </td>
                 </tr>
                 <tr>
                    <td>&nbsp;</td>
                    <td style="padding-bottom: 0px !important; padding-top: 15px !important;"><div style="" onload="javascript:jQuery('#btnSaveSelection').hide();">
                                     <a onclick="javascript: jQuery('#txtQuerySet').show();
                                         jQuery('#btnSaveSelection').show();" style='cursor:pointer'>
                                     <b>Save Selection</b></a><br>
                                     <input type="text" id='txtQuerySet' style='display:none;' /><br>
                                     <input type="button" id='btnSaveSelection' value="Save Selection" style="height:50px;display:none;"
                                      onclick="javascript:if(jQuery('#txtQuerySet').val()!=='')
                                               { saveSelection(); }
                                               else
                                               { alert('Enter query set name'); }
                                                                                                   " /> 
                                    </div>
                     </td>
                     <td class='label' >
			   <?php xl('QuerySets','e'); ?>:
	            </td>
                     <td style="padding-bottom: 0px !important; padding-top: 15px !important;">
                        <div class='divSelect'>
                            <select id='selectQuerySet' onchange="javascript:setByQuerySet();" class="btn btn-default"  style="text-align:left;">
                                <option value='-1'>---Select---</option>
                            <?php

                                $getQuerySets=sqlStatement("SELECT id,setname FROM tbl_allcarereports_querysets where screen='appt_enc_report'");
                                while ($rowQuerySets = sqlFetchArray($getQuerySets)) 
                                {
                                    echo "<option value='".$rowQuerySets['setname']."'>".$rowQuerySets['setname']."</option>";
                                }  

                            ?>
                            </select>

                        </div>  <br> 
                     </td> 
                     <td></td>
                     <td>
                        <a onclick="javascript:if( confirm('Are you sure to delete the selected Query set ?'))
                                                   {
                                                        deleteQuerySet();
                                                    }" style='cursor:pointer'> <b>Delete query set</b></a><br>
                     </td> 
                 </tr>
	</table>

	</div>

  </td>
  <td align='left' valign='middle' height="100%">
	<table style='border-left:1px solid; width:100%; height:100%' >
		<tr>
			<td>
				<div style='margin-left:15px'>
					<a href='#' class='css_button' onclick='$("#form_refresh").attr("value","true"); $("#theform").submit();'>
					<span>
						<?php xl('Submit','e'); ?>
					</span>
					</a>

					<?php if ($_POST['form_refresh']) { ?>
					<a href='#' class='css_button' onclick='window.print()'>
						<span>
							<?php xl('Print','e'); ?>
						</span>
					</a>
					<?php } ?>
				</div>
			</td>
		</tr>
	</table>
  </td>
 </tr>
</table>

</div> <!-- end apptenc_report_parameters -->

<?php
 if ($_POST['form_refresh'] ) {
?>
<div id="report_results">

<?php

//if ($_POST['form_refresh']) {
  $form_from_date = fixDate($_POST['form_from_date'], date('Y-m-d'));
  $form_to_date = fixDate($_POST['form_to_date'], "");

  // MySQL doesn't grok full outer joins so we do it the hard way.
 
  //patient list filters open app and due app
  
  $getOpenAppQuery = sqlStatement("SELECT pc_pid, MAX( pc_eid ),pc_eventDate 
                                FROM openemr_postcalendar_events
                                WHERE pc_catid
                                IN ($currentVisitCategories ) 
                                AND pc_eventDate > CURDATE( ) 
                                GROUP BY pc_pid");
$openAppId = array();
if(sqlNumRows($getOpenAppQuery)>0)
{
    while($rowTemp=sqlFetchArray($getOpenAppQuery))
    {
        if($rowTemp['pc_pid'] != ""){
            $openAppId[] = $rowTemp['pc_pid'];
        }
    }
    $openAppIdStr = implode(",",$openAppId);
    if($openAppIdStr != ""){
        $openAppIdStrInClause = " AND p.pid  IN (".$openAppIdStr.")";
//        $openSetAppIdStrInClause = " AND p.pid IN (".$openAppIdStr.")";
    }    
}


$getDueAppQuery = sqlStatement("SELECT pc_pid, MAX( pc_eid ),pc_eventDate 
                                FROM openemr_postcalendar_events
                                WHERE pc_catid
                                IN ($currentVisitCategories ) 
                                AND pc_eventDate < CURDATE( ) 
                                GROUP BY pc_pid");
$dueAppId = array();
if(sqlNumRows($getDueAppQuery)>0)
{
    while($rowTemp1=sqlFetchArray($getDueAppQuery))
    {
        if($rowTemp1['pc_pid'] != ""){
            $dueAppId[] = $rowTemp1['pc_pid'];
        }
    }
    $dueAppIdStr = implode(",",$dueAppId);
    if($dueAppIdStr != ""){
        $dueAppIdStrInClause = " AND p.pid  IN (".$dueAppIdStr.")";
//        $openSetAppIdStrInClause = " AND p.pid IN (".$openAppIdStr.")";
    }    
}
  
  if($selected_fields[0]!='-1' && !empty($_POST['selectEncColmsData']))  {
    // echo "<pre>"; print_r($selected_fields); echo "</pre>";
      foreach($selected_fields as $key=> $value){
         
          if($value=='patient'){
              $field_val.='CONCAT(p.fname,",", p.lname) AS Patient,';
          }else if($value=='cpo_log'){
              $field_val.="(select cpo_data from tbl_form_cpo cpo inner join forms fo where cpo.id = fo.form_id and fo.deleted = 0 and fo.encounter = f.encounter and fo.form_name='CPO' order by cpo.id desc limit 0,1) as cpo_data,";
          }else if($value=='ccm_log'){
               $field_val.="(select ccm_data from tbl_form_ccm ccm inner join forms fo where ccm.id = fo.form_id and fo.deleted = 0 and fo.encounter = f.encounter and fo.form_name='CCM' order by ccm.id desc limit 0,1) as ccm_data,";
          }elseif($value=='billing_note'){
               $field_val.="fe.billing_note AS Billing_Note,";
          }else if($value=='Facility'){
               $field_val .= 'fe.facility AS Facility,';
          }else if($value=='visit_cat'){
                $field_val .= 'fe.pc_catid AS Visit_category,';
          } else if($value!='billed' &&  $value!='error'){
              $field_val .= str_replace('-', '.',str_replace('--', ' ', $value)).",";
          }
         
          $fieldval1=rtrim($field_val,",");
    }
//    $pos=strpos($fieldval1, 'Practitioner');
   // $pos1=strpos($fieldval1, 'Date');
    //$practitioner='';
//    if($pos!=''){
//        $practitioner='Practitioner,';
//    }
//    $date='';
//    if($pos1!=''){
//        $date='Date,';
//    }
   
  $query = "( " .
   "SELECT " ." ".$fieldparamters1.",".$fieldval1." ".
    "FROM openemr_postcalendar_events AS e " .
   "LEFT OUTER JOIN form_encounter AS fe " .
   "ON fe.date = e.pc_eventDate AND fe.pid = e.pc_pid " .
   "LEFT OUTER JOIN forms AS f ON f.pid = fe.pid AND f.encounter = fe.encounter AND f.formdir = 'newpatient' " .
   "LEFT OUTER JOIN patient_data AS p ON p.pid = e.pc_pid " .
   // "LEFT OUTER JOIN users AS u ON BINARY u.username = BINARY f.user WHERE ";
             
   "LEFT OUTER JOIN users AS u ON u.id = fe.provider_id WHERE ";
  if ($form_to_date) {
   $query .= "e.pc_eventDate >= '$form_from_date' AND e.pc_eventDate <= '$form_to_date' ";
  } else {
   $query .= "e.pc_eventDate = '$form_from_date' ";
  }
  if (!empty($form_facility)) {
   foreach($form_facility as $fac) {
       if(!empty($fac)){
              
               $fac1.="'$fac'".",";
               $fac2= rtrim($fac1,",");   
               
        }
   }
   if($fac2!=''){
    $query .= "AND fe.facility_id IN ($fac2) ";
   }
  }
  if($patientVisitType!='' && $currentVisitCategories!=0){
      $query .= "AND e.pc_catid  IN ($currentVisitCategories ) ";
  }
  if($patientlist!=''){
       $query .= "AND ".$patientlist;
  }
  if($patientapp!=''){
       if($patientapp == 1 && $dueAppIdStrInClause!=''){
            // get due for appointment patient in grid
          
           $query .= $dueAppIdStrInClause;
        }
        else if($patientapp == 2 && $openAppIdStrInClause!=''){
            // get appointmented patients in grid
             $query .= $openAppIdStrInClause;
        }
  }
  
  if (!empty($rendering_provider)) {
   foreach($rendering_provider as $rend) {
       if(!empty($rend) && $rend!=0){
            $rend1.="'$rend'".",";
            $rend2= rtrim($rend1,",");   
           
         }
     }
     if($rend2!=''){
      $query .= "AND fe.rendering_provider IN ($rend2) ";
     }
  }
  
  if (!empty($appt_stat)) {
   foreach($appt_stat as $stat) {
       if(!empty($stat) && $stat!='0'){
            $stat1.="'$stat'".",";
            $stat2= rtrim($stat1,",");   
           
         }
   }
   if($stat2!='') {
    $query .= "AND e.pc_apptstatus IN ($stat2) ";
   }
  }
  if(!empty($practitioner)){
      foreach($practitioner as $prac) {
       if(!empty($prac) && $prac!=0){
            $prac1.="'$prac'".",";
            $prac2= rtrim($prac1,",");   
           
         }
   }
   if($prac2!=''){
    $query .= "AND fe.provider_id IN ($prac2) ";
   }
  }
  // $query .= "AND ( e.pc_catid = 5 OR e.pc_catid = 9 OR e.pc_catid = 10 ) " .
  $query .= "AND e.pc_pid != '' AND e.pc_apptstatus != '?' " .
   ") UNION ( " .
   "SELECT " ." ".$fieldparamters1.",".$fieldval1." ".
   "FROM form_encounter AS fe " .
   "LEFT OUTER JOIN openemr_postcalendar_events AS e " .
   "ON fe.date = e.pc_eventDate AND fe.pid = e.pc_pid AND " .
   // "( e.pc_catid = 5 OR e.pc_catid = 9 OR e.pc_catid = 10 ) " .
   "e.pc_pid != '' AND e.pc_apptstatus != '?' " .
   "LEFT OUTER JOIN forms AS f ON f.pid = fe.pid AND f.encounter = fe.encounter AND f.formdir = 'newpatient' " .
   "LEFT OUTER JOIN patient_data AS p ON p.pid = fe.pid " .
   // "LEFT OUTER JOIN users AS u ON BINARY u.username = BINARY f.user WHERE ";
       
   "LEFT OUTER JOIN users AS u ON u.id = fe.provider_id WHERE ";
  if ($form_to_date) {
   // $query .= "LEFT(fe.date, 10) >= '$form_from_date' AND LEFT(fe.date, 10) <= '$form_to_date' ";
   $query .= "fe.date >= '$form_from_date 00:00:00' AND fe.date <= '$form_to_date 23:59:59' ";
  } else {
   // $query .= "LEFT(fe.date, 10) = '$form_from_date' ";
   $query .= "fe.date >= '$form_from_date 00:00:00' AND fe.date <= '$form_from_date 23:59:59' ";
  }
  if (!empty($form_facility)) {
       foreach($form_facility as $fac3) {
           if(!empty($fac3)){
              
               $fac4.="'$fac3'".",";
               $fac5= rtrim($fac4,",");   
              
            }
       }
       if($fac5!=''){
        $query .= "AND fe.facility_id IN ($fac5) ";
       }
   }
   if($patientVisitType!='' && $currentVisitCategories!=0){
      $query .= "AND e.pc_catid  IN ($currentVisitCategories ) ";
  }
  
  if($patientlist!=''){
       $query .= "AND ".$patientlist;
  }
   if($patientapp!=''){
       if($patientapp == 1 && $dueAppIdStrInClause!=''){
            // get due for appointment patient in grid
          
           $query .= $dueAppIdStrInClause;
        }
        else if($patientapp == 2 && $openAppIdStrInClause!=''){
            // get appointmented patients in grid
             $query .= $openAppIdStrInClause;
        }
  }
   if (!empty($rendering_provider)) {
   foreach($rendering_provider as $rend3) {
       if(!empty($rend3) && $rend3!=0){
            $rend4.="'$rend3'".",";
            $rend5= rtrim($rend4,",");   
            
         }
     }
     if($rend5!=''){
     $query .= "AND fe.rendering_provider IN ($rend5) ";
     }
  }
  
  if (!empty($appt_stat)) {
   foreach($appt_stat as $stat3) {
       if(!empty($stat3) && $stat3!='0'){
            $stat4.="'$stat3'".",";
            $stat5= rtrim($stat4,",");   
           
         }
   }
     if($stat5!=''){
       $query .= "AND e.pc_apptstatus IN ($stat5) ";
     }
  }
  if(!empty($practitioner)){
      foreach($practitioner as $prac3) {
       if(!empty($prac3) && $prac3!=0){
            $prac4.="'$prac3'".",";
            $prac5= rtrim($prac4,",");   
           
         }
   }
   if($prac5!=''){
    $query .= "AND fe.provider_id IN ($prac5) ";
   }
  }
  $query .= ") ORDER BY practitioner, Date, pc_startTime";
 //echo $query; 
  $res = sqlStatement($query);
  $res1=sqlStatement($query);
  } else {
   
      $query = "( " .
   "SELECT " ." ".$fieldparamters1.",".$fieldparamters." ".
    "FROM openemr_postcalendar_events AS e " .
   "LEFT OUTER JOIN form_encounter AS fe " .
   "ON fe.date = e.pc_eventDate AND fe.pid = e.pc_pid " .
   "LEFT OUTER JOIN forms AS f ON f.pid = fe.pid AND f.encounter = fe.encounter AND f.formdir = 'newpatient' " .
   "LEFT OUTER JOIN patient_data AS p ON p.pid = e.pc_pid " .
   // "LEFT OUTER JOIN users AS u ON BINARY u.username = BINARY f.user WHERE ";
               
   "LEFT OUTER JOIN users AS u ON u.id = fe.provider_id WHERE ";
  if ($form_to_date) {
   $query .= "e.pc_eventDate >= '$form_from_date' AND e.pc_eventDate <= '$form_to_date' ";
  } else {
   $query .= "e.pc_eventDate = '$form_from_date' ";
  }
  if (!empty($form_facility)) {
   foreach($form_facility as $fac) {
        if(!empty($fac)){
               $fac1.="'$fac'".",";
               $fac2= rtrim($fac1,",");  
              
            }
   }
   if($fac2!=''){
    $query .= "AND fe.facility_id IN ($fac2) ";
   }
  }
  if($patientVisitType!='' && $currentVisitCategories!=0){
      $query .= "AND e.pc_catid  IN ($currentVisitCategories ) ";
  }
  if($patientlist!=''){
       $query .= "AND ".$patientlist;
  }
   if($patientapp!=''){
       if($patientapp == 1 && $dueAppIdStrInClause!=''){
            // get due for appointment patient in grid
          
           $query .= $dueAppIdStrInClause;
        }
        else if($patientapp == 2 && $openAppIdStrInClause!=''){
            // get appointmented patients in grid
             $query .= $openAppIdStrInClause;
        }
  }
   if (!empty($rendering_provider)) {
   foreach($rendering_provider as $rend) {
       if(!empty($rend) && $rend!=0){
            $rend1.="'$rend'".",";
            $rend2= rtrim($rend1,",");   
           
         }
     }
     if($rend2!=''){
      $query .= "AND fe.rendering_provider IN ($rend2) ";
     }
  }
  
  if (!empty($appt_stat)) {
   foreach($appt_stat as $stat) {
       if(!empty($stat) && $stat!='0'){
            $stat1.="'$stat'".",";
            $stat2= rtrim($stat1,",");   
           
         }
   }
   if($stat2!=''){
    $query .= "AND e.pc_apptstatus IN ($stat2) ";
   }
  }
  if(!empty($practitioner)){
      foreach($practitioner as $prac) {
       if(!empty($prac) && $prac!=0){
            $prac1.="'$prac'".",";
            $prac2= rtrim($prac1,",");   
            
         }
   }
   if($prac2!=''){
   $query .= "AND fe.provider_id IN ($prac2) ";
   }
   
  }
  // $query .= "AND ( e.pc_catid = 5 OR e.pc_catid = 9 OR e.pc_catid = 10 ) " .
  $query .= "AND e.pc_pid != '' AND e.pc_apptstatus != '?' " .
   ") UNION ( " .
   "SELECT " ." ".$fieldparamters1.",".$fieldparamters." ".
   "FROM form_encounter AS fe " .
   "LEFT OUTER JOIN openemr_postcalendar_events AS e " .
   "ON fe.date = e.pc_eventDate AND fe.pid = e.pc_pid AND " .
   // "( e.pc_catid = 5 OR e.pc_catid = 9 OR e.pc_catid = 10 ) " .
   "e.pc_pid != '' AND e.pc_apptstatus != '?' " .
   "LEFT OUTER JOIN forms AS f ON f.pid = fe.pid AND f.encounter = fe.encounter AND f.formdir = 'newpatient' " .
   "LEFT OUTER JOIN patient_data AS p ON p.pid = fe.pid " .
   // "LEFT OUTER JOIN users AS u ON BINARY u.username = BINARY f.user WHERE ";
        
   "LEFT OUTER JOIN users AS u ON u.id = fe.provider_id WHERE ";
  if ($form_to_date) {
   // $query .= "LEFT(fe.date, 10) >= '$form_from_date' AND LEFT(fe.date, 10) <= '$form_to_date' ";
   $query .= "fe.date >= '$form_from_date 00:00:00' AND fe.date <= '$form_to_date 23:59:59' ";
  } else {
   // $query .= "LEFT(fe.date, 10) = '$form_from_date' ";
   $query .= "fe.date >= '$form_from_date 00:00:00' AND fe.date <= '$form_from_date 23:59:59' ";
  }
  if (!empty($form_facility)) {
       foreach($form_facility as $fac3) {
           if(!empty($fac3)){
               $fac4.="'$fac3'".",";
               $fac5= rtrim($fac4,",");  
               
            }
       }
       if($fac5!=''){
       $query .= "AND fe.facility_id IN ($fac5) ";
       }
     }
     if($patientVisitType!='' && $currentVisitCategories!=0){
      $query .= "AND e.pc_catid  IN ($currentVisitCategories ) ";
  }
  if($patientlist!=''){
       $query .= "AND ".$patientlist;
  }
   if($patientapp!=''){
       if($patientapp == 1 && $dueAppIdStrInClause!=''){
            // get due for appointment patient in grid
          
           $query .= $dueAppIdStrInClause;
        }
        else if($patientapp == 2 && $openAppIdStrInClause!=''){
            // get appointmented patients in grid
             $query .= $openAppIdStrInClause;
        }
  }
   if (!empty($rendering_provider)) {
      foreach($rendering_provider as $rend3) {
      if(!empty($rend3) && $rend3!=0){
            $rend4.="'$rend3'".",";
            $rend5= rtrim($rend4,",");   
            
         }
     }
     if($rend5!=''){
     $query .= "AND fe.rendering_provider IN ($rend5) ";
     }
  }
  
  if (!empty($appt_stat)) {
   foreach($appt_stat as $stat3) {
       if(!empty($stat3) && $stat3!='0'){
            $stat4.="'$stat3'".",";
            $stat5= rtrim($stat4,",");   
            
         }
   }
   if($stat5!=''){
   $query .= "AND e.pc_apptstatus IN ($stat5) ";
   }
  }
  if(!empty($practitioner)){
      foreach($practitioner as $prac3) {
       if(!empty($prac3) && $prac3!=0){
            $prac4.="'$prac3'".",";
            $prac5= rtrim($prac4,",");   
            
         }
   }
   if($prac5!=''){
   $query .= "AND fe.provider_id IN ($prac5) ";
   }
  }
  $query .= ") ORDER BY practitioner, Date, pc_startTime";
 //echo $query;
  $res = sqlStatement($query);
  $res1=sqlStatement($query);
  }
// }
 
 
 
 if ($res) {
     
 // function display_db_query($res) {
	
        echo "<br/>";
	print("<TABLE border='1' id='appt_enc_reports' class='display'>\n");
	        //$row1 = sqlFetchArray($res);
               
	
  $docrow = array('Practitioner' => '', 'charges' => 0, 'copays' => 0, 'encounters' => 0);
  $i=0;
  while ($row = sqlFetchArray($res)) {
      $data[]=$row;
      $i++;
   $patient_id = $row['Chart'];
   $encounter  = $row['Encounter'];
   $docname    = $row['Practitioner'] ? $row['Practitioner'] : xl('Unknown');
   
//   $count1=count($selected_fields);
//   if ($docname != $docrow['Practitioner']) {
//     endDoctor($docrow,$count1);
//   }

   $errmsg  = "";
   $billed  = "Y";
   $charges = 0;
   $copays  = 0;
   $gcac_related_visit = false;

   // Scan the billing items for status and fee total.
   //
    $query = "SELECT code_type, code, modifier, authorized, billed, fee, justify " .
    "FROM billing WHERE " .
    "pid = '$patient_id' AND encounter = '$encounter' AND activity = 1";
   
   $bres = sqlStatement($query);
   //
   while ($brow = sqlFetchArray($bres)) {
    $code_type = $brow['code_type'];
   
    if ($code_types[$code_type]['fee'] && !$brow['billed']) {
      $billed = ""; 
    }
    if (!$GLOBALS['simplified_demographics'] && !$brow['authorized']) {
         postError(xl('Needs Auth'));
        
    }
     
    if ($code_types[$code_type]['just']) {
     if (! $brow['justify']) { postError(xl('Needs Justify'));  }
     }
    if ($code_types[$code_type]['fee']) {
     $charges += $brow['fee'];
     if ($brow['fee'] == 0 && !$GLOBALS['ippf_specific']) { postError(xl('Missing Fee')); }
    } else {
     if ($brow['fee'] != 0)  { postError(xl('Fee is not allowed')); }
    }

    // Custom logic for IPPF to determine if a GCAC issue applies.
    if ($GLOBALS['ippf_specific']) {
      if (!empty($code_types[$code_type]['fee'])) {
        $query = "SELECT related_code FROM codes WHERE code_type = '" .
          $code_types[$code_type]['id'] . "' AND " .
          "code = '" . $brow['code'] . "' AND ";
        if ($brow['modifier']) {
          $query .= "modifier = '" . $brow['modifier'] . "'";
        } else {
          $query .= "(modifier IS NULL OR modifier = '')";
        }
        $query .= " LIMIT 1";
        $tmp = sqlQuery($query);
        $relcodes = explode(';', $tmp['related_code']);
        foreach ($relcodes as $codestring) {
          if ($codestring === '') continue;
          list($codetype, $code) = explode(':', $codestring);
          if ($codetype !== 'IPPF') continue;
          if (preg_match('/^25222/', $code)) $gcac_related_visit = true;
        }
      }
    } // End IPPF stuff

   } // end while
   
   $copays -= getPatientCopay($patient_id,$encounter);

   // The following is removed, perhaps temporarily, because gcac reporting
   // no longer depends on gcac issues.  -- Rod 2009-08-11
   /******************************************************************
   // More custom code for IPPF.  Generates an error message if a
   // GCAC issue is required but is not linked to this visit.
   if (!$errmsg && $gcac_related_visit) {
    $grow = sqlQuery("SELECT l.id, l.title, l.begdate, ie.pid " .
      "FROM lists AS l " .
      "LEFT JOIN issue_encounter AS ie ON ie.pid = l.pid AND " .
      "ie.encounter = '$encounter' AND ie.list_id = l.id " .
      "WHERE l.pid = '$patient_id' AND " .
      "l.activity = 1 AND l.type = 'ippf_gcac' " .
      "ORDER BY ie.pid DESC, l.begdate DESC LIMIT 1");
    // Note that reverse-ordering by ie.pid is a trick for sorting
    // issues linked to the encounter (non-null values) first.
    if (empty($grow['pid'])) { // if there is no linked GCAC issue
      if (empty($grow)) { // no GCAC issue exists
        $errmsg = "GCAC issue does not exist";
      }
      else { // there is one but none is linked
        $errmsg = "GCAC issue is not linked";
      }
    }
   }
   ******************************************************************/
   if ($gcac_related_visit) {
      $grow = sqlQuery("SELECT COUNT(*) AS count FROM forms " .
        "WHERE pid = '$patient_id' AND encounter = '$encounter' AND " .
        "deleted = 0 AND formdir = 'LBFgcac'");
      if (empty($grow['count'])) { // if there is no gcac form
        postError(xl('GCAC visit form is missing'));
      }
   } // end if
   /*****************************************************************/

   if (!$billed) postError($GLOBALS['simplified_demographics'] ?
     xl('Not checked out') : xl('Not billed'));
   if (!$encounter) postError(xl('No visit'));

   if (! $charges) $billed = "";

   $docrow['charges'] += $charges;
   $docrow['copays']  += $copays;
   
   if ($encounter) ++$docrow['encounters'];

   if ($_POST['form_details']) {
      
       if($i=='1'){
         
           print("<thead><tr>");
           //echo count($selected_fields);
//            echo "<pre>"; print_r($row['Practitioner']); echo "</pre>";
         //echo "<pre>"; print_r($row); echo "</pre>";
		foreach($row as $key => $value) {
                     if($key!='encdate' && $key!='authorized'){
                         if($key=='cpo_data'){
                             print("<th>CPO Log</th>");
                         }else if($key=='ccm_data'){
                              print("<th>CCM Log</th>");
                         }elseif($key=='pc_startTime'){
                               print("<th>Charges</th>");
                               print("<th>Copays</th>");
                         }else {
                          $header=str_replace("_"," ",$key);   
                          print("<th>$header</th>");
                         }
                     }

                }
              
                if(!empty($selected_fields)){
                    if(in_array('billed',$selected_fields)){
                         print("<th>Billed</th>");
                    }
                    if(in_array('error',$selected_fields)){
                         print("<th>Error</th>");
                    }
                }else{
                     print("<th>Billed</th>");
                     print("<th>Error</th>");
                }
		print("</tr></thead>\n");
	        print("<tfoot><tr>");
		foreach($row as $key => $value) {
                  if($key!='encdate' && $key!='authorized'){
                        if($key=='cpo_data'){
                             print("<th>CPO Log</th>");
                         }else if($key=='ccm_data'){
                              print("<th>CCM Log</th>");
                         }elseif($key=='pc_startTime'){
                               print("<th>Charges</th>");
                               print("<th>Copays</th>");
                         }else {
                          print("<th>$key</th>");
                         }
                     }
                }
               
               if(!empty($selected_fields)){
                    if(in_array('billed',$selected_fields)){
                         print("<th>Billed</th>");
                    }
                    if(in_array('error',$selected_fields)){
                         print("<th>Error</th>");
                    }
                }else{
                     print("<th>Billed</th>");
                     print("<th>Error</th>");
                }
               
		print("</tr></tfoot>\n");
       }
       print("<tr align=left valign=top>");
		foreach($row as $key => $value) {
                    if( $key!='encdate' && $key!='authorized'){
                        if($key=='Date') { 
                           if (empty($row['Date'])) {
                               $date_val= oeFormatShortDate(substr($row['encdate'], 0, 10));
                               print("<td>$date_val</td>\n");
                            }
                            else {
                              $date_val= oeFormatShortDate($row['Date']) . ' ' . substr($row['pc_startTime'], 0, 5);
                               print("<td>$date_val</td>\n");
                            }
                       } else if($key=='Visit_category') {
                           $sql=sqlStatement("SELECT * FROM  `openemr_postcalendar_categories` where pc_catid='".$row['Visit_category']."'"); $row12=sqlFetchArray($sql);
                           $category=$row12['pc_catname'];
                           print("<td>$category</td>\n");
                       }else if($key=='cpo_data'){
                           if($value!=''){
                           $cpoarray = unserialize($value);
                           $cnt1= count($cpoarray);                          
                          // echo "<td>"; echo $cnt1; echo "<pre>"; print_r($cpoarray); echo "</pre>"; echo "</td>";
                           if($cnt1>1){
                               $times12='';
                                for($i=0; $i< count($cpoarray); $i++){
                                    foreach($cpoarray[$i] as $key1 => $value1){
                                        if($key1 == 'timeinterval') {
                                           $time=sqlStatement("select title from list_options where list_id='Time_Interval' AND option_id='$value1'");
                                            $time2 = sqlFetchArray($time);
                                            $times12+=$time2['title'];
                                          
                                        }
                                    }
                                }
                                echo "<td>"; echo $times12; echo "</td>";
                           }else{
                               for($i=0; $i< count($cpoarray); $i++){
                                    foreach($cpoarray[$i] as $key1 => $value1){
                                        if($key1 == 'timeinterval') {    
                                            $time=sqlStatement("select title from list_options where list_id='Time_Interval' AND option_id='$value1'");
                                            $time2 = sqlFetchArray($time);
                                             echo "<td>"; echo $time2['title']; echo "</td>";
                                        }
                                    }
                                }
                           }
                         }else{
                               echo "<td>";  echo "</td>";
                         }
                       }else if($key=='ccm_data'){
                           if($value!=''){
                               $ccmarray = unserialize($value);
                               $cnt= count($ccmarray);

                                if($cnt>1){
                                    $times_val='';
                                    for($i=0; $i< count($ccmarray); $i++){
                                        foreach($ccmarray[$i] as $key2 => $value2){
                                            if($key2 == 'timeinterval') {

                                            $time3=sqlStatement("select title from list_options where list_id='Time_Interval' AND option_id='$value2'");
                                            $times = sqlFetchArray($time3);
                                            $times_val+=$times['title'];

                                          }
                                        }
                                    }
                                       echo "<td>"; echo $times_val;  echo "</td>";
                                }else{
                                    for($i=0; $i< count($ccmarray); $i++){
                                        foreach($ccmarray[$i] as $key2 => $value2){
                                            if($key2 == 'timeinterval') {
                                            $time3=sqlStatement("select title from list_options where list_id='Time_Interval' AND option_id='$value2'");
                                            $times = sqlFetchArray($time3);
                                            echo "<td>"; echo $$times['title']; echo "</td>";
                                          }
                                        }
                                    }
                                }
                           }else {
                               echo "<td>";  echo "</td>";
                           }  
                       }elseif($key=='pc_startTime'){
                            echo "<td>"; bucks($charges); echo"</td>";
                            echo "<td>"; bucks($copays); echo"</td>";
                       }elseif($key=='Practitioner'){
                           if(($docname == $row[$key])){
                               echo "<td>"; echo $row[$key]; echo "</td>";
                           }else{
                                echo "<td>"; echo ($docname == $row[$key]) ? "" : $docname; echo "</td>";
                           }
                       }
                       elseif($key=='Rendering_Provider' && $row[$key]!='' ){
                          
                              $users=sqlStatement("select * from users where id=$row[$key]");
                              $rend_pro=sqlFetchArray($users);
                              echo "<td>"; echo $rend_pro['fname']." ".$rend_pro['lname']; echo "</td>";
                       }elseif($key=='Appointment_Status' && $row[$key]!=''){
                              $appt=sqlStatement("select * from list_options where option_id='$row[$key]'");
                              $appt_st=sqlFetchArray($appt);
                              echo "<td>"; echo $appt_st['title']; echo "</td>";
                       }
                       else {
                        print("<td>$row[$key]</td>\n");
                       }
                     }
                   
                
		}
                
               
                
                if(!empty($selected_fields)){
                    if(in_array('billed',$selected_fields)){
                          print("<td>$billed</td>\n");   
                    }
                    if(in_array('error',$selected_fields)){
                        print("<td>$errmsg</td>\n");
                    }
                }else{
                     print("<td>$billed</td>\n");   
                     print("<td>$errmsg</td>\n");
                }       
                
		print("</tr>\n");
?>

<?php
   } // end of details line

   $docrow['Practitioner'] = $docname;
  } // end of row
  //endDoctor($docrow,count($selected_fields));

	print("</table>\n"); 

  
}
 if ($res1) {

echo "<br/>";
print("<TABLE border='1' id='appt_enc_reports1' class='display'>\n");
$docrow = array('Practitioner' => '', 'charges' => 0, 'copays' => 0, 'encounters' => 0);
  $i=0;
  while ($row1 = sqlFetchArray($res1)) {
      $data[]=$row1;
      $i++;
   $patient_id = $row1['Chart'];
   $encounter  = $row1['Encounter'];
   $docname    = $row1['Practitioner'] ? $row1['Practitioner'] : xl('Unknown');
   
   $count1=count($selected_fields);
       if ($docname != $docrow['Practitioner']) {
         endDoctor($docrow,$count1);
       }

   $errmsg  = "";
   $billed  = "Y";
   $charges = 0;
   $copays  = 0;
   $gcac_related_visit = false;

   // Scan the billing items for status and fee total.
   //
    $query = "SELECT code_type, code, modifier, authorized, billed, fee, justify " .
    "FROM billing WHERE " .
    "pid = '$patient_id' AND encounter = '$encounter' AND activity = 1";
   
   $bres = sqlStatement($query);
   //
   while ($brow = sqlFetchArray($bres)) {
    $code_type = $brow['code_type'];
   
    if ($code_types[$code_type]['fee'] && !$brow['billed']) {
      $billed = ""; 
    }
    if (!$GLOBALS['simplified_demographics'] && !$brow['authorized']) {
         postError(xl('Needs Auth'));
        
    }
     
    if ($code_types[$code_type]['just']) {
     if (! $brow['justify']) { postError(xl('Needs Justify'));  }
     }
    if ($code_types[$code_type]['fee']) {
     $charges += $brow['fee'];
     if ($brow['fee'] == 0 && !$GLOBALS['ippf_specific']) { postError(xl('Missing Fee')); }
    } else {
     if ($brow['fee'] != 0)  { postError(xl('Fee is not allowed')); }
    }

    // Custom logic for IPPF to determine if a GCAC issue applies.
    if ($GLOBALS['ippf_specific']) {
      if (!empty($code_types[$code_type]['fee'])) {
        $query = "SELECT related_code FROM codes WHERE code_type = '" .
          $code_types[$code_type]['id'] . "' AND " .
          "code = '" . $brow['code'] . "' AND ";
        if ($brow['modifier']) {
          $query .= "modifier = '" . $brow['modifier'] . "'";
        } else {
          $query .= "(modifier IS NULL OR modifier = '')";
        }
        $query .= " LIMIT 1";
        $tmp = sqlQuery($query);
        $relcodes = explode(';', $tmp['related_code']);
        foreach ($relcodes as $codestring) {
          if ($codestring === '') continue;
          list($codetype, $code) = explode(':', $codestring);
          if ($codetype !== 'IPPF') continue;
          if (preg_match('/^25222/', $code)) $gcac_related_visit = true;
        }
      }
    } // End IPPF stuff

   } // end while
   
   $copays -= getPatientCopay($patient_id,$encounter);

   // The following is removed, perhaps temporarily, because gcac reporting
   // no longer depends on gcac issues.  -- Rod 2009-08-11
   /******************************************************************
   // More custom code for IPPF.  Generates an error message if a
   // GCAC issue is required but is not linked to this visit.
   if (!$errmsg && $gcac_related_visit) {
    $grow = sqlQuery("SELECT l.id, l.title, l.begdate, ie.pid " .
      "FROM lists AS l " .
      "LEFT JOIN issue_encounter AS ie ON ie.pid = l.pid AND " .
      "ie.encounter = '$encounter' AND ie.list_id = l.id " .
      "WHERE l.pid = '$patient_id' AND " .
      "l.activity = 1 AND l.type = 'ippf_gcac' " .
      "ORDER BY ie.pid DESC, l.begdate DESC LIMIT 1");
    // Note that reverse-ordering by ie.pid is a trick for sorting
    // issues linked to the encounter (non-null values) first.
    if (empty($grow['pid'])) { // if there is no linked GCAC issue
      if (empty($grow)) { // no GCAC issue exists
        $errmsg = "GCAC issue does not exist";
      }
      else { // there is one but none is linked
        $errmsg = "GCAC issue is not linked";
      }
    }
   }
   ******************************************************************/
   if ($gcac_related_visit) {
      $grow = sqlQuery("SELECT COUNT(*) AS count FROM forms " .
        "WHERE pid = '$patient_id' AND encounter = '$encounter' AND " .
        "deleted = 0 AND formdir = 'LBFgcac'");
      if (empty($grow['count'])) { // if there is no gcac form
        postError(xl('GCAC visit form is missing'));
      }
   } // end if
   /*****************************************************************/

   if (!$billed) postError($GLOBALS['simplified_demographics'] ?
     xl('Not checked out') : xl('Not billed'));
   if (!$encounter) postError(xl('No visit'));

   if (! $charges) $billed = "";

   $docrow['charges'] += $charges;
   $docrow['copays']  += $copays;
   
   if ($encounter) ++$docrow['encounters'];

   if ($_POST['form_details']) {
      
       if($i=='1'){
         echo "<thead><tr>";
                  echo "<th>&nbsp;</th>";
                  echo "<th>Encounter</th>";
                  echo "<th>Charges</th>";
                  echo "<th>Copays</th>";
         echo "</tr></thead>";
           
       }
       
?>

<?php
   } // end of details line
   else {
        if($i=='1'){
         echo "<thead><tr>";
                  echo "<th>&nbsp;</th>";
                  echo "<th>Encounter</th>";
                  echo "<th>Charges</th>";
                  echo "<th>Copays</th>";
         echo "</tr></thead>";
           
       }
          
   }
   $docrow['Practitioner'] = $docname;
  } // end of row

 
  endDoctor($docrow,count($selected_fields));

  echo " <tr class='report_totals'>\n";
  echo "  <td colspan=''>\n";
  echo "   &nbsp;" . xl('Grand Totals') . "\n";
  echo "  </td>\n";
  echo "  <td align='right'>\n";
  echo "   &nbsp;" . $grand_total_encounters . "&nbsp;\n";
  echo "  </td>\n";
  echo "  <td align='right'>\n";
  echo "   &nbsp;"; bucks($grand_total_charges); echo "&nbsp;\n";
  echo "  </td>\n";
  echo "  <td align='right'>\n";
  echo "   &nbsp;"; bucks($grand_total_copays); echo "&nbsp;\n";
  echo "  </td>\n";
  echo " </tr>\n";
  print("</table>\n"); 
 
  
}
?>

</div> <!-- end the apptenc_report_results -->
<?php } else { ?>
<div class='text'>
 	<?php echo xl('Please input search criteria above, and click Submit to view results.', 'e' ); ?>
</div>
<?php } ?>

<input type='hidden' name='form_refresh' id='form_refresh' value=''/>

</form>

<script>
<?php if ($alertmsg) { echo " alert('$alertmsg');\n"; } ?>
</script>
<script type='text/javascript'>
jQuery(document).ready(function($) {
    // Setup - add a text input to each footer cell
    $('#appt_enc_reports tfoot th').each( function () {
        var title = $('#appt_enc_reports thead th').eq( $(this).index() ).text();
        $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
    } );
   // DataTable
    var table = $('#appt_enc_reports').DataTable({ 
        //bSort : false,
        dom: 'T<"clear">lfrtip',
                    "tableTools": {
                        "sSwfPath": "../../swf/copy_csv_xls_pdf.swf",
                        "aButtons": [
                            {
                                "sExtends": "xls",
                                "sButtonText": "Save to Excel"
                            }
                        ]
                    } ,
                    "iDisplayLength": 100
                });
 
    // Apply the search
    table.columns().every( function () {
        var that = this;
 
        $( 'input', this.footer() ).on( 'keyup change', function () {
            that
                .search( this.value )
                .draw();
        } );
    } );
} );

    </script>
</body>

<!-- stuff for the popup calendar -->
<style type="text/css">@import url(../../../library/dynarch_calendar.css);</style>
<script type="text/javascript" src="../../../library/dynarch_calendar.js"></script>
<?php include_once("{$GLOBALS['srcdir']}/dynarch_calendar_en.inc.php"); ?>
<script type="text/javascript" src="../../../library/dynarch_calendar_setup.js"></script>
<script type="text/javascript" src="../../../library/js/jquery.1.3.2.js"></script>

<script language="Javascript">
 Calendar.setup({inputField:"form_from_date", ifFormat:"%Y-%m-%d", button:"img_from_date"});
 Calendar.setup({inputField:"form_to_date", ifFormat:"%Y-%m-%d", button:"img_to_date"});
</script>

</html>