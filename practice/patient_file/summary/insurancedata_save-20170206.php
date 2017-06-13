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

//include_once("../../globals.php");
include_once("$srcdir/patient.inc");
include_once("$srcdir/acl.inc");
include_once("$srcdir/options.inc.php");
include_once("$srcdir/formdata.inc.php");


// Check authorization.
if ($pid) {
  if ( !acl_check('patients','demo','','write') )
    die(xl('Updating demographics is not authorized.'));
  $tmp = getPatientData($pid, "squad");
  if ($tmp['squad'] && ! acl_check('squads', $tmp['squad']))
    die(xl('You are not authorized to access this squad.'));
} else {
  if (!acl_check('patients','demo','',array('write','addonly') ))
    die(xl('Adding demographics is not authorized.'));
}
 
foreach ($_POST as $key => $val) {
  if ($val == "MM/DD/YYYY") {
    $_POST[$key] = "";
  }
}

// Update patient_data and employer_data:
//
$newdata = array();
$newdata['patient_data']['id'] = $_POST['db_id'];
$fres = sqlStatement("SELECT * FROM layout_options " .
  "WHERE form_id = 'DEM' AND uor > 0 AND field_id != '' " .
  "ORDER BY group_name, seq");
while ($frow = sqlFetchArray($fres)) {
  $data_type = $frow['data_type'];
  $field_id  = $frow['field_id'];
  // $value  = '';
  $colname = $field_id;
  $table = 'patient_data';
  if (strpos($field_id, 'em_') === 0) {
    $colname = substr($field_id, 3);
    $table = 'employer_data';
  }

  // if (isset($_POST["form_$field_id"])) $value = $_POST["form_$field_id"];
  $value = get_layout_form_value($frow);

  $newdata[$table][$colname] = $value;
}

$i1dob = fixDate(formData("i1subscriber_DOB"));
$i1date = fixDate(formData("i1effective_date"), date('Y-m-d'));

$get_patient_name = sqlStatement("SELECT CONCAT(fname,' ', mname , ' ', lname) as patient_name FROM patient_data WHERE pid = '$pid'");

while($set_patient_name = sqlFetchArray($get_patient_name)){
    $patient_name = $set_patient_name['patient_name'];
}
newInsuranceData(
  $pid,
  "primary",
  formData("i1provider"),
  formData("i1policy_number"),
  formData("i1group_number"),
  formData("i1plan_name_label"),
  formData("i1subscriber_lname"),
  formData("i1subscriber_mname"),
  formData("i1subscriber_fname"),
  formData("form_i1subscriber_relationship"),
  formData("i1subscriber_ss"),
  $i1dob,
  formData("i1subscriber_street"),
  formData("i1subscriber_postal_code"),
  formData("i1subscriber_city"),
  formData("form_i1subscriber_state"),
  formData("form_i1subscriber_country"),
  formData("i1subscriber_phone"),
  formData("i1subscriber_employer"),
  formData("i1subscriber_employer_street"),
  formData("i1subscriber_employer_city"),
  formData("i1subscriber_employer_postal_code"),
  formData("form_i1subscriber_employer_state"),
  formData("form_i1subscriber_employer_country"),
  formData('i1copay'),
  formData('form_i1subscriber_sex'),
  $i1date,
  formData('i1accept_assignment'),
  formData('i1policy_type')
);


$i2dob = fixDate(formData("i2subscriber_DOB"));
$i2date = fixDate(formData("i2effective_date"), date('Y-m-d'));

newInsuranceData(
  $pid,
  "secondary",
  formData("i2provider"), 
  formData("i2policy_number"),
  formData("i2group_number"),
  formData("i2plan_name_label"),
  formData("i2subscriber_lname"),
  formData("i2subscriber_mname"),
  formData("i2subscriber_fname"),
  formData("form_i2subscriber_relationship"),
  formData("i2subscriber_ss"),
  $i2dob,
  formData("i2subscriber_street"),
  formData("i2subscriber_postal_code"),
  formData("i2subscriber_city"),
  formData("form_i2subscriber_state"),
  formData("form_i2subscriber_country"),
  formData("i2subscriber_phone"),
  formData("i2subscriber_employer"),
  formData("i2subscriber_employer_street"),
  formData("i2subscriber_employer_city"),
  formData("i2subscriber_employer_postal_code"),
  formData("form_i2subscriber_employer_state"),
  formData("form_i2subscriber_employer_country"),
  formData('i2copay'),
  formData('form_i2subscriber_sex'),
  $i2date,
  formData('i2accept_assignment'),
  formData('i2policy_type')
);

