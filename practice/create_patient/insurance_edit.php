<?php
/**
 * Copyright (C) 2010 OpenEMR Support LLC
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * 2013/02/08 Minor tweaks by EMR Direct to allow integration with Direct messaging
 * 2013-03-27 by sunsetsystems: Fixed some weirdness with assigning a message recipient,
 *   and allowing a message to be closed with a new note appended and no recipient.
 */
require_once("../verify_session.php");

require_once("../../interface/globals.php");
require_once("$srcdir/acl.inc");
require_once("$srcdir/options.inc.php");
require_once("$srcdir/formatting.inc.php");
require_once("$srcdir/erx_javascript.inc.php");

 // Session pid must be right or bad things can happen when demographics are saved!
 //
 include_once("$srcdir/pid.inc");
 $set_pid = $_GET["set_pid"] ? $_GET["set_pid"] : $_GET["pid"];
 if ($set_pid && $set_pid != $_SESSION["pid"]) {
    setpid($set_pid);
 }

 include_once("$srcdir/patient.inc");

 $result = getPatientData($pid, "*, DATE_FORMAT(DOB,'%Y-%m-%d') as DOB_YMD");
 $result2 = getEmployerData($pid);

 // Check authorization.
 if ($pid) {
  if (!acl_check('patients', 'demo', '', 'write'))
   die(xl('Updating demographics is not authorized.'));
  if ($result['squad'] && ! acl_check('squads', $result['squad']))
   die(xl('You are not authorized to access this squad.'));
 } else {
  if (!acl_check('patients', 'demo', '', array('write','addonly') ))
   die(xl('Adding demographics is not authorized.'));
 }

$CPR = 4; // cells per row

// $statii = array('married','single','divorced','widowed','separated','domestic partner');
// $langi = getLanguages();
// $ethnoraciali = getEthnoRacials();
// $provideri = getProviderInfo();

$insurancei = getInsuranceProviders();

$fres = sqlStatement("SELECT * FROM layout_options " .
  "WHERE form_id = 'DEM' AND uor > 0 " .
  "ORDER BY group_name, seq");
?>
<html>
<head>
<?php html_header_show();?>

<link rel="stylesheet" href="<?php echo $css_header; ?>" type="text/css">

<style type="text/css">@import url(../../library/dynarch_calendar.css);</style>

<script type="text/javascript" src="../../library/dialog.js"></script>
<script type="text/javascript" src="../../library/textformat.js"></script>
<script type="text/javascript" src="../../library/dynarch_calendar.js"></script>
<?php include_once("{$GLOBALS['srcdir']}/dynarch_calendar_en.inc.php"); ?>
<script type="text/javascript" src="../../library/dynarch_calendar_setup.js"></script>

<script type="text/javascript" src="../../library/js/jquery.1.3.2.js"></script>
<script type="text/javascript" src="../../library/js/common.js"></script>

<script type="text/javascript" src="../../library/js/fancybox/jquery.fancybox-1.2.6.js"></script>
<link rel="stylesheet" type="text/css" href="../../library/js/fancybox/jquery.fancybox-1.2.6.css" media="screen" />

<script type="text/javascript">
var mypcc = '<?php echo $GLOBALS['phone_country_code'] ?>';

//code used from https://tech.irt.org/articles/js037/
function replace(string,text,by) {
    // Replaces text with by in string
    var strLength = string.length, txtLength = text.length;
    if ((strLength == 0) || (txtLength == 0)) return string;

    var i = string.indexOf(text);
    if ((!i) && (text != string.substring(0,txtLength))) return string;
    if (i == -1) return string;

    var newstr = string.substring(0,i) + by;

    if (i+txtLength < strLength)
     newstr += replace(string.substring(i+txtLength,strLength),text,by);

    return newstr;
}

function upperFirst(string,text) {
    return replace(string,text,text.charAt(0).toUpperCase() + text.substring(1,text.length));
}

<?php for ($i=1;$i<=3;$i++) { ?>
function auto_populate_employer_address<?php echo $i ?>(){
    var f = document.demographics_form;
    if (f.form_i<?php echo $i?>subscriber_relationship.options[f.form_i<?php echo $i?>subscriber_relationship.selectedIndex].value == "self")
    {
        f.i<?php echo $i?>subscriber_fname.value=f.form_fname.value;
        f.i<?php echo $i?>subscriber_mname.value=f.form_mname.value;
        f.i<?php echo $i?>subscriber_lname.value=f.form_lname.value;
        f.i<?php echo $i?>subscriber_street.value=f.form_street.value;
        f.i<?php echo $i?>subscriber_city.value=f.form_city.value;
        f.form_i<?php echo $i?>subscriber_state.value=f.form_state.value;
        f.i<?php echo $i?>subscriber_postal_code.value=f.form_postal_code.value;
        if (f.form_country_code)
            f.form_i<?php echo $i?>subscriber_country.value=f.form_country_code.value;
        f.i<?php echo $i?>subscriber_phone.value=f.form_phone_home.value;
        f.i<?php echo $i?>subscriber_DOB.value=f.form_DOB.value;
        if(typeof f.form_ss!="undefined")
        {
            f.i<?php echo $i?>subscriber_ss.value=f.form_ss.value;  
        }
        f.form_i<?php echo $i?>subscriber_sex.value = f.form_sex.value;
        f.i<?php echo $i?>subscriber_employer.value=f.form_em_name.value;
        f.i<?php echo $i?>subscriber_employer_street.value=f.form_em_street.value;
        f.i<?php echo $i?>subscriber_employer_city.value=f.form_em_city.value;
        f.form_i<?php echo $i?>subscriber_employer_state.value=f.form_em_state.value;
        f.i<?php echo $i?>subscriber_employer_postal_code.value=f.form_em_postal_code.value;
        if (f.form_em_country)
            f.form_i<?php echo $i?>subscriber_employer_country.value=f.form_em_country.value;
    }
}

<?php } ?>

