<?php

 //SANITIZE ALL ESCAPES
 $sanitize_all_escapes=$_POST['true'];

 //STOP FAKE REGISTER GLOBALS
 $fake_register_globals=$_POST['false'];

include_once("../../globals.php");
include_once("$srcdir/api.inc");
include_once("$srcdir/forms.inc");
require_once("$srcdir/formdata.inc.php");
require_once($GLOBALS['srcdir'].'/options.inc.php');

$encounter=$_REQUEST['encounter'];
//if (! $encounter) { // comes from globals.php
// die(xl("Internal error: we do not seem to be in an encounter!"));
//}
$id = 0 + (isset($_REQUEST['id']) ? $_REQUEST['id'] : '');
$formid=$_REQUEST['formid'];
$pid=$_REQUEST['pid'];

 for($i=1; $i<=$_REQUEST['noofrows']; $i++){
    if($i == 1):
        $cpotypeval  = "cpotype";
        $reference    = "reference";
        $description = "description";
        $start_date  = "start_date";
        $timeinterval    = "timeinterval";
        $location    = "location";
        $users    = "users";
    else:
        $cpotypeval  = "cpotype".$i;
        $reference    = "reference".$i;
        $description = "description".$i;
        $start_date  = "start_date".$i;
        $timeinterval    = "timeinterval".$i;
        $location    = "location".$i;
        $users    = "users".$i;
    endif;
    $array2[] = array(  'cpotype' => $_REQUEST[$cpotypeval],
                        'start_date' =>  $_REQUEST[$start_date], 
                        'timeinterval' => $_REQUEST[$timeinterval],
                        'description' => addslashes(htmlspecialchars($_REQUEST[$description])),
                        'reference' => addslashes(htmlspecialchars($_REQUEST[$reference])),
                        'users' => $_REQUEST[$users],
                        'location' => addslashes(htmlspecialchars($_REQUEST[$location])));
}
$cpo_data= ( serialize($array2) );
$sets = "pid = {$_REQUEST["pid"]},
    authProvider = '" . $_SESSION["authProvider"] . "',
    user = '" . $_SESSION["authUser"] . "',
    authorized = $userauthorized, 
    activity=1, 
    date = NOW(),
    provider_id  =  '" .add_escape_custom($_POST["provider_id"]) . "',
    cpo_data = '" .$cpo_data. "', 
    signed_date =    '" .$_POST["signed_date"] . "',
    count =    '" .add_escape_custom($_POST["noofrows"]) . "'";

if (empty($id)) {
    $newid = sqlInsert("INSERT INTO tbl_form_cpo SET $sets");
    addForm($encounter, "CPO", $newid, "cpo", $_REQUEST["pid"], $userauthorized);
   
}
else {
    //echo "UPDATE tbl_form_cpo SET $sets WHERE id = '". add_escape_custom("$id"). "'";
    sqlStatement("UPDATE tbl_form_cpo SET $sets WHERE id = '". add_escape_custom("$id"). "'");
   
}
if($_REQUEST['mode']=='add'){

 
 function updateENC_forms($id, $new, $create,$ecounter1,$pid1,$field_id)
{
 
  $db_id = 0;
 // print_r($new);
   
  if ($create) {
    /*$sql = "INSERT INTO lbf_data SET  form_id = $id";
    foreach ($new as $key => $value) {
      if ($key == 'id') continue;
      $sql .= ", `$key` = " . pdValueOrNull($key, $value);
    }*/

        
     //foreach ($new as $key => $value) {
      
        if($new!=''){ 
         
          
         sqlInsert("INSERT INTO lbf_data (form_id,field_id,field_value) VALUES (?,?,?)", array($id,$field_id,$new));
        }
     // } 
    
    $db_id = 1;
  
    
  }
  else {
       //echo $db_id = $new['id'];
       // foreach ($new as $key => $value) {
            if($new!=''){
              //echo "UPDATE lbf_data SET  `field_value` = ? WHERE  field_id= ? AND form_id = ?",array($value,$key,$id);
               sqlStatement("UPDATE lbf_data SET  `field_value` = ? WHERE  field_id= ? AND form_id = ?",array($new,$field_id,$id));
            }else {
               sqlStatement("delete  from lbf_data where form_id=$id AND field_id='$field_id'");
            }
        //}
        $db_id = 1;
        
    }
  return $db_id;
}
 
 $newdata = array();

$fres = sqlStatement("SELECT * FROM layout_options " .
  "WHERE form_id = 'LBF2' AND uor > 0  AND group_name LIKE '%CPO%' AND field_id IN ('cpo_stat','cpo_review')" .
  "ORDER BY seq");
$field_id1=array();
while ($frow = sqlFetchArray($fres)) {
  $data_type = $frow['data_type'];
  $field_id  = $frow['field_id'];
  $field_id1[]  = $frow['field_id'];
  // $value  = '';
  $colname = $field_id;
  $table = 'lbf_data';
 
  $value = get_layout_form_value($frow);

  $newdata[$table][$colname] = $value;
}
if(!empty($newdata['lbf_data']) && $formid==0) {
      $sql_form=sqlStatement("select max(form_id)as new_form from forms where form_name='Allcare Encounter Forms' AND formdir='LBF2'");
    $row_form=sqlFetchArray($sql_form);
    $new_fid= $row_form['new_form'];
    $new_id1=++$new_fid;
    //echo "INSERT INTO  forms (date,encounter,form_name,form_id,pid,user,groupname,authorized,deleted,formdir) VALUES (NOW(),$ecounter1,'Allcare Encounter Forms',$new_id1,$pid1,'$_SESSION[authUser]','default',1,0,'LBF2')";
    $ins_form=sqlStatement("INSERT INTO  forms (date,encounter,form_name,form_id,pid,user,groupname,authorized,deleted,formdir) VALUES (NOW(),$encounter,'Allcare Encounter Forms',$new_id1,$pid,'$_SESSION[authUser]','default',1,0,'LBF2')");
    $row1_form=sqlFetchArray($ins_form); 
    $formid=$new_id1;
  }
//echo "<pre>"; print_r($field_id1); echo "</pre>";
foreach($field_id1 as $val){
   // echo "select * from lbf_data lb INNER JOIN layout_options l ON l.field_id=lb.field_id where lb.form_id='$formid' AND l.form_id='LBF2' AND l.group_name LIKE '%CPO%' AND lb.field_id LIKE '%$val%' order by seq"; 
    $res1=sqlstatement("select * from lbf_data lb INNER JOIN layout_options l ON l.field_id=lb.field_id where lb.form_id='$formid' AND l.form_id='LBF2' AND l.group_name LIKE '%CPO%' AND lb.field_id LIKE '%$val%' order by seq");
 $res_row1=sqlFetchArray($res1);
 if(!empty($res_row1)){
     
     updateENC_forms($formid, $newdata['lbf_data'][$val] ,$create=false,$encounter,$pid,$val);
     
       echo "<script>  window.close();
   window.opener.location.href='../../reports/incomplete_charts.php';</script>";
 }else{
      updateENC_forms($formid, $newdata['lbf_data'][$val] ,$create=true,$encounter,$pid,$val);
        echo "<script>  window.close();
   window.opener.location.href='../../reports/incomplete_charts.php';</script>";
 }
}
 
 }
// function formJump_custom()
//{
//	echo "\n<script language='Javascript'> window.close(); window.opener.location.href = '../../reports/incomplete_charts.php';</script>\n";
//	
//}
//$_SESSION["encounter"] = $encounter;
//formHeader("Redirecting....");
//formJump_custom();
//formFooter();
?>
