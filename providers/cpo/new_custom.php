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
include_once("$srcdir/api.inc");
require_once("$srcdir/patient.inc");
require_once("$srcdir/options.inc.php");
require_once("$srcdir/htmlspecialchars.inc.php");
include_once("$srcdir/acl.inc");

function formFetch_custom ($tableName, $id, $pid, $cols="*", $activity="1")
{
        // Run through escape_table_name() function to support dynamic form names in addition to mitigate sql table casing issues.
	return sqlQuery ( "select $cols from `" . escape_table_name($tableName) . "` where id=? and pid = ? and activity like ? order by date DESC LIMIT 0,1", array($id,$pid,$activity) ) ;
}
formHeader("Form:CPO Log");
//$returnurl = $GLOBALS['concurrent_layout'] ? 'encounter_top.php' : 'patient_encounter.php';

$pid=$_REQUEST['pid'];
$formid = 0 + (isset($_REQUEST['id']) ? $_REQUEST['id'] : '');
$obj = $formid ? formFetch_custom("tbl_form_cpo", $formid,$pid) : array();
$cpo_data2 = preg_replace_callback ( '!s:(\d+):"(.*?)";!',
    function($match) {
        return ($match[1] == strlen($match[2])) ? $match[0] : 's:' . strlen($match[2]) . ':"' . $match[2] . '";';
    },
$obj{"cpo_data"} );
    
$cpo_data = unserialize($cpo_data2);


$enc_formid=$_REQUEST['formid'];

$sql=sqlStatement("SELECT id, fname, lname, specialty FROM users " .
      "WHERE active = 1 AND ( info IS NULL OR info NOT LIKE '%Inactive%' ) " .
      "AND authorized = 1 AND username='".$provider."'" .
      "ORDER BY lname, fname"); 
$id=sqlFetchArray($sql);
$id1=$id['id'];
?>
<html>
<head> 
<?php html_header_show();?>
<script type="text/javascript" src="../../../library/dialog.js"></script>
<!-- pop up calendar -->
<style type="text/css">@import url(<?php echo $GLOBALS['webroot'] ?>/library/dynarch_calendar.css);</style>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/dynarch_calendar.js"></script>
<?php include_once("{$GLOBALS['srcdir']}/dynarch_calendar_en.inc.php"); ?>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/dynarch_calendar_setup.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/textformat.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/dialog.js"></script>
<link rel="stylesheet" href="<?php echo $css_header;?>" type="text/css">
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
<script language="javascript">
    function addRow(tableID){
        var table=document.getElementById(tableID);
        var rowCount=table.rows.length;
        var row=table.insertRow(rowCount);
        var colCount=table.rows[0].cells.length;
        var hidvalue = document.getElementById("noofrows").value = rowCount;
        for(var i=0;i<colCount;i++){
            var newcell=row.insertCell(i);
            newcell.innerHTML=table.rows[1].cells[i].innerHTML;
            newcell.childNodes[0].name=(newcell.childNodes[0].name)+rowCount;
            if(newcell.childNodes[0].id == 'start_date'){
                newcell.childNodes[0].id=(newcell.childNodes[0].id)+rowCount;
                newcell.childNodes[1].id=(newcell.childNodes[1].id)+rowCount;   
                //alert(newcell.childNodes[1].id);
                Calendar.setup({inputField:"start_date"+rowCount, ifFormat:"%Y-%m-%d", button:"img_start_date"+rowCount});
            }
            if(newcell.childNodes[0].id == 'timeinterval' ){
                newcell.childNodes[0].id=(newcell.childNodes[0].id)+rowCount;
            }
            switch(newcell.childNodes[0].type){
                case "text":
                case "textarea":newcell.childNodes[0].value="";
                            break;
                case "checkbox":newcell.childNodes[0].checked=false;
                                break;
                case "select-one":newcell.childNodes[0].selectedIndex=0;
                                break;
            }
            
        }
        
    }
