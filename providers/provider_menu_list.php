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
//SANITIZE ALL ESCAPES
$sanitize_all_escapes=true;

//STOP FAKE REGISTER GLOBALS
$fake_register_globals=false;

require_once("../interface/globals.php");
require_once("../library/formdata.inc.php");
require_once("../library/globals.inc.php");

$provider=$_REQUEST['provider_id'];
$sql=sqlStatement("SELECT id, fname, lname, specialty FROM users " .
      "WHERE active = 1 AND ( info IS NULL OR info NOT LIKE '%Inactive%' ) " .
      "AND authorized = 1 AND username='$provider'" .
      "ORDER BY lname, fname");
$id=sqlFetchArray($sql);

?>
<html>
    <head>
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
        <script>
          $(document).ready(function(){
            $(function(){
                $('li a').click(function(){
                 $('li a').each(function(a){
                   $( this ).removeClass('selectedclass')
                 });
                 $( this ).addClass('selectedclass');
               });

              $('ul a').click(function(){
                 $('ul a').each(function(a){
                  $( this ).removeClass('selectedclass')
                 });
                 $( this ).addClass('selectedclass');
               });
           });
        });   
        $(function() { 
    $('#nav').on('click','.nav', function ( e ) {
        e.preventDefault();
        $(this).parents('#nav').find('.active').removeClass('active').end().end().addClass('active');
        $(activeTab).show();
    });
});
        </script>
    <style>
    body {

      background: -webkit-radial-gradient(circle, white, #638fd0);
      background: -moz-radial-gradient(circle, white, #638fd0);
    }

    #tabs {
            float:left;
            width:100%;
    /*	background:#F4F7FB;*/
            font-size:100%;
            line-height:normal;
/*            border-bottom:1px solid #BCD2E6;*/
            }
    #tabs ul {
            margin:0;
            padding:10px 10px 0 50px;
            list-style:none;
            }
    #tabs li {
            display:inline;
            margin:0;
            padding:0;
            }
    #tabs a {
            float:left;
            background:url("tableft.gif") no-repeat left top;
            margin:0;
            padding:0 0 0 4px;
            text-decoration:none;
            }
    #tabs a span {
            float:left;
            display:block;
            background:url("tabright.gif") no-repeat right top;
            padding:5px 15px 4px 6px;
            color:#627EB7;
            }
    /* Commented Backslash Hack hides rule from IE5-Mac \*/
    #tabs a span {float:none;}
    /* End IE5-Mac hack */
    #tabs a:hover span {
            color:#627EB7;
            }
    #tabs a:hover {
            background-position:0% -42px;
            }
    #tabs a:hover span {
            background-position:100% -42px;
            }  
            

    h2 {
      display: block;
      font-size: 1.5em;
      -webkit-margin-before: 0.83em;
      -webkit-margin-after: 0.83em;
      -webkit-margin-start: 0px;
      -webkit-margin-end: 0px;

    }
    .center {
        margin: auto;
        width: 80%;
        padding: 10px;
    }
#tabs ul li a.selectedclass
{
  color:red;
  background-color:black;
  background:url("tabright_blue.gif") no-repeat right top !important;
}
    </style>
</head>
 <body  bgcolor="#638fd0">
      
     <span><div style='position:relative;' ><h2 style='font-style: italic; color: gray; float:left;'>Provider Portal</h2> <h3 style='font-style: italic; color: gray; float:right;'>welcome:<?php echo $id['fname']." ".$id['lname']; ?></h3>  <br><p style='font-style: italic; color: gray;  padding-top:10px;  position:absolute;
    bottom:0;  right:0; top:20px;'><a href='logout_page.php' target="_top">Log Out</a></p></div></span>
  
<div id="tabs">
  <ul><?php $sql12=sqlStatement("SELECT * FROM  `list_options` WHERE list_id =  'AllCareProviderPortal' ORDER BY seq"); 
             while($row11=sqlFetchArray($sql12)){ ?>
                 <li><a href="<?php echo $row11['notes']; ?>" target="content"><span><?php echo $row11['title']; ?></span></a></li>
             <?php } ?>
 
  </ul>
</div>
 </body>

</html>
