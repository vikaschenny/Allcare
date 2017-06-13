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

 

if ($mode) {   
  /**use sql placemaker**/
     ///
    
     if(!empty($_REQUEST['dos'][0]) || $_REQUEST['dos']!='')
      {
        $trans_type='Patient Encounter Specific';
        $group_name =  $_REQUEST['chartgroupshidden2'];
        if(is_string($_REQUEST['dos'])==1) $dos=$_REQUEST['dos'];
        else $dos = implode(',',$_REQUEST['dos']);
        $refer_to=$_REQUEST['refer_to']? $_REQUEST['refer_to']:0;
        $provider=$_REQUEST['form_provider1']? $_REQUEST['form_provider1']:0;
        $facility=$_REQUEST['form_facility']? $_REQUEST['form_facility']:0;
        $pharmacy=$_REQUEST['form_pharmacy']? $_REQUEST['form_pharmacy']:0;
        $payer=$_REQUEST['form_payer']? $_REQUEST['form_payer']:0;
        $who_type=$_REQUEST['who_type2'];
        $notes=$_REQUEST['form_notes2'];
        $form_temp=$_REQUEST['form_temp']; 
        $type=$_REQUEST['transtype'];
        
        $sqlBindArray=array();
        if($type==2) {
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
        }else {
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
        }
        
        
        if ($id) {       
            //use sql placemaker
            array_push($sqlBindArray,date("Y-m-d"),$dos,$group_name,$refer_to,$provider,$facility,$pharmacy,$payer,$who_type, $notes,$form_temp,$trans_type,$type,$id);
            $sets .= " updated_date = ?,date_of_service = ?, chart_group=?, refer_to=?,provider=? , facility=?, pharmacy=? ,payer=?,who_type=? ,notes=? ,form_template=?,trans_type=?,transaction=?";
            $sets1= rtrim($sets,',');
            sqlStatement("UPDATE tbl_form_chartoutput_transactions SET $sets1 WHERE id = ?", $sqlBindArray);

         }else {
            //use sql placemaker
            array_push($sqlBindArray,$pid,date("Y-m-d"),date("Y-m-d"), $dos,$group_name,$refer_to,$provider,$facility,$pharmacy,$payer,$who_type,$notes,$form_temp,$trans_type,$type);
            $sets .= " pid = ?".","."created_date = ?".","."updated_date = ?,date_of_service = ?,chart_group=?,refer_to=?, provider=? , facility=?, pharmacy=? ,payer=?,who_type=?,notes=?,form_template=?,trans_type=?,transaction=?";
            $id = sqlInsert("INSERT INTO tbl_form_chartoutput_transactions SET $sets", $sqlBindArray);
        }    
         if ($GLOBALS['concurrent_layout'])
                    $body_onload_code = "javascript:location.href='add_chartoutput.php?id=$id&group=$group_name';";
         else
                    $body_onload_code = "javascript:parent.Layout-Based Visit Forms.location.href='add_chartoutput.php?id=$id&group=$group_name';";
    }else {
        $trans_type='Patient Specific';
        $group_name =  $_REQUEST['chartgroupshidden1'];
        //$dos = implode(',',$_REQUEST['dos']);
        $refer_to=$_REQUEST['refer_to']? $_REQUEST['refer_to']:0;
        $provider=$_REQUEST['form_provider1']? $_REQUEST['form_provider1']:0;
        $facility=$_REQUEST['form_facility']? $_REQUEST['form_facility']:0;
        $pharmacy=$_REQUEST['form_pharmacy']? $_REQUEST['form_pharmacy']:0;
        $payer=$_REQUEST['form_payer']? $_REQUEST['form_payer']:0;
        $who_type=$_REQUEST['who_type1'];
        $notes=$_REQUEST['form_notes1'];
        $form_temp=$_REQUEST['form_temp']; 
        $provider_sign=$_REQUEST['psign'];
        
        if($provider_sign=='yes' && $form_temp!=''){
            $sqlBindArray=array();   
            if($_REQUEST['new_trans']!='' && $id==''){
               array_push($sqlBindArray, $_REQUEST['new_trans']);
               $sets.='id=?'.',';
            }
            $fres = sqlStatement("SELECT * FROM layout_options " .
                "WHERE form_id = 'NONENC' AND uor > 0 AND field_id != '' AND group_name = '$group_name' " .
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
                array_push($sqlBindArray,date("Y-m-d"),$group_name,$refer_to,$provider,$facility,$pharmacy,$payer,$who_type, $notes,$form_temp,$trans_type,$provider_sign,$id);
                $sets .= " `date` = ?, `group`=?, refer_to=?,provider=? , facility=?, pharmacy=? ,payer=?,who_type=? ,notes=? ,form_template=?,trans_type=?,provider_sign=?";
                $sets1= rtrim($sets,',');
                sqlStatement("UPDATE tbl_nonencounter_data SET $sets1 WHERE id = ?", $sqlBindArray);

             }else {
                //use sql placemaker
                array_push($sqlBindArray,$pid,date("Y-m-d"),$group_name,$refer_to,$provider,$facility,$pharmacy,$payer,$who_type,$notes,$form_temp,$trans_type,$provider_sign);
               
                $sets .= " pid = ?, `date` = ?, `group`=?, refer_to=?, provider=? , facility=?, pharmacy=? ,payer=?,who_type=?,notes=?,form_template=?,trans_type=?,provider_sign=?";
                $id = sqlInsert("INSERT INTO tbl_nonencounter_data SET $sets", $sqlBindArray); 
                
             }      
            if ($GLOBALS['concurrent_layout']) 
                $body_onload_code = "javascript:location.href='add_chartoutput.php?id=$id&group=$group_name';";
            else
                $body_onload_code = "javascript:parent.Layout-Based Visit Forms.location.href='add_chartoutput.php?id=$id&group=$group_name';";
        }else {
            echo "<script>alert('Transaction is not created');</script>";
        }
        
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
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/common.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/jquery.1.3.2.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/fancybox/jquery.fancybox-1.2.6.js"></script>
<script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>		
<script src="../popover/js/jquery.webui-popover.js"></script>
<script type="text/javascript">
var template_save=false; 
var templatedata='';
function formdata(data) {
    $('.pdfpreview').click()
    templatedata=data;
    
}
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

var locationhost = window.location.host;
var templates=[];
var chartgroup=$('#chartgroupshidden').val();

<?php $sql=sqlStatement("select * from list_options where list_id='form_templates'");
      while($row=sqlFetchArray($sql)) { ?>
          templates.push('template_forms/'+'<?php echo $row['notes']; ?>'+'?coid='+<?php echo $id; ?>+'&patient_id='+<?php echo $pid; ?>+'&temp_id='+'<?php echo $row['option_id'] ?>'+'&group1='+'<?php echo $_REQUEST['group_name'] ;?>');
    <?php   } ?>
       // alert(JSON.stringify(templates)); 

pdfpreviewSettings = {
        width:600,
        height:600,
        delay:{show:2000,hide:1000},
        closeable:true,
        title:'',
        trigger: 'click',
        dismissible:true,
        type:'iframe',
	url:"",
        onHide: function($element) {}
};

$(document).ready(function(){
    tabbify();
    var groupname = '<?php echo $_REQUEST['type']; ?>';
    if (groupname!="Patient Specific"){
        $('#div2').click();
        if($('#dos').val() !="" || $('#who_type').val()!="")
          disabledtab($('#div1'));
    }else{
        $('#div1').click();
        if($('#form_temp').val() !="" || $('#who_type').val()!="")
           disabledtab($('#div2'));
    }
    var value = $('.tabContainer .current').find("#who_type").val();
    largeSettings.content = getnodes(value);
    $('a.addnots').webuiPopover('destroy').webuiPopover($.extend({},settings,largeSettings));
    if(value.trim()!=='') {
        //$('#nots .addnots').css("display","inline-block");
       
        $.ajax({
           type: 'POST',
           url: "who_type.php",	
           data:{type:value,id:'<?php echo $id; ?>',trtype:groupname},
           success: function(response)
           {
               
             var $parentcon = $('.tabContainer .current').find('fieldset[id *="ftab"]');
             $parentcon.find('#who').html(response);
             $('#who br').remove();
             showhoTypeCotentOnload($parentcon);
             showhotypecotent($parentcon)
           },
           failure: function(response)
           {
               alert("error"); 
           }		
       });	
  }
  //for group population 
    var templates1=[];
    $('#form_temp').change(function(){
        var datastring = $("#new_chartoutput").serialize();
        for (index = 0; index < templates.length; ++index) {
        if($(this).val()==$('#form_temp').val()){
              templates1.push(templates[index]+'&group='+$('#chartgroupshidden').val()+"&"+datastring);
            }
        }
        tabendis($('.tabNav li[class=""]').find('a'),[$(this).val(),$("#tab1").find("#who_type").val(),$("#tab1").find("#chartgroups").val()]);
        if($(this).val() !=""){
            $('.pdfpreview').show();
            pdfpreviewSettings.url = templates1[($('option:selected',$(this)).index() - 1)];
            pdfpreviewSettings.title = $('#form_temp option:selected').text();
            $('.pdfpreview').prop("title","Click to see "+pdfpreviewSettings.title);
            $('a.pdfpreview').webuiPopover('destroy').webuiPopover($.extend({},settings,pdfpreviewSettings));
            setTimeout(function(){$('a.pdfpreview').webuiPopover('show')},100);
           
        }else
            $('.pdfpreview').hide();
    });
  
  $('#dos').change(function(){
      tabendis($('.tabNav li[class=""]').find('a'),[$(this).val(),$("#tab2").find("#who_type").val(),$("#tab2").find("#chartgroups").val()]);
  });
  
    //for onload for form templates
     var mode='<?php echo $mode; ?>'
        if($('#form_temp').val() !="" && mode ==""){
            
            $('.pdfpreview').show();
            pdfpreviewSettings.url = templates[($('option:selected',$('#form_temp')).index() - 1)];
            pdfpreviewSettings.title = $('#form_temp option:selected').text();
            $('.pdfpreview').prop("title","Click to see "+pdfpreviewSettings.title);
            $('a.pdfpreview').webuiPopover('destroy').webuiPopover($.extend({},settings,pdfpreviewSettings));
            setTimeout(function(){$('a.pdfpreview').webuiPopover('show')},500);
        }
        transaction();
});

function disabledtab($target){
   if(!$($target).parent().is( "div" )){
        $target.wrap( "<div class='disabledtab'></div>");
        $target.addClass('tabdisabled');
        $target.click(function(e){
             e.preventDefault();
         });
    }
}
function enabledtab($target){
    if($($target).parent().is("div")){
        $target.removeClass('tabdisabled');
       $target.unwrap();
    }
}

function tabendis($target,selecters){
    $(selecters).each(function(index,value){
        if(value !=""){
           disabledtab($target);
           return false
        }else{
             enabledtab($target);
        }    
    });
}

function list_val(id){
 var Alltext = $('div.current').find('#form_notes').val()+id+";";
 $('div.current').find('#form_notes').val(Alltext); 
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
        var errorno=0;
        if(($("#single_sel_dos").parent().hasClass('current')=='true' || $("#muti_sel_dos").parent().hasClass('current')=='true' )&& $("#dos").val()==""){
            alert("Please Select Date of Service");
            errorno++;
        }else if($("#form_temp").parent().hasClass('current')){
            if($("#form_temp").val() =="" && $("#psign").val()=="") {
                 alert("Please Select Form Template and Provider Sign");
                 errorno++;
             }
            else if($("#form_temp").val()=="") {
                alert("Please Select Form Template");
                errorno++;
            }
            else if($("#psign").val()=="") {
                alert("Please Select Provider Sign");
                errorno++;
            }
        }
        if(errorno==0){
           
          $.ajax({
           type: 'POST',
           url: "template_forms/template_form_save.php",	
           data:{data:templatedata,formtemp:$('#form_temp').val()},
           success: function(response)
           {
             // alert(response);
               <?php if($id==0){ ?>
                   template_save='true';
               <?php } ?>
               document.getElementById('new_trans').value=response;
           },
           failure: function(response)
           {
               alert("error"); 
           }		
        });	  
         f.submit();
        }

    }
}

function closeme(){
  if(template_save=='true') {
       alert('Please create transaction. otherwise your data will be lost..');
       if($('#new_trans').val()!=''){
           $.ajax({
           type: 'POST',
           url: "template_forms/template_form_save.php",	
           data:{action:'deleterow',delid:$('#new_trans').val()},
           success: function(response)
           {
                window.location.href = 'add_chartoutput.php'; 
           },
           failure: function(response)
           {
               alert("error"); 
           }		
        });	

       }
   }else {
        window.location.href = 'add_chartoutput.php'; 
   }
  
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
function obj_type($element){
    tabendis($('.tabNav li[class=""]').find('a'),[$($element).parents('.current').children('select').val(),$($element).parents('.current').find("#who_type").val(),$($element).parents('.current').find("#chartgroups").val()]);
    var value = $($element).val();
    var $parentcon = $($element).parents('fieldset');
    largeSettings.content = getnodes(value);
    $parentcon.find('a.addnots').webuiPopover('destroy').webuiPopover($.extend({},settings,largeSettings));
    if(value.trim()!=='') {
        $.ajax({
            type: 'POST',
            url: "who_type.php",	
            data:{type:value,id:'<?php echo $id; ?>'},
            success: function(response)
            {
             $parentcon.children('#who').html(response);
             $('#who br').remove();
             showhotypecotent($parentcon)
            },
            failure: function(response)
            {
                alert("error"); 
            }		
        });	
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
          //$parentcon.find('a.whopreview').webuiPopover('show');
       }
    },1000);
}
function previewpost(){
    var datastring = $("#new_chartoutput").serialize();
    location.href = 'preview_chartoutput.php?'+datastring;
}

