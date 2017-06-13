<?php

require_once("../../globals.php");
require_once("../../../library/formdata.inc.php");
require_once("../../../library/globals.inc.php");

$screen=$_POST['screen'];
$getQuerySets=sqlStatement("SELECT id,setname FROM tbl_allcarereports_querysets where screen='$screen'");
                           

$setNamesArray=array();
while ($rowQuerySets = sqlFetchArray($getQuerySets)) 
{
    //echo "<option value='".$rowQuerySets['set_name']."'>".$rowQuerySets['set_name']."</option>";
    array_push($setNamesArray,$rowQuerySets['setname']);

}  

echo implode($setNamesArray,'|');

?>
