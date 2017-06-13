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
   $refer=$_REQUEST['refer']; 
   $_SESSION['refer']=$_REQUEST['refer'];
   $_SESSION['portal_username']=$_REQUEST['provider'];
} 

$base_url="//".$_SERVER['SERVER_NAME'].dirname($_SERVER["REQUEST_URI"].'?').'/';

 $sql=sqlStatement("SELECT id, fname, lname, specialty FROM users " .
      "WHERE active = 1 AND ( info IS NULL OR info NOT LIKE '%Inactive%' ) " .
      "AND authorized = 1 AND username='".$provider."'" .
      "ORDER BY lname, fname");
$id=sqlFetchArray($sql);
$id1=$id['id'];

$order=$_REQUEST['order'];
$provider=$_REQUEST['provider'] ? $_REQUEST['provider'] : $_SESSION['portal_username'];
$page_id=$_REQUEST['id'];


function display_db_query($query_string,$provider,$proid,$page_id) {
// perform the database query
$result_id = mysql_query($query_string)
or die("display_db_query:" . mysql_error());
// find out the number of columns in result
$column_count = mysql_num_fields($result_id)
or die("display_db_query:" . mysql_error());
// Here the table attributes from the $table_params variable are added


print("<TABLE  id='$page_id' class='table table-striped table-bordered dt-responsive nowrap'  cellspacing='0' width='100%'>");
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
             if($field_name=='Name'){
             $pos = strpos($row[$column_num], '.');
             
              if($pos!=''){
                  //echo "test"; echo $pos; echo "test";
                   $title=explode(".",$row[$column_num]);
                   $nam=explode(",",$title[1]);
              }else {
                  //echo "test".$row[$column_num]."<br>";
                  $nam=explode(",",$row[$column_num]);
              }
                
                $sql_id=sqlStatement("select * from patient_data where fname=? AND lname=?", array($nam[0],$nam[1])); 
                  $patientid=sqlFetchArray($sql_id);?>
                <td><a  href="provider_incomplete_charts.php?form_patient=<?php echo $patientid['pid'] ; ?>"><?php echo $row[$column_num] ; ?></a>
                    <?php
                    if($page_id=='all_patients'){
                        ?> 
                        <br>
                        <span>
                            <a  href="javascript:void()" data-href="patient_file/summary/demographics_full.php?pid=<?php echo $patientid['pid'] ; ?>" data-toggle='modal' data-target='#showeditdemographics'><span><img src="images/demographics.png" height='20px' alt="edit demographics image" title="Edit Patient Demographics"  ></span></a> 
                            | 
                            <!--<a  href="javascript:void()" data-href="scheduling/calendar/add_edit_event.php?patientid=<?php echo $patientid['pid'] ; ?>" data-toggle='modal' data-target='#createappointment'><span><img src="images/createappointment.png" height='20px' alt="create appointment image" title="Create Appointment"></span></a> -->
                            <a  href="#" onclick="window.open('<?php echo $base_url ?>scheduling/calendar/add_edit_event.php?patientid=<?php echo $patientid['pid'] ; ?>','name=appt','width=595,height=300')" ><span><img src="images/createappointment.png" height='20px' alt="create appointment image" title="Create Appointment"></span></a> 
                            |
                            <a  href="providers_medrecord.php?form_patient=<?php echo $patientid['pid'] ; ?>" style="font-size: 12px;color: #4169E1; "  ><span>Transactions</span></a>
                        </span>
<!--                        <a  href="javascript:void(0);" onclick='validate_elig();' style="font-size: 12px;color: #F26114;"  ><span>Check Eligibility</span></a>-->
                        <?php
                    }
                    if($page_id=='my_patients') { ?>
                        <br>
                        <span>
                            <a  href="javascript:void()" data-href="patient_file/summary/demographics_full.php?pid=<?php echo $patientid['pid'] ; ?>" data-toggle='modal' data-target='#showeditdemographics'><span><img src="images/demographics.png" height='20px' alt="edit demographics image" title="Edit Patient Demographics"  ></span></a> 
                            | 
                            <!--<a  href="javascript:void()" data-href="scheduling/calendar/add_edit_event.php?patientid=<?php echo $patientid['pid'] ; ?>" data-toggle='modal' data-target='#createappointment'><span><img src="images/createappointment.png" height='20px' alt="create appointment image" title="Create Appointment"></span></a> -->
                            <a  href="#" onclick="window.open('<?php echo $base_url ?>scheduling/calendar/add_edit_event.php?patientid=<?php echo $patientid['pid'] ; ?>','name=appt','width=595,height=300')" ><span><img src="images/createappointment.png" height='20px' alt="create appointment image" title="Create Appointment"></span></a> 
                            |
                            <a  href="providers_medrecord.php?form_patient=<?php echo $patientid['pid'] ; ?>" style="font-size: 12px;color: #4169E1; "  ><span>Transactions</span></a>
                        </span>
                        <br> 
                    <?php } ?>
                    
                <?php 
                if($page_id=='by_facility'){
                    echo "<a href='javascript:;' style='font-size: 12px;color: #4169E1; ' onclick=win1('create_encounter/new_popup.php?provider=$provider&pid=".$patientid['pid']."&enc_facility=".$_POST['fac_filters1']."'); ><span>Create Encounter</span></a>"; 
                }
                ?>
                </td>
          <?php }elseif($field_name=='patient_facility'){
                   if($row[$column_num]!=''){ 
                       $sql=sqlStatement("select * from facility where id=$row[$column_num]");
                       $fac=sqlFetchArray($sql);
                       echo "<td>"; echo $fac['name']; echo"</td>";
                   }else {
                        echo "<td>"; echo $row[$column_num]; echo"</td>";
                   }
                    
                }else{
                  print("<td>$row[$column_num]</td>\n"); 
                 
                }

        }
        print("</tr>\n");
}
                                            
