<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

require_once("verify-session.php");
require_once("../../library/formdata.inc.php");
require_once("../../library/globals.inc.php");

$pagename = "sche"; 
if(isset($_SESSION['portal_username']) !=''){
    $provider    = $_SESSION['portal_username'];
    $refer       = $_REQUEST['refer'];
    
    if($_REQUEST['refer']!='')
        $_SESSION['refer'] = $_REQUEST['refer']; 
}else {
   $provider                     = $_REQUEST['provider'];
    $_SESSION['portal_username'] = $_REQUEST['provider'];
    //for logout
    $refer                       = $_REQUEST['refer'];
    if($_REQUEST['refer']!='')
        $_SESSION['refer'] = $_REQUEST['refer'];
}

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

<script src="../../interface/main/jquery-latest.min.js" type="text/javascript"></script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCzLfZ_qAqcFD_6m-WPm-bcEp6fR5bGdLs&sensor=false"></script>
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

<script type='text/javascript' src='https://code.jquery.com/jquery-1.11.1.min.js'></script>
<script type='text/javascript' src='https://cdn.datatables.net/1.10.2/js/jquery.dataTables.min.js'></script>-->

    <script type="text/javascript">
        function toggle_visibility(id) {
           var view_val = id.split(/_/);
           var id1=view_val[0];
           var view=view_val[1];
           var e = document.getElementById(id1);
           if(id1=='divMain1'){
                 if(view=='full'){
                      document.getElementById('displayMode1').value = 1;
                      var e1 = document.getElementById('Main2');
                      e1.style.display = 'none';
                      var e2 = document.getElementById('FULL');
                      e2.style.display = 'none';
                       var e3 = document.getElementById('NORMAL');
                      e3.style.display = 'block';
                      var elem = document.getElementById('Main1');
                      elem.style.width = 100 + "%"; 
                      var elem1="#AddrVal_wrapper";
                      elem1.style.width = 200 + "%";
                      var elem2="#example_wrapper";
                      elem2.style.width = 200 + "%";
                 } else if(view=='nor') {
                      document.getElementById('displayMode1').value = 0;
                      var e1 = document.getElementById('Main2');
                      e1.style.display = 'block';
                      var e2 = document.getElementById('FULL');
                      e2.style.display = 'block';
                       var e3 = document.getElementById('NORMAL');
                      e3.style.display = 'none';
                      var elem = document.getElementById('Main1');
                      elem.style.width = 50 + "%"; 
                      var elem1="#AddrVal_wrapper";
                      elem1.style.width = 100 + "%";
                      var elem2="#example_wrapper";
                      elem2.style.width = 100 + "%";  
                 }
            
           }else if(id1=='divMain2') {
               if(view=='full'){
                      document.getElementById('displayMode2').value = 1;
                      var e1 = document.getElementById('Main1');
                      e1.style.display = 'none';
                      var e2 = document.getElementById('FULL1');
                      e2.style.display = 'none';
                      var e3 = document.getElementById('NORMAL1');
                      e3.style.display = 'block';
                      var elem = document.getElementById('Main2');
                      elem.style.width = 100 + "%"; 
                      var elem1="#AddrVal_wrapper";
                      elem1.style.width = 200 + "%";
                      var elem2="#example_wrapper";
                      elem2.style.width = 200 + "%";
                 } else if(view=='nor') {
                      document.getElementById('displayMode2').value = 0;  
                      var e1 = document.getElementById('Main1');
                      e1.style.display = 'block';
                      var e2 = document.getElementById('FULL1');
                      e2.style.display = 'block';
                       var e3 = document.getElementById('NORMAL1');
                      e3.style.display = 'none';
                      var elem = document.getElementById('Main2');
                      elem.style.width = 50 + "%"; 
                      var elem1="#AddrVal_wrapper";
                      elem1.style.width = 100 + "%";
                      var elem2="#example_wrapper";
                      elem2.style.width = 100 + "%";  
                 }
           }
            
        }
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
        var markersArray_Gray = []; 
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
<?php 
    $google_api_key = '';
    $get_google_api_key = sqlStatement("SELECT title FROM list_options WHERE list_id= 'allcareConfig' AND option_id='GoogleMapApiKey'");
    while($set_google_api_key = sqlFetchArray($get_google_api_key)){
        $google_api_key = $set_google_api_key['title'];
    }
?>
<div id='divContainer' style='background-color:#FFFFCC;'>
    <div style='height:auto;'>
    <div  id='Main1' style='width:50%;float:left;'>
    <center>
