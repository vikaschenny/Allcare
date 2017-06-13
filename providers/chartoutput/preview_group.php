<?php
 // Copyright (C) 2011 Cassian LUP <cassi.lup@gmail.com>
 // 
 // Moved out of individual get_* portal functions for re-use by
 // Kevin Yeh (kevin.y@integralemr.com) May 2013
 //
 // This program is free software; you can redistribute it and/or
 // modify it under the terms of the GNU General Public License
 // as published by the Free Software Foundation; either version 2
 // of the License, or (at your option) any later version.
 // 
 // 
 
// All of the common intialization steps for the get_* patient portal functions are now in this single include.

//SANITIZE ALL ESCAPES
$sanitize_all_escapes=true;

//STOP FAKE REGISTER GLOBALS
$fake_register_globals=false;

//continue session
session_start();

//landing page definition -- where to go if something goes wrong
$landingpage = "index.php?site=".$_SESSION['site_id'];	
//

// kick out if patient not authenticated
//if ( isset($_SESSION['uid']) && isset($_SESSION['patient_portal_onsite']) ) {
if ( isset($_SESSION['portal_username']) ) {    
$provider = $_SESSION['portal_username'];
}
else {
        session_destroy();
header('Location: '.$landingpage.'&w');
        exit;
}
$ignoreAuth=true; // ignore the standard authentication for a regular OpenEMR user
include_once('../../interface/globals.php');

$enc=$_REQUEST['enc'];
$pid=$_REQUEST['pid'];
$date=$_REQUEST['date'];
$grpnam=$_REQUEST['grp'];

if($grpnam!='0'){
    $grp=substr($grpnam,1);
    $mobile_sql=sqlStatement("SELECT * 
                            FROM  `tbl_chartui_mapping` 
                            WHERE form_id =  'CHARTOUTPUT'
                            AND group_name LIKE  '%$grp%'
                            AND screen_name LIKE  '%$grp%'");
    while($mob_row1=sqlFetchArray($mobile_sql)){
      $field_id='form_'.$mob_row1['field_id'];
      $field_value=$mob_row1['option_value']; 
      $res.=$field_id.'='.$field_value.'&'; 
    } 
?>
<script>
    var datastring='<?php echo $res; ?>'+'patientid'+'='+<?php echo $pid; ?>+'&'+'encounter_id'+'='+<?php echo $enc; ?>+'&'+'dos'+'='+'<?php echo $date; ?>'+'&'+'chartgroupshidden'+'='+'<?php echo $grpnam; ?>'
     window.open('preview_charts.php?'+datastring,'popup','width=900,height=900,scrollbars=no,resizable=yes');
     parent.$.fancybox.close();
</script>   
<?php } else {
    $groups = sqlStatement("SELECT DISTINCT(group_name ) as group_name FROM layout_options " .
"WHERE form_id = 'CHARTOUTPUT' AND uor > 0 " .
"ORDER BY group_name");
?>
<html>
    <head>
        <script type="text/javascript" src="../../../library/js/jquery.1.3.2.js"></script>
    </head>
    <body style="background-color:#FFFFCC;">
        <div id='chartdiv' >
            <br>
            <form action="" method="post">
            <label>Group: </label>    
            <select id ="chartgroups"  name="chartgroups" onchange="this.form.submit()">
                <option value=""> Select </option>
                <?php 
                while ($groups2 = sqlFetchArray($groups)) {
                    echo "<option value =".$groups2['group_name'].">".substr($groups2['group_name'],1). "</option>";
                }

                ?>
            </select>
            <input type="hidden" name="enc" id="enc" value="<?php echo $enc; ?>" />
            <input type="hidden" name="pid" id="pid" value="<?php echo $pid; ?>" />
            <input type="hidden" name="date" id="date" value="<?php echo $date; ?>" />
            </form>
        </div>
    </body>
</html>
    <?php
    if($_POST['chartgroups']!=''){
        $grp=substr($_POST['chartgroups'],1);
        $mobile_sql=sqlStatement("SELECT * 
                                FROM  `tbl_chartui_mapping` 
                                WHERE form_id =  'CHARTOUTPUT'
                                AND group_name LIKE  '%$grp%'
                                AND screen_name LIKE  '%$grp%'");
        while($mob_row1=sqlFetchArray($mobile_sql)){
          $field_id='form_'.$mob_row1['field_id'];
          $field_value=$mob_row1['option_value']; 
          $res.=$field_id.'='.$field_value.'&'; 
        } 
    ?>
    <script>
        var datastring='<?php echo $res; ?>'+'patientid'+'='+<?php echo $pid; ?>+'&'+'encounter_id'+'='+<?php echo $enc; ?>+'&'+'dos'+'='+'<?php echo $date; ?>'+'&'+'chartgroupshidden'+'='+'<?php echo $_POST['chartgroups']; ?>'
         window.open('preview_charts.php?'+datastring,'popup','width=900,height=900,scrollbars=no,resizable=yes');
         parent.$.fancybox.close();
    </script>   
    <?php } 
} ?>