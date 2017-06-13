<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/ajtooltip.js"></script>
<script>
function efmouseover(elem,rowid) {
    textcomment=$("#hidden"+rowid).val();

 ttMouseOver(elem, "viewFacilityComment.php?comment=" + textcomment);
}
    function facilityformview(mode,rowid,name,admitdate,dischargedate,isactive,doclinks)
    {
        //facilitynotes=<?php //echo base64_decode(facilitynotes);?>
        
        if(mode=='showform')
            {
                $("#tblFacilityList").hide();
                $("#tblFacility").show();
               
               if(rowid!=0)
                   {
                        $("#hideFacilityStatus").val('edit');
                        $("#hideFacilityformid").val(rowid);
                        $("#selectFacility").val(name);
                        
                        $("#admitdate").val(admitdate);
                        $("#dischargedate").val(dischargedate);
                        $("#dischargedate").val(dischargedate);
                        $("#facilitynotes").val($("#hidden"+rowid).val());
                        $("#facilitydoclinks").val(doclinks);
                        
                        
                        if(isactive==1)
                            {
                                $('#chkFacilityActive').attr('checked','checked');
                            }
                            else
                            {
                                $('#chkFacilityActive').removeAttr('checked');
                            }

                    $('#idFacilityAddButton').hide();
                   }
               
               
               
            }
            else if(mode=='listview')
            {
                   $("#tblFacilityList").show();
                   $('#idFacilityAddButton').show();
                   $("#tblFacility").hide();
                        
            }
              else if(mode=='addview')
            {
                
                
                   $("#tblFacilityList").hide();
                   $("#tblFacility").show();
                   
                   //alert($("#idFacilityAddButton").text);
                
                    $("#hideFacilityStatus").val('add');
                    $("#hideFacilityformid").val('');
                    
                    
                    $("#selectFacility").val('');
                    $("#admitdate").val('');
                    $("#dischargedate").val('');
                    $('#chkFacilityActive').removeAttr('checked');
                    $("#facilitynotes").val('');
                    $("#facilitydoclinks").val('');
                    
                    $('#idFacilityAddButton').hide();

                        
            }
        
    }
    
    function facilitydatecomparison()
    {
        if(new Date(document.getElementById('dischargedate').value)< new Date(document.getElementById('admitdate').value))
            {
                alert("Discharge date should not be less than Admit Date");
                document.getElementById('dischargedate').value='';
            }
    }

function efmouseover2(elem,rowid) {
    textcomment=$("#hidden2"+rowid).val();

 ttMouseOver(elem, "viewAgencyComment.php?comment=" + textcomment);
}
    function agencyformview(mode,rowid,name,admitdate,dischargedate,isactive,doclinks)
    { 
        //agencynotes= <?php //echo base64_decode(agencynotes);?>
       
        if(mode=='showform')
            {
                $("#tblagencyList").hide();
                $("#tblagency").show();
                
               if(rowid!=0)
                   {
                        $("#hideagencyStatus").val('edit');
                        $("#hideagencyformid").val(rowid);
                        var name = $("#selectagency option:selected").text(name.replace(/\+/g, ' '));
                            var option;
                        $('#selectagency option').each(function() {
                           
                            if($(this).text() == name.text() && $(this).val() != '' && $(this).val() != 0 ) {
                                $("#selectagency").val( $(this).val());
                                option = this;
                                return false;
                            }
                        });
                        
                        $("#orgadmitdate").val(admitdate);
                        $("#orgdischargedate").val(dischargedate);
                        $("#agencynotes").val($("#hidden2"+rowid).val());
                        $("#agencydoclinks").val(doclinks);
                        
                        
                        if(isactive==1)
                            {
                                $('#chkagencyActive').attr('checked','checked');
                            }
                            else
                            {
                                $('#chkagencyActive').removeAttr('checked');
                            }
                         $('#idagencyAddButton').hide();
                   }
               
               
               
            }
            else if(mode=='listview')
            {
                   $("#tblagencyList").show();
                   $('#idagencyAddButton').show();
                   $("#tblagency").hide();
                        
            }
              else if(mode=='addview')
            {
                
                
                   $("#tblagencyList").hide();
                   $("#tblagency").show();
                   
                   //alert($("#idagencyAddButton").text);
                
                    $("#hideagencyStatus").val('add');
                    $("#hideagencyformid").val('');
                    
                    
                    $("#selectagency").val('');
                    $("#orgadmitdate").val('');
                    $("#orgdischargedate").val('');
                    $('#chkagencyActive').removeAttr('checked');
                    $("#agencynotes").val('');
                    $("#agencydoclinks").val('');
                    
                    $('#idagencyAddButton').hide();

                        
            }
        
    }
    function agencydatecomparison()
    {
        
        if(new Date(document.getElementById('orgdischargedate').value)< new Date(document.getElementById('orgadmitdate').value))
            {
                alert("Discharge date should not be less than Admit Date");
                document.getElementById('orgdischargedate').value='';
            }
    }
