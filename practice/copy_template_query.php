<?php
require_once("verify_session.php");
 
if(isset($_SESSION['portal_username']) !=''){
    $provider    = $_SESSION['portal_username'];
    $refer       = $_REQUEST['refer'];
    
    if($_REQUEST['refer']!='')
        $_SESSION['refer'] = $_REQUEST['refer'];
}else {
    $provider                    = $_REQUEST['provider'];
    $_SESSION['portal_username'] = $_REQUEST['provider'];
    //for logout
    $refer                       = $_REQUEST['refer'];
    if($_REQUEST['refer']!='')
        $_SESSION['refer'] = $_REQUEST['refer'];
}

require_once("../interface/globals.php");

echo $copy_to_encounter = $_POST['copy_to_fname1'];
$copy_from_id      = $_POST['copy_from_id'];

$form_name         = $_POST['form_name'];

$pid               = $_REQUEST['pid'];

if($form_name  == 'Chief Complaint'){
   $field_id_txt   = 'chief_complaint';
} else if($form_name == 'History of Present illness'){
   $field_id_txt   = 'hpi';
} else if($form_name == 'Progress Note') {
   $field_id_txt = 'progress_note';
} else if($form_name   == 'Assessment Note'){
   $field_id_txt   ='assessment_note';
} else if($form_name   == 'Plan Note'){
   $field_id_txt   = 'plan_note';
}  else if($form_name  == 'Face to Face HH Plan'){
   $field_id_txt   = 'f2f';
} 

