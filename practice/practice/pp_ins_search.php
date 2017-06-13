<?php
 // Copyright (C) 2005 Rod Roark <rod@sunsetsystems.com>
 //
 // This program is free software; you can redistribute it and/or
 // modify it under the terms of the GNU General Public License
 // as published by the Free Software Foundation; either version 2
 // of the License, or (at your option) any later version.

 // This module is used to find and add insurance companies.
 // It is opened as a popup window.  The opener may have a
 // JavaScript function named set_insurance(id, name), in which
 // case selecting or adding an insurance company will cause the
 // function to be called passing the ID and name of that company.

 // When used for searching, this module will in turn open another
 // popup window ins_list.php, which lists the matched results and
 // permits selection of one of them via the same set_insurance()
 // function.

//SANITIZE ALL ESCAPES
$sanitize_all_escapes=true;

//STOP FAKE REGISTER GLOBALS
$fake_register_globals=false;

require_once("../verify_session.php");


if(isset($_SESSION['portal_username']) !=''){
    $provider    = $_SESSION['portal_username'];
    $refer       = $_REQUEST['refer'];

    if($_REQUEST['refer']!='')
        $_SESSION['refer'] = $_REQUEST['refer'];
}else {
    $provider                    = $_REQUEST['provider'];
    $_SESSION['portal_username'] = $_REQUEST['provider'];
    //for logout
    $refer                       = $_REQUEST['refer'];
    if($_REQUEST['refer']!='')
        $_SESSION['refer'] = $_REQUEST['refer'];
}
 include_once("../../interface/globals.php");
 include_once("$srcdir/acl.inc");
 
  $ignoreAuth=true; 

 // Putting a message here will cause a popup window to display it.
 $info_msg = "";


 $get_refer_user_id = sqlStatement("SELECT id FROM users WHERE username = '".$_REQUEST['refer']."' AND username <> ''");
 $set_refer_user_id = sqlFetchArray($get_refer_user_id); 
 $refer_user_id = $set_refer_user_id['id'];
 
 // This is copied from InsuranceCompany.class.php.  It should
 // really be in a SQL table.
 $freeb_type_array = array(''
  , xl('Other HCFA')
  , xl('Medicare Part B')
  , xl('Medicaid')
  , xl('ChampUSVA')
  , xl('ChampUS')
  , xl('Blue Cross Blue Shield')
  , xl('FECA')
  , xl('Self Pay')
  , xl('Central Certification')
  , xl('Other Non-Federal Programs')
  , xl('Preferred Provider Organization (PPO)')
  , xl('Point of Service (POS)')
  , xl('Exclusive Provider Organization (EPO)')
  , xl('Indemnity Insurance')
  , xl('Health Maintenance Organization (HMO) Medicare Risk')
  , xl('Automobile Medical')
  , xl('Commercial Insurance Co.')
  , xl('Disability')
  , xl('Health Maintenance Organization')
  , xl('Liability')
  , xl('Liability Medical')
  , xl('Other Federal Program')
  , xl('Title V')
  , xl('Veterans Administration Plan')
  , xl('Workers Compensation Health Plan')
  , xl('Mutually Defined')
 );

?>
<html>
<head>
<title><?php xl('Insurance Company Search/Add','e');?></title>
<link rel="stylesheet" href='<?php  echo $css_header ?>' type='text/css'>

<style>
td { font-size:10pt; }
.search { background-color:#aaffaa }

#form_entry {
	display:block;
}

#form_list {
	display:none;
}

</style>

<script type="text/javascript" src="../../library/topdialog.js"></script>
<script type="text/javascript" src="../../library/dialog.js"></script>
<script type="text/javascript" src="../../library/js/jquery.1.3.2.js"></script>
<script src="https://code.jquery.com/jquery-1.11.1.min.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<script language="JavaScript">

 var mypcc = '<?php  echo $GLOBALS['phone_country_code'] ?>';

