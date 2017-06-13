<?php
require_once("../../verify_session.php");

//echo "<pre>"; print_r($_POST); echo "</pre>";
$fields = [];
$layoutQry = sqlStatement("SELECT * FROM `layout_options` WHERE form_id = 'ELIGIBILITY' ORDER BY group_name,seq");
while($layoutRes = sqlFetchArray($layoutQry)){
    $fields[$layoutRes['field_id']] = $layoutRes['title'];
}

//echo "<pre>"; print_r($fields); exit;

 $patElig = [];
 $patient_ids = implode(",", $_POST['pat_ids']);
 $eligQry = sqlStatement("SELECT * FROM `tbl_eligibility_response_data` t1 WHERE t1.pid IN (".$patient_ids.") AND 
                        t1.created_date = 
                            (SELECT MAX(created_date) FROM tbl_eligibility_response_data t2 
                            WHERE t1.pid=t2.pid) 
                        GROUP BY t1.pid");
 while ($eligRes=sqlFetchArray($eligQry)) {
     $patElig["p".$eligRes['pid']] = '';
     foreach($fields as $key=>$value){
         $eligValue = $eligRes[$key];
         if($eligValue != ''){
            $patElig["p".$eligRes['pid']].= "\t".$value." : ".$eligValue."\n";
         }
     }
     
 }
echo json_encode($patElig);