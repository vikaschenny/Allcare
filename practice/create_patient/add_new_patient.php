<?php
 // Copyright (C) 2011 Cassian LUP <cassi.lup@gmail.com>
 // 
 // Moved out of individual get_* portal functions for re-use by
 // Kevin Yeh (kevin.y@integralemr.com) May 2013
 //
 // This program is free software; you can redistribute it and/or
 // modify it under the terms of the GNU General Public License
 // as published by the Free Software Foundation; either version 2
 // of the License, or (at your option) any later version.
 // 
 // 
 
    // All of the common intialization steps for the get_* patient portal functions are now in this single include.

    //SANITIZE ALL ESCAPES
    $sanitize_all_escapes=true; 

    //STOP FAKE REGISTER GLOBALS
    $fake_register_globals=false;

    //continue session
    session_start();

    //landing page definition -- where to go if something goes wrong
    $landingpage = "index.php?site=".$_SESSION['site_id'];	
    //

    // kick out if patient not authenticated
    //if ( isset($_SESSION['uid']) && isset($_SESSION['patient_portal_onsite']) ) {
    if ( isset($_SESSION['portal_username']) ) {    
    $provider = $_SESSION['portal_username'];
    }
    else {
            session_destroy();
    header('Location: '.$landingpage.'&w');
            exit;
    }
    //

    $ignoreAuth=true; // ignore the standard authentication for a regular OpenEMR user
include_once('../../interface/globals.php');
require_once("$srcdir/acl.inc");
require_once("$srcdir/options.inc.php");
require_once("$srcdir/patient.inc");
require_once("$srcdir/erx_javascript.inc.php");
require_once("$srcdir/sql.inc");

?>
<script type='text/javascript' src='../../interface/main/js/jquery-1.11.1.min.js'></script>
<?php
// Check authorization.
//if (!acl_check('patients','demo','',array('write','addonly') ))
//  die("Adding demographics is not authorized.");
$pagename = "plist"; 
$sql=sqlStatement("SELECT id, fname, lname, specialty FROM users " .
      "WHERE active = 1 AND ( info IS NULL OR info NOT LIKE '%Inactive%' ) " .
      "AND authorized = 1 AND username='".$provider."'" .
      "ORDER BY lname, fname");
$id=sqlFetchArray($sql);
$id1=$id['id'];

$base_url="https://".$_SERVER['SERVER_NAME'].dirname($_SERVER["REQUEST_URI"].'?').'/';
if($_POST['save']=='save_data'){
    
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

if ($alertmsg) { 
  echo "<script> alert('$alertmsg'); </script>";
}else 
{ 
     $sql = sqlStatement("select * from list_options where list_id='AllcareDriveSync' and option_id='email'");
        $row = sqlFetchArray($sql);

        $selection = sqlStatement("select * from tbl_drivesync_authentication where email='" . $row['notes'] . "' order by id desc");
        $sel_rows = sqlFetchArray($selection);
        if($sel_rows['patient_folder_trigger']=='yes'){
        ?>
        <script>
            alert('Patient Created Sucessfully');
            $('#dvLoading1').show();
            $.ajax({
                 type: 'POST',
                 url:' patient_folder_creation.php',
                 data:{pid:<?php echo $newpid; ?>},
                 success: function(response)
                 {   
                    if(response=='sucess'){
                      
                        $('#dvLoading1').hide();
                        alert("patient Folder SucessFully Created in Google Drive");
                         window.location.href = '../patient_data.php?id=all_patients';
                    }

                 },
                 error :function(response,status)
                 {
                  alert('error');

                 } 
            });
        </script>
        <?php
    
        }else {
               echo "<script> alert('Patient Created Sucessfully'); window.location.href = '../patient_data.php?id=all_patients';</script>";
        }
    
      //  echo "<script> alert('Patient Created Sucessfully'); window.location.href = '../patient_data.php?id=all_patients';</script>";
    
}
}


function generate_layout_validation_custom($form_id) {
  $fres = sqlStatement("SELECT * FROM layout_options " .
    "WHERE form_id = ? AND uor > 0 AND field_id != '' " .
    "ORDER BY group_name, seq", array($form_id) );

  while ($frow = sqlFetchArray($fres)) {
    if ($frow['uor'] < 2) continue;
    $data_type = $frow['data_type'];
    $field_id  = $frow['field_id'];
    $fldtitle  = $frow['title'];
    $group_name=$frow['group_name'];
   
    if (!$fldtitle) $fldtitle  = $frow['description'];
    $fldname   = htmlspecialchars( "form_$field_id", ENT_QUOTES);
    switch($data_type) {
      case  1:
      case 11:
      case 12:
      case 13:
      case 14:
      case 26:
      case 33:
        echo
        " if (f.$fldname.selectedIndex <= 0) {\n" .
        "  if (f.$fldname.focus) f.$fldname.focus();\n" .
        "  		errMsgs['$field_id'] = '" . htmlspecialchars( (xl_layout_label($fldtitle)), ENT_QUOTES) . "'; \n" .
        " }\n";
        break;
      case 27: // radio buttons
        echo
        " var i = 0;\n" .
        " for (; i < f.$fldname.length; ++i) if (f.$fldname[i].checked) break;\n" .
        " if (i >= f.$fldname.length) {\n" .
        "  		errMsgs['$field_id'] = '" . htmlspecialchars( (xl_layout_label($fldtitle)), ENT_QUOTES) . "'; \n" .
        " }\n";
        break;
      case  2:
      case  3:
      case  4:
      case 15:
        echo
        " if (trimlen(f.$fldname.value) == 0) {\n" .
        "  		if (f.$fldname.focus) f.$fldname.focus();\n" .
		"  		$('#" . $fldname . "').parents('div.tab').each( function(){ var tabHeader = $('#header_' + $(this).attr('id') ); tabHeader.css('color','red'); } ); " .
		"  		$('#" . $fldname . "').attr('style','background:red'); \n" .
        "  		errMsgs['$field_id'] = '" . htmlspecialchars( (xl_layout_label($fldtitle)), ENT_QUOTES) . "'; \n" .
        " } else { " .
		" 		$('#" . $fldname . "').attr('style',''); " .
		"  		$('#" . $fldname . "').parents('div.tab').each( function(){ var tabHeader = $('#header_' + $(this).attr('id') ); tabHeader.css('color','');  } ); " .
		" } \n";
        break;
    }
  }
}




$CPR = 4; // cells per row

$searchcolor = empty($GLOBALS['layout_search_color']) ?
  '#ffff55' : $GLOBALS['layout_search_color'];

 $WITH_SEARCH = ($GLOBALS['full_new_patient_form'] == '1' || $GLOBALS['full_new_patient_form'] == '2');
 $SHORT_FORM  = ($GLOBALS['full_new_patient_form'] == '2' || $GLOBALS['full_new_patient_form'] == '3');


