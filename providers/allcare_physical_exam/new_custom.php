<!DOCTYPE html>
<html lang="en">
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

    $ignoreAuth = true; // ignore the standard authentication for a regular OpenEMR user
include_once('../../interface/globals.php');
include_once("$srcdir/api.inc");
include_once("$srcdir/forms.inc");
include_once("lines.php");

//$edit=$_REQUEST['edit'];
$encounter  = $_REQUEST['encounter'];
$pid1       = $_REQUEST['pid'];
$location   = $_REQUEST['location'];
$provider   = $_REQUEST['provider'];
$menu       = $_REQUEST['menu_val'];

if($_REQUEST['isSingleView'])
    $isSingleView = 1;
else 
    $isSingleView = 0;

if($_REQUEST['isFromCharts']){
    $isFromCharts = 1;
}else{
    $isFromCharts = 0;
}
if($location == 'provider_portal'){ ?>
<style> .body_top { background-color: #F6F6F6 !important; } </style>
<?php }
if (! $encounter) { // comes from globals.php
 die("Internal error: we do not seem to be in an encounter!");
}

//$returnurl = $GLOBALS['concurrent_layout'] ? 'encounter_top.php' : 'patient_encounter.php';

function showAllcareExamLine($line_id, $description, &$linedbrow, $sysnamedisp) {
 $dres = sqlStatement("SELECT * FROM tbl_form_physical_exam_diagnoses " .
  "WHERE line_id = '$line_id' ORDER BY ordering, diagnosis");

 echo " <tr>\n";
 echo "  <td align='center'><input type='checkbox' name='form_obs[$line_id][wnl]' " .
  "value='1'" . ($linedbrow['wnl'] ? " checked" : "") . " /></td>\n";
 echo "  <td align='center'><input type='checkbox' name='form_obs[$line_id][abn]' " .
  "value='1'" . ($linedbrow['abn'] ? " checked" : "") . " /></td>\n";
 echo "  <td nowrap>$sysnamedisp</td>\n";
 echo "  <td wrap>$description</td>\n";

 echo "  <td><select name='form_obs[$line_id][diagnosis]' onchange='seldiag(this, \"$line_id\")' style='width:100%'>\n";
 echo "   <option value=''></option>\n";
 $diagnosis = $linedbrow['diagnosis'];
 while ($drow = sqlFetchArray($dres)) {
  $sel = '';
  $diag = $drow['diagnosis'];
  if ($diagnosis && $diag == $diagnosis) {
   $sel = 'selected';
   $diagnosis = '';
  }
  echo "   <option value='$diag' $sel>$diag</option>\n";
 }
 // If the diagnosis was not in the standard list then it must have been
 // there before and then removed.  In that case show it in parentheses.
 if ($diagnosis) {
  echo "   <option value='$diagnosis' selected>($diagnosis)</option>\n";
 }
 echo "   <option value='*'>-- Edit --</option>\n";
 echo "   </select></td>\n";

 $s1 =htmlspecialchars ($linedbrow['comments']);
 //$s1=str_replace("/'", "'", $linedbrow['comments']);

 if($s1!='')
 {
?> <td ><input type='text'  name='<?php echo "form_obs[".$line_id."][comments]"; ?>' size='20' maxlength='250' style='width:100%' value="<?php echo  $s1; ?> " /></td>
      <?php echo "\n";
 echo " </tr>\n";
 }
 else {
        echo "  <td><input type='text'  name='form_obs[$line_id][comments]' " .
      "size='20' maxlength='250' style='width:100%' " .
      "value='' /></td>\n";
     echo " </tr>\n";
 }
 
}
function showAllcareTreatmentLine($line_id, $description, &$linedbrow) {
 echo " <tr>\n";
 echo "  <td align='center'><input type='checkbox' name='form_obs[$line_id][wnl]' " .
  "value='1'" . ($linedbrow['wnl'] ? " checked" : "") . " /></td>\n";
 echo "  <td></td>\n";
 echo "  <td colspan='2' wrap>$description</td>\n";
 echo "  <td colspan='2'><input type='text' name='form_obs[$line_id][comments]' " .
  "size='20' maxlength='250' style='width:100%' " .
  "value=" . htmlentities($linedbrow['comments'], ENT_COMPAT) . " /></td>\n";
 echo " </tr>\n";
}
$edit='';
 $formid = $_REQUEST['id'];
//$edit=$_REQUEST['edit'];
// If Save was clicked, save the info.
//
function GetIP()
{
if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown"))
$ip = getenv("HTTP_CLIENT_IP");
else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown"))
$ip = getenv("HTTP_X_FORWARDED_FOR");
else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown"))
$ip = getenv("REMOTE_ADDR");
else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown"))
$ip = $_SERVER['REMOTE_ADDR'];
else
$ip = "unknown";
return($ip);
}
$ip_addr=GetIP();
$ip_addr = $ip_addr."(provider portal)";
if ($_POST['bn_save']) {
 $finalized=($_POST['cbFinalized']=='on')?'Y':'N';
 $pending=($_POST['cbPending']=='on')?'Y':'N';
 // We are to update/insert multiple table rows for the form.
 // Each has 2 checkboxes, a dropdown and a text input field.
 // Skip rows that have no entries.
 // There are also 3 special rows with just one checkbox and a text
 // input field.  Maybe also a diagnosis line, not clear.

 if ($formid) {
  $query = "DELETE FROM tbl_form_physical_exam WHERE forms_id = '$formid'";
  sqlStatement($query);
  }
 else {
  $formid = addForm($encounter, "Allcare Physical Exam", 0, "allcare_physical_exam", $pid1, $userauthorized);
  $query = "UPDATE forms SET form_id = id WHERE id = '$formid' AND form_id = 0";
  sqlStatement($query);
 }
 
 $form_obs = $_POST['form_obs'];
 foreach ($form_obs as $line_id => $line_array) {
  $wnl = $line_array['wnl'] ? '1' : '0';
  $abn = $line_array['abn'] ? '1' : '0';
  $diagnosis = $line_array['diagnosis'] ? $line_array['diagnosis'] : '';
  $comments  = $line_array['comments']  ? $line_array['comments'] : '';
  if ($wnl || $abn || $diagnosis || $comments) {
   /*$query = "INSERT INTO tbl_form_physical_exam ( " .
    "forms_id, line_id, wnl, abn, diagnosis, comments " .
    ") VALUES ( " .
    "'$formid', '$line_id', '$wnl', '$abn', '$diagnosis', '$comments' " .
    ")";
   sqlInsert($query);*/
      sqlInsert("INSERT INTO tbl_form_physical_exam ( " .
    "forms_id, line_id, wnl, abn, diagnosis, comments " .
    ") VALUES (?,?,?,?,?,?)",array($formid,$line_id,$wnl,$abn,$diagnosis,$comments));
  }
 }
 if($finalized || $pending){
     
        $logdata= array();
        $data = mysql_query("SELECT logdate from `tbl_allcare_formflag` WHERE  form_id=".$formid);
        while ($row = mysql_fetch_array($data,MYSQL_ASSOC)) {
            $array =  unserialize($row['logdate']);
            $count= count($array);
        }
        $res = sqlStatement("SELECT * FROM `tbl_allcare_formflag` WHERE form_id = '$formid'");
        $row1 = sqlFetchArray($res);
        $count = isset($count)? $count: 0;
        if($formid && $formid != $row1['form_id']){
            $array2[] = array( 'authuser' =>$_SESSION["portal_username"],'pending' => $pending,'finalized' => $finalized, 'date' => date("Y/m/d"), 'action'=>'Created','ip_address'=>$ip_addr ,'count'=> $count+1);
            $logdata = array_merge_recursive($array, $array2);
            $logdata= ($logdata? serialize($logdata): serialize($array2) );
            $query1 = "INSERT INTO tbl_allcare_formflag ( " .
                    "encounter_id,form_id, form_name,pending,finalized, logdate" .
                    ") VALUES ( " .
                    "".$encounter.",'$formid', 'Allcare Physical Exam','$pending', '$finalized', '".$logdata."' " .
                    ")";
                     sqlInsert($query1);
        } 

        else if($formid == $row1['form_id']){
                $array2[] = array( 'authuser' =>$provider,'pending' => $pending,'finalized' => $finalized, 'date' => date("Y/m/d"), 'action' => 'Updated','ip_address'=>$ip_addr ,'count'=> $count+1);
                $logdata = array_merge_recursive($array, $array2);
                $logdata= ($logdata? serialize($logdata): serialize($array2) );
                $query2 = "UPDATE tbl_allcare_formflag SET pending = '$pending', finalized = '$finalized',logdate =  '".$logdata."' WHERE  form_id = $formid AND encounter_id = '$encounter' AND form_name= 'Allcare Physical Exam' ";
                sqlStatement($query2);
        }
        //save physical exam status in lbf_data
        
        if($finalized=='Y' && $pending=='Y'){
            $finalized1='finalized';
            $pending1='pending';
            $lb_value=$finalized1.'|'. $pending1;

        }else if($finalized=='Y'){
           $finalized1='finalized';
            $lb_value=$finalized1;

        }else if($pending=='Y'){
             $pending1='pending';
            $lb_value=$pending1;


        }
         $res12=sqlstatement("select form_id  from forms where form_name ='Allcare Encounter Forms' AND encounter='".$encounter."' AND pid='$pid1' AND deleted=0 order by id desc");
         $frow_res = sqlFetchArray($res12);
         if(!empty($frow_res)){
             if($lb_value!=''){
                 $formid_lb=$frow_res['form_id'];
                 $res1=sqlstatement("select * from lbf_data lb INNER JOIN layout_options l ON l.field_id=lb.field_id where lb.form_id='$formid_lb' AND l.form_id='LBF2' AND l.group_name LIKE '%Physical Exam%' AND lb.field_id LIKE '%_stat%' order by seq");
                 $res_row1=sqlFetchArray($res1);
                 if(!empty($res_row1)){

                     $update=sqlStatement("UPDATE lbf_data SET  `field_value` = ? WHERE  field_id= ? AND form_id = ?",array($lb_value,'physical_exam_stat',$formid_lb));
                 }else{

                       sqlInsert("INSERT INTO lbf_data (form_id,field_id,field_value) VALUES (?,?,?)", array($formid_lb,'physical_exam_stat',$lb_value));
                 }
             }
             
         }else{
             
             if($lb_value!=''){
                
                $sql_form=sqlStatement("select max(form_id)as new_form from forms where form_name='Allcare Encounter Forms' AND formdir='LBF2'");
                $row_form=sqlFetchArray($sql_form);
                $new_fid= $row_form['new_form'];
                $new_id1=++$new_fid;
                //echo "INSERT INTO  forms (date,encounter,form_name,form_id,pid,user,groupname,authorized,deleted,formdir) VALUES (NOW(),$ecounter1,'Allcare Encounter Forms',$new_id1,$pid1,'$_SESSION[authUser]','default',1,0,'LBF2')";
                $ins_form=sqlStatement("INSERT INTO  forms (date,encounter,form_name,form_id,pid,user,groupname,authorized,deleted,formdir) VALUES (NOW(),$encounter,'Allcare Encounter Forms',$new_id1,$pid1,'".$provider."','default',1,0,'LBF2')");
                $row1_form=sqlFetchArray($ins_form);
                $res1=sqlstatement("select * from lbf_data lb INNER JOIN layout_options l ON l.field_id=lb.field_id where lb.form_id='$new_id1' AND l.form_id='LBF2' AND l.group_name LIKE '%Physical Exam%' AND lb.field_id LIKE '%_stat%' order by seq");
                $res_row1=sqlFetchArray($res1);
                if(!empty($res_row1)){
                   
                      $update=sqlStatement("UPDATE lbf_data SET  `field_value` = ? WHERE  field_id= ? AND form_id = ?",array($lb_value,'physical_exam_stat',$new_id1));
                }else{
                    
                      sqlInsert("INSERT INTO lbf_data (form_id,field_id,field_value) VALUES (?,?,?)", array($new_id1,'physical_exam_stat',$lb_value));
               }
             }
        }        
    }
    if($formid_lb != 0 || $formid_lb != '')
        $lbf_form_id = $formid_lb;
    else
        $lbf_form_id = $new_id1;
    
    if (! $_POST['form_refresh']&&$location=='') {
        if($isSingleView == 1 && $isFromCharts == 0){
            echo "<script>window.close();
                window.opener.location.href = '../../../providers/single_view_form.php?encounter=$encounter&pid=$pid1';</script>";
        }else if($isSingleView == 1 && $isFromCharts == 1){ 
            echo "<script>window.opener.datafromchildwindow($formid,$lbf_form_id);window.close();</script>";
        }else{
            echo "<script>window.close();
                window.opener.location.href = '../../../providers/provider_incomplete_charts.php?checkencounter=$encounter';</script>";
        }
        exit;
   }else if(! $_POST['form_refresh']&&$location=='provider_portal'){
        if($isSingleView == 1 && $isFromCharts == 0){
            echo "<script>window.close();
                window.opener.location.href = '../../../providers/single_view_form.php?encounter=$encounter&pid=$pid1';</script>";
        }else if($isSingleView == 1 && $isFromCharts == 1){
            echo "<script>window.opener.datafromchildwindow($formid,$lbf_form_id);window.close();</script>";
        }else{
            echo "<script>window.close();
                window.opener.location.href = '../../../providers/provider_incomplete_charts.php?checkencounter=$encounter';</script>";
        }
        exit;
    }
}

// Load all existing rows for this form as a hash keyed on line_id.
//
$rows = array();
if ($formid) {
   $res = sqlStatement("SELECT * FROM tbl_form_physical_exam  WHERE forms_id = '$formid'");
 /*$res = sqlStatement("SELECT * FROM tbl_form_physical_exam e INNER JOIN tbl_form_physical_exam_status s ON s.form_id=e.forms_id WHERE forms_id = '$formid' "
         . "ORDER BY s.updated_date DESC ,s.id ASC  LIMIT 0,1");*/
 while ($row = sqlFetchArray($res)) {
  $rows[$row['line_id']] = $row;
 }
}
?>
<head>
<meta content="width=device-width,initial-scale=1.0" name="viewport">
<?php html_header_show();?>
<link rel="stylesheet" href="<?php echo $css_header;?>" type="text/css">
<link rel="stylesheet" href="../../tableresponsive/dialog_responsive.css"/>
<script type="text/javascript" src="../../../library/dialog.js"></script>
<script language="JavaScript">
 function seldiag(selobj, line_id) {
  var i = selobj.selectedIndex;
  var opt = selobj.options[i];
  if (opt.value == '*') {
   selobj.selectedIndex = 0;
  window.open('../allcare_physical_exam/edit_diagnoses.php?lineid=' + line_id, '_blank', 500, 400);
  }
  }
 
 function details(formid){
      window.open('../allcare_physical_exam/details.php?formid=' + formid, '_blank', 500, 400);
    }

 function refreshme() {
  top.restoreSession();
  var f = document.forms[0];
  f.form_refresh.value = '1';
  f.submit();
 }
 

</script>

</head>

<body class="body_top">
    

<form method="post" action="new_custom.php?encounter=<?php echo $encounter; ?>&pid=<?php echo $pid1; ?>&id=<?php echo $formid; ?>&location=<?php echo $location; ?>&provider=<?php echo $provider; ?>&menu_val=<?php echo $menu; ?>&isSingleView=<?php echo $isSingleView; ?>&isFromCharts=<?php echo $isFromCharts; ?>"
 onsubmit="return top.restoreSession()">

  <?php 
        $sql_pname=sqlStatement("select CONCAT(lname,' ',fname) AS pname from  patient_data  where   pid=$pid1");
        $res_row1=sqlFetchArray($sql_pname);
        $dos_sql=sqlStatement("select * from form_encounter where encounter=$encounter");
        $res_dos=sqlFetchArray($dos_sql);
        $dos=explode(" ",$res_dos['date']);

        $cat=sqlStatement("select * from openemr_postcalendar_categories where pc_catid='".$res_dos['pc_catid']."'");
        $res_cat=sqlFetchArray($cat);
        echo "<table style='border:0 !important'>";
        echo "<tr><td style='border:0 !important'><b>Patient Name: </b>".$res_row1['pname']."</td><td style='border:0 !important'><b>Encounter: </b>".$encounter."</td></tr>";
        echo "<tr><td style='border:0 !important'><b>Date Of Service: </b>".$dos[0]."</td><td style='border:0 !important'><b>Visit Category: </b>".$res_cat['pc_catname']."</td></tr>";
        echo "</table>";  ?>
<center>

<p>
<table border='0' width='98%'>

 <tr>
  <td align='center' width='1%' nowrap><b><?php xl('WNL','e'); ?></b></td>
  <td align='center' width='1%' nowrap><b><?php xl('ABN1','e'); ?></b></td>
  <td align='left'   width='1%' nowrap><b><?php xl('System','e'); ?></b></td>
  <td align='left'   width='65%' wrap><b><?php xl('Specific','e'); ?></b></td>
  <td align='left'   width='1%' nowrap><b><?php xl('Diagnosis','e'); ?></b></td>
  <td align='left'   width='95%' nowrap><b><?php xl('Comments','e'); ?></b></td>
 </tr>

<?php
 foreach ($pelines as $sysname => $sysarray) {
  $sysnamedisp = $sysname;
  if ($sysname == '*') {
   // TBD: Show any remaining entries in $rows (should not be any).
   echo " <tr><td colspan='6'>\n";
   echo "   &nbsp;<br><b>" .xl('Treatment:'). "</b>\n";
   echo " </td></tr>\n";
  }
  else {
    $sysnamedisp = xl($sysname);
  }
  foreach ($sysarray as $line_id => $description) {
   if ($sysname != '*') {
   showAllcareExamLine($line_id, $description, $rows[$line_id], $sysnamedisp);
   } else {
    showAllcareTreatmentLine($line_id, $description, $rows[$line_id]);
   }
   $sysnamedisp = '';
   // TBD: Delete $rows[$line_id] if it exists.
  } // end of line
 } // end of system name
?>

</table>
<br>
<p align="left"><b>Finalized</b><input type="checkbox" id="cbFinalized" name="cbFinalized" class="" /><b>Pending</b><input type="checkbox" id="cbPending" name="cbPending" class="" />
<p align="right"><a href="javascript:details(<?php echo $formid; ?>)">Details</a></p>
</p>

<p>
<input type='hidden' name='form_refresh' value='' />


<input type='submit' name='bn_save' value='<?php xl('Save','e'); ?>'/>
&nbsp;
<input type='button' value='<?php xl('Cancel','e'); ?>'
 onclick="window.close();" />

</p>

</center>

</form>
    
    <?php 
    if($formid)
    {
    $sql="SELECT DISTINCT f.finalized , f.pending
                   FROM `tbl_allcare_formflag` f INNER JOIN tbl_form_physical_exam e ON e.forms_id=f.form_id
                   WHERE form_id= $formid ORDER BY f.id DESC LIMIT 0,1";
    $getres=sqlStatement($sql);
    $res=sqlFetchArray($getres);
    
    if($res['finalized']=='Y')
    {  
        echo "<script type='text/javascript'>            
           
            //jQuery('#cbFinalized').prop('checked',true);
            
            /*$('#frmActionPlan :input').attr('disabled', true);            
            $('#frmActionPlan :select').attr('disabled', true);
            $('#frmActionPlan :textarea').attr('disabled', true);*/

            
            
            document.getElementById('cbFinalized').checked=true;

            /*var inputs = document.getElementsByTagName('input');
            for (var i = 0; i < inputs.length; i++) {

                    inputs[i].disabled = true;

            }       

                        document.getElementById('btnCancel').disabled='';*/
            
             </script>";            
    }
    
  if($res['pending']=='Y')
    {  
        echo "<script type='text/javascript'>            
            document.getElementById('cbPending').checked=true;
            </script>";  
    }  
    } 
    ?>
<?php
// TBD: If $alertmsg, display it with a JavaScript alert().
?>    
</body>
</html>
