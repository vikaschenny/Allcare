<?php
require_once("../../verify_session.php");
require_once("$srcdir/patient.inc");
$keyword             = isset($_REQUEST['keyword'])           ? $_REQUEST['keyword']                         : '';
$pc_keywords_andor   = isset($_REQUEST['pc_keywords_andor']) ? $_REQUEST['pc_keywords_andor']               : '';
$pc_title            = isset($_REQUEST['pc_title'])       ? $_REQUEST['pc_title']                     : array();
$pc_category         = isset($_REQUEST['pc_category'])       ? $_REQUEST['pc_category']                     : array();
//$pc_visittype        = isset($_REQUEST['pc_visittype'])      ? $_REQUEST['pc_visittype']                    : array();
$pc_providers        = isset($_REQUEST['pc_providers'])      ? $_REQUEST['pc_providers']                    : array(); 
$pc_facilities       = isset($_REQUEST['pc_facilities'])     ? $_REQUEST['pc_facilities']                   : array();

if($_REQUEST['start'] != ''){
    $getdate = explode("-",$_REQUEST['start']);
    $start = date('Y-m-d', strtotime($getdate[1]."-".$getdate[0]."-".$getdate[2]));
}else
    $start = '';

if($_REQUEST['end'] != ''){
    $setdate = explode("-",$_REQUEST['end']);
    $end = date('Y-m-d', strtotime($setdate[1]."-".$setdate[0]."-".$setdate[2]));
}else
    $end = '';
?>
<div class="col-sm-4" id="vcontenar">
    <select id="vcolumns" multiple="">
        <option value="0">Provider</option>
        <option value="1">Start Date</option>  
        <option value="2">End Date</option>
        <option value="3">Start Time</option> 
        <option value="4">End Time</option>
        <option value="5">Facility</option>  
        <option value="6">Title</option>
        <option value="7">Category</option>
        <option value="8">All Day</option>
    </select>
