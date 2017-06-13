<?php
$ignoreAuth=true; // ignore the standard authentication for a regular OpenEMR user

include_once('../../interface/globals.php');
 
include_once("$srcdir/api.inc");
require_once("$srcdir/patient.inc");
require_once("$srcdir/options.inc.php");
require_once("$srcdir/htmlspecialchars.inc.php");
include_once("$srcdir/acl.inc");

$posted_data   = $_POST;
//echo "<pre>"; print_r($posted_data); echo "</pre>";
$patientid          = $posted_data['patient_dropdown'];
$dos                = $posted_data['dos'];
$visit_category     = $posted_data['visit_category'];
$facility           = $posted_data['facility'];
$billing_facility   = $posted_data['billing_facility'];
$rendering_provider = $posted_data['rendering_provider'];

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
    $sqlLastEncounter = sqlStatement("SELECT MAX(encounter) as encounter, form_encounter.id, username 
        FROM form_encounter 
        INNER JOIN users ON form_encounter.rendering_provider = users.id 
        WHERE pid=$patientid AND form_encounter.rendering_provider=$rendering_provider AND form_encounter.encounter = $encounter");
    $sqlGetLastEncounter = sqlFetchArray($sqlLastEncounter);
    if(!empty($sqlGetLastEncounter)){
        $insertform = sqlStatement("INSERT INTO forms (date, encounter, form_name, form_id, pid, user, groupname, authorized, deleted, formdir)
            VALUES(NOW(),".$sqlGetLastEncounter['encounter'].",'New Patient Encounter',".$sqlGetLastEncounter['id'].",$patientid,'".$sqlGetLastEncounter['username']."','Default',1, 0,'newpatient')");
        
         // log data
        $logdata= array(); 
        $data = sqlStatement("SELECT logdate from `tbl_allcare_formflag` WHERE  form_id='".$sqlGetLastEncounter['id'] . "' AND encounter_id = '".$sqlGetLastEncounter['encounter']."' AND form_name = 'Patient Encounter'");
//        $data_stmt7 = $db->prepare($data);
//        $data_stmt7->execute();  
//        $form_flag_res1 = $data_stmt7->fetchAll(PDO::FETCH_OBJ);
        //echo "<pre>"; print_r($form_flag_res1); echo "</pre>";
        while($datalog = sqlFetchArray($data)){
                $array =  unserialize($datalog['logdate']);
                $count= count($array);
        }
        $username2      = sqlStatement("SELECT username FROM users where id = $rendering_provider");
        $usernameget    = sqlFetchArray($username2);
        $username       = isset($usernameget['username'])? $usernameget['username'] : ''; 

        $res = sqlStatement("SELECT * FROM `tbl_allcare_formflag` WHERE  form_id='".$sqlGetLastEncounter['id'] . "' AND encounter_id = '".$sqlGetLastEncounter['encounter']."' AND form_name = 'Patient Encounter'");
//        $row1 = $db->prepare($res);
//        $row1->execute();  
//        $row1_res1 = $row1->fetchAll(PDO::FETCH_OBJ);
        if(empty($row1_res1)){
            $count = 0;

            $array2[] = array( 'authuser' =>$username,'Status' => 'Incomplete', 'date' => date("Y/m/d"), 'action'=>'created', 'ip_address'=>'Mobile','count'=> $count+1);
//                    $logdata = array_merge_recursive($array, $array2);
            $logdata=  serialize($array2);
            $query1 = sqlStatement("INSERT INTO tbl_allcare_formflag ( encounter_id,form_id, form_name,pending,finalized, logdate" .
                    ") VALUES ( '".$sqlGetLastEncounter['encounter']."','".$sqlGetLastEncounter['id'] ."', 'Patient Encounter',NULL, NULL, '".$logdata."' )");

//            $log_stmt = $db->prepare($query1);
//            $log_stmt->execute();  
//            $check_data = 1;
//        }else{
//            $count = isset($count)? $count: 0;

            $array2[] = array( 'authuser' =>$username,'Status' => 'Incomplete', 'date' => date("Y/m/d"), 'action'=>'updated' ,'ip_address'=>'Mobile','count'=> $count+1);
            $logdata = array_merge_recursive($array, $array2);
            $logdata= ($logdata? serialize($logdata): serialize($array2) );
            $query1 = sqlStatement("UPDATE tbl_allcare_formflag SET logdate=  '".$logdata."' WHERE encounter_id ='".$sqlGetLastEncounter['encounter']."' and form_id = '".$sqlGetLastEncounter['id'] . "' and form_name = 'Patient Encounter'"); 
//            $log_stmt = $db->prepare($query1);
//            $log_stmt->execute();  
//            $check_data = 1;
        }
                    
    }
    
}
 echo "<script>
        window.close();
        window.opener.location.href =  '../../../providers/provider_incomplete_charts.php?provider=".$rendering_provider." &form_patient=$patientid&form_to_date=$dos';
    </script>";
//echo json_encode($array);
?>