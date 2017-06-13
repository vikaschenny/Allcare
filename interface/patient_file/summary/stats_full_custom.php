<!DOCTYPE html>
<html lang="en">
<?php
/**
 * Copyright (C) 2005-2009 Rod Roark <rod@sunsetsystems.com>
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 */

//SANITIZE ALL ESCAPES
$sanitize_all_escapes=true;
//

//STOP FAKE REGISTER GLOBALS
$fake_register_globals=false;
//

require_once('../../globals.php');
require_once($GLOBALS['srcdir'].'/lists.inc');
require_once($GLOBALS['srcdir'].'/acl.inc');
require_once($GLOBALS['fileroot'].'/custom/code_types.inc.php');
require_once($GLOBALS['srcdir'].'/options.inc.php');
require_once($GLOBALS['srcdir'].'/patient.inc');

$patient_id     = $_REQUEST['pid'];
$encounter      = $_REQUEST['encounter'];
$location       = $_REQUEST['location'];
$provider       = $_REQUEST['provider'];

if($_REQUEST['isFromCharts'] == 1)
    $isFromCharts = 1;
else
    $isFromCharts = 0;

$id         = trim($_REQUEST['formid']);
$sql_pname  = sqlStatement("select CONCAT(lname,' ',fname) AS pname from  patient_data  where   pid=$patient_id");
$res_row1   = sqlFetchArray($sql_pname);
//echo "<b>Patient Name: </b>".$res_row1['pname']."<br>";
//echo "<b>Encounter: </b>".$encounter;
                
$dos_sql =sqlStatement("select * from form_encounter where encounter=$encounter");
$res_dos =sqlFetchArray($dos_sql);
$dos     =explode(" ",$res_dos['date']);

$cat=sqlStatement("select * from openemr_postcalendar_categories where pc_catid='".$res_dos['pc_catid']."'");
$res_cat=sqlFetchArray($cat);
echo "<table style='border:0 !important'>";
echo "<tr><td style='border:0 !important'><b>Patient Name: </b>".$res_row1['pname']."</td><td style='border:0 !important'><b>Encounter: </b>".$encounter."</td></tr>";
echo "<tr><td style='border:0 !important'><b>Date Of Service: </b>".$dos[0]."</td><td style='border:0 !important'><b>Visit Category: </b>".$res_cat['pc_catname']."</td></tr>";
echo "</table><br>";
    




 // Check authorization.
 if (acl_check('patients','med')) {
    $tmp = getPatientData($patient_id, "squad");
    if ($tmp['squad'] && ! acl_check('squads', $tmp['squad']))
        die(htmlspecialchars( xl('Not authorized'), ENT_NOQUOTES) );
 }else {
    die(htmlspecialchars( xl('Not authorized'), ENT_NOQUOTES) );
 }

 // Collect parameter(s)
$category   = empty($_REQUEST['category']) ? '' : $_REQUEST['category'];
$formid     = $id;
if($category == 'surgery'){
    $gname      = 'surgeries';
    $fieldid    = 'surgeries_stat';
}else if($category == 'dental'){
     $gname     = 'Dental';
     $fieldid   = 'dental_problems_stat';
}else if($category == 'allergy'){
    $gname      = 'Allergies';
    $fieldid    = 'allergies_stat';
}else if($category == 'medication'){
    $gname      = 'Medication';
    $fieldid    = 'medication_stat';
}else if($category == 'medical_problem'){
     $gname     = 'Medical Problems';
     $fieldid   = 'medical_problem_stat';
}else if($category == 'DME'){
     $gname     = 'DME';
     $fieldid   = 'dme_stat';
}

