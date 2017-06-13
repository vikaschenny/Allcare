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
}

$ignoreAuth=true; // ignore the standard authentication for a regular OpenEMR user
include_once('../../interface/globals.php');

//include_once('../../interface/globals.php');
include_once("$srcdir/api.inc");
require_once("$srcdir/patient.inc");
require_once("$srcdir/options.inc.php");
require_once("$srcdir/htmlspecialchars.inc.php");
include_once("$srcdir/acl.inc");
formHeader("Form:Codes");
$pid    = $_REQUEST['pid'];
if($_REQUEST['isSingleView'] == 1)
    $isSingleView = 1;
else
    $isSingleView = 0;

if($_REQUEST['isFromCharts'] == 1)
    $isFromCharts = 1;
else
    $isFromCharts = 0;
?>
<html>
<head> 
<?php html_header_show();?> 
<meta content="width=device-width,initial-scale=1.0" name="viewport">
<script type="text/javascript" src="../../../library/dialog.js"></script>
<!-- pop up calendar -->
<style type="text/css">@import url(<?php echo $GLOBALS['webroot'] ?>/library/dynarch_calendar.css);</style>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/dynarch_calendar.js"></script>
<?php include_once("{$GLOBALS['srcdir']}/dynarch_calendar_en.inc.php"); ?>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/dynarch_calendar_setup.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/textformat.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/dialog.js"></script>
<link rel="stylesheet" href="<?php echo $css_header;?>" type="text/css">
<link rel="stylesheet" type="text/css" href="css/assets/css/bootstrap.min.css">
<link rel="stylesheet" href="css/gh-buttons.css">
<link rel="stylesheet" href="css/font-awesome.min.css">
<link rel="stylesheet" href="css/bootstrap-multiselect.css" type="text/css"/>
<link rel="stylesheet" href="css/customize.css" type="text/css">
<style type="text/css">
.form-group select + .btn-group {
    width: 100% !important;
	display:table-cell;
	float:none;
}
.multiselect {
    width: 100% !important;
	text-align:left;
	white-space:normal;
	height:34px;
	font-size:15px;
}
.multiselect .caret{
	float:right;
	margin-top: -11px;
}
.multiselect-container{
	width:100% !important;
}
.btn-primary{
	margin-right:15px;
}