function deleteRow(tableID){
    try{
        var table=document.getElementById(tableID);
        var rowCount=table.rows.length;
        for(var i=0;i<rowCount;i++){
            var row=table.rows[i];
            var chkbox=row.cells[0].childNodes[0];
            if(null!=chkbox&&true==chkbox.checked){
                if(rowCount<=2){
                    alert("Cannot delete all the rows.");
                    break;
                }
                table.deleteRow(i);
                var count = rowCount--;
                document.getElementById('noofrows').value = count-2;
                i--;
            }
        }
    }catch(e){
        alert(e);
    }
}
$.noConflict();
jQuery(window).load(function(){
    var countrows = jQuery("#noofrows").val();
    if(countrows != ''){
        for(var i=1; i<=countrows; i++){
            if(i == 1){
                var countval = '';
            }else{
                var countval = i;
            }
            Calendar.setup({inputField:"start_date"+countval, ifFormat:"%Y-%m-%d", button:"img_start_date"+countval});
        }
    }
});
function addTime(){
    var countrows = jQuery("#noofrows").val();
    var timeval = 0;
    var timevalue = 0;
    if(countrows != ''){
        for(var i=1; i<=countrows; i++){
            if(i == 1){
                var countval = '';
            }else{
                var countval = i;
            }
            timevalue = jQuery("#timeinterval"+countval+ " option:selected").text();
            if(isNaN(timevalue)){
                timevalue = 0; 
            }
            timeval = parseInt(timeval) + parseInt(timevalue);
        }
        jQuery("#timespent").html(timeval);
        jQuery("#totaltime").val(timeval);
    }
}
function previewpost(){
    var datastring = jQuery("#my_form").serialize();
   // top.restoreSession();
    location.href = 'print.php?'+datastring;
}

</script>
 
</head>
<body class="body_top">
    <p><span class="forms-title"><b><?php echo xlt('CPO Log '); ?></b></span></p>
<?php
if($_REQUEST['isSingleView'] == 1)
    $isSingleView = 1;
else
    $isSingleView = 0;

if($_REQUEST['isFromCharts'] == 1)
    $isFromCharts = 1;
else
    $isFromCharts = 0;
echo "<form method='post' name='my_form' id='my_form'" .
  "action='save_custom.php?id=" . attr($formid) ."&encounter=".$_REQUEST['encounter']."&isSingleView=$isSingleView&isFromCharts=$isFromCharts'>\n";
    $dos_sql=sqlStatement("select * from form_encounter where encounter='".$_REQUEST['encounter']."'");
    $res_dos=sqlFetchArray($dos_sql);
    $dos=explode(" ",$res_dos['date']);

    $cat=sqlStatement("select * from openemr_postcalendar_categories where pc_catid='".$res_dos['pc_catid']."'");
    $res_cat=sqlFetchArray($cat);
    echo "<table>";
    echo "<tr><td><b>Patient Name: </b>"; 
        if (is_numeric($pid)) {
            $result = getPatientData($pid, "title,fname,lname,squad");
            echo htmlspecialchars(text($result['title'])." ".text($result['fname'])." ".text($result['lname']));
        }      
    echo"</td><td>&nbsp;</td><td><b>Encounter: </b>".$_REQUEST['encounter']."</td></tr>";
    echo "<tr><td><b>Date Of Service: </b>".$dos[0]."</td><td>&nbsp;</td><td><b>Visit Category: </b>".$res_cat['pc_catname']."</td></tr>";
    echo "</table><br>"; 
