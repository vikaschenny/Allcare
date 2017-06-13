
<style type="text/css">@import url(../../library/dynarch_calendar.css);</style>
<script type="text/javascript" src="../../library/dialog.js"></script>
<script type="text/javascript" src="../../library/textformat.js"></script>
<script type="text/javascript" src="../../library/dynarch_calendar.js"></script>
<script type="text/javascript" src="../../library/dynarch_calendar_en.js"></script>
<script type="text/javascript" src="../../library/dynarch_calendar_setup.js"></script>

<?php

      echo "<table id='tbl_AllCare_Patients' name='tbl_AllCare_Patients'>
                <tr title='1 to 1' border='1'>
                    <td valign='top' colspan='2'><b>AllCare_Patients1to1 </b></td>
                    <td valign='top'><input id='patients_chk1to1' type='checkbox' value='1' name='patients_chk1to1'></td>
                </tr>
                <tr title='1 to n' border='1'>
                    <td valign='top' colspan='2'><b>AllCare_Patients1ton </b></td>
                    <td valign='top'><input id='patients_chk1ton' type='checkbox' value='1' name='patients_chk1ton'></td>
                </tr>
            </table>
            <input type='button' id='patients_btnNext' name='patients_btnNext' value='Go to Patient&#39;s Attribute selection' style='float: right; margin-top: -35px;'
                   onclick='javascript: jQuery(\"#tbl_AllCare_Patients\").hide();
                                        jQuery(\"#tbl_grid\").hide();
                                        jQuery(\"#patients_tbl_step2\").show();
                                        jQuery(\"#patients_btnNext\").hide();
                                        jQuery(\"#patients_btnBack\").show();
                                        jQuery(\"#patients_btnSaveFields\").hide();                     
                                        jQuery(\"#patients_btnClear\").hide();  
                                        jQuery(\"#patients_btn_Cancel\").hide();  ' />
            <input type='button' id='patients_btnBack' name='patients_btnBack' value='Back' style='display:none' 
                   onclick='javascript: jQuery(\"#tbl_AllCare_Patients\").show();
                                        jQuery(\"#tbl_grid\").show();
                                        jQuery(\"#patients_tbl_step2\").hide();
                                        jQuery(\"#patients_btnBack\").hide();
                                        jQuery(\"#patients_btnNext\").show();
                                        jQuery(\"#patients_btnSaveFields\").show();                     
                                        jQuery(\"#patients_btnClear\").show();  
                                        jQuery(\"#patients_btn_Cancel\").show();' />
            <br>
            <table border='1' id='tbl_grid' name='tbl_grid'>                
                <tr>
                    <th><input type='checkbox' id='patients_check_All' name='patients_check_All' onclick='patients_checkAll(this.checked);' /></th>
                    <th>Related Table</th>
                    <th>Field Name</th>
                    <th>Data Type</th>
                    <th>Data Length</th>
                    <th>Reqd (Y/N)</th>
                    <th>Default Value</th>
                    <th>View/Edit</th>
                    <th></th>
                </tr>";
        
      //$res = sqlStatement('SHOW TABLES');  
      $res = sqlStatement('SELECT table_name FROM information_schema.tables WHERE table_schema = DATABASE()');  
      $table_list='';
      while($row = sqlFetchArray($res)) 
      {
        //$table_list .= "<option value='" . $row['Tables_in_openemr'] . "'>".$row['Tables_in_openemr']."</option>";
          if(strpos($row['table_name'],'tbl_allcare')===false)
          {
            $table_list .= "<option value='" . $row['table_name'] . "'>".$row['table_name']."</option>";
          }
      }  
      
//      $dataTypes = sqlStatement('SELECT title FROM tbl_data_types');  
//      $dataTypes_list='';
//      while ($row = sqlFetchArray($res)) 
//      {
//        $dataTypes_list .= "<option value='" . $row['title'] . "'>".$row['title']."</option>";
//      }  
      
      for($cnt=0;$cnt<20;$cnt++)
      {
               echo "<tr>
                    <td align='center'><input type='checkbox' id='patients_checkClear".$cnt."' name='patients_checkClear' /></td>";
                                           
      echo "<td><select id='patients_related_tables".$cnt."' name='patients_related_tables' onchange='javascript:return patients_newEvt(this.id);' style='width:125px'>          
            <option value='none'>none</option>
                $table_list
            </select></td>
            <td><input type='text' id='patients_txtFieldName".$cnt."' name='patients_txtFieldName' style='width:125px' /></td>";

            //echo "<td><input type='text' id='txtFieldType".$cnt."' name='txtFieldType[]' style='width:125px' /></td>";
            
            echo "<td>
                    <select id='patients_data_types".$cnt."' name='patients_data_types' onchange='patients_checkDataType();'>
                        <option value='none'>none</option>   
                        <option value='tinyint'>tinyint</option>                        
                        <option value='bigint'>smallint</option>
                        <option value='int'>int</option>                        
                        <option value='bigint'>bigint</option>
                        <option value='decimal'>decimal</option>
                        <option value='float'>float</option>
                        <option value='double'>double</option>
                        <option value='real'>real</option>
                        <option value='bit'>bit</option>
                        <option value='boolean'>boolean</option>
                        <option value='serial'>serial</option>
                        <option value='date'>date</option>
                        <option value='datetime'>datetime</option>
                        <option value='timestamp'>timestamp</option>
                        <option value='time'>time</option>
                        <option value='year'>year</option>
                        <option value='char'>char</option>
                        <option value='varchar'>varchar</option>
                        <option value='tinytext'>tinytext</option>
                        <option value='text'>text</option>
                        <option value='mediumtext'>mediumtext</option>
                        <option value='longtext'>longtext</option>
                        <option value='binary'>binary</option>
                        <option value='varbinary'>varbinary</option>
                        <option value='tinyblob'>tinyblob</option>
                        <option value='mediumblob'>mediumblob</option>
                        <option value='blob'>blob</option>
                        <option value='longblob'>longblob</option>
                        <option value='enum'>enum</option>
                        <option value='set'>set</option>
                        <option value='geometry'>geometry</option>
                        <option value='point'>point</option>
                        <option value='linestring'>linestring</option>
                        <option value='polygon'>polygon</option>
                        <option value='multipoint'>multipoint</option>
                        <option value='multilinestring'>multilinestring</option>
                        <option value='multipolygon'>multipolygon</option>
                        <option value='geometrycollection'>geometrycollection</option>
                    </select>
                  </td>";
                                              
            echo "<td><input type='text' id='patients_txtDataLength".$cnt."' name='patients_txtDataLength' style='width:125px' /></td>
            <td align='center'>
                <input type='checkbox' id='patients_boolFieldRequired".$cnt."' name='patients_boolFieldRequired' />
            </td>
            <td>
                <!--<input type='text' id='patients_txtDefaultValue".$cnt."' name='patients_txtDefaultValue' style='width:125px' />-->
                <input type='text' size='10' name='patients_txtDefaultValue' id='patients_txtDefaultValue".$cnt."' onkeyup='datekeyup(this,mypcc)' onblur='dateblur(this,mypcc)' title='yyyy-mm-dd last date of this event' />
<img src='../../interface/pic/show_calendar.gif' align='absbottom' width='24' height='22'
id='patients_img_calendar".$cnt."' border='0' alt='[?]' style='cursor:pointer;display:none;'
title='Click here to choose a date'>
<script>
Calendar.setup({inputField:'patients_txtDefaultValue".$cnt."', ifFormat:'%Y-%m-%d', button:'patients_img_calendar".$cnt."'});
</script>
            
            </td>
                           
            
            <td>
                <select id='patients_view_edit".$cnt."' name='patients_view_edit' style='width:100px'>
                    <option value='Y'>Edit</option>
                    <option value='N'>View</option>                    
                </select>
            </td>
        </tr>";
      } 
      echo "</table>";           
        
    $postyperes = sqlStatement("SELECT s.id,s.code,s.title FROM `tbl_pos_types` s group by title order by code asc ");
    $postyperesoptions = array();
    while($postyperesrows = sqlFetchArray($postyperes))
    {
      $postypeoptionvalue .="<option value=".$postyperesrows['code'].">".$postyperesrows['code']." :" .$postyperesrows['title']."</option>";
    }
        
?>

    <table id="patients_tbl_step2" name="patients_tbl_step2" style="display:none;">
        <tr>
            <td>Patient Attribute Types</td>
            <td>
                <select id='patients_lstpostype' name='patients_lstpostype' 
                        onchange="javascript:jQuery('#patients_rd1to1').attr('checked',false);
                                            jQuery('#patients_rd1ton').attr('checked',false);
                                            jQuery('#patients_lblExistingGroups').hide();   
                                            jQuery('#patients_lblExistingRecordsets').hide();  
                                            jQuery('#patients_showGroupsRecordsets').html('');
                                            patients_hideNewButtonData();">
                <?php echo $postypeoptionvalue;?>
                </select>
            </td>
        </tr>
        <tr><td></td></tr>
        <tr> <td></td>               
            <td align="left"><input type="radio" id="patients_rd1to1" name="patients_radallcare" value="1to1"
                       onchange="javascript:jQuery('#patients_lblExistingRecordsets').hide();
                                            jQuery('#patients_lblExistingGroups').show();  
                                            patients_showGroupsRecordsets();
                                            patients_hideNewButtonData();
                                            ">
            AllCare_Patients1to1</td>
        </tr>
        <tr>   <td></td>             
            <td align="left">
                <input type="radio" id="patients_rd1ton" name="patients_radallcare" value="1ton"
                       onchange="javascript:jQuery('#patients_lblExistingRecordsets').show();
                                            jQuery('#patients_lblExistingGroups').hide();  
                                            patients_showGroupsRecordsets();
                                            patients_hideNewButtonData();                                                 
                                            " />
            AllCare_Patients1ton</td>
        </tr>  
        <tr><td></td></tr>
        <tr>
            <td style="width:150px"><label id='patients_lblExistingGroups' style='display:none;'>Existing Groups </label>
                <label id='patients_lblExistingRecordsets' style='display:none;'>Existing Recordsets</label>
            </td>                                
            <td>                    
                <div id="patients_showGroupsRecordsets" name="patients_showGroupsRecordsets"></div>
            </td>
        </tr>
        <tr>
            <td>
                <label id='patients_lblFieldsSelected' style='display:none;'>Field Selected : </label>
            </td>
            <td>
                <div id="patients_showFieldsByGroupRecordsets" name="patients_showFieldsByGroupRecordsets"></div>
            </td>
        </tr>
        <tr>
            <td><label id='patients_lblGroup' style='display:none;'>Group Name</label>
                <label id='patients_lblRecordset' style='display:none;'>Recordset</label>
            </td>
            <td>
                <input type="text" id='patients_txtGroupRecordset' name="patients_txtGroupRecordset" value="" style='display:none;'>
            </td>
        </tr>
        <tr>     
            <td><label id='patients_lblExistingFields' style='display:none;'>Fields : </label></td>           
            <td>
                <div id="patients_showFields" name="patients_showFields" style='border:1px'>
            <?php                                              
    //    $postyperes = sqlStatement("SHOW COLUMNS FROM tbl_allcare_patients1ton 
    //                                WHERE Field!='id' AND Field!='pid' AND Field!='pos_id'");
    //    $allcarerows = array();
    //    while($allcarerows = sqlFetchArray($postyperes))
    //    {
    //             echo  $allcarerows['Field'] ."<input type='checkbox' name='chkFields[]' value='$allcarerows[Field]'>";
    //    }    
            ?>             
                </div> 
            </td>
        </tr>

        <tr>
            <td colspan="2" align="center">      
    <!--            <input type='submit' id='btnSave' name='btnSave' value='Save' />-->
                <input type='button' id='patients_btnSave' name='patients_btnSave' value='Save' 
                       onclick='javascript: 
                                            if(jQuery("#patients_comboGroupsRecordsets").val()!=="none")    
                                            {
                                                if(patients_validateStep2(0))
                                                {
                                                    patients_editGroupRecordset();
                                                }
                                            }
                                            if(jQuery("#patients_txtGroupRecordset").val()!=="")
                                            {
                                                if(patients_validateStep2(1))
                                                {
                                                    patients_insert_in_mapping();
                                                }
                                            }
                                            if(jQuery("#patients_comboGroupsRecordsets").val()==="none" && jQuery("#patients_txtGroupRecordset").val()==="")
                                            {
                                                alert("Enter the Group/Recordset name");
                                                
                                            }
                                               ' />
                <input type='button' id='patients_btnCancel' name='patients_btnCancel' value='Cancel'
                       onclick='javascript:location.href="edit_globals.php";'/>
            </td>
        </tr>
    </table>

    <br>
        <center>                   
            <input type='button' id='patients_btnSaveFields' name='patients_btnSaveFields' value='Save Fields' 
                   onclick='javascript:if(patients_validateStep1())
                                       {
                                            if(!patients_checkIfFieldAlreadyExists() && patients_validateDataTypes() && patients_validateDefaultValues())
                                            {
                                                patients_insertFields();
                                            }                                            
                                       }' />                                
            <input type='button' id='patients_btnClear' name='patients_btnClear' value='Clear' onclick='javascript:patients_clearSelected();' />
            <input type='button' id='patients_btn_Cancel' name='patients_btn_Cancel' value='Cancel' 
                   onclick='javascript:location.href="edit_globals.php";' />
        </center>
    
<script type='text/javascript'>
    
function patients_newEvt(selectedBox)
{
     if(document.getElementById(selectedBox).value!=='none')
     {
  dlgopen('all_extensions/get_table_fields.php?table_name='+document.getElementById(selectedBox).value+'&selectedBox='+selectedBox+'&extension_name=patients',
   '_blank', 550, 270);
     }
  return false;
}

var totalFields=0;
 
function patients_receivedFromChild(selectedBox,tableName,checkedFields,checkedFieldsType)
{
    //alert('sbox='+selectedBox);
    var SBNum=0;
    var s=selectedBox.split("patients_related_tables"); 
    SBNum=s[1];
    //alert('SBNum = '+SBNum);    
    //alert('length = '+checkedFields.length);  
    //alert('totalFields = '+totalFields);  
    var split0='',split1='',split2='';        
                
    jQuery('#patients_related_tables'+SBNum).val(tableName);
    jQuery('#patients_txtFieldName'+SBNum).val(checkedFields[0]);
    //alert('patients_txtFieldName'+SBNum+'='+jQuery('#patients_txtFieldName'+SBNum).val());
    if(checkedFieldsType[0].indexOf('(') !== -1)  // datatype contains '('  e.g. varchar(100)
    {
        split0 = checkedFieldsType[0].split("(");
        split1 = split0[0];
        split2 = split0[1].split(")");
    }
    else
    {
        split1 = checkedFieldsType[0];   
        split2 = '';
    }
    
    jQuery('#patients_data_types'+SBNum).val(split1);    
    jQuery('#patients_txtDataLength'+SBNum).val(split2[0]);
        
    if(jQuery('#patients_related_tables'+SBNum).val()!=='none')
    {
        jQuery('#patients_txtFieldName'+SBNum).attr('readonly','readonly');                            
        jQuery('#patients_data_types'+SBNum).attr('disabled','disabled');    
        jQuery('#patients_txtDataLength'+SBNum).attr('readonly','readonly');
    }
    
    if(jQuery('#patients_data_types'+SBNum).val()==='text' || jQuery('#patients_data_types'+SBNum).val()==='tinytext' || jQuery('#patients_data_types'+SBNum).val()==='mediumtext' ||
               jQuery('#patients_data_types'+SBNum).val()==='longtext' || jQuery('#patients_data_types'+SBNum).val()==='year' ||
               jQuery('#patients_data_types'+SBNum).val()==='blob' || jQuery('#patients_data_types'+SBNum).val()==='tinyblob' ||
               jQuery('#patients_data_types'+SBNum).val()==='mediumblob' || jQuery('#patients_data_types'+SBNum).val()==='longblob')
    {
            jQuery('#patients_boolFieldRequired'+SBNum).attr('disabled','disabled');
            jQuery('#patients_txtDefaultValue'+SBNum).attr('disabled','disabled');
    }
    
    (jQuery('#patients_data_types'+SBNum).val()==='date' || 
     jQuery('#patients_data_types'+SBNum).val()==='datetime') ? jQuery('#patients_img_calendar'+SBNum).show() : jQuery('#patients_img_calendar'+SBNum).hide();
            
                        
    if(SBNum>=totalFields)   
    {
        totalFields++;
    }
    
    var allTotalFields=0;
    var cntFilledFieldText=0;
    while(jQuery('#patients_txtFieldName'+cntFilledFieldText).val()!=='')
    {
        //alert('val='+jQuery('#patients_txtFieldName'+cntFilledFieldText).val());
        allTotalFields++;
        cntFilledFieldText++;
    }
     
    totalFields=allTotalFields;

    for(var i=totalFields;i<(totalFields+checkedFields.length-1);i++)
    {
        jQuery('#patients_related_tables'+i).val(tableName);
        jQuery('#patients_txtFieldName'+i).val(checkedFields[i-totalFields+1]);
        
        if(checkedFieldsType[i-totalFields+1].indexOf('(') !== -1)  // datatype contains '('  e.g. varchar(100)
        {
            split0 = checkedFieldsType[i-totalFields+1].split("(");
            split1 = split0[0];
            split2 = split0[1].split(")");
        }
        else
        {
            split1 = checkedFieldsType[i-totalFields+1];   
            split2 = '';
        }
        
        jQuery('#patients_data_types'+i).val(split1);    
        jQuery('#patients_txtDataLength'+i).val(split2[0]);
                
        if(jQuery('#patients_related_tables'+i).val()!=='none')
        {
            jQuery('#patients_txtFieldName'+i).attr('readonly','readonly');                            
            jQuery('#patients_data_types'+i).attr('disabled','disabled');    
            jQuery('#patients_txtDataLength'+i).attr('readonly','readonly');
        }
        
        if(jQuery('#patients_data_types'+i).val()==='text' || jQuery('#patients_data_types'+i).val()==='tinytext' || jQuery('#patients_data_types'+i).val()==='mediumtext' ||
               jQuery('#patients_data_types'+i).val()==='longtext' || jQuery('#patients_data_types'+i).val()==='year' ||
               jQuery('#patients_data_types'+i).val()==='blob' || jQuery('#patients_data_types'+i).val()==='tinyblob' ||
               jQuery('#patients_data_types'+i).val()==='mediumblob' || jQuery('#patients_data_types'+i).val()==='longblob')
        {
                jQuery('#patients_boolFieldRequired'+i).attr('disabled','disabled');
                jQuery('#patients_txtDefaultValue'+i).attr('disabled','disabled');
        }
         
        if(jQuery('#patients_data_types'+i).val()==='date' || jQuery('#patients_data_types'+i).val()==='datetime')
        {
                jQuery('#patients_img_calendar'+i).show();
        }    
                  
    }

    totalFields=(SBNum>=totalFields) ? (totalFields+checkedFields.length-1) : (totalFields+checkedFields.length);  
    
    //alert('totalFields = '+totalFields);
    totalFields--;
         
}
  
function patients_unselectTable(selectedBox)
{ 
    jQuery('#patients_'+selectedBox).val('none');    
}
         	
function patients_addRow(numRow,tableName,checkFieldName,checkFieldType,checkFieldSize)
{
        for(var i=0;i<numRow;i++)
        {
            jQuery('#patients_related_tables'+numRow).val(tableName);
            jQuery('#patients_txtFieldName'+numRow).val(checkFieldName);
            //jQuery('#patients_txtFieldType'+numRow).val(checkFieldType);
            jQuery('#patients_txtDataLength'+numRow).val(checkFieldSize);
            jQuery('#patients_data_types'+numRow).val(checkFieldType);
            //jQuery('#patients_txtDefaultValue'+numRow).val(tableName);                                
        }
}

function patients_skip_none(array_name)
{
    var new_array=new Array();
    var j=0;
    for(var i=0;i<20;i++)
    {
        if(array_name[i]!=='none')
        {
           new_array[j]= array_name[i];
           j++;
        }
    }
    return new_array;   
}

function patients_skip_blank(array_name)
{
    var new_array=new Array();
    var j=0;
    for(var i=0;i<20;i++)
    {
        if(array_name[i]!=='')
        {
           new_array[j]= array_name[i];
           j++;
        }
    }
    return new_array;    
}

function patients_validateStep1()
{
    if(!(jQuery('#patients_chk1to1').is(':checked')) && !(jQuery('#patients_chk1ton').is(':checked')))
    {
        alert("Please check the table AllCare_Patients1to1 or AllCare_Patients1ton");
        return false;
    }
        
    var relTables=document.getElementsByName("patients_related_tables");    
    //var fieldsEntered=document.getElementsByName("related_tables");    
    
    var flag=0;
    for(j=0;j<relTables.length;j++)
    {
        if(relTables[j].value==='none' && jQuery('#patients_txtFieldName').val()==='')
        {
            flag=0;
        }
        else
        {
            flag=1;
            break;
        }
    }
    
    if(flag===0)
    {
        alert("Please select the fields from at least one related table.");
        return false;
    }
    
    return true;
    
}

function patients_validateStep2(mapping_flag)
{
    if(!(jQuery('#patients_rd1to1').is(':checked')) && !(jQuery('#patients_rd1ton').is(':checked')))
    {
        alert("Please check the table AllCare_Patients1to1 or AllCare_Patients1ton");
        return false;
    }
    
        
    var checked_fields;
    if(mapping_flag===0)
    {
        checked_fields=document.getElementsByName("patients_chkSelectedFields");        
    }
    else if(mapping_flag===1)
    {
        checked_fields=document.getElementsByName("patients_chkAllFields");
    }
        
    //var checked_fields=document.getElementsByName("patients_chkSelectedFields");        
    var flag=0;   
    
    for(i=0;i<checked_fields.length;i++)
    {
        
        //delList[i].checked = checkboxstatus;//(chkBtn.checked)? true : false;   
        if(checked_fields[i].checked)
        {
            flag=1;
            break;
        }
        else
        {
            flag=0;
        }
    }
    
    if(flag===0)
    {
        alert("Please select the field/s");
        return false;
    }
    
    //if(mapping_flag===1 && )
            
    return true;
    
}

function patients_checkIfFieldAlreadyExists()
{ 
    var FieldName='';

    var chk1to1=(jQuery('#patients_chk1to1').is(':checked'))?1:0;
    var chk1ton=(jQuery('#patients_chk1ton').is(':checked'))?1:0;
    
    for(var i=0;i<20;i++)
    {         
        if(jQuery('#patients_txtFieldName'+i).val()!=='')
        {
            FieldName=jQuery('#patients_txtFieldName'+i).val();    
            var exists;
            $.ajax({
                type: 'POST',
                url: 'all_extensions/check_if_field_already_exists.php',	
                dataType: "html",
		async: false, 
                data: {extension_name:'patients',FieldName:FieldName,chk1to1:chk1to1,chk1ton:chk1ton},

                success: function(response)
                {    
                    if((chk1to1===1 && chk1ton===1))
                    {
                        if(response!=='11')
                        {
                            alert(response);     
                            //return false;
                            exists=true;                                
                        }
                        else
                        {
                            exists=false;
                        }
                    }
                    else if((chk1to1===1 && chk1ton===0) || (chk1to1===0 && chk1ton===1))
                    {
                        if(response!=='1')
                        {
                            alert(response);     
                            //return false;
                            exists=true;    
                        }
                        else
                        {
                            exists=false;
                        }
                    }                    
                },
                failure: function(response)
                {
                        alert("error");
                }		
               });	
               
               if(exists===true)
               {
                   break;
               }
          }       
    }
    //return true;
    return exists;
}

function patients_validateDataTypes()
{
    for(var i=0;i<20;i++)
    {     
        if(jQuery('#patients_txtFieldName'+i).val()!=='' && jQuery('#patients_data_types'+i).val()!=='')
        {
            // datalength is NOT mandatory for the following data types , 
            // eg. if the data type is 'date' then don't validate its empty datalength.
            //     else if data type is 'int' then validate its empty datalength
            if(jQuery('#patients_data_types'+i).val()!=='date' && jQuery('#patients_data_types'+i).val()!=='datetime' &&
               jQuery('#patients_data_types'+i).val()!=='time' && jQuery('#patients_data_types'+i).val()!=='timestamp' &&
               jQuery('#patients_data_types'+i).val()!=='text' && jQuery('#patients_data_types'+i).val()!=='tinytext' && 
               jQuery('#patients_data_types'+i).val()!=='mediumtext' && jQuery('#patients_data_types'+i).val()!=='longtext' && 
               jQuery('#patients_data_types'+i).val()!=='blob' && jQuery('#patients_data_types'+i).val()!=='tinyblob' &&
               jQuery('#patients_data_types'+i).val()!=='mediumblob' && jQuery('#patients_data_types'+i).val()!=='longblob' &&                        
               jQuery('#patients_data_types'+i).val()!=='geometry' && jQuery('#patients_data_types'+i).val()!=='point' &&
               jQuery('#patients_data_types'+i).val()!=='linestring' && jQuery('#patients_data_types'+i).val()!=='polygon' &&  
               jQuery('#patients_data_types'+i).val()!=='multipoint' && jQuery('#patients_data_types'+i).val()!=='multilinestring' &&    
               jQuery('#patients_data_types'+i).val()!=='multipolygon' && jQuery('#patients_data_types'+i).val()!=='geometrycollection')
            {
                if(jQuery('#patients_txtDataLength'+i).val()===''  && 
                   jQuery('#patients_txtDataLength'+i).attr('readonly')===true)   //  isNaN(jQuery('#patients_txtDataLength'+i).val())
                {
                    alert('Please enter a valid numerical Data Length for the field '+jQuery('#patients_txtFieldName'+i).val());
                    jQuery('#patients_txtDataLength'+i).focus();
                    return false;                            
                }
            }
        }
        
        if(jQuery('#patients_txtFieldName'+i).val()!=='' && jQuery('#patients_data_types'+i).val()==='none')
        {
            alert('Enter data type for the field '+jQuery('#patients_txtFieldName'+i).val());
            return false;
        }
        
    }
    return true;
}

function patients_validateDefaultValues()
{
    for(var i=0;i<20;i++)
    {
        if(jQuery('#patients_txtFieldName'+i).val()!=='')
        {
            var FieldName=jQuery('#patients_txtFieldName'+i).val();               
            var dataType=jQuery('#patients_data_types'+i).val();
            var dataLength=jQuery('#patients_txtDataLength'+i).val();
            var defaultValue=jQuery('#patients_txtDefaultValue'+i).val();
            
            switch(dataType)
            {
                case 'tinyint':
                case 'smallint':
                case 'mediumint':
                case 'int':
                case 'bigint':
                case 'decimal':    
                case 'float':
                case 'double':
                case 'real':
                case 'year':if(isNaN(defaultValue))
                            {
                                alert('Default value for the field '+FieldName+' is invalid');
                                jQuery('#patients_data_types'+i).val('none');
                                return false;
                            }
                            break;
                            
                case 'char':if(defaultValue!=='' && (!isNaN(defaultValue) || defaultValue.length>1))
                            {
                                alert('Default value for the field '+FieldName+' is invalid, only 1 Character is allowed');
                                jQuery('#patients_txtDefaultValue'+i).val('');
                                //jQuery('#patients_data_types'+i).val('none');
                                return false;
                            }  
                            if(dataLength!=='1')
                            {
                                alert(dataLength+ "=Data Length for the 'char' field "+FieldName+" should be 1");
                                jQuery('#patients_txtDataLength'+i).val('');
                                //jQuery('#patients_data_types'+i).val('none');
                                return false;
                            }
                            break;
                            
            }
            
            if(jQuery('#patients_boolFieldRequired'+i).is(':checked') && defaultValue===''
                && (jQuery('#patients_data_types'+i).val()!=='text' && jQuery('#patients_data_types'+i).val()!=='tinytext' && 
                    jQuery('#patients_data_types'+i).val()!=='mediumtext' && jQuery('#patients_data_types'+i).val()!=='longtext' &&
                    jQuery('#patients_data_types'+i).val()!=='blob' && jQuery('#patients_data_types'+i).val()!=='tinyblob' &&
                    jQuery('#patients_data_types'+i).val()!=='mediumblob' && jQuery('#patients_data_types'+i).val()!=='longblob'))                                        
            {
                alert('Please enter the Default value for the field '+FieldName+' checked as Required');
                return false;
            }                                    
        }
    }
    return true;
}

function patients_checkDataType()
{
    for(var i=0;i<20;i++)
    {
        jQuery('#patients_img_calendar'+i).hide(); 
        jQuery('#patients_boolFieldRequired'+i).prop('disabled','');
        jQuery('#patients_txtDefaultValue'+i).prop('disabled','');
        jQuery('#patients_txtDataLength'+i).prop('readonly','');
        
        if(jQuery('#patients_data_types'+i).val()==='text' || jQuery('#patients_data_types'+i).val()==='tinytext' || 
           jQuery('#patients_data_types'+i).val()==='mediumtext' || jQuery('#patients_data_types'+i).val()==='longtext' || 
           jQuery('#patients_data_types'+i).val()==='blob' || jQuery('#patients_data_types'+i).val()==='tinyblob' ||
           jQuery('#patients_data_types'+i).val()==='mediumblob' || jQuery('#patients_data_types'+i).val()==='longblob')
        {
                jQuery('#patients_boolFieldRequired'+i).attr('disabled','disabled');
                jQuery('#patients_txtDefaultValue'+i).attr('disabled','disabled');
        }
        
        if(jQuery('#patients_data_types'+i).val()==='date' || jQuery('#patients_data_types'+i).val()==='datetime')
        {
            jQuery('#patients_img_calendar'+i).show(); 
            jQuery('#patients_txtDataLength'+i).attr('readonly','readonly');
        }
    }
}

function patients_insertFields()
{
        var finalURL='all_extensions/add_new_fields.php';  
        
        var chk1to1=(jQuery('#patients_chk1to1').is(':checked'))?1:0;
        var chk1ton=(jQuery('#patients_chk1ton').is(':checked'))?1:0;
        //var related_tables=new Array();   
        var relatedTables=new Array();  
        //var relatedTables=[];
        var FieldName=new Array();   
        var FieldType=new Array();   
        var DataLength=new Array();   
        var FieldRequired=new Array();   
        var DefaultValue=new Array();                  
        var FieldViewEdit=new Array();
                
        for(var i=0;i<20;i++)
        {
            relatedTables[i]=jQuery('#patients_related_tables'+i).val();
            //relatedTables.push(jQuery('#patients_related_tables'+i).val());
            FieldName[i]=jQuery('#patients_txtFieldName'+i).val();                                    
            FieldType[i]=jQuery('#patients_data_types'+i).val();
            DataLength[i]=jQuery('#patients_txtDataLength'+i).val();
            FieldRequired[i]=(jQuery('#patients_boolFieldRequired'+i).is(':checked'))?1:0;
            DefaultValue[i]=jQuery('#patients_txtDefaultValue'+i).val();            
            FieldViewEdit[i]=jQuery('#patients_view_edit'+i).val();                                    	            
        }                       
                
        relatedTables=patients_skip_none(relatedTables);                
        relatedTables=relatedTables.toString();
        //alert("rt= "+relatedTables);
        
        FieldName=patients_skip_blank(FieldName);
        FieldName=FieldName.toString();
        //alert('saving4');  
        FieldType=patients_skip_none(FieldType);
        FieldType=FieldType.toString();
        //alert("DL = "+DataLength);
        for(var i=0;i<20;i++)
        {
            if(DataLength[i]==='')
            {
               DataLength[i]=0;         
            }
            else if ( DataLength[i].indexOf(',') !== -1 ) // for enum data types
            {                 
               DataLength[i]=DataLength[i].replace(/,/gi,"*"); 
            }
        }
        DataLength=DataLength.toString();
        //alert("DL = "+DataLength);
        FieldRequired=FieldRequired.toString();
        
        DefaultValue=DefaultValue.toString();
        
        FieldViewEdit=FieldViewEdit.toString();
                       
        $.ajax({
                type: 'POST',
		url: finalURL,	
		data: {extension_name:'patients',chk1to1:chk1to1,chk1ton:chk1ton,relatedTables:relatedTables,FieldName:FieldName,
                       FieldType:FieldType,DataLength:DataLength,FieldRequired:FieldRequired,DefaultValue:DefaultValue,
                       FieldViewEdit:FieldViewEdit},
		
		success: function(response)
		{                      
                     if(response==='0')            
			{
				alert("You cannot insert duplicate field names");
			}
                        
                        else if(isNaN(response))
                            {
                                alert("The column "+response+" already exists in above table/s");
                            }
			else if(response==='1')
			{       
                     jQuery("#tbl_grid").hide();     
                     jQuery("#tbl_AllCare_Patients").hide();  
                     
                     jQuery("#patients_btnSaveFields").hide();                     
                     jQuery("#patients_btnClear").hide();  
                     jQuery("#patients_btn_Cancel").hide();  
                     jQuery("#patients_tbl_step2").show(); 
                     jQuery("#patients_btnNext").hide();
                     jQuery("#patients_btnBack").show();
                        }
		},
		failure: function(response)
		{
			alert("error");
		}		
        });	       
}

function patients_showGroupsRecordsets()
{
    var finalURL='all_extensions/show_groups_recordsets.php';  
    var table_name=jQuery("#patients_rd1to1").is(":checked")?'tbl_allcare_patients1to1_fieldmapping':'tbl_allcare_patients1ton_fieldmapping';
    var pos_id=jQuery("#patients_lstpostype").val();
           
    $.ajax({
                type: 'POST',
                url: finalURL,	
                data: {extension_name:'patients',table_name:table_name,pos_id:pos_id},

                //data:relatedTables,
                success: function(response)
                {                      
                     jQuery("#patients_showGroupsRecordsets").html(response);                     
                },
                failure: function(response)
                {
                        alert("error");
                }		
            });	
}

function patients_showFields()
{                      
        var finalURL='all_extensions/show_fields.php';  
        var table_name=jQuery("#patients_rd1to1").is(":checked")?'tbl_allcare_patients1to1':'tbl_allcare_patients1ton';
                
        $.ajax({
                    type: 'POST',
                    url: finalURL,	
                    data: {extension_name:'patients',table_name:table_name},

                    //data:relatedTables,
                    success: function(response)
                    {                      
                         jQuery("#patients_showFields").html(response);                     
                    },
                    failure: function(response)
                    {
                            alert("error");
                    }		
               });		
}

function patients_hideNewButtonData()
{
    jQuery('#patients_lblGroup').hide();  
    jQuery('#patients_lblRecordset').hide();  
    jQuery('#patients_lblExistingFields').hide();  
    jQuery('#patients_txtGroupRecordset').val('');  
    jQuery('#patients_txtGroupRecordset').hide();      
    jQuery('#patients_lblFieldsSelected').hide();   
    jQuery('#patients_showFieldsByGroupRecordsets').html(''); 
    jQuery('#patients_showFields').html(''); 
}

function patients_showFieldsByGroupRecordsets(tableName)
{
        var finalURL='all_extensions/show_fields_by_groups_recordsets.php';  
        //var table_name=jQuery("#patients_rd1to1").is(":checked")?'tbl_allcare_patients1to1_fieldmapping':'tbl_allcare_patients1ton_fieldmapping';
        var table_name=tableName;
        var selectedGR=jQuery("#patients_comboGroupsRecordsets").val();
        var pos_id=jQuery("#patients_lstpostype").val();
        
        $.ajax({
                    type: 'POST',
                    url: finalURL,	
                    data: {extension_name:'patients',pos_id:pos_id,table_name:table_name,selectedGR:selectedGR},

                    //data:relatedTables,
                    success: function(response)
                    {                      
                         jQuery("#patients_showFieldsByGroupRecordsets").html(response);                     
                    },
                    failure: function(response)
                    {
                            alert("error");
                    }		
               });		
}

function patients_editGroupRecordset()
{ 
              
        var finalURL='all_extensions/edit_group_recordset.php';
        var pos_id=jQuery("#patients_lstpostype").val();
        var TableName=(jQuery('#patients_rd1to1').is(':checked'))?'tbl_allcare_patients1to1_fieldmapping':'tbl_allcare_patients1ton_fieldmapping';    
        
        var GroupRecordsetName=jQuery('#patients_comboGroupsRecordsets').val();
        
        var selectedFields=new Array();   
        
        var checked_fields=document.getElementsByName("patients_chkSelectedFields");        

        for(i=0;i<checked_fields.length;i++)
        {
            //delList[i].checked = checkboxstatus;//(chkBtn.checked)? true : false;   
            if(checked_fields[i].checked)
            {
                selectedFields.push(checked_fields[i].value);
            }
        }      
        
        selectedFields=selectedFields.toString();
        
        $.ajax({
            type: 'POST',
            url: finalURL,	
            data: {extension_name:'patients',pos_id:pos_id,TableName:TableName,GroupRecordsetName:GroupRecordsetName,
                   selectedFields:selectedFields},

            //data:relatedTables,
            success: function(response)
            {                      
                 alert(response);     

            },
            failure: function(response)
            {
                    alert("error");
            }		
           });		                      
 
}

function patients_insert_in_mapping()
{
    var finalURL='all_extensions/insert_in_mapping.php';  
                
    var posType=jQuery('#patients_lstpostype').val();
    
var mappingTableName=(jQuery('#patients_rd1to1').is(':checked'))?'tbl_allcare_patients1to1_fieldmapping':'tbl_allcare_patients1ton_fieldmapping';    
    
    var groupRecordsetName=jQuery('#patients_txtGroupRecordset').val();

    //var selectedFields=jQuery('#patients_comboFields').val();
    var selectedFields=new Array();
    
    var checked_fields=document.getElementsByName("patients_chkAllFields");            

    for(i=0;i<checked_fields.length;i++)
    {
        //delList[i].checked = checkboxstatus;//(chkBtn.checked)? true : false;   
        if(checked_fields[i].checked)
        {
            selectedFields.push(checked_fields[i].value);
        }
    }      
               
    selectedFields=selectedFields.toString();
    
    $.ajax({
            type: 'POST',
            url: finalURL,	
            data: {extension_name:'patients',posType:posType,mappingTableName:mappingTableName,groupRecordsetName:groupRecordsetName,
                   selectedFields:selectedFields},

            //data:relatedTables,
            success: function(response)
            {                      
                 alert(response);     
		 patients_showGroupsRecordsets();
                 patients_hideNewButtonData();

            },
            failure: function(response)
            {
                    alert("error");
            }		
           });		         
}

var checkedAll=false;
function patients_checkAll(checkboxstatus)
{
    var delList=document.getElementsByName("patients_checkClear");        

    for(var i=0;i<delList.length;i++)
    {
        delList[i].checked = checkboxstatus;//(chkBtn.checked)? true : false;            
    }
    
    checkedAll=checkboxstatus;
}

function patients_clearSelected()
{
    var delList=document.getElementsByName("patients_checkClear"); 
    var checkedNum=0;
    var allFieldsClear=0;
         
    for(var i=(delList.length-1);i>=0;i--)
    {
        if(delList[i].checked===true)
        {
            checkedNum++;        
            
            jQuery('#patients_related_tables'+i).val('none');
            jQuery('#patients_txtFieldName'+i).val('');
            jQuery('#patients_data_types'+i).val('none');
            jQuery('#patients_txtDataLength'+i).val('');
            /*
            jQuery('#patients_txtFieldName'+i).prop('readonly','');                            
            jQuery('#patients_data_types'+i).prop('disabled','');    
            jQuery('#patients_txtDataLength'+i).prop('readonly','');
            */
            jQuery('#patients_boolFieldRequired'+i).prop('checked', false);            
            jQuery('#patients_txtDefaultValue'+i).val('');
            
            if(jQuery('#patients_img_calendar'+i).css('display')==='inline' || jQuery('#patients_img_calendar'+i).css('display')==='block')
            {
                jQuery('#patients_img_calendar'+i).hide();
            }
            
            jQuery('#patients_view_edit'+i).val('Y');            
            delList[i].checked=false;
            
            for(var j=i;j<(totalFields);j++)
            {
                jQuery('#patients_related_tables'+j).val(jQuery('#patients_related_tables'+(j+1)).val());
                jQuery('#patients_txtFieldName'+j).val(jQuery('#patients_txtFieldName'+(j+1)).val());
                jQuery('#patients_data_types'+j).val(jQuery('#patients_data_types'+(j+1)).val());
                jQuery('#patients_txtDataLength'+j).val(jQuery('#patients_txtDataLength'+(j+1)).val());                
                
                var fn = jQuery('#patients_txtFieldName'+(j+1)).prop('readonly') ? 'readonly' :'';
                jQuery('#patients_txtFieldName'+j).prop('readonly',fn);                            
                
                var dt = jQuery('#patients_data_types'+(j+1)).prop('disabled') ? 'disabled' :'';
                jQuery('#patients_data_types'+j).prop('disabled',dt);                          
                                
                var dl = jQuery('#patients_txtDataLength'+(j+1)).prop('readonly') ? 'readonly' : '';
                jQuery('#patients_txtDataLength'+j).prop('readonly',dl);                                                       
                
                var fr = jQuery('#patients_boolFieldRequired'+(j+1)).is(':checked') ? true : false;
                jQuery('#patients_boolFieldRequired'+j).prop('checked', fr);
                                
                jQuery('#patients_txtDefaultValue'+j).val(jQuery('#patients_txtDefaultValue'+(j+1)).val());
                jQuery('#patients_view_edit'+j).val(jQuery('#patients_view_edit'+(j+1)).val());                                   
                                
                var frReq = jQuery('#patients_boolFieldRequired'+(j+1)).prop('disabled') ? 'disabled' : '';
                jQuery('#patients_boolFieldRequired'+j).prop('disabled',frReq);                                                       
                
                var dv = jQuery('#patients_txtDefaultValue'+(j+1)).prop('disabled') ? 'disabled' : '';
                jQuery('#patients_txtDefaultValue'+j).prop('disabled', dv);
                
                var imgCal = (jQuery('#patients_img_calendar'+(j+1)).css('display')==='inline' || jQuery('#patients_img_calendar'+(j+1)).css('display')==='block') ? 'inline' : 'none';
                
                jQuery('#patients_img_calendar'+j).css('display', imgCal);                
                                                
                jQuery('#patients_related_tables'+(j+1)).val('none');
                jQuery('#patients_txtFieldName'+(j+1)).val('');
                jQuery('#patients_data_types'+(j+1)).val('none');
                jQuery('#patients_txtDataLength'+(j+1)).val('');            

                jQuery('#patients_txtFieldName'+(j+1)).prop('readonly','');                            
                jQuery('#patients_data_types'+(j+1)).prop('disabled','');    
                jQuery('#patients_txtDataLength'+(j+1)).prop('readonly','');

                jQuery('#patients_boolFieldRequired'+(j+1)).prop('checked', false);            
                jQuery('#patients_txtDefaultValue'+(j+1)).val('');
                jQuery('#patients_img_calendar'+(j+1)).hide();             
                jQuery('#patients_boolFieldRequired'+(j+1)).prop('disabled','');    
                jQuery('#patients_txtDefaultValue'+(j+1)).prop('disabled','');    
                
                jQuery('#patients_view_edit'+(j+1)).val('Y');                                      
            }
            
            jQuery('#patients_related_tables'+j).val('none');
            jQuery('#patients_txtFieldName'+j).val('');
            jQuery('#patients_data_types'+j).val('none');
            jQuery('#patients_txtDataLength'+j).val('');            

            jQuery('#patients_txtFieldName'+j).prop('readonly','');                            
            jQuery('#patients_data_types'+j).prop('disabled','');    
            jQuery('#patients_txtDataLength'+j).prop('readonly','');

            jQuery('#patients_boolFieldRequired'+j).prop('checked', false);            
            jQuery('#patients_txtDefaultValue'+j).val('');
            jQuery('#patients_img_calendar'+j).hide();          
            jQuery('#patients_view_edit'+j).val('Y');                
                      
            //if(i===0)
            if(i===0 && totalFields===0)
            {allFieldsClear=1;}
                      
        }
        
        jQuery('#patients_check_All').prop('checked', false);   
    }
    
    //totalFields=0;
    
    totalFields = (allFieldsClear===1 || checkedAll===true) ? 0 : Math.abs(totalFields-checkedNum);
    checkedAll=false;
    alert('now tf= '+totalFields);
}

</script>

