<?php
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.

// add_transaction is a misnomer, as this script will now also edit
// existing transactions.

//SANITIZE ALL ESCAPES
$sanitize_all_escapes=true;
//

//STOP FAKE REGISTER GLOBALS
$fake_register_globals=false;
//

require_once("../globals.php");
require_once("$srcdir/f2f_lib.php");
require_once("$srcdir/options.inc.php");
require_once("$srcdir/amc.php");


 $id = empty($_REQUEST['f2fid']) ? 0 : $_REQUEST['f2fid'] + 0;
 $mode    = empty($_POST['mode' ]) ? '' : $_POST['mode' ];
 //edit
 $inmode    = $_GET['inmode'];
 $encounter_id= $_REQUEST['encounter_id'];
 $pid1=$_REQUEST['pid1'];
 //add
 $mode1=$_REQUEST['mode1'];
 $enc1=$_GLOBALS['encounter'];
 $ab1=str_replace("?reload"," ","$mode1");
 
 
 $body_onload_code=""; 
 $form_id=$_REQUEST['form_id'];
 $form_name=$_REQUEST['form_name'];
//create
 $inmode1    = $_GET['inmode1'];
 $encounter=$_REQUEST['enc'];
 $pname=$_REQUEST['pid'];
 
//echo $pname;
/*$enc=sqlStatement("SELECT DISTINCT tff .*
                                    FROM tbl_form_facetoface tff
                                    INNER JOIN forms f ON tff.id = f.form_id
                                    INNER JOIN tbl_allcare_formflag flg 
                                    INNER JOIN tbl_form_facetoface_transactions ft       
                                    WHERE tff.pid ='$pid'
                                    AND flg.finalized = 'Y'
                                    AND f.deleted = '0'
                                   AND f.pid ='$pid' AND tff.date_of_service= '".$_POST['form_date_of_service']."'");*/
