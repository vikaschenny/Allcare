<?php 
require_once("../globals.php");

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
