<?php
require_once("{$GLOBALS['srcdir']}/sql.inc");

function getChartOutputById ($id, $group_name,$type, $cols = "*")
{
    if($type=='2'){
        $labels = sqlStatement("select field_id from layout_options where group_name  LIKE '%$group_name%' and form_id = 'CHARTOUTPUT' AND field_id LIKE '%f2f%' " );
    }else {
        $labels = sqlStatement("select field_id from layout_options where group_name  LIKE '%$group_name%' and form_id = 'CHARTOUTPUT'" );
    }
    
    
    while ($labels2 = sqlFetchArray($labels)) {
        $titles .= $labels2['field_id'].',' ; 
        $columncheck .= $labels2['field_id']." <> '' OR ";
    }
  $titles2 = rtrim($titles ,',');
  $columncheck2 = rtrim($columncheck, ' OR ');
  
  return sqlQuery("select id,$titles2 from tbl_form_chartoutput_transactions where id=$id AND ($columncheck2 )" .
    "order by id DESC  limit 0,1");
}
//for who & notes
function getF2FById ($id,$cols = "*") 
{
    $labels = sqlStatement("select field_id from layout_options where form_id = 'F2F' AND data_type!=36" );
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
    
  $labels = sqlStatement("select field_id from layout_options where group_name  LIKE '%$group_name%' and form_id = 'CHARTOUTPUT'" );
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
     
  $res = sqlStatement("select id,refer_to,provider,facility,pharmacy,payer,notes,dos,form_template,trans_type,created_date,updated_date,transaction,date_of_service,$titles2 from tbl_form_chartoutput_transactions where pid=$pid AND ($columncheck2)
    order by id DESC ");
   
  for ($iter = 0; $row = sqlFetchArray($res); $iter++) 
    $all[$iter] = $row;
  return $all;
  }
  
}
//for nonencounter
function getNonencChartById ($id, $group_name, $cols = "*")
{
   
        $labels = sqlStatement("select field_id from layout_options where group_name  LIKE '%$group_name%' and form_id = 'NONENC'" );
    
    
    
    while ($labels2 = sqlFetchArray($labels)) {
        $titles .= $labels2['field_id'].',' ; 
        $columncheck .= $labels2['field_id']." <> '' OR ";
    }
  $titles2 = rtrim($titles ,',');
  $columncheck2 = rtrim($columncheck, ' OR ');
  
  return sqlQuery("select id,$titles2 from tbl_nonencounter_data where id=$id AND ($columncheck2 )" .
    "order by id DESC  limit 0,1");
}

function getNonEncChartByPid ($pid, $group_name, $cols = "*")
{
  
  $labels = sqlStatement("select field_id from layout_options where group_name  LIKE '%$group_name%' and form_id = 'NONENC'" );
  while ($labels2 = sqlFetchArray($labels)) {
        $titles .= $labels2['field_id'].',' ; 
         $columncheck .= $labels2['field_id']." <> '' OR ";
  }
  $titles2 = rtrim($titles ,',');
  $columncheck2 = rtrim($columncheck, ' OR ');
  
  if(!empty($group_name)){
  
  $res = sqlStatement("select id,refer_to,provider,facility,pharmacy,payer,notes,form_template,trans_type,date , $titles2 from tbl_nonencounter_data where pid=$pid AND ($columncheck2)
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