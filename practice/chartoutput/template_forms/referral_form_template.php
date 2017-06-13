<?php
 // Copyright (C) 2011 Cassian LUP <cassi.lup@gmail.com>
 // 
 // Moved out of individual get_* portal functions for re-use by
 // Kevin Yeh (kevin.y@integralemr.com) May 2013
 //
 // This program is free software; you can redistribute it and/or
 // modify it under the terms of the GNU General Public License
 // as published by the Free Software Foundation; either version 2
 // of the License, or (at your option) any later version.
 // 
 // 
 
    // All of the common intialization steps for the get_* patient portal functions are now in this single include.

    //SANITIZE ALL ESCAPES
    $sanitize_all_escapes=true;

    //STOP FAKE REGISTER GLOBALS
    $fake_register_globals=false;

    //continue session
    session_start();

    //landing page definition -- where to go if something goes wrong 
    $landingpage = "index.php?site=".$_SESSION['site_id'];	
    //

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
include_once('../../../interface/globals.php'); 
include_once("$srcdir/sql.inc");

$transid=$_REQUEST['coid'];
$patientid=$_REQUEST['patient_id'] ? $_REQUEST['patient_id']:0;
$temp_id=$_REQUEST['temp_id'];
$group=$_REQUEST['group'] ? $_REQUEST['group'] : $_REQUEST['group1'] ;