function getLayoutRes() {
  global $SHORT_FORM;
  return sqlStatement("SELECT * FROM layout_options " .
    "WHERE form_id = 'DEM' AND uor > 0 AND field_id != '' " .
    ($SHORT_FORM ? "AND ( uor > 1 OR edit_options LIKE '%N%' ) " : "") .
    "ORDER BY group_name, seq");
}




// Determine layout field search treatment from its data type:
// 1 = text field
// 2 = select list
// 0 = not searchable
//
function getSearchClass($data_type) {
  switch($data_type) {
    case  1: // single-selection list
    case 10: // local provider list
    case 11: // provider list
    case 12: // pharmacy list
    case 13: // squads
    case 14: // address book list
    case 26: // single-selection list with add
    case 35: // facilities
      return 2;
    case  2: // text field
    case  3: // textarea
    case  4: // date
      return 1;
  }
  return 0;
}

$fres = getLayoutRes();


?>
<html>
<head>
<?php html_header_show(); ?>

<!--<link rel="stylesheet" href="<?php echo $css_header; ?>" type="text/css">-->
 <meta charset="UTF-8">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <link href='https://fonts.googleapis.com/css?family=Abel' rel='stylesheet' type='text/css'>
	    <link href='https://fonts.googleapis.com/css?family=Roboto:400,300,500' rel='stylesheet' type='text/css'>
	    <link href='https://fonts.googleapis.com/css?family=Dosis:300,400,500,600' rel='stylesheet' type='text/css'>
            <link rel="stylesheet" type="text/css" href="../assets/css/animate.css">
            <link rel="stylesheet" type="text/css" href="../assets/css/bootstrap.min.css">
            <link rel="stylesheet" type="text/css" href="../assets/css/owl.carousel.css">
            <link rel="stylesheet" type="text/css" href="../assets/css/owl.theme.css">
            <link rel="stylesheet" type="text/css" href="../assets/css/owl.transitions.css">
            <link rel="stylesheet" type="text/css" href="../assets/css/font-awesome.min.css">
            <link rel="stylesheet" type="text/css" href="../assets/css/main.css">
            <link rel="stylesheet" type="text/css" href="../css/scollypay.css">
            <link rel="stylesheet" type="text/css" href="../assets/css/customize.css">
            <link href='https://fonts.googleapis.com/css?family=Roboto+Condensed:400,300' rel='stylesheet' type='text/css'>
            
            <script type="text/javascript" src="../assets/js/bootstrap.min.js"></script>
<style>
body, td, input, select, textarea {
 font-family: Arial, Helvetica, sans-serif;
 font-size: 10pt;
}



div.section {
 border: solid;
 border-width: 1px;
 border-color: #0000ff;
 padding: 5pt;
}

.fontbold{
    font-weight:bold;
}
.bs-docs-sidenav .active a:hover {
        background-color: #4ac2dc;
    }
    #sidenave .active {
       background-color: #4ac2dc;
       cursor: default;
    }

     #sidenave li:last-child .active {
        background-color: #4ac2dc;
        border-radius: 0 0 6px 6px;
        cursor: default;
    }

    #sidenave li:first-child .active {
        background-color: #4ac2dc;
        border-radius: 0 0 6px 6px;
        cursor: default;
    }

    #sidenave .active a {
        color:#fff !important;
        font-weight:bold;
        text-decoration: none;
    }
    #content table ul li{
       display: block;
    }
    .bs-docs-sidenav.affix {
        top: 94px;
    }
    .bs-docs-sidenav > li:first-child > a {
        border-radius: 6px 6px 0 0;
    }
    #content {
        padding-bottom: 16px;
        overflow-x: visible;
        overflow-y: hidden;
    }
   .bs-docs-sidenav > li:first-child > a {
        border-radius: 6px 6px 0 0;
    }
    .ui-menu .ui-menu-item{
        display: block;
    }
</style>

<style type="text/css">@import url(../../library/dynarch_calendar.css);</style>
<link rel="stylesheet"  type="text/css" href="../../library/popover/css/jquery.webui-popover.min.css"/>
<script type="text/javascript" src="../../library/dialog.js"></script>
<script type="text/javascript" src="../../library/textformat.js"></script>
<script type="text/javascript" src="../../library/dynarch_calendar.js"></script>
<?php include_once("{$GLOBALS['srcdir']}/dynarch_calendar_en.inc.php"); ?>
<script type="text/javascript" src="../../library/dynarch_calendar_setup.js"></script>
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.0/jquery-ui.js"></script>
<script src="../../library/popover/js/jquery.webui-popover.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<!--<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/jquery.js"></script>-->


<!--<link rel="stylesheet" href="../../library/js/jquery.treeview-1.4.1/jquery.treeview.css" />-->
<!--<script src="../../library/js/jquery-1.6.4.min.js" type="text/javascript"></script>
<script src="../../library/js/jquery.treeview-1.4.1/jquery.treeview.js" type="text/javascript"></script>-->


<SCRIPT LANGUAGE="JavaScript"><!--
//Visolve - sync the radio buttons - Start
//if((top.window.parent) && (parent.window)){
//        var wname = top.window.parent.left_nav;
//        fname = (parent.window.name)?parent.window.name:window.name;
//        wname.syncRadios();
//        wname.setRadio(fname, "new");
//}
//Visolve - sync the radio buttons - End

var mypcc = '<?php echo $GLOBALS['phone_country_code'] ?>';

// This may be changed to true by the AJAX search script.
var force_submit = false;

//code used from https://tech.irt.org/articles/js037/
function replace(string,text,by) {
 // Replaces text with by in string
 var strLength = string.length, txtLength = text.length;
 if ((strLength == 0) || (txtLength == 0)) return string;

 var i = string.indexOf(text);
 if ((!i) && (text != string.substring(0,txtLength))) return string;
 if (i == -1) return string;

 var newstr = string.substring(0,i) + by;

 if (i+txtLength < strLength)
  newstr += replace(string.substring(i+txtLength,strLength),text,by);

 return newstr;
}

<?php for ($i=1;$i<=3;$i++) { ?>
function auto_populate_employer_address<?php echo $i ?>(){
 var f = document.demographics_form;
 if (f.form_i<?php echo $i?>subscriber_relationship.options[f.form_i<?php echo $i?>subscriber_relationship.selectedIndex].value == "self") {
  f.i<?php echo $i?>subscriber_fname.value=f.form_fname.value;
  f.i<?php echo $i?>subscriber_mname.value=f.form_mname.value;
  f.i<?php echo $i?>subscriber_lname.value=f.form_lname.value;
  f.i<?php echo $i?>subscriber_street.value=f.form_street.value;
  f.i<?php echo $i?>subscriber_city.value=f.form_city.value;
  f.form_i<?php echo $i?>subscriber_state.value=f.form_state.value;
  f.i<?php echo $i?>subscriber_postal_code.value=f.form_postal_code.value;
  if (f.form_country_code)
    f.form_i<?php echo $i?>subscriber_country.value=f.form_country_code.value;
  f.i<?php echo $i?>subscriber_phone.value=f.form_phone_home.value;
  f.i<?php echo $i?>subscriber_DOB.value=f.form_DOB.value;
  f.i<?php echo $i?>subscriber_ss.value=f.form_ss.value;
  f.form_i<?php echo $i?>subscriber_sex.value = f.form_sex.value;
  f.i<?php echo $i?>subscriber_employer.value=f.form_em_name.value;
  f.i<?php echo $i?>subscriber_employer_street.value=f.form_em_street.value;
  f.i<?php echo $i?>subscriber_employer_city.value=f.form_em_city.value;
  f.form_i<?php echo $i?>subscriber_employer_state.value=f.form_em_state.value;
  f.i<?php echo $i?>subscriber_employer_postal_code.value=f.form_em_postal_code.value;
  if (f.form_em_country)
    f.form_i<?php echo $i?>subscriber_employer_country.value=f.form_em_country.value;
 }
}

<?php } ?>

