<!DOCTYPE html>
<html lang="en">
<head>
<?php
require_once("interface/globals.php");
$mobileapp='y' ;
$pid=$_REQUEST['pid'];


?>    
        <meta http-equiv="content-type" content="text/html; charset=UTF-8"> 
        <title>Face-to-Face Encounter</title>
        
        
<link rel="stylesheet" href="interface/main/css/bootstrap-3.0.3.min.css" type="text/css">
<script type="text/javascript" src="interface/main/js/jquery-2.0.3.min.js"></script>
<script type="text/javascript" src="interface/main/js/bootstrap-3.0.3.min.js"></script>
<script type="text/javascript">
 $(document).ready(function(){
	//alert("hello");
	var FormHtml = $("#frmActionPlan").html();

	$('#btnSubmit').click(function() {
		//alert("submit");
		/*var patientId = document.getElementById("patientId").value;
		var encounterId = document.getElementById("encounterId").value;
		var authUserID = document.getElementById("authUserID").value;
		var templateName = "Asthma Action Plan";
		//alert("encounterId="+encounterId+" "+"patientId="+patientId);
*/
		var ControlName = "";
		var ControlValue = "";

		$("input").each(function(index,item){

			if($(item).attr("type")=="text" || $(item).attr("type")=="hidden")
			{
				if($(item).attr("name")!=undefined)
				{
					ControlName = $(item).attr("name");
					ControlValue= $(item).val();
					FormHtml = FormHtml.replace('name="'+ControlName+'" value=""','name="'+ControlName+'" value="'+ControlValue+'"');				
				}
			}
                        
			else if (($(item).attr("type")=="checkbox" || $(item).attr("type")=="radio" ) && $(item).attr("checked")=="checked")
			{
                            
				if($(item).attr("name")!=undefined)
				{
                                    
					ControlName = $(item).attr("name");
					FormHtml = FormHtml.replace('name="'+ControlName+'"','name="'+ControlName+'" checked ');
				}
			}
		
		});


		$("textarea").each(function(index,item){
			
			if($(item).attr("name")!=undefined)
				{
					ControlName = $(item).attr("name");
					ControlValue= $(item).val();
					FormHtml = FormHtml.replace('name="'+ControlName+'" class="form-control pull-left"></textarea>','name="'+ControlName+'" class="form-control pull-left" readonly>'+ControlValue+'</textarea>');				
				}
		
		});

		

		//alert(FormHtml.length);
		var formContent = FormHtml.replace(/&/g,"~");
                                //alert(formContent);
                              $("#texens").val(formContent);
                                 document.frmActionPlan.action = "generatereport.php";
                                $("frmActionPlan").submit();
                               
                                
                                
		//var content = FormHtml.substr(0,1021) ;
		//var rsp = 7; 
                /*
		var rsp = $.ajax(
                                                                {
                                                                        url:'saveActionPlan.php',
                                                                        context: document.body,
                                                                        type: 'POST',
                                                                        data: 'FormHtml='+formContent,
                                                                        async:false,
                                                                        success:function(response)
                                                                        {		
                                                                                alert(response);
                                                                        }
                                                                }
                                                    ).responseText;
                                                
                                            //  alert("response="+rsp);  
	/*

		alert("response="+rsp);
		if(rsp!= 0)
		{	document.getElementById("recordId").value = rsp;
			var templateName = document.getElementById("templateName").value ;
			//document.forms['soap'].elements['plan'].value = 
		   // window.opener.writing("<a href='#' onclick='openPlanPopup(\'show_asthma_plan.html?recordId="+rsp+"\')\' id='aplan'></a>") ;
		  // window.opener.document.getElementById("divplanlinks").innerHTML = "<a href='#' onclick='openPlanPopup(\"show_asthma_plan.html?recordId="+rsp+"\");' id='aplan'>Asthma act Plan</a><br>";
		  //window.opener.$('.inner').append("<a href='#' onclick='openPlanPopup(\"show_asthma_plan.html?recordId="+rsp+"\");' id='aplan'>Asthma Action Plan</a><br>");
		   window.opener.$('.inner').append("<a href='#' onclick='openPlanPopup(\"show_asthma_plan.php?recordId="+rsp+"&templateName="+templateName+"\");'>Asthma Action Plan</a><br>");

		   window.close();	
		}*/
		
	});


	

});
        
