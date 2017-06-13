<?php
// Copyright (C) 2009 Rod Roark <rod@sunsetsystems.com>
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.

require_once("../globals.php");
require_once("$srcdir/sql.inc");

// Validation for non-unique external patient identifier.
$alertmsg = '';
if (!empty($_POST["form_pubpid"])) {
  $form_pubpid = trim($_POST["form_pubpid"]);
  $result = sqlQuery("SELECT count(*) AS count FROM patient_data WHERE " .
    "pubpid = '$form_pubpid'");
  if ($result['count']) {
    // Error, not unique.
    $alertmsg = xl('Warning: Patient ID is not unique!');
  }
}

require_once("$srcdir/pid.inc"); 
require_once("$srcdir/patient.inc");
require_once("$srcdir/options.inc.php");

// here, we lock the patient data table while we find the most recent max PID
// other interfaces can still read the data during this lock, however
// sqlStatement("lock tables patient_data read");

$result = sqlQuery("SELECT MAX(pid)+1 AS pid FROM patient_data");

$newpid = 1;

if ($result['pid'] > 1) $newpid = $result['pid'];

setpid($newpid);

if (empty($pid)) {
  // sqlStatement("unlock tables");
  die("Internal error: setpid($newpid) failed!");
}

// Update patient_data and employer_data:
//
$newdata = array();
$newdata['patient_data' ] = array();
$newdata['employer_data'] = array();
$fres = sqlStatement("SELECT * FROM layout_options " .
  "WHERE form_id = 'DEM' AND uor > 0 AND field_id != '' " .
  "ORDER BY group_name, seq");
while ($frow = sqlFetchArray($fres)) {
  $data_type = $frow['data_type'];
  $field_id  = $frow['field_id'];
  // $value     = '';
  $colname   = $field_id;
  $tblname   = 'patient_data';
  if (strpos($field_id, 'em_') === 0) {
    $colname = substr($field_id, 3);
    $tblname = 'employer_data';
  }

  $value = get_layout_form_value($frow);

  if ($field_id == 'pubpid' && empty($value)) $value = $pid;
  $newdata[$tblname][$colname] = $value;
}
updatePatientData($pid, $newdata['patient_data'], true);
updateEmployerData($pid, $newdata['employer_data'], true);

$i1dob = fixDate(formData("i1subscriber_DOB"));
$i1date = fixDate(formData("i1effective_date"));

// sqlStatement("unlock tables");
// end table lock

newHistoryData($pid);
newInsuranceData(
  $pid,
  "primary",
  formData("i1provider"),
  formData("i1policy_number"),
  formData("i1group_number"),
  formData("i1plan_name"),
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
  formData('i1accept_assignment')
);


$i2dob = fixDate(formData("i2subscriber_DOB"));
$i2date = fixDate(formData("i2effective_date"));



newInsuranceData(
  $pid,
  "secondary",
  formData("i2provider"),
  formData("i2policy_number"),
  formData("i2group_number"),
  formData("i2plan_name"),
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
  formData('i2accept_assignment')
);

$i3dob  = fixDate(formData("i3subscriber_DOB"));
$i3date = fixDate(formData("i3effective_date"));

newInsuranceData(
  $pid,
  "tertiary",
  formData("i3provider"),
  formData("i3policy_number"),
  formData("i3group_number"),
  formData("i3plan_name"),
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
  formData('i3accept_assignment')
);

// save POS data after patient gets created