function upperFirst(string,text) {
 return replace(string,text,text.charAt(0).toUpperCase() + text.substring(1,text.length));
}

// The ins_search.php window calls this to set the selected insurance.
function set_insurance(ins_id, ins_name) {
 var thesel = document.forms[0]['i' + insurance_index + 'provider'];
 var theopts = thesel.options; // the array of Option objects
 var i = 0;
 for (; i < theopts.length; ++i) {
  if (theopts[i].value == ins_id) {
   theopts[i].selected = true;
   return;
  }
 }
 // no matching option was found so create one, append it to the
 // end of the list, and select it.
 theopts[i] = new Option(ins_name, ins_id, false, true);
}

// Indicates which insurance slot is being updated.
var insurance_index = 0;

// The OnClick handler for searching/adding the insurance company.
function ins_search(ins) {
 insurance_index = ins;
 window.open('../../interface/practice/ins_search.php', '_blank', 550, 400);
 return false;
}

function checkNum () {
 var re= new RegExp();
 re = /^\d*\.?\d*$/;
 str=document.forms[0].monthly_income.value;
 if(re.exec(str))
 {
 }else{
  alert("Please enter a dollar amount using only numbers and a decimal point.");
 }
}

// This capitalizes the first letter of each word in the passed input
// element.  It also strips out extraneous spaces.
function capitalizeMe(elem) {
 var a = elem.value.split(' ');
 var s = '';
 for(var i = 0; i < a.length; ++i) {
  if (a[i].length > 0) {
   if (s.length > 0) s += ' ';
   s += a[i].charAt(0).toUpperCase() + a[i].substring(1);
  }
 }
 elem.value = s;
}

// Onkeyup handler for policy number.  Allows only A-Z and 0-9.
function policykeyup(e) {
 var v = e.value.toUpperCase();
 for (var i = 0; i < v.length; ++i) {
  var c = v.charAt(i);
  if (c >= '0' && c <= '9') continue;
  if (c >= 'A' && c <= 'Z') continue;
  if (c == '*') continue;
  if (c == '-') continue;
  if (c == '_') continue;
  if (c == '(') continue;
  if (c == ')') continue;
  if (c == '#') continue;
  v = v.substring(0, i) + v.substring(i + i);
  --i;
 }
 e.value = v;
 return;
}

function divclick(cb, divid) {

 var divstyle = document.getElementById(divid).style;
 if (cb.checked) {
  divstyle.display = 'block';
 } else {
  divstyle.display = 'none';
 }
 return true;
}

// Compute the length of a string without leading and trailing spaces.
function trimlen(s) {
 var i = 0;
 var j = s.length - 1;
 for (; i <= j && s.charAt(i) == ' '; ++i);
 for (; i <= j && s.charAt(j) == ' '; --j);
 if (i > j) return 0;
 return j + 1 - i;
}

function validate(f) {
    
//alert('entered');
  
 var errCount = 0;
 var errMsgs = new Array();
// alert(errMsgs);
<?php generate_layout_validation_custom('DEM'); ?>
 

 var msg = "";
 msg += "<?php echo htmlspecialchars( xl('The following fields are required'), ENT_QUOTES); ?>:\n\n";
// for ( var i = 0; i < errMsgs.length; i++ ) {
//     
//	msg += errMsgs[i] + "\n";
//       
// }
var arr_length=0;
 for (var key in errMsgs) {
     
     jQuery.ajax({
        type: 'POST',
        url: "group.php",
        dataType : "json",
        data: {
                key : key
            },

        success: function(data)
        {
            var stringified = '';
//            jQuery('#patient_dropdown').empty();
//            jQuery('#patient_dropdown').append(jQuery('<option>', { 
//                    value: '',
//                    text : 'Select'
//                }));
            stringified = JSON.stringify(data, undefined, 2);
            var objectified = jQuery.parseJSON(stringified);
            for(var key1 in objectified ){
               // alert(key1+"="+objectified[key1]);
                var res = objectified[key1].substring(0, 1);
                var divid="div_"+res;
                var divstyle = document.getElementById(divid).style;
               // alert(divstyle.display);
                if(divstyle.display=='none'){
                    divstyle.display = 'block';
                    document.getElementById("form_cb_"+res).checked = true;
                }
            }
        },
        failure: function(response)
        {
            alert("error");
        }  
    });
    // alert(key);
   var value = errMsgs[key];
   msg += value + "\n";
   arr_length++;
}
 msg += "\n<?php echo htmlspecialchars( xl('Please fill them in before continuing.'), ENT_QUOTES); ?>";
//alert(arr_length);
 if ( arr_length > 0 ) {
	alert(msg);
 }
 
 if(arr_length==0){
    return true;
 }else {
      return arr_length < 1;
 }


}

function toggleSearch(elem) {
 var f = document.forms[0];
 
<?php if ($WITH_SEARCH) { ?>
 // Toggle background color.

 if (elem.style.backgroundColor == ''){
  elem.style.backgroundColor = '<?php echo $searchcolor; ?>'; 
  }else{
  elem.style.backgroundColor = '';
  }

<?php } ?>
 if (force_submit) {
  force_submit = false;
  f.create.value = '<?php xl('Create New Patient','e'); ?>';
 }
 return true;
}


// If a <select> list is dropped down, this is its name.
var open_sel_name = '';

function selClick(elem) { 
 if (open_sel_name == elem.name) {
  open_sel_name = '';
 }
 else {
  open_sel_name = elem.name;
  toggleSearch(elem);
 }
 return true;
}