<!--    <a id='linkGrid1' href='#' onclick='javascript:jQuery("#divMain1").toggle(500);'>
        <h2>Patients due for Appointments</h2>
    </a>-->
     <h2>Patients due for Appointments</h2>
    </center>
    <div id="divMain1" style='margin:2%;padding:2%;height:auto;background:#FFFF66'>
        <input type="hidden" id="displayMode1" name="displayMode1" value="0" />
        <a href="#" id='FULL' style="display:block;" onclick="toggle_visibility('divMain1_full');">Full View</a>
       <a href="#" id='NORMAL' style="display:none;" onclick="toggle_visibility('divMain1_nor');">Normal View</a>
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
                                               { alert('Enter Scheduling view name'); }
                                                                                                   " /> 
        </div>
        
        <div>
            <div class="lblSelect"><b>Scheduling view:</b></div>
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
                                            confirm('Are you sure to delete the selected Scheduling view ?'))
                                        {
                                            deleteQuerySet(1);
                                        }" style='cursor:pointer'>
                                <b>Delete Scheduling view</b></a><br>
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
            <div class="lblSelect"><b>Calculations:</b></div>
            <div class="divSelect">
                <select id='selectCalc' class="btn btn-default" style="text-align:left;">
                    <option value='0'>---Select---</option>
                    <option value="1">New/Established Last Visit Dates</option>
                    <option value="8">New/Established Certification Ends</option>
                    <option value="9">New/Established Next Home Visit Days</option>
                    <option value="10">New/Established Calculated Next Visit Dates</option>
                    <option value="2">AWV Last Visit Dates</option>
                    <option value="11">AWV Next Home Visit Days</option>
                    <option value="12">AWV Calculated Next Visit Dates</option>
                    <option value="3">H & P Last Visit Dates</option>
                    <option value="13">H & P Next Home Visit Days</option>
                    <option value="14">H & P Calculated Next Visit Dates</option>
                    <option value="4">CPO/Supervision Last Visit Dates</option>
                    <option value="15">CPO/Supervision Calculated Next Visit Dates</option>
                    <option value="5">HH Certification Last Visit Dates</option>
                    <option value="16">HH Certification Next Home Visit Days</option>
                    <option value="17">HH Certification Calculated Next Visit Dates</option>
                    <option value="6">CCM Last Visit Dates</option>
                    <option value="18">CCM Next Home Visit Days</option>
                    <option value="19">CCM Calculated Next Visit Dates</option>
                    <option value="7">Sudo Last Visit Dates</option>
                    <option value="20">Sudo Next Home Visit Days</option>
                    <option value="21">Sudo Calculated Next Visit Dates</option>
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
        <div id="divselectIsDeceased">
		<div class="lblSelect"><b>Deceased Status:</b></div>
		<div class='divSelect'>
                    <select id='selectIsDeceased' class="btn btn-default"  style="text-align:left;"
                            onchange="javascript://getQueryColumns();
                                                 showPatients('Y');" multiple>
                        <option value="-1">--Select--</option>
                        <option value='-2'>All</option>
                        <option value='YES'>Yes</option>
                        <option value='NO' selected>No</option>
                   
                    </select>                                                  
		</div>
	</div> 
        <br>
        <div>
            <div class="lblSelect"><b>Visit Type:</b></div>
            <div class="divSelect">
                <select id='selectVisitTp' name="selectVisitTp" class="btn btn-default" style="text-align:left;" onChange="javascript:changeStatuslabel(this);showPatients('Y')">
                    <option value='1'>New/Established</option>
                    <option value="2">AWV</option>
                    <option value="3">H&P</option>
                    <option value="4">CPO/Supervision</option>
                    <option value="5">HH Certification</option>
                    <option value="6">CCM</option>
                    <option value="7">Sudo</option>
                </select>   
            </div>
        </div>
        <br>
        <div id="divPracticeIfActive" style="display:none;">
		<div class="lblSelect"><b>Current Status With Practice:</b></div>
		<div class='divSelect'>
                    <select id='practiceIfActive' class="btn btn-default"  style="text-align:left;"
                            onchange="javascript://getQueryColumns();
                                                 showPatients('Y');" multiple>
                        <option value="-1">--Select--</option>
                        <option value='-2'>All</option>
                        <option value='YES' selected>Yes</option>
                        <option value='NO'>No</option>
                        <option value='PENDING'>Pending</option>
                   
                    </select>                                                  
		</div>
	</div>
        <br>
        <div id="divSelectIsActive">
		<div class="lblSelect"><b id="statuslabel">Current Status With Practice:</b></div>
		<div class='divSelect'>
                    <select id='selectIsActive' class="btn btn-default"  style="text-align:left;"
                            onchange="javascript://getQueryColumns();
                                                 showPatients('Y');" multiple>
                        <option value="-1">--Select--</option>
                        <option value='-2'>All</option>
                        <option value='YES' selected>Yes</option>
                        <option value='NO'>No</option>
                        <option value='PENDING'>Pending</option>
                   
                    </select>                                                  
		</div>
	</div>
        <br>
        <div>
            <div class="lblSelect"><b>Patient List:</b></div>
            <div class="divSelect">
                <select id='selectPatientsL' name="selectPatientsL" class="btn btn-default" style="text-align:left;" onChange="javascript:showPatients('Y')">
                    <option value='0'>All</option>
                    <option value="1">Patients Due For Appointments</option>
                    <option value="2">Patients With Open Appointments</option>
                </select>   
            </div>
        </div>
        <br>
        <div id="divSelectPatientData">		
        <div class="lblSelect"><b>Patient Standard Data:</b></div>
            <div class='divSelect'>
                <select id="selectPatientData" multiple="multiple" style="height:300px;width: 200px;">
                    <option value="">--Select the query--</option>
                <?php
                /*
                $getPatientFields=sqlStatement("SELECT DISTINCT COLUMN_NAME FROM information_schema.columns
                                                WHERE Table_Name='patient_data'
                                                AND (COLUMN_NAME!='id' AND COLUMN_NAME!='pid') ORDER BY COLUMN_NAME");
                    */  
                
                $getPatientFields=sqlStatement("SELECT field_id,title
                                                FROM `layout_options`
                                                WHERE `form_id` = 'DEM' AND uor > 0 AND field_id != ''
                                                ORDER BY group_name,seq");
                
                if(sqlNumRows($getPatientFields)>0)
                {
                    while ($rowPatientFields = sqlFetchArray($getPatientFields)) 
                    {
                      //echo "<option value='" . $rowPatientFields['field_id'] . "'>".str_replace("_"," ",ucfirst($rowPatientFields['field_id']))."</option>";
                        $label = $rowPatientFields['title'];
                        $label = ($label == "")? $rowPatientFields['field_id'] : $label;
                        echo "<option value='" . $rowPatientFields['field_id'] . "'>".$label."</option>";
                    }
                }
                ?>
                </select><input type="button" name="columnupdate" value="Update Column Display" onclick="showPatients('N');" />
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
        <br>
        
	<!--<div id="divSelectPayer">
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
	</div>->
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
               <a onclick="javascript:jQuery('#AddrVal_wrapper').toggle(500);
                                      jQuery('#lblHide').hide();
                                      jQuery('#lblShow').show();jQuery('#addrvalshow').val('0');"
               style="cursor:pointer;">
                   <label id="lblHide"><b>Hide Patient Address</b></label>
               </a>
            
               <a onclick="javascript:jQuery('#AddrVal_wrapper').toggle(500);
                                      jQuery('#lblHide').show();
                                      jQuery('#lblShow').hide();jQuery('#addrvalshow').val('1');"
               style="cursor:pointer;">
                   <label id="lblShow" style="display:none;"><b>Show Patient Address</b></label>
               </a>
               <input type="hidden" id="addrvalshow" name="addrvalshow" value=""/>
               <input type="hidden" id="patshow" name="patshow" value=""/>
        </div>
        
        <br>
        <div id='divPatientsList' style='height:auto;width:auto;background: ORANGE'></div>
        
        <br>
        
        </div>
    
    </div>
    
    <div id='Main2' style='width:50%;float:left;'>
    <center>
<!--    <a id='linkGrid2' href='#' onclick='javascript:jQuery("#divMain2").toggle(500);'>
        <h2>Patients due for Encounters</h2>
    </a>-->
    <h2>Patients due for Encounters</h2> 
    </center>
    <div id="divMain2" style='margin:2%;padding:2%;height:auto;background:#FF99FF'>
        <input type="hidden" id="displayMode2" name="displayMode2" value="0" />
        <a href="#" id='FULL1' style="display:block;" onclick="toggle_visibility('divMain2_full');">Full View </a>
         <a href="#" id='NORMAL1' style="display:none;" onclick="toggle_visibility('divMain2_nor');">Normal View </a>
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
                                               { alert('Enter Scheduling view name'); }
                                                                                                   " /> 
        </div>
        
        
        <div>
            <div class="lblSelect"><b>Scheduling view:</b></div>
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
                                            confirm('Are you sure to delete the selected Scheduling view ?'))
                                        {
                                            deleteQuerySet(2);
                                        }" style='cursor:pointer'>
                                <b>Delete Scheduling view</b></a><br>
        </div>        
        <br>
        <div id="divselectEncColmsData">		
        <div class="lblSelect"><b>Patient Standard Data:</b></div> 
            <div class='divSelect'>
                <select id="selectEncColmsData" name="selectEncColmsData" multiple="multiple" style="height:300px;width: 200px;" >
                    <option value="-1">--Select the query--</option>
                <?php
                
                /*
                $getPatientFields=sqlStatement("SELECT DISTINCT COLUMN_NAME FROM information_schema.columns
                                                WHERE Table_Name='patient_data'
                                                AND (COLUMN_NAME!='id' AND COLUMN_NAME!='pid') ORDER BY COLUMN_NAME");
                    */  
                $fields = array('pd.title'=> 'Title',
                    'pd.fname'=> 'Fname',
                    'pd.lname'=> 'Lname',
                    'pd.mname'=> 'Mname',
                    'opc.pc_eventDate AS Appointment_Date' => 'Appointment Date' ,
                    'provider' => 'Provider',
                    'opc.pc_title AS Visit_Category'=>  'VisitCategory',
                    'opc.pc_comments AS Comments'=> 'Comments' ,
                    'pd.last_visit AS Last_Visit'=>' Last Visit ',
                    'pd.cert_end AS Cert_End_Date'=> 'Certification End Date' ,
                    'pd.hhs_days AS Next_Visit'=>'Next Visit ',
                    'pd.calc_next_visit AS Calc_Next_Visit'=> 'Calc Next Visit',
                    
                    'pd.awv_last_visit AS AWV_Last_Visit'=>'AWV Last Visit Date',
                    'pd.awv_hhs_days AS AWV_Next_Visit'=>'AWV Next Visit Days',
                    'pd.calc_next_awv AS AWV_Calc_Next_Visit'=> 'AWV Calculated Next Visit Date',
                    
                    'pd.hp_last_visit AS HP_Last_Visit'=>'H&P Last Visit Date',
                    'pd.hp_hhs_days AS HP_Next_Visit'=>'H&P Next Visit Days',
                    'pd.calc_next_hp AS HP_Calc_Next_Visit'=> 'H&P Calculated Next Visit Date',
                    
                    'pd.sp_last_visit AS CPO_Last_Visit'=>'Supervision Last Visit Date',
                    'pd.calc_next_sp AS CPO_Calc_Next_Visit'=> 'Supervision Calculated Next Visit Date',
                    
                    'pd.ct_last_visit AS Cert_Last_Visit'=>'Certification Last Cert Date',
                    'pd.ct_hhs_days AS Cert_Next_Visit'=>'Certification Next Cert Days',
                    'pd.calc_next_ct AS Cert_Calc_Next_Visit'=> 'Certification Calculated Next Cert Date',
                    
                    'pd.ccm_last_visit AS CCM_Last_Visit'=>'CCM Last Visit Date',
                    'pd.ccm_hhs_days AS CCM_Next_Visit'=>'CCM Next Visit Days',
                    'pd.calc_next_ccm AS CCM_Calc_Next_Visit'=> 'CCM Calculated Next Visit Date',
                    
                    'pd.sudo_last_visit AS Sudo_Last_Visit'=>'Sudo Last Visit Date',
                    'pd.sudo_hhs_days AS Sudo_Next_Visit'=>'Sudo Next Visit Days',
                    'pd.calc_next_sudo AS Sudo_Calc_Next_Visit'=> 'Sudo Calculated Next Visit Date',
                    
                    'pd.street'=> 'Street',
                    'pd.street_addr'=> 'Street Address' ,
                    'pd.suite'=> 'Suite',
                    'pd.postal_code'=> 'Postal Code',
                    'pd.city'=> 'City',
                    'pd.state'=> 'State',
                    'pd.country_code'=>  'Country Code',
                    'pd.deceased_date'=>' Deceased Date',
                    'pd.deceased_reason'=> 'Deceased Reason',
                    'pd.deceased_stat'=> 'Deceased Stat',
                    'pd.practice_status'=> 'Practice Status',
                    'pd.patient_facility'=> 'Patient Facility',
                    'pd.cpo'=> 'CPO',
                    'pd.ccm'=> 'CCM',
                    'pd.refill_due'=> 'Refill Due',
                    'pd.language'=> 'Language',
                    'pd.ethnicity'=> 'Ethnicity',
                    'pd.race'=> 'Race',
                    'pd.financial_review'=> 'Financial Review',
                    'pd.family_size'=> 'Family Size',
                    'pd.monthly_income'=>'Monthly Income' ,
                    'pd.Statement_cat'=> 'Statement Cat',
                    'pd.homeless'=> 'Homeless',
                    'pd.living_facility'=> 'Living Facility',
                    'pd.living_facility_org'=> 'Living Facility Org',
                    'pd.interpretter'=> 'Interpretter',
                    'pd.migrantseasonal'=> 'Migrantseasonal',
                    'pd.contrastart'=> 'Contrastart',
                    'pd.referral_source'=> 'Referral Source',
                    'pd.referlink'=> 'Referlink',
                    'pd.referred_date'=> 'Referred Date',
                    'pd.hhagency'=> 'HHagency',
                    'pd.vfc'=> 'VFC',
                    'pd.Insured_Type'=> 'Insured Type',
                    'pd.authorization'=> 'Authorization',
                    'pd.Copay'=> 'Copay',
                    'pd.Deduct_Fam_Ann'=> 'Deduct Fam Ann',
                    'pd.Deduct_Indiv_Ann'=> 'Deduct Indiv Ann' ,
                    'pd.indoutofpocket'=> 'Indoutofpocket',
                    'pd.familyoutofpocket'=> 'Familyoutofpocket',
                    'pd.PCP'=> 'PCP',
                    'opc.pc_endDate'=> 'pc_endDate',
                    'opc.pc_duration'=> 'pc_duration',
                    'opc.pc_time'=> 'pc_time',
                    'opc.pc_recurrfreq'=> 'pc_recurrfreq',
                    'opc.pc_startTime'=> 'pc_startTime',
                    'opc.pc_endTime'=> 'pc_endTime',
                    'opc.pc_alldayevent'=> 'pc_alldayevent',
                    'opc.pc_fee'=> 'pc_fee',
                    'opc.pc_eventstatus'=> 'pc_eventstatus',
                    'opc.pc_sharing'=> 'pc_sharing',
                    'opc.pc_language'=> 'pc_language',
                    'lo.title AS App_status'=> 'App_status',
                    'opc.pc_prefcatid' => 'pc_prefcatid' ,
                    'fc.name AS Facility' => 'Facility',
                    'opc.pc_sendalertsms' => 'pc_sendalertsms',
                    'opc.pc_sendalertemail' => 'pc_sendalertemail' ,
                    'bfc.name AS Billing_facility '=> 'Billing_facility' ,
                    'pd.DOB'=> 'DOB',
                    'pd.phone_home'=> 'Phone Home',
                    'pd.phone_biz'=> 'Phone Biz',
                    'pd.phone_contact'=> 'Phone Contact',
                    'pd.phone_cell'=> 'Phone Cell',
                    'pd.sex'=> 'Sex' ,
                    'actualprovider'=> 'Actual Provider',
                    'referrerprovider'=>  'Referrer Provider',
                    'pd.email'=> 'Email' ,
                    'op_cat.pc_catname'=> 'pc_catname',
                    'opc.pc_hometext'=> 'pc_hometext',
                    'opc.pc_counter'=> 'pc_counter',
                    'opc.pc_topic'=> 'pc_topic',
                    'opc.pc_informant'=> 'pc_informant',
                    'opc.pc_eid'=> 'pc_eid',
                    'pd.pid'=> 'pid' );
                foreach($fields as $key => $value){
                     ?><option value="<?php echo  str_replace('.', '-',str_replace(' ', '--', $key));  ?>"> <?php echo $value; ?></option><?php 
                }
                echo "</select>";
