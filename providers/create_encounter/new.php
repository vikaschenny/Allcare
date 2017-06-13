<?php
//SANITIZE ALL ESCAPES
$sanitize_all_escapes=true;

//STOP FAKE REGISTER GLOBALS
$fake_register_globals=false;

//continue session
session_start();

//landing page definition -- where to go if something goes wrong
$landingpage = "index.php?site=".$_SESSION['site_id']; 

// kick out if patient not authenticated
//if ( isset($_SESSION['uid']) && isset($_SESSION['patient_portal_onsite']) ) {
if ( isset($_SESSION['portal_username']) ) {    
$provider = $_SESSION['portal_username'];
}
else {
        session_destroy();
header('Location: '.$landingpage.'&w');
        exit;
}
//


$ignoreAuth=true; // ignore the standard authentication for a regular OpenEMR user
include_once('../../interface/globals.php');
include_once("$srcdir/api.inc");
require_once("$srcdir/patient.inc");
require_once("$srcdir/options.inc.php");
require_once("$srcdir/htmlspecialchars.inc.php");
include_once("$srcdir/acl.inc");
//formHeader("Form:New Encounter");
$pid = $_REQUEST['pid'];

$pagename = "plist"; 
$sql = sqlStatement("SELECT id, fname, lname, specialty FROM users " .
      "WHERE active = 1 AND ( info IS NULL OR info NOT LIKE '%Inactive%' ) " .
      "AND authorized = 1 AND username='".$provider."'" .
      "ORDER BY lname, fname");
$id = sqlFetchArray($sql);
$id1 = $id['id'];

