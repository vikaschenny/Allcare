<?php /* Smarty version 2.6.2, created on 2016-12-05 11:56:29
         compiled from /var/www/html/allcaretphc/templates/insurance_companies/general_list.html */ ?>
<?php require_once(SMARTY_DIR . 'core' . DIRECTORY_SEPARATOR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'xl', '/var/www/html/allcaretphc/templates/insurance_companies/general_list.html', 2, false),array('modifier', 'upper', '/var/www/html/allcaretphc/templates/insurance_companies/general_list.html', 20, false),)), $this); ?>
 <a href="controller.php?practice_settings&<?php echo $this->_tpl_vars['TOP_ACTION']; ?>
insurance_company&action=edit" <?php  if (!$GLOBALS['concurrent_layout']) echo "target='Main'";  ?> onclick="top.restoreSession()" class="css_button" >
<span><?php echo smarty_function_xl(array('t' => 'Add a Company'), $this);?>
</span></a>
<!--<a href="/templates/insurance_companies/insurance_dropdown_1to1.php" <?php  if (!$GLOBALS['concurrent_layout']) echo "target='Main'";  ?> onclick="top.restoreSession()" class="css_button" >
<span><?php echo smarty_function_xl(array('t' => 'Add a Company Custom Attributes'), $this);?>
</span></a>
<a href="/templates/insurance_companies/insuranceplans_1ton.php?active=all"  onclick='top.restoreSession()' class='css_button' ><span>Insurance Plans </span></a>
<a href="/templates/insurance_companies/provider_credentials_1ton.php?active=all"  onclick='top.restoreSession()' class='css_button' ><span>Provider Credentials </span></a>-->
<br>
<table cellpadding="1" cellspacing="0" class="showborder">
	<tr class="showborder_head">
		<th width="170px"><b><?php echo smarty_function_xl(array('t' => 'Name'), $this);?>
</b></th>
		<th width="250px"><b><?php echo smarty_function_xl(array('t' => 'City, State'), $this);?>
</b></th>
		<th><b><?php echo smarty_function_xl(array('t' => 'Default X12 Partner'), $this);?>
</b></th>
                <th><b><?php echo smarty_function_xl(array('t' => 'Others'), $this);?>
</b></th>
                
	</tr>
	<?php if (count($_from = (array)$this->_tpl_vars['icompanies'])):
    foreach ($_from as $this->_tpl_vars['insurancecompany']):
?>
	<tr height="22">
                
		<td><a href="<?php echo $this->_tpl_vars['CURRENT_ACTION']; ?>
action=edit&id=<?php echo $this->_tpl_vars['insurancecompany']->id; ?>
" onsubmit="return top.restoreSession()"><?php echo $this->_tpl_vars['insurancecompany']->name; ?>
&nbsp;</a></td> 
                <td><?php echo $this->_tpl_vars['insurancecompany']->address->city; ?>
 <?php echo ((is_array($_tmp=$this->_tpl_vars['insurancecompany']->address->state)) ? $this->_run_mod_handler('upper', true, $_tmp) : smarty_modifier_upper($_tmp)); ?>
&nbsp;</td>
		<td><?php echo $this->_tpl_vars['insurancecompany']->get_x12_default_partner_name(); ?>
&nbsp;</td>
                <td width="209"><select id="insurance" onchange="showPopOver(this)"><option value="">Select</option><option value="Add Custom Attributes">Insurance Custom Attributes</option><option value="insurance_company">Insurance Plans</option><option value="provider_cred">Provider Credentials </option></select><div class="newwindowicon"></div></td>
                
	</tr>
	<?php endforeach; unset($_from); else: ?> 
	<tr class="center_display">
		<td colspan="4"><?php echo smarty_function_xl(array('t' => 'No Insurance Companies Found'), $this);?>
