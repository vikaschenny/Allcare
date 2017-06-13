<!--
<style type="text/css">@import url(../../library/dynarch_calendar.css);</style>
<script type="text/javascript" src="../../library/dialog.js"></script>
<script type="text/javascript" src="../../library/textformat.js"></script>
<script type="text/javascript" src="../../library/dynarch_calendar.js"></script>
<script type="text/javascript" src="../../library/dynarch_calendar_en.js"></script>
<script type="text/javascript" src="../../library/dynarch_calendar_setup.js"></script>
-->
<?php

      echo "<table id='provider_tbl_AllCare' name='provider_tbl_AllCare'>
                <tr title='1 to 1' border='1'>
                    <td valign='top' colspan='2'><b>AllCare_Provider1to1 </b></td>
                    <td valign='top'><input id='provider_chk1to1' type='checkbox' value='1' name='provider_chk1to1'></td>
                </tr>
                <tr title='1 to n' border='1'>
                    <td valign='top' colspan='2'><b>AllCare_Provider1ton </b></td>
                    <td valign='top'><input id='provider_chk1ton' type='checkbox' value='1' name='provider_chk1ton'></td>
                </tr>
            </table>
            <input type='button' id='provider_btnNext' name='provider_btnNext' value='Next' style='float: right; margin-top: -35px;'
                   onclick='javascript: jQuery(\"#provider_tbl_AllCare\").hide();
                                        jQuery(\"#provider_tbl_grid\").hide();
                                        jQuery(\"#provider_tbl_step2\").show();
                                        jQuery(\"#provider_btnNext\").hide();
                                        jQuery(\"#provider_btnBack\").show();
                                        jQuery(\"#provider_btnSaveFields\").hide();                     
                                        jQuery(\"#provider_btnClear\").hide();  
                                        jQuery(\"#provider_btn_Cancel\").hide();  ' />
            <input type='button' id='provider_btnBack' name='provider_btnBack' value='Back' style='display:none' 
                   onclick='javascript: jQuery(\"#provider_tbl_AllCare\").show();
                                        jQuery(\"#provider_tbl_grid\").show();
                                        jQuery(\"#provider_tbl_step2\").hide();
                                        jQuery(\"#provider_btnBack\").hide();
                                        jQuery(\"#provider_btnNext\").show();
                                        jQuery(\"#provider_btnSaveFields\").show();                     
                                        jQuery(\"#provider_btnClear\").show();  
                                        jQuery(\"#provider_btn_Cancel\").show();' />
            <br>
            <table border='1' id='provider_tbl_grid' name='provider_tbl_grid'>                
                <tr>
                    <th><input type='checkbox' id='provider_check_All' name='provider_check_All' onclick='provider_checkAll(this.checked);' /></th>
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
                    <td align='center'><input type='checkbox' id='provider_checkClear".$cnt."' name='provider_checkClear' /></td>";
                                           
      echo "<td><select id='provider_related_tables".$cnt."' name='provider_related_tables' onchange='javascript:return provider_newEvt(this.id);' style='width:125px'>          
            <option value='none'>none</option>
                $table_list
            </select></td>
            <td><input type='text' id='provider_txtFieldName".$cnt."' name='provider_txtFieldName' style='width:125px' /></td>";

            //echo "<td><input type='text' id='provider_txtFieldType".$cnt."' name='provider_txtFieldType[]' style='width:125px' /></td>";
            
            echo "<td>
                    <select id='provider_data_types".$cnt."' name='provider_data_types' onchange='provider_checkDataType();'>
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
                                              
            echo "<td><input type='text' id='provider_txtDataLength".$cnt."' name='provider_txtDataLength' style='width:125px' /></td>
            <td align='center'>
                <input type='checkbox' id='provider_boolFieldRequired".$cnt."' name='provider_boolFieldRequired' />
            </td>
            <td>
                <!--<input type='text' id='provider_txtDefaultValue".$cnt."' name='provider_txtDefaultValue' style='width:125px' />-->
                <input type='text' size='10' name='provider_txtDefaultValue' id='provider_txtDefaultValue".$cnt."' onkeyup='datekeyup(this,mypcc)' onblur='dateblur(this,mypcc)' title='yyyy-mm-dd last date of this event' />
<img src='../../interface/pic/show_calendar.gif' align='absbottom' width='24' height='22'
id='provider_img_calendar".$cnt."' border='0' alt='[?]' style='cursor:pointer;display:none;'
title='Click here to choose a date'>
<script>
Calendar.setup({inputField:'provider_txtDefaultValue".$cnt."', ifFormat:'%Y-%m-%d', button:'provider_img_calendar".$cnt."'});
</script>
            
            </td>
                                       
            <td>
                <select id='provider_view_edit".$cnt."' name='provider_view_edit' style='width:100px'>
                    <option value='Y'>Edit</option>
                    <option value='N'>View</option>                    
                </select>
            </td>
        </tr>";
      } 
      echo "</table>";                 
        
?>

    <table id="provider_tbl_step2" name="provider_tbl_step2" style="display:none;">
        
        <tr><td></td></tr>
        <tr> <td></td>               
            <td align="left"><input type="radio" id="provider_rd1to1" name="provider_radallcare" value="1to1"
                       onchange="javascript:jQuery('#provider_lblExistingRecordsets').hide();
                                            jQuery('#provider_lblExistingGroups').show();  
                                            provider_showGroupsRecordsets();
                                            provider_hideNewButtonData();
                                            jQuery('#td_Show_YesNo').hide();
                                            ">
            AllCare_Provider1to1</td>
        </tr>
        <tr>   <td></td>             
            <td align="left">
                <input type="radio" id="provider_rd1ton" name="provider_radallcare" value="1ton"
                       onchange="javascript:jQuery('#provider_lblExistingRecordsets').show();
                                            jQuery('#provider_lblExistingGroups').hide();  
                                            provider_showGroupsRecordsets();
                                            provider_hideNewButtonData();      
                                            jQuery('#td_Show_YesNo').hide();
                                            " />
            AllCare_Provider1ton</td>
        </tr>  
        <tr><td></td></tr>
        <tr>
            <td style="width:150px"><label id='provider_lblExistingGroups' style='display:none;'>Existing Groups </label>
                <label id='provider_lblExistingRecordsets' style='display:none;'>Existing Recordsets</label>
            </td>
            <td>
                <div id="provider_showGroupsRecordsets" name="provider_showGroupsRecordsets"></div>
            </td>
        </tr>
        <tr>
            <td>
                <label id='provider_lblFieldsSelected' style='display:none;'>Field Selected : </label>
            </td>
            <td>
                <div id="provider_showFieldsByGroupRecordsets" name="provider_showFieldsByGroupRecordsets"></div>
            </td>
        </tr>
        <tr>
            <td><label id='provider_lblGroup' style='display:none;'>Group Name</label>
                <label id='provider_lblRecordset' style='display:none;'>Recordset</label>
            </td>
            <td>
                <input type="text" id='provider_txtGroupRecordset' name="provider_txtGroupRecordset" value="" style='display:none;'>
            </td>
        </tr>
        <tr>
            <td><label id='provider_lblExistingFields' style='display:none;'>Fields : </label></td>           
            <td>
                <div id="provider_showFields" name="provider_showFields" style='border:1px'>               
                </div>
            </td>
        </tr>
<!--        <tr><td></td>
            <td id="td_Show_YesNo" style="display:none;"><label id='provider_lblShowYesNo'>Show this Group/Recordset      
                <input type='radio' id='rd_show_yes' name='rd_show_yes_no' value='Y' checked /><label for='rd_show_yes'>Yes</label>
                <input type='radio' id='rd_show_no' name='rd_show_yes_no' value='N' /><label for='rd_show_no'>No</label>
            </td>
        </tr>-->

        <tr>
            <td colspan="2" align="center">      
    <!--            <input type='submit' id='provider_btnSave' name='provider_btnSave' value='Save' />-->
                <input type='button' id='provider_btnSave' name='provider_btnSave' value='Save' 
                       onclick='javascript: if(jQuery("#provider_comboGroupsRecordsets").val()!=="none")    
                                            {
                                                if(provider_validateStep2(0))
                                                {
                                                    provider_editGroupRecordset();
                                                }
                                            }
                                            if(jQuery("#provider_txtGroupRecordset").val()!=="")
                                            {
                                                if(provider_validateStep2(1))
                                                {
                                                    provider_insert_in_mapping();
                                                }
                                            }
                                            if(jQuery("#insurance_comboGroupsRecordsets").val()==="none" && jQuery("#insurance_txtGroupRecordset").val()==="")
                                            {
                                                alert("Enter the Group/Recordset name");
                                                
                                            }
                                        ' />
                <input type='button' id='provider_btnCancel' name='provider_btnCancel' value='Cancel'
                       onclick='javascript:location.href="edit_globals.php";'/>
            </td>
        </tr>
     </table>

    <br>
        <center>                   
            <input type='button' id='provider_btnSaveFields' name='provider_btnSaveFields' value='Save Fields' 
                   onclick='javascript:if(provider_validateStep1())
                                       {
                                            if(!provider_checkIfFieldAlreadyExists() && provider_validateDataTypes() && provider_validateDefaultValues())
                                            {   
                                                provider_insertFields();
                                            }                                            
                                       }' />                                
            <input type='button' id='provider_btnClear' name='provider_btnClear' value='Clear' onclick='javascript:provider_clearSelected();' />
            <input type='button' id='provider_btn_Cancel' name='provider_btn_Cancel' value='Cancel' 
                   onclick='javascript:location.href="edit_globals.php";' />
        </center>
    

<script type='text/javascript'>
    
function provider_newEvt(selectedBox)
{
     if(document.getElementById(selectedBox).value!=='none')
     {
  dlgopen('provider_get_table_fields.php?table_name='+document.getElementById(selectedBox).value+'&selectedBox='+selectedBox,
   '_blank', 550, 270);
     }
  return false;
}

var provider_totalFields=0;
 
function provider_receivedFromChild(selectedBox,tableName,checkedFields,checkedFieldsType)
{
    var SBNum=0;
    var s=selectedBox.split("provider_related_tables"); 
    SBNum=s[1];
    /*
    if(SBNum>(provider_totalFields+1))
    {
        SBNum=(provider_totalFields===0)?0:provider_totalFields;
    }
    */
    //alert('SBNum = '+SBNum);    
    //alert('length = '+checkedFields.length);  
    //alert('provider_totalFields = '+provider_totalFields);  
    var split0='',split1='',split2='';        
                
    jQuery('#provider_related_tables'+SBNum).val(tableName);
    jQuery('#provider_txtFieldName'+SBNum).val(checkedFields[0]);
    
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
    
    jQuery('#provider_data_types'+SBNum).val(split1);    
    jQuery('#provider_txtDataLength'+SBNum).val(split2[0]);
        
    if(jQuery('#provider_related_tables'+SBNum).val()!=='none')
    {
        jQuery('#provider_txtFieldName'+SBNum).attr('readonly','readonly');                            
        jQuery('#provider_data_types'+SBNum).attr('disabled','disabled');    
        jQuery('#provider_txtDataLength'+SBNum).attr('readonly','readonly');
    }
    
    if(jQuery('#provider_data_types'+SBNum).val()==='text' || jQuery('#provider_data_types'+SBNum).val()==='tinytext' || jQuery('#provider_data_types'+SBNum).val()==='mediumtext' ||
               jQuery('#provider_data_types'+SBNum).val()==='longtext' || jQuery('#provider_data_types'+SBNum).val()==='year' ||
               jQuery('#provider_data_types'+SBNum).val()==='blob' || jQuery('#provider_data_types'+SBNum).val()==='tinyblob' ||
               jQuery('#provider_data_types'+SBNum).val()==='mediumblob' || jQuery('#provider_data_types'+SBNum).val()==='longblob')
    {
            jQuery('#provider_boolFieldRequired'+SBNum).attr('disabled','disabled');
            jQuery('#provider_txtDefaultValue'+SBNum).attr('disabled','disabled');
    }
    
    (jQuery('#provider_data_types'+SBNum).val()==='date' || 
     jQuery('#provider_data_types'+SBNum).val()==='datetime') ? jQuery('#provider_img_calendar'+SBNum).show() : jQuery('#provider_img_calendar'+SBNum).hide();
            
                        
    if(SBNum>=provider_totalFields)   
    {
        provider_totalFields++;
    }

    for(var i=provider_totalFields;i<(provider_totalFields+checkedFields.length-1);i++)
    {
        jQuery('#provider_related_tables'+i).val(tableName);
        jQuery('#provider_txtFieldName'+i).val(checkedFields[i-provider_totalFields+1]);
        
        if(checkedFieldsType[i-provider_totalFields+1].indexOf('(') !== -1)  // datatype contains '('  e.g. varchar(100)
        {
            split0 = checkedFieldsType[i-provider_totalFields+1].split("(");
            split1 = split0[0];
            split2 = split0[1].split(")");
        }
        else
        {
            split1 = checkedFieldsType[i-provider_totalFields+1];   
            split2 = '';
        }
        
        jQuery('#provider_data_types'+i).val(split1);    
        jQuery('#provider_txtDataLength'+i).val(split2[0]);
                
        if(jQuery('#provider_related_tables'+i).val()!=='none')
        {
            jQuery('#provider_txtFieldName'+i).attr('readonly','readonly');                            
            jQuery('#provider_data_types'+i).attr('disabled','disabled');    
            jQuery('#provider_txtDataLength'+i).attr('readonly','readonly');
        }     
        
        if(jQuery('#provider_data_types'+i).val()==='text' || jQuery('#provider_data_types'+i).val()==='tinytext' || jQuery('#provider_data_types'+i).val()==='mediumtext' ||
               jQuery('#provider_data_types'+i).val()==='longtext' || jQuery('#provider_data_types'+i).val()==='year' ||
               jQuery('#provider_data_types'+i).val()==='blob' || jQuery('#provider_data_types'+i).val()==='tinyblob' ||
               jQuery('#provider_data_types'+i).val()==='mediumblob' || jQuery('#provider_data_types'+i).val()==='longblob')
        {
                jQuery('#provider_boolFieldRequired'+i).attr('disabled','disabled');
                jQuery('#provider_txtDefaultValue'+i).attr('disabled','disabled');
        }
         
        if(jQuery('#provider_data_types'+i).val()==='date' || jQuery('#provider_data_types'+i).val()==='datetime')
        {            
                jQuery('#provider_img_calendar'+i).show();
        }    
                  
    }

    provider_totalFields=(SBNum>=provider_totalFields) ? (provider_totalFields+checkedFields.length-1) : (provider_totalFields+checkedFields.length);  
    provider_totalFields--;
    
}
  
function provider_unselectTable(selectedBox)
{
    jQuery('#'+selectedBox).val('none');    
}
          
function provider_addRow(numRow,tableName,checkFieldName,checkFieldType,checkFieldSize)
{
        for(var i=0;i<numRow;i++)
        {
            jQuery('#provider_related_tables'+numRow).val(tableName);
            jQuery('#provider_txtFieldName'+numRow).val(checkFieldName);
            //jQuery('#txtFieldType'+numRow).val(checkFieldType);
            jQuery('#provider_txtDataLength'+numRow).val(checkFieldSize);
            jQuery('#provider_data_types'+numRow).val(checkFieldType);
            //jQuery('#provider_txtDefaultValue'+numRow).val(tableName);                                
        }
}

function provider_skip_none(array_name)
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

function provider_skip_blank(array_name)
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

function provider_validateStep1()
{
    if(!(jQuery('#provider_chk1to1').is(':checked')) && !(jQuery('#provider_chk1ton').is(':checked')))
    {
        alert("Please check the table AllCare_Provider1to1 or AllCare_Provider1ton");
        return false;
    }
        
    var relTables=document.getElementsByName("provider_related_tables");    
    //var fieldsEntered=document.getElementsByName("related_tables");    
    
    var flag=0;
    for(var j=0;j<relTables.length;j++)
    {
        if(relTables[j].value==='none' && jQuery('#provider_txtFieldName'+j).val()==='')
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
    
        
    var fieldName=document.getElementsByName("provider_txtFieldName"); 
    for(var k=0;k<fieldName.length;k++)
    {
        if(/\s/.test(jQuery('#provider_txtFieldName'+k).val()))
        {
            alert("Remove space in the Field name '"+jQuery('#provider_txtFieldName'+k).val()+"'");
            return false;           
        }
    }
    
    return true;
    
}

/*
function provider_validateStep2()
{
    if(!(jQuery('#provider_rd1to1').is(':checked')) && !(jQuery('#provider_rd1ton').is(':checked')))
    {
        alert("Please check the table AllCare_Provider1to1 or AllCare_Provider1ton");
        return false;
    }    
    
//    else if(jQuery('#provider_txtGroupRecordset').val()==='')
//    {
//        alert("Please enter the Group/Recordset name");
//        return false;
//    }
//    
//    else if(!jQuery('#provider_comboFields').val())
//    {
//        alert("Please select the field/s");
//        return false;
//    }
        
    var checked_fields=document.getElementsByName("provider_chkSelectedFields");        
    var flag=0;
    for(var i=0;i<checked_fields.length;i++)
    {
        //provider_delList[i].checked = checkboxstatus;//(chkBtn.checked)? true : false;   
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
*/


function provider_validateStep2(mapping_flag)
{
    if(!(jQuery('#provider_rd1to1').is(':checked')) && !(jQuery('#provider_rd1ton').is(':checked')))
    {
        alert("Please check the table AllCare_Provider1to1 or AllCare_Provider1ton");
        return false;
    }
       
    var checked_fields;
    if(mapping_flag===0)
    {
        checked_fields=document.getElementsByName("provider_chkSelectedFields");        
    }
    else if(mapping_flag===1)
    {
        checked_fields=document.getElementsByName("provider_chkAllFields");        
    }
        //alert('mf=='+mapping_flag);
    //var checked_fields=document.getElementsByName("provider_chkSelectedFields");        
    var flag=0;
    for(var i=0;i<checked_fields.length;i++)
    {
        //provider_delList[i].checked = checkboxstatus;//(chkBtn.checked)? true : false;   
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


function provider_checkIfFieldAlreadyExists()
{
    var FieldName='';

    var chk1to1=(jQuery('#provider_chk1to1').is(':checked'))?1:0;
    var chk1ton=(jQuery('#provider_chk1ton').is(':checked'))?1:0;
    
    for(var i=0;i<20;i++)
    {      
        if(jQuery('#provider_txtFieldName'+i).val()!=='')
        {
            FieldName=jQuery('#provider_txtFieldName'+i).val();    
            var exists;
            $.ajax({
                type: 'POST',
                url: 'provider_check_if_field_already_exists.php',	
                dataType: "html",
		async: false, 
                data: {FieldName:FieldName,chk1to1:chk1to1,chk1ton:chk1ton},

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

function provider_validateDataTypes()
{
    for(var i=0;i<20;i++)
    {           
        if(jQuery('#provider_txtFieldName'+i).val()!=='' && jQuery('#provider_data_types'+i).val()!=='')
        {
            // datalength is NOT mandatory for the following data types , 
            // eg. if the data type is 'date' then don't validate its empty datalength.
            //     else if data type is 'int' then validate its empty datalength
            if(jQuery('#provider_data_types'+i).val()!=='date' && jQuery('#provider_data_types'+i).val()!=='datetime' &&
               jQuery('#provider_data_types'+i).val()!=='time' && jQuery('#provider_data_types'+i).val()!=='timestamp' &&
               jQuery('#provider_data_types'+i).val()!=='text' && jQuery('#provider_data_types'+i).val()!=='tinytext' && 
               jQuery('#provider_data_types'+i).val()!=='mediumtext' && jQuery('#provider_data_types'+i).val()!=='longtext' && 
               jQuery('#provider_data_types'+i).val()!=='blob' && jQuery('#provider_data_types'+i).val()!=='tinyblob' &&
               jQuery('#provider_data_types'+i).val()!=='mediumblob' && jQuery('#provider_data_types'+i).val()!=='longblob' &&                        
               jQuery('#provider_data_types'+i).val()!=='geometry' && jQuery('#provider_data_types'+i).val()!=='point' &&
               jQuery('#provider_data_types'+i).val()!=='linestring' && jQuery('#provider_data_types'+i).val()!=='polygon' &&  
               jQuery('#provider_data_types'+i).val()!=='multipoint' && jQuery('#provider_data_types'+i).val()!=='multilinestring' &&    
               jQuery('#provider_data_types'+i).val()!=='multipolygon' && jQuery('#provider_data_types'+i).val()!=='geometrycollection')
            {
                if(jQuery('#provider_txtDataLength'+i).val()==='' && 
                   jQuery('#provider_txtDataLength'+i).attr('readonly')===true) //  isNaN(jQuery('#provider_txtDataLength'+i).val())
                {
                    alert('Please enter a valid numerical Data Length for the field '+jQuery('#provider_txtFieldName'+i).val());
                    jQuery('#provider_txtDataLength'+i).focus();
                    return false;                            
                }
            }
        }                        
    }
    return true;
}

function provider_validateDefaultValues()
{        
    for(var i=0;i<20;i++)
    {          
        if(jQuery('#provider_txtFieldName'+i).val()!=='')
        {
            var FieldName=jQuery('#provider_txtFieldName'+i).val();               
            var dataType=jQuery('#provider_data_types'+i).val();
            var defaultValue=jQuery('#provider_txtDefaultValue'+i).val();
            
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
                                jQuery('#provider_txtDefaultValue'+i).val('');
                                //jQuery('#data_types'+i).val('none');
                                return false;
                            }  
                            if(dataLength!=='1')
                            {
                                alert("Data Length for the 'char' field "+FieldName+" should be 1");
                                jQuery('#provider_txtDataLength'+i).val('');
                                //jQuery('#provider_data_types'+i).val('none');
                                return false;
                            }
                            break;
                                            
            }
            
            if(jQuery('#provider_boolFieldRequired'+i).is(':checked') && defaultValue===''
                && (jQuery('#provider_data_types'+i).val()!=='text' && jQuery('#provider_data_types'+i).val()!=='tinytext' && 
                    jQuery('#provider_data_types'+i).val()!=='mediumtext' && jQuery('#provider_data_types'+i).val()!=='longtext' &&
                    jQuery('#provider_data_types'+i).val()!=='blob' && jQuery('#provider_data_types'+i).val()!=='tinyblob' &&
                    jQuery('#provider_data_types'+i).val()!=='mediumblob' && jQuery('#provider_data_types'+i).val()!=='longblob'))                                        
            {
                alert('Please enter the Default value for the field '+FieldName+' checked as Required');
                return false;
            }                                    
        }
    }
    return true;
}

function provider_checkDataType()
{
    for(var i=0;i<20;i++)
    { 
        jQuery('#provider_img_calendar'+i).hide(); 
        jQuery('#provider_boolFieldRequired'+i).prop('disabled','');
        jQuery('#provider_txtDefaultValue'+i).prop('disabled','');
        jQuery('#provider_txtDataLength'+i).prop('readonly','');
        
        if(jQuery('#provider_data_types'+i).val()==='text' || jQuery('#provider_data_types'+i).val()==='tinytext' || 
           jQuery('#provider_data_types'+i).val()==='mediumtext' || jQuery('#provider_data_types'+i).val()==='longtext' || 
           jQuery('#provider_data_types'+i).val()==='blob' || jQuery('#provider_data_types'+i).val()==='tinyblob' ||
           jQuery('#provider_data_types'+i).val()==='mediumblob' || jQuery('#provider_data_types'+i).val()==='longblob')
        {
                jQuery('#provider_boolFieldRequired'+i).attr('disabled','disabled');
                jQuery('#provider_txtDefaultValue'+i).attr('disabled','disabled');
        }
        if(jQuery('#provider_data_types'+i).val()==='date' || jQuery('#provider_data_types'+i).val()==='datetime')
        {
            jQuery('#provider_img_calendar'+i).show(); 
            jQuery('#provider_txtDataLength'+i).attr('readonly','readonly');
        }
    }
}

function provider_insertFields()
{
        var finalURL='provider_add_new_fields.php';  
        
        var chk1to1=(jQuery('#provider_chk1to1').is(':checked'))?1:0;
        var chk1ton=(jQuery('#provider_chk1ton').is(':checked'))?1:0;
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
            relatedTables[i]=jQuery('#provider_related_tables'+i).val();
            //relatedTables.push(jQuery('#provider_related_tables'+i).val());
            FieldName[i]=jQuery('#provider_txtFieldName'+i).val();                                    
            FieldType[i]=jQuery('#provider_data_types'+i).val();
            DataLength[i]=jQuery('#provider_txtDataLength'+i).val();
            FieldRequired[i]=(jQuery('#provider_boolFieldRequired'+i).is(':checked'))?1:0;
            DefaultValue[i]=jQuery('#provider_txtDefaultValue'+i).val();            
            FieldViewEdit[i]=jQuery('#provider_view_edit'+i).val();                                    	            
        }                       
                
        relatedTables=provider_skip_none(relatedTables);                
        relatedTables=relatedTables.toString();
        
        FieldName=provider_skip_blank(FieldName);
        FieldName=FieldName.toString();
        
        FieldType=provider_skip_none(FieldType);
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
		data: {chk1to1:chk1to1,chk1ton:chk1ton,relatedTables:relatedTables,FieldName:FieldName,
                       FieldType:FieldType,DataLength:DataLength,FieldRequired:FieldRequired,DefaultValue:DefaultValue,
                       FieldViewEdit:FieldViewEdit},
		
		success: function(response)
		{                         
                     alert(response);     
                     jQuery("#provider_tbl_grid").hide();     
                     jQuery("#provider_tbl_AllCare").hide();  
                     
                     jQuery("#provider_btnSaveFields").hide();                     
                     jQuery("#provider_btnClear").hide();  
                     jQuery("#provider_btn_Cancel").hide();  
                     jQuery("#provider_tbl_step2").show(); 
                     jQuery("#provider_btnNext").hide();
                     jQuery("#provider_btnBack").show();
                     
		},
		failure: function(response)
		{
			alert("error");
		}		
        });	       
}

function provider_showGroupsRecordsets()
{
    var finalURL='provider_show_groups_recordsets.php';  
    var table_name=jQuery("#provider_rd1to1").is(":checked")?'tbl_allcare_provider1to1_fieldmapping':'tbl_allcare_provider1ton_fieldmapping';    
           
    $.ajax({
                type: 'POST',
                url: finalURL,	
                data: {table_name:table_name},

                //data:relatedTables,
                success: function(response)
                {                         
                     jQuery("#provider_showGroupsRecordsets").html(response);                     
                },
                failure: function(response)
                {
                        alert("error");
                }		
            });	
}

function provider_showFields()
{                         
        var finalURL='provider_show_fields.php';  
        var table_name=jQuery("#provider_rd1to1").is(":checked")?'tbl_allcare_provider1to1':'tbl_allcare_provider1ton';
                
        $.ajax({
                    type: 'POST',
                    url: finalURL,	
                    data: {table_name:table_name},

                    //data:relatedTables,
                    success: function(response)
                    {                         
                         jQuery("#provider_showFields").html(response);                     
                    },
                    failure: function(response)
                    {
                            alert("error");
                    }		
               });		
}

function provider_hideNewButtonData()
{
    jQuery('#provider_lblGroup').hide();  
    jQuery('#provider_lblRecordset').hide();  
    jQuery('#provider_lblExistingFields').hide();  
    jQuery('#provider_txtGroupRecordset').val('');  
    jQuery('#provider_txtGroupRecordset').hide();      
    jQuery('#provider_lblFieldsSelected').hide();   
    jQuery('#provider_showFieldsByGroupRecordsets').html(''); 
    jQuery('#provider_showFields').html(''); 
}

function provider_showFieldsByGroupRecordsets(tableName)
{
        var finalURL='provider_show_fields_by_groups_recordsets.php';  
        //var table_name=jQuery("#provider_rd1to1").is(":checked")?'tbl_allcare_provider1to1_fieldmapping':'tbl_allcare_provider1ton_fieldmapping';
        var table_name=tableName;
        var selectedGR=jQuery("#provider_comboGroupsRecordsets").val();        
        
        $.ajax({
                    type: 'POST',
                    url: finalURL,	
                    data: {table_name:table_name,selectedGR:selectedGR},

                    //data:relatedTables,
                    success: function(response)
                    {                         
                         jQuery("#provider_showFieldsByGroupRecordsets").html(response);                     
                    },
                    failure: function(response)
                    {
                            alert("error");
                    }		
               });		
}

function provider_editGroupRecordset()  
{               
        var finalURL='provider_edit_group_recordset.php';
        
        var TableName=(jQuery('#provider_rd1to1').is(':checked'))?'tbl_allcare_provider1to1_fieldmapping':'tbl_allcare_provider1ton_fieldmapping';    
        
        var GroupRecordsetName=jQuery('#provider_comboGroupsRecordsets').val();
        
        var selectedFields=new Array();   
        
        var showYesNo = jQuery('#rd_show_yes').is(':checked') ? 'Y' : 'N';
        
        var checked_fields=document.getElementsByName("provider_chkSelectedFields");        

        for(i=0;i<checked_fields.length;i++)
        {
            //provider_delList[i].checked = checkboxstatus;//(chkBtn.checked)? true : false;   
            if(checked_fields[i].checked)
            {
                selectedFields.push(checked_fields[i].value);
            }
        }      
        
        selectedFields=selectedFields.toString();
        
        $.ajax({
            type: 'POST',
            url: finalURL,	
            data: {TableName:TableName,GroupRecordsetName:GroupRecordsetName,
                   selectedFields:selectedFields,showYesNo:showYesNo},

            //data:relatedTables,
            success: function(response)
            {                         
                 alert(response);   
                 provider_showGroupsRecordsets();
                 provider_hideNewButtonData();

            },
            failure: function(response)
            {
                    alert("error");
            }		
           });		                       
}

function provider_insert_in_mapping()
{
    var finalURL='provider_insert_in_mapping.php';                  
    
var mappingTableName=(jQuery('#provider_rd1to1').is(':checked'))?'tbl_allcare_provider1to1_fieldmapping':'tbl_allcare_provider1ton_fieldmapping';    
    
    
    var showYesNo = jQuery('#rd_show_yes').is(':checked') ? 'Y' : 'N';
    
    var groupRecordsetName=jQuery('#provider_txtGroupRecordset').val();

    //var selectedFields=jQuery('#comboFields').val();
    var selectedFields=new Array();
    
    var checked_fields=document.getElementsByName("provider_chkAllFields");            

    for(i=0;i<checked_fields.length;i++)
    {
        //provider_delList[i].checked = checkboxstatus;//(chkBtn.checked)? true : false;   
        if(checked_fields[i].checked)
        {
            selectedFields.push(checked_fields[i].value);
        }
    }      
               
    selectedFields=selectedFields.toString();
    
    $.ajax({
            type: 'POST',
            url: finalURL,	
            data: {mappingTableName:mappingTableName,groupRecordsetName:groupRecordsetName,
                   selectedFields:selectedFields,showYesNo:showYesNo},

            //data:relatedTables,
            success: function(response)
            {                         
                 alert(response);   
                 provider_showGroupsRecordsets();
                 provider_hideNewButtonData();

            },
            failure: function(response)
            {
                    alert("error");
            }		
           });		         
}

var provider_checkedAll=false;
function provider_checkAll(checkboxstatus)
{            
    var provider_delList=document.getElementsByName("provider_checkClear");        

    for(var i=0;i<provider_delList.length;i++)
    {
        provider_delList[i].checked = checkboxstatus;//(chkBtn.checked)? true : false;            
    }        
    
    provider_checkedAll=checkboxstatus;
}


function provider_clearSelected()
{
    var provider_delList=document.getElementsByName("provider_checkClear"); 
    var provider_checkedNum=0;
    var provider_allFieldsClear=0;
    
    for(var i=(provider_delList.length-1);i>=0;i--)
    {
        if(provider_delList[i].checked===true)
        {
            provider_checkedNum++;        
            
            jQuery('#provider_related_tables'+i).val('none');
            jQuery('#provider_txtFieldName'+i).val('');
            jQuery('#provider_data_types'+i).val('none');
            jQuery('#provider_txtDataLength'+i).val('');
            /*
            jQuery('#provider_txtFieldName'+i).prop('readonly','');                            
            jQuery('#provider_data_types'+i).prop('disabled','');    
            jQuery('#provider_txtDataLength'+i).prop('readonly','');
            */
            jQuery('#provider_boolFieldRequired'+i).prop('checked', false);            
            jQuery('#provider_txtDefaultValue'+i).val('');
            
            if(jQuery('#provider_img_calendar'+i).css('display')==='inline' || jQuery('#provider_img_calendar'+i).css('display')==='block')
            {
                jQuery('#provider_img_calendar'+i).hide();
            }
            
            jQuery('#provider_view_edit'+i).val('Y');            
            provider_delList[i].checked=false;
            
            for(var j=i;j<(provider_totalFields);j++)
            {
                jQuery('#provider_related_tables'+j).val(jQuery('#provider_related_tables'+(j+1)).val());
                jQuery('#provider_txtFieldName'+j).val(jQuery('#provider_txtFieldName'+(j+1)).val());
                jQuery('#provider_data_types'+j).val(jQuery('#provider_data_types'+(j+1)).val());
                jQuery('#provider_txtDataLength'+j).val(jQuery('#provider_txtDataLength'+(j+1)).val());                
                
                var fn = jQuery('#provider_txtFieldName'+(j+1)).prop('readonly') ? 'readonly' :'';
                jQuery('#provider_txtFieldName'+j).prop('readonly',fn);                            
                
                var dt = jQuery('#provider_data_types'+(j+1)).prop('disabled') ? 'disabled' :'';
                jQuery('#provider_data_types'+j).prop('disabled',dt);                          
                                
                var dl = jQuery('#provider_txtDataLength'+(j+1)).prop('readonly') ? 'readonly' : '';
                jQuery('#provider_txtDataLength'+j).prop('readonly',dl);                                                       
                
                var fr = jQuery('#provider_boolFieldRequired'+(j+1)).is(':checked') ? true : false;
                jQuery('#provider_boolFieldRequired'+j).prop('checked', fr);
                                
                jQuery('#provider_txtDefaultValue'+j).val(jQuery('#provider_txtDefaultValue'+(j+1)).val());
                jQuery('#provider_view_edit'+j).val(jQuery('#provider_view_edit'+(j+1)).val());                                   
                                
                var frReq = jQuery('#provider_boolFieldRequired'+(j+1)).prop('disabled') ? 'disabled' : '';
                jQuery('#provider_boolFieldRequired'+j).prop('disabled',frReq);                                                       
                
                var dv = jQuery('#provider_txtDefaultValue'+(j+1)).prop('disabled') ? 'disabled' : '';
                jQuery('#provider_txtDefaultValue'+j).prop('disabled', dv);
                
                var imgCal = (jQuery('#provider_img_calendar'+(j+1)).css('display')==='inline' || jQuery('#provider_img_calendar'+(j+1)).css('display')==='block') ? 'inline' : 'none';
                
                jQuery('#provider_img_calendar'+j).css('display', imgCal);                
                                                
                jQuery('#provider_related_tables'+(j+1)).val('none');
                jQuery('#provider_txtFieldName'+(j+1)).val('');
                jQuery('#provider_data_types'+(j+1)).val('none');
                jQuery('#provider_txtDataLength'+(j+1)).val('');            

                jQuery('#provider_txtFieldName'+(j+1)).prop('readonly','');                            
                jQuery('#provider_data_types'+(j+1)).prop('disabled','');    
                jQuery('#provider_txtDataLength'+(j+1)).prop('readonly','');

                jQuery('#provider_boolFieldRequired'+(j+1)).prop('checked', false);            
                jQuery('#provider_txtDefaultValue'+(j+1)).val('');
                jQuery('#provider_img_calendar'+(j+1)).hide();             
                jQuery('#provider_boolFieldRequired'+(j+1)).prop('disabled','');    
                jQuery('#provider_txtDefaultValue'+(j+1)).prop('disabled','');    
                
                jQuery('#provider_view_edit'+(j+1)).val('Y');                                      
            }
            
            jQuery('#provider_related_tables'+j).val('none');
            jQuery('#provider_txtFieldName'+j).val('');
            jQuery('#provider_data_types'+j).val('none');
            jQuery('#provider_txtDataLength'+j).val('');            

            jQuery('#provider_txtFieldName'+j).prop('readonly','');                            
            jQuery('#provider_data_types'+j).prop('disabled','');    
            jQuery('#provider_txtDataLength'+j).prop('readonly','');

            jQuery('#provider_boolFieldRequired'+j).prop('checked', false);            
            jQuery('#provider_txtDefaultValue'+j).val('');
            jQuery('#provider_img_calendar'+j).hide();          
            jQuery('#provider_view_edit'+j).val('Y');                
                      
            //if(i===0)
            if(i===0 && provider_totalFields===0)
            {provider_allFieldsClear=1;}
                      
        }
        
        jQuery('#provider_check_All').prop('checked', false);   
    }
    
    //provider_totalFields=0;
    
    provider_totalFields = (provider_allFieldsClear===1 || provider_checkedAll===true) ? 0 : Math.abs(provider_totalFields-provider_checkedNum);
    provider_checkedAll=false;
    //alert('now tf= '+provider_totalFields);
}


</script>
