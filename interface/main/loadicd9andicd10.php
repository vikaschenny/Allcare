<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once("../globals.php");
require_once("../../library/formdata.inc.php");
require_once("../../library/globals.inc.php");

//Upload File
if (isset($_POST['submit'])) {
    if (is_uploaded_file($_FILES['filename']['tmp_name'])) {
            echo "<h1>" . "File ". $_FILES['filename']['name'] ." uploaded successfully." . "</h1>";
            echo "<h2>Displaying contents:</h2>";
            //readfile($_FILES['filename']['tmp_name']);
    }
    sqlStatement("DELETE FROM list_options WHERE list_id = 'icd9toicd10Mapping'");
    //Import uploaded file to Database
    $handle = fopen($_FILES['filename']['tmp_name'], "r");
    $logTxt = $logTxt2 = "";
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $desc = str_replace("'", "\'", $data[1]);
            // validate ICD10
            $icdTen = explode(";",$data[2]);
            foreach($icdTen as $ten):
                //echo "SELECT formatted_dx_code FROM icd10_dx_order_code WHERE formatted_dx_code = '".substr($ten,6)."'<br />";
                if($ten != ""):
                    $rs = sqlStatement("SELECT formatted_dx_code FROM icd10_dx_order_code WHERE formatted_dx_code = '".substr($ten,6)."'");
                    if(sqlNumRows($rs) > 0):
                        $query = "INSERT IGNORE INTO list_options (list_id,option_id,title,codes) VALUES ('icd9toicd10Mapping','$data[0]','$desc','$data[2]')";
                        sqlStatement($query);
                        $logTxt .= $ten . " is added in icd9toicd10Mapping LIST since this exists in Master Table\n";
                    else:
                        $logTxt2 .= $ten . " is not added in icd9toicd10Mapping LIST since this doesn't exist in Master Table\n";
                    endif; 
                else:
                    $logTxt2 .= "Sheet does not have ICD10 mapped to ICD9 = $data[0]\n";
                endif;    
            endforeach;
    }
    fclose($handle);
    echo nl2br($logTxt);
    echo "<br />";
    echo nl2br($logTxt2);
    print "Import done";
    $log  = $logTxt . "\n\n". $logTxt2;
    file_put_contents('../../../../logs/loadicd9andicd10'.date('Y-m-d').'.log', $log, FILE_APPEND);
    //view upload form
}
?>

<b>Upload new icd9 and icd10 mapping csv by browsing to file and clicking on Upload</b>
<form enctype='multipart/form-data' action='loadicd9andicd10.php' method='post'>
    File name to import: <input size='50' type='file' name='filename'><br />
    <input type='submit' name='submit' value='Upload'>
</form>