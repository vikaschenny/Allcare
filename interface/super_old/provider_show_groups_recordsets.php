<?php

require_once("../globals.php");
require_once("$srcdir/acl.inc");
require_once("$srcdir/formdata.inc.php");
require_once("$srcdir/globals.inc.php");
require_once("$srcdir/user.inc");
require_once("$srcdir/classes/CouchDB.class.php");

$table_name=$_POST['table_name'];

$GR_name=($table_name=='tbl_allcare_provider1to1_fieldmapping') ? 'Grouping_Name' : 'Recordset_Name' ;
$buttonLabel=($table_name=='tbl_allcare_provider1to1_fieldmapping') ? 'New Group' : 'New Recordset' ;
$lblHide=($table_name=='tbl_allcare_provider1to1_fieldmapping') ? 'provider_lblRecordset' : 'provider_lblGroup';
$lblShow=($table_name=='tbl_allcare_provider1to1_fieldmapping') ? 'provider_lblGroup' : 'provider_lblRecordset';

$postyperes = sqlStatement("SELECT DISTINCT $GR_name AS GR_name FROM $table_name ORDER BY $GR_name");

echo "<select id='provider_comboGroupsRecordsets' name='provider_comboGroupsRecordsets' 
              onchange=\"javascript:jQuery('#provider_lblFieldsSelected').show(); 
                                    provider_showFieldsByGroupRecordsets('".$table_name."');
                                    provider_hideNewButtonData();
                                    jQuery('#td_Show_YesNo').show();
                                    jQuery('#provider_lblFieldsSelected').show();   \">
      <option value='none'>none</option>";
while($allcarerows = sqlFetchArray($postyperes))
{
    echo "<option value='".$allcarerows['GR_name']."'>".$allcarerows['GR_name']."</option>";        
}

echo "</select>   
<input type='button' id='provider_btnNewGR' name='provider_btnNewGR' value='$buttonLabel' 
       onclick='javascript: jQuery(\"#$lblShow\").show();
                            jQuery(\"#$lblHide\").hide();                               
                            jQuery(\"#provider_txtGroupRecordset\").show();  
                            jQuery(\"#provider_lblExistingFields\").show();
                            jQuery(\"#provider_lblFieldsSelected\").hide();   
                            jQuery(\"#provider_showFieldsByGroupRecordsets\").html(\"\"); 
                            jQuery(\"#provider_comboGroupsRecordsets\").val(\"none\"); 
                            jQuery(\"#td_Show_YesNo\").show();
                            provider_showFields();' />";

?>
