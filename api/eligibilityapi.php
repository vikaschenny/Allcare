<?php  
/* 
Webservice for user/provider authentication with openemr
*/

// do initial application
//require_once("../interface/globals.php");
require 'Slim/Slim.php';
require 'Slim/Route.php';
require 'AesEncryption/GibberishAES.php';
//require __DIR__ . '/RNCryptor/autoload.php';

// initialize app 
$app = new Slim();

// to get patient eligibility
$app->get('/checkeligscreen/:providerid', 'checkEligScreen');
// to get patient eligibility
$app->get('/checkeligibility/:pid/:dos/:x12', 'checkeligibility');
// to get eligibility based on patient
$app->get('/getpatientpayer/:pid/:dos','getPatientPayer');
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
function checkEligScreen($providerid){
    try{
        $db = getConnection(); 
        $apikey = 'rotcoderaclla';

        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        $resultant = array();

        $resultant[0]['Label']          = 'Patient';
        $resultant[0]['Url']            = "/api/searchpatientbyname/$providerid/";
        $resultant[0]['field_id']       = 'pid';
        $resultant[0]['Type']           = 'searchbox';
        $resultant[0]['isRequired']     = 'Required';
        
        $resultant[1]['Label']             = 'Date:';
        $resultant[1]['field_id']          = 'date_of_service';
        $resultant[1]['Type']              = 'Date';
        $resultant[1]['isRequired']        = 'Required';
        $resultant[1]['SelectedValue']     = '';
        $resultant[1]['placeholder']       = 'Select date';

        $sql3 = "select * from x12_partners ORDER BY name";
        $db->query( "SET NAMES utf8"); 
        $stmt3 = $db->prepare($sql3) ;
        $stmt3->execute();                       
        $getListData2 = $stmt3->fetchAll(PDO::FETCH_OBJ); 
        for($i=0; $i< count($getListData2); $i++){
            $X12info = $getListData2[$i]->id."|".$getListData2[$i]->id_number."|".$getListData2[$i]->x12_sender_id."|".$getListData2[$i]->x12_receiver_id."|".$getListData2[$i]->x12_version."|".$getListData2[$i]->processing_format;
            $getListData[$i]['optionid']  = $X12info;
            $getListData[$i]['optionval'] = $getListData2[$i]->name;
        }
        
        $resultant[2]['Label']             = 'X12 Partners';
        $resultant[2]['field_id']          = 'x12_partner';
        $resultant[2]['Type']              = 'ListBox';
        $resultant[2]['isRequired']        = 'Required';
        $resultant[2]['SelectedValue']     = '';
        $resultant[2]['Options']           = $getListData;
        
//        echo "<pre>"; print_r($resultant); echo"</pre>";
        if($resultant){
            $vitalsdetailsres =  json_encode($resultant); 
            echo $visitresult = GibberishAES::enc($vitalsdetailsres, $apikey);
        }
        else{
            $vitalsdetailsres =  '[{"id":"0"}]';
            echo $visitresult = GibberishAES::enc($vitalsdetailsres, $apikey);
        }
    }catch(PDOException $e){
        $vitalsdetailserror =  '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo $visitresult = GibberishAES::enc($vitalsdetailserror, $apikey);
    }
}

function getPatientPayer($pid,$dos){
    try{
        $db = getConnection(); 
        $apikey = 'rotcoderaclla';

        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        $resultant = array();

        $date        = date_create($dos);
        $month_value = date_format( $date,'m-Y'); 
//        
        $sql = "select t.elig_verify_type,i.pid,i.provider as payer_id from insurance_data i "
                . " INNER JOIN tbl_inscomp_custom_attr_1to1 t ON t.insuranceid = i.provider "
                . "WHERE i.pid = '$pid' AND i.type='primary' AND isExternalPayer <> 'YES'  ORDER BY i.date desc limit 0,1";
        
        $db->query( "SET NAMES utf8"); 
        $stmt = $db->prepare($sql) ;
        $stmt->execute();                       
        $getInsuranceData = $stmt->fetchAll(PDO::FETCH_OBJ); 
        
        $sql2 = "SELECT t.id FROM tbl_eligibility_response_data t 
                        INNER JOIN insurance_data ins ON t.payerid = ins.provider 
                        WHERE t.month = '$month_value' AND t.updated_date > ins.revised_elig_date AND t.pid = ins.pid AND ins.type='primary' AND ins.pid = '$pid' 
                        ORDER BY ins.id DESC LIMIT 0,1 ";
        
        $db->query( "SET NAMES utf8"); 
        $stmt2 = $db->prepare($sql2) ;
        $stmt2->execute();                       
        $getElig_data = $stmt2->fetchAll(PDO::FETCH_OBJ); 
        
        if(!empty($getInsuranceData)){
            
            $getInsuranceData[0]->page1         = '';
            $getInsuranceData[0]->page1_name    = '';
            
            foreach($getInsuranceData[0] as $ikey => $ivalue){
                if($ikey == 'elig_verify_type' && $ivalue  == 'patient_eligibility')
                    $getInsuranceData[0]->page1         = 'elig-verify';
                    $getInsuranceData[0]->page1_name    = 'Eligibility Response';
                if($ikey == 'elig_verify_type' && $ivalue  == 'patient_estimation')
                    $getInsuranceData[0]->page1         = 'patient_estimation_verify';
                    $getInsuranceData[0]->page1_name    = 'Estimation Response';
            }
            $getInsuranceData[0]->page2 = 'save_eligibility_response_data';
            $getInsuranceData[0]->page2_name = 'Eligibility Data Screen';
            $getInsuranceData[0]->form_id = isset($getElig_data[0]->id )? $getElig_data[0]->id  : 0;
        }
//        echo "<pre>"; print_r($getInsuranceData); echo"</pre>";
        if($getInsuranceData){
            $vitalsdetailsres =  json_encode($getInsuranceData); 
            echo $visitresult = GibberishAES::enc($vitalsdetailsres, $apikey);
        }
        else{
            $vitalsdetailsres =  '[{"id":"0"}]';
            echo $visitresult = GibberishAES::enc($vitalsdetailsres, $apikey);
        }
    }catch(PDOException $e){
        $vitalsdetailserror =  '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo $visitresult = GibberishAES::enc($vitalsdetailserror, $apikey);
    }
}

