<?php

require_once("../globals.php");
require_once("$srcdir/acl.inc");
require_once("$srcdir/formdata.inc.php");
require_once("$srcdir/globals.inc.php");
require_once("$srcdir/user.inc");
require_once("$srcdir/classes/CouchDB.class.php");

$mappingTableName=$_POST['mappingTableName'];
$groupRecordsetName=$_POST['groupRecordsetName'];
$showYesNo=$_POST['showYesNo'];

$selectedFields=explode(",",$_POST['selectedFields']);
//print_r($selectedFields);
$Gid='';
$RSid='';

if($mappingTableName=='tbl_allcare_addressbook1to1_fieldmapping')    
{    
    /*$getLastGroupRecordID=sqlStatement('SELECT Grouping_ID as GRid FROM tbl_allcare_addressbook1to1_fieldmapping
                                        WHERE id IN (SELECT MAX(id) FROM tbl_allcare_addressbook1to1_fieldmapping)');*/
    $getLastGroupRecordID=sqlStatement('SELECT MAX(Grouping_ID) FROM tbl_allcare_addressbook1to1_fieldmapping');
    $lastGroupRecordID=sqlFetchArray($getLastGroupRecordID);     
    $Gid=$lastGroupRecordID['GRid'];    
}
else if($mappingTableName=='tbl_allcare_addressbook1ton_fieldmapping') 
{
    /*$getLastGroupRecordID=sqlStatement('SELECT Recordset_ID as GRid FROM tbl_allcare_addressbook1ton_fieldmapping
                                        WHERE id IN (SELECT MAX(id) FROM tbl_allcare_addressbook1ton_fieldmapping)');*/
    $getLastGroupRecordID=sqlStatement('SELECT MAX(Recordset_ID) FROM tbl_allcare_addressbook1ton_fieldmapping');
    $lastGroupRecordID=sqlFetchArray($getLastGroupRecordID);    
    $RSid=$lastGroupRecordID['GRid'];    
}

foreach($selectedFields as $field)
{            
    $lastGroupRecordID='';
    $getFieldId='';

    if($mappingTableName=='tbl_allcare_addressbook1to1_fieldmapping')    
    {
        $tableId=1;
        $getLastGroupRecordID=sqlStatement('SELECT Grouping_ID as GRid FROM tbl_allcare_addressbook1to1_fieldmapping
                                            WHERE id IN (SELECT MAX(id) FROM tbl_allcare_addressbook1to1_fieldmapping)');
        $lastGroupRecordID=sqlFetchArray($getLastGroupRecordID);    
        
        /*echo "SELECT field_ID FROM tbl_allcare_tablemeta_addressbook 
                                    WHERE field_Name='".$field."'
                                    AND table_ID=1"; die;
        */
        $getFieldName=sqlStatement("SELECT field_ID FROM tbl_allcare_tablemeta_addressbook 
                                    WHERE field_Name='".$field."'
                                    AND table_ID=1");
        $getFieldId=sqlFetchArray($getFieldName);         
        
        $sql ="INSERT INTO $mappingTableName(Grouping_ID,Grouping_Name,Table_ID,Field_ID,isVisible,Created_Date)
               VALUES(".$Gid."+1,'".$groupRecordsetName."',".$tableId.",".$getFieldId['field_ID'].",'$showYesNo',now())";

        $fieldmappingQry = sqlStatement($sql);                
    }
    else if($mappingTableName=='tbl_allcare_addressbook1ton_fieldmapping') 
    {
        $tableId=2;
        $getLastGroupRecordID=sqlStatement('SELECT Recordset_ID as GRid FROM tbl_allcare_addressbook1ton_fieldmapping
                                            WHERE id IN (SELECT MAX(id) FROM tbl_allcare_addressbook1ton_fieldmapping)');
        $lastGroupRecordID=sqlFetchArray($getLastGroupRecordID);    
        
        /*echo "SELECT field_ID FROM tbl_allcare_tablemeta_addressbook 
                                    WHERE field_Name='".$field."'
                                    AND table_ID=2";
        */
        $getFieldName=sqlStatement("SELECT field_ID FROM tbl_allcare_tablemeta_addressbook 
                                    WHERE field_Name='".$field."'
                                    AND table_ID=2");
        $getFieldId=sqlFetchArray($getFieldName); 
                
        $sql ="INSERT INTO $mappingTableName(Recordset_ID,Recordset_Name,Table_ID,Field_ID,isVisible,Created_Date)
               VALUES(".$RSid."+1,'".$groupRecordsetName."',
              ".$tableId.",".$getFieldId['field_ID'].",'$showYesNo',now())";

        $fieldmappingQry = sqlStatement($sql);        
    }        
}
    
echo "Mapping done successfully";

?>