</script>
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
   
       
       
/*
     if(sqlNumRows($showGroupQry)>0)
        {  
                            
              echo "<table border='1'>";
              while($POS1to1Rec = sqlFetchArray($showGroupQry)) 
              {
                  echo "<tr><td class='label' style='text-align:left' width='25%'>Group : </td><td class=data>".$POS1to1Res['Grouping_Name']."</td></tr>";

		  $FieldSql = "SELECT fg.Grouping_ID,fg.Table_ID,fg.POS_id, tb.Field_Name, tb.Table_Name
                                FROM tbl_allcare_tablemeta tb
                                INNER JOIN tbl_allcare_patients1to1_fieldmapping fg ON tb.Field_ID = fg.Field_ID
                                INNER JOIN tbl_pos_types pt ON pt.id = fg.POS_id and fg.Grouping_ID=".$POS1to1Rec['Grouping_ID']." and fg.POS_id=".$POS1to1Rec['POS_id']."
                                group by fg.Grouping_ID,fg.Field_ID order by tb.Field_Name";
      
                  $groupFieldSqlQry =  sqlStatement($FieldSql);      
                 
                    if(sqlNumRows($groupFieldSqlQry)>0)
                    {

                        while($FieldRes = sqlFetchArray($groupFieldSqlQry))     
                        {                   
                           //$_SESSION['group_id_array'][$totalGroupCountForThisPatient]=array();
                            $_SESSION['group_id_array'][$totalGroupCountForThisPatient]=$FieldRes['Grouping_ID'];     

                            $field_number=0;

                            $field1to1Sql ="select ".$FieldRes['Field_Name']." from tbl_allcare_patients1to1 where pos_id=".$POS1to1Rec['POS_id']." and pid=".$GLOBALS['pid']; 
                            $field1to1Qry = sqlStatement($field1to1Sql);

                            while($field1to1Res = sqlFetchArray($field1to1Qry)) 
                            {
                                echo "<tr><td class='label' style='text-align:left' >".$FieldRes['Field_Name']." : </td>"
                               . "<td class='label' style='text-align:left' >".$field1to1Res[$FieldRes['Field_Name']]."
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



*/



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
                       <table id='tblFields_".$POS1to1Rec['Recordset_Name']."' border='1' cellspacing=0 cellpadding=0  width=100%>";

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
                        $textline = ($rowRecSql[$RecordsetFieldNamesArray[$field_number]]=='' ? '&nbsp;':$rowRecSql[$RecordsetFieldNamesArray[$field_number]]);
                        
                       echo "<td  style='min-width:50px;'>".$textline."</td>";
                       $field_number++;
                    }
                    echo "</tr>";
                }                    
             
             
             echo "</table></td></tr>"; 	
             
             
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