function selBlur(elem) {
 if (open_sel_name == elem.name) {
  open_sel_name = '';
 }
 return true;
}
//  Based on POS selection POS fields and recordsets will be displayed
function showMapping(POSid)
{
    $("#divResponse").html('');
    //alert(POSid);
    if(POSid!=0)
    {
        var AjxURL=document.URL.split("/new.php")[0]+'/show_mapping.php?POSid='+POSid; 
        //alert(document.URL);

        $.ajax({
                     type: 'POST',
                     url: AjxURL,

                     success: function(response)
                     { 
                        //alert('success'+response);
                         $("#divResponse").html(response);
                        //      return true;
                     },
                     error :function(response,status)
                     {
                      alert('error');
                      //return false;
                     } 
                    });
    }
}
//// This invokes the patient search dialog.
function searchme() {
 var f = document.forms[0];
 var url = 'patient_select.php?popup=1';

<?php
$lres = getLayoutRes();

while ($lrow = sqlFetchArray($lres)) {
  $field_id  = $lrow['field_id'];
  if (strpos($field_id, 'em_') === 0) continue;
  $data_type = $lrow['data_type'];
  $fldname = "form_$field_id";
  switch(getSearchClass($data_type)) {
    case  1:
      echo
      " if (f.$fldname.style.backgroundColor != '' && trimlen(f.$fldname.value) > 0) {\n" .
      "  url += '&$field_id=' + encodeURIComponent(f.$fldname.value);\n" .
      " }\n";
      break;
    case 2:
      echo
      " if (f.$fldname.style.backgroundColor != '' && f.$fldname.selectedIndex > 0) {\n" .
      "  url += '&$field_id=' + encodeURIComponent(f.$fldname.options[f.$fldname.selectedIndex].value);\n" .
      " }\n";
      break;
  }
}
?>

 window.open(url, '_blank', 700, 500);
}






//-->
 function dropdownchange(val){
                var f = document.forms['patients_filters'];
                f.submit();
          }                       
       
        function DoPost(page_name, provider) {
                    method = "post"; // Set method to post by default if not specified.
                    var form = document.createElement("form");
                    form.setAttribute("method", method);
                    form.setAttribute("action", page_name);
                    var key='provider';
                    var hiddenField = document.createElement("input");
                    hiddenField.setAttribute("type", "hidden");
                    hiddenField.setAttribute("name", key);
                    hiddenField.setAttribute("value", provider);

                    form.appendChild(hiddenField);
                    document.body.appendChild(form);
                    form.submit();
                } 
                
                $(function () {
                    setNavigation();
                });
                function setNavigation() {                    
                     $('#sidenave li').eq(6).addClass('active');
                    $('#sidenave li').eq(6).find('a').removeAttr("href");
                }
function DoPost_patient(url) {
    var res = url.split("?");
    var param=res[1].split("&");
    method = "post"; // Set method to post by default if not specified.
    var form = document.createElement("form");
        form.setAttribute("method", method);
        form.setAttribute("action", res[0]);
    for(var i=0; i<param.length; i++){
        var param1=param[i].split("=");
        var hiddenField = document.createElement("input");
        hiddenField.setAttribute("type", "hidden");
        hiddenField.setAttribute("name", param1[0]);
        hiddenField.setAttribute("value", param1[1]);
        form.appendChild(hiddenField);
    }
      document.body.appendChild(form);
      form.submit();
}
</script>

  <script>
  $( function() {
          <?php for($i = 1; $i <= 3; $i++){ ?>
                    $( "#i<?php echo $i; ?>providerid" ).autocomplete({
                        minLength: 0,
                        source: function(request, response) {
                            if(request.term !== ''){
                                $.post("../patient_file/summary/search_payerplan.php", {searchit :$( "#i<?php echo $i; ?>providerid" ).val()}, function(data){
                                    if(data){
                                        var projects = JSON.parse(data);
                                        response(projects.returndata); 
                                        $( "#i<?php echo $i; ?>plan_name"  ).removeAttr("disabled"); 
                                    }
                                });
                            }
                            if(request.term == '')
                                    $( "#i<?php echo $i; ?>plan_name"  ).attr("disabled", "disabled"); 
                        },
                        focus: function( event, ui ) {
                          $( "#i<?php echo $i; ?>providerid"  ).val( ui.item.label );
                          return false;
                        },
                        select: function( event, ui ) {
                          $( "#i<?php echo $i; ?>providerid"  ).val( ui.item.label );
                          $( "#i<?php echo $i; ?>provider"  ).val( ui.item.value );
                          $( "#i<?php echo $i; ?>provider_payerplan"  ).val( ui.item.payerID );
                          $( "#i<?php echo $i; ?>provider-id" ).val( ui.item.zirmed_payer_id );
                          return false;
                        }
                      })
                      .autocomplete( "instance" )._renderItem = function( ul, item ) {
                        return $( "<li>" )
                          .append(  "<div>" + item.label + "<i><span style='font-size:8pt;'>" + item.desc + "</span></i></div>"  )
                          .appendTo( ul );
                    };
                    // plan auto complete
                    $.widget( "custom.plancombobox", {
                        _create: function() {
                          this.wrapper = $( "<span>" )
                            .addClass( "custom-combobox" )
                            .insertAfter( this.element );

                          this.element.hide();
                          this._createAutocomplete();
                          this._createShowAllButton();
                        },

                        _createAutocomplete: function() {
                          var selected = this.element.children( ":selected" ),
                            value = selected.val() ? selected.text() : "";
                          this.input = $( "<input>" )
                            .appendTo( this.wrapper )
                            .val( value )
                            .attr( "title", "" )
                            .addClass( "custom-combobox-input ui-widget ui-widget-content ui-state-default ui-corner-left" )
                            .autocomplete({
                              delay: 0,
                              minLength: 0,
                              source: $.proxy( this, "_source" )
                            })
                            .tooltip({
                              classes: {
                                "ui-tooltip": "ui-state-highlight"
                              }
                            });
                          this._on( this.input, {
                            autocompleteselect: function( event, ui ) {
                             //console.log(JSON.stringify(ui.item));
                             ui.item.option.selected = true;
                             $('#i<?php echo $i; ?>plan_name-id').val(ui.item.option.value);
                              this._trigger( "select", event, {
                                item: ui.item.option
                              });
                            },

                           
                          });
                        },

                        _createShowAllButton: function() {
                          var input = this.input,
                            wasOpen = false;

                          $( "<a>" )
                            .attr( "tabIndex", -1 )
                            .attr( "title", "Show All Items" )
                            .tooltip()
                            .appendTo( this.wrapper )
                            .button({
                              icons: {
                                primary: "ui-icon-triangle-1-s"
                              },
                              text: false
                            })
                            .removeClass( "ui-corner-all" )
                            .addClass( "custom-combobox-toggle ui-corner-right" )
                            .on( "mousedown", function() {
                              wasOpen = input.autocomplete( "widget" ).is( ":visible" );
                            })
                            .on( "click", function() {
                              input.trigger( "focus" );

                              // Close if already visible
                              if ( wasOpen ) {
                                return;
                              }

                              // Pass empty string as value to search for, displaying all results
                              input.autocomplete( "search", "" );
                            });
                        },
                        select: function (event, ui) {
                            /*ui.item.option.selected = true;
                            self._trigger("selected", event, {
                                item: ui.item.option
                            });
                            select.trigger("change");*/
                        },

                        _source: function( request, response ) {
                            var element = this.element;
                          $.post("../patient_file/summary/get_payerplan_plans_search.php", {searchit :request.term,post_parent:$( "#i<?php echo $i; ?>provider_payerplan" ).val()}, function(data){
                                if(data){
                                    var projects2 = JSON.parse(data);
                                    console.log(projects2.returndata);
                                    var elementoptions = "";
                                    $.each(projects2.returndata,function(i,v){
                                       elementoptions += '<option value="'+v.value+'">'+v.label+'</option>';
                                    })
                                     element.html(elementoptions);
                                    response(element.children("option").map(function () {
                                        var text = $(this).text();
//                                        alert(text);
                                        return {
                                            label: text,
                                            value: text,
                                            option: this
                                        };
                                    }));
            
                                }
                            });       
                        }
                      });
                       $( "#i<?php echo $i; ?>plan_name" ).plancombobox({ 
                            select: function (event, ui) { 
                                    //alert($(this).attr('id'));
                              $(this).next('.custom-combobox').find("input").attr("value",ui.item.label);
                              $("#i<?php echo $i?>plan_name_label").attr("value",ui.item.label);
                              
                              
                            }
                        });  

        <?php } ?>
         $('a.card').webuiPopover('destroy').webuiPopover({trigger:'click',title:'Insurance Card',padding:0,animation:'pop',closeable:true,placement:'right-bottom'});
         $('.card').click(function(){
            $(this).next().hide();
        })      
        
        <?php for($i=1; $i<4; $i++){ ?>
            $('a.plan_benefits<?php echo $i; ?>').webuiPopover('destroy').webuiPopover({type:'iframe',url:'../../main/allcarereports/get_plan_benefits.php?plan_id=0',title:'Plan Benefits',padding:0,animation:'pop',closeable:true,width:600, height:300});       
            $('a.plan_benefits<?php echo $i; ?>').click(function(event){
                var plan_id_value = $("#i<?php echo $i; ?>plan_name-id").val();
                var url = '../main/allcarereports/get_plan_benefits.php?plan_id='; 
                var res = url.concat(plan_id_value).concat("&payer_id="+$("#i<?php echo $i?>provider").val());
                
//                console.log(plan_id_value);
                $('a.plan_benefits<?php echo $i; ?>').webuiPopover('destroy').webuiPopover({type:'iframe',url:res,title:'Plan Benefits',padding:0,animation:'pop',closeable:true,width:600, height:300});
                $('a.plan_benefits<?php echo $i; ?>').webuiPopover('show'); 
            })
        <?php } ?>
        $('#demo_save_button').click(function() {
            window.parent.closeModalWindow();
        });
        $("#backimage").on('submit',(function(e){
            e.preventDefault();
            $.ajax({
                url: "../../main/allcarereports/saveimage.php",
                type: "POST",
                data:  new FormData(this),
                contentType: false,
                cache: false,
                processData:false,
                success: function(data){
                $("#backimagediv").html(data);
                },
                error: function(){} 	        
            });
        }));
        $("#frontimage").on('submit',(function(e){
            e.preventDefault();
            $.ajax({
                url: "../../main/allcarereports/saveimage.php",
                type: "POST",
                data:  new FormData(this),
                contentType: false,
                cache: false,
                processData:false,
                success: function(data){
                $("#frontimagediv").html(data);
                },
                error: function(){} 	        
            });
        }))
  } );
    

  </script>
  <style>
    .bodyclass {
        padding-top: 0px;
    }   
        
  .project-description {
    margin: 0;
    padding: 0;
    font-size:10px
  }
  </style>
  <style>
  .custom-combobox {
    position: relative;
    display: inline-block;
    height: 20px; 
  }
  .custom-combobox-toggle {
    position: absolute;
    top: 0;
    bottom: 0;
    margin-left: -1px;
    padding: 0;
    height: 20px; 
    
  }
  .custom-combobox-input {
    margin: 0;
    padding: 5px 10px;
    height: 20px; 
    width: 190px;
  }
  .zirmedsmallsize{
      font-size: 10px;
  }
  #newdemographics.modal-body{
      padding: 0px !important;
  }
  
  </style>
