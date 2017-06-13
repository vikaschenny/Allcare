<?php

//SANITIZE ALL ESCAPES
$sanitize_all_escapes=true;
//

//STOP FAKE REGISTER GLOBALS
$fake_register_globals=false;
//

include_once("../globals.php");
require_once("../../library/formdata.inc.php");
require_once("../../library/globals.inc.php");
?>
<html>
<head>
<?php html_header_show();?>

<link rel="stylesheet" href="<?php echo $css_header;?>" type="text/css">
<link rel="stylesheet" type="text/css" href="../../../library/js/fancybox/jquery.fancybox-1.2.6.css" media="screen" />
<script type="text/javascript" src="../../../library/textformat.js"></script>
<script type="text/javascript" src="../../../library/dialog.js"></script>
<script type="text/javascript" src="../../../library/js/jquery.1.3.2.js"></script>
<script type="text/javascript" src="../../../library/js/common.js"></script>
<script type="text/javascript" src="../../../library/js/fancybox/jquery.fancybox-1.2.6.js"></script>


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
<style type="text/css">@import url(../../library/dynarch_calendar.css);</style>
<link rel=stylesheet href="<?php echo $css_header;?>" type="text/css">
<link rel='stylesheet' type='text/css' href='../main/css/jquery.dataTables.css'>
<link rel='stylesheet' type='text/css' href='../main/css/dataTables.tableTools.css'>
<link rel='stylesheet' type='text/css' href='../main/css/dataTables.colVis.css'>
<link rel='stylesheet' type='text/css' href='../main/css/dataTables.colReorder.css'>
<!--<style>
div.DTTT_container {
	float: none;
}
</style>-->
<script type='text/javascript' src='../main/js/jquery-1.11.1.min.js'></script>
<script type='text/javascript' src='../main/js/jquery.dataTables.min.js'></script>
<script type='text/javascript' src='../main/js/dataTables.tableTools.js'></script>
<script type='text/javascript' src='../main/js/dataTables.colReorder.js'></script>
<script type='text/javascript' src='../main/js/dataTables.colVis.js'></script>
<!--<script type='text/javascript'>
    $('#dvLoading').show();
    $("#uca").load(function(){ 
    $('#dvLoading').hide();
});
    </script>-->
</head>

<body class="body_top">
    <script type='text/javascript'>
   $(document).ready( function () {
                $('#dvLoading1').show();
                $('#vnfFilter1').DataTable( {   "iDisplayLength": 100
                } );
                $('#dvLoading1').hide(); 
            } );