</style>
<script src="js/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="js/bootstrap.min.js"></script>
<script type="text/javascript" src="js/bootstrap-multiselect.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/common.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        var encounterid = '<?php echo $_REQUEST['encounter']; ?>';
         var pid = '<?php echo $_REQUEST['pid']; ?>';
        jQuery.ajax({
            type: 'POST',
            url: "check_billed.php",
            dataType : "json",
            data: {
                    encounterid : encounterid,
                    pid : pid
                },

            success: function(data)
            {
                var stringified = '';
                stringified = JSON.stringify(data, undefined, 2);
                var objectified = jQuery.parseJSON(stringified);
//                    alert(stringified);
                for(var key in objectified ){
                    if(objectified[key] == 1){
                         jQuery("#my_form :input").attr("disabled", true);
                         jQuery("#display_field").show();
                         jQuery("#save_btnup").hide();
                            jQuery("#cptdivcheck").hide();
                            jQuery("#deletecpt").hide();
                            jQuery("#deletecpticd").hide();
                            jQuery("#deleteicd").hide();
                            jQuery("input[type=button]").hide();
                            jQuery("#icd_search").hide();
                            jQuery("#icd_search_button").hide();
                            jQuery("#icd_dropdowncontenar").hide();
                            jQuery("#save_btndown").hide();
                    }
                }
                
            },
            failure: function(response)
            {
                alert("error");
            }		
        });
        //jQuery('#deleteicd').hide();
        if(jQuery('#cpt_table').length >0){
            jQuery('#deletecpt').show();
        }else{
            jQuery('#deletecpt').hide();
        }
        $('#icd_search_button').click(function(){
            jQuery.ajax({
                type: 'POST',
                url: "codes_search.php",
                dataType : "json",
                data: {
                        code_type : 'ICD10',
                        searchstring : jQuery('#icd_search').val()
                    },

                success: function(data)
                {
                    var stringified = '';
                    jQuery('#icd_dropdown').empty();
                    jQuery('#icd_dropdown').append(jQuery('<option>', { 
                            value: '',
                            text : 'Select'
                        }));
                    stringified = JSON.stringify(data, undefined, 2);
                    var objectified = jQuery.parseJSON(stringified);
					var data = [];
					
					for(var key in objectified ){
                        data.push({label:key+" - "+objectified[key], value:key})
                    }
						
					$('#icd_dropdown').multiselect('dataprovider', data);

                },
                failure: function(response)
                {
                    alert("error");
                }		
            });
			
					
        });
		
		jQuery("#icd_dropdown").multiselect({
                    includeSelectAllOption: true,
                                enableFiltering: true,
                                maxHeight: 250,
                                buttonWidth: '175px',
                                onDropdownHide: function(event) {
                                jQuery('#icd_dropdowncontenar').find('select option:selected').each(function() {
                                           var sarchval = "ICD10:"+$(this).val();
                                           var isrowinsert = false;
                                           $('#icddiv .searchrow').find('input[type="radio"]').each(function() {
                                                  if(sarchval == $(this).val()){
                                                         isrowinsert = true;
                                                         return false;
                                                  }

                                           });

                                           if(jQuery(this).val() !== '' && !isrowinsert){
                                                  var table = document.getElementById('justify');
                                                  var rowCount = table.rows.length;
                                                  var row = table.insertRow(rowCount);
                                                  var cell01 = row.insertCell(0);
                                                  var element01 = document.createElement("input");
                                                  row.setAttribute("class","searchrow");
                                                  element01.type = "checkbox";
                                                  element01.id = "icddelete";
                                                  element01.name="icddelete[]";
                                                  cell01.appendChild(element01);
                                                  cell01.setAttribute("style","padding: 10px;");

                                                  var cell1 = row.insertCell(1);
                                                  var element1 = document.createElement("input");
                                                  element1.type = "radio";
                                                  element1.name="icd_primary";
                                                  element1.value = "ICD10:"+jQuery(this).val();
                                                  cell1.appendChild(element1);
                                                  cell1.setAttribute("style","text-align: center;");						
                                                  var element11 = document.createTextNode('Primary');
                                                  cell1.appendChild (element11);

                                                  var cell2 = row.insertCell(2);
                                                  var element2 = document.createElement("input");
                                                  element2.type = "checkbox";
                                                  element2.value = "ICD10:"+jQuery(this).val();
                                                  element2.name="icd_justify[]";
                                                  cell2.appendChild(element2);
                                                  cell2.setAttribute("style","text-align: center;");

                                                  var element21 = document.createTextNode('Justify');
                                                  cell2.appendChild (element21);

                                                  var cell3 = row.insertCell(3);

                                                  var element3 = document.createElement("input");
                                                  element3.type = "checkbox";
                                                  element3.value = "ICD10:"+jQuery(this).val();
                                                  element3.name="icd_mproblem[]";
                                                  element3.innerHTML='Active';
                                                  cell3.appendChild(element3);
                                                  cell3.setAttribute("style","text-align: center;");

                                                  var element31 = document.createTextNode('Active');
                                                  cell3.appendChild (element31);
                                                  var cell4 = row.insertCell(4);
                                                  var element4 = document.createTextNode("|ICD10:" + jQuery(this).text());
                                                  cell4.appendChild(element4);
                                                  jQuery("#icd_values").val(jQuery("#icd_values").val()  +","+ jQuery(this).val());
                                                  /*
                                                  if(jQuery("#icddelete").length > 0)
                                                         jQuery('#deleteicd').show();
                                                  else
                                                         jQuery('#deleteicd').hide();
                                                  */       
                                           }
                      });
                    }
        });
		
		$('.multiselect span').wrap("<div class='textcon'></div>");
		
        jQuery("#cptlistbutton").click(function() {
               var checkupdate2 = 0;
               var selectValues = new Array();
               jQuery('#cpt_div').find('select option:selected').each(function() {
		   selectValues.push(jQuery(this).val());
               });
               var req = jQuery.ajax({
                   type: 'POST',
                   url: "cpt_values.php",
                   dataType : "json",
                   data: {
                           selectValues : selectValues
                       },

                   success: function(data)
                   {
                       var stringified = '';
                       stringified = JSON.stringify(data, undefined, 2);
                       var objectified = jQuery.parseJSON(stringified);
                       var existingCPT = $("#existingCPT").val();
                       existingCPT = existingCPT.split(",");
                        for(var key in objectified ){
                           var exists = $.inArray(key, $('#mappedCPTs').val().split(',')) != -1;
                           if(exists) 
                               var table = document.getElementById('cpticd_table');
                           else 
                               var table = document.getElementById('cpt_table');
                           var rowCount = table.rows.length;
                           var row = table.insertRow(rowCount);
                           var cell1 = row.insertCell(0);
                           
                           var element1 = document.createElement("input");
                           element1.type = "checkbox";
                           element1.name="cpt_delete[]";
                           element1.val= key;
                           cell1.appendChild(element1);

                           var element2 = document.createTextNode(key+"-"+objectified[key].charAt(0).toUpperCase() + objectified[key].substr(1).toLowerCase());
                           cell1.appendChild(element2);
                           var cell2 = row.insertCell(1);
                           if(exists){
                               var element4 = document.createElement("select");
                               element4.id = "icdjustify_"+key;
                               element4.name = "icdjustify_"+key+"[]";
                               cell2.appendChild(element4);
                               element4.setAttribute('multiple','multiple');
                           }
                           
                           
                           var cell3 = row.insertCell(2);
                           
                           var element3 =document.createElement("input");
                           element3.type = "textbox";
                           element3.name="cpt_modifier_"+key;
                           element3.id="cpt_modifier_"+key;
                           element3.placeholder="Modifier";
                           cell3.appendChild(element3);
                           
                           var keystring = jQuery("#cpt_values").val()+","+key;
                        jQuery("#cpt_values").val(keystring);
                        if(jQuery("#cptdiv input:checkbox").length > 0)
                            jQuery('#deletecpt').show();
                        else
                            jQuery('#deletecpt').hide();
                    }    
                    checkmodifier();
                    checkRequiredPrimary();
                },
                failure: function(response)
                {
                    alert("error");
                }		
            });
            // Populate ICDs related to selected CPTs based on AllCareCPTvsICD list
                jQuery.ajax({
                    type: 'POST',
                    url: "cptvsicd.php",
                    dataType: "json",
                    data: {
                        selectValues : selectValues
                    },

                    success: function(data)
                    {   var stringified = '';
                        stringified = JSON.stringify(data, undefined, 2);
                        var objectified = jQuery.parseJSON(stringified);
                        var cptArr = [];
                        for(var key in objectified ){
                            var cptValue = key.split("$$"); // Gets CPT related to ICD
                            var selectCpt = document.getElementById("icdjustify_"+cptValue[0]);
                            var icdVal = document.createElement("option");
                            icdVal.value = objectified[key][0];
                            icdVal.innerHTML = objectified[key][0];
                            icdVal.setAttribute('selected','selected');
                            selectCpt.appendChild(icdVal);
                            
                            cptArr.push(cptValue[0]);
                            
                            var table = document.getElementById('justify');
                            var rowCount = table.rows.length;
                            var row = table.insertRow(rowCount);
                            var cell01 = row.insertCell(0);
                            var element01 = document.createElement("input");
                            row.setAttribute("class","searchrow");
                            element01.type = "checkbox";
                            element01.id = "icddelete";
                            element01.name="icddelete[]";
                            cell01.appendChild(element01);
                            cell01.setAttribute("style","padding: 10px;");

                            var cell1 = row.insertCell(1);
                            var element1 = document.createElement("input");
                            element1.type = "radio";
                            element1.name="icd_primary";
                            element1.value = "ICD10:"+objectified[key][0];
                            cell1.appendChild(element1);
                            cell1.setAttribute("style","text-align: center;");						
                            var element11 = document.createTextNode('Primary');
                            cell1.appendChild (element11);

                            var cell2 = row.insertCell(2);
                            var element2 = document.createElement("input");
                            element2.type = "checkbox";
                            element2.value = "ICD10:"+objectified[key][0];
                            element2.name="icd_justify[]";
                            cell2.appendChild(element2);
                            cell2.setAttribute("style","text-align: center;");

                            var element21 = document.createTextNode('Justify');
                            cell2.appendChild (element21);

                            var cell3 = row.insertCell(3);

                            var element3 = document.createElement("input");
                            element3.type = "checkbox";
                            element3.value = "ICD10:"+objectified[key][0];
                            element3.name="icd_mproblem[]";
                            element3.innerHTML='Active';
                            cell3.appendChild(element3);
                            cell3.setAttribute("style","text-align: center;");

                            var element31 = document.createTextNode('Active');
                            cell3.appendChild (element31);
                            var cell4 = row.insertCell(4);
                            var element4 = document.createTextNode("|ICD10:" + objectified[key][0] + " - " + objectified[key][1]);
                            cell4.appendChild(element4);
                            jQuery("#icd_values").val(jQuery("#icd_values").val()  +","+ objectified[key][0]);
                            /*
                            if(jQuery("#icddelete").length > 0)
                            jQuery('#deleteicd').show();
                            else
                            jQuery('#deleteicd').hide();
                            */
                        }
                        var cptUnique = $.unique(cptArr);
                        for(var i in cptUnique){
                            $('#icdjustify_'+cptUnique[i]).multiselect({
                            includeSelectAllOption: true,
                            enableFiltering: true,
                            maxHeight: 250,
                            buttonWidth: '175px'
                          });
                        }
                    },
                    failure: function(response)
                    {
                        alert("error");
                    }
                });

            
        var isTouchDevice = 'ontouchstart' in document.documentElement;
        });
        checkRequiredPrimary();
    });
    
