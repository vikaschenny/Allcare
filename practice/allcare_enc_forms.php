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
require_once("../f2f/f2f_lib.php");
require_once("$srcdir/options.inc.php");
require_once("$srcdir/amc.php");



  $mode    = empty($_POST['mode' ]) ? '' : $_POST['mode' ];
  $form_id=empty($_REQUEST['form_id1']) ? 0 : $_REQUEST['form_id1'] + 0;
  $inmode    = $_GET['inmode1'];
  $pid1=$_REQUEST['pid1'];
//echo $form_id;
  $ecounter=$_REQUEST['enc3'];
  $file_name=$_REQUEST['file_name1'];
  $form_name1=$_REQUEST['fname1'];
  $form_name=str_replace("_"," ",$form_name1);
//$enc1=$_REQUEST['enc'];
 //$tab=$_REQUEST['tab'];
  $groupname1=$_REQUEST['groupname'];
   $groupname=str_replace("_"," ",$groupname1);
  $provider=$_REQUEST['provider'];
  $location=$_REQUEST['location'];



if ($mode) {   
 
function updateENC_forms($id, $new, $create,$ecounter1,$pid1)
{
 
  $db_id = 0;
  //print_r($new);
  
  if ($create) {
    /*$sql = "INSERT INTO lbf_data SET  form_id = $id";
    foreach ($new as $key => $value) {
      if ($key == 'id') continue;
      $sql .= ", `$key` = " . pdValueOrNull($key, $value);
    }*/
    if($id==0){
        $sql_form=sqlStatement("select max(form_id)as new_form from forms where form_name='Allcare Encounter Forms' AND formdir='LBF2'");
        $row_form=sqlFetchArray($sql_form);
        $new_fid= $row_form['new_form'];
        $new_id1=++$new_fid;
        //echo "INSERT INTO  forms (date,encounter,form_name,form_id,pid,user,groupname,authorized,deleted,formdir) VALUES (NOW(),$ecounter1,'Allcare Encounter Forms',$new_id1,$pid1,'$_SESSION[authUser]','default',1,0,'LBF2')";
        $ins_form=sqlStatement("INSERT INTO  forms (date,encounter,form_name,form_id,pid,user,groupname,authorized,deleted,formdir) VALUES (NOW(),$ecounter1,'Allcare Encounter Forms',$new_id1,$pid1,'$_SESSION[authUser]','default',1,0,'LBF2')");
        $row1_form=sqlFetchArray($ins_form);
        foreach ($new as $key => $value) {
       // echo "INSERT INTO lbf_data (form_id,field_id,field_value) VALUES (?,?,?)", array($id,$key,$value);
        if($value!=''){ 
          //echo "INSERT INTO lbf_data (form_id,field_id,field_value) VALUES (?,?,?)", array($new_id1,$key,$value);  
         
         sqlInsert("INSERT INTO lbf_data (form_id,field_id,field_value) VALUES (?,?,?)", array($new_id1,$key,$value));
        }
      } 
        
    }else{
     foreach ($new as $key => $value) {
      
        if($value!=''){ 
         
          
         sqlInsert("INSERT INTO lbf_data (form_id,field_id,field_value) VALUES (?,?,?)", array($id,$key,$value));
        }
      } 
    }
    $db_id = 1;
    //echo $group_name1;
    $array=array();
    $array2[] = array( 'authuser' =>$_SESSION["authUser"],'date' => date("Y/m/d"), 'action'=>'Created','count'=> $count+1,'formName'=>'F2F_encounter_form', 'group_name' => $group_name1);
            //echo "<pre>"; print_r($array2); echo "</pre>";
            $logdata = array_merge_recursive($array, $array2);
            $logdata= ($logdata? serialize($logdata): serialize($array2) );
            $query1 = "INSERT INTO tbl_allcare_formflag ( " .
                    "encounter_id,form_id, form_name,pending,finalized, logdate" .
                    ") VALUES ( " .
                    "".$ecounter1.",".$id.", 'Allcare Encounter Forms', '$pending','$finalized', '".$logdata."' " .
                    ")";
    //sqlInsert($query1);
  }
  else {
       //echo $db_id = $new['id'];
        foreach ($new as $key => $value) {
            if($value!=''){
              // echo "UPDATE lbf_data SET  `field_value` = ? WHERE  field_id= ? AND form_id = ?",array($value,$key,$id);
               sqlStatement("UPDATE lbf_data SET  `field_value` = ? WHERE  field_id= ? AND form_id = ?",array($value,$key,$id));
            }else {
               sqlStatement("delete  from lbf_data where form_id=$id AND field_id='$key'");
            }
        }
        $db_id = 1;
        $array2[] = array( 'authuser' =>$_SESSION["authUser"], 'date' => date("Y/m/d"), 'action' => 'Updated','count'=> $count+1,'formName'=>'F2F_encounter_form','group_name' => $group_name1);
                $logdata = array_merge_recursive($array, $array2);
                $logdata= ($logdata? serialize($logdata): serialize($array2) );
                $query2 = "INSERT INTO tbl_allcare_formflag ( " .
                "encounter_id,form_id, form_name,pending,finalized, logdate" .
                ") VALUES ( " .
                "".$ecounter1.",".$id.", 'Allcare Encounter Forms','$pending', '$finalized', '".$logdata."' " .
                ")";
    //sqlInsert($query2);
    }
  return $db_id;
}
    
    
$newdata = array();

$fres = sqlStatement("SELECT * FROM layout_options " .
  "WHERE form_id = 'LBF2' AND uor > 0 AND field_id != '' AND group_name LIKE '%$groupname%'" .
  "ORDER BY group_name, seq");
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


//echo "select * from lbf_data lb INNER JOIN layout_options l ON l.field_id=lb.field_id where lb.form_id='$form_id' AND l.form_id='LBF2' AND l.group_name LIKE '%$groupname%' order by seq"; 
$res1=sqlstatement("select * from lbf_data lb INNER JOIN layout_options l ON l.field_id=lb.field_id where lb.form_id='$form_id' AND l.form_id='LBF2' AND l.group_name LIKE '%$groupname%' order by seq");
while($frow1=sqlFetchArray($res1))
{
    $fid[]=$frow1['field_id'];
    
}
//print_r($field_id1);

//print_r($fid); 
$diff = array_diff($field_id1,$fid);
//$diff = array_diff($fid,$field_id1);
//echo "<pre>"; print_r($diff); echo "</pre>";
//echo "<pre>"; print_r($newdata['lbf_data']); echo "</pre>";

$newValue = array();

if(!empty($diff))
{ 
  //echo  $ecounter=$_REQUEST['enc2'];
  foreach($diff as $val):
      if(array_key_exists($val, $newdata['lbf_data']) && $newdata['lbf_data'][$val] != ""):
          $newValue[$val] = $newdata['lbf_data'][$val];
      endif;
  endforeach;
  //echo "<pre>"; print_r($newValue); echo "</pre>";
  //echo $diff['value'] . "==" . $newdata['key'] . " <br />";
  updateENC_forms($form_id, $newdata['lbf_data'] ,$create=false,$ecounter,$pid1);
  if(count($newValue) > 0)
  {    //echo  $ecounter=$_REQUEST['enc2'];
      updateENC_forms($form_id, $newValue ,$create=true,$ecounter,$pid1);
  }
    
  
}
else
{  
    //updateF2F($form_id, $newdata['lbf_data'] ,$diff,$create=false,$_REQUEST['enc2']);
  //updateENC_forms($form_id, $newdata['lbf_data'] ,$create=true,$ecounter);
    if(!empty($fid)){
        updateENC_forms($form_id, $newdata['lbf_data'] ,$create=false,$ecounter,$pid1);
    }else {
        updateENC_forms($form_id, $newdata['lbf_data'] ,$create=true,$ecounter,$pid1);
    }
    
}



 if ($GLOBALS['concurrent_layout']) {
    //echo '<script> self.close();</script>';
    // echo '<script>  window.location.href = "../../reports/incomplete_charts.php"; </script>';
   echo "<script>window.close();

    window.opener.location.href = '../provider_incomplete_charts.php?checkencounter=".$ecounter."';</script>";     
 
   
  }
  else{
   
   echo "<script>window.close();

    window.opener.location.href = '../provider_incomplete_charts.php?checkencounter=".$ecounter."';</script>";
    
  }

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

   $trow = $form_id ?getF2FEncounterForm($form_id,$id) : array();
?>
<html>
<head>
<?php html_header_show(); ?>
<meta content="width=device-width,initial-scale=1.0" name="viewport">
<link rel='stylesheet' href="<?php echo $css_header;?>" type="text/css">
<link rel="stylesheet" type="text/css" href="../../../library/js/fancybox/jquery.fancybox-1.2.6.css" media="screen" />

<style type="text/css">@import url(../../../library/dynarch_calendar.css);</style>
<link rel="stylesheet" href="../tableresponsive/allcare_enc_forms_responsive.css"/>
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


function submitme() {
 var f = document.forms['new_f2f'];
 if (validate(f)) {
  //top.restoreSession();
  f.submit();
 }
 
}

function closeme(){
    window.close();
}

</script>


<style type="text/css">
div.tab {
	height: auto;
	width: auto;
}
</style>

</head>
<!--<body class="body_top" >-->
<body class="body_top" onload="<?php echo $body_onload_code; ?>" >    
<form name='new_f2f' method='post' action='allcare_enc_forms.php?form_id1=<?php echo htmlspecialchars( $form_id, ENT_QUOTES); ?>&enc3=<?php echo htmlspecialchars( $ecounter, ENT_QUOTES); ?>&file_name1=<?php echo htmlspecialchars( $file_name, ENT_QUOTES); ?>&fname1=<?php echo htmlspecialchars( $form_name, ENT_QUOTES); ?>&pid1=<?php echo htmlspecialchars( $pid1, ENT_QUOTES); ?>&groupname=<?php echo htmlspecialchars( $groupname, ENT_QUOTES); ?>&provider=<?php echo $provider; ?>&location=<?php echo $location ; ?>'onsubmit='return validate(this)'>
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
                <?php if($location=='provider_portal'){ ?>
                       <a href="javascript:;"  <?php if (!$GLOBALS['concurrent_layout']) echo "target='Main'"; ?> class="css_button" onclick="closeme();">
                         <span><?php echo htmlspecialchars( xl('Cancel'), ENT_NOQUOTES); ?></span>
                       </a>
               <?php } else { ?> 
                <a href="incomplete_charts.php"  <?php if (!$GLOBALS['concurrent_layout']) echo "target='Main'"; ?> class="css_button" >
                    <span><?php echo htmlspecialchars( xl('Cancel'), ENT_NOQUOTES); ?></span>
                </a>
               <?php } ?>  
<!--                 <a href='#' class="css_button" onclick=self.close();><span><?php echo xl('Cancel');?></span></a>-->
            </td>
        </tr>
	</table>
<br>
    <?php  
                $form_id=empty($_REQUEST['form_id1']) ? 0 : $_REQUEST['form_id1'] + 0; 
                $sql_pname=sqlStatement("select f.*,CONCAT(lname,' ',fname) AS pname from form_encounter f INNER JOIN patient_data p ON p.pid=f.pid where encounter='$ecounter' AND f.pid=$pid1");
                $res_row1=sqlFetchArray($sql_pname);
                echo "Patient Name:".$res_row1['pname']."<br>";
                echo "Encounter:".$res_row1['encounter'];
          
            ?>
<div id='f2fdiv'>
   

					<div id="Face_To_Face">
						<ul class="tabNav">
<?php
$fres = sqlStatement("SELECT * FROM layout_options " .
  "WHERE form_id = 'LBF2' AND uor > 0 AND group_name LIKE '%$groupname%'" .
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
  "WHERE form_id = 'LBF2' AND uor > 0 AND group_name LIKE '%$groupname%'" .
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
//    if($form_name=='New Patient Encounter'){
//        $res=sqlstatement("select * from lbf_data where field_id='$field_id' AND form_id='".$new_id1."'");
//        $frow1 = sqlFetchArray($res);
//        $currvalue=$frow1['field_value'];
//    } else {
        $res=sqlstatement("select * from lbf_data where field_id='$field_id' AND form_id='".$form_id."'");
        $frow1 = sqlFetchArray($res);
        $currvalue=$frow1['field_value'];
    //}
    
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
