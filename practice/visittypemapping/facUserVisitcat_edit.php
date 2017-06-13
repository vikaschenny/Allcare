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
    #facuservisit{background-color: #fe5301;width: 1100px;height: 600px;overflow: scroll;}
</style>
<script type="text/javascript">
    $(document).ready(function() {
        
        
//        $("#codeoption_2Dictation").hide();
//                $("#codeoption_3Form_Data").hide();
//                $("#codeoption_5Predefined_Forms").hide();
//                $("#codeoption_6Codes").hide();
//                $("#codeoption_7Hyperlink").hide();
                
                
        $("select#screens-post").on('change', function () {
               if ($(this).val() == "2Dictation") {
                 $("#codeoption_2Dictation").show();
                $("#codeoption_3Form_Data").hide();
                $("#codeoption_5Predefined_Forms").hide();
                $("#codeoption_6Codes").hide();
                $("#codeoption_7Hyperlink").hide();
                
               } else if ($(this).val() == "3Form Data") {
                $("#codeoption_2Dictation").hide();
                $("#codeoption_3Form_Data").show();
                $("#codeoption_5Predefined_Forms").hide();
                $("#codeoption_6Codes").hide();
                $("#codeoption_7Hyperlink").hide();
                
               } else if ($(this).val() == "5Predefined Forms") {
                 $("#codeoption_2Dictation").hide();
                $("#codeoption_3Form_Data").hide();
                $("#codeoption_5Predefined_Forms").show();
                $("#codeoption_6Codes").hide();
                $("#codeoption_7Hyperlink").hide();
               }
               else if ($(this).val() == "6Codes") {
                 $("#codeoption_2Dictation").hide();
                $("#codeoption_3Form_Data").hide();
                $("#codeoption_5Predefined_Forms").hide();
                $("#codeoption_6Codes").show();
                $("#codeoption_7Hyperlink").hide();
               }
               else if ($(this).val() == "7Hyperlink") {
                 $("#codeoption_2Dictation").hide();
                $("#codeoption_3Form_Data").hide();
                $("#codeoption_5Predefined_Forms").hide();
                $("#codeoption_6Codes").hide();
                $("#codeoption_7Hyperlink").show();
               }else if ($(this).val() == "") {
                 $("#codeoption_2Dictation").hide();
                $("#codeoption_3Form_Data").hide();
                $("#codeoption_5Predefined_Forms").hide();
                $("#codeoption_6Codes").hide();
                $("#codeoption_7Hyperlink").hide();
               }
         });
         
         
        $('#facilities-post').multiselect({
            includeSelectAllOption: true,
            enableFiltering: true
        });
        $('#users-post').multiselect({
            includeSelectAllOption: true,
            enableFiltering: true
        });
        $('#visitcat-post').multiselect({
            includeSelectAllOption: true,
            enableFiltering: true
        });
        $('#screens-post').multiselect({
            includeSelectAllOption: true,
            enableFiltering: true
        });
//        $('#medicalrecord-post').multiselect({
//            includeSelectAllOption: true,
//            enableFiltering: true
//     
//        });
        $("#form-horizontal").submit(function(e) {
            e.preventDefault();
            
            if(jQuery('#facilities-post').val() == null || 
               jQuery('#users-post').val() == null ||
               jQuery('#visitcat-post').val() == null ||
               jQuery('#screens-post').val() == null ||
               jQuery('#screenlinks').val() == ""
              ){
                      alert("All fields are required");
                      return false;
              }
            
            var facilities=jQuery('#facilities-post').val(); 
            var users=jQuery('#users-post').val();
            var visitcat=jQuery('#visitcat-post').val();
            var screens=jQuery('#screens-post').val();
            var screenlinks=jQuery('#screenlinks').val();
            //var medicalgroup=jQuery('#medicalrecord-post').val(); 
            var fuvid=jQuery('#fuvid').val();
            
             var result_arr=[];
            
                    jQuery('div', jQuery("#screen_names_"+screens.replace(" ", "_"))).each(function() {
                        var seq=jQuery("#"+jQuery(this).attr('id')+"-seq").val();
                       // alert(seq); 
                        var req=jQuery("#"+"screens-names-"+jQuery(this).attr('id')).val();
                       // alert(req); 
                        var req_val=req.replace("-","$");
                        var req_val1=req_val.replace("-","$");
                        var result=seq+"$$"+req_val1;
                        result_arr.push(result);
                      
                    });
          // alert(result_arr);
            jQuery.ajax({
                    type: 'POST',
                    url:  "facUserVisitcat_update.php",	
                    data: {
                            facilities:facilities,                        
                            users:users,
                            visitcat:visitcat,
                            screens:screens,
                            screenlinks:screenlinks,
                           // medicalgroup:medicalgroup,
                            fuvid: fuvid,
                            result_arr:result_arr
                        },
                        
                    success: function(response)
                    {
                      // alert(response);
                        window.opener.loadGrid();
                        //window.close();
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
        $selectFuvQuery = "SELECT * FROM tbl_allcare_facuservisit WHERE id = ". $_GET['id'] ." ORDER BY id DESC";
        $fuvRows = sqlStatement($selectFuvQuery);
        
            while($row = sqlFetchArray($fuvRows)):
                $facilities = unserialize($row['facilities']);
                
                $users = unserialize($row['users']);
                
                $categories = unserialize($row['visit_categories']);
                
               $screengroup =$row['screen_group'];
               
               $screen_names=unserialize($row['screen_names']);
             //  echo "<pre>"; print_r($screen_names); echo "</pre>";
               $medical_group =$row['medicalgroup'];
               
                
                //$screenlinks = $row['screen_links'];
           endwhile;
    endif;
    
    // Get list of facilities
    $f_res = sqlStatement("select id,name from `facility` order by `name`");
    $f_arr = array();
    for($i=0; $row=sqlFetchArray($f_res); $i++) {
      $f_arr[$i]=$row;
    }
    // Get list of provider users
    $p_res = sqlStatement("select id,fname,mname,lname from `users` where authorized = 1 order by `fname`");
    $p_arr = array();
    for($i=0; $prow=sqlFetchArray($p_res); $i++) {
      $p_arr[$i]=$prow;
    }
    // Get list of visit categories
    $v_res = sqlStatement("select pc_catid,pc_catname from `openemr_postcalendar_categories` order by `pc_catname`");
    $v_arr = array();
    for($i=0; $vrow=sqlFetchArray($v_res); $i++) {
      $v_arr[$i]=$vrow;
    }
    // Get list of active forms
//    $s_res = sqlStatement("select DISTINCT group_name  from `layout_options` WHERE form_id='LBF1'");
//    $s_arr = array();
//    for($i=0; $srow=sqlFetchArray($s_res); $i++) {
//      $s_arr[$i]=$srow;
//    }
    
    $cg_res = sqlStatement("select DISTINCT group_name from `layout_options` WHERE form_id='LBF1'");
     $cg_arr = array();
    for($i=0; $cgrow=sqlFetchArray($cg_res); $i++) {
        $s_arr[$i]=$cgrow;
        $cgo_res = sqlStatement("SELECT field_id,title FROM `layout_options` WHERE `group_name` ='".$cgrow['group_name']."' ");
        $cgo_arr = array();
        for($j=0; $cgorow=sqlFetchArray($cgo_res); $j++) {  
          $cgo_arr[$j]=$cgorow;
          //print_r($cgo_arr[$j]);
          }
    }
    // Get Medical Record Screens
    $medical_record_result = sqlStatement("select DISTINCT group_name from `layout_options` WHERE form_id='CHARTOUTPUT'");
    $medical_record_array = array();
    for($i=0; $medical_record_row=sqlFetchArray($medical_record_result); $i++) {
      $medical_record_array[$i]=$medical_record_row;
    }
?>
<form class="form-horizontal" id="form-horizontal" method="POST">
    <div class="form-group">
        <label class="col-sm-2 control-label">Facilities</label>
        <div class="col-sm-10">
            <select id="facilities-post" name="facilities[]" multiple="multiple">
                <?php
                    foreach($f_arr as $facility):
                    ?>
                        <option value="<?php echo $facility['id']; ?>" <?php if(in_array($facility['id'],$facilities)): ?> selected="selected" <?php endif;?>><?php echo $facility['name']; ?></option>
                    <?php
                    endforeach;
                ?>
            </select>
        </div>
        <label class="col-sm-2 control-label">Users</label>
        <div class="col-sm-10">
            <select id="users-post" name="users[]" multiple="multiple">
                <?php
                    foreach($p_arr as $provider):
                    ?>
                        <option value="<?php echo $provider['id']; ?>" <?php if(in_array($provider['id'],$users)): ?> selected="selected" <?php endif;?>><?php echo $provider['fname']." ". $provider['mname']. " ". $provider['lname']; ?></option>
                    <?php
                    endforeach;
                ?>
            </select>
        </div>
        <label class="col-sm-2 control-label">Visit Categories</label>
        <div class="col-sm-10">
            <select id="visitcat-post" name="visitcat[]" multiple="multiple">
                <?php
                    foreach($v_arr as $visitCat):
                    ?>
                        <option value="<?php echo $visitCat['pc_catid']; ?>" <?php if(in_array($visitCat['pc_catid'],$categories)): ?> selected="selected" <?php endif;?>><?php echo $visitCat['pc_catname']; ?></option>
                    <?php
                    endforeach;
                ?>
            </select>
        </div>
        <label class="col-sm-2 control-label">Screen Groups</label>
        <div class="col-sm-10">
            <select id="screens-post">
                <option value="">none selected</option>
                <?php
                    foreach($s_arr as $screen):
                    ?>
                        <option value="<?php echo $screen['group_name']; ?>" <?php if($screen['group_name']==$screengroup): ?> selected="selected" <?php endif;?>><?php echo substr($screen['group_name'], 1); ?></option>
                    <?php
                    endforeach;
                ?>
            </select>
        </div>
         <?php 
          $cg_res = sqlStatement("select DISTINCT group_name from `layout_options` WHERE form_id='LBF1'");
         $cg_arr = array();
        for($i=0; $cgrow=sqlFetchArray($cg_res); $i++) {
            $cg_arr[$i]=$cgrow;
            $cgo_res = sqlStatement("SELECT field_id,title FROM `layout_options` WHERE `group_name` ='".$cgrow['group_name']."' ");
            $cgo_arr = array();
            $j = $i + 1;
            $classname = "codegroupsopt".$j."[]";
            $idvalue = "codegroupsopt-post".$j;
            ?>
         
         
         <div class="col-sm-10" id="codeoption_<?php echo str_replace(" ","_",$cgrow['group_name']) ?>" <?php if($cgrow['group_name'] == $screengroup): ?> style="display: block;" <?php else: ?> style="display:none;" <?php endif; ?>>
         <label class="col-sm-2 control-label">Screen Names</label>
             <div id="screen_names_<?php echo str_replace(" ","_",$cgrow['group_name']) ?>" style="background-color:#ffffff; border-color:#000000; width:60% !important;  border:2px;  height:auto; padding-left:10px; padding-top:10px; box-shadow: inset 0 3px 5px rgba(0,0,0,.125); overflow: auto;">
               
             <table id="tab">
                 <thead><tr><td>Order</td><td></td> <td>Req/Opt</td><td></td><td>Names</td></tr></thead><tbody>
                <?php    
                      for($j=0; $cgorow=sqlFetchArray($cgo_res); $j++) {  
                        $cgo_arr[$j]=$cgorow;
                        ?>
                     <div id="<?php echo $cgorow['field_id']; ?>"><tr>
                        <td><input type="text"  id="<?php echo $cgorow['field_id']."-seq"; ?>"  size="1" 
                            <?php foreach($screen_names as $val1){ $val2=explode("$$",$val1); if( $cgorow['field_id']==$val2[2] && $cgrow['group_name'] == $screengroup){?> value="<?php echo $val2[0] ?>"  <?php } } ?>/></td> 
                        <td>&nbsp;</td><td><select id="screens-names-<?php echo $cgorow['field_id']; ?>" name="screens-names">
                              <option value="Optional--<?php echo $cgorow['field_id']; ?>" selected <?php foreach($screen_names as $val) { 
                                  $val1=explode("$$",$val); 
                                  $opt1= $val1[1]."--".$val1[2];
                                  if($opt1=="Optional--".$cgorow['field_id'] && $cgrow['group_name'] == $screengroup){?>
                                      selected="selected" 
                               <?php   }        
                              }?>>Optional</option>
                              <option value="Required--<?php echo $cgorow['field_id']; ?>" <?php foreach($screen_names as $val) { 
                                  $val1=explode("$$",$val); 
                                  $opt1= $val1[1]."--".$val1[2];
                                  if($opt1=="Required--".$cgorow['field_id'] && $cgrow['group_name'] == $screengroup){?>
                                      selected="selected" 
                               <?php   }        
                              }?>>Required</option>
                              <option value="Unused--<?php echo $cgorow['field_id']; ?>" <?php foreach($screen_names as $val) { 
                                  $val1=explode("$$",$val); 
                                  $opt1= $val1[1]."--".$val1[2];
                                  if($opt1=="Unused--".$cgorow['field_id'] && $cgrow['group_name'] == $screengroup){?>
                                      selected="selected" 
                               <?php   }        
                              }?>>Unused</option>
                            </select></td><td>&nbsp;</td><td><?php echo $cgorow['title']; ?></td></tr></div>
                    
                        <?php
                      }
                ?>
                 </tbody>
        </table>
        </div></div>
       
        <?php } ?>
       
<!--       <label class="col-sm-2 control-label">Medical Groups</label>
         <div class="col-sm-10">
            <select id="medicalrecord-post" name="medicalrecord-post" >
                <option value="" selected>none selected</option>
               
                <?php
                    foreach($medical_record_array as $medical_record):
                    ?>
                        <option value="<?php echo $medical_record['group_name']; ?>" <?php if($medical_record['group_name']==$medical_group): ?> selected="selected" <?php endif;?>><?php echo substr($medical_record['group_name'], 1); ?></option>
                    <?php
                    endforeach;
                ?>
            </select>
        </div>
   
        <label class="col-sm-2 control-label">Medical Groups</label>
        <div class="col-sm-10">
            <select id="medicalrecord-post" name="medicalrecord-post" >
                <option value="" >none selected</option>
               
                <?php
                    foreach($medical_record_array as $medical_record):
                    ?>
                        <option value="<?php echo $medical_record['group_name']; ?>" <?php if($medical_record['group_name']==$medical_group): ?> selected="selected" <?php endif;?>><?php echo substr($medical_record['group_name'], 1); ?></option>
                    <?php
                    endforeach;
                ?>
            </select>
        </div>-->
        <!--<label class="col-sm-2 control-label">Screen links</label>
        <div class="col-sm-10">
            <textarea rows="10" name="screenlinks" id="screenlinks"><?php echo $screenlinks; ?></textarea><br />
            <em>mention screen links in order of screen names above by separating them by comma (,)</em>
        </div>-->
<!--        <label class="col-sm-2 control-label">Group name</label>
        <div class="col-sm-10">
            <input type="text" class="inputtext-control" id="groupname" name="groupname" value="<?php echo $groupname; ?>">
        </div>-->
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
