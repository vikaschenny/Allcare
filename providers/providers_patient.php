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


$pagename = "plist"; 
if(isset($_SESSION['portal_username']) !=''){
   $provider=$_SESSION['portal_username'];
}else {
   $provider=$_REQUEST['provider'];
}


 $sql=sqlStatement("SELECT id, fname, lname, specialty FROM users " .
      "WHERE active = 1 AND ( info IS NULL OR info NOT LIKE '%Inactive%' ) " .
      "AND authorized = 1 AND username='".$provider."'" .
      "ORDER BY lname, fname");
$id=sqlFetchArray($sql);
$id1=$id['id'];



$_POST['string']; 
 $provider=$_REQUEST['provider'];
 $facility=$_POST['facility'];
 $all_patients=$_POST['all_patients'];
$sql=sqlStatement("SELECT id, fname, lname, specialty FROM users " .
      "WHERE active = 1 AND ( info IS NULL OR info NOT LIKE '%Inactive%' ) " .
      "AND authorized = 1 AND username='".$provider."'" .
      "ORDER BY lname, fname");
 $id=sqlFetchArray($sql);

 $order=$_POST['col'];

 $menu=$_REQUEST['menu_val'];
if($provider!='' && $order!='' && $menu!=''){
    $sql1=sqlStatement("SELECT * from tbl_allcare_providers_fieldsorder where username='$provider' AND menu='$menu'");
    $row1=sqlFetchArray($sql1);
    if(empty($row1)){
       
        $sql=sqlStatement("INSERT INTO `tbl_allcare_providers_fieldsorder`(`username`, `menu`, `order_of_columns`) VALUES ('$provider','$menu','$order') ");
    }else{
        
        $sql=sqlStatement("UPDATE `tbl_allcare_providers_fieldsorder` SET `order_of_columns`='$order' WHERE username='$provider' AND menu='$menu'");
    }
}

 $filters=$_POST["my_filters"]; 
 $filters1=$_POST["all_filters"];
 $fac_filter=$_POST["fac_filters"];
 $fac_filter1=$_POST["fac_filters1"];
 $app_date=$_POST["app_date"];
 $enc_val=$_POST['enc_val'];
 

$base_url="http://".$_SERVER['SERVER_NAME'].dirname($_SERVER["REQUEST_URI"].'?').'/';


//for patient_statement
$form_payer_id=$_POST['form_payer_id'];
$form_source=$_POST['form_source'];
$form_pid=$_POST['form_pid'];
$form_paydate=$_POST['form_paydate'];
$form_deposit_date=$_POST['form_deposit_date'];
$form_amount=$_POST['form_amount'];
$form_encounter=$_POST['form_encounter'];
$form_date=$_POST['form_date'];
$form_to_date=$_POST['form_to_date'];
$form_ageby=$_POST['form_ageby'];
$form_age_cols=$_POST['form_age_cols'];
$form_age_inc=$_POST['form_age_inc'];
$form_category=$_POST['form_category'];
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
            <link rel="stylesheet" type="text/css" href="assets/css/customize.css">
            <link href='http://fonts.googleapis.com/css?family=Roboto+Condensed:400,300' rel='stylesheet' type='text/css'>
            <link rel="stylesheet" href="css/dataTables.bootstrap.css"/>
            <link rel="stylesheet" href="css/datatables.responsive_bootstrap.css"/>
            <script src="js/responsive_datatable/jquery.min.js"></script>
            <script src="js/responsive_datatable/jquery.dataTables.min.js"></script>
            <script src="js/responsive_datatable/dataTables.bootstrap.js"></script>
            <script src="js/responsive_datatable/datatables.responsive.js"></script>
