<?php
require_once("../verify_session.php");
extract($_POST);
sqlStatement("UPDATE form_encounter SET billing_note = ? WHERE encounter=?",array($bnote,$encId));
echo "Billing note updated";
?>