function facilitylistview($pid,$mode)
{
     
    
$facilitylistSql="select pf.id,patientid,facilityid,admitdate,dischargedate,isactive,f.name,pf.notes ,related_links
from tbl_patientfacility pf
inner join facility f on f.id=pf.facilityid
where pf.patientid=".$pid;
$facilitylistQry= mysql_query($facilitylistSql);
$facilitylistNumrows = mysql_num_rows($facilitylistQry);

    if($facilitylistNumrows>0)
    {
        
        echo "<table border='1' cellspacing='0' cellpadding='0' width='100%' align='center' id='tblFacilityList' style='display:block;'>";
        echo "<tbody>
            <tr>
            <td class='bold'>Facility Name</td>
            <td class='bold'>Admit Date</td>
            <td class='bold'>Discharge Date</td>
            <td class='bold'>Is Active</td>
            <td class='bold'>Notes</td>
            <td class='bold'>Related Document Links</td>
            </tr>";
        
        $editlinktext='';
        while($facilitylistrows = mysql_fetch_array($facilitylistQry))
        {
              $pfnotes = stripslashes($facilitylistrows['notes']);
               echo "<input type='hidden' id='hidden".$facilitylistrows['id']."' name='hidden".$facilitylistrows['id']."' value='".$pfnotes."'> ";
               
          
              echo "<tr>";
              echo "<td class='data' $editlinktext width='25%'>".$facilitylistrows['name']."</td>";
              echo "<td class='data' width='25%'>".$facilitylistrows['admitdate']."</td>";
              echo "<td class='data' width='25%'>".$facilitylistrows['dischargedate']."</td>";
              echo "<td class='data' width='25%'>".($facilitylistrows['isactive']=='1' ? Active : 'In- Active')."</td>";
              echo "<td class='data' width='20%' onmouseout='ttMouseOut()' onmouseover='efmouseover(this,".$facilitylistrows['id'].")'>".(strlen($facilitylistrows['notes'])<=30? $facilitylistrows['notes'] : substr($facilitylistrows['notes'],0,30)."...")."</td>";
              //echo "<td class='data' width='20%'>".$facilitylistrows['related_links']."</td>";
              $exarray2 = explode(',', $facilitylistrows['related_links']);
               echo "<td class='data' width='30%'>";
               foreach ($exarray2 as $value) {
                   
                  if(filter_var($value, FILTER_VALIDATE_URL)){ 
                        echo "<a href='$value' target='_blank' > ".$value."</a><br>";
                   }else if(preg_match("/\b(?:(?:https?|?:http?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i", $value)){
                         echo "<a href='http://$value' target='_blank' > ".$value."</a><br>";
                       
                   }else{
                         echo "<a href='http://$value' target='_blank' style='color:red'> ".$value."</a><br>";
                       
                   }
                  
              }
            echo "</td></tr>";
        }
        echo "</tbody></table>";
    }
    
       echo "<div id='tooltipdiv'
                     style='position:absolute;width:400pt;border:1px solid black;padding:2px;background-color:#ffffaa;visibility:hidden;z-index:1000;font-size:9pt;'
                     ></div>";
                
}

