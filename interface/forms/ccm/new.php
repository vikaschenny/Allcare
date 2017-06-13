<?php

//SANITIZE ALL ESCAPES
$sanitize_all_escapes=true;

//STOP FAKE REGISTER GLOBALS
$fake_register_globals=false;

include_once("../../globals.php");
include_once("$srcdir/api.inc");
require_once("$srcdir/patient.inc");
require_once("$srcdir/options.inc.php");
require_once("$srcdir/htmlspecialchars.inc.php");
include_once("$srcdir/acl.inc");

formHeader("Form:CCM Log");
$returnurl = $GLOBALS['concurrent_layout'] ? 'encounter_top.php' : 'patient_encounter.php';
$formid = 0 + (isset($_GET['id']) ? $_GET['id'] : '');
$obj = $formid ? formFetch("tbl_form_ccm", $formid) : array();
//$ccm_data = unserialize($obj{"ccm_data"});
$ccm_data2 = preg_replace_callback ( '!s:(\d+):"(.*?)";!',
    function($match) {
        return ($match[1] == strlen($match[2])) ? $match[0] : 's:' . strlen($match[2]) . ':"' . $match[2] . '";';
    },
$obj{"ccm_data"} );
    
$ccm_data = unserialize($ccm_data2);
//echo "<pre>"; print_r($ccm_data); echo "</pre>";

?>
<html>
<head> 
<?php html_header_show();?>
<script type="text/javascript" src="../../../library/dialog.js"></script>
<!-- pop up calendar -->
<style type="text/css">@import url(<?php echo $GLOBALS['webroot'] ?>/library/dynarch_calendar.css);</style>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/dynarch_calendar.js"></script>
<?php include_once("{$GLOBALS['srcdir']}/dynarch_calendar_en.inc.php"); ?>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/dynarch_calendar_setup.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/textformat.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/dialog.js"></script>
<link rel="stylesheet" href="<?php echo $css_header;?>" type="text/css">
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
<script language="javascript">
    function addRowCCM(tableID){
        var table=document.getElementById(tableID);
        var rowCount=table.rows.length;
        var row=table.insertRow(rowCount);
        var colCount=table.rows[0].cells.length;
        var hidvalue = document.getElementById("noofrows").value = rowCount;
        for(var i=0;i<colCount;i++){
            var newcell=row.insertCell(i);
            newcell.innerHTML=table.rows[1].cells[i].innerHTML;
            newcell.childNodes[0].name=(newcell.childNodes[0].name)+rowCount;
            if(newcell.childNodes[0].id == 'start_date'){
                newcell.childNodes[0].id=(newcell.childNodes[0].id)+rowCount;
                newcell.childNodes[1].id=(newcell.childNodes[1].id)+rowCount;   
                //alert(newcell.childNodes[1].id);
                Calendar.setup({inputField:"start_date"+rowCount, ifFormat:"%Y-%m-%d", button:"img_start_date"+rowCount});
            }
            if(newcell.childNodes[0].id == 'timeinterval' ){
                newcell.childNodes[0].id=(newcell.childNodes[0].id)+rowCount;
            }
            switch(newcell.childNodes[0].type){
                case "text":
                case "textarea":newcell.childNodes[0].value="";
                            break;
                case "checkbox":newcell.childNodes[0].checked=false;
                                break;
                case "select-one":newcell.childNodes[0].selectedIndex=0;
                                break;
            }
            
        }
        
    }
function deleteRow(tableID){
    try{
        var table=document.getElementById(tableID);
        var rowCount=table.rows.length;
        for(var i=0;i<rowCount;i++){
            var row=table.rows[i];
            var chkbox=row.cells[0].childNodes[0];
            if(null!=chkbox&&true==chkbox.checked){
                if(rowCount<=2){
                    alert("Cannot delete all the rows.");
                    break;
                }
                table.deleteRow(i);
                var count = rowCount--;
                document.getElementById('noofrows').value = count-2;
                i--;
            }
        }
    }catch(e){
        alert(e);
    }
}
$.noConflict();
jQuery(window).load(function(){
    var countrows = jQuery("#noofrows").val();
    if(countrows != ''){
        for(var i=1; i<=countrows; i++){
            if(i == 1){
                var countval = '';
            }else{
                var countval = i;
            }
            Calendar.setup({inputField:"start_date"+countval, ifFormat:"%Y-%m-%d", button:"img_start_date"+countval});
        }
    }
});
function addTimeCCM(){
    var countrows = jQuery("#noofrows").val();
    var timeval = 0;
    var timevalue = 0;
    if(countrows != ''){
        for(var i=1; i<=countrows; i++){
            if(i == 1){
                var countval = '';
            }else{
                var countval = i;
            }
            timevalue = jQuery("#timeinterval"+countval+ " option:selected").text();
            if(isNaN(timevalue)){
                timevalue = 0; 
            }
            timeval = parseInt(timeval) + parseInt(timevalue);
        }
        jQuery("#timespent").html(timeval);
        jQuery("#totaltime").val(timeval);
    }
}
function previewpostCCM(){
    var datastring = jQuery("#my_form").serialize();
    top.restoreSession();
    location.href = '../../forms/ccm/print.php?'+datastring;
}

