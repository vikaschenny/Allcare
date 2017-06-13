<?php  
/* 
Webservice for user/provider authentication with openemr
*/

// do initial application
//require_once("../interface/globals.php");
require 'Slim/Slim.php';
require 'Slim/Route.php';
require 'AesEncryption/GibberishAES.php';
require_once('../library/encounter_events.inc.php');

// initialize app 
$app = new Slim();
// for issues
$app->get('/editissues/:id/:pid/:type','editIssues');
// to get addressbook search
$app->post('/searchaddressbook','searchAddressbook');
// for search icd's 
$app->get('/searchicds/:type/:value','searchICDs');
// for edit Prescription
$app->get('/editprescriptioin/:pid/:id','editPrescription');
// for edit Immunization
$app->get('/editimmunization/:pid/:id','editImmunization');
// for save issues
$app->post('/savedynamicissues','saveDynamicIssues');
// for save Prescription
$app->post('/savedynamicprescription','saveDynamicPrescription');
// for save Immunization
$app->post('/savedynamicimmunization','saveDynamicImmunization');
// for delete issues
$app->get('/deleteissue/:pid/:id/:type/:username/:eid','deleteIssue');
// for delete addressbook
$app->get('/deleteaddressbook/:id/:username','deleteAddressbook');
// for delete addressbook cred
$app->get('/deleteaddressbookcred/:id/:username','deleteAddressbookCred');
// for delete addressbook contact
$app->get('/deleteaddressbookcontact/:id/:username','deleteAddressbookContact');
// for delete patient agency
$app->get('/deleteagency/:id/:username','deleteAgency');
// to get users list
$app->get('/getallusers','getAllUsers');
// to add field to list
$app->get('/addlist/:option_id/:list_id/:username', 'addListOptions');
// to add finalize data for issues screens and all lbf screens
$app->post('/addlbfreview', 'addLBFReview');
// to edit demographics
$app->get('/editdemographics/:pid/:group_name','editDemographics');
// to save demographics
$app->post('/savedemographics','saveDemographics');
// to save audit notes
$app->post('/saveauditnotes','saveAuditNotes');
// to edit patient facility
$app->get('/editpatientfacility/:id', 'editPatientFaility');
// to edit agencies
$app->get('/editagencies/:id', 'editAgencies');
// to create agencies
$app->get('/createagencies', 'createAgencies');
// to save patient agency
$app->post('/savepatientagency', 'savePatientAgency');
// to edit newencounterscreen
$app->get('/newencounterscreen/:loginProvderId', 'newEncounterScreen');
// to save new encounter
$app->post('/newencounter', 'createNewEncounter');
// create new patient
$app->get('/createpatient','createNewPatient');
// create save patient
$app->post('/savenewpatient','saveNewPatient');
// to create new Procedure order
$app->get('/getprocedureorder/:id/:eid/:pid','getProcedureOrder');
// to search Procedure order
$app->get('/searchprocedure/:labid/:searchstring','searchProcedure');
// to save Procedure order
$app->post('/saveprocedureorder','saveProcedureOrder');
// to get user attributes
$app->get('/usercustomattributes/:uid','getUsersCustomAttributes');
// to get user credentials
$app->get('/usercred/:uid','getUsersCred');
// to get user contacts
$app->get('/userpayroll/:uid','getUsersPayroll');
// to edit user attributes
$app->get('/editusercustomattributes/:uid/:id','editUsersCustomAttributes');
// to edit user credentials
$app->get('/editusercred/:uid/:id','editUsersCred');
// to edit user contacts
$app->get('/edituserpayroll/:uid/:id','editUsersPayroll');
// to edit user attributes
$app->get('/createusercustomattributes/:uid','createUsersCustomAttributes');
// to edit user credentials
$app->get('/createusercred/:uid','createUsersCred');
// to edit user contacts
$app->get('/createuserpayroll/:uid','createUsersPayroll');
// to save user attributes
$app->post('/saveusercustomattributes','saveUsersCustomAttributes');
// to save user credentials
$app->post('/saveusercred','saveUsersCred');
// to save user contacts
$app->post('/saveuserpayroll','saveUsersPayroll');
//to create/ edit message
$app->get('/editmessage/:uid/:id', 'editMessages');
//to save message
$app->post('/savemessage', 'saveMessage');
//to create / edit Appointment
$app->get('/createappointment/:uid', 'createAppointment');
// method to save new appointment
$app->post('/saveappointment', 'saveAppointment');
//to create / edit Reminder
$app->get('/editreminder/:uid/:id', 'editReminder');
// to insert remainder
$app->post('/savereminder','saveReminder');
//to create / edit Reminder
$app->get('/editinsurance/:type/:pid/:id', 'editInsurance');
//to save insurance
$app->post('/saveinsurance', 'saveInsurance');
// to get Certification Recertification
$app->get('/getcertrecert/:encounterid/:formid/:uid', 'getCertification_Recertification');
// to get patient eligibility
$app->get('/checkeligibility/:pid/:month', 'checkeligibility');

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

	$dbhost="mysql51-140.wc2.dfw1.stabletransit.com";
	$dbuser="551948_qa2allcr";
	$dbpass="<)rSg3q=)64Rd=z~";
	$dbname="551948_qa2allcr";		
  
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
function editIssues($id,$pid,$type){
    $apikey = 'rotcoderaclla';
    // The default key size is 256 bits.
    $old_key_size = GibberishAES::size();
    GibberishAES::size(256);
    try
    {
        $db = getConnection();
        $issues = array();
        $setted = 0;
        if($type == 'allergy'):
            $list_id = 'allergy_issue_list';
            $setted = 1;
        elseif($type == 'surgery'):
            $list_id =  'surgery_issue_list';
            $setted = 1;
        elseif($type == 'medication'):
            $list_id =  'medication_issue_list';
            $setted = 1;
        elseif($type == 'medical_problem'):
            $list_id =  'medical_problem_issue_list';
            $setted = 1;
        endif;
        
        
        $issues[0]['Label'] = 'Type';
        $issues[0]['Value'] = $type;
        $issues[0]['Type'] = 'NotEditable';
        $issues[0]['field_id'] = 'type';
        $issues[0]['isRequired'] = 'Optional';
        $issues[0]['placeholder'] = '';
        $issues[0]['max_length'] = '200';
        if($setted == 1){
            $issues[1]['Label'] = '';
            $get_allergylist = array();
            $get_allergy = "SELECT option_id,title FROM list_options WHERE list_id = '$list_id'  order by seq";
            $db->query( "SET NAMES utf8");
            $stmt_list = $db->prepare($get_allergy) ;
            $stmt_list->execute();

            $get_allergylist2 = $stmt_list->fetchAll(PDO::FETCH_OBJ);
            for($i=0; $i< count($get_allergylist2); $i++){
                $get_allergylist[$i]['optionid'] = $get_allergylist2[$i]->option_id;
                $get_allergylist[$i]['optionval'] = $get_allergylist2[$i]->title;
            }
            $issues[1]['Value'] = $get_allergylist;
            $issues[1] ['Note'] = "(Select one of these, or type your own title)";
            $issues[1] ['Type'] = 'ListBox';
            $issues[1] ['SelectedValue'] = '';
            $issues[1] ['linkedfield'] = 'title';
            $issues[1]['max_length'] = '';
            $issues[1]['isRequired'] = 'Optional';
            $issues[1]['placeholder'] = '';
            $issues[1]['max_length'] = '';
        }
        $issues[2]['Label'] = 'Title';
        $issues[2]['Value'] = '';
        $issues[2]['field_id'] = 'title';
        $issues[2]['Type'] = 'TextBox';
        $issues[2]['isRequired'] = 'Required';
        $issues[2]['placeholder'] = 'Enter title';
        $issues[2]['max_length'] = '60';
        
        $issues[3]['Label'] = 'Diagnosis Code';
        $issues[3]['Value'] = '';
        $issues[3]['field_id'] = 'diagnosis';
        $issues[3]['Type'] = 'Clickable';
        $issues[3]['isRequired'] = 'Optional';
        $issues[3]['placeholder'] = 'Click to select diagnosis code';
        $issues[3]['max_length'] = '200';
        
        $issues[3] ['Note'] = "(Click to select or Change Diagnoses)";
        $issues[4]['Label'] = 'Begin Date';
        $issues[4]['Value'] = '';
        $issues[4]['field_id'] = 'begdate';
        $issues[4]['Type'] = 'DateTime';
        $issues[4]['isRequired'] = 'Optional';
        $issues[4]['placeholder'] = 'Select begin date';
        $issues[4]['max_length'] = '20';
        $issues[4]['checkdatelink'] = 'begdate-enddate';
        
        $issues[5]['Label'] = 'End Date';
        $issues[5]['Value'] = '';
        $issues[5]['field_id'] = 'enddate';
        $issues[5]['Note'] = '(leave blank if still active)';
        $issues[5]['Type'] = 'DateTime';
        $issues[5]['isRequired'] = 'Optional';
        $issues[5]['placeholder'] = 'Select end date';
        $issues[5]['max_length'] = '20';
        $issues[5]['checkdatelink'] = 'begdate-enddate';
        
        
        $get_occurrence = "SELECT option_id,title FROM list_options WHERE list_id = 'occurrence'  order by seq";
        $db->query( "SET NAMES utf8");
        $stmt_occurrence = $db->prepare($get_occurrence) ;
        $stmt_occurrence->execute();
        $get_occurrencelist= array();
        $get_occurrencelist2 = $stmt_occurrence->fetchAll(PDO::FETCH_OBJ);
        for($i=0; $i< count($get_occurrencelist2); $i++){
            $get_occurrencelist[$i]['optionid'] = $get_occurrencelist2[$i]->option_id;
            $get_occurrencelist[$i]['optionval'] = $get_occurrencelist2[$i]->title;
        }
        $issues[6]['Label'] = 'Occurrence';
        $issues[6]['Value'] = $get_occurrencelist;
        $issues[6]['field_id'] = 'occurrence';
        $issues[6]['Type'] = 'ListBox';
        $issues[6]['isRequired'] = 'Optional';
        $issues[6]['max_length'] = '';
        
        $issues[7]['Label'] = 'Reaction';
        $issues[7]['Value'] = '';
        $issues[7]['field_id'] = 'reaction';
        $issues[7]['Type'] = 'TextBox';
        $issues[7]['isRequired'] = 'Optional';
        $issues[7]['placeholder'] = 'Enter reaction';
        $issues[7]['max_length'] = '200';
        
        $issues[8]['Label'] = 'Referred by';
        $issues[8]['Value'] = '';
        $issues[8]['field_id'] = 'referredby';
        $issues[8]['Type'] = 'TextBox';
        $issues[8]['isRequired'] = 'Optional';
        $issues[8]['placeholder'] = 'Enter Referred by';
        $issues[8]['max_length'] = '200';
        $get_outcomelist = array();
        $get_outcome = "SELECT option_id,title FROM list_options WHERE list_id = 'outcome'  order by seq";
        $db->query( "SET NAMES utf8");
        $stmt_outcome = $db->prepare($get_outcome) ;
        $stmt_outcome->execute();

        $get_outcomelist2 = $stmt_outcome->fetchAll(PDO::FETCH_OBJ);
        for($i=0; $i< count($get_outcomelist2); $i++){
            $get_outcomelist[$i]['optionid'] = $get_outcomelist2[$i]->option_id;
            $get_outcomelist[$i]['optionval'] = $get_outcomelist2[$i]->title;
        }
        $issues[9]['Label'] = 'Outcome';
        $issues[9]['Value'] = $get_outcomelist;
        $issues[9]['field_id'] = 'outcome';
        $issues[9]['Type'] = 'ListBox';
        $issues[9]['isRequired'] = 'Optional';
        $issues[9]['placeholder'] = 'Enter outcome';
        $issues[9]['max_length'] = '200';
        
        $issues[10]['Label'] = 'Destination';
        $issues[10]['Value'] = '';
        $issues[10]['field_id'] = 'destination';
        $issues[10]['Type'] = 'TextBox';
        $issues[10]['isRequired'] = 'Optional';
        $issues[10]['placeholder'] = 'Enter destination';
        $issues[10]['max_length'] = '200';
        
        if($id != 0){
            $get_layout = "SELECT * FROM lists WHERE type = '$type' and id = '$id' and pid= '$pid'";
            $db->query( "SET NAMES utf8");
            $stmt_layout = $db->prepare($get_layout) ;
            $stmt_layout->execute();

            $layout_fields = $stmt_layout->fetchAll(PDO::FETCH_OBJ);
            
        }
        
//        if(!empty($layout_fields)){
                $issues[2]['SelectedValue']     = isset($layout_fields[0]->title)       ? $layout_fields[0]->title          : '';
                $issues[3]['SelectedValue']     = isset($layout_fields[0]->diagnosis)   ? $layout_fields[0]->diagnosis      : '';
                $issues[4]['SelectedValue']     = isset($layout_fields[0]->begdate)     ? $layout_fields[0]->begdate        : '';
                $issues[5]['SelectedValue']     = isset($layout_fields[0]->enddate)     ? $layout_fields[0]->enddate        : '';
                $issues[6]['SelectedValue']     = isset($layout_fields[0]->occurrence)  ? $layout_fields[0]->occurrence     : '';
                $issues[7]['SelectedValue']     = isset($layout_fields[0]->reaction)    ? $layout_fields[0]->reaction       : '';
                $issues[8]['SelectedValue']     = isset($layout_fields[0]->referredby)  ? $layout_fields[0]->referredby     : '';
                $issues[9]['SelectedValue']     = isset($layout_fields[0]->outcome)     ? $layout_fields[0]->outcome        : '';
                $issues[10]['SelectedValue']    = isset($layout_fields[0]->destination) ? $layout_fields[0]->destination    : '';
//            }
        

//                echo "<pre>"; print_r(array_values($issues)); echo "</pre>";
        if($issues)
        {
            $patientres = json_encode(($issues));
            echo $patientresult = GibberishAES::enc($patientres, $apikey);
        }
        else
        {
            $patientres =  '[{"id":"0"}]';
            echo $patientresult = GibberishAES::enc($patientres, $apikey);
        }
    }
    catch(PDOException $e)
    {

        $patientres =  '{"error":{"text":'. $e->getMessage() .'}}';
        echo $patientresult = GibberishAES::enc($patientres, $apikey);
    }
}
function searchAddressbook (){
    try
    {
        $apikey = 'rotcoderaclla';
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        $request = Slim::getInstance()->request();
        $result =  GibberishAES::dec($request->getBody(), $apikey);
        $array              = json_decode($result,TRUE);
//        $form_organization  = 'best s';
//        $form_fname         = 'e';
//        $form_lname         = '';
//        $form_specialty     = '';
//        $form_abook_type    = 'hha';
//        $form_external      = 1;
        $form_organization  = $array['form_organization'];
        $form_fname         = $array['form_fname'];
        $form_lname         = $array['form_lname'];
        $form_specialty     = $array['form_specialty'];
        $form_abook_type    = $array['form_abook_type'];
        $form_external      = $array['form_external'];

    
        $db = getConnection();
        $sqlBindArray = array();
        $query = "SELECT u.id,u.organization,u.username,CONCAT(u.fname,' ', u.lname)as name,u.specialty,u.phonew1 as Phone,u.phonecell as Mobile,u.fax,u.email,u.street,u.city,u.state,u.zip, (select title FROM list_options WHERE option_id = u.abook_type AND list_id ='abook_type') as Type FROM users AS u " .
          "LEFT JOIN list_options AS lo ON " .
          "list_id = 'abook_type' AND option_id = u.abook_type " .
          "WHERE u.active = 1 AND ( u.authorized = 1 OR u.username = '' ) ";
        if ($form_organization != '') {
         $query .= "AND u.organization LIKE '%$form_organization%' ";
        }
        if ($form_lname != '') {
         $query .= "AND u.lname LIKE '$form_lname%' ";
        }
        if ($form_fname != '') {
         $query .= "AND u.fname LIKE '$form_fname%' ";
        }
        if ($form_specialty!= '') {
         $query .= "AND u.specialty LIKE '%$form_specialty%' ";
        }
        if ($form_abook_type != '') {
         $query .= "AND u.abook_type LIKE '$form_abook_type' ";
        }
        if ($form_external != 0) {
         $query .= "AND u.username = '' ";
        }
        if ($form_lname != '') { 
            $query .= "ORDER BY u.lname, u.fname, u.mname";
        } else if ($form_organization != '') {
            $query .= "ORDER BY u.organization";
        } else {
            $query .= "ORDER BY u.organization, u.lname, u.fname";
        }
        $query .= " LIMIT 500";

        $db->query( "SET NAMES utf8");
        $stmt_list = $db->prepare($query) ;
        $stmt_list->execute();
        $set_query = $stmt_list->fetchAll(PDO::FETCH_OBJ);
//        echo "<pre>";        print_r($set_query); echo "</pre>";
        if($set_query)
        {
            $patientres = json_encode(array_values($set_query));
            echo $patientresult = GibberishAES::enc($patientres, $apikey);
        }
        else
        {
            $patientres =  '[{"id":"0"}]';
            echo $patientresult = GibberishAES::enc($patientres, $apikey);
        }
    }
    catch(PDOException $e)
    {

        $patientres =  '{"error":{"text":'. $e->getMessage() .'}}';
        echo $patientresult = GibberishAES::enc($patientres, $apikey);
    }
}
function searchICDs($form_code_type,$search_term){
    $apikey = 'rotcoderaclla';
    // The default key size is 256 bits.
    $old_key_size = GibberishAES::size();
    GibberishAES::size(256);
    try
    {
        $db = getConnection(); 
        if($search_term == 'all'){
            $search_term2 = '%';
        }else {
            $search_term2 = $search_term;
        }
        if($form_code_type == 'ICD9'){
            $query = "SELECT   icd9_dx_code.formatted_dx_code AS code, icd9_dx_code.long_desc AS code_text, icd9_dx_code.short_desc AS code_text_short,  'ICD9' AS code_type_name
                    FROM icd9_dx_code
                    LEFT OUTER JOIN  `codes` ON icd9_dx_code.formatted_dx_code = codes.code
                    AND codes.code_type =  '2'
                    WHERE (icd9_dx_code.formatted_dx_code LIKE  '%$search_term2%'  OR icd9_dx_code.long_desc LIKE  '%$search_term2%' OR icd9_dx_code.long_desc LIKE  '%short_desc%'  )
                    AND icd9_dx_code.active =  '1'
                    AND (
                    codes.active =1 || codes.active IS NULL
                    )
                    ORDER BY icd9_dx_code.formatted_dx_code +0, icd9_dx_code.formatted_dx_code LIMIT 500";
        }elseif($form_code_type == 'ICD10'){ 
            $query = "SELECT  icd10_dx_order_code.dx_code , icd10_dx_order_code.formatted_dx_code as code, icd10_dx_order_code.long_desc as code_text, icd10_dx_order_code.short_desc as code_text_short,   'ICD10' as code_type_name                   
                FROM icd10_dx_order_code 
                LEFT OUTER JOIN `codes`  ON icd10_dx_order_code.formatted_dx_code = codes.code AND codes.code_type = '102' 
                WHERE (icd10_dx_order_code.formatted_dx_code like '%$search_term2%'   OR icd10_dx_order_code.long_desc like '%$search_term2%'   OR icd10_dx_order_code.short_desc like '%$search_term2%'  )
                AND icd10_dx_order_code.active='1' 
                AND icd10_dx_order_code.valid_for_coding = '1' 
                AND (codes.active = 1 || codes.active IS NULL)  
                ORDER BY icd10_dx_order_code.formatted_dx_code+0,icd10_dx_order_code.formatted_dx_code LIMIT 500";
        }elseif($form_code_type == 'CVX'){ 
            $query = "SELECT  codes.code as code, codes.code_text as code_text, codes.code_text_short as code_text_short,  'CVX' as code_type_name   
                FROM codes 
                WHERE (codes.code like '$search_term2%'  OR codes.code_text like '$search_term2%'  OR codes.code_text_short like '$search_term2%'  )
                AND codes.code_type=100 
                AND codes.active = 1  
                ORDER BY codes.code+0,codes.code LIMIT 500";
        }elseif($form_code_type == 'CPT'){ 
            $query = "SELECT  codes.code as code, codes.code_text as code_text, codes.code_text_short as code_text_short,  'CPT' as code_type_name   
                FROM codes 
                WHERE (codes.code like '%$search_term2%'  OR codes.code_text like '%$search_term2%'  OR codes.code_text_short like '%$search_term2%'  )
                AND codes.code_type=1 
                AND codes.active = 1  
                ORDER BY codes.code+0,codes.code LIMIT 500";
        }

        $db->query("SET SQL_BIG_SELECTS=1"); 
        $db->query( "SET NAMES utf8");
        $stmt_layout = $db->prepare($query);
        $stmt_layout->execute();

        $layout_fields = $stmt_layout->fetchAll(PDO::FETCH_OBJ);
//        echo "<pre>"; print_r($layout_fields); echo "</pre>";

        if($layout_fields)
        {
            $patientres = json_encode($layout_fields);
            echo $patientresult = GibberishAES::enc($patientres, $apikey);
        }
        else
        {
            $patientres =  '[{"id":"0"}]';
            echo $patientresult = GibberishAES::enc($patientres, $apikey);
        }
    }
    catch(PDOException $e)
    {

        $patientres =  '{"error":{"text":'. $e->getMessage() .'}}';
        echo $patientresult = GibberishAES::enc($patientres, $apikey);
    }
}
function editImmunization($patientId,$id){
    $apikey = 'rotcoderaclla';
    // The default key size is 256 bits.
    $old_key_size = GibberishAES::size();
    GibberishAES::size(256);
    try
    {
        $db = getConnection(); 
        if($id !== 0){
             $query = "select i.id as id, i.administered_date, i.immunization_id as immunization_id, i.cvx_code as cvx_code, c.code_text_short as cvx_text, i.manufacturer,i.lot_number,i.administered_by_id,(select concat(lname,' ',fname) from users where id= i.administered_by) as administered_by,
             i.education_date,i.vis_date,i.note,i.amount_administered,i.amount_administered_unit ,i.expiration_date,i.route,i.administration_site,i.added_erroneously".
             " from immunizations i ".
             " left join code_types ct on ct.ct_key = 'CVX' ".
             " left join codes c on c.code_type = ct.ct_id AND i.cvx_code = c.code ".
             " where i.patient_id = $patientId  AND i.id = $id".
             " and i.added_erroneously = 0".
             " order by i.administered_date desc";
        
            $db->query("SET SQL_BIG_SELECTS=1"); 
            $db->query( "SET NAMES utf8");
            $stmt_layout = $db->prepare($query);
            $stmt_layout->execute();

            $layout_fields = $stmt_layout->fetchAll(PDO::FETCH_OBJ);
//            echo "<pre>"; print_r($layout_fields); echo "</pre>";
        }
        $immu[0]['Label'] = 'Immunization (CVX Code)';
        $immu[0]['Type']  = 'Clickable';
        $immu[0]['field_id']  = 'cvx_code';
        $immu[0]['SelectedValue'] = isset($layout_fields[0]->cvx_code) ? $layout_fields[0]->cvx_code: '';
        $immu[0]['Notes'] = 'Click to Select of change CVX code ';
        $immu[0]['isRequired']  = 'Required';
        $immu[0]['placeholder']  = 'Click here to select CVX code';
        
        $immu[1]['Label'] = 'Date & Time Administered';
        $immu[1]['Type'] = 'DateTime';
        $immu[1]['field_id'] = 'administered_date';
        $immu[1]['SelectedValue'] = isset($layout_fields[0]->administered_date) ? $layout_fields[0]->administered_date: '';
        $immu[1]['isRequired']  = 'Optional';
        $immu[1]['placeholder']  = 'Select date';
        $immu[1]['checkdatelink']  = 'administered_date-expiration_date';
        
        $immu[2]['Label'] = 'Amount Administered';
        $immu[2]['Type'] = 'TextBox';
        $immu[2]['field_id'] = 'amount_administered';
        $immu[2]['SelectedValue'] = isset($layout_fields[0]->amount_administered) ? $layout_fields[0]->amount_administered: '';
        $immu[2]['isRequired']  = 'Optional';
        $immu[2]['placeholder']  = 'Enter Amount Administered';
        
        $unit_sql1 ="SELECT option_id , title FROM list_options WHERE list_id='drug_units' order by seq";
        $res_unit1 = $db->prepare($unit_sql1);
        $db->query( "SET NAMES utf8");
        $res_unit1->execute();
        $admin_units2 = $res_unit1->fetchAll(PDO::FETCH_OBJ);
        if(!empty($admin_units2)){
            for($i=0; $i< count($admin_units2); $i++){
                $admin_units[$i]['optionid'] = $admin_units2[$i]->option_id;
                $admin_units[$i]['optionval'] = $admin_units2[$i]->title;
            }
        }
        
        $immu[3]['Label'] = '';
        $immu[3]['Type'] = 'Listbox';
        $immu[3]['field_id'] = 'amount_administered_unit';
        $immu[3]['SelectedValue'] = isset($layout_fields[0]->amount_administered_unit) ? $layout_fields[0]->amount_administered_unit: '';
        $immu[3]['Value'] = $admin_units;
        
                
        $immu[4]['Label'] = 'Immunization Expiration Date';
        $immu[4]['Type'] = 'DateTime';
        $immu[4]['field_id'] = 'expiration_date';
        $immu[4]['SelectedValue'] = isset($layout_fields[0]->expiration_date) ? $layout_fields[0]->expiration_date: '';
        $immu[4]['isRequired']  = 'Optional';
        $immu[4]['placeholder']  = 'Enter Immunization Expiration Date';
        $immu[4]['checkdatelink']  = 'administered_date-expiration_date';
        
        $immu[5]['Label'] = 'Immunization Manufacturer';
        $immu[5]['Type'] = 'TextBox';
        $immu[5]['field_id'] = 'manufacturer';
        $immu[5]['SelectedValue'] = isset($layout_fields[0]->manufacturer) ? $layout_fields[0]->manufacturer: '';
        $immu[5]['isRequired']  = 'Optional';
        $immu[5]['placeholder']  = 'Enter Immunization Manufacturer';
        
        $immu[6]['Label'] = 'Immunization Lot Number';
        $immu[6]['Type'] = 'TextBox';
        $immu[6]['field_id'] = 'lot_number';
        $immu[6]['SelectedValue'] = isset($layout_fields[0]->lot_number) ? $layout_fields[0]->lot_number: '';
        $immu[6]['isRequired']  = 'Optional';
        $immu[6]['placeholder']  = 'Enter Lot Number';
        
        if(isset($layout_fields[0]->administered_by_id )){
            if($layout_fields[0]->administered_by_id != '' && isset($layout_fields[0]->administered_by_id)){
                $qry2 = "SELECT concat(lname,', ',fname) as full_name FROM users WHERE id=:loginProvderId";
                $stmt2 = $db->prepare($qry2) ;
                $stmt2->bindParam("loginProvderId", $layout_fields[0]->administered_by_id);            
                $stmt2->execute();
                $rs2 = $stmt2->fetchAll(PDO::FETCH_OBJ);
            }
        }
        $qry = "select id, concat(lname,', ',fname) as full_name " .
                       "from users where username != '' " .
                       "order by concat(lname,', ',fname)";
        $stmt = $db->prepare($qry) ;
        $stmt->execute();
        $rs = $stmt->fetchAll(PDO::FETCH_OBJ);
        if(!empty($rs)){
            for($i=0; $i< count($rs); $i++){
                $usernames[$i]['optionid'] = $rs[$i]->id;
                $usernames[$i]['optionval'] = $rs[$i]->full_name;
            }
        }
        $immu[7]['Label'] = 'Name and Title of Immunization Administrator';
        $immu[7]['Type'] = 'NotEditable';
        $immu[7]['SelectedValue'] = isset($rs2[0]->full_name) ? $rs2[0]->full_name: '';
        $immu[7]['isRequired']  = 'Optional';
        $immu[7]['placeholder']  = 'Select Administrator name frombelow dropdown ';
        
        
        $immu[8]['Label'] = 'or choose';
        $immu[8]['Type'] = 'Listbox';
        $immu[8]['field_id'] = 'administered_by_id';
        $immu[8]['SelectedValue'] = isset($layout_fields[0]->administered_by_id) ? $layout_fields[0]->administered_by_id: '';
        $immu[8]['Value'] = $usernames;
        $immu[8]['linkedfield'] = 'Name and Title of Immunization Administrator';
        
        $immu[9]['Label'] = 'Date Immunization Information Statements Given';
        $immu[9]['Type'] = 'Date';
        $immu[9]['field_id'] = 'education_date';
        $immu[9]['SelectedValue'] = isset($layout_fields[0]->education_date) ? $layout_fields[0]->education_date: '';
        $immu[9]['isRequired']  = 'Optional';
        $immu[9]['placeholder']  = 'Select date';
        
        $immu[10]['Label'] = 'Date of VIS Statement (?)';
        $immu[10]['Type'] = 'Date';
        $immu[10]['field_id'] = 'vis_date';
        $immu[10]['SelectedValue'] = isset($layout_fields[0]->vis_date) ? $layout_fields[0]->vis_date: '';
        $immu[10]['isRequired']  = 'Optional';
        $immu[10]['placeholder']  = 'Select VIS date';
        
        
        $unit_sql2 ="SELECT option_id , title FROM list_options WHERE  list_id='drug_route' order by seq";
        $res_unit2 = $db->prepare($unit_sql2);
        $db->query( "SET NAMES utf8");
        $res_unit2->execute();
        $routes2 = $res_unit2->fetchAll(PDO::FETCH_OBJ);
        if(!empty($routes2)){
            for($i=0; $i< count($routes2); $i++){
                $routes[$i]['optionid'] = $routes2[$i]->option_id;
                $routes[$i]['optionval'] = $routes2[$i]->title;
            }
        }
        
        $immu[11]['Label'] = 'Route';
        $immu[11]['Type'] = 'Listbox';
        $immu[11]['field_id'] = 'route';
        $immu[11]['SelectedValue'] = isset($layout_fields[0]->route) ? $layout_fields[0]->route: '';
        $immu[11]['Value'] = $routes;
        
        $unit_sql3 ="SELECT option_id , title FROM list_options WHERE list_id='proc_body_site' order by seq";
        $res_unit3 = $db->prepare($unit_sql3);
        $db->query( "SET NAMES utf8");
        $res_unit3->execute();
        $admin_sites2 = $res_unit3->fetchAll(PDO::FETCH_OBJ);
        if(!empty($admin_sites2)){
            for($i=0; $i< count($admin_sites2); $i++){
                $admin_sites[$i]['optionid'] = $admin_sites2[$i]->option_id;
                $admin_sites[$i]['optionval'] = $admin_sites2[$i]->title;
            }
        }
        $immu[12]['Label'] = 'Administration Site';
        $immu[12]['Type'] = 'Listbox';
        $immu[12]['field_id'] = 'administration_site';
        $immu[12]['SelectedValue'] = isset($layout_fields[0]->administration_site) ? $layout_fields[0]->administration_site: '';
        $immu[12]['Value'] = $admin_sites;
        
        $immu[13]['Label'] = 'Notes';
        $immu[13]['Type'] = 'TextArea';
        $immu[13]['field_id'] = 'note';
        $immu[13]['SelectedValue'] = isset($layout_fields[0]->note) ? $layout_fields[0]->note: '';
        $immu[13]['isRequired']  = 'Optional';
        $immu[13]['placeholder']  = 'Enter notes here';
        
//        echo "<pre>"; print_r($immu); echo "</pre>";

        if($immu)
        {
            $patientres = json_encode($immu);
            echo $patientresult = GibberishAES::enc($patientres, $apikey);
        }
        else
        {
            $patientres =  '[{"id":"0"}]';
            echo $patientresult = GibberishAES::enc($patientres, $apikey);
        }
    }
    catch(PDOException $e)
    {

        $patientres =  '{"error":{"text":'. $e->getMessage() .'}}';
        echo $patientresult = GibberishAES::enc($patientres, $apikey);
    }
}
function editPrescription($patientId,$id){
    $apikey = 'rotcoderaclla';
    // The default key size is 256 bits.
    $old_key_size = GibberishAES::size();
    GibberishAES::size(256);
    try
    {
        $db = getConnection(); 
        
        if($id != 0){
            $query = "SELECT id, patient_id, `interval`,provider_id ,filled_by_id, date_added, date_modified, encounter, start_date, drug, drug_id, rxnorm_drugcode, form, dosage, quantity, size, unit, route, substitute, refills, per_refill, filled_date, medication, note, active, site, prescriptionguid, erx_source, erx_uploaded, drug_info_erx
                FROM prescriptions
                WHERE patient_id =$patientId
                AND active =  '1'";
            
            $db->query( "SET NAMES utf8");
            $stmt_layout = $db->prepare($query);
            $stmt_layout->execute();

            $layout_fields = $stmt_layout->fetchAll(PDO::FETCH_OBJ);
//            echo "<pre>"; print_r($layout_fields); echo "</pre>";
        }


        $presc[0]['Label'] = 'Currently Active';
        $presc[0]['Type']  = 'CheckBox';
        $presc[0]['field_id']  = 'active';
        $presc[0]['SelectedValue'] = isset($layout_fields[0]->active) ? $layout_fields[0]->active: '';
        $presc[0]['isRequired']  = 'Optional';
        $presc[0]['placeholder']  = '';
        
        $date = explode('-',isset($layout_fields[0]->date_added) ? $layout_fields[0]->date_added: '');
        
        $month = array('01'=> 'January','02'=>'February','03'=>'March','04'=>'April','05'=>'May','06'=>'June','07'=>'July', '08'=> 'August', '09'=>'September','10'=>'October','11'=>'November','12'=>'December');
        $presc[1]['Label'] = 'Starting Date';
        $presc[1]['Type'] = 'DateTime';
        $presc[1]['field_id']  = 'start_date';
        $presc[1]['SelectedValue'] = isset($layout_fields[0]->date_added) ? $layout_fields[0]->date_added: '';
        $presc[1]['isRequired']  = 'Optional';
        $presc[1]['placeholder']  = 'Select starting date';
//        $presc[1]['Value'] = $month;
        
//        for($i=1;$i<32;$i++) {
//            $datearray[$i] = sprintf("%02d",$i);
//        }
//        $date = array(  1=>'01',
//                        2=>'02',
//                        3=>'03',
//                        4=>'04',
//                        5=>'05',
//                        6=>'06',
//                        7=>'07',
//                        8=>'08',
//                        9=>'09',
//                        10=>'10',
//                        11=>'11',
//                        12=>'12',
//                        13=>'13',
//                        14=>'14',
//                        15=>'15',
//                        16=>'16',
//                        17=>'17',
//                        18=>'18',
//                        19=>'19',
//                        20=>'20',
//                        21=>'21',
//                        22=>'22',
//                        23=>'23',
//                        24=>'24',
//                        25=>'25',
//                        26=>'26',
//                        27=>'27',
//                        28=>'28',
//                        29=>'29',
//                        30=>'30',
//                        31=>'31',
//                );
//        $presc[2]['Label'] = '';
//        $presc[2]['Type'] = 'Listbox';
//        $presc[1]['field_id']  = 'start_date2';
//        $presc[2]['SelectedValue'] = isset($date[2]) ? $date[2]: '';
//        $presc[2]['Value'] = $datearray;
//        for($i=2020;$i>=2005;$i--) {
//            $year[$i] = sprintf("%02d",$i);
//        }
//        $year = array(  '2005'=>'2005',
//                        '2006'=>'2006',
//                        '2007'=>'2007',
//                        '2008'=>'2008',
//                        '2009'=>'2009',
//                        '2010'=>'2010',
//                        '2011'=>'2011',
//                        '2012'=>'2012',
//                        '2013'=>'2013',
//                        '2014'=>'2014',
//                        '2015'=>'2015',
//                        '2016'=>'2016',
//                        '2017'=>'2017',
//                        '2018'=>'2018',
//                        '2019'=>'2019',
//                        '2020'=>'2020');
        
//        $presc[3]['Label'] = '';
//        $presc[3]['Type'] = 'Listbox';
//        $presc[1]['field_id']  = 'start_date3';
//        $presc[3]['SelectedValue'] = isset($date[0]) ? $date[0]: '';
//        $presc[3]['Value'] = $year;
        
        $qry = "select id, concat(fname,' ',lname) as full_name " .
                       "from users where username != '' AND active=1 AND authorized=1 " .
                       "order by concat(lname,', ',fname)";
        $stmt = $db->prepare($qry) ;
        $stmt->execute();
        $rs = $stmt->fetchAll(PDO::FETCH_OBJ);
        if(!empty($rs)){
            for($i=0; $i< count($rs); $i++){
                $usernames[$rs[$i]->id] = $rs[$i]->full_name;
            }
        }
        
        $presc[2]['Label'] = 'Provider';
        $presc[2]['Type'] = 'Listbox';
        $presc[2]['field_id'] = 'provider_id';
        $presc[2]['SelectedValue'] = isset($layout_fields[0]->provider_id) ? $layout_fields[0]->provider_id: '';
        $presc[2]['Value'] = $usernames;
        $presc[2]['isRequired']  = 'Optional';
        $presc[2]['placeholder']  = 'Select provider';
        
        $presc[3]['Label'] = 'Drug';
        $presc[3]['Type'] = 'TextBox';
        $presc[3]['field_id'] = 'drug';
        $presc[3]['SelectedValue'] = isset($layout_fields[0]->drug) ? $layout_fields[0]->drug: '';
        $presc[3]['isRequired']  = 'Required';
        $presc[3]['placeholder']  = 'Enter drug name';
        
        $presc[4]['Label'] = 'Quantity';
        $presc[4]['Type'] = 'TextBox';
        $presc[4]['field_id'] = 'quantity';
        $presc[4]['linkedfield'] = 'per_refill';
        $presc[4]['SelectedValue'] = isset($layout_fields[0]->quantity) ? $layout_fields[0]->quantity: '';
        $presc[4]['isRequired']  = 'Optional';
        $presc[4]['placeholder']  = 'Enter Quantity';
        
        $presc[5]['Label'] = 'Medicine Units';
        $presc[5]['Type'] = 'TextBox';
        $presc[5]['field_id'] = 'size';
        $presc[5]['SelectedValue'] = isset($layout_fields[0]->size) ? $layout_fields[0]->size: '';
        $presc[5]['isRequired']  = 'Optional';
        $presc[5]['placeholder']  = 'Enter Medicine Units';
        
        $unit_sql1 ="SELECT option_id , title FROM list_options WHERE list_id='drug_units' order by seq";
        $res_unit1 = $db->prepare($unit_sql1);
        $db->query( "SET NAMES utf8");
        $res_unit1->execute();
        $admin_units2 = $res_unit1->fetchAll(PDO::FETCH_OBJ);
        if(!empty($admin_units2)){
            for($i=0; $i< count($admin_units2); $i++){
                $admin_units[$admin_units2[$i]->option_id] = $admin_units2[$i]->title;
            }
        }

        $presc[6]['Label'] = '';
        $presc[6]['Type'] = 'Listbox';
        $presc[6]['field_id'] = 'unit';
        $presc[6]['SelectedValue'] = isset($layout_fields[0]->unit) ? $layout_fields[0]->unit: '';
        $presc[6]['Value'] = $admin_units;
        $presc[6]['isRequired']  = 'Optional';
        $presc[6]['placeholder']  = 'Enter Units';
        
        $presc[7]['Label'] = 'Take';
        $presc[7]['Type'] = 'TextBox';
        $presc[7]['field_id'] = 'dosage';
        $presc[7]['SelectedValue'] = isset($layout_fields[0]->dosage) ? $layout_fields[0]->dosage: '';
        $presc[7]['isRequired']  = 'Optional';
        $presc[7]['placeholder']  = 'Enter Dosage';
        
        $unit_sql3 ="SELECT option_id , title FROM list_options WHERE list_id='drug_form' order by seq";
        $res_unit3 = $db->prepare($unit_sql3);
        $db->query( "SET NAMES utf8");
        $res_unit3->execute();
        $drug_form2 = $res_unit3->fetchAll(PDO::FETCH_OBJ);
        if(!empty($drug_form2)){
            for($i=0; $i< count($drug_form2); $i++){
                $drug_form[$drug_form2[$i]->option_id] = $drug_form2[$i]->title;
            }
        }
        
        $presc[8]['Label'] = 'in';
        $presc[8]['Type'] = 'Listbox';
        $presc[8]['field_id'] = 'form';
        $presc[8]['SelectedValue'] = isset($layout_fields[0]->form) ? $layout_fields[0]->form: '';
        $presc[8]['Value'] = $drug_form;
        $presc[8]['isRequired']  = 'Optional';
        $presc[8]['placeholder']  = 'Enter form';
        
        $unit_sql4 ="SELECT option_id , title FROM list_options WHERE list_id='drug_route' order by seq";
        $res_unit4 = $db->prepare($unit_sql4);
        $db->query( "SET NAMES utf8");
        $res_unit4->execute();
        $drug_route2 = $res_unit4->fetchAll(PDO::FETCH_OBJ);
        if(!empty($drug_route2)){
            for($i=0; $i< count($drug_route2); $i++){
                $drug_route[$drug_route2[$i]->option_id] = $drug_route2[$i]->title;
            }
        }
        
        $presc[9]['Label'] = '';
        $presc[9]['Type'] = 'Listbox';
        $presc[9]['field_id'] = 'route';
        $presc[9]['SelectedValue'] = isset($layout_fields[0]->route) ? $layout_fields[0]->route: '';
        $presc[9]['Value'] = $drug_route;
        $presc[9]['isRequired']  = 'Optional';
        $presc[9]['placeholder']  = 'Enter route';
        
        $unit_sql5 ="SELECT option_id , title FROM list_options WHERE list_id='drug_interval' order by seq";
        $res_unit5 = $db->prepare($unit_sql5);
        $db->query( "SET NAMES utf8");
        $res_unit5->execute();
        $drug_interval2 = $res_unit5->fetchAll(PDO::FETCH_OBJ);
        if(!empty($drug_interval2)){
            for($i=0; $i< count($drug_interval2); $i++){
                $drug_interval[$drug_interval2[$i]->option_id] = $drug_interval2[$i]->title;
            }
        }
        
        $presc[10]['Label'] = '';
        $presc[10]['Type'] = 'Listbox';
        $presc[10]['field_id'] = 'interval';
        $presc[10]['SelectedValue'] = isset($layout_fields[0]->interval) ? $layout_fields[0]->interval: '';
        $presc[10]['Value'] = $drug_interval;
        $presc[10]['isRequired']  = 'Optional';
        $presc[10]['placeholder']  = 'Enter interval';
        
        $unit_sql3 ="SELECT option_id , title FROM list_options WHERE list_id='drug_route' order by seq";
        $res_unit3 = $db->prepare($unit_sql3);
        $db->query( "SET NAMES utf8");
        $res_unit3->execute();
        $drug_route = $res_unit3->fetchAll(PDO::FETCH_OBJ);
        if(!empty($drug_route)){
            for($i=0; $i< count($drug_route); $i++){
                $admin_sites[$drug_route[$i]->option_id] = $drug_route[$i]->title;
            }
        }
        
        for($i=0;$i<21;$i++) {
            $refills[$i] = sprintf("%02d",$i);
        }
        $presc[11]['Label'] = 'Refills';
        $presc[11]['Type'] = 'Listbox';
        $presc[11]['field_id'] = 'refills';
        $presc[11]['SelectedValue'] = isset($layout_fields[0]->refills) ? $layout_fields[0]->refills: '';
        $presc[11]['Value'] = $refills;
        $presc[11]['isRequired']  = 'Optional';
        $presc[11]['placeholder']  = 'Enter refills';
        
        $presc[12]['Label'] = '  # of tablets:';
        $presc[12]['Type'] = 'TextBox';
        $presc[12]['field_id'] = 'per_refill';
        $presc[12]['SelectedValue'] = isset($layout_fields[0]->per_refill) ? $layout_fields[0]->per_refill: '';
        $presc[12]['isRequired']  = 'Optional';
        $presc[12]['placeholder']  = 'Enter tablets for refill count';
        
        $presc[13]['Label'] = 'Notes';
        $presc[13]['Type'] = 'TextArea';
        $presc[13]['field_id'] = 'note';
        $presc[13]['SelectedValue'] = isset($layout_fields[0]->note) ? $layout_fields[0]->note: '';
        $presc[13]['isRequired']  = 'Optional';
        $presc[13]['placeholder']  = 'Enter notes here';
        
        $presc[14]['Label'] = 'Add to Medication List';
        $presc[14]['Type'] = 'Listbox';
        $presc[14]['field_id'] = 'medication';
        $presc[14]['SelectedValue'] = isset($layout_fields[0]->medication) ? $layout_fields[0]->medication: '';
        $presc[14]['Value'] = array('0'=>'No','1'=>'Yes');
        $presc[14]['isRequired']  = 'Optional';
        $presc[14]['placeholder']  = 'Add to Medication List';
        
        $presc[15]['Label'] = '';
        $presc[15]['Type'] = 'Listbox';
        $presc[15]['field_id'] = 'substitute';
        $presc[15]['SelectedValue'] = isset($layout_fields[0]->substitute) ? $layout_fields[0]->substitute: '';
        $presc[15]['Value'] = array('1'=>'substitution allowed','2'=>'do not substitute');
        
//        echo "<pre>"; print_r($presc); echo "</pre>";
        if($presc)
        {
            $patientres = json_encode($presc);
            echo $patientresult = GibberishAES::enc($patientres, $apikey);
        }
        else
        {
            $patientres =  '[{"id":"0"}]';
            echo $patientresult = GibberishAES::enc($patientres, $apikey);
        }
    }
    catch(PDOException $e)
    {

        $patientres =  '{"error":{"text":'. $e->getMessage() .'}}';
        echo $patientresult = GibberishAES::enc($patientres, $apikey);
    }
}
function saveDynamicIssues(){
    try
    {
        $db = getConnection();
        $request = Slim::getInstance()->request();
        $apikey = 'rotcoderaclla';
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        $appres = GibberishAES::dec($request->getBody(), $apikey);
//        $appres = GibberishAES::dec('U2FsdGVkX190XUmiZ4HTm8SyIAoMoI9+eGm4Dlgnk/N3kBMSQBCToHRSWQJeey3lzPdXGBwOV/h8vYjiXAW43lHb57/t36CNpwZOgNCdU/Rx6b2ZzgUPx8uEuF0nK+WBY6jbZzBHy922J78YuxrPPClG+nt1UVzGTojBZcNEKttZ6M+IIhnAHs5Jd0EAfj7+Gj5Upq82SMUCp8q9eQ/i6PMVlT1X1+K7V7IP5/VnEFnL52inhXRIyOKlsFpx3j/V4ydsRWCYJ7FIow+r3ZqryWCvqWlz/MCMEqctMc/krCSwVmbX5xkzoYsvUmXZeN7Tdg2YLu5fYEFEwDiPUvj4JWhWeHh6IzCBYVf7kPEibZH4ZUAeaC922aTJH3afs0Jri8PTFZaUBU2ARmyB8otgCY1gPf8rFl65/oUserX+vUs=', $apikey);
        $insertArray = json_decode($appres,TRUE);
//        echo "<pre>"; print_r($insertArray); echo "</pre>";

        $updatequery = '';
        $setquery2 = '';
        $setvalues2= '';
        $type       = $insertArray['type'];
        $username2  = getUserName($insertArray['loginProviderId']);
        $username   = isset($username2[0]['username'])? $username2[0]['username'] : ''; 
        foreach($insertArray as $key => $value){
            ${$key} = $value;
            if($key == 'loginProviderId')
                $keyname = 'user';
            else 
               $keyname = $key ;
            if($key == 'id') {
                $where = " WHERE $key = '".addslashes($value)."'";
            }else if($key != 'encounter'){
                if($insertArray['id'] == 0){
                    $setquery2 .= "`".$keyname."`,";
                    if($key == 'begdate' || $key == 'enddate'){ 
                        if(empty($value))  
                            $setvalues2 .= "NULL,";
                        else
                            $setvalues2 .= "'".addslashes($value )."',";;
                    }elseif($key == 'type' && $value == 'medical'){
                        $setvalues2 .= "'medical_problem',";
                    }elseif($key == 'diagnosis' ){
                        $value1 = str_replace("|", ";", $value);
                        $setvalues2 .= "'".addslashes($value1) ."',";
                    }else{
                        $setvalues2 .= "'".addslashes($value )."',";
                    }
                }else{
                    if($keyname == 'begdate' || $keyname == 'enddate'){ 
                        if(empty($value))  
                            $update_value = "NULL";
                        else
                            $update_value =" '".addslashes($value)."'";
                    }else{
                        $update_value = " '".addslashes($value)."'";
                    }
                    $updatequery .= "`".$keyname."` = ".$update_value.",";
                }
            }

        }
        $lastId = 0;
        $isnew  = '';
        if($insertArray['id'] == 0){
            $setquery = rtrim($setquery2,',');
            $setvalues = rtrim($setvalues2,',');
            $query = "INSERT INTO lists($setquery,date) VALUES($setvalues,NOW()) ";
            $checkinsert = 'insert';
            $isnew = 'New';
        }else{
            $update =  rtrim($updatequery,','). " ".$where;
            $query = "UPDATE lists SET ".$update;
            $checkinsert = 'update';
        }
        $db->query( "SET NAMES utf8");
        $stmt_layout = $db->prepare($query);
        $valuesset = 0;
        if($stmt_layout->execute()){
            insertMobileLog($checkinsert,$username,$query,$pid,$encounter,"$isnew $type  Screen",1);
            $lastId = $db->lastInsertId(); 
            $valuesset = 1;
    //        $layout_fields = $stmt_layout->fetchAll(PDO::FETCH_OBJ);
    //        if($insertArray['id'] == 0) echo $lastId = $db->lastInsertId(); 
            if($lastId != 0 && $insertArray['id'] == 0 && $encounter !=0){
                
                $checkvalue = "SELECT count(*) FROM issue_encounter WHERE pid = '$pid' AND list_id = '$lastId' AND encounter = '$encounter'";
                $db->query( "SET NAMES utf8");
                $stmt_layout2 = $db->prepare($checkvalue);
                $stmt_layout2->execute();
                $layout_fields2 = $stmt_layout2->fetchColumn(); 
                if($layout_fields2==0){
                    $query3 = " INSERT INTO issue_encounter (pid, list_id,encounter) VALUES('$pid','$lastId','$encounter')";
                    $db->query( "SET NAMES utf8");
                    $stmt_layout3 = $db->prepare($query3);
                    if($stmt_layout3->execute())
                        insertMobileLog('insert',$username,$query3,$pid,$encounter,"New $type Issue Enounter Screen",1);
                    else 
                        insertMobileLog('insert',$username,$query3,$pid,$encounter,"New $type Issue Enounter Screen- Failed",0);
                }

            }
        }else 
            insertMobileLog($checkinsert,$username,$query,$pid,$encounter,"$isnew $type  Screen- Failed",0);
    

        if($valuesset == 1){  
            $insertcheck =  '[{"id":"1"}]';
            echo GibberishAES::enc($insertcheck, $apikey);
	}else{
            $insertcheck = '[{"id":"0"}]';
            echo GibberishAES::enc($insertcheck, $apikey);
	}
    }
    catch(PDOException $e)
    {

        $patientres =  '{"error":{"text":'. $e->getMessage() .'}}';
        echo $patientresult = GibberishAES::enc($patientres, $apikey);
    }
}
function saveDynamicPrescription(){
    try
    {
        $db = getConnection();
        $request = Slim::getInstance()->request();
        $apikey = 'rotcoderaclla';
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        $appres = GibberishAES::dec($request->getBody(), $apikey);
//      $appres = GibberishAES::dec('U2FsdGVkX19ZWnsUhT21KP/vtCdmBwLAtpAN72SsSjRUokXJcS34RaEavHVJZWGBg0qbfXpingQLWaXhDedpHRlzRWvl8F/bIouUN60XqkvXbrPz4eoRW/8+bYaR7YugyBP+Ajn5XjcTOByxtFQIPdgglkhYtLn9EfZdkbR2T8tADblDZqVY1FWNrs9GGjRvpAYcfCqc4ZKvmx9TrlLPqSwF22o2qtB6L6uU4rX+p66Kkec0fromzNMzExIr6cZ705qLQ/WllPqDBKWOJvckr4ELPhq7FDDYlgMi89ps9Gj3ZPvQnVfxSQYs3qMcHHgMh5s+a5fS1XXJoLwiq6LcrEgGMQ6jJxMPyEpLWiEAsLQHtjNO6IpV3YTAvBWTpxjL1NbjOw0cUtwPtyI5Qb61Mw==', $apikey);
        $insertArray = json_decode($appres,TRUE);
//        echo "<pre>"; print_r($insertArray); echo "</pre>";

        $updatequery = '';
        $setquery2 = '';
        $setvalues2= '';
        $username2                  = getUserName($insertArray['loginProviderId']);
        $username                   = isset($username2[0]['username'])? $username2[0]['username'] : ''; 
        foreach($insertArray as $key => $value){
            ${$key} = $value;
            if($key == 'loginProviderId')
                $keyname = 'user';
            else 
               $keyname = $key ;
            if($key == 'id') {
                $where = " WHERE $key = '$value'";
            }else {
                if($insertArray['id'] == 0){
                    $setquery2 .= "`".$keyname."`,";
                    $setvalues2 .= "'".addslashes($value) ."',";
                }else{
                    $updatequery .= "`".$keyname."` ='".addslashes($value)."',";
                }
            }

        }
        if($insertArray['id'] == 0){
            $setquery = rtrim($setquery2,',');
            $setvalues = rtrim($setvalues2,',');
            $query = "INSERT INTO prescriptions($setquery,date_added) VALUES($setvalues, NOW()) ";
            $checkinsert = 'insert';
        }else{
            $update =  rtrim($updatequery,','). " ".$where;
            $query = "UPDATE prescriptions SET date_modified = NOW(),".$update ." ";
            $checkinsert = 'update';
        }
        $db->query( "SET NAMES utf8");
        $stmt_layout = $db->prepare($query);
   

        if($stmt_layout->execute()){  
            $insertcheck =  '[{"id":"1"}]';
            echo GibberishAES::enc($insertcheck, $apikey);
            insertMobileLog($checkinsert,$username,$query,$patient_id,'',"Prescriptions  Screen",1);
	}else{
            $insertcheck = '[{"id":"0"}]';
            echo GibberishAES::enc($insertcheck, $apikey);
            insertMobileLog($checkinsert,$username,$query,$patient_id,'',"Prescriptions Screen- Failed",0);
	}
    }
    catch(PDOException $e)
    {

        $patientres =  '{"error":{"text":'. $e->getMessage() .'}}';
        echo $patientresult = GibberishAES::enc($patientres, $apikey);
        insertMobileLog('insert/update',$username,$patientres,$patient_id,'',"Prescriptions Screen - Query Failed",0);
    }
}
function saveDynamicImmunization(){
    try
    {
        $db = getConnection();
        $request = Slim::getInstance()->request();
        $apikey = 'rotcoderaclla';
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        $appres = GibberishAES::dec($request->getBody(), $apikey);
//        $appres = GibberishAES::dec('U2FsdGVkX19CK1JIFgWSV+7a3HXGD0b6a6sTM4aC62mQi0Flb7bRatu57G85wWqFpP6kAoRwbuwg0YW2ZOr385eD9JFDAOBZ2A7/FMcKJSd2Psilw12/yIbnZMWAXNFxHuIboVDTtX0C+mayiyJrWttUk7uI1gZtYnZEe6tvBsIA6PheBj03It5FqIgD5uiCJAxdbI/rYh2r+f/A7T4zYnwzd8ShK0r7bT/VMBfJQi440S+LmoVeyGxkrrP5/Lf/0SYtfcf3Ue2cDZavyJmoZKY27Fa8wLVVdC6SpNRba2G3v5cZ/SwOtvA/CMz7qnplDnXufUtIjbzVwcYyr+zQQxh/OKwAl2fiLHZwtCB5ztY=', $apikey);
        $insertArray = json_decode($appres,TRUE);
//        echo "<pre>"; print_r($insertArray); echo "</pre>";

        $updatequery = '';
        $setquery2 = '';
        $setvalues2= '';
        $username2                  = getUserName($insertArray['loginProviderId']);
        $username                   = isset($username2[0]['username'])? $username2[0]['username'] : ''; 
        foreach($insertArray as $key => $value){
            ${$key} = $value;
//            if($key == 'loginProviderId')
//                $keyname = $key;
//            else 
               $keyname = $key ;
            if($key == 'id') {
                $where = " WHERE $key = '$value'";
            }else if($key == 'encounter' || $key == 'loginProviderId'){
            
            }else {
                if($insertArray['id'] == 0){
                    $setquery2 .= $keyname.",";
                    $setvalues2 .= "'".$value ."',";
                }else{
                    $updatequery .= $keyname." ='".$value."',";
                }
            }

        }
        if($insertArray['id'] == 0 && $encounter != 0){
            $setquery = rtrim($setquery2,',');
            $setvalues = rtrim($setvalues2,',');
            $query = "INSERT INTO immunizations($setquery) VALUES($setvalues) ";
            $checkinsert = 'insert';
            
        }else{
            $update =  rtrim($updatequery,','). " ".$where;
            $query = "UPDATE immunizations SET ".$update;
            $checkinsert = 'update';
        }
        $db->query( "SET NAMES utf8");
        $stmt_layout = $db->prepare($query);

        if($stmt_layout->execute()){  
            $insertcheck =  '[{"id":"1"}]';
            echo GibberishAES::enc($insertcheck, $apikey);
            insertMobileLog($checkinsert,$username,$query,$patient_id,'',"Immunization  Screen",1);
	}else{
            $insertcheck = '[{"id":"0"}]';
            echo GibberishAES::enc($insertcheck, $apikey);
            insertMobileLog($checkinsert,$username,$query,$patient_id,'',"Immunization Screen- Failed",0);
	}
    }
    catch(PDOException $e)
    {

        $patientres =  '{"error":{"text":'. $e->getMessage() .'}}';
        echo $patientresult = GibberishAES::enc($patientres, $apikey);
        insertMobileLog('insert/update',$username,$patientres,$patient_id,'',"Immunization Screen - Query Failed",0);
    }
}
function deleteIssue($pid,$id,$type,$username,$eid){
    $apikey = 'rotcoderaclla';
    // The default key size is 256 bits.
    $old_key_size = GibberishAES::size();
    GibberishAES::size(256);
    try
    {
        $db = getConnection();
        $username = $username;
        $issues = array();
        $setted = 0;
        if($type == 'allergy' || $type == 'surgery' || $type == 'medication'|| $type == 'medical_problem' || $type == 'DME' || $type == 'dental'){
            $query = "DELETE FROM lists WHERE id = $id AND pid = $pid AND type='$type'";
        }else if($type=='prescriptions'){
             $query = "DELETE FROM prescriptions WHERE id = $id AND patient_id = $pid";
        }elseif($type=='immunizations'){
            $query = "DELETE FROM immunizations WHERE id = $id AND patient_id = $pid";
        }
        $stmt_layout = $db->prepare($query);
        if($stmt_layout->execute()){  
            $insertcheck =  '[{"id":"1"}]';
            echo GibberishAES::enc($insertcheck, $apikey);
            insertMobileLog('delete',$username,$query,$pid,$eid,"DELETE $type  Screen",1);
	}else{
            $insertcheck = '[{"id":"0"}]';
            echo GibberishAES::enc($insertcheck, $apikey);
            insertMobileLog('delete',$username,$query,$pid,$eid,"DELETE $type  Screen",0);
	}
    }
    catch(PDOException $e)
    {

        $patientres =  '{"error":{"text":'. $e->getMessage() .'}}';
        echo $patientresult = GibberishAES::enc($patientres, $apikey);
        insertMobileLog('delete',$username,$patientres,$pid,$eid,"DELETE $type Screen - Query Failed",0);
    }    
}
function deleteAddressbook($id,$username){
    $apikey = 'rotcoderaclla';
    // The default key size is 256 bits.
    $old_key_size = GibberishAES::size();
    GibberishAES::size(256);
    try
    {
        $db = getConnection();
        $issues = array();
        $setted = 0;
        $query = "DELETE FROM users WHERE id = $id ";
        
        $stmt_layout = $db->prepare($query);
        if($stmt_layout->execute()){  
            $insertcheck =  '[{"id":"1"}]';
            echo GibberishAES::enc($insertcheck, $apikey);
            insertMobileLog('delete',$username,$query,'','',"DELETE Addressbook Screen",1);
	}else{
            $insertcheck = '[{"id":"0"}]';
            echo GibberishAES::enc($insertcheck, $apikey);
            insertMobileLog('delete',$username,$query,'','',"DELETE Addressbook Screen",0);
	}
    }
    catch(PDOException $e)
    {

        $patientres =  '{"error":{"text":'. $e->getMessage() .'}}';
        echo $patientresult = GibberishAES::enc($patientres, $apikey);
        insertMobileLog('delete',$username,$patientres,'','',"DELETE Addressbook Screen - Query Failed",0);
    }    
}
function deleteAddressbookCred($id,$username){
    $apikey = 'rotcoderaclla';
    // The default key size is 256 bits.
    $old_key_size = GibberishAES::size();
    GibberishAES::size(256);
    try
    {
        $db = getConnection();
        $issues = array();
        $setted = 0;
        $query = "DELETE FROM tbl_user_cred WHERE id = $id ";
        
        $stmt_layout = $db->prepare($query);
        if($stmt_layout->execute()){  
            $insertcheck =  '[{"id":"1"}]';
            echo GibberishAES::enc($insertcheck, $apikey);
            insertMobileLog('delete',$username,$query,'','',"DELETE Addressbook Credentials",1);
	}else{
            $insertcheck = '[{"id":"0"}]';
            echo GibberishAES::enc($insertcheck, $apikey);
            insertMobileLog('delete',$username,$query,'','',"DELETE Addressbook Credentials - Failed",0);
	}
    }
    catch(PDOException $e)
    {

        $patientres =  '{"error":{"text":'. $e->getMessage() .'}}';
        echo $patientresult = GibberishAES::enc($patientres, $apikey);
        insertMobileLog('delete',$username,$patientres,'','',"DELETE Addressbook Credentials Screen - Query Failed",0);
    }    
}
function deleteAddressbookContact($id,$username){
    $apikey = 'rotcoderaclla';
    // The default key size is 256 bits.
    $old_key_size = GibberishAES::size();
    GibberishAES::size(256);
    try
    {
        $db = getConnection();
        $issues = array();
        $setted = 0;
        $query = "DELETE FROM tbl_user_abookcontact  WHERE id = $id ";
        
        $stmt_layout = $db->prepare($query);
        if($stmt_layout->execute()){  
            $insertcheck =  '[{"id":"1"}]';
            echo GibberishAES::enc($insertcheck, $apikey);
            insertMobileLog('delete',$username,$query,'','',"DELETE Addressbook Contacts",1);
	}else{
            $insertcheck = '[{"id":"0"}]';
            echo GibberishAES::enc($insertcheck, $apikey);
            insertMobileLog('delete',$username,$query,'','',"DELETE Addressbook Contacts - Failed",0);
	}
    }
    catch(PDOException $e)
    {

        $patientres =  '{"error":{"text":'. $e->getMessage() .'}}';
        echo $patientresult = GibberishAES::enc($patientres, $apikey);
        insertMobileLog('delete',$username,$patientres,'','',"DELETE Addressbook Contacts - Query Failed",0);
    }    
}
function deleteAgency($id,$username){
    $apikey = 'rotcoderaclla';
    // The default key size is 256 bits.
    $old_key_size = GibberishAES::size();
    GibberishAES::size(256);
    try
    {
        $db = getConnection();
        $issues = array();
        $setted = 0;
        $query = "DELETE FROM tbl_patientagency WHERE id = $id ";
        
        $stmt_layout = $db->prepare($query);
        if($stmt_layout->execute()){  
            $insertcheck =  '[{"id":"1"}]';
            echo GibberishAES::enc($insertcheck, $apikey);
            insertMobileLog('delete',$username,$query,'','',"DELETE Patient Agency Screen",1);
	}else{
            $insertcheck = '[{"id":"0"}]';
            echo GibberishAES::enc($insertcheck, $apikey);
            insertMobileLog('delete',$username,$query,'','',"DELETE Patient Agency Screen - Failed",0);
	}
    }
    catch(PDOException $e)
    {

        $patientres =  '{"error":{"text":'. $e->getMessage() .'}}';
        echo $patientresult = GibberishAES::enc($patientres, $apikey);
        insertMobileLog('delete',$username,$patientres,'','',"DELETE Patient Agency Screen - Query Failed",0);
    }    
}
function getAllUsers(){
    $apikey = 'rotcoderaclla';
    // The default key size is 256 bits.
    $old_key_size = GibberishAES::size();
    GibberishAES::size(256);
    try
    {
        $db = getConnection();

        $query = "SELECT id, concat(fname,' ', lname) as name FROM users  WHERE username <> '' AND active = 1 ";
        
        $stmt_layout = $db->prepare($query);
        $stmt_layout->execute();
        $get_users = $stmt_list->fetchAll(PDO::FETCH_OBJ);
        if($get_users){  
            $insertcheck =  json_encode($get_users);
            echo GibberishAES::enc($insertcheck, $apikey);
	}else{
            $insertcheck = '[{"id":"0"}]';
            echo GibberishAES::enc($insertcheck, $apikey);
	}
    }
    catch(PDOException $e)
    {

        $patientres =  '{"error":{"text":'. $e->getMessage() .'}}';
        echo $patientresult = GibberishAES::enc($patientres, $apikey);
    }    
}
function addListOptions($option_id,$list_id,$username){
    $apikey = 'rotcoderaclla';
    // The default key size is 256 bits.
    $old_key_size = GibberishAES::size();
    GibberishAES::size(256);
    try
    {
        $db = getConnection();
        
        $get_list_option = "SELECT option_id FROM list_options WHERE list_id = '$list_id' AND option_id = '$option_id'";
        $stmt_layout_list = $db->prepare($get_list_option);
        $stmt_layout_list->execute();
        $checklistoption = $stmt_layout_list->fetchAll(PDO::FETCH_OBJ);
        
        if(empty($checklistoption)){
            $get_max_seq = "SELECT MAX(seq) as seq FROM list_options WHERE list_id = '$list_id'";
            $stmt_layout = $db->prepare($get_max_seq);
            $stmt_layout->execute();
            $set_seq = $stmt_layout->fetchAll(PDO::FETCH_OBJ); 
            if(!empty($set_seq))
                $seq = $set_seq[0]->seq+1;
            else
                $seq = 1;

            $option = str_replace('_',' ',$option_id);
            $query2 = "INSERT INTO list_options (option_id,title,list_id,seq) VALUES ('$option_id','$option','$list_id','$seq')";
            $db->query( "SET NAMES utf8"); 
            $stmt_layout2 = $db->prepare($query2);
            if($stmt_layout2->execute()){  
                $insertcheck =  '[{"id":"1"}]';
                echo GibberishAES::enc($insertcheck, $apikey);
                insertMobileLog('insert',$username,$query2,'','',"Insert New Option in $list_id List Screen",1);
            }else{
                $insertcheck = '[{"id":"0"}]';
                echo GibberishAES::enc($insertcheck, $apikey);
                insertMobileLog('insert',$username,$query2,'','',"Insert New Option in $list_id List Screen - Failed",0);
            }
        }else{
            $insertcheck = '{"error":{"text":"Duplicate Entry. Option '. $option_id .' already existed"},"value":"'. $option_id .'"}';
            echo GibberishAES::enc($insertcheck, $apikey);
            insertMobileLog('insert',$username,$insertcheck,'','',"Insert New Option in $list_id List Screen - Duplicate",0);
        }
    }
    catch(PDOException $e)
    {

        $patientres =  '{"error":{"text":'. $e->getMessage() .'}}';
        echo $patientresult = GibberishAES::enc($patientres, $apikey);
        insertMobileLog('insert',$username,$patientres,'','',"Insert New Option in $list_id List Screen - Query Failed",0);
    }  
}
function getUserName($loginProvderId)
{
    $db = getConnection();
    $qry = "SELECT username FROM users WHERE id=:loginProvderId";
    $stmt = $db->prepare($qry) ;
    $stmt->bindParam("loginProvderId", $loginProvderId);            
    $stmt->execute();
    $rs = $stmt->fetchAll();
    return $rs;
}
function addLBFReview(){
    $apikey = 'rotcoderaclla';
    // The default key size is 256 bits.
    $old_key_size = GibberishAES::size();
    GibberishAES::size(256);
    try
    {
        $db = getConnection();
        
        $request = Slim::getInstance()->request();
        $apikey = 'rotcoderaclla';
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        $appres = GibberishAES::dec($request->getBody(), $apikey);
//        $appres = GibberishAES::dec('U2FsdGVkX1/0k9If1eSlXsZosJQNRPzNPI03Mttd67FoZz6h8PjqHIUgoRh2ptJOFytOumCOpEWFiIpf3oLuVUuRkc/4yd/bxx+tREoPw7Jlc2jmYE0bV1aqxvs22WzBW1L3hXK/4S2UNl3xqTbBJmh5hY9/nMjsFSo7zTEX10xrkmc7HuLLIe73Ln5dkfKT5d5zQV0DdqgBD2pmvU5B0qcfs+Dt0P3PCs09da6KLlPlmWmfXAW0XYOquolH4IQxuik/xXDHh1rcbkvmW2A1DknLUZ//MPCStqvQ4JzRnK4nB4tFn+MnEtbkJhwXcTycWwyDHe4mqB/wDtXSjWEFbgCqz13l1/f9XJlvJpSjO7E=', $apikey);
        $insertArray = json_decode($appres,TRUE);
        
        $data = 0;
        
//        echo "<pre>"; print_r($insertArray); echo "</pre>";
        $form_id    = $insertArray['form_id'];
        $pid        = $insertArray['pid'];
        $group      = $insertArray['group'];
        $form       = $insertArray['form'];
        $user       = $insertArray['user'];
        $username2  = getUserName($insertArray['user']);
        $username   = isset($username2[0]['username'])? $username2[0]['username'] : ''; 
        $encounter  = $insertArray['encounter'];
        
        if(empty($form_id)){
            $lastformid2 = "SELECT MAX(form_id) as forms FROM lbf_data";
            $stmt5 = $db->prepare($lastformid2) ;
            $stmt5->execute();
            $maxformidval2 = $stmt5->fetchAll(PDO::FETCH_OBJ);
            $form_id =  $maxformidval2[0]->forms + 1;
            
            $insertform2 = "INSERT INTO forms (date,encounter, form_name, form_id, pid, user, groupname, authorized, deleted, formdir) VALUES (NOW(), $encounter, 'Allcare Encounter Forms', $form_id, $pid, '$username', 'Default', 1, 0, 'LBF2' )";
            $stmt4 = $db->prepare($insertform2);
            if($stmt4->execute())
                insertMobileLog('insert',$username,$insertform2,$pid,$encounter,"New LBF form FROM Review Screen($group)",1);
            else 
                insertMobileLog('insert',$username,$insertform2,$pid,$encounter,"New LBF form FROM Review ($group)Screen - Failed",1);
//            $form_id = $db->lastInsertId();
            
        }
        if(!empty($insertArray['data'][0])){
            foreach($insertArray['data'][0] as $akey => $avalue){
                if (count($db->query("SELECT * FROM lbf_data WHERE form_id = $form_id AND field_id = '$akey'")->fetchAll())==0) {
                    $sql2 = "INSERT into lbf_data (form_id, field_id, field_value) VALUES ($form_id,'$akey','".addslashes($avalue)."')";
                    $check = 'insert';
                } else {
                   $sql2 = "UPDATE lbf_data SET field_value = '".addslashes($avalue)."' WHERE field_id ='$akey'  AND form_id = $form_id";
                   $check = 'update';
                }

                $stmt4 = $db->prepare($sql2);
                if($stmt4->execute()){
                    $data = 1;
                    insertMobileLog($check,$username,$sql2,$pid,$encounter,"Save LBF Data($group) FROM Review Screen",1);
                }else{
                    $data = 0;
                    insertMobileLog($check,$username,$sql2,$pid,$encounter,"Save LBF Data($group) FROM Review Screen - Failed",1);
                }
            }
        }
        if($data == 1){  
            $insertcheck =  '[{"id":"1"}]';
            echo GibberishAES::enc($insertcheck, $apikey);
	}else{
            $insertcheck = '[{"id":"0"}]';
            echo GibberishAES::enc($insertcheck, $apikey);
	}
    }
    catch(PDOException $e)
    {

        $patientres =  '{"error":{"text":'. $e->getMessage() .'}}';
        echo $patientresult = GibberishAES::enc($patientres, $apikey);
        insertMobileLog('insert/update',$username,$patientres,$pid,$encounter,"Save LBF Data($group) FROM Review Screen - Query Failed",1);
    }  
}
function editDemographics($patientId,$group_name){
    $apikey = 'rotcoderaclla';
    // The default key size is 256 bits.
    $old_key_size = GibberishAES::size();
    GibberishAES::size(256);
    try
    {
        $db = getConnection();
        
        $patientsreminder = array();
        $patientsreminder = editLayoutFunction($patientId,'DEM',$group_name);
        

//        echo "<pre>"; print_r($patientsreminder); echo "</pre>";
        if($patientsreminder){  
            $insertcheck =  json_encode($patientsreminder);
            echo GibberishAES::enc($insertcheck, $apikey);
	}else{
            $insertcheck = '[{"id":"0"}]';
            echo GibberishAES::enc($insertcheck, $apikey);
	}
    }
    catch(PDOException $e)
    {

        $patientres =  '{"error":{"text":'. $e->getMessage() .'}}';
        echo $patientresult = GibberishAES::enc($patientres, $apikey);
    }  
}
function editLayoutFunction ($patientId,$form_name,$group_name){
    $db = getConnection();
    $get_layout2 = "SELECT group_name,list_id, title,field_id,form_id,
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
                                END as isRequired,description,max_length FROM layout_options WHERE form_id = '$form_name' AND uor <> 0 AND group_name LIKE '%$group_name' order by group_name, seq";
    $db->query( "SET NAMES utf8");
    $stmt_layout2 = $db->prepare($get_layout2) ;
    $stmt_layout2->execute();                       
    $layout_fields2 = $stmt_layout2->fetchAll(PDO::FETCH_OBJ);
    if(!empty($layout_fields2)){
//                $patientsreminder[substr($layout_fields2[0]->group_name,1)]['form_id'] = $setform_id;
        for($i=0; $i< count($layout_fields2); $i++){
            if(!empty($layout_fields2[$i]->field_type)){
                if(strpos($layout_fields2[$i]->field_id,'em_',0) === false){
                    $sql = "select ".$layout_fields2[$i]->field_id." from patient_data where pid  = '$patientId' ";   
                }else {
                    $sql ="SELECT ". str_replace("em_","",$layout_fields2[$i]->field_id)." FROM employer_data where pid = $patientId ";
                }
                $db->query( "SET NAMES utf8");
                $stmt = $db->prepare($sql) ;
                $stmt->execute();                       

                $patientsreminders = $stmt->fetchAll(PDO::FETCH_NUM);   
                $pvalue = isset($patientsreminders[0][0])? $patientsreminders[0][0]: '';

                $patientsreminder[substr($layout_fields2[$i]->group_name,1)][$i]['field_id'] = $layout_fields2[$i]->field_id;
                $patientsreminder[substr($layout_fields2[$i]->group_name,1)][$i]['field_type'] =  $layout_fields2[$i]->field_type;
                $patientsreminder[substr($layout_fields2[$i]->group_name,1)][$i]['isRequired'] =  $layout_fields2[$i]->isRequired;
                $patientsreminder[substr($layout_fields2[$i]->group_name,1)][$i]['label']  = $layout_fields2[$i]->title;
                $patientsreminder[substr($layout_fields2[$i]->group_name,1)][$i]['list_id']  = $layout_fields2[$i]->list_id;
                $patientsreminder[substr($layout_fields2[$i]->group_name,1)][$i]['placeholder']  = $layout_fields2[$i]->description;
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
                        $titles[$k]['option_id'] = $settitle2[$k]->option_id;
                        $titles[$k]['option_value'] = $settitle2[$k]->title;
                    }
                }
                $patientsreminder[substr($layout_fields2[$i]->group_name,1)][$i]['options']  = $titles;
                if($layout_fields2[$i]->list_id != ''){
                    $patientsreminder[substr($layout_fields2[$i]->group_name,1)][$i]['value'] = $pvalue;
                }else{
                    if($layout_fields2[$i]->field_type == 'Static Text'){
                    $patientsreminder[substr($layout_fields2[$i]->group_name,1)][$i]['value'] = $layout_fields2[$i]->description;
                }else if($layout_fields2[$i]->field_type == 'Providers' || $layout_fields2[$i]->field_type == 'Providers NPI'){
                    $sql3 = "SELECT id, CONCAT(fname, ' ',lname) as name FROM `users`  WHERE active=1 AND authorized=1 ORDER BY fname,lname ";
                    $db->query( "SET NAMES utf8"); 
                    $stmt3 = $db->prepare($sql3) ;
                    $stmt3->execute();                       
                    $getListData = $stmt3->fetchAll(PDO::FETCH_OBJ);  
                    if(!empty($getListData)):
                        $data2 = array();
                        $j=0;
                        foreach($getListData as $list){
                            $data2[$j]['option_id'] = $list->id;
                            $data2[$j]['option_value'] = $list->name;
                            $j++;
                        }
                    else:
                        $data2 = '';
                    endif;

                    $patientsreminder[substr($layout_fields2[$i]->group_name,1)][$i]['options']  = $data2;
                    $patientsreminder[substr($layout_fields2[$i]->group_name,1)][$i]['value'] = $pvalue;
                }else if($layout_fields2[$i]->field_type == 'Pharmacies'){
                    $sql3 = "SELECT id, name FROM `pharmacies`   ORDER BY name";
                    $db->query( "SET NAMES utf8"); 
                    $stmt3 = $db->prepare($sql3) ;
                    $stmt3->execute();                       
                    $getListData = $stmt3->fetchAll(PDO::FETCH_OBJ);  
                    if(!empty($getListData)):
                        $data2 = array();
                        $j=0;
                        foreach($getListData as $list){
                            $data2[$j]['option_id'] = $list->id;
                            $data2[$j]['option_value'] = $list->name;
                            $j++;
                        }
                    else:
                        $data2 = '';
                    endif;

                    $patientsreminder[substr($layout_fields2[$i]->group_name,1)][$i]['options']  = $data2;
                    $patientsreminder[substr($layout_fields2[$i]->group_name,1)][$i]['value'] = $pvalue;
                }else if($layout_fields2[$i]->field_type == 'Organizations'){
                    $sql3 = "SELECT id, fname, lname, organization, username FROM users WHERE active = 1 AND ( info IS NULL OR info NOT LIKE '%Inactive%' ) AND ( username = '' OR authorized = 1 ) ORDER BY organization, lname, fname";
                    $db->query( "SET NAMES utf8"); 
                    $stmt3 = $db->prepare($sql3) ;
                    $stmt3->execute();                       
                    $getListData = $stmt3->fetchAll(PDO::FETCH_OBJ);  
                    if(!empty($getListData)):
                        $data2 = array();
                        $j=0;
                        foreach($getListData as $list){
                            $uname = $list->organization;
                            if (empty($uname) || substr($uname, 0, 1) == '(') {
                                $uname = $list->lname;
                                if ($list->fname) $uname .= ", " . $list->fname;
                            }
                            $data2[$j]['option_id'] = $list->id;
                            $data2[$j]['option_value'] = $uname;
                            $j++;
                        }
                    else:
                        $data2 = '';
                    endif;

                    $patientsreminder[substr($layout_fields2[$i]->group_name,1)][$i]['options']  = $data2;
                    $patientsreminder[substr($layout_fields2[$i]->group_name,1)][$i]['value'] = $pvalue;
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
                    $patientsreminder[substr($layout_fields2[$i]->group_name,1)][$i]['value'] = $data2;
                }else if($layout_fields2[$i]->field_type == 'Lifestyle status'){
                    $patientsreminder[substr($layout_fields2[$i]->group_name,1)][$i]['value'] = $pvalue;
                }else if($layout_fields2[$i]->field_type == 'Facilities'){
                    $sql3 = "SELECT id, name FROM facility ORDER BY name";
                    $db->query( "SET NAMES utf8"); 
                    $stmt3 = $db->prepare($sql3) ;
                    $stmt3->execute();                       
                    $getListData = $stmt3->fetchAll(PDO::FETCH_OBJ);  
                    if(!empty($getListData)):
                        $data2 = array();
                        $j=0;
                        foreach($getListData as $list){
                            $data2[$j]['option_id'] = $list->id;
                            $data2[$j]['option_value'] = $list->name;
                            $j++;
                        }
                    else:
                        $data2 = '';
                    endif;

                    $patientsreminder[substr($layout_fields2[$i]->group_name,1)][$i]['options']  = $data2;
                    $patientsreminder[substr($layout_fields2[$i]->group_name,1)][$i]['value'] = $pvalue;
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
                        $j=0;
                        foreach($getListData as $list){
                            $data2[$j]['option_id'] = $list->date;
                            $data2[$j]['option_value'] = $list->date;
                            $j++;
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
                        $j=0;
                        foreach($getListData as $list){
                            $data2[$j]['option_id'] = $list->id;
                            $data2[$j]['option_value'] = $list->name;
                            $j++;
                        }
                    else:
                        $data2 = '';
                    endif;

                    $patientsreminder[substr($layout_fields2[$i]->group_name,1)][$i]['options']  = $data2;
                    $patientsreminder[substr($layout_fields2[$i]->group_name,1)][$i]['value'] = $pvalue;
                }else if($layout_fields2[$i]->field_type == 'Users'){
                    $sql3 = "SELECT id, CONCAT(fname, ' ',lname) as name FROM `users`  WHERE username <> '' ORDER BY fname,lname ";
                    $db->query( "SET NAMES utf8"); 
                    $stmt3 = $db->prepare($sql3) ;
                    $stmt3->execute();                       
                    $getListData = $stmt3->fetchAll(PDO::FETCH_OBJ);  
                    if(!empty($getListData)):
                        $data2 = array();
                        $j=0;
                        foreach($getListData as $list){
                            $data2[$j]['option_id'] = $list->id;
                            $data2[$j]['option_value'] = $list->name;
                            $j++;
                        }
                    else:
                        $data2 = '';
                    endif;

                    $patientsreminder[substr($layout_fields2[$i]->group_name,1)][$i]['options']  = $data2;
                    $patientsreminder[substr($layout_fields2[$i]->group_name,1)][$i]['value'] = $pvalue;
                }else{
                    $patientsreminder[substr($layout_fields2[$i]->group_name,1)][$i]['value'] = $pvalue;
                }
                }
            }
        }
    }
    return $patientsreminder;
}
function saveDemographics(){
    try
    {
        $db = getConnection();
        $request = Slim::getInstance()->request();
        $apikey = 'rotcoderaclla';
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        $appres = GibberishAES::dec($request->getBody(), $apikey);
//        $appres = GibberishAES::dec("U2FsdGVkX195ufiFMQBaibrXZLideNCE8yCzEAHW/8ceGHBfSOz9fDZld9Hc5TUaKSRfm+Fn3b0BpxssKvy/XD7uuFcqM+Xkw4ZjMUsfuY9awIJQonMhHEXCsiFtw3xbHRcO8KzZI4P2bBkVn9woJu2ufT6zfkpGxhvQEj35rbgyHI2quiS3JJQS6YkaL0FozbcBXOV2RnDMCizNOVAGrsUBmqF5FilV+pyL/k40TAtpRIA1Kb2YK/LzIRr9DAjeDuIZWsevHf7vLisOYYW3wmfdnT/CvYnxNmeR1V24MmXCJgvCV2N+vpcalwvWA5SS001dZE/rFZqy91hgokJ8NJY2+pNk6GYSoa+9gRkCk6tH7f2jjPoKX2fSYPNdgXu+B01KZ+NDGgF/OTy4jUiT2G7h7cAjjDuLrVC20Fh6vHA=", $apikey);
        $insertArray = json_decode($appres,TRUE);
//        echo "<pre>"; print_r($insertArray); echo "</pre>";
        $username       = $insertArray['user'];
        $group_name     = $insertArray['group_name'];
        $pid            = $insertArray['pid'];
        
        
        $data1 = $data2 = 0;
        $update_patient_data = $update_em_patient_data  = $insert_em_data = $insert_em_data_values2 = '';
        $fieldnames = "SELECT field_id
                        FROM layout_options WHERE form_id='DEM' AND uor <> 0 AND group_name LIKE'%$group_name' ORDER BY seq";
        $stmt2 = $db->prepare($fieldnames); 
        $stmt2->execute();
        $fieldnamesresult = $stmt2->fetchAll(PDO::FETCH_OBJ);

        if(!empty($fieldnamesresult)){
            for($i=0; $i< count($fieldnamesresult); $i++){
                foreach($fieldnamesresult[$i] as $fkey => $fvalue){
                    if($fkey !== 'pid'){
                        if(strpos($fvalue,'em_',0) === false){
                            $update_patient_data .= "`".$fvalue."`= '". addslashes($insertArray['data'][0][$fvalue])."',";
                        }else{
                            $update_em_patient_data .= "`".str_replace("em_","",$fvalue)."`= '". addslashes($insertArray['data'][0][$fvalue])."',";
                            $insert_em_data .= "`".str_replace("em_","",$fvalue)."`,";
                            $insert_em_data_values2 .= "'".addslashes($insertArray['data'][0][$fvalue])."',";
                        }    
                        ${$fvalue} = $insertArray['data'][0][$fvalue];
                    }
                }
            }
            if(!empty($update_patient_data)){
                $update_patient_data = rtrim($update_patient_data,',');
                $query = "  UPDATE patient_data SET $update_patient_data WHERE pid = $pid ";
                $stmtquery = $db->prepare($query); 
                if($stmtquery->execute()){
                    $data1 = 1;
                    insertMobileLog('update',"$username",$query,$pid,'',"Edit Patient Data($group_name) Screen", 1);
                }else{
                    insertMobileLog('update',"$username",$query,$pid,'',"Edit Patient Data($group_name) Screen - Failed", 0);
                }
            }else{
                $data1 = 1;
            }
            if(!empty($update_em_patient_data)){
                $update_em_patient_data = rtrim($update_em_patient_data,',');
                $check_employer = "SELECT * FROM employer_data where pid = $pid";
                $stmtqueryemp = $db->prepare($check_employer); 
                $stmtqueryemp->execute();
                $empfields = $stmtqueryemp->fetchAll(PDO::FETCH_OBJ);
                if(!empty($empfields)){
                    $query2 = "  UPDATE employer_data SET $update_em_patient_data WHERE pid = $pid ";
                    $stmtquery2 = $db->prepare($query2); 
                    if($stmtquery2->execute()){
                        $data2 = 1;
                        insertMobileLog('update',"$username",$query,$pid,'',"Edit Employer Data($group_name) Screen", 1);
                    }else{
                        insertMobileLog('update',"$username",$query,$pid,'',"Edit Employer Data($group_name) Screen - Failed", 0);
                    }
                }else{
                    $query2 = "  INSERT INTO employer_data ($insert_em_data `pid`) VALUES ($insert_em_data_values2 '$pid')";
                    $stmtquery2 = $db->prepare($query2); 
                    if($stmtquery2->execute()){
                        $data2 = 1;
                        insertMobileLog('insert',"$username",$query,$pid,'',"Insert Employer Data($group_name) Screen", 1);
                    }else{
                        insertMobileLog('insert',"$username",$query,$pid,'',"Insert Employer Data($group_name) Screen - Failed", 0);
                    }
                }
            } else {
                $data2 = 1;
            }
           
        }
        if($data1 == 1 && $data2 == 1){  
            $insertcheck =  '[{"id":"1"}]';
            echo GibberishAES::enc($insertcheck, $apikey);
	}else{
            $insertcheck = '[{"id":"0"}]';
            echo GibberishAES::enc($insertcheck, $apikey);
	}
    }
    catch(PDOException $e)
    {

        $patientres =  '{"error":{"text":'. $e->getMessage() .'}}';
        echo $patientresult = GibberishAES::enc($patientres, $apikey);
        insertMobileLog('update',"$username",$patientres,$pid,'',"Edit Employer Data($group_name) Screen - Query Failed", 0);
    }  
}
function saveAuditNotes(){
    $apikey = 'rotcoderaclla';
    // The default key size is 256 bits.
    $old_key_size = GibberishAES::size();
    GibberishAES::size(256);
    try
    {
        $db = getConnection();
        
        $request = Slim::getInstance()->request();
        $apikey = 'rotcoderaclla';
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        $appres = GibberishAES::dec($request->getBody(), $apikey);
//        $appres = GibberishAES::dec('U2FsdGVkX1/45X0WDuKuDl3vwwcBhHea665IwWPRJMXuYUYXnEgb4Jbscy367DnOp2KinY3R93zLQBcLfxYh1DfbjTUZ/quPe9Zr+dR7DQwjntOkh8/78YYOtDNRD2QEnkrclSGhl1UOUyJCRlUxFtJUI6vmeqE759ythF03OlTq67qS5WJdRhVMMIuTbU4qDBlwzcVsv1htb7hEKneOzpZMVkZSwEok61E9iucaVjFnqo2+GJP43mML6rBlwglPDlunduoS05rswAc9im2J5YVn9+UsrAaeS1IcZonLReJq03OntkxKElqKKnT8SWK3BDo2tLN7g2rWou2o2PUq82fgtYCq1JBt7rfCj1WUPVb6IOPPRVK9F4WniXo488HitrDtIYONL5zqxGcwzcTjMzwtAUzFw9ysNUWvbxJiHwT4YYcvFOlR9yVwsDQklTSh1UowS/2jk48REE4C/ODtbAoY0YWeOzMZbGn/CJsP4AA=', $apikey);
        $insertArray = json_decode($appres,TRUE);
        
        $data = $data2 = 0;
        
//        echo "<pre>"; print_r($insertArray); echo "</pre>";

        $pid                = $insertArray['pid'];
        $audit_note         = $insertArray['ESIGN'];
        $user               = $insertArray['user'];
        $username           = $insertArray['username'];
        $encounter          = $insertArray['encounter'];
        $update_audit_data  = '';
        
        if(!empty($encounter)){
            // esign save 
            $fieldnames = "SELECT field_id
                        FROM layout_options WHERE form_id='ESIGN' AND uor <> 0 ORDER BY seq";
            $stmt2 = $db->prepare($fieldnames); 
            $stmt2->execute();
            $fieldnamesresult = $stmt2->fetchAll(PDO::FETCH_OBJ);
            if(!empty($fieldnamesresult)){
                for($i=0; $i< count($fieldnamesresult); $i++){
                    foreach($fieldnamesresult[$i] as $fkey => $fvalue){
                        $update_audit_data .= "`".$fvalue."`= '". addslashes($insertArray['ESIGN'][0][$fvalue])."',"; 
                        ${$fvalue} = $insertArray['ESIGN'][0][$fvalue];
                    }
                }
                if(!empty($update_audit_data)){
                    $update_audit_data = rtrim($update_audit_data,',');
                    $sql2 = "UPDATE form_encounter SET $update_audit_data WHERE pid ='$pid'  AND encounter = $encounter";
                    $stmt4 = $db->prepare($sql2);
                    if($stmt4->execute()){
                        $data = 1;
                        insertMobileLog('update',"$username",$sql2,"$pid","$encounter","Edit Audit Data Screen", 1);
                    }else{
                        insertMobileLog('update',"$username",$sql2,"$pid","$encounter","Edit Audit Data Screen - Failed", 0);
                        $data = 0;
                    }
                }
            }
            // cpo and ccm save in audit form
            $auditform_id                              = $insertArray['formid'];
            $group_array['defaultcpo']                 = $insertArray['CPO'][0]['defaultcpo'];
            $group_array['defaultccm']                 = $insertArray['CCM'][0]['defaultccm'];

            //echo "<pre>";print_r($group_array); echo "</pre>";

            $audit_data= ( serialize($group_array) );
            
            if($auditform_id != 0){
                $sql = "select audit_data from tbl_form_audit WHERE pid = '$pid' AND id = '$auditform_id'";
                $db->query( "SET NAMES utf8"); 
                $stmt = $db->prepare($sql) ;
                $stmt->execute();    
                
                $get_audit = $stmt->fetchAll(PDO::FETCH_OBJ);
                $audit_array = array();
                
                $audit_array = unserialize($get_audit[0]->audit_data); 
                foreach($audit_array as $key=>$value){ 
                    if($key == 'defaultcpo')
                        $new_audit_array[$key] = $group_array['defaultcpo'];
                    else if($key == 'defaultccm')
                        $new_audit_array[$key] = $group_array['defaultccm'];
                    else 
                        $new_audit_array[$key] = $value;
                }
                $audit_data = ( serialize($new_audit_array) );
                $updateaudit = "UPDATE tbl_form_audit SET  pid = $pid,
                authProvider = '" .$user . "',
                user = '" . $user. "',
                authorized = 1, 
                activity=1, 
                date = NOW(),
                audit_data = '" .$audit_data. "' WHERE id = '$auditform_id'";
                $db->query( "SET NAMES utf8");
                $stmt_audit = $db->prepare($updateaudit);
                if($stmt_audit->execute()){
                    $data2 = 1;
                    insertMobileLog('update',"$username",$updateaudit,"$pid","$encounter","Update Audit Form CPO, CCM Screen", 1);
                }else{
                    insertMobileLog('update',"$username",$updateaudit,"$pid","$encounter","Update Audit Form CPO, CCM Screen - Failed", 0);
                    $data2 = 0;
                }
            }else{
                $lastformid2 = "SELECT MAX(form_id) as forms FROM forms WHERE formdir='auditform'";
                $db->query( "SET NAMES utf8");
                $stmt5 = $db->prepare($lastformid2) ;
                $stmt5->execute();
                $maxformidval2 = $stmt5->fetchAll(PDO::FETCH_OBJ);
                $form_id =  $maxformidval2[0]->forms + 1;

                $insertform2 = "INSERT INTO forms (date,encounter, form_name, form_id, pid, user, groupname, authorized, deleted, formdir) VALUES (NOW(), $encounter, 'Audit Form', $form_id, $pid, '$username', 'Default', 1, 0, 'auditform' )";
                $db->query( "SET NAMES utf8");
                $stmt4 = $db->prepare($insertform2);
                if($stmt4->execute()){
                    insertMobileLog('insert',$username,$insertform2,$pid,$encounter,"New Audit form Screen",1);
                }else {
                    insertMobileLog('insert',$username,$insertform2,$pid,$encounter,"New Audit form Screen - Failed",0); 
                }
                $insert_audit = "INSERT INTO tbl_form_audit (id,pid,authProvider,authorized,activity,date,audit_data) VALUES($form_id,'$pid','$user',1,1, NOW(),'$audit_data')";
                $stmt_audit = $db->prepare($insert_audit);
                if($stmt_audit->execute()){
                    $data2 = 1;
                    insertMobileLog('insert',"$username",$insert_audit,"$pid","$encounter","Edit Audit Form CPO, CCM Screen", 1);
                }else{
                    insertMobileLog('insert',"$username",$insert_audit,"$pid","$encounter","Edit Audit Form CPO, CCM Screen - Failed", 0);
                    $data2 = 0;
                }
            }
            
        }

        if($data == 1 && $data2 == 1){  
            $insertcheck =  '[{"id":"1"}]';
            echo GibberishAES::enc($insertcheck, $apikey);
	}else{
            $insertcheck = '[{"id":"0"}]';
            echo GibberishAES::enc($insertcheck, $apikey);
	}
    }
    catch(PDOException $e)
    {

        $patientres =  '{"error":{"text":'. $e->getMessage() .'}}';
        echo $patientresult = GibberishAES::enc($patientres, $apikey);
        insertMobileLog('update',"$username",$patientres,$pid,'',"Edit Audit Data Screen - Query Failed", 0);
    }  
}
function editPatientFaility($id){
    $apikey = 'rotcoderaclla';
    // The default key size is 256 bits.
    $old_key_size = GibberishAES::size();
    GibberishAES::size(256);
    try 
    {
        $db = getConnection();

        $sql = "SELECT group_name,field_id,title,list_id,
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
        END as field_type,
                        CASE uor
                            WHEN 1 THEN 'Optional'
                            WHEN 2 THEN 'Required'
                            END as isRequired,description,max_length FROM layout_options WHERE form_id = 'SERVICEFAC' and uor<> 0 order by group_name, seq";   
        $db->query( "SET NAMES utf8");
        $stmt = $db->prepare($sql) ;
        $stmt->execute();                       

        $patientsreminders = $stmt->fetchAll(PDO::FETCH_OBJ);    
        $patientsreminder = array();
        if(!empty($patientsreminders)){
            for($i=0; $i<count($patientsreminders); $i++){
                $sql_fac = "select  ". $patientsreminders[$i]->field_id. " as field_name from tbl_patientfacility where id= '$id'";   
                $db->query( "SET NAMES utf8");
                $stmt_p = $db->prepare($sql_fac) ;
                $stmt_p->execute();                       

                $patientfacility = $stmt_p->fetchAll(PDO::FETCH_OBJ);  
                
                $patientsreminder[substr($patientsreminders[$i]->group_name,1)][$i]['field_id']  = $patientsreminders[$i]->field_id;
                $patientsreminder[substr($patientsreminders[$i]->group_name,1)][$i]['isRequired']  = $patientsreminders[$i]->isRequired;
                $patientsreminder[substr($patientsreminders[$i]->group_name,1)][$i]['field_type']  = $patientsreminders[$i]->field_type;
                $patientsreminder[substr($patientsreminders[$i]->group_name,1)][$i]['label']  = $patientsreminders[$i]->title;
                $patientsreminder[substr($patientsreminders[$i]->group_name,1)][$i]['list_id']  =$patientsreminders[$i]->list_id;
                $patientsreminder[substr($patientsreminders[$i]->group_name,1)][$i]['placeholder']  =$patientsreminders[$i]->description;
                $field_value  = isset($patientfacility[0]->field_name)? $patientfacility[0]->field_name : '';
                
                if($patientsreminders[$i]->max_length == 0)
                        $maxlength = '';
                    else
                        $maxlength = $patientsreminders[$i]->max_length;
                    $patientsreminder[substr($patientsreminders[$i]->group_name,1)][$i]['max_length']  = $maxlength;
                $get_title2 = "SELECT option_id, title FROM list_options WHERE list_id = '".$patientsreminders[$i]->list_id."'";
                $db->query( "SET NAMES utf8");
                $title_stmt2 = $db->prepare($get_title2) ;
                $title_stmt2->execute();                       

                $settitle2 = $title_stmt2->fetchAll(PDO::FETCH_OBJ);
                $titles= array();
                if(!empty($settitle2)){
                    for($k = 0; $k< count($settitle2); $k++){
                        $titles['option_id']    = $settitle2[$k]->option_id;
                        $titles['option_value'] = $settitle2[$k]->title;
                    }
                }
                $patientsreminder[substr($patientsreminders[$i]->group_name,1)][$i]['options']  = $titles;
                if($patientsreminders[$i]->field_type == 'Static Text'){
                        $patientsreminder[substr($patientsreminders[$i]->group_name,1)][$i]['value'] = $patientsreminders[$i]->description;
                    }else if($patientsreminders[$i]->field_type == 'Providers' || $patientsreminders[$i]->field_type == 'Providers NPI'){
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
                        $patientsreminder[substr($patientsreminders[$i]->group_name,1)][$i]['options']  = $data2;
                        $patientsreminder[substr($patientsreminders[$i]->group_name,1)][$i]['value'] = $field_value;
                    }else if($patientsreminders[$i]->field_type == 'Pharmacies'){
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
                        $patientsreminder[substr($patientsreminders[$i]->group_name,1)][$i]['options']  = $data2;
                        $patientsreminder[substr($patientsreminders[$i]->group_name,1)][$i]['value'] = $field_value;
                    }else if($patientsreminders[$i]->field_type == 'Organizations'){
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
                        $patientsreminder[substr($patientsreminders[$i]->group_name,1)][$i]['options']  = $data2;
                        $patientsreminder[substr($patientsreminders[$i]->group_name,1)][$i]['value'] = $field_value;
                    }else if($patientsreminders[$i]->field_type == 'Patient allergies'){
                        $patientsreminder[substr($patientsreminders[$i]->group_name,1)][$i]['value'] = $field_value;
                    }else if($patientsreminders[$i]->field_type == 'Lifestyle status'){
                        $patientsreminder[substr($patientsreminders[$i]->group_name,1)][$i]['value'] = $field_value;
                    }else if($patientsreminders[$i]->field_type == 'Facilities'){
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
                        $patientsreminder[substr($patientsreminders[$i]->group_name,1)][$i]['options']  = $data2;
                        $patientsreminder[substr($patientsreminders[$i]->group_name,1)][$i]['value'] = $field_value;
                    }else if($patientsreminders[$i]->field_type == 'Date Of Service'){
                        $patientsreminder[substr($patientsreminders[$i]->group_name,1)][$i]['options']  = '';
                        $patientsreminder[substr($patientsreminders[$i]->group_name,1)][$i]['value'] = $field_value;
                    }else if($patientsreminders[$i]->field_type == 'Insurance Companies'){
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
                        $patientsreminder[substr($patientsreminders[$i]->group_name,1)][$i]['options']  = $data2;
                        $patientsreminder[substr($patientsreminders[$i]->group_name,1)][$i]['value'] = $field_value;
                    }else if($patientsreminders[$i]->field_type == 'Users'){
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
                        $patientsreminder[substr($patientsreminders[$i]->group_name,1)][$i]['options']  = $data2;
                        $patientsreminder[substr($patientsreminders[$i]->group_name,1)][$i]['value'] = $field_value;
                    }else{
                        $patientsreminder[substr($patientsreminders[$i]->group_name,1)][$i]['value'] = $field_value;
                    }
             
            }
        }
//        echo "<pre>"; print_r($patientsreminder); echo "</pre>";
        if($patientsreminder)
        {
            $patientres = json_encode($patientsreminder); 
            echo $patientresult = GibberishAES::enc($patientres, $apikey);
        }
        else
        {
            $patientres =  '[{"id":"0"}]';
            echo $patientresult = GibberishAES::enc($patientres, $apikey);
        }
    } 
    catch(PDOException $e) 
    {

        $patientres =  '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo $patientresult = GibberishAES::enc($patientres, $apikey);
    }
}
function editAgencies($id){
    $apikey = 'rotcoderaclla';
    // The default key size is 256 bits.
    $old_key_size = GibberishAES::size();
    GibberishAES::size(256);
    try 
    {
        $db = getConnection();
        $get_layout = "SELECT field_id,title FROM layout_options WHERE form_id = 'AGENCY' and uor<> 0 order by group_name, seq";
        $db->query( "SET NAMES utf8");
        $stmt_layout = $db->prepare($get_layout) ;
        $stmt_layout->execute();                       

        $layout_fields = $stmt_layout->fetchAll(PDO::FETCH_OBJ);  
        $enc_val = '';
        for($i = 0; $i<count($layout_fields); $i++){
           $enc_val .=  "`".$layout_fields[$i]->field_id."`,";//." as ". preg_replace('/[^a-zA-Z0-9_.]/', '', $layout_fields[$i]->title).",";//str_replace(" ","_",$layout_fields[$i]->title). ",";
        }

        $enc_value = rtrim($enc_val,",");

        $sql = "select $enc_value from tbl_patientagency where id= '$id'";   
        $db->query( "SET NAMES utf8");
        $stmt = $db->prepare($sql) ;
        $stmt->execute();                       

        $patientsreminders = $stmt->fetchAll(PDO::FETCH_OBJ);    
        $patientsreminder = array();
        $i = 0;
        if(!empty($patientsreminders)){
            foreach($patientsreminders[0] as $pkey => $pvalue){
                $patientsreminder = getLayoutGroupSpecificFunction('AGENCY', $pvalue);
//                $get_layout2 = "SELECT group_name,list_id, title,
//                CASE data_type
//                WHEN 1  THEN  'List box'
//                WHEN 2  THEN  'Textbox'
//                WHEN 3  THEN  'Textarea'
//                WHEN 4  THEN  'Text-date'
//                WHEN 10 THEN  'Providers'
//                WHEN 11 THEN  'Providers NPI'
//                WHEN 12 THEN  'Pharmacies'
//                WHEN 13 THEN  'Squads'
//                WHEN 14 THEN  'Organizations'
//                WHEN 15 THEN  'Billing codes'
//                WHEN 21 THEN  'Checkbox list'
//                WHEN 22 THEN  'Textbox list'
//                WHEN 23 THEN  'Exam results'
//                WHEN 24 THEN  'Patient allergies'
//                WHEN 25 THEN  'Checkbox w/text'
//                WHEN 26 THEN  'List box w/add'
//                WHEN 27 THEN  'Radio buttons'
//                WHEN 28 THEN  'Lifestyle status'
//                WHEN 31 THEN  'Static Text'
//                WHEN 32 THEN  'Smoking Status'
//                WHEN 33 THEN  'Race and Ethnicity'
//                WHEN 34 THEN  'NationNotes'
//                WHEN 35 THEN  'Facilities'
//                WHEN 36 THEN  'Date Of Service'
//                WHEN 37 THEN  'Insurance Companies'
//                WHEN 38 THEN  'Users'
//                WHEN 39 THEN  'DateTime'
//                END as field_type,                         CASE uor
//                                WHEN 1 THEN 'Optional'
//                                WHEN 2 THEN 'Required'
//                                END as isRequired,max_length,description FROM layout_options WHERE form_id = 'AGENCY' AND field_id = '$pkey' order by group_name, seq";
//                $db->query( "SET NAMES utf8");
//                $stmt_layout2 = $db->prepare($get_layout2) ;
//                $stmt_layout2->execute();                       
//                $layout_fields2 = $stmt_layout2->fetchAll(PDO::FETCH_OBJ);
//                if(!empty($layout_fields2)){
//                    $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['field_id'] = $pkey;
//                    $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['field_type'] =  $layout_fields2[0]->field_type;
//                    $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['isRequired'] =  $layout_fields2[0]->isRequired;
//                    $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['label']  = $layout_fields2[0]->title;
//                    $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['list_id']  = $layout_fields2[0]->list_id;
//                    if($layout_fields2[0]->max_length == 0)
//                        $maxlength = '';
//                    else
//                        $maxlength = $layout_fields2[0]->max_length;
//                    $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['max_length']  = $maxlength;
//                    $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['placeholder']  = $layout_fields2[0]->description;
//                    $get_title2 = "SELECT option_id, title FROM list_options WHERE list_id = '".$layout_fields2[0]->list_id."' order by seq";
//                    $db->query( "SET NAMES utf8");
//                    $title_stmt2 = $db->prepare($get_title2) ;
//                    $title_stmt2->execute();                       
//
//                    $settitle2 = $title_stmt2->fetchAll(PDO::FETCH_OBJ);
//                    $titles= array();
//                    if(!empty($settitle2)){
//                        for($k = 0; $k< count($settitle2); $k++){
//                            $titles[$settitle2[$k]->option_id] = $settitle2[$k]->title;
//                        }
//                    }
//                    $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['options']  = $titles;
//                    if($layout_fields2[0]->list_id != ''){
//                        $exploded_val = explode('|', $pvalue);
//
//                        $stringvalue = '';
//                        for($j=0; $j<count($exploded_val); $j++){
//                            $get_title = "SELECT title,option_id FROM list_options WHERE option_id = '$exploded_val[$j]' AND list_id = '".$layout_fields2[0]->list_id."'";
//                            $db->query( "SET NAMES utf8");
//                            $title_stmt = $db->prepare($get_title) ;
//                            $title_stmt->execute();                       
//
//                            $settitle = $title_stmt->fetchAll(PDO::FETCH_OBJ);  
//                            if(!empty($settitle)){
//                               $stringvalue .= $settitle[0]->option_id.",";
//                            }
//                        }
//                        $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['value'] = rtrim($stringvalue,',');
//                    }else{
//                        if($layout_fields2[0]->field_type == 'Static Text'){
//                        $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['value'] = $layout_fields2[0]->description;
//                    }else if($layout_fields2[0]->field_type == 'Providers' || $layout_fields2[0]->field_type == 'Providers NPI'){
//                        $sql3 = "SELECT id, CONCAT(fname, ' ',lname) as name FROM `users`  WHERE active=1 AND authorized=1 ORDER BY fname,lname ";
//                        $db->query( "SET NAMES utf8"); 
//                        $stmt3 = $db->prepare($sql3) ;
//                        $stmt3->execute();                       
//                        $getListData = $stmt3->fetchAll(PDO::FETCH_OBJ);  
//                        if(!empty($getListData)):
//                            $data2 = array();
//                            foreach($getListData as $list){
//                                $data2[$list->id] = $list->name;
//                            }
//                        else:
//                            $data2 = '';
//                        endif;
////                        $data['options_list'] = $data2; 
//                        $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['options']  = $data2;
//                        $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['value'] = $pvalue;
//                    }else if($layout_fields2[0]->field_type == 'Pharmacies'){
//                        $sql3 = "SELECT id, name FROM `pharmacies`   ORDER BY name";
//                        $db->query( "SET NAMES utf8"); 
//                        $stmt3 = $db->prepare($sql3) ;
//                        $stmt3->execute();                       
//                        $getListData = $stmt3->fetchAll(PDO::FETCH_OBJ);  
//                        if(!empty($getListData)):
//                            $data2 = array();
//                            foreach($getListData as $list){
//                                $data2[$list->id] = $list->name;
//                            }
//                        else:
//                            $data2 = '';
//                        endif;
////                        $data['options_list'] = $data2; 
//                        $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['options']  = $data2;
//                        $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['value'] = $pvalue;
//                    }else if($layout_fields2[0]->field_type == 'Organizations'){
//                        $sql3 = "SELECT id, fname, lname, organization, username FROM users WHERE active = 1 AND ( info IS NULL OR info NOT LIKE '%Inactive%' ) AND ( username = '' OR authorized = 1 ) ORDER BY organization, lname, fname";
//                        $db->query( "SET NAMES utf8"); 
//                        $stmt3 = $db->prepare($sql3) ;
//                        $stmt3->execute();                       
//                        $getListData = $stmt3->fetchAll(PDO::FETCH_OBJ);  
//                        if(!empty($getListData)):
//                            $data2 = array();
//                            foreach($getListData as $list){
//                                $uname = $list->organization;
//                                if (empty($uname) || substr($uname, 0, 1) == '(') {
//                                    $uname = $list->lname;
//                                    if ($list->fname) $uname .= ", " . $list->fname;
//                                }
//                                $data2[$list->id] = $uname;
//                            }
//                        else:
//                            $data2 = '';
//                        endif;
////                        $data['options_list'] = $data2; 
//                        $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['options']  = $data2;
//                        $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['value'] = $pvalue;
//                    }else if($layout_fields2[0]->field_type == 'Patient allergies'){
//                        $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['value'] = '';
//                    }else if($layout_fields2[0]->field_type == 'Lifestyle status'){
//                        $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['value'] = $pvalue;
//                    }else if($layout_fields2[0]->field_type == 'Facilities'){
//                        $sql3 = "SELECT id, name FROM facility ORDER BY name";
//                        $db->query( "SET NAMES utf8"); 
//                        $stmt3 = $db->prepare($sql3) ;
//                        $stmt3->execute();                       
//                        $getListData = $stmt3->fetchAll(PDO::FETCH_OBJ);  
//                        if(!empty($getListData)):
//                            $data2 = array();
//                            foreach($getListData as $list){
//                                $data2[$list->id] = $list->name;
//                            }
//                        else:
//                            $data2 = '';
//                        endif;
////                        $data['options_list'] = $data2; 
//                        $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['options']  = $data2;
//                        $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['value'] = $pvalue;
//                    }else if($layout_fields2[0]->field_type == 'Date Of Service'){
//                        $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['options']  = '';
//                        $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['value'] = '';
//                    }else if($layout_fields2[0]->field_type == 'Patient allergies'){
//
//                        $sql3 = "SELECT title FROM lists WHERE " .
//                        "pid = $patientId AND type = 'allergy' AND enddate IS NULL " .
//                        "ORDER BY begdate";
//                        $db->query( "SET NAMES utf8"); 
//                        $stmt3 = $db->prepare($sql3) ;
//                        $stmt3->execute();                       
//                        $getListData = $stmt3->fetchAll(PDO::FETCH_OBJ);  
//                        if(!empty($getListData)):
//                            $data2 = '';
//                            foreach($getListData as $liste){
//                                $data2 .= $liste->title;
//                            }
//                        else:
//                            $data2 = '';
//                        endif;
//                        $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['value'] = $data2;
//                    }else if($layout_fields2[0]->field_type == 'Insurance Companies'){
//                        $sql3 = "SELECT id,  name FROM `insurance_companies`  ORDER BY name";
//                        $db->query( "SET NAMES utf8"); 
//                        $stmt3 = $db->prepare($sql3) ;
//                        $stmt3->execute();                       
//                        $getListData = $stmt3->fetchAll(PDO::FETCH_OBJ);  
//                        if(!empty($getListData)):
//                            $data2 = array();
//                            foreach($getListData as $list){
//                                $data2[$list->id] = $list->name;
//                            }
//                        else:
//                            $data2 = '';
//                        endif;
////                        $data['options_list'] = $data2; 
//                        $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['options']  = $data2;
//                        $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['value'] = $pvalue;
//                    }else if($layout_fields2[0]->field_type == 'Users'){
//                        $sql3 = "SELECT id, CONCAT(fname, ' ',lname) as name FROM `users`  WHERE username <> '' ORDER BY fname,lname ";
//                        $db->query( "SET NAMES utf8"); 
//                        $stmt3 = $db->prepare($sql3) ;
//                        $stmt3->execute();                       
//                        $getListData = $stmt3->fetchAll(PDO::FETCH_OBJ);  
//                        if(!empty($getListData)):
//                            $data2 = array();
//                            foreach($getListData as $list){
//                                $data2[$list->id] = $list->name;
//                            }
//                        else:
//                            $data2 = '';
//                        endif;
////                        $data['options_list'] = $data2; 
//                        $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['options']  = $data2;
//                        $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['value'] = $pvalue;
//                    }else{
//                        $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['value'] = $pvalue;
//                    }
//                    }
//                }
                $i++;
            }
        }
        echo "<pre>"; print_r($patientsreminder); echo "</pre>";
        if($patientsreminder)
        {
            $patientres = json_encode($patientsreminder); 
            echo $patientresult = GibberishAES::enc($patientres, $apikey);
//            echo $fileresult =  GibberishAES::dec($patientresult, $apikey);
        }
        else
        {
            $patientres =  '[{"id":"0"}]';
            echo $patientresult = GibberishAES::enc($patientres, $apikey);
        }
    } 
    catch(PDOException $e) 
    {

        $patientres =  '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo $patientresult = GibberishAES::enc($patientres, $apikey);
    }
}
function createAgencies(){
    $apikey = 'rotcoderaclla';
    // The default key size is 256 bits.
    $old_key_size = GibberishAES::size();
    GibberishAES::size(256);
    try 
    {
        $db = getConnection();
        $patientsreminder = getLayoutGroupSpecificFunction('AGENCY', '');
//        echo "<pre>"; print_r($patientsreminder); echo "</pre>";
        if($patientsreminder)
        {
            $patientres = json_encode($patientsreminder); 
            echo $patientresult = GibberishAES::enc($patientres, $apikey);
        }
        else
        {
            $patientres =  '[{"id":"0"}]';
            echo $patientresult = GibberishAES::enc($patientres, $apikey);
        }
    } 
    catch(PDOException $e) 
    {

        $patientres =  '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo $patientresult = GibberishAES::enc($patientres, $apikey);
    }
}
function getLayoutGroupSpecificFunction($form_name,$pvalue){
    $db = getConnection();
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
                                END as isRequired,seq,max_length,description FROM layout_options WHERE form_id = '$form_name' AND uor<>0 order by group_name, seq";
    $db->query( "SET NAMES utf8");
    $stmt_layout2 = $db->prepare($get_layout2);
    $stmt_layout2->execute();                       
    $layout_fields2 = $stmt_layout2->fetchAll(PDO::FETCH_OBJ);
    if(!empty($layout_fields2)){
//        $patientsreminder[substr($layout_fields2[0]->group_name,1)]['form_id'] = $formid;
        for($i=0; $i< count($layout_fields2); $i++){
            if(!empty($layout_fields2[$i]->field_type)){
//                $sql = "select field_value from lbf_data where field_id  = '". $layout_fields2[$i]->field_id."' and form_id= '$formid'";   
//                $db->query( "SET NAMES utf8");
//                $stmt = $db->prepare($sql);
//                $stmt->execute();                       
//
//                $patientsreminders = $stmt->fetchAll(PDO::FETCH_OBJ);   
//                $pvalue = isset($patientsreminders[0]->field_value)? $patientsreminders[0]->field_value: '';

//                $patientsreminder[substr($layout_fields2[$i]->group_name,1)][$i]['group_name'] = substr($layout_fields2[$i]->group_name,1);
                $patientsreminder[substr($layout_fields2[$i]->group_name,1)][$i]['field_id'] = $layout_fields2[$i]->field_id;
                $patientsreminder[substr($layout_fields2[$i]->group_name,1)][$i]['isRequired'] =  $layout_fields2[$i]->isRequired; 
                $patientsreminder[substr($layout_fields2[$i]->group_name,1)][$i]['field_type'] =  $layout_fields2[$i]->field_type;
                $patientsreminder[substr($layout_fields2[$i]->group_name,1)][$i]['label']  = $layout_fields2[$i]->title;
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
                $patientsreminder[substr($layout_fields2[$i]->group_name,1)][$i]['options']  = $titles;
                if($layout_fields2[$i]->list_id != ''){
                    $patientsreminder[substr($layout_fields2[$i]->group_name,1)][$i]['value'] = $pvalue;
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
                    $patientsreminder[substr($layout_fields2[$i]->group_name,1)][$i]['options']  = $data2;
                    $patientsreminder[substr($layout_fields2[$i]->group_name,1)][$i]['value'] = $pvalue;
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
                    $patientsreminder[substr($layout_fields2[$i]->group_name,1)][$i]['options']  = $data2;
                    $patientsreminder[substr($layout_fields2[$i]->group_name,1)][$i]['value'] = $pvalue;
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
                    $patientsreminder[substr($layout_fields2[$i]->group_name,1)][$i]['options']  = $data2;
                    $patientsreminder[substr($layout_fields2[$i]->group_name,1)][$i]['value'] = $pvalue;
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
                    $patientsreminder[substr($layout_fields2[$i]->group_name,1)][$i]['value'] = $data2;
                }else if($layout_fields2[$i]->field_type == 'Lifestyle status'){
                    $patientsreminder[substr($layout_fields2[$i]->group_name,1)][$i]['value'] = $pvalue;
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
                    $patientsreminder[substr($layout_fields2[$i]->group_name,1)][$i]['options']  = $data2;
                    $patientsreminder[substr($layout_fields2[$i]->group_name,1)][$i]['value'] = $pvalue;
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
                    $patientsreminder[substr($layout_fields2[$i]->group_name,1)][$i]['options']  = $data2;
                    $patientsreminder[substr($layout_fields2[$i]->group_name,1)][$i]['value'] = $pvalue;
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
                    $patientsreminder[substr($layout_fields2[$i]->group_name,1)][$i]['options']  = $data2;
                    $patientsreminder[substr($layout_fields2[$i]->group_name,1)][$i]['value'] = $pvalue;
                }else{
                    $patientsreminder[substr($layout_fields2[$i]->group_name,1)][$i]['value'] = $pvalue;
                }
                }
            }
        }
    }

    
    return $patientsreminder;
}
function newEncounterScreen($loginProvderId){
    $apikey = 'rotcoderaclla';
    // The default key size is 256 bits.
    $old_key_size = GibberishAES::size();
    GibberishAES::size(256);
    try
    {
        $db = getConnection(); 
        
        $get_fuv = "SELECT visit_categories FROM tbl_allcare_facuservisit WHERE  `users`  REGEXP ('".":\"".$loginProvderId."\"')";
        $fuv_stmt = $db->prepare($get_fuv) ;
        $fuv_stmt->execute();
        $set_fuv = $fuv_stmt->fetchAll(PDO::FETCH_OBJ); 
        for($i = 0; $i<count($set_fuv); $i++){
           $array[] =  unserialize( $set_fuv[$i]->visit_categories);
        }
//        print_r($array);
        $dataArray = array();
        for($j = 0; $j<count($array); $j++){
            foreach($array[$j] as $arraykey){
                 $dataArray[] = $arraykey;
            }
        }
        $dataarray = array_unique($dataArray);
        $k =0;
        foreach($dataarray as $arrayval){
            $getVisitname = "SELECT pc_catname FROM openemr_postcalendar_categories WHERE pc_catid = $arrayval";
            $fuv_stmt_name = $db->prepare($getVisitname) ;
            $fuv_stmt_name->execute();
            $setVisitname = $fuv_stmt_name->fetchAll(PDO::FETCH_OBJ); 
            if(!empty($setVisitname)){
               $visit_categories[$k]['option_id']  = $arrayval;
               $visit_categories[$k]['option_title']  = $setVisitname[0]->pc_catname;
            }
            $k++;
        }
        $presc[0]['Label'] = 'Visit category';
        $presc[0]['Type']  = 'List box'; //"text area" is the datatype
        $presc[0]['field_id']  = 'pc_catid';
        $presc[0]['SelectedValue'] = '';
        $presc[0]['Value'] = $visit_categories;
        $presc[0]['isRequired'] =  'Required';
        
        $get_fuv2 = "SELECT facilities FROM tbl_allcare_facuservisit WHERE  `users`  REGEXP ('".":\"".$loginProvderId."\"')";
        $fuv_stmt2 = $db->prepare($get_fuv2) ;
        $fuv_stmt2->execute();
        $set_fuv2 = $fuv_stmt2->fetchAll(PDO::FETCH_OBJ); 
        for($i = 0; $i<count($set_fuv2); $i++){
           $array2[] =  unserialize( $set_fuv2[$i]->facilities);
        }
//        print_r($array2);
        $dataArray2 = array();
        for($j = 0; $j<count($array2); $j++){
            foreach($array2[$j] as $arraykey2){
                 $dataArray2[] = $arraykey2;
            }
        }
        $dataarray2 = array_unique($dataArray2);
        $m = 0;
        foreach($dataarray2 as $arrayval2){
            $getfacname = "SELECT name FROM facility WHERE id = $arrayval2";
            $fac_stmt_name = $db->prepare($getfacname) ;
            $fac_stmt_name->execute();
            $setfacname = $fac_stmt_name->fetchAll(PDO::FETCH_OBJ); 
            if(!empty($setVisitname)){
               $facility[$m]['option_id']  = $arrayval2;
               $facility[$m]['option_title']  = $setfacname[0]->name;
            }
            $m++;
        }
        
        $presc[1]['Label'] = 'Facility';
        $presc[1]['Type'] = 'List box';
        $presc[1]['field_id']  = 'facility_id';
        $presc[1]['SelectedValue'] =  '';
        $presc[1]['Value'] = $facility;
        $presc[1]['isRequired'] =  'Required';
        
        $qry = "select id, name FROM facility WHERE primary_business_entity =1";
        $stmt = $db->prepare($qry) ;
        $stmt->execute();
        $rs = $stmt->fetchAll(PDO::FETCH_OBJ);
        if(!empty($rs)){
            for($i=0; $i< count($rs); $i++){
                $biling[$i]['option_id']  = $rs[0]->id;
                $biling[$i]['option_title']  = $rs[0]->name;
            }
        }
        
        $presc[2]['Label'] = 'Billing Facility';
        $presc[2]['Type'] = 'List box';
        $presc[2]['field_id'] = 'billing_facility';
        $presc[2]['SelectedValue'] = $biling[0]['option_id'];
        $presc[2]['Value'] = $biling;
        $presc[2]['isRequired'] =  'Required';
        
        $presc[3]['Label'] = 'Date of Service';
        $presc[3]['Type'] = 'Text-date';
        $presc[3]['field_id'] = 'date';
        $presc[3]['SelectedValue'] = '';
        $presc[3]['isRequired'] =  'Required';
        
        $provider = "SELECT id, CONCAT(fname,' ',lname) as name FROM users WHERE " .
                    "( authorized = 1 OR info LIKE '%provider%' ) AND username != '' " .
                    "AND active = 1 AND ( info IS NULL OR info NOT LIKE '%Inactive%' ) " .
                    "ORDER BY lname, fname" ; 
        $selectprovider = $db->prepare($provider) ; 
        $selectprovider->execute(); 
        $providerlist = $selectprovider->fetchAll(PDO::FETCH_OBJ); 
        if(!empty($providerlist)){
            for($i=0; $i< count($providerlist); $i++){
                $providers[$i]['option_id']  = $providerlist[$i]->id;
                $providers[$i]['option_title']  = $providerlist[$i]->name;
            }
        }

        $checkprovider = "SELECT id, CONCAT(fname,' ',lname) as name FROM users WHERE " .
                    "( authorized = 1 OR info LIKE '%provider%' ) AND username != '' " .
                    "AND active = 1 AND ( info IS NULL OR info NOT LIKE '%Inactive%' ) " .
                    "and id = $loginProvderId" ; 
        $checkprovider_stmt = $db->prepare($checkprovider) ; 
        $checkprovider_stmt->execute(); 
        $setcheckprovider = $checkprovider_stmt->fetchAll(PDO::FETCH_OBJ); 
        $setprovider = 0;
        if(!empty($setcheckprovider)){
            $setprovider = 1;
        }
        
        $get_accesssql = "SELECT provider_plist_links FROM tbl_user_custom_attr_1to1 WHERE userid= '$loginProvderId'";
        $get_accessstmt = $db->prepare($get_accesssql) ;
        $get_accessstmt->execute();                       

        $setaccess = $get_accessstmt->fetchAll(PDO::FETCH_OBJ);
        $hasaccess = 0;
        if($setaccess ){
            if (strpos($setaccess[0]->provider_plist_links,'create_enc') !== false) {
                $hasaccess = 1;
            }
        }
        
        $presc[4]['Label'] = 'Rendering Provider';
        $presc[4]['Type'] = 'List box';
        $presc[4]['field_id'] = 'rendering_provider';
        $presc[4]['SelectedValue'] =  '';
        $presc[4]['Value'] = $providers;
        $presc[4]['isProvider'] = $setprovider;
        $presc[4]['hasAccess'] = $hasaccess;
        $presc[4]['isRequired'] =  'Required';
        
        $presc[5]['Label'] = 'Consultation Brief Description';
        $presc[5]['Type'] = 'Textarea';
        $presc[5]['field_id'] = 'reason';
        $presc[5]['SelectedValue'] =  '';
        $presc[5]['isRequired'] =  'Optional';
        $presc[5]['placeholder'] =  'Consultation Brief Description';
                
//        echo "<pre>"; print_r($presc); echo "</pre>";
        if($presc)
        {
            $patientres = json_encode($presc);
            echo $patientresult = GibberishAES::enc($patientres, $apikey);
        }
        else
        {
            $patientres =  '[{"id":"0"}]';
            echo $patientresult = GibberishAES::enc($patientres, $apikey);
        }
    }
    catch(PDOException $e)
    {

        $patientres =  '{"error":{"text":'. $e->getMessage() .'}}';
        echo $patientresult = GibberishAES::enc($patientres, $apikey);
    }
}
function createNewEncounter(){
    $db = getConnection();
    $key = 'rotcoderaclla';
    $old_key_size = GibberishAES::size();
    $request = Slim::getInstance()->request();
    GibberishAES::size(256);
    $appres = GibberishAES::dec($request->getBody(), $key);
    //$appres = GibberishAES::dec('U2FsdGVkX18TIOfKRQmpxtC6HvMAaYgNBoZUbuaGkYCXwsWjoQMs5x/8+WX4HsH4J0adZ2gVVlvbxBEY0zPZ+PiH5gLBR/oJgmk9ExUbn6wu/GFgiijfyX7JV2AjJF5HPi0opo/c1z5r+WjbF6XDD162Yb9vzDKxvmMPAz5ym46/JFaUOZHmCVppTValgVUQAz3bTeEDliEpWH3Sc5xXrFYjNeUg61b1C7VJNOZILqGhnztVViR5HxsY+InXatsX', $key);
    $insertArray                = json_decode($appres,TRUE);
    $date                       = date('Y-m-d H:i:s', strtotime(str_replace('-','/', $insertArray['date'])));  
    $pid                        = $insertArray['pid'];
    $user                       = $insertArray['loginProvderId'];
    $username2                  = getUserName($user);
    $username                   = isset($username2[0]['username'])? $username2[0]['username'] : ''; 
    $billing_facility           = $insertArray['billing_facility'];
    $pc_facility                = $insertArray['facility_id'];
//    $encDate                    = date('Y-m-d H:i:s', strtotime(str_replace('-','/', $insertArray[0]['encDate'])));  
    $pc_catid                   = $insertArray['pc_catid']; 
    $reason                     = addslashes($insertArray['reason']); 
//    $pc_apptstatus              = $insertArray[0]['pc_apptstatus']; 
//    $pc_eid                     = $insertArray[0]['apptid'];
    
    try{
        
        $getfacilityname = "SELECT name FROM facility where id = $pc_facility";
        $fstmt = $db->prepare($getfacilityname);
        $fstmt->execute();
        $getfacilityname = $fstmt->fetchAll(PDO::FETCH_OBJ);
        $facility = $getfacilityname[0]->name;

        $sqlGetMaxEncounter="SELECT id as max_encounter FROM sequences";

        $stmt2 = $db->prepare($sqlGetMaxEncounter);
        $stmt2->execute();
        $resMaxEncounter = $stmt2->fetchAll(PDO::FETCH_OBJ);

        if($resMaxEncounter){
            $encounter = $resMaxEncounter[0]->max_encounter +1;
            
            $queryseq = "UPDATE sequences SET id = $encounter ";
            $db->query( "SET NAMES utf8");
            $stmt_layout = $db->prepare($queryseq);
            if($stmt_layout->execute()){
                insertMobileLog('update',"$username",$queryseq,$pid,$encounter,"Save Encounter Sequence Data Screen", 1);
            }else{
                insertMobileLog('update',"$username",$queryseq,$pid,$encounter,"Save Encounter Sequence Data Screen - Failed", 0);
            }
            
        }

        $sql10 = "INSERT INTO form_encounter (date, reason,facility, facility_id, pid, encounter, pc_catid, provider_id, billing_facility,rendering_provider)
        VALUES ('$date','$reason', '$facility',$pc_facility,$pid,$encounter,$pc_catid,$user,$billing_facility,'$user')";
        
        $q = $db->prepare($sql10);

        if($q->execute()){
            $lastId = $db->lastInsertId(); 
            $sqlGetLastEncounter="SELECT MAX(encounter) as encounter, form_encounter.id, username FROM form_encounter INNER JOIN users ON form_encounter.provider_id = users.id WHERE pid=$pid AND form_encounter.provider_id=$user AND form_encounter.id = '$lastId'";

            //$db = getConnection();
            $stmt = $db->prepare($sqlGetLastEncounter) ;
            $stmt->execute();
            $newEnc = $stmt->fetchAll(PDO::FETCH_OBJ);
            insertMobileLog('insert',"$username",$sql10,$pid,$encounter,"INSERT Encounter Form Data Screen", 1);
            
            $insertform = "INSERT INTO forms (date, encounter, form_name, form_id, pid, user, groupname, authorized, deleted, formdir) VALUES(NOW(),".$newEnc[0]->encounter.",'New Patient Encounter',".$newEnc[0]->id.",$pid,'".$newEnc[0]->username."','Default',1, 0,'newpatient')";
            $stmt3 = $db->prepare($insertform) ;
            if($stmt3->execute()){
                insertMobileLog('insert',"$username",$insertform,$pid,$encounter,"Create New Encounter Screen", 1);
            }else{
                insertMobileLog('insert',"$username",$insertform,$pid,$encounter,"Create New Encounter Screen - Failed", 0);
            }
            //$newform = $stmt3->fetchAll(PDO::FETCH_OBJ);
            
//            $updatestatus = "UPDATE openemr_postcalendar_events SET pc_catid = $pc_catid, pc_title= '$pc_title', pc_apptstatus = '$pc_apptstatus'  WHERE pc_eid=$pc_eid ";
//            $stmt4 = $db->prepare($updatestatus) ;
//            $stmt4->execute();
            //$updated = $stmt4->fetchAll(PDO::FETCH_OBJ);
            if($newEnc){
                     // log data
                    $logdata= array(); 
                    $data = "SELECT logdate from `tbl_allcare_formflag` WHERE  form_id='".$newEnc[0]->id . "' AND encounter_id = '".$newEnc[0]->encounter."' AND form_name = 'Patient Encounter'";
                    $data_stmt7 = $db->prepare($data);
                    $data_stmt7->execute();  
                    $form_flag_res1 = $data_stmt7->fetchAll(PDO::FETCH_OBJ);
                    //echo "<pre>"; print_r($form_flag_res1); echo "</pre>";
                    foreach ($form_flag_res1 as $value2) {
                            $array =  unserialize($value2->logdate);
                            $count= count($array);
                    }
                    $username2  = getUserName($user);
                    $username   = isset($username2[0]['username'])? $username2[0]['username'] : ''; 

                    $res = "SELECT * FROM `tbl_allcare_formflag` WHERE  form_id='".$newEnc[0]->id . "' AND encounter_id = '".$newEnc[0]->encounter."' AND form_name = 'Patient Encounter'";
                    $row1 = $db->prepare($res);
                    $row1->execute();  
                    $row1_res1 = $row1->fetchAll(PDO::FETCH_OBJ);
                    if(empty($row1_res1)){
                        $count = 0;

                        $array2[] = array( 'authuser' =>$username,'Status' => 'Incomplete', 'date' => date("Y/m/d"), 'action'=>'created', 'ip_address'=>'Mobile','count'=> $count+1);
    //                    $logdata = array_merge_recursive($array, $array2);
                        $logdata=  serialize($array2);
                        $query1 = "INSERT INTO tbl_allcare_formflag ( encounter_id,form_id, form_name,pending,finalized, logdate" .
                                ") VALUES ( '".$newEnc[0]->encounter."','".$newEnc[0]->id ."', 'Patient Encounter',NULL, NULL, '".$logdata."' )";

                        $log_stmt = $db->prepare($query1);
                        if($log_stmt->execute()){
                            insertMobileLog('insert',"$username",$query1,$pid,$encounter,"Create log data Screen", 1);
                        }else{
                            insertMobileLog('insert',"$username",$query1,$pid,$encounter,"Create log data Screen - Failed", 0);
                        }
                        $check_data = 1;
                    }else{
                        $count = isset($count)? $count: 0;

                        $array2[] = array( 'authuser' =>$username,'Status' => 'Incomplete', 'date' => date("Y/m/d"), 'action'=>'updated' ,'ip_address'=>'Mobile','count'=> $count+1);
                        $logdata = array_merge_recursive($array, $array2);
                        $logdata= ($logdata? serialize($logdata): serialize($array2) );
                        $query1 = "UPDATE tbl_allcare_formflag SET logdate=  '".$logdata."' WHERE encounter_id ='".$newEnc[0]->encounter."' and form_id = '".$newEnc[0]->id . "' and form_name = 'Patient Encounter'"; 
                        $log_stmt = $db->prepare($query1);
                        if($log_stmt->execute()){
                            insertMobileLog('update',"$username",$query1,$pid,$encounter,"Update log data Screen", 1);
                        }else{
                            insertMobileLog('update',"$username",$query1,$pid,$encounter,"Update log data Screen - Failed", 0);
                        } 
                        $check_data = 1;
                    }
                    $newencres = '[{"id":"'.$newEnc[0]->encounter.'"}]'; 
                    echo $newencresult = GibberishAES::enc($newencres, $key);
            }else{
                    $newencres = '[{"id":"-1"}]';     
                    echo $newencresult = GibberishAES::enc($newencres, $key);
            }
        } else {
            $newencres = '[{"id":"0"}]';
            echo $newencresult = GibberishAES::enc($newencres, $key);
            insertMobileLog('insert',"$username",$sql10,$pid,$encounter,"INSERT Encounter Form Data Screen - Failed", 0);
        }
    } catch (Exception $ex) {
        $error =  '{"error":{"text":'. $ex->getMessage() .'}}'; 
        echo $datares = GibberishAES::enc($error, $key);
        insertMobileLog('insert',"$username",$error,$pid,$encounter,"Create new Encounter from BY Facility Screen - Query Failed", 0);
    }
    
}
function MobileFileUpload() {
    try{
        $apikey = 'rotcoderaclla';
        $request = Slim::getInstance()->request();
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        $fileresult =  GibberishAES::dec($request->getBody(), $apikey);
        $filesArray = json_decode($fileresult,TRUE);
//        echo "<pre>"; print_r($filesArray); echo "</pre>";
        $string = $filesArray['string'];
        $pid = $filesArray['pid'];
        $encounter = $filesArray['encounter'];
        $providerid = $filesArray['loginProviderId'];
        $filename = $filesArray['filename'];
        $size = $filesArray['size'];
        $mimetype = $filesArray['mimetype'];
        $form_name = $filesArray['form_name'];
        if(!file_exists ("uploads/$pid/$encounter/$form_name")){
            if (mkdir("uploads/$pid/$encounter/$form_name", 0777, true)) {
                $datacheck = file_put_contents("uploads/$pid/$encounter/$form_name/$filename", base64_decode($string));
            }
        }else{ 
            $datacheck = file_put_contents("uploads/$pid/$encounter/$form_name/$filename", base64_decode($string));
        }
        $fileurl = "http://".$_SERVER['SERVER_NAME']."/api/uploads/$pid/$encounter/$form_name/$filename";
        if($datacheck != 0){
            $db = getConnection();
            $sql_table = "INSERT INTO tbl_mobile_wave_files (pid, encounter,user,url,filename,mimetype,size,created_date,filedata,form_name) VALUES('$pid','$encounter','$providerid','$fileurl','$filename','$mimetype','$size', NOW(),'$string','$form_name')";
            $stmt_table = $db->prepare($sql_table) ;
            if($stmt_table->execute()){
                $dataresultant = '[{"filename":"'.$filename.'","fileurl":"'.$fileurl.'","mimetype":"'.$mimetype.'","info":{"createdate":"NOW()","size":"'.$size.'"},"patientinfo":{"userid":"'.$providerid.'","eid":"'.$encounter.',"pid":"'.$pid.'"}}]';
            }                      

        }
        if($datacheck != 0 && $dataresultant)
        {   
           $datares = $dataresultant;//$datares = json_encode($dataResult);
           echo $patientresult = GibberishAES::enc($datares, $apikey);
        }
        else
        {
            $datares = '[{"id":"0"}]';   
            echo $patientresult = GibberishAES::enc($datares, $apikey);
        }
    }catch(PDOException $e){
        $insertquery = '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo GibberishAES::enc($insertquery, $apikey);
    }
}
function getDictationFiles($pid, $encounter,$form_name) {
    try{
        $apikey = 'rotcoderaclla';
        $request = Slim::getInstance()->request();
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        
        $db = getConnection();
        $sql = "SELECT id,filename,url FROM tbl_mobile_wave_files WHERE pid = '$pid' AND encounter = '$encounter' and form_name = '$form_name' ";
        $stmt = $db->prepare($sql) ;
        $stmt->execute();                       

        $dataResult = $stmt->fetchAll(PDO::FETCH_OBJ);    
            
        if($dataResult)
        {   
           $datares = json_encode($dataResult);
           echo $patientresult = GibberishAES::enc($datares, $apikey);
        }
        else
        {
            $datares = '[{"id":"0"}]';   
            echo $patientresult = GibberishAES::enc($datares, $apikey);
        }
    }catch(PDOException $e){
        $insertquery = '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo GibberishAES::enc($insertquery, $apikey);
    }
}
function renameDictationFile() {
    try{
        $apikey = 'rotcoderaclla';
        $request = Slim::getInstance()->request();
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        $fileresult =  GibberishAES::dec($request->getBody(), $apikey);
        $filesArray = json_decode($fileresult,TRUE);
//        echo "<pre>"; print_r($filesArray); echo "</pre>";
        $old_file_name = $filesArray['old_file_name'];
        $new_file_name = $filesArray['old_file_name'];
        $id            = $filesArray['id'];
        $pid           = $filesArray['pid'];
        $encounter     = $filesArray['encounter'];
        $form_name     = $filesArray['form_name'];
        $datacheck     = 0;
        if(file_exists ("uploads/$pid/$encounter/$form_name/$old_file_name")){
            if(rename("uploads/$pid/$encounter/$form_name/$old_file_name", "uploads/$pid/$encounter/$form_name/$new_file_name")===true){
                $datacheck = 1;
                $db = getConnection();
                $sql = "UPDATE tbl_mobile_wave_files SET filename = '$new_file_name' WHERE  id='$id' ";
                $stmt = $db->prepare($sql) ;
            }
        }
        
        if($stmt->execute() && $datacheck == 1)
        {   
           $datares =  '[{"id":"1"}]';  
           echo $patientresult = GibberishAES::enc($datares, $apikey);
        }
        else
        {
            $datares = '[{"id":"0"}]';   
            echo $patientresult = GibberishAES::enc($datares, $apikey);
        }
    }catch(PDOException $e){
        $insertquery = '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo GibberishAES::enc($insertquery, $apikey);
    }
}
function deleteDictationFile() {
    try{
        $apikey = 'rotcoderaclla';
        $request = Slim::getInstance()->request();
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        $fileresult =  GibberishAES::dec($request->getBody(), $apikey);
        $filesArray = json_decode($fileresult,TRUE);
//        echo "<pre>"; print_r($filesArray); echo "</pre>";
        $file_name     = $filesArray['old_file_name'];
        $id            = $filesArray['id'];
        $pid           = $filesArray['pid'];
        $encounter     = $filesArray['encounter'];
        $form_name     = $filesArray['form_name'];
        $datacheck     = 0;
        if(file_exists ("uploads/$pid/$encounter/$form_name/$file_name")){
            if(unlink("uploads/$pid/$encounter/$form_name/$file_name")===true){
                $datacheck = 1;
                $db = getConnection();
                $sql = "DELETE FROM tbl_mobile_wave_files WHERE  id='$id' ";
                $stmt = $db->prepare($sql) ;
            }
        }
        
        if($stmt->execute() && $datacheck == 1)
        {   
           $datares =  '[{"id":"1"}]';  
           echo $patientresult = GibberishAES::enc($datares, $apikey);
        }
        else
        {
            $datares = '[{"id":"0"}]';   
            echo $patientresult = GibberishAES::enc($datares, $apikey);
        }
    }catch(PDOException $e){
        $insertquery = '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo GibberishAES::enc($insertquery, $apikey);
    }
}
function createNewPatient(){
    $apikey = 'rotcoderaclla';
    // The default key size is 256 bits.
    $old_key_size = GibberishAES::size();
    GibberishAES::size(256);
    try
    {
        $db = getConnection();
        
        $patientsreminder = array();
//        $arraytexts = "";
//        $test2 = array($arraytexts);
        $listfields = " AND field_id IN ('title','fname','mname','lname','DOB','ss','phone_home','phone_cell','sex','street','city','state','country_code','postal_code','usertext1')";
        $get_layout2 = "SELECT group_name,list_id, title,field_id,form_id,
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
                                END as isRequired,description,max_length FROM layout_options WHERE form_id = 'DEM' $listfields AND uor<>0 order by group_name, seq";
            $db->query( "SET NAMES utf8");
            $stmt_layout2 = $db->prepare($get_layout2) ;
            $stmt_layout2->execute();                       
            $layout_fields2 = $stmt_layout2->fetchAll(PDO::FETCH_OBJ);
            if(!empty($layout_fields2)){
//                $patientsreminder[substr($layout_fields2[0]->group_name,1)]['form_id'] = $setform_id;
                for($i=0; $i< count($layout_fields2); $i++){
                    $pvalue =  '';

                    $patientsreminder[$i]['field_id'] = $layout_fields2[$i]->field_id;
                    $patientsreminder[$i]['field_type'] =  $layout_fields2[$i]->field_type;
                    $patientsreminder[$i]['isRequired'] =  $layout_fields2[$i]->isRequired;
                    $patientsreminder[$i]['label']  = $layout_fields2[$i]->title;
                    $patientsreminder[$i]['list_id']  = $layout_fields2[$i]->list_id;
                    $patientsreminder[$i]['placeholder']  = $layout_fields2[$i]->description;
                    if($layout_fields2[$i]->max_length == 0)
                        $maxlength = '';
                    else
                        $maxlength = $layout_fields2[$i]->max_length;
                    $patientsreminder[$i]['max_length']  = $maxlength;
                    $get_title2 = "SELECT option_id, title FROM list_options WHERE list_id = '".$layout_fields2[$i]->list_id."' order by seq";
                    $db->query( "SET NAMES utf8");
                    $title_stmt2 = $db->prepare($get_title2) ;
                    $title_stmt2->execute();                       

                    $settitle2 = $title_stmt2->fetchAll(PDO::FETCH_OBJ);
                    $titles= array();
                    if(!empty($settitle2)){
                        for($k = 0; $k< count($settitle2); $k++){
                            $titles[$k]['option_id'] = $settitle2[$k]->option_id;
                            $titles[$k]['option_value'] = $settitle2[$k]->title;
                        }
                    }
                    $patientsreminder[$i]['options']  = $titles;
                    if($layout_fields2[$i]->list_id != ''){
                        $patientsreminder[$i]['value'] = $pvalue;
                    }else{
                        if($layout_fields2[$i]->field_type == 'Static Text'){
                        $patientsreminder[$i]['value'] = $layout_fields2[$i]->description;
                    }else if($layout_fields2[$i]->field_type == 'Providers' || $layout_fields2[$i]->field_type == 'Providers NPI'){
                        $sql3 = "SELECT id, CONCAT(fname, ' ',lname) as name FROM `users`  WHERE active=1 AND authorized=1 ORDER BY fname,lname ";
                        $db->query( "SET NAMES utf8"); 
                        $stmt3 = $db->prepare($sql3) ;
                        $stmt3->execute();                       
                        $getListData = $stmt3->fetchAll(PDO::FETCH_OBJ);  
                        if(!empty($getListData)):
                            $data2 = array();
                            $j=0;
                            foreach($getListData as $list){
                                $data2[$j]['option_id'] = $list->id;
                                $data2[$j]['option_value'] = $list->name;
                                $j++;
                            }
                        else:
                            $data2 = '';
                        endif;
 
                        $patientsreminder[$i]['options']  = $data2;
                        $patientsreminder[$i]['value'] = $pvalue;
                    }else if($layout_fields2[$i]->field_type == 'Pharmacies'){
                        $sql3 = "SELECT id, name FROM `pharmacies`   ORDER BY name";
                        $db->query( "SET NAMES utf8"); 
                        $stmt3 = $db->prepare($sql3) ;
                        $stmt3->execute();                       
                        $getListData = $stmt3->fetchAll(PDO::FETCH_OBJ);  
                        if(!empty($getListData)):
                            $data2 = array();
                            $j=0;
                            foreach($getListData as $list){
                                $data2[$j]['option_id'] = $list->id;
                                $data2[$j]['option_value'] = $list->name;
                                $j++;
                            }
                        else:
                            $data2 = '';
                        endif;
 
                        $patientsreminder[$i]['options']  = $data2;
                        $patientsreminder[$i]['value'] = $pvalue;
                    }else if($layout_fields2[$i]->field_type == 'Organizations'){
                        $sql3 = "SELECT id, fname, lname, organization, username FROM users WHERE active = 1 AND ( info IS NULL OR info NOT LIKE '%Inactive%' ) AND ( username = '' OR authorized = 1 ) ORDER BY organization, lname, fname";
                        $db->query( "SET NAMES utf8"); 
                        $stmt3 = $db->prepare($sql3) ;
                        $stmt3->execute();                       
                        $getListData = $stmt3->fetchAll(PDO::FETCH_OBJ);  
                        if(!empty($getListData)):
                            $data2 = array();
                            $j=0;
                            foreach($getListData as $list){
                                $uname = $list->organization;
                                if (empty($uname) || substr($uname, 0, 1) == '(') {
                                    $uname = $list->lname;
                                    if ($list->fname) $uname .= ", " . $list->fname;
                                }
                                $data2[$j]['option_id'] = $list->id;
                                $data2[$j]['option_value'] = $uname;
                                $j++;
                            }
                        else:
                            $data2 = '';
                        endif;
 
                        $patientsreminder[$i]['options']  = $data2;
                        $patientsreminder[$i]['value'] = $pvalue;
                    }else if($layout_fields2[$i]->field_type == 'Patient allergies'){
                        $patientsreminder[$i]['value'] = '';
                    }else if($layout_fields2[$i]->field_type == 'Lifestyle status'){
                        $patientsreminder[$i]['value'] = $pvalue;
                    }else if($layout_fields2[$i]->field_type == 'Facilities'){
                        $sql3 = "SELECT id, name FROM facility ORDER BY name";
                        $db->query( "SET NAMES utf8"); 
                        $stmt3 = $db->prepare($sql3) ;
                        $stmt3->execute();                       
                        $getListData = $stmt3->fetchAll(PDO::FETCH_OBJ);  
                        if(!empty($getListData)):
                            $data2 = array();
                            $j=0;
                            foreach($getListData as $list){
                                $data2[$j]['option_id'] = $list->id;
                                $data2[$j]['option_value'] = $list->name;
                                $j++;
                            }
                        else:
                            $data2 = '';
                        endif;
 
                        $patientsreminder[$i]['options']  = $data2;
                        $patientsreminder[$i]['value'] = $pvalue;
                    }else if($layout_fields2[$i]->field_type == 'Date Of Service'){
                        $data['data']['list'] = '';
                   }else if($layout_fields2[$i]->field_type == 'Insurance Companies'){
                        $sql3 = "SELECT id,  name FROM `insurance_companies`  ORDER BY name";
                        $db->query( "SET NAMES utf8"); 
                        $stmt3 = $db->prepare($sql3) ;
                        $stmt3->execute();                       
                        $getListData = $stmt3->fetchAll(PDO::FETCH_OBJ);  
                        if(!empty($getListData)):
                            $data2 = array();
                            $j=0;
                            foreach($getListData as $list){
                                $data2[$j]['option_id'] = $list->id;
                                $data2[$j]['option_value'] = $list->name;
                                $j++;
                            }
                        else:
                            $data2 = '';
                        endif;
 
                        $patientsreminder[$i]['options']  = $data2;
                        $patientsreminder[$i]['value'] = $pvalue;
                    }else if($layout_fields2[$i]->field_type == 'Users'){
                        $sql3 = "SELECT id, CONCAT(fname, ' ',lname) as name FROM `users`  WHERE username <> '' ORDER BY fname,lname ";
                        $db->query( "SET NAMES utf8"); 
                        $stmt3 = $db->prepare($sql3) ;
                        $stmt3->execute();                       
                        $getListData = $stmt3->fetchAll(PDO::FETCH_OBJ);  
                        if(!empty($getListData)):
                            $data2 = array();
                            $j=0;
                            foreach($getListData as $list){
                                $data2[$j]['option_id'] = $list->id;
                                $data2[$j]['option_value'] = $list->name;
                                $j++;
                            }
                        else:
                            $data2 = '';
                        endif;
 
                        $patientsreminder[$i]['options']  = $data2;
                        $patientsreminder[$i]['value'] = $pvalue;
                    }else{
                        $patientsreminder[$i]['value'] = $pvalue;
                    }
                    }
                }
            }


//        echo "<pre>"; print_r($patientsreminder); echo "</pre>";
        if($patientsreminder){  
            $insertcheck =  json_encode($patientsreminder);
            echo GibberishAES::enc($insertcheck, $apikey);
	}else{
            $insertcheck = '[{"id":"0"}]';
            echo GibberishAES::enc($insertcheck, $apikey);
	}
    }
    catch(PDOException $e)
    {

        $patientres =  '{"error":{"text":'. $e->getMessage() .'}}';
        echo $patientresult = GibberishAES::enc($patientres, $apikey);
    }  
}
function defaultInsuranceData($insuranceType,$pid)
{
    $sqlInsertInsuranceData = "INSERT INTO insurance_data(type,provider,plan_name,policy_number,group_number,subscriber_lname,subscriber_mname, subscriber_fname,subscriber_relationship,subscriber_ss,subscriber_DOB,subscriber_street,
subscriber_postal_code,subscriber_city,subscriber_state,subscriber_country,
subscriber_phone,subscriber_employer,subscriber_employer_street,subscriber_employer_postal_code,
subscriber_employer_state,subscriber_employer_country,subscriber_employer_city,
copay,date,pid,subscriber_sex,accept_assignment,policy_type)
    VALUES('$insuranceType' ,'' ,'' ,'' ,'' ,'' ,'' ,'' ,'' ,'' ,'' ,'' ,'' ,'' ,'' ,'' ,'' ,'' ,'' ,'' ,'' ,'' ,'' ,'' ,'' ,'$pid' ,'' ,'' ,'')";
       
    return $sqlInsertInsuranceData;
}
function saveNewPatient(){
    try
    {
        $db = getConnection();
        $request = Slim::getInstance()->request();
        $apikey = 'rotcoderaclla';
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        $appres = GibberishAES::dec($request->getBody(), $apikey);
//        $appres = GibberishAES::dec('U2FsdGVkX191XG1W0L9LpRkp3TwZzKIYXqYaUqyHhOxJXeNLXdC36r6g9U9xVdFCIo6vV7LPW8HTlwCxurN1nuQe3xcj0L2WNnDHmdgtK+6EcEVlidI8SRcf62rhLWgq3pFZ3MwDxPhvLz065g1UjivKMInBDFcIleBZlHt2CZQpCETJohdMsQ4uhOuuMQPHvCzJTzznFz/EGapSLO0GgwUtzLfttC2BeqGeTjFc6eVU3eJqsGMOJb66CQP8mfquHhvvogp2YdCV5GG51r745jqDeeQxF6Y+b5BfipPIwHCyfJP/BOooEPXR1aqdQC1QoxIVCtb1yihrA0IHJ1CDPP295RxBC+H8nIUXVeBN6ZytbWJy5PevAdn4S6to5yhRvQh6SiRpGExP67U3zo4hqLQ0eaDTdpLXpxIcxLqb1aD3yMWXHyaLMYGc/YLVDlXu', $apikey);
        $insertArray = json_decode($appres,TRUE);
//        echo "<pre>"; print_r($insertArray); echo "</pre>";
        $pid            = $insertArray['pid'];
        $username       = $insertArray['username'];
        $userid         = $insertArray['user'];
        
        $data1 = $data2 = $data3 = $data4 = $data5 = $data6 = 0;
        $update_patient_data = $update_em_patient_data  = $update_patient_data_values2 = $update_em_patient_data_values2 = '';
        foreach($insertArray['data'][0] as $arraykey => $arrayvalue){
            $fieldnamesresult[$arraykey] = $arrayvalue;
        }
        
        
        $sqlGetMaxPid="SELECT MAX(pid) as max_pid FROM patient_data";
        $stmtGetMaxPid = $db->prepare($sqlGetMaxPid) ;           
       //$stmt->bindParam("date_today", $date_today);            
        $stmtGetMaxPid->execute();
        $maxPid = $stmtGetMaxPid->fetchAll(PDO::FETCH_OBJ);
        
        $pid = $maxPid[0]->max_pid+1;
        $pubpid=$pid;
        
        if(!empty($fieldnamesresult)){
            foreach($fieldnamesresult as $fkey => $fvalue){
                if($fkey != 'pid'){
                    if(strpos($fkey,'em_',0) === false){
                        if( $fkey != 'practice_status'){
                            $update_patient_data .= "`".$fkey."`,";
                            $update_patient_data_values2 .= "'".addslashes($fvalue)."',";
                        }
                    }else{
                        $update_em_patient_data .= "`".$fkey."`,";
                        $update_em_patient_data_values2 .= "'".addslashes($fvalue)."',";
                    }  
                }
            }

            if(!empty($update_patient_data)){
                $update_patient_data = rtrim($update_patient_data,',');
                $update_patient_data_values2 = rtrim($update_patient_data_values2,',');
                $query = "  INSERT INTO patient_data (`pid`,`pubpid`,`practice_status`,$update_patient_data,`deceased_date`) VALUES($pid,$pubpid,'YES',$update_patient_data_values2,'0000-00-00 00:00:00')";
                $stmtquery = $db->prepare($query); 
                if($stmtquery->execute()){
                    $data1 = 1;
                    insertMobileLog('insert',"$username",$query,$pid,'',"New Patient Data Screen", 1);
                }else{
                    insertMobileLog('insert',"$username",$query,$pid,'',"New Patient Data Screen - Failed", 0);
                }
            }else{
                $data1 = 1;
            }
            if(!empty($update_em_patient_data)){
                $update_em_patient_data = rtrim($update_em_patient_data,',');
                $update_em_patient_data_values2 = rtrim($update_em_patient_data_values2,',');
                $query2 = "  INSERT INTO employer_data (`pid`,$update_em_patient_data) VALUES($pid,$update_em_patient_data_values2)";
                $stmtquery2 = $db->prepare($query2); 
                if($stmtquery2->execute()){
                    $data1 = 1;
                    insertMobileLog('insert',"$username",$query2,'','',"New Employer Data Screen", 1);
                }else{
                    insertMobileLog('insert',"$username",$query2,'','',"New Employer Data Screen - Failed", 0);
                }
            } else {
                $data2 = 1;
            }
            // Insert in table 'history_data'
            $sqlInsertHistoryData = "INSERT INTO history_data(date,pid)
                                     VALUES(NOW(),'$pid')";           
            $stmtInsertHistoryData = $db->prepare($sqlInsertHistoryData) ;    
            if($stmtInsertHistoryData->execute()){
                $data3 = 1;
                insertMobileLog('insert',"$username",$sqlInsertHistoryData,$pid,'',"New History Data Screen", 1);
            }else{
                insertMobileLog('insert',"$username",$sqlInsertHistoryData,$pid,'',"New History Data Screen - Failed", 0);
            }

            // Insert in table 'insurance_data'
            $sqlInsertInsuranceData1 = defaultInsuranceData('primary',$pid);
            $stmtInsertInsuranceData1 = $db->prepare($sqlInsertInsuranceData1) ;     
            if($stmtInsertInsuranceData1->execute()){
                $data4 = 1;
                insertMobileLog('insert',"$username",$sqlInsertInsuranceData1,$pid,'',"New Insurance Data Primary Screen", 1);
            }else{
                insertMobileLog('insert',"$username",$sqlInsertInsuranceData1,$pid,'',"New Insurance Data Primary Screen - Failed", 0);
            }
            $sqlInsertInsuranceData2 = defaultInsuranceData('secondary',$pid);
            $stmtInsertInsuranceData2 = $db->prepare($sqlInsertInsuranceData2) ;      
            if($stmtInsertInsuranceData2->execute()){
                $data5 = 1;
                insertMobileLog('insert',"$username",$sqlInsertInsuranceData2,$pid,'',"New Insurance Data Secondary Screen", 1);
            }else{
                insertMobileLog('insert',"$username",$sqlInsertInsuranceData2,$pid,'',"New Insurance Data Secondary - Failed", 0);
            }
                
            $sqlInsertInsuranceData3 = defaultInsuranceData('tertiary',$pid);
            $stmtInsertInsuranceData3 = $db->prepare($sqlInsertInsuranceData3) ;     
            if($stmtInsertInsuranceData3->execute()){
                $data6 = 1;
                insertMobileLog('insert',"$username",$sqlInsertInsuranceData3,$pid,'',"New Insurance Data Tertiary Screen", 1);
            }else{
                insertMobileLog('insert',"$username",$sqlInsertInsuranceData3,$pid,'',"New Insurance Data Tertiary Screen - Failed", 0);
            }
           
        }
        if($data1 == 1 && $data2 == 1 && $data3 == 1 && $data4 == 1 && $data5 == 1 && $data6 == 1){  
            $insertcheck =  '[{"id":"1"}]';
            echo GibberishAES::enc($insertcheck, $apikey);
	}else{
            $insertcheck = '[{"id":"0"}]';
            echo GibberishAES::enc($insertcheck, $apikey);
	}
    }
    catch(PDOException $e)
    {

        $patientres =  '{"error":{"text":'. $e->getMessage() .'}}';
        echo $patientresult = GibberishAES::enc($patientres, $apikey);
        insertMobileLog('insert',"$username",$patientres,$pid,'',"New Patient/Employer Data Screen - Query Failed", 0);
    }  
}
function getProcedureOrder($id,$encounter,$pid){
    $apikey = 'rotcoderaclla';
    // The default key size is 256 bits.
    $old_key_size = GibberishAES::size();
    GibberishAES::size(256);
    try
    {
        $db = getConnection();
        $issues = array();
        
        $get_order_providers = "SELECT id, CONCAT(fname, ' ',lname) as name FROM `users`  WHERE active=1 AND authorized=1 ORDER BY fname,lname ";
        $db->query( "SET NAMES utf8");
        $prov_stmt = $db->prepare($get_order_providers);
        $prov_stmt->execute();
        $set_order_providers = $prov_stmt->fetchAll(PDO::FETCH_OBJ);
        
        if(!empty($set_order_providers)){
            for($i=0; $i< count($set_order_providers); $i++){
                $set_providers[$i]['option_id']  = $set_order_providers[$i]->id;
                $set_providers[$i]['option_title']  = $set_order_providers[$i]->name;
            }
        }
        
        $issues[0]['Label'] = 'Ordering Provider:';
        $issues[0]['Options'] = $set_providers;
        $issues[0]['Type'] = 'ListBox';
        $issues[0]['field_id'] = 'provider_id';
        
        $get_sending_to = "SELECT ppid, name FROM procedure_providers " .
                          "ORDER BY name, ppid";
        $db->query( "SET NAMES utf8");
        $proc_stmt = $db->prepare($get_sending_to);
        $proc_stmt->execute();
        $set_sending_to = $proc_stmt->fetchAll(PDO::FETCH_OBJ);
        
        if(!empty($set_sending_to)){
            for($i=0; $i< count($set_sending_to); $i++){
                $set_sending[$i]['option_id']  = $set_sending_to[$i]->ppid;
                $set_sending[$i]['option_title']  = $set_sending_to[$i]->name;
            }
        }
        $issues[1]['Label'] = 'Sending To:';
        $issues[1]['Options'] = $set_sending;
        $issues[1]['Type'] = 'ListBox';
        $issues[1]['field_id'] = 'lab_id';

        $issues[2]['Label'] = 'Order Date:';
        $issues[2]['Options'] = '';
        $issues[2]['field_id'] = 'date_ordered';
        $issues[2]['Type'] = 'Date';
        
        $issues[3]['Label'] = 'Internal Time Collected:';
        $issues[3]['Options'] = '';
        $issues[3]['field_id'] = 'date_collected';
        $issues[3]['Type'] = 'DateTime';
        
        $get_order_priority = "SELECT option_id, title FROM list_options WHERE list_id = 'ord_priority' ORDER BY title";
        $db->query( "SET NAMES utf8");
        $order_priority_stmt = $db->prepare($get_order_priority);
        $order_priority_stmt->execute();
        $set_order_priority = $order_priority_stmt->fetchAll(PDO::FETCH_OBJ);
        
        if(!empty($set_order_priority)){
            for($i=0; $i< count($set_order_priority); $i++){
                $order_priority[$i]['option_id']  = $set_order_priority[$i]->option_id;
                $order_priority[$i]['option_title']  = $set_order_priority[$i]->title;
            }
        }
        
        $issues[4]['Label'] = 'Priority:';
        $issues[4]['Options'] = $order_priority;
        $issues[4]['field_id'] = 'order_priority';
        $issues[4]['Type'] = 'ListBox';
        
        $get_order_status = "SELECT option_id, title FROM list_options WHERE list_id = 'ord_status' ORDER BY title";
        $db->query( "SET NAMES utf8");
        $order_status_stmt = $db->prepare($get_order_status);
        $order_status_stmt->execute();
        $set_order_status = $order_status_stmt->fetchAll(PDO::FETCH_OBJ);
        
        if(!empty($set_order_status)){
            for($i=0; $i< count($set_order_status); $i++){
                $order_status[$i]['option_id']  = $set_order_status[$i]->option_id;
                $order_status[$i]['option_title']  = $set_order_status[$i]->title;
            }
        }
        
        $issues[5]['Label'] = 'Status:';
        $issues[5]['Options'] = $order_status;
        $issues[5]['field_id'] = 'order_status';
        $issues[5]['Type'] = 'ListBox';
        
        $issues[6]['Label'] = 'Clinical History:';
        $issues[6]['Options'] = '';
        $issues[6]['field_id'] = 'clinical_hx';
        $issues[6]['Type'] = 'TextBox';
        
        $get_procedure_count = "SELECT procedure_code, procedure_name, diagnoses, procedure_order_seq FROM procedure_order_code WHERE procedure_order_id = '$id' ORDER BY procedure_order_seq";
        $db->query( "SET NAMES utf8");
        $order_stmt = $db->prepare($get_procedure_count);
        $order_stmt->execute();
        $set_procedure_count = $order_stmt->fetchAll(PDO::FETCH_OBJ);
        $k = 7;
        for($i=0; $i< count($set_procedure_count); $i++){
            $j = $k+$i;
            $count = $i+1 ;
            $issues[$j]["Procedure $count"]['procedure_seq'] = $set_procedure_count[$i]->procedure_order_seq;
            $issues[$j]["Procedure $count"]['procedure_data'][0]['Label'] = "";
            $issues[$j]["Procedure $count"]['procedure_data'][0]['Options'] = '';
            $issues[$j]["Procedure $count"]['procedure_data'][0]['field_id'] = 'procedure_name';
            $issues[$j]["Procedure $count"]['procedure_data'][0]['procedure_code'] = $set_procedure_count[$i]->procedure_code;
            $issues[$j]["Procedure $count"]['procedure_data'][0]['SelectedValue'] = $set_procedure_count[$i]->procedure_name;
            $issues[$j]["Procedure $count"]['procedure_data'][0]['Type'] = 'TextBox';
            
            $issues[$j]["Procedure $count"]['procedure_data'][1]['Label'] = "Diagnoses: ";
            $issues[$j]["Procedure $count"]['procedure_data'][1]['Options'] = '';
            $issues[$j]["Procedure $count"]['procedure_data'][1]['field_id'] = 'diagnoses';
            $issues[$j]["Procedure $count"]['procedure_data'][1]['SelectedValue'] = $set_procedure_count[$i]->diagnoses;
            $issues[$j]["Procedure $count"]['procedure_data'][1]['Type'] = 'TextBox';
        }
        
        $template["Procedure"]['procedure_seq'] = "";
        $template["Procedure"]['procedure_data'][0]['Label'] = "";
        $template["Procedure"]['procedure_data'][0]['Options'] = '';
        $template["Procedure"]['procedure_data'][0]['field_id'] = 'procedure_name';
        $template["Procedure"]['procedure_data'][0]['procedure_code'] = '';
        $template["Procedure"]['procedure_data'][0]['SelectedValue'] = '';
        $template["Procedure"]['procedure_data'][0]['Type'] = 'Clickable';
        
        $template["Procedure"]['procedure_data'][1]['Label'] = "Diagnoses: ";
        $template["Procedure"]['procedure_data'][1]['Options'] = '';
        $template["Procedure"]['procedure_data'][1]['field_id'] = 'diagnoses';
        $template["Procedure"]['procedure_data'][1]['SelectedValue'] = '';
        $template["Procedure"]['procedure_data'][1]['Type'] = 'Clickable';
        
        if($id != 0){
            $get_layout = "SELECT * FROM procedure_order WHERE encounter_id = '$encounter' and procedure_order_id = '$id' and patient_id= '$pid'";
            $db->query( "SET NAMES utf8");
            $stmt_layout = $db->prepare($get_layout) ;
            $stmt_layout->execute();

            $layout_fields = $stmt_layout->fetchAll(PDO::FETCH_OBJ);
            
        }

        
        $issues[0]['SelectedValue']     = isset($layout_fields[0]->provider_id)     ? $layout_fields[0]->provider_id    : '';
        $issues[1]['SelectedValue']     = isset($layout_fields[0]->lab_id)          ? $layout_fields[0]->lab_id         : '';
        $issues[2]['SelectedValue']     = isset($layout_fields[0]->date_ordered)    ? $layout_fields[0]->date_ordered   : '';
        $issues[3]['SelectedValue']     = isset($layout_fields[0]->date_collected)  ? $layout_fields[0]->date_collected : '';
        $issues[4]['SelectedValue']     = isset($layout_fields[0]->order_priority)  ? $layout_fields[0]->order_priority : '';
        $issues[5]['SelectedValue']     = isset($layout_fields[0]->order_status)    ? $layout_fields[0]->order_status   : '';
        $issues[6]['SelectedValue']     = isset($layout_fields[0]->clinical_hx)     ? $layout_fields[0]->clinical_hx    : '';
        $encform="SELECT form_id FROM forms WHERE formdir= 'LBF2' and deleted = 0 and encounter = $encounter and pid = $pid order by date asc";
            $encstmt31= $db->prepare($encform) ;
            $encform_id = $encstmt31->execute();
            $encformid_val = $encstmt31->fetchAll(PDO::FETCH_OBJ);
            $patientsreminder = array();   
            if(!empty($encformid_val))
                $formid = $encformid_val[0]-> form_id;
            else
                $formid = 0;
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
                                    END as isRequired,seq,max_length,description FROM layout_options WHERE form_id = 'LBF2' AND group_name LIKE '%Procedure' order by group_name, seq";
                $db->query( "SET NAMES utf8");
                $stmt_layout2 = $db->prepare($get_layout2) ;
                $stmt_layout2->execute();                       
                $layout_fields2 = $stmt_layout2->fetchAll(PDO::FETCH_OBJ);
                if(!empty($layout_fields2)){
                    $patientsreminder[substr($layout_fields2[0]->group_name,1)]['form_id'] = $formid;
                    for($i=0; $i< count($layout_fields2); $i++){
                        $sql = "select field_value from lbf_data where field_id  = '". $layout_fields2[$i]->field_id."' and form_id= '$formid'";   
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

//                if($Id != 0){
//                $vitals  = "SELECT field_value FROM lbf_data WHERE form_id = '".$encformid_val[0]->form_id."' AND field_id = 'vitals_%'";
//                $stmtV = $db->prepare($vitals);
//                $stmtV->execute();
//                $set_status = $stmtV->fetchAll(PDO::FETCH_OBJ);
//
//                if(!empty($set_status)){
//                    if($set_status[0]->field_value == 'pending')
//                        $vitalsdetails[0]-> vitals_stat = 'pending';
//                    elseif($set_status[0]->field_value == 'finalized')
//                        $vitalsdetails[0]-> vitals_stat = 'finalized';
//                    elseif($set_status[0]->field_value == 'finalized|pending' || $set_status[0]->field_value == 'pending|finalized')
//                        $$vitalsdetails[0]-> vitals_stat= 'finalized';
//                    else
//                        $field_value = '';
//                }else{
//                    if(!empty($vitalsdetails[0]-> vitals_stat))
//                        $vitalsdetails[0]-> vitals_stat= $vitalsdetails[0]-> vitals_stat;
//                    else
//                        $vitalsdetails[0]-> vitals_stat= '';
//                }
//            }else{
//                    $vitalsdetails[0]-> vitals_stat= '';
//            }

        $fullarray['Procedure'] = $issues;
        $fullarray['Template'] = $template;
        $fullarray['LBF2'] = $patientsreminder;
        
//                echo "<pre>"; print_r($fullarray); echo "</pre>";
        if($issues)
        {
            $patientres = json_encode(($fullarray));
            echo $patientresult = GibberishAES::enc($patientres, $apikey);
        }
        else
        {
            $patientres =  '[{"id":"0"}]';
            echo $patientresult = GibberishAES::enc($patientres, $apikey);
        }
    }
    catch(PDOException $e)
    {

        $patientres =  '{"error":{"text":'. $e->getMessage() .'}}';
        echo $patientresult = GibberishAES::enc($patientres, $apikey);
    }
}
function searchProcedure($labid,$search_term){
    $apikey = 'rotcoderaclla';
    // The default key size is 256 bits.
    $old_key_size = GibberishAES::size();
    GibberishAES::size(256);
    try
    {
        $db = getConnection();
        if($search_term == 'all'){
            $search_term2 = '%';
        }else {
            $search_term2 = $search_term;
        }
        
        $query = "SELECT procedure_type_id, procedure_code, name " .
            "FROM procedure_type WHERE " .
            "lab_id = '$labid' AND " .
            "procedure_type LIKE 'ord' AND " .
            "activity = 1 AND " .
            "(procedure_code LIKE '%$search_term2%' OR name LIKE '%$search_term2%') " .
            "ORDER BY seq, procedure_code";
        $db->query( "SET NAMES utf8");
        $stmt_layout = $db->prepare($query) ;
        $stmt_layout->execute();

        $layout_fields = $stmt_layout->fetchAll(PDO::FETCH_OBJ);
            
//        echo "<pre>"; print_r($layout_fields); echo "</pre>";
        if($layout_fields)
        {
            $patientres = json_encode(($layout_fields));
            echo $patientresult = GibberishAES::enc($patientres, $apikey);
        }
        else
        {
            $patientres =  '[{"id":"0"}]';
            echo $patientresult = GibberishAES::enc($patientres, $apikey);
        }
    }
    catch(PDOException $e)
    {

        $patientres =  '{"error":{"text":'. $e->getMessage() .'}}';
        echo $patientresult = GibberishAES::enc($patientres, $apikey);
    }
}
function saveProcedureOrder(){
    try
    {
        $db = getConnection();
        
        $request = Slim::getInstance()->request();
        $apikey = 'rotcoderaclla';
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        $appres = GibberishAES::dec($request->getBody(), $apikey);
//        $appres = GibberishAES::dec("U2FsdGVkX1/CCfP9zFn4PCeTRtAEBjlMEDq9HRijkx39Gq0JP+bcsYnmS9ks21MyHjpe5TgivenKYr1CVxH8w2kREkUlHVeJ9Z2sUh3q4i0XUmvK7Gs6UyD0SBZFLO4YT07GOoifSXRL5k5fQxbTd8XnTKG6bom8eCwlSknoS8gOGoE2m9U5XsiRe37G52zba4Bb9OaQX7hDlif+rrgmwBiTyKf9FXLtAM6JVQ10OxA6p6Bg1UYmAlOOpeOCdPiWR/yyJml63OpoKbK5qxzbGS7/VepQfwM8bQ96SyA5snwIZuuKTFBjvldc9yDFu98cKKgwePpCFsqhSZ9wkFEVW8haMsCaigFLLeoTvKhB9EkjN5kSfg2H1VIT6p2RPt4uE4daIOvjkehmksh1oUdNRBqM0a76S10y2pURv24kIn4YvdqFS3Jl2b8LEhXn4JtZzJwXQYUlHGgjgWfaZwYrxbJP98bmZP7VxKCVYiDZBk845npgy4htXvmsHSOvQT3COC6TcMvMqdVJoEiX7TX72GMZdv6C+XQ7w+C5R3I1MPLD2h9y3Pay+p675zdaYWecdIuA0t+Haa+YiTX3E2TyslMbO292CmQq/0pfs4BuXblUxVMdQs7HjnpjP0e/IpjAb+jv6E4EIjc/xAblW1FAhJjYRZ7EQRMCBjntV8LrYFuJr30dqKwDMEic8vlBXuwidKdd+zmle8LheSvs3C8TMSoWlshtun/04NDN37XcJ98O8HnqCZ/9Amdd9BLtnl1UQPmk1UG9fVGfyETYOu7bSFaV7dob9jjX3mINkFhH088NwMwR28scZbEVGHB0T3OwBiPOz4KwIiJHVGyzs4a2mNCotEXjM5dy2esDMTMZ+4b529ZS4/03RyboJnPStTScC5wDYLAIKBnqsmvyyiM/fOIrgRgJ34RBhphLYi857T+g24Zn9TEBjAcUB+g9uTgUerBBpBPo/ZqBpzKwe3eaETZTRO14Zq4CtyW5/6xBcrtu/8QkTOUkwZsRWyx7dhf7GAFpAHh0s3syNEFjsnIZN39OZP+gSB3kZcTOr4pOSJw92KKHyGF0gwz3b6r5hrZ5R3W//HdfwXCATTPTxRcapPUL6m4QgepmS1KM/kssp9Z3KTgsa4k6zRvuApfSOdpObVPy6UVQwdGE3O7rdVjSZ9siHjEvhTxDlbjMX1Ev9EwpysHRX8mZ9avxAQcJ9Pj6GqI0FxwPRWk578K8kcQrJ1SgCDeG09QaoOWbn+83OKBrSSNfekensJq5BsqYsOz8j7VcEKG/+NRJuCz9v5FSyFxFNp8rDtoGti/NM2TKzoA=", $apikey);
        $insertArray = json_decode($appres,TRUE);

//        echo "<pre>"; print_r($insertArray); echo "</pre>";
        $form_id            = $insertArray['form_id'];
        $pid                = $insertArray['pid'];
        $encounter          = $insertArray['encounter'];
        $username           = $insertArray['username'];
        
        $provider_id        = $insertArray['data'][0]['provider_id'];
        $lab_id             = $insertArray['data'][0]['lab_id'];
        $date_ordered       = $insertArray['data'][0]['date_ordered'];
        $date_collected     = $insertArray['data'][0]['date_collected'];
        $order_priority     = $insertArray['data'][0]['order_priority'];
        $order_status       = $insertArray['data'][0]['order_status'];
        $clinical_hx        = addslashes($insertArray['data'][0]['clinical_hx']);
        $procedure_array    = $insertArray['data'][0]['procedure'];
        $lbf2formid         = $insertArray['LBF2'][0]['formId'];
        $lbf2               = $insertArray['LBF2'][1];
        $data1 = $data2 = 0;
        $checkinsert2 = 'insert/update';
        
        if($form_id != 0){
            $sql2 = "UPDATE procedure_order  SET provider_id = '$provider_id',lab_id = '$lab_id', date_ordered = '$date_ordered' ,date_collected = '$date_collected',order_priority = '$order_priority',order_status = '$order_status', clinical_hx = '$clinical_hx' WHERE patient_id ='$pid'  AND encounter_id = '$encounter' AND  procedure_order_id  = '$form_id'";
            $checkinsert = 'update';
        }else{
            $get_max_procedure = "SELECT MAX(form_id) as form_id FROM forms WHERE formdir = 'procedure_order' AND form_name = 'Procedure Order'";
            $db->query( "SET NAMES utf8");
            $max_procedure_stmt = $db->prepare($get_max_procedure);
            $max_procedure_stmt->execute();
            $set_max_procedure = $max_procedure_stmt->fetchAll(PDO::FETCH_OBJ);
            if(!empty($set_max_procedure))
                $procedure_order_id = $set_max_procedure[0]->form_id + 1;
            else  
                $procedure_order_id = 1;
            $form_id = $procedure_order_id;
            $insertform = "INSERT INTO forms (date,encounter, form_name, form_id, pid, user, groupname, authorized, deleted, formdir) VALUES (NOW(), $encounter, 'Procedure Order', $procedure_order_id, $pid, '$username', 'Default', 1, 0, 'procedure_order' )";
            $insert_stmt = $db->prepare($insertform);
            $db->query( "SET NAMES utf8");
            if($insert_stmt->execute())
                insertMobileLog("insert","$username",$insertform,$pid,$encounter,"New Procedure Form Screen", 1);
            else
                insertMobileLog("insert","$username",$insertform,$pid,$encounter,"New Procedure Form Screen - Failed ", 0);
            
            $sql2 = "INSERT INTO procedure_order(procedure_order_id, provider_id, lab_id, date_ordered, date_collected, order_priority, order_status, clinical_hx, patient_id, encounter_id) VALUES ('$procedure_order_id' ,'$provider_id','$lab_id','$date_ordered','$date_collected','$order_priority','$order_status','$clinical_hx','$pid','$encounter')";
            $checkinsert = 'insert';
        }
        $db->query( "SET NAMES utf8");
        $stmt4 = $db->prepare($sql2);
        if($stmt4->execute()){
            $data = 1;
            insertMobileLog("$checkinsert","$username",$sql2,$pid,$encounter,"Save Procedure Data Screen", 1);
        }else{
            $data = 0;
            insertMobileLog("$checkinsert","$username",$sql2,$pid,$encounter,"Save Procedure Data Screen - Failed", 0);
        }

        if(!empty($procedure_array)){
            for($i=0; $i <count($procedure_array); $i++){
                $seq            = $procedure_array[$i]['procedure_seq'];
                $procedure_code = $procedure_array[$i]['procedure_code'];
                $diagnoses      = $procedure_array[$i]['diagnoses'];
                $procedure_name = $procedure_array[$i]['procedure_name'];
                if($seq == ''){
                    $j = $i+1;
                    $procedure_query = "INSERT INTO procedure_order_code (`procedure_order_id`,`procedure_order_seq`,`procedure_name`,`diagnoses`,`do_not_send`,`procedure_source`,`procedure_code`) VALUES ($form_id,$j,'$procedure_name','$diagnoses',0,1,'$procedure_code')";
                    $checkinsert2 = 'insert';
                }else{
                    $procedure_query = "UPDATE procedure_order_code SET procedure_name = '$procedure_name', procedure_code = '$procedure_code', diagnoses = '$diagnoses' WHERE procedure_order_id= $form_id AND procedure_order_seq = $seq";
                    $checkinsert2 = 'update';
                }
                $db->query( "SET NAMES utf8");
                $insert_procedure = $db->prepare($procedure_query);
                if($insert_procedure->execute()){
                    $data2 = 1;
                    insertMobileLog("$checkinsert2","$username",$procedure_query,$pid,$encounter,"Save Procedure Code Data Screen", 1);
                }else{
                    $data2 = 0;
                    insertMobileLog("$checkinsert2","$username",$procedure_query,$pid,$encounter,"Save Procedure Code Data Screen - Failed", 0);
                }
            }
        }
        
        // lbf2 data
//        $getformid1 = "SELECT form_id FROM forms WHERE formdir= 'LBF2' and deleted = 0 and encounter = '$encounter' and pid = '$pid' and form_id = $lbfformid order by date asc";
//        $stmt31 = $db->prepare($getformid1) ;
//        $stmt31->execute();
//        $formidval2 = $stmt31->fetchAll(PDO::FETCH_OBJ);

        if($lbf2formid == 0 ){
            $lastformid2 = "SELECT MAX(form_id) as forms FROM forms where formdir='LBF2'";
            $stmt5 = $db->prepare($lastformid2) ;
            $stmt5->execute();
            $maxformidval2 = $stmt5->fetchAll(PDO::FETCH_OBJ);
            $maxformid2 =  $maxformidval2[0]->forms + 1; 
            
            $insertform2 = "INSERT INTO forms (date,encounter, form_name, form_id, pid, user, groupname, authorized, deleted, formdir) VALUES (NOW(), $encounter, 'Allcare Encounter Forms', $maxformid2, $pid, '$username', 'Default', $authorized, 0, 'LBF2' )";
            $stmt4 = $db->prepare($insertform2);
            if($stmt4->execute()){
                insertMobileLog('insert',"$username",$insertform2,$pid,$encounter,"New Procedure LBF Form Screen", 1);
            }else{
                insertMobileLog('insert',"$username",$insertform2,$pid,$encounter,"New Procedure LBF Form Screen - Failed", 0);
            }
            $newformid3 = $db->lastInsertId();
            
            $getformid = "SELECT form_id FROM forms WHERE id = $newformid3";
            $stmt3 = $db->prepare($getformid) ;
            $stmt3->execute();
            $formidval = $stmt3->fetchAll(PDO::FETCH_OBJ);
            $newformid2 =  $formidval[0]->form_id;
        }else{
            $newformid2 = $lbf2formid;
        }
        foreach($lbf2 as $lbfkey => $lbfvalue){
            if (count($db->query("SELECT * FROM lbf_data WHERE form_id = $newformid2 AND field_id = '$lbfkey'")->fetchAll())==0) {
                $sql5 = "INSERT into lbf_data (form_id, field_id, field_value) VALUES ($newformid2,'$lbfkey','".addslashes($lbfvalue)."')";
                $checkstring = 'insert';
            } else {
               $sql5 = "UPDATE lbf_data SET field_value = '".addslashes($lbfvalue)."' WHERE field_id ='$lbfkey'  AND form_id = $newformid2";
               $checkstring = 'update';
            }
            $stmt41 = $db->prepare($sql5);
            if($stmt41->execute()){
                insertMobileLog($checkstring,"$username",$sql5,$pid,$encounter,"New Procedure LBF Form Data Screen", 1);
            }else{
                insertMobileLog($checkstring,"$username",$sql5,$pid,$encounter,"New Procedure LBF Form Data Screen - Failed", 0);
            }
        }
           
        if($data == 1 && $data2 == 1){  
            $insertcheck =  '[{"id":"1"}]';
            echo GibberishAES::enc($insertcheck, $apikey);
	}else{
            $insertcheck = '[{"id":"0"}]';
            echo GibberishAES::enc($insertcheck, $apikey);
	}
    }
    catch(PDOException $e)
    {

        $patientres =  '{"error":{"text":'. $e->getMessage() .'}}';
        echo $patientresult = GibberishAES::enc($patientres, $apikey);
        insertMobileLog("insert/update","$username",$patientres,$pid,$encounter,"Save Procedure Data Screen - Query Failed", 0);
    }  
}
function savePatientAgency(){
    try
    {
        $db = getConnection();
        $request = Slim::getInstance()->request();
        $apikey = 'rotcoderaclla';
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        $appres = GibberishAES::dec($request->getBody(), $apikey);
//        $appres =  GibberishAES::dec('U2FsdGVkX1+JiQPUvwMq9YmqFJ89WEKa8DncE7YyyPVRcAJroLlwNh3ARzJeVnu4nT6PWVz5qPykCnCZd/CbFWrN3I1cQXagVpCaapibplnkqed/NrurLRTxRDuNVpN94W+LC/XwdsWJ0UIQrLNxS2EeH5ZcuZyxgOBx+C7O17u8TfP+X4nBFzNpKFgFMKxQ73TXMzwXAWZjV2Jr1SfQuJjFFhruBjAcQn/rB82NeRsFz9d/s0A05tJCmVUKolYcFGse+AzJ2F//qig4g+qHiXx3c6/dZoEj261OWRzVTXs0kLfq1SSWsDlg6D8SV0ZUJgyq63cz8TTTUXhfiWqssg==', $apikey);
        $insertArray = json_decode($appres,TRUE);
//        echo "<pre>"; print_r($insertArray); echo "</pre>";
        $id             = $insertArray['id'];
        $pid            = $insertArray['pid'];
        $username       = $insertArray['username'];
        
        $data1 = $data2 = 0;
        $update_patient_data = $nameslist2  = $namesvalues2 = '';
        $fieldnames = "SELECT field_id
                        FROM layout_options WHERE form_id='AGENCY' AND uor <> 0 ORDER BY seq";
        $stmt2 = $db->prepare($fieldnames); 
        $stmt2->execute();
        $fieldnamesresult = $stmt2->fetchAll(PDO::FETCH_OBJ);

        if(!empty($fieldnamesresult)){
            for($i=0; $i< count($fieldnamesresult); $i++){
                foreach($fieldnamesresult[$i] as $fkey => $fvalue){
                    if($id == 0){
                        $nameslist2 .=  "`".$fvalue."`,";
                        $namesvalues2 .=  "'".addslashes($insertArray['data'][0][$fvalue])."',";
                    }else{
                        $update_patient_data .= "`".$fvalue."`= '".addslashes( $insertArray['data'][0][$fvalue])."',";
                    }
                }
            }
            if($id == 0){
                if(!empty($nameslist2) && !empty($namesvalues2)){
                    $nameslist2 = rtrim($nameslist2,',');
                    $namesvalues2 = rtrim($namesvalues2,',');
                    
                    $query = "  INSERT INTO tbl_patientagency ( patientid, $nameslist2 ) VALUES ($pid, $namesvalues2)";
                    $stmtquery = $db->prepare($query); 
                    if($stmtquery->execute()){
                        $data1 = 1;
                        insertMobileLog('insert',"$username",$query,$pid,'',"Edit Patient Agency Data Screen", 1);
                    }else{
                        insertMobileLog('insert',"$username",$query,$pid,'',"Edit Patient Agency Data Screen - Failed", 0);
                    }
                }else{
                    $data1 = 1;
                }
            }else{
                if(!empty($update_patient_data)){
                    $update_patient_data = rtrim($update_patient_data,',');
                    $query = "  UPDATE tbl_patientagency SET $update_patient_data WHERE patientid = $pid AND id = $id";
                    $stmtquery = $db->prepare($query); 
                    if($stmtquery->execute()){
                        $data1 = 1;
                        insertMobileLog('update',"$username",$query,$pid,'',"Edit Patient Agency Data Screen", 1);
                    }else{
                        insertMobileLog('update',"$username",$query,$pid,'',"Edit Patient Agency Data Screen - Failed", 0);
                    }
                }else{
                    $data1 = 1;
                }
            }
           
        }
        if($data1 == 1){  
            $insertcheck =  '[{"id":"1"}]';
            echo GibberishAES::enc($insertcheck, $apikey);
	}else{
            $insertcheck = '[{"id":"0"}]';
            echo GibberishAES::enc($insertcheck, $apikey);
	}
    }
    catch(PDOException $e)
    {

        $patientres =  '{"error":{"text":'. $e->getMessage() .'}}';
        echo $patientresult = GibberishAES::enc($patientres, $apikey);
        insertMobileLog('insert/update',"$username",$patientres,$pid,'',"Edit Patient Agency Data Screen - Query Failed", 0);
    }  
}
function getUsersCustomAttributes($uid){
    $apikey = 'rotcoderaclla';
    // The default key size is 256 bits.
    $old_key_size = GibberishAES::size();
    GibberishAES::size(256);
    try 
    {
        $db = getConnection();
        $get_layout = "SELECT field_id,title,list_id FROM layout_options WHERE form_id = 'UCA' and uor<> 0 order by group_name,seq";
        $db->query( "SET NAMES utf8");
        $stmt_layout = $db->prepare($get_layout) ;
        $stmt_layout->execute();                       
        $layout_fields = $stmt_layout->fetchAll(PDO::FETCH_OBJ);  
        if(!empty($layout_fields)){
            $enc_val = '';
            for($i = 0; $i<count($layout_fields); $i++){
               $enc_val .=  "`".$layout_fields[$i]->field_id."`,";//." as ". preg_replace('/[^a-zA-Z0-9_.]/', '', $layout_fields[$i]->title).",";//str_replace(" ","_",$layout_fields[$i]->title). ",";
            }

            $enc_value = rtrim($enc_val,",");
            if(!empty($enc_value))
                $enc_value = ",".$enc_value;
                $sql = "select id $enc_value from tbl_user_custom_attr_1to1 where userid = '$uid'";   
            $db->query( "SET NAMES utf8");
            $stmt = $db->prepare($sql) ;
            $stmt->execute();                       

            $patientsreminders = $stmt->fetchAll(PDO::FETCH_OBJ);    
            $patientsreminder = array();
            $patientsreminderval = array();
            if(!empty($patientsreminders)){
                for($i = 0 ; $i< count($patientsreminders); $i++){
                    $patientsreminder = array();
                    foreach($patientsreminders[$i] as $pkey => $pvalue){
                        $get_layout2 = "SELECT group_name,list_id, title FROM layout_options WHERE form_id = 'UCA' AND field_id = '$pkey' order by group_name,seq"; 
                        $db->query( "SET NAMES utf8");
                        $stmt_layout2 = $db->prepare($get_layout2) ;
                        $stmt_layout2->execute();                       
                        $layout_fields2 = $stmt_layout2->fetchAll(PDO::FETCH_OBJ);
                        if(!empty($layout_fields2)){
                            if($layout_fields2[0]->list_id != ''){
                                $exploded_val = explode('|', $pvalue);

                                $stringvalue = '';
                                for($j=0; $j<count($exploded_val); $j++){
                                    $get_title = "SELECT title FROM list_options WHERE option_id = '$exploded_val[$j]' AND list_id = '".$layout_fields2[0]->list_id."'";
                                    $db->query( "SET NAMES utf8");
                                    $title_stmt = $db->prepare($get_title) ;
                                    $title_stmt->execute();                       

                                    $settitle = $title_stmt->fetchAll(PDO::FETCH_OBJ);  
                                    if(!empty($settitle)){
                                       $stringvalue .= $settitle[0]->title.",";
                                    }
                                }
                                $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i][$layout_fields2[0]->title] = rtrim($stringvalue,',');
                            }else{
                                $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i][$layout_fields2[0]->title] = $pvalue;
                            }
                        }
//                        if($pkey == 'id')
//                            $patientsreminder[$i]['id'] = $pvalue;
                    }
                    $patientsreminderval[$i]['id']= $patientsreminders[$i]->id;
                    $patientsreminderval[$i]['value'] = $patientsreminder;
                }
            }
        }    
//        echo "<pre>"; print_r($patientsreminderval); echo "</pre>";
        if($patientsreminderval)
        {
            $patientres = json_encode($patientsreminderval); 
            echo $patientresult = GibberishAES::enc($patientres, $apikey);
        }
        else
        {
            //echo 'No patients available';
            $patientres =  '[{"id":"0"}]';
            echo $patientresult = GibberishAES::enc($patientres, $apikey);
        }
    } 
    catch(PDOException $e) 
    {

        $patientres =  '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo $patientresult = GibberishAES::enc($patientres, $apikey);
    }
}
function getUsersCred($uid){
    $apikey = 'rotcoderaclla';
    // The default key size is 256 bits.
    $old_key_size = GibberishAES::size();
    GibberishAES::size(256);
    try 
    {
        $db = getConnection();
        $get_layout = "SELECT field_id,title FROM layout_options WHERE form_id = 'USERS' and uor<> 0 order by group_name,seq";
        $db->query( "SET NAMES utf8");
        $stmt_layout = $db->prepare($get_layout) ;
        $stmt_layout->execute();                       

        $layout_fields = $stmt_layout->fetchAll(PDO::FETCH_OBJ);  
        if(!empty($layout_fields)){
            $enc_val = '';
            for($i = 0; $i<count($layout_fields); $i++){
               $enc_val .=  "`".$layout_fields[$i]->field_id."`,";
            }

            $enc_value = rtrim($enc_val,",");
            if(!empty($enc_value))
                $enc_value = ",".$enc_value;
            $sql = "select id $enc_value from tbl_patientuser where userid = '$uid'";   
            $db->query( "SET NAMES utf8");
            $stmt = $db->prepare($sql) ;
            $stmt->execute();                       

            $patientsreminders = $stmt->fetchAll(PDO::FETCH_OBJ);                        
            $patientsreminderval = array();
            $patientsreminder = array();
            if(!empty($patientsreminders)){
                for($i = 0 ; $i< count($patientsreminders); $i++){
                    $patientsreminder = array();
                    foreach($patientsreminders[$i] as $pkey => $pvalue){
                        $get_layout2 = "SELECT group_name,list_id, title FROM layout_options WHERE form_id = 'USERS' AND field_id = '$pkey' order by group_name,seq"; 
                        $db->query( "SET NAMES utf8");
                        $stmt_layout2 = $db->prepare($get_layout2) ;
                        $stmt_layout2->execute();                       
                        $layout_fields2 = $stmt_layout2->fetchAll(PDO::FETCH_OBJ);
                        if(!empty($layout_fields2)){
                            if($layout_fields2[0]->list_id != ''){
                                $exploded_val = explode('|', $pvalue);

                                $stringvalue = '';
                                for($j=0; $j<count($exploded_val); $j++){
                                    $exploded = str_replace(" ", "_", $exploded_val[$j]);
                                    $get_title = "SELECT title FROM list_options WHERE option_id = '$exploded' AND list_id = '".$layout_fields2[0]->list_id."'";
                                    $db->query( "SET NAMES utf8");
                                    $title_stmt = $db->prepare($get_title) ;
                                    $title_stmt->execute();                       

                                    $settitle = $title_stmt->fetchAll(PDO::FETCH_OBJ);  
                                    if(!empty($settitle)){
                                       $stringvalue .= $settitle[0]->title.",";
                                    }
                                }
                                $patientsreminder[substr($layout_fields2[0]->group_name,1)][$layout_fields2[0]->title] = rtrim($stringvalue,',');
                            }else{
                                $patientsreminder[substr($layout_fields2[0]->group_name,1)][$layout_fields2[0]->title] = $pvalue;
                            }
                        }
//                        if($pkey == 'id')
//                            $patientsreminder[$i]['id'] = $pvalue;
                    }
                    $patientsreminderval[$i]['id']= $patientsreminders[$i]->id;
                    $patientsreminderval[$i]['value'] = $patientsreminder;
                }
            }
        }    
//        echo "<pre>"; print_r($patientsreminderval); echo "</pre>";
        
        if($patientsreminderval)
        {
            $patientres = json_encode($patientsreminderval); 
            echo $patientresult = GibberishAES::enc($patientres, $apikey);
        }
        else
        {
            //echo 'No patients available';
            $patientres =  '[{"id":"0"}]';
            echo $patientresult = GibberishAES::enc($patientres, $apikey);
        }
    } 
    catch(PDOException $e) 
    {

        $patientres =  '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo $patientresult = GibberishAES::enc($patientres, $apikey);
    }
}
function getUsersPayroll($uid){
    $apikey = 'rotcoderaclla';
    // The default key size is 256 bits.
    $old_key_size = GibberishAES::size();
    GibberishAES::size(256);
    try 
    {
        $db = getConnection();
        $get_layout = "SELECT field_id,title FROM layout_options WHERE form_id = 'PAYROLL' and uor<> 0 order by group_name, seq";
        $db->query( "SET NAMES utf8");
        $stmt_layout = $db->prepare($get_layout) ;
        $stmt_layout->execute();                       

        $layout_fields = $stmt_layout->fetchAll(PDO::FETCH_OBJ);  
        if(!empty($layout_fields)){
            $enc_val = '';
            for($i = 0; $i<count($layout_fields); $i++){
               $enc_val .=  "`".$layout_fields[$i]->field_id."`,";
            }

            $enc_value = rtrim($enc_val,",");
            if(!empty($enc_value))
                $enc_value = ",".$enc_value;
            $sql = "select id $enc_value from tbl_userpayroll where userid = '$uid'";   
            $db->query( "SET NAMES utf8");
            $stmt = $db->prepare($sql) ;
            $stmt->execute();                       

            $patientsreminders = $stmt->fetchAll(PDO::FETCH_OBJ); 
            $patientsreminder = array();
            $patientsreminderval = array();
            if(!empty($patientsreminders)){
                for($i = 0 ; $i< count($patientsreminders); $i++){
                    $patientsreminder = array();
                    foreach($patientsreminders[$i] as $pkey => $pvalue){
                        $get_layout2 = "SELECT group_name,list_id, title FROM layout_options WHERE form_id = 'PAYROLL' AND field_id = '$pkey' order by group_name, seq"; 
                        $db->query( "SET NAMES utf8");
                        $stmt_layout2 = $db->prepare($get_layout2) ;
                        $stmt_layout2->execute();                       
                        $layout_fields2 = $stmt_layout2->fetchAll(PDO::FETCH_OBJ);
                        if(!empty($layout_fields2)){
                            if($layout_fields2[0]->list_id != ''){
                                $exploded_val = explode('|', $pvalue);

                                $stringvalue = '';
                                for($j=0; $j<count($exploded_val); $j++){
                                    $get_title = "SELECT title FROM list_options WHERE option_id = '$exploded_val[$j]' AND list_id = '".$layout_fields2[0]->list_id."'";
                                    $db->query( "SET NAMES utf8");
                                    $title_stmt = $db->prepare($get_title) ;
                                    $title_stmt->execute();                       

                                    $settitle = $title_stmt->fetchAll(PDO::FETCH_OBJ);  
                                    if(!empty($settitle)){
                                       $stringvalue .= $settitle[0]->title.",";
                                    }
                                }
                                $patientsreminder[substr($layout_fields2[0]->group_name,1)][$layout_fields2[0]->title] = rtrim($stringvalue,',');
                            }else{
                                $patientsreminder[substr($layout_fields2[0]->group_name,1)][$layout_fields2[0]->title] = $pvalue;
                            }
                        }
//                        if($pkey == 'id')
//                            $patientsreminder[$i]['id'] = $pvalue;
                    }
                    $patientsreminderval[$i]['id']= $patientsreminders[$i]->id;
                    $patientsreminderval[$i]['value'] = $patientsreminder;
                }
            }
        }    
//        echo "<pre>"; print_r($patientsreminderval); echo "</pre>";
        if($patientsreminderval)
        {
            $patientres = json_encode($patientsreminderval); 
            echo $patientresult = GibberishAES::enc($patientres, $apikey);
        }
        else
        {
            $patientres =  '[{"id":"0"}]';
            echo $patientresult = GibberishAES::enc($patientres, $apikey);
        }
    } 
    catch(PDOException $e) 
    {

        $patientres =  '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo $patientresult = GibberishAES::enc($patientres, $apikey);
    }
}
function editUsersCustomAttributes($uid,$id){
    $apikey = 'rotcoderaclla';
    // The default key size is 256 bits.
    $old_key_size = GibberishAES::size();
    GibberishAES::size(256);
    try 
    {
        $db = getConnection();
        $get_layout = "SELECT field_id,title FROM layout_options WHERE form_id = 'UCA' and uor<> 0 order by group_name, seq";
        $db->query( "SET NAMES utf8");
        $stmt_layout = $db->prepare($get_layout) ;
        $stmt_layout->execute();                       

        $layout_fields = $stmt_layout->fetchAll(PDO::FETCH_OBJ);  
        $enc_val = '';
        for($i = 0; $i<count($layout_fields); $i++){
           $enc_val .=  "`".$layout_fields[$i]->field_id."`,";//." as ". preg_replace('/[^a-zA-Z0-9_.]/', '', $layout_fields[$i]->title).",";//str_replace(" ","_",$layout_fields[$i]->title). ",";
        }

        $enc_value = rtrim($enc_val,",");

        $sql = "select $enc_value from tbl_user_custom_attr_1to1 where userid = '$uid' and id= '$id'";   
        $db->query( "SET NAMES utf8");
        $stmt = $db->prepare($sql) ;
        $stmt->execute();                       

        $patientsreminders = $stmt->fetchAll(PDO::FETCH_OBJ);    
        $patientsreminder = array();
        $i = 0;
        if(!empty($patientsreminders)){
            foreach($patientsreminders[0] as $pkey => $pvalue){
                $get_layout2 = "SELECT group_name,list_id, title,
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
                                END as isRequired,description FROM layout_options WHERE form_id = 'UCA' AND field_id = '$pkey' order by group_name, seq";
                $db->query( "SET NAMES utf8");
                $stmt_layout2 = $db->prepare($get_layout2) ;
                $stmt_layout2->execute();                       
                $layout_fields2 = $stmt_layout2->fetchAll(PDO::FETCH_OBJ);
                if(!empty($layout_fields2)){
                    $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['field_id'] = $pkey;
                    $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['field_type'] =  $layout_fields2[0]->field_type;
                    $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['isRequired'] =  $layout_fields2[0]->isRequired;
                    $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['label']  = $layout_fields2[0]->title;
                    $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['list_id']  = $layout_fields2[0]->list_id;
                    $get_title2 = "SELECT option_id, title FROM list_options WHERE list_id = '".$layout_fields2[0]->list_id."' order by seq";
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
                    $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['options']  = $titles;
                    if($layout_fields2[0]->list_id != ''){
                        $exploded_val = explode('|', $pvalue);

//                        $stringvalue = '';
//                        for($j=0; $j<count($exploded_val); $j++){
//                            $get_title = "SELECT title,option_id FROM list_options WHERE option_id = '$exploded_val[$j]' AND list_id = '".$layout_fields2[0]->list_id."'";
//                            $db->query( "SET NAMES utf8");
//                            $title_stmt = $db->prepare($get_title) ;
//                            $title_stmt->execute();                       
//
//                            $settitle = $title_stmt->fetchAll(PDO::FETCH_OBJ);  
//                            if(!empty($settitle)){
//                               $stringvalue .= $settitle[0]->option_id.",";
//                            }
//                        }
                        $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['value'] =$pvalue;
                    }else{
                        if($layout_fields2[0]->field_type == 'Static Text'){
                        $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['value'] = $layout_fields2[0]->description;
                    }else if($layout_fields2[0]->field_type == 'Providers' || $layout_fields2[0]->field_type == 'Providers NPI'){
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
                        $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['options']  = $data2;
                        $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['value'] = $pvalue;
                    }else if($layout_fields2[0]->field_type == 'Pharmacies'){
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
                        $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['options']  = $data2;
                        $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['value'] = $pvalue;
                    }else if($layout_fields2[0]->field_type == 'Organizations'){
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
                        $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['options']  = $data2;
                        $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['value'] = $pvalue;
                    }else if($layout_fields2[0]->field_type == 'Patient allergies'){
                        $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['value'] = '';
                    }else if($layout_fields2[0]->field_type == 'Lifestyle status'){
                        $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['value'] = $pvalue;
                    }else if($layout_fields2[0]->field_type == 'Facilities'){
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
                        $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['options']  = $data2;
                        $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['value'] = $pvalue;
                    }else if($layout_fields2[0]->field_type == 'Date Of Service'){
                        $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['options']  = '';
                        $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['value'] = '';
                    }else if($layout_fields2[0]->field_type == 'Insurance Companies'){
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
                        $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['options']  = $data2;
                        $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['value'] = $pvalue;
                    }else if($layout_fields2[0]->field_type == 'Users'){
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
                        $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['options']  = $data2;
                        $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['value'] = $pvalue;
                    }else{
                        $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['value'] = $pvalue;
                    }
                    }
                }
                $i++;
            }
        }
//        echo "<pre>"; print_r($patientsreminder); echo "</pre>";
        if($patientsreminder)
        {
            $patientres = json_encode($patientsreminder); 
            echo $patientresult = GibberishAES::enc($patientres, $apikey);
//            echo $fileresult =  GibberishAES::dec($patientresult, $apikey);
        }
        else
        {
            $patientres =  '[{"id":"0"}]';
            echo $patientresult = GibberishAES::enc($patientres, $apikey);
        }
    } 
    catch(PDOException $e) 
    {

        $patientres =  '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo $patientresult = GibberishAES::enc($patientres, $apikey);
    }
}
function editUsersCred($uid,$id){
    $apikey = 'rotcoderaclla';
    // The default key size is 256 bits.
    $old_key_size = GibberishAES::size();
    GibberishAES::size(256);
    try 
    {
        $db = getConnection();
        $get_layout = "SELECT field_id,title FROM layout_options WHERE form_id = 'USERS' and uor<> 0 order by group_name, seq";
        $db->query( "SET NAMES utf8");
        $stmt_layout = $db->prepare($get_layout) ;
        $stmt_layout->execute();                       

        $layout_fields = $stmt_layout->fetchAll(PDO::FETCH_OBJ);  
        $enc_val = '';
        for($i = 0; $i<count($layout_fields); $i++){
           $enc_val .=  "`".$layout_fields[$i]->field_id."`,";//." as ". preg_replace('/[^a-zA-Z0-9_.]/', '', $layout_fields[$i]->title).",";//str_replace(" ","_",$layout_fields[$i]->title). ",";
        }

        $enc_value = rtrim($enc_val,",");

        $sql = "select $enc_value from tbl_patientuser where userid = '$uid' and id= '$id'";   
        $db->query( "SET NAMES utf8");
        $stmt = $db->prepare($sql) ;
        $stmt->execute();                       

        $patientsreminders = $stmt->fetchAll(PDO::FETCH_OBJ);    
        $patientsreminder = array();
        $i = 0;
        foreach($patientsreminders[0] as $pkey => $pvalue){
            $get_layout2 = "SELECT group_name,list_id, title,
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
            END as field_type,
                        CASE uor
                            WHEN 1 THEN 'Optional'
                            WHEN 2 THEN 'Required'
                            END as isRequired,description FROM layout_options WHERE form_id = 'USERS' AND field_id = '$pkey' order by group_name, seq"; 
            $db->query( "SET NAMES utf8");
            $stmt_layout2 = $db->prepare($get_layout2) ;
            $stmt_layout2->execute();                       
            $layout_fields2 = $stmt_layout2->fetchAll(PDO::FETCH_OBJ);
            if(!empty($layout_fields2)){
                $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['field_id'] = $pkey;
                $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['field_type'] =  $layout_fields2[0]->field_type;
                $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['isRequired'] =  $layout_fields2[0]->isRequired;
                $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['label']  = $layout_fields2[0]->title;
                $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['list_id']  = $layout_fields2[0]->list_id;
                $get_title2 = "SELECT option_id, title FROM list_options WHERE list_id = '".$layout_fields2[0]->list_id."'";
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
                $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['options']  = $titles;
                if($layout_fields2[0]->list_id != ''){
                    $exploded_val = explode('|', $pvalue);
                    $stringvalue = '';
//                    for($j=0; $j<count($exploded_val); $j++){
//                        $exploded = str_replace(" ", "_", $exploded_val[$j]);
//                        $get_title = "SELECT title,option_id FROM list_options WHERE option_id = '$exploded' AND list_id = '".$layout_fields2[0]->list_id."'";
//                        $db->query( "SET NAMES utf8");
//                        $title_stmt = $db->prepare($get_title) ;
//                        $title_stmt->execute();                       
//
//                        $settitle = $title_stmt->fetchAll(PDO::FETCH_OBJ);  
//                        if(!empty($settitle)){
//                           $stringvalue .=  $settitle[0]->option_id.",";
//                        }
//                    }
                    $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['value'] = $pvalue;//rtrim($stringvalue,',');
                }else{
                    if($layout_fields2[0]->field_type == 'Static Text'){
                        $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['value'] = $layout_fields2[0]->description;
                    }else if($layout_fields2[0]->field_type == 'Providers' || $layout_fields2[0]->field_type == 'Providers NPI'){
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
                        $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['options']  = $data2;
                        $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['value'] = $pvalue;
                    }else if($layout_fields2[0]->field_type == 'Pharmacies'){
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
                        $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['options']  = $data2;
                        $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['value'] = $pvalue;
                    }else if($layout_fields2[0]->field_type == 'Organizations'){
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
                        $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['options']  = $data2;
                        $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['value'] = $pvalue;
                    }else if($layout_fields2[0]->field_type == 'Patient allergies'){
                        $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['value'] = '';
                    }else if($layout_fields2[0]->field_type == 'Lifestyle status'){
                        $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['value'] = $pvalue;
                    }else if($layout_fields2[0]->field_type == 'Facilities'){
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
                        $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['options']  = $data2;
                        $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['value'] = $pvalue;
                    }else if($layout_fields2[0]->field_type == 'Date Of Service'){
                        $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['options']  = '';
                        $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['value'] = '';
                    }else if($layout_fields2[0]->field_type == 'Insurance Companies'){
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
                        $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['options']  = $data2;
                        $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['value'] = $pvalue;
                    }else if($layout_fields2[0]->field_type == 'Users'){
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
                        $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['options']  = $data2;
                        $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['value'] = $pvalue;
                    }else{
                        $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['value'] = $pvalue;
                    }
                }
            }
            $i++;
        }
      
//        echo "<pre>"; print_r($patientsreminder); echo "</pre>";
        if($patientsreminder)
        {
            $patientres = json_encode($patientsreminder); 
            echo $patientresult = GibberishAES::enc($patientres, $apikey);
        }
        else
        {
            $patientres =  '[{"id":"0"}]';
            echo $patientresult = GibberishAES::enc($patientres, $apikey);
        }
    } 
    catch(PDOException $e) 
    {

        $patientres =  '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo $patientresult = GibberishAES::enc($patientres, $apikey);
    }
}
function editUsersPayroll($uid,$id){
    $apikey = 'rotcoderaclla';
    // The default key size is 256 bits.
    $old_key_size = GibberishAES::size();
    GibberishAES::size(256);
    try 
    {
        $db = getConnection();
        $get_layout = "SELECT field_id,title FROM layout_options WHERE form_id = 'PAYROLL' and uor<> 0 order by group_name, seq";
        $db->query( "SET NAMES utf8");
        $stmt_layout = $db->prepare($get_layout) ;
        $stmt_layout->execute();                       

        $layout_fields = $stmt_layout->fetchAll(PDO::FETCH_OBJ);  
        $enc_val = '';
        for($i = 0; $i<count($layout_fields); $i++){
           $enc_val .=  "`".$layout_fields[$i]->field_id."`,";//." as ". preg_replace('/[^a-zA-Z0-9_.]/', '', $layout_fields[$i]->title).",";//str_replace(" ","_",$layout_fields[$i]->title). ",";
        }

        $enc_value = rtrim($enc_val,",");

        $sql = "select $enc_value from tbl_userpayroll where userid = '$uid' and id= '$id'";   
        $db->query( "SET NAMES utf8");
        $stmt = $db->prepare($sql) ;
        $stmt->execute();                       

        $patientsreminders = $stmt->fetchAll(PDO::FETCH_OBJ);    
        $patientsreminder = array();
        $i = 0;
        foreach($patientsreminders[0] as $pkey => $pvalue){
            $get_layout2 = "SELECT group_name,list_id, title,
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
            END as field_type,
                        CASE uor
                            WHEN 1 THEN 'Optional'
                            WHEN 2 THEN 'Required'
                            END as isRequired,description FROM layout_options WHERE form_id = 'PAYROLL' AND field_id = '$pkey' order by group_name, seq"; 
            $db->query( "SET NAMES utf8");
            $stmt_layout2 = $db->prepare($get_layout2) ;
            $stmt_layout2->execute();                       
            $layout_fields2 = $stmt_layout2->fetchAll(PDO::FETCH_OBJ);
            if(!empty($layout_fields2)){
                $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['field_id'] = $pkey; 
                $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['isRequired'] =  $layout_fields2[0]->isRequired;
                $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['field_type'] =  $layout_fields2[0]->field_type;
                $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['label']  = $layout_fields2[0]->title;
                $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['list_id']  = $layout_fields2[0]->list_id;
                $get_title2 = "SELECT option_id, title FROM list_options WHERE list_id = '".$layout_fields2[0]->list_id."'";
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
                $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['options']  = $titles;
                if($layout_fields2[0]->list_id != ''){
                    $exploded_val = explode('|', $pvalue);

//                    $stringvalue = '';
//                    for($j=0; $j<count($exploded_val); $j++){
//                        $get_title = "SELECT title,option_id FROM list_options WHERE option_id = '$exploded_val[$j]' AND list_id = '".$layout_fields2[0]->list_id."'";
//                        $db->query( "SET NAMES utf8");
//                        $title_stmt = $db->prepare($get_title) ;
//                        $title_stmt->execute();                       
//
//                        $settitle = $title_stmt->fetchAll(PDO::FETCH_OBJ);  
//                        if(!empty($settitle)){
//                           $stringvalue .= $settitle[0]->option_id.",";
//                        }
//                    }
                    $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['value'] = $pvalue; //rtrim($stringvalue,',');
                }else{
                    if($layout_fields2[0]->field_type == 'Static Text'){
                        $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['value'] = $layout_fields2[0]->description;
                    }else if($layout_fields2[0]->field_type == 'Providers' || $layout_fields2[0]->field_type == 'Providers NPI'){
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
                        $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['options']  = $data2;
                        $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['value'] = $pvalue;
                    }else if($layout_fields2[0]->field_type == 'Pharmacies'){
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
                        $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['options']  = $data2;
                        $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['value'] = $pvalue;
                    }else if($layout_fields2[0]->field_type == 'Organizations'){
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
                        $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['options']  = $data2;
                        $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['value'] = $pvalue;
                    }else if($layout_fields2[0]->field_type == 'Patient allergies'){
                        $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['value'] = '';
                    }else if($layout_fields2[0]->field_type == 'Lifestyle status'){
                        $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['value'] = $pvalue;
                    }else if($layout_fields2[0]->field_type == 'Facilities'){
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
                        $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['options']  = $data2;
                        $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['value'] = $pvalue;
                    }else if($layout_fields2[0]->field_type == 'Date Of Service'){
                        $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['options']  = '';
                        $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['value'] = '';
                    }else if($layout_fields2[0]->field_type == 'Insurance Companies'){
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
                        $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['options']  = $data2;
                        $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['value'] = $pvalue;
                    }else if($layout_fields2[0]->field_type == 'Users'){
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
                        $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['options']  = $data2;
                        $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['value'] = $pvalue;
                    }else{
                        $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['value'] = $pvalue;
                    }
                }
            }
            $i++;
        }
      
//        echo "<pre>"; print_r($patientsreminder); echo "</pre>";
        if($patientsreminder)
        {
            $patientres = json_encode($patientsreminder); 
            echo $patientresult = GibberishAES::enc($patientres, $apikey);
        }
        else
        {
            $patientres =  '[{"id":"0"}]';
            echo $patientresult = GibberishAES::enc($patientres, $apikey);
        }
    } 
    catch(PDOException $e) 
    {

        $patientres =  '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo $patientresult = GibberishAES::enc($patientres, $apikey);
    }
    
}
function createUsersCustomAttributes($uid){
    $apikey = 'rotcoderaclla';
    // The default key size is 256 bits.
    $old_key_size = GibberishAES::size();
    GibberishAES::size(256);
    try 
    {
        $db = getConnection();

        $sql = "SELECT group_name,field_id,title,list_id,
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
        END as field_type,
                        CASE uor
                            WHEN 1 THEN 'Optional'
                            WHEN 2 THEN 'Required'
                            END as isRequired FROM layout_options WHERE form_id = 'UCA' and uor<> 0 order by group_name, seq";   
        $db->query( "SET NAMES utf8");
        $stmt = $db->prepare($sql) ;
        $stmt->execute();                       

        $patientsreminders = $stmt->fetchAll(PDO::FETCH_OBJ);    
        $patientsreminder = array();
        if(!empty($patientsreminders)){
            for($i=0; $i<count($patientsreminders); $i++){
                $patientsreminder[substr($patientsreminders[$i]->group_name,1)][$i]['field_id']  = $patientsreminders[$i]->field_id;
                $patientsreminder[substr($patientsreminders[$i]->group_name,1)][$i]['isRequired']  = $patientsreminders[$i]->isRequired;
                $patientsreminder[substr($patientsreminders[$i]->group_name,1)][$i]['field_type']  = $patientsreminders[$i]->field_type;
                $patientsreminder[substr($patientsreminders[$i]->group_name,1)][$i]['label']  = $patientsreminders[$i]->title;
                $patientsreminder[substr($patientsreminders[$i]->group_name,1)][$i]['list_id']  =$patientsreminders[$i]->list_id;
                $get_title2 = "SELECT option_id, title FROM list_options WHERE list_id = '".$patientsreminders[$i]->list_id."'";
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
                $patientsreminder[substr($patientsreminders[$i]->group_name,1)][$i]['options']  = $titles;
                if($patientsreminders[$i]->field_type == 'Static Text'){
                        $patientsreminder[substr($patientsreminders[$i]->group_name,1)][$i]['value'] = $patientsreminders[$i]->description;
                    }else if($patientsreminders[$i]->field_type == 'Providers' || $patientsreminders[$i]->field_type == 'Providers NPI'){
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
                        $patientsreminder[substr($patientsreminders[$i]->group_name,1)][$i]['options']  = $data2;
                        $patientsreminder[substr($patientsreminders[$i]->group_name,1)][$i]['value'] = '';
                    }else if($patientsreminders[$i]->field_type == 'Pharmacies'){
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
                        $patientsreminder[substr($patientsreminders[$i]->group_name,1)][$i]['options']  = $data2;
                        $patientsreminder[substr($patientsreminders[$i]->group_name,1)][$i]['value'] = '';
                    }else if($patientsreminders[$i]->field_type == 'Organizations'){
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
                        $patientsreminder[substr($patientsreminders[$i]->group_name,1)][$i]['options']  = $data2;
                        $patientsreminder[substr($patientsreminders[$i]->group_name,1)][$i]['value'] = '';
                    }else if($patientsreminders[$i]->field_type == 'Patient allergies'){
                        $patientsreminder[substr($patientsreminders[$i]->group_name,1)][$i]['value'] = '';
                    }else if($patientsreminders[$i]->field_type == 'Lifestyle status'){
                        $patientsreminder[substr($patientsreminders[$i]->group_name,1)][$i]['value'] = '';
                    }else if($patientsreminders[$i]->field_type == 'Facilities'){
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
                        $patientsreminder[substr($patientsreminders[$i]->group_name,1)][$i]['options']  = $data2;
                        $patientsreminder[substr($patientsreminders[$i]->group_name,1)][$i]['value'] = '';
                    }else if($patientsreminders[$i]->field_type == 'Date Of Service'){
                        $patientsreminder[substr($patientsreminders[$i]->group_name,1)][$i]['options']  = '';
                        $patientsreminder[substr($patientsreminders[$i]->group_name,1)][$i]['value'] = '';
                    }else if($patientsreminders[$i]->field_type == 'Insurance Companies'){
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
                        $patientsreminder[substr($patientsreminders[$i]->group_name,1)][$i]['options']  = $data2;
                        $patientsreminder[substr($patientsreminders[$i]->group_name,1)][$i]['value'] = '';
                    }else if($patientsreminders[$i]->field_type == 'Users'){
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
                        $patientsreminder[substr($patientsreminders[$i]->group_name,1)][$i]['options']  = $data2;
                        $patientsreminder[substr($patientsreminders[$i]->group_name,1)][$i]['value'] = '';
                    }else{
                        $patientsreminder[substr($patientsreminders[$i]->group_name,1)][$i]['value'] = '';
                    }
             
            }
        }
//        echo "<pre>"; print_r($patientsreminder); echo "</pre>";
        if($patientsreminder)
        {
            $patientres = json_encode($patientsreminder); 
            echo $patientresult = GibberishAES::enc($patientres, $apikey);
        }
        else
        {
            $patientres =  '[{"id":"0"}]';
            echo $patientresult = GibberishAES::enc($patientres, $apikey);
        }
    } 
    catch(PDOException $e) 
    {

        $patientres =  '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo $patientresult = GibberishAES::enc($patientres, $apikey);
    }
}
function createUsersPayroll($uid){
    $apikey = 'rotcoderaclla';
    // The default key size is 256 bits.
    $old_key_size = GibberishAES::size();
    GibberishAES::size(256);
    try 
    {
        $db = getConnection();

        $sql = "SELECT group_name,field_id,title,list_id,
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
        END as field_type,
                        CASE uor
                            WHEN 1 THEN 'Optional'
                            WHEN 2 THEN 'Required'
                            END as isRequired FROM layout_options WHERE form_id = 'PAYROLL' and uor<> 0 order by group_name, seq";   
        $db->query( "SET NAMES utf8");
        $stmt = $db->prepare($sql) ;
        $stmt->execute();                       

        $patientsreminders = $stmt->fetchAll(PDO::FETCH_OBJ);    
        $patientsreminder = array();
        if(!empty($patientsreminders)){
            for($i=0; $i<count($patientsreminders); $i++){
                $patientsreminder[substr($patientsreminders[$i]->group_name,1)][$i]['field_id']  = $patientsreminders[$i]->field_id;
                $patientsreminder[substr($patientsreminders[$i]->group_name,1)][$i]['isRequired']  = $patientsreminders[$i]->isRequired;
                $patientsreminder[substr($patientsreminders[$i]->group_name,1)][$i]['field_type']  = $patientsreminders[$i]->field_type;
                $patientsreminder[substr($patientsreminders[$i]->group_name,1)][$i]['label']  = $patientsreminders[$i]->title;
                $patientsreminder[substr($patientsreminders[$i]->group_name,1)][$i]['list_id']  =$patientsreminders[$i]->list_id;
                $get_title2 = "SELECT option_id, title FROM list_options WHERE list_id = '".$patientsreminders[$i]->list_id."'";
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
                $patientsreminder[substr($patientsreminders[$i]->group_name,1)][$i]['options']  = $titles;
//                $patientsreminder[substr($patientsreminders[$i]->group_name,1)][$i]['value'] = '';
//                if($patientsreminders[$i]->list_id != ''){
//                    $exploded_val = explode('|', $pvalue);
//
//                    $stringvalue = '';
//                    for($j=0; $j<count($exploded_val); $j++){
//                        $get_title = "SELECT title,option_id FROM list_options WHERE option_id = '$exploded_val[$j]' AND list_id = '".$patientsreminders[$i]->list_id."'";
//                        $db->query( "SET NAMES utf8");
//                        $title_stmt = $db->prepare($get_title) ;
//                        $title_stmt->execute();                       
//
//                        $settitle = $title_stmt->fetchAll(PDO::FETCH_OBJ);  
//                        if(!empty($settitle)){
//                           $stringvalue .= $settitle[0]->option_id.",";
//                        }
//                    }
//                    $patientsreminder[substr($patientsreminders[$i]->group_name,1)][$i]['value'] = '';
//                }else{
                    if($patientsreminders[$i]->field_type == 'Static Text'){
                        $patientsreminder[substr($patientsreminders[$i]->group_name,1)][$i]['value'] = $patientsreminders[$i]->description;
                    }else if($patientsreminders[$i]->field_type == 'Providers' || $patientsreminders[$i]->field_type == 'Providers NPI'){
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
                        $patientsreminder[substr($patientsreminders[$i]->group_name,1)][$i]['options']  = $data2;
                        $patientsreminder[substr($patientsreminders[$i]->group_name,1)][$i]['value'] = '';
                    }else if($patientsreminders[$i]->field_type == 'Pharmacies'){
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
                        $patientsreminder[substr($patientsreminders[$i]->group_name,1)][$i]['options']  = $data2;
                        $patientsreminder[substr($patientsreminders[$i]->group_name,1)][$i]['value'] = '';
                    }else if($patientsreminders[$i]->field_type == 'Organizations'){
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
                        $patientsreminder[substr($patientsreminders[$i]->group_name,1)][$i]['options']  = $data2;
                        $patientsreminder[substr($patientsreminders[$i]->group_name,1)][$i]['value'] = '';
                    }else if($patientsreminders[$i]->field_type == 'Patient allergies'){
                        $patientsreminder[substr($patientsreminders[$i]->group_name,1)][$i]['value'] = '';
                    }else if($patientsreminders[$i]->field_type == 'Lifestyle status'){
                        $patientsreminder[substr($patientsreminders[$i]->group_name,1)][$i]['value'] = '';
                    }else if($patientsreminders[$i]->field_type == 'Facilities'){
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
                        $patientsreminder[substr($patientsreminders[$i]->group_name,1)][$i]['options']  = $data2;
                        $patientsreminder[substr($patientsreminders[$i]->group_name,1)][$i]['value'] = '';
                    }else if($patientsreminders[$i]->field_type == 'Date Of Service'){
                        $patientsreminder[substr($patientsreminders[$i]->group_name,1)][$i]['options']  = '';
                        $patientsreminder[substr($patientsreminders[$i]->group_name,1)][$i]['value'] = '';
                    }else if($patientsreminders[$i]->field_type == 'Insurance Companies'){
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
                        $patientsreminder[substr($patientsreminders[$i]->group_name,1)][$i]['options']  = $data2;
                        $patientsreminder[substr($patientsreminders[$i]->group_name,1)][$i]['value'] = '';
                    }else if($patientsreminders[$i]->field_type == 'Users'){
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
                        $patientsreminder[substr($patientsreminders[$i]->group_name,1)][$i]['options']  = $data2;
                        $patientsreminder[substr($patientsreminders[$i]->group_name,1)][$i]['value'] = '';
                    }else{
                        $patientsreminder[substr($patientsreminders[$i]->group_name,1)][$i]['value'] = '';
                    }
//                }
             
            }
        }
//        echo "<pre>"; print_r($patientsreminder); echo "</pre>";
        if($patientsreminder)
        {
            $patientres = json_encode($patientsreminder); 
            echo $patientresult = GibberishAES::enc($patientres, $apikey);
        }
        else
        {
            $patientres =  '[{"id":"0"}]';
            echo $patientresult = GibberishAES::enc($patientres, $apikey);
        }
    } 
    catch(PDOException $e) 
    {

        $patientres =  '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo $patientresult = GibberishAES::enc($patientres, $apikey);
    }
}
function createUsersCred($uid){
    $apikey = 'rotcoderaclla';
    // The default key size is 256 bits.
    $old_key_size = GibberishAES::size();
    GibberishAES::size(256);
    try 
    {
        $db = getConnection();

        $sql = "SELECT group_name,field_id,title,list_id,
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
        END as field_type,
                        CASE uor
                            WHEN 1 THEN 'Optional'
                            WHEN 2 THEN 'Required'
                            END as isRequired FROM layout_options WHERE form_id = 'USERS' and uor<> 0 order by group_name, seq";   
        $db->query( "SET NAMES utf8");
        $stmt = $db->prepare($sql) ;
        $stmt->execute();                       

        $patientsreminders = $stmt->fetchAll(PDO::FETCH_OBJ);    
        $patientsreminder = array();
        if(!empty($patientsreminders)){
            for($i=0; $i<count($patientsreminders); $i++){
                $patientsreminder[substr($patientsreminders[$i]->group_name,1)][$i]['field_id']  = $patientsreminders[$i]->field_id;
                $patientsreminder[substr($patientsreminders[$i]->group_name,1)][$i]['isRequired']  = $patientsreminders[$i]->isRequired;
                $patientsreminder[substr($patientsreminders[$i]->group_name,1)][$i]['field_type']  = $patientsreminders[$i]->field_type;
                $patientsreminder[substr($patientsreminders[$i]->group_name,1)][$i]['label']  = $patientsreminders[$i]->title;
                $patientsreminder[substr($patientsreminders[$i]->group_name,1)][$i]['list_id']  =$patientsreminders[$i]->list_id;
                $get_title2 = "SELECT option_id, title FROM list_options WHERE list_id = '".$patientsreminders[$i]->list_id."'";
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
                $patientsreminder[substr($patientsreminders[$i]->group_name,1)][$i]['options']  = $titles;
                if($patientsreminders[$i]->field_type == 'Static Text'){
                        $patientsreminder[substr($patientsreminders[$i]->group_name,1)][$i]['value'] = $patientsreminders[$i]->description;
                    }else if($patientsreminders[$i]->field_type == 'Providers' || $patientsreminders[$i]->field_type == 'Providers NPI'){
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
                        $patientsreminder[substr($patientsreminders[$i]->group_name,1)][$i]['options']  = $data2;
                        $patientsreminder[substr($patientsreminders[$i]->group_name,1)][$i]['value'] = '';
                    }else if($patientsreminders[$i]->field_type == 'Pharmacies'){
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
                        $patientsreminder[substr($patientsreminders[$i]->group_name,1)][$i]['options']  = $data2;
                        $patientsreminder[substr($patientsreminders[$i]->group_name,1)][$i]['value'] = '';
                    }else if($patientsreminders[$i]->field_type == 'Organizations'){
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
                        $patientsreminder[substr($patientsreminders[$i]->group_name,1)][$i]['options']  = $data2;
                        $patientsreminder[substr($patientsreminders[$i]->group_name,1)][$i]['value'] = '';
                    }else if($patientsreminders[$i]->field_type == 'Patient allergies'){
                        $patientsreminder[substr($patientsreminders[$i]->group_name,1)][$i]['value'] = '';
                    }else if($patientsreminders[$i]->field_type == 'Lifestyle status'){
                        $patientsreminder[substr($patientsreminders[$i]->group_name,1)][$i]['value'] = '';
                    }else if($patientsreminders[$i]->field_type == 'Facilities'){
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
                        $patientsreminder[substr($patientsreminders[$i]->group_name,1)][$i]['options']  = $data2;
                        $patientsreminder[substr($patientsreminders[$i]->group_name,1)][$i]['value'] = '';
                    }else if($patientsreminders[$i]->field_type == 'Date Of Service'){
                        $patientsreminder[substr($patientsreminders[$i]->group_name,1)][$i]['options']  = '';
                        $patientsreminder[substr($patientsreminders[$i]->group_name,1)][$i]['value'] = '';
                    }else if($patientsreminders[$i]->field_type == 'Insurance Companies'){
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
                        $patientsreminder[substr($patientsreminders[$i]->group_name,1)][$i]['options']  = $data2;
                        $patientsreminder[substr($patientsreminders[$i]->group_name,1)][$i]['value'] = '';
                    }else if($patientsreminders[$i]->field_type == 'Users'){
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
                        $patientsreminder[substr($patientsreminders[$i]->group_name,1)][$i]['options']  = $data2;
                        $patientsreminder[substr($patientsreminders[$i]->group_name,1)][$i]['value'] = '';
                    }else{
                        $patientsreminder[substr($patientsreminders[$i]->group_name,1)][$i]['value'] = '';
                    }
             
            }
        }
//        echo "<pre>"; print_r($patientsreminder); echo "</pre>";
        if($patientsreminder)
        {
            $patientres = json_encode($patientsreminder); 
            echo $patientresult = GibberishAES::enc($patientres, $apikey);
        }
        else
        {
            $patientres =  '[{"id":"0"}]';
            echo $patientresult = GibberishAES::enc($patientres, $apikey);
        }
    } 
    catch(PDOException $e) 
    {

        $patientres =  '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo $patientresult = GibberishAES::enc($patientres, $apikey);
    }
}
function saveUsersCustomAttributes(){
    try{
        
        $db = getConnection();
        $flag=0;
        $request = Slim::getInstance()->request();
        $apikey = 'rotcoderaclla';
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        $appres = GibberishAES::dec($request->getBody(), $apikey);
//        $appres = GibberishAES::dec('U2FsdGVkX197uixV1771PzmX84d0aS0l0Wx+mk5APEAipQu50iLk3Fdrjiarp5UmYMysVrkJWZ3J8LJjsbZboLMxRcTKOfPV5idxqNdV88kV3rMJsEyY+I6ywXGA6g/csqrT2S1t7KfF24Ct/vZ9Vg7mNOmcaCmp+2QQOUGdqKqwH6MtftkWfvEjLAv5eEUYWx9mAACySWCuueLCXUtV/pXqTxYEvZx/XCm+wV8OAXN5F/flY0dQHjItsdUyCYLmRbTfemxVNkNUSSbh1QGB3vwmBzvtZDuY2XNCDPtTzxmkSDCeW6c+GmrKYAs1H92sf7GWVuTMb30olEZ8BCQZ3Q==', $apikey);
        $insertArray = json_decode($appres,TRUE);
//        echo "<pre>"; print_r($insertArray); echo "</pre>";
         
        $form_id = $insertArray['form_id'];
        $addrbk_type_id = $insertArray['addrbk_type_id'];
//        $user = $insertArray['user'];
//exit();
        $getcolumnNames = "select column_name as columns from information_schema.columns where table_name='tbl_user_custom_attr_1to1'";
        $stmt = $db->prepare($getcolumnNames) ;
        $stmt->execute();                       

        $getnames = $stmt->fetchAll(PDO::FETCH_OBJ); 
        
        foreach ($getnames as $value) {
           if((strcmp($value->columns, 'id') !== 0) && (strcmp($value->columns, 'addrbk_type_id') !== 0) && (strcmp($value->columns, 'updated_date') !== 0) && (strcmp($value->columns, 'created_date') !== 0) && (strcmp($value->columns, 'date') !== 0)  && (strcmp($value->columns, 'activity') !== 0) && (strcmp($value->columns, 'authorized') !== 0)):
                ${$value->columns} = $insertArray['data'][0][$value->columns];//str_replace("_", ' ', $insertArray['data'][0][$value->columns]);
                
            endif;
        }
        $checkname= '';
        $nameslist2 = '';
        $namesvalues2 = '';
        $updatenamelist2= '';
        foreach ($getnames as $value) {
            if($value->columns != 'id' && $value->columns != 'updated_date' && $value->columns != 'created_date'  && $value->columns != 'addrbk_type_id'):
                $nameslist2 .=  "`".$value->columns."`,";
                if(isset(${$value->columns}))
                    $namesvalues2 .= "'".${$value->columns}."',";
            endif;
            if($value->columns == 'id'){
               $updateid = 0;//$formid;
            }elseif($value->columns == 'date'){
                $updatenamelist2 .= "date = NOW(),";
            }elseif($value->columns == 'updated_date'){
                if($form_id != 0){
                    $updatenamelist2 .= "updated_date = NOW(),";
                }
            }elseif($value->columns == 'created_date'){
                if($form_id == 0){
                    $updatenamelist2 .= "created_date = NOW(),";
                }
            }elseif($value->columns == 'activity'){
                $updatenamelist2 .= "activity = 1,";
            }else{
                if(isset(${$value->columns}))
                    $updatenamelist2 .= "`".$value->columns."`='".${$value->columns}."',";
            }
        }
        $nameslist = rtrim($nameslist2, ',');         
        $namesvalues = rtrim($namesvalues2, ',');
        $updatenamelist = rtrim($updatenamelist2, ',');

        if($form_id == 0){
            $nameslist = rtrim($nameslist2, ',');
            $namesvalues = rtrim($namesvalues2, ',');
            $insert = "INSERT INTO tbl_user_custom_attr_1to1 (addrbk_type_id,$nameslist,created_date) VALUES('$addrbk_type_id',$namesvalues,NOW())";
            $stmt2 = $db->prepare($insert) ;
            $insertval = $stmt2->execute();                     
        }else{
            $nameslist = rtrim($nameslist2, ',');
            $namesvalues = rtrim($namesvalues2, ',');
            $insert = "UPDATE tbl_user_custom_attr_1to1 SET $updatenamelist WHERE id = '$form_id'";
            $stmt2 = $db->prepare($insert) ;
            $insertval = $stmt2->execute();  
        }
        
        if($insertval){  
            $insertcheck =  '[{"id":"1"}]';
            echo GibberishAES::enc($insertcheck, $apikey);
	}else{
            $insertcheck = '[{"id":"0"}]';
            echo GibberishAES::enc($insertcheck, $apikey);
	}
	  
    }catch(PDOException $e){
         $insertquery = '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo GibberishAES::enc($insertquery, $apikey);
    }
}
function saveUsersCred(){
    try{
        
        $db = getConnection();
        $flag=0;
        $request = Slim::getInstance()->request();
        $apikey = 'rotcoderaclla';
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        $appres = GibberishAES::dec($request->getBody(), $apikey);
//        $appres = GibberishAES::dec('U2FsdGVkX19W+fnFi+Vih5MQiKCUY8NMWKHWSJiqr/ohdsyl0dFFGGxB+zEBxTus8z1AZNt2Q32MCGosidpacEifaTeshQE9hwENsyGtqy0gRfTcMS2XEJ0PYzaa5+BR0cElrT195rG66m3kvY7zfUemcYWUQx8GSFL7E5/H2lII5yf5G7ys4gajNDiIzuuo', $apikey);
        $insertArray = json_decode($appres,TRUE);
//        echo "<pre>"; print_r($insertArray); echo "</pre>";
         
        $form_id = $insertArray['form_id'];
//        $encounter = $insertArray['encounter'];
        $userid = $insertArray['user'];

        $getcolumnNames = "select column_name as columns from information_schema.columns where table_name='tbl_patientuser'";
        $stmt = $db->prepare($getcolumnNames) ;
        $stmt->execute();                       

        $getnames = $stmt->fetchAll(PDO::FETCH_OBJ); 
        
        foreach ($getnames as $value) {
           if((strcmp($value->columns, 'id') !== 0) && (strcmp($value->columns, 'userid') !== 0)&& (strcmp($value->columns, 'updated_date') !== 0) && (strcmp($value->columns, 'created_date') !== 0)&& (strcmp($value->columns, 'date') !== 0)  && (strcmp($value->columns, 'activity') !== 0) && (strcmp($value->columns, 'authorized') !== 0)):
                ${$value->columns} = $insertArray['data'][0][$value->columns];//str_replace("_", ' ', $insertArray['data'][0][$value->columns]);
                
            endif;
        }
        $checkname= '';
        $nameslist2 = '';
        $namesvalues2 = '';
        $updatenamelist2= '';
        foreach ($getnames as $value) {
            if($value->columns != 'id' && $value->columns != 'updated_date' && $value->columns != 'created_date' && $value->columns != 'userid'):
                $nameslist2 .=  "`".$value->columns."`,";
                if(isset(${$value->columns}))
                    $namesvalues2 .= "'".${$value->columns}."',";
            endif;
            if($value->columns == 'id'){
               $updateid = 0;//$formid;
            }elseif($value->columns == 'date'){
                $updatenamelist2 .= "date = NOW(),";
            }elseif($value->columns == 'updated_date'){
                if($form_id != 0){
                    $updatenamelist2 .= "updated_date = NOW(),";
                }
            }elseif($value->columns == 'created_date'){
                if($form_id == 0){
                    $updatenamelist2 .= "created_date = NOW(),";
                }
            }elseif($value->columns == 'activity'){
                $updatenamelist2 .= "activity = 1,";
            }else{
                if(isset(${$value->columns}))
                    $updatenamelist2 .= "`".$value->columns."`='".${$value->columns}."',";
            }
        }
        $nameslist = rtrim($nameslist2, ',');         
        $namesvalues = rtrim($namesvalues2, ',');
        $updatenamelist = rtrim($updatenamelist2, ',');

        if($form_id == 0){
            $nameslist = rtrim($nameslist2, ',');
            $namesvalues = rtrim($namesvalues2, ',');
            $insert = "INSERT INTO tbl_patientuser ($nameslist,userid,created_date) VALUES($namesvalues,$userid,NOW())";
            $stmt2 = $db->prepare($insert) ;
            $insertval = $stmt2->execute();                     
        }else{
            $nameslist = rtrim($nameslist2, ',');
            $namesvalues = rtrim($namesvalues2, ',');
            $insert = "UPDATE tbl_patientuser SET $updatenamelist WHERE id = '$form_id'";
            $stmt2 = $db->prepare($insert) ;
            $insertval = $stmt2->execute();  
        }
        
        if($insertval){  
            $insertcheck =  '[{"id":"1"}]';
            echo GibberishAES::enc($insertcheck, $apikey);
	}else{
            $insertcheck = '[{"id":"0"}]';
            echo GibberishAES::enc($insertcheck, $apikey);
	}
	  
    }catch(PDOException $e){
        $insertquery = '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo GibberishAES::enc($insertquery, $apikey);
    }
}
function saveUsersPayroll(){
    try{
        
        $db = getConnection();
        $flag=0;
        $request = Slim::getInstance()->request();
        $apikey = 'rotcoderaclla';
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        $appres = GibberishAES::dec($request->getBody(), $apikey);
//        $appres = GibberishAES::dec('U2FsdGVkX19SdAEVuRcHeIFlqRdqNTCKSG4Erxmy1v6IcsHRSpamf35mSWyU68cF1soQNhtvOfR7K7mUmIKNkKI+vv0wwJKj4R4JkfKmA30qE9VQYkimlCNENxJrO8nfwvHU7icYH8YC5zH9wP1IC9cxQvutFArRGWMh0igPfnVNfDLQAdP/ZX66v1fXtBJL+x5zP6JteuZh1nB/CSXAPIpc0cIA6egdHsHqoISwWmOJWw2U2326EA+HmuNeSLuRYBV6VBxvA587bOBPblIGeQ==', $apikey);
        $insertArray = json_decode($appres,TRUE);
//        echo "<pre>"; print_r($insertArray); echo "</pre>";
         
        $form_id = $insertArray['form_id'];
//        $encounter = $insertArray['encounter'];
        $userid = $insertArray['user'];

        $getcolumnNames = "select column_name as columns from information_schema.columns where table_name='tbl_userpayroll'";
        $stmt = $db->prepare($getcolumnNames) ;
        $stmt->execute();                       

        $getnames = $stmt->fetchAll(PDO::FETCH_OBJ); 
        
        foreach ($getnames as $value) {
           if((strcmp($value->columns, 'id') !== 0) && (strcmp($value->columns, 'userid') !== 0) && (strcmp($value->columns, 'updated_date') !== 0) && (strcmp($value->columns, 'created_date') !== 0)&& (strcmp($value->columns, 'date') !== 0)  && (strcmp($value->columns, 'activity') !== 0) && (strcmp($value->columns, 'authorized') !== 0)):
                ${$value->columns} = $insertArray['data'][0][$value->columns]; //str_replace("", ' ', $insertArray['data'][0][$value->columns]);
                
            endif;
        }
        $checkname= '';
        $nameslist2 = '';
        $namesvalues2 = '';
        $updatenamelist2= '';
        foreach ($getnames as $value) {
            if($value->columns != 'id' && $value->columns != 'updated_date' && $value->columns != 'created_date' && $value->columns != 'userid'):
                $nameslist2 .=  "`".$value->columns."`,";
                if(isset(${$value->columns}))
                    $namesvalues2 .= "'".${$value->columns}."',";
            endif;
            if($value->columns == 'id'){
               $updateid = 0;//$formid;
            }elseif($value->columns == 'date'){
                $updatenamelist2 .= "date = NOW(),";
            }elseif($value->columns == 'updated_date'){
                if($form_id != 0){
                    $updatenamelist2 .= "updated_date = NOW(),";
                }
            }elseif($value->columns == 'created_date'){
                if($form_id == 0){
                    $updatenamelist2 .= "created_date = NOW(),";
                }
            }elseif($value->columns == 'activity'){
                $updatenamelist2 .= "activity = 1,";
            }else{
                if(isset(${$value->columns}))
                    $updatenamelist2 .= "`".$value->columns."`='".${$value->columns}."',";
            }
        }
        $nameslist = rtrim($nameslist2, ',');         
        $namesvalues = rtrim($namesvalues2, ',');
        $updatenamelist = rtrim($updatenamelist2, ',');

        if($form_id == 0){
            $nameslist = rtrim($nameslist2, ',');
            $namesvalues = rtrim($namesvalues2, ',');
            $insert = "INSERT INTO tbl_userpayroll ($nameslist,userid,created_date) VALUES($namesvalues,$userid,NOW())";
            $stmt2 = $db->prepare($insert) ;
            $insertval = $stmt2->execute();                     
        }else{
            $nameslist = rtrim($nameslist2, ',');
            $namesvalues = rtrim($namesvalues2, ',');
            $insert = "UPDATE tbl_userpayroll SET $updatenamelist  WHERE id = '$form_id'";
            $stmt2 = $db->prepare($insert) ;
            $insertval = $stmt2->execute();  
        }
        
        if($insertval){  
            $insertcheck =  '[{"id":"1"}]';
            echo GibberishAES::enc($insertcheck, $apikey);
	}else{
            $insertcheck = '[{"id":"0"}]';
            echo GibberishAES::enc($insertcheck, $apikey);
	}
	  
    }catch(PDOException $e){
        $insertquery = '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo GibberishAES::enc($insertquery, $apikey);
    }
}
function editMessages($providerid,$id){
    $apikey = 'rotcoderaclla';
    // The default key size is 256 bits.
    $old_key_size = GibberishAES::size();
    GibberishAES::size(256);
    try
    {
        $db = getConnection();
        $issues = array();
        
        
        if($id != 0){
            $get_layout = "SELECT * FROM pnotes WHERE id = '$id' ";
            $db->query( "SET NAMES utf8");
            $stmt_layout = $db->prepare($get_layout) ;
            $stmt_layout->execute();

            $layout_fields = $stmt_layout->fetchAll(PDO::FETCH_OBJ);
            
            $get_pname = "SELECT pid, concat(title,fname,' ',lname) as full_name FROM patient_data WHERE pid = '".$layout_fields[0]->pid."' ";
            $db->query( "SET NAMES utf8");
            $stmt_pname = $db->prepare($get_pname) ;
            $stmt_pname->execute();

            $pname_field = $stmt_pname->fetchAll(PDO::FETCH_OBJ);
            $pid            = isset($pname_field[0]->pid)               ? $pname_field[0]->pid                  : '';
            $full_name      = isset($pname_field[0]->full_name)         ? $pname_field[0]->full_name            : '';
        }
        
        $get_note_type = "SELECT option_id,title FROM list_options WHERE list_id = 'note_type'  order by seq";
        $db->query( "SET NAMES utf8");
        $stmt_note_type = $db->prepare($get_note_type) ;
        $stmt_note_type->execute();
        $get_note_typelist= array();
        $get_note_typelist2 = $stmt_note_type->fetchAll(PDO::FETCH_OBJ);
        for($i=0; $i< count($get_note_typelist2); $i++){
            $get_note_typelist[$i]['optionid'] = $get_note_typelist2[$i]->option_id;
            $get_note_typelist[$i]['optionval'] = $get_note_typelist2[$i]->title;
        }
        
        $issues[0]['Label'] = 'Type';
        $issues[0]['Options'] = $get_note_typelist;
        $issues[0]['Type'] = 'ListBox';
        $issues[0]['field_id'] = 'title';
        $issues[0]['isRequired'] = 'Required';
        
        $issues[1]['Label'] = 'Patient';
        $issues[1]['Url'] = "/api/searchpatientbyname/$providerid/";
        $issues[1]['field_id'] = 'pid';
        $issues[1]['Type'] = 'searchbox';
        $issues[1]['isRequired'] = 'Required';

        
        $get_message_status = "SELECT option_id,title FROM list_options WHERE list_id = 'message_status'  order by seq";
        $db->query( "SET NAMES utf8");
        $stmt_message_status = $db->prepare($get_message_status) ;
        $stmt_message_status->execute();
        $get_message_statuslist= array();
        $get_message_statuslist2 = $stmt_message_status->fetchAll(PDO::FETCH_OBJ);
        for($i=0; $i< count($get_message_statuslist2); $i++){
            $get_message_statuslist[$i]['optionid'] = $get_message_statuslist2[$i]->option_id;
            $get_message_statuslist[$i]['optionval'] = $get_message_statuslist2[$i]->title;
        }
        
        $issues[2]['Label'] = 'Status';
        $issues[2]['Options'] = $get_message_statuslist;
        $issues[2]['field_id'] = 'message_status';
        $issues[2]['Type'] = 'ListBox';
        $issues[2]['isRequired'] = 'Required';
        
        $qry = "select username, concat(lname,', ',fname) as full_name " .
                       "from users where username != '' AND (lname != '' OR fname != '') " .
                       "order by concat(lname,', ',fname)";
        $db->query( "SET NAMES utf8");
        $stmt = $db->prepare($qry) ;
        $stmt->execute();
        $rs = $stmt->fetchAll(PDO::FETCH_OBJ);
        $issues[3]['SelectedValue'][0]['optionid']       = '';          
        $issues[3]['SelectedValue'][0]['optionval']      = '';
        if(!empty($rs)){
            $j = 0;
            for($i=0; $i< count($rs); $i++){
                $usernames[$i]['optionid'] = $rs[$i]->username;
                $usernames[$i]['optionval'] = $rs[$i]->full_name;
                $assigned_to = isset($layout_fields[0]->assigned_to)? $layout_fields[0]->assigned_to : '';
                if($rs[$i]->username == $assigned_to ){
                    $issues[3]['SelectedValue'][$j]['optionid']                 = isset($layout_fields[0]->assigned_to)     ? $layout_fields[0]->assigned_to                : '';
                    $issues[3]['SelectedValue'][$j]['optionval']                = isset($rs[$i]->full_name)                 ? $rs[$i]->full_name                            : '';
                    $j++;
                }
            }
        }
        
        $issues[3]['Label'] = 'To';
        $issues[3]['Options'] = $usernames;
        $issues[3]['field_id'] = 'assigned_to';
        $issues[3]['Type'] = 'MultiSelect';
        $issues[3]['Notes'] = 'Select Users From The Dropdown List';
        $issues[3]['isRequired'] = 'Required';
        
        if($id != 0){
            $issues[4]['Label'] = 'Previous Message';
            $issues[4]['Options'] = '';
            $issues[4]['field_id'] = 'previous_msg';
            $issues[4]['Type'] = 'NotEditable';
            $issues[4]['isRequired'] = 'Optional';
        }
        
        $issues[5]['Label'] = 'Message';
        $issues[5]['Options'] = '';
        $issues[5]['field_id'] = 'body';
        $issues[5]['Type'] = 'TextArea';
        $issues[5]['SelectedValue'] = '';
        $issues[5]['isRequired'] = 'Required';
        $issues[5]['placeholder'] = 'Enter message here';
        
        
        $issues[0]['SelectedValue']                 = isset($layout_fields[0]->title)           ? $layout_fields[0]->title                      : '';

        $issues[1]['SelectedValue'][0]['optionid']     = isset($pid)                               ?  $pid                                         : '';
        $issues[1]['SelectedValue'][0]['optionval']    = isset($full_name)                         ?  $full_name                                   : '';

        $issues[2]['SelectedValue']                 = isset($layout_fields[0]->message_status)  ? $layout_fields[0]->message_status             : '';
        
        if($id != 0)
            $issues[4]['SelectedValue']             = isset($layout_fields[0]->body)            ? $layout_fields[0]->body                       : '';
        

//                echo "<pre>"; print_r(array_values($issues)); echo "</pre>";
        if($issues)
        {
            $patientres = json_encode(($issues));
            echo $patientresult = GibberishAES::enc($patientres, $apikey);
        }
        else
        {
            $patientres =  '[{"id":"0"}]';
            echo $patientresult = GibberishAES::enc($patientres, $apikey);
        }
    }
    catch(PDOException $e)
    {

        $patientres =  '{"error":{"text":'. $e->getMessage() .'}}';
        echo $patientresult = GibberishAES::enc($patientres, $apikey);
    }
} 
// method to save message
function saveMessage(){
    try
    {
        $db = getConnection();
        $key = 'rotcoderaclla';
        $msgrequest = Slim::getInstance()->request();
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        $msgresult =  GibberishAES::dec($msgrequest->getBody(), $key);
//        $msgresult =  GibberishAES::dec('U2FsdGVkX19Qe/VChNlJ02bMmZ216ZetLcLDzJiHFob8ESkuSCJ2NuzVX9EUyh5lV7bIrF70s+W7Y4ZyLUUF/sdqCXabeIr27HzHQkRPVdpc3YZRWbGieh+grk5V3uB2QF/xXFSzBdlk/TL2ivqYJzTaNZmlK48ZPGsRRrI3t56UjIRWGMjz/mvFoZC9ElVm1+fo5V1kymLHEjlCEZZ5fy1YbbYG2fI+3Z/cBDWwAo8GtK4ZH1v2NkB62LJZd3oaPGic/RnZcLCdMx1Zw7V1MQ==', $key);
        $msgArray = json_decode($msgresult,TRUE);
//        echo "<pre>";        print_r($msgArray); echo "</pre>";

        $msgId              = $msgArray['id'];
        $loginProvderId     = $msgArray['providerid'];
        $pid                = $msgArray['data'][0]['pid'];
        $assigned_to        = $msgArray['data'][0]['assigned_to'];
        $body               = addslashes($msgArray['data'][0]['body']);
        $title              = $msgArray['data'][0]['title'];
        $msg_status         = $msgArray['data'][0]['message_status'];
        $username           = $msgArray['username'];
        $resultant_error    = 0;
        $resultant_error2   = 0;
        $resultant          = 0;
        if($username)
        {
            $assignedto = explode('|',$assigned_to);
            if($msgId != 0){
                $previous_msg       = addslashes($msgArray['data'][0]['previous_msg']);
                for($i=0;$i<count($assignedto);$i++){
//                    $getname = "SELECT username FROM users WHERE id = $assignedto[$i]";
//                    $getnamestmt = $db->prepare($getname);
//                    $getnamestmt->execute();
//                    $name = $getnamestmt->fetchAll(PDO::FETCH_OBJ);

                    //$sql= "UPDATE pnotes SET date=NOW(),body=CONCAT(date_format(now(), '%Y-%m-%d %H:%i'),'".' '."($username to ".$name[0]->username.")".' '." $body'),pid=$pid,title='$title', user='$username', assigned_to='".$name[0]->username."',message_status='$msg_status' WHERE id=$msgId";
                    $sql= "UPDATE pnotes SET date=NOW(),body=CONCAT('$previous_msg',CONCAT(date_format(now(), '%Y-%m-%d %H:%i'),'".' '."($username to ".$assignedto[$i].")".' '." $body')),pid=$pid,title='$title', user='$username', assigned_to='".$assignedto[$i]."',message_status='$msg_status' WHERE id=$msgId";
                    $q = $db->prepare($sql);
                    if($q->execute())
                    {   
                        $resultant = 1;
                        insertMobileLog('update',$username,$sql,$pid,'','Update Message',1);
                    }
                    else
                    {
                        $resultant_error = 1;
                        insertMobileLog('update',$username,$sql,$pid,'','Update Message',0);
                    }
                }
            }else{
                for($i=0;$i<count($assignedto);$i++)
                {       
//                        $getname = "SELECT username FROM users WHERE id = $assignedto[$i]";
//                        $getnamestmt = $db->prepare($getname);
//                        $getnamestmt->execute();
//                        $name = $getnamestmt->fetchAll(PDO::FETCH_OBJ);
                        
                        $sql = "INSERT INTO pnotes (date, body, pid, user, groupname, activity, authorized, title, assigned_to, message_status)
                        values (NOW(), CONCAT(date_format(now(), '%Y-%m-%d %H:%i'),'".' '."($username to ".$assignedto[$i].")".' '." $body'), $pid, '$username', 'Default', 1, 1, '$title', '".$assignedto[$i]."', '$msg_status')";
                        $q = $db->prepare($sql);
                        if($q->execute())

                        {   
                            $resultant = 1;
                            insertMobileLog('insert',$username,$sql,$pid,'','Create Message',1);
                        }
                        else
                        {
                            $resultant_error = 1;
                            insertMobileLog('insert',$username,$sql,$pid,'','Create Message',0);
                        }
                }
            }
            
        }
        else
        {
            $resultant_error2 = 1;
        }
        if($resultant == 1 && $resultant_error == 0 && $resultant_error2 == 0){
                $result = '[{"id":"1"}]';
                echo $result2 = GibberishAES::enc($result, $key);
            }else{
                $result = '[{"id":"0"}]';
                echo $result2 = GibberishAES::enc($result, $key);
            }
    }
    catch(PDOException $e) 
    {   
        $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo $result = GibberishAES::enc($error, $key);
        insertMobileLog('insert/update',$username,$error,$pid,'','Create Message - Query Failed',0);
    }
}
function createAppointment($providerid){
    $apikey = 'rotcoderaclla';
    // The default key size is 256 bits.
    $old_key_size = GibberishAES::size();
    GibberishAES::size(256);
    try
    {
        $db = getConnection();
        $issues = array();
        
        $get_catname="SELECT pc_catid, pc_catname, ROUND( pc_duration /60, 0 ) AS minutes
                FROM openemr_postcalendar_categories
                WHERE pc_cattype =0
                ORDER BY pc_catname";
        $db->query( "SET NAMES utf8");
        $stmt_catname = $db->prepare($get_catname) ;
        $stmt_catname->execute();
        $get_catnamelist= array();
        $get_catnamelist2 = $stmt_catname->fetchAll(PDO::FETCH_OBJ);
        for($i=0; $i< count($get_catnamelist2); $i++){
            $get_catnamelist[$i]['optionid'] = $get_catnamelist2[$i]->pc_catid;
            $get_catnamelist[$i]['optionval'] = $get_catnamelist2[$i]->pc_catname;
            $get_catnamelist[$i]['optiontime'] = $get_catnamelist2[$i]->minutes;
        }
        
        $issues[0]['Label']             = 'Category:';
        $issues[0]['Options']           = $get_catnamelist;
        $issues[0]['Type']              = 'ListBox';
        $issues[0]['field_id']          = 'pc_catid';
        $issues[0]['isRequired']        = 'Required';
        $issues[0]['linkedfield']       = 'pc_title|pc_duration';
        $issues[0]['SelectedValue']     = '';
        
        $issues[1]['Label']             = 'Date:';
        $issues[1]['field_id']          = 'pc_eventDate';
        $issues[1]['Type']              = 'Date';
        $issues[1]['isRequired']        = 'Required';
        $issues[1]['SelectedValue']     = '';
        $issues[1]['placeholder']       = 'Select date';

        $issues[2]['Label']             = 'Time:';
        $issues[2]['field_id']          = 'pc_startTime';
        $issues[2]['Type']              = 'Time';
        $issues[2]['isRequired']        = 'Required';
        $issues[2]['SelectedValue']     = '';
        $issues[2]['placeholder']       = 'Select time';
        
        $issues[3]['Label']             = 'Duration:';
        $issues[3]['field_id']          = 'pc_duration';
        $issues[3]['Type']              = 'NotEditable';
        $issues[3]['isRequired']        = 'Required';
        $issues[3]['SelectedValue']     = '';
        
        $issues[4]['Label']             = 'Find Available Slots';
        $issues[4]['Url']               = "/api/availabletimeslots/$providerid/";
        $issues[4]['linkedfield']       = 'pc_eventDate';
        $issues[4]['linkedfield_label'] = 'Date';
        $issues[4]['Type']              = 'Popup';
        $issues[4]['SelectedValue']     = '';
        
        $issues[5]['Label']             = 'Title:';
        $issues[5]['field_id']          = 'pc_title';
        $issues[5]['Type']              = 'NotEditable';
        $issues[5]['SelectedValue']     = '';
                
        $sql3 = "SELECT id, name FROM facility ORDER BY name";
        $db->query( "SET NAMES utf8"); 
        $stmt3 = $db->prepare($sql3) ;
        $stmt3->execute();                       
        $getListData2 = $stmt3->fetchAll(PDO::FETCH_OBJ); 
        for($i=0; $i< count($getListData2); $i++){
            $getListData[$i]['optionid'] = $getListData2[$i]->id;
            $getListData[$i]['optionval'] = $getListData2[$i]->name;
        }
        
        $issues[6]['Label']         = 'Facility:';
        $issues[6]['field_id']      = 'pc_facility';
        $issues[6]['Options']       = $getListData;
        $issues[6]['Type']          = 'ListBox';
        $issues[6]['isRequired']    = 'Required';
        $issues[6]['SelectedValue'] = '';
        
        $sql4 = "SELECT id, name FROM facility ORDER BY name";
        $db->query( "SET NAMES utf8"); 
        $stmt4 = $db->prepare($sql4) ;
        $stmt4->execute();                       
        $getListData3 = $stmt4->fetchAll(PDO::FETCH_OBJ); 
        if(!empty($getListData3)){
            $getBillingData[0]['optionid']  = $getListData3[0]->id;
            $getBillingData[0]['optionval'] = $getListData3[0]->name;
            $issues[7]['SelectedValue']     = $getBillingData[0]['optionid'];
        }
        
        $issues[7]['Label']             = 'Billing Facility:';
        $issues[7]['Options']           = $getBillingData;
        $issues[7]['field_id']          = 'pc_billing_location';
        $issues[7]['Type']              = 'ListBox';
        $issues[7]['isRequired']        = 'Required';
        
        $issues[8]['Label']             = 'Patient:';
        $issues[8]['Url']               = "/api/searchpatientbyname/$providerid/";
        $issues[8]['field_id']          = 'pc_pid';
        $issues[8]['Type']              = 'searchbox';
        $issues[8]['isRequired']        = 'Required';
        $issues[8]['SelectedValue']     = '';
        
        $check_admin = "select gag.name as acl_role 
                        from users u
                        inner join gacl_aro ga on ga.value= u.username
                        inner join gacl_groups_aro_map ggam on ggam.aro_id = ga.id
			inner join gacl_aro_groups gag on gag.id = ggam.group_id
                        WHERE u.id = $providerid";
        $stmt_admin = $db->prepare($check_admin) ;
        $stmt_admin->execute();
        $admin_check = $stmt_admin->fetchAll(PDO::FETCH_OBJ);
        $admin = 0;
        if(!empty($admin_check)){
            for($i=0; $i< count($admin_check); $i++){
                if($admin_check[$i]->acl_role == 'Administrators'){
                    $admin = 1;
                }
            }
        }
        
        if($admin == 0){
            $qry = "select id, concat(lname,', ',fname) as full_name " .
                       "from users where active=1 AND authorized=1 " .
                       " AND id = $providerid";
        }
        if($admin == 1){
            $qry = "select id, concat(lname,', ',fname) as full_name " .
                           "from users where active=1 AND authorized=1 " .
                           "order by concat(lname,', ',fname)";
        }
        $stmt = $db->prepare($qry) ;
        $stmt->execute();
        $rs = $stmt->fetchAll(PDO::FETCH_OBJ);
        if(!empty($rs)){
            for($i=0; $i< count($rs); $i++){
                $usernames[$i]['optionid'] = $rs[$i]->id;
                $usernames[$i]['optionval'] = $rs[$i]->full_name;
            }
        }
        $issues[9]['Label']             = 'Provider:';
        $issues[9]['Options']           = $usernames;
        $issues[9]['field_id']          = 'pc_aid';
        $issues[9]['Type']              = 'ListBox';
        $issues[9]['isRequired']        = 'Required';
        if($admin == 0){
            $issues[9]['SelectedValue']     = $providerid;
        }
        if($admin == 1){
            $issues[9]['SelectedValue']     = '';
        }
        
        $get_appt_status = "SELECT option_id,title FROM list_options WHERE list_id = 'apptstat' AND notes = 'M' order by seq";
        $db->query( "SET NAMES utf8");
        $stmt_appt_status = $db->prepare($get_appt_status) ;
        $stmt_appt_status->execute();
        $get_appt_statuslist= array();
        $get_appt_statuslist2 = $stmt_appt_status->fetchAll(PDO::FETCH_OBJ);
        for($i=0; $i< count($get_appt_statuslist2); $i++){
            $get_appt_statuslist[$i]['optionid'] = $get_appt_statuslist2[$i]->option_id;
            $get_appt_statuslist[$i]['optionval'] = $get_appt_statuslist2[$i]->title;
        }
        
        
        $issues[10]['Label']            = 'Status:';
        $issues[10]['Options']          = $get_appt_statuslist;
        $issues[10]['field_id']         = 'pc_apptstatus';
        $issues[10]['Type']             = 'ListBox';
        $issues[10]['isRequired']       = 'Required';
        $issues[10]['SelectedValue']    = '';
        
        $issues[11]['Label']            = 'Comments:';
        $issues[11]['field_id']         = 'pc_hometext';
        $issues[11]['Type']             = 'TextBox';
        $issues[11]['isRequired']       = 'Optional';
        $issues[11]['SelectedValue']    = '';
        $issues[11]['placeholder']      = 'Enter Comment here..';
        

//        echo "<pre>"; print_r(array_values($issues)); echo "</pre>";
        if($issues)
        {
            $patientres = json_encode(($issues));
            echo $patientresult = GibberishAES::enc($patientres, $apikey);
        }
        else
        {
            $patientres =  '[{"id":"0"}]';
            echo $patientresult = GibberishAES::enc($patientres, $apikey);
        }
    }
    catch(PDOException $e)
    {

        $patientres =  '{"error":{"text":'. $e->getMessage() .'}}';
        echo $patientresult = GibberishAES::enc($patientres, $apikey);
    }
}  
// methods to get dates available in between
function getBetweenDates($pc_recurrspec,$pc_eventDate,$pc_endDate)
{	
	try
	{
            $between_dates = array($pc_eventDate);
            if($pc_recurrspec !=NULL)
            {
                $pc_recurrspec_str = stripslashes($pc_recurrspec);
                $pc_recurrspec_str = str_replace("\n","",$pc_recurrspec_str); 
                $pc_recurrspec_str = str_replace('"','',$pc_recurrspec_str);
                $pc_recurrspec_Array = explode(';s:1:',$pc_recurrspec_str); 
                //print_r($pc_recurrspec_Array);die;

                $event_repeat_freq_Array=explode(';',$pc_recurrspec_Array[1]);
                $event_repeat_freq=$event_repeat_freq_Array[0];
                $event_repeat_freq_type_Array=explode(';',$pc_recurrspec_Array[2]);
                $event_repeat_freq_type=$event_repeat_freq_type_Array[0];

                $event_repeat_on_num_Array=explode(';',$pc_recurrspec_Array[3]);
                $event_repeat_on_num=$event_repeat_on_num_Array[0];
                $event_repeat_on_day_Array=explode(';',$pc_recurrspec_Array[4]);
                $event_repeat_on_day=$event_repeat_on_day_Array[0];
				
		if(count($pc_recurrspec_Array)>5)
		{
                $event_repeat_on_freq_Array=explode(';',$pc_recurrspec_Array[5]);
                $event_repeat_on_freq=$event_repeat_on_freq_Array[0];
		}
							
                //$pc_recurrspec_Array = unserialize($pc_recurrspec_str); 
                
                $repeat_freq=$event_repeat_freq; 
                $repeat_freq_type=$event_repeat_freq_type;
                
                $event_endDate='';
                if($pc_endDate == '0000-00-00')
                {
                        $event_endDate=$pc_eventDate;
                }
                else
                {
                        $event_endDate=$pc_endDate;
                }

                if($event_repeat_on_num!=0 &&$event_repeat_on_day!=0)
                {   //echo " eg. 2nd Saturday";
                    
                    // get the day of the selected date                    
                    $day_name=date('l', strtotime($pc_eventDate));
                    $y=date('Y', strtotime($pc_eventDate));
                    $m=date('m', strtotime($pc_eventDate));
                    $num_str='';
                    switch($event_repeat_on_num)
                    {
                       case 1:$num_str='first';break;
                       case 2:$num_str='second';break;
                       case 3:$num_str='third';break;
                       case 4:$num_str='fourth';break;
                       case 5:$num_str='fifth';break;                       
                        
                    }
                   
                    // get date of the 2nd Saturday of the next month                  
 
                    while(end($between_dates) < $event_endDate)
                    {
                        
                        $m++;
                        $date2 = strtotime("$num_str $day_name $y-$m");
                        
                      	$between_dates[]=date('Y-m-d',$date2);
			$y=date('Y', strtotime(current($between_dates)));
                    }
                                        
                }
                else
                {        
                    if ($repeat_freq_type == 0) //--day    //(0 => xl('day') , 4 => xl('workday'), 1 => xl('week'), 2 => xl('month'), 3 => xl('year'),   5 => '?', 6 => '?')
                    {
                        //if($repeat_freq==1) //(1 => xl('every'), 2 => xl('2nd'), 3 => xl('3rd'), 4 => xl('4th'), 5 => xl('5th'), 6 => xl('6th')
                        //{
                            while(end($between_dates) < $event_endDate)
                            {
                              $between_dates[]=date('Y-m-d',strtotime(end($between_dates).'+'.$repeat_freq.' day'));
                            }
                        //}
                    }
                    else if ($repeat_freq_type == 1) 
                    {                        
                            while(end($between_dates) < $event_endDate)
                            {
                              $between_dates[]=date('Y-m-d',strtotime(end($between_dates).'+'.$repeat_freq.' week'));
                            }
                        
                    }
                    else if ($repeat_freq_type == 2) 
                    {                        
		            while(end($between_dates) < $event_endDate)
		            {
		              $between_dates[]=date('Y-m-d',strtotime(end($between_dates).'+'.$repeat_freq.' month'));
		            }                        
                    }
                    else if ($repeat_freq_type == 3) 
                    {                                       
                            while(end($between_dates) < $event_endDate)
                            {
                              $between_dates[]=date('Y-m-d',strtotime(end($between_dates).'+'.$repeat_freq.' year'));
                            }                       
                    }

		    else if ($repeat_freq_type == 4) 
                    {
                           			
                            while(end($between_dates) < $event_endDate)
                            {
				$day_name=date('l', strtotime(date('Y-m-d',strtotime(end($between_dates).'+'.$repeat_freq.' day'))));
			
				if($day_name=='Saturday' || $day_name=='Sunday')
				{
		                      $between_dates[]=date('Y-m-d',strtotime(end($between_dates).'+'.($repeat_freq+2).' day'));
				}
				else
				{					
				   $D1=end($between_dates);
				   $D2=date('Y-m-d',strtotime(end($between_dates).'+'.($repeat_freq).' day'));
				   $FLAG=0;
				   while($D1 < $D2)
			           {
					
					$day_name_btwn=date('l', strtotime(date('Y-m-d',strtotime($D1.'+1 day'))));
					if($day_name_btwn=='Saturday' || $day_name_btwn=='Sunday')
					{
						$between_dates[]=date('Y-m-d',strtotime(end($between_dates).'+'.($repeat_freq+2).' day'));
						$FLAG=1;
						break;	
					}
										
					$D1=date('Y-m-d',strtotime($D1.'+1 day'));					

				    }   
					
				    if($FLAG==0)
				    {
					$between_dates[]=$D2;
				    }
									
				}
                            }      
                        
                    }

                }    
                                    
	    }		
	    return $between_dates;

	}
	catch(PDOException $e) 
	{
		echo '{"error":{"text":'. $e->getMessage() .'}}'; 
	}

}
   
// method to check if time slot is available
function checkTimeSlot($pc_aid,$pc_eventDate,$pc_endDate,$pc_startTime,$pc_endTime)
{
    try
    {
        
        $db = getConnection();
        
        $S_T=strtotime($pc_startTime);
        $E_T=strtotime($pc_endTime);
        /*
        $sqlGetTimeSlots="SELECT pc_eid,pc_starttime,pc_endtime 
                          FROM openemr_postcalendar_events
                          WHERE pc_aid=$pc_aid
                          AND pc_eventdate=date(now())";
	*/
        $sqlGetTimeSlots="SELECT pc_eid,pc_starttime,pc_endtime 
                  FROM openemr_postcalendar_events
                  WHERE pc_aid=$pc_aid
                  AND pc_eventdate='$pc_eventDate'";


        $stmt = $db->prepare($sqlGetTimeSlots);
        $stmt->execute();
        $resGetTimeSlots = $stmt->fetchAll(PDO::FETCH_OBJ);
        $flag=0;
        $i=0;
        while(isset($resGetTimeSlots[$i]->pc_eid))
        {

            $pc_starttime=strtotime($resGetTimeSlots[$i]->pc_starttime);
            $pc_endtime=strtotime($resGetTimeSlots[$i]->pc_endtime);	 
	
	    $A1=$pc_starttime;
	    $A2=$pc_endtime;
	    $D1=$S_T;
	    $D2=$E_T;

	    if( ($D1>$A1 && $D1<$A2) ||
		($A1>$D1 && $A1<$D2) ||
		($A2>$D1 && $A2<$D2) ||
		($A1==$D1 && $A2==$D2) )
/*            if( ($pc_starttime<=$S_T && $pc_endtime>$S_T) ||
                ($pc_starttime<$E_T && $pc_endtime>=$E_T) ||
                ($pc_starttime>=$S_T && $pc_endtime<=$E_T) ||
                ($pc_starttime<=$S_T && $pc_endtime>=$E_T) )*/
            {
                    $flag=1;
                    $exist_st=$resGetTimeSlots[$i]->pc_starttime;
                    $exist_et=$resGetTimeSlots[$i]->pc_endtime;
                    break;		// clash of the time slots
            }
            $i++;

        }

        if($flag==1)
        {
            $ex=array($exist_st,$exist_et);            
            return $ex;
        }
        else
        {
            return true;
        }

    }
    catch(PDOException $e) 
    {
        echo '{"error":{"text":'. $e->getMessage() .'}}'; 
    }
    
}


function checkTimeSlotsRepeatAppointment( $pc_aid,$pc_eventDate,$pc_endDate,$pc_startTime,$pc_endTime,$pc_alldayevent,$between_dates ) 
{
	try
	{
        $db = getConnection();
 
        $sqlRepeatAppointments="SELECT *
			FROM openemr_postcalendar_events
			WHERE pc_aid=$pc_aid
			AND ( (pc_eventDate >= '$pc_eventDate' AND
			       pc_eventDate <= '$pc_endDate') OR
			      (pc_endDate >= '$pc_eventDate' AND
			       pc_endDate <= '$pc_endDate') OR
			      (pc_eventDate <= '$pc_eventDate' AND
			       pc_endDate >= '$pc_eventDate') OR
			      (pc_eventDate <= '$pc_endDate' AND
			       pc_endDate >= '$pc_endDate')                         
		            )";

        
	$stmt = $db->prepare($sqlRepeatAppointments);
        $stmt->execute();
        $res = $stmt->fetchAll(PDO::FETCH_OBJ);     
        //echo "<br>res=". print_r($res);
	//echo "<br>res==".count($res);die;
        $flag=0;
        if( $res )
	{       
            
            $i=0;
	    while ( isset($res[$i]->pc_eid) )
	    //while ($i<count($res))  
	    {          
		$flag=0;                             
		/*if($pc_endDate=='0000-00-00')
		{
			$pc_endDate=$pc_eventDate;
		}*/
		if($res[$i]->pc_endDate=='0000-00-00')
		{
                    $res[$i]->pc_endDate=$res[$i]->pc_eventDate;
//$res[$i]->pc_endDate=date('Y-m-d',strtotime($res[$i]->pc_eventDate.'+'.$repeat_freq.' day'));
		} 
		
		$recurring_dates=getBetweenDates($res[$i]->pc_recurrspec,$res[$i]->pc_eventDate,$res[$i]->pc_endDate);
		
                foreach($between_dates as $btwn_date)
                {
                    if(in_array($btwn_date, $recurring_dates)) // clash found at particular date
                    {	//echo "<br>Common date = ".$btwn_date;
                        if($res[$i]->pc_alldayevent || $pc_alldayevent==1) 
                        {
				
                            // Cannot create the desired event for the specified date/s
                            $flag=1;
                            break;                    
                        }
                        else
                        {   // check whether times are also clashing or not

			    $A1=strtotime($res[$i]->pc_startTime);
			    $A2=strtotime($res[$i]->pc_endTime);
			    $D1=strtotime($pc_startTime);
			    $D2=strtotime($pc_endTime);
				
                            if( ($D1>$A1 && $D1<$A2) ||
				($A1>$D1 && $A1<$D2) ||
				($A2>$D1 && $A2<$D2) ||
				($A1==$D1 && $A2==$D2) )
                                {
                                    $flag=1;
                                    //echo "<br>sd=".$exist_sd=$res[$i]->pc_eventDate;
                                    //echo "<br>ed=".$exist_ed=$res[$i]->pc_endDate;
                                    //echo "<br>st=".$exist_st=$res[$i]->pc_startTime;
                                    //echo "<br>et=".$exist_et=$res[$i]->pc_endTime;
                                    break;		// clash of the time slots
                                }  
			        
                        }

                    }
		    
                }				                     
		
                if($flag==1)
                {
                    break; 
                }

		$i++;
            }            		
            //echo "<br>FG---".$flag;
		      
            if($flag==0)
            {
                //echo '[{"id":"1"}]';
                //return true;
		return 'Y';
            }
            else
            {
                //echo '[{"id":"0"}]';

		$slot1=$res[$i]->pc_startTime;
		$slot2=$res[$i]->pc_endTime;		
		
		$av_s_hour1=date("H",strtotime($slot1));                    
		$av_s1=date("h:i",strtotime($slot1));
		if($av_s_hour1>=12)
		{                        
			$av_s1=$av_s1." PM";
		}
		else
		{
			$av_s1=$av_s1." AM";
		}

		$av_s_hour2=date("H",strtotime($slot2));                    
		$av_s2=date("h:i",strtotime($slot2));
		if($av_s_hour2>=12)
		{                        
			$av_s2=$av_s2." PM";
		}
		else
		{
			$av_s2=$av_s2." AM";
		}

		$sqlPname="SELECT CONCAT(pd.title,pd.fname,' ',pd.lname) as pname
			FROM patient_data pd
			WHERE pd.pid=".$res[$i]->pc_pid."";
        
		$stmtPname = $db->prepare($sqlPname);
		$stmtPname->execute();
		$resPname = $stmtPname->fetchAll(PDO::FETCH_OBJ);     
 //$msg="Time clash with appointment from ".$res[$i]->pc_eventDate." to ".$res[$i]->pc_endDate." at $av_s1 to $av_s2";
		//print_r($resPname);die;
	$msg="Time clash with appointment at $av_s1 to $av_s2 with ".$resPname[0]->pname."";
		
                //return false;checkTimeSlotsRepeatAppointment
		return $msg;
            }
                          
	}
        else
        {            
            //return true;
		return 'Y';
        }   

	}
	catch(PDOException $e) 
	{
		echo '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
                        
}
// method to create new appointment
function saveAppointment()
{
    try
    {
        $db = getConnection();
        $key = 'rotcoderaclla';
        $request = Slim::getInstance()->request();
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        $result =  GibberishAES::dec($request->getBody(), $key);
//        $result =  GibberishAES::dec('U2FsdGVkX1+jawnGyzHhIXLf0ie+ZcrZEvJRaAGtIqTyx0Ynu/hWb7Q+ivJxvuXPOINoxV2jH60pqAXToWZg/xBZCyXzqsMy+b20kLO1yCApwpkMcTGau4iNXXIE161et25mPdL2JCrJyJwfNpWE+iGJffWdZpMf76hexX68N5vbPtIs/H+kyo7S7jCVrcmQOo8vFBB8UkIAXvB2Opz/LkqezzrLDHzy+OhwenV37e4hwIP0efP+Cd0Jm41adIW+U5bSDtDQH0JPexB3Kbm8g1xhW/3s5zNFrCcSzNdPqhgTb/9oqyx1m6pTugkQVbj7Zs3MUz8lv22hXazjx7D9/8gxFdOyC4hV0qWxvfoBIug=', $key);
        $appointmentDetailsArray = json_decode($result,TRUE);
//        echo "<pre>"; print_r($appointmentDetailsArray); echo "</pre>";
        
        $username = $appointmentDetailsArray['username'];
        $pc_aid=$appointmentDetailsArray['data'][0]['pc_aid'];
        $pc_pid=$appointmentDetailsArray['data'][0]['pc_pid'];        
        $pc_catid=$appointmentDetailsArray['data'][0]['pc_catid'];                
        //$pc_time=$appointmentDetailsArray['pc_time'];
        $pc_title=$appointmentDetailsArray['data'][0]['pc_title'];
        
        $pc_hometext=addslashes($appointmentDetailsArray['data'][0]['pc_hometext']);
        //$pc_informant=$appointmentDetailsArray['informant'];
        $pc_eventDate=$appointmentDetailsArray['data'][0]['pc_eventDate'];
        //$pc_endDate=$appointmentDetailsArray['appointmentEndDate'];
        
        $pc_duration=$appointmentDetailsArray['data'][0]['pc_duration'] * 60; 
        $pc_recurrtype=isset($appointmentDetailsArray['data'][0]['repeat'])? $appointmentDetailsArray['data'][0]['repeat'] : '';
        $pc_recurrspec= 'a:5:{' .
                        's:17:"event_repeat_freq";N;' .
                        's:22:"event_repeat_freq_type";s:1:"0";' .
                        's:19:"event_repeat_on_num";s:1:"1";' .
                        's:19:"event_repeat_on_day";s:1:"0";' .
                        's:20:"event_repeat_on_freq";s:1:"0";}';
        $pc_informant=0;

        if($pc_recurrtype==1)
        {
            $pc_informant=1;
            //$pc_recurrspec=$appointmentDetailsArray['recurrspec'];
            
		$pc_recurrspec = 'a:5:{' .
            's:17:"event_repeat_freq";s:1:"' . $appointmentDetailsArray['data'][0]['repeat_freq'] . '";' .
            's:22:"event_repeat_freq_type";s:1:"'.$appointmentDetailsArray['data'][0]['repeat_type'] .'";' .
            's:19:"event_repeat_on_num";s:1:"1";' .
            's:19:"event_repeat_on_day";s:1:"0";' .
            's:20:"event_repeat_on_freq";s:1:"0";}';
	    if(!is_numeric($appointmentDetailsArray['data'][0]['repeat_type']))
	    {
		$repeat_type_Array=explode(' ',$appointmentDetailsArray['data'][0]['repeat_type']);
		
		$num_str=$repeat_type_Array[0];
		$day_name=$repeat_type_Array[1];
		
		$repeat_on_num=$num_str[0];
		$repeat_on_day=0;
		switch($day_name)
		{
			case 'Monday':$repeat_on_day=1;break;
			case 'Tuesday':$repeat_on_day=2;break;
			case 'Wednesday':$repeat_on_day=3;break;
			case 'Thursday':$repeat_on_day=4;break;
			case 'Friday':$repeat_on_day=5;break;
			case 'Saturday':$repeat_on_day=6;break;
			case 'Sunday':$repeat_on_day=7;break;
		}
		$pc_recurrspec = 'a:6:{' .
		    's:17:"event_repeat_freq";s:1:"0";' .
		    's:22:"event_repeat_freq_type";s:1:"0";' .
		    's:19:"event_repeat_on_num";s:1:"'.$repeat_on_num.'";' .
		    's:19:"event_repeat_on_day";s:1:"'.$repeat_on_day.'";' .
		    's:20:"event_repeat_on_freq";s:1:"0";}';	
		
	    }				

	                    
        }  
	else if($pc_recurrtype==2)
	{
	    $pc_informant=1;
	    if(!is_numeric($appointmentDetailsArray['data'][0]['repeat_type']))
	    {
		$repeat_type_Array=explode(' ',$appointmentDetailsArray['data'][0]['repeat_type']);
		
		$num_str=$repeat_type_Array[0];
		$day_name=$repeat_type_Array[1];
		
		$repeat_on_num=$num_str[0];
		$repeat_on_day=0;
		switch($day_name)
		{
			case 'Monday':$repeat_on_day=1;break;
			case 'Tuesday':$repeat_on_day=2;break;
			case 'Wednesday':$repeat_on_day=3;break;
			case 'Thursday':$repeat_on_day=4;break;
			case 'Friday':$repeat_on_day=5;break;
			case 'Saturday':$repeat_on_day=6;break;
			case 'Sunday':$repeat_on_day=7;break;
		}
		$pc_recurrspec = 'a:6:{' .
		    's:17:"event_repeat_freq";s:1:"0";' .
		    's:22:"event_repeat_freq_type";s:1:"0";' .
		    's:19:"event_repeat_on_num";s:1:"'.$repeat_on_num.'";' .
		    's:19:"event_repeat_on_day";s:1:"'.$repeat_on_day.'";' .
		    's:20:"event_repeat_on_freq";s:1:"0";}';	
		
	    }				

	}
      
	
	$pc_alldayevent=isset($appointmentDetailsArray['data'][0]['allDayEvent'])? $appointmentDetailsArray['data'][0]['allDayEvent'] : '';         
       
	if($pc_alldayevent==0)
	{
		$startTimearray=explode(' ',$appointmentDetailsArray['data'][0]['pc_startTime']);
		
		$am_pm=$startTimearray[1];
		$startTimeH_M=explode(':',$startTimearray[0]);
		$St_H=$startTimeH_M[0];
		$St_M=$startTimeH_M[1];
		
		if($am_pm=='PM' && $St_H<12)
		{
		    $St_H=$St_H + 12;
		}
                
		$pc_startTime=$St_H.":".$St_M.":00"; 
		//$pc_endTime=$appointmentDetailsArray['endTime'];
		$pc_endTime= date( "h:i:s",strtotime($pc_startTime) + $pc_duration);
	     
		$Et_H= date("h",strtotime($pc_startTime) + $pc_duration);
		
		if($am_pm=='PM' || $Et_H >=12)
		//if($am_pm=='PM')
		{
		   $pc_endTime= date( "H:i:s",strtotime($pc_endTime." pm"));       // add 12 hours  
		   
		}
        }     
        
        else if($pc_alldayevent==1)
        {
            $pc_startTime='00:00:00';           
            $pc_endTime='24:00:00';      
            $pc_endDate=$appointmentDetailsArray['data'][0]['appointmentEndDate'];
        }
		
        $pc_alldayevent=isset($appointmentDetailsArray['data'][0]['allDayEvent'] )? $appointmentDetailsArray['data'][0]['allDayEvent'] :''; 
        $pc_endDate=isset($appointmentDetailsArray['data'][0]['appointmentEndDate'])? $appointmentDetailsArray['data'][0]['appointmentEndDate'] : $appointmentDetailsArray['data'][0]['pc_eventDate'];	

        $pc_apptstatus=$appointmentDetailsArray['data'][0]['pc_apptstatus']; 
        //$pc_apptstatus='-'; 
        //$pc_prefcatid=$appointmentDetailsArray['prefcatid']; 
        //$pc_location=$appointmentDetailsArray['location']; 
        //$pc_location='';
        //$pc_eventstatus=$appointmentDetailsArray['eventstatus']; 
        $pc_eventstatus=1;
        //$pc_sharing=$appointmentDetailsArray['title']; 
        $pc_facility=$appointmentDetailsArray['data'][0]['pc_facility']; 
        $billing_facility=$appointmentDetailsArray['data'][0]['pc_billing_location']; 
        
        //$pc_billing_location=$appointmentDetailsArray['billing_location'];
        //$pc_billing_location='';
               
	$between_dates=array();
	$between_dates=getBetweenDates($pc_recurrspec,$pc_eventDate,$pc_endDate);

        
$sqlInsertAppointmentData = "INSERT INTO openemr_postcalendar_events 
(pc_catid, pc_aid, pc_pid, pc_title,pc_time, pc_hometext,pc_informant, pc_eventDate, pc_endDate, pc_duration, pc_recurrtype, pc_recurrspec, pc_startTime, pc_endTime, pc_alldayevent, pc_apptstatus, pc_eventstatus, pc_facility, pc_billing_location ) 
VALUES ('$pc_catid','$pc_aid','$pc_pid','$pc_title',now(),'$pc_hometext','$pc_informant','$pc_eventDate','$pc_endDate','$pc_duration','$pc_recurrtype','$pc_recurrspec','$pc_startTime','$pc_endTime','$pc_alldayevent','$pc_apptstatus','$pc_eventstatus','$pc_facility','$billing_facility')";

$stmtInsertAppointmentData = $db->prepare($sqlInsertAppointmentData) ;             
                                     
$checkSlot=checkTimeSlot($pc_aid,$pc_eventDate,$pc_endDate,$pc_startTime,$pc_endTime);
$checkSlotRepeatAppointment=checkTimeSlotsRepeatAppointment($pc_aid,$pc_eventDate,$pc_endDate,$pc_startTime,$pc_endTime,$pc_alldayevent,$between_dates);
//	echo "T/F=".$checkSlotRepeatAppointment;die;
        if(!is_array($checkSlot) && $checkSlot==true && $checkSlotRepeatAppointment=='Y')
        {
		//echo "1st if";
            if($stmtInsertAppointmentData->execute())
            {
                $appfixed = '[{"id":"1"}]';
                echo $appfixedresult = GibberishAES::enc($appfixed, $key);
                insertMobileLog('insert',$username,$sqlInsertAppointmentData,$pc_pid,'','Create Appointment',1);
            }
            else
            {
                $appfixed = '[{"id":"0"}]';
                echo $appfixedresult = GibberishAES::enc($appfixed, $key);
                insertMobileLog('insert',$username,$sqlInsertAppointmentData,$pc_pid,'','Create Appointment - Failed',0);
            }
        }
	
	else if(is_string($checkSlotRepeatAppointment) && $checkSlotRepeatAppointment!='Y')
	{//echo "2st else if";
		$apprefixed = '[{"id":"0","msg":"'.$checkSlotRepeatAppointment.'"}]';
                echo $apprefixedresult = GibberishAES::enc($apprefixed, $key);
		//echo '[{"id":"0","msg":"Time Clash"}]';		
	}
        else if(is_array($checkSlot))
        {//echo "3rd else if";
            if(date("H",strtotime($checkSlot[0]))>=12)
            {
                $from=date("h:i",strtotime($checkSlot[0]))." PM";     
            }
            else
            {
                $from=date("h:i",strtotime($checkSlot[0]))." AM";     
            }
                        
            if(date("H",strtotime($checkSlot[1]))>=12)
            {
                $to=date("h:i",strtotime($checkSlot[1]))." PM";     
            }
            else
            {
                $to=date("h:i",strtotime($checkSlot[1]))." AM";     
            }
                                    
            //echo '[{"id":"0","msg":"An appointment is fixed from '.$checkSlot[0].' and '.$checkSlot[1].'"}]';
            $appfixed = '[{"id":"0","msg":"An appointment is fixed from '.$from.' and '.$to.'"}]';           
            echo $appfixedresult = GibberishAES::enc($appfixed, $key);
            insertMobileLog('insert',$username,$sqlInsertAppointmentData,$pc_pid,'','Create Appointment',1);
            
            //echo '[{"id":"0"}]';
        }

                
    }
    catch(PDOException $e) 
    {
        $appfixed = '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo $appfixedresult = GibberishAES::enc($appfixed, $key);
        insertMobileLog('insert',$username,$appfixed,$pc_pid,'','Create Appointment- Query Failed',1);
    }
}
function editReminder($providerid,$id){
    $apikey = 'rotcoderaclla';
    // The default key size is 256 bits.
    $old_key_size = GibberishAES::size();
    GibberishAES::size(256);
    try
    {
        $db = getConnection();
        $issues = array();
        
        $issues[0]['Label'] = 'Link To Patient :';
        $issues[0]['Url'] = "/api/searchpatientbyname/$providerid/";
        $issues[0]['Type'] = 'searchbox';
        $issues[0]['field_id'] = 'pid';
        $issues[0]['isRequired'] = 'Optional';

        $qry = "select id, concat(lname,', ',fname) as full_name " .
                           "from users where active=1 AND authorized=1 " .
                           "order by concat(lname,', ',fname)";

        $stmt = $db->prepare($qry) ;
        $stmt->execute();
        $rs = $stmt->fetchAll(PDO::FETCH_OBJ);
        if(!empty($rs)){
            for($i=0; $i< count($rs); $i++){
                $usernames[$i]['optionid'] = $rs[$i]->id;
                $usernames[$i]['optionval'] = $rs[$i]->full_name;
            }
        }
        
        $issues[1]['Label'] = 'Send to :';
        $issues[1]['field_id'] = 'to';
        $issues[1]['Type'] = 'MultiSelect';
        $issues[1]['isRequired'] = 'Required';
        $issues[1]['Options'] = $usernames;
        
        $issues[2]['Label'] = 'Due Date :';
        $issues[2]['field_id'] = 'due_date';
        $issues[2]['Type'] = 'Date';
        $issues[2]['isRequired'] = 'Required';
        
        /*$dateRanges[0]['optionid'] = '1_day';
        $dateRanges[0]['optionval'] = '1 Day From Now';
        $dateRanges[1]['optionid'] = '2_day';
        $dateRanges[1]['optionval'] ='2 Days From Now';  
        $dateRanges[2]['optionid'] = '3_day';
        $dateRanges[2]['optionval'] ='3 Days From Now';  
        $dateRanges[3]['optionid'] = '4_day';
        $dateRanges[3]['optionval'] ='4 Days From Now';  
        $dateRanges[4]['optionid'] = '5_day';
        $dateRanges[4]['optionval'] ='5 Days From Now'; 
        $dateRanges[5]['optionid'] = '6_day';
        $dateRanges[5]['optionval'] ='6 Days From Now';
        $dateRanges[6]['optionid'] = '1_week';
        $dateRanges[6]['optionval'] ='1 Week From Now';
        $dateRanges[7]['optionid'] = '2_week';
        $dateRanges[7]['optionval'] ='2 Weeks From Now';
        $dateRanges[8]['optionid'] = '3_week';
        $dateRanges[8]['optionval'] ='3 Weeks From Now';
        $dateRanges[9]['optionid'] = '4_week';
        $dateRanges[9]['optionval'] ='4 Weeks From Now';
        $dateRanges[10]['optionid'] = '5_week';
        $dateRanges[10]['optionval'] ='5 Weeks From Now';
        $dateRanges[11]['optionid'] = '6_week';
        $dateRanges[11]['optionval'] ='6 Weeks From Now';
        $dateRanges[12]['optionid'] = '1_month';
        $dateRanges[12]['optionval'] ='1 Month From Now';  
        $dateRanges[13]['optionid'] = '2_month';
        $dateRanges[13]['optionval'] ='2 Months From Now'; 
        $dateRanges[14]['optionid'] = '3_month';
        $dateRanges[14]['optionval'] ='3 Months From Now';
        $dateRanges[15]['optionid'] = '4_month';
        $dateRanges[15]['optionval'] ='4 Months From Now';
        $dateRanges[16]['optionid'] = '5_month';
        $dateRanges[16]['optionval'] ='5 Months From Now';
        $dateRanges[17]['optionid'] = '6_month';
        $dateRanges[17]['optionval'] ='6 Months From Now'; 
        $dateRanges[18]['optionid'] = '7_month';
        $dateRanges[18]['optionval'] ='7 Months From Now';
        $dateRanges[19]['optionid'] = '8_month';
        $dateRanges[19]['optionval'] ='8 Months From Now';
        $dateRanges[20]['optionid'] = '9_month';
        $dateRanges[20]['optionval'] ='9 Months From Now'; 
        $dateRanges[21]['optionid'] = '1_year';
        $dateRanges[21]['optionval'] ='1 Year From Now';  
        $dateRanges[22]['optionid'] = '2_year';
        $dateRanges[22]['optionval'] ='2 Years From Now';
        
        $issues[3]['Label'] = 'Select a Time Span :';
        $issues[3]['field_id'] = 'pc_duration';
        $issues[3]['Type'] = 'ListBox';
        $issues[3]['isRequired'] = 'Required';
        $issues[3]['Options'] = $dateRanges;*/
        
        $message_priority[0]['optionid'] = '3';
        $message_priority[0]['optionval'] = 'Low';
        $message_priority[1]['optionid'] = '2';
        $message_priority[1]['optionval'] ='Medium';  
        $message_priority[2]['optionid'] = '1';
        $message_priority[2]['optionval'] ='High'; 
        
        $issues[4]['Label'] = 'Priority :';
        $issues[4]['field_id'] = 'message_priority';
        $issues[4]['Type'] = 'radio buttons';
        $issues[4]['isRequired'] = 'Required';
        $issues[4]['Options'] = $message_priority;
        
        $issues[5]['Label']         = 'Type Your message here :';
        $issues[5]['field_id']      = 'dr_message_text';
        $issues[5]['Type']          = 'TextArea';
        $issues[5]['isRequired']    = 'Optional';
        $issues[5]['placeholder']   = 'Type Your message here..';
         
        if($id != 0){
            $get_layout = "SELECT 
                    dr.pid, CONCAT(pd.fname,' ',pd.lname) AS patient_name, dr.dr_id, dr.dr_message_text,dr.dr_message_due_date, message_priority
                    FROM `dated_reminders` dr 
                    JOIN `dated_reminders_link` drl ON dr.dr_id = drl.dr_id 
                    JOIN `patient_data` pd ON dr.pid = pd.pid  
                    WHERE drl.to_id = '$providerid'
                    AND dr.`message_processed` = 0
                    AND dr.dr_id= '$id' ";
            $db->query( "SET NAMES utf8");
            $stmt_layout = $db->prepare($get_layout) ;
            $stmt_layout->execute();

            $layout_fields = $stmt_layout->fetchAll(PDO::FETCH_OBJ);
            
        }
        
        $issues[0]['SelectedValue']     = isset($layout_fields[0]->pid)                 ? $layout_fields[0]->pid                : '';
        if($id != 0 )
            $issues[1]['SelectedValue'] = isset($providerid)                            ? $providerid                           : '';
        else
            $issues[1]['SelectedValue']     =  '';
        $issues[2]['SelectedValue']     = isset($layout_fields[0]->dr_message_due_date) ? $layout_fields[0]->dr_message_due_date: '';
        $issues[4]['SelectedValue']     = isset($layout_fields[0]->message_priority)    ? $layout_fields[0]->message_priority   : '';
        $issues[5]['SelectedValue']     = isset($layout_fields[0]->dr_message_text)     ? $layout_fields[0]->dr_message_text    : '';
       
//        echo "<pre>"; print_r(array_values($issues)); echo "</pre>";
        if($issues)
        {
            $patientres = json_encode(($issues));
            echo $patientresult = GibberishAES::enc($patientres, $apikey);
        }
        else
        {
            $patientres =  '[{"id":"0"}]';
            echo $patientresult = GibberishAES::enc($patientres, $apikey);
        }
    }
    catch(PDOException $e)
    {

        $patientres =  '{"error":{"text":'. $e->getMessage() .'}}';
        echo $patientresult = GibberishAES::enc($patientres, $apikey);
    }
}  
//save Reminder
function saveReminder(){
    try{
        $key = 'rotcoderaclla';
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        $db = getConnection();
        $flag=0;
        $request = Slim::getInstance()->request();
        $msgresult =  GibberishAES::dec($request->getBody(), $key);
//        $msgresult =  GibberishAES::dec('U2FsdGVkX18jNhZxOqACJjjEONJElVEG2aLwrTZ2HXZxb5XNqCZKXY3MkVQmSLhf3ePLSeURaIp2PJnu3+d7UpqZy7vlnymY/BfV23zzQL3ZNaAPqiwLl2lzi60UwbX+Qf3x7FqpK/36bb9MnSc0d3Z57exbQflAE/x4BVutHWtGTzndp21qQgkoatqlGI4P2ZrftAgc3kq5y8qa5vLZm9T0N6e2B/cf7BJ2huTL3mNGYH6FA1KcZ173+aGdBXUS', $key);
        $reminderArray = json_decode($msgresult,TRUE);
//        echo "<pre>";print_r($reminderArray);echo "</pre>"; 

        
        $username       = $reminderArray['username'];  
        $id             = $reminderArray['id'];  
        $to_array       = $reminderArray['data'][0]['to'];  
        $pid            = $reminderArray['data'][0]['pid'];
	$from           = $reminderArray['userid'];
        $message        = addslashes($reminderArray['data'][0]['dr_message_text']);
        $due_date       = $reminderArray['data'][0]['due_date'];
        $msg_status     = 0 ; 
        $msg_priority   = $reminderArray['data'][0]['message_priority'];
  
        $to_explode = explode('|',$to_array);
        $resultant_error = 0;
        $resultant_error2 = 0;
        $resultant = 0;
        $save_reminder= "INSERT INTO dated_reminders(dr_from_ID,dr_message_text,dr_message_sent_date,dr_message_due_date,pid,message_priority,message_processed,processed_date,dr_processed_by)
            VALUES('$from','$message',NOW(),'$due_date','$pid','$msg_priority','$msg_status',NOW(),0)";

        $db->query( "SET NAMES utf8"); 
        $q = $db->prepare($save_reminder);

        if($q->execute()){  
            insertMobileLog('insert',$username,$save_reminder,$pid,'','Create Reminder',1);
            $lastId = $db->lastInsertId();
            foreach($to_explode as $to){
                $save_reminder_link = "INSERT INTO dated_reminders_link (dr_id,to_id) VALUES ($lastId, $to)";

                $q2 = $db->prepare($save_reminder_link);
                if($q2->execute())
                {
                    $resultant = 1;
                    insertMobileLog('insert',$username,$save_reminder_link,$pid,'','Create Reminder Link ',1);
                }
                else
                {
                      $resultant_error = 1;
                      insertMobileLog('insert',$username,$save_reminder_link,$pid,'','Create Reminder Link - Failed',0);
                }
            }
        }else{
            $resultant_error2 = 1;
            insertMobileLog('insert',$username,$save_reminder,$pid,'','Create Reminder - Failed',0);
       }

        if($resultant == 1 && $resultant_error == 0 && $resultant_error2 == 0){
            $result = '[{"id":"1"}]';
            echo $result2 = GibberishAES::enc($result, $key);
        }else{
            $result = '[{"id":"0"}]';
            echo $result2 = GibberishAES::enc($result, $key);
        }
            
        	
    }catch(PDOException $e){
	$error = '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo $agencydataresult = GibberishAES::enc($error, $key);
        insertMobileLog('insert',$username,$error,$pid,'','Create Reminder - Query Failed',0);
    }
	
}
function editInsurance($type,$pid,$id){
    $apikey = 'rotcoderaclla';
    // The default key size is 256 bits.
    $old_key_size = GibberishAES::size();
    GibberishAES::size(256);
    try
    {
        $db = getConnection();
        $issues = array();
        
        $issues[0]['Label']         = 'Insurance Type';
        $issues[0]['SelectedValue'] = 'Primary';
        $issues[0]['Type']          = 'NotEditable';
        $issues[0]['field_id']      = 'type';
       
        $get_insurance = "SELECT id,name FROM `insurance_companies` order by name";
        $db->query( "SET NAMES utf8");
        $stmt_insurance = $db->prepare($get_insurance) ;
        $stmt_insurance->execute();
        $get_insurancelist= array();
        $get_insurancelist2 = $stmt_insurance->fetchAll(PDO::FETCH_OBJ);
        for($i=0; $i< count($get_insurancelist2); $i++){
            $get_insurancelist[$i]['optionid'] = $get_insurancelist2[$i]->id;
            $get_insurancelist[$i]['optionval'] = $get_insurancelist2[$i]->name;
        }
        
        $issues[1]['Label']         = 'Primary Insurance Provider';
        $issues[1]['field_id']      = 'provider';
        $issues[1]['Type']          = 'ListBox';
        $issues[1]['isRequired']    = 'Required';
        $issues[1]['Options']       = $get_insurancelist;
        
        $issues[2]['Label']         = 'Plan Name';
        $issues[2]['field_id']      = 'plan_name';
        $issues[2]['Type']          = 'TextBox';
        $issues[2]['isRequired']    = 'Required';
        $issues[2]['placeholder']   = 'plan name';
        
        $issues[3]['Label']         = 'Effective Date';
        $issues[3]['field_id']      = 'date';
        $issues[3]['Type']          = 'Date';
        $issues[3]['isRequired']    = 'Optional';
        $issues[3]['placeholder']   = 'select date';
        
        $issues[4]['Label']         = 'Policy Number';
        $issues[4]['field_id']      = 'policy_number';
        $issues[4]['Type']          = 'TextBox';
        $issues[4]['isRequired']    = 'Optional';
        $issues[4]['placeholder']   = 'Enter policy number';
        
        $issues[5]['Label']         = 'Group Number';
        $issues[5]['field_id']      = 'group_number';
        $issues[5]['Type']          = 'TextBox';
        $issues[5]['isRequired']    = 'Optional';
        $issues[5]['placeholder']   = 'Enter group number';
                
        $issues[6]['Label']         = 'Subscriber Employer (SE)';
        $issues[6]['field_id']      = 'subscriber_employer';
        $issues[6]['Type']          = 'TextBox';
        $issues[6]['isRequired']    = 'Required';
        $issues[6]['placeholder']   = 'Enter subscriber employer number';
        
        $issues[7]['Label']         = 'SE Address';
        $issues[7]['field_id']      = 'subscriber_employer_street';
        $issues[7]['Type']          = 'TextBox';
        $issues[7]['isRequired']    = 'Required';
        $issues[7]['placeholder']   = 'Enter subscriber employer street';
        
        $issues[8]['Label']         = 'SE City';
        $issues[8]['field_id']      = 'subscriber_employer_city';
        $issues[8]['Type']          = 'TextBox';
        $issues[8]['isRequired']    = 'Required';
        $issues[8]['placeholder']   = 'Enter subscriber employer city';
        
        $get_state = "select option_id, title from list_options where list_id='state' order by title";
        $db->query( "SET NAMES utf8");
        $stmt_state = $db->prepare($get_state) ;
        $stmt_state->execute();
        $get_statelist= array();
        $get_statelist2 = $stmt_state->fetchAll(PDO::FETCH_OBJ);
        for($i=0; $i< count($get_statelist2); $i++){
            $get_statelist[$i]['optionid'] = $get_statelist2[$i]->option_id;
            $get_statelist[$i]['optionval'] = $get_statelist2[$i]->title;
        }
        
        $issues[9]['Label']         = 'SE State';
        $issues[9]['field_id']      = 'subscriber_employer_state';
        $issues[9]['Type']          = 'ListBox';
        $issues[9]['isRequired']    = 'Required';
        $issues[9]['Options']       = $get_statelist;
        
        $issues[10]['Label']         = 'SE Zip Code';
        $issues[10]['field_id']      = 'subscriber_employer_postal_code';
        $issues[10]['Type']          = 'TextBox';
        $issues[10]['placeholder']   = 'Enter subscriber employer postal code';
       
        $get_country = "select option_id, title from list_options where list_id='country' order by title";
        $db->query( "SET NAMES utf8");
        $stmt_country = $db->prepare($get_country) ;
        $stmt_country->execute();
        $get_countrylist= array();
        $get_countrylist2 = $stmt_country->fetchAll(PDO::FETCH_OBJ);
        for($i=0; $i< count($get_countrylist2); $i++){
            $get_countrylist[$i]['optionid'] = $get_countrylist2[$i]->option_id;
            $get_countrylist[$i]['optionval'] = $get_countrylist2[$i]->title;
        }
        
        $issues[11]['Label']         = 'SE Country';
        $issues[11]['field_id']      = 'subscriber_employer_country';
        $issues[11]['Type']          = 'ListBox';
        $issues[11]['isRequired']    = 'Required';
        $issues[11]['Options']       = $get_countrylist;
        
        $issues[12]['Label']         = 'Relationship';
        $issues[12]['field_id']      = 'subscriber_relationship';
        $issues[12]['Type']          = 'TextBox';
        $issues[12]['isRequired']    = 'Required';
        $issues[12]['placeholder']   = 'Enter subscriber relationship';
        
        $issues[13]['Label']         = 'Subscriber';
        $issues[13]['field_id']      = 'subscriber_fname';
        $issues[13]['Type']          = 'TextBox';
        $issues[13]['isRequired']    = 'Required';
        $issues[13]['placeholder']   = 'Enter subscriber first name';
        
        $issues[14]['Label']         = '';
        $issues[14]['field_id']      = 'subscriber_mname';
        $issues[14]['Type']          = 'TextBox';
        $issues[14]['isRequired']    = 'Required';
        $issues[14]['placeholder']   = 'Enter subscriber middle name';
        
        $issues[15]['Label']         = '';
        $issues[15]['field_id']      = 'subscriber_lname';
        $issues[15]['Type']          = 'TextBox';
        $issues[15]['isRequired']    = 'Required';
        $issues[15]['placeholder']   = 'Enter subscriber last name';
        
        $issues[16]['Label']         = 'D.O.B.';
        $issues[16]['field_id']      = 'subscriber_DOB';
        $issues[16]['Type']          = 'Date';
        $issues[16]['isRequired']    = 'Optional';
        $issues[16]['placeholder']   = 'Select subscriber date of birth';
        
        $get_gender = "select option_id, title from list_options where list_id='sex' order by title";
        $db->query( "SET NAMES utf8");
        $stmt_gender = $db->prepare($get_gender) ;
        $stmt_gender->execute();
        $get_genderlist= array();
        $get_genderlist2 = $stmt_gender->fetchAll(PDO::FETCH_OBJ);
        for($i=0; $i< count($get_genderlist2); $i++){
            $get_genderlist[$i]['optionid'] = $get_genderlist2[$i]->option_id;
            $get_genderlist[$i]['optionval'] = $get_genderlist2[$i]->title;
        }
        $issues[17]['Label']         = 'Sex';
        $issues[17]['field_id']      = 'subscriber_sex';
        $issues[17]['Type']          = 'ListBox';
        $issues[17]['Options']       = $get_genderlist;
                
        $issues[18]['Label']         = 'S.S.';
        $issues[18]['field_id']      = 'subscriber_ss';
        $issues[18]['Type']          = 'TextBox';
        $issues[18]['isRequired']    = 'Required';
        $issues[18]['placeholder']   = 'Enter subscriber SS';
        
        $issues[19]['Label']         = 'Subscriber Address';
        $issues[19]['field_id']      = 'subscriber_street';
        $issues[19]['Type']          = 'TextBox';
        $issues[19]['isRequired']    = 'Required';
        $issues[19]['placeholder']   = 'Enter subscriber street address';
        
        $issues[20]['Label']         = 'State';
        $issues[20]['field_id']      = 'subscriber_state';
        $issues[20]['Type']          = 'ListBox';
        $issues[20]['isRequired']    = 'Required';
        $issues[20]['Options']       = $get_statelist;
        
        $issues[21]['Label']         = 'City';
        $issues[21]['field_id']      = 'subscriber_city';
        $issues[21]['Type']          = 'TextBox';
        $issues[21]['isRequired']    = 'Required';
        $issues[21]['placeholder']   = 'Enter subscriber city';
        
        $issues[22]['Label']         = 'Country';
        $issues[22]['field_id']      = 'subscriber_country';
        $issues[22]['Type']          = 'ListBox';
        $issues[22]['Options']       = $get_countrylist;
       
        $issues[23]['Label']         = 'Zip Code';
        $issues[23]['field_id']      = 'subscriber_postal_code';
        $issues[23]['Type']          = 'TextBox';
        $issues[23]['isRequired']    = 'Required';
        $issues[23]['placeholder']   = 'Enter subscriber postal code';
        
        $issues[24]['Label']         = 'Subscriber Phone';
        $issues[24]['field_id']      = 'subscriber_phone';
        $issues[24]['Type']          = 'TextBox';
        $issues[24]['isRequired']    = 'Required';
        $issues[24]['placeholder']   = 'Enter subscriber phone number';
        
        $issues[25]['Label']         = 'CoPay';
        $issues[25]['field_id']      = 'copay';
        $issues[25]['Type']          = 'TextBox';
        $issues[25]['isRequired']    = 'Required';
        $issues[25]['placeholder']   = 'Enter copay';
        
        $accept_assignment[0]['optionid']   = 'TRUE';
        $accept_assignment[0]['optionval']  = 'YES';
        $accept_assignment[1]['optionid']   = 'FALSE';
        $accept_assignment[1]['optionval']  ='NO';  
        
        $issues[26]['Label']         = 'Accept Assignment';
        $issues[26]['field_id']      = 'accept_assignment';
        $issues[26]['Type']          = 'ListBox';
        $issues[26]['isRequired']    = 'Optional';
        $issues[26]['Options']       = $accept_assignment;
        
        $secondary_medicare_type[0]['optionid']     = '';
        $secondary_medicare_type[0]['optionval']    = 'N/A';
        $secondary_medicare_type[1]['optionid']     = '12';
        $secondary_medicare_type[1]['optionval']    ='Working Aged Beneficiary or Spouse with Employer Group Health Plan';  
        $secondary_medicare_type[2]['optionid']     = '13';
        $secondary_medicare_type[2]['optionval']    ='End-Stage Renal Disease Beneficiary in MCP with Employer`s Group Plan';  
        $secondary_medicare_type[3]['optionid']     = '14';
        $secondary_medicare_type[3]['optionval']    ='No-fault Insurance including Auto is Primary';  
        $secondary_medicare_type[4]['optionid']     = '15';
        $secondary_medicare_type[4]['optionval']    ='Worker`s Compensation'; 
        $secondary_medicare_type[5]['optionid']     = '16';
        $secondary_medicare_type[5]['optionval']    ='Public Health Service (PHS) or Other Federal Agency';
        $secondary_medicare_type[6]['optionid']     = '41';
        $secondary_medicare_type[6]['optionval']    ='Black Lung';
        $secondary_medicare_type[7]['optionid']     = '42';
        $secondary_medicare_type[7]['optionval']    ='Veteran`s Administration';
        $secondary_medicare_type[8]['optionid']     = '43';
        $secondary_medicare_type[8]['optionval']    ='Disabled Beneficiary Under Age 65 with Large Group Health Plan (LGHP)';
        $secondary_medicare_type[9]['optionid']     = '47';
        $secondary_medicare_type[9]['optionval']    ='Other Liability Insurance is Primary';

      
        $issues[27]['Label']         = 'Secondary Medicare Type';
        $issues[27]['field_id']      = 'policy_type';
        $issues[27]['Type']          = 'ListBox';
        $issues[27]['Options']       = $secondary_medicare_type;
                
        
        if($id != 0){
            $get_layout = "SELECT i.id, i.provider,ic.name ,i.group_number ,i.plan_name,i.policy_number,i.subscriber_relationship ,
            i.subscriber_ss,i.subscriber_DOB ,i.subscriber_street ,i.subscriber_postal_code ,i.subscriber_city , i.subscriber_state , i.subscriber_country ,
            i.subscriber_employer ,i.subscriber_employer_street ,i.subscriber_employer_postal_code,i.subscriber_employer_state,i.subscriber_employer_country, i.subscriber_employer_city ,
            i.copay ,i.date, i.subscriber_sex ,i.accept_assignment ,subscriber_phone,subscriber_fname,subscriber_lname,subscriber_mname,i.date,
            i.policy_type
               
            from insurance_data i 
            inner join insurance_companies ic on ic.id=i.provider
            where pid=$pid and type = '$type' and i.id = $id ";
            $db->query( "SET NAMES utf8");
            $stmt_layout = $db->prepare($get_layout) ;
            $stmt_layout->execute();

            $layout_fields = $stmt_layout->fetchAll(PDO::FETCH_OBJ);
            
        }
        $issues[0]['SelectedValue']      = isset($type)                                             ? $type                                             : '';
        $issues[1]['SelectedValue']      = isset($layout_fields[0]->provider)                       ? $layout_fields[0]->provider                       : '';
        $issues[2]['SelectedValue']      = isset($layout_fields[0]->plan_name)                      ? $layout_fields[0]->plan_name                      : '';
        $issues[3]['SelectedValue']      = isset($layout_fields[0]->date)                           ? $layout_fields[0]->date                           : '';
        $issues[4]['SelectedValue']      = isset($layout_fields[0]->policy_number)                  ? $layout_fields[0]->policy_number                  : '';
        $issues[5]['SelectedValue']      = isset($layout_fields[0]->group_number)                   ? $layout_fields[0]->group_number                   : '';
        $issues[6]['SelectedValue']      = isset($layout_fields[0]->subscriber_employer)            ? $layout_fields[0]->subscriber_employer            : '';
        $issues[7]['SelectedValue']      = isset($layout_fields[0]->subscriber_employer_street)     ? $layout_fields[0]->subscriber_employer_street     : '';
        $issues[8]['SelectedValue']      = isset($layout_fields[0]->subscriber_employer_city)       ? $layout_fields[0]->subscriber_employer_city       : '';
        $issues[9]['SelectedValue']      = isset($layout_fields[0]->subscriber_employer_state)      ? $layout_fields[0]->subscriber_employer_state      : '';
        $issues[10]['SelectedValue']     = isset($layout_fields[0]->subscriber_employer_postal_code)? $layout_fields[0]->subscriber_employer_postal_code: '';
        $issues[11]['SelectedValue']     = isset($layout_fields[0]->subscriber_employer_country)    ? $layout_fields[0]->subscriber_employer_country    : '';
        $issues[12]['SelectedValue']     = isset($layout_fields[0]->subscriber_relationship)        ? $layout_fields[0]->subscriber_relationship        : '';
        $issues[13]['SelectedValue']     = isset($layout_fields[0]->subscriber_fname)               ? $layout_fields[0]->subscriber_fname               : '';
        $issues[14]['SelectedValue']     = isset($layout_fields[0]->subscriber_mname)               ? $layout_fields[0]->subscriber_mname               : '';
        $issues[15]['SelectedValue']     = isset($layout_fields[0]->subscriber_lname)               ? $layout_fields[0]->subscriber_lname               : '';
        $issues[16]['SelectedValue']     = isset($layout_fields[0]->subscriber_DOB)                 ? $layout_fields[0]->subscriber_DOB                 : '';
        $issues[17]['SelectedValue']     = isset($layout_fields[0]->subscriber_sex)                 ? $layout_fields[0]->subscriber_sex                 : '';
        $issues[18]['SelectedValue']     = isset($layout_fields[0]->subscriber_ss)                  ? $layout_fields[0]->subscriber_ss                  : '';
        $issues[19]['SelectedValue']     = isset($layout_fields[0]->subscriber_street)              ? $layout_fields[0]->subscriber_street              : '';
        $issues[20]['SelectedValue']     = isset($layout_fields[0]->subscriber_state)               ? $layout_fields[0]->subscriber_state               : '';
        $issues[21]['SelectedValue']     = isset($layout_fields[0]->subscriber_city)                ? $layout_fields[0]->subscriber_city                : '';
        $issues[22]['SelectedValue']     = isset($layout_fields[0]->subscriber_country)             ? $layout_fields[0]->subscriber_country             : '';
        $issues[23]['SelectedValue']     = isset($layout_fields[0]->subscriber_postal_code)         ? $layout_fields[0]->subscriber_postal_code         : '';
        $issues[24]['SelectedValue']     = isset($layout_fields[0]->subscriber_phone)               ? $layout_fields[0]->subscriber_phone               : '';
        $issues[25]['SelectedValue']     = isset($layout_fields[0]->copay)                          ? $layout_fields[0]->copay                          : '';
        $issues[26]['SelectedValue']     = isset($layout_fields[0]->accept_assignment)              ? $layout_fields[0]->accept_assignment              : '';
        $issues[27]['SelectedValue']     = isset($layout_fields[0]->policy_type)                    ? $layout_fields[0]->policy_type                    : '';
        

       
//        echo "<pre>"; print_r(array_values($issues)); echo "</pre>";
        if($issues)
        {
            $patientres = json_encode(($issues));
            echo $patientresult = GibberishAES::enc($patientres, $apikey);
        }
        else
        {
            $patientres =  '[{"id":"0"}]';
            echo $patientresult = GibberishAES::enc($patientres, $apikey);
        }
    }
    catch(PDOException $e)
    {

        $patientres =  '{"error":{"text":'. $e->getMessage() .'}}';
        echo $patientresult = GibberishAES::enc($patientres, $apikey);
    }
} 
function saveInsurance(){
    try{
        $db = getConnection();
        $request = Slim::getInstance()->request();
        $apikey = 'rotcoderaclla';
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        $appres = GibberishAES::dec($request->getBody(), $apikey);
//        $appres = GibberishAES::dec("U2FsdGVkX1+0SP9HQn2ew6a9daDX6N2/EM5YvdABc27pzkD5y57y3Tk07zZMppp0OpoA2WZpbfNyTxHnVR3EZXNLdNvgYe6K7Siat23kZEOOXklhNRR62JtvQ+0r/Bjr+Py+D0qAKN5A27antShlrClmCtnAVh9F1oxksaek7lGTCJ8PTJnLhnxSbvWtkkJZC8sm/8ouxFM7v4LLCgZixinuTveu5cWDa5kzNzfHV0phXcd1Z6LlM/diEEa5z7oaWBwAErRP/Jt+8lnSQJUJx5LIpPVuh480S83pbrKqkdxU8usK5x2ZjqdDi0wzBexjJw867lNksrL0j7VEGhmkdbLcboU/PIwaDt3fcgpyC5wV3BZAvYKTH5E/io2mr8yAZb7OfkNHRHoNENAWNysdiKkLpMFP0ATcOdCJ6FI5dRNl0Gmvtr5Em6sFFgxTg/56kmxwBjUDFJGw8pUo3eFHPdEHdIYXRC5QXe4KlBlXENd7jDF3GyZuhGIL9AS+HrCuITMbof7dgEsKhF3SE8hbhlLCZKAaaKXYZhk7BZNmutWUSAACzSsj9f90hbS/N57173SQSxuPRkQsUTqxMI9qH8h5egfwxOsqmlH465YiyYwFhT57e8A1UJzKYfHA3KtgCSAhmaPhWKMDjqt0R5oZ8fsEzx9kEYweuiPRisoZEDSQ23CFcgCxY/cL+4R1ssJkTl5rPfE4Dhi/Gh25ipj46QgPGv8yxD6FNP2XmpVprrxc7Pp8hH41QBm4B9DJvYd/uZal4ClKUZag4vcHEMdYNOM4VyEnbJZ/t2EQycdErZf/rrZeTEMPaCFe8YRBubWfhip+12EE6a/fgA0jZvFjQSxgpEuu8SF5yYxLEXVHIcgLWKd2GlxrJZjm4VORW9YgeHL2/eJpFT3Neb65ApuuEN7AJqGQvOHeojCayMcIF2V2pDQczF/tSKwmKE+gbGEFKr1hJ4wG9FLefbQx2Gum2L7S5/Y4qKgqMu1dC6s871gMkWy+6uiIR10HaY+pz7mz", $apikey);
        $insertArray = json_decode($appres,TRUE);
//        echo "<pre>"; print_r($insertArray); echo "</pre>";
        $insurancetype = '';
        $updatequery = '';
        $setquery2 = '';
        $setvalues2= '';
        $id = $insertArray['id'];
        $username   = $insertArray['username'];
        $pid        = $insertArray['pid'];
        $insurancetypes = array("primary","secondary","teritary");
        foreach($insertArray['data'][0] as $key => $value){
            ${$key} = $value;
            if($key == 'date'){
                if($value == '')
                    $value = '0000-00-00';
                $keyname1 = " and date = '$value'";
            }
            $keyname = $key ;
            if($key == 'type')
                $insurancetype = $value;
            
            $setquery2      .= $keyname.",";
            $setvalues2     .= "'".addslashes($value) ."',";

            $updatequery    .= $keyname." ='".addslashes($value)."',";
        }
        if($insertArray['id'] == 0){
            $setquery = rtrim($setquery2,',');
            $setvalues = rtrim($setvalues2,',');
            $query = "INSERT INTO insurance_data(pid,$setquery) VALUES($pid,$setvalues) ";
            $checkstmt = 'insert';
        }else{
            if($date == '')
                $date = '0000-00-00';
            $get_check_insu = "SELECT date FROM insurance_data WHERE pid = $pid AND type = '$type' AND date = '$date' AND id = $id ";
            $db->query( "SET NAMES utf8");
            $stmt_get_insu = $db->prepare($get_check_insu);
            $stmt_get_insu->execute();
            $get_insu = $stmt_get_insu->fetchAll(PDO::FETCH_OBJ);
            if(!empty($get_insu)){
                $update =  rtrim($updatequery,','). "  where id = $id.$keyname1";
                $query = "UPDATE insurance_data SET ".$update;
                $checkstmt = 'update';
            }else{
                $setquery = rtrim($setquery2,',');
                $setvalues = rtrim($setvalues2,',');
                $query = "INSERT INTO insurance_data(pid,$setquery) VALUES($pid,$setvalues) ";
                $checkstmt = 'insert';
            }
        }
        $db->query( "SET NAMES utf8");
        $stmt_layout = $db->prepare($query);

        if($stmt_layout->execute()){  
            if($insertArray['id'] == 0){
                for($i=0; $i< count($insurancetypes); $i++){
                    if($insurancetypes[$i] != $insurancetype){
                        $otherquery = "INSERT INTO insurance_data(pid,type,accept_assignment,date) VALUES($pid,'$insurancetypes[$i]','YES','0000-00-00') ";
                        $stmt_get_type = $db->prepare($otherquery);
                        if($stmt_get_type->execute())
                            insertMobileLog($checkstmt,$username,$otherquery,$pid,'',"Insert $insurancetypes[$i] Insurance Data",1);
                        else
                            insertMobileLog($checkstmt,$username,$otherquery,$pid,'',"Insert $insurancetypes[$i] Insurance Data - Failed",0);
                    }
                }
            }
            $insertcheck =  '[{"id":"1"}]';
            echo GibberishAES::enc($insertcheck, $apikey);
            insertMobileLog($checkstmt,$username,$query,$pid,'',"Edit $insurancetype Insurance Data",1);
            
	}else{
            $insertcheck = '[{"id":"0"}]';
            echo GibberishAES::enc($insertcheck, $apikey);
            insertMobileLog($checkstmt,$username,$query,$pid,'',"Edit $insurancetype Insurance Data - Failed",0);
	}
    }
    catch(PDOException $e)
    {

        $patientres =  '{"error":{"text":'. $e->getMessage() .'}}';
        echo $patientresult = GibberishAES::enc($patientres, $apikey);
        insertMobileLog('insert',$username,$patientres,$pid,'','Edit Insurance Data - Query Failed',0);
    }
}
function getCertification_Recertification($encounterid,$formid,$uid){
    try 
    {
        $db = getConnection();
        $apikey = 'rotcoderaclla';
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
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
                                END as isRequired,seq,max_length,description FROM layout_options WHERE form_id = 'LBF2' AND group_name LIKE '%Certification_Recertification' order by group_name, seq";
            $db->query( "SET NAMES utf8");
            $stmt_layout2 = $db->prepare($get_layout2) ;
            $stmt_layout2->execute();                       
            $layout_fields2 = $stmt_layout2->fetchAll(PDO::FETCH_OBJ);
            if(!empty($layout_fields2)){
                $patientsreminder[substr($layout_fields2[0]->group_name,1)]['form_id'] = $formid;
                for($i=0; $i< count($layout_fields2); $i++){
                    $sql = "select field_value from lbf_data where field_id  = '". $layout_fields2[$i]->field_id."' and form_id= '$formid'";   
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
        
        $dataarray = $patientsreminder;
       
//        echo "<pre>"; print_r($dataarray); echo "</pre>";
        $newdemo=encode_demo($dataarray);  
        $newdemo2['Certification_Recertification'] = check_data_available($newdemo);
        if($newdemo2)
        {
            //$result = utf8_encode_recursive($newdemo2);
            $patientres =  json_encode($newdemo2); 
            echo $patientresult = GibberishAES::enc($patientres, $apikey);
        }
        else
        {
           $patientres =  '[{"id":"0"}]';
           echo $patientresult = GibberishAES::enc($patientres, $apikey);
        }
    } 
    catch(PDOException $e) 
    {

        $patientres =  '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo $patientresult = GibberishAES::enc($patientres, $apikey);
    }
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
function checkeligibility($pid,$month){
    $sql = "SELECT p.pid 
        FROM openemr_postcalendar_events AS e 
        LEFT JOIN users AS d on (e.pc_aid is not null and e.pc_aid = d.id) 
        LEFT JOIN facility AS f on (f.id = e.pc_facility) 
        LEFT JOIN patient_data AS p ON p.pid = e.pc_pid 
        LEFT JOIN insurance_data AS i ON (
                                    i.id =( 
                                          SELECT id FROM insurance_data AS i 
                                          WHERE pid = p.pid AND type = 'primary' ORDER BY date DESC LIMIT 1 
                                        ) 
                                    ) 
        LEFT JOIN insurance_companies as c ON (c.id = i.provider) 
        WHERE e.pc_pid IS NOT NULL AND e.pc_eventDate >= ? AND e.pc_eventDate <= ? AND (i.policy_number is not null and i.policy_number != '') AND p.pid = $pid 
            ORDER BY p.lname,p.fname,p.mname ASC";
//    $printData = allCare_mbl_print_elig($res2,$X12info,$segTer,$compEleSep);
//    $printData = allCare_print_elig('pc_eventDate,pc_facility,lname,fname,mname,dob,ss,sex,pid,pubpid,policy_number,payer_id,subscriber_relationship,subscriber_lname,subscriber_fname,subscriber_mname,subscriber_dob,subscriber_ss,subscriber_sex,date,provider_lname,provider_fname,provider_npi,provider_pin,federal_ein,facility_npi,facility_name,payer_name 20160324,3,0000,000,,20160302,,Male,6021,6021,978669869,5257,jhfgh,gkg,kggk,jkjgk,11/25/2006,989898698,Female,20151125,,,,,46-4596181,1124441423,Texas Physician House Calls (H),American Republic Insurance Company 20160325,3,0000,000,,20160302,,Male,6021,6021,978669869,5257,jhfgh,gkg,kggk,jkjgk,11/25/2006,989898698,Female,20151125,Ketha,Sumana,1962447805,,46-4596181,1124441423,Texas Physician House Calls (H),American Republic Insurance Company 20160325,3,0000,000,,20160302,,Male,6021,6021,978669869,5257,jhfgh,gkg,kggk,jkjgk,11/25/2006,989898698,Female,20151125,Ketha,Sumana,1962447805,,46-4596181,1124441423,Texas Physician House Calls (H),American Republic Insurance Company 20160325,3,0000,000,,20160302,,Male,6021,6021,978669869,5257,jhfgh,gkg,kggk,jkjgk,11/25/2006,989898698,Female,20151125,Ketha,Sumana,1962447805,,46-4596181,1124441423,Texas Physician House Calls (H),American Republic Insurance Company ','5|46-4596718||ZIRMED|005010X222A1|standard','~','^');
}
function allCare_mbl_print_elig($res2,$X12info,$segTer,$compEleSep){
		
	$i=1;

	$PATEDI	   = "";

	// For Header Segment 

	$nHlCounter = 1;
	$rowCount	= 0;
	$trcNo		= 1234501;
	$refiden	= 5432101;
	
	while ($row = sqlFetchArray($res)) 
	{
		
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
		

		if($rowCount == sqlNumRows($res))
		{
			$segmentcount = $segmentcount + 1;
			$PATEDI	  .= create_SE($row,$segmentcount,$X12info,$segTer,$compEleSep);
			$PATEDI	  .= create_GE($row,$X12info,$segTer,$compEleSep);
			$PATEDI	  .= create_IEA($row,$X12info,$segTer,$compEleSep);
		}
	}

	return $PATEDI;
}
?>