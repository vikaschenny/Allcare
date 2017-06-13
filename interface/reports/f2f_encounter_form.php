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



 $mode    = empty($_POST['mode' ]) ? '' : $_POST['mode' ];
 $form_id=empty($_REQUEST['form_id']) ? 0 : $_REQUEST['form_id'] + 0;
 $inmode    = $_GET['inmode'];
 $pid1=$_REQUEST['pid'];
//echo $form_id;
 $ecounter=$_REQUEST['enc2'];
 $file_name=$_REQUEST['file_name'];
 $form_name=$_REQUEST['fname'];
//$enc1=$_REQUEST['enc'];
$tab=$_REQUEST['tab'];
/*$enc=sqlStatement("SELECT DISTINCT tff .*
                                    FROM tbl_form_facetoface tff
                                    INNER JOIN forms f ON tff.id = f.form_id
                                    INNER JOIN tbl_allcare_formflag flg 
                                    INNER JOIN tbl_form_facetoface_transactions ft       
                                    WHERE tff.pid ='$pid'
                                    AND flg.finalized = 'Y'
                                    AND f.deleted = '0'
                                    AND f.pid ='$pid' AND tff.date_of_service= '".$_POST['form_date_of_service']."'");*/
/*$enc=sqlStatement("SELECT *
                FROM forms f
                INNER JOIN lbf_data lb ON lb.form_id = f.form_id
                WHERE f.deleted=0
                AND f.pid ='$pid' AND f.date='".$_POST['form_date_of_service']."' AND lb.field_id='f2f_stat' AND lb.field_value='finalized'");


$encrow=sqlFetchArray($enc);*/

/*$enc=sqlStatement("SELECT fe.encounter,f.* 
                FROM forms f
                INNER JOIN lbf_data lb ON lb.form_id = f.form_id
                INNER JOIN form_encounter fe ON fe.encounter = f.encounter
                WHERE f.deleted=0 AND f.formdir = 'LBF2'
                AND fe.pid ='$pid1' AND f.form_id='".$form_id."'");


$encrow=sqlFetchArray($enc);
$encounter=$encrow['encounter'];*/

