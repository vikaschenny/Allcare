<?php 
/*
 * Author: Mahesh Ravva
 * Desc: Page is invoked with ajax request from my_documents.php when the User selects a Patient name to load the corresponding documents within the page
 * Date: 18th October, 2013
 */
//Include OpenEMR globals.php file
include_once("globals.php");
?>
<style>
#docdata { background-color: #fedf98; height: 400px; overflow: auto; }
</style>
<script type="text/javascript">
$(document).ready(function(){
	function checkLength( o, n, min, max ) {
	    if ( o.val().length > max || o.val().length < min ) {
	      o.addClass( "ui-state-error" );
	      updateTips( "Length of " + n + " must be between " +
	        min + " and " + max + "." );
	      return false;
	    } else {
	      return true;
	    }
	  }
	
	  function checkRegexp( o, regexp, n ) {
	  //alert(o);
	  //alert(o.val());
	    if ( !( regexp.test( o ) ) ) {
	      o.addClass( "ui-state-error" );
	      updateTips( n );
	      return false;
	    } else {
	      return true;
	    }
	  }
					    
	  //Send Email
	  $(".submit_mail").click(function(e){
			e.preventDefault();
			//alert('You Clicked Email button');
			var docId = $(this).attr("rel");						
			var sendTo=$("#mailto-"+docId).val();
			var patientId = $("#patientid-"+docId).val();
			if(validateForm(sendTo)) {
				//alert("recipient: "+sendTo);
				$.post("email_doc.php", { docid: $(this).attr("rel"), recipient: sendTo, pid: patientId })						
					.done(function(data) {
						//$("#email-result").css("display","block");
						//$("#email-result").html(data);
						$("#mailto-"+docId).val('');
						alert(data);						  		
					});
			}	
		});
		 
		
		//Document Operations
		$(".docOps").change('click',function()
		{
			var ID = $(this).attr('rel');
			var pid = $(this).attr('pid');
			var docid = $(this).attr('docid');
			//alert(ID);
			var operation = $(this).val();
			//alert( test );
			var optionSelected = $(this).attr('pid');
			var params = "select: " + $(this).val() + "patient id: " + $(this).attr('pid') + " docid: " + $(this).attr('docid') + " operation: "+operation;
			if(operation == 'Edit') {						
				open_editor($(this).attr('pid'), $(this).attr('docid'));
			}
			else if(operation == 'Email') {
				var email = $( "#email" );
				//console.log(email);
				
				$( "#emailform" ).dialog({							      
				      height: 300,
				      width: 350,
				      modal: true,
				      buttons: {
				        "Send Email": function() {
						   var bValid = true;							          
				          bValid = bValid && checkLength( email, "email", 6, 120 );
						  var email_to_be_splitted = email.val();
						  var splitted_emails = email_to_be_splitted.split(',');
				          for (x in splitted_emails){
						    // From jquery.validate.js (by joern), contributed by Scott Gonzalez: http://projects.scottsplayground.com/email_address_validation/
				            bValid = bValid && checkRegexp( splitted_emails[x], /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i, "eg. ui@jquery.com" );							          
						  	if ( bValid ) {
				            $( "#emailresult" ).append( "send email.. "+ splitted_emails[x] );
				            $.post("email_doc.php", { docid: docid, recipient: splitted_emails[x], pid: pid })						
							.done(function(data) {											
								alert(data);						  		
							});
				            $( this ).dialog( "close" );
				          }
						  }
				        },
				        Cancel: function() {
				          $( this ).dialog( "close" );
				        }
				      },
				      close: function() {
				        //allFields.val( "" ).removeClass( "ui-state-error" );
				      }
				    });
			}
			else if(operation == 'Quick View') {
				open_viewer($(this).attr('pid'), $(this).attr('docid'));
			}
			else if(operation == 'Drive View') {
				open_drive_viewer($(this).attr('pid'), $(this).attr('docid'), $(this).attr('gdocurl'));
			}
			else if(operation == 'Download') {
				open_download($(this).attr('pid'), $(this).attr('docid'), $(this).attr('gdocurl'), $(this).attr('doctype'));
			}
			//alert(params);
		});
		//Sort by status
		$("a#sortbystatus").click(function(e) {			
			var order = $(this).attr('rel');
			$("#tableContainer").html('<center><img src="resources/loading.gif" /><center>'); // Loading image
			$.ajax({
				type: "POST",
				url: "patient_documents.php",
				data: 'id='+<?php echo $_REQUEST['id'];?>+'&sortbystatus='+order,
				cache: false,
				success: function(data){							
					$("#tableContainer").html(data);							
				},						
				error: function(data){ $("#document-operations").html('Could not serve your request'); },
			});
			if(order == 'asc')
				$(this).attr('rel', 'desc');
			else
				$(this).attr('rel', 'asc');			
			e.preventDefault();
		});
});