function facilitylistAddEditview($pid,$mode)
{
    
 $facilitylistSql="select pf.id,patientid,facilityid,admitdate,dischargedate,isactive,f.name,pf.notes ,related_links
from tbl_patientfacility pf
inner join facility f on f.id=pf.facilityid
where pf.patientid=".$pid;
$facilitylistQry= mysql_query($facilitylistSql);
$facilitylistNumrows = mysql_num_rows($facilitylistQry);

    if($facilitylistNumrows>0)
    {
        
        echo "<table border='1' cellspacing='0' cellpadding='0' width='100%' align='center' id='tblFacilityList' style='display:block;'>";
        echo "<tbody>
            <tr>
            <td class='bold'>Facility Name</td>
            <td class='bold'>Admit Date</td>
            <td class='bold'>Discharge Date</td>
            <td class='bold'>Is Active</td>
            <td class='bold'>Notes</td>
            <td class='bold'>Related Document Links</td>
            </tr>";
        $editlinktext='';
        while($facilitylistrows = mysql_fetch_array($facilitylistQry))
        {
            
            
                 $pfnotes = stripslashes($facilitylistrows['notes']);
               
                 $editlinktext ="onclick=facilityformview('showform',".$facilitylistrows['id'].",'".$facilitylistrows['facilityid']."','".$facilitylistrows['admitdate']."','".$facilitylistrows['dischargedate']."','".$facilitylistrows['isactive']."','".$facilitylistrows['related_links']."');";
                
                  echo "<input type='hidden' id='hidden".$facilitylistrows['id']."' name='hidden".$facilitylistrows['id']."' value='".$pfnotes."' > ";
            
              echo "<tr>";
              echo "<td class='data' style='color:blue;text-decoration:underline;cursor:pointer;' width='25%'><a href='#' $editlinktext>".$facilitylistrows['name']."</a></td>";
              echo "<td class='data' width='20%'>".$facilitylistrows['admitdate']."</td>";
              echo "<td class='data' width='20%'>".$facilitylistrows['dischargedate']."</td>";
              echo "<td class='data' width='20%'>".($facilitylistrows['isactive']=='1' ? Active : 'In- Active')."</td>";
              echo "<td class='data' width='20%' onmouseout='ttMouseOut()' onmouseover='efmouseover(this,".$facilitylistrows['id'].")'>".(strlen($facilitylistrows['notes'])<=30? $facilitylistrows['notes'] : substr($facilitylistrows['notes'],0,30)."...")."</td>";
              // echo "<td class='data' width='20%'>".$facilitylistrows['related_links']."</td>";
              $exarray2 = explode(',', $facilitylistrows['related_links']);
               echo "<td class='data' width='30%'>";
               foreach ($exarray2 as $value) {
                   
                  if(filter_var($value, FILTER_VALIDATE_URL)){ 
                        echo "<a href='$value' target='_blank' > ".$value."</a><br>";
                   }else if(preg_match("/\b(?:(?:https?|?:http?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i", $value)){
                         echo "<a href='http://$value' target='_blank' > ".$value."</a><br>";
                       
                   }else{
                         echo "<a href='http://$value' target='_blank' style='color:red'> ".$value."</a><br>";
                       
                   }
                  
              }
                echo "</td></tr>";
            
        }
        echo "</tbody></table>";
    }
}
function agencylistview($pid,$mode)
{
     
    
$agencylistSql="SELECT pf.id, patientid, admitdate, dischargedate, isactive, related_links, f.organization, pf.notes,related_links
FROM tbl_patientagency pf
INNER JOIN users f ON f.id = pf.agencyid
WHERE pf.patientid =".$pid;
$agencylistQry= mysql_query($agencylistSql);
$agencylistNumrows = mysql_num_rows($agencylistQry);

    if($agencylistNumrows>0)
    {
        
        echo "<table border='1' cellspacing='0' cellpadding='0' width='100%' align='center' id='tblagencyList' style='display:block;'>";
        echo "<tbody>
            <tr>
            <td class='bold'>Agency Name</td>
            <td class='bold'>Admit Date</td>
            <td class='bold'>Discharge Date</td>
            <td class='bold'>Is Active</td>
            <td class='bold'>Notes</td>
            <td class='bold'>Related Document Links</td>
            </tr>";
        
        $editlinktext='';
        while($agencylistrows = mysql_fetch_array($agencylistQry))
        {
              $pfnotes = stripslashes($agencylistrows['notes']);
               echo "<input type='hidden' id='hidden2".$agencylistrows['organization']."' name='hidden2".$agencylistrows['id']."' value='".$pfnotes."'> ";
               
          
              echo "<tr>";
              echo "<td class='data' $editlinktext width='25%'>".$agencylistrows['organization']."</td>";
              echo "<td class='data' width='25%'>".$agencylistrows['admitdate']."</td>";
              echo "<td class='data' width='25%'>".$agencylistrows['dischargedate']."</td>";
              echo "<td class='data' width='25%'>".($agencylistrows['isactive']=='1' ? Active : 'In- Active')."</td>";
              echo "<td class='data' width='20%' onmouseout='ttMouseOut()' onmouseover='efmouseover2(this,".$agencylistrows['id'].")'>".(strlen($agencylistrows['notes'])<=30? $agencylistrows['notes'] : substr($agencylistrows['notes'],0,30)."...")."</td>";
              //echo "<td class='data' width='30%'>".$agencylistrows['related_links']."</td>";
              $exarray = explode(',', $agencylistrows['related_links']);
               echo "<td class='data' width='30%'>";
               foreach ($exarray as $value) {
                   
                  if(filter_var($value, FILTER_VALIDATE_URL)){ 
                        echo "<a href='$value' target='_blank' > ".$value."</a><br>";
                   }else if(preg_match("/\b(?:(?:https?|?:http?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i", $value)){
                         echo "<a href='http://$value' target='_blank' > ".$value."</a><br>";
                       
                   }else{
                         echo "<a href='http://$value' target='_blank' style='color:red'> ".$value."</a><br>";
                       
                   }
                  
              }
                echo "</td></tr>";
            
        }
        echo "</tbody></table>";
    }
    
       echo "<div id='tooltipdiv2'
                     style='position:absolute;width:400pt;border:1px solid black;padding:2px;background-color:#ffffaa;visibility:hidden;z-index:1000;font-size:9pt;'
                     ></div>";
                
}

