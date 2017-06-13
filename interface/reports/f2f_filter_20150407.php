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
                $('#dvLoading').show();
                $('#snfFilter').DataTable( {  
                } );
                $('#dvLoading').hide(); 
            } );
    </script>
    <?php 
    $nursing=$_REQUEST['nid'];
    function SubmittedNotSigned($resid1,$respid1,$resenc1,$nursing)
       {
              /* echo "SELECT lb.* ,CONCAT(p.fname,' ',p.lname) AS pname,f.encounter
                            FROM lbf_data lb
                            INNER JOIN layout_options l ON lb.field_id = l.field_id
                            INNER JOIN patient_data p
                            INNER JOIN form_encounter f 
                            WHERE lb.form_id ='".$resid1."' AND p.pid=".$respid1." AND f.encounter=".$resenc1."
                            AND l.group_name =  '7Face to Face HH Plan'
                            AND (select count(*) from lbf_data where field_id='f2f_ps' AND form_id='".$resid1."')=0
                            AND (select count(*) from lbf_data where field_id='f2f_ps_on'  AND field_value!='' AND form_id='".$resid1."')=0    
                            AND (select count(*) from lbf_data where field_id='f2f_stat'  AND field_value!='' AND form_id='".$resid1."')=0    
                            AND (select count(*) from lbf_data where field_id='f2f_np_on' AND field_value!='' AND form_id='".$resid1."')>0    
                            ORDER BY seq";*/
                $ra = sqlStatement("select count(*) as cnt from lbf_data where field_id='f2f_ps' AND form_id='".$resid1."'");
                $rb = sqlStatement("select count(*) as cnt from lbf_data where field_id='f2f_ps_on'  AND field_value!='' AND form_id='".$resid1."'");
                $rc = sqlStatement("select count(*) as cnt from lbf_data where field_id='f2f_stat'  AND field_value IN ('finalized','pending') AND form_id='".$resid1."'");
                $flag = 0;
                $a = $b = $c = 0;
                while($frowa = sqlFetchArray($ra)){
                    $a = $frowa['cnt'];
                }
                while($frowb = sqlFetchArray($rb)){
                    $b = $frowb['cnt'];
                }
                while($frowc = sqlFetchArray($rc)){
                    $c = $frowc['cnt'];
                }
                //echo $resenc1 . " +++ " . $a . " -- " . $b . " -- " . $c . "<br />";

                if($a == 0 || $b == 0 || $c == 0){
                    $r2=sqlStatement("SELECT lb.* ,CONCAT(p.fname,' ',p.lname) AS pname,f.encounter
                                FROM lbf_data lb
                                INNER JOIN layout_options l ON lb.field_id = l.field_id
                                INNER JOIN patient_data p
                                INNER JOIN form_encounter f 
                                WHERE lb.form_id ='".$resid1."' AND p.pid=".$respid1." AND f.encounter=".$resenc1."
                                AND l.group_name =  '7Face to Face HH Plan'
                                AND (select count(*) from lbf_data where field_id='f2f_np_on' AND field_value!='' AND form_id='".$resid1."')>0   
                                AND (select count(*) from lbf_data where field_id='f2f_nursing' AND field_value='".$nursing."' AND form_id='".$resid1."')>0   
                                ORDER BY seq");
                    while ($frow3 = sqlFetchArray( $r2)) { //echo "<pre>";print_r($frow); echo "</pre>";
                            $ext1[]= $frow3;
                        }
                   // echo "<pre>";print_r($ext1); echo "</pre>";
                    return $ext1;
                }    
    }?>
   
    <div  style='margin-top:10px'> <!-- start main content div -->
<div id="dvLoading" style="display:none"></div>
<div id="uca">
<table class='display'  id='snfFilter' border="1">

<?php
    $field_id3=array();
    $title3=array();
 $fres = sqlStatement("SELECT * FROM layout_options " .
    "WHERE form_id = 'LBF2' AND uor > 0 AND field_id != '' AND group_name =  '7Face to Face HH Plan'" .
    "ORDER BY  seq");
  while ($frow = sqlFetchArray($fres)) {
    $field_id3[]=  $frow['field_id'];
    $title3[] = $frow['title'];
      
  }
  
 //print_r($title)."hai";
// Print Heading .. to have better Understanding of the Listed Transactions -- starts here Dec 07,09
	 print "<thead><tr class='showborder_head'><th style='width=10px;'>&nbsp;</th>"
         . "<th style='width:180px;'>".htmlspecialchars( xl('Patient Name'), ENT_NOQUOTES)."</th>"."<th style='width:180px;'>".htmlspecialchars( xl('Encounter'), ENT_NOQUOTES)."</th>";
	 
          foreach($title3 as $label)
                        { 
                        echo "<th style='width:180px;'>".htmlspecialchars( xl($label), ENT_NOQUOTES)."</th>" ;
                        }
          echo "</tr></thead>\n";
		// Print Heading .. to have better Understanding of the Listed Transactions   -- ends here

             
            $resid1=sqlStatement("select f.pid,f.encounter,f.form_id from forms f INNER JOIN lbf_data l ON l.form_id=f.form_id  where deleted=0 AND form_name='Allcare Encounter Forms' AND formdir='LBF2' AND field_id='f2f_np'");
            while ($resid_row1=sqlFetchArray($resid1)){
             if ($result1 = SubmittedNotSigned($resid_row1['form_id'],$resid_row1['pid'],$resid_row1['encounter'],$nursing)) {
                  echo "<tr height='25'><td style='width:60px;'>";
                        print "<a href='f2f_encounter_form.php?formname=LBF2&form_id=".$result1[0]['form_id']."&enc2=".$result1[0]['encounter']."&pid=".$resid_row1['pid']."&inmode=edit
                                'onclick='top.restoreSession()' class='css_button_small'><span>".
                                htmlspecialchars( xl('Edit'), ENT_NOQUOTES)."</span></a>";
                        echo "</td>";
                        echo "<td>".$result1[0]['pname']."</td>";
                        echo "<td>".$result1[0]['encounter']."</td>";
                         
                        
              
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