</script>
  <SCRIPT language="javascript">
    function submitFunction(){
        var d = document.getElementById("cpt_Primary"); 

        if (d.hasAttribute("required")) {
            alert('Please select Primary CPT..!');
            return false;
        }else{
            return  submitFunction();
            return true;
        }
    }
    function cleardropdown(value) {
          jQuery('#'+value+'_dropdown').empty();
          if(value === 'icd'){
              $('#icd_dropdown').multiselect('dataprovider', {});
          }else{
              if(value.indexOf('icdjustify') == -1){
                  var encounterid = '<?php echo $_REQUEST['encounter']; ?>';
                  //jQuery("#"+jQuery.trim(value)+ " option:selected").removeAttr("selected");
                   $('#'+jQuery.trim(value)).multiselect('deselectAll', false);
                   $('#'+jQuery.trim(value)).multiselect('updateButtonText');
              }     
          }
      }
        function deletefields(value) {
            jQuery("#"+value+"div tr td:first-child input:checked").each(function() {
                if(value === 'icd'){
                    var  str = jQuery(this).attr('name');
                    if(jQuery('input[name="'+str+'"]:checked')  && str.indexOf('icddelete') != -1){
                        var string =  jQuery(this).closest("tr").find('td:eq(4)').text() ;
                        var string2 = string.replace("|ICD10:","");
                        var string3 = string2.split('-');
                        var string4 =  jQuery.trim(string3[0]);
                        jQuery(this).closest('tr').remove();
                        var getstring = jQuery("#icd_values").val();
                        var setstring = getstring.replace(string4,'');
                        jQuery("#icd_values").val(setstring);
                        //string4 = string4.substring(1);
                        if(string4 !== ''){
                            var encounterid = '<?php echo $_REQUEST['encounter']; ?>';
                            var pid = '<?php echo $_REQUEST['pid']; ?>';
                            jQuery.ajax({
                                type: 'POST',
                                url: "delete_icd.php",
                                dataType : "json",
                                data: {
                                    icddel : string4,
                                    encounterid : encounterid,
                                    pid : pid,
                                },

                                success: function(data)
                                {
                                    //alert("Deleted "+deletestring +" CPT from Feesheet");
                                },
                                failure: function(response)
                                {
                                    alert("error");
                                }		
                            });
                        }
                        /*
                        if(jQuery("#icddelete").length > 0)
                            jQuery('#deleteicd').show();
                        else
                            jQuery('#deleteicd').hide();
                        */
                    }
                }
                else if(value === 'cpticd'){
                    if(jQuery("#"+value+"div input:checkbox").length == 1)
                        jQuery('#deletecpt').hide();
                    else
                        jQuery('#deletecpt').show();
                    
                    var string =  jQuery(this).closest("tr").find('td:eq(0)').text() ;
                    var stringid = 0;
                    stringid = jQuery(this).closest("tr").find('input[type=checkbox]').val();
//                    var string2 = string.replace("|ICD10:","");
                    var string3 = string.split('-');
                    var deletestring =  jQuery.trim(string3[0]);
//                       alert(stringid);
                    jQuery(this).closest('tr').remove();
                    var getstring = jQuery("#cpt_values").val();
//                    alert(setstring+"=="+getstring);
                    var setstring = getstring.replace(deletestring,'');
                    if(stringid !== ''){
                        var encounterid = '<?php echo $_REQUEST['encounter']; ?>';
                        var pid = '<?php echo $_REQUEST['pid']; ?>';
                        jQuery.ajax({
                            type: 'POST',
                            url: "delete_cpt.php",
                            dataType : "json",
                            data: {
                                deletestring : deletestring,
                                encounterid : encounterid,
                                pid : pid,
                                stringid : stringid
                            },

                            success: function(data)
                            {
                                //alert("Deleted "+deletestring +" CPT from Feesheet");
                            },
                            failure: function(response)
                            {
                                alert("error");
                            }		
                        });
                    }
//                        alert(setstring);
                    jQuery("#cpt_values").val(setstring);
                }else{
                    if(jQuery("#"+value+"div input:checkbox").length == 1)
                        jQuery('#deletecpt').hide();
                    else
                        jQuery('#deletecpt').show();
                    
                    var string =  jQuery(this).closest("tr").find('td:eq(0)').text() ;
                    var stringid = 0;
                    stringid = jQuery(this).closest("tr").find('input[type=checkbox]').val();
//                    var string2 = string.replace("|ICD10:","");
                    var string3 = string.split('-');
                    var deletestring =  jQuery.trim(string3[0]);
//                       alert(stringid);
                    jQuery(this).closest('tr').remove();
                    var getstring = jQuery("#cpt_values").val();
//                    alert(setstring+"=="+getstring);
                    var setstring = getstring.replace(deletestring,'');
                    if(stringid !== ''){
                        var encounterid = '<?php echo $_REQUEST['encounter']; ?>';
                        var pid = '<?php echo $_REQUEST['pid']; ?>';
                        jQuery.ajax({
                            type: 'POST',
                            url: "delete_cpt.php",
                            dataType : "json",
                            data: {
                                deletestring : deletestring,
                                encounterid : encounterid,
                                pid : pid,
                                stringid : stringid
                            },

                            success: function(data)
                            {
                                //alert("Deleted "+deletestring +" CPT from Feesheet");
                            },
                            failure: function(response)
                            {
                                alert("error");
                            }		
                        });
                    }
//                        alert(setstring);
                    jQuery("#cpt_values").val(setstring);
                }
            });
            checkRequiredPrimary();
        }
        function changesmodifier(textvalue){
             jQuery.ajax({
                type: 'POST',
                url: "update_modifier.php",
                dataType : "json",
                data: {
                    id : textvalue,
                    value: jQuery('#cpt_modifier_'+textvalue ).val()
                },

                success: function(data)
                {
                    //alert("Updated Modifier");
                },
                failure: function(response)
                {
                    alert("error");
                }		
            });
            return;
        }
        function checkmodifier(){
            jQuery("#cptdivcheck select").each(function(index, value) { 
                var checkupdate = 0;
                for(var i = 0,opts = document.getElementById('cpt_Primary').options; i < opts.length; ++i){
                    if(jQuery('#cpt_table tr > td:contains('+opts[i].value+')').length>0){
                        var cpt_prim = 1;
                        checkupdate = 1;
                    }else{
                        var cpt_prim = 0;
                    }
                    if(checkupdate == 1)
                        break;
                }    
                var patient_in_hospice = '';
                // get hospice value
                <?php
//                echo "select patient_in_hospice from patient_data where pid = '$pid'";
                    $gethospice = sqlStatement("select patient_in_hospice from patient_data where pid = '$pid'");
                    $sethospice = sqlFetchArray($gethospice);
                    if(!empty($sethospice)){
                      ?> patient_in_hospice = '<?php echo $sethospice['patient_in_hospice']; ?>'; <?php
                     }     
                ?>

                opts = 0;
                var inputstring = '';
                if(patient_in_hospice == 'YES')
                    inputstring = 'GW';
                else
                    inputstring = '25';
                // primary code gets modifier 25 if secondary visit code, vaccine, test/exams, counselling are selected
                if((jQuery(this).attr('id') == 'cpt_Secondary' || jQuery(this).attr('id') == 'cpt_TestExams' || jQuery(this).attr('id') == 'cpt_Vaccine' || jQuery(this).attr('id')== 'cpt_Counselling') && cpt_prim == 1){
                    if(jQuery("#"+jQuery(this).attr('id')+" option:selected").length > 0 ){
                        var check = 0;
                        for(var i = 0,opts = document.getElementById('cpt_Primary').options; i < opts.length; ++i){
                           if(jQuery('#cpt_table tr > td:contains('+opts[i].value+')').length>0){
                                if(jQuery('#cpt_table tr > td:contains('+opts[i].value+')').find('input:checkbox').val() == 'on'){
                                    jQuery("#cpt_modifier_"+opts[i].value).val(inputstring);
                                    check = 1;
                                }else{
                                    var idvalue = jQuery('#cpt_table tr > td:contains('+opts[i].value+')').find('input:checkbox').val();
                                    jQuery("#cpt_modifier_"+idvalue).val(inputstring);
                                    changesmodifier(idvalue);
                                    check = 1;
                                }
                                if(check == 1)
                                    break;
                            }

                        }    

                    }
                }
                // vaccine, test/exams when used without primary code, then modifier 25 is placed on the when multiple first CPT code
                if(( jQuery(this).attr('id') == 'cpt_TestExams' || jQuery(this).attr('id') == 'cpt_Vaccine'  ) && cpt_prim == 0){
                    if(jQuery("#"+jQuery(this).attr('id')+" option:selected").length > 1  ){
                        for(var i2 = 0,opts2 = document.getElementById(jQuery(this).attr('id')).options; i2 < opts2.length; ++i2){
//                           alert(jQuery('#cpt_table tr > td:contains('+opts2[i2].value+')').length);
                           if(jQuery('#cpt_table tr > td:contains('+opts2[i2].value+')').length>0){
                                if(jQuery('#cpt_table tr > td:contains('+opts2[i2].value+')').find('input:checkbox').val() === 'on'){
                                    jQuery("#cpt_modifier_"+opts2[i2].value).val(inputstring);
                                }else{
                                    var idvalue2 = jQuery('#cpt_table tr > td:contains('+opts2[i2].value+')').find('input:checkbox').val();
                                    jQuery("#cpt_modifier_"+idvalue2).val(inputstring);
                                    if(jQuery('#cpt_table tr > td:contains('+opts2[i2].value+')').length >0){
                                       changesmodifier(idvalue2);
                                       checkupdate = 1;
                                    }
                                }
                            }

                        }    

                    }
                }
            });
            jQuery('#cpt_div').find('select').each(function() {
                   if(jQuery("#"+jQuery(this).attr('id')+" option:selected").length > 0)
                        cleardropdown($(this).attr('id'));
               })
            return;
        }
	function updateProb(prob){
            var check = (prob.checked)? 1 : 0;
            jQuery.ajax({
                type: 'POST',
                url: "update_icd.php",
                data: {
                    value: prob.value,
                    ischecked: check
                },

                success: function(data)
                {
                    //alert(data);
                },
                failure: function(response)
                {
                    alert("error");
                }		
            });
            return;
        }	
        function checkRequiredPrimary(){
            // check the primary cpt selected/existed or not in the form cpts
            var arr = [];
            jQuery("#cpticd_table tr").each(function(){
                if(jQuery(this).find("td:first").text() != ''){
                    var splitString = jQuery(this).find("td:first").text().split('-');alert(splitString);
                    arr.push(jQuery.trim(splitString[0])); //put elements into array
                }
            });
            jQuery("#cpt_table tr").each(function(){
                if(jQuery(this).find("td:first").text() != ''){
                    var splitString = jQuery(this).find("td:first").text().split('-');alert(splitString);
                    arr.push(jQuery.trim(splitString[0])); //put elements into array
                }
            });
            jQuery('#cpt_Primary option').each(function() {
                var isthere = arr.indexOf(jQuery(this).val());
                if(isthere != -1){
                    jQuery("#cpt_Primary").prop('required',false);
                    jQuery("label[for='cpt_Primary']").text(jQuery("label[for='cpt_Primary']").text());
                    jQuery("label[for='cpt_Primary']").css("color", "black");
                }else{
                    jQuery("#cpt_Primary").prop('required',true);
                    jQuery("label[for='cpt_Primary']").text(jQuery("label[for='cpt_Primary']").text());
                    jQuery("label[for='cpt_Primary']").css("color", "red");
                }
            });
            return;
        }
    </SCRIPT>
    
