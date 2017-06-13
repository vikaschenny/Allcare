<?php

require_once("../globals.php");
require_once("../../library/formdata.inc.php");
require_once("../../library/globals.inc.php");

$deleteQuerySet=sqlStatement("DELETE FROM tbl_allcare_query_sets
                            WHERE set_name='".$_POST['querySet']."'
                            AND app_enc='".$_POST['app_enc']."'");

?>
