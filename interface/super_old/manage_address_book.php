<!--
<style type="text/css">@import url(../../library/dynarch_calendar.css);</style>
<script type="text/javascript" src="../../library/dialog.js"></script>
<script type="text/javascript" src="../../library/textformat.js"></script>
<script type="text/javascript" src="../../library/dynarch_calendar.js"></script>
<script type="text/javascript" src="../../library/dynarch_calendar_en.js"></script>
<script type="text/javascript" src="../../library/dynarch_calendar_setup.js"></script>
-->
<?php

      echo "<table id='addressbook_tbl_AllCare' name='addressbook_tbl_AllCare'>
                <tr title='1 to 1' border='1'>
                    <td valign='top' colspan='2'><b>AllCare_AddressBook1to1 </b></td>
                    <td valign='top'><input id='addressbook_chk1to1' type='checkbox' value='1' name='addressbook_chk1to1'></td>
                </tr>
                <tr title='1 to n' border='1'>
                    <td valign='top' colspan='2'><b>AllCare_AddressBook1ton </b></td>
                    <td valign='top'><input id='addressbook_chk1ton' type='checkbox' value='1' name='addressbook_chk1ton'></td>
                </tr>
            </table>
            <input type='button' id='addressbook_btnNext' name='addressbook_btnNext' value='Next' style='float: right; margin-top: -35px;'
                   onclick='javascript: jQuery(\"#addressbook_tbl_AllCare\").hide();
                                        jQuery(\"#addressbook_tbl_grid\").hide();
                                        jQuery(\"#addressbook_tbl_step2\").show();
                                        jQuery(\"#addressbook_btnNext\").hide();
                                        jQuery(\"#addressbook_btnBack\").show();
                                        jQuery(\"#addressbook_btnSaveFields\").hide();                     
                                        jQuery(\"#addressbook_btnClear\").hide();  
                                        jQuery(\"#addressbook_btn_Cancel\").hide();  ' />
            <input type='button' id='addressbook_btnBack' name='addressbook_btnBack' value='Back' style='display:none' 
                   onclick='javascript: jQuery(\"#addressbook_tbl_AllCare\").show();
                                        jQuery(\"#addressbook_tbl_grid\").show();
                                        jQuery(\"#addressbook_tbl_step2\").hide();
                                        jQuery(\"#addressbook_btnBack\").hide();
                                        jQuery(\"#addressbook_btnNext\").show();
                                        jQuery(\"#addressbook_btnSaveFields\").show();                     
                                        jQuery(\"#addressbook_btnClear\").show();  
                                        jQuery(\"#addressbook_btn_Cancel\").show();' />
            <br>
            <table border='1' id='addressbook_tbl_grid' name='addressbook_tbl_grid'>                
                <tr>
                    <th><input type='checkbox' id='addressbook_check_All' name='addressbook_check_All' onclick='addressbook_checkAll(this.checked);' /></th>
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
                    <td align='center'><input type='checkbox' id='addressbook_checkClear".$cnt."' name='addressbook_checkClear' /></td>";
                                           
      echo "<td><select id='addressbook_related_tables".$cnt."' name='addressbook_related_tables' onchange='javascript:return addressbook_newEvt(this.id);' style='width:125px'>          
            <option value='none'>none</option>
                $table_list
            </select></td>
            <td><input type='text' id='addressbook_txtFieldName".$cnt."' name='addressbook_txtFieldName' style='width:125px' /></td>";

            //echo "<td><input type='text' id='addressbook_txtFieldType".$cnt."' name='addressbook_txtFieldType[]' style='width:125px' /></td>";
            
            echo "<td>
                    <select id='addressbook_data_types".$cnt."' name='addressbook_data_types' onchange='addressbook_checkDataType();'>
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
                                              
            echo "<td><input type='text' id='addressbook_txtDataLength".$cnt."' name='addressbook_txtDataLength' style='width:125px' /></td>
            <td align='center'>
                <input type='checkbox' id='addressbook_boolFieldRequired".$cnt."' name='addressbook_boolFieldRequired' />
            </td>
            <td>
                <!--<input type='text' id='addressbook_txtDefaultValue".$cnt."' name='addressbook_txtDefaultValue' style='width:125px' />-->
                <input type='text' size='10' name='addressbook_txtDefaultValue' id='addressbook_txtDefaultValue".$cnt."' onkeyup='datekeyup(this,mypcc)' onblur='dateblur(this,mypcc)' title='yyyy-mm-dd last date of this event' />
<img src='../../interface/pic/show_calendar.gif' align='absbottom' width='24' height='22'
id='addressbook_img_calendar".$cnt."' border='0' alt='[?]' style='cursor:pointer;display:none;'
title='Click here to choose a date'>
<script>
Calendar.setup({inputField:'addressbook_txtDefaultValue".$cnt."', ifFormat:'%Y-%m-%d', button:'addressbook_img_calendar".$cnt."'});
</script>
            
            </td>
                                       
            <td>
                <select id='addressbook_view_edit".$cnt."' name='addressbook_view_edit' style='width:100px'>
                    <option value='Y'>Edit</option>
                    <option value='N'>View</option>                    
                </select>
            </td>
        </tr>";
      } 
      echo "</table>";                 
        
?>

    <table id="addressbook_tbl_step2" name="addressbook_tbl_step2" style="display:none;">
        
        <tr><td></td></tr>
        <tr> <td></td>               
            <td align="left"><input type="radio" id="addressbook_rd1to1" name="addressbook_radallcare" value="1to1"
                       onchange="javascript:jQuery('#addressbook_lblExistingRecordsets').hide();
                                            jQuery('#addressbook_lblExistingGroups').show();  
                                            addressbook_showGroupsRecordsets();
                                            addressbook_hideNewButtonData();
                                            jQuery('#td_Show_YesNo').hide();
                                            ">
            AllCare_AddressBook1to1</td>
        </tr>
        <tr>   <td></td>             
            <td align="left">
                <input type="radio" id="addressbook_rd1ton" name="addressbook_radallcare" value="1ton"
                       onchange="javascript:jQuery('#addressbook_lblExistingRecordsets').show();
                                            jQuery('#addressbook_lblExistingGroups').hide();  
                                            addressbook_showGroupsRecordsets();
                                            addressbook_hideNewButtonData();      
                                            jQuery('#td_Show_YesNo').hide();
                                            " />
            AllCare_AddressBook1ton</td>
        </tr>  
        <tr><td></td></tr>
        <tr>
            <td style="width:150px"><label id='addressbook_lblExistingGroups' style='display:none;'>Existing Groups </label>
                <label id='addressbook_lblExistingRecordsets' style='display:none;'>Existing Recordsets</label>
            </td>                                
            <td>                    
                <div id="addressbook_showGroupsRecordsets" name="addressbook_showGroupsRecordsets"></div>
            </td>
        </tr>
        <tr>
            <td>
                <label id='addressbook_lblFieldsSelected' style='display:none;'>Field Selected : </label>
            </td>
            <td>
                <div id="addressbook_showFieldsByGroupRecordsets" name="addressbook_showFieldsByGroupRecordsets"></div>
            </td>
        </tr>
        <tr>
            <td><label id='addressbook_lblGroup' style='display:none;'>Group Name</label>
                <label id='addressbook_lblRecordset' style='display:none;'>Recordset</label>
            </td>
            <td>
                <input type="text" id='addressbook_txtGroupRecordset' name="addressbook_txtGroupRecordset" value="" style='display:none;'>
            </td>
        </tr>
        <tr>     
            <td><label id='addressbook_lblExistingFields' style='display:none;'>Fields : </label></td>           
            <td>
                <div id="addressbook_showFields" name="addressbook_showFields" style='border:1px'>               
                </div> 
            </td>
        </tr>
        <tr><td></td>
            <td><div id="td_Show_YesNo" style="display:none;"><label id='addressbook_lblShowYesNo'>Show this Group/Recordset</label>      
                    <input type='radio' id='rd_show_yes' name='rd_show_yes_no' value='Y' checked /><label for='rd_show_yes'>Yes</label>
                    <input type='radio' id='rd_show_no' name='rd_show_yes_no' value='N' /><label for='rd_show_no'>No</label>
                </div>
            </td>
        </tr>

        <tr>
            <td colspan="2" align="center">      
    <!--            <input type='submit' id='addressbook_btnSave' name='addressbook_btnSave' value='Save' />-->
                <input type='button' id='addressbook_btnSave' name='addressbook_btnSave' value='Save' 
                       onclick='javascript: if(jQuery("#addressbook_comboGroupsRecordsets").val()!=="none")    
                                            {
                                                if(addressbook_validateStep2(0))
                                                {
                                                    addressbook_editGroupRecordset();
                                                }
                                            }
                                            if(jQuery("#addressbook_txtGroupRecordset").val()!=="")
                                            {
                                                if(addressbook_validateStep2(1))
                                                {
                                                    addressbook_insert_in_mapping();
                                                }
                                            }
                                            if(jQuery("#addressbook_comboGroupsRecordsets").val()==="none" && jQuery("#addressbook_txtGroupRecordset").val()==="")
                                            {
                                                alert("Enter the Group/Recordset name");
                                                
                                            }
                                            //jQuery("#td_Show_YesNo").hide();
                                            //jQuery("input:radio[name=rd_show_yes_no]").prop("checked",false); 
                                        ' />
                <input type='button' id='addressbook_btnCancel' name='addressbook_btnCancel' value='Cancel'
                       onclick='javascript:location.href="edit_globals.php";'/>
            </td>
        </tr>
     </table>

    <br>
        <center>                   
            <input type='button' id='addressbook_btnSaveFields' name='addressbook_btnSaveFields' value='Save Fields' 
                   onclick='javascript:if(addressbook_validateStep1())
                                       {
                                            if(!addressbook_checkIfFieldAlreadyExists() && addressbook_validateDataTypes() && addressbook_validateDefaultValues())
                                            {   
                                                addressbook_insertFields();
                                            }                                            
                                       }' />                                
            <input type='button' id='addressbook_btnClear' name='addressbook_btnClear' value='Clear' onclick='javascript:addressbook_clearSelected();' />
            <input type='button' id='addressbook_btn_Cancel' name='addressbook_btn_Cancel' value='Cancel' 
                   onclick='javascript:location.href="edit_globals.php";' />
        </center>
    

<script type='text/javascript'>
    
function addressbook_newEvt(selectedBox)
{
     if(document.getElementById(selectedBox).value!=='none')
     {
  dlgopen('all_extensions/get_table_fields.php?table_name='+document.getElementById(selectedBox).value+'&selectedBox='+selectedBox+'&extension_name=addressbook',
   '_blank', 550, 270);
     }
  return false;
}

var addressbook_totalFields=0;
 
function addressbook_receivedFromChild(selectedBox,tableName,checkedFields,checkedFieldsType)
{
    var SBNum=0;
    var s=selectedBox.split("addressbook_related_tables"); 
    SBNum=s[1];
    /*
    if(SBNum>(addressbook_totalFields+1))
    {
        SBNum=(addressbook_totalFields===0)?0:addressbook_totalFields;
    }
    */
    //alert('SBNum = '+SBNum);    
    //alert('length = '+checkedFields.length);  
    //alert('addressbook_totalFields = '+addressbook_totalFields);  
    var split0='',split1='',split2='';        
                
    jQuery('#addressbook_related_tables'+SBNum).val(tableName);
    jQuery('#addressbook_txtFieldName'+SBNum).val(checkedFields[0]);
    
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
    
    jQuery('#addressbook_data_types'+SBNum).val(split1);    
    jQuery('#addressbook_txtDataLength'+SBNum).val(split2[0]);
        
    if(jQuery('#addressbook_related_tables'+SBNum).val()!=='none')
    {
        jQuery('#addressbook_txtFieldName'+SBNum).attr('readonly','readonly');                            
        jQuery('#addressbook_data_types'+SBNum).attr('disabled','disabled');    
        jQuery('#addressbook_txtDataLength'+SBNum).attr('readonly','readonly');
    }     

    if(jQuery('#addressbook_data_types'+SBNum).val()==='text' || jQuery('#addressbook_data_types'+SBNum).val()==='tinytext' || jQuery('#addressbook_data_types'+SBNum).val()==='mediumtext' ||
               jQuery('#addressbook_data_types'+SBNum).val()==='longtext' || jQuery('#addressbook_data_types'+SBNum).val()==='year' ||
               jQuery('#addressbook_data_types'+SBNum).val()==='blob' || jQuery('#addressbook_data_types'+SBNum).val()==='tinyblob' ||
               jQuery('#addressbook_data_types'+SBNum).val()==='mediumblob' || jQuery('#addressbook_data_types'+SBNum).val()==='longblob')
    {
            jQuery('#addressbook_boolFieldRequired'+SBNum).attr('disabled','disabled');
            jQuery('#addressbook_txtDefaultValue'+SBNum).attr('disabled','disabled');
    }
    
    (jQuery('#addressbook_data_types'+SBNum).val()==='date' || 
     jQuery('#addressbook_data_types'+SBNum).val()==='datetime') ? jQuery('#addressbook_img_calendar'+SBNum).show() : jQuery('#addressbook_img_calendar'+SBNum).hide();
            
                        
    if(SBNum>=addressbook_totalFields)   
    {
        addressbook_totalFields++;
    }

    for(var i=addressbook_totalFields;i<(addressbook_totalFields+checkedFields.length-1);i++)
    {
        jQuery('#addressbook_related_tables'+i).val(tableName);
        jQuery('#addressbook_txtFieldName'+i).val(checkedFields[i-addressbook_totalFields+1]);
        
        if(checkedFieldsType[i-addressbook_totalFields+1].indexOf('(') !== -1)  // datatype contains '('  e.g. varchar(100)
        {
            split0 = checkedFieldsType[i-addressbook_totalFields+1].split("(");
            split1 = split0[0];
            split2 = split0[1].split(")");
        }
        else
        {
            split1 = checkedFieldsType[i-addressbook_totalFields+1];   
            split2 = '';
        }
        
        jQuery('#addressbook_data_types'+i).val(split1);    
        jQuery('#addressbook_txtDataLength'+i).val(split2[0]);
                
        if(jQuery('#addressbook_related_tables'+i).val()!=='none')
        {
            jQuery('#addressbook_txtFieldName'+i).attr('readonly','readonly');                            
            jQuery('#addressbook_data_types'+i).attr('disabled','disabled');    
            jQuery('#addressbook_txtDataLength'+i).attr('readonly','readonly');
        }     
        
        if(jQuery('#addressbook_data_types'+i).val()==='text' || jQuery('#addressbook_data_types'+i).val()==='tinytext' || jQuery('#addressbook_data_types'+i).val()==='mediumtext' ||
               jQuery('#addressbook_data_types'+i).val()==='longtext' || jQuery('#addressbook_data_types'+i).val()==='year' ||
               jQuery('#addressbook_data_types'+i).val()==='blob' || jQuery('#addressbook_data_types'+i).val()==='tinyblob' ||
               jQuery('#addressbook_data_types'+i).val()==='mediumblob' || jQuery('#addressbook_data_types'+i).val()==='longblob')
        {
                jQuery('#addressbook_boolFieldRequired'+i).attr('disabled','disabled');
                jQuery('#addressbook_txtDefaultValue'+i).attr('disabled','disabled');
        }
         
        if(jQuery('#addressbook_data_types'+i).val()==='date' || jQuery('#addressbook_data_types'+i).val()==='datetime')
        {            
                jQuery('#addressbook_img_calendar'+i).show();
        }    
                  
    }

    addressbook_totalFields=(SBNum>=addressbook_totalFields) ? (addressbook_totalFields+checkedFields.length-1) : (addressbook_totalFields+checkedFields.length);  
    addressbook_totalFields--;
    
}
  
function addressbook_unselectTable(selectedBox)
{    
    jQuery('#'+selectedBox).val('none');    
}
          
function addressbook_addRow(numRow,tableName,checkFieldName,checkFieldType,checkFieldSize)
{	          
        for(var i=0;i<numRow;i++)
        {
            jQuery('#addressbook_related_tables'+numRow).val(tableName);
            jQuery('#addressbook_txtFieldName'+numRow).val(checkFieldName);
            //jQuery('#txtFieldType'+numRow).val(checkFieldType);
            jQuery('#addressbook_txtDataLength'+numRow).val(checkFieldSize);
            jQuery('#addressbook_data_types'+numRow).val(checkFieldType);
            //jQuery('#addressbook_txtDefaultValue'+numRow).val(tableName);                                
        }
}

function addressbook_skip_none(array_name)
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

function addressbook_skip_blank(array_name)
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

function addressbook_validateStep1()
{
    if(!(jQuery('#addressbook_chk1to1').is(':checked')) && !(jQuery('#addressbook_chk1ton').is(':checked')))
    {
        alert("Please check the table AllCare_AddressBook1to1 or AllCare_AddressBook1ton");
        return false;
    }
        
    var relTables=document.getElementsByName("addressbook_related_tables");    
    //var fieldsEntered=document.getElementsByName("related_tables");    
    
    var flag=0;
    for(var j=0;j<relTables.length;j++)
    {
        if(relTables[j].value==='none' && jQuery('#addressbook_txtFieldName'+j).val()==='')
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
    
        
    var fieldName=document.getElementsByName("addressbook_txtFieldName"); 
    for(var k=0;k<fieldName.length;k++)
    {                        
        if(/\s/.test(jQuery('#addressbook_txtFieldName'+k).val()))
        {
            alert("Remove space in the Field name '"+jQuery('#addressbook_txtFieldName'+k).val()+"'");
            return false;           
        }        
    }
    
    return true;
    
}

function addressbook_validateStep2(mapping_flag)
{
    if(!(jQuery('#addressbook_rd1to1').is(':checked')) && !(jQuery('#addressbook_rd1ton').is(':checked')))
    {
        alert("Please check the table AllCare_AddressBook1to1 or AllCare_AddressBook1ton");
        return false;
    }
          
    var checked_fields;
    if(mapping_flag===0)
    {
        checked_fields=document.getElementsByName("addressbook_chkSelectedFields");        
    }
    else if(mapping_flag===1)
    {
        checked_fields=document.getElementsByName("addressbook_chkAllFields");        
    }
      
    //var checked_fields=document.getElementsByName("addressbook_chkSelectedFields");        
    var flag=0;
    for(var i=0;i<checked_fields.length;i++)
    {
        //addressbook_delList[i].checked = checkboxstatus;//(chkBtn.checked)? true : false;   
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

function addressbook_checkIfFieldAlreadyExists()
{
    var FieldName='';

    var chk1to1=(jQuery('#addressbook_chk1to1').is(':checked'))?1:0;
    var chk1ton=(jQuery('#addressbook_chk1ton').is(':checked'))?1:0;
    
    for(var i=0;i<20;i++)
    {      
        if(jQuery('#addressbook_txtFieldName'+i).val()!=='')
        {
            FieldName=jQuery('#addressbook_txtFieldName'+i).val();    
            var exists;
            $.ajax({
                type: 'POST',
                url: 'addressbook_check_if_field_already_exists.php',	
                dataType: "html",
		async: false, 
                data: {extension_name:'addressbook',FieldName:FieldName,chk1to1:chk1to1,chk1ton:chk1ton},

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

function addressbook_validateDataTypes()
{
    for(var i=0;i<20;i++)
    {           
        if(jQuery('#addressbook_txtFieldName'+i).val()!=='' && jQuery('#addressbook_data_types'+i).val()!=='')
        {
            // datalength is NOT mandatory for the following data types , 
            // eg. if the data type is 'date' then don't validate its empty datalength.
            //     else if data type is 'int' then validate its empty datalength
            if(jQuery('#addressbook_data_types'+i).val()!=='date' && jQuery('#addressbook_data_types'+i).val()!=='datetime' &&
               jQuery('#addressbook_data_types'+i).val()!=='time' && jQuery('#addressbook_data_types'+i).val()!=='timestamp' &&
               jQuery('#addressbook_data_types'+i).val()!=='text' && jQuery('#addressbook_data_types'+i).val()!=='tinytext' && 
               jQuery('#addressbook_data_types'+i).val()!=='mediumtext' && jQuery('#addressbook_data_types'+i).val()!=='longtext' && 
               jQuery('#addressbook_data_types'+i).val()!=='blob' && jQuery('#addressbook_data_types'+i).val()!=='tinyblob' &&
               jQuery('#addressbook_data_types'+i).val()!=='mediumblob' && jQuery('#addressbook_data_types'+i).val()!=='longblob' &&                        
               jQuery('#addressbook_data_types'+i).val()!=='geometry' && jQuery('#addressbook_data_types'+i).val()!=='point' &&
               jQuery('#addressbook_data_types'+i).val()!=='linestring' && jQuery('#addressbook_data_types'+i).val()!=='polygon' &&  
               jQuery('#addressbook_data_types'+i).val()!=='multipoint' && jQuery('#addressbook_data_types'+i).val()!=='multilinestring' &&    
               jQuery('#addressbook_data_types'+i).val()!=='multipolygon' && jQuery('#addressbook_data_types'+i).val()!=='geometrycollection')
            {
                if(jQuery('#addressbook_txtDataLength'+i).val()==='' && 
                   jQuery('#addressbook_txtDataLength'+i).attr('readonly')===true) //  isNaN(jQuery('#addressbook_txtDataLength'+i).val())
                {
                    alert('Please enter a valid numerical Data Length for the field '+jQuery('#addressbook_txtFieldName'+i).val());
                    jQuery('#addressbook_txtDataLength'+i).focus();
                    return false;                            
                }
            }
        }                        
    }
    return true;
}

function addressbook_validateDefaultValues()
{        
    for(var i=0;i<20;i++)
    {
        if(jQuery('#addressbook_txtFieldName'+i).val()!=='')
        {
            var FieldName=jQuery('#addressbook_txtFieldName'+i).val();               
            var dataType=jQuery('#addressbook_data_types'+i).val();
            var defaultValue=jQuery('#addressbook_txtDefaultValue'+i).val();
            
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
                                jQuery('#addressbook_txtDefaultValue'+i).val('');
                                //jQuery('#data_types'+i).val('none');
                                return false;
                            }  
                            if(dataLength!=='1')
                            {
                                alert("Data Length for the 'char' field "+FieldName+" should be 1");
                                jQuery('#addressbook_txtDataLength'+i).val('');
                                //jQuery('#addressbook_data_types'+i).val('none');
                                return false;
                            }
                            break;
                                            
            }
            
            if(jQuery('#addressbook_boolFieldRequired'+i).is(':checked') && defaultValue===''
                && (jQuery('#addressbook_data_types'+i).val()!=='text' && jQuery('#addressbook_data_types'+i).val()!=='tinytext' && 
                    jQuery('#addressbook_data_types'+i).val()!=='mediumtext' && jQuery('#addressbook_data_types'+i).val()!=='longtext' &&
                    jQuery('#addressbook_data_types'+i).val()!=='blob' && jQuery('#addressbook_data_types'+i).val()!=='tinyblob' &&
                    jQuery('#addressbook_data_types'+i).val()!=='mediumblob' && jQuery('#addressbook_data_types'+i).val()!=='longblob'))                                        
            {
                alert('Please enter the Default value for the field '+FieldName+' checked as Required');
                return false;
            }                                    
        }
    }
    return true;
}

function addressbook_checkDataType()
{
    for(var i=0;i<20;i++)
    { 
        jQuery('#addressbook_img_calendar'+i).hide(); 
        jQuery('#addressbook_boolFieldRequired'+i).prop('disabled','');
        jQuery('#addressbook_txtDefaultValue'+i).prop('disabled','');
        jQuery('#addressbook_txtDataLength'+i).prop('readonly','');
        
        if(jQuery('#addressbook_data_types'+i).val()==='text' || jQuery('#addressbook_data_types'+i).val()==='tinytext' || 
           jQuery('#addressbook_data_types'+i).val()==='mediumtext' || jQuery('#addressbook_data_types'+i).val()==='longtext' || 
           jQuery('#addressbook_data_types'+i).val()==='blob' || jQuery('#addressbook_data_types'+i).val()==='tinyblob' ||
           jQuery('#addressbook_data_types'+i).val()==='mediumblob' || jQuery('#addressbook_data_types'+i).val()==='longblob')
        {
            jQuery('#addressbook_boolFieldRequired'+i).attr('disabled','disabled');
            jQuery('#addressbook_txtDefaultValue'+i).attr('disabled','disabled');
        }
        if(jQuery('#addressbook_data_types'+i).val()==='date' || jQuery('#addressbook_data_types'+i).val()==='datetime')
        {
            jQuery('#addressbook_img_calendar'+i).show(); 
            jQuery('#addressbook_txtDataLength'+i).attr('readonly','readonly');
        }
    }
}

function addressbook_insertFields()
{
        var finalURL='all_extensions/add_new_fields.php';  
        
        var chk1to1=(jQuery('#addressbook_chk1to1').is(':checked'))?1:0;
        var chk1ton=(jQuery('#addressbook_chk1ton').is(':checked'))?1:0;
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
            relatedTables[i]=jQuery('#addressbook_related_tables'+i).val();
            //relatedTables.push(jQuery('#addressbook_related_tables'+i).val());
            FieldName[i]=jQuery('#addressbook_txtFieldName'+i).val();                                    
            FieldType[i]=jQuery('#addressbook_data_types'+i).val();
            DataLength[i]=jQuery('#addressbook_txtDataLength'+i).val();
            FieldRequired[i]=(jQuery('#addressbook_boolFieldRequired'+i).is(':checked'))?1:0;
            DefaultValue[i]=jQuery('#addressbook_txtDefaultValue'+i).val();            
            FieldViewEdit[i]=jQuery('#addressbook_view_edit'+i).val();                                    	            
        }                       
                
        relatedTables=addressbook_skip_none(relatedTables);                
        relatedTables=relatedTables.toString();
        
        FieldName=addressbook_skip_blank(FieldName);
        FieldName=FieldName.toString();
        
        FieldType=addressbook_skip_none(FieldType);
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
		data: {extension_name:'addressbook',chk1to1:chk1to1,chk1ton:chk1ton,relatedTables:relatedTables,FieldName:FieldName,
                       FieldType:FieldType,DataLength:DataLength,FieldRequired:FieldRequired,DefaultValue:DefaultValue,
                       FieldViewEdit:FieldViewEdit},
		
		success: function(response)
		{                         
                     alert(response);     
                     jQuery("#addressbook_tbl_grid").hide();     
                     jQuery("#addressbook_tbl_AllCare").hide();  
                     
                     jQuery("#addressbook_btnSaveFields").hide();                     
                     jQuery("#addressbook_btnClear").hide();  
                     jQuery("#addressbook_btn_Cancel").hide();  
                     jQuery("#addressbook_tbl_step2").show(); 
                     jQuery("#addressbook_btnNext").hide();
                     jQuery("#addressbook_btnBack").show();
                     
		},
		failure: function(response)
		{
			alert("error");
		}		
        });	       
}

function addressbook_showGroupsRecordsets()
{
    var finalURL='all_extensions/show_groups_recordsets.php';  
    var table_name=jQuery("#addressbook_rd1to1").is(":checked")?'tbl_allcare_addressbook1to1_fieldmapping':'tbl_allcare_addressbook1ton_fieldmapping';    
           
    $.ajax({
                type: 'POST',
                url: finalURL,	
                data: {extension_name:'addressbook',table_name:table_name},

                //data:relatedTables,
                success: function(response)
                {                         
                     jQuery("#addressbook_showGroupsRecordsets").html(response);                     
                },
                failure: function(response)
                {
                        alert("error");
                }		
            });	
}

function addressbook_showFields()
{                         
        var finalURL='all_extensions/show_fields.php';  
        var table_name=jQuery("#addressbook_rd1to1").is(":checked")?'tbl_allcare_addressbook1to1':'tbl_allcare_addressbook1ton';
                
        $.ajax({
                    type: 'POST',
                    url: finalURL,	
                    data: {extension_name:'addressbook',table_name:table_name},

                    //data:relatedTables,
                    success: function(response)
                    {                         
                         jQuery("#addressbook_showFields").html(response);                     
                    },
                    failure: function(response)
                    {
                            alert("error");
                    }		
               });		
}

function addressbook_hideNewButtonData()
{
    jQuery('#addressbook_lblGroup').hide();  
    jQuery('#addressbook_lblRecordset').hide();  
    jQuery('#addressbook_lblExistingFields').hide();  
    jQuery('#addressbook_txtGroupRecordset').val('');  
    jQuery('#addressbook_txtGroupRecordset').hide();      
    jQuery('#addressbook_lblFieldsSelected').hide();   
    jQuery('#addressbook_showFieldsByGroupRecordsets').html(''); 
    jQuery('#addressbook_showFields').html(''); 
}

function addressbook_showFieldsByGroupRecordsets(tableName)
{
        var finalURL='all_extensions/show_fields_by_groups_recordsets.php';  
        //var table_name=jQuery("#addressbook_rd1to1").is(":checked")?'tbl_allcare_addressbook1to1_fieldmapping':'tbl_allcare_addressbook1ton_fieldmapping';
        var table_name=tableName;
        var selectedGR=jQuery("#addressbook_comboGroupsRecordsets").val();        
        
        $.ajax({
                    type: 'POST',
                    url: finalURL,	
                    data: {extension_name:'addressbook',table_name:table_name,selectedGR:selectedGR},

                    //data:relatedTables,
                    success: function(response)
                    {                         
                         jQuery("#addressbook_showFieldsByGroupRecordsets").html(response);                     
                    },
                    failure: function(response)
                    {
                            alert("error");
                    }		
               });		
}

function addressbook_editGroupRecordset()  
{               
        var finalURL='all_extensions/edit_group_recordset.php';
        
        var TableName=(jQuery('#addressbook_rd1to1').is(':checked'))?'tbl_allcare_addressbook1to1_fieldmapping':'tbl_allcare_addressbook1ton_fieldmapping';    
        
        var GroupRecordsetName=jQuery('#addressbook_comboGroupsRecordsets').val();
        
        var selectedFields=new Array();   
        
        var showYesNo = jQuery('#rd_show_yes').is(':checked') ? 'Y' : 'N';
        
        var checked_fields=document.getElementsByName("addressbook_chkSelectedFields");        

        for(i=0;i<checked_fields.length;i++)
        {
            //addressbook_delList[i].checked = checkboxstatus;//(chkBtn.checked)? true : false;   
            if(checked_fields[i].checked)
            {
                selectedFields.push(checked_fields[i].value);
            }
        }      
        
        selectedFields=selectedFields.toString();
        
        $.ajax({
            type: 'POST',
            url: finalURL,	
            data: {extension_name:'addressbook',TableName:TableName,GroupRecordsetName:GroupRecordsetName,
                   selectedFields:selectedFields,showYesNo:showYesNo},

            //data:relatedTables,
            success: function(response)
            {                         
                 alert(response);   
                 addressbook_showGroupsRecordsets();
                 addressbook_hideNewButtonData();

            },
            failure: function(response)
            {
                    alert("error");
            }		
           });		                       
}

function addressbook_insert_in_mapping()
{
    var finalURL='all_extensions/insert_in_mapping.php';                  
    
var mappingTableName=(jQuery('#addressbook_rd1to1').is(':checked'))?'tbl_allcare_addressbook1to1_fieldmapping':'tbl_allcare_addressbook1ton_fieldmapping';    
    
    
    var showYesNo = jQuery('#rd_show_yes').is(':checked') ? 'Y' : 'N';
    
    var groupRecordsetName=jQuery('#addressbook_txtGroupRecordset').val();

    //var selectedFields=jQuery('#comboFields').val();
    var selectedFields=new Array();
    
    var checked_fields=document.getElementsByName("addressbook_chkAllFields");            

    for(i=0;i<checked_fields.length;i++)
    {
        //addressbook_delList[i].checked = checkboxstatus;//(chkBtn.checked)? true : false;   
        if(checked_fields[i].checked)
        {
            selectedFields.push(checked_fields[i].value);
        }
    }      
               
    selectedFields=selectedFields.toString();
    
    $.ajax({
            type: 'POST',
            url: finalURL,	
            data: {extension_name:'addressbook',mappingTableName:mappingTableName,groupRecordsetName:groupRecordsetName,
                   selectedFields:selectedFields,showYesNo:showYesNo},

            //data:relatedTables,
            success: function(response)
            {                         
                 alert(response);   
                 addressbook_showGroupsRecordsets();
                 addressbook_hideNewButtonData();

            },
            failure: function(response)
            {
                    alert("error");
            }		
           });		         
}

var addressbook_checkedAll=false;
function addressbook_checkAll(checkboxstatus)
{            
    var addressbook_delList=document.getElementsByName("addressbook_checkClear");        

    for(var i=0;i<addressbook_delList.length;i++)
    {
        addressbook_delList[i].checked = checkboxstatus;//(chkBtn.checked)? true : false;            
    }        
    
    addressbook_checkedAll=checkboxstatus;
}


function addressbook_clearSelected()
{
    var addressbook_delList=document.getElementsByName("addressbook_checkClear"); 
    var addressbook_checkedNum=0;
    var addressbook_allFieldsClear=0;
    
    for(var i=(addressbook_delList.length-1);i>=0;i--)
    {
        if(addressbook_delList[i].checked===true)
        {
            addressbook_checkedNum++;        
            
            jQuery('#addressbook_related_tables'+i).val('none');
            jQuery('#addressbook_txtFieldName'+i).val('');
            jQuery('#addressbook_data_types'+i).val('none');
            jQuery('#addressbook_txtDataLength'+i).val('');
            /*
            jQuery('#addressbook_txtFieldName'+i).prop('readonly','');                            
            jQuery('#addressbook_data_types'+i).prop('disabled','');    
            jQuery('#addressbook_txtDataLength'+i).prop('readonly','');
            */
            jQuery('#addressbook_boolFieldRequired'+i).prop('checked', false);            
            jQuery('#addressbook_txtDefaultValue'+i).val('');
            
            if(jQuery('#addressbook_img_calendar'+i).css('display')==='inline' || jQuery('#addressbook_img_calendar'+i).css('display')==='block')
            {
                jQuery('#addressbook_img_calendar'+i).hide();
            }
            
            jQuery('#addressbook_view_edit'+i).val('Y');            
            addressbook_delList[i].checked=false;
            
            for(var j=i;j<(addressbook_totalFields);j++)
            {
                jQuery('#addressbook_related_tables'+j).val(jQuery('#addressbook_related_tables'+(j+1)).val());
                jQuery('#addressbook_txtFieldName'+j).val(jQuery('#addressbook_txtFieldName'+(j+1)).val());
                jQuery('#addressbook_data_types'+j).val(jQuery('#addressbook_data_types'+(j+1)).val());
                jQuery('#addressbook_txtDataLength'+j).val(jQuery('#addressbook_txtDataLength'+(j+1)).val());                
                
                var fn = jQuery('#addressbook_txtFieldName'+(j+1)).prop('readonly') ? 'readonly' :'';
                jQuery('#addressbook_txtFieldName'+j).prop('readonly',fn);                            
                
                var dt = jQuery('#addressbook_data_types'+(j+1)).prop('disabled') ? 'disabled' :'';
                jQuery('#addressbook_data_types'+j).prop('disabled',dt);                          
                                
                var dl = jQuery('#addressbook_txtDataLength'+(j+1)).prop('readonly') ? 'readonly' : '';
                jQuery('#addressbook_txtDataLength'+j).prop('readonly',dl);                                                       
                
                var fr = jQuery('#addressbook_boolFieldRequired'+(j+1)).is(':checked') ? true : false;
                jQuery('#addressbook_boolFieldRequired'+j).prop('checked', fr);
                                
                jQuery('#addressbook_txtDefaultValue'+j).val(jQuery('#addressbook_txtDefaultValue'+(j+1)).val());
                jQuery('#addressbook_view_edit'+j).val(jQuery('#addressbook_view_edit'+(j+1)).val());                                   
                                
                var frReq = jQuery('#addressbook_boolFieldRequired'+(j+1)).prop('disabled') ? 'disabled' : '';
                jQuery('#addressbook_boolFieldRequired'+j).prop('disabled',frReq);                                                       
                
                var dv = jQuery('#addressbook_txtDefaultValue'+(j+1)).prop('disabled') ? 'disabled' : '';
                jQuery('#addressbook_txtDefaultValue'+j).prop('disabled', dv);
                
                var imgCal = (jQuery('#addressbook_img_calendar'+(j+1)).css('display')==='inline' || jQuery('#addressbook_img_calendar'+(j+1)).css('display')==='block') ? 'inline' : 'none';
                
                jQuery('#addressbook_img_calendar'+j).css('display', imgCal);                
                                                
                jQuery('#addressbook_related_tables'+(j+1)).val('none');
                jQuery('#addressbook_txtFieldName'+(j+1)).val('');
                jQuery('#addressbook_data_types'+(j+1)).val('none');
                jQuery('#addressbook_txtDataLength'+(j+1)).val('');            

                jQuery('#addressbook_txtFieldName'+(j+1)).prop('readonly','');                            
                jQuery('#addressbook_data_types'+(j+1)).prop('disabled','');    
                jQuery('#addressbook_txtDataLength'+(j+1)).prop('readonly','');

                jQuery('#addressbook_boolFieldRequired'+(j+1)).prop('checked', false);            
                jQuery('#addressbook_txtDefaultValue'+(j+1)).val('');
                jQuery('#addressbook_img_calendar'+(j+1)).hide();             
                jQuery('#addressbook_boolFieldRequired'+(j+1)).prop('disabled','');    
                jQuery('#addressbook_txtDefaultValue'+(j+1)).prop('disabled','');    
                
                jQuery('#addressbook_view_edit'+(j+1)).val('Y');                                      
            }
            
            jQuery('#addressbook_related_tables'+j).val('none');
            jQuery('#addressbook_txtFieldName'+j).val('');
            jQuery('#addressbook_data_types'+j).val('none');
            jQuery('#addressbook_txtDataLength'+j).val('');            

            jQuery('#addressbook_txtFieldName'+j).prop('readonly','');                            
            jQuery('#addressbook_data_types'+j).prop('disabled','');    
            jQuery('#addressbook_txtDataLength'+j).prop('readonly','');

            jQuery('#addressbook_boolFieldRequired'+j).prop('checked', false);            
            jQuery('#addressbook_txtDefaultValue'+j).val('');
            jQuery('#addressbook_img_calendar'+j).hide();          
            jQuery('#addressbook_view_edit'+j).val('Y');                
                      
            //if(i===0)
            if(i===0 && addressbook_totalFields===0)
            {addressbook_allFieldsClear=1;}
                      
        }
        
        jQuery('#addressbook_check_All').prop('checked', false);   
    }
    
    //addressbook_totalFields=0;
    
    addressbook_totalFields = (addressbook_allFieldsClear===1 || addressbook_checkedAll===true) ? 0 : Math.abs(addressbook_totalFields-addressbook_checkedNum);
    addressbook_checkedAll=false;
    //alert('now tf= '+addressbook_totalFields);
}


</script>
