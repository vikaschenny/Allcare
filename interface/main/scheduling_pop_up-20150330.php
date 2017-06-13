<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

require_once("../globals.php");
require_once("../../library/formdata.inc.php");
require_once("../../library/globals.inc.php");

?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta name="HandheldFriendly" content="true">

<style>
/* Tablet Landscape */
@media screen and (max-width: 1060px) {
#primary { width:67%; }
#secondary { width:30%; margin-left:3%;} 
}
 
/* Tabled Portrait */
@media screen and (max-width: 768px) {
#primary { width:100%; }
#secondary { width:100%; margin:0; border:none; }
}

html { font-size:100%;background-color:#FFFFCC;font-family: sans-serif; }

@media (min-width: 640px) { body {font-size:1rem;} }
@media (min-width:960px) { body {font-size:1.1rem;} }
@media (min-width:1100px) { body {font-size:1.2rem;} }

.lblSelect{min-width:180px;float:left;width:15%;}
.divSelect{max-width:80%;}
select{width:150px;}

#divMain{max-width:auto;}

</style>

<script src="jquery-latest.min.js" type="text/javascript"></script>
<script src="http://maps.googleapis.com/maps/api/js?key=AIzaSyCzLfZ_qAqcFD_6m-WPm-bcEp6fR5bGdLs&sensor=false"></script>
<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=places"></script>


<!-- Multiselect starts -->
	<link rel="stylesheet" href="css/bootstrap-3.0.3.min.css" type="text/css">
	<link rel="stylesheet" href="css/bootstrap-multiselect.css" type="text/css">		
<!--	<script type="text/javascript" src="js/jquery-2.0.3.min.js"></script>-->
	<script type="text/javascript" src="js/bootstrap-3.0.3.min.js"></script>
	<script type="text/javascript" src="js/bootstrap-multiselect.js"></script>
<!-- Multiselect ends  -->
<!--
    <link rel="stylesheet" type="text/css" href='css/jquery.dataTables.css'>

<script type='text/javascript' src='http://code.jquery.com/jquery-1.11.1.min.js'></script>
<script type='text/javascript' src='http://cdn.datatables.net/1.10.2/js/jquery.dataTables.min.js'></script>-->

    <script type="text/javascript">
	$(document).ready(function() {
                                
                                //$('#example').dataTable();
            //jQuery("#divMain1").hide();
            //jQuery("#divMain2").hide();

/*
                $('#selectPOSFields').multiselect({
                    
			            //buttonClass: 'btn btn-default btn-sm',
                                    buttonWidth: 'auto',
                                 maxHeight: 300,
                                includeSelectAllOption: true,
                                  numberDisplayed:6,
                                 selectAllText:'Select All',
                                 nonSelectedText: 'Select POS',
                                  selectAllValue : 'select all'  

			        });

                       $('#selectPatientData').multiselect({
			            //buttonClass: 'btn btn-default btn-sm',
                                    buttonWidth: 'auto',
                                 maxHeight: 300,
                                includeSelectAllOption: true,
                                  numberDisplayed:6,
                                 selectAllText:'Select All',
                                 nonSelectedText: 'Select Patient Data',
                                    selectAllValue : 'select all'

			        });
*/
            //showTodaysAppointments();
            showPatients('Y');
            showPatientsByEncounters();
            
            //setByQuerySet();

        });
              
        var markersArray = [];  
        var markersArray_Enc = [];  
        var marker_providersArray = [];  
        var marker_providersArray_Enc = [];
            
            /*
        $(document).ajaxStart(function(){   
            alert('loading show');
                 $('#loading').show();
 });    
            
        $(document).ajaxStop(function(){
            alert('loading hide');
                 $('#loading').hide();
 });    
                
            */
    </script>
    <style>
        .css_button_small {
        -moz-font-feature-settings: normal;
        -moz-font-language-override: normal;
        -moz-text-decoration-color: -moz-use-text-color;
        -moz-text-decoration-line: none;
        -moz-text-decoration-style: solid;
        -x-system-font: none;
        background-attachment: scroll;
        background-clip: border-box;
        background-color: transparent;
        background-image: url("../../images/bg_button_a_small.gif");
        background-origin: padding-box;
        background-position: right top;
        background-repeat: no-repeat;
        background-size: auto auto;
        color: #444;
        display: block;
        float: left;
        font-family: arial,sans-serif;
        font-size: 9px;
        font-size-adjust: none;
        font-stretch: normal;
        font-style: normal;
        font-variant: normal;
        font-weight: bold;
        height: 19px;
        line-height: normal;
        margin-right: 3px;
        padding-right: 10px;
        }

        .css_button_small span {
        background-attachment: scroll;
        background-clip: border-box;
        background-color: transparent;
        background-image: url("../../images/bg_button_span_small.gif");
        background-origin: padding-box;
        background-position: 0 0;
        background-repeat: no-repeat;
        background-size: auto auto;
        display: block;
        line-height: 20px;
        padding-bottom: 0;
        padding-left: 10px;
        padding-right: 0;
        padding-top: 0;
        }
    </style>
</head>
<body>

<div id='divContainer' style='background-color:#FFFFCC;'>
    <div style='height:auto;'>
    <div style='width:50%;float:left;'>
    <center>
    <a id='linkGrid1' href='#' onclick='javascript:jQuery("#divMain1").toggle(500);'>
        <h2>Patients due for Appointments</h2>
    </a>
    </center>
    <div id="divMain1" style='margin:2%;padding:2%;height:auto;background:#FFFF66'>
        
        <div style="float:right">
            <input type="button" value="Refresh" style="height:50px"
                           onclick="javascript:showPatients('N');" />                  
        </div>
        <br><br><br>
        <div style="float:right" onload="javascript:jQuery('#btnSaveSelection').hide();">
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
        
        <div>
            <div class="lblSelect"><b>Query Sets:</b></div>
		<div class='divSelect'>
                    <select id='selectQuerySet' onchange="javascript:setByQuerySet();" class="btn btn-default"  style="text-align:left;">
                        <option value='-1'>---Select---</option>
                    <?php

    $getQuerySets=sqlStatement("SELECT id,set_name FROM tbl_allcare_query_sets
                                WHERE app_enc='1'");

    while ($rowQuerySets = sqlFetchArray($getQuerySets)) 
    {
        echo "<option value='".$rowQuerySets['set_name']."'>".$rowQuerySets['set_name']."</option>";
    }  

                    ?>
                    </select>
                                                  
                </div>  <br> 
                
                <a onclick="javascript: if(jQuery('#selectQuerySet').val()!=='-1'
                                            &&
                                            confirm('Are you sure to delete the selected Query set ?'))
                                        {
                                            deleteQuerySet(1);
                                        }" style='cursor:pointer'>
                                <b>Delete query set</b></a><br>
        </div>
        <br>
        <!--<div id="divSelectQueryName">
		<div class="lblSelect"><b>Query Name:</b></div>
		<div class='divSelect'>
                    <select id='selectQueryList' class="btn btn-default"  style="text-align:left;"
                            onchange="javascript://getQueryColumns();
                                                 showPatients('Y');">
                        <option value="">--Select--</option>
                    <?php
    $getQueries= sqlStatement("SELECT id,name FROM tbl_allcare_query");

    while ($rowQueries = sqlFetchArray($getQueries)) 
    {
      echo "<option value='" . $rowQueries['name'] . "'>".$rowQueries['name']."</option>";
    }  

                    ?>
                    </select>                                                  
		</div>
	</div> -->   
        <br>
        <div>
            <div class="lblSelect"><b>Attributes:</b></div>
            <div class="divSelect">
                <select id='selectCalc' class="btn btn-default" style="text-align:left;">
                    <option value='0'>---Select---</option>
                    <option value="1">Last Visit Date</option>
                    <option value="2">Certification Ends</option>
                    <option value="3">Next Home Visit Days</option>
                    <option value="4">Calculated Next Visit Date</option>
                </select>   
                <input type="button" onclick="javascript:CalcResults();" value="Update">
            </div>    
        </div>
        <div>
            <div class="lblSelect"></div>
            <div class="divSelect">
                <span id="calmsg"></span>
            </div>    
        </div>
        <br>
        <div id="divSelectPatientData">		
        <div class="lblSelect"><b>Patient Standard Data:</b></div>
            <div class='divSelect'>
                <select id="selectPatientData" multiple="multiple" onchange="javascript:showPatients('N');">
                    <option value="">--Select the query--</option>
                <?php
                /*
                $getPatientFields=sqlStatement("SELECT DISTINCT COLUMN_NAME FROM information_schema.columns
                                                WHERE Table_Name='patient_data'
                                                AND (COLUMN_NAME!='id' AND COLUMN_NAME!='pid') ORDER BY COLUMN_NAME");
                    */  
                
                $getPatientFields=sqlStatement("SELECT field_id
                                                FROM `layout_options`
                                                WHERE `form_id` = 'DEM' AND uor > 0 AND field_id != ''
                                                ORDER BY group_name,seq");
                
                if(sqlNumRows($getPatientFields)>0)
                {
                    while ($rowPatientFields = sqlFetchArray($getPatientFields)) 
                    {
                      echo "<option value='" . $rowPatientFields['field_id'] . "'>".str_replace("_"," ",ucfirst($rowPatientFields['field_id']))."</option>";
                    }
                }
                ?>
                </select>
            </div>
    
        </div>
        <br>    
        <div id="divSelectProvider">
		<div class="lblSelect"><b>Primary Care Provider:</b></div>
		<div class='divSelect'>
                    <select id='selectProviders' onchange="javascript:showPatients('N');" class="btn btn-default" style="text-align:left;" multiple>
                        <option value='-1'>---Select---</option>
                        <option value='-2' selected>All</option>
                    <?php

            $getProviders=  sqlStatement("SELECT id,fname,lname FROM users WHERE authorized=1 AND active=1");
            $allProviders=array();
            while ($rowProviders = sqlFetchArray($getProviders)) 
            {
                    echo "<option value='" . $rowProviders['id'] . "'>".$rowProviders['fname']." ".$rowProviders['lname']."</option>";
                    
                    array_push($allProviders,$rowProviders['id']);
                    
            }              
                        
                    ?>
                    </select>
                    <input type='hidden' id='hdnAllProviders' name='hdnAllProviders' 
                           value='<?php echo implode(',',$allProviders); ?>' />
		</div>
	</div>
	<br>

	<div id="divSelectPayer">
		<div class="lblSelect"><b>Primary Insurance:</b></div>
		<div class='divSelect'>
		<select id='selectPayers' onchange="javascript:showPatients('N');" class="btn btn-default" style="text-align:left;" multiple>
                    <option value='-1'>---Select---</option>
                    <option value='-2' selected>All</option>
		<?php
		            
	$getPayers=  sqlStatement("SELECT id,name FROM insurance_companies");
        $allPayers=array();
	while ($rowPayers = sqlFetchArray($getPayers)) 
	{
		echo "<option value='" . $rowPayers['id'] . "'>".$rowPayers['name']."</option>";
                array_push($allPayers,$rowPayers['id']);
	}
		            
		?>
		</select>
                <input type='hidden' id='hdnAllPayers' name='hdnAllPayers' 
                           value='<?php echo implode(',',$allPayers); ?>' />
		</div>
	</div>
        <!--
        
	<div id="divSelectVisitCategory">
		<div class="lblSelect"><b>Visit Category:</b></div>
		<div class='divSelect'>
                    <select id='selectVisitCategory' onchange="javascript:showPatients('N');" class="btn btn-default" style="text-align:left;" multiple>
                        <option value='-1'>---Select---</option>
                        <option value='-2' selected>All</option>
                    <?php

            $getCategories=sqlStatement("SELECT pc_catid,pc_catname FROM openemr_postcalendar_categories 
                                         WHERE pc_cattype=0 ORDER BY pc_catname");
            $allCategories=array();
            while ($rowCategories = sqlFetchArray($getCategories)) 
            {
                    echo "<option value='" . $rowCategories['pc_catid'] . "'>".$rowCategories['pc_catname']."</option>";
                    
                    array_push($allCategories,$rowCategories['pc_catid']);
                    
            }
                        
                    ?>
                    </select>
                    <input type='hidden' id='hdnAllVisitCategories' name='hdnAllVisitCategories' 
                           value='<?php echo implode(',',$allCategories); ?>' />
		</div>
	</div>        
        
        -->
        
	<br>                
        
        <div>
<!--            <a onclick="javascript:jQuery('#divPatientsList').toggle(500);"
               style="cursor:pointer;"><b>Show/Hide Grid</b></a>-->
               <a onclick="javascript:jQuery('#divPatientsList').toggle(500);
                                      jQuery('#lblHide').hide();
                                      jQuery('#lblShow').show();"
               style="cursor:pointer;">
                   <label id="lblHide"><b>Hide</b></label>
               </a>
            
               <a onclick="javascript:jQuery('#divPatientsList').toggle(500);
                                      jQuery('#lblHide').show();
                                      jQuery('#lblShow').hide();"
               style="cursor:pointer;">
                   <label id="lblShow" style="display:none;"><b>Show</b></label>
               </a>
        </div>
        
        <br>
        
	<div id='divPatientsList' style='height:auto;width:auto;overflow-y:auto;background: ORANGE'></div>
        <br>
        
        </div>
    
    </div>
    
    <div style='width:50%;float:left;'>
    <center>
    <a id='linkGrid2' href='#' onclick='javascript:jQuery("#divMain2").toggle(500);'>
        <h2>Patients due for Encounters</h2>
    </a>
    </center>
    <div id="divMain2" style='margin:2%;padding:2%;height:auto;background:#FF99FF'>
        <style type="text/css">@import url(../../library/dynarch_calendar.css);</style>
<script type="text/javascript" src="../../library/dialog.js"></script>
<script type="text/javascript" src="../../library/textformat.js"></script>
<script type="text/javascript" src="../../library/dynarch_calendar.js"></script>
<script type="text/javascript" src="../../library/dynarch_calendar_en.js"></script>
<script type="text/javascript" src="../../library/dynarch_calendar_setup.js"></script>

        <div style="float:right">
            <input type="button" value="Refresh" style="height:50px"
                           onclick="javascript:showPatientsByEncounters();" />                  
        </div>
        <br><br><br> 
        <div style="float:right" onload="javascript:jQuery('#btnSaveSelectionEncounter').hide();">
            <a onclick="javascript: jQuery('#txtQuerySetEncounter').show();
                                    jQuery('#btnSaveSelectionEncounter').show();" style='cursor:pointer'>
                                <b>Save Selection</b></a><br>
                                                                            
                                            
            <input type="text" id='txtQuerySetEncounter' style='display:none;' /><br>
            <input type="button" id='btnSaveSelectionEncounter' value="Save Selection" style="height:50px;display:none;"
                           onclick="javascript:if(jQuery('#txtQuerySetEncounter').val()!=='')
                                               { saveSelectionEncounter(); }
                                               else
                                               { alert('Enter query set name'); }
                                                                                                   " /> 
        </div>
        
        
        <div>
            <div class="lblSelect"><b>Query Sets:</b></div>
		<div class='divSelect'>
                    <select id='selectQuerySetEncounter' onchange="javascript:setByQuerySetEncounter();" class="btn btn-default"  style="text-align:left;">
                        <option value='-1'>---Select---</option>
                    <?php

    $getQuerySets=sqlStatement("SELECT id,set_name FROM tbl_allcare_query_sets
                                WHERE app_enc='2'");

    while ($rowQuerySets = sqlFetchArray($getQuerySets)) 
    {
        echo "<option value='".$rowQuerySets['set_name']."'>".$rowQuerySets['set_name']."</option>";
    }  

                    ?>
                    </select>
                                                  
                </div>  <br> 
                
                <a onclick="javascript: if(jQuery('#selectQuerySetEncounter').val()!=='-1'
                                            &&
                                            confirm('Are you sure to delete the selected Query set ?'))
                                        {
                                            deleteQuerySet(2);
                                        }" style='cursor:pointer'>
                                <b>Delete query set</b></a><br>
        </div>        
        <br>
        
	<div id="divSelectSchedulingProvider">
		<div class="lblSelect"><b>Scheduling Provider:</b></div>
		<div class='divSelect'>
                    <select id='selectSchedulingProviders' onchange='javascript:showPatientsByEncounters();' class="btn btn-default" style="text-align:left;" multiple>
                        <option value='-1'>---Select---</option>
                        <option value='-2' selected>All</option>
                    <?php

            $getProviders=sqlStatement("SELECT id,fname,lname FROM users WHERE authorized=1 AND active=1");
            $allProviders=array();
            while ($rowProviders = sqlFetchArray($getProviders))
            {
                    echo "<option value='" . $rowProviders['id'] . "'>".$rowProviders['fname']." ".$rowProviders['lname']."</option>";
                    
                    array_push($allProviders,$rowProviders['id']);
                    
            }
                        
                    ?>
                    </select>
                    <input type='hidden' id='hdnAllSchedulingProviders' name='hdnAllSchedulingProviders' 
                           value='<?php echo implode(',',$allProviders); ?>' />
		</div>
	</div>
	<br>
        
        <div id="divDateRange">
            <div class=""><b>From:</b></div>
            <input type='text' size='10' name="date_from" id="date_from" 
       onkeyup='datekeyup(this,mypcc)' onblur='dateblur(this,mypcc)' 
       title='yyyy-mm-dd from date of this event' readonly="readonly" 
       value="<?php echo date('Y-m-d'); ?>" onchange='javascript:fromSelected();showPatientsByEncounters()' />
<img src='../pic/show_calendar.gif' align='absbottom' width='24' height='22'
id='img_calendar_from' border='0' alt='[?]' style='cursor:pointer;'
title='Click here to choose a date'>
<script>
Calendar.setup({inputField:"date_from", ifFormat:'%Y-%m-%d', button:"img_calendar_from"});

</script>

         <div class=""><b>To:</b></div>
            <input type='text' size='10' name="date_to" id="date_to" 
       onkeyup='datekeyup(this,mypcc)' onblur='dateblur(this,mypcc)' 
       title='yyyy-mm-dd last date of this event' readonly="readonly" 
       value="<?php echo date('Y-m-d'); ?>" onchange='javascript:showPatientsByEncounters()' />
<img src='../pic/show_calendar.gif' align='absbottom' width='24' height='22'
id='img_calendar_to' border='0' alt='[?]' style='cursor:pointer;'
title='Click here to choose a date'>
<script>
Calendar.setup({inputField:"date_to", ifFormat:'%Y-%m-%d', button:"img_calendar_to"});

</script>
                        
        </div>
        <br>       
                                
	<div id="divSelectVisitCategoryEncounter">
		<div class="lblSelect"><b>Visit Category:</b></div>
		<div class='divSelect'>
                    <select id='selectVisitCategoryEncounter' onchange="javascript:showPatientsByEncounters();" class="btn btn-default" style="text-align:left;" multiple>
                        <option value='-1'>---Select---</option>
                        <option value='-2' selected>All</option>
                    <?php

            $getCategories=sqlStatement("SELECT pc_catid,pc_catname FROM openemr_postcalendar_categories 
                                         WHERE pc_cattype=0 ORDER BY pc_catname");
            $allCategories=array();
            while ($rowCategories = sqlFetchArray($getCategories)) 
            {
                    echo "<option value='" . $rowCategories['pc_catid'] . "'>".$rowCategories['pc_catname']."</option>";
                    
                    array_push($allCategories,$rowCategories['pc_catid']);
                    
            }              
                        
                    ?>
                    </select>
                    <input type='hidden' id='hdnAllVisitCategoriesEncounter' name='hdnAllVisitCategoriesEncounter' 
                           value='<?php echo implode(',',$allCategories); ?>' />
		</div>
	</div>        
	<br>                
          
        <div>
<!--            <a onclick="javascript:jQuery('#divPatientsListByEncounter').toggle(500);"
               style="cursor:pointer;"><b>Show/Hide Grid</b></a>-->
               <a onclick="javascript:jQuery('#divPatientsListByEncounter').toggle(500);
                                      jQuery('#lblHideEnc').hide();
                                      jQuery('#lblShowEnc').show();"
               style="cursor:pointer;">
                   <label id="lblHideEnc"><b>Hide</b></label>
               </a>
            
               <a onclick="javascript:jQuery('#divPatientsListByEncounter').toggle(500);
                                      jQuery('#lblHideEnc').show();
                                      jQuery('#lblShowEnc').hide();"
               style="cursor:pointer;">
                   <label id="lblShowEnc" style="display:none;"><b>Show</b></label>
               </a>
        </div>
        
        
        <br>
        
        <div id='divPatientsListByEncounter' style='height:auto;width:auto;overflow-y:auto;background:ORANGE'></div>
        <br>
       
    </div>
    
    </div>
    </div>
    
    <div id='divGoogleMap' style='float:right;height:700px;width:100%;border:1px solid;'></div>
    
    <div id="loading" style="display:none;margin:32%;height:100px;width:200px;position:absolute;background:#fff">
        <center><b><p style="padding:20%;z-index:3000;">
                    Please Wait ..
                </p></b></center>
    </div>
           
</div>


<!--<div id='divGoogleMap' style='height:500px;width:100%;border:1px solid;'></div>-->
    
<script type='text/javascript'>

	$(window).load(function() {
		//showPatients('N');  
                
//                jQuery('#divPatientsList').on('click','#example',function(){
//                    
//               $("#example").appendTo("#divPatientsList");
//               showPatients('N');  
//               //alert('clicking 2');
//                });      
                                                
	});
        
        google.maps.event.addDomListener(window, 'load', initialize);
        google.maps.event.addDomListener('#divGoogleMap', 'load', initialize);

function initialize()
{
    //var latlng = new google.maps.LatLng(100,100);
    var latlng = new google.maps.LatLng(32.780140, -96.800451);
    var mapOptions = {
                             zoom: 4,
                             center: latlng
                     }

    map = new google.maps.Map(document.getElementById("divGoogleMap"), mapOptions);
         
}

function showPatients(isQueryChanged)
{

	jQuery('#divPatientsList').html('');	
        jQuery('#divPatientsList').html('<img src="loading.GIF"> loading...');	
    //jQuery('#divGoogleMap').html('');	
	//var queryName=jQuery('#selectQueryList').val(); 
                     
	var providerId=jQuery('#selectProviders').val();
        var allProviders=jQuery('#hdnAllProviders').val();
        //alert('allProviders='+allProviders);
	var payerId=jQuery('#selectPayers').val(); 
        var allPayers=jQuery('#hdnAllPayers').val();
        var visitCategoryId=jQuery('#selectVisitCategory').val(); 
        var allVisitCategories=jQuery('#hdnAllVisitCategories').val(); 
        
        var patientStandardData=jQuery('#selectPatientData').val();
        
//        $('#loading').show(); 
	$.ajax({
		type: 'POST',
		url: "display_patients_by_query.php",	
		data: { //queryName:queryName,                        
                        providerId:providerId,payerId:payerId,
                        allProviders:allProviders,allPayers:allPayers,
                        visitCategoryId:visitCategoryId,allVisitCategories:allVisitCategories,
                        patientStandardData:patientStandardData,
                        isQueryChanged:isQueryChanged
                },

		success: function(response)
		{
                    
                    jQuery('#divPatientsList').html(response);
                   // alert('11');
                    //showTodaysAppointments();
                   // alert('22');
                    showLocations();                    
                   // alert('33');
                   // showProviderLocation();
                   // alert('44');
                    
                    //jQuery('#hdnSortColumn').val('');
                    //jQuery('#hdnSortOrder').val('');
		    $('#loading').hide();		     
		},
		failure: function(response)
		{
			alert("error");
		}		
	});	       
}

function fromSelected(){
    jQuery('#date_to').val(jQuery('#date_from').val());
}

function showPatientsByEncounters()
{
	jQuery('#divPatientsListByEncounter').html('');	
	jQuery('#divPatientsListByEncounter').html('<img src="loading.GIF"> loading...');	
	//jQuery('#divGoogleMap').html('');	
	
	var schedulingProviderId=jQuery('#selectSchedulingProviders').val();
        var allSchedulingProviders=jQuery('#hdnAllSchedulingProviders').val();
	var fromDate=jQuery('#date_from').val(); 
        var toDate=jQuery('#date_to').val();
        var visitCategoryId=jQuery('#selectVisitCategoryEncounter').val(); 
        var allVisitCategories=jQuery('#hdnAllVisitCategoriesEncounter').val();
       
	$.ajax({
		type: 'POST',
		url: "display_patients_by_encounter.php",	
		data: {schedulingProviderId:schedulingProviderId,
                        allSchedulingProviders:allSchedulingProviders,
                        fromDate:fromDate,toDate:toDate,
                        visitCategoryId:visitCategoryId,
                        allVisitCategories:allVisitCategories
                       },	

		success: function(response)
		{
                    
                    jQuery('#divPatientsListByEncounter').html(response);
                    //showProviderLocationEncounter();
                    //showTodaysAppointments();
                    showLocationsEncounter();
		     		     
		},
		failure: function(response)
		{
			alert("error");
		}		
	});	       
}

var marker_provider = new google.maps.Marker({
                        map: map, position: ''	
             });  
    
function showProviderLocation()
{
    var providerList = '';
    //showPatients('N');
    if(jQuery('#selectProviders').val().toString()==='-2')
    {
        providerList = $.map($('#selectProviders option'), function(e) {
            return e.value;
        });

        // as a comma separated string        
        //providerList=provider_values.join(',');
    }

    if(jQuery('#selectProviders').val().toString()!=='-1' &&
       jQuery('#selectProviders').val().toString()!=='-2' )
    {
        providerList=jQuery('#selectProviders').val();
    }
        
    
        for (var i=0;i<marker_providersArray.length;i++) 
        {
            marker_providersArray[i].setMap(null);
        }

        var providerArray = new Array();
        providerArray = providerList.toString().split(",");
        /*
        if(jQuery('#selectProviders').val()!=='-2')
        {
            jQuery('#selectProviders option').each(function(){
            providerArray.push($(this).val());
            });
            
        }      */
        
        //alert('providerList='+providerList);
        //alert('pArr='+providerArray);
        
        var show_provider_address= new Array();
        
        //for(var x=0;x<providerArray.length;x++)
        //{
        jQuery.each(providerArray,function(x,val){
                
                show_provider_address[x]=(jQuery("#hdnProviderDetails"+providerArray[x]+"").val());
                var spa=show_provider_address[x];
                
                var geocoder_provider = new google.maps.Geocoder();
                var address_provider = jQuery("#hdnProviderAddress"+providerArray[x]+"").val();

                geocoder_provider.geocode({ 'address': address_provider }, function (results, status) {
                    if (status === google.maps.GeocoderStatus.OK) 
                    {
                        map.setCenter(results[0].geometry.location);

                        marker_provider = new google.maps.Marker({
                                   map: map,
                                   position: results[0].geometry.location	
                        });
                        
             //marker_provider.setIcon('http://gmaps-samples.googlecode.com/svn/trunk/markers/blue/blank.png);			
//marker_provider.setIcon('http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=P|FF0000|000000');
                        marker_provider.setIcon('images/firstaid.png');

//marker_provider.setTitle(jQuery("#hdnProviderAddress"+providerArray[x]).val());
                        marker_provider.setTitle(address_provider);

                        marker_providersArray.push(marker_provider);

                        var infowindow_provider = new google.maps.InfoWindow();
                        var service = new google.maps.places.PlacesService(map);

                        //alert('spa_in'+x+'='+spa);
                        google.maps.event.addListener(marker_provider, 'click', function() {

                                    //infowindow_provider.setContent(show_provider_address.toString());
                                    infowindow_provider.setContent(spa.toString());
                                    infowindow_provider.open(map, this);
                              });	

                        google.maps.event.addListener(marker_provider, 'dblclick', function() {

                              //map.setZoom(5);
                        });			  							
                                                
                    } 
                    else{
                        alert("Error " + google.maps.GeocoderStatus);
                    }
                    
            });
               
            
        });  
        //}		                
           
}

var marker_provider_enc = new google.maps.Marker({
                        map: map, position: ''	
             });

function showProviderLocationEncounter()
{

    if(jQuery('#selectSchedulingProviders').val()!=='none')
    {
        var providerList=jQuery('#selectSchedulingProviders').val();

        for (var i=0;i<marker_providersArray_Enc.length;i++) 
        {
            marker_providersArray_Enc[i].setMap(null);
        }
        
        var providerArray = new Array();
        providerArray = providerList.toString().split(",");
        //alert('pArr='+providerArray);
        var show_provider_address= new Array();
        //for(var x=0;x<providerArray.length;x++)
        //{
        jQuery.each(providerArray,function(x,val){
                show_provider_address[x]=(jQuery("#hdnSchedulingProviderDetails"+providerArray[x]+"").val());
                var spa=show_provider_address[x];
                var geocoder_provider = new google.maps.Geocoder();
                var address_provider = jQuery("#hdnSchedulingProviderAddress"+providerArray[x]+"").val();

                geocoder_provider.geocode({ 'address': address_provider }, function (results, status) {
                    if (status === google.maps.GeocoderStatus.OK) 
                    {                           
                        map.setCenter(results[0].geometry.location);

                        marker_provider_enc = new google.maps.Marker({
                                   map: map,
                                   position: results[0].geometry.location	
                        });

             //marker_provider.setIcon('http://gmaps-samples.googlecode.com/svn/trunk/markers/blue/blank.png);			
//marker_provider_enc.setIcon('http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=P|009900|000000');
                        marker_provider_enc.setIcon('images/first_aid.png');

                        //marker_provider_enc.setTitle(jQuery("#hdnSchedulingProviderAddress"+providerArray[x]).val());
                        marker_provider_enc.setTitle(address_provider);

                        marker_providersArray_Enc.push(marker_provider_enc);

                        var infowindow_provider = new google.maps.InfoWindow();
                        var service = new google.maps.places.PlacesService(map);

                        //alert('spa_in'+x+'='+spa);
                        google.maps.event.addListener(marker_provider_enc, 'click', function() {

                                    //infowindow_provider.setContent(show_provider_address.toString());
                                    infowindow_provider.setContent(spa.toString());
                                    infowindow_provider.open(map, this);
                              });	

                        google.maps.event.addListener(marker_provider_enc, 'dblclick', function() {

                              //map.setZoom(5);
                        });			  							
                        //alert('x2='+x);
                    } 
            });
           
           
        });
        //}		
    }

}

function showTodaysAppointments()
{
    var appointments=document.getElementsByName("hdnAppointmentAddress");    

    var get_appointment=0;
    
    jQuery.each(appointments,function(j,val){
    
    //for(var j=0;j<appointments.length;j++)
    //{
            var geocoder_appointment = new google.maps.Geocoder();
            var appointment = jQuery("#hdnAppointmentAddress_"+j).val();

            geocoder_appointment.geocode({ 'address': appointment }, function (results, status) {
                    if (status === google.maps.GeocoderStatus.OK) 
                    {
                             map.setCenter(results[0].geometry.location);
                             var marker_appointment = new google.maps.Marker({
                                        map: map,
                                        position: results[0].geometry.location

                             });

                    marker_appointment.setTitle(jQuery("#hdnAppointmentAddress_"+get_appointment).val());
                    //marker_appointment.setIcon('http://maps.google.com/mapfiles/ms/icons/blue.png');
                    //marker_appointment.setIcon('http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=A|0000FF|000000');
                    marker_appointment.setIcon('images/appointment.png');

                              var infowindow = new google.maps.InfoWindow();
                              var service = new google.maps.places.PlacesService(map);

                              var show_appointment=jQuery("#hdnAppointmentDetails_"+get_appointment).val();
                              google.maps.event.addListener(marker_appointment, 'click', function() {

                                    infowindow.setContent(show_appointment.toString());
                                    infowindow.open(map, this);
                              });

                              google.maps.event.addListener(marker_appointment, 'dblclick', function() {

                                    //map.setZoom(3);
                              });


                              get_appointment++;

                    } 	
            });
    //}
    
    
    });
}
           
var marker = new google.maps.Marker({
           map: map, position: ''
});

function showLocations()
{
        var addresses=document.getElementsByName("hdnAddress");
        var addressesback = document.getElementsByName("hdnAddressBack");
        
        for (var i=0;i<markersArray.length;i++) 
        {
            markersArray[i].setMap(null);
        }
        
            // alert('ADDRS LrL= '+addresses.length);   
	var get_address=0;
        var get_addressBack=0;
	//for(var i=0;i<addresses.length;i++)
	//{
        //$('#AddrVal').dataTable().fnClearTable();
	jQuery.each(addressesback,function(i,val){
		
/**************************************************************************/
	    //alert('add '+i+':'+jQuery("#hdnAddressBack_"+i).val());
            //var geocoder = new google.maps.Geocoder();
            var address_patient = jQuery("#hdnAddressBack_"+i).val();
            var show_address=jQuery("#hdnDetailsBack_"+i).val();
//alert('ADDRS_D = '+jQuery("#hdnDetails_"+i).val());
            
            //setTimeout( function () {
            //var address = encodeURIComponent(address_patient);
            var address = address_patient.split("$*$");
            var street = encodeURIComponent(address[0]);
            var city = encodeURIComponent(address[1]);
            var state = encodeURIComponent(address[2]);
            var zip = encodeURIComponent(address[3]);
            //alert(address);
//            jQuery.getJSON('geocode.php?addr='+address, function(data) { 
//                var results = data.results;
//                status = data.status;
            
            if(address_patient != "$*$$*$$*$$*$"){
                $.ajax({
                            type: 'get',
                            cache: false,
                            url: "https://geoservices.tamu.edu/Services/Geocode/WebService/GeocoderWebServiceHttpNonParsedDetailed_V04_01.aspx?streetAddress="+street+"&city="+city+"&state="+state+"&zip="+zip+"&apikey=0704d4f1e43c45ce89fb2ff5e20a8942&format=json&census=true&censusYear=2010&notStore=false&version=4.01",
                            success: function(response) {
                                //document.write(response);
                                var addr = JSON.parse(response); 
                                var lat = addr.OutputGeocodes[0].OutputGeocode.Latitude;
                                var lon = addr.OutputGeocodes[0].OutputGeocode.Longitude;
                                var latlon = lat +","+lon;
                                //document.write(response);
                                

                    if (lat != "" & lon != "") 
                    {
                        $.ajax({
                            type: 'get',
                            cache: false,
                            url: "http://maps.googleapis.com/maps/api/geocode/json?latlng="+latlon+"&sensor=false",
                            success: function(response) {
                               // alert(JSON.stringify(response));
                                var obj = JSON.parse(JSON.stringify(response) );
                                //alert(JSON.stringify(obj["results"][0]["formatted_address"]));
                                //alert(address_patient);
                                patientAddSplit = show_address.split('-<br>');
                                patientName = patientAddSplit[0];
                                correctAddress = JSON.stringify(obj["results"][0]["formatted_address"]);
                                correctAddress = correctAddress.substring(1,correctAddress.length-1);
                                //alert(correctAddress +"!="+ address_patient);
                                var exactaddress_patient = address_patient.split("$*$").join(", ");
                                //alert(exactaddress_patient);
                                if(correctAddress != exactaddress_patient){
                                    //$("#AddrVal > tbody").append('<tr><td>'+ patientName +'</td><td>'+ addressPatient +'</td><td>'+ JSON.stringify(obj["results"][0]["formatted_address"]) +'</td></tr>');
                                    $("#AddrVal").dataTable().fnAddData( [
                                        patientName,
                                        exactaddress_patient,
                                        correctAddress
                                    ]);
                                }
                            },
                            error: "Unknown Place"
                        });
                        var myLatlng = new google.maps.LatLng(lat, lon);
                        map.setCenter(myLatlng);
                        marker = new google.maps.Marker({
                                   map: map,
                                   position: myLatlng

                        });

                        //marker.setTitle(jQuery("#hdnAddress_"+get_address).val());
                        marker.setTitle(exactaddress_patient);

                        //marker.setIcon('http://maps.google.com/mapfiles/ms/icons/yellow.png');
                        marker.setIcon('images/yellow.png');
                        markersArray.push(marker);
                        var infowindow = new google.maps.InfoWindow();
                        var service = new google.maps.places.PlacesService(map);

                        //var show_address=jQuery("#hdnDetails_"+get_address).val();
    //alert('add '+i+':'+jQuery("#hdnAddress_"+i).val());
    //alert('show_address '+i+':'+jQuery("#hdnDetails_"+i).val());
                        google.maps.event.addListener(marker, 'click', function() {

                              infowindow.setContent(show_address.toString());
                              infowindow.open(map, this);

                            //get_address++;
                        });

                        google.maps.event.addListener(marker, 'dblclick', function() {

                        //map.setZoom(3);

                        });


                        get_addressBack++;

                    } 
                    else{
                        var exactaddress_patient = address_patient.split("$*$").join(", ");
                        patientAddSplit = show_address.split('-<br>');
                        patientName = patientAddSplit[0];
                        $("#AddrVal").dataTable().fnAddData( [
                            patientName,
                            exactaddress_patient,
                            "Sorry! No Match Found"
                        ]);
                    }

                }});
              }  
              
            //}, i * 500);
/*****************************************************/
        //if(i == 15) return false;
        });
        
        map.setZoom(12);
                          
	//}            

} 

var marker_enc = new google.maps.Marker({
                                map: map, position:''
                     });
       
function showLocationsEncounter()
{
    var addresses=document.getElementsByName("hdnAddressEncBack");    
    for(var i=0;i<addresses.length;i++)
    {
        //alert('addresses='+jQuery("#hdnDetailsEnc_"+i).val());
    }
        
    for (var i=0;i<markersArray_Enc.length;i++) 
    {
        markersArray_Enc[i].setMap(null);
    }
       
    var get_address=0;
    //for(var i=0;i<addresses.length;i++)
    //{
    //$('#encAddrVal').dataTable().fnClearTable();
    jQuery.each(addresses,function(k,val){
        //var geocoder = new google.maps.Geocoder();
        var address_patient = jQuery("#hdnAddressEncBack_"+k).val();
        //alert("k = "+k+ " -- " +address_patient);
        var show_address=jQuery("#hdnDetailsEncBack_"+k).val();
        //setTimeout( function () {
        var address = address_patient.split("$*$");
        var street = encodeURIComponent(address[0]);
        var city = encodeURIComponent(address[1]);
        var state = encodeURIComponent(address[2]);
        var zip = encodeURIComponent(address[3]);
    
        if(address_patient != "$*$$*$$*$$*$"){
                $.ajax({
                            type: 'get',
                            cache: false,
                            url: "https://geoservices.tamu.edu/Services/Geocode/WebService/GeocoderWebServiceHttpNonParsedDetailed_V04_01.aspx?streetAddress="+street+"&city="+city+"&state="+state+"&zip="+zip+"&apikey=0704d4f1e43c45ce89fb2ff5e20a8942&format=json&census=true&censusYear=2010&notStore=false&version=4.01",
                            success: function(response) {
                                //document.write(response);
                                var addr = JSON.parse(response); 
                                var lat = addr.OutputGeocodes[0].OutputGeocode.Latitude;
                                var lon = addr.OutputGeocodes[0].OutputGeocode.Longitude;
                                var latlon = lat +","+lon;
                                //document.write(response);
                                

                    if (lat != "" & lon != "") 
                    { 
                        $.ajax({
                            type: 'get',
                            cache: false,
                            url: "http://maps.googleapis.com/maps/api/geocode/json?latlng="+latlon+"&sensor=false",
                            success: function(response) {
                             // alert(JSON.stringify(response));
                                var obj = JSON.parse(JSON.stringify(response) );
                                //alert(JSON.stringify(obj["results"][0]["formatted_address"]));
                                //alert(address_patient);
                                patientAddSplit = show_address.split('-<br>');
                                patientName = patientAddSplit[0];
                                correctAddress = JSON.stringify(obj["results"][0]["formatted_address"]);
                                correctAddress = correctAddress.substring(1,correctAddress.length-1);
                                var exactaddress_patient = address_patient.split("$*$").join(", ");
                                //alert(correctAddress +"!="+ address_patient);
                                //alert("IF - " + address_patient);
                                if(correctAddress != exactaddress_patient){
                                    $("#encAddrVal").dataTable().fnAddData( [
                                        patientName,
                                        exactaddress_patient,
                                        correctAddress
                                    ]);
                                }
                            },
                            error: "Unknown Place"
                        });
                
                var myLatlng = new google.maps.LatLng(lat, lon);
                map.setCenter(myLatlng);
                marker_enc = new google.maps.Marker({
                           map: map,
                           position: myLatlng
                });

                //marker_enc.setTitle(jQuery("#hdnAddressEnc_"+get_address).val());
                marker_enc.setTitle(address_patient);
                //marker_enc.setIcon('http://maps.google.com/mapfiles/ms/icons/pink.png');
                marker_enc.setIcon('images/pink.png');
                markersArray_Enc.push(marker_enc);	
                var infowindow = new google.maps.InfoWindow();
                var service = new google.maps.places.PlacesService(map);

                //var show_address=jQuery("#hdnDetailsEnc_"+get_address).val();
                google.maps.event.addListener(marker_enc, 'click', function() {

                       infowindow.setContent(show_address.toString());
                       infowindow.open(map, this);
                 });

                google.maps.event.addListener(marker_enc, 'dblclick', function() {

                       //map.setZoom(5);
                 });

                get_address++;

            } 	
            
           else {
                //alert("ELSE - " + address_patient);
                var exactaddress_patient = address_patient.split("$*$").join(", ");
                patientAddSplit = show_address.split('-<br>');
                patientName = patientAddSplit[0];
                $("#encAddrVal").dataTable().fnAddData( [
                    patientName,
                    exactaddress_patient,
                    "Sorry! No Match Found"
                ]);
           }
                            }});
        }                   
        //}, k * 500);
    });
        
    //}
}

function saveSelection()
{
    jQuery('#divPatientsList').html('');	
	//jQuery('#divGoogleMap').html('');	
    var queryName=jQuery('#selectQueryList').val(); 
    var querySetName=jQuery('#txtQuerySet').val(); 
    
    var patientStandardData=jQuery('#selectPatientData').val();
    var providerId=jQuery('#selectProviders').val();
    var allProviders=jQuery('#hdnAllProviders').val();
    var payerId=jQuery('#selectPayers').val(); 
    var allPayers=jQuery('#hdnAllPayers').val();
    var visitCategoryId=jQuery('#selectVisitCategory').val(); 
    var allVisitCategories=jQuery('#hdnAllVisitCategories').val(); 
                 
    $.ajax({
            type: 'POST',
            url: "save_query_set.php",	
            data:{app_enc:1,queryName:queryName,querySetName:querySetName,
                   patientStandardData:patientStandardData,
                   providerId:providerId,payerId:payerId,
                   visitCategoryId:visitCategoryId,
                   allProviders:allProviders,allPayers:allPayers,
                   allVisitCategories:allVisitCategories
                  
            },	

            success: function(response)
            {
                getSetNames(1);
                jQuery('#txtQuerySet').val(''); 
                jQuery('#txtQuerySet').hide(); 
                jQuery('#btnSaveSelection').hide();
            },
            failure: function(response)
            {
                    alert("error");
            }		
    });
}

function saveSelectionEncounter()
{
    jQuery('#divPatientsList').html('');	    
    var querySetName=jQuery('#txtQuerySetEncounter').val(); 
    
    var providerIdEncounter=jQuery('#selectSchedulingProviders').val();
    var allProvidersEncounter=jQuery('#hdnAllSchedulingProviders').val();
    var date_from=jQuery('#date_from').val(); 
    var date_to=jQuery('#date_to').val();
    var visitCategoryIdEncounter=jQuery('#selectVisitCategoryEncounter').val(); 
    var allVisitCategoriesEncounter=jQuery('#hdnAllVisitCategoriesEncounter').val(); 
    
    $.ajax({
            type: 'POST',
            url: "save_query_set.php",	
            data:{app_enc:2,querySetName:querySetName,providerIdEncounter:providerIdEncounter,
                allProvidersEncounter:allProvidersEncounter,
                date_from:date_from,date_to:date_to,
                visitCategoryIdEncounter:visitCategoryIdEncounter,
                allVisitCategoriesEncounter:allVisitCategoriesEncounter
            },	

            success: function(response)
            {
                getSetNames(2);
                jQuery('#txtQuerySetEncounter').val(''); 
                jQuery('#txtQuerySetEncounter').hide(); 
                jQuery('#btnSaveSelectionEncounter').hide();
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
   
    if(querySetName==='-1')
    {
        //("select option").prop("selected", false);
    }
    $.ajax({
            type: 'POST',
            url: "set_by_queryset.php",	
            data:{querySetName:querySetName,app_enc:1},	

            success: function(response)
            {                                
                var setArray=response.split("|");                
                
                var patientsData=setArray[1].slice(1);
                patientsData=patientsData.slice(0,-1);
                var customData=setArray[2].slice(1);
                customData=customData.slice(0,-1);                
                var providers=setArray[3].slice(1);
                providers=providers.slice(0,-1);
                var payers=setArray[4].slice(1);
                payers=payers.slice(0,-1);                
                var visitCategories=setArray[5].slice(1);
                visitCategories=visitCategories.slice(0,-1);
                visitCategories=visitCategories.slice(0,-1);
                
                jQuery('#selectQueryList').val(setArray[0]);
                
                jQuery('#selectPatientData').val('');
                jQuery('#selectPOSFields').val('');
                jQuery('#selectProviders').val('');
                jQuery('#selectPayers').val(''); 
                jQuery('#selectVisitCategory').val(''); 
                
                if(patientsData==='All' || patientsData==='')
                {
                    jQuery("#selectPatientData option[value=-2]").prop("selected","selected") ;                    
                }
                else
                {
                    var patientsDataArray=patientsData.split(',');                
                    for(var i=0;i<patientsDataArray.length;i++)
                    {
                        jQuery("#selectPatientData option[value=" + patientsDataArray[i] +"]").prop("selected","selected") ;
                    }                     
                }             
                
                if(customData==='All' || customData==='')
                {
                    jQuery("#selectPOSFields option[value=-2]").prop("selected","selected") ;                    
                }
                else
                {
                    var customDataArray=customData.split(',');                
                    for(var i=0;i<customDataArray.length;i++)
                    {
                        jQuery("#selectPOSFields option[value=" + customDataArray[i] +"]").prop("selected","selected") ;
                    }                     
                }             
                
                if(providers==='All' || providers==='')
                {
                    jQuery("#selectProviders option[value=-2]").prop("selected","selected") ;                    
                }
                else
                {
                    var providersArray=providers.split(',');                
                    for(var i=0;i<providersArray.length;i++)
                    {
                        jQuery("#selectProviders option[value=" + providersArray[i] +"]").prop("selected","selected") ;
                    }                     
                }
                
                
                if(payers==='All' || payers==='')
                {
                    jQuery("#selectPayers option[value=-2]").prop("selected","selected") ;
                }
                else
                {
                    var payersArray=payers.split(',');                
                    for(var i=0;i<payersArray.length;i++)
                    {
                        jQuery("#selectPayers option[value=" + payersArray[i] +"]").prop("selected","selected") ;
                    }
                }
                
                
                if(visitCategories==='All' || visitCategories==='')
                {
                    jQuery("#selectVisitCategory option[value=-2]").prop("selected","selected") ;
                }
                else
                {
                    var visitCategoriesArray=visitCategories.split(',');                
                    for(var i=0;i<visitCategoriesArray.length;i++)
                    {
                        jQuery("#selectVisitCategory option[value=" + visitCategoriesArray[i] +"]").prop("selected","selected") ;
                    }
                }
                
                
                //getQueryColumns();
                showPatients('N');
                
                
            },
            failure: function(response)
            {
                    alert("error");
            }		
    });
}

function setByQuerySetEncounter()
{
    var querySetName=jQuery('#selectQuerySetEncounter').val(); 
    
    $.ajax({
            type: 'POST',
            url: "set_by_queryset.php",
            data:{querySetName:querySetName,app_enc:2},
            success: function(response)
            {
                var setArray=response.split("|");
                
                var providers=setArray[0].slice(1);
                providers=providers.slice(0,-1);
                  
                jQuery('#date_from').val(setArray[1]); 
                jQuery('#date_to').val(setArray[2]);      
                  
                var visitCategories=setArray[3].slice(1);
                visitCategories=visitCategories.slice(0,-1);
                visitCategories=visitCategories.slice(0,-1);
                 
                jQuery('#selectSchedulingProviders').val('');                
                jQuery('#selectVisitCategoryEncounter').val('');  
              
                if(providers==='All' || providers==='')
                {
                    jQuery("#selectSchedulingProviders option[value=-2]").prop("selected","selected") ;                    
                }
                else
                {
                    var providersArray=providers.split(',');                
                    for(var i=0;i<providersArray.length;i++)
                    {
                        jQuery("#selectSchedulingProviders option[value=" + providersArray[i] +"]").prop("selected","selected") ;
                    }
                }
                
                if(visitCategories==='All' || visitCategories==='')
                {
                    jQuery("#selectVisitCategoryEncounter option[value=-2]").prop("selected","selected") ;
                }
                else
                {
                    var visitCategoriesArray=visitCategories.split(',');                
                    for(var i=0;i<visitCategoriesArray.length;i++)
                    {
                        jQuery("#selectVisitCategoryEncounter option[value=" + visitCategoriesArray[i] +"]").prop("selected","selected") ;
                    }
                }
                
                showPatientsByEncounters();
                
            },
            failure: function(response)
            {
                    alert("error");
            }	
            
    });
}

function getQueryColumns(app_enc)
{
    var queryName='';
    if(app_enc===1)
    {
        queryName=jQuery('#selectQuerySet').val(); 
    }
    else if(app_enc===2)
    {
        queryName=jQuery('#selectQuerySetEncounter').val(); 
    }
    
    $.ajax({
		type: 'POST',
		url: "get_query_columns.php",	
		data: {queryName:queryName},	

		success: function(response)
		{
                    var patientsDataArray=response.split("|");
                    jQuery("#selectPatientData").find('option').remove();
                    
                    for(var i=0;i<patientsDataArray.length;i++)
                    {
                        var col_name=patientsDataArray[i].replace(/_/g," ");
                        col_name=col_name.charAt(0).toUpperCase()+col_name.slice(1);
jQuery("#selectPatientData").append("<option value='"+ patientsDataArray[i] +"'>"+ col_name +"</option>");                        
                    }

		},
		failure: function(response)
		{
                    alert("error");
		}		
	});	 
}

function getSetNames(app_enc)
{
    
    var QuerySet='';
    if(app_enc===1)
    {
        QuerySet=jQuery('#selectQuerySet'); 
    }
    else if(app_enc===2)
    {
        QuerySet=jQuery('#selectQuerySetEncounter'); 
    }
    
    $.ajax({
		type: 'POST',
		url: "get_query_sets.php",	
                data:{app_enc:app_enc},
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

function deleteQuerySet(app_enc)
{
    
    var querySet='';
    if(app_enc===1)
    {
        querySet=jQuery('#selectQuerySet').val(); 
    }
    else if(app_enc===2)
    {
        querySet=jQuery('#selectQuerySetEncounter').val(); 
    }
    
    $.ajax({
		type: 'POST',
		url: "delete_query_set.php",	
                data:{querySet:querySet,app_enc:app_enc},
		success: function()
		{
                    getSetNames(app_enc);

		},
		failure: function(response)
		{
                    alert("error");
		}		
	});
    getSetNames(app_enc);
    
}

function CalcResults(){
        var selectCalc=jQuery('#selectCalc').val(); 
                
        $.ajax({
		type: 'POST',
		url: "update_attr.php",	
		data: {                       
                        selectCalc:selectCalc
                      },

		success: function(response)
		{
                    
                    jQuery('#calmsg').html(response);
                },
		failure: function(response)
		{
			alert("error");
		}		
	});	 
}
</script>
</body>
</html>
    