function popUp(URL) {
    day = new Date();
    id = day.getTime();
   // top.restoreSession();
    eval("page" + id + " = window.open(URL, '" + id + "', 'toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=1,width=400,height=300,left = 440,top = 362');");
}

function checkNum () {
    var re= new RegExp();
    re = /^\d*\.?\d*$/;
    str=document.demographics_form.monthly_income.value;
    if(re.exec(str))
    {
    }else{
     alert("<?php xl('Please enter a monetary amount using only numbers and a decimal point.','e'); ?>");
    }
}

// Indicates which insurance slot is being updated.
var insurance_index = 0;

// The OnClick handler for searching/adding the insurance company.
function ins_search(ins) {
    insurance_index = ins;
    return false;
}

// The ins_search.php window calls this to set the selected insurance.
function set_insurance(ins_id, ins_name) {
    var thesel = document.forms[0]['i' + insurance_index + 'provider'];
    var theopts = thesel.options; // the array of Option objects
    var i = 0;
    for (; i < theopts.length; ++i) {
        if (theopts[i].value == ins_id) {
            theopts[i].selected = true;
            return;
        }
    }
    // no matching option was found so create one, append it to the
    // end of the list, and select it.
    theopts[i] = new Option(ins_name, ins_id, false, true);
}

// This capitalizes the first letter of each word in the passed input
// element.  It also strips out extraneous spaces.
function capitalizeMe(elem) {
    var a = elem.value.split(' ');
    var s = '';
    for(var i = 0; i < a.length; ++i) {
        if (a[i].length > 0) {
            if (s.length > 0) s += ' ';
            s += a[i].charAt(0).toUpperCase() + a[i].substring(1);
        }
    }
    elem.value = s;
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

// Compute the length of a string without leading and trailing spaces.
function trimlen(s) {
    var i = 0;
    var j = s.length - 1;
    for (; i <= j && s.charAt(i) == ' '; ++i);
    for (; i <= j && s.charAt(j) == ' '; --j);
    if (i > j) return 0;
    return j + 1 - i;
}

function validate(f) {
    var errCount = 0;
    var errMsgs = new Array();
   <?php generate_layout_validation('DEM'); ?>

    var msg = "";
    msg += "<?php xl('The following fields are required', 'e' ); ?>:\n\n";
    for ( var i = 0; i < errMsgs.length; i++ ) {
           msg += errMsgs[i] + "\n";
    }
    msg += "\n<?php xl('Please fill them in before continuing.', 'e'); ?>";

    if ( errMsgs.length > 0 ) {
           alert(msg);
    }

   //Patient Data validations
    <?php if($GLOBALS['erx_enable']){ ?>
    alertMsg='';
    for(i=0;i<f.length;i++){
     if(f[i].type=='text' && f[i].value)
     {
      if(f[i].name == 'form_fname' || f[i].name == 'form_mname' || f[i].name == 'form_lname')
      {
       alertMsg += checkLength(f[i].name,f[i].value,35);
       alertMsg += checkUsername(f[i].name,f[i].value);
      }
      else if(f[i].name == 'form_street' || f[i].name == 'form_city')
      {
       alertMsg += checkLength(f[i].name,f[i].value,35);
       alertMsg += checkAlphaNumericExtended(f[i].name,f[i].value);
      }
      else if(f[i].name == 'form_phone_home')
      {
       alertMsg += checkPhone(f[i].name,f[i].value);
      }
     }
    }
    if(alertMsg)
    {
      alert(alertMsg);
      return false;
    }
    <?php } ?>
    //return false;

   // Some insurance validation.
    for (var i = 1; i <= 3; ++i) {
     subprov = 'i' + i + 'provider';
     if (!f[subprov] || f[subprov].selectedIndex <= 0) continue;
     var subpfx = 'i' + i + 'subscriber_';
     var subrelat = f['form_' + subpfx + 'relationship'];
     var samename =
      f[subpfx + 'fname'].value == f.form_fname.value &&
      f[subpfx + 'mname'].value == f.form_mname.value &&
      f[subpfx + 'lname'].value == f.form_lname.value;
     var ss_regexp=/[0-9][0-9][0-9]-?[0-9][0-9]-?[0-9][0-9][0-9][0-9]/;
     var samess=true;
     var ss_valid=false;
     if(typeof f.form_ss!="undefined")
         {
           samess = f[subpfx + 'ss'].value == f.form_ss.value;
           ss_valid=ss_regexp.test(f[subpfx + 'ss'].value) && ss_regexp.test(f.form_ss.value);  
         }
     if (subrelat.options[subrelat.selectedIndex].value == "self") {
      if (!samename) {
       if (!confirm("<?php echo xls('Subscriber relationship is self but name is different! Is this really OK?'); ?>"))
        return false;
      }
      if (!samess && ss_valid) {
       if(!confirm("<?php echo xls('Subscriber relationship is self but SS number is different!')." ". xls("Is this really OK?"); ?>"))
       return false;
      }
     } // end self
     else {
      if (samename) {
       if (!confirm("<?php echo xls('Subscriber relationship is not self but name is the same! Is this really OK?'); ?>"))
        return false;
      }
      if (samess && ss_valid)  {
       if(!confirm("<?php echo xls('Subscriber relationship is not self but SS number is the same!') ." ". xls("Is this really OK?"); ?>"))
       return false;
      }
     } // end not self
    } // end for

    return errMsgs.length < 1;
}

function submitme() {
 var f = document.forms[0];
 if (validate(f)) {
     
 // top.restoreSession();
  f.submit();
  
 }
}

// Onkeyup handler for policy number.  Allows only A-Z and 0-9.
function policykeyup(e) {
 var v = e.value.toUpperCase();
 for (var i = 0; i < v.length; ++i) {
  var c = v.charAt(i);
  if (c >= '0' && c <= '9') continue;
  if (c >= 'A' && c <= 'Z') continue;
  if (c == '*') continue;
  if (c == '-') continue;
  if (c == '_') continue;
  if (c == '(') continue;
  if (c == ')') continue;
  if (c == '#') continue;
  v = v.substring(0, i) + v.substring(i + i);
  --i;
 }
 e.value = v;
 return;
}
function search_ajax_way(index){
    var search_this=$('#i'+index+'plan_name').val();
    $.post("../../practice/get_payerplan_plans_search.php", {search_plan: search_this,ins_name:$('#i'+index+'provider').val()}, function(data){
    //alert(data);
    $("#plan_name"+index).html(data);
 
    })
} 
function payer(num){
    var name=document.getElementById('i'+num+'provider').value;
    document.getElementById('i'+num+'plan_name').value='';
    $("#i"+num+"plan_name").keyup(function(event){
        event.preventDefault();
        search_ajax_way(num);
    });
}

// Added 06/2009 by BM to make compatible with list_options table and functions - using jquery
$(document).ready(function() {

 <?php for ($i=1;$i<=3;$i++) { ?>
  $("#form_i<?php echo $i?>subscriber_relationship").change(function() { auto_populate_employer_address<?php echo $i?>(); });
 <?php } ?>
     
     

});

$(document).ready(function(){
                  
    tabbify();
    enable_modals();

    // special size for
	$(".medium_modal").fancybox({
		'overlayOpacity' : 0.0,
		'showCloseButton' : true,
		'frameHeight' : 460,
		'frameWidth' : 650
	});
   
});
</script>
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.0/jquery-ui.js"></script>
<script src="../../library/popover/js/jquery.webui-popover.js"></script>
<script>
  $( function() {
          <?php for($i = 1; $i <= 3; $i++){ ?>
                    $( "#i<?php echo $i; ?>providerid" ).autocomplete({
                        minLength: 0,
                        source: function(request, response) {
                            if(request.term !== ''){
                                $.post("search_payerplan.php", {searchit :$( "#i<?php echo $i; ?>providerid" ).val()}, function(data){
                                    if(data){
                                        var projects = JSON.parse(data);
                                        response(projects.returndata); 
                                        $( "#i<?php echo $i; ?>plan_name"  ).removeAttr("disabled"); 
                                    }
                                });
                            }
                            if(request.term == '')
                                    $( "#i<?php echo $i; ?>plan_name"  ).attr("disabled", "disabled"); 
                        },
                        focus: function( event, ui ) {
                          $( "#i<?php echo $i; ?>providerid"  ).val( ui.item.label );
                          return false;
                        },
                        select: function( event, ui ) {
                          $( "#i<?php echo $i; ?>providerid"  ).val( ui.item.label );
                          $( "#i<?php echo $i; ?>provider"  ).val( ui.item.value );
                          $( "#i<?php echo $i; ?>provider_payerplan"  ).val( ui.item.payerID );
                          $( "#i<?php echo $i; ?>provider-id" ).val( ui.item.zirmed_payer_id );
                          return false;
                        }
                      })
                      .autocomplete( "instance" )._renderItem = function( ul, item ) {
                        return $( "<li>" )
                          .append(  "<div>" + item.label + "<i><span style='font-size:8pt;'>" + item.desc + "</span></i></div>"  )
                          .appendTo( ul );
                    };
                    // plan auto complete
                    $.widget( "custom.plancombobox", {
                        _create: function() {
                          this.wrapper = $( "<span>" )
                            .addClass( "custom-combobox" )
                            .insertAfter( this.element );

                          this.element.hide();
                          this._createAutocomplete();
                          this._createShowAllButton();
                        },

                        _createAutocomplete: function() {
                          var selected = this.element.children( ":selected" ),
                            value = selected.val() ? selected.text() : "";
                          this.input = $( "<input>" )
                            .appendTo( this.wrapper )
                            .val( value )
                            .attr( "title", "" )
                            .addClass( "custom-combobox-input ui-widget ui-widget-content ui-state-default ui-corner-left" )
                            .autocomplete({
                              delay: 0,
                              minLength: 0,
                              source: $.proxy( this, "_source" )
                            })
                            .tooltip({
                              classes: {
                                "ui-tooltip": "ui-state-highlight"
                              }
                            });
                          this._on( this.input, {
                            autocompleteselect: function( event, ui ) {
                             //console.log(JSON.stringify(ui.item));
                             ui.item.option.selected = true;
                             $('#i<?php echo $i; ?>plan_name-id').val(ui.item.option.value);
                              this._trigger( "select", event, {
                                item: ui.item.option
                              });
                            },

                           
                          });
                        },

                        _createShowAllButton: function() {
                          var input = this.input,
                            wasOpen = false;

                          $( "<a>" )
                            .attr( "tabIndex", -1 )
                            .attr( "title", "Show All Items" )
                            .tooltip()
                            .appendTo( this.wrapper )
                            .button({
                              icons: {
                                primary: "ui-icon-triangle-1-s"
                              },
                              text: false
                            })
                            .removeClass( "ui-corner-all" )
                            .addClass( "custom-combobox-toggle ui-corner-right" )
                            .on( "mousedown", function() {
                              wasOpen = input.autocomplete( "widget" ).is( ":visible" );
                            })
                            .on( "click", function() {
                              input.trigger( "focus" );

                              // Close if already visible
                              if ( wasOpen ) {
                                return;
                              }

                              // Pass empty string as value to search for, displaying all results
                              input.autocomplete( "search", "" );
                            });
                        },
                        select: function (event, ui) {
                            /*ui.item.option.selected = true;
                            self._trigger("selected", event, {
                                item: ui.item.option
                            });
                            select.trigger("change");*/
                        },

                        _source: function( request, response ) {
                            var element = this.element;
                          $.post("get_payerplan_plans_search.php", {searchit :request.term,post_parent:$( "#i<?php echo $i; ?>provider_payerplan" ).val()}, function(data){
                                if(data){
                                    var projects2 = JSON.parse(data);
                                    console.log(projects2.returndata);
                                    var elementoptions = "";
                                    $.each(projects2.returndata,function(i,v){
                                       elementoptions += '<option value="'+v.value+'">'+v.label+'</option>';
                                    })
                                     element.html(elementoptions);
                                    response(element.children("option").map(function () {
                                        var text = $(this).text();
//                                        alert(text);
                                        return {
                                            label: text,
                                            value: text,
                                            option: this
                                        };
                                    }));
            
                                }
                            });       
                        }
                      });
                       $( "#i<?php echo $i; ?>plan_name" ).plancombobox({ 
                            select: function (event, ui) { 
                                    //alert($(this).attr('id'));
                              $(this).next('.custom-combobox').find("input").attr("value",ui.item.label);
                              $("#i<?php echo $i?>plan_name_label").attr("value",ui.item.label);
                              
                              
                            }
                        });  

        <?php } ?>
         $('a.card').webuiPopover('destroy').webuiPopover({trigger:'click',title:'Insurance Card',padding:0,animation:'pop',closeable:true,placement:'right-bottom'});
         $('.card').click(function(){
            $(this).next().hide();
        })      
        $('a.uploadfront').webuiPopover('destroy').webuiPopover({trigger:'click',title:'Upload Insurance Card',padding:0,animation:'pop',closeable:true,placement:'right-bottom'});
         $('.uploadfront').click(function(){
            $(this).next().hide();
        }) 
        <?php for($i=1; $i<4; $i++){ ?>
            $('a.plan_benefits<?php echo $i; ?>').webuiPopover('destroy').webuiPopover({type:'iframe',url:'../../main/allcarereports/get_plan_benefits.php?plan_id=0',title:'Plan Benefits',padding:0,animation:'pop',closeable:true,width:600, height:300});       
            $('a.plan_benefits<?php echo $i; ?>').click(function(event){
                var plan_id_value = $("#i<?php echo $i; ?>plan_name-id").val();
                var url = '../main/allcarereports/get_plan_benefits.php?plan_id='; 
                var res = url.concat(plan_id_value).concat("&payer_id ="+$("#i<?php echo $i?>provider").val());
                
//                console.log(plan_id_value);
                $('a.plan_benefits<?php echo $i; ?>').webuiPopover('destroy').webuiPopover({type:'iframe',url:res,title:'Plan Benefits',padding:0,animation:'pop',closeable:true,width:600, height:300});
                $('a.plan_benefits<?php echo $i; ?>').webuiPopover('show'); 
            })
        <?php } ?>
        $('#demo_save_button').click(function() {
            window.parent.closeModalWindow();
        });
        $("#backimage").on('submit',(function(e){
            e.preventDefault();
            $.ajax({
                url: "../main/allcarereports/saveimage.php",
                type: "POST",
                data:  new FormData(this),
                contentType: false,
                cache: false,
                processData:false,
                success: function(data){
                $("#backimagediv").html(data);
                },
                error: function(){} 	        
            });
        }));
        $("#frontimage").on('submit',(function(e){
            e.preventDefault();
            $.ajax({
                url: "../main/allcarereports/saveimage.php",
                type: "POST",
                data:  new FormData(this),
                contentType: false,
                cache: false,
                processData:false,
                success: function(data){
                $("#frontimagediv").html(data);
                },
                error: function(){} 	        
            });
        }))
        
  } );
    

</script>
<style>
  .body{
      height: 100%;
  }
.project-description {
  margin: 0;
  padding: 0;
  font-size:10px
}
</style>
<style>
.custom-combobox {
  position: relative;
  display: inline-block;
  height: 20px; 
}
.custom-combobox-toggle {
  position: absolute;
  top: 0;
  bottom: 0;
  margin-left: -1px;
  padding: 0;
  height: 20px; 

}
.custom-combobox-input {
  margin: 0;
  padding: 5px 10px;
  height: 20px; 
  width: 190px;
}
.zirmedsmallsize{
    font-size: 10px;
}
#showeditdemographics.modal-body{
    padding: 0px !important;
}