if($_REQUEST['mode1']=='add'){
 
 function updateENC_forms($id, $new, $create,$ecounter1,$pid1)
{
    global $id;
    $db_id = 0;
   // print_r($new);
    if($id==0 && !empty($new)) {
        $sql_form=sqlStatement("select max(form_id)as new_form from forms where form_name='Allcare Encounter Forms' AND formdir='LBF2'");
        $row_form=sqlFetchArray($sql_form);
        $new_fid= $row_form['new_form'];
        $new_id1=++$new_fid;
        //echo "INSERT INTO  forms (date,encounter,form_name,form_id,pid,user,groupname,authorized,deleted,formdir) VALUES (NOW(),$ecounter1,'Allcare Encounter Forms',$new_id1,$pid1,'$_SESSION[authUser]','default',1,0,'LBF2')";
        $ins_form=sqlStatement("INSERT INTO  forms (date,encounter,form_name,form_id,pid,user,groupname,authorized,deleted,formdir) VALUES (NOW(),$ecounter1,'Allcare Encounter Forms',$new_id1,$pid1,'$_SESSION[authUser]','default',1,0,'LBF2')");
        $row1_form=sqlFetchArray($ins_form); 
        $id=$new_id1;
    }
    if ($create) {
        foreach ($new as $key => $value) {
            if($value!=''){ 
               sqlInsert("INSERT INTO lbf_data (form_id,field_id,field_value) VALUES (?,?,?)", array($id,$key,$value));
            }
        } 
        $db_id = 1;
   }
   else {
        //echo $db_id = $new['id'];
        foreach ($new as $key => $value) {
           if($value!=''){
             //echo "UPDATE lbf_data SET  `field_value` = ? WHERE  field_id= ? AND form_id = ?",array($value,$key,$id);
              sqlStatement("UPDATE lbf_data SET  `field_value` = ? WHERE  field_id= ? AND form_id = ?",array($value,$key,$id));
           }else {
              sqlStatement("delete  from lbf_data where form_id=$id AND field_id='$key'");
           }
        }
        $db_id = 1;

    }
    return $db_id;
    
}


    $newdata = array();

    $fres = sqlStatement("SELECT * FROM layout_options " .
      "WHERE form_id = 'LBF2' AND uor > 0 AND field_id != '' AND group_name LIKE '%$gname%'" .
      "ORDER BY seq");
    $field_id1=array();
    while ($frow = sqlFetchArray($fres)) {
        $data_type = $frow['data_type'];
        $field_id  = $frow['field_id'];
        $field_id1[]  = $frow['field_id'];
        // $value  = '';
        $colname = $field_id;
        $table = 'lbf_data';

        $value = get_layout_form_value($frow);

        $newdata[$table][$colname] = $value;
    }
    $res1=sqlstatement("select * from lbf_data lb INNER JOIN layout_options l ON l.field_id=lb.field_id where lb.form_id='$formid' AND l.form_id='LBF2' AND l.group_name LIKE '%$gname%' AND lb.field_id LIKE '%$fieldid%' order by seq");
    $res_row1=sqlFetchArray($res1);
    if(!empty($res_row1)){
        updateENC_forms($formid, $newdata['lbf_data'] ,$create=false,$encounter,$patient_id,$isFromCharts);
        if($_REQUEST['isSingleView'] == 1 && $isFromCharts == 0)
            echo "<script> window.close();window.opener.location.href = '../../reports/single_view_form.php?encounter=$encounter&pid=$patient_id';</script>";
        else if($_REQUEST['isSingleView'] == 1 && $isFromCharts == 1)
                echo "<script>window.opener.datafromchildwindow('',$id);window.close();</script>";
                    #window.opener.location.href = '../../reports/patient_full_encounters_single_view.php?encounter=$encounter&pid=$patient_id';</script>";
        else
           echo "<script>  window.close();
             window.opener.location.href='../../reports/incomplete_charts.php?encounter=$encounter';</script>";

    }else{
        updateENC_forms($formid, $newdata['lbf_data'] ,$create=true,$encounter,$patient_id,$isFromCharts);
        if($_REQUEST['isSingleView'] == 1 && $isFromCharts == 0)
            echo "<script> window.close();window.opener.location.href = '../../reports/single_view_form.php?encounter=$encounter&pid=$patient_id';</script>";
        else if($_REQUEST['isSingleView'] == 1 && $isFromCharts == 1)
                echo "<script>window.opener.datafromchildwindow('',$id);window.close();</script>";
                    #window.opener.location.href = '../../reports/patient_full_encounters_single_view.php?encounter=$encounter&pid=$patient_id';</script>";
        else
            echo "<script> window.close();
              window.opener.location.href='../../reports/incomplete_charts.php?encounter=$encounter';</script>";
    }
 }
?>
<html>