</td>
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
        #insurance{
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
        }else if($self.val() == "insurance_company" || $self.val() == "provider_cred" ){
            var $selectpharma = $self.parents("tr").find(\'td:first-child > a\');
            modalbodyurl =  "/templates/insurance_companies/add_"+$self.val()+".php?insuranceid=";
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
            popoverSettings.url = "/templates/insurance_companies/insurance_full_1to1.php?insid1="+getUrlParameter($selectpharma.attr("href"),"id");
            popoverSettings.title = $self.val();
            $self.parent(\'td\').children(".newwindowicon").find(\'a.popoverph\').webuiPopover(\'destroy\').webuiPopover($.extend({},settings,popoverSettings));
            setTimeout(function(){$self.parent(\'td\').children(".newwindowicon").find(\'a.popoverph\').webuiPopover(\'show\')},100);
        }

    }
    
    function hidepopover(){
        $(\'a.popoverph\').webuiPopover(\'hide\');
    }
    
    function hidepenal(type){
        $("#penalcontenar").animate({opacity:0,top:-50},400,function(){$(this).remove();});
        $("#Modal .modal-body").css("overflow","auto");
        if(type[0]=="save"){
            $("#insurancecompanydata_div").load("/templates/insurance_companies/"+type[2]+".php?insuranceid="+type[1],function(){
                $(this).css("display","block");
                $(this).next().remove();
            });
        }
     }
    
    function insurancetable(event,target,paneltitle){
        event.preventDefault();
        event.stopPropagation();
        $("#Modal .modal-body").css("overflow","hidden");
        var panelheading = paneltitle;
        var frameurl = (function(){
            var oldurl = $(target).attr("href");
            return "/templates/insurance_companies/"+oldurl;
        })(target);
        $("#Modal .modal-body").append(\'<div id="penalcontenar"><div class="panel panel-default"><div class="panel-heading">\'+panelheading+\'<button id="penalclose" type="button" class="close">&times;</button></div><div class="panel-body"><iframe src="\'+frameurl+\'" width="200" height="200"></iframe></div></div></div>\');
        var penalbodyheight = ($(\'.modal-body\').height() - ($(".panel-heading").height()+parseInt($("#penalcontenar").css("padding-bottom"))+parseInt($("#penalcontenar").css("padding-top"))));
        $(\'.panel-body\').css({height:penalbodyheight});
        $(\'.panel-body > iframe\').css({height:penalbodyheight});
        $("#penalcontenar").animate({opacity:1,top:0},400);
        $("#penalclose").click(function(){
           $("#penalcontenar").animate({opacity:0,top:-50},400,function(){$(this).remove();});
           $("#Modal .modal-body").css("overflow","auto");
        });
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
    function loadmodalbody($target,url){
        $target.load(url);
    }
    
    function deleteinsurance1tonrow(event,target,id,wpsettings){
        event.preventDefault();
        event.stopPropagation();
        var insid=getUrlParameter($(target).attr("href"),"insuranceid");
        var plan_id=getUrlParameter($(target).attr("href"),"insuid");
        var url = "/interface/patient_file/practice_settings_deleter.php?"+$(target).attr("href").substring(($(target).attr("href").indexOf("?")+1),$(target).attr("href").length);
        var alertcontent = "Do you really want to delete "+id+" "+ getUrlParameter($(target).attr("href"),id)+" and all subordinate data?";
        $.confirm({
                title: \'Confirm!\',
                content:alertcontent,
                confirm: function () {
                    $.ajax({
                        url:url,
                        success:function(){
//                            if(wpsettings=="WP"){
//                                $.ajax({
//                                  url:url,
//                                  data:{insid:insid,plan_id:plan_id},
//                                  success:function(response){
//                                      $(target).parents("tr").remove();
//                                      $.alert(\'deleted successfully!\');
//                                  },error:function(){
//                                        alert(\'Ajax Error not deleted Please Try!\');
//                                    }
//                                });
//                            }else{
                                $(target).parents("tr").remove();
                                $.alert(\'deleted successfully!\');
//                            }
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