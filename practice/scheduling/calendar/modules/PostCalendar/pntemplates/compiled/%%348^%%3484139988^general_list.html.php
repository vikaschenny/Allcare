<?php /* Smarty version 2.6.2, created on 2016-03-22 09:02:50
         compiled from /mnt/stor10-wc2-dfw1/551939/551948/allcare.texashousecalls.com/web/content/templates/pharmacies/general_list.html */ ?>
<?php require_once(SMARTY_DIR . 'core' . DIRECTORY_SEPARATOR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'xl', '/mnt/stor10-wc2-dfw1/551939/551948/allcare.texashousecalls.com/web/content/templates/pharmacies/general_list.html', 2, false),array('modifier', 'upper', '/mnt/stor10-wc2-dfw1/551939/551948/allcare.texashousecalls.com/web/content/templates/pharmacies/general_list.html', 20, false),)), $this); ?>
<a href="controller.php?practice_settings&<?php echo $this->_tpl_vars['TOP_ACTION']; ?>
pharmacy&action=edit" <?php  if (!$GLOBALS['concurrent_layout']) echo "target='Main'";  ?> onclick="top.restoreSession()" class="css_button" >
<span><?php echo smarty_function_xl(array('t' => 'Add a Pharmacy'), $this);?>
</span></a> 
<!--<a href="/templates/pharmacies/pharmacy_dropdown_1to1.php" <?php  if (!$GLOBALS['concurrent_layout']) echo "target='Main'";  ?> onclick="top.restoreSession()" class="css_button" >
<span><?php echo smarty_function_xl(array('t' => 'Add Pharmacy Custom Attributes'), $this);?>
</span></a>
<a href="/templates/pharmacies/pharmacy_1ton.php"  onclick='top.restoreSession()' class='css_button' ><span> Pharmacy Preferences </span></a>
<br><br>-->
<table cellpadding="1" cellspacing='0' class="showborder">
	<tr class="showborder_head">
		<th width="170px"><b><?php echo smarty_function_xl(array('t' => 'Name'), $this);?>
</b></th>
		<th width="250px"><b><?php echo smarty_function_xl(array('t' => 'Address'), $this);?>
</b></th>
		<th><b><?php echo smarty_function_xl(array('t' => 'Default Method'), $this);?>
</b></th>
                <th><b><?php echo smarty_function_xl(array('t' => 'Others'), $this);?>
</b></th>
	</tr>
	<?php if (count($_from = (array)$this->_tpl_vars['pharmacies'])):
    foreach ($_from as $this->_tpl_vars['pharmacy']):
?>
	<tr height="22">
		<td><a href="<?php echo $this->_tpl_vars['CURRENT_ACTION']; ?>
action=edit&id=<?php echo $this->_tpl_vars['pharmacy']->id; ?>
" onclick="top.restoreSession()"><?php echo $this->_tpl_vars['pharmacy']->name; ?>
&nbsp;</a></td>
		<td>
		<?php if ($this->_tpl_vars['pharmacy']->address->line1 != ''):  echo $this->_tpl_vars['pharmacy']->address->line1; ?>
, <?php endif; ?>
		<?php if ($this->_tpl_vars['pharmacy']->address->city != ''):  echo $this->_tpl_vars['pharmacy']->address->city; ?>
, <?php endif; ?>
			<?php echo ((is_array($_tmp=$this->_tpl_vars['pharmacy']->address->state)) ? $this->_run_mod_handler('upper', true, $_tmp) : smarty_modifier_upper($_tmp)); ?>
 <?php echo $this->_tpl_vars['pharmacy']->address->zip; ?>
&nbsp;</td>
		<td><?php echo $this->_tpl_vars['pharmacy']->get_transmit_method_display(); ?>
&nbsp;
                </td>
                <td width="178"><select class="pharmacy" onchange="showPopOver(this)"><option value="">Select</option><option value="Pharmacy Custom Attributes">Add Custom Attributes</option><option value="Pharmacy Preferences">Pharmacy Preferences</option></select><div class="newwindowicon"></div></td>
                <?php endforeach; unset($_from); else: ?>
	</tr>

	<tr class="center_display">
		<td colspan="4"><b><?php echo smarty_function_xl(array('t' => 'No Pharmacies Found'), $this);?>
