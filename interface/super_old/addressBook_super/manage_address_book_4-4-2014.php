<!--
<style type="text/css">@import url(../../library/dynarch_calendar.css);</style>
<script type="text/javascript" src="../../library/dialog.js"></script>
<script type="text/javascript" src="../../library/textformat.js"></script>
<script type="text/javascript" src="../../library/dynarch_calendar.js"></script>
<script type="text/javascript" src="../../library/dynarch_calendar_en.js"></script>
<script type="text/javascript" src="../../library/dynarch_calendar_setup.js"></script>
-->
<?php

      echo "<table id='addressBook_tbl_AllCare' name='addressBook_tbl_AllCare'>
                <tr title='1 to 1' border='1'>
                    <td valign='top' colspan='2'><b>AllCare_AddressBook1to1 </b></td>
                    <td valign='top'><input id='addressBook_chk1to1' type='checkbox' value='1' name='addressBook_chk1to1'></td>
                </tr>
                <tr title='1 to n' border='1'>
                    <td valign='top' colspan='2'><b>AllCare_AddressBook1ton </b></td>
                    <td valign='top'><input id='addressBook_chk1ton' type='checkbox' value='1' name='addressBook_chk1ton'></td>
                </tr>
            </table>
            <input type='button' id='addressBook_btnNext' name='addressBook_btnNext' value='Next' style='float: right; margin-top: -35px;'
                   onclick='javascript: jQuery(\"#addressBook_tbl_AllCare\").hide();
                                        jQuery(\"#addressBook_tbl_grid\").hide();
                                        jQuery(\"#addressBook_tbl_step2\").show();
                                        jQuery(\"#addressBook_btnNext\").hide();
                                        jQuery(\"#addressBook_btnBack\").show();
                                        jQuery(\"#addressBook_btnSaveFields\").hide();                     
                                        jQuery(\"#addressBook_btnClear\").hide();  
                                        jQuery(\"#addressBook_btn_Cancel\").hide();  ' />
            <input type='button' id='addressBook_btnBack' name='addressBook_btnBack' value='Back' style='display:none' 
                   onclick='javascript: jQuery(\"#addressBook_tbl_AllCare\").show();
                                        jQuery(\"#addressBook_tbl_grid\").show();
                                        jQuery(\"#addressBook_tbl_step2\").hide();
                                        jQuery(\"#addressBook_btnBack\").hide();
                                        jQuery(\"#addressBook_btnNext\").show();
                                        jQuery(\"#addressBook_btnSaveFields\").show();                     
                                        jQuery(\"#addressBook_btnClear\").show();  
                                        jQuery(\"#addressBook_btn_Cancel\").show();' />
            <br>
            <table border='1' id='addressBook_tbl_grid' name='addressBook_tbl_grid'>                
                <tr>
                    <th><input type='checkbox' id='addressBook_check_All' name='addressBook_check_All' onclick='addressBook_checkAll(this.checked);' /></th>
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
          $table_list .= "<option value='" . $row['table_name'] . "'>".$row['table_name']."</option>";
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
                    <td align='center'><input type='checkbox' id='addressBook_checkClear".$cnt."' name='addressBook_checkClear' /></td>";
                                           
      echo "<td><select id='addressBook_related_tables".$cnt."' name='addressBook_related_tables' onchange='javascript:return addressBook_newEvt(this.id);' style='width:125px'>          
            <option value='none'>none</option>
                $table_list
            </select></td>
            <td><input type='text' id='addressBook_txtFieldName".$cnt."' name='addressBook_txtFieldName' style='width:125px' /></td>";

            //echo "<td><input type='text' id='addressBook_txtFieldType".$cnt."' name='addressBook_txtFieldType[]' style='width:125px' /></td>";
            
            echo "<td>
                    <select id='addressBook_data_types".$cnt."' name='addressBook_data_types' onchange='addressBook_checkDataType();'>
                        <option value='none'>none</option>   
                        <option value='int'>tinyint</option>                        
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
                                              
            echo "<td><input type='text' id='addressBook_txtDataLength".$cnt."' name='addressBook_txtDataLength' style='width:125px' /></td>
            <td align='center'>
                <input type='checkbox' id='addressBook_boolFieldRequired".$cnt."' name='addressBook_boolFieldRequired' />
            </td>
            <td>
                <!--<input type='text' id='addressBook_txtDefaultValue".$cnt."' name='addressBook_txtDefaultValue' style='width:125px' />-->
                <input type='text' size='10' name='addressBook_txtDefaultValue' id='addressBook_txtDefaultValue".$cnt."' onkeyup='datekeyup(this,mypcc)' onblur='dateblur(this,mypcc)' title='yyyy-mm-dd last date of this event' />
<img src='../../interface/pic/show_calendar.gif' align='absbottom' width='24' height='22'
id='addressBook_img_calendar".$cnt."' border='0' alt='[?]' style='cursor:pointer;display:none;'
title='Click here to choose a date'>
<script>
Calendar.setup({inputField:'addressBook_txtDefaultValue".$cnt."', ifFormat:'%Y-%m-%d', button:'addressBook_img_calendar".$cnt."'});
</script>
            
            </td>
                                       
            <td>
                <select id='addressBook_view_edit".$cnt."' name='addressBook_view_edit' style='width:100px'>
                    <option value='Y'>Edit</option>
                    <option value='N'>View</option>                    
                </select>
            </td>
        </tr>";
      } 
      echo "</table>";                 
        
?>

    <table id="addressBook_tbl_step2" name="addressBook_tbl_step2" style="display:none;">
        
        <tr><td></td></tr>
        <tr> <td></td>               
            <td align="left"><input type="radio" id="addressBook_rd1to1" name="addressBook_radallcare" value="1to1"
                       onchange="javascript:jQuery('#addressBook_lblExistingRecordsets').hide();
                                            jQuery('#addressBook_lblExistingGroups').show();  
                                            addressBook_showGroupsRecordsets();
                                            addressBook_hideNewButtonData();
                                            jQuery('#td_Show_YesNo').hide();
                                            ">
            AllCare_AddressBook1to1</td>
        </tr>
        <tr>   <td></td>             
            <td align="left">
                <input type="radio" id="addressBook_rd1ton" name="addressBook_radallcare" value="1ton"
                       onchange="javascript:jQuery('#addressBook_lblExistingRecordsets').show();
                                            jQuery('#addressBook_lblExistingGroups').hide();  
                                            addressBook_showGroupsRecordsets();
                                            addressBook_hideNewButtonData();      
                                            jQuery('#td_Show_YesNo').hide();
                                            " />
            AllCare_AddressBook1ton</td>
        </tr>  
        <tr><td></td></tr>
        <tr>
            <td style="width:150px"><label id='addressBook_lblExistingGroups' style='display:none;'>Existing Groups </label>
                <label id='addressBook_lblExistingRecordsets' style='display:none;'>Existing Recordsets</label>
            </td>                                
            <td>                    
                <div id="addressBook_showGroupsRecordsets" name="addressBook_showGroupsRecordsets"></div>
            </td>
        </tr>
        <tr>
            <td>
                <label id='addressBook_lblFieldsSelected' style='display:none;'>Field Selected : </label>
            </td>
            <td>
                <div id="addressBook_showFieldsByGroupRecordsets" name="addressBook_showFieldsByGroupRecordsets"></div>
            </td>
        </tr>
        <tr>
            <td><label id='addressBook_lblGroup' style='display:none;'>Group Name</label>
                <label id='addressBook_lblRecordset' style='display:none;'>Recordset</label>
            </td>
            <td>
                <input type="text" id='addressBook_txtGroupRecordset' name="addressBook_txtGroupRecordset" value="" style='display:none;'>
            </td>
        </tr>
        <tr>     
            <td><label id='addressBook_lblExistingFields' style='display:none;'>Fields : </label></td>           
            <td>
                <div id="addressBook_showFields" name="addressBook_showFields" style='border:1px'>               
                </div> 
            </td>
        </tr>
        <tr><td></td>
            <td id="td_Show_YesNo" style="display:none;"><label id='addressBook_lblShowYesNo'>Show this Group/Recordset
        
                <input type='radio' id='rd_show_yes' name='rd_show_yes_no' value='Y' checked /><label for='rd_show_yes'>Yes</label>
                <input type='radio' id='rd_show_no' name='rd_show_yes_no' value='N' /><label for='rd_show_no'>No</label>
            </td>
        </tr>

        <tr>
            <td colspan="2" align="center">      
    <!--            <input type='submit' id='addressBook_btnSave' name='addressBook_btnSave' value='Save' />-->
                <input type='button' id='addressBook_btnSave' name='addressBook_btnSave' value='Save' 
                       onclick='javascript: if(jQuery("#addressBook_comboGroupsRecordsets").val()!=="none")    
                                            {
                                                addressBook_editGroupRecordset();
                                            }
                                            if(jQuery("#addressBook_txtGroupRecordset").val()!=="")
                                            {
                                                if(addressBook_validateStep2())
                                                {
                                                    addressBook_insert_in_mapping();
                                                }
                                            }' />
                <input type='button' id='addressBook_btnCancel' name='addressBook_btnCancel' value='Cancel'
                       onclick='javascript:location.href="edit_globals.php";'/>
            </td>
        </tr>
     </table>

    <br>
        <center>                   
            <input type='button' id='addressBook_btnSaveFields' name='addressBook_btnSaveFields' value='Save Fields' 
                   onclick='javascript:if(addressBook_validateStep1())
                                       {
                                            if(!addressBook_checkIfFieldAlreadyExists() && addressBook_validateDataTypes() && addressBook_validateDefaultValues())
                                            {   
                                                addressBook_insertFields();
                                            }                                            
                                       }' />                                
            <input type='button' id='addressBook_btnClear' name='addressBook_btnClear' value='Clear' onclick='javascript:addressBook_clearSelected();' />
            <input type='button' id='addressBook_btn_Cancel' name='addressBook_btn_Cancel' value='Cancel' 
                   onclick='javascript:location.href="edit_globals.php";' />
        </center>
    

<script type='text/javascript'>
    
function addressBook_newEvt(selectedBox)
{
     if(document.getElementById(selectedBox).value!=='none')
     {
  dlgopen('addressBook_get_table_fields.php?table_name='+document.getElementById(selectedBox).value+'&selectedBox='+selectedBox,
   '_blank', 550, 270);
     }
  return false;
}

var addressBook_totalFields=0;
 
function addressBook_receivedFromChild(selectedBox,tableName,checkedFields,checkedFieldsType)
{
    var SBNum=0;
    var s=selectedBox.split("related_tables"); 
    SBNum=s[1];
    
    if(SBNum>(addressBook_totalFields+1))
    {
        SBNum=(addressBook_totalFields===0)?0:addressBook_totalFields;
    }
    
    //alert('SBNum = '+SBNum);    
    //alert('length = '+checkedFields.length);  
    //alert('addressBook_totalFields = '+addressBook_totalFields);  
    var split0='',split1='',split2='';        
                
    jQuery('#addressBook_related_tables'+SBNum).val(tableName);
    jQuery('#addressBook_txtFieldName'+SBNum).val(checkedFields[0]);
    
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
    
    jQuery('#addressBook_data_types'+SBNum).val(split1);    
    jQuery('#addressBook_txtDataLength'+SBNum).val(split2[0]);
        
    if(jQuery('#addressBook_related_tables'+SBNum).val()!=='none')
    {
        jQuery('#addressBook_txtFieldName'+SBNum).attr('readonly','readonly');                            
        jQuery('#addressBook_data_types'+SBNum).attr('disabled','disabled');    
        jQuery('#addressBook_txtDataLength'+SBNum).attr('readonly','readonly');
    }     
    
    if(jQuery('#addressBook_data_types'+SBNum).val()==='text' || jQuery('#addressBook_data_types'+SBNum).val()==='tinytext' || jQuery('#addressBook_data_types'+SBNum).val()==='mediumtext' ||
               jQuery('#addressBook_data_types'+SBNum).val()==='longtext' || jQuery('#addressBook_data_types'+SBNum).val()==='year' ||
               jQuery('#addressBook_data_types'+SBNum).val()==='blob' || jQuery('#addressBook_data_types'+SBNum).val()==='tinyblob' ||
               jQuery('#addressBook_data_types'+SBNum).val()==='mediumblob' || jQuery('#addressBook_data_types'+SBNum).val()==='longblob')
    {
            jQuery('#addressBook_boolFieldRequired'+SBNum).attr('disabled','disabled');
            jQuery('#addressBook_txtDefaultValue'+SBNum).attr('disabled','disabled');
    }
    
    (jQuery('#addressBook_data_types'+SBNum).val()==='date' || 
     jQuery('#addressBook_data_types'+SBNum).val()==='datetime') ? jQuery('#addressBook_img_calendar'+SBNum).show() : jQuery('#addressBook_img_calendar'+SBNum).hide();
            
                        
    if(SBNum>=addressBook_totalFields)   
    {
        addressBook_totalFields++;
    }

    for(var i=addressBook_totalFields;i<(addressBook_totalFields+checkedFields.length-1);i++)
    {
        jQuery('#addressBook_related_tables'+i).val(tableName);
        jQuery('#addressBook_txtFieldName'+i).val(checkedFields[i-addressBook_totalFields+1]);
        
        if(checkedFieldsType[i-addressBook_totalFields+1].indexOf('(') !== -1)  // datatype contains '('  e.g. varchar(100)
        {
            split0 = checkedFieldsType[i-addressBook_totalFields+1].split("(");
            split1 = split0[0];
            split2 = split0[1].split(")");
        }
        else
        {
            split1 = checkedFieldsType[i-addressBook_totalFields+1];   
            split2 = '';
        }
        
        jQuery('#addressBook_data_types'+i).val(split1);    
        jQuery('#addressBook_txtDataLength'+i).val(split2[0]);
                
        if(jQuery('#addressBook_related_tables'+i).val()!=='none')
        {
            jQuery('#addressBook_txtFieldName'+i).attr('readonly','readonly');                            
            jQuery('#addressBook_data_types'+i).attr('disabled','disabled');    
            jQuery('#addressBook_txtDataLength'+i).attr('readonly','readonly');
        }     
        
        if(jQuery('#addressBook_data_types'+i).val()==='text' || jQuery('#addressBook_data_types'+i).val()==='tinytext' || jQuery('#addressBook_data_types'+i).val()==='mediumtext' ||
               jQuery('#addressBook_data_types'+i).val()==='longtext' || jQuery('#addressBook_data_types'+i).val()==='year' ||
               jQuery('#addressBook_data_types'+i).val()==='blob' || jQuery('#addressBook_data_types'+i).val()==='tinyblob' ||
               jQuery('#addressBook_data_types'+i).val()==='mediumblob' || jQuery('#addressBook_data_types'+i).val()==='longblob')
        {
                jQuery('#addressBook_boolFieldRequired'+i).attr('disabled','disabled');
                jQuery('#addressBook_txtDefaultValue'+i).attr('disabled','disabled');
        }
         
        if(jQuery('#addressBook_data_types'+i).val()==='date' || jQuery('#addressBook_data_types'+i).val()==='datetime')
        {            
                jQuery('#addressBook_img_calendar'+i).show();
        }    
                  
    }

    addressBook_totalFields=(SBNum>=addressBook_totalFields) ? (addressBook_totalFields+checkedFields.length-1) : (addressBook_totalFields+checkedFields.length);  
    addressBook_totalFields--;
    
}
  
function addressBook_unselectTable(selectedBox)
{    
    jQuery('#'+selectedBox).val('none');    
}
          
function addressBook_addRow(numRow,tableName,checkFieldName,checkFieldType,checkFieldSize)
{	          
        for(var i=0;i<numRow;i++)
        {
            jQuery('#addressBook_related_tables'+numRow).val(tableName);
            jQuery('#addressBook_txtFieldName'+numRow).val(checkFieldName);
            //jQuery('#txtFieldType'+numRow).val(checkFieldType);
            jQuery('#addressBook_txtDataLength'+numRow).val(checkFieldSize);
            jQuery('#addressBook_data_types'+numRow).val(checkFieldType);
            //jQuery('#addressBook_txtDefaultValue'+numRow).val(tableName);                                
        }
}

function addressBook_skip_none(array_name)
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

function addressBook_skip_blank(array_name)
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

function addressBook_validateStep1()
{
    if(!(jQuery('#addressBook_chk1to1').is(':checked')) && !(jQuery('#addressBook_chk1ton').is(':checked')))
    {
        alert("Please check the table AllCare_AddressBook1to1 or AllCare_AddressBook1ton");
        return false;
    }
        
    var relTables=document.getElementsByName("addressBook_related_tables");    
    //var fieldsEntered=document.getElementsByName("related_tables");    
    
    var flag=0;
    for(var j=0;j<relTables.length;j++)
    {
        if(relTables[j].value==='none' && jQuery('#addressBook_txtFieldName'+j).val()==='')
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
    
        
    var fieldName=document.getElementsByName("addressBook_txtFieldName"); 
    for(var k=0;k<fieldName.length;k++)
    {                        
        if(/\s/.test(jQuery('#addressBook_txtFieldName'+k).val()))
        {
            alert("Remove space in the Field name '"+jQuery('#addressBook_txtFieldName'+k).val()+"'");
            return false;           
        }        
    }
    
    return true;
    
}

function addressBook_validateStep2()
{
    if(!(jQuery('#addressBook_rd1to1').is(':checked')) && !(jQuery('#addressBook_rd1ton').is(':checked')))
    {
        alert("Please check the table AllCare_AddressBook1to1 or AllCare_AddressBook1ton");
        return false;
    }
    
    
//    else if(jQuery('#addressBook_txtGroupRecordset').val()==='')
//    {
//        alert("Please enter the Group/Recordset name");
//        return false;
//    }
//    
//    else if(!jQuery('#addressBook_comboFields').val())
//    {
//        alert("Please select the field/s");
//        return false;
//    }
        
    var checked_fields=document.getElementsByName("addressBook_chkAllFields");        
    var flag=0;
    for(var i=0;i<checked_fields.length;i++)
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
            
    return true;
    
}

function addressBook_checkIfFieldAlreadyExists()
{    
    var FieldName='';

    var chk1to1=(jQuery('#addressBook_chk1to1').is(':checked'))?1:0;
    var chk1ton=(jQuery('#addressBook_chk1ton').is(':checked'))?1:0;
    
    for(var i=0;i<20;i++)
    {            
        if(jQuery('#addressBook_txtFieldName'+i).val()!=='')
        {
            FieldName=jQuery('#addressBook_txtFieldName'+i).val();    
            var exists;
            $.ajax({
                type: 'POST',
                url: 'addressBook_check_if_field_already_exists.php',	
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

function addressBook_validateDataTypes()
{
    for(var i=0;i<20;i++)
    {                    
        if(jQuery('#addressBook_txtFieldName'+i).val()!=='' && jQuery('#addressBook_data_types'+i).val()!=='')
        {
            // datalength is NOT mandatory for the following data types , 
            // eg. if the data type is 'date' then don't validate its empty datalength.
            //     else if data type is 'int' then validate its empty datalength
            if(jQuery('#addressBook_data_types'+i).val()!=='date' && jQuery('#addressBook_data_types'+i).val()!=='datetime' &&
               jQuery('#addressBook_data_types'+i).val()!=='time' && jQuery('#addressBook_data_types'+i).val()!=='timestamp' &&
               jQuery('#addressBook_data_types'+i).val()!=='text' && jQuery('#addressBook_data_types'+i).val()!=='tinytext' && 
               jQuery('#addressBook_data_types'+i).val()!=='mediumtext' && jQuery('#addressBook_data_types'+i).val()!=='longtext' && 
               jQuery('#addressBook_data_types'+i).val()!=='blob' && jQuery('#addressBook_data_types'+i).val()!=='tinyblob' &&
               jQuery('#addressBook_data_types'+i).val()!=='mediumblob' && jQuery('#addressBook_data_types'+i).val()!=='longblob' &&                        
               jQuery('#addressBook_data_types'+i).val()!=='geometry' && jQuery('#addressBook_data_types'+i).val()!=='point' &&
               jQuery('#addressBook_data_types'+i).val()!=='linestring' && jQuery('#addressBook_data_types'+i).val()!=='polygon' &&  
               jQuery('#addressBook_data_types'+i).val()!=='multipoint' && jQuery('#addressBook_data_types'+i).val()!=='multilinestring' &&    
               jQuery('#addressBook_data_types'+i).val()!=='multipolygon' && jQuery('#addressBook_data_types'+i).val()!=='geometrycollection')
            {
                if(jQuery('#addressBook_txtDataLength'+i).val()==='') //  isNaN(jQuery('#addressBook_txtDataLength'+i).val())
                {
                    alert('Please enter a valid numerical Data Length for the field '+jQuery('#addressBook_txtFieldName'+i).val());
                    jQuery('#addressBook_txtDataLength'+i).focus();
                    return false;                            
                }
            }
        }                        
    }
    return true;
}

function addressBook_validateDefaultValues()
{        
    for(var i=0;i<20;i++)
    {            
        if(jQuery('#addressBook_txtFieldName'+i).val()!=='')
        {
            var FieldName=jQuery('#addressBook_txtFieldName'+i).val();               
            var dataType=jQuery('#addressBook_data_types'+i).val();
            var defaultValue=jQuery('#addressBook_txtDefaultValue'+i).val();
            
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
            }
            
            if(jQuery('#addressBook_boolFieldRequired'+i).is(':checked') && defaultValue===''
                && (jQuery('#addressBook_data_types'+i).val()!=='text' && jQuery('#addressBook_data_types'+i).val()!=='tinytext' && 
                    jQuery('#addressBook_data_types'+i).val()!=='mediumtext' && jQuery('#addressBook_data_types'+i).val()!=='longtext' &&
                    jQuery('#addressBook_data_types'+i).val()!=='blob' && jQuery('#addressBook_data_types'+i).val()!=='tinyblob' &&
                    jQuery('#addressBook_data_types'+i).val()!=='mediumblob' && jQuery('#addressBook_data_types'+i).val()!=='longblob'))                                        
            {
                alert('Please enter the Default value for the field '+FieldName+' checked as Required');
                return false;
            }                                    
        }
    }
    return true;
}

function addressBook_checkDataType()
{
    for(var i=0;i<20;i++)
    { 
        if(jQuery('#addressBook_data_types'+i).val()==='text' || jQuery('#addressBook_data_types'+i).val()==='tinytext' || 
           jQuery('#addressBook_data_types'+i).val()==='mediumtext' || jQuery('#addressBook_data_types'+i).val()==='longtext' || 
           jQuery('#addressBook_data_types'+i).val()==='blob' || jQuery('#addressBook_data_types'+i).val()==='tinyblob' ||
           jQuery('#addressBook_data_types'+i).val()==='mediumblob' || jQuery('#addressBook_data_types'+i).val()==='longblob')
        {
                jQuery('#addressBook_boolFieldRequired'+i).attr('disabled','disabled');
                jQuery('#addressBook_txtDefaultValue'+i).attr('disabled','disabled');
        }
        if(jQuery('#addressBook_data_types'+i).val()==='date' || jQuery('#addressBook_data_types'+i).val()==='datetime')
        {
            jQuery('#addressBook_img_calendar'+i).show(); 
            jQuery('#addressBook_txtDataLength'+i).attr('readonly','readonly');
        }
    }
}

function addressBook_insertFields()
{
        var finalURL='addressBook_add_new_fields.php';  
        
        var chk1to1=(jQuery('#addressBook_chk1to1').is(':checked'))?1:0;
        var chk1ton=(jQuery('#addressBook_chk1ton').is(':checked'))?1:0;
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
            relatedTables[i]=jQuery('#addressBook_related_tables'+i).val();
            //relatedTables.push(jQuery('#addressBook_related_tables'+i).val());
            FieldName[i]=jQuery('#addressBook_txtFieldName'+i).val();                                    
            FieldType[i]=jQuery('#addressBook_data_types'+i).val();
            DataLength[i]=jQuery('#addressBook_txtDataLength'+i).val();
            FieldRequired[i]=(jQuery('#addressBook_boolFieldRequired'+i).is(':checked'))?1:0;
            DefaultValue[i]=jQuery('#addressBook_txtDefaultValue'+i).val();            
            FieldViewEdit[i]=jQuery('#addressBook_view_edit'+i).val();                                    	            
        }                       
                
        relatedTables=addressBook_skip_none(relatedTables);                
        relatedTables=relatedTables.toString();
        
        FieldName=addressBook_skip_blank(FieldName);
        FieldName=FieldName.toString();
        
        FieldType=addressBook_skip_none(FieldType);
        FieldType=FieldType.toString();
        
        for(var i=0;i<20;i++)
        {
            if(DataLength[i]==='')
            {
               DataLength[i]=0;         
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
                     jQuery("#addressBook_tbl_grid").hide();     
                     jQuery("#addressBook_tbl_AllCare").hide();  
                     
                     jQuery("#addressBook_btnSaveFields").hide();                     
                     jQuery("#addressBook_btnClear").hide();  
                     jQuery("#addressBook_btn_Cancel").hide();  
                     jQuery("#addressBook_tbl_step2").show(); 
                     jQuery("#addressBook_btnNext").hide();
                     jQuery("#addressBook_btnBack").show();
                     
		},
		failure: function(response)
		{
			alert("error");
		}		
        });	       
}

function addressBook_showGroupsRecordsets()
{
    var finalURL='addressBook_show_groups_recordsets.php';  
    var table_name=jQuery("#addressBook_rd1to1").is(":checked")?'tbl_allcare_addressbook1to1_fieldmapping':'tbl_allcare_addressbook1ton_fieldmapping';    
           
    $.ajax({
                type: 'POST',
                url: finalURL,	
                data: {table_name:table_name},

                //data:relatedTables,
                success: function(response)
                {                         
                     jQuery("#addressBook_showGroupsRecordsets").html(response);                     
                },
                failure: function(response)
                {
                        alert("error");
                }		
            });	
}

function addressBook_showFields()
{                         
        var finalURL='addressBook_show_fields.php';  
        var table_name=jQuery("#addressBook_rd1to1").is(":checked")?'tbl_allcare_addressbook1to1':'tbl_allcare_addressbook1ton';
                
        $.ajax({
                    type: 'POST',
                    url: finalURL,	
                    data: {table_name:table_name},

                    //data:relatedTables,
                    success: function(response)
                    {                         
                         jQuery("#addressBook_showFields").html(response);                     
                    },
                    failure: function(response)
                    {
                            alert("error");
                    }		
               });		
}

function addressBook_hideNewButtonData()
{
    jQuery('#addressBook_lblGroup').hide();  
    jQuery('#addressBook_lblRecordset').hide();  
    jQuery('#addressBook_lblExistingFields').hide();  
    jQuery('#addressBook_txtGroupRecordset').val('');  
    jQuery('#addressBook_txtGroupRecordset').hide();      
    jQuery('#addressBook_lblFieldsSelected').hide();   
    jQuery('#addressBook_showFieldsByGroupRecordsets').html(''); 
    jQuery('#addressBook_showFields').html(''); 
}

function addressBook_showFieldsByGroupRecordsets(tableName)
{
        var finalURL='addressBook_show_fields_by_groups_recordsets.php';  
        //var table_name=jQuery("#addressBook_rd1to1").is(":checked")?'tbl_allcare_addressbook1to1_fieldmapping':'tbl_allcare_addressbook1ton_fieldmapping';
        var table_name=tableName;
        var selectedGR=jQuery("#addressBook_comboGroupsRecordsets").val();        
        
        $.ajax({
                    type: 'POST',
                    url: finalURL,	
                    data: {table_name:table_name,selectedGR:selectedGR},

                    //data:relatedTables,
                    success: function(response)
                    {                         
                         jQuery("#addressBook_showFieldsByGroupRecordsets").html(response);                     
                    },
                    failure: function(response)
                    {
                            alert("error");
                    }		
               });		
}

function addressBook_editGroupRecordset()   
{               
        var finalURL='addressBook_edit_group_recordset.php';
        
        var TableName=(jQuery('#addressBook_rd1to1').is(':checked'))?'tbl_allcare_addressbook1to1_fieldmapping':'tbl_allcare_addressbook1ton_fieldmapping';    
        
        var GroupRecordsetName=jQuery('#addressBook_comboGroupsRecordsets').val();
        
        var selectedFields=new Array();   
        
        var showYesNo = jQuery('#rd_show_yes').is(':checked') ? 'Y' : 'N';
        
        var checked_fields=document.getElementsByName("addressBook_chkSelectedFields");        

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
            data: {TableName:TableName,GroupRecordsetName:GroupRecordsetName,
                   selectedFields:selectedFields,showYesNo:showYesNo},

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

function addressBook_insert_in_mapping()
{
    var finalURL='addressBook_insert_in_mapping.php';                  
    
var mappingTableName=(jQuery('#addressBook_rd1to1').is(':checked'))?'tbl_allcare_addressbook1to1_fieldmapping':'tbl_allcare_addressbook1ton_fieldmapping';    
    
    
    var showYesNo = jQuery('#rd_show_yes').is(':checked') ? 'Y' : 'N';
    
    var groupRecordsetName=jQuery('#addressBook_txtGroupRecordset').val();

    //var selectedFields=jQuery('#comboFields').val();
    var selectedFields=new Array();
    
    var checked_fields=document.getElementsByName("addressBook_chkAllFields");            

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
            data: {mappingTableName:mappingTableName,groupRecordsetName:groupRecordsetName,
                   selectedFields:selectedFields,showYesNo:showYesNo},

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

function addressBook_checkAll(checkboxstatus)
{            
    var delList=document.getElementsByName("addressBook_checkClear");        

    for(var i=0;i<delList.length;i++)
    {
        delList[i].checked = checkboxstatus;//(chkBtn.checked)? true : false;            
    }        
}

/*
function addressBook_clearSelected()
{    
    var delList=document.getElementsByName("addressBook_checkClear"); 
    var checkedNum=0;
    var allFieldsClear=0;
    
            
    for(var i=(delList.length-1);i>=0;i--)
    {
        if(delList[i].checked===true)
        {
            checkedNum++;        
            
            jQuery('#addressBook_related_tables'+i).val('none');
            jQuery('#addressBook_txtFieldName'+i).val('');
            jQuery('#addressBook_data_types'+i).val('none');
            jQuery('#addressBook_txtDataLength'+i).val('');
            
            jQuery('#addressBook_boolFieldRequired'+i).attr('checked', false);
            jQuery('#addressBook_boolFieldRequired'+i).attr('disabled','');                                                       
            jQuery('#addressBook_txtDefaultValue'+i).val('');
            
            if(jQuery('#addressBook_img_calendar'+i).css('display')==='inline' || jQuery('#addressBook_img_calendar'+i).css('display')==='block')
            {
                jQuery('#addressBook_img_calendar'+i).hide();
            }
            
            jQuery('#addressBook_view_edit'+i).val('Y');            
            delList[i].checked=false;
            
            for(var j=i;j<(addressBook_totalFields);j++)
            {
                jQuery('#addressBook_related_tables'+j).val(jQuery('#addressBook_related_tables'+(j+1)).val());
                jQuery('#addressBook_txtFieldName'+j).val(jQuery('#addressBook_txtFieldName'+(j+1)).val());
                jQuery('#addressBook_data_types'+j).val(jQuery('#addressBook_data_types'+(j+1)).val());
                jQuery('#addressBook_txtDataLength'+j).val(jQuery('#addressBook_txtDataLength'+(j+1)).val());                
                
                var fn = jQuery('#addressBook_txtFieldName'+(j+1)).attr('readonly') ? 'readonly' :'';
                jQuery('#addressBook_txtFieldName'+j).attr('readonly',fn);                            
                
                var dt = jQuery('#addressBook_data_types'+(j+1)).attr('disabled') ? 'disabled' :'';
                jQuery('#addressBook_data_types'+j).attr('disabled',dt);                          
                                
                var dl = jQuery('#addressBook_txtDataLength'+(j+1)).attr('readonly') ? 'readonly' : '';
                jQuery('#addressBook_txtDataLength'+j).attr('readonly',dl);                                                       
                
                var fr = jQuery('#addressBook_boolFieldRequired'+(j+1)).is(':checked') ? true : false;
                jQuery('#addressBook_boolFieldRequired'+j).attr('checked', fr);
                                
                jQuery('#addressBook_txtDefaultValue'+j).val(jQuery('#addressBook_txtDefaultValue'+(j+1)).val());
                jQuery('#addressBook_view_edit'+j).val(jQuery('#addressBook_view_edit'+(j+1)).val());                     
                                
                var frReq = jQuery('#addressBook_boolFieldRequired'+(j+1)).attr('disabled') ? 'disabled' : '';
                jQuery('#addressBook_boolFieldRequired'+j).attr('disabled',frReq);                                                       
                
                var dv = jQuery('#addressBook_txtDefaultValue'+(j+1)).attr('disabled') ? 'disabled' : '';
                jQuery('#addressBook_txtDefaultValue'+j).attr('disabled', dv);
                
                var imgCal = (jQuery('#addressBook_img_calendar'+(j+1)).css('display')==='inline' || jQuery('#addressBook_img_calendar'+(j+1)).css('display')==='block') ? 'inline' : 'none';
                
                jQuery('#addressBook_img_calendar'+j).css('display', imgCal);                
                
                                                
                jQuery('#addressBook_related_tables'+(j+1)).val('none');
                jQuery('#addressBook_txtFieldName'+(j+1)).val('');
                jQuery('#addressBook_data_types'+(j+1)).val('none');
                jQuery('#addressBook_txtDataLength'+(j+1)).val('');            

                jQuery('#addressBook_txtFieldName'+(j+1)).attr('readonly','');                            
                jQuery('#addressBook_data_types'+(j+1)).attr('disabled','');    
                jQuery('#addressBook_txtDataLength'+(j+1)).attr('readonly','');

                jQuery('#addressBook_boolFieldRequired'+(j+1)).attr('checked', false);            
                jQuery('#addressBook_txtDefaultValue'+(j+1)).val('');
                jQuery('#addressBook_img_calendar'+(j+1)).hide();             
                jQuery('#addressBook_boolFieldRequired'+(j+1)).attr('disabled','');    
                jQuery('#addressBook_txtDefaultValue'+(j+1)).attr('disabled','');    
                
                jQuery('#addressBook_view_edit'+(j+1)).val('Y');                                      
            } 
            
            jQuery('#addressBook_related_tables'+j).val('none');
            jQuery('#addressBook_txtFieldName'+j).val('');
            jQuery('#addressBook_data_types'+j).val('none');
            jQuery('#addressBook_txtDataLength'+j).val('');            

            jQuery('#addressBook_txtFieldName'+j).attr('readonly','');                            
            jQuery('#addressBook_data_types'+j).attr('disabled','');    
            jQuery('#addressBook_txtDataLength'+j).attr('readonly','');

            jQuery('#addressBook_boolFieldRequired'+j).attr('checked', false);       
            jQuery('#addressBook_boolFieldRequired'+j).attr('disabled','');        
            jQuery('#addressBook_txtDefaultValue'+j).val('');
            jQuery('#addressBook_img_calendar'+j).hide();          
            jQuery('#addressBook_view_edit'+j).val('Y');                
                      
            if(i===0)
            {allFieldsClear=1;}
                      
        }
        
        jQuery('#addressBook_check_All').attr('checked', false);   
    }  
    
    //addressBook_totalFields=0;
    
    addressBook_totalFields = (allFieldsClear===1) ? 0 : Math.abs(addressBook_totalFields-checkedNum);
        
    //alert('now tf= '+addressBook_totalFields);
}

*/


function addressBook_clearSelected()
{
    var delList=document.getElementsByName("addressBook_checkClear"); 
    var checkedNum=0;
    var allFieldsClear=0;
         
    for(var i=(delList.length-1);i>=0;i--)
    {
        if(delList[i].checked===true)
        {
            checkedNum++;        
            
            jQuery('#addressBook_related_tables'+i).val('none');
            jQuery('#addressBook_txtFieldName'+i).val('');
            jQuery('#addressBook_data_types'+i).val('none');
            jQuery('#addressBook_txtDataLength'+i).val('');
            /*
            jQuery('#addressBook_txtFieldName'+i).attr('readonly','');                            
            jQuery('#addressBook_data_types'+i).attr('disabled','');    
            jQuery('#addressBook_txtDataLength'+i).attr('readonly','');
            */
            jQuery('#addressBook_boolFieldRequired'+i).attr('checked', false);            
            jQuery('#addressBook_txtDefaultValue'+i).val('');
            
            if(jQuery('#addressBook_img_calendar'+i).css('display')==='inline' || jQuery('#addressBook_img_calendar'+i).css('display')==='block')
            {
                jQuery('#addressBook_img_calendar'+i).hide();
            }
            
            jQuery('#addressBook_view_edit'+i).val('Y');            
            delList[i].checked=false;
            
            for(var j=i;j<(addressBook_totalFields);j++)
            {
                jQuery('#addressBook_related_tables'+j).val(jQuery('#addressBook_related_tables'+(j+1)).val());
                jQuery('#addressBook_txtFieldName'+j).val(jQuery('#addressBook_txtFieldName'+(j+1)).val());
                jQuery('#addressBook_data_types'+j).val(jQuery('#addressBook_data_types'+(j+1)).val());
                jQuery('#addressBook_txtDataLength'+j).val(jQuery('#addressBook_txtDataLength'+(j+1)).val());                
                
                var fn = jQuery('#addressBook_txtFieldName'+(j+1)).attr('readonly') ? 'readonly' :'';
                jQuery('#addressBook_txtFieldName'+j).attr('readonly',fn);                            
                
                var dt = jQuery('#addressBook_data_types'+(j+1)).attr('disabled') ? 'disabled' :'';
                jQuery('#addressBook_data_types'+j).attr('disabled',dt);                          
                                
                var dl = jQuery('#addressBook_txtDataLength'+(j+1)).attr('readonly') ? 'readonly' : '';
                jQuery('#addressBook_txtDataLength'+j).attr('readonly',dl);                                                       
                
                var fr = jQuery('#addressBook_boolFieldRequired'+(j+1)).is(':checked') ? true : false;
                jQuery('#addressBook_boolFieldRequired'+j).attr('checked', fr);
                                
                jQuery('#addressBook_txtDefaultValue'+j).val(jQuery('#addressBook_txtDefaultValue'+(j+1)).val());
                jQuery('#addressBook_view_edit'+j).val(jQuery('#addressBook_view_edit'+(j+1)).val());                                   
                                
                var frReq = jQuery('#addressBook_boolFieldRequired'+(j+1)).attr('disabled') ? 'disabled' : '';
                jQuery('#addressBook_boolFieldRequired'+j).attr('disabled',frReq);                                                       
                
                var dv = jQuery('#addressBook_txtDefaultValue'+(j+1)).attr('disabled') ? 'disabled' : '';
                jQuery('#addressBook_txtDefaultValue'+j).attr('disabled', dv);
                
                var imgCal = (jQuery('#addressBook_img_calendar'+(j+1)).css('display')==='inline' || jQuery('#addressBook_img_calendar'+(j+1)).css('display')==='block') ? 'inline' : 'none';
                
                jQuery('#addressBook_img_calendar'+j).css('display', imgCal);                
                                                
                jQuery('#addressBook_related_tables'+(j+1)).val('none');
                jQuery('#addressBook_txtFieldName'+(j+1)).val('');
                jQuery('#addressBook_data_types'+(j+1)).val('none');
                jQuery('#addressBook_txtDataLength'+(j+1)).val('');            

                jQuery('#addressBook_txtFieldName'+(j+1)).attr('readonly','');                            
                jQuery('#addressBook_data_types'+(j+1)).attr('disabled','');    
                jQuery('#addressBook_txtDataLength'+(j+1)).attr('readonly','');

                jQuery('#addressBook_boolFieldRequired'+(j+1)).attr('checked', false);            
                jQuery('#addressBook_txtDefaultValue'+(j+1)).val('');
                jQuery('#addressBook_img_calendar'+(j+1)).hide();             
                jQuery('#addressBook_boolFieldRequired'+(j+1)).attr('disabled','');    
                jQuery('#addressBook_txtDefaultValue'+(j+1)).attr('disabled','');    
                
                jQuery('#addressBook_view_edit'+(j+1)).val('Y');                                      
            }
            
            jQuery('#addressBook_related_tables'+j).val('none');
            jQuery('#addressBook_txtFieldName'+j).val('');
            jQuery('#addressBook_data_types'+j).val('none');
            jQuery('#addressBook_txtDataLength'+j).val('');            

            jQuery('#addressBook_txtFieldName'+j).attr('readonly','');                            
            jQuery('#addressBook_data_types'+j).attr('disabled','');    
            jQuery('#addressBook_txtDataLength'+j).attr('readonly','');

            jQuery('#addressBook_boolFieldRequired'+j).attr('checked', false);            
            jQuery('#addressBook_txtDefaultValue'+j).val('');
            jQuery('#addressBook_img_calendar'+j).hide();          
            jQuery('#addressBook_view_edit'+j).val('Y');                
                      
            //if(i===0)
            if(i===0 && addressBook_totalFields===0)
            {allFieldsClear=1;}
                      
        }
        
        jQuery('#addressBook_check_All').attr('checked', false);   
    }
    
    //addressBook_totalFields=0;
    
    addressBook_totalFields = (allFieldsClear===1 || checkedAll===true) ? 0 : Math.abs(addressBook_totalFields-checkedNum);
    checkedAll=false;
    //alert('now tf= '+addressBook_totalFields);
}


</script>
