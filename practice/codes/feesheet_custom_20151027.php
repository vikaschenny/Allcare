<?php
//SANITIZE ALL ESCAPES
$sanitize_all_escapes=true;

//STOP FAKE REGISTER GLOBALS
$fake_register_globals=false;

//continue session
session_start();

//landing page definition -- where to go if something goes wrong
$landingpage = "index.php?site=".$_SESSION['site_id']; 

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
//

$ignoreAuth=true; // ignore the standard authentication for a regular OpenEMR user
include_once('../../interface/globals.php');
include_once("$srcdir/api.inc");
require_once("$srcdir/patient.inc");
require_once("$srcdir/options.inc.php");
require_once("$srcdir/htmlspecialchars.inc.php");
include_once("$srcdir/acl.inc");
formHeader("Form:Codes");


?>
<html>
<head> 
<?php html_header_show();?>
<meta content="width=device-width,initial-scale=1.0" name="viewport">
<script type="text/javascript" src="../../../library/dialog.js"></script>
<!-- pop up calendar -->
<style type="text/css">@import url(<?php echo $GLOBALS['webroot'] ?>/library/dynarch_calendar.css);</style>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/dynarch_calendar.js"></script>
<?php include_once("{$GLOBALS['srcdir']}/dynarch_calendar_en.inc.php"); ?>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/dynarch_calendar_setup.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/textformat.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/dialog.js"></script>
<link rel="stylesheet" href="<?php echo $css_header;?>" type="text/css">
<link rel="stylesheet" href="../css/gh-buttons.css">
<link rel="stylesheet" href="../css/font-awesome.min.css">
<link rel="stylesheet" href="../css/customize.css" type="text/css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js">
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/jquery.1.3.2.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/common.js"></script>
<!--<script type="text/javascript" src="../js/jquery.nicescroll.min.js"></script>-->
<script type="text/javascript">

    $(document).ready(function() {
        var encounterid = '<?php echo $_REQUEST['encounter']; ?>';
         var pid = '<?php echo $_REQUEST['pid']; ?>';
        jQuery.ajax({
            type: 'POST',
            url: "check_billed.php",
            dataType : "json",
            data: {
                    encounterid : encounterid,
                    pid : pid
                },

            success: function(data)
            {
                var stringified = '';
                stringified = JSON.stringify(data, undefined, 2);
                var objectified = jQuery.parseJSON(stringified);
//                    alert(stringified);
                for(var key in objectified ){
                    if(objectified[key] == 1){
                         jQuery("#my_form :input").attr("disabled", true);
                         jQuery("#display_field").show();
                    }
                }
            },
            failure: function(response)
            {
                alert("error");
            }		
        });
        jQuery('#deleteicd').hide();
        if(jQuery('#cpt_table').length >0){
            jQuery('#deletecpt').show();
        }else{
            jQuery('#deletecpt').hide();
        }
        $('#icd_search_button').click(function(){
            jQuery.ajax({
                type: 'POST',
                url: "codes_search.php",
                dataType : "json",
                data: {
                        code_type : 'ICD10',
                        searchstring : jQuery('#icd_search').val()
                    },

                success: function(data)
                {
                    var stringified = '';
                    jQuery('#icd_dropdown').empty();
                    jQuery('#icd_dropdown').append(jQuery('<option>', { 
                            value: '',
                            text : 'Select'
                        }));
                    stringified = JSON.stringify(data, undefined, 2);
                    var objectified = jQuery.parseJSON(stringified);
                    for(var key in objectified ){
                        jQuery('#icd_dropdown').append(jQuery('<option>', { 
                            value: key,
                            text : key+" - "+objectified[key]
                        }));
                    }
                },
                failure: function(response)
                {
                    alert("error");
                }		
            });
        });
        jQuery("#icd_dropdown").change(function() {
            if(jQuery("#icd_dropdown option:selected").val() !== ''){
                var table = document.getElementById('justify');

                var rowCount = table.rows.length;
                var row = table.insertRow(rowCount);

                var cell01 = row.insertCell(0);

                var element01 = document.createElement("input");
                element01.type = "checkbox";
                element01.id = "icddelete";
                element01.name="icddelete[]";
                cell01.appendChild(element01);


                var cell1 = row.insertCell(1);

                var element1 = document.createElement("input");
                element1.type = "radio";
                element1.name="icd_primary";
                element1.value = "ICD10:"+jQuery("#icd_dropdown option:selected").val();
                cell1.appendChild(element1);

                var element11 = document.createTextNode('Primary');
                cell1.appendChild (element11);

                var cell2 = row.insertCell(2);

                var element2 = document.createElement("input");
                element2.type = "checkbox";
                element2.value = "ICD10:"+jQuery("#icd_dropdown option:selected").val();
                element2.name="icd_justify[]";
                cell2.appendChild(element2);

                var element21 = document.createTextNode('Justify');
                cell2.appendChild (element21);

                var cell3 = row.insertCell(3);

                var element3 = document.createElement("input");
                element3.type = "checkbox";
                element3.checked = true;
                element3.value = "ICD10:"+jQuery("#icd_dropdown option:selected").val();
                element3.name="icd_mproblem[]";
                element3.innerHTML='Active';
                cell3.appendChild(element3);

                var element31 = document.createTextNode('Active');
                cell3.appendChild (element31);

                var cell4 = row.insertCell(4);
                var element4 = document.createTextNode("|ICD10:" + jQuery("#icd_dropdown option:selected").text());
                cell4.appendChild(element4);
                jQuery("#icd_values").val(jQuery("#icd_values").val()  +","+ jQuery("#icd_dropdown option:selected").val());
                if(jQuery("#icddelete").length > 0)
                    jQuery('#deleteicd').show();
                else
                    jQuery('#deleteicd').hide();
            }
         });
         jQuery("#cptlistbutton").click(function() {
            var selectValues = new Array();
            jQuery('#cpt_div').find('select option:selected').each(function() {
                selectValues.push(jQuery(this).val());
            });
            
            jQuery.ajax({
                type: 'POST',
                url: "cpt_values.php",
                dataType : "json",
                data: {
                        selectValues : selectValues
                    },

                success: function(data)
                {
                    var stringified = '';
                    stringified = JSON.stringify(data, undefined, 2);
                    var objectified = jQuery.parseJSON(stringified);
                    
                    var table = document.getElementById('cpt_table');
                    

                    
                    for(var key in objectified ){
                        var rowCount = table.rows.length;
                        var row = table.insertRow(rowCount);
                        var cell1 = row.insertCell(0);
                        
                        var element1 = document.createElement("input");
                        element1.type = "checkbox";
                        element1.name="cpt_delete[]";
                        element1.val= key;
                        cell1.appendChild(element1);

                        var element2 = document.createTextNode(key+"-"+ objectified[key] );
                        cell1.appendChild(element2);
                        var keystring = jQuery("#cpt_values").val()+","+key;
                        jQuery("#cpt_values").val(keystring);
                        if(jQuery("#cptdiv input:checkbox").length == 0)
                            jQuery('#deletecpt').hide();
                        else
                            jQuery('#deletecpt').show();
                    }    
                    
                },
                failure: function(response)
                {
                    alert("error");
                }		
            });

        var isTouchDevice = 'ontouchstart' in document.documentElement;
        });
    });
