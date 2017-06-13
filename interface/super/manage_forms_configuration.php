<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

?>

<script type="text/javascript">
function showFormFields(form_val)
{
    jQuery('#div_formFields').html('');
    jQuery('#div_FacetoFace_FormFields').html('');
    
    $.ajax({
            type: 'POST',
            url: 'show_form_fields_for_configuration.php',            

            data: {form_val:form_val},

            success: function(response)
            {
                jQuery('#div_formFields').html(response);
                             
            },
            failure: function(response)
            {
                    alert("error");
            }		
           });	
            
}


function add_form_configuration(form_table_name,mode)
{
    
    
    
}

   
function showConfigByDiagnosis(pri_diagnosis_id)
{ 
    //jQuery('#div_formFields').html('');
    //jQuery('#div_FacetoFace_FormFields').html('');
    $.ajax({
                type: 'POST',
                url: 'face_to_face_config_by_diagnosis.php',                    
                data: {pri_diagnosis_id:pri_diagnosis_id},
                //contentType: 'multipart/form-data',

                success: function(response)
                {
                    jQuery("#div_FacetoFace_FormFields").html(response);
                    //location.reload();

                },
                failure: function(response)
                {
                    alert("Failed");
                }
            });
}        
    
    
function saveFacetoFaceConfig()
{
     $.ajax({
                type: 'POST',
                url: 'save_facetoface_config.php',                    
                data: $("#form_facetoface_configuration").serialize(),
                //contentType: 'multipart/form-data',

                success: function(response)
                {
                    alert(response);                        
                    jQuery('#div_formFields').html('');
                    jQuery('#div_FacetoFace_FormFields').html('');
                    jQuery('#div_LabRequest_FormFields').html('');
                    jQuery('#select_form').val('0');
                },
                failure: function(response)
                {
                    alert("Failed");
                }
            });
}    


function saveLabRequestConfig()
{
    $.ajax({
                type: 'POST',
                url: 'save_lab_requisition_config.php',                    
                data: $("#form_lab_requisition_configuration").serialize(),
                //contentType: 'multipart/form-data',

                success: function(response)
                {
                    alert(response);                        
                    jQuery('#div_formFields').html('');
                    jQuery('#div_FacetoFace_FormFields').html('');
                    jQuery('#div_LabRequest_FormFields').html('');
                    jQuery('#select_form').val('0');
                },
                failure: function(response)
                {
                    alert("Failed");
                }
            });
}


</script>

<div>
    Select the form : 
    <select id="select_form" name="select_form" onchange="showFormFields(this.value)" onload="showFormFields(this.value)">
        <option value="0">--select--</option>
        <option value="1">Face to Face</option>
        <option value="2">Lab Requisition</option>
        
    </select>
    
    <div id="div_formFields">
    </div>
    
    <div id="div_FacetoFace_FormFields">
    </div>
    
    <div id="div_LabRequest_FormFields">
    </div>
    
</div>
