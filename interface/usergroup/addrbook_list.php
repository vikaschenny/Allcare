<?php
/**
 * The address book entry editor.
 * Available from Administration->Addr Book in the concurrent layout.
 *
 * Copyright (C) 2006-2010 Rod Roark <rod@sunsetsystems.com>
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * Improved slightly by tony@mi-squared.com 2011, added organization to view
 * and search
 *
 * @package OpenEMR
 * @author  Rod Roark <rod@sunsetsystems.com>
 * @link    http://open-emr.org
 */

 //SANITIZE ALL ESCAPES
 $sanitize_all_escapes=true;
 //

 //STOP FAKE REGISTER GLOBALS
 $fake_register_globals=false;
 //

 require_once("../globals.php");
 require_once("$srcdir/acl.inc");
 require_once("$srcdir/formdata.inc.php");
 require_once("$srcdir/options.inc.php");
 require_once("$srcdir/htmlspecialchars.inc.php");

 $popup = empty($_GET['popup']) ? 0 : 1;

 $form_fname = trim($_POST['form_fname']);
 $form_lname = trim($_POST['form_lname']);
 $form_specialty = trim($_POST['form_specialty']);
 $form_organization = trim($_POST['form_organization']);
 $form_abook_type = trim($_REQUEST['form_abook_type']);
 $form_external = $_POST['form_external'] ? 1 : 0;

$sqlBindArray = array();
$query = "SELECT u.*, lo.option_id AS ab_name, lo.option_value as ab_option FROM users AS u " .
  "LEFT JOIN list_options AS lo ON " .
  "list_id = 'abook_type' AND option_id = u.abook_type " .
  "WHERE u.active = 1 AND ( u.authorized = 1 OR u.username = '' ) ";
if ($form_organization) {
 $query .= "AND u.organization LIKE ? ";
 array_push($sqlBindArray,$form_organization."%");
}
if ($form_lname) {
 $query .= "AND u.lname LIKE ? ";
 array_push($sqlBindArray,$form_lname."%");
}
if ($form_fname) {
 $query .= "AND u.fname LIKE ? ";
 array_push($sqlBindArray,$form_fname."%");
}
if ($form_specialty) {
 $query .= "AND u.specialty LIKE ? ";
 array_push($sqlBindArray,"%".$form_specialty."%");
}
if ($form_abook_type) {
 $query .= "AND u.abook_type LIKE ? ";
 array_push($sqlBindArray,$form_abook_type);
}
if ($form_external) {
 $query .= "AND u.username = '' ";
}
if ($form_lname) { 
    $query .= "ORDER BY u.lname, u.fname, u.mname";
} else if ($form_organization) {
    $query .= "ORDER BY u.organization";
} else {
    $query .= "ORDER BY u.organization, u.lname, u.fname";
}
$query .= " LIMIT 500";

$res = sqlStatement($query,$sqlBindArray);

?>
<html>

<head>

<link rel="stylesheet" href='<?php echo $css_header ?>' type='text/css'>
<link rel="stylesheet" type="text/css" href="../../library/bootstrap/docs/css/bootstrap-3.2.0.min.css" media="screen" />
<link rel="stylesheet" type="text/css" href="../../library/js/fancybox/jquery.fancybox-1.2.6.css" media="screen" />
<link rel="stylesheet" type="text/css" href="../../library/popover/css/jquery.webui-popover.min.css" media="screen" />
<link rel="stylesheet" type="text/css" href="../../library/jalert/css/jquery-confirm.css" media="screen" />
<title><?php echo xlt('Address Book'); ?></title>
<script type="text/javascript" src="../../library/js/jquery.1.3.2.js"></script>
<!-- style tag moved into proper CSS file -->
<style>
    
    #otherbox {
        display:-ms-flexbox;
        display: -webkit-flex;
        display: -webkit-box;
        display: -moz-box;
        display: flex;
    }
    table {
        border-collapse: unset;
        border-spacing: 2px;
    }
    .addrbook{
        float:left;
    }

    .newwindowicon{
        float:left;
        margin: 3px;
    }
    .modal-lg {
        /*width: 1100px;*/
    }
    .modal-body{
       overflow: auto;
    }
    .webui-popover-content{
        padding:0 !important;
    }
    .content{
        clip:auto !important;
        font-size:14px;
    }

    .panel-heading{
        font-size:15px;
    }
    .panel-body > iframe {
        border: 0 none;
        width: 100%;
    }
    .panel-body{
        overflow: auto;
        padding:0px 0px 0px 0px;
    }
    #penalcontenar {
        left: 0;
        padding: 13px;
        position: absolute;
        top: -50px;
        opacity : 0.20;
        width: 100%;
        height:100%;
    }