$i3dob  = fixDate(formData("i3subscriber_DOB"));
$i3date = fixDate(formData("i3effective_date"), date('Y-m-d'));

newInsuranceData(
  $pid,
  "tertiary",
  formData("i3provider"),
  formData("i3policy_number"),
  formData("i3group_number"),
  formData("i3plan_name_label"),
  formData("i3subscriber_lname"),
  formData("i3subscriber_mname"),
  formData("i3subscriber_fname"),
  formData("form_i3subscriber_relationship"),
  formData("i3subscriber_ss"),
  $i3dob,
  formData("i3subscriber_street"),
  formData("i3subscriber_postal_code"),
  formData("i3subscriber_city"),
  formData("form_i3subscriber_state"),
  formData("form_i3subscriber_country"),
  formData("i3subscriber_phone"),
  formData("i3subscriber_employer"),
  formData("i3subscriber_employer_street"),
  formData("i3subscriber_employer_city"),
  formData("i3subscriber_employer_postal_code"),
  formData("form_i3subscriber_employer_state"),
  formData("form_i3subscriber_employer_country"),
  formData('i3copay'),
  formData('form_i3subscriber_sex'),
  $i3date,
  formData('i3accept_assignment'),
  formData('i3policy_type')
);

// Update insurance_data  custom fields:

