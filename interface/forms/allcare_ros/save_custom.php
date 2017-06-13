<?php

/*
echo "<br>post 55 ";
        print_r($_POST);
        echo "<br>post 77 ";die;*/
include_once("../../globals.php");
include_once("$srcdir/api.inc");

require ("allcare_C_FormROS_custom.class.php");

$c = new allcare_C_FormROS1();
echo $c->default_action_process1($_POST); 
//print_r($_POST);

//echo "<pre>"; print_r($_REQUEST); echo "</pre>";exit();exit();
$encounter      = !empty($_REQUEST['encounter1'])   ? $_REQUEST['encounter1']   : $_REQUEST['encounter'];
$pid            = !empty($_REQUEST['pid2'])         ? $_REQUEST['pid2']         : $_REQUEST['pid2'];
$newformid      = 0;
$isSingleView   = $_REQUEST['isSingleView'];
$isFromCharts   = $_REQUEST['isFromCharts'];

$result     = mysql_query("SELECT form_id FROM forms WHERE `form_name` = 'Allcare Review of Systems' AND `pid` =  ".$pid ." AND encounter = $encounter AND deleted = 0");
if(mysql_num_rows($result) > 0){
    $result1 = sqlFetchArray($result);
    $newformid = $result1['form_id'];
}
$res12  = sqlstatement("select form_id  from forms where form_name ='Allcare Encounter Forms' AND encounter=$encounter AND pid=$pid AND deleted=0 order by id desc");
$frow_res = sqlFetchArray($res12);
if($frow_res['form_id']){
    $lbf_form_id = $frow_res['form_id'];
    $new = 0;
}else{ 
    $sql_form = sqlStatement("select max(form_id)as new_form from forms where form_name='Allcare Encounter Forms' AND formdir='LBF2'");
    $row_form = sqlFetchArray($sql_form);
    $new_fid  = $row_form['new_form'];
    $lbf_form_id = $new_fid + 1;
    $new = 1;
}


save_form_flag($newformid,$encounter,$lbf_form_id,$new);


//@formJump();
if($_POST['provider']!=''){
    if($isSingleView == 1 && $isFromCharts == 0){
        echo "<script>window.close();
            window.opener.location.href = '../../reports/single_view_form.php?encounter=$encounter&pid=$pid';</script>";
    }else if($isSingleView == 1 && $isFromCharts == 1){
        echo "<script>window.opener.datafromchildwindow($newformid,$lbf_form_id);window.close();</script>";
            #window.opener.location.href = '../../reports/patient_full_encounters_single_view.php?encounter=$encounter&pid=$pid';";
    }else{
        $provider=$_POST['provider'];
        echo "<script>window.close();
            window.opener.location.href = '../../reports/incomplete_charts.php?encounter=$encounter';</script>";
    }
}else {
    if($isSingleView == 1 && $isFromCharts == 0){
        echo "<script>window.close();
            window.opener.location.href = '../../reports/single_view_form.php?encounter=$encounter&pid=$pid';</script>";
    }else if($isSingleView == 1 && $isFromCharts == 1){
        echo "<script>window.opener.datafromchildwindow($newformid,$lbf_form_id);window.close();</script>";
            #window.opener.location.href = '../../reports/patient_full_encounters_single_view.php?encounter=$encounter&pid=$pid';</script>";
    }else{
        $provider=$_POST['provider1'];
        echo "<script>window.close();
            window.opener.location.href = '../../reports/incomplete_charts.php?encounter=$encounter';</script>";
    }
}


