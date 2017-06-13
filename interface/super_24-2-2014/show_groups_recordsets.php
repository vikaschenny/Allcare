<?php

require_once("../globals.php");
require_once("$srcdir/acl.inc");
require_once("$srcdir/formdata.inc.php");
require_once("$srcdir/globals.inc.php");
require_once("$srcdir/user.inc");
require_once("$srcdir/classes/CouchDB.class.php");

$table_name=$_POST['table_name'];
$pos_id=$_POST['pos_id'];

$GR_name=($table_name=='tbl_allcare_patients1to1_fieldmapping') ? 'Grouping_Name' : 'Recordset_Name' ;
$buttonLabel=($table_name=='tbl_allcare_patients1to1_fieldmapping') ? 'New Group' : 'New Recordset' ;
$lblHide=($table_name=='tbl_allcare_patients1to1_fieldmapping') ? 'lblRecordset' : 'lblGroup';
$lblShow=($table_name=='tbl_allcare_patients1to1_fieldmapping') ? 'lblGroup' : 'lblRecordset';

$postyperes = sqlStatement("SELECT DISTINCT $GR_name AS GR_name FROM $table_name 
                            WHERE POS_id=$pos_id ORDER BY $GR_name");

echo "<select id='comboGroupsRecordsets' name='comboGroupsRecordsets' 
              onchange=\"javascript:jQuery('#lblFieldsSelected').show(); 
                                    showFieldsByGroupRecordsets('".$table_name."');
                                    hideNewButtonData();
                                    jQuery('#lblFieldsSelected').show();   \">
      <option value='none'>none</option>";
while($allcarerows = sqlFetchArray($postyperes))
{                 
    echo "<option value='".$allcarerows['GR_name']."'>".$allcarerows['GR_name']."</option>";        
}

echo "</select>   
<input type='button' id='btnNewGR' name='btnNewGR' value='$buttonLabel' 
       onclick='javascript: jQuery(\"#$lblShow\").show();
                            jQuery(\"#$lblHide\").hide();                               
                            jQuery(\"#txtGroupRecordset\").show();  
                            jQuery(\"#lblExistingFields\").show();
                            jQuery(\"#lblFieldsSelected\").hide();   
                            jQuery(\"#showFieldsByGroupRecordsets\").html(\"\"); 
                            jQuery(\"#comboGroupsRecordsets\").val(\"none\");  
                            showFields();' />";

?>
