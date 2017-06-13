<?php
require_once('../interface/globals.php');
require_once("$srcdir/formdata.inc.php");

$provider=$_REQUEST['provider_id'];
$pid=$_REQUEST['pid'];
function getDosFormData($eid){
  try 
    {
        $db = getConnection();
        
        $apikey = 'rotcoderaclla';
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        
        $sql = "SELECT f.pc_catid, o.pc_aid as provider_id, f.facility_id, p.pid
                FROM  `form_encounter` f 
                INNER JOIN patient_data p ON p.pid = f.pid
                inner join openemr_postcalendar_events o on f.pid = o.pc_pid and f.pc_catid = o.pc_catid and o.pc_eventDate = DATE_FORMAT(f.date,'%Y-%m-%d')
                WHERE encounter =$eid ";
        
        $stmt = $db->prepare($sql) ; 
        $stmt->execute();
        $namesresult = $stmt->fetchAll(PDO::FETCH_OBJ); 
        foreach ($namesresult as $pidval) {
            $pid = $pidval->pid;
        }
        $fieldnamesresult = array();
        $formnames = array();
        $formfields = array();
        $newdemo = array();
        $formvalues = array();
        foreach ($namesresult as $value) {
            
            $sql2 ="SELECT DISTINCT  l.title AS screen_name,f.screen_group, l.description AS screen_link
                FROM  `tbl_allcare_facuservisit` f
                INNER JOIN layout_options l ON l.group_name = f.screen_group
                AND l.form_id = f.form_id
                WHERE  `facilities` REGEXP ('".":\"".$value->facility_id."\"') AND  `users` REGEXP ('".":\"".$value->provider_id."\"') AND  `visit_categories` REGEXP ('".":\"".$value->pc_catid."\"') order by f.id ";      
            $stmt2 = $db->prepare($sql2); 
            $stmt2->execute();
            $fieldnamesresult[] = $stmt2->fetchAll(PDO::FETCH_OBJ);
            
        } 
//        echo "<pre>"; print_r($fieldnamesresult); echo "</pre>";
        foreach ($fieldnamesresult as $value2) {
            
            foreach ($value2 as $value3) {
                $sql3 = "SELECT DISTINCT(SUBSTRING(lo.group_name FROM 2)) as group_name , lo.field_id 
                                FROM layout_options lo
                                LEFT JOIN forms f ON lo.form_id = 'LBF1'
                                WHERE lo.title = '$value3->screen_name'
                                AND f.encounter = $eid";
                $stmt3 = $db->prepare($sql3) ;
                $stmt3->execute();         
              
                
                $formnames = $stmt3->fetchAll(PDO::FETCH_OBJ);
                foreach ($formnames as $value4) {
                    $datacheck = array();
//                    echo $sql4 = "SELECT screen_names FROM tbl_allcare_facuservisit WHERE  ";
                    $sql4 = "SELECT (SELECT DISTINCT(SUBSTRING(lo.group_name FROM 2)) as group_name  
                                FROM layout_options lo
                                LEFT JOIN forms f ON lo.form_id = 'LBF1'
                                WHERE lo.title = '$value3->screen_name'
                                AND f.encounter = $eid) as form_type, f.form_id, (SELECT lo.field_id
                                                        FROM layout_options lo 
                                                        WHERE lo.title =  '$value3->screen_name' AND lo.form_id = 'LBF1') as form_name ,SUBSTRING(lo.group_name ,-length(lo.group_name),1) as grouporder,SUBSTRING(lo.group_name FROM 2) as GroupName,
                                (SELECT lo.description
                                                        FROM layout_options lo 
                                                        WHERE lo.title =  '$value3->screen_name' AND lo.form_id = 'LBF1') as  description, 
                                    CASE lf.field_value
                                        WHEN 'finalized|pending' THEN 'finalized'
                                        WHEN 'pending|finalized' THEN 'finalized'
                                        WHEN 'pending' THEN 'pending'
                                        WHEN 'finalized' THEN 'finalized'
                                    END as   field_value  
                                FROM layout_options lo
                                INNER JOIN forms f ON lo.form_id = f.formdir
                                INNER JOIN lbf_data lf ON lf.form_id = f.form_id
                                AND lo.field_id = lf.field_id
                                WHERE lo.form_id = 'LBF2' 
                                AND lo.field_id  LIKE '".$value4->field_id."_stat'
                                AND f.encounter = $eid
                                AND f.deleted=0";
                    $stmt4 = $db->prepare($sql4) ;
                    $stmt4->execute();    
                    $datacheck = $stmt4->fetchAll(PDO::FETCH_OBJ);  
                    
                    if(!empty($datacheck)){
                        $get_isRequired = "SELECT id,screen_names FROM tbl_allcare_facuservisit WHERE screen_group LIKE '%".$datacheck[0]->form_type."' AND `users`  REGEXP ('".":\"".$value->provider_id."\"') ";
                        $db->query( "SET NAMES utf8");
                        $isReq_stmt = $db->prepare($get_isRequired) ;
                        $isReq_stmt->execute();    
                        $req_datacheck = $isReq_stmt->fetchAll(PDO::FETCH_OBJ); 
                        $s_array = array();
                        $dataArray = '';
                        if(!empty($req_datacheck)){
                            $s_array =  unserialize( $req_datacheck[0]->screen_names);
                            for($j = 0; $j<count($s_array); $j++){
//                                foreach($s_array[$j] as $arraykey){
                                    if(strpos($s_array[$j],$datacheck[0]->form_name) !== false){
                                       $dataArray = $s_array[$j];
                                    }
//                                }
                            }
                            $fields = explode('$$', $dataArray);
                            if(!empty($fields)){
                                $datacheck[0]->FormOrder = (isset($fields[0]) ? $fields[0] : '');
                                $datacheck[0]->isRequired = (isset($fields[1]) ? $fields[1] : '');
                            }else{
                                $datacheck[0]->FormOrder = '';
                                $datacheck[0]->isRequired = '';
                            }
                            $datacheck[0]->id = $req_datacheck[0]->id;
                        }else{
                            $datacheck[0]->FormOrder = '';
                            $datacheck[0]->isRequired = '';
                            $datacheck[0]->id = '';
                        }
                    }
//                    echo "<pre>"; print_r($datacheck); echo "</pre>";
                        if(empty($datacheck) || ($value4->field_id == 'ros') || ($value4->field_id == 'physical_exam')){ 
                            $field_value = '';
//                            $sql5 = "SELECT l.field_id, l.seq as FormOrder,SUBSTRING(l.group_name ,-length(l.group_name),1) as grouporder,SUBSTRING(l.group_name FROM 2) as GroupName,
//                                     CASE l.uor
//                                    WHEN 0 THEN 'UnUsed' 
//                                    WHEN 1 THEN 'Optional'
//                                    WHEN 2 THEN 'Required'
//                                    END as isRequired,l.description
//                                from layout_options l WHERE l.title LIKE '$value3->screen_name' and l.form_id='LBF1' ";
                            $sql5 = "SELECT l.field_id, SUBSTRING(l.group_name ,-length(l.group_name),1) as grouporder,SUBSTRING(l.group_name FROM 2) as GroupName,
                                     l.description
                                from layout_options l WHERE l.title LIKE '$value3->screen_name' and l.form_id='LBF1' ";
                            $stmt5 = $db->prepare($sql5);
                            $stmt5->execute();  
                            $datacheck2 = $stmt5->fetchAll(PDO::FETCH_OBJ); 
                            
                            $get_isRequired = "SELECT id,screen_names FROM tbl_allcare_facuservisit WHERE screen_group LIKE '%".$datacheck2[0]->GroupName."' AND `users`  REGEXP ('".":\"".$value->provider_id."\"') ";
                            $db->query( "SET NAMES utf8");
                            $isReq_stmt = $db->prepare($get_isRequired) ;
                            $isReq_stmt->execute();    
                            $req_datacheck = $isReq_stmt->fetchAll(PDO::FETCH_OBJ); 
                            $s_array = array();
                            $dataArray = '';
                            if(!empty($req_datacheck)){
                                $s_data = $req_datacheck[0]->screen_names;
                                $s_array[] =  unserialize($s_data);
                                for($j = 0; $j<count($s_array); $j++){
                                    foreach($s_array[$j] as $arraykey){
                                        if(strpos($arraykey,$datacheck2[0]->field_id) !== false){
                                           $dataArray = $arraykey;
                                        }
                                    }
                                }
                                $fields = explode('$$', $dataArray);
                                if(!empty($fields)){
                                    $datacheck2[0]->FormOrder = (isset($fields[0]) ? $fields[0] : '');
                                    $datacheck2[0]->isRequired = (isset($fields[1]) ? $fields[1] : '');
                                }else{
                                    $datacheck2[0]->FormOrder = '';
                                    $datacheck2[0]->isRequired = '';
                                }
                                $datacheck2[0]->id = $req_datacheck[0]->id;
                            }else{
                                $datacheck2[0]->FormOrder = '';
                                $datacheck2[0]->isRequired = '';
                                $datacheck2[0]->id = '';
                            }
                            
                            //echo "<pre>"; print_r($datacheck2); echo "</pre>"; 
                            $sql6 = "SELECT form_id from forms WHERE encounter = $eid and deleted = 0 and formdir='LBF2'";
                            $stmt6 = $db->prepare($sql6);
                            $stmt6->execute();  
                            $datacheck3 = $stmt6->fetchAll(PDO::FETCH_OBJ);
                            if(!empty($datacheck3)):
                                $form_id = $datacheck3[0]->form_id;
                            else:
                                $form_id = 0;
                            endif;
                            if($datacheck2[0]->field_id == 'physical_exam'):
                                $sql7 = "SELECT form_id  from forms WHERE encounter = $eid and deleted = 0 and form_name = 'Allcare Physical Exam' and formdir='allcare_physical_exam'";
                                $stmt7 = $db->prepare($sql7);
                                $stmt7->execute();  
                                $datacheck4 = $stmt7->fetchAll(PDO::FETCH_OBJ);
                                if(!empty($datacheck4)):
                                    $form_id = $datacheck4[0]->form_id;
                                else:
                                    $form_id = 0;
                                endif;
                                if($form_id != 0){
                                    $get_status = "SELECT * FROM tbl_allcare_formflag WHERE form_name='Allcare Physical Exam' AND encounter_id=$eid  AND form_id = $form_id";
                                    $f_status = $db->prepare($get_status);
                                    $f_status->execute();  
                                    $set_status = $f_status->fetchAll(PDO::FETCH_OBJ);
                                    if(!empty($set_status)){
                                        if($set_status[0]->pending == 'Y')
                                            $field_value = 'pending';
                                        elseif($set_status[0]->finalized == 'Y')
                                            $field_value = 'finalized';
                                        else
                                            $field_value = '';
                                    }else{
                                        if(!empty($datacheck[0]->field_value))
                                            $field_value = $datacheck[0]->field_value;
                                        else
                                            $field_value = '';
                                    }
                                }else{
                                        $field_value = '';
                                }
                            endif;
                            if($datacheck2[0]->field_id == 'ros'):
                                $sql8 = "SELECT form_id  from forms WHERE encounter = $eid and deleted = 0 and form_name = 'Allcare Review Of Systems' and formdir='allcare_ros'";
                                $stmt8 = $db->prepare($sql8);
                                $stmt8->execute();  
                                $datacheck5 = $stmt8->fetchAll(PDO::FETCH_OBJ);
                                if(!empty($datacheck5)):
                                    $form_id = $datacheck5[0]->form_id;
                                else:
                                    $form_id = 0;
                                endif;
                                if($form_id != 0){
                                    $get_status = "SELECT * FROM tbl_allcare_formflag WHERE form_name='Allcare Review of Systems' AND encounter_id=$eid  AND form_id = $form_id";
                                    $f_status = $db->prepare($get_status);
                                    $f_status->execute();  
                                    $set_status = $f_status->fetchAll(PDO::FETCH_OBJ);
                                    if(!empty($set_status)){
                                        if($set_status[0]->pending == 'Y')
                                            $field_value = 'pending';
                                        elseif($set_status[0]->finalized == 'Y')
                                            $field_value = 'finalized';
                                        else
                                            $field_value = '';
                                    }else{
                                        if(!empty($datacheck[0]->field_value))
                                            $field_value = $datacheck[0]->field_value;
                                        else
                                            $field_value = '';
                                    }
                                }else{
                                        $field_value = '';
                                }
                            endif;
                            if($datacheck2[0]->field_id == 'vitals'):
                                $sql9 = "SELECT form_id  from forms WHERE encounter = $eid and deleted = 0 and form_name = 'Vitals' and formdir='vitals'";
                                $stmt9 = $db->prepare($sql9);
                                $stmt9->execute();  
                                $datacheck6 = $stmt9->fetchAll(PDO::FETCH_OBJ);
                                if(!empty($datacheck6)):
                                    $form_id = $datacheck6[0]->form_id;
                                else:
                                    $form_id = 0;
                                endif;    
                                if($form_id != 0){
                                    $vitals  = "SELECT field_value FROM lbf_data WHERE form_id = $form_id AND field_id = 'vitals_stat'";
                                    $stmtV = $db->prepare($vitals);
                                    $stmtV->execute();
                                    $set_status = $stmtV->fetchAll(PDO::FETCH_OBJ);
                                    
                                    if(!empty($set_status)){
                                        if($set_status[0]->field_value == 'pending')
                                            $field_value = 'pending';
                                        elseif($set_status[0]->field_value == 'finalized')
                                            $field_value = 'finalized';
                                        elseif($set_status[0]->field_value == 'finalized|pending' || $set_status[0]->field_value == 'pending|finalized')
                                            $datacheck[0]->field_value = 'finalized';
                                        else
                                            $field_value = '';
                                    }else{
                                        if(!empty($datacheck[0]->field_value))
                                            $field_value = $datacheck[0]->field_value;
                                        else
                                            $field_value = '';
                                    }
                                }else{
                                        $field_value = '';
                                }
                            endif;
                            if($datacheck2[0]->field_id == 'family_exam_test' || $datacheck2[0]->field_id == 'family_history'|| $datacheck2[0]->field_id == 'family_med_con' || $datacheck2[0]->field_id == 'history_past' || $datacheck2[0]->field_id == 'history_social'):
//                                $form_id = 0;
//                                $field_value = '';
                                $get_form_status = "SELECT form_id FROM forms where deleted = 0 and formdir = 'LBF2' and form_name = 'Allcare Encounter Forms' and encounter = $eid order by id asc limit 0,1";
                                $stmtf = $db->prepare($get_form_status);
                                $stmtf->execute();
                                $set_form_status = $stmtf->fetchAll(PDO::FETCH_OBJ);
                                if(!empty($set_form_status)):
                                    $form_id = $set_form_status[0]->form_id;
                                else:
                                    $form_id = 0;
                                endif;    
                                if($form_id != 0){
                                    $vitals  = "SELECT field_value FROM lbf_data WHERE form_id = $form_id AND field_id = '".$datacheck2[0]->field_id."_stat'";
                                    $stmtV = $db->prepare($vitals);
                                    $stmtV->execute();
                                    $set_status = $stmtV->fetchAll(PDO::FETCH_OBJ);
                                    
                                    if(!empty($set_status)){
                                        if($set_status[0]->field_value == 'pending')
                                            $field_value = 'pending';
                                        elseif($set_status[0]->field_value == 'finalized')
                                            $field_value = 'finalized';
                                        elseif($set_status[0]->field_value == 'finalized|pending' || $set_status[0]->field_value == 'pending|finalized')
                                            $datacheck[0]->field_value = 'finalized';
                                        else
                                            $field_value = '';
                                    }else{
                                        if(!empty($datacheck[0]->field_value)){
                                             if($datacheck[0]->field_value == 'pending')
                                                $field_value = 'pending';
                                            elseif($datacheck[0]->field_value == 'finalized')
                                                $field_value = 'finalized';
                                            elseif(trim($datacheck[0]->field_value) == 'finalized|pending' || trim($datacheck[0]->field_value) == 'pending|finalized')
                                                $datacheck[0]->field_value = 'finalized';
                                            else
                                                $field_value = '';
                                        }else
                                            $field_value = '';
                                    }
                                }else{
                                        $field_value = '';
                                }
                            endif;
                            if($datacheck2[0]->field_id  == 'allergies' || $datacheck2[0]->field_id  == 'dental_problems'  || $datacheck2[0]->field_id  == 'immunization' || $datacheck2[0]->field_id  == 'medical_problem'|| $datacheck2[0]->field_id  == 'medication' ):
                                $form_id = 0;
                                $field_value = '';
                            endif;
                            if($datacheck2[0]->field_id  == 'face2face' ){
                                $get_form_status = "SELECT form_id FROM forms where deleted = 0 and formdir = 'LBF2' and form_name = 'Allcare Encounter Forms' and encounter = $eid order by id asc limit 0,1";
                                $stmtf = $db->prepare($get_form_status);
                                $stmtf->execute();
                                $set_form_status = $stmtf->fetchAll(PDO::FETCH_OBJ);
                                if(!empty($set_form_status)):
                                    $form_id = $set_form_status[0]->form_id;
                                else:
                                    $form_id = 0;
                                endif;    
                                if($form_id != 0){
                                    $vitals  = "SELECT field_value FROM lbf_data WHERE form_id = $form_id AND field_id = 'f2f_stat'";
                                    $stmtV = $db->prepare($vitals);
                                    $stmtV->execute();
                                    $set_status = $stmtV->fetchAll(PDO::FETCH_OBJ);
                                    
                                    if(!empty($set_status)){
                                        if($set_status[0]->field_value == 'pending')
                                            $field_value = 'pending';
                                        elseif($set_status[0]->field_value == 'finalized')
                                            $field_value = 'finalized';
                                        elseif($set_status[0]->field_value == 'finalized|pending' || $set_status[0]->field_value == 'pending|finalized')
                                            $field_value = 'finalized';
                                        else
                                            $field_value = '';
                                    }else{
                                        if(!empty($datacheck2[0]->field_value)){
                                            if($datacheck2[0]->field_value == 'pending')
                                                $field_value = 'pending';
                                            elseif($datacheck2[0]->field_value == 'finalized')
                                                $field_value = 'finalized';
                                            elseif(trim($datacheck2[0]->field_value) == 'finalized|pending' || trim($datacheck2[0]->field_value) == 'pending|finalized')
                                                $field_value = 'finalized';
                                            else
                                                $field_value = '';
                                        }else
                                            $field_value = '';
                                    }
                                }else{
                                        $field_value = '';
                                }
                            }
                            if($datacheck2[0]->field_id  == 'codes'){
                                $selectquery = "SELECT b.billed
                                        FROM billing b 
                                        INNER JOIN form_encounter f ON b.encounter = f.encounter  
                                        WHERE b.encounter =   $eid and code_type='CPT4' and b.activity = 1 order by b.date desc ";
                                $stmtb = $db->prepare($selectquery);
                                $stmtb->execute();
                                $set_billing = $stmtb->fetchAll(PDO::FETCH_OBJ);
                                if(!empty($set_billing)){
                                    $form_id = 0;
                                    $billing = $set_billing[0]-> billed;
                                    if($billing == 0){
                                        $field_value = ' Not Billed';
                                    }else{
                                        $field_value = 'Billed';
                                    }
                                        
                                }else{
                                     $field_value = 'Not Billed';
                                }
                            }
                            if($datacheck2[0]->field_id == 'auditform'):
                                $sqla = "SELECT form_id  from forms WHERE encounter = $eid and deleted = 0 and form_name = 'Audit Form' and formdir='auditform'";
                                $stmta = $db->prepare($sqla);
                                $stmta->execute();  
                                $datachecka = $stmta->fetchAll(PDO::FETCH_OBJ);
                                if(!empty($datachecka)):
                                    $form_id = $datachecka[0]->form_id;
                                else:
                                    $form_id = 0;
                                endif;    
                                if($form_id != 0){
                                    $get_audit = "SELECT audit_data FROM tbl_form_audit WHERE id = $form_id ";
                                    $stmta = $db->prepare($get_audit);
                                    $stmta->execute();
                                    $set_status = $stmta->fetchAll(PDO::FETCH_OBJ);
                                    if(!empty($set_status)){
                                        $unserialized_data = unserialize($set_status[0]->audit_data);
                                        if(trim(str_replace('CPT Code:','',$unserialized_data['hiddenaudit'])) == 'None') 
                                            $field_value = 'Not Audited';
                                        else
                                            $field_value = 'Audited';
                                    }else{
                                        if(!empty($datacheck2[0]->field_value))
                                            $field_value = $datacheck2[0]->field_value;
                                        else
                                            $field_value = '';
                                    }
                                }else{
                                        $field_value = '';
                                }
                            endif;
                            if($datacheck2[0]->field_id == 'cpo'):
                                $sqlc = "SELECT form_id  from forms WHERE encounter = $eid and deleted = 0 and form_name = 'CPO' and formdir='cpo'";
                                $stmtc = $db->prepare($sqlc);
                                $stmtc->execute();  
                                $datacheckc = $stmtc->fetchAll(PDO::FETCH_OBJ);
                                if(!empty($datacheckc)):
                                    $form_id = $datacheckc[0]->form_id;
                                else:
                                    $form_id = 0;
                                endif;    
                            endif; 
                            if($datacheck2[0]->field_id == 'ccm'):
                                $sqlcm = "SELECT form_id  from forms WHERE encounter = $eid and deleted = 0 and form_name = 'CCM' and formdir='ccm'";
                                $stmtcm = $db->prepare($sqlcm);
                                $stmtcm->execute();  
                                $datacheckcm = $stmtcm->fetchAll(PDO::FETCH_OBJ);
                                if(!empty($datacheckcm)):
                                    $form_id = $datacheckcm[0]->form_id;
                                else:
                                    $form_id = 0;
                                endif;    
                            endif; 
                            $formvalues[] = (object)array('form_type' => $datacheck2[0]->GroupName,  'form_id'=> $form_id, 'form_name' =>$datacheck2[0]->field_id, 'FormOrder' =>$datacheck2[0]->FormOrder, 'grouporder' => $datacheck2[0]->grouporder, 'GroupName' => $value3->screen_name, 'isRequired' => $datacheck2[0]->isRequired , 'description' => $datacheck2[0]->description, 'field_value' => $field_value, 'id'=>$datacheck2[0]->id);

                        }else{ 
                            if($datacheck[0]->form_name == 'vitals'):
                                $sql9 = "SELECT form_id  from forms WHERE encounter = $eid and deleted = 0 and form_name = 'Vitals' and formdir='vitals'";
                                $stmt9 = $db->prepare($sql9);
                                $stmt9->execute();  
                                $datacheck6 = $stmt9->fetchAll(PDO::FETCH_OBJ);
                                if(!empty($datacheck6)):
                                    $datacheck[0]->form_id = $datacheck6[0]->form_id;
                                else:
                                    $datacheck[0]->form_id = 0;
                                endif;  
                                if($form_id != 0){
                                    $vitals  = "SELECT field_value FROM lbf_data WHERE form_id = $form_id AND field_id = 'vitals_stat'";
                                    $stmtV = $db->prepare($vitals);
                                    $stmtV->execute();
                                    $set_status = $stmtV->fetchAll(PDO::FETCH_OBJ);
                                    
                                    if(!empty($set_status)){
                                        if($set_status[0]->field_value == 'pending')
                                            $field_value = 'pending';
                                        elseif($set_status[0]->field_value == 'finalized')
                                            $field_value = 'finalized';
                                        elseif($set_status[0]->field_value == 'finalized|pending' || $set_status[0]->field_value == 'pending|finalized')
                                            $datacheck[0]->field_value = 'finalized';
                                        else
                                            $field_value = '';
                                    }else{
                                        if(!empty($datacheck[0]->field_value))
                                            $field_value = $datacheck[0]->field_value;
                                        else
                                            $field_value = '';
                                    }
                                }else{
                                        $field_value = '';
                                }
                            endif;
                            
                            if($datacheck[0]->form_name == 'family_exam_test' || $datacheck[0]->form_name == 'family_history'|| $datacheck[0]->form_name == 'family_med_con' || $datacheck[0]->form_name == 'history_past' || $datacheck[0]->form_name == 'history_social'):
                               // $datacheck[0]->form_id = 0;
                                $get_form_status = "SELECT form_id FROM forms where deleted = 0 and formdir = 'LBF2' and form_name = 'Allcare Encounter Forms' and encounter = $eid order by id asc limit 0,1";
                                $stmtf = $db->prepare($get_form_status);
                                $stmtf->execute();
                                $set_form_status = $stmtf->fetchAll(PDO::FETCH_OBJ);
                                if(!empty($set_form_status)):
                                    $form_id = $set_form_status[0]->form_id;
                                else:
                                    $form_id = 0;
                                endif;    
                                if($form_id != 0){
                                    $vitals  = "SELECT field_value FROM lbf_data WHERE form_id = $form_id AND field_id = '".$datacheck[0]->form_name."_stat'";
                                    $stmtV = $db->prepare($vitals);
                                    $stmtV->execute();
                                    $set_status = $stmtV->fetchAll(PDO::FETCH_OBJ);
                                    
                                    if(!empty($set_status)){
                                        if($set_status[0]->field_value == 'pending')
                                            $field_value = 'pending';
                                        elseif($set_status[0]->field_value == 'finalized')
                                            $field_value = 'finalized';
                                        elseif(trim($set_status[0]->field_value) == 'finalized|pending' || trim($set_status[0]->field_value) == 'pending|finalized'){
                                            $datacheck[0]->field_value = 'finalized';
                                        }
                                            
                                        else
                                            $field_value = '';
                                    }else{ 
                                        if(!empty($datacheck[0]->field_value)){
                                            if($datacheck[0]->field_value == 'pending')
                                                $field_value = 'pending';
                                            elseif($datacheck[0]->field_value == 'finalized')
                                                $field_value = 'finalized';
                                            elseif(trim($datacheck[0]->field_value) == 'finalized|pending' || trim($datacheck[0]->field_value) == 'pending|finalized')
                                                $datacheck[0]->field_value = 'finalized';
                                            else
                                            $field_value = '';
                                        }else
                                            $field_value = '';
                                    }
                                }else{
                                        $field_value = '';
                                }
                            endif;
                            if($datacheck[0]->form_name  == 'allergies' || $datacheck[0]->form_name  == 'dental_problems' || $datacheck[0]->form_name  == 'immunization' || $datacheck[0]->form_name  == 'medical_problem'|| $datacheck[0]->form_name  == 'medication'):
                               $datacheck[0]->form_id = 0;
                            endif;
                            if($datacheck[0]->form_name  == 'codes'){
                                $selectquery = "SELECT b.billed
                                        FROM billing b 
                                        INNER JOIN form_encounter f ON b.encounter = f.encounter  
                                        WHERE b.encounter =   $eid and code_type='CPT4' and b.activity = 1 order by b.date desc ";
                                $stmtb = $db->prepare($selectquery);
                                $stmtb->execute();
                                $set_billing = $stmtb->fetchAll(PDO::FETCH_OBJ);
                                if(!empty($set_billing)){
                                    $datacheck[0]->form_id = 0;
                                    $billing = $set_billing[0]-> billed;
                                    if($billing == 0){
                                        $datacheck[0]->field_value = 'Not billed';
                                    }else{
                                        $datacheck[0]->field_value = 'Billed';
                                    }
                                        
                                }else{
                                     $datacheck[0]->field_value = 'Not billed';
                                }
                            }
                            if($datacheck[0]->form_name == 'auditform'):
                                $sqla = "SELECT form_id  from forms WHERE encounter = $eid and deleted = 0 and form_name = 'Audit Form' and formdir='auditform'";
                                $stmta = $db->prepare($sqla);
                                $stmta->execute();  
                                $datachecka = $stmta->fetchAll(PDO::FETCH_OBJ);
                                if(!empty($datachecka)):
                                    $form_id = $datachecka[0]->form_id;
                                else:
                                    $form_id = 0;
                                endif;    
                                if($form_id != 0){
                                   $get_audit = "SELECT audit_data FROM tbl_form_audit WHERE id = $form_id ";
                                    $stmta = $db->prepare($get_audit);
                                    $stmta->execute();
                                    $set_status = $stmta->fetchAll(PDO::FETCH_OBJ);
                                    if(!empty($set_status)){
                                        $unserialized_data = unserialize($set_status[0]->audit_data);
                                        if(trim(str_replace('CPT Code:','',$unserialized_data['hiddenaudit'])) == 'None') 
                                            $datacheck[0]->field_value = 'Not Audited';
                                        else
                                            $datacheck[0]->field_value = 'Audited';
                                    }else{
                                        $datacheck[0]->field_value = '';
                                    }
                                }else{
                                        $datacheck[0]->field_value = '';
                                }
                            endif;
                            if($datacheck[0]->form_name == 'cpo'):
                                $sqlc = "SELECT form_id  from forms WHERE encounter = $eid and deleted = 0 and form_name = 'CPO' and formdir='cpo'";
                                $stmtc = $db->prepare($sqlc);
                                $stmtc->execute();  
                                $datacheckc = $stmtc->fetchAll(PDO::FETCH_OBJ);
                                if(!empty($datacheckc)):
                                    $datacheck[0]->form_id = $datacheckc[0]->form_id;
                                else:
                                    $datacheck[0]->form_id = 0;
                                endif;    
                            endif;    
                            if($datacheck[0]->form_name == 'ccm'):
                                $sqlcm = "SELECT form_id  from forms WHERE encounter = $eid and deleted = 0 and form_name = 'CCM' and formdir='ccm'";
                                $stmtcm = $db->prepare($sqlcm);
                                $stmtcm->execute();  
                                $datacheckcm = $stmtcm->fetchAll(PDO::FETCH_OBJ);
                                if(!empty($datacheckcm)):
                                    $datacheck[0]->form_id = $datacheckcm[0]->form_id;
                                else:
                                    $datacheck[0]->form_id = 0;
                                endif;    
                            endif;    
                           $formvalues[] = $datacheck[0];//$stmt4->fetchAll(PDO::FETCH_OBJ); 
                        }
                }
             } 
        }
        $itemArray = array();
        foreach($formvalues as $item) {
           $itemArray[] = (array)$item;
        }
//        $sorted2 =uasort($itemArray, 'cmp');
        $sorted = array_orderby($itemArray, 'FormOrder', SORT_ASC);
        $arr = array();
        for($i=0; $i<count($sorted); $i++){
            $check = array_search('Unused', $sorted[$i],TRUE);
            if(empty($check)){
                $arr[] = $sorted[$i];
            }
        }
//        echo "<pre>"; print_r($arr);echo "</pre>";
        
             $new = encode_demo(array_filter( $arr));
             $newdemo['FormsData'] = check_data_available($new);
            if($newdemo) {
                $newdemores = json_encode($newdemo);
                echo $incompletelistresult = GibberishAES::enc($newdemores, $apikey);

            }else
            {
                $demo1='[{"id":"0"}]';
                $newdemo1=encode_demo($demo1);      
                $newdemo['FormsData'] = check_data_available($newdemo1);
                $newdemores = json_encode($newdemo);
                echo $incompletelistresult = GibberishAES::enc($newdemores, $apikey);
            }
    } 
    catch(PDOException $e) 
    {

        $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo $incompletelistresult = GibberishAES::enc($error, $apikey);
    }
  
}
?>
<!DOCTYPE HTML>
<html>
	<head>
		<title>Medical Website Template | News :: W3layouts</title>
		<link href="css/style.css" rel="stylesheet" type="text/css"  media="all" />
		<link href='https://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
                <link rel='stylesheet' type='text/css' href='../interface/main/css/jquery.dataTables.css'>
                <link rel='stylesheet' type='text/css' href='../interface/main/css/dataTables.tableTools.css'>
                <link rel='stylesheet' type='text/css' href='../interface/main/css/dataTables.colVis.css'>
                <link rel='stylesheet' type='text/css' href='../interface/main/css/dataTables.colReorder.css'>
                <style>
                div.DTTT_container {
                        float: none;
                }
                </style>
                <script type='text/javascript' src='../interface/main/js/jquery-1.11.1.min.js'></script>
