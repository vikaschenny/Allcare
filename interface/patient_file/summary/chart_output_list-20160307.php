<?php

//SANITIZE ALL ESCAPES
$sanitize_all_escapes=true;
//

//STOP FAKE REGISTER GLOBALS
$fake_register_globals=false;
//

include_once("../../globals.php");
//include_once("$srcdir/transactions.inc"); 
include_once("$srcdir/chartoutput_lib.php");
require_once("$srcdir/options.inc.php");
$group_name1 =  $_REQUEST['group_name']; 
$group_name2 = substr($group_name,0, 1);
//print_r($_GET);
//print_r($_POST);
//print_r($_SESSION);
$provider=$_REQUEST['provider'];
$pid=$_REQUEST['pid'] ? $_REQUEST['pid'] :$pid;
?>
 
<html>
<head> 
<?php html_header_show();?>
<link rel="stylesheet" href="<?php echo $css_header;?>" type="text/css">
<script language="javascript">
// Called by the deleteme.php window on a successful delete.
function imdeleted() {
 top.restoreSession();
 location.href = 'summary/add_chartoutput.php';
}

function win1(url){
     // alert(url);
    window.open(url,'popup','width=900,height=900,scrollbars=no,resizable=yes');
}
</script>
<link rel="stylesheet" href="../../../providers/css/dataTables.bootstrap.css"/>
<link rel="stylesheet" href="../../../providers/css/datatables.responsive_bootstrap.css"/>
<script src="../../../providers/js/responsive_datatable/jquery.min.js"></script>
<script src="../../../providers/js/responsive_datatable/jquery.dataTables.min.js"></script>
 <script src="../../../providers/js/responsive_datatable/dataTables.bootstrap.js"></script>
<script src="../../../providers/js/responsive_datatable/datatables.responsive.js"></script>