//                $getPatientFields=sqlStatement("SELECT DISTINCT pd.title,pd.fname,pd.lname,pd.mname,opc.pc_eventDate AS Appointment_Date,CONCAT(u.fname,' ',u.lname) AS Provider,opc.pc_title AS Visit_Category,opc.pc_comments AS Comments,pd.last_visit AS Last_Visit,pd.cert_end AS Cert_End_Date,
//pd.hhs_days AS Next_Visit,pd.calc_next_visit AS Calc_Next_Visit,pd.street,pd.street_addr,pd.suite,pd.postal_code,pd.city,pd.state,pd.country_code,pd.deceased_date,pd.deceased_reason,pd.deceased_stat,pd.practice_status,pd.patient_facility,pd.cpo,pd.ccm,pd.refill_due,pd.insuranceID,pd.language,pd.ethnicity,pd.race,pd.financial_review,pd.family_size,pd.monthly_income,pd.Statement_cat,pd.homeless,
//pd.living_facility,pd.living_facility_org,pd.interpretter,pd.migrantseasonal,pd.contrastart,pd.referral_source,pd.referlink,pd.referral_note,pd.referred_date,pd.hhagency,
//pd.vfc,pd.Insured_Type,pd.annual_visit,pd.authorization,pd.Copay,pd.Deduct_Fam_Ann,pd.Deduct_Indiv_Ann,pd.indoutofpocket,pd.familyoutofpocket,
//pd.PCP,opc.pc_endDate,opc.pc_duration,opc.pc_time,opc.pc_recurrfreq,opc.pc_startTime,opc.pc_endTime,opc.pc_alldayevent,opc.pc_fee,opc.pc_eventstatus,opc.pc_sharing,opc.pc_language,lo.title AS App_status,opc.pc_prefcatid,fc.name AS Facility,opc.pc_sendalertsms,opc.pc_sendalertemail,bfc.name AS Billing_facility,pd.DOB,pd.phone_home,pd.phone_biz,pd.phone_contact,pd.phone_cell,
//pd.sex,CONCAT(u1.fname,' ',u1.lname) AS Actual_Provider,CONCAT(u2.fname,' ',u2.lname) AS Referrer_Provider,pd.email,op_cat.pc_catname,opc.pc_hometext,opc.pc_counter,opc.pc_topic,opc.pc_informant,opc.pc_eid,pd.pid,pd.latitude,pd.longitude

foreach($fields as $key => $value){
    if($key == 'provider')
        $fieldset2 .= 'CONCAT(u.fname ," " ,u.lname) AS Provider,';
    elseif($key == 'actualprovider')
        $fieldset2 .= 'CONCAT(u1.fname ," " ,u1.lname) AS Actual_Provider,';
    elseif($key == 'referrerprovider')
         $fieldset2 .= 'CONCAT(u2.fname ," " ,u2.lname) AS Referrer_Provider,';
    else
        $fieldset2 .= $key.",";
    }       
    