</style>
</head>

<body class="body_top">
<form action='demographics_save.php' name='demographics_form' method='post' onsubmit='return validate(this)'>
<input type='hidden' name='mode' value='save' />
<input type='hidden' name='db_id' value="<?php echo $result['id']?>" />

<?php

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

$last_group = '';
$cell_count = 0;
$item_count = 0;
$display_style = 'block';

$group_seq=0; // this gives the DIV blocks unique IDs

?>
<div class="tabContainer"style="height: 300px;background:#FFF; overflow-y: scroll; width: 80%; display:none;">
        <?php display_layout_tabs_data_editable('DEM', $result, $result2); ?>
</div>
<div class="section-header">
    <span class="text"><b><?php xl("Insurance", "e" )?></b></span>
</div>
<div id="DEM" >


<?php
if (! $GLOBALS['simplified_demographics']) {

	  $insurance_headings = array(xl("Primary Insurance Provider"), xl("Secondary Insurance Provider"), xl("Tertiary Insurance provider"));
	  $insurance_info = array();
	  $insurance_info[1] = getInsuranceData($pid,"primary");
	  $insurance_info[2] = getInsuranceData($pid,"secondary");
	  $insurance_info[3] = getInsuranceData($pid,"tertiary");
          function insurance_custom ($pid,$type,$id,$cols = "*")
            {
              
              return sqlQuery("select $cols from insurance_data where pid=? " .
                " AND type=? AND id=? order by id DESC limit 0,1", array($pid,$type,$id) );
            }
            $layout[1] = insurance_custom($pid,'primary',$insurance_info[1]['id']);
            $layout[2] = insurance_custom($pid,'secondary',$insurance_info[2]['id']);
            $layout[3] = insurance_custom($pid,'tertiary',$insurance_info[3]['id']);
           
            echo "<input type='hidden' id='pri_ins_id' name='pri_ins_id' value='".$insurance_info[1]['id']."' />";
            echo "<input type='hidden' id='sec_ins_id' name='sec_ins_id' value='".$insurance_info[2]['id']."' />";
            echo "<input type='hidden' id='ter_ins_id' name='ter_ins_id' value='".$insurance_info[3]['id']."' />"; 
            echo "<div style='text-align:right;'>";
            echo "<a data-href='patient_file/summary/patient_insurance_history.php?pid=$pid' data-title='Patient Information' data-frameheight='450' data-modalsize='modal-lg' data-bodypadding='0' class='appname' data-toggle='modal' data-target='#modalwindow' style='cursor: pointer;'>Insurance History</a>";
            echo "</div>";
	?>
	<div id="INSURANCE" >
		<ul class="tabNav">
		<?php
		foreach (array('primary','secondary','tertiary') as $instype) {
			?><li <?php echo $instype == 'primary' ? 'class="current"' : '' ?>><a href="/play/javascript-tabbed-navigation/"><?php $CapInstype=ucfirst($instype); xl($CapInstype,'e'); ?></a></li><?php
		}
		?>
		</ul>

	<div class="tabContainer">

	<?php
	  for($i=1;$i<=3;$i++) {
	   $result3 = $insurance_info[$i];
           
	?>

		<div class="tab <?php echo $i == 1 ? 'current': '' ?>" style='height:auto;width:auto'>		<!---display icky, fix to auto-->
                <table border="0">

		 <tr>
		  <td valign=top width="430">
		   <table border="0">

			 <tr>
			  <td valign='top'>
			   <span class='required'><?php echo $insurance_headings[$i -1]."&nbsp;"?></span> 
			  </td>
			  <td class='required'>:</td>
			  <td>
                              <?php 
                                $payer_name = '';
                                foreach ($insurancei as $iid => $iname) {
                                       if (strtolower($iid) == strtolower($result3{"provider"}))
                                         $payer_name = $iname ;
                                }
                                
                                 if($i == 1)
                                    $new_ins_type = 'primary';
                                 else if($i == 2)
                                    $new_ins_type = 'secondary';
                                 else if($i == 3)
                                    $new_ins_type = 'teritary';




                                 $get_image = sqlStatement("SELECT frontimage, backimage,plan_id,payer_id,post_parent FROM tbl_patient_insurancedata_meta_data WHERE pid='$pid'  and `type` = '$new_ins_type'");
                                 $set_image = sqlFetchArray($get_image);

                                
                              ?>
                            <input name="i<?php echo $i?>providerid" id="i<?php echo $i?>providerid" value='<?php echo $payer_name ; ?>'>
                            <input type="hidden" name="i<?php echo $i?>provider" id="i<?php echo $i?>provider" value='<?php echo $result3{"provider"} ; ?>'>
                            <input type="hidden" id="i<?php echo $i?>provider-id"  name="i<?php echo $i?>provider-id" value='<?php echo $set_image['payer_id']; ?>'>
                            <input type='hidden' name='i<?php echo $i?>provider_payerplan' id='i<?php echo $i?>provider_payerplan' value='<?php echo $set_image['post_parent']; ?>'>
                            <p id="project-description" class='project-description' ></p>
                            <input type='hidden' name="i<?php echo $i?>provider_change" id="i<?php echo $i?>provider_change" value='<?php echo $payer_name; ?>'>
                          </td>
                         </tr>
                         <tr>
                             <td class=leftborder><span class=bold></span></td>

                             <td colspan="2">
                             <?php 
                             
//                             echo $set_image['frontimage'];
//                             echo "<pre>"; print_r($set_image); echo "</pre>";
                             echo '<a class="card" href="#" style="font-size:12px;">Card Front Image</a>
                                 <div class="webui-popover-content" id="frontimagediv">';
                              if($set_image['frontimage'] == '')
                                 echo '<image src="../images/No_Image_Available.png" alt="No image icon"  width="290" height="200"> ';
                              else   
                                 echo ' <img src="data:image/jpeg;base64,' . $set_image['frontimage'] . '" width="290" height="200">';
                              
                               echo '  </div>';
//                               echo '| <a class="uploadfront" href="#" style="font-size:12px; ">Upload Card Front Image</a>
//                                 <div class="webui-popover-content">';
//                               echo '<form action="saveimage.php" method="post" id="frontimage" enctype="multipart/form-data">
//                                    <p style="font-size:12px;">Select image to upload:
//                                    <input type="file" name="fileToUpload" id="fileToUpload">
//                                    <input type="submit" value="Upload Image" name="submit">
//                                    </p>
//                                </form>
//                                ';
//                               echo '  </div>';
                                    echo '<br/>';
                             echo '<a class="card" href="#" style="font-size:12px;">Card Back Image</a>
                                 <div class="webui-popover-content" id="backimagediv">';
                             if($set_image['backimage'] == '')
                                 echo '<image src="../images/No_Image_Available.png" alt="No image icon"  width="290" height="200"> ';
                             else
                                 echo '<img src="data:image/jpeg;base64,' . $set_image['backimage'] . '" width="290" height="200">';
                            echo '</div>';
//                            echo '| <a class="uploadfront" href="#" style="font-size:12px;">Upload Card Back Image</a>
//                                 <div class="webui-popover-content">';
//                               echo '<form action="saveimage.php" method="post" id="backimage" enctype="multipart/form-data">
//                                    <p style="font-size:12px;">Select image to upload:
//                                    <input type="file" name="fileToUpload" id="fileToUpload">
//                                    <input type="submit" value="Upload Image" name="submit">
//                                    </p>
//                                </form>
//                                ';
//                            echo '  </div>';
                                ?>
			  </td>
			 </tr>

			<tr>
			 <td>
                          <span class='required'><?php xl('Plan Name','e'); ?> </span>
			 </td>
			 <td class='required'>:</td>
<!--			 <td>
                         <select name='i<?php echo $i?>plan_name' id='i<?php echo $i?>plan_name'  class="custom-combobox" value="<?php echo $set_image['plan_id'];?>">
                            <option value="">Select one...</option>
                            <?php if($set_image['plan_id'] != ''){ ?>
                                <option value="<?php echo $set_image['plan_id'];?>" selected><?php echo $result3{"plan_name"} ; ?> </option>
                            <?php } ?>    
                          </select>
                             <input type='hidden' id='i<?php echo $i?>plan_name_label' name='i<?php echo $i?>plan_name_label' value="<?php echo $result3{"plan_name"} ; ?>" >
                            <input type="hidden" id="i<?php echo $i?>plan_name-id" name="i<?php echo $i?>plan_name-id" value='<?php echo $set_image['plan_id']; ?>'>
                            <input type='hidden' name="i<?php echo $i?>plan_name_change" id="i<?php echo $i?>plan_name_change" value='<?php echo $result3{"plan_name"}; ?>'>
                         </td>-->
                         <td>
                            <select name='i<?php echo $i?>plan_name' id='i<?php echo $i?>plan_name'  class="custom-combobox" value="<?php echo $set_image['plan_id'];?>">
                            <option value="">Select one...</option>
                            <?php if($set_image['plan_id'] != ''){ ?>
                                <option value="<?php echo $set_image['plan_id'];?>" selected><?php echo $result3{"plan_name"} ; ?> </option>
                            <?php } ?>    
                         </select>
                          <input type='hidden' id='i<?php echo $i?>plan_name_label' name='i<?php echo $i?>plan_name_label' value="<?php echo $result3{"plan_name"} ; ?>" >
                            <input type="hidden" id="i<?php echo $i?>plan_name-id" name="i<?php echo $i?>plan_name-id" value='<?php echo $set_image['plan_id']; ?>'>
                            <input type='hidden' name="i<?php echo $i?>plan_name_change" id="i<?php echo $i?>plan_name_change" value='<?php echo $result3{"plan_name"}; ?>'>
                         </td>
                        </tr>
                        <tr>
                            <td class=leftborder><span class=bold></span></td>
                            <td></td>
                            <td>
                                <a class="plan_benefits<?php echo $i?>" href="#" style="font-size: 12px;" >Plan Benefits</a>
                                <div class="webui-popover-content">
                                   
                                </div>
                            </td> 
                         
                        </tr>

			<tr>
			 <td>
			  <span class='required'><?php xl('Effective Date','e'); ?></span>
			 </td>
			 <td class='required'>:</td>
			 <td>
			  <input type='entry' size='16' id='i<?php echo $i ?>effective_date' name='i<?php echo $i ?>effective_date'
			   value='<?php echo $result3['date'] ?>'
			   onkeyup='datekeyup(this,mypcc)' onblur='dateblur(this,mypcc)'
			   title='yyyy-mm-dd' />
                          <img src='../pic/show_calendar.gif' align='absbottom' width='24' height='22' id='img_i<?php echo $i ?>effective_date' border='0' alt='[?]' style='cursor:pointer' title='<?php xl('Click here to choose a date','e'); ?>'>
			 </td>
			</tr>

			<tr>
			 <td><span class=required><?php xl('Policy Number','e'); ?></span></td>
			 <td class='required'>:</td>
			 <td><input type='entry' size='16' name='i<?php echo $i?>policy_number' value="<?php echo $result3{"policy_number"}?>"
			  onkeyup='policykeyup(this)'></td>
			</tr>

			<tr>
			 <td><span class=required><?php xl('Group Number','e'); ?></span></td>
			 <td class='required'>:</td>
			 <td><input type=entry size=16 name=i<?php echo $i?>group_number value="<?php echo $result3{"group_number"}?>" onkeyup='policykeyup(this)'></td>
			</tr>

			<tr<?php if ($GLOBALS['omit_employers']) echo " style='display:none'"; ?>>
			 <td class='required'><?php xl('Subscriber Employer (SE)','e'); ?><br><span style='font-weight:normal'>
			  (<?php xl('if unemployed enter Student','e'); ?>,<br><?php xl('PT Student, or leave blank','e'); ?>) </span></td>
			  <td class='required'>:</td>
			 <td><input type=entry size=25 name=i<?php echo $i?>subscriber_employer
			  value="<?php echo $result3{"subscriber_employer"}?>"
			   onchange="capitalizeMe(this);" /></td>
			</tr>

			<tr<?php if ($GLOBALS['omit_employers']) echo " style='display:none'"; ?>>
			 <td><span class=required><?php xl('SE Address','e'); ?></span></td>
			 <td class='required'>:</td>
			 <td><input type=entry size=25 name=i<?php echo $i?>subscriber_employer_street
			  value="<?php echo $result3{"subscriber_employer_street"}?>"
			   onchange="capitalizeMe(this);" /></td>
			</tr>

			<tr<?php if ($GLOBALS['omit_employers']) echo " style='display:none'"; ?>>
			 <td colspan="3">
			  <table>
			   <tr>
				<td><span class=required><?php xl('SE City','e'); ?>: </span></td>
				<td><input type=entry size=15 name=i<?php echo $i?>subscriber_employer_city
				 value="<?php echo $result3{"subscriber_employer_city"}?>"
				  onchange="capitalizeMe(this);" /></td>
				<td><span class=required><?php echo ($GLOBALS['phone_country_code'] == '1') ? xl('SE State','e') : xl('SE Locality','e') ?>: </span></td>
                                <td>
                                     <?php
                                      // Modified 7/2009 by BM to incorporate data types
                                        generate_form_field(array('data_type'=>$GLOBALS['state_data_type'],'field_id'=>('i'.$i.'subscriber_employer_state'),'list_id'=>$GLOBALS['state_list'],'fld_length'=>'15','max_length'=>'63','edit_options'=>'C'), $result3['subscriber_employer_state']);
                                     ?>
                                </td>
			   </tr>
			   <tr>
				<td><span class=required><?php echo ($GLOBALS['phone_country_code'] == '1') ? xl('SE Zip Code','e') : xl('SE Postal Code','e') ?>: </span></td>
				<td><input type=entry size=15 name=i<?php echo $i?>subscriber_employer_postal_code value="<?php echo $result3{"subscriber_employer_postal_code"}?>"></td>
				<td><span class=required><?php xl('SE Country','e'); ?>: </span></td>
			<td>
				 <?php
				  // Modified 7/2009 by BM to incorporate data types
			  generate_form_field(array('data_type'=>$GLOBALS['country_data_type'],'field_id'=>('i'.$i.'subscriber_employer_country'),'list_id'=>$GLOBALS['country_list'],'fld_length'=>'10','max_length'=>'63','edit_options'=>'C'), $result3['subscriber_employer_country']);
				 ?>
			</td>
			   </tr>
			  </table>
			 </td>
			</tr>

		   </table>
		  </td>

		  <td valign=top>
		<table border="0">
			<tr>
				<td><span class=required><?php xl('Relationship','e'); ?></span></td>
				<td class=required>:</td>
				<td colspan=3><?php
					// Modified 6/2009 by BM to use list_options and function
					generate_form_field(array('data_type'=>1,'field_id'=>('i'.$i.'subscriber_relationship'),'list_id'=>'sub_relation','empty_title'=>' '), $result3['subscriber_relationship']);
					?>

				<a href="javascript:popUp('../browse.php?browsenum=<?php echo $i?>')" class=text>(<?php xl('Browse','e'); ?>)</a></td>
				<td></td><td></td><td></td><td></td>
			</tr>
                        <tr>
				<td width=120><span class=required><?php xl('Subscriber','e'); ?> </span></td>
				<td class=required>:</td>
				<td colspan=3><input type=entry size=10 name=i<?php echo $i?>subscriber_fname	value="<?php echo $result3{"subscriber_fname"}?>" onchange="capitalizeMe(this);" />
				<input type=entry size=3 name=i<?php echo $i?>subscriber_mname value="<?php echo $result3{"subscriber_mname"}?>" onchange="capitalizeMe(this);" />
				<input type=entry size=10 name=i<?php echo $i?>subscriber_lname value="<?php echo $result3{"subscriber_lname"}?>" onchange="capitalizeMe(this);" /></td>
				<td></td><td></td><td></td><td></td>
			</tr>
			<tr>
				<td><span class=bold><?php xl('D.O.B.','e'); ?> </span></td>
				<td class=required>:</td>
				<td><input type='entry' size='11' id='i<?php echo $i?>subscriber_DOB' name='i<?php echo $i?>subscriber_DOB' value='<?php echo $result3['subscriber_DOB'] ?>' onkeyup='datekeyup(this,mypcc)' onblur='dateblur(this,mypcc)' title='yyyy-mm-dd' /><img src='../pic/show_calendar.gif' align='absbottom' width='24' height='22' id='img_i<?php echo $i; ?>dob_date' border='0' alt='[?]' style='cursor:pointer' title='<?php xl('Click here to choose a date','e'); ?>'></td>

				<td><span class=bold><?php xl('Sex','e'); ?>: </span></td>
				<td><?php
					// Modified 6/2009 by BM to use list_options and function
					generate_form_field(array('data_type'=>1,'field_id'=>('i'.$i.'subscriber_sex'),'list_id'=>'sex'), $result3['subscriber_sex']);
					?>
				</td>
				<td></td><td></td> <td></td><td></td>
			</tr>
			<tr>
				<td class=leftborder><span class=bold><?php xl('S.S.','e'); ?> </span></td>
				<td class=required>:</td>
				<td><input type=entry size=11 name=i<?php echo $i?>subscriber_ss value="<?php echo trim($result3{"subscriber_ss"})?>"></td>
			</tr>

			<tr>
				<td><span class=required><?php xl('Subscriber Address','e'); ?> </span></td>
				<td class=required>:</td>
				<td><input type=entry size=20 name=i<?php echo $i?>subscriber_street value="<?php echo $result3{"subscriber_street"}?>" onchange="capitalizeMe(this);" /></td>

				<td><span class=required><?php echo ($GLOBALS['phone_country_code'] == '1') ? xl('State','e') : xl('Locality','e') ?>: </span></td>
				<td>
					<?php
					// Modified 7/2009 by BM to incorporate data types
					generate_form_field(array('data_type'=>$GLOBALS['state_data_type'],'field_id'=>('i'.$i.'subscriber_state'),'list_id'=>$GLOBALS['state_list'],'fld_length'=>'15','max_length'=>'63','edit_options'=>'C'), $result3['subscriber_state']);
				?>
				</td>
			</tr>
			<tr>
				<td class=leftborder><span class=required><?php xl('City','e'); ?></span></td>
				<td class=required>:</td>
				<td><input type=entry size=11 name=i<?php echo $i?>subscriber_city value="<?php echo $result3{"subscriber_city"}?>" onchange="capitalizeMe(this);" /></td><td class=leftborder><span class='required'<?php if ($GLOBALS['omit_employers']) echo " style='display:none'"; ?>><?php xl('Country','e'); ?>: </span></td><td>
					<?php
					// Modified 7/2009 by BM to incorporate data types
					generate_form_field(array('data_type'=>$GLOBALS['country_data_type'],'field_id'=>('i'.$i.'subscriber_country'),'list_id'=>$GLOBALS['country_list'],'fld_length'=>'10','max_length'=>'63','edit_options'=>'C'), $result3['subscriber_country']);
					?>
				</td>
</tr>
			<tr>
				<td><span class=required><?php echo ($GLOBALS['phone_country_code'] == '1') ? xl('Zip Code','e') : xl('Postal Code','e') ?> </span></td><td class=required>:</td><td><input type=entry size=10 name=i<?php echo $i?>subscriber_postal_code value="<?php echo $result3{"subscriber_postal_code"}?>"></td>

				<td colspan=2>
				</td><td></td>
			</tr>
			<tr>
				<td><span class=bold><?php xl('Subscriber Phone','e'); ?></span></td>
				<td class=required>:</td>
				<td><input type='text' size='20' name='i<?php echo $i?>subscriber_phone' value='<?php echo $result3["subscriber_phone"] ?>' onkeyup='phonekeyup(this,mypcc)' /></td>
				<td colspan=2><span class=bold><?php xl('CoPay','e'); ?>: <input type=text size="6" name=i<?php echo $i?>copay value="<?php echo $result3{"copay"}?>"></span></td>
				<td colspan=2>
				</td><td></td><td></td>
			</tr>
			<tr>
				<td colspan=0><span class='required'><?php xl('Accept Assignment','e'); ?></span></td>
				<td class=required>:</td>
				<td colspan=2>
					<select name=i<?php echo $i?>accept_assignment>
						<option value="TRUE" <?php if (strtoupper($result3{"accept_assignment"}) == "TRUE") echo "selected"?>><?php xl('YES','e'); ?></option>
						<option value="FALSE" <?php if (strtoupper($result3{"accept_assignment"}) == "FALSE") echo "selected"?>><?php xl('NO','e'); ?></option>
					</select>
				</td>
				<td></td><td></td>
				<td colspan=2>
				</td><td></td>
			</tr>
      <tr>
        <td><span class='bold'><?php xl('Secondary Medicare Type','e'); ?></span></td>
        <td class='bold'>:</td>
        <td colspan='6'>
          <select name=i<?php echo $i?>policy_type>
<?php
  foreach ($policy_types AS $key => $value) {
    echo "            <option value ='$key'";
    if ($key == $result3['policy_type']) echo " selected";
    echo ">" . htmlspecialchars($value) . "</option>\n";
  }
?>
          </select>
        </td>
      </tr>
       <tr> 
    <?php 
        $get_attribute = sqlStatement("SELECT edit_revised_elig_da FROM tbl_user_custom_attr_1to1 " .
          "WHERE edit_revised_elig_da='YES' AND userid = '".$_SESSION['authId'] . "' ");
        $set_attribute = sqlFetchArray($get_attribute) ;
//        echo $set_attribute['edit_revised_elig_da'] ."hema";
        $trow= $layout[$i] ;
        
        $fres = sqlStatement("SELECT * FROM layout_options " .
          "WHERE form_id = 'INSCUTOM' AND uor > 0 " .
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
          
          
          //for custom field 
          $frow['field_id']='i'.$i.$field_id;
         $currvalue  = '';
          if (isset($trow[$field_id])) $currvalue = $trow[$field_id];
          // Handle a data category (group) change.

        // Handle a data category (group) change.
          if (strcmp($this_group, $last_group) != 0) {
            end_group();
           $group_seq  = substr($this_group, 0, 1);
           $group_name = substr($this_group, 1);
           $last_group = $this_group;
           $group_seq_esc = htmlspecialchars( $group_seq, ENT_QUOTES);
                if($group_seq==1):	//echo "<div class='tab current' id='div_$group_seq_esc'>";
                else:			echo "<div class='tab' id='div_$group_seq_esc'>";
                endif;
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
            echo "<td valign='top' colspan='$datacols_esc' class='text' id='".$frow['field_id'] ."'";
            if ($cell_count > 0) echo " style='padding-left:5pt'";
            echo ">";
            $cell_count += $datacols;
          }
          
          
          ++$item_count;
          
          generate_form_field($frow, $currvalue);
          
            if(strpos($frow['field_id'],'revised_elig_date') != false ){
                if($set_attribute['edit_revised_elig_da'] != 'YES'){
                    ?>
                    <script>
                    document.getElementById("form_<?php echo $frow['field_id']; ?>").disabled = true;
                    document.getElementById("img_<?php echo $frow['field_id']; ?>").style.display = "none";
                   </script>

                    <?php
                }
             }
          echo "</div>";
        }

        end_group();
        ?>
        
    </tr> 
		</table>

		  </td>
		 </tr>
		</table>

		</div>

	<?php } //end insurer for loop 
          ?>
            
	</div>
</div>

<?php } // end of "if not simplified_demographics" ?>
</div></div>
<center>
    <a href="javascript:submitme();" class='css_button'>
            <span><?php xl('Save','e'); ?></span>
    </a>
