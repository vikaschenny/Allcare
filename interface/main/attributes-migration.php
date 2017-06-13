<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once("../globals.php");
require_once("../../library/formdata.inc.php");
require_once("../../library/globals.inc.php");

?>
<script src="jquery-latest.min.js" type="text/javascript"></script>
<h1>Attribute Migration</h1>
<select id='selectMigration' class="btn btn-default" style="text-align:left;">
    <option value='0'>---Select---</option>
    <option value="1">Deceased Status</option>
    <option value="3">Insurance ID</option>
    <option value="4">Kareo Text</option>
    <option value="5">Patient Street Address</option>
    <option value="6">Resolve Encounter DOS mismatch</option>
</select>   
<input type="button" onclick="javascript:attrMigration();" value="Update">
<div class="divSelect">
    <span id="calmsg"></span>
</div> 
<!--
<h1>Patients Based On Fee Sheet codes</h1>
MYSQL CLAUSE: <select id="clause"><option value="1">BETWEEN</option><option value="2">IN</option></select>
<div id="clauseBetween">CPT Code From: <input type="text" id="from" name="from" value=""><br />
CPT Code To: <input type="text" id="to" name="to" value=""><br />
</div>
<div id="clauseIn">CPT Codes: <input type="text" id="incodes" name="incodes" value=""><i>Ex: 99301,99302</i></div>
Practice: <select id="practice"><option value="0">---Select---</option><option value="YES">YES</option><option value="NO">NO</option></select><br />
<input type="button" onclick="javascript:attr2Migration();" value="Update">
-->
<script type='text/javascript'>
function attrMigration(){
    var selectMigration=jQuery('#selectMigration').val(); 
    $.ajax({
            type: 'POST',
            url: "process-migration.php",	
            data: {                       
                    attrmigrate:selectMigration
                  },

            success: function(response)
            {

                jQuery('#calmsg').html(response);
            },
            failure: function(response)
            {
                    alert("error");
            }		
    });	 
}
function attr2Migration(){
    var from=jQuery('#from').val(); 
    var to=jQuery('#to').val(); 
    var practice=jQuery('#practice').val(); 
    var incodes=jQuery('#incodes').val(); 
    $.ajax({
            type: 'POST',
            url: "process-migration.php",	
            data: {                       
                    from:from,
                    to:to,
                    practice:practice,
                    incodes:incodes
                  },

            success: function(response)
            {

                jQuery('#calmsg').html(response);
            },
            failure: function(response)
            {
                    alert("error");
            }		
    });	 
}
$('#clauseBetween').show();
$('#clauseIn').hide();
$('#clause').on('change', function() {
  if(this.value == 1){
      $('#clauseBetween').show();
      $('#clauseIn').hide();
  }
  else{
      $('#clauseBetween').hide();
      $('#clauseIn').show();
  }
});
</script>