<head>
    <?php html_header_show();?>
    <meta content="width=device-width,initial-scale=1.0" name="viewport">
    <link rel="stylesheet" href='<?php echo $css_header ?>' type='text/css'>
    <link rel="stylesheet" href="../../tableresponsive/stats_full_costom_responsive.css"/>
    <title><?php echo htmlspecialchars( xl('Patient Issues'), ENT_NOQUOTES) ; ?></title>

    <script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/dialog_custom.js"></script>
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/jquery.js"></script>

    <script language="JavaScript">

        // callback from add_edit_issue.php:
        function refreshIssue(issue, title) {

           // top.restoreSession();
            location.reload();
        }

        function dopclick(id,category,pid,location) {
            <?php if (acl_check('patients','med','','write')): ?>
            if (category == 0) category = '';
            dlgopen('add_edit_issue_custom.php?issue=' + encodeURIComponent(id) + '&thistype=' + encodeURIComponent(category) + '&thispid='+pid+'&thislocation='+location, '_blank', 550, 400);
            <?php else: ?>
            alert("<?php echo addslashes( xl('You are not authorized to add/edit issues') ); ?>");
            <?php endif; ?>
        }

        // Process click on number of encounters.
        function doeclick(id,pid) {
           //alert(id+"=="+pid);
            dlgopen('../problem_encounter_custom.php?issue=' + id+'&pid='+pid, '_blank', 550, 400);
            //alert('returned');

        }

        // Add Encounter button is clicked.
        function newEncounter() {
            var f = document.forms[0];
            top.restoreSession();
           <?php if ($GLOBALS['concurrent_layout']) { ?>
            parent.left_nav.setRadio(window.name, 'nen');
            location.href='../../forms/newpatient/new.php?autoloaded=1&calenc=';
           <?php } else { ?>
            top.Title.location.href='../encounter/encounter_title.php';
            top.Main.location.href='../encounter/patient_encounter.php?mode=new';
           <?php } ?>
        }

    </script>
    <style>
       #status td {
            border:0px !important;
        }
    </style>
</head>

<body class="body_top">

<br>
<?php if($location!='provider_portal') { ?>
<div style="text-align:center" class="buttons">
  <a href='javascript:;' class='css_button' id='back'><span><?php echo htmlspecialchars( xl('Back'), ENT_NOQUOTES); ?></span></a>
</div>
<?php }else { ?>
<div style="text-align:center" class="buttons">
  <a href='javascript:;' class='css_button' id='back_provider'><span><?php echo htmlspecialchars( xl('Back'), ENT_NOQUOTES); ?></span></a>
</div>
<?php } ?>
<br>
<br>

<div id='patient_stats'>

<?php   if($_REQUEST['isSingleView'] == 1)
            $isSingleView = 1;
        else
            $isSingleView = 0;
?>
<form method='post' action='stats_full_custom.php?isSingleView=<?php echo $isSingleView; ?>&isFromCharts=<?php echo $isFromCharts; ?>' onsubmit='return top.restoreSession()'>

<table>

