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
                $('#vnfFilter').DataTable( {   "iDisplayLength": 100
                } );
                $('#dvLoading1').hide(); 
            } );
    </script>
    <?php 
    // $nursing=$_REQUEST['nid1'];
      $deceased=$_REQUEST['deseased_val1'];
      $practice=$_REQUEST['practice_val1'];
      //$cpo=$_REQUEST['cpo_val1'];
    function VisitsWithNoF2F($resid1,$respid1,$resenc1,$resfname1,$uni_Y,$deceased,$practice)
       {
               // print_r($uni_Y);

//                        if($nursing == 'ALL')
//                        {
////                                       
////                                        $query="SELECT p.sex,p.deceased_stat,p.practice_status,p.cpo,lb.* ,CONCAT(p.fname,' ',p.lname) AS pname,f.encounter
////                                            FROM lbf_data lb
////                                            INNER JOIN layout_options l ON lb.field_id = l.field_id
////                                            INNER JOIN patient_data p
////                                            INNER JOIN form_encounter f 
////                                            WHERE lb.form_id ='".$resid1."' AND p.pid=".$respid1." AND f.encounter=".$resenc1." AND field_id LIKE '%f2f_%'
////                                            AND l.group_name LIKE '%Face to Face HH Plan%'"; 
//
//                             if($resfname1=='Allcare Encounter Forms'){
//                                  $query="SELECT *,p.sex,p.deceased_stat,p.practice_status,p.cpo ,CONCAT(p.fname,' ',p.lname) AS pname
//                                            FROM forms f
//                                            LEFT JOIN lbf_data lb ON lb.form_id = f.form_id
//                                            LEFT JOIN patient_data p ON p.pid=f.pid    
//                                            WHERE (select count(*) from lbf_data  where field_id LIKE  '%f2f_%' AND form_id='".$resid1."')=0 AND f.pid=".$respid1." AND f.encounter=".$resenc1." AND lb.form_id='".$resid1."' ";
//
//                                     //Deceased_status
//                                    if($deceased=='ALL'){
//                                        $query.="  AND p.deceased_stat IN ('','YES','NO')";
//                                    }else 
//                                    $query.="  AND p.deceased_stat='$deceased'";
//
//                                  //Practice Status
//                                   if($practice=='ALL'){
//                                        $query.="  AND p.practice_status IN ('','YES','NO')";
//                                    }else 
//                                        $query.="  AND p.practice_status='$practice'";
//
//                                    //CPO
////                                    if($cpo=='ALL'){
////                                        $query.="  AND p.cpo IN ('','YES','NO')";
////                                    }else 
////                                        $query.="  AND p.cpo='$cpo'";
//
//
//                                  //  $query.=" ORDER BY seq";
//                                    //echo $query;
//                                    $r2=sqlStatement($query);
//
//                                    while ($frow3 = sqlFetchArray( $r2)) { //echo "<pre>";print_r($frow); echo "</pre>";
//                                            $ext1[]= $frow3;
//                                        }
//                                  
//                                    return $ext1;
//                             } 
//                             if($resfname1!='Allcare Encounter Forms'){
//                                  if(!in_array($resenc1,$uni_Y)){
//                                       $query2="SELECT f.*,p.sex,p.deceased_stat,p.practice_status,p.cpo ,CONCAT(p.fname,' ',p.lname) AS pname
//                                            FROM forms f
//                                            LEFT JOIN patient_data p ON p.pid=f.pid    
//                                           WHERE  f.pid=".$respid1." AND f.encounter=".$resenc1."";
//                                      // Deceased_status
//                                    if($deceased=='ALL'){
//                                        $query2.="  AND p.deceased_stat IN ('','YES','NO')";
//                                    }else 
//                                    $query2.="  AND p.deceased_stat='$deceased'";
//
//                                  //Practice Status
//                                   if($practice=='ALL'){
//                                        $query2.="  AND p.practice_status IN ('','YES','NO')";
//                                    }else 
//                                        $query2.="  AND p.practice_status='$practice'";
//
//                                    $r3=sqlStatement($query2);
//                                    while ($frow4 = sqlFetchArray( $r3)) { //echo "<pre>";print_r($frow); echo "</pre>";
//                                            $ext2[]= $frow4;
//                                        }
//                                  }
//                                   return $ext2;
//                             }
//                        }
                   //  else {

//                                       $query= "SELECT p.sex,p.deceased_stat,p.practice_status,p.cpo,lb.* ,CONCAT(p.fname,' ',p.lname) AS pname,f.encounter
//                                            FROM lbf_data lb
//                                            INNER JOIN layout_options l ON lb.field_id = l.field_id
//                                            INNER JOIN patient_data p
//                                            INNER JOIN form_encounter f 
//                                            WHERE lb.form_id ='".$resid1."' AND p.pid=".$respid1." AND f.encounter=".$resenc1." AND field_id LIKE '%f2f_%'
//                                            AND l.group_name LIKE '%Face to Face HH Plan%'"; 

                                if($resfname1=='Allcare Encounter Forms'){
                                      $query="SELECT f.*,p.sex,p.deceased_stat,p.practice_status,p.cpo ,CONCAT(p.fname,' ',p.lname) AS pname,fe.facility,fe.date,fe.pc_catid
                                            FROM forms f
                                           LEFT JOIN lbf_data lb ON lb.form_id = f.form_id
                                           LEFT JOIN patient_data p ON p.pid=f.pid   
                                           INNER JOIN form_encounter fe ON fe.encounter=".$resenc1." AND fe.pid=".$respid1."
                                           WHERE (select count(*) from lbf_data  where field_id LIKE  '%f2f_%' AND form_id='".$resid1."')=0 AND f.pid=".$respid1." AND f.encounter=".$resenc1." AND f.form_id='".$resid1."' AND deleted=0";
                                      // Deceased_status
                                    if($deceased=='ALL'){
                                        $query.="  AND p.deceased_stat IN ('','YES','NO')";
                                    }else 
                                    $query.="  AND p.deceased_stat='$deceased'";

                                  //Practice Status
                                   if($practice=='ALL'){
                                        $query.="  AND p.practice_status IN ('','YES','NO')";
                                    }else 
                                        $query.="  AND p.practice_status='$practice'";

//                                            //CPO
//                                            if($cpo=='ALL'){
//                                                $query.="  AND p.cpo IN ('','YES','NO')";
//                                            }else 
//                                                $query.="  AND p.cpo='$cpo'";
//                                            
                              
                              //  $query.=" ORDER BY seq";
                                $r2=sqlStatement($query);
                                    while ($frow3 = sqlFetchArray( $r2)) { //echo "<pre>";print_r($frow); echo "</pre>";
                                            $ext1[]= $frow3;
                                        }
                                         return $ext1;
                                } 

                                if($resfname1!='Allcare Encounter Forms'){
                                    if(!in_array($resenc1,$uni_Y)){
                                        $query2="SELECT f.*,p.sex,p.deceased_stat,p.practice_status,p.cpo ,CONCAT(p.fname,' ',p.lname) AS pname,fe.facility,fe.date,fe.pc_catid
                                            FROM forms f
                                            LEFT JOIN patient_data p ON p.pid=f.pid  
                                            INNER JOIN form_encounter fe ON fe.encounter=".$resenc1." AND fe.pid=".$respid1."
                                           WHERE  f.pid=".$respid1." AND f.encounter=".$resenc1." AND deleted=0";
                                      // Deceased_status
                                    if($deceased=='ALL'){
                                        $query2.="  AND p.deceased_stat IN ('','YES','NO')";
                                    }else 
                                    $query2.="  AND p.deceased_stat='$deceased'";

                                  //Practice Status
                                   if($practice=='ALL'){
                                        $query2.="  AND p.practice_status IN ('','YES','NO')";
                                    }else 
                                        $query2.="  AND p.practice_status='$practice'";
                                    
                                    $r3=sqlStatement($query2);
                                    while ($frow4 = sqlFetchArray( $r3)) { //echo "<pre>";print_r($frow); echo "</pre>";
                                            $ext2[]= $frow4;
                                        }
                                    }
                                    return $ext2;
                                }

                                // echo "<pre>"; print_r($ext2); echo "</pre>";
                                 //return $ext1;
                          //}
                      
    }?>
   
    <div  style='margin-top:10px'> <!-- start main content div -->