foreach (array('primary','secondary','tertiary') as $instype) {
    $primarydata = array();
    $prefix='';
    if($instype=='primary'){
        $primarydata['id'] = $_POST['pri_ins_id'];
        $prefix='i1';
    }
    if($instype=='secondary'){
        $primarydata['id'] = $_POST['sec_ins_id'];
        $prefix='i2';
    }
    if($instype=='tertiary'){
        $primarydata['id'] = $_POST['ter_ins_id'];
        $prefix='i3';
    }
    
    

    $fres = sqlStatement("SELECT * FROM layout_options " .
                          "WHERE form_id = 'INSCUTOM' AND uor > 0 AND field_id != '' " .
                          "ORDER BY group_name, seq");
    while ($frow = sqlFetchArray($fres)) {
      $data_type = $frow['data_type'];
      $field_id  = $frow['field_id'];
      // $value  = '';
      $colname = $field_id;
      $table = 'insurance_data';
      $frow['field_id'] = $prefix.$field_id;

      
      // if (isset($_POST["form_$field_id"])) $value = $_POST["form_$field_id"];
      $value = get_layout_form_value($frow);

      $primarydata[$colname] = $value;

      foreach($primarydata as $key =>$key_value){
          $revised_elig_date = '';
            if($key == 'revised_elig_date'){
                if($instype == 'primary'){
                    if(formData("i1provider") == $_POST['i1provider_change'] && formData("i1plan_name") == $_POST['i1plan_name_change'])
                        $revised_elig_date = $key_value;
                    else
                        $revised_elig_date = date("Y-m-d");
                }
                else if($instype == 'secondary'){

                    if(formData("i2provider") == $_POST['i2provider_change'] && formData("i2plan_name") == $_POST['i2plan_name_change'])
                        $revised_elig_date = $key_value;
                    else
                        $revised_elig_date = date("Y-m-d");

                } 
                else if($instype == 'tertiary'){

                    if(formData("i3provider") == $_POST['i3provider_change'] && formData("i3plan_name") == $_POST['i3plan_name_change'])
                        $revised_elig_date = $key_value;
                    else
                        $revised_elig_date = date("Y-m-d");

                }
                 $primarydata[$key] = $revised_elig_date;
            }
        }

    }
    $title = "New Payer Assigned to Patient $patient_name.";
    // to insert new payerplan insurance data
    if($instype == 'primary'){
        if(formData("i1providerid") !== '' && formData("i1provider") == ''){
            $body = addslashes(" New Payer with name '".formData("i1providerid") ."' is assigned to Patient name $patient_name ($pid). ");
            $sql = sqlStatement("INSERT INTO pnotes (date, body, pid, user, groupname, activity, authorized, title, assigned_to, message_status)
                     values (NOW(), CONCAT(date_format(now(), '%Y-%m-%d %H:%i'),'".' '."(".$_SESSION['authUser']." to ".$_SESSION['authUser'].")".' '." $body(primary)'), $pid, '".$_SESSION['authUser']."', 'Default', 1, 1, '$title', '".$_SESSION['authUser']."', 'New')");

        }
    }
    else if($instype == 'secondary'){
        if(formData("i2providerid") !== '' && formData("i2provider") == ''){
            $body = addslashes(" New Payer with name '".formData("i2providerid") ."' is assigned to Patient name $patient_name ($pid). ");
             $sql = sqlStatement("INSERT INTO pnotes (date, body, pid, user, groupname, activity, authorized, title, assigned_to, message_status)
                     values (NOW(), CONCAT(date_format(now(), '%Y-%m-%d %H:%i'),'".' '."(".$_SESSION['authUser']." to ".$_SESSION['authUser'].")".' '." $body(secondary)'), $pid, '".$_SESSION['authUser']."', 'Default', 1, 1, '$title', '".$_SESSION['authUser']."', 'New')");
        }
    }
    else if($instype == 'tertiary'){
        if(formData("i3providerid") !== '' && formData("i3provider") == ''){
            $body = addslashes(" New Payer with name '".formData("i3providerid") ."' is assigned to Patient name $patient_name ($pid). ");
             $sql = sqlStatement("INSERT INTO pnotes (date, body, pid, user, groupname, activity, authorized, title, assigned_to, message_status)
                     values (NOW(), CONCAT(date_format(now(), '%Y-%m-%d %H:%i'),'".' '."(".$_SESSION['authUser']." to ".$_SESSION['authUser'].")".' '." $body(teritary)'), $pid, '".$_SESSION['authUser']."', 'Default', 1, 1, '$title', '".$_SESSION['authUser']."', 'New')");
        }
    }
    
    $plantitle = "New Plan added to Patient $patient_name.";
    
    // to insert new payerplan insurance data
    if($instype == 'primary'){
        if(formData("i1plan_name-id") == '' && formData("i1plan_name") !== ''){
            $planbody = addslashes(" New Plan type '".formData("i1plan_name") ."' is added to Patient name $patient_name ($pid). ");
            $sql = sqlStatement("INSERT INTO pnotes (date, body, pid, user, groupname, activity, authorized, title, assigned_to, message_status)
                     values (NOW(), CONCAT(date_format(now(), '%Y-%m-%d %H:%i'),'".' '."(".$_SESSION['authUser']." to ".$_SESSION['authUser'].")".' '." $planbody(primary)'), $pid, '".$_SESSION['authUser']."', 'Default', 1, 1, '$plantitle', '".$_SESSION['authUser']."', 'New')");

        }
    }
    else if($instype == 'secondary'){
        if(formData("i2plan_name-id") == '' && formData("i2plan_name") !== ''){
            $planbody = addslashes(" New Plan type '".formData("i2plan_name") ."' is added to Patient name $patient_name ($pid). ");
             $sql = sqlStatement("INSERT INTO pnotes (date, body, pid, user, groupname, activity, authorized, title, assigned_to, message_status)
                     values (NOW(), CONCAT(date_format(now(), '%Y-%m-%d %H:%i'),'".' '."(".$_SESSION['authUser']." to ".$_SESSION['authUser'].")".' '." $planbody(secondary)'), $pid, '".$_SESSION['authUser']."', 'Default', 1, 1, '$plantitle', '".$_SESSION['authUser']."', 'New')");
        }
    }
    else if($instype == 'tertiary'){
        if(formData("i3plan_name-id") == '' && formData("i3plan_name") !== ''){
            $planbody = addslashes(" New Plan type '".formData("i3plan_name") ."' is added to Patient name $patient_name ($pid). ");
             $sql = sqlStatement("INSERT INTO pnotes (date, body, pid, user, groupname, activity, authorized, title, assigned_to, message_status)
                     values (NOW(), CONCAT(date_format(now(), '%Y-%m-%d %H:%i'),'".' '."(".$_SESSION['authUser']." to ".$_SESSION['authUser'].")".' '." $planbody(teritary)'), $pid, '".$_SESSION['authUser']."', 'Default', 1, 1, '$plantitle', '".$_SESSION['authUser']."', 'New')");
        }
    }
    // insert data
    $numrows = 0;
    $check_id_available = sqlStatement("SELECT * FROM `tbl_patient_insurancedata_meta_data` WHERE insurance_id = '".$primarydata['id']."' AND `pid`='$pid' AND `type`='$instype'");
    $set_id_available = sqlFetchArray($check_id_available);
    if (! empty($set_id_available)) {
        $data_available = 1;
        if($instype == 'primary'){
            $update_meta = sqlStatement("UPDATE `tbl_patient_insurancedata_meta_data` SET `updated_date` =NOW(), `user`='".$_SESSION['authUser']."', `provider`='".$_SESSION['authUser']."',`plan_id`='".formData("i1plan_name-id")."',`payer_name`='".formData("i1providerid") ."' , `payer_id` ='".formData("i1provider-id") ."', post_parent='".formData("i1provider_payerplan") ."'  WHERE `insurance_id`='".$primarydata['id']."' AND `pid`='$pid' AND `type`='$instype' ");
        }
        else if($instype == 'secondary'){
            $update_meta = sqlStatement("UPDATE `tbl_patient_insurancedata_meta_data` SET `updated_date` =NOW(), `user`='".$_SESSION['authUser']."', `provider`='".$_SESSION['authUser']."',`plan_id`='".formData("i2plan_name-id")."',`payer_name`='".formData("i21providerid") ."', `payer_id` ='".formData("i2provider-id") ."', post_parent='".formData("i2provider_payerplan") ."' WHERE `insurance_id`='".$primarydata['id']."' AND `pid`='$pid' AND `type`='$instype' ");
        }
        else if($instype == 'tertiary'){
            $update_meta = sqlStatement("UPDATE `tbl_patient_insurancedata_meta_data` SET `updated_date` =NOW(), `user`='".$_SESSION['authUser']."', `provider`='".$_SESSION['authUser']."',`plan_id`='".formData("i3plan_name-id")."',`payer_name`='".formData("i3providerid") ."', `payer_id` ='".formData("i3provider-id") ."', post_parent='".formData("i3provider_payerplan") ."' WHERE `insurance_id`='".$primarydata['id']."' AND `pid`='$pid' AND `type`='$instype' ");
        }
    }else{
        if($instype == 'primary'){
            $insert_meta = sqlStatement("INSERT INTO `tbl_patient_insurancedata_meta_data` (`insurance_id`, `pid`, `type`, `created_date`, `updated_date`, `user`, `provider`,`plan_id`,`payer_name`,`payer_id`,`post_parent` ) VALUES ( '".$primarydata['id']."', '$pid', '$instype', NOW(), NOW(), '".$_SESSION['authUser']."', '".$_SESSION['authUser']."','".formData("i1plan_name-id")."','".formData("i1providerid") ."','".formData("i1provider-id") ."','".formData("i1provider_payerplan") ."')");
        }
        else if($instype == 'secondary'){
            $insert_meta = sqlStatement("INSERT INTO `tbl_patient_insurancedata_meta_data` (`insurance_id`, `pid`, `type`, `created_date`, `updated_date`, `user`, `provider`,`plan_id`,`payer_name`,`payer_id`,`post_parent` ) VALUES ( '".$primarydata['id']."', '$pid', '$instype', NOW(), NOW(), '".$_SESSION['authUser']."', '".$_SESSION['authUser']."','".formData("i2plan_name-id")."','".formData("i2providerid") ."','".formData("i2provider-id") ."','".formData("i2provider_payerplan") ."')");
        }
        else if($instype == 'tertiary'){
            $insert_meta = sqlStatement("INSERT INTO `tbl_patient_insurancedata_meta_data` (`insurance_id`, `pid`, `type`, `created_date`, `updated_date`, `user`, `provider`,`plan_id`,`payer_name`,`payer_id`,`post_parent` ) VALUES ( '".$primarydata['id']."', '$pid', '$instype', NOW(), NOW(), '".$_SESSION['authUser']."', '".$_SESSION['authUser']."','".formData("i3plan_name-id")."','".formData("i3providerid") ."','".formData("i3provider-id") ."','".formData("i3provider_payerplan") ."')");
        }
    }
//
//
updateInsuranceLayoutData($pid,$primarydata,$instype);

}
function updateInsuranceLayoutData($pid,$primarydata,$type) {
    
    $pri_id = $primarydata['id'];
    
    $rez = sqlQuery("SELECT pid FROM insurance_data WHERE id = '$pri_id'");
    // Check for brain damage:
    if ($pid != $rez['pid']) {
      $errmsg = "Internal error: Attempt to change patient data with pid = '" .
        $rez['pid'] . "' when current pid is '$pid' for id '$pri_id'";
      die($errmsg);
    }
    $sql = "UPDATE insurance_data SET ";
    $sql2 = '';
    foreach ($primarydata as $key => $value) {
        /* hema */ 
        
        if($key!='id'){
             $sql2 .= " `$key` = " . pdValueOrNull($key, $value).","; 
        }  

        /* ============== */
      }
      $sql .= rtrim($sql2,",");
      $sql .= " WHERE id = '$pri_id' AND type='$type'";
      sqlStatement($sql);
}


if ($GLOBALS['concurrent_layout']) {
 include_once("demographics.php");
} else {
 include_once("patient_summary.php");
}
?>