</script>
<script language="javascript">
			function validateForm(sendTo)
			{
			var x=sendTo;
			var atpos=x.indexOf("@");
			var dotpos=x.lastIndexOf(".");
			if (atpos<1 || dotpos<atpos+2 || dotpos+2>=x.length)
			  {
			  alert("Not a valid e-mail address");
			  return false;
			  }
			else
				return true;
			}
			//Open Editor in a popup window by providing the patient id and document id as input parameters
			function open_editor(pid, docid) {
				window.open('http://'+window.location.host+'/interface/patient_file/DocEditor.php?pid='+pid+'&document='+docid,'Edit Document','width=700,height=500');
				return false;
				}
			//Open Viewer in a popup window by providing the patient id and document id as input parameters
			function open_viewer(pid, docid) {
				window.open('http://'+window.location.host+'/interface/patient_file/DocViewer.php?pid='+pid+'&document='+docid,'View Document','width=700,height=500');
				return false;
			}
			function open_drive_viewer(pid, docid, gdocurl) {
				var editDoc = gdocurl.split('export=download');
				//alert(editDoc[0]);
				//window.open('http://'+window.location.host+'/interface/patient_file/DocViewer.php?pid='+pid+'&document='+docid,'View Drive Document','width=700,height=500');
				window.open(editDoc[0]+'edit','View Drive Document','width=700,height=500');
				return false;
			}
			function open_download(pid, docid, gdocurl, doctype) {
				//window.open('http://'+window.location.host+'/interface/patient_file/DocViewer.php?pid='+pid+'&document='+docid,'View Drive Document','width=700,height=500');
				//window.location(gdocurl,'Download Document');
			if(doctype == "web_url"){
			 	window.location = gdocurl;
				
				return false;
			  } else {
			  	window.open('http://'+window.location.hostname+'/controller.php?document&retrieve&patient_id='+pid+'&document_id='+docid,'Download');
 
				return false;
			  }
			}
			//Perform the selected action for the appropriate document
			function triggerAction(sel, pid, docid) {
				//alert("You have changed the action");
				var action = sel.options[sel.selectedIndex].value;
				
				switch(action)
				{
				case 'Edit':
				  alert('Edit Document');
				  open_editor(pid, docid);
				  break;
				case 'Email':
				  alert('Email Document');
				  break;
				case 'Quick View':
					alert('View Document');
					open_viewer(pid, docid);
					break;
				case 'Drive View':
					alert('View Drive Document');
					open_drive_viewer(pid, docid, gdocurl);
				case 'Download':
					alert('Download Document');
					open_download(pid, docid, gdocurl);
				}
			}
		</script>
<?php 
//Get all documents associated to the current patient
$documentRes = array();					
$orderby = ' order by docs.id';	//sort on document id by default
$statusorder = 'asc';
if(isset($_REQUEST['sortbystatus'])) {
	if($_REQUEST['sortbystatus'] == 'asc') { 
		$statusorder = 'desc';
		$orderby = ' order by status asc';
	}
	else {
		$statusorder = 'asc';
		$orderby = ' order by status desc';
	}
}
if(isset($_REQUEST['id']) && ($_REQUEST['id'] != '')) {
	$sql = "SELECT pdata.id, pdata.fname, pdata.lname, pdata.email, pdata.providerID, docs.id as docid, docs.url, docs.mimetype, docs.status, docs.type FROM patient_data as pdata INNER JOIN documents AS docs ON pdata.pid = docs.foreign_id INNER JOIN users ON
					pdata.providerID = users.ID AND pdata.pid = ".$_REQUEST['id'];	
	if(isset($_REQUEST['sortbystatus']))
		$orderby = " order by status ".$_REQUEST['sortbystatus'];	
	$documentRes = sqlStatement($sql . $orderby);
}
else {
		//Do nothing
		echo '<p style="height: auto !important;">Please select a Patient to get the Document Details.</p>';
}
if(sqlNumRows($documentRes) > 0) {
		$iterator = 0;
		echo '<div id="tableContainer">
				<div id="heading">
					<div id="doctitle">Document Location</div>
					<div id="status"><a href="#" id="sortbystatus" rel="'.$statusorder.'">Status</a></div>
					<div id="actions">Actions</div>
					<div class="clearfix"></div>
				</div>
				<div id="docdata">';
		while ($docRec = sqlFetchArray($documentRes))
		{ 
			$status = '';
			if(strlen($docRec['status']) > 0)
				$status = $docRec['status'];
			else 
				$status = '-';
			echo '<div class="tableData">
					<div class="doctitle">'.$docRec['url'].'</div>
					<div class="status">'.$status.'</div>
					<div id="" class="actions">
						<select class="docOps" name="action" rel="'.$iterator.'" pid="'.$_REQUEST['id'].'" docid="'.$docRec['docid'].'" gdocurl="'.$docRec['url'].'" doctype="'.$docRec['type'].'">
							<option value="">Select</option>
							<option value="Edit">Edit</option>
							<option value="Email">Email</option>
							<option value="Quick View">Quick View</option>
							<option value="Drive View">Google Drive View</option>
							<option value="Download">Download</option>
						</select>
					</div>
					<div class="clearfix"></div>';
			echo '</div><!-- End of tableData -->';
			$iterator++;
		}
		echo '</div></div><!-- End of tableData -->';
		//echo '</ol>';
}			
else {
		echo '<p style="height: auto !important;">There are no Documents associated with this Patient</p>';
}
echo '</div>';	//End of <div id="tableContainer">	
?>