</head>

<body class='bodyclass'><div id="dvLoading1" style="display:none; "></div>
<form action='add_new_patient.php' name='demographics_form' method='post' onsubmit='return validate(this)'>
<input type="hidden" name="save" id="save" value="save_data" />
<table width='100%' cellpadding='0' cellspacing='8'>
 <tr>
  <td align='left' valign='top'>
<?php if ($SHORT_FORM) echo "  <center>\n"; ?>
<?php

function end_cell() {
  global $item_count, $cell_count;
  if ($item_count > 0) {
    echo "</td>";
    $item_count = 0;
  }
}

function end_row() {
  global $cell_count, $CPR;
  end_cell();
  if ($cell_count > 0) {
    for (; $cell_count < $CPR; ++$cell_count) echo "<td></td>";
    echo "</tr>\n";
    $cell_count = 0;
  }
}

function end_group() {
  global $last_group, $SHORT_FORM;
  if (strlen($last_group) > 0) {
    end_row();
    echo " </table>\n";
    if (!$SHORT_FORM) echo "</div>\n";
  }
}

$last_group    = '';
$cell_count    = 0;
$item_count    = 0;
$display_style = 'block';
$group_seq     = 0; // this gives the DIV blocks unique IDs



while ($frow = sqlFetchArray($fres)) {
    
  $this_group = $frow['group_name'];
  $titlecols  = $frow['titlecols'];
  $datacols   = $frow['datacols'];
  $data_type  = $frow['data_type'];
  $field_id   = $frow['field_id'];
  $list_id    = $frow['list_id'];
  $currvalue  = '';


 
 
  
  if (strpos($field_id, 'em_') === 0) {
    $tmp = substr($field_id, 3);
    if (isset($result2[$tmp])) $currvalue = $result2[$tmp];
  }
  else {
    if (isset($result[$field_id])) $currvalue = $result[$field_id];
  }

  // Handle a data category (group) change.
  if (strcmp($this_group, $last_group) != 0) {
     
       
    if (!$SHORT_FORM) {
        
      end_group();
      $group_seq++;    // ID for DIV tags
      $group_name = substr($this_group, 1);
      if (strlen($last_group) > 0) echo "<br />";
      echo "<span class='bold'><input type='checkbox' name='form_cb_$group_seq' id='form_cb_$group_seq' value='1' " .
        "onclick='return divclick(this,\"div_$group_seq\");'";
      if ($display_style == 'block') echo " checked";
        
      // Modified 6-09 by BM - Translate if applicable  
      echo " /><b>" . xl_layout_label($group_name) . "</b></span>\n";
        
      echo "<div id='div_$group_seq' class='section' style='display:$display_style;'>\n";
        if($field_id=="POSid")
       {
           
       }
      
      
      echo " <table border='0' cellpadding='0'>\n";
      $display_style = 'none';
    }
    else if (strlen($last_group) == 0) {
      echo " <table border='0' cellpadding='0'>\n";
    }
    
    $last_group = $this_group;
    
  }

  // Handle starting of a new row.
  if (($titlecols > 0 && $cell_count >= $CPR) || $cell_count == 0) {
    end_row();
    echo "  <tr>";
  }

  if ($item_count == 0 && $titlecols == 0) $titlecols = 1;

  // Handle starting of a new label cell.
  if ($titlecols > 0) {
    end_cell();
    echo "<td colspan='$titlecols'";
    echo ($frow['uor'] == 2) ? " class='required'" : " class='bold'";
    if ($cell_count == 2) echo " style='padding-left:10pt'";
    echo ">";
    $cell_count += $titlecols;
  }
  ++$item_count;

  echo "<b>";
    
  // Modified 6-09 by BM - Translate if applicable  
  if($field_id!="POSid")
  {
  if ($frow['title']) echo (xl_layout_label($frow['title']).":"); else echo "&nbsp;";
  }
  echo "</b>";

  // Handle starting of a new data cell.
  if ($datacols > 0) {
    end_cell();
    echo "<td colspan='$datacols' class='text'";
    if ($cell_count > 0) echo " style='padding-left:5pt'";
    echo ">";
    $cell_count += $datacols;
  }

  ++$item_count;
  generate_form_field($frow, $currvalue);
}

