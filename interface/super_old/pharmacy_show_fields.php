<?php

require_once("../globals.php");
require_once("$srcdir/acl.inc");
require_once("$srcdir/formdata.inc.php");
require_once("$srcdir/globals.inc.php");
require_once("$srcdir/user.inc");
require_once("$srcdir/classes/CouchDB.class.php");

    $table_name=$_POST['table_name'];
                                                
    $postyperes = sqlStatement("SHOW COLUMNS FROM ".$table_name." 
                                WHERE Field!='id' AND Field!='pid' AND Field!='Group_ID' AND Field!='Recordset_ID' AND Field!='pharmacy_id'");
    $allcarerows = array();
    
    $existingFields=array();
    if($table_name=='tbl_allcare_pharmacy1to1')
    {
        $existingFieldsNames=sqlStatement("SELECT tmeta.field_ID,tmeta.field_Name FROM tbl_allcare_tablemeta_pharmacy tmeta 
                                 INNER JOIN ".$table_name."_fieldmapping tfm ON tmeta.field_ID=tfm.Field_ID 
                                 ORDER BY tmeta.field_Name");

        while($resExistingFieldsNames = sqlFetchArray($existingFieldsNames))
        {
            array_push($existingFields,$resExistingFieldsNames['field_Name']);
        }
        echo "<b>Note:</b> The fields with <b>disabled</b> check boxes are already added to some other group/s";
    }
    
    echo "<div id='pharmacy_divFields' name='pharmacy_divFields' style='border:1px SOLID #000;height:150px;width:300px;overflow-y:scroll;'>";
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
                    <input type='checkbox' id='pharmacy_chkbox_".$allcarerows['Field']."' 
                        name='pharmacy_chkAllFields' value='".$allcarerows['Field']."' 
                        $chkDisabled />
                </td>
                <td>
                    <label for='pharmacy_chkbox_".$allcarerows['Field']."'>".$allcarerows['Field']."</label>
                </td>
              </tr>";    
               
    }
    
    
    echo "</table></div>";                

?>
