<?php
// +-----------------------------------------------------------------------------+
// Copyright (C) 2012 NP Clinics <info@npclinics.com.au>
//
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
//
// A copy of the GNU General Public License is included along with this program:
// openemr/interface/login/GnuGPL.html
// For more information write to the Free Software
// Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
//
// Author:   Scott Wakefield <scott@npclinics.com.au>
//
// +------------------------------------------------------------------------------+

//SANITIZE ALL ESCAPES
$sanitize_all_escapes=true;


//STOP FAKE REGISTER GLOBALS
$fake_register_globals=false;


require_once("../globals.php");
require_once("$srcdir/sql.inc");
require_once("$srcdir/formdata.inc.php");
require_once("$srcdir/options.inc.php");
require_once("$srcdir/acl.inc");

// Ensure authorized
if (!acl_check('admin', 'users')) {
  die(xlt("Unauthorized"));
}
?>
<html>
<head>
<link rel="stylesheet" href="<?php echo $css_header;?>" type="text/css">
<link rel="stylesheet" href="<?php echo $GLOBALS['webroot'] ?>/library/bootstrap/docs/css/bootstrap-3.2.0.min.css" type="text/css">
<link rel="stylesheet" href="<?php echo $GLOBALS['webroot'] ?>/library/bootstrap/docs/css/bootstrap-example.css" type="text/css">


<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/bootstrap/docs/js/jquery-2.1.1.min.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/bootstrap/docs/js/bootstrap-3.2.0.min.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/bootstrap/docs/js/prettify.js"></script>

<link rel="stylesheet" href="<?php echo $GLOBALS['webroot'] ?>/library/bootstrap/dist/css/bootstrap-multiselect.css" type="text/css">
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/bootstrap/dist/js/bootstrap-multiselect.js"></script>

<style>
    .inputtext-control{width:"20px;"}
    #visitcat_codegrp{background-color: #fe5301;width: 1100px;height: 600px;overflow: scroll;}
