<?php
require_once("../../globals.php");
require_once("$srcdir/sql.inc");
require_once("$srcdir/formdata.inc.php");
require_once("$srcdir/options.inc.php");
require_once("$srcdir/acl.inc");

$type=$_REQUEST['user_type'];
$gmem=serialize($_REQUEST['group_mem']);
$gtype=$_REQUEST['group_type'];
$gname=$_REQUEST['gname'];
$roles=serialize(array_filter($_REQUEST['role']));
if($type != "" &&  !empty($gmem) && $gtype != "" && $gname != ""):
  
    $sqlUpdate=sqlStatement("update tbl_allcare_usergroup set date=now(),user_type='$type',group_type='$gtype',group_members='$gmem',login_user='".$_SESSION['authId']."',user_roles='$roles' where group_name='$gname'");
    echo "Group updated successfully";            
endif;
   
?> 