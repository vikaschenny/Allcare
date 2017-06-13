<?php
//STOP FAKE REGISTER GLOBALS
$fake_register_globals=false;

//continue session
session_start();

//landing page definition -- where to go if something goes wrong
$landingpage = "../index.php?site=".$_SESSION['site_id'];	


if ( isset($_SESSION['portal_username']) ) {    
    $portal_user = $_SESSION['portal_username']; 
}else {
    session_destroy();
    header('Location: '.$landingpage.'&w');
    exit;
} 

$ignoreAuth=true; // ignore the standard authentication for a regular OpenEMR user
include_once('../../../interface/globals.php');
 
 $type=$_REQUEST['type'];
 $grp=$_REQUEST['group']; //for usergroup mapping only
 
 if($grp=='group'){
    if($type=='emr') { 
         if($_REQUEST['role']!=''){
             if($_REQUEST['action']!='edit'){
              $roles=implode("','",$_REQUEST['role']);
             }else {
               $roles=$_REQUEST['role'];
             }
             $sql12=sqlStatement("select id from gacl_aro_groups where name IN ('$roles')");
             while($urow12=sqlFetchArray($sql12)){
                 $arr[]=$urow12['id'];
             }
             $rolesid=implode("','",$arr);
         
             $sql13=sqlStatement("select * from gacl_groups_aro_map where group_id IN ('$rolesid')");
             while($urow123=sqlFetchArray($sql13)){
                $user=sqlStatement("select value from gacl_aro where id=".$urow123['aro_id']);
                $user12=sqlFetchArray($user);
                $arr1[]=$user12['value'];
             }
             $username=implode("','",$arr1);
             
          }
         
        $user=sqlStatement("select * from users where username!='' and (fname!='' or lname !='') and username IN ('$username')");
    }else if($type=='patients'){
        $user=sqlStatement("select p.pid as id ,portal_username as username, pd.* from patient_access_onsite p inner join patient_data pd ON p.pid=pd.pid where portal_username!=''");  
    }else if($type=='agencies'){
        $user=sqlStatement("select uid as id ,portal_username as username,u.* from tbl_allcare_agencyportal a inner join users u on u.id=a.uid where portal_username!=''");   
    }
    $arr_user=[];
    while($urow=sqlFetchArray($user)) {
        $arr=''; $arr=array(); $str='';
        $uname=$urow['username'];
        $name=trim($urow['lname'].", ".$urow['fname'],",");
        $arr_user[$uname]=$name;
    }
    echo json_encode($arr_user);
 }else{
    if($type=='emr') {    
        $user=sqlStatement("select * from users where username!='' and (fname!='' or lname !='') ");
    }else if($type=='patients'){
        $user=sqlStatement("select pid as id ,portal_username as username from patient_access_onsite where portal_username!=''");  
    }else if($type=='agencies'){
        $user=sqlStatement("select uid as id ,portal_username as username from tbl_allcare_agencyportal where portal_username!=''");   
    }
    $arr_user=[];
    while($urow=sqlFetchArray($user)) {
        $arr=''; $arr=array(); $str='';
        $id=$urow['id'];
        $sql=sqlStatement("select * from tbl_allcare_userfolder_links where user_id=$id");
        $arr_user[$id]=$urow['username'];
    }
    echo json_encode($arr_user);
 }
?> 