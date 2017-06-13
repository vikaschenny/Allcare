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

 $_POST['string']; 
 $provider=$_REQUEST['provider'];
$sql=sqlStatement("SELECT id, fname, lname, specialty FROM users " .
      "WHERE active = 1 AND ( info IS NULL OR info NOT LIKE '%Inactive%' ) " .
      "AND authorized = 1 AND username='".$provider."'" .
      "ORDER BY lname, fname");
$id=sqlFetchArray($sql);
?>

<!DOCTYPE html>

<html>

	<head>
		<meta charset="UTF-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
	    <meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="shortcut icon" href="img/season-change.jpg" type="image/x-icon">
		<title>HealthCare</title>

		
	    <link href='http://fonts.googleapis.com/css?family=Abel' rel='stylesheet' type='text/css'>
	    <!-- <link href='http://fonts.googleapis.com/css?family=Pontano+Sans' rel='stylesheet' type='text/css'>
	    <link href='http://fonts.googleapis.com/css?family=Alegreya+Sans:300,400,500,700' rel='stylesheet' type='text/css'> -->
	    <link href='http://fonts.googleapis.com/css?family=Roboto:400,300,500' rel='stylesheet' type='text/css'>
	    <link href='http://fonts.googleapis.com/css?family=Dosis:300,400,500,600' rel='stylesheet' type='text/css'>
	    
	    
		<link rel="stylesheet" type="text/css" href="assets/css/animate.css">
		<link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">
		<link rel="stylesheet" type="text/css" href="assets/css/owl.carousel.css">
		<link rel="stylesheet" type="text/css" href="assets/css/owl.theme.css">
		<link rel="stylesheet" type="text/css" href="assets/css/owl.transitions.css">
		<link rel="stylesheet" type="text/css" href="assets/css/font-awesome.min.css">
		<link rel="stylesheet" type="text/css" href="assets/css/main.css">
		<link href='http://fonts.googleapis.com/css?family=Roboto+Condensed:400,300' rel='stylesheet' type='text/css'>
                <!--  datatable-->
<!--                <link rel="stylesheet" href="http://netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css"/>
                <link rel="stylesheet" href="http://netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap-theme.min.css"/>
-->             <link rel="stylesheet" href="css/dataTables.bootstrap.css"/>
                <link rel="stylesheet" href="css/datatables.responsive_bootstrap.css"/>
                <script src="js/responsive_datatable/jquery.min.js"></script>
                <script src="js/responsive_datatable/jquery.dataTables.min.js"></script>
                <script src="js/responsive_datatable/dataTables.bootstrap.js"></script>
                <script src="js/responsive_datatable/datatables.responsive.js"></script>
                 <link rel="stylesheet" href="fancybox/source/jquery.fancybox.css" type="text/css" media="screen" />
<script type="text/javascript" src="fancybox/source/jquery.fancybox.pack.js"></script>
                <style>
/*                    #menu {
  text-align: center;
  transition: all ease-out 0.3s;
}*/

#menu a { color: white; }

#menu ul {
  margin: 0;
  padding: 0;
  background-color:#46A1B4;
}

#menu ul li {
  display: inline-block;
  position: relative;
}

#menu ul li > a {
  display: inline-block;
  padding: 10px;
}
#menu ul li.active > a {
/*  background: #ffffff;*/
  color: #000000;
  background-color:#ffffff;
  border-top:6px ridge #ffffff;
}
#menu ul li > a > i {
  margin-left: 15px;
  transition: all ease-out 0.3s;
  -webkit-transition: all ease-out 0.1s;
}

#menu ul li ul {
  display: none;
  position: absolute;
  top: 38px;
  width: 200px;
  text-align: left;
}

#menu ul li ul li { display: block; }

#menu ul li ul li a { display: block; }

#menu ul li:hover > a { background-color: rgba(0, 0, 0, 0.3); }

#menu ul li:hover > a > i { transform: rotateZ(90deg); }

#menu ul li:hover ul { display: block; }

/*a#toggle {
  position: fixed;
  top: 10px;
  left: 10px;
  width: 40px;
  height: 40px;
  background-color: #46A1B4;
  text-align: center;
  color: white;
  display: none;
  transition: all ease-out 0.3s;
   float: right;
}

a#toggle i {
  position: relative;
  top: 50%;
  transform: translateY(-50%);
}*/
@media screen and (max-width: 767px) {

/*a#toggle { display: block; }*/

main#content {
  margin-top: 65px;
  transition: all ease-out 0.3s;
}

