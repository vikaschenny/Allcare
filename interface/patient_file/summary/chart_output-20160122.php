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

require_once("../../globals.php");
require_once("$srcdir/chartoutput_lib.php");
require_once("$srcdir/options.inc.php");
require_once("$srcdir/amc.php");

$id = empty($_REQUEST['coid']) ? 0 : $_REQUEST['coid'] + 0;
$mode    = empty($_POST['mode' ]) ? '' : $_POST['mode' ];
$inmode    = $_GET['inmode'];
$encounter_id= $_REQUEST['encounter_id'];
$body_onload_code=""; 
//$pid =  $_SESSION['pid'];
$pid=$_REQUEST['patient_id'] ? $_REQUEST['patient_id'] :$_SESSION['pid'];
$provider=$_REQUEST['provider'];
$location=$_REQUEST['location'];

/* $form_name=$_GET['form_name'];
 $patient_id=$_GET['patient_id'];*/

 
if ($mode) {   
  /**use sql placemaker**/
   
 //$sets1 = "from_dos =?, to_dos = ?, demographics = ?, history = ?, insurance = ?, immunizations = ?,vitals = ?";
 //$sqlBindArray = array($_POST['form_from_dos'], $_POST['form_to_dos'],$_POST['form_demographics'],$_POST['form_history'],$_POST['form_insurance'],$_POST['form_immunizations'],$_POST['form_vitals']);
  $group_name =  $_REQUEST['chartgroupshidden'];
  $dos = implode(',',$_REQUEST['dos']);
 
  $sqlBindArray=array();   
  $fres = sqlStatement("SELECT * FROM layout_options " .
    "WHERE form_id = 'CHARTOUTPUT' AND uor > 0 AND field_id != '' AND group_name = '$group_name' " .
    "ORDER BY group_name, seq");
  while ($frow = sqlFetchArray($fres)) {
    $data_type = $frow['data_type'];
    $field_id  = $frow['field_id'];
    $value = $_POST["form_$field_id"];
    $sets .=  add_escape_custom($field_id) . " = ?"."," ;
    array_push($sqlBindArray, $value);
  }
  if ($id) {       
    //use sql placemaker
    array_push($sqlBindArray,date("Y-m-d"),$dos, $id);
    $sets .= " updated_date = ?,date_of_service = ? ";
    $sets1= rtrim($sets,',');
    sqlStatement("UPDATE tbl_form_chartoutput_transactions SET $sets1 WHERE id = ?", $sqlBindArray);
  }
  else {
    //use sql placemaker
      
    array_push($sqlBindArray,$pid,date("Y-m-d"),date("Y-m-d"), $dos);
    $sets .= " pid = ?".","."created_date = ?".","."updated_date = ?,date_of_service = ?";
    $id = sqlInsert("INSERT INTO tbl_form_chartoutput_transactions SET $sets", $sqlBindArray);
  
  }     
  
if($location=='provider_portal' && $provider!=''){
     echo "<script> window.parent.location.href = '../../../providers/providers_medrecord.php?provider=$provider&form_patient=$pid'; parent.$.fancybox.close();  </script>"; 
}else{
     if ($GLOBALS['concurrent_layout'])
        $body_onload_code = "javascript:location.href='add_chartoutput.php?id=$id';";
      else
        $body_onload_code = "javascript:parent.Layout-Based Visit Forms.location.href='add_chartoutput.php?id=$id';";
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

//// If we are editing a transaction, get its ID and data.
//$trow = $id ?getChartOutputById($id ,$_REQUEST['group_name']) : array();

?>
<!DOCTYPE html>
<head>
<?php html_header_show(); ?>

<link rel='stylesheet' href="<?php echo $css_header;?>" type="text/css">
<link rel="stylesheet" type="text/css" href="../../../library/js/fancybox/jquery.fancybox-1.2.6.css" media="screen" />

<style type="text/css">@import url(../../../library/dynarch_calendar.css);</style>
<link rel="stylesheet" type="text/css" href="../../../providers/css/mobileview_fancybox_content.css" />
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
 dlgopen('../deleter.php?coid=<?php echo htmlspecialchars( $id, ENT_QUOTES); ?>', '_blank', 500, 450);
 return false;
}

// Called by the deleteme.php window on a successful delete.
function imdeleted() {
 top.restoreSession();
 location.href = 'summary/add_chartoutput.php';
}

// Compute the length of a string without leading and trailing spaces.
function trimlen(s) {
    alert(s);
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

 var chartgroups =  $('#chartgroups option:selected').text();
    <?php generate_group_validation('CHARTOUTPUT', chartgroups); ?>


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
 var f = document.forms['new_chartoutput'];
 if (validate(f)) {
  //top.restoreSession();
  f.submit();
 }
}

function closeme(){
   // window.close();
   parent.$.fancybox.close();
}
function previewpost(){
    var datastring = $("#new_chartoutput").serialize();
//    $.ajax({
//            type: "POST",
//            url: "preview_chartoutput.php",
//            data: datastring,
//            success: function(data) {
//                alert("success");
//                location.href = 'preview_chartoutput.php';
//                 //top.restoreSession();
//            }
//        });
//    alert(datastring);
//      var str = '';
//        var elem = document.getElementById('new_chartoutput').elements;
//        var postdata = {};
//        for(var i = 0; i < elem.length; i++)
//        {
//            var str = elem[i].name;
//            var string = str.replace('[]', '' );
//            postdata[string] = elem[i].value;
//            alert(document.getElementById("dos").value);
//        }
        //top.restoreSession();
        location.href = 'preview_chartoutput.php?'+datastring;
//        $.ajax({
//            url: 'preview_chartoutput.php',
//            contentType: "application/json; charset=utf-8",
//            async: true,
//            type: 'POST',
//            data:   postdata ,
//            success: function(content)
//            {
//                top.restoreSession();
//                location.href = 'preview_chartoutput.php';
//            } ,
//            error: function(jqXHR, textStatus, errorThrown) {
//                console.log(textStatus, errorThrown);
//            }
//    });
}

function group_selected(){
    if( '<?php echo isset($_REQUEST['group_name']); ?>' != '' ){
        var chartgroupshidden = '<?php echo $_REQUEST['group_name']; ?>';
        $('#chartgroupshidden').val(chartgroupshidden);
    }else{
        var chartgroupshidden = $('#chartgroups option:selected').val()+$('#chartgroups option:selected').text();
        $('#chartgroupshidden').val(chartgroupshidden);
    }
    
     $.ajax({
            url: 'chart_output_dynamic.php',
            type: 'POST',
            data:  { chartgroupshidden:chartgroupshidden,id:<?php echo $id; ?> },
            success: function(content)
            {
                $("#data").html(content);
                //return content;
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
<form id="new_chartoutput" name='new_chartoutput' method='post' action='chart_output.php?coid=<?php echo htmlspecialchars( $id, ENT_QUOTES); ?>&location=<?php echo $location; ?>&provider=<?php echo $provider;?>&pid=<?php echo $pid; ?>' onsubmit='return validate(this)'>
<input type='hidden' id="hdnmode" name='mode' value='add'>
<input type='hidden' id="hdnEncId" name='hdnEncId' value='<?php echo $encounter_id;?>'>
<input type='hidden' id="hdnEncId" name='hdnEncId' value='<?php echo $encounter_id;?>'>
<input type='hidden' id="patientid" name='patientid' value='<?php echo $pid;?>'>
    <table>
     <tr>
        <td>
            <a href="javascript:;"  <?php if (!$GLOBALS['concurrent_layout']) echo "target='Main'"; ?> class="css_button" onclick="submitme();">
                <span><?php echo htmlspecialchars( xl('Save'), ENT_NOQUOTES); ?></span>
            </a>
        </td>
        
            <?php if($location=='provider_portal'){ ?>
                    <td>
                       <a href="javascript:;"  <?php if (!$GLOBALS['concurrent_layout']) echo "target='Main'"; ?> class="css_button" onclick="closeme();">
                         <span><?php echo htmlspecialchars( xl('Cancel'), ENT_NOQUOTES); ?></span>
                       </a>
                    </td>
                     
            <?php } else { ?> 
                        <td>
                            <a href="add_chartoutput.php"  <?php if (!$GLOBALS['concurrent_layout']) echo "target='Main'"; ?> class="css_button" >
                                <span><?php echo htmlspecialchars( xl('Cancel'), ENT_NOQUOTES); ?></span>
                            </a>
                       </td>
                      
              <?php } ?> 
               <td style='width:150px;'>&nbsp;</td>
                        <td>
                             <a href="javascript:;" <?php if (!$GLOBALS['concurrent_layout']) echo "target='Main'"; ?> class="css_button" onclick="previewpost();">
                                <span><?php echo htmlspecialchars( xl('Preview'), ENT_NOQUOTES); ?></span>
                            </a>
                        </td>         
     </tr>
    </table>
<?php 
$groups = sqlStatement("SELECT DISTINCT(group_name) as group_name FROM layout_options " .
              "WHERE form_id = 'CHARTOUTPUT' AND uor > 0 " .
              "ORDER BY group_name");
//$dos = sqlStatement("SELECT DATE_FORMAT(f.date, '%Y-%m-%d') as date, o.pc_catname as pc_catname FROM form_encounter f INNER JOIN openemr_postcalendar_categories o ON f.pc_catid = o.pc_catid INNER JOIN form_encounter fe ON  fe.encounter=f.encounter WHERE f.pid = $pid ");
$dos = sqlStatement("SELECT fe.encounter, DATE_FORMAT(fe.date, '%Y-%m-%d') as date,openemr_postcalendar_categories.pc_catname FROM form_encounter AS fe ".
    " left join openemr_postcalendar_categories on fe.pc_catid=openemr_postcalendar_categories.pc_catid  WHERE fe.pid = ? order by fe.date desc", array($pid));
?>
<div id='chartdiv'>
    <br>
     <label>Date of Service </label>
    <select id="dos" name = "dos[]"  multiple>
        <option value =" ">Select</option>
        <?php 
        while ($dos2 = sqlFetchArray($dos)) { 
            if(!isset($_REQUEST['group_name2'])):
                echo "<option value =".$dos2['date'].">".$dos2['date']."-".$dos2['pc_catname'] ."</option>";
            else:
                $sql = sqlStatement("SELECT date_of_service FROM tbl_form_chartoutput_transactions WHERE id = $id ");
                while ($sql2 = sqlFetchArray($sql)) {
                   $date  =  $sql2['date_of_service'];
                }
                $d = explode(',', $date);
                echo "<option value =".$dos2['date']. " ";
                foreach($d as $val){
                    if($val == $dos2['date']){
                        echo "selected";
                    }
                }
                echo ">".$dos2['date']."-".$dos2['pc_catname']. "</option>";
            endif;
        }
        ?>
    </select>
    
     <br><br>
    <?php if(!isset($_REQUEST['group_name2'])):?>
    <label>Group </label>
    <select id ="chartgroups"  onchange="javascript:group_selected();">
        <option value=""> Select </option>
        <?php 
        while ($groups2 = sqlFetchArray($groups)) {
             ?><option value ="<?php echo substr($groups2['group_name'],0,1);?>"><?php echo substr($groups2['group_name'],1); ?></option><?php 
        }
        ?>
    </select>
    <input type="hidden" id ="chartgroupshidden" name = 'chartgroupshidden'/>
    <?php else:
        echo "Group Name: <b>".substr($_REQUEST['group_name'],1)."</b>";
        ?><script>
            javascript:group_selected();
            </script>
            <input type="hidden" id ="chartgroupshidden" name = 'chartgroupshidden' value="<?php echo $_REQUEST['group_name']; ?> "/>
        <?php   
        endif;
    ?>
    
   
    <div id="data"></div>
   
</div>

<!-- include support for the list-add selectbox feature -->
<?php include $GLOBALS['fileroot']."/library/options_listadd.inc"; ?>
</body>
<script language="JavaScript">
<?php echo $date_init; ?>
</script>
</html>