function getX12Partner($id) {
    $db = getConnection(); 
    $rez = "select * from x12_partners where id = '$id'";
    $db->query( "SET NAMES utf8");
    $stmt = $db->prepare($rez) ;
    $stmt->execute();
    $returnval = $stmt->fetchAll(PDO::FETCH_ASSOC);
//	for($iter=0; $iter<count($returnval2); $iter++)
//		$returnval[$iter]=$row;

	return $returnval;
}
function checkeligibility($pid,$month,$x12){
    try{
        $db = getConnection(); 
        $resultant = array();

        $apikey = 'rotcoderaclla';
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        
        // Element data seperator		
        $eleDataSep		= "*";

        // Segment Terminator	
        $segTer		= "~";

        $compEleSep         = '^';

        $date=date_create($month);
        $month_value =  date_format( $date,'m-Y');

        $month_check = "SELECT ter.id FROM tbl_eligibility_response_data ter WHERE ter.pid= $pid AND ter.month = '$month_value' LIMIT 1 ";
        $db->query( "SET NAMES utf8");
        $stmt = $db->prepare($month_check) ;
        $stmt->execute();
        $month_checked = $stmt->fetchAll(PDO::FETCH_ASSOC);   
        $isdata_exist = '';
        if(!empty($month_checked)){
            $isdata_exist = $month_checked[0]['id'];
        }
        if($isdata_exist != ''){
            $formid = $isdata_exist;
            $patientsreminder = array();
            $get_layout2 = "SELECT group_name,list_id, title,field_id, form_id,
                CASE data_type
                WHEN 1  THEN  'List box'
                WHEN 2  THEN  'Textbox'
                WHEN 3  THEN  'Textarea'
                WHEN 4  THEN  'Text-date'
                WHEN 10 THEN  'Providers'
                WHEN 11 THEN  'Providers NPI'
                WHEN 12 THEN  'Pharmacies'
                WHEN 13 THEN  'Squads'
                WHEN 14 THEN  'Organizations'
                WHEN 15 THEN  'Billing codes'
                WHEN 21 THEN  'Checkbox list'
                WHEN 22 THEN  'Textbox list'
                WHEN 23 THEN  'Exam results'
                WHEN 24 THEN  'Patient allergies'
                WHEN 25 THEN  'Checkbox w/text'
                WHEN 26 THEN  'List box w/add'
                WHEN 27 THEN  'Radio buttons'
                WHEN 28 THEN  'Lifestyle status'
                WHEN 31 THEN  'Static Text'
                WHEN 32 THEN  'Smoking Status'
                WHEN 33 THEN  'Race and Ethnicity'
                WHEN 34 THEN  'NationNotes'
                WHEN 35 THEN  'Facilities'
                WHEN 36 THEN  'Date Of Service'
                WHEN 37 THEN  'Insurance Companies'
                WHEN 38 THEN  'Users'
                WHEN 39 THEN  'DateTime'
                END as field_type,                         CASE uor
                                WHEN 1 THEN 'Optional'
                                WHEN 2 THEN 'Required'
                                END as isRequired,seq,max_length,description FROM layout_options WHERE form_id = 'ELIGIBILITY' order by group_name, seq";
            $db->query( "SET NAMES utf8");
            $stmt_layout2 = $db->prepare($get_layout2) ;
            $stmt_layout2->execute();                       
            $layout_fields2 = $stmt_layout2->fetchAll(PDO::FETCH_OBJ);
            if(!empty($layout_fields2)){
                $patientsreminder[substr($layout_fields2[0]->group_name,1)]['form_id'] = $formid;
                for($i=0; $i< count($layout_fields2); $i++){
                    $field_id = $layout_fields2[$i]->field_id;
    //                $sql = "select field_value from lbf_data where field_id  = '". $layout_fields2[$i]->field_id."' and form_id= '$formid'";   
                    $sql = "SELECT `$field_id` as field_value FROM tbl_eligibility_response_data WHERE id ='$formid'";
                    $db->query( "SET NAMES utf8");
                    $stmt = $db->prepare($sql) ;
                    $stmt->execute();                       

                    $patientsreminders = $stmt->fetchAll(PDO::FETCH_OBJ);   
                    $pvalue = isset($patientsreminders[0]->field_value)? $patientsreminders[0]->field_value: '';

                    $patientsreminder[substr($layout_fields2[$i]->group_name,1)][$i]['group_name'] = substr($layout_fields2[$i]->group_name,1);
                    $patientsreminder[substr($layout_fields2[$i]->group_name,1)][$i]['field_id'] = $layout_fields2[$i]->field_id;
                    $patientsreminder[substr($layout_fields2[$i]->group_name,1)][$i]['data_type'] =  $layout_fields2[$i]->field_type;
                    $patientsreminder[substr($layout_fields2[$i]->group_name,1)][$i]['isRequired'] =  $layout_fields2[$i]->isRequired;
                    $patientsreminder[substr($layout_fields2[$i]->group_name,1)][$i]['title']  = $layout_fields2[$i]->title;
                    $patientsreminder[substr($layout_fields2[$i]->group_name,1)][$i]['list_id']  = $layout_fields2[$i]->list_id;
                    $patientsreminder[substr($layout_fields2[$i]->group_name,1)][$i]['seq']  = $layout_fields2[$i]->seq;
                    $patientsreminder[substr($layout_fields2[$i]->group_name,1)][$i]['placeholder']  =$layout_fields2[$i]->description;
                    if($layout_fields2[$i]->max_length == 0)
                        $maxlength = '';
                    else
                        $maxlength = $layout_fields2[$i]->max_length;
                    $patientsreminder[substr($layout_fields2[$i]->group_name,1)][$i]['max_length']  = $maxlength;
                    $get_title2 = "SELECT option_id, title FROM list_options WHERE list_id = '".$layout_fields2[$i]->list_id."' order by seq";
                    $db->query( "SET NAMES utf8");
                    $title_stmt2 = $db->prepare($get_title2) ;
                    $title_stmt2->execute();                       

                    $settitle2 = $title_stmt2->fetchAll(PDO::FETCH_OBJ);
                    $titles= array();
                    if(!empty($settitle2)){
                        for($k = 0; $k< count($settitle2); $k++){
                            $titles[$settitle2[$k]->option_id] = $settitle2[$k]->title;
                        }
                    }
                    $patientsreminder[substr($layout_fields2[$i]->group_name,1)][$i]['options_list']  = $titles;
                    if($layout_fields2[$i]->list_id != ''){
                        $patientsreminder[substr($layout_fields2[$i]->group_name,1)][$i]['selected_value'] = $pvalue;
                    }else{
                        if($layout_fields2[$i]->field_type == 'Static Text'){
                        $patientsreminder[substr($layout_fields2[$i]->group_name,1)][$i]['selected_value'] = $layout_fields2[$i]->description;
                    }else if($layout_fields2[$i]->field_type == 'Providers' || $layout_fields2[$i]->field_type == 'Providers NPI'){
                        $sql3 = "SELECT id, CONCAT(fname, ' ',lname) as name FROM `users`  WHERE active=1 AND authorized=1 ORDER BY fname,lname ";
                        $db->query( "SET NAMES utf8"); 
                        $stmt3 = $db->prepare($sql3) ;
                        $stmt3->execute();                       
                        $getListData = $stmt3->fetchAll(PDO::FETCH_OBJ);  
                        if(!empty($getListData)):
                            $data2 = array();
                            foreach($getListData as $list){
                                $data2[$list->id] = $list->name;
                            }
                        else:
                            $data2 = '';
                        endif;
    //                        $data['options_list'] = $data2; 
                        $patientsreminder[substr($layout_fields2[$i]->group_name,1)][$i]['options_list']  = $data2;
                        $patientsreminder[substr($layout_fields2[$i]->group_name,1)][$i]['selected_value'] = $pvalue;
                    }else if($layout_fields2[$i]->field_type == 'Pharmacies'){
                        $sql3 = "SELECT id, name FROM `pharmacies`   ORDER BY name";
                        $db->query( "SET NAMES utf8"); 
                        $stmt3 = $db->prepare($sql3) ;
                        $stmt3->execute();                       
                        $getListData = $stmt3->fetchAll(PDO::FETCH_OBJ);  
                        if(!empty($getListData)):
                            $data2 = array();
                            foreach($getListData as $list){
                                $data2[$list->id] = $list->name;
                            }
                        else:
                            $data2 = '';
                        endif;
    //                        $data['options_list'] = $data2; 
                        $patientsreminder[substr($layout_fields2[$i]->group_name,1)][$i]['options_list']  = $data2;
                        $patientsreminder[substr($layout_fields2[$i]->group_name,1)][$i]['selected_value'] = $pvalue;
                    }else if($layout_fields2[$i]->field_type == 'Organizations'){
                        $sql3 = "SELECT id, fname, lname, organization, username FROM users WHERE active = 1 AND ( info IS NULL OR info NOT LIKE '%Inactive%' ) AND ( username = '' OR authorized = 1 ) ORDER BY organization, lname, fname";
                        $db->query( "SET NAMES utf8"); 
                        $stmt3 = $db->prepare($sql3) ;
                        $stmt3->execute();                       
                        $getListData = $stmt3->fetchAll(PDO::FETCH_OBJ);  
                        if(!empty($getListData)):
                            $data2 = array();
                            foreach($getListData as $list){
                                $uname = $list->organization;
                                if (empty($uname) || substr($uname, 0, 1) == '(') {
                                    $uname = $list->lname;
                                    if ($list->fname) $uname .= ", " . $list->fname;
                                }
                                $data2[$list->id] = $uname;
                            }
                        else:
                            $data2 = '';
                        endif;
    //                        $data['options_list'] = $data2; 
                        $patientsreminder[substr($layout_fields2[$i]->group_name,1)][$i]['options_list']  = $data2;
                        $patientsreminder[substr($layout_fields2[$i]->group_name,1)][$i]['selected_value'] = $pvalue;
                    }else if($layout_fields2[$i]->field_type == 'Patient allergies'){
                        $sql3 = "SELECT title FROM lists WHERE " .
                            "pid = $patientId AND type = 'allergy' AND enddate IS NULL " .
                            "ORDER BY begdate";
                            $db->query( "SET NAMES utf8"); 
                            $stmt3 = $db->prepare($sql3) ;
                            $stmt3->execute();                       
                            $getListData = $stmt3->fetchAll(PDO::FETCH_OBJ);  
                            if(!empty($getListData)):
                                $data2 = '';
                                foreach($getListData as $liste){
                                    $data2 .= $liste->title;
                                }
                            else:
                                $data2 = '';
                            endif;
    //                            $data['data']['selected_list'] = $data2;
                        $patientsreminder[substr($layout_fields2[$i]->group_name,1)][$i]['selected_value'] = $data2;
                    }else if($layout_fields2[$i]->field_type == 'Lifestyle status'){
                        $patientsreminder[substr($layout_fields2[$i]->group_name,1)][$i]['selected_value'] = $pvalue;
                    }else if($layout_fields2[$i]->field_type == 'Facilities'){
                        $sql3 = "SELECT id, name FROM facility ORDER BY name";
                        $db->query( "SET NAMES utf8"); 
                        $stmt3 = $db->prepare($sql3) ;
                        $stmt3->execute();                       
                        $getListData = $stmt3->fetchAll(PDO::FETCH_OBJ);  
                        if(!empty($getListData)):
                            $data2 = array();
                            foreach($getListData as $list){
                                $data2[$list->id] = $list->name;
                            }
                        else:
                            $data2 = '';
                        endif;
    //                        $data['options_list'] = $data2; 
                        $patientsreminder[substr($layout_fields2[$i]->group_name,1)][$i]['options_list']  = $data2;
                        $patientsreminder[substr($layout_fields2[$i]->group_name,1)][$i]['selected_value'] = $pvalue;
                    }else if($layout_fields2[$i]->field_type == 'Date Of Service'){
                        $sql3 = "SELECT DATE_FORMAT( fe.date, '%Y-%m-%d' ) as date FROM `form_encounter` fe
                            INNER JOIN forms f ON  fe.id = f.form_id AND fe.encounter = f.encounter AND fe.pid = f.pid
                            WHERE fe.pid = $patientId ORDER BY date desc";
                        $db->query( "SET NAMES utf8"); 
                        $stmt3 = $db->prepare($sql3) ;
                        $stmt3->execute();                       
                        $getListData = $stmt3->fetchAll(PDO::FETCH_OBJ);  
                        if(!empty($getListData)):
                            $data2 = array();
                            foreach($getListData as $liste){
                                $data2[$list->date] = $list->date;
                            }
                        else:
                            $data2 = '';
                        endif;
                        $data['data']['list'] = $data2;
                   }else if($layout_fields2[$i]->field_type == 'Insurance Companies'){
                        $sql3 = "SELECT id,  name FROM `insurance_companies`  ORDER BY name";
                        $db->query( "SET NAMES utf8"); 
                        $stmt3 = $db->prepare($sql3) ;
                        $stmt3->execute();                       
                        $getListData = $stmt3->fetchAll(PDO::FETCH_OBJ);  
                        if(!empty($getListData)):
                            $data2 = array();
                            foreach($getListData as $list){
                                $data2[$list->id] = $list->name;
                            }
                        else:
                            $data2 = '';
                        endif;
    //                        $data['options_list'] = $data2; 
                        $patientsreminder[substr($layout_fields2[$i]->group_name,1)][$i]['options_list']  = $data2;
                        $patientsreminder[substr($layout_fields2[$i]->group_name,1)][$i]['selected_value'] = $pvalue;
                    }else if($layout_fields2[$i]->field_type == 'Users'){
                        $sql3 = "SELECT id, CONCAT(fname, ' ',lname) as name FROM `users`  WHERE username <> '' ORDER BY fname,lname ";
                        $db->query( "SET NAMES utf8"); 
                        $stmt3 = $db->prepare($sql3) ;
                        $stmt3->execute();                       
                        $getListData = $stmt3->fetchAll(PDO::FETCH_OBJ);  
                        if(!empty($getListData)):
                            $data2 = array();
                            foreach($getListData as $list){
                                $data2[$list->id] = $list->name;
                            }
                        else:
                            $data2 = '';
                        endif;
    //                        $data['options_list'] = $data2; 
                        $patientsreminder[substr($layout_fields2[$i]->group_name,1)][$i]['options_list']  = $data2;
                        $patientsreminder[substr($layout_fields2[$i]->group_name,1)][$i]['selected_value'] = $pvalue;
                    }else{
                        $patientsreminder[substr($layout_fields2[$i]->group_name,1)][$i]['selected_value'] = $pvalue;
                    }
                    }
                }
            }
            $resultant['data'] = $patientsreminder;
            $resultant['type'] = 'layout';
    //        echo "<pre>"; print_r($patientsreminder); echo "</pre>";
        }else{
            $sql = "SELECT DATE_FORMAT(e.pc_eventDate, '%Y%m%d') as pc_eventDate, e.pc_facility, p.lname, p.fname, p.mname, DATE_FORMAT(p.dob, '%Y%m%d') as dob, p.ss, p.sex, p.pid, p.pubpid, i.policy_number, i.provider as payer_id, i.subscriber_relationship, i.subscriber_lname, i.subscriber_fname, i.subscriber_mname, DATE_FORMAT(i.subscriber_dob, '%m/%d/%Y') as subscriber_dob, i.subscriber_ss, i.subscriber_sex, DATE_FORMAT(i.date,'%Y%m%d') as date, d.lname as provider_lname, d.fname as provider_fname, d.npi as provider_npi, d.upin as provider_pin, f.federal_ein, f.facility_npi, f.name as facility_name, c.name as payer_name FROM openemr_postcalendar_events AS e LEFT JOIN users AS d on (e.pc_aid is not null and e.pc_aid = d.id) LEFT JOIN facility AS f on (f.id = e.pc_facility) 
                LEFT JOIN patient_data AS p ON p.pid = e.pc_pid 
                LEFT JOIN insurance_data AS i ON (i.id =( SELECT id FROM insurance_data AS i WHERE pid = p.pid AND type = 'primary' ORDER BY date DESC LIMIT 1 ) ) 
                LEFT JOIN insurance_companies as c ON (c.id = i.provider) 
                WHERE e.pc_pid IS NOT NULL AND e.pc_eventDate >=  '$month'
            AND e.pc_eventDate <=  '$month' AND (i.policy_number is not null and i.policy_number != '') AND p.pid = $pid ORDER BY p.lname,p.fname,p.mname ASC ";
            $db->query( "SET NAMES utf8");
            $stmt = $db->prepare($sql) ;
            $stmt->execute();
            $sprintdata = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // to get x12 id
            $clearinghouse	= getX12Partner($x12);

        //    echo "<pre>";print_r($clearinghouse);echo "</pre>";
            $X12info = $clearinghouse[0]['id']."|".$clearinghouse[0]['id_number']."|".$clearinghouse[0]['x12_sender_id']."|".$clearinghouse[0]['x12_receiver_id']."|".$clearinghouse[0]['x12_version']."|".$clearinghouse[0]['processing_format'];

            $compEleSep = '^';
        //    echo "<pre>";print_r($clearinghouses);echo "</pre>";
            $resp = allCare_mbl_print_elig($sprintdata,$X12info,$segTer,$compEleSep);//print_r($printData); echo "===";
        //    $printData = allCare_mbl_print_elig('pc_eventDate,pc_facility,lname,fname,mname,dob,ss,sex,pid,pubpid,policy_number,payer_id,subscriber_relationship,subscriber_lname,subscriber_fname,subscriber_mname,subscriber_dob,subscriber_ss,subscriber_sex,date,provider_lname,provider_fname,provider_npi,provider_pin,federal_ein,facility_npi,facility_name,payer_name 20160324,3,0000,000,,20160302,,Male,6021,6021,978669869,5257,jhfgh,gkg,kggk,jkjgk,11/25/2006,989898698,Female,20151125,,,,,46-4596181,1124441423,Texas Physician House Calls (H),American Republic Insurance Company 20160325,3,0000,000,,20160302,,Male,6021,6021,978669869,5257,jhfgh,gkg,kggk,jkjgk,11/25/2006,989898698,Female,20151125,Ketha,Sumana,1962447805,,46-4596181,1124441423,Texas Physician House Calls (H),American Republic Insurance Company 20160325,3,0000,000,,20160302,,Male,6021,6021,978669869,5257,jhfgh,gkg,kggk,jkjgk,11/25/2006,989898698,Female,20151125,Ketha,Sumana,1962447805,,46-4596181,1124441423,Texas Physician House Calls (H),American Republic Insurance Company 20160325,3,0000,000,,20160302,,Male,6021,6021,978669869,5257,jhfgh,gkg,kggk,jkjgk,11/25/2006,989898698,Female,20151125,Ketha,Sumana,1962447805,,46-4596181,1124441423,Texas Physician House Calls (H),American Republic Insurance Company ','5|46-4596718||ZIRMED|005010X222A1|standard','~','^');

            $getcred = "SELECT title,option_id FROM list_options WHERE list_id='AllCareConfig' and option_id IN('zirmedUsername','zirmedPassword','zirmedCustID','zirmedResponseType')";
            $db->query( "SET NAMES utf8");
            $stmt = $db->prepare($getcred) ;
            $stmt->execute();
            $setcred = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if(!empty($setcred)){ 
        //        echo "<pre>"; print_r($setcred); echo "</pre>";exit();
                for($i=0; $i< count($setcred); $i++){
                    ${$setcred[$i]['option_id']} = $setcred[$i]['title'];
                }

                $curl = curl_init();

                // Define URL where the form resides
                $form_url = 'https://eligibilityapi.zirmed.com/1.0/Rest/Gateway/GatewayAsync.ashx';

                // This is the data to POST to the form. The KEY of the array is the name of the field. The value is the value posted.
                $data_to_post = array();
                $data_to_post['UserID']         = $zirmedUsername;
                $data_to_post['Password']       = $zirmedPassword;
                $data_to_post['CustID']         = $zirmedCustID;
                $data_to_post['DataFormat' ]    = 'X12';

                $data_to_post['Data'] = $resp; //'ISA*00*0000000   *00*0000000000*ZZ*101246         *ZZ*ZIRMED         *151209*0752*U*00401*000000001*1*P*~^GS*HS*101246*ZIRMED*20151209*075240*000000002*X*004010X092A1^ST*270*000000003^BHT*0022*13*PROVTest600*20151209*075240  ^HL*1**20*1^NM1*PR*2*Medicare B Texas (SMTX0)*****46*ZIRMED^HL*2*1*21*1^NM1*IP*1*Texas Physician House Calls (H)*Perkins*Darolyn***XX*1609272905^REF*4A*^HL*3*2*22*0^TRN*1*1234501*9000000000*5432101^NM1*IL*1*Brown*Billy****MI*455040140A^REF*EJ*5979^DMG*D8*19560804^DTP*472*D8*20151209^EQ*30^HL*4*2*22*0^TRN*1*1234502*9000000000*5432102^NM1*IL*1*Wilson*Connie****MI*462561011C5^REF*EJ*1234^DMG*D8*19650919^DTP*472*D8*20151209^EQ*30^HL*5*2*22*0^TRN*1*1234503*9000000000*5432103^NM1*IL*1*Chitwood*Virginia****MI*466204078D^REF*EJ*6000^DMG*D8*19290703^DTP*472*D8*20151209^EQ*30^HL*6*2*22*0^TRN*1*1234504*9000000000*5432104^NM1*IL*1*Rose*Refugia****MI*449667780A^REF*EJ*1389^DMG*D8*19420913^DTP*472*D8*20151209^EQ*30^HL*7*2*22*0^TRN*1*1234505*9000000000*5432105^NM1*IL*1*Finley*Governor****MI*437501408A^REF*EJ*6020^DMG*D8*19380314^DTP*472*D8*20151209^EQ*30^HL*8*2*22*0^TRN*1*1234506*9000000000*5432106^NM1*IL*1*Hanks*Roylene****MI*459845711A^REF*EJ*5932^DMG*D8*19490803^DTP*472*D8*20151209^EQ*30^HL*9*2*22*0^TRN*1*1234507*9000000000*5432107^NM1*IL*1*Mullen*Jettie****MI*465522005A^REF*EJ*6039^DMG*D8*19340911^DTP*472*D8*20151209^EQ*30^HL*10*2*22*0^TRN*1*1234508*9000000000*5432108^NM1*IL*1*Draper*Charlesetta****MI*464279993A^REF*EJ*2924^DMG*D8*19610413^DTP*472*D8*20151209^EQ*30^HL*11*2*22*0^TRN*1*1234509*9000000000*5432109^NM1*IL*1*Portley*Meladie****MI*462157775A^REF*EJ*5663^DMG*D8*19760403^DTP*472*D8*20151209^EQ*30^HL*12*2*22*0^TRN*1*1234510*9000000000*5432110^NM1*IL*1*Curlin*Franklin****MI*456689901A^REF*EJ*1999^DMG*D8*19421226^DTP*472*D8*20151209^EQ*30^HL*13*2*22*0^TRN*1*1234511*9000000000*5432111^NM1*IL*1*Oliver*Ora*F***MI*464628748A^REF*EJ*5747^DMG*D8*19461126^DTP*472*D8*20151209^EQ*30^HL*14*2*22*0^TRN*1*1234512*9000000000*5432112^NM1*IL*1*Rufus*Bessie****MI*450480485A^REF*EJ*5789^DMG*D8*19280502^DTP*472*D8*20151209^EQ*30^HL*15*2*22*0^TRN*1*1234513*9000000000*5432113^NM1*IL*1*Hoffmann*Jerry****MI*452528349A^REF*EJ*5855^DMG*D8*19370409^DTP*472*D8*20151209^EQ*30^HL*16*2*22*0^TRN*1*1234514*9000000000*5432114^NM1*IL*1*West*Rebertha****MI*450983344A^REF*EJ*1119^DMG*D8*19370215^DTP*472*D8*20151209^EQ*30^HL*17*2*22*0^TRN*1*1234515*9000000000*5432115^NM1*IL*1*Wilmore*Elizabeth****MI*450665969A^REF*EJ*3186^DMG*D8*19441120^DTP*472*D8*20151209^EQ*30^HL*18*2*22*0^TRN*1*1234516*9000000000*5432116^NM1*IL*1*Anderson*Dorothy****MI*459748446A^REF*EJ*6046^DMG*D8*19440925^DTP*472*D8*20151209^EQ*30^HL*19*2*22*0^TRN*1*1234517*9000000000*5432117^NM1*IL*1*Shoulder*Joann****MI*436196361A^REF*EJ*6013^DMG*D8*19581011^DTP*472*D8*20151209^EQ*30^HL*20*2*22*0^TRN*1*1234518*9000000000*5432118^NM1*IL*1*Durant*Tyree*P***MI*562745698A^REF*EJ*5828^DMG*D8*19481206^DTP*472*D8*20151209^EQ*30^HL*21*2*22*0^TRN*1*1234519*9000000000*5432119^NM1*IL*1*Luna*Guadalupe****MI*457042557A^REF*EJ*1245^DMG*D8*19310930^DTP*472*D8*20151209^EQ*30^SE*141*000000003^GE*1*000000002^IEA*1*000000001^';
                $data_to_post['ResponseType']   = $zirmedResponseType;


                //
                //// Set the options
                curl_setopt($curl,CURLOPT_URL, $form_url);
                //
                //  This sets the number of fields to post
                curl_setopt($curl,CURLOPT_POST, sizeof($data_to_post));
                //echo "<p style='color:blue;'><strong>Requested Data Sent to Zirmed.</strong></p>";
                //echo "<p style='color:blue;'>Parameters passed are: <ol style='color:blue;'><li>Userid</li><li>Password</li><li>CustId</li><li>X12 DATA</li><li>Input File format (X12)</li><li>Response format (HTML)</li></ol></p>";
                //print_r($data_to_post);
                //
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                //  This is the fields to post in the form of an array.
                curl_setopt($curl,CURLOPT_POSTFIELDS, $data_to_post);
                //
                ////execute the post
                //echo "<br /><br /><br />";
                //echo "<p style='color:blue;'><strong>Response Recieved From Zirmed</strong></p>";
                echo $result = curl_exec($curl);
            //    }

                //
                ////close the connection
                curl_close($curl); 
//                $arr = array_map('utf8_encode', $result);
                $resultant['data'] = htmlspecialchars($result);
                $resultant['type'] = 'html';
            }
        }
    if($resultant){
            $vitalsdetailsres =  json_encode($resultant); 
            echo $visitresult = GibberishAES::enc($vitalsdetailsres, $apikey);
        }
        else{
            $vitalsdetailsres =  '[{"id":"0"}]';
            echo $visitresult = GibberishAES::enc($vitalsdetailsres, $apikey);
        }
    }catch(PDOException $e){
        $vitalsdetailserror =  '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo $visitresult = GibberishAES::enc($vitalsdetailserror, $apikey);
    }
    
//    while($setcred = sqlFetchArray($getcred)){
//        ${$setcred['option_id']} = $setcred['title'];
//    }
}
function allCare_mbl_print_elig($res,$X12info,$segTer,$compEleSep){
		
	$i=1;

	$PATEDI	   = "";

	// For Header Segment 

	$nHlCounter = 1;
	$rowCount	= 0;
	$trcNo		= 1234501;
	$refiden	= 5432101;
	
//	while ($row = sqlFetchArray($res)) 
        for($i=0; $i<count($res); $i++)
	{
		$row = $res[$i]; 
		if($nHlCounter == 1)
		{
			// create ISA 
			$PATEDI	   = create_ISA($row,$X12info,$segTer,$compEleSep);
			
			// create GS 
			$PATEDI	  .= create_GS($row,$X12info,$segTer,$compEleSep);

			// create ST 
			$PATEDI	  .= create_ST($row,$X12info,$segTer,$compEleSep);
			
			// create BHT 
			$PATEDI	  .= create_BHT($row,$X12info,$segTer,$compEleSep);
			
			// For Payer Segment 
				
			$PATEDI  .= create_HL($row,1,$X12info,$segTer,$compEleSep);
			$PATEDI  .= allcare_create_NM1($row,'PR',$X12info,$segTer,$compEleSep);

			// For Provider Segment 				
					
			$PATEDI  .= create_HL($row,2,$X12info,$segTer,$compEleSep);
			$PATEDI  .= allcare_create_NM1($row,'1P',$X12info,$segTer,$compEleSep);
			$PATEDI  .= create_REF($row,'1P',$X12info,$segTer,$compEleSep);

			$nHlCounter = $nHlCounter + 2;	
			$segmentcount = 7; // segement counts - start from ST 
		}

		// For Subscriber Segment 				
		
		$PATEDI  .= create_HL($row,$nHlCounter,$X12info,$segTer,$compEleSep);
		$PATEDI  .= create_TRN($row,$trcNo,$refiden,$X12info,$segTer,$compEleSep);
		$PATEDI  .= allcare_create_NM1($row,'IL',$X12info,$segTer,$compEleSep);
		$PATEDI  .= create_REF($row,'IL',$X12info,$segTer,$compEleSep);
		$PATEDI  .= create_DMG($row,$X12info,$segTer,$compEleSep);
		
		//	$PATEDI  .= create_DTP($row,'102',$X12info,$segTer,$compEleSep);
		
		$PATEDI  .= create_DTP($row,'472',$X12info,$segTer,$compEleSep);
		$PATEDI  .= create_EQ($row,$X12info,$segTer,$compEleSep);
								
		$segmentcount	= $segmentcount + 7;
		$nHlCounter	= $nHlCounter + 1;
		$rowCount	= $rowCount + 1;
		$trcNo		= $trcNo + 1;
		$refiden	= $refiden + 1;
		

		if($rowCount == count($res))
		{
			$segmentcount = $segmentcount + 1;
			$PATEDI	  .= create_SE($row,$segmentcount,$X12info,$segTer,$compEleSep);
			$PATEDI	  .= create_GE($row,$X12info,$segTer,$compEleSep);
			$PATEDI	  .= create_IEA($row,$X12info,$segTer,$compEleSep);
		}
	}

	return $PATEDI;
}