/*#menu {
  position: fixed;
 // width: 250px;
  height: 100%;
  top: 0;
  left: 0;
  overflow: hidden;
  overflow-y: auto;
  background-color: transparent;
  transform: translateX(-250px);
}*/

#menu ul {
  text-align: left;
  background-color: #46A1B4;
}

#menu ul li { display: block; }

#menu ul li a { display: block; }

#menu ul li a > i { float: right; }

#menu ul li ul {
  display: none;
  position: static;
  width: 100%;
 background-color: rgba(22,160,133, 0.8);
}

#menu ul li:hover > ul { display: none; }

#menu ul li:hover > a > i { transform: rotateZ(0); }

#menu ul li.open > a { background-color: rgba(0, 0, 0, 0.3); }

#menu ul li.open > a > i { transform: rotateZ(90deg); }

#menu ul li.open > ul { display: block; }

/*div#overlay {
  display: block;
  visibility: hidden;
  position: fixed;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.8);
  transition: all ease-out 0.3s;
  z-index: 1;
  opacity: 0;
}

html.open-menu { overflow: hidden; }

html.open-menu div#overlay {
  visibility: visible;
  opacity: 1;
  width: calc(-150%);
  left: 250px;
}

html.open-menu a#toggle,
 html.open-menu main#content { transform: translateX(250px); }

html.open-menu nav#menu {
  z-index: 3;
  transform: translateX(0);
}*/
}
/* drag and drop               */
ul#slippylist{
    width:120px;
    height:80px;
    padding-left: 0px !important;
}
ul#slippylist li {
    user-select: none;
    -webkit-user-select: none;
/*    border 1px solid lightgrey;*/
    list-style: none;
/*    height: 25px;*/
/*    max-width: 200px;*/
    cursor: move;
    margin-top: -1px;
    margin-bottom: 0;
    padding-right:50px;
    padding-left:7px;
    font-weight: bold;
    color: black;
    text-align:left;
}
    ul#slippylist li.slip-reordering {
        box-shadow: 0 2px 10px rgba(0,0,0,0.45);
    }
  </style>
  <script>function divclick(cb, divid) {
                         var divstyle = document.getElementById(divid).style;
                         if (cb.checked) {
                          divstyle.display = 'block';
                         } else {
                          divstyle.display = 'none';
                         }
                         return true;
                        }</script> 
  <script type='text/javascript'>
function win1(url){
     // alert(url);
    window.open(url,'popup','width=900,height=900,scrollbars=yes,resizable=yes');
} 
			$(document).ready(function() {
                           
                             
                              $("#form-horizontal").submit(function(e) {
                                var  provider=document.getElementById('provider').value;
                                var menu='plist';
                              // alert(document.getElementById('provider').value);    
                               var order = []; var string1='';
                                $( ".order" ).each(function( index ) {
                                  console.log( index + ": " + $( this ).text() );
                                  //order =+index + ": " + $( this ).text() + ",";
                                  string1 += index + ":" +$( this ).text()+",";
                                 });
                          
                              // alert(string1);
                            //var string=string1.trim(",");
                         var string= string1.substring(0,string1.lastIndexOf(","));
                          // alert(string);
                           
//                                jQuery.ajax({
//                                        type: 'POST',
//                                        url: "providers_patient.php",	
//                                        data: {
//                                               
//                                                string:string
//                                            },
//
//                                        success: function(response)
//                                        {
//                                            //alert(string);
//                                            alert(response);
//                                          
//                                        },
//                                        failure: function(response)
//                                        {
//                                                alert("error"); 
//                                        }
//                                     
//                                });
//                                
                                var request = $.ajax({
                                  url: "fields_order.php",
                                  method: "POST",
                                  data: { string : string,
                                          provider: provider,
                                          menu:menu
                                        }
                                 
                                });

                                request.done(function( msg ) {
                                 //alert("sucess" + msg);
                                });

                                request.fail(function( jqXHR, textStatus ) {
                                  alert( "Request failed: " + textStatus); 
                                 
                                });
                            
                          });
                        });
                        
		</script>
                 <script language="javascript"> 

           function DoPost(page_name, provider) {
                method = "post"; // Set method to post by default if not specified.

              // alert(provider);

                var form = document.createElement("form");
                form.setAttribute("method", method);
                form.setAttribute("action", page_name);
                var key='provider';
//                for(var key in params) {
//                    if(params.hasOwnProperty(key)) {
                        var hiddenField = document.createElement("input");
                        hiddenField.setAttribute("type", "hidden");
                        hiddenField.setAttribute("name", key);
                        hiddenField.setAttribute("value", provider);

                        form.appendChild(hiddenField);
//                     }
                //}

               document.body.appendChild(form);
                form.submit();
        }
        </script>       
	</head>

	<body>
           
		<section class= "navs">
			<nav class="navbar navbar-default navbar-fixed-top" role="navigation" id="menu">
                              <div style="background-color:#e7e7e7 !important; height:20px; padding-right:20px;"><a style="color:black; float:right;" href="logout_page.php">Logout</a></div>
  				<div class="container-fluid">
    				<!-- Brand and toggle get grouped for better mobile display -->
    				<div class="navbar-header">
      					<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
					        <span class="sr-only">Toggle navigation</span>
					        <span class="icon-bar"></span>
					        <span class="icon-bar"></span>
					        <span class="icon-bar"></span>
	      				</button>
	      				<a class="navbar-brand logo" href="#">
	      					<span><i class="fa fa-stethoscope"></i></span>
	      					HealthCare
	      				</a>
	    			</div>
		
		    		<!-- Collect the nav links, forms, and other content for toggling -->