?>
<html>
    <head>
        <style>
            .text-line {
                background-color: transparent;
                outline: none;
                outline-style: none;
                outline-offset: 0;
                border-top: none;
                border-left: none;
                border-right: none;
                border-bottom: solid black 1px;
                padding: 3px 10px;
            }
            //non encounter data
             @page { size 8.5in 11in; margin: 2cm; }
            div.page { page-break-before: always }
            ul
            {
                list-style-type: none;
                -webkit-padding-start: 0px !important;

            }
            li { padding-right:40px; }
            ul{float:left;}
            select {
                border: 1px solid #111;
                background: transparent;
                padding: 5px;
                font-size: 16px;
                border: 1px solid #ccc;
                height: 34px;
                -webkit-appearance: none;
                -moz-appearance: none;
                appearance: none;
            }
            @media print 
            {
                .hidden-print { display: none; }

            }
        </style>    
        <script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/jquery.1.3.2.js"></script>
          <script>
            
            $(document).ready(function () {
                var print_val='<?php echo $_REQUEST['print']; ?>';
                var form = document.getElementById("referral");
                var elements = form.elements;
                for (var i = 0, len = elements.length; i < len; ++i) {
                    elements[i].disabled = true;
                }
                document.getElementById('save').disabled = false;  
                 <?php  $sql1=sqlStatement("select * from  list_options where list_id='form_templates' and option_id=$temp_id");
                        $row1=sqlFetchArray($sql1);
                        
                        $list_id=str_replace(" ","",$row1['title'])."Mapping";
                        
                        $sql=sqlStatement("select * from list_options where list_id='$list_id' and notes='E'");
                        while ($row=sqlFetchArray($sql)) { 
                            if($row['notes']=='E') { ?>
                                   
                                    if(print_val==1){
                                        document.getElementById('save').style.display = 'none';  
                                    }else {
                                        var name2='<?php echo $row['title']; ?>';
                                        var n = name2.indexOf("radio");
                                        if(n!='-1'){
                                            var fname=name2.split("_");
                                            var x=document.getElementsByName(fname[0]).length;
                                            for (var j = 0, len = document.getElementsByName(fname[0]).length; j< len; ++j) {
                                                 document.getElementsByName(fname[0])[j].disabled = false;
                                            }
                                        }else {
                                             document.getElementById('<?php echo $row['title']; ?>').disabled = false;
                                        }
                                       
                                       
                                        
                                    }
                                    <?php if($transid!=0) { 
                                         if(strpos($row['title'],'radio')!='false'){
                                              $nam=explode("_",$row['title']);
                                              $title=$nam[0];
                                         }else {
                                              $title=$row['title'];
                                         }
                                         
                                         $sql3=sqlStatement("select * from tbl_form_template_editvalues where pid=$patientid and transaction_id=$transid and form_name='".$row1['title']."' and form_field='".$title."'");
                                         $row3=sqlFetchArray($sql3); if ($row3['form_value']!='') { ?>
//                                          var name2='<?php echo $row3['form_field']; ?>';
//                                         
//                                         var n = name2.indexOf("radio");       
//                                         if(n!='-1'){
//                                             
//                                             var fname=name2.split("_");
//                                             
//                                             var db_val=<?php  echo $row3['form_value'];  ?>;
//                                             for (var j = 1, len = document.getElementsByName(fname[0]).length; j< len; ++j) {
//                                                var rid=fname[0]+j;
//                                                alert(rid+$('#rid').val());
//                                                if($('#rid').val()==db_val) document.getElementById(rid).checked = true;
//                                             }
//                                         }else{
//                                            document.getElementById('<?php echo $row3['form_field']; ?>').value='<?php  echo $row3['form_value'];  ?>';
//                                         }
                                            
                                           
                                            if(document.getElementsByName('<?php echo $row3['form_field']; ?>').length>1){
                                                 for (var j =1, len = document.getElementsByName('<?php echo $row3['form_field']; ?>').length; j<=len; ++j) {
                                                   var rid='<?php echo $row3['form_field']; ?>'+j;
                                                    var db_val='<?php  echo $row3['form_value'];  ?>';     
                                                   
                                                    if($('#'+rid).val()==db_val) {  document.getElementById(rid).checked = true; }
                                                }
                                            }else {
                                                
                                                 document.getElementById('<?php echo $row3['form_field']; ?>').value='<?php  echo $row3['form_value'];  ?>';
                                            }
                                         
                                    <?php } 
                                    
                                         } 

                             } 
                        
                         } ?> 
                 
                $('input[type="submit"]').click(function(){
                    var params = '';
//                    for( var i=0; i<document.referral.elements.length; i++ )
//                    {
//                       var fieldName = document.referral.elements[i].name;
//                       var fieldValue = document.referral.elements[i].value;
//
//                       // use the fields, put them in a array, etc.
//
//                       // or, add them to a key-value pair strings, 
//                       // as in regular POST 
//
//                       params += fieldName + '=' + fieldValue + '&';
//                    }
                     params = $("#referral").serialize();
                     var params1=params+'&patient_id='+$('#patient_id').val()+'&coid='+$('#coid').val()+'&mode='+$('#mode').val()+'&transid='+$('#transid').val();
//                     alert(params+'&patient_id='+$('#patient_id').val()+'&coid='+$('#coid').val()+'&mode='+$('#mode').val()+'&transid='+$('#transid').val()); 
                    //for passing data to chartouput page /parent page  
                    window.parent.formdata(params1);
                });
                });    
               
       
        </script>
    </head>
    <body>
        <?php
        if($transid==0){
            $trans=sqlStatement("select max(id) as id from tbl_nonencounter_data " );
            $trans_row=sqlFetchArray($trans);
            $new_id=$trans_row['id']+1;
        }
         if($patientid!='') {
            $data=sqlStatement("select * from patient_data where pid=$patientid");
            $prow=sqlFetchArray($data); 
             
        }
        ?>
        <form name="referral" id="referral" action="../chart_output.php" method="post" >
            <div>
                
                <input type="submit" id="save" name="save" value="save" class="hidden-print" />
                <input type="hidden" id="patient_id" name="patient_id" value="<?php echo $_REQUEST['patient_id'] ?>" />
                <input type="hidden" id="coid" name="coid" value="<?php echo $_REQUEST['coid'] ?>" />
                <input type="hidden" id="mode" name="mode" value="add" />
                <input type="hidden" id="transid" name="transid" value="<?php echo $new_id; ?>" />
                <center><h3 style="margin:0px !important">Texas Physician House Calls</h3><hr style="border-width: 3px ! important ; margin:0px !important">
                    <table>
                        <tr><td align="center">Sumana Ketha M.D.</td></tr>
                        <tr><td align="center"><i>Board Certified in internal Medicine</i></td></tr>
                        <tr><td align="center">2925 Skyway circle North,</td></tr>
                        <tr><td align="center">Irving, Texas 75038</td></tr>
                        <tr><td align="center">HHSUPPORT@TEXASHOUSECALLS.COM</td></tr>
                    </table>
                </center>
                <br>
                <table>
                    <tr><td><b>Referral source:</b><input type="text" name="referral_source" id="referral_source" value="<?php echo $prow['referral_source']; ?>" class="text-line"/></td><td><b>Date:</b><input type="text" name="referred_date" id="referred_date" value="<?php echo $prow['referred_date']; ?>" class="text-line" /></td></tr>
                    <tr><td><b>Agent:</b><input type="text" name="agent" id="agent" value="" class="text-line"/></td><td><b>Phone:</b><input type="text" name="phone" id="phone" value="" class="text-line"/></td><td><b>Fax:</b><input type="text" name="Fax" id="Fax" value="" class="text-line"/></td></tr>
                    <tr><td><b>Home Health Agency:</b><input type="text" name="hhagency" id="hhagency" value="<?php echo $prow['hhagency']; ?>" class="text-line"/></td></tr>
                </table>
                <br>
                <b><u>Patient Information</u></b><br>
                <table>
                    <tr>
                        <td><b>Patient (Last name):</b><input type="text" name="lname" id="lname" value="<?php echo $prow['lname']; ?>" class="text-line"/></td>
                        <td><b>(First Name):</b><input type="text" name="fname" id="fname" value="<?php echo $prow['fname']; ?>" class="text-line"/></td>
                        <td><b>(Middle Initial):</b><input type="text" name="mname" id="mname" value="<?php echo $prow['mname']; ?>" class="text-line"/></td>
                    </tr>
                    <tr>
                        <td><b>D.O.B.:</b><input type="text" name="DOB" id="DOB" value="<?php echo $prow['DOB']; ?>" class="text-line"/></td> 
                        <td><b>SSN:</b><input type="text" name="ss" id="ss" value="<?php echo $prow['ss']; ?>" class="text-line"/></td> 
                        <td><b>M:</b><input type="radio" name="sex" id="sex1" value="Male" <?php if($prow['sex']=='Male') echo "checked"; ?> />F:</b><input type="radio" name="sex" id="sex2" value="Female" <?php if($prow['sex']=='Female') echo "checked"; ?>/><b>Phone:</b><input type="text" name="patient_phone" id="patient_phone" value="<?php if($prow['phone_cell']!=''){ echo $prow['phone_cell']; }else if($prow['phone_home']!=''){ echo $prow['phone_home']; }?>" class="text-line"/></td> 
                    </tr>
                    <tr>
                        <td><b>Address: </b><input type="text" name="street" id="street" value="<?php echo $prow['street']; ?>" class="text-line"/></td>
                        <td><b>City:</b><input type="text" name="city" id="city" value="<?php echo $prow['city']; ?>" class="text-line"/></td>
                        <td><b>ZIP:</b><input type="text" name="postal_code" id="postal_code" value="<?php echo $prow['postal_code']; ?>" class="text-line"/></td>
                    </tr>
                    <tr>
                        <td><b>Race: </b><input type="text" name="race" id="race" value="<?php echo $prow['race']; ?>" class="text-line"/></td>
                        <td><b>Language :    Spanish / English /  Other : </b><input type="text" name="language" id="language" value="<?php echo $prow['language']; ?>" class="text-line"/></td>
                    </tr>
                    <tr>
                        <td><b>Alternate Contact: </b><input type="text" name="alt_contact" id="alt_contact" value="" class="text-line"/></td>
                        <td><b>Relationship:</b><input type="text" name="relation" id="relation" value="" class="text-line"/></td>
                        <td><b>Phone:</b><input type="text" name="patient_phone1" id="patient_phone1" value="" class="text-line"/></td>
                    </tr>
                    <tr>
                        <td><b>Patient Email Address:</b><input type="email" name="email" id="email" value="<?php echo $prow['email']; ?>" class="text-line"/></td>
                    </tr>
                    <tr><td><b>Medical Reason for referral:</b><input type="text" name="med_reason" id="med_reason" value="" class="text-line"/></td></tr>
                    <tr><td><b>Patient diagnosis:</b><input type="text" name="diag" id="diag" value="" class="text-line"/></td></tr> 
                    <tr><td><b>Is Patient Homebound?</b><input type="radio" name="hb" id="hb1" value="YES" />YES<input type="radio" name="hb" id="hb2" value="NO" />NO</td><td><b>Needs Home Health:</b><input type="radio" name="hh" id="hh1" value="YES" />YES<input type="radio" name="hh" id="hh1" value="NO" />NO</td></tr>
                    <tr><td><b>How soon does the patient need to be seen?</b><input type="text" name="seen" id="seen" value="" class="text-line"/></td></tr>
                </table>
                <br>
                <b><u>INSURANCE INFORMATION:</u></b><br>
                 <?php $ins1=sqlStatement("SELECT * FROM  `insurance_data` WHERE pid =$patientid and type='primary'");
                       $insdata1=sqlFetchArray($ins1);
                       //insurance companies
                       $sql3=sqlStatement("select name from insurance_companies where id='".$insdata1['provider']."'");
                       $company2=sqlFetchArray($sql3);
                       
                       $ins2=sqlStatement("SELECT * FROM  `insurance_data` WHERE pid =$patientid and type='secondary'");
                       $insdata2=sqlFetchArray($ins2);
                 ?>
                <table>
