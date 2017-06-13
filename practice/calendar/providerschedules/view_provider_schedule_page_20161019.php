<?php
require_once("../../verify_session.php");
require_once("$srcdir/patient.inc");
include_once("$srcdir/calendar.inc");

?>
<html>
    <head>
        <title> Search Provider Schedules</title>
        <script src="//code.jquery.com/jquery-latest.min.js" type="text/javascript"></script>
        <style type="text/css">@import url(../../../library/dynarch_calendar.css);</style>
        <link rel="stylesheet" type="text/css" href="../../assets/css/bootstrap.min.css">
        <script type="text/javascript" src="../../../library/dynarch_calendar.js"></script>
        <?php include_once("$srcdir/dynarch_calendar_en.inc.php"); ?>
        <link rel="stylesheet" href="../../css/version1.0/dataTables.bootstrap.min.css"/>
        <link rel="stylesheet" href="../../css/version1.0/responsive.bootstrap.min.css"/>
        <script type="text/javascript" src="../../../library/dynarch_calendar_setup.js"></script>
        <script type="text/javascript" src="../../assets/js/jquery.min.js"></script>
        <script src="../../js/responsive_datatable/version1.0/jquery.dataTables.min.js"></script>
        <script src="../../js/responsive_datatable/version1.0/dataTables.bootstrap.min.js"></script>
        <script src="../../js/responsive_datatable/version1.0/dataTables.responsive.min.js"></script>
        <script type='text/javascript' src='../../js/responsive_datatable/dataTables.bootstrap.js'></script>
        <script type="text/javascript" src="../../assets/js/bootstrap.min.js"></script>
        <script>
            $(document).ready(function() {
                $('#modalwindow').on("show.bs.modal", function(event){
                    var target = $(event.relatedTarget);
                    var modal = $(this);
                    var url = target.data("href");
                    var modalclass = target.data("modalsize");
                    var frameheight = target.data("frameheight");
                    var modalbodypadding = target.data("bodypadding");
                    var title = target.data("title"); 
                    target.addClass("active");
                    modal.find('.modal-header').show();
                    modal.find('.modal-header #myModalLabel').html(title).css("font-weight","500");
                    modal.children("div").removeClass();
                    modal.children("div").addClass("modal-dialog "+modalclass);
                    modal.find(".modal-body").css("padding",modalbodypadding+"px");
                    modal.find(".modal-body").html("<iframe src='"+url+"' style='border:none; width:100%; height:"+frameheight+"px;'></iframe>");       
                });
                $('#modalwindow').on("hidden.bs.modal", function(event){
                    $('#sidebar li a').removeClass("active");
                });
                
                $( document ).on( 'click', '.bs-dropdown-to-select-group .dropdown-menu li', function( event ) {
                    var $target = $( event.currentTarget );
                    $target.closest('.bs-dropdown-to-select-group')
                            .find('[data-bind="bs-drp-sel-value"]').val($target.attr('data-value'))
                            .end()
                            .children('.dropdown-toggle').dropdown('toggle');
                    $target.closest('.bs-dropdown-to-select-group')
                    .find('[data-bind="bs-drp-sel-label"]').text($target.context.textContent);
                    return false;
                });
                get_provider_schedule_ajax();
            });
            
            function get_provider_schedule_ajax(){
                $('#provider_searchloader').show();
                var keyword             = $("#keywordtext").val();
                var pc_keywords_andor   = $("#pc_keywords_andor").text();
                
                var selectedcategory = [];
                $('#pc_category :selected').each(function(i, selected){
                    selectedcategory[i] = $(selected).val();
                });
                var pc_category         = selectedcategory;
                
//                var selectedvisittype        = [];
//                $('#pc_visittype :selected').each(function(i, selected){
//                    selectedvisittype[i] = $(selected).val();
//                });
//                var pc_visittype       = selectedvisittype;
                
                var start               = $("#start").val();
                var end                 = $("#end").val();
                
                var selectedproviders = [];
                $('#pc_providers :selected').each(function(i, selected){
                    selectedproviders[i] = $(selected).val();
                });
                var pc_providers    = selectedproviders;
                
                var selectedfacilities        = [];
                $('#pc_facilities :selected').each(function(i, selected){
                    selectedfacilities[i] = $(selected).val();
                });
                var pc_facilities       = selectedfacilities;
                
                $('#provider_searchdata_div').html('');
                
                $.ajax({
                    url:"provider_schedule_search.php",
                    method:"POST",
                    data:{
                        keyword             : keyword,
                        pc_keywords_andor   : pc_keywords_andor,
                        pc_category         : pc_category,
//                        pc_visittype        : pc_visittype,
                        start               : start,
                        end                 : end,
                        pc_providers        : pc_providers,
                        pc_facilities       : pc_facilities
                    },
                    success:function(result){
                       $('#provider_searchdata_div').html(result);
                       $('#provider_searchloader').hide();
                       var table = $('#provider_schedule_div #search-table').DataTable();
                       $("html,body").animate({scrollTop:$('#provider_schedule_div #search-table').offset().top-60});
                    },
                    error:function(event, jqxhr, settings, thrownError){
                        alert(jqxhr);
                        $('#provider_searchloader').hide();
                    }
                });
            }
        </script>
        <style>
            .bodyclass {
                padding: 20px;
            }
            #provider_searchloader{
                background: rgba(0,0,0,0.56);
                border-radius: 4px;
                display:table;
                height: 48px;
                width: 242px;
                color: #fff;
                position: absolute;
                left: 0px;
                top:0px;
                bottom: 0px;
                right: 0px;
                margin: auto;
                display: none;
            }
            .ajax-spinner-bars {
                height: 48px;
                left: 23px;
                position: relative;
                top: 20px;
                width: 35px;
                display: table-cell;
            }
            .ajax-spinner-bars > div {
                position: absolute;
                width: 2px;
                height: 8px;
                background-color: #fff;
                opacity: 0.05;
                animation: fadeit 0.8s linear infinite;
            }
            .ajax-spinner-bars > .bar-1 {
                transform: rotate(0deg) translate(0, -12px);
                animation-delay:0.05s;
            }
            .ajax-spinner-bars > .bar-2 {
                transform: rotate(22.5deg) translate(0, -12px);
                animation-delay:0.1s;
            }
            .ajax-spinner-bars > .bar-3 {
                transform: rotate(45deg) translate(0, -12px);
                animation-delay:0.15s;
            }
            .ajax-spinner-bars > .bar-4 {
                transform: rotate(67.5deg) translate(0, -12px);
                animation-delay:0.2s;
            }
            .ajax-spinner-bars > .bar-5 {
                transform: rotate(90deg) translate(0, -12px);
                animation-delay:0.25s;
            }
            .ajax-spinner-bars > .bar-6 {
                transform: rotate(112.5deg) translate(0, -12px);
                animation-delay:0.3s;
            }
            .ajax-spinner-bars > .bar-7 {
                transform: rotate(135deg) translate(0, -12px);
                animation-delay:0.35s;
            }
            .ajax-spinner-bars > .bar-8 {
                transform: rotate(157.5deg) translate(0, -12px);
                animation-delay:0.4s;
            }
            .ajax-spinner-bars > .bar-9 {
                transform: rotate(180deg) translate(0, -12px);
                animation-delay:0.45s;
            }
            .ajax-spinner-bars > .bar-10 {
                transform: rotate(202.5deg) translate(0, -12px);
                animation-delay:0.5s;
            }
            .ajax-spinner-bars > .bar-11 {
                transform: rotate(225deg) translate(0, -12px);
                animation-delay:0.55s;
            }
            .ajax-spinner-bars > .bar-12 {
                transform: rotate(247.5deg) translate(0, -12px);
                animation-delay:0.6s;
            }
            .ajax-spinner-bars> .bar-13 {
                transform: rotate(270deg) translate(0, -12px);
                animation-delay:0.65s;
            }
            .ajax-spinner-bars > .bar-14 {
                transform: rotate(292.5deg) translate(0, -12px);
                animation-delay:0.7s;
            }
            .ajax-spinner-bars > .bar-15 {
                transform: rotate(315deg) translate(0, -12px);
                animation-delay:0.75s;
            }
            .ajax-spinner-bars> .bar-16 {
                transform: rotate(337.5deg) translate(0, -12px);
                animation-delay:0.8s;
            }
            @keyframes fadeit{
                0%{ opacity:1; }
                100%{ opacity:0;}
            }
            #provider_searchloadertitle {
                display: table-cell;
                font-size: 17px;
                padding-left: 14px;
                vertical-align: middle;
            }
            div.searchdivtable{
                padding: 1px 0px;
                display: none;
                width: 75%;
                background-color: #B4FCFF;
                margin-left: 80px;
            }
            button.searchdivtableaccordion {
                background-color: #82CAFF;
                color: black;
                cursor: pointer;
                padding: 5px;
                width: 75%;
                border: none;
                text-align: left;
                outline: none;
                font-size: 17px;
                transition: 0.4s;
                margin-left: 80px;
            }

            button.searchdivtableaccordion.active, button.searchdivtableaccordion:hover {
                background-color: #FFB682;
                color: black;
            }
            button.searchdivtableaccordion{
                margin-left: 0px;
            }
            .panel-title {
                display: block;
                padding: 10px 15px;
                text-decoration: none;
            }
            .panel-heading{
                padding: 0px;
            }
            .panel-title:hover, a.panel-title{
                color: #fff;
                text-decoration: none;
            }
            .panel-heading a:after {
                font-family:'Glyphicons Halflings';
                content:"\e114";
                float: right;
                color: #fff;
            }
            .panel-heading a.collapsed:after {
                content:"\e080";
                float: right;
                color: #fff;
            }
            .input-group-addon {
                padding: 0 6px !important;
            }
                    
        </style>
    </head>
    <?php
    /*
    * af_providers for provider filter
    * af_visittype for visit type filter 
    */

   $mpr_pc_keywords_andor   = '';
   $mpr_pc_category         = '';
   $mpr_pc_providers        = '';
   $mpr_pc_facilities       = '';

   $getdatafilter = sqlStatement("SELECT mpr_pc_keywords_andor,mpr_pc_category,mpr_pc_providers,mpr_pc_facilities FROM tbl_providerportal_filters WHERE userid = '".$_SESSION['portal_userid']."' and screen_name = 'maintain_provider_schedule'");
   while($setdatafilter = sqlFetchArray($getdatafilter)){
       $mpr_pc_keywords_andor   = $setdatafilter['mpr_pc_keywords_andor'];
       $mpr_pc_category         = $setdatafilter['mpr_pc_category'];
       $mpr_pc_providers        = $setdatafilter['mpr_pc_providers'];
       $mpr_pc_facilities       = $setdatafilter['mpr_pc_facilities'];
   }

   $mpr_pc_keywords_andor2  = explode(",",$mpr_pc_keywords_andor);
   $mpr_pc_category2        = explode(",",$mpr_pc_category);
   $mpr_pc_providers2       = explode(",",$mpr_pc_providers);
   $mpr_pc_facilities2      = explode(",",$mpr_pc_facilities);

    
    ?>
    <body class="bodyclass">
        <form class="form-horizontal">
            <div class="panel-group">
                <div class="panel panel-primary">
                  <div class="panel-heading">
                      <a data-toggle="collapse" href="#collapse1" class="panel-title collapsed">
                      Filters
                    </a>
                  </div>
                  <div id="collapse1" class="panel-collapse collapse">
                      <div class="panel-body">
                          <div class="row">
                              <div class="col-sm-12">
                                  <div class="form-group">
                                      <label class="col-sm-2 control-label" for="keywordtext">Keywords:</label>
                                      <div class="col-sm-4 input-group-sm">
                                          <div class="input-group">
                                            <input type="text" id ="keywordtext" name="keywordtext" title="Enter any keyword to search" value="" class="form-control">
                                            <div class="input-group-btn bs-dropdown-to-select-group">
                                                <button type="button" class="btn btn-default dropdown-toggle as-is bs-dropdown-to-select" data-toggle="dropdown">
                                                    <span id="pc_keywords_andor" data-bind="bs-drp-sel-label">AND</span>
                                                    <input type="hidden" name="selected_value" data-bind="bs-drp-sel-value" value="">
                                                    <span class="caret"></span>
                                                    <span class="sr-only">Toggle Dropdown</span>
                                                </button>
                                                <ul class="dropdown-menu" role="menu" style="">
                                                    <li data-value="1"><a href="#">AND</a></li>
                                                    <li data-value="2"><a href="#">OR</a></li>
                                                </ul>
                                            </div>
                                         </div>
                                      </div>
                                      <label for="pc_category" class="col-sm-2 control-label">Category:</label>
                                      <div class="col-sm-4">
                                          <select id="pc_category" class="form-control" multiple="multiple" name="pc_category">
                                              <!--<option value="" selected>Any Category</option>-->
                                              <?php
                                                $get_catgories = sqlStatement("SELECT pc_catid,pc_catname FROM openemr_postcalendar_categories WHERE pc_cattype='1' ORDER BY pc_catname");
                                                while($set_catgories = sqlFetchArray($get_catgories)){
                                                    echo "<option value = '".$set_catgories['pc_catid']."'";
                                                    if( in_array ( $set_catgories['pc_catid'], $mpr_pc_category2) == true )
                                                        echo " selected ";
                                                    echo "> ".$set_catgories['pc_catname']."</option>";
                                                }
                                            ?>
                                          </select>
                                      </div>
