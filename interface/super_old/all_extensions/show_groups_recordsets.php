<?php

require_once("../../globals.php");
require_once("$srcdir/acl.inc");
require_once("$srcdir/formdata.inc.php");
require_once("$srcdir/globals.inc.php");
require_once("$srcdir/user.inc");
require_once("$srcdir/classes/CouchDB.class.php");

$extension_name=$_POST['extension_name'];

$table_name=$_POST['table_name'];

$GR_name=($table_name=='tbl_allcare_'.$extension_name.'1to1_fieldmapping') ? 'Grouping_Name' : 'Recordset_Name' ;
$buttonLabel=($table_name=='tbl_allcare_'.$extension_name.'1to1_fieldmapping') ? 'New Group' : 'New Recordset' ;
$lblHide=($table_name=='tbl_allcare_'.$extension_name.'1to1_fieldmapping') ? $extension_name.'_lblRecordset' : $extension_name.'_lblGroup';
$lblShow=($table_name=='tbl_allcare_'.$extension_name.'1to1_fieldmapping') ? $extension_name.'_lblGroup' : $extension_name.'_lblRecordset';

$postyperes = sqlStatement("SELECT DISTINCT $GR_name AS GR_name FROM $table_name ORDER BY $GR_name");

echo "<select id='".$extension_name."_comboGroupsRecordsets' name='".$extension_name."_comboGroupsRecordsets' 
              onchange=\"javascript:jQuery('#".$extension_name."_lblFieldsSelected').show(); 
                                    ".$extension_name."_showFieldsByGroupRecordsets('".$table_name."');
                                    ".$extension_name."_hideNewButtonData();
                                    jQuery('#td_Show_YesNo').show();
                                    jQuery('#".$extension_name."_lblFieldsSelected').show();   \">
      <option value='none'>none</option>";
while($allcarerows = sqlFetchArray($postyperes))
{                 
    echo "<option value='".$allcarerows['GR_name']."'>".$allcarerows['GR_name']."</option>";        
}

echo "</select>   
<input type='button' id='".$extension_name."_btnNewGR' name='".$extension_name."_btnNewGR' value='$buttonLabel' 
       onclick='javascript: jQuery(\"#$lblShow\").show();
                            jQuery(\"#$lblHide\").hide();                               
                            jQuery(\"#".$extension_name."_txtGroupRecordset\").show();  
                            jQuery(\"#".$extension_name."_lblExistingFields\").show();
                            jQuery(\"#".$extension_name."_lblFieldsSelected\").hide();   
                            jQuery(\"#".$extension_name."_showFieldsByGroupRecordsets\").html(\"\"); 
                            jQuery(\"#".$extension_name."_comboGroupsRecordsets\").val(\"none\"); 
                            jQuery(\"#td_Show_YesNo\").show();
                            ".$extension_name."_showFields();' />";

?>
