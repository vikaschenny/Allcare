<?php
$ignoreAuth=true; // ignore the standard authentication for a regular OpenEMR user

include_once('../../interface/globals.php');
 
include_once("$srcdir/api.inc");
require_once("$srcdir/patient.inc");
require_once("$srcdir/options.inc.php");
require_once("$srcdir/htmlspecialchars.inc.php");
include_once("$srcdir/acl.inc");

//echo "<pre>"; print_r($_POST); echo "</pre>";

$codes = $_POST['cpt_values'];
$icd_primary = $_POST['icd_primary'];
$icd_justify = $_POST['icd_justify'];
$icd_mproblem = $_POST['icd_mproblem'];

$icd_values = substr($_POST['icd_values'],1); 
$primaryCPT = $_POST['primaryCPT']; // Get Primary CPTs so as to identify them to justify ICDs
$primaryCPTArr = explode(",",$primaryCPT);

$getid = sqlStatement("select id from users where username='".$_SESSION['provider']."'");
while($setid = sqlFetchArray($getid)){
    $user  = $setid['id'];
}
$encounter                  = $_POST['encounter'];
$pid                        = $_POST['pid'];
$user                       = isset($user)? $user :0;
$provider_id                = $_POST['providerid'];
$billed                     = 0;
$bill_date                  = 'NULL';
$bill_process               = 0;
$payer_id                   = 'NULL';
$process_date               = 'NULL'; 
$process_file               = 'NULL';
$authorized                 = 1; 
$activity                   = 1 ;
$groupname                  = 'default';
$code_text                  = '';
$modifier                   = '';
$target                     = '';
     