<!--                <script type='text/javascript' src='../interface/main/js/jquery.dataTables.min.js'></script>-->
                <script type='text/javascript' src='../interface/main/js/jquery.dataTables-1.10.7.min.js'></script>
                <script type='text/javascript' src='../interface/main/js/dataTables.tableTools.js'></script>
                <script type='text/javascript' src='../interface/main/js/dataTables.colReorder.js'></script>
                <script type='text/javascript' src='../interface/main/js/dataTables.colVis.js'></script>
	</head>
	<body>
		<!---start-wrap---->
		
			<!---start-header---->
			<div class="header">
				
					<div class="main-header">
						<div class="wrap">
							<div class="logo">
								<a href="index.html"><img src="images/logo.png" title="logo" /></a>
							</div>
							<div class="social-links">
								<ul>
									
                                                                      <li class="login"><a href="logout_page.php">Logout</a></li>
									<div class="clear"> </div>
								</ul>
							</div>
							<div class="clear"> </div>
						</div>
					</div>
					<div class="clear"> </div>
				             <div id='cssmenu1'>
                                            <?php $sql12=sqlStatement("SELECT * FROM  `list_options` WHERE list_id ='AllCareProviderPortal' ORDER BY seq");?>
                                              <ul>   
                                                 <?php while($row11=sqlFetchArray($sql12)){ 
                                                        $mystring = $row11['option_id'];
                                                        $pos = strpos($mystring, '_');
                                                        if(false == $pos) {
                                                                $sql_lis=sqlStatement("SELECT * FROM  `list_options` WHERE list_id ='AllCareProviderPortal' AND option_id = '$mystring' ORDER BY seq");
                                                                while($row_lis=sqlFetchArray($sql_lis)){
                                                                $opt_id=$row_lis['option_id']."_";
                                                                $sql_li=sqlStatement("SELECT * FROM  `list_options` WHERE list_id ='AllCareProviderPortal' AND option_id LIKE '%$opt_id%' ORDER BY seq");
                                                                if(sqlNumRows($sql_li) != 0 ){ ?>
                                                                     <li <?php if($row11['option_id']=='incomp'){ ?> class='active has-sub' <?php } else {  ?> class='has-sub' <?php } ?>><a href="<?php echo $row_lis['notes']; ?>?provider=<?php echo $provider;  ?>"><span><?php echo $row_lis['title']; ?></span></a>
                                                                     <ul>
                                                                 <?php while($row_li=sqlFetchArray($sql_li)){ 
                                                                             $ex=explode("_",$row_li['option_id']); 
                                                                             if(count($ex)==2){
                                                                                   $sub1=$ex[0]."_".$ex[1];
                                                                                   $sql_sub=sqlStatement("SELECT * FROM  `list_options` WHERE list_id ='AllCareProviderPortal' AND option_id = '$sub1' ORDER BY seq");
                                                                                   $row_sub=sqlFetchArray($sql_sub);
                                                                                   ?>
                                                                                    <li class=last'><a href="<?php echo $row_sub['notes']; ?>?provider=<?php echo $provider;  ?>"><span><?php  echo $row_sub['title']; ?></span></a> 
                                                                                   </li>   
                                                                            <?php   } ?>
                                                                             
                                                                    <?php } ?> </ul></li>
                                                                <?php }else { 
                                                                    ?>
                                                                     <li><a href="<?php echo $row11['notes']; ?>?provider=<?php echo $provider;  ?>"><span><?php echo $row11['title']; ?></span></a></li>
                                                               <?php 
                                                                
                                                                }
                                                               
                                                            }    
                                                         }
                                                         
                                                    } ?>
                                              </ul>      
                                        </div>
			</div>
			<!---End-header---->
			<!----start-content----->
			<div class="content">
				<div class="wrap">
					<div class="services">
						<div class="service-content">

                                                    <h3>Incomplete Encounter Count : <?php  echo getIncompleteEncounterCount($provider_id);    ?></h3>
                                                 <?php $result= getIncompleteEncounterList($pid,$provider_id); ?>
                                                <table border="1">
                                                    <thead style='background-color:#87CEFA; height:50px;'><tr>
                                                        <th style="padding: 10px 18px; ">Patient_id</th><th style=" padding: 10px 18px; ">Patient_name</th><th style="padding: 10px 18px;">Date_of_service</th><th style=" padding: 10px 18px;">Encounter</th><th style=" padding: 10px 18px;">Visit_category</th><th style=" padding: 10px 18px;">Facility</th><th style=" padding: 10px 18px;">Audit_status</th>
                                                        </tr></thead>

                                                      <?php    for($i = 0; $i<count($result); $i++){?>
                                                     <tr>
                                                     <td style=" padding: 5px; "><?php echo $result[$i]['pid']; ?></td>
                                                     <td style="padding: 5px ; "><?php echo $result[$i]['pname']; ?></td>
                                                     <td style="padding: 5px ; "><?php echo $result[$i]['dos']; ?></td>
                                                     <td style="padding: 5px ; "><?php echo $result[$i]['encounter']; ?></td>
                                                     <td style="padding: 5px ; "><?php echo $result[$i]['visitcategory']; ?></td>
                                                     <td style="padding: 5px ; "><?php echo $result[$i]['facility']; ?></td>
                                                     <td style="padding: 5px ; "><?php echo $result[$i]['audited_status']; ?></td></tr>
                                                <?php }?>
                                                  </table>
                                               </div>

						<div class="clear"> </div>
					</div>
				<div class="clear"> </div>
				</div>
			<!----End-content----->
		</div>
		<!---End-wrap---->
              
	</body>
</html>

