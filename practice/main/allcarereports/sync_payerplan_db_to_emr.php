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

require_once("$srcdir/globals.inc.php");
require_once("$srcdir/api.inc");
require_once("$srcdir/forms.inc");
require_once("$srcdir/options.inc.php");
require_once("$srcdir/patient.inc");
require_once("$srcdir/formdata.inc.php");
require_once("$srcdir/formatting.inc.php");

$list   = sqlStatement("SELECT * FROM list_options WHERE list_id='AllcareWPDB'");
while ($list_val    = sqlFetchArray($list)) {
    //wordpress db connection
    if($list_val['option_id'] == 'host'){
        $dbhost = $list_val['notes'];
    }
    if($list_val['option_id'] == 'user'){
        $dbuser = $list_val['notes'];
    }
    if($list_val['option_id'] == 'pwd') {
        $dbpass = $list_val['notes'];
    }
    if($list_val['option_id'] == 'dbname'){
        $dbname = $list_val['notes']; 
    }
}
 $conn = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

// get primary entity practice id 
$get_practice_id = sqlStatement("SELECT domain_identifier FROM facility WHERE primary_business_entity = 1 AND billing_location = 1");
while($set_practice_id = sqlFetchArray($get_practice_id)){
    $practice_id = $set_practice_id['domain_identifier'];
}