</head>
<body class="body_top">   
    <div class="page-header">
        <h3><?php echo xlt('Codes'); ?></h3>
    </div>
    
<?php
echo "<form method='post' name='my_form' id='my_form'" .
  "action='save_codes.php?id=" . attr($formid) ."&isSingleView=$isSingleView&isFromCharts=$isFromCharts'>\n";
    $requested = $_REQUEST;
    ?>
    <button id="save_btnup" class="btn btn-default fa fa-floppy-o" onClick="return  submitFunction()"> Save</button>

    <div id='display_field' name='display_field' style="display:none; color:green;text-align: center;"> This encounter has been billed. If you need to change it, it must be re-opened. </div>
    <div id="cpt_div" class="row">
        <div class="col-sm-12">
        <h4>CPT </h4>
        <input type='hidden' name='pid' value='<?php echo $pid; ?>'>
        <input type='hidden' name='encounter' value='<?php echo $requested['encounter']; ?>'>
        <input type='hidden' name='user' value='<?php echo $requested['provider']; ?>'>
        <?php 
            $getcptlist = sqlStatement("select option_id,title from list_options where list_id  = 'Allcare_Visit_Code_Group_List' order by seq");
            $cptlist = array();
            if(!empty($getcptlist)){
                while($setcptlist = sqlFetchArray($getcptlist)){
                    $cptlist[$setcptlist['option_id']] = $setcptlist['title'];
                }
            }