$base_url="//".$_SERVER['SERVER_NAME'].dirname($_SERVER["REQUEST_URI"].'?').'/';
//echo "<pre>"; print_r($_POST); echo "</pre>";
if(!empty($_POST['patient_dropdown']) && !empty($_POST['dos']) && !empty($_POST['visit_category'] )){
    $posted_data   = $_POST;
    //echo "<pre>"; print_r($posted_data); echo "</pre>";
    $patientid          = $posted_data['patient_dropdown'];
    $dos                = $posted_data['dos'];
    $visit_category     = $posted_data['visit_category'];
    $facility           = $posted_data['facility'];
    $billing_facility   = $posted_data['billing_facility'];
    $rendering_provider = trim($posted_data['rendering_provider']);

    $getfacilityname = sqlStatement("SELECT name FROM facility where id = $facility");
    $facility_name = '';
    if(!empty($getfacilityname)){
        while($setfacilityname = sqlFetchArray($getfacilityname)){
            $facility_name = $setfacilityname['name'];
        }
    }
    $query  = sqlStatement("SELECT id as max_encounter FROM sequences");
    $array = array();
    while($setquery = sqlFetchArray($query)){
        $encounter = $setquery['max_encounter'] + 1;
        $queryseq = sqlStatement("UPDATE sequences SET id = $encounter ");
        $insert_encounter = sqlStatement("INSERT INTO form_encounter (date, facility, facility_id, pid, encounter, pc_catid, provider_id, billing_facility,rendering_provider)
            VALUES ('$dos', '$facility_name',$facility,$patientid,$encounter,$visit_category,$rendering_provider,$billing_facility,'$rendering_provider')");
        $sqlLastEncounter = sqlStatement("SELECT MAX(encounter) as encounter, form_encounter.id, username 
            FROM form_encounter 
            INNER JOIN users ON form_encounter.rendering_provider = users.id 
            WHERE pid=$patientid AND form_encounter.rendering_provider=$rendering_provider AND form_encounter.encounter = $encounter");
        $sqlGetLastEncounter = sqlFetchArray($sqlLastEncounter);
        if(!empty($sqlGetLastEncounter)){
            $insertform = sqlStatement("INSERT INTO forms (date, encounter, form_name, form_id, pid, user, groupname, authorized, deleted, formdir)
                VALUES(NOW(),".$sqlGetLastEncounter['encounter'].",'New Patient Encounter',".$sqlGetLastEncounter['id'].",$patientid,'".$sqlGetLastEncounter['username']."','Default',1, 0,'newpatient')");

             // log data
            $logdata= array(); 
            $data = sqlStatement("SELECT logdate from `tbl_allcare_formflag` WHERE  form_id='".$sqlGetLastEncounter['id'] . "' AND encounter_id = '".$sqlGetLastEncounter['encounter']."' AND form_name = 'Patient Encounter'");
            while($datalog = sqlFetchArray($data)){
                    $array =  unserialize($datalog['logdate']);
                    $count= count($array);
            }
            $username2      = sqlStatement("SELECT username FROM users where id = $rendering_provider");
            $usernameget    = sqlFetchArray($username2);
            $username       = isset($usernameget['username'])? $usernameget['username'] : ''; 

            $res = sqlStatement("SELECT * FROM `tbl_allcare_formflag` WHERE  form_id='".$sqlGetLastEncounter['id'] . "' AND encounter_id = '".$sqlGetLastEncounter['encounter']."' AND form_name = 'Patient Encounter'");
            if(empty($row1_res1)){
                $count = 0;

                $array2[] = array( 'authuser' =>$username,'Status' => 'Incomplete', 'date' => date("Y/m/d"), 'action'=>'created', 'ip_address'=>'Provider Portal','count'=> $count+1);
                $logdata=  serialize($array2);
                $query1 = sqlStatement("INSERT INTO tbl_allcare_formflag ( encounter_id,form_id, form_name,pending,finalized, logdate" .
                        ") VALUES ( '".$sqlGetLastEncounter['encounter']."','".$sqlGetLastEncounter['id'] ."', 'Patient Encounter',NULL, NULL, '".$logdata."' )");

            }else{
                $count = isset($count)? $count: 0;

                $array2[] = array( 'authuser' =>$username,'Status' => 'Incomplete', 'date' => date("Y/m/d"), 'action'=>'updated' ,'ip_address'=>'Provider Portal','count'=> $count+1);
                $logdata = array_merge_recursive($array, $array2);
                $logdata= ($logdata? serialize($logdata): serialize($array2) );
                $query1 = sqlStatement("UPDATE tbl_allcare_formflag SET logdate=  '".$logdata."' WHERE encounter_id ='".$sqlGetLastEncounter['encounter']."' and form_id = '".$sqlGetLastEncounter['id'] . "' and form_name = 'Patient Encounter'"); 
            }

        }

    }
    echo "<script>
        window.location.href = '../../../providers/provider_incomplete_charts.php?provider=".$rendering_provider." &form_patient=$patientid&form_to_date=$dos';
    </script>";
}


?>
<html>
<head> 
<?php // html_header_show();?>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta content="width=device-width,initial-scale=1.0" name="viewport">
<!-- pop up calendar -->
<style type="text/css">@import url(<?php echo $GLOBALS['webroot'] ?>/library/dynarch_calendar.css);</style>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/dynarch_calendar.js"></script>
<?php include_once("{$GLOBALS['srcdir']}/dynarch_calendar_en.inc.php"); ?>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/dynarch_calendar_setup.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/textformat.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/dialog.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/common.js"></script>
<link href='//fonts.googleapis.com/css?family=Abel' rel='stylesheet' type='text/css'>
<link href='//fonts.googleapis.com/css?family=Roboto:400,300,500' rel='stylesheet' type='text/css'>
<link href='//fonts.googleapis.com/css?family=Dosis:300,400,500,600' rel='stylesheet' type='text/css'>
<link rel="stylesheet" type="text/css" href="../assets/css/animate.css">
<link rel="stylesheet" type="text/css" href="../assets/css/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="../assets/css/owl.carousel.css">
<link rel="stylesheet" type="text/css" href="../assets/css/owl.theme.css">
<link rel="stylesheet" type="text/css" href="../assets/css/owl.transitions.css">
<link rel="stylesheet" type="text/css" href="../assets/css/font-awesome.min.css">
<link rel="stylesheet" type="text/css" href="../assets/css/main.css">
<link rel="stylesheet" type="text/css" href="../css/scollypay.css">
<link rel="stylesheet" type="text/css" href="../assets/css/customize.css">
<link href='//fonts.googleapis.com/css?family=Roboto+Condensed:400,300' rel='stylesheet' type='text/css'>
<script src="../js/responsive_datatable/version1.0/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="../assets/js/bootstrap.min.js"></script>
<style>
    
    .bs-docs-sidenav .active a:hover {
        background-color: #4ac2dc;
    }
    #sidenave .active {
       background-color: #4ac2dc;
       cursor: default;
    }

     #sidenave li:last-child .active {
        background-color: #4ac2dc;
        border-radius: 0 0 6px 6px;
        cursor: default;
    }

    #sidenave li:first-child .active {
        background-color: #4ac2dc;
        border-radius: 0 0 6px 6px;
        cursor: default;
    }

    #sidenave .active a {
        color:#fff !important;
        font-weight:bold;
        text-decoration: none;
    }
    #content table ul li{
       display: block;
    }
    .bs-docs-sidenav.affix {
        top: 94px;
    }
    .bs-docs-sidenav > li:first-child > a {
        border-radius: 6px 6px 0 0;
    }
    #content {
        padding-bottom: 16px;
        overflow-x: visible;
        overflow-y: hidden;
    }
   .bs-docs-sidenav > li:first-child > a {
        border-radius: 6px 6px 0 0;
    }
    .input-group-addon{
        padding: 3px 8px !important;
    }
    .page-header {
        margin:0px;
        border-bottom: 1px solid #000;
    }
