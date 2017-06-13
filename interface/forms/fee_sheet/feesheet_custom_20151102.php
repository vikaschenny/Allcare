<?php
// Copyright (C) 2005-2011 Rod Roark <rod@sunsetsystems.com>
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.

$fake_register_globals=false;
$sanitize_all_escapes=true;

require_once("../../globals.php");
require_once("$srcdir/acl.inc");
require_once("$srcdir/api.inc");
require_once("codes.php");
require_once("../../../custom/code_types.inc.php");
require_once("../../drugs/drugs.inc.php");
require_once("$srcdir/formatting.inc.php");
require_once("$srcdir/options.inc.php");
require_once("$srcdir/formdata.inc.php");
formHeader("Form:FeeSheet");


?>
<html>
<head> 
<?php html_header_show();?>
<script type="text/javascript" src="../../../../library/dialog.js"></script>
<!-- pop up calendar -->
<style type="text/css">@import url(<?php echo $GLOBALS['webroot'] ?>/library/dynarch_calendar.css);</style>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/dynarch_calendar.js"></script>
<?php include_once("{$GLOBALS['srcdir']}/dynarch_calendar_en.inc.php"); ?>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/dynarch_calendar_setup.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/textformat.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/dialog.js"></script>
<link rel="stylesheet" href="<?php echo $css_header;?>" type="text/css">
<link rel="stylesheet" href="../../css/gh-buttons.css">
<link rel="stylesheet" href="../../css/font-awesome.min.css.css">
<link rel="stylesheet" href="../../css/customize.css" type="text/css">
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js">
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/jquery.1.3.2.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/common.js"></script>

<script type="text/javascript">

//    jQuery( "#my_form" ).load(function() {alert("df");
//         var encounterid = '<?php echo $_REQUEST['encounter']; ?>';
//         var pid = '<?php echo $_REQUEST['pid']; ?>';
//         alert(encounterid);
//        jQuery.ajax({
//            type: 'POST',
//            url: "check_billed.php",
//            dataType : "json",
//            data: {
//                    encounterid : encounterid,
//                    pid : pid
//                },
//
//            success: function(data)
//            {
//                var stringified = '';
//                stringified = JSON.stringify(data, undefined, 2);
//                var objectified = jQuery.parseJSON(stringified);
////                    alert(stringified);
//                for(var key in objectified ){
//                    if(objectified[key] == 1){
//                        jQuery("#my_form").prop("disabled", true);
//                    }
//                }
//            },
//            failure: function(response)
//            {
//                alert("error");
//            }		
//        });
//    });
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
        jQuery('#deletecpt').hide();
        jQuery('#cpt_search_button').click(function(){
            jQuery.ajax({
                type: 'POST',
                url: "codes_search.php",
                dataType : "json",
                data: {
                        code_type : 'CPT',
                        searchstring : jQuery('#cpt_search').val()
                    },

                success: function(data)
                {
                    var stringified = '';
                    jQuery('#cpt_dropdown').empty();
                    jQuery('#cpt_dropdown').append(jQuery('<option>', { 
                            value: '',
                            text : 'Select'
                        }));
                    stringified = JSON.stringify(data, undefined, 2);
                    var objectified = jQuery.parseJSON(stringified);
//                    alert(stringified);
                    for(var key in objectified ){
                        $('#cpt_dropdown').append($('<option>', { 
                            value: key,
                            text : key+" "+objectified[key]
                        }));
                    }
                },
                failure: function(response)
                {
                    alert("error");
                }		
            });
        });
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
         jQuery("#cpt_dropdown").change(function() {
             if(jQuery("#cpt_dropdown option:selected").text() !== 'Select' && jQuery("#cpt_dropdown option:selected").text() !== ''){
                var table = document.getElementById('cpt_table');

                var rowCount = table.rows.length;
                var row = table.insertRow(rowCount);
    //            alert(row);
                var cell1 = row.insertCell(0);

                var element1 = document.createElement("input");
                element1.type = "checkbox";
                element1.name="cpt_delete[]";
                element1.val=jQuery("#cpt_dropdown").val();
                cell1.appendChild(element1);

                var element2 = document.createTextNode( jQuery("#cpt_dropdown option:selected").text());
                cell1.appendChild(element2);

                jQuery("#cpt_values").val(jQuery("#cpt_values").val()  +","+ jQuery("#cpt_dropdown option:selected").val());
                if(jQuery("#cptdiv input:checkbox").length == 0)
                    jQuery('#deletecpt').hide();
                else
                    jQuery('#deletecpt').show();
            }
//            alert(jQuery("#icd_dropdown option:selected").val());
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
//                alert(encounterid);
                jQuery.ajax({
                type: 'POST',
                url: "cpt_default.php",
                dataType : "json",
                data: {
                        encounterid : encounterid
                    },

                success: function(data)
                {
                    var stringified = '';
                    jQuery('#cpt_dropdown').empty();
                    jQuery('#cpt_dropdown').append(jQuery('<option>', { 
                            value: '',
                            text : 'Select'
                        }));
                    stringified = JSON.stringify(data, undefined, 2);
                    var objectified = jQuery.parseJSON(stringified);
//                    alert(stringified);
                    for(var key in objectified ){
                        $('#cpt_dropdown').append($('<option>', { 
                            value: key,
                            text : key+" "+objectified[key]
                        }));
                    }
                },
                failure: function(response)
                {
                    alert("error");
                }		
            });
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
//                    var string2 = string.replace("|ICD10:","");
                    var string3 = string.split(' ');
                    var string4 =  jQuery.trim(string3[0]);
//                       alert(setstring);
                    jQuery(this).closest('tr').remove();
                    var getstring = jQuery("#cpt_values").val();
                    var setstring = getstring.replace(string4,'');
//                        alert(setstring);
                    jQuery("#cpt_values").val(setstring);
                }
            });
        }
    </SCRIPT>
