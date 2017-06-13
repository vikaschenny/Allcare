<?php

require_once("../globals.php");
require_once("$srcdir/acl.inc");
require_once("$srcdir/formdata.inc.php");
require_once("$srcdir/globals.inc.php");
require_once("$srcdir/user.inc");
require_once("$srcdir/classes/CouchDB.class.php");

$table_name=$_POST['table_name'];

$GR_name=($table_name=='tbl_allcare_insurance1to1_fieldmapping') ? 'Grouping_Name' : 'Recordset_Name' ;
$buttonLabel=($table_name=='tbl_allcare_insurance1to1_fieldmapping') ? 'New Group' : 'New Recordset' ;
$lblHide=($table_name=='tbl_allcare_insurance1to1_fieldmapping') ? 'insurance_lblRecordset' : 'insurance_lblGroup';
$lblShow=($table_name=='tbl_allcare_insurance1to1_fieldmapping') ? 'insurance_lblGroup' : 'insurance_lblRecordset';

$postyperes = sqlStatement("SELECT DISTINCT $GR_name AS GR_name FROM $table_name ORDER BY $GR_name");

echo "<select id='insurance_comboGroupsRecordsets' name='insurance_comboGroupsRecordsets' 
              onchange=\"javascript:jQuery('#insurance_lblFieldsSelected').show(); 
                                    insurance_showFieldsByGroupRecordsets('".$table_name."');
                                    insurance_hideNewButtonData();
                                    jQuery('#td_Show_YesNo').show();
                                    jQuery('#insurance_lblFieldsSelected').show();   \">
      <option value='none'>none</option>";
while($allcarerows = sqlFetchArray($postyperes))
{                 
    echo "<option value='".$allcarerows['GR_name']."'>".$allcarerows['GR_name']."</option>";        
}

echo "</select>   
<input type='button' id='insurance_btnNewGR' name='insurance_btnNewGR' value='$buttonLabel' 
       onclick='javascript: jQuery(\"#$lblShow\").show();
                            jQuery(\"#$lblHide\").hide();                               
                            jQuery(\"#insurance_txtGroupRecordset\").show();  
                            jQuery(\"#insurance_lblExistingFields\").show();
                            jQuery(\"#insurance_lblFieldsSelected\").hide();   
                            jQuery(\"#insurance_showFieldsByGroupRecordsets\").html(\"\"); 
                            jQuery(\"#insurance_comboGroupsRecordsets\").val(\"none\"); 
                            jQuery(\"#td_Show_YesNo\").show();
                            insurance_showFields();' />";

?>