<?php //require($GLOBALS['srcdir'] . "/restoreSession.php"); ?>

 function doescape(value) {
  return encodeURIComponent(value);
 }

 // This is invoked when our Search button is clicked.
 function dosearch() {

	$("#form_entry").hide();
  	var f = document.forms[0];
	var search_list = 'ins_list.php' +
   '?form_name='   + doescape(f.form_name.value  ) +
   '&form_payer_id='   + doescape(f.form_payer_id.value  ) +
   '&form_attn='   + doescape(f.form_attn.value  ) +
   '&form_addr1='  + doescape(f.form_addr1.value ) +
   '&form_addr2='  + doescape(f.form_addr2.value ) +
   '&form_city='   + doescape(f.form_city.value  ) +
   '&form_state='  + doescape(f.form_state.value ) +
   '&form_zip='    + doescape(f.form_zip.value   ) +
   '&form_phone='  + doescape(f.form_phone.value ) +
   '&form_cms_id=' + doescape(f.form_cms_id.value);

    //top.restoreSession();
    $("#form_list").load( search_list ).show();	

  return false;
 }

 // The ins_list.php window calls this to set the selected insurance.
 function set_insurance(ins_id, ins_name) {
  if (opener.closed || ! opener.set_insurance)
   alert('The target form was closed; I cannot apply your selection.');
  else
   opener.set_insurance(ins_id, ins_name);
//   parent.$.fn.fancybox.close();

   parent.location.reload();
   window.close();
   //top.restoreSession();
 }

 // This is set to true on a mousedown of the Save button.  The
 // reason is so we can distinguish between clicking on the Save
 // button vs. hitting the Enter key, as we prefer the "default"
 // action to be search and not save.
 var save_clicked = false;

 // Onsubmit handler.
 function validate(f) {
  // If save was not clicked then default to searching.
  if (! save_clicked) return dosearch();
  save_clicked = false;

  msg = '';
  if (! f.form_name.value.length ) msg += 'Company name is missing. ';
  if (! f.form_addr1.value.length) msg += 'Address is missing. ';
  if (! f.form_city.value.length ) msg += 'City is missing. ';
  if (! f.form_state.value.length) msg += 'State is missing. ';
  if (! f.form_zip.value.length  ) msg += 'Zip is missing.';
  if (! f.form_payer_id.value.length  ) msg += 'Payer ID  is missing.';
  if (msg) {
   alert(msg);
   return false;
  }

  //top.restoreSession();
  return true;
 }