<!--            <link rel="stylesheet" href="fancybox/source/jquery.fancybox.css" type="text/css" media="screen" />
            <script type="text/javascript" src="fancybox/source/jquery.fancybox.pack.js"></script>-->
            
            
            <script type="text/javascript" src="js/ddaccordion.js">

            /***********************************************
            * Accordion Content script- (c) Dynamic Drive DHTML code library (www.dynamicdrive.com)
            * Visit http://www.dynamicDrive.com for hundreds of DHTML scripts
            * Please keep this notice intact
            ***********************************************/

            </script>
            <style>
                @media screen and (max-width: 767px) {

                    main#content {
                      margin-top: 65px;
                      transition: all ease-out 0.3s;
                    }
                    #byfilter{
                        
                    }
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
                .navbar-nav > li > .dropdown-menu{
                    margin-top: 4px !important;
                }
                
                .mypets{ /*header of 1st demo*/
                cursor: hand;
                cursor: pointer;
                padding: 2px 5px;
                border: 1px solid rgba(76, 214, 245, 0.62);
                background: rgba(76, 214, 245, 0.62);
                }

                .openpet{ /*class added to contents of 1st demo when they are open*/
                  background-color: rgba(76, 214, 245, 0.62);
                   border: 1px solid black;
                }

                .technology{ /*header of 2nd demo*/
                cursor: hand;
                cursor: pointer;
                font: bold 14px Verdana;
                margin: 10px 0;
                }


                

            </style>
            <script>
                function divclick(cb, divid) {
                    var divstyle = document.getElementById(divid).style;
                    if (cb.checked) {
                     divstyle.display = 'block';
                    } else {
                     divstyle.display = 'none';
                    }
                    return true;
               }
           </script> 
           <script type='text/javascript'>
               //Initialize first demo:
                ddaccordion.init({
                        headerclass: "mypets", //Shared CSS class name of headers group
                        contentclass: "thepet", //Shared CSS class name of contents group
                        revealtype: "click", //Reveal content when user clicks or onmouseover the header? Valid value: "click", "clickgo", or "mouseover"
                        //revealtype: "mouseover",
                        mouseoverdelay: 200, //if revealtype="mouseover", set delay in milliseconds before header expands onMouseover
                        collapseprev: true, //Collapse previous content (so only one open at any time)? true/false 
                        defaultexpanded: [0], //index of content(s) open by default [index1, index2, etc]. [] denotes no content.
                        onemustopen: false, //Specify whether at least one header should be open always (so never all headers closed)
                        animatedefault: false, //Should contents open by default be animated into view?
                        scrolltoheader: false, //scroll to header each time after it's been expanded by the user?
                        persiststate: true, //persist state of opened contents within browser session?
                        toggleclass: ["", "openpet"], //Two CSS classes to be applied to the header when it's collapsed and expanded, respectively ["class1", "class2"]
                        togglehtml: ["none", "", ""], //Additional HTML added to the header when it's collapsed and expanded, respectively  ["position", "html1", "html2"] (see docs)
                        animatespeed: "fast", //speed of animation: integer in milliseconds (ie: 200), or keywords "fast", "normal", or "slow"
                        oninit:function(expandedindices){ //custom code to run when headers have initalized
                                //do nothing
                        },
                        onopenclose:function(header, index, state, isuseractivated){ //custom code to run whenever a header is opened or closed
                                //do nothing
                        }
                })

               
                function win1(url){
                     // alert(url);
                    window.open(url,'popup','width=900,height=900,scrollbars=yes,resizable=yes');
                } 
                function submitme1() {
                   
                    var string1='';
                    $( ".order" ).each(function( index ) {
                      console.log( index + ": " + $( this ).text() );
                      string1 += index + ":" +$( this ).text()+",";
                     });
                     var string= string1.substring(0,string1.lastIndexOf(","));
                     
                     document.getElementById("col").value = string;  
                     document.getElementById("menu_val").value = 'plist';  
                     var f1 = document.forms['form-horizontal'];
                     f1.submit();
                
                }
		</script>
                <script language="javascript"> 

                function DoPost(page_name, provider) {
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
                    document.body.appendChild(form);
                    form.submit();
                }
            </script>       
	</head>

	<body><?php include 'header_nav.php'; ?>
             <section id= "services">
                <div class= "container">
				<div class= "row">
					<div class= "col-lg-12 col-sm-12 col-xs-12" style='padding-top:100px !important;'>
					
                                            <?php //echo $_POST['string']; 
                                                      function display_db_query($query_string,$provider,$proid) {
                                                            // perform the database query
                                                            $result_id = mysql_query($query_string)
                                                            or die("display_db_query:" . mysql_error());
                                                            // find out the number of columns in result
                                                            $column_count = mysql_num_fields($result_id)
                                                            or die("display_db_query:" . mysql_error());
                                                            // Here the table attributes from the $table_params variable are added
                                                            echo "<br/>"; ?>
                                                             <?php $display_style='none' ;
                                                             $sql_vis=sqlStatement("SELECT provider_plist from tbl_user_custom_attr_1to1 where userid='$proid'");
                                                             $row1_vis=sqlFetchArray($sql_vis);
                                                             $avail_fields=explode("|",$row1_vis['provider_plist']);
                                                            // echo '<pre>'; print_r($row1_vis); echo "</pre>";
                                                               //echo '<pre>'; print_r($avail_fields); echo "</pre>";
                                                               for($column_num = 0; $column_num < $column_count; $column_num++) {
                                                                $field_name[] = mysql_field_name($result_id, $column_num); 
                                                               }
                                                             //  echo '<pre>'; print_r($field_name); echo "</pre>";
                                                               $diff=array_diff($avail_fields,$field_name);
                                                               // echo '<pre>'; print_r($diff); echo "</pre>";
                                                             ?>
                                                            
                                                            <input type='checkbox' name='filter' id='filter' value='1' onclick='return divclick(this,"filters");'   <?php if ($display_style == 'block') echo " checked"; ?>><b>Select Column Sequence</b>
                                                            <div id='filters' class="appointment1" style='display:<?php echo $display_style; ?>'>
                                                            <form class="form-horizontal" id="form-horizontal" action="providers_patient.php" method="POST">
                                                                <p>Column Names:</p>
                                                                
                                                                <div style='min-height: 200px; width:200px; overflow-y: scroll; border: 1px solid black;'><ul id="slippylist">
                                                              <?php for($column_num = 0; $column_num < $column_count; $column_num++) {
                                                                $field_name = mysql_field_name($result_id, $column_num); ?>
<!--                                                                 <li class='order'> <?php echo $field_name; ?> </li>-->
                                                               <?php  
                                                                        if($field_name!='pid' && $field_name!='age' && $field_name!='Name'){
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
                                                                    
                                                              } 
                                                              if(!empty($diff)){
                                                                        foreach($diff as $diffval){
                                                                            if($diffval!='age' && $diffval!='Patient_Id' && $diffval!='Patient_Name'){
                                                                             $sql3=sqlStatement("SELECT title,field_id from layout_options where field_id='$diffval' AND form_id='DEM'");
                                                                                while($row3=sqlFetchArray($sql3)){ 
                                                                                     if($row3['title']!=''){?>
                                                                                         <li class='order' style="color:red;" ><?php echo str_replace(" " ,"_",$row3['title']); ?></li>
                                                                                    <?php }else { ?>
                                                                                      <li class='order' style="color:red;"><?php echo $row1['field_id']; ?></li>
                                                                             <?php }
                                                                                
                                                                                }      
                                                                        }
                                                                    }
                                                              } ?>
                                                                    </ul></div>
                                                                   
<!--                                                            <input type='hidden' id='provider' name='provider' value='<?php echo $_REQUEST['provider']; ?>' />
                                                                <div align='right'><button type="submit" class="btn btn-default">Submit</button></div>-->
                                                                <input type='hidden' id='provider' name='provider' value='<?php echo $_REQUEST['provider']; ?>' />
                                                              <input type='hidden' id='col' name='col' value='' />
                                                              <input type='hidden' id='menu_val' name='menu_val' value='' />
                                                              <div align='right'> <a href="javascript:;"  class="btn btn-default" onclick="submitme1();">
                                                                <span><?php echo htmlspecialchars( xl('Submit'), ENT_NOQUOTES); ?></span>
                                                             </a></div>
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
                                                           
                                                    }
                                                    function display_db_table($provider,$facility,$all_patients) {
                                                        //echo "select id from users where username='".$provider."'";
                                                          $sql_list=sqlStatement("SELECT * FROM `list_options`  where list_id='AllCareProviderPatients' order by seq");
                                                          while($row_list=sqlFetchArray($sql_list)){
                                                              $lists[]=$row_list['option_id'];
                                                          }
                                                        //  echo "<pre>"; print_r($lists); echo "</pre>";
                                                          $sql=sqlStatement("select id from users where username='".$provider."'");
                                                          $row=sqlFetchArray($sql);
                                                          $id=$row['id'];
                                                          $sql_vis=sqlStatement("SELECT provider_plist from tbl_user_custom_attr_1to1 where userid='$id'");
                                                          $row1_vis=sqlFetchArray($sql_vis);
                                                          if(!empty($row1_vis)) {
                                                            $avail3=explode("|",$row1_vis['provider_plist']);
                                                            foreach($avail3 as $val6){
                                                                if(in_array($val6, $lists)){
                                                                    $available[]=$val6;
                                                                }
                                                                
                                                            }
                                                            //    echo "<pre>"; print_r($available); echo "</pre>";
                                                                  
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
                                                                      if (in_array($row2['field_id'], $available)){
                                                                            $field1.=$row2['field_id'].",";
                                                                      }
                                                                     
                                                                     
                                                                  }elseif($field[1]=='Patient_Id'){
                                                                       if (in_array($field[1], $available)){
                                                                             $field1.="fe.pid,";
                                                                       }
                                                                  }elseif($field[1]=='Patient_Name'){
                                                                      if (in_array($field[1], $available)){
                                                                        $field1.="CONCAT(title ,'',fname,',',lname) as Name,";
                                                                      }
                                                                  }elseif($field[1]=='Age'){
                                                                       if (in_array($field[1], $available)){
                                                                         $field1.="DATE_FORMAT(FROM_DAYS(DATEDIFF(CURDATE(),DOB)), '%Y')+0 AS age,";
                                                                       }
                                                                  }    
                                                               }
                                                                   $field2=rtrim($field1,",");
                                                                     $sql12="SELECT $field2 from form_encounter fe  INNER JOIN patient_data p ON p.pid=fe.pid  INNER JOIN openemr_postcalendar_events o ON o.pc_pid = fe.pid "
                                                                  . " AND o.pc_catid = fe.pc_catid AND fe.facility_id = o.pc_facility
                                                                      AND o.pc_eventDate = DATE_FORMAT( fe.date,  '%Y-%m-%d' )where o.pc_aid =$id  ";
                                                                    if($facility!=''){
                                                                        $sql12.="AND ".$facility;
                                                                    }
                                                                     if($all_patients!=''){
                                                                        $sql12.="AND ".$all_patients;
                                                                    }
                                                                    $sql12.=" group by fe.pid ";
                                                                    
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
                                                                      AND o.pc_eventDate = DATE_FORMAT( fe.date,  '%Y-%m-%d' )where o.pc_aid =$id ";
                                                                  
                                                                   if($facility!=''){
                                                                        $sql12.=" AND ".$facility;
                                                                    }
                                                                    if($all_patients!=''){
                                                                        $sql12.=" AND ".$all_patients;
                                                                    }
                                                                    $sql12.=" group by fe.pid";
                                                          }
                                                            display_db_query($sql12, $provider,$row['id']);
                                                       }    
                                                    }
                                               display_db_table($provider,$facility,$all_patients); ?>
                                                 
                                                 
                                                <h3 class="mypets">My Patients</h3>
                                                <div class="thepet">
                                                <a class="hiddenajaxlink" href="<?php echo $base_url ?>/patient_data.php?order=<?php echo $order; ?>&provider=<?php echo $provider; ?>&id=my_patients&my_filters=<?php echo $filters; ?>"></a>
                                                </div>

                                                <h3 class="mypets">All Patients</h3>
                                                <div class="thepet">
                                                <a class="hiddenajaxlink" href="<?php echo $base_url ?>/patient_data.php?order=<?php echo $order; ?>&provider=<?php echo $provider; ?>&id=all_patients&all_filters=<?php echo $filters1; ?>"></a>
                                                </div> 
                                                
                                                <h3 class="mypets">Patients By Facility</h3>
                                                <div class="thepet">
                                                <a class="hiddenajaxlink" href="<?php echo $base_url ?>/patient_data.php?order=<?php echo $order; ?>&provider=<?php echo $provider; ?>&id=by_facility&fac_filters=<?php echo $fac_filter; ?>&fac_filters1=<?php echo $fac_filter1; ?>"></a>
                                                </div>
                                                
                                                <h3 class="mypets">Patients By Appointments</h3>
                                                <div class="thepet">
                                                <a class="hiddenajaxlink" href="<?php echo $base_url ?>/patient_data.php?order=<?php echo $order; ?>&provider=<?php echo $provider; ?>&id=by_appointment&app_date=<?php echo $app_date; ?>&enc_val=<?php echo $enc_val; ?>"></a>
                                                </div>
