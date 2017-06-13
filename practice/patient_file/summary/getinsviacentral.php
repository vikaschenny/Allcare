<?php
require_once("../../verify_session.php");
require_once("../../../library/sqlCentralDB.inc");
global $sqlconfCentralDB;
$mainPlans = [];
$mainPlans['insplans'] = [];
$insPlans = [];
$insid = $_POST['insid'];
$sql2   = $sqlconfCentralDB->prepare("SELECT * from `tbl_patientinsurancecompany` WHERE `insuranceid` = '".$insid."'");
$sql2->execute();
while($row2  = $sql2->fetch()){
    
    $insType = $row2['insurance_type'];
    
    $instype = $sqlconfCentralDB->prepare("SELECT `title` from `list_options` WHERE `list_id` = 'Payer_Types' AND `option_id` = '".$insType."'");
    $instype->execute();
    $instypeRes = $instype->fetch();
    
    $row2['insurance_type'] = $instypeRes['title'];
    
    // Get benefit fields related to this insurance type
    $bfields = array();
    if($insType != ""):
        $query = $sqlconfCentralDB->prepare("SELECT fields FROM tbl_benefit_fields_map WHERE ins_type=".$insType);
        $query->execute();
        $bfieldArr = $query->fetch();
        $bfieldsStr = str_replace('"','',$bfieldArr['fields']);
        $bfieldsStr = str_replace('[','',$bfieldsStr);
        $bfieldsStr = str_replace(']','',$bfieldsStr);
        $bfields = explode(",",$bfieldsStr);
    endif;
    
    $resultantArr = array();
    $sql3 = $sqlconfCentralDB->prepare("SELECT * FROM tbl_inscomp_benefits WHERE planid=".$row2['id']);
    $sql3->execute();
    $i = 0;
    while($row3 = $sql3->fetch()):
        foreach($row3 as $key => $value):
            $groupName = "";
            if(in_array($key,$bfields)):
                if($key !== 0):
                    $groupQuery = $sqlconfCentralDB->prepare("SELECT group_name,title FROM `layout_options` WHERE form_id='BENEFITS' AND field_id='".$key."'");
                    $groupQuery->execute();
                    $groupRow = $groupQuery->fetch();
                    $groupName = substr($groupRow['group_name'], 1);
                    if($key=='plan_type'){
                        $list=sqlStatement("select * from list_options where list_id='Allcare_Plan_Type' and option_id='".$value."'");
                        $ldata=sqlFetchArray($list);
                        $resultantArr[$i][$groupName][$groupRow['title']] = nl2br($ldata['title'],false);
                    }else {
                        $resultantArr[$i][$groupName][$groupRow['title']] = nl2br($value,false);
                    }
                endif;    
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