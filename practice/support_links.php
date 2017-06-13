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



require_once("verify_session.php");

if($_POST['user'] != ''){
    $uid = $_POST['user'];
}else {
    $uid = $_SESSION['uid'];
}
$pagename = "support"; 




$provider                       = $_REQUEST['provider'];

//for logout
$refer                          = $_REQUEST['refer'];
$_SESSION['refer']              = $_REQUEST['refer'];
$_SESSION['portal_username']    = $_REQUEST['provider'];

$sql    = sqlStatement("SELECT id, fname, lname, specialty FROM users " .
            "WHERE active = 1 AND ( info IS NULL OR info NOT LIKE '%Inactive%' ) " .
            "AND authorized = 1 AND username='".$provider."'" .
            "ORDER BY lname, fname");
$id         = sqlFetchArray($sql);
$patient    = $_REQUEST['form_patient'];
?>

<!DOCTYPE html>

<html>

	<head>
            <meta charset="UTF-8">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1 maximum-scale=1, user-scalable=no">
            <title>HealthCare</title>
	    <link href='https://fonts.googleapis.com/css?family=Abel' rel='stylesheet' type='text/css'>
	    <link href='https://fonts.googleapis.com/css?family=Roboto:400,300,500' rel='stylesheet' type='text/css'>
	    <link href='https://fonts.googleapis.com/css?family=Dosis:300,400,500,600' rel='stylesheet' type='text/css'>
            <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">
            <link rel="stylesheet" type="text/css" href="assets/css/font-awesome.min.css">
            <link rel="stylesheet" type="text/css" href="assets/css/main.css">
            <link rel="stylesheet" type="text/css" href="assets/css/customize.css">
            <link href='https://fonts.googleapis.com/css?family=Roboto+Condensed:400,300' rel='stylesheet' type='text/css'>
            <script type="text/javascript" src="assets/js/jquery.min.js"></script>
            <script type="text/javascript" src="https://code.jquery.com/ui/1.9.2/jquery-ui.js"></script>
            <style>
              @media screen and (max-width: 767px) {

                    main#content {
                      margin-top: 65px;
                      transition: all ease-out 0.3s;
                    }

                }
                .navbar-nav > li > .dropdown-menu{
                    margin-top: 4px !important;
                }
                #services {
                    margin-bottom: -45px;
                }
            </style>

 <script type='text/javascript'>
     
    function DoPost(page_name, provider,refer) {
        method = "post"; // Set method to post by default if not specified.
        var form = document.createElement("form");
        form.setAttribute("method", method);
        form.setAttribute("action", page_name);
        var key='provider';
        var hiddenField = document.createElement("input");
        hiddenField.setAttribute("type", "hidden");
        hiddenField.setAttribute("name", key);
        hiddenField.setAttribute("value", provider);
        form.appendChild(hiddenField);
        var key1='refer';
        var hiddenField1 = document.createElement("input");
        hiddenField1.setAttribute("type", "hidden");
        hiddenField1.setAttribute("name", key1);
        hiddenField1.setAttribute("value", refer);
        form.appendChild(hiddenField1);
        document.body.appendChild(form);
        form.submit();
    }
    function win1(url){

        window.open(url,'popup','width=900,height=900,scrollbars=no,resizable=yes');
    }
    
</script>
</head>
    <body>
        <?php include 'header_nav.php'; ?>
        <section id= "services">
            <div class= "container-fluid">
		<div class= "row">
                    <div class= "col-lg-12 col-sm-12 col-xs-12" style='padding-top:100px !important;'>
                        
			<?php 
                         $sql2=sqlStatement("select * from list_options where list_id='AllCareProviderPortal' AND option_id='support'");
                         $row3=sqlFetchArray($sql2);
                         echo "<h3>"; echo $row3['title']; echo "</h3>";
                        $sql_vis=sqlStatement("SELECT support_links from tbl_user_custom_attr_1to1 where userid='".$id['id']."'");
                        $row1_vis=sqlFetchArray($sql_vis);
                        if(!empty($row1_vis)) {
                             $links=explode("|",$row1_vis['support_links']);
                           
                        }
                        $sql=sqlStatement("select * from list_options where list_id='AllcareSupportLinks' ORDER BY seq ASC");
                        while($row=sqlFetchArray($sql)) { if(in_array($row['option_id'],$links)) { ?>  
                        <a href="<?php echo $row['notes']; ?>" target="_blank"><?php echo $row['title']; ?></a><br><?php } }?>
                    </div>
		</div>
                 <div><br><br></div>
            </div>
	</section>
        <?php include 'footer.php'; ?>
        <script type="text/javascript" src="assets/js/bootstrap.min.js"></script>        
        <script>
            $( function() {
                $("#help_dialog").draggable({ handle:'#header'});
                $("#services").css("min-height",window.innerHeight+"px");
            });
            
        </script>
    </body>
    <script type="text/javascript">

        var _gaq = _gaq || [];
        _gaq.push(['_setAccount', 'UA-36251023-1']);
        _gaq.push(['_setDomainName', 'jqueryscript.net']);
        _gaq.push(['_trackPageview']);

        (function() {
          var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
          ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'https://www') + '.google-analytics.com/ga.js';
          var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
        })();

</script>
</html>
