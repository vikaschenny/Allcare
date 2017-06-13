<section class= "navs">
  <nav class="navbar navbar-default navbar-fixed-top" role="navigation">
      <div style="background-color:#e7e7e7 !important; height:39px; padding-top:4px; padding-right:15px;"><div style="float:right !important;" ><?php if($_SESSION['refer']==''){ echo $id['fname']." ".$id['lname']; }else { echo $_SESSION['refer'] .' as '.$id['fname']." ".$id['lname']; } echo "&nbsp;&nbsp;" ?><a  class="btn btn-default btn-sm" data-toggle="modal" data-target="#logout" style="color:black; "><span class="glyphicon glyphicon-lock"></span>  Logout</a> <a  class="btn btn-default btn-sm" id="helpop" style="color:black; "><span class="glyphicon glyphicon-question-sign"> </span>  Help</a></div></div>
                <div class="container-fluid">
                <!-- Brand and toggle get grouped for better mobile display -->
                <div class="navbar-header">
                        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                                <span class="sr-only">Toggle navigation</span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                        </button>
                        <?php $sql=sqlStatement("select * from globals where gl_name='openemr_name'");
                              $row1=sqlFetchArray($sql);?>
                        <a class="navbar-brand logo" href="home.php">
                               <?php echo $row1['gl_value']; ?>
                        </a>
                </div>
              <?php  $base_url3="https://".$_SERVER['SERVER_NAME'];  ?>
                <!-- Collect the nav links, forms, and other content for toggling -->
                    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                        <?php  
                         $sql_vis=sqlStatement("SELECT provider_menus from tbl_user_custom_attr_1to1 where userid=".$usr_id."");
                          $row1_vis=sqlFetchArray($sql_vis);
                         
                          if(!empty($row1_vis)) {
                                    $avail3=explode("|",$row1_vis['provider_menus']);
                                    $count = 0;
                                    $lislength = count($avail3);
                                     $sql12=sqlStatement("SELECT * FROM  `list_options` WHERE list_id ='AllCareProviderPortal'   ORDER BY seq");?>
                                     <ul class='nav navbar-nav navbar-right'>
                                        <?php while($row11=sqlFetchArray($sql12)){
                                             if(in_array($row11['option_id'], $avail3)){
                                             $mystring = $row11['option_id'];
                                             $pos = strpos($mystring, '_');
                                             if(false == $pos) {
                                                $sql_lis=sqlStatement("SELECT * FROM  `list_options` WHERE list_id ='AllCareProviderPortal' AND option_id = '$mystring' ORDER BY seq");
                                                while($row_lis=sqlFetchArray($sql_lis)){
                                                    if(in_array($row_lis['option_id'], $avail3)){
                                                     $opt_id=$row_lis['option_id']."_";
                                                     $sql_li=sqlStatement("SELECT * FROM  `list_options` WHERE list_id ='AllCareProviderPortal' AND option_id LIKE '%$opt_id%' ORDER BY seq");
                                                     $count++;
                                                     $lislength = $lislength - sqlNumRows($sql_li);
                                                     if(sqlNumRows($sql_li) != 0 ){ 
                                                         $dropdownclass = $lislength==$count?'dropdown-menu-right': 'dropdown-menu-left';
                                                         ?>
                                                         <li class="dropdown <?php if($row11['option_id']==$pagename){ ?>active <?php }else{ ?>underline<?php } ?>"><a class="dropdown-toggle" data-toggle="dropdown" href="javascript:DoPost('<?php echo $base_url3."/practice/".$row_lis['notes']; ?>','<?php echo $provider;  ?>','<?php echo $_SESSION['refer'];  ?>')"><?php echo $row_lis['title']; ?> <b class="caret"></b></a>
                                                         <ul class="dropdown-menu <?php echo $dropdownclass?>">
                                                            <?php while($row_li=sqlFetchArray($sql_li)){ 
                                                                  if(in_array($row_li['option_id'], $avail3)){  
                                                                  $ex=explode("_",$row_li['option_id']); 
                                                                    if(count($ex)==2){
                                                                       $sub1=$ex[0]."_".$ex[1];
                                                                       $sql_sub=sqlStatement("SELECT * FROM  `list_options` WHERE list_id ='AllCareProviderPortal' AND option_id = '$sub1' ORDER BY seq");
                                                                       $row_sub=sqlFetchArray($sql_sub); ?>
                                                                         <li><a href="javascript:DoPost('<?php echo $base_url3."/practice/".$row_sub['notes']; ?>','<?php echo $provider;  ?>','<?php echo $_SESSION['refer'];  ?>')"><span><?php  echo $row_sub['title']; ?></span></a> 
                                                                                   </li>    
                                                                <?php }
                                                                  }  } ?>
                                                        </ul></li>
                                                    <?php }else{
                                                          if($row11['option_id']==$pagename){?>
                                                              <li class="active"><a href="javascript:DoPost('<?php echo $base_url3."/practice/".$row11['notes']; ?>','<?php echo $provider;  ?>','<?php echo $_SESSION['refer'];  ?>')"><span><?php echo $row11['title']; ?></span></a></li>
                                                    <?php   }else { ?>
                                                              <li class="underline"><a href="javascript:DoPost('<?php echo $base_url3."/practice/".$row11['notes']; ?>','<?php echo $provider;  ?>','<?php echo $_SESSION['refer'];  ?>')"><span><?php echo $row11['title']; ?></span></a></li>
                                                   <?php   }

                                                    }
                                                }
                                                }
                                             }
                                        } } ?>  
                                    </ul>
                            <?php   }  ?>

                    </div><!-- navbar-collapse 
                </div><!-- container-fluid -->
        </nav>
    <script>
        function showloader(){
            $('.helploader').hide();
        };
   </script>
        <div id="help_dialog" class="help_dialog" style="display: none;">
            <div id="header" class="help-header">
                <div class="help-header-row">
                    <div class="lineheader">
                        <button class="help-header-previousIcon help-previousIcon"></button>
                        <h1 class="help-header-title" id="header-title">Help</h1>
                    </div>
                    <button aria-label="Close dialog" class="help-header-closeIcon help-closeIcon"></button>
                </div>
                <div class="help-header-searchRow">
                    <div class="help-header-searchFormContainer">
                        <input type="text" autocomplete="off" placeholder="Search Help" class="help-header-searchBox" id="search-box" style="outline: medium none;">
                        <div class="help-header-searchIcon help-searchIcon helpv-loaded"></div>
                    </div>
                </div>
            </div>
            <div id="content-container" class="help-content"  style="display: block;">
                <div id="content-view">
                    <div id="helppages">
                        <h4 id="help-searchResults-title" class="help-card-title">Help Links</h4>
                        <ul class="help-card-list">
                        </ul>
                    </div>
                </div>
                <div class="helploader"><div class="helpdocloader"></div></div>
            </div>
        </div>
    
        <div class="modal fade" id="logout" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-sm">
              <div class="modal-content">
                <div class="modal-header"><h4>Logout <i class="fa fa-lock"></i></h4></div>
                <div class="modal-body"><i class="fa fa-question-circle"></i> Are you sure you want to log-off?</div>
                <div class="modal-footer"><a href="<?php echo $base_url3; ?>/practice/logout_page.php?provider=<?php echo $provider ; ?>&refer=<?php echo $refer; ?>" class="btn btn-primary btn-block" >Logout</a><a href="javascript:void" class="btn btn-primary btn-block" data-dismiss="modal">Cancel</a></div>
              </div>
            </div>
         </div>
    