</style>
<SCRIPT language="javascript">
    $(document).ready(function() {
        jQuery('#patient_search_button').click(function(){
                jQuery.ajax({
                    type: 'POST',
                    url: "patient_search.php",
                    dataType : "json",
                    data: {
                            searchstring : jQuery('#patient_search').val()
                        },

                    success: function(data)
                    {
                        var stringified = '';
                        jQuery('#patient_dropdown').empty();
                        jQuery('#patient_dropdown').append(jQuery('<option>', { 
                                value: '',
                                text : 'Select'
                            }));
                        stringified = JSON.stringify(data, undefined, 2);
                        var objectified = jQuery.parseJSON(stringified);
                        for(var key in objectified ){
                            jQuery('#patient_dropdown').append(jQuery('<option>', { 
                                value: key,
                                text : key+" - "+objectified[key]
                            }));
                        }
                    },
                    failure: function(response)
                    {
                        alert("error");
                    }		
                });
            });
            setNavigation();
            function setNavigation() {
                 $('#sidenave li').eq(8).addClass('active');
                 $('#sidenave li').eq(8).find('a').removeAttr("href");
            }
        });
        function cleardropdown(value) {
            jQuery('#'+value+'_dropdown').empty();
            if(value === 'icd'){
                jQuery('#'+value+'_dropdown').each(function() {
                    var option = $("<option />");
                    option.attr("value", ' ').text('Select');
                    jQuery('#'+value+'_dropdown').append(option);
                });
            }else{
                var encounterid = '<?php echo $_REQUEST['encounter']; ?>';
                jQuery("#"+jQuery.trim(value)+ " option:selected").removeAttr("selected");
            }
        }
         function DoPost(page_name, provider) {
            method = "post"; // Set method to post by default if not specified.
            var form = document.createElement("form");
            form.setAttribute("method", method);
            form.setAttribute("action", page_name);
            var key='provider';
            var hiddenField = document.createElement("input");
            hiddenField.setAttribute("type", "hidden");
            hiddenField.setAttribute("name", key);
            hiddenField.setAttribute("value", provider);

            form.appendChild(hiddenField);
            document.body.appendChild(form);
            form.submit();
        } 
                
        function DoPost_patient(url) {
            var res = url.split("?");
            var param=res[1].split("&");
            method = "post"; // Set method to post by default if not specified.
            var form = document.createElement("form");
                form.setAttribute("method", method);
                form.setAttribute("action", res[0]);
            for(var i=0; i<param.length; i++){
                var param1=param[i].split("=");
                var hiddenField = document.createElement("input");
                hiddenField.setAttribute("type", "hidden");
                hiddenField.setAttribute("name", param1[0]);
                hiddenField.setAttribute("value", param1[1]);
                form.appendChild(hiddenField);
            }
              document.body.appendChild(form);
              form.submit();
        }