</style>
<script type="text/javascript">
    $(document).ready(function() {
        
              $("select#codegroups-post").on('change', function () {
               if ($(this).val() == "1Out Patient New Patient") {
                $("#codeoption_1Out_Patient_New_Patient").show();
                $("#codeoption_2Out_Patient_Established_Patient").hide();
                $("#codeoption_3CPO").hide();
                $("#codeoption_4Tests\\/Exams").hide();
                $("#codeoption_5AWV").hide();                
                $("#codeoption_6CCM").hide();
                $("#codeoption_7H\\&P").hide();                
                $("#codeoption_8IPPE").hide();
                
               } else if ($(this).val() == "2Out Patient Established Patient") {
                $("#codeoption_1Out_Patient_New_Patient").hide();
                $("#codeoption_2Out_Patient_Established_Patient").show();
                $("#codeoption_3CPO").hide();
                $("#codeoption_4Tests\\/Exams").hide();
                $("#codeoption_5AWV").hide();                
                $("#codeoption_6CCM").hide();
                $("#codeoption_7H\\&P").hide();                
                $("#codeoption_8IPPE").hide();
               } else if ($(this).val() == "3CPO") {
                 $("#codeoption_1Out_Patient_New_Patient").hide();
                $("#codeoption_2Out_Patient_Established_Patient").hide();
                $("#codeoption_3CPO").show();
                $("#codeoption_4Tests\\/Exams").hide();
                $("#codeoption_5AWV").hide();                
                $("#codeoption_6CCM").hide();
                $("#codeoption_7H\\&P").hide();                
                $("#codeoption_8IPPE").hide();
               }
               else if ($(this).val() == "4Tests/Exams") {
                 $("#codeoption_1Out_Patient_New_Patient").hide();
                $("#codeoption_2Out_Patient_Established_Patient").hide();
                $("#codeoption_3CPO").hide();
                $("#codeoption_4Tests\\/Exams").show();
                $("#codeoption_5AWV").hide();                
                $("#codeoption_6CCM").hide();
                $("#codeoption_7H\\&P").hide();                
                $("#codeoption_8IPPE").hide();
               }
                else if ($(this).val() == "5AWV") {
                 $("#codeoption_1Out_Patient_New_Patient").hide();
                $("#codeoption_2Out_Patient_Established_Patient").hide();
                $("#codeoption_3CPO").hide();
                $("#codeoption_4Tests\\/Exams").hide();
                $("#codeoption_5AWV").show();                
                $("#codeoption_6CCM").hide();
                $("#codeoption_7H\\&P").hide();                
                $("#codeoption_8IPPE").hide();
               }
               else if ($(this).val() == "6CCM") {
                 $("#codeoption_1Out_Patient_New_Patient").hide();
                $("#codeoption_2Out_Patient_Established_Patient").hide();
                $("#codeoption_3CPO").hide();
                $("#codeoption_4Tests\\/Exams").hide();
                $("#codeoption_5AWV").hide();                
                $("#codeoption_6CCM").show();
                $("#codeoption_7H\\&P").hide();                
                $("#codeoption_8IPPE").hide();
               }
               else if ($(this).val() == "7H&P") {
                 $("#codeoption_1Out_Patient_New_Patient").hide();
                $("#codeoption_2Out_Patient_Established_Patient").hide();
                $("#codeoption_3CPO").hide();
                $("#codeoption_4Tests\\/Exams").hide();
                $("#codeoption_5AWV").hide();                
                $("#codeoption_6CCM").hide();
                $("#codeoption_7H\\&P").show();                
                $("#codeoption_8IPPE").hide();
               }
               else if ($(this).val() == "8IPPE") {
                 $("#codeoption_1Out_Patient_New_Patient").hide();
                $("#codeoption_2Out_Patient_Established_Patient").hide();
                $("#codeoption_3CPO").hide();
                $("#codeoption_4Tests\\/Exams").hide();
                $("#codeoption_5AWV").hide();                
                $("#codeoption_6CCM").hide();
                $("#codeoption_7H\\&P").hide();                
                $("#codeoption_8IPPE").show();
               }
            });
     
        
        $('#facilities-post').multiselect({
            includeSelectAllOption: true,
            enableFiltering: true
        });
        $('#visitcat-post').multiselect({
            includeSelectAllOption: true,
            enableFiltering: true
        });
        $('#codegroups-post').multiselect({
            includeSelectAllOption: true,
            enableFiltering: true
        });
        $('#codegroupsopt-post1').multiselect({
            includeSelectAllOption: true,
            enableFiltering: true
        });
        $('#codegroupsopt-post2').multiselect({
            includeSelectAllOption: true,
            enableFiltering: true
        });
        $('#codegroupsopt-post3').multiselect({
            includeSelectAllOption: true,
            enableFiltering: true
        });
        $('#codegroupsopt-post4').multiselect({
            includeSelectAllOption: true,
            enableFiltering: true
        });
        $('#codegroupsopt-post5').multiselect({
            includeSelectAllOption: true,
            enableFiltering: true
        });
        $('#codegroupsopt-post6').multiselect({
            includeSelectAllOption: true,
            enableFiltering: true
        });
        $('#codegroupsopt-post7').multiselect({
            includeSelectAllOption: true,
            enableFiltering: true
        });
        $('#codegroupsopt-post8').multiselect({
            includeSelectAllOption: true,
            enableFiltering: true
        });
        $('#codegroupsopt-post9').multiselect({
            includeSelectAllOption: true,
            enableFiltering: true
        });
        $('#codegroupsopt-post10').multiselect({
            includeSelectAllOption: true,
            enableFiltering: true
        });
        $('#codegroupsopt-post11').multiselect({
            includeSelectAllOption: true,
            enableFiltering: true
        });
        
        $('#codegroupsopt-post12').multiselect({
            includeSelectAllOption: true,
            enableFiltering: true
        });
        $('#codegroupsopt-post13').multiselect({
            includeSelectAllOption: true,
            enableFiltering: true
        });
        $('#codegroupsopt-post14').multiselect({
            includeSelectAllOption: true,
            enableFiltering: true
        });
        
        $("#form-horizontal").submit(function(e) {
            e.preventDefault();
            
            if(jQuery('#facilities-post').val() == null || 
               jQuery('#visitcat-post').val() == null ||
               jQuery('#codegroups-post').val() == null 
              ){
                      alert("All fields are required");
                      return false;
              }
            
            var facilities=jQuery('#facilities-post').val(); 
            var visitcat=jQuery('#visitcat-post').val();
            var codegroups=jQuery('#codegroups-post').val();
            var codegroupsopt=jQuery('#codegroupsopt-post1').val();
            var codegroupsopt2=jQuery('#codegroupsopt-post2').val();
            var codegroupsopt3=jQuery('#codegroupsopt-post3').val();
            var codegroupsopt4=jQuery('#codegroupsopt-post4').val();
            var codegroupsopt5=jQuery('#codegroupsopt-post5').val();
            
            var codegroupsopt6=jQuery('#codegroupsopt-post6').val();
            var codegroupsopt7=jQuery('#codegroupsopt-post7').val();
            var codegroupsopt8=jQuery('#codegroupsopt-post8').val();
            var codegroupsopt9=jQuery('#codegroupsopt-post9').val();
            var codegroupsopt10=jQuery('#codegroupsopt-post10').val();
            var codegroupsopt11=jQuery('#codegroupsopt-post11').val();
            var codegroupsopt12=jQuery('#codegroupsopt-post12').val();
            var codegroupsopt13=jQuery('#codegroupsopt-post13').val();
            var codegroupsopt14=jQuery('#codegroupsopt-post14').val();
            
            var fuvid=jQuery('#fuvid').val();
          
            jQuery.ajax({
                    type: 'POST',
                    url:  "visitcat_codegrp_update.php",	
                    data: {
                            fuvid:fuvid,
                            facilities:facilities,                        
                            visitcat:visitcat,
                            codegroups:codegroups,
                            codegroupsopt:codegroupsopt,
                            codegroupsopt2:codegroupsopt2,
                            codegroupsopt3:codegroupsopt3,
                            codegroupsopt4:codegroupsopt4,
                            codegroupsopt5:codegroupsopt5,
                            
                            codegroupsopt6:codegroupsopt6,
                            codegroupsopt7:codegroupsopt7,
                            codegroupsopt8:codegroupsopt8,
                            codegroupsopt9:codegroupsopt9,
                            codegroupsopt10:codegroupsopt10,
                            codegroupsopt11:codegroupsopt11,
                            codegroupsopt12:codegroupsopt12,
                            codegroupsopt13:codegroupsopt13,
                            codegroupsopt14:codegroupsopt14
                            
                        },
                        
                    success: function(response)
                    {  
                        //alert(response);
                        window.opener.loadGrid();
                        window.close();
                    },
                    failure: function(response)
                    {
                            alert("error");
                    }		
            });
            
        });
    });
    
    
