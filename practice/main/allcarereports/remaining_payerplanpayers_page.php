<?php
//SANITIZE ALL ESCAPES
$sanitize_all_escapes=true;
//

//STOP FAKE REGISTER GLOBALS
$fake_register_globals=false;
//

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


$list_ids = '';
$get_payers_list = sqlStatement("SELECT GROUP_CONCAT( DISTINCT payerplan_payer_id) as ids FROM tbl_payerplan_emrpayerplan_mapping ");
while($set_payers_list = sqlFetchArray($get_payers_list)){
    $list_ids              = $set_payers_list['ids'];
}
/*echo "SELECT p2.meta_value, (
                                    SELECT post_title
                                    FROM wp_posts
                                    WHERE ID = p2.post_id
                                ) as title 
                        FROM wp_postmeta p1
                        INNER JOIN wp_postmeta p2 ON p1.post_id = p2.post_id
                        WHERE p1.meta_key =  'practice_id'
                        AND p2.meta_key =  'Claim Payer ID'
                        AND NOT FIND_IN_SET( p2.meta_value,  '$list_ids' ) ";*/
$get_mapping_payer = sqlStatement("SELECT p2.meta_value, (
                                    SELECT post_title
                                    FROM wp_posts
                                    WHERE ID = p2.post_id AND post_type='payer' 
                                ) as title 
                        FROM wp_postmeta p1
                        INNER JOIN wp_posts ON ID = post_id 
                        INNER JOIN wp_postmeta p2 ON p1.post_id = p2.post_id
                        WHERE p1.meta_key =  'practice_id'
                        AND p2.meta_key =  'Claim Payer ID'  AND post_type='payer' 
                        AND NOT FIND_IN_SET( p2.meta_value,  '$list_ids' ) ");
while($set_mapping_payer = sqlFetchArray($get_mapping_payer)){
    $new_id              = $set_mapping_payer['meta_value'];
    $payerplan_payer_name     = $set_mapping_payer['title'];
    ?>
    <input type="checkbox"  onchange="changeAddPayer();" class='reminingpayers'  id="addpayer<?php echo $new_id; ?>" name="addpayer" value='<?php echo $new_id; ?>' ><?php echo $payerplan_payer_name; ?> <br>
    <?php 
}
?>
    <br>
    <input type='button' id='savenewpayer' name='savenewpayer' onclick="addNewPayers();" value='Add New Payer'>
    <br>
    <br>
    <br>