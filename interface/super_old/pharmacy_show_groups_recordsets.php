<?php

require_once("../globals.php");
require_once("$srcdir/acl.inc");
require_once("$srcdir/formdata.inc.php");
require_once("$srcdir/globals.inc.php");
require_once("$srcdir/user.inc");
require_once("$srcdir/classes/CouchDB.class.php");

$table_name=$_POST['table_name'];

$GR_name=($table_name=='tbl_allcare_pharmacy1to1_fieldmapping') ? 'Grouping_Name' : 'Recordset_Name' ;
$buttonLabel=($table_name=='tbl_allcare_pharmacy1to1_fieldmapping') ? 'New Group' : 'New Recordset' ;
$lblHide=($table_name=='tbl_allcare_pharmacy1to1_fieldmapping') ? 'pharmacy_lblRecordset' : 'pharmacy_lblGroup';
$lblShow=($table_name=='tbl_allcare_pharmacy1to1_fieldmapping') ? 'pharmacy_lblGroup' : 'pharmacy_lblRecordset';

$postyperes = sqlStatement("SELECT DISTINCT $GR_name AS GR_name FROM $table_name ORDER BY $GR_name");

echo "<select id='pharmacy_comboGroupsRecordsets' name='pharmacy_comboGroupsRecordsets' 
              onchange=\"javascript:jQuery('#pharmacy_lblFieldsSelected').show(); 
                                    pharmacy_showFieldsByGroupRecordsets('".$table_name."');
                                    pharmacy_hideNewButtonData();
                                    jQuery('#td_Show_YesNo').show();
                                    jQuery('#pharmacy_lblFieldsSelected').show();   \">
      <option value='none'>none</option>";
while($allcarerows = sqlFetchArray($postyperes))
{                 
    echo "<option value='".$allcarerows['GR_name']."'>".$allcarerows['GR_name']."</option>";        
}

echo "</select>   
<input type='button' id='pharmacy_btnNewGR' name='pharmacy_btnNewGR' value='$buttonLabel' 
       onclick='javascript: jQuery(\"#$lblShow\").show();
                            jQuery(\"#$lblHide\").hide();                               
                            jQuery(\"#pharmacy_txtGroupRecordset\").show();  
                            jQuery(\"#pharmacy_lblExistingFields\").show();
                            jQuery(\"#pharmacy_lblFieldsSelected\").hide();   
                            jQuery(\"#pharmacy_showFieldsByGroupRecordsets\").html(\"\"); 
                            jQuery(\"#pharmacy_comboGroupsRecordsets\").val(\"none\"); 
                            jQuery(\"#td_Show_YesNo\").show();
                            pharmacy_showFields();' />";

?>