</head>
<body class="body_top">
<br>
<?php
if($group_name1!=''){
    $group_name=substr($_REQUEST['group_name'],1);
    echo '<table class="showborder" cellspacing="0px" cellpadding="2px">';
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
    
         // Print Heading .. to have better Understanding of the Listed Transactions -- starts here Dec 07,09
	print "<tr class='showborder_head'><th style='width=10px;'>&nbsp;</th><th style='width=10px;'>&nbsp;</th><th style='width=10px;'>&nbsp;</th><th style='width=10px;'>&nbsp;</th><th style='width=10px;'>&nbsp;</th><th style='width:10px;'>Type</th><th style='width:10px;'>Date</th>";
	foreach($title as $label)
        { 
            echo "<th style='width:200px;'>".htmlspecialchars( xl($label), ENT_NOQUOTES)."&nbsp;</th>" ;
        }
                         
        echo "<th style='width:200px;'>".htmlspecialchars( xl('Who'), ENT_NOQUOTES)."&nbsp;</th>" ;
        echo "<th style='width:200px;'>".htmlspecialchars( xl('Provider'), ENT_NOQUOTES)."&nbsp;</th>" ;
        echo "<th style='width:200px;'>".htmlspecialchars( xl('Facility'), ENT_NOQUOTES)."&nbsp;</th>" ;
        echo "<th style='width:200px;'>".htmlspecialchars( xl('Pharmacy'), ENT_NOQUOTES)."&nbsp;</th>" ;
        echo "<th style='width:200px;'>".htmlspecialchars( xl('Payer'), ENT_NOQUOTES)."&nbsp;</th>" ;
        echo "<th style='width:200px;'>".htmlspecialchars( xl('Notes'), ENT_NOQUOTES)."&nbsp;</th>" ;
                        
        echo "</tr>\n";
		// Print Heading .. to have better Understanding of the Listed Transactions   -- ends here

        if ($result = getChartOutputByPid($pid, $group_name)) {
            foreach ($result as $iter) {
                        $trans_type2=$iter{'trans_type'};
                        $transaction=$iter{'transaction'};
                        //for face to face
                        $enc=sqlStatement("select * from form_encounter where  DATE_FORMAT(date, '%Y-%m-%d')='".$iter['date_of_service']."' and pid=$pid" );
                        $renc=sqlFetchArray($enc);

                        echo "<tr height='25'><td style='width:60px;' >";
                        echo "</td><td style='width:60px;'>";
                        echo "</td><td style='width:60px;'>";
                        if($transaction==2) {
                            $tr_name='f2f';
                            echo "<a href='print_f2fchart.php?f2fid=".$iter['id']."&encounter_id=". $renc['encounter']."".            
                            "&patient_id=$pid&date_of_service=".$iter['date_of_service']."'onclick='top.restoreSession()' class='css_button_small'><span>".
                                                htmlspecialchars( xl('Print'), ENT_NOQUOTES)."</span></a>";
                        }else {    
                            $tr_name='med';
                            echo "<a href='print_chart_static.php?coid=".$iter['id']."".            
                                "&patient_id=$pid"."&group=$group_name1'onclick='top.restoreSession()' class='css_button_small'><span>".
                                 htmlspecialchars( xl('Print'), ENT_NOQUOTES)."</span></a>";
                        }
                        echo "</td><td style='width:60px;'>";
                        print "<a href='chart_output.php?coid=".htmlspecialchars( $iter{"id"}, ENT_NOQUOTES).
                                "&inmode=edit&group_name2=$group_name2&group_name=$group_name1&type=$trans_type2' onclick='top.restoreSession()' class='css_button_small'><span>".
                                htmlspecialchars( xl('Edit'), ENT_NOQUOTES)."</span></a>";
                        echo "</td><td>";
                        if (acl_check('admin', 'super')) {
                                echo "<a href='../deleter.php?coid=".
                                        htmlspecialchars( $iter{"id"}, ENT_QUOTES).
                                        "&patient_id=$pid' onclick='top.restoreSession()' class='css_button_small'><span>".
                                        htmlspecialchars( xl('Delete'), ENT_NOQUOTES)."</span></a>";
                        }
                        else {
                                echo "&nbsp;";
                        }
                        echo "</td>";
                        //transaction type
                        echo " <td style='width:200px;'>&nbsp;&nbsp;&nbsp;&nbsp;" .
                                    htmlspecialchars( ($iter{'trans_type'}." ( ". $tr_name ." )"), ENT_NOQUOTES). "&nbsp;</td>";

                        //transaction date            
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
                         //for layout fields              
                        foreach($field_id as $attr)
                        { 
                               if($attr!=''){
                                     echo " <td style='width:200px;'>&nbsp;&nbsp;&nbsp;&nbsp;" .
                                    htmlspecialchars( ($iter{$attr}), ENT_NOQUOTES). "&nbsp;</td>";
                                }else {
                                     echo " <td style='width:200px;'>&nbsp;&nbsp;&nbsp;&nbsp;" .
                                     htmlspecialchars( (''), ENT_NOQUOTES). "&nbsp;</td>";
                                }
                        }

                        //refer to
                        if($iter{'refer_to'}!='' && $iter{'refer_to'}!='null' && $iter{'refer_to'}!='0'){
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
                        if($iter{'provider'}!='' && $iter{'provider'}!='null' && $iter{'provider'}!='0'){
                            $users2=sqlStatement("select concat(lname,' ',fname) as name from users where id='".$iter{'provider'}."'");
                            $res3=sqlFetchArray($users2);
                            echo " <td style='width:200px;'>&nbsp;&nbsp;&nbsp;&nbsp;" .
                            htmlspecialchars( ($res3{name}), ENT_NOQUOTES). "&nbsp;</td>";
                        }else {
                             echo " <td style='width:200px;'>&nbsp;</td>";
                        }

                        //facility
                        if($iter{'facility'}!='' && $iter{'facility'}!='null' && $iter{'facility'}!='0'){

                            $fac2=sqlStatement("SELECT name FROM facility where id='".$iter{'facility'}."'");
                            $res4=sqlFetchArray($fac2);
                            echo " <td style='width:200px;'>&nbsp;&nbsp;&nbsp;&nbsp;" .
                            htmlspecialchars( ($res4{name}), ENT_NOQUOTES). "&nbsp;</td>";
                        }else {
                            echo " <td style='width:200px;'>&nbsp;</td>";
                        }

                        //pharmacy
                        if($iter{'pharmacy'}!='' && $iter{'pharmacy'}!='null' && $iter{'pharmacy'}!='0'){
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

                        echo " <td style='width:200px;'>&nbsp;&nbsp;&nbsp;&nbsp;" .
                                htmlspecialchars( ($iter{'notes'}), ENT_NOQUOTES). "&nbsp;</td>";
                        echo "</tr>\n";
                        $notes_count++;

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
                $trans_type1=$iter1{'trans_type'};
                echo "<tr height='25'><td style='width:60px;' >";
		echo "</td><td style='width:60px;'>";
		echo "</td><td style='width:60px;'>"; 
                $sql1=sqlStatement("select * from  list_options where list_id='form_templates' and option_id='".$iter1{'form_template'}."'");
                $row1=sqlFetchArray($sql1);
                echo "<a href='template_forms/".$row1['notes']."?coid=".$iter1['id']."".            
                        "&patient_id=$pid&group=".$group_name1."&temp_id=".$iter1['form_template']."&print=1"."'onclick='top.restoreSession()' class='css_button_small'><span>".
                         htmlspecialchars( xl('Print'), ENT_NOQUOTES)."</span></a>";
                echo "</td><td style='width:60px;'>";
		print "<a href='chart_output.php?coid=".htmlspecialchars( $iter1{"id"}, ENT_NOQUOTES).
			"&inmode=edit&group_name2=$group_name2&group_name=$group_name1&type=$trans_type1' onclick='top.restoreSession()' class='css_button_small'><span>".
			htmlspecialchars( xl('Edit'), ENT_NOQUOTES)."</span></a>";
		echo "</td><td>";
                if (acl_check('admin', 'super')) {
			echo "<a href='../deleter.php?coid=".
				htmlspecialchars( $iter1{"id"}, ENT_QUOTES).
				"&patient_id=$pid&nonenc=1' onclick='top.restoreSession()' class='css_button_small'><span>".
				htmlspecialchars( xl('Delete'), ENT_NOQUOTES)."</span></a>";
		}
		else {
			echo "&nbsp;";
		}
		echo "</td>";
              
                echo " <td style='width:200px;'>&nbsp;&nbsp;&nbsp;&nbsp;" .
                            htmlspecialchars( ($iter1{'trans_type'}), ENT_NOQUOTES). "&nbsp;</td>";
                            
                if($iter1{'date'}!='')  {  
                     
                     echo " <td style='width:200px;'>&nbsp;&nbsp;&nbsp;&nbsp;" .
                            htmlspecialchars( ($iter1{'date'}), ENT_NOQUOTES). "&nbsp;</td>";  
                }
                else {
                   
                    echo " <td style='width:200px;'>&nbsp;&nbsp;&nbsp;&nbsp;" .
                            htmlspecialchars( (''), ENT_NOQUOTES). "&nbsp;</td>";  
                }            
                //for layout fields     
                
                 foreach($field_id as $attr1)
                 { 
                    
                    if(in_array($attr1,$field_id2)){
                        echo " <td style='width:200px;'>&nbsp;&nbsp;&nbsp;&nbsp;" .
                        htmlspecialchars( ($iter1{$attr1}), ENT_NOQUOTES). "&nbsp;</td>";
                    }else {
                        echo " <td style='width:200px;'>&nbsp;&nbsp;&nbsp;&nbsp;" .
                        htmlspecialchars( (''), ENT_NOQUOTES). "&nbsp;</td>";
                    }
                   
                 }
                //refer to    
                if($iter1{'refer_to'}!='' && $iter1{'refer_to'}!='null' && $iter1{'refer_to'}!='0'){
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
                
                //for provider
                if($iter1{'provider'}!='' && $iter1{'provider'}!='null' && $iter1{'provider'}!='0'){
                    $users2=sqlStatement("select concat(lname,' ',fname) as name from users where id='".$iter1{'provider'}."'");
                    $res3=sqlFetchArray($users2);
                    echo " <td style='width:200px;'>&nbsp;&nbsp;&nbsp;&nbsp;" .
                    htmlspecialchars( ($res3{name}), ENT_NOQUOTES). "&nbsp;</td>";
                }else {
                     echo " <td style='width:200px;'>&nbsp;</td>";
                }
                
                //for facility
                if($iter1{'facility'}!='' && $iter1{'facility'}!='null' && $iter1{'facility'}!='0'){

                    $fac2=sqlStatement("SELECT name FROM facility where id='".$iter1{'facility'}."'");
                    $res4=sqlFetchArray($fac2);
                    echo " <td style='width:200px;'>&nbsp;&nbsp;&nbsp;&nbsp;" .
                    htmlspecialchars( ($res4{name}), ENT_NOQUOTES). "&nbsp;</td>";
                }else {
                    echo " <td style='width:200px;'>&nbsp;</td>";
                }
                        
                //for Pharmacy
                if($iter1{'pharmacy'}!='' && $iter1{'pharmacy'}!='null' && $iter1{'pharmacy'}!='0'){
                    $ph2=sqlStatement("SELECT name FROM pharmacies where id='".$iter1{'pharmacy'}."'");
                    $res5=sqlFetchArray($ph2);
                    echo " <td style='width:200px;'>&nbsp;&nbsp;&nbsp;&nbsp;" .
                    htmlspecialchars( ($res5{name}), ENT_NOQUOTES). "&nbsp;</td>";
                }else {
                    echo " <td style='width:200px;'>&nbsp;</td>";
                }
                
                //for payer
                if($iter1{'payer'}!='' && $iter1{'payer'}!='null' && $iter1{'payer'}!='0'){
                    $pay2=sqlStatement("SELECT name FROM insurance_companies where id='".$iter1{'payer'}."'");
                    $res6=sqlFetchArray($pay2);
                    echo " <td style='width:200px;'>&nbsp;&nbsp;&nbsp;&nbsp;" .
                    htmlspecialchars( ($res6{name}), ENT_NOQUOTES). "&nbsp;</td>";
                }else {
                    echo " <td style='width:200px;'>&nbsp;</td>";
                }
                        
                echo " <td style='width:200px;'>&nbsp;&nbsp;&nbsp;&nbsp;" .
                    htmlspecialchars( ($iter1{'notes'}), ENT_NOQUOTES). "&nbsp;</td>";
                echo "</tr>\n";
		$notes_count++;

            }
        }

    echo "</table>";
    
}
else {
    echo '<table class="showborder" cellspacing="0px" cellpadding="2px">';
    $labels = sqlStatement("select DISTINCT(group_name) from layout_options where  form_id = 'CHARTOUTPUT' order by group_name" );
    $i=1;
    while($frow2 = sqlFetchArray($labels)) {
            $field_id=array();
            $title2=array();
            $group=$frow2['group_name'];
            $group2=substr($group,0,1);
            $fres = sqlStatement("SELECT * FROM layout_options " .
                    "WHERE form_id = 'CHARTOUTPUT' AND uor > 0 AND field_id != ''  AND group_name = '".$frow2['group_name']."'" .
                    "ORDER BY group_name, seq");
            while ($frow = sqlFetchArray($fres)) {
                $data_type = $frow['data_type'];
                $field_id[]  = $frow['field_id'];
                $value = $_POST["form_$field_id"];
                $title2[] = $frow['title'];
            }
           
            if($i==1){
                print "<tr class='showborder_head'><th style='width=10px;'>&nbsp;</th><th style='width=10px;'>&nbsp;</th><th style='width=10px;'>&nbsp;</th><th style='width=10px;'>&nbsp;</th><th style='width=10px;'>&nbsp;</th><th style='width:10px;'>Type</th><th style='width:10px;'>Group</th><th style='width:10px;'>Date</th>";
                foreach($title2 as $label){ 
                    echo "<th style='width:200px;'>".htmlspecialchars( xl($label), ENT_NOQUOTES)."&nbsp;</th>" ;
                }
                echo "<th style='width:200px;'>".htmlspecialchars( xl('Who'), ENT_NOQUOTES)."&nbsp;</th>" ;
                echo "<th style='width:200px;'>".htmlspecialchars( xl('Provider'), ENT_NOQUOTES)."&nbsp;</th>" ;
                echo "<th style='width:200px;'>".htmlspecialchars( xl('Facility'), ENT_NOQUOTES)."&nbsp;</th>" ;
                echo "<th style='width:200px;'>".htmlspecialchars( xl('Pharmacy'), ENT_NOQUOTES)."&nbsp;</th>" ;
                echo "<th style='width:200px;'>".htmlspecialchars( xl('Payer'), ENT_NOQUOTES)."&nbsp;</th>" ;
                echo "<th style='width:200px;'>".htmlspecialchars( xl('Notes'), ENT_NOQUOTES)."&nbsp;</th>" ;
                echo "</tr>\n";
                $i++;
            }
            if ($result = getChartOutputByPid($pid, $group)) {
                 foreach ($result as $iter) {
                    $trans_type2=$iter{'trans_type'};
                    $transaction=$iter{'transaction'}; 
                    $enc=sqlStatement("select * from form_encounter where  DATE_FORMAT(date, '%Y-%m-%d')='".$iter['date_of_service']."' and pid=$pid" );
                    $renc=sqlFetchArray($enc);
                    echo "<tr height='25'><td style='width:60px;' >";
                    echo "</td><td style='width:60px;'>";
                    echo "</td><td style='width:60px;'>";
                    if($transaction==2) {
                        $tr_name='f2f';
                        echo "<a href='print_f2fchart.php?f2fid=".$iter['id']."&encounter_id=". $renc['encounter']."".            
                        "&patient_id=$pid&date_of_service=".$iter['date_of_service']."'onclick='top.restoreSession()' class='css_button_small'><span>".
                        htmlspecialchars( xl('Print'), ENT_NOQUOTES)."</span></a>";
                    }else {  
                        $tr_name='med';
                                echo "<a href='print_chart_static.php?coid=".$iter['id']."".            
                                        "&patient_id=$pid"."&group=$group'onclick='top.restoreSession()' class='css_button_small'><span>".
                                         htmlspecialchars( xl('Print'), ENT_NOQUOTES)."</span></a>";
                    }
                    echo "</td><td style='width:60px;'>";
                    print "<a href='chart_output.php?coid=".htmlspecialchars( $iter{"id"}, ENT_NOQUOTES).
                            "&inmode=edit&group_name2=$group2&group_name=$group&type=$trans_type2' onclick='top.restoreSession()' class='css_button_small'><span>".
                            htmlspecialchars( xl('Edit'), ENT_NOQUOTES)."</span></a>";
                    echo "</td><td>";
                    if (acl_check('admin', 'super')) {
                            echo "<a href='../deleter.php?coid=".
                                    htmlspecialchars( $iter{"id"}, ENT_QUOTES).
                                    "&patient_id=$pid' onclick='top.restoreSession()' class='css_button_small'><span>".
                                    htmlspecialchars( xl('Delete'), ENT_NOQUOTES)."</span></a>";
                    }
                    else {
                            echo "&nbsp;";
                    }
                    echo "</td>";
                    //transaction type
                    echo " <td style='width:200px;'>&nbsp;&nbsp;&nbsp;&nbsp;" .
                                    htmlspecialchars( ($iter{'trans_type'}." ( ". $tr_name ." )"), ENT_NOQUOTES). "&nbsp;</td>";

                    echo " <td style='width:200px;'>&nbsp;&nbsp;&nbsp;&nbsp;" .
                                htmlspecialchars( (substr($group,1)), ENT_NOQUOTES). "&nbsp;</td>";                      
                    //transaction date            
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
                     //for layout fields              
                    foreach($field_id as $attr)
                    { 
                        if($attr!=''){
                             echo " <td style='width:200px;'>&nbsp;&nbsp;&nbsp;&nbsp;" .
                            htmlspecialchars( ($iter{$attr}), ENT_NOQUOTES). "&nbsp;</td>";
                        }
                        else {
                             echo " <td style='width:200px;'>&nbsp;&nbsp;&nbsp;&nbsp;" .
                             htmlspecialchars( (''), ENT_NOQUOTES). "&nbsp;</td>";
                        }

                    }

                    //refer to
                    if($iter{'refer_to'}!='' && $iter{'refer_to'}!='null' && $iter{'refer_to'}!='0'){
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
                    if($iter{'provider'}!='' && $iter{'provider'}!='null' && $iter{'provider'}!='0'){
                        $users2=sqlStatement("select concat(lname,' ',fname) as name from users where id='".$iter{'provider'}."'");
                        $res3=sqlFetchArray($users2);
                        echo " <td style='width:200px;'>&nbsp;&nbsp;&nbsp;&nbsp;" .
                        htmlspecialchars( ($res3{name}), ENT_NOQUOTES). "&nbsp;</td>";
                    }else {
                         echo " <td style='width:200px;'>&nbsp;</td>";
                    }

                    //facility
                    if($iter{'facility'}!='' && $iter{'facility'}!='null' && $iter{'facility'}!='0'){

                        $fac2=sqlStatement("SELECT name FROM facility where id='".$iter{'facility'}."'");
                        $res4=sqlFetchArray($fac2);
                        echo " <td style='width:200px;'>&nbsp;&nbsp;&nbsp;&nbsp;" .
                        htmlspecialchars( ($res4{name}), ENT_NOQUOTES). "&nbsp;</td>";
                    }else {
                        echo " <td style='width:200px;'>&nbsp;</td>";
                    }

                    //pharmacy
                    if($iter{'pharmacy'}!='' && $iter{'pharmacy'}!='null' && $iter{'pharmacy'}!='0'){
                        $ph2=sqlStatement("SELECT name FROM pharmacies where id='".$iter{'pharmacy'}."'");
                        $res5=sqlFetchArray($ph2);
                        echo " <td style='width:200px;'>&nbsp;&nbsp;&nbsp;&nbsp;" .
                        htmlspecialchars( ($res5{name}), ENT_NOQUOTES). "&nbsp;</td>";
                    }else {
                        echo " <td style='width:200px;'>&nbsp;</td>";
                    }

                    //payer
                    if($iter{'payer'}!='' && $iter{'payer'}!='null'  && $iter{'payer'}!='0'){
                        $pay2=sqlStatement("SELECT name FROM insurance_companies where id='".$iter{'payer'}."'");
                        $res6=sqlFetchArray($pay2);
                        echo " <td style='width:200px;'>&nbsp;&nbsp;&nbsp;&nbsp;" .
                        htmlspecialchars( ($res6{name}), ENT_NOQUOTES). "&nbsp;</td>";
                    }else {
                        echo " <td style='width:200px;'>&nbsp;</td>";
                    }

                    echo " <td style='width:200px;'>&nbsp;&nbsp;&nbsp;&nbsp;" .
                            htmlspecialchars( ($iter{'notes'}), ENT_NOQUOTES). "&nbsp;</td>";
                    echo "</tr>\n";
                    $notes_count++;
                        
                 }
            }
            
            
            
    }
    
    
    //for nonencounter
    
    $labels1 = sqlStatement("select DISTINCT(group_name) from layout_options where  form_id = 'NONENC' order by group_name" );
    while($frow3=sqlFetchArray($labels1)) { 
    
        $field_id=array();
        $title=array();   
        $group3=$frow3['group_name'];
        $group4=substr($group3,0,1);
        $group5=substr($group3,1);
         //for nonencounter
        $fres = sqlStatement("SELECT * FROM layout_options " .
            "WHERE form_id = 'CHARTOUTPUT' AND uor > 0 AND field_id != ''  AND group_name LIKE '%$group5%'" .
            "ORDER BY group_name, seq");
          while ($frow = sqlFetchArray($fres)) {
            $data_type = $frow['data_type'];
            $field_id[]  = $frow['field_id'];
            $value = $_POST["form_$field_id"];
            $title[] = $frow['title'];
           }
        
        $field_id2=array();
        $title=array();
        $fres2 = sqlStatement("SELECT * FROM layout_options " .
            "WHERE form_id = 'NONENC' AND uor > 0 AND field_id != ''  AND group_name = '".$frow3['group_name']."'" .
            "ORDER BY group_name, seq");
        while ($frow2 = sqlFetchArray($fres2)) {
            $field_id2[]  = $frow2['field_id'];
            $value = $_POST["form_$field_id2"];
            $title[] = $frow2['title'];
        }
           
            if ($result1 = getNonEncChartByPid($pid, $group3)) {
                
                foreach ($result1 as $iter1) {
                        $trans_type1=$iter1{'trans_type'};
                        echo "<tr height='25'><td style='width:60px;' >";
                        echo "</td><td style='width:60px;'>";
                        echo "</td><td style='width:60px;'>"; 
                        $sql1=sqlStatement("select * from  list_options where list_id='form_templates' and option_id='".$iter1{'form_template'}."'");
                        $row1=sqlFetchArray($sql1);
                        echo "<a href='template_forms/".$row1['notes']."?coid=".$iter1['id']."".            
                                "&patient_id=$pid&group=".$group3."&temp_id=".$iter1['form_template']."&print=1"."'onclick='top.restoreSession()' class='css_button_small'><span>".
                                 htmlspecialchars( xl('Print'), ENT_NOQUOTES)."</span></a>";
                        echo "</td><td style='width:60px;'>";
                        print "<a href='chart_output.php?coid=".htmlspecialchars( $iter1{"id"}, ENT_NOQUOTES).
                                "&inmode=edit&group_name2=$group4&group_name=$group3&type=$trans_type1' onclick='top.restoreSession()' class='css_button_small'><span>".
                                htmlspecialchars( xl('Edit'), ENT_NOQUOTES)."</span></a>";
                        echo "</td><td>";
                        if (acl_check('admin', 'super')) {
                                        echo "<a href='../deleter.php?coid=".
                                                htmlspecialchars( $iter1{"id"}, ENT_QUOTES).
                                                "&patient_id=$pid&nonenc=1' onclick='top.restoreSession()' class='css_button_small'><span>".
                                                htmlspecialchars( xl('Delete'), ENT_NOQUOTES)."</span></a>";
                                }
                                else {
                                        echo "&nbsp;";
                                }
                                echo "</td>";

                                echo " <td style='width:200px;'>&nbsp;&nbsp;&nbsp;&nbsp;" .
                                            htmlspecialchars( ($iter1{'trans_type'}), ENT_NOQUOTES). "&nbsp;</td>";
                                echo " <td style='width:200px;'>&nbsp;&nbsp;&nbsp;&nbsp;" .
                                            htmlspecialchars( (substr($group3,1)), ENT_NOQUOTES). "&nbsp;</td>";            
                                if($iter1{'date'}!='')  {  
                                    echo " <td style='width:200px;'>&nbsp;&nbsp;&nbsp;&nbsp;" .
                                            htmlspecialchars( ($iter1{'date'}), ENT_NOQUOTES). "&nbsp;</td>";  
                                }
                                        
                                //for layout fields            
                                 foreach($field_id as $attr)
                                 { 
                                    if(in_array($attr,$field_id2)){
                                        echo " <td style='width:200px;'>&nbsp;&nbsp;&nbsp;&nbsp;" .
                                        htmlspecialchars( ($iter1{$attr}), ENT_NOQUOTES). "&nbsp;</td>";
                                    }else {
                                        echo " <td style='width:200px;'>&nbsp;&nbsp;&nbsp;&nbsp;" .
                                        htmlspecialchars( (''), ENT_NOQUOTES). "&nbsp;</td>";
                                    }
                                 }
                                //refer to    
                                if($iter1{'refer_to'}!='' && $iter1{'refer_to'}!='null' && $iter1{'refer_to'}!='0'){
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

                                //for provider
                                if($iter1{'provider'}!='' && $iter1{'provider'}!='null' && $iter1{'provider'}!='0'){
                                    $users2=sqlStatement("select concat(lname,' ',fname) as name from users where id='".$iter1{'provider'}."'");
                                    $res3=sqlFetchArray($users2);
                                    echo " <td style='width:200px;'>&nbsp;&nbsp;&nbsp;&nbsp;" .
                                    htmlspecialchars( ($res3{name}), ENT_NOQUOTES). "&nbsp;</td>";
                                }else {
                                     echo " <td style='width:200px;'>&nbsp;</td>";
                                }

                                //for facility
                                
                                if($iter1{'facility'}!='' && $iter1{'facility'}!='null' && $iter1{'facility'}!='0'){

                                    $fac2=sqlStatement("SELECT name FROM facility where id='".$iter1{'facility'}."'");
                                    $res4=sqlFetchArray($fac2);
                                    echo " <td style='width:200px;'>&nbsp;&nbsp;&nbsp;&nbsp;" .
                                    htmlspecialchars( ($res4{name}), ENT_NOQUOTES). "&nbsp;</td>";
                                }else {
                                    echo " <td style='width:200px;'>&nbsp;</td>";
                                }

                                //for Pharmacy
                                if($iter1{'pharmacy'}!='' && $iter1{'pharmacy'}!='null' && $iter1{'pharmacy'}!='0'){
                                    $ph2=sqlStatement("SELECT name FROM pharmacies where id='".$iter1{'pharmacy'}."'");
                                    $res5=sqlFetchArray($ph2);
                                    echo " <td style='width:200px;'>&nbsp;&nbsp;&nbsp;&nbsp;" .
                                    htmlspecialchars( ($res5{name}), ENT_NOQUOTES). "&nbsp;</td>";
                                }else {
                                    echo " <td style='width:200px;'>&nbsp;</td>";
                                }

                                //for payer
                                if($iter1{'payer'}!='' && $iter1{'payer'}!='null' && $iter1{'payer'}!='0'){
                                    $pay2=sqlStatement("SELECT name FROM insurance_companies where id='".$iter{'payer'}."'");
                                    $res6=sqlFetchArray($pay2);
                                    echo " <td style='width:200px;'>&nbsp;&nbsp;&nbsp;&nbsp;" .
                                    htmlspecialchars( ($res6{name}), ENT_NOQUOTES). "&nbsp;</td>";
                                }else {
                                    echo " <td style='width:200px;'>&nbsp;</td>";
                                }

                                echo " <td style='width:200px;'>&nbsp;&nbsp;&nbsp;&nbsp;" .
                                    htmlspecialchars( ($iter1{'notes'}), ENT_NOQUOTES). "&nbsp;</td>";
                                echo "</tr>\n";
                                $notes_count++;

                            }
                            
        }
    }
     echo "</table>";
     
}
?>


<script type='text/javascript'>
    $(document).ready(function() {
        //datatable
        var responsiveHelper;
        var breakpointDefinition = {
            tablet: 1024,
            phone : 480
        };
        var tableElement = $('#chart_data');
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
</body>  
</html>