</SCRIPT>
    
</head>
<body >   
    <?php include '../header_nav.php'; ?>
    <section id= "services" style="padding-top:60px !important">
    <div class= "container">
        <div class= "row">
            <div id="contents">
                <div id="sidenave" class="col-sm-3">                                           
                    <ul class="nav nav-list bs-docs-sidenav affix">
                        <input type="hidden" id="sidenavep" value="<?php echo $page_id; ?>"/>
                        <li class=""><a  style="border-radius: 6px 6px 0 0;" href='javascript:;' onclick=DoPost_patient('../patient_data.php?order=<?php echo $order; ?>&provider=<?php echo $provider; ?>&id=my_patients')>My Patients</a></li>
                        <li class=""><a  href='javascript:;' onclick=DoPost_patient('../patient_data.php?order=<?php echo $order; ?>&provider=<?php echo $provider; ?>&id=all_patients')>All Patients</a></li>
                        <li class=""><a  href='javascript:;' onclick=DoPost_patient('../patient_data.php?order=<?php echo $order; ?>&provider=<?php echo $provider; ?>&id=by_facility')>Patients By Facility</a></li>
                        <li class=""><a  href='javascript:;' onclick=DoPost_patient('../patient_data.php?order=<?php echo $order; ?>&provider=<?php echo $provider; ?>&id=by_appointment')>Patients By Appointments</a></li>
                        <?php  $sql_vis=sqlStatement("SELECT provider_plist_links from tbl_user_custom_attr_1to1 where userid='".$id['id']."'");
                                $row1_vis=sqlFetchArray($sql_vis);  
                                if(!empty($row1_vis)){
                                    $links=explode("|",$row1_vis['provider_plist_links']);
                                    if(in_array('patient_center',$links)){ ?>
                                        <li class=""><a href='javascript:;' onclick=DoPost_patient('../patient-center-batch.php?provider=<?php echo $provider; ?>')>Patient Center Batch</a></li>
                                    <?php  } if(in_array('patient_stat',$links)){  ?>
                                          <li class=""><a href='javascript:;' onclick=DoPost_patient('../patient-statement.php?provider=<?php echo $provider; ?>')>Patient Statement Batch</a></li>
                                    <?php }if(in_array('create_patient',$links)){ 
                                                ?>
                                                   <li class=""><a href='javascript:;' onclick=DoPost_patient('../create_patient/new_comprehensive.php?provider=<?php echo $provider; ?>')>Create Patient</a></li>
                                          
                                               <?php 
                                            } if(in_array('create_app',$links)){ ?>
                                                   <li class=""><a href='#' onclick="window.open('../scheduling/calendar/add_edit_event.php','name=appt','width=595,height=300')">Create Appointment</a></li>
                                            <?php }  if(in_array('create_enc',$links)){ ?>
                                                    <li class=""><a href='javascript:;' onclick=DoPost_patient('../create_encounter/new.php?provider=<?php echo $provider; ?>')>Create Encounter</a></li>
                                            <?php } if(in_array('scheduling',$links)){ ?>
                                                                   <li class=""><a href='../scheduling/scheduling_pop_up.php' target="_blank"">Scheduling</a></li>
                                                            <?php }
                                }
                        ?>
                    </ul>                  
                </div> 
                <div id="content" class="col-sm-9">
                    <div><h3><?php echo xlt('Create Encounter'); ?></h3></div>
                <div id="encounter_div" class="row">
                    <div class="col-sm-12">
            <?php echo "<form method='post' name='my_form' role='form' class='form-horizontal' id='my_form' action=''>\n"; ?>
                        
                        <br/>
                        <div class="form-group">
                            <div class="text-center">
                                <!--<button id="save_btnup" class="btn btn-default fa fa-floppy-o" onclick="my_form.submit()"> Save</button>-->
                            </div>
                        </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="patient_search">Patient: </label>
                        <div class="col-sm-9">
                            <div class="input-group input-group-sm">
                                <input type="text" name='patient_search' id='patient_search' aria-describedby="fromadion" class="form-control" placeholder='Enter Patient Name to Search'>
                                <span class="input-group-btn"><button class="btn btn-default" id="patient_search_button" name="patient_search_button" type="button"><span class="glyphicon glyphicon-search" aria-hidden="true"></span> Search</button></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-xs-6 col-sm-6 col-sm-push-3 input-group-sm">
                          <select id='patient_dropdown' name='patient_dropdown' class="form-control" required>
                        <option>       </option>
                          </select>
                        </div>
                        <div class="col-xs-6 col-sm-3 col-sm-push-3 input-group-sm">
                            <button class="btn btn-default btn-sm" type="button" id="icdbtnclear" onclick="cleardropdown('patient')">Clear Dropdown</button>
                        </div>
                    </div>
                    <?php
                        $getprovidersql=sqlStatement("SELECT id, fname, lname, specialty FROM users " .
                            "WHERE active = 1 AND ( info IS NULL OR info NOT LIKE '%Inactive%' ) " .
                            "AND authorized = 1 AND username='".$provider."'" .
                            "ORDER BY lname, fname");
                        $providerid = sqlFetchArray($getprovidersql);
                        $provider_id = $providerid['id'];

                        $getfuv = sqlStatement("SELECT facilities,visit_categories FROM tbl_allcare_facuservisit WHERE  `users`  REGEXP ('".":\"".$provider_id."\"')");
