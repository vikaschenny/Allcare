<?php

require_once("../globals.php");
require_once("$srcdir/acl.inc");
require_once("$srcdir/formdata.inc.php");
require_once("$srcdir/globals.inc.php");
require_once("$srcdir/user.inc");
require_once("$srcdir/classes/CouchDB.class.php");

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

//echo "<table border='1'>";

$form_table_name='';

switch($_POST['form_val'])
{
    //case 1: $form_table_name='tbl_form_facetoface_configuration';         break;
    //case 2: $form_table_name='tbl_form_lab_requisition_configuration';    break;
    
    case 1:include('face_to_face_diagnosis.php');break;
    case 2:include('lab_request_config.php');break;    
    
}

/*
if($_POST['form_val']==1) // face to face
{
    
    echo "<tr><td align='right'>Select the Note</td><td>";
    
    $sqlGetConfigurationNotes="SELECT option_id,title 
                               FROM list_options
                               WHERE list_id='FaceToFace_Configuration_Notes'";

    $resGetConfigurationNotes=sqlStatement($sqlGetConfigurationNotes);

    echo "<select id='selectConfigNote' name='selectConfigNote' onchange=''>";
    echo "<option value='0'>--Select--</option>";
    while($rowNotes=mysql_fetch_array($resGetConfigurationNotes))
    {
        echo "<option value='".$rowNotes['option_id']."'>".$rowNotes['title']."</option>";
    }
    echo "</select>";    
    
    echo "</td></tr>";
    
}

$sqlGetFormFields="SHOW COLUMNS FROM ".$form_table_name." 
                   WHERE Field!='id' AND Field!='note_id' AND Field!='encounter' AND Field!='created_by'";

$resSqlStatement=sqlStatement($sqlGetFormFields);

while($row=mysql_fetch_array($resSqlStatement))
{
    echo "<tr>";
    echo "<td align='right'>".str_replace('_',' ',ucfirst($row['Field']))."</td>";
    echo "<td>
             <input type='text' id='txt_".$row['Field']."' name='txt_".$row['Field']."' ";
                
  
    echo" />
          </td>";
    echo "</tr>";
        
}

echo "<tr></tr>";
echo "<tr><td><input type='button' id='btnSaveFormConfig' name='btnSaveFormConfig' 
                     value='Save' onclick='add_form_configuration($form_table_name,$mode)' />
          </td></tr>";
echo "<tr></tr>";

echo "</table>";
*/

?>
