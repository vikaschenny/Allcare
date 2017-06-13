<?php
// Copyright (C) 2010 MMF Systems, Inc>
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.

	/** This report is the batch report required for batch eligibility verification. **/

	//SANITIZE ALL ESCAPES
	$sanitize_all_escapes=true;
	//

	//STOP FAKE REGISTER GLOBALS
	$fake_register_globals=false;
	//

	require_once("../../globals.php");
	require_once("$srcdir/forms.inc");
	require_once("$srcdir/billing.inc");
	require_once("$srcdir/patient.inc");
	require_once("$srcdir/formatting.inc.php");
	require_once "$srcdir/options.inc.php";
	require_once "$srcdir/formdata.inc.php";
	include_once("$srcdir/calendar.inc");
	include_once("$srcdir/edi.inc");

	// Element data seperator		
	$eleDataSep		= "*";

	// Segment Terminator	
	$segTer			= "~"; 	

	// Component Element seperator
	$compEleSep		= "^"; 	
	
	// filter conditions for the report and batch creation 

	$from_date		= fixDate($_POST['form_from_date'], date('Y-m-d'));
	$to_date		= fixDate($_POST['form_to_date'], date('Y-m-d'));
	$form_facility	= $_POST['form_facility'] ? $_POST['form_facility'] : '';
	$form_provider	= $_POST['form_users'] ? $_POST['form_users'] : '';
	$exclude_policy = $_POST['removedrows'] ? $_POST['removedrows'] : '';
	$X12info		= $_POST['form_x12'] ? explode("|",$_POST['form_x12']) : '';

	//Set up the sql variable binding array (this prevents sql-injection attacks)
	$sqlBindArray = array();

	$where  = "e.pc_pid IS NOT NULL AND e.pc_eventDate >= ?";
	array_push($sqlBindArray, $from_date);
	
	//$where .="and e.pc_eventDate = (select max(pc_eventDate) from openemr_postcalendar_events where pc_aid = d.id)";

	if ($to_date) {
		$where .= " AND e.pc_eventDate <= ?";
		array_push($sqlBindArray, $to_date);
	}

	if($form_facility != "") {
		$where .= " AND f.id = ? ";
		array_push($sqlBindArray, $form_facility);
	}

	if($form_provider != "") {
		$where .= " AND d.id = ? ";
		array_push($sqlBindArray, $form_provider);
	}

	if($exclude_policy != ""){	$arrayExplode	=	explode(",", $exclude_policy);
								array_walk($arrayExplode, 'arrFormated');
								$exclude_policy = implode(",",$arrayExplode);
								$where .= " AND i.policy_number not in (".stripslashes($exclude_policy).")";
							}
        
        $where .= " AND (i.policy_number is not null and i.policy_number != '')";  
        // Subhan: This query is used to loop 270 records based on each patient
	$query2 = sprintf("		SELECT  DISTINCT p.pid,p.fname,p.lname,p.mname 
							FROM openemr_postcalendar_events AS e
							LEFT JOIN users AS d on (e.pc_aid is not null and e.pc_aid = d.id)
							LEFT JOIN facility AS f on (f.id = e.pc_facility)
							LEFT JOIN patient_data AS p ON p.pid = e.pc_pid
							LEFT JOIN insurance_data AS i ON (i.id =(
                                                                                                SELECT id
                                                                                                FROM insurance_data AS i
                                                                                                WHERE pid = p.pid AND type = 'primary'
                                                                                                ORDER BY date DESC
                                                                                                LIMIT 1
                                                                                                )
                                                                                        )
							LEFT JOIN insurance_companies as c ON (c.id = i.provider)
							WHERE %s ",	$where );
        // Subhan: This query is used to DISPLAY records with patient name in ASC order
        $query3 = sprintf("SELECT DATE_FORMAT(e.pc_eventDate, '%%Y%%m%%d') as pc_eventDate,
                                               e.pc_facility,
                                               p.lname,
                                               p.fname,
                                               p.mname, 
                                               DATE_FORMAT(p.dob, '%%Y%%m%%d') as dob,
                                               p.ss,
                                               p.sex,
                                               p.pid,
                                               p.pubpid,
                                               i.policy_number,
                                               i.provider as payer_id,
                                               i.subscriber_relationship,
                                               i.subscriber_lname,
                                               i.subscriber_fname,
                                               i.subscriber_mname,
                                               DATE_FORMAT(i.subscriber_dob, '%%m/%%d/%%Y') as subscriber_dob,
                                               i.subscriber_ss,
                                               i.subscriber_sex,
                                               DATE_FORMAT(i.date,'%%Y%%m%%d') as date,
                                               d.lname as provider_lname,
                                               d.fname as provider_fname,
                                               d.npi as provider_npi,
                                               d.upin as provider_pin,
                                               f.federal_ein,
                                               f.facility_npi,
                                               f.name as facility_name,
                                               c.name as payer_name,(SELECT ter.id FROM tbl_eligibility_response_data ter WHERE ter.pid= p.pid AND ter.month = DATE_FORMAT(e.pc_eventDate, '%%m-%%Y') LIMIT 1) as month_check,
                                                DATE_FORMAT(e.pc_eventDate, '%%m-%%Y') as month_value
                                    FROM openemr_postcalendar_events AS e
                                    LEFT JOIN users AS d on (e.pc_aid is not null and e.pc_aid = d.id)
                                    LEFT JOIN facility AS f on (f.id = e.pc_facility)
                                    LEFT JOIN patient_data AS p ON p.pid = e.pc_pid
                                    LEFT JOIN insurance_data AS i ON (i.id =(
                                                                            SELECT id
                                                                            FROM insurance_data AS i
                                                                            WHERE pid = p.pid AND type = 'primary'
                                                                            ORDER BY date DESC
                                                                            LIMIT 1
                                                                            )
                                                                    )
                                    LEFT JOIN insurance_companies as c ON (c.id = i.provider)
                                    WHERE %s %s",	$where, " ORDER BY p.lname,p.fname,p.mname ASC");
        
	// Run the query 
	$res2                   = sqlStatement($query2, $sqlBindArray);
        $res3                   = sqlStatement($query3, $sqlBindArray);
	
	// Get the facilities information 
	$facilities		= getUserFacilities($_SESSION['authId']);

	// Get the Providers information 
	$providers		= getUsernames();

	//Get the x12 partners information 
	$clearinghouses	= getX12Partner();
        
        if (isset($_POST['form_savefile']) && !empty($_POST['form_savefile'])) {
        // Subhan: We use ZipAchive which is (PHP 5 >= 5.2.0, PECL zip >= 1.1.0) feature    
	$zip = new ZipArchive();
        $filename = sprintf('elig-270-%s-%s.zip',
                            $from_date,
                            $to_date);

                if ($zip->open($filename, ZipArchive::CREATE)!==TRUE) {
                    exit("cannot open <$filename>\n");
                }
        // Subhan: Here we loop results based on patient ID. So that we create files of each patient        
	while($rows = sqlFetchArray($res2)):
            $clause  = " AND p.pid = ". $rows['pid']." ORDER BY p.lname,p.fname,p.mname ASC";
            
            $query = sprintf("SELECT DATE_FORMAT(e.pc_eventDate, '%%Y%%m%%d') as pc_eventDate,
                                               e.pc_facility,
                                               p.lname,
                                               p.fname,
                                               p.mname, 
                                               DATE_FORMAT(p.dob, '%%Y%%m%%d') as dob,
                                               p.ss,
                                               p.sex,
                                               p.pid,
                                               p.pubpid,
                                               i.policy_number,
                                               i.provider as payer_id,
                                               i.subscriber_relationship,
                                               i.subscriber_lname,
                                               i.subscriber_fname,
                                               i.subscriber_mname,
                                               DATE_FORMAT(i.subscriber_dob, '%%m/%%d/%%Y') as subscriber_dob,
                                               i.subscriber_ss,
                                               i.subscriber_sex,
                                               DATE_FORMAT(i.date,'%%Y%%m%%d') as date,
                                               d.lname as provider_lname,
                                               d.fname as provider_fname,
                                               d.npi as provider_npi,
                                               d.upin as provider_pin,
                                               f.federal_ein,
                                               f.facility_npi,
                                               f.name as facility_name,
                                               c.name as payer_name,(SELECT ter.id FROM tbl_eligibility_response_data ter WHERE ter.pid= p.pid AND ter.month = DATE_FORMAT(e.pc_eventDate, '%%m-%%Y') LIMIT 1) as month_check,
                                               DATE_FORMAT(e.pc_eventDate, '%%m-%%Y') as month_value
                                    FROM openemr_postcalendar_events AS e
                                    LEFT JOIN users AS d on (e.pc_aid is not null and e.pc_aid = d.id)
                                    LEFT JOIN facility AS f on (f.id = e.pc_facility)
                                    LEFT JOIN patient_data AS p ON p.pid = e.pc_pid
                                    LEFT JOIN insurance_data AS i ON (i.id =(
                                                                            SELECT id
                                                                            FROM insurance_data AS i
                                                                            WHERE pid = p.pid AND type = 'primary'
                                                                            ORDER BY date DESC
                                                                            LIMIT 1
                                                                            )
                                                                    )
                                    LEFT JOIN insurance_companies as c ON (c.id = i.provider)
                                    WHERE %s %s",	$where,$clause );
            $res = sqlStatement($query, $sqlBindArray);
            // Subhan: /library/edi.inc file uses "print_elig" method. But if we use this method here then
            // we get values of file displayed in screen itself. So, we added a new method "allCare_print_elig"
            // which returns the result instead of echo
            $printData = allCare_print_elig($res,$X12info,$segTer,$compEleSep);
            $zip->addFromString("elig-270-".$rows['lname']."-".$rows['fname'].".elg", $printData);
        endwhile;
        $zip->close();
        $length = filesize($filename);
        header('Content-Type: application/zip');
        header('Content-Length: ' . $length);
        header('Content-Disposition: attachment; filename="'.$filename.'"');
        readfile("$filename");
        unlink($filename);
        exit;
        }
?>

<html>

	<head>

		<?php html_header_show();?>

		<title><?php echo htmlspecialchars( xl('Eligibility 270 Inquiry Batch'), ENT_NOQUOTES); ?></title>

		<style type="text/css">@import url(../../../library/dynarch_calendar.css);</style>

		<link rel=stylesheet href="<?php echo $css_header;?>" type="text/css">

		<style type="text/css">

			/* specifically include & exclude from printing */
			@media print {
				#report_parameters {
					visibility: hidden;
					display: none;
				}
				#report_parameters_daterange {
					visibility: visible;
					display: inline;
				}
				#report_results table {
				   margin-top: 0px;
				}
			}

			/* specifically exclude some from the screen */
			@media screen {
				#report_parameters_daterange {
					visibility: hidden;
					display: none;
				}
			}

		</style>

		<script type="text/javascript" src="../../../library/textformat.js"></script>
		<script type="text/javascript" src="../../../library/dialog.js"></script>
		<script type="text/javascript" src="../../../library/dynarch_calendar.js"></script>
		<?php include_once("{$GLOBALS['srcdir']}/dynarch_calendar_en.inc.php"); ?>
		<script type="text/javascript" src="../../../library/dynarch_calendar_setup.js"></script>
		<script type="text/javascript" src="../../../library/js/jquery.1.3.2.js"></script>
                <script src="//code.jquery.com/jquery-1.12.0.min.js"></script>
                <script type='text/javascript'>
                function datafromchildwindow(id,month,pid) {
                    var class_name_check = "check"+month+"-p"+pid;
                    var getvrclass = document.getElementsByClassName(class_name_check);
                    for(var i=0; i<getvrclass.length; i++){
                        var oldattr = getvrclass[i].getAttribute('onclick');
                        getvrclass[i].setAttribute( "onClick", " return validate_elig('"+pid+"','"+id.trim()+"','"+month+"')" );
                    }
                    var class_name_checkbox = "checkbox"+month+"-p"+pid;
                    $("."+class_name_checkbox).prop('checked', true);
                }
                </script>
		<script type="text/javascript">

			var mypcc = "<?php echo htmlspecialchars( $GLOBALS['phone_country_code'], ENT_QUOTES); ?>";
			var stringDelete = "<?php echo htmlspecialchars( xl('Do you want to remove this record?'), ENT_QUOTES); ?>?";
			var stringBatch	 = "<?php echo htmlspecialchars( xl('Please select X12 partner, required to create the 270 batch'), ENT_QUOTES); ?>";
                        var stringverify = "<?php echo htmlspecialchars( xl('Please select X12 partner, required to verify the 270 batch'), ENT_QUOTES); ?>";

			// for form refresh 

			function refreshme() {
				document.forms[0].submit();
			}

			//  To delete the row from the reports section 
			function deletetherow(id){
				var suredelete = confirm(stringDelete);
				if(suredelete == true){
					document.getElementById('PR'+id).style.display="none";
					if(document.getElementById('removedrows').value == ""){
						document.getElementById('removedrows').value = "'" + id + "'"; 
					}else{
						document.getElementById('removedrows').value = document.getElementById('removedrows').value + ",'" + id + "'"; 
					
					}
				}
				
			}

			//  To validate the batch file generation - for the required field [clearing house/x12 partner] 
			function validate_batch()
			{
				if(document.getElementById('form_x12').value=='')
				{
					alert(stringBatch);
					return false;
				}
				else
				{
					document.getElementById('form_savefile').value = "true";
					document.theform.submit();
					
				}


			}
                        
                        //Eligibility verification
                        function validate_elig(pid,form_id,month_value)
                        {
                            if(document.getElementById('form_x12').value=='')
                            {
                                alert(stringverify);
                                return false;
                            }
                            else
                            {
                                from        = document.getElementById('form_from_date').value;
                                to          = document.getElementById('form_to_date').value;
                                facility    = document.getElementById('form_facility').value;
                                provider    = document.getElementById('form_users').value;
                                removedrows = document.getElementById('removedrows').value;
                                form_x12    = document.getElementById('form_x12').value;
                                if(form_id == '' || form_id == '0' || form_id == 0){
                                    form_id     = 0;
                                }

                                if(pid != ''){
                                    // checkbox condition
                                    var class_name_checkbox = "checkbox"+month_value+"-p"+pid;

                                    var viewportwidth   = document.documentElement.clientWidth;
                                    var viewportheight  = document.documentElement.clientHeight;
                                    window.resizeBy(-300,0);
                                    window.moveTo(0,0);

                                    if ($("."+class_name_checkbox).is(':checked') == false) {
                                        window.open("elig-verify.php?from="+from+"&to="+to+"&facility="+facility+"&provider="+provider+"&removedrows="+removedrows+"&form_x12="+form_x12+"&pid="+pid, "", "width=700, height=600,scrollbars=1,resizable=1");
                                    }
                                    window.open("save_eligibility_response_data.php?pid="+pid+"&form_id="+form_id+"&month_value="+month_value, "", "width=600,left="+(viewportwidth-100)+",height=600,top=0,scrollbars=1,resizable=1");
                                }else{
                                    window.open("elig-verify.php?from="+from+"&to="+to+"&facility="+facility+"&provider="+provider+"&removedrows="+removedrows+"&form_x12="+form_x12+"&pid="+pid, "", "width=880, height=600,scrollbars=1,resizable=1");
                                }
                            }
                        }

			// To Clear the hidden input field 

			function validate_policy()
			{
				document.getElementById('removedrows').value = "";
				document.getElementById('form_savefile').value = "";
				return true;
			}

			// To toggle the clearing house empty validation message 
			function toggleMessage(id,x12){
				
				var spanstyle = new String();

				spanstyle		= document.getElementById(id).style.visibility;
				selectoption	= document.getElementById(x12).value;
				
				if(selectoption != '')
				{
					document.getElementById(id).style.visibility = "hidden";
				}
				else
				{
					document.getElementById(id).style.visibility = "visible";
					document.getElementById(id).style.display = "inline";
				}
				return true;

			}

		</script>

	</head>
	<body class="body_top">

		<!-- Required for the popup date selectors -->
		<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>

		<span class='title'><?php echo htmlspecialchars( xl('Report'), ENT_NOQUOTES); ?> - <?php echo htmlspecialchars( xl('Eligibility 270 Inquiry Batch'), ENT_NOQUOTES); ?></span>

		<div id="report_parameters_daterange">
			<?php echo htmlspecialchars( date("d F Y", strtotime($form_from_date)), ENT_NOQUOTES) .
				" &nbsp; " . htmlspecialchars( xl('to'), ENT_NOQUOTES) . 
				"&nbsp; ". htmlspecialchars( date("d F Y", strtotime($form_to_date)), ENT_NOQUOTES); ?>
		</div>

		<form method='post' name='theform' id='theform' action='' >
			<input type="hidden" name="removedrows" id="removedrows" value="">
			<div id="report_parameters">
				<table>
					<tr>
						<td width='550px'>
							<div style='float:left'>
								<table class='text'>
									<tr>
										<td class='label'>
										   <?php xl('From','e'); ?>:
										</td>
										<td>
										   <input type='text' name='form_from_date' id="form_from_date" size='10' value='<?php echo htmlspecialchars( $from_date, ENT_QUOTES) ?>' onkeyup='datekeyup(this,mypcc)' onblur='dateblur(this,mypcc)' title='yyyy-mm-dd'>
										   <img src='../../pic/show_calendar.gif' align='absbottom' width='24' height='22'
											id='img_from_date' border='0' alt='[?]' style='cursor:pointer'
											title='<?php echo htmlspecialchars( xl('Click here to choose a date'), ENT_QUOTES); ?>'>
										</td>
										<td class='label'>
										   <?php echo htmlspecialchars( xl('To'), ENT_NOQUOTES); ?>:
										</td>
										<td>
										   <input type='text' name='form_to_date' id="form_to_date" size='10' value='<?php echo htmlspecialchars( $to_date, ENT_QUOTES) ?>'
											onkeyup='datekeyup(this,mypcc)' onblur='dateblur(this,mypcc)' title='yyyy-mm-dd'>
										   <img src='../../pic/show_calendar.gif' align='absbottom' width='24' height='22'
											id='img_to_date' border='0' alt='[?]' style='cursor:pointer'
											title='<?php echo htmlspecialchars( xl('Click here to choose a date'), ENT_QUOTES); ?>'>
										</td>
										<td>&nbsp;</td>
									</tr>
									
									<tr>
										<td class='label'>
											<?php echo htmlspecialchars( xl('Facility'), ENT_NOQUOTES); ?>:
										</td>
										<td>
											<?php dropdown_facility($form_facility,'form_facility',false);	?>
										</td>
										<td class='label'>
										   <?php echo htmlspecialchars( xl('Provider'), ENT_NOQUOTES); ?>:
										</td>
										<td>
											<select name='form_users' id="form_users" onchange='form.submit();'>
												<option value=''>-- <?php echo htmlspecialchars( xl('All'), ENT_NOQUOTES); ?> --</option>
												<?php foreach($providers as $user): ?>
													<option value='<?php echo htmlspecialchars( $user['id'], ENT_QUOTES); ?>'
														<?php echo $form_provider == $user['id'] ? " selected " : null; ?>
													><?php echo htmlspecialchars( $user['fname']." ".$user['lname'], ENT_NOQUOTES); ?></option>
												<?php endforeach; ?>
											</select>
										</td>
										<td>&nbsp;
										</td>
									</tr>
									
									<tr>
										<td class='label'>
											<?php echo htmlspecialchars( xl('X12 Partner'), ENT_NOQUOTES); ?>:
										</td>
										<td colspan='5'>
											<select name='form_x12' id='form_x12' onchange='return toggleMessage("emptyVald","form_x12");' >
														<!--<option value=''>--<?php //echo htmlspecialchars( xl('select'), ENT_NOQUOTES); ?>--</option>-->
														<?php 
															if(isset($clearinghouses) && !empty($clearinghouses))
															{
																foreach($clearinghouses as $clearinghouse): ?>
																	<option value='<?php echo htmlspecialchars( $clearinghouse['id']."|".$clearinghouse['id_number']."|".$clearinghouse['x12_sender_id']."|".$clearinghouse['x12_receiver_id']."|".$clearinghouse['x12_version']."|".$clearinghouse['processing_format'], ENT_QUOTES); ?>'
																		<?php echo $clearinghouse['id'] == $X12info[0] ? " selected " : null; ?>
																	><?php echo htmlspecialchars( $clearinghouse['name'], ENT_NOQUOTES); ?></option>
														<?php	endforeach; 
															}
															
														?>
												</select> 
												<span id='emptyVald' style='color:red;font-size:12px;'> * <?php echo htmlspecialchars( xl('Clearing house info required for EDI 270 batch creation.'), ENT_NOQUOTES); ?></span>
										</td>
									</tr>
								</table>
							</div>
						</td>
						<td align='left' valign='middle' height="100%">
							<table style='border-left:1px solid; width:100%; height:100%' >
								<tr>
									<td>
										<div style='margin-left:15px'>
											<a href='#' class='css_button' onclick='validate_policy(); $("#theform").submit();'>
											<span>
												<?php echo htmlspecialchars( xl('Refresh'), ENT_NOQUOTES); ?>
											</span>
											</a>
											<a href='#' class='css_button' onclick='return validate_elig(p="");'>
												<span>
													<?php echo htmlspecialchars( xl('Eligibility Verification'), ENT_NOQUOTES); ?>
                                                                                                        <input type='hidden' name='form_eligverify' id='form_eligverify' value=''></input>
												</span>
											</a>										
											<a href='#' class='css_button' onclick='return validate_batch();'>
												<span>
													<?php echo htmlspecialchars( xl('Create batch'), ENT_NOQUOTES); ?>
													<input type='hidden' name='form_savefile' id='form_savefile' value=''></input>
												</span>
											</a>
                                                                                </div>
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</div> 

			<div class='text'>
				<?php echo htmlspecialchars( xl('Please choose date range criteria above, and click Refresh to view results.'), ENT_NOQUOTES); ?>
			</div>

		</form>

		<?php
			if ($res3){
				show_elig($res3,$X12info,$segTer,$compEleSep);
			}
		?>
	</body>

	<script language='JavaScript'>
		Calendar.setup({inputField:"form_from_date", ifFormat:"%Y-%m-%d", button:"img_from_date"});
		Calendar.setup({inputField:"form_to_date", ifFormat:"%Y-%m-%d", button:"img_to_date"});
		<?php if ($alertmsg) { echo " alert('$alertmsg');\n"; } ?>
                $(document).ready(function(){
                    // update image path without touching code in edi library
                    $('.detail img').attr('src',$('.detail img').attr('src').replace('../../images', '../../../images'));
                    $('input[type="checkbox"]').click(function(){
                        var className = $(this).attr('class');
                        if($("."+className).prop("checked") == true){
                            $("."+className).prop('checked', true);
                        }
                        else if($("."+className).prop("checked") == false){
                            $("."+className).prop('checked', false);
                        }
                    });
                    $(document).on('click', '.detail>a.css_button', function(e) {
                        e.preventDefault(); 
                    });
                });    
	</script>

</html>