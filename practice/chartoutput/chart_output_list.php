<?php
 // Copyright (C) 2011 Cassian LUP <cassi.lup@gmail.com>
 // 
 // Moved out of individual get_* portal functions for re-use by
 // Kevin Yeh (kevin.y@integralemr.com) May 2013
 //
 // This program is free software; you can redistribute it and/or
 // modify it under the terms of the GNU General Public License
 // as published by the Free Software Foundation; either version 2
 // of the License, or (at your option) any later version.
 // 
 // 
 
    // All of the common intialization steps for the get_* patient portal functions are now in this single include.

    //SANITIZE ALL ESCAPES
    $sanitize_all_escapes=true;

    //STOP FAKE REGISTER GLOBALS 
    $fake_register_globals=false;

    //continue session
    session_start();

    //landing page definition -- where to go if something goes wrong
    $landingpage = "index.php?site=".$_SESSION['site_id'];	
    //

    

    $ignoreAuth=true; // ignore the standard authentication for a regular OpenEMR user
    include_once('../../interface/globals.php');
    include_once("chartoutput_lib.php");

    $group_name1 =  $_REQUEST['group_name'] ; 
    $group_name2 = substr($group_name1,0, 1);
    echo $provider=$_REQUEST['provider'];
    $pid=$_REQUEST['pid'] ? $_REQUEST['pid'] :$pid;
?>
<html>
<head>
<?php html_header_show();?>

<script language="javascript">
// Called by the deleteme.php window on a successful delete.
function imdeleted() {
 top.restoreSession();
 location.href = 'add_chartoutput.php';
}

function win1(url){
     // alert(url);
    window.open(url,'popup','width=900,height=900,scrollbars=no,resizable=yes');
}
</script>
<style>
    #chart_data td ul li{
       display: block;
    }
    .DTTT.btn-group{
        float: right;
        padding-left: 13px;
        position: relative;
    }
    #chart_data_length{
        float:left;
    }
    @media only screen and (max-width: 620px){
        .DTTT.btn-group{
            float: none;
            margin-bottom: 6px;
            padding-left: 40%;
            position: relative;
        }
        #chart_data_length{
            float:none;
        }

    }