</head>
<body class="body_top">
    <p><span class="forms-title"><b><?php echo xlt('FeeSheet'); ?></b></span></p>
    <div id='display_field' name='display_field' style="display:none; color:green;text-align: center;"> This encounter has been billed. If you need to change it, it must be re-opened. </div>
<?php
echo "<form method='post' name='my_form' id='my_form'" .
  "action='save_codes.php?id=" . attr($formid) ."'>\n";
    ?>
    <b>CPT: </b>
    <input type='hidden' name='pid' value='<?php echo $_REQUEST['pid']; ?>'>
    <input type='hidden' name='encounter' value='<?php echo $_REQUEST['encounter']; ?>'>
    <input type='hidden' name='user' value='<?php echo $_SESSION['portal_username']; ?>'>
    
    <input type='textbox' name='cpt_search' value="" id='cpt_search' ><a class="button icon search" id="cpt_search_button" name='cpt_search_button' ><i class="fa fa-home fa-fw"></i>&nbsp; Search</a><!--<input type="button" id='cpt_search_button' name='cpt_search_button' value='Search'>--><br>
    <?php
        $encounterid = $_REQUEST['encounter'];
        $pid         = $_REQUEST['pid'];

        $getfuv = sqlStatement("select facility_id,pc_catid from form_encounter where encounter = $encounterid");
        $fuvrow = sqlFetchArray($getfuv);
        if(!empty($fuvrow)){
            $facility_id    = $fuvrow['facility_id'];
            $pc_catid       = $fuvrow['pc_catid'];
        }
//        $getcpts = sqlStatement("SELECT co.code AS code, co.code_text AS Description
//                            FROM fee_sheet_options fo
//                            INNER JOIN tbl_allcare_vistcat_codegrp vc ON vc.code_groups = fo.fs_category
//                            INNER JOIN codes co ON SUBSTRING( fo.fs_codes, 6, LENGTH( fo.fs_codes ) -6 ) = co.code
//                            WHERE  `facility` = $facility_id
//                            AND  `visit_category` = $pc_catid
//                            AND vc.code_options REGEXP (fo.fs_option)");
        $getquery = sqlStatement("SELECT fo.fs_option, vc.code_options,fo.fs_codes FROM fee_sheet_options fo INNER JOIN tbl_allcare_vistcat_codegrp vc ON vc.code_groups = fo.fs_category  WHERE `facility` = 3 AND `visit_category` = 17 AND vc.code_options REGEXP (fo.fs_option)");
        $array = array();
        while($setquery = sqlFetchArray($getquery)){
            $codes = $setquery['fs_codes'];
            $codesarray = explode('~',str_replace("CPT4","",str_replace("|","",$codes) ));
            for($i=0; $i< count($codesarray); $i++){
        //        echo "SELECT code_text FROM codes WHERE code = '".$codesarray[$i]."'";
                $getcodes = sqlStatement("SELECT code_text FROM codes WHERE code = '".$codesarray[$i]."'");
                $setcodes = sqlFetchArray($getcodes);
                if(!empty($setcodes)){
                    $getcpts[$codesarray[$i]]= $setcodes['code_text'];
                }
            }

        }
    ?><br>
    
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <select id='cpt_dropdown' name='cpt_dropdown'>
        <option>Select</option>
        <?php
        foreach($getcpts as $cpt_key => $cpt_value)
