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
        
                $("#codeoption_2Dictation").hide();
                $("#codeoption_3Form_Data").hide();
                $("#codeoption_5Predefined_Forms").hide();
                $("#codeoption_6Codes").hide();
                $("#codeoption_7Hyperlink").hide();
              
               
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
        
        jQuery('#facuservisit').load('facUserVisitcat_add.php');
        
        $("#form-horizontal").submit(function(e) {
            e.preventDefault();
            var facilities=jQuery('#facilities-post').val(); 
            var users=jQuery('#users-post').val();
            var visitcat=jQuery('#visitcat-post').val();
            var screens=jQuery('#screens-post').val();
            //alert(screens);
            var screenlinks=jQuery('#screenlinks').val();
          // var medicalgroup=jQuery('#medicalrecord-post').val(); 
            var seq=jQuery('#result').val();
            var req=jQuery('#req_result').val();
            
            var result_arr=[];
            
                    jQuery('div', jQuery("#screen_names_"+screens.replace(" ", "_"))).each(function() {
                        var seq=jQuery("#"+jQuery(this).attr('id')+"-seq").val();
                      //  alert(seq); 
                        var req=jQuery("#"+"screens-names-"+jQuery(this).attr('id')).val();
                       // alert(req); 
                        var req_val=req.replace("-","$");
                        var req_val1=req_val.replace("-","$");
                        var result=seq+"$$"+req_val1;
                        result_arr.push(result);
                       //alert(result_arr);
                    });

            
            //alert(jQuery('.sequence_'+screens).val());
            if(jQuery('#facilities-post').val() == null || 
               jQuery('#users-post').val() == null ||
               jQuery('#visitcat-post').val() == null ||
               jQuery('#screens-post').val() == null ||
               jQuery('#screenlinks').val() == "" ){
                 
                      alert("All fields are required");
                      return false;
              }
//            jQuery('div', jQuery("#screen_names_"+screens)).each(function() {
//                jQuery("#"+jQuery(this).attr('id')+"-seq").val()="";
//                alert("sequence field is required");
//             });   
            jQuery('#facuservisit').html('loading...');

            jQuery.ajax({
                    type: 'POST',
                    url: "facUserVisitcat_add.php",	
                    data: {
                            facilities:facilities,                        
                            users:users,
                            visitcat:visitcat,
                            screens:screens,
                            screenlinks:screenlinks,
                            //medicalgroup:medicalgroup,
                            result_arr:result_arr
                        },

                    success: function(response)
                    {
                        //alert(response);
                        jQuery('#facuservisit').html(response);
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
//    // Get Screen Names
//    $s_res = sqlStatement("select DISTINCT group_name from `layout_options` WHERE form_id='LBF1'");
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
                        <option value="<?php echo $facility['id']; ?>"><?php echo $facility['name']; ?></option>
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
                        <option value="<?php echo $provider['id']; ?>"><?php echo $provider['fname']." ". $provider['mname']. " ". $provider['lname']; ?></option>
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
                        <option value="<?php echo $visitCat['pc_catid']; ?>"><?php echo $visitCat['pc_catname']; ?></option>
                    <?php
                    endforeach;
                ?>
            </select>
        </div>
        <label class="col-sm-2 control-label">Screen Groups</label>
        <div class="col-sm-10">
            <select id="screens-post" name="screens-post" >
                <option value="" selected>none selected</option>
               
                <?php
                    foreach($s_arr as $screen):
                    ?>
                        <option value="<?php echo $screen['group_name']; ?>"><?php echo substr($screen['group_name'], 1); ?></option>
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
          <div id="codeoption_<?php echo str_replace(" ","_",$cgrow['group_name']) ?>" >
          <label class="col-sm-2 control-label">Screen Names</label>
         <div class="col-sm-10">
         <div id="screen_names_<?php echo str_replace(" ","_",$cgrow['group_name']) ?>" style="background-color:#ffffff; border-color:#000000; width:50% !important;  border:2px;  height:auto; padding-left:10px; padding-top:10px; box-shadow: inset 0 3px 5px rgba(0,0,0,.125); overflow: auto;">
               
             <table id="tab">
                 <thead><tr><td>Order</td><td></td><td>Req/Opt</td><td></td><td>Names</td></tr></thead><tbody>
                <?php    
                      for($j=0; $cgorow=sqlFetchArray($cgo_res); $j++) {  
                        $cgo_arr[$j]=$cgorow;
                        ?>
                     <div id="<?php echo $cgorow['field_id']; ?>"><tr>
                        <td><input type="text"  id="<?php echo $cgorow['field_id']."-seq"; ?>"  size="1" /></td> 
                        <td>&nbsp;</td><td><select id="screens-names-<?php echo $cgorow['field_id']; ?>" name="screens-names" >
                              <option value="Optional--<?php echo $cgorow['field_id']; ?>" selected>Optional</option>
                              <option value="Required--<?php echo $cgorow['field_id']; ?>">Required</option>
                              <option value="Unused--<?php echo $cgorow['field_id']; ?>" >Unused</option>
                            </select></td> <td>&nbsp;</td><td><?php echo $cgorow['title']; ?></td></tr></div>
                    
                        <?php
                      }
                ?>
                 </tbody>
        </table>
        </div></div>
          </div>
        <?php } ?>
<!--       
       <label class="col-sm-2 control-label">Medical Groups</label>
         <div class="col-sm-10">
            <select id="medicalrecord-post" name="medicalrecord-post" >
                <option value="" selected>none selected</option>
               
                <?php
                    foreach($medical_record_array as $medical_record):
                    ?>
                        <option value="<?php echo $medical_record['group_name']; ?>"><?php echo substr($medical_record['group_name'], 1); ?></option>
                    <?php
                    endforeach;
                ?>
            </select>
        </div>-->
   

<!--        <label class="col-sm-2 control-label">Group name</label>
        <div class="col-sm-10">
            <input type="text" class="inputtext-control" id="groupname" name="groupname">
        </div>-->
    </div>
    
    
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <button type="submit" class="btn btn-default">Submit</button>
<!--            <input type="hidden" id="result" value=""/>
            <input type="hidden" id="req_result" value=""/>-->
        </div>
    </div>
</form>
    <div id="msg"></div>
    <div id="facuservisit"></div>
    
</body>
</html>
