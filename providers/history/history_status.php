<?php

/*
echo "<br>post 55 ";
        print_r($_POST);
        echo "<br>post 77 ";die;*/

 // Copyright (C) 2011 Cassian LUP <cassi.lup@gmail.com>
 // 
 // Moved out of individual get_* portal functions for re-use by
 // Kevin Yeh (kevin.y@integralemr.com) May 2013
 //
 // This program is free software; you can redistribute it and/or
 // modify it under the terms of the GNU General Public License
 // as published by the Free Software Foundation; either version 2
 // of the License, or (at your option) any later version.
 // 
 // 
 
    // All of the common intialization steps for the get_* patient portal functions are now in this single include.

    //SANITIZE ALL ESCAPES
    $sanitize_all_escapes=true;

    //STOP FAKE REGISTER GLOBALS
    $fake_register_globals=false;

    //continue session
    session_start();

    //landing page definition -- where to go if something goes wrong
    $landingpage = "index.php?site=".$_SESSION['site_id'];	
    //

    // kick out if patient not authenticated
    //if ( isset($_SESSION['uid']) && isset($_SESSION['patient_portal_onsite']) ) {
    if ( isset($_SESSION['portal_username']) ) {    
    $provider = $_SESSION['portal_username'];
    }
    else {
            session_destroy();
    header('Location: '.$landingpage.'&w');
            exit;
    }
    //

$ignoreAuth=true; // ignore the standard authentication for a regular OpenEMR user
 include_once('../../interface/globals.php');
 require_once("$srcdir/patient.inc");
 require_once("history.inc.php");
 require_once("$srcdir/options.inc.php");
 require_once("$srcdir/acl.inc");
?>
<html>
<head>
<?php html_header_show();?>
<link rel="stylesheet" href="<?php echo $css_header;?>" type="text/css">
<script type="text/javascript" src="../../../library/js/jquery.1.3.2.js"></script>
<script type="text/javascript" src="../../../library/js/common.js"></script>

<script type="text/javascript">
$(document).ready(function(){
    tabbify();
});
</script>

<style type="text/css">
</style>

</head>
<body class="body_top">

<?php
$pid=$_REQUEST['pid'];
$provider=$_REQUEST['provider'];
$location=$_REQUEST['location'];
$grp_stat=str_replace("_", " " ,$_REQUEST['grp_stat']);
$form_id=$_REQUEST['form_id'];


function getHistoryStat($pid, $given = "*" ,$formid ,$gname)
{
         $res = sqlStatement("select lb.$given from lbf_data lb INNER JOIN layout_options l ON l.field_id=lb.field_id where lb.form_id='$formid' AND l.form_id='LBF2' AND l.group_name LIKE '%$gname%'  order by seq");
        while($frow2 = sqlFetchArray($res)){
            $resdata[$frow2['field_id']]=$frow2['field_value'];
        }
   

    return $resdata;
}

$result = getHistoryStat($pid,'*',$form_id,$grp_stat);

function display_layout_tabs_custom($formtype, $grp_stat,$result1, $result2='') {
  global $item_count, $cell_count, $last_group, $CPR;

  
  $fres = sqlStatement("SELECT distinct group_name FROM layout_options " .
    "WHERE form_id = ? AND uor > 0  AND group_name LIKE '%$grp_stat%' " .
    "ORDER BY seq", array($formtype) );

  //$first = true;
 // while ($frow = sqlFetchArray($fres)) {
      $frow = sqlFetchArray($fres);
	  $this_group = $frow['group_name'];
      $group_name = substr($this_group, 1);
      ?>
		<li>
			<a href="/play/javascript-tabbed-navigation/" id="header_tab_<?php echo ".htmlspecialchars($group_name,ENT_QUOTES)."?>">
                        <?php echo htmlspecialchars(xl_layout_label($group_name),ENT_NOQUOTES); ?></a>
		</li>
	  <?php
	  //$first = false;
 // }
  
  ?>
<!--                <li>
                    <a href="/play/javascript-tabbed-navigation/" id="header_tab_.htmlspecialchars(POS,ENT_QUOTES).">
                    Patient's Attributes</a>
                </li>-->
                <?php
}