if($practice_id != ''){
    $final_fetched_ids = '';
    $sql_post_id = $conn->query("SELECT ID FROM `wp_posts` p INNER JOIN wp_postmeta pm ON p.ID = pm.post_id where p.post_type='payer' AND pm.meta_key ='practice_id' AND meta_value = '$practice_id' ");
    if ($sql_post_id->num_rows != 0) {
        while($fetch_post_id = $sql_post_id->fetch_object()){
            $fetch_meta_ids         .= "'$fetch_post_id->ID',";
        }
    }
    
    $final_fetched_ids = rtrim($fetch_meta_ids,','); 
    

    if($final_fetched_ids != ''){
    
        $sql_post = $conn->query("SELECT * FROM `wp_posts` WHERE ID IN ($final_fetched_ids) OR post_parent IN ($final_fetched_ids) ");
        if ($sql_post->num_rows != 0) {
            $create_payer_table = sqlStatement("CREATE TABLE IF NOT EXISTS `wp_posts` (
                                                      `ID` bigint(20) unsigned NOT NULL,
                                                      `post_author` bigint(20) unsigned NOT NULL DEFAULT '0',
                                                      `post_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
                                                      `post_date_gmt` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
                                                      `post_content` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
                                                      `post_title` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
                                                      `post_excerpt` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
                                                      `post_status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'publish',
                                                      `comment_status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'open',
                                                      `ping_status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'open',
                                                      `post_password` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
                                                      `post_name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
                                                      `to_ping` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
                                                      `pinged` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
                                                      `post_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
                                                      `post_modified_gmt` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
                                                      `post_content_filtered` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
                                                      `post_parent` bigint(20) unsigned NOT NULL DEFAULT '0',
                                                      `guid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
                                                      `menu_order` int(11) NOT NULL DEFAULT '0',
                                                      `post_type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'post',
                                                      `post_mime_type` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
                                                      `comment_count` bigint(20) NOT NULL DEFAULT '0'
                                                    ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
            $truncate_table = sqlStatement("TRUNCATE `wp_posts`");
            $meta_ids = '';

            //wp_posts
            while($post_id = $sql_post->fetch_object()){
                $insert_key_string = '';
                $insert_val_string = '';

                $meta_ids         .= "'$post_id->ID',";

                foreach($post_id as $pkey => $pvalue){
                    $insert_key_string .= "`$pkey`,";
                    $insert_val_string .= "'$pvalue',";
                }

                $final_columns_key = rtrim($insert_key_string,",");
                $final_columns_val = rtrim($insert_val_string,",");
                $string_insert = sqlStatement("INSERT INTO `wp_posts` ($final_columns_key) VALUES($final_columns_val)");
            }
            $final_meta_ids = rtrim($meta_ids,",");
            if(!empty($final_meta_ids)){
                $create_payerplan_meta_table = sqlStatement("CREATE TABLE IF NOT EXISTS `wp_postmeta` (
                                                            `meta_id` bigint(20) unsigned NOT NULL,
                                                              `post_id` bigint(20) unsigned NOT NULL DEFAULT '0',
                                                              `meta_key` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                                              `meta_value` longtext COLLATE utf8mb4_unicode_ci
                                                            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

                $truncate_table = sqlStatement("TRUNCATE `wp_postmeta`");

                $sql_post_meta = $conn->query("SELECT * FROM `wp_postmeta` WHERE post_id IN($final_meta_ids) ");

                while($post_meta_id = $sql_post_meta->fetch_object()){
                    $insert_metakey_string = '';
                    $insert_metaval_string = '';
                    foreach($post_meta_id as $pmetakey => $pmetavalue){
                        $insert_metakey_string .= "`$pmetakey`,";
                        $insert_metaval_string .= "'$pmetavalue',";
                    }
                    $final_columns_metakey = rtrim($insert_metakey_string,",");
                    $final_columns_metaval = rtrim($insert_metaval_string,",");
                    $string_insert_meta = sqlStatement("INSERT INTO `wp_postmeta` ($final_columns_metakey) VALUES($final_columns_metaval)");
                }
                
                // insert benifits
                $create_benifits_screen = sqlStatement("CREATE TABLE IF NOT EXISTS `wp_benefits` (
                    `id` int(11) NOT NULL,
                      `practice_id` int(30) NOT NULL,
                      `plan_id` int(30) NOT NULL,
                      `visit_category` int(30) NOT NULL,
                      `benefit_title` varchar(255) NOT NULL,
                      `benefit_type` varchar(20) NOT NULL COMMENT 'deductables,oop,copay',
                      `in_individual` varchar(255) NOT NULL,
                      `in_family` varchar(255) NOT NULL,
                      `out_individual` varchar(255) NOT NULL,
                      `out_family` varchar(255) NOT NULL,
                      `in_copay` varchar(255) NOT NULL,
                      `out_copay` varchar(255) NOT NULL
                    ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;");

                $truncate_table = sqlStatement("TRUNCATE `wp_benefits`");

                $sql_benifits_meta = $conn->query("SELECT * FROM `wp_benefits` WHERE plan_id IN($final_meta_ids) ");
                
                while($benifits_meta_id = $sql_benifits_meta->fetch_object()){
                    $insert_benifitmetakey_string = '';
                    $insert_benifitmetaval_string = '';
                    foreach($benifits_meta_id as $bmetakey => $bmetavalue){
                        $insert_benifitmetakey_string .= "`$bmetakey`,";
                        $insert_benifitmetaval_string .= "'$bmetavalue',";
                    }
                    $final_benifitcolumns_metakey = rtrim($insert_benifitmetakey_string,",");
                    $final_benifitcolumns_metaval = rtrim($insert_benifitmetaval_string,",");
                    $benifitstring_insert_meta = sqlStatement("INSERT INTO `wp_benefits` ($final_benifitcolumns_metakey) VALUES($final_benifitcolumns_metaval)");
                }
                // insert benifits name slugs
                $create_benifits_label_screen = sqlStatement("CREATE TABLE IF NOT EXISTS `wp_terms` (
                        `term_id` bigint(20) unsigned NOT NULL,
                          `name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
                          `slug` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
                          `term_group` bigint(10) NOT NULL DEFAULT '0'
                        ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

                $truncate_benefits_table = sqlStatement("TRUNCATE `wp_terms`");
                
                $sql_benifits_label_meta = $conn->query("SELECT * FROM `wp_terms` WHERE plan_id IN($final_meta_ids) ");
                
                while($benifits_label_meta_id = $sql_benifits_label_meta->fetch_object()){
                    $insert_benifit_labelmetakey_string = '';
                    $insert_benifit_labelmetaval_string = '';
                    foreach($benifits_label_meta_id as $blmetakey => $blmetavalue){
                        $insert_benifit_labelmetakey_string .= "`$blmetakey`,";
                        $insert_benifit_labelmetaval_string .= "'$blmetavalue',";
                    }
                    $final_benifit_labelcolumns_metakey = rtrim($insert_benifit_labelmetakey_string,",");
                    $final_benifit_labelcolumns_metaval = rtrim($insert_benifit_labelmetaval_string,",");
                    $benifit_labelstring_insert_meta = sqlStatement("INSERT INTO `wp_terms` ($final_benifit_labelcolumns_metakey) VALUES($final_benifit_labelcolumns_metaval)");
                }
            }
        }
    }
    
}

