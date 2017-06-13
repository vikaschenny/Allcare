<?php

require_once("../globals.php");
require_once("$srcdir/acl.inc");
require_once("$srcdir/formdata.inc.php");
require_once("$srcdir/globals.inc.php");
require_once("$srcdir/user.inc");
require_once("$srcdir/classes/CouchDB.class.php");

$table_name=$_POST['TableName'];
$group_recordset_name=$_POST['GroupRecordsetName'];
$pos_id=$_POST['pos_id'];
$selectedFields=explode(",",$_POST['selectedFields']);

//print_r($selectedFields);

$GR_name=($table_name=='tbl_allcare_patients1to1_fieldmapping') ? 'Grouping_Name' : 'Recordset_Name';
$GR_ID=($table_name=='tbl_allcare_patients1to1_fieldmapping') ? 'Grouping_ID' : 'Recordset_ID';
$table_ID=($table_name=='tbl_allcare_patients1to1_fieldmapping') ? 1 : 2;
$GR=($table_name=='tbl_allcare_patients1to1_fieldmapping') ? 'Group' : 'Recordset';

$getCurrentGRid=sqlStatement("SELECT DISTINCT $GR_ID FROM $table_name WHERE $GR_name='$group_recordset_name'");
$currentGRid=sqlFetchArray($getCurrentGRid);   
$GRid=$currentGRid[$GR_ID];

$getCreatedDateOfGR=sqlStatement("SELECT DISTINCT Created_Date FROM $table_name WHERE $GR_name='$group_recordset_name'");
$createdDateGR=sqlFetchArray($getCreatedDateOfGR);   
$GRcreatedDate=$createdDateGR['Created_Date'];

$deleteGroupRecordset=sqlStatement("DELETE FROM $table_name
                                    WHERE $GR_name='$group_recordset_name'");

foreach($selectedFields as $field)
{                
    $getFieldName=sqlStatement("SELECT field_ID FROM tbl_allcare_tablemeta 
                                WHERE field_Name='$field'
                                AND table_ID=$table_ID");
    $getFieldId=sqlFetchArray($getFieldName);   
        
    $sql ="INSERT INTO $table_name(POS_id,$GR_ID,$GR_name,Table_ID,Field_ID,Created_Date,Updated_Date)
           VALUES(".$pos_id.",$GRid,'$group_recordset_name',$table_ID,".$getFieldId['field_ID'].",'$GRcreatedDate',now())";

    $fieldmappingQry = sqlStatement($sql);       
                
}

echo "$GR updated successfully";

?>
