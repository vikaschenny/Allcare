<?php
//Include OpenEMR globals.php file 
include_once("globals.php");
?>
<html>
	<head>
		<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
		<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
		<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>		
		<link rel="stylesheet" href="themes/style_oemr.css" />
		<style>			
		  .clearfix {clear: both;}
		  body { font-size: 12px; }
		  #patient-list {width: 800px; }		  
		  #content-wrapper { width: 100%;}		  
		  #patient-search { border: 1px solid black; margin-bottom: 2%; padding: 1.5% 0% 1.5% 1.5%; background: #ececec}
		  #letters .letter { margin-right: 2%;  }
		  #letters span.selected { color: black; font-weight: bold;}
		  div #data { width: 100%; margin: 5px 0px; border: 1px solid black; min-height: 400px;}
		  #data #patient-list { float: left; width: 20%; border: 1px solid black;  }
		  #patient-list ul {  }
		  #patient-list { min-height: 300px; }
		  #data #document-operations { float: right; width: 79%; }		  
		  li.patient-name { list-style-type: none; padding: 3px; background-color: white; border-top: 2px solid #EAE6FF; }
		  li.patient-name a { color:  #000; }		  
		  li.patient-name a:hover { font-weight: bold; color: orange;  }
		  li.patient-name a:active { text-decoration: 600; color: red;}
		  ul#patients { margin: 0px; padding: 0px;}
		  #tableContainer { width: 100%; border: 1px solid black; margin-top: 1px; }
		  #heading { border-bottom: 1px solid black; padding: 5px; background-color: #ddd; }		
		  #heading div { font-weight: bold; font-weight: 12px;}		  
		  div.tableData { border-bottom: 1px dashed; padding: 5px;} 
		  div#doctitle { width: 70%; float: left; } 
		  div#status { width: 15%; float: left;padding-left: 5px;}
		  div#actions { width: 14%; float: left;} 
		  div.doctitle { float: left; width: 70%; padding-top: 1px; word-wrap: break-word; }
		  div.status { float: left; width: 15%; padding-top: 1px; padding-left: 5px; }
		  div.actions { float: left; width: 14%; padding-top: 1px; }
		  div#emailform { width: auto; min-height: 0px; max-height: none; height: auto !important; padding: 12px; }		
		  #container{ font-family: arial; }
		  div.section div:nth-of-type(even) { color: Green; background-color: #FCF8FA; }
		  div.section div:nth-of-type(odd) { color: Red; background-color: #FFFFFF; }
		  .span_menu span{ padding-top: 10px; }
  		  .ui-widget { font-size: 80%; }
		</style>
		<script>
			$(document).ready(function(){
				$(".patient").click(function(e) {
					$(".patient-name a").css("background-color", "transparent");
					$(".patient-name").css('background-color', 'white');
					$(this).parent().css("background-color", "#fedf98");
					$("#document-operations").html('<center><img src="resources/loading.gif" /><center>'); // Loading image
					//alert($(this).attr("patientid"));
					var patientid = $(this).attr("patientid");
					$.ajax({
						type: "POST",
						url: "patient_documents.php",
						data: 'id='+patientid,
						cache: false,
						success: function(data){							
							$("#document-operations").html(data);							
						},						
						error: function(data){ $("#document-operations").html('Could not serve your request'); },
					});
					e.preventDefault();
				});
				
				});
		</script>
	</head>
	<body class="body_top">	
		<span class="title">List of Patients</span><br><br>
		<div id="patient-search">
			<form id="search" action="" method="post">
				<label>Search for Patient</label> <input type="text" name="searchPatient" id="searchPatient" />
				<input type="submit" value="Search" />
			</form>
			<div id="letters">
				<?php 
				echo "<span class='letter'><a href='".$_SERVER['PHP_SELF']."'>Show All</a></span>";
				$letters = range('A', 'Z');
				foreach ($letters as $letter) {
					if(isset($_REQUEST['start']) && ($_REQUEST['start'] == $letter))
						echo "<span class='letter selected'>$letter</span>";
					else 
						echo "<span class='letter'><a href='".$_SERVER['PHP_SELF']."?start=$letter'>$letter</a></span>";
				} 
			?>
			</div>
		</div>
		<div id="data">
			<div id="patient-list">
				<?php 
				  $patientSql = null;
				  $patientWhereConditionsSql = null;				  
				  $patientOrderBy = " order by pdata.fname";
				  $patientRes = null;
				  //Build query to get all the Patients associated to the Provider based on the logged in user's role
				  $patientSql = "SELECT pdata.id, pdata.fname, pdata.lname, pdata.email, pdata.pid, concat(pdata.fname , ' ', pdata.lname) as pname  FROM patient_data AS pdata LEFT JOIN users ON pdata.providerID = users.ID ";
				  //Check for the logged in user's authentication
				  if(acl_check('admin', 'super')) {
					$patientWhereConditionsSql = null;					
					//Search for the requested patient
				  if(isset($_POST['searchPatient']) && ($_POST['searchPatient'] != null)) {						
						if(isset($patientWhereConditionsSql))
							$patientWhereConditionsSql .= " and concat(pdata.fname , ' ', pdata.lname) as pname LIKE '%".$_POST['searchPatient']."%'";
						else $patientWhereConditionsSql = " concat(pdata.fname , ' ', pdata.lname) LIKE '%".$_POST['searchPatient']."%'";
					}
					//Check if requested for patient names starting with a specific letter
					else if(isset($_REQUEST['start']) && ($_REQUEST['start'] != null)) {
						$patientWhereConditionsSql = " pdata.fname LIKE '".$_REQUEST['start']."%'";						
					}
				  }
				else if(acl_check('patients', 'docs')){
					$patientWhereConditionsSql .= " pdata.providerID = '".$_SESSION['authUserID']."' ";					
					//Search for the requested patient
					if(isset($_POST['searchPatient']) && ($_POST['searchPatient'] != null)) {						
						if(isset($patientWhereConditionsSql))
							$patientWhereConditionsSql .= " and concat(pdata.fname , ' ', pdata.lname) LIKE '%".$_POST['searchPatient']."%'";
						else $patientWhereConditionsSql = " concat(pdata.fname , ' ', pdata.lname) LIKE '%".$_POST['searchPatient']."%'";
					}
					//Check if requested for patient names starting with a specific letter
					if(isset($_REQUEST['start']) && ($_REQUEST['start'] != null)) {
						$patientWhereConditionsSql = " pdata.fname LIKE '".$_REQUEST['start']."%'";
					}
				}				  
				  
				//echo "<p>$patientSql where $patientWhereConditionsSql $patientOrderBy </p>";
				if($patientWhereConditionsSql != null)
					$patientRes = sqlStatement($patientSql." where ".$patientWhereConditionsSql.$patientOrderBy);
				else $patientRes = sqlStatement($patientSql." ".$patientOrderBy);				  
				//echo '<p>Number of records: '.sqlNumRows($patientRes).'</p>';
				
				if(sqlNumRows($patientRes) > 0) {
					echo '<ul id="patients">';
					while ($row = sqlFetchArray($patientRes)) {
						$pname = '';						
						if(strlen($row['pname']) > 1) {
							echo '<li class="patient-name"><a class="patient" patientid="'.$row['pid'].'" id="'.$row['id'].'" href="'.$_SERVER['PHP_SELF'].'?id='.$row['id'].'">'.$row['pname'].'</a></li>';
						}
						else {	//Note: hide patient info whose name is not available
							//echo '<li class="patient-name"><a class="patient" patientid="'.$row['pid'].'" id="'.$row['id'].'" href="'.$_SERVER['PHP_SELF'].'?id='.$row['id'].'">'.$row['pname'].'</a></li>';						
						}	
					}	//end of while
					echo '</ul>';
					}
					else {
						echo '<div class="message" style="margin: 5px;">Patient Data not found</div>';
					}
				?>
			</div>				
			<div id="document-operations">
				<p style="height: auto !important;">Please select a Patient to get the Document Details</p>
			</div><!--  end of div id="document-operations"  -->
			<div class="clearfix"></div>
		</div><!--  end of div id="data"  -->
	</div><!-- end of content-wrapper -->
	<div id="emailform" style="display: none;" title="Email Document">
		Recipient Email: <input type="text" name="email" id="email" value="" class="text ui-widget-content ui-corner-all" />
	</div>		
	</body>
</html>