function agencylistAddEditview($pid,$mode)
{
    
    $agencylistSql="SELECT pf.id, patientid, admitdate, dischargedate, isactive, u.organization, pf.notes, abook_type, related_links, l.title
                    FROM tbl_patientagency pf
                    INNER JOIN users u ON u.id = pf.agencyid
                    INNER JOIN list_options l ON u.abook_type = l.option_id
                    WHERE pf.patientid =".$pid;
    $agencylistQry= mysql_query($agencylistSql);
    $agencylistNumrows = mysql_num_rows($agencylistQry);

    if($agencylistNumrows>0)
    {
        
        echo "<table border='1' cellspacing='0' cellpadding='0' width='100%' align='center' id='tblagencyList' style='display:block;'>";
        echo "<tbody>
            <tr>
            <td class='bold'>Agency Name</td>
            <td class='bold'>Admit Date</td>
            <td class='bold'>Discharge Date</td>
            <td class='bold'>Is Active</td>
            <td class='bold'>Notes</td>
            <td class='bold'>Related Document Links</td>
            </tr>";
        $editlinktext='';
        while($agencylistrows = mysql_fetch_array($agencylistQry))
        {
            
                $org = str_replace(" ","+",$agencylistrows['title'].'-'.$agencylistrows['organization']);
                $pfnotes = stripslashes($agencylistrows['notes']);
               
                $editlinktext ="onclick=agencyformview('showform','".$agencylistrows['id']."','$org','".$agencylistrows['admitdate']."','".$agencylistrows['dischargedate']."','".$agencylistrows['isactive']."','".$agencylistrows['related_links']."');";
         
                echo "<input type='hidden' id='hidden2".$agencylistrows['id']."' name='hidden2".$agencylistrows['id']."' value='".$pfnotes."'> ";
            
              echo "<tr>";
              echo "<td class='data' style='color:blue;text-decoration:underline;cursor:pointer;' width='25%'><a href='#' $editlinktext>".$agencylistrows['organization']."</a></td>";
              echo "<td class='data' width='20%'>".$agencylistrows['admitdate']."</td>";
              echo "<td class='data' width='20%'>".$agencylistrows['dischargedate']."</td>";
              echo "<td class='data' width='20%'>".($agencylistrows['isactive']=='1' ? Active : 'In- Active')."</td>";
              echo "<td class='data' width='20%' onmouseout='ttMouseOut()' onmouseover='efmouseover2(this,".$agencylistrows['id'].")'>".(strlen($agencylistrows['notes'])<=30? $agencylistrows['notes'] : substr($agencylistrows['notes'],0,30)."...")."</td>";
             // echo "<td class='data' width='30%'>".$agencylistrows['related_links']."</td>";
              $exarray = explode(',', $agencylistrows['related_links']);
              echo "<td class='data' width='30%'>";
               foreach ($exarray as $value) {
                   if(filter_var($value, FILTER_VALIDATE_URL)){ 
                        echo "<a href='$value' target='_blank' > ".$value."</a><br>";
                   }else if(preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i", $value)){
                         echo "<a href='http://$value' target='_blank' > ".$value."</a><br>";
                       
                   }else{
                         echo "<a href='http://$value' target='_blank' style='color:red'> ".$value."</a><br>";
                       
                   }
               }
                echo "</td></tr>";

            }
        echo "</tbody></table>";
    }
}
?>