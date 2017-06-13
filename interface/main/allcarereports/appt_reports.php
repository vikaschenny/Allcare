<?php
// Copyright (C) 2005-2010 Rod Roark <rod@sunsetsystems.com>
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.

// This report shows upcoming appointments with filtering and
// sorting by patient, practitioner, appointment type, and date.
// 2012-01-01 - Added display of home and cell phone and fixed header

require_once("../../globals.php");
require_once("../../../library/patient.inc");
require_once("$srcdir/formatting.inc.php");
require_once "$srcdir/options.inc.php";
require_once "$srcdir/formdata.inc.php";
require_once "$srcdir/appointments.inc.php";

$alertmsg = ''; // not used yet but maybe later
$patient = $_REQUEST['patient'];

if ($patient && ! $_POST['form_from_date']) {
	// If a specific patient, default to 2 years ago. 
	$tmp = date('Y') - 2;
	$from_date = date("$tmp-m-d");
} else {
	$from_date = fixDate($_POST['form_from_date'], date('Y-m-d'));
	$to_date = fixDate($_POST['form_to_date'], date('Y-m-d'));
}

$show_available_times = false;
if ( $_POST['form_show_available'] ) {
	$show_available_times = true;
}

$chk_with_out_provider = false;
if ( $_POST['with_out_provider'] ) {
	$chk_with_out_provider = true;
}

$chk_with_out_facility = false;
if ( $_POST['with_out_facility'] ) {
	$chk_with_out_facility = true;
}

//$to_date   = fixDate($_POST['form_to_date'], '');
$provider  = $_POST['form_provider'];
$facility  = $_POST['form_facility1'];  //(CHEMED) facility filter
$category=$_POST['form_apptcat'];
$fieldsarray = $_POST['selectEncColmsData'];
$example=$_POST['uid'];


//print_r($filter_id); 
//print_r($_POST['filterid_practice_status']);

//print_r($filter_value2); 
//print_r($provider);
//print_r($facility);
//print_r($category);
//print_r($fieldsarray);

$form_orderby = getComparisonOrder( $_REQUEST['form_orderby'] ) ?  $_REQUEST['form_orderby'] : 'date';

