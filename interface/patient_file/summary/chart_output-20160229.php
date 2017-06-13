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


if ($mode) {   
  /**use sql placemaker**/
  
 //$sets1 = "from_dos =?, to_dos = ?, demographics = ?, history = ?, insurance = ?, immunizations = ?,vitals = ?";
 //$sqlBindArray = array($_POST['form_from_dos'], $_POST['form_to_dos'],$_POST['form_demographics'],$_POST['form_history'],$_POST['form_insurance'],$_POST['form_immunizations'],$_POST['form_vitals']);
  $group_name =  $_REQUEST['chartgroupshidden'];
  $dos = implode(',',$_REQUEST['dos']);
  $refer_to=$_REQUEST['refer_to'] ?  $_REQUEST['refer_to'] : 0;
  $provider=$_REQUEST['form_provider'] ? $_REQUEST['form_provider'] : 0;
  $facility=$_REQUEST['form_facility'] ? $_REQUEST['form_facility']:0 ;
  $pharmacy=$_REQUEST['form_pharmacy'] ? $_REQUEST['form_pharmacy']:0;
  $payer=$_REQUEST['form_payer'] ? $_REQUEST['form_payer']:0;
  $who_type=$_REQUEST['who_type'];
  $notes=$_REQUEST['form_notes'];
  
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
    array_push($sqlBindArray,date("Y-m-d"),$dos,$group_name,$refer_to,$provider,$facility,$pharmacy,$payer,$who_type, $notes,$id);
    $sets .= " updated_date = ?,date_of_service = ?, chart_group=?, refer_to=?,provider=? , facility=?, pharmacy=? ,payer=?,who_type=? ,notes=? ";
    $sets1= rtrim($sets,',');
    
    sqlStatement("UPDATE tbl_form_chartoutput_transactions SET $sets1 WHERE id = ?", $sqlBindArray);
  }
  else {
    //use sql placemaker
      
    array_push($sqlBindArray,$pid,date("Y-m-d"),date("Y-m-d"), $dos,$group_name,$refer_to,$provider,$facility,$pharmacy,$payer,$who_type,$notes);
    $sets .= " pid = ?".","."created_date = ?".","."updated_date = ?,date_of_service = ?,chart_group=?,refer_to=?, provider=? , facility=?, pharmacy=? ,payer=?,who_type=?,notes=?";
    $id = sqlInsert("INSERT INTO tbl_form_chartoutput_transactions SET $sets", $sqlBindArray);
  
  }     
  