$fieldset = rtrim($fieldset2, ',');
$fieldparamters = $fieldset; 

               
//                if(sqlNumRows($getPatientFields)>0)
//                {
//                    while ($rowPatientFields = sqlFetchArray($getPatientFields)) 
//                    {
//                      echo "<option value='" . $rowPatientFields['field_id'] . "'>".str_replace("_"," ",ucfirst($rowPatientFields['field_id']))."</option>";
//                    }
//                }
                ?>
                <input type="button" name="columnupdate" value="Update Column Display" onclick="showPatientsByEncounters();" />
            </div>
    
        </div>
        <br />
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
<img src='../../interface/pic/show_calendar.gif' align='absbottom' width='24' height='22'
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
<img src='../../interface/pic/show_calendar.gif' align='absbottom' width='24' height='22'
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
                    <select id='selectVisitCategoryEncounter' onchange="javascript:showPatientsByEncounters();" class="btn btn-default" style="text-align:left;height:150px;width:200px;" multiple>
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
               <a onclick="javascript:jQuery('#encAddrVal_wrapper').toggle(500);
                                      jQuery('#lblHideEnc').hide();
                                      jQuery('#lblShowEnc').show();jQuery('#encaddrvalshow').val('0');"
               style="cursor:pointer;">
                   <label id="lblHideEnc"><b>Hide Patient Address</b></label>
               </a>
            
               <a onclick="javascript:jQuery('#encAddrVal_wrapper').toggle(500);
                                      jQuery('#lblHideEnc').show();
                                      jQuery('#lblShowEnc').hide();jQuery('#encaddrvalshow').val('1');"
               style="cursor:pointer;">
                   <label id="lblShowEnc" style="display:none;"><b>Show Patient Address</b></label>
               </a>
               <input type="hidden" id="encaddrvalshow" name="encaddrvalshow" value=""/>
               <input type="hidden" id="encpatshow" name="encpatshow" value=""/>
        </div>
        
        
        <br>
        
        <div id='divPatientsListByEncounter' style='height:auto;width:auto;overflow-y:auto;background:ORANGE'></div>
        <br>
       
    </div>
    
    </div>
    </div>
    <div style="clear:both;"></div>
    <div id='hdmap'>
      <a onclick="javascript:jQuery('#divGoogleMap').toggle(500);
                              jQuery('#lblHideMap').hide();
                              jQuery('#lblShowMap').show();jQuery('#hsmap').val('0');"
       style="cursor:pointer;">
           <label id="lblHideMap"><b>Hide Map</b></label>
       </a>

       <a onclick="javascript:jQuery('#divGoogleMap').toggle(500);
                              jQuery('#lblHideMap').show();
                              jQuery('#lblShowMap').hide();jQuery('#hsmap').val('1');"
       style="cursor:pointer;">
           <label id="lblShowMap" style="display:none;"><b>Show Map</b></label>
       </a>
    </div>
    <input type="hidden" id="hsmap" name="hsmap" value="">
    <script>
        if(jQuery('#hsmap').val() == 0){
            jQuery('#divGoogleMap').hide();
            jQuery('#lblHideMap').hide();
            jQuery('#lblShowMap').show();
        }    
        if(jQuery('#hsmap').val() == 1 || jQuery('#hsmap').val().length === 0){
            jQuery('#divGoogleMap').show();
            jQuery('#lblHideMap').show();
            jQuery('#lblShowMap').hide();
        }        
    </script>
    
    
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

function changeStatuslabel(obj){
    if(obj.value == 1){
        jQuery('#statuslabel').html('Current Status With Practice:');
        jQuery('#divPracticeIfActive').hide();
    }
    else if(obj.value == 2){
        jQuery('#statuslabel').html('AWV Qualified:');
        jQuery('#divPracticeIfActive').show();
    }
    else if(obj.value == 3){
        jQuery('#statuslabel').html('H & P Required:');
        jQuery('#divPracticeIfActive').show();
    }
    else if(obj.value == 4){
        jQuery('#statuslabel').html('Supervision CPO Reqd:');
        jQuery('#divPracticeIfActive').show();
    }
    else if(obj.value == 5){
        jQuery('#statuslabel').html('HH Certification Reqd:');
        jQuery('#divPracticeIfActive').show();
    }
    else if(obj.value == 6){
        jQuery('#statuslabel').html('CCM Reqd:');
        jQuery('#divPracticeIfActive').show();
    }
    else if(obj.value == 7){
        jQuery('#statuslabel').html('Sudo Test Required:');
        jQuery('#divPracticeIfActive').show();
    }
}


