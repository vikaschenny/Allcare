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


$order=$_REQUEST['order'];
$provider=$_REQUEST['provider'];
$page_id=$_REQUEST['id'];


function display_db_query($query_string,$provider,$proid,$page_id) {
// perform the database query
$result_id = mysql_query($query_string)
or die("display_db_query:" . mysql_error());
// find out the number of columns in result
$column_count = mysql_num_fields($result_id)
or die("display_db_query:" . mysql_error());
// Here the table attributes from the $table_params variable are added


echo "<br><br><br>";
print("<TABLE  id='$page_id'cellpadding='0' cellspacing='0' border='0' class='table table-bordered table-striped'style=' word-wrap: break-word ; table-layout:fixed !important;  width: 100% !important;'>\n");
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
<!-- <td> <a href='javascript:;' onclick=win1('providers_patient_edit.php?set_pid=<?php echo $row[$column_num] ; ?>&provider=<?php echo $provider; ?>') >     
               <span><?php echo $row[$column_num]; ?></span></a></td>-->
                <td><a class="various" data-fancybox-type="iframe" href="providers_patient_edit.php?set_pid=<?php echo $row[$column_num] ; ?>&provider=<?php echo $provider; ?>"><?php echo $row[$column_num] ; ?></a></td>
           <?php  }else{
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
    $avail3=explode("|",$row1_vis['provider_plist']);
    foreach($avail3 as $val6){
        if(in_array($val6, $lists)){
            $available[]=$val6;
        }

    }
    

  $sql1=sqlStatement("SELECT * from tbl_allcare_providers_fieldsorder where username='$provider' AND menu='plist'");
  $row1=sqlFetchArray($sql1);
  if(!empty($row1)){
      $orders=explode(",",$row1['order_of_columns']);
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
                     $field1.="p.pid,";
               }
          }elseif($field[1]=='Patient_Name'){
              if (in_array($field[1], $available)){
                $field1.="CONCAT(p.title ,'',fname,',',lname) as Name,";
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
              AND o.pc_eventDate = DATE_FORMAT( fe.date,  '%Y-%m-%d' ) ";
          
          //filters
        if($page_id=='my_patients'){
            if($filters!=''){
              $sql12.=" where o.pc_aid =$id AND $filters ";
            }else{
              $sql12.=" where o.pc_aid =$id AND deceased_stat <> 'YES' AND practice_status ='YES' ";
            }
        }
        if($page_id=='all_patients'){
          
            if($filters!=''){
              $sql12.=" where  $filters ";
            }else{
              $sql12.=" where sudo_required = 'YES' ";
            }
        }
        
         if($page_id=='by_facility'){
          
            if($filters!=''){
              $filter=explode("$",$filters); 
             
              if($filter[0]!='' && $filter[1]=='0'){
                 $sql12.=" where fe.pid not in (SELECT DISTINCT(patientid) FROM tbl_patientfacility) AND   $filter[0]";
              }elseif($filter[0]!='' && $filter[1]!=''){
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
              $sql12.=" where fe.pid not in (SELECT DISTINCT(patientid) FROM tbl_patientfacility) AND deceased_stat <> 'YES' AND practice_status ='YES' ";
            }
        }
        if($page_id=='by_appointment'){
            if($filters!=''){
                 $filter1=explode("$",$filters);
                 if($filter1[0]!='' && $filter1[1]!='' ){
                    $sql12='';
                     $sql12=" SELECT $field2
                        FROM patient_data p INNER JOIN openemr_postcalendar_events ope ON p.pid=ope.pc_pid
                                inner join openemr_postcalendar_categories pc on pc.pc_catid=ope.pc_catid 
                                                        inner join list_options lo on lo.option_id=ope.pc_apptstatus
                        WHERE ope.pc_aid=$id AND ope.pc_eventdate='$filter1[0]' and p.practice_status = 'YES' AND p.deceased_stat != 'YES' AND (p.deceased_date = '' OR p.deceased_date = '0000-00-00 00:00:00' ) and ope.pc_catid IN ($filter1[1])  order by p.lname, p.fname ";
                     
                 }
            }else{
               $today=date("Y-m-d"); 
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
                         
                      $sql12=" SELECT $field2
                        FROM patient_data p INNER JOIN openemr_postcalendar_events ope ON p.pid=ope.pc_pid
                                inner join openemr_postcalendar_categories pc on pc.pc_catid=ope.pc_catid 
                                                        inner join list_options lo on lo.option_id=ope.pc_apptstatus
                        WHERE ope.pc_aid=$id AND ope.pc_eventdate='$today' and p.practice_status = 'YES' AND p.deceased_stat != 'YES' AND (p.deceased_date = '' OR p.deceased_date = '0000-00-00 00:00:00' ) and ope.pc_catid IN ($enc_value)   ";
            }
        }
       if($page_id=='by_appointment'){
        if($filters==''){
             $sql12.=" group by p.pid order by p.lname, p.fname";
        }   
       
       }else{
           $sql12.=" group by p.pid ";
       }
      
  }
  else {
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
         $sql12="SELECT $avail_field2 from form_encounter fe  INNER JOIN patient_data p ON p.pid=fe.pid  INNER JOIN openemr_postcalendar_events o ON o.pc_pid = fe.pid "
          . " AND o.pc_catid = fe.pc_catid AND fe.facility_id = o.pc_facility
              AND o.pc_eventDate = DATE_FORMAT( fe.date,  '%Y-%m-%d' )";
          
           //filters
        if($page_id=='my_patients'){
          
            if($filters!=''){
              $sql12.=" where o.pc_aid =$id AND $filters ";
            }else{
              $sql12.=" where o.pc_aid =$id AND deceased_stat <> 'YES' AND practice_status ='YES' ";
            }
        }
        if($page_id=='all_patients'){
          
            if($filters!=''){
              $sql12.=" where  $filters ";
            }else{
              $sql12.=" where sudo_required = 'YES'   ";
            }
        }
        if($page_id=='by_facility'){
          
            if($filters!=''){
              $filter=explode("$",$filters); 
              
              if($filter[0]!='' && $filter[1]=='0'){
                 $sql12.=" where fe.pid not in (SELECT DISTINCT(patientid) FROM tbl_patientfacility) AND $filter[0]";
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
              $sql12.=" where fe.pid not in (SELECT DISTINCT(patientid) FROM tbl_patientfacility) AND deceased_stat <> 'YES' AND practice_status ='YES' ";
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
        
  }
    display_db_query($sql12, $provider,$row['id'],$page_id);
}    
}
?>
    <head>
        <script type='text/javascript' src='../interface/main/js/jquery-1.11.1.min.js'></script>
           <style>
        .css_button_small {
        -moz-font-feature-settings: normal;
        -moz-font-language-override: normal;
        -moz-text-decoration-color: -moz-use-text-color;
        -moz-text-decoration-line: none;
        -moz-text-decoration-style: solid;
        -x-system-font: none;
        background-attachment: scroll;
        background-clip: border-box;
        background-color: transparent;
        background-image: url("../../../images/bg_button_a_small.gif");
        background-origin: padding-box;
        background-position: right top;
        background-repeat: no-repeat;
        background-size: auto auto;
        color: #444;
        display: block;
        float: left;
        font-family: arial,sans-serif;
        font-size: 9px;
        font-size-adjust: none;
        font-stretch: normal;
        font-style: normal;
        font-variant: normal;
        font-weight: bold;
        height: 19px;
        line-height: normal;
        margin-right: 3px;
        padding-right: 10px;
        }

        .css_button_small span {
        background-attachment: scroll;
        background-clip: border-box;
        background-color: transparent;
        background-image: url("../../../images/bg_button_span_small.gif");
        background-origin: padding-box;
        background-position: 0 0;
        background-repeat: no-repeat;
        background-size: auto auto;
        display: block;
        line-height: 20px;
        padding-bottom: 0;
        padding-left: 10px;
        padding-right: 0;
        padding-top: 0;
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
    </script>
      <link rel="stylesheet" href="css/dataTables.bootstrap.css"/>
            <link rel="stylesheet" href="css/datatables.responsive_bootstrap.css"/>
            <script src="js/responsive_datatable/jquery.min.js"></script>
            <script src="js/responsive_datatable/jquery.dataTables.min.js"></script>
            <script src="js/responsive_datatable/dataTables.bootstrap.js"></script>
            <script src="js/responsive_datatable/datatables.responsive.js"></script>
            <link rel="stylesheet" href="fancybox/source/jquery.fancybox.css" type="text/css" media="screen" />
            <script type="text/javascript" src="fancybox/source/jquery.fancybox.pack.js"></script>
    </head>
<body style="background-color:#FFFFCC;" >
<?php 
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
<form action="providers_patient.php" method="post" name="patients_filters">
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
                     <option value="<?php echo $row_mypatients['notes']; ?>" <?php if($filters!='' && $filters==$row_mypatients['notes'] )  echo "selected"; elseif($filters=='' && $row_mypatients['title']=="Sudo Active") echo "selected"; ?>><?php echo $row_mypatients['title']; ?></option>
             <?php }
            echo "</select>"; 
}
if($page_id=='by_facility'){
    $sql_mypatients2=sqlStatement("SELECT * FROM `list_options`  where list_id='Mobile_Facility_Filters' order by seq");
            echo "<select name='fac_filters' id='fac_filters' >";
            echo "<option  value=''>select</option>";
              while($row_mypatients2=sqlFetchArray($sql_mypatients2)){ ?>
                     <option value="<?php echo $row_mypatients2['notes']; ?>" <?php if($filters!='' && $_REQUEST["fac_filters"]==$row_mypatients2['notes']) echo "selected"; elseif($row_mypatients2['title']=="Practice Active") echo "selected"; ?>><?php echo $row_mypatients2['title']; ?></option>
             <?php }
            echo "</select>&nbsp;&nbsp;&nbsp;"; 
    
    $sql_mypatients3=sqlStatement("SELECT *  FROM facility WHERE service_location!=0");
            
            echo "<select name='fac_filters1' id='fac_filters1' >";
            echo "<option  value='0'>select</option>";
              while($row_mypatients3=sqlFetchArray($sql_mypatients3)){ ?>
                     <option value="<?php echo $row_mypatients3['id']; ?>" <?php if($_REQUEST["fac_filters1"]==$row_mypatients3['id']) echo "selected"; ?>><?php echo $row_mypatients3['name']; ?></option>
             <?php }
            echo "</select>";    
    ?>        
   <a href="javascript:;"  class="btn btn-default" onclick="submitme();">
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
</form>
 
<?php 

 
  
display_db_table($provider,$page_id,$filters);
 ?>    
<script type='text/javascript'>
$(document).ready( function () {
                 //datatable
                             var responsiveHelper;
                            var breakpointDefinition = {
                                tablet: 1024,
                                phone : 480
                            };
                            var tableElement = $('#'+'<?php echo $page_id; ?>');
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

            } );
    </script>
</body>