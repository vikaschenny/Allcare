<!--
<style type="text/css">@import url(../../library/dynarch_calendar.css);</style>
<script type="text/javascript" src="../../library/dialog.js"></script>
<script type="text/javascript" src="../../library/textformat.js"></script>
<script type="text/javascript" src="../../library/dynarch_calendar.js"></script>
<script type="text/javascript" src="../../library/dynarch_calendar_en.js"></script>
<script type="text/javascript" src="../../library/dynarch_calendar_setup.js"></script>
-->
<?php


      echo "<table id='pharmacy_tbl_AllCare' name='pharmacy_tbl_AllCare'>
                <tr title='1 to 1' border='1'>
                    <td valign='top' colspan='2'><b>AllCare_Pharmacy1to1 </b></td>
                    <td valign='top'><input id='pharmacy_chk1to1' type='checkbox' value='1' name='pharmacy_chk1to1'></td>
                </tr>
                <tr title='1 to n' border='1'>
                    <td valign='top' colspan='2'><b>AllCare_Pharmacy1ton </b></td>
                    <td valign='top'><input id='pharmacy_chk1ton' type='checkbox' value='1' name='pharmacy_chk1ton'></td>
                </tr>
            </table>
            <input type='button' id='pharmacy_btnNext' name='pharmacy_btnNext' value='Next' style='float: right; margin-top: -35px;'
                   onclick='javascript: jQuery(\"#pharmacy_tbl_AllCare\").hide();
                                        jQuery(\"#pharmacy_tbl_grid\").hide();
                                        jQuery(\"#pharmacy_tbl_step2\").show();
                                        jQuery(\"#pharmacy_btnNext\").hide();
                                        jQuery(\"#pharmacy_btnBack\").show();
                                        jQuery(\"#pharmacy_btnSaveFields\").hide();                     
                                        jQuery(\"#pharmacy_btnClear\").hide();  
                                        jQuery(\"#pharmacy_btn_Cancel\").hide();  ' />
            <input type='button' id='pharmacy_btnBack' name='pharmacy_btnBack' value='Back' style='display:none' 
                   onclick='javascript: jQuery(\"#pharmacy_tbl_AllCare\").show();
                                        jQuery(\"#pharmacy_tbl_grid\").show();
                                        jQuery(\"#pharmacy_tbl_step2\").hide();
                                        jQuery(\"#pharmacy_btnBack\").hide();
                                        jQuery(\"#pharmacy_btnNext\").show();
                                        jQuery(\"#pharmacy_btnSaveFields\").show();                     
                                        jQuery(\"#pharmacy_btnClear\").show();  
                                        jQuery(\"#pharmacy_btn_Cancel\").show();' />
            <br>
            <table border='1' id='pharmacy_tbl_grid' name='pharmacy_tbl_grid'>                
                <tr>
                    <th><input type='checkbox' id='pharmacy_check_All' name='pharmacy_check_All' onclick='pharmacy_checkAll(this.checked);' /></th>
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
                    <td align='center'><input type='checkbox' id='pharmacy_checkClear".$cnt."' name='pharmacy_checkClear' /></td>";
                                           
      echo "<td><select id='pharmacy_related_tables".$cnt."' name='pharmacy_related_tables' onchange='javascript:return pharmacy_newEvt(this.id);' style='width:125px'>          
            <option value='none'>none</option>
                $table_list
            </select></td>
            <td><input type='text' id='pharmacy_txtFieldName".$cnt."' name='pharmacy_txtFieldName' style='width:125px' /></td>";

            //echo "<td><input type='text' id='pharmacy_txtFieldType".$cnt."' name='pharmacy_txtFieldType[]' style='width:125px' /></td>";
            
            echo "<td>
                    <select id='pharmacy_data_types".$cnt."' name='pharmacy_data_types' onchange='pharmacy_checkDataType();'>
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
                                              
            echo "<td><input type='text' id='pharmacy_txtDataLength".$cnt."' name='pharmacy_txtDataLength' style='width:125px' /></td>
            <td align='center'>
                <input type='checkbox' id='pharmacy_boolFieldRequired".$cnt."' name='pharmacy_boolFieldRequired' />
            </td>
            <td>
                <!--<input type='text' id='pharmacy_txtDefaultValue".$cnt."' name='pharmacy_txtDefaultValue' style='width:125px' />-->
                <input type='text' size='10' name='pharmacy_txtDefaultValue' id='pharmacy_txtDefaultValue".$cnt."' onkeyup='datekeyup(this,mypcc)' onblur='dateblur(this,mypcc)' title='yyyy-mm-dd last date of this event' />
<img src='../../interface/pic/show_calendar.gif' align='absbottom' width='24' height='22'
id='pharmacy_img_calendar".$cnt."' border='0' alt='[?]' style='cursor:pointer;display:none;'
title='Click here to choose a date'>
<script>
Calendar.setup({inputField:'pharmacy_txtDefaultValue".$cnt."', ifFormat:'%Y-%m-%d', button:'pharmacy_img_calendar".$cnt."'});
</script>
            
            </td>
                                       
            <td>
                <select id='pharmacy_view_edit".$cnt."' name='pharmacy_view_edit' style='width:100px'>
                    <option value='Y'>Edit</option>
                    <option value='N'>View</option>                    
                </select>
            </td>
        </tr>";
      } 
      echo "</table>";                 
        
?>

    <table id="pharmacy_tbl_step2" name="pharmacy_tbl_step2" style="display:none;">
        
        <tr><td></td></tr>
        <tr> <td></td>               
            <td align="left"><input type="radio" id="pharmacy_rd1to1" name="pharmacy_radallcare" value="1to1"
                       onchange="javascript:jQuery('#pharmacy_lblExistingRecordsets').hide();
                                            jQuery('#pharmacy_lblExistingGroups').show();  
                                            pharmacy_showGroupsRecordsets();
                                            pharmacy_hideNewButtonData();
                                            jQuery('#td_Show_YesNo').hide();
                                            ">
            AllCare_Pharmacy1to1</td>
        </tr>
        <tr>   <td></td>             
            <td align="left">
                <input type="radio" id="pharmacy_rd1ton" name="pharmacy_radallcare" value="1ton"
                       onchange="javascript:jQuery('#pharmacy_lblExistingRecordsets').show();
                                            jQuery('#pharmacy_lblExistingGroups').hide();  
                                            pharmacy_showGroupsRecordsets();
                                            pharmacy_hideNewButtonData();      
                                            jQuery('#td_Show_YesNo').hide();
                                            " />
            AllCare_Pharmacy1ton</td>
        </tr>  
        <tr><td></td></tr>
        <tr>
            <td style="width:150px"><label id='pharmacy_lblExistingGroups' style='display:none;'>Existing Groups </label>
                <label id='pharmacy_lblExistingRecordsets' style='display:none;'>Existing Recordsets</label>
            </td>                                
            <td>                    
                <div id="pharmacy_showGroupsRecordsets" name="pharmacy_showGroupsRecordsets"></div>
            </td>
        </tr>
        <tr>
            <td>
                <label id='pharmacy_lblFieldsSelected' style='display:none;'>Field Selected : </label>
            </td>
            <td>
                <div id="pharmacy_showFieldsByGroupRecordsets" name="pharmacy_showFieldsByGroupRecordsets"></div>
            </td>
        </tr>
        <tr>
            <td><label id='pharmacy_lblGroup' style='display:none;'>Group Name</label>
                <label id='pharmacy_lblRecordset' style='display:none;'>Recordset</label>
            </td>
            <td>
                <input type="text" id='pharmacy_txtGroupRecordset' name="pharmacy_txtGroupRecordset" value="" style='display:none;'>
            </td>
        </tr>
        <tr>     
            <td><label id='pharmacy_lblExistingFields' style='display:none;'>Fields : </label></td>           
            <td>
                <div id="pharmacy_showFields" name="pharmacy_showFields" style='border:1px'>               
                </div> 
            </td>
        </tr>
<!--        <tr><td></td>
            <td id="td_Show_YesNo" style="display:none;"><label id='pharmacy_lblShowYesNo'>Show this Group/Recordset      
                <input type='radio' id='rd_show_yes' name='rd_show_yes_no' value='Y' checked /><label for='rd_show_yes'>Yes</label>
                <input type='radio' id='rd_show_no' name='rd_show_yes_no' value='N' /><label for='rd_show_no'>No</label>
            </td>
        </tr>-->

        <tr>
            <td colspan="2" align="center">      
    <!--            <input type='submit' id='pharmacy_btnSave' name='pharmacy_btnSave' value='Save' />-->
                <input type='button' id='pharmacy_btnSave' name='pharmacy_btnSave' value='Save' 
                       onclick='javascript: 
                           if(jQuery("#pharmacy_comboGroupsRecordsets").val()!=="none")    
                                            {
                                                if(pharmacy_validateStep2(0))
                                                {
                                                    pharmacy_editGroupRecordset();
                                                }
                                            }
                                            if(jQuery("#pharmacy_txtGroupRecordset").val()!=="")
                                            {
                                                if(pharmacy_validateStep2(1))
                                                {
                                                    pharmacy_insert_in_mapping();
                                                }
                                            }
                                            if(jQuery("#pharmacy_comboGroupsRecordsets").val()==="none" && jQuery("#pharmacy_txtGroupRecordset").val()==="")
                                            {
                                                alert("Enter the Group/Recordset name");
                                                
                                            }
                                        ' />
                <input type='button' id='pharmacy_btnCancel' name='pharmacy_btnCancel' value='Cancel'
                       onclick='javascript:location.href="edit_globals.php";'/>
            </td>
        </tr>
     </table>

    <br>
        <center>                   
            <input type='button' id='pharmacy_btnSaveFields' name='pharmacy_btnSaveFields' value='Save Fields' 
                   onclick='javascript:if(pharmacy_validateStep1())
                                       {
                                            if(!pharmacy_checkIfFieldAlreadyExists() && pharmacy_validateDataTypes() && pharmacy_validateDefaultValues())
                                            {   
                                                pharmacy_insertFields();
                                            }                                            
                                       }' />                                
            <input type='button' id='pharmacy_btnClear' name='pharmacy_btnClear' value='Clear' onclick='javascript:pharmacy_clearSelected();' />
            <input type='button' id='pharmacy_btn_Cancel' name='pharmacy_btn_Cancel' value='Cancel' 
                   onclick='javascript:location.href="edit_globals.php";' />
        </center>
    

<script type='text/javascript'>
    
function pharmacy_newEvt(selectedBox)
{
     if(document.getElementById(selectedBox).value!=='none')
     {
  dlgopen('all_extensions/get_table_fields.php?table_name='+document.getElementById(selectedBox).value+'&selectedBox='+selectedBox+'&extension_name=pharmacy',
   '_blank', 550, 270);
     }
  return false;
}

var pharmacy_totalFields=0;
 
function pharmacy_receivedFromChild(selectedBox,tableName,checkedFields,checkedFieldsType)
{
   //alert('ft='+checkedFieldsType);
    var SBNum=0;
    var s=selectedBox.split("pharmacy_related_tables"); 
    SBNum=s[1];
    /*
    if(SBNum>(pharmacy_totalFields+1))
    {
        SBNum=(pharmacy_totalFields===0)?0:pharmacy_totalFields;
    }
    */
    //alert('SBNum = '+SBNum);    
    //alert('length = '+checkedFields.length);  
    //alert('pharmacy_totalFields = '+pharmacy_totalFields);  
    var split0='',split1='',split2='';        
                
    jQuery('#pharmacy_related_tables'+SBNum).val(tableName);
    jQuery('#pharmacy_txtFieldName'+SBNum).val(checkedFields[0]);
    
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
    
    jQuery('#pharmacy_data_types'+SBNum).val(split1);    
    jQuery('#pharmacy_txtDataLength'+SBNum).val(split2[0]);
        
    if(jQuery('#pharmacy_related_tables'+SBNum).val()!=='none')
    {
        jQuery('#pharmacy_txtFieldName'+SBNum).attr('readonly','readonly');                            
        jQuery('#pharmacy_data_types'+SBNum).attr('disabled','disabled');    
        jQuery('#pharmacy_txtDataLength'+SBNum).attr('readonly','readonly');
    }     
    
    if(jQuery('#pharmacy_data_types'+SBNum).val()==='text' || jQuery('#pharmacy_data_types'+SBNum).val()==='tinytext' || jQuery('#pharmacy_data_types'+SBNum).val()==='mediumtext' ||
               jQuery('#pharmacy_data_types'+SBNum).val()==='longtext' || jQuery('#pharmacy_data_types'+SBNum).val()==='year' ||
               jQuery('#pharmacy_data_types'+SBNum).val()==='blob' || jQuery('#pharmacy_data_types'+SBNum).val()==='tinyblob' ||
               jQuery('#pharmacy_data_types'+SBNum).val()==='mediumblob' || jQuery('#pharmacy_data_types'+SBNum).val()==='longblob')
    {
            jQuery('#pharmacy_boolFieldRequired'+SBNum).attr('disabled','disabled');
            jQuery('#pharmacy_txtDefaultValue'+SBNum).attr('disabled','disabled');
    }
    
    (jQuery('#pharmacy_data_types'+SBNum).val()==='date' || 
     jQuery('#pharmacy_data_types'+SBNum).val()==='datetime') ? jQuery('#pharmacy_img_calendar'+SBNum).show() : jQuery('#pharmacy_img_calendar'+SBNum).hide();
            
                        
    if(SBNum>=pharmacy_totalFields)   
    {
        pharmacy_totalFields++;
    }

    for(var i=pharmacy_totalFields;i<(pharmacy_totalFields+checkedFields.length-1);i++)
    {
        jQuery('#pharmacy_related_tables'+i).val(tableName);
        jQuery('#pharmacy_txtFieldName'+i).val(checkedFields[i-pharmacy_totalFields+1]);
        
        if(checkedFieldsType[i-pharmacy_totalFields+1].indexOf('(') !== -1)  // datatype contains '('  e.g. varchar(100)
        {
            split0 = checkedFieldsType[i-pharmacy_totalFields+1].split("(");
            split1 = split0[0];
            split2 = split0[1].split(")");
        }
        else
        {
            split1 = checkedFieldsType[i-pharmacy_totalFields+1];   
            split2 = '';
        }
        
        jQuery('#pharmacy_data_types'+i).val(split1);    
        jQuery('#pharmacy_txtDataLength'+i).val(split2[0]);
                
        if(jQuery('#pharmacy_related_tables'+i).val()!=='none')
        {
            jQuery('#pharmacy_txtFieldName'+i).attr('readonly','readonly');                            
            jQuery('#pharmacy_data_types'+i).attr('disabled','disabled');    
            jQuery('#pharmacy_txtDataLength'+i).attr('readonly','readonly');
        }     
        
        if(jQuery('#pharmacy_data_types'+i).val()==='text' || jQuery('#pharmacy_data_types'+i).val()==='tinytext' || jQuery('#pharmacy_data_types'+i).val()==='mediumtext' ||
               jQuery('#pharmacy_data_types'+i).val()==='longtext' || jQuery('#pharmacy_data_types'+i).val()==='year' ||
               jQuery('#pharmacy_data_types'+i).val()==='blob' || jQuery('#pharmacy_data_types'+i).val()==='tinyblob' ||
               jQuery('#pharmacy_data_types'+i).val()==='mediumblob' || jQuery('#pharmacy_data_types'+i).val()==='longblob')
        {
                jQuery('#pharmacy_boolFieldRequired'+i).attr('disabled','disabled');
                jQuery('#pharmacy_txtDefaultValue'+i).attr('disabled','disabled');
        }
         
        if(jQuery('#pharmacy_data_types'+i).val()==='date' || jQuery('#pharmacy_data_types'+i).val()==='datetime')
        {            
                jQuery('#pharmacy_img_calendar'+i).show();
        }    
                  
    }

    pharmacy_totalFields=(SBNum>=pharmacy_totalFields) ? (pharmacy_totalFields+checkedFields.length-1) : (pharmacy_totalFields+checkedFields.length);  
    pharmacy_totalFields--;
    
}
  
function pharmacy_unselectTable(selectedBox)
{
    jQuery('#'+selectedBox).val('none');    
}
          
function pharmacy_addRow(numRow,tableName,checkFieldName,checkFieldType,checkFieldSize)
{
        for(var i=0;i<numRow;i++)
        {
            jQuery('#pharmacy_related_tables'+numRow).val(tableName);
            jQuery('#pharmacy_txtFieldName'+numRow).val(checkFieldName);
            //jQuery('#txtFieldType'+numRow).val(checkFieldType);
            jQuery('#pharmacy_txtDataLength'+numRow).val(checkFieldSize);
            jQuery('#pharmacy_data_types'+numRow).val(checkFieldType);
            //jQuery('#pharmacy_txtDefaultValue'+numRow).val(tableName);                                
        }
}

function pharmacy_skip_none(array_name)
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

function pharmacy_skip_blank(array_name)
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

function pharmacy_validateStep1()
{
    if(!(jQuery('#pharmacy_chk1to1').is(':checked')) && !(jQuery('#pharmacy_chk1ton').is(':checked')))
    {
        alert("Please check the table AllCare_Pharmacy1to1 or AllCare_Pharmacy1ton");
        return false;
    }
        
    var relTables=document.getElementsByName("pharmacy_related_tables");    
    //var fieldsEntered=document.getElementsByName("related_tables");    
    
    var flag=0;
    for(var j=0;j<relTables.length;j++)
    {
        if(relTables[j].value==='none' && jQuery('#pharmacy_txtFieldName'+j).val()==='')
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
    
        
    var fieldName=document.getElementsByName("pharmacy_txtFieldName"); 
    for(var k=0;k<fieldName.length;k++)
    {                        
        if(/\s/.test(jQuery('#pharmacy_txtFieldName'+k).val()))
        {
            alert("Remove space in the Field name '"+jQuery('#pharmacy_txtFieldName'+k).val()+"'");
            return false;           
        }        
    }
    
    return true;
    
}

function pharmacy_validateStep2(mapping_flag)
{
    if(!(jQuery('#pharmacy_rd1to1').is(':checked')) && !(jQuery('#pharmacy_rd1ton').is(':checked')))
    {
        alert("Please check the table AllCare_Pharmacy1to1 or AllCare_Pharmacy1ton");
        return false;
    }
            
    var checked_fields;
    if(mapping_flag===0)
    {
        checked_fields=document.getElementsByName("pharmacy_chkSelectedFields");        
    }
    else if(mapping_flag===1)
    {
        checked_fields=document.getElementsByName("pharmacy_chkAllFields");        
    }
            
    //var checked_fields=document.getElementsByName("pharmacy_chkSelectedFields");
    //var checked_fields=jQuery("input[name=pharmacy_chkAllFields]");
    var flag=0;
    
    for(var i=0;i<checked_fields.length;i++)
    {
        //pharmacy_delList[i].checked = checkboxstatus;//(chkBtn.checked)? true : false;   
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

function pharmacy_checkIfFieldAlreadyExists()
{
    var FieldName='';

    var chk1to1=(jQuery('#pharmacy_chk1to1').is(':checked'))?1:0;
    var chk1ton=(jQuery('#pharmacy_chk1ton').is(':checked'))?1:0;
    
    for(var i=0;i<20;i++)
    {      
        if(jQuery('#pharmacy_txtFieldName'+i).val()!=='')
        {
            FieldName=jQuery('#pharmacy_txtFieldName'+i).val();    
            var exists;
            $.ajax({
                type: 'POST',
                url: 'pharmacy_check_if_field_already_exists.php',	
                dataType: "html",
		async: false, 
                data: {extension_name:'pharmacy',FieldName:FieldName,chk1to1:chk1to1,chk1ton:chk1ton},

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

function pharmacy_validateDataTypes()
{
    for(var i=0;i<20;i++)
    {           
        if(jQuery('#pharmacy_txtFieldName'+i).val()!=='' && jQuery('#pharmacy_data_types'+i).val()!=='')
        {
            // datalength is NOT mandatory for the following data types , 
            // eg. if the data type is 'date' then don't validate its empty datalength.
            //     else if data type is 'int' then validate its empty datalength
            if(jQuery('#pharmacy_data_types'+i).val()!=='date' && jQuery('#pharmacy_data_types'+i).val()!=='datetime' &&
               jQuery('#pharmacy_data_types'+i).val()!=='time' && jQuery('#pharmacy_data_types'+i).val()!=='timestamp' &&
               jQuery('#pharmacy_data_types'+i).val()!=='text' && jQuery('#pharmacy_data_types'+i).val()!=='tinytext' && 
               jQuery('#pharmacy_data_types'+i).val()!=='mediumtext' && jQuery('#pharmacy_data_types'+i).val()!=='longtext' && 
               jQuery('#pharmacy_data_types'+i).val()!=='blob' && jQuery('#pharmacy_data_types'+i).val()!=='tinyblob' &&
               jQuery('#pharmacy_data_types'+i).val()!=='mediumblob' && jQuery('#pharmacy_data_types'+i).val()!=='longblob' &&                        
               jQuery('#pharmacy_data_types'+i).val()!=='geometry' && jQuery('#pharmacy_data_types'+i).val()!=='point' &&
               jQuery('#pharmacy_data_types'+i).val()!=='linestring' && jQuery('#pharmacy_data_types'+i).val()!=='polygon' &&  
               jQuery('#pharmacy_data_types'+i).val()!=='multipoint' && jQuery('#pharmacy_data_types'+i).val()!=='multilinestring' &&    
               jQuery('#pharmacy_data_types'+i).val()!=='multipolygon' && jQuery('#pharmacy_data_types'+i).val()!=='geometrycollection')
            {
                if(jQuery('#pharmacy_txtDataLength'+i).val()==='' && 
                   jQuery('#pharmacy_txtDataLength'+i).attr('readonly')===true) //  isNaN(jQuery('#pharmacy_txtDataLength'+i).val())
                {
                    alert('Please enter a valid numerical Data Length for the field '+jQuery('#pharmacy_txtFieldName'+i).val());
                    jQuery('#pharmacy_txtDataLength'+i).focus();
                    return false;                            
                }
            }
        }                        
    }
    return true;
}

function pharmacy_validateDefaultValues()
{        
    for(var i=0;i<20;i++)
    {          
        if(jQuery('#pharmacy_txtFieldName'+i).val()!=='')
        {
            var FieldName=jQuery('#pharmacy_txtFieldName'+i).val();               
            var dataType=jQuery('#pharmacy_data_types'+i).val();
            var defaultValue=jQuery('#pharmacy_txtDefaultValue'+i).val();
            
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
                                jQuery('#pharmacy_txtDefaultValue'+i).val('');
                                //jQuery('#data_types'+i).val('none');
                                return false;
                            }  
                            if(dataLength!=='1')
                            {
                                alert("Data Length for the 'char' field "+FieldName+" should be 1");
                                jQuery('#pharmacy_txtDataLength'+i).val('');
                                //jQuery('#pharmacy_data_types'+i).val('none');
                                return false;
                            }
                            break;
                                            
            }
            
            if(jQuery('#pharmacy_boolFieldRequired'+i).is(':checked') && defaultValue===''
                && (jQuery('#pharmacy_data_types'+i).val()!=='text' && jQuery('#pharmacy_data_types'+i).val()!=='tinytext' && 
                    jQuery('#pharmacy_data_types'+i).val()!=='mediumtext' && jQuery('#pharmacy_data_types'+i).val()!=='longtext' &&
                    jQuery('#pharmacy_data_types'+i).val()!=='blob' && jQuery('#pharmacy_data_types'+i).val()!=='tinyblob' &&
                    jQuery('#pharmacy_data_types'+i).val()!=='mediumblob' && jQuery('#pharmacy_data_types'+i).val()!=='longblob'))                                        
            {
                alert('Please enter the Default value for the field '+FieldName+' checked as Required');
                return false;
            }                                    
        }
    }
    return true;
}

function pharmacy_checkDataType()
{
    for(var i=0;i<20;i++)
    { 
        jQuery('#pharmacy_img_calendar'+i).hide(); 
        jQuery('#pharmacy_boolFieldRequired'+i).prop('disabled','');
        jQuery('#pharmacy_txtDefaultValue'+i).prop('disabled','');
        jQuery('#pharmacy_txtDataLength'+i).prop('readonly','');
        
        if(jQuery('#pharmacy_data_types'+i).val()==='text' || jQuery('#pharmacy_data_types'+i).val()==='tinytext' || 
           jQuery('#pharmacy_data_types'+i).val()==='mediumtext' || jQuery('#pharmacy_data_types'+i).val()==='longtext' || 
           jQuery('#pharmacy_data_types'+i).val()==='blob' || jQuery('#pharmacy_data_types'+i).val()==='tinyblob' ||
           jQuery('#pharmacy_data_types'+i).val()==='mediumblob' || jQuery('#pharmacy_data_types'+i).val()==='longblob')
        {
                jQuery('#pharmacy_boolFieldRequired'+i).attr('disabled','disabled');
                jQuery('#pharmacy_txtDefaultValue'+i).attr('disabled','disabled');
        }
        if(jQuery('#pharmacy_data_types'+i).val()==='date' || jQuery('#pharmacy_data_types'+i).val()==='datetime')
        {
            jQuery('#pharmacy_img_calendar'+i).show(); 
            jQuery('#pharmacy_txtDataLength'+i).attr('readonly','readonly');
        }
    }
}

function pharmacy_insertFields()
{
        var finalURL='all_extensions/add_new_fields.php';  
        //var finalURL='all_extensions/add_new_fields.php';  
        
        var chk1to1=(jQuery('#pharmacy_chk1to1').is(':checked'))?1:0;
        var chk1ton=(jQuery('#pharmacy_chk1ton').is(':checked'))?1:0;
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
            relatedTables[i]=jQuery('#pharmacy_related_tables'+i).val();
            //relatedTables.push(jQuery('#pharmacy_related_tables'+i).val());
            FieldName[i]=jQuery('#pharmacy_txtFieldName'+i).val();                                    
            FieldType[i]=jQuery('#pharmacy_data_types'+i).val();
            DataLength[i]=jQuery('#pharmacy_txtDataLength'+i).val();
            FieldRequired[i]=(jQuery('#pharmacy_boolFieldRequired'+i).is(':checked'))?1:0;
            DefaultValue[i]=jQuery('#pharmacy_txtDefaultValue'+i).val();            
            FieldViewEdit[i]=jQuery('#pharmacy_view_edit'+i).val();                                    	            
        }                       
                
        relatedTables=pharmacy_skip_none(relatedTables);                
        relatedTables=relatedTables.toString();
        
        FieldName=pharmacy_skip_blank(FieldName);
        FieldName=FieldName.toString();
        
        FieldType=pharmacy_skip_none(FieldType);
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
		data: {extension_name:'pharmacy',chk1to1:chk1to1,chk1ton:chk1ton,relatedTables:relatedTables,FieldName:FieldName,
                       FieldType:FieldType,DataLength:DataLength,FieldRequired:FieldRequired,DefaultValue:DefaultValue,
                       FieldViewEdit:FieldViewEdit},
		
		success: function(response)
		{                         
                     alert(response);     
                     jQuery("#pharmacy_tbl_grid").hide();     
                     jQuery("#pharmacy_tbl_AllCare").hide();  
                     
                     jQuery("#pharmacy_btnSaveFields").hide();                     
                     jQuery("#pharmacy_btnClear").hide();  
                     jQuery("#pharmacy_btn_Cancel").hide();  
                     jQuery("#pharmacy_tbl_step2").show(); 
                     jQuery("#pharmacy_btnNext").hide();
                     jQuery("#pharmacy_btnBack").show();
                     
		},
		failure: function(response)
		{
			alert("error");
		}		
        });	       
}

function pharmacy_showGroupsRecordsets()
{
    var finalURL='all_extensions/show_groups_recordsets.php';  
    var table_name=jQuery("#pharmacy_rd1to1").is(":checked")?'tbl_allcare_pharmacy1to1_fieldmapping':'tbl_allcare_pharmacy1ton_fieldmapping';    
           
    $.ajax({
                type: 'POST',
                url: finalURL,	
                data: {extension_name:'pharmacy',table_name:table_name},

                //data:relatedTables,
                success: function(response)
                {                         
                     jQuery("#pharmacy_showGroupsRecordsets").html(response);                     
                },
                failure: function(response)
                {
                        alert("error");
                }		
            });	
}

function pharmacy_showFields()
{                         
        var finalURL='all_extensions/show_fields.php';  
        var table_name=jQuery("#pharmacy_rd1to1").is(":checked")?'tbl_allcare_pharmacy1to1':'tbl_allcare_pharmacy1ton';
                
        $.ajax({
                    type: 'POST',
                    url: finalURL,	
                    data: {extension_name:'pharmacy',table_name:table_name},

                    //data:relatedTables,
                    success: function(response)
                    {                         
                         jQuery("#pharmacy_showFields").html(response);                     
                    },
                    failure: function(response)
                    {
                            alert("error");
                    }		
               });		
}

function pharmacy_hideNewButtonData()
{
    jQuery('#pharmacy_lblGroup').hide();  
    jQuery('#pharmacy_lblRecordset').hide();  
    jQuery('#pharmacy_lblExistingFields').hide();  
    jQuery('#pharmacy_txtGroupRecordset').val('');  
    jQuery('#pharmacy_txtGroupRecordset').hide();      
    jQuery('#pharmacy_lblFieldsSelected').hide();   
    jQuery('#pharmacy_showFieldsByGroupRecordsets').html(''); 
    jQuery('#pharmacy_showFields').html(''); 
}

function pharmacy_showFieldsByGroupRecordsets(tableName)
{
        var finalURL='all_extensions/show_fields_by_groups_recordsets.php';  
        //var table_name=jQuery("#pharmacy_rd1to1").is(":checked")?'tbl_allcare_pharmacy1to1_fieldmapping':'tbl_allcare_pharmacy1ton_fieldmapping';
        var table_name=tableName;
        var selectedGR=jQuery("#pharmacy_comboGroupsRecordsets").val();        
        
        $.ajax({
                    type: 'POST',
                    url: finalURL,	
                    data: {extension_name:'pharmacy',table_name:table_name,selectedGR:selectedGR},

                    //data:relatedTables,
                    success: function(response)
                    {                         
                         jQuery("#pharmacy_showFieldsByGroupRecordsets").html(response);                     
                    },
                    failure: function(response)
                    {
                            alert("error");
                    }		
               });		
}

function pharmacy_editGroupRecordset()  
{               
        var finalURL='all_extensions/edit_group_recordset.php';
        
        var TableName=(jQuery('#pharmacy_rd1to1').is(':checked'))?'tbl_allcare_pharmacy1to1_fieldmapping':'tbl_allcare_pharmacy1ton_fieldmapping';    
        
        var GroupRecordsetName=jQuery('#pharmacy_comboGroupsRecordsets').val();
        
        var selectedFields=new Array();   
        
        var showYesNo = jQuery('#rd_show_yes').is(':checked') ? 'Y' : 'N';
        
        var checked_fields=document.getElementsByName("pharmacy_chkSelectedFields");        

        for(i=0;i<checked_fields.length;i++)
        {
            //pharmacy_delList[i].checked = checkboxstatus;//(chkBtn.checked)? true : false;   
            if(checked_fields[i].checked)
            {
                selectedFields.push(checked_fields[i].value);
            }
        }      
        
        selectedFields=selectedFields.toString();
        
        $.ajax({
            type: 'POST',
            url: finalURL,	
            data: {extension_name:'pharmacy',TableName:TableName,GroupRecordsetName:GroupRecordsetName,
                   selectedFields:selectedFields,showYesNo:showYesNo},

            //data:relatedTables,
            success: function(response)
            {                         
                 alert(response);   
                 pharmacy_showGroupsRecordsets();
                 pharmacy_hideNewButtonData();

            },
            failure: function(response)
            {
                    alert("error");
            }		
           });		                       
}

function pharmacy_insert_in_mapping()
{
    var finalURL='all_extensions/insert_in_mapping.php';                  
    
var mappingTableName=(jQuery('#pharmacy_rd1to1').is(':checked'))?'tbl_allcare_pharmacy1to1_fieldmapping':'tbl_allcare_pharmacy1ton_fieldmapping';    
    
    
    var showYesNo = jQuery('#rd_show_yes').is(':checked') ? 'Y' : 'N';
    
    var groupRecordsetName=jQuery('#pharmacy_txtGroupRecordset').val();

    //var selectedFields=jQuery('#comboFields').val();
    var selectedFields=new Array();
    
    var checked_fields=document.getElementsByName("pharmacy_chkAllFields");            

    for(i=0;i<checked_fields.length;i++)
    {
        //pharmacy_delList[i].checked = checkboxstatus;//(chkBtn.checked)? true : false;   
        if(checked_fields[i].checked)
        {
            selectedFields.push(checked_fields[i].value);
        }
    }      
               
    selectedFields=selectedFields.toString();
    
    $.ajax({
            type: 'POST',
            url: finalURL,	
            data: {extension_name:'pharmacy',mappingTableName:mappingTableName,groupRecordsetName:groupRecordsetName,
                   selectedFields:selectedFields,showYesNo:showYesNo},

            //data:relatedTables,
            success: function(response)
            {                         
                 alert(response);   
                 pharmacy_showGroupsRecordsets();
                 pharmacy_hideNewButtonData();

            },
            failure: function(response)
            {
                    alert("error");
            }		
           });		         
}

var pharmacy_checkedAll=false;
function pharmacy_checkAll(checkboxstatus)
{            
    var pharmacy_delList=document.getElementsByName("pharmacy_checkClear");        

    for(var i=0;i<pharmacy_delList.length;i++)
    {
        pharmacy_delList[i].checked = checkboxstatus;//(chkBtn.checked)? true : false;            
    }        
    
    pharmacy_checkedAll=checkboxstatus;
}


function pharmacy_clearSelected()
{
    var pharmacy_delList=document.getElementsByName("pharmacy_checkClear"); 
    var pharmacy_checkedNum=0;
    var pharmacy_allFieldsClear=0;
    
    for(var i=(pharmacy_delList.length-1);i>=0;i--)
    {
        if(pharmacy_delList[i].checked===true)
        {
            pharmacy_checkedNum++;        
            
            jQuery('#pharmacy_related_tables'+i).val('none');
            jQuery('#pharmacy_txtFieldName'+i).val('');
            jQuery('#pharmacy_data_types'+i).val('none');
            jQuery('#pharmacy_txtDataLength'+i).val('');
            /*
            jQuery('#pharmacy_txtFieldName'+i).prop('readonly','');                            
            jQuery('#pharmacy_data_types'+i).prop('disabled','');    
            jQuery('#pharmacy_txtDataLength'+i).prop('readonly','');
            */
            jQuery('#pharmacy_boolFieldRequired'+i).prop('checked', false);            
            jQuery('#pharmacy_txtDefaultValue'+i).val('');
            
            if(jQuery('#pharmacy_img_calendar'+i).css('display')==='inline' || jQuery('#pharmacy_img_calendar'+i).css('display')==='block')
            {
                jQuery('#pharmacy_img_calendar'+i).hide();
            }
            
            jQuery('#pharmacy_view_edit'+i).val('Y');            
            pharmacy_delList[i].checked=false;
            
            for(var j=i;j<(pharmacy_totalFields);j++)
            {
                jQuery('#pharmacy_related_tables'+j).val(jQuery('#pharmacy_related_tables'+(j+1)).val());
                jQuery('#pharmacy_txtFieldName'+j).val(jQuery('#pharmacy_txtFieldName'+(j+1)).val());
                jQuery('#pharmacy_data_types'+j).val(jQuery('#pharmacy_data_types'+(j+1)).val());
                jQuery('#pharmacy_txtDataLength'+j).val(jQuery('#pharmacy_txtDataLength'+(j+1)).val());                
                
                var fn = jQuery('#pharmacy_txtFieldName'+(j+1)).prop('readonly') ? 'readonly' :'';
                jQuery('#pharmacy_txtFieldName'+j).prop('readonly',fn);                            
                
                var dt = jQuery('#pharmacy_data_types'+(j+1)).prop('disabled') ? 'disabled' :'';
                jQuery('#pharmacy_data_types'+j).prop('disabled',dt);                          
                                
                var dl = jQuery('#pharmacy_txtDataLength'+(j+1)).prop('readonly') ? 'readonly' : '';
                jQuery('#pharmacy_txtDataLength'+j).prop('readonly',dl);                                                       
                
                var fr = jQuery('#pharmacy_boolFieldRequired'+(j+1)).is(':checked') ? true : false;
                jQuery('#pharmacy_boolFieldRequired'+j).prop('checked', fr);
                                
                jQuery('#pharmacy_txtDefaultValue'+j).val(jQuery('#pharmacy_txtDefaultValue'+(j+1)).val());
                jQuery('#pharmacy_view_edit'+j).val(jQuery('#pharmacy_view_edit'+(j+1)).val());                                   
                                
                var frReq = jQuery('#pharmacy_boolFieldRequired'+(j+1)).prop('disabled') ? 'disabled' : '';
                jQuery('#pharmacy_boolFieldRequired'+j).prop('disabled',frReq);                                                       
                
                var dv = jQuery('#pharmacy_txtDefaultValue'+(j+1)).prop('disabled') ? 'disabled' : '';
                jQuery('#pharmacy_txtDefaultValue'+j).prop('disabled', dv);
                
                var imgCal = (jQuery('#pharmacy_img_calendar'+(j+1)).css('display')==='inline' || jQuery('#pharmacy_img_calendar'+(j+1)).css('display')==='block') ? 'inline' : 'none';
                
                jQuery('#pharmacy_img_calendar'+j).css('display', imgCal);                
                                                
                jQuery('#pharmacy_related_tables'+(j+1)).val('none');
                jQuery('#pharmacy_txtFieldName'+(j+1)).val('');
                jQuery('#pharmacy_data_types'+(j+1)).val('none');
                jQuery('#pharmacy_txtDataLength'+(j+1)).val('');            

                jQuery('#pharmacy_txtFieldName'+(j+1)).prop('readonly','');                            
                jQuery('#pharmacy_data_types'+(j+1)).prop('disabled','');    
                jQuery('#pharmacy_txtDataLength'+(j+1)).prop('readonly','');

                jQuery('#pharmacy_boolFieldRequired'+(j+1)).prop('checked', false);            
                jQuery('#pharmacy_txtDefaultValue'+(j+1)).val('');
                jQuery('#pharmacy_img_calendar'+(j+1)).hide();             
                jQuery('#pharmacy_boolFieldRequired'+(j+1)).prop('disabled','');    
                jQuery('#pharmacy_txtDefaultValue'+(j+1)).prop('disabled','');    
                
                jQuery('#pharmacy_view_edit'+(j+1)).val('Y');                                      
            }
            
            jQuery('#pharmacy_related_tables'+j).val('none');
            jQuery('#pharmacy_txtFieldName'+j).val('');
            jQuery('#pharmacy_data_types'+j).val('none');
            jQuery('#pharmacy_txtDataLength'+j).val('');            

            jQuery('#pharmacy_txtFieldName'+j).prop('readonly','');                            
            jQuery('#pharmacy_data_types'+j).prop('disabled','');    
            jQuery('#pharmacy_txtDataLength'+j).prop('readonly','');

            jQuery('#pharmacy_boolFieldRequired'+j).prop('checked', false);            
            jQuery('#pharmacy_txtDefaultValue'+j).val('');
            jQuery('#pharmacy_img_calendar'+j).hide();          
            jQuery('#pharmacy_view_edit'+j).val('Y');                
                      
            //if(i===0)
            if(i===0 && pharmacy_totalFields===0)
            {pharmacy_allFieldsClear=1;}
                      
        }
        
        jQuery('#pharmacy_check_All').prop('checked', false);   
    }
    
    //pharmacy_totalFields=0;
    
    pharmacy_totalFields = (pharmacy_allFieldsClear===1 || pharmacy_checkedAll===true) ? 0 : Math.abs(pharmacy_totalFields-pharmacy_checkedNum);
    pharmacy_checkedAll=false;
    //alert('now tf= '+pharmacy_totalFields);
}


</script>