function showPatients(isQueryChanged)
{

	jQuery('#divPatientsList').html('');	
        jQuery('#divPatientsList').html('<img src="loading.GIF"> loading...');	
    //jQuery('#divGoogleMap').html('');	
	//var queryName=jQuery('#selectQueryList').val(); 
        var patientL=jQuery('#selectPatientsL').val();             
	var providerId=jQuery('#selectProviders').val();
        var allProviders=jQuery('#hdnAllProviders').val();
        //alert('allProviders='+allProviders);
	//var payerId=jQuery('#selectPayers').val(); 
        var allPayers=jQuery('#hdnAllPayers').val();
        var visitCategoryId=jQuery('#selectVisitCategory').val(); 
        var allVisitCategories=jQuery('#hdnAllVisitCategories').val(); 
        var selectIsActive = jQuery('#selectIsActive').val();
        var practiceIfActive = jQuery('#practiceIfActive').val();
        var selectIsDeceased = jQuery('#selectIsDeceased').val();
        var patientStandardData=jQuery('#selectPatientData').val();
        var patientVisitType=jQuery('#selectVisitTp').val();
        
//        $('#loading').show(); 
	$.ajax({
		type: 'POST',
		url: "display_patients_by_query.php",	
		data: { //queryName:queryName,                        
                        patientL: patientL,
                        providerId:providerId,
                        allProviders:allProviders,allPayers:allPayers,
                        visitCategoryId:visitCategoryId,allVisitCategories:allVisitCategories,
                        patientStandardData:patientStandardData,
                        isQueryChanged:isQueryChanged,
                        selectIsActive:selectIsActive,
                        practiceIfActive:practiceIfActive,
                        selectIsDeceased:selectIsDeceased,
                        patientVisitType: patientVisitType
                },

		success: function(response)
		{
                    
                    jQuery('#divPatientsList').html(response);
                   // alert('11');
                    //showTodaysAppointments();
                   // alert('22');
                    if(patientL == 1){
                        showLocations();
                        for (var i=0;i<markersArray_Gray.length;i++) 
                        {
                            markersArray_Gray[i].setMap(null);
                        }
                        markersArray_Gray.length = 0;
                    }
                    else if(patientL == 2){
                        for (var i=0;i<markersArray.length;i++) 
                        {
                            markersArray[i].setMap(null);
                        }
                        markersArray.length = 0;
                        showGrayLocations();
                    }
                    else{
                        showLocations(); 
                        showGrayLocations();
                    }
                    
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
        var allSelectedFields = jQuery("#selectEncColmsData").val();
        var selectIsActive = jQuery('#selectIsActive').val();
        var selectIsDeceased = jQuery('#selectIsDeceased').val();

	$.ajax({
		type: 'POST',
		url: "display_patients_by_encounter.php",	
		data: {schedulingProviderId:schedulingProviderId,
                        allSchedulingProviders:allSchedulingProviders,
                        fromDate:fromDate,toDate:toDate,
                        visitCategoryId:visitCategoryId,
                        allVisitCategories:allVisitCategories,
                        allSelectedFields:allSelectedFields,
                        selectIsActive:selectIsActive,
                        selectIsDeceased:selectIsDeceased,
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
                        
             //marker_provider.setIcon('https://gmaps-samples.googlecode.com/svn/trunk/markers/blue/blank.png);			
//marker_provider.setIcon('https://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=P|FF0000|000000');
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

             //marker_provider.setIcon('https://gmaps-samples.googlecode.com/svn/trunk/markers/blue/blank.png);			
//marker_provider_enc.setIcon('https://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=P|009900|000000');
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
                    //marker_appointment.setIcon('https://maps.google.com/mapfiles/ms/icons/blue.png');
                    //marker_appointment.setIcon('https://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=A|0000FF|000000');
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
        //var addresses=document.getElementsByName("hdnAddress");
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
            var country = encodeURIComponent(address[4]);
            var latitude = encodeURIComponent(address[5]);
            var longitude = encodeURIComponent(address[6]);
            var pid = encodeURIComponent(address[7]);
            //alert(address);
//            jQuery.getJSON('geocode.php?addr='+address, function(data) { 
//                var results = data.results;
//                status = data.status;
            
            if(address_patient != "$*$$*$$*$$*$$*$$*$$*$"){
                if(latitude!="" && longitude!=""){
                    var lat = latitude;
                    var lon = longitude;
                    var latlon = lat +","+lon;
                    if (lat != "" & lon != "") 
                        {
                            $.ajax({
                                type: 'get',
                                cache: false,
                                url: "https://maps.googleapis.com/maps/api/geocode/json?latlng="+latlon+"&sensor=false",
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
                                    var formatedAddr = exactaddress_patient.split(",");
                                    // Eliminate firt word from address
                                    //formatcorrectAddress = correctAddress.substring( correctAddress.indexOf(" ") + 1, correctAddress.length );
                                    formatcorrectAddress = correctAddress;
                                    var streetformat = formatedAddr[0].substring( formatedAddr[0].indexOf(" ") + 1, formatedAddr[0].length );
                                    var completeAddr = streetformat+","+formatedAddr[1]+","+formatedAddr[2]+" "+formatedAddr[3]+","+formatedAddr[4];
                                    var displayAddr = formatedAddr[0]+","+formatedAddr[1]+","+formatedAddr[2]+" "+formatedAddr[3]+","+formatedAddr[4];
                                    var cAdd = formatcorrectAddress.split(" ").join("");
                                    var comAdd = completeAddr.split(" ").join("");
                                    if(cAdd.toLowerCase() != comAdd.toLowerCase()){
                                        //$("#AddrVal > tbody").append('<tr><td>'+ patientName +'</td><td>'+ addressPatient +'</td><td>'+ JSON.stringify(obj["results"][0]["formatted_address"]) +'</td></tr>');
                                        $("#AddrVal").dataTable().fnAddData( [
                                            patientName,
                                            displayAddr,
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

                            //marker.setIcon('https://maps.google.com/mapfiles/ms/icons/yellow.png');
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
                            var formatedAddr = exactaddress_patient.split(",");
                            var completeAddr = formatedAddr[0]+","+formatedAddr[1]+","+formatedAddr[2]+" "+formatedAddr[3]+","+formatedAddr[4];
                            patientAddSplit = show_address.split('-<br>');
                            patientName = patientAddSplit[0];
                            $("#AddrVal").dataTable().fnAddData( [
                                patientName,
                                completeAddr,
                                "Sorry! No Match Found"
                            ]);
                        }
                }
                else{
                        $.ajax({
                                type: 'get',
                                cache: false,
                                url: "https://maps.googleapis.com/maps/api/geocode/json?address="+street+","+city+","+state+","+zip+","+country+"&sensor=false&key=<?php echo $google_api_key; ?>",
                                success: function(response) {
                                    var coordinateobj = JSON.parse(JSON.stringify(response) );
                                    var coordinates = coordinateobj["results"][0]["geometry"];
                                    var lat = coordinates.location.lat;
                                    var lon = coordinates.location.lng;
                                    var latlon = lat +","+lon;
                                 $.ajax({
                                     type: 'get',
                                     cache: false,
                                     url: "geocode.php?pid="+pid+"&lat="+lat+"&lon="+lon,
                                     success: function(response) {
                                         
                                     }
                                 });   
                                 
                        if (lat != "" & lon != "") 
                        {
                            $.ajax({
                                type: 'get',
                                cache: false,
                                url: "https://maps.googleapis.com/maps/api/geocode/json?latlng="+latlon+"&sensor=false",
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
                                    var formatedAddr = exactaddress_patient.split(",");
                                    // Eliminate firt word from address
                                    //formatcorrectAddress = correctAddress.substring( correctAddress.indexOf(" ") + 1, correctAddress.length );
                                    formatcorrectAddress = correctAddress;
                                    var streetformat = formatedAddr[0].substring( formatedAddr[0].indexOf(" ") + 1, formatedAddr[0].length );
                                    var completeAddr = streetformat+","+formatedAddr[1]+","+formatedAddr[2]+" "+formatedAddr[3]+","+formatedAddr[4];
                                    var displayAddr = formatedAddr[0]+","+formatedAddr[1]+","+formatedAddr[2]+" "+formatedAddr[3]+","+formatedAddr[4];
                                    var cAdd = formatcorrectAddress.split(" ").join("");
                                    var comAdd = completeAddr.split(" ").join("");
                                    if(cAdd.toLowerCase() != comAdd.toLowerCase()){
                                        //$("#AddrVal > tbody").append('<tr><td>'+ patientName +'</td><td>'+ addressPatient +'</td><td>'+ JSON.stringify(obj["results"][0]["formatted_address"]) +'</td></tr>');
                                        $("#AddrVal").dataTable().fnAddData( [
                                            patientName,
                                            displayAddr,
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

                            //marker.setIcon('https://maps.google.com/mapfiles/ms/icons/yellow.png');
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
                            var formatedAddr = exactaddress_patient.split(",");
                            var completeAddr = formatedAddr[0]+","+formatedAddr[1]+","+formatedAddr[2]+" "+formatedAddr[3]+","+formatedAddr[4];
                            patientAddSplit = show_address.split('-<br>');
                            patientName = patientAddSplit[0];
                            $("#AddrVal").dataTable().fnAddData( [
                                patientName,
                                completeAddr,
                                "Sorry! No Match Found"
                            ]);
                        }

                    }});
                }
                
              }  
              
            //}, i * 500);
/*****************************************************/
        //if(i == 15) return false;
        });
        
        map.setZoom(12);
                          
	//}            

}

var marker_gray = new google.maps.Marker({
           map: map, position: ''
});

function showGrayLocations()
{
        var addressesbackk = document.getElementsByName("hdnAddressBackk");
        
        for (var i=0;i<markersArray_Gray.length;i++) 
        {
            markersArray_Gray[i].setMap(null);
        }
        
            // alert('ADDRS LrL= '+addresses.length);   
	var get_address=0;
        var get_addressBack=0;
	
        
	jQuery.each(addressesbackk,function(m,val){
		
/**************************************************************************/
	    
            //var geocoder = new google.maps.Geocoder();
            var address_patient = jQuery("#hdnAddressBackk_"+m).val();
            var show_address=jQuery("#hdnDetailsBackk_"+m).val();
//alert('ADDRS_D = '+jQuery("#hdnDetails_"+i).val());
            
            //setTimeout( function () {
            //var address = encodeURIComponent(address_patient);
            var address = address_patient.split("$*$");
            var street = encodeURIComponent(address[0]);
            var city = encodeURIComponent(address[1]);
            var state = encodeURIComponent(address[2]);
            var zip = encodeURIComponent(address[3]);
            var country = encodeURIComponent(address[4]);
            var latitude = encodeURIComponent(address[5]);
            var longitude = encodeURIComponent(address[6]);
            var pid = encodeURIComponent(address[7]);
            //alert(address);
//            jQuery.getJSON('geocode.php?addr='+address, function(data) { 
//                var results = data.results;
//                status = data.status;
            
            if(address_patient != "$*$$*$$*$$*$$*$$*$$*$"){
                if(latitude!="" && longitude!=""){
                    var lat = latitude;
                    var lon = longitude;
                    var latlon = lat +","+lon;
                    if (lat != "" & lon != "") 
                        {
                            $.ajax({
                                type: 'get',
                                cache: false,
                                url: "https://maps.googleapis.com/maps/api/geocode/json?latlng="+latlon+"&sensor=false",
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
                                    var formatedAddr = exactaddress_patient.split(",");
                                    // Eliminate firt word from address
                                    //formatcorrectAddress = correctAddress.substring( correctAddress.indexOf(" ") + 1, correctAddress.length );
                                    formatcorrectAddress = correctAddress;
                                    var streetformat = formatedAddr[0].substring( formatedAddr[0].indexOf(" ") + 1, formatedAddr[0].length );
                                    var completeAddr = streetformat+","+formatedAddr[1]+","+formatedAddr[2]+" "+formatedAddr[3]+","+formatedAddr[4];
                                    var displayAddr = formatedAddr[0]+","+formatedAddr[1]+","+formatedAddr[2]+" "+formatedAddr[3]+","+formatedAddr[4];
                                    var cAdd = formatcorrectAddress.split(" ").join("");
                                    var comAdd = completeAddr.split(" ").join("");
                                    if(cAdd.toLowerCase() != comAdd.toLowerCase()){
                                        //$("#AddrVal > tbody").append('<tr><td>'+ patientName +'</td><td>'+ addressPatient +'</td><td>'+ JSON.stringify(obj["results"][0]["formatted_address"]) +'</td></tr>');
                                        $("#AddrVal").dataTable().fnAddData( [
                                            patientName,
                                            displayAddr,
                                            correctAddress
                                        ]);
                                    }
                                },
                                error: "Unknown Place"
                            });
                            var myLatlng = new google.maps.LatLng(lat, lon);
                            map.setCenter(myLatlng);
                            marker_gray = new google.maps.Marker({
                                       map: map,
                                       position: myLatlng

                            });

                            //marker.setTitle(jQuery("#hdnAddress_"+get_address).val());
                            marker_gray.setTitle(exactaddress_patient);

                            //marker.setIcon('https://maps.google.com/mapfiles/ms/icons/yellow.png');
                            marker_gray.setIcon('images/grey.png');
                            markersArray_Gray.push(marker_gray);
                            var infowindow = new google.maps.InfoWindow();
                            var service = new google.maps.places.PlacesService(map);

                            //var show_address=jQuery("#hdnDetails_"+get_address).val();
        //alert('add '+i+':'+jQuery("#hdnAddress_"+i).val());
        //alert('show_address '+i+':'+jQuery("#hdnDetails_"+i).val());
                            google.maps.event.addListener(marker_gray, 'click', function() {

                                  infowindow.setContent(show_address.toString());
                                  infowindow.open(map, this);

                                //get_address++;
                            });

                            google.maps.event.addListener(marker_gray, 'dblclick', function() {

                            //map.setZoom(3);

                            });


                            get_addressBack++;

                        } 
                        else{
                            var exactaddress_patient = address_patient.split("$*$").join(", ");
                            var formatedAddr = exactaddress_patient.split(",");
                            var completeAddr = formatedAddr[0]+","+formatedAddr[1]+","+formatedAddr[2]+" "+formatedAddr[3]+","+formatedAddr[4];
                            patientAddSplit = show_address.split('-<br>');
                            patientName = patientAddSplit[0];
                            $("#AddrVal").dataTable().fnAddData( [
                                patientName,
                                completeAddr,
                                "Sorry! No Match Found"
                            ]);
                        }
                }
                else{
                        $.ajax({
                                type: 'get',
                                cache: false,
                                url: "https://maps.googleapis.com/maps/api/geocode/json?address="+street+","+city+","+state+","+zip+","+country+"&sensor=false&key=<?php echo $google_api_key; ?>",
                                success: function(response) {
                                    var coordinateobj = JSON.parse(JSON.stringify(response) );
                                    var coordinates = coordinateobj["results"][0]["geometry"];
                                    var lat = coordinates.location.lat;
                                    var lon = coordinates.location.lng;
                                    var latlon = lat +","+lon;
                                 $.ajax({
                                     type: 'get',
                                     cache: false,
                                     url: "geocode.php?pid="+pid+"&lat="+lat+"&lon="+lon,
                                     success: function(response) {
                                         
                                     }
                                 });   
                                 
                        if (lat != "" & lon != "") 
                        {
                            $.ajax({
                                type: 'get',
                                cache: false,
                                url: "https://maps.googleapis.com/maps/api/geocode/json?latlng="+latlon+"&sensor=false",
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
                                    var formatedAddr = exactaddress_patient.split(",");
                                    // Eliminate firt word from address
                                    //formatcorrectAddress = correctAddress.substring( correctAddress.indexOf(" ") + 1, correctAddress.length );
                                    formatcorrectAddress = correctAddress;
                                    var streetformat = formatedAddr[0].substring( formatedAddr[0].indexOf(" ") + 1, formatedAddr[0].length );
                                    var completeAddr = streetformat+","+formatedAddr[1]+","+formatedAddr[2]+" "+formatedAddr[3]+","+formatedAddr[4];
                                    var displayAddr = formatedAddr[0]+","+formatedAddr[1]+","+formatedAddr[2]+" "+formatedAddr[3]+","+formatedAddr[4];
                                    var cAdd = formatcorrectAddress.split(" ").join("");
                                    var comAdd = completeAddr.split(" ").join("");
                                    if(cAdd.toLowerCase() != comAdd.toLowerCase()){
                                        //$("#AddrVal > tbody").append('<tr><td>'+ patientName +'</td><td>'+ addressPatient +'</td><td>'+ JSON.stringify(obj["results"][0]["formatted_address"]) +'</td></tr>');
                                        $("#AddrVal").dataTable().fnAddData( [
                                            patientName,
                                            displayAddr,
                                            correctAddress
                                        ]);
                                    }
                                },
                                error: "Unknown Place"
                            });
                            var myLatlng = new google.maps.LatLng(lat, lon);
                            map.setCenter(myLatlng);
                            marker_gray = new google.maps.Marker({
                                       map: map,
                                       position: myLatlng

                            });

                            //marker.setTitle(jQuery("#hdnAddress_"+get_address).val());
                            marker_gray.setTitle(exactaddress_patient);

                            //marker.setIcon('https://maps.google.com/mapfiles/ms/icons/yellow.png');
                            marker_gray.setIcon('images/grey.png');
                            markersArray_Gray.push(marker_gray);
                            var infowindow = new google.maps.InfoWindow();
                            var service = new google.maps.places.PlacesService(map);

                            //var show_address=jQuery("#hdnDetails_"+get_address).val();
        //alert('add '+i+':'+jQuery("#hdnAddress_"+i).val());
        //alert('show_address '+i+':'+jQuery("#hdnDetails_"+i).val());
                            google.maps.event.addListener(marker_gray, 'click', function() {

                                  infowindow.setContent(show_address.toString());
                                  infowindow.open(map, this);

                                //get_address++;
                            });

                            google.maps.event.addListener(marker_gray, 'dblclick', function() {

                            //map.setZoom(3);

                            });


                            get_addressBack++;

                        } 
                        else{
                            var exactaddress_patient = address_patient.split("$*$").join(", ");
                            var formatedAddr = exactaddress_patient.split(",");
                            var completeAddr = formatedAddr[0]+","+formatedAddr[1]+","+formatedAddr[2]+" "+formatedAddr[3]+","+formatedAddr[4];
                            patientAddSplit = show_address.split('-<br>');
                            patientName = patientAddSplit[0];
                            $("#AddrVal").dataTable().fnAddData( [
                                patientName,
                                completeAddr,
                                "Sorry! No Match Found"
                            ]);
                        }

                    }});
                }
                
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
        var country = encodeURIComponent(address[4]);
        var latitude = encodeURIComponent(address[5]);
        var longitude = encodeURIComponent(address[6]);
        var pid = encodeURIComponent(address[7]);
        
        if(address_patient != "$*$$*$$*$$*$$*$$*$$*$"){
            if(latitude != "" && longitude != ""){
                var lat = latitude;
                var lon = longitude;
                var latlon = lat +","+lon;
                
                if (lat != "" & lon != "") 
                    { 
                        $.ajax({
                            type: 'get',
                            cache: false,
                            url: "https://maps.googleapis.com/maps/api/geocode/json?latlng="+latlon+"&sensor=false",
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
                                var formatedAddr = exactaddress_patient.split(",");
                                // Eliminate firt word from address
                                //formatcorrectAddress = correctAddress.substring( correctAddress.indexOf(" ") + 1, correctAddress.length );
                                formatcorrectAddress = correctAddress;
                                var streetformat = formatedAddr[0].substring( formatedAddr[0].indexOf(" ") + 1, formatedAddr[0].length );
                                var completeAddr = streetformat+","+formatedAddr[1]+","+formatedAddr[2]+" "+formatedAddr[3]+","+formatedAddr[4];
                                var displayAddr = formatedAddr[0]+","+formatedAddr[1]+","+formatedAddr[2]+" "+formatedAddr[3]+","+formatedAddr[4];
                                var cAdd = formatcorrectAddress.split(" ").join("");
                                var comAdd = completeAddr.split(" ").join("");
                                if(cAdd.toLowerCase() != comAdd.toLowerCase()){
                                    $("#encAddrVal").dataTable().fnAddData( [
                                        patientName,
                                        displayAddr,
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
                //marker_enc.setIcon('https://maps.google.com/mapfiles/ms/icons/pink.png');
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
                    var formatedAddr = exactaddress_patient.split(",");
                    var completeAddr = formatedAddr[0]+","+formatedAddr[1]+","+formatedAddr[2]+" "+formatedAddr[3]+","+formatedAddr[4];
                    patientAddSplit = show_address.split('-<br>');
                    patientName = patientAddSplit[0];
                    $("#encAddrVal").dataTable().fnAddData( [
                        patientName,
                        completeAddr,
                        "Sorry! No Match Found"
                    ]);
                }
            }
            else{
                $.ajax({
                            type: 'get',
                            cache: false,
                            url: "https://maps.googleapis.com/maps/api/geocode/json?address="+street+","+city+","+state+","+zip+","+country+"&sensor=false&key=<?php echo $google_api_key; ?>",
                            success: function(response) {
                                var coordinateobj = JSON.parse(JSON.stringify(response) );
                                var coordinates = coordinateobj["results"][0]["geometry"];
                                var lat = coordinates.location.lat;
                                var lon = coordinates.location.lng;
                                var latlon = lat +","+lon;
                                //document.write(response);
                             $.ajax({
                                     type: 'get',
                                     cache: false,
                                     url: "geocode.php?pid="+pid+"&lat="+lat+"&lon="+lon,
                                     success: function(response) {
                                         
                                     }
                                 });       
                    if (lat != "" & lon != "") 
                    { 
                        $.ajax({
                            type: 'get',
                            cache: false,
                            url: "https://maps.googleapis.com/maps/api/geocode/json?latlng="+latlon+"&sensor=false",
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
                                var formatedAddr = exactaddress_patient.split(",");
                                // Eliminate firt word from address
                                //formatcorrectAddress = correctAddress.substring( correctAddress.indexOf(" ") + 1, correctAddress.length );
                                formatcorrectAddress = correctAddress;
                                var streetformat = formatedAddr[0].substring( formatedAddr[0].indexOf(" ") + 1, formatedAddr[0].length );
                                var completeAddr = streetformat+","+formatedAddr[1]+","+formatedAddr[2]+" "+formatedAddr[3]+","+formatedAddr[4];
                                var displayAddr = formatedAddr[0]+","+formatedAddr[1]+","+formatedAddr[2]+" "+formatedAddr[3]+","+formatedAddr[4];
                                var cAdd = formatcorrectAddress.split(" ").join("");
                                var comAdd = completeAddr.split(" ").join("");
                                if(cAdd.toLowerCase() != comAdd.toLowerCase()){
                                    $("#encAddrVal").dataTable().fnAddData( [
                                        patientName,
                                        displayAddr,
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
                //marker_enc.setIcon('https://maps.google.com/mapfiles/ms/icons/pink.png');
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
                    var formatedAddr = exactaddress_patient.split(",");
                    var completeAddr = formatedAddr[0]+","+formatedAddr[1]+","+formatedAddr[2]+" "+formatedAddr[3]+","+formatedAddr[4];
                    patientAddSplit = show_address.split('-<br>');
                    patientName = patientAddSplit[0];
                    $("#encAddrVal").dataTable().fnAddData( [
                        patientName,
                        completeAddr,
                        "Sorry! No Match Found"
                    ]);
                }
                            }});
            }    
                
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
    //var payerId=jQuery('#selectPayers').val(); 
    var allPayers=jQuery('#hdnAllPayers').val();
    var visitCategoryId=jQuery('#selectVisitCategory').val(); 
    var allVisitCategories=jQuery('#hdnAllVisitCategories').val(); 
     var selectIsActive = jQuery('#selectIsActive').val();
     var practiceIfActive = jQuery('#practiceIfActive').val();
     var selectIsDeceased = jQuery('#selectIsDeceased').val();
     var selectVisitTp = jQuery('#selectVisitTp').val();
     var displayMode = jQuery('#displayMode1').val();
     var selectPatientsL = jQuery('#selectPatientsL').val();
     var addrvalshow = jQuery('#addrvalshow').val();
     var hsmap = jQuery('#hsmap').val();
     
     var patshow = jQuery('#patshow').val();
    $.ajax({
            type: 'POST',
            url: "save_query_set.php",	
            data:{app_enc:1,queryName:queryName,querySetName:querySetName,
                   patientStandardData:patientStandardData,
                   providerId:providerId,
                   visitCategoryId:visitCategoryId,
                   allProviders:allProviders,allPayers:allPayers,
                   allVisitCategories:allVisitCategories,
                   active:selectIsActive,
                   deceased:selectIsDeceased,
                   selectVisitTp:selectVisitTp,
                   displayMode:displayMode,
                   selectPatientsL: selectPatientsL,
                   addrvalshow:addrvalshow,
                   patshow:patshow,
                   hsmap:hsmap,
                   practiceIfActive:practiceIfActive
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
    var allSelectedFields = jQuery("#selectEncColmsData").val();
    var displayMode2 = jQuery('#displayMode2').val();
    var encaddrvalshow = jQuery('#encaddrvalshow').val();
    var encpatshow = jQuery('#encpatshow').val();
    var hsmap = jQuery('#hsmap').val();
    
    $.ajax({
            type: 'POST',
            url: "save_query_set.php",	
            data:{app_enc:2,querySetName:querySetName,providerIdEncounter:providerIdEncounter,
                allProvidersEncounter:allProvidersEncounter,
                date_from:date_from,date_to:date_to,
                visitCategoryIdEncounter:visitCategoryIdEncounter,
                allVisitCategoriesEncounter:allVisitCategoriesEncounter,
                allSelectedFields:allSelectedFields,
                displayMode2:displayMode2,
                encaddrvalshow:encaddrvalshow,
                encpatshow:encpatshow,
                hsmap:hsmap
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
                var active=setArray[6].slice(1);
                active=active.slice(0,-1);
                var practiceIfActive=setArray[14].slice(1);
                practiceIfActive=practiceIfActive.slice(0,-2);
                var deceased=setArray[7].slice(1);
                deceased=deceased.slice(0,-1);
                
                var selectVisitTp = setArray[8];  
                
                
                jQuery("#selectVisitTp option[value=" + selectVisitTp +"]").prop("selected","selected") ;
                if(selectVisitTp == 1){
                    jQuery('#statuslabel').html('Current Status With Practice:');
                    jQuery('#divPracticeIfActive').hide();
                }
                else if(selectVisitTp == 2){
                    jQuery('#statuslabel').html('AWV Qualified:');
                    jQuery('#divPracticeIfActive').show();
                }
                else if(selectVisitTp == 3){
                    jQuery('#statuslabel').html('H & P Required:');
                    jQuery('#divPracticeIfActive').show();
                }
                else if(selectVisitTp == 4){
                    jQuery('#statuslabel').html('Supervision CPO Reqd:');
                    jQuery('#divPracticeIfActive').show();
                }
                else if(selectVisitTp == 5){
                    jQuery('#statuslabel').html('HH Certification Reqd:');
                    jQuery('#divPracticeIfActive').show();
                }
                else if(selectVisitTp == 6){
                    jQuery('#statuslabel').html('CCM Reqd:');
                    jQuery('#divPracticeIfActive').show();
                }
                else if(selectVisitTp == 7){
                    jQuery('#statuslabel').html('Sudo Test Required:');
                    jQuery('#divPracticeIfActive').show();
                }
                
                var displayMode = setArray[9];
                var selectPatientsL = setArray[10];
                var addrvalshow = setArray[11];
                var patshow = setArray[12];
                var hsmap = setArray[13];
                
                jQuery('#addrvalshow').val(addrvalshow);
                jQuery('#patshow').val(patshow);
                jQuery('#hsmap').val(hsmap);
                
                if(jQuery('#hsmap').val() == 0){
                    jQuery('#divGoogleMap').hide();
                    jQuery('#lblHideMap').hide();
                    jQuery('#lblShowMap').show();
                }    
                if(jQuery('#hsmap').val() == 1){
                    jQuery('#divGoogleMap').show();
                    jQuery('#lblHideMap').show();
                    jQuery('#lblShowMap').hide();
                }  
                
                jQuery('#selectQueryList').val(setArray[0]);
                
                jQuery('#selectPatientData').val('');
                //jQuery('#selectPOSFields').val('');
                jQuery('#selectProviders').val('');
                //jQuery('#selectPayers').val(''); 
                jQuery('#selectVisitCategory').val(''); 
                 jQuery('#selectIsActive').val('');
                 jQuery('#practiceIfActive').val('');
                jQuery('#selectIsDeceased').val(''); 
                
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
               
                /*
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
                */
                
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
                
                //Practice is Active
                if(active==='All' || active==='')
                {
                    jQuery("#selectIsActive option[value=-2]").prop("selected","selected") ;
                }
                else
                {
                    var activeArray=active.split(',');                
                    for(var i=0;i<activeArray.length;i++)
                    {
                        jQuery("#selectIsActive option[value=" + activeArray[i] +"]").prop("selected","selected") ;
                    }
                }
                //NEW/ESTB Practice is Active
                if(practiceIfActive==='All' || practiceIfActive==='')
                {
                    jQuery("#practiceIfActive option[value=-2]").prop("selected","selected") ;
                }
                else
                {
                    var practiceIfActiveArray=practiceIfActive.split(',');                
                    for(var i=0;i<practiceIfActiveArray.length;i++)
                    {
                        jQuery("#practiceIfActive option[value=" + practiceIfActiveArray[i] +"]").prop("selected","selected") ;
                    }
                }
                //deceased Status
                if(deceased==='All' || deceased==='')
                {
                    jQuery("#selectIsDeceased option[value=-2]").prop("selected","selected") ;
                }
                else
                {
                    var deceasedArray=deceased.split(',');                
                    for(var i=0;i<deceasedArray.length;i++)
                    {
                        jQuery("#selectIsDeceased option[value=" + deceasedArray[i] +"]").prop("selected","selected") ;
                    }
                }
                
                jQuery("#selectPatientsL option[value=" + selectPatientsL +"]").prop("selected","selected") ;
                //getQueryColumns();
                showPatients('N');
                if(displayMode == 1){
                    toggle_visibility('divMain1_full');
                }
                if(displayMode == 0){
                    toggle_visibility('divMain1_nor');
                } 
                
                
                
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
                //visitCategories=visitCategories.slice(0,-1);
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
                 var patientsData=setArray[4].slice(1);
                patientsData=patientsData.slice(0,-2);
                
                var displayMode = setArray[5];
                
                var encaddrvalshow = setArray[6];
                var encpatshow = setArray[7];
                var hsmap = setArray[8];
                
                jQuery('#encaddrvalshow').val(encaddrvalshow);
                jQuery('#encpatshow').val(encpatshow);
                jQuery('#hsmap').val(hsmap);
                
                if(jQuery('#hsmap').val() == 0){
                    jQuery('#divGoogleMap').hide();
                    jQuery('#lblHideMap').hide();
                    jQuery('#lblShowMap').show();
                }    
                if(jQuery('#hsmap').val() == 1){
                    jQuery('#divGoogleMap').show();
                    jQuery('#lblHideMap').show();
                    jQuery('#lblShowMap').hide();
                } 
                
                jQuery('#selectEncColmsData').val('');
                 
                if( patientsData==='')
                {
                    jQuery("#selectEncColmsData option[value='-1']").prop("selected","selected") ;                    
                }
                else
                {   
                    var patientsDataArray=patientsData.split(',');     
                    for(var i=0;i<patientsDataArray.length;i++)
                    {
//                        var str = patientsDataArray[i];
//                        var patientfield = str.replace('.', '-');
                        jQuery("#selectEncColmsData option[value=" + patientsDataArray[i] +"]").prop("selected","selected") ;     
                    }                     
                }   
              
                showPatientsByEncounters();
                
                if(displayMode == 1){
                    toggle_visibility('divMain2_full');
                }
                if(displayMode == 0){
                    toggle_visibility('divMain2_nor');
                } 
                
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
        var selectIsDeceased = jQuery('#selectIsDeceased').val();
        var selectIsActive = jQuery('#selectIsActive').val();
        if(selectCalc == 1 || selectCalc == 8 || selectCalc == 9 || selectCalc == 10){
            jQuery.ajax({
                    type: 'POST',
                    url: "update_attr.php",
                    data: {                       
                            selectCalc:selectCalc,
                            selectIsDeceased:selectIsDeceased,
                            selectIsActive: selectIsActive 
                         },
                    beforeSend: function(){
                        jQuery('#calmsg').html("Please Wait...");
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
        else if(selectCalc == 2 || selectCalc == 11 || selectCalc == 12){
            jQuery.ajax({
                    type: 'POST',
                    url: "update_awvattr.php",	
                    data: {                       
                            selectCalc:selectCalc,
                            selectIsDeceased:selectIsDeceased,
                            selectIsActive: selectIsActive
                          },
                    beforeSend: function(){
                        jQuery('#calmsg').html("Please Wait...");
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
        else if(selectCalc == 3 || selectCalc == 13 || selectCalc == 14){
            jQuery.ajax({
                    type: 'POST',
                    url: "update_hpattr.php",	
                    data: {                       
                            selectCalc:selectCalc,
                            selectIsDeceased:selectIsDeceased,
                            selectIsActive: selectIsActive
                          },
                    beforeSend: function(){
                        jQuery('#calmsg').html("Please Wait...");
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
        else if(selectCalc == 4 || selectCalc == 15){
            jQuery.ajax({
                    type: 'POST',
                    url: "update_spattr.php",	
                    data: {                       
                            selectCalc:selectCalc,
                            selectIsDeceased:selectIsDeceased,
                            selectIsActive: selectIsActive
                          },
                    beforeSend: function(){
                        jQuery('#calmsg').html("Please Wait...");
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
        else if(selectCalc == 5 || selectCalc == 16 || selectCalc == 17){
            jQuery.ajax({
                    type: 'POST',
                    url: "update_ctattr.php",	
                    data: {                       
                            selectCalc:selectCalc,
                            selectIsDeceased:selectIsDeceased,
                            selectIsActive: selectIsActive
                          },
                    beforeSend: function(){
                        jQuery('#calmsg').html("Please Wait...");
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
        else if(selectCalc == 6 || selectCalc == 18 || selectCalc == 19){
            jQuery.ajax({
                    type: 'POST',
                    url: "update_ccmattr.php",	
                    data: {                       
                            selectCalc:selectCalc,
                            selectIsDeceased:selectIsDeceased,
                            selectIsActive: selectIsActive
                          },
                    beforeSend: function(){
                        jQuery('#calmsg').html("Please Wait...");
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
        else if(selectCalc == 7 || selectCalc == 20 || selectCalc == 21){
            jQuery.ajax({
                    type: 'POST',
                    url: "update_sudoattr.php",	
                    data: {                       
                            selectCalc:selectCalc,
                            selectIsDeceased:selectIsDeceased,
                            selectIsActive: selectIsActive
                       },
                    beforeSend: function(){
                        jQuery('#calmsg').html("Please Wait...");
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
        
}

</script>
</body>
</html>
 
