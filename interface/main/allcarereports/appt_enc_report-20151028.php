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

 function endDoctor(&$docrow) {
 //echo "<pre>"; print_r($docrow); echo "</pre>";    
   
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
  //echo "  <td>\n";
 // echo "   &nbsp;\n";
  //echo "  </td>\n";
  echo " </tr>\n";

  $grand_total_charges     += $docrow['charges'];
  $grand_total_copays      += $docrow['copays'];
  $grand_total_encounters  += $docrow['encounters'];

  $docrow['charges']     = 0;
  $docrow['copays']      = 0;
  $docrow['encounters']  = 0;
 }

 $form_facility  = isset($_POST['form_facility']) ? $_POST['form_facility'] : '';
 $form_from_date = fixDate($_POST['form_from_date'], date('Y-m-d'));
 $form_to_date = fixDate($_POST['form_to_date'], date('Y-m-d'));
 
 $selected_fields=$_POST['selectEncColmsData'];
 
// if ($_POST['form_refresh']) {
//  $form_from_date = fixDate($_POST['form_from_date'], date('Y-m-d'));
//  $form_to_date = fixDate($_POST['form_to_date'], "");
//
//  // MySQL doesn't grok full outer joins so we do it the hard way.
//  //
//  $query = "( " .
//   "SELECT " .
//   "e.pc_eventDate, e.pc_startTime, " .
//   "fe.encounter, fe.date AS encdate, " .
//   "f.authorized, " .
//   "p.fname, p.lname, p.pid, p.pubpid, " .
//   "CONCAT( u.lname, ', ', u.fname ) AS docname " .
//   "FROM openemr_postcalendar_events AS e " .
//   "LEFT OUTER JOIN form_encounter AS fe " .
//   "ON fe.date = e.pc_eventDate AND fe.pid = e.pc_pid " .
//   "LEFT OUTER JOIN forms AS f ON f.pid = fe.pid AND f.encounter = fe.encounter AND f.formdir = 'newpatient' " .
//   "LEFT OUTER JOIN patient_data AS p ON p.pid = e.pc_pid " .
//   // "LEFT OUTER JOIN users AS u ON BINARY u.username = BINARY f.user WHERE ";
//   "LEFT OUTER JOIN users AS u ON u.id = fe.provider_id WHERE ";
//  if ($form_to_date) {
//   $query .= "e.pc_eventDate >= '$form_from_date' AND e.pc_eventDate <= '$form_to_date' ";
//  } else {
//   $query .= "e.pc_eventDate = '$form_from_date' ";
//  }
//  if (!empty($form_facility)) {
//   foreach($form_facility as $fac) {  
//   $query .= "AND e.pc_facility = '$fac' ";
//   }
//  }
//  // $query .= "AND ( e.pc_catid = 5 OR e.pc_catid = 9 OR e.pc_catid = 10 ) " .
//  $query .= "AND e.pc_pid != '' AND e.pc_apptstatus != '?' " .
//   ") UNION ( " .
//   "SELECT " .
//   "e.pc_eventDate, e.pc_startTime, " .
//   "fe.encounter, fe.date AS encdate, " .
//   "f.authorized, " .
//   "p.fname, p.lname, p.pid, p.pubpid, " .
//   "CONCAT( u.lname, ', ', u.fname ) AS docname " .
//   "FROM form_encounter AS fe " .
//   "LEFT OUTER JOIN openemr_postcalendar_events AS e " .
//   "ON fe.date = e.pc_eventDate AND fe.pid = e.pc_pid AND " .
//   // "( e.pc_catid = 5 OR e.pc_catid = 9 OR e.pc_catid = 10 ) " .
//   "e.pc_pid != '' AND e.pc_apptstatus != '?' " .
//   "LEFT OUTER JOIN forms AS f ON f.pid = fe.pid AND f.encounter = fe.encounter AND f.formdir = 'newpatient' " .
//   "LEFT OUTER JOIN patient_data AS p ON p.pid = fe.pid " .
//   // "LEFT OUTER JOIN users AS u ON BINARY u.username = BINARY f.user WHERE ";
//   "LEFT OUTER JOIN users AS u ON u.id = fe.provider_id WHERE ";
//  if ($form_to_date) {
//   // $query .= "LEFT(fe.date, 10) >= '$form_from_date' AND LEFT(fe.date, 10) <= '$form_to_date' ";
//   $query .= "fe.date >= '$form_from_date 00:00:00' AND fe.date <= '$form_to_date 23:59:59' ";
//  } else {
//   // $query .= "LEFT(fe.date, 10) = '$form_from_date' ";
//   $query .= "fe.date >= '$form_from_date 00:00:00' AND fe.date <= '$form_from_date 23:59:59' ";
//  }
//  if (!empty($form_facility)) {
//       foreach($form_facility as $fac) {
//           if(!empty($fac)){
//               $fac1.="'$fac'".",";
//               $fac2= rtrim($fac1,",");    
//            }
//       }
//       $query .= "AND fe.facility_id IN ($fac2) ";
//  }
//  $query .= ") ORDER BY docname, pc_eventDate, pc_startTime";
//  echo $query;
//  $res = sqlStatement($query);
// }
?>
<html>
<head>
<?php html_header_show();?>
<link rel="stylesheet" href="<?php echo $css_header;?>" type="text/css">
<script type="text/javascript">