</style>
</head>

<body class="body_top">

<div id="addressbook_list">
    
<form method='post' action='addrbook_list.php' onsubmit='return top.restoreSession()'>
<table>
 <tr class='search'> <!-- bgcolor='#ddddff' -->
     <td>&nbsp;</td>
   <td>
   <?php echo xlt('Organization')?>:
   <input type='text' name='form_organization' size='10' value='<?php echo attr($_POST['form_organization']); ?>'
    class='inputtext' title='<?php echo xla("All or part of the organization") ?>' />&nbsp;
   <?php echo xlt('First Name')?>:
   <input type='text' name='form_fname' size='10' value='<?php echo attr($_POST['form_fname']); ?>'
    class='inputtext' title='<?php echo xla("All or part of the first name") ?>' />&nbsp;
   <?php echo xlt('Last Name')?>:
   <input type='text' name='form_lname' size='10' value='<?php echo attr($_POST['form_lname']); ?>'
    class='inputtext' title='<?php echo xla("All or part of the last name") ?>' />&nbsp;
   <?php echo xlt('Specialty')?>:
   <input type='text' name='form_specialty' size='10' value='<?php echo attr($_POST['form_specialty']); ?>'
    class='inputtext' title='<?php echo xla("Any part of the desired specialty") ?>' />&nbsp;
<?php
  echo xlt('Type') . ": ";
  // Generates a select list named form_abook_type:
  echo generate_select_list("form_abook_type", "abook_type", $_REQUEST['form_abook_type'], '', 'All');
?>
   <input type='checkbox' name='form_external' value='1'<?php if ($form_external) echo ' checked'; ?>
    title='<?php echo xla("Omit internal users?") ?>' />
   <?php echo xlt('External Only')?>&nbsp;&nbsp;
   <input type='submit' title='<?php echo xla("Use % alone in a field to just sort on that column") ?>' class='button' name='form_search' value='<?php echo xla("Search")?>' />
   <input type='button' class='button' value='<?php echo xla("Add New"); ?>' onclick='doedclick_add(document.forms[0].form_abook_type.value)' />
</td>
</tr>
</table>

    <table>
 <tr class='head'>
  <td><?php echo xlt('Others'); ?></td>    
  <td><?php echo xlt('Agency Portal'); ?></td>   
  <td title='<?php echo xla('Click to view or edit'); ?>'><?php echo xlt('Organization'); ?></td>
  <td><?php echo xlt('Name'); ?></td>
  <td><?php echo xlt('Local'); ?></td><!-- empty for external -->
  <td><?php echo xlt('Type'); ?></td>
  <td><?php echo xlt('Specialty'); ?></td>
  <td><?php echo xlt('Phone'); ?></td>
  <td><?php echo xlt('Mobile'); ?></td>
  <td><?php echo xlt('Fax'); ?></td>
  <td><?php echo xlt('Email'); ?></td>
  <td><?php echo xlt('Street'); ?></td>
  <td><?php echo xlt('City'); ?></td>
  <td><?php echo xlt('State'); ?></td>
  <td><?php echo xlt('Postal'); ?></td>
 </tr>

