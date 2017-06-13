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
$icd_values = $_POST['icd_values'];

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
        if($codesarray[$i] !== ''){
//            echo "select code_text from codes where code='".$codesarray[$i]."' AND code_type=1";
            $desc = sqlStatement("select code_text from codes where code='".$codesarray[$i]."' AND code_type=1");
            $string = '';
//            if(!empty($desc)){
                while($setdesc = sqlFetchArray($desc)){
                   $codes_desc = $setdesc['code_text'];
                   $string = str_replace("'", "\\'", $codes_desc);
                }
                $sql = sqlStatement("INSERT INTO billing (date, code_type, code, pid, provider_id, user,  groupname, authorized, encounter,  code_text, billed, activity,payer_id,bill_process,bill_date,process_date,process_file,modifier,units,fee,justify,target)"
                        . " VALUES ( NOW(),'CPT4','".$codesarray[$i]."','$pid','$provider_id', '$user','$groupname', ' $authorized',  ' $encounter', '$string', '$billed', '$activity' ,'$payer_id','$bill_process','$bill_date','$process_date','$process_file','$modifier','".$codearray[$i]['units']."','".$codearray[$i]['fee']."','$justify','$target')"); 

//            }
        }
    }
}
if(!empty($icd_mproblem)){
     for($i=0; $i<count($icd_mproblem); $i++){ 
        $icd_code2 = ''; 
        
        if(strpos($icd_mproblem[$i], 'ICD9',0)!== false){
            $checkstring = "ICD9:";
            $checkstring1 = 'ICD9';
            $table = 'icd9_dx_code';
            $icdstring = str_replace("ICD9:","",$icd_mproblem[$i]);
        }
        if(strpos($icd_mproblem[$i], 'ICD10',0)!== false){
            $checkstring = "ICD10:";
            $table = 'icd10_dx_order_code';
            $checkstring1 = 'ICD10';
            $icdstring = str_replace("ICD10:","",$icd_mproblem[$i]);
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
        }
            
     }
}

if(!empty($icd_primary) || !empty($icd_primary) || !empty($icd_values)){
    $icd_justify[] = $icd_primary;
    $icdarray = explode(",",$icd_values);
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

    window.opener.location.href = '../../../providers/provider_incomplete_charts.php?provider=".$_SESSION['provider']."';</script>";
?>
