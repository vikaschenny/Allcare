<?php

require_once("../../globals.php");
require_once("$srcdir/acl.inc");
require_once("$srcdir/formdata.inc.php");
require_once("$srcdir/globals.inc.php");
require_once("$srcdir/user.inc");
require_once("$srcdir/classes/CouchDB.class.php");

$extension_name=$_POST['extension_name'];

$meta_table_name=($extension_name=='patients') ? 'tbl_allcare_tablemeta' : 'tbl_allcare_tablemeta_'.$extension_name; 

$table_name=$_POST['table_name'];

    $postyperes = sqlStatement("SHOW COLUMNS FROM ".$table_name." 
                            WHERE Field!='id' AND Field!='pid' 
                            AND Field!='Group_ID' AND Field!='Recordset_ID' 
                            AND Field!='pos_id' AND Field!='".$extension_name."_company_id'
                            AND Field!='".$extension_name."_id'");
$allcarerows = array();
$existingFields=array();
//if($table_name=='tbl_allcare_'.$extension_name.'1to1')
//{
//echo 'TN='.$table_name;
        $existingFieldsNames=sqlStatement("SELECT tmeta.field_ID,tmeta.field_Name FROM $meta_table_name tmeta 
                             INNER JOIN ".$table_name."_fieldmapping tfm ON tmeta.field_ID=tfm.Field_ID 
                             ORDER BY tmeta.field_Name");  



    while($resExistingFieldsNames = sqlFetchArray($existingFieldsNames))
    {
        array_push($existingFields,$resExistingFieldsNames['field_Name']);
    }

    $G_R=($table_name=='tbl_allcare_'.$extension_name.'1to1') ? 'group/s' : 'Recordset/s';
    echo "<b>Note:</b> The fields with <b>disabled</b> check boxes are already added to some other $G_R";
//}

echo "<div id='".$extension_name."_divFields' name='".$extension_name."_divFields' style='border:1px SOLID #000;height:150px;width:300px;overflow-y:scroll;'>";
echo "<table border='1' style='width:275px'>";
while($allcarerows = sqlFetchArray($postyperes))
{
    $chkDisabled='';                 
    if(in_array($allcarerows['Field'], $existingFields))
    {
        $chkDisabled='disabled';              
    }

    echo "<tr>
            <td id='".$allcarerows['Field']."'>
                <input type='checkbox' id='".$extension_name."_chkbox_".$allcarerows['Field']."' 
                    name='".$extension_name."_chkAllFields' value='".$allcarerows['Field']."'
                    $chkDisabled />
            </td>
            <td>
                <label for='".$extension_name."_chkbox_".$allcarerows['Field']."'>".$allcarerows['Field']."</label>
            </td>
          </tr>";    

}


echo "</table></div>";                

?>
