<?php

require_once("../globals.php");
require_once("$srcdir/acl.inc");
require_once("$srcdir/formdata.inc.php");
require_once("$srcdir/globals.inc.php");
require_once("$srcdir/user.inc");
require_once("$srcdir/classes/CouchDB.class.php");

$relatedTables=array();
$relatedTables = explode(',', $_POST['relatedTables']);

$FieldName=array();
$FieldName = explode(',', $_POST['FieldName']);

$FieldType=array();
$FieldType = explode(',', $_POST['FieldType']);

$DataLength=array();
$DataLength = explode(',', $_POST['DataLength']);

//print_r($DataLength);

$FieldRequired=array();
$FieldRequired = explode(',', $_POST['FieldRequired']);

$DefaultValue=array();
$DefaultValue = explode(',', $_POST['DefaultValue']);

$FieldViewEdit=array();
$FieldViewEdit = explode(',', $_POST['FieldViewEdit']);

function insert_in_tables($tableName)
{
    GLOBAL $relatedTables;
    GLOBAL $FieldName;
    GLOBAL $FieldType;
    GLOBAL $DataLength;
    GLOBAL $FieldRequired;
    GLOBAL $DefaultValue; 
    GLOBAL $FieldViewEdit;
    //print_r($DataLength); die;
    if(isset($FieldName))   //if(isset($relatedTables))
    {
        foreach($FieldName as $key=>$value)   //foreach($relatedTables as $key=>$value)
        {
            if($value!='none')
            {
                $isNull=($FieldRequired[$key]==0)?"NULL":"NOT NULL";
                $insertFields='';
                if(strpos($DataLength[$key],'*')) // if data type is enum
                {
                    $DataLength[$key]=str_replace('*',',',$DataLength[$key]);
                }    
                $DataLength[$key]=stripslashes($DataLength[$key]);
                //if(($DataLength[$key]=='-' || $DataLength[$key]=='') && 
                if(($DataLength[$key]=='' || $DataLength[$key]==0) && 
                    ($FieldType[$key]=='date' || $FieldType[$key]=='datetime' ||
                    $FieldType[$key]=='time' || $FieldType[$key]=='timestamp' || $FieldType[$key]=='text' ||
                    $FieldType[$key]=='tinytext' || $FieldType[$key]=='mediumtext' ||
                    $FieldType[$key]=='longtext' || $FieldType[$key]=='year' ||
                    $FieldType[$key]=='blob' || $FieldType[$key]=='tinyblob' ||
                    $FieldType[$key]=='mediumblob' || $FieldType[$key]=='longblob'||                        
                    $FieldType[$key]=='geometry' || $FieldType[$key]=='point' ||
                    $FieldType[$key]=='linestring' || $FieldType[$key]=='polygon' ||  
                    $FieldType[$key]=='multipoint' || $FieldType[$key]=='multilinestring' ||    
                    $FieldType[$key]=='multipolygon' || $FieldType[$key]=='geometrycollection'))      
                {
                    $insertFields="ALTER TABLE ".$tableName."
                                   ADD COLUMN ".$FieldName[$key]." ".$FieldType[$key]." ".$isNull."";                                        
                }
                else
                {
                    $insertFields="ALTER TABLE ".$tableName."
                                   ADD COLUMN ".$FieldName[$key]." ".$FieldType[$key]."(".$DataLength[$key].") ".$isNull."";                    
                }
                 
                if($DefaultValue[$key]!='')
                {
                    $insertFields=$insertFields." DEFAULT '".$DefaultValue[$key]."'";
                }
                
                $executeInsertFields=sqlStatement($insertFields);      
                
                // Get field_ID from metatable
                $lastRecordFieldId=  sqlStatement('SELECT field_ID FROM tbl_allcare_tablemeta_provider
                                                   WHERE id IN (SELECT MAX(id) FROM tbl_allcare_tablemeta_provider)');
                
                //$lastInsertedFieldId=  sqlStatement('SELECT field_ID FROM tbl_allcare_tablemeta_provider WHERE id='.mysql_insert_id().'');
                $lastFieldId=sqlFetchArray($lastRecordFieldId);
                
                $tableId=($tableName=='tbl_allcare_provider1to1')?1:2;
                 
                $table_original=($relatedTables[$key]!='none') ? $relatedTables[$key] : '' ;
//                
//sqlStatement("INSERT INTO tbl_allcare_tablemeta_provider(table_ID, table_Name,field_ID,field_Name,is_Field_Editable,created_Date) 
//      VALUES ( ?, ?, ?, ?, ?, ? )", array($tableId, $relatedTables[$key], $lastFieldId['field_ID']+1, 
//                                          $FieldName[$key], $FieldViewEdit[$key],date('Y-m-d H:i:s')));                                          
//                
                /*
sqlStatement("INSERT INTO tbl_allcare_tablemeta_provider(table_ID, table_Name,field_ID,field_Name,is_Field_Editable,created_Date) 
      VALUES ( ?, ?, ?, ?, ?, ? )", array($tableId, $table_original, $lastFieldId['field_ID']+1, 
                                          $FieldName[$key], $FieldViewEdit[$key],date('Y-m-d H:i:s')));                                          
                
             */
sqlStatement("INSERT INTO tbl_allcare_tablemeta_provider(table_ID,table_Name,field_ID,field_Name,is_Field_Editable,created_Date) 
      VALUES($tableId,'$table_original',".$lastFieldId['field_ID']."+1,'$FieldName[$key]','$FieldViewEdit[$key]','date(Y-m-d H:i:s)')");   
                
                
            }
        }
    }
}

if(isset($_POST['chk1to1']) && $_POST['chk1to1']==1)
{
    insert_in_tables('tbl_allcare_provider1to1');
}

if(isset($_POST['chk1ton']) && $_POST['chk1ton']==1)
{
    insert_in_tables('tbl_allcare_provider1ton');
}

echo " Data saved successfully ";

?>