print("</table>\n"); 
}
function display_db_table($provider,$page_id,$filters) {

  $sql_list=sqlStatement("SELECT * FROM `list_options`  where list_id='AllCareProviderPatients' order by seq");
  while($row_list=sqlFetchArray($sql_list)){
      $lists[]=$row_list['option_id'];
  }

  $sql=sqlStatement("select id from users where username='".$provider."'");
  $row=sqlFetchArray($sql);
  $id=$row['id'];
  $sql_vis=sqlStatement("SELECT provider_plist from tbl_user_custom_attr_1to1 where userid='$id'");
  $row1_vis=sqlFetchArray($sql_vis);
  if(!empty($row1_vis)) {
    $avail3 = explode("|",$row1_vis['provider_plist']);
    foreach($avail3 as $val6){
        if(in_array($val6, $lists)){
            $available[]=$val6;
        }

    }
    
    $avail=explode("|",$row1_vis['provider_plist']);
    foreach($avail as $val2){
         if($val2=='Patient_Id'){
             $avail_field.="p.pid,";
         }elseif($val2=='Patient_Name'){
             $avail_field.="CONCAT(p.title ,'',fname,',',lname) as Name,";
         }elseif($val2=='age'){
             $avail_field.="DATE_FORMAT(FROM_DAYS(DATEDIFF(CURDATE(),DOB)), '%Y')+0 AS age,";
         }else{
             $avail_field.=$val2.",";
         }
    }
    $avail_field2=rtrim($avail_field,",");
    if($avail_field2==''){
              echo "<br><br>"; echo "<b>You doesn't have access to view patient details..<b>";  echo "<br><br>";
    }else {
          
           //filters
        if($page_id=='my_patients'){
           $sql12='';
            $sql12="SELECT $avail_field2
                from patient_data p
                INNER JOIN form_encounter f ON f.pid = p.pid 
               ";
            if($filters!=''){
              $sql12.=" where f.rendering_provider =$id AND $filters ";
            }else{
              $sql12.=" where f.rendering_provider =$id AND deceased_stat <> 'YES' AND practice_status ='YES' ";
            }
        }
        if($page_id=='all_patients'){
           $sql12='';
            $sql12="SELECT $avail_field2
                from patient_data p
              
               ";
            if($filters!=''){
              $sql12.=" where  $filters ";
            }
//            else{
//              $sql12.=" where sudo_required = 'YES'   ";
//            }
        }
        if($page_id=='by_facility'){
           $sql12='';
            $sql12="SELECT $avail_field2
                from patient_data p
              
               ";
            if($filters!=''){
              $filter=explode("$",$filters); 
              
              if($filter[0]!='' && $filter[1]=='0'){
                 $sql12.=" where p.pid not in (SELECT DISTINCT(patientid) FROM tbl_patientfacility) AND $filter[0]";
              }elseif($filter[0]!='' && $filter[1]!='0'){
                $sql12.=" INNER JOIN tbl_patientfacility tpf ON p.pid = tpf.patientid
                WHERE facility_isactive =  'YES'
                AND tpf.facilityid =$filter[1]
                AND tpf.id
                IN (

                SELECT MAX( id ) 
                FROM tbl_patientfacility
                WHERE facility_isactive =  'YES' 
                GROUP BY patientid
                ) AND $filter[0] ";
              }
             
            }else{
              $sql12.=" where p.pid not in (SELECT DISTINCT(patientid) FROM tbl_patientfacility) AND deceased_stat <> 'YES' AND practice_status ='YES' ";
            }
        }
        
        if($page_id=='by_appointment'){
            if($filters!=''){
                 $filter1=explode("$",$filters);
                 if($filter1[0]!='' && $filter1[1]!='' ){
                    $sql12='';
                     $sql12=" SELECT $avail_field2
                        FROM patient_data p INNER JOIN openemr_postcalendar_events ope ON p.pid=ope.pc_pid
                                inner join openemr_postcalendar_categories pc on pc.pc_catid=ope.pc_catid 
                                                        inner join list_options lo on lo.option_id=ope.pc_apptstatus
                        WHERE ope.pc_aid=$id AND ope.pc_eventdate='$filter1[0]' and p.practice_status = 'YES' AND p.deceased_stat != 'YES' AND (p.deceased_date = '' OR p.deceased_date = '0000-00-00 00:00:00' ) and ope.pc_catid IN ($filter1[1]) order by p.lname, p.fname ";
                     
                 }
            }else{
               
                $sql=sqlStatement("select id from users where username='".$provider."'");
                  $row=sqlFetchArray($sql);
                  $id=$row['id'];
                     $sql_apppatients3=sqlStatement("SELECT visit_categories FROM tbl_allcare_facuservisit WHERE  `users`  REGEXP ('".":\"".$id."\"')");
                      while($row_apppatients3=sqlFetchArray($sql_apppatients3)){
                           $array[]=unserialize($row_apppatients3['visit_categories']);
                      }
                      $dataArray = array();
                        for($j = 0; $j<count($array); $j++){
                            foreach($array[$j] as $arraykey){
                                 $dataArray[] = $arraykey;
                            }
                        }
                        $enc_val = '';
                        $dataarray = array_unique($dataArray);
                        foreach($dataarray as $arrayval){
                            $enc_val .= $arrayval.",";
                        }
                        $enc_value = rtrim($enc_val,",");
                         $sql12='';
                         
                       $sql12=" SELECT $avail_field2
                        FROM patient_data p INNER JOIN openemr_postcalendar_events ope ON p.pid=ope.pc_pid
                                inner join openemr_postcalendar_categories pc on pc.pc_catid=ope.pc_catid 
                                                        inner join list_options lo on lo.option_id=ope.pc_apptstatus
                        WHERE ope.pc_aid=$id AND ope.pc_eventdate='$today' and p.practice_status = 'YES' AND p.deceased_stat != 'YES' AND (p.deceased_date = '' OR p.deceased_date = '0000-00-00 00:00:00' ) and ope.pc_catid IN ($enc_value)   ";
            }
        }
        if($page_id=='by_appointment'){
        if($filters==''){
             $sql12.=" group by p.pid order by p.lname, p.fname ";
        }   
       
       }else{
           $sql12.=" group by p.pid ";
       }
      //echo $sql12;
        display_db_query($sql12, $provider,$row['id'],$page_id);
    }
}    
}
?>
                
