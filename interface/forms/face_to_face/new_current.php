<?php
require_once("../../globals.php");
require_once("$srcdir/acl.inc");
require_once("$srcdir/formdata.inc.php");
require_once("$srcdir/globals.inc.php");
require_once("$srcdir/user.inc");
require_once("$srcdir/classes/CouchDB.class.php");

?>
<!DOCTYPE html>
<html lang="en">
<head>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8"> 
        <title>Face-to-Face Encounter</title>
                
<link rel=stylesheet href="../themes/style_oemr.css" type="text/css">
        
<link rel="stylesheet" href="../../main/css/bootstrap-3.0.3.min.css" type="text/css">
<script type="text/javascript" src="../../main/js/jquery-2.0.3.min.js"></script>
<script type="text/javascript" src="../../main/js/bootstrap-3.0.3.min.js"></script>

<style type="text/css">@import url(../../../library/dynarch_calendar.css);</style>
<script type="text/javascript" src="../../../library/dialog.js"></script>
<script type="text/javascript" src="../../../library/textformat.js"></script>
<script type="text/javascript" src="../../../library/dynarch_calendar.js"></script>
<script type="text/javascript" src="../../../library/dynarch_calendar_en.js"></script>
<script type="text/javascript" src="../../../library/dynarch_calendar_setup.js"></script>

<script type='text/javascript'>
    var radios='';
</script>

<script>
    
    function showConfigByNote(note_id)
    {alert('n='+note_id);
        $.ajax({
                    type: 'POST',
                    url: 'face_to_face_form.php',                    
                    data: {note_id:note_id},
                    //contentType: 'multipart/form-data',

                    success: function(response)
                    {
                        jQuery("#div_form_facetoface").html(response);
                        //location.reload();
                        
                    },
                    failure: function(response)
                    {
                        alert("Failed");
                    }
                });
    }
    
</script>


</head>
<!-- onload="showConfigByNote(jQuery('#sel_notes').val());" -->

<?php
$mode=0;

if(strpos($_SERVER["HTTP_REFERER"],'/interface/patient_file/encounter/load_form.php'))
{
    $mode=0;
}
//else if(strpos($_SERVER["HTTP_REFERER"],'/interface/patient_file/encounter/view_form.php'))
else if(strpos($_SERVER["HTTP_REFERER"],'/interface/patient_file/encounter/forms.php') && $_REQUEST['id']!='')
{
    $mode=1;
        
    $getEncounterDetails=sqlStatement("SELECT * FROM tbl_form_facetoface 
                                       WHERE id=".$_REQUEST['id']." 
                                         AND pid=".$GLOBALS['pid']." 
                                         AND encounter=".$GLOBALS['encounter']."");
    $resEncounterDetails=  sqlFetchArray($getEncounterDetails);
    
}
?>

<body onload="showConfigByNote(jQuery('#sel_notes').val());">
<form name="frmActionPlan" id="frmActionPlan" method="POST" role="form">
<div class="container">
  <div class="form-group">
    <div class="row" style="text-align: center;">
        <h4>Face-to-Face Encounter</h4>
    </div>
    
	<div class="row">
        <b>Note</b>
        
        <?php 
			//echo "SELECT option_id,title FROM list_options WHERE list_id='FaceToFace_Configuration_Notes'";
		
            $getNotes=sqlStatement("SELECT option_id,title FROM list_options WHERE list_id='FaceToFace_Configuration_Notes'");
            
			//print_r($resNotes);
        ?>        
        <select id="sel_notes" name="sel_notes" onchange='showConfigByNote(this.value)'>
            <?php 

            echo '<option value="">Select Note</option>';	
            while($resNotes=  sqlFetchArray($getNotes))
            {
                    if($resNotes['option_id']==$resEncounterDetails['note_id'])
                    {
                            $selected='selected';
                    }
                    else
                    {
                            $selected='';
                    }
                    echo '<option value='.$resNotes['option_id'].' '.$selected.'>'.$resNotes['title'].'</option>';
            }
            ?>
			
			
	</select>
    </div>
	
      <div id="div_form_facetoface"></div>  
      
    </div>
        
</div>
    <input type="hidden" name="texens" id="texens" value="" >
</form>
</body>
</html>


