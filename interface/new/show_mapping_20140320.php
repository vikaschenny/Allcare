<script type="text/javascript">
    
    function isNumber(evt)
{
    var charCode = (evt.which) ? evt.which : event.keyCode
    return !(charCode > 31 && (charCode < 48 || charCode > 57));
}
/*
var i=1;
function addtablerow(tblid)
{
    alert(tblid);
    //var $curRow = $(this).closest('tr');
    //alert($('#'+tblid+'').innerHTML);
     //$('#'+tblid+'').append($(tblid + ' tr:last').clone());
          
//    alert($('tblRS2').innerHTML);
//    $('#'+tblid+' > tbody:last').append('<tr>...</tr><tr>...</tr>');

$('#'+tblid+').append($('#'+tblid+' tr:last').clone());
//         $('#'+tblid+' tr:last input').attr("name", $('#'+tblid+' tr:first input').attr("name")+i);
//         i++;    

}*/
    var i = 1;
function addtablerow(tblid,recid) 
{
    var prevValue = parseInt(document.getElementById('hiddenaddcount['+recid+']').value);
    
    document.getElementById('hiddenaddcount['+recid+']').value=prevValue+1;
    
   //$("#hiddenaddcount["+recid+"]").val( +$("#hiddenaddcount["+recid+"]").val() + 1 );
    
  $("#"+tblid+" tr:last").clone().find("input").each(function() {
    $(this).attr({
      'id': function(_, id) { return id},
      'name': function(_, name) { return name }
      /*,
      'value': function(_, value) { return value }               */
    });
  }).end().appendTo("#"+tblid);
  
  i++;
  
  $("#spnDelete").show();
}


deleteLastRow=function(tblid,recid)
{
//    $("#"+tblid).closest("tr").remove();
    //alert(tblid);
    
    var lastindex = $('#'+tblid).find('tr').index()+1;
    //alert(lastindex);

        if(lastindex>2)
        {
                if(confirm("Are you sure to delete last row?"))
                {

                    $('#'+tblid).find("tr").last().remove();
                    var prevValue = parseInt(document.getElementById('hiddenaddcount['+recid+']').value);
                    document.getElementById('hiddenaddcount['+recid+']').value=prevValue-1;

              }
        }
        
}
    

/*
function removeTableRow()
{
alert('tdId');
	//jQuery("#"+rowId).remove();
	jQuery(this).parent('td').parent('tr').remove();
	    return false;
}
*/

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
   echo "<b><table border=0 width=100% class='label'>";
    while($POS1tonRes = sqlFetchArray($showGroupQry)) 
         {
            echo "<tr class='bold'><td align='left'>Group : ";
            echo $POS1tonRes['Grouping_Name']."</td>";
            echo "</tr>";
            echo "<tr ><td>";
            echo "<table border=0 class='bold' width=100%>";
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
             . "<td width=70% >";
	

	if($columnTypeRe['Type']=='date' || $columnTypeRe['Type']=='datetime')
	{
		echo "<input type='textbox' id='".$FieldRes['Field_Name']."' name='".$FieldRes['Field_Name']."' maxlength='20'   value='$defaultValue' onkeyup='datekeyup(this,mypcc)' onblur='dateblur(this,mypcc)' title='yyyy-mm-dd last date of this event' />
<img src='../show_calendarimg.gif' align='absbottom' width='24' height='22'
id='img_calendar".$columnTypeRe['Field']."' border='0' alt='[?]' style='cursor:pointer;'
title='Click here to choose a date'>
<script>
Calendar.setup({inputField:'".$FieldRes['Field_Name']."', ifFormat:'%Y-%m-%d', button:'img_calendar".$columnTypeRe['Field']."'});
</script>";
	}
	else
	{
	echo "<input type='textbox' name='".$FieldRes['Field_Name']."' maxlength='20' $validationpart1  value='$defaultValue' >";
	}
              
                
     }
     
 }