//            function win1(gname,form_id,encounter,pid,form_name) {
//                  var gname1=gname; 
//                  var form_id1=form_id;
//                  var encounter1=encounter;
//                  var pid1=pid;
//                  var form_name1=form_name;
//                 // alert("allcare_enc_forms.php?groupname="+gname1+"&form_id1="+form_id1+"&enc3='"+encounter1+"'&pid1="+pid1+"&inmode1=edit&file_name1=Incomplete_charts&fname1="+form_name1+"");
//                  window.open("allcare_enc_forms.php?groupname="+gname1+"&form_id1="+form_id1+"&enc3="+encounter1+"&pid1="+pid1+"&inmode1=edit&file_name1=Incomplete_charts&fname1="+form_name1+"", "Window2", "width=600,height=600,scrollbars=yes");
//                }
    </script>
    <?php 
    
    function VisitsWithNoForms($resid1,$respid1,$resenc1,$resfname1,$ros,$pe)
       {
                             
                               
                                if($resfname1=='Allcare Encounter Forms'){
//                                    $query="SELECT DISTINCT (lb.form_id),f.*,p.sex,p.deceased_stat,p.practice_status,p.cpo ,CONCAT(p.fname,' ',p.lname) AS pname
//                                           FROM forms f
//                                           LEFT JOIN lbf_data lb ON lb.form_id = f.form_id
//                                           LEFT JOIN patient_data p ON p.pid=f.pid
//                                           WHERE (select count(*) from lbf_data  where field_id LIKE  '%chief_complaint_%' AND form_id='".$resid1."')=0"
//                                         . " AND f.pid=".$respid1." AND f.encounter=".$resenc1." AND f.form_id='".$resid1."' AND deleted=0 ";
//                                     
//                                    $r2=sqlStatement($query);
//                                    while ($frow3 = sqlFetchArray( $r2)) { //echo "<pre>";print_r($frow); echo "</pre>";
//                                            $ext1[]= $frow3;
//                                        }
//                                        array_push($ext1, "cheif_complaint");
//                                       // echo "<pre>";print_r($ext1); echo "</pre>";
//                                        
//                                       return $ext1;
                                     
                                          
                                      
                                        
                                    $ra = sqlStatement("select count(*) as cnt from lbf_data  where field_id LIKE  '%chief_complaint_%' AND form_id='".$resid1."'");
                                    $rb = sqlStatement("select count(*) as cnt from lbf_data  where field_id LIKE  '%hpi_%' AND form_id='".$resid1."'");
                                    $rc = sqlStatement("select count(*) as cnt from lbf_data  where field_id LIKE  '%assessment_note_%' AND form_id='".$resid1."'");
                                    $rd= sqlStatement("select count(*) as cnt from lbf_data  where field_id LIKE  '%progress_note_%' AND form_id='".$resid1."'");
                                    $re= sqlStatement("select count(*) as cnt from lbf_data  where field_id LIKE  '%plan_note_%' AND form_id='".$resid1."'");
                                    
                                    $a = $b = $c = $d = 0;
                                    while($frowa = sqlFetchArray($ra)){
                                        $a = $frowa['cnt'];
                                    }
                                    while($frowb = sqlFetchArray($rb)){
                                        $b = $frowb['cnt'];
                                    }
                                    while($frowc = sqlFetchArray($rc)){
                                        $c = $frowc['cnt'];
                                    }
                                    while($frowd = sqlFetchArray($rd)){
                                        $d = $frowd['cnt'];
                                    }
                                     while($frowe = sqlFetchArray($re)){
                                        $e = $frowe['cnt'];
                                    }
                                    if($ros=='NO'){
                                     //$fname='<a href="/interface/reports/form_load.php?formname=allcare_ros&edit=custom">Allcare_ros</a>'.",";
                                     $fname="<a href='/interface/forms/allcare_ros/new1.php?formname=allcare_ros&encounter=$resenc1&pid=$respid1'
                                                   ><span>".
                                                    htmlspecialchars( xl('Allcare Ros'), ENT_NOQUOTES)."</span></a>,";
                                    }
                                    if($pe=='NO'){
                                     $fname.="<a href='/interface/forms/allcare_physical_exam/new1.php?formname=allcare_physical_exam&edit=custom_pe&encounter=$resenc1&pid=$respid1'
                                                   ><span>".
                                                    htmlspecialchars( xl('Allcare Physical_exam'), ENT_NOQUOTES)."</span></a>,";
                                      //$fname.="<a href='/interface/reports/form_load.php?formname=allcare_physical_exam&encounter=$resenc1&pid=$respid1'>Allcare Physical_exam</a>".",";
//                                     $fname.= "<a href='javascript:win1('/interface/patient_file/encounter/load_form.php?formname=allcare_physical_exam&edit=custom_pe');' onmouseover='self.status='Open A Window'; return true;'>Chief Complaint</a>".",";
                                    }
                                   
                                    
                                    if($a==0){
                                           $fname.="<a href='allcare_enc_forms.php?groupname=Chief Complaint&form_id1=".$resid1."&enc3=".$resenc1."&pid1=".$respid1."&inmode1=edit&file_name1=Incomplete_charts&fname1=".$resfname1."
                                                  'onclick='top.restoreSession()' ><span>".
                                                    htmlspecialchars( xl('Chief Complaint'), ENT_NOQUOTES)."</span></a>,";
//                                         $groupname='"Chief Complaint"';
//                                         $form_name='"Allcare Encounter Forms"';
//                                         $fname.= "<a href='javascript:win1($groupname,$resid1,$resenc1,$respid1,$form_name);' onmouseover='self.status='Open A Window'; return true;'>Chief Complaint</a>".",";
                                    }
                                    if($b==0){
                                        $fname.="<a href='allcare_enc_forms.php?groupname=History of Present illness&form_id1=".$resid1."&enc3=".$resenc1."&pid1=".$respid1."&inmode1=edit&file_name1=Incomplete_charts&fname1=".$resfname1."
                                                  'onclick='top.restoreSession()' ><span>".
                                                    htmlspecialchars( xl('History of Present illness'), ENT_NOQUOTES)."</span></a>,";
//                                         $groupname='"History of Present illness"';
//                                         $form_name='"Allcare Encounter Forms"';
//                                         $fname.= "<a href='javascript:win1($groupname,$resid1,$resenc1,$respid1,$form_name);' onmouseover='self.status='Open A Window'; return true;'>History of Present illness</a>".",";
                                    }
                                    if($c==0){
                                           $fname.="<a href='allcare_enc_forms.php?groupname=Assessment Note&form_id1=".$resid1."&enc3=".$resenc1."&pid1=".$respid1."&inmode1=edit&file_name1=Incomplete_charts&fname1=".$resfname1."
                                                  'onclick='top.restoreSession()' ><span>".
                                                    htmlspecialchars( xl('Assessment Note'), ENT_NOQUOTES)."</span></a>,";
//                                         $groupname='"Assessment Note"';
//                                         $form_name='"Allcare Encounter Forms"';
//                                         $fname.= "<a href='javascript:win1($groupname,$resid1,$resenc1,$respid1,$form_name);' onmouseover='self.status='Open A Window'; return true;'>Assessment Note</a>".",";
                                    }
                                    if($d==0){
                                           $fname.="<a href='allcare_enc_forms.php?groupname=Progress Note&form_id1=".$resid1."&enc3=".$resenc1."&pid1=".$respid1."&inmode1=edit&file_name1=Incomplete_charts&fname1=".$resfname1."
                                                  'onclick='top.restoreSession()' ><span>".
                                                    htmlspecialchars( xl('Progress Note'), ENT_NOQUOTES)."</span></a>,";
//                                         $groupname='"Progress Note"';
//                                         $form_name='"Allcare Encounter Forms"';
//                                         $fname.= "<a href='javascript:win1($groupname,$resid1,$resenc1,$respid1,$form_name);' onmouseover='self.status='Open A Window'; return true;'>Progress Note</a>".",";
                                         
                                    }
                                    if($e==0){
                                           $fname.="<a href='allcare_enc_forms.php?groupname=Plan Note&form_id1=".$resid1."&enc3=".$resenc1."&pid1=".$respid1."&inmode1=edit&file_name1=Incomplete_charts&fname1=".$resfname1."
                                                  'onclick='top.restoreSession()' ><span>".
                                                    htmlspecialchars( xl('Plan Note'), ENT_NOQUOTES)."</span></a>,";
//                                         $groupname='"Plan Note"';
//                                         $form_name='"Allcare Encounter Forms"';
//                                         $fname.= "<a href='javascript:win1($groupname,$resid1,$resenc1,$respid1,$form_name);' onmouseover='self.status='Open A Window'; return true;'>Plan Note</a>".",";
                                    }
                                   $fname1=rtrim($fname,",");
                                  // print_r($fname1);
                                     if($a==0 || $b==0 || $c==0 || $d==0 || $e==0){
                                         $sql1=sqlStatement("select * from forms where form_name='Audit Form' AND deleted=0 AND encounter='$resenc1'");
                                         $audit_res=sqlFetchArray($sql1);
                                         if(empty($audit_res)){
                                         // echo "select CONCAT(p.fname,' ',p.lname) AS pname,p.sex,encounter,form_name from forms f INNER JOIN patient_data p ON p.pid=f.pid  where form_name='Allcare Encounter Forms' AND deleted=0 AND form_id='".$resid1."'";
                                         $sql = sqlStatement("select CONCAT(p.fname,' ',p.lname) AS pname,p.sex,encounter from forms f INNER JOIN patient_data p ON p.pid=f.pid  where form_name='Allcare Encounter Forms' AND deleted=0 AND form_id='".$resid1."'");
                                         $frow3 = sqlFetchArray($sql); 
                                         array_push($frow3,'Incomplete');
                                         array_push($frow3,$fname1);
                                         } else {
                                              $sql2=sqlStatement("select * from tbl_form_audit where id='".$audit_res['form_id']."'");
                                              $audit_st = sqlFetchArray($sql2);
                                              if(!empty($audit_st)){
                                                  $sql = sqlStatement("select CONCAT(p.fname,' ',p.lname) AS pname,p.sex,f.encounter ,fe.audited_status from forms f INNER JOIN patient_data p ON p.pid=f.pid  INNER JOIN  form_encounter fe ON fe.encounter=f.encounter where form_name='Allcare Encounter Forms' AND deleted=0 AND f.encounter='".$resenc1."'");
                                                  $frow3 = sqlFetchArray($sql);
                                                  if(!empty($frow3) && $frow3['audited_status']=='Complete'){
                                                      array_push($frow3,'Complete');   
                                                      array_push($frow3,$fname1);
                                                  }else {
                                                      array_push($frow3,'Incomplete');   
                                                      array_push($frow3,$fname1);
                                                  }
                                                }
                                            }
                                       } else {
                                            $sql1=sqlStatement("select * from forms where form_name='Audit Form' AND deleted=0 AND encounter='$resenc1'");
                                         $audit_res=sqlFetchArray($sql1);
                                         if(empty($audit_res)){
                                         // echo "select CONCAT(p.fname,' ',p.lname) AS pname,p.sex,encounter,form_name from forms f INNER JOIN patient_data p ON p.pid=f.pid  where form_name='Allcare Encounter Forms' AND deleted=0 AND form_id='".$resid1."'";
                                         $sql = sqlStatement("select CONCAT(p.fname,' ',p.lname) AS pname,p.sex,encounter from forms f INNER JOIN patient_data p ON p.pid=f.pid  where form_name='Allcare Encounter Forms' AND deleted=0 AND form_id='".$resid1."'");
                                         $frow3 = sqlFetchArray($sql); 
                                         array_push($frow3,'Incomplete');
                                         array_push($frow3,$fname1);
                                         } else {
                                              $sql2=sqlStatement("select * from tbl_form_audit where id='".$audit_res['form_id']."'");
                                              $audit_st = sqlFetchArray($sql2);
                                              if(!empty($audit_st)){
                                                  $sql = sqlStatement("select CONCAT(p.fname,' ',p.lname) AS pname,p.sex,f.encounter ,fe.audited_status from forms f INNER JOIN patient_data p ON p.pid=f.pid  INNER JOIN  form_encounter fe ON fe.encounter=f.encounter where form_name='Allcare Encounter Forms' AND deleted=0 AND f.encounter='".$resenc1."'");
                                                  $frow3 = sqlFetchArray($sql);
                                                  if(!empty($frow3) && $frow3['audited_status']=='Complete'){
                                                      array_push($frow3,'Complete');   
                                                      array_push($frow3,$fname1);
                                                  }else {
                                                      array_push($frow3,'Incomplete');   
                                                      array_push($frow3,$fname1);
                                                  }
                                                }
                                            }
                                       }
                                       
                                   // echo "<pre>"; print_r($frow3); echo "</pre>";
                                       return $frow3;
                                    } 
       }?>
   