function group_selected($target){
    tabendis($('.tabNav li[class=""]').find('a'),[$target.children('select').val(),$target.find("#who_type").val(),$target.find("#chartgroups").val()]);
    
    if( '<?php echo isset($_REQUEST['group_name']); ?>' != '' ){
        var chartgroupshidden = '<?php echo $_REQUEST['group_name']; ?>';
        $($target).children('.groupcon').find('#chartgroupshidden').val(chartgroupshidden);
    }else{
        var gettarget = $($target).children('.groupcon').find('#chartgroups')
        var chartgroupshidden = $(gettarget).find('option:selected').val()+$(gettarget).find('option:selected').text();
        $($target).children('.groupcon').find('#chartgroupshidden').val(chartgroupshidden);
        
     }
     
     $.ajax({
            url: 'chart_output_dynamic.php',
            type: 'POST',
            data:  { chartgroupshidden:chartgroupshidden,id:<?php echo $id; ?>,f2ftrans:$('#transtype').val()},
            success: function(content)
            {
                if($('#transtype').val()==2){ 
                    document.getElementById('data').style.display='none';
                }else {
                    $($target).children('.groupcon').find("#data").html(content);
                    $('#data > div > div[class="tabContainer"]').removeClass('tabContainer');
                    //return content;
                }
            }  
    });
}