function save_form_flag($formid,$encounter,$lbf_form_id,$new){
    
    if(empty($formid) ){
      $formid = $_REQUEST['id']; 
    }
    $logdata    = array();
    $array      = array();
    $data = mysql_query("SELECT logdate from `tbl_allcare_formflag` WHERE  form_id=".$formid);
    while ($row = mysql_fetch_array($data,MYSQL_ASSOC)) {
        $array =  unserialize($row['logdate']);
        $count= count($array);
    }
    $count      = isset($count)? $count: 0;
    $pending    = $_POST['pending'];
    $finalized  = $_POST['finalized'];
    
    
    $ip_addr    = GetIP();
    $ip_addr    = $ip_addr ."(incomplete encounters)";
    if(empty($formid)):
        $array2[]   = array( 'authuser' =>$_SESSION["authUser"],'pending' => $pending,'finalized' => $finalized, 'date' => date("Y/m/d"), 'action'=>'created','ip_address'=>$ip_addr ,'count'=> $count+1);
        $logdata    = array_merge_recursive($array, $array2);
        $logdata    = ($logdata? serialize($logdata): serialize($array2) );
        sqlInsert("INSERT into `tbl_allcare_formflag`(`form_id`, `encounter_id`, `form_name`, `pending`,`finalized`,`logdate`) VALUES(".$formid.",".$encounter.",'Allcare Review of Systems','".$pending."','".$finalized."','".$logdata."')");
    else: 
        $result     = mysql_query("SELECT * FROM tbl_allcare_formflag WHERE `form_name` = 'Allcare Review of Systems' AND `form_id` =  ".$formid);
        if(mysql_num_rows($result) > 0){
                $array2[]   = array( 'authuser' =>$_SESSION["authUser"],'pending' => $pending,'finalized' => $finalized, 'date' => date("Y/m/d"), 'action' => 'updated','ip_address'=>$ip_addr ,'count'=> $count+1);
                $logdata    = array_merge_recursive($array, $array2);
                $logdata    = ($logdata? serialize($logdata): serialize($array2) );
                sqlInsert("UPDATE `tbl_allcare_formflag` SET `finalized`='$finalized',
                `pending` = '$pending',`logdate` ='".$logdata."'  WHERE `form_name` = 'Allcare Review of Systems' AND `form_id` =  ".$formid);
        }else{ 
                $array2[]   = array( 'authuser' =>$_SESSION["authUser"],'pending' => $pending,'finalized' => $finalized, 'date' => date("Y/m/d"), 'action'=>'created','ip_address'=>$ip_addr ,'count'=> $count+1);
                $logdata    = array_merge_recursive($array, $array2);
                $logdata    = ($logdata? serialize($logdata): serialize($array2) );
                sqlInsert("INSERT into `tbl_allcare_formflag`(`form_id`, `encounter_id`, `form_name`, `pending`,`finalized`,`logdate`) VALUES(".$formid.",".$encounter.",'Allcare Review of Systems','".$pending."','".$finalized."','".$logdata."')");
        }
    endif;
    
     //save ros status in lbf_data
        
        if($finalized=='YES' && $pending=='YES'){
            $finalized1 = 'finalized';
            $pending1   = 'pending';
            $lb_value   = $finalized1.'|'. $pending1;

        }else if($finalized == 'YES'){
           $finalized1  = 'finalized';
            $lb_value   = $finalized1;

        }else if($pending == 'YES'){
             $pending1  = 'pending';
            $lb_value   = $pending1;


        }
        if($_POST['encounter']!=''){
            $encounter = $_POST['encounter'];
        }else if($_POST['encounter1']!=''){
            $encounter = $_POST['encounter1'];
        }
        if($_POST['pid']!=''){
            $pid = $_POST['pid'];
        }else if($_POST['pid2']!=''){
             $pid = $_POST['pid2'];
        }
        // echo "select form_id  from forms where form_name ='Allcare Encounter Forms' AND encounter='".$encounter."' AND pid='".$pid."' AND deleted=0 order by id desc";
//        $res12  = sqlstatement("select form_id  from forms where form_name ='Allcare Encounter Forms' AND encounter='".$encounter."' AND pid='".$pid."' AND deleted=0 order by id desc");
//        $frow_res = sqlFetchArray($res12);
        if($new == 0){
            if($lb_value != ''){
                $formid_lb  = $lbf_form_id;
                $res1   = sqlstatement("select * from lbf_data lb INNER JOIN layout_options l ON l.field_id=lb.field_id where lb.form_id='$formid_lb' AND l.form_id='LBF2' AND l.group_name LIKE '%Review Of Systems%' AND lb.field_id LIKE '%_stat%' order by seq");
                $res_row1 = sqlFetchArray($res1);
                if(!empty($res_row1)){
                    $update = sqlStatement("UPDATE lbf_data SET  `field_value` = ? WHERE  field_id= ? AND form_id = ?",array($lb_value,'ros_stat',$formid_lb));
                }else{
                    sqlInsert("INSERT INTO lbf_data (form_id,field_id,field_value) VALUES (?,?,?)", array($formid_lb,'ros_stat',$lb_value));
                }
            }
        }else{
            ///echo $lb_value;
            if($lb_value!=''){
               // echo $lb_value; 
//                $sql_form=sqlStatement("select max(form_id)as new_form from forms where form_name='Allcare Encounter Forms' AND formdir='LBF2'");
//                $row_form=sqlFetchArray($sql_form);
//                $new_fid= $row_form['new_form'];
//                $new_id1=++$new_fid;
//                echo "INSERT INTO  forms (date,encounter,form_name,form_id,pid,user,groupname,authorized,deleted,formdir) VALUES (NOW(),$ecounter1,'Allcare Encounter Forms',$lbf_form_id,$pid1,'$_SESSION[authUser]','default',1,0,'LBF2')";exit();
                $ins_form   = sqlStatement("INSERT INTO  forms (date,encounter,form_name,form_id,pid,user,groupname,authorized,deleted,formdir) VALUES (NOW(),'".$encounter."','Allcare Encounter Forms',$lbf_form_id,'".$pid."','".$_SESSION["authUser"]."','default',1,0,'LBF2')");
                $row1_form  = sqlFetchArray($ins_form);
                $res1       = sqlstatement("select * from lbf_data lb INNER JOIN layout_options l ON l.field_id=lb.field_id where lb.form_id='$lbf_form_id' AND l.form_id='LBF2' AND l.group_name LIKE '%Review Of Systems%' AND lb.field_id LIKE '%_stat%' order by seq");
                $res_row1   = sqlFetchArray($res1);
                if(!empty($res_row1)){
                    $update = sqlStatement("UPDATE lbf_data SET  `field_value` = ? WHERE  field_id= ? AND form_id = ?",array($lb_value,'ros_stat',$lbf_form_id));
                }else{
                    sqlInsert("INSERT INTO lbf_data (form_id,field_id,field_value) VALUES (?,?,?)", array($lbf_form_id,'ros_stat',$lb_value));
               }
            }

         }        
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
