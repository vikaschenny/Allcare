<?php
require_once("../globals.php");
require_once("../../library/formdata.inc.php");
require_once("../../library/globals.inc.php");
?>
<body class="body_top" style="background-color:#FFFFCC;">
<?php
if(isset($_POST['submit_btn'])){
    $ignoreAuth=true;
    $query1 = sqlStatement("SELECT b.id, a.pc_aid, a.pc_pid, b.encounter, b.date FROM openemr_postcalendar_events AS a INNER JOIN form_encounter AS b ON a.pc_pid = b.pid AND DATE( a.pc_time ) = DATE( b.date ) AND rendering_provider ='' Group by id,pc_aid");

    while($query1_exc = sqlFetchArray($query1)){
            $id = $query1_exc[id];
            $data[$id][] = $query1_exc['pc_aid'];
            $date[$id] = $query1_exc['pc_pid'];
            $enc[$id] = $query1_exc['encounter'];
            $encdos[$id] = $query1_exc['date'];
    }

    $text ="";
    foreach($data as $key =>$value){ 
            if(sizeof($data[$key]) == 1){
                    $query2 = sqlStatement("UPDATE form_encounter SET rendering_provider = ".$value[0]." WHERE id =".$key);
                    $text .= "Updated rendering_provider = ". $value[0] ." for encounter = ".$enc[$key].", DOS = ". $encdos[$key] ." AND patientid = ". $date[$key] ."\n";
            }else{
                    $text .= "Not updated rendering_provider for encounter = ".$enc[$key].", DOS = ". $encdos[$key] ." AND patientid = ". $date[$key] . " because appointment provider and Fee Sheet provider dont match \n";

            }
    }

    $query3 = sqlStatement("SELECT id,provider_id,encounter,pid,date FROM `form_encounter` where rendering_provider =''");
    while($query3_exc = sqlFetchArray($query3)){ 
            $id = $query3_exc['id'];
            $aid = $query3_exc['provider_id'];
            $pid = $query3_exc['pid'];
            $encount = $query3_exc['encounter'];
            $encountdos = $query3_exc['date'];
            if($pid > 0){
                    $query4 = sqlStatement("UPDATE form_encounter SET rendering_provider = ".$aid." WHERE id =".$id);
                    $text .= "Updated rendering_provider = ".$aid." for encounter = ".$encount. ", DOS = ". $encountdos ." AND patientid = ". $pid."\n";
            }else{
                    $text .= "Not updated rendering_provider for encounter = ".$encount.", DOS = ". $encountdos ." AND patientid = ". $pid ." because there is no rendering provider mentioned in Fee Sheet\n";
            }
    }
    file_put_contents('../../../../logs/rendering_provider'.date('Y-m-d H-i-s').'.log', $text, FILE_APPEND);
echo nl2br($text);    
echo "<p><strong>updated successfully rendering_provider</strong></p>" ;
} 

?>
<form name="fn" method="post" action="">
    Bulk Update of Rendering Provider in Patient Encounter Screen Either From Appointment Provider Id or From Feesheet Rendering Provider:<br>
<input type="submit" name="submit_btn" id="submit_btn" value="Update" />
</form>
</body>    