function create_ISA($row,$X12info,$segTer,$compEleSep) {

	$ISA	 =	array();

	$ISA[0] = "ISA";							// Interchange Control Header Segment ID 
	
	$ISA[1] = "00";								// Author Info Qualifier 
	
	$ISA[2] = str_pad("0000000",10," ");		// Author Information 
	
	$ISA[3] = "00";								//   Security Information Qualifier
												//   MEDI-CAL NOTE: For Leased-Line & Dial-Up use '01', 
												//   for BATCH use '00'.
												//   '00' No Security Information Present 
												//   (No Meaningful Information in I04)

	$ISA[4] = str_pad("0000000000",10," ");		// Security Information 
	
	$ISA[5] = str_pad("ZZ",2," ");				// Interchange ID Qualifier
	
	$ISA[6] = str_pad($X12info[2],15," ");		// INTERCHANGE SENDER ID 
	
	$ISA[7] = str_pad("ZZ",2," ");				// Interchange ID Qualifier 
	
	$ISA[8] = str_pad($X12info[3],15," ");		// INTERCHANGE RECEIVER ID  
	
	$ISA[9] = str_pad(date('ymd'),6," ");		// Interchange Date (YYMMDD) 
	
	$ISA[10] = str_pad(date('Hi'),4," ");		// Interchange Time (HHMM) 
	
	$ISA[11] = "U";								// Interchange Control Standards Identifier 
	
	$ISA[12] = str_pad("00401",5," ");			// Interchange Control Version Number 
	
	$ISA[13] = str_pad("000000001",9," ");		// INTERCHANGE CONTROL NUMBER   
	
	$ISA[14] = str_pad("1",1," ");				// Acknowledgment Request [0= not requested, 1= requested]  
	
	$ISA[15] =  str_pad("P",1," ");				// Usage Indicator [ P = Production Data, T = Test Data ]  
	
	$ISA['Created'] = implode('*', $ISA);		// Data Element Separator 

	$ISA['Created'] = $ISA['Created'] ."*";

	$ISA['Created'] = $ISA ['Created'] . $segTer . $compEleSep; 
	
	return trim($ISA['Created']);
	
}