<!DOCTYPE html>
<html>
    <head>
            <meta charset="UTF-8">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1 maximum-scale=1, user-scalable=no">
            <link href='https://fonts.googleapis.com/css?family=Abel' rel='stylesheet' type='text/css'>
	    <link href='https://fonts.googleapis.com/css?family=Roboto:400,300,500' rel='stylesheet' type='text/css'>
	    <link href='https://fonts.googleapis.com/css?family=Dosis:300,400,500,600' rel='stylesheet' type='text/css'>
            <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">
            <link rel="stylesheet" type="text/css" href="assets/css/font-awesome.min.css">
            <link rel="stylesheet" type="text/css" href="assets/css/main.css">
            <link rel="stylesheet" type="text/css" href="assets/css/customize.css">
            <link href='https://fonts.googleapis.com/css?family=Roboto+Condensed:400,300' rel='stylesheet' type='text/css'>
            <script type='text/javascript' src='../interface/main/js/jquery-1.11.1.min.js'></script>
           <style>
            html,body{
                height: 100%;
            }
            #content table ul li{
               display: block;
            }
            
            #content {
                margin-bottom: 45px;
                overflow-x: visible;
                overflow-y: hidden;
            }
           
            .DTTT.btn-group{
                float: right;
                padding-left: 13px;
                position: relative;
            }
            #my_patients_length{
                float:left;
            }
            #all_patients_length{
                float:left;
            }
            #by_facility_length{
                float:left;
            }
            #by_appointment_length{
                float:left;
            }
            .costmizecolumns {
                margin-bottom: 7px;
                margin-top: 13px;
                text-align: center;
                margin-left: 30%;
                width: 220px;
            }
            @media only screen and (min-width: 1024px){
                .costmizecolumns {
                    position: relative;
                    top:0px;
                    margin-right: 0px;
                }
            }

           @media only screen and (min-width: 1200px){
                .costmizecolumns {
                    position: relative;
                    top:33px;
                    margin-right: 113px;
                }
            }

            @media only screen and (max-width: 620px){
                .DTTT.btn-group{
                    float: none;
                    margin-bottom: 6px;
                    padding-left: 40%;
                    position: relative;
                }
                #my_patients_length{
                    float:none;
                }
                #all_patients_length{
                    float:none;
                }
                #by_facility_length{
                    float:none;
                }
                #by_appointment_length{
                    float:left;
                }
                .costmizecolumns {
                    margin-bottom: 7px;
                    margin-top: 13px;
                    text-align: center;
                    margin-left: 0;
                    width: auto;
                }
            }
            #showeditdemographics .modal-body, #createappointment .modal-body{
                padding: 0px;
            }
            #services {
                margin-bottom: -45px;
            }
            @media screen and (min-width: 992px) {
                #showeditdemographics .modal-lg {
                 width: 90%; 
               }
               #createappointment .modal-lg{
                 width: 70%; 
               }
            }
            
            /* side menu*/
            .navbar-default {
                border-color: transparent;
            }

            .page-content > .row{
                margin-left:0px;
                margin-right:0px;
                min-height: 100%;
                margin-bottom: -45px;
            }

            #wrapper {
                padding-left: 53px;
                transition: all .4s ease 0s;
                height: 100%;
                padding-top: 89px;
                height: 100%
            }

            #sidebar-wrapper {
                margin-left: -235px;
                left: 53px;
                width: 235px;
                background: #46a1b4;//#222;
                position: fixed;
                height: 100%;
                z-index: 1;
                transition: all .4s ease 0s;
            }

            .sidebar-nav {
                display: block;
                float: left;
                width: 235px;
                list-style: none;
                margin: 0;
                padding: 0;
            }
            .sidebar-nav li{
                display: inline;
            }
            .page-content{
                min-height: 100%;
            }
            #page-content-wrapper {
                padding-left: 0;
                margin-left: 0;
                width: 100%;
                min-height: 100%;
                height: auto;
                margin-bottom: -45px;
            }
            #wrapper.active {
                padding-left: 235px;
            }
            #wrapper.active #sidebar-wrapper {
                left: 235px;
            }

            #page-content-wrapper {
              width: 100%;
              min-height: 100%;
            }
            #sidebar {
                height: 100%;
                overflow-y: auto;
                -webkit-overflow-scrolling: touch;
            }


            #sidebar_menu li a, .sidebar-nav li a {
                color: #fff; //#999;
                display: block;
                float: left;
                text-decoration: none;
                width: 235px;
                background: #46a1b4;//#252525;
                border-top: 1px solid #54afc2;//#373737;
                border-bottom: 1px solid #3893a6; //#1A1A1A;
                -webkit-transition: background .5s;
                -moz-transition: background .5s;
                -o-transition: background .5s;
                -ms-transition: background .5s;
                transition: background .5s;
            }
            .sidebar_name {
                padding-top: 25px;
                color: #fff;
                opacity: .7;
            }

            .sidebar-nav li {
              line-height: 40px;
              text-indent: 15px;
            }

            .sidebar-nav li a {
              color: #fff;//#999999;
              display: block;
              text-decoration: none;
            }

            .sidebar-nav li a:hover {
              color: #fff;
              background: rgba(255,255,255,0.2);
              text-decoration: none;
              cursor: pointer;
            }
            
            .sidebar-nav li a.active {
                background-color: rgba(76, 214, 245, 0.62);
                cursor: default;
            }
            
            .sidebar-nav li a:active,
            .sidebar-nav li a:focus {
              text-decoration: none;
            }

            .sidebar-nav > .sidebar-brand {
              height: 45px;
              line-height: 45px;
              font-size: 18px;
            }

            .sidebar-nav > .sidebar-brand a {
              color: #999999;
            }

            .sidebar-nav > .sidebar-brand a:hover {
              color: #fff;
              background: none;
            }

            #main_icon
            {
               float:right;
               padding-right: 20px;
               padding-top:13px;
            }
            .sub_icon
            {
                float:right;
               padding-right: 20px;
               padding-top:10px;
            }
            .content-header {
              height: 65px;
              line-height: 65px;
            }

            .content-header h1 {
              margin: 0;
              margin-left: 20px;
              line-height: 65px;
              display: inline-block;
            }

            @media (max-width:767px) {
                #wrapper {
                    padding-left: 53px;
                    transition: all .4s ease 0s;
                }
                #sidebar-wrapper {
                    left: 53px;
                }
                #wrapper.active {
                    padding-left: 53px;
                }
                #wrapper.active #sidebar-wrapper {
                    left: 235px;
                    width: 235px;
                    transition: all .4s ease 0s;
                }
            }
    </style>
    
    <script type='text/javascript'>
            function dropdownchange(val){
                var f = document.forms['patients_filters'];
                f.submit();
            }                       
            function submitme() {
              var f1 = document.forms['patients_filters'];
              f1.submit();
            }
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
                var key1='refer';
                var hiddenField1 = document.createElement("input");
                hiddenField1.setAttribute("type", "hidden");
                hiddenField1.setAttribute("name", key1);
                hiddenField1.setAttribute("value", refer);
                form.appendChild(hiddenField1);
                form.appendChild(hiddenField);
                document.body.appendChild(form);
                form.submit();
            } 
                
            $(function () {
                setNavigation();
            });
            
            function setNavigation() {                    
                if($('#sidenavep').val() == 'my_patients'){
                    $('#sidebar li').eq(0).find("a").addClass('active');
                }else if($('#sidenavep').val() == 'all_patients'){
                    $('#sidebar li').eq(1).find("a").addClass('active');
                }else if($('#sidenavep').val()  == 'by_facility'){
                    $('#sidebar li').eq(2).find("a").addClass('active');
                }else if($('#sidenavep').val() == 'by_appointment'){
                    $('#sidebar li').eq(3).find("a").addClass('active');
                }
            }
            function DoPost_patient(element,url) {
                if(element.className == "active")
                    return false;
                var res = url.split("?");
                var param=res[1].split("&");
                method = "post"; // Set method to post by default if not specified.
                var form = document.createElement("form");
                    form.setAttribute("method", method);
                    form.setAttribute("action", res[0]);
                for(var i=0; i<param.length; i++){
                    var param1=param[i].split("=");
                    var hiddenField = document.createElement("input");
                    hiddenField.setAttribute("type", "hidden");
                    hiddenField.setAttribute("name", param1[0]);
                    hiddenField.setAttribute("value", param1[1]);
                    form.appendChild(hiddenField);
                }
                document.body.appendChild(form);
                form.submit();
            }
            //Eligibility verification
            function validate_elig(){ 
                
                //pid,form_id,month_value,payer,provider_id,dos
                if(document.getElementById('form_x12').value=='')
                {
                    alert(stringverify);
                    return false;
                }
                else
                {
                    from        = document.getElementById('form_from_date').value;
                    to          = document.getElementById('form_to_date').value;
                    facility    = document.getElementById('form_facility').value;
                    providerid  = document.getElementById('form_users').value;
                    removedrows = document.getElementById('removedrows').value;
                    form_x12    = document.getElementById('form_x12').value;

                    provider    = '<?php echo $provider; ?>';
                    refer       = '<?php echo $refer; ?>';

                    if(form_id === '' || form_id === '0'){
                        form_id     = 0;
                    }
                    var eligibility = [
                        {from:from,to:to,facility:facility,provider:provider,providerid:providerid,refer:refer,removedrows:removedrows,form_x12:form_x12,pid:pid,frame:"show"},
                        {pid:pid,form_id:form_id,month_value:month_value,verify_type:"patient_eligibility",payer_id:payer,provider:provider,provider_id:provider_id,refer:refer,dos:dos,frame:"show"}
                    ];
                    if(pid != ''){
                        // checkbox condition
                        var class_name_checkbox = "checkbox"+month_value+"-p"+pid;

                        var viewportwidth   = document.documentElement.clientWidth;
                        var viewportheight  = document.documentElement.clientHeight;
                        window.resizeBy(-300,0);
                        window.moveTo(0,0);


                        if ($("."+class_name_checkbox).is(':checked') == false) {
                            //window.open("elig-verify.php?from="+from+"&to="+to+"&facility="+facility+"&provider="+provider+"&removedrows="+removedrows+"&form_x12="+form_x12+"&pid="+pid, "", "width=700, height=600,scrollbars=1,resizable=1");
                            //window.open("save_eligibility_response_data.php?pid="+pid+"&form_id="+form_id+"&month_value="+month_value+"&verify_type=patient_eligibility&payer_id="+payer+"&provider_id="+provider_id+"&dos="+dos, "", "width=600,left="+(viewportwidth-100)+",height=600,top=0,scrollbars=1,resizable=1");

                            eligibility[0].page = "elig-verify";
//                                eligibility[0].page = "save_eligibility_response_data";
                            eligibility[1].page = "save_eligibility_response_data";
                            eligibility[0].pagename = "Eligibility Response";
                            eligibility[1].pagename = "Eligibility Data Screen";

                            window.localStorage.setItem("provider_eligibility",JSON.stringify(eligibility))
                            var url = "verify_eligibility.php";
                            window.open(url,"","width=1000, height=600,scrollbars=1,resizable=1");

                        }else{
                            eligibility[0].frame="hide";
                            eligibility[1].page = "save_eligibility_response_data";
                            eligibility[1].pagename = "Eligibility Data Screen";
                            window.localStorage.setItem("provider_eligibility",JSON.stringify(eligibility))
                            var url = "verify_eligibility.php";
                            window.open(url,"","width=1000, height=600,scrollbars=1,resizable=1");
                          //window.open("save_eligibility_response_data.php?pid="+pid+"&form_id="+form_id+"&month_value="+month_value+"&verify_type=patient_eligibility&payer_id="+payer+"&provider_id="+provider_id+"&dos="+dos, "","width=1000, height=600,scrollbars=1,resizable=1");  
                        }
                    }else{
                        eligibility[1].frame="hide";
                        eligibility[0].page = "elig-verify";
//                            eligibility[0].page = "save_eligibility_response_data";
                        eligibility[0].pagename = "Eligibility Response";
                        window.localStorage.setItem("provider_eligibility",JSON.stringify(eligibility));
                        var url = "verify_eligibility.php";
                        window.open(url,"","width=1000, height=600,scrollbars=1,resizable=1");
                       //window.open("elig-verify.php?from="+from+"&to="+to+"&facility="+facility+"&provider="+provider+"&removedrows="+removedrows+"&form_x12="+form_x12+"&pid="+pid, "", "width=880, height=600,scrollbars=1,resizable=1");
                    }
                }
            }
            
    </script>
        <link rel="stylesheet" href="css/version1.0/dataTables.bootstrap.min.css"/>
        <link rel="stylesheet" href="css/version1.0/responsive.bootstrap.min.css"/>
        <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/themes/redmond/jquery-ui.css" /> 
        <link rel="stylesheet" href="css/pqselect.min.css"/>
        <script src="https://code.jquery.com/ui/1.10.2/jquery-ui.js"></script>
        <script src="js/pqselect.min.js"></script>
        <script src="js/responsive_datatable/version1.0/jquery.dataTables.min.js"></script>
        <script src="js/responsive_datatable/version1.0/dataTables.bootstrap.min.js"></script>
        <script src="js/responsive_datatable/version1.0/dataTables.responsive.min.js"></script>
        <script type='text/javascript' src='js/responsive_datatable/dataTables.tableTools.js'></script>
        <script type='text/javascript' src='js/responsive_datatable/dataTables.bootstrap.js'></script>
        <script type="text/javascript" src="assets/js/bootstrap.min.js"></script>
    </head>