<div id="dvLoading1" style="display:none"></div>
<div id="div_noF2F">
<table class='display'  id='vnfFilter' border="1">

<?php
    $field_id3=array();
    $title3=array();
 $fres = sqlStatement("SELECT * FROM layout_options " .
    "WHERE form_id = 'LBF2' AND uor > 0 AND field_id != '' AND group_name LIKE '%Face to Face HH Plan%'" .
    "ORDER BY  seq");
  while ($frow = sqlFetchArray($fres)) {
    $field_id3[]=  $frow['field_id'];
    $title3[] = $frow['title'];
      
  }
  
 
	 print "<thead><tr class='showborder_head'><th style='width=10px;'>&nbsp;</th>"
         . "<th style='width:180px;'>".htmlspecialchars( xl('Patient Name'), ENT_NOQUOTES)."</th>"."<th style='width:180px;'>".htmlspecialchars( xl('Gender'), ENT_NOQUOTES)."</th>"."<th style='width:180px;'>".htmlspecialchars( xl('Encounter'), ENT_NOQUOTES)."</th>".
                  "<th style='width:180px;'>".htmlspecialchars( xl('Date_of_service'), ENT_NOQUOTES)."</th>"."<th style='width:180px;'>".htmlspecialchars( xl('Visit_Category'), ENT_NOQUOTES)."</th>"."<th style='width:180px;'>".htmlspecialchars( xl('Facility'), ENT_NOQUOTES)."</th>"."<th style='width:180px;'>".htmlspecialchars( xl('Deceased_status'), ENT_NOQUOTES)."</th>".
                  "<th style='width:180px;'>".htmlspecialchars( xl('Practice Status'), ENT_NOQUOTES)."</th>"."<th style='width:180px;'>".htmlspecialchars( xl('CPO'), ENT_NOQUOTES)."</th>";
	 
          foreach($title3 as $label)
                        { 
                        echo "<th style='width:180px;'>".htmlspecialchars( xl($label), ENT_NOQUOTES)."</th>" ;
                        }
          echo "</tr></thead>\n";
		

             
            //$resid1=sqlStatement("select DISTINCT(f.form_id),f.pid,f.encounter from forms f INNER JOIN patient_data p ON p.pid=f.pid  INNER JOIN form_encounter fe ON fe.pid=f.pid AND fe.encounter=f.encounter where deleted=0 AND form_name='Allcare Encounter Forms' AND formdir='LBF2' AND p.cpo='YES' AND fe.pc_catid IN (15, 16, 17, 18, 19, 20, 24, 25, 29, 44 )");
          $resid2=sqlStatement("SELECT DISTINCT (
                                f.form_id
                                ), f.pid, f.encounter,f.form_name
                                FROM forms f
                                INNER JOIN patient_data p ON p.pid = f.pid
                                INNER JOIN form_encounter fe ON fe.pid = f.pid
                                AND fe.encounter = f.encounter
                                WHERE deleted =0
                                AND p.cpo =  'YES'
                                AND fe.pc_catid
                                IN ( 15, 16, 17, 18, 19, 20, 24, 25, 29, 44 ) AND f.form_name IN ('New Patient Encounter','Allcare Encounter Forms') AND deleted=0");  
          while ($resid_row1=sqlFetchArray($resid2)){
              if($resid_row1['form_name']=='Allcare Encounter Forms'){
                  $f2fenc_Y[]=$resid_row1['encounter'];
              } else {
                   $f2fenc_N[]=$resid_row1['encounter'];
              }
          }
          $uni_Y=array_unique($f2fenc_Y);
         // print_r($uni_Y);
          $uni_N=array_unique($f2fenc_N);
          //print_r($uni_N);
          $resid1=sqlStatement("SELECT DISTINCT (
                                f.form_id
                                ), f.pid, f.encounter,f.form_name
                                FROM forms f
                                INNER JOIN patient_data p ON p.pid = f.pid
                                INNER JOIN form_encounter fe ON fe.pid = f.pid
                                AND fe.encounter = f.encounter
                                WHERE deleted =0
                                AND p.cpo =  'YES'
                                AND fe.pc_catid
                                IN ( 15, 16, 17, 18, 19, 20, 24, 25, 29, 44 ) AND f.form_name IN ('New Patient Encounter','Allcare Encounter Forms' ) AND deleted=0");  
          while ($resid_row1=sqlFetchArray($resid1)){
             
             if ($result1 = VisitsWithNoF2F($resid_row1['form_id'],$resid_row1['pid'],$resid_row1['encounter'],$resid_row1['form_name'],$uni_Y,$deceased,$practice)) {
               //  echo "<pre>";print_r($result1); echo "</pre>";
                 echo "<tr height='25'><td style='width:60px;'>";
                        print "<a href='f2f_encounter_form.php?formname=LBF2&form_id=".$result1[0]['form_id']."&enc2=".$result1[0]['encounter']."&pid=".$resid_row1['pid']."&inmode=edit&file_name=VNf2f&fname=".$result1[0]['form_name']."
                                'onclick='top.restoreSession()' class='css_button_small'><span>".
                                htmlspecialchars( xl('Create'), ENT_NOQUOTES)."</span></a>";
                        echo "</td>";
                        echo "<td>".$result1[0]['pname']."</td>";
                        echo "<td>".$result1[0]['sex']."</td>";
                        echo "<td>".$result1[0]['encounter']."</td>";
                        echo "<td>"; $date=explode(" ",$result1[0]['date']); echo $date[0]; echo"</td>";
                        echo "<td>"; $catid=$result1[0]['pc_catid']; $sql=sqlStatement("SELECT * FROM  `openemr_postcalendar_categories` where pc_catid=$catid"); $row=sqlFetchArray($sql); echo $row['pc_catname']; echo"</td>";
                        echo "<td>".$result1[0]['facility']."</td>";
                        echo "<td>".$result1[0]['deceased_stat']."</td>";  
                        echo "<td>".$result1[0]['practice_status']."</td>";
                        echo "<td>".$result1[0]['cpo']."</td>";
                        
              
                    foreach($field_id3 as $val){ 
                        echo" <td style='width:150px;'>";
                        for($i=0; $i<count($result1); $i++){
                           if($val == $result1[$i]['field_id'] ){ 
                                if($result1[$i]['field_id']=='f2f_np')
                                    {   
                                    $res=sqlStatement("select concat(fname,lname) as name from users where id='".$result1[$i]['field_value']."'");
                                    $row=sqlFetchArray($res);
                                     echo $data = $row['name'];
                                    }
                                    else if($result1[$i]['field_id']=='f2f_ps'){
                                    $res=sqlStatement("select concat(fname,lname) as name1 from users where id='".$result1[$i]['field_value']."'");
                                    $row=sqlFetchArray($res);
                                    echo $data = $row['name1'];
                                    }
                                  else {
                                      echo $data = $result1[$i]['field_value'];
                                  }
                                
                            }else{ 
                            }
                        }
                        echo "</td>";    
                    } 
                    echo "</tr>\n";
            
                     
           
                 }
            }
?>
</table>
<!--</div>-->
   </div> <!-- end main content div -->
</body>
</html>