</script>
  <SCRIPT language="javascript">
      function cleardropdown(value) {
            jQuery('#'+value+'_dropdown').empty();
            if(value === 'icd'){
                jQuery('#'+value+'_dropdown').each(function() {
                    var option = $("<option />");
                    option.attr("value", ' ').text('Select');
                    jQuery('#'+value+'_dropdown').append(option);
                });
            }else{
                var encounterid = '<?php echo $_REQUEST['encounter']; ?>';
                jQuery("#"+jQuery.trim(value)+ " option:selected").removeAttr("selected");
            }
        }
        function deletefields(value) {
            jQuery("#"+value+"div input:checked").each(function() {
                if(value === 'icd'){
                    var  str = jQuery(this).attr('name');
                    if(jQuery("#"+str+" input:checked")  && str.indexOf('icddelete') !== -1){
                        var string =  jQuery(this).closest("tr").find('td:eq(4)').text() ;
                        var string2 = string.replace("|ICD10:","");
                        var string3 = string2.split('-');
                        var string4 =  jQuery.trim(string3[0]);

                        jQuery(this).closest('tr').remove();
                        var getstring = jQuery("#icd_values").val();
                        var setstring = getstring.replace(string4,'');
                        jQuery("#icd_values").val(setstring);
                        if(jQuery("#icddelete").length > 0)
                            jQuery('#deleteicd').show();
                        else
                            jQuery('#deleteicd').hide();
                    }
                }else{
                    if(jQuery("#"+value+"div input:checkbox").length == 1)
                        jQuery('#deletecpt').hide();
                    else
                        jQuery('#deletecpt').show();
                    var string =  jQuery(this).closest("tr").find('td:eq(0)').text() ;
                    var stringid = 0;
                    stringid = jQuery(this).closest("tr").find('input[type=checkbox]').val();
//                    var string2 = string.replace("|ICD10:","");
                    var string3 = string.split('-');
                    var deletestring =  jQuery.trim(string3[0]);
//                       alert(stringid);
                    jQuery(this).closest('tr').remove();
                    var getstring = jQuery("#cpt_values").val();
//                    alert(setstring+"=="+getstring);
                    var setstring = getstring.replace(deletestring,'');
                    if(stringid !== ''){
                        var encounterid = '<?php echo $_REQUEST['encounter']; ?>';
                        var pid = '<?php echo $_REQUEST['pid']; ?>';
                        jQuery.ajax({
                            type: 'POST',
                            url: "delete_cpt.php",
                            dataType : "json",
                            data: {
                                deletestring : deletestring,
                                encounterid : encounterid,
                                pid : pid,
                                stringid : stringid
                            },

                            success: function(data)
                            {
                                alert("Deleted CPT from Feesheet");
                            },
                            failure: function(response)
                            {
                                alert("error");
                            }		
                        });
                    }
//                        alert(setstring);
                    jQuery("#cpt_values").val(setstring);
                }
            });
        }
        function changesmodifier(textvalue){
             jQuery.ajax({
                type: 'POST',
                url: "update_modifier.php",
                dataType : "json",
                data: {
                    id : textvalue,
                    value: jQuery('#cpt_modifier_'+textvalue +" option:selected").val()
                },

                success: function(data)
                {
                    alert("Updated Modifier");
                },
                failure: function(response)
                {
                    alert("error");
                }		
            });
        }
    </SCRIPT>
    