// GS Segment  - EDI-270 format 

function create_GS($row,$X12info,$segTer,$compEleSep) {

	$GS	   = array();

	$GS[0] = "GS";						// Functional Group Header Segment ID 
	
	$GS[1] = "HS";						// Functional ID Code [ HS = Eligibility, Coverage or Benefit Inquiry (270) ] 
	
	$GS[2] =  $X12info[2];				// Application Sender's ID 
	
	$GS[3] =  $X12info[3];				// Application Receiver's ID 
	
	$GS[4] = date('Ymd');				// Date [CCYYMMDD] 
	
	$GS[5] = date('His');				// Time [HHMM] Group Creation Time  
	
	$GS[6] = "000000002";				// Group Control Number 
	
	$GS[7] = "X";					// Responsible Agency Code Accredited Standards Committee X12 ] 
	
	$GS[8] = "004010X092A1";			// Version Release / Industry[ Identifier Code Query 

	$GS['Created'] = implode('*', $GS);		// Data Element Separator 

	$GS['Created'] = $GS ['Created'] . $compEleSep; 
	 
	return trim($GS['Created']);
	
}

// ST Segment  - EDI-270 format 

function create_ST($row,$X12info,$segTer,$compEleSep) {

	$ST	   =	array();

	$ST[0] = "ST";								// Transaction Set Header Segment ID 
	
	$ST[1] = "270";								// Transaction Set Identifier Code (Inquiry Request) 
	
	$ST[2] = "000000003";						// Transaction Set Control Number - Must match SE's 
	
	$ST['Created'] = implode('*', $ST);			// Data Element Separator 

	$ST['Created'] = $ST ['Created'] . $compEleSep; 
	 
	return trim($ST['Created']);
			
}

