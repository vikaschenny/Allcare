<?php
require_once("{$GLOBALS['srcdir']}/sql.inc");

function getF2FById ($id, $cols = "*")
{
  return sqlQuery("select $cols from tbl_form_facetoface_transactions where id=? " .
    "order by date_of_service DESC  limit 0,1", array($id) );
}

function getF2FByPid ($pid, $cols = "*")
{
  /*$res = sqlStatement("select ft.$cols,CONCAT(u.fname,' ',u.lname) AS refer_to from tbl_form_facetoface_transactions ft INNER JOIN users u on u.id=ft.refer_to where pid=? " .
    "order by date_of_service DESC ", array($pid) );
  for ($iter = 0; $row = sqlFetchArray($res); $iter++)
    $all[$iter] = $row;
  return $all;*/
    $res = sqlStatement("select ft.$cols from tbl_form_facetoface_transactions ft INNER JOIN users u on u.id=ft.refer_to where pid=? " .
    "order by date_of_service DESC ", array($pid) );
  for ($iter = 0; $row = sqlFetchArray($res); $iter++)
    $all[$iter] = $row;
  return $all;
}
function getF2FReportsByPid ($pid, $encounter,$cols = "*")
{
  $res = sqlStatement("select ft.$cols,CONCAT(u.fname,' ',u.lname) AS refer_to from tbl_form_facetoface_transactions ft INNER JOIN users u on u.id=ft.refer_to where pid=? AND encounter=?" .
   "order by date_of_service DESC", array($pid,$encounter) );
  for ($iter = 0; $row = sqlFetchArray($res); $iter++)
    $all[$iter] = $row;
  return $all;
}
function getF2FEncounterForm($form_id, $field_id ,$cols = "*")
{
   /* echo "select $cols from lbf_data where form_id=? AND field_id=? 
    , array($form_id,$field_id)";*/
  return sqlQuery("select $cols from lbf_data where form_id=? AND field_id=?" 
    , array($form_id,$field_id) );
}

function newF2F($pid, $date_of_service, $refer_to, $notes )
{
  $body = mysql_escape_string($body);
  return sqlInsert("insert into tbl_form_facetoface_transactions ( " .
    " pid, date_of_service, refer_to,  notes " .
    ") values ( " .
    "'$pid', '$date_of_service', '$refer_to', '$notes' " .
    ")");
}

function updateF2FForm($id, $new, $create)
{
  /*******************************************************************
    $real = getPatientData($pid);
    $new['DOB'] = fixDate($new['DOB']);
    while(list($key, $value) = each ($new))
        $real[$key] = $value;
    $real['date'] = "'+NOW()+'";
    $real['id'] = "";
    $sql = "insert into patient_data set ";
    while(list($key, $value) = each($real))
        $sql .= $key." = '$value', ";
    $sql = substr($sql, 0, -2);
    return sqlInsert($sql);
  *******************************************************************/

  // The above was broken, though seems intent to insert a new patient_data
  // row for each update.  A good idea, but nothing is doing that yet so
  // the code below does not yet attempt it.

  //$new['DOB'] = fixDate($new['DOB']);
   
    //for inserting log data
    $logdata= array();
        $data = mysql_query("SELECT logdate from `tbl_allcare_formflag` WHERE  form_id=".$formid);
        while ($row = mysql_fetch_array($data,MYSQL_ASSOC)) {
            $array =  unserialize($row['logdate']);
            $count= count($array);
        }
        $res = sqlStatement("SELECT * FROM `tbl_allcare_formflag` WHERE form_id = '$formid'");
        $row1 = sqlFetchArray($res);
        $count = isset($count)? $count: 0;
        $finalized='Y'; $pending='N';
  
  if ($create) {
    $sql = "INSERT INTO tbl_form_facetoface_transactions SET  created_date = NOW()";
    foreach ($new as $key => $value) {
      if ($key == 'id') continue;
      $sql .= ", `$key` = " . pdValueOrNull($key, $value);
    }
    $db_id = sqlInsert($sql);
     $array2[] = array( 'authuser' =>$_SESSION["authUser"],'pending' => $pending,'finalized' => $finalized,'date' => date("Y/m/d"), 'action'=>'Created','count'=> $count+1,'formName'=>'F2F_transaction');
            $logdata = array_merge_recursive($array, $array2);
            $logdata= ($logdata? serialize($logdata): serialize($array2) );
            $query1 = "INSERT INTO tbl_allcare_formflag ( " .
                    "encounter_id,form_id, form_name,pending,finalized, logdate" .
                    ") VALUES ( " .
                    "".$new['encounter'].",".$new['form_id'].", 'Allcare Encounter Forms', '$pending','$finalized', '".$logdata."' " .
                    ")";
    sqlInsert($query1); 
  }
  else {
   //echo $db_id = $new['id'];
     
    $sql = "UPDATE tbl_form_facetoface_transactions SET updated_date = NOW()";
    foreach ($new as $key => $value) {
      $sql .= ", `$key` = " . pdValueOrNull($key, $value);
    }
    $sql .= " WHERE id = '$id'";
    sqlStatement($sql);
    $array2[] = array( 'authuser' =>$_SESSION["authUser"],'pending' => $pending,'finalized' => $finalized, 'date' => date("Y/m/d"), 'action' => 'Updated', 'count'=> $count+1,'formName'=>'F2F_transaction');
                $logdata = array_merge_recursive($array, $array2);
                $logdata= ($logdata? serialize($logdata): serialize($array2) );
                $query2 = "INSERT INTO tbl_allcare_formflag ( " .
                "encounter_id,form_id, form_name,pending,finalized, logdate" .
                ") VALUES ( " .
                "".$new['encounter'].",".$new['form_id'].", 'Allcare Encounter Forms','$pending', '$finalized', '".$logdata."' " .
                ")";
    sqlInsert($query2);
                
  }
  return $db_id;
}
?>