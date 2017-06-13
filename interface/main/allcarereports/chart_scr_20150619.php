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

require_once("../../globals.php");

?>
<html>
<head>

<?php html_header_show();?>
<link rel="stylesheet" href="<?php echo $css_header;?>" type="text/css">
<script type="text/javascript" src="../../../library/dialog.js"></script>
<script type="text/javascript" src="../../../library/textformat.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/jquery.js"></script>
<style>
    div.section {
 border: solid;
 border-width: 1px;
 border-color: #0000ff;
 margin: 0 0 0 10pt;
 padding: 5pt;
}

</style>
<script>
$(document).ready(function(){
 function displayVals() {
      var name1 = this.name;
      var val1 = this.value;
      var fields = name1.split(/_/);
      var grp = fields[2];
      var chart_grp = fields[3];
     //var listValue = $("select[name='form_s1_"+grp+"_"+ chart_grp+"']:selected").val();
       var listValue = $("#form_s1_"+grp+"_"+ chart_grp).val();
       //var pagebr=$("#form_pgb1_"+grp+"_"+ chart_grp).val();
           $('form select').each(
                    function(index){  
                        var input = $(this);
                      // alert( 'Name: ' + input.attr('name') + 'Value: ' + input.val());
                       var fdname =input.attr('name');
                       var fdname1 = fdname.split(/-/);
                       var title1=fdname1[0];
                       var grp1=fdname1[1];
                       var chart_grp1=fdname1[2];
                       if(listValue=='YES'){
                           jQuery("#"+title1+"-"+grp+"-"+chart_grp).find("option:contains('YES')").each(function()
                            { 
                                 if( jQuery(this).text() == 'YES' )
                                 { 
                                   jQuery(this).attr("selected","selected");
                                 }
                            });
                       }
                       else if(listValue=='NO'){
                           jQuery("#"+title1+"-"+grp+"-"+chart_grp).find("option:contains('NO')").each(function()
                            { 
                                 if( jQuery(this).text() == 'NO' )
                                 { 
                                   jQuery(this).attr("selected","selected");
                                 }
                            });
                       }
                       else if(listValue==''){
                           jQuery("#"+title1+"-"+grp+"-"+chart_grp).find("option:contains('Select')").each(function()
                            { 
                                 if( jQuery(this).text() == 'Select' )
                                 { 
                                   jQuery(this).attr("selected","selected");
                                 }
                            });
                        }
                      }
                   );
   }
 $( "select" ).change( displayVals );
displayVals();
 });