</script>
 
</head>
<body class="body_top">
    <p><span class="forms-title"><b><?php echo xlt('CCM Log '); ?></b></span></p>
<?php
echo "<form method='post' name='my_form' id='my_form'" .
  "action='$rootdir/forms/ccm/save.php?id=" . attr($formid) ."'>\n";
echo xlt('Patient Name:' ); 
if (is_numeric($pid)) {
    $result = getPatientData($pid, "title,fname,lname,squad");
   echo "<b>".htmlspecialchars(text($result['title'])." ".text($result['fname'])." ".text($result['lname']))."</b>";
}
?><input type="hidden" name="patientname" value="<?php echo htmlspecialchars(text($result['title'])." ".text($result['fname'])." ".text($result['lname'])); ?>">
  <input type="hidden" name="pid" value="<?php echo $pid; ?>">
  <input type="hidden" name="encounter" value="<?php echo $_SESSION["encounter"]; ?>">
 <br><br>
 <input type="button" value="Add Row" onclick="addRowCCM('dataTable');">
 
    <input type="button" value="Delete Row" onclick="deleteRow('dataTable')">
    <br><br>
    <table id="dataTable" width="350px" border="1" style ="border-collapse: collapse;">
        <thead>
             <tr>
                <th></th>
                <th> Type of CCM Interaction </th>
                <th> Date</th>
                <th> Minutes </th>
                <th> Users </th>
                <th> Location </th>
                <th> Description </th>
                <th> Reference </th>
            </tr>
        </thead>
        <tbody>
           <?php 
            if($formid != 0):
                $timeinterval_val2 = 0;
                for($i=1; $i<=count($ccm_data); $i++){ 
                    if($i == 1):
                        $ccmtype     = "ccmtype";
                        $reference   = "reference";
                        $description = "description";
                        $start_date  = "start_date";
                        $img_start_date    = "img_start_date";
                        $timeinterval    = "timeinterval";
                        $location    = "location";
                        $users    = "users";
                    else:
                        $ccmtype     = "ccmtype".$i;
                        $reference   = "reference".$i;
                        $description = "description".$i;
                        $start_date  = "start_date".$i;
                        $img_start_date    = "img_start_date".$i;
                        $timeinterval    = "timeinterval".$i;
                        $location    = "location".$i;
                        $users    = "users".$i;
                    endif;
                    foreach ($ccm_data[$i-1] as $key => $value) {
                        ${$key."_val"} = $value;
                        if($key == 'timeinterval' && $value != ''):
                            $ures = sqlStatement("SELECT title FROM list_options WHERE option_id= '$value' and list_id = 'Time_Interval'");
                            while ($urow = sqlFetchArray($ures)) {
                                $title = $urow['title'];
                            }
                            if($title == ''):
                                $title = 0;
                            endif;
                            $timeinterval_val2 = $timeinterval_val2 + $title;
                        endif;
                    }
                ?>
               <tr>
                <td><input type="checkbox" name="chk"></td>
                <td><select class='select' name="<?php echo $ccmtype ; ?>" id="ccmtype" >
                        <option value="">---Select type---</option>
                        <?php 
                        $sql = sqlStatement ("SELECT * FROM list_options WHERE list_id = 'CCM_types'");
                        while ($row = mysql_fetch_array($sql)) {
                            echo "<option value = '".$row['option_id']."'";
                            if($ccmtype_val == $row['option_id'] ) echo "selected";
                            echo ">".$row['title']."</option>";
                        }
                        ?>
                    </select></td>
                <td><input type='text' size='10' name='<?php echo $start_date ; ?>' id='<?php echo $start_date ; ?>' <?php echo attr($disabled); ?>
                    value='<?php echo $start_date_val; ?>'   
                    title='<?php echo xla('yyyy-mm-dd Date of service'); ?>'
                    onkeyup='datekeyup(this,mypcc)' onblur='dateblur(this,mypcc)' /><img src='../../pic/show_calendar.gif' align='absbottom' width='24' height='22'
                    id='<?php echo $img_start_date ; ?>' border='0' alt='[?]' style='cursor:pointer;cursor:hand'
                    title='<?php echo xla('Click here to choose a date'); ?>'></td>
                <td><select class='select' name="<?php echo $timeinterval; ?>" id="<?php echo $timeinterval; ?>" onchange="addTimeCCM();" >
                        <option value="">Select Duration</option>
                        <?php 
                        $sql = sqlStatement ("SELECT * FROM list_options WHERE list_id = 'Time_Interval'");
                        while ($row = mysql_fetch_array($sql)) {
                            echo "<option value = '".$row['option_id']."'";
                            if($timeinterval_val == $row['option_id'] ) echo "selected";
                            echo ">".$row['title']."</option>";
                        }
                        ?>
                    </select></td>
                    <td><select class='select' name="<?php echo $users; ?>" id="<?php echo $users; ?>" >
                        <option value="">Select</option>
                        <?php 
                        $sql2 = sqlStatement ("SELECT id, CONCAT(fname,' ', lname) as name FROM users WHERE username <> '' AND  active= 1");
                        while ($row2 = mysql_fetch_array($sql2)) {
                            echo "<option value = '".$row2['id']."'";
                            if($users_val == $row2['id'] ) echo "selected";
                            echo ">".$row2['name']."</option>";
                        }
                        ?>
                    </select></td>    
                <td><textarea  name="<?php echo $location ; ?>" rows="4" cols="50"  ><?php echo $location_val; ?></textarea></td>    
                <td><textarea name="<?php echo $description ; ?>" rows="4" cols="50"  ><?php echo $description_val; ?></textarea></td>
                <td><textarea name="<?php echo $reference ; ?>" rows="4" cols="50" ><?php echo $reference_val; ?></textarea></td>
            </tr>
            <?php 
            } 
            else:
                ?>
                <tr>
                <td><input type="checkbox" name="chk"></td>
                <td><select class='select' name="ccmtype" id="ccmtype" >
                        <option value="">---Select type---</option>
                        <?php 
                        $sql = sqlStatement ("SELECT * FROM list_options WHERE list_id = 'CCM_types'");
                        while ($row = mysql_fetch_array($sql)) {
                            echo "<option value = '".$row['option_id']."'>".$row['title']."</option>";
                        }
                        ?>
                    </select></td>
                <td><input type='text' size='10' name='start_date' id='start_date' <?php echo attr($disabled); ?>
                    value=''   
                    title='<?php echo xla('yyyy-mm-dd Date of service'); ?>'
                    onkeyup='datekeyup(this,mypcc)' onblur='dateblur(this,mypcc)' /><img src='../../pic/show_calendar.gif' align='absbottom' width='24' height='22'
                    id='img_start_date' border='0' alt='[?]' style='cursor:pointer;cursor:hand'
                    title='<?php echo xla('Click here to choose a date'); ?>'></td>
                <td><select class='select' name="timeinterval" id="timeinterval" onchange="addTimeCCM();">
                        <option value="">Select Duration</option>
                        <?php 
                        $sql = sqlStatement ("SELECT * FROM list_options WHERE list_id = 'Time_Interval'");
                        while ($row = mysql_fetch_array($sql)) {
                            echo "<option value = '".$row['option_id']."'>".$row['title']."</option>";
                        }
                        ?>
                    </select></td>
                    <td><select class='select' name="users" id="users" >
                        <option value="">Select</option>
                        <?php 
                        $sql2 = sqlStatement ("SELECT id, CONCAT(fname,' ', lname) as name FROM users WHERE username <> '' AND  active= 1");
                        while ($row2 = mysql_fetch_array($sql2)) {
                            //echo "<option value = '".$row2['id']."'>".$row2['name']."</option>";
                            echo "<option value = '".$row2['id']."'";
                            if($_SESSION['authUserID'] == $row2['id'] ) echo "selected";
                            echo ">".$row2['name']."</option>";
                        }
                        ?>
                    </select></td>    
                <td><textarea  name="location" rows="4" cols="50"  ><?php echo $location_val; ?></textarea></td>    
                <td><textarea  name="description" rows="4" cols="50"  ><?php echo $description_val; ?></textarea></td>
                <td><textarea name="reference" rows="4" cols="50"  ><?php echo $reference_val; ?></textarea></td>
            </tr>
                <?php 
            endif; 
            ?>
    </tbody></table>
    <br><input type="hidden" name="totaltime" id="totaltime" value="<?php if($timeinterval_val2 == '') echo 0; else echo $timeinterval_val2; ?>" />
    <?php 
    echo "<table border = '0'";
    echo "<tr><td><b> Total: <span id='timespent'> ";if($timeinterval_val2 == '') echo 0; else echo $timeinterval_val2;echo " </span> minutes<b></td></tr>";
    $providercheck = sqlStatement("SELECT processproviders FROM tbl_user_custom_attr_1to1 WHERE userid= ".$_SESSION['authUserID']);
    while($providercheck2 = sqlFetchArray($providercheck)){
        $authcheck = $providercheck2['processproviders'];
    }
    if ($authcheck == 'YES') {
    echo "<tr><td width='70%'>";
    echo xlt('NP/Physician Signature:' ); 
    $ures = sqlStatement("SELECT id, fname, lname, specialty FROM users " .
      "WHERE active = 1 AND ( info IS NULL OR info NOT LIKE '%Inactive%' ) " .
      "AND authorized = 1 " .
      "ORDER BY lname, fname");
    echo "<select name='provider_id' id='provider_id' title='$description'>";
        echo "<option value=''>Unassigned</option>";
        while ($urow = sqlFetchArray($ures)) {
            echo $uname = htmlspecialchars( $urow['fname'] . ' ' . $urow['lname'], ENT_NOQUOTES);
            $optionId = htmlspecialchars( $urow['id'], ENT_QUOTES);
            echo "<option value='$optionId'";
            if ($urow['id'] == $obj{"provider_id"}) echo " selected";
            echo ">$uname</option>";
        }
    
    
    echo "</select>";
    echo"</td><td width='30%'>";
    echo xlt('Date:' ); 
    ?> 
    <input type='text' size='10' name='signed_date' id='signed_date' <?php echo attr($disabled); ?>;
        value='<?php echo attr($obj{"signed_date"}); ?>'   
        title='<?php echo xla('yyyy-mm-dd Date of service'); ?>'
        onkeyup='datekeyup(this,mypcc)' onblur='dateblur(this,mypcc)' />
        <img src='../../pic/show_calendar.gif' align='absbottom' width='24' height='22'
        id='img_signed_date' border='0' alt='[?]' style='cursor:pointer;cursor:hand'
        title='<?php echo xla('Click here to choose a date'); ?>'>
    <?php
    echo "</td><tr></table>";
    }else{
        ?>
        <input type="hidden" value="<?php echo $obj{"provider_id"}; ?>" name='provider_id' id='provider_id' />
        <input type='hidden' name='signed_date' id='signed_date' <?php echo attr($disabled); ?>;
        value='<?php echo attr($obj{"signed_date"}); ?>'   
        title='<?php echo xla('yyyy-mm-dd Date of service'); ?>'
        onkeyup='datekeyup(this,mypcc)' onblur='dateblur(this,mypcc)' />
    <?php 
    } 
    ?>
    <input type="hidden" id= "noofrows" name="noofrows" value = "<?php if($obj{"count"}== '') echo 1; else echo $obj{"count"} ; ?>">
    <input type='submit'  value='<?php echo xlt('Save');?>' class="button-css">&nbsp;
    <!--<input type='button'  value="Print" onclick="window.print()" class="button-css">&nbsp;-->
    <input type='button' class="button-css" value='<?php echo xlt('Cancel');?>'
        onclick="javascript:top.restoreSession();document.location.href='<?php echo "$rootdir/patient_file/encounter/$returnurl"; ?>'" />
    <br><br>
     <a href="javascript:;" <?php if (!$GLOBALS['concurrent_layout']) echo "target='Main'"; ?> class="css_button" onclick="previewpostCCM();">
                <span><?php echo htmlspecialchars( xl('Print'), ENT_NOQUOTES); ?></span>
</form>
<script language="javascript">
/* required for popup calendar */
Calendar.setup({inputField:"start_date", ifFormat:"%Y-%m-%d", button:"img_start_date"});
if (document.forms[0].signed_date){
    Calendar.setup({inputField:"signed_date", ifFormat:"%Y-%m-%d", button:"img_signed_date"});
}
</script>
<?php
formFooter();
?>