<div  style='margin-top:10px'> <!-- start main content div -->
<div id="dvLoading1" style="display:none"></div>
<div id="div_noform">
<table class='display'  id='vnfFilter1' border="1">

<?php
//    $field_id3=array();
//    $title3=array();
// $fres = sqlStatement("SELECT * FROM layout_options " .
//    "WHERE form_id = 'LBF2' AND uor > 0 AND field_id != '' AND group_name LIKE '%2Chief Complaint%'" .
//    "ORDER BY  seq");
//  while ($frow = sqlFetchArray($fres)) {
//    $field_id3[]=  $frow['field_id'];
//    $title3[] = $frow['title'];
//      
//  }
  
 
	 print "<thead><tr class='showborder_head'>"
         . "<th style='width:180px;'>".htmlspecialchars( xl('Patient Name'), ENT_NOQUOTES)."</th>"."<th style='width:180px;'>".htmlspecialchars( xl('Gender'), ENT_NOQUOTES)."</th>"."<th style='width:180px;'>".htmlspecialchars( xl('Encounter'), ENT_NOQUOTES)."</th>"."<th style='width:180px;'>".htmlspecialchars( xl('Form_name'), ENT_NOQUOTES)."</th>".
                  "<th style='width:180px;'>".htmlspecialchars( xl('Audit Status'), ENT_NOQUOTES)."</th>";
	 
