<?php
    //STOP FAKE REGISTER GLOBALS
    $fake_register_globals=false;
    //continue session
    session_start();

    $landingpage = "../../../index.php?site=".$_SESSION['site_id'];

    if ( isset($_SESSION['portal_username']) ) {    
        $portal_user = $_SESSION['portal_username']; 
    }else {
        session_destroy();
        header('Location: '.$landingpage.'&w');
        exit;
    }

    $ignoreAuth=true; // ignore the standard authentication for a regular OpenEMR user

    include_once("../../../interface/globals.php");
    
    $freeb_type_array = array('Other HCFA'
                                ,'Medicare Part B'
                                ,'Medicaid'
                                ,'ChampUSVA'
                                ,'ChampUS'
                                ,'Blue Cross Blue Shield'
                                ,'FECA'
                                ,'Self Pay'
                                ,'Central Certification'
                                ,'Other Non-Federal Programs'
                                ,'Preferred Provider Organization (PPO)'
                                ,'Point of Service (POS)'
                                ,'Exclusive Provider Organization (EPO)'
                                ,'Indemnity Insurance'
                                ,'Health Maintenance Organization (HMO) Medicare Risk'
                                ,'Automobile Medical'
                                ,'Commercial Insurance Co.'
                                ,'Disability'
                                ,'Health Maintenance Organization'
                                ,'Liability'
                                ,'Liability Medical'
                                ,'Other Federal Program'
                                ,'Title V'
                                ,'Veterans Administration Plan'
                                ,'Workers Compensation Health Plan'
                                ,'Mutually Defined'
                                );

    $freeb_claim_type_array = array('16'
                                   ,'MB'
                                   ,'MC'
                                   ,'CH'
                                   ,'CH'
                                   ,'BL'
                                   ,'16'
                                   ,'09'
                                   ,'10'
                                   ,'11'
                                   ,'12'
                                   ,'13'
                                   ,'14'
                                   ,'15'
                                   ,'16'
                                   ,'AM'
                                   ,'CI'
                                   ,'DS'
                                   ,'HM'
                                   ,'LI'
                                   ,'LM'
                                   ,'OF'
                                   ,'TV'
                                   ,'VA'
                                   ,'WC'
                                   ,'ZZ'
                                   );
    
    
    $insDataRes = [];
    $state = $_GET['state'];
    $uniqueid = $_GET['uniqueid'];
    $formid = $_GET['formid'];
    if($state == 'update' || $state == 'edit'){
        
        if($uniqueid == 'manual'){
            $insData = mysql_query("SELECT * FROM `insurance_companies` as ic,`tbl_inscomp_custom_attr_1to1` as a,`addresses` as ad WHERE a.`insuranceid` = '".$formid."' AND a.`insuranceid` = ic.`id` AND ad.`foreign_id` = ic.`id`");
            $insDataRes = mysql_fetch_assoc($insData);
        }
        else{
            $insData = mysql_query("SELECT * FROM `insurance_companies` as ic,`tbl_inscomp_custom_attr_1to1` as a,`addresses` as ad WHERE ic.`id` = '".$uniqueid."' AND a.`insuranceid` = ic.`id` AND ad.`foreign_id` = ic.`id`");
            $insDataRes = mysql_fetch_assoc($insData);
        }
    }
?>