function divclick(cb, divid) {
     var divstyle = document.getElementById(divid).style;
     if (cb.checked) {
      divstyle.display = 'block';
     } else {
      divstyle.display = 'none';
     }
     return true;
}
function submitme() {
    var f = document.forms['chart_form'];
    f.submit();
}
</script>
</head>
<body class="body_top">                     
<form action='chart_scr.php' name='chart_form' method='post' onsubmit="">
    <a href="javascript:;"  <?php if (!$GLOBALS['concurrent_layout']) echo "target='Main'"; ?>   class="css_button" onclick="submitme();">
        <span><?php echo htmlspecialchars( xl('Save'), ENT_NOQUOTES); ?></span>
    </a>
    <a href="transactions.php"  <?php if (!$GLOBALS['concurrent_layout']) echo "target='Main'"; ?> class="css_button" >
         <span><?php echo htmlspecialchars( xl('Cancel'), ENT_NOQUOTES); ?></span>
    </a></span><br><br>
    
    <?php         
        $chart = sqlStatement("SELECT DISTINCT(group_name) FROM layout_options " .
                    "WHERE form_id = 'CHARTOUTPUT' AND uor > 0 AND field_id != '' " .
                    "ORDER BY group_name, seq");
        while ($crow = sqlFetchArray($chart)) {
            $chart_group[]=$crow['group_name'];   
        } 
        foreach ($chart_group as $cgroup){
            $display_style = 'block';
            echo "<span class='bold'><input type='checkbox' name='form_cb_$cgroup' id='form_cb_$cgroup' value='1' onclick='return divclick(this,\"div_$cgroup\");'";
            if ($display_style == 'block') echo " checked";
            echo " /><b>".htmlspecialchars( xl(substr( $cgroup, 1)), ENT_NOQUOTES)."</b></span>\n";
            echo "<div id='div_$cgroup'  class='section' style='display:$display_style;' >";

            $form=array('CHARTOUTPUT'=>'Default Medical Record Sections','DEM'=>'Demographics','HIS'=>'History','LBF2'=>'Allcare Encounter Forms');
            foreach($form as $key => $value){ 
                $group_name=array();
                $fres = sqlStatement("SELECT DISTINCT(group_name) FROM layout_options WHERE form_id = '$key' AND uor > 0 AND field_id != '' ORDER BY group_name, seq");
                while ($frow = sqlFetchArray($fres)) {
                  $group_name[]=$frow['group_name'];   
                } 
               $display_style = 'none';
                echo "<span class='bold'><input type='checkbox' name='form_cb_$key.$cgroup' id='form_cb_$key.$cgroup' value='1' onclick='return divclick(this,\"div_$key.$cgroup\");'";
                if ($display_style == 'block') echo " checked";
                echo " /><b>" . xl_layout_label($value) . "</b></span>\n"; 
                    
                if(substr($cgroup, 1)=='Home Health' || substr($cgroup, 1) == 'Payer Audit'){ 
                    $cgroup1=str_replace(' ', '-', $cgroup);
                    ?>
                    <select name="<?php echo "form_s1"."_".$key."_".$cgroup1 ; ?>" id="<?php echo "form_s1"."_".$key."_".$cgroup1; ?>" >
                        <!--<option value='' <?php if($selrow['option_value']=='') echo "selected"; ?> >Select</option>
                        <option  value ='YES' <?php if($selrow['option_value']=='YES') echo "selected"; ?> >YES</option>
                        <option   value='NO'  <?php if($selrow['option_value']=='NO') echo "selected"; ?>  >NO</option>-->
                        <option value="">Select</option>
                        <option value="YES">YES</option>
                        <option value="NO">NO</option>
                    </select>
                    <?php  
                }else {
                    ?>
                    <select name="<?php echo "form_s1"."_".$key."_".$cgroup  ; ?>" id="<?php echo "form_s1"."_".$key."_".$cgroup; ?>" >
                        <!--<option value='' <?php if($selrow['option_value']=='') echo "selected"; ?> >Select</option>
                        <option  value ='YES' <?php if($selrow['option_value']=='YES') echo "selected"; ?> >YES</option>
                        <option   value='NO'  <?php if($selrow['option_value']=='NO') echo "selected"; ?>  >NO</option>-->
                        <option value="">Select</option>
                        <option value="YES">YES</option>
                        <option value="NO">NO</option>
                      </select> 
                    <?php 
                }
                echo "<i style='font-size:14px'> (Select YES/NO for ALL)</i>";
                echo "<div id='div_$key.$cgroup'  style='display:$display_style;' >";
                foreach($group_name as $group){
                    if($group == $cgroup && $key=='CHARTOUTPUT'){
                        //echo "<b>".htmlspecialchars( xl(substr( $group, 1)), ENT_NOQUOTES)."</b></span>\n";
                        $fres1 = sqlStatement("SELECT * FROM layout_options WHERE form_id = '$key' AND uor > 0 AND field_id != ''  AND group_name='$group' ORDER BY seq");
                        echo "<div class='section'>";
                        echo "<table  cellspacing='0px' cellpadding='2px'>";
                        $field_id=array();
                        $title=array();
                        while($frowres = sqlFetchArray($fres1)){
                            $field_id  = $frowres['field_id'];
                            $title = $frowres['title'];
                            $formdata = sqlStatement("select * from tbl_chartui_mapping where form_id='$key' AND field_id = '$field_id' AND group_name = '$group' AND screen_name= '$cgroup'");
                            $fid_res = sqlFetchArray($formdata);
                            if(substr($cgroup, 1)=='Home Health' || substr($cgroup, 1) == 'Payer Audit') {
                                $cgroup1=str_replace(' ', '-', $cgroup);
                            if(empty($fid_res)):
                                $fins = sqlStatement("insert into tbl_chartui_mapping (form_id,field_id,group_name,option_value,screen_name,page_break) values('$key','$field_id','$group','".$_POST[$field_id."-".$key."-".$cgroup1]."','$cgroup','".$_POST["form_pgb1"."_".$field_id."_".$key."_".$cgroup1]."')");
                            elseif( !empty($fid_res) && $_REQUEST['status'] != 'noupdate'):
                                //echo "update tbl_chartui_mapping SET option_value='".$_POST[$field_id."-".$key."-".$cgroup1]."' , page_break='".$_POST["form_pgb1"."_".$field_id."_".$key."_".$cgroup1]."'  where form_id='$key' AND field_id='$field_id' AND group_name='$group' AND screen_name='$cgroup'";
                                $update=sqlStatement("update tbl_chartui_mapping SET option_value='".$_POST[$field_id."-".$key."-".$cgroup1]."' , page_break='".$_POST["form_pgb1"."_".$field_id."_".$key."_".$cgroup1]."'  where form_id='$key' AND field_id='$field_id' AND group_name='$group' AND screen_name='$cgroup'");
                            endif;
                            $sel=sqlStatement("select * from tbl_chartui_mapping where form_id='$key' AND group_name='$group' AND field_id='$field_id' AND screen_name= '$cgroup'"); 
                            $selrow = sqlFetchArray($sel);
                            if(!empty($selrow)){
                                echo "<tr>";  
                                if($title!='')
                                    echo "<td style='width:180px;'>".htmlspecialchars( xl($title), ENT_NOQUOTES)."</td><td style='width:180px;'>";
                                else 
                                    echo "<td style='width:180px;'>".htmlspecialchars( xl($field_id), ENT_NOQUOTES)."</td><td style='width:180px;'>";
                                if (strpos($field_id,'demographics') !== false) {
                                    
                                ?>
                                <select name="<?php echo $field_id."-".$key."-".$cgroup1 ; ?>" id="<?php echo $field_id."-".$key."-".$cgroup1; ?>" >
                                    <option  value ='YES' selected disabled>YES</option>
                                </select>
                            <?php }else{   ?> 

                                <select name="<?php echo $field_id."-".$key."-".$cgroup1 ; ?>" id="<?php echo $field_id."-".$key."-".$cgroup1; ?>" >
                                    <option value='' <?php if($selrow['option_value']=='') echo "selected"; ?> >Select</option>
                                    <option  value ='YES' <?php if($selrow['option_value']=='YES') echo "selected"; ?> >YES</option>
                                    <option   value='NO'  <?php if($selrow['option_value']=='NO') echo "selected"; ?>  >NO</option>
                                </select> 
                              
                                <?php
                            }
                                echo "</td>" ;
                                echo "</tr>\n";
                            }else{
                                echo "<tr>";  
                                echo "<td style='width:180px;'>".htmlspecialchars( xl($title), ENT_NOQUOTES)."</td><td style='width:180px;'>";
                                ?>
                                <select  name="<?php $field_id."-".$key."-".$cgroup1 ; ?>" id="<?php echo $field_id."-".$key."-".$cgroup1; ?>" >
                                    <option value="">Select</option>
                                    <option value="YES">YES</option>
                                    <option value="NO">NO</option>
                                </select> 
                                <?php
                                echo "</td>" ;
                                echo "</tr>\n";
                            }
                       }else{
                           if(empty($fid_res)):
                                $fins = sqlStatement("insert into tbl_chartui_mapping (form_id,field_id,group_name,option_value,screen_name,page_break) values('$key','$field_id','$group','".$_POST[$field_id."-".$key."-".$cgroup]."','$cgroup','".$_POST["form_pgb1"."_".$key."_".$cgroup]."')");
                            elseif( !empty($fid_res) && $_REQUEST['status'] != 'noupdate'):
                                //echo "update tbl_chartui_mapping SET option_value='".$_POST[$field_id."-".$key."-".$cgroup]."' , page_break='".$_POST["form_pgb1"."_".$key."_".$cgroup]."' where form_id='$key' AND field_id='$field_id' AND group_name='$group' AND screen_name='$cgroup'";
                                $update=sqlStatement("update tbl_chartui_mapping SET option_value='".$_POST[$field_id."-".$key."-".$cgroup]."' , page_break='".$_POST["form_pgb1"."_".$key."_".$cgroup]."' where form_id='$key' AND field_id='$field_id' AND group_name='$group' AND screen_name='$cgroup'");
                            endif;
                            $sel=sqlStatement("select * from tbl_chartui_mapping where form_id='$key' AND group_name='$group' AND field_id='$field_id' AND screen_name= '$cgroup'"); 
                            $selrow = sqlFetchArray($sel);
                            if(!empty($selrow)){
                                echo "<tr>";  
                                if($title!='')
                                    echo "<td style='width:180px;'>".htmlspecialchars( xl($title), ENT_NOQUOTES)."</td><td style='width:180px;'>";
                                else 
                                    echo "<td style='width:180px;'>".htmlspecialchars( xl($field_id), ENT_NOQUOTES)."</td><td style='width:180px;'>";
                                if (strpos($field_id,'demographics') !== false) {
                                    
                                ?>
                                <select name="<?php echo $field_id."-".$key."-".$cgroup ; ?>" id="<?php echo $field_id."-".$key."-".$cgroup; ?>" >
                                    <option  value ='YES' selected disabled>YES</option>
                                </select>
                            <?php }else{   ?> 
                                <select name="<?php echo $field_id."-".$key."-".$cgroup ; ?>" id="<?php echo $field_id."-".$key."-".$cgroup; ?>" >
                                    <option value='' <?php if($selrow['option_value']=='') echo "selected"; ?> >Select</option>
                                    <option  value ='YES' <?php if($selrow['option_value']=='YES') echo "selected"; ?> >YES</option>
                                    <option   value='NO'  <?php if($selrow['option_value']=='NO') echo "selected"; ?>  >NO</option>
                                </select> 

                                <?php
                                }
                                echo "</td>" ;
                                echo "</tr>\n";

                            }else{
                                echo "<tr>";  
                                echo "<td style='width:180px;'>".htmlspecialchars( xl($title), ENT_NOQUOTES)."</td><td style='width:180px;'>";
                                ?>
                                <select  name="<?php $field_id."-".$key."-".$cgroup ; ?>" id="<?php echo $field_id."-".$key."-".$cgroup; ?>" >
                                    <option value="">Select</option>
                                    <option value="YES">YES</option>
                                    <option value="NO">NO</option>
                                </select> 
                                <?php
                                echo "</td>" ;
                                echo "</tr>\n";
                            }
                       }
                        }
                    echo "</table>";
                    echo  "</div>\n";
                    echo "<br>";
                }else if($group!= $cgroup && $key!='CHARTOUTPUT'){
                    if(substr($group, 1)!='History Past' && substr($group, 1)!='Family History' && substr($group, 1)!='Family History Medical Conditi' && substr($group, 1)!='Family History Exam Test' && substr($group, 1)!='Codes' && substr($group, 1)!='History Social' ) {
                        echo "<br>";echo "<b>".htmlspecialchars( xl(substr( $group, 1)), ENT_NOQUOTES)."</b></span>&nbsp"; 
                            if(substr($cgroup, 1)=='Home Health' || substr($cgroup, 1) == 'Payer Audit') {
                                $cgroup1=str_replace(' ', '-', $cgroup);
                                $group1=str_replace(' ', '-', $group);
                                if($_REQUEST['status'] != 'noupdate'){
                                    //echo "update tbl_chartui_mapping SET   layout_col='".$_POST["form_lc1"."-".$group1."-".$key."-".$cgroup1]."' ,page_break='".$_POST["form_pgb1"."-".$group1."-".$key."-".$cgroup1]."'   where form_id='$key' AND group_name='$group' AND screen_name='$cgroup'";
                                    $update1=sqlStatement("update tbl_chartui_mapping SET layout_type = '".$_POST["form_ltype"."-".$group."-".$key."-".$cgroup1]."', layout_col='".$_POST["form_lc1"."-".$group1."-".$key."-".$cgroup1]."' ,page_break='".$_POST["form_pgb1"."-".$group1."-".$key."-".$cgroup1]."'   where form_id='$key' AND group_name='$group' AND screen_name='$cgroup'");
                                }
                                $lcsel2=sqlStatement("select * from tbl_chartui_mapping where form_id='$key' AND group_name='$group' AND screen_name= '$cgroup'"); 
                                $lcselrow2 = sqlFetchArray($lcsel2);?>
                                <?php 
                                if(in_array(substr($group1,1), array('Vitals', 'Medical-Problems', 'Allergies', 'Surgeries', 'Medication', 'Dental-Problems','DME','Prescription'))){
                                ?>    
                                <label>Layout Type:</label> 
                                <select name="<?php echo "form_ltype"."-".$group1."-".$key."-".$cgroup1; ?>" id="<?php echo "form_ltype"."-".$group1."-".$key."-".$cgroup1; ?>" >
                                    <option value='' <?php if($lcselrow2['layout_type']=='') echo "selected"; ?> >Select</option>
                                    <option  value ='list' <?php if($lcselrow2['layout_type']=='list') echo "selected"; ?> >List</option>
                                    <option   value='grid'  <?php if($lcselrow2['layout_type']=='grid') echo "selected"; ?>  >Grid</option>
                                </select> 
                                <i style='font-size:14px'> (Select Layout type List/Grid)</i>
                                <?php 
                                }
                                ?>
                                <label>Layout Column:</label> 
                                <select name="<?php echo "form_lc1"."-".$group1."-".$key."-".$cgroup1; ?>" id="<?php echo "form_lc1"."-".$group1."-".$key."-".$cgroup1; ?>" >
                                    <option value='1' <?php if($lcselrow2['layout_col']=='1') echo "selected"; ?> >1</option>
                                    <option  value ='2' <?php if($lcselrow2['layout_col']=='2') echo "selected"; ?> >2</option>
                                    <option   value='3'  <?php if($lcselrow2['layout_col']=='3') echo "selected"; ?>  >3</option>
                                </select> 
                                <i style='font-size:14px'> (Select display layout 1/2/3)</i>
                                <label>Page Break:</label> 
                                <select name="<?php echo "form_pgb1"."-".$group1."-".$key."-".$cgroup1; ?>" id="<?php echo "form_pgb1"."-".$group1."-".$key."-".$cgroup1; ?>" >
                                    <option value='' <?php if($lcselrow2['page_break']=='') echo "selected"; ?> >Select</option>
                                    <option  value ='YES' <?php if($lcselrow2['page_break']=='YES') echo "selected"; ?> >YES</option>
                                    <option   value='NO'  <?php if($lcselrow2['page_break']=='NO') echo "selected"; ?>  >NO</option>
                                </select> 
                                <i style='font-size:14px'> (Select page break YES/NO)</i>
                        <?php
                        }else{  
                            $group1=str_replace(' ', '-', $group);
                            if($_REQUEST['status'] != 'noupdate'){
                                //echo "update tbl_chartui_mapping SET layout_type = '".$_POST["form_ltype"."-".$group1."-".$key."-".$cgroup]."',layout_col='".$_POST["form_lc1"."-".$group1."-".$key."-".$cgroup]."' ,page_break='".$_POST["form_pgb1"."-".$group1."-".$key."-".$cgroup]."'  where form_id='$key' AND group_name='$group' AND screen_name='$cgroup'";
                                $update2=sqlStatement("update tbl_chartui_mapping SET layout_type = '".$_POST["form_ltype"."-".$group1."-".$key."-".$cgroup]."',layout_col='".$_POST["form_lc1"."-".$group1."-".$key."-".$cgroup]."' ,page_break='".$_POST["form_pgb1"."-".$group1."-".$key."-".$cgroup]."'  where form_id='$key' AND group_name='$group' AND screen_name='$cgroup'");
                            }
                            $lcsel=sqlStatement("select * from tbl_chartui_mapping where form_id='$key' AND group_name='$group' AND screen_name= '$cgroup'"); 
                            $lcselrow = sqlFetchArray($lcsel);?> 
                            <?php 
                            if(in_array(substr($group1,1), array('Vitals', 'Medical-Problems', 'Allergies', 'Surgeries', 'Medication', 'Dental-Problems','DME','Prescription', 'Immunization'))){
                            ?>    
                            <label>Layout Type:</label> 
                            <select name="<?php echo "form_ltype"."-".$group1."-".$key."-".$cgroup; ?>" id="<?php echo "form_ltype"."-".$group1."-".$key."-".$cgroup; ?>" >
                                <option value='' <?php if($lcselrow['layout_type']=='') echo "selected"; ?> >Select</option>
                                <option  value ='list' <?php if($lcselrow['layout_type']=='list') echo "selected"; ?> >List</option>
                                <option   value='grid'  <?php if($lcselrow['layout_type']=='grid') echo "selected"; ?>  >Grid</option>
                            </select> 
                            <i style='font-size:14px'> (Select Layout type List/Grid)</i>
                            <?php 
                            }
                            ?>
                            <label>Layout Column:</label> 
                            <select name="<?php echo "form_lc1"."-".$group1."-".$key."-".$cgroup; ?>" id="<?php echo "form_lc1"."-".$group1."-".$key."-".$cgroup; ?>" >
                                <option value='1' <?php if($lcselrow['layout_col']=='1') echo "selected"; ?> >1</option>
                                <option  value ='2' <?php if($lcselrow['layout_col']=='2') echo "selected"; ?> >2</option>
                                <option   value='3'  <?php if($lcselrow['layout_col']=='3') echo "selected"; ?>  >3</option>
                            </select>
                            <i style='font-size:14px'> (Select display layout 1/2/3)</i>
                            <label>Page Break:</label> 
                            <select name="<?php echo "form_pgb1"."-".$group1."-".$key."-".$cgroup; ?>" id="<?php echo "form_pgb1"."-".$group1."-".$key."-".$cgroup; ?>" >
                                <option value='' <?php if($lcselrow['page_break']=='') echo "selected"; ?> >Select</option>
                                <option  value ='YES' <?php if($lcselrow['page_break']=='YES') echo "selected"; ?> >YES</option>
                                <option   value='NO'  <?php if($lcselrow['page_break']=='NO') echo "selected"; ?>  >NO</option>
                            </select> 
                            <i style='font-size:14px'> (Select page break YES/NO)</i>
                            <?php 
                    }
                    $fres1 = sqlStatement("SELECT * FROM layout_options WHERE form_id = '$key' AND uor > 0 AND field_id != ''  AND group_name='$group' ORDER BY seq");
                    if(substr($group, 1)!='Vitals' && substr($group, 1)!='Review Of Systems' && substr($group, 1)!='Physical Exam' && substr($group, 1)!='Medical Problems' && substr($group, 1)!='Allergies' && substr($group, 1)!='Surgeries' && substr($group, 1)!='Medication'  && substr($group, 1)!='Dental Problems' && substr($group, 1)!='Immunization' && substr($group, 1)!='DME' && substr($group, 1)!='Procedure' && substr($group, 1)!='Prescription'  ){
                        echo "<div class='section' >";
                    }else{
                        echo "<div class='section' style='display:none;'>";
                    }
                    echo "<table  cellspacing='0px' cellpadding='2px'>";
                    $field_id=array();
                    $title=array();
                    while($frowres = sqlFetchArray($fres1)){
                            $field_id  = $frowres['field_id'];
                            $title = $frowres['title'];
                            $formdata = sqlStatement("select * from tbl_chartui_mapping where form_id='$key' AND field_id = '$field_id' AND group_name = '$group' AND screen_name= '$cgroup'");
                            $fid_res = sqlFetchArray($formdata);
                            if(substr($cgroup, 1)=='Home Health' || substr($cgroup, 1) == 'Payer Audit') {
                                    $cgroup1=str_replace(' ', '-', $cgroup);
                            if(empty($fid_res)):
                                $fins = sqlStatement("insert into tbl_chartui_mapping (form_id,field_id,group_name,option_value,screen_name,page_break,layout_col, layout_type) values('$key','$field_id','$group','".$_POST[$field_id."-".$key."-".$cgroup1]."','$cgroup','".$_POST["form_pgb1"."-".$field_id."-".$key."-".$cgroup1]."','".$_POST["form_lc1"."-".$group."-".$key."-".$cgroup1]."','".$_POST["form_ltype"."-".$group."-".$key."-".$cgroup1]."')");
                            elseif( !empty($fid_res) && $_REQUEST['status'] != 'noupdate'):
                                //echo "update tbl_chartui_mapping SET layout_type = '".$_POST["form_ltype"."-".$group."-".$key."-".$cgroup1]."',option_value='".$_POST[$field_id."-".$key."-".$cgroup1]."' ,page_break='".$_POST["form_pgb1"."-".$field_id."-".$key."-".$cgroup1]."' ,layout_col='".$_POST["form_lc1"."-".$group."-".$key."-".$cgroup1]."' , layout_type = '".$_POST["form_ltype"."-".$group."-".$key."-".$cgroup1]."' where form_id='$key' AND field_id='$field_id' AND group_name='$group' AND screen_name='$cgroup'";
                                $update=sqlStatement("update tbl_chartui_mapping SET option_value='".$_POST[$field_id."-".$key."-".$cgroup1]."'   where form_id='$key' AND field_id='$field_id' AND group_name='$group' AND screen_name='$cgroup'");
                            endif;
                            $sel=sqlStatement("select * from tbl_chartui_mapping where form_id='$key' AND group_name='$group' AND field_id='$field_id' AND screen_name= '$cgroup'"); 
                            $selrow = sqlFetchArray($sel);
                            if(!empty($selrow)){
                                echo "<tr>";  
                                if($title!='')
                                    echo "<td style='width:180px;'>".htmlspecialchars( xl($title), ENT_NOQUOTES)."</td><td style='width:180px;'>";
                                else 
                                    echo "<td style='width:180px;'>".htmlspecialchars( xl($field_id), ENT_NOQUOTES)."</td><td style='width:180px;'>";
                                ?>

                                <select name="<?php echo $field_id."-".$key."-".$cgroup1 ; ?>"  id="<?php echo $field_id."-".$key."-".$cgroup1; ?>" >
                                    <option value='' <?php if($selrow['option_value']=='') echo "selected"; ?> >Select</option>
                                    <option  value ='YES' <?php if($selrow['option_value']=='YES') echo "selected"; ?> >YES</option>
                                    <option   value='NO'  <?php if($selrow['option_value']=='NO') echo "selected"; ?>  >NO</option>
                                </select>  <?php echo '</td><td>'; ?> 
                                 <?php
                                echo "</td>" ;
                                echo "</tr>\n";

                            }else{
                                echo "<tr>";  
                                echo "<td style='width:180px;'>".htmlspecialchars( xl($title), ENT_NOQUOTES)."</td><td style='width:180px;'>";

                                ?>
                                <select  name="<?php echo $field_id."-".$key."-".$cgroup1 ; ?>" id="<?php echo $field_id."-".$key."-".$cgroup1; ?>" >
                                    <option value="">Select</option>
                                    <option value="yes">Yes</option>
                                    <option value="no">No</option>
                                </select>  <?php echo '</td><td>'; ?>
                                <?php
                                echo "</td>" ;
                                echo "</tr>\n";
                            }

                        }else {
                             if(empty($fid_res)):
                                $fins = sqlStatement("insert into tbl_chartui_mapping (form_id,field_id,group_name,option_value,screen_name,page_break,layout_col, layout_type) values('$key','$field_id','$group','".$_POST[$field_id."-".$key."-".$cgroup]."','$cgroup','".$_POST["form_pgb1"."-".$field_id."-".$key."-".$cgroup]."','".$_POST["form_lc1"."-".$group."-".$key."-".$cgroup]."','".$_POST["form_ltype"."-".$group."-".$key."-".$cgroup]."')");
                            elseif( !empty($fid_res) && $_REQUEST['status'] != 'noupdate'):
                                //echo "update tbl_chartui_mapping SET option_value='".$_POST[$field_id."-".$key."-".$cgroup]."' ,page_break='".$_POST["form_pgb1"."-".$field_id."-".$key."-".$cgroup]."' , layout_col='".$_POST["form_lc1"."-".$group."-".$key."-".$cgroup]."'  where form_id='$key' AND field_id='$field_id' AND group_name='$group' AND screen_name='$cgroup'";
                                $update=sqlStatement("update tbl_chartui_mapping SET option_value='".$_POST[$field_id."-".$key."-".$cgroup]."' where form_id='$key' AND field_id='$field_id' AND group_name='$group' AND screen_name='$cgroup'");
                            endif;
                            $sel=sqlStatement("select * from tbl_chartui_mapping where form_id='$key' AND group_name='$group' AND field_id='$field_id' AND screen_name= '$cgroup'"); 
                            $selrow = sqlFetchArray($sel);
                            if(!empty($selrow)){
                                echo "<tr>";  
                                if($title!='')
                                    echo "<td style='width:180px;'>".htmlspecialchars( xl($title), ENT_NOQUOTES)."</td><td style='width:180px;'>";
                                else 
                                    echo "<td style='width:180px;'>".htmlspecialchars( xl($field_id), ENT_NOQUOTES)."</td><td style='width:180px;'>";
                                ?>

                                <select name="<?php echo $field_id."-".$key."-".$cgroup; ?>"  id="<?php echo $field_id."-".$key."-".$cgroup; ?>" >
                                    <option value='' <?php if($selrow['option_value']=='') echo "selected"; ?> >Select</option>
                                    <option  value ='YES' <?php if($selrow['option_value']=='YES') echo "selected"; ?> >YES</option>
                                    <option   value='NO'  <?php if($selrow['option_value']=='NO') echo "selected"; ?>  >NO</option>
                                </select>  <?php echo '</td><td>'; ?>
                                <?php
                                echo "</td>" ;
                                echo "</tr>\n";

                            }else{
                                echo "<tr>";  
                                echo "<td style='width:180px;'>".htmlspecialchars( xl($title), ENT_NOQUOTES)."</td><td style='width:180px;'>";

                                ?>
                                <select  name="<?php echo $field_id."-".$key."-".$cgroup; ?>" id="<?php echo $field_id."-".$key."-".$cgroup; ?>" >
                                    <option value="">Select</option>
                                    <option value="yes">Yes</option>
                                    <option value="no">No</option>
                                </select>  <?php echo '</td><td>'; ?>
                                 <?php
                                echo "</td>" ;
                                echo "</tr>\n";
                            }
                        }
                    }
                    echo "</table>";
                    echo  "</div>\n";
                    //}
                    echo "<br>";

                    }
               }
            }echo   "</div>\n";
            echo "<br><br>";
        }
?>
<?php echo "</div>\n"; echo "<br><br>"; }?>  
 </form>                               
</body>
</html>