<!--                                 <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                                     <ul class='navbar-right'>
                                            <li><a href="#">Item 01</a></li>
                                            <li><a href="#">Item 02</a></li>
                                            <li><a href="#">Item 03</a><ul>
                                                <li><a href="#">Submenu 01</a></li>
                                                <li><a href="#">Submenu 02</a></li>
                                                <li><a href="#">Submenu 03</a></li>
                                              </ul></li>

                                            <li><a href="#">Item 04</a>

                                            </li>
                                            <li><a href="#">Item 05</a></li>
                                    </ul>
                                </div>-->
                          <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                               <?php 
                                    $sql_vis=sqlStatement("SELECT provider_menus from tbl_user_custom_attr_1to1 where userid=".$id['id']."");
                                      $row1_vis=sqlFetchArray($sql_vis);
                                      if(!empty($row1_vis)) {
                                            $avail3=explode("|",$row1_vis['provider_menus']);
                                                   
                                                 $sql12=sqlStatement("SELECT * FROM  `list_options` WHERE list_id ='AllCareProviderPortal'  ORDER BY seq");?>
                                                     <ul class='navbar-right'>
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
                                                                         <li <?php if($row11['option_id']=='plist'){ ?> class='active' <?php } ?>><a href="javascript:DoPost('<?php echo $row_lis['notes']; ?>','<?php echo $provider;  ?>')"><span><?php echo $row_lis['title']; ?></span></a>
                                                                         <ul>
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
                                                                    <?php }else{ if($row11['option_id']=='plist'){?>
                                                                             <li class="active"><a href="javascript:DoPost('<?php echo $row11['notes']; ?>','<?php echo $provider;  ?>')"><span><?php echo $row11['title']; ?></span></a></li>
                                                                    <?php }else{ ?>
                                                                             <li><a href="javascript:DoPost('<?php echo $row11['notes']; ?>','<?php echo $provider;  ?>')"><span><?php echo $row11['title']; ?></span></a></li>
                                                                   <?php }
                                                                    }
                                                                 }
                                                             }  
                                                             }
                                                        } } ?>  
                                                    </ul>
                                          <?php } ?>
                                </div>
				</div><!-- container-fluid -->
			</nav>
               </section>
             <section id= "services">
                <div class= "container">
				<div class= "row">
					<div class= "col-lg-12 col-sm-12 col-xs-12">
						<h2 class= "headline text-center">
						</h2>
                                           
                                            <?php
                                                      function display_db_query($query_string,$provider) {
                                                            // perform the database query
                                                            $result_id = mysql_query($query_string)
                                                            or die("display_db_query:" . mysql_error());
                                                            // find out the number of columns in result
                                                            $column_count = mysql_num_fields($result_id)
                                                            or die("display_db_query:" . mysql_error());
                                                            // Here the table attributes from the $table_params variable are added
                                                            echo "<br/>"; ?>
                                                             <?php $display_style='none' ?>
                                                            
                                                            <input type='checkbox' name='filter' id='filter' value='1' onclick='return divclick(this,"filters");'   <?php if ($display_style == 'block') echo " checked"; ?>><b>Filters</b>
                                                            <div id='filters' class="appointment1" style='display:<?php echo $display_style; ?>'>
                                                            <form class="form-horizontal" id="form-horizontal" action="" method="POST">
                                                                <p>Column Names:</p>
                                                                
                                                                <div style='min-height: 200px; width:200px; overflow-y: scroll; border: 1px solid black;'><ul id="slippylist">
                                                              <?php for($column_num = 0; $column_num < $column_count; $column_num++) {
                                                                $field_name = mysql_field_name($result_id, $column_num); ?>
<!--                                                                 <li class='order'> <?php echo $field_name; ?> </li>-->
                                                               <?php  if($field_name!='pid' && $field_name!='age' && $field_name!='Name'){
                                                                                $sql1=sqlStatement("SELECT title,field_id from layout_options where field_id='$field_name' AND form_id='DEM'");
                                                                                while($row1=sqlFetchArray($sql1)){
                                                                                   if($row1['title']!=''){  ?>
                                                                                    <li class='order'><?php echo str_replace(" " ,"_",$row1['title']); ?></li>
                                                                                <?php  }else{ ?>
                                                                                   <li class='order'><?php echo $row1['field_id']; ?></li>
                                                                               <?php    }
                                                                                }
                                                                            }else{
                                                                                if($field_name=='pid'){ ?>
                                                                                    <li class='order'><?php echo "Patient_Id"; ?></li>
                                                                             <?php  }elseif($field_name=='age'){ ?>
                                                                                  <li class='order'><?php echo "Age"; ?></li>
                                                                              <?php }elseif($field_name=='Name'){ ?>
                                                                                  <li class='order'><?php echo "Patient_Name"; ?></li>
                                                                            <?php  }
                                                                               
                                                                            }
                                                              } ?>
                                                              </ul></div>
                                                                
                                                                <input type='hidden' id='provider' name='provider' value='<?php echo $_REQUEST['provider']; ?>' />
                                                                <div align='right'><button type="submit" class="btn btn-default">Submit</button></div>
                                                            </form>
                                                            </div>
                                                              <script src="js/slip.js"></script>
                                                              <script>
                                                                var list = document.getElementById('slippylist');
                                                                new Slip(list);
                                                                list.addEventListener('slip:reorder', function(e) {
                                                                e.target.parentNode.insertBefore(e.target, e.detail.insertBefore);
                                                              });
                                                              </script>
                                                            <?php
                                                            echo "<br><br><br>";
                                                            print("<TABLE  id='patient_data'cellpadding='0' cellspacing='0' border='0' class='table table-bordered table-striped'style=' word-wrap: break-word ; table-layout:fixed !important;  width: 100% !important;'>\n");
                                                            // optionally print a bold header at top of table

                                                                    print("<thead><tr>");
                                                                    for($column_num = 0; $column_num < $column_count; $column_num++) {
                                                                            $field_name = mysql_field_name($result_id, $column_num);
                                                                            if($field_name!='pid' && $field_name!='age' && $field_name!='Name'){
                                                                                $sql1=sqlStatement("SELECT title,field_id from layout_options where field_id='$field_name' AND form_id='DEM'");
                                                                                while($row1=sqlFetchArray($sql1)){
                                                                                   if($row1['title']!=''){ 
                                                                                    echo"<th data-hide='phone' data-name='".$row1['title']."'>"; echo $row1['title']; echo "</th>";
                                                                                   }else{
                                                                                        echo"<th data-hide='phone' data-name='".$row1['title']."'>"; echo $row1['field_id']; echo "</th>";
                                                                                   }
                                                                                }
                                                                            }else{
                                                                                if($field_name=='pid'){
                                                                                     echo"<th  data-hide='phone' data-name='Patient Id'>"; echo "Patient Id"; echo "</th>";
                                                                                }elseif($field_name=='age'){
                                                                                     echo"<th data-hide='phone' data-name='Age'>"; echo "Age"; echo "</th>";
                                                                                }elseif($field_name=='Name'){
                                                                                    echo"<th data-class='expand'>"; echo "Patient Name"; echo "</th>";
                                                                                }
                                                                                
                                                                            }
                                                                    }
                                                                    print("</tr></thead>\n");

                                                            // print the body of the table
                                                            while($row = mysql_fetch_row($result_id)) {
                                                                    print("<tr align=left valign=top>");
                                                                    for($column_num = 0; $column_num < $column_count; $column_num++) {
                                                                         $field_name = mysql_field_name($result_id, $column_num);
                                                                         if($field_name=='pid'){ ?>
<!--                                                                       <td> <a href='javascript:;' onclick=win1('providers_patient_edit.php?set_pid=<?php echo $row[$column_num] ; ?>&provider=<?php echo $provider; ?>') >     
                                                                           <span><?php echo $row[$column_num]; ?></span></a></td>-->
                                                                            <td><a class="various" data-fancybox-type="iframe" href="providers_patient_edit.php?set_pid=<?php echo $row[$column_num] ; ?>&provider=<?php echo $provider; ?>"><?php echo $row[$column_num] ; ?></a></td>
                                                                       <?php  }else{
                                                                              print("<td>$row[$column_num]</td>\n");
                                                                         }
                                                                           
                                                                    }
                                                                    print("</tr>\n");
                                                            }
//                                                            print("<tfoot><tr>");
//                                                                    for($column_num = 0; $column_num < $column_count; $column_num++) {
//                                                                            $field_name = mysql_field_name($result_id, $column_num);
//                                                                            if($field_name!='pid' && $field_name!='age'){
//                                                                                $sql1=sqlStatement("SELECT title,field_id from layout_options where field_id='$field_name' AND form_id='DEM'");
//                                                                                while($row1=sqlFetchArray($sql1)){
//                                                                                   if($row1['title']!=''){ 
//                                                                                    echo"<th>"; echo $row1['title']; echo "</th>";
//                                                                                   }else{
//                                                                                        echo"<th>"; echo $row1['field_id']; echo "</th>";
//                                                                                   }
//                                                                                }
//                                                                            }else{
//                                                                                if($field_name=='pid'){
//                                                                                     echo"<th>"; echo "Patient Id"; echo "</th>";
//                                                                                }elseif($field_name=='age'){
//                                                                                     echo"<th>"; echo "Age"; echo "</th>";
//                                                                                }elseif($field_name=='Name'){
//                                                                                    echo"<th data-class='expand'>"; echo "Patient Name"; echo "</th>";
//                                                                                }
//                                                                                
//                                                                            }
//                                                                    }
//                                                                    print("</tr></tfoot>\n");
                                                            print("</table>\n"); 
                                                    }
                                                    function display_db_table($provider) {
                                                        //echo "select id from users where username='".$provider."'";
                                                          $sql=sqlStatement("select id from users where username='".$provider."'");
                                                          $row=sqlFetchArray($sql);
                                                          $id=$row['id'];
                                                          $sql_vis=sqlStatement("SELECT provider_plist from tbl_user_custom_attr_1to1 where userid='$id'");
                                                          $row1_vis=sqlFetchArray($sql_vis);
                                                          if(!empty($row1_vis)) {
                                                            $avail3=explode("|",$row1_vis['provider_plist']);
                                                                 //echo "<pre>"; print_r($avail3); echo "</pre>";
                                                                  
                                                          $sql1=sqlStatement("SELECT * from tbl_allcare_providers_fieldsorder where username='$provider' AND menu='plist'");
                                                          $row1=sqlFetchArray($sql1);
                                                          if(!empty($row1)){
                                                              $orders=explode(",",$row1['order_of_columns']);
                                                              //echo "<pre>"; print_r($orders); echo "</pre>";
                                                              $field1='';  $fields=array();
                                                              foreach($orders as $value2){
                                                                  $field=explode(":",$value2);
                                                                 
                                                                  if($field[1]!='Patient_Id' && $field[1]!='Patient_Name' && $field[1]!='Age'){
                                                                      $title=str_replace("_"," ",$field[1]);
                                                                      $sql2=sqlStatement("SELECT field_id from layout_options where form_id='DEM' AND title='$title' order by seq, group_name");
                                                                       $row2=sqlFetchArray($sql2);
                                                                      if (in_array($row2['field_id'], $avail3)){
                                                                            $field1.=$row2['field_id'].",";
                                                                      }
                                                                     
                                                                     
                                                                  }elseif($field[1]=='Patient_Id'){
                                                                       if (in_array($field[1], $avail3)){
                                                                             $field1.="fe.pid,";
                                                                       }
                                                                  }elseif($field[1]=='Patient_Name'){
                                                                      if (in_array($field[1], $avail3)){
                                                                        $field1.="CONCAT(title ,'',fname,',',lname) as Name,";
                                                                      }
                                                                  }elseif($field[1]=='Age'){
                                                                       if (in_array($field[1], $avail3)){
                                                                         $field1.="DATE_FORMAT(FROM_DAYS(DATEDIFF(CURDATE(),DOB)), '%Y')+0 AS age,";
                                                                       }
                                                                  }    
                                                               }
                                                                    $field2=rtrim($field1,",");
                                                                    $sql12="SELECT $field2 from form_encounter fe  INNER JOIN patient_data p ON p.pid=fe.pid  INNER JOIN openemr_postcalendar_events o ON o.pc_pid = fe.pid "
                                                                  . " AND o.pc_catid = fe.pc_catid AND fe.facility_id = o.pc_facility
                                                                      AND o.pc_eventDate = DATE_FORMAT( fe.date,  '%Y-%m-%d' )where o.pc_aid =$id group by fe.pid";
                                                          }
                                                          else {
                                                                 $avail=explode("|",$row1_vis['provider_plist']);
                                                                 
                                                                 foreach($avail as $val2){
                                                                     if($val2=='Patient_Id'){
                                                                         $avail_field.="fe.pid,";
                                                                     }elseif($val2=='Patient_Name'){
                                                                         $avail_field.="CONCAT(title ,'',fname,',',lname) as Name,";
                                                                     }elseif($val2=='age'){
                                                                         $avail_field.="DATE_FORMAT(FROM_DAYS(DATEDIFF(CURDATE(),DOB)), '%Y')+0 AS age,";
                                                                     }else{
                                                                         $avail_field.=$val2.",";
                                                                     }
                                                                     
                                                                 }
                                                                 $avail_field2=rtrim($avail_field,",");
//                                                                 $sql12="SELECT fe.pid,CONCAT(title ,'',fname,',',lname) as Name, DATE_FORMAT(DOB,'%m-%d-%Y') AS DOB,DATE_FORMAT(FROM_DAYS(DATEDIFF(CURDATE(),DOB)), '%Y')+0 AS age,if (sex = 'Female' ,'F','M' ) as sex,street,city,state,country_code,postal_code,phone_home,phone_biz,phone_cell,contact_relationship , phone_contact,email from form_encounter fe  INNER JOIN patient_data p ON p.pid=fe.pid  INNER JOIN openemr_postcalendar_events o ON o.pc_pid = fe.pid "
//                                                                  . " AND o.pc_catid = fe.pc_catid AND fe.facility_id = o.pc_facility
//                                                                      AND o.pc_eventDate = DATE_FORMAT( fe.date,  '%Y-%m-%d' )where o.pc_aid =$id group by fe.pid";
                                                                  $sql12="SELECT $avail_field2 from form_encounter fe  INNER JOIN patient_data p ON p.pid=fe.pid  INNER JOIN openemr_postcalendar_events o ON o.pc_pid = fe.pid "
                                                                  . " AND o.pc_catid = fe.pc_catid AND fe.facility_id = o.pc_facility
                                                                      AND o.pc_eventDate = DATE_FORMAT( fe.date,  '%Y-%m-%d' )where o.pc_aid =$id group by fe.pid";
                                                          }
                                                            display_db_query($sql12, $provider);
                                                       }    
                                                    }
                                                display_db_table($provider); ?>
                                                           
					
				</div>
				
				
				</div>
                 <div><br><br></div>
                  
		</section>
          
                <section id="footer">
			<div class="container">
				<div class="row">
					<div class="col-md-6 col-md-offset-3">
						<ul class="text-center contact">
				        	<li class= "socials-icons">
								<a href="#" data-toggle="tooltip" title="Share in Facebook" class="facebook"><i class="fa fa-facebook"></i></a>
							</li>
							<li class= "socials-icons">
								<a href="#" data-toggle="tooltip" title="Share in Twitter" class="twitter"><i class="fa fa-twitter"></i></a>
							</li>
							<li class= "socials-icons">
								<a href="#" data-toggle="tooltip" title="Share in Google +" class="google-plus"><i class="fa fa-google-plus"></i></a>
							</li>
							<li class= "socials-icons">
								<a href="#" data-toggle="tooltip" title="Share in Instagram" class="instagram"><i class="fa fa-instagram"></i></a>
							</li>
							<li class= "socials-icons">
								<a href="#" data-toggle="tooltip" title="Share in Pinterest" class="pinterest"><i class="fa fa-pinterest"></i></a>
							</li>
							<li class= "socials-icons">
								<a href="#" data-toggle="tooltip" title="Connect with Skype" class="skype"><i class="fa fa-skype"></i></a>
							</li>
				      	</ul>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="copy-right-text text-center">
							&copy; Copyright 2014, Your Website Link. Theme by <a href="https://themewagon.com/">ThemeWagon</a>
						</div>
					</div>
				</div>
			</div>
		</section>

		<script type="text/javascript" src="assets/js/jquery.min.js"></script>
		<script type="text/javascript" src="assets/js/owl.carousel.min.js"></script>
		<script type="text/javascript" src="assets/js/isotope.pkgd.min.js"></script>
		<script type="text/javascript" src="assets/js/wow.min.js"></script>
		<script type="text/javascript" src="assets/js/bootstrap.min.js"></script>

		<script>
      		new WOW().init();
		</script>

		<script type='text/javascript'>
                 
			$(document).ready(function() {
                            jQuery.noConflict();
                           jQuery("#starting-slider").owlCarousel({
  					autoPlay: 3000,
      				navigation : false, // Show next and prev buttons
      				slideSpeed : 700,
      				paginationSpeed : 1000,
      				singleItem:true
  				});
   
                             //datatable
                             var responsiveHelper;
                            var breakpointDefinition = {
                                tablet: 1024,
                                phone : 480
                            };
                            var tableElement = $('#patient_data');
                            tableElement.dataTable({
                                autoWidth        : false,
                                preDrawCallback: function () {
                                    // Initialize the responsive datatables helper once.
                                    if (!responsiveHelper) {
                                        responsiveHelper = new ResponsiveDatatablesHelper(tableElement, breakpointDefinition);
                                    }
                                },
                                rowCallback    : function (nRow) {
                                    responsiveHelper.createExpandIcon(nRow);
                                },
                                drawCallback   : function (oSettings) {
                                    responsiveHelper.respond();
                                }
                            });
                             
                           $(".fancybox").fancybox({
                                openEffect  : 'none',
                                closeEffect : 'none',
                                iframe : {
                                        preload: false
                                }
                            });



                            $(".various").fancybox({
                                    maxWidth	: 800,
                                    maxHeight	: 600,
                                    fitToView	: false,
                                    width		: '70%',
                                    height		: '70%',
                                    autoSize	: false,
                                    closeClick	: false,
                                    openEffect	: 'none',
                                    closeEffect	: 'none'
                                    
                            });



                            $('.fancybox-media').fancybox({
                                    openEffect  : 'none',
                                    closeEffect : 'none',
                                    helpers : {
                                            media : {}
                                    }
                            });

                    });
                        
		</script>


		<script>
			jQuery( function() {
				  // init Isotope
			  	var $container = jQuery('.isotope').isotope
			  	({
				    itemSelector: '.element-item',
				    layoutMode: 'fitRows'
			  	});


  				// bind filter button click
  				jQuery('#filters').on( 'click', 'button', function() 
  				{
				    var filterValue = $( this ).attr('data-filter');
				    // use filterFn if matches value
				    $container.isotope({ filter: filterValue });
				 });
  
			  // change is-checked class on buttons
			  	jQuery('.button-group').each( function( i, buttonGroup ) 
			  	{
			    	var $buttonGroup = $( buttonGroup );
			    	$buttonGroup.on( 'click', 'button', function() 
			    	{
			      		$buttonGroup.find('.is-checked').removeClass('is-checked');
			      		jQuery( this ).addClass('is-checked');
			    	});
			  	});
			  
			});
		</script>
<!--                <script src="http://code.jquery.com/jquery-1.11.3.min.js"></script> -->
                <script>
                (function($){
                  var ico = $('<i class="fa fa-caret-right"></i>');
                  $('nav#menu li:has(ul) > a').append(ico);

                  $('nav#menu li:has(ul)').on('click',function(){
                    $(this).toggleClass('open');
                  });

                  $('a#toggle').on('click',function(e){
                    $('html').toggleClass('open-menu');
                    return false;
                  });


                  $('div#overlay').on('click',function(){
                    $('html').removeClass('open-menu');
                  })

                })(jQuery)
                </script>
                
	</body>
        
        <script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-36251023-1']);
  _gaq.push(['_setDomainName', 'jqueryscript.net']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
</html>