</script>

</head>
<body class="body_top">
<?php
    if(isset($_GET['id'])):
        $selectFuvQuery = "SELECT * FROM tbl_allcare_vistcat_codegrp WHERE id = ". $_GET['id'] ." ORDER BY id DESC";
        $fuvRows = sqlStatement($selectFuvQuery);
        
            while($row = sqlFetchArray($fuvRows)):
                    $facilities = $row['facility'];   
                
                    $categories = $row['visit_category'];
                
                    $codegrps   = $row['code_groups'];
                
                    $codeopt = unserialize($row['code_options']);
                
              
            endwhile;
    endif;
    
    // Get list of facilities
    $f_res = sqlStatement("select id,name from `facility` order by `name`");
    $f_arr = array();
    for($i=0; $row=sqlFetchArray($f_res); $i++) {
      $f_arr[$i]=$row;
    }
    
    // Get list of visit categories
    $v_res = sqlStatement("select pc_catid,pc_catname from `openemr_postcalendar_categories` order by `pc_catname`");
    $v_arr = array();
    for($i=0; $vrow=sqlFetchArray($v_res); $i++) {
      $v_arr[$i]=$vrow;
    }
    // Get list of active forms
    $cg_res = sqlStatement("SELECT DISTINCT (`fs_category`) FROM  `fee_sheet_options` ");
    $cg_arr = array();
    for($i=0; $cgrow=sqlFetchArray($cg_res); $i++) {
        $cg_arr[$i]=$cgrow;   
    }
