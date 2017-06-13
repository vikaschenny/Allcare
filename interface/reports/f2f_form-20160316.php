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


 $id = empty($_REQUEST['coid']) ? 0 : $_REQUEST['coid'] + 0; 
 $mode    = empty($_POST['mode' ]) ? '' : $_POST['mode' ];
 //edit
 $inmode    = $_GET['inmode'];
 $encounter_id= $_REQUEST['enc'];
 $pid1=$_REQUEST['pid'];
 //add
 $mode1=$_REQUEST['mode1'];
 $enc1=$_REQUEST['enc'];
 $ab1=str_replace("?reload"," ","$mode1");
 
 
 $body_onload_code=""; 
 $form_id=$_REQUEST['form_id'];
 $form_name=$_REQUEST['form_name'];
//create
$inmode1    = $_GET['inmode1'];
$encounter=$_REQUEST['enc'];
$pname=$_REQUEST['pid'];
$pid=$_REQUEST['pid'];
$_SESSION['pid']=$pid;

$enc=sqlStatement("SELECT fe.encounter,f.* 
                FROM forms f
                INNER JOIN lbf_data lb ON lb.form_id = f.form_id
                INNER JOIN form_encounter fe ON fe.encounter = f.encounter
                WHERE f.deleted=0 AND f.formdir = 'LBF2'
                AND fe.pid ='$pid' AND fe.date='".$_POST['form_date_of_service']."' AND lb.field_id='f2f_stat' AND lb.field_value='finalized'");


$encrow=sqlFetchArray($enc);


if ($mode) {   

//$newdata = array();
//$newdata['tbl_form_facetoface_transactions']['pid'] = $pid;
//$newdata['tbl_form_facetoface_transactions']['form_id'] = $encrow['form_id'];
//$newdata['tbl_form_facetoface_transactions']['encounter'] = $encrow['encounter'];
//$fres = sqlStatement("SELECT * FROM layout_options " .
//  "WHERE form_id = 'F2F' AND uor > 0 AND field_id != '' " .
//  "ORDER BY group_name, seq");
//while ($frow = sqlFetchArray($fres)) {
//  $data_type = $frow['data_type'];
//  $field_id  = $frow['field_id'];
//  // $value  = '';
//  $colname = $field_id;
//  $table = 'tbl_form_facetoface_transactions';
//  
//  // if (isset($_POST["form_$field_id"])) $value = $_POST["form_$field_id"];
//  $value = get_layout_form_value($frow);
//
//  $newdata[$table][$colname] = $value;
//}
//
//if($id)
//{ 
//   updateF2FForm($id, $newdata['tbl_form_facetoface_transactions'] ,$create=false);
//}
//else
//{  
//    updateF2FForm($id, $newdata['tbl_form_facetoface_transactions'],$create=true);
//
//}  

if($_REQUEST['form_date_of_service']!='') {
    
    $trans_type='Patient Encounter Specific';
    $group_name =  $_REQUEST['chartgroupshidden'];
    $dos=$_REQUEST['form_date_of_service'];
    $refer_to=$_REQUEST['refer_to']? $_REQUEST['refer_to']:0;
    $provider=$_REQUEST['form_provider1']? $_REQUEST['form_provider1']:0;
    $facility=$_REQUEST['form_facility']? $_REQUEST['form_facility']:0;
    $pharmacy=$_REQUEST['form_pharmacy']? $_REQUEST['form_pharmacy']:0;
    $payer=$_REQUEST['form_payer']? $_REQUEST['form_payer']:0;
    $who_type=$_REQUEST['who_type'];
    $notes=$_REQUEST['form_notes2'];
    $form_temp=$_REQUEST['form_temp']; 
    $type=2;

    $sqlBindArray=array();
    $fres = sqlStatement("SELECT * FROM layout_options " .
            "WHERE form_id = 'CHARTOUTPUT' AND uor > 0 AND field_id != '' AND group_name = '$group_name' AND field_id LIKE '%f2f%'" .
            "ORDER BY group_name, seq");
            while ($frow = sqlFetchArray($fres)) {
                $data_type = $frow['data_type'];
                $field_id  = $frow['field_id'];
                $value = 'YES';
                $sets .=  add_escape_custom($field_id) . " = ?"."," ;
                array_push($sqlBindArray, $value);
            }
            $enc=sqlStatement("select * from form_encounter where  DATE_FORMAT(date, '%Y-%m-%d')='".$dos."' and pid=$pid" );
            $renc=sqlFetchArray($enc);
            $encounter=$renc['encounter'];
}
        
if ($id) {       
    //use sql placemaker
    array_push($sqlBindArray,date("Y-m-d"),$dos,$group_name,$refer_to,$provider,$facility,$pharmacy,$payer,$who_type, $notes,$trans_type,$type,$encounter,$id);
    $sets .= " updated_date = ?,date_of_service = ?, chart_group=?, refer_to=?,provider=? , facility=?, pharmacy=? ,payer=?,who_type=? ,notes=? ,trans_type=?,transaction=?,encounter=?";
    $sets1= rtrim($sets,',');
    sqlStatement("UPDATE tbl_form_chartoutput_transactions SET $sets1 WHERE id = ?", $sqlBindArray);

 }else {
    //use sql placemaker
    array_push($sqlBindArray,$pid,date("Y-m-d"),date("Y-m-d"), $dos,$group_name,$refer_to,$provider,$facility,$pharmacy,$payer,$who_type,$notes,$trans_type,$type,$encounter);
    $sets .= " pid = ?".","."created_date = ?".","."updated_date = ?,date_of_service = ?,chart_group=?,refer_to=?, provider=? , facility=?, pharmacy=? ,payer=?,who_type=?,notes=?,trans_type=?,transaction=?,encounter=?";
    $id = sqlInsert("INSERT INTO tbl_form_chartoutput_transactions SET $sets", $sqlBindArray);
}    

 if ($GLOBALS['concurrent_layout'])
    $body_onload_code = "javascript:location.href='add_f2f.php?id=$id&group=$group_name&enc=$encounter';";
 else
    $body_onload_code = "javascript:parent.Layout-Based Visit Forms.location.href='add_f2f.php?id=$id&group=$group_name&enc=$encounter';";







//  if ($GLOBALS['concurrent_layout'])
//    $body_onload_code = "javascript:location.href='add_f2f.php';";
//  else
//    $body_onload_code = "javascript:parent.Layout-Based Visit Forms.location.href='add_f2f.php';";
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
<link rel="stylesheet" type="text/css" href="../../../library/bootstrap/docs/css/bootstrap-3.2.0.min.css" media="screen" />
<link rel="stylesheet" type="text/css" href="../patient_file/popover/css/jquery.webui-popover.min.css" media="screen" />

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
<script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
<script src="../patient_file/popover/js/jquery.webui-popover.js"></script>
<script type="text/javascript">
$(document).ready(function(){
    tabbify();
   
var value = $("#who_type").val();
largeSettings.content = getnodes(value);
$('a.addnots').webuiPopover('destroy').webuiPopover($.extend({},settings,largeSettings));
    if(value.trim()!=='') {
        $.ajax({
            type: 'POST',
            url: "../patient_file/summary/who_type.php",	
            data:{type:value,id:'<?php echo $id; ?>',fid:'f2f_reports',trtype:'<?php echo $_REQUEST['type']; ?>'},
            success: function(response)
            {
                 var $parentcon = $('.tabContainer .current').find('fieldset[id *="ftab"]');

                $parentcon.find('#who').html(response);
                $('#who br').remove();
                showhoTypeCotentOnload($parentcon);
                showhotypecotent($parentcon);
            },
            failure: function(response)
            {
                alert("error"); 
            }		
        });	
    }
});
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
        closeable:true,
};
whoprevewSettings = {
        content:'',
        width:320,
        height:200,
        delay:{show:300,hide:1000},
        closeable:true,
        title:'',
        trigger: 'click',
        dismissible:true,
        onHide: function($element) {}
};

function list_val(id){
 var Alltext = $('div.current').find('#form_notes').val()+id+";";
 $('div.current').find('#form_notes').val(Alltext); 
}

//function list_val(id){
//
//<?php $sql=sqlStatement("select * from tbl_form_chartoutput_transactions where id=$id"); 
      $row=sqlFetchArray($sql); ?>//
// var note='<?php echo $row['notes']; ?>';
// Alltext += id+";";
// if(note!=''){
//     document.getElementById("form_notes").value = note+Alltext;
// }else {
//     document.getElementById("form_notes").value = Alltext;
// }
// 
//}
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
function showhotypecotent($parentcon){
    $parentcon.find('#addr_bk').hide();
    $parentcon.children('#who').children('select').change(function(){
        var $select = $(this);
        setTimeout(function(){
           $parentcon.find('a.whopreview').webuiPopover('hide');
           $parentcon.children('#who').children("br,a").remove();
           if($select.val()!=""){
             $parentcon.children('#who').append("<a href='#' class='show-pop-async whopreview'  title='Click to see Template' data-placement='right-bottom'><i class='glyphicon glyphicon-new-window'></i></a>");
              whoprevewSettings.content = "<div id='whocontent'><table>"+$parentcon.find('#addr_bk fieldset table').html()+"</table></div>";
              whoprevewSettings.title = $parentcon.find('#addr_bk legend').text().slice(0,$parentcon.find('#addr_bk legend').text().length - 1);
              $parentcon.find('a.whopreview').prop("title","Click to see "+whoprevewSettings.title);
              $parentcon.find('a.whopreview').webuiPopover('destroy').webuiPopover($.extend({},settings,whoprevewSettings));
              $parentcon.find('a.whopreview').webuiPopover('show');
           }
        },1000);

    });
}

function showhoTypeCotentOnload($parentcon){
    $parentcon.find('#addr_bk').hide();
    var $select = $parentcon.children('#who').children('select');
    setTimeout(function(){
       $parentcon.find('a.whopreview').webuiPopover('hide');
       $parentcon.children('#who').children("br,a").remove();
       if($select.val()!=""){
         $parentcon.children('#who').append("<a href='#' class='show-pop-async whopreview'  title='Click to see Template' data-placement='right-bottom'><i class='glyphicon glyphicon-new-window'></i></a>");
          whoprevewSettings.content = "<div id='whocontent'><table>"+$parentcon.find('#addr_bk fieldset table').html()+"</table></div>";
          whoprevewSettings.title = $parentcon.find('#addr_bk legend').text().slice(0,$parentcon.find('#addr_bk legend').text().length - 1);
          $parentcon.find('a.whopreview').prop("title","Click to see "+whoprevewSettings.title);
          $parentcon.find('a.whopreview').webuiPopover('destroy').webuiPopover($.extend({},settings,whoprevewSettings));
          $parentcon.find('a.whopreview').webuiPopover('show');
       }
    },1000);
}
</script>
<script language="JavaScript">
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
 //if (validate(f)) {
 
  f.submit();
 //}
}
function addr_bk(data){
    var value = $("#form_"+data).val();
    $.ajax({
        type: 'POST',
        url: "../patient_file/summary/addr_bk_details.php",	
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

function group_selected($target){
    if( '<?php echo isset($_REQUEST['group_name']); ?>' != '' ){
        var chartgroupshidden = '<?php echo $_REQUEST['group_name']; ?>';
        $('#chartgroupshidden').val(chartgroupshidden);
    }else{
        var chartgroupshidden = $('#chartgroups option:selected').val()+$('#chartgroups option:selected').text();
        $('#chartgroupshidden').val(chartgroupshidden);
    }
     

}
function obj_type($element){
    var value = $("#who_type").val();
    var $parentcon = $($element).parents('fieldset');
    largeSettings.content = getnodes(value);
    $parentcon.find('a.addnots').webuiPopover('destroy').webuiPopover($.extend({},settings,largeSettings));
    if(value.trim()!=='') {
        $.ajax({
            type: 'POST',
            url: "../patient_file/summary/who_type.php",	
            data:{type:value,id:'<?php echo $id; ?>',fid:'f2f_reports'},
            success: function(response)
            {
               var $parentcon = $('.tabContainer .current').find('fieldset[id *="ftab"]');
                $parentcon.find('#who').html(response);
                $('#who br').remove();
                showhoTypeCotentOnload($parentcon);
                 showhotypecotent($parentcon);
            },
            failure: function(response)
            {
                alert("error"); 
            }		
        });	
    }
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
.webui-popover-content iframe{
    height: 274px;
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
ul.tabNav li {
    float: left;
    font-size: 1em;
    margin: 0 1px 0 0;
    padding: 6px 0 0;
    text-align: left;
    vertical-align: bottom;
}
ul.tabNav li.current {
    font-size: 1em;
    font-weight: bold;
    padding-top: 0;
    text-align: left;
    vertical-align: bottom;
}
#merecord {
    margin-top: 7px;
}
label {
    display: inline-block;
    font-weight: 700;
    margin-bottom: 5px;
    max-width: 100%;
    vertical-align: top;
}
#whocontent {
    margin: 8px 15px 8px 15px;
}
#whocontent table tr td:nth-last-child(1){
    margin-left: 4px;
}
#who_type {
    margin-left: 28px;
    min-width: 254px;
}
#who select {
    margin-left: 32px;
    min-width: 254px;
}
#form_notes {
    margin-left: 27px;
    min-width: 254px;
}
#form_provider1,#form_pharmacy{
    margin-left: 0px !important;
}
#form_facility{
     margin-left: 18px !important;;
}
#form_payer{
     margin-left: 26px !important;;
}
#form_temp {
    margin-right: 6px;
}
.pdfpreview{display: none;}
.tabdisabled{
    color:#585858;
    background:#a9a9a9;
    cursor:default;
}
#prevbtn {
    float: right;
}
</style>

