<?php

//SANITIZE ALL ESCAPES
$sanitize_all_escapes=true;
//

//STOP FAKE REGISTER GLOBALS
$fake_register_globals=false;
//

 include_once("../../globals.php");
 include_once("$srcdir/patient.inc");
 include_once("history.inc.php");
 include_once("$srcdir/acl.inc");
 include_once("$srcdir/options.inc.php");

 $pid=$_REQUEST['pid'];
 $grname=$_REQUEST['grpname'];
$grp_stat=$_REQUEST['grp_stat'];
$form_id=$_REQUEST['form_id'];
$encounter=$_REQUEST['encounter'];


 // Check authorization.
 if (acl_check('patients','med')) {
  $tmp = getPatientData($pid, "squad");
  if ($tmp['squad'] && ! acl_check('squads', $tmp['squad']))
   die(htmlspecialchars(xl("Not authorized for this squad."),ENT_NOQUOTES));
 }
 if ( !acl_check('patients','med','',array('write','addonly') ))
  die(htmlspecialchars(xl("Not authorized"),ENT_NOQUOTES));

foreach ($_POST as $key => $val) {
  if ($val == "YYYY-MM-DD") {
    $_POST[$key] = "";
  }
}

// Update history_data:
//
$fres12 = sqlStatement("SELECT * FROM layout_options " .
  "WHERE form_id = 'LBF2' AND uor > 0 AND field_id != '' AND group_name LIKE '%$grp_stat%' " .
  "ORDER BY seq");
$field_id1=array();
while ($frow12 = sqlFetchArray($fres12)) {
 $data_type = $frow12['data_type'];
  $field_id  = $frow12['field_id'];
  $field_id1[]  = $frow12['field_id'];
  // $value  = '';
  $colname = $field_id;
  $table = 'lbf_data';
 
  $value = get_layout_form_value($frow12);

  $newdata_stat[$table][$colname] = $value;
}
//print_r($newdata_stat);
$data1=serialize($newdata_stat);
?>
<script type='text/javascript' src='../../main/js/jquery-1.11.1.min.js'></script>
 
<?php





$gethistorydatasql = sqlStatement("SELECT  * FROM history_data where pid = $pid  ORDER BY id DESC LIMIT 1 ");
$frow1 = sqlFetchArray($gethistorydatasql);
//echo "<pre>"; print_r($frow1);  echo "</pre>";
$newdata1 = array();
$fres = sqlStatement("SELECT * FROM layout_options " .
  "WHERE form_id = 'HIS' AND uor > 0 AND field_id != '' AND  group_name NOT LIKE '%$grname%' " .
  "ORDER BY group_name, seq");
while ($frow = sqlFetchArray($fres)) {
  $field_id  = $frow['field_id'];
  $newdata1[$field_id] = $frow1[$field_id];
}

//echo "<pre>"; print_r($newdata1);  echo "</pre>";
$newdata = array();
$fres = sqlStatement("SELECT * FROM layout_options " .
  "WHERE form_id = 'HIS' AND uor > 0 AND field_id != '' AND group_name LIKE '%$grname%' " .
  "ORDER BY seq");
while ($frow = sqlFetchArray($fres)) {
  $field_id  = $frow['field_id'];
  $newdata[$field_id] = get_layout_form_value($frow);
}
//echo "<pre>"; print_r($newdata);  echo "</pre>";

//echo "<pre>";  print_r(array_merge($newdata1,$newdata));  echo "</pre>";
$new_data3=array_merge($newdata1,$newdata);
updateHistoryData($pid, $new_data3);






// POS start
//saved POS data into database tables, after patient gets saved.


if($_POST['txtPOSid']!=0)
{
    
 $Fields1to1Sql ="SELECT fg.id, fg.POS_id, fg.Grouping_ID, fg.Grouping_Name, fg.Table_ID, fg.Field_ID, pt.title, tb.Field_Name, tb.Table_Name
FROM tbl_allcare_tablemeta tb
INNER JOIN tbl_allcare_patients1to1_fieldmapping fg ON tb.Field_ID = fg.Field_ID
INNER JOIN tbl_pos_types pt ON pt.id = fg.POS_id and fg.POS_id=".$_POST['txtPOSid']."
group by fg.Grouping_ID,fg.Field_ID";

 $Fields1to1Qry =  sqlStatement($Fields1to1Sql); 
   if(sqlNumRows($Fields1to1Qry)>0)
   {
       while($Fields1to1Res = sqlFetchArray($Fields1to1Qry)) 
            {
                $field = $Fields1to1Res['Field_Name'];
                $fieldvalue=$_POST[$field];

            $totalColumn .= $Fields1to1Res['Field_Name'].",";     
            $fieldValues .= "'".$fieldvalue."',";
            }

            $totalColumn = substr($totalColumn,0,strlen($totalColumn)-1) ;
            $fieldValues = substr($fieldValues,0,strlen($fieldValues)-1) ;


              $fieldInsertSql1to1 ="insert into tbl_allcare_patients1to1(pid,pos_id,$totalColumn) values($pid,".$_POST['txtPOSid'].",$fieldValues)";   
            $result = sqlStatement($fieldInsertSql1to1);

   }


$totalColumn='';
$fieldValues='';
$Fields1tonSql ="SELECT fg.id, fg.POS_id, fg.Recordset_ID, fg.Recordset_Name, fg.Table_ID, fg.Field_ID, pt.title, tb.Field_Name, tb.Table_Name
FROM tbl_allcare_tablemeta tb
INNER JOIN tbl_allcare_patients1ton_fieldmapping fg ON tb.Field_ID = fg.Field_ID
INNER JOIN tbl_pos_types pt ON pt.id = fg.POS_id and fg.POS_id=".$_POST['txtPOSid']."
group by fg.Recordset_ID,fg.Field_ID";

 $Fields1tonQry =  sqlStatement($Fields1tonSql); 
    if(sqlNumRows($Fields1tonQry)>0)
   {
        while($Fields1tonRes = sqlFetchArray($Fields1tonQry)) 
        {
            $field = $Fields1tonRes['Field_Name'];
            $fieldvalue=$_POST[$field];

        $totalColumn .= $Fields1tonRes['Field_Name'].",";     
        $fieldValues .= "'".$fieldvalue."',";
        }

        $totalColumn = substr($totalColumn,0,strlen($totalColumn)-1) ;
        $fieldValues = substr($fieldValues,0,strlen($fieldValues)-1) ;


        $fieldInsertSql1ton ="insert into tbl_allcare_patients1ton(pid,pos_id,$totalColumn) values($pid,".$_POST['txtPOSid'].",$fieldValues)";   
        $result = sqlStatement($fieldInsertSql1ton);

    }

 
 
}
        

//POS ends


//if ($GLOBALS['concurrent_layout']) {
// echo "<script>window.close();
//
//    window.opener.location.href = '../../reports/incomplete_charts.php';</script>";
//} else {
// include_once("patient_history.php");
//}
?>
<script>
    
     $.ajax({
		type: 'POST',
		url: "history_status_save.php",	
                data:{encounter:'<?php echo $encounter; ?>',
                       form_id:'<?php echo $form_id;?>',
                       grpstat:'<?php echo $grp_stat; ?>',
                       mode:'save',
                       pid:'<?php echo $pid; ?>',
                       data1:'<?php echo $data1 ?>'
                   },
		success: function(response)
		{
                    //alert(response);
                   window.close();

                     window.opener.location.href = '../../reports/incomplete_charts.php';

		},
		failure: function(response)
		{
                    alert("error");
		}		
	});	 
</script>