<?php
$encount = 0;
$lasttype = "";
$first = 1; // flag for first section
foreach ($ISSUE_TYPES as $focustype => $focustitles) {

  if ($category) {
    // Only show this category
    if ($focustype != $category) continue;
  }

  if ($first) {
    $first = 0;
  }
  else {
    echo "</table>";
  }

  // Show header
  $disptype = $focustitles[0];
  if(($focustype=='allergy' || $focustype=='medication') && $GLOBALS['erx_enable'])
  echo "<a href='../../eRx_custom.php?page=medentry&pid=<?php echo $patient_id; ?>' class='css_button_small' onclick='top.restoreSession()' ><span>" . htmlspecialchars( xl('Add'), ENT_NOQUOTES) . "</span></a>\n";
  else
  echo "<a href='javascript:;' class='css_button_small' onclick='dopclick(0,\"" . htmlspecialchars($focustype,ENT_QUOTES)  . "\",$patient_id,\"" . htmlspecialchars($location,ENT_QUOTES)  . "\")'><span>" . htmlspecialchars( xl('Add'), ENT_NOQUOTES) . "</span></a>\n";
  echo "  <span class='title'>" . htmlspecialchars($disptype,ENT_NOQUOTES) . "</span>\n";
  echo " <table style='margin-bottom:1em;text-align:center'>";
  ?>
  <tr class='head'>
    <th><?php echo htmlspecialchars( xl('Title'), ENT_NOQUOTES); ?></th>
    <th><?php echo htmlspecialchars( xl('Begin'), ENT_NOQUOTES); ?></th>
    <th><?php echo htmlspecialchars( xl('End'), ENT_NOQUOTES); ?></th>
    <th><?php echo htmlspecialchars( xl('Diag'), ENT_NOQUOTES); ?></th>
    <th><?php echo htmlspecialchars(xl('Status'),ENT_NOQUOTES); ?></th>
    <th><?php echo htmlspecialchars( xl('Occurrence'), ENT_NOQUOTES); ?></th>
    <?php if ($focustype == "allergy") { ?>
      <th><?php echo htmlspecialchars( xl('Reaction'), ENT_NOQUOTES); ?></th>
    <?php } ?>
    <?php if ($GLOBALS['athletic_team']) { ?>
      <th><?php echo htmlspecialchars( xl('Missed'), ENT_NOQUOTES); ?></th>
    <?php } else { ?>
      <th><?php echo htmlspecialchars( xl('Referred By'), ENT_NOQUOTES); ?></th>
    <?php } ?>
    <th><?php echo htmlspecialchars( xl('Comments'), ENT_NOQUOTES); ?></th>
    <th><?php echo htmlspecialchars( xl('Enc'), ENT_NOQUOTES); ?></th>
    </tr>
  <?php

  // collect issues
  $condition = '';
  if($GLOBALS['erx_enable'] && $GLOBALS['erx_medication_display'] && $focustype=='medication')
   $condition .= "and erx_uploaded != '1' ";
  $pres = sqlStatement("SELECT * FROM lists WHERE pid = ? AND type = ? $condition" .
   "ORDER BY - ISNULL( enddate ) , begdate DESC", array($patient_id,$focustype) );

  // if no issues (will place a 'None' text vs. toggle algorithm here)
  if (sqlNumRows($pres) < 1) {
    if ( getListTouch($patient_id,$focustype) ) {
      // Data entry has happened to this type, so can display an explicit None.
      echo "<tr><td class='text'><b>" . htmlspecialchars( xl("None"), ENT_NOQUOTES) . "</b></td></tr>";
    }
    else {
      // Data entry has not happened to this type, so can show the none selection option.
      echo "<tr><td class='text'><input type='checkbox' class='noneCheck' name='" . htmlspecialchars($focustype,ENT_QUOTES) . "' value='none' /><b>" . htmlspecialchars( xl("None"), ENT_NOQUOTES) . "</b></td></tr>";
    }
  }

  // display issues
  while ($row = sqlFetchArray($pres)) {

    $rowid = $row['id'];

    $disptitle = trim($row['title']) ? $row['title'] : "[Missing Title]";

    $ierow = sqlQuery("SELECT count(*) AS count FROM issue_encounter WHERE " .
      "list_id = ?", array($rowid) );

    // encount is used to toggle the color of the table-row output below
    ++$encount;
    $bgclass = (($encount & 1) ? "bg1" : "bg2");

    // look up the diag codes
    $codetext = "";
    if ($row['diagnosis'] != "") {
        $diags = explode(";", $row['diagnosis']);
        foreach ($diags as $diag) {
            $codedesc = lookup_code_descriptions($diag);
            $codetext .= htmlspecialchars($diag,ENT_NOQUOTES) . " (" . htmlspecialchars($codedesc,ENT_NOQUOTES) . ")<br>";
        }
    }

    // calculate the status
    if ($row['outcome'] == "1" && $row['enddate'] != NULL) {
      // Resolved
      $statusCompute = generate_display_field(array('data_type'=>'1','list_id'=>'outcome'), $row['outcome']);
    }
    else if($row['enddate'] == NULL) {
      $statusCompute = htmlspecialchars( xl("Active") ,ENT_NOQUOTES);
    }
    else {
      $statusCompute = htmlspecialchars( xl("Inactive") ,ENT_NOQUOTES);
    }
    $click_class='statrow';
    if($row['erx_source']==1 && $focustype=='allergy')
    $click_class='';
    elseif($row['erx_uploaded']==1 && $focustype=='medication')
    $click_class='';
    // output the TD row of info
    if ($row['enddate'] == NULL) {
      echo " <tr class='$bgclass detail $click_class' style='color:red;font-weight:bold' id='$rowid'>\n";
    }
    else {
      echo " <tr class='$bgclass detail $click_class' id='$rowid'>\n";
    }
    echo "  <td style='text-align:left'>" . htmlspecialchars($disptitle,ENT_NOQUOTES) . "</td>\n";
    echo "  <td>" . htmlspecialchars($row['begdate'],ENT_NOQUOTES) . "&nbsp;</td>\n";
    echo "  <td>" . htmlspecialchars($row['enddate'],ENT_NOQUOTES) . "&nbsp;</td>\n";
    // both codetext and statusCompute have already been escaped above with htmlspecialchars)
    echo "  <td>" . $codetext . "</td>\n";
    echo "  <td>" . $statusCompute . "&nbsp;</td>\n";
    echo "  <td class='nowrap'>";
    echo generate_display_field(array('data_type'=>'1','list_id'=>'occurrence'), $row['occurrence']);
    echo "</td>\n";
    if ($focustype == "allergy") {
      echo "  <td>" . htmlspecialchars($row['reaction'],ENT_NOQUOTES) . "&nbsp;</td>\n";
    }
    if ($GLOBALS['athletic_team']) {
        echo "  <td class='center'>" . $row['extrainfo'] . "</td>\n"; // games missed
    }
    else {
        echo "  <td>" . htmlspecialchars($row['referredby'],ENT_NOQUOTES) . "</td>\n";
    }
    echo "  <td>" . htmlspecialchars($row['comments'],ENT_NOQUOTES) . "</td>\n";
    echo "  <td id='e_$rowid' class='noclick center' title='" . htmlspecialchars( xl('View related encounters'), ENT_QUOTES) . "'>";
    echo "  <input type='button' value='" . htmlspecialchars($ierow['count'],ENT_QUOTES) . "' class='editenc' id='" . htmlspecialchars($rowid,ENT_QUOTES) . "' />";
    echo "  </td>";
    echo " </tr>\n";
  }
}
echo "</table>";
?>