<?php
 $encount = 0;
 while ($row = sqlFetchArray($res)) {
  ++$encount;
  //$bgcolor = "#" . (($encount & 1) ? "ddddff" : "ffdddd");
  $bgclass = (($encount & 1) ? "evenrow" : "oddrow");
  $username = $row['username'];
  if (! $row['active']) $username = '--';

  $displayName = $row['fname'] . ' ' . $row['mname'] . ' ' . $row['lname']; // Person Name

  if ( acl_check('admin', 'practice' ) || (empty($username) && empty($row['ab_name'])) ) {
   // Allow edit, since have access or (no item type and not a local user)
   $trTitle = xl('Edit'). ' ' . $displayName;
   echo " <tr class='detail $bgclass' style='cursor:pointer' " .
        "onclick='doedclick_edit(" . $row['id'] . ")' title='".attr($trTitle)."'>\n"; 
  }
  else {
   // Do not allow edit, since no access and (item is a type or is a local user)
   $trTitle = $displayName . " (" . xl("Not Allowed to Edit") . ")";
   echo " <tr class='detail $bgclass' title='".attr($trTitle)."'>\n";
  }
  
  // echo "SELECT agencyportal FROM tbl_addrbk_custom_attr_1to1 WHERE addrbk_type_id='".$row['id']."'";
    $portalStatus = sqlQuery("SELECT agencyportal FROM tbl_addrbk_custom_attr_1to1 WHERE addrbk_type_id=?",array($row['id']));
    echo '<td onclick="stopbubling(event)" width="250" style="cursor:default" title="Select Others"><div id="otherbox"><select class="addrbook" onchange="showPopOver(this)" data-userid="'.$row['id'].'"><option value="">Select</option><option value="Addressbook Custom Attributes">Addressbook Custom Attributes</option><option value="add_abook_contact">Addressbook Contacts</option><option value="add_abook_cred">Addressbook Credentials</option></select><div class="newwindowicon"></div></div></td>';
    if ($portalStatus['agencyportal']=='YES') {
      $portalLogin = sqlQuery("SELECT uid FROM `tbl_allcare_agencyportal` WHERE `uid`=?", array($row['id']));
      echo "<td><a class='css_button iframe small_modal' href='create_agencyportallogin.php?portalsite=on&userid=" . htmlspecialchars($row['id'],ENT_QUOTES) . "' onclick='top.restoreSession()'>";
      if (empty($portalLogin)) {
        echo "<span>".htmlspecialchars(xl('Create'),ENT_NOQUOTES)."</span></a></td>";
      }
      else {
        echo "<span>".htmlspecialchars(xl('Reset'),ENT_NOQUOTES)."</span></a></td>";
      }
    }
    else {
       echo "<td>User Not Authorized </td>";
    } 
  
  echo "  <td class='organization'>" . text($row['organization']) . "</td>\n";
  echo "  <td class='name'>" . text($displayName) . "</td>\n";
  echo "  <td>" . ($username ? '*' : '') . "</td>\n";
  echo "  <td>" . generate_display_field(array('data_type'=>'1','list_id'=>'abook_type'),$row['ab_name']) . "</td>\n";
  echo "  <td>" . text($row['specialty']) . "</td>\n";
  echo "  <td>" . text($row['phonew1'])   . "</td>\n";
  echo "  <td>" . text($row['phonecell']) . "</td>\n";
  echo "  <td>" . text($row['fax'])       . "</td>\n";
  echo "  <td>" . text($row['email'])     . "</td>\n";
  echo "  <td>" . text($row['street'])    . "</td>\n";
  echo "  <td>" . text($row['city'])      . "</td>\n";
  echo "  <td>" . text($row['state'])     . "</td>\n";
  echo "  <td>" . text($row['zip'])       . "</td>\n";
  echo " </tr>\n";
 }
?>
</table>
    <!-- Modal -->
<div class="modal fade" id="Modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Modal title</h4>
      </div>
      <div class="modal-body">
      </div>
    </div>
  </div>
</div>
    
<div style="display: none;">
  <a class="iframe addrbookedit_modal"></a>
</div>
<script language="JavaScript">

<?php if ($popup) require($GLOBALS['srcdir'] . "/restoreSession.php"); ?>
$j = $.noConflict();
function stopbubling(event){
    event.stopPropagation();
}
// Callback from popups to refresh this display.
function refreshme() {
 // location.reload();
 document.forms[0].submit();
}

