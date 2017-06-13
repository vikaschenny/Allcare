<?php

require_once("../globals.php");
require_once("$srcdir/acl.inc");
require_once("$srcdir/formdata.inc.php");
require_once("$srcdir/globals.inc.php");
require_once("$srcdir/user.inc");
require_once("$srcdir/classes/CouchDB.class.php");

?>

<br>
  
    <table border='1'>
      <tr>
<!--    <td align='right'>Select the Primary Diagnosis</td>-->
        <td align='right'>Select the Diagnosis Category</td>
    <td>
    <?php
    
    $sqlGetConfigurationDiagnosis="SELECT option_id,title 
                               FROM list_options
                               WHERE list_id='F2F_Diagnosis_Categories'";

    
    
    $resGetConfigurationDiagnosis=sqlStatement($sqlGetConfigurationDiagnosis);
    ?>
    <select id='selectConfigDiagnosis' name='selectConfigDiagnosis' onchange='javascript:showConfigByDiagnosis(this.value);'>
    <option value='0'>--Select--</option>
    <?php
    while($rowDiagnosis=mysql_fetch_array($resGetConfigurationDiagnosis))
    {
        echo "<option value='".$rowDiagnosis['option_id']."'>".$rowDiagnosis['title']."</option>";
    }
    ?>
    </select>   
    </td>
    </tr>
    </table>
    