end_group();
?>

<?php
if (! $GLOBALS['simplified_demographics']) {
  $insurancei = getInsuranceProviders();
  $pid = 0;
  $insurance_headings = array(xl("Primary Insurance Provider"), xl("Secondary Insurance Provider"), xl("Tertiary Insurance provider"));
  $insurance_info = array();
  $insurance_info[1] = getInsuranceData($pid,"primary");
  $insurance_info[2] = getInsuranceData($pid,"secondary");
  $insurance_info[3] = getInsuranceData($pid,"tertiary");

  echo "<br /><span class='bold'><input type='checkbox' name='form_cb_ins' value='1' " .
    "onclick='return divclick(this,\"div_ins\");'";
  if ($display_style == 'block') echo " checked";
  echo " /><b>" . xl('Insurance') . "</b></span>\n";
  echo "<div id='div_ins' class='section' style='display:$display_style;'>\n";

  for($i=1;$i<=3;$i++) {
   $result3 = $insurance_info[$i];
?>
<table border="0">
  <tr>
    <td>
     
    <!--<td class='required'>:</td>-->
        <tr>
            
            <td><span class='required'><?php echo $insurance_headings[$i -1]."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"?></span> 
                <?php 
                  $payer_name = '';
                  foreach ($insurancei as $iid => $iname) {
                         if (strtolower($iid) == strtolower($result3{"provider"}))
                           $payer_name = $iname ;
                  }

                   if($i == 1)
                      $new_ins_type = 'primary';
                   else if($i == 2)
                      $new_ins_type = 'secondary';
                   else if($i == 3)
                      $new_ins_type = 'teritary';




                   $get_image = sqlStatement("SELECT frontimage, backimage,plan_id,payer_id,post_parent FROM tbl_patient_insurancedata_meta_data WHERE pid='$pid'  and `type` = '$new_ins_type'");
                   $set_image = sqlFetchArray($get_image);


                ?>
                <input name="i<?php echo $i?>providerid" id="i<?php echo $i?>providerid" value='<?php echo $payer_name ; ?>' size="50">
              <input type="hidden" name="i<?php echo $i?>provider" id="i<?php echo $i?>provider" value='<?php echo $result3{"provider"} ; ?>'>
              <input type="hidden" id="i<?php echo $i?>provider-id"  name="i<?php echo $i?>provider-id" value='<?php echo $set_image['payer_id']; ?>'>
              <input type='hidden' name='i<?php echo $i?>provider_payerplan' id='i<?php echo $i?>provider_payerplan' value='<?php echo $set_image['post_parent']; ?>'>
              <p id="project-description" class='project-description' ></p>
              <input type='hidden' name="i<?php echo $i?>provider_change" id="i<?php echo $i?>provider_change" value='<?php echo $payer_name; ?>'>
            </td>
        </tr>
    </td>
   </tr>
 <tr>
  <td valign=top>
   <table border="0">

    <tr>
        <td><span class=bold></span></td>
       <!--<td></td>-->
        <td colspan="2">
        <?php 

//                             echo $set_image['frontimage'];
//                             echo "<pre>"; print_r($set_image); echo "</pre>";
        echo '<a class="card" href="#" style="font-size:12px;">Card Front Image</a>
            <div class="webui-popover-content" id="frontimagediv">';
         if($set_image['frontimage'] == '')
            echo '<image src="../../images/No_Image_Available.png" alt="No image icon"  width="290" height="200"> ';
         else   
            echo ' <img src="data:image/jpeg;base64,' . $set_image['frontimage'] . '" width="290" height="200">';

          echo '  </div>';
//          echo '| <a class="uploadfront" href="#" style="font-size:12px; ">Upload Card Front Image</a>
//            <div class="webui-popover-content">';
//          echo '<form action="saveimage.php" method="post" id="frontimage" enctype="multipart/form-data">
//               <p style="font-size:12px;">Select image to upload:
//               <input type="file" name="fileToUpload" id="fileToUpload">
//               <input type="submit" value="Upload Image" name="submit">
//               </p>
//           </form>
//           ';
//          echo '  </div>';
               echo '<br/>';
        echo '<a class="card" href="#" style="font-size:12px;">Card Back Image</a>
            <div class="webui-popover-content" id="backimagediv">';
        if($set_image['backimage'] == '')
            echo '<image src="../../images/No_Image_Available.png" alt="No image icon"  width="290" height="200"> ';
        else
            echo '<img src="data:image/jpeg;base64,' . $set_image['backimage'] . '" width="290" height="200">';
       echo '</div>';
//       echo '| <a class="uploadfront" href="#" style="font-size:12px;">Upload Card Back Image</a>
//            <div class="webui-popover-content">';
//          echo '<form action="saveimage.php" method="post" id="backimage" enctype="multipart/form-data">
//               <p style="font-size:12px;">Select image to upload:
//               <input type="file" name="fileToUpload" id="fileToUpload">
//               <input type="submit" value="Upload Image" name="submit">
//               </p>
//           </form>
//           ';
//       echo '  </div>';
       ?>
     </td>
    </tr>

   <tr>
    <td>
     <span class='required'><?php xl('Plan Name','e'); ?> </span>
    </td>
    <td>
       <select name='i<?php echo $i?>plan_name' id='i<?php echo $i?>plan_name'  class="custom-combobox" value="<?php echo $set_image['plan_id'];?>">
       <option value="">Select one...</option>
       <?php if($set_image['plan_id'] != ''){ ?>
           <option value="<?php echo $set_image['plan_id'];?>" selected><?php echo $result3{"plan_name"} ; ?> </option>
       <?php } ?>    
    </select>
     <input type='hidden' id='i<?php echo $i?>plan_name_label' name='i<?php echo $i?>plan_name_label' value="<?php echo $result3{"plan_name"} ; ?>" >
       <input type="hidden" id="i<?php echo $i?>plan_name-id" name="i<?php echo $i?>plan_name-id" value='<?php echo $set_image['plan_id']; ?>'>
       <input type='hidden' name="i<?php echo $i?>plan_name_change" id="i<?php echo $i?>plan_name_change" value='<?php echo $result3{"plan_name"}; ?>'>
    </td>
   </tr>
   <tr>
       <td><span class=bold></span></td>
       <!--<td></td>-->
       <td>
           <a class="plan_benefits<?php echo $i?>" href="#" style="font-size: 12px;" >Plan Benefits</a>
           <div class="webui-popover-content">

           </div>
       </td> 

   </tr>

    <tr>
     <td>
      <span class='required'><?php xl('Effective Date','e'); ?>: </span>
     </td>
     <td>
      <input type='entry' size='11' name='i<?php echo $i ?>effective_date'
       id='i<?php echo $i ?>effective_date'
       value='<?php echo $result3['date'] ?>'
       onkeyup='datekeyup(this,mypcc)' onblur='dateblur(this,mypcc)'
       title='yyyy-mm-dd' />

      <img src='../../interface/pic/show_calendar.gif' align='absbottom' width='24' height='22'
      id='img_i<?php echo $i ?>effective_date' border='0' alt='[?]' style='cursor:pointer'
      title='<?php xl('Click here to choose a date','e'); ?>'>

      <script LANGUAGE="JavaScript">
      Calendar.setup({inputField:"i<?php echo $i ?>effective_date", ifFormat:"%Y-%m-%d", button:"img_i<?php echo $i; ?>effective_date"});
      </script>


     </td>
    </tr>

    <tr>
     <td><span class=required><?php xl('Policy Number','e'); ?>: </span></td>
     <td><input type='entry' size='16' name='i<?php echo $i?>policy_number' value="<?php echo $result3{"policy_number"}?>"
      onkeyup='policykeyup(this)'></td>
    </tr>

    <tr>
     <td><span class=required><?php xl('Group Number','e'); ?>: </span></td><td><input type=entry size=16 name=i<?php echo $i?>group_number value="<?php echo $result3{"group_number"}?>" onkeyup='policykeyup(this)'></td>
    </tr>

    <tr<?php if ($GLOBALS['omit_employers']) echo " style='display:none'"; ?>>
     <td class='required'><?php xl('Subscriber Employer (SE)','e'); ?><br><span style='font-weight:normal'>
      (<?php xl('if unemployed enter Student','e'); ?>,<br><?php xl('PT Student, or leave blank','e'); ?>): </span></td>
     <td><input type=entry size=25 name=i<?php echo $i?>subscriber_employer
      value="<?php echo $result3{"subscriber_employer"}?>"
       onchange="capitalizeMe(this);" /></td>
    </tr>

    <tr<?php if ($GLOBALS['omit_employers']) echo " style='display:none'"; ?>>
     <td><span class=required><?php xl('SE Address','e'); ?>: </span></td>
     <td><input type=entry size=25 name=i<?php echo $i?>subscriber_employer_street
      value="<?php echo $result3{"subscriber_employer_street"}?>"
       onchange="capitalizeMe(this);" /></td>
    </tr>

    <tr<?php if ($GLOBALS['omit_employers']) echo " style='display:none'"; ?>>
     <td colspan="2">
      <table>
       <tr>
        <td><span class=required><?php xl('SE City','e'); ?>: </span></td>
        <td><input type=entry size=15 name=i<?php echo $i?>subscriber_employer_city
         value="<?php echo $result3{"subscriber_employer_city"}?>"
          onchange="capitalizeMe(this);" /></td>
        <td><span class=required><?php echo ($GLOBALS['phone_country_code'] == '1') ? xl('SE State','e') : xl('SE Locality','e') ?>: </span></td>
	<td>
         <?php
          // Modified 7/2009 by BM to incorporate data types
	  generate_form_field(array('data_type'=>$GLOBALS['state_data_type'],'field_id'=>('i'.$i.'subscriber_employer_state'),'list_id'=>$GLOBALS['state_list'],'fld_length'=>'15','max_length'=>'63','edit_options'=>'C'), $result3['subscriber_employer_state']);
         ?>
        </td>
       </tr>
       <tr>
        <td><span class=required><?php echo ($GLOBALS['phone_country_code'] == '1') ? xl('SE Zip Code','e') : xl('SE Postal Code','e') ?>: </span></td>
        <td><input type=entry size=10 name=i<?php echo $i?>subscriber_employer_postal_code value="<?php echo $result3{"subscriber_employer_postal_code"}?>"></td>
        <td><span class=required><?php xl('SE Country','e'); ?>: </span></td>
	<td>
         <?php
          // Modified 7/2009 by BM to incorporate data types
	  generate_form_field(array('data_type'=>$GLOBALS['country_data_type'],'field_id'=>('i'.$i.'subscriber_employer_country'),'list_id'=>$GLOBALS['country_list'],'fld_length'=>'10','max_length'=>'63','edit_options'=>'C'), $result3['subscriber_employer_country']);
         ?>
	</td>
       </tr>
      </table>
     </td>
    </tr>

   </table>
  </td>

  <td valign=top>
   <span class=required><?php xl('Subscriber','e'); ?>: </span>
   <input type=entry size=10 name=i<?php echo $i?>subscriber_fname
    value="<?php echo $result3{"subscriber_fname"}?>"
    onchange="capitalizeMe(this);" />
   <input type=entry size=3 name=i<?php echo $i?>subscriber_mname
    value="<?php echo $result3{"subscriber_mname"}?>"
    onchange="capitalizeMe(this);" />
   <input type=entry size=10 name=i<?php echo $i?>subscriber_lname
    value="<?php echo $result3{"subscriber_lname"}?>"
    onchange="capitalizeMe(this);" />
   <br>
   <span class=required><?php xl('Relationship','e'); ?>: </span>
   <?php
    // Modified 6/2009 by BM to use list_options and function
    generate_form_field(array('data_type'=>1,'field_id'=>('i'.$i.'subscriber_relationship'),'list_id'=>'sub_relation','empty_title'=>' '), $result3['subscriber_relationship']);
   ?>
   <a href="javascript:popUp('../../interface/patient_file/summary/browse.php?browsenum=<?php echo $i?>')" class=text>(<?php xl('Browse','e'); ?>)</a><br />

   <span class=bold><?php xl('D.O.B.','e'); ?>: </span>
   <input type='entry' size='11' name='i<?php echo $i?>subscriber_DOB'
    id='i<?php echo $i?>subscriber_DOB'
    value='<?php echo $result3['subscriber_DOB'] ?>'
    onkeyup='datekeyup(this,mypcc)' onblur='dateblur(this,mypcc)'
    title='yyyy-mm-dd' />

   <img src='../../interface/pic/show_calendar.gif' align='absbottom' width='24' height='22'
    id='img_i<?php echo $i; ?>dob_date' border='0' alt='[?]' style='cursor:pointer'
    title='<?php xl('Click here to choose a date','e'); ?>'>

    <script LANGUAGE="JavaScript">
    Calendar.setup({inputField:"i<?php echo $i?>subscriber_DOB", ifFormat:"%Y-%m-%d", button:"img_i<?php echo $i; ?>dob_date"});
    </script>


   <span class=bold><?php xl('S.S.','e'); ?>: </span><input type=entry size=11 name=i<?php echo $i?>subscriber_ss value="<?php echo $result3{"subscriber_ss"}?>">&nbsp;
   <span class=bold><?php xl('Sex','e'); ?>: </span>
   <?php
    // Modified 6/2009 by BM to use list_options and function
    generate_form_field(array('data_type'=>1,'field_id'=>('i'.$i.'subscriber_sex'),'list_id'=>'sex'), $result3['subscriber_sex']);
   ?>	
   <br>
   <span class=required><?php xl('Subscriber Address','e'); ?>: </span>
   <input type=entry size=25 name=i<?php echo $i?>subscriber_street
    value="<?php echo $result3{"subscriber_street"}?>"
    onchange="capitalizeMe(this);" /><br>
   <span class=required><?php xl('City','e'); ?>: </span>
   <input type=entry size=15 name=i<?php echo $i?>subscriber_city
    value="<?php echo $result3{"subscriber_city"}?>"
    onchange="capitalizeMe(this);" />
   <span class=required><?php echo ($GLOBALS['phone_country_code'] == '1') ? xl('State','e') : xl('Locality','e') ?>: </span>
   <?php
    // Modified 7/2009 by BM to incorporate data types
    generate_form_field(array('data_type'=>$GLOBALS['state_data_type'],'field_id'=>('i'.$i.'subscriber_state'),'list_id'=>$GLOBALS['state_list'],'fld_length'=>'15','max_length'=>'63','edit_options'=>'C'), $result3['subscriber_state']);
   ?>
   <br />	
   <span class=required><?php echo ($GLOBALS['phone_country_code'] == '1') ? xl('Zip Code','e') : xl('Postal Code','e') ?>: </span><input type=entry size=10 name=i<?php echo $i?>subscriber_postal_code value="<?php echo $result3{"subscriber_postal_code"}?>">
   <span class='required'<?php if ($GLOBALS['omit_employers']) echo " style='display:none'"; ?>>
   <?php xl('Country','e'); ?>: </span>
   <?php
    // Modified 7/2009 by BM to incorporate data types
    generate_form_field(array('data_type'=>$GLOBALS['country_data_type'],'field_id'=>('i'.$i.'subscriber_country'),'list_id'=>$GLOBALS['country_list'],'fld_length'=>'10','max_length'=>'63','edit_options'=>'C'), $result3['subscriber_country']);
   ?>
   <br />
   <span class=bold><?php xl('Subscriber Phone','e'); ?>: 
   <input type='text' size='20' name='i<?php echo $i?>subscriber_phone' value='<?php echo $result3["subscriber_phone"] ?>' onkeyup='phonekeyup(this,mypcc)' />
   </span><br />
   <span class=bold><?php xl('CoPay','e'); ?>: <input type=text size="6" name=i<?php echo $i?>copay value="<?php echo $result3{"copay"}?>">
   </span><br />
   <span class='required'><?php xl('Accept Assignment','e'); ?>: </span>
   <select name=i<?php echo $i?>accept_assignment>
     <option value="TRUE" <?php if (strtoupper($result3{"accept_assignment"}) == "TRUE") echo "selected"?>><?php xl('YES','e'); ?></option>
     <option value="FALSE" <?php if (strtoupper($result3{"accept_assignment"}) == "FALSE") echo "selected"?>><?php xl('NO','e'); ?></option>
   </select>
  </td>
 </tr>

</table>
      
      
      
<hr />
<?php
  }
  echo "</div>\n";
               
echo "</div>";


 } // end of "if not simplified_demographics"
