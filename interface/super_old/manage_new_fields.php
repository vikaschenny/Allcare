
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
                    <td valign='top'><input id='chk1to1' type='checkbox' value='1' name='chk1to1'></td>
                </tr>
                <tr title='1 to n' border='1'>
                    <td valign='top' colspan='2'><b>AllCare_Patients1ton </b></td>
                    <td valign='top'><input id='chk1ton' type='checkbox' value='1' name='chk1ton'></td>
                </tr>
            </table>
            <input type='button' id='btnNext' name='btnNext' value='Go to POS selection' style='float: right; margin-top: -35px;'
                   onclick='javascript: jQuery(\"#tbl_AllCare_Patients\").hide();
                                        jQuery(\"#tbl_grid\").hide();
                                        jQuery(\"#tbl_step2\").show();
                                        jQuery(\"#btnNext\").hide();
                                        jQuery(\"#btnBack\").show();
                                        jQuery(\"#btnSaveFields\").hide();                     
                                        jQuery(\"#btnClear\").hide();  
                                        jQuery(\"#btn_Cancel\").hide();  ' />
            <input type='button' id='btnBack' name='btnBack' value='Back' style='display:none' 
                   onclick='javascript: jQuery(\"#tbl_AllCare_Patients\").show();
                                        jQuery(\"#tbl_grid\").show();
                                        jQuery(\"#tbl_step2\").hide();
                                        jQuery(\"#btnBack\").hide();
                                        jQuery(\"#btnNext\").show();
                                        jQuery(\"#btnSaveFields\").show();                     
                                        jQuery(\"#btnClear\").show();  
                                        jQuery(\"#btn_Cancel\").show();' />
            <br>
            <table border='1' id='tbl_grid' name='tbl_grid'>                
                <tr>
                    <th><input type='checkbox' id='check_All' name='check_All' onclick='checkAll(this.checked);' /></th>
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
                    <td align='center'><input type='checkbox' id='checkClear".$cnt."' name='checkClear' /></td>";
                                           
      echo "<td><select id='related_tables".$cnt."' name='related_tables' onchange='javascript:return newEvt(this.id);' style='width:125px'>          
            <option value='none'>none</option>
                $table_list
            </select></td>
            <td><input type='text' id='txtFieldName".$cnt."' name='txtFieldName' style='width:125px' /></td>";

            //echo "<td><input type='text' id='txtFieldType".$cnt."' name='txtFieldType[]' style='width:125px' /></td>";
            
            echo "<td>
                    <select id='data_types".$cnt."' name='data_types' onchange='checkDataType();'>
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
                                              
            echo "<td><input type='text' id='txtDataLength".$cnt."' name='txtDataLength' style='width:125px' /></td>
            <td align='center'>
                <input type='checkbox' id='boolFieldRequired".$cnt."' name='boolFieldRequired' />
            </td>
            <td>
                <!--<input type='text' id='txtDefaultValue".$cnt."' name='txtDefaultValue' style='width:125px' />-->
                <input type='text' size='10' name='txtDefaultValue' id='txtDefaultValue".$cnt."' onkeyup='datekeyup(this,mypcc)' onblur='dateblur(this,mypcc)' title='yyyy-mm-dd last date of this event' />
<img src='../../interface/pic/show_calendar.gif' align='absbottom' width='24' height='22'
id='img_calendar".$cnt."' border='0' alt='[?]' style='cursor:pointer;display:none;'
title='Click here to choose a date'>
<script>
Calendar.setup({inputField:'txtDefaultValue".$cnt."', ifFormat:'%Y-%m-%d', button:'img_calendar".$cnt."'});
</script>
            
            </td>
                           
            
            <td>
                <select id='view_edit".$cnt."' name='view_edit' style='width:100px'>
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

    <table id="tbl_step2" name="tbl_step2" style="display:none;">
        <tr>
            <td>POS Types</td>
            <td>
                <select id='lstpostype' name='lstpostype' onchange="javascript:jQuery('#rd1to1').attr('checked',false);
                                                                               jQuery('#rd1ton').attr('checked',false);
                                                                               jQuery('#lblExistingGroups').hide();   
                                                                               jQuery('#lblExistingRecordsets').hide();  
                                                                               jQuery('#showGroupsRecordsets').html('');
                                                                               hideNewButtonData();">
                <?php echo $postypeoptionvalue;?>
                </select>
            </td>
        </tr>
        <tr><td></td></tr>
        <tr> <td></td>               
            <td align="left"><input type="radio" id="rd1to1" name="radallcare" value="1to1"
                       onchange="javascript:jQuery('#lblExistingRecordsets').hide();
                                            jQuery('#lblExistingGroups').show();  
                                            showGroupsRecordsets();
                                            hideNewButtonData();
                                            ">
            AllCare_Patients1to1</td>
        </tr>
        <tr>   <td></td>             
            <td align="left">
                <input type="radio" id="rd1ton" name="radallcare" value="1ton"
                       onchange="javascript:jQuery('#lblExistingRecordsets').show();
                                            jQuery('#lblExistingGroups').hide();  
                                            showGroupsRecordsets();
                                            hideNewButtonData();                                                 
                                            " />
            AllCare_Patients1ton</td>
        </tr>  
        <tr><td></td></tr>
        <tr>
            <td style="width:150px"><label id='lblExistingGroups' style='display:none;'>Existing Groups </label>
                <label id='lblExistingRecordsets' style='display:none;'>Existing Recordsets</label>
            </td>                                
            <td>                    
                <div id="showGroupsRecordsets" name="showGroupsRecordsets"></div>
            </td>
        </tr>
        <tr>
            <td>
                <label id='lblFieldsSelected' style='display:none;'>Field Selected : </label>
            </td>
            <td>
                <div id="showFieldsByGroupRecordsets" name="showFieldsByGroupRecordsets"></div>
            </td>
        </tr>
        <tr>
            <td><label id='lblGroup' style='display:none;'>Group Name</label>
                <label id='lblRecordset' style='display:none;'>Recordset</label>
            </td>
            <td>
                <input type="text" id='txtGroupRecordset' name="txtGroupRecordset" value="" style='display:none;'>
            </td>
        </tr>
        <tr>     
            <td><label id='lblExistingFields' style='display:none;'>Fields : </label></td>           
            <td>
                <div id="showFields" name="showFields" style='border:1px'>
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
                <input type='button' id='btnSave' name='btnSave' value='Save' 
                       onclick='javascript: 
                                            if(jQuery("#comboGroupsRecordsets").val()!=="none")    
                                            {
                                                editGroupRecordset();
                                            }
                                            if(jQuery("#txtGroupRecordset").val()!=="")
                                            {
                                                if(validateStep2())
                                                {
                                                    insert_in_mapping();
                                                }
                                            }' />
                <input type='button' id='btnCancel' name='btnCancel' value='Cancel'
                       onclick='javascript:location.href="edit_globals.php";'/>
            </td>
        </tr>
     </table>

    <br>
        <center>                   
            <input type='button' id='btnSaveFields' name='btnSaveFields' value='Save Fields' 
                   onclick='javascript:if(validateStep1())
                                       {
                                            if(!checkIfFieldAlreadyExists() && validateDataTypes() && validateDefaultValues())
                                            {   
                                                insertFields();
                                            }                                            
                                       }' />                                
            <input type='button' id='btnClear' name='btnClear' value='Clear' onclick='javascript:clearSelected();' />
            <input type='button' id='btn_Cancel' name='btn_Cancel' value='Cancel' 
                   onclick='javascript:location.href="edit_globals.php";' />
        </center>
    

<script type='text/javascript'>
    
function newEvt(selectedBox)
{
     if(document.getElementById(selectedBox).value!=='none')
     {
  dlgopen('get_table_fields.php?table_name='+document.getElementById(selectedBox).value+'&selectedBox='+selectedBox,
   '_blank', 550, 270);
     }
  return false;
}


var totalFields=0;

function receivedFromChild(selectedBox,tableName,checkedFields,checkedFieldsType)
{
    var SBNum=0;
    var s=selectedBox.split("related_tables"); 
    SBNum=s[1];
    //alert('SBNum = '+SBNum);    
    //alert('length = '+checkedFields.length);  
    //alert('totalFields = '+totalFields);  
    var split0='',split1='',split2='';        
                
    jQuery('#related_tables'+SBNum).val(tableName);
    jQuery('#txtFieldName'+SBNum).val(checkedFields[0]);
    
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
    
    jQuery('#data_types'+SBNum).val(split1);    
    jQuery('#txtDataLength'+SBNum).val(split2[0]);
        
    if(jQuery('#related_tables'+SBNum).val()!=='none')
    {
        jQuery('#txtFieldName'+SBNum).attr('readonly','readonly');                            
        jQuery('#data_types'+SBNum).attr('disabled','disabled');    
        jQuery('#txtDataLength'+SBNum).attr('readonly','readonly');
    }     
    
    if(jQuery('#data_types'+SBNum).val()==='text' || jQuery('#data_types'+SBNum).val()==='tinytext' || jQuery('#data_types'+SBNum).val()==='mediumtext' ||
               jQuery('#data_types'+SBNum).val()==='longtext' || jQuery('#data_types'+SBNum).val()==='year' ||
               jQuery('#data_types'+SBNum).val()==='blob' || jQuery('#data_types'+SBNum).val()==='tinyblob' ||
               jQuery('#data_types'+SBNum).val()==='mediumblob' || jQuery('#data_types'+SBNum).val()==='longblob')
    {
            jQuery('#boolFieldRequired'+SBNum).attr('disabled','disabled');
            jQuery('#txtDefaultValue'+SBNum).attr('disabled','disabled');
    }
    
    (jQuery('#data_types'+SBNum).val()==='date' || 
     jQuery('#data_types'+SBNum).val()==='datetime') ? jQuery('#img_calendar'+SBNum).show() : jQuery('#img_calendar'+SBNum).hide();
            
                        
    if(SBNum>=totalFields)   
    {
        totalFields++;
    }

    for(var i=totalFields;i<(totalFields+checkedFields.length-1);i++)
    {
        jQuery('#related_tables'+i).val(tableName);
        jQuery('#txtFieldName'+i).val(checkedFields[i-totalFields+1]);
        
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
        
        jQuery('#data_types'+i).val(split1);    
        jQuery('#txtDataLength'+i).val(split2[0]);
                
        if(jQuery('#related_tables'+i).val()!=='none')
        {
            jQuery('#txtFieldName'+i).attr('readonly','readonly');                            
            jQuery('#data_types'+i).attr('disabled','disabled');    
            jQuery('#txtDataLength'+i).attr('readonly','readonly');
        }     
        
        if(jQuery('#data_types'+i).val()==='text' || jQuery('#data_types'+i).val()==='tinytext' || jQuery('#data_types'+i).val()==='mediumtext' ||
               jQuery('#data_types'+i).val()==='longtext' || jQuery('#data_types'+i).val()==='year' ||
               jQuery('#data_types'+i).val()==='blob' || jQuery('#data_types'+i).val()==='tinyblob' ||
               jQuery('#data_types'+i).val()==='mediumblob' || jQuery('#data_types'+i).val()==='longblob')
        {
                jQuery('#boolFieldRequired'+i).attr('disabled','disabled');
                jQuery('#txtDefaultValue'+i).attr('disabled','disabled');
        }
         
        if(jQuery('#data_types'+i).val()==='date' || jQuery('#data_types'+i).val()==='datetime')
        {            
                jQuery('#img_calendar'+i).show();
        }    
                  
    }

    totalFields=(SBNum>=totalFields) ? (totalFields+checkedFields.length-1) : (totalFields+checkedFields.length);  
    totalFields--;
    
}
  
function unselectTable(selectedBox)
{    
    jQuery('#'+selectedBox).val('none');    
}
         	
function addRow(numRow,tableName,checkFieldName,checkFieldType,checkFieldSize)
{	          
        for(var i=0;i<numRow;i++)
        {
            jQuery('#related_tables'+numRow).val(tableName);
            jQuery('#txtFieldName'+numRow).val(checkFieldName);
            //jQuery('#txtFieldType'+numRow).val(checkFieldType);
            jQuery('#txtDataLength'+numRow).val(checkFieldSize);
            jQuery('#data_types'+numRow).val(checkFieldType);
            //jQuery('#txtDefaultValue'+numRow).val(tableName);                                
        }
}

function skip_none(array_name)
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

function skip_blank(array_name)
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

function validateStep1()
{
    if(!(jQuery('#chk1to1').is(':checked')) && !(jQuery('#chk1ton').is(':checked')))
    {
        alert("Please check the table AllCare_Patients1to1 or AllCare_Patients1ton");
        return false;
    }
        
    var relTables=document.getElementsByName("related_tables");    
    //var fieldsEntered=document.getElementsByName("related_tables");    
    
    var flag=0;
    for(var j=0;j<relTables.length;j++)
    {
        if(relTables[j].value==='none' && jQuery('#txtFieldName'+j).val()==='')
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
    
    
    var fieldName=document.getElementsByName("txtFieldName"); 
    for(var k=0;k<fieldName.length;k++)
    {                        
        if(/\s/.test(jQuery('#txtFieldName'+k).val()))
        {
            alert("Remove space in the Field name '"+jQuery('#txtFieldName'+k).val()+"'");
            return false;        
        }        
    }
        
    return true;    
}

function validateStep2()
{
    if(!(jQuery('#rd1to1').is(':checked')) && !(jQuery('#rd1ton').is(':checked')))
    {
        alert("Please check the table AllCare_Patients1to1 or AllCare_Patients1ton");
        return false;
    }
    
    /*
    else if(jQuery('#txtGroupRecordset').val()==='')
    {
        alert("Please enter the Group/Recordset name");
        return false;
    }
    
    else if(!jQuery('#comboFields').val())
    {
        alert("Please select the field/s");
        return false;
    }*/
        
    var checked_fields=document.getElementsByName("chkAllFields");        
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
            
    return true;
    
}

function checkIfFieldAlreadyExists()
{    
    var FieldName='';

    var chk1to1=(jQuery('#chk1to1').is(':checked'))?1:0;
    var chk1ton=(jQuery('#chk1ton').is(':checked'))?1:0;
    
    for(var i=0;i<20;i++)
    {            
        if(jQuery('#txtFieldName'+i).val()!=='')
        {
            FieldName=jQuery('#txtFieldName'+i).val();    
            var exists;
            $.ajax({
                type: 'POST',
                url: 'check_if_field_already_exists.php',	
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

function validateDataTypes()
{
    for(var i=0;i<20;i++)
    {                    
        if(jQuery('#txtFieldName'+i).val()!=='' && jQuery('#data_types'+i).val()!=='')
        {
            // datalength is NOT mandatory for the following data types , 
            // eg. if the data type is 'date' then don't validate its empty datalength.
            //     else if data type is 'int' then validate its empty datalength
            if(jQuery('#data_types'+i).val()!=='date' && jQuery('#data_types'+i).val()!=='datetime' &&
               jQuery('#data_types'+i).val()!=='time' && jQuery('#data_types'+i).val()!=='timestamp' &&
               jQuery('#data_types'+i).val()!=='text' && jQuery('#data_types'+i).val()!=='tinytext' && 
               jQuery('#data_types'+i).val()!=='mediumtext' && jQuery('#data_types'+i).val()!=='longtext' && 
               jQuery('#data_types'+i).val()!=='blob' && jQuery('#data_types'+i).val()!=='tinyblob' &&
               jQuery('#data_types'+i).val()!=='mediumblob' && jQuery('#data_types'+i).val()!=='longblob' &&                        
               jQuery('#data_types'+i).val()!=='geometry' && jQuery('#data_types'+i).val()!=='point' &&
               jQuery('#data_types'+i).val()!=='linestring' && jQuery('#data_types'+i).val()!=='polygon' &&  
               jQuery('#data_types'+i).val()!=='multipoint' && jQuery('#data_types'+i).val()!=='multilinestring' &&    
               jQuery('#data_types'+i).val()!=='multipolygon' && jQuery('#data_types'+i).val()!=='geometrycollection')
            {
                if(jQuery('#txtDataLength'+i).val()==='') //  isNaN(jQuery('#txtDataLength'+i).val())
                {
                    alert('Please enter a valid numerical Data Length for the field '+jQuery('#txtFieldName'+i).val());
                    jQuery('#txtDataLength'+i).focus();
                    return false;                            
                }
            }
        }                        
    }
    return true;
}

function validateDefaultValues()
{        
    for(var i=0;i<20;i++)
    {            
        if(jQuery('#txtFieldName'+i).val()!=='')
        {
            var FieldName=jQuery('#txtFieldName'+i).val();               
            var dataType=jQuery('#data_types'+i).val();
            var defaultValue=jQuery('#txtDefaultValue'+i).val();
            
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
            
            if(jQuery('#boolFieldRequired'+i).is(':checked') && defaultValue===''
                && (jQuery('#data_types'+i).val()!=='text' && jQuery('#data_types'+i).val()!=='tinytext' && 
                    jQuery('#data_types'+i).val()!=='mediumtext' && jQuery('#data_types'+i).val()!=='longtext' &&
                    jQuery('#data_types'+i).val()!=='blob' && jQuery('#data_types'+i).val()!=='tinyblob' &&
                    jQuery('#data_types'+i).val()!=='mediumblob' && jQuery('#data_types'+i).val()!=='longblob'))                                        
            {
                alert('Please enter the Default value for the field '+FieldName+' checked as Required');
                return false;
            }                                    
        }
    }
    return true;
}

function checkDataType()
{
    for(var i=0;i<20;i++)
    { 
        if(jQuery('#data_types'+i).val()==='text' || jQuery('#data_types'+i).val()==='tinytext' || 
           jQuery('#data_types'+i).val()==='mediumtext' || jQuery('#data_types'+i).val()==='longtext' || 
           jQuery('#data_types'+i).val()==='blob' || jQuery('#data_types'+i).val()==='tinyblob' ||
           jQuery('#data_types'+i).val()==='mediumblob' || jQuery('#data_types'+i).val()==='longblob')
        {
                jQuery('#boolFieldRequired'+i).attr('disabled','disabled');
                jQuery('#txtDefaultValue'+i).attr('disabled','disabled');
        }
        
        if(jQuery('#data_types'+i).val()==='date' || jQuery('#data_types'+i).val()==='datetime')
        {
            jQuery('#img_calendar'+i).show(); 
            jQuery('#txtDataLength'+i).attr('readonly','readonly');
        }
        else
        {
            jQuery('#img_calendar'+i).hide(); 
            jQuery('#txtDataLength'+i).attr('readonly','');
        }
    }
}

function insertFields()
{
        var finalURL='add_new_fields.php';  
        
        var chk1to1=(jQuery('#chk1to1').is(':checked'))?1:0;
        var chk1ton=(jQuery('#chk1ton').is(':checked'))?1:0;
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
            relatedTables[i]=jQuery('#related_tables'+i).val();
            //relatedTables.push(jQuery('#related_tables'+i).val());
            FieldName[i]=jQuery('#txtFieldName'+i).val();                                    
            FieldType[i]=jQuery('#data_types'+i).val();
            DataLength[i]=jQuery('#txtDataLength'+i).val();
            FieldRequired[i]=(jQuery('#boolFieldRequired'+i).is(':checked'))?1:0;
            DefaultValue[i]=jQuery('#txtDefaultValue'+i).val();            
            FieldViewEdit[i]=jQuery('#view_edit'+i).val();                                    	            
        }                       
                
        relatedTables=skip_none(relatedTables);                
        relatedTables=relatedTables.toString();
        //alert("rt= "+relatedTables);
        
        FieldName=skip_blank(FieldName);
        FieldName=FieldName.toString();
        
        FieldType=skip_none(FieldType);
        FieldType=FieldType.toString();
        //alert("DL = "+DataLength);
        for(var i=0;i<20;i++)
        {
            if(DataLength[i]==='')
            {
               DataLength[i]=0;         
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
		data: {chk1to1:chk1to1,chk1ton:chk1ton,relatedTables:relatedTables,FieldName:FieldName,
                       FieldType:FieldType,DataLength:DataLength,FieldRequired:FieldRequired,DefaultValue:DefaultValue,
                       FieldViewEdit:FieldViewEdit},
		
		success: function(response)
		{                         
                     alert(response);     
                     jQuery("#tbl_grid").hide();     
                     jQuery("#tbl_AllCare_Patients").hide();  
                     
                     jQuery("#btnSaveFields").hide();                     
                     jQuery("#btnClear").hide();  
                     jQuery("#btn_Cancel").hide();  
                     jQuery("#tbl_step2").show(); 
                     jQuery("#btnNext").hide();
                     jQuery("#btnBack").show();
                     
		},
		failure: function(response)
		{
			alert("error");
		}		
        });	       
}

function showGroupsRecordsets()
{
    var finalURL='show_groups_recordsets.php';  
    var table_name=jQuery("#rd1to1").is(":checked")?'tbl_allcare_patients1to1_fieldmapping':'tbl_allcare_patients1ton_fieldmapping';
    var pos_id=jQuery("#lstpostype").val();
           
    $.ajax({
                type: 'POST',
                url: finalURL,	
                data: {table_name:table_name,pos_id:pos_id},

                //data:relatedTables,
                success: function(response)
                {                         
                     jQuery("#showGroupsRecordsets").html(response);                     
                },
                failure: function(response)
                {
                        alert("error");
                }		
            });	
}

function showFields()
{                         
        var finalURL='show_fields.php';  
        var table_name=jQuery("#rd1to1").is(":checked")?'tbl_allcare_patients1to1':'tbl_allcare_patients1ton';
                
        $.ajax({
                    type: 'POST',
                    url: finalURL,	
                    data: {table_name:table_name},

                    //data:relatedTables,
                    success: function(response)
                    {                         
                         jQuery("#showFields").html(response);                     
                    },
                    failure: function(response)
                    {
                            alert("error");
                    }		
               });		
}

function hideNewButtonData()
{
    jQuery('#lblGroup').hide();  
    jQuery('#lblRecordset').hide();  
    jQuery('#lblExistingFields').hide();  
    jQuery('#txtGroupRecordset').val('');  
    jQuery('#txtGroupRecordset').hide();      
    jQuery('#lblFieldsSelected').hide();   
    jQuery('#showFieldsByGroupRecordsets').html(''); 
    jQuery('#showFields').html(''); 
}

function showFieldsByGroupRecordsets(tableName)
{
        var finalURL='show_fields_by_groups_recordsets.php';  
        //var table_name=jQuery("#rd1to1").is(":checked")?'tbl_allcare_patients1to1_fieldmapping':'tbl_allcare_patients1ton_fieldmapping';
        var table_name=tableName;
        var selectedGR=jQuery("#comboGroupsRecordsets").val();
        var pos_id=jQuery("#lstpostype").val();
        
        $.ajax({
                    type: 'POST',
                    url: finalURL,	
                    data: {pos_id:pos_id,table_name:table_name,selectedGR:selectedGR},

                    //data:relatedTables,
                    success: function(response)
                    {                         
                         jQuery("#showFieldsByGroupRecordsets").html(response);                     
                    },
                    failure: function(response)
                    {
                            alert("error");
                    }		
               });		
}

function editGroupRecordset()
{ 
              
        var finalURL='edit_group_recordset.php';
        var pos_id=jQuery("#lstpostype").val();
        var TableName=(jQuery('#rd1to1').is(':checked'))?'tbl_allcare_patients1to1_fieldmapping':'tbl_allcare_patients1ton_fieldmapping';    
        
        var GroupRecordsetName=jQuery('#comboGroupsRecordsets').val();
        
        var selectedFields=new Array();   
        
        var checked_fields=document.getElementsByName("chkSelectedFields");        

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
            data: {pos_id:pos_id,TableName:TableName,GroupRecordsetName:GroupRecordsetName,
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

function insert_in_mapping()
{
    var finalURL='insert_in_mapping.php';  
                
    var posType=jQuery('#lstpostype').val();
    
var mappingTableName=(jQuery('#rd1to1').is(':checked'))?'tbl_allcare_patients1to1_fieldmapping':'tbl_allcare_patients1ton_fieldmapping';    
    
    var groupRecordsetName=jQuery('#txtGroupRecordset').val();

    //var selectedFields=jQuery('#comboFields').val();
    var selectedFields=new Array();
    
    var checked_fields=document.getElementsByName("chkAllFields");            

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
            data: {posType:posType,mappingTableName:mappingTableName,groupRecordsetName:groupRecordsetName,
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

function checkAll(checkboxstatus)
{            
    var delList=document.getElementsByName("checkClear");        

    for(var i=0;i<delList.length;i++)
    {
        delList[i].checked = checkboxstatus;//(chkBtn.checked)? true : false;            
    }        
}

function clearSelected()
{    
    var delList=document.getElementsByName("checkClear"); 
    var checkedNum=0;
    var allFieldsClear=0;
    /*
//    for(var i=0;i<delList.length;i++)
//    {
//        if(delList[i].checked===true)
//        {
//            jQuery('#related_tables'+i).val('none');
//            jQuery('#txtFieldName'+i).val('');
//            jQuery('#data_types'+i).val('none');
//            jQuery('#txtDataLength'+i).val('');
//
//            jQuery('#txtFieldName'+i).attr('readonly','');                            
//            jQuery('#data_types'+i).attr('disabled','');    
//            jQuery('#txtDataLength'+i).attr('readonly','');

            jQuery('#boolFieldRequired'+i).attr('checked', false);            
            jQuery('#txtDefaultValue'+i).val('');
            jQuery('#view_edit'+i).val('Y');            
            delList[i].checked=false;
        }
        jQuery('#check_All').attr('checked', false);   
    }  
    */
            
    for(var i=(delList.length-1);i>=0;i--)
    {
        if(delList[i].checked===true)
        {
            checkedNum++;        
            
            jQuery('#related_tables'+i).val('none');
            jQuery('#txtFieldName'+i).val('');
            jQuery('#data_types'+i).val('none');
            jQuery('#txtDataLength'+i).val('');
            /*
            jQuery('#txtFieldName'+i).attr('readonly','');                            
            jQuery('#data_types'+i).attr('disabled','');    
            jQuery('#txtDataLength'+i).attr('readonly','');
            */
            jQuery('#boolFieldRequired'+i).attr('checked', false);    
            jQuery('#boolFieldRequired'+i).attr('disabled','');                                                       
            jQuery('#txtDefaultValue'+i).val('');
            jQuery('#txtDefaultValue'+i).attr('disabled','');    
            
            if(jQuery('#img_calendar'+i).css('display')==='inline' || jQuery('#img_calendar'+i).css('display')==='block')
            {
                jQuery('#img_calendar'+i).hide();
            }
            
            jQuery('#view_edit'+i).val('Y');            
            delList[i].checked=false;
            
            for(var j=i;j<(totalFields);j++)
            {
                jQuery('#related_tables'+j).val(jQuery('#related_tables'+(j+1)).val());
                jQuery('#txtFieldName'+j).val(jQuery('#txtFieldName'+(j+1)).val());
                jQuery('#data_types'+j).val(jQuery('#data_types'+(j+1)).val());
                jQuery('#txtDataLength'+j).val(jQuery('#txtDataLength'+(j+1)).val());                
                
                var fn = jQuery('#txtFieldName'+(j+1)).attr('readonly') ? 'readonly' :'';
                jQuery('#txtFieldName'+j).attr('readonly',fn);                            
                
                var dt = jQuery('#data_types'+(j+1)).attr('disabled') ? 'disabled' :'';
                jQuery('#data_types'+j).attr('disabled',dt);                          
                                
                var dl = jQuery('#txtDataLength'+(j+1)).attr('readonly') ? 'readonly' : '';
                jQuery('#txtDataLength'+j).attr('readonly',dl);                                                       
                
                var fr = jQuery('#boolFieldRequired'+(j+1)).is(':checked') ? true : false;
                jQuery('#boolFieldRequired'+j).attr('checked', fr);
                                
                jQuery('#txtDefaultValue'+j).val(jQuery('#txtDefaultValue'+(j+1)).val());
                jQuery('#view_edit'+j).val(jQuery('#view_edit'+(j+1)).val());                                   
                                
                var frReq = jQuery('#boolFieldRequired'+(j+1)).attr('disabled') ? 'disabled' : '';
                jQuery('#boolFieldRequired'+j).attr('disabled',frReq);                                                       
                
                var dv = jQuery('#txtDefaultValue'+(j+1)).attr('disabled') ? 'disabled' : '';
                jQuery('#txtDefaultValue'+j).attr('disabled', dv);
                
                var imgCal = (jQuery('#img_calendar'+(j+1)).css('display')==='inline' || jQuery('#img_calendar'+(j+1)).css('display')==='block') ? 'inline' : 'none';
                
                jQuery('#img_calendar'+j).css('display', imgCal);                
                                                
                jQuery('#related_tables'+(j+1)).val('none');
                jQuery('#txtFieldName'+(j+1)).val('');
                jQuery('#data_types'+(j+1)).val('none');
                jQuery('#txtDataLength'+(j+1)).val('');            

                jQuery('#txtFieldName'+(j+1)).attr('readonly','');                            
                jQuery('#data_types'+(j+1)).attr('disabled','');    
                jQuery('#txtDataLength'+(j+1)).attr('readonly','');

                jQuery('#boolFieldRequired'+(j+1)).attr('checked', false);            
                jQuery('#txtDefaultValue'+(j+1)).val('');
                jQuery('#img_calendar'+(j+1)).hide();             
                jQuery('#boolFieldRequired'+(j+1)).attr('disabled','');    
                jQuery('#txtDefaultValue'+(j+1)).attr('disabled','');    
                
                jQuery('#view_edit'+(j+1)).val('Y');                                      
            } 
            
            jQuery('#related_tables'+j).val('none');
            jQuery('#txtFieldName'+j).val('');
            jQuery('#data_types'+j).val('none');
            jQuery('#txtDataLength'+j).val('');            

            jQuery('#txtFieldName'+j).attr('readonly','');                            
            jQuery('#data_types'+j).attr('disabled','');    
            jQuery('#txtDataLength'+j).attr('readonly','');

            jQuery('#boolFieldRequired'+j).attr('checked', false);     
            jQuery('#boolFieldRequired'+j).attr('disabled','');  
            jQuery('#txtDefaultValue'+j).val('');
            jQuery('#txtDefaultValue'+j).attr('disabled','');    
            jQuery('#img_calendar'+j).hide();          
            jQuery('#view_edit'+j).val('Y');                
                      
            if(i===0)
            {allFieldsClear=1;}
                      
        }
        
        jQuery('#check_All').attr('checked', false);   
    }  
    
    //totalFields=0;
    
    totalFields = (allFieldsClear===1) ? 0 : Math.abs(totalFields-checkedNum);
        
    //alert('now tf= '+totalFields);
}

</script>