$(document).ready(function(){ 
    
//    //load datalist
//   var ins_id= $('#id').val();
//        $.ajax({
//            type: 'POST',
//            url: "../../templates/insurance_companies/wp_insurance_search.php",	
//            data:{ins_id:ins_id},
//            success: function(response)
//            {
//                // alert(response);
//                 $('#name_list').html(response);
//            },
//            failure: function(response)
//            {
//                alert("error"); 
//            }		
//        });
     var payer_loaded = false;
     if(!payer_loaded){
        $("#form_name").attr("disabled",true).val("loading...");
        $.ajax({
            dataType: "json",
            type: 'POST',
            url: 'get_payers_auto.php',
            data: {},
            success: function(list) { //document.write(list); return false;
                payer_loaded = true;
                $("#form_name").attr("disabled",false).val("");
                $( "#form_name" ).autocomplete({
                    dataType: 'json',
                    source: function( request, response ) {
                        var matcher = new RegExp( "^" + $.ui.autocomplete.escapeRegex( request.term ), "i" );
                        response( $.grep( list, function( item ){
                            return matcher.test( item.label );
                        }) );
                    },
                    select: function( event, ui ) {
                        $("#form_payer_id").val(ui.item.payerid).attr('disabled',true);
//                        $("#hid_post_id").val(ui.item.postid);
                    },
//                    change: function(event,ui){
//                        $(this).val((ui.item ? ui.item.label : ""));
//                      }
                });
            },
            error: function(jqXHR, exception){
                $("#ajaxloader").hide();
                alert("failed" + jqXHR.responseText);
            }
        });
    }
    
    //for search criteria
//    $("#form_name").on('input', function () {
//        var val = this.value;
//        if(val.length >= 3 ){
//        if($('#ins_name option').filter(function(){
//            return this.value === val;        
//        }).length) {
//            //send ajax request
//           // alert(this.value);
//            $.ajax({
//                type: 'POST',
//                url: "../templates/insurance_companies/wp_insurance_search.php",	
//                data:{ins_name:this.value},
//
//                success: function(response)
//                {
//                  // alert(response);
//                    var res = response.split('&');
//                    var key='';
//                    for( var i=0; i<res.length; i++ )
//                    {   
//                        //alert(res[i]);
//                        var data_val=res[i].split('=');
//                       // alert(data_val[1]);
//                        if(data_val[1]!='') {
//                        
//                        // alert(key);
//                         if(data_val[0]=='address_line1'){
//                            key='form_addr1';
//                        }else if(data_val[0]=='address_line2'){
//                            key='form_addr2';
//                        }else {
//                            key='form_'+data_val[0];
//                        }
//                         document.getElementById(key).value=data_val[1];
//                        }
// 
//                    }
//                    
//                },
//                failure: function(response)
//                {
//                    alert("error"); 
//                }		
//            });
//        }
//        }
//    });
});

function addedit_insurance_attributes()
{
   var insurance_id= jQuery("[name=id]").val();
  
    jQuery.ajax({
                type: 'POST',
                data:{insurance_id:insurance_id},
		url: window.location.origin+"/openemr/templates/insurance_companies/insurance_attributes.php",					
		success: function(response)
		{                 
                     jQuery('#div_patient_insurance_attributes').html(response);                     
		},
		failure: function(response)
		{
			alert("error");
		}		
        });	
        
}

//$(document).ready(function(){
//$("#form_name").keyup(function(event){
//event.preventDefault();
//search_ajax_way();
//});
//
//});
//
//function search_ajax_way(){
//
//var search_this=$("#form_name").val();
//$.post("search.php", {searchit : search_this}, function(data){
//
//$("#ins_name").html(data);
//
//})
//
//}

</script>

</head>

