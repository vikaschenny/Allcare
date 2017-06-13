<?php
//print_r($_POST);
	//echo $_POST['value'].$_POST['id'].' (server updated)';

require_once("../globals.php");
require_once("$srcdir/acl.inc");
require_once("$srcdir/formdata.inc.php");
require_once("$srcdir/globals.inc.php");
require_once("$srcdir/user.inc");
require_once("$srcdir/classes/CouchDB.class.php");


 
 if($_POST['value']!='')
     {
                if($_POST['column']==1)
                {
                   $updatePart = "set description='".$_POST['value']."'";
                }
                else
                {
                    $updatePart = "set querystring='".$_POST['value']."'";
                }
                $sqlQuery ="update tbl_allcare_query  ".$updatePart." where id=".$_POST['row_id'];

                $updateQuery=sqlStatement($sqlQuery);

                echo $_POST['value'];
}
 
 
?>