if($_POST['form_cb_pos']==1 && $_POST['txtPOSid']!=0)
{
    
 $Fields1to1Sql ="SELECT fg.id, fg.POS_id, fg.Grouping_ID, fg.Grouping_Name, fg.Table_ID, fg.Field_ID, pt.title, tb.Field_Name, tb.Table_Name
FROM tbl_allcare_tablemeta tb
INNER JOIN tbl_allcare_patients1to1_fieldmapping fg ON tb.Field_ID = fg.Field_ID
INNER JOIN tbl_pos_types pt ON pt.id = fg.POS_id and fg.POS_id=".$_POST['txtPOSid']."
group by fg.Grouping_ID,fg.Field_ID";

 $Fields1to1Qry =  sqlStatement($Fields1to1Sql); 
   if(sqlNumRows($Fields1to1Qry)>0)
   {
       while($Fields1to1Res = sqlFetchArray($Fields1to1Qry)) 
            {
                $field = $Fields1to1Res['Field_Name'];
                $fieldvalue=$_POST[$field];

            $totalColumn .= $Fields1to1Res['Field_Name'].",";     
            $fieldValues .= "'".$fieldvalue."',";
            }

            $totalColumn = substr($totalColumn,0,strlen($totalColumn)-1) ;
            $fieldValues = substr($fieldValues,0,strlen($fieldValues)-1) ;


              $fieldInsertSql1to1 ="insert into tbl_allcare_patients1to1(pid,pos_id,$totalColumn) values($pid,".$_POST['txtPOSid'].",$fieldValues)";   
            $result = sqlStatement($fieldInsertSql1to1);

   }

/*
$totalColumn='';
$fieldValues='';
$Fields1tonSql ="SELECT fg.id, fg.POS_id, fg.Recordset_ID, fg.Recordset_Name, fg.Table_ID, fg.Field_ID, pt.title, tb.Field_Name, tb.Table_Name
FROM tbl_allcare_tablemeta tb
INNER JOIN tbl_allcare_patients1ton_fieldmapping fg ON tb.Field_ID = fg.Field_ID
INNER JOIN tbl_pos_types pt ON pt.id = fg.POS_id and fg.POS_id=".$_POST['txtPOSid']."
group by fg.Recordset_ID,fg.Field_ID";

 $Fields1tonQry =  sqlStatement($Fields1tonSql); 
    if(sqlNumRows($Fields1tonQry)>0)
   {
        while($Fields1tonRes = sqlFetchArray($Fields1tonQry)) 
        {
            $field = $Fields1tonRes['Field_Name'];
            $fieldvalue=$_POST[$field];

        $totalColumn .= $Fields1tonRes['Field_Name'].",";     
        $fieldValues .= "'".$fieldvalue."',";
        }

        $totalColumn = substr($totalColumn,0,strlen($totalColumn)-1) ;
        $fieldValues = substr($fieldValues,0,strlen($fieldValues)-1) ;


        $fieldInsertSql1ton ="insert into tbl_allcare_patients1ton(pid,pos_id,$totalColumn) values($pid,".$_POST['txtPOSid'].",$fieldValues)";   
        $result = sqlStatement($fieldInsertSql1ton);

    }

 */
    //print_r($_POST['hiddenRecsetID']);die;
       foreach ($_POST['hiddenaddcount'] as $key => $value) 
       {
         //  print_r($_POST['hiddenaddcount']);
             //  echo "<br>";
        //   print_r($_POST[$key]);
           //   echo "<br>$value==".count($_POST[$key]);
           //  echo "<br>";
              
              $rowsvalues=count($_POST[$key])/$value;
              //$rowsvalues=$_POST['hiddenaddcount'][$key];
           //   echo "<br>".$rowsvalues;
              //print_r($_POST[$key]);
              //echo "<br>";
              $cnt3=0;
              for($cnt1=0;$cnt1<$value;$cnt1++)
                {
                  
                  $insertline='';
                    for($cnt2=0;$cnt2<$rowsvalues;$cnt2++)
                    {
                        
                          $insertline.=  "'".$_POST[$key][$cnt3]."'," ;
                          $cnt3++;
                    }
                    
                  
                      $insertline = substr($insertline,0,strlen($insertline)-1) ;
                  
                      $insertSql = "insert into tbl_allcare_patients1ton(pid,pos_id,Recordset_ID,".$_POST['hiddenrecid'][$key].") "
                            . "values ($pid,".$_POST['txtPOSid'].",$key,$insertline)" ;
                        //echo "<br>".$insertSql;
                $result = sqlStatement($insertSql);
                 }               
              
          
       }
       
    
    
    
    //echo "<pre>";print_r($_POST['hiddenrecid']);echo "</pre>";die;
    
  
}
        

// Facility starts here
if($_POST['selectFacility']!=0)
{
    $facilityid=$_POST['selectFacility'];
    $admitdate=$_POST['admitdate'];
    $dischargedate=$_POST['dischargedate'];
    $isactive=($_POST['chkFacilityActive']!=1 ? 0 : 1);
    
    $facilityStatus=$_POST['hideFacilityStatus'];
    $facilityformid=$_POST['hideFacilityformid'];
    
    $notes=  addslashes($_POST['facilitynotes']);
    $links= $_POST['facilitydoclinks'];
   
    if($facilityStatus=='edit')
    {
     $FacilitySql="update tbl_patientfacility set 
         facilityid=".$facilityid.",admitdate='".$admitdate."',dischargedate='".$dischargedate."',isactive=".$isactive.",
             updatedby='".$_SESSION['authUserID']."',updateddate='".date('Y-m-d')."',notes='".$notes."',related_links='".$links."'  
         where id=".$facilityformid;   
    }
    else
    {
     $FacilitySql = "insert into tbl_patientfacility(patientid,facilityid,admitdate,dischargedate,isactive,createdby,createddate,notes,related_links) 
        values(".$pid.",".$facilityid.",'".$admitdate."','".$dischargedate."',".$isactive.",".$_SESSION['authUserID'].",'".date('Y-m-d')."','".$notes."','".$links."')";
    }
    //echo $FacilitySql;
    $insertFacilityQry = mysql_query($FacilitySql);
    
    
    
}

