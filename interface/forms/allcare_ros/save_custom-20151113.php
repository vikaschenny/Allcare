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



save_form_flag();
//@formJump();
if($_POST['provider']!=''){
    $provider=$_POST['provider'];

        echo "<script>window.close();

    window.opener.location.href = '../../reports/incomplete_charts.php';</script>";
    

}else {
     $provider=$_POST['provider1'];

     echo "<script>window.close();

    window.opener.location.href = '../../reports/incomplete_charts.php';</script>";


}


function save_form_flag(){
    
    if(!empty($_REQUEST['id']) ){
      $formid = $_REQUEST['id']; 
    }else{
        $result = mysql_query("SELECT id FROM tbl_form_allcare_ros ORDER BY id DESC LIMIT 1");
        while ($row = mysql_fetch_array($result,MYSQL_ASSOC)){
            $formid =  $row['id']; 
        } 
    }
    $logdata= array();
    $data = mysql_query("SELECT logdate from `tbl_allcare_formflag` WHERE  form_id=".$formid);
    while ($row = mysql_fetch_array($data,MYSQL_ASSOC)) {
        $array =  unserialize($row['logdate']);
        $count= count($array);
    }
    $count = isset($count)? $count: 0;
    $pending = $_POST['pending'];
    $finalized = $_POST['finalized'];
    
    $array=array();
    $ip_addr=GetIP();
    if(empty($_REQUEST['id'])):
            $array2[] = array( 'authuser' =>$_SESSION["authUser"],'pending' => $pending,'finalized' => $finalized, 'date' => date("Y/m/d"), 'action'=>'created','ip_address'=>$ip_addr ,'count'=> $count+1);
            $logdata = array_merge_recursive($array, $array2);
            $logdata= ($logdata? serialize($logdata): serialize($array2) );
            sqlInsert("INSERT into `tbl_allcare_formflag`(`form_id`, `encounter_id`, `form_name`, `pending`,`finalized`,`logdate`) VALUES(".$formid.",".$GLOBALS['encounter'].",'Allcare Review of Systems','".$pending."','".$finalized."','".$logdata."')");
    else: 
            $result = mysql_query("SELECT * FROM tbl_allcare_formflag WHERE `form_name` = 'Allcare Review of Systems' AND `form_id` =  ".$formid);
            if(mysql_num_rows($result) > 0){
                    $array2[] = array( 'authuser' =>$_SESSION["authUser"],'pending' => $pending,'finalized' => $finalized, 'date' => date("Y/m/d"), 'action' => 'updated','ip_address'=>$ip_addr ,'count'=> $count+1);
                    $logdata = array_merge_recursive($array, $array2);
                    $logdata= ($logdata? serialize($logdata): serialize($array2) );
                    sqlInsert("UPDATE `tbl_allcare_formflag` SET `finalized`='$finalized',
                    `pending` = '$pending',`logdate` ='".$logdata."'  WHERE `form_name` = 'Allcare Review of Systems' AND `form_id` =  ".$formid);
            }else{ 
                    $array2[] = array( 'authuser' =>$_SESSION["authUser"],'pending' => $pending,'finalized' => $finalized, 'date' => date("Y/m/d"), 'action'=>'created','ip_address'=>$ip_addr ,'count'=> $count+1);
                    $logdata = array_merge_recursive($array, $array2);
                    $logdata= ($logdata? serialize($logdata): serialize($array2) );
                    sqlInsert("INSERT into `tbl_allcare_formflag`(`form_id`, `encounter_id`, `form_name`, `pending`,`finalized`,`logdate`) VALUES(".$formid.",".$GLOBALS['encounter'].",'Allcare Review of Systems','".$pending."','".$finalized."','".$logdata."')");
            }
    endif;
    
     //save ros status in lbf_data
        
        if($finalized=='YES' && $pending=='YES'){
            $finalized1='finalized';
            $pending1='pending';
            $lb_value=$finalized1.'|'. $pending1;

        }else if($finalized=='YES'){
           $finalized1='finalized';
            $lb_value=$finalized1;

        }else if($pending=='YES'){
             $pending1='pending';
            $lb_value=$pending1;


        }
        if($_POST['encounter']!=''){
            $encounter=$_POST['encounter'];
        }else if($_POST['encounter1']!=''){
            $encounter=$_POST['encounter1'];
        }
        if($_POST['pid']!=''){
            $pid=$_POST['pid'];
        }else if($_POST['pid2']!=''){
             $pid=$_POST['pid2'];
        }
        echo "select form_id  from forms where form_name ='Allcare Encounter Forms' AND encounter='".$encounter."' AND pid='".$pid."' AND deleted=0 order by id desc";
    $res12=sqlstatement("select form_id  from forms where form_name ='Allcare Encounter Forms' AND encounter='".$encounter."' AND pid='".$pid."' AND deleted=0 order by id desc");
         $frow_res = sqlFetchArray($res12);
         if(!empty($frow_res)){
             $formid_lb=$frow_res['form_id'];
             $res1=sqlstatement("select * from lbf_data lb INNER JOIN layout_options l ON l.field_id=lb.field_id where lb.form_id='$formid_lb' AND l.form_id='LBF2' AND l.group_name LIKE '%Review Of Systems%' AND lb.field_id LIKE '%_stat%' order by seq");
             $res_row1=sqlFetchArray($res1);
             if(!empty($res_row1)){
                 if($lb_value!='')
                 $update=sqlStatement("UPDATE lbf_data SET  `field_value` = ? WHERE  field_id= ? AND form_id = ?",array($lb_value,'ros_stat',$formid_lb));
             }else{
                  if($lb_value!='')
                   sqlInsert("INSERT INTO lbf_data (form_id,field_id,field_value) VALUES (?,?,?)", array($formid_lb,'ros_stat',$lb_value));
             }
         }else{
             echo $lb_value;
             if($lb_value!=''){
                echo $lb_value; 
                $sql_form=sqlStatement("select max(form_id)as new_form from forms where form_name='Allcare Encounter Forms' AND formdir='LBF2'");
                $row_form=sqlFetchArray($sql_form);
                $new_fid= $row_form['new_form'];
                $new_id1=++$new_fid;
                //echo "INSERT INTO  forms (date,encounter,form_name,form_id,pid,user,groupname,authorized,deleted,formdir) VALUES (NOW(),$ecounter1,'Allcare Encounter Forms',$new_id1,$pid1,'$_SESSION[authUser]','default',1,0,'LBF2')";
                $ins_form=sqlStatement("INSERT INTO  forms (date,encounter,form_name,form_id,pid,user,groupname,authorized,deleted,formdir) VALUES (NOW(),'".$encounter."','Allcare Encounter Forms',$new_id1,'".$pid."','".$_SESSION["authUser"]."','default',1,0,'LBF2')");
                $row1_form=sqlFetchArray($ins_form);
                $res1=sqlstatement("select * from lbf_data lb INNER JOIN layout_options l ON l.field_id=lb.field_id where lb.form_id='$new_id1' AND l.form_id='LBF2' AND l.group_name LIKE '%Review Of Systems%' AND lb.field_id LIKE '%_stat%' order by seq");
                $res_row1=sqlFetchArray($res1);
                if(!empty($res_row1)){
                     echo $lb_value."update"; exit();
                      $update=sqlStatement("UPDATE lbf_data SET  `field_value` = ? WHERE  field_id= ? AND form_id = ?",array($lb_value,'ros_stat',$new_id1));
                }else{
                      echo $lb_value."insert"; 
                      sqlInsert("INSERT INTO lbf_data (form_id,field_id,field_value) VALUES (?,?,?)", array($new_id1,'ros_stat',$lb_value));
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
