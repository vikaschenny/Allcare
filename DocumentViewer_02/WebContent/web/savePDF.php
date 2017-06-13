<?php
$request_body = file_get_contents('php://input');
$fileName = 'drive2357.pdf';	
$fileSaved = file_put_contents("/mnt/stor10-wc2-dfw1/551939/551948/emrsb.risecorp.com/web/content/interface/patient_file/".$fileName, $request_body);	
echo $fileSaved;
?>