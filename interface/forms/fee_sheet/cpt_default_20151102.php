<?php
$ignoreAuth=true; // ignore the standard authentication for a regular OpenEMR user

include_once('../../globals.php');
 
include_once("$srcdir/api.inc");
require_once("$srcdir/patient.inc");
require_once("$srcdir/options.inc.php");
require_once("$srcdir/htmlspecialchars.inc.php");
include_once("$srcdir/acl.inc");

$encounterid   = $_REQUEST['encounterid'];
$getfuv = sqlStatement("select facility_id,pc_catid from form_encounter where encounter = '$encounterid'");
$fuvrow = sqlFetchArray($getfuv);
if(!empty($fuvrow)){
    $facility_id    = $fuvrow['facility_id'];
    $pc_catid       = $fuvrow['pc_catid'];
}
//$getquery = sqlStatement("SELECT co.code AS code, co.code_text 
//                            FROM fee_sheet_options fo
//                            INNER JOIN tbl_allcare_vistcat_codegrp vc ON vc.code_groups = fo.fs_category
//                            INNER JOIN codes co ON SUBSTRING( fo.fs_codes, 6, LENGTH( fo.fs_codes ) -6 ) = co.code
//                            WHERE  `facility` = '$facility_id'
//                            AND  `visit_category` = '$pc_catid'
//                            AND vc.code_options REGEXP (fo.fs_option)");

$getquery = sqlStatement("SELECT fo.fs_option, vc.code_options,fo.fs_codes FROM fee_sheet_options fo INNER JOIN tbl_allcare_vistcat_codegrp vc ON vc.code_groups = fo.fs_category  WHERE `facility` = 3 AND `visit_category` = 17 AND vc.code_options REGEXP (fo.fs_option)");
$array = array();
while($setquery = sqlFetchArray($getquery)){
    $codes = $setquery['fs_codes'];
    $codesarray = explode('~',str_replace("CPT4","",str_replace("|","",$codes) ));
    for($i=0; $i< count($codesarray); $i++){
//        echo "SELECT code_text FROM codes WHERE code = '".$codesarray[$i]."'";
        $getcodes = sqlStatement("SELECT code_text FROM codes WHERE code = '".$codesarray[$i]."'");
        $setcodes = sqlFetchArray($getcodes);
        if(!empty($setcodes)){
            $array[$codesarray[$i]]= $setcodes['code_text'];
        }
    }
    
}

echo json_encode($array);
?>