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
//SANITIZE ALL ESCAPES
$sanitize_all_escapes=true;

//STOP FAKE REGISTER GLOBALS
$fake_register_globals=false;

require_once("../globals.php");
require_once("../../library/formdata.inc.php");
require_once("../../library/globals.inc.php");

function SignedButNoTransaction($resid,$respid,$resenc)
{ 
 
       $r1=sqlStatement("SELECT  lb.*, CONCAT(p.fname,' ',p.lname) AS pname,f.encounter
                        FROM lbf_data lb
                        INNER JOIN layout_options l ON lb.field_id = l.field_id
                        INNER JOIN patient_data p 
                        INNER JOIN form_encounter f
                        WHERE lb.form_id ='".$resid."' AND p.pid=".$respid." AND f.encounter=".$resenc."
                        AND l.group_name =  '7Face to Face HH Plan' AND (select count(*) from lbf_data where field_id='f2f_ps' AND form_id='".$resid."')>0
                        AND (select count(*) from lbf_data where field_id='f2f_ps_on' AND field_value!='' AND form_id='".$resid."')>0
                        AND (select count(*) from lbf_data where field_id='f2f_stat' AND field_value='finalized' AND form_id='".$resid."')=0
                        AND (select count(*) from lbf_data where field_id='f2f_stat' AND field_value='pending' AND form_id='".$resid."')=0    
                        ORDER BY seq");

            while ($frow2 = sqlFetchArray( $r1)) { //echo "<pre>";print_r($frow2); echo "</pre>";
                    $ext[]= $frow2;
                }
               // echo "<pre>";print_r($ext); echo "</pre>";
          return $ext;
}

function SubmittedNotSigned($resid1,$respid1,$resenc1,$nursing)
       {
              
                $ra = sqlStatement("select count(*) as cnt from lbf_data where field_id='f2f_ps' AND form_id='".$resid1."'");
                $rb = sqlStatement("select count(*) as cnt from lbf_data where field_id='f2f_ps_on'  AND field_value!='' AND form_id='".$resid1."'");
                $rc = sqlStatement("select count(*) as cnt from lbf_data where field_id='f2f_stat'  AND field_value='finalized' AND form_id='".$resid1."'");
                $rd= sqlStatement("select count(*) as cnt from lbf_data where field_id='f2f_stat'  AND field_value='pending' AND form_id='".$resid1."'");
                $flag = 0;
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
                //echo $resenc1 . " +++ " . $a . " -- " . $b . " -- " . $c . "<br />";
               
                if($a == 0 || $b == 0 && $c == 0){
                    if($c == 0 && $d == 0)
                    {
                            $r2=sqlStatement("SELECT lb.* ,CONCAT(p.fname,' ',p.lname) AS pname,f.encounter
                                FROM lbf_data lb
                                INNER JOIN layout_options l ON lb.field_id = l.field_id
                                INNER JOIN patient_data p
                                INNER JOIN form_encounter f 
                                WHERE lb.form_id ='".$resid1."' AND p.pid=".$respid1." AND f.encounter=".$resenc1."
                                AND l.group_name =  '7Face to Face HH Plan'
                                AND (select count(*) from lbf_data where field_id='f2f_np_on' AND field_value!='' AND form_id='".$resid1."')>0  
                                ORDER BY seq");
                    while ($frow3 = sqlFetchArray( $r2)) { //echo "<pre>";print_r($frow); echo "</pre>";
                            $ext1[]= $frow3;
                        }
                   // echo "<pre>";print_r($ext1); echo "</pre>";
                    return $ext1;
                }  
            }
    }
    
?>

    <head>
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
        .link1 {
         font-family: sans-serif;
         text-decoration: none;
         color: #0000cc;
         padding-left:30px;
         //font-size: 70%;
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

</style>-->
<script type='text/javascript' src='../main/js/jquery-1.11.1.min.js'></script>
<script type='text/javascript' src='../main/js/jquery.dataTables.min.js'></script>
<script type='text/javascript' src='../main/js/dataTables.tableTools.js'></script>
<script type='text/javascript' src='../main/js/dataTables.colReorder.js'></script>
<script type='text/javascript' src='../main/js/dataTables.colVis.js'></script>

<script type='text/javascript'>
   /*  function dropdownchange(){
                if($( "#nursing option:selected" ).val() == 0){
                   $('#uca').hide(); 
                   $('#unassign').show();
                }
               else if($( "#nursing option:selected" ).val() == 'YES'){
                   var nid = $( "#nursing option:selected" ).val();
                    
                   
                   // $("#uca").load("f2f_filter.php?nid="+nid,function(){ 
                    //$('#dvLoading').hide();
                     $('#unassign').hide();
                     $('#uca').show();
                      $('#dvLoading').show();
                     $.ajax({
                                url: 'f2f_filter.php',
                                type: 'POST',
                                data:  { nid:nid },
                                success: function(content)
                                {
                                    $("#uca").html(content);
                                    
                                }  
                        });
            }
            
         } */
    
    
    
    function dropdownchange(){
                if($( "#nursing option:selected" ).val() == 0){
                   $('#uca').hide(); 
                   $('#unassign').show();
                }
               else if($( "#nursing option:selected" ).val() !== 0){
                    var nid = $( "#nursing option:selected" ).val();
                    
                    $('#unassign').hide();
                    $('#uca').show(); 
                    $('#dvLoading').show();
                    $("#uca").load("f2f_filter.php?nid="+nid,function(){ 
                    $('#dvLoading').hide();
                });
            }
         }

</script>
</head>
 <body class="body_top" style="background-color:#FFFFCC;" >
     
     <script type='text/javascript'>
            
            $(document).ready( function () {
                $('#snt').DataTable( {
                } );
                $( window ).load(function() {
                if (window.location.href.indexOf('reload')==-1) {
                     window.location.replace(window.location.href+'?reload');
                }
            });
            jQuery("#nursing").find("option:contains('unassigned')").each(function()
                { 
                 if( jQuery(this).text() == 'unassigned' )
                 { 
                  jQuery(this).attr("selected","selected");
                   $('#unassign').show();
                  }
                });
                $('#snf').DataTable( {
                } );
            } );
            
    </script>
<!-- Required for the popup date selectors -->
<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>

<span class='title'><?php xl('Report','e'); ?> - <?php xl('FacetoFace Encounters','e'); ?></span>

<a href="javascript:;" class="link1" onclick="window.open( 'http://<?php echo $_SERVER[HTTP_HOST]; ?>/interface/reports/details.php', '', 'width=500, height=600')"> [Details] </a>
<ul class="tabNav">

<li class='current'>
<a id="tab_fnp" onclick="javascript:$('#theform_snf').hide();
                       $('#theform_fnp').show();
                       $('.tabNav > li').removeClass('current');
                       $(this).parent('li').addClass('current');" style='cursor:pointer'>Signed But No Transaction</a>
</li>
<li>
<a id="tab_snf" onclick="javascript:$('#theform_fnp').hide();
                       $('#theform_snf').show();
                       $('.tabNav > li').removeClass('current');
                       $(this).parent('li').addClass('current');" style='cursor:pointer'>Submitted But Not Signed</a>
</li>
</ul>

<?php
 if($_GET['showForm']=='snf')
 {
     echo "<script>
$('.tabNav > li').removeClass('current');
$('#tab_snf').parent('li').addClass('current');
</script>";
 }

?>
<form method='post' name='theform_fnp' id='theform_fnp' action='f2f_encounters_report.php?showForm=fnp'
      <?php if(($_GET['showForm'])=='snf') 
              {echo "style='display:none;'";}
              ?>>
         
<!----------------- Finalized But not printed Starts ---------->
    
<div id="div_signed_but_no transaction">
        
<div id="fnp_report_parameters">
<!--<script type='text/javascript'>    
 $(document).ready(function() 
    { 
        $('#snt').tablesorter({ headers: { 0: { sorter: false}, 1: {sorter: false},2: {sorter: false} } });
    } 
);   
</script>   --> 
<table class='display'  id='snt' border="1">

<?php

 /*$field_id=array();*/
 $title1=array();
 $fres = sqlStatement("SELECT * FROM layout_options " .
    "WHERE form_id = 'LBF2' AND uor > 0 AND field_id != '' AND group_name =  '7Face to Face HH Plan'" .
    "ORDER BY  seq");
  while ($frow = sqlFetchArray($fres)) {
    $title1[] = $frow['title'];
    $field_id4[]=$frow['field_id'];
      
  }
  
  
	 print "<thead><tr class='showborder_head'><th style='width=10px;'>&nbsp;</th><th style='width=10px;'>&nbsp;</th>"
               . "<th style='width:180px;'>".htmlspecialchars( xl('Patient Name'), ENT_NOQUOTES)."</th>"."<th style='width:180px;'>".htmlspecialchars( xl('Encounter'), ENT_NOQUOTES)."</th>";
          foreach($title1 as $label)
                        { 
                        echo "<th style='width:180px;'>".htmlspecialchars( xl($label), ENT_NOQUOTES)."</th>" ;
                        }
          echo "</tr></thead>\n";
		
          
        $resid=sqlStatement("select DISTINCT(f.form_id) ,f.pid, f.encounter from forms f INNER JOIN lbf_data l ON l.form_id=f.form_id where deleted=0 AND form_name='Allcare Encounter Forms' AND formdir='LBF2'");   
        while ($resid_row=sqlFetchArray($resid)){
            $fid=sqlStatement("SELECT field_id 
                    FROM lbf_data l
                    INNER JOIN forms f ON f.form_id = l.form_id 
                    WHERE f.deleted =0
                    AND formdir =  'LBF2'
                    AND l.form_id ='".$resid_row['form_id']."'");
            $field=array();
            while($fid_row=sqlFetchArray($fid))
            {
                $field[]=$fid_row['field_id'];
                //$pname=$fid_row['pname'];
            }
           // print_r($field);
            //print_r($field_id4);
            //echo $pname;
        if ($result = SignedButNoTransaction($resid_row['form_id'],$resid_row['pid'],$resid_row['encounter'])) {
              
             echo "<tr height='25'><td style='width:60px;' >";
             echo "<a href='f2f_form.php?form_id=".$result[0]['form_id']."&enc=".$result[0]['encounter']."&pname=".$result[0]['pname']."&pid=".$resid_row['pid']."&inmode1=create&form_name=Allcare Encounter Forms      
               'onclick='top.restoreSession()' class='css_button_small'><span>".
                                    htmlspecialchars( xl('Create'), ENT_NOQUOTES)."</span></a>";
                    echo "</td><td style='width:60px;'>";
                    print "<a href='f2f_encounter_form.php?formname=LBF2&form_id=".$result[0]['form_id']."&enc2=".$result[0]['encounter']."&pid=".$resid_row['pid']."&inmode=edit
                            'onclick='top.restoreSession()' class='css_button_small'><span>".
                            htmlspecialchars( xl('Edit'), ENT_NOQUOTES)."</span></a>";
                    echo "</td>";
                   echo "<td>".$result[0]['pname']."</td>";
                   echo "<td>".$result[0]['encounter']."</td>"; 
                   
                    foreach($field_id4 as $val){ 
                        echo" <td style='width:150px;'>";
                        for($i=0; $i<count($result); $i++){
                           if($val == $result[$i]['field_id'] ){ 
                                if($result[$i]['field_id']=='f2f_np')
                                    {   
                                    $res=sqlStatement("select concat(fname,lname) as name from users where id='".$result[$i]['field_value']."'");
                                    $row=sqlFetchArray($res);
                                     echo $data = $row['name'];
                                    }
                                    else if($result[$i]['field_id']=='f2f_ps'){
                                    $res=sqlStatement("select concat(fname,lname) as name1 from users where id='".$result[$i]['field_value']."'");
                                    $row=sqlFetchArray($res);
                                    echo $data = $row['name1'];
                                    }
                                  else {
                                      echo $data = $result[$i]['field_value'];
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

</div> <!-- end report_parameters -->
</div>

<!----------------- Finalized but not printed Ends ---------->
</form>


<form  method='post' name='theform_snf' id='theform_snf' action='f2f_encounters_report.php?showForm=snf'
       <?php if(!isset($_GET['showForm']) || ($_GET['showForm'])=='fnp') 
              {echo "style='display:none;'";}
              ?>>
    
<!----------- Submitted but not finalized Starts -------------->   
          
<div id="div_signed_not_finalized">
        
<div id="snf_report_parameters">
   <br>  <br>  <br>
   <form name="user_dropdown"  action="" method="POST">
                <table>
                    <tr>
                        <td>
                            <span class='bold'><?php echo htmlspecialchars( xl('Nursing Required'), ENT_NOQUOTES); ?>:</span>
                            <select id="nursing" name="nursing" onchange="javascript:dropdownchange();">
                                <option value="0">unassigned</option>
                                <option value="YES">YES</option>
                                <option value="NO">NO</option>
                            </select>
                        </td>
                    </tr>
                </table>
                  <br><br>
                  <input type="hidden" id="hdnnursing" name="hdnnursing" value="YES"/>
            </form> 
    <div id="uca"><div id="dvLoading" style="display:none"></div></div> 
   <div id="unassign" style="display:none">
<table class='display'  id='snf' border="1">

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
       </div>
</div> <!-- end report_parameters --> 
</div>

<!----------- Submitted but not finalized Ends ------------>      
</form>

</body>
