<?php
require_once("{$GLOBALS['srcdir']}/sql.inc");

function getChartOutputById ($id, $group_name, $cols = "*")
{
    $labels = sqlStatement("select field_id from layout_options where group_name  = '$group_name' and form_id = 'CHARTOUTPUT'" );
    while ($labels2 = sqlFetchArray($labels)) {
        $titles .= $labels2['field_id'].',' ; 
        $columncheck .= $labels2['field_id']." <> '' OR ";
  }
  $titles2 = rtrim($titles ,',');
  $columncheck2 = rtrim($columncheck, ' OR ');
  return sqlQuery("select id,$titles2 from tbl_form_chartoutput_transactions where id=$id AND ($columncheck2 )" .
    "order by id DESC  limit 0,1");
}

function getChartOutputByPid ($pid, $group_name, $cols = "*")
{
  $labels = sqlStatement("select field_id from layout_options where group_name  = '$group_name' and form_id = 'CHARTOUTPUT'" );
  while ($labels2 = sqlFetchArray($labels)) {
        $titles .= $labels2['field_id'].',' ; 
         $columncheck .= $labels2['field_id']." <> '' OR ";
  }
  $titles2 = rtrim($titles ,',');
  $columncheck2 = rtrim($columncheck, ' OR ');
  //return $allstring;  
  
  //$all = '';
  if(!empty($group_name)){
//      if($pid == 0):
//          $pid = $_SESSION['pid'];
//      endif;
     
  $res = sqlStatement("select id,$titles2 from tbl_form_chartoutput_transactions where pid=$pid AND ($columncheck2)
    order by id DESC ");
   
  for ($iter = 0; $row = sqlFetchArray($res); $iter++)
    $all[$iter] = $row;
  return $all;
  }
  
}

function newchartOutput($pid, $from_dos, $to_dos, $patientinfo )
{
  $body = mysql_escape_string($body);
  return sqlInsert("insert into tbl_form_chartoutput_transactions ( " .
    " pid, from_dos, to_dos,  patientinfo " .
    ") values ( " .
    "'$pid', '$from_dos', '$to_dos', '$patientinfo' " .
    ")");
}
?>