</section>
<script>
    var linkurl = linkurl || "helplinks.php";
    var uiinsert = uiinsert || "in";
    var isdrage =  isdrage || true;
   /*var uilib = document.createElement("script");
   uilib.type = "text/javascript";
   uilib.src = "https://code.jquery.com/ui/1.9.2/jquery-ui.js";
   if(uiinsert  == "in")
     document.head.appendChild(uilib);*/
   
$(function(){
    function ajaxcall(url,data,type,callback,errorcallback){
        $.ajax({url:url,type:type,xhrFields: {withCredentials: true},data:data, crossDomain: true,error:errorcallback,success:callback});
     }

    $("#helpop").click(function(event){
        //$("#help_dialog").draggable({ handle:'#header'});
        $('#help_dialog').show();
        $('.help-header-searchRow').show();
        $('.help_dialog').find("#content-container").height(sethightofcontent(100,48,$('.help_dialog').find("#header").height()));
        $(".helploader").show();
        ajaxcall(linkurl,null,"post",function(data){
            console.log(data);
            var $data = JSON.parse(data);
            var helpcards = "";
            $.each($data,function(index,value){
               helpcards +='<li class="help-card-listItem">\n\
                                <a class="help-iconTextComponent" data-href="'+value.helplink+'">\n\
                                    <span class="help-iconTextComponent-icon help-articleIcon"></span>\n\
                                    <span class="help-iconTextComponent-label">'+value.title+'</span>\n\
                                </a>\n\
                            </li>';
            });

            $('.help-card-list').html(helpcards);

            $(".helploader").hide();
        },function(error){
            $(".helploader").hide();
        });
    });
    $('.help-header-closeIcon').click(function(){
        $('#help_dialog').hide();
        $('.help_dialog').find("#content-container").height(0);
        $('#helpdocs').remove();
        $('.help-previousIcon').hide();
    });
    $(document).on("click",".help-iconTextComponent",function(evt){
       evt.preventDefault();
       $('<iframe src="" id="helpdocs" class="help-contentFrame" onload="showloader()" height="100%"></iframe>').insertAfter('#content-view')
       $('.help-card-list').html("");
       $('.help-header-searchRow').hide();
       $(".helploader").show();
       $('.help_dialog').find("#content-container").height(sethightofcontent(100,48,$('.help_dialog').find("#header").height()));
       $('#helpdocs').prop("src",$(this).data('href')).show();
       $('.help-previousIcon').show();
    });
    $('.help-previousIcon').click(function(){
        $('.help-header-searchRow').show();
        $('#helpdocs').remove();
        $(this).hide();
        $(".helploader").show();
        $('.help_dialog').find("#content-container").height(sethightofcontent(100,48,$('.help_dialog').find("#header").height()));
         ajaxcall(linkurl,null,"post",function(data){
            var $data = JSON.parse(data);
            var helpcards = "";
            $.each($data,function(index,value){
               helpcards +='<li class="help-card-listItem">\n\
                                <a class="help-iconTextComponent" data-href="'+value.helplink+'">\n\
                                    <span class="help-iconTextComponent-icon help-articleIcon"></span>\n\
                                    <span class="help-iconTextComponent-label">'+value.title+'</span>\n\
                                </a>\n\
                            </li>';
            });

            $('.help-card-list').html(helpcards);

            $(".helploader").hide();
        },function(error){
            $(".helploader").hide();
        });
    });

    $("#search-box").on("keyup",function(){
        var inputval = $(this).val().trim();
        $('.help-iconTextComponent').hide();
        $('.help-iconTextComponent').filter(function(){
            var patt = new RegExp(inputval,"i");
            var res = patt.test($(this).find('.help-iconTextComponent-label').text());
            return res;
        }).show();

    });

    function sethightofcontent(topmargin,footermargin,penalheaderhight){                    
        var contentcontenarheight = (window.innerHeight - (topmargin + footermargin + penalheaderhight));
        return contentcontenarheight;
    }
                
    function reposition() {
        var modal = $(this),
            dialog = modal.find('.modal-dialog');
            modal.css('display', 'block');

        // Dividing by two centers the modal exactly, but dividing by three 
        // or four works better for larger screens.
        /*alert($(window).height() + " " + window.innerHeight + " " + self.innerHeight + " " + parent.innerHeight + " " + top.innerHeight);*/
        dialog.css("margin-top", Math.max(0, (window.innerHeight - dialog.height()) / 2));
    }
    // Reposition when a modal is shown
    $('#logout').on('show.bs.modal', reposition);
    // Reposition when the window is resized
    $(window).on('resize', function() {
        $('#logout:visible').each(reposition);
    });
        
})
</script>
