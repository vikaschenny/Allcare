<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

require_once("../../globals.php");
require_once("$srcdir/acl.inc");
require_once("$srcdir/formdata.inc.php");
require_once("$srcdir/globals.inc.php");
require_once("$srcdir/user.inc");
require_once("$srcdir/classes/CouchDB.class.php");

$extension_name=$_POST['extension_name'];

function checkInTable($fieldName,$tableName)
{
    $checkAlreadyExists=  sqlStatement("SHOW COLUMNS FROM $tableName 
                                        WHERE Field='$fieldName'" );

    $field = sqlFetchArray($checkAlreadyExists);
    
    if($field['Field']==$fieldName)
    {
        echo "The field '$fieldName' already exists in table '$tableName'\n";
    }       
    else
    {
        echo 1;
    }
}


if(isset($_POST['chk1to1']) && $_POST['chk1to1']==1)
{
   checkInTable($_POST['FieldName'],'tbl_allcare_'.$extension_name.'1to1');    
}

if(isset($_POST['chk1ton']) && $_POST['chk1ton']==1)
{
   checkInTable($_POST['FieldName'],'tbl_allcare_'.$extension_name.'1ton');    
}


?>