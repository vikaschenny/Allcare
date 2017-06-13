<?php
include_once("../../globals.php");
include_once("$srcdir/api.inc");

require ("C_FormVitals_custom.class.php");
$c = new C_FormVitals_custom();
echo $c->default_action_process_custom($_POST);
print_r($_POST);

if(!empty($_POST['finalized']) && !empty($_POST['pending'])){
    $finalized=$_POST['finalized'];
    $pending=$_POST['pending'];
    $value=$finalized[0].'|'. $pending[0];
   
}else if(!empty($_POST['finalized'])){
    $finalized=$_POST['finalized'];
    $value=$finalized[0];
   
}else if(!empty($_POST['pending'])){
    $pending=$_POST['pending'];
    $value=$pending[0];
   
   
}
$resenc1=$_POST['encounter1'];
$respid1=$_POST['pid2'];
 $res12=sqlstatement("select form_id  from forms where form_name ='Allcare Encounter Forms' AND encounter='$resenc1' AND pid='$respid1' AND deleted=0 order by id desc");
 $frow_res = sqlFetchArray($res12);
 if(!empty($frow_res)){
     $formid=$frow_res['form_id'];
     $res1=sqlstatement("select * from lbf_data lb INNER JOIN layout_options l ON l.field_id=lb.field_id where lb.form_id='$formid' AND l.form_id='LBF2' AND l.group_name LIKE '%Vitals%' AND lb.field_id LIKE '%_stat%' order by seq");
     $res_row1=sqlFetchArray($res1);
     if(!empty($res_row1)){
         $update=sqlStatement("UPDATE lbf_data SET  `field_value` = ? WHERE  field_id= ? AND form_id = ?",array($value,'vitals_stat',$formid));
     }else{
         sqlInsert("INSERT INTO lbf_data (form_id,field_id,field_value) VALUES (?,?,?)", array($formid,'vitals_stat',$value));
     }
 }else{
        $sql_form=sqlStatement("select max(form_id)as new_form from forms where form_name='Allcare Encounter Forms' AND formdir='LBF2'");
        $row_form=sqlFetchArray($sql_form);
        $new_fid= $row_form['new_form'];
        $new_id1=++$new_fid;
        //echo "INSERT INTO  forms (date,encounter,form_name,form_id,pid,user,groupname,authorized,deleted,formdir) VALUES (NOW(),$ecounter1,'Allcare Encounter Forms',$new_id1,$pid1,'$_SESSION[authUser]','default',1,0,'LBF2')";
        $ins_form=sqlStatement("INSERT INTO  forms (date,encounter,form_name,form_id,pid,user,groupname,authorized,deleted,formdir) VALUES (NOW(),$resenc1,'Allcare Encounter Forms',$new_id1,$respid1,'$_SESSION[authUser]','default',1,0,'LBF2')");
        $row1_form=sqlFetchArray($ins_form);
        $res1=sqlstatement("select * from lbf_data lb INNER JOIN layout_options l ON l.field_id=lb.field_id where lb.form_id='$new_id1' AND l.form_id='LBF2' AND l.group_name LIKE '%Vitals%' AND lb.field_id LIKE '%_stat%' order by seq");
        $res_row1=sqlFetchArray($res1);
        if(!empty($res_row1)){
         $update=sqlStatement("UPDATE lbf_data SET  `field_value` = ? WHERE  field_id= ? AND form_id = ?",array($value,'vitals_stat',$new_id1));
        }else{
         sqlInsert("INSERT INTO lbf_data (form_id,field_id,field_value) VALUES (?,?,?)", array($new_id1,'vitals_stat',$value));
       }
 }
 
//@formJump();
$provider=$_POST['provider'];

    echo "<script>window.close();

    window.opener.location.href = '../../reports/incomplete_charts.php';</script>";
    


?>