// BHT Segment  - EDI-270 format 

function create_BHT($row,$X12info,$segTer,$compEleSep) {

	$BHT	=	array();
	
	$BHT[0] = "BHT";						// Beginning of Hierarchical Transaction Segment ID 

	$BHT[1] = "0022";						// Subscriber Structure Code   

	$BHT[2] = "13";							// Purpose Code - This is a Request   

	$BHT[3] = "PROVTest600";				//  Submitter Transaction Identifier  
											//This information is required by the information Receiver 
											//when using Real Time transactions. 
											//For BATCH this can be used for optional information.

	$BHT[4] = str_pad(date('Ymd'),8," ");			// Date Transaction Set Created 
	
	$BHT[5] = str_pad(date('His'),8," ");			// Time Transaction Set Created 

	$BHT['Created'] = implode('*', $BHT);			// Data Element Separator 

	$BHT['Created'] = $BHT ['Created'] . $compEleSep; 
	 
	return trim($BHT['Created']);
	
}

// HL Segment  - EDI-270 format 

function create_HL($row, $nHlCounter,$X12info,$segTer,$compEleSep) {

	$HL		= array();

	$HL[0]		= "HL";			// Hierarchical Level Segment ID 
	$HL_LEN[0]	=  2;

	$HL[1] = $nHlCounter;		// Hierarchical ID No. 
	
	if($nHlCounter == 1)
	{ 
		$HL[2] = ""; 
		$HL[3] = 20;			// Description: Identifies the payor, maintainer, or source of the information.
		$HL[4] = 1;				// 1 Additional Subordinate HL Data Segment in This Hierarchical Structure. 
	}
	else if($nHlCounter == 2)
	{
		$HL[2] = 1;				// Hierarchical Parent ID Number 
		$HL[3] = 21;			// Hierarchical Level Code. '21' Information Receiver
		$HL[4] = 1;				// 1 Additional Subordinate HL Data Segment in This Hierarchical Structure. 
	}
	else
	{
		$HL[2] = 2;
		$HL[3] = 22;			// Hierarchical Level Code.'22' Subscriber 
		$HL[4] = 0;				// 0 no Additional Subordinate in the Hierarchical Structure. 
	}
	
	$HL['Created'] = implode('*', $HL);		// Data Element Separator 

	$HL['Created'] = $HL ['Created'] . $compEleSep; 
	 
	return trim($HL['Created']);

}