<body class="body_top" onunload='imclosing()'>
<?php
 // If we are saving, then save and close the window.
 //
 if ($_POST['form_save']) {
  $ins_id = '';
  $ins_name = $_POST['form_name'];
    
  
  //api validation
  
  // Initialize cURL
    $curl = curl_init();
// Define URL where the form resides
    $form_url = 'https://www.zirmed.com/Shared/Payers/ViewPayersCSV.aspx';

// This is the data to POST to the form. The KEY of the array is the name of the field. The value is the value posted.
    $data_to_post = array();
    $data_to_post['PayerName']  = $ins_name;
    $data_to_post['GroupBy']    = 'N';
    $data_to_post['Application']= 'All';
    $data_to_post['AllStates' ] = 'on';
    $data_to_post['StateCodes'] = ''; //'ISA*00*0000000   00*0000000000*ZZ*101246         ZZ*ZIRMED         151209*0752*U*00401*000000001*1*P*~^GS*HS*101246*ZIRMED*20151209*075240*000000002*X*004010X092A1^ST*270*000000003^BHT*0022*13*PROVTest600*20151209*075240  ^HL*1**20*1^NM1*PR*2*Medicare B Texas (SMTX0)*****46*ZIRMED^HL*2*1*21*1^NM1*IP*1*Texas Physician House Calls (H)*Perkins*Darolyn***XX*1609272905^REF*4A*^HL*3*2*22*0^TRN*1*1234501*9000000000*5432101^NM1*IL*1*Brown Billy****MI*455040140A^REF*EJ*5979^DMG*D8*19560804^DTP*472*D8*20151209^EQ*30^HL*4*2*22*0^TRN*1*1234502*9000000000*5432102^NM1*IL*1*Wilson*Connie****MI*462561011C5^REF*EJ*1234^DMG*D8*19650919^DTP*472*D8*20151209^EQ*30^HL*5*2*22*0^TRN*1*1234503*9000000000*5432103^NM1*IL*1*Chitwood*Virginia****MI*466204078D^REF*EJ*6000^DMG*D8*19290703^DTP*472*D8*20151209^EQ*30^HL*6*2*22*0^TRN*1 1234504*9000000000*5432104^NM1*IL*1*Rose*Refugia****MI*449667780A^REF*EJ*1389^DMG*D8*19420913^DTP*472*D8*20151209^EQ*30^HL*7*2*22*0^TRN*1*1234505*9000000000*5432105^NM1*IL*1*Finley*Governor****MI*437501408A^REF*EJ*6020^DMG*D8*19380314^DTP*472*D8*20151209^EQ*30^HL*8*2*22*0^TRN*1*1234506*9000000000*5432106^NM1*IL*1*Hanks*Roylene****MI*459845711A^REF*EJ*5932^DMG*D8*19490803^DTP*472*D8*20151209^EQ*30^ HL*9*2*22*0^TRN*1*1234507*9000000000*5432107^NM1*IL*1*Mullen*Jettie****MI*465522005A^REF*EJ*6039^DMG*D8*19340911^DTP*472*D8*20151209^EQ*30^HL*10*2*22*0^TRN*1*1234508*9000000000*5432108^NM1*IL*1*Draper*Charlesetta****MI*464279993A^REF*EJ*2924^DMG*D8*19610413^DTP*472*D8*20151209^EQ*30^HL*11*2*22*0^TRN*1*1234509*9000000000*5432109^NM1*IL*1*Portley*Meladie****MI*462157775A^REF*EJ*5663^DMG*D8 19760403^DTP*472*D8*20151209^EQ*30^HL*12*2*22*0^TRN*1*1234510*9000000000*5432110^NM1*IL*1*Curlin*Franklin****MI*456689901A^REF*EJ*1999^DMG*D8*19421226^DTP*472*D8*20151209^EQ*30^HL*13*2*22*0^TRN*1*1234511*9000000000*5432111^NM1*IL*1*Oliver*Ora*F***MI*464628748A^REF*EJ*5747^DMG*D8*19461126^DTP*472*D8*20151209^EQ*30^HL*14*2*22*0^TRN*1*1234512*9000000000*5432112^NM1*IL*1*Rufus Bessie****MI*450480485A^REF*EJ*5789^DMG*D8*19280502^DTP*472*D8*20151209^EQ*30^HL*15*2*22*0^TRN*1*1234513*9000000000*5432113^NM1*IL*1*Hoffmann*Jerry****MI*452528349A^REF*EJ*5855^DMG*D8*19370409^DTP*472*D8*20151209^EQ*30^HL*16*2*22*0^TRN*1*1234514*9000000000*5432114^NM1*IL*1*West*Rebertha****MI*450983344A^REF*EJ*1119^DMG*D8*19370215^DTP*472*D8*20151209^EQ*30^HL*17*2*22*0^TRN*1 1234515*9000000000*5432115^NM1*IL*1*Wilmore*Elizabeth****MI*450665969A^REF*EJ*3186^DMG*D8*19441120^DTP*472*D8*20151209^EQ*30^HL*18*2*22*0^TRN*1*1234516*9000000000*5432116^NM1*IL*1*Anderson*Dorothy****MI*459748446A^REF*EJ*6046^DMG*D8*19440925^DTP*472*D8*20151209^EQ*30^HL*19*2*22*0^TRN*1*1234517*9000000000*5432117^NM1*IL*1*Shoulder*Joann****MI*436196361A^REF*EJ*6013^DMG*D8*19581011^DTP*472*D8*20151209^EQ*30^ HL*20*2*22*0^TRN*1*1234518*9000000000*5432118^NM1*IL*1*Durant*Tyree*P***MI*562745698A^REF*EJ*5828^DMG*D8*19481206^DTP*472*D8*20151209^EQ*30^HL*21*2*22*0^TRN*1*1234519*9000000000*5432119^NM1*IL*1*Luna*Guadalupe****MI*457042557A^REF*EJ*1245^DMG*D8*19310930^DTP*472*D8*20151209^EQ*30^SE*141*000000003^GE*1*000000002^IEA*1*000000001^';
    $data_to_post['BCBS']       = True;
    $data_to_post['Medicare']   = True;
    $data_to_post['Medicaid']   = True;
    $data_to_post['Commercial'] = True;
    $data_to_post['DMERC']      = True;
    $data_to_post['TRICARE']    = True;

//// Set the options
    curl_setopt($curl,CURLOPT_URL, $form_url);
    //
    //  This sets the number of fields to post
    curl_setopt($curl,CURLOPT_POST, sizeof($data_to_post));

//  This is the fields to post in the form of an array.
    curl_setopt($curl,CURLOPT_POSTFIELDS, $data_to_post);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);  
    //
    ////execute the post
    $result = curl_exec($curl);
    $resultant = $result;
    //
    ////close the connection
    curl_close($curl);

    $exploded_string = explode("\n", $resultant);
                                
    $exploded_csv_result = array();

    if(count($exploded_string)>6){
        $k=0;
        for($i=7; $i<count($exploded_string); $i++){
            $exploded_csv = explode("\",", $exploded_string[$i]);
            for($j=0; $j< count($exploded_csv); $j++){
                $exploded_csv_result[$k][]= trim($exploded_csv[$j],'"');
            }
            $k++;
        }
    }
    $array_size = $k;

    foreach($exploded_csv_result[0] as $key => $value){
        if($value == $ins_name) { 
            $check_payer = 1;
            break;
        }
    }
  
  if($check_payer==1){ 
        $rediect="true";
        if ($ins_id) {
            // sql for updating could go here if this script is enhanced to support
            // editing of existing insurance companies.
       } else {
            $sql=sqlStatement("select * from insurance_companies where payer_id='".$_POST['form_payer_id']."' and name='".$ins_name."'");
            $row=sqlFetchArray($sql);
            if(!empty($row)){
                 echo "<p style='color:red;'>Warning:This Payer name already exists!!</p>";
                 $rediect="false";
            }else {
                $sql_vis     = sqlStatement("SELECT insurance_company from tbl_user_custom_attr_1to1 where userid='".$refer_user_id."'");
                $row1_vis    = sqlFetchArray($sql_vis);   
                $avail4 = explode("|",$row1_vis['insurance_company']);
                if(!in_array("insert",$avail4)){
                    echo "<p style='color:red;'>Warning: You doesn't have access to add insurance company</p>";
                   $rediect="false";
                }
            }
           $ins_id = generate_id();

            sqlInsert("INSERT INTO insurance_companies ( " .
              "id, name, attn, cms_id, freeb_type, x12_receiver_id, x12_default_partner_id ,payer_id" .
              ") VALUES ( " .
              $ins_id                         . ", "  .
              "'" . $ins_name                 . "', " .
              "'" . $_POST['form_attn']       . "', " .
              "'" . $_POST['form_cms_id']     . "', " .
              "'" . $_POST['form_freeb_type'] . "', " .
              "'" . $_POST['form_partner']    . "', " .
              "'" . $_POST['form_partner']    . "', "  .
              "'" . $_POST['form_payer_id']    . "' "  .       
             ")");

             sqlInsert("INSERT INTO addresses ( " .
              "id, line1, line2, city, state, zip, country, foreign_id " .
              ") VALUES ( " .
              generate_id()                . ", "  .
              "'" . $_POST['form_addr1']   . "', " .
              "'" . $_POST['form_addr2']   . "', " .
              "'" . $_POST['form_city']    . "', " .
              "'" . $_POST['form_state']   . "', " .
              "'" . $_POST['form_zip']     . "', " .
              "'" . $_POST['form_country'] . "', " .
              $ins_id                      . " "   .
             ")");

             $phone_parts = array();
             preg_match("/(\d\d\d)\D*(\d\d\d)\D*(\d\d\d\d)/", $_POST['form_phone'],
              $phone_parts);

             sqlInsert("INSERT INTO phone_numbers ( " .
              "id, country_code, area_code, prefix, number, type, foreign_id " .
              ") VALUES ( " .
              generate_id()         . ", "  .
              "'+1'"                . ", "  .
              "'" . $phone_parts[1] . "', " .
              "'" . $phone_parts[2] . "', " .
              "'" . $phone_parts[3] . "', " .
              "'2'"                 . ", "  .
              $ins_id               . " "   .
             ")");


             $date= date("Y-m-d") ;
             //insurance comapny log
              sqlInsert("INSERT INTO insurance_company_log ( " .
              "date, user_id, action, insurance_id, post_type,domain" .
              ") VALUES ( " .
              "'". $date. "', "  .
              "'". $refer_user_id. "', "  .
              "'"   .'insert'. "', " .
              "'" . $ins_id . "', " .
               "'" . 'payer' . "'," .       
              "'" . 'EMR' . "'" .

             ")"); 

             //save data to wordpress db
            $list=sqlStatement("select * from list_options where list_id='AllcareWPDB'");
            while ($list_val=sqlFetchArray($list)) {
                //wordpress db connection
                if($list_val['option_id']=='host'){
                    $dbhost=$list_val['notes'];
                }
                if($list_val['option_id']=='user'){
                     $dbuser=$list_val['notes'];
                }
                if($list_val['option_id']=='pwd') {
                    $dbpass=$list_val['notes'];
                }
                if($list_val['option_id']=='dbname'){
                    $dbname=$list_val['notes']; 
                }
            }

            $conn = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
            // Check connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            } 
            global $wpdb;
            $post_title=$_POST['form_name'].':'.$_POST['form_payer_id'];

            $result = $conn->query("SELECT * from wp_eligibility_posttypes where post_type='payer'");

            if ($result->num_rows == 0) {
                $ins = $conn->query("insert into wp_eligibility_posttypes (post_type) values ('payer')");
            }
            $sql1=$conn->query("select ID from wp_posts where post_type='payer' and post_title='$post_title'");
            if($sql1->num_rows == 0){
                $today = date("Y-m-d H:i:s");   
               // echo "insert into wp_posts (post_date,post_title,post_type) values ('$today','$post_title','payer')";
                $ins = $conn->query("insert into wp_posts (post_date,post_title,post_type) values ('$today','$post_title','payer')");
                $se12l=$conn->query("select ID  from wp_posts where post_title='$post_title' and post_type='payer'");
                $obj12 = $se12l->fetch_object();
                //echo "insert into wp_payer_log (date,user_id,action,post_id,post_type,domain) values ('$date','".$refer_user_id."','insert',$obj12->ID,'payer','EMR')"; 
                //wordpress log
                 $ins_log = $conn->query("insert into wp_payer_log (date,user_id,action,post_id,post_type,domain) values ('$date','".$refer_user_id."','insert',$obj12->ID,'payer','EMR')");
            }else { 
                $today = date("Y-m-d H:i:s");    
               // echo "UPDATE wp_posts SET post_date='$today' where post_title='$post_title' AND post_type='payer'";
                $ins = $conn->query("UPDATE wp_posts SET post_date='$today' where post_title='$post_title' AND post_type='payer'");

                $se12l=$conn->query("select ID  from wp_posts where post_title='$post_title' and post_type='payer'");
                $obj12 = $se12l->fetch_object();
                //wordpress log
                 $update_log = $conn->query("insert into wp_payer_log (date,user_id,action,post_id,post_type,domain) values ('$date','".$refer_user_id."','update',$obj12->ID,'payer','EMR')");

            }
            $sel=$conn->query("select ID  from wp_posts where post_title='$post_title' and post_type='payer'");
            $obj = $sel->fetch_object();


            foreach($_REQUEST as $key1 => $value){
                 if($key1!='OpenEMR' && $key1!='X-Mapping-moenbdmd' && $key1!='form_save'){
                    $key2=explode("_",$key1);
                    if($key2[1]=='addr1'){
                        $key='address_line1';
                    }else if($key2[1]=='addr2'){
                        $key='address_line2';
                    }else if($key2[1]=='payer') {
                        $key=$key2[1]."_".$key2[2];
                    }else if($key2[1]=='cms') {
                        $key=$key2[1]."_".$key2[2];
                    }else if($key2[1]=='freeb') {
                        $key=$key2[1]."_".$key2[2];
                    }else {
                        $key=$key2[1];
                    }
                    $sel2=$conn->query("select * from wp_postmeta where post_id=$obj->ID and meta_key='$key' ");
                    if($sel2->num_rows == 0){
                        $ins_meta = $conn->query("insert into wp_postmeta (post_id,meta_key,meta_value) values ($obj->ID,'$key','$value')");
                    }else {
                        $update1=$conn->query("update wp_postmeta SET meta_value='$value' where post_id='$obj->ID'  and meta_key='$key'");
                    }
                 }

            }

        }  
        // Close this window and tell our opener to select the new company.
        //
        if($rediect=='true'){
            echo "<script language='JavaScript'>\n";
            if ($info_msg) echo " alert('$info_msg');\n";
            echo " if (parent.set_insurance) parent.set_insurance($ins_id,'$ins_name');\n";
            echo " window.close();\n";
            echo " parent.location.reload();\n";
            echo "</script></body></html>\n";
            exit();
        }
  }else {
        echo "<p style='color:red;'>Warning:Payer Name is not valid!!</p>";
  }
  
  

  
          
  

  
 }

 // Query x12_partners.
 $xres = sqlStatement(
  "SELECT id, name FROM x12_partners ORDER BY name"
 );

