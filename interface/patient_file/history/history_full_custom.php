<?php
/**
 *
 * Patient history form.
 *
 * LICENSE: This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 3
 * of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://opensource.org/licenses/gpl-license.php>;.
 *
 * @package OpenEMR
 * @author  Brady Miller <brady@sparmy.com>
 * @link    http://www.open-emr.org
 */

//SANITIZE ALL ESCAPES
$sanitize_all_escapes=true;
//

//STOP FAKE REGISTER GLOBALS
$fake_register_globals=false;
//

require_once("../../globals.php");
require_once("$srcdir/patient.inc");
require_once("history.inc.php");
require_once("$srcdir/acl.inc");
require_once("$srcdir/options.inc.php");

$CPR = 4; // cells per row

$pid=$_REQUEST['pid'];
$grp_stat=$_REQUEST['grp_stat'];
$form_id=$_REQUEST['form_id'];
$encounter=$_REQUEST['encounter'];

// Check authorization.
if (acl_check('patients','med')) {
  $tmp = getPatientData($pid, "squad");
  if ($tmp['squad'] && ! acl_check('squads', $tmp['squad']))
   die(htmlspecialchars(xl("Not authorized for this squad."),ENT_NOQUOTES));
}
if ( !acl_check('patients','med','',array('write','addonly') ))
  die(htmlspecialchars(xl("Not authorized"),ENT_NOQUOTES));
?>
<html>
<head>
<?php html_header_show();?>
<link rel="stylesheet" href="<?php echo $css_header ?>" type="text/css">

<style>
.control_label {
 font-family: Arial, Helvetica, sans-serif;
 font-size: 10pt;
}
</style>

<style type="text/css">@import url(../../../library/dynarch_calendar.css);</style>

<script type="text/javascript" src="../../../library/dialog.js"></script>
<script type="text/javascript" src="../../../library/textformat.js"></script>
<script type="text/javascript" src="../../../library/dynarch_calendar.js"></script>
<?php include_once("{$GLOBALS['srcdir']}/dynarch_calendar_en.inc.php"); ?>
<script type="text/javascript" src="../../../library/dynarch_calendar_setup.js"></script>

<script type="text/javascript" src="../../../library/js/jquery.1.3.2.js"></script>
<script type="text/javascript" src="../../../library/js/common.js"></script>

<script LANGUAGE="JavaScript">

var mypcc = '<?php echo $GLOBALS['phone_country_code'] ?>';

function divclick(cb, divid) {
 var divstyle = document.getElementById(divid).style;
 if (cb.checked) {
  divstyle.display = 'block';
 } else {
  divstyle.display = 'none';
 }
 return true;
}

// Compute the length of a string without leading and trailing spaces.
function trimlen(s) {
 var i = 0;
 var j = s.length - 1;
 for (; i <= j && s.charAt(i) == ' '; ++i);
 for (; i <= j && s.charAt(j) == ' '; --j);
 if (i > j) return 0;
 return j + 1 - i;
}

function validate(f) {
<?php generate_layout_validation('HIS'); ?>
 return true;
}

function submitme() {
 var f = document.forms[0];
 if (validate(f)) {
  //top.restoreSession();
  f.submit();
 }
}

function submit_history() {
   // top.restoreSession();
    document.forms[0].submit();
}

//function for selecting the smoking status in radio button based on the selection of drop down list.
function radioChange(rbutton)
{
     if (rbutton == 1 || rbutton == 2 || rbutton == 15 || rbutton == 16)
     {
     document.getElementById('radio_tobacco[current]').checked = true;
     }
     else if (rbutton == 3)
     {
     document.getElementById('radio_tobacco[quit]').checked = true;
     }
     else if (rbutton == 4)
     {
     document.getElementById('radio_tobacco[never]').checked = true;
     }
     else if (rbutton == 5 || rbutton == 9)
     {
     document.getElementById('radio_tobacco[not_applicable]').checked = true;
     }
     else if (rbutton == '')
     {
     var radList = document.getElementsByName('radio_tobacco');
     for (var i = 0; i < radList.length; i++) {
     if(radList[i].checked) radList[i].checked = false;
     }
     }
}

