<script>
    
    function isNumber(evt)
{
    var charCode = (evt.which) ? evt.which : event.keyCode
    return !(charCode > 31 && (charCode < 48 || charCode > 57));
}

</script>    
<?php

require_once("../globals.php");
/*require_once("$srcdir/acl.inc");
require_once("$srcdir/options.inc.php");
require_once("$srcdir/patient.inc");
require_once("$srcdir/erx_javascript.inc.php");
*/

$POSid=$_REQUEST['POSid'];

$POSidArray = @split('_',$POSid);

///print_r($POSidArray);

//if($POSidArray[0]==1)
//{
   $showGroupSql ="SELECT fg.id, fg.POS_id, fg.Grouping_ID,fg.Grouping_Name, pt.title
                                        from tbl_allcare_patients1to1_fieldmapping fg
                                        INNER JOIN tbl_pos_types pt ON pt.id = fg.POS_id where POS_id=".$POSidArray[1]."
                                        group by fg.Grouping_ID";

   $showGroupQry =  sqlStatement($showGroupSql);
   
  // echo sqlNumRows($showGroupQry);

   
   if(sqlNumRows($showGroupQry)>0)
   {
   echo "<b><table border=0 width=60% class='label'>";
    while($POS1tonRes = sqlFetchArray($showGroupQry)) 
         {
            echo "<tr class='bold'><td align=left>Group : ";
            echo $POS1tonRes['Grouping_Name']."</td>";
            echo "</tr>";
            echo "<tr ><td>";
            echo "<table border=0 class='bold'>";
            $FieldSql ="SELECT fg.Table_ID,fg.POS_id, tb.Field_Name, tb.Table_Name
FROM tbl_allcare_tablemeta tb
INNER JOIN tbl_allcare_patients1to1_fieldmapping fg ON tb.Field_ID = fg.Field_ID
INNER JOIN tbl_pos_types pt ON pt.id = fg.POS_id and fg.Grouping_ID=".$POS1tonRes['Grouping_ID']." and fg.POS_id=".$POSidArray[1]."
group by fg.Grouping_ID,fg.Field_ID order by tb.Field_Name";
            
$FieldSqlQry =  sqlStatement($FieldSql);       
 while($FieldRes = sqlFetchArray($FieldSqlQry))     
{
     $columnTypeqry1 =  sqlStatement("SHOW COLUMNS FROM tbl_allcare_patients1to1 where Field='".$FieldRes['Field_Name']."'");
     while($columnTypeRe = sqlFetchArray($columnTypeqry1)) 
     {
         $validationpart1='';
         $defaultValue = ($columnTypeRe['Default']!='' ? $columnTypeRe['Default'] : '');

         $first3letters = substr($columnTypeRe['Type'], 0, 3) ; 
         
         if( ($first3letters=='int'  || $first3letters=='flo' || $first3letters =='boo'  ) )
         {
               $validationpart1= "onkeypress='return isNumber(event);'" ;
            
         }
                
       echo "<tr><td  width=30% >".$FieldRes['Field_Name']."</td>"
             . "<td width=70% ><input type='textbox' name=".$FieldRes['Field_Name']." maxlength='20' $validationpart1  value='$defaultValue' ></td></tr>";
              
                
     }
     
 }
echo "</table>";                     
echo "</td></tr><tr><td>&nbsp;</td></tr>";
         }
         
echo "</table></b>";         
         
   }
//}
//else
//{
    
    //echo "1ton".$POSidArray[1];
    
    $showRecordsetSql ="SELECT fg.id, fg.POS_id, fg.Recordset_ID, fg.Recordset_Name, pt.title
                                        from tbl_allcare_patients1ton_fieldmapping fg
                                        INNER JOIN tbl_pos_types pt ON pt.id = fg.POS_id where POS_id=".$POSidArray[1]."
                                        group by fg.Recordset_ID";

   $showRecordsetQry =  sqlStatement($showRecordsetSql);
   
  // echo sqlNumRows($showRecordsetQry);
   
   
   if(sqlNumRows($showRecordsetQry)>0)
   {
   echo "<table border=0 width=60% class='label'>";
    while($POS1tonRes = sqlFetchArray($showRecordsetQry)) 
         {
            echo "<tr  class='bold'><td align='left'>Recordset : ";
            echo $POS1tonRes['Recordset_Name']."</td>";
            echo "</tr>";
            echo "<tr><td>";
            echo "<table border=0 class='bold'  width='60%'>";
            $FieldSql ="SELECT fg.Table_ID,fg.POS_id, tb.Field_Name, tb.Table_Name
FROM tbl_allcare_tablemeta tb
INNER JOIN tbl_allcare_patients1ton_fieldmapping fg ON tb.Field_ID = fg.Field_ID
INNER JOIN tbl_pos_types pt ON pt.id = fg.POS_id and fg.Recordset_ID=".$POS1tonRes['Recordset_ID']." and fg.POS_id=".$POSidArray[1]."
group by fg.Recordset_ID,fg.Field_ID order by tb.Field_Name";
            
$FieldSqlQry =  sqlStatement($FieldSql);       
while($FieldRes = sqlFetchArray($FieldSqlQry))     
{
     
     $columnTypeqry =  sqlStatement("SHOW COLUMNS FROM tbl_allcare_patients1ton where Field='".$FieldRes['Field_Name']."'");
     while($columnTypeRes = sqlFetchArray($columnTypeqry)) 
     {
           $defaultValue = ($columnTypeRes['Default']!='' ? $columnTypeRes['Default'] : '');

           $validationpart='';
           
         $first3letters = substr($columnTypeRes['Type'], 0, 3) ; 
         
         if( ($first3letters=='int'  || $first3letters=='flo' || $first3letters =='boo'  ) )
         {
               $validationpart= "onkeypress='return isNumber(event);'" ;
            
         }

          
         echo "<tr><td  width=30% >".$FieldRes['Field_Name']."</td>"
             . "<td   width=70%><input type='textbox'  name=".$FieldRes['Field_Name']."  value='$defaultValue'  maxlength='20' $validationpart> </td></tr>";
     
     }
     
 }
echo "</table>";                     
echo "</td></tr><tr><td>&nbsp;</td></tr>";
         }
         
echo "</table>";         

   }
  
//}
?>
<input type="hidden" name="txtPOSid" id="txtPOSid"  value="<?php echo $POSidArray[1];?>">