<?php
require_once("../../verify_session.php");

$pagename = "plist"; 
if(isset($_SESSION['portal_username']) !=''){
   $provider=$_SESSION['portal_username'];
}else {
   $provider=$_REQUEST['provider'];
   $refer=$_REQUEST['refer']; 
   $_SESSION['refer']=$_REQUEST['refer'];
   $_SESSION['portal_username']=$_REQUEST['provider'];
} 

$base_url="//".$_SERVER['SERVER_NAME'].dirname($_SERVER["REQUEST_URI"].'?').'/';

 $sql=sqlStatement("SELECT id, fname, lname, specialty FROM users " .
      "WHERE active = 1 AND ( info IS NULL OR info NOT LIKE '%Inactive%' ) " .
      "AND authorized = 1 AND username='".$provider."'" .
      "ORDER BY lname, fname");
$id=sqlFetchArray($sql);
$id1=$id['id'];

$term = strip_tags(substr($_POST['searchit'],0, 100));
$post_parent = trim($_REQUEST['post_parent']);
$term = mysql_escape_string($term); // Attack Prevention

$params='';
$array_values = $array_values_result = array();
$get_column = '';
$i = 0;
$get_practice_id = sqlStatement("SELECT DISTINCT p.ID, p.post_title
                    FROM wp_posts p 
                    INNER JOIN wp_postmeta pm ON pm.post_id = p.ID 
                    WHERE ( post_title LIKE '%$term%' OR meta_value LIKE '%$term%' ) AND post_type
                    = 'plan' and p.post_parent ='$post_parent'");
while($set_practice_id = sqlFetchArray($get_practice_id)){
     $array_values[$i]['label'] = $set_practice_id['post_title'];
     $array_values[$i]['value'] = $set_practice_id['ID'];

     $i++;
}
$array_values_result['returndata'] = $array_values;
echo json_encode($array_values_result);


?>