?>
<form class="form-horizontal" id="form-horizontal" method="POST">
    <div class="form-group">
        <label class="col-sm-2 control-label">Facilities</label>
        <div class="col-sm-10">
            <select id="facilities-post" name="facilities">
                <?php
                    foreach($f_arr as $facility):
                    ?>
                        <option value="<?php echo $facility['id']; ?>" <?php if($facility['id']==$facilities): ?> selected="selected" <?php endif;?>><?php echo $facility['name']; ?></option>
                    <?php
                    endforeach;
                ?>
            </select>
        </div>
        <label class="col-sm-2 control-label">Visit Categories</label>
        <div class="col-sm-10">
            <select id="visitcat-post" name="visitcat" >
                <?php
                    foreach($v_arr as $visitCat):
                    ?>
                        <option value="<?php echo $visitCat['pc_catid']; ?>" <?php if($visitCat['pc_catid']==$categories): ?> selected="selected" <?php endif;?>><?php echo $visitCat['pc_catname']; ?></option>
                    <?php
                    endforeach;
                ?>
            </select>
        </div>
        <label class="col-sm-2 control-label">Code Groups</label>
        <div class="col-sm-10">
          <select  id="codegroups-post" name="codegroups" onchange=""><option value="" selected>none selected</option>
                <?php
                foreach ($cg_arr as $noticia2) {
                ?>
                <option  value="<?php echo $noticia2['fs_category']; ?>" <?php if($noticia2['fs_category']==$codegrps): ?> selected="selected" <?php endif;?>><?php echo $noticia2['fs_category']; ?></option>
                <?php } ?>
           </select>

        </div>
          <label class="col-sm-2 control-label">Code Group Options</label>
       <?php
        $cg_res = sqlStatement("SELECT DISTINCT (`fs_category`) FROM  `fee_sheet_options` ");
        $cg_arr = array();
        for($i=0; $cgrow=sqlFetchArray($cg_res); $i++) {
            $cg_arr[$i]=$cgrow;
            $cgo_res = sqlStatement("SELECT `fs_option` FROM `fee_sheet_options` WHERE `fs_category` ='".$cgrow['fs_category']."' ");
            $cgo_arr = array();
            $j = $i + 1;
            $classname = "codegroupsopt".$j."[]";
            $idvalue = "codegroupsopt-post".$j;
            ?>
          
          <div class="col-sm-10" id="codeoption_<?php echo str_replace(" ","_",$cgrow['fs_category']) ?>" <?php if($cgrow['fs_category'] == $codegrps): ?> style="display: block;" <?php else: ?> style="display:none;" <?php endif; ?>>
                <select id='<?php echo $idvalue; ?>' name='<?php echo $classname ?>' multiple="multiple" >
                <?php    
                      for($j=0; $cgorow=sqlFetchArray($cgo_res); $j++) {  
                        $cgo_arr[$j]=$cgorow;
                        ?>
                        <option value="<?php echo $cgorow['fs_option']; ?>" <?php if(in_array($cgorow['fs_option'],$codeopt ) && $cgrow['fs_category']==$codegrps): ?> selected="selected" <?php endif;?>><?php echo $cgorow['fs_option']; ?></option>
                        <?php
                      }
                ?>
               </select>         
            </div>
            <?php    
        }
       ?>
    </div>
    
    
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <button type="submit" class="btn btn-default">Submit</button>
        </div>
    </div>
    <input type="hidden" name="fuvid" id="fuvid" value="<?php echo $_GET['id']; ?>" />
</form>
    <div id="msg"></div>
</body>
</html>
