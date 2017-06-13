<?php
require_once("../verify_session.php");
$benefitid = $_POST['benefitid'];
$finalAry = [];
$groupResAry = [];
$groupAry = [];
$count = 0;

$benefitsQry = sqlStatement("SELECT * FROM `tbl_inscomp_benefits` WHERE id = '".$benefitid."' and deleted=0");
$benefitsRes = sqlFetchArray($benefitsQry);


$sqlFields = sqlStatement("SELECT * FROM `layout_options` WHERE form_id = 'BENEFITS' AND uor > 0 AND field_id != '' ORDER BY group_name, seq");
while($resFields = sqlFetchArray($sqlFields)){
//                                echo "<pre>"; print_r($resFields);
    if(!in_array($resFields['group_name'], $groupAry)){
        array_push($groupAry, $resFields['group_name']);
    }
}
//echo "<pre>"; print_r($groupAry);

foreach($groupAry as $each){
    $groupResAry[substr($each, 1)] = [];
    $count++;
    $sqlgroupFields = sqlStatement("SELECT * FROM `layout_options` WHERE form_id = 'BENEFITS' AND `group_name` = '".$each."' AND uor > 0 AND field_id != '' ORDER BY  seq");
    while($resgroupFields = sqlFetchArray($sqlgroupFields)){
        if($resgroupFields['uor'] != 0){
            if($resgroupFields['field_id']=='plan_type') {
                $list=sqlStatement("select * from list_options where list_id='Allcare_Plan_Type' and option_id='".$benefitsRes[$resgroupFields['field_id']]."'");
                $ldata=sqlFetchArray($list);
                $groupResAry[substr($each, 1)][$resgroupFields['title']] = $ldata['title']; 
            }else
                $groupResAry[substr($each, 1)][$resgroupFields['title']] = $benefitsRes[$resgroupFields['field_id']];
            
            
            //echo $each."-- Field ID: ".$resgroupFields['field_id']; echo "<br>";
        }
    }
}
echo json_encode($groupResAry);
?> 