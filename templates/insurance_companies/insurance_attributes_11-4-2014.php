<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */



?>
<script type='text/javascript'>
    
function isNumber(evt)
{
    var charCode = (evt.which) ? evt.which : event.keyCode
    return !(charCode > 31 && (charCode < 48 || charCode > 57));
}

var i = 1;
function addtablerow(tblid,recid) 
{
    var prevValue = parseInt(document.getElementById('hiddenaddcount['+recid+']').value);   
    document.getElementById('hiddenaddcount['+recid+']').value=prevValue+1;
    
   
    
  $('#'+tblid+' tr:last').clone().find('input').each(function() {
    $(this).attr({
      'id': function(_, id) { return id},
      'name': function(_, name) { return name }
      /*,
      'value': function(_, value) { return value }               */
    });
  }).end().appendTo('#'+tblid);
  
  i++;
    
}

function deleteRow(el,recid) { 

  // while there are parents, keep going until reach TR 
  while (el.parentNode && el.tagName.toLowerCase() != 'tr') 
  {
    el = el.parentNode;
  }

  // If el has a parentNode it must be a TR, so delete it
  // Don't delte if only 3 rows left in table
  if (el.parentNode && el.parentNode.rows.length > 2) 
  {
      var prevValue = parseInt(document.getElementById('hiddenaddcount['+recid+']').value);
                    //alert(prevValue);
                if(confirm('Are you sure to delete this row?'))
                {
                    el.parentNode.removeChild(el);
                    document.getElementById('hiddenaddcount['+recid+']').value=prevValue-1;
                }
  }
  else
  {      
      alert('Row can not be deleted');
  }
}

</script>

<?php
 // Copyright (C) 2006-2010 Rod Roark <rod@sunsetsystems.com>
 //
 // This program is free software; you can redistribute it and/or
 // modify it under the terms of the GNU General Public License
 // as published by the Free Software Foundation; either version 2
 // of the License, or (at your option) any later version.

 //SANITIZE ALL ESCAPES

 $sanitize_all_escapes=true;
 //

 //STOP FAKE REGISTER GLOBALS
 $fake_register_globals=false;
 //


 include_once("../../globals.php");
 include_once("../../sql.inc");
 include_once("$srcdir/acl.inc");
// $GLOBALS['OE_SITE_DIR']="../../library";

 include_once("$srcdir/options.inc.php");
 include_once("$srcdir/sqlconf.php");
 include_once("$srcdir/formdata.inc.php");
 include_once("$srcdir/htmlspecialchars.inc.php");

 
$host="mysql51-121.wc2.dfw1.stabletransit.com:3306";
//$host="192.168.100.196";
$username="551948_emrsbox";
$psw="Emrsb@321";
$db_live="551948_emrsbox";
$link=  mysql_connect($host,$username,$psw) or die(mysql_error());
$db = mysql_select_db($db_live,$link);

 
 // Collect user id if editing entry
 $userid = $_REQUEST['userid'];
 
 // Collect type if creating a new entry
 $type = $_REQUEST['type'];

 $info_msg = "";

 function invalue($name) {
  $fld = add_escape_custom(trim($_POST[$name]));
  return "'$fld'";
 }

?>

<title><?php echo $userid ? 'Edit':'Add New'; ?> <?php echo 'Person'; ?></title>
<link rel='stylesheet' href='<?php echo $css_header;?>' type='text/css'>
<script type='text/javascript' src='../../library/js/jquery.1.3.2.js'></script>

<style>
td { font-size:10pt; }

.inputtext {
 padding-left:2px;
 padding-right:2px;
}

.button {
 font-family:sans-serif;
 font-size:9pt;
 font-weight:bold;
}
</style>

<?php

    ?>
<?php
 // If we are saving, then save and close the window.
 //