?>

<?php if (!$SHORT_FORM) echo "  <center>\n"; ?>
<br />
<?php if ($WITH_SEARCH) { ?>
<!--<input type="button" id="search" value=<?php xl('Search','e','\'','\''); ?>
 style='background-color:<?php echo $searchcolor; ?>' />-->
&nbsp;&nbsp;
<?php } ?>
<input type="button" name='create' id="create" value=<?php xl('Create New Patient','e','\'','\''); ?> />

</center>

  </td>
  <td align='right' valign='top' width='1%' nowrap>
   <!-- Image upload stuff was here but got moved. -->
  </td>
 </tr>
</table>

</form>
   
<!-- include support for the list-add selectbox feature -->
<?php include($GLOBALS['fileroot']."/library/options_listadd.inc"); ?>
</body>

<script language="JavaScript">

// fix inconsistently formatted phone numbers from the database
var f = document.forms[0];
if (f.form_phone_contact) phonekeyup(f.form_phone_contact,mypcc);
if (f.form_phone_home   ) phonekeyup(f.form_phone_home   ,mypcc);
if (f.form_phone_biz    ) phonekeyup(f.form_phone_biz    ,mypcc);
if (f.form_phone_cell   ) phonekeyup(f.form_phone_cell   ,mypcc);

<?php echo $date_init; ?>

