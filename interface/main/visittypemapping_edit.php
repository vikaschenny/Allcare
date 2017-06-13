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

<link rel="stylesheet" href="<?php echo $GLOBALS['webroot'] ?>/library/bootstrap/dist/css/bootstrap-multiselect.css" type="text/css">
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/bootstrap/dist/js/bootstrap-multiselect.js"></script>

<style>
    .inputtext-control{width:"20px;"}
    #facuservisit{background-color: #fe5301;width: 1100px;height: 600px;overflow: scroll;}
</style>
<script type="text/javascript">
    $(document).ready(function() {

        $("#form-horizontal").submit(function(e) {
            e.preventDefault();
            var visitType=jQuery('#visitType-post').val(); 
            var feesheetvisit=jQuery('#feesheetvisit-post').val(); 
            var auditvisit=jQuery('#auditvisit-post').val(); 
            var fuvid=jQuery('#fuvid').val(); 
            
            jQuery.ajax({
                    type: 'POST',
                    url:  "visittypemapping_update.php",	
                    data: {
                            visitType:visitType,                        
                            feesheetvisit:feesheetvisit,
                            auditvisit:auditvisit,
                            fuvid: fuvid
                        },
                        
                    success: function(response)
                    {
                        //console.log(response);
                        alert(response);
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
        
    endif;
    
    
?>
<form class="form-horizontal" id="form-horizontal" method="POST">
    <div class="form-group">
        <label class="col-sm-2 control-label">Appt Visit Category</label>
        <div class="col-sm-10">
            <?php
            // Get list of visit categories
            $sql = sqlStatement("select pc_catid,pc_catname from `openemr_postcalendar_categories` WHERE pc_catid = ".$_GET['id']);
            $row = sqlFetchArray($sql);
            echo $row['pc_catname'];
            ?>
        </div>
        <?php
        $sql = sqlStatement("SELECT * FROM tbl_allcare_visittypemapping WHERE visit_category = ".$_GET['id']);
        while($row = sqlFetchArray($sql)):
            $visitType = $row['visit_type'];
            $feevisitcategory = $row['fee_visit_category'];
            $auditvisitcategory = $row['audit_visit_category'];
        endwhile;
        ?>
        <label class="col-sm-2 control-label">Visit Type</label>
        <div class="col-sm-10">
            <select id="visitType-post" name="visitType[]">
                <?php
                    $sql = sqlStatement("SELECT * FROM list_options WHERE list_id = 'AllcareVisitTypes'");
                    while($row = sqlFetchArray($sql)):
                        ?>
                            <option value="<?php echo $row['option_id']; ?>" <?php if($row['option_id'] == $visitType): ?> selected <?php endif; ?>><?php echo $row['title']; ?></option>
                        <?php
                    endwhile;
                ?>
            </select>
        </div>
        <label class="col-sm-2 control-label">Fee Sheet Visit Categories</label>
        <div class="col-sm-10">
            <select id="feesheetvisit-post" name="feesheetvisit[]">
                <?php
                    $sql = sqlStatement("SELECT * FROM fee_sheet_options");
                    while($row = sqlFetchArray($sql)):
                            $fcat = $row['fs_category'].'$$'.$row['fs_option'];
                        ?>
                            <option value="<?php echo $row['fs_category'].'$$'.$row['fs_option']; ?>" <?php if($feevisitcategory == $fcat): ?> selected <?php endif; ?>><?php echo $row['fs_category'].'--'.$row['fs_option']; ?></option>
                        <?php
                    endwhile;
                ?>
            </select>
        </div>
        <label class="col-sm-2 control-label">Audit Visit Category</label>
        <div class="col-sm-10">
            <select id="auditvisit-post">
                <?php
                    $sql = sqlStatement("SELECT * FROM list_options WHERE list_id = 'Level_Of_Service'");
                    while($row = sqlFetchArray($sql)):
                            $audcat = $row['option_id'].'$$'.$row['title'];
                        ?>
                            <option value="<?php echo $row['option_id'].'$$'.$row['title']; ?>" <?php if($auditvisitcategory == $audcat): ?> selected <?php endif; ?>><?php echo $row['option_id'].'--'.$row['title']; ?></option>
                        <?php
                    endwhile;
                ?>
            </select>
        </div>
        
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
