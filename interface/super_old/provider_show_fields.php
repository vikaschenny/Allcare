<?php

require_once("../globals.php");
require_once("$srcdir/acl.inc");
require_once("$srcdir/formdata.inc.php");
require_once("$srcdir/globals.inc.php");
require_once("$srcdir/user.inc");
require_once("$srcdir/classes/CouchDB.class.php");

    $table_name=$_POST['table_name'];
                                                
    $postyperes = sqlStatement("SHOW COLUMNS FROM ".$table_name." 
                                WHERE Field!='id' AND Field!='pid' AND Field!='Group_ID' AND Field!='Recordset_ID' AND Field!='provider_id'");
    $allcarerows = array();
    $existingFields=array();
    if($table_name=='tbl_allcare_provider1to1')
    {
        $existingFieldsNames=sqlStatement("SELECT tmeta.field_ID,tmeta.field_Name FROM tbl_allcare_tablemeta_provider tmeta 
                                 INNER JOIN ".$table_name."_fieldmapping tfm ON tmeta.field_ID=tfm.Field_ID 
                                 ORDER BY tmeta.field_Name");

        while($resExistingFieldsNames = sqlFetchArray($existingFieldsNames))
        {
            array_push($existingFields,$resExistingFieldsNames['field_Name']);
        }
        echo "<b>Note:</b> The fields with <b>disabled</b> check boxes are already added to some other group/s";
    }
    /*
    echo "<select id='comboFields' name='comboFields' size='5' multiple>";
    while($allcarerows = sqlFetchArray($postyperes))
    {        
             //echo  $allcarerows['Field'] ."<input type='checkbox' name='chkFields[]' value='$allcarerows[Field]'>";
        echo "<option value='".$allcarerows['Field']."'>".$allcarerows['Field']."</option>";        
    }
    
    echo "</select>";    */
    
     echo "<div id='provider_divFields' name='provider_divFields' style='border:1px SOLID #000;height:150px;width:300px;overflow-y:scroll;'>";
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
                    <input type='checkbox' id='provider_chkbox_".$allcarerows['Field']."' 
                        name='provider_chkAllFields' 
                        value='".$allcarerows['Field']."'
                        $chkDisabled />
                </td>
                <td>
                    <label for='provider_chkbox_".$allcarerows['Field']."'>".$allcarerows['Field']."</label>
                </td>
              </tr>";    
               
    }
    
    
    echo "</table></div>";                

?>
