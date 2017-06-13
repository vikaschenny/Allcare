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
    include_once("f2f_lib.php");
    require_once("$srcdir/options.inc.php");
    require_once("$srcdir/amc.php");

 //for refer login 
    $refer=$_REQUEST['refer'];
    $_SESSION['refer']=$refer;
    
$id = empty($_REQUEST['f2fid']) ? 0 : $_REQUEST['f2fid'] + 0;
$mode    = empty($_POST['mode' ]) ? '' : $_POST['mode' ];
$inmode    = $_GET['inmode'];
$encounter_id= $_REQUEST['encounter_id'];
$pid=$_REQUEST['patient_id'] ? $_REQUEST['patient_id'] :$pid;
$provider=$_REQUEST['provider'];
$location=$_REQUEST['location'];
$body_onload_code=""; 
$_SESSION['pid']=$pid;
$closed=$_REQUEST['closed'];
/* $form_name=$_GET['form_name'];
 $patient_id=$_GET['patient_id'];*/


$enc=sqlStatement("SELECT fe.encounter,f.* 
                FROM forms f
                INNER JOIN lbf_data lb ON lb.form_id = f.form_id
                INNER JOIN form_encounter fe ON fe.encounter = f.encounter
                WHERE f.deleted=0 AND f.formdir = 'LBF2'
                AND fe.pid ='$pid' AND fe.date='".$_POST['form_date_of_service']."' AND lb.field_id='f2f_stat' AND lb.field_value='finalized'");


$encrow=sqlFetchArray($enc);

 

if ($mode) {   

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
$id = empty($_REQUEST['f2fid']) ? 0 : $_REQUEST['f2fid'] + 0;
if($id)
{ 
   updateF2FForm($id, $newdata['tbl_form_facetoface_transactions'] ,$create=false);
   //echo "<script> window.parent.location.href = '../providers_f2f.php?provider=$provider&form_patient=$pid&refer=$refer'; parent.$.fancybox.close();  </script>";
   $body_onload_code = "javascript:DoPostf2f('../providers_f2f.php','$provider','$pid','$refer'); parent.$.fancybox.close();";
}
else
{  
    updateF2FForm($id, $newdata['tbl_form_facetoface_transactions'],$create=true);
     //echo "<script> window.parent.location.href = '../providers_f2f.php?provider=$provider&form_patient=$pid&refer=$refer'; parent.$.fancybox.close();  </script>"; 
    $body_onload_code = "javascript:DoPostf2f('../providers_f2f.php','$provider','$pid','$refer'); parent.$.fancybox.close();";
}  

//if($location=='provider_portal' && $closed=='YES'){
//   if ($GLOBALS['concurrent_layout']) {
//        
//         echo "<script> window.parent.location.href = '../providers_f2f.php?provider=$provider&form_patient=$pid'; parent.$.fancybox.close();  </script>"; 
//   }else{
//     
//    echo "<script> window.parent.location.href = '../providers_f2f.php?provider=$provider&form_patient=$pid'; parent.$.fancybox.close();  </script>"; 
//   }
//}
 
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
<!DOCTYPE html>
<head>
<?php html_header_show(); ?>

<link rel='stylesheet' href="<?php echo $css_header;?>" type="text/css">
<link rel="stylesheet" type="text/css" href="../../../library/js/fancybox/jquery.fancybox-1.2.6.css" media="screen" />

<style type="text/css">@import url(../../../library/dynarch_calendar.css);</style>
<link rel="stylesheet" type="text/css" href="../../../practice/css/mobileview_fancybox_content.css" />
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
    
    var nid =jQuery('#form_refer_to').val(); 
    if(nid!=''){
        $("#addr_bk").load("addr_bk_details.php?org="+nid ,function(){ 
       });
    }
    
});
</script>
<script language="JavaScript">