if(!empty($icd_justify) || !empty($icd_primary)){
    $justify =  !empty($icd_primary )? str_replace(":","|",$icd_primary).":" : ''; 
    for($i=0; $i<count($icd_justify); $i++){ 
        $justify .= str_replace(":","|",$icd_justify[$i]).":";
    }
}
if(!empty($codes)){
    
    $codesarray = explode(",",$codes);
    for($i=0; $i<count($codesarray); $i++){
        $allcarecptvsicd = "";
        if($codesarray[$i] !== ''){

            $desc = sqlStatement("select id, code_text from codes where code='".$codesarray[$i]."' AND code_type=1");
            $string = '';

                $codes_desc = $codeKey = "";
                while($setdesc = sqlFetchArray($desc)){
                   $codes_desc = $setdesc['code_text'];
                   $codeKey = $setdesc['id'];
                   $string = str_replace("'", "\\'", $codes_desc);
                }
                if(isset($_REQUEST['cpt_modifier_'.$codesarray[$i]]))
                    $modifier = $_REQUEST['cpt_modifier_'.$codesarray[$i]];
                // Get standard fee related to the CPT
                $pres = sqlQuery("SELECT p.pr_price " .
                                      "FROM prices AS p " .
                                      "WHERE  p.pr_id = ? AND p.pr_selector = '' AND p.pr_level = 'standard' ", array($codeKey) );
                /*
                 * Identify whether $codesarray[$i] is included in AllCareCPTvsICD. If exists then
                 * map/justify the corresponding ICDs to the CPT
                 */
                $notes = "";
                $getquery = sqlQuery("SELECT notes
                                FROM  `list_options` 
                                WHERE  `list_id` =  'AllCareCPTvsICD'
                                AND FIND_IN_SET(  '".$codesarray[$i]."', REPLACE( title, SPACE( 1 ) ,  '' ) ) >0");
                $getICDsMapped = "TRIM('". str_replace(",","'),TRIM('",$getquery['notes']) ."')";
                $notes = $getquery['notes']; // This is to check if this cpt is in mapped list or no
                $getquery = sqlStatement("SELECT DISTINCT formatted_dx_code, long_desc FROM icd10_dx_order_code WHERE formatted_dx_code IN (".$getICDsMapped.") AND active = 1");
                while($setquery = sqlFetchArray($getquery)){
                    $allcarecptvsicd .= "ICD10|".$setquery['formatted_dx_code'].",";
                }
                
                /* This logic is to assign selected ICDS to Primary. And mapped ICDs to mapped CPTs
                 * Rest of the selected CPTs will not get any ICDs
                 */
                /* This logic is now abandoned - 20151209 */
                /*
                $icdJustify = "";
                if(in_array($codesarray[$i],$primaryCPTArr)):
                    $icdJustify = $justify;
                else:
                    $icdJustify = $allcarecptvsicd;
                endif;
                $sql = sqlStatement("INSERT INTO billing (date, code_type, code, pid, provider_id, user,  groupname, authorized, encounter,  code_text, billed, activity,payer_id,bill_process,bill_date,process_date,process_file,modifier,units,fee,justify,target)"
                        . " VALUES ( NOW(),'CPT4','".$codesarray[$i]."','$pid','$provider_id', '$user','$groupname', ' $authorized',  ' $encounter', '$string', '$billed', '$activity' ,'$payer_id','$bill_process','$bill_date','$process_date','$process_file','$modifier','".$codearray[$i]['units']."','".$pres['pr_price']."','$icdJustify','$target')"); 
                */
                /* This logic is to assign selected ICDS to Primary and non mapped CPTs. And mapped ICDs to mapped CPTs
                 * 
                 */
                $icdJustify = "";
                if(in_array($codesarray[$i],$primaryCPTArr) || $notes == ""):
                    $icdJustify = $justify;
                else:
                    $selectedJustify = "";
                    $selectedJustify = $_POST['icdjustify_'.$codesarray[$i]]; //This will get all the icds selected for a Predefined CPT
                    $prefixedselectedJustify = preg_filter('/^/', 'ICD10|', $selectedJustify); // This will prefix ICD10| to each icd in array
                    $allcarecptvsicd = implode(",",$prefixedselectedJustify);
                    $icdJustify = $allcarecptvsicd;
                endif;
                $sql = sqlStatement("INSERT INTO billing (date, code_type, code, pid, provider_id, user,  groupname, authorized, encounter,  code_text, billed, activity,payer_id,bill_process,bill_date,process_date,process_file,modifier,units,fee,justify,target)"
                        . " VALUES ( NOW(),'CPT4','".$codesarray[$i]."','$pid','$provider_id', '$user','$groupname', ' $authorized',  ' $encounter', '$string', '$billed', '$activity' ,'$payer_id','$bill_process','$bill_date','$process_date','$process_file','$modifier','".$codearray[$i]['units']."','".$pres['pr_price']."','$icdJustify:','$target')"); 
        }
    }
   
}

// If new CPT is not added then old/new CPT ICD justification UPDATE goes here
$getCPTs = sqlStatement("SELECT id,code FROM billing WHERE pid =".$pid." AND encounter=".$encounter." AND code_type='CPT4' AND activity=1");
while($row = sqlFetchArray($getCPTs)):
    $selectedJustify = "";
    $selectedJustify = $_POST['icdjustify_'.$row['code']]; //This will get all the icds selected for a Predefined CPT
    $prefixedselectedJustify = preg_filter('/^/', 'ICD10|', $selectedJustify); // This will prefix ICD10| to each icd in array
    $allcarecptvsicd = implode(",",$prefixedselectedJustify);
    $icdJustify = $allcarecptvsicd;
    if($icdJustify != "") sqlStatement("UPDATE billing SET justify='".$icdJustify.":' WHERE id=".$row['id']);
endwhile;

if(!empty($provider_id))
    $sqlprovider = sqlStatement("UPDATE form_encounter SET rendering_provider = $provider_id , provider_id = $provider_id WHERE encounter = $encounter");
if(!empty($icd_mproblem)){
     for($i=0; $i<count($icd_mproblem); $i++){ 
        $icd_code2 = ''; 
        $icdProblem = '';
        $icdProblemArr = explode("$$",$icd_mproblem[$i]);
        if(count($icdProblemArr)>1):
            $icdProblem = $icdProblemArr[0];
        else:  
            $icdProblem = $icd_mproblem[$i];
        endif;
        
        if(strpos($icdProblem, 'ICD9',0)!== false){
            $checkstring = "ICD9:";
            $checkstring1 = 'ICD9';
            $table = 'icd9_dx_code';
            $icdstring = str_replace("ICD9:","",$icdProblem);
        }
        if(strpos($icdProblem, 'ICD10',0)!== false){
            $checkstring = "ICD10:";
            $table = 'icd10_dx_order_code';
            $checkstring1 = 'ICD10';
            $icdstring = str_replace("ICD10:","",$icdProblem);
        }
        $exploded_code = str_replace($checkstring, "", $icdstring);
//        echo "SELECT long_desc FROM $table WHERE formatted_dx_code ='$icdstring' and active= 1";
        $descr2 = sqlStatement("SELECT long_desc FROM $table WHERE formatted_dx_code ='$icdstring' and active= 1"); 
        $title3 = '';
        while($setdescr2 = sqlFetchArray($descr2)){
            $title =   $setdescr2['long_desc'];
            $title3 = str_replace("'", "\\'", $title);
            $title3 = trim($title3);
        }
        $icd_code3 = str_replace($checkstring, "",$icdstring );
//        $check_query2 = sqlStatement("SELECT * FROM billing WHERE code = '$icd_code3' AND activity = 1 AND pid = '$pid' and encounter = $encounter AND code_text = '$title3'"); 
//        $setquery2 = sqlFetchArray($check_query2);
//        if(empty($setquery2)){
            // Check if icd is already there in list or no. If present then it should not be allowed to add in lists and feesheet again
            $checkQuery = sqlStatement("SELECT id FROM lists WHERE pid=".$pid." AND type='medical_problem' 
                                        AND diagnosis='$checkstring1:$icdstring' AND enddate IS NULL");
            $checkRows = sqlNumRows($checkQuery);
            if($checkRows == 0):
                $sql12 =  sqlStatement("INSERT INTO billing (date, code_type, code, pid, provider_id, user,  groupname, authorized, encounter,  code_text, billed, activity,payer_id,bill_process,bill_date,process_date,process_file,target)"
                                        . " VALUES ( NOW(),'$checkstring1','$icd_code3','$pid','$provider_id', '$user','$groupname', ' $authorized',  ' $encounter', '$title3', '$billed', '$activity' ,'$payer_id','$bill_process','$bill_date', '$process_date','$process_file','$target')");
                $sql = sqlStatement("INSERT INTO lists ( date, pid, type, title, comments, begdate, enddate,  diagnosis, occurrence,  referredby,  outcome, destination,    reaction )"
                            . " VALUES ( NOW(), '$pid','medical_problem','$title3','',NOW(), NULL,'$checkstring1:$icdstring', 0,  '', 0, '', '' )
                             ");    

                $sel = sqlStatement("select id from lists where type='medical_problem' AND title='$title3' AND pid=$pid");
                $setdescr2 = sqlFetchArray($sel);
                if(empty($setdescr2)){
                    $newid = $setdescr2[0]->id;

                    $sql_ie = sqlStatement("SELECT * FROM issue_encounter where pid = $pid and list_id = $newid and encounter = $encounter");
                    $idval_ie = sqlFetchArray($sql_ie);
                    if(empty($idval_ie)){
                        $sql_list = sqlStatement("INSERT INTO issue_encounter ( pid, list_id, encounter) VALUES ( $pid, $newid, $encounter)");
                    }
                    $selve=sqlStatement("select pid,type from lists_touch where pid=$pid");
                    $idval_ies = sqlFetchArray($selve);
                    if(empty($idval_ies)){
                        $sql6 = sqlStatement("INSERT INTO lists_touch ( pid, type, date) VALUES ( $pid, 'medical_problem', NOW())");
                    }        
                }
           endif;    
        //}
            
     }
}

if(!empty($icd_primary) || !empty($icd_values)){
    $icd_justify[] = $icd_primary;
    $icdarray = explode(",",$icd_values);
    
    // If user did not specify PRIMARY then first selected ICD10 would add in first selection for the CPT. If this is not done
    // then user would see MYSQL error message. So to avoid such messages this is been done.
    if($icd_justify[0] == ""):
        $icd_justify[0] = "ICD10:".$icdarray[0];
    endif;
    
    for($j= 0; $j< count($icdarray); $j++){
        if($icdarray[$j] !== '')
            $icd_justify[] = "ICD10:".$icdarray[$j];
    }
//    print_r($icd_justify);
    for($i=0; $i<count($icd_justify); $i++){ 
        $icd_code2 = ''; 
        if($icd_justify !== ''){
            if(strpos($icd_justify[$i], 'ICD9',0)!== false){
                $checkstring = "ICD9:";
                $checkstring1 = 'ICD9';
                $table = 'icd9_dx_code';
                $icdstring = str_replace("ICD9:","",$icd_justify[$i]);
            }
            if(strpos($icd_justify[$i], 'ICD10',0)!== false){
                $checkstring = "ICD10:";
                $table = 'icd10_dx_order_code';
                $checkstring1 = 'ICD10';
                $icdstring = str_replace("ICD10:","",$icd_justify[$i]);
            }
            $exploded_code = str_replace($checkstring, "", $icdstring);
    //        echo "SELECT long_desc FROM $table WHERE formatted_dx_code ='$icdstring' and active= 1";
            $descr2 = sqlStatement("SELECT long_desc FROM $table WHERE formatted_dx_code ='$icdstring' and active= 1"); 
            $title3 = '';
            while($setdescr2 = sqlFetchArray($descr2)){
                $title =   $setdescr2['long_desc'];
                $title3 = str_replace("'", "\\'", $title);
                $title3 = trim($title3);
            }
            $icd_code3 = str_replace($checkstring, "",$icdstring );
    //        echo "SELECT * FROM billing WHERE code = '$icd_code3' AND activity = 1 AND pid = '$pid' and encounter = $encounter AND code_text = '$title3'";
            $check_query2 = sqlStatement("SELECT * FROM billing WHERE code = '$icd_code3' AND activity = 1 AND pid = '$pid' and encounter = $encounter AND code_text = '$title3'"); 
            $setquery2 = sqlFetchArray($check_query2);
            if(empty($setquery2)){
                $sql12 =  sqlStatement("INSERT INTO billing (date, code_type, code, pid, provider_id, user,  groupname, authorized, encounter,  code_text, billed, activity,payer_id,bill_process,bill_date,process_date,process_file,target)"
                                        . " VALUES ( NOW(),'$checkstring1','$icd_code3','$pid','$provider_id', '$user','$groupname', ' $authorized',  ' $encounter', '$title3', '$billed', '$activity' ,'$payer_id','$bill_process','$bill_date', '$process_date','$process_file','$target')");
            }

         }
    }
}
echo "<script>window.close();

    window.opener.location.href = '../../../practice/provider_incomplete_charts.php?provider=".$_SESSION['provider']."';</script>";
?>