//function for selecting the smoking status in drop down list based on the selection in radio button.
function smoking_statusClicked(cb) 
{    
     if (cb.value == 'currenttobacco')
     {
     document.getElementById('form_tobacco').selectedIndex = 1;
     }
     else if (cb.value == 'nevertobacco')
     {
     document.getElementById('form_tobacco').selectedIndex = 4;
     }
     else if (cb.value == 'quittobacco')
     {
     document.getElementById('form_tobacco').selectedIndex = 3;
     }
     else if (cb.value == 'not_applicabletobacco')
     {
     document.getElementById('form_tobacco').selectedIndex = 6;
     }
}
</script>

<script type="text/javascript">
/// todo, move this to a common library
$(document).ready(function(){
    tabbify();
});
</script>

<style type="text/css">
div.tab {
	height: auto;
	width: auto;
}
</style>

</head>
<body class="body_top">

<?php
$result = getHistoryData($pid);
if (!is_array($result)) {
  newHistoryData($pid);
  $result = getHistoryData($pid);
}

$fres = sqlStatement("SELECT * FROM layout_options " .
  "WHERE form_id = 'HIS' AND uor > 0 " .
  "ORDER BY group_name, seq");
?>

<form action="history_save_custom.php?pid=<?php echo $pid; ?>&grpname=<?php echo $_REQUEST['grpname']; ?>&grp_stat=<?php echo $grp_stat; ?>&form_id=<?php echo $form_id; ?>&encounter=<?php echo $encounter; ?>" name='history_form' method='post' onsubmit='return validate(this)' >
    <input type='hidden' name='mode' value='save'>

    <div>
        <span class="title"><?php echo htmlspecialchars(xl('Patient History / Lifestyle'),ENT_NOQUOTES); ?></span>
    </div>
    <div style='float:left;margin-right:10px'>
  <?php echo htmlspecialchars(xl('for'),ENT_NOQUOTES);?>&nbsp;<span class="title"><?php echo htmlspecialchars(getPatientName($pid),ENT_NOQUOTES); ?></span>
    </div>
    <div>
        <a href="javascript:submit_history();" class='css_button'>
            <span><?php echo htmlspecialchars(xl('Save'),ENT_NOQUOTES); ?></span>
        </a>
        <a href="history_custom.php?pid=<?php echo $pid; ?>&grpname=<?php echo $_REQUEST['grpname']; ?>&form_id=<?php echo $form_id; ?>&grp_stat=<?php echo $grp_stat; ?>" <?php if (!$GLOBALS['concurrent_layout']) echo "target='Main'"; ?> class="css_button" onclick="top.restoreSession()">
            <span><?php echo htmlspecialchars(xl('Back To View'),ENT_NOQUOTES); ?></span>
        </a>
    </div>

    <br/>

    <?php
    $grname=str_replace("_", " " ,$_REQUEST['grpname']);
    function display_layout_tabs_custom($formtype, $grname,$result1, $result2='') {
      global $item_count, $cell_count, $last_group, $CPR;

  
  $fres = sqlStatement("SELECT distinct group_name FROM layout_options " .
    "WHERE form_id = ? AND uor > 0  AND group_name LIKE '%$grname%' " .
    "ORDER BY seq", array($formtype) );
    $frow = sqlFetchArray($fres);
      $this_group = $frow['group_name'];
      $group_name = substr($this_group, 1);
      ?>
    <li>
            <a href="/play/javascript-tabbed-navigation/" id="header_tab_<?php echo ".htmlspecialchars($group_name,ENT_QUOTES)."?>">
            <?php echo htmlspecialchars(xl_layout_label($group_name),ENT_NOQUOTES); ?></a>
    </li>
 <?php }   
 
 function display_layout_tabs_data_editable_custom($formtype,$grname, $result1, $result2='') {
  global $item_count, $cell_count, $last_group, $CPR;

  $fres = sqlStatement("SELECT distinct group_name FROM layout_options " .
    "WHERE form_id = ? AND uor > 0 AND group_name LIKE '%$grname%'  " .
    "ORDER BY seq", array($formtype) );

	$first = true;
	while ($frow = sqlFetchArray($fres)) {
		$this_group = $frow['group_name'];
		$group_name = substr($this_group, 1);
	        $group_name_esc = htmlspecialchars( $group_name, ENT_QUOTES);
		$titlecols  = $frow['titlecols'];
		$datacols   = $frow['datacols'];
		$data_type  = $frow['data_type'];
		$field_id   = $frow['field_id'];
		$list_id    = $frow['list_id'];
		$currvalue  = '';

		$group_fields_query = sqlStatement("SELECT * FROM layout_options " .
		"WHERE form_id = ? AND uor > 0 AND group_name = ? " .
		"ORDER BY seq", array($formtype,$this_group) );
	?>

		<div class="tab <?php echo $first ? 'current' : '' ?>" id="tab_<?php echo $group_name_esc?>" style="width:100%">
			<table border='0' cellpadding='0'>

			<?php
				while ($group_fields = sqlFetchArray($group_fields_query)) {

					$titlecols  = $group_fields['titlecols'];
					$datacols   = $group_fields['datacols'];
					$data_type  = $group_fields['data_type'];
					$field_id   = $group_fields['field_id'];
					$list_id    = $group_fields['list_id'];
					$currvalue  = '';

					if ($formtype == 'DEM') {
					  if ($GLOBALS['athletic_team']) {
						// Skip fitness level and return-to-play date because those appear
						// in a special display/update form on this page.
						if ($field_id === 'fitness' || $field_id === 'userdate1') continue;
					  }
					  if (strpos($field_id, 'em_') === 0) {
					// Skip employer related fields, if it's disabled.
						if ($GLOBALS['omit_employers']) continue;
						$tmp = substr($field_id, 3);
						if (isset($result2[$tmp])) $currvalue = $result2[$tmp];
					  }
					  else {
						if (isset($result1[$field_id])) $currvalue = $result1[$field_id];
					  }
					}
					else {
					  if (isset($result1[$field_id])) $currvalue = $result1[$field_id];
					}

					// Handle a data category (group) change.
					if (strcmp($this_group, $last_group) != 0) {
					  $group_name = substr($this_group, 1);
					  // totally skip generating the employer category, if it's disabled.
					  if ($group_name === 'Employer' && $GLOBALS['omit_employers']) continue;
					  $last_group = $this_group;
					}

					// Handle starting of a new row.
					if (($titlecols > 0 && $cell_count >= $CPR) || $cell_count == 0) {
					  disp_end_row();
					  echo "<tr>";
					}

					if ($item_count == 0 && $titlecols == 0) {
						$titlecols = 1;
					}

					// Handle starting of a new label cell.
					if ($titlecols > 0) {
					  disp_end_cell();
					  $titlecols_esc = htmlspecialchars( $titlecols, ENT_QUOTES);
					  echo "<td class='label' colspan='$titlecols_esc' ";
					  echo ">";
					  $cell_count += $titlecols;
					}
					++$item_count;

					// Added 5-09 by BM - Translate label if applicable
					if ($group_fields['title']) echo (htmlspecialchars( xl_layout_label($group_fields['title']), ENT_NOQUOTES).":"); else echo "&nbsp;";

					// Handle starting of a new data cell.
					if ($datacols > 0) {
					  disp_end_cell();
					  $datacols_esc = htmlspecialchars( $datacols, ENT_QUOTES);
					  echo "<td class='text data' colspan='$datacols_esc'";
					  echo ">";
					  $cell_count += $datacols;
					}

					++$item_count;
					echo generate_form_field($group_fields, $currvalue);
				  }
			?>

			</table>
		</div>
                
               

 	 <?php

	$first = false;

	}
        ?>
         <div class="tab " id="tab_POS">
        <table border="0" cellpadding="0" width="100%">

<tbody><tr>
        <td>
            
    
<?php


//  For groups 

//  End for groups

/*
echo  $existingPOSSsql = "select p2.pos_id,pt.title from tbl_allcare_patients1ton p2 
INNER JOIN tbl_pos_types pt ON pt.id = p2.pos_id
 and p2.pid=".$GLOBALS['pid']." group by p2.pos_id order by p2.id desc";
*/

$existingPOSSsql = "SELECT p2.pos_id,pt.title FROM tbl_allcare_patients1ton p2 
			INNER JOIN tbl_pos_types pt ON pt.id = p2.pos_id
			AND p2.pid=".$pid." 
			UNION
			SELECT p1.pos_id,pt.title FROM tbl_allcare_patients1to1 p1 
					    INNER JOIN tbl_pos_types pt ON pt.id = p1.pos_id
					    AND p1.pid=".$pid."";

     
 $existingPOSSQry =  sqlStatement($existingPOSSsql);
 $existingPOSSRows = sqlNumRows($existingPOSSQry);

 if($existingPOSSRows>0)
 {
     
     $_SESSION['group_id_array']=array();
     
     $totalGroupCountForThisPatient=0;
     
     echo "<p  style='font-size: 10pt;'>(Note: After Inline Editing please hit 'Enter' to save record.)</p>";
            //echo "<p>".$existingPOSSRes['title']."</p>";     
     
         while ($existingPOSSRes = sqlFetchArray($existingPOSSQry)) 
         {
		  //echo "<br><b>POS :".$existingPOSSRes['title']."</b>";
		  echo "<br><b>Patient's Attribute :".$existingPOSSRes['title']."</b>";
		  echo "<br><br>";

		$showGroupSql ="SELECT fg.id, fg.POS_id, fg.Grouping_ID,fg.Grouping_Name, pt.title
                                from tbl_allcare_patients1to1_fieldmapping fg
                                INNER JOIN tbl_pos_types pt ON pt.id = fg.POS_id 
                                where POS_id=".$existingPOSSRes['pos_id']."
                                group by fg.Grouping_ID";

        	$showGroupQry =  sqlStatement($showGroupSql);
   
        if(sqlNumRows($showGroupQry)>0)
        {  
                            
              echo "<table border='1'>";
              while($POS1to1Rec = sqlFetchArray($showGroupQry)) 
              {
			echo "<tr><td></td></tr>";
                  echo "<tr><td class='label' style='text-align:left' width='25%'>Group : </td><td class=data>".$POS1to1Rec['Grouping_Name']."</td></tr>";

		  $FieldSql =  "SELECT fg.Grouping_ID,fg.Table_ID,fg.POS_id, tb.Field_Name, tb.Table_Name
                                FROM tbl_allcare_tablemeta tb
                                INNER JOIN tbl_allcare_patients1to1_fieldmapping fg ON tb.Field_ID = fg.Field_ID
                                INNER JOIN tbl_pos_types pt ON pt.id = fg.POS_id and fg.Grouping_ID=".$POS1to1Rec['Grouping_ID']." and fg.POS_id=".$existingPOSSRes['pos_id']."
                                 group by fg.Grouping_ID,fg.Field_ID order by tb.Field_Name";
      
                  $groupFieldSqlQry =  sqlStatement($FieldSql);      
                 
                    if(sqlNumRows($groupFieldSqlQry)>0)
                    {
                                                                        
                             while($FieldRes = sqlFetchArray($groupFieldSqlQry))     
                             { 
                                 
                       //
				//$_SESSION['group_id_array'][$totalGroupCountForThisPatient]=array();
                       $_SESSION['group_id_array'][$totalGroupCountForThisPatient]=$FieldRes['Grouping_ID'];     
                 
				
                                 $field_number=0;
                                                                                         
                                 $field1to1Sql ="select ".$FieldRes['Field_Name']." from tbl_allcare_patients1to1 where pos_id=".$existingPOSSRes['pos_id']." and pid=".$pid; 
                                 $field1to1Qry = sqlStatement($field1to1Sql);

                                 while($field1to1Res = sqlFetchArray($field1to1Qry)) 
                                 {
                                     echo "<tr><td class='label' style='text-align:left' >".$FieldRes['Field_Name']." : </td>"
                                    . "<td class='text data'>
                                       <input type='text' id='txt_".$FieldRes['Field_Name']."' 
                                                          name='".$FieldRes['Field_Name']."'
                                                          value='".$field1to1Res[$FieldRes['Field_Name']]."'
                                               />
                                       </td></tr>";
                                     
			//$_SESSION['group_id_array'][$totalGroupCountForThisPatient][$field_number]=$field1to1Res[$FieldRes['Field_Name']];
                                     
                                    $field_number++;
                         
                                 }     

                             }  

				 $totalGroupCountForThisPatient++;

                    }              

              }
              
             
              
              
              echo "</table>";              

        }

//echo "<br>TGC = ".$totalGroupCountForThisPatient;
	echo "<br>";


//print_r($_SESSION['group_id_array']);


     echo '<div id="tabs'.$existingPOSSRes['pos_id'].'" class="class_tabs" style="font-size: 10pt;background:lightgrey;"><ul>';
     
          
            $findRecordsetSql = "SELECT fg.Recordset_ID,fg.Recordset_Name
                    from tbl_allcare_patients1ton_fieldmapping fg
                    INNER JOIN tbl_pos_types pt ON pt.id = fg.POS_id where POS_id=".$existingPOSSRes['pos_id']."
                    group by fg.Recordset_ID order by fg.Recordset_Name ";
            $findRecordsetQry =  sqlStatement($findRecordsetSql);
 $findRecordsetRows = sqlNumRows($findRecordsetQry);

 if($findRecordsetRows>0)
 {
     //echo "<ul>";
     $cnt=0;
      while ($existingRecorsetRes = sqlFetchArray($findRecordsetQry)) 
        {
            echo "<li><b><a href='#tabs-".$existingRecorsetRes['Recordset_ID']."'>".$existingRecorsetRes['Recordset_Name']."</a></<b></li>";
            $recorsetIdarray[$cnt] =$existingRecorsetRes['Recordset_ID'];
            $cnt++;
        }
   

     
 }
  echo '</ul>   ';
 foreach ($recorsetIdarray as $key => $value) {
      echo "<div id='tabs-".$value."'>";
      
      echo "<table cellpadding='0' cellspacing='0' border='0' class='display' id='example".$existingPOSSRes['pos_id'].$value."'>";
    
echo	'<thead>
    <tr  style="font-size: 10pt;background:lightgrey;">';
			
		
//echo "<br>==example$value";

 
$FieldSql ="SELECT fg.Table_ID,fg.POS_id, tb.Field_Name, tb.Table_Name
FROM tbl_allcare_tablemeta tb
INNER JOIN tbl_allcare_patients1ton_fieldmapping fg ON tb.Field_ID = fg.Field_ID
INNER JOIN tbl_pos_types pt ON pt.id = fg.POS_id and fg.Recordset_ID=".$value." and fg.POS_id=".$existingPOSSRes['pos_id']." 
group by fg.Recordset_ID,fg.Field_ID order by tb.Field_Name";
          //  echo $FieldSql;
            
	$RecordsetFieldNames='';

           $FieldSqlQry =  sqlStatement($FieldSql);       
	   if(sqlNumRows($FieldSqlQry)>0)
	   {
		    while($FieldRes = sqlFetchArray($FieldSqlQry))     
		    {
		        

		         $field1to1Sql ="select ".$FieldRes['Field_Name']." from  tbl_allcare_patients1ton 
		                         where pos_id=".$existingPOSSRes['pos_id']." and pid=".$pid; 
		         $field1to1Qry = sqlStatement($field1to1Sql);
		          $RecordsetFieldNames .= $FieldRes['Field_Name'].',';
		          
		          echo "<th width='auto'  style='text-align:left;font-size:0.8em;'>".$FieldRes['Field_Name']."</th>";

		     }
                     echo "<th></th>";
	    }
	
	echo '</tr></thead><tbody>';
        


	$RecordsetFieldNames = substr($RecordsetFieldNames,0,strlen($RecordsetFieldNames)-1);
	

	   if(sqlNumRows($FieldSqlQry)>0)
	   {
		     $RecSql = "select id,$RecordsetFieldNames from  tbl_allcare_patients1ton 
		    where pos_id=".$existingPOSSRes['pos_id']." and pid=".$pid." and Recordset_ID=".$value."";
		           		     		     
		     $RecordsetFieldNamesArray=explode(',',$RecordsetFieldNames);
		                  
		     $ResRecSql=  sqlStatement($RecSql);
		                  
		     while($rowRecSql=sqlFetchArray($ResRecSql))
		     {
		     //    print_r($rowRecSql);
		         echo "<tr id=".$rowRecSql['id']." style='font-size: 10pt;'>";
		         $field_number=0;                 
		         while($RecordsetFieldNamesArray[$field_number])
		         {                 
		             $textline = ($rowRecSql[$RecordsetFieldNamesArray[$field_number]]=='' ? '-':$rowRecSql[$RecordsetFieldNamesArray[$field_number]]);
		            echo "<td class='editable_class' id='".$RecordsetFieldNamesArray[$field_number]."'>".$textline."</td>";
		            $field_number++;
		         }
                          ?>
                         <td><input type="button" value="Remove" id="btn_remove_<?php echo $rowRecSql['id'];?>" 
                                          onclick="javascript:if(confirm('Are you sure to remove ?'))
                                                              {
                                                      deleteRecordsetRow(<?php echo $rowRecSql['id'];?>);
                                          //jQuery(this).closest('tr').remove();
                                          jQuery(this).parent('td').parent('tr#<?php echo $rowRecSql['id'];?>').hide();
				          jQuery(this).parent('td').parent('tr#<?php echo $rowRecSql['id'];?>').remove();
                                           }" />
                         </td>
                         <?php
		         echo "</tr>";
		     }                    
             }
             
             //echo "</table></td></tr>"; 
             echo "</tbody></table></div>"; 
	


 }
 

      
?>

  
 </div>                                        
              <?php
          
            
         }
 }

echo "<table border=0  class='label'><tr><td width=25% align=left class='fontbold'>Patient Attribute Type :</td>";
  echo "<td align='left'>";  
  echo "<select name='lstPOS' id='lstPOS' style='width:250px;' onchange='return showMapping(this.value);'>"
  . "<option value=0>- Patient Attribute Type -</option>";
  
  $POS1tonSql = "SELECT pt.id,pt.title from tbl_pos_types pt 
                    INNER JOIN tbl_allcare_patients1to1_fieldmapping fgn ON fgn.POS_id=pt.id
                    and pt.id not in(select pos_id from tbl_allcare_patients1to1 where pid=".$pid.")
                    group by pt.title order by pt.title";

  $POS1tonqry =  sqlStatement($POS1tonSql);

  
    while ($POS1tonRes = sqlFetchArray($POS1tonqry)) 
         {
            echo "<option value=n_".$POS1tonRes['id'].">".$POS1tonRes['title']."</option>";

         }
         
  $POS1to1Sql = "SELECT pt.id,pt.title from tbl_pos_types pt 
                    INNER JOIN tbl_allcare_patients1ton_fieldmapping fg1 ON fg1.POS_id=pt.id 
                    and pt.id not in(select fg2.POS_id 
                   from tbl_allcare_patients1to1_fieldmapping fg2 group by POS_id )
                   and pt.id not in(select pos_id from tbl_allcare_patients1ton where pid=".$pid.")
                    group by pt.title order by pt.title";

  $POS1to1qry =  sqlStatement($POS1to1Sql);

  
    while ($POS1to1Res = sqlFetchArray($POS1to1qry)) 
         {
            echo "<option value=1_".$POS1to1Res['id'].">".$POS1to1Res['title']."</option>";

         }
  
         echo "</select>" ;
         
    echo "</td></tr>";     
    
    echo "</table>";
      echo "<br/><div id='divResponse1'></div>";
?>

</td></tr>
    </tbody></table>
                </div>
<?php                
}

 ?>
 <div id='div_stat' ><div id="dvLoading" style="display:none; "></div></div>
<script>
    $(document).ready(function() {
        $('#dvLoading').show();
        $("#div_stat").load("history_status.php?grp_stat=<?php echo $grp_stat; ?>&mode=edit&form_id=<?php echo $_REQUEST['form_id']; ?>");
        $('#dvLoading').hide();
    });
</script> 
    <!-- history tabs -->
    <div id="HIS" style='float:none; margin-top: 10px; margin-right:20px'>
        <ul class="tabNav" >
           <?php display_layout_tabs_custom('HIS', $grname, $result, $result2); ?>
        </ul>
        <div class="tabContainer">
            <?php display_layout_tabs_data_editable_custom('HIS', $grname, $result, $result2); ?>
        </div>
    </div>
</form>

<!-- include support for the list-add selectbox feature -->
<?php include $GLOBALS['fileroot']."/library/options_listadd.inc"; ?>

</body>

<script language="JavaScript">
<?php echo $date_init; // setup for popup calendars ?>
</script>


</html>