</style>
</head>
<body class="body_top">
<?php if($provider!=''){ 
    $group_name=substr($_REQUEST['group_name'],1);
    
    $field_id=array();
    $title=array();
 
    $fres = sqlStatement("SELECT * FROM layout_options " .
    "WHERE form_id = 'CHARTOUTPUT' AND uor > 0 AND field_id != ''  AND group_name LIKE '%$group_name%'" .
    "ORDER BY group_name, seq");
      while ($frow = sqlFetchArray($fres)) {
        $data_type = $frow['data_type'];
        $field_id[]  = $frow['field_id'];
        $value = $_POST["form_$field_id"];
        $title[] = $frow['title'];
        $sets .=  add_escape_custom($field_id);

        }
  
     //for who & notes
    $labels3 = sqlStatement("SELECT * FROM layout_options " . 
     "WHERE form_id = 'F2F' AND uor > 0 AND data_type!=36 " .
     "ORDER BY group_name, seq" );
    while ($labels_d = sqlFetchArray($labels3)) {
         $field_id_f[]  = $labels_d['field_id'];
          $title_f[] = $labels_d['title'];

   }   
    ?>

<table  id='chart_data' cellpadding='0' class='table table-striped table-bordered dt-responsive nowrap'  width='100%'>
    <thead>
        <tr><th data-class='expand'>Print</th><th>Edit</th><th>Delete</th><th>Type</th><th>Date</th><th>Date Of Service</th>
            <?php  foreach($title as $label) { ?>
                     <th data-hide='phone' data-name='<?php echo $label ; ?>'><?php echo $label; ?></th>
            <?php }
            echo "<th style='width:200px;'>".htmlspecialchars( xl('Who'), ENT_NOQUOTES)."&nbsp;</th>" ;
            echo "<th style='width:200px;'>".htmlspecialchars( xl('Provider'), ENT_NOQUOTES)."&nbsp;</th>" ;
            echo "<th style='width:200px;'>".htmlspecialchars( xl('Facility'), ENT_NOQUOTES)."&nbsp;</th>" ;
            echo "<th style='width:200px;'>".htmlspecialchars( xl('Pharmacy'), ENT_NOQUOTES)."&nbsp;</th>" ;
            echo "<th style='width:200px;'>".htmlspecialchars( xl('Payer'), ENT_NOQUOTES)."&nbsp;</th>" ;
            echo "<th style='width:200px;'>".htmlspecialchars( xl('Notes'), ENT_NOQUOTES)."&nbsp;</th>" ;
             ?>
        </tr>
    </thead>
    <?php $group_name=substr($_REQUEST['group_name'],1); 
          if ($result = getChartOutputByPid($pid, $group_name)) {
                //echo "<pre>"; print_r($result); echo "</pre>";
                foreach ($result as $iter) {
                    $trans_type2=$iter{'trans_type'};
                    $transaction=$iter{'transaction'};
                    //for face to face
                    $enc=sqlStatement("select * from form_encounter where  DATE_FORMAT(date, '%Y-%m-%d')='".$iter['date_of_service']."' and pid=$pid" );
                    $renc=sqlFetchArray($enc);
                         $id=$iter['id'];
                         $id=$iter['id'];
                         $encounter=$renc['encounter'];
                        // $dos=$iter['date_of_service'];
                     ?>
                    <tr>
                        <td>
                           <?php if($transaction==2) { 
                                      $tr_name='f2f';    
                                     print "<a href='javascript:;' onclick=win1('f2f/print_f2fchart.php?f2fid=$id&encounter_id=$encounter&patient_id=$pid&date_of_service=".$iter['date_of_service']."') class='welcome-btn1' >            
                                    <span>".htmlspecialchars( xl('Print'), ENT_NOQUOTES)."</span></a>";

                                 } else { 
                                     $tr_name='med';
                                     echo "<a href='javascript:;' onclick=win1('chartoutput/print_charts.php?coid=$id&pid=$pid&group=$group_name1',event) class='welcome-btn1' >               
                                         <span>".htmlspecialchars( xl('Print'), ENT_NOQUOTES)."</span></a>"; 
                                 } ?>
                        </td>
                        <td>
                            <?php   
                                echo "<a class='various' data-title='Edit Chart' href='#' data-frameheight='420' data-modalsize='modal-lg' data-bodypadding='0' data-href='chartoutput/chart_output.php?coid=".htmlspecialchars( $iter{"id"}, ENT_NOQUOTES).
                                    "&inmode=edit&group_name2=$group_name2&group_name=$group_name1&location=provider_portal&provider=$provider&pid=$pid&refer=$refer&type=$trans_type2' data-toggle='modal' data-target='#modalwindow' style='background-color:#49C1DC;
                                     margin-top: 20px; color: #fff; border-radius:20px; font: bold 10px arial, sans-serif;  transition: all 0.3s ease-in;  padding: 8px 10px;   border: 2px solid #fff;'>".htmlspecialchars( xl('Edit'), ENT_NOQUOTES)."</a>";
                            ?>
                        </td>
                        <td>
                            <?php 
                                echo "<a class='various' data-title='Delete Chart' href='#' data-frameheight='100%' data-modalsize='modal-md' data-bodypadding='0' data-href='chartoutput/deleter.php?coid=".
                                     htmlspecialchars( $iter{"id"}, ENT_QUOTES).
                                     "&patient_id=$pid&location=provider_portal&provider=$provider&refer=$refer&trans=medrec' data-toggle='modal' data-target='#modalwindow' style='background-color:#49C1DC;
                                     margin-top: 20px; color: #fff; border-radius:20px; font: bold 10px arial, sans-serif;  transition: all 0.3s ease-in;  padding: 8px 10px;   border: 2px solid #fff;' onclick='getdeleteelem(this)'>".htmlspecialchars( xl('Delete'), ENT_NOQUOTES)."</a>";
                            ?>
                        </td>
        <!--                transaction type-->
                               <td style='width:200px;'>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $iter{'trans_type'}." ( ". $tr_name ." )" ; ?>&nbsp; </td>

                        <?php   //transaction date            
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

                                
                                //date of service
                                if($iter{'date_of_service'}!='')  { 

                                    echo " <td style='width:200px;'>" .
                                    htmlspecialchars( (trim($iter{'date_of_service'},",")), ENT_NOQUOTES). "</td>"; 
                                }
                                else {
                                   echo "<td style='width:200px;'>&nbsp;</td>";
                                }
                                //layout fields
                                foreach($field_id as $attr)
                                { 
                                 echo " <td>" .
                                htmlspecialchars( ($iter{$attr}), ENT_NOQUOTES). "&nbsp;</td>";
                                }
                                
                                //refer_to
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
                               // pharmacy
                                if($iter{'pharmacy'}!='' && $iter{'pharmacy'}!='null'){
                                    $ph2=sqlStatement("SELECT name FROM pharmacies where id='".$iter{'pharmacy'}."'");
                                    $res5=sqlFetchArray($ph2);
                                    echo " <td style='width:200px;'>&nbsp;&nbsp;&nbsp;&nbsp;" .
                                    htmlspecialchars( ($res5{name}), ENT_NOQUOTES). "&nbsp;</td>";
                                }else {
                                    echo " <td style='width:200px;'>&nbsp;</td>";
                                }
                                //payer
                                if($iter{'payer'}!='' && $iter{'payer'}!='null'){
                                    $pay2=sqlStatement("SELECT name FROM insurance_companies where id='".$iter{'payer'}."'");
                                    $res6=sqlFetchArray($pay2);
                                    echo " <td style='width:200px;'>&nbsp;&nbsp;&nbsp;&nbsp;" .
                                    htmlspecialchars( ($res6{name}), ENT_NOQUOTES). "&nbsp;</td>";
                                }else {
                                    echo " <td style='width:200px;'>&nbsp;</td>";
                                }

                                echo " <td style='width:200px;'>&nbsp;&nbsp;&nbsp;&nbsp;" .
                                    htmlspecialchars( ($iter{'notes'}), ENT_NOQUOTES). "&nbsp;</td>";
                                echo "</tr>\n"; ?>
                    </tr>
           <?php } 
    
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
                $trans_type3=$iter1{'trans_type'}; ?>
                 <tr>
                <td>
                   <?php
                        $sql1=sqlStatement("select * from  list_options where list_id='form_templates' and option_id='".$iter1{'form_template'}."'");
                        $row1=sqlFetchArray($sql1);
                        $notes=$row1['notes']; 
                        echo "<a href='javascript:;' onclick=win1('chartoutput/template_forms/$notes?coid=".$iter1['id']."&patient_id=$pid&group=".$group_name1."&temp_id=".$iter1['form_template']."&print=1') class='welcome-btn1' >               
                              <span>".htmlspecialchars( xl('Print'), ENT_NOQUOTES)."</span></a>"; 
                    ?>
                </td>
                <td>
                    <?php   
                        echo "<a class='various' data-fancybox-type='iframe' href='chartoutput/chart_output.php?coid=".htmlspecialchars( $iter1{"id"}, ENT_NOQUOTES).
                            "&inmode=edit&group_name2=$group_name2&group_name=$group_name1&location=provider_portal&provider=$provider&pid=$pid&refer=$refer&type=$trans_type3' style='background-color:#49C1DC;
                             margin-top: 20px; color: #fff; border-radius:20px; font: bold 10px arial, sans-serif;  transition: all 0.3s ease-in;  padding: 8px 10px;   border: 2px solid #fff;'>".htmlspecialchars( xl('Edit'), ENT_NOQUOTES)."</a>";
                    ?>
                </td>
                <td>
                    <?php 
                        echo "<a class='various' data-fancybox-type='iframe' href='chartoutput/deleter.php?coid=".
                             htmlspecialchars( $iter1{"id"}, ENT_QUOTES).
                             "&patient_id=$pid&location=provider_portal&provider=$provider&refer=$refer&trans=medrec&nonenc=1' style='background-color:#49C1DC;
                             margin-top: 20px; color: #fff; border-radius:20px; font: bold 10px arial, sans-serif;  transition: all 0.3s ease-in;  padding: 8px 10px;   border: 2px solid #fff;' onclick='getdeleteelem(this)'>".htmlspecialchars( xl('Delete'), ENT_NOQUOTES)."</a>";
                    ?>
                </td>
<!--                transaction type-->
                       <td style='width:200px;'>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $iter1{'trans_type'} ; ?>&nbsp; </td>
                                   
                <?php   //transaction date            
                        if($iter1{'date'}!='')  {  
                     
                             echo " <td style='width:200px;'>&nbsp;&nbsp;&nbsp;&nbsp;" .
                                    htmlspecialchars( ($iter1{'date'}), ENT_NOQUOTES). "&nbsp;</td>";  
                        }
                        else {

                            echo " <td style='width:200px;'>&nbsp;&nbsp;&nbsp;&nbsp;" .
                                    htmlspecialchars( (''), ENT_NOQUOTES). "&nbsp;</td>";  
                        }       
                         echo " <td style='width:200px;'>&nbsp;</td>";
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
                        if($iter1{'refer_to'}!='null' && $iter1{'refer_to'}!=''){
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
                    if($iter1{'payer'}!='' && $iter1{'payer'}!='null'  && $iter1{'payer'}!='0'){
                        $pay2=sqlStatement("SELECT name FROM insurance_companies where id='".$iter1{'payer'}."'");
                        $res6=sqlFetchArray($pay2);
                        echo " <td style='width:200px;'>&nbsp;&nbsp;&nbsp;&nbsp;" .
                        htmlspecialchars( ($res6{name}), ENT_NOQUOTES). "&nbsp;</td>";
                    }else {
                        echo " <td style='width:200px;'>&nbsp;</td>";
                    }
                    
                    if($iter1{'notes'}!=''){
                        echo " <td style='width:200px;'>&nbsp;&nbsp;&nbsp;&nbsp;" .
                            htmlspecialchars( ($iter1{'notes'}), ENT_NOQUOTES). "&nbsp;</td>";
                    }else {
                        echo " <td style='width:200px;'>&nbsp;</td>";
                    }
                        
                     ?>
            </tr>

           <?php }
        }?>
</table>



<?php } ?>

<script type='text/javascript'>
    var currentrow=null;
    function getdeleteelem(target){
        currentrow = target;
    }
   var tableElement = $('#chart_data').dataTable({
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
    function deleterow(){
        tableElement.fnDeleteRow($(currentrow).parents("tr[role='row']"));
        closefancybox();
    }
</script>
</body>  
</html>