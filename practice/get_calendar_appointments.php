<?php 
require_once("verify_session.php");

$selectedprovider   = $_REQUEST['selectedprovider'];
$visitType   = $_REQUEST['visitType']; 

$pc_aids            = '';
if(is_array($selectedprovider)){
    $query2          = '';
    $providerslist2  = '';
    $pc_aids         = '';
    foreach($selectedprovider as $pkey => $pvalue){
        $query2             .= " `users`  REGEXP ('".":\"".$pvalue."\"') OR ";
        $providerslist2     .= " $pvalue ,";
    }

    $query = rtrim($query2, " OR ");
    $providerslist = rtrim($providerslist2, " ,");
    if($providerslist != '')
        $pc_aids = " ope.pc_aid IN ($providerslist) AND ";
}else{
    $query      = " `users`  REGEXP ('".":\"".$selectedprovider."\"') ";
    $pc_aids    = " ope.pc_aid = '$selectedprovider' AND ";
}

$sql_apppatients3 = sqlStatement("SELECT visit_categories FROM tbl_allcare_facuservisit WHERE $query");
while($row_apppatients3 = sqlFetchArray($sql_apppatients3)){
     $array[] = unserialize($row_apppatients3['visit_categories']);
}

$dataArray = array();
for($j = 0; $j<count($array); $j++){
    foreach($array[$j] as $arraykey){
         $dataArray[] = $arraykey;
    }
}
$enc_val = '';
$dataarray = array_unique($dataArray);

// Now check visit categories from visittypemapping table "tbl_allcare_visittypemapping"
$vtype = implode("','",$visitType);
$sql = sqlStatement("SELECT visit_category FROM  tbl_allcare_visittypemapping WHERE visit_type IN ('".$vtype."')");

while($vrow = sqlFetchArray($sql)):
    $vrowArr[] = $vrow['visit_category'];
endwhile;

$arrInrst = array_intersect($vrowArr,$dataarray);

foreach($arrInrst as $arrayval){
    $enc_val .= $arrayval.",";
}

$enc_value      = rtrim($enc_val,",");

$sql12          ='';
$today1         = date("Y-m-d");
$today_event1   = explode("-","$today");
if($enc_value  != ''){
    $sql13 = sqlStatement("SELECT ope.pc_eid
         FROM patient_data p INNER JOIN openemr_postcalendar_events ope ON p.pid=ope.pc_pid
         inner join openemr_postcalendar_categories pc on pc.pc_catid=ope.pc_catid 
         inner join list_options lo on lo.option_id=ope.pc_apptstatus
         WHERE $pc_aids  p.practice_status = 'YES' AND ope.pc_eventDate='$today1' AND p.deceased_stat != 'YES' AND (p.deceased_date = '' OR p.deceased_date = '0000-00-00 00:00:00' ) and ope.pc_catid IN ($enc_value) group by ope.pc_eventDate");   
    $row_app31 = sqlFetchArray($sql13);
    if(empty($row_app31)){
        echo " <div class='day-event1 row-eq-height' id='app'  date-day=$today_event1[2] date-month=$today_event1[1] date-year=$today_event1[0] data-number='1' style='padding:10px 10px 0px 0px; background-color: #F2F2F2; height:400px; display:block;'>";
        //echo "<div class='addpatient'><img src='images/patient_record.png' alt='add patient' title='Add New Patient' width='40px'></div><div class='addapp'><img src='images/createappointment.png' alt='add appointment' title='Add New Appointment' width='35px'></div>";
         echo "<div style='padding-top:140px;'><center><h2>No Appointments</h2></center></div>";
        echo "</div>";
        $no_app=1;

    }

    $sql12=sqlStatement("SELECT ope.pc_eventDate,ope.pc_startTime
              FROM patient_data p INNER JOIN openemr_postcalendar_events ope ON p.pid = ope.pc_pid
              inner join openemr_postcalendar_categories pc on pc.pc_catid=ope.pc_catid 
              inner join list_options lo on lo.option_id=ope.pc_apptstatus
              WHERE $pc_aids p.practice_status = 'YES' AND p.deceased_stat != 'YES' AND (p.deceased_date = '' OR p.deceased_date = '0000-00-00 00:00:00' ) and ope.pc_catid IN ($enc_value) group by ope.pc_eventDate");
    $lab_cnt=0;
    while($row_app3=sqlFetchArray($sql12)){
        $date=$row_app3['pc_eventDate'];
        $time=$row_app3['pc_startTime'];
        $starttimeh = substr($time, 0, 2) + 0;
        $starttimem = substr($time, 3, 2);
        $startampm = 'AM';
        if ($starttimeh >= 12) { // p.m. starts at noon and not 12:01
            $startampm = 'PM';
          if ($starttimeh > 12) $starttimeh -= 12;
        }
        $allow='YES';

        if($allow=='YES' && $date!=''){
            $dateSrc =  $date; 
            $dateTime1 = date_create( $dateSrc);
            $app=$dateTime1->format("Y_n_j");
            $app_date=explode("_","$app");
            $today = date("Y-m-d");
            $today_event=explode("-","$today");
            ?>
            <div class="day-event row row-eq-height"  date-day="<?php echo $app_date[2] ; ?>" date-month="<?php echo $app_date[1] ; ?>" date-year="<?php echo $app_date[0] ; ?>"  data-number="1"></div>
            <?php 
        }else if($allow=='NO' && $date==date("Y-m-d") && $no_app!=1){
            if($lab_cnt==0){ 
                echo "<div class='day-event1 row-eq-height' id='app'  date-day=$app_date[2]  date-month=$app_date[1] date-year=$app_date[0] data-number='1' style='padding:10px 10px 0px 0px; background-color: #F2F2F2; height:400px; display:block;'>";
                ?>
                <?php
                echo "<div style='padding-top:140px;'><center><h2>No Appointments</h2></center></div>";
                echo "</div>";
                $lab_cnt++;
            }
        }
    }
}
echo "<input type='hidden' value = '$enc_value' name ='hiddenenc_value' id='hiddenenc_value'>";
?>