// $('#form_apptcat').multiselect({
//   //buttonClass: 'btn btn-default btn-sm',
//    buttonWidth: 'auto',
//    maxHeight: 50,
//    includeSelectAllOption: true,
//    numberDisplayed:0,
//    selectAllText:'Select All',
//    nonSelectedText: 'form facility1',
//    selectAllValue : 'select all'
// });
// function showPatients()
//{
//    var facility=jQuery('#form_facility1').val();
//    alert(facility);
//  	$.ajax({
//		type: 'POST',
//		url: "appt_sample.php",	
//		data: {  
//                         facility:facility
//                 },
//
//		success: function(response)
//		{
//                   
//		},
//		failure: function(response)
//		{
//			alert("error");
//		}		
//	});	       
//}
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
     //jQuery('#divPatientsList').html('');	
    //jQuery('#divGoogleMap').html('');	
   //    var queryName=jQuery('#selectQueryList').val(); 
 
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
   
   var screen='appt_enc_report';
  
    $.ajax({
            type: 'POST',
            url: "save_fac_query_set.php",	
            data:{
                   screen:screen,querySetName:querySetName,facility_enc:facility_enc,
                   from_date:from_date,to_date:to_date,details:details,selected_fields1:selected_fields1
             },
 
            success: function(response)
            {
                
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
            //facility
                jQuery('#form_facility').val('');
                  var facilityDataArray=facilityData.split(',');     
                     for(var i=0;i<facilityDataArray.length;i++)
                       {      
                           $('#form_facility').find('option').each(function(){
                                if($(this).val()==+ facilityDataArray[i]){
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
//function handleClick(cb)      
//{      
//if(document.theform.form_details.checked == true)      
//{      
//  $('#form_details').attr('checked', true);     
//}      
//}  
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
			<td class='label'>
			   <?php xl('DOS','e'); ?>:
			</td>
			<td>
			   <input type='text' name='form_from_date' id="form_from_date" size='10' value='<?php  echo $form_from_date; ?>'
				title='Date of appointments mm/dd/yyyy' >
			   <img src='../../pic/show_calendar.gif' align='absbottom' width='24' height='22'
				id='img_from_date' border='0' alt='[?]' style='cursor:pointer'
				title='<?php xl('Click here to choose a date','e'); ?>'>
			</td>
                       
			<td class='label'>
			   <?php xl('To','e'); ?>:
			</td>
			<td>
			   <input type='text' name='form_to_date' id="form_to_date" size='10' value='<?php  echo $form_to_date; ?>'
				title='Optional end date mm/dd/yyyy' >
			   <img src='../../pic/show_calendar.gif' align='absbottom' width='24' height='22'
				id='img_to_date' border='0' alt='[?]' style='cursor:pointer'
				title='<?php xl('Click here to choose a date','e'); ?>'>
			</td>
                        
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>
                            <input type='checkbox' name='form_details' id='form_details' 
                            value='1'  <?php if ($_POST['form_details']) echo " checked";?>><?php xl('Details','e') ?>
                             
			</td>
                        <td>
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
                                                   
                                                    'e.pc_eventDate AS Date'=> 'Date',
//                                                    'fe.encounter AS Encounter'=> 'Encounter', 
                                                    'Facility'=>'Facility',
                                                    'visit_cat'=>'Visit Category',
                                                    'patient'=>'Patient',
                                                    'p.pubpid AS ID'=> 'ID',
                                                    'cpo_log' => 'CPO Log',
                                                    'ccm_log'=> 'CCM Log',
                                                    'billing_note'=>'Billing Note'
                                                     );
                                                 $static=array('billed'=> 'Billed',
                                                                'error'=> 'Error',);
                                                  $fields_dy=array_merge($fields_array,$filterfields);
                                                  $fields=array_merge($fields_dy,$static);
                                                  ?>
                                        <div class="lblSelect"><b>Columns:</b></div> 
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
//                                                  'e.pc_eventDate AS Date'=> 'Date/Appt',
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
<table>

 <thead>
<!--  <th> &nbsp;<?php  xl('Practitioner','e'); ?> </th>
  <th> &nbsp;<?php  xl('Date/Appt','e'); ?> </th>
  <th> &nbsp;<?php  xl('Patient','e'); ?> </th>
  <th> &nbsp;<?php  xl('ID','e'); ?> </th>
  <th align='right'> <?php  xl('Chart','e'); ?>&nbsp; </th>
  <th align='right'> <?php  xl('Encounter','e'); ?>&nbsp; </th>
  <th align='right'> <?php  xl('Charges','e'); ?>&nbsp; </th>
  <th align='right'> <?php  xl('Copays','e'); ?>&nbsp; </th>
  <th> <?php  xl('Billed','e'); ?> </th>
  <th> &nbsp;<?php  xl('Error','e'); ?> </th>-->
 </thead>
 <tbody>
<?php

//if ($_POST['form_refresh']) {
  $form_from_date = fixDate($_POST['form_from_date'], date('Y-m-d'));
  $form_to_date = fixDate($_POST['form_to_date'], "");

  // MySQL doesn't grok full outer joins so we do it the hard way.
  //
  
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
    $pos1=strpos($fieldval1, 'Date');
    //$practitioner='';
//    if($pos!=''){
//        $practitioner='Practitioner,';
//    }
    $date='';
    if($pos1!=''){
        $date='Date,';
    }
   
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
       if($fac!=''){    
       $query .= "AND e.pc_facility = '$fac' ";
       }
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
       foreach($form_facility as $fac) {
           if(!empty($fac)){
              
               $fac1.="'$fac'".",";
               $fac2= rtrim($fac1,",");   
               $query .= "AND fe.facility_id IN ($fac2) ";
            }
       }
   }
  $query .= ") ORDER BY practitioner, $date pc_startTime";
 
  $res = sqlStatement($query);
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
       if($fac!=''){    
       $query .= "AND e.pc_facility = '$fac' ";
       }
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
       foreach($form_facility as $fac) {
           if(!empty($fac)){
               $fac1.="'$fac'".",";
               $fac2= rtrim($fac1,",");  
               $query .= "AND fe.facility_id IN ($fac2) ";
            }
       }
     }
  $query .= ") ORDER BY practitioner, Date, pc_startTime";
 
  $res = sqlStatement($query);
      
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

   if ($docname != $docrow['Practitioner']) {
    endDoctor($docrow);
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
         
           print("<thead><tr>");
//            echo "<pre>"; print_r($row['Practitioner']); echo "</pre>";
//            echo "<pre>"; print_r($selected_fields); echo "</pre>";
		foreach($row as $key => $value) {
                     if($key!='pc_startTime' && $key!='encdate' && $key!='authorized'){
                         if($key=='cpo_data'){
                             print("<th>CPO Log</th>");
                         }else if($key=='ccm_data'){
                              print("<th>CCM Log</th>");
                         }else {
                          $header=str_replace("_"," ",$key);   
                          print("<th>$header</th>");
                         }
                     }
//                   if($key=='pc_startTime') {
//                        print("<th>Charges</th>");
//                   } else if($key=='encdate'){
//                        print("<th>copays</th>");
//                   } else if($key=='authorized'){
//                        print("<th>Billed</th>");
//                   } else if($key=='u.lname'){
//                       print("<th>Error</th>");
//                   }else {
//                   print("<th>$key</th>");
//                   }
                }
                print("<th>Charges</th>");
                print("<th>Copays</th>");
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
                  if($key!='pc_startTime' && $key!='encdate' && $key!='authorized'){
                        if($key=='cpo_data'){
                             print("<th>CPO Log</th>");
                         }else if($key=='ccm_data'){
                              print("<th>CCM Log</th>");
                         }else {
                          print("<th>$key</th>");
                         }
                     }
                }
                print("<th>Charges</th>");
                print("<th>Copays</th>");
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
                    if($key!='pc_startTime' && $key!='encdate' && $key!='authorized'){
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
                       }else {
                        print("<td>$row[$key]</td>\n");
                       }
                     }
                   
//                       if($key=='Date') { 
//                           if (empty($row['Date'])) {
//                               $date_val= oeFormatShortDate(substr($row['encdate'], 0, 10));
//                               print("<td>$date_val</td>\n");
//                            }
//                            else {
//                              $date_val= oeFormatShortDate($row['Date']) . ' ' . substr($row['pc_startTime'], 0, 5);
//                               print("<td>$date_val</td>\n");
//                            }
//                       } else if($key=='pc_startTime'){
//                           print("<td>$charges</td>\n");
//                       } else if($key=='encdate'){
//                            print("<td>$copays</td>\n");
//                       } else if($key=='encdate'){
//                           print("<td>$billed</td>\n");
//                       } else if($key=='u.lname'){
//                            print("<td>$errmsg</td>\n");
//                       }
//                       else {
//                            print("<td>$row[$key]</td>\n");
//                       } 
                   
		}
                
                echo "<td>"; bucks($charges); echo"</td>";
                echo "<td>"; bucks($copays); echo"</td>";
                
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
<!-- <tr>
  <td>
   &nbsp;<?php  echo ($docname == $docrow['Practitioner']) ? "" : $docname ?>
  </td>
  <td>
   &nbsp;<?php
    /*****************************************************************
    if ($form_to_date) {
        echo $row['pc_eventDate'] . '<br>';
        echo substr($row['pc_startTime'], 0, 5);
    }
    *****************************************************************/
//    if (empty($row['pc_eventDate'])) {
//      echo oeFormatShortDate(substr($row['encdate'], 0, 10));
//    }
//    else {
//      echo oeFormatShortDate($row['pc_eventDate']) . ' ' . substr($row['pc_startTime'], 0, 5);
//    }
    ?>
  </td>
  <td>
   &nbsp;<?php  //echo $row['fname'] . " " . $row['lname']  
   echo $row['Patient'];?>
  </td>
  <td>
   &nbsp;<?php  echo $row['ID'] ?>
  </td>
  <td align='right'>
   <?php  echo $row['Chart'] ?>&nbsp;
  </td>
  <td align='right'>
   <?php  echo $encounter ?>&nbsp;
  </td>
  <td align='right'>
   <?php // bucks($charges) ?>&nbsp;
  </td>
  <td align='right'>
   <?php // bucks($copays) ?>&nbsp;
  </td>
  <td>
   <?php // echo $billed ?>
  </td>
  <td style='color:#cc0000'>
   <?php //echo $errmsg; ?>&nbsp;
  </td>
 </tr>-->
<?php
   } // end of details line
   else {
       if($i=='1'){
            print("<thead><tr>");
                print("<th>&nbsp;</th>");
		print("<th>Encounter</th>");
                print("<th>Charges</th>");
                print("<th>Copays</th>");
            print("</tr></thead>\n");
	    print("<tfoot><tr>");
	        print("<th>&nbsp;</th>");
		print("<th>Encounter</th>");
                print("<th>Charges</th>");
                print("<th>Copays</th>");
            print("</tr></tfoot>\n");
       }      
   }
   $docrow['Practitioner'] = $docname;
  } // end of row
//echo "<pre>"; print_r($data); echo "</pre>";
  endDoctor($docrow);

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
  //echo "  <td colspan=''>\n";
 // echo "   &nbsp;\n";
  //echo "  </td>\n";
  echo " </tr>\n";
	print("</table>\n"); 
//}   
  // display_db_query($res);
}
?>
</tbody>
</table>
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
//            
//            $(document).ready( function () {
//                $('#encounter_report').DataTable( {
//                    dom: 'lfrtip',
//                    
//                } );
//            } );

jQuery(document).ready(function($) {
    // Setup - add a text input to each footer cell
    $('#appt_enc_reports tfoot th').each( function () {
        var title = $('#appt_enc_reports thead th').eq( $(this).index() ).text();
        $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
    } );
 
    // DataTable
    var table = $('#appt_enc_reports').DataTable({ "iDisplayLength": 100});
 
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