<?php
require_once("../../verify_session.php");
require_once("$srcdir/patient.inc");
$keyword             = isset($_REQUEST['keyword'])           ? $_REQUEST['keyword']                         : '';
$pc_keywords_andor   = isset($_REQUEST['pc_keywords_andor']) ? $_REQUEST['pc_keywords_andor']               : '';
$pc_category         = isset($_REQUEST['pc_category'])       ? $_REQUEST['pc_category']                     : array();
$pc_visittype        = isset($_REQUEST['pc_visittype'])      ? $_REQUEST['pc_visittype']                    : array();
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


// get layout names
$set_layout_names_field_id = array();
$get_layout_names = sqlStatement("SELECT title,field_id FROM layout_options WHERE form_id = 'DEM' AND uor <> 0");
while($set_layout_names= sqlFetchArray($get_layout_names)){
    $set_layout_names_field_id[$set_layout_names['field_id']] = $set_layout_names['title'];
}
    

?>

<div class="col-sm-4" id="vcontenar">
    <select id="vcolumns" multiple="">
        <option value="0">Provider</option>
        <option value="1">Date</option>  
        <option value="2">Patient Name</option>
        <option value="3">Start Time</option> 
        <option value="4">End Time</option>
        <option value="5">Facility</option>  
        <option value="6">Category</option>
        <option value="7">All Day</option>
    </select>
</div>
<table id="search-table" class="table table-striped table-bordered dt-responsive nowrap"  cellspacing="0" width="100%" style="font-size:14px;">
    <thead>
        <tr>
            <th>Provider</th>
            <th>Date</th>
            <th>Patient Name </th>
            <th>Start Time</th>
            <th>End Time</th>
            <th>Facility</th>
            <th>Category</th>
            <th>All Day</th>
            <th>Comments</th>
            <?php
            foreach($set_layout_names_field_id as $skey =>$svalue){
                echo "<th>".$svalue."</th>";
            }
            ?>
        </tr>
    </thead>
    <tbody>
    <?php 
    $data = 0;
    $querystring    = " WHERE ";
    $querystring    .= " (pc_hometext like '%$keyword%' OR u.fname LIKE '%$keyword%'  OR u.mname LIKE '%$keyword%'  OR u.lname LIKE '%$keyword%' OR  u.id LIKE '%$keyword%' OR p.fname LIKE '%$keyword%'  OR p.mname LIKE '%$keyword%'  OR p.lname LIKE '%$keyword%' OR  p.id LIKE '%$keyword%' ";

    if(!empty($pc_category ) && count($pc_category) != 0){
        foreach($pc_category as $ckey => $cvalue){
            $pc_category_value .= $cvalue.",";
        }
        $querystring    .= " $pc_keywords_andor pc_catid IN (".rtrim($pc_category_value,",").") ) ";
    }else{
        $querystring    .= " ) ";
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

    $set_visitlists = '';
    $plistvisttype  = '';
    if(!empty($pc_visittype) && count($pc_visittype) != 0){
        $pc_visittype_str = implode("','",$pc_visittype);
        $plistvisttype = implode(",",$pc_visittype);
        $sql = sqlStatement("SELECT * FROM tbl_allcare_visittypemapping WHERE visit_type IN ('".$pc_visittype_str."')");
        $vCategories = array();
        while($row = sqlFetchArray($sql)):
            $vCategories[] = $row['visit_category'];
        endwhile;
        
        $vCategoriesStr = implode(",",$vCategories);
        if($vCategoriesStr != ""):
            $querystring    .= " AND pc_catid IN (".$vCategoriesStr.") ";
        endif;
        
    }
    
    /*
     * insert default filter values
     */
    $getdatafilter = sqlStatement("SELECT * FROM tbl_providerportal_filters WHERE userid = '".$_SESSION['portal_userid']."' AND screen_name = 'search_patient_appointments'");
    $setdatafilter = sqlFetchArray($getdatafilter);
    if(!empty($setdatafilter)){
        $updatefilter = sqlStatement("UPDATE tbl_providerportal_filters SET mpt_pc_keywords_andor = '$pc_keywords_andor',mpt_pc_category = '$pc_category_value',`mpt_pc_visittype`= '$plistvisttype',`mpt_pc_providers`= '$pc_providers_value',`mpt_pc_facilities`= '$pc_facilities_value', `date` = NOW() WHERE userid = '".$_SESSION['portal_userid']."' AND screen_name = 'search_patient_appointments'");
    }else{
        $insertfilter = sqlStatement("INSERT INTO tbl_providerportal_filters (`userid`,`mpt_pc_keywords_andor`, `date`,`screen_name`,`mpt_pc_category`,`mpt_pc_visittype`,`mpt_pc_providers`,`mpt_pc_facilities`) VALUES ('".$_SESSION['portal_userid']."','$pc_keywords_andor', NOW(),'search_patient_appointments','$pc_category_value','$plistvisttype', '$pc_providers_value', '$pc_facilities_value') ");
    }
    $get_patient_appointment = sqlStatement("SELECT pc_eventDate,(SELECT CONCAT(fname, ' ', lname ) FROM users WHERE id = pc_aid) as providername,(SELECT CONCAT(fname, ' ', lname ) FROM patient_data WHERE pid = pc_pid) as patient_name,pc_title,(SELECT name FROM facility WHERE id = pc_facility ) as facilityname,DATE_FORMAT(pc_startTime,'%h:%i %p') AS start_time,DATE_FORMAT(pc_endTime,'%h:%i %p') AS end_time,pc_alldayevent,pc_hometext,p.* FROM openemr_postcalendar_events e INNER JOIN users u ON u.id = e.pc_aid INNER JOIN patient_data p ON pc_pid = pid $querystring");
    while($set_patient_appointment = sqlFetchArray($get_patient_appointment)){
        echo "<tr>";
            echo "<td><a data-modalsize='modal-md' data-frameheight='350' data-bodypadding='0' data-href='../add_edit_custom_provider_event.php?prov=true&eid=&startampm=&starttimeh=&userid=&starttimem=&date=&catid=' data-toggle='modal' data-target='#modalwindow' id='editproviderschedule' style='color:black; ' title='Edit Provider Schedule'><span class='glyphicon glyphicon-pencil'></span></a>  ".$set_patient_appointment['providername']."</td>";
            echo "<td>".$set_patient_appointment['pc_eventDate']."</td>";
            echo "<td>".$set_patient_appointment['patient_name']."</td>";
            echo "<td>".$set_patient_appointment['start_time']."</td>";
            echo "<td>".$set_patient_appointment['end_time']."</td>";
            echo "<td>".$set_patient_appointment['facilityname']."</td>";
            echo "<td>".$set_patient_appointment['pc_title']."</td>";
            echo "<td>"; 
                if($set_patient_appointment['pc_alldayevent'] == 1) 
                    echo "YES"; 
                else 
                    echo "NO"; 
            echo "</td>";
            echo "<td>".$set_patient_appointment['pc_hometext']."</td>";
            foreach($set_layout_names_field_id as $skey =>$svalue){
                echo "<th>".$set_patient_appointment[$skey]."</th>";
            }
        echo "</tr>";
        $data = 1;
    }
    ?>
    </tbody>
</table>