<!--                    <tr><td><b>Primary insurance:</b><input type="text" name="primary_ins" id="primary_ins" value="<?php echo $company2['name']; ?>" class="text-line"/></td></tr>-->
                     <tr><td><b>Primary insurance:</b><select id="provider" name="provider" class="text-line"><option value=""></option><?php while( $inscomp_data=sqlFetchArray($ins_comp)) { ?><option value="<?php echo $inscomp_data['id']; ?>"><?php echo $inscomp_data['name']; ?></option> <?php }?></select></td></tr>
                    <tr><td><b>Medicare #:</b><input type="text" name="medicare" id="medicare" value="" class="text-line"/></td><td><b>Medicaid #:</b><input type="text" name="medicaid" id="medicaid" value="" class="text-line"/></td></tr>
                    <tr><td><b>Secondary:</b><input type="text" name="sec_ins" id="sec_ins" value="" class="text-line"/></td><td><b>Policy/Group #:</b><input type="text" name="policy_number" id="policy_number" value="<?php echo $$insdata2['policy_number']; ?>" class="text-line"/></td><td><b>Effective date:</b><input type="date" name="" id="" value="" class="text-line"/></td></tr>
                </table>
                <br>
                <b>&bull;Copies of Medicare and insurance cards</b><br><br>
                <b>Referral signature:</b><input type="text" name="sign" id="sign" value="" class="text-line"/><b>Date:</b><input type="text" name="sign_date" id="sign_date" value="" class="text-line"/>
                <br><h3>Referral Fax line (972) 675 7310 Main: (972) 675 7313 ext 105</h3>
                <p>All inquiries will be responded in 24 hours during work days, For all electronic inquiries please send secure email to hhsupport@texashousecalls.com. Thank you for your referral.</p>
                <p>You can also complete the Referral form online at https://www.texashousecalls.com/request-a-housecall/</p>
            </div>
          </form>
        <hr style="border-width: 8px ! important ;" class="hidden-print">
        <p style="page-break-before:always;"></p>
         <?php if($transid!='0') include('non_encounter_chart.php'); else if($transid=='0') include('preview_non_encounter_chart.php');
        if($_REQUEST['print']==1){ ?>
          <script type="text/javascript">
             window.print();
           </script>
        <?php } ?>
    </body>
</html>