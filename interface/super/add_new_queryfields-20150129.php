<?php

require_once("../globals.php");
require_once("$srcdir/acl.inc");
require_once("$srcdir/formdata.inc.php");
require_once("$srcdir/globals.inc.php");
require_once("$srcdir/user.inc");
require_once("$srcdir/classes/CouchDB.class.php");

 $sqlQuery ="insert into tbl_allcare_query(name,description,querystring,addedby,addeddate) values ('".$_POST['name']."','".$_POST['description']."','".$_POST['querystring']."',".$_POST['addedby'].",'".$_POST['addeddate']."')";
    //echo $sqlQuery;die;
 $insertQueryFields=sqlStatement($sqlQuery);
 
 
//$getQueryFields=sqlStatement("select q.name,q.description,q.querystring from tbl_allcare_query q order by name asc");
// $sqlGroupRows = sqlNumRows($getQueryFields);

/*$newString=" <input type='button' name='btnAddNew' id='btnAddNew' value='New' style='float: right !important;' onclick='showAddMode('Y');'>
    <p>&nbsp;</p>
  
  
<table border=0   cellpadding=1 cellspacing=0 width='100% !important;'  style='width:100%;border: 1px #000000 solid;'>
 <tr height='24' style='background:lightgrey;'>
            <td width='20%' class='bold borderclass'>Query Name</td>
            <td width='20%' class='bold borderclass'>Description</td>
            <td  class='bold borderclass'>SQL</td>
            <td width='15%' class='bold borderclass'>Added By</td>
            
        </tr>
         while($rowGroup=mysql_fetch_array($getQueryFields))
         {
             echo '<tr  style=background:white height=24>
                <td class='text borderclass'>".$rowGroup['name']."</td>
                <td width='200px' class='text borderclass'>".$rowGroup['description']."</td>
                <td width='200px' class='text borderclass'>".$rowGroup['querystring']."</td>
                <td class='text borderclass'>admin</td>
            </tr>
         }  

</table>";      */

 echo "Query Added Successfully ";

?>
