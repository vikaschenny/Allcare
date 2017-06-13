<!--
<style type="text/css">@import url(../../library/dynarch_calendar.css);</style>
<script type="text/javascript" src="../../library/dialog.js"></script>
<script type="text/javascript" src="../../library/textformat.js"></script>
<script type="text/javascript" src="../../library/dynarch_calendar.js"></script>
<script type="text/javascript" src="../../library/dynarch_calendar_en.js"></script>
<script type="text/javascript" src="../../library/dynarch_calendar_setup.js"></script>
-->
<?php

      echo "<table id='insurance_tbl_AllCare' name='insurance_tbl_AllCare'>
                <tr title='1 to 1' border='1'>
                    <td valign='top' colspan='2'><b>AllCare_Insurance1to1 </b></td>
                    <td valign='top'><input id='insurance_chk1to1' type='checkbox' value='1' name='insurance_chk1to1'></td>
                </tr>
                <tr title='1 to n' border='1'>
                    <td valign='top' colspan='2'><b>AllCare_Insurance1ton </b></td>
                    <td valign='top'><input id='insurance_chk1ton' type='checkbox' value='1' name='insurance_chk1ton'></td>
                </tr>
            </table>
            <input type='button' id='insurance_btnNext' name='insurance_btnNext' value='Next' style='float: right; margin-top: -35px;'
                   onclick='javascript: jQuery(\"#insurance_tbl_AllCare\").hide();
                                        jQuery(\"#insurance_tbl_grid\").hide();
                                        jQuery(\"#insurance_tbl_step2\").show();
                                        jQuery(\"#insurance_btnNext\").hide();
                                        jQuery(\"#insurance_btnBack\").show();
                                        jQuery(\"#insurance_btnSaveFields\").hide();                     
                                        jQuery(\"#insurance_btnClear\").hide();  
                                        jQuery(\"#insurance_btn_Cancel\").hide();  ' />
            <input type='button' id='insurance_btnBack' name='insurance_btnBack' value='Back' style='display:none' 
                   onclick='javascript: jQuery(\"#insurance_tbl_AllCare\").show();
                                        jQuery(\"#insurance_tbl_grid\").show();
                                        jQuery(\"#insurance_tbl_step2\").hide();
                                        jQuery(\"#insurance_btnBack\").hide();
                                        jQuery(\"#insurance_btnNext\").show();
                                        jQuery(\"#insurance_btnSaveFields\").show();                     
                                        jQuery(\"#insurance_btnClear\").show();  
                                        jQuery(\"#insurance_btn_Cancel\").show();' />
            <br>
            <table border='1' id='insurance_tbl_grid' name='insurance_tbl_grid'>                
                <tr>
                    <th><input type='checkbox' id='insurance_check_All' name='insurance_check_All' onclick='insurance_checkAll(this.checked);' /></th>
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
      while ($row = sqlFetchArray($res)) 
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
                    <td align='center'><input type='checkbox' id='insurance_checkClear".$cnt."' name='insurance_checkClear' /></td>";
                                           
      echo "<td><select id='insurance_related_tables".$cnt."' name='insurance_related_tables' onchange='javascript:return insurance_newEvt(this.id);' style='width:125px'>          
            <option value='none'>none</option>
                $table_list
            </select></td>
            <td><input type='text' id='insurance_txtFieldName".$cnt."' name='insurance_txtFieldName' style='width:125px' /></td>";

            //echo "<td><input type='text' id='insurance_txtFieldType".$cnt."' name='insurance_txtFieldType[]' style='width:125px' /></td>";
            
            echo "<td>
                    <select id='insurance_data_types".$cnt."' name='insurance_data_types' onchange='insurance_checkDataType();'>
                        <option value='none'>none</option>   
                        <option value='tinyint'>tinyint</option>                        
                        <option value='smallint'>smallint</option>
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
                                              
            echo "<td><input type='text' id='insurance_txtDataLength".$cnt."' name='insurance_txtDataLength' style='width:125px' /></td>
            <td align='center'>
                <input type='checkbox' id='insurance_boolFieldRequired".$cnt."' name='insurance_boolFieldRequired' />
            </td>
            <td>
                <!--<input type='text' id='insurance_txtDefaultValue".$cnt."' name='insurance_txtDefaultValue' style='width:125px' />-->
                <input type='text' size='10' name='insurance_txtDefaultValue' id='insurance_txtDefaultValue".$cnt."' onkeyup='datekeyup(this,mypcc)' onblur='dateblur(this,mypcc)' title='yyyy-mm-dd last date of this event' />
<img src='../../interface/pic/show_calendar.gif' align='absbottom' width='24' height='22'
id='insurance_img_calendar".$cnt."' border='0' alt='[?]' style='cursor:pointer;display:none;'
title='Click here to choose a date'>
<script>
Calendar.setup({inputField:'insurance_txtDefaultValue".$cnt."', ifFormat:'%Y-%m-%d', button:'insurance_img_calendar".$cnt."'});
</script>
            
            </td>
                                       
            <td>
                <select id='insurance_view_edit".$cnt."' name='insurance_view_edit' style='width:100px'>
                    <option value='Y'>Edit</option>
                    <option value='N'>View</option>                    
                </select>
            </td>
        </tr>";
      } 
      echo "</table>";                 
        
?>

    <table id="insurance_tbl_step2" name="insurance_tbl_step2" style="display:none;">
        
        <tr><td></td></tr>
        <tr> <td></td>               
            <td align="left"><input type="radio" id="insurance_rd1to1" name="insurance_radallcare" value="1to1"
                       onchange="javascript:jQuery('#insurance_lblExistingRecordsets').hide();
                                            jQuery('#insurance_lblExistingGroups').show();  
                                            insurance_showGroupsRecordsets();
                                            insurance_hideNewButtonData();
                                            jQuery('#td_Show_YesNo').hide();
                                            ">
            AllCare_Insurance1to1</td>
        </tr>
        <tr>   <td></td>             
            <td align="left">
                <input type="radio" id="insurance_rd1ton" name="insurance_radallcare" value="1ton"
                       onchange="javascript:jQuery('#insurance_lblExistingRecordsets').show();
                                            jQuery('#insurance_lblExistingGroups').hide();  
                                            insurance_showGroupsRecordsets();
                                            insurance_hideNewButtonData();      
                                            jQuery('#td_Show_YesNo').hide();
                                            " />
            AllCare_Insurance1ton</td>
        </tr>  
        <tr><td></td></tr>
        <tr>
            <td style="width:150px"><label id='insurance_lblExistingGroups' style='display:none;'>Existing Groups </label>
                <label id='insurance_lblExistingRecordsets' style='display:none;'>Existing Recordsets</label>
            </td>                                
            <td>                    
                <div id="insurance_showGroupsRecordsets" name="insurance_showGroupsRecordsets"></div>
            </td>
        </tr>
        <tr>
            <td>
                <label id='insurance_lblFieldsSelected' style='display:none;'>Field Selected : </label>
            </td>
            <td>
                <div id="insurance_showFieldsByGroupRecordsets" name="insurance_showFieldsByGroupRecordsets"></div>
            </td>
        </tr>
        <tr>
            <td><label id='insurance_lblGroup' style='display:none;'>Group Name</label>
                <label id='insurance_lblRecordset' style='display:none;'>Recordset</label>
            </td>
            <td>
                <input type="text" id='insurance_txtGroupRecordset' name="insurance_txtGroupRecordset" value="" style='display:none;'>
            </td>
        </tr>
        <tr>     
            <td><label id='insurance_lblExistingFields' style='display:none;'>Fields : </label></td>           
            <td>
                <div id="insurance_showFields" name="insurance_showFields" style='border:1px'>               
                </div> 
            </td>
        </tr>
        <tr><td></td>
            <td id="td_Show_YesNo" style="display:none;"><label id='insurance_lblShowYesNo'>Show this Group/Recordset      
                <input type='radio' id='rd_show_yes' name='rd_show_yes_no' value='Y' checked /><label for='rd_show_yes'>Yes</label>
                <input type='radio' id='rd_show_no' name='rd_show_yes_no' value='N' /><label for='rd_show_no'>No</label>
            </td>
        </tr>

        <tr>
            <td colspan="2" align="center">      
    <!--            <input type='submit' id='insurance_btnSave' name='insurance_btnSave' value='Save' />-->
                <input type='button' id='insurance_btnSave' name='insurance_btnSave' value='Save' 
                       onclick='javascript: if(jQuery("#insurance_comboGroupsRecordsets").val()!=="none")    
                                            {
                                                if(insurance_validateStep2(0))
                                                {
                                                    insurance_editGroupRecordset();
                                                }
                                            }
                                            if(jQuery("#insurance_txtGroupRecordset").val()!=="")
                                            {
                                                if(insurance_validateStep2(1))
                                                {
                                                    insurance_insert_in_mapping();
                                                }
                                            }
                                            if(jQuery("#insurance_comboGroupsRecordsets").val()==="none" && jQuery("#insurance_txtGroupRecordset").val()==="")
                                            {
                                                alert("Enter the Group/Recordset name");
                                            }
                                        ' />
                <input type='button' id='insurance_btnCancel' name='insurance_btnCancel' value='Cancel'
                       onclick='javascript:location.href="edit_globals.php";'/>
            </td>
        </tr>
     </table>

    <br>
        <center>                   
            <input type='button' id='insurance_btnSaveFields' name='insurance_btnSaveFields' value='Save Fields' 
                   onclick='javascript:if(insurance_validateStep1())
                                       {
                                            if(!insurance_checkIfFieldAlreadyExists() && insurance_validateDataTypes() && insurance_validateDefaultValues())
                                            {   
                                                insurance_insertFields();
                                            }                                            
                                       }' />                                
            <input type='button' id='insurance_btnClear' name='insurance_btnClear' value='Clear' onclick='javascript:insurance_clearSelected();' />
            <input type='button' id='insurance_btn_Cancel' name='insurance_btn_Cancel' value='Cancel' 
                   onclick='javascript:location.href="edit_globals.php";' />
        </center>
    

<script type='text/javascript'>
    
function insurance_newEvt(selectedBox)
{
     if(document.getElementById(selectedBox).value!=='none')
     {
  dlgopen('all_extensions/get_table_fields.php?table_name='+document.getElementById(selectedBox).value+'&selectedBox='+selectedBox+'&extension_name=insurance',
   '_blank', 550, 270);
     }
  return false;
}

var insurance_totalFields=0;
 
function insurance_receivedFromChild(selectedBox,tableName,checkedFields,checkedFieldsType)
{
    var SBNum=0;
    var s=selectedBox.split("insurance_related_tables"); 
    SBNum=s[1];
    /*
    if(SBNum>(insurance_totalFields+1))
    {
        SBNum=(insurance_totalFields===0)?0:insurance_totalFields;
    }
    */
    //alert('SBNum = '+SBNum);    
    //alert('length = '+checkedFields.length);  
    //alert('insurance_totalFields = '+insurance_totalFields);  
    var split0='',split1='',split2='';        
                
    jQuery('#insurance_related_tables'+SBNum).val(tableName);
    jQuery('#insurance_txtFieldName'+SBNum).val(checkedFields[0]);
    
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
    
    jQuery('#insurance_data_types'+SBNum).val(split1);    
    jQuery('#insurance_txtDataLength'+SBNum).val(split2[0]);
        
    if(jQuery('#insurance_related_tables'+SBNum).val()!=='none')
    {
        jQuery('#insurance_txtFieldName'+SBNum).attr('readonly','readonly');                            
        jQuery('#insurance_data_types'+SBNum).attr('disabled','disabled');    
        jQuery('#insurance_txtDataLength'+SBNum).attr('readonly','readonly');
    }     
    
    if(jQuery('#insurance_data_types'+SBNum).val()==='text' || jQuery('#insurance_data_types'+SBNum).val()==='tinytext' || jQuery('#insurance_data_types'+SBNum).val()==='mediumtext' ||
               jQuery('#insurance_data_types'+SBNum).val()==='longtext' || jQuery('#insurance_data_types'+SBNum).val()==='year' ||
               jQuery('#insurance_data_types'+SBNum).val()==='blob' || jQuery('#insurance_data_types'+SBNum).val()==='tinyblob' ||
               jQuery('#insurance_data_types'+SBNum).val()==='mediumblob' || jQuery('#insurance_data_types'+SBNum).val()==='longblob')
    {
            jQuery('#insurance_boolFieldRequired'+SBNum).attr('disabled','disabled');
            jQuery('#insurance_txtDefaultValue'+SBNum).attr('disabled','disabled');
    }
    
    (jQuery('#insurance_data_types'+SBNum).val()==='date' || 
     jQuery('#insurance_data_types'+SBNum).val()==='datetime') ? jQuery('#insurance_img_calendar'+SBNum).show() : jQuery('#insurance_img_calendar'+SBNum).hide();
            
                        
    if(SBNum>=insurance_totalFields)   
    {
        insurance_totalFields++;
    }

    for(var i=insurance_totalFields;i<(insurance_totalFields+checkedFields.length-1);i++)
    {
        jQuery('#insurance_related_tables'+i).val(tableName);
        jQuery('#insurance_txtFieldName'+i).val(checkedFields[i-insurance_totalFields+1]);
        
        if(checkedFieldsType[i-insurance_totalFields+1].indexOf('(') !== -1)  // datatype contains '('  e.g. varchar(100)
        {
            split0 = checkedFieldsType[i-insurance_totalFields+1].split("(");
            split1 = split0[0];
            split2 = split0[1].split(")");
        }
        else
        {
            split1 = checkedFieldsType[i-insurance_totalFields+1];   
            split2 = '';
        }
        
        jQuery('#insurance_data_types'+i).val(split1);    
        jQuery('#insurance_txtDataLength'+i).val(split2[0]);
                
        if(jQuery('#insurance_related_tables'+i).val()!=='none')
        {
            jQuery('#insurance_txtFieldName'+i).attr('readonly','readonly');                            
            jQuery('#insurance_data_types'+i).attr('disabled','disabled');    
            jQuery('#insurance_txtDataLength'+i).attr('readonly','readonly');
        }     
        
        if(jQuery('#insurance_data_types'+i).val()==='text' || jQuery('#insurance_data_types'+i).val()==='tinytext' || jQuery('#insurance_data_types'+i).val()==='mediumtext' ||
               jQuery('#insurance_data_types'+i).val()==='longtext' || jQuery('#insurance_data_types'+i).val()==='year' ||
               jQuery('#insurance_data_types'+i).val()==='blob' || jQuery('#insurance_data_types'+i).val()==='tinyblob' ||
               jQuery('#insurance_data_types'+i).val()==='mediumblob' || jQuery('#insurance_data_types'+i).val()==='longblob')
        {
                jQuery('#insurance_boolFieldRequired'+i).attr('disabled','disabled');
                jQuery('#insurance_txtDefaultValue'+i).attr('disabled','disabled');
        }
         
        if(jQuery('#insurance_data_types'+i).val()==='date' || jQuery('#insurance_data_types'+i).val()==='datetime')
        {            
                jQuery('#insurance_img_calendar'+i).show();
        }    
                  
    }

    insurance_totalFields=(SBNum>=insurance_totalFields) ? (insurance_totalFields+checkedFields.length-1) : (insurance_totalFields+checkedFields.length);  
    insurance_totalFields--;
    
}
  
function insurance_unselectTable(selectedBox)
{    
    jQuery('#'+selectedBox).val('none');    
}
          
function insurance_addRow(numRow,tableName,checkFieldName,checkFieldType,checkFieldSize)
{	          
        for(var i=0;i<numRow;i++)
        {
            jQuery('#insurance_related_tables'+numRow).val(tableName);
            jQuery('#insurance_txtFieldName'+numRow).val(checkFieldName);
            //jQuery('#txtFieldType'+numRow).val(checkFieldType);
            jQuery('#insurance_txtDataLength'+numRow).val(checkFieldSize);
            jQuery('#insurance_data_types'+numRow).val(checkFieldType);
            //jQuery('#insurance_txtDefaultValue'+numRow).val(tableName);                                
        }
}

function insurance_skip_none(array_name)
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

function insurance_skip_blank(array_name)
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

function insurance_validateStep1()
{
    if(!(jQuery('#insurance_chk1to1').is(':checked')) && !(jQuery('#insurance_chk1ton').is(':checked')))
    {
        alert("Please check the table AllCare_Insurance1to1 or AllCare_Insurance1ton");
        return false;
    }
        
    var relTables=document.getElementsByName("insurance_related_tables");    
    //var fieldsEntered=document.getElementsByName("related_tables");    
    
    var flag=0;
    for(var j=0;j<relTables.length;j++)
    {
        if(relTables[j].value==='none' && jQuery('#insurance_txtFieldName'+j).val()==='')
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
    
        
    var fieldName=document.getElementsByName("insurance_txtFieldName"); 
    for(var k=0;k<fieldName.length;k++)
    {                        
        if(/\s/.test(jQuery('#insurance_txtFieldName'+k).val()))
        {
            alert("Remove space in the Field name '"+jQuery('#insurance_txtFieldName'+k).val()+"'");
            return false;           
        }        
    }
    
    return true;
    
}

function insurance_validateStep2(mapping_flag)
{
    if(!(jQuery('#insurance_rd1to1').is(':checked')) && !(jQuery('#insurance_rd1ton').is(':checked')))
    {
        alert("Please check the table AllCare_Insurance1to1 or AllCare_Insurance1ton");
        return false;
    }
    
      
    var checked_fields;
    if(mapping_flag===0)
    {
        checked_fields=document.getElementsByName("insurance_chkSelectedFields");        
    }
    else if(mapping_flag===1)
    {
        checked_fields=document.getElementsByName("insurance_chkAllFields");        
    }
    //var checked_fields=document.getElementsByName("insurance_chkSelectedFields");        
    var flag=0;
    for(var i=0;i<checked_fields.length;i++)
    {
        //insurance_delList[i].checked = checkboxstatus;//(chkBtn.checked)? true : false;   
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
            
    return true;
    
}

function insurance_checkIfFieldAlreadyExists()
{
    var FieldName='';

    var chk1to1=(jQuery('#insurance_chk1to1').is(':checked'))?1:0;
    var chk1ton=(jQuery('#insurance_chk1ton').is(':checked'))?1:0;
    
    for(var i=0;i<20;i++)
    {      
        if(jQuery('#insurance_txtFieldName'+i).val()!=='')
        {
            FieldName=jQuery('#insurance_txtFieldName'+i).val();    
            var exists;
            $.ajax({
                type: 'POST',
                url: 'insurance_check_if_field_already_exists.php',	
                dataType: "html",
		async: false, 
                data: {extension_name:'insurance',FieldName:FieldName,chk1to1:chk1to1,chk1ton:chk1ton},

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

function insurance_validateDataTypes()
{
    for(var i=0;i<20;i++)
    {           
        if(jQuery('#insurance_txtFieldName'+i).val()!=='' && jQuery('#insurance_data_types'+i).val()!=='')
        {
            // datalength is NOT mandatory for the following data types , 
            // eg. if the data type is 'date' then don't validate its empty datalength.
            //     else if data type is 'int' then validate its empty datalength
            if(jQuery('#insurance_data_types'+i).val()!=='date' && jQuery('#insurance_data_types'+i).val()!=='datetime' &&
               jQuery('#insurance_data_types'+i).val()!=='time' && jQuery('#insurance_data_types'+i).val()!=='timestamp' &&
               jQuery('#insurance_data_types'+i).val()!=='text' && jQuery('#insurance_data_types'+i).val()!=='tinytext' && 
               jQuery('#insurance_data_types'+i).val()!=='mediumtext' && jQuery('#insurance_data_types'+i).val()!=='longtext' && 
               jQuery('#insurance_data_types'+i).val()!=='blob' && jQuery('#insurance_data_types'+i).val()!=='tinyblob' &&
               jQuery('#insurance_data_types'+i).val()!=='mediumblob' && jQuery('#insurance_data_types'+i).val()!=='longblob' &&                        
               jQuery('#insurance_data_types'+i).val()!=='geometry' && jQuery('#insurance_data_types'+i).val()!=='point' &&
               jQuery('#insurance_data_types'+i).val()!=='linestring' && jQuery('#insurance_data_types'+i).val()!=='polygon' &&  
               jQuery('#insurance_data_types'+i).val()!=='multipoint' && jQuery('#insurance_data_types'+i).val()!=='multilinestring' &&    
               jQuery('#insurance_data_types'+i).val()!=='multipolygon' && jQuery('#insurance_data_types'+i).val()!=='geometrycollection')
            {
                if(jQuery('#insurance_txtDataLength'+i).val()==='' && 
                   jQuery('#insurance_txtDataLength'+i).attr('readonly')===true) //  isNaN(jQuery('#insurance_txtDataLength'+i).val())
                {
                    alert('Please enter a valid numerical Data Length for the field '+jQuery('#insurance_txtFieldName'+i).val());
                    jQuery('#insurance_txtDataLength'+i).focus();
                    return false;                            
                }
            }
        }                        
    }
    return true;
}

function insurance_validateDefaultValues()
{        
    for(var i=0;i<20;i++)
    {          
        if(jQuery('#insurance_txtFieldName'+i).val()!=='')
        {
            var FieldName=jQuery('#insurance_txtFieldName'+i).val();               
            var dataType=jQuery('#insurance_data_types'+i).val();
            var defaultValue=jQuery('#insurance_txtDefaultValue'+i).val();
            
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
                                return false;
                            }
                            break;
                            
                case 'char':if(defaultValue!=='' && (!isNaN(defaultValue) || defaultValue.length>1))
                            {
                                alert('Default value for the field '+FieldName+' is invalid, only 1 Character is allowed');
                                jQuery('#insurance_txtDefaultValue'+i).val('');
                                //jQuery('#data_types'+i).val('none');
                                return false;
                            }  
                            if(dataLength!=='1')
                            {
                                alert("Data Length for the 'char' field "+FieldName+" should be 1");
                                jQuery('#insurance_txtDataLength'+i).val('');
                                //jQuery('#insurance_data_types'+i).val('none');
                                return false;
                            }
                            break;
                                            
            }
            
            if(jQuery('#insurance_boolFieldRequired'+i).is(':checked') && defaultValue===''
                && (jQuery('#insurance_data_types'+i).val()!=='text' && jQuery('#insurance_data_types'+i).val()!=='tinytext' && 
                    jQuery('#insurance_data_types'+i).val()!=='mediumtext' && jQuery('#insurance_data_types'+i).val()!=='longtext' &&
                    jQuery('#insurance_data_types'+i).val()!=='blob' && jQuery('#insurance_data_types'+i).val()!=='tinyblob' &&
                    jQuery('#insurance_data_types'+i).val()!=='mediumblob' && jQuery('#insurance_data_types'+i).val()!=='longblob'))                                        
            {
                alert('Please enter the Default value for the field '+FieldName+' checked as Required');
                return false;
            }                                    
        }
    }
    return true;
}

function insurance_checkDataType()
{
    for(var i=0;i<20;i++)
    { 
        jQuery('#insurance_img_calendar'+i).hide(); 
        jQuery('#insurance_boolFieldRequired'+i).prop('disabled','');
        jQuery('#insurance_txtDefaultValue'+i).prop('disabled','');
        jQuery('#insurance_txtDataLength'+i).prop('readonly','');
        
        if(jQuery('#insurance_data_types'+i).val()==='text' || jQuery('#insurance_data_types'+i).val()==='tinytext' || 
           jQuery('#insurance_data_types'+i).val()==='mediumtext' || jQuery('#insurance_data_types'+i).val()==='longtext' || 
           jQuery('#insurance_data_types'+i).val()==='blob' || jQuery('#insurance_data_types'+i).val()==='tinyblob' ||
           jQuery('#insurance_data_types'+i).val()==='mediumblob' || jQuery('#insurance_data_types'+i).val()==='longblob')
        {
                jQuery('#insurance_boolFieldRequired'+i).attr('disabled','disabled');
                jQuery('#insurance_txtDefaultValue'+i).attr('disabled','disabled');
        }
        if(jQuery('#insurance_data_types'+i).val()==='date' || jQuery('#insurance_data_types'+i).val()==='datetime')
        {
            jQuery('#insurance_img_calendar'+i).show(); 
            jQuery('#insurance_txtDataLength'+i).attr('readonly','readonly');
        }
    }
}

function insurance_insertFields()
{
        var finalURL='all_extensions/add_new_fields.php';  
        
        var chk1to1=(jQuery('#insurance_chk1to1').is(':checked'))?1:0;
        var chk1ton=(jQuery('#insurance_chk1ton').is(':checked'))?1:0;
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
            relatedTables[i]=jQuery('#insurance_related_tables'+i).val();
            //relatedTables.push(jQuery('#insurance_related_tables'+i).val());
            FieldName[i]=jQuery('#insurance_txtFieldName'+i).val();                                    
            FieldType[i]=jQuery('#insurance_data_types'+i).val();
            DataLength[i]=jQuery('#insurance_txtDataLength'+i).val();
            FieldRequired[i]=(jQuery('#insurance_boolFieldRequired'+i).is(':checked'))?1:0;
            DefaultValue[i]=jQuery('#insurance_txtDefaultValue'+i).val();            
            FieldViewEdit[i]=jQuery('#insurance_view_edit'+i).val();                                    	            
        }                       
                
        relatedTables=insurance_skip_none(relatedTables);                
        relatedTables=relatedTables.toString();
        
        FieldName=insurance_skip_blank(FieldName);
        FieldName=FieldName.toString();
        
        FieldType=insurance_skip_none(FieldType);
        FieldType=FieldType.toString();
        
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
        
        FieldRequired=FieldRequired.toString();
        
        DefaultValue=DefaultValue.toString();
        
        FieldViewEdit=FieldViewEdit.toString();
                           
        $.ajax({
                type: 'POST',
		url: finalURL,	
		data: {extension_name:'insurance',chk1to1:chk1to1,chk1ton:chk1ton,relatedTables:relatedTables,FieldName:FieldName,
                       FieldType:FieldType,DataLength:DataLength,FieldRequired:FieldRequired,DefaultValue:DefaultValue,
                       FieldViewEdit:FieldViewEdit},
		
		success: function(response)
		{                         
                     alert(response);     
                     jQuery("#insurance_tbl_grid").hide();     
                     jQuery("#insurance_tbl_AllCare").hide();  
                     
                     jQuery("#insurance_btnSaveFields").hide();                     
                     jQuery("#insurance_btnClear").hide();  
                     jQuery("#insurance_btn_Cancel").hide();  
                     jQuery("#insurance_tbl_step2").show(); 
                     jQuery("#insurance_btnNext").hide();
                     jQuery("#insurance_btnBack").show();
                     
		},
		failure: function(response)
		{
			alert("error");
		}		
        });	       
}

function insurance_showGroupsRecordsets()
{
    var finalURL='all_extensions/show_groups_recordsets.php';  
    var table_name=jQuery("#insurance_rd1to1").is(":checked")?'tbl_allcare_insurance1to1_fieldmapping':'tbl_allcare_insurance1ton_fieldmapping';    
           
    $.ajax({
                type: 'POST',
                url: finalURL,	
                data: {extension_name:'insurance',table_name:table_name},

                //data:relatedTables,
                success: function(response)
                {                         
                     jQuery("#insurance_showGroupsRecordsets").html(response);                     
                },
                failure: function(response)
                {
                        alert("error");
                }		
            });	
}

function insurance_showFields()
{                         
        var finalURL='all_extensions/show_fields.php';  
        var table_name=jQuery("#insurance_rd1to1").is(":checked")?'tbl_allcare_insurance1to1':'tbl_allcare_insurance1ton';
                
        $.ajax({
                    type: 'POST',
                    url: finalURL,	
                    data: {extension_name:'insurance',table_name:table_name},

                    //data:relatedTables,
                    success: function(response)
                    {                         
                         jQuery("#insurance_showFields").html(response);                     
                    },
                    failure: function(response)
                    {
                            alert("error");
                    }		
               });		
}

function insurance_hideNewButtonData()
{
    jQuery('#insurance_lblGroup').hide();  
    jQuery('#insurance_lblRecordset').hide();  
    jQuery('#insurance_lblExistingFields').hide();  
    jQuery('#insurance_txtGroupRecordset').val('');  
    jQuery('#insurance_txtGroupRecordset').hide();      
    jQuery('#insurance_lblFieldsSelected').hide();   
    jQuery('#insurance_showFieldsByGroupRecordsets').html(''); 
    jQuery('#insurance_showFields').html(''); 
}

function insurance_showFieldsByGroupRecordsets(tableName)
{
        var finalURL='all_extensions/show_fields_by_groups_recordsets.php';  
        //var table_name=jQuery("#insurance_rd1to1").is(":checked")?'tbl_allcare_insurance1to1_fieldmapping':'tbl_allcare_insurance1ton_fieldmapping';
        var table_name=tableName;
        var selectedGR=jQuery("#insurance_comboGroupsRecordsets").val();        
        
        $.ajax({
                    type: 'POST',
                    url: finalURL,	
                    data: {extension_name:'insurance',table_name:table_name,selectedGR:selectedGR},

                    //data:relatedTables,
                    success: function(response)
                    {                         
                         jQuery("#insurance_showFieldsByGroupRecordsets").html(response);                     
                    },
                    failure: function(response)
                    {
                            alert("error");
                    }		
               });		
}

function insurance_editGroupRecordset()  
{               
        var finalURL='all_extensions/edit_group_recordset.php';
        
        var TableName=(jQuery('#insurance_rd1to1').is(':checked'))?'tbl_allcare_insurance1to1_fieldmapping':'tbl_allcare_insurance1ton_fieldmapping';    
        
        var GroupRecordsetName=jQuery('#insurance_comboGroupsRecordsets').val();
        
        var selectedFields=new Array();   
        
        var showYesNo = jQuery('#rd_show_yes').is(':checked') ? 'Y' : 'N';
        
        var checked_fields=document.getElementsByName("insurance_chkSelectedFields");        

        for(i=0;i<checked_fields.length;i++)
        {
            //insurance_delList[i].checked = checkboxstatus;//(chkBtn.checked)? true : false;   
            if(checked_fields[i].checked)
            {
                selectedFields.push(checked_fields[i].value);
            }
        }      
        
        selectedFields=selectedFields.toString();
        
        $.ajax({
            type: 'POST',
            url: finalURL,	
            data: {extension_name:'insurance',TableName:TableName,GroupRecordsetName:GroupRecordsetName,
                   selectedFields:selectedFields,showYesNo:showYesNo},

            //data:relatedTables,
            success: function(response)
            {                         
                 alert(response);   
                 insurance_showGroupsRecordsets();
                 insurance_hideNewButtonData();

            },
            failure: function(response)
            {
                    alert("error");
            }		
           });		                       
}

function insurance_insert_in_mapping()
{
    var finalURL='all_extensions/insert_in_mapping.php';                  
    
var mappingTableName=(jQuery('#insurance_rd1to1').is(':checked'))?'tbl_allcare_insurance1to1_fieldmapping':'tbl_allcare_insurance1ton_fieldmapping';    
    
    
    var showYesNo = jQuery('#rd_show_yes').is(':checked') ? 'Y' : 'N';
    
    var groupRecordsetName=jQuery('#insurance_txtGroupRecordset').val();

    //var selectedFields=jQuery('#comboFields').val();
    var selectedFields=new Array();
    
    var checked_fields=document.getElementsByName("insurance_chkAllFields");            

    for(i=0;i<checked_fields.length;i++)
    {
        //insurance_delList[i].checked = checkboxstatus;//(chkBtn.checked)? true : false;   
        if(checked_fields[i].checked)
        {
            selectedFields.push(checked_fields[i].value);
        }
    }      
               
    selectedFields=selectedFields.toString();
    
    $.ajax({
            type: 'POST',
            url: finalURL,	
            data: {extension_name:'insurance',mappingTableName:mappingTableName,groupRecordsetName:groupRecordsetName,
                   selectedFields:selectedFields,showYesNo:showYesNo},

            //data:relatedTables,
            success: function(response)
            {                         
                 alert(response);   
                 insurance_showGroupsRecordsets();
                 insurance_hideNewButtonData();

            },
            failure: function(response)
            {
                    alert("error");
            }		
           });		         
}

var insurance_checkedAll=false;
function insurance_checkAll(checkboxstatus)
{            
    var insurance_delList=document.getElementsByName("insurance_checkClear");        

    for(var i=0;i<insurance_delList.length;i++)
    {
        insurance_delList[i].checked = checkboxstatus;//(chkBtn.checked)? true : false;            
    }        
    
    insurance_checkedAll=checkboxstatus;
}


function insurance_clearSelected()
{
    var insurance_delList=document.getElementsByName("insurance_checkClear"); 
    var insurance_checkedNum=0;
    var insurance_allFieldsClear=0;
    
    for(var i=(insurance_delList.length-1);i>=0;i--)
    {
        if(insurance_delList[i].checked===true)
        {
            insurance_checkedNum++;        
            
            jQuery('#insurance_related_tables'+i).val('none');
            jQuery('#insurance_txtFieldName'+i).val('');
            jQuery('#insurance_data_types'+i).val('none');
            jQuery('#insurance_txtDataLength'+i).val('');
            /*
            jQuery('#insurance_txtFieldName'+i).prop('readonly','');                            
            jQuery('#insurance_data_types'+i).prop('disabled','');    
            jQuery('#insurance_txtDataLength'+i).prop('readonly','');
            */
            jQuery('#insurance_boolFieldRequired'+i).prop('checked', false);            
            jQuery('#insurance_txtDefaultValue'+i).val('');
            
            if(jQuery('#insurance_img_calendar'+i).css('display')==='inline' || jQuery('#insurance_img_calendar'+i).css('display')==='block')
            {
                jQuery('#insurance_img_calendar'+i).hide();
            }
            
            jQuery('#insurance_view_edit'+i).val('Y');            
            insurance_delList[i].checked=false;
            
            for(var j=i;j<(insurance_totalFields);j++)
            {
                jQuery('#insurance_related_tables'+j).val(jQuery('#insurance_related_tables'+(j+1)).val());
                jQuery('#insurance_txtFieldName'+j).val(jQuery('#insurance_txtFieldName'+(j+1)).val());
                jQuery('#insurance_data_types'+j).val(jQuery('#insurance_data_types'+(j+1)).val());
                jQuery('#insurance_txtDataLength'+j).val(jQuery('#insurance_txtDataLength'+(j+1)).val());                
                
                var fn = jQuery('#insurance_txtFieldName'+(j+1)).prop('readonly') ? 'readonly' :'';
                jQuery('#insurance_txtFieldName'+j).prop('readonly',fn);                            
                
                var dt = jQuery('#insurance_data_types'+(j+1)).prop('disabled') ? 'disabled' :'';
                jQuery('#insurance_data_types'+j).prop('disabled',dt);                          
                                
                var dl = jQuery('#insurance_txtDataLength'+(j+1)).prop('readonly') ? 'readonly' : '';
                jQuery('#insurance_txtDataLength'+j).prop('readonly',dl);                                                       
                
                var fr = jQuery('#insurance_boolFieldRequired'+(j+1)).is(':checked') ? true : false;
                jQuery('#insurance_boolFieldRequired'+j).prop('checked', fr);
                                
                jQuery('#insurance_txtDefaultValue'+j).val(jQuery('#insurance_txtDefaultValue'+(j+1)).val());
                jQuery('#insurance_view_edit'+j).val(jQuery('#insurance_view_edit'+(j+1)).val());                                   
                                
                var frReq = jQuery('#insurance_boolFieldRequired'+(j+1)).prop('disabled') ? 'disabled' : '';
                jQuery('#insurance_boolFieldRequired'+j).prop('disabled',frReq);                                                       
                
                var dv = jQuery('#insurance_txtDefaultValue'+(j+1)).prop('disabled') ? 'disabled' : '';
                jQuery('#insurance_txtDefaultValue'+j).prop('disabled', dv);
                
                var imgCal = (jQuery('#insurance_img_calendar'+(j+1)).css('display')==='inline' || jQuery('#insurance_img_calendar'+(j+1)).css('display')==='block') ? 'inline' : 'none';
                
                jQuery('#insurance_img_calendar'+j).css('display', imgCal);                
                                                
                jQuery('#insurance_related_tables'+(j+1)).val('none');
                jQuery('#insurance_txtFieldName'+(j+1)).val('');
                jQuery('#insurance_data_types'+(j+1)).val('none');
                jQuery('#insurance_txtDataLength'+(j+1)).val('');            

                jQuery('#insurance_txtFieldName'+(j+1)).prop('readonly','');                            
                jQuery('#insurance_data_types'+(j+1)).prop('disabled','');    
                jQuery('#insurance_txtDataLength'+(j+1)).prop('readonly','');

                jQuery('#insurance_boolFieldRequired'+(j+1)).prop('checked', false);            
                jQuery('#insurance_txtDefaultValue'+(j+1)).val('');
                jQuery('#insurance_img_calendar'+(j+1)).hide();             
                jQuery('#insurance_boolFieldRequired'+(j+1)).prop('disabled','');    
                jQuery('#insurance_txtDefaultValue'+(j+1)).prop('disabled','');    
                
                jQuery('#insurance_view_edit'+(j+1)).val('Y');                                      
            }
            
            jQuery('#insurance_related_tables'+j).val('none');
            jQuery('#insurance_txtFieldName'+j).val('');
            jQuery('#insurance_data_types'+j).val('none');
            jQuery('#insurance_txtDataLength'+j).val('');            

            jQuery('#insurance_txtFieldName'+j).prop('readonly','');                            
            jQuery('#insurance_data_types'+j).prop('disabled','');    
            jQuery('#insurance_txtDataLength'+j).prop('readonly','');

            jQuery('#insurance_boolFieldRequired'+j).prop('checked', false);            
            jQuery('#insurance_txtDefaultValue'+j).val('');
            jQuery('#insurance_img_calendar'+j).hide();          
            jQuery('#insurance_view_edit'+j).val('Y');                
                      
            //if(i===0)
            if(i===0 && insurance_totalFields===0)
            {insurance_allFieldsClear=1;}
                      
        }
        
        jQuery('#insurance_check_All').prop('checked', false);   
    }
    
    //insurance_totalFields=0;
    
    insurance_totalFields = (insurance_allFieldsClear===1 || insurance_checkedAll===true) ? 0 : Math.abs(insurance_totalFields-insurance_checkedNum);
    insurance_checkedAll=false;
    //alert('now tf= '+insurance_totalFields);
}


</script>