?>
<div id="form_entry">

<form method='post' name='theform' action='pp_ins_search.php?refer=<?php echo $refer; ?>'
 onsubmit='return validate(this)'>
<center>

<a onclick="javascript:jQuery('#div_patient_insurance').show();
                       jQuery('.tabNav > li').removeClass('current');
                       jQuery(this).parent('li').addClass('current');" style='cursor:pointer;border:1px SOLID #000;'> Company Details</a>
<br><br>


<p>
<div id="div_patient_insurance">

<table border='0' width='100%'>

 <!--
 <tr>
  <td valign='top' width='1%' nowrap>&nbsp;</td>
  <td>
   Note: Green fields are searchable.
  </td>
 </tr>
 -->

 <tr>
  <td valign='top' width='1%' nowrap><b><?php xl('Name','e');?>:</b></td>
  <td>
      <input type='text' size='20' list="ins_name" name='form_name' id='form_name'  maxlength='200' autocomplete="off"
    class='search' style='width:100%' title=<?php xl('Name of insurance company','e');?> /><datalist id="ins_name"></datalist>
  </td>
 </tr>
 
 <tr>
  <td valign='top' width='1%' nowrap><b><?php xl('Payer ID','e');?>:</b></td>
  <td>
   <input type='text' size='20' name='form_payer_id' id='form_payer_id' maxlength='35' autocomplete="off"
    class='search' style='width:100%' title=<?php xl('Payer ID','e');?> />
  </td>
 </tr> 
 
 <tr>
  <td valign='top' nowrap><b><?php xl('Attention','e');?>:</b></td>
  <td>
   <input type='text' size='20' name='form_attn'  id='form_attn' maxlength='35'
    class='search' style='width:100%' title=".xl('Contact name')." />
  </td>
 </tr>

 <tr>
  <td valign='top' nowrap><b><?php xl('Address1','e');?>:</b></td>
  <td>
   <input type='text' size='20' name='form_addr1' id='form_addr1'  maxlength='35'
    class='search' style='width:100%' title='First address line' />
  </td>
 </tr>

 <tr>
  <td valign='top' nowrap><b><?php xl('Address2','e');?>:</b></td>
  <td>
   <input type='text' size='20' name='form_addr2' id='form_addr2' maxlength='35'
    class='search' style='width:100%' title='Second address line, if any' />
  </td>
 </tr>

 <tr>
  <td valign='top' nowrap><b><?php xl('City/State','e');?>:</b></td>
  <td>
   <input type='text' size='20' name='form_city' id='form_city' maxlength='25'
    class='search' title='City name' />
   &nbsp;
   <input type='text' size='3' name='form_state' id='form_state' maxlength='35'
    class='search' title='State or locality' />
  </td>
 </tr>

 <tr>
  <td valign='top' nowrap><b><?php xl('Zip/Country:','e'); ?></b></td>
  <td>
   <input type='text' size='20' name='form_zip'  id='form_zip'  maxlength='10'
    class='search' title='Postal code' />
   &nbsp;
   <input type='text' size='20' name='form_country' id='form_country'  value='USA' maxlength='35'
    title='Country name' />
  </td>
 </tr>

 <tr>
  <td valign='top' nowrap><b><?php xl('Phone','e');?>:</b></td>
  <td>
   <input type='text' size='20' name='form_phone' id='form_phone' maxlength='20'
    class='search' title='Telephone number' />
  </td>
 </tr>

 <!--
 <tr>
  <td valign='top' width='1%' nowrap>&nbsp;</td>
  <td>
   &nbsp;<br><b>Other data:</b>
  </td>
 </tr>
 -->

 <tr>
  <td valign='top' nowrap><b><?php xl('CMS ID','e');?>:</b></td>
  <td>
   <input type='text' size='20' name='form_cms_id' id='form_cms_id' maxlength='15'
    class='search' title='Identifier assigned by CMS' />
  </td>
 </tr>

 <tr>
  <td valign='top' nowrap><b><?php xl('Payer Type','e');?>:</b></td>
  <td>
   <select name='form_freeb_type' id='form_freeb_type'>
