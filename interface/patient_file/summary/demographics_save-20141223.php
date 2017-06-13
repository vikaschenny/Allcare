<?php
include_once("../../globals.php");
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
updatePatientData($pid, $newdata['patient_data']);
updateEmployerData($pid, $newdata['employer_data']);

$i1dob = fixDate(formData("i1subscriber_DOB"));
$i1date = fixDate(formData("i1effective_date"), date('Y-m-d'));



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
  formData('i3accept_assignment'),
  formData('i3policy_type')
);


//  For edit group


//print_r($_SESSION['group_id_array']);


if(!empty($_SESSION['group_id_array']))
{    
      $gid=0;
    
      while($_SESSION['group_id_array'][$gid])
      {          
         $groupId=$_SESSION['group_id_array'][$gid];

	$queryGroupName="SELECT Grouping_Name FROM tbl_allcare_patients1to1_fieldmapping
                                          WHERE Grouping_ID=$groupId";

	  $getGroupName = sqlStatement($queryGroupName);                
          $rowGroupName = sqlFetchArray($getGroupName);

	//echo "<br>G Name = ".$rowGroupName['Grouping_Name'];
          
          // Get the POS of the group first
	$queryGroupPOS="SELECT DISTINCT POS_id FROM tbl_allcare_patients1to1_fieldmapping
                                    WHERE Grouping_ID=$groupId
				    AND POS_id IN (SELECT pos_id FROM tbl_allcare_patients1to1 WHERE pid =$pid)";
          $getGroupPOS = sqlStatement($queryGroupPOS);      
          
          $rowPOSId = sqlFetchArray($getGroupPOS);
          
          $this_pos_id=$rowPOSId['POS_id'];
          
          // Get the fields of the group
	$queryGroupFields="SELECT Field_Name FROM tbl_allcare_tablemeta
                           WHERE field_ID IN (SELECT Field_ID 
                                              FROM tbl_allcare_patients1to1_fieldmapping
                                              WHERE Grouping_ID=$groupId)";
          $getGroupFields = sqlStatement($queryGroupFields);      
          
          $field_number=0;
          
          while($rowGroupFields = sqlFetchArray($getGroupFields))
          {
              $field_name=$rowGroupFields['Field_Name'];
              $field_val=$_POST[$field_name];
           /*   
$updatePatientPOSGroupSql=sqlStatement("UPDATE tbl_allcare_patients1to1
				        SET $field_name=$field_val
				        WHERE pid=$pid 
				        AND pos_id=$this_pos_id
				        AND Group_ID=$groupId");
	*/
	
		 $queryUpdatePatientPOSGroup="UPDATE tbl_allcare_patients1to1
				        SET $field_name='$field_val'
				        WHERE pid=$pid AND pos_id=$this_pos_id";
		
		$updatePatientPOSGroupSql=sqlStatement($queryUpdatePatientPOSGroup);
              
              $field_number++;
          }                      
          
	$gid++;

      }                
}
unset($_SESSION['group_id_array']);




//  End edit group




// POS start
//saved POS data into database tables, after patient gets saved.


if($_POST['txtPOSid']!=0)
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
  }
        

//POS ends

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

// Facility ends here 

// Agency starts here
if($_POST['selectagency']!=0)
{ 
    
    $agencyid=$_POST['selectagency'];
    $admitdate=$_POST['orgadmitdate'];
    $dischargedate=$_POST['orgdischargedate'];
    $isactive=($_POST['chkagencyActive']!=1 ? 0 : 1);
    
    $agencyStatus=$_POST['hideagencyStatus'];
    $agencyformid=$_POST['hideagencyformid'];
    
    $notes=  addslashes($_POST['agencynotes']);
    $links= $_POST['agencydoclinks']; 
   
    if($agencyStatus=='edit')
    { 
       $agencySql="update tbl_patientagency set 
         agencyid=".$agencyid.",admitdate='".$admitdate."',dischargedate='".$dischargedate."',isactive=".$isactive.",
             updatedby='".$_SESSION['authUserID']."',updateddate='".date('Y-m-d')."',notes='".$notes."',related_links='".$links."'  
         where id=".$agencyformid;   
    }
    else
    {
        $agencySql = "insert into tbl_patientagency(patientid,agencyid,admitdate,dischargedate,isactive,createdby,createddate,notes,related_links) 
        values(".$pid.",".$agencyid.",'".$admitdate."','".$dischargedate."',".$isactive.",".$_SESSION['authUserID'].",'".date('Y-m-d')."','".$notes."','".$links."')";
    }
    //echo $agencySql;
    $insertagencyQry = mysql_query($agencySql);
    
    
    
}

// Agency ends here 


if ($GLOBALS['concurrent_layout']) {
 include_once("demographics.php");
} else {
 include_once("patient_summary.php");
}
?>