</div>
<table id="search-table" class="table table-striped table-bordered dt-responsive nowrap"  cellspacing="0" width="100%" style="font-size:14px;">
    <thead>
        <tr>
            <th>Provider</th>
            <th>Start Date</th>
            <th>End Date</th>
            <th>Start Time</th>
            <th>End Time</th>
            <th>Facility</th>
            <th>Title</th>
            <th>Category</th>
            <th>All Day</th>
            <th>Comments</th>
        </tr>
    </thead>
    <tbody>
    <?php 
    $data = 0;
    $querystring    = " WHERE ";
    $querystring    .= " (pc_hometext like '%$keyword%' OR fname LIKE '%$keyword%'  OR mname LIKE '%$keyword%'  OR lname LIKE '%$keyword%' OR  u.id LIKE '%$keyword%' ) ";

    if(!empty($pc_category ) && count($pc_category) != 0){
        foreach($pc_category as $ckey => $cvalue){
            $pc_category_value .= $cvalue.",";
        }
        $querystring    .= " $pc_keywords_andor pc_prefcatid IN (".rtrim($pc_category_value,",").")";
    }
    
    if(!empty($pc_title ) && count($pc_title) != 0){
        foreach($pc_title as $ckey => $cvalue){
            $pc_title_value .= $cvalue.",";
        }
        $querystring    .= " $pc_keywords_andor pc_catid IN (".rtrim($pc_title_value,",").")";
    }

    if($start != '' && $end != '')
        $querystring    .= " AND pc_eventDate BETWEEN '$start' AND '$end' ";
    else if($start != '')
        $querystring    .= " AND pc_eventDate > '$start' ";
    else if($end != '')
        $querystring    .= " AND pc_eventDate < '$end'  ";

    if(!empty($pc_providers) && count($pc_providers) != 0){
        foreach($pc_providers as $pkey => $pvalue){
            $pc_providers_value .= $pvalue.",";
        }
        $querystring    .= " AND pc_aid IN (".rtrim($pc_providers_value,",").")  ";
    }

    if (!empty($pc_facilities ) && count($pc_facilities) != 0){
        foreach($pc_facilities as $fkey => $fvalue){
            $pc_facilities_value .= $fvalue.",";
        }
        $querystring    .= " AND pc_facility IN (".rtrim($pc_facilities_value,",").") ";
    }

    /*$set_visitlists = '';
    if(!empty($pc_visittype) && count($pc_visittype) != 0){
        foreach($pc_visittype as $pvkey => $pvvalue){
            $get_lists = sqlStatement("SELECT notes FROM list_options WHERE notes<> '' AND option_id = '$pvvalue' AND list_id = 'Appointment_Visit_Types'");
            while($set_lists = sqlFetchArray($get_lists)){
                $get_lists_val = sqlStatement("SELECT title FROM list_options WHERE option_id = '".trim($set_lists['notes'])."' AND list_id = 'allcareConfig'");
                while($set_lists_val = sqlFetchArray($get_lists_val)){
                    $set_visitlists .= trim($set_lists_val['title']).",";
                }
            }
        }
        $querystring    .= " AND pc_catid IN (".rtrim($set_visitlists,",").") ";
    }
     
     */

    /*
     * insert default filter values
     */
    $getdatafilter = sqlStatement("SELECT * FROM tbl_providerportal_filters WHERE userid = '".$_SESSION['portal_userid']."' AND screen_name = 'maintain_provider_schedule'");
    $setdatafilter = sqlFetchArray($getdatafilter);
    if(!empty($setdatafilter)){
        $updatefilter = sqlStatement("UPDATE tbl_providerportal_filters SET mpr_pc_keywords_andor = '$pc_keywords_andor',mpt_pc_category = '$pc_title_value',mpr_pc_category = '$pc_category_value',`mpr_pc_providers`= '$pc_providers_value',`mpr_pc_facilities`= '$pc_facilities_value', `date` = NOW() WHERE userid = '".$_SESSION['portal_userid']."' AND screen_name = 'maintain_provider_schedule'");
//        $updatefilter = sqlStatement("UPDATE tbl_providerportal_filters SET mpr_pc_keywords_andor = '$pc_keywords_andor',mpr_pc_category = '$pc_category_value',`mpr_pc_visittype`= '$set_visitlists',`mpr_pc_providers`= '$pc_providers_value',`mpr_pc_facilities`= '$pc_facilities_value', `date` = NOW() WHERE userid = '".$_SESSION['portal_userid']."' AND screen_name = 'maintain_provider_schedule'");
    }else{
        $insertfilter = sqlStatement("INSERT INTO tbl_providerportal_filters (`userid`,`mpr_pc_keywords_andor`, `date`,`screen_name`,`mpt_pc_category`,`mpr_pc_category`,`mpr_pc_providers`,`mpr_pc_facilities`) VALUES ('".$_SESSION['portal_userid']."','$pc_keywords_andor', NOW(),'maintain_provider_schedule','$pc_title_value','$pc_category_value','$pc_providers_value', '$pc_facilities_value') ");
//        $insertfilter = sqlStatement("INSERT INTO tbl_providerportal_filters (`userid`,`mpr_pc_keywords_andor`, `date`,`screen_name`,`mpr_pc_category`,`mpr_pc_visittype`,`mpr_pc_providers`,`mpr_pc_facilities`) VALUES ('".$_SESSION['portal_userid']."','$pc_keywords_andor', NOW(),'maintain_provider_schedule','$pc_category_value','$set_visitlists', '$pc_providers_value', '$pc_facilities_value') ");
    }
    $get_provider_schedule = sqlStatement("SELECT pc_eid,pc_eventDate,pc_endDate,(SELECT pc_catname FROM  `openemr_postcalendar_categories` WHERE pc_catid = e.pc_prefcatid) as category,(SELECT CONCAT(fname, ' ', lname ) FROM users WHERE id = pc_aid) as providername,pc_title,(SELECT name FROM facility WHERE id = pc_facility ) as facilityname,DATE_FORMAT(pc_startTime,'%h:%i %p') AS start_time,DATE_FORMAT(pc_endTime,'%h:%i %p') AS end_time,pc_alldayevent,pc_hometext FROM openemr_postcalendar_events e INNER JOIN users u ON u.id = e.pc_aid $querystring");
    while($set_provider_schedule = sqlFetchArray($get_provider_schedule)){
        echo "<tr>";
            echo "<td><a data-modalsize='modal-md' data-frameheight='350' data-bodypadding='0' data-href='../add_edit_custom_provider_event.php?prov=true&eid=".$set_provider_schedule['pc_eid']."&startampm=&starttimeh=&userid=&starttimem=&date=&catid=' data-toggle='modal' data-target='#modalwindow' id='editproviderschedule' style='color:black; ' title='Edit Provider Schedule'><span class='glyphicon glyphicon-pencil'></span></a>  ".$set_provider_schedule['providername']."</td>";
            echo "<td>".$set_provider_schedule['pc_eventDate']."</td>";
            $endD = $set_provider_schedule['pc_eventDate'];
            if($set_provider_schedule['pc_endDate'] != '0000-00-00'):
                $endD = $set_provider_schedule['pc_endDate'];
            endif;
            echo "<td>".$endD."</td>";
            echo "<td>".$set_provider_schedule['start_time']."</td>";
            echo "<td>".$set_provider_schedule['end_time']."</td>";
            echo "<td>".$set_provider_schedule['facilityname']."</td>";
            echo "<td>".$set_provider_schedule['pc_title']."</td>";
            echo "<td>".$set_provider_schedule['category']."</td>";
            echo "<td>"; 
                if($set_provider_schedule['pc_alldayevent'] == 1) 
                    echo "YES"; 
                else 
                    echo "NO"; 
            echo "</td>";
            echo "<td>".$set_provider_schedule['pc_hometext']."</td>";
        echo "</tr>";
        $data = 1;
    }
    ?>
    </tbody>
</table>
