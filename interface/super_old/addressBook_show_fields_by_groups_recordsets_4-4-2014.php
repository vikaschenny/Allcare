<?php

require_once("../globals.php");
require_once("$srcdir/acl.inc");
require_once("$srcdir/formdata.inc.php");
require_once("$srcdir/globals.inc.php");
require_once("$srcdir/user.inc");
require_once("$srcdir/classes/CouchDB.class.php");

$table_name=$_POST['table_name'];
$selectedGR=$_POST['selectedGR'];

$GR_name=($table_name=='tbl_allcare_addressbook1to1_fieldmapping') ? 'Grouping_Name' : 'Recordset_Name';

$tbl_name=($table_name=='tbl_allcare_addressbook1to1_fieldmapping') ? 'tbl_allcare_addressbook1to1' : 'tbl_allcare_addressbook1ton';
$fieldsNames=sqlStatement("SHOW COLUMNS FROM $tbl_name
                           WHERE Field!='id' AND Field!='pid'");


echo "<div id='addressBook_divFieldsByGR' name='addressBook_divFieldsByGR' style='border:1px SOLID #000;height:150px;width:300px;overflow-y:scroll;'>";
//echo "<ul style='list-style-type: none;'>";
echo "<table border='1' style='width:275px'>";
while($rows = sqlFetchArray($fieldsNames))
{                 

    echo "<tr>
            <td id='".$rows['Field']."'>
                <input type='checkbox' id='addressBook_chkbox_".$rows['Field']."' name='addressBook_chkSelectedFields' value='".$rows['Field']."'>
            </td>
            <td>
                <label for='addressBook_chkbox_".$rows['Field']."'>".$rows['Field']."</label>
            </td>
          </tr>";    

}
echo "</table></div>";  


$fieldsNames=sqlStatement("SELECT tmeta.field_ID,tmeta.field_Name FROM tbl_allcare_tablemeta_addressbook tmeta 
                           INNER JOIN $table_name tfm ON tmeta.field_ID=tfm.Field_ID 
                           WHERE tfm.$GR_name='$selectedGR' ORDER BY tmeta.field_Name");

while($rows = sqlFetchArray($fieldsNames))
{ 
          echo "<script>
                    jQuery('#addressBook_chkbox_".$rows['field_Name']."').attr('checked',true);
                    //jQuery('#addressBook_chkbox".$rows['field_Name']."').attr('disabled','disabled');
                    //jQuery('#addressBook_".$rows['field_Name']."').css('background','GREEN');
                </script>";

}


?>
