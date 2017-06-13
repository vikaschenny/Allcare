<?php
require_once("../verify_session.php");
require_once("../../library/sqlCentralDB.inc");
global $sqlconfCentralDB;

// Get posted data as variables
extract($_POST);
$filterVar = explode("|",$insoptions);
$layoutId = $filterVar[0];
$tableName = $filterVar[1];
$query = sqlStatement("SELECT * FROM layout_options WHERE form_id='".$layoutId."'");
$practicefieldId = array();
while($sqlFtch1 = sqlFetchArray($query)):
    $practicefieldId[] = $sqlFtch1['field_id'];
endwhile;

$query = "SELECT * FROM layout_options WHERE form_id='".$layoutId."'";
$centralfieldId = array();
foreach ($sqlconfCentralDB->query($query) as $row) {
    $centralfieldId[] = $row['field_id'];
}

/*
 *  array array_diff ( array $array1 , array $array2 [, array $... ] )
    Compares array1 against one or more other arrays and returns the values 
    in array1 that are not present in any of the other arrays.
 */

$resultFields = array_diff($centralfieldId,$practicefieldId);
$resultFieldsStr = implode("','",$resultFields);
$resultFieldsStr = "'".$resultFieldsStr."'";

$query = "SELECT * FROM layout_options WHERE form_id='".$layoutId."' AND field_id IN (".$resultFieldsStr.")";
$reslt = $sqlconfCentralDB->prepare($query);
$reslt->execute();
$centralfieldId = array();
while($row = $reslt->fetchAll(PDO::FETCH_ASSOC)):
    $centralfieldId[] = $row;
endwhile;
//print_r($centralfieldId[0]);
$instArr = array();
$i = 0;

foreach($centralfieldId[0] as $row):
    $query = "INSERT INTO layout_options (form_id,field_id,group_name,title,
                                          seq,data_type,uor,fld_length,max_length,list_id,
                                          titlecols,datacols,default_value,edit_options,description,fld_rows) 
                                          VALUES ('".mysql_real_escape_string($row['form_id'])."',
                                                  '".mysql_real_escape_string($row['field_id'])."',
                                                  '".mysql_real_escape_string($row['group_name'])."',
                                                  '".mysql_real_escape_string($row['title'])."',
                                                  '".mysql_real_escape_string($row['seq'])."',
                                                  '".mysql_real_escape_string($row['data_type'])."',
                                                  '".mysql_real_escape_string($row['uor'])."',
                                                  '".mysql_real_escape_string($row['fld_length'])."',
                                                  '".mysql_real_escape_string($row['max_length'])."',
                                                  '".mysql_real_escape_string($row['list_id'])."',    
                                                  '".mysql_real_escape_string($row['titlecols'])."',
                                                  '".mysql_real_escape_string($row['datacols'])."',
                                                  '".mysql_real_escape_string($row['default_value'])."',
                                                  '".mysql_real_escape_string($row['edit_options'])."',
                                                  '".mysql_real_escape_string($row['description'])."',
                                                  '".mysql_real_escape_string($row['fld_rows'])."')";
   $inst = sqlInsert($query);
   $instArr[] = $inst;
   $i++;
endforeach;

// Now get custom table columns from central
$prepQuery = $sqlconfCentralDB->prepare("DESCRIBE $tableName");
$prepQuery->execute();
$table_fields = $prepQuery->fetchAll(PDO::FETCH_COLUMN);

// Now get columns from practice
$pracQuery = sqlListFields($tableName);

$remainingColumns = array_diff($table_fields,$pracQuery);

foreach($remainingColumns as $column):
    $query = "ALTER TABLE $tableName ADD $column TEXT NOT NULL;";
    sqlStatement($query);
endforeach;

echo json_encode($instArr);
//echo json_encode($resultFields);
?>