<!--                                      <label for="pc_visittype" class="col-sm-2 control-label">IN:</label>
                                      <div class="col-sm-4">
                                          <select id="pc_visittype" class="form-control" multiple="multiple" name="pc_visittype">
                                              <option value="" selected>Any Visit Type</option>
                                              <?php
//                                                $get_visittype = sqlStatement("SELECT option_id,title FROM list_options WHERE list_id='Appointment_Visit_Types' ORDER BY title");
//                                                while($set_visittype = sqlFetchArray($get_visittype)){
//                                                    echo "<option value = '".$set_visittype['option_id']."'> ".$set_visittype['title']."</option>";
//                                                }
                                            ?>
                                          </select>
                                      </div>-->
                                  </div>
                                  <div class="form-group">
                                      <label for="start" class="col-sm-2 control-label">From</label>
                                      <div class="col-sm-4">
                                            <div class="input-group input-group-sm">
                                                <input type="text" title="yyyy-mm-dd" onblur="dateblur(this,mypcc)" onkeyup="datekeyup(this,mypcc)" value="<?php echo date('m-d-Y',strtotime('-3 Months')); ?>" size="10" id="start" aria-describedby="fromadion" class="form-control" name="start"><span id="bettwenadion" class="input-group-addon"><img width="24" border="0" align="absbottom" height="22" title="Click here to choose a date" style="cursor: pointer" alt="[?]" id="img_from_date" src="/interface/pic/show_calendar.gif"></span>
                                            </div>
                                      </div>
                                      <label for="end" class="col-sm-2 control-label">To</label>
                                      <div class="col-sm-4">
                                            <div class="input-group input-group-sm">
                                                <input type="text" title="yyyy-mm-dd" onblur="dateblur(this,mypcc)" onkeyup="datekeyup(this,mypcc)" value="<?php echo date('m-d-Y',strtotime('+12 Months')); ?>" size="10" id="end" aria-describedby="fromadion" class="form-control" name="end"><span id="andadion" class="input-group-addon"><img width="24" border="0" align="absbottom" height="22" title="Click here to choose a date" style="cursor: pointer" alt="[?]" id="img_to_date" src="/interface/pic/show_calendar.gif"></span>
                                            </div>
                                      </div>
                                  </div>
                                  <div class="form-group">
                                      <label for="pc_providers" class="col-sm-2 control-label">Provider</label>
                                      <div class="col-sm-4">
                                           <select id="pc_providers" class="form-control" multiple='multiple' name="pc_providers">
                                              <!--<option value="" selected>All Providers</option>-->
                                              <?php
                                                $get_providers = sqlStatement("SELECT id,CONCAT(fname, ' ', lname) as providername FROM users WHERE username <> '' AND active=1 AND authorized=1 ORDER BY fname,lname");
                                                while($set_providers = sqlFetchArray($get_providers)){
                                                    echo "<option value = '".$set_providers['id']."'";
                                                    if( in_array ( $set_providers['id'], $mpr_pc_providers2) == true )
                                                        echo " selected ";
                                                    echo "> ".$set_providers['providername']."</option>";
                                                }
                                               ?>
                                          </select>
                                      </div>
                                      <label for="pc_facilities" class="col-sm-2 control-label">Facility</label>
                                      <div class="col-sm-4">
                                           <select id="pc_facilities" class="form-control" multiple='multiple' name="pc_facilities">
                                              <!--<option value="" selected>All Facilities</option>-->
                                                <?php
                                                    $get_facilities = sqlStatement("SELECT id,name FROM facility name");
                                                    while($set_facilities = sqlFetchArray($get_facilities)){
                                                        echo "<option value = '".$set_facilities['id']."'";
                                                    if( in_array ( $set_facilities['id'], $mpr_pc_facilities2) == true )
                                                        echo " selected ";
                                                    echo "> ".$set_facilities['name']."</option>";
                                                    }
                                               ?>
                                          </select>
                                      </div>
                                  </div>
                                  <div class="form-group">
                                      <div class="col-sm-3 col-sm-offset-6">
                                          <input type="button" class="btn btn-primary" name ="submitform" id="submitform" onclick="get_provider_schedule_ajax();" value="Submit">
                                      </div>
                                  </div>
                              </div>
                          </div>
                          <!--<table id='filterdiv' style="display:block;">
                                <tr>
                                    <td>
                                        <label> Keywords: </label>
                                        <input type="text" id ="keywordtext" name="keywordtext" size="15" title="Enter any keyword to search">
                                    </td>
                                    <td>
                                        <select name="pc_keywords_andor" id='pc_keywords_andor'>
                                            <option value="AND">AND</option>
                                            <option value="OR">OR</option>
                                        </select>
                                        <label> IN: </label>
                                        <select name="pc_category[]" multiple="multiple">
                                            <option value="">Any Category</option>
                                            <?php
                                            $get_catgories = sqlStatement("SELECT pc_catid,pc_catname FROM openemr_postcalendar_categories ORDER BY pc_catname");
                                            while($set_catgories = sqlFetchArray($get_catgories)){
                                                echo "<option value = '".$set_catgories['pc_catid']."'> ".$set_catgories['pc_catname']."</option>";
                                            }
                                            ?>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label> between </label>
                                        <input type="text" name="start" id="start" value="" size="10"/>
                                        <img src='/interface/pic/show_calendar.gif' align='absbottom' width='24' height='22' id='img_from_date' border='0' alt='[?]' style='cursor:pointer' title='Click here to choose a date'>
                                    </td>
                                    <td>
                                        <label> and </label>
                                        <input type="text" name="end" id="end" value="" size="10"/>
                                        <img src='/interface/pic/show_calendar.gif' align='absbottom' width='24' height='22' id='img_to_date' border='0' alt='[?]' style='cursor:pointer' title='Click here to choose a date'>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label>for</label>
                                        <select name="pc_providers[]" multiple="multiple">
                                            <option value="">All Providers</option>
                                            <?php
                                            $get_providers = sqlStatement("SELECT id,CONCAT(fname, ' ', lname) as providername FROM users WHERE username <> '' AND active=1 AND authorized=1 ORDER BY fname,lname");
                                            while($set_providers = sqlFetchArray($get_providers)){
                                                echo "<option value = '".$set_providers['id']."'> ".$set_providers['providername']."</option>";
                                            }
                                            ?>
                                        </select>
                                    </td>
                                    <td>
                                        <label>at</label>
                                        <select name="pc_facilities[]" multiple="multiple">
                                            <option value="">All Facilities</option>
                                            <?php
                                            $get_facilities = sqlStatement("SELECT id,name FROM facility name");
                                            while($set_facilities = sqlFetchArray($get_facilities)){
                                                echo "<option value = '".$set_facilities['id']."'> ".$set_facilities['name']."</option>";
                                            }
                                            ?>
                                        </select>
                                        <input type="button" name ="submitform" id="submitform" onclick="get_provider_schedule_ajax();" value="Submit">
                                    </td>
                                </tr>
                            </table>-->
                      </div>
                  </div>
                </div>
            </div>
            
            <div class='text-right'>
                <a data-modalsize='modal-md' data-frameheight="350" data-bodypadding='0' data-href='../add_edit_custom_provider_event.php?prov=true&eid=&startampm=&starttimeh=&userid=&starttimem=&date=&catid=' data-toggle='modal' data-target='#modalwindow' class="btn btn-default btn-sm" id="createproviderschedule" style="color:black; " title="Add New Provider Schedule"><span class="glyphicon"> </span>  Add Provider Schedule</a>
            </div>
        </form>
        <div id='provider_schedule_div'>
            <div id="provider_searchloader">
                <div class="ajax-spinner-bars">
                    <div class="bar-1"></div>
                    <div class="bar-2"></div>
                    <div class="bar-3"></div>
                    <div class="bar-4"></div>
                    <div class="bar-5"></div>
                    <div class="bar-6"></div>
                    <div class="bar-7"></div>
                    <div class="bar-8"></div>
                    <div class="bar-9"></div>
                    <div class="bar-10"></div>
                    <div class="bar-11"></div>
                    <div class="bar-12"></div>
                    <div class="bar-13"></div>
                    <div class="bar-14"></div>
                    <div class="bar-15"></div>
                    <div class="bar-16"></div>
                </div>
                <div id="provider_searchloadertitle">Loading...</div>
            </div>
            <duv id="provider_searchdata_div"></duv>
        </div>
        <div class="modal fade" name = "newdemographics" id="modalwindow" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-hidden="true" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header" style="background-color:#46a1b4; border-radius: 5px 5px 0px 0px;">
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel" >Add Provider Schedule</h4>
                    </div>
                    <div class="modal-body">

                    </div>
                </div>
            </div>
        </div>
    </body>
</html>

<script language="Javascript">
 Calendar.setup({inputField:"start", ifFormat:"%m/%d/%Y", button:"img_from_date"});
 Calendar.setup({inputField:"end", ifFormat:"%m/%d/%Y", button:"img_to_date"});
</script>