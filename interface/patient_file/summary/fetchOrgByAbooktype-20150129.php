<?php 
require_once("../../globals.php");
require_once("$srcdir/sql.inc");

$fres = sqlStatement("SELECT id, users.organization
                                        FROM users
                                        JOIN list_options ON users.abook_type = list_options.option_id
                                        AND users.organization !=  ''
                                        AND list_id =  'abook_type' AND seq=".$_GET['type']);
if ($fres) {
    echo "<option value=0> Select </option>";
    while($row = mysql_fetch_array($fres)){
        echo "<option value=".$row['id'].">" . $row['organization'] . "</option>";
    }   
}
?>