//            echo "<option value ='".$setcpts['code']."'>".$setcpts['code']." ".$setcpts['Description']."</option>";
            echo "<option value ='".$cpt_key."'>".$cpt_key." ".$cpt_value."</option>";
        $sql2 = sqlStatement("SELECT b.code, b.code_text
                FROM billing b 
                INNER JOIN form_encounter f ON b.encounter = f.encounter  
                WHERE b.encounter =   $encounterid and code_type='CPT4' and b.activity = 1 order by b.date desc ");
        ?>
    </select>&nbsp;&nbsp;<input type="button" id="cptbtnclear" onclick="cleardropdown('cpt')" value="Clear Dropdown" />
    <br><br>
    <div style="border: 1px solid black;overflow: none" id="cptdiv">
        <!--<input type="button" class="button danger icon trash" id="deletecpt" onclick="deletefields('cpt')" value="Delete" />-->
        <a class="button danger icon trash" id="deletecpt" onclick="deletefields('cpt')">Delete</a>
    <table id="cpt_table" name="cpt_table" border="0"  > <?php 
            while($setsql = sqlFetchArray($sql2)){
                echo "<tr><td><p>";
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
    
    <br><br>
    <b>ICD: </b>
    <input type='textbox' name='icd_search' id='icd_search' ><a class="button icon search" id="icd_search_button" name='icd_search_button' >Search</a><!--<input type="button" id='icd_search_button' name='icd_search_button' value='Search'>--><br><br>
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <select id='icd_dropdown' name='icd_dropdown'>
        <option>       </option>
    </select>&nbsp;&nbsp;<input type="button" id="icdbtnclear" onclick="cleardropdown('icd')" value="Clear Dropdown" />
    <br><br>
    <div style="border: 1px solid black;overflow: none" id='icddiv'>
        <a class="button danger icon trash" id="deleteicd" onclick="deletefields('icd')">Delete</a>
        <table id="justify" name="justify" border="0" > <?php 
                while($setsql = sqlFetchArray($sql)){
                    echo "<tr><td></td><td>";
                    echo "<input type='radio' id='icd_primary' name='icd_primary' value='".$setsql['Codes']."'";
                    if(substr( $justify, 0, strlen($setsql['Codes']) )  === str_replace(':',"|",$setsql['Codes'])) 
                            echo " checked  ";
                    echo ">Primary</input>";
                    echo "</td><td>";
                    echo "<input type='checkbox' name='icd_justify[]' id='icd_checkboxes' value ='".$setsql['Codes']."'";
//                    if(strpos( $justify, str_replace(':',"|",$setsql['Codes'])) !== false) 
//                            echo " checked ";
                    echo ">Justify</input>";
                    echo "</td><td>";
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
//    if(empty($selectquery)){ 
        
        $get_providerName = sqlStatement( "SELECT  CONCAT( u.fname,  ' ', u.lname ) AS rendering_ProviderName, p.pc_aid AS rendering_providerid
                FROM form_encounter f 
                INNER JOIN openemr_postcalendar_events p ON  p.pc_eventDate = DATE_FORMAT( f.date,  '%Y-%m-%d' ) and p.pc_pid = f.pid 
                inner join users u on u.id = p.pc_aid 
                WHERE f.encounter =$encounterid and f.pid = $pid");
        while($setprovider = sqlFetchArray($get_providerName)){
            $providerid = $setprovider['rendering_providerid'];
            echo "<span>Provider:".$setprovider['rendering_ProviderName']."</span><br>" ;
        }
//    }
    ?>
    <input type='hidden' name='providerid' value='<?php echo $providerid; ?>'>
    <input type="hidden" val='' name='cpt_values' id='cpt_values'>
    <input type="hidden" val='' name='icd_values' id='icd_values'>
    <input type='submit'  value='<?php echo xlt('Save');?>' class="button-css">&nbsp;
   
    <!--<input type='button'  value="Print" onclick="window.print()" class="button-css">&nbsp;-->
    
</form>
<?php
formFooter();
?>