function facility_list1($selected = '', $name = 'form_facility1[]', $id='form_facility1', $allow_unspecified = true, $allow_allfacilities = true) {
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


//generate form_field
function generate_form_field1($frow, $currvalue) {
  global $rootdir, $date_init;

  $sel_value=explode("|",$currvalue);
  $currescaped = htmlspecialchars($currvalue, ENT_QUOTES);

  $data_type   = $frow['data_type'];
  $field_id    = $frow['field_id'];
  $list_id     = $frow['list_id'];
  // escaped variables to use in html
  $field_id_esc= htmlspecialchars( $field_id, ENT_QUOTES);
  $list_id_esc = htmlspecialchars( $list_id, ENT_QUOTES);

  // Added 5-09 by BM - Translate description if applicable  
  $description = (isset($frow['description']) ? htmlspecialchars(xl_layout_label($frow['description']), ENT_QUOTES) : '');
      
  // added 5-2009 by BM to allow modification of the 'empty' text title field.
  //  Can pass $frow['empty_title'] with this variable, otherwise
  //  will default to 'Unassigned'.
  // modified 6-2009 by BM to allow complete skipping of the 'empty' text title
  //  if make $frow['empty_title'] equal to 'SKIP'
  $showEmpty = true;
  if (isset($frow['empty_title'])) {
   if ($frow['empty_title'] == "SKIP") {
    //do not display an 'empty' choice
    $showEmpty = false;
    $empty_title = "Unassigned";
   }
   else {     
    $empty_title = $frow['empty_title'];
   }
  }
  else {
   $empty_title = "Unassigned";   
  }
    
  // generic single-selection list
  if ($data_type == 1) {
    echo generate_select_list1("form_$field_id", $list_id, $sel_value,
      $description, $showEmpty ? $empty_title : '');
  }
}

// Function to generate a drop-list.
//
function generate_select_list1($tag_name, $list_id, $sel_value, $title,
  $empty_name=' ', $class='', $onchange='', $tag_id = '', $custom_attributes = null )
{
  //print_r($sel_value);   
  $s = '';
  $tag_name_esc = htmlspecialchars( $tag_name, ENT_QUOTES);
  $tag_name_esc1=$tag_name_esc."[]";
  $s .= "<select name='$tag_name_esc1' multiple ";
  $tag_id_esc = $tag_name_esc;
  if ( $tag_id != '' ) {
      $tag_id_esc = htmlspecialchars( $tag_id, ENT_QUOTES);
  }   
  $s .=  " id='$tag_id_esc'";
  if ($class) $s .= " class='$class'";
  if ($onchange) $s .= " onchange='$onchange'";
  if ( $custom_attributes != null && is_array($custom_attributes) ) {
      foreach ( $custom_attributes as $attr => $val ) {
          if ( isset($custom_attributes[$attr] ) ) {
              $s .= " ".htmlspecialchars( $attr, ENT_QUOTES)."='".htmlspecialchars( $val, ENT_QUOTES)."'";
          }
      }
  }
  $selectTitle = htmlspecialchars( $title, ENT_QUOTES);
  $s .= " title='$selectTitle'>";
  $selectEmptyName = htmlspecialchars( xl($empty_name), ENT_NOQUOTES);
  
  if ($empty_name) $s .= "<option value=''";   if($sel_value=='') { $s .= " selected";
  $got_selected = TRUE; }  
  $s .= ">" . $selectEmptyName . "</option>";

  $lres = sqlStatement("SELECT * FROM list_options " .
    "WHERE list_id = ? ORDER BY seq, title", array($list_id) );
  $got_selected = FALSE;
  while ($lrow = sqlFetchArray($lres)) {
    $optionValue = htmlspecialchars( $lrow['option_id'], ENT_QUOTES);
    $s .= "<option value='$optionValue'";
    foreach($sel_value as $value) {
        if ((strlen($value) == 0 && $lrow['is_default']) ||
            (strlen($value)  > 0 && $lrow['option_id'] == $value))
        {
          $s .= " selected";
          $got_selected = TRUE;
        }
    }
    $optionLabel = htmlspecialchars( xl_list_label($lrow['title']), ENT_NOQUOTES);
    $s .= ">$optionLabel</option>\n";
  }
  foreach($sel_value as $value){
  if (!$got_selected && strlen($value) > 0) {
    $currescaped = htmlspecialchars($value, ENT_QUOTES);
    $s .= "<option value='$currescaped' selected>* $currescaped *</option>";
    $s .= "</select>";
    $fontTitle = htmlspecialchars( xl('Please choose a valid selection from the list.'), ENT_QUOTES);
    $fontText = htmlspecialchars( xl('Fix this'), ENT_NOQUOTES);
    $s .= " <font color='red' title='$fontTitle'>$fontText!</font>";
  }
  else {
    $s .= "</select>";
  }
  }
  return $s;
}

function fetchEvents1( $from_date, $to_date, $where_param = null, $fieldparamters,$fieldparamters1,$orderby_param = null ) 
{       
	$where =
		"( (e.pc_endDate >= '$from_date' AND e.pc_eventDate <= '$to_date' AND e.pc_recurrtype = '1') OR " .
  		  "(e.pc_eventDate >= '$from_date' AND e.pc_eventDate <= '$to_date') )";
	if ( $where_param ) $where .= $where_param;
	
	$order_by = "e.pc_eventDate, e.pc_startTime";
	if ( $orderby_param ) {
		$order_by = $orderby_param;
	}
	
//	echo $query = "SELECT " .
//  	"e.pc_eventDate, e.pc_endDate, e.pc_startTime, e.pc_endTime, e.pc_duration, e.pc_recurrtype, e.pc_recurrspec, e.pc_recurrfreq, e.pc_catid, e.pc_eid, " .
//  	"e.pc_title, e.pc_hometext, e.pc_apptstatus, " .
//  	"p.fname, p.mname, p.lname, p.pid, p.pubpid, p.phone_home, p.phone_cell, " .
//  	"u.fname AS ufname, u.mname AS umname, u.lname AS ulname, u.id AS uprovider_id, " .
//	"c.pc_catname, c.pc_catid " .
//  	"FROM openemr_postcalendar_events AS e " .
//  	"LEFT OUTER JOIN patient_data AS p ON p.pid = e.pc_pid " .
//  	"LEFT OUTER JOIN users AS u ON u.id = e.pc_aid " .
//	"LEFT OUTER JOIN openemr_postcalendar_categories AS c ON c.pc_catid = e.pc_catid " .
//	"WHERE $where " . 
//	"ORDER BY $order_by";
        
        $query = "SELECT" ." ". $fieldparamters." ,".$fieldparamters1." ".
  	"FROM openemr_postcalendar_events AS e " .
  	"LEFT OUTER JOIN patient_data AS p ON p.pid = e.pc_pid " .
  	"LEFT OUTER JOIN users AS u ON u.id = e.pc_aid " .
	"LEFT OUTER JOIN openemr_postcalendar_categories AS c ON c.pc_catid = e.pc_catid " .
	"WHERE $where " . 
	"ORDER BY $order_by";
       
	$res = sqlStatement( $query );
	$events = array();
	if ( $res )
	{
		while ( $row = sqlFetchArray($res) ) 
		{
			// if it's a repeating appointment, fetch all occurances in date range
			if ( $row['pc_recurrtype'] ) {
				$reccuringEvents = getRecurringEvents( $row, $from_date, $to_date );
				$events = array_merge( $events, $reccuringEvents );
			} else {
				$events []= $row;
			}
		}
	}
	
	return $events;
}

//fetch appointments
function fetchAppointments1( $from_date, $to_date, $patient_id = null, $provider_id = null, $facility_id = null, $pc_appstatus = null, $with_out_provider = null, $with_out_facility = null, $pc_catid = null,$filter_id=null,$filter_value=null ,$fieldparamters,$fieldparamters1)
{
      
       $pc_appstatus1=explode("|",$pc_appstatus);
       $fid1=explode("-",$filter_id);
       $fvalue=explode(",",$filter_value);
   
	$where = "";
           //provider_id
	if($provider_id !=null){
           foreach($provider_id as $val){
                    if(!empty($val)){
                        $pro1.="'$val'".",";
                        $pro2= rtrim($pro1,",");
                        $provider=" AND e.pc_aid IN  ($pro2)";
                }
             }
               $where .= $provider;     
         }
	if ( $patient_id ) {
		$where .= " AND e.pc_pid = '$patient_id'";
	} else {
		$where .= " AND e.pc_pid != ''";
	}		

        //facility
	$facility_filter = '';
        foreach($facility_id as $fac) {
             if(!empty($fac)){
                            $fac1.="'$fac'".",";
                            $fac2= rtrim($fac1,",");
                            $event_facility_filter = " AND ( e.pc_facility IN ($fac2)";
                            $provider_facility_filter = " OR u.facility_id IN ($fac2))";
                            $facility_filter = $event_facility_filter . $provider_facility_filter;
             }
        }
        $where .= $facility_filter;
	
	//Appointment Status Checking
	$filter_appstatus = '';
        if(!empty($pc_appstatus1)){
                    $status1='';
                    foreach($pc_appstatus1 as $status){
                        if(!empty($status)){
                        $status1.="'$status'".",";
                        $status2= rtrim($status1,",");
                         $filter_appstatus = " AND e.pc_apptstatus IN  ($status2)";
                        }
                        else {
                             
                        }
                    }
                   
                    $where .= $filter_appstatus;
            }
       
        // category
        foreach($pc_catid as $catid){
            $catid1.="'$catid'".",";
            $catid2= rtrim($catid1,",");
        }
        //echo $catid2;
        if($catid2 !=null)
        {
        $where .= " AND e.pc_catid IN (".$catid2.")"; // using intval to escape this parameter
        }
       
	//Without Provider checking
	$filter_woprovider = '';
	if($with_out_provider != ''){
		$filter_woprovider = " AND e.pc_aid = ''";
	}
	$where .= $filter_woprovider;
	
	//Without Facility checking
	$filter_wofacility = '';
	if($with_out_facility != ''){
		$filter_wofacility = " AND e.pc_facility = 0";
	}
	$where .= $filter_wofacility;
        
        //filter_id
        if($fid1[0]!=null){
            if($fvalue!=null){
                foreach($fvalue as $key => $value){
                    if($value!=''){
                        $filter_val1.="'$value'".",";
                        $filter_val2= rtrim($filter_val1,",");
                        $filter_val3 = " AND"." " ."p.".$fid1[0]." IN(".$filter_val2.")";
                    } else if($value ==''){
                        $filter_val1="'YES','NO'";
                        //$filter_val2= rtrim($filter_val1,",");
                        $filter_val3 = " AND"." " ."p.".$fid1[0]." IN($filter_val1)";
                    }    
                }
            }
            
            $where.=$filter_val3;
        }
	
	$appointments = fetchEvents1( $from_date, $to_date, $where,$fieldparamters,$fieldparamters1);
        
	return $appointments;
}

function fetchAllEvents1( $from_date, $to_date, $provider_id = null, $facility_id = null ,$selected_fields,$fieldparamters1)
{       
      
	$where = "";
        if($provider_id!=null){
            foreach($provider_id as $val1) { 
                if(!empty($val1)){
                            $provider1.="'$val1'".",";
                            $provider2= rtrim($provider1,",");
                            $provider3= " AND e.pc_aid  IN  ($provider2)";
                    }
                 }
        }
            $where .= $provider3;

	$facility_filter = '';
	if ($facility_id!=null) {
              foreach($facility_id as $facility) {
                 if(!empty($facility)){
                                $facility1.="'$facility'".",";
                                $facility2= rtrim($facility1,",");
                                $event_facility_filter = " AND e.pc_facility IN ($facility2)";
                                $provider_facility_filter = " AND u.facility_id IN ($facility2)";
                                $facility_filter = $event_facility_filter . $provider_facility_filter;
                 }
               }
	}
	
	$where .= $facility_filter;
        $appointments = fetchEvents1( $from_date, $to_date, $where,$selected_fields,$fieldparamters1 );
	return $appointments;
}

function createAvailableSlot1( $event_date, $start_time, $provider, $unique_key,$jvalue,$cat_name = "Available" )
{      
        $sel_col=array();
        $newSlot = array();
        if($jvalue==0){
            $selected=$unique_key;
            $sel_col=explode(",",$selected);
             $time=$start_time;
             $catname=$cat_name;
              for($i=0;$i<count($sel_col);$i++)
                {
                    if($sel_col[$i]=='Time'){
                        $newSlot[$sel_col[$i]] = $time;
                    }else if($sel_col[$i]=='Type'){
                        $newSlot[$sel_col[$i]] = $catname;
                    }else if($sel_col[$i]=='provider'){
                       $newSlot[$sel_col[$i]] = $provider;
                    } else {
                        $newSlot[$sel_col[$i]] = '';
                    }
                }
            
         }else{
             $time=$start_time;
             $catname=$cat_name;
            $selected=$unique_key;
            $sel_col=explode(",",$selected); 
            for($i=0;$i<count($sel_col);$i++)
                {
                    if($sel_col[$i]=='Time'){
                        $newSlot[$sel_col[$i]] = $time;
                    }else if($sel_col[$i]=='Type'){
                        $newSlot[$sel_col[$i]] = $catname;
                    }else if($sel_col[$i]=='provider') {
                        $newSlot[$sel_col[$i]] = $provider;
                    }else {
                        $newSlot[$sel_col[$i]] = '';
                    }
                }
            }
       
        
        
         //print_r($newSlot);
//	$newSlot['provider'] = $provider;
//        $newSlot['Date'] = $event_date;
//	$newSlot['Time'] = $start_time;
//	$newSlot['Type'] = $cat_name;
//     	$newSlot['pc_catid'] = $provider_fname;
//	$newSlot['uprovider_id'] = $provider_mname;
//        $newSlot['pc_endTime'] = $start_time;
       
	return $newSlot;
}
function getSlotSize1()
{    
      if ( isset( $GLOBALS['calendar_interval'] ) ) {
               
		return $GLOBALS['calendar_interval'] * 60;
	}
	return 15 * 60;
}
function getAvailableSlots1( $from_date, $to_date, $provider_id = null, $facility_id = null ,$selected_fields,$fieldparamters1)
{
        
       
	$appointments = fetchAllEvents1( $from_date, $to_date, $provider_id, $facility_id ,$selected_fields,$fieldparamters1);
        foreach($appointments as $key => $value){
            foreach($value as $key=>$val1){
                $appt_key.=$key.",";
            }
        }
        $appt_key1=rtrim($appt_key,",");
        $appt_key2=explode(",",$appt_key1);
        $unique_key=array_unique($appt_key2);
        foreach($unique_key as $key=>$val)
        {
            $appt_col.=$val.",";
        }
       $appt_col1=rtrim($appt_col,",");
        $appointments = sortAppointments( $appointments, "date" );
        $from_datetime = strtotime( $from_date." 00:00:00" );
	$to_datetime = strtotime( $to_date." 23:59:59" );
	$availableSlots = array();
	$start_time = 0;
	$date = 0;
        
	for ( $i = 0; $i < count( $appointments ); ++$i )
	{
               
		if ( $appointments[$i]['pc_catid'] == 2 ) { // 2 == In Office
			  $start_time = $appointments[$i]['Time'];
			  $date = $appointments[$i]['Date'];
			  $provider_id = $appointments[$i]['uprovider_id'];
		} else if ( $appointments[$i]['pc_catid'] == 3 ) { // 3 == Out Of Office
			continue;
		} else {
			  $start_time = $appointments[$i]['pc_endTime'];
		          $date = $appointments[$i]['Date'];
			  $provider_id = $appointments[$i]['uprovider_id'];
		}

		// find next appointment with the same provider
		$next_appointment_date = 0;
		$next_appointment_time = 0;
                for ( $j = $i+1; $j < count( $appointments ); ++$j ) {
                    if ( $appointments[$j]['uprovider_id'] == $provider_id ) {
                               
                               $next_appointment_date = $appointments[$j]['Date'];
			       $next_appointment_time = $appointments[$j]['Time'];
				break;
			}
		}
                
		 $same_day = ( strtotime( $next_appointment_date ) == strtotime( $date ) ) ? true : false;
                
		if ( $next_appointment_time && $same_day ) {
                        
			// check the start time of the next appointment
			  $start_datetime = strtotime( $date." ".$start_time );
			  $next_appointment_datetime = strtotime( $next_appointment_date." ".$next_appointment_time );
			  $curr_time = $start_datetime;
                        $j=0;
                     
			while ( $curr_time < $next_appointment_datetime - (getSlotSize1() / 2) ) {
				//create a new appointment ever 15 minutes
                          $time = date( "H:i:s", $curr_time );
                             
                               $available_slot = createAvailableSlot1( 
					$appointments[$i]['Date'], 
					$time, 
                                        $appointments[$i]['provider'],
					$appt_col1,$j++);
				$availableSlots []= $available_slot;
				$curr_time += getSlotSize1(); // add a 15-minute slot
			}
		}
	}
       
	return $availableSlots;
}

?>

<html>

<head>
    
<?php html_header_show();?>

<link rel="stylesheet" href="<?php echo $css_header;?>" type="text/css">
<!--<link rel="stylesheet" href="../css/bootstrap-3.0.3.min.css" type="text/css">
<link rel="stylesheet" href="../css/bootstrap-multiselect.css" type="text/css">-->
<title><?php xl('Appointments Report','e'); ?></title>

<script type="text/javascript" src="../../../library/overlib_mini.js"></script>
<script type="text/javascript" src="../../../library/textformat.js"></script>
<script type="text/javascript" src="../../../library/dialog.js"></script>
<script type="text/javascript" src="../../../library/js/jquery.1.3.2.js"></script>

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
<script type="text/javascript" src="../js/bootstrap-multiselect.js"></script>
<!--<script type='text/javascript' src='../js/jquery.dataTables.min.js'></script>-->
<script type="text/javascript" src="../js/bootstrap-3.0.3.min.js"></script>
<script type='text/javascript' src='../js/jquery.dataTables-1.10.7.min.js'></script>

<script type='text/javascript' src='../js/dataTables.tableTools.js'></script>
<script type='text/javascript' src='../js/dataTables.colReorder.js'></script>
<script type='text/javascript' src='../js/dataTables.colVis.js'></script>
<script type="text/javascript">

 var mypcc = '<?php echo $GLOBALS['phone_country_code'] ?>';
 
//  $(document).ready( function () { 
//      $('#dvLoading').show();
//      $("#uca").load("appointment_report_filter.php");
//      $('#dvLoading').hide();});

 function dosort(orderby) {
    var f = document.forms[0];
    f.form_orderby.value = orderby;
    f.submit();
    return false;
 }

 function oldEvt(eventid) {
    dlgopen('../../main/calendar/add_edit_event.php?eid=' + eventid, 'blank', 550, 270);
 }

 function refreshme() {
     
    // location.reload();
    document.forms[0].submit();
 }


 $('#form_apptcat').multiselect({
   //buttonClass: 'btn btn-default btn-sm',
    buttonWidth: 'auto',
    maxHeight: 50,
    includeSelectAllOption: true,
    numberDisplayed:0,
    selectAllText:'Select All',
    nonSelectedText: 'form facility1',
    selectAllValue : 'select all'
 });

function getSetNames()
{
    
     var QuerySet='';
     QuerySet=jQuery('#selectQuerySet'); 
     var screen='appt';
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
  
   var screen='appt';
   var querySetName=jQuery('#txtQuerySet').val(); 
   var facility=jQuery('#form_facility1').val();
   var facility_val=facility+" ";
   var provider=jQuery('#form_provider').val();
   var provider_val=provider+" ";
   var status=jQuery('#form_apptstatus').val();
   var status_val=status+" ";
   var category=jQuery('#form_apptcat').val();
   var category_val=category+" ";
 
   
   var appt_from_dt=jQuery('#form_from_date').val();
   var appt_to_dt=jQuery('#form_to_date').val();
   //var available_slot= $("#form_show_available").attr("checked") ? "Checked" : "Unchecked";
   var available_slot1= $('form #form_show_available').is(':checked') ? "true" : "false";
    if(available_slot1=='true'){
      aval_slot='1'; }
   else if(available_slot1=="false"){
      aval_slot='0';
   }
   //var without_provider= $("#with_out_provider").attr("checked") ? "Checked" : "Unchecked";
  var without_provider= $('form #with_out_provider').is(':checked')  ? "true" : "false" ;
   if(without_provider=="true"){
       without_provider1='1'; }
   else if(without_provider=="false"){
      without_provider1='0';
   }
   //var without_facility= $("#with_out_facility").attr("checked") ? "Checked" : "Unchecked";
    var without_facility= $('form #with_out_facility').is(':checked')  ? "true" : "false";
     if(without_facility=="true"){
           without_facility1='1'; }
       else if(without_facility=="false"){
          without_facility1='0';
       }
   var selected_fields=jQuery('#selectEncColmsData').val();
   var selected_fields1=selected_fields+" ";
   
//   var filter_id=jQuery('#form_fliter').val();
//   var filter_id_res = filter_id.split("-");
    //document.getElementById('filterid1').value = filter_id;
  $.ajax({
            type: 'POST',
            url: "save_fac_query_set.php",	
            data:{
                   querySetName:querySetName,screen:screen,facility_val:facility_val,provider_val:provider_val,category_val:category_val,
                   status_val:status_val,appt_from_dt:appt_from_dt,appt_to_dt:appt_to_dt,aval_slot:aval_slot,without_provider1:without_provider1,without_facility1:without_facility1,
                   selected_fields1:selected_fields1/*,filter_id:filter_id,filter_val:filter_val*/
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
  var screen='appt';  
  var querySetName=jQuery('#selectQuerySet').val();     
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
                var patientsData=setArray[0];
                var provider=setArray[1];
                var status=setArray[2];
                var category=setArray[3];
                var from_dt= setArray[4];
                var to_dt=setArray[5];
                var avail_slot=setArray[6];
                var without_provider=setArray[7];
                var without_facility=setArray[8];
                var sel_fields=setArray[9];
                var sel_fields1=sel_fields.trim();
                var filter_id=setArray[10];
                var filter_val=setArray[11];
               
                //facility
                jQuery('#form_facility1').val('');
                  var patientsDataArray=patientsData.split(',');     
                     for(var i=0;i<patientsDataArray.length;i++)
                       {      
                           $('#form_facility1').find('option').each(function(){
                                if($(this).val()==+ patientsDataArray[i]){
                                    $(this).attr('selected','selected');
                                }
                             });
                       }
                //provider
                jQuery('#form_provider').val('');
                  var providerArray=provider.split(',');     
                     for(var i=0;i<providerArray.length;i++)
                       {      
                           $('#form_provider').find('option').each(function(){
                                if($(this).val()==+ providerArray[i]){
                                    $(this).attr('selected','selected');
                                }
                             });
                       }
                       
                //status       
                jQuery('#form_apptstatus').val('');
                  var statusArray=status.split(',');
                  for(var i=0;i<statusArray.length;i++)
                       {      
                           //alert(statusArray[i]);
                           $('#form_apptstatus').find('option').each(function(){
                             //alert($(this).val()+"=="+statusArray[i]);
                                if($(this).val()==statusArray[i]){
                                    $(this).attr('selected','selected');
                                }
                             });
                       }
                       
                //category
                jQuery('#form_apptcat').val('');
                 var categoryArray=category.split(',');     
                     for(var i=0;i<categoryArray.length;i++)
                       {      
                            $('#form_apptcat').find('option').each(function(){
                                if($(this).val()==+ categoryArray[i]){
                                    $(this).attr('selected','selected');
                                }
                             });
                       }
                 //from_date
                 jQuery('#form_from_date').val(from_dt);
                //to_date      
                    jQuery('#form_to_date').val(to_dt);  
                //available slots
                    if(avail_slot==1){
                      $('#form_show_available').attr('checked', 1);
                    }
                 //without provider
                  if(without_provider==1)
                      $('#with_out_provider').attr('checked', 1);  
                 //without facility
                   if(without_facility==1)
                      $('#with_out_facility').attr('checked', 1);  
                  
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
                  
//                    //filter_id
//                    jQuery('#form_fliter').val('');
//                       $('#form_fliter').find('option').each(function(){
//                                if($(this).val()== filter_id){
//                                    $(this).attr('selected','selected');
//                                }
//                             });
//                    //filter_val
//                    if(filter_id!=''){
//                         if(filter_val!='')
//                          $("#uca").load("appointment_report_filter.php?field_id="+filter_id+"&filter_val="+filter_val);
//                         // $("#uca").load("appt_report_filter.php?field_id="+filter_id+"&filter_val="+filter_val);
//                         else if(filter_val=='') {
//                             filter_val="'YES','NO'";
//                              $("#uca").load("appointment_report_filter.php?field_id="+filter_id+"&filter_val="+filter_val);
//                             // $("#uca").load("appt_report_filter.php?field_id="+filter_id+"&filter_val="+filter_val);
//                          }
//                      }      
                          
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
     var screen='appt';
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
</head>

<body class="body_top">

<!-- Required for the popup date selectors -->
<div id="overDiv"
	style="position: absolute; visibility: hidden; z-index: 1000;"></div>

<span class='title'><?php xl('Report','e'); ?> - <?php xl('Appointments','e'); ?></span>

<div id="report_parameters_daterange"><?php echo date("d F Y", strtotime($from_date)) ." &nbsp; to &nbsp; ". date("d F Y", strtotime($to_date)); ?>
</div>

<form method='POST' name='theform' id='theform' action='appt_reports.php'>


<div id="report_parameters">

<table>
	<tr>
		<td width='650px'>
		<div style='float: left'>

		<table class='text'>
			<tr>
                                <td><?php echo xlt('Category')?></td>
				<td>
                                    <select id="form_apptcat" name="form_apptcat[]" multiple id="form_apptcat">
                                        <?php
                                        $categories=fetchAppointmentCategories();
                                            echo "<option value='ALL'";   foreach($category as $val2){ if($val2=='ALL')
                                                    {
                                                        echo " selected='true' ";
                                                    } 
                                            }   echo">".xlt("All")."</option>";
                                            while($cat=sqlFetchArray($categories))
                                            {
                                                echo "<option value='".attr($cat['id'])."'";
                                                foreach($category as $val2){
                                                    if($cat['id']==$val2)
                                                    {
                                                        echo " selected='true' ";
                                                    }
                                                }
                                                echo    ">".text(xl_appt_category($cat['category']))."</option>";
                                            }
                                           
                                        ?>
                                    </select>
                                </td>
				<td class='label'><?php xl('Facility','e'); ?>:</td>
                                <td><?php $facility1=implode("|",$facility);   facility_list1(strip_escape_custom($facility1), 'form_facility1[]' ,'form_facility1',true); ?>
				
				
                               <td><input type='checkbox' name='form_show_available' id='form_show_available'
					title='<?php xl('Show Available Times','e'); ?>'
					<?php  if ( $show_available_times ) echo ' checked'; ?>> <?php  xl( 'Show Available Times','e' ); ?>
				</td>
			</tr>
			<tr>
				<td class='label'><?php xl('From','e'); ?>:</td>
				<td><input type='text' name='form_from_date' id="form_from_date"
					size='10' value='<?php echo $from_date ?>'
					onkeyup='datekeyup(this,mypcc)' onblur='dateblur(this,mypcc)'
					title='yyyy-mm-dd'> <img src='../../pic/show_calendar.gif'
					align='absbottom' width='24' height='22' id='img_from_date'
					border='0' alt='[?]' style='cursor: pointer'
					title='<?php xl('Click here to choose a date','e'); ?>'></td>
				<td class='label'><?php xl('To','e'); ?>:</td>
				<td><input type='text' name='form_to_date' id="form_to_date"
					size='10' value='<?php echo $to_date ?>'
					onkeyup='datekeyup(this,mypcc)' onblur='dateblur(this,mypcc)'
					title='yyyy-mm-dd'> <img src='../../pic/show_calendar.gif'
					align='absbottom' width='24' height='22' id='img_to_date'
					border='0' alt='[?]' style='cursor: pointer'
					title='<?php xl('Click here to choose a date','e'); ?>'></td>
			</tr>
			
			<tr>
                               <td class='label'><?php xl('Provider','e'); ?>:</td>
				<td><?php

				// Build a drop-down list of providers.
				//

				$query = "SELECT id, lname, fname FROM users WHERE ".
				  "authorized = 1 $provider_facility_filter ORDER BY lname, fname"; //(CHEMED) facility filter

				$ures = sqlStatement($query);

				echo "   <select name='form_provider[]'  multiple  id='form_provider'>\n";
				echo "    <option value=''"; foreach($provider as $val1) { if ($val1=='') echo " selected";} 
                                echo  "selected"; echo" >-- " . xl('All') . " --\n";

				while ($urow = sqlFetchArray($ures)) {
					$provid = $urow['id'];
					echo "    <option value='$provid'";
                                        foreach($provider as $val1){    
                                        if ($provid == $val1) echo " selected";}
					echo ">" . $urow['lname'] . ", " . $urow['fname'] . "\n";
				}

				echo "   </select>\n";
                              
				?></td>
				<td class='label'><?php xl('Status','e'); ?>:</td>
				<td><?php $appt_status=$_POST['form_apptstatus']; 
                                          $appt_status1=implode("|",$appt_status); 
                                          
                                          generate_form_field1(array('data_type'=>1,'field_id'=>'apptstatus','list_id'=>'apptstat','empty_title'=>'All'),$appt_status1);?></td>
				
                                <td>
                                     <div id="divselectEncColmsData">	
                                       <?php   $query1= "SELECT field_id,list_id ,title FROM layout_options " .
                                                    "WHERE form_id = 'DEM' AND uor > 0 AND field_id != '' AND data_type  IN('1','4')  AND group_name='5Misc' " .
                                                    "ORDER BY  seq";
                                                 $ures1 = sqlStatement($query1); 
                                                 while($row1 = sqlFetchArray($ures1)){ 
                                                     //$field_key= ucwords($row1['field_id']);
                                                     $title=str_replace(" ","_",$row1['title']);
                                                     $field_key=$row1['field_id']." "."AS"." "."'$title'";
                                                     $filterfields[$field_key]=$row1['field_id'];
                                                 }
                                                  $fields_array = array('provider'=> 'Provider',
                                                    'e.pc_eventDate AS Date'=> 'Date',
                                                    'e.pc_startTime AS Time'=> 'Time',
                                                    'patient'=>'Patient',
                                                    'p.pubpid AS ID'=> 'ID',
                                                    'p.phone_home AS Home'=>'Home',
                                                    'p.phone_cell AS Cell'=>'Cell',
                                                    'c.pc_catname AS Type' => 'Type' ,
                                                    'e.pc_apptstatus AS Status' => 'Status',
                                                    'e.pc_hometext AS Comment' => 'Comment'
                                                      );
                                                  $fields=array_merge($fields_array,$filterfields);
                                                  //print_r($fields);
                                               ?>
                                        <div class="lblSelect"><b>Standard Data:</b></div> 
                                            <div class='divSelect'>
                                                <select id="selectEncColmsData" name="selectEncColmsData[]" multiple="multiple" style="height:180px;" >
                                                    <option value="-1">--Select the query--</option>
                                                <?php
                                              
                                               
                                                 
                                                foreach($fields as $key => $value){
                                                       $option_val=str_replace('.', '-',str_replace(' ', '--', $key));
                                                     ?><option value="<?php echo  str_replace('.', '-',str_replace(' ', '--', $key));  ?>" <?php foreach($fieldsarray as $val) { if($val==$option_val) echo "selected"; }?> > <?php echo $value; ?></option><?php 
                                                }
                                                echo "</select>";
                                                echo "<br><br>";
                                                
                               
 
                                $fields1 = array( ' e.pc_catid' => '',
                                                  'u.id AS uprovider_id'=>'',
                                                  'e.pc_endTime'=> '',
//                                                  'c.pc_catname' =>'',
                                                  'c.pc_catid '=>'', 
                                                 
                                                  );
                              
                                foreach($fields1 as $key => $value){
                                     $fieldset3 .= $key.",";
                                }   
                                $fieldsets = rtrim($fieldset3, ',');
                                $fieldparamters1 = $fieldsets; 
                                foreach($fields as $key => $value){
//                                    if($key=='patient')
//                                         $field_set4.='CONCAT(p.fname," ", p.mname," ", p.lname) AS Patient,';
//                                    if($key == 'provider')
//                                        $fieldset2 .= 'CONCAT(u.lname ," " ,u.fname) AS provider,';
//                                    else if($key!='patient')
//                                        $fieldset2 .= $key.",";
                                        if($key=='provider') {
                                              $field_val .= 'CONCAT(u.lname ," " ,u.fname) AS provider,';
                                         } else if($key=='patient'){
                                              $field_val.='CONCAT(p.fname," ", p.mname," ", p.lname) AS Patient,';
                                         }else {
                                              $field_val .= $key.",";
                                         }
                                   
                                    }       
                                //$fieldset= $field_set4.$fieldset2;
                                $fieldset1 = rtrim($field_val, ',');
                                $fieldparamters = $fieldset1; 
                                //echo $fieldparamters;
                                
                             ?>
                                    <!--<input type="button" name="columnupdate" value="Update Column Display" onclick="showAppointments();" />-->
                                </div>

                            </div>
                                </td>
                        </tr>
                        <tr>
				<td colspan="2"><input type="checkbox" name="with_out_provider" id="with_out_provider" <?php if($chk_with_out_provider) echo "checked";?>>&nbsp;<?php xl('Without Provider','e'); ?></td>
				<td colspan="2"><input type="checkbox" name="with_out_facility" id="with_out_facility" <?php if($chk_with_out_facility) echo "checked";?>>&nbsp;<?php xl('Without Facility','e'); ?></td>
			</tr>
                        <tr>
                            <td colspan="2" style="padding-bottom: 0px !important; padding-top: 15px !important;"><div style="" onload="javascript:jQuery('#btnSaveSelection').hide();">
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
                                <td class='label'><?php xl('QuerySets','e'); ?>:</td>
                                <td style="padding-bottom: 0px !important; padding-top: 15px !important;">
                                    <div class='divSelect'>
                                        <select id='selectQuerySet' onchange="javascript:setByQuerySet();" class="btn btn-default"  style="text-align:left;">
                                            <option value='-1'>---Select---</option>
                                        <?php

                                            $getQuerySets=sqlStatement("SELECT id,setname FROM tbl_allcarereports_querysets where screen='appt'");
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
		<table style='border-left: 1px solid; width: 100%; height: 100%'>
			<tr>
				<td>
				<div style='margin-left: 15px'>
                                <a href='#' class='css_button' onclick='$("#form_refresh").attr("value","true"); $("#theform").submit();'>
<!--                                <a href='#' class='css_button' onclick='submitForm();'>    -->
				<span> <?php xl('Submit','e'); ?> </span> </a> 
                                
                                <?php if ($_POST['form_refresh'] || $_POST['form_orderby'] ) { ?>
				<a href='#' class='css_button' onclick='window.print()'> 
                                    <span> <?php xl('Print','e'); ?> </span> </a> 
                                <a href='#' class='css_button' onclick='window.open("../../patient_file/printed_fee_sheet.php?fill=2","_blank")'> 
                                    <span> <?php xl('Superbills','e'); ?> </span> </a> 
                                <?php } ?></div>
				</td>
			</tr>
                        <tr>&nbsp;&nbsp;<?php xl('Most column headers can be clicked to change sort order','e') ?></tr>
		</table>
		</td>
	</tr>
</table>

</div>
<!-- end of search parameters --> <?php
if ($_POST['form_refresh'] || $_POST['form_orderby']) {
	?>
<div id="report_results">
 <?php 
        function display_db_query($totalAppontments,$appointments) {
	
        foreach($appointments as $key=> $value)
        {
            foreach($value as $key1=>$value1){
                if($key1!='pc_catid' && $key1!='uprovider_id' && $key1!='pc_endTime'){
                  $appt_key.=$key1.",";
                  $appt_val.=$value1.",";
                   
                }
            }
        }
      
        $result_id = rtrim($appt_key, ',');
        $result_id1=explode(",",$result_id);
        $uni=array_unique($result_id1);
        $result_val = rtrim($appt_val, ',');
        $result_val1=explode(",",$result_val);
        foreach($uni as $key => $value){
            $count+=count($key);
        }
       // $rows=array_chunk($result_val1,$count);
        //print_r($rows);
//        foreach($fields as $key => $value){
//          
//           $field=explode(".",$key) ;   
//           if(sizeof($field)== 2){
//             $f1[]=$field[1];
//             $v1[]=$value;
//           }
//           else {    
//               $f2[]=$field[0];
//               $v2[]=$value;
//           }
//           
//        }
//       $col_name=array_merge($f1,$f2);
//       $col_name1=array_merge($v1,$v2);
//       $columns=array_combine($col_name1,$col_name);
//      // print_r($columns);
       
        echo "<br/>";
	print("<TABLE border='1' id='patient_data' class='display'>\n");
	
	       print("<thead><tr>");
                
                 foreach($uni as $key => $value) {
                     //$header_val=ucwords($value);
		   print("<th>$value</th>");
                 }
		print("</tr></thead>\n");
                
	        print("<tfoot><tr>");
                  foreach($uni as $key => $value) {
		   print("<th>$value</th>");
                 }
               print("</tr></tfoot>");
        
	      $status_query=sqlStatement("SELECT * FROM  `list_options` WHERE list_id =  'apptstat' order by seq");
              while($status_res=sqlFetchArray($status_query)){
                  $option_val[]=$status_res['option_id'];
              }
                foreach($appointments as $key=> $value)
                {
                       print("<tr>");
                    foreach($value as $key1=>$value1){
                        if($key1!='pc_catid' && $key1!='uprovider_id' && $key1!='pc_endTime'){
                            if(in_array($value1, $option_val)) {
                                $status_query1=sqlStatement("SELECT * FROM  `list_options` WHERE list_id =  'apptstat'  AND option_id='$value1' order by seq");
                                $status_res1=sqlFetchArray($status_query1);  ?>
                                <td>&nbsp;<?php echo $status_res1['title'] ?></td>
                           <?php }else if(!in_array($value1, $option_val)){ ?>
                                    <td>&nbsp;<?php echo $value1 ?></td>
                            <?php }
                        }
                    }
                     print("</tr>\n");
                }
             ?>
                      
		<?php  xl('Total number of appointments','e'); ?>:&nbsp;<?php echo $totalAppontments; echo "<br><br>"; ?>
	     
	      
	<?php print("</table>\n"); 
}  
function display_db_table($totalAppontments,$appointments) {

                display_db_query($totalAppontments,$appointments);
            } ?>
<!--<table>

	<thead>
		<th><a href="nojs.php" onclick="return dosort('doctor')"
	<?php if ($form_orderby == "doctor") echo " style=\"color:#00cc00\"" ?>><?php  xl('Provider','e'); ?>
		</a></th>

		<th><a href="nojs.php" onclick="return dosort('date')"
	<?php if ($form_orderby == "date") echo " style=\"color:#00cc00\"" ?>><?php  xl('Date','e'); ?></a>
		</th>

		<th><a href="nojs.php" onclick="return dosort('time')"
	<?php if ($form_orderby == "time") echo " style=\"color:#00cc00\"" ?>><?php  xl('Time','e'); ?></a>
		</th>

		<th><a href="nojs.php" onclick="return dosort('patient')"
	<?php if ($form_orderby == "patient") echo " style=\"color:#00cc00\"" ?>><?php  xl('Patient','e'); ?></a>
		</th>

		<th><a href="nojs.php" onclick="return dosort('pubpid')"
	<?php if ($form_orderby == "pubpid") echo " style=\"color:#00cc00\"" ?>><?php  xl('ID','e'); ?></a>
		</th>

         	<th><?php xl('Home','e'); //Sorting by phone# not really useful ?></th>

                <th><?php xl('Cell','e'); //Sorting by phone# not really useful ?></th>
                
		<th><a href="nojs.php" onclick="return dosort('type')"
	<?php if ($form_orderby == "type") echo " style=\"color:#00cc00\"" ?>><?php  xl('Type','e'); ?></a>
		</th>
		
		<th><a href="nojs.php" onclick="return dosort('status')"
			<?php if ($form_orderby == "status") echo " style=\"color:#00cc00\"" ?>><?php  xl('Status','e'); ?></a>
		</th>

		<th><a href="nojs.php" onclick="return dosort('comment')"
	<?php if ($form_orderby == "comment") echo " style=\"color:#00cc00\"" ?>><?php  xl('Comment','e'); ?></a>
		</th>

	</thead>-->
<!--	<tbody>
		 added for better print-ability -->
	<?php

	$lastdocname = "";
	//Appointment Status Checking
       
        $form_apptstatus = $_POST['form_apptstatus'];
        $form_apptstatus1=implode("|",$form_apptstatus);
        
       
        $form_apptcat=null;
        foreach($category as $cat1) {
            if(isset($cat1))
            {
                if($cat1!="ALL")
                {
                    $form_apptcat[]=$cat1;
                }
            }
        } 
       
	//Without provider and facility data checking
	$with_out_provider = null;
	$with_out_facility = null;
       
	if( isset($_POST['with_out_provider']) ){
		$with_out_provider = $_POST['with_out_provider'];
	}
	
	if( isset($_POST['with_out_facility']) ){
		$with_out_facility = $_POST['with_out_facility'];
	}
   
        
	if($_POST['selectEncColmsData'][0] == '-1' || empty($_POST['selectEncColmsData']) ){
         
          $appointments = fetchAppointments1( $from_date, $to_date, $patient, $provider, $facility, $form_apptstatus1, $with_out_provider, $with_out_facility,$form_apptcat,$filter_id,$filter_value2, $fieldparamters,$fieldparamters1);
         
          if ( $show_available_times ) {
                   
                    $availableSlots = getAvailableSlots1( $from_date, $to_date, $provider, $facility ,$fieldparamters,$fieldparamters1);
                   // print_r($availableSlots);
                    $count=count($appointments)-1;
                    foreach($availableSlots as $key => $value){
                        $appointments[++$count]=$value;
                       
                    }
                    
                    //print_r($appointments);
                    //$appointments = array_merge( $appointments, $availableSlots );
                   
            }
            
            $appointments = sortAppointments( $appointments, $form_orderby );
            $pid_list = array();  // Initialize list of PIDs for Superbill option
            $totalAppontments = count($appointments);    
            
              display_db_table($totalAppontments,$appointments);
         }else if(isset($_POST['selectEncColmsData'])){ 
           
            $fieldsarray = $_POST['selectEncColmsData'];
            
            foreach ($fieldsarray as $key1 => $value1 ) {
//                if($value == 'provider')
//                    $fieldparamters2 .= 'CONCAT(u.lname ," " ,u.fname) AS provider,';
//                else if($value!='patient')
//                    $fieldparamters2 .= str_replace('-', '.', str_replace('--',' ',$value)).",";
//                if($value == 'patient')
//                     $fieldparamters4 .='CONCAT(p.fname," ", p.mname," ", p.lname) AS Patient,';
                 if($value1=='provider') {
                      $field_val1 .= 'CONCAT(u.lname ," " ,u.fname) AS provider,';
                 } else if($value1=='patient'){
                      $field_val1.='CONCAT(p.fname," ", p.mname," ", p.lname) AS Patient,';
                 }else {
                      $field_val1 .= str_replace('-', '.', str_replace('--',' ',$value1)).",";
                 }
                 
            }
            //$fieldparam=$fieldparamters4.$fieldparamters2;
            $selected_fields = rtrim($field_val1, ',');
            
            $appointments = fetchAppointments1( $from_date, $to_date, $patient, $provider, $facility, $form_apptstatus1, $with_out_provider, $with_out_facility,$form_apptcat,$filter_id,$filter_value2, $selected_fields,$fieldparamters1);
           
            if ( $show_available_times ) {
                   
                    $availableSlots = getAvailableSlots1( $from_date, $to_date, $provider, $facility ,$selected_fields,$fieldparamters1);
                    //print_r($availableSlots);
                    $count=count($appointments)-1;
                    foreach($availableSlots as $key => $value){
                        $appointments[++$count]=$value;
                       
                    }
                   
                    //$appointments = array_merge( $appointments, $availableSlots );
                   
            }
            $appointments = sortAppointments( $appointments, $form_orderby );
            $pid_list = array();  // Initialize list of PIDs for Superbill option
            $totalAppontments = count($appointments); 
            display_db_table($totalAppontments,$appointments); 
         }
        
        
//	 
//	if ( $show_available_times ) {
//                foreach($facility as $value){
//		$availableSlots = getAvailableSlots( $from_date, $to_date, $provider, $facility );
//		$appointments = array_merge( $appointments, $availableSlots );
//                }
//	}
     
//	$appointments = sortAppointments( $appointments, $form_orderby );
//        $pid_list = array();  // Initialize list of PIDs for Superbill option
//        $totalAppontments = count($appointments);   
	
	foreach ( $appointments as $appointment ) {
                array_push($pid_list,$appointment['pid']);
		$patient_id = $appointment['pid'];
		$docname  = $appointment['ulname'] . ', ' . $appointment['ufname'] . ' ' . $appointment['umname'];
                
        $errmsg  = "";
		$pc_apptstatus = $appointment['pc_apptstatus'];

		?>

<!--	<tr bgcolor='<?php echo $bgcolor ?>'>
		<td class="detail">&nbsp;<?php echo ($docname == $lastdocname) ? "" : $docname ?>
                <td class="detail">&nbsp;<?php echo $appointment['provider'] ?>    
		</td>

		<td class="detail"><?php echo oeFormatShortDate($appointment['pc_eventDate']) ?>
		</td>

		<td class="detail"><?php echo oeFormatTime($appointment['pc_startTime']) ?>
		</td>

		<td class="detail">&nbsp;<?php echo $appointment['fname'] . " " . $appointment['lname'] ?>
                    <td class="detail">&nbsp;<?php echo $appointment['Patient'] ?>
		</td>

		<td class="detail">&nbsp;<?php echo $appointment['pubpid'] ?></td>

        <td class="detail">&nbsp;<?php echo $appointment['phone_home'] ?></td>

        <td class="detail">&nbsp;<?php echo $appointment['phone_cell'] ?></td>

		<td class="detail">&nbsp;<?php echo xl_appt_category($appointment['pc_catname']) ?></td>
		
		<td class="detail">&nbsp;
			<?php
				//Appointment Status
				if($pc_apptstatus != ""){
					$frow['data_type']=1;
					$frow['list_id']='apptstat';
					generate_print_field($frow, $pc_apptstatus);
				}
			?>
		</td>

		<td class="detail">&nbsp;<?php echo $appointment['pc_hometext'] ?></td>

	</tr>-->

	<?php
	$lastdocname = $docname;
	}
	// assign the session key with the $pid_list array - note array might be empty -- handle on the printed_fee_sheet.php page.
        $_SESSION['pidList'] = $pid_list;
	?>
<!--	<tr>
		<td colspan="10" align="left"><?php  xl('Total number of appointments','e'); ?>:&nbsp;<?php echo $totalAppontments;?></td>
	</tr>
	</tbody>
</table>-->
  
</div>
<!-- end of search results --> <?php } else { ?>
<div class='text'><?php echo xl('Please input search criteria above, and click Submit to view results.', 'e' ); ?>
</div>
	<?php } ?> <input type="hidden" name="form_orderby"
	value="<?php echo $form_orderby ?>" /> <input type="hidden"
	name="patient" value="<?php echo $patient ?>" /> <input type='hidden'
	name='form_refresh' id='form_refresh' value='' />
       <input type='hidden' name='filterval1' id='filterval1' />
       <input type='hidden' name='filterid1' id='filterid1' value=''/>

      
<script type="text/javascript">

<?php
if ($alertmsg) { echo " alert('$alertmsg');\n"; }
?>

</script>
<script type='text/javascript'>
            
//            $(document).ready( function () {
//                
//                $('#patient_data').DataTable( {
//                    dom: 'lfrtip'
//                   
//                } );
//            } );

$(document).ready(function() {
    // Setup - add a text input to each footer cell
    $('#patient_data tfoot th').each( function () {
        var title = $('#patient_data thead th').eq( $(this).index() ).text();
        $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
    } );
 
    // DataTable
    var table = $('#patient_data').DataTable({ 
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
        "iDisplayLength": 100});
 
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
<style type="text/css">
    @import url(../../../library/dynarch_calendar.css);
</style>
<script type="text/javascript" src="../../../library/dynarch_calendar.js"></script>
<?php include_once("{$GLOBALS['srcdir']}/dynarch_calendar_en.inc.php"); ?>
<script type="text/javascript"
	src="../../../library/dynarch_calendar_setup.js"></script>
<script type="text/javascript">
 Calendar.setup({inputField:"form_from_date", ifFormat:"%Y-%m-%d", button:"img_from_date"});
 Calendar.setup({inputField:"form_to_date", ifFormat:"%Y-%m-%d", button:"img_to_date"});
</script>

</html>