<body  ><?php include 'header_nav.php'; ?>
    <div id="wrapper" class="">  
    <div id="sidebar-wrapper">
        <ul id="sidebar_menu" class="sidebar-nav">
           <li class="sidebar-brand"><a id="menu-toggle" href="#">Menu<span id="main_icon" class="glyphicon glyphicon-align-justify"></span></a></li>
        </ul>
        <ul class="sidebar-nav" id="sidebar">
          <input type="hidden" id="sidenavep" value="<?php echo $page_id; ?>"/>
          <li><a onclick=DoPost_patient(this,'<?php echo $base_url ?>patient_data.php?order=<?php echo $order; ?>&provider=<?php echo $provider; ?>&id=my_patients')>My Patients<span class="sub_icon glyphicon glyphicon-link"></span></a></li>
          <li><a onclick=DoPost_patient(this,'<?php echo $base_url ?>patient_data.php?order=<?php echo $order; ?>&provider=<?php echo $provider; ?>&id=all_patients')>All Patients<span class="sub_icon glyphicon glyphicon-link"></span></a></li>
          <li><a onclick=DoPost_patient(this,'<?php echo $base_url ?>patient_data.php?order=<?php echo $order; ?>&provider=<?php echo $provider; ?>&id=by_facility')>Patients By Facility<span class="sub_icon glyphicon glyphicon-link"></span></a></li>
          <li><a onclick=DoPost_patient(this,'<?php echo $base_url ?>patient_data.php?order=<?php echo $order; ?>&provider=<?php echo $provider; ?>&id=by_appointment')>Patients By Appointments<span class="sub_icon glyphicon glyphicon-link"></span></a></li>
          <?php  $sql_vis=sqlStatement("SELECT provider_plist_links from tbl_user_custom_attr_1to1 where userid='".$id['id']."'");
            $row1_vis=sqlFetchArray($sql_vis);  
            if(!empty($row1_vis)){
                $links=explode("|",$row1_vis['provider_plist_links']);
                 if(in_array('patient_center',$links)){ ?>
          <li><a onclick=DoPost_patient('<?php echo $base_url ?>patient-center-batch.php?provider=<?php echo $provider; ?>')>Patient Center Batch<span class="sub_icon glyphicon glyphicon-link"></span></a></li>
          <?php  } if(in_array('patient_stat',$links)){  ?>
          <li><a onclick=DoPost_patient('<?php echo $base_url ?>patient-statement.php?provider=<?php echo $provider; ?>')>Patient Statement Batch<span class="sub_icon glyphicon glyphicon-link"></span></a></li>
          <?php }if(in_array('create_patient',$links)){?>
          <li><a onclick=DoPost_patient('<?php echo $base_url ?>create_patient/new_comprehensive.php?provider=<?php echo $provider; ?>')>Create Patient<span class="sub_icon glyphicon glyphicon-link"></span></a></li>
          <?php }if(in_array('create_app',$links)){ ?>
          <li><a onclick="window.open('<?php echo $base_url ?>scheduling/calendar/add_edit_event.php','name=appt','width=595,height=300')">Create Appointment<span class="sub_icon glyphicon glyphicon-link"></span></a></li>
          <?php }  if(in_array('create_enc',$links)){ ?>
          <li><a onclick=DoPost_patient('<?php echo $base_url ?>create_encounter/new.php?provider=<?php echo $provider; ?>')>Create Encounter<span class="sub_icon glyphicon glyphicon-link"></span></a></li>
          <?php }if(in_array('scheduling',$links)){ ?>
          <li><a href='<?php echo $base_url ?>scheduling/scheduling_pop_up.php' target="_blank">Scheduling<span class="sub_icon glyphicon glyphicon-link"></span></a></li>
          <?php }
            }
          ?>
          <li style="visibility: hidden"><a>Scheduling<span class="sub_icon glyphicon glyphicon-link"></span></a></li>
          <li style="visibility: hidden"><a>Scheduling<span class="sub_icon glyphicon glyphicon-link"></span></a></li>
          <li style="visibility: hidden"><a>Scheduling<span class="sub_icon glyphicon glyphicon-link"></span></a></li>
          <li style="visibility: hidden"><a>Scheduling<span class="sub_icon glyphicon glyphicon-link"></span></a></li>
        </ul>
      </div>
          
      <div id="page-content-wrapper">
        <div class="page-content inset">
            <div class="row">
              <div class="col-md-12">
                    <div id="content">
                        <?php 
                           if($page_id=='my_patients'){
                               $title='My Patients';
                           }else if($page_id=='all_patients'){
                               $title='All Patients';
                           }else if($page_id=='by_facility'){
                               $title='Patients By Facility';
                           }else if($page_id=='by_appointment'){
                               $title='Patients By Appointments ';
                           }

                           //request from providers_patient.php 
                           if($_REQUEST["my_filters"]!=''){
                             $filters=$_REQUEST["my_filters"];

                           }elseif($_REQUEST["all_filters"]!=''){
                               $filters=$_REQUEST["all_filters"];
                           }elseif($_REQUEST["fac_filters"]!='' || $_REQUEST["fac_filters1"]!=''){
                               $filters=$_REQUEST["fac_filters"]."$".$_REQUEST["fac_filters1"];
                           }elseif($_REQUEST['app_date']!='' && $_REQUEST['enc_val']!='' ){
                               $filters=$_REQUEST['app_date'].'$'.$_REQUEST['enc_val'];
                           }else{
                               $filters='';
                           }

                        ?>  
                       <h3><?php echo $title; ?></h3>                                            
                       <form action="patient_data.php" method="post" name="patients_filters">
                           <input type="hidden" id="sidenavep" value="<?php echo $page_id; ?>"/>
                       <?php
                       if($page_id=='my_patients'){
                                  $sql_mypatients1=sqlStatement("SELECT * FROM `list_options`  where list_id='Mobile_My_Patients_Filters' order by seq");
                                   echo "<select name='my_filters' id='my_filters' onchange='javascript:dropdownchange();'>";
                                   echo "<option  value=''>select</option>";
                                     while($row_mypatients1=sqlFetchArray($sql_mypatients1)){ ?>
                                            <option value="<?php echo $row_mypatients1['notes']; ?>" <?php if($filters!='' && $filters==$row_mypatients1['notes'] )  echo "selected"; elseif($row_mypatients1['title']=="Practice Active")  echo "selected"; ?>><?php echo $row_mypatients1['title']; ?></option>
                                    <?php }
                                   echo "</select>";  
                       }
                       if($page_id=='all_patients'){
                           $sql_mypatients=sqlStatement("SELECT * FROM `list_options`  where list_id='Mobile_All_Patients_Filters' order by seq");
                                   echo "<select name='all_filters' id='all_filters' onchange='javascript:dropdownchange();'>";
                                   echo "<option  value=''>select</option>";
                                     while($row_mypatients=sqlFetchArray($sql_mypatients)){ ?>
                                            <option value="<?php echo $row_mypatients['notes']; ?>" <?php if($filters!='' && $filters==$row_mypatients['notes'] )  echo "selected";  ?>><?php echo $row_mypatients['title']; ?></option>
                                    <?php }
                                   echo "</select>"; 
                       }
                       if($page_id=='by_facility'){
                           echo "<div class='row'><div class='col-xs-12'><div class='form-group'><div class='col-sm-6'>";
                           $sql_mypatients2=sqlStatement("SELECT * FROM `list_options`  where list_id='Mobile_Facility_Filters' order by seq");
                                   echo "<select name='fac_filters' id='fac_filters' class='form-control'>";
                                   echo "<option  value=''>select</option>";
                                     while($row_mypatients2=sqlFetchArray($sql_mypatients2)){ ?>
                                            <option value="<?php echo $row_mypatients2['notes']; ?>" <?php if($filters!='' && $_REQUEST["fac_filters"]==$row_mypatients2['notes']) echo "selected"; elseif($row_mypatients2['title']=="Practice Active") echo "selected"; ?>><?php echo $row_mypatients2['title']; ?></option>
                                    <?php }
                                   echo "</select></div>"; 

                           $sql_mypatients3=sqlStatement("SELECT *  FROM facility WHERE service_location!=0");

                                   echo "<div class='col-sm-6'><select name='fac_filters1' id='fac_filters1' class='form-control'>";
                                   echo "<option  value='0'>select</option>";
                                     while($row_mypatients3=sqlFetchArray($sql_mypatients3)){ ?>
                                            <option value="<?php echo $row_mypatients3['id']; ?>" <?php if($_REQUEST["fac_filters1"]==$row_mypatients3['id']) echo "selected"; ?>><?php echo $row_mypatients3['name']; ?></option>
                                    <?php }
                                   echo "</select></div></div>";    
                           ?>
                           <a href="javascript:;"  class="btn btn-submit btn-sm" onclick="submitme();" class="form-control" style="margin-top:10px;">
                           <span><?php echo htmlspecialchars( xl('Submit'), ENT_NOQUOTES); ?></span>
                        </a>
                       <?php }
                       if($page_id=='by_appointment'){
                          $sql=sqlStatement("select id from users where username='".$provider."'");
                         $row=sqlFetchArray($sql);
                         $id=$row['id'];
                            $sql_apppatients3=sqlStatement("SELECT visit_categories FROM tbl_allcare_facuservisit WHERE  `users`  REGEXP ('".":\"".$id."\"')");
                             while($row_apppatients3=sqlFetchArray($sql_apppatients3)){
                                  $array[]=unserialize($row_apppatients3['visit_categories']);
                             }
                             $dataArray = array();
                               for($j = 0; $j<count($array); $j++){
                                   foreach($array[$j] as $arraykey){
                                        $dataArray[] = $arraykey;
                                   }
                               }
                               $enc_val = '';
                               $dataarray = array_unique($dataArray);
                               foreach($dataarray as $arrayval){
                                   $enc_val .= $arrayval.",";
                               }
                               $enc_value = rtrim($enc_val,",");
                               $sql=sqlStatement("select DISTINCT DATE_FORMAT(ope.pc_eventdate,'%Y-%m-%d') as datevalue from openemr_postcalendar_events ope where ope.pc_aid=$id AND ope.pc_catid IN ($enc_value) ORDER BY ope.pc_eventdate ");
                               while($row_app=sqlFetchArray($sql)){
                                $apptdates[]=$row_app['datevalue'];
                             }
                              $today1=date('Y-m-d');
                               echo "<select id='app_date' name='app_date' onchange='javascript:dropdownchange();'>";
                               echo "<option value='$today1'>$today1</option>";
                               foreach($apptdates as $app_key => $app_value){ ?>
                                            <option value='<?php echo $app_value; ?>' <?php if($_REQUEST['app_date']==$app_value) echo "selected"; ?>><?php echo $app_value; ?></option>
                               <?php }
                               echo "</select>";
                               echo "<input type='hidden' name='enc_val' id='enc_val'  value='$enc_value' />";
                       }
                       ?>
                        <input type='hidden' name='provider' id='provider' value='<?php echo $provider; ?>'     />
                        <input type='hidden' name='id' id='id' value='<?php echo $page_id; ?>'     />  
                        <input type="hidden" name="refer" id="refer" value="<?php echo $refer; ?>"
                       </form>
                       <div class="costmizecolumns">
                           <select id="showhidecolumns" multiple=multiple style="width:220px;"></select>
                       </div>
                       <?php 
                       display_db_table($provider,$page_id,$filters);
                        ?>
                    </div>   
                </div>
            </div>
          </div>
        </div>
        <?php include 'footer.php'; ?>  
    </div>
