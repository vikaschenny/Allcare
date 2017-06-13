<?php  
header('Access-Control-Allow-Origin: *');
/* 
Webservice for user/provider authentication with openemr
*/

// do initial application
//require_once("../interface/globals.php");
require '../Slim/Slim.php';
require '../Slim/Route.php';
require __DIR__ . '/RNCryptor-php-3.0.4/autoload.php';

ini_set('post_max_size', '1000M');
ini_set('upload_max_filesize', '200M');
ini_set('max_execution_time', 600); 

// initialize app 
$app = new Slim();

// to get patient eligibility
$app->get('/checkeligibility/:pid/:month', 'checkeligibility');
// to get patient eligibility
$app->get('/createmedicalrecordPDF/:pid/:dos/:provider', 'createMedicalRecordPDF');
// to save upload html pdf
$app->get('/savetodrive','saveToDrive');
// to get previous versions of forms
$app->get('/getprevious/:form_name/:pid/:form_id/:encounter', 'getPreviousTranscriptions');

//// to upload file 
//$app->post('/voicefileupload', 'MobileFileUpload');
//// to get files list 
//$app->get('/getvoicefileslist/:pid/:eid/:form_name','getDictationFiles');
//// to rename dictation file
//$app->post('/renamefile','renameDictationFile');
//// to delete dictation file
//$app->post('/deletefile','deleteDictationFile');
$app->run();

// connection to openemr database
function getConnection() 
{

	$dbhost="smartmbbsdb.cklyp7uk4jgt.us-west-2.rds.amazonaws.com";
	$dbuser="allcaretphc";
	$dbpass="Db0Em4DbDfRrP0d";
	$dbname="allcaretphc";			
  
	$dbh = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);	
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	return $dbh;
}
function insertMobileLog($event,$user,$comment,$patient,$encounter,$user_notes,$success){    

    $sql = "INSERT INTO mbl_log(date,event,user,groupname,comments,patient_id,encounter_id,user_notes,success) values(NOW(),'".$event."','".$user."','Default','".addslashes($comment)."','".$patient."','".$encounter."','".$user_notes."',$success)";
    $db = getConnection();
    $stmt = $db->prepare($sql);
    $stmt->execute();
    return true;
}

function encode_demo($demo)
{    
      
	$newdemo=array();
	
	if(count($demo) > 0 && !empty($demo))
	{
        $demo['Data']=array_values((array)$demo);
	    $demo['DataAvailable'] = "YES"; 	    
	}
	else
	{ 
	    $demo['DataAvailable'] = "NO";	    
	}
        
	//$rs = json_encode($demo);
	//$newdemo = json_decode($rs,true);
	return $demo;

}  
function check_data_available($newdemoArray)
{
	
	if($newdemoArray['DataAvailable'] == "YES")
	{			
		return $newdemoArray;
	}
	else
	{
		return json_decode('{"DataAvailable":"NO"}');
	}
}
function url(){
    if(isset($_SERVER['HTTPS'])){
        $protocol = ($_SERVER['HTTPS'] && $_SERVER['HTTPS'] != "off") ? "https" : "http";
    }
    else{
        $protocol = 'http';
    }
    return $protocol . "://" . $_SERVER['SERVER_NAME'];
}
function saveToDrive($pid,$encounter_id,$provider,$temp_file_name){

    
    $db = getConnection();

    $curl = curl_init();
    
    $site_url = url();
    
    // Define URL where the form resides
    $form_url = "$site_url/api/DriveSync/uploadMobilePDFHTML";

    // This is the data to POST to the form. The KEY of the array is the name of the field. The value is the value posted.
    $data_to_post = array();

    // to get email id 
    $get_email_query = 'select notes from list_options where list_id="AllcareDriveSync" and option_id = "email"';
    $stmt_sql_array = $db->prepare($get_email_query) ; 
    $stmt_sql_array->execute();
    $set_email_query = $stmt_sql_array->fetchAll(PDO::FETCH_OBJ);

    $drive_email = isset($set_email_query[0]->notes)? $set_email_query[0]->notes: '';
    // to get folder id 
    $get_folder_query = "select drive_sync_folder from tbl_user_custom_attr_1to1 where userid= '$provider'";
    $stmt_sql_array = $db->prepare($get_folder_query) ; 
    $stmt_sql_array->execute();
    $set_folder_query = $stmt_sql_array->fetchAll(PDO::FETCH_OBJ);
    $folder_id = isset($set_folder_query[0]->drive_sync_folder)? $set_folder_query[0]->drive_sync_folder : '';

    // to get username of provider
    $get_username_query = "select username from users where id= '$provider'";
    $stmt_sql_array = $db->prepare($get_username_query) ; 
    $stmt_sql_array->execute();
    $set_username_query = $stmt_sql_array->fetchAll(PDO::FETCH_OBJ);
    $username = isset($set_username_query[0]->username)? $set_username_query[0]->username : '';

    // filename based on configuration
    $patient_file_format_query = "select * from tbl_drivesync_authentication where email='$drive_email' order by id desc";
    $stmt_sql_array = $db->prepare($patient_file_format_query) ; 
    $stmt_sql_array->execute();
    $set_patient_file_format_query = $stmt_sql_array->fetchAll(PDO::FETCH_OBJ);
//        $set_patient_file_format = isset($set_patient_file_format_query[0]->username)? $set_patient_file_format_query[0]->username : '';

    $file_name = '';
//    if (is_numeric(strpos($set_patient_file_format_query[0]->patient_file_format, "dos"))) {
//            $query = $set_patient_file_format_query[0]->patient_file . " as file_name where c.pid=" . $pid . " and c.id=" . $trans;
//            $query = $set_patient_file_format_query[0]->patient_file . " as file_name where p.pid=" . $pid;
        $file_format = $set_patient_file_format_query[0]->patient_file_format;
        $exploded_format = explode("_", $file_format);
        $file_name_string = '';

        // get patient info
        $get_patient_query = "select fname,lname,mname,dob from patient_data where pid = '$pid'";
        $stmt_sql_array = $db->prepare($get_patient_query) ; 
        $stmt_sql_array->execute();
        $set_patient_query = $stmt_sql_array->fetchAll(PDO::FETCH_OBJ);
//            $encounter_date = isset($set_patient_query[0]->date)? $set_encounter_date_query[0]->date : '';
//echo  count($exploded_format);                    
        for($inc = 0; $inc< count($exploded_format); $inc++){
            if($exploded_format[$inc] =='dos'){
                // get encounter date
                $get_encounter_date_query = "select DATE_FORMAT(date,'%Y-%m-%d') as date from form_encounter where encounter= '$encounter_id'";
                $stmt_sql_array = $db->prepare($get_encounter_date_query) ; 
                $stmt_sql_array->execute();
                $set_encounter_date_query = $stmt_sql_array->fetchAll(PDO::FETCH_OBJ);
                $encounter_date = isset($set_encounter_date_query[0]->date)? $set_encounter_date_query[0]->date : '';

                $file_name_string .= str_replace("-","",$encounter_date)."_";
            }
            else if($exploded_format[$inc] == 'dob'){
                $file_name_string .= $set_patient_query[0]->dob."_";
            }
            else if($exploded_format[$inc] == 'fname'){
                $file_name_string .= $set_patient_query[0]->fname."_";
            }
            else if($exploded_format[$inc] == 'lname'){
                $file_name_string .= $set_patient_query[0]->lname."_";
            }
            else if($exploded_format[$inc] == 'mname'){
                $file_name_string .= $set_patient_query[0]->mname."_";
            }
            else if($exploded_format[$inc] == 'pid'){
                $file_name_string .= $pid."_";
            }
        }

        $file_name = rtrim($file_name_string,"_");
//    } 


    $data_to_post['useremail']          = $drive_email ; //'hemasrit@smartmbbs.com';
//        $data_to_post['encodedURL']         = $encoded_string;
    $data_to_post['folderid']           = $folder_id; //'0B9MTlL-8l-n5aDZ2NV9hNmpGWmM';
    $data_to_post['filename' ]          = $file_name;
    $data_to_post['temp_file_name']    = $temp_file_name;
//exit();   
    //// Set the options
    curl_setopt($curl,CURLOPT_URL, $form_url);
    //
    //  This sets the number of fields to post
    curl_setopt($curl,CURLOPT_POST, sizeof($data_to_post));

    curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);

    curl_setopt($curl,CURLOPT_POSTFIELDS, $data_to_post);

    $result = curl_exec($curl);
    echo $file_id = str_replace("File ID: ", "",$result);
    //
    ////close the connection
    curl_close($curl);
    $folder_link = addslashes('https://drive.google.com/drive/folders/'.$folder_id);

    // insert log in DriveSync_log table
    $ins_log = "insert into DriveSync_log (date,user,email,encounter,patient_id,google_folder,file_name,file_id,status,watsID)values(now(),'".$username."','".$drive_email."','".$encounter_id."',$pid,'$folder_link','".$file_name."','$file_id','file Created(Mobile)','')";
    $stmt_sql_array = $db->prepare($ins_log) ; 
//        $stmt_sql_array->execute();
    
}


function createMedicalRecordPDF($pid,$encounter_id,$provider){
    try{
        $db = getConnection();
        
        $display_string2 = '';
        
        $getlayout_fields = "SELECT * FROM layout_options WHERE form_id = 'CHARTOUTPUT' AND uor > 0 AND field_id != ''  AND group_name LIKE '%Mobile' ORDER BY seq";
        $stmt_sql = $db->prepare($getlayout_fields) ; 
        $stmt_sql->execute();
        $getlayout_fields = $stmt_sql->fetchAll(PDO::FETCH_OBJ);
//        while($setlayout = sqlFetchArray($getlayout_fields)){   
        foreach ($getlayout_fields as $setlayout_key => $setlayout_value) {
//            $result[$setlayout['seq']]=$setlayout['field_id'];
            $result[$setlayout_value->seq]=$setlayout_value->field_id;
        }
        $get_layout_fields = "SELECT option_value,field_id,group_name FROM tbl_chartui_mapping WHERE form_id ='CHARTOUTPUT' AND screen_name LIKE '%Mobile' AND group_name LIKE '%Mobile'";
        $stmt_sql_layout = $db->prepare($get_layout_fields) ; 
        $stmt_sql_layout->execute();
        $set_layout_fields = $stmt_sql_layout->fetchAll(PDO::FETCH_OBJ);
//        echo "<pre>"; print_r($result); echo "</pre>";
//        echo "<pre>"; print_r($set_layout_fields); echo "</pre>";
        $display_array = array();
        $minvalue = array();
        $keyarr = array();
        foreach($result as $key1 => $value1){
            for($i=0; $i< count($set_layout_fields); $i++){
                if($set_layout_fields[$i]->option_value == 'YES'  && $set_layout_fields[$i]->field_id == $value1){
                    $display_array["form_".$value1] = $set_layout_fields[$i]->option_value;
                    $keyarr[$key1]="form_".$value1;
                    $minvalue[] = $key1;
                }
            }
        }
       
        if(!empty($minvalue)){
            $minValue = min($minvalue);
            ksort($keyarr); 
        }else {
            $minValue = '';
        }

        $j = 1;
        
        $groupName = isset($set_layout_fields[0]->group_name)? $set_layout_fields[0]->group_name :'';
        
        $display_stringarray = static_patient_data($groupName, $keyarr, $pid, $minValue,$provider,$display_array);
        $display_string2 =  html_entity_decode ($display_stringarray);
//        echo "<pre>"; print_r($keyarr); echo "</pre>";exit();
        
        $geteid = "SELECT fe.encounter,DATE_FORMAT(fe.date, '%Y-%m-%d') as date,openemr_postcalendar_categories.pc_catname,fe.elec_signedby, fe.elec_signed_on 
            FROM form_encounter AS fe 
            left join openemr_postcalendar_categories on fe.pc_catid=openemr_postcalendar_categories.pc_catid  
            WHERE fe.pid = ".$pid." AND  fe.encounter ='$encounter_id' order by fe.date desc";
        $stmt = $db->prepare($geteid) ; 
        $stmt->execute();
        $eidval = $stmt->fetchAll(PDO::FETCH_OBJ);   
        for($inc=0; $inc< count($eidval); $inc++){
//            echo $eidval[$inc]->encounter;
    //    while ($eidval = sqlFetchArray($geteid)){ 
        $i = 1;
        $tempvalue  = 1;
        
//        $display_string2 .= "<div class='page' style='clear:both;'>";
        foreach($keyarr as $key => $value) {
            $display_title = $display_string = $display = '';
            $get_display_field_label = "SELECT title FROM layout_options WHERE form_id = 'CHARTOUTPUT' AND field_id = '".substr($value, 5)."'";
            $stmt2 = $db->prepare($get_display_field_label) ; 
            $stmt2->execute();
            $set_display_field_label = $stmt2->fetchAll(PDO::FETCH_OBJ); 
//            $set_display_field_label = sqlFetchArray($get_display_field_label);
            if(!empty($set_display_field_label))
                $display_title = $set_display_field_label[0]->title;
                

            if($value=='form_mobile_allergy' || $value=='form_homehealth_allergy' || $value=='form_payeraudit_allergy' || $value=='form_referral_allergy' || $value=='form_appeal_allergy'){
                if($display_array[$value] == "YES") {
                    $display_string .= "<div id='show_div_allergy".$eidval[$inc]->encounter."' style='display:none'>";
                    $setpagebr5 = '';
                    $setpagebr_allr = '';
                    $display_string .= "<div style='clear:both;'>";
                    $getpagebr = "SELECT group_name, layout_type,page_break FROM tbl_chartui_mapping WHERE screen_name = '$groupName' AND group_name LIKE '%Allergies'";
//                    $getpagebr_allr = sqlStatement($getpagebr);
//                    $setpagebr_allr= sqlFetchArray($getpagebr_allr);
                    $stmt_sql_array = $db->prepare($getpagebr) ; 
                    $stmt_sql_array->execute();
                    $setpagebr_allr = $stmt_sql_array->fetchAll(PDO::FETCH_OBJ);
                    if(substr($setpagebr_allr[0]->group_name, 1)=='Allergies' ) {
                        $idName = str_replace(" ","-",trim($groupName)."-".'Allergies');
                        if($setpagebr_allr[0]->page_break == 'YES' || $i == 1 || $tempvalue == 1){ 
                            //display_div_function(substr($setpagebr_allr[0]->group_name, 1),$eidval[$inc]->encounter );
                            $display = display_div_function($display_title,$eidval[$inc]->encounter,$pid );
                        }else{
                            $display = "<h2>". htmlspecialchars($display_title).": </h2>";
                        }               
                        $display_string .= $display ;
                    }
                    $display_string .= " <div id='1".$idName.$j."'  style='clear:both;'>"; 
                    $display_string .= "<ul  class='1".$idName.$j."' >"; 
                    $display_stringarray = display_allergy($eidval[$inc]->encounter,$setpagebr_allr[0]->layout_type,$display_string,$pid);
//                    $display_stringarray =  display_cc($eidval[$inc]->encounter,$groupName,$display_string,$pid);
                    $display_string2 .= $display_stringarray['display_string2'];
                    $tempvalue       = $display_stringarray['tempvalue'];
                    if(!empty($display_stringarray['display_string2']))
                        $display_string2 .= "</ul></div></div>";
                    $i++;
                    if(!empty($display_stringarray['display_string2']))
                        $display_string2 .= "</div>";
                }
            }
            if($value=='form_mobile_surgery' || $value=='form_homehealth_surgery' || $value=='form_payeraudit_surgery' || $value=='form_referral_surgery' || $value=='form_appeal_surgery'){
                if($display_array[$value] == "YES") {
                    $display_string .= "<div id='show_div_surgery".$eidval[$inc]->encounter."' style='display:none'>";
                    $setpagebr5 = '';    
                    $setpagebr_allr = '';
                    $display_string .= "<div style='clear:both;'>";
                    $getpagebr = "SELECT group_name, layout_type, page_break FROM tbl_chartui_mapping WHERE screen_name = '$groupName' AND group_name LIKE '%Surgeries'";
//                    $setpagebr_allr = sqlFetchArray($getpagebr_allr); 
                    $stmt_sql_array = $db->prepare($getpagebr) ; 
                    $stmt_sql_array->execute();
                    $setpagebr_allr = $stmt_sql_array->fetchAll(PDO::FETCH_OBJ);
                    if(substr($setpagebr_allr[0]->group_name, 1) == 'Surgeries') {
                        $idName = str_replace(" ","-",trim($groupName)."-".'Surgeries');
                         if($setpagebr_allr[0]->page_break  == 'YES' || $i == 1 || $tempvalue == 1){
                            $display = display_div_function($display_title,$eidval[$inc]->encounter,$pid );
                        }else{
                            $display = "<h2>". htmlspecialchars($display_title).": </h2>";
                        }               
                        
                    }
                    $display_string .= $display ;
                    $display_string .= " <div id='1".$idName.$j."'  style='clear:both;'>"; 
                    $display_string .= "<ul  class='1".$idName.$j."' >"; 
                    $display_stringarray = display_surgery($eidval[$inc]->encounter,$setpagebr_allr[0]->layout_type,$display_string,$pid);
                    $display_string2 .= $display_stringarray['display_string2'];
                    $tempvalue       = $display_stringarray['tempvalue'];
                    if(!empty($display_stringarray['display_string2']))
                        $display_string2 .= "</ul></div></div>";
                    $i++;
                    if(!empty($display_stringarray['display_string2']))
                        $display_string2 .= "</div>";
                }   
            }    
            if($value=='form_mobile_dental' || $value=='form_homehealth_dental' || $value=='form_payeraudit_dental' || $value=='form_referral_dental' || $value=='form_appeal_dental'){
                if ($display_array[$value] == "YES"){
                    $display_string .= "<div id='show_div_dental".$eidval[$inc]->encounter."' style='display:none'>";
                    $setpagebr5 = '';
                    $setpagebr_allr = '';
                    $display_string .= "<div style='clear:both;'>";
                    $getpagebr = "SELECT group_name, layout_type,page_break FROM tbl_chartui_mapping WHERE screen_name = '$groupName' AND group_name LIKE '%Dental Problems'";
//                    $getpagebr_allr = sqlStatement($getpagebr);
//                    $setpagebr_allr = sqlFetchArray($getpagebr_allr);
                    $stmt_sql_array = $db->prepare($getpagebr) ; 
                    $stmt_sql_array->execute();
                    $setpagebr_allr = $stmt_sql_array->fetchAll(PDO::FETCH_OBJ);
                    if(substr($setpagebr_allr[0]->group_name, 1) == 'Dental Problems') {
                        $idName = str_replace(" ","-",trim($groupName)."-".'Dental Problems');
                        if($setpagebr_allr[0]->page_break  == 'YES'|| $i == 1 || $tempvalue == 1){
                            $display = display_div_function($display_title,$eidval[$inc]->encounter,$pid );
                        }else{
                            $display = "<h2>". htmlspecialchars($display_title).": </h2>";
                        }               
                        $display_string .= $display ;
                    }
                    $display_string .= " <div id='1".$idName.$j."'  style='clear:both;'>"; 
                    $display_string .= "<ul  class='1".$idName.$j."' >"; 
                    $display_stringarray = display_dental($eidval[$inc]->encounter,$setpagebr_allr[0]->layout_type,$display_string,$pid);
                    $display_string2 .= $display_stringarray['display_string2'];
                    $tempvalue       = $display_stringarray['tempvalue'];
                    if(!empty($display_stringarray['display_string2']))
                        $display_string2 .= "</ul></div></div>";
                    $i++;
                    if(!empty($display_stringarray['display_string2']))
                        $display_string2 .= "</div>";
                }
            }
            if($value=='form_mobile_mproblem' || $value=='form_homehealth_mproblem' || $value=='form_payeraudit_mproblem' || $value=='form_referral_mproblem' || $value=='form_appeal_mproblem'){
                if ($display_array[$value] == "YES"){
                    $display_string .= "<div id='show_div_mproblem".$eidval[$inc]->encounter."' style='display:none'>";
                    $setpagebr5 = '';
                    $setpagebr_allr = '';
                    $display_string .= "<div style='clear:both;'>";
                    $getpagebr = "SELECT id,group_name, layout_type,page_break FROM tbl_chartui_mapping WHERE screen_name = '$groupName' AND group_name LIKE '%Medical Problems'";
//                    $getpagebr_allr = sqlStatement($getpagebr);
//                    $setpagebr_allr = sqlFetchArray($getpagebr_allr);
                    $stmt_sql_array = $db->prepare($getpagebr) ; 
                    $stmt_sql_array->execute();
                    $setpagebr_allr = $stmt_sql_array->fetchAll(PDO::FETCH_OBJ);
                    if(substr($setpagebr_allr[0]->group_name, 1) == 'Medical Problems') {
                        $idName = str_replace(" ","-",trim($groupName)."-".'Medical Problems');
                         if($setpagebr_allr[0]->page_break  == 'YES'|| $i == 1 || $tempvalue == 1){
                            $display = display_div_function($display_title,$eidval[$inc]->encounter,$pid );
                        }else{
                            $display = "<h2>". htmlspecialchars($display_title).": </h2>";
                        }               
                        $display_string .= $display ;
                    }
                    $display_string .= " <div id='1".$idName.$j."'  style='clear:both;'>"; 
                    $display_string .= "<ul  class='1".$idName.$j."' >"; 
                    $display_stringarray = display_mproblem($eidval[$inc]->encounter,$setpagebr_allr[0]->layout_type,$display_string,$pid);
                    $display_string2 .= $display_stringarray['display_string2'];
                    $tempvalue       = $display_stringarray['tempvalue'];
                    if(!empty($display_stringarray['display_string2']))
                        $display_string2 .= "</ul></div></div>";
                    $i++;
                    if(!empty($display_stringarray['display_string2']))
                        $display_string2 .= "</div>";
                }
            }
            if($value=='form_mobile_med' || $value=='form_homehealth_med' || $value=='form_payeraudit_med' || $value=='form_referral_med' || $value=='form_appeal_med'){
                if ($display_array[$value] == "YES"){
                    $display_string = '';
                    $display_string .= "<div id='show_div_med".$eidval[$inc]->encounter."' style='display:none'>";
                    $setpagebr5 = '';
                    $setpagebr_allr = '';
                    $display_string .= "<div style='clear:both;'>";
                    $getpagebr = "SELECT group_name, layout_type,page_break FROM tbl_chartui_mapping WHERE screen_name = '$groupName' AND group_name LIKE '%Medication'";
//                    $getpagebr_allr = sqlStatement($getpagebr);
//                    $setpagebr_allr = sqlFetchArray($getpagebr_allr);
                    $stmt_sql_array = $db->prepare($getpagebr) ; 
                    $stmt_sql_array->execute();
                    $setpagebr_allr = $stmt_sql_array->fetchAll(PDO::FETCH_OBJ);
                    if(substr($setpagebr_allr[0]->group_name, 1) == 'Medication') {
                        $idName = str_replace(" ","-",trim($groupName)."-".'Medication');
                        if($setpagebr_allr[0]->page_break  == 'YES' || $i == 1 || $tempvalue == 1) {
                            $display = display_div_function($display_title,$eidval[$inc]->encounter,$pid );
                        }else{
                           $display = "<h2>". htmlspecialchars($display_title).": </h2>";
                        }               
                        $display_string .= $display ;
                    }
                    $display_string .= " <div id='1".$idName.$j."'  style='clear:both;'>"; 
                    $display_string .= "<ul  class='1".$idName.$j."' >"; 
                    $display_stringarray = display_medications($eidval[$inc]->encounter,$setpagebr_allr[0]->layout_type,$display_string,$pid);
                    $display_string2 .= $display_stringarray['display_string2'];
                    $tempvalue       = $display_stringarray['tempvalue'];
                    if(!empty($display_stringarray['display_string2']))
                        $display_string2 .= "</ul></div></div>";
                    $i++;
                    if(!empty($display_stringarray['display_string2'])) 
                        $display_string2 .= "</div>";
                }
            }  
            if($value=='form_mobile_dme' || $value=='form_homehealth_dme' || $value=='form_payeraudit_dme' || $value=='form_referral_dme' || $value=='form_appeal_dme'){
                if ($display_array[$value] == "YES"){
                    $display_string .= "<div id='show_div_dme".$eidval[$inc]->encounter."' style='display:none'>";
                    $setpagebr5 = '';
                    $setpagebr_allr = '';
                    $display_string .= "<div style='clear:both;'>";
                    $getpagebr = "SELECT group_name, layout_type,page_break FROM tbl_chartui_mapping WHERE screen_name = '$groupName' AND group_name LIKE '%DME'";
//                    $getpagebr_allr = sqlStatement($getpagebr);
//                    $setpagebr_allr = sqlFetchArray($getpagebr_allr);
                    $stmt_sql_array = $db->prepare($getpagebr) ; 
                    $stmt_sql_array->execute();
                    $setpagebr_allr = $stmt_sql_array->fetchAll(PDO::FETCH_OBJ);
                    if(substr($setpagebr_allr[0]->group_name, 1) == 'DME') {
                        $idName = str_replace(" ","-",trim($groupName)."-".'DME');
                         if($setpagebr_allr[0]->page_break  == 'YES' || $i == 1 || $tempvalue == 1){
                           $display = display_div_function($display_title,$eidval[$inc]->encounter,$pid );
                        }else{
                            $display = "<h2>". htmlspecialchars($display_title).": </h2>";
                        }               
                        $display_string .= $display ;
                    }
                    $display_string .= " <div id='1".$idName.$j."'  style='clear:both;'>"; 
                    $display_string .= "<ul  class='1".$idName.$j."' >"; 
                    $display_stringarray = display_dme($eidval[$inc]->encounter,$setpagebr_allr[0]->layout_type,$display_string,$pid);
                    $display_string2 .= $display_stringarray['display_string2'];
                    $tempvalue       = $display_stringarray['tempvalue'];
                    if(!empty($display_stringarray['display_string2']))
                        $display_string2 .= "</ul></div></div>";
                    $i++;
                    if(!empty($display_stringarray['display_string2'])) 
                        $display_string2 .= "</div>";
                }
            }
            if ($value=='form_mobile_vitals'|| $value=='form_homehealth_vitals'|| $value=='form_payeraudit_vitals' || $value=='form_referral_vitals'|| $value=='form_appeal_vitals'){
                if ($display_array[$value] == "YES"){
                    $display_string .= "<div id='show_div_vitals".$eidval[$inc]->encounter."' style='display:none'>";
                    $setpagebr5 = '';
                    $setpagebr_allr = '';
                    $display_string .= "<div style='clear:both;'>";
                    $getpagebr = "SELECT group_name, layout_type,page_break FROM tbl_chartui_mapping WHERE screen_name = '$groupName' AND group_name LIKE '%Vitals'";
//                    $getpagebr_allr = sqlStatement($getpagebr);
//                    $setpagebr_allr = sqlFetchArray($getpagebr_allr);
                    $stmt_sql_array = $db->prepare($getpagebr) ; 
                    $stmt_sql_array->execute();
                    $setpagebr_allr = $stmt_sql_array->fetchAll(PDO::FETCH_OBJ);
                    if(substr($setpagebr_allr[0]->group_name, 1) == 'Vitals') {
                        $idName = str_replace(" ","-",trim($groupName)."-".'Vitals');
                         if($setpagebr_allr[0]->page_break  == 'YES' || $i == 1 || $tempvalue == 1){
                            $display = display_div_function($display_title,$eidval[$inc]->encounter,$pid );
                        }else{
                            $display = "<h2>". htmlspecialchars($display_title).": </h2>";
                        }               
                        $display_string .= $display ;
                    }
                    $display_string .= " <div id='1".$idName.$j."'  style='clear:both;'>"; 
                    $display_string .= "<ul  class='1".$idName.$j."' >"; 
                    $display_stringarray = display_vitals($eidval[$inc]->encounter,$setpagebr_allr[0]->layout_type,$display_string,$pid);
                    $display_string2 .= $display_stringarray['display_string2'];
                    $tempvalue       = $display_stringarray['tempvalue'];
                    if(!empty($display_stringarray['display_string2']))
                        $display_string2 .= "</ul></div></div>";
                    $i++;
                    if(!empty($display_stringarray['display_string2']))
                        $display_string2 .= "</div>";
                }
            }
            if ($value=='form_mobile_ros' || $value=='form_homehealth_ros'|| $value=='form_payeraudit_ros'|| $value=='form_referral_ros'|| $value=='form_appeal_ros'){
                if($display_array[$value] == "YES" ){
                    $display_string .= "<div id='show_div_ros".$eidval[$inc]->encounter."' style='display:block'>";
                    $setpagebr5 = '';
                    $display_string .= "<div style='clear:both;'>";
                    $getpagebr4 = "SELECT DISTINCT(group_name) as group_name,page_break FROM tbl_chartui_mapping WHERE screen_name = '$groupName' AND group_name LIKE '%Review Of Systems'";
//                    $setpagebr4 = sqlFetchArray($getpagebr4);
                    $stmt_sql_array = $db->prepare($getpagebr4) ; 
                    $stmt_sql_array->execute();
                    $setpagebr4 = $stmt_sql_array->fetchAll(PDO::FETCH_ASSOC);
                    if(substr($setpagebr4[0]['group_name'],1)=='Review Of Systems'){
                        $idName = str_replace(" ","-",trim($groupName)."-".'Review Of Systems');
                        if($setpagebr4[0]['page_break'] == 'YES' || $i == 1 || $tempvalue == 1){
                            $display = display_div_function($display_title,$eidval[$inc]->encounter,$pid,$display_string );
                        }else{
                            $display = "<h2>". htmlspecialchars($display_title).": </h2>";
                        }               
                        $display_string .= $display ;          
                        $display_string .= " <div id='".$idName.$j."'  style='clear:both;'>";   
                        $name = $idName.$j;
                        $display_string .= "<ul  class='".$idName.$j."' >";
                        $display_stringarray = display_ros($eidval[$inc]->encounter,$groupName,$display_string,$pid,$name);
                        $display_string2 .= $display_stringarray['display_string2'];
                        $tempvalue       = $display_stringarray['tempvalue'];
                        if(!empty($display_stringarray['display_string2']))
                            $display_string2 .= "</ul></div></div>";
                        $i++;
                    } 
                    if(!empty($display_stringarray['display_string2']))
                        $display_string2 .= "</div>";
                }
                //echo $display_string2;
            }
            if ($value=='form_mobile_physical_exam'  || $value=='form_homehealth_physical_' || $value=='form_payeraudit_physical_' || $value=='form_referral_physical_ex' || $value=='form_appeal_physical_exam'){
                if($display_array[$value] == "YES"){
                    $display_string .= "<div id='show_div_physical_exam".$eidval[$inc]->encounter."' style='display:none'>";
                    $setpagebr5 = '';
                    $display_string .= "<div style='clear:both;'>";
                    $getpagebr5 = "SELECT DISTINCT(group_name) as group_name,page_break  FROM tbl_chartui_mapping WHERE screen_name = '$groupName' AND group_name LIKE '%Physical Exam'";
//                    $setpagebr5= sqlFetchArray($getpagebr5);
                    $stmt_sql_array = $db->prepare($getpagebr5) ; 
                    $stmt_sql_array->execute();
                    $setpagebr5 = $stmt_sql_array->fetchAll(PDO::FETCH_OBJ);
                    if(substr($setpagebr5[0]->group_name,1)=='Physical Exam'){
                        $idName = str_replace(" ","-",trim($groupName)."-".'Physical Exam');
                        if($setpagebr5[0]->page_break == 'YES' || $i == 1 || $tempvalue == 1){
                            $display = display_div_function($display_title,$eidval[$inc]->encounter,$pid );
                        }else{
                            $display = "<h2>". htmlspecialchars($display_title).": </h2>";
                        }               
                        $display_string .= $display ;           
                        $display_string .= " <div id='".$idName.$j."'  style='clear:both;'>";   
                        $display_string .= "<ul  class='".$idName.$j."' >";
                        $display_stringarray = display_physical_exam($eidval[$inc]->encounter,$groupName,$display_string,$pid);
                        $display_string2 .= $display_stringarray['display_string2'];
                        $tempvalue       = $display_stringarray['tempvalue'];
                        if(!empty($display_stringarray['display_string2']))
                            $display_string2 .= "</ul></div></div>";
                        $i++;
                    } 
                    if(!empty($display_stringarray['display_string2']))
                        $display_string2 .= "</div>";
                }
            } 
            if ($value=='form_mobile_cc' || $value=='form_homehealth_cc' || $value=='form_payeraudit_cc' || $value=='form_referral_cc' || $value=='form_appeal_cc'){
                
                if($display_array[$value] == "YES"){
                    $display_string .= "<div id='show_div_cc".$eidval[$inc]->encounter."' style='display:none'>";
                    $setpagebr5 = '';
                    $display_string .= "<div style='clear:both;'>";
                    $getpagebr5 = "SELECT DISTINCT(group_name) as group_name,page_break  FROM tbl_chartui_mapping WHERE screen_name = '$groupName' AND group_name LIKE '%Chief Complaint'";
//                    $setpagebr5= sqlFetchArray($getpagebr5);
                    $stmt_sql_array = $db->prepare($getpagebr5) ; 
                    $stmt_sql_array->execute();
                    $setpagebr5 = $stmt_sql_array->fetchAll(PDO::FETCH_OBJ);
                    if(substr($setpagebr5[0]->group_name,1)=='Chief Complaint'){
                        $idName = str_replace(" ","-",trim($groupName)."-".'Chief Complaint');
                        if($setpagebr5[0]->page_break == 'YES' || $i == 1 || $tempvalue == 1){
                            $display = display_div_function($display_title,$eidval[$inc]->encounter,$pid );
                        }else{
                            $display = "<h2>". htmlspecialchars($display_title).": </h2>";
                        }               
                        $display_string .= $display ;         
                        $display_string .=  " <div id='".$idName.$j."'  style='clear:both;'>";   
                        $display_string .=  "<ul  class='".$idName.$j."' >";
                        $display_stringarray =  display_cc($eidval[$inc]->encounter,$groupName,$display_string,$pid);
                        $display_string2 .= $display_stringarray['display_string2'];
                        $tempvalue       = $display_stringarray['tempvalue'];
                        if(!empty($display_stringarray['display_string2']))
                            $display_string2 .=  "</ul></div></div>";
                        $i++;
                    } 
                    if(!empty($display_stringarray['display_string2']))
                        $display_string2 .=  "</div>";
                }
//                echo htmlentities($display_string2);
                               

            }
            if ($value=='form_mobile_hpi' || $value=='form_homehealth_hpi' || $value=='form_payeraudit_hpi' || $value=='form_referral_hpi' || $value=='form_appeal_hpi'){
                if($display_array[$value] == "YES"){
                    $display_string .= "<div id='show_div_hpi".$eidval[$inc]->encounter."' style='display:none'>";
                    $setpagebr5 = '';
                    $display_string .= "<div style='clear:both;'>";
                    $getpagebr5 = "SELECT DISTINCT(group_name) as group_name,page_break  FROM tbl_chartui_mapping WHERE screen_name = '$groupName' AND group_name LIKE '%History of Present illness'";
//                    $setpagebr5= sqlFetchArray($getpagebr5);
                    $stmt_sql_array = $db->prepare($getpagebr5) ; 
                    $stmt_sql_array->execute();
                    $setpagebr5 = $stmt_sql_array->fetchAll(PDO::FETCH_OBJ);
                    if(substr($setpagebr5[0]->group_name,1)=='History of Present illness'){
                        $idName = str_replace(" ","-",trim($groupName)."-".'History of Present illness');
                        if($setpagebr5[0]->page_break == 'YES' || $i == 1 || $tempvalue == 1){
                            $display = display_div_function($display_title,$eidval[$inc]->encounter,$pid );
                        }else{
                            $display = "<h2>". htmlspecialchars($display_title).": </h2>";
                        }               
                        $display_string .= $display ;         
                        $display_string .= " <div id='".$idName.$j."'  style='clear:both;'>";   
                        $display_string .= "<ul  class='".$idName.$j."' >";
                        $display_stringarray = display_hpi($eidval[$inc]->encounter,$groupName,$display_string,$pid);
                        $display_string2 .= $display_stringarray['display_string2'];
                        $tempvalue       = $display_stringarray['tempvalue'];
                        if(!empty($display_stringarray['display_string2']))
                            $display_string2 .= "</ul></div></div>";
                        $i++;
                    } 
                    if(!empty($display_stringarray['display_string2']))
                        $display_string2 .= "</div>";
                }  
            }
            if($value=='form_mobile_assess' || $value=='form_homehealth_assess' || $value=='form_payeraudit_assess' || $value=='form_referral_assess' || $value=='form_appeal_assess'){
                if($display_array[$value] == "YES"){
                    $display_string .= "<div id='show_div_assess".$eidval[$inc]->encounter."' style='display:none'>";
                    $setpagebr5 = '';
                    $display_string .= "<div style='clear:both;'>";
                    $getpagebr5 = "SELECT DISTINCT(group_name) as group_name,page_break  FROM tbl_chartui_mapping WHERE screen_name = '$groupName' AND group_name LIKE '%Assessment Note'";
//                    $setpagebr5= sqlFetchArray($getpagebr5);
                    $stmt_sql_array = $db->prepare($getpagebr5) ; 
                    $stmt_sql_array->execute();
                    $setpagebr5 = $stmt_sql_array->fetchAll(PDO::FETCH_OBJ);
                    if(substr($setpagebr5[0]->group_name,1)=='Assessment Note'){
                        $idName = str_replace(" ","-",trim($groupName)."-".'Assessment Note');
                        if($setpagebr5[0]->page_break == 'YES' || $i == 1 || $tempvalue == 1){
                            $display = display_div_function($display_title,$eidval[$inc]->encounter,$pid );
                        }else{
                            $display = "<h2>". htmlspecialchars($display_title).": </h2>";
                        }               
                        $display_string .= $display ;          
                        $display_string .= " <div id='".$idName.$j."'  style='clear:both;'>";   
                        $display_string .= "<ul  class='".$idName.$j."' >";
                        $display_stringarray = display_assess($eidval[$inc]->encounter,$groupName,$display_string,$pid);
                        $display_string2 .= $display_stringarray['display_string2'];
                        $tempvalue       = $display_stringarray['tempvalue'];
                        if(!empty($display_stringarray['display_string2']))
                            $display_string2 .= "</ul></div></div>";
                        $i++;
                    } 
                    if(!empty($display_stringarray['display_string2']))
                        $display_string2 .= "</div>";
                } 
            }
            if($value=='form_mobile_plan' || $value=='form_homehealth_plan' || $value=='form_payeraudit_plan' || $value=='form_referral_plan' || $value=='form_appeal_plan'){
                if($display_array[$value] == "YES"){
                    $display_string .= "<div id='show_div_plan".$eidval[$inc]->encounter."' style='display:none'>";
                    $setpagebr5 = '';
                    $display_string .= "<div style='clear:both;'>";
                    $getpagebr5 = "SELECT DISTINCT(group_name) as group_name,page_break  FROM tbl_chartui_mapping WHERE screen_name = '$groupName' AND group_name LIKE '%Plan Note'";
//                    $setpagebr5= sqlFetchArray($getpagebr5);
                    $stmt_sql_array = $db->prepare($getpagebr5) ; 
                    $stmt_sql_array->execute();
                    $setpagebr5 = $stmt_sql_array->fetchAll(PDO::FETCH_OBJ);
                    if(substr($setpagebr5[0]->group_name,1)=='Plan Note'){
                        $idName = str_replace(" ","-",trim($groupName)."-".'Plan Note');
                        if($setpagebr5[0]->page_break == 'YES' || $i == 1 || $tempvalue == 1){
                            $display = display_div_function($display_title,$eidval[$inc]->encounter,$pid );
                        }else{
                            $display = "<h2>". htmlspecialchars($display_title).": </h2>";
                        }               
                        $display_string .= $display ;
                        $display_string .= " <div id='".$idName.$j."'  style='clear:both;'>";   
                        $display_string .= "<ul  class='".$idName.$j."' >";
                        $display_stringarray = display_plan($eidval[$inc]->encounter,$groupName,$display_string,$pid);
                        $display_string2 .= $display_stringarray['display_string2'];
                        $tempvalue       = $display_stringarray['tempvalue'];
                        if(!empty($display_stringarray['display_string2']))
                            $display_string2 .= "</ul></div></div>";
                        $i++;
                    } 
                    if(!empty($display_stringarray['display_string2']))
                        $display_string2 .= "</div>";
                } 
            }
            if ($value=='form_mobile_progress' || $value=='form_homehealth_progress' || $value=='form_payeraudit_progress' || $value=='form_referral_progress' || $value=='form_appeal_progress'){
                if($display_array[$value] == "YES"){
                    $display_string .=  "<div id='show_div_progress".$eidval[$inc]->encounter."' style='display:none'>";
                    $display_string .=  "<div style='clear:both;'>";
                    $setpagebr5 = '';
                    $getpagebr5 = "SELECT DISTINCT(group_name) as group_name,page_break  FROM tbl_chartui_mapping WHERE screen_name = '$groupName' AND group_name LIKE '%Progress Note'";
//                    $setpagebr5= sqlFetchArray($getpagebr5);
                    $stmt_sql_array = $db->prepare($getpagebr5) ; 
                    $stmt_sql_array->execute();
                    $setpagebr5 = $stmt_sql_array->fetchAll(PDO::FETCH_OBJ);
                    if(substr($setpagebr5[0]->group_name,1)=='Progress Note' ){
                        $idName = str_replace(" ","-",trim($groupName)."-".'Progress Note');
                        if($setpagebr5[0]->page_break == 'YES' || $i == 1 || $tempvalue == 1){
                            $display = display_div_function($display_title,$eidval[$inc]->encounter,$pid );
                        }else{
                            $display = "<h2>". htmlspecialchars($display_title).": </h2>";
                        }               
                        $display_string .= $display ;
                        $display_string .=  " <div id='".$idName.$j."'  style='clear:both;'>";   
                        $display_string .=  "<ul  class='".$idName.$j."' >";
                        $display_stringarray = display_progress($eidval[$inc]->encounter,$groupName,$display_string,$pid);
                        $display_string2 .= $display_stringarray['display_string2'];
                        $tempvalue        = $display_stringarray['tempvalue'];
                        if(!empty($display_stringarray['display_string2']))
                            $display_string2 .=  "</ul></div></div>";
                        $i++;
                    } 
                    if(!empty($display_stringarray['display_string2']))
                        $display_string2 .=  "</div>";
                }  
            }
            if ($value=='form_mobile_cert_recert' || $value=='form_homehealth_cert_rece' || $value=='form_payeraudit_cert_rece' || $value=='form_referral_cert_recert' || $value=='form_appeal_cert_recert'){
                
                if($display_array[$value] == "YES"){
                    $display_string .= "<div id='show_div_cert_recert".$eidval[$inc]->encounter."' style='display:none'>";
                    $setpagebr5 = '';
                    $display_string .= "<div style='clear:both;'>";
                    $getpagebr5 = "SELECT DISTINCT(group_name) as group_name,page_break  FROM tbl_chartui_mapping WHERE screen_name = '$groupName' AND group_name LIKE '%Certification_Recertification'";
//                    $setpagebr5= sqlFetchArray($getpagebr5);
                    $stmt_sql_array = $db->prepare($getpagebr5) ; 
                    $stmt_sql_array->execute();
                    $setpagebr5 = $stmt_sql_array->fetchAll(PDO::FETCH_OBJ);
//                    echo "<pre>"; print_r($setpagebr5); echo "</pre>";
                    if(substr($setpagebr5[0]->group_name,1)=='Certification_Recertification'){
                        $idName = str_replace(" ","-",trim($groupName)."-".'Certification_Recertification');
                        if($setpagebr5[0]->page_break == 'YES' || $i == 1 ){
                            $display = display_div_function(htmlspecialchars($display_title),$eidval[$inc]->encounter,$pid );
                        }else{
//                            echo htmlspecialchars($display_title);
                            $display = "<h2>". htmlspecialchars($display_title).": </h2>";
                        }               
                        $display_string .= $display ;
                        $display_string .= " <div id='".$idName.$j."'  style='clear:both;'>";   
                        $display_string .= "<ul  class='".$idName.$j."' >";
                        $display_stringarray = display_cert_recert($eidval[$inc]->encounter,$groupName,$display_string,$pid);
                        $display_string2 .= $display_stringarray['display_string2'];
                        $tempvalue       = $display_stringarray['tempvalue'];
                        if(!empty($display_stringarray['display_string2']))
                            $display_string2 .= "</ul></div></div>";

                        $i++;
                    } 
                    if(!empty($display_stringarray['display_string2']))
                        $display_string2 .= "</div>";
                }    
            }
            if($value=='form_mobile_cpo' || $value=='form_homehealth_cpo' || $value=='form_payeraudit_cpo' || $value=='form_referral_cpo' || $value=='form_appeal_cpo'){
                if($display_array[$value] == "YES"){
                    $display_string .= "<div id='show_div_cpo".$eidval[$inc]->encounter."' style='display:none'>";
                    $setpagebr5 = '';
                    $display_string .= "<div style='clear:both;'>";
                    $getpagebr5 = "SELECT DISTINCT(group_name) as group_name,page_break  FROM tbl_chartui_mapping WHERE screen_name = '$groupName' AND group_name LIKE '%CPO'";
//                    $setpagebr5= sqlFetchArray($getpagebr5);
                    $stmt_sql_array = $db->prepare($getpagebr5) ; 
                    $stmt_sql_array->execute();
                    $setpagebr5 = $stmt_sql_array->fetchAll(PDO::FETCH_OBJ);
                    if(substr($setpagebr5[0]->group_name,1)=='CPO'){
                        $idName = str_replace(" ","-",trim($groupName)."-".'CPO');
                        if($setpagebr5[0]->page_break == 'YES' || $i == 1 || $tempvalue == 1){
                            $display = display_div_function($display_title,$eidval[$inc]->encounter,$pid );
                        }else{
                            $display = "<h2>". htmlspecialchars($display_title).": </h2>";
                        }               
                        $display_string .= $display ;          
                        $display_string .= " <div id='".$idName.$j."'  style='clear:both;'>";   
                        $display_string .= "<ul  class='".$idName.$j."' >";
                        $display_stringarray =display_cpo($eidval[$inc]->encounter,$groupName,$display_string,$pid);
                        $display_string2 .= $display_stringarray['display_string2'];
                        $tempvalue       = $display_stringarray['tempvalue'];
                        if(!empty($display_stringarray['display_string2']))
                            $display_string2 .= "</ul></div></div>";
                        $i++;
                    } 
                    if(!empty($display_stringarray['display_string2']))
                        $display_string2 .= "</div>";
                }    
            }
            if($value=='form_mobile_ccm' || $value=='form_homehealth_ccm' || $value=='form_payeraudit_ccm' || $value=='form_referral_ccm' || $value=='form_appeal_ccm'){
                if($display_array[$value] == "YES"){
                    $display_string .= "<div id='show_div_ccm".$eidval[$inc]->encounter."' style='display:none'>";
                    $setpagebr5 = '';
                    $display_string .= "<div style='clear:both;'>";
                    $getpagebr5 = "SELECT DISTINCT(group_name) as group_name,page_break  FROM tbl_chartui_mapping WHERE screen_name = '$groupName' AND group_name LIKE '%CCM'";
//                    $setpagebr5= sqlFetchArray($getpagebr5);
                    $stmt_sql_array = $db->prepare($getpagebr5) ; 
                    $stmt_sql_array->execute();
                    $setpagebr5 = $stmt_sql_array->fetchAll(PDO::FETCH_OBJ);
                    if(substr($setpagebr5[0]->group_name,1)=='CCM'){
                        $idName = str_replace(" ","-",trim($groupName)."-".'CCM');
                        if($setpagebr5[0]->page_break == 'YES' || $i == 1 || $tempvalue == 1){
                            $display = display_div_function($display_title,$eidval[$inc]->encounter,$pid );
                        }else{
                            $display = "<h2>". htmlspecialchars($display_title).": </h2>";
                        }               
                        $display_string .= $display ;         
                        $display_string .= " <div id='".$idName.$j."'  style='clear:both;'>";   
                        $display_string .= "<ul  class='".$idName.$j."' >";
                        $display_stringarray = display_ccm($eidval[$inc]->encounter,$groupName,$display_string,$pid);
                        $display_string2 .= $display_stringarray['display_string2'];
                        $tempvalue       = $display_stringarray['tempvalue'];
                        if(!empty($display_stringarray['display_string2']))
                            $display_string2 .= "</ul></div></div>";
                        $i++;
                    } 
                    if(!empty($display_stringarray['display_string2']))
                        $display_string2 .= "</div>";
                }    
            }
            if($value=='form_mobile_audit' || $value=='form_homehealth_audit' || $value=='form_payeraudit_audit' || $value=='form_referral_audit' || $value=='form_appeal_audit'){
                if($display_array[$value] == "YES"){
                    $display_string .= "<div id='show_div_audit".$eidval[$inc]->encounter."' style='display:none'>";
                    $setpagebr5 = '';
                    $display_string .= "<div style='clear:both;'>";
                    $getpagebr5 = "SELECT DISTINCT(group_name) as group_name,page_break  FROM tbl_chartui_mapping WHERE screen_name = '$groupName' AND group_name LIKE '%CPO'";
//                    $setpagebr5= sqlFetchArray($getpagebr5);
                    $stmt_sql_array = $db->prepare($getpagebr5) ; 
                    $stmt_sql_array->execute();
                    $setpagebr5 = $stmt_sql_array->fetchAll(PDO::FETCH_OBJ);
                    if(substr($setpagebr5[0]->group_name,1)=='CPO'){
                        $idName = str_replace(" ","-",trim($groupName)."-".'CPO');
                        if($setpagebr5[0]->page_break == 'YES' || $i == 1 || $tempvalue == 1){
                            $display = display_div_function($display_title,$eidval[$inc]->encounter,$pid );
                        }else{
                            $display = "<h2>". htmlspecialchars($display_title).": </h2>";
                        }               
                        $display_string .= $display ;          
                        $display_string .= " <div id='".$idName.$j."'  style='clear:both;'>";   
                        $display_string .= "<ul  class='".$idName.$j."' >";
                        $display_stringarray = display_audit($eidval[$inc]->encounter,$groupName,$display_string,$pid);
                        $display_string2 .= $display_stringarray['display_string2'];
                        $tempvalue       = $display_stringarray['tempvalue'];
                        if(!empty($display_stringarray['display_string2']))
                            $display_string2 .= "</ul></div></div>";
                        $i++;
                    } 
                    if(!empty($display_stringarray['display_string2']))
                        $display_string2 .= "</div>";
                }  
            }
            if($value=='form_mobile_f2f' || $value=='form_homehealth_f2f' || $value=='form_payeraudit_f2f' || $value=='form_referral_f2f' || $value=='form_appeal_f2f'){
                if($display_array[$value] == "YES"){
                    $display_string .= "<div id='show_div_f2f".$eidval[$inc]->encounter."' style='display:none'>";
                    $setpagebr5 = '';
                    $display_string .= "<div style='clear:both;'>";
                    $getpagebr5 = "SELECT DISTINCT(group_name) as group_name,page_break  FROM tbl_chartui_mapping WHERE screen_name = '$groupName' AND group_name LIKE '%Face to Face HH Plan'";
//                    $setpagebr5= sqlFetchArray($getpagebr5);
                    $stmt_sql_array = $db->prepare($getpagebr5) ; 
                    $stmt_sql_array->execute();
                    $setpagebr5 = $stmt_sql_array->fetchAll(PDO::FETCH_OBJ);
                    if(substr($setpagebr5[0]->group_name,1)=='Face to Face HH Plan'){
                        $idName = str_replace(" ","-",trim($groupName)."-".'Face to Face HH Plan');
                        if($setpagebr5[0]->page_break == 'YES' || $i == 1 || $tempvalue == 1){
                            $display = display_div_function($display_title,$eidval[$inc]->encounter,$pid );
                        }else{
                            $display = "<h2>". htmlspecialchars($display_title).": </h2>";
                        }               
                        $display_string .= $display ;         
                        $display_string .= " <div id='".$idName.$j."'  style='clear:both;'>";   
                        $display_string .= "<ul  class='".$idName.$j."' >";
                        $display_stringarray = display_f2f($eidval[$inc]->encounter,$groupName,$display_string,$pid);
                        $display_string2 .= $display_stringarray['display_string2'];
                        $tempvalue       = $display_stringarray['tempvalue'];
                        if(!empty($display_stringarray['display_string2']))
                            $display_string2 .= "</ul></div></div>";
                        $i++;
                    } 
                    if(!empty($display_stringarray['display_string2']))
                        $display_string2 .= "</div>";
                }  
            }
            if($value=='form_mobile_procedure' || $value=='form_homehealth_procedure'|| $value=='form_payeraudit_procedure'|| $value=='form_referral_procedure' || $value=='form_appeal_procedure'){
                if($display_array[$value] == "YES"){
                    $display_string .= "<div id='show_div_procedure".$eidval[$inc]->encounter."' style='display:block'>";
                    $setpagebr5 = '';
                    $display_string .= "<div style='clear:both;'>";
                    $getpagebr5 = "SELECT DISTINCT(group_name) as group_name,layout_type,page_break  FROM tbl_chartui_mapping WHERE screen_name = '$groupName' AND group_name LIKE '%Procedure'";
//                    $setpagebr5= sqlFetchArray($getpagebr5);
                    $stmt_sql_array = $db->prepare($getpagebr5) ; 
                    $stmt_sql_array->execute();
                    $setpagebr5 = $stmt_sql_array->fetchAll(PDO::FETCH_OBJ);
                    if(substr($setpagebr5[0]->group_name,1)=='Procedure'){
                        $idName = str_replace(" ","-",trim($groupName)."-".'Procedure');
                        if($setpagebr5[0]->page_break == 'YES' || $i == 1 || $tempvalue == 1){
                            $display = display_div_function($display_title,$eidval[$inc]->encounter,$pid );
                        }else{
                            $display = "<h2>". htmlspecialchars($display_title).": </h2>";
                        }               
                        $display_string .= $display ;          
                        $display_string .= " <div id='".$idName.$j."'  style='clear:both;'>";   
                        $display_string .= "<ul  class='".$idName.$j."' >";
                        $display_stringarray = display_procedure($eidval[$inc]->encounter,$setpagebr5[0]->layout_type,$display_string,$pid,$tempvalue);
                        $display_string2 .= $display_stringarray['display_string2'];
                        $tempvalue       = $display_stringarray['tempvalue'];
                        if(!empty($display_stringarray['display_string2']))
                            $display_string2 .= "</ul></div></div>"; 
                        $i++;
                    } 
                    if(!empty($display_stringarray['display_string2']))
                        $display_string2 .= "</div>";
                }  
            }
        }
        
   
        $j++;
        // for np signature
        if(!empty($eidval[0]->elec_signedby)  && !empty($eidval[0]->elec_signed_on) && $eidval[0]->elec_signed_on != '0000-00-00 00:00:00'){
            $getSignImg="SELECT signature_image, CONCAT(fname,' ', lname) as fname,
                    (SELECT GROUP_CONCAT(provider_credentials) FROM tbl_patientuser where userid = '".$eidval[0]->elec_signedby."' AND provider_credentials <>  '' ) as designation
                    FROM users
                    WHERE id='".$eidval[0]->elec_signedby."'";
//            $resSignImg=sqlStatement($getSignImg);
//            $rowSignImg=sqlFetchArray($resSignImg);
            $stmt_sql_array = $db->prepare($getSignImg) ; 
            $stmt_sql_array->execute();
            $rowSignImg = $stmt_sql_array->fetchAll(PDO::FETCH_OBJ);
            
            $http = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on'? "https://" : "http://";

            $newval= $http.$_SERVER['SERVER_NAME']."/interface/pic/user_sign/".$rowSignImg[0]->signature_image;
            $display_string2 .= "<div id='sign_image' style='clear:both;'><table><tr><td>";
            $display_string2 .= '<img src="'.$newval. '" alt="'.$newval.'" align="left" height="142" width="300"/>';
            $display_string2 .= "</td></tr>";
            $display_string2 .= "<tr><td>";
            $desg= '';
            if(!empty($rowSignImg[0]->designation))
                $desg = ", ".$rowSignImg[0]->designation;
                $esigned = explode(" ",$eidval[0]->elec_signed_on);
                if(!empty($esigned)){
                    $esign = $esigned[0];
                    if(!empty($esigned[1]))
                        if($esigned[1] != '00:00' && $esigned[1] != '') $esign.= " at ".$esigned[1];
                }
            $display_string2 .= "<br/><br/>Electronically Signed by <b>". ucwords($rowSignImg[0]->fname).$desg."</b> on <b>".$esign."</b>.";
            $display_string2 .= "</td></tr></table></div>";
        }
        $display_string2 .= "</div>";

        $date = new DateTime('now', new DateTimeZone('America/New_York'));

        $encoded_string =  "<html><style>@page { size 8.5in 11in; margin: 2cm; }
            body { font-family: sans-serif;font-size: 10.5;padding:10px;}
            div.page { page-break-before: always } .firsttd{
                font-size: 15;
            }
            .secondtd{
                font-size: 12;
            }
            .thirdtd{
                font-size: 12;
            }
            .byelement{
                color: gray;
            }   
            ul{    list-style-type: none;   padding:0px;   -webkit-padding-start: 0px !important;}li { padding-right:40px; }ul{float:left;}</style><body  style='border:1px solid black;width:980px;' cellspacing='0'>".$display_string2."<footer style='clear:both;' align='right'>
              <p>Printed on ". $date->format('d-M-Y H:i:s a').".</p>
            </footer></body></html>" ;
        /* hema */
        if (!file_exists('../mobileMedicalRecords')) {
            mkdir('../mobileMedicalRecords', 0777, true);
        }
       
        $tmpFilename = uniqid();
        $tmpFile=$tmpFilename.".pdf";
        include('../DriveSync/mpdf/mpdf.php');
        $mpdf=new mPDF('c','A4','','' , 0 , 0 , 0 , 0 , 0 , 0);
         // $mpdf=new mPDF('win-1252','A4','','',15,10,16,10,10,10);
        $mpdf->SetDisplayMode('fullpage');
        $mpdf->list_indent_first_level = 0; 
        $mpdf->WriteHTML($encoded_string);
        $mpdf->Output("../mobileMedicalRecords/".$tmpFile, 'F'); 
        chmod("../mobileMedicalRecords/".$tmpFile,0777);
        
        /* ===================== */
        
        
        $site_url = url();
        echo "<!DOCTYPE html'><html><head>
            <script src='//ajax.googleapis.com/ajax/libs/jquery/1.8.1/jquery.min.js'></script>
            <script>                    
                    function savetodrivefunction(pid,encounter,provider){
                        var hiddenfile  = document.getElementById('hiddenpost').value;
                        
                        var url = document.location.origin+'/api/medrapi.php/savetodrive/'+pid+'/'+encounter+'/'+provider+'/'+hiddenfile;                   
//                       alert(url);
                        $.ajax({
                            type: 'GET',
                            url: url,
                            error:function(error){
                                alert(JSON.stringify(error));
                            },
                            success:function(data){alert('File Saved in Drive'); document.getElementById('drivebtn').style.visibility = 'hidden';}
                        });
                    }
                
            </script>
        <style>
            body{ font-family:  Arial, Helvetica, sans-serif;padding-left: 10px;padding-right: 20px;}.myButton {
	-moz-box-shadow:inset 0px 1px 0px 0px #ffffff;
	-webkit-box-shadow:inset 0px 1px 0px 0px #ffffff;
	box-shadow:inset 0px 1px 0px 0px #ffffff;
	background:-webkit-gradient(linear, left top, left bottom, color-stop(0.05, #f9f9f9), color-stop(1, #e9e9e9));
	background:-moz-linear-gradient(top, #f9f9f9 5%, #e9e9e9 100%);
	background:-webkit-linear-gradient(top, #f9f9f9 5%, #e9e9e9 100%);
	background:-o-linear-gradient(top, #f9f9f9 5%, #e9e9e9 100%);
	background:-ms-linear-gradient(top, #f9f9f9 5%, #e9e9e9 100%);
	background:linear-gradient(to bottom, #f9f9f9 5%, #e9e9e9 100%);
	filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#f9f9f9', endColorstr='#e9e9e9',GradientType=0);
	background-color:#f9f9f9;
	-moz-border-radius:6px;
	-webkit-border-radius:6px;
	border-radius:6px;
	border:1px solid #dcdcdc;
	display:inline-block;
	cursor:pointer;
	color:#666666;
	font-family:Arial;
	font-size:50px;
	font-weight:bold;
	padding:10px 24px;
	text-decoration:none;
	text-shadow:0px 1px 0px #ffffff;
}
.myButton:hover {
	background:-webkit-gradient(linear, left top, left bottom, color-stop(0.05, #e9e9e9), color-stop(1, #f9f9f9));
	background:-moz-linear-gradient(top, #e9e9e9 5%, #f9f9f9 100%);
	background:-webkit-linear-gradient(top, #e9e9e9 5%, #f9f9f9 100%);
	background:-o-linear-gradient(top, #e9e9e9 5%, #f9f9f9 100%);
	background:-ms-linear-gradient(top, #e9e9e9 5%, #f9f9f9 100%);
	background:linear-gradient(to bottom, #e9e9e9 5%, #f9f9f9 100%);
	filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#e9e9e9', endColorstr='#f9f9f9',GradientType=0);
	background-color:#e9e9e9;
}
.myButton:active {
	position:relative;
	top:1px;
}
.drivebtn{
    margin: auto;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    width: 490px;
}.loader{    opacity: .40;
    position: fixed;
    left: 0;
    top: 0;
    bottom: 0;
    right: 0;
    margin: auto;
    width: 150px;
    height: 150px;}
            div.page { page-break-before: always } 
            ul{    list-style-type: none;    -webkit-padding-start: 0px !important;}li { padding-right:0px; }ul{float:none;word-wrap: break-word;}</style>
            </head><body class='changbg' style='width:980px;' cellspacing='0'>";
        
        $get_is_save_to_drive = "SELECT notes FROM list_options WHERE list_id = 'AllcareDriveSync' AND option_id = 'save_to_drive_button'";

        $set_is_save_to_drive = $db->prepare($get_is_save_to_drive) ; 
        $set_is_save_to_drive->execute();
        $set_is_save_to_drive_result = $set_is_save_to_drive->fetchAll(PDO::FETCH_OBJ);
        if(strtolower($set_is_save_to_drive_result[0]->notes) == 'yes'){
            echo "<div class='drivebtn' id='drivebtn'>
                <input type='hidden' id='hiddenpost' value='$tmpFilename'>
                <input type='hidden' id='pid' value='$pid'>
                <input type='hidden' id='encounter' value='$encounter_id'>
                <input type='hidden' id='provider' value='$provider'>
                <a href='#' data-url='$site_url/api/medrapi.php/savetodrive' onClick='savetodrivefunction($pid,$encounter_id,$provider);' class='myButton'>
                <img src='data:image/png;base64,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' width='62' style='vertical-align: middle;'>&nbsp;Save to Drive</a></div>"; 
        }
        echo $display_string2."
            <footer style='clear:both;' align='right'>
              <p>Printed on ". $date->format('d-M-Y H:i:s a').".</p>
            </footer><div class='loader' style='display:none;'><img width = '150' src='data:image/gif;base64,R0lGODlhLgAuALMAAP///+7u7t3d3czMzLu7u6qqqpmZmYiIiHd3d2ZmZlVVVURERDMzMyIiIhEREQAAACH/C05FVFNDQVBFMi4wAwEAAAAh+QQJAwAAACwAAAAALgAuAAAE/xDISSt4OGttu/dbKGJfWY2oaJZpu62WK7/wNd/kiu+P6SqIwmAQGOw+rURBEGg6eaCRwiBgOpsEaGyUIFS/1wCvR0ElBl+wMzsui87patFwQCDGZFtI4Y0TEnghEiMGaGkHgSp6GmdDVQN3iYKEQ5WIkjMKlUMFmDcImwOAnjKFlWykLkKWqTIErwSQrS6wr6OzKLV/uCm6kbwiBbWXwCEHsAUGxSIIBMIFBbfLGArQ1sTTGAfWyb+tixnV1gYG0p6DzNDkdOaS6HsGyeQHdQsjDg44E1Lr9PQICRQsYMCggUF8N9y8mfcPYECBBA/mk3FCir86DgMOLNgA38QUHThQKEjQ0KHAjRI/KtoiMoGdjBAjdmyBpMWCkQlynixIkUUxGMBqgDsn9J27ogoDIQ3ZZqlPF0UjAAAh+QQJAwAAACwAAAAALgAuAAAE/xDISSt4OGttu/dbKGJfWY2oaJZpu62WK7/wNd/kiu+P6SqIwmAQCAwLB9mnlSgInsVoUTADjRSGp1YbBSCqsVGCsC13FbeTeFA2T3eU9bBMMBwQiIOBAJdcCUOBAgQJPCN+IgeBgUmGhzYbCYtDX46HIwaTjZY3CpMFnDsIBKSAhaE3e6WgqDcFpQSbrS6wBJWzLq+lp7gtBboFvL0ovwS/t8OYv8fJKQfLSM0oTb8GBsLSGQrL1rLZGc/WdtizkBpY4gcHaL2IIQjd6gjs5ebn6vJ4CQv19tr4eBAkSKCAAYMGDRw44BHnSj6BBBcYRKiwzwQUQAIOVCDxYMKFaTXCiFiQQF/Ejh9BurCCguRGjhNTKmGZYoHNjh5VpvCRDYa0Gv5QAb3YaqgaTkY7OErKcyXQCAAh+QQJAwAAACwAAAAALgAuAAAE/xDISSt4OGttu/dbKGJfWY2oaJZpu62WK7/wNd/kiu+P6SqIwmAgEAwLCIXr00oIi1BBYBoQFBIt0EhhGEa/1Kkh1UElCEPiFxoGYMkUMzqtjlapgIIsLjrT0wQGBwgIB11TNksSW0J/BG8hCAIIN4siBwMEcwMHPHB9mqEElJ4oiRsHogSdpTsKqmOtOwiqkLIzqaGxtzcGBQUEBay8M7/GtsQtxr/IySjLV84yywbN0iG+Btqk1yiG2oLdKQngBwdK4iJc5ubc6RuF7EnipxkK8oQL15aR7QgJCfQ547cBCKF/CRQsYJBswpaDABUyYNDAgYNWfNQBjLiQYsWLnjpOjFiwUaJHiyFjjFTAsmODjzy0oGCwwCXMHUxcTHxpEeQMH+9gpKtRjxhRh0aPZsSoVGXMpiz2EI0AACH5BAkDAAAALAAAAAAuAC4AAAT/EMhJK3g4a22791soYl9Zjaholmm7rZYrv/A13+SK74/pKggDgSAQDA0IhevTShgG0EFxOi0kWqCR4hCNUqmBgEE56qAUha73WwwHBtcyBZUgDOxqKdsdECBQcyIKQ4RRBAcHCAgHT21uAAOAEloFhIRWIwgEfAZYNiEHlkMHLgcBkEufGmiifzIHBTKqGqGVQ648PGgFvAWdubkJvbxxwDuwvb/GOwbJuMs3BtLSxdAz09Jk1tfTB9rbpYiI1eAp4uPlMouIiukuCeKKC+4pW4kICeT0GwmK+Anz6M3CAORfAgUM3E0S0S+fAgULEpZbGGJBvoMLIjZwAG7CCIsPRSMyaLCR47JAIhaEZDCyJLQTIxhkZEnSgUlgZmKybGnTWBYUDXje5MHEhc2hOHzsy6FUYA2nNSi+jArzJNWcRK829VQjAgAh+QQJAwAAACwAAAAALgAuAAAE/xDISSt4OGttu/dbKGJfWY2oaJZpu62WK7/wNd/kiu+P6SoIA4EwGAwNCIXr01IIi9CBYCoYFBIt0EhxGEah1LBBOeqgFIWh+isNTwfYMgWVSKu9X7cgEBDEVRNbdncEBQcHCQkHBm1UfH5yNiFOhXdXIwkEjnwDZCESIwgFaaQILgd7fHwGciJoo7B/LQipARKeHCMHsKOmNwd8AAQ7r6MGBzxSPA8JBs7OsjO4OEHPyMvYi86I2NmHh9HdM9+H0+Iy3whJ5zuH6uvsN+/q5vF06on19q74CQoM+1wsSORPwYKAP/ItWAAQYQ8RAxUYZMCgAUJQEA0yrOggIMYQDEUWUuTY0V4gESEpNmjgoCS7OSNGrmxpEqaIlSxdnjODYqZObFpQtPy5jIlDGkaP9tBxtIakfU5PvoxqsxtVnjyu+pARNQIAIfkEBQMAAAAsAAAAAC4ALgAABP8QyEkreDhrbbv3WyhiX1mNqGiWabutliu/8DXf5Irvj+kqiAOBMBgMDQiF69NSCIfEorRYSLRAI2cBCp0WBYKBQTnqoBQGwpYbnYLBBGuZgkoU7uuud/AGD+QqE1kGeHkFBwgJCQdCfH1hgDQ2IWiFdwaRGgkEjwEEZCESIwiWBQguB30BAQZzImgGsYSZKAiqAbQ9o7Kxpzepq6sFN04GB7EHPATBq6Ati4yMzjMJzAHJMkHRvjwDAROt2dEHuTIFAmM4jAjs0zw77PEL7/QP8Yrz9Tzsign5+jj6JVDwD+AMBYoUEDSIY4FCggsaMJzBQOGCBQwYTJxxESODBhJON2aYpIGBR5ANHIjsQbJkRpAOVIoUJaLBx5QyZ9IMgTLmSjojcK5kKWiET50nhgaKoTQUlqY5mECF0bRGS4ZWixrMmlQfVzPvvvqQkTUCACH5BAkDAAAALAAAAAAuAC4AAATZEMhJq7046827/2AojmRpnmjqPQlQFATxqhssxTFl0BPizrecZEDk/YBBwoRYTO0kLxuOwqQZntGnxDccqA7XMOCw8U4EpQNY3DEDBGiRek4eweX0Vgh+/yzGYwdce3dxHgiIiCV9HwmJCHokAZMFHo4JmAokBpOTbhuYoX8jB50BhqCZCgwkCKYBHgqysqwjrqaxCgu7tUYZuwsMwr4awsYNxBnHDQ0OyRcNDMzNzs8W0w7ZD9YVDtQP4NvcE9rh4uMA5uro6evsEu7v7fL09fb3+Pn6+/z6EQAh+QQJAwAAACwAAAAALgAuAAAE/xDISSt4OGttu/dbKGJfWY2oaJZpu62WK7/wNd/kiu+PKScHQ4FAKBQOCIXr01IcjNAhcWpItEAjBUIYnXoJg8FBOeqgnIZ0VPoNDwjWMuV8qKfV0Lb7HVdNsnWBd0gICQkIT2B7YX00NiELiIGBjRoJBYsCBGQhEiOHhHWVIgduAqcGciKRCK2tnC0IYaenoz2frq84B7SnBTcLhsK2LQS9ArApwcMLPAnHBzMK09Q8GMa0vzLUCwsM1g8GvQQz3d4M39YHAafs5ejoDeAI7AIBATPwDA3y1vT39/Lx4+cA3DqAAmYMdMAQnAGAAQYobMCwILgCASQE0OaiIrgNCkDERZth8aPJkzceodzhaSWPli5x/Ik5Yw5NGSdupjCj00+Mnp2wAM3BZCgMoDVUukw6EyXTnCaf8nwplQXOpBEAACH5BAkDAAAALAAAAAAuAC4AAAT/EMhJK3g4a22791soYl9Zjaholmm7rZYrv/A13+SK748pJweDoVAQHhIK16elQAiJUCJhakimQKNmUPiETr+Eg1UVyyIO6O3QCyYMCgnUyZxGc6Pt6YAQH1FGCwiCdWgICYdnBWADjHx+EigJgoMHCGMbCYqMmwSXHDYhCoiTfSkHbpsDBo8iDIevljMIqYylHIAKuYeeLQe0qzMMC7nEPAUDAskCvCPCC88LDDwJyskHwQzZ2TwYBNUF2NrS3AbVBDMN6ercDwjVA+jpDg0O7O7VMw76+/bVAvkY+HE7ECBZQXbsDARYGAAeQh4DGAYA9xAHAokBrlW8IQAjs40jQQgCWEgxBCiQGw6MtJUBEsqQrF7imCBzxp+aSm7ifDRnJ40yPj91CNqSRVAYPmucRKmUJsimPRFCHcptqg8ZTSMAACH5BAkDAAAALAAAAAAuAC4AAAT/EMhJK3g4a22791soYl9Zjaholmm7rZYrv/A13+SK748pJ4jDwVAwGA4J2aelCAqNxoJUelC0QKMFUMiFTgsEqnXUQWkRaO4wOg0TwmMVxQxEO7vE9nufJE+yCYF1QQiCgQhSe4pxNDYiCpCChYwaCQaKbwWUGRJZkJEJmyEIigMDB34iDAusC5ALM6Sms30vIwy4qwsMOAezpgY3Dbm5PAW/A6IiDczMDA08CcgIMw7WzTwYBL/BMtbfDtkPBgMC5gQ44OII5uYD2eHZ7O0C4uv09fY88+76PAb00PnDUa5dgYE3EASghwqhjAELBSxU5lDDgQAYMR5UURHDxYwYRGtxcOTQAACQ3UJ06vgAwckAyfyQLAlAgMiRK1miQXGCZYoyPuXECKoSC9EcS47CIFpjJsKmf55CneNvKlAeVn0oaRoBACH5BAkDAAAALAAAAAAuAC4AAAT/EMhJK3g4a22791soYl9Zjaholmm7rZYrv/A13+SK748pKwnEYThEJGSf1iIRRAiJhqjhoGiBRgwg0/kcSgtgKqqDYiwU2mb3Cy4YqqMTdnFGM5vEQ7TdPsYnIw0Mg3R2CWiGem0EBYxwIYBYg4R0DCMJB2AEm45/gQ2gk5YtCJqcB54hDg6grQ0zCZycfi8jq7erOAiyBAY4Dhi3PAacAwSPPDyxBAPNCMnQDwXNzb7RPAfUxtc8CNoD3DsJ3+G61ALg5TPeAu3p6i4G7e0E8DIE8wIF9i0J+QKo+KHAlw+ZQA0H/u1TwQ/BPwG0ONhQdyBAgHzWIE3kpkCAxY8BQgYYzBAp3ACQFyNKlAAPwEcBz/6U5EbA5QCVKymo0zeSJJlyPXPKOegzCdEeOg7W2Khu6cxrTodGi/qTB1UfSJZGAAAh+QQFAwAAACwAAAAALgAuAAAE/xDISSt4OGttu/dbKGJfWY2oaJZpu62WK7/wNd/kiu+PKS+KBGJITMg+LQYwwSQenk+EogUaNZTAYBMBPRi+UlQHdWVgFVrn82soGKajk7VsXmSFQyi74DbGJyMODg2EZlh2dkFce3wFcCGAgYKDdCMKXm2Nb38pk5QuCI18BAecLZMzCaMFBI4qPDKhBLOksLYPBrSzj7c3CboECL08rLQGwzsHugXIOAgDBAPQzb7S1tSp1tLYsdoD3C4H2gTgLdHWx+UjCQICA+7C6iIE7e0DvPIYB/Xt6ZDgCPi180OD2z6B/go2Q0AggMB7f2zcKjAggEWBBGlIGNbQokOLAzkyatx4ywAAjyBFcohx66RHVxHl2DopoIDKkTJ5JDiATwWLfDDk1ZCIbWgkZEZzzkzKkgdTH0eGRgAAIfkECQMAAAAsAAAAAC4ALgAABMgQyEmrvTjrzbv/YCiOZGmeaPoxi6IkMKyoV8Pc7ZsgPF09DhuuBeMhAIckDRgUslzFY/JgOKAezCYOupMmDWATFusoO7eTKThMGpObGYR1fXKXOQk6oHAskUMFez4fBYWBgxsHhoWIG4yHjRkGFFaRliUEAASZlxebn50Wn5uhoqClF5ClqqgTA6+vrXuwsa20A7K4ALCyFLqdCQACFZyIPFQSAsOulgMBz8LK0Z3P1crXxZEE1dDKk9TcvduvfL3m5+jp6uuREQAh+QQJAwAAACwAAAAALgAuAAAE/xDISSt4OGttu/dbKGJfWY2oaJZpu62WK7/wNd/kiu+PKTcNBmOxUBgVso/L4QAKh8ZEApFApkDLZhCqkCK+VVRnxnQOi97vAWFVUXBlIbE7RRzuh3bofWNq5V1qeHkjEzwjC1ODBoRuhygJiwYIhTaPIpEHBpsGehmWl5icBgUHoTyapAUFnqctCauxlK43qqumtDMIsQUGubqrBKu/MrAEwgTELgnHzcotCM3HzykH0gXUKAXSvtkhCQPSs94aBQPh4a3ZB+ft3XvKCO3tCY6/7PMDuHugocwCANsRUNdDwo5ea+wYGACwIb1KBnEMCECRYsOLAgbUgxhxBoCKGDsvEtjo5kSxjxZDCugkZowMARVjNixAkqPJFgrMTQxwrgAbFz68wchWox+tooaOIuUTaqlLHk6DAi0aAQAh+QQJAwAAACwAAAAALgAuAAAE/xDISSt4OGttu/dbKGJfWY2oaJZpu62WK7/wNd/kiu+PiTuORoNBlH12QCFxoVAsWqBfcMhgKhJOVIeXpFoT4OfoxA0umWCEWqGdlKdVdEK9Hkt4I8Z1rj4g2Co2eCIKdAeHCHaDKIWHjoAviykJjgcGiZI7CIcGnZCZMgqWnQYJoDijnQenNwikBQasMwkFtbCyMrS2Bbguura9LQi2BLzBKAfFBASxxyMGy8urziEJ0cuY1BoF1wSf2snX0yGCrAgEA9em5OWgBwPw6QPjNDcKA9kb1vHx3nbtWgIEGGDAzyZoAvjBI7CO3R0XCARKFECxIkV+DNu4gSIxgMWP/ToaOqQwqaPAjxXhGfjGYcukAiZRDiggMpDLFIUKwLs4848LH9RgOKsBEBTRjUaPksyk9OaOpkB/Eo0AACH5BAkDAAAALAAAAAAuAC4AAAT/EMhJK3g4a22791soYl9Zjaholmm7rZYrv/A13+SK74/JPw5HoyH7/IDCBmPZAv2Cw+ViwUB1jsgoY6FQLKwUbFbKTSS+o/AxKCQrzOe0RKxRct8JRFxlo2cYeHl6cn4bC2YIiQgKKoUhCooHBwiOM3mSkoyVLZCYBgmbLgiYk6EtCZIGkqYpqAavBqwoCbCvsiO0rwWxtyEJBcC7vSEIwcLDGgbGB8gZv8aUzQ8GBAXVBZrICATc3MyNot+i3dzYfC4HAQHiKAfk3oStAOoBA6AivwPv5nx9IQcS6KkjoEqPJAIDEurrdi+EGhEKBAgUQLEiRYUYuTV0+NDXAHoWPkNiTFhgI40TIxQUABnyIsYD2TjGaFWgZUWFn5o4SQGpQMKLBBe58HELhqwa/hwhnaB0accjTq/8iEp0KNIIACH5BAkDAAAALAAAAAAuAC4AAAT/EMhJK3g4a22791soYl9Zjaholmm7rZYrv/A13+SK74/J85/fxkFsgYQZYqOR6iA1SoYUdXpilA3pdESxPoiOpXSxYHAl3quYQVYoqLb0I8t2J96quJy9sCcWKnIbDH4ICYIyCgkJCI2AiCkLjI0IeJAok5SXKYwHngibKAqepKEjo6QHpiKopasbCQcGs6qvGgiztLYasrm1PJY3CQXEBgWHPAMDPAfEzsEyAwABAb8yCM7E1hwjAtTfoDIJBNkF0DkjBd/UAtsiCATx5ATu6KwD3wL6BMghw/LyzJ3RoyGBtwD6EhIwcGCRJwIDAMY7NpDgrYMJMyrbGBEgxYoWQzMkGJCxJMeOE89lcHLKQEl9JzfSU5kjBqcCJk8upLnySAoF2OIpi2egkgsfpmCEqhHSCtMJcp5WeSKVJZCqLGQ8jQAAIfkEBQMAAAAsAAAAAC4ALgAABP8QyEkreDhrbbv3WyhiX1mNqGiWabutliu/8DXf5Irvj8nzn98PJNx1iiOHA3VCipTLEcUZgjYaUgm16rhis1uN8soog8OYLpmxYKjQmjV7AXeVFwuFwl1HzfUKdH0oeQoJCQqDhIaHCYojC40Ijo8hkQiYCJWWmZibGwqYB6M7BwQHQgmjo5oyCAIBAAEDQgirB60uBwG8vLk3CrcHlC4KvbwEPAcGzAYHiTMFxwGoNwjNzL8cIgm9At/aKAkF2AbQNCMGvN/f1SkIBfHy4TkjCgMB7N8ExBsJBvLkmctiI0SCAfq+DTg1LIGtAgQiEhB4joaWEQcTChjAsaPEiAFF+1m8yI1Awo4cP4IkMJDgFHsG2KH0+LHAMyZHUoybSTPizRRBWoQyEDElOQQVXeZUBINpjT41XlKJGsMJVSJArvqQQTUCACH5BAkDAAAALAAAAAAuAC4AAATWEMhJq7046827/2AojmRpnmiqrmzrrk/8crE8Y7V9W7mz8zWH8EcJOhpI4kR4RDaUSydjCgUcp9gqAMtYLLRTrxfsVZg9hMGAQCgxzOaEJ0Cny0eMhF7vEdQDByQLe3wdA38GJAoIjAh3HAV/JQmNjiECfo8gjACcH36YAiMIB6WBl6GiIAmmpSKpIomtI7ADHga4Bq4kqhK2Gqe5uipqAAWdFAYFy8e5xGq/AGxtxswSiSnQxdLTE8zHLNoS09TWCC7Y490S4D/HbMcHmlr09fb3+Pk/EQAh+QQJAwAAACwAAAAALgAuAAAE/xDISSt4OGttu/dbKGJfWY2oaJZpu62WK7/wNd/kiu+PyfOf3w8k3HWKQwpSqFwCJU6mLWqcUm/Wq9bpuB0IiKhj3JUVAoDAIPwjjxuzQWAeMAjJjfxMQA+weQ55DQwMM31+QoOEhIZzfH87ioQLewECfAdCDAucnHGWl3Y/DAoLCqczBZeXBEKnrwozB6uXCTwLCbm5lDIKtAKtO7q6vGa/mTcKCMvLtioiCb9rMwoHzMvFHFkZBrQDA8gpCQfk5c4hUCIKBALf7gXnG9Xl5QjZ2tsYCe78AwXWuZYZGDiQXiwVE1Ds60egYcMCECESNEDuIMKEIxIQ4OfQYUSJBT4toosxopq7jh4/GkAgkgZJhQZQEvgI8UDLkUdaKDsA8SHFBDdxEtmSAQbRHEa31GjiZOnLIk6HVonKQobTCAAh+QQJAwAAACwAAAAALgAuAAAE/xDISSt4OGttu/dbKGJfWY2oaJZpu62WK7/wNd/kiu+PyfOf3w8k3HWKQwpSqFwCJU6mLWSIpqChRCBwsKpGhS0ggPC6FNt0wdw6pLdlNiqcHshTg4BAEKjeR3x7XFEOM3uHcTwOi4yGhwKJO4wODQ2Oh10/lJWVMwSPfjwNDKSkMwWPBEKlpTMHjwMJPAwLtAu1MwqwazsLCre3DDcGAwIDx5kzCwkKzb8jUxoJx9QDkSkKCdraCsIhWFTVAwTJKAoICNvM0NEaCgTVBAQFsiLnB+j5CQvs7RkJ8I7Jk1cA3TYEBxImzIeAn4oJKACOG0iwgEUDGBUqRKAAxYkRCUMKTKRo8WLGjR37NbF3gOK8kgUwGkDp8Qi2lhVhZuTYIkiLbAcwmjywrieLPz103KnhzwlTiFGefkQi1eaTqj5dPI0AACH5BAUDAAAALAAAAAAuAC4AAAT/EMhJK3g4a22791soYl9Zjaholmm7rZYrv/A13+SK74/J85/fDyTcdYpDCgohQKKUooQg0HSGJiPFIBAAVK0cW8jA5XrB4WiZK0CgM+JNgToNHN4txVQwJeBbB3x8AW5/KAWCAn6GKASJBowoiW2RI5MJlSKThZkaA4IDd50ajp8DkD8OqqszBgOvA4s8DauqMwiwr5g7Db2+DTNauQU8DMa/N66wBKIzxs/GI3EZCcsEBJwtDAoL3QvRV9MZB7HX19kjCwrr3N/S4hgKBeXmBrshCgn6CewKDO/wHiQwR7CAAQT79CFYiHCfP4ABBRK8VqCigYsHMjJsmGDBEygiQ+RRrEjyooGMGhd2/HgiywGSME2i1LgSIkiXMA1iRMmvRZA8CQ5cNEhTgQsflWBEqhGxCFMsYJ62RCL1SJKqP48yjQAAIfkECQMAAAAsAAAAAC4ALgAABNAQyEmrvTjrzbv/YCiOZGmeaKquVcJ+ShG8XTIEOK25OK7oml4OiBH0CMSKUWAMHJIUphRqkTKpSokAW3Rxv+DOYDIGD87nMDqtTiPa4+foQa+PCqS6HUToz/V7HEh9SCIODoAhhABvH4ePh4p+El4cDZcNkCKFEngcDKAMmIgjnAUFchcMCwuhoA2BH3izpwYHLgq5CgkJuayhsCWnwwUGxgfICAi8vQqsrSgGxMa2ycrMvgw01MjWzAnQOsfdysvOSQjdB9fnYe7v8PHy8zQRACH5BAkDAAAALAAAAAAuAC4AAAT/EMhJK3g4a22791soYl9Zjaholmm7rZYrv/A13+SK74/pKogDL/NpJQqBgG3Y66AUhqSUmTuNEgNpEmCgNicobFKgJXi/yw1WwCYnB4UzWKQgtNkBQuJMTGsMd2xCfH0iCYECCIQcI4B3g4spCgN3ZpEtCJRte5cpBQOgA5adKAShA5CkIqcDiqojrJyvIayusxumoq23IQUEvwSpvA8GwARxwxkIxnrJGHXGXc4PB8AFBbaXfs++19eyLQ4zEiPV3gUG2SMO7DLkdAbnBgYHCiMN+OziLXMhR97z6CFIoGCBQQYLGDDIpw8FhTDx0M07QHEgwYIKFzJ0+HAEFIn0Qw4gGJngYkKF+ThaoYNgIkWRFgkeRKlypccgL0mWlKmQH4gWChKMHFqyoDsWs2C8qrGND9N+cp529CLVyZCqPo7WiAAAOw=='/></div></body></html>";    
                 
        }


    }catch(PDOException $e){

        echo $patientres =  '{"error":{"text":'. $e->getMessage() .'}}';
//        echo $patientresult = $encryptor->encrypt($patientres, $apikey);
//        insertMobileLog('update',"$username",$patientres,$pid,'',"Edit Facility Data Screen - Query Failed", 0);
    } 
}

function display_demographics($pid, $groupName){
    $display_string = '';
    $db = getConnection();
    $getgroupnames = "SELECT DISTINCT(group_name) as group_name from layout_options where form_id='DEM' and uor <> 0 order by group_name";
    $check = 0;
    $db->query("SET SQL_BIG_SELECTS=1"); 
    $db->query( "SET NAMES utf8");
    $stmt_layout = $db->prepare($getgroupnames);
    $stmt_layout->execute();

    $setgroupnames = $stmt_layout->fetchAll(PDO::FETCH_ASSOC);
    $title = $fname = $mname = $lname = '';
//    while($setgroupnames=sqlFetchArray($getgroupnames)){
    for($g = 0; $g< count($setgroupnames); $g++){
        $gettitles =  "SELECT group_concat(field_id) as id, group_concat(title) as title from layout_options where form_id='DEM' and uor <> 0 AND group_name='".$setgroupnames[$g]['group_name']."'"  ;
        
        $idName = trim($groupName)."-".trim(substr($setgroupnames[$g]['group_name'],1));
        $idName = str_replace(" ","-",$idName);
        $display_string .= " <div id='".$idName."' style='clear:both;'><ul  class='".$idName."' >";
//        while($settitles=sqlFetchArray($gettitles)){
        $db->query("SET SQL_BIG_SELECTS=1"); 
        $db->query( "SET NAMES utf8");
        $stmt_layout = $db->prepare($gettitles);
        $stmt_layout->execute();
        
        $settitles = $stmt_layout->fetchAll(PDO::FETCH_ASSOC);
        for($h= 0; $h< count($settitles); $h++){
            $getselectedvales = "SELECT field_id as selectedfield FROM tbl_chartui_mapping WHERE screen_name = '$groupName' AND group_name = '".$setgroupnames[$g]['group_name']."' AND option_value = 'YES'";
            $idlist2 = '';
            $db->query("SET SQL_BIG_SELECTS=1"); 
            $db->query( "SET NAMES utf8");
            $stmt_layout = $db->prepare($getselectedvales);
            $stmt_layout->execute();
            
            $setselectedvalues = $stmt_layout->fetchAll(PDO::FETCH_ASSOC);
//            while($setselectedvalues= sqlFetchArray($getselectedvales)){
            for($k= 0; $k< count($setselectedvalues); $k++){    
                $selected= explode(',',$settitles[$h]['id']);
                for($i=0; $i< count($selected); $i++){
                    if($setselectedvalues[$k]['selectedfield'] == $selected[$i] ):
                        if($selected[$i] == 'em_city' || $selected[$i] == 'em_street' || $selected[$i] == 'em_name' || $selected[$i] == 'em_state' || $selected[$i] == 'em_postal_code' || $selected[$i] == 'em_country'):
                            $check = 1;
                            $idlist2 .= "e.".substr($setselectedvalues[$k]['selectedfield'],3).",";
                        elseif($selected[$i] == 'title' || $selected[$i] == 'fname' || $selected[$i] == 'lname' || $selected[$i] == 'mname'):
                            if($selected[$i] == 'title'):
                                $title = 'p.title,';
                            elseif($selected[$i] == 'fname'):
                                $fname = '" ",p.fname,';
                            elseif($selected[$i] == 'mname'):
                                $mname = '" ",p.mname,';
                            elseif($selected[$i] == 'lname'):
                                $lname = '" ",p.lname';
                            endif;
                            $getname = "CONCAT(".$title.$fname.$mname.$lname.") as Name,";
                        else:
                            $idlist2 .= "p.".$setselectedvalues[$k]['selectedfield'].",";
                        endif;
                    endif;
                }
            }
            $idlist = rtrim($idlist2, ',');
            if($idlist !=''):
                if(substr($setgroupnames[$g]['group_name'], 1) != 'Who' ):
                    $getname = '';
                endif;
                if($check == 1):
                    $getgroupval2 = "SELECT ". $idlist." FROM patient_data p LEFT JOIN employer_data e ON e.pid= p.pid WHERE e.pid = $pid ";
                else:
                    $getgroupval2 = "SELECT ".$getname. $idlist." FROM patient_data p WHERE pid = $pid ";
                endif;
//                $getgroupval = sqlFetchArray($getgroupval2);
                $db->query("SET SQL_BIG_SELECTS=1"); 
                $db->query( "SET NAMES utf8");
                $stmt_layout = $db->prepare($getgroupval2);
                $stmt_layout->execute();

                $getgroupval = $stmt_layout->fetchAll(PDO::FETCH_ASSOC);
                for($l=0; $l< count($getgroupval); $l++){
                    foreach($getgroupval[$l] as $key => $val){

                        $explodeval = array();
                        $listname = '';
                        $getlistname = "SELECT list_id, field_id, title, data_type FROM layout_options WHERE field_id = '$key'" ;
//                        while($setlistname=sqlFetchArray($getlistname)){
                        $db->query("SET SQL_BIG_SELECTS=1"); 
                        $db->query( "SET NAMES utf8");
                        $stmt_layout = $db->prepare($getlistname);
                        $stmt_layout->execute();

                        $setlistname = $stmt_layout->fetchAll(PDO::FETCH_ASSOC);
                        for($m =0; $m < count($setlistname); $m++){
                            $listname = $setlistname[$m]['list_id'];
                            $field_id = $setlistname[$m]['field_id'];
                            $subtitle = $setlistname[$m]['title'] ;
                            $datatypeno = $setlistname[$m]['data_type'];
                        }
                        if($listname != ''){
                            $explodeval = explode("|", $val);

                            for($i=0; $i< count($explodeval); $i++){
                                $getvalname = "SELECT title FROM list_options WHERE option_id =  '".addslashes($explodeval[$i])."' AND list_id = '$listname'";
                                $db->query("SET SQL_BIG_SELECTS=1"); 
                                $db->query( "SET NAMES utf8");
                                $stmt_layout = $db->prepare($getvalname);
                                $stmt_layout->execute();

                                $setvalname2 = $stmt_layout->fetchAll(PDO::FETCH_ASSOC);
//                                $setvalname2=sqlFetchArray($getvalname);
                                $getlayoutval = "SELECT layout_col,group_name FROM tbl_chartui_mapping WHERE screen_name = '$groupName' AND group_name = '".$setgroupnames[$g]['group_name']."'";
                                $db->query("SET SQL_BIG_SELECTS=1"); 
                                $db->query( "SET NAMES utf8");
                                $stmt_layout = $db->prepare($getlayoutval);
                                $stmt_layout->execute();

                                $setlayoutval = $stmt_layout->fetchAll(PDO::FETCH_ASSOC);
//                                $setlayoutval=sqlFetchArray($getlayoutval);
                                 //echo $setlayoutval['layout_col']."===".$setlayoutval['group_name'];

                                if(empty($setvalname2)){
                                    //echo   "<li><b>".$subtitle.": </b></li>";
                                }else{
                                    foreach($setvalname2[0] as $skey => $setvalname){
                                        if(!empty($setvalname) && $setvalname != '0000-00-00 00:00:00')
                                        $display_string .=   "<li><b>".$subtitle.": </b>".$setvalname."</li>";
                                    }
                                } 
                            }
                        }else{ 
                           $subtitle2 = '';
                            if($key == 'Name'):
                                $subtitle2 = 'Name';
                            else:
                                if($subtitle != ''):
                                    $subtitle2 = $subtitle;
                                else:
                                    $subtitle2 = $field_id;
                                endif; 
                            endif;    

                            if($key == 'providerID' || $key == 'ref_providerID')
                            {
                                if(!empty($val) && $val != '0000-00-00 00:00:00'){
                                    $getporvidername = "SELECT CONCAT(fname,' ',lname) AS name FROM users WHERE id='$val'" ;
                                    $db->query("SET SQL_BIG_SELECTS=1"); 
                                    $db->query( "SET NAMES utf8");
                                    $stmt_layout = $db->prepare($getporvidername);
                                    $stmt_layout->execute();

                                    $rowName = $stmt_layout->fetchAll(PDO::FETCH_ASSOC);
//                                    $rowName=sqlFetchArray($getporvidername);
                                    if($rowName)
                                        $provider_name=$rowName[0]['name'];
                                    else
                                        $provider_name= '';
                                    
                                    $display_string .= "<li><b>".$subtitle2.": </b>".$provider_name."</li>";
                                }
                            }elseif($key == 'pharmacy_id' )
                            {
                                if(!empty($val) && $val != '0000-00-00 00:00:00'){ 
                                   $getpharmacyname = "SELECT name FROM pharmacies WHERE id='$val'" ;
//                                   $rowName=sqlFetchArray($getpharmacyname);
//                                   $setpharmacyname=$rowName['name'];
                                    $db->query("SET SQL_BIG_SELECTS=1"); 
                                    $db->query( "SET NAMES utf8");
                                    $stmt_layout = $db->prepare($getpharmacyname);
                                    $stmt_layout->execute();

                                    $rowName = $stmt_layout->fetchAll(PDO::FETCH_ASSOC);
//                                    $rowName=sqlFetchArray($getporvidername);
                                    if($rowName)
                                        $setpharmacyname=$rowName[0]['name'];
                                    else
                                        $setpharmacyname= '';
                                    $display_string .= "<li><b>".$subtitle2.": </b>".$setpharmacyname."</li>";
                                }    
                            } else{
                                if(!empty($val) && $val != '0000-00-00 00:00:00')
                                    $display_string .= "<li><b>".$subtitle2.": </b>".$val."</li>";
                            }
                        }
                    }
                }
            endif;
        }
        $display_string .= "</ul></div>";
    }
   return $display_string;
}
function display_history($pid, $groupName){
    $db = getConnection();
    $j = '';
    $returnarray = '';
    $getgroupval3 = "SELECT date as Date,date FROM history_data  WHERE pid = $pid order by date desc limit 1";
//    $getgroupval3=sqlFetchArray($getgroupval3);
    $db->query("SET SQL_BIG_SELECTS=1"); 
    $db->query( "SET NAMES utf8");
    $stmt_sql_array = $db->prepare($getgroupval3) ; 
    $stmt_sql_array->execute();
    $getgroupval3 = $stmt_sql_array->fetchAll(PDO::FETCH_ASSOC);
    $getgroupnames = "SELECT DISTINCT(group_name) as group_name from layout_options where form_id='HIS' and uor <> 0 order by group_name";
//    while($setgroupnames=sqlFetchArray($getgroupnames)){
    $db->query("SET SQL_BIG_SELECTS=1"); 
    $db->query( "SET NAMES utf8");
    $stmt_sql_array = $db->prepare($getgroupnames) ; 
    $stmt_sql_array->execute();
    $setgroupnames = $stmt_sql_array->fetchAll(PDO::FETCH_ASSOC);
    for($g = 0; $g < count($setgroupnames); $g++){
        $display_string2 = $display_string = '';
        $setpagebr_allr = '';
        $display_string .= "<div id='".$setgroupnames[$g]['group_name']."' style='clear:both;display:none'>";
        $getpagebr = "SELECT group_name, layout_type,page_break FROM tbl_chartui_mapping WHERE screen_name = '$groupName' AND group_name LIKE '".$setgroupnames[$g]['group_name']."'";
//        $setpagebr_allr = sqlFetchArray($getpagebr);    
        $db->query("SET SQL_BIG_SELECTS=1"); 
        $db->query( "SET NAMES utf8");
        $stmt_sql_array = $db->prepare($getpagebr) ; 
        $stmt_sql_array->execute();
        $setpagebr_allr = $stmt_sql_array->fetchAll(PDO::FETCH_ASSOC);
        if($setpagebr_allr[0]['group_name'] == $setgroupnames[$g]['group_name']) {
            $idName = str_replace(" ","-",trim($groupName)."-".$setpagebr_allr[0]['group_name']);
            if($setpagebr_allr[0]['page_break'] == 'YES'){
                $display_string .= display_div_function(substr($setpagebr_allr[0]['group_name'], 1),0,$pid);
            }else{
                $display_string .= "<h2>". substr($setpagebr_allr[0]['group_name'], 1).": </h2>";
            }
        }
        $display_string .= " <div id='1".$idName.$j."'  style='clear:both;display:none'>"; 
        $display_string .= "<ul class='1".$idName.$j."' >"; 
        $gettitles =  "SELECT group_concat(field_id) as id, group_concat(title) as title from layout_options where form_id='HIS' and uor <> 0 AND group_name='".$setgroupnames[$g]['group_name']."'"  ;
//        while($settitles=sqlFetchArray($gettitles)){
        $db->query("SET SQL_BIG_SELECTS=1"); 
        $db->query( "SET NAMES utf8");
        $stmt_sql_array = $db->prepare($gettitles) ; 
        $stmt_sql_array->execute();
        $settitles = $stmt_sql_array->fetchAll(PDO::FETCH_ASSOC);
        for($h = 0; $h < count($settitles); $h++){
            $datacheck = '';
            $getselectedvales = "SELECT field_id as selectedfield FROM tbl_chartui_mapping WHERE screen_name = '$groupName' AND group_name = '".$setgroupnames[$g]['group_name']."' AND option_value = 'YES'";
            $idlist2 = '';
//            while($setselectedvalues= sqlFetchArray($getselectedvales)){
            $db->query("SET SQL_BIG_SELECTS=1"); 
            $db->query( "SET NAMES utf8");
            $stmt_sql_array = $db->prepare($getselectedvales) ; 
            $stmt_sql_array->execute();
            $setselectedvalues = $stmt_sql_array->fetchAll(PDO::FETCH_ASSOC);
            if(!empty($setselectedvalues)){
//                $display_string .= str_replace("<div id='".$setgroupnames[$g]['group_name']."' style='clear:both;display:none'>","<div id='".$setgroupnames[$g]['group_name']."' style='clear:both;display:block'>",$display_string);
            }
            for($k = 0; $k < count($setselectedvalues); $k++){
                $selected= explode(',',$settitles[$h]['id']);
                for($i=0; $i< count($selected); $i++){
                    if($setselectedvalues[$k]['selectedfield'] == $selected[$i] ):
                        $idlist2 .= "`".$setselectedvalues[$k]['selectedfield']."`,";
                    endif;
                }
            }
            $idlist = rtrim($idlist2, ',');
            if($idlist !=''){
                $getgroupcheck = "SELECT  ". $idlist."  FROM history_data  WHERE pid = $pid order by date desc limit 1";
//                $setgroupcheck=sqlFetchArray($getgroupcheck);
                $db->query("SET SQL_BIG_SELECTS=1"); 
                $db->query( "SET NAMES utf8");
                $stmt_sql_array = $db->prepare($getgroupcheck) ; 
                $stmt_sql_array->execute();
                $setgroupcheck = $stmt_sql_array->fetchAll(PDO::FETCH_ASSOC);
                $datacheck= 0; $add=0;
                foreach ($setgroupcheck[0] as $key3 => $value3) { 
                    if(empty($value3) || $value3 == '|0||' || $value3 == '|0|'){
                        $datacheck = $datacheck+1;
                    }else{    
                        $add= $add+1;
                    }    
                }
                $getgroupval = '';
                $getgroupval2 = "SELECT  DATE_FORMAT(date,'%m-%d-%Y') as Date1 ,". $idlist."  FROM history_data  WHERE pid = $pid order by date desc limit 1";
//                $getgroupval=sqlFetchArray($getgroupval2);
                $db->query("SET SQL_BIG_SELECTS=1"); 
                $db->query( "SET NAMES utf8");
                $stmt_sql_array = $db->prepare($getgroupval2) ; 
                $stmt_sql_array->execute();
                $getgroupval = $stmt_sql_array->fetchAll(PDO::FETCH_ASSOC);
                if(!empty($getgroupval)){
//                    $display_string = '';
                   foreach($getgroupval[0] as $key => $val){
                        $explodeval = array();
                        $listname = '';
                        $getlistname = "SELECT list_id, field_id, title, data_type FROM layout_options WHERE field_id = '$key'" ;
//                        while($setlistname=sqlFetchArray($getlistname)){
                        $db->query("SET SQL_BIG_SELECTS=1"); 
                        $db->query( "SET NAMES utf8");
                        $stmt_sql_array = $db->prepare($getlistname) ; 
                        $stmt_sql_array->execute();
                        $setlistname = $stmt_sql_array->fetchAll(PDO::FETCH_ASSOC);
                        $listname = $datatypeno = '';
                        for($l = 0; $l < count($setlistname); $l++){
                            $listname = $setlistname[0]['list_id'];
                            $subtitle = $setlistname[0]['title'];
                            $datatypeno = $setlistname[0]['data_type'];
                            $field_id = $setlistname[0]['field_id'];
                        }
                        if($listname != ''){
                            if($datatypeno == 23){ 
                                if(!empty($val)){
                                    $explodeval2 = explode("|", $val);
                                    $explodelist2 = array();
                                    for($i= 0; $i< count($explodeval2); $i++){
                                        $explodelist2 = explode(":", $explodeval2[$i]);
                                        $getvalname = "SELECT title FROM list_options WHERE option_id =  '".addslashes($explodelist2[0])."' AND list_id = '$listname'";

//                                        while($setvalname=sqlFetchArray($getvalname)){
                                        $db->query("SET SQL_BIG_SELECTS=1"); 
                                        $db->query( "SET NAMES utf8");
                                        $stmt_sql_array = $db->prepare($getvalname) ; 
                                        $stmt_sql_array->execute();
                                        $setvalname = $stmt_sql_array->fetchAll(PDO::FETCH_ASSOC);
                                        if(!empty($setvalname)){
                                            if($explodelist2[1] == 0){
                                                $type = 'N/A';
                                                //$nastring .= $setvalname['title'].",";
                                            }elseif($explodelist2[1] == 1){ 
                                                $type = 'Normal';
                                                //$normalstring .=  $setvalname['title'].",";
                                            }elseif($explodelist2[1] == 2){
                                                $type = 'Abnormal';
                                                //$abnormalstring .= $setvalname['title'].",";
                                            }
                                            if($explodelist2[1] == 2 || !empty($explodelist2[2])){
                                                $display_string .= "<li><b>".$setvalname[0]['title']."</b>&nbsp&nbsp $type&nbsp&nbsp ".$explodelist2[2]."<li>";
                                                //$i++;$datacheck = '';
                                            }    
                                        }
                                    }
                                }    
    //                            echo "<li><b>N/A: </b>".rtrim($nastring, ',');
    //                            if($nastring != '') echo ".</li>";
    //                            
    //                            echo "<li><b>Normal: </b>".rtrim($normalstring, ',');
    //                            if($normalstring != '') echo ".</li>";
    //                            
    //                            echo "<li><b>Abnormal: </b>".rtrim($abnormalstring, ',');
    //                            if($abnormalstring != '') echo ".</li>";

                            }elseif($datatypeno == 32){
                                if(!empty($val) && $val != '|0||'){
                                    $explodeval = explode("|", $val); 
                                    //if($val != '|0||'){
                                        $display_string .= "<li><b>".$subtitle.": </b>";
                                        if($datatypeno == 32 && !empty($explodeval[3])):
                                            $getvalname = "SELECT title FROM list_options WHERE option_id =  '".addslashes($explodeval[3])."' AND list_id = '$listname'";
//                                            while($setvalname=sqlFetchArray($getvalname)){
                                            $db->query("SET SQL_BIG_SELECTS=1"); 
                                            $db->query( "SET NAMES utf8");
                                            $stmt_sql_array = $db->prepare($getvalname) ; 
                                            $stmt_sql_array->execute();
                                            $setvalname = $stmt_sql_array->fetchAll(PDO::FETCH_ASSOC);
                                            if(!empty($setvalname)){
                                                $display_string .= $setvalname[0]['title']."               ";
                                            }
                                        endif;
                                        $statusname = ''; 
                                        $statustypes = array('current'=>'Current','quit' =>'Quit','never' => 'Never','not_applicable' => 'N/A');
                                        foreach($statustypes as $key => $stype){
                                            if($explodeval[1] == $key.$field_id):
                                                $statusname = $stype;
                                            endif;
                                        }

                                        if(!empty($explodeval) && (!empty($explodeval[0]) || !empty($statusname) || !empty($explodeval[2]))){
                                            $exploded_0  = isset($explodeval[0])? $explodeval[0] : '';
                                            $exploded_2 =  isset($explodeval[2])? $explodeval[2] : '';
                                            $display_string .= $exploded_0.str_repeat('&nbsp;', 5)."<b><u>Status</u>:</b> ".$statusname. "  ".$exploded_2."</li>";
                                            //$i++;$datacheck = '';
                                        }else{
                                            echo "</li>";
                                        }     
                                    }
                                //}
                            }else{
                                $setedvalname = '';
                                if(!empty($val) && $val !== '|0|'){
                                    $explodeval = explode("|", $val);
                                    //if($val != '|0|'){
                                        for($i=0; $i< count($explodeval); $i++){
                                            $getvalname = "SELECT title FROM list_options WHERE option_id =  '".addslashes($explodeval[$i])."' AND list_id = '$listname'";
//                                           while($setvalname=sqlFetchArray($getvalname)){
                                            $db->query("SET SQL_BIG_SELECTS=1"); 
                                            $db->query( "SET NAMES utf8");
                                            $stmt_sql_array = $db->prepare($getvalname) ; 
                                            $stmt_sql_array->execute();
                                            $setvalname = $stmt_sql_array->fetchAll(PDO::FETCH_ASSOC);
                                            if(!empty($setvalname)){
                                                $setedvalname .=  $setvalname[0]['title'].",";
                                            }
                                        }
                                        if(!empty($setedvalname)){
//                                            $display_string .= "<li><b>".$subtitle.": </b>";
                                            $trimedvalue =  rtrim($setedvalname, ',');
                                            //$i++;$datacheck = '';
                                            if($trimedvalue != ''){
                                                $display_string .= "<li><b>".$subtitle.": </b>".$trimedvalue."."."</li>";
                                            }
//                                            $display_string .= "</li>";
                                        }
                                    }
                                }
                            //}    
                        }else{ 
                            if($datatypeno == 28){
                                if(!empty($val) && $val != '|0|'){
                                    $explodeval = explode("|", $val);
                                    //if($val != '|0|'){
                                        $display_string .= "<li><b>".$subtitle.": </b>";
                                        $statusname = '';
                                        $statustypes = array('current'=>'Current','quit' =>'Quit','never' => 'Never','not_applicable' => 'N/A');
                                        foreach($statustypes as $key => $stype){
                                            if($explodeval[1] == $key.$field_id):
                                                $statusname = $stype;
                                            endif;
                                        }
                                        if(!empty($explodeval) && (!empty($explodeval[0]) || !empty($statusname) || !empty($explodeval[2]))){
                                            $exploded_0  = isset($explodeval[0])? $explodeval[0] : '';
                                            $exploded_2 =  isset($explodeval[2])? $explodeval[2] : '';
                                            $display_string .= $exploded_0.str_repeat('&nbsp;', 5)."<b><u>Status</u>:</b> ".$statusname. "  ".$exploded_2  . "</li>";
                                            //$i++;$datacheck = '';
                                        }else{
                                            echo "</li>";
                                        }    
                                    }
                                //}    
                            }else{
                                $subtitle2 = '';
                                if($key == 'Date1'){
                                    $subtitle2 = 'Last Recorded On';
                                }else{
                                    if($subtitle != ''):
                                        $subtitle2 = $subtitle;
                                    else:
                                        $subtitle2 = $field_id;
                                    endif;
                                }
                                
                                if(!empty($val)){
                                    if( $add != 0){
                                      $display_string .= "<li><b>".$subtitle2.": </b>".$val;  
                                    }
                                }
                                $valcheck = '';
                                
                                if($i==0) $valcheck = 1;
                                
                                if($val != ''  && $add >= 1) $display_string .= ".";
                                
                                if(!empty($val))
                                    if( $add != 0)
                                        $display_string .= "</li>";
                            }
                        }
                    } 
                    if($add >= 1 ){ 
                        $display_header = $setgroupnames[$g]['group_name'];
                        $display_check2 = "1".$idName.$j;
                        $display_string_check = str_replace("<div id='".$display_header."' style='clear:both;display:none'>","<div id='".$display_header."' style='clear:both;display:block'>",$display_string);
                        $display_string2 = str_replace("<div id='$display_check2'  style='clear:both;display:none'>","<div id='$display_check2'  style='clear:both;display:block'>",$display_string_check);
                    }
                }
                
            }
            if($display_string2 != '')
                $display_string2 .= "</ul></div></div>";
            
//            $display_string2 .= $display_string;    
        }

        $returnarray .= $display_string2;            
    } 
    return $returnarray;
}
function display_insurance($patient_id,$display_string){    
    $db = getConnection();
    $display_string2 = '';
    $datacheck = '';
    $left_div_fields_array=array('Payer','Priority','Type','Relationship_to_Insured','Start_Date','End_Date');
    $right_div_fields_array=array('Copay','Group_Number','Insured_ID_Number','Employer_Name');                
    $getInsuranceData="SELECT insd.type AS Priority,
                              insd.copay AS Copay,
                              insd.date AS Start_Date,
                              insd.subscriber_relationship AS Relationship_to_Insured,
                              insd.policy_type AS Type,
                              insd.policy_number AS Insured_ID_Number,
                              insd.group_number AS Group_Number,
                              CONCAT(insd.subscriber_fname,' ',insd.subscriber_lname) AS Employer_Name,
                              insc.name AS Payer 
                       FROM insurance_data insd
                       INNER JOIN insurance_companies insc ON insd.provider=insc.id
                          
                       WHERE insd.pid='".$patient_id."'";        

//    $resInsuranceData= sqlStatement($getInsuranceData); 
    $db->query("SET SQL_BIG_SELECTS=1"); 
    $db->query( "SET NAMES utf8");
    $stmt_layout = $db->prepare($getInsuranceData);
    $stmt_layout->execute();

    $rowInsuranceData = $stmt_layout->fetchAll(PDO::FETCH_ASSOC);
//    while($rowInsuranceData= sqlFetchArray($resInsuranceData)){
    for($i=0; $i< count($rowInsuranceData); $i++){
        $display_string .=  "<h3>".$rowInsuranceData[$i]['Payer']."</h3> ";
	$display_string .=  "<table style='width:100%;'><tr>";
        $display_string .=  "  <td style='width:50%;'>";
        $datacheck = 1 ;
        for($i=0; $i< count($rowInsuranceData); $i++){
            foreach($rowInsuranceData[$i] as $key=>$value) {
                if(in_array($key,$left_div_fields_array))  {
                    if($key=='Type'){                        
                        $policy_types = array(
                            ''   => 'N/A',
                            '12' => 'Working Aged Beneficiary or Spouse with Employer Group Health Plan',
                            '13' => 'End-Stage Renal Disease Beneficiary in MCP with Employer`s Group Plan',
                            '14' => 'No-fault Insurance including Auto is Primary',
                            '15' => 'Worker`s Compensation',
                            '16' => 'Public Health Service (PHS) or Other Federal Agency',
                            '41' => 'Black Lung',
                            '42' => 'Veteran`s Administration',
                            '43' => 'Disabled Beneficiary Under Age 65 with Large Group Health Plan (LGHP)',
                            '47' => 'Other Liability Insurance is Primary',
                          );// taken from patient.inc
                       $value=$policy_types[$value];  
                    // $policy_types array comes from patient.inc
                    }
                    $display_string .=  "<label><b>".ucfirst(str_replace('_',' ',$key))." : </b></label>";
                    $display_string .=  "<label>".ucfirst($value)."</label>";
                    $display_string .=  "<br>";
                }
            }         
        }
        $display_string .=  "</td>";
        $display_string .=  "  <td style='width:50%;'>";
        for($i=0; $i< count($rowInsuranceData); $i++){
            foreach($rowInsuranceData[$i] as $key=>$value) {
                if(in_array($key,$right_div_fields_array)){
                    $display_string .=  "<label><b>".ucfirst(str_replace('_',' ',$key))." : </b></label>";
                    $display_string .=  "<label>".$value."</label>";                
                    $display_string .=  "<br>";
                }                
            }
        }
        $display_string .= "</td></tr></table>";
        $display_string .=  "<br>";
    }
    if($datacheck != ''){
        $display_string2 = str_replace("<div id='show_div_insurance' style='display:none'>","<div id='show_div_insurance' style='display:block'>",$display_string);
    }    

    return $display_string2;
//        echo "No Insurance data for this patient.";
}
function display_vitals($encounter,$layout_type,$display_string,$pid){
    $display_string2 = '';
    $db = getConnection();
    global $tempvalue;
//    $getVitals = sqlStatement("SET NAMES utf8");
    $getVitals="SELECT DATE( v.date ) AS Service_Date, v.bps AS BPS, v.bpd AS BPD, v.weight AS Wt, v.height AS Ht, v.temperature, v.respiration AS RR, note, v.BMI, v.head_circ
                FROM form_vitals v
                INNER JOIN forms f ON v.id = f.form_id
                AND f.pid = v.pid
                WHERE v.pid='".$pid."'
                AND encounter = ($encounter) AND f.deleted = 0";
//    $resVitals=sqlStatement($getVitals);
//    $vitaldata=sqlStatement($getVitals);
    $db->query("SET SQL_BIG_SELECTS=1"); 
    $db->query( "SET NAMES utf8");
    $stmt_layout = $db->prepare($getVitals);
    $stmt_layout->execute();

    $resVitals = $stmt_layout->fetchAll(PDO::FETCH_ASSOC);
    $get_rendering_provider = "SELECT CONCAT(u.fname,' ',u.lname) AS provider_name ,DATE_FORMAT(f.date,'%m-%d-%Y') as date
                                        FROM users u
                                        INNER JOIN form_encounter f ON f.provider_id = u.id
                                        WHERE f.encounter = $encounter ";
    $db->query("SET SQL_BIG_SELECTS=1"); 
    $db->query( "SET NAMES utf8");
    $stmt_layout = $db->prepare($get_rendering_provider);
    $stmt_layout->execute();

    $set_rendering_provider = $stmt_layout->fetchAll(PDO::FETCH_ASSOC);
    $data2 = array();
//    $set_rendering_provider = sqlFetchArray($get_rendering_provider);
    for($i=0; $i< count($resVitals); $i++){
        $data2[] = $resVitals[$i]; 
    }
    if(count($data2)>0):
        if($layout_type == 'list'){
            foreach($data2 as $value){
                foreach ($value as $key => $val) {
                    if(!empty($val))
                        $display_string .= "<li><b>". ucfirst(str_replace('_',' ',$key.":"))."</b>".$val."</li>";
                }
//                if(!empty($set_rendering_provider['provider_name']))
//                    echo "<li><b> Seen by: </b> ".$set_rendering_provider['provider_name']."</li>";
                $display_string .= "<li>&nbsp;</li>";
            }
        }else{
            $display_string .= "<hr />";
            $display_string .= "<div class=''>\n";
            $display_string .= "<style>
                  .tbl_vitals {border:1px solid #C3E7F5;background-color: #E0E0E0; }
                  .tbl_vitals_th
                  {border:0px 1px 1px 0px solid  #C3E7F5;;text-align:center; background-color:  #E0E0E0;}
                  .tbl_vitals_td 
                  {border:0px 1px 1px 0px solid  ;text-align:center; background:#FFFFFF;}
                 </style>";
            $display_string .= "<table width='980px' class='tbl_vitals'>";
            $display_string .= "<tr>";
//            $rowVitalFields=sqlFetchArray($resVitals);
            foreach($resVitals[0] as $key=>$value){
                $display_string .= "<th class='tbl_vitals_th'>". ucfirst(str_replace('_',' ',$key))."</th>";
            }
            //echo "<th> Seen by </th>";
            $display_string .= "</tr>";
            foreach($data2 as $value){
                $display_string .= "<tr>";
                foreach ($value as $val) {
                    $display_string .= "<td class='tbl_vitals_td'>".$val."</td>";
                }
                //echo "<td class='tbl_vitals_td'>".$set_rendering_provider['provider_name']."</td>";
               $display_string .= "</tr>";
            }
            $display_string .= "</table>";
            $display_string .= "</div>\n";
        }
        $tempvalue = 0 ;
        $display_string2 = str_replace("<div id='show_div_vitals$encounter' style='display:none'>","<div id='show_div_vitals$encounter' style='display:block'>",$display_string);
   
    endif;
    $returnarray['tempvalue'] = $tempvalue;
    $returnarray['display_string2'] = $display_string2;
    return $returnarray;
}
function display_ros($encounter,$groupName,$display_string,$pid,$name){
    $display_string4 = '';
    $db = getConnection();
    $display_string2 = '';
    global $tempvalue;
//    $getROS = sqlStatement("SET NAMES utf8");
    $getROS="SELECT r.*
                FROM  tbl_form_allcare_ros r
                INNER JOIN forms f ON r.id = f.form_id
                WHERE r.pid=".$pid."
                AND encounter = ($encounter) AND f.deleted = 0 and formdir='allcare_ros'";

    
//    $resROS=sqlStatement($getROS);
//    $rosdata=sqlStatement($getROS);
    $db->query("SET SQL_BIG_SELECTS=1"); 
    $db->query( "SET NAMES utf8");
    $stmt_layout = $db->prepare($getROS);
    $stmt_layout->execute();

    $rowROSFields = $stmt_layout->fetchAll(PDO::FETCH_ASSOC);
    $datacheck = '';
    if(!empty($rowROSFields)){
//       while ($frow = sqlFetchArray($rosdata)) {
//          $data3[] = $frow; 
//        }
//        $rowROSFields=sqlFetchArray($rosdata);
        $datacheck = '';
        $checkingval  = 0;
//        echo "<pre>"; print_r($rowROSFields); echo "</pre>";
        foreach ($rowROSFields[0] as $key=>$value){
            if($key == 'constitutional'  ){
                 $datacheck = 1;
                $sub = 1;
            }
        } 
        if($datacheck == 1){
            $checkval  = 0;
            $display_string_field= '';
            $display_string_field .="<li id='constitutional$encounter' style='display: none;'><b>Constitutional: </b>";
            if($sub == 0){
                $display_string_field .="Normal.</li>";
            }elseif($sub == 1){
                $display_string_field .="<li style='margin-left: 1cm;'>";
                foreach ($rowROSFields[0] as $key=>$value) {
                    if($key == 'weight_change' || $key == 'weakness' || $key == 'fatigue' || $key == 'anorexia' || $key == 'fever' || $key == 'chills' || $key == 'night_sweats' || $key == 'insomnia' || $key == 'irritability' || $key == 'heat_or_cold' || $key == 'intolerance' || $key == 'change_in_appetite'){
                        if($value == 'YES'):
                            $display_string_field .=ucwords(str_replace('_',' ',$key)) ." YES<br>";
                            $checkval  = 1;
                            //$constitutional_val .=  ucwords(str_replace('_',' ',$key)).",";
                        elseif($value == 'NO'):
                            $display_string_field .="No ".ucwords(str_replace('_',' ',$key))."<br>";
                            $checkval  = 1;
                        endif;
                    }elseif($key == 'constitutional_text' ){
                        if(!empty($value)){
                            $display_string_field .="<b>Other Details:</b>".$value;
                            $checkval  = 1;
                        }
                    }
                }
                if($checkval == 1){
                    $display_string2 .= str_replace("<li id='constitutional$encounter' style='display: none;'>","<li id='constitutional$encounter' style='display: block;'>",$display_string_field);
                    $checkingval = 1;
                    $display_string2 .="</li></li>";
                }
//                $display_string2 .="</li>";
            }
            if($checkval == 1)$display_string2 .="<br>";
        }
        $datacheck = '';
        foreach ($rowROSFields[0] as $key=>$value){
            if($key == 'eyes'  ){
                 $datacheck = 1;
                $sub = 1;
            }
        }
        if($datacheck == 1){
            $checkval  = 0;
            $display_string_field= '';
            $display_string_field .="<li id='eyes$encounter' style='display: none;'><b>Eyes: </b>";
            if($sub == 0){
                $display_string_field .="Normal.</li>";
            }elseif($sub == 1){
                $display_string_field .="<li style='margin-left: 1cm;'>";
                foreach ($rowROSFields[0] as $key=>$value) {
                    if($key == 'change_in_vision' || $key == 'glaucoma_history' || $key == 'eye_pain' || $key == 'irritation' || $key == 'redness' || $key == 'excessive_tearing' || $key == 'double_vision' || $key == 'blind_spots' || $key == 'photophobia' || $key == 'glaucoma' || $key == 'cataract' || $key == 'injury' || $key == 'ha' || $key =='coryza' || $key == 'obstruction'){
                        if ($key == "glaucoma_history") { $key = "Glaucoma Family History"; }
                        if ($key == "irritation") { $key = "Eye Irritation"; }
                        if ($key == "redness") { $key = "Eye Redness"; }
                        if($value == 'YES'):
                            $display_string_field .=ucwords(str_replace('_',' ',$key)) ." YES<br>";
                         $checkval  = 1;
                            //$constitutional_val .=  ucwords(str_replace('_',' ',$key)).",";
                        elseif($value == 'NO'):
                            $display_string_field .="No ".ucwords(str_replace('_',' ',$key))."<br>";
                         $checkval  = 1;
                        endif;
                    }elseif($key == 'eyes_text' ){
                        if(!empty($value)){
                            $display_string_field .="<b>Other Details:</b>".$value;
                            $checkval  = 1;
                        }
                    }
                }  
                if($checkval == 1){
                    $display_string2 .= str_replace("<li id='eyes$encounter' style='display: none;'>","<li id='eyes$encounter' style='display: block;'>",$display_string_field);
                    $checkingval = 1;
                    $display_string2 .="</li></li>";
                }
//                $display_string2 .="</li>";
            }    
            if($checkval == 1)$display_string2 .="<br>";
        } 
        foreach ($rowROSFields[0] as $key=>$value){
            if($key == 'ent'  ){
                 $datacheck = 1;
                $sub = 1;
            }
        }
        
        if($datacheck == 1){
            $checkval  = 0;
            $display_string_field= '';
            $display_string_field .="<li id='ent$encounter' style='display: none;'><b>Ears, Nose, Mouth, Throat: </b>";
            if($sub == 0){
                $display_string_field .="Normal.</li>";
            }elseif($sub == 1){
                $display_string_field .="<li style='margin-left: 1cm;'>";
                foreach ($rowROSFields[0] as $key=>$value) {
                    if($key == 'hearing_loss' || $key == 'discharge' || $key == 'pain' || $key == 'vertigo' || $key == 'tinnitus' || $key == 'frequent_colds' || $key == 'sore_throat' || $key == 'sinus_problems' || $key == 'post_nasal_drip' || $key == 'nosebleed' || $key == 'snoring' || $key == 'apnea' || $key == 'bleeding_gums' || $key =='hoarseness' || $key == 'dental_difficulties' || $key == 'use_of_dentures'){
                        if ($key == "discharge") { $key = "ENT Discharge"; }
                        if ($key == "pain") { $key = "ENT Pain"; }
                        if($value == 'YES'):
                            $display_string_field .=ucwords(str_replace('_',' ',$key)) ." YES<br>";
                         $checkval  = 1;
                            //$constitutional_val .=  ucwords(str_replace('_',' ',$key)).",";
                        elseif($value == 'NO'):
                            $display_string_field .="No ".ucwords(str_replace('_',' ',$key))."<br>";
                         $checkval  = 1;
                        endif;
                    }elseif($key == 'ent_text' ){
                        if(!empty($value)){
                            $display_string_field .="<b>Other Details:</b>".$value;
                            $checkval  = 1;
                        }
                    }
                }   
                if($checkval == 1){
                    $display_string2 .= str_replace("<li id='ent$encounter' style='display: none;'>","<li id='ent$encounter' style='display: block;'>",$display_string_field);
                    $checkingval = 1;
                    $display_string2 .="</li></li>";
                }
//                $display_string2 .="</li>";
            }else{
                $display_string2 .="</li>";
            }   
            if($checkval == 1)$display_string2 .="<br>";
        }
        $datacheck = '';
        foreach ($rowROSFields[0] as $key=>$value){
            if($key == 'neck'  ){
                 $datacheck = 1;
                $sub = 1;
            }
        }
        if($datacheck == 1){
            $display_string_field= '';
            $checkval  = 0;
            $display_string_field .="<li id='neck$encounter' style='display: none;'><b>Neck: </b>";
            if($sub == 0){
                $display_string_field .="Normal.</li>";
            }elseif($sub == 1){
                $display_string_field .="<li style='margin-left: 1cm;'>";
                foreach ($rowROSFields[0] as $key=>$value) {
                   if( $key == 'stiffness' || $key == 'neck_pain' || $key == 'masses' || $key == 'tenderness'){
                        if($value == 'YES'):
                            $display_string_field .=ucwords(str_replace('_',' ',$key)) ." YES<br>";
                            $checkval  = 1;
                            //$constitutional_val .=  ucwords(str_replace('_',' ',$key)).",";
                        elseif($value == 'NO'):
                            $display_string_field .="No ".ucwords(str_replace('_',' ',$key))."<br>";
                            $checkval  = 1;
                        endif;
                   }elseif($key == 'neck_text' ){
                        if(!empty($value)){
                            $display_string_field .="<b>Other Details:</b>".$value;
                            $checkval  = 1;
                        }
                    }
                }  
                if($checkval == 1){
                    $display_string2 .= str_replace("<li id='neck$encounter' style='display: none;'>","<li id='neck$encounter' style='display: block;'>",$display_string_field);
                    $checkingval = 1;
                    $display_string2 .="</li></li>";
                }
//                $display_string2 .="</li>";
            }    
            if($checkval == 1)$display_string2 .="<br>";
        }    
        $datacheck = '';
        foreach ($rowROSFields[0] as $key=>$value){
            if($key == 'breast'  ){
                 $datacheck = 1;
                $sub = 1;
            }
        }
        if($datacheck == 1){
            $display_string_field= '';
            $checkval  = 0;
            $display_string_field .="<li id='breast$encounter' style='display: none;'><b>Breast: </b>";
            if($sub == 0){
                $display_string_field .="Normal.</li>";
            }elseif($sub == 1){
                $display_string_field .="<li style='margin-left: 1cm;'>";
                foreach ($rowROSFields[0] as $key=>$value) {
                   if( $key == 'breast_mass' || $key == 'breast_discharge' || $key == 'biopsy' || $key == 'abnormal_mammogram'){
                        if ($key == "biopsy") { $key = "Breast Biopsy"; }
                        if($value == 'YES'):
                            $display_string_field .=ucwords(str_replace('_',' ',$key)) ." YES<br>";
                            $checkval  = 1;
                            //$constitutional_val .=  ucwords(str_replace('_',' ',$key)).",";
                        elseif($value == 'NO'):
                            $display_string_field .="No ".ucwords(str_replace('_',' ',$key))."<br>";
                            $checkval  = 1;
                        endif;
                   }elseif($key == 'breast_text' ){
                        if(!empty($value)){
                            $display_string_field .="<b>Other Details:</b>".$value;
                            $checkval  = 1;
                        }
                    }
                }  
                if($checkval == 1){
                    $display_string2 .= str_replace("<li id='breast$encounter' style='display: none;'>","<li id='breast$encounter' style='display: block;'>",$display_string_field);
                    $checkingval = 1;
                    $display_string2 .="</li></li>";
                }
//                $display_string2 .="</li>";
            }    
            if($checkval == 1)$display_string2 .="<br>";
        }    
        $datacheck = '';
        foreach ($rowROSFields[0] as $key=>$value){
            if($key == 'respiratory'  ){
                 $datacheck = 1;
                $sub = 1;
            }
        }
        if($datacheck == 1){
            $display_string_field= '';
            $checkval  = 0;
            $display_string_field .="<li id='respiratory$encounter' style='display: none;'><b>Respiratory: </b>";
            if($sub == 0){
                $display_string_field .="Normal.</li>";
            }elseif($sub == 1){
                $display_string_field .="<li style='margin-left: 1cm;'>";
                foreach ($rowROSFields[0] as $key=>$value) {
                   if( $key == 'cough' || $key == 'sputum' || $key == 'shortness_of_breath' || $key == 'wheezing' || $key == 'hemoptsyis' || $key == 'asthma' || $key == 'copd' ){
                        if ($key == "hemoptsyis") { $key = "Hemoptysis"; }
                        if ($key == "copd") { $key = "COPD"; }
                        if($value == 'YES'):
                            $display_string_field .=ucwords(str_replace('_',' ',$key)) ." YES<br>";
                            $checkval  = 1;
                            //$constitutional_val .=  ucwords(str_replace('_',' ',$key)).",";
                        elseif($value == 'NO'):
                            $display_string_field .="No ".ucwords(str_replace('_',' ',$key))."<br>";
                            $checkval  = 1;
                        endif;
                   }elseif($key == 'respiratory_text' ){
                        if(!empty($value)){
                            $display_string_field .="<b>Other Details:</b>".$value;
                            $checkval  = 1;
                        }
                    }
                }    
                if($checkval == 1){
                    $display_string2 .= str_replace("<li id='respiratory$encounter' style='display: none;'>","<li id='respiratory$encounter' style='display: block;'>",$display_string_field);
                    $checkingval = 1;
                    $display_string2 .="</li></li>";
                }
//                $display_string2 .="</li>";
            }    
            if($checkval == 1)$display_string2 .="<br>";
        }    
        $datacheck = '';
        foreach ($rowROSFields[0] as $key=>$value){
            if($key == 'cardiovascular'  ){
                 $datacheck = 1;
                $sub = 1;
            }
        }
        if($datacheck == 1){
            $display_string_field= '';
            $checkval  = 0;
            $display_string_field .="<li id='cardiovascular$encounter' style='display: none;'><b>Cardiovascular: </b>";
            if($sub == 0){
                $display_string_field .="Normal.</li>";
            }elseif($sub == 1){
                $display_string_field .="<li style='margin-left: 1cm;'>";
                foreach ($rowROSFields[0] as $key=>$value) {
                   if($key == 'chest_pain' || $key == 'palpitation' || $key == 'syncope' || $key == 'pnd' || $key == 'doe' || $key == 'orthopnea' || $key == 'peripheal' || $key == 'edema' || $key == 'legpain_cramping' || $key == 'history_murmur' || $key == 'arrythmia' || $key == 'heart_problem'){
                        if ($key == "pnd") { $key = "PND"; }
                        if ($key == "doe") { $key = "DOE"; }
                        if ($key == "peripheal") { $key = "Peripheral"; }
                        if ($key == "legpain_cramping") { $key = "Leg Pain/Cramping"; } 
                        if($value == 'YES'):
                            $display_string_field .=ucwords(str_replace('_',' ',$key)) ." YES<br>";
                            $checkval  = 1;
                            //$constitutional_val .=  ucwords(str_replace('_',' ',$key)).",";
                        elseif($value == 'NO'):
                            $display_string_field .="No ".ucwords(str_replace('_',' ',$key))."<br>";
                            $checkval  = 1;
                        endif;
                   }elseif($key == 'cardiovascular_text' ){
                        if(!empty($value)){
                            $display_string_field .="<b>Other Details:</b>".$value;
                            $checkval  = 1;
                        }
                    }
                }   
                if($checkval == 1){
                    $display_string2 .= str_replace("<li id='cardiovascular$encounter' style='display: none;'>","<li id='cardiovascular$encounter' style='display: block;'>",$display_string_field);
                    $checkingval = 1;
                    $display_string2 .="</li></li>";
                }
//                $display_string2 .="</li>";
            }    
            if($checkval == 1)$display_string2 .="<br>";
        }    
        $datacheck = '';
        foreach ($rowROSFields[0] as $key=>$value){
            if($key == 'gastrointestinal'  ){
                 $datacheck = 1;
                $sub = 1;
            }
        }
        if($datacheck == 1){
            $display_string_field= '';
            $checkval  = 0;
            $display_string_field .="<li id='gastrointestinal$encounter' style='display: none;'><b>Gastrointestinal: </b>";
            if($sub == 0){
                $display_string_field .="Normal.</li>";
            }elseif($sub == 1){
                $display_string_field .="<li style='margin-left: 1cm;'>";
                foreach ($rowROSFields[0] as $key=>$value) {
                   if( $key == 'dysphagia' || $key == 'heartburn' || $key == 'bloating' || $key == 'belching' || $key == 'flatulence' || $key == 'nausea' || $key == 'vomiting' || $key == 'hematemesis' || $key == 'gastro_pain' || $key == 'food_intolerance' || $key == 'hepatitis' || $key == 'jaundice' || $key == 'hematochezia' || $key =='changed_bowel' || $key == 'diarrhea' || $key == 'constipation' || $key == 'blood_in_stool'){
                        if($value == 'YES'):
                            $display_string_field .=ucwords(str_replace('_',' ',$key)) ." YES<br>";
                            $checkval  = 1;
                            //$constitutional_val .=  ucwords(str_replace('_',' ',$key)).",";
                        elseif($value == 'NO'):
                            $display_string_field .="No ".ucwords(str_replace('_',' ',$key))."<br>";
                            $checkval  = 1;
                        endif;
                   }elseif($key == 'gastrointestinal_text' ){
                        if(!empty($value)){
                            $display_string_field .="<b>Other Details:</b>".$value;
                            $checkval  = 1;
                        }
                    }
                } 
                if($checkval == 1){
                    $display_string2 .= str_replace("<li id='gastrointestinal$encounter' style='display: none;'>","<li id='gastrointestinal$encounter' style='display: block;'>",$display_string_field);
                    $checkingval = 1;
                    $display_string2 .="</li></li>";
                }
//                $display_string2 .="</li>";
            }    
            if($checkval == 1)$display_string2 .="<br>";
        }    
        $datacheck = '';
        foreach ($rowROSFields[0] as $key=>$value){
            if($key == 'genitourinary'  ){
                 $datacheck = 1;
                $sub = 1;
            }
        }
        if($datacheck == 1){
            $display_string_field= '';
            $checkval  = 0;
            $display_string_field .="<li id='genitourinary$encounter' style='display: none;'><b>Genitourinary General: </b>";
            if($sub == 0){
                $display_string_field .="Normal.</li>";
            }elseif($sub == 1){
                $display_string_field .="<li style='margin-left: 1cm;'>";
                foreach ($rowROSFields[0] as $key=>$value) {
                   if( $key == 'polyuria' || $key == 'polydypsia' || $key == 'dysuria' || $key == 'hematuria' || $key == 'frequency' || $key == 'urgency' || $key == 'incontinence' || $key == 'renal_stones' || $key == 'utis' || $key == 'blood_in_urine' || $key == 'urinary_retention' || $key == 'change_in_nature_of_urine' ){
                        if ($key == "frequency") { $key = "Urine Frequency"; }
                        if ($key == "urgency") { $key = "Urine Urgency"; }
                        if ($key == "utis") { $key = "UTIs"; }
                       
                        if($value == 'YES'):
                            $display_string_field .=ucwords(str_replace('_',' ',$key)) ." YES<br>";
                            $checkval  = 1;
                            //$constitutional_val .=  ucwords(str_replace('_',' ',$key)).",";
                        elseif($value == 'NO'):
                            $display_string_field .="No ".ucwords(str_replace('_',' ',$key))."<br>";
                            $checkval  = 1;
                        endif;
                   }elseif($key == 'genitourinary_text' ){
                        if(!empty($value)){
                            $display_string_field .="<b>Other Details:</b>".$value;
                            $checkval  = 1;
                        }
                    }
                } 
                if($checkval == 1){
                    $display_string2 .= str_replace("<li id='genitourinary$encounter' style='display: none;'>","<li id='genitourinary$encounter' style='display: block;'>",$display_string_field);
                    $checkingval = 1;
                    $display_string2 .="</li></li>";
                }
//                $display_string2 .="</li>";
            }    
            if($checkval == 1)$display_string2 .="<br>";
        }    
        $datacheck = '';
        foreach ($rowROSFields[0] as $key=>$value){
            if($key == 'genitourinarymale'  ){
                 $datacheck = 1;
                $sub = 1;
            }
        }
        if($datacheck == 1){
            $display_string_field= '';
            $checkval  = 0;
            $display_string_field .="<li id='genitourinarymale$encounter' style='display: none;'><b>Genitourinary Male: </b>";
            if($sub == 0){
                $display_string_field .="Normal.</li>";
            }elseif($sub == 1){
                $display_string_field .="<li style='margin-left: 1cm;'>";
                foreach ($rowROSFields[0] as $key=>$value) {
                   if($key == 'hesitancy' || $key == 'dribbling' || $key == 'stream' || $key == 'nocturia' || $key == 'erections' || $key == 'ejaculations' ){
                        if ($key == "hesitancy") { $key = "Urine Hesitancy"; }
                        if ($key == "dribbling") { $key = "Urine Dribbling"; }
                        if ($key == "stream") { $key = "Urine Stream"; }
                        if($value == 'YES'):
                            $display_string_field .=ucwords(str_replace('_',' ',$key)) ." YES<br>";
                            $checkval  = 1;
                            //$constitutional_val .=  ucwords(str_replace('_',' ',$key)).",";
                        elseif($value == 'NO'):
                            $display_string_field .="No ".ucwords(str_replace('_',' ',$key))."<br>";
                            $checkval  = 1;
                        endif;
                   }elseif($key == 'genitourinarymale_text' ){
                        if(!empty($value)){
                            $display_string_field .="<b>Other Details:</b>".$value;
                            $checkval  = 1;
                            $display_string2 .="</li>";
                        }
                    }
                }  
                if($checkval == 1){
                    $display_string2 .= str_replace("<li id='genitourinarymale$encounter' style='display: none;'>","<li id='genitourinarymale$encounter' style='display: block;'>",$display_string_field);
                    $checkingval = 1;
                    $display_string2 .="</li></li>";
                }
                
            }    
            if($checkval == 1)$display_string2 .="<br>";
        }    
        $datacheck = '';
        foreach ($rowROSFields[0] as $key=>$value){
            if($key == 'genitourinaryfemale'  ){
                 $datacheck = 1;
                $sub = 1;
            }
        }
        if($datacheck == 1){
            $display_string_field= '';
            $checkval  = 0;
            $display_string_field .="<li id='genitourinaryfemale$encounter' style='display: none;'><b>Genitourinary Female: </b>";
            if($sub == 0){
                $display_string_field .="Normal.</li>";
            }elseif($sub == 1){
                $display_string_field .="<li style='margin-left: 1cm;'>";
                foreach ($rowROSFields[0] as $key=>$value) {
                   if($key == 'g' || $key == 'p' || $key == 'ap' || $key == 'lc' || $key == 'mearche' || $key == 'menopause' || $key == 'lmp' || $key == 'f_frequency' || $key == 'f_flow' || $key == 'f_symptoms' || $key == 'abnormal_hair_growth' || $key == 'f_hirsutism' ){
                        if ($key == "g") { $key = "Female G"; }
                        if ($key == "p") { $key = "Female P"; }
                        if ($key == "lc") { $key = "Female LC"; }
                        if ($key == "ap") { $key = "Female AP"; }
                        if ($key == "mearche") { $key = "Menarche"; }
                        if ($key == "lmp") { $key = "LMP"; }
                        if ($key == "f_frequency") { $key = "Menstrual Frequency"; }
                        if ($key == "f_flow") { $key = "Menstrual Flow"; }
                        if ($key == "f_symptoms") { $key = "Female Symptoms"; }
                        if ($key == "f_hirsutism") { $key = "Hirsutism/Striae"; }
                        if($value == 'YES'):
                            $display_string_field .=ucwords(str_replace('_',' ',$key)) ." YES<br>";
                            $checkval  = 1;
                            //$constitutional_val .=  ucwords(str_replace('_',' ',$key)).",";
                        elseif($value == 'NO'):
                            $display_string_field .="No ".ucwords(str_replace('_',' ',$key))."<br>";
                            $checkval  = 1;
                        endif;
                   }elseif($key == 'genitourinaryfemale_text' ){
                        if(!empty($value)){
                            $display_string_field .="<b>Other Details:</b>".$value;
                            $checkval  = 1;
                        }
                    }
                }  
                if($checkval == 1){
                    $display_string2 .= str_replace("<li id='genitourinaryfemale$encounter' style='display: none;'>","<li id='genitourinaryfemale$encounter' style='display: block;'>",$display_string_field);
                    $checkingval = 1;
                    $display_string2 .="</li></li>";
                }
//                $display_string2 .="</li>";
            }    
            if($checkval == 1)$display_string2 .="<br>";
        }    
        $datacheck = '';
        foreach ($rowROSFields[0] as $key=>$value){
            if($key == 'musculoskeletal'  ){
                 $datacheck = 1;
                $sub = 1;
            }
        }
        if($datacheck == 1){
            $display_string_field= '';
            $checkval  = 0;
            $display_string_field .="<li id='musculoskeletal$encounter' style='display: none;'><b>Musculoskeletal: </b>";
            if($sub == 0){
                $display_string_field .="Normal.</li>";
            }elseif($sub == 1){
                $display_string_field .="<li style='margin-left: 1cm;'>";
                foreach ($rowROSFields[0] as $key=>$value) {
                   if( $key == 'joint_pain' || $key == 'swelling' || $key == 'm_redness' || $key == 'm_warm' || $key == 'm_stiffness' || $key == 'm_aches' || $key == 'fms' || $key == 'arthritis' || $key == 'gout' || $key == 'back_pain' || $key == 'paresthesia' || $key == 'muscle_pain' || $key =='limitation_in_range_of_motion' ){
                        if ($key == "swelling") { $key = "Musc Swelling"; }
                        if ($key == "m_redness") { $key = "Musc Redness"; }
                        if ($key == "m_warm") { $key = "Musc Warm"; }
                        if ($key == "m_stiffness") { $key = "Musc Stiffness"; }
                        if ($key == "m_aches") { $key = "Musc Aches"; }
                        if ($key == "fms") { $key = "FMS"; }
                        if($value == 'YES'):
                            $display_string_field .=ucwords(str_replace('_',' ',$key)) ." YES<br>";
                            $checkval  = 1;
                            //$constitutional_val .=  ucwords(str_replace('_',' ',$key)).",";
                        elseif($value == 'NO'):
                            $display_string_field .="No ".ucwords(str_replace('_',' ',$key))."<br>";
                            $checkval  = 1;
                        endif;
                   }elseif($key == 'musculoskeletal_text' ){
                        if(!empty($value)){
                            $display_string_field .="<b>Other Details:</b>".$value;
                            $checkval  = 1;
                        }
                    }
                } 
                if($checkval == 1){
                    $display_string2 .= str_replace("<li id='musculoskeletal$encounter' style='display: none;'>","<li id='musculoskeletal$encounter' style='display: block;'>",$display_string_field);
                    $checkingval = 1;
                    $display_string2 .="</li></li>";
                }
//                $display_string2 .="</li>";
            }    
            if($checkval == 1)$display_string2 .="<br>";
        }    
        $datacheck = '';
        foreach ($rowROSFields[0] as $key=>$value){
            if($key == 'extremities'  ){
                 $datacheck = 1;
                $sub = 1;
            }
        }
        if($datacheck == 1){
            $display_string_field= '';
            $checkval  = 0;
            $display_string_field .="<li id='extremities$encounter' style='display: none;'><b>Extremities: </b>";
            if($sub == 0){
                $display_string_field .="Normal.</li>";
            }elseif($sub == 1){
                $display_string_field .="<li style='margin-left: 1cm;'>";
                foreach ($rowROSFields[0] as $key=>$value) {
                   if( $key == 'spasms' || $key == 'extreme_tremors'){
                        if($value == 'YES'):
                            $display_string_field .=ucwords(str_replace('_',' ',$key)) ." YES<br>";
                            $checkval  = 1;
                            //$constitutional_val .=  ucwords(str_replace('_',' ',$key)).",";
                        elseif($value == 'NO'):
                            $display_string_field .="No ".ucwords(str_replace('_',' ',$key))."<br>";
                            $checkval  = 1;
                        endif;
                   }elseif($key == 'extremities_text' ){
                        if(!empty($value)){
                            $display_string_field .="<b>Other Details:</b>".$value;
                            $checkval  = 1;
                        }
                    }
                } 
                if($checkval == 1){
                    $display_string2 .= str_replace("<li id='extremities$encounter' style='display: none;'>","<li id='extremities$encounter' style='display: block;'>",$display_string_field);
                    $checkingval = 1;
                    $display_string2 .="</li></li>";
                }
//                $display_string2 .="</li>";
            }    
            if($checkval == 1)$display_string2 .="<br>";
        }    
        $datacheck = '';
        foreach ($rowROSFields[0] as $key=>$value){
            if($key == 'neurologic'  ){
                 $datacheck = 1;
                $sub = 1;
            }
        }
        if($datacheck == 1){
            $display_string_field= '';
            $checkval  = 0;
            $display_string_field .="<li id='neurologic$encounter' style='display: none;'><b>Neurologic: </b>";
            if($sub == 0){
                $display_string_field .="Normal.</li>";
            }elseif($sub == 1){
                $display_string_field .="<li style='margin-left: 1cm;'>";
                foreach ($rowROSFields[0] as $key=>$value) {
                   if($key == 'loc' || $key == 'seizures' ||  $key == 'stroke'  || $key == 'tia' || $key == 'n_numbness' || $key == 'n_weakness' || $key == 'paralysis' || $key == 'intellectual_decline' || $key == 'memory_problems' || $key == 'dementia' || $key == 'n_headache' || $key == 'dizziness_vertigo' || $key == 'slurred_speech' || $key =='tremors' || $key == 'migraines' || $key == 'changes_in_mentation' ){
                        if ($key == "loc") { $key = "LOC"; }
                        if ($key == "tia") { $key = "TIA"; }
                        if ($key == "n_numbness") { $key = "Neuro Numbness"; }
                        if ($key == "n_weakness") { $key = "Neuro Weakness"; }
                        if ($key == "n_headache") { $key = "Headache"; } 
                        if($value == 'YES'):
                            $display_string_field .=ucwords(str_replace('_',' ',$key)) ." YES<br>";
                            $checkval  = 1;
                            //$constitutional_val .=  ucwords(str_replace('_',' ',$key)).",";
                        elseif($value == 'NO'):
                            $display_string_field .="No ".ucwords(str_replace('_',' ',$key))."<br>";
                            $checkval  = 1;
                        endif;
                   }elseif($key == 'neurologic_text' ){
                        if(!empty($value)){
                            $display_string_field .="<b>Other Details:</b>".$value;
                            $checkval  = 1;
                        }
                    }
                } 
                if($checkval == 1){
                    $display_string2 .= str_replace("<li id='neurologic$encounter' style='display: none;'>","<li id='neurologic$encounter' style='display: block;'>",$display_string_field);
                    $checkingval = 1;
                    $display_string2 .="</li></li>";
                }
//                $display_string2 .="</li>";
            }    
            if($checkval == 1)$display_string2 .="<br>";
        }    
        $datacheck = '';
        foreach ($rowROSFields[0] as $key=>$value){
            if($key == 'skin'  ){
                 $datacheck = 1;
                $sub = 1;
            }
        }
        if($datacheck == 1){
            $display_string_field= '';
            $checkval  = 0;
            $display_string_field .="<li id='skin$encounter' style='display: none;'><b>Skin: </b>";
            if($sub == 0){
                $display_string_field .="Normal.</li>";
            }elseif($sub == 1){
                $display_string_field .="<li style='margin-left: 1cm;'>";
                foreach ($rowROSFields[0] as $key=>$value) {
                   if($key == 's_cancer' || $key == 'psoriasis' || $key == 's_acne' || $key == 's_other' || $key == 's_disease' || $key == 'rashes' || $key == 'dryness' || $key == 'itching' ){
                        if ($key == "s_cancer") { $key = "Skin Cancer"; }
                        if ($key == "s_acne") { $key = "Acne"; }
                        if ($key == "s_other") { $key = "Skin Other"; }
                        if ($key == "s_disease") { $key = "Skin Disease"; } 
                        if($value == 'YES'):
                            $display_string_field .=ucwords(str_replace('_',' ',$key)) ." YES<br>";
                            $checkval  = 1;
                            //$constitutional_val .=  ucwords(str_replace('_',' ',$key)).",";
                        elseif($value == 'NO'):
                            $display_string_field .="No ".ucwords(str_replace('_',' ',$key))."<br>";
                            $checkval  = 1;
                        endif;
                   }elseif($key == 'skin_text' ){
                        if(!empty($value)){
                            $display_string_field .="<b>Other Details:</b>".$value;
                            $checkval  = 1;
                        }
                    }
                } 
                if($checkval == 1){
                    $display_string2 .= str_replace("<li id='skin$encounter' style='display: none;'>","<li id='skin$encounter' style='display: block;'>",$display_string_field);
                    $checkingval = 1;
                    $display_string2 .="</li></li>";
                }
//                $display_string2 .="</li>";
            }    
            if($checkval == 1)$display_string2 .="<br>";
        }    
        $datacheck = '';
        foreach ($rowROSFields[0] as $key=>$value){
            if($key == 'psychiatric'  ){
                 $datacheck = 1;
                $sub = 1;
            }
        }
        if($datacheck == 1){
            $display_string_field= '';
            $checkval  = 0;
            $display_string_field .="<li id='psychiatric$encounter' style='display: none;'><b>Psychiatric: </b>";
            if($sub == 0){
                $display_string_field .="Normal.</li>";
            }elseif($sub == 1){
                $display_string_field .="<li style='margin-left: 1cm;'>";
                foreach ($rowROSFields[0] as $key=>$value) {
                   if($key == 'p_diagnosis' || $key == 'p_medication' || $key == 'depression' || $key == 'anxiety' || $key == 'social_difficulties' || $key == 'alcohol_drug_dependence' || $key == 'suicide_thoughts' || $key == 'use_of_antideprassants' || $key == 'thought_content' ){
                        if ($key == "p_diagnosis") { $key = "Psych Diagnosis"; }
                        if ($key == "p_medication") { $key = "Psych Medication"; }
                        if($value == 'YES'):
                            $display_string_field .=ucwords(str_replace('_',' ',$key)) ." YES<br>";
                            $checkval  = 1;
                            //$constitutional_val .=  ucwords(str_replace('_',' ',$key)).",";
                        elseif($value == 'NO'):
                            $display_string_field .="No ".ucwords(str_replace('_',' ',$key))."<br>";
                            $checkval  = 1;
                        endif;
                   }elseif($key == 'psychiatric_text' ){
                        if(!empty($value)){
                            $display_string_field .="<b>Other Details:</b>".$value;
                            $checkval  = 1;
                        }
                    }
                } 
                if($checkval == 1){
                    $display_string2 .= str_replace("<li id='psychiatric$encounter' style='display: none;'>","<li id='psychiatric$encounter' style='display: block;'>",$display_string_field);
                    //$display_string .="Not Recorded.";
                   $checkingval = 1;
                   $display_string2 .="</li></li>";
                }
//                $display_string2 .="</li>";
            }    
            if($checkval == 1)$display_string2 .="<br>";
        }    
        $datacheck = '';
        foreach ($rowROSFields[0] as $key=>$value){
            if($key == 'endocrine'  ){
                 $datacheck = 1;
                $sub = 1;
            }
        }
        if($datacheck == 1){
            $display_string_field= '';
            $checkval  = 0;
            $display_string_field .="<li id='endocrine$encounter' style='display: none;'><b>Endocrine: </b>";
            if($sub == 0){
                $display_string_field .="Normal.</li>";
            }elseif($sub == 1){
                $display_string_field .="<li style='margin-left: 1cm;'>";
                foreach ($rowROSFields[0] as $key=>$value) {
                   if($key == 'thyroid_problems' || $key == 'diabetes' || $key == 'abnormal_blood' || $key == 'goiter' || $key == 'heat_intolerence' || $key == 'cold_intolerence' || $key == 'increased_thirst' || $key == 'excessive_sweating' || $key == 'excessive_hunger' ){
                        if ($key == "abnormal_blood") { $key = "Endo Abnormal Blood"; }
                        if($value == 'YES'):
                            $display_string_field .=ucwords(str_replace('_',' ',$key)) ." YES<br>";
                            $checkval  = 1;
                            //$constitutional_val .=  ucwords(str_replace('_',' ',$key)).",";
                        elseif($value == 'NO'):
                            $display_string_field .="No ".ucwords(str_replace('_',' ',$key))."<br>";
                            $checkval  = 1;
                        endif;
                   }elseif($key == 'endocrine_text' ){
                        if(!empty($value)){
                            $display_string_field .="<b>Other Details:</b>".$value;
                            $checkval  = 1;
                        }
                    }
                }  
                if($checkval == 1){
                    $display_string2 .= str_replace("<li id='endocrine$encounter' style='display: none;'>","<li id='endocrine$encounter' style='display: block;'>",$display_string_field);
                    $checkingval = 1;
                    $display_string2 .="</li></li>";
                }
//                $display_string2 .="</li>";
            }        
            if($checkval == 1)$display_string2 .="<br>";
        }    
        $datacheck = '';
        foreach ($rowROSFields[0] as $key=>$value){
            if($key == 'hai'  ){
                $datacheck = 1;
                $sub = 1;
//                if($value == 'Normal'){
//                    $datacheck = 1;
//                    $sub = 0;
//                }elseif($value == 'Select Details'){
//                    $datacheck = 1;
//                    $sub = 1;
//                }elseif($value == 'Not Examined'){
//                    $datacheck = 0;
//                }
            }
        }
        if($datacheck == 1){
            $display_string_field= '';
            $checkval  = 0;
            $display_string_field .="<li id='hai$encounter' style='display: none;'><b>Hematologic/Allergic/Immunologic: </b>";
            if($sub == 0){
                $display_string_field .="Normal.</li>";
            }elseif($sub == 1){
                $display_string_field .="<li style='margin-left: 1cm;'>";
                foreach ($rowROSFields[0] as $key=>$value) {
                   if($key == 'anemia' || $key == 'fh_blood_problems' || $key == 'bleeding_problems' || $key == 'allergies' || $key == 'frequent_illness' || $key == 'hiv' || $key == 'hai_status' || $key == 'hay_fever' || $key == 'positive_ppd' ){
                        if ($key == "fh_blood_problems") { $key = "FH Blood Problems"; }
                        if ($key == "hiv") { $key = "HIV"; }
                        if ($key == "hai_status") { $key = "HAI Status"; }
                        if($value == 'YES'):
                            $display_string_field .=ucwords(str_replace('_',' ',$key)) ." YES<br>";
                            $checkval  = 1;
                            //$constitutional_val .=  ucwords(str_replace('_',' ',$key)).",";
                        elseif($value == 'NO'):
                            $display_string_field .="No ".ucwords(str_replace('_',' ',$key))."<br>";
                            $checkval  = 1;
                        endif;
                   }elseif($key == 'hai_text' ){
                        if(!empty($value)){
                            $display_string_field .="<b>Other Details:</b>".$value;
                            $checkval  = 1;
                        }
                    }
                }  
                if($checkval == 1){
                    $display_string2 .= str_replace("<li id='hai$encounter' style='display: none;'>","<li id='hai$encounter' style='display: block;'>",$display_string_field);
                    $checkingval = 1;
                    $display_string2 .="</li></li>";
                }
//                $display_string2 .="</li>";
            } 
            if($checkval == 1)
                $display_string2 .="<br>";
        }   
        $tempvalue = 0 ;
        if($checkingval == 1){
            $display_string3 = $display_string.$display_string2;
            $display_string4 = str_replace("<div id='show_div_ros$encounter' style='display:none'>","<div id='show_div_ros$encounter' style='display:block'>",$display_string3);
        }else{
            $display_string4 = '';
        }
    }
    $returnarray['tempvalue'] = $tempvalue;
    $returnarray['display_string2'] = $display_string4; //$display_string2;
    return $returnarray;
}
  
function display_physical_exam($encounter,$groupName,$display_string,$pid){
    $db = getConnection();
    $display_string2= '';
    global $tempvalue;
//    $getphysicalexam = sqlStatement("SET NAMES utf8");
    $getphysicalexam = "SELECT r.*
                FROM  tbl_form_physical_exam r
                INNER JOIN forms f ON r.forms_id = f.form_id
                WHERE f.pid=".$pid."
                AND encounter = ($encounter) AND f.deleted = 0 and formdir='allcare_physical_exam'";
//    $resphysicalexam=sqlStatement($getphysicalexam);
//    $physicalexamdata=sqlStatement($getphysicalexam);
    $db->query("SET SQL_BIG_SELECTS=1"); 
    $db->query( "SET NAMES utf8");
    $stmt_layout = $db->prepare($getphysicalexam);
    $stmt_layout->execute();

    $physicalexamdata = $stmt_layout->fetchAll(PDO::FETCH_ASSOC);
    $pelines = array(
            'GEN' => array(
                    'GENWELL'  =>'Appearance',
                    'GENAWAKE' =>'Awake, Alert, Oriented, in No Acute Distress'),
            'HEAD' =>array(
                    'HEADNORM'  =>'Normocephalic, Autramatic',
                    'HEADLESI'  =>'Lesions' ),
            'EYE' => array(
                    'EYECP'    =>'Conjuntiva, Pupils',
                    'EYECON'   =>'Conjuctive Clear, Tms Intact,Discharge, Wax, Oral Lesions, Gums pink, Bilateral Nasal Turbinates',
                    'EYEPER'  =>'PERRLA, EOMI'),
            'ENT' => array(
                    'ENTTM'    =>'TMs/EAMs/EE, Ext Nose',
                    'ENTNASAL' =>'Nasal Mucosa Pink, Septum Midline',
                    'ENTORAL'  =>'Oral Mucosa Pink, Throat Clear',
                    'ENTSEPT'  =>'Septum Midline'),
            'NECK'=> array(
                    'NECKSUP'    =>'Supple,Thyromegaly, Carotid of the Nasal Septum,  JVD,  lymphadenopathy'),
            'BACK'=> array(
                    'BACKCUR'    =>'Normal Curvature, Tenderness'),
            'CV' => array(
                    'CVRRR'    =>'RRR',
                    'CVNTOH'   =>'Thrills or Heaves',
                    'CVCP'     =>'Cartoid Pulsations, Pedal Pulses',
                    'CVNPE'    =>'Peripheral Edema',
                    'CVNMU'    =>'Murmur, Rubs,Gallops'),
            'CHEST' => array(
                    'CHNSD'    =>'Skin Dimpling or Breast Nodules'),
            'RESP' => array(
                    'RECTAB'   =>'Lungs CTAB',
                    'REEFF'    =>'Respirator Effort Unlabored',
                    'RELUN'    =>'Lungs Clear,Rales,Rhonchi,Wheezes'),
            'GI' => array(
                    'GIOG'     =>'Ogrganomegoly',
                    'GIHERN'   =>'Hernia',
                    'GIRECT'   =>'Anus, Rectal Tenderness/Mass',
                    'GISOFT'   =>'Soft, Non Tender, Non Distended, Masses',
                    'GIBOW'   =>'Bowel Sounds present in all four quadrants'),
            'GU' => array(
                    'GUTEST'   =>'Testicular Tenderness, Masses',
                    'GUPROS'   =>'Prostate w/o Enlrgmt, Nodules, Tender',
                    'GUEG'     =>'Ext Genitalia, Vag Mucosa, Cervix',
                    'GUAD'     =>'Adnexal Tenderness/Masses',
                    'GULES'    =>'Normal. Lesions, Discharge, Hernias Noted, Deferred'),

        'EXTREMITIES'=> array(
                    'EXTREMIT'  =>'Edema, Cyanosis or Clubbing',
                    'EXTREDEF'  =>'Deformities'),
            'LYMPH' => array(
                    'LYAD'     =>'Adenopathy (2 areas required)'),
            'MUSC' => array(
                    'MUSTR'    =>'Strength',
                    'MUROM'    =>'ROM',
                    'MUSTAB'   =>'Stability',
                    'MUINSP'   =>'Inspection'),
            'NEURO' => array(
                    'NEUCN2'   =>'CN2-12 Intact',
                    'NEUREF'   =>'Reflexes Normal',
                    'NEUSENS'  =>'Sensory Exam Normal',
                    'NEULOCAL'  =>'Physiological, Localizing Findings'),
            'PSYCH' => array(
                    'PSYAFF'   =>'Affect Normal',
                    'PSYJUD'   =>'Normal Affect, Judgement and Mood, Alert and Oriented X3',
                    'PSYDEP'   =>'Depressive Symptoms',
                    'PSYSLE'   =>'Change In Sleeping Habit',
                    'PSYTHO'   =>'Change In Thought Content',
                    'PSYAPP'   =>'Patient Appears To Be In Good Mood',
                    'PSYABL'   =>'Able To Answer Questions Qppropriately'),
            'SKIN' => array(
                    'SKRASH'   =>'Rash or Abnormal Lesions',
                    'SKCLEAN'   =>'Clean & Intact with Good Skin Turgor'),
            'OTHER' => array(
                    'OTHER'    =>'Other'),
    );
    if($physicalexamdata):
//        while ($frow = sqlFetchArray($physicalexamdata)) {
        for($i=0; $i< count($physicalexamdata); $i++){
          $data4[] = $physicalexamdata[$i];
        }
        foreach ($pelines as $sysname => $sysarray) {
            $sysnamedisp = $sysname;
            if ($sysname == '*') {
              
            }
            else {
              $sysnamedisp = $sysname;
            }
            $datacheck = 0;
            $check = 0;
            $namevalue = $sysnamedisp;
            foreach ($sysarray as $line_id => $description) {
                foreach($data4 as $value){
                   
                    if((!empty($value['line_id']) || !empty($value['wnl']) || !empty($value['abn']) || $value['comments'])  && $line_id == $value['line_id']){
                        if($value['wnl']!=0 || $value['abn']!=0){ 
                         if($sysnamedisp == '' && $check == 1){
                             $display_string .= "<br> &nbsp;&nbsp;&nbsp;&nbsp;";
                            }else{
                                $display_string .= "<li>";
                                $display_string .= "<b>".$namevalue.":</b> <br>&nbsp;&nbsp;&nbsp;&nbsp;";
                                $check = 1;
                            }    
                            $wnl = '';$abn = '';
                            if($value['wnl']== 1):
                                $wnl = "Within Normal Limits";
                            endif;
                            if($value['abn']== 1):
                                $abn = "Abnormal Limits";
                            endif;
                            if($line_id == $value['line_id']):
                                $display_string .= $description."-".$wnl. " ".$abn.".".$value['comments'];
                                $datacheck = 1;
                                if($value['comments'] != ''):
                                    $display_string .= ".";
                                endif;
                                $display_string .= "</li>";
                                break;
                            else:    
                                if($sysnamedisp != '' || $check != 1)
                                    $display_string .= "</li>";
                            endif;
//                            if($sysnamedisp != '' || $check != 1)
//                                echo $display_string .= "</li>";
                       }
                       
//                        echo htmlentities($display_string);echo "hema"
                    }    
                }
                $sysnamedisp = '';
            } 
        }
        $tempvalue = 0 ;
        $display_string2 = str_replace("<div id='show_div_physical_exam$encounter' style='display:none'>","<div id='show_div_physical_exam$encounter' style='display:block'>",$display_string);
    
    endif;
    $returnarray['tempvalue'] = $tempvalue;
    $returnarray['display_string2'] = $display_string2;
    return $returnarray;
}
function display_medications($encounterid,$layout_type,$display_string,$pid){
    $db = getConnection();
    global $tempvalue;
    
    $dmesqlquery = '';
    $display_string2 = '';
    $returnarray = array();
    if($encounterid != 0){
        $dmesqlquery .= "SELECT DISTINCT l.id, l.title AS Title, 
                                    CASE 
                                    WHEN enddate IS NULL 
                                    THEN  'Active'
                                    ELSE  'Inactive'
                                    END AS is_Issue_Active, outcome, destination, begdate AS Begin_date, enddate AS End_date, diagnosis AS Codes, occurrence AS Occurrence, referredby AS ReferredBy, comments
                                    FROM lists AS l
                                    INNER JOIN issue_encounter AS ie ON ie.list_id = l.id
                                    AND ie.encounter = $encounterid
                                    WHERE l.type =  'medication'
                                    AND l.pid = $pid
                                    ORDER BY is_Issue_Active ASC , begdate DESC ";
    }else{
       $dmesqlquery .= "SELECT DISTINCT l.id, l.title AS Title, 
                                    CASE 
                                    WHEN enddate IS NULL 
                                    THEN  'Active'
                                    ELSE  'Inactive'
                                    END AS is_Issue_Active, outcome, destination, begdate AS Begin_date, enddate AS End_date, diagnosis AS Codes, occurrence AS Occurrence, referredby AS ReferredBy, comments
                                    FROM lists AS l
                                    WHERE l.type =  'allergy'
                                    AND l.pid = $pid AND l.id NOT IN ( SELECT DISTINCT list_id FROM issue_encounter WHERE pid = $pid ) 
                                    ORDER BY is_Issue_Active ASC , begdate DESC";
    }

    $db->query("SET SQL_BIG_SELECTS=1"); 
    $db->query( "SET NAMES utf8");
    $stmt_layout = $db->prepare($dmesqlquery);
    $stmt_layout->execute();

    $datadme = $stmt_layout->fetchAll(PDO::FETCH_ASSOC);

    $datacheck = '';
    if(count($datadme)>0){
        if($layout_type == 'list'){
            foreach($datadme as $key => $value){
                foreach ($value as $key => $val) {
                    if(!empty($val)){
                        $display_string .= "<li>";
                        $datacheck = 1;
                        if($key == 'Occurrence'){
                            $occsql =  "select title from list_options  where list_id='occurrence' AND option_id='$val'";
//                            $frow1 = sqlFetchArray($occsql);
                            $db->query("SET SQL_BIG_SELECTS=1"); 
                            $db->query( "SET NAMES utf8");
                            $stmt_layout = $db->prepare($occsql);
                            $stmt_layout->execute();

                            $frow1 = $stmt_layout->fetchAll(PDO::FETCH_ASSOC);
                             if(!empty($frow1[0]['title']))   
                                 $display_string .= "<b>". ucfirst(str_replace('_',' ',$key.":"))."</b>".$frow1[0]['title'];
                        }else if($key == 'Codes'){
                            // multiple icd codes description
                            $icdcodesarray = explode(";",$val);
                            $icd_description_value = '';
                            for($ic = 0; $ic < count($icdcodesarray) ; $ic++){
                                if(strpos($icdcodesarray[$ic], 'ICD9',0)!== false){
                                    $checkstring = "ICD9:";
                                    $checkstring1 = 'ICD9';
                                    $table = 'icd9_dx_code';
                                }
                                if(strpos($icdcodesarray[$ic], 'ICD10',0)!== false || empty($table)){
                                    $checkstring = "ICD10:";
                                    $table = 'icd10_dx_order_code';
                                    $checkstring1 = 'ICD10';
                                }
                                $exploded_code = str_replace($checkstring, "", $icdcodesarray[$ic]);
                                $icd_description_sql = "SELECT long_desc FROM $table WHERE formatted_dx_code ='$exploded_code' and active= 1";
                                $db->query("SET SQL_BIG_SELECTS=1"); 
                                $db->query( "SET NAMES utf8");
                                $stmt_layout = $db->prepare($icd_description_sql);
                                $stmt_layout->execute();

                                $icd_description = $stmt_layout->fetchAll(PDO::FETCH_ASSOC);
//                                $icd_description = sqlFetchArray($icd_description_sql);
                                if(!empty($icd_description)){
                                    $icd_description_value .=  "( ".$icdcodesarray[$ic] ." ".$icd_description[0]['long_desc'].")<br />";
                                }
                            }
                            $display_string .= "<b>". ucfirst(str_replace('_',' ',$key.":"))."</b>".$icd_description_value;
                        }else if($key != 'id' && $key != 'encounter' && $key != 'enc'){
                            $display_string .= "<b>". ucfirst(str_replace('_',' ',$key.":"))."</b>".$val;
                        }
                        $display_string .= "</li>";
                    }
                }
                if($datacheck == ''){
                    $tempvalue = 1 ;
                }else{
                    $display_string .= "<li>&nbsp;</li>";
                    $display_string2 = str_replace("<div id='show_div_med$encounterid' style='display:none'>","<div id='show_div_med$encounterid' style='display:block'>",$display_string);
                    $tempvalue = 0 ;
                } 
            } 
        }else{
            $display_string .= "<table width='980px' style='border:1px solid black;' cellspacing='0'>";
            $display_string .= "<tr>
                    <th style='border-bottom:1px solid black;text-align:center;' width='50%'><b> Description </b></th> 
                    <th style='border-bottom:1px solid black;text-align:left;' width='10%'><b> Status </b></th> 
                    <th style='border-bottom:1px solid black;text-align:left;' width='20%'><b> Start Date </b></th> 
                    <th style='border-bottom:1px solid black;text-align:left;' width='20%'><b> End Date </b></th> 
                </tr>";
                foreach($datadme as $key =>$value){
                    $pres_array[$key] = $value;
                }
            for($i=0; $i< count($pres_array); $i++){
                if($pres_array[$i]['Occurrence'] != ''){
                    $getoccu = "SELECT title FROM list_options WHERE option_id = '".$pres_array[$i]['Occurrence']."' AND list_id = 'Occurrence'";
//                    $setoccu = sqlFetchArray($getoccu);
                    $db->query("SET SQL_BIG_SELECTS=1"); 
                    $db->query( "SET NAMES utf8");
                    $stmt_layout = $db->prepare($getoccu);
                    $stmt_layout->execute();

                    $setoccu = $stmt_layout->fetchAll(PDO::FETCH_ASSOC);
                    if(!empty($setoccu)){
                        $occurance = $setoccu[0]['title']; 
                    }else{
                        $occurance = '';
                    }    
                }

                if($pres_array[$i]['outcome'] != ''){
                    $getoutcome = "SELECT title FROM list_options WHERE option_id = '".$pres_array[$i]['outcome']."' AND list_id = 'Outcome'";
//                    $setoutcome = sqlFetchArray($getoutcome);
                    $db->query("SET SQL_BIG_SELECTS=1"); 
                    $db->query( "SET NAMES utf8");
                    $stmt_layout = $db->prepare($getoutcome);
                    $stmt_layout->execute();

                    $setoutcome = $stmt_layout->fetchAll(PDO::FETCH_ASSOC);
                    if(!empty($setoutcome)){
                        $outcome = $setoutcome[0]['title']; 
                    }else{
                        $outcome = '';
                    }    
                }
                $display_string .="<tr ><td style='border-bottom:1px solid black;'><table border = '0'>";
                $display_string .= "<tr><td width='50%' class='firsttd'>";
                if(!empty($pres_array[$i]['Title']))
                    $display_string .= $pres_array[$i]['Title']; 
                else
                    $display_string .= "Not Specified. ";
                $display_string .= "</td></tr>";
                // multiple icd codes description
                $icdcodesarray = explode(";",$pres_array[$i]['Codes']);
                $icd_description_value = '';
                for($ic = 0; $ic < count($icdcodesarray) ; $ic++){
                    if(strpos($icdcodesarray[$ic], 'ICD9',0)!== false){
                        $checkstring = "ICD9:";
                        $checkstring1 = 'ICD9';
                        $table = 'icd9_dx_code';
                    }
                    if(strpos($icdcodesarray[$ic], 'ICD10',0)!== false || empty($table)){
                        $checkstring = "ICD10:";
                        $table = 'icd10_dx_order_code';
                        $checkstring1 = 'ICD10';
                    }
                    $exploded_code = str_replace($checkstring, "", $icdcodesarray[$ic]);
                    $icd_description_sql = "SELECT long_desc FROM $table WHERE formatted_dx_code ='$exploded_code' and active= 1";
                    $db->query("SET SQL_BIG_SELECTS=1"); 
                    $db->query( "SET NAMES utf8");
                    $stmt_layout = $db->prepare($icd_description_sql);
                    $stmt_layout->execute();

                    $icd_description = $stmt_layout->fetchAll(PDO::FETCH_ASSOC);
//                    $icd_description = sqlFetchArray($icd_description_sql);
                    if(!empty($icd_description)){
                        $icd_description_value .=  "( ".$icdcodesarray[$ic] ." ".$icd_description[0]['long_desc'].")<br />";
                    }
                }
                if(!empty($pres_array[$i]['Codes']) || !empty($icd_description_value)){
                    $display_string .= "<tr><td class='secondtd'>";
                    $display_string .= $icd_description_value;
                    $display_string .= "</td></tr>";
                }
                
                if($outcome !='Unassigned' ||$occurance !='Unknown'){
                    $display_string .= "<tr><td class='thirdtd'> ";
                    $display_string .= $occurance;
                    if($outcome != 'Unassigned')
                        $display_string .= ", ". $outcome;
                    $display_string .= "</td></tr>";
                }
                if(!empty($pres_array[$i]['ReferredBy'])){
                    $display_string .= "<tr><td class='thirdtd'>";
                    $display_string .= "<span class='byelement'>  by </span>". $pres_array[$i]['ReferredBy'];
                    $display_string .= "</td></tr>";
                }
                
                if(!empty($pres_array[$i]['destination'])){
                    $display_string .= "<tr><td class='thirdtd'> ";
                    $display_string .= $pres_array[$i]['destination'];
                    $display_string .= "</td></tr>";
                }
                $display_string .= "</table></td>";
                $display_string .= "<td width='10%' style='border-bottom:1px solid black;'>".$pres_array[$i]['is_Issue_Active']."</td><td width='20%' style='border-bottom:1px solid black;'>".$pres_array[$i]['Begin_date']."</td><td width='20%' style='border-bottom:1px solid black;'>".$pres_array[$i]['End_date']."</td>";
                $display_string .= "</tr>";
            }
            $display_string .= "</table>";
        }
        $display_string2 = str_replace("<div id='show_div_med$encounterid' style='display:none'>","<div id='show_div_med$encounterid' style='display:block'>",$display_string);
        $tempvalue = 0 ;
    }
//    echo $display_string2;
    $returnarray['tempvalue'] = $tempvalue;
    $returnarray['display_string2'] = $display_string2;
    return $returnarray;
}
function display_cc($encounterid,$groupName,$display_string){
    $db = getConnection();
    $display_string2 = '';
    global $tempvalue;
    $getselectedvales = "SELECT field_id as selectedfield FROM tbl_chartui_mapping WHERE screen_name = '$groupName' AND form_id = 'LBF2' AND group_name LIKE '%Chief Complaint' AND option_value = 'YES'";
    $stmt_sql_array = $db->prepare($getselectedvales) ; 
    $stmt_sql_array->execute();
    $setselectedvalues = $stmt_sql_array->fetchAll(PDO::FETCH_OBJ);
    $listselected = array();
    foreach($setselectedvalues  as $skey => $svalue){
        $listselected[] = $svalue->selectedfield;
    }
    $getCC = "SELECT field_id,title FROM layout_options WHERE form_id = 'LBF2' AND group_name LIKE '%Chief Complaint'";
    $stmt_sql_array2 = $db->prepare($getCC) ; 
    $stmt_sql_array2->execute();
    $setCCResult = $stmt_sql_array2->fetchAll(PDO::FETCH_OBJ);
 
    foreach($setCCResult as $getCCResultkey => $getCCResult){
        for($i=0; $i<count($listselected); $i++){
            if($listselected[$i] == $getCCResult->field_id):
                $fieldid[$getCCResult->title] = $getCCResult->field_id;
            endif;
        }
    }
    if(!empty($fieldid)){
        $getformid = "SELECT form_id FROM forms WHERE encounter = $encounterid AND form_name = 'Allcare Encounter Forms' AND deleted = 0 ";
        $stmt_sql_array3 = $db->prepare($getformid) ; 
        $stmt_sql_array3->execute();
        $setformid = $stmt_sql_array3->fetchAll(PDO::FETCH_OBJ);

        if(!empty($setformid)){
            for($i=0; $i< count($setformid); $i++){
                $formid = $setformid[0]->form_id;
                $datacheck = '';
                foreach($fieldid as $fkey => $fid){
                    $getCCdata = "SELECT field_value FROM lbf_data WHERE form_id = '$formid' AND field_id = '".$fid."'";

                    $stmt_sql_array4 = $db->prepare($getCCdata) ; 
                    $stmt_sql_array4->execute();
                    $getCCdataResult2 = $stmt_sql_array4->fetchAll(PDO::FETCH_OBJ);
                    
                    if(!empty($getCCdataResult2[0]->field_value)){
                        $display_string .= "<li>";
                        if(strcmp($fkey, 'Chief Complaint Text') != 0):
                            $display_string .= "<b>".$fkey.":</b>";
                        endif;
                        $datacheck = 1;
                        if(!empty($getCCdataResult2)):
                            if(strcmp($fkey, 'Chief Complaint Status') == 0):
                                $display_string .=  ucwords(str_replace('|', ',', $getCCdataResult2[0]->field_value))."</li>";
                            else:
                                $display_string .=  nl2br($getCCdataResult2[0]->field_value)."</li>";
                            endif;
                        endif;
                    }
                }
                if($datacheck == ''){
                    $display_string2 = $display_string;
                }else{
                    $tempvalue = 0 ;
                    $display_string2 = str_replace("<div id='show_div_cc$encounterid' style='display:none'>","<div id='show_div_cc$encounterid' style='display:block'>",$display_string);
                }
            }
        } 
    } 
    $returnarray['tempvalue'] = 0;
    $returnarray['display_string2'] = $display_string2;
    return $returnarray;
}
function display_progress($encounterid,$groupName,$display_string){
    $db = getConnection();
    $display_string2 = '';
    global $tempvalue;
    $getselectedvales = "SELECT field_id as selectedfield FROM tbl_chartui_mapping WHERE screen_name = '$groupName' AND form_id = 'LBF2' AND group_name LIKE '%Progress Note' AND option_value = 'YES'";
    $stmt_sql_array = $db->prepare($getselectedvales) ; 
    $stmt_sql_array->execute();
    $setselectedvalues = $stmt_sql_array->fetchAll(PDO::FETCH_OBJ);
    $listselected = array();
    foreach($setselectedvalues  as $skey => $svalue){
        $listselected[] = $svalue->selectedfield;
    }
    $getCC = "SELECT field_id,title FROM layout_options WHERE form_id = 'LBF2' AND group_name LIKE '%Progress Note'";
    $stmt_sql_array2 = $db->prepare($getCC) ; 
    $stmt_sql_array2->execute();
    $setCCResult = $stmt_sql_array2->fetchAll(PDO::FETCH_OBJ);
 
    foreach($setCCResult as $getCCResultkey => $getCCResult){
        for($i=0; $i<count($listselected); $i++){
            if($listselected[$i] == $getCCResult->field_id):
                $fieldid[$getCCResult->title] = $getCCResult->field_id;
            endif;
        }
    }
    if(!empty($fieldid)){
        $getformid = "SELECT form_id FROM forms WHERE encounter = $encounterid AND form_name = 'Allcare Encounter Forms' AND deleted = 0 ";
        $stmt_sql_array3 = $db->prepare($getformid) ; 
        $stmt_sql_array3->execute();
        $setformid = $stmt_sql_array3->fetchAll(PDO::FETCH_OBJ);

        if(!empty($setformid)){
            for($i=0; $i< count($setformid); $i++){
                $formid = $setformid[0]->form_id;
                $datacheck = '';
                foreach($fieldid as $fkey => $fid){
                    $getCCdata = "SELECT field_value FROM lbf_data WHERE form_id = '$formid' AND field_id = '".$fid."'";

                    $stmt_sql_array4 = $db->prepare($getCCdata) ; 
                    $stmt_sql_array4->execute();
                    $getCCdataResult2 = $stmt_sql_array4->fetchAll(PDO::FETCH_OBJ);
                    
                    if(!empty($getCCdataResult2[0]->field_value)){
                        $display_string .= "<li>";
                        if(strcmp($fkey, 'Progress Note Text') != 0):
                            $display_string .= "<b>".$fkey.":</b>";
                        endif;
                        $datacheck = 1;
                        if(!empty($getCCdataResult2)):
                            if(strcmp($fkey, 'Progress Note Status') == 0):
                                $display_string .=  ucwords(str_replace('|', ',', $getCCdataResult2[0]->field_value))."</li>";
                            else:
                                $display_string .=  nl2br($getCCdataResult2[0]->field_value)."</li>";
                            endif;
                        endif;
                    }
                }
                if($datacheck == ''){
                    $display_string2 = $display_string;
                }else{
                    $tempvalue = 0 ;
                    $display_string2 = str_replace("<div id='show_div_progress$encounterid' style='display:none'>","<div id='show_div_progress$encounterid' style='display:block'>",$display_string);
                }
            }
        } 
    } 
    
    $returnarray['tempvalue'] = 0;
    $returnarray['display_string2'] = $display_string2;
    return $returnarray;
}
function display_hpi($encounterid,$groupName,$display_string){
    $db = getConnection();
    $display_string2 = '';
    global $tempvalue;
    $getCC = "SELECT field_id,title FROM layout_options WHERE form_id = 'LBF2' AND group_name LIKE '%History of Present illness'";
    $db->query("SET SQL_BIG_SELECTS=1"); 
    $db->query( "SET NAMES utf8");
    $stmt_sql_array = $db->prepare($getCC) ; 
    $stmt_sql_array->execute();
    $getCCResult = $stmt_sql_array->fetchAll(PDO::FETCH_ASSOC);
    
    $getselectedvales = "SELECT field_id as selectedfield FROM tbl_chartui_mapping WHERE screen_name = '$groupName' AND form_id = 'LBF2' AND group_name LIKE '%History of Present illness' AND option_value = 'YES'";
    $db->query("SET SQL_BIG_SELECTS=1"); 
    $db->query( "SET NAMES utf8");
    $stmt_sql_array = $db->prepare($getselectedvales) ; 
    $stmt_sql_array->execute();
    $setselectedvalues = $stmt_sql_array->fetchAll(PDO::FETCH_ASSOC);
    $listselected = array();
    for($i=0; $i< count($setselectedvalues); $i++){
        $listselected[] = $setselectedvalues[$i]['selectedfield'];
    }
    for($i=0; $i<count($getCCResult); $i++){
        for($j=0; $j<count($listselected); $j++){
            if($listselected[$j] == $getCCResult[$i]['field_id']):
                $fieldid[$getCCResult[$i]['title']] = $getCCResult[$i]['field_id'];
            endif;
        }
    }
    if(!empty($fieldid)):
        $getformid = "SELECT form_id FROM forms WHERE encounter = $encounterid AND form_name = 'Allcare Encounter Forms' AND deleted = 0 ";
        $db->query("SET SQL_BIG_SELECTS=1"); 
        $db->query( "SET NAMES utf8");
        $stmt_sql_array = $db->prepare($getformid) ; 
        $stmt_sql_array->execute();
        $setformid = $stmt_sql_array->fetchAll(PDO::FETCH_ASSOC);
        
//        $setformid=sqlFetchArray($getformid);
        if(!empty($setformid)):
        for($i=0; $i< count($setformid); $i++){
            $formid = $setformid[$i]['form_id'];
            $datacheck = '';
            foreach($fieldid as $fkey => $fid){
                $getCCdata = "SELECT field_value FROM lbf_data WHERE form_id = '$formid' AND field_id = '".$fid."'";
                $db->query("SET SQL_BIG_SELECTS=1"); 
                $db->query( "SET NAMES utf8");
                $stmt_sql_array = $db->prepare($getCCdata) ; 
                $stmt_sql_array->execute();
                $getCCdataResult2 = $stmt_sql_array->fetchAll(PDO::FETCH_ASSOC);
//                $getCCdataResult2=sqlFetchArray($getCCdata);
                if(!empty($getCCdataResult2[0]['field_value'])){
                    $datacheck = 1;
                    $display_string .= "<li>";
                    if(strcmp($fkey, 'HPI Text') != 0):
                        $display_string .= "<b>".$fkey.":</b>";
                    endif;
                    if(!empty($getCCdataResult2)):
                        if($fkey == 'HPI Status'):
                            $display_string .=  ucwords(str_replace('|', ',', $getCCdataResult2[0]['field_value']))."</li>";
                        else:
                            $display_string .= nl2br($getCCdataResult2[0]['field_value'])."</li>";
                        endif;
                    endif;
                }
            }
//            if($datacheck == '')
//            echo "<h2>HPI Data</h2>";
            $check = '';
            $display_string .= "<li>";
            $gethistorypast = "SELECT field_value FROM lbf_data WHERE form_id = '$formid' AND field_id = 're_up_patient1'";
//            $sethistorypast=sqlFetchArray($gethistorypast);
            $db->query("SET SQL_BIG_SELECTS=1"); 
            $db->query( "SET NAMES utf8");
            $stmt_sql_array = $db->prepare($gethistorypast) ; 
            $stmt_sql_array->execute();
            $sethistorypast = $stmt_sql_array->fetchAll(PDO::FETCH_ASSOC);
            
            if(!empty($sethistorypast[0]['field_value'])){
                $display_string .= "<b> Past Medical History:</b>".$sethistorypast[0]['field_value'];
                $check = 1;
            }    
            $getfamilyhistory = "SELECT field_value FROM lbf_data WHERE form_id = '$formid' AND field_id = 're_up_patient2'";
            $db->query("SET SQL_BIG_SELECTS=1"); 
            $db->query( "SET NAMES utf8");
            $stmt_sql_array = $db->prepare($getfamilyhistory) ; 
            $stmt_sql_array->execute();
            $setfamilyhistory = $stmt_sql_array->fetchAll(PDO::FETCH_ASSOC);
            
//            $setfamilyhistory=sqlFetchArray($getfamilyhistory);
            if(!empty($setfamilyhistory[0]['field_value'])){
                $display_string .= "<br><b> Family History:</b>".$setfamilyhistory[0]['field_value'];
                $check = 1;
            }   
            $getprimaryfamilyhistory = "SELECT field_value FROM lbf_data WHERE form_id = '$formid' AND field_id = 're_up_patient3'";
//            $setprimaryfamilyhistory=sqlFetchArray($getprimaryfamilyhistory);
            $db->query("SET SQL_BIG_SELECTS=1"); 
            $db->query( "SET NAMES utf8");
            $stmt_sql_array = $db->prepare($getprimaryfamilyhistory) ; 
            $stmt_sql_array->execute();
            $setprimaryfamilyhistory = $stmt_sql_array->fetchAll(PDO::FETCH_ASSOC);
            if(!empty($setprimaryfamilyhistory[0]['field_value'])){
                $display_string .= "<br><b> Primary Family Med Conditions:</b>".$setprimaryfamilyhistory[0]['field_value'];
                $check = 1;
            }   
//            $gettestexams = sqlStatement("SELECT field_value FROM lbf_data WHERE form_id = '$formid' AND field_id = 're_up_patient4'");
//            $settestexams=sqlFetchArray($gettestexams);
//            if(!empty($settestexams['field_value'])){
//                echo "<br><b> Tests and Exams:</b>".$settestexams['field_value'];
//                $check = 1;
//            }  
            $getsocialhistory = "SELECT field_value FROM lbf_data WHERE form_id = '$formid' AND field_id = 're_up_patient5'";
            $db->query("SET SQL_BIG_SELECTS=1"); 
            $db->query( "SET NAMES utf8");
            $stmt_sql_array = $db->prepare($getsocialhistory) ; 
            $stmt_sql_array->execute();
            $setsocialhistory = $stmt_sql_array->fetchAll(PDO::FETCH_ASSOC);
//            $setsocialhistory=sqlFetchArray($getsocialhistory);
            if(!empty($setsocialhistory[0]['field_value'])){
                $display_string .= "<br><b> Social History:</b>".$setsocialhistory[0]['field_value'];
                $check = 1;
            } 
            $display_string .= "</li>";
            if($datacheck == '' && $check == ''){
                $display_string2 = $display_string;
            }else{
                $tempvalue = 0 ;
                $display_string2 = str_replace("<div id='show_div_hpi$encounterid' style='display:none'>","<div id='show_div_hpi$encounterid' style='display:block'>",$display_string);
            }
        }
        
        endif;
    endif;
    $returnarray['tempvalue'] = $tempvalue;
    $returnarray['display_string2'] = $display_string2;
    return $returnarray;
}
function display_assess($encounterid,$groupName,$display_string){
    $db = getConnection();

    global $tempvalue;
    $getselectedvales = "SELECT field_id as selectedfield FROM tbl_chartui_mapping WHERE screen_name = '$groupName' AND form_id = 'LBF2' AND group_name LIKE '%Assessment Note' AND option_value = 'YES'";
    $stmt_sql_array = $db->prepare($getselectedvales) ; 
    $stmt_sql_array->execute();
    $setselectedvalues = $stmt_sql_array->fetchAll(PDO::FETCH_OBJ);
    $listselected = array();
    foreach($setselectedvalues  as $skey => $svalue){
        $listselected[] = $svalue->selectedfield;
    }
    $getCC = "SELECT field_id,title FROM layout_options WHERE form_id = 'LBF2' AND group_name LIKE '%Assessment Note'";
    $stmt_sql_array2 = $db->prepare($getCC) ; 
    $stmt_sql_array2->execute();
    $setCCResult = $stmt_sql_array2->fetchAll(PDO::FETCH_OBJ);
 
    foreach($setCCResult as $getCCResultkey => $getCCResult){
        for($i=0; $i<count($listselected); $i++){
            if($listselected[$i] == $getCCResult->field_id):
                $fieldid[$getCCResult->title] = $getCCResult->field_id;
            endif;
        }
    }
    if(!empty($fieldid)){
        $getformid = "SELECT form_id FROM forms WHERE encounter = $encounterid AND form_name = 'Allcare Encounter Forms' AND deleted = 0 ";
        $stmt_sql_array3 = $db->prepare($getformid) ; 
        $stmt_sql_array3->execute();
        $setformid = $stmt_sql_array3->fetchAll(PDO::FETCH_OBJ);

        if(!empty($setformid)){
            for($i=0; $i< count($setformid); $i++){
                $formid = $setformid[0]->form_id;
                $datacheck = '';
                foreach($fieldid as $fkey => $fid){
                    $getCCdata = "SELECT field_value FROM lbf_data WHERE form_id = '$formid' AND field_id = '".$fid."'";

                    $stmt_sql_array4 = $db->prepare($getCCdata) ; 
                    $stmt_sql_array4->execute();
                    $getCCdataResult2 = $stmt_sql_array4->fetchAll(PDO::FETCH_OBJ);
                    
                    if(!empty($getCCdataResult2[0]->field_value)){
                        $display_string .= "<li>";
                        if(strcmp($fkey, 'Assessment Note Text') != 0):
                            $display_string .= "<b>".$fkey.":</b>";
                        endif;
                        $datacheck = 1;
                        if(!empty($getCCdataResult2)):
                            if(strcmp($fkey, 'Assessment Note Status') == 0):
                                $display_string .=  ucwords(str_replace('|', ',', $getCCdataResult2[0]->field_value))."</li>";
                            else:
                                $display_string .=  nl2br($getCCdataResult2[0]->field_value)."</li>";
                            endif;
                        endif;
                    }
                }
                if($datacheck == ''){
                    $display_string2 = $display_string;
                }else{
                    $tempvalue = 0 ;
                    $display_string2 = str_replace("<div id='show_div_assess$encounterid' style='display:none'>","<div id='show_div_assess$encounterid' style='display:block'>",$display_string);
                }
            }
        } 
    } 
    
    $returnarray['tempvalue'] = 0;
    $returnarray['display_string2'] = '';
    return $returnarray;
}
function display_plan($encounterid,$groupName,$display_string){
    $db = getConnection();
    $display_string2 = '';
    global $tempvalue;
    $getselectedvales = "SELECT field_id as selectedfield FROM tbl_chartui_mapping WHERE screen_name = '$groupName' AND form_id = 'LBF2' AND group_name LIKE '%Plan Note' AND option_value = 'YES'";
    $stmt_sql_array = $db->prepare($getselectedvales) ; 
    $stmt_sql_array->execute();
    $setselectedvalues = $stmt_sql_array->fetchAll(PDO::FETCH_OBJ);
    $listselected  = array();
//    echo "<pre>"; print_r($setselectedvalues); echo "</pre>";exit();
    foreach($setselectedvalues  as $skey => $svalue){
        $listselected[] = $svalue->selectedfield;
    }
    $getCC = "SELECT field_id,title FROM layout_options WHERE form_id = 'LBF2' AND group_name LIKE '%Plan Note'";
    $stmt_sql_array2 = $db->prepare($getCC) ; 
    $stmt_sql_array2->execute();
    $setCCResult = $stmt_sql_array2->fetchAll(PDO::FETCH_OBJ);
    
    foreach($setCCResult as $getCCResultkey => $getCCResult){
        for($i=0; $i<count($listselected); $i++){
            if($listselected[$i] == $getCCResult->field_id):
                $fieldid[$getCCResult->title] = $getCCResult->field_id;
            endif;
        }
    }
    if(!empty($fieldid)){
        $getformid = "SELECT form_id FROM forms WHERE encounter = $encounterid AND form_name = 'Allcare Encounter Forms' AND deleted = 0 ";
        $stmt_sql_array3 = $db->prepare($getformid) ; 
        $stmt_sql_array3->execute();
        $setformid = $stmt_sql_array3->fetchAll(PDO::FETCH_OBJ);

        if(!empty($setformid)){
            for($i=0; $i< count($setformid); $i++){
                $formid = $setformid[0]->form_id;
                $datacheck = '';
                foreach($fieldid as $fkey => $fid){
                    $getCCdata = "SELECT field_value FROM lbf_data WHERE form_id = '$formid' AND field_id = '".$fid."'";

                    $stmt_sql_array4 = $db->prepare($getCCdata) ; 
                    $stmt_sql_array4->execute();
                    $getCCdataResult2 = $stmt_sql_array4->fetchAll(PDO::FETCH_OBJ);
                    
                    if(!empty($getCCdataResult2[0]->field_value)){
                        $display_string .= "<li>";
                        if(strcmp($fkey, 'Plan Note Text') != 0):
                            $display_string .= "<b>".$fkey.":</b>";
                        endif;
                        $datacheck = 1;
                        if(!empty($getCCdataResult2)):
                            if(strcmp($fkey, 'Plan Note Status') == 0):
                                $display_string .=  ucwords(str_replace('|', ',', $getCCdataResult2[0]->field_value))."</li>";
                            else:
                                $display_string .=  nl2br($getCCdataResult2[0]->field_value)."</li>";
                            endif;
                        endif;
                    }
                }
                if($datacheck == ''){
                    $display_string2 = $display_string;
                }else{
                    $tempvalue = 0 ;
                    $display_string2 = str_replace("<div id='show_div_plan$encounterid' style='display:none'>","<div id='show_div_plan$encounterid' style='display:block'>",$display_string);
                }
            }
        } 
    } 
    
    $returnarray['tempvalue'] = 0;
    $returnarray['display_string2'] = $display_string2;
    return $returnarray;
}
function display_mproblem($encounterid,$layout_type,$display_string,$pid){
    $db = getConnection();
    global $tempvalue;
//    $MedicalProblemssql = sqlStatement('SET SQL_BIG_SELECTS=1'); 
//    $MedicalProblemssql = sqlStatement("SET NAMES utf8");
    $medicalProblemssqlquery = '';
    $display_string2 = '';
    if($encounterid != 0){
        $medicalProblemssqlquery .= "SELECT DISTINCT l.id, l.title AS Title, 
                                    CASE 
                                    WHEN enddate IS NULL 
                                    THEN  'Active'
                                    ELSE  'Inactive'
                                    END AS is_Issue_Active, outcome, destination, begdate AS Begin_date, enddate AS End_date, diagnosis AS Codes, occurrence AS Occurrence, referredby AS ReferredBy, comments
                                    FROM lists AS l
                                    INNER JOIN issue_encounter AS ie ON ie.list_id = l.id
                                    AND ie.encounter = $encounterid
                                    WHERE l.type =  'medical_problem'
                                    AND l.pid = ".$pid."
                                    ORDER BY is_Issue_Active ASC , begdate DESC ";
    }else{
       $medicalProblemssqlquery .= " SELECT DISTINCT l.id, l.title AS Title, 
                                    CASE 
                                    WHEN enddate IS NULL 
                                    THEN  'Active'
                                    ELSE  'Inactive'
                                    END AS is_Issue_Active, outcome, destination, begdate AS Begin_date, enddate AS End_date, diagnosis AS Codes, occurrence AS Occurrence, referredby AS ReferredBy, comments
                                    FROM lists AS l
                                    WHERE l.type =  'medical_problem'
                                    AND l.pid =".$pid." AND l.id NOT IN ( SELECT DISTINCT list_id FROM issue_encounter WHERE pid =".$pid." ) 
                                    ORDER BY is_Issue_Active ASC , begdate DESC";
    }

    $db->query("SET SQL_BIG_SELECTS=1"); 
    $db->query( "SET NAMES utf8");
    $stmt_layout = $db->prepare($medicalProblemssqlquery);
    $stmt_layout->execute();

    $dataMedicalProblems = $stmt_layout->fetchAll(PDO::FETCH_ASSOC);

    $datacheck = '';
    if(count($dataMedicalProblems)>0){
        if($layout_type == 'list'){
            foreach($dataMedicalProblems as $key => $value){
                foreach ($value as $key => $val) {
                    if(!empty($val)){
                        $display_string .= "<li>";
                        $datacheck = 1;
                        if($key == 'Occurrence'){
                            $occsql =  "select title from list_options  where list_id='occurrence' AND option_id='$val'";
//                            $frow1 = sqlFetchArray($occsql);
                            $db->query("SET SQL_BIG_SELECTS=1"); 
                            $db->query( "SET NAMES utf8");
                            $stmt_layout = $db->prepare($occsql);
                            $stmt_layout->execute();

                            $frow1 = $stmt_layout->fetchAll(PDO::FETCH_ASSOC);
                             if(!empty($frow1[0]['title']))   
                                 $display_string .= "<b>". ucfirst(str_replace('_',' ',$key.":"))."</b>".$frow1[0]['title'];
                        }else if($key == 'Codes'){
                            // multiple icd codes description
                            $icdcodesarray = explode(";",$val);
                            $icd_description_value = '';
                            for($ic = 0; $ic < count($icdcodesarray) ; $ic++){
                                if(strpos($icdcodesarray[$ic], 'ICD9',0)!== false){
                                    $checkstring = "ICD9:";
                                    $checkstring1 = 'ICD9';
                                    $table = 'icd9_dx_code';
                                }
                                if(strpos($icdcodesarray[$ic], 'ICD10',0)!== false || empty($table)){
                                    $checkstring = "ICD10:";
                                    $table = 'icd10_dx_order_code';
                                    $checkstring1 = 'ICD10';
                                }
                                $exploded_code = str_replace($checkstring, "", $icdcodesarray[$ic]);
                                $icd_description_sql = "SELECT long_desc FROM $table WHERE formatted_dx_code ='$exploded_code' and active= 1";
                                $db->query("SET SQL_BIG_SELECTS=1"); 
                                $db->query( "SET NAMES utf8");
                                $stmt_layout = $db->prepare($icd_description_sql);
                                $stmt_layout->execute();

                                $icd_description = $stmt_layout->fetchAll(PDO::FETCH_ASSOC);
//                                $icd_description = sqlFetchArray($icd_description_sql);
                                if(!empty($icd_description)){
                                    $icd_description_value .=  "( ".$icdcodesarray[$ic] ." ".$icd_description[0]['long_desc'].")<br />";
                                }
                            }
                            $display_string .= "<b>". ucfirst(str_replace('_',' ',$key.":"))."</b>".$icd_description_value;
                        }else if($key != 'id' && $key != 'encounter' && $key != 'enc'){
                            $display_string .= "<b>". ucfirst(str_replace('_',' ',$key.":"))."</b>".$val;
                        }
                        $display_string .= "</li>";
                    }
                }
                if($datacheck == ''){
                    $tempvalue = 1 ;
                }else{
                    $display_string .= "<li>&nbsp;</li>";
                    $display_string2 = str_replace("<div id='show_div_mproblem$encounterid' style='display:none'>","<div id='show_div_mproblem$encounterid' style='display:block'>",$display_string);
                    $tempvalue = 0 ;
                } 
            } 
        }else{
            $display_string .= "<table width='980px' style='border:1px solid black;' cellspacing='0'>";
            $display_string .= "<tr>
                    <th style='border-bottom:1px solid black;text-align:center;' width='50%'><b> Description </b></th> 
                    <th style='border-bottom:1px solid black;text-align:left;' width='10%'><b> Status </b></th> 
                    <th style='border-bottom:1px solid black;text-align:left;' width='20%'><b> Start Date </b></th> 
                    <th style='border-bottom:1px solid black;text-align:left;' width='20%'><b> End Date </b></th> 
                </tr>";
                foreach($dataMedicalProblems as $key =>$value){
                    $pres_array[$key] = $value;
                }
            for($i=0; $i< count($pres_array); $i++){
                if($pres_array[$i]['Occurrence'] != ''){
                    $getoccu = "SELECT title FROM list_options WHERE option_id = '".$pres_array[$i]['Occurrence']."' AND list_id = 'Occurrence'";
//                    $setoccu = sqlFetchArray($getoccu);
                    $db->query("SET SQL_BIG_SELECTS=1"); 
                    $db->query( "SET NAMES utf8");
                    $stmt_layout = $db->prepare($getoccu);
                    $stmt_layout->execute();

                    $setoccu = $stmt_layout->fetchAll(PDO::FETCH_ASSOC);
                    if(!empty($setoccu)){
                        $occurance = $setoccu[0]['title']; 
                    }else{
                        $occurance = '';
                    }    
                }

                if($pres_array[$i]['outcome'] != ''){
                    $getoutcome = "SELECT title FROM list_options WHERE option_id = '".$pres_array[$i]['outcome']."' AND list_id = 'Outcome'";
//                    $setoutcome = sqlFetchArray($getoutcome);
                    $db->query("SET SQL_BIG_SELECTS=1"); 
                    $db->query( "SET NAMES utf8");
                    $stmt_layout = $db->prepare($getoutcome);
                    $stmt_layout->execute();

                    $setoutcome = $stmt_layout->fetchAll(PDO::FETCH_ASSOC);
                    if(!empty($setoutcome)){
                        $outcome = $setoutcome[0]['title']; 
                    }else{
                        $outcome = '';
                    }    
                }
                $display_string .="<tr ><td style='border-bottom:1px solid black;'><table border = '0'>";
                $display_string .= "<tr><td width='50%' class='firsttd'>";
                if(!empty($pres_array[$i]['Title']))
                    $display_string .= $pres_array[$i]['Title']; 
                else
                    $display_string .= "Not Specified. ";
                $display_string .= "</td></tr>";
                // multiple icd codes description
                $icdcodesarray = explode(";",$pres_array[$i]['Codes']);
                $icd_description_value = '';
                for($ic = 0; $ic < count($icdcodesarray) ; $ic++){
                    if(strpos($icdcodesarray[$ic], 'ICD9',0)!== false){
                        $checkstring = "ICD9:";
                        $checkstring1 = 'ICD9';
                        $table = 'icd9_dx_code';
                    }
                    if(strpos($icdcodesarray[$ic], 'ICD10',0)!== false || empty($table)){
                        $checkstring = "ICD10:";
                        $table = 'icd10_dx_order_code';
                        $checkstring1 = 'ICD10';
                    }
                    $exploded_code = str_replace($checkstring, "", $icdcodesarray[$ic]);
                    $icd_description_sql = "SELECT long_desc FROM $table WHERE formatted_dx_code ='$exploded_code' and active= 1";
                    $db->query("SET SQL_BIG_SELECTS=1"); 
                    $db->query( "SET NAMES utf8");
                    $stmt_layout = $db->prepare($icd_description_sql);
                    $stmt_layout->execute();

                    $icd_description = $stmt_layout->fetchAll(PDO::FETCH_ASSOC);
//                    $icd_description = sqlFetchArray($icd_description_sql);
                    if(!empty($icd_description)){
                        $icd_description_value .=  "( ".$icdcodesarray[$ic] ." ".$icd_description[0]['long_desc'].")<br />";
                    }
                }
                if(!empty($pres_array[$i]['Codes']) || !empty($icd_description_value)){
                    $display_string .= "<tr><td class='secondtd'>";
                    $display_string .= $icd_description_value;
                    $display_string .= "</td></tr>";
                }
                
                if($outcome !='Unassigned' ||$occurance !='Unknown'){
                    $display_string .= "<tr><td class='thirdtd'> ";
                    $display_string .= $occurance;
                    if($outcome != 'Unassigned')
                        $display_string .= ", ". $outcome;
                    $display_string .= "</td></tr>";
                }
                if(!empty($pres_array[$i]['ReferredBy'])){
                    $display_string .= "<tr><td class='thirdtd'>";
                    $display_string .= $pres_array[$i]['Begin_date']. "<span class='byelement'>  by </span>". $pres_array[$i]['ReferredBy'];
                    $display_string .= "</td></tr>";
                }
                
                if(!empty($pres_array[$i]['destination'])){
                    $display_string .= "<tr><td class='thirdtd'> ";
                    $display_string .= $pres_array[$i]['destination'];
                    $display_string .= "</td></tr>";
                }
                $display_string .= "</table></td>";
                $display_string .= "<td width='10%' style='border-bottom:1px solid black;'>".$pres_array[$i]['is_Issue_Active']."</td><td width='20%' style='border-bottom:1px solid black;'>".$pres_array[$i]['Begin_date']."</td><td width='20%' style='border-bottom:1px solid black;'>".$pres_array[$i]['End_date']."</td>";
                $display_string .= "</tr>";
            }
            $display_string .= "</table>";
        }
        $display_string2 = str_replace("<div id='show_div_mproblem$encounterid' style='display:none'>","<div id='show_div_mproblem$encounterid' style='display:block'>",$display_string);
        $tempvalue = 0 ;
    }
    $returnarray['tempvalue'] = $tempvalue;
    $returnarray['display_string2'] = $display_string2;
    return $returnarray;
}        
function display_allergy($encounterid,$layout_type,$display_string,$pid){
    $db = getConnection();
    global $tempvalue;
//    $allergysql = sqlStatement('SET SQL_BIG_SELECTS=1'); 
//    $allergysql = sqlStatement("SET NAMES utf8");
    $allegrysqlquery = '';
    $display_string2 = '';
    if($encounterid != 0){
        $allegrysqlquery .= "SELECT DISTINCT l.id, l.title AS Title, 
                                    CASE 
                                    WHEN enddate IS NULL 
                                    THEN  'Active'
                                    ELSE  'Inactive'
                                    END AS is_Issue_Active, outcome, destination, begdate AS Begin_date, enddate AS End_date, diagnosis AS Codes, occurrence AS Occurrence, referredby AS ReferredBy, comments
                                    FROM lists AS l
                                    INNER JOIN issue_encounter AS ie ON ie.list_id = l.id
                                    AND ie.encounter = $encounterid
                                    WHERE l.type =  'allergy'
                                    AND l.pid = ".$pid."
                                    ORDER BY is_Issue_Active ASC , begdate DESC ";
    }else{
       $allegrysqlquery .= " SELECT DISTINCT l.id, l.title AS Title, 
                                    CASE 
                                    WHEN enddate IS NULL 
                                    THEN  'Active'
                                    ELSE  'Inactive'
                                    END AS is_Issue_Active, outcome, destination, begdate AS Begin_date, enddate AS End_date, diagnosis AS Codes, occurrence AS Occurrence, referredby AS ReferredBy, comments
                                    FROM lists AS l
                                    WHERE l.type =  'allergy'
                                    AND l.pid =".$pid." AND l.id NOT IN ( SELECT DISTINCT list_id FROM issue_encounter WHERE pid =".$pid." ) 
                                    ORDER BY is_Issue_Active ASC , begdate DESC";
    }
//    $allegrysql = sqlStatement($allegrysqlquery);

    $db->query("SET SQL_BIG_SELECTS=1"); 
    $db->query( "SET NAMES utf8");
    $stmt_layout = $db->prepare($allegrysqlquery);
    $stmt_layout->execute();

    $dataallergy = $stmt_layout->fetchAll(PDO::FETCH_ASSOC);
//    echo "<pre>"; print_r($allegrysql); echo "</pre>";exit();
    //while ($frow = sqlFetchArray($allegrysql)) {
//    for($fkey= 0; $fkey< count($allegrysql); $fkey++){
//        foreach($allegrysql[$fkey] as $frow=>$fcolumn)
//            $dataallergy[$fkey][$frow] = $fcolumn; 
//    }
//    echo "<pre>"; print_r($allegrysql); echo "</pre>";exit();
    $datacheck = '';
    if(count($dataallergy)>0){
        if($layout_type == 'list'){
            foreach($dataallergy as $key => $value){
                foreach ($value as $key => $val) {
                    if(!empty($val)){
                        $display_string .= "<li>";
                        $datacheck = 1;
                        if($key == 'Occurrence'){
                            $occsql =  "select title from list_options  where list_id='occurrence' AND option_id='$val'";
//                            $frow1 = sqlFetchArray($occsql);
                            $db->query("SET SQL_BIG_SELECTS=1"); 
                            $db->query( "SET NAMES utf8");
                            $stmt_layout = $db->prepare($occsql);
                            $stmt_layout->execute();

                            $frow1 = $stmt_layout->fetchAll(PDO::FETCH_ASSOC);
                             if(!empty($frow1[0]['title']))   
                                 $display_string .= "<b>". ucfirst(str_replace('_',' ',$key.":"))."</b>".$frow1[0]['title'];
                        }else if($key == 'Codes'){
                            // multiple icd codes description
                            $icdcodesarray = explode(";",$val);
                            $icd_description_value = '';
                            for($ic = 0; $ic < count($icdcodesarray) ; $ic++){
                                if(strpos($icdcodesarray[$ic], 'ICD9',0)!== false){
                                    $checkstring = "ICD9:";
                                    $checkstring1 = 'ICD9';
                                    $table = 'icd9_dx_code';
                                }
                                if(strpos($icdcodesarray[$ic], 'ICD10',0)!== false || empty($table)){
                                    $checkstring = "ICD10:";
                                    $table = 'icd10_dx_order_code';
                                    $checkstring1 = 'ICD10';
                                }
                                $exploded_code = str_replace($checkstring, "", $icdcodesarray[$ic]);
                                $icd_description_sql = "SELECT long_desc FROM $table WHERE formatted_dx_code ='$exploded_code' and active= 1";
                                $db->query("SET SQL_BIG_SELECTS=1"); 
                                $db->query( "SET NAMES utf8");
                                $stmt_layout = $db->prepare($icd_description_sql);
                                $stmt_layout->execute();

                                $icd_description = $stmt_layout->fetchAll(PDO::FETCH_ASSOC);
//                                $icd_description = sqlFetchArray($icd_description_sql);
                                if(!empty($icd_description)){
                                    $icd_description_value .=  "( ".$icdcodesarray[$ic] ." ".$icd_description[0]['long_desc'].")<br />";
                                }
                            }
                            $display_string .= "<b>". ucfirst(str_replace('_',' ',$key.":"))."</b>".$icd_description_value;
                        }else if($key != 'id' && $key != 'encounter' && $key != 'enc'){
                            $display_string .= "<b>". ucfirst(str_replace('_',' ',$key.":"))."</b>".$val;
                        }
                        $display_string .= "</li>";
                    }
                }
                if($datacheck == ''){
                    $tempvalue = 1 ;
                }else{
                    $display_string .= "<li>&nbsp;</li>";
                    $display_string2 = str_replace("<div id='show_div_allergy$encounterid' style='display:none'>","<div id='show_div_allergy$encounterid' style='display:block'>",$display_string);
                    
                    $tempvalue = 0 ;
                } 
            } 
        }else{
            $display_string .= "<table width='980px' style='border:1px solid black;' cellspacing='0'>";
            $display_string .= "<tr>
                    <th style='border-bottom:1px solid black;text-align:center;' width='50%'><b> Description </b></th> 
                    <th style='border-bottom:1px solid black;text-align:left;' width='10%'><b> Status </b></th> 
                    <th style='border-bottom:1px solid black;text-align:left;' width='20%'><b> Start Date </b></th> 
                    <th style='border-bottom:1px solid black;text-align:left;' width='20%'><b> End Date </b></th> 
                </tr>";
                foreach($dataallergy as $key =>$value){
                    $pres_array[$key] = $value;
                }
            for($i=0; $i< count($pres_array); $i++){
                if($pres_array[$i]['Occurrence'] != ''){
                    $getoccu = "SELECT title FROM list_options WHERE option_id = '".$pres_array[$i]['Occurrence']."' AND list_id = 'Occurrence'";
//                    $setoccu = sqlFetchArray($getoccu);
                    $db->query("SET SQL_BIG_SELECTS=1"); 
                    $db->query( "SET NAMES utf8");
                    $stmt_layout = $db->prepare($getoccu);
                    $stmt_layout->execute();

                    $setoccu = $stmt_layout->fetchAll(PDO::FETCH_ASSOC);
                    if(!empty($setoccu)){
                        $occurance = $setoccu[0]['title']; 
                    }else{
                        $occurance = '';
                    }    
                }

                if($pres_array[$i]['outcome'] != ''){
                    $getoutcome = "SELECT title FROM list_options WHERE option_id = '".$pres_array[$i]['outcome']."' AND list_id = 'Outcome'";
//                    $setoutcome = sqlFetchArray($getoutcome);
                    $db->query("SET SQL_BIG_SELECTS=1"); 
                    $db->query( "SET NAMES utf8");
                    $stmt_layout = $db->prepare($getoutcome);
                    $stmt_layout->execute();

                    $setoutcome = $stmt_layout->fetchAll(PDO::FETCH_ASSOC);
                    if(!empty($setoutcome)){
                        $outcome = $setoutcome[0]['title']; 
                    }else{
                        $outcome = '';
                    }    
                }
                $display_string .="<tr><td style='border-bottom:1px solid black;'><table border = '0'>";
                $display_string .= "<tr><td width='50%' class='firsttd'>";
                if(!empty($pres_array[$i]['Title']))
                    $display_string .= $pres_array[$i]['Title']; 
                else
                    $display_string .= "Not Specified. ";
                $display_string .= "</td></tr>";
                // multiple icd codes description
                $icdcodesarray = explode(";",$pres_array[$i]['Codes']);
                $icd_description_value = '';
                for($ic = 0; $ic < count($icdcodesarray) ; $ic++){
                    if(strpos($icdcodesarray[$ic], 'ICD9',0)!== false){
                        $checkstring = "ICD9:";
                        $checkstring1 = 'ICD9';
                        $table = 'icd9_dx_code';
                    }
                    if(strpos($icdcodesarray[$ic], 'ICD10',0)!== false || empty($table)){
                        $checkstring = "ICD10:";
                        $table = 'icd10_dx_order_code';
                        $checkstring1 = 'ICD10';
                    }
                    $exploded_code = str_replace($checkstring, "", $icdcodesarray[$ic]);
                    $icd_description_sql = "SELECT long_desc FROM $table WHERE formatted_dx_code ='$exploded_code' and active= 1";
                    $db->query("SET SQL_BIG_SELECTS=1"); 
                    $db->query( "SET NAMES utf8");
                    $stmt_layout = $db->prepare($icd_description_sql);
                    $stmt_layout->execute();

                    $icd_description = $stmt_layout->fetchAll(PDO::FETCH_ASSOC);
//                    $icd_description = sqlFetchArray($icd_description_sql);
                    if(!empty($icd_description)){
                        $icd_description_value .=  "( ".$icdcodesarray[$ic] ." ".$icd_description[0]['long_desc'].")<br />";
                    }
                }
                if(!empty($pres_array[$i]['Codes']) || !empty($icd_description_value)){
                    $display_string .= "<tr><td class='secondtd'>";
                    $display_string .= $icd_description_value;
                    $display_string .= "</td></tr>";
                }
                
                if($outcome !='Unassigned' ||$occurance !='Unknown'){
                    $display_string .= "<tr><td class='thirdtd'> ";
                    $display_string .= $occurance;
                    if($outcome != 'Unassigned')
                        $display_string .= ", ". $outcome;
                    $display_string .= "</td></tr>";
                }
                if(!empty($pres_array[$i]['ReferredBy'])){
                    $display_string .= "<tr><td class='thirdtd'>";
                    $display_string .= $pres_array[$i]['Begin_date']. "<span class='byelement'>  by </span>". $pres_array[$i]['ReferredBy'];
                    $display_string .= "</td></tr>";
                }
                
                if(!empty($pres_array[$i]['destination'])){
                    $display_string .= "<tr><td class='thirdtd'> ";
                    $display_string .= $pres_array[$i]['destination'];
                    $display_string .= "</td></tr>";
                }
                $display_string .= "</table></td>";
                $display_string .= "<td width='10%' style='border-bottom:1px solid black;'>".$pres_array[$i]['is_Issue_Active']."</td><td width='20%' style='border-bottom:1px solid black;'>".$pres_array[$i]['Begin_date']."</td><td width='20%' style='border-bottom:1px solid black;'>".$pres_array[$i]['End_date']."</td>";
                $display_string .= "</tr>";
            }
            $display_string .= "</table>";
        }
        $display_string2 = str_replace("<div id='show_div_allergy$encounterid' style='display:none'>","<div id='show_div_allergy$encounterid' style='display:block'>",$display_string);
        $tempvalue = 0 ;
    }
//    echo ">>>>>>>>>";echo htmlentities($display_string2);echo "+++++++++++";
    $returnarray['tempvalue'] = $tempvalue;
    $returnarray['display_string2'] = $display_string2;
    return $returnarray;
}
function display_surgery($encounterid,$layout_type,$display_string,$pid){
    $db = getConnection();
    global $tempvalue;
    $display_string .= "<br>";
    $display_string2 = '';
//    if($layout_type == 'grid'){
//        $display_string .= "<style>
//            .tbl_surgery {border:1px solid #C3E7F5;background-color: #E0E0E0 ; }
//            .tbl_surgery_th
//            {border:0px 1px 1px 0px solid  #C3E7F5;;text-align:center; background-color:  #E0E0E0;}
//            .tbl_surgery_td 
//            {border:0px 1px 1px 0px solid  ;text-align:center; background:#FFFFFF ;}
//           </style>";
//    }
    $surgerysql2 = '';
    if($encounterid != 0){
        $surgerysql2 = "SELECT DISTINCT l.id, l.title AS Title, 
                                    CASE 
                                    WHEN enddate IS NULL 
                                    THEN  'Active'
                                    ELSE  'Inactive'
                                    END AS is_Issue_Active, outcome, destination, begdate AS Begin_date, enddate AS End_date, diagnosis AS Codes, occurrence AS Occurrence, referredby AS ReferredBy, comments
                                    FROM lists AS l
                                    INNER JOIN issue_encounter AS ie ON ie.list_id = l.id
                                    AND ie.encounter = $encounterid
                                    WHERE l.type =  'surgery'
                                    AND l.pid = ".$pid."
                                    ORDER BY is_Issue_Active ASC , begdate DESC";
    }else{
       $surgerysql2 = "SELECT DISTINCT l.id, l.title AS Title, 
                                    CASE 
                                    WHEN enddate IS NULL 
                                    THEN  'Active'
                                    ELSE  'Inactive'
                                    END AS is_Issue_Active, outcome, destination, begdate AS Begin_date, enddate AS End_date, diagnosis AS Codes, occurrence AS Occurrence, referredby AS ReferredBy, comments
                                    FROM lists AS l
                                    WHERE l.type =  'surgery'
                                    AND l.pid =".$pid." AND l.id NOT IN ( SELECT DISTINCT list_id FROM issue_encounter WHERE pid =".$pid." ) 
                                    ORDER BY is_Issue_Active ASC , begdate DESC";
    }
    $db->query("SET SQL_BIG_SELECTS=1"); 
    $db->query( "SET NAMES utf8");
    $stmt_layout = $db->prepare($surgerysql2);
    $stmt_layout->execute();

    $datasurgery = $stmt_layout->fetchAll(PDO::FETCH_ASSOC);
    
    $datacheck = '';
    if(count($datasurgery)>0){
        if($layout_type == 'list'){
            foreach($datasurgery as $key => $value){
                foreach ($value as $key => $val) {
                    if(!empty($val)){
                        $display_string .= "<li>";
                        $datacheck = 1;
                        if($key == 'Occurrence'){
                            $occsql =  "select title from list_options  where list_id='occurrence' AND option_id='$val'";
                            $db->query("SET SQL_BIG_SELECTS=1"); 
                            $db->query( "SET NAMES utf8");
                            $stmt_layout = $db->prepare($occsql);
                            $stmt_layout->execute();

                            $frow1 = $stmt_layout->fetchAll(PDO::FETCH_ASSOC);
                             if(!empty($frow1[0]['title']))   
                                 $display_string .= "<b>". ucfirst(str_replace('_',' ',$key.":"))."</b>".$frow1[0]['title'];
                        }else if($key == 'Codes'){
                            // multiple icd codes description
                            $icdcodesarray = explode(";",$val);
                            $icd_description_value = '';
                            for($ic = 0; $ic < count($icdcodesarray) ; $ic++){
                                if(strpos($icdcodesarray[$ic], 'ICD9',0)!== false){
                                    $checkstring = "ICD9:";
                                    $checkstring1 = 'ICD9';
                                    $table = 'icd9_dx_code';
                                }
                                if(strpos($icdcodesarray[$ic], 'ICD10',0)!== false || empty($table)){
                                    $checkstring = "ICD10:";
                                    $table = 'icd10_dx_order_code';
                                    $checkstring1 = 'ICD10';
                                }
                                $exploded_code = str_replace($checkstring, "", $icdcodesarray[$ic]);
                                $icd_description_sql = "SELECT long_desc FROM $table WHERE formatted_dx_code ='$exploded_code' and active= 1";
								$db->query("SET SQL_BIG_SELECTS=1"); 
                                $db->query( "SET NAMES utf8");
                                $icd_description = $db->prepare($icd_description_sql);
                                if(!empty($icd_description)){
                                    $icd_description_value .=  "( ".$icdcodesarray[$ic] ." ".$icd_description[0]['long_desc'].")<br />";
                                }
                            }
                            $display_string .= "<b>". ucfirst(str_replace('_',' ',$key.":"))."</b>".$icd_description_value;
                        }else if($key != 'id' && $key != 'encounter' && $key != 'enc') {
                           $display_string .= "<b>". ucfirst(str_replace('_',' ',$key.":"))."</b>".$val;
                        }
                        $display_string .= "</li>";
                    }
                }
                
                if($datacheck == ''){

                }else{
                    $display_string .= "<li>&nbsp;</li>";
                    $display_string2 = str_replace("<div id='show_div_surgery$encounterid' style='display:none'>","<div id='show_div_surgery$encounterid' style='display:block'>",$display_string);
                    $tempvalue = 0 ;
                } 
            } 
        }else{
            $display_string .= "<table width='980px' style='border:1px solid black;' cellspacing='0'>";
            $display_string .= "<tr>
                    <th style='border-bottom:1px solid black;text-align:center;' width='50%'><b> Description </b></th> 
                    <th style='border-bottom:1px solid black;text-align:left;' width='10%'><b> Status </b></th> 
                    <th style='border-bottom:1px solid black;text-align:left;' width='20%'><b> Start Date </b></th> 
                    <th style='border-bottom:1px solid black;text-align:left;' width='20%'><b> End Date </b></th> 
                 </tr>";
            foreach($datasurgery as $key =>$value){
                $pres_array[$key] = $value;
            }
            for($i=0; $i< count($pres_array); $i++){
                if($pres_array[$i]['Occurrence'] != ''){
                    $getoccu = "SELECT title FROM list_options WHERE option_id = '".$pres_array[$i]['Occurrence']."' AND list_id = 'Occurrence'";
                    $db->query("SET SQL_BIG_SELECTS=1"); 
                    $db->query( "SET NAMES utf8");
                    $stmt_layout = $db->prepare($getoccu);
                    $stmt_layout->execute();

                    $setoccu = $stmt_layout->fetchAll(PDO::FETCH_ASSOC);
                    if(!empty($setoccu)){
                        $occurance = $setoccu[0]['title']; 
                    }else{
                        $occurance = '';
                    }    
                }
                if($pres_array[$i]['outcome'] != ''){
                    $getoutcome = "SELECT title FROM list_options WHERE option_id = '".$pres_array[$i]['outcome']."' AND list_id = 'Outcome'";
                    $db->query("SET SQL_BIG_SELECTS=1"); 
                    $db->query( "SET NAMES utf8");
                    $stmt_layout = $db->prepare($getoutcome);
                    $stmt_layout->execute();

                    $setoutcome = $stmt_layout->fetchAll(PDO::FETCH_ASSOC);
                    if(!empty($setoutcome)){
                        $outcome = $setoutcome[0]['title']; 
                    }else{
                        $outcome = '';
                    }    
                }
                $display_string .="<tr><td style='border-bottom:1px solid black;'><div><table border = '0'>";
                $display_string .= "<tr><td width='50%' class='firsttd'>";
                if(!empty($pres_array[$i]['Title']))
                    $display_string .= $pres_array[$i]['Title']; 
                else
                    $display_string .= "Not Specified. ";
                $display_string .= "</td></tr>";
                // multiple icd codes description
                $icdcodesarray = explode(";",$pres_array[$i]['Codes']);
                $icd_description_value = '';
                for($ic = 0; $ic < count($icdcodesarray) ; $ic++){
                    if(strpos($icdcodesarray[$ic], 'ICD9',0)!== false){
                        $checkstring = "ICD9:";
                        $checkstring1 = 'ICD9';
                        $table = 'icd9_dx_code';
                    }
                    if(strpos($icdcodesarray[$ic], 'ICD10',0)!== false || empty($table)){
                        $checkstring = "ICD10:";
                        $table = 'icd10_dx_order_code';
                        $checkstring1 = 'ICD10';
                    }
                    $exploded_code = str_replace($checkstring, "", $icdcodesarray[$ic]);
                    $icd_description_sql = "SELECT long_desc FROM $table WHERE formatted_dx_code ='$exploded_code' and active= 1";
                    $db->query("SET SQL_BIG_SELECTS=1"); 
                    $db->query( "SET NAMES utf8");
                    $stmt_layout = $db->prepare($icd_description_sql);
                    $stmt_layout->execute();

                    $icd_description = $stmt_layout->fetchAll(PDO::FETCH_ASSOC);
                    if(!empty($icd_description)){
                        $icd_description_value .=  "( ".$icdcodesarray[$ic] ." ".$icd_description[0]['long_desc'].")<br />";
                    }
                }
                if(!empty($pres_array[$i]['Codes']) || !empty($icd_description_value)){
                    $display_string .= "<tr><td class='secondtd'>";
                    $display_string .= $icd_description_value;
                    $display_string .= "</td></tr>";
                }
                if($outcome !='Unassigned' ||$occurance !='Unknown'){
                    $display_string .= "<tr><td class='thirdtd'> ";
                    $display_string .= $occurance;
                    if($outcome != 'Unassigned')
                        $display_string .= ", ". $outcome;
                    $display_string .= "</td></tr>";
                }
                if(!empty($pres_array[$i]['ReferredBy'])){
                    $display_string .= "<tr><td class='thirdtd'>";
                    $display_string .= $pres_array[$i]['Begin_date']. "<span class='byelement'>  by </span>". $pres_array[$i]['ReferredBy'];
                    $display_string .= "</td></tr>";
                }
                if(!empty($pres_array[$i]['destination'])){
                    $display_string .= "<tr><td class='thirdtd'> ";
                    $display_string .= $pres_array[$i]['destination'];
                    $display_string .= "</td></tr>";
                }
                $display_string .= "</table></td>";
                $display_string .= "<td width='10%' style='border-bottom:1px solid black;'>".$pres_array[$i]['is_Issue_Active']."</td><td width='20%' style='border-bottom:1px solid black;'>".$pres_array[$i]['Begin_date']."</td><td width='20%' style='border-bottom:1px solid black;'>".$pres_array[$i]['End_date']."</td>";
                $display_string .= "</tr>";
            }
            $display_string .= "</table></div>";
        }
        $display_string2 = str_replace("<div id='show_div_surgery$encounterid' style='display:none'>","<div id='show_div_surgery$encounterid' style='display:block'>",$display_string);
        $tempvalue = 0 ;
    }
    $returnarray['tempvalue'] = $tempvalue;
    $returnarray['display_string2'] = $display_string2;
    return $returnarray;
}
function display_dental($encounterid,$layout_type,$display_string,$pid){
    $db = getConnection();
    global $tempvalue;
//    $dentalsql = sqlStatement('SET SQL_BIG_SELECTS=1'); 
//    $dentalsql = sqlStatement("SET NAMES utf8");
    $dentalsqlquery = '';
    $display_string2 = '';
    if($encounterid != 0){
        $dentalsqlquery .= "SELECT DISTINCT l.id, l.title AS Title, 
                                    CASE 
                                    WHEN enddate IS NULL 
                                    THEN  'Active'
                                    ELSE  'Inactive'
                                    END AS is_Issue_Active, outcome, destination, begdate AS Begin_date, enddate AS End_date, diagnosis AS Codes, occurrence AS Occurrence, referredby AS ReferredBy, comments
                                    FROM lists AS l
                                    INNER JOIN issue_encounter AS ie ON ie.list_id = l.id
                                    AND ie.encounter = $encounterid
                                    WHERE l.type =  'dental'
                                    AND l.pid = ".$pid."
                                    ORDER BY is_Issue_Active ASC , begdate DESC ";
    }else{
       $dentalsqlquery .= " SELECT DISTINCT l.id, l.title AS Title, 
                                    CASE 
                                    WHEN enddate IS NULL 
                                    THEN  'Active'
                                    ELSE  'Inactive'
                                    END AS is_Issue_Active, outcome, destination, begdate AS Begin_date, enddate AS End_date, diagnosis AS Codes, occurrence AS Occurrence, referredby AS ReferredBy, comments
                                    FROM lists AS l
                                    WHERE l.type =  'dental'
                                    AND l.pid =".$pid." AND l.id NOT IN ( SELECT DISTINCT list_id FROM issue_encounter WHERE pid =".$pid." ) 
                                    ORDER BY is_Issue_Active ASC , begdate DESC";
    }

    $db->query("SET SQL_BIG_SELECTS=1"); 
    $db->query( "SET NAMES utf8");
    $stmt_layout = $db->prepare($dentalsqlquery);
    $stmt_layout->execute();

    $datadental = $stmt_layout->fetchAll(PDO::FETCH_ASSOC);

    $datacheck = '';
    if(count($datadental)>0){
        if($layout_type == 'list'){
            foreach($datadental as $key => $value){
                foreach ($value as $key => $val) {
                    if(!empty($val)){
                        $display_string .= "<li>";
                        $datacheck = 1;
                        if($key == 'Occurrence'){
                            $occsql =  "select title from list_options  where list_id='occurrence' AND option_id='$val'";
//                            $frow1 = sqlFetchArray($occsql);
                            $db->query("SET SQL_BIG_SELECTS=1"); 
                            $db->query( "SET NAMES utf8");
                            $stmt_layout = $db->prepare($occsql);
                            $stmt_layout->execute();

                            $frow1 = $stmt_layout->fetchAll(PDO::FETCH_ASSOC);
                             if(!empty($frow1[0]['title']))   
                                 $display_string .= "<b>". ucfirst(str_replace('_',' ',$key.":"))."</b>".$frow1[0]['title'];
                        }else if($key == 'Codes'){
                            // multiple icd codes description
                            $icdcodesarray = explode(";",$val);
                            $icd_description_value = '';
                            for($ic = 0; $ic < count($icdcodesarray) ; $ic++){
                                if(strpos($icdcodesarray[$ic], 'ICD9',0)!== false){
                                    $checkstring = "ICD9:";
                                    $checkstring1 = 'ICD9';
                                    $table = 'icd9_dx_code';
                                }
                                if(strpos($icdcodesarray[$ic], 'ICD10',0)!== false || empty($table)){
                                    $checkstring = "ICD10:";
                                    $table = 'icd10_dx_order_code';
                                    $checkstring1 = 'ICD10';
                                }
                                $exploded_code = str_replace($checkstring, "", $icdcodesarray[$ic]);
                                $icd_description_sql = "SELECT long_desc FROM $table WHERE formatted_dx_code ='$exploded_code' and active= 1";
                                $db->query("SET SQL_BIG_SELECTS=1"); 
                                $db->query( "SET NAMES utf8");
                                $stmt_layout = $db->prepare($icd_description_sql);
                                $stmt_layout->execute();

                                $icd_description = $stmt_layout->fetchAll(PDO::FETCH_ASSOC);
//                                $icd_description = sqlFetchArray($icd_description_sql);
                                if(!empty($icd_description)){
                                    $icd_description_value .=  "( ".$icdcodesarray[$ic] ." ".$icd_description[0]['long_desc'].")<br />";
                                }
                            }
                            $display_string .= "<b>". ucfirst(str_replace('_',' ',$key.":"))."</b>".$icd_description_value;
                        }else if($key != 'id' && $key != 'encounter' && $key != 'enc'){
                            $display_string .= "<b>". ucfirst(str_replace('_',' ',$key.":"))."</b>".$val;
                        }
                        $display_string .= "</li>";
                    }
                }
                if($datacheck == ''){
                    $tempvalue = 1 ;
                }else{
                    $display_string .= "<li>&nbsp;</li>";
                    $display_string2 = str_replace("<div id='show_div_dental$encounterid' style='display:none'>","<div id='show_div_dental$encounterid' style='display:block'>",$display_string);
                    $tempvalue = 0 ;
                } 
            } 
        }else{
            $display_string .= "<table width='980px' style='border:1px solid black;' cellspacing='0'>";
            $display_string .= "<tr>
                    <th style='border-bottom:1px solid black;text-align:center;' width='50%'><b> Description </b></th> 
                    <th style='border-bottom:1px solid black;text-align:left;' width='10%'><b> Status </b></th> 
                    <th style='border-bottom:1px solid black;text-align:left;' width='20%'><b> Start Date </b></th> 
                    <th style='border-bottom:1px solid black;text-align:left;' width='20%'><b> End Date </b></th> 
                </tr>";
                foreach($datadental as $key =>$value){
                    $pres_array[$key] = $value;
                }
            for($i=0; $i< count($pres_array); $i++){
                if($pres_array[$i]['Occurrence'] != ''){
                    $getoccu = "SELECT title FROM list_options WHERE option_id = '".$pres_array[$i]['Occurrence']."' AND list_id = 'Occurrence'";
//                    $setoccu = sqlFetchArray($getoccu);
                    $db->query("SET SQL_BIG_SELECTS=1"); 
                    $db->query( "SET NAMES utf8");
                    $stmt_layout = $db->prepare($getoccu);
                    $stmt_layout->execute();

                    $setoccu = $stmt_layout->fetchAll(PDO::FETCH_ASSOC);
                    if(!empty($setoccu)){
                        $occurance = $setoccu[0]['title']; 
                    }else{
                        $occurance = '';
                    }    
                }

                if($pres_array[$i]['outcome'] != ''){
                    $getoutcome = "SELECT title FROM list_options WHERE option_id = '".$pres_array[$i]['outcome']."' AND list_id = 'Outcome'";
//                    $setoutcome = sqlFetchArray($getoutcome);
                    $db->query("SET SQL_BIG_SELECTS=1"); 
                    $db->query( "SET NAMES utf8");
                    $stmt_layout = $db->prepare($getoutcome);
                    $stmt_layout->execute();

                    $setoutcome = $stmt_layout->fetchAll(PDO::FETCH_ASSOC);
                    if(!empty($setoutcome)){
                        $outcome = $setoutcome[0]['title']; 
                    }else{
                        $outcome = '';
                    }    
                }
                $display_string .="<tr ><td style='border-bottom:1px solid black;'><table border = '0'>";
                $display_string .= "<tr><td width='50%' class='firsttd'>";
                if(!empty($pres_array[$i]['Title']))
                    $display_string .= $pres_array[$i]['Title']; 
                else
                    $display_string .= "Not Specified. ";
                $display_string .= "</td></tr>";
                // multiple icd codes description
                $icdcodesarray = explode(";",$pres_array[$i]['Codes']);
                $icd_description_value = '';
                for($ic = 0; $ic < count($icdcodesarray) ; $ic++){
                    if(strpos($icdcodesarray[$ic], 'ICD9',0)!== false){
                        $checkstring = "ICD9:";
                        $checkstring1 = 'ICD9';
                        $table = 'icd9_dx_code';
                    }
                    if(strpos($icdcodesarray[$ic], 'ICD10',0)!== false || empty($table)){
                        $checkstring = "ICD10:";
                        $table = 'icd10_dx_order_code';
                        $checkstring1 = 'ICD10';
                    }
                    $exploded_code = str_replace($checkstring, "", $icdcodesarray[$ic]);
                    $icd_description_sql = "SELECT long_desc FROM $table WHERE formatted_dx_code ='$exploded_code' and active= 1";
                    $db->query("SET SQL_BIG_SELECTS=1"); 
                    $db->query( "SET NAMES utf8");
                    $stmt_layout = $db->prepare($icd_description_sql);
                    $stmt_layout->execute();

                    $icd_description = $stmt_layout->fetchAll(PDO::FETCH_ASSOC);
//                    $icd_description = sqlFetchArray($icd_description_sql);
                    if(!empty($icd_description)){
                        $icd_description_value .=  "( ".$icdcodesarray[$ic] ." ".$icd_description[0]['long_desc'].")<br />";
                    }
                }
                if(!empty($pres_array[$i]['Codes']) || !empty($icd_description_value)){
                    $display_string .= "<tr><td class='secondtd'>";
                    $display_string .= $icd_description_value;
                    $display_string .= "</td></tr>";
                }
                
                if($outcome !='Unassigned' ||$occurance !='Unknown'){
                    $display_string .= "<tr><td class='thirdtd'> ";
                    $display_string .= $occurance;
                    if($outcome != 'Unassigned')
                        $display_string .= ", ". $outcome;
                    $display_string .= "</td></tr>";
                }
                if(!empty($pres_array[$i]['ReferredBy'])){
                    $display_string .= "<tr><td class='thirdtd'>";
                    $display_string .= $pres_array[$i]['Begin_date']. "<span class='byelement'>  by </span>". $pres_array[$i]['ReferredBy'];
                    $display_string .= "</td></tr>";
                }
                
                if(!empty($pres_array[$i]['destination'])){
                    $display_string .= "<tr><td class='thirdtd'> ";
                    $display_string .= $pres_array[$i]['destination'];
                    $display_string .= "</td></tr>";
                }
                $display_string .= "</table></td>";
                $display_string .= "<td width='10%' style='border-bottom:1px solid black;'>".$pres_array[$i]['is_Issue_Active']."</td><td width='20%' style='border-bottom:1px solid black;'>".$pres_array[$i]['Begin_date']."</td><td width='20%' style='border-bottom:1px solid black;'>".$pres_array[$i]['End_date']."</td>";
                $display_string .= "</tr>";
            }
            $display_string .= "</table>";
        }
        $display_string2 = str_replace("<div id='show_div_dental$encounterid' style='display:none'>","<div id='show_div_dental$encounterid' style='display:block'>",$display_string);
        $tempvalue = 0 ;
    }
    $returnarray['tempvalue'] = $tempvalue;
    $returnarray['display_string2'] = $display_string2;
    return $returnarray;
}
//face to face
function display_f2f($encounterid,$groupName,$display_string,$pid){
    $db = getConnection();
    $display_string2 = '';
    global $tempvalue;
    $getCC = "SELECT field_id,title FROM layout_options WHERE form_id = 'LBF2' AND group_name LIKE '%Face to Face HH Plan' order by seq";
    $db->query("SET SQL_BIG_SELECTS=1"); 
    $db->query( "SET NAMES utf8");
    $stmt_layout = $db->prepare($getCC);
    $stmt_layout->execute();

    $getCCResult = $stmt_layout->fetchAll(PDO::FETCH_ASSOC);
    
    $getselectedvales = "SELECT field_id as selectedfield FROM tbl_chartui_mapping WHERE screen_name = '$groupName' AND form_id = 'LBF2' AND group_name LIKE '%Face to Face HH Plan' AND option_value = 'YES'";
    $db->query("SET SQL_BIG_SELECTS=1"); 
    $db->query( "SET NAMES utf8");
    $stmt_layout = $db->prepare($getselectedvales);
    $stmt_layout->execute();

    $setselectedvalues = $stmt_layout->fetchAll(PDO::FETCH_ASSOC);
    $listselected = array();
    for($i=0; $i< count($setselectedvalues); $i++){
         $listselected[] = $setselectedvalues[$i]['selectedfield'];
    }
    
    for($j=0; $j< count($getCCResult); $j++){
        for($i=0; $i<count($listselected); $i++){
        if($listselected[$i] == $getCCResult[$j]['field_id']):
            $fieldid[$getCCResult[$j]['title']] = $getCCResult[$j]['field_id'];
        endif;
        }
    }
    if(!empty($fieldid)):
        $getformid = "SELECT form_id FROM forms WHERE encounter = $encounterid AND form_name = 'Allcare Encounter Forms' AND deleted = 0 ";
//            $setformid=sqlFetchArray($getformid);
            $db->query("SET SQL_BIG_SELECTS=1"); 
            $db->query( "SET NAMES utf8");
            $stmt_layout = $db->prepare($getformid);
            $stmt_layout->execute();

            $setformid = $stmt_layout->fetchAll(PDO::FETCH_ASSOC);
            if(!empty($setformid)):
                for($i=0; $i< count($setformid); $i++){
                    $formid = $setformid[0]['form_id'];
                    $datacheck = '';
                    foreach($fieldid as $fkey => $fid){

                        $getCCdata = "SELECT field_value FROM lbf_data WHERE form_id = '$formid' AND field_id = '".$fid."'";// AND (SELECT count(*) FROM lbf_data WHERE form_id = '$formid' AND field_id = 'f2f_stat' AND field_value='finalized')>0");
//                        $getCCdataResult2=sqlFetchArray($getCCdata);
                        $db->query("SET SQL_BIG_SELECTS=1"); 
                        $db->query( "SET NAMES utf8");
                        $stmt_layout = $db->prepare($getCCdata);
                        $stmt_layout->execute();

                        $getCCdataResult2 = $stmt_layout->fetchAll(PDO::FETCH_ASSOC);
                        $display_string .= "<li>";
                        if(!empty($getCCdataResult2)):
                            $display_string .= "<b>".$fkey.": "."</b>".str_replace('|',',',$getCCdataResult2[0]['field_value']);
                            $datacheck = 1;  
                        endif;
                        $display_string .= "</li>";
                        
                    }
                    $tempvalue = 0 ;
                    if($datacheck == ''){

                    }else{
                       $display_string2 = str_replace("<div id='show_div_f2f$encounterid' style='display:none'>","<div id='show_div_f2f$encounterid' style='display:block'>",$display_string);
                    }
                }
            endif;
            
    endif;
    $returnarray['tempvalue'] = $tempvalue;
    $returnarray['display_string2'] = $display_string2;
    return $returnarray;
            
}
function display_dme($encounterid,$layout_type,$display_string,$pid){
    $db = getConnection();
    global $tempvalue;
//    $dmesql = sqlStatement('SET SQL_BIG_SELECTS=1'); 
//    $dmesql = sqlStatement("SET NAMES utf8");
    $dmesqlquery = '';
    $display_string2 = '';
    if($encounterid != 0){
        $dmesqlquery .= "SELECT DISTINCT l.id, l.title AS Title, 
                                    CASE 
                                    WHEN enddate IS NULL 
                                    THEN  'Active'
                                    ELSE  'Inactive'
                                    END AS is_Issue_Active, outcome, destination, begdate AS Begin_date, enddate AS End_date, diagnosis AS Codes, occurrence AS Occurrence, referredby AS ReferredBy, comments
                                    FROM lists AS l
                                    INNER JOIN issue_encounter AS ie ON ie.list_id = l.id
                                    AND ie.encounter = $encounterid
                                    WHERE l.type =  'DME'
                                    AND l.pid = ".$pid."
                                    ORDER BY is_Issue_Active ASC , begdate DESC ";
    }else{
       $dmesqlquery .= " SELECT DISTINCT l.id, l.title AS Title, 
                                    CASE 
                                    WHEN enddate IS NULL 
                                    THEN  'Active'
                                    ELSE  'Inactive'
                                    END AS is_Issue_Active, outcome, destination, begdate AS Begin_date, enddate AS End_date, diagnosis AS Codes, occurrence AS Occurrence, referredby AS ReferredBy, comments
                                    FROM lists AS l
                                    WHERE l.type =  'DME'
                                    AND l.pid =".$pid." AND l.id NOT IN ( SELECT DISTINCT list_id FROM issue_encounter WHERE pid =".$pid." ) 
                                    ORDER BY is_Issue_Active ASC , begdate DESC";
    }

    $db->query("SET SQL_BIG_SELECTS=1"); 
    $db->query( "SET NAMES utf8");
    $stmt_layout = $db->prepare($dmesqlquery);
    $stmt_layout->execute();

    $datadme = $stmt_layout->fetchAll(PDO::FETCH_ASSOC);

    $datacheck = '';
    if(count($datadme)>0){
        if($layout_type == 'list'){
            foreach($datadme as $key => $value){
                foreach ($value as $key => $val) {
                    if(!empty($val)){
                        $display_string .= "<li>";
                        $datacheck = 1;
                        if($key == 'Occurrence'){
                            $occsql =  "select title from list_options  where list_id='occurrence' AND option_id='$val'";
//                            $frow1 = sqlFetchArray($occsql);
                            $db->query("SET SQL_BIG_SELECTS=1"); 
                            $db->query( "SET NAMES utf8");
                            $stmt_layout = $db->prepare($occsql);
                            $stmt_layout->execute();

                            $frow1 = $stmt_layout->fetchAll(PDO::FETCH_ASSOC);
                             if(!empty($frow1[0]['title']))   
                                 $display_string .= "<b>". ucfirst(str_replace('_',' ',$key.":"))."</b>".$frow1[0]['title'];
                        }else if($key == 'Codes'){
                            // multiple icd codes description
                            $icdcodesarray = explode(";",$val);
                            $icd_description_value = '';
                            for($ic = 0; $ic < count($icdcodesarray) ; $ic++){
                                if(strpos($icdcodesarray[$ic], 'ICD9',0)!== false){
                                    $checkstring = "ICD9:";
                                    $checkstring1 = 'ICD9';
                                    $table = 'icd9_dx_code';
                                }
                                if(strpos($icdcodesarray[$ic], 'ICD10',0)!== false || empty($table)){
                                    $checkstring = "ICD10:";
                                    $table = 'icd10_dx_order_code';
                                    $checkstring1 = 'ICD10';
                                }
                                $exploded_code = str_replace($checkstring, "", $icdcodesarray[$ic]);
                                $icd_description_sql = "SELECT long_desc FROM $table WHERE formatted_dx_code ='$exploded_code' and active= 1";
                                $db->query("SET SQL_BIG_SELECTS=1"); 
                                $db->query( "SET NAMES utf8");
                                $stmt_layout = $db->prepare($icd_description_sql);
                                $stmt_layout->execute();

                                $icd_description = $stmt_layout->fetchAll(PDO::FETCH_ASSOC);
//                                $icd_description = sqlFetchArray($icd_description_sql);
                                if(!empty($icd_description)){
                                    $icd_description_value .=  "( ".$icdcodesarray[$ic] ." ".$icd_description[0]['long_desc'].")<br />";
                                }
                            }
                            $display_string .= "<b>". ucfirst(str_replace('_',' ',$key.":"))."</b>".$icd_description_value;
                        }else if($key != 'id' && $key != 'encounter' && $key != 'enc'){
                            $display_string .= "<b>". ucfirst(str_replace('_',' ',$key.":"))."</b>".$val;
                        }
                        $display_string .= "</li>";
                    }
                }
                if($datacheck == ''){
                    $tempvalue = 1 ;
                }else{
                    $display_string .= "<li>&nbsp;</li>";
                    $display_string2 = str_replace("<div id='show_div_dme$encounterid' style='display:none'>","<div id='show_div_dme$encounterid' style='display:block'>",$display_string);
                    $tempvalue = 0 ;
                } 
            } 
        }else{
            $display_string .= "<table width='980px' style='border:1px solid black;' cellspacing='0'>";
            $display_string .= "<tr>
                    <th style='border-bottom:1px solid black;text-align:center;' width='50%'><b> Description </b></th> 
                    <th style='border-bottom:1px solid black;text-align:left;' width='10%'><b> Status </b></th> 
                    <th style='border-bottom:1px solid black;text-align:left;' width='20%'><b> Start Date </b></th> 
                    <th style='border-bottom:1px solid black;text-align:left;' width='20%'><b> End Date </b></th> 
                </tr>";
                foreach($datadme as $key =>$value){
                    $pres_array[$key] = $value;
                }
            for($i=0; $i< count($pres_array); $i++){
                if($pres_array[$i]['Occurrence'] != ''){
                    $getoccu = "SELECT title FROM list_options WHERE option_id = '".$pres_array[$i]['Occurrence']."' AND list_id = 'Occurrence'";
//                    $setoccu = sqlFetchArray($getoccu);
                    $db->query("SET SQL_BIG_SELECTS=1"); 
                    $db->query( "SET NAMES utf8");
                    $stmt_layout = $db->prepare($getoccu);
                    $stmt_layout->execute();

                    $setoccu = $stmt_layout->fetchAll(PDO::FETCH_ASSOC);
                    if(!empty($setoccu)){
                        $occurance = $setoccu[0]['title']; 
                    }else{
                        $occurance = '';
                    }    
                }

                if($pres_array[$i]['outcome'] != ''){
                    $getoutcome = "SELECT title FROM list_options WHERE option_id = '".$pres_array[$i]['outcome']."' AND list_id = 'Outcome'";
//                    $setoutcome = sqlFetchArray($getoutcome);
                    $db->query("SET SQL_BIG_SELECTS=1"); 
                    $db->query( "SET NAMES utf8");
                    $stmt_layout = $db->prepare($getoutcome);
                    $stmt_layout->execute();

                    $setoutcome = $stmt_layout->fetchAll(PDO::FETCH_ASSOC);
                    if(!empty($setoutcome)){
                        $outcome = $setoutcome[0]['title']; 
                    }else{
                        $outcome = '';
                    }    
                }
                $display_string .="<tr ><td style='border-bottom:1px solid black;'><table border = '0'>";
                $display_string .= "<tr><td width='50%' class='firsttd'>";
                if(!empty($pres_array[$i]['Title']))
                    $display_string .= $pres_array[$i]['Title']; 
                else
                    $display_string .= "Not Specified. ";
                $display_string .= "</td></tr>";
                // multiple icd codes description
                $icdcodesarray = explode(";",$pres_array[$i]['Codes']);
                $icd_description_value = '';
                for($ic = 0; $ic < count($icdcodesarray) ; $ic++){
                    if(strpos($icdcodesarray[$ic], 'ICD9',0)!== false){
                        $checkstring = "ICD9:";
                        $checkstring1 = 'ICD9';
                        $table = 'icd9_dx_code';
                    }
                    if(strpos($icdcodesarray[$ic], 'ICD10',0)!== false || empty($table)){
                        $checkstring = "ICD10:";
                        $table = 'icd10_dx_order_code';
                        $checkstring1 = 'ICD10';
                    }
                    $exploded_code = str_replace($checkstring, "", $icdcodesarray[$ic]);
                    $icd_description_sql = "SELECT long_desc FROM $table WHERE formatted_dx_code ='$exploded_code' and active= 1";
                    $db->query("SET SQL_BIG_SELECTS=1"); 
                    $db->query( "SET NAMES utf8");
                    $stmt_layout = $db->prepare($icd_description_sql);
                    $stmt_layout->execute();

                    $icd_description = $stmt_layout->fetchAll(PDO::FETCH_ASSOC);
//                    $icd_description = sqlFetchArray($icd_description_sql);
                    if(!empty($icd_description)){
                        $icd_description_value .=  "( ".$icdcodesarray[$ic] ." ".$icd_description[0]['long_desc'].")<br />";
                    }
                }
                if(!empty($pres_array[$i]['Codes']) || !empty($icd_description_value)){
                    $display_string .= "<tr><td class='secondtd'>";
                    $display_string .= $icd_description_value;
                    $display_string .= "</td></tr>";
                }
                
                if($outcome !='Unassigned' ||$occurance !='Unknown'){
                    $display_string .= "<tr><td class='thirdtd'> ";
                    $display_string .= $occurance;
                    if($outcome != 'Unassigned')
                        $display_string .= ", ". $outcome;
                    $display_string .= "</td></tr>";
                }
                if(!empty($pres_array[$i]['ReferredBy'])){
                    $display_string .= "<tr><td class='thirdtd'>";
                    $display_string .= $pres_array[$i]['Begin_date']. "<span class='byelement'>  by </span>". $pres_array[$i]['ReferredBy'];
                    $display_string .= "</td></tr>";
                }
                
                if(!empty($pres_array[$i]['destination'])){
                    $display_string .= "<tr><td class='thirdtd'> ";
                    $display_string .= $pres_array[$i]['destination'];
                    $display_string .= "</td></tr>";
                }
                $display_string .= "</table></td>";
                $display_string .= "<td width='10%' style='border-bottom:1px solid black;'>".$pres_array[$i]['is_Issue_Active']."</td><td width='20%' style='border-bottom:1px solid black;'>".$pres_array[$i]['Begin_date']."</td><td width='20%' style='border-bottom:1px solid black;'>".$pres_array[$i]['End_date']."</td>";
                $display_string .= "</tr>";
            }
            $display_string .= "</table>";
        }
        $display_string2 = str_replace("<div id='show_div_dme$encounterid' style='display:none'>","<div id='show_div_dme$encounterid' style='display:block'>",$display_string);
        $tempvalue = 0 ;
    }
    $returnarray['tempvalue'] = $tempvalue;
    $returnarray['display_string2'] = $display_string2;
    return $returnarray;
}
function display_cert_recert($encounterid,$groupName,$display_string){
    $db = getConnection();
    $display_string2 = '';
    global $tempvalue;
    $getselectedvales = "SELECT field_id as selectedfield FROM tbl_chartui_mapping WHERE screen_name = '$groupName' AND form_id = 'LBF2' AND group_name LIKE '%Certification_Recertification' AND option_value = 'YES'";
    $stmt_sql_array = $db->prepare($getselectedvales) ; 
    $stmt_sql_array->execute();
    $setselectedvalues = $stmt_sql_array->fetchAll(PDO::FETCH_OBJ);
    $listselected = array();
    foreach($setselectedvalues  as $skey => $svalue){
        $listselected[] = $svalue->selectedfield;
    }
    $getCC = "SELECT field_id,title FROM layout_options WHERE form_id = 'LBF2' AND group_name LIKE '%Certification_Recertification'";
    $stmt_sql_array2 = $db->prepare($getCC) ; 
    $stmt_sql_array2->execute();
    $setCCResult = $stmt_sql_array2->fetchAll(PDO::FETCH_OBJ);
 
    foreach($setCCResult as $getCCResultkey => $getCCResult){
        for($i=0; $i<count($listselected); $i++){
            if($listselected[$i] == $getCCResult->field_id):
                $fieldid[$getCCResult->title] = $getCCResult->field_id;
            endif;
        }
    }
    if(!empty($fieldid)){
        $getformid = "SELECT form_id FROM forms WHERE encounter = $encounterid AND form_name = 'Allcare Encounter Forms' AND deleted = 0 ";
        $stmt_sql_array3 = $db->prepare($getformid) ; 
        $stmt_sql_array3->execute();
        $setformid = $stmt_sql_array3->fetchAll(PDO::FETCH_OBJ);

        if(!empty($setformid)){
            for($i=0; $i< count($setformid); $i++){
                $formid = $setformid[0]->form_id;
                $datacheck = '';
                foreach($fieldid as $fkey => $fid){
                    $getCCdata = "SELECT field_value FROM lbf_data WHERE form_id = '$formid' AND field_id = '".$fid."'";

                    $stmt_sql_array4 = $db->prepare($getCCdata) ; 
                    $stmt_sql_array4->execute();
                    $getCCdataResult2 = $stmt_sql_array4->fetchAll(PDO::FETCH_OBJ);
                    
                    if(!empty($getCCdataResult2[0]->field_value)){
                        $display_string .= "<li>";
                        //if(strcmp($fkey, 'Certification/Recertification Text') != 0):
                            $display_string .= "<b>".$fkey.":</b>";
                        //endif;
                        $datacheck = 1;
                        if(!empty($getCCdataResult2)):
                            if(strcmp($fkey, 'Certification/Recertification Status') == 0):
                                $display_string .=  ucwords(str_replace('|', ',', $getCCdataResult2[0]->field_value))."</li>";
                            else:
                                $display_string .=  nl2br($getCCdataResult2[0]->field_value)."</li>";
                            endif;
                        endif;
                    }
                }
                if($datacheck == ''){
                    $display_string2 = $display_string;
                }else{
                    $tempvalue = 0 ;
                    $display_string2 = str_replace("<div id='show_div_cert_recert$encounterid' style='display:none'>","<div id='show_div_cert_recert$encounterid' style='display:block'>",$display_string);
                }
            }
        } 
    } 
    
    $returnarray['tempvalue'] = 0;
    $returnarray['display_string2'] = $display_string2;
    return $returnarray;
}
function display_cpo($encounterid,$groupName,$display_string,$pid){
    $db = getConnection();
    global $tempvalue;
    $display_string2 = '';
    $getCC = "SELECT field_id,title FROM layout_options WHERE form_id = 'LBF2' AND group_name LIKE '%CPO'";
    $db->query("SET SQL_BIG_SELECTS=1"); 
    $db->query( "SET NAMES utf8");
    $stmt_layout = $db->prepare($getCC);
    $stmt_layout->execute();

    $getCCResult = $stmt_layout->fetchAll(PDO::FETCH_ASSOC);
    
    $getselectedvales = "SELECT field_id as selectedfield FROM tbl_chartui_mapping WHERE screen_name = '$groupName' AND form_id = 'LBF2' AND group_name LIKE '%CPO' AND option_value = 'YES'";
    $db->query("SET SQL_BIG_SELECTS=1"); 
    $db->query( "SET NAMES utf8");
    $stmt_layout = $db->prepare($getselectedvales);
    $stmt_layout->execute();

    $setselectedvalues = $stmt_layout->fetchAll(PDO::FETCH_ASSOC);
    $listselected = array();
//    while($setselectedvalues= sqlFetchArray($getselectedvales)){
    for($i=0; $i< count($setselectedvalues); $i++){
         $listselected[] = $setselectedvalues[$i]['selectedfield'];
    }
//    while($getCCResult = sqlFetchArray($getCC)){
    for($j=0; $j< count($getCCResult); $j++){
        for($i=0; $i<count($listselected); $i++){
        if($listselected[$i] == $getCCResult[$j]['field_id']):
            ${$getCCResult[$j]['field_id']} = $getCCResult[$j]['field_id'];
            ${"title".$getCCResult[$j]['field_id']} = $getCCResult[$j]['title'];
            $fieldid[$getCCResult[$j]['title']] = $getCCResult[$j]['field_id'];
        endif;
        }
    }
//    echo $users."hema";
//    echo "<pre>"; print_r($listselected); echo "</pre>";
//    echo "<pre>"; print_r($fieldid); echo "</pre>";exit();
    $date3="select cp.cpo_data, cp.provider_id  from tbl_form_cpo cp INNER JOIN form_encounter fe on fe.pid=cp.pid INNER JOIN forms f on fe.encounter = f.encounter AND f.form_id=cp.id  where cp.pid=".$pid." AND fe.encounter='$encounterid' AND form_name='cpo' and deleted=0 group by cp.id ";
//    $fdate2=sqlFetchArray($date3);
    $db->query("SET SQL_BIG_SELECTS=1"); 
    $db->query( "SET NAMES utf8");
    $stmt_layout = $db->prepare($date3);
    $stmt_layout->execute();

    $fdate2 = $stmt_layout->fetchAll(PDO::FETCH_ASSOC);
//    echo "<pre>"; print_r($fdate2); echo "</pre>";
    if(!empty($fdate2)){
        foreach($fdate2[0] as $key=> $value){
            if($key == 'cpo_data'){
//                $cpoarray = unserialize($value);
                $cpo_data2 = preg_replace_callback ( '!s:(\d+):"(.*?)";!',
                    function($match) {
                        return ($match[1] == strlen($match[2])) ? $match[0] : 's:' . strlen($match[2]) . ':"' . $match[2] . '";';
                    },
                $value );

                $cpoarray = unserialize($cpo_data2);
                for($i=0; $i< count($cpoarray); $i++){
                    foreach($cpoarray[$i] as $key1 => $value1){
                        if(!empty($value1)){
//                            if(isset(${$key})){
                                if($key1 == 'cpotype' ) {    
                                    $ctype="select title from list_options where list_id='CPO_types' AND option_id='$value1'";
    //                                    $crow = sqlFetchArray($ctype);
                                    $db->query("SET SQL_BIG_SELECTS=1"); 
                                    $db->query( "SET NAMES utf8");
                                    $stmt_layout = $db->prepare($ctype);
                                    $stmt_layout->execute();

                                    $crow = $stmt_layout->fetchAll(PDO::FETCH_ASSOC);
                                    $display_string .=  "<li><b>".${"title".$key1}.":"."</b>".$crow[0]['title']."</li>";

                                }else if( "cpo_".$key1 == ${"cpo_".$key1}){
                                    if($key1 == 'timeinterval') {    
                                        $time="select title from list_options where list_id='Time_Interval' AND option_id='$value1'";
                                        $db->query("SET SQL_BIG_SELECTS=1"); 
                                        $db->query( "SET NAMES utf8");
                                        $stmt_layout = $db->prepare($time);
                                        $stmt_layout->execute();

                                        $time2 = $stmt_layout->fetchAll(PDO::FETCH_ASSOC);
    //                                    $time2 = sqlFetchArray($time);
                                        $display_string .=  "<li><b>".${"titlecpo_".$key1}.":"."</b>".$time2[0]['title']."</li>";
                                    }elseif($key1 == 'users') {    
                                        $users="SELECT CONCAT(fname,' ',lname) AS name FROM users WHERE id='$value1'";
                                        $db->query("SET SQL_BIG_SELECTS=1"); 
                                        $db->query( "SET NAMES utf8");
                                        $stmt_layout = $db->prepare($users);
                                        $stmt_layout->execute();

                                        $urow_data = $stmt_layout->fetchAll(PDO::FETCH_ASSOC);
    //                                    $urow_data = sqlFetchArray($users);
                                       // echo "<pre>"; print_r($crow_data); echo "</pre>";
                                        $display_string .=  "<li><b>".${"titlecpo_".$key1}.":"."</b>".$urow_data[0]['name']."</li>";

                                    }else{
                                        $display_string .=  "<li><b>".ucwords(str_replace('_',' ',${"titlecpo_".$key1})).":"."</b>".$value1."</li>";
                                    }
                                }
//                            }
                        }
                    }
                    $display_string .= "<li> <br></li>";
                }
                
            }else{
                if($key== 'provider_id'):
                    $getporvidername = "SELECT CONCAT(lname,' ',fname) AS name FROM users WHERE id='".$value."'" ;
                    $db->query("SET SQL_BIG_SELECTS=1"); 
                    $db->query( "SET NAMES utf8");
                    $stmt_layout = $db->prepare($getporvidername);
                    $stmt_layout->execute();

                    $rowName = $stmt_layout->fetchAll(PDO::FETCH_ASSOC);
                    if($rowName)
                        $provider1=$rowName[0]['name'];
                    else
                        $provider1 = '';
//                    $rowName=sqlFetchArray($getporvidername);
                    
                    if(!empty($provider1)):
                        $display_string .=  "<li><b>".ucwords(str_replace('_',' ',$key)).":"."</b>".$provider1."</li> ";
                    endif;
                else:
                    if(!empty($value)):
                        $display_string .=  "<li><b>".ucwords(str_replace('_',' ',$key)).":"."</b>".$value."</li> ";
                    endif;
                endif;
            }
        }
        $display_string2 = str_replace("<div id='show_div_cpo$encounterid' style='display:none'>","<div id='show_div_cpo$encounterid' style='display:block'>",$display_string);
        $tempvalue = 0 ;
    }
    $returnarray['tempvalue'] = $tempvalue;
    $returnarray['display_string2'] = $display_string2;
    return $returnarray;
 }
function display_ccm($encounterid,$groupName,$display_string,$pid){
    $display_string2 = '';
    $db = getConnection();
    global $tempvalue;
    $getCC = "SELECT field_id,title FROM layout_options WHERE form_id = 'LBF2' AND group_name LIKE '%CCM'";
    $db->query("SET SQL_BIG_SELECTS=1"); 
    $db->query( "SET NAMES utf8");
    $stmt_layout = $db->prepare($getCC);
    $stmt_layout->execute();

    $getCCResult = $stmt_layout->fetchAll(PDO::FETCH_ASSOC);
    
    $getselectedvales = "SELECT field_id as selectedfield FROM tbl_chartui_mapping WHERE screen_name = '$groupName' AND form_id = 'LBF2' AND group_name LIKE '%CCM' AND option_value = 'YES'";
    $db->query("SET SQL_BIG_SELECTS=1"); 
    $db->query( "SET NAMES utf8");
    $stmt_layout = $db->prepare($getselectedvales);
    $stmt_layout->execute();
    $listselected = array();
    $setselectedvalues = $stmt_layout->fetchAll(PDO::FETCH_ASSOC);

//    while($setselectedvalues= sqlFetchArray($getselectedvales)){
    for($i=0; $i< count($setselectedvalues); $i++){
         $listselected[] = $setselectedvalues[$i]['selectedfield'];
    }
//    while($getCCResult = sqlFetchArray($getCC)){
    for($j=0; $j< count($getCCResult); $j++){
        for($i=0; $i<count($listselected); $i++){
        if($listselected[$i] == $getCCResult[$j]['field_id']):
            ${$getCCResult[$j]['field_id']} = $getCCResult[$j]['field_id'];
            ${"title".$getCCResult[$j]['field_id']} = $getCCResult[$j]['title'];
            $fieldid[$getCCResult[$j]['title']] = $getCCResult[$j]['field_id'];
        endif;
        }
    }

    $date3="select cp.ccm_data, cp.provider_id  from tbl_form_ccm cp INNER JOIN form_encounter fe on fe.pid=cp.pid INNER JOIN forms f on fe.encounter = f.encounter AND f.form_id=cp.id  where cp.pid=".$pid." AND fe.encounter='$encounterid' AND form_name='ccm' and deleted=0 group by cp.id ";
//    $fdate2=sqlFetchArray($date3);
    $db->query("SET SQL_BIG_SELECTS=1"); 
    $db->query( "SET NAMES utf8");
    $stmt_layout = $db->prepare($date3);
    $stmt_layout->execute();

    $fdate2 = $stmt_layout->fetchAll(PDO::FETCH_ASSOC);
//    echo "<pre>"; print_r($fdate2); echo "</pre>";
    if(!empty($fdate2)){
        foreach($fdate2[0] as $key=> $value){
            if($key == 'ccm_data'){
//                $ccmarray = unserialize($value);
                $ccm_data2 = preg_replace_callback ( '!s:(\d+):"(.*?)";!',
                    function($match) {
                        return ($match[1] == strlen($match[2])) ? $match[0] : 's:' . strlen($match[2]) . ':"' . $match[2] . '";';
                    },
                $value );

                $ccmarray = unserialize($ccm_data2);
                for($i=0; $i< count($ccmarray); $i++){
                    foreach($ccmarray[$i] as $key1 => $value1){
                        if(!empty($value1)){
//                            if(isset(${$key})){
                                if($key1 == 'ccmtype' ) {    
                                    $ctype="select title from list_options where list_id='CCM_types' AND option_id='$value1'";
    //                                    $crow = sqlFetchArray($ctype);
                                    $db->query("SET SQL_BIG_SELECTS=1"); 
                                    $db->query( "SET NAMES utf8");
                                    $stmt_layout = $db->prepare($ctype);
                                    $stmt_layout->execute();

                                    $crow = $stmt_layout->fetchAll(PDO::FETCH_ASSOC);
                                    $display_string .=  "<li><b>".${"title".$key1}.":"."</b>".$crow[0]['title']."</li>";

                                }else if( "ccm_".$key1 == ${"ccm_".$key1}){
                                    if($key1 == 'timeinterval') {    
                                        $time="select title from list_options where list_id='Time_Interval' AND option_id='$value1'";
                                        $db->query("SET SQL_BIG_SELECTS=1"); 
                                        $db->query( "SET NAMES utf8");
                                        $stmt_layout = $db->prepare($time);
                                        $stmt_layout->execute();

                                        $time2 = $stmt_layout->fetchAll(PDO::FETCH_ASSOC);
    //                                    $time2 = sqlFetchArray($time);
                                        $display_string .=  "<li><b>".${"titleccm_".$key1}.":"."</b>".$time2[0]['title']."</li>";
                                    }elseif($key1 == 'users') {    
                                        $users="SELECT CONCAT(fname,' ',lname) AS name FROM users WHERE id='$value1'";
                                        $db->query("SET SQL_BIG_SELECTS=1"); 
                                        $db->query( "SET NAMES utf8");
                                        $stmt_layout = $db->prepare($users);
                                        $stmt_layout->execute();

                                        $urow_data = $stmt_layout->fetchAll(PDO::FETCH_ASSOC);
    //                                    $urow_data = sqlFetchArray($users);
                                       // echo "<pre>"; print_r($crow_data); echo "</pre>";
                                        $display_string .=  "<li><b>".${"titleccm_".$key1}.":"."</b>".$urow_data[0]['name']."</li>";

                                    }else{
                                        $display_string .=  "<li><b>".ucwords(str_replace('_',' ',${"titleccm_".$key1})).":"."</b>".$value1."</li>";
                                    }
                                }
//                            }
                        }
                    }
                    $display_string .= "<li> <br></li>";
                }
                
            }else{
                if($key== 'provider_id'):
                    $getporvidername = "SELECT CONCAT(lname,' ',fname) AS name FROM users WHERE id='".$value."'" ;
                    $db->query("SET SQL_BIG_SELECTS=1"); 
                    $db->query( "SET NAMES utf8");
                    $stmt_layout = $db->prepare($getporvidername);
                    $stmt_layout->execute();

                    $rowName = $stmt_layout->fetchAll(PDO::FETCH_ASSOC);
                    if($rowName)
                        $provider1=$rowName[0]['name'];
                    else
                        $provider1 = '';
//                    $rowName=sqlFetchArray($getporvidername);
                    
                    if(!empty($provider1)):
                        $display_string .=  "<li><b>".ucwords(str_replace('_',' ',$key)).":"."</b>".$provider1."</li> ";
                    endif;
                else:
                    if(!empty($value)):
                        $display_string .=  "<li><b>".ucwords(str_replace('_',' ',$key)).":"."</b>".$value."</li> ";
                    endif;
                endif;
            }
        }
        $display_string2 = str_replace("<div id='show_div_ccm$encounterid' style='display:none'>","<div id='show_div_ccm$encounterid' style='display:block'>",$display_string);
        $tempvalue = 0 ;
    }
    $returnarray['tempvalue'] = $tempvalue;
    $returnarray['display_string2'] = $display_string2;
    return $returnarray;
 }

function display_div_function($gname,$encounter,$pid){
    $db = getConnection();
    $display_string = '';
    if($gname == 'Patient Information')
        $display_string .= "<div style='clear:both'>";
    else
        $display_string .= "<div class='page' style='clear:both'>";
    $getPatientName = "SELECT CONCAT(fname,' ',lname) AS pname ,pid,DATE_FORMAT(DOB,'%m-%d-%Y') as DOB ,ss, providerID, street,city,state,country_code,postal_code FROM patient_data WHERE pid=".$pid."";
//    $resPatientName = sqlFetchArray($getPatientName);
    $stmt_sql_array = $db->prepare($getPatientName) ; 
    $stmt_sql_array->execute();
    $resPatientName = $stmt_sql_array->fetchAll(PDO::FETCH_OBJ);
    $name           = $resPatientName[0]->pname;
    $dob            = $resPatientName[0]->DOB;
    $ssn            = $resPatientName[0]->ss;
    $pid            = $resPatientName[0]->pid;
    $provider       = $resPatientName[0]->providerID;   
    $getporvidername= "SELECT f.name as faclityname, f.street, f.city, f.state, f.postal_code, f.country_code, f.email,f.website,f.fax, f.phone
                                        FROM facility f
                                        WHERE primary_business_entity=1" ;
//    $rowName        = sqlFetchArray($getporvidername);
    $stmt_sql_array = $db->prepare($getporvidername) ; 
    $stmt_sql_array->execute();
    $rowName = $stmt_sql_array->fetchAll(PDO::FETCH_OBJ);
    if(!empty($rowName))
        $count_rows = count($rowName);
    else
        $count_rows = 0;
//    echo "<pre>"; print_r($getporvidername); echo "</pre>";exit();
    if(!empty($rowName)){
        //$provider_name  = $rowName['name'];
        $facilityname   = $rowName[0]->faclityname;
        if(!empty($rowName[0]->website))
            $website = $rowName[0]->website.", ";
        if(!empty($rowName[0]->phone))
            $phone = "Phone:".$rowName[0]->phone.", ";
        if(!empty($rowName[0]->fax))
            $fax = "Fax:".$rowName[0]->fax.", ";
        if(!empty($rowName[0]->fax))
           $email =  "Email:".$rowName[0]->email;
        $location       = $rowName[0]->street.", ".$rowName[0]->city.", ".$rowName[0]->state.", ".$rowName[0]->country_code.", ".$rowName[0]->postal_code;
        $contact        = str_replace(',,', ' ', $website.$phone.$fax.$email);
    }

//    $display_string .= "<br><div style='width:980px;height:110px;border:1px solid #000;clear:both;'><table width='100%' height='100%' cellpadding='0' style='border:1px solid #000;clear:both;'><tr><td align='left' width='70%' ><b>$name: $gname</b><br>Patient Record Number:$pid</td><td width='70%'><span><b>$facilityname</b><br><font size='2'>$location<br>$contact</font></b></span></td></tr></table></div><br>";
    $display_string .= "<br><div style='width:980px;height:auto; font-size:30px;border:1px solid #000;clear:both;' ><table width='100%' height:'100%' style='font-size: 14px;'><tr><td align='left' width='70%'><b>$name: $gname</b><br>Patient Record Number:$pid</td><td width='70%'><span><b>$facilityname</b><br><font size='2'>$location<br>$contact</font></b></span></td></tr></table></div><br>";
    if($encounter != 0){
        $get_rendering_provider = "SELECT CONCAT(u.fname,' ',u.lname) AS provider_name ,u.id, DATE_FORMAT(f.date,'%d-%M-%Y') as date
                                        FROM users u
                                        INNER JOIN form_encounter f ON f.provider_id = u.id
                                        WHERE f.encounter = $encounter ";
//        $set_rendering_provider = sqlFetchArray($get_rendering_provider);
        $stmt_sql_array = $db->prepare($get_rendering_provider) ; 
        $stmt_sql_array->execute();
        $set_rendering_provider = $stmt_sql_array->fetchAll(PDO::FETCH_OBJ);
        if(!empty($set_rendering_provider)){
        $getprovider_credentials = "SELECT provider_credentials FROM tbl_patientuser WHERE userid = ".$set_rendering_provider[0]->id;
//        $setprovider_credentials = sqlFetchArray($getprovider_credentials);
        $stmt_sql_array = $db->prepare($getprovider_credentials) ; 
        $stmt_sql_array->execute();
        $setprovider_credentials = $stmt_sql_array->fetchAll(PDO::FETCH_OBJ);
        if(!empty($setprovider_credentials))
            $provider_cred = $setprovider_credentials[0]->provider_credentials;
        else
             $provider_cred = '';
        }else{
            $provider_cred = '';
        }
        $display_string .= "<div align='right'>";
        $display_string .= "<b>Seen by </b>".$set_rendering_provider[0]->provider_name."&nbsp; <font size='2' >". $provider_cred."</font><br>";
        $display_string .= "<b>Seen on </b>".$set_rendering_provider[0]->date;
        $display_string .= "</div>";
    }
    $display_string .= "</div>";
    $display_string2 = $display_string;
    return $display_string2; 
}

function display_prescription($layout_type,$pid,$display_string){
    $db = getConnection();
    global $tempvalue;
    $display_string2 = '';
    $pressql = "SELECT  * FROM  `prescriptions` WHERE  `patient_id` =".$pid." order by active desc ,date_added desc ";
//    while ($frow = sqlFetchArray($pressql)) {
//        $datapre[] = $frow; 
//    }
    $db->query("SET SQL_BIG_SELECTS=1"); 
    $db->query( "SET NAMES utf8");
    $stmt_layout = $db->prepare($pressql);
    $stmt_layout->execute();

    $frow = $stmt_layout->fetchAll(PDO::FETCH_ASSOC);
    for($i=0; $i< count($frow); $i++){
        $datapre[] = $frow[$i]; 
    }
    $datacheck = '';
    if(!empty($datapre)){
        if($layout_type == 'list'){
            foreach($datapre as $value){
                foreach ($value as $key => $val) {
                    if(!empty($val)){
                        $datacheck = 1;
                        $display_string .= "<li>";
                        if($key=='provider_id'){
                            $getporvidername = "SELECT CONCAT(fname,' ',lname) AS name FROM users WHERE id='$val'" ;
                            $db->query("SET SQL_BIG_SELECTS=1"); 
                            $db->query( "SET NAMES utf8");
                            $stmt_layout = $db->prepare($getporvidername);
                            $stmt_layout->execute();

                            $rowName = $stmt_layout->fetchAll(PDO::FETCH_ASSOC);
//                            $rowName=sqlFetchArray($getporvidername);
                            $provider_name=$rowName[0]['name'];
                            $display_string .=  "<b> Provider:"."</b>". $provider_name;
                        }else if($key=='Unit'){
                            $getunits = "SELECT title FROM list_options WHERE option_id='$val' AND list_id='drug_units'" ;
//                            $rowName=sqlFetchArray($getunits);
                            $db->query("SET SQL_BIG_SELECTS=1"); 
                            $db->query( "SET NAMES utf8");
                            $stmt_layout = $db->prepare($getunits);
                            $stmt_layout->execute();

                            $rowName = $stmt_layout->fetchAll(PDO::FETCH_ASSOC);
                            $units=$rowName[0]['title'];   
                            $display_string .=  "<b>".  ucfirst(str_replace('_',' ',$key)).":"."</b>".$units;
                        }elseif($key == 'active' || $key == 'substitute' || $key == 'medication'){
                            if($val== 1)
                                $active = "Yes";
                            else
                                 $active = "No";
                            $display_string .=  "<b>".  ucfirst(str_replace('_',' ',$key)).":"."</b>". $active;
                        }else if($key != 'id' && $key != 'encounter' && $key != 'enc'){
                            $display_string .=  "<b>".  ucfirst(str_replace('_',' ',$key)).":"."</b>". $val;
                        }
                        $display_string .= "</li>"; 
                    }
                }
                if($datacheck != ''){
                    $display_string .= "<li>&nbsp;</li>"; 
                    $display_string2 = str_replace("<div id='show_div_prescript0' style='display:none'>","<div id='show_div_prescript0' style='display:block'>",$display_string);
                    $tempvalue = 0 ;
                } 
            } 
        }else{
            $display_string .= "<style>
                .tbl_pre {border:1px solid #C3E7F5;background-color: #E0E0E0; }
                .tbl_pre_th
                {border:0px 1px 1px 0px solid  #C3E7F5;;text-align:center; background-color:  #E0E0E0;}
                .tbl_pre_td 
                {border:0px 1px 1px 0px solid  ;text-align:center; background:#FFFFFF;}
               </style>";
            $display_string .= "<table width='980px' style='border:1px solid black;' cellspacing='0'>";
            $display_string .= "<tr> 
                    <th width='50%' style='border-bottom:1px solid black;text-align:center;'><b> Description </b></td> 
                    <th width='10%' style='border-bottom:1px solid black;text-align:left;'><b> Status </b></td> 
                    <th width='20%' style='border-bottom:1px solid black;text-align:left;'><b> Start Date </b></td> 
                    <th width='20%' style='border-bottom:1px solid black;text-align:left;'><b> End Date </b></td> 
                  </tr>";
            foreach($datapre as $key =>$value){
                $pres_array[$key] = $value;
            }  
            for($i=0; $i< count($pres_array); $i++){
                if(!empty($pres_array[$i]['unit'])){
                    $getdrugunit = "SELECT title FROM list_options WHERE option_id = '".$pres_array[$i]['unit']."' AND list_id = 'drug_units'";
//                    $setdrugunit = sqlFetchArray($getdrugunit);
                    $db->query("SET SQL_BIG_SELECTS=1"); 
                    $db->query( "SET NAMES utf8");
                    $stmt_layout = $db->prepare($getdrugunit);
                    $stmt_layout->execute();

                    $setdrugunit = $stmt_layout->fetchAll(PDO::FETCH_ASSOC);
                    $drugunit = $setdrugunit[0]['title'];
                }else{
                    $drugunit = '';
                } 
                if(!empty($pres_array[$i]['form'])){
                    $getdrugform = "SELECT title FROM list_options WHERE option_id = '".$pres_array[$i]['form']."' AND list_id = 'drug_form'";
                    $db->query("SET SQL_BIG_SELECTS=1"); 
                    $db->query( "SET NAMES utf8");
                    $stmt_layout = $db->prepare($getdrugform);
                    $stmt_layout->execute();

                    $setdrugform = $stmt_layout->fetchAll(PDO::FETCH_ASSOC);
//                    $setdrugform = sqlFetchArray($getdrugform);
                    $drugform = $setdrugform[0]['title']; 
                }else{
                    $drugform = '';
                } 
                if(!empty($pres_array[$i]['interval'])){
                    $getdruginterval = "SELECT title FROM list_options WHERE option_id = '".$pres_array[$i]['interval']."' AND list_id = 'drug_interval'";
//                    $setdruginterval = sqlFetchArray($getdruginterval);
                    $db->query("SET SQL_BIG_SELECTS=1"); 
                    $db->query( "SET NAMES utf8");
                    $stmt_layout = $db->prepare($getdruginterval);
                    $stmt_layout->execute();

                    $setdruginterval = $stmt_layout->fetchAll(PDO::FETCH_ASSOC);
                    $druginterval = $setdruginterval[0]['title']; 
                }else{
                    $druginterval = '';
                } 
                $provider_name = '';
                if(!empty($pres_array[$i]['provider_id'])){
                    $getporvidername = "SELECT CONCAT(fname,' ',lname) AS name FROM users WHERE id='".$pres_array[$i]['provider_id']."'" ;
//                    $setporvidername = sqlFetchArray($getporvidername);
                    $db->query("SET SQL_BIG_SELECTS=1"); 
                    $db->query( "SET NAMES utf8");
                    $stmt_layout = $db->prepare($getporvidername);
                    $stmt_layout->execute();

                    $setporvidername = $stmt_layout->fetchAll(PDO::FETCH_ASSOC);
                    $provider_name = $setporvidername[0]['name'];
                }
                if(!empty($pres_array[$i]['refills']) || !empty($pres_array[$i]['per_refill'])){
                   $refill = (!empty($pres_array[$i]['refills'])? $pres_array[$i]['refills'] : 0 )." # of tablets ". $pres_array[$i]['per_refill'];
                }else{
                    $refill = "No refills";
                }

                $display_string .= "<tr><td style='border-bottom:1px solid black;'><table border = '0'>";
                $display_string .= "<tr><td class='firsttd' width='50%'>";
                $display_string .= $pres_array[$i]['drug']. " ".$pres_array[$i]['quantity']." ".$drugunit. " ".$drugform;
                $display_string .= "</td></tr><tr><td class='secondtd'>";
                $display_string .= $pres_array[$i]['dosage']. " ". $druginterval;
                $display_string .= "</td></tr>";
                $display_string .= "<tr><td class='thirdtd'>";
                $display_string .= $pres_array[$i]['date_added']. "<span class='byelement'> by </span> ". $provider_name . " ".$pres_array[$i]['quantity']." ".$drugunit. " ".$drugform ."(".$refill.")";
                $display_string .= "</td></tr>";
                $display_string .= "</table></td>";
                if($pres_array[$i]['active'] == 1)
                    $active = "Active";
                else
                    $active = "Inactive";
                $display_string .= "<td width='10%' style='border-bottom:1px solid black;'>".$active."</td><td width='20%' style='border-bottom:1px solid black;'>".$pres_array[$i]['start_date']."</td><td width='20%' style='border-bottom:1px solid black;'>".$pres_array[$i]['date_modified']."</td>";
                $display_string .= "</tr>";
            }
//            $display_string .= "</td>";
            $display_string .= "</table>";
        }
        $display_string2 = str_replace("<div id='show_div_prescript0' style='display:none'>","<div id='show_div_prescript0' style='display:block'>",$display_string);
        $tempvalue = 0 ;
    }
    $returnarray['tempvalue'] = $tempvalue;
    $returnarray['display_string2'] = $display_string2;
    return $returnarray;
}      
function display_procedure($encounter, $layout_type,$display_string, $pid,$tempvalue){ 
    $tempvalue = $tempvalue;
    $display_string2 = '';
    $db = getConnection();
    $getProc = "SELECT procedure_order_id FROM procedure_order where encounter_id=$encounter
                and patient_id='".$pid."'";
    $orderid = '';
//    $Procdata=sqlStatement($getProc);
    $db->query("SET SQL_BIG_SELECTS=1"); 
    $db->query( "SET NAMES utf8");
    $stmt_sql_array = $db->prepare($getProc) ; 
    $stmt_sql_array->execute();
    $Procdata = $stmt_sql_array->fetchAll(PDO::FETCH_ASSOC);
//    while ($frow = sqlFetchArray($Procdata)) {
    for($i=0; $i<count($Procdata); $i++){
        $orderid = $Procdata[$i]['procedure_order_id']; 
    
        if($orderid !== ''){
            if($layout_type =='list'){
                $get_procedure = "SELECT " .
                    "po.procedure_order_id as Order_Id, po.date_ordered as Order_Date, " .
                    "po.order_status as Order_Status, po.specimen_type as Specimen_type, " .
                    "pd.pubpid as Patient_Id, CONCAT(pd.lname,' ', pd.fname,' ', pd.mname) as Patient_Name ," .
                    "fe.date as Date, " .
                    "pp.name as Lab_Name, " .
                    "CONCAT(u.lname , ' ',u.fname ,' ',u.mname )AS Name " .
                    "FROM procedure_order AS po " .
                    "LEFT JOIN patient_data AS pd ON pd.pid = po.patient_id " .
                    "LEFT JOIN procedure_providers AS pp ON pp.ppid = po.lab_id " .
                    "LEFT JOIN users AS u ON u.id = po.provider_id " .
                    "LEFT JOIN form_encounter AS fe ON fe.pid = po.patient_id AND fe.encounter = po.encounter_id " .
                    "WHERE po.procedure_order_id = $orderid";
                $db->query("SET SQL_BIG_SELECTS=1"); 
                $db->query( "SET NAMES utf8");
                $stmt_sql_array = $db->prepare($get_procedure) ; 
                $stmt_sql_array->execute();
                $procedure = $stmt_sql_array->fetchAll(PDO::FETCH_ASSOC);
//                $procedure = sqlFetchArray($get_procedure);
                for($j=0; $j<count($procedure); $j++){
                    foreach($procedure[$j] as $pkey =>$pvalue){
                        $display_string.= "<li>";
                        $display_string.= ucfirst(str_replace('_',' ',$pkey.":")) . $pvalue;
                        $display_string.= "</li>";
                    }
                }
                $query = "SELECT " .
                      "po.date_ordered as Date , CONCAT(pc.procedure_code ,':',pc.procedure_name ) as Procedure_Name, " .
                      "pr.procedure_report_id, pr.date_report as Reported, pr.date_collected, pr.specimen_num , " .
                      "pr.report_status as Specimen, pr.review_status as Status, pr.report_notes as Note " .
                      "FROM procedure_order AS po " .
                      "JOIN procedure_order_code AS pc ON pc.procedure_order_id = po.procedure_order_id " .
                      "LEFT JOIN procedure_report AS pr ON pr.procedure_order_id = po.procedure_order_id AND " .
                      "pr.procedure_order_seq = pc.procedure_order_seq " .
                      "WHERE po.procedure_order_id = $orderid " .
                      "ORDER BY pc.procedure_order_seq, pr.procedure_report_id";
                $db->query("SET SQL_BIG_SELECTS=1"); 
                $db->query( "SET NAMES utf8");
                $stmt_sql_array = $db->prepare($query) ; 
                $stmt_sql_array->execute();
                $procedure_result = $stmt_sql_array->fetchAll(PDO::FETCH_ASSOC);
//                $procedure_result = sqlFetchArray($query);
                if(!empty($procedure_result)){
                    for($k=0; $k<count($procedure_result); $k++){
                        foreach($procedure_result[$k] as $pkey1 =>$pvalue1){
                            if(!empty($pvalue1)){
                                $display_string.= "<li>";
                                $display_string.= ucfirst(str_replace('_',' ',$pkey1.":")) . $pvalue1;
                                $display_string.= "</li>";
                            }
                        }
                    }
                }
                $query1 = "SELECT " .
                    "ps.result_code as Code, ps.result_text as Name, ps.abnormal as Abn, ps.result as Value, " .
                    "ps.range as _Range, ps.result_status as Status,  ps.facility as Facility, ps.units as Units, ps.comments as Note " .
                    "FROM procedure_result AS ps " .
                    "WHERE ps.procedure_report_id = $orderid " .
                    "ORDER BY ps.result_code, ps.procedure_result_id";
                $db->query("SET SQL_BIG_SELECTS=1"); 
                $db->query( "SET NAMES utf8");
                $stmt_sql_array = $db->prepare($query1) ; 
                $stmt_sql_array->execute();
                $procedure_result1 = $stmt_sql_array->fetchAll(PDO::FETCH_ASSOC);
//                $procedure_result1 = sqlFetchArray($query1);
                if(!empty($procedure_result1)){
                    for($l=0; $l<count($procedure_result1); $l++){
                        foreach($procedure_result1[$l] as $pkey2 =>$pvalue2){
                            if(!empty($pvalue2)){
                                $display_string.= "<li>";
                                $display_string.= ucfirst(str_replace('_',' ',$pkey2.":")) . $pvalue2;
                                $display_string.= "</li>";
                            }
                        }
                    }
                }
                $display_string2 = str_replace("<div id='show_div_procedure$encounter' style='display:none'>","<div id='show_div_procedure$encounter' style='display:block'>",$display_string);
                ?><script>
                    //document.getElementById('show_div_procedure<?php //echo $encounter; ?>').style.display = "block";
                </script>

                <?php
                $tempvalue = 0 ;
            }else{    
                $input_form=false;
                global $aNotes;

                // Check authorization.
//                $thisauth = acl_check('patients', 'med');
//                if (!$thisauth) return 'Not authorized';

                $orow2 = "SELECT " .
                  "po.procedure_order_id, po.date_ordered, " .
                  "po.order_status, po.specimen_type, " .
                  "pd.pubpid, pd.lname, pd.fname, pd.mname, " .
                  "fe.date, " .
                  "pp.name AS labname, " .
                  "u.lname AS ulname, u.fname AS ufname, u.mname AS umname " .
                  "FROM procedure_order AS po " .
                  "LEFT JOIN patient_data AS pd ON pd.pid = po.patient_id " .
                  "LEFT JOIN procedure_providers AS pp ON pp.ppid = po.lab_id " .
                  "LEFT JOIN users AS u ON u.id = po.provider_id " .
                  "LEFT JOIN form_encounter AS fe ON fe.pid = po.patient_id AND fe.encounter = po.encounter_id " .
                  "WHERE po.procedure_order_id = $orderid";
                  $db->query("SET SQL_BIG_SELECTS=1"); 
                $db->query( "SET NAMES utf8");
                $stmt_sql_array = $db->prepare($orow2) ; 
                $stmt_sql_array->execute();
                $orow = $stmt_sql_array->fetchAll(PDO::FETCH_ASSOC);
                  $display_string.="<style>

                  .labres tr.head   { font-size:10pt; text-align:center; }
                  .labres tr.detail { font-size:10pt; }
                  .labres a, .labres a:visited, .labres a:hover { color:#0000cc; }

                  .labres table {
                   border-style: solid;
                   border-width: 1px 0px 0px 1px;
                   border-color: black;
                  }

                  .labres td, .labres th {
                   border-style: solid;
                   border-width: 0px 1px 1px 0px;
                   border-color: black;
                  }

                  </style>";

                 if ($input_form) {
//                  <script type="text/javascript" src="../../library/dialog.js"></script>
//                  <script type="text/javascript" src="../../library/textformat.js"></script>
                  } // end if input form ?>

                  <?php if (empty($GLOBALS['PATIENT_REPORT_ACTIVE'])) { ?>
                  <script language="JavaScript">
                  var mypcc = '<?php //echo $GLOBALS['phone_country_code'] ?>';
                  // Called to show patient notes related to this order in the "other" frame.
                  function showpnotes(orderid) {
                   // Look for the top or bottom frame that contains this document, return if none.
                   var w;
                   for (w = window; w.name != 'RTop' && w.name != 'RBot'; w = w.parent) {
                    if (w.parent == w) return false;
                   }
                   var othername = (w.name == 'RTop') ? 'RBot' : 'RTop';
                   w.parent.left_nav.forceDual();
                   w.parent.left_nav.setRadio(othername, 'pno');
                   w.parent.left_nav.loadFrame('pno1', othername, 'patient_file/summary/pnotes_full.php?orderid=' + orderid);
                   return false;
                  }
                  </script>
                  <?php } // end if not patient report ?>
-->
                  <?php if ($input_form) {
                  $display_string.= "<form method='post' action='single_order_results.php?orderid=$orderid'>";
                   } // end if input form 

                  $display_string.= "<div class='labres'>

                  <table width='980px' cellpadding='2' cellspacing='0'>
                   <tr>
                    <td width='10%' nowrap>Patient ID</td>
                    <td width='40%'>".myCellText($orow[0]['pubpid'])."</td>
                    <td width='10%' nowrap>Order ID</td>

                    <td width='40%'>
                  ".myCellText($orow[0]['procedure_order_id']);
                 $display_string.= 
                    "</td>
                   </tr>
                   <tr>
                    <td nowrap>Patient Name</td>
                    <td>". myCellText($orow[0]['lname'] . ', ' . $orow[0]['fname'] . ' ' . $orow[0]['mname'])."</td>
                    <td nowrap>Ordered By</td>
                    <td>". myCellText($orow[0]['ulname'] . ', ' . $orow[0]['ufname'] . ' ' . $orow[0]['umname'])."</td>
                   </tr>
                  <tr>
                    <td nowrap>Order Date</td>
                    <td>". myCellText(oeFormatShortDate($orow[0]['date_ordered']))."</td>
                    <td nowrap>Print Date</td>
                    <td>".oeFormatShortDate(date('Y-m-d'))."</td>
                   </tr>
                   <tr>
                    <td nowrap>Order Status</td>
                    <td>".myCellText($orow[0]['order_status'])."</td>
                    <td nowrap>Encounter Date</td>
                    <td>".myCellText(oeFormatShortDate(substr($orow[0]['date'], 0, 10)))."</td>
                   </tr>
                   <tr>
                    <td nowrap>Lab</td>
                    <td>". myCellText($orow[0]['labname'])."</td>
                    <td nowrap>Specimen Type></td>
                    <td>".myCellText($orow[0]['specimen_type'])."</td>
                   </tr>
                  </table>

                  &nbsp;<br />

                  <table width='980px' cellpadding='2' cellspacing='0' >

                   <tr class='head'>
                    <td rowspan='2' valign='middle'>Ordered Procedure</td>
                    <td colspan='4'>Report</td>
                    <td colspan='7'>Results</td>
                   </tr>

                   <tr class='head'>
                    <td>Reported</td>
                    <td>Specimen</td>
                    <td>Status</td>
                    <td>Note</td>
                    <td>Code</td>
                    <td>Name</td>
                    <td>Abn</td>
                    <td>Value</td>
                    <td>Range</td>
                    <td>Units</td>
                    <td>Note</td>
                   </tr>";

                  
                     $query = "SELECT " .
                      "po.date_ordered, pc.procedure_order_seq, pc.procedure_code, " .
                      "pc.procedure_name, " .
                      "pr.procedure_report_id, pr.date_report, pr.date_collected, pr.specimen_num, " .
                      "pr.report_status, pr.review_status, pr.report_notes " .
                      "FROM procedure_order AS po " .
                      "JOIN procedure_order_code AS pc ON pc.procedure_order_id = po.procedure_order_id " .
                      "LEFT JOIN procedure_report AS pr ON pr.procedure_order_id = po.procedure_order_id AND " .
                      "pr.procedure_order_seq = pc.procedure_order_seq " .
                      "WHERE po.procedure_order_id = $orderid " .
                      "ORDER BY pc.procedure_order_seq, pr.procedure_report_id";
                    $db->query("SET SQL_BIG_SELECTS=1"); 
                    $db->query( "SET NAMES utf8");
                    $stmt_sql_array = $db->prepare($query) ; 
                    $stmt_sql_array->execute();
                    $row = $stmt_sql_array->fetchAll(PDO::FETCH_ASSOC);
                    
//                    $res = sqlStatement($query, array($orderid));
//echo "<pre>"; print_r($row); echo "</pre>";
                    $lastpoid = -1;
                    $lastpcid = -1;
                    $lastprid = -1;
                    $encount = 0;
                    $lino = 0;
                    $extra_html = '';
                    $aNotes = array();
                    $sign_list = '';
                    $order_id = $orderid;
//                    while ($row = sqlFetchArray($res)) {
                    for($m=0; $m< count($row); $m++){
                      
                      $order_type_id  = empty($row[0]['order_type_id'      ]) ? 0 : ($row[0]['order_type_id' ] + 0);
                      $order_seq      = empty($row[0]['procedure_order_seq']) ? 0 : ($row[0]['procedure_order_seq'] + 0);
                      $report_id      = empty($row[0]['procedure_report_id']) ? 0 : ($row[0]['procedure_report_id'] + 0);
                      $procedure_code = empty($row[0]['procedure_code'  ]) ? '' : $row[0]['procedure_code'];
                      $procedure_name = empty($row[0]['procedure_name'  ]) ? '' : $row[0]['procedure_name'];
                      $date_report    = empty($row[0]['date_report'     ]) ? '' : $row[0]['date_report'];
                      $date_collected = empty($row[0]['date_collected'  ]) ? '' : substr($row[0]['date_collected'], 0, 16);
                      $specimen_num   = empty($row[0]['specimen_num'    ]) ? '' : $row[0]['specimen_num'];
                      $report_status  = empty($row[0]['report_status'   ]) ? '' : $row[0]['report_status']; 
                      $review_status  = empty($row[0]['review_status'   ]) ? 'received' : $row[0]['review_status'];

                      if ($review_status != 'reviewed' && $report_id) {
                        if ($sign_list) $sign_list .= ',';
                        $sign_list .= $report_id;
                      }

                      $report_noteid ='';
                      if (!empty($row[0]['report_notes'])) {
                        $report_noteid = 1 + storeNote($row[0]['report_notes']);
                      }

                      $query = "SELECT " .
                        "ps.result_code, ps.result_text, ps.abnormal, ps.result, " .
                        "ps.range, ps.result_status, ps.facility, ps.units, ps.comments " .
                        "FROM procedure_result AS ps " .
                        "WHERE ps.procedure_report_id = $report_id " .
                        "ORDER BY ps.result_code, ps.procedure_result_id";
                      $db->query("SET SQL_BIG_SELECTS=1"); 
                        $db->query( "SET NAMES utf8");
                        $stmt_sql_array = $db->prepare($query) ; 
                        $stmt_sql_array->execute();
                        $rres = $stmt_sql_array->fetchAll(PDO::FETCH_ASSOC);
//                      $rres = sqlStatement($query, array($report_id));
                      $rrows = array();
//                      while ($rrow = sqlFetchArray($rres)) {
                      for($n=0; $n< count($rres); $n++){
                        $rrows[] = $rres[$n];
                      }
//                      echo "<pre>"; print_r($rres); echo "</pre>";
                      if (empty($rrows)) {
                        $rrows[0] = array('result_code' => '');
                      }
//                      echo "<pre>"; print_r($rrows); echo "</pre>";
                      foreach ($rrows as $rrow) {
                        $result_code      = empty($rrow['result_code'     ]) ? '' : $rrow['result_code'];
                        $result_text      = empty($rrow['result_text'     ]) ? '' : $rrow['result_text'];
                        $result_abnormal  = empty($rrow['abnormal'        ]) ? '' : $rrow['abnormal'];
                        $result_result    = empty($rrow['result'          ]) ? '' : $rrow['result'];
                        $result_units     = empty($rrow['units'           ]) ? '' : $rrow['units'];
                        $result_facility  = empty($rrow['facility'        ]) ? '' : $rrow['facility'];
                        $result_comments  = empty($rrow['comments'        ]) ? '' : $rrow['comments'];
                        $result_range     = empty($rrow['range'           ]) ? '' : $rrow['range'];
                        $result_status    = empty($rrow['result_status'   ]) ? '' : $rrow['result_status'];

                        $result_comments = trim($result_comments);
                        $result_noteid = '';
                        if (!empty($result_comments)) {
                          $result_noteid = 1 + storeNote($result_comments);
                        }
//                        echo $lastpoid;
                        if ($lastpoid != $order_id || $lastpcid != $order_seq) {
                          ++$encount;
                        }
                        //$bgcolor = "#" . (($encount & 1) ? "ddddff" : "ffdddd");

                        $display_string.= " <tr class='detail' bgcolor='FFFFFF'>\n";

                        if ($lastpcid != $order_seq) {
                          $lastprid = -1; // force report fields on first line of each procedure
                          $display_string.= "  <td>" . ("$procedure_code: $procedure_name") . "</td>\n";
                        }
                        else {
                          $display_string.= "  <td style='background-color:transparent'>&nbsp;</td>";
                        }

                        // If this starts a new report or a new order, generate the report fields.
                        if ($report_id != $lastprid) {
                          $display_string.= "  <td>";
                          $display_string.= myCellText(oeFormatShortDate($date_report));
                          $display_string.= "</td>\n";

                          $display_string.= "  <td>";
                          $display_string.= myCellText($specimen_num);
                          $display_string.= "</td>\n";

                          $display_string.= "  <td title='" . ('Check mark indicates reviewed') . "'>";
                          $display_string.= myCellText(getListItem('proc_rep_status', $report_status));
                          if ($row[0]['review_status'] == 'reviewed') {
                            $display_string.= " &#x2713;"; // unicode check mark character
                          }
                          $display_string.= "</td>\n";

                          $display_string.= "  <td align='center'>";
                          $display_string.= myCellText($report_noteid);
                          $display_string.= "</td>\n";
                        }
                        else {
                          $display_string.= "  <td colspan='4' style='background-color:transparent'>&nbsp;</td>\n";
                        }

                        if ($result_code !== '') {
                          $display_string.= "  <td>";
                          $display_string.= myCellText($result_code);
                          $display_string.= "</td>\n";
                          $display_string.= "  <td>";
                          $display_string.= myCellText($result_text);
                          $display_string.= "</td>\n";
                          $display_string.= "  <td>";
                          $display_string.= myCellText(getListItem('proc_res_abnormal', $result_abnormal));
                          $display_string.= "</td>\n";
                          $display_string.= "  <td>";
                          $display_string.= myCellText($result_result);
                          $display_string.= "</td>\n";
                          $display_string.= "  <td>";
                          $display_string.= myCellText($result_range);
                          $display_string.= "</td>\n";
                          $display_string.= "  <td>";
                          $display_string.= myCellText($result_units);
                          $display_string.= "</td>\n";
                          $display_string.= "  <td align='center'>";
                          $display_string.= myCellText($result_noteid);
                          $display_string.= "</td>\n";
                        }
                        else {
                          $display_string.= "  <td colspan='7' style='background-color:transparent'>&nbsp;</td>\n";
                        }

                        $display_string.= " </tr>\n";

                        $lastpoid = $order_id;
                        $lastpcid = $order_seq;
                        $lastprid = $report_id;
                        ++$lino;
                      }
                    }
                  $display_string.= "</table>

                  &nbsp;<br />
                  <table width='980px' style='border-width:0px;' >
                   <tr>
                    <td style='border-width:0px;'>";
                  
                    if (!empty($aNotes)) {
                      $display_string.= "<table cellpadding='3' cellspacing='0'>\n";
                      $display_string.= " <tr>\n";
                      $display_string.= "  <th align='center' colspan='2'>" . ('Notes') . "</th>\n";
                      $display_string.= " </tr>\n";
                      foreach ($aNotes as $key => $value) {
                        $display_string.= " <tr>\n";
                        $display_string.= "  <td valign='top'>" . ($key + 1) . "</td>\n";
                        $display_string.= "  <td>" . nl2br(($value)) . "</td>\n";
                        $display_string.= " </tr>\n";
                      }
                      $display_string.= "</table>\n";
                    }
                  $display_string.= "</td></tr>
                  </table>

                  </div>";

                if ($input_form) {
                $display_string.= "</form>";
                } // end if input form 

                $display_string2 = str_replace("<div id='show_div_procedure$encounter' style='display:none'>","<div id='show_div_procedure$encounter' style='display:block'>",$display_string);

                $tempvalue = 0 ;
            }
        }
    }
//    echo "+++++++++++"; echo htmlentities($display_string2); echo "============";
    $returnarray['tempvalue'] = $tempvalue;
    $returnarray['display_string2'] = $display_string2;
    return $returnarray;
}
function static_patient_data($groupName,$result1, $pid,$minValue,$provider,$display_array){ 
    $db = getConnection();
    $result_array   = $result1; 
    $display_string2 = '';
    $display_string2 =  "<div class='text dem' id='DEM'>\n";
//    $getcred1       = "SELECT provider_credentials AS pcred FROM tbl_patientuser WHERE userid='$provider' AND provider_credentials!=''"; 
//    $resCred1=sqlStatement($getcred1);
//    $rowCred1=sqlFetchArray($resCred1);
//    $db->query("SET SQL_BIG_SELECTS=1"); 
//    $db->query( "SET NAMES utf8");
//    $stmt_sql_array = $db->prepare($getcred1) ; 
//    $stmt_sql_array->execute();
//    $rowCred1       = $stmt_sql_array->fetchAll(PDO::FETCH_ASSOC);
//    $credential     = $rowCred1[0]['pcred'];
    $getpagebr      = "SELECT DISTINCT(group_name) FROM tbl_chartui_mapping WHERE screen_name = '$groupName' AND group_name LIKE '%Who' ";
//    $setpagebr= sqlFetchArray($getpagebr);
    $db->query("SET SQL_BIG_SELECTS=1"); 
    $db->query( "SET NAMES utf8");
    $stmt_sql_array = $db->prepare($getpagebr) ; 
    $stmt_sql_array->execute();
    $setpagebr      = $stmt_sql_array->fetchAll(PDO::FETCH_OBJ);
    $display_string2 .= display_div_function("Patient Information",0,$pid);
    $display_string2 .= display_demographics($pid, $groupName);
    $display_string2 .= "</div>\n";   
    foreach($result1 as $key => $value) { 
        $display_title      = '';
        $get_display_field_label = "SELECT title FROM layout_options WHERE form_id = 'CHARTOUTPUT' AND field_id = '".substr($value, 5)."'";
//        $set_display_field_label = sqlFetchArray($get_display_field_label);
        $db->query("SET SQL_BIG_SELECTS=1"); 
        $db->query( "SET NAMES utf8");
        $stmt_sql_array = $db->prepare($get_display_field_label) ; 
        $stmt_sql_array->execute();
        $set_display_field_label = $stmt_sql_array->fetchAll(PDO::FETCH_ASSOC);
        if(!empty($set_display_field_label))
            $display_title = $set_display_field_label[0]['title'];
        if($value=='form_mobile_history' || $value=='form_homehealth_history' || $value=='form_payeraudit_history' || $value=='form_referral_history' || $value=='form_appeal_history'){
            if ($display_array[$value] == "YES"){
                $display_string2 .= display_history($pid,$groupName);
            }
        }
        if($value=='form_mobile_insurance' || $value=='form_homehealth_insurance' || $value=='form_payeraudit_insurance' || $value=='form_referral_insurance' || $value=='form_appeal_insurance'){  
            if ($display_array[$value] == "YES"){
                $display_string= '';
                $display_string .= "<div id='show_div_insurance' style='display:none'>";
                if($key!=$minValue){
                    $display_string .= "<div class='text insurance' style='clear:both;'>";
                    $display_string .= '<br><h2>'.$display_title.':</h2><br>';
                    $display_string2 .= display_insurance($pid,$display_string);
                    $display_string2 .= "</div>"; 
                }else { 
                    $getpagebr = "SELECT DISTINCT(group_name) FROM tbl_chartui_mapping WHERE screen_name = '$groupName' AND group_name = '1Who'";
//                    $setpagebr= sqlFetchArray($getpagebr);
                    $db->query("SET SQL_BIG_SELECTS=1"); 
                    $db->query( "SET NAMES utf8");
                    $stmt_sql_array = $db->prepare($getpagebr) ; 
                    $stmt_sql_array->execute();
                    $setpagebr = $stmt_sql_array->fetchAll(PDO::FETCH_OBJ);
                    $display_string2 .= display_div_function($display_title,0,$pid);
                    $display_string2 .= display_insurance($pid,$display_string);
                }
                $display_string2 .= "</div>";
            }
        }
    }
    
//    echo "<pre>"; print_r($display_array); echo "</pre>";
//    exit();
    $resultant_string = patientdata_not_related_to_encounters($result_array,$groupName,$pid,$display_array);
    $display_string2 .=  html_entity_decode ($resultant_string);
    return htmlentities($display_string2);
}
function patientdata_not_related_to_encounters($result_array,$groupName,$pid,$display_array){
    $db = getConnection();
    $j = 0;
    
    $display_string2    = '';
//    echo "<pre>"; print_r($display_array); echo "</pre>";
//    exit();
    foreach($result_array as $key => $value){
        $display_string     = '';
        $display_title = '';
        $get_display_field_label = "SELECT title FROM layout_options WHERE form_id = 'CHARTOUTPUT' AND field_id = '".substr($value, 5)."'";
        $db->query("SET SQL_BIG_SELECTS=1"); 
        $db->query( "SET NAMES utf8");
        $stmt_sql_array = $db->prepare($get_display_field_label) ; 
        $stmt_sql_array->execute();
        $set_display_field_label = $stmt_sql_array->fetchAll(PDO::FETCH_ASSOC);
//        $set_display_field_label = sqlFetchArray($get_display_field_label);
        if(!empty($set_display_field_label))
            $display_title = $set_display_field_label[0]['title'];
            $display_stringarray = '';
            if($value=='form_mobile_allergy' || $value=='form_homehealth_allergy' || $value=='form_payeraudit_allergy' || $value=='form_referral_allergy' || $value=='form_appeal_allergy'){
                if($display_array[$value] == "YES") {
                    $display_string .= "<div id='show_div_allergy0' style='display:none'>";
                    $setpagebr5 = '';
                    $setpagebr_allr = '';
                    $display_string .= "<div style='clear:both;'>";
                    $getpagebr = "SELECT group_name, layout_type,page_break FROM tbl_chartui_mapping WHERE screen_name = '$groupName' AND group_name LIKE '%Allergies'";
//                    $getpagebr_allr = sqlStatement($getpagebr);
//                    $setpagebr_allr= sqlFetchArray($getpagebr_allr);
                    $db->query("SET SQL_BIG_SELECTS=1"); 
                    $db->query( "SET NAMES utf8");
                    $stmt_sql_array = $db->prepare($getpagebr) ; 
                    $stmt_sql_array->execute();
                    $setpagebr_allr = $stmt_sql_array->fetchAll(PDO::FETCH_OBJ);
                    if(substr($setpagebr_allr[0]->group_name, 1)=='Allergies' ) {
                        $idName = str_replace(" ","-",trim($groupName)."-".'Allergies');
                        if($setpagebr_allr[0]->page_break == 'YES'){ 
                            //display_div_function(substr($setpagebr_allr[0]->group_name, 1),$eidval[$inc]->encounter );
                            $display = display_div_function($display_title,0,$pid );
                        }else{
                            $display = "<h2>". htmlspecialchars($display_title).": </h2>";
                        }               
                        $display_string .= $display ;
                    }
                    $display_string .= " <div id='1".$idName.$j."'  style='clear:both;'>"; 
                    $display_string .= "<ul  class='1".$idName.$j."' >"; 
                    $display_stringarray = display_allergy(0,$setpagebr_allr[0]->layout_type,$display_string,$pid);
//                    $display_stringarray =  display_cc($eidval[$inc]->encounter,$groupName,$display_string,$pid);
                    $display_string2 .= $display_stringarray['display_string2'];
                    $tempvalue       = $display_stringarray['tempvalue'];
                    $display_string2 .= "</ul></div></div>";
                    $display_string2 .= "</div>";
                }
            }
            if($value=='form_mobile_surgery' || $value=='form_homehealth_surgery' || $value=='form_payeraudit_surgery' || $value=='form_referral_surgery' || $value=='form_appeal_surgery'){
                if($display_array[$value] == "YES") {
                    $display_string .= "<div id='show_div_surgery0' style='display:none'>";
                    $setpagebr5 = '';    
                    $setpagebr_allr = '';
                    $display_string .= "<div style='clear:both;'>";
                    $getpagebr = "SELECT group_name, layout_type, page_break FROM tbl_chartui_mapping WHERE screen_name = '$groupName' AND group_name LIKE '%Surgeries'";
//                    $setpagebr_allr = sqlFetchArray($getpagebr_allr); 
                    $db->query("SET SQL_BIG_SELECTS=1"); 
                    $db->query( "SET NAMES utf8");
                    $stmt_sql_array = $db->prepare($getpagebr) ; 
                    $stmt_sql_array->execute();
                    $setpagebr_allr = $stmt_sql_array->fetchAll(PDO::FETCH_OBJ);
                    if(substr($setpagebr_allr[0]->group_name, 1) == 'Surgeries') {
                        $idName = str_replace(" ","-",trim($groupName)."-".'Surgeries');
                         if($setpagebr_allr[0]->page_break  == 'YES'){
                            $display = display_div_function($display_title,0,$pid );
                        }else{
                            $display = "<h2>". htmlspecialchars($display_title).": </h2>";
                        }               

                    }
                    $display_string .= $display ;
                    $display_string .= " <div id='1".$idName.$j."'  style='clear:both;'>"; 
                    $display_string .= "<ul  class='1".$idName.$j."' >"; 
                    $display_stringarray = display_surgery(0,$setpagebr_allr[0]->layout_type,$display_string,$pid);
                    $display_string2 .= $display_stringarray['display_string2'];
                    $tempvalue       = $display_stringarray['tempvalue'];
                    $display_string2 .= "</ul></div></div>";
                    $display_string2 .= "</div>";
                }   
            }    
            if($value=='form_mobile_dental' || $value=='form_homehealth_dental' || $value=='form_payeraudit_dental' || $value=='form_referral_dental' || $value=='form_appeal_dental'){
                if ($display_array[$value] == "YES"){
                    $display_string .= "<div id='show_div_dental0' style='display:none'>";
                    $setpagebr5 = '';
                    $setpagebr_allr = '';
                    $display_string .= "<div style='clear:both;'>";
                    $getpagebr = "SELECT group_name, layout_type,page_break FROM tbl_chartui_mapping WHERE screen_name = '$groupName' AND group_name LIKE '%Dental Problems'";
//                    $getpagebr_allr = sqlStatement($getpagebr);
//                    $setpagebr_allr = sqlFetchArray($getpagebr_allr);
                    $db->query("SET SQL_BIG_SELECTS=1"); 
                    $db->query( "SET NAMES utf8");
                    $stmt_sql_array = $db->prepare($getpagebr) ; 
                    $stmt_sql_array->execute();
                    $setpagebr_allr = $stmt_sql_array->fetchAll(PDO::FETCH_OBJ);
                    if(substr($setpagebr_allr[0]->group_name, 1) == 'Dental Problems') {
                        $idName = str_replace(" ","-",trim($groupName)."-".'Dental Problems');
                        if($setpagebr_allr[0]->page_break  == 'YES'){
                            $display = display_div_function($display_title,0,$pid );
                        }else{
                            $display = "<h2>". htmlspecialchars($display_title).": </h2>";
                        }               
                        $display_string .= $display ;
                    }
                    $display_string .= " <div id='1".$idName.$j."'  style='clear:both;'>"; 
                    $display_string .= "<ul  class='1".$idName.$j."' >"; 
                    $display_stringarray = display_dental(0,$setpagebr_allr[0]->layout_type,$display_string,$pid);
                    $display_string2 .= $display_stringarray['display_string2'];
                    $tempvalue       = $display_stringarray['tempvalue'];
                    $display_string2 .= "</ul></div></div>";
                    $display_string2 .= "</div>";
                }
            }
            if($value=='form_mobile_mproblem' || $value=='form_homehealth_mproblem' || $value=='form_payeraudit_mproblem' || $value=='form_referral_mproblem' || $value=='form_appeal_mproblem'){
                if ($display_array[$value] == "YES"){
                    $display_string .= "<div id='show_div_mproblem0' style='display:none'>";
                    $setpagebr5 = '';
                    $setpagebr_allr = '';
                    $display_string .= "<div style='clear:both;'>";
                    $getpagebr = "SELECT id,group_name, layout_type,page_break FROM tbl_chartui_mapping WHERE screen_name = '$groupName' AND group_name LIKE '%Medical Problems'";
//                    $getpagebr_allr = sqlStatement($getpagebr);
//                    $setpagebr_allr = sqlFetchArray($getpagebr_allr);
                    $db->query("SET SQL_BIG_SELECTS=1"); 
                    $db->query( "SET NAMES utf8");
                    $stmt_sql_array = $db->prepare($getpagebr) ; 
                    $stmt_sql_array->execute();
                    $setpagebr_allr = $stmt_sql_array->fetchAll(PDO::FETCH_OBJ);
                    if(substr($setpagebr_allr[0]->group_name, 1) == 'Medical Problems') {
                        $idName = str_replace(" ","-",trim($groupName)."-".'Medical Problems');
                         if($setpagebr_allr[0]->page_break  == 'YES'){
                            $display = display_div_function($display_title,0,$pid );
                        }else{
                            $display = "<h2>". htmlspecialchars($display_title).": </h2>";
                        }               
                        $display_string .= $display ;
                    }
                    $display_string .= " <div id='1".$idName.$j."'  style='clear:both;'>"; 
                    $display_string .= "<ul  class='1".$idName.$j."' >"; 
                    $display_stringarray = display_mproblem(0,$setpagebr_allr[0]->layout_type,$display_string,$pid);
                    $display_string2 .= $display_stringarray['display_string2'];
                    $tempvalue       = $display_stringarray['tempvalue'];
                    $display_string2 .= "</ul></div></div>";
                    $display_string2 .= "</div>";
                }
            }
            if($value=='form_mobile_med' || $value=='form_homehealth_med' || $value=='form_payeraudit_med' || $value=='form_referral_med' || $value=='form_appeal_med'){
                if ($display_array[$value] == "YES"){
                    $display_string = '';
                    $display_string .= "<div id='show_div_med0' style='display:none'>";
                    $setpagebr5 = '';
                    $setpagebr_allr = '';
                    $display_string .= "<div style='clear:both;'>";
                    $getpagebr = "SELECT group_name, layout_type,page_break FROM tbl_chartui_mapping WHERE screen_name = '$groupName' AND group_name LIKE '%Medication'";
//                    $getpagebr_allr = sqlStatement($getpagebr);
//                    $setpagebr_allr = sqlFetchArray($getpagebr_allr);
                    $db->query("SET SQL_BIG_SELECTS=1"); 
                    $db->query( "SET NAMES utf8");
                    $stmt_sql_array = $db->prepare($getpagebr) ; 
                    $stmt_sql_array->execute();
                    $setpagebr_allr = $stmt_sql_array->fetchAll(PDO::FETCH_OBJ);
                    if(substr($setpagebr_allr[0]->group_name, 1) == 'Medication') {
                        $idName = str_replace(" ","-",trim($groupName)."-".'Medication');
                        if($setpagebr_allr[0]->page_break  == 'YES') {
                            $display = display_div_function($display_title,0,$pid );
                        }else{
                           $display = "<h2>". htmlspecialchars($display_title).": </h2>";
                        }               
                        $display_string .= $display ;
                    }
                    $display_string .= " <div id='1".$idName.$j."'  style='clear:both;'>"; 
                    $display_string .= "<ul  class='1".$idName.$j."' >"; 
                    $display_stringarray = display_medications(0,$setpagebr_allr[0]->layout_type,$display_string,$pid);//print_r($display_stringarray);
                    $display_string2 .= $display_stringarray['display_string2'];
                    $tempvalue        = $display_stringarray['tempvalue'];
                    $display_string2 .= "</ul></div></div>";
                    $display_string2 .= "</div>";
//                    $display_string2 = htmlentities($display_string2);
                }
            }  
            if($value=='form_mobile_dme' || $value=='form_homehealth_dme' || $value=='form_payeraudit_dme' || $value=='form_referral_dme' || $value=='form_appeal_dme'){
                if ($display_array[$value] == "YES"){
                    $display_string .= "<div id='show_div_dme0' style='display:none'>";
                    $setpagebr5 = '';
                    $setpagebr_allr = '';
                    $display_string .= "<div style='clear:both;'>";
                    $getpagebr = "SELECT group_name, layout_type,page_break FROM tbl_chartui_mapping WHERE screen_name = '$groupName' AND group_name LIKE '%DME'";
//                    $getpagebr_allr = sqlStatement($getpagebr);
//                    $setpagebr_allr = sqlFetchArray($getpagebr_allr);
                    $db->query("SET SQL_BIG_SELECTS=1"); 
                    $db->query( "SET NAMES utf8");
                    $stmt_sql_array = $db->prepare($getpagebr) ; 
                    $stmt_sql_array->execute();
                    $setpagebr_allr = $stmt_sql_array->fetchAll(PDO::FETCH_OBJ);
                    if(substr($setpagebr_allr[0]->group_name, 1) == 'DME') {
                        $idName = str_replace(" ","-",trim($groupName)."-".'DME');
                         if($setpagebr_allr[0]->page_break  == 'YES'){
                           $display = display_div_function($display_title,0,$pid );
                        }else{
                            $display = "<h2>". htmlspecialchars($display_title).": </h2>";
                        }               
                        $display_string .= $display ;
                    }
                    $display_string .= " <div id='1".$idName.$j."'  style='clear:both;'>"; 
                    $display_string .= "<ul  class='1".$idName.$j."' >"; 
                    $display_stringarray = display_dme(0,$setpagebr_allr[0]->layout_type,$display_string,$pid);
                    $display_string2 .= $display_stringarray['display_string2'];
                    $tempvalue       = $display_stringarray['tempvalue'];
                    $display_string2 .= "</ul></div></div>";
                    $display_string2 .= "</div>";
                }
            }
            if ($value=='form_mobile_prescript' || $value=='form_homehealth_prescript' || $value=='form_payeraudit_prescript' || $value=='form_referral_prescript' || $value=='form_appeal_prescript'){
                if ($display_array[$value] == "YES"){
                    $display_string .= "<div id='show_div_prescript0' style='display:none'>";
                    $setpagebr5 = '';
                    $setpagebr_allr = '';
                    $display_string .= "<div style='clear:both;'>";
                    $getpagebr = "SELECT group_name, layout_type,page_break FROM tbl_chartui_mapping WHERE screen_name = '$groupName' AND group_name LIKE '%Prescription'";
//                    $getpagebr_allr = sqlStatement($getpagebr);
//                    $setpagebr_allr = sqlFetchArray($getpagebr_allr);
                    $db->query("SET SQL_BIG_SELECTS=1"); 
                    $db->query( "SET NAMES utf8");
                    $stmt_layout = $db->prepare($getpagebr);
                    $stmt_layout->execute();

                    $setpagebr_allr = $stmt_layout->fetchAll(PDO::FETCH_OBJ);
                    if(substr($setpagebr_allr[0]->group_name, 1) == 'Prescription') {
                        $idName = str_replace(" ","-",trim($groupName)."-".'Prescription');
                        if($setpagebr_allr[0]->page_break  == 'YES'){
                           $display = display_div_function($display_title,0,$pid );
                        }else{
                            $display = "<h2>". htmlspecialchars($display_title).": </h2>";
                        }               
                    }
                    $display_string .= $display ;
                    $display_string .= " <div id='1".$idName.$j."'  style='clear:both;'>"; 
                    $display_string .= "<ul  class='1".$idName.$j."' >"; 
                    $display_stringarray = display_prescription($setpagebr_allr[0]->layout_type,$pid,$display_string);
                    $display_string2 .= $display_stringarray['display_string2'];
                    $tempvalue       = $display_stringarray['tempvalue'];
                    $display_string2 .= "</ul></div></div>";
                    $display_string2 .= "</div>";
                }
//            if ($_REQUEST[$value] == "YES"){
//                $display_string .= "<div id='show_div_prescript0' style='display:none'>";
//                $setpagebr_allr = '';
//                $display_string .= "<div style='clear:both;'>";
//                $getpagebr = "SELECT group_name, layout_type,page_break FROM tbl_chartui_mapping WHERE screen_name = '$groupName' AND group_name LIKE '%Prescription'";
//                $getpagebr_allr = sqlStatement($getpagebr);
//                $setpagebr_allr = sqlFetchArray($getpagebr_allr);
//                if(substr($setpagebr_allr['group_name'], 1) == 'Prescription') {
//                    $idName = str_replace(" ","-",trim($groupName)."-".'Prescription');
//                    if($setpagebr_allr['page_break'] == 'YES'){
//                       display_div_function($display_title,0);
//                    }else{
//                        $display_string .= "<h2> $display_title: </h2>";
//                    }
//                }
//                $display_string .= " <div id='1".$idName.$j."'  style='clear:both;'>"; 
//                $display_string .= "<ul  class='1".$idName.$j."' >"; 
//                display_prescription($setpagebr_allr['layout_type']);
//                $display_string .= "</ul></div></div></div>";
//            }
        }
            if($value=='form_mobile_immunizations' || $value=='form_homehealth_immunizations' || $value=='form_payeraudit_immunizations' || $value=='form_referral_immunizations' || $value=='form_appeal_immunizations'){
                if ($display_array[$value] == "YES" ){
                    $display_string .= "<div id='show_div_immunizations' >";
                    $setpagebr_allr = '';
    //                if (acl_check('patients', 'med')) {
                        $getpagebr = "SELECT group_name, layout_type, page_break FROM tbl_chartui_mapping WHERE screen_name = '$groupName' AND group_name LIKE '%Immunization'";
    //                    $getpage = sqlStatement($getpagebr);
    //                    $setpagebr9= sqlFetchArray($getpage);
                        $db->query("SET SQL_BIG_SELECTS=1"); 
                        $db->query( "SET NAMES utf8");
                        $stmt_layout = $db->prepare($getpagebr);
                        $stmt_layout->execute();

                        $setpagebr9 = $stmt_layout->fetchAll(PDO::FETCH_ASSOC);
                        if(substr($setpagebr9[0]['group_name'], 1)=='Immunization') {
                            $idName = $groupName."-".'Immunization';
                            $idName = str_replace(" ","-",$idName);
                            $display_string .= "<div style='clear:both;'>"; 
                            if($setpagebr9[0]['group_name'] == 'YES'){
                                $display_string .= display_div_function($display_title,0,$display_string,$pid);
                        }else{
                            $display_string .= "<h2> $display_title: </h2>";
                        }  
                        }
                        if($display_array[$value] == "YES" ){
                            $display_string .= "<div id='".$idName."' style='clear:both;'>";
                            $display_string .= "<ul  class='".$idName.$j."' >";
                            $sql = "select  c.code_text_short as Vaccine,i1.administered_date AS Date,CONCAT(i1.amount_administered ,' ',i1.amount_administered_unit	)as Amount ,i1.manufacturer as Manufacturer, i1.lot_number as Lot_Number,i1.administered_by_id as AdministeredBy ,i1.education_date as Education_Date,i1.Route as Route ,i1.administration_site as Administration_Site,substring(i1.note,1,20) as immunization_note
                                   from immunizations i1 
                                   left join code_types ct on ct.ct_key = 'CVX' 
                                   left join codes c on c.code_type = ct.ct_id AND i1.cvx_code = c.code 
                                   where i1.patient_id ='".$pid."'  and i1.added_erroneously = 0 
                                   order by administered_date desc";
    //                        $result = sqlStatement($sql);
                            $db->query("SET SQL_BIG_SELECTS=1"); 
                            $db->query( "SET NAMES utf8");
                            $stmt_layout = $db->prepare($sql);
                            $stmt_layout->execute();

                            $result = $stmt_layout->fetchAll(PDO::FETCH_ASSOC);
                            if($result){
    //                           // echo "<li><label> There are no recorded immunizations for this patient at this time.</label></li>";
    //                            
    //                             <script>
    //                                document.getElementById('show_div_immunizations').style.display = "none";
    //                            </script> 

    //                        }else{
    //                            while ($row=sqlFetchArray($result)) {
                                for($i=0; $i< count($result); $i++){
                                    $immun  = $result[$i];   
                                    foreach($immun as $key1 => $value1){
                                        if(!empty($value1)){
                                            if($key1=='AdministeredBy'){
                                                $getporvidername = "SELECT CONCAT(lname,' ',fname) AS name FROM users WHERE id='$value1'" ;
                                                $db->query("SET SQL_BIG_SELECTS=1"); 
                                                $db->query( "SET NAMES utf8");
                                                $stmt_layout = $db->prepare($getporvidername);
                                                $stmt_layout->execute();

                                                $rowName = $stmt_layout->fetchAll(PDO::FETCH_ASSOC);
    //                                            $rowName=sqlFetchArray($getporvidername);
                                                if(!empty($rowName))
                                                    $admin1=$rowName[0]['name'];   
                                                else
                                                    $admin1 = '';
                                                $display_string .= "<li><b>".$key1.": "."</b>".$admin1."</li>";
                                            }else if($key1=='Amount'){
                                                $string = $value1;
                                                $string = explode(' ', $string);
                                                $getunits = "SELECT title FROM list_options WHERE option_id='$string[1]' AND list_id='drug_units'" ;
    //                                            $rowName = sqlFetchArray($getunits);
                                                $db->query("SET SQL_BIG_SELECTS=1"); 
                                                $db->query( "SET NAMES utf8");
                                                $stmt_layout = $db->prepare($getunits);
                                                $stmt_layout->execute();

                                                $rowName = $stmt_layout->fetchAll(PDO::FETCH_ASSOC);
                                                if(!empty($rowName))
                                                    $units=$rowName[0]['title']; 
                                                else
                                                    $units = '';
                                                $display_string .= "<li><b>".ucwords(str_replace('_',' ',$key1)).": "."</b>". $string[0].' '.$units."</li>";
                                            }else if($key1=='Route'){
                                                $getunits = "SELECT title FROM list_options WHERE list_id='drug_route' AND option_id = '$value1'" ;
    //                                            $rowName = sqlFetchArray($getunits);
                                                $db->query("SET SQL_BIG_SELECTS=1"); 
                                                $db->query( "SET NAMES utf8");
                                                $stmt_layout = $db->prepare($getunits);
                                                $stmt_layout->execute();

                                                $rowName = $stmt_layout->fetchAll(PDO::FETCH_ASSOC);
                                                if(!empty($rowName))
                                                    $units=$rowName[0]['title']; 
                                                else
                                                    $units = '';
                                                $display_string .= "<li><b>".ucwords(str_replace('_',' ',$key1)).":</b>".$units."</li>";
                                            }else {
                                                $display_string .= "<li><b>".ucwords(str_replace('_',' ',$key1)).": "."</b>".$value1."</li>";
                                            }
                                        }
                                    }
                                    $display_string .= "<li>&nbsp;</li>";
                                }
                            }
                            $display_string .= "</ul></div></div>\n";
                        }
    //                } 
                    $display_string .= "</div>";
                }
                $display_string2 = $display_string;
            }
    }  
    return htmlentities($display_string2);
}
function myCellText($s) {
  if ($s === '') return '&nbsp;';
  return $s;
}

// Check if the given string already exists in the $aNotes array.
// If not, stores it as a new entry.
// Either way, returns the corresponding key which is a small integer.
function storeNote($s) {
  global $aNotes;
  $key = array_search($s, $aNotes);
  if ($key !== FALSE) return $key;
  $key = count($aNotes);
  $aNotes[$key] = $s;
  return $key;
}
function oeFormatShortDate($date='today') {
//  if ($date === 'today') $date = date('Y-m-d');
  $db = getConnection();
  if (strlen($date) == 10) {
    $getunits = "SELECT gl_value FROM globals WHERE gl_name =  'date_display_format'" ;
    $db->query("SET SQL_BIG_SELECTS=1"); 
    $db->query( "SET NAMES utf8");
    $stmt_layout = $db->prepare($getunits);
    $stmt_layout->execute();

    $rowName = $stmt_layout->fetchAll(PDO::FETCH_ASSOC);
    if(!empty($rowName))
        $units=$rowName[0]['gl_value'];
    // assume input is yyyy-mm-dd
    if ($units == 1)      // mm/dd/yyyy
      $date = substr($date, 5, 2) . '/' . substr($date, 8, 2) . '/' . substr($date, 0, 4);
    else if ($units == 2) // dd/mm/yyyy
      $date = substr($date, 8, 2) . '/' . substr($date, 5, 2) . '/' . substr($date, 0, 4);
    else 
        $date = date('Y-m-d');
  }
  return $date;
}
function getListItem($listid, $value) {
    $db = getConnection();
    $lrow = "SELECT title FROM list_options " .
      "WHERE list_id = '$listid' AND option_id = '$value'";
    $db->query("SET SQL_BIG_SELECTS=1"); 
    $db->query( "SET NAMES utf8");
    $stmt_layout = $db->prepare($lrow);
    $stmt_layout->execute();

    $rowName = $stmt_layout->fetchAll(PDO::FETCH_ASSOC);
    if(!empty($rowName))
        $tmp = $rowName[0]['title'];
    if (empty($tmp)) $tmp = (($value === '') ? '' : "($value)");
    return $tmp;
}

function getPreviousTranscriptions($form_name,$pid,$form_id,$encounter){
    try{
        $db = getConnection();
        $key = 'rotcoderaclla';
        $encryptor    = new \RNCryptor\Encryptor();
        $lrow = "SELECT speech_data,log_date,user FROM mbl_transcribe_log " .
          "WHERE form_name = '$form_name' AND form_id = '$form_id' AND encounter = '$encounter' AND pid = '$pid'"; 
        $db->query("SET SQL_BIG_SELECTS=1"); 
        $db->query( "SET NAMES utf8");
        $stmt_layout = $db->prepare($lrow);
        $stmt_layout->execute();
        $presclist = $stmt_layout->fetchAll(PDO::FETCH_OBJ);          
         if($presclist)
        {
            $presres = json_encode($presclist); 
            echo $presresult = $encryptor->encrypt($presres, $key); 

        }
        else
        {
            $presres = '[{"DataAvailable":"No"}]';
            echo $presresult = $encryptor->encrypt($presres, $key);
        }
                
    }catch(PDOException $e)
    {
        $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo $presresult = $encryptor->encrypt($error, $key);
    }    
}
?>