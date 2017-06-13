<?php
// Copyright (C) 2007-2010 Rod Roark <rod@sunsetsystems.com>
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.

// This report shows past encounters with filtering and sorting.

require_once("../../globals.php");
require_once("$srcdir/forms.inc");
require_once("$srcdir/billing.inc");
require_once("$srcdir/patient.inc");
require_once("$srcdir/formatting.inc.php");
require_once "$srcdir/options.inc.php";
require_once "$srcdir/formdata.inc.php";

$alertmsg = ''; // not used yet but maybe later

// For each sorting option, specify the ORDER BY argument.
//
$ORDERHASH = array(
  'doctor'  => 'lower(u.lname), lower(u.fname), fe.date',
  'patient' => 'lower(p.lname), lower(p.fname), fe.date',
  'pubpid'  => 'lower(p.pubpid), fe.date',
  'time'    => 'fe.date, lower(u.lname), lower(u.fname)',
);

function bucks($amount) {
  if ($amount) printf("%.2f", $amount);
}

function show_doc_total($lastdocname, $doc_encounters) {
  if ($lastdocname) {
    echo " <tr>\n";
    echo "  <td class='detail'>$lastdocname</td>\n";
    echo "  <td class='detail' align='right'>$doc_encounters</td>\n";
    echo " </tr>\n";
  }
}

$form_from_date = fixDate($_POST['form_from_date'], date('Y-m-d'));
$form_to_date = fixDate($_POST['form_to_date'], date('Y-m-d'));
$form_provider  = $_POST['form_provider'];
$form_facility  = $_POST['form_facility'];
$form_details   = $_POST['form_details'] ? true : false;
$form_new_patients = $_POST['form_new_patients'] ? true : false;
$form_selected_fields=$_POST['selectEncColmsData'];
$form_orderby = $ORDERHASH[$_REQUEST['form_orderby']] ?
  $_REQUEST['form_orderby'] : 'doctor';
$orderby = $ORDERHASH[$form_orderby];

//facility lidt
function facility_list1($selected = '', $name = 'form_facility[]', $id='form_facility', $allow_unspecified = true, $allow_allfacilities = true) {
  $sel_value=explode("|",$selected);
  $have_selected = false;
  $query = "SELECT id, name FROM facility ORDER BY name";
  $fres = sqlStatement($query);

  $name = htmlspecialchars($name, ENT_QUOTES);
  echo "   <select name=\"$name\" multiple id=\"$id\"  >\n";

  if ($allow_allfacilities) {
    $option_value = '';
    $option_selected_attr = '';	
    foreach($sel_value as $value){
    if ($value == '') {
      $option_selected_attr = ' selected="selected"';
      $have_selected = true;
    }
    }
    $option_content = htmlspecialchars('-- ' . xl('All Facilities') . ' --', ENT_NOQUOTES);
    echo "    <option value=\"$option_value\" $option_selected_attr>$option_content</option>\n";
  } elseif ($allow_unspecified) {
  	$option_value = '0';
    $option_selected_attr = '';
    foreach($sel_value as $value){
    if ( $value == '0' ) {
      $option_selected_attr = ' selected="selected"';
      $have_selected = true;
    }
    }
    $option_content = htmlspecialchars('-- ' . xl('Unspecified') . ' --', ENT_NOQUOTES);
    echo "    <option value=\"$option_value\" $option_selected_attr>$option_content</option>\n";
    }
  
  while ($frow = sqlFetchArray($fres)) {
    $facility_id = $frow['id'];
    $option_value = htmlspecialchars($facility_id, ENT_QUOTES);
    $option_selected_attr = '';
    foreach($sel_value as $value){
    if ($value == $facility_id) {
      $option_selected_attr = ' selected="selected"';
      $have_selected = true;
    }
    }
    $option_content = htmlspecialchars($frow['name'], ENT_NOQUOTES);
    echo "    <option value=\"$option_value\" $option_selected_attr>$option_content</option>\n";
  }

  if ($allow_unspecified && $allow_allfacilities) {
    $option_value = '0';
    $option_selected_attr = '';
    foreach($sel_value as $value){
    if ( $value == '0' ) {
      $option_selected_attr = ' selected="selected"';
      $have_selected = true;
    }
    }
    $option_content = htmlspecialchars('-- ' . xl('Unspecified') . ' --', ENT_NOQUOTES);
    echo "    <option value=\"$option_value\" $option_selected_attr>$option_content</option>\n";
  }

  if (!$have_selected) {
    foreach($sel_value as $value) { 
    $option_value = htmlspecialchars($selected, ENT_QUOTES);
    $option_label = htmlspecialchars('(' . xl('Do not change') . ')', ENT_QUOTES);
    $option_content = htmlspecialchars(xl('Missing or Invalid'), ENT_NOQUOTES);
    echo "    <option value='$option_value' label='$option_label' selected='selected'>$option_content</option>\n";
    }
  }
  echo "   </select>\n";
}