</table>
<?php
//$sql=sqlstatement("select * from layout_options where form_id='LBF2' AND group_name LIKE '%$gname%'");
//$row=sqlFetchArray($sql);

$CPR = 4; // cells per row

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
  global $last_group;
  if (strlen($last_group) > 0) {
    end_row();
    echo " </table>\n";
    echo "</div>\n";
  }
}

// If we are editing a transaction, get its ID and data.
//echo $new_id1;

  // $trow = $form_id ?getF2FEncounterForm($form_id,$id) : array();
?>
<div class="tabContainer">							
 <?php
$fres = sqlStatement("SELECT * FROM layout_options " .
  "WHERE form_id = 'LBF2' AND uor > 0 AND group_name LIKE '%$gname%' " .
  "ORDER BY  seq");
$last_group = '';
$cell_count = 0;
$item_count = 0;
$display_style = 'block';

while ($frow = sqlFetchArray($fres)) {
  
  $this_group = $frow['group_name'];
  $titlecols  = $frow['titlecols'];
  $datacols   = $frow['datacols'];
  $data_type  = $frow['data_type'];
  $field_id   = $frow['field_id'];
  $list_id    = $frow['list_id'];
 
   $currvalue= '';
  

        $res=sqlstatement("select * from lbf_data where field_id='$field_id' AND form_id=$formid");
        $frow1 = sqlFetchArray($res);
        if($frow1['field_value']!=''){
             $currvalue=$frow1['field_value'];
        }else {
            if($data_type==3){
                 $sql=sqlStatement("select * from list_options where list_id='AllCareEncFormsAutoText' AND option_id='$field_id' order by seq");
                 $def=sqlFetchArray($sql);
                 $currvalue=$def['notes']; 
            }else {
                $currvalue= '';
            }
        }
       
 
// Handle a data category (group) change.
  if (strcmp($this_group, $last_group) != 0) {
    end_group();
   $group_seq  = substr($this_group, 0, 1);
   $group_name = substr($this_group, 1);
   $last_group = $this_group;
   $group_seq_esc = htmlspecialchars( $group_seq, ENT_QUOTES);
	//if($group_seq==6)	
           // echo "<div id='div_$group_seq_esc'>";
	//else				
            ///echo "<div class='tab' id='div_$group_seq_esc'>";
    echo " <table id='status' cellpadding='0'>\n";
    $display_style = 'none';
  }
  // Handle starting of a new row.
  if (($titlecols > 0 && $cell_count >= $CPR) || $cell_count == 0) {
    end_row();
    echo " <tr>";
  }

  if ($item_count == 0 && $titlecols == 0) $titlecols = 1;

  // Handle starting of a new label cell.
  if ($titlecols > 0) {
    end_cell();
    $titlecols_esc = htmlspecialchars( $titlecols, ENT_QUOTES);
    echo "<td width='70' valign='top' colspan='$titlecols_esc'";
    echo ($frow['uor'] == 2) ? " class='required'" : " class='bold'";
    if ($cell_count == 2) echo " style='padding-left:10pt'";
    echo ">";
    $cell_count += $titlecols;
  }
  ++$item_count;

  echo "<b>";

  // Modified 6-09 by BM - Translate if applicable
  if ($frow['title']) echo (htmlspecialchars( xl_layout_label($frow['title']), ENT_NOQUOTES) . ":"); else echo "&nbsp;";

  echo "</b>";

  // Handle starting of a new data cell.
  if ($datacols > 0) {
    end_cell();
    $datacols_esc = htmlspecialchars( $datacols, ENT_QUOTES);
    echo "<td valign='top' colspan='$datacols_esc' class='text'";
    if ($cell_count > 0) echo " style='padding-left:5pt'";
    echo ">";
    $cell_count += $datacols;
  }

  ++$item_count;
 generate_form_field($frow, $currvalue);
  //echo "</div>";

  }