$ip_addr    = GetIP();
$ip_addr    = $ip_addr."(provider portal)";

 
if($field_id_txt    != '') {
    $enc_id                 = sqlStatement("select encounter from forms where form_id=$copy_from_id AND deleted=0 AND formdir='LBF2'");
    $res_enc                = sqlFetchArray($enc_id);
    $copy_from_encounter    = $res_enc['encounter'];
    $r1     = sqlStatement("SELECT lb.*
                        FROM lbf_data lb
                        INNER JOIN layout_options l ON lb.field_id = l.field_id
                        INNER JOIN form_encounter f
                        WHERE lb.form_id ='".$copy_from_id."'  AND f.encounter=".$res_enc['encounter']."
                        AND l.group_name LIKE '%$form_name%' AND lb.field_id LIKE  '%$field_id_txt%'
                        ORDER BY seq");
    while ($frow2 = sqlFetchArray( $r1)) { 
        $ext[]          = $frow2;
        $field_id1[]    = $frow2['field_id'];
                   
    }
    if($copy_to_encounter!=''){
        $form_sql   = sqlStatement("SELECT DISTINCT form_name, form_id, formdir,encounter
                            FROM forms
                            WHERE encounter =$copy_to_encounter
                            AND deleted =0 AND form_name='Allcare Encounter Forms'
                            GROUP BY form_name
                            ORDER BY id DESC ");

        $row_form   = sqlFetchArray($form_sql);
          
        $form_sql1  = sqlStatement("SELECT DISTINCT form_name, form_id, formdir,encounter
                            FROM forms
                            WHERE encounter =$copy_to_encounter
                            AND deleted =0 AND form_name='Allcare Encounter Forms'
                            GROUP BY form_name
                            ORDER BY id DESC ");
         
        $row   = sqlFetchArray($form_sql1); 
        $form_id    = $row['form_id'];
            
        if(empty($row_form)) {

            global $form_id;
//            $sql_pid    = sqlStatement("select * from forms where encounter=$copy_to_encounter");  
//            $row_pid    = sqlFetchArray($sql_pid); 
            
            $sql_form   = sqlStatement("select max(form_id)as new_form from forms where form_name='Allcare Encounter Forms' AND formdir='LBF2'");
            $row_form1  = sqlFetchArray($sql_form);
            $new_fid    = $row_form1['new_form'];
            $new_id1    = ++$new_fid;
          
            $ins_form   = sqlStatement("INSERT INTO forms (date,encounter,form_name,form_id,pid,user,groupname,authorized,deleted,formdir) VALUES (NOW(),$copy_to_encounter,'Allcare Encounter Forms',$new_id1,$pid,'$provider','default',1,0,'LBF2')");
            $row1_form  = sqlFetchArray($ins_form);

            $form_id        = $new_id1;
            $r2     = sqlStatement("SELECT lb.*
                        FROM lbf_data lb
                        INNER JOIN layout_options l ON lb.field_id = l.field_id
                        INNER JOIN form_encounter f
                        WHERE lb.form_id ='".$form_id."'  AND f.encounter=".$copy_to_encounter."
                        AND l.group_name LIKE '%$form_name%' AND lb.field_id LIKE  '%$field_id_txt%'
                        ORDER BY seq");
            while($frow3 = sqlFetchArray( $r2)){
                 $field_id[]    = $frow3['field_id'];
                 $ext3[]        = $frow3;
            }
            $diff   = array_diff($field_id1,$field_id);
            $diff1  = array_diff($field_id,$field_id1);
            if($diff1){
                foreach($diff1 as $val){
                    $delete = sqlStatement("delete from lbf_data where form_id=$form_id AND field_id='$val'");
                }
            }
            if($diff){
                foreach($ext as $key=>$value){
                    if(in_array($value['field_id'],$diff))  {
                        if($value['field_id']!='f2f_ps' && $value['field_id']!='f2f_ps_on' && $value['field_id']!='f2f_printed'){
                            $sql = "INSERT into lbf_data (form_id, field_id, field_value) VALUES ('".$form_id."','".$value['field_id']."','".addslashes($value['field_value'])."')";
                            sqlInsert($sql);
                        }
                    } else {
                        if($value['field_id']!='f2f_ps' && $value['field_id']!='f2f_ps_on' && $value['field_id']!='f2f_printed'){
                            $sql = sqlStatement("UPDATE lbf_data SET field_value = '".addslashes($value['field_value'])."' WHERE field_id ='".$value['field_id']."'  AND form_id = '".$form_id."'");
                        }
                    }
                 }

            } else {
                if(empty($diff) && !empty($ext3)){
                    foreach($ext as $key=>$value){
                        if($value['field_id']!='f2f_ps' && $value['field_id']!='f2f_ps_on' && $value['field_id']!='f2f_printed'){
                            $sql = sqlStatement("UPDATE lbf_data SET field_value = '".addslashes($value['field_value'])."' WHERE field_id ='".$value['field_id']."'  AND form_id = '".$form_id."'");
                      }
                    }
                } else if(empty($ext3)) {
                    foreach($ext as $key=>$value){
                        if($value['field_id']!='f2f_ps' && $value['field_id']!='f2f_ps_on' && $value['field_id']!='f2f_printed'){
                            $sql = "INSERT into lbf_data (form_id, field_id, field_value) VALUES ('".$form_id."','".$value['field_id']."','".addslashes($value['field_value'])."')";
                            sqlInsert($sql);
                        }
                    }

                }
            }     
        } else { 

            $r2 = sqlStatement("SELECT lb.*
                        FROM lbf_data lb
                        INNER JOIN layout_options l ON lb.field_id = l.field_id
                        INNER JOIN form_encounter f
                        WHERE lb.form_id ='".$row_form['form_id']."'  AND f.encounter=".$row_form['encounter']."
                        AND l.group_name LIKE '%$form_name%' AND lb.field_id LIKE  '%$field_id_txt%'
                        ORDER BY seq");
            while($frow3 = sqlFetchArray( $r2)){
                $field_id[] = $frow3['field_id'];
                $pos = strpos($frow3['field_id'], '_text');
                if(strpos($frow3['field_id'], '_text') && $frow3['field_value']!='')
                    $sample=1;
                else
                    $ext3[]= $frow3;
                
            }
            $diff   = array_diff($field_id1,$field_id);
            $diff1  = array_diff($field_id,$field_id1);

            if($diff1){
                foreach($diff1 as $val){
                    $delete = sqlStatement("delete from lbf_data where form_id=$form_id AND field_id='$val'");
                }
            }
            if($diff){
                foreach($ext as $key=>$value){
                    if(in_array($value['field_id'],$diff))  {
                        if($value['field_id']!='f2f_ps' && $value['field_id']!='f2f_ps_on' && $value['field_id']!='f2f_printed'){
                           $sql = "INSERT into lbf_data (form_id, field_id, field_value) VALUES ('".$form_id."','".$value['field_id']."','".addslashes($value['field_value'])."')";
                           sqlInsert($sql);
                        }
                    } else {
                        if($value['field_id']!='f2f_ps' && $value['field_id']!='f2f_ps_on' && $value['field_id']!='f2f_printed'){
                            if(strpos($value['field_id'], '_text') && $sample!=1)
                            $sql = sqlStatement("UPDATE lbf_data SET field_value = '".addslashes($value['field_value'])."' WHERE field_id ='".$value['field_id']."'  AND form_id = '".$form_id."'");
                        }  
                    }
                 }
                 
            } else {
                if(empty($diff) && !empty($ext3)){
                    foreach($ext as $key=>$value){
                        if($value['field_id']!='f2f_ps' && $value['field_id']!='f2f_ps_on' && $value['field_id']!='f2f_printed'){
                            if(strpos($value['field_id'], '_text') && $sample!=1)
                            $sql = sqlStatement("UPDATE lbf_data SET field_value = '".addslashes($value['field_value'])."' WHERE field_id ='".$value['field_id']."'  AND form_id = '".$form_id."'");
                        }
                    }
                } else if(empty($ext3)) {
                    foreach($ext as $key=>$value){
                        if($value['field_id']!='f2f_ps' && $value['field_id']!='f2f_ps_on' && $value['field_id']!='f2f_printed'){   
                           $sql = "INSERT into lbf_data (form_id, field_id, field_value) VALUES ('".$form_id."','".$value['field_id']."','".addslashes($value['field_value'])."')";
                           sqlInsert($sql);
                        }   
                    }
                }
            }
        }

        $ins_log = "insert into tbl_allcare_template (date,copy_from_enc,copy_from_id,copy_to_enc,copy_to_id,form_name,user) values (NOW(),$copy_from_encounter,$copy_from_id,$copy_to_encounter,$form_id,'$form_name','$provider')";
        sqlInsert($ins_log);
           
   }
   echo "LBF2-".$form_id."$form_name";
}
else if($form_name=='Allcare Physical Exam'){
    
    $enc_id2    = sqlStatement("select encounter from forms where form_id=$copy_from_id AND deleted=0 AND formdir='allcare_physical_exam'");
    $res_enc2   = sqlFetchArray($enc_id2);
    
    $copy_from_encounter = $res_enc2['encounter'];
    
    $enc_id1    = sqlStatement("select form_id from forms where encounter=$copy_to_encounter AND deleted=0 AND formdir='allcare_physical_exam'");
    $res_enc1   = sqlFetchArray($enc_id1);
    if($res_enc1['form_id']!=''){
        $copy_to_id     = $res_enc1['form_id'];

        $pe_sql     = sqlStatement("SELECT * FROM tbl_form_physical_exam WHERE forms_id= $copy_from_id");
        while($pe_row = sqlFetchArray($pe_sql)){
          $pe_result[] = $pe_row;
        }

        $pe_sql1    = sqlStatement("SELECT * FROM tbl_form_physical_exam WHERE forms_id= $copy_to_id");
        while($pe_row1  = sqlFetchArray($pe_sql1)){
            $pe_result1[]   = $pe_row1;
            $line_id[]      = $pe_row1['line_id'];
        } 

        if(!empty($pe_result1)){
            $delete=sqlStatement("delete  from tbl_form_physical_exam where forms_id=$copy_to_id ");
            foreach($pe_result as $val1){
                sqlInsert("INSERT INTO tbl_form_physical_exam ( " .
                    "forms_id, line_id, wnl, abn, diagnosis, comments " .
                    ") VALUES (?,?,?,?,?,?)",array($copy_to_id,$val1['line_id'],$val1['wnl'],$val1['abn'],$val1['diagnosis'],$val1['comments']));
            } 
                   //this is for pe finalized and pending details
            $form_flag  =   sqlStatement("SELECT * 
                                    FROM  `tbl_allcare_formflag` 
                                    WHERE form_id =$copy_from_id AND form_name='Allcare Physical Exam' order by id desc");
            while($row1 = sqlFetchArray($form_flag)){
                $finalized  = $row1['finalized'];
                $pending    = $row1['pending'];
            }
            $logdata    = array();
            $data       = mysql_query("SELECT logdate from `tbl_allcare_formflag` WHERE  form_id=".$copy_to_id);
            while ($row = mysql_fetch_array($data,MYSQL_ASSOC)) {
                $array  = unserialize($row['logdate']);
                $count  = count($array);
            }
            $res    = sqlStatement("SELECT * FROM `tbl_allcare_formflag` WHERE form_id = '$copy_to_id'");
            $row1   = sqlFetchArray($res);
            $count  = isset($count)? $count: 0;

            if(empty($row1['form_id'])){
            $array2[]   = array( 'authuser' =>$provider,'pending' => $pending,'finalized' => $finalized, 'date' => date("Y/m/d"), 'action'=>'Copied' ,'ip_address'=>$ip_addr,'count'=> $count+1);
            $logdata    = array_merge_recursive($array, $array2);
            $logdata    = ($logdata? serialize($logdata): serialize($array2) );
            $query1     = "INSERT INTO tbl_allcare_formflag ( " .
                            "encounter_id,form_id, form_name,pending,finalized, logdate" .
                            ") VALUES ( " .
                            "".$copy_to_encounter.",'".$copy_to_id."', 'Allcare Physical Exam','$pending', '$finalized', '".$logdata."' " .
                            ")";
            sqlInsert($query1);
            } 
            else {
                $array2[] = array( 'authuser' =>$provider,'pending' => $pending,'finalized' => $finalized, 'date' => date("Y/m/d"), 'action' => 'Updated/Copied','ip_address'=>$ip_addr ,'count'=> $count+1);
                $logdata = array_merge_recursive($array, $array2);
                $logdata= ($logdata? serialize($logdata): serialize($array2) );
                $query2 = "UPDATE tbl_allcare_formflag SET pending = '$pending', finalized = '$finalized',logdate =  '".$logdata."' WHERE  form_id = ".$copy_to_id ." AND encounter_id = '$copy_to_encounter' AND form_name= 'Allcare Physical Exam' ";
                sqlStatement($query2);
            }

            $ins_log = "insert into tbl_allcare_template (date,copy_from_enc,copy_from_id,copy_to_enc,copy_to_id,form_name,user) values (NOW(),$copy_from_encounter,$copy_from_id,$copy_to_encounter,$copy_to_id,'$form_name','$provider')";
            sqlInsert($ins_log);

        }
    } else {
        $sql_pid    = sqlStatement("select * from forms where encounter=$copy_to_encounter");  
        $row_pid    = sqlFetchArray($sql_pid); 
         
        $sql_form   = sqlStatement("select max(form_id)as new_form from forms where form_name='Allcare Physical Exam' AND formdir='allcare_physical_exam'");
        $row_form   = sqlFetchArray($sql_form);
        $new_fid    = $row_form['new_form'];
        $new_id1    = ++$new_fid;
        
        $ins_form   = sqlStatement("INSERT INTO forms (date,encounter,form_name,form_id,pid,user,groupname,authorized,deleted,formdir) VALUES (NOW(),$copy_to_encounter,'Allcare Physical Exam',$new_id1,$pid,'$provider','default',1,0,'allcare_physical_exam')");
        $row1_form  = sqlFetchArray($ins_form);
        $copy_to_id = $new_id1;
    
        $pe_sql = sqlStatement("SELECT * FROM tbl_form_physical_exam WHERE forms_id= $copy_from_id");
        while($pe_row   = sqlFetchArray($pe_sql)){
            $pe_result[]   = $pe_row;
        }
        $pe_sql1    = sqlStatement("SELECT * FROM tbl_form_physical_exam WHERE forms_id= $copy_to_id");
        while($pe_row1 = sqlFetchArray($pe_sql1)){
            $pe_result1[]   = $pe_row1;
            $line_id[]      = $pe_row1['line_id'];
        } 
    

        if(!empty($pe_result1)){
            $delete = sqlStatement("delete  from tbl_form_physical_exam where forms_id=$copy_to_id ");
            foreach($pe_result as $val1){
              sqlInsert("INSERT INTO tbl_form_physical_exam ( " .
                  "forms_id, line_id, wnl, abn, diagnosis, comments " .
                  ") VALUES (?,?,?,?,?,?)",array($copy_to_id,$val1['line_id'],$val1['wnl'],$val1['abn'],$val1['diagnosis'],$val1['comments']));
            }               
        } else {
            foreach($pe_result as $val1){
               sqlInsert("INSERT INTO tbl_form_physical_exam ( " .
                   "forms_id, line_id, wnl, abn, diagnosis, comments " .
                   ") VALUES (?,?,?,?,?,?)",array($copy_to_id,$val1['line_id'],$val1['wnl'],$val1['abn'],$val1['diagnosis'],$val1['comments']));
            }    
        }
        
        //this is for pe finalized and pending details
        $form_flag  = sqlStatement("SELECT * 
                       FROM  `tbl_allcare_formflag` 
                       WHERE form_id =$copy_from_id AND form_name='Allcare Physical Exam' order by id desc limit 0,1");
        while($row1 = sqlFetchArray($form_flag)){
            $finalized  = $row1['finalized'];
            $pending    = $row1['pending'];
        } 
        $logdata= array();
        $data = mysql_query("SELECT logdate from `tbl_allcare_formflag` WHERE  form_id=".$copy_to_id);
        while ($row = mysql_fetch_array($data,MYSQL_ASSOC)) {
            $array  =  unserialize($row['logdate']);
            $count  = count($array);
        }
        $res    = sqlStatement("SELECT * FROM `tbl_allcare_formflag` WHERE form_id = '$copy_to_id'");
        $row1   = sqlFetchArray($res);
        $count  = isset($count)? $count: 0;

        
        if(empty($row1['form_id'])){
            $array2[]   = array( 'authuser' =>$provider,'pending' => $pending,'finalized' => $finalized, 'date' => date("Y/m/d"), 'action'=>'Copied' ,'ip_address'=>$ip_addr,'count'=> $count+1);
            $logdata    = array_merge_recursive($array, $array2);
            $logdata    = ($logdata? serialize($logdata): serialize($array2) );
            $query1     = "INSERT INTO tbl_allcare_formflag ( " .
                            "encounter_id,form_id, form_name,pending,finalized, logdate" .
                            ") VALUES ( " .
                            "".$copy_to_encounter.",'".$copy_to_id."', 'Allcare Physical Exam','$pending', '$finalized', '".$logdata."' " .
                            ")";
            sqlInsert($query1);
        } 
        else {
            $array2[] = array( 'authuser' =>$provider,'pending' => $pending,'finalized' => $finalized, 'date' => date("Y/m/d"), 'action' => 'Updated/Copied','ip_address'=>$ip_addr ,'count'=> $count+1);
            $logdata = array_merge_recursive($array, $array2);
            $logdata= ($logdata? serialize($logdata): serialize($array2) );
            $query2 = "UPDATE tbl_allcare_formflag SET pending = '$pending', finalized = '$finalized',logdate =  '".$logdata."' WHERE  form_id = ".$copy_to_id ." AND encounter_id = '$copy_to_encounter' AND form_name= 'Allcare Physical Exam' ";
            sqlStatement($query2);
        }

        $ins_log    = "insert into tbl_allcare_template (date,copy_from_enc,copy_from_id,copy_to_enc,copy_to_id,form_name,user) values (NOW(),$copy_from_encounter,$copy_from_id,$copy_to_encounter,$copy_to_id,'$form_name','$provider')";
        sqlInsert($ins_log);
    }
    echo $form_name."-".$copy_to_id;
} 
else if($form_name    == 'Allcare Review Of Systems'){
    
    $enc_id2             = sqlStatement("select encounter from forms where form_id=$copy_from_id AND deleted=0 AND formdir='allcare_ros'");
    $res_enc2            = sqlFetchArray($enc_id2);
    $copy_from_encounter = $res_enc2['encounter'];
    
    $enc_id1    = sqlStatement("select form_id from forms where encounter=$copy_to_encounter AND deleted=0 AND formdir='allcare_ros'");
    $res_enc1   = sqlFetchArray($enc_id1);
    if($res_enc1['form_id'] != ''){
        $copy_to_id = $res_enc1['form_id'];
    } else {
        $sql_pid    = sqlStatement("select * from forms where encounter=$copy_to_encounter");  
        $row_pid    = sqlFetchArray($sql_pid); 
        $pid       = $row_pid['pid'];
         
        $sql_form   = sqlStatement("SELECT id as new_form FROM  `sequences`");
        $row_form   = sqlFetchArray($sql_form);
        $new_fid    = $row_form['new_form'];
        $new_ros    = ++$new_fid;
        $seq_update = sqlStatement("update sequences set id=$new_ros");
        $ins_form   = sqlStatement("INSERT INTO  forms (date,encounter,form_name,form_id,pid,user,groupname,authorized,deleted,formdir) VALUES (NOW(),$copy_to_encounter,'Allcare Review Of Systems',$new_ros,$pid,'$provider','default',1,0,'allcare_ros')");
        $row1_form  = sqlFetchArray($ins_form);
        $copy_to_id = $new_ros;
    }

     //copy from   form
    $pe_sql = sqlStatement("SELECT * FROM tbl_form_allcare_ros WHERE id= $copy_from_id");
    while($pe_row   = sqlFetchArray($pe_sql)){
        $pe_result[]=$pe_row;
    }

    //copy to form
    $pe_sql1    = sqlStatement("SELECT * FROM tbl_form_allcare_ros WHERE id= $copy_to_id");
    while($pe_row1 = sqlFetchArray($pe_sql1)){
        $pe_result1[] = $pe_row1;
    } 
        
    if(!empty($pe_result1)){
        $delete = sqlStatement("delete  from tbl_form_allcare_ros where id=$copy_to_id ");

        foreach($pe_result as $key => $value){
            foreach($value as $key1 => $value1){
                $count= count($key1);
                if($key1    != 'id'){    
                    $fields        .= "`".$key1."`,";
                    $fields1        = rtrim($fields,",");
                    $field_value   .= '"'.addslashes($value1).'",';
                    $field_value1   = rtrim($field_value,",");
                    ${$key1}        = $value1;
                }
            }
        }
       $ins_ros = 'insert into tbl_form_allcare_ros (id,'.$fields1.') values ('.$copy_to_id.','.$field_value1.')';
       sqlInsert($ins_ros);

       $ins_log = "insert into tbl_allcare_template (date,copy_from_enc,copy_from_id,copy_to_enc,copy_to_id,form_name,user) values (NOW(),$copy_from_encounter,$copy_from_id,$copy_to_encounter,$copy_to_id,'$form_name','$provider]')";
       sqlInsert($ins_log);
    } else {
        foreach($pe_result as $key => $value){
            foreach($value as $key1 => $value1){
                $count = count($key1);
                if($key1 != 'id'){    
                    $fields        .= "`".$key1."`,";
                    $fields1        = rtrim($fields,",");
                    $field_value   .= '"'.addslashes($value1).'",';
                    $field_value1   = rtrim($field_value,",");
                    ${$key1}        = $value1;
                 }
            }
        }
        $ins_ros = 'insert into tbl_form_allcare_ros (id,'.$fields1.') values ('.$copy_to_id.','.$field_value1.')';
        sqlInsert($ins_ros);

        $ins_log = "insert into tbl_allcare_template (date,copy_from_enc,copy_from_id,copy_to_enc,copy_to_id,form_name,user) values (NOW(),$copy_from_encounter,$copy_from_id,$copy_to_encounter,$copy_to_id,'$form_name','$provider')";
        sqlInsert($ins_log);
    } 
    $data = mysql_query("SELECT logdate from `tbl_allcare_formflag` WHERE form_id=".$copy_to_id);
    while ($row = mysql_fetch_array($data,MYSQL_ASSOC)) {
        $array =  unserialize($row['logdate']);
        $count= count($array);
    }
    $count = isset($count)? $count: 0;
    
    if(empty($pe_result1)):
        $array2[] = array( 'authuser' =>$provider,'pending' => $pending,'finalized' => $finalized, 'date' => date("Y/m/d"), 'action'=>'created','ip_address'=>$ip_addr ,'count'=> $count+1);
        $logdata = array_merge_recursive($array, $array2);
        $logdata= ($logdata? serialize($logdata): serialize($array2) );
        sqlInsert("INSERT into `tbl_allcare_formflag`(`form_id`, `encounter_id`, `form_name`, `pending`,`finalized`,`logdate`) VALUES(".$copy_to_id.",".$encounter.",'Allcare Review of Systems','".$pending."','".$finalized."','".$logdata."')");
    else: 
        $result = mysql_query("SELECT * FROM tbl_allcare_formflag WHERE `form_name` = 'Allcare Review of Systems' AND `form_id` =  ".$copy_to_id);
        if(mysql_num_rows($result) > 0){
            $array2[] = array( 'authuser' =>$provider,'pending' => $pending,'finalized' => $finalized, 'date' => date("Y/m/d"), 'action' => 'updated','ip_address'=>$ip_addr ,'count'=> $count+1);
            $logdata = array_merge_recursive($array, $array2);
            $logdata= ($logdata? serialize($logdata): serialize($array2) );
            sqlInsert("UPDATE `tbl_allcare_formflag` SET `finalized`='$finalized',
            `pending` = '$pending',`logdate` ='".$logdata."'  WHERE `form_name` = 'Allcare Review of Systems' AND `form_id` =  ".$copy_to_id);
        }else{ 
            $array2[] = array( 'authuser' =>$provider,'pending' => $pending,'finalized' => $finalized, 'date' => date("Y/m/d"), 'action'=>'created','ip_address'=>$ip_addr ,'count'=> $count+1);
            $logdata = array_merge_recursive($array, $array2);
            $logdata= ($logdata? serialize($logdata): serialize($array2) );
            sqlInsert("INSERT into `tbl_allcare_formflag`(`form_id`, `encounter_id`, `form_name`, `pending`,`finalized`,`logdate`) VALUES(".$copy_to_id.",".$encounter.",'Allcare Review of Systems','".$pending."','".$finalized."','".$logdata."')");
        }
    endif;
    echo $form_name."-".$copy_to_id;
}
else if($form_name == 'CPO'){
     
     //copy_from_encounter
     $enc   = sqlStatement("select encounter,pid from forms where form_id=$copy_from_id AND deleted=0 AND formdir='cpo'");
     $res1  = sqlFetchArray($enc);
     
     $copy_from_encounter   = $res1['encounter'];
     
     $enc_id1   = sqlStatement("select form_id,pid from forms where encounter=$copy_to_encounter AND deleted=0 AND formdir='cpo'");
     $res_enc1  = sqlFetchArray($enc_id1);
     if($res_enc1['form_id']    != ''){
        $copy_to_id =$res_enc1['form_id'];
        $pid       = $res_enc1['pid'];
     } else {
         $sql_pid   = sqlStatement("select * from forms where encounter=$copy_to_encounter");  
         $row_pid   = sqlFetchArray($sql_pid); 
         $pid      = $row_pid['pid'];
         
        $sql_form   = sqlStatement("select max(form_id)as new_form from forms where form_name='CPO' AND formdir='cpo'");
        $row_form   = sqlFetchArray($sql_form);
        $new_fid    = $row_form['new_form'];
        $new_cpo    = ++$new_fid;
        
        $ins_form   = sqlStatement("INSERT INTO  forms (date,encounter,form_name,form_id,pid,user,groupname,authorized,deleted,formdir) VALUES (NOW(),$copy_to_encounter,'CPO',$new_cpo,$pid,'$provider','default',1,0,'cpo')");
        $row1_form  = sqlFetchArray($ins_form);
        $copy_to_id = $new_cpo;
    }
     
     
     //copy from   form
    $cpo_sql = sqlStatement("select *  from tbl_form_cpo  where pid=$pid AND id=$copy_from_id");

    while($cpo_row = sqlFetchArray($cpo_sql)){
      $cpo_result[] = $cpo_row;
    }
  
    //copy to form
    $cpo_sql1   = sqlStatement("select *  from tbl_form_cpo  where pid=$pid AND id=$copy_to_id");
    while($cpo_row1 = sqlFetchArray($cpo_sql1)){
        $cpo_result1[]  = $cpo_row1;
    } 
    $cpo_fields1 =  $cpo_field_value1 = '';
    if(!empty($cpo_result1)){
        $delete = sqlStatement("delete  from tbl_form_cpo where id=$copy_to_id AND pid=$pid");
        foreach($cpo_result as $key=> $value){
            foreach($value as $key1 => $value1){
                if($key1 != 'id'){    
                    $cpo_fields        .= "`".$key1."`,";
                    $cpo_field_value   .= "'".addslashes($value1)."',";
                    
                }
                 
            }
        }
    $cpo_fields1        = rtrim($cpo_fields,",");
    $cpo_field_value1   = rtrim($cpo_field_value,",");   
    $ins_cpo    = "insert into tbl_form_cpo (id,$cpo_fields1) values ($copy_to_id,$cpo_field_value1)";
    sqlInsert($ins_cpo);

    $ins_log = "insert into tbl_allcare_template (date,copy_from_enc,copy_from_id,copy_to_enc,copy_to_id,form_name,user) values (NOW(),$copy_from_encounter,$copy_from_id,$copy_to_encounter,$copy_to_id,'$form_name','$provider')";
    sqlInsert($ins_log);
    }else {
        foreach($cpo_result as $key=> $value){
           foreach($value as $key1 => $value1){
               if($key1 != 'id'){    
                   $cpo_fields         .= "`".$key1."`,";
                   $cpo_field_value    .= "'".addslashes($value1)."',";
                }
           }
        }
        $cpo_fields1         = rtrim($cpo_fields,",");
        $cpo_field_value1    = rtrim($cpo_field_value,",");
        
        $ins_cpo    = "insert into tbl_form_cpo (id,$cpo_fields1) values ($copy_to_id,$cpo_field_value1)";
        sqlInsert($ins_cpo);

        $ins_log    = "insert into tbl_allcare_template (date,copy_from_enc,copy_from_id,copy_to_enc,copy_to_id,form_name,user) values (NOW(),$copy_from_encounter,$copy_from_id,$copy_to_encounter,$copy_to_id,'$form_name','$provider')";
        sqlInsert($ins_log);
    }
    echo $form_name."-".$copy_to_id;
}
function GetIP()
{
    if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown"))
        $ip = getenv("HTTP_CLIENT_IP");
    else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown"))
        $ip = getenv("HTTP_X_FORWARDED_FOR");
    else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown"))
        $ip = getenv("REMOTE_ADDR");
    else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown"))
        $ip = $_SERVER['REMOTE_ADDR'];
    else
        $ip = "unknown";
    return($ip);
}
?>
