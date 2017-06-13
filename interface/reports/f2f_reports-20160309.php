<?php

//SANITIZE ALL ESCAPES
$sanitize_all_escapes=true;
//

//STOP FAKE REGISTER GLOBALS
$fake_register_globals=false;
//

include_once("../globals.php");
//include_once("$srcdir/transactions.inc");
include_once("$srcdir/chartoutput_lib.php");
require_once("$srcdir/options.inc.php");

//print_r($_GET);
//print_r($_POST);
//print_r($_SESSION);
$group_name1 =  $_REQUEST['group_name']; 
$group_name2 = substr($group_name,0, 1);
$pid=$_REQUEST['pid'] ? $_REQUEST['pid'] :$pid;
$encounter=$_REQUEST['enc'];
?>

<html>
<head>
<?php html_header_show();?>
<link rel="stylesheet" href="<?php echo $css_header;?>" type="text/css">
<script language="javascript">
// Called by the deleteme.php window on a successful delete.
function imdeleted() {
 top.restoreSession();
 location.href = 'reports/add_f2f.php';
}
</script>

</head>
<body class="body_top">
    <br>
<div id="pname">
    <?php //$encounter=$GLOBALS['encounter'];
         //echo $encounter=$_SESSION['encounter'];?>
    <span ><?php echo htmlspecialchars( xl('Patient Name'), ENT_NOQUOTES); ?>:</span>
        <?php 
            $getPatientName=sqlStatement("SELECT CONCAT(fname,' ',lname) AS pname FROM patient_data WHERE pid='".$pid."'");
            $resPatientName=sqlFetchArray($getPatientName);
            
//            $getencounter=sqlStatement("SELECT encounter from form_encounter WHERE pid='".$pid."'");
//            $resencounter=sqlFetchArray($getencounter);
        ?>
        
    <span class='bold'><?php echo htmlspecialchars( xl($resPatientName['pname']), ENT_NOQUOTES); ?></span></br>
    <span><?php echo htmlspecialchars( xl('Encounter Id'), ENT_NOQUOTES); ?>:</span>
    <span class='bold'><?php echo htmlspecialchars( xl($encounter), ENT_NOQUOTES); ?></span>
        
</div> 
<br>







<table class="showborder" cellspacing="0px" cellpadding="2px">

<?php