end_group();

?>
<input type="hidden" id="mode1" name="mode1" value="add"/>
<input type="hidden" id="encounter" name="encounter" value="<?php echo $encounter; ?>"/>
<input type="hidden" id="pid" name="pid" value="<?php echo $patient_id;  ?>"/>
<input type="hidden" id="location" name="location" value="<?php echo $location; ?>"/>
<input type="hidden" id="category" name="category" value="<?php echo $category; ?>"/>
<input type="hidden" id="formid" name="formid" value="<?php echo $formid; ?>"/>
<input type="submit" id="fn_save" value="submit"/>
</div>
</form>
</div> <!-- end patient_stats -->

</body>

<script language="javascript">
// jQuery stuff to make the page a little easier to use

$(document).ready(function(){
   
    $(".statrow").mouseover(function() { $(this).toggleClass("highlight"); });
    $(".statrow").mouseout(function() { $(this).toggleClass("highlight"); });

    $(".statrow").click(function() { dopclick(this.id,0); });
    $(".editenc").click(function(event) { doeclick(this.id,'<?php echo $patient_id; ?>'); event.stopPropagation(); });
    $("#newencounter").click(function() { newEncounter(); });
    $("#history").click(function() { GotoHistory(); });
    $("#back").click(function() { GoBack(); });
    $("#back_provider").click(function() { GoBack_provider(); });


    $(".noneCheck").click(function() {
     // top.restoreSession();
      $.post( "../../../library/ajax/lists_touch.php", { type: this.name, patient_id: <?php echo htmlspecialchars($patient_id,ENT_QUOTES); ?> });
      $(this).hide(); 
    });
});

var GotoHistory = function() {
    top.restoreSession();
<?php if ($GLOBALS['concurrent_layout']): ?>
    parent.left_nav.setRadio(window.name,'his');
    location.href='../history/history_full.php';
<?php else: ?>
    location.href='../history/history_full.php';
<?php endif; ?>
}

var GoBack = function () {
     location.href='../../reports/incomplete_charts.php';
}
var GoBack_provider = function () {
    window.close();
//      window.opener.location.href='../../../providers/provider_incomplete_charts.php?provider=<?php echo $provider; ?>';
}

</script>

</html>
