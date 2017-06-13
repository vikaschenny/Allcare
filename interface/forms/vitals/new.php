<?php
include_once("../../globals.php");
include_once("$srcdir/api.inc");

require ("C_FormVitals.class.php");

$c = new C_FormVitals();
#echo $c->view_action(0);
// $res12=sqlstatement("select form_id  from forms where form_name ='Allcare Encounter Forms' AND encounter='".$GLOBALS['encounter']."' AND pid='".$GLOBALS['pid']."' AND deleted=0 order by id desc");
// $frow_res = sqlFetchArray($res12);
// if(!empty($frow_res)){
//     $formid=$frow_res['form_id'];
//     $res1=sqlstatement("select * from lbf_data lb INNER JOIN layout_options l ON l.field_id=lb.field_id where lb.form_id='$formid' AND l.form_id='LBF2' AND l.group_name LIKE '%Vitals%' AND lb.field_id LIKE '%_stat%' order by seq");
//     $res_row1=sqlFetchArray($res1);
// }
// $stat=explode("|",$res_row1['field_value']);
//if(in_array('pending',$stat)){
//$pending='pending';
//}
//if(in_array('finalized',$stat)){
// $finalized='finalized';    
//}
echo $c->default_action(0);
?>