function getF2FChartByPid ($pid, $group_name, $type,$cols = "*")
{
  $labels = sqlStatement("select field_id from layout_options where group_name  LIKE '%$group_name%' and form_id = 'CHARTOUTPUT' AND field_id LIKE '%f2f%'" );
  while ($labels2 = sqlFetchArray($labels)) {
        $titles .= $labels2['field_id'].',' ; 
         $columncheck .= $labels2['field_id']." <> '' OR ";
  }
  $titles2 = rtrim($titles ,',');
  $columncheck2 = rtrim($columncheck, ' OR ');
  
 
  
  //return $allstring;  
  
  //$all = '';
  if(!empty($group_name)){
//      if($pid == 0):
//          $pid = $_SESSION['pid'];
//      endif;
    
  $res = sqlStatement("select id,refer_to,provider,facility,pharmacy,payer,notes,dos,form_template,trans_type,created_date,updated_date,transaction,date_of_service,$titles2 from tbl_form_chartoutput_transactions where pid=$pid AND transaction=$type AND ($columncheck2)
    order by id DESC ");
   
  for ($iter = 0; $row = sqlFetchArray($res); $iter++) 
    $all[$iter] = $row;
  return $all;
  }
}
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
	print "<tr class='showborder_head'><th style='width=10px;'>&nbsp;</th><th style='width=10px;'>&nbsp;</th><th style='width=10px;'>&nbsp;</th><th style='width=10px;'>&nbsp;</th><th style='width=10px;'>&nbsp;</th><th style='width:500px;'>Type</th><th style='width:10px;'>Date</th>";
//	foreach($title as $label)
//        { 
//            echo "<th style='width:200px;'>".htmlspecialchars( xl($label), ENT_NOQUOTES)."&nbsp;</th>" ;
//        }
//                         
        echo "<th style='width:200px;'>".htmlspecialchars( xl('Who'), ENT_NOQUOTES)."&nbsp;</th>" ;
        echo "<th style='width:200px;'>".htmlspecialchars( xl('Provider'), ENT_NOQUOTES)."&nbsp;</th>" ;
        echo "<th style='width:200px;'>".htmlspecialchars( xl('Facility'), ENT_NOQUOTES)."&nbsp;</th>" ;
        echo "<th style='width:200px;'>".htmlspecialchars( xl('Pharmacy'), ENT_NOQUOTES)."&nbsp;</th>" ;
        echo "<th style='width:200px;'>".htmlspecialchars( xl('Payer'), ENT_NOQUOTES)."&nbsp;</th>" ;
        echo "<th style='width:200px;'>".htmlspecialchars( xl('Notes'), ENT_NOQUOTES)."&nbsp;</th>" ;
                        
        echo "</tr>\n";
		// Print Heading .. to have better Understanding of the Listed Transactions   -- ends here

        if ($result = getF2FChartByPid($pid, $group_name,2)) {
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
                            echo "<a href='print_f2f.php?f2fid=".$iter['id']."&encounter_id=". $renc['encounter']."".            
                            "&patient_id=$pid&date_of_service=".$iter['date_of_service']."'onclick='top.restoreSession()' class='css_button_small'><span>".
                                                htmlspecialchars( xl('Print'), ENT_NOQUOTES)."</span></a>";
                        }
                        echo "</td><td style='width:60px;'>";
                        print "<a href='f2f_form.php?coid=".htmlspecialchars( $iter{"id"}, ENT_NOQUOTES).
                                "&inmode=edit&group_name2=$group_name2&group_name=$group_name1&type=$trans_type2&pid=$pid&enc=$encounter' onclick='top.restoreSession()' class='css_button_small'><span>".
                                htmlspecialchars( xl('Edit'), ENT_NOQUOTES)."</span></a>";
                        echo "</td><td>";
                        if (acl_check('admin', 'super')) {
                                echo "<a href='../patient_file/deleter.php?coid=".
                                        htmlspecialchars( $iter{"id"}, ENT_QUOTES).
                                        "&patient_id=$pid' onclick='top.restoreSession()' class='css_button_small'><span>".
                                        htmlspecialchars( xl('Delete'), ENT_NOQUOTES)."</span></a>";
                        }
                        else {
                                echo "&nbsp;";
                        }
                        echo "</td>";
                        //transaction type
                        echo " <td style='width:400px;'>&nbsp;&nbsp;&nbsp;&nbsp;" .
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
//                        foreach($field_id as $attr)
//                        { 
//                               if($attr!=''){
//                                     echo " <td style='width:200px;'>&nbsp;&nbsp;&nbsp;&nbsp;" .
//                                    htmlspecialchars( ($iter{$attr}), ENT_NOQUOTES). "&nbsp;</td>";
//                                }else {
//                                     echo " <td style='width:200px;'>&nbsp;&nbsp;&nbsp;&nbsp;" .
//                                     htmlspecialchars( (''), ENT_NOQUOTES). "&nbsp;</td>";
//                                }
//                        }

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
    echo "</table>";
    
}else {
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
//                foreach($title2 as $label){ 
//                    echo "<th style='width:200px;'>".htmlspecialchars( xl($label), ENT_NOQUOTES)."&nbsp;</th>" ;
//                }
                echo "<th style='width:200px;'>".htmlspecialchars( xl('Who'), ENT_NOQUOTES)."&nbsp;</th>" ;
                echo "<th style='width:200px;'>".htmlspecialchars( xl('Provider'), ENT_NOQUOTES)."&nbsp;</th>" ;
                echo "<th style='width:200px;'>".htmlspecialchars( xl('Facility'), ENT_NOQUOTES)."&nbsp;</th>" ;
                echo "<th style='width:200px;'>".htmlspecialchars( xl('Pharmacy'), ENT_NOQUOTES)."&nbsp;</th>" ;
                echo "<th style='width:200px;'>".htmlspecialchars( xl('Payer'), ENT_NOQUOTES)."&nbsp;</th>" ;
                echo "<th style='width:200px;'>".htmlspecialchars( xl('Notes'), ENT_NOQUOTES)."&nbsp;</th>" ;
                echo "</tr>\n";
                $i++;
            }
            if ($result = getF2FChartByPid($pid, $group,2)) {
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
                        echo "<a href='print_f2f.php?f2fid=".$iter['id']."&encounter_id=". $renc['encounter']."".            
                        "&patient_id=$pid&date_of_service=".$iter['date_of_service']."'onclick='top.restoreSession()' class='css_button_small'><span>".
                        htmlspecialchars( xl('Print'), ENT_NOQUOTES)."</span></a>";
                    }
                    echo "</td><td style='width:60px;'>";
                    print "<a href='f2f_form.php?coid=".htmlspecialchars( $iter{"id"}, ENT_NOQUOTES).
                            "&inmode=edit&group_name2=$group2&group_name=$group&type=$trans_type2&pid=$pid&enc=$encounter' onclick='top.restoreSession()' class='css_button_small'><span>".
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
                      echo " <td style='width:400px;'>&nbsp;&nbsp;&nbsp;&nbsp;" .
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
//                    foreach($field_id as $attr)
//                    { 
//                        if($attr!=''){
//                             echo " <td style='width:200px;'>&nbsp;&nbsp;&nbsp;&nbsp;" .
//                            htmlspecialchars( ($iter{$attr}), ENT_NOQUOTES). "&nbsp;</td>";
//                        }
//                        else {
//                             echo " <td style='width:200px;'>&nbsp;&nbsp;&nbsp;&nbsp;" .
//                             htmlspecialchars( (''), ENT_NOQUOTES). "&nbsp;</td>";
//                        }
//
//                    }

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
    
    
    
     
} ?>
</table>
</body>
</html>