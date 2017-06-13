<nav class="navbar navbar-custom navbar-fixed-top" role="navigation">
    <div class="top-area">
        <div class="container-fluid">
            <div class="pull-right userlogout">
                <ul class="pull-right nav " style="list-style: outside none none;">
                <li class="dropdown user-dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown">Welcome, <?php echo $_SESSION['portal_username']; ?> <b class="caret"></b></a>
                    <ul class="dropdown-menu">
                       <li><a href="#" id="helpop"><i class="glyphicon glyphicon-question-sign"></i> Help</a></li>
                       <li><a href="<?php echo $base_url3; ?>/agencies/logout_page.php?logout=1"><i class="glyphicon glyphicon-off"></i> Logout</a></li>
                    </ul>
                </li>
                </ul>
          </div>
        </div>    
     </div>
        <div class="container-fluid navigation">
            <div class="navbar-header page-scroll">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-main-collapse">
                    <i class="fa fa-bars"></i>
                </button>
                <a class="navbar-brand" href="home.php">
<!--                    <img src="img/logo.png" alt="" width="150" height="40" />-->
                    <?php
                    $base_url = "//".$_SERVER['SERVER_NAME'].'/agencies/';
                    echo "<h4>Agency Portal</h4>"; 
                    ?>
                </a>
            </div>
            
            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse navbar-right navbar-main-collapse">
                <?php 
                
                $sql_vis=sqlStatement("SELECT agencymenu from tbl_addrbk_custom_attr_1to1 where addrbk_type_id=".$_SESSION['uid']);
                $row1_vis=sqlFetchArray($sql_vis);
                if(!empty($row1_vis)) {
                    $avail3=explode("|",$row1_vis['agencymenu']);
                    $sql12=sqlStatement("SELECT * FROM  `list_options` WHERE list_id ='AllCareAgencyPortal'   ORDER BY seq");
                    echo  "<ul class='nav navbar-nav'>";
                    while($row11=sqlFetchArray($sql12)){
                        $mystring = $row11['option_id'];
                        $notes=$base_url.$row11['notes'];
                        if(in_array($mystring, $avail3)){
                           if($mystring==$page_id){ ?>
                                <li class="active"><a href="javascript:DoPost('<?php echo $notes; ?>','<?php echo $_SESSION['uid'];  ?>','<?php echo $_SESSION['portal_username'];  ?>')"><span><?php  echo $row11['title']; ?></span></a></li> 
                                 
                            <?php }else{ 
                                // echo "<li><a href='$notes'>".$row11['title']."</a></li>"; ?>
                                <li><a href="javascript:DoPost('<?php echo $notes; ?>','<?php echo $_SESSION['uid'];  ?>','<?php echo $_SESSION['portal_username'];  ?>')"><span><?php  echo $row11['title']; ?></span></a></li> 
                            <?php }
                        }
                    }
                    echo "</ul>";
                }
                ?>
<!--                <ul class="nav navbar-nav" id="agencyMenu">
                    <li class="active"><a href="#intro">Home</a></li>
                    <li><a href="<?php echo $base_url; ?>drive_view/ggl_drive_folders.php">Drive View</a></li>
                    <li><a href="#doctor">Doctors</a></li>
                    <li><a href="#facilities">Facilities</a></li>
                    <li><a href="#pricing">Pricing</a></li>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="badge custom-badge red pull-right">Extra</span>More <b class="caret"></b></a>
                        <ul class="dropdown-menu">
                            <li><a href="home.php">Home CTA</a></li>
                            <li><a href="index-form.html">Home Form</a></li>
                            <li><a href="index-video.html">Home video</a></li>
                        </ul>
                    </li>
                </ul>-->
            </div>
            <!-- /.navbar-collapse -->
        </div>
        <!-- /.container -->
    </nav>
<script>
   //to matain session in all pages 
   function DoPost(page_name, aid,aname) {
       
        method = "post"; // Set method to post by default if not specified.
        var form = document.createElement("form");
        form.setAttribute("method", method);
        form.setAttribute("action", page_name);
        var key1='agency_id';
        var hiddenField1 = document.createElement("input");
        hiddenField1.setAttribute("type", "hidden");
        hiddenField1.setAttribute("name", key1);
        hiddenField1.setAttribute("value", aid);
        form.appendChild(hiddenField1);
        
        var key='agency_name';
        var hiddenField = document.createElement("input");
        hiddenField.setAttribute("type", "hidden");
        hiddenField.setAttribute("name", key);
        hiddenField.setAttribute("value", aname);

        form.appendChild(hiddenField);
        document.body.appendChild(form);
        form.submit();
    } 
</script>    