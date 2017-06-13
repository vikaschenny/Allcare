<?php

require_once("../globals.php");
require_once("$srcdir/acl.inc");
require_once("$srcdir/formdata.inc.php");
require_once("$srcdir/globals.inc.php");
require_once("$srcdir/user.inc");
require_once("$srcdir/classes/CouchDB.class.php");

$table_name=$_POST['table_name'];

$GR_name=($table_name=='tbl_allcare_addressbook1to1_fieldmapping') ? 'Grouping_Name' : 'Recordset_Name' ;
$buttonLabel=($table_name=='tbl_allcare_addressbook1to1_fieldmapping') ? 'New Group' : 'New Recordset' ;
$lblHide=($table_name=='tbl_allcare_addressbook1to1_fieldmapping') ? 'addressBook_lblRecordset' : 'addressBook_lblGroup';
$lblShow=($table_name=='tbl_allcare_addressbook1to1_fieldmapping') ? 'addressBook_lblGroup' : 'addressBook_lblRecordset';

$postyperes = sqlStatement("SELECT DISTINCT $GR_name AS GR_name FROM $table_name ORDER BY $GR_name");

echo "<select id='addressBook_comboGroupsRecordsets' name='addressBook_comboGroupsRecordsets' 
              onchange=\"javascript:jQuery('#addressBook_lblFieldsSelected').show(); 
                                    addressBook_showFieldsByGroupRecordsets('".$table_name."');
                                    addressBook_hideNewButtonData();
                                    jQuery('#td_Show_YesNo').show();
                                    jQuery('#addressBook_lblFieldsSelected').show();   \">
      <option value='none'>none</option>";
while($allcarerows = sqlFetchArray($postyperes))
{                 
    echo "<option value='".$allcarerows['GR_name']."'>".$allcarerows['GR_name']."</option>";        
}

echo "</select>   
<input type='button' id='addressBook_btnNewGR' name='addressBook_btnNewGR' value='$buttonLabel' 
       onclick='javascript: jQuery(\"#$lblShow\").show();
                            jQuery(\"#$lblHide\").hide();                               
                            jQuery(\"#addressBook_txtGroupRecordset\").show();  
                            jQuery(\"#addressBook_lblExistingFields\").show();
                            jQuery(\"#addressBook_lblFieldsSelected\").hide();   
                            jQuery(\"#addressBook_showFieldsByGroupRecordsets\").html(\"\"); 
                            jQuery(\"#addressBook_comboGroupsRecordsets\").val(\"none\"); 
                            jQuery(\"#td_Show_YesNo\").show();
                            addressBook_showFields();' />";

?>