// NM1 Segment  - EDI-270 format 

function create_NM1($row,$nm1Cast,$X12info,$segTer,$compEleSep) {

	$NM1		= array();
	
	$NM1[0]		= "NM1";					// Subscriber Name Segment ID 
	
	if($nm1Cast == 'PR')
	{
		$NM1[1] = "PR";						// Entity ID Code - Payer [PR Payer] 
		$NM1[2] = "2";						// Entity Type - Non-Person 
		$NM1[3] = $row["payer_name"];		// Organizational Name 
		$NM1[4] = "";						// Data Element not required.
		$NM1[5] = "";						// Data Element not required.
		$NM1[6] = "";						// Data Element not required.
		$NM1[7] = "";						// Data Element not required.
		$NM1[8] = "46";						// 46 - Electronic Transmitter Identification Number (ETIN) 
		$NM1[9] = $X12info[3];				// Application Sender's ID 
	}
	else if($nm1Cast == '1P')
	{
		$NM1[1] = "IP";						// Entity ID Code - Provider [1P Provider]
		$NM1[2] = "1";						// Entity Type - Person 
		$NM1[3] = $row['facility_name'];			// Organizational Name 
		$NM1[4] = $row['provider_lname'];			// Data Element not required.
		$NM1[5] = $row['provider_fname'];			// Data Element not required.
		$NM1[6] = "";						// Data Element not required.
		$NM1[7] = "";						// Data Element not required.
		$NM1[8] = "XX";						
		$NM1[9] = $row['provider_npi'];		
	}
	else if($nm1Cast == 'IL')
	{
		$NM1[1] = "IL";						// Insured or Subscriber 
		$NM1[2] = "1";						// Entity Type - Person 
		$NM1[3] = $row['lname'];				// last Name	
		$NM1[4] = $row['fname'];				// first Name	
		$NM1[5] = $row['mname'];				// middle Name	
		$NM1[6] = "";						// data element 
		$NM1[7] = "";						// data element 
		$NM1[8] = "MI";						// Identification Code Qualifier 
		//$NM1[9] = $row['subscriber_ss'];			// Identification Code 
                $NM1[9] = $row['policy_number'];			// Identification Code // Subhan
	}
	
	$NM1['Created'] = implode('*', $NM1);				// Data Element Separator 

	$NM1['Created'] = $NM1['Created'] . $compEleSep; 
	 
	return trim($NM1['Created']);

}

