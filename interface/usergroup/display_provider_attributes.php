<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once("../globals.php");
require_once("../../library/acl.inc");
require_once("$srcdir/sql.inc");
require_once("$srcdir/calendar.inc");
require_once("$srcdir/formdata.inc.php");
require_once("$srcdir/options.inc.php");
require_once(dirname(__FILE__) . "/../../library/classes/WSProvider.class.php");
require_once("$srcdir/erx_javascript.inc.php");



?>

 <tr><td valign="top"><b><?php echo xlt('Groups'); ?>:</b></td>
        <td>
                   <table border="0" width="100%">

                <?php

                $getGroups=sqlStatement("SELECT DISTINCT mg.Grouping_Name
       FROM tbl_allcare_provider1to1_fieldmapping mg
       INNER JOIN tbl_allcare_tablemeta_provider addr 
       ON addr.Field_ID=mg.Field_ID AND mg.Table_ID=1
       ORDER BY mg.Grouping_Name, addr.field_Name");
       $cnt=0;       
       $sqlGroupRows = sqlNumRows($getGroups);

       if($sqlGroupRows>0)
       {

                while($rowGroup=mysql_fetch_array($getGroups))
                {

                    echo ($cnt==0 ? "" : "<tr><td colspan='2'><hr/></td></tr>");
                    echo "<tr><td colspan=2><b>".$rowGroup['Grouping_Name']."</b></td></tr>";
                   $cnt++;

                    $getGroupFields=sqlStatement("SELECT mg.Grouping_ID,addr.field_Name
                                               FROM tbl_allcare_provider1to1_fieldmapping mg
                                               INNER JOIN tbl_allcare_tablemeta_provider addr 
                                               ON addr.Field_ID=mg.Field_ID
                                               AND mg.Table_ID=1 AND mg.Grouping_Name='".$rowGroup['Grouping_Name']."'
                                               ORDER BY mg.Grouping_Name, addr.field_Name");


                    while($rowGroupFields=mysql_fetch_array($getGroupFields))
                    {
                        $columnTypeqry1 =  sqlStatement("SHOW COLUMNS FROM tbl_allcare_provider1to1 where Field='".$rowGroupFields['field_Name']."'");
                           while($columnTypeRe = sqlFetchArray($columnTypeqry1)) 
                           {
                               $validationpart1='';
                              $defaultValue = ($columnTypeRe['Default']!='' ? $columnTypeRe['Default'] : '');
                              $first3letters = substr($columnTypeRe['Type'], 0, 3) ; 
                              if( ($first3letters=='int'  || $first3letters=='flo' || $first3letters =='boo'  || $first3letters=='big' ) )
                              {
                                    $validationpart1= "onkeypress='return isNumber(event);'" ;

                              }

                               echo "<tr><td width='25%' align=left><b>".$rowGroupFields['field_Name'].":</b></td>";

                               $columnvalueqry1Rows=0;
                                if ($userid) 
                                {
                                    //echo "select ".$rowGroupFields['field_Name']." from tbl_allcare_provider1to1 where pid=".$userid;
                                  $columnvalueqry1 =  sqlStatement("select ".$rowGroupFields['field_Name']." from tbl_allcare_provider1to1 where provider_id=$userid");
                                   $columnvalueqry1Rows = sqlNumRows($columnvalueqry1);    
                                }   
                                   if($columnvalueqry1Rows>0)
                                   {
                                      while($columnvalueRes = sqlFetchArray($columnvalueqry1)) 
                                       {  
                                           echo "<td align='left'>";
                                           echo "<input type='text' size='40'  value='".$columnvalueRes[$rowGroupFields['field_Name']]."' name='".$rowGroupFields['field_Name']."' maxlength='20' $validationpart1 style='width:100%' class='inputtext' />";
                                           echo "</td></tr>";
                                        }

                                }
                                else
                                {
                                   echo "<td align='left'>"
                                       . "<input type='text' size='40'  value='$defaultValue' name='".$rowGroupFields['field_Name']."' maxlength='20' $validationpart1 style='width:100%' class='inputtext' />"
                                       . "</td></tr>";

                                }

                               $GroupFieldNames .= $rowGroupFields['field_Name'].',';
                           }
                    }

                }

               $GroupFieldNames = substr($GroupFieldNames,0,strlen($GroupFieldNames)-1);
         }       
         
       
                ?>
                </table>
        </td>
        </tr>
        <tr><td colspan='2'>&nbsp;</td></tr>
        <tr><td valign="top"><b><?php echo xlt('Recordsets'); ?>:</b></td>
        <td valign="top">
            <table border="0" width="100%">
                
         <?php
         
         $getRecordSet = sqlStatement("SELECT DISTINCT mg.Recordset_Name,mg.Recordset_ID
                                        FROM tbl_allcare_provider1ton_fieldmapping mg
                                        INNER JOIN tbl_allcare_tablemeta_provider addr 
                                        ON addr.Field_ID=mg.Recordset_ID 
                                        AND mg.Table_ID=2
                                        ORDER BY mg.Recordset_Name, addr.field_Name");
        $sqlRecordsetRows = sqlNumRows($getRecordSet);

if($sqlRecordsetRows>0)
{
         
         $cnt=0;
         while($rowRecordSet=mysql_fetch_array($getRecordSet))
         {
              
              //echo ($cnt==0 ? "" : "<tr><td colspan='2'><hr/></td></tr>");
             echo "<tr><td><b>".$rowRecordSet['Recordset_Name']."</b></td>";
             echo "<td align='right'>";?>
             <a href='javascript:void(0);' onclick="javascript:addtablerow('tbl<?php echo $rowRecordSet['Recordset_ID'];?>','<?php echo $rowRecordSet['Recordset_ID'];?>');">Add</a>
             <?php
             echo "</td></tr>";
            //$cnt++;
             echo "<tr><td colspan=2>";
             echo "<table border='1' cellpadding='1' cellspacing='0' width='100%' id='tbl".$rowRecordSet['Recordset_ID']."' ><tr>";
             
           
             $getRecordsetSql="SELECT mg.Recordset_Name,mg.Recordset_ID,addr.field_Name
                                            FROM tbl_allcare_provider1ton_fieldmapping mg
                                            INNER JOIN tbl_allcare_tablemeta_provider addr 
                                            ON addr.Field_ID=mg.Field_ID 
                                            AND mg.Table_ID=2  AND mg.Recordset_Name='".$rowRecordSet['Recordset_Name']."'
                                            ORDER BY mg.Recordset_Name, addr.field_Name";
             $getRecordsetFields1=sqlStatement($getRecordsetSql);
             $getRecordsetFields=sqlStatement($getRecordsetSql);
             $colcount=0;
             while($rowRecordsetFields1=mysql_fetch_array($getRecordsetFields1))
             {
                 
                         echo "<td width='25%' align=left><b>".$rowRecordsetFields1['field_Name'].":</b></td>";
                          $RecordsetFieldNames .= $rowRecordsetFields1['field_Name'].',';
                          $colcount++;
                     
             }
             echo "<td>&nbsp;</td></tr>";
             
              $RecordsetFieldNames = substr($RecordsetFieldNames,0,strlen($RecordsetFieldNames)-1);
            echo "<input type='hidden' name='hiddenrecid[$rowRecordSet[Recordset_ID]]' value='$RecordsetFieldNames' />";
            
            
            $updateRecorsetRows=0;
            if($userid){
            $updateRecorsetSql = "select $RecordsetFieldNames from tbl_allcare_provider1ton where Recordset_ID=$rowRecordSet[Recordset_ID] and provider_id=".$userid;
            
            $updateRecorsetqry=sqlStatement($updateRecorsetSql);
             $updateRecorsetRows = sqlNumRows($updateRecorsetqry);
            }
            if($updateRecorsetRows>0)
            {
                echo "<input type='hidden' name='hiddenaddcount[$rowRecordSet[Recordset_ID]]' id='hiddenaddcount[$rowRecordSet[Recordset_ID]]' value='".$updateRecorsetRows."' />";
                $cnt1=0;
                while($updateRecorsetRes=mysql_fetch_array($updateRecorsetqry))
                    {
                   // echo "<pre>";print_r($updateRecorsetRes);echo "</pre>";
                    echo "<tr>";
                      for ($cnt2=0;$cnt2<$colcount;$cnt2++)
                      {
                          
                          $splitNames = explode(",", $RecordsetFieldNames);
                          
                          $columnTypeqry2 =  sqlStatement("SHOW COLUMNS FROM tbl_allcare_provider1ton where Field='".$splitNames[$cnt2]."'");
                           while($columnTypeRes = sqlFetchArray($columnTypeqry2)) 
                           {    
                                 $validationpart1='';
                                 $first3letters = substr($columnTypeRes['Type'], 0, 3) ; 
                                      if( ($first3letters=='int'  || $first3letters=='flo' || $first3letters =='boo'  || $first3letters=='big' ) )
                                      {
                                            $validationpart1= "onkeypress='return isNumber(event);'" ;

                                      }
                                 
                            
                               echo "<td>";
                                echo "<input type='text' value='".$updateRecorsetRes[$cnt1++]."' name='".$rowRecordSet['Recordset_ID']."[]' maxlength='20' $validationpart1 style='width:80%' class='inputtext' />";
                                echo "</td>";
                                
                           }
                           
                      }
                         echo '<td><input type="button" value="Remove" onclick="deleteRow(this,'.$rowRecordSet['Recordset_ID'].');" /></td>';     
                        $cnt1=0;
                        
                        echo "</tr>";
                    }
                    
                    echo "</table>";     
            
            }
            else
            {
                echo "<input type='hidden' name='hiddenaddcount[$rowRecordSet[Recordset_ID]]' id='hiddenaddcount[$rowRecordSet[Recordset_ID]]' value='1' />";
                
                echo "<tr>";
                    while($rowRecordsetFields=mysql_fetch_array($getRecordsetFields))
                    {

                        $columnTypeqry2 =  sqlStatement("SHOW COLUMNS FROM tbl_allcare_provider1ton where Field='".$rowRecordsetFields['field_Name']."'");
                           while($columnTypeRes = sqlFetchArray($columnTypeqry2)) 
                           {
                                       $validationpart1='';
                                      $defaultValue = ($columnTypeRes['Default']!='' ? $columnTypeRes['Default'] : '');
                                      $first3letters = substr($columnTypeRes['Type'], 0, 3) ; 
                                      if( ($first3letters=='int'  || $first3letters=='flo' || $first3letters =='boo'  ) )
                                      {
                                            $validationpart1= "onkeypress='return isNumber(event);'" ;

                                      }

                                 echo "<td>";
                                 echo "<input type='text' value='$defaultValue' name='".$rowRecordsetFields['Recordset_ID']."[]' maxlength='20' $validationpart1 style='width:80%' class='inputtext' />";

                                 echo "</td>";
                               
                            }
                    }
                    
                    echo '<td><input type="button" value="Remove" onclick="deleteRow(this,'.$rowRecordSet['Recordset_ID'].');" /></td>';     
                    echo "</tr></table>";     
            
            }
            
            
$RecordsetFieldNames='';

             echo "</td></tr>";
             
            
         } //==
         
        
   }      
                          
         ?>
            
            </table>
            <input type="hidden" name="hiddensqlGroupRows_provider" value="<?php echo $sqlGroupRows;?>" >
<input type="hidden" name="hiddenarrayGroup_provider" value="<?php echo $GroupFieldNames;?>" >
             </td>
        </tr>
  


