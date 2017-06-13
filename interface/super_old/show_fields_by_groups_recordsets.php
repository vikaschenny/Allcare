<?php

require_once("../globals.php");
require_once("$srcdir/acl.inc");
require_once("$srcdir/formdata.inc.php");
require_once("$srcdir/globals.inc.php");
require_once("$srcdir/user.inc");
require_once("$srcdir/classes/CouchDB.class.php");

//echo 'sss='.$_POST['pos_id'];die;
$table_name=$_POST['table_name'];
$selectedGR=$_POST['selectedGR'];

$GR_name=($table_name=='tbl_allcare_patients1to1_fieldmapping') ? 'Grouping_Name' : 'Recordset_Name';
/*
$fieldsNames=sqlStatement("SELECT tmeta.field_ID,tmeta.field_Name FROM tbl_allcare_tablemeta tmeta 
                           INNER JOIN $table_name tfm ON tmeta.field_ID=tfm.Field_ID 
                           WHERE tfm.$GR_name='$selectedGR' ORDER BY tmeta.field_Name");
*/

$tbl_name=($table_name=='tbl_allcare_patients1to1_fieldmapping') ? 'tbl_allcare_patients1to1' : 'tbl_allcare_patients1ton';
$fieldsNames=sqlStatement("SHOW COLUMNS FROM $tbl_name
                           WHERE Field!='id' AND Field!='pid' AND Field!='pos_id'");

/*
echo "Field Selected : <select id='comboFieldsByGR' name='comboFieldsByGR'>";
    while($allcarerows = sqlFetchArray($postyperes))
    {        
          //echo  $allcarerows['Field'] ."<input type='checkbox' name='chkFields[]' value='$allcarerows[Field]'>";
        echo "<option value='".$allcarerows['Field']."'>".$allcarerows['Field']."</option>";        
    }
    
    echo "</select>";   
*/

    echo "<div id='divFieldsByGR' name='divFieldsByGR' style='border:1px SOLID #000;height:150px;width:300px;overflow-y:scroll;'>";
    //echo "<ul style='list-style-type: none;'>";
    echo "<table border='1' style='width:275px'>";
    while($rows = sqlFetchArray($fieldsNames))
    {        
          //echo  $allcarerows['Field'] ."<input type='checkbox' name='chkFields[]' value='$allcarerows[Field]'>";
        /*
        echo "<li id='".$rows['field_Name']."'>
              <input type='checkbox' id='chk".$rows['field_Name']."' name='chkSelectedFields' value=''>
              <label for='chk".$rows['field_Name']."'>".$rows['field_Name']."</label>
              </li>";        
         
         */
        
        echo "<tr>
                <td id='".$rows['Field']."'>
                    <input type='checkbox' id='chkbox_".$rows['Field']."' name='chkSelectedFields' value='".$rows['Field']."'>
                </td>
                <td>
                    <label for='chkbox_".$rows['Field']."'>".$rows['Field']."</label>
                </td>
              </tr>";    
               
    }
    echo "</table></div>";           
    $fieldsNames=sqlStatement("SELECT tmeta.field_ID,tmeta.field_Name FROM tbl_allcare_tablemeta tmeta 
                           INNER JOIN $table_name tfm ON tmeta.field_ID=tfm.Field_ID 
                           WHERE tfm.POS_id=$pos_id AND tfm.$GR_name='$selectedGR' ORDER BY tmeta.field_Name");
    
    while($rows = sqlFetchArray($fieldsNames))
    { 
    /*
        echo "<li id='".$rows['field_Name']."'>
              <input type='checkbox' id='chk".$rows['field_Name']."' name='chkSelectedFields' value=''>
              <label for='chk".$rows['field_Name']."'>".$rows['field_Name']."</label>
              </li>";        
        */
              echo "<script>
                        jQuery('#chkbox_".$rows['field_Name']."').attr('checked',true);
                        //jQuery('#chkbox".$rows['field_Name']."').attr('disabled','disabled');
                        //jQuery('#".$rows['field_Name']."').css('background','GREEN');
                    </script>";
         
         
    }
    
    //echo "</ul></div>";   
    

?>