echo "</td></tr></table>";                     
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
       $cnt=0;
   echo "<table border=0 width=100% class='label'>";
    while($POS1tonRes = sqlFetchArray($showRecordsetQry)) 
         {
            $RecsetID .= $POS1tonRes['Recordset_ID'].",";
            echo "<tr  class='bold'><td align='left'>Recordset : ";
            echo $POS1tonRes['Recordset_Name']."</td>";
            echo "</tr>";
            echo "<tr><td align=right>";?>  <a href='javascript:void(0);' onclick="javascript:addtablerow('tbl<?php echo $POS1tonRes['Recordset_ID'];?>','<?php echo $POS1tonRes['Recordset_ID'];?>');">Add</a>
<span id="spnDelete" onclick="javascript:deleteLastRow('tbl<?php echo $POS1tonRes['Recordset_ID'];?>','<?php echo $POS1tonRes['Recordset_ID'];?>');"><a id="idDeleteRow" href='javascript:void(0);' >Delete</a></span></td>

</tr>
            <?php echo "<tr><td>";
            echo "<table border=1 cellspacing=0 cellpadding=0 class='bold'  width='100%' id='tbl".$POS1tonRes['Recordset_ID']."'>";
            
            $FieldSql ="SELECT fg.Table_ID,fg.POS_id, tb.Field_Name, tb.Table_Name
FROM tbl_allcare_tablemeta tb
INNER JOIN tbl_allcare_patients1ton_fieldmapping fg ON tb.Field_ID = fg.Field_ID
INNER JOIN tbl_pos_types pt ON pt.id = fg.POS_id and fg.Recordset_ID=".$POS1tonRes['Recordset_ID']." and fg.POS_id=".$POSidArray[1]."
group by fg.Recordset_ID,fg.Field_ID order by tb.Field_Name";
            
$FieldSqlQry =  sqlStatement($FieldSql);       
$FieldSqlQry1 =  sqlStatement($FieldSql);       
echo "<tr>";

while($FieldResLabels = sqlFetchArray($FieldSqlQry))     
{
    echo "<td>".$FieldResLabels['Field_Name']."</td>";
    
     $RecordsetFieldNames .= $FieldResLabels['Field_Name'].',';
}
  $RecordsetFieldNames = substr($RecordsetFieldNames,0,strlen($RecordsetFieldNames)-1);

 echo "<input type='hidden' name='hiddenrecid[$POS1tonRes[Recordset_ID]]' value='$RecordsetFieldNames' />";
echo "<input type='hidden' name='hiddenaddcount[$POS1tonRes[Recordset_ID]]' id='hiddenaddcount[$POS1tonRes[Recordset_ID]]' value='1' />";
 
  $RecordsetFieldNames='';
echo "</tr><tr>";
while($FieldRes = sqlFetchArray($FieldSqlQry1))     
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

          
         //echo "<td>".$FieldRes['Field_Name']."</td>"
            echo  "<td align='left' valign='top'>";


	if($columnTypeRes['Type']=='date' || $columnTypeRes['Type']=='datetime')
	{/*
		echo "<input type='textbox' id='".$FieldRes['Field_Name']."' name='".$FieldRes['Field_Name']."[]' maxlength='20' $validationpart  value='$defaultValue' onkeyup='datekeyup(this,mypcc)' onblur='dateblur(this,mypcc)' title='yyyy-mm-dd last date of this event' />
<img src='../show_calendarimg.gif' align='absbottom' width='24' height='22'
id='img_calendar1".$columnTypeRes['Field']."' border='0' alt='[?]' style='cursor:pointer;'
title='Click here to choose a date'>
<script>
Calendar.setup({inputField:'".$FieldRes['Field_Name']."', ifFormat:'%Y-%m-%d', button:'img_calendar1".$columnTypeRes['Field']."'});
</script>";*/
echo "<input type='textbox' name='".$POS1tonRes['Recordset_ID']."[]' maxlength='20' placeholder='yyyy-mm-dd'  value='$defaultValue' >";            
	}
	else
	{

  	echo "<input type='textbox' name='".$POS1tonRes['Recordset_ID']."[]' maxlength='20' $validationpart  value='$defaultValue' >";
 
	}




echo " </td>";
     
     }
     
 }

//echo '<td><input type="button" value="Remove" onclick="javascript:jQuery(this).parent(\'td\').parent(\'tr\').remove();" /></td>';     

echo "</tr></table>";                     
echo "</td></tr><tr><td>&nbsp;</td></tr>";
        $cnt++;
         }
         
echo "</table>";         
     $RecsetID = substr($RecsetID,0,strlen($RecsetID)-1) ;
   }
  
//}

?>

<input type="hidden" name="hiddenRecsetID" id="hiddenrecsetID" value="<?php echo $RecsetID;?>">
<input type="hidden" name="txtPOSid" id="txtPOSid"  value="<?php echo $POSidArray[1];?>">
