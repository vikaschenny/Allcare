<section class= "navs">
  <nav class="navbar navbar-default navbar-fixed-top" role="navigation">
     <div style="background-color:#e7e7e7 !important; height:39px; padding-top:4px; padding-right:20px;"><a  class="btn btn-default btn-sm" data-toggle="modal" data-target="#logout" style="color:black; float:right;"><span class="glyphicon glyphicon-lock"></span>  Logout</a></div>
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

                <!-- Collect the nav links, forms, and other content for toggling -->
                    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                        <?php  
                         $sql_vis=sqlStatement("SELECT provider_menus from tbl_user_custom_attr_1to1 where userid=".$id['id']."");
                          $row1_vis=sqlFetchArray($sql_vis);
                          if(!empty($row1_vis)) {
                                    $avail3=explode("|",$row1_vis['provider_menus']);

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
                                                     if(sqlNumRows($sql_li) != 0 ){ ?>
                                                         <li class="dropdown <?php if($row11['option_id']==$pagename){ ?>active <?php }else{ ?>underline<?php } ?>"><a class="dropdown-toggle" data-toggle="dropdown" href="javascript:DoPost('<?php echo $row_lis['notes']; ?>','<?php echo $provider;  ?>')"><?php echo $row_lis['title']; ?> <b class="caret"></b></a>
                                                         <ul class="dropdown-menu">
                                                            <?php while($row_li=sqlFetchArray($sql_li)){ 
                                                                  if(in_array($row_li['option_id'], $avail3)){  
                                                                  $ex=explode("_",$row_li['option_id']); 
                                                                    if(count($ex)==2){
                                                                       $sub1=$ex[0]."_".$ex[1];
                                                                       $sql_sub=sqlStatement("SELECT * FROM  `list_options` WHERE list_id ='AllCareProviderPortal' AND option_id = '$sub1' ORDER BY seq");
                                                                       $row_sub=sqlFetchArray($sql_sub); ?>
                                                                         <li><a href="javascript:DoPost('<?php echo $row_sub['notes']; ?>','<?php echo $provider;  ?>')"><span><?php  echo $row_sub['title']; ?></span></a> 
                                                                                   </li>    
                                                                <?php }
                                                                  }  } ?>
                                                        </ul></li>
                                                    <?php }else{
                                                          if($row11['option_id']==$pagename){?>
                                                              <li class="active"><a href="javascript:DoPost('<?php echo $row11['notes']; ?>','<?php echo $provider;  ?>')"><span><?php echo $row11['title']; ?></span></a></li>
                                                    <?php   }else { ?>
                                                              <li class="underline"><a href="javascript:DoPost('<?php echo $row11['notes']; ?>','<?php echo $provider;  ?>')"><span><?php echo $row11['title']; ?></span></a></li>
                                                   <?php   }

                                                    }
                                                }
                                                }
                                             }
                                        } } ?>  
                                    </ul>
                            <?php   }  ?>

                    </div><!-- navbar-collapse -->
                </div><!-- container-fluid -->
        </nav>

        <div class="modal fade" id="logout" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-sm">
              <div class="modal-content">
                <div class="modal-header"><h4>Logout <i class="fa fa-lock"></i></h4></div>
                <div class="modal-body"><i class="fa fa-question-circle"></i> Are you sure you want to log-off?</div>
                <div class="modal-footer"><a href="logout_page.php" class="btn btn-primary btn-block" >Logout</a><a href="javascript:void" class="btn btn-primary btn-block" data-dismiss="modal">Cancel</a></div>
              </div>
            </div>
         </div>
</section>
<script>
$(function(){
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
    $('.modal').on('show.bs.modal', reposition);
    // Reposition when the window is resized
    $(window).on('resize', function() {
        $('.modal:visible').each(reposition);
    });
        
})
</script>