//          foreach($title3 as $label)
//                        { 
//                        echo "<th style='width:180px;'>".htmlspecialchars( xl($label), ENT_NOQUOTES)."</th>" ;
//                        }
          echo "</tr></thead>\n";
		

             
            //$resid1=sqlStatement("select DISTINCT(f.form_id),f.pid,f.encounter from forms f INNER JOIN patient_data p ON p.pid=f.pid  INNER JOIN form_encounter fe ON fe.pid=f.pid AND fe.encounter=f.encounter where deleted=0 AND form_name='Allcare Encounter Forms' AND formdir='LBF2' AND p.cpo='YES' AND fe.pc_catid IN (15, 16, 17, 18, 19, 20, 24, 25, 29, 44 )");
//          $resid2=sqlStatement("SELECT DISTINCT (
//                                f.form_id
//                                ), f.pid, f.encounter,f.form_name
//                                FROM forms f
//                                INNER JOIN patient_data p ON p.pid = f.pid
//                                INNER JOIN form_encounter fe ON fe.pid = f.pid
//                                AND fe.encounter = f.encounter
//                                WHERE deleted =0
//                                AND p.cpo =  'YES'
//                                AND fe.pc_catid
//                                IN ( 15, 16, 17, 18, 19, 20, 24, 25, 29, 44 ) AND f.form_name IN ('Allcare Encounter Forms')  AND deleted=0");  
          
          $resid2=sqlStatement("SELECT DISTINCT (f.form_id), f.pid, f.encounter,f.form_name
                                FROM forms f                               
                                INNER JOIN patient_data p ON p.pid = f.pid
                                INNER JOIN form_encounter fe ON fe.pid = f.pid
                                AND fe.encounter = f.encounter
                                WHERE deleted =0
                                AND p.cpo =  'YES'
                                AND fe.pc_catid
                                IN ( 15, 16, 17, 18, 19, 20, 24, 25, 29, 44 ) AND f.form_name IN ('Allcare Encounter Forms')   AND deleted=0"); 
          while ($resid_row1=sqlFetchArray($resid2)){
              $f2fenc_Y[]=$resid_row1['encounter'];
           }
          $uni_Y=array_unique($f2fenc_Y);
          //print_r($uni_Y);
          foreach($uni_Y as $value) {
               $resid1=sqlStatement("select count(*) as count from forms where form_name  IN ('Allcare Review Of Systems','Allcare Physical Exam') AND encounter='$value' AND deleted=0 ");  
              while ($resid_row1=sqlFetchArray($resid1)){
                  if($resid_row1['count']==0) {
                      $pe='NO'; $ros='NO';
                     $resid3=sqlStatement("select *  from forms  where  encounter='$value' AND form_name IN ('Allcare Encounter Forms') AND deleted=0");  
                          while ($resid_row3=sqlFetchArray($resid3)) {

                                 if ($result1 = VisitsWithNoForms($resid_row3['form_id'],$resid_row3['pid'],$resid_row3['encounter'],$resid_row3['form_name'],$ros,$pe)) {
                                   //  echo "<pre>";print_r($result1); echo "</pre>";
                                     echo "<tr height='25'>";
//                                            echo "<td style='width:60px;'>";
//                                            print "<a href='f2f_encounter_form.php?formname=LBF2&form_id=".$result1[0]['form_id']."&enc2=".$result1[0]['encounter']."&pid=".$resid_row1['pid']."&inmode=edit&file_name=VNf2f&fname=".$result1[0]['form_name']."
//                                                    'onclick='top.restoreSession()' class='css_button_small'><span>".
//                                                    htmlspecialchars( xl('Create'), ENT_NOQUOTES)."</span></a>";
//                                            echo "</td>";
                                            echo "<td>".$result1['pname']."</td>";
                                            echo "<td>".$result1['sex']."</td>";
                                            echo "<td>".$result1['encounter']."</td>";
                                            echo "<td>"; echo $result1[1];   echo"</td>";
                                            echo "<td>"; echo $result1[0];   echo"</td>";     
                                           

                                            echo "</tr>\n";
                                 }
                           }
                     } else if($resid_row1['count']!=0){
                          $ros_sql=sqlStatement("select *  from forms  where  encounter='$value' AND form_name IN ('Allcare Review Of Systems') AND deleted=0");
                          $ros_row3=sqlFetchArray($ros_sql);
                          if(!empty($ros_row3)){
                              $ros='YES';
                          }else 
                               $ros='NO';
                          $pe_sql=sqlStatement("select *  from forms  where  encounter='$value' AND form_name IN ('Allcare Physical Exam') AND deleted=0");
                          $pe_row3=sqlFetchArray($pe_sql);
                          if(!empty($pe_row3)){
                              $pe='YES';
                          }else 
                               $pe='NO';
                          $resid3=sqlStatement("select *  from forms  where  encounter='$value' AND form_name IN ('Allcare Encounter Forms') AND deleted=0");  
                          while ($resid_row3=sqlFetchArray($resid3)) {

                                 if ($result1 = VisitsWithNoForms($resid_row3['form_id'],$resid_row3['pid'],$resid_row3['encounter'],$resid_row3['form_name'],$ros,$pe)) {
                                   //  echo "<pre>";print_r($result1); echo "</pre>";
                                     echo "<tr height='25'>";
//                                            echo "<td style='width:60px;'>";
//                                            print "<a href='f2f_encounter_form.php?formname=LBF2&form_id=".$result1[0]['form_id']."&enc2=".$result1[0]['encounter']."&pid=".$resid_row1['pid']."&inmode=edit&file_name=VNf2f&fname=".$result1[0]['form_name']."
//                                                    'onclick='top.restoreSession()' class='css_button_small'><span>".
//                                                    htmlspecialchars( xl('Create'), ENT_NOQUOTES)."</span></a>";
//                                            echo "</td>";
                                            echo "<td>".$result1['pname']."</td>";
                                            echo "<td>".$result1['sex']."</td>";
                                            echo "<td>".$result1['encounter']."</td>";
                                            echo "<td>"; echo $result1[1];   echo"</td>";
                                            echo "<td>"; echo $result1[0];   echo"</td>";     
                                           

                                            echo "</tr>\n";
                                 }
                           }
                     }
                 }
         }   
?>
</table>
<!--</div>-->
   </div> <!-- end main content div -->
</body>
</html>