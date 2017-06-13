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
require_once("$srcdir/api.inc");
require_once("$srcdir/forms.inc");
require_once("$srcdir/options.inc.php");
require_once("$srcdir/formdata.inc.php");
require_once("$srcdir/formatting.inc.php");
require_once("../../interface/orders/qoe.inc.php");
require_once("../../interface/orders/gen_hl7_order.inc.php");
require_once("../../custom/code_types.inc.php");


$sql=sqlStatement("SELECT id, fname, lname, specialty FROM users " .
      "WHERE active = 1 AND ( info IS NULL OR info NOT LIKE '%Inactive%' ) " .
      "AND authorized = 1 AND username='".$provider."'" .
      "ORDER BY lname, fname");
$id=sqlFetchArray($sql);
$id1=$id['id'];

global $form_id; 

$_SESSION['authUserID']=$id1;
$encounter             =$_REQUEST['encounter'];
$form_id               =trim($_REQUEST['formid']);// for staus finalized/pending
$pid                   =$_REQUEST['pid'];
if($_REQUEST['isSingleView'] == 1)
    $isSingleView = 1;
else
    $isSingleView = 0;

if($_REQUEST['isFromCharts'] == 1)
    $isFromCharts = 1;
else
    $isFromCharts = 0;

if($_REQUEST['mode']=='add'){
 

    function updateENC_forms($form_id, $new, $create,$ecounter1,$pid1,$provider,$field_id)
    {
        global $form_id;
        $db_id = 0;
        if ($create) {
            if($new!=''){ 
                sqlInsert("INSERT INTO lbf_data (form_id,field_id,field_value) VALUES (?,?,?)", array($form_id,$field_id,$new));
            }
        $db_id = 1;
        }
        else {
            if($new!=''){
                sqlStatement("UPDATE lbf_data SET  `field_value` = ? WHERE  field_id= ? AND form_id = ?",array($new,$field_id,$form_id));
            }else {
                sqlStatement("delete  from lbf_data where form_id=$form_id AND field_id='$field_id'");
            }
            $db_id = 1;
       }
     return $db_id;
    }
 
 
$newdata = array();

$fres = sqlStatement("SELECT * FROM layout_options " .
  "WHERE form_id = 'LBF2' AND uor > 0 AND field_id != '' AND group_name LIKE '%Procedure%'" .
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
 if(!empty($newdata['lbf_data']) && $form_id==0){
    $sql_form = sqlStatement("select max(form_id)as new_form from forms where form_name='Allcare Encounter Forms' AND formdir='LBF2'");
    $row_form = sqlFetchArray($sql_form);
    $new_fid  = $row_form['new_form'];
    $new_id1  = ++$new_fid;
    //echo "INSERT INTO  forms (date,encounter,form_name,form_id,pid,user,groupname,authorized,deleted,formdir) VALUES (NOW(),$ecounter1,'Allcare Encounter Forms',$new_id1,$pid1,'$_SESSION[authUser]','default',1,0,'LBF2')";
    $ins_form  = sqlStatement("INSERT INTO  forms (date,encounter,form_name,form_id,pid,user,groupname,authorized,deleted,formdir) VALUES (NOW(),$encounter,'Allcare Encounter Forms',$new_id1,$pid,'$provider','default',1,0,'LBF2')");
    $row1_form = sqlFetchArray($ins_form);
    $form_id   = $new_id1;
}
  
foreach($field_id1 as $val){ 
    $res1     = sqlstatement("select * from lbf_data lb INNER JOIN layout_options l ON l.field_id=lb.field_id where lb.form_id='$form_id' AND l.form_id='LBF2' AND l.group_name LIKE '%Procedure%' AND lb.field_id LIKE '%$val%' order by seq");
    $res_row1 = sqlFetchArray($res1);
    if(!empty($res_row1)){
        updateENC_forms($form_id, $newdata['lbf_data'][$val] ,$create=false,$encounter,$pid,$provider,$val);
    }else{
        updateENC_forms($form_id, $newdata['lbf_data'][$val] ,$create=true,$encounter,$pid,$provider,$val);
    }
}
 }
// Defaults for new orders.
$row = array(
  'provider_id' => $_SESSION['authUserID'],
  'date_ordered' => date('Y-m-d'),
  'date_collected' => date('Y-m-d H:i'),
);

if (! $encounter) { // comes from globals.php
 die("Internal error: we do not seem to be in an encounter!");
}

function cbvalue($cbname) {
    return $_POST[$cbname] ? '1' : '0';
}

function cbinput($name, $colname) {
    global $row;
    $ret  = "<input type='checkbox' name='$name' value='1'";
    if ($row[$colname]) $ret .= " checked";
    $ret .= " />";
    return $ret;
}

function cbcell($name, $desc, $colname) {
    return "<td width='25%' nowrap>" . cbinput($name, $colname) . "$desc</td>\n";
}

function QuotedOrNull($fld) {
    if (empty($fld)) return "NULL";
    return "'$fld'";
}

//$formid = formData('id', 'G') + 0;
$formid = $_REQUEST['id'];
// If Save or Transmit was clicked, save the info.
//
if ($_POST['bn_save'] || $_POST['bn_xmit']) {
    $ppid = formData('form_lab_id') + 0;

    $sets =
      "date_ordered = " . QuotedOrNull(formData('form_date_ordered'))     . ", " .
      "provider_id = " . (formData('form_provider_id') + 0)               . ", " .
      "lab_id = " . $ppid                                                 . ", " .
      "date_collected = " . QuotedOrNull(formData('form_date_collected')) . ", " .
      "order_priority = '" . formData('form_order_priority')              . "', " .
      "order_status = '" . formData('form_order_status')                  . "', " .
      "clinical_hx = '" . formData('form_clinical_hx')                    . "', " .
      "patient_instructions = '" . formData('form_patient_instructions')  . "', " .
      "patient_id = '" . $pid                                             . "', " .
      "encounter_id = '" . $encounter                                     . "'";

 // echo $sets;
  // If updating an existing form...
  //
    if ($formid) {
      $query = "UPDATE procedure_order SET $sets "  .
        "WHERE procedure_order_id = '$formid'";
      sqlStatement($query);
    }

  // If adding a new form...
  //
    else {

     $query = "INSERT INTO procedure_order SET $sets";
      $formid = sqlInsert($query);
      addForm($encounter, "Procedure Order", $formid, "procedure_order", $pid, $userauthorized);

    }

  // Remove any existing procedures and their answers for this order and
  // replace them from the form.

    sqlStatement("DELETE FROM procedure_answers WHERE procedure_order_id = ?",
      array($formid));
    sqlStatement("DELETE FROM procedure_order_code WHERE procedure_order_id = ?",
      array($formid));

    for ($i = 0; isset($_POST['form_proc_type'][$i]); ++$i) {
        $ptid = $_POST['form_proc_type'][$i] + 0;
        if ($ptid <= 0) continue;

        $prefix = "ans$i" . "_";

        $poseq = sqlInsert("INSERT INTO procedure_order_code SET ".
          "procedure_order_id = ?, " .
          "diagnoses = ?, " .
          "procedure_code = (SELECT procedure_code FROM procedure_type WHERE procedure_type_id = ?), " .
          "procedure_name = (SELECT name FROM procedure_type WHERE procedure_type_id = ?)",
          array($formid, strip_escape_custom($_POST['form_proc_type_diag'][$i]), $ptid, $ptid));

        $qres = sqlStatement("SELECT " .
          "q.procedure_code, q.question_code, q.options, q.fldtype " .
          "FROM procedure_type AS t " .
          "JOIN procedure_questions AS q ON q.lab_id = t.lab_id " .
          "AND q.procedure_code = t.procedure_code AND q.activity = 1 " .
          "WHERE t.procedure_type_id = ? " .
          "ORDER BY q.seq, q.question_text", array($ptid));

        while ($qrow = sqlFetchArray($qres)) {
            $options = trim($qrow['options']);
            $qcode = trim($qrow['question_code']);
            $fldtype = $qrow['fldtype'];
            $data = '';
            if ($fldtype == 'G') {
                if ($_POST["G1_$prefix$qcode"]) {
                      $data = $_POST["G1_$prefix$qcode"] * 7 + $_POST["G2_$prefix$qcode"];
                }
            }
            else {
                $data = $_POST["$prefix$qcode"];
            }
            if (!isset($data) || $data === '') continue;
            if (!is_array($data)) $data = array($data);
            foreach ($data as $datum) {
              // Note this will auto-assign the seq value.
                sqlStatement("INSERT INTO procedure_answers SET ".
                    "procedure_order_id = ?, " .
                    "procedure_order_seq = ?, " .
                    "question_code = ?, " .
                    "answer = ?",
                array($formid, $poseq, $qcode, strip_escape_custom($datum)));
            }
        }
    }

    $alertmsg = '';
    if ($_POST['bn_xmit']) { $hl7 = '';
          $alertmsg = gen_hl7_order($formid, $hl7);
          if (empty($alertmsg)) {
              $alertmsg = send_hl7_order($ppid, $hl7);
          }
          if (empty($alertmsg)) {
              sqlStatement("UPDATE procedure_order SET date_transmitted = NOW() WHERE " .
                  "procedure_order_id = ?", array($formid));
          }
    }

    formHeader("Redirecting....");
    if ($alertmsg) {
        echo "\n<script language='Javascript'>alert('";
        echo addslashes(xl('Transmit failed') . ': ' . $alertmsg);
        echo "')</script>\n";
    }
    function formJump_custom($provider,$encounter,$pid,$isSingleView,$isFromCharts,$formid,$form_id)
    {
        if($isSingleView == 1 && $isFromCharts == 0)
            echo "<script> window.close();window.opener.location.href = '../single_view_form.php?encounter=$encounter&pid=$pid';</script>";
        else if($isSingleView == 1 && $isFromCharts == 1)
            echo "<script>window.opener.datafromchildwindow($formid,$form_id);window.close();</script>";
        else
            echo "\n<script language='Javascript'> window.close(); window.opener.location.href = '../provider_incomplete_charts.php?provider=$provider&checkencounter=$encounter';</script>\n";
    }
    formJump_custom($provider,$encounter,$pid,$isSingleView,$isFromCharts,$formid,$form_id);
    formFooter();
    exit;
}

if ($formid) {
    $row = sqlQuery ("SELECT * FROM procedure_order WHERE " .
        "procedure_order_id = ?",
    array($formid)) ;
}

$enrow = sqlQuery("SELECT p.fname, p.mname, p.lname, fe.date FROM " .
    "form_encounter AS fe, forms AS f, patient_data AS p WHERE " .
    "p.pid = ? AND f.pid = p.pid AND f.encounter = ? AND " .
    "f.formdir = 'newpatient' AND f.deleted = 0 AND " .
    "fe.id = f.form_id LIMIT 1",
array($pid, $encounter));
?>
<html>
<head>
<?php html_header_show(); ?>
<link rel="stylesheet" href="<?php echo $css_header;?>" type="text/css" />

<style>

td {
 font-size:10pt;
}

.inputtext {
 padding-left:2px;
 padding-right:2px;
}

</style>

<style type="text/css">@import url(<?php echo $GLOBALS['webroot'] ?>/library/dynarch_calendar.css);</style>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/dynarch_calendar.js"></script>
<?php include_once("{$GLOBALS['srcdir']}/dynarch_calendar_en.inc.php"); ?>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/dynarch_calendar_setup.js"></script>

<script type="text/javascript" src="../../../library/dialog.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/textformat.js"></script>

<script language='JavaScript'>

// This invokes the find-procedure-type popup.
// formseq = 0-relative index in the form.
var gbl_formseq;
function sel_proc_type(formseq) {
 var f = document.forms[0];
 // if (!f.form_lab_id.value) {
 //  alert('<?php echo xls('Please select a procedure provider'); ?>');
 //  return;
 // }
 gbl_formseq = formseq;
 var ptvarname = 'form_proc_type[' + formseq + ']';
 /********************************************************************
 dlgopen('../../orders/types.php?popup=1' +
  '&labid=' + f.form_lab_id.value +
  '&order=' + f[ptvarname].value +
  '&formid=<?php echo $formid; ?>' +
  '&formseq=' + formseq,
  '_blank', 800, 500);
 ********************************************************************/
 // This replaces the above for an easier/faster order picker tool.
 window.open('find_order_popup.php' +
  '?labid=' + f.form_lab_id.value +
  '&order=' + f[ptvarname].value +
  '&formid=<?php echo $formid; ?>' +
  '&formseq=' + formseq,
  '_blank', 800, 500);
}

// This is for callback by the find-procedure-type popup.
// Sets both the selected type ID and its descriptive name.
function set_proc_type(typeid, typename) {
 var f = document.forms[0];
 var ptvarname = 'form_proc_type[' + gbl_formseq + ']';
 var ptdescname = 'form_proc_type_desc[' + gbl_formseq + ']';
 f[ptvarname].value = typeid;
 f[ptdescname].value = typename;
}

// This is also for callback by the find-procedure-type popup.
// Sets the contents of the table containing the form fields for questions.
function set_proc_html(s, js) {
 document.getElementById('qoetable[' + gbl_formseq + ']').innerHTML = s;
 eval(js);
}

// New lab selected so clear all procedures and questions from the form.
function lab_id_changed() {
 var f = document.forms[0];
 for (var i = 0; true; ++i) {
  var ix = '[' + i + ']';
  if (!f['form_proc_type' + ix]) break;
  f['form_proc_type' + ix].value = '-1';
  f['form_proc_type_desc' + ix].value = '';
  document.getElementById('qoetable' + ix).innerHTML = '';
 }
}

// Add a line for entry of another procedure.
function addProcLine() {
 var f = document.forms[0];
 var table = document.getElementById('proctable');
 // Compute i = next procedure index.
 var i = 0;
 for (; f['form_proc_type[' + i + ']']; ++i);
 var row = table.insertRow(table.rows.length);
 var cell = row.insertCell(0);
 cell.vAlign = 'top';
 cell.innerHTML = "<b><?php echo xls('Procedure'); ?> " + (i + 1) + ":</b>";
 var cell = row.insertCell(1);
 cell.vAlign = 'top';
 cell.innerHTML =
  "<input type='text' size='50' name='form_proc_type_desc[" + i + "]'" +
  " onclick='sel_proc_type(" + i + ")'" +
  " onfocus='this.blur()'" +
  " title='<?php echo xla('Click to select the desired procedure'); ?>'" +
  "  style='width:100%;cursor:pointer;cursor:hand' readonly />" +
  " <input type='hidden' name='form_proc_type[" + i + "]' value='-1' />" +
  "<br /><b><?php echo xla('Diagnoses'); ?>: </b>" +
  "<input type='text' size='50' name='form_proc_type_diag[" + i + "]'" +
  " onclick='sel_related(this.name)'" +
  " title='<?php echo xla('Click to add a diagnosis'); ?>'" +
  " onfocus='this.blur()'" +
  " style='cursor:pointer;cursor:hand' readonly />" +
  " <div style='width:95%;' id='qoetable[" + i + "]'></div>";
 sel_proc_type(i);
 return false;
}

// The name of the form field for find-code popup results.
var rcvarname;

// This is for callback by the find-code popup.
// Appends to or erases the current list of related codes.
function set_related(codetype, code, selector, codedesc) {
 var f = document.forms[0];
 var s = f[rcvarname].value;
 if (code) {
  if (s.length > 0) s += ';';
  s += codetype + ':' + code;
 } else {
  s = '';
 }
 f[rcvarname].value = s;
}

// This invokes the find-code popup.
function sel_related(varname) {
 rcvarname = varname;
 // codetype is just to make things easier and avoid mistakes.
 // Might be nice to have a lab parameter for acceptable code types.
 // Also note the controlling script here runs from interface/patient_file/encounter/.
 window.open('find_code_popup.php?codetype=<?php echo attr(collect_codetypes("diagnosis","csv")) ?>', '_blank', 500, 400);
}

var transmitting = false;

// Issue a Cancel/OK warning if a previously transmitted order is being transmitted again.
function validate(f) {
<?php if (!empty($row['date_transmitted'])) { ?>
 if (transmitting) {
  if (!confirm('<?php echo xls('This order was already transmitted on') . ' ' .
    addslashes($row['date_transmitted']) . '. ' .
    xls('Are you sure you want to transmit it again?'); ?>')) {
    return false;
  }
 }
<?php } ?>
 
 return true;
}

</script>

</head>

<body class="body_top">

<form method="post" action="new.php?id=<?php echo $formid ?>&encounter=<?php echo $encounter; ?>&isSingleView=<?php echo $isSingleView; ?>&isFromCharts=<?php echo $isFromCharts; ?>"
 onsubmit="return validate(this)">
<input type="hidden"  name="pid" id="pid" value="<?php echo $pid; ?>" />
<p class='title' style='margin-top:8px;margin-bottom:8px;text-align:center'>
<?php

   $sql_pname=sqlStatement("select CONCAT(lname,' ',fname) AS pname from  patient_data  where   pid='".$_REQUEST['pid']."'");
    $res_row1=sqlFetchArray($sql_pname);

    $dos_sql=sqlStatement("select * from form_encounter where encounter='".$_REQUEST['encounter']."'");
    $res_dos=sqlFetchArray($dos_sql);
    $dos=explode(" ",$res_dos['date']);

    $cat=sqlStatement("select * from openemr_postcalendar_categories where pc_catid='".$res_dos['pc_catid']."'");
    $res_cat=sqlFetchArray($cat);
    echo "<table style='border:0 !important'>";
    echo "<tr><td style='border:0 !important'><b>Patient Name: </b>".$res_row1['pname']."</td><td>&nbsp;</td><td style='border:0 !important'><b>Encounter: </b>".$_REQUEST['encounter']."</td></tr>";
    echo "<tr><td style='border:0 !important'><b>Date Of Service: </b>".$dos[0]."</td><td>&nbsp;</td><td style='border:0 !important'><b>Visit Category: </b>".$res_cat['pc_catname']."</td></tr>";
    echo "</table><br>"; 
  echo xl('Procedure Order for') . ' ';
  echo $enrow['fname'] . ' ' . $enrow['mname'] . ' ' . $enrow['lname'];
  echo ' ' . xl('on') . ' ' . oeFormatShortDate(substr($enrow['date'], 0, 10));
?>
</p>

<center>

<p>
<table border='1' width='95%' id='proctable'>

 <tr>
  <td width='1%' valign='top' nowrap><b><?php xl('Ordering Provider','e'); ?>:</b></td>
  <td valign='top'>
<?php
//generate_form_field(array('data_type'=>10,'field_id'=>'provider_id'),
//  $row['provider_id']);
 $query = "SELECT id, lname, fname FROM users WHERE ".
                                      "authorized = 1  AND id=$id1 ORDER BY lname, fname"; //(CHEMED) facility filter

$ures = sqlStatement($query);

echo "   <select name='form_provider_id'  id='form_provider_id'>\n";
echo "    <option value=''"; 
echo  "selected"; echo" >-- " . xl('Select') . " --\n";

while ($urow = sqlFetchArray($ures)) {
        $provid = $urow['id'];
        echo "    <option value='$provid'";
        if(!empty($id1)){

        if ($provid == $id1) echo " selected"; }
        echo ">" . $urow['fname'] . ' ' . $urow['lname'] . "\n";
}

echo "   </select>\n";
?>
  </td>
 </tr>

 <tr>
  <td width='1%' valign='top' nowrap><b><?php xl('Sending To','e'); ?>:</b></td>
  <td valign='top'>
   <select name='form_lab_id' onchange='lab_id_changed()'>
 <?php
  $ppres = sqlStatement("SELECT ppid, name FROM procedure_providers " .
    "ORDER BY name, ppid");
  while ($pprow = sqlFetchArray($ppres)) {
    echo "<option value='" . attr($pprow['ppid']) . "'";
    if ($pprow['ppid'] == $row['lab_id']) echo " selected";
    echo ">" . text($pprow['name']) . "</option>";
  }
?>
   </select>
  </td>
 </tr>

 <tr>
  <td width='1%' valign='top' nowrap><b><?php xl('Order Date','e'); ?>:</b></td>
  <td valign='top'>
<?php
    echo "<input type='text' size='10' name='form_date_ordered' id='form_date_ordered'" .
      " value='" . $row['date_ordered'] . "'" .
      " title='" . xl('Date of this order') . "'" .
      " onkeyup='datekeyup(this,mypcc)' onblur='dateblur(this,mypcc)'" .
      " />" .
      "<img src='$rootdir/pic/show_calendar.gif' align='absbottom' width='24' height='22'" .
      " id='img_date_ordered' border='0' alt='[?]' style='cursor:pointer'" .
      " title='" . xl('Click here to choose a date') . "' />";
?>
  </td>
 </tr>

 <tr>
  <td width='1%' valign='top' nowrap><b><?php xl('Internal Time Collected','e'); ?>:</b></td>
  <td valign='top'>
<?php
    echo "<input type='text' size='16' name='form_date_collected' id='form_date_collected'" .
      " value='" . substr($row['date_collected'], 0, 16) . "'" .
      " title='" . xl('Date and time that the sample was collected') . "'" .
      // " onkeyup='datekeyup(this,mypcc)' onblur='dateblur(this,mypcc)'" .
      " />" .
      "<img src='$rootdir/pic/show_calendar.gif' align='absbottom' width='24' height='22'" .
      " id='img_date_collected' border='0' alt='[?]' style='cursor:pointer'" .
      " title='" . xl('Click here to choose a date and time') . "' />";
?>
  </td>
 </tr>

 <tr>
  <td width='1%' valign='top' nowrap><b><?php xl('Priority','e'); ?>:</b></td>
  <td valign='top'>
<?php
generate_form_field(array('data_type'=>1,'field_id'=>'order_priority',
  'list_id'=>'ord_priority'), $row['order_priority']);
?>
  </td>
 </tr>

 <tr>
  <td width='1%' valign='top' nowrap><b><?php xl('Status','e'); ?>:</b></td>
  <td valign='top'>
<?php
generate_form_field(array('data_type'=>1,'field_id'=>'order_status',
  'list_id'=>'ord_status'), $row['order_status']);
?>
  </td>
 </tr>

 <tr>
  <td width='1%' valign='top' nowrap><b><?php xl('Clinical History','e'); ?>:</b></td>
  <td valign='top'>
   <input type='text' maxlength='255' name='form_clinical_hx' style='width:100%'
    class='inputtext' value='<?php echo attr($row['clinical_hx']); ?>' />
  </td>
 </tr>

 <!-- Will enable this later, nothing uses it yet. -->
 <tr style='display:none'>
  <td width='1%' valign='top' nowrap><b><?php xl('Patient Instructions','e'); ?>:</b></td>
  <td valign='top'>
   <textarea rows='3' cols='40' name='form_patient_instructions' style='width:100%'
    wrap='virtual' class='inputtext' /><?php echo $row['patient_instructions'] ?></textarea>
  </td>
 </tr>

<?php

  // This section merits some explanation. :)
  //
  // If any procedures have already been saved for this form, then a top-level table row is
  // created for each of them, and includes the relevant questions and any existing answers.
  // Otherwise a single empty table row is created for entering the first or only procedure.
  //
  // If a new procedure is selected or changed, the questions for it are (re)generated from
  // the dialog window from which the procedure is selected, via JavaScript.  The sel_proc_type
  // function and the types.php script that it invokes collaborate to support this feature.
  //
  // The generate_qoe_html function in qoe.inc.php contains logic to generate the HTML for
  // the questions, and can be invoked either from this script or from types.php.
  //
  // The $i counter that you see below is to resolve the need for unique names for form fields
  // that may occur for each of the multiple procedure requests within the same order.
  // procedure_order_seq serves a similar need for uniqueness at the database level.

  $oparr = array();
  if ($formid) {
    $opres = sqlStatement("SELECT " .
      "pc.procedure_order_seq, pc.procedure_code, pc.procedure_name, " .
      "pc.diagnoses, pt.procedure_type_id " .
      "FROM procedure_order_code AS pc " .
      "LEFT JOIN procedure_type AS pt ON pt.lab_id = ? AND " .
      "pt.procedure_code = pc.procedure_code " .
      "WHERE pc.procedure_order_id = ? " .
      "ORDER BY pc.procedure_order_seq",
      array($row['lab_id'], $formid));
    while ($oprow = sqlFetchArray($opres)) {
      $oparr[] = $oprow;
    }
  }
  if (empty($oparr)) $oparr[] = array('procedure_name' => '');

  $i = 0;
  foreach ($oparr as $oprow) {
    $ptid = -1; // -1 means no procedure is selected yet
    if (!empty($oprow['procedure_type_id'])) {
      $ptid = $oprow['procedure_type_id'];
    }
?>
 <tr>
  <td width='1%' valign='top'><b><?php echo xl('Procedure') . ' ' . ($i + 1); ?>:</b></td>
  <td valign='top'>
   <input type='text' size='50' name='form_proc_type_desc[<?php echo $i; ?>]'
    value='<?php echo attr($oprow['procedure_name']) ?>'
    onclick="sel_proc_type(<?php echo $i; ?>)"
    onfocus='this.blur()'
    title='<?php xla('Click to select the desired procedure','e'); ?>'
    style='width:100%;cursor:pointer;cursor:hand' readonly />
   <input type='hidden' name='form_proc_type[<?php echo $i; ?>]' value='<?php echo $ptid ?>' />
   <br /><b><?php echo xlt('Diagnoses'); ?>:</b>
   <input type='text' size='50' name='form_proc_type_diag[<?php echo $i; ?>]'
    value='<?php echo attr($oprow['diagnoses']) ?>' onclick='sel_related(this.name)'
    title='<?php echo xla('Click to add a diagnosis'); ?>'
    onfocus='this.blur()'
    style='cursor:pointer;cursor:hand' readonly />
   <!-- MSIE innerHTML property for a TABLE element is read-only, so using a DIV here. -->
   <div style='width:95%;' id='qoetable[<?php echo $i; ?>]'>
<?php
$qoe_init_javascript = '';
echo generate_qoe_html($ptid, $formid, $oprow['procedure_order_seq'], $i);
if ($qoe_init_javascript)
  echo "<script language='JavaScript'>$qoe_init_javascript</script>";
?>
   </div>
  </td>
 </tr>
<?php
    ++$i;
  }
?>
 <tr>
           <?php

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
  "WHERE form_id = 'LBF2' AND uor > 0 AND group_name LIKE '%PProcedure%'" .
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
  

        $res=sqlstatement("select * from lbf_data where field_id='$field_id' AND form_id=$form_id");
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
            echo "<div id='div_$group_seq_esc'>";
	//else				
            ///echo "<div class='tab' id='div_$group_seq_esc'>";
    echo " <table border='0' cellpadding='0'>\n";
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

  echo "</div>";

  }
end_group();

?>
</div>
 </tr>
</table>

<p>
<input type='hidden' name='mode' id='mode' value='add'/>  
<input type='hidden' name='formid' id='formid' value='<?php echo $form_id?>'/>
<input type='button' value='<?php echo xla('Add Procedure'); ?>' onclick="addProcLine()" />
&nbsp;
<input type='submit' id='bn_save' name='bn_save' value='<?php echo xla('Save'); ?>' onclick='transmitting = false;' />
&nbsp;
<input type='submit' id='bn_xmit' name='bn_xmit' value='<?php echo xla('Save and Transmit'); ?>' onclick='transmitting = true;' />
&nbsp;
<input type='button' value='<?php echo xla('Cancel'); ?>' onclick="window.close();" />
</p>

</center>

    <script language='JavaScript'>
        Calendar.setup({inputField:'form_date_ordered', ifFormat:'%Y-%m-%d',
         button:'img_date_ordered'});
        Calendar.setup({inputField:'form_date_collected', ifFormat:'%Y-%m-%d %H:%M',
         button:'img_date_collected', showsTime:true});
    </script>
</form>
</body>
</html>
<script language="JavaScript">
    <?php echo $date_init; ?>
</script>
