<?php
require_once("verify-session.php");
require_once("../../library/formdata.inc.php");
require_once("../../library/globals.inc.php");

$getQuerySets=sqlStatement("SELECT id,set_name FROM tbl_allcare_query_sets
                            WHERE app_enc='".$_POST['app_enc']."'");

$setNamesArray=array();
while ($rowQuerySets = sqlFetchArray($getQuerySets)) 
{
    //echo "<option value='".$rowQuerySets['set_name']."'>".$rowQuerySets['set_name']."</option>";
    array_push($setNamesArray,$rowQuerySets['set_name']);

}  

echo implode($setNamesArray,'|');

?>