</head>
<body class="body_top">
    <a id="save_btnup" class="button-css button fa fa-floppy-o" onclick="my_form.submit()"> Save</a>
    <p><span class="forms-title"><b><?php echo xlt('Codes'); ?></b></span></p>
    <div id='display_field' name='display_field' style="display:none; color:green;text-align: center;"> This encounter has been billed. If you need to change it, it must be re-opened. </div>
<?php
echo "<form method='post' name='my_form' id='my_form'" .
  "action='save_codes.php?id=" . attr($formid) ."'>\n";
    ?>
    <div id="cpt_div">
        <b>CPT: </b>
        <input type='hidden' name='pid' value='<?php echo $_REQUEST['pid']; ?>'>
        <input type='hidden' name='encounter' value='<?php echo $_REQUEST['encounter']; ?>'>
        <input type='hidden' name='user' value='<?php echo $_SESSION['portal_username']; ?>'>
        <?php 
            $getcptlist = sqlStatement("select option_id,title from list_options where list_id  = 'Allcare_Visit_Code_Group_List' order by seq");
            $cptlist = array();
            if(!empty($getcptlist)){
                while($setcptlist = sqlFetchArray($getcptlist)){
                    $cptlist[$setcptlist['option_id']] = $setcptlist['title'];
                }
            }
            $getcptmodifer = sqlStatement("select option_id,title from list_options where list_id  = 'CPT_Modifiers' order by seq");
            $getcptmodiferlist = array();
            if(!empty($getcptmodifer)){
                while($setcptmodifer = sqlFetchArray($getcptmodifer)){
                    $getcptmodiferlist[$setcptmodifer['option_id']] = $setcptmodifer['title'];
                }
            }
            $encounterid = $_REQUEST['encounter'];
            $pid         = $_REQUEST['pid'];

            $getfuv = sqlStatement("select facility_id,pc_catid from form_encounter where encounter = $encounterid");
            $fuvrow = sqlFetchArray($getfuv);
            if(!empty($fuvrow)){
                $facility_id    = $fuvrow['facility_id'];
                $pc_catid       = $fuvrow['pc_catid'];
            }
            $getquery = sqlStatement("SELECT fo.fs_option, vc.code_options,fo.fs_codes FROM fee_sheet_options fo
                INNER JOIN tbl_allcare_vistcat_codegrp vc ON vc.code_groups = fo.fs_category  
                WHERE `facility` = $facility_id AND `visit_category` = $pc_catid AND vc.code_options REGEXP (fo.fs_option)");
            $array = array();
            while($setquery = sqlFetchArray($getquery)){
                $codes = $setquery['fs_codes'];
                $codesarray = explode('~',str_replace("CPT4","",str_replace("|","",$codes) ));
                for($i=0; $i< count($codesarray); $i++){
                    $getcodes = sqlStatement("SELECT code_text FROM codes WHERE code = '".$codesarray[$i]."'");
                    $setcodes = sqlFetchArray($getcodes);
                    if(!empty($setcodes)){
                        $getcpts[$codesarray[$i]]= $setcodes['code_text'];
                    }
                }

            }
        ?><br>
        <?php
        foreach($cptlist as $cptkey => $cptvalue){
                $explodedstring = array();
                $explodedstring = explode(",",$cptvalue);
                $stringarray = count($explodedstring);
                echo "<b>".ucwords($cptkey).":</b>";
                $cptkey1 = str_replace('/',"",$cptkey);
                echo "<select id= 'cpt_$cptkey1' name = 'cpt_$cptkey1' size='4' multiple style='display:none;'>";
                for($i=0; $i< $stringarray; $i++){
                    $getcptvalues = sqlStatement("SELECT fo.fs_codes FROM fee_sheet_options fo
                        INNER JOIN tbl_allcare_vistcat_codegrp vc ON vc.code_groups = fo.fs_category  
                        WHERE `facility` = $facility_id AND `visit_category` = $pc_catid AND  vc.code_groups = '$explodedstring[$i]' AND vc.code_options REGEXP (fo.fs_option)");
                    if(!empty($getcptvalues)){
                        while($setcptvalues = sqlFetchArray($getcptvalues)){
                            $explodedcpt = array();
                            $cptval = $setcptvalues['fs_codes'];
                            if(strpos($cptval,"~") !== false){
                                $explodedcpt = explode("~",str_replace("|","",str_replace("CPT4|","",$cptval)));
                            }else{
                                $explodedcpt[0] = str_replace("|","",str_replace("CPT4","",$cptval));
                            }
                            for($j=0; $j< count($explodedcpt); $j++){
                                echo "<option value='".$explodedcpt[$j]."'>".$explodedcpt[$j]."</option>";
                            }
                        }
                    }
                }

                echo "</select>";
                echo "<span id= 'cpt".$cptkey1."span' style='display:none;font-size: 70%;'>No related Codes Mapped</span>"
                ?><a class="button danger icon remove" style='display:none' id="cpt<?php echo $cptkey1; ?>btnclear" onclick="cleardropdown('cpt_<?php echo $cptkey1; ?>')">Clear Dropdown</a><!--<input type="button" id="cptbtnclear" onclick="cleardropdown('cpt')" value="Clear Dropdown" />--> 
                <script>
                    if(jQuery('#cpt_<?php echo $cptkey1; ?>').children('option').length >0){
                        jQuery('#cpt_<?php echo $cptkey1; ?>').show();
                        jQuery('#cpt<?php echo $cptkey1; ?>btnclear').show();
                    }else{
                        jQuery('#cpt<?php echo $cptkey1; ?>span').show();
                    }
                </script>
                <?php
                echo "<br />";
            }
        ?>
        <input type='button' value="submit" id="cptlistbutton">
            <?php
            foreach($getcpts as $cpt_key => $cpt_value)
                $sql2 = sqlStatement("SELECT b.id,b.code, b.code_text,b.modifier
                    FROM billing b 
                    INNER JOIN form_encounter f ON b.encounter = f.encounter  
                    WHERE b.encounter =   $encounterid and code_type='CPT4' and b.activity = 1 order by b.date desc ");
            ?>
        <br><br>
        <div style="border: 1px solid black;overflow: none" id="cptdiv">
            <a class="button danger icon trash" id="deletecpt" onclick="deletefields('cpt')">Delete</a>
        <table id="cpt_table" name="cpt_table" border="0"  > <?php 
                while($setsql = sqlFetchArray($sql2)){
                    echo "<tr><td><p><input type='checkbox' name='cpt_delete[]' value='".$setsql['id']."'><select  id='cpt_modifier_".trim($setsql['id'])."' placeholder='Modifier' onchange= changesmodifier('".$setsql['id']."');  ><option>Select</option>";
                    foreach($getcptmodiferlist as $mkey => $mvalue){
                        echo "<option value='$mkey' ";
                        if($mkey == $setsql['modifier']) 
                            echo " selected ";
                        echo ">". $mvalue." </option>";
                    }
                    echo "</select>";
                    echo $setsql['code']."-".$setsql['code_text'];
                    echo "</p><td></tr>";
                }
                ?> 

        </table>
        </div><br>
        <?php
        $providerid = 0;
        $selectquery = sqlStatement("SELECT (select group_concat(justify) from billing WHERE encounter =   $encounterid and code_type='CPT4' and activity = 1) as justify, b.notecodes, b.code_text,f.provider_id as rendering_providerid, (SELECT  CONCAT( fname,  ' ', lname ) FROM users where id = f.provider_id)  AS rendering_ProviderName
                        FROM billing b 
                        INNER JOIN form_encounter f ON b.encounter = f.encounter  
                        WHERE b.encounter =   $encounterid and code_type='CPT4' and b.activity = 1 order by b.date desc ");
        if(!empty($selectquery)){
            while($setquery = sqlFetchArray($selectquery)){
               $justify = $setquery['justify'];
               $providerid = $setquery['rendering_providerid'];
            }
        }
        $sql = sqlStatement("SELECT DISTINCT l.id, l.title AS Title, l.diagnosis AS Codes, if(SUBSTRING(l.diagnosis,1,4)='ICD9', (select long_desc from `icd9_dx_code` where l.diagnosis = CONCAT( 'ICD9:', formatted_dx_code ) and active = 1), (select long_desc from `icd10_dx_order_code` where l.diagnosis = CONCAT( 'ICD10:', formatted_dx_code ) and active = 1)) as Description
                                FROM lists AS l
                                LEFT JOIN issue_encounter AS ie ON ie.list_id = l.id
                                AND ie.encounter =$encounterid

                                WHERE l.type =  'medical_problem' AND l.pid =$pid
                                AND ( ( l.begdate IS NULL ) OR (l.begdate IS NOT NULL  AND l.begdate <= NOW( )  ) ) AND (( l.enddate IS NULL ) OR ( l.enddate IS NOT NULL  AND l.enddate >= NOW( ) ))
                                ORDER BY ie.encounter DESC , l.id") ; 
        ?> 

        <input type="hidden" id= "noofrows" name="noofrows" value = "<?php if(mysql_num_rows($sql)== 0) echo 1; else echo mysql_num_rows($sql) ; ?>">
    </div>
    <br><br>
    <b>ICD: </b>
    <input type='textbox' name='icd_search' id='icd_search' ><a class="button icon search" id="icd_search_button" name='icd_search_button' >Search</a><!--<input type="button" id='icd_search_button' name='icd_search_button' value='Search'>--><br><br>
    <select id='icd_dropdown' name='icd_dropdown'>
        <option>       </option>
    </select><a class="button danger icon remove" id="icdbtnclear" onclick="cleardropdown('icd')">Clear Dropdown</a><!--<input type="button" id="icdbtnclear" onclick="cleardropdown('icd')" value="Clear Dropdown" />-->
    <br><br>
    <div style="border: 1px solid black;overflow: none" id='icddiv'>
        <a class="button danger icon trash" id="deleteicd" onclick="deletefields('icd')">Delete</a>
        <table id="justify" name="justify" border="0" > <?php 
                while($setsql = sqlFetchArray($sql)){
                    echo "<tr><td></td><td style='text-align:center;'>";
                    echo "<input type='radio' id='icd_primary' name='icd_primary' value='".$setsql['Codes']."'";
                    if(substr( $justify, 0, strlen($setsql['Codes']) )  === str_replace(':',"|",$setsql['Codes'])) 
                            echo " checked  ";
                    echo ">Primary</input>";
                    echo "</td><td style='text-align:center;'>";
                    echo "<input type='checkbox' name='icd_justify[]' id='icd_checkboxes' value ='".$setsql['Codes']."'";
                    echo ">Justify</input>";
                    echo "</td><td style='text-align:center;'>";
                    echo "<input type='checkbox' name='icd_mproblem[]' id='icd_checkboxes' checked= 'checked' value ='".$setsql['Codes']."'>Active</input>" ;
                    echo "</td><td><span>|";
                    echo $setsql['Codes']." - ".$setsql['Title'];
                    echo "</span></td>";
                    echo "</tr>";
                }
                ?> 

        </table>
    </div>
    <?php
 
        $get_providerName = sqlStatement( "SELECT  CONCAT( u.fname,  ' ', u.lname ) AS rendering_ProviderName, p.pc_aid AS rendering_providerid
                FROM form_encounter f 
                INNER JOIN openemr_postcalendar_events p ON  p.pc_eventDate = DATE_FORMAT( f.date,  '%Y-%m-%d' ) and p.pc_pid = f.pid 
                inner join users u on u.id = p.pc_aid 
                WHERE f.encounter =$encounterid and f.pid = $pid");
        while($setprovider = sqlFetchArray($get_providerName)){
            $providerid = $setprovider['rendering_providerid'];
            echo "<span id='provider'>Provider:".$setprovider['rendering_ProviderName']."</span><br>" ;
        }
//    }
    ?>
    <input type='hidden' name='providerid' value='<?php echo $providerid; ?>'>
    <input type="hidden" val='' name='cpt_values' id='cpt_values'>
    <input type="hidden" val='' name='icd_values' id='icd_values'>
    <!--<input type='submit'  value='<?php echo xlt('Save');?>' class="button-css button fa fa-hourglass-half">&nbsp;-->
    <a id="save_btndown" class="button-css button fa fa-floppy-o" onclick="my_form.submit()"> Save</a>
   
</form>
<?php
formFooter();
?>