// -=- jQuery makes life easier -=-

// var matches = 0; // number of patients that match the demographic information being entered
// var override = false; // flag that overrides the duplication warning

$(document).ready(function() {

    // added to integrate insurance stuff
    <?php for ($i=1;$i<=3;$i++) { ?>
    $("#form_i<?php echo $i?>subscriber_relationship").change(function() { auto_populate_employer_address<?php echo $i?>(); });
    <?php } ?>
	
    $('#search').click(function() { searchme(); });
    $('#create').click(function() {   submitme(); });
    $('#search_fac').click(function() { searchit(); });
    $('#search_agen').click(function() { searchagen(); });

    var submitme = function() {
      
      var f = document.forms[0];

      if (validate(f)) {
          //alert('validated');
        //if (force_submit) {
          // In this case dups were shown already and Save should just save.
          f.submit();
          return;
        //}
<?php
// D in edit_options indicates the field is used in duplication checking.
// This constructs a list of the names of those fields.
$mflist = "";
$mfres = sqlStatement("SELECT * FROM layout_options " .
  "WHERE form_id = 'DEM' AND uor > 0 AND field_id != '' AND " .
  "edit_options LIKE '%D%' " .
  "ORDER BY group_name, seq");
while ($mfrow = sqlFetchArray($mfres)) {
  $field_id  = $mfrow['field_id'];
  if (strpos($field_id, 'em_') === 0) continue;
  if (!empty($mflist)) $mflist .= ",";
  $mflist .= "'" . htmlentities($field_id) . "'";
}
?>        
     
      } // end if validate
    } // end function

// Set onclick/onfocus handlers for toggling background color.
<?php
$lres = getLayoutRes();
while ($lrow = sqlFetchArray($lres)) {
  $field_id  = $lrow['field_id'];
  switch(getSearchClass($lrow['data_type'])) {
    case 1:
      echo "    \$('#form_$field_id').click(function() { toggleSearch(this); });\n";
      break;
    case 2:
      echo "    \$('#form_$field_id').click(function() { selClick(this); });\n";
      echo "    \$('#form_$field_id').blur(function() { selBlur(this); });\n";
      break;
  }
}


?>
      
 
}); // end document.ready

</script>

</html>

 