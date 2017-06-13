<?php

require_once("../globals.php");
require_once("$srcdir/acl.inc");
require_once("$srcdir/formdata.inc.php");
require_once("$srcdir/globals.inc.php");
require_once("$srcdir/user.inc");
require_once("$srcdir/classes/CouchDB.class.php");

$posType=$_POST['posType'];
$mappingTableName=$_POST['mappingTableName'];
$groupRecordsetName=$_POST['groupRecordsetName'];

$selectedFields=explode(",",$_POST['selectedFields']);
//print_r($selectedFields);
$Gid='';
$RSid='';

if($mappingTableName=='tbl_allcare_patients1to1_fieldmapping')    
{    
    $getLastGroupRecordID=sqlStatement('SELECT Grouping_ID as GRid FROM tbl_allcare_patients1to1_fieldmapping
                                   WHERE id IN (SELECT MAX(id) FROM tbl_allcare_patients1to1_fieldmapping)');
    $lastGroupRecordID=sqlFetchArray($getLastGroupRecordID);     
    $Gid=$lastGroupRecordID['GRid'];    
}
else if($mappingTableName=='tbl_allcare_patients1ton_fieldmapping') 
{    
    $getLastGroupRecordID=sqlStatement('SELECT Recordset_ID as GRid FROM tbl_allcare_patients1ton_fieldmapping
                                   WHERE id IN (SELECT MAX(id) FROM tbl_allcare_patients1ton_fieldmapping)');
    $lastGroupRecordID=sqlFetchArray($getLastGroupRecordID);    
    $RSid=$lastGroupRecordID['GRid'];    
}

foreach($selectedFields as $field)
{            
    $lastGroupRecordID='';
    $getFieldId='';

    if($mappingTableName=='tbl_allcare_patients1to1_fieldmapping')    
    {
        $tableId=1;
        $getLastGroupRecordID=sqlStatement('SELECT Grouping_ID as GRid FROM tbl_allcare_patients1to1_fieldmapping
                                       WHERE id IN (SELECT MAX(id) FROM tbl_allcare_patients1to1_fieldmapping)');
        $lastGroupRecordID=sqlFetchArray($getLastGroupRecordID);    
        
        $getFieldName=sqlStatement("SELECT field_ID FROM tbl_allcare_tablemeta 
                                WHERE field_Name='".$field."'
                                AND table_ID=1");
        $getFieldId=sqlFetchArray($getFieldName);         
        
        $sql ="INSERT INTO $mappingTableName(POS_id,Grouping_ID,Grouping_Name,Table_ID,Field_ID,Created_Date)
               VALUES(".$posType.",".$Gid."+1,'".$groupRecordsetName."',".$tableId.",".$getFieldId['field_ID'].",now())";

        $fieldmappingQry = sqlStatement($sql);                
    }
    else if($mappingTableName=='tbl_allcare_patients1ton_fieldmapping') 
    {
        $tableId=2;
        $getLastGroupRecordID=sqlStatement('SELECT Recordset_ID as GRid FROM tbl_allcare_patients1ton_fieldmapping
                                       WHERE id IN (SELECT MAX(id) FROM tbl_allcare_patients1ton_fieldmapping)');
        $lastGroupRecordID=sqlFetchArray($getLastGroupRecordID);    
        
        $getFieldName=sqlStatement("SELECT field_ID FROM tbl_allcare_tablemeta 
                                WHERE field_Name='".$field."'
                                AND table_ID=2");
        $getFieldId=sqlFetchArray($getFieldName); 
                
        $sql ="INSERT INTO $mappingTableName(POS_id,Recordset_ID,Recordset_Name,Table_ID,Field_ID,Created_Date)
               VALUES(".$posType.",".$RSid."+1,'".$groupRecordsetName."',
              ".$tableId.",".$getFieldId['field_ID'].",now())";

        $fieldmappingQry = sqlStatement($sql);        
    }        
}
    
echo "Mapping done successfully";

?>
