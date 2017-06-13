<?php

require_once("../globals.php");
require_once("$srcdir/acl.inc");
require_once("$srcdir/formdata.inc.php");
require_once("$srcdir/globals.inc.php");
require_once("$srcdir/user.inc");
require_once("$srcdir/classes/CouchDB.class.php");


//echo $_POST['name'];
//    echo "select count(*) from  tbl_allcare_query where name='".$_POST['name']."'";
$findQuery ="select * from  tbl_allcare_query where name='".$_POST['name']."'";
$findQueryRes=sqlStatement($findQuery);

echo $findQueryRows = sqlNumRows($findQueryRes);



?>