<?php
 for ($i = 1; $i < count($freeb_type_array); ++$i) {
  echo "   <option value='$i'";
  // if ($i == $row['freeb_type']) echo " selected";
  echo ">" . $freeb_type_array[$i] . "\n";
 }
?>
   </select>
  </td>
 </tr>

 <tr>
  <td valign='top' nowrap><b><?php xl('X12 Partner','e');?>:</b></td>
  <td>
   <select name='form_partner' title='Default X12 Partner' id='form_partner'>
    <option value=""><?php xl('None','e','-- ',' --'); ?></option>
<?php
 while ($xrow = sqlFetchArray($xres)) {
  echo "   <option value='" . $xrow['id'] . "'";
  // if ($xrow['id'] == $row['x12_default_partner_id']) echo " selected";
  echo ">" . $xrow['name'] . "</option>\n";
 }
?>
   </select>
  </td>
 </tr>

</table>
</div>

<p>&nbsp;<br>
<!--<input type='button' value='' class='search' onclick='dosearch()' />-->
&nbsp;
<input type='submit' value='<?php xl('Save as New','e'); ?>' name='form_save' onmousedown='save_clicked=true' />
&nbsp;
<!--<input type='button' value='<?php xl('Cancel','e'); ?>' onclick='parent.$.fn.fancybox.close();'/>-->
</p>

</center>
</form>
</div>
</body>
</html>
