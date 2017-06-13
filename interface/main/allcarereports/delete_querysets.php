<?php

require_once("../../globals.php");
require_once("../../../library/formdata.inc.php");
require_once("../../../library/globals.inc.php");

$deleteQuerySet=sqlStatement("DELETE FROM tbl_allcarereports_querysets
                            WHERE setname='".$_POST['querySet']."' AND screen='".$_POST['screen']."' ");
                          

?>
