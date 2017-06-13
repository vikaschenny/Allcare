<?php

function allcare1t01po($pid)
{
//    echo "<div style='overflow-x: scroll; width:50%;'>";
     $pos1to1Sql ="select p1.pos_id,pt.title from tbl_allcare_patients1to1 p1 
                            INNER JOIN tbl_pos_types pt ON pt.id = p1.pos_id
                            and p1.pid=".$pid." UNION "
           . "
select p2.pos_id,pt.title from tbl_allcare_patients1ton p2 
INNER JOIN tbl_pos_types pt ON pt.id = p2.pos_id
 and p2.pid=$pid";
   
   // $pos1to1Title = sqlStatement($pos1to1Sql);    $pos1to1TitleRes = sqlFetchArray($pos1to1Title);
    
    
   $pos1to1Qry = sqlStatement($pos1to1Sql);
  
  

   
   if(sqlNumRows($pos1to1Qry)>0)
   {

    while($POS1to1Res = sqlFetchArray($pos1to1Qry)) 
     {
       echo "<br><label class='label'>POS : </label><label class='data'>".$POS1to1Res['title']."</label>";
       
echo "<table border='0' cellpadding=0 >";       
//echo "<tr><td colspan=2>&nbsp;</td></tr>";
//echo "<tr><td class='label'  width='40%'>POS : </td><td  width='60%' class='data'>".$POS1to1Res['title']."</td></tr>";
        
         $showGroupSql ="SELECT fg.id, fg.POS_id, fg.Grouping_ID,fg.Grouping_Name, pt.title
                                        from tbl_allcare_patients1to1_fieldmapping fg
                                        INNER JOIN tbl_pos_types pt ON pt.id = fg.POS_id where POS_id=".$POS1to1Res['pos_id']."
                                        group by fg.Grouping_ID";

   $showGroupQry =  sqlStatement($showGroupSql);
   
   
   if(sqlNumRows($showGroupQry)>0)
   {
//echo "<tr><td colspan=2>&nbsp;</td></tr>";



    while($POS1to1Rec = sqlFetchArray($showGroupQry)) 
         {


           echo "<tr><td class='label' style='text-align:left' width='25%'>Group : </td><td class=data>".$POS1to1Rec['Grouping_Name']."</td></tr>";
           
             $FieldSql ="SELECT fg.Table_ID,fg.POS_id, tb.Field_Name, tb.Table_Name
FROM tbl_allcare_tablemeta tb
INNER JOIN tbl_allcare_patients1to1_fieldmapping fg ON tb.Field_ID = fg.Field_ID
INNER JOIN tbl_pos_types pt ON pt.id = fg.POS_id and fg.Grouping_ID=".$POS1to1Rec['Grouping_ID']." and fg.POS_id=".$POS1to1Res['pos_id']."
group by fg.Grouping_ID,fg.Field_ID order by tb.Field_Name";
            
$FieldSqlQry =  sqlStatement($FieldSql);       

 while($FieldRes = sqlFetchArray($FieldSqlQry))     
{
   

      $field1to1Sql ="select ".$FieldRes['Field_Name']." from  tbl_allcare_patients1to1 where pos_id=".$POS1to1Res['pos_id']." and pid=".$pid; 
     $field1to1Qry = sqlStatement($field1to1Sql);
   
        while($field1to1Res = sqlFetchArray($field1to1Qry)) 
          {
              echo "<tr><td class='label'   style='text-align:left' >".$FieldRes['Field_Name']." : </td>"
             . "<td class='text data'>".$field1to1Res[$FieldRes['Field_Name']]."</td></tr>";
          }     
 }
//echo "</table>";                     

         }
         echo "<tr><td colspan=2>&nbsp;</td></tr>";

       
       }
   
       
       
           $showGroupSql ="SELECT fg.id, fg.POS_id, fg.Recordset_ID,fg.Recordset_Name, pt.title
                                        from tbl_allcare_patients1ton_fieldmapping fg
                                        INNER JOIN tbl_pos_types pt ON pt.id = fg.POS_id where POS_id=".$POS1to1Res['pos_id']."
                                        group by fg.Recordset_ID";
           
           

   $showGroupQry =  sqlStatement($showGroupSql);
   
   
   if(sqlNumRows($showGroupQry)>0)
   {

         while($POS1to1Rec = sqlFetchArray($showGroupQry)) 
         {
		echo "<tr><td>&nbsp;</td></tr>";
           echo "<tr id='tr_recordset_name_".$POS1to1Rec['Recordset_Name']."'>
                        <td class='label' style='text-align:left;width:80px'>Recordset:</td>
                        <td class='data' style='text-align:left;width:auto'>".$POS1to1Rec['Recordset_Name']."</td>
                 </tr>";
           
             $FieldSql ="SELECT fg.Table_ID,fg.POS_id, tb.Field_Name, tb.Table_Name
FROM tbl_allcare_tablemeta tb
INNER JOIN tbl_allcare_patients1ton_fieldmapping fg ON tb.Field_ID = fg.Field_ID
INNER JOIN tbl_pos_types pt ON pt.id = fg.POS_id and fg.Recordset_ID=".$POS1to1Rec['Recordset_ID']." and fg.POS_id=".$POS1to1Res['pos_id']." 
group by fg.Recordset_ID,fg.Field_ID order by tb.Field_Name";
            //echo $FieldSql;
           $FieldSqlQry =  sqlStatement($FieldSql);       

	$RecordsetFieldNames='';
           echo "<tr><td colspan='2'>
                       <table id='tblFields_".$POS1to1Rec['Recordset_Name']."' border='1' cellspacing=0 cellpadding=0 >";

            while($FieldRes = sqlFetchArray($FieldSqlQry))     
            {
                

                 $field1to1Sql ="select ".$FieldRes['Field_Name']." from  tbl_allcare_patients1ton 
                                 where pos_id=".$POS1to1Res['pos_id']." and pid=".$pid; 
                 $field1to1Qry = sqlStatement($field1to1Sql);
               /*
                    while($field1to1Res = sqlFetchArray($field1to1Qry)) 
                      {
                          echo "<tr><td width='40%' class='label'  >".$FieldRes['Field_Name']." : </td>"
                         . "<td width='60%' class='text data'>".$field1to1Res[$FieldRes['Field_Name']]."</td></tr>";
                      }     
                */
                  $RecordsetFieldNames .= $FieldRes['Field_Name'].',';
                  
                  echo "<th width='auto'  style='text-align:left;font-size:0.8em;'>".$FieldRes['Field_Name']."</th>";


             }

		$RecordsetFieldNames = substr($RecordsetFieldNames,0,strlen($RecordsetFieldNames)-1);
	
            //echo "<br>rfn=".$RecordsetFieldNames;
             $RecSql = "select $RecordsetFieldNames from  tbl_allcare_patients1ton 
            where pid=".$pid." and pos_id=".$POS1to1Res['pos_id']."  and Recordset_ID=".$POS1to1Rec['Recordset_ID'];
                    
            
             
             
             $RecordsetFieldNamesArray=explode(',',$RecordsetFieldNames);
                          
             $ResRecSql=  sqlStatement($RecSql);
                          
             while($rowRecSql=sqlFetchArray($ResRecSql))
             {
                 echo "<tr>";
                 $field_number=0;                 
                 while($RecordsetFieldNamesArray[$field_number])
                 {                 
                    echo "<td>".$rowRecSql[$RecordsetFieldNamesArray[$field_number]]."</td>";
                    $field_number++;
                 }
                 echo "</tr>";
             }                    
             
             
             echo "</table></td></tr>"; 
	
	//if(sqlNumRows($FieldSqlQry1)==0 || empty($num_field_array[0]))
             
             /*
		if($num_field_array[0][0]==0)	
		{
		echo "<script type='text/javascript'>
			      document.getElementById('tblFields_".$POS1to1Rec['Recordset_Name']."').style.display='none';
			      document.getElementById('tr_recordset_name_".$POS1to1Rec['Recordset_Name']."').style.display='none';
		      </script>";
		}       
                */
		
	
         }
         echo "</table>";         
         
   }
       
       }
       
    } // while pos rows 
    
    /*
       $pos1tonQry = sqlStatement($pos1to1Sql);
    // for 1 to n
    while($POS1tonRes = sqlFetchArray($pos1tonQry)) 
     {
        
   
    }*/
//    echo "</div>";
}


?>