//                        $fuvrow = sqlFetchArray($getfuv);
                        $visit_categories = $facility = $dataArray = $dataArray2 = array();
                        if(!empty($getfuv)){
                            while($fuvrow = sqlFetchArray($getfuv)){
                                $facility[]               = unserialize($fuvrow['facilities']);
                                $visit_categories[]       = unserialize($fuvrow['visit_categories']);
                            }
                        }
                        // visit category
                        for($j = 0; $j<count($visit_categories); $j++){
                            foreach($visit_categories[$j] as $arraykey){
                                $dataArray[] = $arraykey;
                            }
                        }

                        $visit_category = array_unique($dataArray);
                        if(!empty($visit_category)){
                            for($i=0;$i< count($visit_category); $i++){
                                $getvisit = sqlStatement("SELECT pc_catname FROM openemr_postcalendar_categories WHERE pc_catid='$visit_category[$i]'");
                                $setvisit = sqlFetchArray($getvisit);
                                if(!empty($setvisit)){
                                    $visitcategoryArray[$visit_category[$i]] = $setvisit['pc_catname'];
                                }
                            }
                        }

                        // facility
                        for($j = 0; $j<count($facility); $j++){
                            foreach($facility[$j] as $arraykeys){
                                $dataArray2[] = $arraykeys;
                           }
                        }
                        $facility = array_unique($dataArray2);
                        if(!empty($facility)){
                            for($i=0;$i< count($facility); $i++){
                                $getfac = sqlStatement("SELECT name FROM facility WHERE id='$facility[$i]'");
                                $setfac = sqlFetchArray($getfac);
                                if(!empty($setfac)){
                                    $facilityArray[$facility[$i]] = $setfac['name'];
                                }
                            }
                        }

                        // billing Facility
                        $billingfacilityArray = array();
                        $getbillingfac = sqlStatement("SELECT id, name FROM facility WHERE primary_business_entity =1");
                        $setbillingfac = sqlFetchArray($getbillingfac);
                        if(!empty($setbillingfac)){
                            $billingfacilityArray[$setbillingfac['id']] = $setbillingfac['name'];
                        }

                        // Rendering Provider
                        $rendering_provider = array();
                        $getrendering_provider = sqlStatement("SELECT id, CONCAT(fname , ' ',lname) as name FROM users WHERE id ='$provider_id'");
                        $setrendering_provider = sqlFetchArray($getrendering_provider);
                        if(!empty($setrendering_provider)){
                            $rendering_provider[$setrendering_provider['id']] = $setrendering_provider['name'];
                        }
                    ?>
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="dos" >Date of Service: </label>
                        <div class="col-sm-3">
                            <div class="input-group input-group-sm">
                                <input type='text' size='10' class="form-control" aria-describedby="fromadion" name='dos' id='dos' value='' onkeyup='datekeyup(this,mypcc)' onblur='dateblur(this,mypcc)'
                                title='yyyy-mm-dd' required/>
                               <span class="input-group-addon" id="fromadion"><img src='../../interface/pic/show_calendar.gif' align='absbottom' width='24' height='22'
                                id='img_dob' border='0' alt='[?]' style='cursor:pointer'
                                title='Click here to choose a date'></span>
                            </div>
                        </div>
                         <label class="col-sm-2 col-md-3 control-label" for="dos" >Visit Category: </label>
                         <div class="col-sm-4 col-md-3 input-group-sm">
                            <select name="visit_category" id="visit_category" class="form-control" required>
                                <option></option>
                                <?php foreach($visitcategoryArray as $vkey => $vvalue){ ?>
                                <option value = '<?php echo $vkey; ?>'> 
                                    <?php echo $vvalue;
                                } ?>    
                                </option>
                            </select>
                         </div>
                    </div>            
                   <script LANGUAGE="JavaScript">
                    Calendar.setup({inputField:"dos", ifFormat:"%Y-%m-%d", button:"img_dob"});
                   </script>
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="facility" >Facility: </label>
                        <div class="col-sm-9 input-group-sm">
                            <select name="facility" id="facility" class="form-control" required>
                                <option></option>
                                <?php foreach($facilityArray as $fkey => $fvalue){ ?>
                                <option value = '<?php echo $fkey; ?>'> 
                                    <?php echo $fvalue;
                                } ?>    
                                </option>
                            </select>
                        </div>
                    </div>

                   <div class="form-group">
                        <label class="col-sm-3 control-label" for="billing_facility">Billing Facility: </label>
                        <div class="col-sm-9 input-group-sm">
                            <select name="billing_facility" id="billing_facility" class="form-control">
                                <?php foreach($billingfacilityArray as $bkey => $bvalue){ ?>
                                <option value = '<?php echo $bkey; ?>'> 
                                    <?php echo $bvalue;
                                } ?>    
                                </option>
                            </select>
                        </div>
                    </div>

                   <div class="form-group">
                        <label class="col-sm-3 control-label" for="billing_facility">Rendering Provider: </label>
                        <div class="col-sm-9 input-group-sm">
                            <select name="rendering_provider" id="rendering_provider" class="form-control">
                                <?php foreach($rendering_provider as $rkey => $rvalue){ ?>
                                <option value = '<?php echo $rkey; ?>'> 
                                    <?php echo $rvalue;
                                } ?>    
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                       <div class="text-center">
                           <input type="submit" id="save_btnup" name= 'save_btnup' class="btn btn-default fa fa-floppy-o" value="Save"/> 
                       </div>
                   </div>      


        </form>
                </div>
            </div>
                </div>
            </div>
      </div>    
    </div>    
    </section>
<?php include '../footer.php'; ?>      
<?php
formFooter();
?>