// AllCare NM1 Segment  - EDI-270 format 

function allcare_create_NM1($row,$nm1Cast,$X12info,$segTer,$compEleSep) {
//        echo "<pre>"; print_r($row); echo "</pre>";
	$NM1		= array();
	
	$NM1[0]		= "NM1";					// Subscriber Name Segment ID 
	
	if($nm1Cast == 'PR')
	{
		$NM1[1] = "PR";						// Entity ID Code - Payer [PR Payer] 
		$NM1[2] = "2";						// Entity Type - Non-Person 
		$NM1[3] = $row["payer_name"];		// Organizational Name 
		$NM1[4] = "";						// Data Element not required.
		$NM1[5] = "";						// Data Element not required.
		$NM1[6] = "";						// Data Element not required.
		$NM1[7] = "";						// Data Element not required.
		$NM1[8] = "PI";						// PI for zirmed webservice 
                preg_match('#\((.*?)\)#', $NM1[3], $match); 
                $NM1[9] = $match[1];				// Application Sender's ID 
	}
	else if($nm1Cast == '1P')
	{
		$NM1[1] = "IP";						// Entity ID Code - Provider [1P Provider]
		$NM1[2] = "1";						// Entity Type - Person 
		$NM1[3] = $row['facility_name'];			// Organizational Name 
		$NM1[4] = $row['provider_lname'];			// Data Element not required.
		$NM1[5] = $row['provider_fname'];			// Data Element not required.
		$NM1[6] = "";						// Data Element not required.
		$NM1[7] = "";						// Data Element not required.
		$NM1[8] = "XX";						
		$NM1[9] = $row['provider_npi'];		
	}
	else if($nm1Cast == 'IL')
	{
		$NM1[1] = "IL";						// Insured or Subscriber 
		$NM1[2] = "1";						// Entity Type - Person 
		$NM1[3] = $row['lname'];				// last Name	
		$NM1[4] = $row['fname'];				// first Name	
		$NM1[5] = $row['mname'];				// middle Name	
		$NM1[6] = "";						// data element 
		$NM1[7] = "";						// data element 
		$NM1[8] = "MI";						// Identification Code Qualifier 
		//$NM1[9] = $row['subscriber_ss'];			// Identification Code 
                $NM1[9] = $row['policy_number'];			// Identification Code // Subhan
	}
	
	$NM1['Created'] = implode('*', $NM1);				// Data Element Separator 

	$NM1['Created'] = $NM1['Created'] . $compEleSep; 
	 
	return trim($NM1['Created']);

}