</script>
</head>
<body>
<form name="frmActionPlan" id="frmActionPlan" method="POST" role="form">    
<div class="container">
    
        <div class="form-group">
    <div class="row" style="text-align: center;">
    <h4>Documentation of Face-to-Face Encounter</h4>
    </div>
    
    <div class="row">
        Patient name and Identification<input type="text" name="txtPatientName" value=""  > </div>
            
    <div class="row">I certify that this patient is under my care and that I, or a nurse practitioner or physician’s
assistant working with me, had a face-to-face encounter that meets the physician face-to-face
encounter requirements with this patient on: (insert date that visit occurred)
    </div>
    
    <div  class="container">
        <div class="row">
          Is Patient Home Bound or Can’t Drive (Circle your choice)
          
        <input name="radBound" type="radio"> Y <input name="radBound" type="radio"> N
        
        </div>
    
        <div class="row">
        Is Home Health Care Needed (Circle your choice)
        <input name="radCare" type="radio"> Y <input name="radCare" type="radio"> N
    </div>
    
        <div class="row">
        Does Patient have reliable other Primary Care Physician (Circle your choice) 
        <input name="radPhysician" type="radio"  value="Y"> Y <input name="radPhysician" type="radio" value="N"> N
    </div>
    
    <div class="row">
    Is House Visit Needed (Circle your choice)
    <input name="radVisit" type="radio" value="Y"> Y <input name="radVisit" type="radio" value="N"> N
    </div>
    
        <div class="row">
    If Yes (Circle Next Visit in Days approximately) 
    <input name="radDays" type="radio" value="30"> 30 <input name="radDays" type="radio" value="60"> 60
     <input type="radio" name="radDays" value="90"> 90
     
    Other <input type="text" name="txtOther" value="" class="form-control" > 
    
    
    </div>
    
        <div class="row">
        The encounter with the patient was in whole or in part for the following medical condition which is the 
        primary reason for home health care and <b>HOW LONG:</b> (List medical condition)<br>
        <textarea  name="txtMedical"  class="form-control pull-left"></textarea>
    </div>
    
        <div class="row">
        
        I certify that, based on my findings, the following services are medically necessary home health
services:
</div>
        
        <div class="row"><input type="text" name="txtNursing"  value=""  class="form-control" > Nursing </div>    
<div class="row"><input type="text" name="txtPhysical"   value=""  class="form-control" > Physical Therapy  </div>    
<div class="row"><input type="text" name="txtOccupational"  value=""  class="form-control" > Occupational Therapy  </div>    
<div class="row"><input type="text" name="txtSpeech" value=""  class="form-control" > Speech-language Pathology  </div>    

         <div class="row">
    To provide the following care/treatments: (Required only when the physician completing the face to face encounter documentation is different than the physician completing the plan of care):
    <br>
        <textarea name="txtTreatment" class="form-control pull-left"></textarea>
</div>    

<div class="row">
    My clinical findings support the need for the above services because:
    <br>
        <textarea name="txtFindings" class="form-control pull-left"></textarea>
</div>    

<div class="row">
    Further, I certify that my clinical findings support that this patient is homebound (i.e. absences from home require considerable and taxing effort and are for medical reasons or religious services or infrequently or of short duration when for other reasons) because
    <br>
        <textarea name="txtHomeBound" class="form-control pull-left"></textarea>
</div>  

<div class="row">
  <div class="col-md-8">    
      Nurse Practitioner Signature <input type="text" name="txtNurse" value="" class="form-control" size="60">
  </div>
  <div class="col-md-4">
       Date <input type="text" name="txtDate" value="" size="30" class="form-control"  value="">
  </div>
</div>




<div class="row">
    Physician’s Signature <input type="text" name="txtPhysician" value="" size="80" class="form-control" >
</div>


<div class="row">
  <div class="col-md-8">    
      Printed Name <input type="text" name="txtPrintedName" value="" size="80"  class="form-control">
  </div>
  <div class="col-md-4">
       Date <input type="text" name="txtPrintedDate" value="" size="30" class="form-control" >
  </div>
</div>


<div class="row" align="center"><input type="submit" name="btnSubmit" value="Submit" id="btnSubmit" class="btn" ></div>
                </div>            
            </div>        
        
</div>        
    <input type="hidden" name="texens" id="texens" value="" >
</form>    
</body>

</html>