function non_encounter_group($target){
    tabendis($('.tabNav li[class=""]').find('a'),[$target.children('select').val(),$target.find("#who_type").val(),$target.find("#chartgroups").val()]);
    
    if( '<?php echo isset($_REQUEST['group_name']); ?>' != '' ){
        var chartgroupshidden = '<?php echo $_REQUEST['group_name']; ?>';
        $($target).children('.groupcon').find('#chartgroupshidden').val(chartgroupshidden);
    }else{
        var gettarget = $($target).children('.groupcon').find('#chartgroups')
        var chartgroupshidden = $(gettarget).find('option:selected').val()+$(gettarget).find('option:selected').text();
        $($target).children('.groupcon').find('#chartgroupshidden').val(chartgroupshidden);
        
     }
     
     $.ajax({
            url: 'non_enc_dynamic.php',
            type: 'POST',
            data:  { chartgroupshidden:chartgroupshidden,id:<?php echo $id; ?>,f2ftrans:0},
            success: function(content)
            {
                
                if($('#transtype').val()==2){ 
                    document.getElementById('data').style.display='none';
                }else {
                    $($target).children('.groupcon').find("#data").html(content);
                    $('#data > div > div[class="tabContainer"]').removeClass('tabContainer');
                    //return content;
                }
            }  
    });
}