function display_layout_tabs_data_custom($formtype, $grp_stat, $result1, $result2='') {
  global $item_count, $cell_count, $last_group, $CPR;

  $fres = sqlStatement("SELECT distinct group_name FROM layout_options " .
    "WHERE form_id = ? AND uor > 0 AND group_name LIKE '%$grp_stat%' " .
    "ORDER BY seq", array($formtype));

	$first = true;
	while ($frow = sqlFetchArray($fres)) {
		$this_group = isset($frow['group_name']) ? $frow['group_name'] : "" ;
		$titlecols  = isset($frow['titlecols']) ? $frow['titlecols'] : "";
		$datacols   = isset($frow['datacols']) ? $frow['datacols'] : "";
		$data_type  = isset($frow['data_type']) ? $frow['data_type'] : "";
		$field_id   = isset($frow['field_id']) ? $frow['field_id'] : "";
		$list_id    = isset($frow['list_id']) ? $frow['list_id'] : "";
		$currvalue  = '';

		$group_fields_query = sqlStatement("SELECT * FROM layout_options " .
		"WHERE form_id = ? AND uor > 0 AND group_name = ? " .
		"ORDER BY seq", array($formtype, $this_group) );
	?>

		<div class="tab <?php echo $first ? 'current' : '' ?>">
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
					 if (isset($result1[$field_id])) { 
                                               $currvalue = $result1[$field_id]; 
                                              
                                          }
                                          else {
                                              if($data_type==3){
                                                  $sql=sqlStatement("select * from list_options where list_id='AllCareEncFormsAutoText' AND option_id='$field_id' order by seq");
                                                  $def=sqlFetchArray($sql);
                                                $currvalue=$def['notes']; 
                                              }
                                          }
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
					if ($group_fields['title']) echo htmlspecialchars(xl_layout_label($group_fields['title']).":",ENT_NOQUOTES); else echo "&nbsp;";

					// Handle starting of a new data cell.
					if ($datacols > 0) {
					  disp_end_cell();
					  $datacols_esc = htmlspecialchars( $datacols, ENT_QUOTES);
					  echo "<td class='text data' colspan='$datacols_esc'";
					  echo ">";
					  $cell_count += $datacols;
					}

					++$item_count;
					echo generate_display_field($group_fields, $currvalue);
				  }
        disp_end_row();
			?>
                                    
			</table>
		</div>

 	 <?php

	$first = false;

	}
        ?>
        <div class="tab ">
            <table border='0' cellpadding='0' width="100%" align="center">
                <tr>
                <td>
                <?php 
                        echo allcare1t01po($pid);                
                ?>
                </td>
                </tr>
            </table>
        </div>
                
                            
        <?php

}

function display_layout_tabs_data_editable_custom($formtype,$grp_stat, $result1, $result2='') {
  global $item_count, $cell_count, $last_group, $CPR;

  $fres = sqlStatement("SELECT distinct group_name FROM layout_options " .
    "WHERE form_id = ? AND uor > 0 AND group_name LIKE '%$grp_stat%'  " .
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
					 if (isset($result1[$field_id])) { 
                                              $currvalue = $result1[$field_id]; 
                                              
                                          }
                                          else {
                                              if($data_type==3){
                                                  $sql=sqlStatement("select * from list_options where list_id='AllCareEncFormsAutoText' AND option_id='$field_id' order by seq");
                                                  $def=sqlFetchArray($sql);
                                                  $currvalue=$def['notes'];
                                              }
                                          }
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
} ?>

<div style='float:none; margin-top: 10px; margin-right:20px'>
    <table>
    <tr>
        <td>
            <!-- Demographics -->
            <div id="HIS">
                <ul class="tabNav">
                   <?php  $result2='';  display_layout_tabs_custom('LBF2', $grp_stat,$result, $result2); ?>
                </ul>
                <div class="tabContainer">
                   <?php  if($_REQUEST['mode']=='edit'){ 
                             display_layout_tabs_data_editable_custom('LBF2', $grp_stat, $result, $result2); 
                   }else {
                                $result2='';  display_layout_tabs_data_custom('LBF2',$grp_stat, $result, $result2);  
                       
                   }?>
                </div>
            </div>
        </td>
    </tr>
    </table>
</div>

</body>
</html>