<b></td>
	</tr>
	<?php endif; ?>
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
<style type="text/css">
    <?php echo '
        .pharmacy{
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
            height: 100%;
            width: 100%;
        }
        .panel-body{
            overflow: auto;
            padding:0px 0px 15px 0px;
            
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
        
                        
    '; ?>

</style>
<script language="JavaScript" type="text/javascript">
<?php echo '
 var settings = {
    trigger:\'click\',
    title:\'Template\',
    content:\'\',						
    multi:true,						
    closeable:false,
    style:\'\',
    delay:300,
    padding:true,
    backdrop:false,
};
popoverSettings = {
        width:500,
        height:300,
        delay:{show:2000,hide:1000},
        closeable:true,
        title:\'\',
        trigger: \'click\',
        dismissible:true,
        type:\'iframe\',
	url:"",
        fullscreen:true,
        onHide: function($element) {}
};
//get href parameters values
var getUrlParameter = function getUrlParameter(url,sParam) {
    var sPageURL = url.substring(url.indexOf("?")+1,url.length),
        sURLVariables = sPageURL.split(\'&\'),
        sParameterName,
        i;
    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split(\'=\');

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
    if($self.val() == ""){
        $self.parent(\'td\').children(".newwindowicon").html("");
    }else if($self.val() == "Pharmacy Preferences"){
        var $selectpharma = $self.parents("tr").find(\'td:first-child > a\');
        modalbodyurl =  "/templates/pharmacies/add_pharmacy.php?pharmaid=";
        modalbody = modalbodyurl+getUrlParameter($selectpharma.attr("href"),"id");
        $self.parent(\'td\').children(".newwindowicon").html("<a href=\'#\' class=\'\' data-toggle=\'modal\' data-modalbody=\'"+modalbodyurl+"\' data-target=\'#Modal\'><i class=\'glyphicon glyphicon-new-window\'></i></a>");
        $(\'#Modal\').find(\'.modal-title\').html($selectpharma.text());
        $(\'#Modal\').find(\'.modal-body\').empty();
        loadmodalbody($(\'#Modal\').find(\'.modal-body\'),modalbody);
        $(\'#Modal\').modal({
            show: true
        });
    }else{
        var $selectpharma = $self.parents("tr").find(\'td:first-child > a\');
        $self.parent(\'td\').children(".newwindowicon").html("<a href=\'#\' class=\'show-pop-async popoverph\'  title=\'Click to see Template\' data-placement=\'right-bottom\'><i class=\'glyphicon glyphicon-new-window\'></i></a>");
        popoverSettings.url = "/templates/pharmacies/pharmacies_full_1to1.php?phid1="+getUrlParameter($selectpharma.attr("href"),"id");
        popoverSettings.title = $self.val();
        $self.parent(\'td\').children(".newwindowicon").find(\'a.popoverph\').webuiPopover(\'destroy\').webuiPopover($.extend({},settings,popoverSettings));
        setTimeout(function(){$self.parent(\'td\').children(".newwindowicon").find(\'a.popoverph\').webuiPopover(\'show\')},100);
    }
   
}
//modal window load
$(\'#Modal\').on(\'show.bs.modal\', function (event) {
    var $currentTarget = $(event.relatedTarget);
    var $modal = $(this);
    $(this).find(".modal-body").css("height",$(window.parent.document).height()+"px");
    if($currentTarget.length !=0){
        $modal.find(\'.modal-body\').empty();
        var modalbodyurl = $currentTarget.data(\'modalbody\')+getUrlParameter($currentTarget.parents("tr").find(\'td:first-child > a\').attr("href"),"id");
        $modal.find(\'.modal-title\').html($currentTarget.parents("tr").find(\'td:first-child > a\').text());
       loadmodalbody($modal.find(\'.modal-body\'),modalbodyurl);
    }
});

function phrmacytable(event,target){
    event.preventDefault();
    event.stopPropagation();
    $("#Modal .modal-body").css("overflow","hidden");
    var getpharmaid = getUrlParameter($(target).attr("href"),"pharmaid");
    var getpharmacyid = getUrlParameter($(target).attr("href"),"pharmacyid") || "";
    var inmode = getUrlParameter($(target).attr("href"),"inmode")|| "";
    var frameurl = "/templates/pharmacies/pharmacy_data.php?pharmaid="+getpharmaid+"&pharmacyid="+getpharmacyid+"&inmode="+inmode;
    $("#Modal .modal-body").append(\'<div id="penalcontenar"><div class="panel panel-default"><div class="panel-heading">Pharmacy Preferences<button id="penalclose" type="button" class="close">&times;</button></div><div class="panel-body"><iframe src="\'+frameurl+\'" width="200" height="200"></iframe></div></div></div>\');
    var penalbodyheight = ($(\'.modal-body\').height() - ($(".panel-heading").height()+parseInt($("#penalcontenar").css("padding-bottom"))+parseInt($("#penalcontenar").css("padding-top"))));
    $(\'.panel-body\').css({height:penalbodyheight});
    $(\'.panel-body > iframe\').css({height:penalbodyheight});
    $("#penalcontenar").animate({opacity:1,top:0},400);
    $("#penalclose").click(function(){
       $("#penalcontenar").animate({opacity:0,top:-50},400,function(){$(this).remove();});
       $("#Modal .modal-body").css("overflow","auto");
    });
}


function loadmodalbody($target,url){
    $target.load(url);
}

function hidepopover(){
    $(\'a.popoverph\').webuiPopover(\'hide\');
}
function hidepenal(type){
   $("#penalcontenar").animate({opacity:0,top:-50},400,function(){$(this).remove();});
   $("#Modal .modal-body").css("overflow","auto");
   if(type[0]=="save"){
       $("#pharmacydata_div").load("/templates/pharmacies/pharmacy_data_list.php?pharmaid="+type[1],function(){
           $(this).css("display","block");
           $(this).next().remove();
       });
   }
}
function deleterow(event,target){
    event.preventDefault();
    event.stopPropagation();
    var url = "/interface/patient_file/practice_settings_deleter.php?pharmacyid="+getUrlParameter($(target).attr("href"),"pharmacyid");
    var alertcontent = "Do you really want to delete pharmacy_id "+ getUrlParameter($(target).attr("href"),"pharmacyid")+"and all subordinate data?"
    $.confirm({
            title: \'Confirm!\',
            content:alertcontent,
            confirm: function () {
                $.ajax({
                    url:url,
                    success:function(){
                        $(target).parents("tr").remove();
                        $.alert(\'deleted successfully!\');
                    },error:function(){
                        $.alert(\'Ajax Error not deleted Please Try!\');
                    }
                })
            },
            cancel: function () {
                    //$.alert(\'Canceled!\');
            }
    });
    
}
'; ?>

</script>