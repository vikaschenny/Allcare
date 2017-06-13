<?php

require_once("../../globals.php");
require_once("$srcdir/acl.inc");
require_once("$srcdir/formdata.inc.php");
require_once("$srcdir/globals.inc.php");
require_once("$srcdir/user.inc");
require_once("$srcdir/classes/CouchDB.class.php");

//echo $_POST['name'];
//    echo "select count(*) from  tbl_allcare_query where name='".$_POST['name']."'";
$findQuery ="delete from tbl_allcare_patients1ton where id=".$_POST['tblRowId'];
$findQueryRes=sqlStatement($findQuery);

echo "Record deleted successfully";


?>