$enc=sqlStatement("SELECT fe.encounter,f.* 
                FROM forms f
                INNER JOIN lbf_data lb ON lb.form_id = f.form_id
                INNER JOIN form_encounter fe ON fe.encounter = f.encounter
                WHERE f.deleted=0 AND f.formdir = 'LBF2'
                AND fe.pid ='$pid' AND fe.date='".$_POST['form_date_of_service']."' AND lb.field_id='f2f_stat' AND lb.field_value='finalized'");


$encrow=sqlFetchArray($enc);

 //echo $encrow['form_id'];
//echo $encrow['encounter'];
//$EncDos=explode(';',$_POST['form_date_of_service']);
//echo $EncDos[0];

if ($mode) {   
  /**use sql placemaker**/
    
// $sets1 = "date_of_service =?, refer_to = ?, notes = ? updated_date = ?";
 //$sqlBindArray = array($_POST['form_date_of_service'], $_POST['form_refer_to'], $_POST['form_notes']);

 /* $sqlBindArray=array(); 
  $fres = sqlStatement("SELECT * FROM layout_options " .
    "WHERE form_id = 'F2F' AND uor > 0 AND field_id != '' " .
    "ORDER BY group_name, seq");
  while ($frow = sqlFetchArray($fres)) {
    $data_type = $frow['data_type'];
    $field_id  = $frow['field_id'];
    $value = $_POST["form_$field_id"];
      $sets .=  add_escape_custom($field_id) . " = ?"."," ;
      array_push($sqlBindArray, $value);
  }
  
 
  
  //print_r($_SESSION);print_r($_POST);
   
     if ($id) {       
    //use sql placemaker
    array_push($sqlBindArray,date("Y-m-d"),$id);
     $sets .= " updated_date = ?";
     $sets1= rtrim($sets,',');
    sqlStatement("UPDATE tbl_form_facetoface_transactions SET $sets1 WHERE id = ?", $sqlBindArray);
  }
  else {
    //use sql placemaker
      
    array_push($sqlBindArray,$pid,$encrow['encounter'],date("Y-m-d"),date("Y-m-d"),$encrow['form_id']);
    $sets .= " pid = ?".","."encounter = ?".","."created_date = ?".","."updated_date = ?".","."form_id =?";
    $id = sqlInsert("INSERT INTO tbl_form_facetoface_transactions SET $sets", $sqlBindArray);   
  }     */
    
    
$newdata = array();
$newdata['tbl_form_facetoface_transactions']['pid'] = $pid;
$newdata['tbl_form_facetoface_transactions']['form_id'] = $encrow['form_id'];
$newdata['tbl_form_facetoface_transactions']['encounter'] = $encrow['encounter'];
$fres = sqlStatement("SELECT * FROM layout_options " .
  "WHERE form_id = 'F2F' AND uor > 0 AND field_id != '' " .
  "ORDER BY group_name, seq");
while ($frow = sqlFetchArray($fres)) {
  $data_type = $frow['data_type'];
  $field_id  = $frow['field_id'];
  // $value  = '';
  $colname = $field_id;
  $table = 'tbl_form_facetoface_transactions';
  
  // if (isset($_POST["form_$field_id"])) $value = $_POST["form_$field_id"];
  $value = get_layout_form_value($frow);

  $newdata[$table][$colname] = $value;
}

if($id)
{ 
   updateF2FForm($id, $newdata['tbl_form_facetoface_transactions'] ,$create=false);
}
else
{  
    updateF2FForm($id, $newdata['tbl_form_facetoface_transactions'],$create=true);

}  
  if ($GLOBALS['concurrent_layout'])
    $body_onload_code = "javascript:location.href='add_f2f.php';";
  else
    $body_onload_code = "javascript:parent.Layout-Based Visit Forms.location.href='add_f2f.php';";
}

  
  
  
/************************************
//Migrated this to the list_options engine (transactions list)
$trans_types = array(
  'Referral'          => xl('Referral'),
  'Patient Request'   => xl('Patient Request'),
  'Physician Request' => xl('Physician Request'),
  'Legal'             => xl('Legal'),
  'Billing'           => xl('Billing'),
);
************************************/

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

$trow = $id ?getF2FById($id) : array();
//print_r($trow);

?>
<html>
    <title>Documentation of Face-to-Face Encounter</title>    
<head>
<?php html_header_show(); ?>

<link rel='stylesheet' href="<?php echo $css_header;?>" type="text/css">
<link rel="stylesheet" type="text/css" href="../../../library/js/fancybox/jquery.fancybox-1.2.6.css" media="screen" />

<style type="text/css">@import url(../../../library/dynarch_calendar.css);</style>
<script type="text/javascript" src="../../../library/dynarch_calendar.js"></script>
<?php include_once("{$GLOBALS['srcdir']}/dynarch_calendar_en.inc.php"); ?>
<script type="text/javascript" src="../../../library/dynarch_calendar_setup.js"></script>
<script type="text/javascript" src="../../../library/textformat.js"></script>
<script type="text/javascript" src="../../../library/dialog.js"></script>

<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/jquery.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/common.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/jquery.1.3.2.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/fancybox/jquery.fancybox-1.2.6.js"></script>
<script type="text/javascript">
$(document).ready(function(){
    tabbify();
    enable_modals();
});
</script>
<script language="JavaScript">
function divclick(cb, divid) {
 var divstyle = document.getElementById(divid).style;
 if (cb.checked) {
  divstyle.display = 'block';
 } else {
  divstyle.display = 'none';
 }
 return true;
}

// Process click on Delete link.
function deleteme() {
// onclick='return deleteme()'
 dlgopen('../deleter.php?f2fid=<?php echo htmlspecialchars( $id, ENT_QUOTES); ?>', '_blank', 500, 450);
 return false;
}

// Called by the deleteme.php window on a successful delete.
function imdeleted() {
 top.restoreSession();
 location.href = 'summary/add_face_to_face.php';
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

// Validation logic for form submission.
function validate(f) {
    
 var errCount = 0;
 var errMsgs = new Array();

 
    <?php generate_layout_validation('F2F'); ?>


 var msg = "";
 msg += "<?php echo htmlspecialchars( xl('The following fields are required'), ENT_QUOTES); ?>:\n\n";
 for ( var i = 0; i < errMsgs.length; i++ ) {
	msg += errMsgs[i] + "\n";
 }
 msg += "\n<?php echo htmlspecialchars( xl('Please fill them in before continuing.'), ENT_QUOTES); ?>";

 if ( errMsgs.length > 0 ) {
	alert(msg);
 }
 
  if( jQuery('#form_date_of_service').val()==='0')
     {
         alert('Date of Service is empty');
         return false;
     }         
     
 return errMsgs.length < 1;
}

function submitme() {
 var f = document.forms['new_f2f'];
 if (validate(f)) {
  //top.restoreSession();
  f.submit();
 }
}


</script>


<style type="text/css">
div.tab {
	height: auto;
	width: auto;
}
</style>

</head>
<body class="body_top" onload="<?php echo $body_onload_code; ?>" >
<form name='new_f2f' method='post' action='f2f_form.php?f2fid=<?php echo htmlspecialchars( $id, ENT_QUOTES); ?>' onsubmit='return validate(this)'>
<input type='hidden' id="hdnmode" name='mode' value='add'>
<input type='hidden' id="hdnEncId" name='hdnEncId' value='<?php echo $encounter_id;?>'>


	<table>
	    <tr>
            <td>
                  <a href="javascript:;"  <?php if (!$GLOBALS['concurrent_layout']) echo "target='Main'"; ?> class="css_button" onclick="submitme();">
                    <span><?php echo htmlspecialchars( xl('Save'), ENT_NOQUOTES); ?></span>
                 </a>
             </td>
             <td>
                <a href="f2f_encounters_report.php"  <?php if (!$GLOBALS['concurrent_layout']) echo "target='Main'"; ?> class="css_button" >
                    <span><?php echo htmlspecialchars( xl('Cancel'), ENT_NOQUOTES); ?></span>
                </a>
            </td>
        </tr>
	</table>

   <?php if($ab1=='add') { ?>
                        <span><?php echo htmlspecialchars( xl('Patient Name'), ENT_NOQUOTES); ?>:</span>
                        <?php 
                                $getPatientName=sqlStatement("SELECT CONCAT(fname,' ',lname) AS pname2 FROM patient_data WHERE pid='".$pid."'");
                                $resPatientName=sqlFetchArray($getPatientName);
                            ?>
                        <span class='bold'><?php echo htmlspecialchars( xl($resPatientName['pname2']), ENT_NOQUOTES); ?></span><br>
                                <?php $getenc=sqlStatement("SELECT encounter from form_encounter WHERE pid='".$pid."'");
                                 $resenc=sqlFetchArray($getenc); ?>

                        <span><?php echo htmlspecialchars( xl('Encounter Id'), ENT_NOQUOTES); ?>:</span>
                        <span class='bold'><?php echo htmlspecialchars( xl($resenc['encounter']), ENT_NOQUOTES); ?></span>

   <?php } else if($inmode1=='create') { 
                         $getPatientName1=sqlStatement("SELECT CONCAT(fname,' ',lname) AS pname2 FROM patient_data WHERE pid='".$pname."'");
                          $resPatientName1=sqlFetchArray($getPatientName1); ?>
                        <span><?php echo htmlspecialchars( xl('Patient Name'), ENT_NOQUOTES); ?>:</span>
                        <span class='bold'><?php echo htmlspecialchars( xl( $resPatientName1['pname2']), ENT_NOQUOTES);?></span><br>
                               <?php $getenc1=sqlStatement("SELECT encounter from form_encounter WHERE pid='".$pname."'");
                                 $resenc1=sqlFetchArray($getenc1); ?>
                        
                         <span><?php echo htmlspecialchars( xl('Encounter Id'), ENT_NOQUOTES); ?>:</span>
                        <span class='bold'><?php echo htmlspecialchars( xl($resenc1['encounter']), ENT_NOQUOTES); ?></span>
    <?php } else if($inmode=='edit'){?>
                        <span><?php echo htmlspecialchars( xl('Patient Name'), ENT_NOQUOTES); ?>:</span>
                            <?php 
                                $getPatientName=sqlStatement("SELECT CONCAT(fname,' ',lname) AS pname1 FROM patient_data WHERE pid='".$pid1."'");
                                $resPatientName=sqlFetchArray($getPatientName);
                            ?>

                        <span class='bold'><?php echo htmlspecialchars( xl($resPatientName['pname1']), ENT_NOQUOTES); ?></span><br>
   
                        <span><?php echo htmlspecialchars( xl('Encounter Id'), ENT_NOQUOTES); ?>:</span>
                        <span class='bold'><?php echo htmlspecialchars( xl($encounter_id), ENT_NOQUOTES); ?></span>
    <?php } else { ?>
         <span><?php echo htmlspecialchars( xl('Patient Name'), ENT_NOQUOTES); ?>:</span>
        <?php 
            $getPatientName=sqlStatement("SELECT CONCAT(fname,' ',lname) AS pname1 FROM patient_data WHERE pid='".$pid."'");
            $resPatientName=sqlFetchArray($getPatientName);
        ?>
        
    <span class='bold'><?php echo htmlspecialchars( xl($resPatientName['pname1']), ENT_NOQUOTES); ?></span><br>
      <?php $getenc2=sqlStatement("SELECT encounter from form_encounter WHERE pid='".$pid."'");
                                 $resenc2=sqlFetchArray($getenc2); ?>
     <span><?php echo htmlspecialchars( xl('Encounter Id'), ENT_NOQUOTES); ?>:</span>
    <span class='bold'><?php echo htmlspecialchars( xl($resenc2['encounter']), ENT_NOQUOTES); ?></span>
   <?php }
?>
  
    
    
        
       
	
<div id='f2fdiv'>
   

					<div id="Face_To_Face">
						<ul class="tabNav">
<?php
$fres = sqlStatement("SELECT * FROM layout_options " .
  "WHERE form_id = 'F2F' AND uor > 0 " .
  "ORDER BY group_name, seq");
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

  

  // Handle a data category (group) change.
  if (strcmp($this_group, $last_group) != 0) {
    $group_seq  = substr($this_group, 0, 1);
    $group_name = substr($this_group, 1);
    $last_group = $this_group;
	if($group_seq==1)	echo "<li class='current'>";
	else				echo "<li class=''>";
        $group_seq_esc = htmlspecialchars( $group_seq, ENT_QUOTES);
        $group_name_show = htmlspecialchars( xl_layout_label($group_name), ENT_NOQUOTES);
	echo "<a href='' id='div_$group_seq_esc'>".
	    "$group_name_show</a></li>";
  }
  ++$item_count;
}
?>
						</ul>
						<div class="tabContainer">							
 <?php
$fres = sqlStatement("SELECT * FROM layout_options " .
  "WHERE form_id = 'F2F' AND uor > 0 " .
  "ORDER BY group_name, seq");
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

  
 $currvalue  = '';
  if (isset($trow[$field_id])) $currvalue = $trow[$field_id];
  // Handle a data category (group) change.
  //print_r($currvalue);
// Handle a data category (group) change.
  if (strcmp($this_group, $last_group) != 0) {
    end_group();
   $group_seq  = substr($this_group, 0, 1);
   $group_name = substr($this_group, 1);
   $last_group = $this_group;
   $group_seq_esc = htmlspecialchars( $group_seq, ENT_QUOTES);
	if($group_seq==1)	echo "<div class='tab current' id='div_$group_seq_esc'>";
	else				echo "<div class='tab' id='div_$group_seq_esc'>";
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
</div></div>
</div>
<!-- include support for the list-add selectbox feature -->
<?php include $GLOBALS['fileroot']."/library/options_listadd.inc"; ?>
</form>
 <?php 
 if($inmode1=='create')
{
    $final_sql=sqlStatement("select field_value from lbf_data where field_id='f2f_stat' AND form_id='".$form_id."'");
    $row1=sqlFetchArray($final_sql);
    if(!empty($row1)) {
       // echo "update lbf_data SET field_value='finalized' where field_id='f2f_stat' AND form_id='".$form_id."'";
        $res=sqlStatement("update lbf_data SET field_value='finalized' where field_id='f2f_stat' AND form_id='".$form_id."'");
        $row=sqlFetchArray($res);
    } else {
       // echo "insert into lbf_data (field_value) values ('finalized') where field_id='f2f_stat' AND form_id='".$form_id."'";
        $res_ins=sqlStatement("insert into lbf_data (form_id,field_id,field_value) values ($form_id,'f2f_stat','finalized') ");
        $row_ins=sqlFetchArray($res_ins);
    }
   
    $res1=sqlStatement("SELECT DISTINCT(fe.date)as date,DATE_FORMAT(fe.date,'%m/%d/%Y') as fdate
                FROM forms f
                INNER JOIN lbf_data lb ON lb.form_id = f.form_id
                INNER JOIN form_encounter fe ON fe.encounter=f.encounter
                WHERE f.deleted=0 AND f.formdir = 'LBF2' AND lb.field_id='f2f_stat' AND lb.field_value='finalized'
                AND f.pid ='$pid' AND lb.form_id='".$form_id."'");
    $row1=sqlFetchArray($res1);
    //echo $row1['date'];
    ?>
     <div id='create'></div>
    <script language="JavaScript">
       $(document).ready(function(){
            $( window ).load(function() {
                if (window.location.href.indexOf('reload')==-1) {
                     window.location.replace(window.location.href+'?reload');
                }
            });
            jQuery("#form_date_of_service").find("option:contains('<?php echo $row1['fdate']; ?>')").each(function()
                { 
                 if( jQuery(this).text() == '<?php echo $row1['fdate']; ?>' )
                 { 
                  jQuery(this).attr("selected","selected");
                  }
                });
            }); 
    
            
    </script>
 <?php   
}
?>
</body>
<script language="JavaScript">
<?php echo $date_init; ?>
</script>
</html>
