<?php
$ignoreAuth=true; // ignore the standard authentication for a regular OpenEMR user

include_once('../../globals.php');
 
include_once("$srcdir/api.inc");
require_once("$srcdir/patient.inc");
require_once("$srcdir/options.inc.php");
require_once("$srcdir/htmlspecialchars.inc.php");
include_once("$srcdir/acl.inc");

$form_code_type = $_REQUEST['code_type'];
$search_term2   = $_REQUEST['searchstring'];

if($form_code_type == 'ICD9'){ 
    $query = "SELECT icd9_dx_code.formatted_dx_code AS code, icd9_dx_code.long_desc AS code_text, icd9_dx_code.short_desc AS code_text_short,  'ICD9' AS code_type_name
        FROM icd9_dx_code
        LEFT OUTER JOIN  `codes` ON icd9_dx_code.formatted_dx_code = codes.code
        AND codes.code_type =  '2'
        WHERE (icd9_dx_code.formatted_dx_code LIKE  '%$search_term2%'  OR icd9_dx_code.long_desc LIKE  '%$search_term2%' OR icd9_dx_code.long_desc LIKE  '%short_desc%'  )
        AND icd9_dx_code.active =  '1'
        AND (
        codes.active =1 || codes.active IS NULL
        )
        ORDER BY icd9_dx_code.formatted_dx_code +0, icd9_dx_code.formatted_dx_code LIMIT 500";
}elseif($form_code_type == 'ICD10'){ 
    $query = "SELECT  icd10_dx_order_code.dx_code ,icd10_dx_order_code.formatted_dx_code as code, icd10_dx_order_code.long_desc as code_text, icd10_dx_order_code.short_desc as code_text_short,   'ICD10' as code_type_name                   FROM icd10_dx_order_code 
        LEFT OUTER JOIN `codes`  ON icd10_dx_order_code.formatted_dx_code = codes.code AND codes.code_type = '102' 
        WHERE (icd10_dx_order_code.formatted_dx_code like '%$search_term2%'   OR icd10_dx_order_code.long_desc like '%$search_term2%'   OR icd10_dx_order_code.short_desc like '%$search_term2%'  )
        AND icd10_dx_order_code.active='1' 
        AND icd10_dx_order_code.valid_for_coding = '1' 
        AND (codes.active = 1 || codes.active IS NULL)  
        ORDER BY icd10_dx_order_code.formatted_dx_code+0,icd10_dx_order_code.formatted_dx_code LIMIT 500";
}elseif($form_code_type == 'CPT'){ 
    $query = "SELECT codes.code as code, codes.code_text as code_text, codes.code_text_short as code_text_short,  'CPT' as code_type_name   
        FROM codes 
        WHERE (codes.code like '%$search_term2%'  OR codes.code_text like '%$search_term2%'  OR codes.code_text_short like '%$search_term2%'  )
        AND codes.code_type=1 
        AND codes.active = 1  
        ORDER BY codes.code+0,codes.code LIMIT 500";
}
$getquery = sqlStatement($query);
$array = array();
while($setquery = sqlFetchArray($getquery)){
    $array[$setquery['code']] = $setquery['code_text'];
}
echo json_encode($array);
?>