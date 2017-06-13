<?php
require_once("../../verify_session.php");
$mainPlans = [];
$mainPlans['insplans'] = [];
$insPlans = [];
$insid = $_POST['insid'];
$sql2   = sqlStatement("SELECT * from `tbl_patientinsurancecompany` WHERE `insuranceid` = '".$insid."'");
while($row2  = sqlFetchArray($sql2)){
    
    $insType = $row2['insurance_type'];
    
    $instype = sqlStatement("SELECT `title` from `list_options` WHERE `list_id` = 'Payer_Types' AND `option_id` = '".$insType."'");
    $instypeRes = sqlFetchArray($instype);
    
    $row2['ins_type'] = $insType;
    
    $row2['insurance_type'] = $instypeRes['title'];
    
    // Get benefit fields related to this insurance type
    $bfields = array();
    if($insType != ""):
        $query = sqlStatement("SELECT fields FROM tbl_benefit_fields_map WHERE ins_type=".$insType);
        $bfieldArr = sqlFetchArray($query);
        $bfieldsStr = str_replace('"','',$bfieldArr['fields']);
        $bfieldsStr = str_replace('[','',$bfieldsStr);
        $bfieldsStr = str_replace(']','',$bfieldsStr);
        $bfields = explode(",",$bfieldsStr);
    endif;
    
    $resultantArr = array();
    $sql3 = sqlStatement("SELECT * FROM tbl_inscomp_benefits WHERE planid=".$row2['id']." and deleted=0");
    $i = 0;
    while($row3 = sqlFetchArray($sql3)):
        foreach($row3 as $key => $value):
            $groupName = "";
            if(in_array($key,$bfields)):
                $groupQuery = sqlStatement("SELECT group_name,title FROM `layout_options` WHERE form_id='BENEFITS' AND field_id='".$key."'");
                $groupRow = sqlFetchArray($groupQuery);
                $groupName = substr($groupRow['group_name'], 1);
                 if($key=='plan_type') {
                    $list=sqlStatement("select * from list_options where list_id='Allcare_Plan_Type' and option_id='$value'");
                    $ldata=sqlFetchArray($list);
                    $resultantArr[$i][$groupName][$groupRow['title']] = $ldata['title'];
                }else
                    $resultantArr[$i][$groupName][$groupRow['title']] = $value;
            endif;
        endforeach;
        $i++;
    endwhile;
    
    $row2['benefits'][$row2['id']] = $resultantArr;

    array_push($insPlans,$row2);
}



$mainPlans['insplans'] = $insPlans;

echo json_encode($mainPlans);

?>