/*
 if ($_POST['form_save']) {

 // Collect the form_abook_type option value
 //  (ie. patient vs company centric)
 $type_sql_row = sqlQuery("SELECT `option_value` FROM `list_options` WHERE `list_id` = 'abook_type' AND `option_id` = ?", array(trim($_POST['form_abook_type'])));
 $option_abook_type = $type_sql_row['option_value'];
 // Set up any abook_type specific settings
 if ($option_abook_type == 3) {
  // Company centric
  $form_title = invalue('form_director_title');
  $form_fname = invalue('form_director_fname');
  $form_lname = invalue('form_director_lname');
  $form_mname = invalue('form_director_mname');
 }
 else {
  // Person centric
  $form_title = invalue('form_title');
  $form_fname = invalue('form_fname');
  $form_lname = invalue('form_lname');
  $form_mname = invalue('form_mname');
 }

  if ($userid) 
  { 
      
      if($_POST['hiddensqlGroupRows']>0)
      {                    
          if($_POST['hiddenarrayGroup']!='')
          {
              $splitGroupArray = @split(",",$_POST['hiddenarrayGroup']);
                            
                foreach ($splitGroupArray as $spvalue) 
                {
                    $insertValues .= "'".$_POST[$spvalue]."',";
                    $updateValues .= "$spvalue='".$_POST[$spvalue]."',";

                }  
                
                $insertValues =substr($insertValues,0,strlen($insertValues)-1);
                $updateValues =substr($updateValues,0,strlen($updateValues)-1);
                
             $groupCheckQuery = mysql_query("select insurance_company_id from tbl_allcare_insurance1to1 where insurance_company_id=".add_escape_custom($userid));
             if(mysql_num_rows($groupCheckQuery)>0)
             {
                 
                $groupupdateSql1to1 ="update tbl_allcare_insurance1to1 set $updateValues where insurance_company_id =".add_escape_custom($userid);   
                
                mysql_query($groupupdateSql1to1);                
             }
             else
             {
                 $groupInsertSql1to1 ="insert into tbl_allcare_insurance1to1(insurance_company_id,".$_POST['hiddenarrayGroup'].") values(".add_escape_custom($userid).",$insertValues)";   
                
                mysql_query($groupInsertSql1to1);
             }                                       
          }

        }

       mysql_query("delete from tbl_allcare_insurance1ton where insurance_company_id=".$userid);
                  
        foreach ($_POST['hiddenaddcount'] as $key => $value) 
       {
         
              $rowsvalues=count($_POST[$key])/$value;
              
              $cnt3=0;
              for($cnt1=0;$cnt1<$value;$cnt1++)
                {
                  
                  $insertline='';
                    for($cnt2=0;$cnt2<$rowsvalues;$cnt2++)
                    {
                        
                          $insertline.=  "'".$_POST[$key][$cnt3]."'," ;
                          $cnt3++;
                    }                    
                  
                      $insertline = substr($insertline,0,strlen($insertline)-1) ;
                  
                      $insertSql = "insert into tbl_allcare_insurance1ton(insurance_company_id,Recordset_ID,".$_POST['hiddenrecid'][$key].") "
                            . "values ($userid,$key,$insertline)" ;
                      //  echo "<br>".$insertSql;
                $result = mysql_query($insertSql);
                 }                                       
       }            
  } 
  else
  {   
      if($_POST['hiddensqlGroupRows']>0)
      {          
          if($_POST['hiddenarrayGroup']!='')
          {
              
               $splitGroupArray = @split(",",$_POST['hiddenarrayGroup']);
                foreach ($splitGroupArray as $spvalue) 
                    {
                        $insertValues .= "'".$_POST[$spvalue]."',";
                    }  
                
                $insertValues =substr($insertValues,0,strlen($insertValues)-1);
                
                $groupInsertSql1to1 ="insert into tbl_allcare_insurance1to1(insurance_company_id,".$_POST['hiddenarrayGroup'].") values($userid,$insertValues)";   
                mysql_query($groupInsertSql1to1);
             
          }//
      } //
      
       foreach ($_POST['hiddenaddcount'] as $key => $value) 
       {
         
              $rowsvalues=count($_POST[$key])/$value;
              
              $cnt3=0;
              for($cnt1=0;$cnt1<$value;$cnt1++)
              {
                  
                    $insertline='';
                    for($cnt2=0;$cnt2<$rowsvalues;$cnt2++)
                    {
                        
                          $insertline.=  "'".$_POST[$key][$cnt3]."'," ;
                          $cnt3++;
                    }
                                      
                    $insertline = substr($insertline,0,strlen($insertline)-1) ;
                  
                    $insertSql = "insert into tbl_allcare_insurance1ton(insurance_company_id,Recordset_ID,".$_POST['hiddenrecid'][$key].") "
                            . "values ($userid,$key,$insertline)" ;
                        //echo "<br>".$insertSql;
                    $result = mysql_query($insertSql);
              }                                       
       }                
  }
 }

 else  if ($_POST['form_delete']) {

  if ($userid) {
   // Be careful not to delete internal users.
   mysql_query("DELETE FROM users WHERE id = ? AND username = ''", array($userid));
   
   
   mysql_query("DELETE FROM tbl_allcare_insurance1to1 WHERE insurance_company_id = $userid");
   mysql_query("DELETE FROM tbl_allcare_insurance1ton WHERE insurance_company_id = $userid");
  }

 }

 if ($_POST['form_save'] || $_POST['form_delete']) { 
  // Close this window and redisplay the updated list.
  echo "<script language='JavaScript'>\n";
  if ($info_msg) echo " alert('".addslashes($info_msg)."');\n";
  echo " window.close();\n";
  echo " if (opener.refreshme) opener.refreshme();\n";
  echo "</script></body></html>\n";
  exit();
 }

*/
?>