</div>
    <script>
        $(document).ready( function (){
            $("#help_dialog").draggable({ handle:'#header'});
            var opttext=[];
                windowresize();
                function windowresize(){
                    $('#showhidecolumns').empty();
                   $('#'+'<?php echo $page_id; ?>'+' thead tr th').each(function(index,elm){
                        var optiontext = $(elm).text();
                        opttext.push(optiontext);
                        $('#showhidecolumns').append("<option data-column='"+index+"'>"+optiontext+"</option>");
                    });
                }
                 var responsiveHelper;
                var breakpointDefinition = {
                    tablet: 1024,
                    phone : 480
                };
                 var table = $('#'+'<?php echo $page_id; ?>').DataTable({
                        "iDisplayLength": 25,
                     dom: 'T<\"clear\">lfrtip',
                   tableTools: {
                         "sSwfPath": "../interface/swf/copy_csv_xls_pdf.swf",
                        aButtons: [
                            {
                                sExtends: "xls",
                                sButtonText: "Save to Excel",
                            }       
                        ]
                    }
                });

                selectedoptions();
                intlizeselectbox();
                function selectedoptions(){
                   $('#'+'<?php echo $page_id; ?>'+' thead tr th').each(function(index,elm){
                        var selectedcolm = table.column(index);
                        if(selectedcolm.visible()==true){
                           $('#showhidecolumns option').eq(index).attr("selected","selected")
                       }
                    })
                }

                 function intlizeselectbox(){
                    $("#showhidecolumns").pqSelect({
                        multiplePlaceholder: 'Show / Hide Columns',
                        checkbox: true, //adds checkbox to options    
                        maxDisplay: 0,
                        search: false,
                        displayText: "columns {0} of {1} selected"
                    }).on("change", function(evt) {
                        var val = $(this).val();
                        $.each(opttext,function(index,elm){
                            var column = table.column(index);
                             if(val !=null){
                                if(val.indexOf(elm) !=-1)
                                    column.visible(true);
                                else
                                    column.visible(false);
                            }
                        })
                    });

                }
                $('#showeditdemographics,#createappointment').on("show.bs.modal", function(event){
                    var target = $(event.relatedTarget);
                    var modal = $(this);
                    var url = target.data("href");
                    modal.find(".modal-body").html("<iframe src='"+url+"' style='border:none; width:100%; height:896px'></iframe>");       
                });
       
        });

        function closeModalWindow(){
            $('#showeditdemographics').modal('hide');
        }
        function OpenWindowWithPost(url, windowoption, name, params)
        {
         var form = document.createElement("form");
         form.setAttribute("method", "post");
         form.setAttribute("action", url);
         form.setAttribute("target", name);
         for (var i in params){

             var input = document.createElement('input');
             input.type = 'hidden';
             input.name = i;
             input.value = params[i];
             form.appendChild(input);
         }
         document.body.appendChild(form);

         window.open("post.htm", name, windowoption);
         form.submit();
         document.body.removeChild(form);
        }

        function win1(relativeUrl)
        {
             var res = relativeUrl.split("?");
             var param2=res[1].split("&");
             var dict = new Array();
             for(var i=0; i<param2.length; i++){
                    var param1=param2[i].split("=");
                    dict[ param1[0] ] = param1[1];

               }
         OpenWindowWithPost(res[0], "width=1000, height=600, left=100, top=100, resizable=yes, scrollbars=yes", "NewFile", dict);
        }
        if($(window).width() > 767)
             $("#wrapper").addClass("active");        

        $("#menu-toggle").click(function(e) {
            e.preventDefault();
            $("#wrapper").toggleClass("active");
        });
    </script>
    <div class="modal fade" name = "showeditdemographics" id="showeditdemographics" tabindex="-1" role="dialog" aria-hidden="true" >
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="background-color:#46a1b4; border-radius: 5px 5px 0px 0px; ">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel" >Edit Demographics</h4>
                </div>
                <div class="modal-body" style="padding:15px;">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" name = "createappointment" id="createappointment" tabindex="-1" role="dialog" aria-hidden="true" >
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="background-color:#46a1b4; border-radius: 5px 5px 0px 0px; ">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel" >Create Appointment</h4>
                </div>
                <div class="modal-body">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    
</body>