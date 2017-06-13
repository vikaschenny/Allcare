<?php
/**
 * Copyright (C) 2010 OpenEMR Support LLC
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * 2013/02/08 Minor tweaks by EMR Direct to allow integration with Direct messaging
 * 2013-03-27 by sunsetsystems: Fixed some weirdness with assigning a message recipient,
 *   and allowing a message to be closed with a new note appended and no recipient. 
 */

require_once("verify_session.php");
require_once("$srcdir/patient.inc");

$patientid              = trim($_REQUEST['patientid']);
$pc_eid                 = $_REQUEST['pc_eid'];

// mark as arrived
$set_arrived = sqlStatement("UPDATE openemr_postcalendar_events SET pc_apptstatus = '@' WHERE pc_eid = '$pc_eid' AND pc_pid = '$patientid'");

$get_visit_details = sqlStatement("SELECT pc_eventDate,pc_catid,pc_facility,pc_billing_location,pc_aid FROM openemr_postcalendar_events WHERE pc_eid = '$pc_eid' AND pc_pid = '$patientid'");
while($set_visit_details = sqlFetchArray($get_visit_details)){
    // New encounter Creation
    $dos                = $set_visit_details['pc_eventDate'];
    $visit_category     = $set_visit_details['pc_catid'];
    $facility           = $set_visit_details['pc_facility'];
    $billing_facility   = $set_visit_details['pc_billing_location'];
    $rendering_provider = trim($set_visit_details['pc_aid']);

    $getfacilityname = sqlStatement("SELECT name FROM facility where id = $facility");
    $facility_name = '';
    if(!empty($getfacilityname)){
        while($setfacilityname = sqlFetchArray($getfacilityname)){
            $facility_name = $setfacilityname['name'];
        }
    }
    $query  = sqlStatement("SELECT id as max_encounter FROM sequences");
    $array = array();
    while($setquery = sqlFetchArray($query)){
        $encounter = $setquery['max_encounter'] + 1;
        $queryseq = sqlStatement("UPDATE sequences SET id = $encounter ");
        $insert_encounter = sqlStatement("INSERT INTO form_encounter (date, facility, facility_id, pid, encounter, pc_catid, provider_id, billing_facility,rendering_provider)
            VALUES ('$dos', '$facility_name',$facility,$patientid,$encounter,$visit_category,$rendering_provider,$billing_facility,'$rendering_provider')");
        $sqlLastEncounter = sqlStatement("SELECT MAX(encounter) as encounter, form_encounter.id
            FROM form_encounter 
            WHERE pid=$patientid AND form_encounter.rendering_provider=$rendering_provider AND form_encounter.encounter = $encounter");
        $sqlGetLastEncounter = sqlFetchArray($sqlLastEncounter);
        if(!empty($sqlGetLastEncounter)){
            $insertform = sqlStatement("INSERT INTO forms (date, encounter, form_name, form_id, pid, user, groupname, authorized, deleted, formdir)
                VALUES(NOW(),".$sqlGetLastEncounter['encounter'].",'New Patient Encounter',".$sqlGetLastEncounter['id'].",$patientid,'".$_SESSION['portal_username']."','Default',1, 0,'newpatient')");

             // log data
            $logdata= array(); 
            $data = sqlStatement("SELECT logdate from `tbl_allcare_formflag` WHERE  form_id='".$sqlGetLastEncounter['id'] . "' AND encounter_id = '".$sqlGetLastEncounter['encounter']."' AND form_name = 'Patient Encounter'");
            while($datalog = sqlFetchArray($data)){
                    $array =  unserialize($datalog['logdate']);
                    $count= count($array);
            }
            $username       = $_SESSION['portal_username']; 

            $res = sqlStatement("SELECT * FROM `tbl_allcare_formflag` WHERE  form_id='".$sqlGetLastEncounter['id'] . "' AND encounter_id = '".$sqlGetLastEncounter['encounter']."' AND form_name = 'Patient Encounter'");
            if(empty($row1_res1)){
                $count = 0;

                $array2[] = array( 'authuser' =>$username,'Status' => 'Incomplete', 'date' => date("Y/m/d"), 'action'=>'created', 'ip_address'=>'Provider Portal','count'=> $count+1);
                $logdata=  serialize($array2);
                $query1 = sqlStatement("INSERT INTO tbl_allcare_formflag ( encounter_id,form_id, form_name,pending,finalized, logdate" .
                        ") VALUES ( '".$sqlGetLastEncounter['encounter']."','".$sqlGetLastEncounter['id'] ."', 'Patient Encounter',NULL, NULL, '".$logdata."' )");

            }else{
                $count = isset($count)? $count: 0;

                $array2[] = array( 'authuser' =>$username,'Status' => 'Incomplete', 'date' => date("Y/m/d"), 'action'=>'updated' ,'ip_address'=>'Provider Portal','count'=> $count+1);
                $logdata = array_merge_recursive($array, $array2);
                $logdata= ($logdata? serialize($logdata): serialize($array2) );
                $query1 = sqlStatement("UPDATE tbl_allcare_formflag SET logdate=  '".$logdata."' WHERE encounter_id ='".$sqlGetLastEncounter['encounter']."' and form_id = '".$sqlGetLastEncounter['id'] . "' and form_name = 'Patient Encounter'"); 
            }
        }
    }
    //echo "<script>
    //    window.location.href = 'provider_incomplete_charts.php?provider=".$rendering_provider." &form_patient=$patientid&form_to_date=$dos';
    //</script>";
    ?>
  <!--<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">-->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
  <!--<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>-->
    <section id= "services">
        <div class= "container-fluid">
            <div class= "row">
                <div class= "col-lg-12 col-sm-12 col-xs-12" style='padding-top:100px !important;'>
                    <div class='col-xs-12' id='Encounter_result' name='Encounter_result' > </div>
                </div>
            </div>
        </div>    
        <!--<div></div>-->
        <!--<div id="mySpinnercharts" class="spinner"><div>Loading…</div></div>-->
    </section>
    <script>
        $(document).ready(function(){
            $.ajax({
                type: "GET",
                url: "provider_encounter_charts.php",
                data: { provider        :<?php echo $rendering_provider; ?>,
                        form_patient    :<?php echo $patientid; ?>,
                        form_to_date    :<?php echo $dos; ?>},
                success: function(data, textStatus) {
                    $("#Encounter_result").html(data);
                },
                error: function(jqXHR, exception){
                    alert("failed" + jqXHR.responseText);
                }    
            });
        });
    </script>

<?php } ?>