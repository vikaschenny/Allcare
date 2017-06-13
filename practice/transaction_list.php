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
//include_once("chartoutput/chartoutput_lib.php");

$pagename = "plist"; 
if(isset($_SESSION['portal_username']) !=''){
   $provider=$_SESSION['portal_username'];
}else {
   $provider=$_REQUEST['provider'];
   $refer=$_REQUEST['refer']; 
   $_SESSION['refer']=$_REQUEST['refer'];
   $_SESSION['portal_username']=$_REQUEST['provider'];
}

$base_url="https://".$_SERVER['SERVER_NAME'].dirname($_SERVER["REQUEST_URI"].'?').'/';

 $sql=sqlStatement("SELECT id, fname, lname, specialty FROM users " .
      "WHERE active = 1 AND ( info IS NULL OR info NOT LIKE '%Inactive%' ) " .
      "AND authorized = 1 AND username='".$provider."'" .
      "ORDER BY lname, fname");
$id=sqlFetchArray($sql);
$id1=$id['id'];

//params
echo $pid=$_REQUEST['form_patient'];


function getChartOutputByPid ($pid, $group_name, $cols = "*")
{
    
  $labels = sqlStatement("select field_id from layout_options where group_name  LIKE '%$group_name%' and form_id = 'CHARTOUTPUT'" );
  while ($labels2 = sqlFetchArray($labels)) {
        $titles .= $labels2['field_id'].',' ; 
         $columncheck .= $labels2['field_id']." <> '' OR ";
  }
  $titles2 = rtrim($titles ,',');
  $columncheck2 = rtrim($columncheck, ' OR ');
  
 
  
  //return $allstring;  
  
  //$all = '';
  if(!empty($group_name)){
      
      $res = sqlStatement("select id,refer_to,provider,facility,pharmacy,payer,notes,dos,form_template,trans_type,created_date,updated_date,transaction,date_of_service,chart_group,$titles2 from tbl_form_chartoutput_transactions where pid=$pid AND ($columncheck2)
        order by id DESC ");

      for ($iter = 0; $row = sqlFetchArray($res); $iter++) 
        $all[$iter] = $row;
      return $all;
  }
  
}
?>
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">
        <link rel="stylesheet" href="css/version1.0/dataTables.bootstrap.min.css"/>
        <link rel="stylesheet" href="css/version1.0/responsive.bootstrap.min.css"/>
        <script src="js/responsive_datatable/version1.0/jquery-1.11.3.min.js"></script>
        <script src="js/responsive_datatable/version1.0/jquery.dataTables.min.js"></script>
        <script src="js/responsive_datatable/version1.0/dataTables.bootstrap.min.js"></script>
        <script src="js/responsive_datatable/version1.0/dataTables.responsive.min.js"></script>
        <script type='text/javascript' src='js/responsive_datatable/dataTables.tableTools.js'></script>
        <script type='text/javascript' src='js/responsive_datatable/dataTables.bootstrap.js'></script>
        <script type="text/javascript" src="assets/js/bootstrap.min.js"></script>
        <style>
        #transaction_data td ul li{
           display: block;
        }
        .DTTT.btn-group{
            float: right;
            padding-left: 13px;
            position: relative;
        }
        #transaction_data_length{
            float:left;
        }
        table.dataTable > tbody > tr.child ul {
            width: 100%;
        }
        @media only screen and (max-width: 620px){
            .DTTT.btn-group{
                float: none;
                margin-bottom: 6px;
                padding-left: 40%;
                position: relative;
            }
            #transaction_data_length{
                float:none;
            }
            
        }
    </style>
    <script>
        function transation_type() {
            if($('#type').val()=='Patient_Encounter_Specific'){
               document.getElementById('temp').style.display='block';
            }
        }
    </script>
    </head>
    <body>
        <form name='transaction' id='transaction' action='' method='post' >
        <input type='hidden' id='form_patient' name='form_patient' value='<?php echo $pid; ?>' />    
        <label>Group:</label> 
        <?php
            $sql = sqlStatement("SELECT DISTINCT(group_name) as group_name FROM layout_options " .
                                    "WHERE form_id = 'CHARTOUTPUT' AND uor > 0 " .
                                    "ORDER BY group_name"); 
            echo "<select id='group' name='group' onchange='group_selection();' ><option value=''>Select</option>";
            while($row=sqlFetchArray($sql)) {
                $id=$row['group_name'];
                $title=substr($row['group_name'],1);
                echo "<option value='$id'"; if($_REQUEST['group']==$id) echo "selected"; echo">$title</option>";
            }
            echo "</select>";
        ?>
        <br><label>Transaction Type:</label><select id="type" name="type" onchange="transation_type();"><option value="">Select</option><option value="Patient_Specific">Patient Specific</option><option value="Patient_Encounter_Specific">Patient Encounter Specific</option></select><br>
        <br>
        <div id="temp" style="display:none;"> Encounter Specific Type:<select name='enc_type' id='enctype' ><option value=''>Select</option><option value='1'>Medical Record</option><option value='2'>Face to Face</option></select></div>
        <input type="submit" name="submit" id="submit" value="submit" />
         </form>
        <?php
            $group_name=substr($_REQUEST['group'],1);
            $enc_type=$_REQUEST['enc_type'];
            $fres = sqlStatement("SELECT * FROM layout_options " .
                                "WHERE form_id = 'CHARTOUTPUT' AND uor > 0 AND field_id != ''  AND group_name LIKE '%$group_name%'" .
                                " ORDER BY group_name, seq");
            $title    =array();
            $field_id =array();
            while ($frow = sqlFetchArray($fres)) {
                $field_id[]  = $frow['field_id'];
                $title[]     = $frow['title'];
            }
            echo "<table  id='transaction_data' cellpadding='0' class='table table-striped table-bordered dt-responsive nowrap'  width='100%'>";
            echo "<thead>";
            echo "<tr><th>Type</th><th>Date</th><th>Group</th>";
             foreach($title as $label) {
                    echo " <th data-hide='phone' data-name='$label'>$label</th>";
            }
            echo "<th style='width:200px;'>".htmlspecialchars( xl('Who'), ENT_NOQUOTES)."&nbsp;</th>" ;
            echo "<th style='width:200px;'>".htmlspecialchars( xl('Provider'), ENT_NOQUOTES)."&nbsp;</th>" ;
            echo "<th style='width:200px;'>".htmlspecialchars( xl('Facility'), ENT_NOQUOTES)."&nbsp;</th>" ;
            echo "<th style='width:200px;'>".htmlspecialchars( xl('Pharmacy'), ENT_NOQUOTES)."&nbsp;</th>" ;
            echo "<th style='width:200px;'>".htmlspecialchars( xl('Payer'), ENT_NOQUOTES)."&nbsp;</th>" ;
            echo "<th style='width:200px;'>".htmlspecialchars( xl('Notes'), ENT_NOQUOTES)."&nbsp;</th>" ;
            echo " </tr>";
            echo "</thead>";
            if ($result = getChartOutputByPid($pid, $group_name,)) {
                
                foreach ($result as $iter) {
                    $transaction=$iter{'transaction'};
                    if($transaction==2) $tr_name='f2f';    
                    else $tr_name='med';
                    echo"<tr>";
                    //trasaction type
                    echo"<td style='width:200px;'>".$iter{'trans_type'}."( $tr_name )&nbsp; </td>";
                    //date
                    if($iter{'updated_date'}=='')  { 
                        $create=explode(" ",$iter{'created_date'});
                        echo " <td style='width:200px;'>&nbsp;&nbsp;&nbsp;&nbsp;" .
                        htmlspecialchars( ($create[0]), ENT_NOQUOTES). "&nbsp;</td>"; 
                    }
                    else {
                        $update=explode(" ",$iter{'updated_date'});
                        echo " <td style='width:200px;'>&nbsp;&nbsp;&nbsp;&nbsp;" .
                        htmlspecialchars( ($update[0]), ENT_NOQUOTES). "&nbsp;</td>";
                    }
                    
                    //group
                    echo"<td style='width:200px;'>".substr($iter{'chart_group'},1)."&nbsp; </td>";
                    //layout field values
                    foreach($field_id as $attr){ 
                        echo " <td>" .
                                htmlspecialchars( ($iter{$attr}), ENT_NOQUOTES). "&nbsp;</td>";
                    }
                    //refer to
                    if($iter{'refer_to'}!='null' && $iter{'refer_to'}!=''){
                        $users1=sqlStatement("select organization from users where id='".$iter{'refer_to'}."'");
                        $res2=sqlFetchArray($users1);
                        if(!empty($res2)){
                              echo " <td style='width:200px;'>&nbsp;&nbsp;&nbsp;&nbsp;" .
                               htmlspecialchars( ($res2{organization}), ENT_NOQUOTES). "&nbsp;</td>";
                        }else {
                              $users=sqlStatement("select concat(lname,'',fname) as name from users where id='".$iter{'refer_to'}."'");
                              $res=sqlFetchArray($users);
                               echo " <td style='width:200px;'>&nbsp;&nbsp;&nbsp;&nbsp;" .
                               htmlspecialchars( ($res{name}), ENT_NOQUOTES). "&nbsp;</td>";
                        }

                    }else {
                         echo " <td style='width:200px;'>&nbsp;</td>";
                    }
                    //provider
                    if($iter{'provider'}!='' && $iter{'provider'}!='null'){
                        $users2=sqlStatement("select concat(lname,' ',fname) as name from users where id='".$iter{'provider'}."'");
                        $res3=sqlFetchArray($users2);
                        echo " <td style='width:200px;'>&nbsp;&nbsp;&nbsp;&nbsp;" .
                        htmlspecialchars( ($res3{name}), ENT_NOQUOTES). "&nbsp;</td>";
                    }else {
                         echo " <td style='width:200px;'>&nbsp;</td>";
                    }
                    
                    //facility
                    if($iter{'facility'}!='' && $iter{'facility'}!='null'){

                        $fac2=sqlStatement("SELECT name FROM facility where id='".$iter{'facility'}."'");
                        $res4=sqlFetchArray($fac2);
                        echo " <td style='width:200px;'>&nbsp;&nbsp;&nbsp;&nbsp;" .
                        htmlspecialchars( ($res4{name}), ENT_NOQUOTES). "&nbsp;</td>";
                    }else {
                        echo " <td style='width:200px;'>&nbsp;</td>";
                    }
                    //pharmacy
                    if($iter{'pharmacy'}!='' && $iter{'pharmacy'}!='null'){
                        $ph2=sqlStatement("SELECT name FROM pharmacies where id='".$iter{'pharmacy'}."'");
                        $res5=sqlFetchArray($ph2);
                        echo " <td style='width:200px;'>&nbsp;&nbsp;&nbsp;&nbsp;" .
                        htmlspecialchars( ($res5{name}), ENT_NOQUOTES). "&nbsp;</td>";
                    }else {
                        echo " <td style='width:200px;'>&nbsp;</td>";
                    }
                    
                    //payer
                    if($iter{'payer'}!='' && $iter{'payer'}!='null' && $iter{'payer'}!='0'){
                        $pay2=sqlStatement("SELECT name FROM insurance_companies where id='".$iter{'payer'}."'");
                        $res6=sqlFetchArray($pay2);
                        echo " <td style='width:200px;'>&nbsp;&nbsp;&nbsp;&nbsp;" .
                        htmlspecialchars( ($res6{name}), ENT_NOQUOTES). "&nbsp;</td>";
                    }else {
                        echo " <td style='width:200px;'>&nbsp;</td>";
                    }
                    //notes
                    if($iter{'notes'}!=''){
                        echo " <td style='width:200px;'>&nbsp;&nbsp;&nbsp;&nbsp;" .
                        htmlspecialchars( ($iter{'notes'}), ENT_NOQUOTES). "&nbsp;</td>";
                    }else {
                        echo " <td style='width:200px;'>&nbsp;&nbsp;&nbsp;&nbsp;" .
                        htmlspecialchars( (''), ENT_NOQUOTES). "&nbsp;</td>";
                    }
                    
                               
                    echo "</tr>";
                }
            }
             //for non encounter
            $field_id2=array();
            $title=array();
            $fres2 = sqlStatement("SELECT * FROM layout_options " .
                "WHERE form_id = 'NONENC' AND uor > 0 AND field_id != ''  AND group_name LIKE  '%$group_name%'" .
                "ORDER BY group_name, seq");
            while ($frow2 = sqlFetchArray($fres2)) {
                $field_id2[]  = $frow2['field_id'];
                $value = $_POST["form_$field_id"];
                $title[] = $frow2['title'];
            }
            if ($result1 = getNonEncChartByPid($pid, $group_name)) {
                foreach ($result1 as $iter1) {
                    echo "<pre>"; print_r($result1); echo "</pre>";
                    //transaction type  
                    if($iter1{'trans_type'}!=''){
                        echo"<td style='width:200px;'>&nbsp;&nbsp;&nbsp;&nbsp;".$iter1{'trans_type'}."&nbsp; </td>";
                    }else {
                        echo " <td style='width:200px;'>&nbsp;</td>";
                    }
                    
                   
                    //transaction date            
                    if($iter1{'date'}!='')  {  

                         echo " <td style='width:200px;'>&nbsp;&nbsp;&nbsp;&nbsp;" .
                                htmlspecialchars( ($iter1{'date'}), ENT_NOQUOTES). "&nbsp;</td>";  
                    }
                    else {

                        echo " <td style='width:200px;'>&nbsp;&nbsp;&nbsp;&nbsp;" .
                                htmlspecialchars( (''), ENT_NOQUOTES). "&nbsp;</td>";  
                    }
                    
                    //group
                    if($iter1{'chart_group'}!=''){
                         echo"<td style='width:200px;'>".substr($iter1{'chart_group'},1)."&nbsp; </td>";
                    }else {
                        echo " <td style='width:200px;'>&nbsp;</td>";
                    }
                   
                    //layout fields
                    foreach($field_id as $attr1)
                    { 
                        if(in_array($attr1,$field_id2)){
                            echo " <td>" .
                            htmlspecialchars( ($iter1{$attr1}), ENT_NOQUOTES). "&nbsp;</td>";
                        }else {
                            echo " <td style='width:200px;'>&nbsp;&nbsp;&nbsp;&nbsp;" .
                            htmlspecialchars( (''), ENT_NOQUOTES). "&nbsp;</td>";
                        }
                    }
                    ///refer_to
                    if($iter1{'refer_to'}!='null' && $iter1{'refer_to'}!='' && $iter1{'refer_to'}!='0'){
                        $users1=sqlStatement("select organization from users where id='".$iter1{'refer_to'}."'");
                        $res2=sqlFetchArray($users1);
                        if(!empty($res2)){
                              echo " <td style='width:200px;'>&nbsp;&nbsp;&nbsp;&nbsp;" .
                               htmlspecialchars( ($res2{organization}), ENT_NOQUOTES). "&nbsp;</td>";
                        }else {
                              $users=sqlStatement("select concat(lname,'',fname) as name from users where id='".$iter1{'refer_to'}."'");
                              $res=sqlFetchArray($users);
                               echo " <td style='width:200px;'>&nbsp;&nbsp;&nbsp;&nbsp;" .
                               htmlspecialchars( ($res{name}), ENT_NOQUOTES). "&nbsp;</td>";
                        }
                               
                    }else {
                         echo " <td style='width:200px;'>&nbsp;</td>";
                    } 
                    //provider
                    if($iter1{'provider'}!='' && $iter1{'provider'}!='null' && $iter1{'provider'}!='0'){
                        $users2=sqlStatement("select concat(lname,' ',fname) as name from users where id='".$iter1{'provider'}."'");
                        $res3=sqlFetchArray($users2);
                        echo " <td style='width:200px;'>&nbsp;&nbsp;&nbsp;&nbsp;" .
                        htmlspecialchars( ($res3{name}), ENT_NOQUOTES). "&nbsp;</td>";
                    }else {
                         echo " <td style='width:200px;'>&nbsp;</td>";
                    }
                    //facility
                    if($iter1{'facility'}!='' && $iter1{'facility'}!='null' && $iter1{'facility'}!='0'){
                        $fac2=sqlStatement("SELECT name FROM facility where id='".$iter1{'facility'}."'");
                        $res4=sqlFetchArray($fac2);
                        echo " <td style='width:200px;'>&nbsp;&nbsp;&nbsp;&nbsp;" .
                        htmlspecialchars( ($res4{name}), ENT_NOQUOTES). "&nbsp;</td>";
                    }else {
                        echo " <td style='width:200px;'>&nbsp;</td>";
                    }
                    //pharmacy    
                    if($iter1{'pharmacy'}!='' && $iter1{'pharmacy'}!='null' && $iter1{'pharmacy'}!='0'){
                        $ph2=sqlStatement("SELECT name FROM pharmacies where id='".$iter1{'pharmacy'}."'");
                        $res5=sqlFetchArray($ph2);
                        echo " <td style='width:200px;'>&nbsp;&nbsp;&nbsp;&nbsp;" .
                        htmlspecialchars( ($res5{name}), ENT_NOQUOTES). "&nbsp;</td>";
                    }else {
                        echo " <td style='width:200px;'>&nbsp;</td>";
                    }
                    //payer    
                    if($iter1{'payer'}!='' && $iter1{'payer'}!='null' && $iter1{'payer'}!='0'){
                        $pay2=sqlStatement("SELECT name FROM insurance_companies where id='".$iter1{'payer'}."'");
                        $res6=sqlFetchArray($pay2);
                        echo " <td style='width:200px;'>&nbsp;&nbsp;&nbsp;&nbsp;" .
                        htmlspecialchars( ($res6{name}), ENT_NOQUOTES). "&nbsp;</td>";
                    }else {
                        echo " <td style='width:200px;'>&nbsp;</td>";
                    }
                    //notes    
                    if($iter1{'notes'}!=''){
                         echo " <td style='width:200px;'>&nbsp;&nbsp;&nbsp;&nbsp;" .
                       htmlspecialchars( ($iter1{'notes'}), ENT_NOQUOTES). "&nbsp;</td>";
                    }else {
                        echo " <td style='width:200px;'>&nbsp;</td>";
                    }
                    
                }
            }
            echo "</table>";
        ?>
        <script type='text/javascript'>
            $(document).ready(function() {
                var tableElement = $('#transaction_data');
                tableElement.dataTable({
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
        });
        </script>
    </body>
</html>