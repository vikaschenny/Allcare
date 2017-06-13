<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once("../globals.php");
require_once("$srcdir/acl.inc");
require_once("$srcdir/formdata.inc.php");
require_once("$srcdir/globals.inc.php");
require_once("$srcdir/user.inc");
require_once("$srcdir/classes/CouchDB.class.php");

//print_r($_POST);die;

?>

<br>

  
    <table border='1'>
    <tr><td align='right'>Select the Note</td>
    <td>
    <?php
    /*$sqlGetConfigurationNotes="SELECT option_id,title 
                               FROM list_options
                               WHERE list_id='FaceToFace_Configuration_Notes'";
*/
    $sqlGetConfigurationNotes="SELECT option_id,title 
                               FROM list_options
                               WHERE list_id='F2F_Config_Notes_Primary'";

    
    
    $resGetConfigurationNotes=sqlStatement($sqlGetConfigurationNotes);
    ?>
    <select id='selectConfigNote' name='selectConfigNote' onchange='javascript:showConfigByNote(this.value);'>
    <option value='0'>--Select--</option>
    <?php
    while($rowNotes=mysql_fetch_array($resGetConfigurationNotes))
    {
        echo "<option value='".$rowNotes['option_id']."'>".$rowNotes['title']."</option>";
    }
    ?>
    </select>   
    </td>
    </tr>
    </table>
    



