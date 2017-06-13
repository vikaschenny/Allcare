<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once("../globals.php");
require_once("$srcdir/sql.inc");
require_once("$srcdir/formdata.inc.php");
require_once("$srcdir/options.inc.php");
require_once("$srcdir/acl.inc");

// Ensure authorized
if (!acl_check('admin', 'users')) {
  die(xlt("Unauthorized"));
}

 $user1=$_POST['users'];

?>

<link rel='stylesheet' type='text/css' href='<?php echo $GLOBALS['webroot'] ?>/interface/main/css/jquery.dataTables.css'>
<link rel='stylesheet' type='text/css' href='<?php echo $GLOBALS['webroot'] ?>/interface/main/css/dataTables.tableTools.css'>
<link rel='stylesheet' type='text/css' href='<?php echo $GLOBALS['webroot'] ?>/interface/main/css/dataTables.colVis.css'>
<link rel='stylesheet' type='text/css' href='<?php echo $GLOBALS['webroot'] ?>/interface/main/css/dataTables.colReorder.css'>

<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/bootstrap/docs/js/jquery-2.1.1.min.js"></script>
<script type='text/javascript' src='<?php echo $GLOBALS['webroot'] ?>/interface/main/js/jquery.dataTables.min.js'></script>
<script type='text/javascript' src='<?php echo $GLOBALS['webroot'] ?>/interface/main/js/dataTables.tableTools.js'></script>
<script type='text/javascript' src='<?php echo $GLOBALS['webroot'] ?>/interface/main/js/dataTables.colReorder.js'></script>
<script type='text/javascript' src='<?php echo $GLOBALS['webroot'] ?>/interface/main/js/dataTables.colVis.js'></script>
<script type="text/javascript">
    $('#gridview').dataTable();
    function edit(id){
        window.open('visittypemapping_edit.php?id='+id,'editwindow','width=600,height=700');
    }
    function loadGrid(){
        jQuery('#visittypemap').html('loading...');
        jQuery('#visittypemap').load('visittypemapping_add.php');
    }
    function del(id){
        if (confirm("Do you really want to delete this group") == true) {
            jQuery('#facuservisit').html('Please wait...');
            jQuery.ajax({
                type: 'POST',
                url: "visittypemapping_add.php",	
                data: {
                        deleteid:id,                        
                      },

                success: function(response)
                {
                   
                    jQuery('#visittypemap').html(response);
                },
                failure: function(response)
                {
                        alert("error");
                }		
            });
        } else {
            return false;
        }
        
    }
</script>


<table id="gridview" class="table table-striped table-bordered" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th>Appt Visit Category</th>
            <th>Visit Type</th>
            <th>Fee Sheet Visit Category</th>
            <th>Audit Visit Category</th>
            <th>CPT</th>
            <th>Edit/Delete</th>
        </tr>
    </thead>

    <tfoot>
        <tr>
            <th>Appt Visit Category</th>
            <th>Visit Type</th>
            <th>Fee Sheet Visit Category</th>
            <th>Audit Visit Category</th>
            <th>CPT</th>
            <th>Edit/Delete</th>
        </tr>
    </tfoot>
    <tbody>
        <?php
        // Get list of visit categories
        $v_res = sqlStatement("select pc_catid,pc_catname from `openemr_postcalendar_categories` order by `pc_catname`");
        $v_arr = array();
        for($i=0; $vrow=sqlFetchArray($v_res); $i++) {
          $v_arr[$i]=$vrow;
        }
        // Check if visit category exists in custom table
        $appVisitCategories = array();
        foreach($v_arr as $visitCat):
            $sql = sqlStatement("SELECT visit_category FROM tbl_allcare_visittypemapping WHERE visit_category = '".$visitCat['pc_catid']."'");
            $appVisitCategories[$visitCat['pc_catid']] = $visitCat['pc_catname'];
            $numrows = sqlNumRows($sql);
            if($numrows == 0):
                sqlStatement("INSERT INTO tbl_allcare_visittypemapping (visit_category) VALUES('".$visitCat['pc_catid']."')");
            endif;
        endforeach;
        
        
        $sql2 = sqlStatement("SELECT * FROM tbl_allcare_visittypemapping");
        while($rows = sqlFetchArray($sql2)):
        ?>
            <tr>
                <td><?php echo $appVisitCategories[$rows['visit_category']]; ?></td>
                <td><?php echo $rows['visit_type']; ?></td>
                <td><?php echo $rows['fee_visit_category']; ?></td>
                <td><?php echo $rows['audit_visit_category']; ?></td>
                <td><?php echo $rows['cpt_code']; ?></td>
                <td><a href="javascript:edit(<?php echo $rows['visit_category']; ?>)">Edit</a>/<a href="javascript:del(<?php echo $rows['visit_category']; ?>)">Delete</a></td>
            </tr>
        <?php
        endwhile;
        foreach($v_arr as $visitCat):
        ?>
        
        <?php
        endforeach;
        
        ?>        
    </tbody>
</table>