// REF Segment  - EDI-270 format 

function create_REF($row,$ref,$X12info,$segTer,$compEleSep) {

	$REF	=	array();

	$REF[0] = "REF";						// Subscriber Additional Identification 

	if($ref == '1P')
	{
		$REF[1] = "4A";						// Reference Identification Qualifier 
		$REF[2] = $row['provider_pin'];				// Provider Pin. 
	}
	else
	{
		$REF[1] = "EJ";						// 'EJ' for Patient Account Number 
		$REF[2] = $row['pid'];					// Patient Account No. 
	}
	$REF['Created'] = implode('*', $REF);				// Data Element Separator 

	$REF['Created'] = $REF['Created'] . $compEleSep; 
	 
	return trim($REF['Created']);
  
}

// TRN Segment - EDI-270 format 

function create_TRN($row,$tracno,$refiden,$X12info,$segTer,$compEleSep) {

	$TRN	=	array();

	$TRN[0] = "TRN";						// Subscriber Trace Number Segment ID 

	$TRN[1] = "1";							// Trace Type Code � Current Transaction Trace Numbers 

	$TRN[2] = $tracno;						// Trace Number 

	$TRN[3] = "9000000000";						// Originating Company ID � must be 10 positions in length 

	$TRN[4] = $refiden;						// Additional Entity Identifier (i.e. Subdivision) 

	$TRN['Created'] = implode('*', $TRN);				// Data Element Separator 

	$TRN['Created'] = $TRN['Created'] . $compEleSep; 
	 
	return trim($TRN['Created']);
  
}

// DMG Segment - EDI-270 format 

function create_DMG($row,$X12info,$segTer,$compEleSep) {

	$DMG	=	array();
	
	$DMG[0] = "DMG";							// Date or Time or Period Segment ID 

	$DMG[1] = "D8";								// Date Format Qualifier - (D8 means CCYYMMDD) 

	$DMG[2] = $row['dob'];						// Subscriber's Birth date 

	$DMG['Created'] = implode('*', $DMG);		// Data Element Separator 

	$DMG['Created'] = $DMG['Created'] .  $compEleSep; 
	 
	return trim($DMG['Created']);			
}

// DTP Segment - EDI-270 format 

function create_DTP($row,$qual,$X12info,$segTer,$compEleSep) {

	$DTP	=	array();
	
	$DTP[0] = "DTP";						// Date or Time or Period Segment ID 
	
	$DTP[1] = $qual;						// Qualifier - Date of Service 
	
	$DTP[2] = "D8";							// Date Format Qualifier - (D8 means CCYYMMDD) 
	
	if($qual == '102'){
		$DTP[3] = $row['date'];				// Date 
	}else{
		$DTP[3] = $row['pc_eventDate'];		// Date of Service 
	}
	$DTP['Created'] = implode('*', $DTP);	// Data Element Separator 

	$DTP['Created'] = $DTP['Created'] .  $compEleSep; 
	 
	return trim($DTP['Created']);
}

// EQ Segment - EDI-270 format 

function create_EQ($row,$X12info,$segTer,$compEleSep) {

	$EQ		=	array();
	
	$EQ[0]	= "EQ";									// Subscriber Eligibility or Benefit Inquiry Information 
	
	$EQ[1]	= "30";									// Service Type Code 
	
	$EQ['Created'] = implode('*', $EQ);				// Data Element Separator 

	$EQ['Created'] = $EQ['Created'] . $compEleSep; 
	 
	return trim($EQ['Created']);
}

// SE Segment - EDI-270 format 

function create_SE($row,$segmentcount,$X12info,$segTer,$compEleSep) {

	$SE	=	array();
	
	$SE[0] = "SE";								// Transaction Set Trailer Segment ID 

	$SE[1] = $segmentcount;						// Segment Count 

	$SE[2] = "000000003";						// Transaction Set Control Number - Must match ST's 

	$SE['Created'] = implode('*', $SE);			// Data Element Separator 

	$SE['Created'] = $SE['Created'] . $compEleSep; 
	 
	return trim($SE['Created']);
}

// GE Segment - EDI-270 format 

function create_GE($row,$X12info,$segTer,$compEleSep) {

	$GE	=	array();
	
	$GE[0]	= "GE";							// Functional Group Trailer Segment ID 

	$GE[1]	= "1";							// Number of included Transaction Sets 

	$GE[2]	= "000000002";						// Group Control Number 

	$GE['Created'] = implode('*', $GE);				// Data Element Separator 

	$GE['Created'] = $GE['Created'] . $compEleSep; 
	 
	return trim($GE['Created']);
}

// IEA Segment - EDI-270 format 

function create_IEA($row,$X12info,$segTer,$compEleSep) {

	$IEA	=	array();
	
	$IEA[0] = "IEA";						// Interchange Control Trailer Segment ID 

	$IEA[1] = "1";							// Number of included Functional Groups 

	$IEA[2] = "000000001";						// Interchange Control Number 

	$IEA['Created'] = implode('*', $IEA);

	$IEA['Created'] = $IEA['Created'] .  $compEleSep; 
	 
	return trim($IEA['Created']);
}