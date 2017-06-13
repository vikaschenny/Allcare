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
 $form_id3=$_REQUEST['form_id'];
 $encounter=$_REQUEST['encounter'];
 $data1= unserialize($_REQUEST['data1']);
 $pid=$_REQUEST['pid'];
 if($form_id3==0 && $data1!=''){
     $sql_form=sqlStatement("select max(form_id)as new_form from forms where form_name='Allcare Encounter Forms' AND formdir='LBF2'");
    $row_form=sqlFetchArray($sql_form);
    $new_fid= $row_form['new_form'];
    $new_id1=++$new_fid;
    //echo "INSERT INTO  forms (date,encounter,form_name,form_id,pid,user,groupname,authorized,deleted,formdir) VALUES (NOW(),$ecounter1,'Allcare Encounter Forms',$new_id1,$pid1,'$_SESSION[authUser]','default',1,0,'LBF2')";
    $ins_form=sqlStatement("INSERT INTO  forms (date,encounter,form_name,form_id,pid,user,groupname,authorized,deleted,formdir) VALUES (NOW(),$encounter,'Allcare Encounter Forms',$new_id1,$pid,'$_SESSION[authUser]','default',1,0,'LBF2')");
    $row1_form=sqlFetchArray($ins_form);
    echo $form_id=$new_id1;
 }else if($form_id3!=0){
     $form_id=$form_id3;
 }

if($_REQUEST['mode']=='save'){
  
 function updateENC_forms($id, $new, $create,$ecounter1,$pid1,$field)
{
 
  $db_id = 0;
  //print_r($new);
  
  if ($create) {
    /*$sql = "INSERT INTO lbf_data SET  form_id = $id";
    foreach ($new as $key => $value) {
      if ($key == 'id') continue;
      $sql .= ", `$key` = " . pdValueOrNull($key, $value);
    }*/

    sqlInsert("INSERT INTO lbf_data (form_id,field_id,field_value) VALUES (?,?,?)", array($id,"$field","$new"));
    $db_id = 1;
  
    
  }
  else {

        if($new!=''){
             // echo "UPDATE lbf_data SET  `field_value` = '$new' WHERE  field_id= '$field' AND form_id = '$id'";
               //sqlStatement("UPDATE lbf_data SET  `field_value` = ? WHERE  field_id= ? AND form_id = ?",array('"'.$value.'"','"'.$key.'"',$id));
               sqlStatement("UPDATE lbf_data SET  `field_value` = '$new' WHERE  field_id= '$field' AND form_id = '$id'");
            }else {
               sqlStatement("delete  from lbf_data where form_id=$id AND field_id='$field'");
            }
        $db_id = 1;
        
    }
  return $db_id;
}
 
 
 $res = sqlStatement("select * from  layout_options where group_name LIKE '%$grp_stat%'  order by seq");
        while($frow2 = sqlFetchArray($res)){
            $field=$frow2['field_id'];
            //echo "select * from lbf_data lb INNER JOIN layout_options l ON l.field_id=lb.field_id where lb.form_id='$form_id' AND l.form_id='LBF2' AND l.group_name LIKE '%$grp_stat%'  AND lb.field_id='$field' order by seq";

             $res1=sqlstatement("select * from lbf_data lb INNER JOIN layout_options l ON l.field_id=lb.field_id where lb.form_id='$form_id' AND l.form_id='LBF2' AND l.group_name LIKE '%$grp_stat%' AND lb.field_id='$field' order by seq");
             $res_row1=sqlFetchArray($res1);
             if(!empty($res_row1)){
                 //echo 'update'; print_r($data1); echo $data1['lbf_data'][$field];
                 updateENC_forms($form_id, $data1['lbf_data'][$field] ,$create=false,$encounter,$pid,$field);
             }else{
                  //  echo 'create';  print_r($data1); echo $data1['lbf_data'][$field];
                  updateENC_forms($form_id, $data1['lbf_data'][$field] ,$create=true,$encounter,$pid,$field);
             }
        }

}

?>