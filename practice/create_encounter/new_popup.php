<!DOCTYPE html>
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

//print_r($_REQUEST);
$ignoreAuth=true; // ignore the standard authentication for a regular OpenEMR user
include_once('../../interface/globals.php');
include_once("$srcdir/api.inc");
require_once("$srcdir/patient.inc");
require_once("$srcdir/options.inc.php");
require_once("$srcdir/htmlspecialchars.inc.php");
include_once("$srcdir/acl.inc");
formHeader("Form:New Encounter");
$pid = $_REQUEST['pid'];


?>
<html>
<head> 
<?php html_header_show();?>
<meta content="width=device-width,initial-scale=1.0" name="viewport">
<script type="text/javascript" src="../../../library/dialog.js"></script>
<!-- pop up calendar -->
<style type="text/css">@import url(<?php echo $GLOBALS['webroot'] ?>/library/dynarch_calendar.css);</style>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/dynarch_calendar.js"></script>
<?php include_once("{$GLOBALS['srcdir']}/dynarch_calendar_en.inc.php"); ?>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/dynarch_calendar_setup.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/textformat.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/dialog.js"></script>
<link rel="stylesheet" href="<?php echo $css_header;?>" type="text/css">
<link rel="stylesheet" type="text/css" href="../assets/css/bootstrap.min.css">
<link rel="stylesheet" href="../css/font-awesome.min.css">
<script src="../js/responsive_datatable/version1.0/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/common.js"></script>
<style>
    .input-group-sm > .form-control, .input-group-sm > .input-group-addon, .input-group-sm > .input-group-btn > .btn{
        padding: 3px 6px;
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
</SCRIPT>
    
</head>
<body class="body_top">   
    <div class="page-header">
        <h3><?php echo xlt('Create Encounter'); ?></h3>
    </div>
    <br/>
    <div class= "container">
        <div id="encounter_div" class="row">
            <div class="col-sm-12">
    <?php 
        $getpatient_name = sqlStatement("SELECT CONCAT(fname, ' ', lname) as  name FROM patient_data WHERE pid ='".$_REQUEST['pid']."'");
        $setpatient_name = sqlFetchArray($getpatient_name);
        $patient_name = '';
        if(!empty($setpatient_name)){
            $patient_name = $setpatient_name['name'];
        }
        ?><form method='post' name='my_form' role='form' class='form-horizontal' id='my_form' action='save.php'>
                <div class="form-group">
                    <div class="text-center">
                        <!--<button id="save_btnup" class="btn btn-default fa fa-floppy-o" onclick="my_form.submit()"> Save</button>-->
                    </div>
                </div>
            <div class="form-group">
                <label class="col-sm-3 control-label" for="patient_search">Patient: </label>
                <div class="col-sm-9">
                    <div class="input-group input-group-sm">
                        <select id='patient_dropdown' name='patient_dropdown' class="form-control" required>
                        <option value='<?php echo $_REQUEST['pid']; ?>'> <?php echo $patient_name; ?> </option>
                          </select>
<!--                        <input type="text" name='patient_search' id='patient_search' aria-describedby="fromadion" class="form-control">
                        <span class="input-group-btn"><button class="btn btn-default" id="atient_search_button" name="patient_search_button" type="button"><span class="glyphicon glyphicon-search" aria-hidden="true"></span> Search</button></span>-->
                    </div>
                </div>
            </div>
<!--            <div class="form-group">
                <div class="col-xs-6 col-sm-6 col-sm-push-3 input-group-sm">
                  <select id='patient_dropdown' name='patient_dropdown' class="form-control">
                <option>       </option>
                  </select>
                </div>
                <div class="col-xs-6 col-sm-3 col-sm-push-3 input-group-sm">
                    <button class="btn btn-default btn-sm" type="button" id="icdbtnclear" onclick="cleardropdown('patient')">Clear Dropdown</button>
                </div>
            </div>-->
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
                // facility name
                $getfacilityname = sqlStatement("SELECT name FROM facility WHERE id='".$_REQUEST['enc_facility']."' ");
                $setfacilityname = sqlFetchArray($getfacilityname);
                $facility_name = '';
                if(!empty($setfacilityname)){
                    $facility_name = $setfacilityname['name'];
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
                <label class="col-sm-3 control-label" for="dos">Date of Service: </label>
                <div class="col-sm-3">
                    <div class="input-group input-group-sm">
                        <input type='text' size='10' class="form-control" aria-describedby="fromadion" name='dos' id='dos' value='' onkeyup='datekeyup(this,mypcc)' onblur='dateblur(this,mypcc)'
                        title='yyyy-mm-dd' required />
                       <span class="input-group-addon" id="fromadion"><img src='../../interface/pic/show_calendar.gif' align='absbottom' width='24' height='22'
                        id='img_dob' border='0' alt='[?]' style='cursor:pointer'
                        title='Click here to choose a date'></span>
                    </div>
                </div>
                 <label class="col-sm-2 col-md-3 control-label" for="dos">Visit Category: </label>
                 <div class="col-sm-4 col-md-3 input-group-sm">
                    <select name="visit_category" id="visit_category" class="form-control" required >
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
                <label class="col-sm-3 control-label" for="facility">Facility: </label>
                <div class="col-sm-9 input-group-sm">
                    <select name="facility" id="facility" class="form-control" required>
                        <?php if($_REQUEST['enc_facility'] == ''){ ?>
                            <option></option>
                            <?php foreach($facilityArray as $fkey => $fvalue){ ?>
                            <option value = '<?php echo $fkey; ?>'> 
                                <?php echo $fvalue;
                            } ?>    
                            </option>
                        <?php }else{
                            ?><option value='<?php echo $_REQUEST['enc_facility']; ?>'><?php echo $facility_name ; ?></option> <?php 
                        } ?>
                    </select>
                </div>
            </div>
            
           <div class="form-group">
                <label class="col-sm-3 control-label" for="billing_facility">Billing Facility: </label>
                <div class="col-sm-9 input-group-sm">
                    <select name="billing_facility" id="billing_facility" class="form-control" required>
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
                   <input type='submit' id="save_btnup" class="btn btn-default fa fa-floppy-o" value='Save'> 
               </div>
           </div>      
    
   
        </form>

        </div>
    </div>
</div>
</body>
</html>
<?php
formFooter();
?>