if ($mode) {   
  /**use sql placemaker**/
    


 /* $sqlBindArray=array(); 
  $fres = sqlStatement("SELECT * FROM layout_options " .
    "WHERE form_id = 'LBF2' AND uor > 0 AND field_id != '' AND group_name LIKE '%Face to Face HH Plan%'" .
    "ORDER BY seq");
  while ($frow = sqlFetchArray($fres)) {
    $data_type = $frow['data_type'];
    $field_id[]  = $frow['field_id'];
    $field_id2  = $frow['field_id'];
    echo $value = $_POST["form_$field_id2"]; echo "</br>";
     // $sets .=  add_escape_custom($field_id) . " = ?"."," ;
      array_push($sqlBindArray, $value);
  }
  //print_r($sqlBindArray);
   $res=array_combine($field_id,$sqlBindArray);
  
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
       
    //print_r($res);  
    $sets.="field_id= ?".","."field_value= ?".","."form_id=?"; 
    foreach($res as $key => $val){ 
        array_push($id ,$key);
        array_push($id,$val);
        array_push($id,$form_id);
    $id = sqlInsert("INSERT INTO lbf_data SET $sets", $id);
   
    }
  // exit();
  } */
  
function updateF2F($id, $new, $create,$ecounter1)
{
 
  $db_id = 0;
  //print_r($new);
  
  if ($create) {
    /*$sql = "INSERT INTO lbf_data SET  form_id = $id";
    foreach ($new as $key => $value) {
      if ($key == 'id') continue;
      $sql .= ", `$key` = " . pdValueOrNull($key, $value);
    }*/
    
     foreach ($new as $key => $value) {
        sqlInsert("INSERT INTO lbf_data (form_id,field_id,field_value) VALUES (?,?,?)", array($id,$key,$value));
      } 
    
    $db_id = 1;
    $array2[] = array( 'authuser' =>$_SESSION["authUser"],'date' => date("Y/m/d"), 'action'=>'Created','count'=> $count+1,'formName'=>'F2F_encounter_form');
            $logdata = array_merge_recursive($array, $array2);
            $logdata= ($logdata? serialize($logdata): serialize($array2) );
            $query1 = "INSERT INTO tbl_allcare_formflag ( " .
                    "encounter_id,form_id, form_name,pending,finalized, logdate" .
                    ") VALUES ( " .
                    "".$ecounter1.",".$id.", 'Allcare Encounter Forms', '$pending','$finalized', '".$logdata."' " .
                    ")";
    sqlInsert($query1);
  }
  else {
       //echo $db_id = $new['id'];
        foreach ($new as $key => $value) {
            sqlStatement("UPDATE lbf_data SET  `field_value` = ? WHERE  field_id= ? AND form_id = ?",array($value,$key,$id));
        }
        $db_id = 1;
        $array2[] = array( 'authuser' =>$_SESSION["authUser"], 'date' => date("Y/m/d"), 'action' => 'Updated','count'=> $count+1,'formName'=>'F2F_encounter_form');
                $logdata = array_merge_recursive($array, $array2);
                $logdata= ($logdata? serialize($logdata): serialize($array2) );
                $query2 = "INSERT INTO tbl_allcare_formflag ( " .
                "encounter_id,form_id, form_name,pending,finalized, logdate" .
                ") VALUES ( " .
                "".$ecounter1.",".$id.", 'Allcare Encounter Forms','$pending', '$finalized', '".$logdata."' " .
                ")";
    sqlInsert($query2);
    }
  return $db_id;
}
    
    
$newdata = array();
//$newdata['tbl_patientfacility']['patientid'] = $pid;
$fres = sqlStatement("SELECT * FROM layout_options " .
  "WHERE form_id = 'LBF2' AND uor > 0 AND field_id != '' AND group_name LIKE '%Face to Face HH Plan%'" .
  "ORDER BY group_name, seq");
$field_id1=array();
while ($frow = sqlFetchArray($fres)) {
  $data_type = $frow['data_type'];
  $field_id  = $frow['field_id'];
  $field_id1[]  = $frow['field_id'];
  // $value  = '';
  $colname = $field_id;
  $table = 'lbf_data';
  
  // if (isset($_POST["form_$field_id"])) $value = $_POST["form_$field_id"];
  $value = get_layout_form_value($frow);

  $newdata[$table][$colname] = $value;
}
  
$res1=sqlstatement("select * from lbf_data lb INNER JOIN layout_options l ON l.field_id=lb.field_id where lb.form_id='$form_id' AND l.form_id='LBF2' AND l.group_name LIKE '%Face to Face HH Plan%' order by seq");
while($frow1=sqlFetchArray($res1))
{
    $fid[]=$frow1['field_id'];
    
}
//print_r($field_id1);
//print_r($fid);
$diff = array_diff($field_id1,$fid);
//echo "<pre>"; print_r($diff); echo "</pre>";
//echo "<pre>"; print_r($newdata['lbf_data']); echo "</pre>";

$newValue = array();
if($file_name!='VNf2f'){
if($diff)
{ 
  //echo  $ecounter=$_REQUEST['enc2'];
  foreach($diff as $val):
      if(array_key_exists($val, $newdata['lbf_data']) && $newdata['lbf_data'][$val] != ""):
          $newValue[$val] = $newdata['lbf_data'][$val];
      endif;
  endforeach;
  //echo "<pre>"; print_r($newValue); echo "</pre>";
  //echo $diff['value'] . "==" . $newdata['key'] . " <br />";
  updateF2F($form_id, $newdata['lbf_data'] ,$create=false,$ecounter);
  if(count($newValue) > 0)
  {    //echo  $ecounter=$_REQUEST['enc2'];
      updateF2F($form_id, $newValue ,$create=true,$ecounter);
  }
    
  
}
else
{  
    //updateF2F($form_id, $newdata['lbf_data'] ,$diff,$create=false,$_REQUEST['enc2']);
    updateF2F($form_id, $newdata['lbf_data'] ,$create=false,$ecounter);
}
}
else if($file_name=='VNf2f'){
    if($form_name=='Allcare Encounter Forms') {
         $new1=  $newdata['lbf_data'];
         updateF2F($form_id, $new1 ,$create=true,$ecounter);
    } else if($form_name=='New Patient Encounter') {
        $new2=  $newdata['lbf_data'];
       // echo "<pre>"; print_r($new2); echo "</pre>";
        $sql_form=sqlStatement("select max(form_id)as new_form from forms where form_name='Allcare Encounter Forms' AND formdir='LBF2'");
        $row_form=sqlFetchArray($sql_form);
        $new_fid= $row_form['new_form'];
        $new_id1=++$new_fid;
       // echo $pid1.$_SESSION['authUserID'];
        $ins_form=sqlStatement("INSERT INTO  forms (date,encounter,form_name,form_id,pid,user,groupname,authorized,deleted,formdir) VALUES (NOW(),$ecounter,'Allcare Encounter Forms',$new_id1,$pid1,'$_SESSION[authUser]','default',1,0,'LBF2')");
        $row1_form=sqlFetchArray($ins_form);
        updateF2F($new_id1, $new2 ,$create=true,$ecounter);
    }
    
    
}
  if ($GLOBALS['concurrent_layout'])
    $body_onload_code = "javascript:location.href='f2f_encounters_report.php';";
  else
    $body_onload_code = "javascript:parent.Layout-Based Visit Forms.location.href='f2f_encounters_report.php';";
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
//echo $new_id1;
if($form_name=='New Patient Encounter'){
   $trow = $new_id1 ?getF2FEncounterForm($new_id1,$id) : array();
} else
   $trow = $form_id ?getF2FEncounterForm($form_id,$id) : array();
?>
<html>
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
 var tab1='<?php echo $tab ; ?>'
 var np_sign=jQuery('#form_f2f_np').val();
 var np_date=jQuery('#form_f2f_np_on').val();
 //alert(np_sign+"=="+np_date);
 if(np_sign=='' && tab1!='SignedButNoTransaction'){
     alert('Please Enter NP Signature ');
 } else if(np_date=='' && tab1!='SignedButNoTransaction') {
     alert('Please Enter NP  Signed Date');
 }
 else if(np_sign!='' && np_date!='' && tab1!='SignedButNoTransaction'){
 
    <?php generate_layout_validation('LBF2'); ?>

 var msg = "";
 msg += "<?php echo htmlspecialchars( xl('The following fields are required'), ENT_QUOTES); ?>:\n\n";
 for ( var i = 0; i < errMsgs.length; i++ ) {
	msg += errMsgs[i] + "\n";
 }
 msg += "\n<?php echo htmlspecialchars( xl('Please fill them in before continuing.'), ENT_QUOTES); ?>";

 if ( errMsgs.length > 0 ) {
	alert(msg);
 }

 return errMsgs.length < 1;
 }
 
  if(tab1=='SignedButNoTransaction') { 
     
    <?php generate_layout_validation('LBF2'); ?>

 var msg = "";
 msg += "<?php echo htmlspecialchars( xl('The following fields are required'), ENT_QUOTES); ?>:\n\n";
 for ( var i = 0; i < errMsgs.length; i++ ) {
	msg += errMsgs[i] + "\n";
 }
 msg += "\n<?php echo htmlspecialchars( xl('Please fill them in before continuing.'), ENT_QUOTES); ?>";

 if ( errMsgs.length > 0 ) {
	alert(msg);
 }

 return errMsgs.length < 1;
 }
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
<form name='new_f2f' method='post' action='f2f_encounter_form.php?form_id=<?php echo htmlspecialchars( $form_id, ENT_QUOTES); ?>&enc2=<?php echo htmlspecialchars( $ecounter, ENT_QUOTES); ?>&file_name=<?php echo htmlspecialchars( $file_name, ENT_QUOTES); ?>&fname=<?php echo htmlspecialchars( $form_name, ENT_QUOTES); ?>&pid=<?php echo htmlspecialchars( $pid1, ENT_QUOTES); ?>&tab=<?php echo htmlspecialchars( $tab, ENT_QUOTES); ?>'onsubmit='return validate(this)'>
<input type='hidden' id="hdnmode" name='mode' value='add'>
<input type='hidden' id="hdnEncId" name='hdnEncId' value='<?php echo $encounter;?>'>


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
<br>
    <?php   if($form_name=='New Patient Encounter') {
                $form_id=empty($_REQUEST['form_id']) ? 0 : $_REQUEST['form_id'] + 0; 
                $sql_pname=sqlStatement("select f.*,CONCAT(lname,' ',fname) AS pname from forms f INNER JOIN patient_data p ON p.pid=f.pid where form_id='$form_id' AND deleted=0 AND form_name='New Patient Encounter' AND f.pid=$pid1 ");
                $res_row1=sqlFetchArray($sql_pname);
                echo "Patient Name:".$res_row1['pname']."<br>";
                echo "Encounter:".$res_row1['encounter'];
           } else {
                $form_id=empty($_REQUEST['form_id']) ? 0 : $_REQUEST['form_id'] + 0; 
                
                $sql_pname=sqlStatement("select f.*,CONCAT(lname,' ',fname) AS pname from forms f INNER JOIN patient_data p ON p.pid=f.pid where form_id='$form_id' AND deleted=0 AND form_name='Allcare Encounter Forms' AND formdir='LBF2' AND f.pid=$pid1");
                $res_row1=sqlFetchArray($sql_pname);
                echo "Patient Name:".$res_row1['pname']."<br>";
                echo "Encounter:".$res_row1['encounter'];
           }
            ?>
<div id='f2fdiv'>
   

					<div id="Face_To_Face">
						<ul class="tabNav">
<?php
$fres = sqlStatement("SELECT * FROM layout_options " .
  "WHERE form_id = 'LBF2' AND uor > 0 AND group_name LIKE '%Face to Face HH Plan%'" .
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

  

  // Handle a data category (group) change.
  if (strcmp($this_group, $last_group) != 0) {
    $group_seq  = substr($this_group, 0, 1);
    $group_name = substr($this_group, 1);
    $last_group = $this_group;
	//if($group_seq==6)	
          echo "<li class='current'>";
	//else				echo "<li class=''>";
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
  "WHERE form_id = 'LBF2' AND uor > 0 AND group_name LIKE '%Face to Face HH Plan%'" .
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
  
 //$currvalue[]  = array();
    if($form_name=='New Patient Encounter'){
        $res=sqlstatement("select * from lbf_data where field_id='$field_id' AND form_id='".$new_id1."'");
        $frow1 = sqlFetchArray($res);
        $currvalue=$frow1['field_value'];
    } else {
        $res=sqlstatement("select * from lbf_data where field_id='$field_id' AND form_id='".$form_id."'");
        $frow1 = sqlFetchArray($res);
        $currvalue=$frow1['field_value'];
    }
    
  //if (isset($trow[$field_id])) { echo "hai"; $currvalue = $trow[$field_id];}
  
  // Handle a data category (group) change.
  //print_r($currvalue);

// Handle a data category (group) change.
  if (strcmp($this_group, $last_group) != 0) {
    end_group();
   $group_seq  = substr($this_group, 0, 1);
   $group_name = substr($this_group, 1);
   $last_group = $this_group;
   $group_seq_esc = htmlspecialchars( $group_seq, ENT_QUOTES);
	//if($group_seq==6)	
            echo "<div class='tab current' id='div_$group_seq_esc'>";
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
</div></div>
</div>
<!-- include support for the list-add selectbox feature -->
<?php include $GLOBALS['fileroot']."/library/options_listadd.inc"; ?>
</form>
</body>
<script language="JavaScript">
<?php echo $date_init; ?>
</script>
</html>