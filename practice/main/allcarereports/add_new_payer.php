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


$zirmed_ids = $_REQUEST['payerlist'];
$zirmed_id = explode(",",$zirmed_ids);
for($i=0; $i< count($zirmed_id); $i++){
    if($zirmed_id[$i] != ''){
        $get_postmeta = sqlStatement("SELECT * FROM wp_postmeta WHERE post_id = '$zirmed_id[$i]'");
        while($set_postmeta = sqlFetchArray($get_postmeta)){
            ${$set_postmeta['meta_key']} = $set_postmeta['meta_value'];
        }

        $get_insurance_columns = sqlStatement("SHOW COLUMNS FROM insurance_companies ");
        while($set_insurance_columns = sqlFetchArray($get_insurance_columns)){
        //    $emr_insurance_meta[] = $set_insurance_columns['Field'];
            ${$set_insurance_columns['Field']} = '';

        }
        $address_data = sqlStatement("SHOW COLUMNS FROM addresses");
        while($set_address_data = sqlFetchArray($address_data)){
        //    $emr_insurance_meta[] = $set_address_data['Field'];
            ${$set_address_data['Field']} = '';
        }

        $get_mapping_data = sqlStatement("SELECT * FROM tbl_insurance_payerplan_meta_mapping");
        while($set_mapping_data = sqlFetchArray($get_mapping_data)){
            ${$set_mapping_data['emr_meta_key']} = isset(${$set_mapping_data['payerplan_meta_key']}) ? ${$set_mapping_data['payerplan_meta_key']} : '';
        }
        $get_new_ids = sqlStatement("SELECT MAX(id) as id FROM insurance_companies");
        while($set_new_ids = sqlFetchArray($get_new_ids)){
            $new_id = $set_new_ids['id'];
        }

        if($new_id){
            $new_id = $new_id + 1;;

            $name = '';
            $get_name = sqlStatement("SELECT post_title FROM wp_posts WHERE ID = $zirmed_id[$i]");
            while($set_name = sqlFetchArray($get_name)){
                $name = $set_name['post_title'];
            }
            $insert_insu_company = sqlStatement("INSERT INTO insurance_companies (id,name,attn,cms_id,freeb_type,x12_receiver_id,x12_default_partner_id,alt_cms_id,payer_id)
                VALUES('$new_id','".$name."','".$attn."','".$cms_id."','".$freeb_type."','".$x12_receiver_id."','".$x12_default_partner_id."','".$alt_cms_id."','".$payer_id."')");

            $get_new_ids = sqlStatement("SELECT MAX(id) as id FROM addresses");
            while($set_new_ids = sqlFetchArray($get_new_ids)){
                $new_line_id = $set_new_ids['id'] + 1;
            }
            $insert_address = sqlStatement("INSERT INTO addresses (id, line1,line2,city,state,zip,plus_four,country,foreign_id)
            VALUES ('$new_line_id','".$line1."','".$line2."','".$city."','".$state."','".$zip."','".$plus_four."','".$country."','".$new_id."') ")  ;      


        }
    }
}

?>