?><input type="hidden" name="patientname" value="<?php echo htmlspecialchars(text($result['title'])." ".text($result['fname'])." ".text($result['lname'])); ?>">
  <input type="hidden" name="pid" value="<?php echo $pid; ?>">
  <input type="hidden" name="encounter" value="<?php echo $_REQUEST['encounter']; ?>">
  <input type="hidden" name="formid" id="formid" value="<?php echo $enc_formid; ?>" />
 <br><br>
 <input type="button" value="Add Row" onclick="addRow('dataTable');">
 
    <input type="button" value="Delete Row" onclick="deleteRow('dataTable')">
    <br><br>
    <table id="dataTable" width="350px" border="1" style ="border-collapse: collapse;">
        <thead>
             <tr>
                <th></th>
                <th> Type of Oversight </th>
                <th> Date</th>
                <th> Minutes </th>
                <th> Users </th>
                <th> Location </th>
                <th> Description </th>
                <th> Reference </th>
            </tr>
        </thead>
        <tbody>
           <?php 
            if($formid != 0):
                $timeinterval_val2 = 0;
                for($i=1; $i<=count($cpo_data); $i++){ 
                    if($i == 1):
                        $cpotype     = "cpotype";
                        $reference   = "reference";
                        $description = "description";
                        $start_date  = "start_date";
                        $img_start_date    = "img_start_date";
                        $timeinterval    = "timeinterval";
                        $location    = "location";
                        $users    = "users";
                    else:
                        $cpotype     = "cpotype".$i;
                        $reference   = "reference".$i;
                        $description = "description".$i;
                        $start_date  = "start_date".$i;
                        $img_start_date    = "img_start_date".$i;
                        $timeinterval    = "timeinterval".$i;
                        $location    = "location".$i;
                        $users    = "users".$i;
                    endif;
                    foreach ($cpo_data[$i-1] as $key => $value) {
                        ${$key."_val"} = $value;
                        if($key == 'timeinterval' && $value != ''):
                            $ures = sqlStatement("SELECT title FROM list_options WHERE option_id= '$value' and list_id = 'Time_Interval'");
                            while ($urow = sqlFetchArray($ures)) {
                                $title = $urow['title'];
                            }
                            if($title == ''):
                                $title = 0;
                            endif;
                            $timeinterval_val2 = $timeinterval_val2 + $title;
                        endif;
                    }
                ?>
               <tr>
                <td><input type="checkbox" name="chk"></td>
                <td><select class='select' name="<?php echo $cpotype ; ?>" id="cpotype" >
                        <option value="">---Select type---</option>
                        <?php 
                        $sql = sqlStatement ("SELECT * FROM list_options WHERE list_id = 'CPO_types'");
                        while ($row = mysql_fetch_array($sql)) {
                            echo "<option value = '".$row['option_id']."'";
                            if($cpotype_val == $row['option_id'] ) echo "selected";
                            echo ">".$row['title']."</option>";
                        }
                        ?>
                    </select></td>
                <td><input type='text' size='10' name='<?php echo $start_date ; ?>' id='<?php echo $start_date ; ?>' <?php echo attr($disabled); ?>
                    value='<?php echo $start_date_val; ?>'   
                    title='<?php echo xla('yyyy-mm-dd Date of service'); ?>'
                    onkeyup='datekeyup(this,mypcc)' onblur='dateblur(this,mypcc)' /><img src='../../interface/pic/show_calendar.gif' align='absbottom' width='24' height='22'
                    id='<?php echo $img_start_date ; ?>' border='0' alt='[?]' style='cursor:pointer;cursor:hand'
                    title='<?php echo xla('Click here to choose a date'); ?>'></td>
                <td><select class='select' name="<?php echo $timeinterval; ?>" id="<?php echo $timeinterval; ?>" onchange="addTime();" >
                        <option value="">Select Duration</option>
                        <?php 
                        $sql = sqlStatement ("SELECT * FROM list_options WHERE list_id = 'Time_Interval'");
                        while ($row = mysql_fetch_array($sql)) {
                            echo "<option value = '".$row['option_id']."'";
                            if($timeinterval_val == $row['option_id'] ) echo "selected";
                            echo ">".$row['title']."</option>";
                        }
                        ?>
                    </select></td>
                    <td><select class='select' name="<?php echo $users; ?>" id="<?php echo $users; ?>" >
                        <option value="">Select</option>
                        <?php 
                        $sql2 = sqlStatement ("SELECT id, CONCAT(fname,' ', lname) as name FROM users WHERE username <> '' AND  active= 1");
                        while ($row2 = mysql_fetch_array($sql2)) {
                            echo "<option value = '".$row2['id']."'";
                            if($users_val == $row2['id'] ) echo "selected";
                            echo ">".$row2['name']."</option>";
                        }
                        ?>
                    </select></td>    
                <td><textarea  name="<?php echo $location ; ?>" rows="4" cols="50"  ><?php echo $location_val; ?></textarea></td>  
                <td><textarea name="<?php echo $description ; ?>" rows="4" cols="50"  ><?php echo $description_val; ?></textarea></td>
                <td><textarea name="<?php echo $reference ; ?>" rows="4" cols="50" ><?php echo $reference_val; ?></textarea></td>
            </tr>
            <?php 
            } 
            else:
                ?>
                <tr>
                <td><input type="checkbox" name="chk"></td>
                <td><select class='select' name="cpotype" id="cpotype" >
                        <option value="">---Select type---</option>
                        <?php 
                        $sql = sqlStatement ("SELECT * FROM list_options WHERE list_id = 'CPO_types'");
                        while ($row = mysql_fetch_array($sql)) {
                            echo "<option value = '".$row['option_id']."'>".$row['title']."</option>";
                        }
                        ?>
                    </select></td>
                <td><input type='text' size='10' name='start_date' id='start_date' <?php echo attr($disabled); ?>
                    value=''   
                    title='<?php echo xla('yyyy-mm-dd Date of service'); ?>'
                    onkeyup='datekeyup(this,mypcc)' onblur='dateblur(this,mypcc)' /><img src='../../interface/pic/show_calendar.gif' align='absbottom' width='24' height='22'
                    id='img_start_date' border='0' alt='[?]' style='cursor:pointer;cursor:hand'
                    title='<?php echo xla('Click here to choose a date'); ?>'></td>
                <td><select class='select' name="timeinterval" id="timeinterval" onchange="addTime();">
                        <option value="">Select Duration</option>
                        <?php 
                        $sql = sqlStatement ("SELECT * FROM list_options WHERE list_id = 'Time_Interval'");
                        while ($row = mysql_fetch_array($sql)) {
                            echo "<option value = '".$row['option_id']."'>".$row['title']."</option>";
                        }
                        ?>
                    </select></td>
                    <td><select class='select' name="users" id="users" >
                        <option value="">Select</option>
                        <?php 
                        $sql2 = sqlStatement ("SELECT id, CONCAT(fname,' ', lname) as name FROM users WHERE username <> '' AND  active= 1");
                        while ($row2 = mysql_fetch_array($sql2)) {
                            //echo "<option value = '".$row2['id']."'>".$row2['name']."</option>";
                            echo "<option value = '".$row2['id']."'";
                            if($id1 == $row2['id'] ) echo "selected";
                            echo ">".$row2['name']."</option>";
                        }
                        ?>
                    </select></td>    
                <td><textarea  name="location" rows="4" cols="50"  ><?php echo $location_val; ?></textarea></td> 
                <td><textarea  name="description" rows="4" cols="50"  ><?php echo $description_val; ?></textarea></td>
                <td><textarea name="reference" rows="4" cols="50"  ><?php echo $reference_val; ?></textarea></td>
            </tr>
                <?php 
            endif; 
            ?>
    </tbody></table>
    <br><input type="hidden" name="totaltime" id="totaltime" value="<?php if($timeinterval_val2 == '') echo 0; else echo $timeinterval_val2; ?>" />
    <?php 
    echo "<table border = '0'";
    echo "<tr><td><b> Total: <span id='timespent'> ";if($timeinterval_val2 == '') echo 0; else echo $timeinterval_val2;echo " </span> minutes<b></td></tr>";
    $providercheck = sqlStatement("SELECT processproviders FROM tbl_user_custom_attr_1to1 WHERE userid= ".$id1);
    while($providercheck2 = sqlFetchArray($providercheck)){
        $authcheck = $providercheck2['processproviders'];
    }
    if ($authcheck == 'YES') {
    echo "<tr><td width='70%'>";
    echo xlt('NP/Physician Signature:' ); 
    $ures = sqlStatement("SELECT id, fname, lname, specialty FROM users " .
      "WHERE active = 1 AND ( info IS NULL OR info NOT LIKE '%Inactive%' ) " .
      "AND authorized = 1 AND id=$id1 " .
      "ORDER BY lname, fname");
    echo "<select name='provider_id' id='provider_id' title='$description'>";
        echo "<option value=''>Unassigned</option>";
        while ($urow = sqlFetchArray($ures)) {
            echo $uname = htmlspecialchars( $urow['fname'] . ' ' . $urow['lname'], ENT_NOQUOTES);
            $optionId = htmlspecialchars( $urow['id'], ENT_QUOTES);
            echo "<option value='$optionId'";
            if ($urow['id'] == $obj{"provider_id"}) echo " selected";
            echo ">$uname</option>";
        }
    
    
    echo "</select>";
    echo"</td><td width='30%'>";
    echo xlt('Date:' ); 
    ?> 
    <input type='text' size='10' name='signed_date' id='signed_date' <?php echo attr($disabled); ?>;
        value='<?php echo attr($obj{"signed_date"}); ?>'   
        title='<?php echo xla('yyyy-mm-dd Date of service'); ?>'
        onkeyup='datekeyup(this,mypcc)' onblur='dateblur(this,mypcc)' />
        <img src='../../interface/pic/show_calendar.gif' align='absbottom' width='24' height='22'
        id='img_signed_date' border='0' alt='[?]' style='cursor:pointer;cursor:hand'
        title='<?php echo xla('Click here to choose a date'); ?>'>
    <?php
    echo "</td><tr></table>";
    }else{
        ?>
        <input type="hidden" value="<?php echo $obj{"provider_id"}; ?>" name='provider_id' id='provider_id' />
        <input type='hidden' name='signed_date' id='signed_date' <?php echo attr($disabled); ?>;
        value='<?php echo attr($obj{"signed_date"}); ?>'   
        title='<?php echo xla('yyyy-mm-dd Date of service'); ?>'
        onkeyup='datekeyup(this,mypcc)' onblur='dateblur(this,mypcc)' />
    <?php 
    }
    ?>
    <!--form_status               -->
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
  "WHERE form_id = 'LBF2' AND uor > 0 AND group_name LIKE '%CPO%' AND  field_id IN ('cpo_stat','cpo_review')" .
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
  

        $res=sqlstatement("select * from lbf_data where field_id='$field_id' AND form_id=$enc_formid");
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
    <input type="hidden" id= "noofrows" name="noofrows" value = "<?php if($obj{"count"}== '') echo 1; else echo $obj{"count"} ; ?>">
    <input type="hidden" id="mode" name="mode" value="add" />
    <input type='submit'  value='<?php echo xlt('Save');?>' class="button-css">&nbsp;
    <!--<input type='button'  value="Print" onclick="window.print()" class="button-css">&nbsp;-->
   <?php  require_once '../Mobile_Detect.php';
          $detect = new Mobile_Detect;  
          if($detect->isMobile()) { ?>
    <input type='button' class="button-css" value='<?php echo xlt('Cancel');?>'
        onclick="window.opener.location.href = '../provider_incomplete_charts.php';"  />
      <?php }else { ?>
          <input type='button' class="button-css" value='<?php echo xlt('Cancel');?>'
        onclick="window.close();"  />
      <?php } ?>
    <br><br>
     <a href="javascript:;" <?php if (!$GLOBALS['concurrent_layout']) echo "target='Main'"; ?> class="css_button" onclick="previewpost();">
                <span><?php echo htmlspecialchars( xl('Print'), ENT_NOQUOTES); ?></span>
                <br><br>                               
</form>

<script language="javascript">
/* required for popup calendar */
Calendar.setup({inputField:"start_date", ifFormat:"%Y-%m-%d", button:"img_start_date"});
if (document.forms[0].signed_date){
    Calendar.setup({inputField:"signed_date", ifFormat:"%Y-%m-%d", button:"img_signed_date"});
}
</script>
<?php
formFooter();
?>