if($location=='provider_portal' && $provider!=''){
     echo "<script> window.parent.location.href = '../../../providers/providers_medrecord.php?provider=$provider&form_patient=$pid'; parent.$.fancybox.close();  </script>"; 
}else{
     if ($GLOBALS['concurrent_layout'])
        $body_onload_code = "javascript:location.href='add_chartoutput.php?id=$id&group=$group_name';";
      else
        $body_onload_code = "javascript:parent.Layout-Based Visit Forms.location.href='add_chartoutput.php?id=$id&group=$group_name';";
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
$trow = $id ?getF2FById($id) : array();
?>
<!DOCTYPE html>
<head>
<?php html_header_show(); ?>

<link rel='stylesheet' href="<?php echo $css_header;?>" type="text/css">
<link rel="stylesheet" type="text/css" href="../../../library/js/fancybox/jquery.fancybox-1.2.6.css" media="screen" />
<link rel="stylesheet" type="text/css" href="../../../library/bootstrap/docs/css/bootstrap-3.2.0.min.css" media="screen" />
<link rel="stylesheet" type="text/css" href="../popover/css/jquery.webui-popover.min.css" media="screen" />
<style type="text/css">@import url(../../../library/dynarch_calendar.css);</style>
<link rel="stylesheet" type="text/css" href="../../../providers/css/mobileview_fancybox_content.css" />
<script type="text/javascript" src="../../../library/dynarch_calendar.js"></script>
<?php include_once("{$GLOBALS['srcdir']}/dynarch_calendar_en.inc.php"); ?>
<script type="text/javascript" src="../../../library/dynarch_calendar_setup.js"></script>
<script type="text/javascript" src="../../../library/textformat.js"></script>
<script type="text/javascript" src="../../../library/dialog.js"></script>

<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/jquery.js"></script>
<!--<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/common.js"></script>-->
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/jquery.1.3.2.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/fancybox/jquery.fancybox-1.2.6.js"></script>
<script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>		
<script src="../popover/js/jquery.webui-popover.js"></script>
<script type="text/javascript">
 var settings = {
    trigger:'click',
    title:'Template',
    content:'',						
    multi:true,						
    closeable:false,
    style:'',
    delay:300,
    padding:true,
    backdrop:false
};
largeSettings = {
        content:'',
        width:200,
        height:180,
        delay:{show:300,hide:1000},
        closeable:true
};

$(document).ready(function(){
    var value = $("#who_type").val();
    largeSettings.content = getnodes(value);
    $('a.show-pop-async').webuiPopover('destroy').webuiPopover($.extend({},settings,largeSettings));
    if(value.trim()!=='') {
        //$('#nots .addnots').css("display","inline-block");
     $.ajax({
        type: 'POST',
        url: "who_type.php",	
        data:{type:value,id:'<?php echo $id; ?>'},
        success: function(response)
        {
          
         $('#who').html(response);

        },
        failure: function(response)
        {
            alert("error"); 
        }		
    });	
  }
});
var Alltext="";
function list_val(id){

<?php $sql=sqlStatement("select * from tbl_form_chartoutput_transactions where id=$id"); 
      $row=sqlFetchArray($sql); ?>
 var note='<?php echo $row['notes']; ?>';
 Alltext += id+";";
 if(note!=''){
     document.getElementById("form_notes").value = note+Alltext;
 }else {
     document.getElementById("form_notes").value = Alltext;
 }
 
}

function getnodes(value1){

    if(value1=='payer'){
        <?php  $ures = sqlStatement("SELECT * 
                 FROM  `list_options` 
                 WHERE list_id ='Payer_Template'
                 ");  ?>


        var html = '<ul class="list-group">';
        <?php  while ($ures21=sqlFetchArray($ures)) { ?>
                var string_title = '<?php echo $ures21['notes']; ?>';
                html+='<li class="list-group-item" onclick="javascript:list_val('+"\'"+string_title+"\'"+');"><?php echo $ures21['title']; ?></li>';
        <?php } ?>
        html+='</ul>';
        return html;
    }else if(value1=='pharmacy'){
         <?php  $ures = sqlStatement("SELECT * 
                 FROM  `list_options` 
                 WHERE list_id ='Pharmacy_Template'
                 ");  ?>

        var html = '<ul class="list-group">';
        <?php  while ($ures21=sqlFetchArray($ures)) { ?>
                var string_title = '<?php echo $ures21['notes']; ?>';
                html+='<li class="list-group-item" onclick="javascript:list_val('+"\'"+string_title+"\'"+');"><?php echo $ures21['title']; ?></li>';
        <?php } ?>
        html+='</ul>';
        return html;
    }else if(value1=='facility'){

         <?php  $ures = sqlStatement("SELECT * 
                 FROM  `list_options` 
                 WHERE list_id ='Facility_Template'
                 ");  ?>

        var html = '<ul class="list-group">';
        <?php  while ($ures21=sqlFetchArray($ures)) { ?>
                var string_title = '<?php echo $ures21['notes']; ?>';
                html+='<li class="list-group-item" onclick="javascript:list_val('+"\'"+string_title+"\'"+');"><?php echo $ures21['title']; ?></li>';
        <?php } ?>
        html+='</ul>';
        return html;
    }else if(value1=='provider') {
         <?php  $ures = sqlStatement("SELECT * 
                 FROM  `list_options` 
                 WHERE list_id ='Provider_Template'
                 ");  ?>


       var html = '<ul class="list-group">';
        <?php  while ($ures21=sqlFetchArray($ures)) { ?>
                var string_title = '<?php echo $ures21['notes']; ?>';
                html+='<li class="list-group-item" onclick="javascript:list_val('+"\'"+string_title+"\'"+');"><?php echo $ures21['title']; ?></li>';
        <?php } ?>
        html+='</ul>';
        return html;
    }else if(value1!='') {
         <?php  $ures = sqlStatement("SELECT * 
                 FROM  `list_options`  
                 WHERE list_id ='Address_Book_Template'
                 ");  ?>


       var html = '<ul class="list-group">';
        <?php  while ($ures21=sqlFetchArray($ures)) { ?>
                var string_title = '<?php echo $ures21['notes']; ?>';
                html+='<li class="list-group-item" onclick="javascript:list_val('+"\'"+string_title+"\'"+');"><?php echo $ures21['title']; ?></li>';
        <?php } ?>
        html+='</ul>';
        return html;
    }else if(value1==''){
        <?php  $ures = sqlStatement("SELECT * 
                 FROM  `list_options`  
                 WHERE list_id ='Patient_Template'
                 ");  ?>


       var html = '<ul class="list-group">';
        <?php  while ($ures21=sqlFetchArray($ures)) { ?>
                var string_title = '<?php echo $ures21['notes']; ?>';
                html+='<li class="list-group-item" onclick="javascript:list_val('+"\'"+string_title+"\'"+');"><?php echo $ures21['title']; ?></li>';
        <?php } ?>
        html+='</ul>';
        return html;
    }
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
   window.parent.location.href = '../../../providers/providers_medrecord.php?provider=<?php echo $provider; ?>&form_patient=<?php echo $pid; ?>'; parent.$.fancybox.close();
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
function obj_type(){
    var value = $("#who_type").val();
    largeSettings.content = getnodes(value);
    $('a.show-pop-async').webuiPopover('destroy').webuiPopover($.extend({},settings,largeSettings));
   // value.trim()!==''?$('#nots .addnots').css("display","inline-block"):$('#nots .addnots').hide();
    if(value.trim()!=='') {
       // document.getElementById("pre_temp").style.display="block";
        $.ajax({
            type: 'POST',
            url: "who_type.php",	
            data:{type:value,id:'<?php echo $id; ?>'},
            success: function(response)
            {

             $('#who').html(response);

            },
            failure: function(response)
            {
                alert("error"); 
            }		
        });	
    }
}
function previewpost(){
    var datastring = $("#new_chartoutput").serialize();

        location.href = 'preview_chartoutput.php?'+datastring;

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
#nots a{
    display:inline-block;
    vertical-align: top;
    color:#fe5909;
}
#nots a:hover{color:#fe5909}
#nots a:visited{color:#fe5909}
#nots a:active{color:#fe5909}
.list-group-item:first-child {
    border-top-left-radius: 0;
    border-top-right-radius: 0;
    border-top: 0 none;
}
.list-group-item {
    background-color: #fff;
    border: 1px solid #ddd;
    display: block;
    margin-bottom: -1px;
    padding: 5px 16px;
    position: relative;
    cursor: pointer;
}
.list-group-item:hover{
    background-color: #fe5909;
    color:#fff;
}
.list-group-item:last-child {
    border-bottom-left-radius: 0;
    border-bottom-right-radius: 0;
     margin-bottom: 0px;
}
.webui-popover .webui-popover-content {
    display: block;
    overflow: auto;
    padding: 0 !important;
}
.webui-popover{padding: 0px}
.list-group{margin-bottom: 10px;}
legend.legendStyle {
padding-left: 5px;
padding-right: 5px;
}
fieldset{
font-family: Verdana, Arial, sans-serif;
font-weight: normal;
border: 1px solid #999999;
padding: 13px;
margin: 5px;
}
legend {
background-color: transparent;
}

legend {
width: auto;
border-bottom: 0px;
margin-bottom: 5px;
color: #000;
font-size: 16px;
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
        

        <td>
            <a href="add_chartoutput.php"  <?php if (!$GLOBALS['concurrent_layout']) echo "target='Main'"; ?> class="css_button" >
                <span><?php echo htmlspecialchars( xl('Cancel'), ENT_NOQUOTES); ?></span>
            </a>
       </td>
                      
           
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
    </select> <br>
    <fieldset class="fsStyle">
        <legend class="legendStyle">Who:</legend>
             <label>Type:</label>
             <?php 
                $sql12=sqlStatement("select * from tbl_form_chartoutput_transactions where id=$id");
                $res21=sqlFetchArray($sql12);
            ?>
            <select id="who_type" name = "who_type"  onchange="obj_type();">
                <option value ="">Select</option>
                <!--        <option value="addr_bk_type" <?php if($res21['who_type']=='addr_bk_type') { echo "selected"; } ?>>Address Book Type</option>-->
                <optgroup label="Address Book Type">
                <?php 
                //for address book
                $ures = sqlStatement("SELECT * 
                                     FROM  `list_options` 
                                     WHERE list_id =  'abook_type'
                                     ");
                while ($ures21=sqlFetchArray($ures)) {
                 ?>    
                 <option value="<?php echo $ures21['option_id'];?>" <?php if( $ures21['option_id']==$res21['who_type']) { echo "selected"; }?>><?php echo  $ures21['title'];?></option>

                <?php } ?>
                </optgroup>
                <option value="provider" <?php if($res21['who_type']=='provider') { echo "selected"; } ?>><b>Provider</b></option>
                <option value="facility" <?php if($res21['who_type']=='facility') { echo "selected"; } ?>><b>Facility</b></option> 
                <option value="pharmacy" <?php if($res21['who_type']=='pharmacy') { echo "selected"; } ?>><b>Pharmacy</b></option> 
                <option value="payer" <?php if($res21['who_type']=='payer') { echo "selected"; } ?>><b>Payer</b></option>
            </select><br>
             <div id="who"></div><br>
           <label>Notes:</label>
           <div id="nots"><textarea name="form_notes" id="form_notes" title="" cols="30" rows="5"><?php echo $res21['notes']; ?></textarea>
           <a href='#' class='show-pop-async addnots'  data-placement='right-bottom'><i class="glyphicon glyphicon-new-window"></i></a></div>
           
   </button>
    </fieldset> <br>
     
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
</form></body>
<script language="JavaScript">
<?php echo $date_init; ?>
</script>

</html>
  