</head>
<body class="body_top" onload="<?php echo $body_onload_code; ?>" >
<form name='new_f2f' method='post' action='f2f_form.php?f2fid=<?php echo htmlspecialchars( $id, ENT_QUOTES); ?>' onsubmit='return validate(this)'>
<input type='hidden' id="hdnmode" name='mode' value='add'>
<input type='hidden' id="hdnEncId" name='hdnEncId' value='<?php echo $encounter_id;?>'>
<input type="hidden" id="pid" name="pid" value='<?php echo $pid; ?>' />
<input type="hidden" id="enc" name="enc" value='<?php echo $encounter; ?>' />


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
                                <?php // $getenc=sqlStatement("SELECT encounter from form_encounter WHERE pid='".$pid."'");
                                 //$resenc=sqlFetchArray($getenc); ?>

                        <span><?php echo htmlspecialchars( xl('Encounter Id'), ENT_NOQUOTES); ?>:</span>
                        <span class='bold'><?php echo htmlspecialchars( xl($enc1), ENT_NOQUOTES); ?></span>

   <?php } else if($inmode1=='create') { 
                         $getPatientName1=sqlStatement("SELECT CONCAT(fname,' ',lname) AS pname2 FROM patient_data WHERE pid='".$pname."'");
                          $resPatientName1=sqlFetchArray($getPatientName1); ?>
                        <span><?php echo htmlspecialchars( xl('Patient Name'), ENT_NOQUOTES); ?>:</span>
                        <span class='bold'><?php echo htmlspecialchars( xl( $resPatientName1['pname2']), ENT_NOQUOTES);?></span><br>
                               <?php if($_REQUEST['enc']==''){ 
                                         $getenc1=sqlStatement("SELECT encounter from form_encounter WHERE pid='".$pname."'");
                                         $resenc1=sqlFetchArray($getenc1); 
                                         $enc_create=$resenc1['encounter'];
                                }else {
                                    $enc_create=$_REQUEST['enc'];
                                }
                                ?>
                        
                         <span><?php echo htmlspecialchars( xl('Encounter Id'), ENT_NOQUOTES); ?>:</span>
                        <span class='bold'><?php echo htmlspecialchars( xl($enc_create), ENT_NOQUOTES); ?></span>
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
            <li class="current">
                <a href="/play/javascript-tabbed-navigation/" id="div2">Face To Face</a>
            </li>
        </ul><br>
        <div class="tabContainer">
            <div class="tab current">
            <div id="single_sel_dos" >
                <label>Date of Service: </label>
                    <select id="form_date_of_service" name = "form_date_of_service"  >
                        <option value ="" selected>Select</option>
                        <?php 
                        $ds = sqlStatement("SELECT fe.date,DATE_FORMAT(fe.date,'%Y-%m-%d') as fdate,openemr_postcalendar_categories.pc_catname
                                        FROM forms f
                                        INNER JOIN lbf_data lb ON lb.form_id = f.form_id
                                        INNER JOIN form_encounter fe ON fe.encounter = f.encounter
                                        left join openemr_postcalendar_categories on fe.pc_catid=openemr_postcalendar_categories.pc_catid
                                        WHERE f.deleted=0 AND f.formdir = 'LBF2' AND lb.field_id='f2f_stat' AND lb.field_value='finalized'
                                        AND f.pid ='$pid' order by fdate");
                        
                        while ($dos3 = sqlFetchArray($ds)) { 
                            if(!isset($_REQUEST['group_name2'])):
                                echo "<option value =".$dos3['fdate'].">".$dos3['fdate']."</option>";
                            else:
                                $sql = sqlStatement("SELECT date_of_service FROM tbl_form_chartoutput_transactions WHERE id = $id ");
                                while ($sql2 = sqlFetchArray($sql)) {
                                   $date  =  $sql2['date_of_service'];
                                }
                                $d = explode(',', $date);
                                echo "<option value =".$dos3['fdate']. " ";
                                foreach($d as $val){
                                    if($val == $dos3['fdate']){
                                        echo "selected";
                                    }
                                }
                                echo ">".$dos3['fdate']. "</option>";
                            endif;
                        }
                         ?>

                    </select>
                <?php $rowcount=mysql_num_rows($ds);
                if($rowcount==0)  { ?><label style="color:red;">* Please Finalize Face To Face HH Plan for this encounter.</label> <?php } ?>
                </div>
              <fieldset id='ftab2' class="fsStyle">
                    <legend class="legendStyle">Who:</legend>
                         <label>Type:</label>
                         <?php 
                            $sql12=sqlStatement("select * from tbl_form_chartoutput_transactions where id=$id and transaction=2 and pid=$pid");
                            $res21=sqlFetchArray($sql12);
                        ?>
                        <select id="who_type" name = "who_type"  onchange="obj_type(this);">
                            <option value ="">Select</option>
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
                        <div id="who"></div>
                       
                       <div id="nots"><label>Notes:</label><textarea name="form_notes2" id="form_notes" title="" cols="30" rows="5"><?php echo $res21['notes']; ?></textarea>
                           <a href='#' class='show-pop-async addnots'  title="Click to see Template"data-placement='right-bottom'><i class="glyphicon glyphicon-new-window"></i></a></div>
              </fieldset><br>
               <div class="groupcon">
                    
                    <?php if(!isset($_REQUEST['group_name2'])): ?>
                    <label>Group </label>
                    <?php $groups1 = sqlStatement("SELECT DISTINCT(group_name) as group_name FROM layout_options " .
                  "WHERE form_id = 'CHARTOUTPUT' AND uor > 0 " .
                  "ORDER BY group_name"); 
                    ?>
                    <select id ="chartgroups"  onchange="javascript:group_selected($('#tab2'));">
                        <option value=""> Select </option>
                        <?php
                        while ($groups2 = sqlFetchArray($groups1)) {
                             ?><option value ="<?php echo substr($groups2['group_name'],0,1);?>" <?php if($_REQUEST['group_name2']==substr($groups2['group_name'],0,1)) { echo "selected"; }?>><?php echo substr($groups2['group_name'],1); ?></option><?php 
                        }
                        ?>
                    </select>

                    <input type="hidden" id ="chartgroupshidden" name = 'chartgroupshidden' value=""/>
                    <?php else:
                        if ($_REQUEST['type']!="Patient Specific"){
                            echo "Group Name: <b>".substr($_REQUEST['group_name'],1)."</b>";
                    ?><script>
                                javascript:group_selected($('#tab2'));
                            </script>
                            <input type="hidden" id ="chartgroupshidden" name = 'chartgroupshidden' value="<?php echo $_REQUEST['group_name']; ?> "/>
                        <?php  } 
                        endif;

                        ?>

                    <div id="data"></div>
               </div></div>
        </div>
            
    </div>
</div>
    <div id="addr_bk"></div>
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
   
    $res1=sqlStatement("SELECT DISTINCT(fe.date)as date,DATE_FORMAT(fe.date,'%Y-%m-%d') as fdate
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