?>
<html>
<head>
<?php html_header_show();?>
<title><?php xl('Encounters Report','e'); ?></title>

<style type="text/css">@import url(../../../library/dynarch_calendar.css);</style>

<link rel=stylesheet href="<?php echo $css_header;?>" type="text/css">
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
     var screen='enc_report';
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
   var facility_encounter=facility+" ";
   var provider=jQuery('#form_provider').val();
   var provider_encounter=provider+" ";
   var enc_from_date=jQuery('#form_from_date').val();
   var enc_to_date=jQuery('#form_to_date').val();
  // var enc_new=jQuery('#form_new_patients').val();
   var enc_new_val= $("#form_new_patients").attr("checked") ? "Checked" : "Unchecked";
   
    if(enc_new_val=="Checked"){
       enc_new='1'; }
   else if(enc_new_val=="Unchecked"){
      enc_new='0';
   }
   var enc_details_val= $("#form_details").attr("checked") ? "Checked" : "Unchecked";
   
   if(enc_details_val=="Checked"){
       enc_details='1'; }
   else if(enc_details_val=="Unchecked"){
      enc_details='0';
   }
   var enc_selected_fields=jQuery('#selectEncColmsData').val();
   var enc_selected_fields1=enc_selected_fields+" ";
   //alert(enc_selected_fields);
   var screen='enc_report';
    $.ajax({
            type: 'POST',
            url: "save_fac_query_set.php",	
            data:{
                   screen:screen,querySetName:querySetName,facility_encounter:facility_encounter,provider_encounter:provider_encounter,
                   enc_from_date:enc_from_date,enc_to_date:enc_to_date,enc_new:enc_new,enc_details:enc_details,enc_selected_fields1:enc_selected_fields1
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
  var screen='enc_report';
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
                var providerData=setArray[1];
                var from_dt=setArray[2];
                var to_dt=setArray[3];
                var details1=setArray[4];
                var new1=setArray[5];
                var sel_fields=setArray[6];
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
                //provider
                jQuery('#form_provider').val('');
                  var providerArray=providerData.split(',');     
                     for(var i=0;i<providerArray.length;i++)
                       {      
                           $('#form_provider').find('option').each(function(){
                                if($(this).val()==+ providerArray[i]){
                                    $(this).attr('selected','selected');
                                }
                             });
                       }
                    
            //from_date
                 jQuery('#form_from_date').val(from_dt);
            //to_date      
                jQuery('#form_to_date').val(to_dt);  
            //details
                if(details1==1){
                  $('#form_details').attr('checked', 1);
                }
             //new
              if(new1==1)
                  $('#form_new_patients').attr('checked', 1);
              
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
     var screen='enc_report';
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

<script type="text/javascript" src="../../../library/textformat.js"></script>
<script type="text/javascript" src="../../../library/dialog.js"></script>
<script type="text/javascript" src="../../../library/dynarch_calendar.js"></script>
<?php include_once("{$GLOBALS['srcdir']}/dynarch_calendar_en.inc.php"); ?>
<script type="text/javascript" src="../../../library/dynarch_calendar_setup.js"></script>
<script type="text/javascript" src="../../../library/js/jquery.1.3.2.js"></script>

<script LANGUAGE="JavaScript">

 var mypcc = '<?php echo $GLOBALS['phone_country_code'] ?>';

 function dosort(orderby) {
  var f = document.forms[0];
  f.form_orderby.value = orderby;
  f.submit();
  return false;
 }

 function refreshme() {
  document.forms[0].submit();
 }

</script>
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
    
</head>
<body class="body_top">
<!-- Required for the popup date selectors -->
<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>

<span class='title'><?php xl('Report','e'); ?> - <?php xl('Encounters','e'); ?></span>

<div id="report_parameters_daterange">
<?php echo date("d F Y", strtotime($form_from_date)) ." &nbsp; to &nbsp; ". date("d F Y", strtotime($form_to_date)); ?>
</div>

<form method='post' name='theform' id='theform' action='enc_report.php'>

<div id="report_parameters">
<table>
 <tr>
  <td width='600px'>
	<div style='float:left'>

	<table class='text'>
		<tr>
			<td class='label'>
				<?php xl('Facility','e'); ?>:
			</td>
			
			  <td><?php $facility1=implode("|",$form_facility);   facility_list1(strip_escape_custom($facility1), 'form_facility[]' ,'form_facility',true); ?>
			</td>
			<td class='label'>
			   <?php xl('Provider','e'); ?>:
			</td>
			<td>
				<?php

				 // Build a drop-down list of providers.
				 //

				 $query = "SELECT id, lname, fname FROM users WHERE ".
				  "authorized = 1 $provider_facility_filter ORDER BY lname, fname"; //(CHEMED) facility filter

				 $ures = sqlStatement($query);

				echo "   <select name='form_provider[]' multiple id='form_provider'>\n";
				echo "    <option value=''"; foreach($form_provider as $val1) { if ($val1=='') echo " selected";} 
                                echo  "selected"; echo" >-- " . xl('All') . " --\n";


				 while ($urow = sqlFetchArray($ures)) {
				  $provid = $urow['id'];
				  echo "    <option value='$provid'";
                                  foreach($form_provider as $val2){
                                  if ($provid == $val2) echo " selected"; }
				  echo ">" . $urow['lname'] . ", " . $urow['fname'] . "\n";
				 }

				 echo "   </select>\n";

				?>
			</td>
                         
			<td>
        <input type='checkbox' name='form_new_patients' id='form_new_patients' title='First-time visits only'<?php  if ($form_new_patients) echo ' checked'; ?>>
        <?php  xl('New','e'); ?>
			</td>
		</tr>
		<tr>
			<td class='label'>
			   <?php xl('From','e'); ?>:
			</td>
			<td>
			   <input type='text' name='form_from_date' id="form_from_date" size='10' value='<?php echo $form_from_date ?>'
				onkeyup='datekeyup(this,mypcc)' onblur='dateblur(this,mypcc)' title='yyyy-mm-dd'>
			   <img src='../../pic/show_calendar.gif' align='absbottom' width='24' height='22'
				id='img_from_date' border='0' alt='[?]' style='cursor:pointer'
				title='<?php xl('Click here to choose a date','e'); ?>'>
			</td>
			<td class='label'>
			   <?php xl('To','e'); ?>:
			</td>
			<td>
			   <input type='text' name='form_to_date' id="form_to_date" size='10' value='<?php echo $form_to_date ?>'
				onkeyup='datekeyup(this,mypcc)' onblur='dateblur(this,mypcc)' title='yyyy-mm-dd'>
			   <img src='../../pic/show_calendar.gif' align='absbottom' width='24' height='22'
				id='img_to_date' border='0' alt='[?]' style='cursor:pointer'
				title='<?php xl('Click here to choose a date','e'); ?>'>
			</td>
			<td>
			   <input type='checkbox'  id='form_details' name='form_details'<?php  if ($form_details) echo ' checked'; ?>>
			   <?php  xl('Details','e'); ?>
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
                                                     $field_key=$row1['field_id']." "."AS"." "."'$title'";
                                                     $filterfields[$field_key]=$row1['field_id'];
                                                 }
                                                 $fields_array = array('provider'=> 'Provider',
                                                    'fe.date AS Date'=> 'Date',
                                                    'patient'=>'Patient',
                                                    'p.pubpid AS ID'=> 'ID',
                                                    'fe.reason AS Encounter'=>'Encounter',
                                                     );
                                                  $fields=array_merge($fields_array,$filterfields);
                                                  ?>
                                        <div class="lblSelect"><b>Standard Data:</b></div> 
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
                                                     ?><option value="<?php echo  str_replace('.', '-',str_replace(' ', '--', $key));  ?>" <?php foreach($form_selected_fields as $val) { if($val==$option_val) echo "selected"; }?> > <?php echo $value; ?></option><?php 
                                                }
                                                echo "</select>";
                                                echo "<br><br>";
                                                
                                $fields1 = array( 'f.formdir' => '',
                                                  'f.form_name'=>'',
                                                  'p.pid'=> '',
                                                  'fe.encounter'=>'',
                                                );
                              
                                foreach($fields1 as $key => $value){
                                     $fieldset3 .= $key.",";
                                }   
                                $fieldsets = rtrim($fieldset3, ',');
                                $fieldparamters1 = $fieldsets; 
    
                                foreach($fields as $key1 => $value1){
                                   if($key1=='provider') {
                                          $fieldset2 .= 'CONCAT(u.lname ," " ,u.fname) AS provider,';
                                     } else if($key1=='patient'){
                                          $fieldset2.='CONCAT(p.lname," ", p.fname," ", p.mname) AS Patient,';
                                     }else {
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
                    <td class='label'>
                       <?php xl('QuerySets','e'); ?>:
                    </td>
                    <td style="padding-bottom: 0px !important; padding-top: 15px !important;">
                            <div class='divSelect'>
                                <select id='selectQuerySet' onchange="javascript:setByQuerySet();" class="btn btn-default"  style="text-align:left;">
                                    <option value='-1'>---Select---</option>
                                <?php

                                    $getQuerySets=sqlStatement("SELECT id,setname FROM tbl_allcarereports_querysets where screen='enc_report'");
                                    while ($rowQuerySets = sqlFetchArray($getQuerySets)) 
                                    {
                                        echo "<option value='".$rowQuerySets['setname']."'>".$rowQuerySets['setname']."</option>";
                                    }  

                                ?>
                                </select>

                            </div>  <br> 
                     </td> 
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

					<?php if ($_POST['form_refresh'] || $_POST['form_orderby'] ) { ?>
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

</div> <!-- end report_parameters -->

<?php
 if ($_POST['form_refresh'] || $_POST['form_orderby']) {
?>
<div id="report_results">
<table>

 <thead>
<?php if ($form_details) {  ?>
     
<!--  <th>
   <a href="nojs.php" onclick="return dosort('doctor')"
   <?php if ($form_orderby == "doctor") echo " style=\"color:#00cc00\"" ?>><?php  xl('Provider','e'); ?> </a>
  </th>
  <th>
   <a href="nojs.php" onclick="return dosort('time')"
   <?php if ($form_orderby == "time") echo " style=\"color:#00cc00\"" ?>><?php  xl('Date','e'); ?></a>
  </th>
  <th>
   <a href="nojs.php" onclick="return dosort('patient')"
   <?php if ($form_orderby == "patient") echo " style=\"color:#00cc00\"" ?>><?php  xl('Patient','e'); ?></a>
  </th>
  <th>
   <a href="nojs.php" onclick="return dosort('pubpid')"
   <?php if ($form_orderby == "pubpid") echo " style=\"color:#00cc00\"" ?>><?php  xl('ID','e'); ?></a>
  </th>
  <th>
   <?php  xl('Status','e'); ?>
  </th>
  <th>
   <?php  xl('Encounter','e'); ?>
  </th>
  <th>
   <?php  xl('Form','e'); ?>
  </th>
  <th>
   <?php  xl('Coding','e'); ?>
  </th>-->
<?php } else { ?>
<!--  <th><?php  xl('Provider','e'); ?></td>
  <th><?php  xl('Encounters','e'); ?></td>-->
<?php } ?>
 </thead>
 <tbody>
<?php
// Get the info.
$form_from_date = fixDate($_POST['form_from_date'], date('Y-m-d'));
$form_to_date = fixDate($_POST['form_to_date'], date('Y-m-d'));
$form_provider  = $_POST['form_provider'];
$form_facility  = $_POST['form_facility'];
$form_details   = $_POST['form_details'] ? true : false;
$form_new_patients = $_POST['form_new_patients'] ? true : false;
$form_selected_fields=$_POST['selectEncColmsData'];
$form_orderby = $ORDERHASH[$_REQUEST['form_orderby']] ?
  $_REQUEST['form_orderby'] : 'doctor';
$orderby = $ORDERHASH[$form_orderby];
//print_r($form_selected_fields);
//$query1 = "SELECT " .
//  "fe.encounter, fe.date, fe.reason, " .
//  "f.formdir, f.form_name, " .
//  "p.fname, p.mname, p.lname, p.pid, p.pubpid, " .
//  "u.lname AS ulname, u.fname AS ufname, u.mname AS umname " .
//  "FROM ( form_encounter AS fe, forms AS f ) " .
//  "LEFT OUTER JOIN patient_data AS p ON p.pid = fe.pid " .
//  "LEFT JOIN users AS u ON u.id = fe.provider_id " .
//  "WHERE f.encounter = fe.encounter AND f.formdir = 'newpatient' ";
echo "<br>";

if($form_selected_fields[0]!='-1' && !empty($_POST['selectEncColmsData'])) {
    foreach($form_selected_fields as $key=> $value){
       
         if($value=='provider') {
              $field_val .= 'CONCAT(u.lname ," " ,u.fname) AS provider,';
         } else if($value=='patient'){
              $field_val.='CONCAT(p.lname," ", p.fname," ", p.mname) AS Patient,';
         }else {
              $field_val .= str_replace('-', '.',str_replace('--', ' ', $value)).",";
         }
    }
    
    $fieldval1=rtrim($field_val,",");
    ///$filed_param=$field_val1.$field_val2.$fieldval1;
    $query = "SELECT " ." ".$fieldval1.",".$fieldparamters1." ".
       "FROM ( form_encounter AS fe, forms AS f ) " .
      "LEFT OUTER JOIN patient_data AS p ON p.pid = fe.pid " .
      "LEFT JOIN users AS u ON u.id = fe.provider_id " .
      "WHERE f.encounter = fe.encounter AND f.formdir = 'newpatient' ";
    if ($form_to_date) {
      $query .= "AND fe.date >= '$form_from_date 00:00:00' AND fe.date <= '$form_to_date 23:59:59' ";
    } else {
      $query .= "AND fe.date >= '$form_from_date 00:00:00' AND fe.date <= '$form_from_date 23:59:59' ";
    }
    if ($form_provider) {
      foreach($form_provider as $pro)  {
          if(!empty($pro)) {
               $pro1.="'$pro'".",";
               $pro2= rtrim($pro1,",");   
               $query .= "AND fe.provider_id IN ($pro2) ";
            }
      }
     }
    if (!empty($form_facility)) {
      foreach($form_facility as $fac)  {
          if(!empty($fac)){    
              $fac1.="'$fac'".",";
              $fac2= rtrim($fac1,",");  
              $query .= "AND fe.facility_id IN ($fac2) ";
           }
        }
    }
    if ($form_new_patients) {
      $query .= "AND fe.date = (SELECT MIN(fe2.date) FROM form_encounter AS fe2 WHERE fe2.pid = fe.pid) ";
    }
    //$query .= "ORDER BY $orderby";
    
    $res = sqlStatement($query);
} else {
    
    $query = "SELECT " ." ".$fieldparamters.",".$fieldparamters1." ".
       "FROM ( form_encounter AS fe, forms AS f ) " .
      "LEFT OUTER JOIN patient_data AS p ON p.pid = fe.pid " .
      "LEFT JOIN users AS u ON u.id = fe.provider_id " .
      "WHERE f.encounter = fe.encounter AND f.formdir = 'newpatient' ";
    if ($form_to_date) {
        
      $query .= "AND fe.date >= '$form_from_date 00:00:00' AND fe.date <= '$form_to_date 23:59:59' ";
    } else {
      $query .= "AND fe.date >= '$form_from_date 00:00:00' AND fe.date <= '$form_from_date 23:59:59' ";
    }
    if ($form_provider) {
      foreach($form_provider as $pro)  {
          if(!empty($pro)) {
               $pro1.="'$pro'".",";
               $pro2= rtrim($pro1,",");   
               $query .= "AND fe.provider_id IN ($pro2) ";
            }
      }
    }
    if (!empty($form_facility)) {
      foreach($form_facility as $fac)  {
          if(!empty($fac)){    
              $fac1.="'$fac'".",";
              $fac2= rtrim($fac1,",");  
              $query .= "AND fe.facility_id IN ($fac2) ";
           }
        }
    }
    if ($form_new_patients) {
      $query .= "AND fe.date = (SELECT MIN(fe2.date) FROM form_encounter AS fe2 WHERE fe2.pid = fe.pid) ";
    }
    //$query .= "ORDER BY $orderby";
    
    $res = sqlStatement($query);
    
}


if ($res) {
     
     
  //function display_db_query($res,$form_details) {
           
           echo "<br/>";
                    print("<TABLE border='1' id='encounter_report' class='display'>\n");
          $lastdocname = "";
          $doc_encounters = 0;   
          $i=0;
          while ($row = sqlFetchArray($res)) {
             $i++;
             $patient_id = $row['pid'];

            $docname = '';
//            if (!empty($row['ulname']) || !empty($row['ufname'])) {
//              $docname = $row['ulname'];
//              if (!empty($row['ufname']) || !empty($row['umname']))
//                $docname .= ', ' . $row['ufname'] . ' ' . $row['umname'];
//            }
           
            if ($row['provider']) {
             $docname = $row['provider'];
            }
            
            $errmsg  = "";

              $encnames = '';      
              $encarr = getFormByEncounter($patient_id, $row['encounter'],
                "formdir, user, form_name, form_id");
              if($encarr!='') {
                      foreach ($encarr as $enc) {
                        if ($enc['formdir'] == 'newpatient') continue;
                        if ($encnames) $encnames .= '<br />';
                        $encnames .= $enc['form_name'];
                      }
              }     

              // Fetch coding and compute billing status.
              $coded = "";
              $billed_count = 0;
              $unbilled_count = 0;
              if ($billres = getBillingByEncounter($row['pid'], $row['encounter'],
                "code_type, code, code_text, billed"))
              {
                foreach ($billres as $billrow) {
                  // $title = addslashes($billrow['code_text']);
                  if ($billrow['code_type'] != 'COPAY' && $billrow['code_type'] != 'TAX') {
                    $coded .= $billrow['code'] . ', ';
                    if ($billrow['billed']) ++$billed_count; else ++$unbilled_count;
                  }
                }
                $coded = substr($coded, 0, strlen($coded) - 2);
              }

              // Figure product sales into billing status.
              $sres = sqlStatement("SELECT billed FROM drug_sales " .
                "WHERE pid = '{$row['pid']}' AND encounter = '{$row['encounter']}'");
              while ($srow = sqlFetchArray($sres)) {
                if ($srow['billed']) ++$billed_count; else ++$unbilled_count;
              }

              // Compute billing status.
              if ($billed_count && $unbilled_count) $status = xl('Mixed' );
              else if ($billed_count              ) $status = xl('Closed');
              else if ($unbilled_count            ) $status = xl('Open'  );
              else                                  $status = xl('Empty' );

              if ($form_details) {
                 
                if($i=='1'){
                    print("<thead><tr>");
                                foreach($row as $key => $value) {
                                     if($key!='formdir' && $key!='form_name' && $key!='pid' && $key!='encounter' ){
                                        print("<th>$key</th>");
                                     } 
                                     else if($key=='formdir') {
                                          print("<th>Status</th>");
                                     }
                                     else if($key=='form_name'){
                                          print("<th>Form</th>");
                                     } else if($key=='encounter') {
                                         print("<th>Coding</th>");
                                     }
                                }
                                print("</tr></thead>\n");
                                print("<tfoot><tr>");
                                  foreach($row as $key => $value) {
                                     if($key!='formdir' && $key!='form_name' && $key!='pid' && $key!='encounter' ){
                                        print("<th>$key</th>");
                                     } else if($key=='formdir') {
                                          print("<th>Status</th>");
                                     }
                                     else if($key=='form_name'){
                                          print("<th>Form</th>");
                                     } else if($key=='encounter') {
                                         print("<th>Coding</th>");
                                     }
                                }
                               print("</tr></tfoot>");
                           } 
                 
              // Fetch all other forms for this encounter.
               print("<tr align=left valign=top>");
                foreach($row as $key=> $value) {
                      if($key!='formdir' && $key!='form_name' && $key!='pid' && $key!='encounter' ){
                        print("<td>$row[$key]</td>\n");
                      } else if($key=='formdir'){
                            print("<td>$status</td>\n"); 
                      }
                      else if($key=='form_name'){
                         print("<td>$encnames</td>\n"); 
                      } else if($key=='encounter') {
                          print("<td>$coded</td>\n"); 
                      }
                }
                print("</tr>\n");
                ?>
<!--                 <tr bgcolor='<?php echo $bgcolor ?>'>
                  <td>
                   <?php //echo ($docname == $lastdocname) ? "" : $docname ?>&nbsp;
                    <?php echo $row['provider']; ?>&nbsp;
                  </td>
                  <td>
                   <?php echo oeFormatShortDate(substr($row['Date'], 0, 10)) ?>&nbsp;
                  </td>
                  <td>
                   <?php //echo $row['lname'] . ', ' . $row['fname'] . ' ' . $row['mname']; ?>&nbsp;
                    <?php echo rtrim($row['Patient'],","); ?>&nbsp;
                  </td>
                  <td>
                   <?php echo $row['ID']; ?>&nbsp;
                  </td>
                  <td>
                   <?php echo $status; ?>&nbsp;
                  </td>
                  <td>
                   <?php echo $row['Encounter']; ?>&nbsp;
                  </td>
                  <td>
                   <?php echo $encnames; ?>&nbsp;
                  </td>
                  <td>
                   <?php echo $coded; ?>
                  </td>
                 </tr>-->
        <?php
            } else {
                if($i=='1'){
                  print("<thead><tr>");
                         print("<th>Provider</th>");
                         print("<th>Encounters</th>");
                  print("</tr></thead>\n");
                  print("<tfoot><tr>");
                         print("<th>Provider</th>");
                         print("<th>Encounters</th>");
                 print("</tr></tfoot>");
                }                  
              if ($docname != $lastdocname) {
                show_doc_total($lastdocname, $doc_encounters);
                $doc_encounters = 0;
              }
              ++$doc_encounters;
            }
            $lastdocname = $docname;
           
          }
          if (!$form_details) { show_doc_total($lastdocname, $doc_encounters);  }
    print("</table>\n"); 
//} 
             //display_db_query($res,$form_details);
  
}
?>
</tbody>
</table>
</div>  <!-- end encresults -->
<?php } else { ?>
<div class='text'>
 	<?php echo xl('Please input search criteria above, and click Submit to view results.', 'e' ); ?>
</div>
<?php } ?>

<input type="hidden" name="form_orderby" value="<?php echo $form_orderby ?>" />
<input type='hidden' name='form_refresh' id='form_refresh' value=''/>

</form>
<script type='text/javascript'>
//            
//            $(document).ready( function () {
//                $('#encounter_report').DataTable( {
//                    dom: 'lfrtip',
//                    
//                } );
//            } );

$(document).ready(function() {
    // Setup - add a text input to each footer cell
    $('#encounter_report tfoot th').each( function () {
        var title = $('#encounter_report thead th').eq( $(this).index() ).text();
        $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
    } );
 
    // DataTable
    var table = $('#encounter_report').DataTable({ "iDisplayLength": 100});
 
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

<script language='JavaScript'>
 Calendar.setup({inputField:"form_from_date", ifFormat:"%Y-%m-%d", button:"img_from_date"});
 Calendar.setup({inputField:"form_to_date", ifFormat:"%Y-%m-%d", button:"img_to_date"});

<?php if ($alertmsg) { echo " alert('$alertmsg');\n"; } ?>

</script>

</html>