</center>
</form>

<br>

<script language="JavaScript">

 // fix inconsistently formatted phone numbers from the database
 var f = document.forms[0];
 if (f.form_phone_contact) phonekeyup(f.form_phone_contact,mypcc);
 if (f.form_phone_home   ) phonekeyup(f.form_phone_home   ,mypcc);
 if (f.form_phone_biz    ) phonekeyup(f.form_phone_biz    ,mypcc);
 if (f.form_phone_cell   ) phonekeyup(f.form_phone_cell   ,mypcc);

<?php if (! $GLOBALS['simplified_demographics']) { ?>
 phonekeyup(f.i1subscriber_phone,mypcc);
 phonekeyup(f.i2subscriber_phone,mypcc);
 phonekeyup(f.i3subscriber_phone,mypcc);
<?php } ?>

<?php //if ($GLOBALS['concurrent_layout'] && $set_pid) { ?>
// parent.left_nav.setPatient(<?php // echo "'" . addslashes($result['fname']) . " " . addslashes($result['lname']) . "',$pid,'" . addslashes($result['pubpid']) . "','', ' " . xl('DOB') . ": " . oeFormatShortDate($result['DOB_YMD']) . " " . xl('Age') . ": " . getPatientAgeDisplay($result['DOB_YMD']) . "'"; ?>);
// parent.left_nav.setRadio(window.name, 'dem');
<?php // } ?>

<?php echo $date_init; ?>
<?php if (! $GLOBALS['simplified_demographics']) { for ($i=1; $i<=3; $i++): ?>
 Calendar.setup({inputField:"i<?php echo $i?>effective_date", ifFormat:"%Y-%m-%d", button:"img_i<?php echo $i?>effective_date"});
 Calendar.setup({inputField:"i<?php echo $i?>subscriber_DOB", ifFormat:"%Y-%m-%d", button:"img_i<?php echo $i?>dob_date"});
<?php endfor; } ?>
</script>

<!-- include support for the list-add selectbox feature -->
<?php include $GLOBALS['fileroot']."/library/options_listadd.inc"; ?>

</body>

</html>