//            $getcptmodifer = sqlStatement("select option_id,title from list_options where list_id  = 'CPT_Modifiers' order by seq");
//            $getcptmodiferlist = array();
//            if(!empty($getcptmodifer)){
//                while($setcptmodifer = sqlFetchArray($getcptmodifer)){
//                    $getcptmodiferlist[$setcptmodifer['option_id']] = $setcptmodifer['title'];
//                }
//            }
            $encounterid = $_REQUEST['encounter'];
            $pid         = $_REQUEST['pid'];

            $getfuv = sqlStatement("select facility_id,pc_catid from form_encounter where encounter = '$encounterid'");
            $fuvrow = sqlFetchArray($getfuv);
            if(!empty($fuvrow)){
                $facility_id    = $fuvrow['facility_id'];
                $pc_catid       = $fuvrow['pc_catid'];
            }
            /* Commented from 487 - 503 by Subhan
            $getquery = sqlStatement("SELECT fo.fs_option, vc.code_options,fo.fs_codes FROM fee_sheet_options fo
                INNER JOIN tbl_allcare_vistcat_codegrp vc ON vc.code_groups = fo.fs_category  
                WHERE `facility` = '$facility_id' AND `visit_category` = '$pc_catid' AND vc.code_options REGEXP (fo.fs_option)");
            $array = array();
            while($setquery = sqlFetchArray($getquery)){
                $codes = $setquery['fs_codes'];
                $codesarray = explode('~',str_replace("CPT4","",str_replace("|","",$codes) ));
                for($i=0; $i< count($codesarray); $i++){
                    $getcodes = sqlStatement("SELECT code_text FROM codes WHERE code = '".$codesarray[$i]."'");
                    $setcodes = sqlFetchArray($getcodes);
                    if(!empty($setcodes)){
                        $getcpts[$codesarray[$i]]= $setcodes['code_text'];
                    }
                }

            }
            */
        ?>
        <?php
        $primaryCPT = ""; // for sending comma separated primary CPTs to save_codes.php
        echo "<div id='cptdivcheck' name='cptdivcheck' role='form' class='form-horizontal'>";
            foreach($cptlist as $cptkey => $cptvalue){
                $explodedstring = array();
                $explodedstring = explode(",",$cptvalue);
                $stringarray = count($explodedstring);
                $cptkey1 = str_replace('/',"",$cptkey);
                
                echo '<div class="form-group">';
                echo "<label class='col-sm-2 control-label' for='cpt_$cptkey1'>".ucwords($cptkey).": </label>";
                echo '<div class="col-sm-10 col-xs-12 input-group btn-group">';
                $selectType = '';
                if($cptkey != 'Primary') $selectType = 'multiple'; // Primary CPTs should be single select and rest as multiple select
                echo "<select id= 'cpt_$cptkey1'  class='form-control' name = 'cpt_$cptkey1' size='4' $selectType style='display:none;'>";
                $primary_cpt_array = array();
                for($i=0; $i< $stringarray; $i++){
                    if($cptkey == 'Primary'){
                        $getcptvalues = sqlStatement("SELECT fo.fs_codes FROM fee_sheet_options fo
                            INNER JOIN tbl_allcare_vistcat_codegrp vc ON vc.code_groups = fo.fs_category  
                            WHERE `facility` = '$facility_id' AND `visit_category` = '$pc_catid' AND  vc.code_groups = '$explodedstring[$i]' AND vc.code_options REGEXP (fo.fs_option)");
                        if(!empty($getcptvalues)){
                            while($setcptvalues = sqlFetchArray($getcptvalues)){
                                $explodedcpt = array();
                                $cptval = $setcptvalues['fs_codes'];
                                if(strpos($cptval,"~") !== false){
                                    $explodedcpt = explode("~",str_replace("|","",str_replace("CPT4|","",$cptval)));
                                }else{
                                    $explodedcpt[0] = str_replace("|","",str_replace("CPT4","",$cptval));
                                }
                                for($j=0; $j< count($explodedcpt); $j++){
                                    $getcodename = sqlStatement("SELECT code_text FROM codes WHERE code = '$explodedcpt[$j]'");
                                    $setcodename = sqlFetchArray($getcodename);
                                    if(!empty($setcodename['code_text'])) $codetext = $setcodename['code_text'];
                                    else $codetext = '';
                                    echo "<option value='".$explodedcpt[$j]."'>".$explodedcpt[$j]."-".ucfirst(strtolower($codetext))."</option>";
                                    $primaryCPT .= $explodedcpt[$j].","; // form comma separated primary CPTs to send to save_codes.php
                                    $primary_cpt_array[] = $explodedcpt[$j];
                                }
                            }
                        }
                    }else{
                        $getcptvalues = sqlStatement("SELECT fs_codes FROM fee_sheet_options WHERE fs_category ='$explodedstring[$i]'");
                        if(!empty($getcptvalues)){
                            while($setcptvalues = sqlFetchArray($getcptvalues)){
                                $explodedcpt = array();
                                $cptval = $setcptvalues['fs_codes'];
                                if(strpos($cptval,"~") !== false){
                                    $explodedcpt = explode("~",str_replace("|","",str_replace("CPT4|","",$cptval)));
                                }else{
                                    $explodedcpt[0] = str_replace("|","",str_replace("CPT4","",$cptval));
                                }
                                for($j=0; $j< count($explodedcpt); $j++){
                                    $getcodename = sqlStatement("SELECT code_text FROM codes WHERE code = '$explodedcpt[$j]'");
                                    $setcodename = sqlFetchArray($getcodename);
                                    if(!empty($setcodename['code_text'])) $codetext = $setcodename['code_text'];
                                    else $codetext = '';
                                    echo "<option value='".$explodedcpt[$j]."'>".$explodedcpt[$j]."-".ucfirst(strtolower($codetext))."</option>";
                                }
                            }   
                        
                        }
                    }
                }

                echo "</select>";
//                echo "</div>";
                echo "<span id= 'cpt".$cptkey1."span' style='display:none;' class='spanfrom-control'>No related Codes Mapped</span>";
                        ?>
                <button type="button" class="btn btn-primary" style='display:none' id="cpt<?php echo $cptkey1; ?>btnclear" onClick="cleardropdown('cpt_<?php echo $cptkey1; ?>')">Clear</button></div></div>
                <script>
                    if(jQuery('#cpt_<?php echo $cptkey1; ?>').children('option').length >0){
                        jQuery('#cpt_<?php echo $cptkey1; ?>').show();
                        jQuery('#cpt<?php echo $cptkey1; ?>btnclear').show();
                        $('#cpt_<?php echo $cptkey1; ?>').multiselect({
                            includeSelectAllOption: true,
                            enableFiltering: true,
                            maxHeight: 250,
                            buttonWidth: '175px'
                        });
                    }else{
                        jQuery('#cpt<?php echo $cptkey1; ?>span').show();
                    }
		    $('.multiselect span').wrap("<div class='textcon'></div>");
					
                </script>
                <?php
               
            }
            echo '<div class="form-group">';
            echo "<label class='col-sm-2 control-label' for='cpt_ALL'>All: </label>";
            echo '<div class="col-sm-10 col-xs-12 input-group btn-group">';
            echo "<select id= 'cpt_ALL'  class='form-control' name = 'cpt_ALL' size='4' multiple>";
            $codeSet = sqlStatement("SELECT id,code,code_text FROM codes where code_type = 1");
            while($codeRow = sqlFetchArray($codeSet)):
                echo "<option value=".$codeRow['code'].">".$codeRow['code']."-".ucfirst(strtolower($codeRow['code_text']))."</option>";
            endwhile;
            echo "</select>";
            ?>
                <button type="button" class="btn btn-primary" style='display:none' id="cptALLbtnclear" onClick="cleardropdown('cpt_ALL')">Clear</button></div></div>
                <script>
                    if(jQuery('#cpt_ALL').children('option').length >0){
                        jQuery('#cpt_ALL').show();
                        jQuery('#cptALLbtnclear').show();
                        $('#cpt_ALL').multiselect({
                            includeSelectAllOption: true,
                            enableFiltering: true,
                            maxHeight: 250,
                            buttonWidth: '175px'
                        });
                    }else{
                        jQuery('#cptALLspan').show();
                    }
					$('.multiselect span').wrap("<div class='textcon'></div>");
					
                </script>
            <?php    
            echo "</div>";
        ?>
                <input type='button' value="submit" id="cptlistbutton" class="btn btn-info btn-sm">
            <?php
            $getICDsQuery = sqlStatement("SELECT title
                                FROM  `list_options` 
                                WHERE  `list_id` =  'AllCareCPTvsICD'");
            $getCPTMapped = array();
            while($rowCpt = sqlFetchArray($getICDsQuery)):
                $getCPTMapped[] = $rowCpt['title'];
            endwhile;
            $getCPTString = implode(",",$getCPTMapped);
            ?>
                <input type="hidden" name="mappedCPTs" id="mappedCPTs" value="<?php echo $getCPTString; ?>">
            <?php    
            $getCPTString = "TRIM('". str_replace(",","'),TRIM('",$getCPTString) ."')";
            $sql2 = sqlStatement("SELECT b.id,b.code, b.code_text,b.modifier,b.justify
                FROM billing b WHERE b.code IN (".$getCPTString.") AND b.encounter =   '$encounterid' and code_type='CPT4' and b.activity = 1 order by b.date asc");
            $sql3 = sqlStatement("SELECT b.id,b.code, b.code_text,b.modifier
                FROM billing b WHERE b.code NOT IN (".$getCPTString.") AND b.encounter =   '$encounterid' and code_type='CPT4' and b.activity = 1 order by b.date asc");
            ?>
        <br><br>
        <div id="cpticdparent"><h4>Predefined CPT-ICD</h4>
        <div style="border: 1px solid black;overflow: none" id="cpticddiv">
            <button type="button" class="button danger icon trash" id="deletecpticd" onClick="deletefields('cpticd')">Delete</button>
        <table id="cpticd_table" name="cpticd_table" border="0"><?php 
                echo "<th></th><th>Justify</th><th>Modifier</th>";
                $existingCPTs = "";
                while($setsql = sqlFetchArray($sql2)){
                    echo "<tr><td  style='color: blue;'><input type='checkbox' name='cpticd_delete[]' value='".$setsql['id']."' >";
                    echo "".$setsql['code']."-".ucfirst(strtolower($setsql['code_text']));//."</td>";
                    echo "</td>";
                    // Get all the predefined ICDs mapped to CPT
                    echo "<td><select id='icdjustify_".$setsql['code']."' name='icdjustify_".$setsql['code']."[]' class='form-control' size=4 multiple style='display:none;'>";
                        $array = array();
                        $getICDsQuery = sqlQuery("SELECT notes
                                FROM  `list_options` 
                                WHERE  `list_id` =  'AllCareCPTvsICD'
                                AND FIND_IN_SET(  '".$setsql['code']."', REPLACE( title, SPACE( 1 ) ,  '' ) ) >0");
                        $getICDsMapped = "TRIM('". str_replace(",","'),TRIM('",$getICDsQuery['notes']) ."')";
                        $getquery = sqlStatement("SELECT DISTINCT formatted_dx_code, long_desc FROM icd10_dx_order_code WHERE formatted_dx_code IN (".$getICDsMapped.") AND active = 1");
                        while($setquery = sqlFetchArray($getquery)){
                            $array[]= $setquery['formatted_dx_code'];
                        }
                        // Check CPT ICD Justification
                        $setjustify = substr($setsql['justify'],0,-1); // remove : from the end of justify
                        $cpticdJustify = str_replace("ICD10|","",$setjustify);
                        $cpticdJustifyArr = explode(",",$cpticdJustify);
                        
                        foreach($array as $value):
                            $selected = "";
                            if(in_array($value,$cpticdJustifyArr)) $selected = 'selected';
                            echo "<option value='".$value."' $selected>".$value."</option>";
                        endforeach;
                    echo "</select></td>";
                    echo "<td><input type='text'  id='cpticd_modifier_".trim($setsql['id'])."' placeholder='Modifier' size=20 value ='".$setsql['modifier']."' onchange=changesmodifier('".$setsql['id']."');  /></td></tr>";
                    $existingCPTs .= $setsql['code'].","; // Check which all Predefined CPTs are already saved
                }
                 $existingCPTs = substr($existingCPTs,0,-1);
                ?> 
        </table>
            <input type="hidden" id="existingCPT" name="existingCPT" value="<?php echo $existingCPTs; ?>">
            <script>
            <?php
            $sql4 = sqlStatement("SELECT b.id,b.code, b.code_text,b.modifier
                FROM billing b WHERE b.code IN (".$getCPTString.") AND b.encounter =   '$encounterid' and code_type='CPT4' and b.activity = 1 order by b.date asc");
            while($setsql = sqlFetchArray($sql4)){
                ?>
                        $('#icdjustify_<?php echo $setsql['code']; ?>').multiselect({
                            includeSelectAllOption: true,
                            enableFiltering: true,
                            maxHeight: 250,
                            buttonWidth: '175px'
                        });
                        $('.multiselect span').wrap("<div class='textcon'></div>");
                        
                <?php
            }
            ?>
            </script>    
        </div>
        </div>    
        <br><br>
        <div id="medicalprobdiv">        <h4>Medical Problems</h4>
        <div style="border: 1px solid black;overflow: none" id="cptdiv">
            <button type="button" class="button danger icon trash" id="deletecpt" onClick="deletefields('cpt')">Delete</button>
        <table id="cpt_table" name="cpt_table" border="0"  > <?php 
                echo "<th></th><th></th><th>Modifier</th>";
                while($setsql = sqlFetchArray($sql3)){
                    echo "<tr><td  style='color: blue;'><input type='checkbox' name='cpt_delete[]' value='".$setsql['id']."' >";
                    echo "".$setsql['code']."-".ucfirst(strtolower($setsql['code_text']));//."</td>";
                    echo "</td><td></td><td><input type='text'  id='cpt_modifier_".trim($setsql['id'])."' placeholder='Modifier' size=20 value ='".$setsql['modifier']."' onchange=changesmodifier('".$setsql['id']."');  /></td></tr>";
                }
                ?> 

        </table>
        </div>
        </div>
        </div><br>       
        <?php
        $providerid = 0;
        $selectquery2 = sqlStatement("SET SQL_BIG_SELECTS=1");
        $selectquery2 = sqlStatement("SELECT (select group_concat(justify) from billing WHERE encounter =   '$encounterid' and code_type='CPT4' and activity = 1) as justify, b.notecodes, b.code_text,(SELECT rendering_provider FROM form_encounter WHERE encounter = '$encounterid') AS rendering_providerid 
                        FROM billing b WHERE b.encounter =   '$encounterid' and code_type='CPT4' and b.activity = 1 order by b.date asc LIMIT 20");
        if(!empty($selectquery2)){
            while($setquery2 = sqlFetchArray($selectquery2)){
               $justify = $setquery2['justify'];
               $providerid = $setquery2['rendering_providerid'];
            }
        }
        $sql = sqlStatement("SELECT DISTINCT l.id, l.title AS Title, l.diagnosis AS Codes, if(SUBSTRING(l.diagnosis,1,4)='ICD9', (select long_desc from `icd9_dx_code` where l.diagnosis = CONCAT( 'ICD9:', formatted_dx_code ) and active = 1), (select long_desc from `icd10_dx_order_code` where l.diagnosis = CONCAT( 'ICD10:', formatted_dx_code ) and active = 1)) as Description
                                FROM lists AS l
                                INNER JOIN issue_encounter AS ie ON ie.list_id = l.id
                                AND ie.encounter ='$encounterid'

                                WHERE l.type =  'medical_problem' AND l.pid ='$pid'
                                AND ( ( l.begdate IS NULL ) OR (l.begdate IS NOT NULL  AND l.begdate <= NOW( )  ) ) AND (( l.enddate IS NULL ) OR ( l.enddate IS NOT NULL  AND l.enddate >= NOW( ) ))
                                ORDER BY ie.encounter DESC , l.id") ; 
        ?> 

        <input type="hidden" id= "noofrows" name="noofrows" value = "<?php if(mysql_num_rows($sql)== 0) echo 1; else echo mysql_num_rows($sql) ; ?>">
</div><br /><br/>
    <div class="row">
        <div class="col-md-12 form-horizontal">
            <div class="form-group">
                <label class="col-sm-1 control-label" for="icd_search">ICD:</label>
                <div class="col-sm-11">
                    <div class="input-group input-group-sm"><input type='textbox' name='icd_search' id='icd_search' aria-describedby="fromadion" class="form-control" >
                            <span class="input-group-btn"><button class="btn btn-default" id="icd_search_button" name="icd_search_button" type="button"><span class="glyphicon glyphicon-search" aria-hidden="true"></span> Search</button></span>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div id="icd_dropdowncontenar" class="col-sm-11 col-sm-push-1 input-group btn-group" style="padding-left:15px;">
                  <select id='icd_dropdown' name='icd_dropdown' class="form-control" size="4" multiple>
                  </select>
                  <button class="btn btn btn-primary" type="button" id="icdbtnclear" onClick="cleardropdown('icd')">Clear</button>
                </div>
                
            </div>
        </div>
    </div>
    <div style="border: 1px solid black;overflow: none" id='icddiv'>
        <button type="button" class="button danger icon trash" id="deleteicd" onClick="deletefields('icd')">Delete</button>
        <?php
            $getJustified = sqlStatement("SELECT justify FROM billing WHERE encounter = '$encounterid' and code_type='CPT4' and activity = 1");
            $justified = array();
            $justifiedArr = array();
            while($row = sqlFetchArray($getJustified)):
                $justified = explode(":",$row['justify']);
                foreach($justified as $k):
                    $formatICD = str_replace("|",":",$k);
                    array_push($justifiedArr,$formatICD); // Now $justifiedArr will contain all the justified ICDs
                endforeach;
                
            endwhile;
        ?>
        <table id="justify" border="0" > <?php 
                $activeProblems = array();
                while($setsql = sqlFetchArray($sql)){
                    echo "<tr style='color: blue;'><td></td><td style='text-align:center;'>";
                    echo "<input type='radio' id='icd_primary' name='icd_primary' value='".$setsql['Codes']."'";
                    if(substr( $justify, 0, strlen($setsql['Codes']) )  === str_replace(':',"|",$setsql['Codes'])) 
                            echo " checked  ";
                    echo ">Primary</input>";
                    echo "</td><td style='text-align:center;'>";
                    echo "<input type='checkbox' name='icd_justify[]' id='icd_checkboxes' value ='".$setsql['Codes']."'";
                    if(in_array($setsql['Codes'],$justifiedArr)):
                        echo "checked";
                    endif;
                    echo ">Justify</input>";
                    echo "</td><td style='text-align:center;'>";
                    echo "<input type='checkbox' name='icd_mproblem[]' id='icd_mproblem_".$setsql['Codes']."' value ='".$setsql['Codes']."$$".$setsql['id']."' checked onClick='updateProb(this)'>Active</input>" ;
                    echo "</td><td><span>|";
                    echo $setsql['Codes']." - ".ucfirst(strtolower($setsql['Title']));
                    echo "</span></td>";
                    echo "</tr>";
                    array_push($activeProblems,$setsql['Codes']);
                }
                // Now get all ICDs which are included in FeeSheet
                $icdQuery = sqlStatement("SELECT code_type,code,code_text FROM billing WHERE pid =".$pid." 
                                          AND encounter = ".$encounterid. " AND activity = 1 AND code_type <> 'CPT4'");
                while($row = sqlFetchArray($icdQuery)):
                    $icdformated = $row['code_type'].":".$row['code'];
                    if(!in_array($icdformated,$activeProblems)):
                        echo "<tr><td>";
                        echo "<input type='checkbox' name='icddelete[]' id='icddelete' value='".$row['code_type'].":".$row['code']."' style='padding:10px;'></input>";
                        echo "</td><td style='text-align:center;'>";
                        echo "<input type='radio' id='icd_primary' name='icd_primary' value='".$row['code_type'].":".$row['code']."'";
                        echo ">Primary</input>";
                        echo "</td><td style='text-align:center;'>";
                        echo "<input type='checkbox' name='icd_justify[]' id='icd_checkboxes' value ='".$row['code_type'].":".$row['code']."'";
                        if(in_array($icdformated,$justifiedArr)):
                            echo "checked";
                        endif;
                        echo ">Justify</input>";
                        echo "</td><td style='text-align:center;'>";
                        echo "<input type='checkbox' name='icd_mproblem[]' id='icd_mproblem_".$row['code']."' value ='".$row['code_type'].":".$row['code']."'>Active</input>" ;
                        echo "</td><td><span>|";
                        echo $row['code_type'].":".$row['code']." - ".ucfirst(strtolower($row['code_text']));
                        echo "</span></td>";
                        echo "</tr>";
                    endif;    
                endwhile;
                ?> 

        </table>
    </div>
    <?php
 
        $get_providerName = sqlStatement( "SELECT  CONCAT( u.fname,  ' ', u.lname ) AS rendering_ProviderName,f.rendering_provider as rendering_providerid
                FROM form_encounter f 
                inner join users u on u.id = f.rendering_provider 
                WHERE f.encounter ='$encounterid' and f.pid = '$pid'");
        while($setprovider = sqlFetchArray($get_providerName)){
            $providerid = $setprovider['rendering_providerid'];
            echo "<span id='provider'>Provider:".$setprovider['rendering_ProviderName']."</span><br>" ;
        }
//    }
    ?>
    <input type='hidden' name='providerid' value='<?php echo $providerid; ?>'>
    <input type="hidden" val='' name='cpt_values' id='cpt_values'>
    <input type="hidden" val='' name='icd_values' id='icd_values'>
    <input type="hidden" val='' name='primaryCPT' id='primaryCPT' value="<?php echo substr($primaryCPT,0,-1); ?>">
    <!--<input type='submit'  value='<?php echo xlt('Save');?>' class="button-css button fa fa-hourglass-half">&nbsp;-->
    <button id="save_btndown" class="btn btn-default fa fa-floppy-o" onClick="return  submitFunction()"> Save</button>
    
</form>
<?php
formFooter();
?>
