<?php
include_once("../../globals.php");
include_once("$srcdir/api.inc");

require ("C_FormVitals_custom.class.php");
$c = new C_FormVitals_custom();
echo $c->default_action_process_custom($_POST);
//print_r($_POST);

//@formJump();
echo '<script>  window.location.href = "../../reports/incomplete_charts.php"; </script>';
?>
