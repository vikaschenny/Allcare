<?php
$db = mysqli_connect("localhost","schemaxt_drive","PKf_p,WmEuN&","schemaxt_drivesync") or die("Error " . mysqli_error($db));
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
	echo "<p>Contact Administrator</p>";
    exit();
}

?>