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
// echo "SELECT  p.sex,lb.*, CONCAT(p.fname,' ',p.lname) AS pname,f.encounter,f.facility,f.pc_catid,f.date
//                        FROM lbf_data lb
//                        INNER JOIN layout_options l ON lb.field_id = l.field_id
//                        INNER JOIN patient_data p  ON p.pid=".$respid."
//                        INNER JOIN form_encounter f ON f.pid=p.pid 
//                        WHERE lb.form_id ='".$resid."' AND p.pid=".$respid." AND f.encounter=".$resenc." AND f.pid=$respid
//                        AND l.group_name LIKE '%Face to Face HH Plan%' AND (select count(*) from lbf_data where field_id='f2f_ps' AND form_id='".$resid."')>0
//                        AND (select count(*) from lbf_data where field_id='f2f_ps_on' AND field_value!='' AND form_id='".$resid."')>0
//                        AND (select count(*) from lbf_data where field_id='f2f_stat' AND field_value='finalized' AND form_id='".$resid."')=0
//                        AND (select count(*) from lbf_data where field_id='f2f_stat' AND field_value='pending' AND form_id='".$resid."')=0    
//                        ORDER BY seq";
       $r1=sqlStatement("SELECT  p.sex,lb.*, CONCAT(p.fname,' ',p.lname) AS pname,f.encounter,f.facility,f.pc_catid,f.date
                        FROM lbf_data lb
                        INNER JOIN layout_options l ON lb.field_id = l.field_id
                        INNER JOIN patient_data p  ON p.pid=".$respid."
                        INNER JOIN form_encounter f ON f.pid=p.pid 
                        WHERE lb.form_id ='".$resid."' AND p.pid=".$respid." AND f.encounter=".$resenc." AND f.pid=$respid
                        AND l.group_name LIKE '%Face to Face HH Plan%' AND (select count(*) from lbf_data where field_id='f2f_ps' AND form_id='".$resid."')>0
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
        #dvLoading1
    {
        background: url(../pic/ajax-loader-large.gif) no-repeat center center;
        height: 100px;
        width: 500px;
        position: fixed;
        z-index: 1000;
        left: 0%;
        top: 50%;
        margin: -25px 0 0 -25px;
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
   
    
    
    function dropdownchange(){
                if($( "#nursing option:selected" ).val() == '0'){
//                     $('#uca').show(); 
//                     $('#unassign').hide(); 
                }
               else if($( "#nursing option:selected" ).val() !== 0){
                    var nid = $( "#nursing option:selected" ).val();
                    
//                   // $('#unassign').hide();
//                    $('#uca').show(); 
//                    $('#dvLoading').show();
//                    $("#uca").load("f2f_filter.php?nid="+nid,function(){ 
//                    $('#dvLoading').hide();
//                  });
               }
            
               // deceased_stat
               if($( "#deceased_stat option:selected" ).val() == '0'){
//                     $('#uca').show(); 
//                     $('#unassign').hide(); 
                }
               else if($( "#deceased_stat option:selected" ).val() !== 0){
                    var deseased_val = $( "#deceased_stat option:selected" ).val();
               }
               
               if($( "#practice_stat option:selected" ).val() == '0'){
//                     $('#uca').show(); 
//                     $('#unassign').hide(); 
                }
               else if($( "#practice_stat option:selected" ).val() !== 0){
                    var practice_val = $( "#practice_stat option:selected" ).val();
               }
               
               if($( "#cpo option:selected" ).val() == '0'){
//                     $('#uca').show(); 
//                     $('#unassign').hide(); 
                }
               else if($( "#cpo option:selected" ).val() !== 0){
                    var cpo_val = $( "#cpo option:selected" ).val();
               }
               
               // $('#unassign').hide();
                    $('#uca').show(); 
                    $('#dvLoading').show();
                    $("#uca").load("f2f_filter.php?nid="+nid + "&deseased_val=" + deseased_val + "&practice_val=" + practice_val + "&cpo_val=" + cpo_val,function(){ 
                    $('#dvLoading').hide();
                  });
            
         }
         
         function dropdownchange1(){
//                if($( "#nursing_f2f option:selected" ).val() == '0'){
////                     $('#uca').show(); 
////                     $('#unassign').hide(); 
//                }
//               else if($( "#nursing_f2f option:selected" ).val() !== 0){
//                    var nid1 = $( "#nursing_f2f option:selected" ).val();
//                    
////                   // $('#unassign').hide();
////                    $('#uca').show(); 
////                    $('#dvLoading').show();
////                    $("#uca").load("f2f_filter.php?nid="+nid,function(){ 
////                    $('#dvLoading').hide();
////                  });
//               }
            
               // deceased_stat
               if($( "#deceased_stat_f2f option:selected" ).val() == '0'){
//                     $('#uca').show(); 
//                     $('#unassign').hide(); 
                }
               else if($( "#deceased_stat_f2f option:selected" ).val() !== 0){
                    var deseased_val1 = $( "#deceased_stat_f2f option:selected" ).val();
               }
               
               if($( "#practice_stat_f2f option:selected" ).val() == '0'){
//                     $('#uca').show(); 
//                     $('#unassign').hide(); 
                }
               else if($( "#practice_stat_f2f option:selected" ).val() !== 0){
                    var practice_val1 = $( "#practice_stat_f2f option:selected" ).val();
               }
               
//               if($( "#cpo_f2f option:selected" ).val() == '0'){
////                     $('#uca').show(); 
////                     $('#unassign').hide(); 
//                }
//               else if($( "#cpo_f2f option:selected" ).val() !== 0){
//                    var cpo_val1 = $( "#cpo_f2f option:selected" ).val();
//               }
               
               // $('#unassign').hide();
                    $('#div_noF2F').show(); 
                    $('#dvLoading1').show();
                    $("#div_noF2F").load("vnf2f_reports.php?&deseased_val1=" + deseased_val1 + "&practice_val1=" + practice_val1 ,function(){ 
                    $('#dvLoading1').hide();
                  });
            
         }
         
         

</script>
</head>
 <body class="body_top" style="background-color:#FFFFCC;" >
     
     <script type='text/javascript'>
            
            $(document).ready( function () {
            function auto_submit(){
                var nid =jQuery('#nursing').val();
                var deseased_val =jQuery('#deceased_stat').val();
                var  practice_val =jQuery('#practice_stat').val();
                var  cpo_val =jQuery('#cpo').val();
                
                $('#dvLoading').show();
                    $("#uca").load("f2f_filter.php?nid="+nid + "&deseased_val=" +deseased_val + "&practice_val=" +practice_val + "&cpo_val=" +cpo_val,function(){ 
                    $('#dvLoading').hide();
                 });
            }
               auto_submit();
//                  var nid,deseased_val,practice_val ,cpo_val;
//                   var nid1,deseased_val1,practice_val1 ,cpo_val1;
                $('#snt').DataTable( {  "iDisplayLength": 100
                } );
                $( window ).load(function() {
                if (window.location.href.indexOf('reload')==-1) {
                     window.location.replace(window.location.href+'?reload');
                }
            });
//            jQuery("#nursing").find("option:contains('YES')").each(function()
//                { 
//                 if( jQuery(this).text() == 'YES' )
//                 { 
//                  jQuery(this).attr("selected","selected");
//                   //$('#unassign').show();
//                    nid ='YES';
//                    
////                    //$('#unassign').hide();
////                    $('#uca').show(); 
////                    $('#dvLoading').show();
////                    $("#uca").load("f2f_filter.php?nid="+nid,function(){ 
////                    $('#dvLoading').hide();
////                    });
//                   
//                  }
//                });
//                
//               // deceased status
//                jQuery("#deceased_stat").find("option:contains('ALL')").each(function()
//                { 
//                     if( jQuery(this).text() == 'ALL' )
//                     { 
//                       jQuery(this).attr("selected","selected");
//                       //$('#unassign').show();
//                       deseased_val ='ALL';
//                     }
//                });
//                
//                //practice_status
//                 jQuery("#practice_stat").find("option:contains('ALL')").each(function()
//                { 
//                     if( jQuery(this).text() == 'ALL' )
//                     { 
//                          jQuery(this).attr("selected","selected");
//                           //$('#unassign').show();
//                            practice_val ='ALL';
//                     }
//                });
//                //cpo
//                 jQuery("#cpo").find("option:contains('ALL')").each(function()
//                { 
//                     if( jQuery(this).text() == 'ALL' )
//                     { 
//                          jQuery(this).attr("selected","selected");
//                           //$('#unassign').show();
//                            cpo_val ='ALL';
//                     }
//                });
                
                
//                    $('#dvLoading').show();
//                    $("#uca").load("f2f_filter.php?nid="+nid + "&deseased_val=" +deseased_val + "&practice_val=" +practice_val + "&cpo_val=" +cpo_val,function(){ 
//                    $('#dvLoading').hide();
//                    });
                    
                    
               
//                $('#snf').DataTable( {
//                } );
                //For VISITS WITH NO F2F
//                 jQuery("#nursing_f2f").find("option:contains('YES')").each(function()
//                { 
//                 if( jQuery(this).text() == 'YES' )
//                 { 
//                  jQuery(this).attr("selected","selected");
//                   //$('#unassign').show();
//                    nid1 ='YES';
//                    
////                    //$('#unassign').hide();
////                    $('#uca').show(); 
////                    $('#dvLoading').show();
////                    $("#uca").load("f2f_filter.php?nid="+nid,function(){ 
////                    $('#dvLoading').hide();
////                    });
//                   
//                  }
//                });
                
               // deceased status
                jQuery("#deceased_stat_f2f").find("option:contains('ALL')").each(function()
                { 
                     if( jQuery(this).text() == 'ALL' )
                     { 
                       jQuery(this).attr("selected","selected");
                       //$('#unassign').show();
                       deseased_val1 ='ALL';
                     }
                });
                
                //practice_status
                 jQuery("#practice_stat_f2f").find("option:contains('ALL')").each(function()
                { 
                     if( jQuery(this).text() == 'ALL' )
                     { 
                          jQuery(this).attr("selected","selected");
                           //$('#unassign').show();
                            practice_val1 ='ALL';
                     }
                });
                //cpo
//                 jQuery("#cpo_f2f").find("option:contains('ALL')").each(function()
//                { 
//                     if( jQuery(this).text() == 'ALL' )
//                     { 
//                          jQuery(this).attr("selected","selected");
//                           //$('#unassign').show();
//                            cpo_val1 ='ALL';
//                     }
//                });
                
                    $('#div_noF2F').show(); 
                    $('#dvLoading1').show();
                    $("#div_noF2F").load("vnf2f_reports.php?&deseased_val1=" +deseased_val1 + "&practice_val1=" +practice_val1  ,function(){ 
                    $('#dvLoading1').hide();
                    });
//                $('#snf').DataTable( {
//                } );

//                    $('#div_noform').show(); 
//                    $('#dvLoading1').show();
//                    $("#div_noform").load("vnencforms_reports.php" ,function(){ 
//                    $('#dvLoading1').hide();
//                  });
            } );
             function submit_form(){
                
                 $("form").submit(function(e) {
//                     alert("submitted");
                        e.preventDefault();
                        var nid=jQuery('#nursing').val(); 
                        var deseased_val=jQuery('#deceased_stat').val();
                        var practice_val=jQuery('#practice_stat').val();
                        var cpo_val=jQuery('#cpo').val();
//                    alert(nid+"=="+deseased_val+"=="+practice_val+"=="+cpo_val);
           jQuery('#uca').html('loading...');

            jQuery.ajax({
                    type: 'POST',
                    url: "f2f_filter.php",	
                    data: {
                            nid:nid,                        
                            deseased_val:deseased_val,
                            practice_val:practice_val,
                            cpo_val:cpo_val
                            
                        },

                    success: function(response)
                    {
                        //alert(response);
                        $('#uca').show(); 
//                          jQuery('#dvLoading').show();
                          jQuery('#uca').html(response);
//                        jQuery('#dvLoading').hide();
                    },
                    failure: function(response)
                    {
                            alert("error");
                    }		
            });
        });
                   
    }
    </script>
<!-- Required for the popup date selectors -->
<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>

<span class='title'><?php xl('Report','e'); ?> - <?php xl('FacetoFace Encounters','e'); ?></span>

<a href="javascript:;" class="link1" onclick="window.open( 'https://<?php echo $_SERVER[HTTP_HOST]; ?>/interface/reports/details.php', '', 'width=500, height=600')"> [Details] </a>
<ul class="tabNav">

<li class='current'>
<a id="tab_fnp" onclick="javascript:$('#theform_snf').hide();
                       $('#user_dropdown').hide();
                       $('#theform_vnf2f').hide();
                       $('#theform_fnp').show();
                       $('#uca').hide();
                       $('.tabNav > li').removeClass('current');
                       $(this).parent('li').addClass('current');" style='cursor:pointer'>Signed But No Transaction</a>
</li>
<li>
<a id="tab_snf" onclick="javascript:$('#theform_fnp').hide();
                       $('#theform_vnf2f').hide();
                       $('#theform_snf').show();
                       $('.tabNav > li').removeClass('current');
                       $(this).parent('li').addClass('current');" style='cursor:pointer'>Submitted But Not Signed</a>
</li>
<li>
<a id="tab_vnf2f" onclick="javascript:$('#theform_fnp').hide();
                       $('#theform_snf').hide();
                       $('#theform_vnf2f').show();
                       $('.tabNav > li').removeClass('current');
                       $(this).parent('li').addClass('current');" style='cursor:pointer'>Visits with No F2F</a>
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
      <?php if(($_GET['showForm'])=='snf' && ($_GET['showForm'])=='vnf2f') 
              {echo "style='display:none;'";}
              ?>>
         
<!--------------- Finalized But not printed Starts -------->
    
<div id="div_signed_but_no transaction">
        
<div id="fnp_report_parameters">
<script type='text/javascript'>    
 $(document).ready(function() 
    { 
        $('#snt').tablesorter({ headers: { 0: { sorter: false}, 1: {sorter: false},2: {sorter: false} } });
    } 
);   
</script>    
<table class='display'  id='snt' border="1">

<?php

 /*$field_id=array();*/
 $title1=array();
 $fres = sqlStatement("SELECT * FROM layout_options " .
    "WHERE form_id = 'LBF2' AND uor > 0 AND field_id != '' AND group_name LIKE '%Face to Face HH Plan%'" .
    "ORDER BY  seq");
  while ($frow = sqlFetchArray($fres)) {
    $title1[] = $frow['title'];
    $field_id4[]=$frow['field_id'];
      
  }
  
  
	 print "<thead><tr class='showborder_head'><th style='width=10px;'>&nbsp;</th><th style='width=10px;'>&nbsp;</th>"
               . "<th style='width:180px;'>".htmlspecialchars( xl('Patient Name'), ENT_NOQUOTES)."</th>"."<th style='width:180px;'>".htmlspecialchars( xl('Gender'), ENT_NOQUOTES)."</th>".
                 "<th style='width:180px;'>".htmlspecialchars( xl('Encounter'), ENT_NOQUOTES)."</th>"."<th style='width:180px;'>".htmlspecialchars( xl('Date_of_service'), ENT_NOQUOTES)."</th>".
                 "<th style='width:180px;'>".htmlspecialchars( xl('Visit_Category'), ENT_NOQUOTES)."</th>"."<th style='width:180px;'>".htmlspecialchars( xl('Facility'), ENT_NOQUOTES)."</th>";
        
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
//             echo "<pre>"; print_r($result); echo "</pre>";  
             echo "<tr height='25'><td style='width:60px;' >";
             echo "<a href='f2f_form.php?form_id=".$result[0]['form_id']."&enc=".$result[0]['encounter']."&pname=".$result[0]['pname']."&pid=".$resid_row['pid']."&inmode1=create&form_name=Allcare Encounter Forms      
               'onclick='top.restoreSession()' class='css_button_small'><span>".
                                    htmlspecialchars( xl('Create'), ENT_NOQUOTES)."</span></a>";
                    echo "</td><td style='width:60px;'>";
                    print "<a href='f2f_encounter_form.php?formname=LBF2&form_id=".$result[0]['form_id']."&enc2=".$result[0]['encounter']."&pid=".$resid_row['pid']."&inmode=edit&tab=SignedButNoTransaction
                            'onclick='top.restoreSession()' class='css_button_small'><span>".
                            htmlspecialchars( xl('Edit'), ENT_NOQUOTES)."</span></a>";
                    echo "</td>";
                   echo "<td>".$result[0]['pname']."</td>";
                   echo "<td>".$result[0]['sex']."</td>";
                   echo "<td>".$result[0]['encounter']."</td>"; 
                   echo "<td>"; $date=explode(" ",$result[0]['date']); echo $date[0]; echo"</td>";
                   echo "<td>"; $catid=$result[0]['pc_catid']; $sql=sqlStatement("SELECT * FROM  `openemr_postcalendar_categories` where pc_catid=$catid"); $row=sqlFetchArray($sql); echo $row['pc_catname']; echo"</td>";
                   echo "<td>".$result[0]['facility']."</td>";
                     
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

</div>  
<!--    end report_parameters -->
</div>

<!--------------- Finalized but not printed Ends -------->
</form>


<form  method='post' name='theform_snf' id='theform_snf' action='f2f_encounters_report.php?showForm=snf'
       <?php if(!isset($_GET['showForm']) || ($_GET['showForm'])=='fnp' || ($_GET['showForm'])=='vnf2f')  
              {echo "style='display:none;'";}
              ?>>
    
<!----------- Submitted but not finalized Starts -------------->   
          
<div id="div_signed_not_finalized">
        
<div id="snf_report_parameters">
   <br>  <br>  <br>
   <form name="user_dropdown" id="user_dropdown" action="" method="POST">
                <table>
                    <tr>
                        <td>
                            <span class='bold'><?php echo htmlspecialchars( xl('Nursing Required'), ENT_NOQUOTES); ?>:</span>
                            <select id="nursing" name="nursing" id="nursing">
                                <option value="ALL">ALL</option>
                                <option value="YES" selected>YES</option>
                                <option value="NO">NO</option>
                            </select>
                        </td>
                        <td>
                            <span class='bold'><?php echo htmlspecialchars( xl('Deceased Status'), ENT_NOQUOTES); ?>:</span>
                            <select id="deceased_stat" name="deceased_stat" id="deceased_stat">
                                <option value="ALL" selected>ALL</option>
                                <option value="YES">YES</option>
                                <option value="NO">NO</option>
                            </select>
                        </td>
                        <td>
                            <span class='bold'><?php echo htmlspecialchars( xl('Practice Status'), ENT_NOQUOTES); ?>:</span>
                            <select id="practice_stat" name="practice_stat" id="practice_stat">
                                <option value="ALL" selected>ALL</option>
                                <option value="YES">YES</option>
                                <option value="NO">NO</option>
                            </select>
                        </td>
                        <td>
                            <span class='bold'><?php echo htmlspecialchars( xl('CPO'), ENT_NOQUOTES); ?>:</span>
                            <select id="cpo" name="cpo" id="cpo">
                                <option value="ALL" selected>ALL</option>
                                <option value="YES">YES</option>
                                <option value="NO">NO</option>
                            </select>
                        </td>
                        <td> 
                            <input type="submit" class="btn btn-default" value="submit" onclick="submit_form()"/>
<!--                            <input type="submit" class="btn btn-default" value="submit" />-->
                        </td>
                    </tr>
                </table>
                  <br><br>
                  <input type="hidden" id="hdnnursing" name="hdnnursing" value="YES"/>
                  <input type="hidden" id="hdndeceased" name="hdndeceased" value="ALL"/>
                  <input type="hidden" id="hdnpractice" name="hdnpractice" value="ALL"/>
                  <input type="hidden" id="hdncpo" name="hdncpo" value="ALL"/>
            </form> 
    <div id="uca"><div id="dvLoading" style="display:none"></div></div> 
<!--     <div id="uca"></div> -->
  

</div> <!-- end report_parameters --> 
</div>

<!----------- Submitted but not finalized Ends ------------>      
</form>

<!--Visits with No F2F-->
<form  method='post' name='theform_vnf2f' id='theform_vnf2f' action='f2f_encounters_report.php?showForm=vnf2f'
       <?php if(!isset($_GET['showForm']) || ($_GET['showForm'])=='fnp' || ($_GET['showForm'])=='snf') 
              {echo "style='display:none;'";}
              ?>>
    
<!--------- Submitted but not finalized Starts ------------>
          
<div id="div_visits_with_NOf2f">
        
<div id="vnf2f_report_parameters">
   <br>  <br>  <br>
   <form name="dropdown_filters"  action="" method="POST">
                <table>
                    <tr>
<!--                        <td>
                            <span class='bold'><?php echo htmlspecialchars( xl('Nursing Required'), ENT_NOQUOTES); ?>:</span>
                            <select id="nursing_f2f" name="nursing_f2f" onchange="javascript:dropdownchange1();">
                                <option value="ALL">ALL</option>
                                <option value="YES">YES</option>
                                <option value="NO">NO</option>
                            </select>
                        </td>-->
                        <td>
                            <span class='bold'><?php echo htmlspecialchars( xl('Deceased Status'), ENT_NOQUOTES); ?>:</span>
                            <select id="deceased_stat_f2f" name="deceased_stat_f2f" onchange="javascript:dropdownchange1();">
                                <option value="ALL">ALL</option>
                                <option value="YES">YES</option>
                                <option value="NO">NO</option>
                            </select>
                        </td>
                        <td>
                            <span class='bold'><?php echo htmlspecialchars( xl('Practice Status'), ENT_NOQUOTES); ?>:</span>
                            <select id="practice_stat_f2f" name="practice_stat_f2f" onchange="javascript:dropdownchange1();">
                                <option value="ALL">ALL</option>
                                <option value="YES">YES</option>
                                <option value="NO">NO</option>
                            </select>
                        </td>
<!--                        <td>
                            <span class='bold'><?php echo htmlspecialchars( xl('CPO'), ENT_NOQUOTES); ?>:</span>
                            <select id="cpo_f2f" name="cpo_f2f" onchange="javascript:dropdownchange1();">
                                <option value="ALL">ALL</option>
                                <option value="YES">YES</option>
                                <option value="NO">NO</option>
                            </select>
                        </td>-->
                    </tr>
                </table>
                  <br><br>
                  <input type="hidden" id="hdnnursing1" name="hdnnursing1" value="YES"/>
                  <input type="hidden" id="hdndeceased1" name="hdndeceased1" value="ALL"/>
                  <input type="hidden" id="hdnpractice1" name="hdnpractice1" value="ALL"/>
                  <input type="hidden" id="hdncpo1" name="hdncpo1" value="ALL"/>
            </form> 
    <div id="div_noF2F"><div id="dvLoading1" style="display:none"></div></div> 
  
</div>  
<!--    end report_parameters  -->
</div>
</form>
</body>
