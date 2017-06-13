<?php

require_once("../globals.php");
require_once("$srcdir/acl.inc");
require_once("$srcdir/formdata.inc.php");
require_once("$srcdir/globals.inc.php");
require_once("$srcdir/user.inc");
require_once("$srcdir/classes/CouchDB.class.php");

    $table_name=$_POST['table_name'];
                                                
    $postyperes = sqlStatement("SHOW COLUMNS FROM ".$table_name." 
                                WHERE Field!='id' AND Field!='pid' AND Field!='pos_id'");
    $allcarerows = array();
    /*
    echo "<select id='comboFields' name='comboFields' size='5' multiple>";
    while($allcarerows = sqlFetchArray($postyperes))
    {        
             //echo  $allcarerows['Field'] ."<input type='checkbox' name='chkFields[]' value='$allcarerows[Field]'>";
        echo "<option value='".$allcarerows['Field']."'>".$allcarerows['Field']."</option>";        
    }
    
    echo "</select>";    */
    
     echo "<div id='divFields' name='divFields' style='border:1px SOLID #000;height:150px;width:300px;overflow-y:scroll;'>";
    echo "<table border='1' style='width:275px'>";
    while($allcarerows = sqlFetchArray($postyperes))
    {        
         
        echo "<tr>
                <td id='".$allcarerows['Field']."'>
                    <input type='checkbox' id='chkbox_".$allcarerows['Field']."' name='chkAllFields' value='".$allcarerows['Field']."'>
                </td>
                <td>
                    <label for='chkbox_".$allcarerows['Field']."'>".$allcarerows['Field']."</label>
                </td>
              </tr>";    
               
    }
    echo "</table></div>";                

?>
