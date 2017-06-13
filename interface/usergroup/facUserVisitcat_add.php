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
 //print_r($_POST['users']);
 $user1=$_POST['users'];
// echo $_POST['screens'];
 
// print_r($_POST['result_arr']);
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
        window.open('facUserVisitcat_edit.php?id='+id,'editwindow','width=600,height=700');
    }
    function loadGrid(){
        jQuery('#facuservisit').html('loading...');
        jQuery('#facuservisit').load('facUserVisitcat_add.php');
    }
    function del(id){
        if (confirm("Do you really want to delete this group") == true) {
            jQuery('#facuservisit').html('Please wait...');
            jQuery.ajax({
                type: 'POST',
                url: "facUserVisitcat_add.php",	
                data: {
                        deleteid:id,                        
                      },

                success: function(response)
                {
                   
                    jQuery('#facuservisit').html(response);
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
        $deleteQuery = "DELETE FROM tbl_allcare_facuservisit WHERE id = ".$_POST['deleteid'];
        sqlStatement($deleteQuery);
        $insertlogQuery = "INSERT INTO tbl_allcare_facuservisit_log (fuvid,userid,timestamp,action) VALUES (".$_POST['deleteid'].",'".$_SESSION['authUser']."',now(),'deleted')";
        sqlStatement($insertlogQuery);
    }
    
    $facilities = (count($_POST['facilities']) > 0)? serialize($_POST['facilities']) : "";
    $users      = (count($_POST['users']) > 0)? serialize($_POST['users']) : "";
    $visitcat   = (count($_POST['visitcat']) > 0)? serialize($_POST['visitcat']) : "" ;
    $medicalgroup   = (count($_POST['medicalgroup']) > 0)? $_POST['medicalgroup'] : "" ;
    //$screens    = (count($_POST['screens']) > 0)? serialize($_POST['screens']) : "";
    $screengroups=$_POST['screens'];
    //$groupname    = ($_POST['groupname'] != "")? $_POST['groupname'] : "";
    
    $screennames=(count($_POST['result_arr']) > 0)? serialize($_POST['result_arr']) : "";

    
    // For Form id
       $formidQuery = sqlStatement("SELECT DISTINCT form_id FROM layout_options WHERE group_name = '".$_POST['screens']."'");
       $link=sqlFetchArray($formidQuery);
            $formid2=$link['form_id'];
    
    if($facilities != "" && $users != "" && $visitcat != "" && $screengroups != "" && $screennames!="" ):
/* hema*/
    /*             
 $uni_user=sqlStatement("select users from tbl_allcare_facuservisit where screen_group='".$screengroups."' AND screen_names='".$screennames."'");
// echo "select users from tbl_allcare_facuservisit where screen_group='".$screengroups."'";
       $i=0;
      // echo sqlNumRows($uni_user);
       if(sqlNumRows($uni_user) > 0 ){
            while($row1=sqlFetchArray($uni_user)) {
                if(!empty($row1)){
                    $sel_user=unserialize($row1['users']);
                    if(sizeof($sel_user)>0){
                        $res= array_intersect($user1,$sel_user);
                        if(!empty($res)) { 
                            $exists="No"; 
                            if($i==0){ ?><script> alert(" User with  same Screen Group selection already exists"); </script><?php $i++; } 
                        }else {
                           $exists="Yes";
                        } 
                    }else{
                          $exists="Yes";
                    }
               } 
           } 
       }else {
           $exists="Yes";
       }*/
/* ======================================================     */


?>
<!--       <script> alert('<?php //echo $exists; ?>'); </script>-->
      <?php // $exists1=trim($exists,",");
      //echo "SELECT screen_group FROM tbl_allcare_facuservisit WHERE facilities='".$facilities."' AND users='".$users."' AND visit_categories='".$visitcat."' AND screen_group='".$screengroups."' AND screen_names='".$screennames."'";
//        $result = sqlStatement("SELECT screen_group FROM tbl_allcare_facuservisit WHERE facilities='".$facilities."' AND users='".$users."' AND visit_categories='".$visitcat."' AND screen_group='".$screengroups."' AND screen_names='".$screennames."'");
      $result = sqlStatement("SELECT screen_group FROM tbl_allcare_facuservisit WHERE facilities='".$facilities."' AND users='".$users."' AND visit_categories='".$visitcat."' AND screen_group='".$screengroups."' AND screen_names='".$screennames."'");
        $result1 = sqlStatement("SELECT screen_group FROM tbl_allcare_facuservisit WHERE screen_group = '".$groupname."'");
        //sqlNumRows($result)."==".sqlNumRows($result1);
        if(sqlNumRows($result) > 0 ):
            ?><script> alert("Screen Group with same selection already exists"); </script>
            <?php
        elseif(sqlNumRows($result1) > 0):
           ?><script> alert("Screen Group with same name already exists"); </script>
            <?php 
        elseif(sqlNumRows($result) == 0 && sqlNumRows($result1) == 0):
           $insertQuery = "INSERT INTO tbl_allcare_facuservisit (screen_group,facilities,users,visit_categories,screen_names,form_id) VALUES('".$screengroups."','". $facilities ."','". $users ."','". $visitcat ."','". $screennames ."','".$formid2."')";
            
            $fuvid = sqlInsert($insertQuery);
            $insertlogQuery = "INSERT INTO tbl_allcare_facuservisit_log (fuvid,userid,timestamp,action) VALUES (".$fuvid.",'".$_SESSION['authUser']."',now(),'created')";
            sqlStatement($insertlogQuery);
            ?><script> alert("New group created"); </script>
            <?php
        endif;
    else:
        echo "All fields are required.";
    endif;
    $selectFuvQuery = "SELECT * FROM tbl_allcare_facuservisit ORDER BY id DESC";
    $fuvRows = sqlStatement($selectFuvQuery);
?>

<table id="gridview" class="table table-striped table-bordered" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th>Screen Group</th>
            <th>Facilities</th>
            <th>Users</th>
            <th>Visit Categories</th>
            <th>Screen Names</th>
<!--            <th>Medical Groups</th>-->
            <th>Screen Links</th>
            <th>Edit/Delete</th>
        </tr>
    </thead>

    <tfoot>
        <tr>
            <th>Screen Group</th>
            <th>Facilities</th>
            <th>Users</th>
            <th>Visit Categories</th>
            <th>Screen Names</th>
<!--            <th>Medical Groups</th>-->
            <th>Screen Links</th>
            <th>Edit/Delete</th>
        </tr>
    </tfoot>

    <tbody>
        <?php
            while($row = sqlFetchArray($fuvRows)):
                $facilities = unserialize($row['facilities']);
                $implodefac = implode(",",$facilities);
                
                $users = unserialize($row['users']);
                $implodeuser = implode(",",$users);
                
                $categories = unserialize($row['visit_categories']);
                $implodecategories = implode(",",$categories);
                
                                
                /*$screenames = unserialize($row['screen_names']);
                $implodescreenames = implode(",",$screenames);*/
                
               /* $screenames=substr($row['screen_names'], 1);*/
                $screengroup=$row['screen_group'];  
                //$medicalgroup = $row['medicalgroup'];
                if($implodefac!=''){
                    $f_res = sqlStatement("select name from `facility` WHERE id IN (".$implodefac.")");
                    $f_arr = array();
                    for($i=0; $rowwww=sqlFetchArray($f_res); $i++) {
                      $f_arr[$i]=$rowwww['name'];
                    }
                }
                if($implodeuser!=''){
                    $f_res2 = sqlStatement("select fname,mname,lname from `users` WHERE id IN (".$implodeuser.")");
                    $f_arr2 = array();
                    for($i=0; $rowwww=sqlFetchArray($f_res2); $i++) {
                      $f_arr2[$i] = $rowwww['fname']." ". $rowwww['mname'] . " " . $rowwww['lname'];
                    }
                }
                
                if($implodecategories!=''){
                    $f_res3 = sqlStatement("select pc_catname from `openemr_postcalendar_categories` WHERE pc_catid IN (".$implodecategories.")");
                    $f_arr3 = array();
                    for($i=0; $rowwww=sqlFetchArray($f_res3); $i++) {
                      $f_arr3[$i]=$rowwww['pc_catname'];
                    }
                }
                               
                
                //screen names
                $sample=array();
                $checkscreens = array();
                $screen_names1 =unserialize($row['screen_names']);
                $f_arr4 = array();
                //echo "<pre>"; print_r($screen_names1); echo"</pre>";
                if(!empty($screen_names1)){
                   foreach($screen_names1 as $val){
                        if (stripos($val, "Unused") == false) {
                            $val1=explode("$$",$val);
                            if($val1[0]!=''){
                                $checkscreens[$val1[0]]= $val1[2];
                                $sample[$val1[0]]=$val1[1]."$$".$val1[2];
                            }else{
                                //$checkscreens[$val1[2]."-".$val1[1]]= $val1[2];
                                $sample[$val1[2]."-".$val1[1]]=$val1[1]."$$".$val1[2];
                            }
                        }
                    }
                }
                
//                    for($i=0; $rowwww=sqlFetchArray($f_res4); $i++) {
//                        $t = $i+1;
//                        // $f_arr4[$i]=$t.".".$rowwww['title']."<br>";
//                        if($rowwww['description']!=='')
//                           $link = $rowwww['description'];
//                        else $link = "No Link";
//                        $f_arr5[$i]=$t.".".$link."<br>";
//                    }
             
              //echo "<pre>"; print_r($sample); echo"</pre>";
                ksort($sample);
                
                if(!empty($screen_names1)){
                    foreach($sample as $key=> $value){
                        if (stripos($value, "Unused") == false) {
                            $val3=explode("$$",$value);
                            if(is_numeric($key)){
                             $scr4 = sqlStatement("select title from `layout_options` WHERE field_id ='".$val3[1]."' and group_name LIKE  '%".$screengroup."%'");  
                             $scr_row=sqlFetchArray($scr4);
                             $f_arr4[$key]=$key.".".$scr_row['title']." "."<font size=2 color='red'>($val3[0])</font>". "<br>";
                            }else {
                             $f_arr4[$key]=$val3[1]." "."<font size=2 color='red'>($val3[0])</font>". "<br>";
                            }
                        }
                    }
                }
                $t = 1;
                $f_arr5 = array();
                ksort($checkscreens);
                foreach($checkscreens as $screenkey => $screencheck ){
                    $f_res4 = sqlStatement("select title, description from `layout_options` WHERE group_name LIKE '%".$screengroup."%' AND field_id = '$screencheck'");
                    while($rowwww=sqlFetchArray($f_res4)){
                        if($rowwww['description']!=='')
                           $link = $rowwww['description'];
                        else $link = "No Link";
                        $f_arr5[$screenkey]=$screenkey.".".$link."<br>";
                        $t++;
                    }
                }
               // $implodefac = implode(",",$facilities);
//                $f_res6 = sqlStatement("SELECT title from `layout_options` WHERE field_id  ='".$medicalgroup."' AND form_id='CHARTOUTPUT'");
//                $f_res7 = array();
//                for($i=0; $field=sqlFetchArray($f_res6); $i++) {
//                   $f_res7[$i]=$field['title'];
//                }
               
                   /* $f_res5 = sqlStatement('SELECT description from `layout_options` WHERE field_id  ='.$slink);
                    $f_arr5 = array();
                    for($i=0; $link=sqlFetchArray($f_res5); $i++) {
                        $t = $i+1;
                        $f_arr5[$i]=$t.".".$link['description']."<br>";
                    }*/
                
        ?>
        <tr><td><?php echo substr($screengroup,1); ?></td><td><?php echo implode(",",$f_arr); ?></td><td><?php echo implode(",",$f_arr2); ; ?></td><td><?php echo implode(",",$f_arr3); ?></td><td><?php echo implode('',$f_arr4); ?></td><td><?php echo implode('',$f_arr5); ?></td><td><a href="javascript:edit(<?php echo $row['id']; ?>)">Edit</a>/<a href="javascript:del(<?php echo $row['id']; ?>)">Delete</a></td></tr>
        <?php
            endwhile;
        ?>
    </tbody>
</table>