function DoPostf2f(page_name, provider,patient,refer) {
                method = "post"; // Set method to post by default if not specified.

                var form = document.createElement("form");
                form.setAttribute("method", method);
                form.setAttribute("action", page_name);
                form.setAttribute("target", '_parent');
                
                var key='provider';
                var hiddenField = document.createElement("input");
                hiddenField.setAttribute("type", "hidden");
                hiddenField.setAttribute("name", key);
                hiddenField.setAttribute("value", provider);
                form.appendChild(hiddenField);
                
                var key1='refer';
                var hiddenField1 = document.createElement("input");
                hiddenField1.setAttribute("type", "hidden");
                hiddenField1.setAttribute("name", key1);
                hiddenField1.setAttribute("value", refer);
                form.appendChild(hiddenField1);

                var key2='form_patient';
                var hiddenField2 = document.createElement("input");
                hiddenField2.setAttribute("type", "hidden");
                hiddenField2.setAttribute("name", key2);
                hiddenField2.setAttribute("value", patient);
                form.appendChild(hiddenField2);
                document.body.appendChild(form);
                form.submit();
                
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
 // top.restoreSession();
  f.submit();
 }
 
}
function closeme(){
    window.close();
  // parent.$.fancybox.close();
  
  window.parent.location.href = '../providers_f2f.php?provider=<?php echo $provider ; ?>&form_patient=<?php echo $pid ?>&refer=<?php echo $refer; ?>'; parent.$.fancybox.close();
}
function addr_bk(data){
    var value = $("#form_"+data).val();
    
    $.ajax({
        type: 'POST',
        url: "addr_bk_details.php",	
        data:{org:value},
        success: function(response)
        {
           
         $('#addr_bk').html(response);

        },
        failure: function(response)
        {
            alert("error");
        }		
    });	
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
<form name='new_f2f' method='post' action='face_to_face.php?f2fid=<?php echo htmlspecialchars( $id, ENT_QUOTES); ?>&location=<?php echo $location; ?>&provider=<?php echo $provider; ?>&patient_id=<?php echo $pid; ?>' onsubmit='return validate(this)'>
<input type='hidden' id="hdnmode" name='mode' value='add'>
<input type='hidden' id="hdnEncId" name='hdnEncId' value='<?php echo $encounter_id;?>'>
<input type='hidden' id="closed" name='hdnEncId' value='YES'>
<input type="hidden" id="refer" name="refer" value="<?php echo $refer; ?>"/>

	<table>
	    <tr>
            <td>
                  <a href="javascript:;"  <?php if (!$GLOBALS['concurrent_layout']) echo "target='Main'"; ?> class="css_button" onclick="submitme();">
                    <span><?php echo htmlspecialchars( xl('Save'), ENT_NOQUOTES); ?></span>
                 </a>
             </td>
             <td>
<!--                <a href="add_face_to_face.php"  <?php if (!$GLOBALS['concurrent_layout']) echo "target='Main'"; ?> class="css_button" >
                    <span><?php echo htmlspecialchars( xl('Cancel'), ENT_NOQUOTES); ?></span>
                </a>-->
                 
                  <?php if($location=='provider_portal'){ ?>
                       <a href="javascript:;"  <?php if (!$GLOBALS['concurrent_layout']) echo "target='Main'"; ?> class="css_button" onclick="closeme();">
                         <span><?php echo htmlspecialchars( xl('Cancel'), ENT_NOQUOTES); ?></span>
                       </a>
               <?php }?> 
                
            </td>
        </tr>
	</table>
       <div>
   <span class='bold'><?php echo htmlspecialchars( xl('Patient Name'), ENT_NOQUOTES); ?>:</span>
        <?php 
            $getPatientName=sqlStatement("SELECT CONCAT(fname,' ',lname) AS pname FROM patient_data WHERE pid='".$pid."'");
            $resPatientName=sqlFetchArray($getPatientName);
        ?>
        
        <span class='bold'><?php echo htmlspecialchars( xl($resPatientName['pname']), ENT_NOQUOTES); ?></span>
</div>  
	
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
 if($data_type!=14 ){
     generate_form_field($frow, $currvalue);
 } else {
     if (strpos($frow['edit_options'], 'L') !== FALSE)
      $tmp = "abook_type = 'ord_lab'";
    else if (strpos($frow['edit_options'], 'O') !== FALSE)
      $tmp = "abook_type LIKE 'ord\\_%'";
    else if (strpos($frow['edit_options'], 'V') !== FALSE)
      $tmp = "abook_type LIKE 'vendor%'";
    else if (strpos($frow['edit_options'], 'R') !== FALSE)
      $tmp = "abook_type LIKE 'dist'";
    else
      $tmp = "( username = '' OR authorized = 1 )";
    $ures = sqlStatement("SELECT id, fname, lname, organization, username FROM users " .
      "WHERE active = 1 AND ( info IS NULL OR info NOT LIKE '%Inactive%' ) " .
      "AND $tmp " .
      "ORDER BY organization, lname, fname"); ?>
    <select name="form_<?php echo $field_id; ?>" id="form_<?php echo $field_id; ?>" title="$description" onchange="addr_bk('<?php echo $field_id; ?>');" >
   <?php  echo "<option value=''>" . htmlspecialchars( xl('Unassigned'), ENT_NOQUOTES) . "</option>";
    while ($urow = sqlFetchArray($ures)) {
      $uname = $urow['organization'];
      if (empty($uname) || substr($uname, 0, 1) == '(') {
        $uname = $urow['lname'];
        if ($urow['fname']) $uname .= ", " . $urow['fname'];
      }
      $optionValue = htmlspecialchars( $urow['id'], ENT_QUOTES);
      $optionLabel = htmlspecialchars( $uname, ENT_NOQUOTES);
      echo "<option value='$optionValue'";
      $title = $urow['username'] ? xl('Local') : xl('External');
      $optionTitle = htmlspecialchars( $title, ENT_QUOTES);
      echo " title='$optionTitle'";
      if ($urow['id'] == $currvalue) echo " selected";
      echo ">$optionLabel</option>";
    } ?>
    </select>
 <?php }
  echo "</div>";
}
end_group();

?>
</div></div>
</div><div id="addr_bk"></div>
<!-- include support for the list-add selectbox feature -->
<?php// include $GLOBALS['fileroot']."/library/options_listadd.inc"; ?>
</body>
<script language="JavaScript">
<?php echo $date_init; ?>
</script>
</html>