<?php 

echo '
<form method="post" name="theform" action="">
<center>
 
<table border="0" width="100%" bgcolor="#DDDDDD" id="table_insurance_attributes"> 
 
 <tr><td valign="top"><b>Groups:</b></td>
 <td>
            <table border="0">';

?>
 
         <?php               
         
         $getGroups=mysql_query("SELECT DISTINCT mg.Grouping_Name
				FROM tbl_allcare_insurance1to1_fieldmapping mg
				INNER JOIN tbl_allcare_tablemeta_insurance addr 
				ON addr.Field_ID=mg.Field_ID WHERE mg.Table_ID=1
				ORDER BY mg.Grouping_Name, addr.field_Name") or die("error:" . mysql_error());
$cnt=0;       

$sqlGroupRows = mysql_num_rows($getGroups);

if($sqlGroupRows>0)
{
    
         while($rowGroup=mysql_fetch_array($getGroups))
         {
             
             echo ($cnt==0 ? "" : "<tr><td colspan='2'><hr/></td></tr>");
             echo "<tr><td colspan=2><b>".$rowGroup['Grouping_Name']."</b></td></tr>";
             $cnt++;
            
		       
		$getGroupFields=mysql_query("SELECT mg.Grouping_ID,addr.field_Name
                                          FROM tbl_allcare_insurance1to1_fieldmapping mg
                                          INNER JOIN tbl_allcare_tablemeta_insurance addr 
                                          ON addr.Field_ID=mg.Field_ID 
                                          WHERE mg.Table_ID=1 AND mg.Grouping_Name='".$rowGroup['Grouping_Name']."'
                                          ORDER BY mg.Grouping_Name, addr.field_Name");
             
		

             while($rowGroupFields=mysql_fetch_array($getGroupFields))
             {
			
                 $columnTypeqry1 =  mysql_query("SHOW COLUMNS FROM tbl_allcare_insurance1to1 where Field='".$rowGroupFields['field_Name']."'");
                    while($columnTypeRe = mysql_fetch_array($columnTypeqry1)) 
                    {

                        $validationpart1='';
                       $defaultValue = ($columnTypeRe['Default']!='' ? $columnTypeRe['Default'] : '');
                       $first3letters = substr($columnTypeRe['Type'], 0, 3) ; 
                       if( ($first3letters=='int'  || $first3letters=='flo' || $first3letters =='boo'  || $first3letters=='big' ) )
                       {
                             $validationpart1= "onkeypress='return isNumber(event);'" ;

                       }

                        echo "<tr><td width='25%' align='left'><b>".$rowGroupFields['field_Name'].":</b></td>";
                        
                        $columnvalueqry1Rows=0;
                         if ($userid) 
                         {
                           $columnvalueqry1 =  mysql_query("select ".$rowGroupFields['field_Name']." from tbl_allcare_insurance1to1 where insurance_company_id=$userid");
                            $columnvalueqry1Rows = mysql_num_rows($columnvalueqry1);    
                         }   
                            if($columnvalueqry1Rows>0)
                            {
                               while($columnvalueRes = mysql_fetch_array($columnvalueqry1)) 
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
 


<?php

echo "
         </table>
        </td>
    </tr>
        <tr><td colspan='2'>&nbsp;</td></tr>
        <tr><td valign='top'><b>Recordsets:</b></td>
        <td valign='top'>
            <table border='0' width='100%'>";

?>
                
         <?php
        
        $getRecordSet = mysql_query("SELECT DISTINCT mg.Recordset_Name,mg.Recordset_ID
                                        FROM tbl_allcare_insurance1ton_fieldmapping mg
                                        INNER JOIN tbl_allcare_tablemeta_insurance addr 
                                        ON addr.Field_ID=mg.Recordset_ID WHERE mg.Table_ID=2
                                        ORDER BY mg.Recordset_Name, addr.field_Name");
        $sqlRecordsetRows = mysql_num_rows($getRecordSet);

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
                     echo "<table border='1' cellpadding='1' cellspacing='0' width='100%' 
id='tbl".$rowRecordSet['Recordset_ID']."' ><tr>";


                     $getRecordsetSql="SELECT mg.Recordset_Name,mg.Recordset_ID,addr.field_Name
                                    FROM tbl_allcare_insurance1ton_fieldmapping mg
                                    INNER JOIN tbl_allcare_tablemeta_insurance addr 
                                    ON addr.Field_ID=mg.Field_ID 
                                    WHERE mg.Table_ID=2  AND mg.Recordset_Name='".$rowRecordSet['Recordset_Name']."'
                                    ORDER BY mg.Recordset_Name, addr.field_Name";
                     $getRecordsetFields1=mysql_query($getRecordsetSql);
                     $getRecordsetFields=mysql_query($getRecordsetSql);
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
                    $updateRecorsetSql = "select $RecordsetFieldNames from tbl_allcare_insurance1ton where Recordset_ID=$rowRecordSet[Recordset_ID] and insurance_company_id=".$userid;

                    $updateRecorsetqry=mysql_query($updateRecorsetSql);
                     $updateRecorsetRows = mysql_num_rows($updateRecorsetqry);
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

                                  $columnTypeqry2 =  mysql_query("SHOW COLUMNS FROM tbl_allcare_insurance1ton where Field='".$splitNames[$cnt2]."'");
                                    while($columnTypeRes = mysql_fetch_array($columnTypeqry2)) 
                                   {    
                                         $validationpart1='';
                                         $first3letters = substr($columnTypeRes['Type'], 0, 3) ; 
                                              if( ($first3letters=='int'  || $first3letters=='flo' || $first3letters =='boo'  || $first3letters=='big' ) )
                                              {
                                                    $validationpart1= "onkeypress='return isNumber(event);'" ;

                                              }


                                       echo "<td>";
                                        echo "<input type='text' size='30'  value='".$updateRecorsetRes[$cnt1++]."' name='".$rowRecordSet['Recordset_ID']."[]' maxlength='20' $validationpart1 style='width:80%' class='inputtext' />";
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

                                $columnTypeqry2 =  mysql_query("SHOW COLUMNS FROM tbl_allcare_insurance1ton where Field='".$rowRecordsetFields['field_Name']."'");
                                   while($columnTypeRes = mysql_fetch_array($columnTypeqry2)) 
                                   {
                                               $validationpart1='';
                                              $defaultValue = ($columnTypeRes['Default']!='' ? $columnTypeRes['Default'] : '');
                                              $first3letters = substr($columnTypeRes['Type'], 0, 3) ; 
                                              if( ($first3letters=='int'  || $first3letters=='flo' || $first3letters =='boo'  ) )
                                              {
                                                    $validationpart1= "onkeypress='return isNumber(event);'" ;

                                              }

                                         echo "<td>";
                                         echo "<input type='text' size='30'  value='$defaultValue' name='".$rowRecordsetFields['Recordset_ID']."[]' maxlength='20' $validationpart1 style='width:80%' class='inputtext' />";


                                         echo "</td>";                              

                                    }
                            }

                            echo '<td><input type="button" value="Remove" onclick="deleteRow(this,'.$rowRecordSet['Recordset_ID'].');" /></td>';     
                            echo "</tr></table>";     

                    }

        $RecordsetFieldNames='';

                     echo "</td></tr>";


                 } 


           }      


         ?>     
                     
<?php

echo "</table>
             </td>
        </tr>
  
</table>

<br />

<input type='hidden' name='hiddensqlGroupRows' value='$sqlGroupRows' >
<input type='hidden' name='hiddenarrayGroup' value='$GroupFieldNames' >

";

?>
                     
<?php if ($userid && !$row['username']) {

echo "<input type='submit' name='form_delete' value='<?php echo xla('Delete'); ?>' style='color:red' />";
 } 


echo "
</center>
</form>";

