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
 
 $grp_stat=$_REQUEST['grpstat'];
 $form_id=$_REQUEST['form_id'];
 $encounter=$_REQUEST['encounter'];
 $data1=$_REQUEST['data1'];

if($_REQUEST['mode']=='save'){
  
 function updateENC_forms($id, $new, $create,$ecounter1,$pid1)
{
 
  $db_id = 0;
  print_r($new);
  
  if ($create) {
    /*$sql = "INSERT INTO lbf_data SET  form_id = $id";
    foreach ($new as $key => $value) {
      if ($key == 'id') continue;
      $sql .= ", `$key` = " . pdValueOrNull($key, $value);
    }*/

        
     foreach ($new as $key1 => $value1) {
         foreach($value1 as $key => $value) {
      
        if($value!=''){ 
         
          
         sqlInsert("INSERT INTO lbf_data (form_id,field_id,field_value) VALUES (?,?,?)", array($id,$key,$value));
        }
     }
      } 
    
    $db_id = 1;
  
    
  }
  else {
       //echo $db_id = $new['id'];
        foreach ($new as $key1 => $value1) {
            foreach($value1 as $key => $value) {
            if($value!=''){
              //echo "UPDATE lbf_data SET  `field_value` = ? WHERE  field_id= ? AND form_id = ?",array($value,$key,$id);
               sqlStatement("UPDATE lbf_data SET  `field_value` = ? WHERE  field_id= ? AND form_id = ?",array($value,$key,$id));
            }else {
               sqlStatement("delete  from lbf_data where form_id=$id AND field_id='$key'");
            }
            }
        }
        $db_id = 1;
        
    }
  return $db_id;
}
 
 



echo "select * from lbf_data lb INNER JOIN layout_options l ON l.field_id=lb.field_id where lb.form_id='$form_id' AND l.form_id='LBF2' AND l.group_name LIKE '%$grp_stat%'  order by seq";

 $res1=sqlstatement("select * from lbf_data lb INNER JOIN layout_options l ON l.field_id=lb.field_id where lb.form_id='$form_id' AND l.form_id='LBF2' AND l.group_name LIKE '%$grp_stat%'  order by seq");
 $res_row1=sqlFetchArray($res1);
 if(!empty($res_row1)){
    
     updateENC_forms($form_id, unserialize($data1) ,$create=false,$encounter,$pid);
 }else{
     
      updateENC_forms($form_id, unserialize($data1) ,$create=true,$encounter,$pid);
 }
}

?>