// Process click to pop up the add window.
function doedclick_add(type) {
 top.restoreSession(); 
 //dlgopen('addrbook_edit.php?type=' + type, '_blank', 700, 550);
 dlgopen('addrbook_edit.php?userid=0&type=' + type, '_blank', 700, 550);
}

// Process click to pop up the edit window.
function doedclick_edit(userid) {
 top.restoreSession();
 dlgopen('addrbook_edit.php?userid=' + userid, '_blank', 700, 550);
}

$j(document).ready(function(){

    $j(".iframe").fancybox( {
        'overlayOpacity' : 0.0,
        'showCloseButton' : true,
        'centerOnScroll' : false
    });



  // initialise a link
  $j(".addrbookedit_modal").fancybox( {
    'overlayOpacity' : 0.0,
    'showCloseButton' : true,
    'frameHeight' : 550,
    'frameWidth' : 700
  });
});

</script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/common.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/fancybox/jquery.fancybox_costom-1.2.6.js"></script>
<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/bootstrap/docs/js/bootstrap-3.2.0.min.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/popover/js/jquery.webui-popover.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/jalert/js/jquery-confirm.js"></script>
<?php if ($popup) { ?>
<script type="text/javascript" src="../../library/topdialog.js"></script>
<?php } ?>
<script type="text/javascript" src="../../library/dialog.js"></script>

