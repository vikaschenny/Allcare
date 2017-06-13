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
        window.open('visitcat_codegrp_edit.php?id='+id,'editwindow','width=600,height=700');
    }
    function loadGrid(){
        jQuery('#visitcat_codegrp').html('loading...');
        jQuery('#visitcat_codegrp').load('visitcat_codegrp_add.php');
    }
    function del(id){
        if (confirm("Do you really want to delete this group") == true) {
            jQuery('#visitcat_codegrp').html('Please wait...');
            jQuery.ajax({
                type: 'POST',
                url: "visitcat_codegrp_add.php",	
                data: {
                        deleteid:id,                        
                      },

                success: function(response)
                {
                    jQuery('#visitcat_codegrp').html(response);
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
<?php
    
    if($_POST['deleteid'] != ""){
        $deleteQuery = "DELETE FROM  tbl_allcare_vistcat_codegrp WHERE id = ".$_POST['deleteid'];
        sqlStatement($deleteQuery);
        $insertlogQuery = "INSERT INTO tbl_allcare_vistcat_codegrp_log (vcgrpid,userid,timestamp,action) VALUES (".$_POST['deleteid'].",'".$_SESSION['authUser']."',now(),'deleted')";
        sqlStatement($insertlogQuery);
    }
    
    /*$facilities = (count($_POST['facilities']) > 0)? serialize($_POST['facilities']) : "";
    $visitcat   = (count($_POST['visitcat']) > 0)? serialize($_POST['visitcat']) : "" ;
    $codegrps   = (count($_POST['codegroups']) > 0)? serialize($_POST['codegroups']) : "";
    $codegrpopt   = (count($_POST['codegroupsopt']) > 0)? serialize($_POST['codegroupsopt']) : "";*/
    
         $facilities = $_POST['facilities'];
         $visitcat   = $_POST['visitcat'];
         $codegrps   = $_POST['codegroups'];
   //echo  $codegrps   = (count($_POST['codegroups']) > 0)? serialize($_POST['codegroups']) : "";
   //echo $_POST['codegroupsopt']; 
    if($_POST['codegroupsopt']!="")   
      echo $codegrpopt   = (count($_POST['codegroupsopt']) > 0)? serialize($_POST['codegroupsopt']) : "";
      //echo $codegrpopt1=unserialize($codegrpopt);
      //echo $implodecodeopt1= implode(",",$codegrpopt1);
    if($_POST['codegroupsopt2']!="")   
       $codegrpopt   = (count($_POST['codegroupsopt2']) > 0)? serialize($_POST['codegroupsopt2']) : "";
    if($_POST['codegroupsopt3']!="")   
       $codegrpopt   = (count($_POST['codegroupsopt3']) > 0)? serialize($_POST['codegroupsopt3']) : "";
    if($_POST['codegroupsopt4']!="")   
        $codegrpopt   = (count($_POST['codegroupsopt4']) > 0)? serialize($_POST['codegroupsopt4']) : "";
    if($_POST['codegroupsopt5']!="")   
       $codegrpopt   = (count($_POST['codegroupsopt5']) > 0)? serialize($_POST['codegroupsopt5']) : "";
    if($_POST['codegroupsopt6']!="")   
       $codegrpopt   = (count($_POST['codegroupsopt6']) > 0)? serialize($_POST['codegroupsopt6']) : "";
    if($_POST['codegroupsopt7']!="")   
       $codegrpopt   = (count($_POST['codegroupsopt7']) > 0)? serialize($_POST['codegroupsopt7']) : "";
    if($_POST['codegroupsopt8']!="")   
       $codegrpopt   = (count($_POST['codegroupsopt8']) > 0)? serialize($_POST['codegroupsopt8']) : "";
    if($_POST['codegroupsopt9']!="")   
       $codegrpopt   = (count($_POST['codegroupsopt9']) > 0)? serialize($_POST['codegroupsopt9']) : "";   
    
    
    if($facilities != ""  && $visitcat != "" && $codegrps != "" && $codegrpopt != ""):
        $result = sqlStatement("SELECT * FROM  tbl_allcare_vistcat_codegrp WHERE    code_groups='".$codegrps."' AND  code_options='".$codegrpopt."' AND facility='".$facilities."' AND visit_category='".$visitcat."'");
       
        
        if(sqlNumRows($result) > 0 ):
            ?><script> alert("facility and visit_category with same selection already exists"); </script>
          
            <?php
        else:
           $insertQuery = "INSERT INTO  tbl_allcare_vistcat_codegrp (facility,visit_category,code_groups,code_options) VALUES('".$facilities."','". $visitcat ."','". $codegrps  ."','". $codegrpopt ."')";
            
            $fuvid = sqlInsert($insertQuery);
            $insertlogQuery = "INSERT INTO tbl_allcare_vistcat_codegrp_log (vcgrpid,userid,timestamp,action) VALUES (".$fuvid.",'".$_SESSION['authUser']."',now(),'created')";
            sqlStatement($insertlogQuery);
            ?><script> alert("New group created"); </script>
           <?php
         endif;
    else:
        echo "All fields are required.";
    endif;
    $selectFuvQuery = "SELECT * FROM  tbl_allcare_vistcat_codegrp ORDER BY id DESC";
    $fuvRows = sqlStatement($selectFuvQuery);
?>

<table id="gridview" class="table table-striped table-bordered" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th>Facilities</th>
            <th>Visit Categories</th>
            <th>Code Groups</th>
            <th>Code Group Options</th>
            <th>Generates</th>
            <th>Edit/Delete</th>
        </tr>
    </thead>

    <tfoot>
        <tr>
            <th>Facilities</th>
            <th>Visit Categories</th>
            <th>Code Groups</th>
            <th>Code Group Options</th>
            <th>Generates</th>
            <th>Edit/Delete</th>
        </tr>
    </tfoot>

    <tbody>
        <?php
            while($row = sqlFetchArray($fuvRows)):
                $facilities = $row['facility'];
                //$implodefac = implode(",",$facilities);
                
                $categories = $row['visit_category'];
                //$implodecategories = implode(",",$categories);
                
                $codegrp = $row['code_groups'];
                 
               
                $codeopt=unserialize($row['code_options']);  
                //$implodecodeopt = implode("','",$codeopt);
                $imp = "'" . implode("','",$codeopt) . "'";
                
                $f_res = sqlStatement("select name from `facility` WHERE id IN (".$facilities.")");
                $f_arr = array();
                for($i=0; $rowwww=sqlFetchArray($f_res); $i++) {
                  $f_arr[$i]=$rowwww['name'];
                }
                 
                $f_res2 = sqlStatement("select pc_catname from `openemr_postcalendar_categories` WHERE pc_catid IN (".$categories.")");
                $f_arr2 = array();
                for($i=0; $rowwww=sqlFetchArray($f_res2); $i++) {
                  $f_arr2[$i]=$rowwww['pc_catname'];
                }
                
                  
                $f_res3 = sqlStatement("SELECT  DISTINCT(fs_category)  FROM  `fee_sheet_options`  WHERE fs_category =  '".$codegrp."' ");
                //$f_res3 = sqlStatement("SELECT DISTINCT(fs_category) FROM  `fee_sheet_options`");
                $f_arr3 = array();
                for($i=0; $rowwww=sqlFetchArray($f_res3); $i++) {
                  $f_arr3[$i] = $rowwww['fs_category'];
                }
                
                //echo "SELECT `fs_option` FROM `fee_sheet_options` WHERE `fs_option` IN ($imp) AND fs_category =  '".$codegrp."' ";
                $f_res4 = sqlStatement("SELECT `fs_option` FROM `fee_sheet_options` WHERE `fs_option` IN ($imp) AND fs_category =  '".$codegrp."' ");
                //$f_res4 = sqlStatement("SELECT `fs_option` FROM `fee_sheet_options`");
                $f_arr4 = array();
                for($i=0; $rowwww=sqlFetchArray($f_res4); $i++) {
                  $f_arr4[$i]=$rowwww['fs_option'];
                }
                
                $f_res5 = sqlStatement("SELECT `fs_codes` FROM `fee_sheet_options` WHERE  fs_category =  '".$codegrp."' AND `fs_option` IN ($imp)");
                //$f_res4 = sqlStatement("SELECT `fs_option` FROM `fee_sheet_options`");
                $f_arr5 = array();
                for($i=0; $rowwww=sqlFetchArray($f_res5); $i++) {
                  $f_arr5[$i]=$rowwww['fs_codes'];
                }
                
                
               
                   /* $f_res5 = sqlStatement('SELECT description from `layout_options` WHERE field_id  ='.$slink);
                    $f_arr5 = array();
                    for($i=0; $link=sqlFetchArray($f_res5); $i++) {
                        $t = $i+1;
                        $f_arr5[$i]=$t.".".$link['description']."<br>";
                    }*/
                
        ?>
        <tr><td><?php echo implode(",",$f_arr); ?></td><td><?php echo implode(",",$f_arr2); ; ?></td><td><?php echo implode(",",$f_arr3); ?></td><td><?php echo implode(',',$f_arr4); ?></td><td><?php echo implode(',',$f_arr5); ?></td><td><a href="javascript:edit(<?php echo $row['id']; ?>)">Edit</a>/<a href="javascript:del(<?php echo $row['id']; ?>)">Delete</a></td></tr>
        <?php
            endwhile;
        ?>
    </tbody>
</table>