<!--                                                 <h3 class="mypets">Patient Center Batch</h3>
                                                <div class="thepet">
                                                <a class="hiddenajaxlink" href="<?php echo $base_url ?>/patient-center-batch.php"></a>
                                                </div>
                                                <h3 class="mypets">Patient Statement Batch</h3>
                                                <div class="thepet">
                                                <a class="hiddenajaxlink" href="<?php echo $base_url ?>/patient-statement.php?form_payer_id=<?php echo $form_payer_id; ?>&form_source=<?php echo $form_source;?>
                                                   &form_pid=<?php echo $form_pid; ?>&form_paydate=<?php echo $form_paydate; ?>&form_deposit_date=<?php echo $form_deposit_date; ?>
                                                   &form_amount=<?php echo $form_amount; ?>&form_encounter=<?php echo $form_encounter; ?>&form_date=<?php echo form_date; ?>
                                                   &form_to_date=<?php echo $form_to_date; ?>&form_ageby=<?php echo $form_ageby; ?>&form_age_cols=<?php echo $form_age_cols; ?>
                                                   &form_age_inc=<?php echo $form_age_inc; ?>&form_category=<?php echo $form_category; ?>"></a>
                                                </div>-->
                                        </div>
				</div>
                 <div><br><br></div>
                  
		</section>
                 <?php include 'footer.php'; ?>    
                <script type="text/javascript" src="assets/js/jquery.min.js"></script>
		<script type="text/javascript" src="assets/js/owl.carousel.min.js"></script>
		<script type="text/javascript" src="assets/js/isotope.pkgd.min.js"></script>
		<script type="text/javascript" src="assets/js/wow.min.js"></script>
		<script type="text/javascript" src="assets/js/bootstrap.min.js"></script>
		<script type='text/javascript'>
                 new WOW().init();
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