<script>
var settings = {
        trigger:'click',
        title:'Template',
        content:'',						
        multi:true,						
        closeable:false,
        style:'',
        delay:300,
        padding:true,
        backdrop:false,
    };
    popoverSettings = {
        width:500,
        height:300,
        delay:{show:2000,hide:1000},
        closeable:true,
        title:'',
        trigger: 'click',
        dismissible:true,
        type:'iframe',
	url:"",
        fullscreen:true,
        onHide: function($element) {}
    };

    //get href parameters values
    var getUrlParameter = function getUrlParameter(url,sParam) {
        var sPageURL = url.substring(url.indexOf("?")+1,url.length),
            sURLVariables = sPageURL.split('&'),
            sParameterName,
            i;
        for (i = 0; i < sURLVariables.length; i++) {
            sParameterName = sURLVariables[i].split('=');

            if (sParameterName[0] === sParam) {
                return sParameterName[1] === undefined ? true : sParameterName[1];
            }
        }
    };

    //onChange DropDown 
   function showPopOver(target){
        var $self = $(target);
        var modalbodyurl="";
        var modalbody="";
        var getuserid = $self.data("userid");
        if($self.val() == ""){
            $self.parent('td #otherbox').children(".newwindowicon").html("");
        }else if($self.val() == "add_abook_contact" || $self.val() == "add_abook_cred" ){
            modalbodyurl =  $self.val()+".php?abookuserid=";
            modalbody = modalbodyurl+getuserid;
            $self.parent('td #otherbox').children(".newwindowicon").html("<a href='#' onclick='showmodal(event,this)' class='' data-toggle='modal' data-modalbody='"+modalbodyurl+"' data-target='#Modal'><i class='glyphicon glyphicon-new-window'></i></a>");
            $('#Modal').find('.modal-title').html($self.parents('tr').find('.name').text());
            $('#Modal').find('.modal-body').empty();
            loadmodalbody($('#Modal').find('.modal-body'),modalbody);
            showmodal();
        }else{
            var $selectpharma = $self.parents("tr").find('td:first-child > a');
            $self.parent('td #otherbox').children(".newwindowicon").html("<a href='#' class='show-pop-async popoverph'  title='Click to see Template' data-placement='right-bottom'><i class='glyphicon glyphicon-new-window'></i></a>");
            popoverSettings.url = "addrbk_attr_full_1to1.php?addrid1="+getuserid;
            popoverSettings.title = $self.val();
            $self.parent('td #otherbox').children(".newwindowicon").find('a.popoverph').webuiPopover('destroy').webuiPopover($.extend({},settings,popoverSettings));
            setTimeout(function(){$self.parent('td #otherbox').children(".newwindowicon").find('a.popoverph').webuiPopover('show')},100);
        }

    }
    function showmodal(event,target){
        
        if(target != undefined){
            event.preventDefault();
            event.stopPropagation();
            $('#Modal').find('.modal-body').empty();
            var modalbodyurl = $(target).data('modalbody')+$(target).parent().prev().data("userid");
             $('#Modal').find('.modal-title').html($(target).parents('tr').find('.name').text());
            loadmodalbody( $('#Modal').find('.modal-body'),modalbodyurl);
        }
        $('#Modal').modal({
            show: true
        });
    }
    function hidepopover(providerlogin,id){
        $('a.popoverph').webuiPopover('hide');
        if(providerlogin != undefined && providerlogin != "")
           $("[data-userid="+id+"]").parents("td").next().html(providerlogin);
        
        $j(".iframe").fancybox( {
            'overlayOpacity' : 0.0,
            'showCloseButton' : true,
            'centerOnScroll' : false
        });
           
    }
    function settorest(id){
        $("[data-userid="+id+"]").parents("td").next().find('a span').html("Reset");
    }
    
    function loadmodalbody($target,url){
        $target.load(url);
    }
    
    function hidepenal(type){
        $("#penalcontenar").animate({opacity:0,top:-50},400,function(){$(this).remove();});
        $("#Modal .modal-body").css("overflow","auto");
        if(type[0]=="save"){
            $("#userdata_div").load(type[2]+".php?abookuserid="+type[1],function(){
                $(this).css("display","block");
                $(this).next().remove();
            });
        }
     }
    
    function addresbooktable(event,target,paneltitle){
        event.preventDefault();
        event.stopPropagation();
        $("#Modal .modal-body").css("overflow","hidden");
        var panelheading = paneltitle;
        var frameurl = (function(){
            var oldurl = $(target).attr("href");
            return oldurl;
        })(target);
        $("#Modal .modal-body").append('<div id="penalcontenar"><div class="panel panel-default"><div class="panel-heading">'+panelheading+'<button id="penalclose" type="button" class="close">&times;</button></div><div class="panel-body"><iframe src="'+frameurl+'" width="200" height="200"></iframe></div></div></div>');
        var penalbodyheight = ($('.modal-body').height() - ($(".panel-heading").height()+parseInt($("#penalcontenar").css("padding-bottom"))+parseInt($("#penalcontenar").css("padding-top"))));
        $('.panel-body').css({height:penalbodyheight});
        $('.panel-body > iframe').css({height:penalbodyheight});
        $("#penalcontenar").animate({opacity:1,top:0},400);
        $("#penalclose").click(function(){
           $("#penalcontenar").animate({opacity:0,top:-50},400,function(){$(this).remove();});
           $("#Modal .modal-body").css("overflow","auto");
        });
    }
    
    //modal window load
    $('#Modal').on('show.bs.modal', function (event) {
        var $currentTarget = $(event.relatedTarget);
        var $modal = $(this);
        $(this).find(".modal-body").css("height",$(window.parent.document).height()+"px");
    });
    
    function deleteaddressbookrow(event,target,id){
        event.preventDefault();
        event.stopPropagation();
        var url = "/interface/patient_file/practice_settings_deleter.php?"+id+"="+getUrlParameter($(target).attr("href"),id);
        var alertcontent = "Do you really want to delete "+id+" "+ getUrlParameter($(target).attr("href"),id)+" and all subordinate data?";
        $.confirm({
                title: 'Confirm!',
                content:alertcontent,
                confirm: function () {
                    $.ajax({
                        url:url,
                        success:function(){
                            $(target).parents("tr").remove();
                            $.alert('deleted successfully!');
                        },error:function(){
                            $.alert('Ajax Error not deleted Please Try!');
                        }
                    });
                },
                cancel: function () {
                        //$.alert('Canceled!');
                }
        });

    }

</script>
<a id="createbtn" class="css_button iframe small_modal" onclick="top.restoreSession()" href="create_agencyportallogin.php?portalsite=on&userid=56"><span>Create</span></span></a>
</body>
</html>