function transaction(){

if($('#transtype').val()==1){
    document.getElementById('muti_sel_dos').style.display='block';
    document.getElementById('single_sel_dos').style.display='none';
    $('#single_sel_dos').find('select').attr('disabled','disabled');
    $('#muti_sel_dos').find('select').attr('disabled',false);
     document.getElementById('preview').style.display='block';
}else if($('#transtype').val()==2) {
    document.getElementById('single_sel_dos').style.display='block';
    document.getElementById('muti_sel_dos').style.display='none';
    $('#muti_sel_dos').find('select').attr('disabled','disabled');
    $('#single_sel_dos').find('select').attr('disabled',false);
    document.getElementById('preview').style.display='none';
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
<form id="new_chartoutput" name='new_chartoutput' method='post' action='chart_output.php?coid=<?php echo htmlspecialchars( $id, ENT_QUOTES); ?>&location=<?php echo $location; ?>&provider=<?php echo $provider;?>&pid=<?php echo $pid; ?>' onsubmit='return validate(this)'>
<input type='hidden' id="hdnmode" name='mode' value='add'>
<input type='hidden' id="hdnEncId" name='hdnEncId' value='<?php echo $encounter_id;?>'>
<input type='hidden' id="hdnEncId" name='hdnEncId' value='<?php echo $encounter_id;?>'>
<input type='hidden' id="patientid" name='patientid' value='<?php echo $pid;?>'>
<input type="hidden" id="new_trans" name="new_trans" value="" />
    <table>
     <tr>
        <td>
            <a href="javascript:;"  <?php if (!$GLOBALS['concurrent_layout']) echo "target='Main'"; ?> class="css_button" onclick="submitme();">
                <span><?php echo htmlspecialchars( xl('Save'), ENT_NOQUOTES); ?></span>
            </a>
        </td>
        

        <td>
<!--            <a href="add_chartoutput.php"  <?php if (!$GLOBALS['concurrent_layout']) echo "target='Main'"; ?> class="css_button" >
                <span><?php echo htmlspecialchars( xl('Cancel'), ENT_NOQUOTES); ?></span>
            </a>-->
            <a href="javascript:;"  <?php if (!$GLOBALS['concurrent_layout']) echo "target='Main'"; ?> class="css_button" onclick="closeme();">
                <span><?php echo htmlspecialchars( xl('Cancel'), ENT_NOQUOTES); ?></span>
            </a>
       </td>
                      
           
   <td style='width:150px;'>&nbsp;</td>
           <!-- <td>
                 <a href="javascript:;" <?php if (!$GLOBALS['concurrent_layout']) echo "target='Main'"; ?> class="css_button" onclick="previewpost();">
                    <span><?php echo htmlspecialchars( xl('Preview'), ENT_NOQUOTES); ?></span>
                </a>
            </td>-->     
     </tr>
    </table>
<?php 

//$dos = sqlStatement("SELECT DATE_FORMAT(f.date, '%Y-%m-%d') as date, o.pc_catname as pc_catname FROM form_encounter f INNER JOIN openemr_postcalendar_categories o ON f.pc_catid = o.pc_catid INNER JOIN form_encounter fe ON  fe.encounter=f.encounter WHERE f.pid = $pid ");
$dos = sqlStatement("SELECT fe.encounter, DATE_FORMAT(fe.date, '%Y-%m-%d') as date,openemr_postcalendar_categories.pc_catname FROM form_encounter AS fe ".
    " left join openemr_postcalendar_categories on fe.pc_catid=openemr_postcalendar_categories.pc_catid  WHERE fe.pid = ? order by fe.date desc", array($pid));
?>

<div id='chartdiv'>
     <div id="merecord">
        <ul class="tabNav">
            <li class="current">
                <a href="/play/javascript-tabbed-navigation/" id="div1">Patient Specific</a>
            </li>
	    <li>
                <a href="/play/javascript-tabbed-navigation/" id="div2">Patient Encounter Specific</a>
            </li>
        </ul>
        <div class="tabContainer">
            <div class="tab current" id="tab1">
                <div class="groupcon">
                    <?php  $groups = sqlStatement("SELECT DISTINCT(group_name) as group_name FROM layout_options " .
                                                  "WHERE form_id = 'NONENC' AND uor > 0 " .
                                                  "ORDER BY group_name");
                    if(!isset($_REQUEST['group_name2'])):?>
                    <label>Group </label>
                    <select id ="chartgroups"  onchange="javascript:non_encounter_group($('#tab1'));">
                        <option value=""> Select </option>
                        <?php 
                        while ($groups2 = sqlFetchArray($groups)) {
                             ?><option value ="<?php echo substr($groups2['group_name'],0,1);?>" <?php if($_REQUEST['group_name2']==substr($groups2['group_name'],0,1)) { echo "selected"; }?>><?php echo substr($groups2['group_name'],1); ?></option><?php 
                        }
                        ?>
                    </select>
                            
                    <input type="hidden" id ="chartgroupshidden" name = 'chartgroupshidden1' value=""/>
                    <?php else:
                        if ($_REQUEST['type']=="Patient Specific"){
                            echo "Group Name: <b>".substr($_REQUEST['group_name'],1)."</b>";
                            ?><script>
                                javascript:non_encounter_group($('#tab1'));
                                </script>
                                <input type="hidden" id ="chartgroupshidden" name = 'chartgroupshidden1' value="<?php echo $_REQUEST['group_name']; ?> "/>
                        <?php }  
                        endif;

                        ?>

                    <div id="data"></div>
                </div>
                <?php $sql_temp = sqlStatement("SELECT form_template FROM tbl_nonencounter_data WHERE id = $id and trans_type='Patient Specific'"); $sql_row = sqlFetchArray($sql_temp) ?><b>Form Template: </b> <select id="form_temp" name="form_temp"  ><option value="" selected>Select</option><?php $sql_form=sqlStatement("select * from list_options where list_id='Form_Templates'"); 
                while ( $form_row=sqlFetchArray($sql_form)) { 
                      echo "<option value =".$form_row['option_id']." "; if($sql_row['form_template']==$form_row['option_id']){ echo "selected"; } echo ">".$form_row['title'] ."</option>";
                 }
                ?></select><a href='#' class='show-pop-async pdfpreview'  title="Click to see Template" data-placement='right-bottom'><i class="glyphicon glyphicon-new-window"></i></a>
                <fieldset id='ftab1' class="fsStyle">
                    <legend class="legendStyle">Transaction:</legend>
                         <label>Who:</label>
                         <?php 
                            $sql12=sqlStatement("select * from tbl_nonencounter_data where id=$id and trans_type='Patient Specific'");
                            $res21=sqlFetchArray($sql12);
                        ?>
                        <select id="who_type" name = "who_type1"  onchange="obj_type(this);">
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
                       
                        <div id="nots"><label>Notes:</label><textarea name="form_notes1" id="form_notes" title="" cols="30" rows="5"><?php echo $res21['notes']; ?></textarea>
                           <a href='#' class='show-pop-async addnots'  title="Click to see Template"data-placement='right-bottom'><i class="glyphicon glyphicon-new-window"></i></a></div>
                           <div id="editable"></div>
                           <label>Provider Sign:</label><select name="psign" id="psign"><option value="">select</option><option value="yes" <?php if($res21['provider_sign']=='yes') echo "selected" ; ?>>YES</option><option value="no" <?php if($res21['provider_sign']=='no') echo "selected" ; ?>>NO</option></select>
                </fieldset>
                
                
            </div>
            <div class="tab" id="tab2">
                
                <?php   $sql12 = sqlStatement("SELECT * FROM tbl_form_chartoutput_transactions WHERE id = $id ");
                        $sql23 = sqlFetchArray($sql12);
                        
                        $date1  =  $sql23['date_of_service'];
                                ?>
                <label>Transaction Type:</label><select id="transtype" name="transtype" onchange="transaction();" ><option value="">select</option><option value="1" <?php if($sql23['transaction']==1 || $sql23['transaction']=='' ) echo "selected"; ?>>Medical Record</option><option value="2" <?php if($sql23['transaction']==2) echo "selected"; ?> >Face To Face</option></select>
                <div id="muti_sel_dos" style="display:none;">
                <label>Date of Service: </label>
                    <select id="dos" name = "dos[]"  multiple >
                        <option value ="" selected>Select</option>
                        <?php 
                        while ($dos2 = sqlFetchArray($dos)) { 
                            if(!isset($_REQUEST['group_name2'])):
                                echo "<option value =".$dos2['date'].">".$dos2['date']."-".$dos2['pc_catname'] ."</option>";
                            else:
//                                $sql = sqlStatement("SELECT date_of_service FROM tbl_form_chartoutput_transactions WHERE id = $id and trans_type='Encounter Based'");
//                                while ($sql2 = sqlFetchArray($sql)) {
//                                   $date  =  $sql2['date_of_service'];
//                                }
                                $d = explode(',', $date1);
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
                </div>
                <div id="single_sel_dos" style="display:none;">
                <label>Date of Service: </label>
                    <select id="dos" name = "dos"  >
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
                                echo "<option value =".$dos3['fdate'].">".$dos3['fdate']."-".$dos3['pc_catname'] ."</option>";
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
                                echo ">".$dos3['fdate']."-".$dos3['pc_catname']. "</option>";
                            endif;
                        }
                         ?>

                    </select>
                <?php $rowcount=mysql_num_rows($ds);
                if($rowcount==0)  { ?><label style="color:red;">* Please Finalize Face To Face HH Plan for this encounter.</label> <?php } ?>
                </div><br>
                    <div id="prevbtn">
                        <a href="javascript:;" <?php if (!$GLOBALS['concurrent_layout']) echo "target='Main'"; ?> id='preview' class="css_button" onclick="previewpost();">
                        <span><?php echo htmlspecialchars( xl('Preview'), ENT_NOQUOTES); ?></span>
                        </a>
                    </div><br>
                    <fieldset id='ftab2' class="fsStyle">
                    <legend class="legendStyle">Who:</legend>
                         <label>Type:</label>
                         <?php 
                            $sql12=sqlStatement("select * from tbl_form_chartoutput_transactions where id=$id ");
                            $res21=sqlFetchArray($sql12);
                        ?>
                        <select id="who_type" name = "who_type2"  onchange="obj_type(this);">
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
                </fieldset> <br>
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

                    <input type="hidden" id ="chartgroupshidden" name = 'chartgroupshidden2' value=""/>
                    <?php else:
                        if ($_REQUEST['type']!="Patient Specific"){
                            echo "Group Name: <b>".substr($_REQUEST['group_name'],1)."</b>";
                    ?><script>
                                javascript:group_selected($('#tab2'));
                            </script>
                            <input type="hidden" id ="chartgroupshidden" name = 'chartgroupshidden2' value="<?php echo $_REQUEST['group_name']; ?> "/>
                        <?php  } 
                        endif;

                        ?>

                    <div id="data"></div>
                </div>
            </div>
     </div>

</div>

<!-- include support for the list-add selectbox feature -->
<?php include $GLOBALS['fileroot']."/library/options_listadd.inc"; ?>
</form></body>
<script language="JavaScript">
<?php echo $date_init; ?>
</script>

</html>