// Agency starts here
if($_POST['selectagency']!=0)
{ 
    $agencyid=$_POST['selectagency'];
    $admitdate=$_POST['orgadmitdate'];
    $dischargedate=$_POST['orgdischargedate'];
    $isactive=($_POST['chkagencyActive']!=1 ? 0 : 1);
    $abookvalue = $_POST['selectabooktype'];
    $agencyStatus=$_POST['hideagencyStatus'];
    $agencyformid=$_POST['hideagencyformid'];
    
    $notes=  addslashes($_POST['agencynotes']);
    $links= $_POST['agencydoclinks'];
       
    if($agencyStatus=='edit')
    {
        $AgencySql="update tbl_patientagency set 
         agencyid=".$agencyid.",admitdate='".$admitdate."',dischargedate='".$dischargedate."',isactive=".$isactive.",agencyid=".$agencyid.",admitdate='".$admitdate."',abookvalue=".$abookvalue.",dischargedate='".$dischargedate."',isactive=".$isactive.",
             updatedby='".$_SESSION['authUserID']."',updateddate='".date('Y-m-d')."',notes='".$notes."',related_links='".$links."'  
         where id=".$agencyformid;   
    }
    else
    {
      $AgencySql = "insert into tbl_patientagency(patientid,agencyid,abookvalue, admitdate,dischargedate,isactive,createdby,createddate,notes,related_links) 
        values(".$pid.",".$agencyid.",".$abookvalue.",'".$admitdate."','".$dischargedate."',".$isactive.",".$_SESSION['authUserID'].",'".date('Y-m-d')."','".$notes."','".$links."')";
    }
    //echo $AgencySql;
    $insertAgencyQry = mysql_query($AgencySql);
    
    
    
}
?>
<html>
<body>
<script type='text/javascript' src='../main/js/jquery-1.11.1.min.js'></script>
<script language="Javascript">
<?php 
if ($alertmsg) { 
  echo "alert('$alertmsg');\n";
}
else{ 
    $protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === true ? 'https://' : 'https://';
    $sql = sqlStatement("select * from list_options where list_id='AllcareDriveSync' and option_id='email'");
    $row = sqlFetchArray($sql);

        $selection = sqlStatement("select * from tbl_drivesync_authentication where email='" . $row['notes'] . "' order by id desc");
        $sel_rows = sqlFetchArray($selection);
        if($sel_rows['patient_folder_trigger']=='yes'){
       // to get configured email
                 if($sel_rows['parent_folder']!='')
                 $parentid = str_replace('https://drive.google.com/drive/folders/', '', $sel_rows['parent_folder']);
                else
                 $parentid='root';   

                if($sel_rows['patient_folder']!='')
                {
                    $query = $sel_rows['patient_folder'] . " where pid=" . $newpid ;
                    $fsql = sqlStatement("$query");
                    $frow = sqlFetchArray($fsql);
                    $folder_name = str_replace(" ", "_", $frow['Patient_folder']);  
                }
                $curl = curl_init();
                $form_url2 = $protocol.$_SERVER['HTTP_HOST'].'/api/DriveSync/insert_folder_web/'.$row['notes'].'/'.$parentid.'/'.$folder_name;
                curl_setopt($curl,CURLOPT_URL, $form_url2);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);  
                $result = curl_exec($curl);
                $resultant = $result;
                curl_close($curl);
                $val= explode(':',$resultant);
                if($val[0]!=''){
                         $link='https://drive.google.com/drive/folders/'.$val[0];
                         $ins=sqlStatement("update patient_data SET patient_folder='$link' where pid=".$newpid);
                         $ins_log=sqlStatement("insert into DriveSync_log (date,user,email,encounter,patient_id,google_folder,file_name,file_id,status,watsID,category)values(now(),'".$_SESSION['authUser']."','".$row['notes']."','','".$newpid."','$link','','','folder_created(during patient Creation in EMR)','','patient')");
                    if($sel_rows['patient_sub_folder']=='yes'){
                        $subsql = sqlStatement("select * from list_options where list_id='AllcarePatientSubfolders'");
                        $link1='';
                        $srow=mysql_num_rows($subsql);
                        if($srow!=0){
                        while($subrow = sqlFetchArray($subsql)){
                            $curl = curl_init();
                            $form_url2 = $protocol.$_SERVER['HTTP_HOST'].'/api/DriveSync/insert_folder_web/'.$row['notes'].'/'.$val[0].'/'.str_replace(" ","_",$subrow['title']); 
                            curl_setopt($curl,CURLOPT_URL, $form_url2);
                            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);  
                            $result = curl_exec($curl);
                            $subresultant = $result;
                            curl_close($curl);
                            $subval= explode(':',$subresultant);
                            $link1.='https://drive.google.com/drive/folders/'.$subval[0]."||";
                        }
                         $ins_log=sqlStatement("insert into DriveSync_log (date,user,email,encounter,patient_id,google_folder,file_name,file_id,status,watsID,category)values(now(),'".$_SESSION['authUser']."','".$row['notes']."','','".$newpid."','$link1','','','subfolder_created(during patient Creation in EMR)','','patient')");
                        }
                    }
                         
                }
    
        }
    
}
if ($GLOBALS['concurrent_layout']) {
  echo "window.location='$rootdir/patient_file/summary/demographics.php?" .
    "set_pid=$pid&is_new=1';\n";
} else {
  echo "window.location='$rootdir/patient_file/patient_file.php?set_pid=$pid';\n";
}
?>
</script>

</body>
</html>

