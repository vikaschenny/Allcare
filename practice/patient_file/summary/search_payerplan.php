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
$term = mysql_escape_string($term); // Attack Prevention
if($term=="")
    echo "";
else{
        $params='';
        $array_values = $array_values_result = array();
        $get_column = '';
        $i = 0;
        $get_post_columns = sqlStatement("SHOW COLUMNS FROM wp_posts ");
        while($set_post_columns = sqlFetchArray($get_post_columns)){
            if($set_post_columns['Field'] == 'post_title')
            $get_column .= " `".$set_post_columns['Field'] ."` LIKE '%$term%' OR";
        }
        
        $get_postmeta_columns = sqlStatement("SHOW COLUMNS FROM wp_postmeta ");
        while($set_postmeta_columns = sqlFetchArray($get_postmeta_columns)){
            if($set_postmeta_columns['Field'] == 'meta_value')
            $get_column .= " `".$set_postmeta_columns['Field'] ."` LIKE '%$term%' OR";
        }
        $get_column_names = rtrim($get_column, 'OR');
        $zirmed_mapped_ids = '';
        // get field value from table 
        $get_practice_id = sqlStatement("SELECT DISTINCT p.ID, payerplan_payer_name, emr_payer_name, emr_payer_id, payerplan_payer_id
                                FROM tbl_payerplan_emrpayerplan_mapping tp
                                INNER JOIN wp_posts p ON tp.payerplan_payer_name = post_title AND post_type =  'payer'
                                INNER JOIN wp_postmeta pm ON pm.post_id = p.ID
                                WHERE (
                                `post_title` LIKE  '%$term%'
                                OR  `meta_value` LIKE  '%$term%'
                                OR emr_payer_name LIKE  '%$term%'
                                )
                                "); 
        /*$get_practice_id = sqlStatement("SELECT DISTINCT emr_payer_id, p.ID, payerplan_payer_name, payerplan_payer_id,emr_payer_name
                        FROM tbl_payerplan_emrpayerplan_mapping tp 
                            LEFT JOIN wp_posts p ON (SELECT DISTINCT post_id FROM wp_postmeta WHERE meta_value LIKE '%$term%' ) = p.ID  AND post_type = 'payer' 
                        WHERE  `post_title` LIKE '%$term%'    or emr_payer_name like '%$term%'");*/
        while($set_practice_id = sqlFetchArray($get_practice_id)){
             $array_values[$i]['label']             = $set_practice_id['emr_payer_name'];
             $array_values[$i]['value']             = $set_practice_id['emr_payer_id'];
             $array_values[$i]['payerID']           = $set_practice_id['ID'];
             $array_values[$i]['desc']              = $set_practice_id['payerplan_payer_name'];
             $array_values[$i]['zirmed_payer_id']   = $set_practice_id['payerplan_payer_id'];
             $zirmed_mapped_ids                    .= $set_practice_id['ID'];
             $i++;
        }
        // now get not mapped fields
        $get_non_mapped_emr = sqlStatement("SELECT emr_payer_name, emr_payer_id 
             FROM  tbl_payerplan_emrpayerplan_mapping 
            WHERE (emr_payer_name LIKE '%$term%' OR emr_payer_id LIKE '%$term%') AND payerplan_payer_name = '' AND payerplan_payer_id = '' 
            ");
        while($set_practice_id = sqlFetchArray($get_non_mapped_emr)){
             $array_values[$i]['label']             = $set_practice_id['emr_payer_name'];
             $array_values[$i]['value']             = $set_practice_id['emr_payer_id'];
             $array_values[$i]['payerID']           = '';
             $array_values[$i]['desc']              = '';
             $array_values[$i]['zirmed_payer_id']   = '';
             $i++;
        }
        if($zirmed_mapped_ids != '')
            $check = "AND ID NOT IN($zirmed_mapped_ids)";
        else
            $check = '';
        $get_non_mapped_emr = sqlStatement("SELECT DISTINCT ID, post_title ,(SELECT meta_value FROM wp_postmeta WHERE post_id = ID AND post_type='payer' AND meta_key='Claim Payer ID') as payerplan_payer_id
             FROM  wp_posts
             INNER JOIN wp_postmeta ON post_id = ID
            WHERE ( post_title  LIKE '%$term%' OR meta_value  LIKE '%$term%' ) $check  AND post_type='payer'
            ");
        while($set_practice_id = sqlFetchArray($get_non_mapped_emr)){
             $array_values[$i]['label']             = '';
             $array_values[$i]['value']             = '';
             $array_values[$i]['payerID']           = $set_practice_id['ID'];
             $array_values[$i]['desc']              = $set_practice_id['post_title'];
             $array_values[$i]['zirmed_payer_id']   = $set_practice_id['payerplan_payer_id'];
             $i++;
        }
        $array_values_result['returndata'] = $array_values;
        echo json_encode($array_values_result);
}

?>