<!DOCKTYPE html>
<html lang="en">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>General Edit</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <script src="../assets/js/responsive_datatable/version1.0/jquery-1.11.3.min.js"></script>
        <style>
            /* Required field START */

            .required-field-block {
                position: relative;   
            }

            .required-field-block .required-icon {
                display: inline-block;
                vertical-align: middle;
                margin: -0.25em 0.25em 0em;
                background-color: #E8E8E8;
                border-color: #E8E8E8;
                padding: 0.5em 0.8em;
                color: rgba(0, 0, 0, 0.65);
                text-transform: uppercase;
                font-weight: normal;
                border-radius: 0.325em;
                -webkit-box-sizing: border-box;
                -moz-box-sizing: border-box;
                -ms-box-sizing: border-box;
                box-sizing: border-box;
                -webkit-transition: background 0.1s linear;
                -moz-transition: background 0.1s linear;
                transition: background 0.1s linear;
                font-size: 75%;
            }

            .required-field-block .required-icon {
                background-color: transparent;
                position: absolute;
                top: 0em;
                right: 0em;
                z-index: 10;
                margin: 0em;
                width: 30px;
                height: 30px;
                padding: 0em;
                text-align: center;
                -webkit-transition: color 0.2s ease;
                -moz-transition: color 0.2s ease;
                transition: color 0.2s ease;
            }

            .required-field-block .required-icon:after {
                position: absolute;
                content: "";
                right: 16px;
                top: 1px;
                z-index: -1;
                width: 0em;
                height: 0em;
                border-top: 0em solid transparent;
                border-right: 30px solid transparent;
                border-bottom: 30px solid transparent;
                border-left: 0em solid transparent;
                border-right-color: inherit;
                -webkit-transition: border-color 0.2s ease;
                -moz-transition: border-color 0.2s ease;
                transition: border-color 0.2s ease;
            }

            .required-field-block .required-icon .text {
                    color: #B80000;
                    font-size: 26px;
                    margin: -4px 0 0 -17px;
            }
            .form-group {
                margin-bottom: 8px;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <form class="form-horizontal" name="insurancecompany" action="insurance_save_main.php" method="post" role="form">
                <input type="hidden" name="hid_uniqueid" id="hid_uniqueid" value="<?php echo $_GET['uniqueid']; ?>">
                <input type="hidden" name="hid_state" id="hid_state" value="<?php echo $_GET['state']; ?>">
                <div class="form-group">
                    <label class="control-label col-sm-3" for="name">Name:</label>
                    <div class="col-sm-9 required-field-block">
                        <input type="text" class="form-control input-sm" id="name" value="<?php if(sizeof($insDataRes) > 0) echo $insDataRes['name']; else echo $_GET['name']; ?>" name="name" placeholder="Enter Name" required>
                        <div class="required-icon">
                            <div class="text">*</div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-3" for="attn">Attn:</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control input-sm" id="attn" name="attn" placeholder="Enter Attn" value="<?php echo $insDataRes['attn']; ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-3" for="address1">Address1:</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control input-sm" id="address1" name="address1" placeholder="Enter Address" value="<?php echo $insDataRes['line1']; ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-3" for="address2">Address2:</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control input-sm" id="address2" name="address2" placeholder="Enter Address" value="<?php echo $insDataRes['line2']; ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-3" for="city">City, State, Zip:</label>
                    <div class="col-sm-4" style="padding-right: 5px;">
                        <input type="text" class="form-control input-sm" id="city" name="city" placeholder="Enter City" value="<?php echo $insDataRes['city']; ?>">
                    </div>
                    <div class="col-sm-2" style="padding-left:5px; padding-right: 5px;">
                        <input type="text" class="form-control input-sm" id="state" name="state" placeholder="Enter State" value="<?php echo $insDataRes['state']; ?>">
                    </div>
                    <div class="col-sm-3" style="padding-left:5px;">
                        <input type="text" class="form-control input-sm" id="zip" name="zip" placeholder="Enter Zip" value="<?php echo $insDataRes['zip']; ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-3" for="phone">Phone:</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control input-sm" id="phone" name="phone" placeholder="Enter Phone Number">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-3" for="cmdid">CMS ID:</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control input-sm" id="cmsid" name="cmsid" placeholder="Enter CMSID" value="<?php echo $insDataRes['cms_id']; ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-3" for="payertype">Payer Type</label>
                    <div class="col-sm-9">
                        <select class="form-control input-sm" id="payertype" name="payertype">
                            <option value="">Select Type</option>
                            <?php $i = 0; foreach($freeb_type_array as $each){ ?>
                                <option <?php if($insDataRes['freeb_type'] == $i) echo "selected"; ?> value="<?php echo $i; ?>"><?php echo $each; ?></option>
                            <?php $i++; } ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-3" for="x12partner">Default X12Partner</label>
                    <div class="col-sm-9">
                        <select class="form-control input-sm" id="x12partner" name="x12partner">
                            <option value="">Select x12Partner</option>
                            <option <?php if($insDataRes['x12_receiver_id'] == 16) echo "selected"; ?> value="16">Zirmed</option>
                        </select>
                    </div>
                </div>
                <input type="hidden" name="formid" id="formid"/>
            </form>
          </div>
         <script>
             function submitform(){
                 
                 if($('.required-field-block input').val().trim() !=""){
                     var formElement = document.querySelector("form");
                     $.ajax({url:"insurance_save_main.php",data:$('form').serializeArray(),method:"post",success: function (data, textStatus, jqXHR) {
                            $(parent.document.getElementById("inscop")).attr("data-formdata",JSON.stringify($('form').serializeArray()));
                            $(parent.document.getElementById("inscop")).attr("data-formid",data);
                            $("#formid").val(data);
                        },error: function (jqXHR, textStatus, errorThrown) {
                                 
                        }
                    });
                    $('.required-field-block input').css("border-color","#ccc");
                 }else{
                     $('.required-field-block input').css("border-color","red");
                 }
             }
        </script>
    </body>
</html>

