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

//require '../interface/globals.php';
//require '../library/globals.inc.php';
//require_once '../library/clinical_rules.php';

// initialize app 
$app = new Slim();
            
//method to get prescriptions
$app->get('/getprescriptions/:patientId','getPrescriptions');
//method to edit patient who details
$app->post('/editwhodetails','editWhoDetails');
//method to store mobile app settings
$app->post('/storemobappsettings','storeMobAppSettings');
$app->post('/test','test');
//method to re-store mobile app settings
$app->post('/restoremobappsettings','restoreMobAppSettings');

/* ============ Hema methods calls start here ================== */
// method to get list of all patients from openEMR based for public user
$app->get('/publicpatient/:fname/:lname/:dob/:ss', 'getPublicPatientDetails');

// method to get list of all patients from openEMR
$app->get('/allpatientsdetails/:all', 'getAllPatientsDetails');
// method to get list of all pharmaciesissues
$app->get('/allpharmacies/:all', 'getAllPharmacies'); 
// To retrieve all addressbook types
$app->get('/alladdressbooktypes/:all', 'getAllAddressbookTypes');
// to retrieve all providers
$app->get('/allproviders/:all', 'getAllProviders');
// to retrieve all insurance companies
$app->get('/allinsurancecompanies/:all', 'getAllInsuranceCompanies');
// to retrieve all categories of openEmr
$app->get('/allcategories/:all', 'getAllCategories');
// to retrieve all face to face encounter of openEmr of single patient
$app->get('/allfacetofaceencounters/:pid', 'getAllFacetoFaceEncounters');
// to retrieve all hha of openEmr
$app->get('/allhha/:all', 'getAllHHA');
// to retrieve all organizations of Addressbook in openEmr
$app->get('/alladdressbookorg/:name', 'getAllAbookOrg');
// to retrieve Single Patient Details
$app->get('/patientdata/:pid', 'getPatientData');
// to retrieve Single Pharmacy Details
$app->get('/pharmacydata/:pharmacyid', 'getPharmacyData');
// to retrieve Single Addressbook Type Details
$app->get('/abooktypedata/:abooktypeid', 'getAddressbookTypeData');
// to retrieve Single Provider Details
$app->get('/providerdata/:providerid', 'getProviderData');
// to retrieve Single Insurance Company Details
$app->get('/insurancecompanydata/:icompanyid', 'getInsuranceCompanyData');
// to retrieve Single Facility Details
$app->get('/facilitydata/:ifacility', 'getFacilityData'); 
// to retrieve Patient Insurance Company Details
$app->get('/patientinsurancedata/:pid/:pidata', 'getPatientInsuranceData');
// to retrieve State Name Details in OpenEmr
$app->get('/getStateName/:sname', 'getStateName');
//method to update appointment status as rescheduled/cancel/ no show
//$app->put('/changeappstatus','changeAppointmentStatus');
//$app->put('/cancelappstatus','cancelAppointmentStatus');
// to retrieve Patient Agency List
$app->get('/patientagencylist/:pid', 'getDynamicPatientAgencies');
// to insert remainder
$app->post('/savereminder','saveReminder');
// to get patient insurance details list
$app->get('/getpatientinsurancelist/:pid','getPatientInsuranceDataList');
// to get incompleteencountercount
$app->get('/incompleteencountercount/:providerid','getIncompleteEncounterCount');
// to get details of incomplete encounter details
$app->get('/incompleteencounterdetails/:pid/:uid','getIncompleteEncounterList');
$app->get('/incompleteencounterdetails1/:pid','getIncompleteEncounterList1');
// to update history data of patient
$app->post('/updatehistorydata','updatePatientHistory');
// to add prescription
$app->post('/addprescription','addPrescription');
// to get incomplete patient encounter list
$app->get('/incompletepatientencounterlist/:providerid','getPatientIncompleteEncounterCount');
// to get visit category and dos 
$app->get('/dosvisitlist/:pid/:eid','getDosVisitList'); 
// to get dos form data
$app->get('/getdosformdata/:eid','getDosFormData'); 
$app->get('/getdosformdata1/:eid','getDosFormData1'); 
// to get app comments and camos data
//$app->get('/getdictationformdata/:eid/:catname','getDictationFormData'); 
//to get hpi form data
$app->get('/gethpiformdata/:eid/:formname','getHPIFormData');
// to insert speech file to google drive
$app->get('/insertfiletogdrive','insertSpeechDicatation');
// to get layout forms data
$app->get('/getdictationformdata/:eid/:formname','getDictationFormData');
// to save layoutform data
$app->post('/savelayoutformsdata','saveLayoutFormsData');
//$app->post('/savelayoutformsdata1','saveLayoutFormsData1');
// to get all patients from openemr for mobile
$app->get('/getallpatients','getTotalPatients');
// to get all form list and data for paticular encounter
$app->get('/getdosformdatadetails/:eid1/:eid2/:provider', 'getDosFormDataDetails');
$app->get('/getdosformdatatext/:eid/:formname', 'getDosFormDataDetailsTest');
// method to get vitals form data
$app->post('/savevitalsform','saveVitalsForm');
// to get hyperlinks form data
$app->get('/gethyperlinkforms/:eid', 'getHyperlinkForms'); 
// to save hyperlink submitted data
$app->get('/savehyperlinkform', 'saveHyperlinkForm');
// to get Review of system api
$app->get('/getrosformdata/:formid', 'getROSFormData');
// to save Review of System api
$app->post('/saverosformdata', 'saveROSFormData');
// to get physical Form
$app->get('/getphysicalexamformdata/:formid', 'getPhysicalForm');
// to save Physical exam
$app->post('/savephysicalexamformdata', 'savePhysicalExamFormData');
// to get face to face 
$app->get('/getfacetoface/:encounterid/:formid/:uid', 'getFacetoFace');
// to filter patients by functionality of deceased date and status
$app->get('/getpatientsbyfilter/:deceased/:active_stat', 'getPatientsByFilter');
// to  get list of appointment status
$app->get('/getapptstatus', 'getapptstatus');
// to create new encounter when arrived status
$app->post('/newencounter', 'createNewEnounter');
// to create diagnosis dropdown in physical form 
$app->post('/adddiagnosis','addDiagnosis');
// to get billing facility
$app->get('/getbillingfacilitylist', 'getBillingFacilityList');
// to get dynamic demographics
$app->get('/getlayoutdemographics/:pid', 'getLayoutDemographicsDynamic');
// to get layout history
$app->get('/getlayouthistory/:pid', 'getLayoutHistory');
// to get  time span
$app->get('/gettimespan', 'getTimeSpan');
// to esign on finalize
$app->post('/finalizeeid', 'finalizeEid');
// to put enddate for medical problem based on checkbox deselection
$app->post('/putendate', 'putMedicalProblemEndDate');
//// to upload file 
//$app->post('/voicefileupload', 'MobileFileUpload');
//// to get files list 
//$app->get('/getvoicefileslist/:pid/:eid/:form_name','getDictationFiles');
//// to rename dictation file
//$app->post('/renamefile','renameDictationFile');
//// to delete dictation file
//$app->post('/deletefile','deleteDictationFile');
// to get billing access of user
$app->get('/getbillingaccess/:uid', 'getBillingAccess');
// to get mobile query dropdown
$app->get('/mobilepatientfilters','MobilePatientFilters');
// to get filters data for allpatients/ mypatients screen in mobile
$app->get('/filtersdata/:pid', 'filitersData');
// to get audit form
$app->get('/auditform/:formid/:pid', 'getAuditForm');
// to get CPO form
$app->get('/getcpo/:formid/:pid/:eid', 'getCPO');
// to get CCM form
$app->get('/getccm/:formid/:pid/:eid', 'getCCM');
// to save CPO form
$app->post('/savecpo', 'saveCPO');
// to save CCM form
$app->post('/saveccm', 'saveCCM');
/* ============ Hema methods calls end here ================== */

/* ============ bhavya methods calls start here ================== */

//method to get patientReminders
$app->get('/patientreminders/:pid', 'getPatientReminders');
//method to get ClinicalReminders
$app->get('/clinicalreminders/:pid', 'getClinicalRem');
//method to get cpt4 codes 
$app->get('/cptcodes/:encounter', 'getCPTCodes');
//To get icd9 codes for issue
$app->get('/icdcodes/:encounter', 'getICDCodes');
//To get cpt4,cvx codes,description
$app->get('/codes/:codetype', 'getCodes');
//To get feesheet codes
$app->get('/feesheet/:encounterid', 'getFeesheetCodes');
//To get issues based on encounter
$app->get('/issues/:encounter/:type', 'getIssuesByEncounter');
//To save issues based on encounter
$app->post('/saveissues', 'saveIssuesByEncounter');
//To get immunization
$app->get('/getimmunization/:pid', 'getImmunization');
//To save immunization
$app->post('/saveimmunization', 'saveImmunization');
//To save feesheet codes
$app->post('/savefeesheet', 'saveFeeSheet');

//To save mobile settings
$app->post('/storemobilesettings','storeMobileSettings');
//$app->post('/settingsallcare','storeAllCareSettings');

//To restore mobile settings
$app->post('/restoremobilesettings','restoreMobileSettings');
/* ============ bhavya methods calls start here ================== */
/* ------------ Subhan methods calls start here ------------------ */
$app->post('/fileupload', 'uploadFile');
/* ------------ Subhan methods calls end here ------------------ */
// method for provider login
$app->post('/login', 'loginUser');
// method to get list of todays appointments for given provider
$app->get('/patients/:loginUserId', 'getPatients');
// method to get list of all patients
$app->get('/allpatients/:value', 'getAllPatients');
// method to get list of patients belonging to given provider
$app->get('/filterpatients/:loginProvderId/:fromCount', 'getPatientsByProvider');
// method to get patient demographics
$app->get('/demographics/:patientId', 'getDemographics');
// method to get list of appointments for a given date
$app->get('/patientsbyday/:loginProvderId/:day', 'getPatientsByday');

// method to get list of complete/incomplete encounters for given provider
$app->get('/myencounters/:loginProvderId/:patientId/:mode', 'getEncounterList');
// method to get list of patients belonging to given provider for messages
$app->get('/mypatients/:loginProvderId/:fromCount','getMyPatients');

// method to create new message
$app->post('/createmessage','createMessage');

// method to get the previous dictation
$app->get('/getdictation/:loginProvderId/:pid/:encounter','getDictation');

// method to store speech dictation
$app->get('/speechAudioDictation/:loginProvderId/:pid/:encounter/:dictation/:isNuance','createAudioDictation');

$app->post('/speechDictation','createDictation');

// method to get ICD codes
$app->get('/icd9codes','getICD9Codes');

// method to check if encounter exists
$app->get('/checkencounterexists/:pid/:loginProvderId/:encDate','checkEncounterExists');

// method to create new encounter
//$app->get('/newencounter/:pid/:loginProvderId/:encDate','createNewEncounter');
$app->post('/newencounter','createNewEncounter');

// method to check completed forms based on encounter
$app->get('/completedformsbyencounter/:pid/:loginProvderId/:encounter','getCompletedFormsByEncounter');

// method to save face to face form                       
$app->post('/createfacetoface', 'createFacetoFace');

// method to save lab request form
$app->post('/labrequest', 'createLabRequest');

// method to get lab request config data
$app->get('/labrequestconfig','getLabRequestConfig');

// method to save Vital form
$app->post('/vital', 'createVital');

// method to save SOAP form
$app->post('/soap', 'createSoap');

// Methods required for Fee Sheet/Billing
$app->get('/users','getUsers');
$app->get('/encounterdates/:pid/:encounter','getEncounterDates');// For date drop down in Review button pop-up
$app->get('/reviewcontent/:pid/:loginProvderId/:encounter','getReviewContents');


$app->get('/codesforsearch','getCodesForSearch');
$app->get('/searchdiagnosis/:codeSelected/:txtSearch','getSearchResult');

$app->get('/pricelevel','getPriceLevel');

$app->get('/patientCategory_Search/:pid/:loginProvderId/:encounter/:code_type/:code','new_established_Patient_searchChangeFeeSheet');

$app->post('/reviewadd', function () use ($app) {
     
    $reviewJSONArray=$app->request()->post('jarrProcIssue_new');
    $reviewArray = json_decode($reviewJSONArray, TRUE);
   
   reviewAdd($reviewArray);
});


$app->get('/feesheet/:encounter','getFeeData');

$app->get('/deletefeesheetrow/:fid','deleteFeeSheetData');

$app->post('/updatebill', 'updateBilling');
$app->post('/savebill', 'saveBilling');

/*     Methods for Fee Sheet/Billing end            */

// method to get patient information
$app->get('/patientinfo/:pid','getPatientInformation');

// method to create new patient
$app->post('/createnewpatient', 'createNewPatient');

// method to get all facilities
$app->get('/facilities','getAllFacilities');

// method to get patients by facility
//$app->get('/patientsbyfacility/:fid/:loginProvderId','getPatientsByFacility');
$app->get('/patientsbyfacility/:fid','getPatientsByFacility');

// method to get visit categories
$app->get('/visitcategories','getVisitCategories');

$app->get('/formreport/:pid/:form_name/:id','form_report');

// method to get SOAP form data
$app->get('/getsoapform/:pid/:encounterId/:Id','getSoapForm');

// method to get face to face form data
$app->get('/getfacetofaceform/:pid/:encounterId/:Id','getFacetoFaceForm');

// method to get face to face configuration notes
$app->get('/facetofacenotes','getFacetoFaceNotes');

// method to get data from the table based on passed note_id
$app->get('/facetofacenoteid/:noteid', 'getFacetoFaceNoteData');

// method to get lab requision form data
$app->get('/getlabrequisitionform/:pid/:encounterId/:Id','getLabRequisitionForm'); 

// method to get vitals form data
$app->get('/getvitalsform/:encounterId/:Id','getVitalsForm');

// method to create new appointment
$app->post('/createappointment', 'createAppointment');

// method to get available time slots
$app->get('/availabletimeslots/:loginProvderId/:startingDate','getAvailableTimeSlots');

// method to search patient by name
$app->get('/searchpatientbyname/:loginProvderId/:pname','searchPatientByName');

// method to get appointment status
$app->get('/appointmentstatuses','getAppointmentStatuses');

// method to get appointment details
$app->get('/getappointmentdetails/:apptid','getAppointmentDetails');

// method to get appointment dates
$app->get('/getappointmentdates/:loginProvderId','getAppointmentDates');

//method to get patient history details
$app->get('/gethistorydetails/:pid/:group_name/:eid','getHistoryDetails');

//method to get patient issues
$app->get('/getissues/:pid','getIssues');

//method to get occurrence list
$app->get('/getoccurrencelist','getOccurrenceList');

//method to get Outcome list
$app->get('/getoutcomelist','getOutcomeList');

//method to add/edit issues
$app->post('/updateissues','updateIssues');

//method to get medical problem list
$app->get('/getmedprobissuelist','getMedProbIssueList');

//method to get allergy issue list
$app->get('/getallergyissuelist','getAllergyIssueList');

//method to get medication issue list
$app->get('/getmedissuelist','getMedIssueList');

//method to get surgery issue list
$app->get('/getsurgeryissuelist','getSurgeryIssueList');

$app->get('/getmessages/:loginProvderId','getMessages');

$app->get('/getreminders/:loginProvderId/:days_to_show/:alerts_to_show','getReminders');

$app->get('/getreminderscount/:loginProvderId/:days_to_show/:alerts_to_show','getRemindersCount');

//method to set reminder as completed
$app->get('/setreminderasprocessed/:reminderId/:loginProvderId','setReminderAsProcessed'); 

$app->get('/getmsgtousers','getMsgToUsers');

$app->get('/getactivemessages/:loginProvderId/:isActive','getActiveMessages');

$app->get('/getactivemessagescount/:loginProvderId/:isActive','getactivemessagesCount');

$app->get('/getmsgstatuses','getMsgStatuses');

$app->get('/getmsgnotetypes','getMsgNoteTypes');

// method to get demographics billing
$app->get('/getdemographicsbilling/:pid/:with_insurance','getDemographicsBilling');

// method to get Clinical reminders by patient
$app->get('/getclinicalreminders/:pid','getClinicalReminders');

//method to get provider list for patient-choices
$app->get('/getchoicesproviderlist','getChoicesProviderList');

//method to get list of pharmacies
$app->get('/getpharmacylist','getPharmacyList');

//method to get list of countries
$app->get('/getcountrystatelist','getCountryStateList');

//method to get list of states
$app->get('/getstatelist','getStateList');

//method to get list of languages
$app->get('/getlanguagelist','getLanguageList');

//method to get list of ethnicity
$app->get('/getethnicitylist','getEthnicityList');

//method to get list of race
$app->get('/getracelist','getRaceList');

//method to get list of ref source
$app->get('/getrefsourcelist','getRefSourceList');

//method to edit patient contact details
$app->post('/editcontactdetails','editContactDetails');

//method to edit patient choices details
$app->post('/editchoicesdetails','editChoicesDetails');

//method to edit patient employer details
$app->post('/editemployerdetails','editEmployerDetails');

//method to edit patient stats details
$app->post('/editstatsdetails','editStatsDetails');

//method to edit patient misc details
$app->post('/editmiscdetails','editMiscDetails');

//method to update appointment status
$app->post('/editapptstatus','editApptStatus');

//method to get titles list
$app->get('/gettitles','getTitles');
$app->get('/getmasterdata','getMasterData');

//method to get sex
$app->get('/getgender', 'getSex');

//method to get marital status list
$app->get('/getmaritalstatus','getMaritalStatus');

//method to add/edit patient facility
$app->post('/editpatientfacility','editPatientFacility');

//method to update appointment status as rescheduled/cancel/ no show
$app->post('/changeappstatus','changeAppointmentStatus');
$app->post('/cancelappstatus','cancelAppointmentStatus');

$app->run();

// connection to openemr database
function getConnection() 
{

	$dbhost="mysql51-140.wc2.dfw1.stabletransit.com";
	$dbuser="551948_qa2allcr";
	$dbpass="Rise@123";
	$dbname="551948_qa2allcr";		
  
	$dbh = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);	
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	return $dbh;
}

function getConnection1() 
{
    $dbhost="mysql51-009.wc2.dfw1.stabletransit.com";
    $dbuser="551948_devcopbox";
    $dbpass="Rise@123";
    $dbname="551948_devcopbx";  

    $dbh = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass); 
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $dbh;
}

// method to get the face to face configuration notes 
function getFacetoFaceNotes()
{
    $key = 'rotcoderaclla';
    // The default key size is 256 bits.
    $old_key_size = GibberishAES::size();
    GibberishAES::size(256);
    $sql = "SELECT option_id,title FROM list_options WHERE list_id='FaceToFace_Configuration_Notes'";
    try 
    {
        $db = getConnection();
        $stmt = $db->prepare($sql) ;        	
        $stmt->execute();
        $notes = $stmt->fetchAll(PDO::FETCH_OBJ);

        if($notes)
        {
            //returns facetofaceform default data
            $notesres = json_encode($notes); 
            echo $notesresult = GibberishAES::enc($notesres, $key);
        }
        else
        {    
            //echo 'No data available';
            $notesres = '[{"id":"0"}]';
            echo $patientresult = GibberishAES::enc($error, $key);
        }
    } 
    catch(PDOException $e) 
    {
        $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo $patientresult = GibberishAES::enc($error, $key);
    }
}

// method to get list of facetoface form default data for a given note_id
function getFacetoFaceNoteData($noteid)
{
    $key = 'rotcoderaclla';
    // The default key size is 256 bits.
    $old_key_size = GibberishAES::size();
    GibberishAES::size(256);
	$sql = "SELECT * FROM tbl_form_facetoface_configuration WHERE note_id=:noteid";        
    
        try 
        {
            $db = getConnection();
            $stmt = $db->prepare($sql) ;
            $stmt->bindParam("noteid", $noteid); 		
            $stmt->execute();
            $note_id = $stmt->fetchAll(PDO::FETCH_OBJ);
            
            
            if($note_id)
            {
                //returns facetofaceform default data
                $noteIdres = json_encode($note_id); 
                echo $notesresult = GibberishAES::enc($noteIdres, $key);
            }
            else
            {    
                //echo 'No data available';
                $noteIdres = '[{"id":"0"}]';
                echo $notesresult = GibberishAES::enc($noteIdres, $key);
            }
        } 
        catch(PDOException $e) 
        {
            
            $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
            echo $notesresult = GibberishAES::enc($error, $key);
        }
}



// method to get lab requisition form config data
function getLabRequestConfig()
{
    $key = 'rotcoderaclla';
    // The default key size is 256 bits.
    $old_key_size = GibberishAES::size();
    GibberishAES::size(256);
    $sql = "SELECT * FROM tbl_form_lab_requisition_configuration";        
    
        try 
        {
            $db = getConnection();
            $stmt = $db->prepare($sql);           
            $stmt->execute();
            $config_data = $stmt->fetchAll(PDO::FETCH_OBJ);            
            
            if($config_data)
            {
                //returns facetofaceform default data
                $congigDatares = json_encode($config_data); 
                echo $congigDataresult = GibberishAES::enc($congigDatares, $key);
            }
            else
            {    
                //echo 'No data available';
                $congigDatares = '[{"id":"0"}]';
                echo $congigDataresult = GibberishAES::enc($congigDatares, $key);
            }
        } 
        catch(PDOException $e) 
        {
            
            $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
            echo $congigDataresult = GibberishAES::enc($error, $key);
            
        }
}

// method to get username
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
function loginUser()
{   
    try 
    {
        $db = getConnection();
        
        $request = Slim::getInstance()->request();
        $logObject = json_decode($request->getBody(),TRUE);
//        $logObject = json_decode('{"username":"U2FsdGVkX1+odwWS73Pg24IRpARVA5kdC7599j9aVDY=","password":"U2FsdGVkX1/VdoDQqquPuPBpvic2Zd6qVc2zswHQ5Ek="}',TRUE);
        $key = 'rotcoderaclla';
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        $username = GibberishAES::dec($logObject['username'], $key);
        $password = GibberishAES::dec($logObject['password'], $key);


        $sql="SELECT us.id,us.username,us.password,us.salt,u.fname,u.lname, u.authorized
        FROM users_secure us 
        INNER JOIN users u ON us.id=u.id 
        WHERE us.username=:username";
        
        $stmt = $db->prepare($sql) ;
        $stmt->bindParam("username", $username);
        $stmt->execute();
        $user = $stmt->fetchObject();                          
        $practicename = "QA2";
        if($user)
        {
            //return a hashed string
            $phash=crypt($password,$user->salt);

            if($phash==$user->password)
            {
                // returns id of the user/provider if user/provider is valid
				/*echo '[{"id":'.json_encode($user->id).',"fname":'.json_encode($user->fname).',"lname":'.json_encode($user->lname).',"authorized":'.json_encode($user->authorized).'}]';*/
				/*$qry="select  distinct u.id,gag.name as role from users u
				inner join gacl_aro ga on ga.value= u.username
				inner join gacl_groups_aro_map ggam on ggam.aro_id=ga.id
				inner join gacl_aro_groups gag on gag.id=ggam.group_id
				inner join gacl_aro_groups_map gagm on gagm.group_id=ggam.group_id
				inner join gacl_aco_map gam on gam.acl_id=gagm.acl_id
				where u.id=:uid";*/
				$qry="select  distinct u.id,gag.name as acl_role_name,li.title as user_role, group_concat(concat(gam.section_value,'-',gam.value,'-',gacl.return_value)) as acl_role_permissions, 
group_concat(concat(gacl.return_value,'-',gacl.note)) as rights

 from users u
				inner join gacl_aro ga on ga.value= u.username
				inner join gacl_groups_aro_map ggam on ggam.aro_id=ga.id
				inner join gacl_aro_groups gag on gag.id=ggam.group_id
				inner join gacl_aro_groups_map gagm on gagm.group_id=ggam.group_id
				inner join gacl_aco_map gam on gam.acl_id=gagm.acl_id
				inner join gacl_acl gacl on gacl.id=gagm.acl_id
                                left join list_options li on li.list_id =  'newcrop_erx_role' AND li.option_id=u.newcrop_user_role
				where u.id=:uid";
				$stmt = $db->prepare($qry) ;
				$stmt->bindParam("uid", $user->id);
				$stmt->execute();
				$acl = $stmt->fetchObject();
				if($acl)
				{
					$user_role=$acl->user_role;
                                        $acl_role_permissions=$acl->acl_role_permissions;
					$acl_role=$acl->acl_role_name;
					$rights=$acl->rights;
				}
				else
					$isacct=0;
                // returns id of the user/provider if user/provider is valid
				$string = '[{"id":'.json_encode($user->id).',"fname":'.json_encode($user->fname).',"lname":'.json_encode($user->lname).',"authorized":'.json_encode($user->authorized).',"user_role":'.json_encode($user_role).',"acl_role_name":'.json_encode($acl_role).',"acl_role_permissions":'.json_encode($acl_role_permissions).',"rights":'.json_encode($rights).',"practice_name":'.json_encode($practicename).'}]';
                                echo $stringenc = GibberishAES::enc($string, $key);
//                                echo "<pre>"; print_r($string); echo "</pre>";
            } 
            else 
            {
                // if username or password is wrong return id=0
                $string = '[{"id":"0"}]';
                echo $stringenc = GibberishAES::enc($string, $key);
            }                               
        }
        else
        {
            // if user does not exist return id=0
            $string = '[{"id":"0"}]';
            echo $stringenc = GibberishAES::enc($string, $key);
        }
    } 
    catch(PDOException $e) 
    {
        $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo $stringenc = GibberishAES::enc($error, $key);
    }
}
function loginUser2()
{   
    try 
    {
        $db = getConnection();
        
        $request = Slim::getInstance()->request();
        $logObject = json_decode($request->getBody(),TRUE);
        
        $key = 'rotcoderaclla';
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        $username = GibberishAES::dec($logObject['username'], $key);
        $password = GibberishAES::dec($logObject['password'], $key);


        $sql="SELECT us.id,us.username,us.password,us.salt,u.fname,u.lname, u.authorized
        FROM users_secure us 
        INNER JOIN users u ON us.id=u.id 
        WHERE us.username=:username";
        
        $stmt = $db->prepare($sql) ;
        $stmt->bindParam("username", $username);
        $stmt->execute();
        $user = $stmt->fetchObject();                          

        if($user)
        {
            //return a hashed string
            $phash=crypt($password,$user->salt);

            if($phash==$user->password)
            {
                // returns id of the user/provider if user/provider is valid
				/*echo '[{"id":'.json_encode($user->id).',"fname":'.json_encode($user->fname).',"lname":'.json_encode($user->lname).',"authorized":'.json_encode($user->authorized).'}]';*/
				/*$qry="select  distinct u.id,gag.name as role from users u
				inner join gacl_aro ga on ga.value= u.username
				inner join gacl_groups_aro_map ggam on ggam.aro_id=ga.id
				inner join gacl_aro_groups gag on gag.id=ggam.group_id
				inner join gacl_aro_groups_map gagm on gagm.group_id=ggam.group_id
				inner join gacl_aco_map gam on gam.acl_id=gagm.acl_id
				where u.id=:uid";*/
				$qry="select  distinct u.id,gag.name as acl_role_name,li.title as user_role, group_concat(concat(gam.section_value,'-',gam.value,'-',gacl.return_value)) as acl_role_permissions, 
group_concat(concat(gacl.return_value,'-',gacl.note)) as rights

 from users u
				inner join gacl_aro ga on ga.value= u.username
				inner join gacl_groups_aro_map ggam on ggam.aro_id=ga.id
				inner join gacl_aro_groups gag on gag.id=ggam.group_id
				inner join gacl_aro_groups_map gagm on gagm.group_id=ggam.group_id
				inner join gacl_aco_map gam on gam.acl_id=gagm.acl_id
				inner join gacl_acl gacl on gacl.id=gagm.acl_id
                                left join list_options li on li.list_id =  'newcrop_erx_role' AND li.option_id=u.newcrop_user_role
				where u.id=:uid";
				$stmt = $db->prepare($qry) ;
				$stmt->bindParam("uid", $user->id);
				$stmt->execute();
				$acl = $stmt->fetchObject();
				if($acl)
				{
					$user_role=$acl->user_role;
                                        $acl_role_permissions=$acl->acl_role_permissions;
					$acl_role=$acl->acl_role_name;
					$rights=$acl->rights;
				}
				else
					$isacct=0;
                // returns id of the user/provider if user/provider is valid
				$string = '[{"id":'.json_encode($user->id).',"fname":'.json_encode($user->fname).',"lname":'.json_encode($user->lname).',"authorized":'.json_encode($user->authorized).',"user_role":'.json_encode($user_role).',"acl_role_name":'.json_encode($acl_role).',"acl_role_permissions":'.json_encode($acl_role_permissions).',"rights":'.json_encode($rights).'}]';
                                echo $stringenc = GibberishAES::enc($string, $key);
            } 
            else 
            {
                // if username or password is wrong return id=0
                $string = '[{"id":"0"}]';
                echo $stringenc = GibberishAES::enc($string, $key);
            }                               
        }
        else
        {
            // if user does not exist return id=0
            $string = '[{"id":"0"}]';
            echo $stringenc = GibberishAES::enc($string, $key);
        }
    } 
    catch(PDOException $e) 
    {
        $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo $stringenc = GibberishAES::enc($error, $key);
    }
}

// method to get list of todays appointments for given provider
// get list of appointments that are to be executed for current date for the logged in user/provider
function getPatients($loginUserId)
{
    	$key = 'rotcoderaclla';
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
	$sql = "SELECT pd.pid,pd.title,pd.fname,pd.lname
                FROM patient_data pd                        
                WHERE pd.providerID=:loginUserId AND pd.practice_status = 'YES' AND pd.deceased_stat != 'YES'";                    

        try 
        {
            $db = getConnection();
            $stmt = $db->prepare($sql) ;
            $stmt->bindParam("loginUserId", $loginUserId);            
            $stmt->execute();
            $patients = $stmt->fetchAll(PDO::FETCH_OBJ);
                        
            if($patients)
            {
                //returns patients list
                $patientsres = json_encode($patients); 
                echo $patientsresult = GibberishAES::enc($patientsres, $key);
            }
            else
            {    
                //echo 'No Patient available';
                $patientsres = '[{"id":"0"}]';
                echo $patientsresult = GibberishAES::enc($patientsres, $key);
            }
        } 
        catch(PDOException $e) 
        {
            
            $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
            echo $patientsresult = GibberishAES::enc($error, $key);
        }
}

// method to get list of appointments for a given date
function getPatientsByday($loginUserId,$day)
{
    $key = 'rotcoderaclla';
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        try 
        {
            $patients = '';
            $db = getConnection();
            
            $get_fuv = "SELECT visit_categories FROM tbl_allcare_facuservisit WHERE  `users`  REGEXP ('".":\"".$loginUserId."\"')";
            $fuv_stmt = $db->prepare($get_fuv) ;
            $fuv_stmt->execute();
            $set_fuv = $fuv_stmt->fetchAll(PDO::FETCH_OBJ); 
            for($i = 0; $i<count($set_fuv); $i++){
               $array[] =  unserialize( $set_fuv[$i]->visit_categories);
            }
            $dataArray = array();
            for($j = 0; $j<count($array); $j++){
                foreach($array[$j] as $arraykey){
                     $dataArray[] = $arraykey;
                }
            }
            $enc_val = '';
            $dataarray = array_unique($dataArray);
            foreach($dataarray as $arrayval){
                $enc_val .= $arrayval.",";
            }
            $enc_value = rtrim($enc_val,",");
            if(!empty($enc_value)){
        
        
                $sql = "SELECT ope.pc_eid as apptid,pd.pid,pd.title,pd.fname,pd.lname,if (pd.sex = 'Female' ,'F','M' ) as sex,
                    DATE_FORMAT(pd.DOB,'%m-%d-%Y') as DOB,DATE_FORMAT(FROM_DAYS(DATEDIFF(CURDATE(),pd.DOB)), '%Y')+0 AS age,pd.street_addr,pd.city,pd.state,pd.country_code,pd.postal_code,pd.phone_home,pd.phone_biz,pd.phone_contact,pd.phone_cell,pd.contact_relationship as emergency_contact, pd.phone_contact as emergency_phone, DATE_FORMAT(ope.pc_eventDate,'%m-%d-%Y') as event_date ,if(ope.pc_endDate='0000-00-00',DATE_FORMAT(ope.pc_eventDate,'%m-%d-%y'),DATE_FORMAT(ope.pc_endDate,'%m-%d-%Y'))  as end_date ,ope.pc_duration, TIME_FORMAT(ope.pc_startTime, '%h:%i %p') AS start_time,TIME_FORMAT(ope.pc_endTime, '%h:%i %p') AS end_time,ope.pc_facility,ope.pc_catid,pc.pc_catname,ope.pc_billing_location,lo.option_id as symbol,lo.title as apptstatus,ope.pc_hometext As comments,pd.deceased_stat,pd.practice_status
                    FROM patient_data pd INNER JOIN openemr_postcalendar_events ope ON pd.pid=ope.pc_pid
                            inner join openemr_postcalendar_categories pc on pc.pc_catid=ope.pc_catid 
                                                    inner join list_options lo on lo.option_id=ope.pc_apptstatus
                    WHERE ope.pc_aid=:loginUserId AND ope.pc_eventdate=:day and pd.practice_status = 'YES' AND pd.deceased_stat != 'YES' and ope.pc_catid IN ($enc_value) order by pd.lname, pd.fname";  



                $stmt = $db->prepare($sql) ;
                $stmt->bindParam("loginUserId", $loginUserId); 
                $stmt->bindParam("day", $day);   			
                //$stmt->bindParam("visitCategory", $visitCategory); 			
                $stmt->execute();
                $patients = $stmt->fetchAll(PDO::FETCH_OBJ);
            
            }
            if($patients)
            {
                //returns patients appointment list
                $patientres = json_encode($patients); 
                echo $patientresult = GibberishAES::enc($patientres, $key);
            }
            else
            {    
                //echo 'No Patient available';
                $patientres = '[{"id":"0"}]';
                echo $patientresult = GibberishAES::enc($patientres, $key);
            }
        } 
        catch(PDOException $e) 
        {
            
            $patientres = '{"error":{"text":'. $e->getMessage() .'}}'; 
            echo $patientresult = GibberishAES::enc($patientres, $key);
        }
}

//// method to get list of all patients with filters
function getAllPatients($value)
{
//        $toCount=$fromCount+20;
	$apikey = 'rotcoderaclla';
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);

        try 
        {
            $db = getConnection();
            
            $query = '';
            
            $get_sql = "select notes from list_options WHERE list_id = 'Mobile_Query_Filters'  AND option_id = '$value'";      
            $set_stmt = $db->prepare($get_sql) ;
            $set_stmt->execute();                       

            $get_value = $set_stmt->fetchAll(PDO::FETCH_OBJ);   
            
            if(!empty($get_value)){
                $query  = " WHERE ".$get_value[0]->notes;
            }
            $sql = "SELECT pid,title,fname,lname, DATE_FORMAT(DOB,'%m-%d-%Y') AS DOB,DATE_FORMAT(FROM_DAYS(DATEDIFF(CURDATE(),DOB)), '%Y')+0 AS age,if (sex = 'Female' ,'F','M' ) as sex,street,city,state,country_code,postal_code,phone_home,phone_biz,phone_contact,phone_cell,contact_relationship as emergency_contact, phone_contact as emergency_phone,email
                from patient_data $query order by lname, fname "; 
            
            $stmt = $db->prepare($sql) ;
            $stmt->execute();                       
             
            $patients = $stmt->fetchAll(PDO::FETCH_OBJ);  
            
            
//            echo "<pre>"; print_r($patients); echo "</pre>";
            
            $patientsdata['PatientData'] = $patients;

            if($patientsdata)
            {
                //returns patients appointment list
                $patientres = json_encode($patientsdata); 
                echo $patientresult = GibberishAES::enc($patientres, $apikey);
            }
            else
            {
                //echo 'No Patient available';
                $patientres = '[{"id":"0"}]';
                echo $patientresult = GibberishAES::enc($patientres, $apikey);
            }
        } 
        catch(PDOException $e) 
        {
            
            $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
            echo $patientresult = GibberishAES::enc($error, $apikey);
        }
}
function getAllPatients2($fromCount)
{
        $toCount=$fromCount+20;
	$apikey = 'rotcoderaclla';
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);

        try 
        {
            $db = getConnection();
            
            $query = '';
            
            $sql6 = "SELECT title FROM list_options WHERE option_id  = 'MobileScreenFilters' ";
            $stmt6 = $db->prepare($sql6) ;
            $stmt6->execute();  
            $screen_filters_names = $stmt6->fetchAll(PDO::FETCH_OBJ); 
            if(!empty($screen_filters_names)){
                $get_fields = explode(',',$screen_filters_names[0]->title);
                for($i=0; $i<count($get_fields); $i++){
                    $sql7 = "SELECT title FROM layout_options WHERE field_id  = '".$get_fields[$i]."' and form_id = 'DEM' ";
                    $stmt7 = $db->prepare($sql7) ;
                    $stmt7->execute();  
                    $screen_filters_fields = $stmt7->fetchAll(PDO::FETCH_OBJ); 
                    if(!empty($screen_filters_fields)){
                                
                        $query2 .=  $get_fields[$i] . " as ".str_replace("&","_",str_replace("/","_",str_replace(' ','_',$screen_filters_fields[0]->title))).",";
                    }
                }
            }
            $query = rtrim($query2, ',');
            
            $sql = "SELECT pid,title,fname,lname, DATE_FORMAT(DOB,'%m-%d-%Y') AS DOB,DATE_FORMAT(FROM_DAYS(DATEDIFF(CURDATE(),DOB)), '%Y')+0 AS age,if (sex = 'Female' ,'F','M' ) as sex,street,city,state,country_code,postal_code,phone_home,phone_biz,phone_contact,phone_cell,contact_relationship as emergency_contact, phone_contact as emergency_phone,email, $query
                from patient_data where  deceased_stat <> 'YES' order by lname, fname "; 
            
            $stmt = $db->prepare($sql) ;
            $stmt->bindParam("fromCount", $fromCount);            
            $stmt->execute();                       
             
            $patients = $stmt->fetchAll(PDO::FETCH_OBJ);  
            if(!empty($patients)){
                for($i=0; $i< count($patients); $i++){
                    foreach($patients[$i] as $key1 => $value1){
                        $key2 = '';
                        if($key1 == 'Active_With_Practice') {
                            unset($patients[$i]->Active_With_Practice);
                            $key2 = 'Practice_Status';
                        }else if($key1 == 'Care_Plan_Oversight_CPO') {
                            unset($patients[$i]->Care_Plan_Oversight_CPO);
                            $key2 = 'CPO';
                        }else if($key1 == 'Chronic_Care_Management') {
                            unset($patients[$i]->Chronic_Care_Management);
                            $key2 = 'CCM';
                        }else if($key1 == 'New_Estb') {
                            unset($patients[$i]->New_Estb);
                            $key2 = 'New/Estb';
                        }else if($key1 == 'H___P') {
                            unset($patients[$i]->H___P);
                            $key2 = 'H&P';
                        }else{
                            $key2 = $key1;
                        }
//                        if($patients[$i]->$key1)
//                            unset($patients[$i]->$key1);
                        $patients[$i]->$key2 = $value1;
                    }
                }
            }

            if(!empty($screen_filters_names)){
                $get_fields = explode(',',$screen_filters_names[0]->title);
                for($i=0; $i<count($get_fields); $i++){
                    $sql7 = "SELECT title FROM layout_options WHERE field_id  = '".$get_fields[$i]."' and form_id = 'DEM' ";
                    $stmt7 = $db->prepare($sql7) ;
                    $stmt7->execute();  
                    $screen_filters_fields = $stmt7->fetchAll(PDO::FETCH_OBJ); 
                    if(!empty($screen_filters_fields)){
                        $options= array();
                        $sql8 = "SELECT li.title,li.option_id FROM layout_options lo INNER JOIN  list_options li ON  li.list_id = lo.list_id WHERE lo.field_id ='$get_fields[$i]'";
                        $stmt8 = $db->prepare($sql8) ;
                        $stmt8->execute();  
                        $options = $stmt8->fetchAll(PDO::FETCH_OBJ); 
                        if(!empty($options)){ 
                            foreach($options as $key => $value){
//                                print_r($value->title);
//                                echo $key.$value."==<br>";
                                if($get_fields[$i] == 'deceased_stat'){
                                    if(($value->title == 'YES') !== false) {
                                        unset($options[$key]);
                                    }
                                }else if($get_fields[$i] == 'practice_status'){
                                    if(($value->title == 'NO') !== false) {
                                        unset($options[$key]);
                                    }
                                }
                            }
                            $name= '';
                            $name2 = str_replace("&","_",str_replace("/","_",str_replace(' ','_',$screen_filters_fields[0]->title)));
//                            foreach($options as $key => $value){print_r($key);
                                if($name2 == 'Active_With_Practice') 
                                    $name = 'Practice_Status';
                                else if($name2 == 'Care_Plan_Oversight_CPO') 
                                    $name = 'CPO';
                                else if($name2 == 'Chronic_Care_Management') 
                                    $name = 'CCM';
                                else if($name2 == 'New_Estb') 
                                    $name = 'New/Estb';
                                else if($name2 == 'H___P') 
                                    $name = 'H&P';
                                else
                                    $name = $name2;
//                        }
                        $fields[$name] = array_values($options);
                        }
                    }
                }
            }
            
            
//            echo "<pre>"; print_r($fields); echo "</pre>";
            
            $patientsdata['PatientData'] = $patients;
            $patientsdata['FilterData'] = $fields;
//            $patientsdata['FilterData']['Practice_Status'] = $practice;
//            $patientsdata['FilterData']['CPO'] = $cpo;
//            $patientsdata['FilterData']['CCM'] = $ccm;
//            echo "<pre>"; print_r($patientsdata); echo "</pre>";
            if($patientsdata)
            {
                //returns patients appointment list
                $patientres = json_encode($patientsdata); 
                echo $patientresult = GibberishAES::enc($patientres, $apikey);
            }
            else
            {
                //echo 'No Patient available';
                $patientres = '[{"id":"0"}]';
                echo $patientresult = GibberishAES::enc($patientres, $apikey);
            }
        } 
        catch(PDOException $e) 
        {
            
            $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
            echo $patientresult = GibberishAES::enc($error, $apikey);
        }
}


// method to get list of patients belonging to given provider
function getPatientsByProvider($loginProvderId,$value)
{
    $apikey = 'rotcoderaclla';
    // The default key size is 256 bits.
    $old_key_size = GibberishAES::size();
    GibberishAES::size(256);    
 
        try 
        {
            $db = getConnection();
            
            
            $query = '';
            
            $get_sql = "select notes from list_options WHERE list_id = 'Mobile_Query_Filters'  AND option_id = '$value'";      
            $set_stmt = $db->prepare($get_sql) ;
            $set_stmt->execute();                       

            $get_value = $set_stmt->fetchAll(PDO::FETCH_OBJ);   
            
            if(!empty($get_value)){
                $query  = " AND ".$get_value[0]->notes;
            }
            $sql = "SELECT pid,title,fname,lname, DATE_FORMAT(DOB,'%m-%d-%Y') AS DOB,DATE_FORMAT(FROM_DAYS(DATEDIFF(CURDATE(),DOB)), '%Y')+0 AS age,if (sex = 'Female' ,'F','M' ) as sex,street,city,state,country_code,postal_code,phone_home,phone_biz,phone_contact,phone_cell,contact_relationship as emergency_contact, phone_contact as emergency_phone,email 
                from patient_data where providerID=:loginProvderId $query order by lname, fname "; 
        
            $stmt = $db->prepare($sql) ;
            $stmt->bindParam("loginProvderId", $loginProvderId);            
            $stmt->execute();
            $patients = $stmt->fetchAll(PDO::FETCH_OBJ);   
  
            
//            echo "<pre>"; print_r($patients); echo "</pre>";
            
            $patientsdata['PatientData'] = $patients;
//            $patientsdata['FilterData'] = $fields;
            if($patientsdata)
            {
                $patientres = json_encode($patientsdata); 
                echo $patientresult =  GibberishAES::enc($patientres, $apikey);
            }
            else
            {   
                $patientres = '[{"id":"0"}]';
                echo $patientsresult = GibberishAES::enc($patientsres, $apikey);
            }
        } 
        catch(PDOException $e) 
        {
            
            $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
            echo $patientsresult = GibberishAES::enc($error, $apikey);
        }
}
function getPatientsByProvider2($loginProvderId,$fromCount)
{
    $apikey = 'rotcoderaclla';
    // The default key size is 256 bits.
    $old_key_size = GibberishAES::size();
    GibberishAES::size(256);    
     $toCount = $fromCount + 20;   
        try 
        {
            $db = getConnection();
            
            
            $query = '';
            
            $sql6 = "SELECT title FROM list_options WHERE option_id  = 'MobileScreenFilters' ";
            $stmt6 = $db->prepare($sql6) ;
            $stmt6->execute();  
            $screen_filters_names = $stmt6->fetchAll(PDO::FETCH_OBJ); 
            if(!empty($screen_filters_names)){
                $get_fields = explode(',',$screen_filters_names[0]->title);
                for($i=0; $i<count($get_fields); $i++){
                    $sql7 = "SELECT title FROM layout_options WHERE field_id  = '".$get_fields[$i]."' and form_id = 'DEM' ";
                    $stmt7 = $db->prepare($sql7) ;
                    $stmt7->execute();  
                    $screen_filters_fields = $stmt7->fetchAll(PDO::FETCH_OBJ); 
                    if(!empty($screen_filters_fields)){
                                
                        $query2 .=  $get_fields[$i] . " as ".str_replace("&","_",str_replace("/","_",str_replace(' ','_',$screen_filters_fields[0]->title))).",";
                    }
                }
            }
            $query = rtrim($query2, ',');
            
            $sql = "SELECT pid,title,fname,lname, DATE_FORMAT(DOB,'%m-%d-%Y') AS DOB,DATE_FORMAT(FROM_DAYS(DATEDIFF(CURDATE(),DOB)), '%Y')+0 AS age,if (sex = 'Female' ,'F','M' ) as sex,street,city,state,country_code,postal_code,phone_home,phone_biz,phone_contact,phone_cell,contact_relationship as emergency_contact, phone_contact as emergency_phone,email, $query
                from patient_data where  deceased_stat <> 'YES' and  providerID=:loginProvderId order by lname, fname "; 
        
            $stmt = $db->prepare($sql) ;
            $stmt->bindParam("loginProvderId", $loginProvderId);            
            $stmt->execute();
            $patients = $stmt->fetchAll(PDO::FETCH_OBJ);   
            if(!empty($patients)){
                for($i=0; $i< count($patients); $i++){
                    foreach($patients[$i] as $key1 => $value1){
                        $key2 = '';
                        if($key1 == 'Active_With_Practice') {
                            unset($patients[$i]->Active_With_Practice);
                            $key2 = 'Practice_Status';
                        }else if($key1 == 'Care_Plan_Oversight_CPO') {
                            unset($patients[$i]->Care_Plan_Oversight_CPO);
                            $key2 = 'CPO';
                        }else if($key1 == 'Chronic_Care_Management') {
                            unset($patients[$i]->Chronic_Care_Management);
                            $key2 = 'CCM';
                        }else if($key1 == 'New_Estb') {
                            unset($patients[$i]->New_Estb);
                            $key2 = 'New/Estb';
                        }else if($key1 == 'H___P') {
                            unset($patients[$i]->H___P);
                            $key2 = 'H&P';
                        }else{
                            $key2 = $key1;
                        }
//                        if($patients[$i]->$key1)
//                            unset($patients[$i]->$key1);
                        $patients[$i]->$key2 = $value1;
                    }
                }
            }

            if(!empty($screen_filters_names)){
                $get_fields = explode(',',$screen_filters_names[0]->title);
                for($i=0; $i<count($get_fields); $i++){
                    $sql7 = "SELECT title FROM layout_options WHERE field_id  = '".$get_fields[$i]."' and form_id = 'DEM' ";
                    $stmt7 = $db->prepare($sql7) ;
                    $stmt7->execute();  
                    $screen_filters_fields = $stmt7->fetchAll(PDO::FETCH_OBJ); 
                    if(!empty($screen_filters_fields)){
                        $options= array();
                        $sql8 = "SELECT li.title,li.option_id FROM layout_options lo INNER JOIN  list_options li ON  li.list_id = lo.list_id WHERE lo.field_id ='$get_fields[$i]'";
                        $stmt8 = $db->prepare($sql8) ;
                        $stmt8->execute();  
                        $options = $stmt8->fetchAll(PDO::FETCH_OBJ); 
                        if(!empty($options)){ 
                            foreach($options as $key => $value){
                                if($get_fields[$i] == 'deceased_stat'){
                                    if(($value->title == 'YES') !== false) {
                                        unset($options[$key]);
                                    }
                                }else if($get_fields[$i] == 'practice_status'){
                                    if(($value->title == 'NO') !== false) {
                                        unset($options[$key]);
                                    }
                                }
                            }
                            $name= '';
                            $name2 = str_replace("&","_",str_replace("/","_",str_replace(' ','_',$screen_filters_fields[0]->title)));

                            if($name2 == 'Active_With_Practice') 
                                $name = 'Practice_Status';
                            else if($name2 == 'Care_Plan_Oversight_CPO') 
                                $name = 'CPO';
                            else if($name2 == 'Chronic_Care_Management') 
                                $name = 'CCM';
                            else if($name2 == 'New_Estb') 
                                $name = 'New/Estb';
                            else if($name2 == 'H___P') 
                                $name = 'H&P';
                            else
                                $name = $name2;

                        $fields[$name] = array_values($options);
                        }
                    }
                }
            }
            
            
//            echo "<pre>"; print_r($fields); echo "</pre>";
            
            $patientsdata['PatientData'] = $patients;
            $patientsdata['FilterData'] = $fields;
            if($patientsdata)
            {
                $patientres = json_encode($patientsdata); 
                echo $patientresult =  GibberishAES::enc($patientres, $apikey);
            }
            else
            {   
                $patientres = '[{"id":"0"}]';
                echo $patientsresult = GibberishAES::enc($patientsres, $apikey);
            }
        } 
        catch(PDOException $e) 
        {
            
            $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
            echo $patientsresult = GibberishAES::enc($error, $apikey);
        }
}

// method to get patient demographics   
function sql_execute($patientId,$sqlStatement)
{
    $db = getConnection();
    $stmt = $db->prepare($sqlStatement) ;
    $stmt->bindParam("patientId", $patientId);            
    $stmt->execute();
    $demo = $stmt->fetchAll(PDO::FETCH_OBJ);
    return $demo;
}
function sql_execute2($listId,$sqlStatement)
{
    $db = getConnection();
    $stmt = $db->prepare($sqlStatement) ;
    $stmt->bindParam("list_id", $listId);            
    $stmt->execute();
    $demo = $stmt->fetchAll(PDO::FETCH_OBJ);
    return $demo;
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
function encode_demo2($demo)
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
  
function check_data_available2($newdemoArray)
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
function getDemographics($patientId)
{   
    $key = 'rotcoderaclla';
    // The default key size is 256 bits.
    $old_key_size = GibberishAES::size();
    GibberishAES::size(256);
    $pid = (int)$patientId;
    /*$sql1 = "select CONCAT(pd.title,pd.fname,' ',pd.lname) as pname, pd.pubpid, pd.DOB, pd.sex, pd.ss, pd.drivers_license, pd.status
    from patient_data as pd
    where pd.pid=:patientId";*/
    $sql1 = "select CONCAT(pd.title,pd.fname,' ',pd.lname) as pname,pd.title,pd.fname,pd.mname,pd.lname, pd.pubpid, pd.pid, pd.DOB, pd.sex, pd.ss, pd.drivers_license, pd.status
        from patient_data as pd
        where pd.pid=$pid";
  

    $sql2 = "select pd.street, pd.city, pd.state, pd.country_code, pd.postal_code, pd.mothersname, pd.guardiansname,pd.contact_relationship as emergency_contact, pd.phone_contact as emergency_phone,pd.phone_home as home_phone,pd.phone_biz as work_phone,pd.phone_cell as mobile_phone, pd.email as contact_email
        from patient_data as pd
        where pd.pid=$pid";

    $sql3 = "select concat(u.fname,' ',u.lname) as provider, pd.providerID as providerid,  pd.ref_providerID as ref_providerId ,concat(ur.fname,' ',ur.lname) as reference_provider, pd.pharmacy_id as pharmacy_id, ph.name as pharmacy, pd.hipaa_notice as hippa_notice_received, pd.hipaa_message, pd.hipaa_allowsms,
        pd.hipaa_voice,pd.hipaa_allowemail,pd.hipaa_mail,pd.allow_imm_reg_use,pd.allow_patient_portal, pd.allow_health_info_ex,pd.allow_imm_info_share
        from patient_data pd
        inner join users  u on pd.providerID=u.id
        inner join users ur on pd.ref_providerID=ur.id
        inner join pharmacies ph on pd.pharmacy_id=ph.id where pd.pid=$pid";

    $sql4 = "select pd.occupation, ed.name as employer, ed.street, ed.city, ed.state, ed.country, ed.postal_code
        from employer_data ed
        left join  patient_data pd 
        on pd.pid=ed.pid
        where ed.pid=$pid
        and (ed.name!='' AND ed.street!='' AND ed.city!='' AND ed.state!='' ANd ed.country!='' AND ed.postal_code!='')
        order by ed.id desc limit 0,1";	

    /*$sql5 = "select pd.language, pd.ethnicity, pd.race, pd.financial_review, pd.family_size, pd.monthly_income, pd.homeless, pd.interpretter, pd.migrantseasonal, pd.referral_source, pd.vfc
        from patient_data pd
        where pd.pid=$pid";*/
    $sql5 = "SELECT pd.language,t.title as languagename ,pd.ethnicity, pd.race, li.title AS racename, l.title AS ethnicityname, e.title AS eligibility_name, r.title AS ref_name, pd.financial_review, pd.family_size, pd.monthly_income, pd.homeless, pd.interpretter, pd.migrantseasonal, pd.referral_source, pd.vfc
FROM patient_data pd
INNER JOIN list_options li ON li.list_id =  'race'
INNER JOIN list_options l ON l.list_id =  'ethnicity'
LEFT JOIN list_options r ON r.list_id =  'refsource'
LEFT JOIN list_options e ON e.list_id =  'eligibility'
LEFT JOIN list_options t ON t.list_id =  'language'
WHERE pd.pid =$pid
AND pd.race = li.option_id
AND pd.ethnicity = l.option_id
AND pd.referral_source = r.option_id
AND e.option_id = pd.vfc
AND pd.language = t.option_id
";
   /* $sql5="SELECT pd.language, if(pd.language='','',t.title )AS languagename, pd.ethnicity, pd.race, if(pd.race='','',li.title) AS racename, if(pd.ethnicity='','',l.title) AS ethnicityname, if(pd.vfc='','',e.title) AS eligibility_name, if(pd.referral_source='','',r.title) AS ref_name, pd.financial_review, pd.family_size, pd.monthly_income, pd.homeless, pd.interpretter, pd.migrantseasonal, pd.referral_source, pd.vfc
            FROM patient_data pd
            INNER JOIN list_options li ON li.list_id =  'race'
            INNER JOIN list_options l ON l.list_id =  'ethnicity'
            LEFT JOIN list_options r ON r.list_id =  'refsource'
            LEFT JOIN list_options e ON e.list_id =  'eligibility'
            LEFT JOIN list_options t ON t.list_id =  'language'
            WHERE pd.pid =$pid
            AND (
            pd.race = li.option_id
            OR pd.ethnicity = l.option_id
            OR pd.referral_source = r.option_id
            OR e.option_id = pd.vfc
            OR pd.language = t.option_id
            LIMIT 0,1
)";
*/
    $sql6 = "select pd.deceased_date, pd.deceased_reason from patient_data pd where pd.pid=$pid";
	
    $sql7 = "select ic.name as insurance_provider, ad.line1 as street, ad.city, ad.city, ad.state, ad.zip, ad.country, ins.plan_name, ins.policy_number, ins.group_number, ins.subscriber_fname, ins.subscriber_lname,
        ins.subscriber_relationship, ins.subscriber_sex, ins.subscriber_DOB, ins.subscriber_street, ins.subscriber_city, ins.subscriber_state,
        ins.subscriber_country, ins.subscriber_postal_code, ins.subscriber_phone,ins.date as effective_date, ins.copay, ins.policy_type
        from insurance_data ins
        inner join insurance_companies ic on ic.id=ins.provider
        inner join addresses ad on ad.foreign_id=ic.id
        where ins.pid=$pid";

    /*$sql8 = "SELECT id,date,type,title,begdate,enddate,returndate,occurrence,classification,referredby,extrainfo,diagnosis,
                    activity,comments,pid,user,groupname,outcome,destination,reinjury_id,injury_part,injury_type,
                    injury_grade,reaction,external_allergyid,erx_source,erx_uploaded,modifydate 
             FROM lists WHERE pid = :patientId AND type = 'medical_problem' ORDER BY begdate";

    $sql9 = "SELECT id,date,type,title,begdate,enddate,returndate,occurrence,classification,referredby,extrainfo,diagnosis,
                    activity,comments,pid,user,groupname,outcome,destination,reinjury_id,injury_part,injury_type,
                    injury_grade,reaction,external_allergyid,erx_source,erx_uploaded,modifydate 
             FROM lists WHERE pid = :patientId AND type = 'allergy' ORDER BY begdate";

    $sql10 = "SELECT id,date,type,title,begdate,enddate,returndate,occurrence,classification,referredby,extrainfo,diagnosis,
                     activity,comments,pid,user,groupname,outcome,destination,reinjury_id,injury_part,injury_type,
                     injury_grade,reaction,external_allergyid,erx_source,erx_uploaded,modifydate
              FROM lists WHERE pid = :patientId AND type = 'medication' ORDER BY begdate";

    $sql11 = "select i1.id as id, i1.immunization_id as immunization_id, i1.cvx_code as cvx_code, c.code_text_short as cvx_text, ".
     " if (i1.administered_date, concat(i1.administered_date,' - '), substring(i1.note,1,20)) as immunization_data ".
     " from immunizations i1 ".
     " left join code_types ct on ct.ct_key = 'CVX' ".
     " left join codes c on c.code_type = ct.ct_id AND i1.cvx_code = c.code ".
     " where i1.patient_id = :patientId ".
             " and i1.added_erroneously = 0".
     " order by i1.administered_date desc";

    $sql12 = "select id,patient_id,filled_by_id,pharmacy_id,date_added,date_modified,provider_id,encounter,start_date,drug,
                     drug_id,rxnorm_drugcode,form,dosage,quantity,size,unit,route,substitute,refills,per_refill,filled_date,
                     medication,note,active,site,prescriptionguid,erx_source,erx_uploaded,drug_info_erx
              from prescriptions where patient_id=:patientId and active='1'";*/

    $sql13 = "select id,date,body,pid,user,groupname,activity,authorized,title,assigned_to,deleted,message_status 
              from pnotes where pid=$pid and deleted=0";

    $sql14 = "select id,date,event,user,recipient,description,patient_id 
              from extended_log where patient_id=$pid";

    $sql15 = "select id,date,pid,user,groupname,authorized,activity,bps,bpd,weight,height,temperature,temp_method,pulse,
                 respiration,note,BMI,BMI_status,waist_circ,head_circ,oxygen_saturation
          from form_vitals where pid=$pid";

    try 
    {
        $db = getConnection();           

        $demo1=sql_execute($patientId,$sql1);
        $demo2=sql_execute($patientId,$sql2);
        $demo3=sql_execute($patientId,$sql3);
        $demo4=sql_execute($patientId,$sql4);
        $demo5=sql_execute($patientId,$sql5);
        $demo6=sql_execute($patientId,$sql6);
        $demo7=sql_execute($patientId,$sql7);
        /*$demo8=sql_execute($patientId,$sql8); //print_r($demo8);
        $demo9=sql_execute($patientId,$sql9);
        $demo10=sql_execute($patientId,$sql10);
        $demo11=sql_execute($patientId,$sql11);
        $demo12=sql_execute($patientId,$sql12);*/
        $demo13=sql_execute($patientId,$sql13);
        $demo14=sql_execute($patientId,$sql14);
        $demo15=sql_execute($patientId,$sql15);

        $newdemo1=encode_demo($demo1);              
        $newdemo2=encode_demo($demo2);  
        $newdemo3=encode_demo($demo3);  
        $newdemo4=encode_demo($demo4);  
        $newdemo5=encode_demo($demo5);  
        $newdemo6=encode_demo($demo6);              
        $newdemo7=encode_demo($demo7);  
        /*$newdemo8=encode_demo($demo8);  //print_r($newdemo8);
        $newdemo9=encode_demo($demo9);  
        $newdemo10=encode_demo($demo10);  
        $newdemo11=encode_demo($demo11);              
        $newdemo12=encode_demo($demo12);*/			
        $newdemo13=encode_demo($demo13);  
        $newdemo14=encode_demo($demo14);  
        $newdemo15=encode_demo($demo15);  

        $newdemo['Who'] = check_data_available($newdemo1);
        $newdemo['Contact'] = check_data_available($newdemo2);
        $newdemo['Choices'] = check_data_available($newdemo3);
        $newdemo['Employer'] = check_data_available($newdemo4);
        $newdemo['Stats'] = check_data_available($newdemo5);
        $newdemo['Misc'] = check_data_available($newdemo6);
        $newdemo['Insurance'] = check_data_available($newdemo7);

        /*$newdemo['Medical_Problems'] = check_data_available($newdemo8);
        $newdemo['Allergies'] = check_data_available($newdemo9);
        $newdemo['Medications'] = check_data_available($newdemo10);
        $newdemo['Immunization'] = check_data_available($newdemo11);
        $newdemo['Prescription'] = check_data_available($newdemo12);*/
        $newdemo['Notes'] = check_data_available($newdemo13);
        $newdemo['Disclosure'] = check_data_available($newdemo14);
        $newdemo['Vitals'] = check_data_available($newdemo15);

        $newdemores = json_encode($newdemo);
        echo $newdemoresult = GibberishAES::enc($newdemores, $key);

    } 
    catch(PDOException $e) 
    {
        $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo $patientsresult = GibberishAES::enc($error, $key);
        
    }
}

// method to get list of encounters for given provider
function getEncounterList($loginProvderId,$patientId,$mode)
{
        $apikey = 'rotcoderaclla';
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
	if($mode=='clinical')
	{
            $sql = " SELECT enc.encounter AS encounter,
		 CONCAT(pd.fname,' ',pd.lname) as patient,
		GROUP_CONCAT(DISTINCT DATE(enc.date)) AS encounterdate,
		GROUP_CONCAT(DISTINCT ifnull(enc.reason,'')) AS reason,
		GROUP_CONCAT(DISTINCT ifnull(concat(u.fname,' ',u.lname),'')) AS provider,
		GROUP_CONCAT(DISTINCT ifnull(enc.facility,'')) AS facility,
		GROUP_CONCAT(DISTINCT ifnull(fc.name,'')) AS billing_facility,
		GROUP_CONCAT(DISTINCT ifnull(CONCAT(' <b><u>',bl.code_type,' ',bl.code, '</u></b> - ',if(bl.code_type ='CPT4', (select code_text from billing where code = bl.code and code_type='CPT4' and activity = 1 limit 0,1), (select title FROM lists where diagnosis = CONCAT('ICD9:',bl.code) limit 0,1) )),'')) AS billing,
		ifnull(CONCAT('Primary: ',ic.name),'') as Insurance
		FROM   form_encounter enc
		LEFT JOIN   issue_encounter ie
		  ON   ie.encounter = enc.encounter
		LEFT JOIN   lists li
		  ON   li.id = ie.list_id
		INNER JOIN   users u
		  ON   u.id = enc.provider_id
		LEFT JOIN facility fc
		  ON	 fc.id = enc.billing_facility
		INNER JOIN patient_data pd
		  ON pd.pid = enc.pid	
		LEFT JOIN billing bl
		  ON	bl.encounter = enc.encounter
		LEFT JOIN insurance_data id
		  ON id.pid=pd.pid
		LEFT JOIN insurance_companies ic
		  ON ic.id=id.provider
		   WHERE enc.provider_id=:loginProvderId
		   AND pd.pid=:patientId AND id.type='primary' 
                GROUP BY enc.encounter
		ORDER BY enc.date desc";
//		$sql = "SELECT enc.encounter AS encounter,
//		 CONCAT(pd.fname,' ',pd.lname) as patient,
//		GROUP_CONCAT(DISTINCT DATE(enc.date)) AS encounterdate,
//		GROUP_CONCAT(DISTINCT ifnull(li.`type`,''),if(li.`type` is not null, ':', ''),ifnull(li.title,'')) AS issues,
//		GROUP_CONCAT(DISTINCT ifnull(enc.reason,'')) AS reason,
//		GROUP_CONCAT(DISTINCT ifnull(concat(u.fname,' ',u.lname),'')) AS provider,
//		GROUP_CONCAT(DISTINCT ifnull(enc.facility,'')) AS facility,
//		GROUP_CONCAT(DISTINCT ifnull(fc.name,'')) AS billing_facility,
//		GROUP_CONCAT(DISTINCT ifnull(CONCAT(bl.code_type,' ',bl.code),'')) AS billing,
//		ifnull(CONCAT('Primary: ',ic.name),'') as Insurance
//		FROM   form_encounter enc
//		LEFT JOIN   issue_encounter ie
//		  ON   ie.encounter = enc.encounter
//		LEFT JOIN   lists li
//		  ON   li.id = ie.list_id
//		INNER JOIN   users u
//		  ON   u.id = enc.provider_id
//		LEFT JOIN facility fc
//		  ON	 fc.id = enc.billing_facility
//		INNER JOIN patient_data pd
//		  ON pd.pid = enc.pid	
//		LEFT JOIN billing bl
//		  ON	bl.encounter = enc.encounter
//		LEFT JOIN insurance_data id
//		  ON id.pid=pd.pid
//		LEFT JOIN insurance_companies ic
//		  ON ic.id=id.provider
//		   WHERE enc.provider_id=:loginProvderId 
//		   AND pd.pid=:patientId AND id.type='primary' 
//                GROUP BY enc.encounter
//		ORDER BY enc.date desc";
        
	try 
        {
            $db = getConnection();
            $stmt = $db->prepare($sql) ;
            $db->query("SET SQL_BIG_SELECTS=1"); 
            $stmt->bindParam("loginProvderId", $loginProvderId); 
            $stmt->bindParam("patientId", $patientId); 
            $stmt->execute();
            $encounters = $stmt->fetchAll(PDO::FETCH_OBJ);
               //print_r($encounters);         
            if($encounters)
            {
                $i=0;
                while(isset($encounters[$i]))
                {
                    $sql = "SELECT form_id,form_name,encounter 
                    FROM forms 
                    WHERE encounter=".$encounters[$i]->encounter."
                    AND pid=$patientId
                    AND (form_name='Face to Face' OR 
                        form_name='Lab Requisition' OR 
                        form_name='Vitals' OR 
                        form_name='SOAP' OR
                        form_name='Speech Dictation' OR
                        form_name='Allcare Review Of Systems' OR
                        form_name='Allcare Physical Exam')
                        AND deleted=0
                        order by date desc";
                    $db->query("SET SQL_BIG_SELECTS=1"); 
                    $stmt = $db->prepare($sql);
                    $stmt->execute();
                    $forms = $stmt->fetchAll(PDO::FETCH_OBJ);
                    if($forms)
                    {
                        //returns forms
                        $j=0;
                        //$encounters[$i]->form_id='';
                        $encounters[$i]->form_name='';
                        while(isset($forms[$j]))
                        {
                            //$form_names=json_encode($forms);                         
                            //$encounters[$i]->form_id.=$forms[$j]->form_id.',';
                            $encounters[$i]->form_name.=$forms[$j]->form_name.',';
                            //print_r($encounters[$i]);
                            $j++;
                        }
                       //$encounters[$i]->form_id=rtrim($encounters[$i]->form_id,',');
                        $encounters[$i]->form_name=rtrim($encounters[$i]->form_name,',');
                    }
                    else
                    {
                        $encounters[$i]->form_name='';
                    }
                    $i++;                    
                }
//                echo "<pre>";print_r($encounters); echo "</pre>"; 
                $encountersres = json_encode($encounters); 
                echo $encountersresult = GibberishAES::enc($encountersres, $apikey);


            }
            else
            {    
                $encountersres = '[{"id":"0"}]';
                echo $encountersresult = GibberishAES::enc($encountersres, $apikey);
            }
        } 
        catch(PDOException $e) 
        {
            
            $error =  '{"error":{"text":'. $e->getMessage() .'}}'; 
            echo $encountersresult = GibberishAES::enc($error, $apikey);
        }
	}
	else
	{
		$sql = "SELECT enc.encounter AS encounter,GROUP_CONCAT(DISTINCT DATE(enc.date)) AS encounterdate,
		enc.billing_note, 
		bl.code,
		bl.fee as charge,
		aa.paid as paid, 
		aa.adj as adj,
		bl.fee-aa.paid as balance,
		ifnull(CONCAT('Primary: ',ic.name),'') as Insurance	
		FROM   form_encounter enc
			INNER JOIN patient_data pd
		  ON pd.pid = enc.pid	
		LEFT JOIN (SELECT	bl.encounter, 
								sum(bl.fee) as fee ,
								GROUP_CONCAT(DISTINCT ifnull(CONCAT(bl.code_type,bl.code),'')) AS code
						FROM billing bl GROUP BY 	bl.encounter) as bl
		  ON	bl.encounter = enc.encounter
		LEFT JOIN (SELECT ar_activity.encounter,
								sum(ar_activity.adj_amount) as adj ,
								sum(ar_session.pay_total) as paid
						FROM ar_activity 
						LEFT join ar_session 
						on ar_session.session_id=ar_activity.session_id
					group by ar_activity.encounter
				) AS aa
		on aa.encounter=enc.encounter
		LEFT JOIN insurance_data id
		  ON id.pid=enc.pid
		LEFT JOIN insurance_companies ic
		  ON ic.id=id.provider	
		   WHERE enc.provider_id=:loginProvderId
		   AND pd.pid=:patientId 
		GROUP BY enc.encounter
		ORDER BY enc.date desc";
        
		try 
        {
            $db = getConnection();
            $stmt = $db->prepare($sql) ;
            $db->query("SET SQL_BIG_SELECTS=1"); 
            $stmt->bindParam("loginProvderId", $loginProvderId); 
            $stmt->bindParam("patientId", $patientId); 
            $stmt->execute();
            $encounters = $stmt->fetchAll(PDO::FETCH_OBJ);
            //print_r($encounters);         
            if($encounters)
            {
               $encountersres = json_encode($encounters); 
               echo $encountersresult = GibberishAES::enc($encountersres, $apikey);
 
            }
            else
            {    
                $res = '[{"id":"0"}]';
                echo $encountersresult = GibberishAES::enc($res, $apikey);
            }
        } 
        catch(PDOException $e) 
        {
            $error = '{"error":{"text":'. $e->getMessage() .'}}';
            echo $encountersresult = GibberishAES::enc($error, $apikey);
        }
	}
}

// method to create new message
function createMessage()
{
    try
    {
        $db = getConnection();
        $key = 'rotcoderaclla';
        $msgrequest = Slim::getInstance()->request();
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        $msgresult =  GibberishAES::dec($msgrequest->getBody(), $key);
//        $msgresult =  GibberishAES::dec('U2FsdGVkX1+2e1tZD/ZWrzBSA/z9ep+dIJPVlOOn7LJJiHlgMfF5vJM7p13j9tvp3ufIQ58BCzaEfBdWd8LB/7nPWfY3IV6FNSrGv+tBYzm+mwFqxzXG8Fn/Eid3hI5yeP0L9q6E4qBIErqNwGQ6qhT8TpyuARXcqfkniTaF0117plbzkzNhCSqSwLE8l2TKMJPtPUf8kdOn2TfXznWN8w==', $key);
        $msgArray = json_decode($msgresult,TRUE);
//        echo "<pre>";        print_r($msgArray); echo "</pre>";
        /* $request = Slim::getInstance()->request();
        $msgArray = json_decode($request->getBody(),TRUE);*/
        $msgId = $msgArray['id'];
        $loginProvderId = $msgArray['providerid'];
        $pid = $msgArray['pid'];
        $assigned_to = $msgArray['assigned_to'];
        $body = $msgArray['body'];
        $title = $msgArray['title'];
        $msg_status = $msgArray['msg_status'];
        $previous_msg = $msgArray['previous_msg'];
        $rs=getUserName($loginProvderId);
        $resultant_error = 0;
        $resultant_error2 = 0;
        $resultant = 0;
        if($rs)
        {
            $username = $rs[0]['username'];
            $assignedto = explode(',',$assigned_to);
            if($msgId){
                for($i=0;$i<count($assignedto);$i++){
                    $getname = "SELECT username FROM users WHERE id = $assignedto[$i]";
                    $getnamestmt = $db->prepare($getname);
                    $getnamestmt->execute();
                    $name = $getnamestmt->fetchAll(PDO::FETCH_OBJ);

                    //$sql= "UPDATE pnotes SET date=NOW(),body=CONCAT(date_format(now(), '%Y-%m-%d %H:%i'),'".' '."($username to ".$name[0]->username.")".' '." $body'),pid=$pid,title='$title', user='$username', assigned_to='".$name[0]->username."',message_status='$msg_status' WHERE id=$msgId";
                    $sql= "UPDATE pnotes SET date=NOW(),body=CONCAT('$previous_msg',CONCAT(date_format(now(), '%Y-%m-%d %H:%i'),'".' '."($username to ".$name[0]->username.")".' '." $body')),pid=$pid,title='$title', user='$username', assigned_to='".$name[0]->username."',message_status='$msg_status' WHERE id=$msgId";
                    $q = $db->prepare($sql);
                    if($q->execute())
                    {   
                        $resultant = 1;
//                            $res = '[{"id":"1"}]';
//                            echo $result = GibberishAES::enc($res, $key);
                    }
                    else
                    {
                        $resultant_error = 1;
//                            $res = '[{"id":"0"}]';   
//                            echo $result = GibberishAES::enc($res, $key);
                    }
                }
            }else{
                for($i=0;$i<count($assignedto);$i++)
                {       
                        $getname = "SELECT username FROM users WHERE id = $assignedto[$i]";
                        $getnamestmt = $db->prepare($getname);
                        $getnamestmt->execute();
                        $name = $getnamestmt->fetchAll(PDO::FETCH_OBJ);
                        
                        $sql = "INSERT INTO pnotes (date, body, pid, user, groupname, activity, authorized, title, assigned_to, message_status)
                        values (NOW(), CONCAT(date_format(now(), '%Y-%m-%d %H:%i'),'".' '."($username to ".$name[0]->username.")".' '." $body'), $pid, '$username', 'Default', 1, 1, '$title', '".$name[0]->username."', '$msg_status')";
                        $q = $db->prepare($sql);
                        if($q->execute())

                        {   
                            $resultant = 1;
//                                echo $res = '[{"id":"1"}]';
//                                echo $result = GibberishAES::enc($res, $key);
                        }
                        else
                        {
                            $resultant_error = 1;
//                                echo $res = '[{"id":"0"}]'; 
//                                echo $result = GibberishAES::enc($res, $key);
                        }
                }
            }
            
        }
        else
        {
            $resultant_error2 = 1;
//                echo $res = '[{"id":"0"}]';
//                echo $result = GibberishAES::enc($res, $key);
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
    }
}

// method to gte previous dictation
function getDictation($loginProvderId, $pid, $encounter)
{
    $key = 'rotcoderaclla';
    // The default key size is 256 bits.
    $old_key_size = GibberishAES::size();
    GibberishAES::size(256);
    try
    {
        $db = getConnection();                               
	$rs=getUserName($loginProvderId);
        $username='';
        if($rs)
        {
            echo $username = $rs[0]['username'];
        }
               
        $qryFormExists="SELECT COUNT(1) FROM forms 
                        WHERE forms.encounter=$encounter 
                        AND forms.form_name='Speech Dictation'
			AND forms.deleted=0";
        
        $stmtFormExists = $db->prepare($qryFormExists) ;		
        $stmtFormExists->execute();
        $resFormExists = $stmtFormExists->fetchAll(PDO::FETCH_OBJ);
          
        if($resFormExists)
        {
          
			$sqlGetDictationText="SELECT fd.pid,fd.date,fd.user,fd.dictation,f.form_name
                                  FROM form_dictation fd
				INNER JOIN forms f ON
				fd.id=f.form_id
                                  WHERE fd.pid=$pid
                                  AND fd.user='$username'
                                  AND f.encounter=$encounter
				  AND f.deleted=0
				  AND f.form_name='Speech Dictation' 
                                  AND pd.practice_status = 'YES' AND pd.deceased_stat != 'YES'
                                  order by f.date DESC LIMIT 1";

            $stmtGetDictationText = $db->prepare($sqlGetDictationText) ;           
            $stmtGetDictationText->execute();
            $dictationText = $stmtGetDictationText->fetchAll(PDO::FETCH_OBJ);
        
            if($dictationText)
            {
                //if encounter already exists return encounter id                
                $dictationTextres = json_encode($dictationText); 
                echo $dictationTextresult = GibberishAES::enc($dictationTextres, $key);
            }
            else
            {
                $dictationTextres = '[{"id":"0"}]';  
                echo $dictationTextresult = GibberishAES::enc($dictationTextres, $key);
                
            }
        }
        else
        {
            $dictationTextres = '[{"id":"0"}]';  
            echo $dictationTextresult = GibberishAES::enc($dictationTextres, $key);
                
        }
        
    }
    catch(PDOException $e) 
    {   
        $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo $dictationTextresult = GibberishAES::enc($error, $key);
    }
}

// method to save dictation
function createAudioDictation($loginProvderId, $pid, $encounter, $dictation, $isNuance)
{
    try
    {
        $db = getConnection();
	$rs=getUserName($loginProvderId);


        if($rs)
        {
            $username = $rs[0]['username'];       	

			$sqlCheckDictationExists="SELECT f.form_id,fd.id,fd.pid,fd.date,fd.user,fd.dictation
                                  FROM form_dictation fd
				INNER JOIN forms f ON
				fd.id=f.form_id
                                  WHERE fd.pid=$pid
                                  AND fd.user='$username'
                                  AND f.encounter=$encounter
				  AND f.deleted=0
				  AND f.form_name='Speech Dictation'
                                  order by f.date DESC LIMIT 1";

			
			$stmtCheckDictationExists = $db->prepare($sqlCheckDictationExists) ;           
           
            $stmtCheckDictationExists->execute();
            $exists = $stmtCheckDictationExists->fetchAll(PDO::FETCH_OBJ);
               
		if(trim($dictation)=='~')
    			$dictation='';
   
//	    if($isNuance==0)
//          {
//                $dictation=$dictation."_(".date("Y-m-d H:i:s").")";
//          }

            if(!$exists)
            {
                $sql = "INSERT INTO form_dictation (date, pid, user, groupname, authorized, activity, dictation)
                        VALUES (NOW(), $pid, '$username', 'Default', 1, 1, '$dictation')";

                $q = $db->prepare($sql);

                if($q->execute())
                {

                    //$lastInsertedId=mysql_insert_id();

                    $sqlGetLastDictation="SELECT MAX(id) as lastInsertedId 
                                          FROM form_dictation";

                    $db = getConnection();
                    $stmt = $db->prepare($sqlGetLastDictation) ;           
                    $stmt->execute();
                    $newEnc = $stmt->fetchAll(PDO::FETCH_OBJ);

                    if($newEnc)
                    {
                        $lastInsertedId = $newEnc[0]->lastInsertedId;
                    }

                    $sql2 = "INSERT INTO forms (date,encounter,form_name,form_id,pid,user,groupname,authorized,deleted,formdir)
                             VALUES (NOW(), $encounter, 'Speech Dictation', $lastInsertedId,$pid,'$username','Default', 1, 0, 'dictation')";

                    $q2 = $db->prepare($sql2);
                    if($q2->execute())
                    {
                        echo '[{"id":"1"}]';
                    }
                    else
                    {
                        echo '[{"id":"0"}]';
                    }
                }
                else
                {
                    echo '[{"id":"-1","msg":"Insert failed"}]';
                }
            }
            else
            {
				$old_dictation=$exists[0]->dictation;
				$old_id=$exists[0]->id;

				$dictation_new=$old_dictation." ".$dictation."";// append current to old

				if($isNuance==0) 
				{ 
					$dictation_new=$old_dictation." ( File: ".$dictation." )";
				}
				else
				{
					$dictation_new=$dictation;
				}

                $sqlUpdateDictation = "UPDATE form_dictation 
                        SET date=NOW(),
                            dictation='$dictation_new' 
                        WHERE pid=$pid 
                        AND user='$username'
						AND id=$old_id";

                $q = $db->prepare($sqlUpdateDictation);

                if($q->execute())
                {
                    echo '[{"id":"1"}]';
                }
                else
                {
                    echo '[{"id":"-2","msg":"Update failed"}]';
                }
            }

        }
        else
        {
            echo '[{"id":"0"}]';
        }
		
    }
    catch(PDOException $e) 
    {   
        echo '{"error":{"text":'. $e->getMessage() .'}}'; 
    }
}

function createDictation()
{
    
    try
    {
        $db = getConnection();
        $key = 'rotcoderaclla';
        $request = Slim::getInstance()->request();
	//echo "<br>body=".$request->getBody();
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        $dictationreq =  GibberishAES::dec($request->getBody(), $key);
        
        $dictationArray = json_decode($dictationreq,TRUE);
	//$dictationArray = json_decode($request->getBody(),TRUE);
        //print_r($dictationArray);die;
        $pid=$dictationArray[0]['pid'];   
        $loginProvderId=$dictationArray[0]['loginProviderId'];
        $encounter=$dictationArray[0]['encounter'];   
        $dictation=$dictationArray[0]['dictation'];   
        $isNuance=$dictationArray[0]['isNuance'];
    
        $rs=getUserName($loginProvderId);
        //if($rs && $rs[0]->username!='')
		if(count($rs)>0 && $rs[0]['username']!='')
        {
            $username = $rs[0]['username'];
        /*
            $sqlCheckDictationExists="SELECT pid,date,user,dictation
                                      FROM form_dictation
                                      WHERE pid=$pid
                                      AND user='$username'
                                      AND DATE(date)=DATE(now())";
		*/

			$qry = "SELECT pid FROM patient_data WHERE pid=$pid  AND practice_status = 'YES' AND deceased_stat = 'NO'";
			$stmt = $db->prepare($qry) ;          
			$stmt->execute();
			$prs = $stmt->fetchAll();
			if(count($prs)>0 && $prs[0]['pid']>0)
			{
				$qry = "SELECT encounter FROM form_encounter WHERE encounter=$encounter AND pid=$pid";
				$stmt = $db->prepare($qry);				
				$stmt->execute();
				$ers = $stmt->fetchAll();
				if(count($ers)>0 && $ers[0]['encounter']>0)
				{
					$sqlCheckDictationExists="SELECT f.form_id,fd.id,fd.pid,fd.date,fd.user,fd.dictation
									  FROM form_dictation fd
					INNER JOIN forms f ON
					fd.id=f.form_id
									WHERE fd.pid=$pid
									AND fd.user='$username'
									AND f.encounter=$encounter
									AND f.form_name='Speech Dictation'
									AND f.deleted=0 order by f.date desc LIMIT 1";

					
					$stmtCheckDictationExists = $db->prepare($sqlCheckDictationExists) ;           
			   
					$stmtCheckDictationExists->execute();
					$exists = $stmtCheckDictationExists->fetchAll(PDO::FETCH_OBJ);
				   
					if(trim($dictation)=='~')
					$dictation='';
	   
					//            if($isNuance==0)
					//            {
					//                $dictation=$dictation."_(".date("Y-m-d H:i:s").")";
					//            }

					if(!$exists)
					{
						$sql = "INSERT INTO form_dictation (date, pid, user, groupname, authorized, activity, dictation)
								VALUES (NOW(), $pid, '$username', 'Default', 1, 1, '$dictation')";

						$q = $db->prepare($sql);

						if($q->execute())
						{

							//$lastInsertedId=mysql_insert_id();

							$sqlGetLastDictation="SELECT MAX(id) as lastInsertedId 
												  FROM form_dictation";

							$db = getConnection();
							$stmt = $db->prepare($sqlGetLastDictation) ;           
							$stmt->execute();
							$newEnc = $stmt->fetchAll(PDO::FETCH_OBJ);

							if($newEnc)
							{
								$lastInsertedId = $newEnc[0]->lastInsertedId;
							}

							$sql2 = "INSERT INTO forms (date,encounter,form_name,form_id,pid,user,groupname,authorized,deleted,formdir)
									 VALUES (NOW(), $encounter, 'Speech Dictation', $lastInsertedId,$pid,'$username','Default', 1, 0, 'dictation')";

							$q2 = $db->prepare($sql2);
							if($q2->execute())
							{
								$dicnotes = '[{"id":"1","msg":"Insert done"}]';
                                                                echo $dicnotesult = GibberishAES::enc($dicnotes, $key); 
							}
							else
							{
								$insertnotdone =  '[{"id":"0","msg":"Insert NOT done"}]';
                                                                echo $dicnotesult = GibberishAES::enc($insertnotdone, $key); 
							}
						}
						else
						{
							$insertfail =  '[{"id":"-1","msg":"Insert failed"}]';
                                                        echo $dicnotesult = GibberishAES::enc($insertfail, $key); 
						}
					}
					else
					{
						$old_dictation=$exists[0]->dictation;
						$old_id=$exists[0]->id;

						$dictation_new=$old_dictation." ".$dictation."";// append current to old

						if($isNuance==0) 
						{ 
							$dictation_new=$old_dictation." ( File: ".$dictation." )";
						}
						else
						{
							$dictation_new=$dictation;
						}

						$sqlUpdateDictation = "UPDATE form_dictation 
								SET date=NOW(),
									dictation='$dictation_new' 
								WHERE pid=$pid 
								AND user='$username'
								AND id=$old_id";

						$q = $db->prepare($sqlUpdateDictation);

						if($q->execute())
						{
							$updatedone =  '[{"id":"1","msg":"Update done"}]';
                                                        echo $dicnotesult = GibberishAES::enc($updatedone, $key); 
						}
						else
						{
							$updatefail =  '[{"id":"-2","msg":"Update failed"}]';
                                                        echo $dicnotesult = GibberishAES::enc($updatefail, $key); 
						}
					}				
				}
				else
				{
					$invalidenc = '[{"id":"4","msg":"Invalid EncounterId"}]';
                                        echo $dicnotesult = GibberishAES::enc($invalidenc, $key); 
				}
			}
			else
			{
				$invalidpat = '[{"id":"3","msg":"Invalid PatientId"}]';
                                echo $dicnotesult = GibberishAES::enc($invalidpat, $key); 
			}
        }
        else
        {
                $invalidpro = '[{"id":"2","msg":"Invalid ProviderId"}]';
                echo $dicnotesult = GibberishAES::enc($invalidpro, $key); 
        }    
    }
    catch(PDOException $e) 
    {   
        $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo $dicnotesult = GibberishAES::enc($error, $key); 
    }        
                
}

// method to get ICD codes
function getICD9Codes()
{
    $key = 'rotcoderaclla';
    // The default key size is 256 bits.
    $old_key_size = GibberishAES::size();
    GibberishAES::size(256);
    $sql = "select dx_code, short_desc from icd9_dx_code limit 150";

    try 
        {
            $db = getConnection();
            $stmt = $db->prepare($sql) ;
            $stmt->execute();
            $codes = $stmt->fetchAll(PDO::FETCH_OBJ);
                        
            if($codes)
            {
                //returns icd9 code list
                //echo "<pre>"; print_r($codes); echo "</pre>";
                $codesres = json_encode($codes); 
                echo $codesresult = GibberishAES::enc($codesres, $key);
            }
            else
            {    
                $codesres = '[{"id":"0"}]';
                echo $codesresult = GibberishAES::enc($codesres, $key);
            }
        } 
        catch(PDOException $e) 
        {
            
            $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
            echo $codesresult = GibberishAES::enc($error, $key);
        }
}

//method to check if encounter exists
function checkEncounterExists($pid,$loginProvderId,$encDate)
{
    try
    {
        $key = 'rotcoderaclla';
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        $sqlCheckEncExists="SELECT encounter FROM form_encounter 
                            WHERE pid=$pid 
                            AND provider_id=$loginProvderId 
                            AND DATE(date)='$encDate'";

        $db = getConnection();
        $stmt = $db->prepare($sqlCheckEncExists);           
        $stmt->execute();
        $encExists = $stmt->fetchAll(PDO::FETCH_OBJ);

        if($encExists)
        {
            //if encounter already exists return encounter id
            $ensres = json_encode($encExists);
            echo $codesresult = GibberishAES::enc($ensres, $key);
        }
        else
        {
            $ensres = '[{"id":"0"}]';
            echo $codesresult = GibberishAES::enc($ensres, $key);
        }
    }
    catch(PDOException $e)
    {
        $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo $codesresult = GibberishAES::enc($error, $key);
    }        
}

// method to create new encounter
function createNewEncounter()
{
    try
    {
	$key = 'rotcoderaclla';
        $request = Slim::getInstance()->request();
        //$statusArray = json_decode($request->getBody(),TRUE);
        //$apptArray = json_decode($request->getBody(),TRUE);
	// The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        $newresult =  GibberishAES::dec($request->getBody(), $key);
        
        $apptArray = json_decode($newresult,TRUE);
        
        $pid=$apptArray['pid'];
        $loginProvderId=$apptArray['loginProvderId'];
        $reason=$apptArray['comments'];
        $pc_facility=$apptArray['pc_facility'];
        $pc_catid=$apptArray['pc_catid'];
        $billing_facility=$apptArray['billing_facility'];
        $encDate=$apptArray['encDate'];
		
        $sqlCheckEncExists="SELECT encounter FROM form_encounter 
                            WHERE pid=$pid 
                            AND provider_id=$loginProvderId 
                            AND DATE(date)='$encDate'";

        $db = getConnection();
        $stmt = $db->prepare($sqlCheckEncExists);
        $stmt->execute();
        $encExists = $stmt->fetchAll(PDO::FETCH_OBJ);

        if($encExists)
        {
            //if encounter already exists return encounter id
            $encexist = json_encode($encExists); 
            echo $encexistres = GibberishAES::enc($encexist, $key);
        }
        else
        {
            //else create new encounter

            $db = getConnection();
    
			$qry = "SELECT gl_value FROM globals 
					WHERE gl_name='auto_create_new_encounters'";
			$stmt = $db->prepare($qry);
			$stmt->execute();
			$global_values = $stmt->fetch(PDO::FETCH_OBJ);           
			if($global_values->gl_value==1)
			{
				$encounter = todaysEncounterCheck($pid, $encDate, $reason, $pc_facility, $billing_facility,$loginProvderId, $pc_catid, false);
				if($encounter)
				{
					/*$info_msg .= xl("New encounter created with id"); 
					$info_msg .= " $encounter";*/
					$encgetres = json_encode($encounter); 
                                        echo $encgetresult = GibberishAES::enc($encgetres, $key);
				}
			}
			else
			{
				$sqlGetMaxEncounter="SELECT MAX(encounter)+1 as max_encounter 
									 FROM form_encounter";

				$stmt = $db->prepare($sqlGetMaxEncounter);
				$stmt->execute();
				$resMaxEncounter = $stmt->fetchAll(PDO::FETCH_OBJ);

				if($resMaxEncounter)
				{
					$encounter = $resMaxEncounter[0]->max_encounter;
				}

				$sql = "INSERT INTO form_encounter (date, facility, facility_id, pid, encounter, pc_catid, provider_id, billing_facility)
				VALUES ('$encDate', '$facility',$pc_facility,$pid,$encounter,$pc_catid,$loginProvderId,$billing_facility)";
				$q = $db->prepare($sql);

				if($q->execute())
				{
					//get the encounter id of the last encounter
					
					$sqlGetLastEncounter="SELECT MAX(encounter) as encounter FROM form_encounter 
								WHERE pid=$pid 
								AND provider_id=$loginProvderId";

					$db = getConnection();
					$stmt = $db->prepare($sqlGetLastEncounter) ;
					$stmt->execute();
					$newEnc = $stmt->fetchAll(PDO::FETCH_OBJ);

					if($newEnc)
					{
						$newencres = json_encode($newEnc); 
                                                echo $newencresult = GibberishAES::enc($newencres, $key);
					}
					else
					{
						$newencres = '[{"id":"-1"}]';     
                                                echo $newencresult = GibberishAES::enc($newencres, $key);
					}
				}
				else
				{
					$newencres = '[{"id":"0"}]';
                                        echo $newencresult = GibberishAES::enc($newencres, $key);
				}
			}
        }

    }
    catch(PDOException $e) 
    {
        $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo $newencresult = GibberishAES::enc($error, $key);
    }
}

// method to get completed forms based on encounter
function getCompletedFormsByEncounter($pid,$loginProvderId,$encounter)
{
    try
    {
        $db=getConnection();
		$rs=getUserName($loginProvderId);
        if($rs)
        {
            $username = $rs[0]['username'];
        }
    
        $allForms='';
        /*
        $qryGetForms="SELECT id,date,encounter,form_name,form_id,pid,user
                      FROM forms
                      WHERE encounter=$encounter AND pid=$pid 
                      AND user='$username' AND DATE(date)=DATE(now()) AND deleted=0";
*/
	$qryGetForms="SELECT id,date,encounter,form_name,form_id,pid,user
                      FROM forms
                      WHERE encounter=$encounter AND pid=$pid 
                      AND user='$username' AND deleted=0";

        $stmtGetForms = $db->prepare($qryGetForms) ;		
        $stmtGetForms->execute();
        $resGetForms = $stmtGetForms->fetchAll(PDO::FETCH_OBJ);
          
        $qryIsFeeSheet="SELECT id,date,code_type,code,pid,provider_id,user
                        FROM billing
                        WHERE encounter=$encounter AND pid=$pid 
                        AND user=$loginProvderId AND DATE(date)=DATE(now())";
        $stmtIsFeeSheet = $db->prepare($qryIsFeeSheet) ;		
        $stmtIsFeeSheet->execute();
        $resIsFeeSheet = $stmtIsFeeSheet->fetchAll(PDO::FETCH_OBJ);
        //$resIsFeeSheet['id']='0';

        if($resIsFeeSheet)
        {
            //$resIsFeeSheet['form_name']='Fee Sheet';        
            $resIsFeeSheet[0]->form_name='Fee Sheet'; 
        }       
        
        $qryIsMessage="SELECT id,date,body,pid,user
                       FROM pnotes
                       WHERE pid=$pid 
                       AND user='$username' AND DATE(date)=DATE(now())";
        $stmtIsMessage = $db->prepare($qryIsMessage) ;		
        $stmtIsMessage->execute();
        $resIsMessage = $stmtIsMessage->fetchAll(PDO::FETCH_OBJ);        
        
        if($resIsMessage)
        {
            //$resIsMessage['form_name']='Create Message';   
            $i=0;
            while($i<count($resIsMessage))
            {
                $resIsMessage[$i]->form_name='Create Message';   
                $i++;
            }
        }
        
        $allForms = array_merge($resGetForms,$resIsFeeSheet,$resIsMessage);
                    
        if($allForms)
        {
                echo json_encode($allForms);  
        }
        else
        {    
            echo '[{"id":"0"}]';
        }
        
    }
    catch(PDOException $e) 
    {      
        echo '{"error":{"text":'. $e->getMessage() .'}}'; 
    }
                        
}

// method to save face to face encounter
function createFacetoFace()
{
    try
    {
        $db = getConnection();
		
        $request = Slim::getInstance()->request();
        $facetofaceArray = json_decode($request->getBody(),TRUE);

        $Id=$facetofaceArray['id'];
        $pid=$facetofaceArray['pid'];
        $encounter=$facetofaceArray['encounter'];
        $loginProvderId=$facetofaceArray['loginProviderId'];
        $note_id=$facetofaceArray['note_id'];
        $radBound=$facetofaceArray['is_home_bound'];
        $radCare=$facetofaceArray['is_hhc_needed'];
        $radPhysician=$facetofaceArray['other_physician'];
        $radVisit=$facetofaceArray['is_house_visit_needed'];
        $txtMedical=$facetofaceArray['medical_condition'];
        $nextVisitDays=$facetofaceArray['necessary_hhs'];
        $txtNursing=$facetofaceArray['nursing'];
        $txtPhysical=$facetofaceArray['physical_therapy'];
        $txtOccupational=$facetofaceArray['occupational_therapy'];
        $txtSpeech=$facetofaceArray['speech'];
        $txtFindings=$facetofaceArray['support_service_reason'];
        $txtTreatment=$facetofaceArray['care_treatment'];
        $txtHomeBound=$facetofaceArray['patient_homebound_reason'];
        /*$txtNurse=$facetofaceArray['nurse_practitioner_signature'];
        $txtNursePractitionerSignDate=$facetofaceArray['nurse_practitioner_signature_date'];
        $txtPhysicianSignature=$facetofaceArray['physician_signature'];
        $txtPrintedName=$facetofaceArray['printed_name'];
        $txtPrintedDate=$facetofaceArray['printed_name_date'];*/
        $txtVisitDate=$facetofaceArray['date_of_service'];
			                 
  
        $radBound =($radBound=='~')?'':$radBound;
        $radCare  =($radCare=='~')?'':$radCare;
        $radPhysician  =($radPhysician=='~')?'':$radPhysician;
        $radVisit =($radVisit=='~')?'':$radVisit;
        $txtMedical =($txtMedical=='~')?'':$txtMedical;
        $nextVisitDays =($nextVisitDays=='~')?'':$nextVisitDays;
        $txtNursing =($txtNursing=='~')?'':$txtNursing;
        $txtPhysical =($txtPhysical=='~')?'':$txtPhysical;
        $txtOccupational=($txtOccupational=='~')?'':$txtOccupational;
        $txtSpeech =($txtSpeech=='~')?'':$txtSpeech;
        $txtFindings =($txtFindings=='~')?'':$txtFindings;
        $txtTreatment =($txtTreatment=='~')?'':$txtTreatment;
        $txtHomeBound =($txtHomeBound=='~')?'':$txtHomeBound;
        /*$txtNurse =($txtNurse=='~')?'':$txtNurse;
        $txtNursePractitionerSignDate =($txtNursePractitionerSignDate=='~')?'':$txtNursePractitionerSignDate;
        $txtPhysicianSignature =($txtPhysicianSignature=='~')?'':$txtPhysicianSignature;
        $txtPrintedName =($txtPrintedName=='~')?'':$txtPrintedName;
        $txtPrintedDate =($txtPrintedDate=='~')?'':$txtPrintedDate;*/
        $txtVisitDate =($txtVisitDate=='~')?'':$txtVisitDate;

       $rs=getUserName($loginProvderId);
		   
		if($rs)
		{
			$username = $rs[0]['username'];
			
			if($Id==0)
			{
				$insert_form_FacetoFace_Sql ="INSERT INTO tbl_form_facetoface(pid,encounter,note_id,is_home_bound,is_hhc_needed,other_physician, is_house_visit_needed,medical_condition,necessary_hhs,nursing,physical_therapy,occupational_therapy,speech,                                                             care_treatment,support_service_reason,patient_homebound_reason,created_date,date_of_service)
				VALUES(:pid,:encounter,:note_id,:radBound,:radCare,:radPhysician,:radVisit,:txtMedical,:nextVisitDays,:txtNursing,:txtPhysical,
				:txtOccupational,:txtSpeech,:txtTreatment,:txtFindings,:txtHomeBound,now(),:txtVisitDate)";

				$q = $db->prepare($insert_form_FacetoFace_Sql);
				if($q->execute(array( ':pid'=>$pid,
                        ':encounter'=>$encounter,
						':note_id'=>$note_id,
						':radBound'=>$radBound,
						':radCare'=>$radCare,
						':radPhysician'=>$radPhysician,
						':radVisit'=>$radVisit,
						':txtMedical'=>$txtMedical,
						':nextVisitDays'=>$nextVisitDays,
						':txtNursing'=>$txtNursing,
						':txtPhysical'=>$txtPhysical,
						':txtOccupational'=>$txtOccupational,
						':txtSpeech'=>$txtSpeech,
						':txtTreatment'=>$txtTreatment,
						':txtFindings'=>$txtFindings, 
						':txtHomeBound'=>$txtHomeBound,
						':txtVisitDate'=>$txtVisitDate

                )))
				{       

					$sqlGetLastDictation="SELECT MAX(id) as lastInsertedId 
                                     FROM tbl_form_facetoface";

                    $db = getConnection();
                    $stmt = $db->prepare($sqlGetLastDictation) ;           
                    $stmt->execute();
                    $newEnc = $stmt->fetchAll(PDO::FETCH_OBJ);
                    if($newEnc)
                    {
                        $lastInsertedId = $newEnc[0]->lastInsertedId;
                    }
                            
                    $sql2 = "INSERT INTO forms (date,encounter,form_name,form_id,pid,user,groupname,authorized,deleted,formdir)
                    values (NOW(), :encounter, 'Face to Face', $lastInsertedId,:pid,:username,'Default', 1, 0, 'face_to_face')";

                    $q2 = $db->prepare($sql2);
                    if($q2->execute(array(':username'=>$username,
                                                    ':pid'=>$pid,
                                                    ':encounter'=>$encounter)))
                    {
                        echo '[{"id":"1"}]';
                    }
                    else
                    {
                        echo '[{"id":"0"}]';
                    }
                
                }
                else
                {
                     echo '[{"id":"0"}]';                 
                }
			}	
			else
            {
              
			    $update_form_FacetoFace_Sql ="UPDATE tbl_form_facetoface
                                                          SET pid=:pid,encounter=:encounter,note_id=:note_id,is_home_bound=:radBound,is_hhc_needed=:radCare,
                                                          other_physician=:radPhysician,is_house_visit_needed=:radVisit,medical_condition=:txtMedical,
                                                          necessary_hhs=:nextVisitDays,nursing=:txtNursing,physical_therapy=:txtPhysical,
                                                          occupational_therapy=:txtOccupational,speech=:txtSpeech,
                                                          care_treatment=:txtTreatment,support_service_reason=:txtFindings,
                                                          patient_homebound_reason=:txtHomeBound,date_of_service=:txtVisitDate
                                                          WHERE Id=:Id";

				$q = $db->prepare($update_form_FacetoFace_Sql);
				if($q->execute(array( ':Id'=>$Id,
                    ':pid'=>$pid,
                    ':encounter'=>$encounter,
					':note_id'=>$note_id,
					':radBound'=>$radBound,
					':radCare'=>$radCare,
					':radPhysician'=>$radPhysician,
					':radVisit'=>$radVisit,
					':txtMedical'=>$txtMedical,
					':nextVisitDays'=>$nextVisitDays,
					':txtNursing'=>$txtNursing,
					':txtPhysical'=>$txtPhysical,
					':txtOccupational'=>$txtOccupational,
					':txtSpeech'=>$txtSpeech,
					':txtTreatment'=>$txtTreatment,
					':txtFindings'=>$txtFindings, 
					':txtHomeBound'=>$txtHomeBound,
					':txtVisitDate'=>$txtVisitDate

                )))
				{
      
					echo '[{"id":"1"}]';
                }
                else
                {
                    echo '[{"id":"0"}]';
                }

            }				   
						
        }
        else
        {
            echo '[{"id":"-12"}]';  
        }            
  
	}
	catch(PDOException $e) 
    {   
        echo '{"error":{"text":'. $e->getMessage() .'}}'; 
    }
}


// method to save bab requisition form
function createLabRequest()
{
  try
  {
        $db = getConnection();
        $flag=0;
        $request = Slim::getInstance()->request();
        $labrequestArray = json_decode($request->getBody(),TRUE);

        $Id=$labrequestArray['id'];
        $pid=$labrequestArray['pid'];
        $encounter=$labrequestArray['encounter'];
        $loginProvderId=$labrequestArray['loginProviderId'];
        $date_of_request=$labrequestArray['date_of_request'];
        $stat=$labrequestArray['stat'];
        $specimen_week=$labrequestArray['specimen_week'];
        $fasting=$labrequestArray['fasting'];
        $frail_health=$labrequestArray['frail_health'];
        $is_home_bound=$labrequestArray['is_home_bound'];
        $is_preference_home_health=$labrequestArray['is_preference_home_health'];
        $diagnosis_codes_arry=$labrequestArray['diagnosis_codes'];
        if($diagnosis_codes_arry!='~')
                $diagnosis_codes=implode(",",$diagnosis_codes_arry);
        else
                $diagnosis_codes='';
        $tests_arry=$labrequestArray['tests'];
        if($tests_arry!='~')
                $tests=implode(",",$tests_arry);
        else
                $tests='';
         
        $other1=$labrequestArray['other1'];
        $other2=$labrequestArray['other2'];
        $other3=$labrequestArray['other3'];
        $is_colonoscopy_required=$labrequestArray['is_colonoscopy_required'];
        $patient_has=$labrequestArray['patient_has'];
        $physician_signature=$labrequestArray['physician_signature'];                        

        $date_of_signature=$labrequestArray['date_of_signature'];
		
        $lab1=$labrequestArray['lab1'];
        $lab2=$labrequestArray['lab2'];
        $lab3=$labrequestArray['lab3'];
        $lab4=$labrequestArray['lab4'];
        $lab5=$labrequestArray['lab5'];                
        
	$date_of_request =($date_of_request=='~')?'':$date_of_request;
        $stat =($stat=='~')?'':$stat;
        $specimen_week  =($specimen_week=='~')?'':$specimen_week;
        $fasting  =($fasting=='~')?'':$fasting;
        $frail_health =($frail_health=='~')?'':$frail_health;
        $is_home_bound =($is_home_bound=='~')?'':$is_home_bound;
        $is_preference_home_health =($is_preference_home_health=='~')?'':$is_preference_home_health;
        $is_colonoscopy_required=($is_colonoscopy_required=='~')?'':$is_colonoscopy_required;
        $patient_has =($patient_has=='~')?'':$patient_has;
        $physician_signature =($physician_signature=='~')?'':$physician_signature;
        $date_of_signature =($date_of_signature=='~')?'':$date_of_signature;            
	
        $lab1 =($lab1=='~')?'':$lab1;
        $lab2 =($lab2=='~')?'':$lab2;
        $lab3 =($lab3=='~')?'':$lab3;
        $lab4 =($lab4=='~')?'':$lab4;
        $lab5 =($lab5=='~')?'':$lab5;
       
        //print_r($labrequestArray);
        $rs=getUserName($loginProvderId);

        if($rs)
        {
            $username = $rs[0]['username'];  

			if($Id==0)
			{
                              
			  $insert_form_lab_requisition_Sql ="INSERT INTO tbl_form_lab_requisition (pid, encounter, created_by, date_of_request, 
			  stat,specimen_week,fasting,frail_health,is_home_bound,is_preference_home_health,
			  diagnosis_codes,tests,other1,other2,other3,is_colonoscopy_required,patient_has,
			  lab1,lab2,lab3,lab4,lab5,created_date)
			  values (:pid,:encounter,:loginProvderId,:date_of_request,:stat,:specimen_week,:fasting,
			:frail_health,:is_home_bound,:is_preference_home_health,:diagnosis_codes,:tests,
			:other1,:other2,:other3,:is_colonoscopy_required,:patient_has,:physician_signature,:date_of_signature,
			:lab1,:lab2,:lab3,:lab4,:lab5,NOW())";

  
			$q = $db->prepare($insert_form_lab_requisition_Sql);
  
			if($q->execute(array( ':pid'=>$pid,':loginProvderId'=>$loginProvderId,
        ':encounter'=>$encounter,
        ':date_of_request'=>$date_of_request,
        ':stat'=>$stat,
        ':specimen_week'=>$specimen_week,
        ':fasting'=>$fasting,
        ':frail_health'=>$frail_health,
        ':is_home_bound'=>$is_home_bound,
		':is_preference_home_health'=>$is_preference_home_health,
        ':diagnosis_codes'=>$diagnosis_codes,
        ':tests'=>$tests,
        ':other1'=>$other1,
        ':other2'=>$other2,
        ':other3'=>$other3,
        ':is_colonoscopy_required'=>$is_colonoscopy_required,
        ':patient_has'=>$patient_has,
        ':physician_signature'=>$physician_signature,
        ':date_of_signature'=>$date_of_signature,
        ':lab1'=>$lab1,
        ':lab2'=>$lab2,
        ':lab3'=>$lab3,
        ':lab4'=>$lab4,
        ':lab5'=>$lab5
       )))

        {
                                        
                                $sqlGetLastDictation="SELECT MAX(id) as lastInsertedId 
                                     FROM tbl_form_lab_requisition";

                                $db = getConnection();
                                $stmt = $db->prepare($sqlGetLastDictation) ;           
                                $stmt->execute();
                                $newEnc = $stmt->fetchAll(PDO::FETCH_OBJ);
                          
                                if($newEnc)
                                {
                                    $lastInsertedId = $newEnc[0]->lastInsertedId;
                                }
                            
                                $sql2 = "INSERT INTO forms (date,encounter,form_name,form_id,pid,user,groupname,authorized,deleted,formdir)
                               values (NOW(), :encounter, 'Lab Requisition', $lastInsertedId,:pid,:username,'Default', 1, 0, 'lab_requisition')";

                                $q2 = $db->prepare($sql2);
                                if($q2->execute(array(':username'=>$username,
                                                    ':pid'=>$pid,
                                                    ':encounter'=>$encounter)))
                                {
                                        echo '[{"id":"1"}]';
                                }
                                else
                                {
                                        echo '[{"id":"0"}]';
                                }
                
                        }
                        else
                        {
                            echo '[{"id":"0"}]';                 
                        }
						
					}
                else
                {
                    $update_form_lab_requisition_Sql ="UPDATE tbl_form_lab_requisition 
					 SET pid=$pid, encounter=$encounter,created_by=$loginProvderId,date_of_request='$date_of_request',
						 stat='$stat',specimen_week='$specimen_week',
					fasting='$fasting',frail_health='$frail_health',is_home_bound='$is_home_bound',
					is_preference_home_health='$is_preference_home_health',
					diagnosis_codes='$diagnosis_codes',tests='$tests',
						other1='$other1',other2='$other2',other3='$other3',is_colonoscopy_required='$is_colonoscopy_required',
					patient_has='$patient_has',physician_signature='$physician_signature',
					date_of_signature='$date_of_signature',
						lab1='$lab1',lab2='$lab2',lab3='$lab3',lab4='$lab4',lab5='$lab5'
					   WHERE Id=$Id";
    
					$q = $db->prepare($update_form_lab_requisition_Sql);
   
   
             if($q->execute())

				{
						echo '[{"id":"1"}]';
				}
				else
               {
						echo '[{"id":"0"}]';
                }

               }			   
						
						
		}
		else
		{
			echo '[{"id":"-12"}]';                 
		}
	}
	catch(PDOException $e) 
	{      
		echo '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
}

// method to get patient information
function getPatientInformation($pid)
{
    
$sql_patient_details="SELECT concat(pd.fname,' ',pd.lname) as pname , pd.DOB, pd.ss, concat(pd.street,',',pd.city,',',pd.state,',',pd.country_code,'-',pd.postal_code) as address, pd.phone_home
                                         FROM patient_data pd WHERE pid=:pid";


$sql_patient_insurance="SELECT tid.plan_name,tid.policy_number,tid.group_number,tic.name as insurance_company
                                         FROM insurance_data tid 
                                         INNER JOIN insurance_companies tic  
                                         ON tid.provider=tic.id
                                         WHERE tid.pid=:pid AND tid.type='primary'";

	try 
        {
            $db = getConnection();
            $stmt1 = $db->prepare($sql_patient_details);
            $stmt1->bindParam("pid", $pid);
            $stmt1->execute();
            $patientinfo = $stmt1->fetchAll(PDO::FETCH_OBJ);

            $stmt2 = $db->prepare($sql_patient_insurance);
            $stmt2->bindParam("pid", $pid);
            $stmt2->execute();
            $insuranceinfo = $stmt2->fetchAll(PDO::FETCH_OBJ);
                
            if($patientinfo)
            {
              
				$array1 = get_object_vars($patientinfo[0]);
				//$arr='"plan_name":"","policy_number":"","group_number":"","insurance_company":""';
		
				$array2['plan_name'] = "";
				$array2['policy_number'] = "";
				$array2['group_number'] = "";
				$array2['insurance_company'] = "";
				$array3 = (!empty($insuranceinfo)) ? get_object_vars($insuranceinfo[0]):$array2;
				//echo print_r($array);

						$array4 = array_merge($array1,$array3);

                //echo json_encode($array4);
				echo '['.json_encode($array4).']';  
            }
            else
            {    
                //echo 'No Patient available';
                echo '[{"id":"0"}]';
            }
        } 
        catch(PDOException $e) 
        {
            
            echo '{"error":{"text":'. $e->getMessage() .'}}'; 
        }

}

// method to save form data
function createVital()
{
    
  try
  {
        $db = getConnection();
		$flag=0;
        $request = Slim::getInstance()->request();
        $vitalsArray = json_decode($request->getBody(),TRUE);            

		$Id=$vitalsArray['id'];
        $pid=$vitalsArray['pid'];
        $loginProvderId=$vitalsArray['loginProviderId'];
        $encounter=$vitalsArray['encounter'];
        $bps=$vitalsArray['bps'];
        $bpd=$vitalsArray['bpd'];
        $weight=$vitalsArray['weight'];
        $weight_unit=$vitalsArray['weight_unit'];
        $height=$vitalsArray['height'];        
        $height_unit=$vitalsArray['height_unit'];
        $temperature=$vitalsArray['temperature'];
        $temperature_unit=$vitalsArray['temperature_unit'];
        $temp_method=$vitalsArray['temp_method'];
        $pulse=$vitalsArray['pulse'];
        $respiration=$vitalsArray['respiration'];
        $note=$vitalsArray['note'];
        $BMI=$vitalsArray['BMI'];
        $BMI_status=$vitalsArray['BMI_status'];
        $waist_circ=$vitalsArray['waist_circ'];
        $waist_circ_unit=$vitalsArray['waist_circ_unit'];
        $head_circ=$vitalsArray['head_circ'];
        $head_circ_unit=$vitalsArray['head_circ_unit'];
        $oxygen_saturation=$vitalsArray['oxygen_saturation'];     

	$bps =($bps=='~')?'':$bps;
	$bpd  =($bpd=='~')?'':$bpd;
	$weight  =($weight=='~')?'':$weight;
	$height =($height=='~')?'':$height;
	$temperature =($temperature=='~')?'':$temperature;
	$temp_method =($temp_method=='~')?'':$temp_method;
	$pulse =($pulse=='~')?'':$pulse;
	$respiration =($respiration=='~')?'':$respiration;
	$note=($note=='~')?'':$note;
	$BMI =($BMI=='~')?'':$BMI;
	$BMI_status =($BMI_status=='~')?'':$BMI_status;
	$waist_circ =($waist_circ=='~')?'':$waist_circ;
	$head_circ =($head_circ=='~')?'':$head_circ;
	$oxygen_saturation =($oxygen_saturation=='~')?'':$oxygen_saturation;     
       
	$rs=getUserName($loginProvderId);
        if($rs)
		{
			$username = $rs[0]['username'];
			 
			          if($Id==0)
			             {
                        $insert_form_vital_Sql="INSERT INTO form_vitals(date,pid,user,authorized,activity,bps,bpd,weight,weight_unit,height,height_unit,temperature,temperature_unit,temp_method,pulse,respiration,note,BMI,BMI_status,waist_circ,waist_circ_unit,head_circ,head_circ_unit,oxygen_saturation)
                           VALUES (NOW(),:pid,:loginProvderId,0,1,:bps,:bpd,:weight,:weight_unit,:height,:height_unit,:temperature,:temperature_unit,
:temp_method,:pulse,:respiration,:note,:BMI,:BMI_status,
:waist_circ,:waist_circ_unit,:head_circ,:head_circ_unit,:oxygen_saturation)";

    $q = $db->prepare($insert_form_vital_Sql);
  
    if($q->execute(array(':pid'=>$pid,':loginProvderId'=>$loginProvderId,
    ':bps'=>$bps,':bpd'=>$bpd,':weight'=>$weight,':weight_unit'=>$weight_unit,':height'=>$height,':height_unit'=>$height_unit,
    ':temperature'=>$temperature,':temperature_unit'=>$temperature_unit,':temp_method'=>$temp_method,':pulse'=>$pulse,
    ':respiration'=>$respiration,':note'=>$note,':BMI'=>$BMI,':BMI_status'=>$BMI_status,
    ':waist_circ'=>$waist_circ,':waist_circ_unit'=>$waist_circ_unit,':head_circ'=>$head_circ,':head_circ_unit'=>$head_circ_unit,':oxygen_saturation'=>$oxygen_saturation
       )))
                        {
        
                                $sqlGetLastDictation="SELECT MAX(id) as lastInsertedId 
                                     FROM form_vitals";

                                $db = getConnection();
                                $stmt = $db->prepare($sqlGetLastDictation) ;           
                                $stmt->execute();
                                $newEnc = $stmt->fetchAll(PDO::FETCH_OBJ);
                          
                                if($newEnc)
                                {
                                    $lastInsertedId = $newEnc[0]->lastInsertedId;
                                }
                            
                                $sql2 = "INSERT INTO forms (date,encounter,form_name,form_id,pid,user,groupname,authorized,deleted,formdir)
                               values (NOW(), :encounter, 'Vitals', $lastInsertedId,:pid,:username,'Default', 1, 0, 'vitals')";

                                $q2 = $db->prepare($sql2);
                                if($q2->execute(array(':username'=>$username,
                                                    ':pid'=>$pid,
                                                    ':encounter'=>$encounter)))
                                {
                                        echo '[{"id":"1"}]';
                                }
                                else
                                {
                                        echo '[{"id":"0"}]';
                                }
                
                        }
												
                        else
                        {
                            echo '[{"id":"0"}]';                 
                        }
					}

                    else
                {
              
          $update_form_vital_Sql="UPDATE form_vitals
        SET pid=:pid,user=:loginProvderId,authorized=0,activity=1,bps=:bps,     bpd=:bpd,weight=:weight,weight_unit=:weight_unit,height=:height,height_unit=:height_unit,     temperature=:temperature,temperature_unit=:temperature_unit,temp_method=:temp_method,pulse=:pulse,
     respiration=:respiration,note=:note,BMI=:BMI,BMI_status=:BMI_status,     waist_circ=:waist_circ,waist_circ_unit=:waist_circ_unit,head_circ=:head_circ,head_circ_unit=:head_circ_unit,
	 oxygen_saturation=:oxygen_saturation
                    WHERE Id=:Id";

                     $q = $db->prepare($update_form_vital_Sql);
      
      if($q->execute(array(':Id'=>$Id,':pid'=>$pid,':loginProvderId'=>$loginProvderId,
    ':bps'=>$bps,':bpd'=>$bpd,':weight'=>$weight,':weight_unit'=>$weight_unit,':height'=>$height,':height_unit'=>$height_unit,
    ':temperature'=>$temperature,':temperature_unit'=>$temperature_unit,':temp_method'=>$temp_method,':pulse'=>$pulse,
    ':respiration'=>$respiration,':note'=>$note,':BMI'=>$BMI,':BMI_status'=>$BMI_status,
    ':waist_circ'=>$waist_circ,':waist_circ_unit'=>$waist_circ_unit,':head_circ'=>$head_circ,':head_circ_unit'=>$head_circ_unit,':oxygen_saturation'=>$oxygen_saturation
       )))
                        {
           echo '[{"id":"1"}]';
                        }
                        else
                        {
                           echo '[{"id":"0"}]';
                         }

   
                }					
						
                }
                else
                {
                    echo '[{"id":"-12"}]';  
                    
                }
    
  }
  catch(PDOException $e) 
  {      
    echo '{"error":{"text":'. $e->getMessage() .'}}'; 
  }
} 

// method to store SOAP form data
function createSoap()
{
    try
    {
        $db = getConnection();
        $flag=0;
        $request = Slim::getInstance()->request();
        $soapArray = json_decode($request->getBody(),TRUE);
		print_r($soapArray);
	
        $id=$soapArray['id'];
        $pid=$soapArray['pid'];
        $loginProvderId=$soapArray['loginProviderId'];
        $encounter=$soapArray['encounter'];
        $subjective=$soapArray['subjective'];
        $objective=$soapArray['objective'];
        $assessment=$soapArray['assessment'];
        $plan=$soapArray['plan'];
	$subjective =($subjective=='~')?'':$subjective;
	$objective  =($objective=='~')?'':$objective;
	$assessment  =($assessment=='~')?'':$assessment;
	$plan =($plan=='~')?'':$plan;
        
	$rs=getUserName($loginProvderId);
//	echo "loginuser=".$loginProvderId;/*
        if($rs)
        {
                $username = $rs[0]['username'];
				if($id==0)
				{
					$insert_form_soap_Sql="INSERT INTO form_soap(date,pid,user,authorized,activity,subjective,objective,assessment,plan)
										   VALUES (NOW(),:pid,:loginProvderId,0,1,:subjective,:objective,:assessment,:plan)";

					$q = $db->prepare($insert_form_soap_Sql);

					if($q->execute(array( ':pid'=>$pid,':loginProvderId'=>$loginProvderId,
					   ':subjective'=>$subjective,':objective'=>$objective,
					  ':assessment'=>$assessment,':plan'=>$plan
					)))
					{                       

							$sqlGetLastDictation="SELECT MAX(id) as lastInsertedId 
								 FROM form_soap";

							$db = getConnection();
							$stmt = $db->prepare($sqlGetLastDictation) ;           
							$stmt->execute();
							$newEnc = $stmt->fetchAll(PDO::FETCH_OBJ);

							if($newEnc)
							{
								$lastInsertedId = $newEnc[0]->lastInsertedId;
							}

							$sql2 = "INSERT INTO forms (date,encounter,form_name,form_id,pid,user,groupname,authorized,deleted,formdir)
						   values (NOW(), :encounter, 'SOAP', $lastInsertedId,:pid,:username,'Default', 1, 0, 'soap')";

							$q2 = $db->prepare($sql2);
							if($q2->execute(array(':username'=>$username,
												':pid'=>$pid,
												':encounter'=>$encounter)))
							{
									echo '[{"id":"1"}]';
							}
							else
							{
									echo '[{"id":"0"}]';
							}

					}
					else
					{
						echo '[{"id":"0"}]';                 
					}
				}
				else
				{
					$update_form_soap_Sql="UPDATE form_soap
					SET date=NOW(),
					subjective=:subjective,
					objective=:objective,
					assessment=:assessment,
					plan=:plan
					WHERE Id=:id";
					$q = $db->prepare($update_form_soap_Sql);

					if($q->execute(array( ':id'=>$id,':subjective'=>$subjective,':objective'=>$objective,
					  ':assessment'=>$assessment,':plan'=>$plan
					)))
					{  
						echo '[{"id":"1"}]';
					}
					else
					{
						echo '[{"id":"0"}]';
					}
				}
        }
        else
        {
            echo '[{"id":"0"}]';  
        }
    }
    catch(PDOException $e) 
    {      
      echo '{"error":{"text":'. $e->getMessage() .'}}'; 
    }        
}

// method to get review contents of previous encounter (Fee Sheet)
function getReviewContents($pid,$loginProvderId,$encounter)
{
    
    $sql = "SELECT code,code_type,code_text,fee,modifier,justify,units,ct_diag,ct_fee,ct_mod  
            FROM billing,code_types as ct  
            WHERE encounter=:encounter AND billing.activity>0 AND ct.ct_key=billing.code_type 
            ORDER BY code_type";//ORDER BY id
    
    try
    {
        $db = getConnection();
        $stmt = $db->prepare($sql);
        $stmt->bindParam("encounter", $encounter);
        $stmt->execute();
        $codes = $stmt->fetchAll(PDO::FETCH_OBJ);

        if($codes)
        {
            echo json_encode($codes); 
        }
        else
        {    
            echo '[{"id":"0"}]';
        }
    }
    catch(PDOException $e) 
    {
        echo '{"error":{"text":'. $e->getMessage() .'}}'; 
    }
}


// method get active users list
function getUsers()
{
    
    $sql = "SELECT id,username,CONCAT(fname,',',lname) as name FROM users
            WHERE fname!='' AND lname!='' AND active=1";

    try
    {
        $db = getConnection();
        $stmt = $db->prepare($sql) ;
        $stmt->execute();
        $codes = $stmt->fetchAll(PDO::FETCH_OBJ);

        if($codes)
        {
            echo json_encode($codes); 
        }
        else
        {    
            echo '[{"id":"0"}]';
        }
    } 
    catch(PDOException $e) 
    {
        echo '{"error":{"text":'. $e->getMessage() .'}}'; 
    }
}

// method to get list of codes for search field
function getCodesForSearch()
{

    $sql="SELECT ct_key,ct_id,ct_seq,ct_mod,ct_just,ct_mask,ct_fee,ct_rel,ct_nofs,ct_diag,ct_active,ct_label,ct_external,ct_claim,ct_proc,ct_term,ct_problem
          FROM code_types WHERE ct_active=1 AND ct_claim=1";
    
/*
$sql="SELECT b.id, b.date, b.code_type, b.code, b.pid, b.provider_id, " .
      "b.user, b.groupname, b.authorized, b.encounter, b.code_text, b.billed, " .
      "b.activity, b.payer_id, b.bill_process, b.bill_date, b.process_date, " .
      "b.process_file, b.modifier, b.units, b.fee, b.justify, b.target, b.x12_partner_id, " .
      "b.ndc_info, b.notecodes, ct.ct_diag " .
      "FROM billing as b INNER JOIN code_types as ct " .
      "ON b.code_type = ct.ct_key " .
      "WHERE ct.ct_claim = '1' AND ct.ct_active = '1' AND " .
      "b.encounter = '901658' AND b.pid = '3696' AND " .
      "b.activity = '1' ORDER BY b.date, b.id";
*/
    try 
    {
        $db = getConnection();
        $stmt = $db->prepare($sql) ;
        
        $stmt->execute();
        $codes = $stmt->fetchAll(PDO::FETCH_OBJ);           

        if($codes)
        {
            echo json_encode($codes); 
        }
        else
        {    
            echo '[{"id":"0"}]';
        }
    } 
    catch(PDOException $e) 
    {
        echo '{"error":{"text":'. $e->getMessage() .'}}'; 
    }
}


// method get list of codes based on search field value
function getSearchResult($codeSelected,$txtSearch)
{
    $db = getConnection();
            
    try 
    {
        
        //$sqlGetCode="SELECT ct_id FROM code_types WHERE ct_label='$codeSelected'";
        $sqlGetCode="SELECT ct_id FROM code_types 
		     WHERE ct_key='$codeSelected'
		     LIMIT 100";
        $stmtGetCode = $db->prepare($sqlGetCode) ;
        $stmtGetCode->bindParam("codeSelected", $codeSelected);            
        
        $stmtGetCode->execute();
        $resCodeTypeId = $stmtGetCode->fetchAll(PDO::FETCH_OBJ);  
        //echo "codeTypeId=".$sqlGetCode;
        
        if($resCodeTypeId)
        {
        $codeTypeId = $resCodeTypeId[0]->ct_id;
       
        $sql="SELECT '4' as code_external, icd9_dx_code.formatted_dx_code as code, icd9_dx_code.long_desc as code_text, icd9_dx_code.short_desc as code_text_short, codes.id, codes.code_type, codes.modifier, codes.units, codes.fee, codes.superbill, codes.related_code, codes.taxrates, codes.cyp_factor, codes.active, codes.reportable, codes.financial_reporting, 'ICD9' as code_type_name FROM icd9_dx_code LEFT OUTER JOIN `codes` ON icd9_dx_code.formatted_dx_code = codes.code AND codes.code_type = $codeTypeId WHERE (1=1 AND icd9_dx_code.long_desc LIKE '%$txtSearch%' ) AND icd9_dx_code.active='1' AND (codes.active = 1 || codes.active IS NULL) ORDER BY icd9_dx_code.formatted_dx_code+0,icd9_dx_code.formatted_dx_code";
     
        
        $stmt = $db->prepare($sql) ;                   
        //$stmt->bindParam("txtSearch", $txtSearch);  
        $stmt->execute();
        $resSearch = $stmt->fetchAll(PDO::FETCH_OBJ);           

            if($resSearch)
            {
                echo json_encode($resSearch); 
            }
            else
            {    
                echo '[{"id":"0"}]';
            }
        }
        else
        {
            echo '[{"id":"0"}]';// if the code itself doesn't exist
        }
    } 
    catch(PDOException $e) 
    {
        echo '{"error":{"text":'. $e->getMessage() .'}}'; 
    }
    
}

// method to get encounter dates
function getEncounterDates($pid,$encounter)
{
    
    $sql="SELECT DATE(date) as date,encounter 
          FROM form_encounter 
          WHERE pid=:pid and encounter!=:encounter 
          ORDER BY date DESC";
    
    try 
    {
        $db = getConnection();
        $stmt = $db->prepare($sql) ;
        $stmt->bindParam("pid", $pid);            
        $stmt->bindParam("encounter", $encounter);  
        $stmt->execute();
        $patients = $stmt->fetchAll(PDO::FETCH_OBJ);           

        if($patients)
        {
            echo json_encode($patients); 
        }
        else
        {    
            echo '[{"id":"0"}]';
        }
    } 
    catch(PDOException $e) 
    {
        echo '{"error":{"text":'. $e->getMessage() .'}}'; 
    }
}

// method to get price level
function getPriceLevel()
{
    $sql="SELECT option_id,title,seq,is_default,option_value,mapping,notes,codes
          FROM list_options WHERE list_id='pricelevel'";
    
    try 
    {
        $db = getConnection();
        $stmt = $db->prepare($sql) ;
        $stmt->execute();
        $priceLevel = $stmt->fetchAll(PDO::FETCH_OBJ);           

        if($priceLevel)
        {
            echo json_encode($priceLevel); 
        }
        else
        {
            echo '[{"id":"0"}]';
        }
    } 
    catch(PDOException $e) 
    {
        echo '{"error":{"text":'. $e->getMessage() .'}}'; 
    }
}

function new_established_Patient_searchChangeFeeSheet($pid,$loginProvderId,$encounter,
        $code_type,$code)
{
    $db = getConnection();
//        echo "<br>ct=".$code_type."<br>cd=".$code;
    try 
    {   
        
        $table=''; $codeColumn=''; $formattedCodeColumn='';
        switch($code_type)
        {
            
            case 'ICD9':$table='icd9_dx_code';
                        $codeColumn='dx_code';
                        $formattedCodeColumn='formatted_dx_code';
                        break;
                
            case 'ICD10':$table='icd10_dx_order_code';
                         $codeColumn='dx_code';
                         $formattedCodeColumn='formatted_dx_code';
                         break;
            
        }
        
        $code_text='';
        
        if($code_type!='CPT4' && ($code_type=='ICD9' || $code_type=='ICD10'))
        {
            $sqlSelectDesc="SELECT *
                        FROM $table 
                        WHERE $codeColumn='$code'
                        OR $formattedCodeColumn='$code'";
        
            $stmtSelectDesc = $db->prepare($sqlSelectDesc);

            $stmtSelectDesc->execute();

            $codeDesc = $stmtSelectDesc->fetchAll(PDO::FETCH_OBJ); 
            if($codeDesc)
            {
                $code_text=$codeDesc[0]->long_desc;
            }
        }
        else if($code_type=='CPT4')
        {
            $code_text='';
        }
        
        $provider=$loginProvderId;        
        
	
        $sqlGetProviderId="SELECT provider_id FROM form_encounter
                           WHERE encounter=$encounter";
                    
        $stmtProviderId = $db->prepare($sqlGetProviderId) ;            
        $stmtProviderId->execute();
        $providerId = $stmtProviderId->fetchAll(PDO::FETCH_OBJ);

        if($providerId)
        {
            $provider = $providerId[0]->provider_id;           
        }
        

        $sql="INSERT INTO billing(date,encounter,code_type,code,code_text,
        pid,authorized,user,groupname,activity,billed,provider_id, 
        modifier,units,fee,ndc_info,justify,notecodes) 
        values(NOW(),$encounter,'$code_type','$code','$code_text', 
               $pid,0,$loginProvderId,'Default',1,0,$provider,'',
               1, 0.00,'','','')";

        $stmt = $db->prepare($sql) ;
            //$stmt->execute();
          
        if($stmt->execute())
        {            
            echo '[{"id":"1"}]';                                    
        }
        else
        {            
            echo '[{"id":"0"}]';
        }
       
    }
    catch(PDOException $e) 
    {
        echo '{"error":{"text":'. $e->getMessage() .'}}'; 
    }
}

function reviewAdd($reviewArray)
{

    try 
    {

	$db = getConnection();
        $flag=0;
	$request = Slim::getInstance()->request();
	//echo 'body='.$request->getBody();
	$reviewArray = json_decode($request->getBody(),TRUE);	


        //print_r($reviewArray);                                
        $i=0;
        while(isset($reviewArray[$i]['pid']))
        {
            
            $pid=$reviewArray[$i]['pid']; 
            $loginProviderId=$reviewArray[$i]['loginProviderId'];
            //$loginProviderId=1;
            $encounter=@$reviewArray[$i]['encounterId'];
            $code=@$reviewArray[$i]['code'];
            $code_type=@$reviewArray[$i]['code_type'];
            $price=0.00;
            if(isset($reviewArray[$i]['price']) && @$reviewArray[$i]['price']!='')
            {
                $price=@$reviewArray[$i]['price'];
            }
            
            $modifier=$reviewArray[$i]['modifier'];
            $units=$reviewArray[$i]['units'];
            $justify=$reviewArray[$i]['justify'];
            $description=$reviewArray[$i]['description'];
            
            $table=''; $codeColumn=''; $formattedCodeColumn='';
            switch($code_type)
            {

                case 'ICD9':$table='icd9_dx_code';
                            $codeColumn='dx_code';
                            $formattedCodeColumn='formatted_dx_code';
                            break;

                case 'ICD10':$table='icd10_dx_order_code';
                             $codeColumn='dx_code';
                             $formattedCodeColumn='formatted_dx_code';
                             break;

            }
                    
            $code_text='';

    	    if($code_type!='CPT4' && ($code_type=='ICD9' || $code_type=='ICD10'))
	    {
		    $sqlSelectDesc="SELECT *
			        FROM $table 
			        WHERE $codeColumn='$code'
			        OR $formattedCodeColumn='$code'";
	
		    $stmtSelectDesc = $db->prepare($sqlSelectDesc);

		    $stmtSelectDesc->execute();

		    $codeDesc = $stmtSelectDesc->fetchAll(PDO::FETCH_OBJ); 
		    if($codeDesc)
		    {
			$code_text=$codeDesc[0]->long_desc;
		    }
	    }
	    else if($code_type=='CPT4')
	    {
	    	$code_text='';
	    }

            $provider=$loginProviderId;

	    
		$sqlGetProviderId="SELECT provider_id FROM form_encounter
				   WHERE encounter=$encounter";
			    
		$stmtProviderId = $db->prepare($sqlGetProviderId) ;            
		$stmtProviderId->execute();
		$providerId = $stmtProviderId->fetchAll(PDO::FETCH_OBJ);

		if($providerId)
		{
		    $provider = $providerId[0]->provider_id;           
		}
        
            
	    $sql="INSERT INTO billing(date,encounter,code_type,code,code_text,
            pid,authorized,user,groupname,activity,billed,provider_id, 
            modifier,units,fee,ndc_info,justify,notecodes) 
            values(NOW(),$encounter,'$code_type','$code','$code_text', 
                   $pid,0,$loginProviderId,'Default',1,0,$provider,'$modifier',
                   '$units', $price,'','$justify','')";
            //echo "<br>";
            $stmt = $db->prepare($sql) ;
            
            if($stmt->execute())
            {                
                $flag=1;
            }
            else
            {                
                $flag=0;
            }
             //echo '<br>i='.$i;                                   
            $i++;
        }    
        //echo "<br>FG=".$flag;
        //if($flag==1 && $i==count($reviewArray))
	if($flag==1 || ($i==count($reviewArray) && $flag==0))
        {                
            echo '[{"id":"1"}]';
        }
        else
        {   
            echo '[{"id":"0"}]';
        }
            
        
    }
    catch(PDOException $e) 
    {
        echo '{"error":{"text":'. $e->getMessage() .'}}'; 
    }      
}


function getFeeData($encounter)
{
    
    $db = getConnection();    
    
    try 
    {
        
	$sqlJustifyAll="SELECT DISTINCT code_type,code,justify FROM billing 
                        WHERE encounter=$encounter 
      		        AND (code_type='ICD9' OR code_type='ICD10')
			AND DATE(date)=DATE(now())
		        AND activity=1";

	    $stmt = $db->prepare($sqlJustifyAll);        
	    $stmt->bindParam("encounter", $encounter);  
	    $stmt->execute();
	    $resJustifyAll = $stmt->fetchAll(PDO::FETCH_OBJ);  
	    $justifyAll='';
	    if($resJustifyAll)
	    {
		//echo json_encode($resJustifyAll)."<br>"; 
		$j=0;
		/*
		while(isset($resJustifyAll[$j]->justify))
		{
		    if(!empty($resJustifyAll[$j]->justify))
		    {
			$justifyAll.=$resJustifyAll[$j]->justify.",";
		    }
		    $j++;
		}code_type,code
		*/

		while(isset($resJustifyAll[$j]->code_type))
		{
		    //if(!empty($resJustifyAll[$j]->justify))
		    //{
		    $justifyAll.=$resJustifyAll[$j]->code_type."|".$resJustifyAll[$j]->code.":";
		    //}
		    $j++;
		}

	    }
    		$justifyAll=rtrim($justifyAll,':');
	
 $sql="select b.id, b.code_type, b.code, b.provider_id, b.code_text, 
              b.modifier, b.units, b.fee, b.justify, '$justifyAll' as justifyAll, 		      b.notecodes

        from billing b 
        where b.encounter=:encounter
        and b.activity=1 
	ORDER BY b.id ASC";


        $stmt = $db->prepare($sql) ;        
        $stmt->bindParam("encounter", $encounter);  
        $stmt->execute();
        $feesheets = $stmt->fetchAll(PDO::FETCH_OBJ);           

        if($feesheets)
        {
            echo json_encode($feesheets); 
        }
        else
        {   
            echo '[{"id":"0"}]';
        }
    } 
    catch(PDOException $e) 
    {
        echo '{"error":{"text":'. $e->getMessage() .'}}'; 
    }
}

function deleteFeeSheetData($fid)
{
    try
    {
      $db = getConnection();
      $delete_fee_sheet="UPDATE billing SET activity=0 WHERE id=:fid";
      $q = $db->prepare($delete_fee_sheet);

      if($q->execute(array(':fid'=>$fid)))
      {
          echo '[{"id":"1"}]';
      }
      else
      {
          echo '[{"id":"0"}]';                 
      }
    }
    catch(PDOException $e) 
    {      
      echo '{"error":{"text":'. $e->getMessage() .'}}'; 
    }      
}

function updateBilling()
{
    
    try 
    {      
	$db = getConnection();
        $flag=0;
	$request = Slim::getInstance()->request();
	$billArray = json_decode($request->getBody(),TRUE);

        $i=0;

        while(isset($billArray[$i]['id']))
        {
            
            $id=$billArray[$i]['id']; 
            $encounter=$billArray[$i]['encounterId'];
            $price=0.00;
            if(isset($billArray[$i]['price']) && @$billArray[$i]['price']!='')
            {
                $price=$billArray[$i]['price'];
            }
            
            $modifier=$billArray[$i]['modifier'];
            $units=$billArray[$i]['units'];
            $justify=$billArray[$i]['justify'];
            $notecodes=$billArray[$i]['notecodes'];                
            $provider=$billArray[$i]['providerId'];
                   
            $sql = "UPDATE billing 
	     	    SET provider_id = '$provider',
	                modifier = '$modifier',
		        units = '$units',
		        fee = $price,
		        justify = '$justify',
		        notecodes = '$notecodes'
	     	    WHERE id=$id
                    AND encounter=$encounter";
             
            $stmt = $db->prepare($sql) ;        
            
            if($stmt->execute())
            {
             $flag=1;
            }
            else
            {
             $flag=0;  
            }
             //echo '<br>i='.$i;     
$flag=1;                              
            $i++;
        }    
       
        //if($flag==1 && $i==count($billArray))
	//if($flag==1 || ($i==count($billArray) && $flag==0))
	if($flag==1 || count($billArray)>0)
        {                
            echo '[{"id":"1"}]';
        }
        else
        {   
            echo '[{"id":"0"}]';
        }
                    
    }
    catch(PDOException $e) 
    {
        echo '{"error":{"text":'. $e->getMessage() .'}}'; 
    }
        
}

function saveBilling()
//function saveBilling($billArray)
{
	try
	{
		$db = getConnection();
		$flag=0;
		$request = Slim::getInstance()->request();
		$billArray = json_decode($request->getBody(),TRUE);
                //print_r($billArray);
		$i=0;
		$pid=$billArray[0]['pid'];
	  	$loginProviderId=$billArray[0]['loginProviderId'];
	  	$rendProviderId=$billArray[0]['rendProviderId'];
		$superProviderId=$billArray[0]['superProviderId'];
		$encounter=$billArray[0]['encounterId'];
		$pricelevel=$billArray[0]['pricelevel'];
		//$modifier=$billArray[0]['FeeSheetData'][0]['modifier'];
		$modifier='';
		$copay_code='';
		$copay_code_type='';
		$iscpt=0;
  
		for($j=0;$j<count(@$billArray[0]['FeeSheetData']);$j++)
		{
			if(@$billArray[0]['FeeSheetData'][$j]['code_type']=='CPT4')
			{
				$copay_code = @$billArray[0]['FeeSheetData'][$j]['code'];
				$iscpt = 1;
				break;
			}
		}

		if($iscpt==0)
		{
			$copay_code_type='';
			$copay_code='';
		}
		else
		{
			$copay_code_type='CPT4';
		}

		for($i=0;$i<count(@$billArray[0]['FeeSheetData']);$i++)
		{

			$id=@$billArray[0]['FeeSheetData'][$i]['id']; 
			$code_type=@$billArray[0]['FeeSheetData'][$i]['code_type'];
		   	$code=@$billArray[0]['FeeSheetData'][$i]['code'];
			//echo "code=".$code;
			//print_r($billArray);
			if($code_type=='ICD9' || $code_type=='ICD10')
			{
				$units=$billArray[0]['FeeSheetData'][$i]['units'];
			$provider=$billArray[0]['FeeSheetData'][$i]['providerId'];

				$sql = "UPDATE billing 
				SET provider_id = $provider,
				units = '$units'
				WHERE id=$id
				AND encounter=$encounter";
				   
				$stmt = $db->prepare($sql);
				if($stmt->execute())
				{
					$flag=1;
				}
				else
				{
					$flag=0;  
				}
   			}
			elseif($code_type=='CPT4')
			{ 
				$price=0.00;
				if(isset($billArray[0]['FeeSheetData'][$i]['price']) && @$billArray[0]['FeeSheetData'][$i]['price']!='')
				{
					$price=$billArray[0]['FeeSheetData'][$i]['price'];
				}
    				
				$modifier=$billArray[0]['FeeSheetData'][$i]['modifier'];
				$units=$billArray[0]['FeeSheetData'][$i]['units'];
				$justify=$billArray[0]['FeeSheetData'][$i]['justify'];
				$notecodes=$billArray[0]['FeeSheetData'][$i]['notecodes'];
				$provider=$billArray[0]['FeeSheetData'][$i]['providerId'];
       
				$sql = "UPDATE billing 
				SET provider_id = '$provider',
				modifier = '$modifier',
				units = '$units',
				fee = '$price',
				justify = '$justify',
				notecodes = '$notecodes'
				WHERE id=$id
				AND encounter=$encounter";

				$stmt = $db->prepare($sql) ;        

				if($stmt->execute())
				{
					$flag=1;
				}
				else
				{
					$flag=0;  
				}
			}
   	elseif($code_type=='COPAY')
   {
    $price=0.00;
    if(isset($billArray[0]['FeeSheetData'][$i]['price']) && @$billArray[0]['FeeSheetData'][$i]['price']!='')
    {
     $price=@$billArray[0]['FeeSheetData'][$i]['price'];
    }
    $qry = "INSERT INTO ar_session (payer_id, user_id, closed, pay_total, created_time, global_amount, payment_type, description, adjustment_code, post_to_date, patient_id) VALUES (0, $loginProviderId, 0,$price, NOW(), 0.00, 'patient', 'COPAY', 'patient_payment', date(NOW()), $pid)";
    
    $stmt = $db->prepare($qry) ;        
    $stmt->execute();
    
    $sqlGetLastSession="SELECT MAX(session_id) as lastInsertedId 
                                  FROM ar_session";
                    
    $stmtLastSession = $db->prepare($sqlGetLastSession) ;           
            
    $stmtLastSession->execute();
    $newSession = $stmtLastSession->fetchAll(PDO::FETCH_OBJ);

    if($newSession)
    {
     $sessionId = $newSession[0]->lastInsertedId;
     $qry_activity = "INSERT INTO ar_activity (pid, encounter, code_type, code, modifier, payer_type, post_time, post_user, session_id, pay_amount, account_code) VALUES ($pid, $encounter, '$copay_code_type', '$copay_code', '$modifier', 0, NOW(), $loginProviderId, $sessionId, $price, 'PCP')";
    
     $stmt = $db->prepare($qry_activity) ;        
     if($stmt->execute())
     {
      $flag=1;
     }
     else
     {
      $flag=0;  
     }
     
    }
    else
    {
     $flag=0;
    } 
   }
}

   //$i++;	
           $qry = "UPDATE form_encounter 
                SET provider_id = $rendProviderId,
                    supervisor_id = $superProviderId 
                WHERE pid=$pid AND encounter=$encounter";
    
            $stmtRendSup = $db->prepare($qry);        
            if($stmtRendSup->execute())
            {
                    $flag=1;
            }
            else
            {
                    $flag=0;  
            }             

	//if($flag==1)
        if($flag==1 || ($i==count($billArray) && $flag==0))
        {                
            echo '[{"id":"1"}]';
        }
        else
        {   
            echo '[{"id":"0"}]';
        }
  
 }
 catch(PDOException $e) 
    {
        echo '{"error":{"text":'. $e->getMessage() .'}}'; 
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

function createNewPatient()
//function createNewPatient($patientDetailsArray)
{
    
    try 
    {
	$key = 'rotcoderaclla';
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        $db = getConnection();        
	$request = Slim::getInstance()->request();
	$patientDetailsArray = json_decode($request->getBody(),TRUE);

        //print_r($patientDetailsArray);
/*   
	$title=$patientDetailsArray[0]['salutation'];
        $fname=$patientDetailsArray[0]['fname'];
        $mname=$patientDetailsArray[0]['mname'];
        $lname=$patientDetailsArray[0]['lname'];                
        $DOB=$patientDetailsArray[0]['dob'];        
        $ss=$patientDetailsArray[0]['ssn'];
        //$occupation=$patientDetailsArray[0]['occupation'];
        $phone_home=$patientDetailsArray[0]['phone_home'];
        $phone_cell=$patientDetailsArray[0]['phone_cell'];                
        $sex=$patientDetailsArray[0]['sex'];
        $providerID=$patientDetailsArray[0]['providerId'];
        $comments=$patientDetailsArray[0]['comments'];
*/

	$title=$patientDetailsArray['salutation'];
        $fname=$patientDetailsArray['fname'];
        $mname=$patientDetailsArray['mname'];
        $lname=$patientDetailsArray['lname'];                
        $DOB=$patientDetailsArray['dob'];        
        $ss=$patientDetailsArray['ssn'];
        //$occupation=$patientDetailsArray['occupation'];
        $phone_home=$patientDetailsArray['phone_home'];
        $phone_cell=$patientDetailsArray['phone_cell'];                
        $sex=$patientDetailsArray['sex'];
        $providerID=$patientDetailsArray['providerId'];
        $comments=$patientDetailsArray['comments'];
        
        $street=$patientDetailsArray['street'];
        $postal_code=$patientDetailsArray['postal_code'];
        $city=$patientDetailsArray['city'];
        $state=$patientDetailsArray['state'];
        $country_code=$patientDetailsArray['country_code'];
        
        

        $sqlGetMaxPid="SELECT MAX(pid) as max_pid FROM patient_data";
        $stmtGetMaxPid = $db->prepare($sqlGetMaxPid) ;           
       //$stmt->bindParam("date_today", $date_today);            
        $stmtGetMaxPid->execute();
        $maxPid = $stmtGetMaxPid->fetchAll(PDO::FETCH_OBJ);
        
        $pid = $maxPid[0]->max_pid+1;
        $pubpid=$pid;
                
        // Insert in table 'patient_data'
        $sqlInsertPatientData = "INSERT INTO patient_data(title,fname,mname,lname,DOB,ss,phone_home,phone_cell,
                                         date,sex,providerID,pubpid,pid,usertext1, street, postal_code,city, state,country_code)
                                 VALUES('$title','$fname','$mname','$lname','$DOB','$ss','$phone_home','$phone_cell',
                                        NOW(),'$sex','$providerID','$pubpid','$pid','$comments', '$street', '$postal_code', '$city', '$state', '$country_code')";           
        $stmtInsertPatientData = $db->prepare($sqlInsertPatientData) ;             
                
        // Insert in table 'employer_data'
        $sqlInsertEmployerData = "INSERT INTO employer_data(name,street,postal_code,city,state,country,date,pid ) 
                                  VALUES('','','','','','',NOW(),'$pid')";           
        $stmtInsertEmployerData = $db->prepare($sqlInsertEmployerData) ;      
        
        
        // Insert in table 'history_data'
        $sqlInsertHistoryData = "INSERT INTO history_data(date,pid)
                                 VALUES(NOW(),'$pid')";           
        $stmtInsertHistoryData = $db->prepare($sqlInsertHistoryData) ;             
                
        // Insert in table 'insurance_data'
        $sqlInsertInsuranceData1 = defaultInsuranceData('primary',$pid);
        $stmtInsertInsuranceData1 = $db->prepare($sqlInsertInsuranceData1) ;      
        $sqlInsertInsuranceData2 = defaultInsuranceData('secondary',$pid);
        $stmtInsertInsuranceData2 = $db->prepare($sqlInsertInsuranceData2) ;      
        $sqlInsertInsuranceData3 = defaultInsuranceData('tertiary',$pid);
        $stmtInsertInsuranceData3 = $db->prepare($sqlInsertInsuranceData3) ;      
                  
        
        if($stmtInsertPatientData->execute() &&
           $stmtInsertEmployerData->execute() && 
           $stmtInsertHistoryData->execute() &&
           $stmtInsertInsuranceData1->execute() && $stmtInsertInsuranceData2->execute() && $stmtInsertInsuranceData3->execute())
        {                
            //echo '[{"id":"1"}]';
	    $resul = '[{"id":"1","patient_id":"'.$pid.'"}]';
            echo $result2= GibberishAES::enc($resul , $key);
        }
        else
        {   
            $resul = '[{"id":"0"}]';
            echo $result2= GibberishAES::enc($resul, $key);
        }
                  
    }
    catch(PDOException $e) 
    {
        $resul = '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo $result2= GibberishAES::enc($resul, $key);
    }
        
}

function getAllFacilities()
{
    //$sql="SELECT id,name,phone,fax,street,city,state,postal_code,country_code,federal_ein,website,email,service_location,billing_location,accepts_assignment,pos_code,x12_sender_id,attn,domain_identifier,facility_npi,tax_id_type,color,primary_business_entity
    //    FROM facility";
    $sql="SELECT id,name,phone,fax,street,city,state,postal_code,country_code,federal_ein,website,email,service_location,billing_location,accepts_assignment,pos_code,x12_sender_id,attn,domain_identifier,facility_npi,tax_id_type,color,primary_business_entity
          FROM facility WHERE service_location!=0";
    $key = 'rotcoderaclla';
    // The default key size is 256 bits.
    $old_key_size = GibberishAES::size();
    GibberishAES::size(256);
    try 
    {
        $db = getConnection();
        $stmt = $db->prepare($sql) ;
        $stmt->execute();
        $facilities = $stmt->fetchAll(PDO::FETCH_OBJ);           
        
        if($facilities)
        {
            $str = '{ 
                "id":"0",
                "name":"Facility not matched",
                "phone":"(000) 000-0000",
                "fax":"(000) 000-0000",
                "street":" ",
                "city":" ",
                "state":" ",
                "postal_code":" ",
                "country_code":" ",
                "federal_ein":" ",
                "website":" ",
                "email":" ",
                "service_location":"1",
                "billing_location":"",
                "accepts_assignment":"",
                "pos_code":"",
                "x12_sender_id":"",
                "attn":""}';
                $arr = json_decode($str, true);
                array_unshift( $facilities, $arr );
            $facilityres= json_encode($facilities); 
            echo $facilityresult= GibberishAES::enc($facilityres, $key);
        }
        else
        {
            $facilityres = '[{"id":"-1"}]';
            echo $facilityresult= GibberishAES::enc($facilityres, $key);
        }
    } 
    catch(PDOException $e) 
    {
        $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo $facilityresult= GibberishAES::enc($error, $key);
    }
}

// method to get patients by facility
//function getPatientsByFacility($fid,$loginProvderId)
function getPatientsByFacility($fid)
{
    $key = 'rotcoderaclla';
    // The default key size is 256 bits.
    $old_key_size = GibberishAES::size();
    GibberishAES::size(256);
    if($fid == 0){
        $sql="select pd.pid, pd.title,pd.fname,pd.lname, DATE_FORMAT(pd.DOB,'%m-%d-%Y') AS DOB,
            DATE_FORMAT(FROM_DAYS(DATEDIFF(CURDATE(),pd.DOB)), '%Y')+0 AS age,if (pd.sex = 'Female' ,'F','M' ) as sex,
            pd.street,pd.city,pd.state,pd.country_code,pd.postal_code,pd.phone_cell,phone_home,phone_biz,phone_contact,pd.deceased_stat,pd.practice_status 
            from patient_data pd where pid not in (SELECT DISTINCT(patientid) FROM tbl_patientfacility)  AND pd.practice_status = 'YES' AND pd.deceased_stat != 'YES' order by pd.lname, pd.fname";  
        try 
        {
            $db = getConnection();
            $stmt = $db->prepare($sql) ;
            $stmt->execute();
            $patients = $stmt->fetchAll(PDO::FETCH_OBJ);           

            if($patients)
            {
                $patientres =  json_encode($patients); 
                echo $patientresult= GibberishAES::enc($patientres, $key);
            }
            else
            {
                $patientres = '[{"id":"0"}]';
                echo $patientresult= GibberishAES::enc($patientres, $key);
            }
        } 
        catch(PDOException $e) 
        {
            $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
            echo $patientresult= GibberishAES::enc($error, $key);
        }
    }
    else{
       $sql="SELECT DISTINCT tpf.patientid as pid, tpf.id, tpf.facilityid, pd.title, pd.fname, pd.lname, DATE_FORMAT( pd.DOB,  '%m-%d-%Y' ) AS DOB, DATE_FORMAT( FROM_DAYS( DATEDIFF( CURDATE( ) , pd.DOB ) ) ,  '%Y' ) +0 AS age, IF( pd.sex =  'Female',  'F',  'M' ) AS sex, pd.street, pd.city, pd.state, pd.country_code, pd.postal_code, pd.phone_cell,phone_home,phone_biz,phone_contact, tpf.facility_admitdate AS admitdate, tpf.facility_dischargedate AS dischargedate, tpf.facility_notes AS notes,facility_related_links as related_links,tpf.facility_roomno 
                FROM patient_data pd
                INNER JOIN tbl_patientfacility tpf ON pd.pid = tpf.patientid
                WHERE facility_isactive =  'YES'
                AND tpf.facilityid =$fid
                AND pd.practice_status =  'YES'
                AND pd.deceased_stat !=  'YES'
                AND tpf.id
                IN (

                SELECT MAX( id ) 
                FROM tbl_patientfacility
                WHERE facility_isactive =  'YES'
                GROUP BY patientid
                )";  
       
        try 
        {
            $db = getConnection();
            $stmt = $db->prepare($sql) ;
            $stmt->execute();
            $patients = $stmt->fetchAll(PDO::FETCH_OBJ);           

            if($patients)
            {
                $patientsres = json_encode($patients); 
                echo $patientsresult= GibberishAES::enc($patientsres, $key);
            }
            else
            {
                $patientres = '[{"id":"0"}]';
                echo $patientresult= GibberishAES::enc($patientres, $key);
            }
        } 
        catch(PDOException $e) 
        {
            $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
            echo $patientresult= GibberishAES::enc($error, $key);
        }
    }
	
}

// method get visit categories
function getVisitCategories()
{
    $key = 'rotcoderaclla';
    // The default key size is 256 bits.
    $old_key_size = GibberishAES::size();
    GibberishAES::size(256);
    try 
    {
        $db = getConnection();
        $sql="SELECT pc_catid, pc_catname, ROUND( pc_duration /60, 0 ) AS minutes
                FROM openemr_postcalendar_categories
                WHERE pc_cattype =0
                ORDER BY pc_catname";
    
        $stmt = $db->prepare($sql) ;
        $stmt->execute();
        $visitcategories = $stmt->fetchAll(PDO::FETCH_OBJ);           

        if($visitcategories)
        {
            $visitres = json_encode($visitcategories); 
            echo $visitresult = GibberishAES::enc($visitres, $key);
        }
        else
        {
            $visitres = '[{"id":"0"}]';
            echo $visitresult = GibberishAES::enc($visitres, $key);
        }
    } 
    catch(PDOException $e) 
    {
        $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo $visitresult = GibberishAES::enc($error, $key);
    }
    
}


function form_report($pid, $form_name, $id) 
{
    try
    {
        $db = getConnection();
        $cols = 1; // force always 1 column
        $count = 0;
 
        switch($form_name)
        {
            case 'Face to Face': $table_name='tbl_form_facetoface';  break;
            case 'Lab Requisition':$table_name='tbl_form_lab_requisition';break;
            case 'Vitals': $table_name='form_vitals';        break;
            case 'SOAP':  $table_name='form_soap';         break;              
	    case 'Speech Dictation':  $table_name='form_dictation';         break; 
        }

 //$data = formFetch("form_soap", $id);
        $sqlGetColumnsData="SELECT * FROM $table_name
                            WHERE id=$id AND pid=$pid";

        $stmtGetColumnsData = $db->prepare($sqlGetColumnsData);
        $stmtGetColumnsData->execute();
        $data = $stmtGetColumnsData->fetchAll(PDO::FETCH_OBJ);     
    
        if ($data) 
        {        
            $new_data=array();
           
            $k=0;
            foreach($data[0] as $key => $value) 
            {
                  if ($key == "id" || $key == "pid" || $key == "user" || $key == "groupname" || $key == "authorized" || $key == "activity" || $key == "date" || $value == "" || $value == "0000-00-00 00:00:00" || $value == "0000-00-00" || $value == "0.0" ) 
                  {
                    continue;
                  }
		  if($table_name == "form_vitals" && ($key == "height_unit" || $key == "weight_unit" || 
                     $key == "temperature_unit" || $key == "waist_circ_unit" || 
                     $key == "head_circ_unit"))
                  {
                      continue;
                  }
                  if ($value == "on")
                  {
                     $value = "yes";
                  }
                  $key=ucwords(str_replace("_"," ",$key));                  
                  
                  //$keyval = array('field'.$k => $key.'^'.$value );
  		  $keyval = array('field' => $key.'^'.$value );
		  
                  if($form_name=='Vitals')
                  {
			
                        if($key == "Weight") 
                        {
                            $keyval = array('field' => $key.'^'.$value.' lb');
                        }
                        elseif ($key == "Height" || $key == "Waist Circ"  || $key == "Head Circ") 
                        {
                            $keyval = array('field' => $key.'^'.$value.' in');
                        }
			elseif($key == "Bps" || $key == "Bpd") 
                        {
                            $keyval = array('field' => $key.'^'.$value.' mmHg');
                        }

			elseif ($key == "Pulse" || $key == "Respiration") 
                        {
                            $keyval = array('field' => $key.'^'.$value.' per min');
                        }

                        elseif ($key == "Temperature") 
                        {
                            $keyval = array('field' => $key.'^'.$value.' F');
                        }
			elseif ($key == "Oxygen Saturation") 
			{
			    $keyval = array('field' => $key.'^'.$value.'%');
	 		}
        		elseif ($key == "BMI") 
			{
		//       $keyval = array('field' => $key.'^'.$value.'kg/m^2');
	      $keyval = array('field' => $key.'^'.$value.'kg/m2');                    
        		}
			
                  }
                  
                  array_push($new_data,$keyval);                  
                  //$new_data=array_merge($new_data,$keyval);
               
                  $count++;
                  if ($count == $cols) 
                  {
                    $count = 0;   
                  }
                  $k++;
            }
            //print_r($new_data);
            //echo "[".json_encode($new_data)."]";
	    echo json_encode($new_data);
         }
         else
        {
            echo '[{"id":"0"}]';
        }
    }
    catch(PDOException $e) 
    {
        echo '{"error":{"text":'. $e->getMessage() .'}}'; 
    }
           
}

// method to get SOAP form data
function getSoapForm($pid, $encounterId,$Id)
{

     /*$sql = "SELECT fs.Id,fs.subjective,fs.objective,fs.assessment,fs.plan FROM form_soap as fs
             INNER JOIN forms as fm on fs.id=fm.form_id WHERE fs.pid=:pid and fm.encounter=:encounterId and fs.Id=:Id ";
*/

	$sql = "SELECT fs.Id,fs.subjective,fs.objective,fs.assessment,fs.plan FROM form_soap as fs
             INNER JOIN forms as fm on fs.id=fm.form_id WHERE fs.pid=$pid and fm.encounter=$encounterId and fs.Id=$Id ";
   try
  {
     
     $db = getConnection();
	 //mysql_set_charset('utf8');
     $stmt = $db->prepare($sql) ;
     //$stmt->bindParam("pid", $pid); 
     //$stmt->bindParam("encounterId", $encounterId);  
     //$stmt->bindParam("Id", $Id);  
        $stmt->execute();
        $facetofacesoapdetails = $stmt->fetchAll(PDO::FETCH_OBJ);           
	 //print_r($facetofacesoapdetails);
        if($facetofacesoapdetails)
        {
            //returns visit categories
            echo json_encode($facetofacesoapdetails); 
        }
        else
        {
            echo '[{"id":"0"}]';
        }

  }

  catch(PDOException $e) 
    {
        echo '{"error":{"text":'. $e->getMessage() .'}}'; 
    }

}

// method to get face to face form data
function getFacetoFaceForm($pid, $encounterId,$Id)
{

   $sql="SELECT id, is_home_bound,is_hhc_needed,other_physician,is_house_visit_needed,medical_condition,necessary_hhs,nursing,
physical_therapy,occupational_therapy,speech,care_treatment,support_service_reason,patient_homebound_reason,nurse_practitioner_signature,
nurse_practitioner_signature_date,physician_signature,printed_name,printed_name_date,date_of_service from tbl_form_facetoface where pid=:pid 
and encounter=:encounterId and Id=:Id";
    
    try 
    {
        $db = getConnection();
        $stmt = $db->prepare($sql) ;
      $stmt->bindParam("pid", $pid); 
      $stmt->bindParam("encounterId", $encounterId);   
      $stmt->bindParam("Id", $Id);   
        $stmt->execute();
        $facetofacedetails = $stmt->fetchAll(PDO::FETCH_OBJ);           

        if($facetofacedetails)
        {
            //returns visit categories
            echo json_encode($facetofacedetails); 
        }
        else
        {
            echo '[{"id":"0"}]';
        }
    } 
    catch(PDOException $e) 
    {
        echo '{"error":{"text":'. $e->getMessage() .'}}'; 
    }
}

// method to get Lab requisition form data
function getLabRequisitionForm($pid, $encounterId, $Id)
{

		$sql="SELECT id,date_of_request,stat,specimen_week,fasting,frail_health,
			  is_home_bound,is_preference_home_health,diagnosis_codes,
			  tests,other1,other2,other3,is_colonoscopy_required,patient_has,
			  physician_signature,date_of_signature,lab1,lab2,lab3,lab4,lab5
		from tbl_form_lab_requisition where pid=:pid and encounter=:encounterId and Id=:Id ";

   	

    try 
    {
        $db = getConnection();
        $stmt = $db->prepare($sql) ;
		$stmt->bindParam("pid", $pid); 
		$stmt->bindParam("encounterId", $encounterId);   
		$stmt->bindParam("Id", $Id);   
        $stmt->execute();
        $labrequestdetails = $stmt->fetchAll(PDO::FETCH_OBJ);           

        if($labrequestdetails)
        {
            //returns visit categories
            echo json_encode($labrequestdetails); 
        }
        else
        {
            echo '[{"id":"0"}]';
        }
    } 
    catch(PDOException $e) 
    {
        echo '{"error":{"text":'. $e->getMessage() .'}}'; 
    }
}
function getVitalsForm($encounterId,$Id){
    $key = 'rotcoderaclla';
    // The default key size is 256 bits.
    $old_key_size = GibberishAES::size();
    GibberishAES::size(256);
    try{
        $patientquery = "SELECT pid, DATE(date) as date FROM form_encounter WHERE encounter = $encounterId";
        $db = getConnection();
        $patientquerystmt = $db->prepare($patientquery) ;
        $patientquerystmt->execute();
        $pidval = $patientquerystmt->fetchAll(PDO::FETCH_OBJ);
        $pid = $pidval[0]->pid;
        $date = $pidval[0]->date;
        if($Id != 0){
            $sql = "SELECT fv.id,fv.pid,fv.user,fv.authorized,fv.activity,fv.bps as bp_systolic,fv.bpd as bp_diastolic,
                fv.weight as weight_lbs,fv.height as height_inches,fv.temperature as temperature_fahrenheit,
                fv.temp_method as temperature_location,fv.pulse,fv.respiration,fv.note,fv.BMI,fv.BMI_status,
                fv.waist_circ as waist_circumference_inches,fv.head_circ as head_circumference_inches,fv.oxygen_saturation 
               FROM form_vitals fv
               INNER JOIN forms fm ON fv.id=fm.form_id 
               WHERE fv.pid=:pid AND fm.encounter=:encounterId AND fv.Id=:Id";
            $stmt = $db->prepare($sql) ;
            $stmt->bindParam("pid", $pid); 
            $stmt->bindParam("encounterId", $encounterId);  
            $stmt->bindParam("Id", $Id);   
            $stmt->execute();
            $vitalsdetails = $stmt->fetchAll(PDO::FETCH_OBJ); 
            $vitalsdetails[0]->date = $date;
        }else{
             $vitalsdetails = array('id'=> 0,'pid'=> $pid,'user'=> '','authorized'=> '','activity'=> '','bp_systolic'=>'',
                 'bp_diastolic'=> '','weight_lbs'=> '','height_inches'=> '', 'temperature_fahrenheit'=> '',
                 'temperature_location'=> '','pulse'=> '','respiration'=> '','note'=> '','BMI'=> '','BMI_status'=> '',
                 'waist_circumference_inches'=> '', 'head_circumference_inches'=> '','oxygen_saturation'=> '', 'date' =>$date );
             //$vitalsdetails[0]->date = $date;
        }
        if($Id != 0){
            $vitalsdetails[0]-> vitals_stat= '';
            $encform="SELECT form_id FROM forms WHERE formdir= 'LBF2' and deleted = 0 and encounter = $encounterId and pid = $pid order by date asc";
            $encstmt31= $db->prepare($encform) ;
            $encform_id = $encstmt31->execute();
            $encformid_val = $encstmt31->fetchAll(PDO::FETCH_OBJ);

            if(!empty($encformid_val)):
                if($Id != 0){
                $vitals  = "SELECT field_value FROM lbf_data WHERE form_id = '".$encformid_val[0]->form_id."' AND field_id = 'vitals_stat'";
                $stmtV = $db->prepare($vitals);
                $stmtV->execute();
                $set_status = $stmtV->fetchAll(PDO::FETCH_OBJ);

                if(!empty($set_status)){
                    if($set_status[0]->field_value == 'pending')
                        $vitalsdetails[0]-> vitals_stat = 'pending';
                    elseif($set_status[0]->field_value == 'finalized')
                        $vitalsdetails[0]-> vitals_stat = 'finalized';
                    elseif($set_status[0]->field_value == 'finalized|pending' || $set_status[0]->field_value == 'pending|finalized')
                        $$vitalsdetails[0]-> vitals_stat= 'finalized';
                    else
                        $field_value = '';
                }else{
                    if(!empty($vitalsdetails[0]-> vitals_stat))
                        $vitalsdetails[0]-> vitals_stat= $vitalsdetails[0]-> vitals_stat;
                    else
                        $vitalsdetails[0]-> vitals_stat= '';
                }
            }else{
                    $vitalsdetails[0]-> vitals_stat= '';
            }
            endif;
        }
                  
//        echo "<pre>"; print_r($vitalsdetails); echo "</pre>";
        if($vitalsdetails){
            $vitalsdetailsres =  json_encode($vitalsdetails); 
            echo $visitresult = GibberishAES::enc($vitalsdetailsres, $key);
        }
        else{
            $vitalsdetailsres =  '[{"id":"0"}]';
            echo $visitresult = GibberishAES::enc($vitalsdetailsres, $key);
        }
    }catch(PDOException $e){
        $vitalsdetailserror =  '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo $visitresult = GibberishAES::enc($vitalsdetailserror, $key);
    }
}

// method to get patient by name
function searchPatientByName($loginProviderId,$patientName)
{
    try
    {
        $db = getConnection();
        
        $sqlGetMyPatientsByName="SELECT id,pid,title,fname,mname,lname
                                FROM patient_data
                                WHERE fname like '%$patientName%'
                                OR mname like '%$patientName%'
                                OR lname like '%$patientName%'
				OR CONCAT(fname,' ',lname) like '%$patientName%'
                                OR CONCAT(fname,' ',mname) like '%$patientName%'
                                AND providerID=$loginProviderId
                                AND  practice_status = 'YES' AND deceased_stat != 'YES'  
                                ORDER BY fname,lname";

        $stmt = $db->prepare($sqlGetMyPatientsByName);
        $stmt->execute();
        $resGetMyPatientsByName = $stmt->fetchAll(PDO::FETCH_OBJ);
        
        if($resGetMyPatientsByName)
        {
            echo json_encode($resGetMyPatientsByName); 
        }
        else
        {   // Patient Not found
            echo '[{"id":"0","msg":"Patient not found"}]';
        }
                
    }
    catch(PDOException $e) 
    {
        echo '{"error":{"text":'. $e->getMessage() .'}}'; 
    }    
    
}

// method to create new appointment
function createAppointment()
{
    try
    {
        $db = getConnection();
        $key = 'rotcoderaclla';
        $request = Slim::getInstance()->request();
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        //$result =  GibberishAES::dec('U2FsdGVkX1/OCfD5z3hyu5oz3kY2ZksCp7e8hTgCcmTk+atIDWKCwdcznEMcNvJtiYAML+u4ELzpwuRcCgCJlWEY8sYjDD2Dz462fCQBX7h0Qvgz34DLg/bzzUgh9EnG08s1QCV5jmrshKw/Z707RZTWvyJ95YozDwtrNISeA4xB5yPXC5TUE8HYtGnG0f1TnmQeKQijMPUc3elp1uMNFfG/2TBHicGQ5akGz4+0oBQskTWY3vypQ1+4cge8LmQTG7lLOFLvLmizrSi6Ry4OIRkZ28IRqiP28DpHLYDTo7iuz1CIxm8IRd4/uvXxFd4n+QEHzPtJqkI1I7NaXzaQJnPYatcPvz2sIjUmtX1M59ZtN9UuOJLNxGtXejuI9/iBSNEq/lBAiO82HZT7i6hXI0PMhHEgm9eYpnR07sV8Nm1yeVSWm0T+hDdXc0Ko3+T8go581ZjRKjNyuOyHcJpbMg==', $key);
        $result =  GibberishAES::dec($request->getBody(), $key);
	$appointmentDetailsArray = json_decode($result,TRUE);
        //print_r($appointmentDetailsArray);
        
        $pc_aid=$appointmentDetailsArray['loginProviderId'];
        $pc_pid=$appointmentDetailsArray['pid'];        
        $pc_catid=$appointmentDetailsArray['category'];                
        //$pc_time=$appointmentDetailsArray['pc_time'];
        $pc_title=$appointmentDetailsArray['title'];
        
        $pc_hometext=$appointmentDetailsArray['comments'];
        //$pc_informant=$appointmentDetailsArray['informant'];
        $pc_eventDate=$appointmentDetailsArray['appointmentStartDate'];
        //$pc_endDate=$appointmentDetailsArray['appointmentEndDate'];
        
        $pc_duration=$appointmentDetailsArray['duration'] * 60; 
        $pc_recurrtype=$appointmentDetailsArray['repeat'];
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
            's:17:"event_repeat_freq";s:1:"' . $appointmentDetailsArray['repeat_freq'] . '";' .
            's:22:"event_repeat_freq_type";s:1:"'.$appointmentDetailsArray['repeat_type'] .'";' .
            's:19:"event_repeat_on_num";s:1:"1";' .
            's:19:"event_repeat_on_day";s:1:"0";' .
            's:20:"event_repeat_on_freq";s:1:"0";}';
	    if(!is_numeric($appointmentDetailsArray['repeat_type']))
	    {
		$repeat_type_Array=explode(' ',$appointmentDetailsArray['repeat_type']);
		
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
	    if(!is_numeric($appointmentDetailsArray['repeat_type']))
	    {
		$repeat_type_Array=explode(' ',$appointmentDetailsArray['repeat_type']);
		
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
      
	
	$pc_alldayevent=$appointmentDetailsArray['allDayEvent'];         
       
	if($pc_alldayevent==0)
	{
		$startTimearray=explode(' ',$appointmentDetailsArray['startTime']);
		
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
            $pc_endDate=$appointmentDetailsArray['appointmentEndDate'];
        }
		
        $pc_alldayevent=$appointmentDetailsArray['allDayEvent']; 
        $pc_endDate=$appointmentDetailsArray['appointmentEndDate'];	

        $pc_apptstatus=$appointmentDetailsArray['status']; 
        //$pc_apptstatus='-'; 
        //$pc_prefcatid=$appointmentDetailsArray['prefcatid']; 
        //$pc_location=$appointmentDetailsArray['location']; 
        //$pc_location='';
        //$pc_eventstatus=$appointmentDetailsArray['eventstatus']; 
        $pc_eventstatus=1;
        //$pc_sharing=$appointmentDetailsArray['title']; 
        $pc_facility=$appointmentDetailsArray['facility']; 
        $billing_facility=$appointmentDetailsArray['billing_facility']; 
        
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
            }
            else
            {
                $appfixed = '[{"id":"0"}]';
                echo $appfixedresult = GibberishAES::enc($appfixed, $key);
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
            
            //echo '[{"id":"0"}]';
        }

                
    }
    catch(PDOException $e) 
    {
        $appfixed = '{"error":{"text":'. $e->getMessage() .'}}'; 
         echo $appfixedresult = GibberishAES::enc($appfixed, $key);
    }
}

// method to get available time slots
function getAvailableTimeSlots($loginProviderId,$startingDate)
{
    $key = 'rotcoderaclla';
    // The default key size is 256 bits.
    $old_key_size = GibberishAES::size();
    GibberishAES::size(256);
    try 
    {
        $db=  getConnection();

	/****************************/
        $datecheck = explode('-', $startingDate);
        if($datecheck[0] != '00' && $datecheck[1] != '00' && $datecheck[2] != '00'){
            
            $day_name=date('l', strtotime($startingDate));
            $y=date('Y', strtotime($startingDate));
            $m=date('m', strtotime($startingDate));
            $num=1;
                    $num_str='';
            $month_date_by_day=date("Y-m-d", strtotime("first $day_name of $y-$m"));

            while($month_date_by_day<$startingDate)
            {
                $month_date_by_day=date('Y-m-d',strtotime($month_date_by_day.' +1 week'));
                $num++;                                                    
            }

            switch($num)
            {
               case 1:$num_str='1st';break;
               case 2:$num_str='2nd';break;
               case 3:$num_str='3rd';break;
               case 4:$num_str='4th';break;
               case 5:$num_str='5th';break;                       

            }

            $day_count_str=$num_str." ".$day_name;        

            /****************************/        


            // first get the start time of the day, end time of the day 
            // & appointment duration (eg. 8:00 AM , 5:00 PM , 15 min)
            // FROM global settings$pc_alldayevent

            $sqlGlobalSettings="SELECT gl_name,gl_value FROM globals
                                WHERE gl_name='schedule_start'
                                OR gl_name='schedule_end'
                                OR gl_name='calendar_interval'
                                ORDER BY gl_name DESC";
            $stmtGlobalSettings = $db->prepare($sqlGlobalSettings);

            $stmtGlobalSettings->execute();
            $resGlobalSettings = $stmtGlobalSettings->fetchAll(PDO::FETCH_OBJ);           

            $start_time='';$end_time='';$slot_duration='';
            if($resGlobalSettings)
            {
                $start_time=gmdate("H:i:s", ($resGlobalSettings[0]->gl_value)*3600);
                $end_time=gmdate("H:i:s",($resGlobalSettings[1]->gl_value)*3600);
                $slot_duration=($resGlobalSettings[2]->gl_value)*60;
            }

            $alltimeslots=array($start_time);
            $next_slot=$start_time;
            while($next_slot!=$end_time)
            {
                $next_slot= date( "H:i:s",strtotime($next_slot) + $slot_duration); 
                array_push($alltimeslots,$next_slot);
            }

            $sqlRepeatAppointments="SELECT * FROM openemr_postcalendar_events
            WHERE (pc_eventDate='$startingDate' 
            OR (pc_eventDate<='$startingDate' AND pc_endDate>='$startingDate'))
            AND pc_aid=$loginProviderId";      

            $stmt = $db->prepare($sqlRepeatAppointments);
            $stmt->execute();
            $res = $stmt->fetchAll(PDO::FETCH_OBJ);     
            $flag=0;
            //echo "<br>";
            //print_r($res);
            //echo "<br>CNT=".count($res);
            if( $res )
            {		
                $i=0;

                $m=0; // for available slot arrays

                while ( isset($res[$i]->pc_eid) )
                //while ($i<count($res))  
                {	

                    $between_dates=getBetweenDates($res[$i]->pc_recurrspec,$res[$i]->pc_eventDate,$res[$i]->pc_endDate);

                    if(in_array($startingDate,$between_dates))
                    {$availableSlotsArray[$m]=array();
                        //echo "<br>in_array";
                        if($res[$i]->pc_alldayevent==1) 
                        {
                               // echo "<br>ALL DAY";
                            // Cannot create the desired event for the specified date/s
                            $flag=1;
                            break;                    
                        }
                        else
                        {
                            //echo "<br>NOTALL DAY";

                          $sqlReservedTimes="SELECT pc_startTime,pc_endTime
                            FROM openemr_postcalendar_events
                            WHERE pc_eid=".$res[$i]->pc_eid." 
                            AND pc_eventDate='".$res[$i]->pc_eventDate."'
                            AND pc_endDate='".$res[$i]->pc_endDate."'
                            AND pc_aid=$loginProviderId";

                            $stmt = $db->prepare($sqlReservedTimes);

                            $stmt->execute();
                            $resReservedTimes = $stmt->fetchAll(PDO::FETCH_OBJ);           

                            $j=0;
                            while(isset($alltimeslots[$j]))
                            {
                                $k=0;
                                $flag_a=0;
                                while(isset($resReservedTimes[$k]->pc_startTime))
                                {
                                    $pc_st=$resReservedTimes[$k]->pc_startTime;
                                    $pc_et=$resReservedTimes[$k]->pc_endTime;
                                    //if(in_array($pc_st, $alltimeslots))
                                    if($alltimeslots[$j]==$pc_st)
                                    {
                                        $flag_a=1;
                                        break;
                                    }
                                    else
                                    {
                                        $flag_a=0;
                                    }

                                    $pc_starttime=strtotime($pc_st);
                                    $pc_endtime=strtotime($pc_et);
                                    $slotTime=strtotime($alltimeslots[$j]);

                                    if(($pc_starttime<$slotTime && $pc_endtime>$slotTime))
                                    {
                                        $flag_a=1;
                                        break;
                                    }
                                    $k++;
                                }
                                if($flag_a==0)
                                {
                                    $av_s_hour=date("H",strtotime($alltimeslots[$j]));                    
                                    $av_s=date("h:i",strtotime($alltimeslots[$j]));
                                    if($av_s_hour>=12)
                                    {
                                        $av_s=$av_s." PM";
                                    }
                                    else
                                    {
                                        $av_s=$av_s." AM";
                                    }
                                    array_push($availableSlotsArray[$m],$av_s);
                                }
                                $j++;

                            }        

                        }

                    }

                    $m++;

                    $i++;          

                }

            $actual_available_slots_Array=array();

            foreach($alltimeslots as $slot)
            {
                    //echo "<br>slot=".$slot;
                    $av_s_hour=date("H",strtotime($slot));                    
                    $av_s=date("h:i",strtotime($slot));
                    if($av_s_hour>=12)
                    {                        
                    $av_s=$av_s." PM";
                    }
                    else
                    {
                    $av_s=$av_s." AM";
                    }
                    //echo "<br>av_s=".$av_s;
               array_push($actual_available_slots_Array,$av_s);			
            }

            $actual_available_slots_Array_original=$actual_available_slots_Array;
                if($flag==1)
                {
                        echo '[{"id":"0"}]';
                }
                else if($m!=0)
                {
                    $i=0;

                    //echo "<br>MM=".$m;
                    //while(!empty($availableSlotsArray[$i]))
                    while($i<$m)
                    {
                            if(!empty($availableSlotsArray[$i]))
                            {
                        $actual_available_slots_Array=array_intersect($availableSlotsArray[$i],$actual_available_slots_Array);

                            }
                        $i++;

                    }

                    $actual_available_slots=implode(',',$actual_available_slots_Array);
                    $avslotres = '[{"available_slots":"'.$actual_available_slots.'","slot_duration":"'.($slot_duration/60).'","day_count_str":"'.$day_count_str.'"}]';
                    echo $avslotresult = GibberishAES::enc($avslotres, $key);
                }
                else
                {
                    $all_time_slots_available=array();
                    foreach($alltimeslots as $slot)
                    {
                            //echo "<br>slot=".$slot;
                            $av_s_hour=date("H",strtotime($slot));                    
                            $av_s=date("h:i",strtotime($slot));
                            if($av_s_hour>=12)
                            {                        
                            $av_s=$av_s." PM";
                            }
                            else
                            {
                            $av_s=$av_s." AM";
                            }
                            //echo "<br>av_s=".$av_s;
                       array_push($all_time_slots_available,$av_s);			
                    }
                    $all_time_slots_available=implode(',',$all_time_slots_available);
                    $avslotres = '[{"available_slots":"'.$all_time_slots_available.'","slot_duration":"'.($slot_duration/60).'","day_count_str":"'.$day_count_str.'"}]';
                    echo $avslotresult = GibberishAES::enc($avslotres, $key);
                }	
            }

            else
            {

                    $all_time_slots_available=array();
                    foreach($alltimeslots as $slot)
                    {
                            //echo "<br>slot=".$slot;
                            $av_s_hour=date("H",strtotime($slot));                    
                            $av_s=date("h:i",strtotime($slot));
                            if($av_s_hour>=12)
                            {                        
                                    $av_s=$av_s." PM";
                            }
                            else
                            {
                                    $av_s=$av_s." AM";
                            }
                            //echo "<br>av_s=".$av_s;
                       array_push($all_time_slots_available,$av_s);			
                    }
                    $all_time_slots_available=implode(',',$all_time_slots_available);
                    $avslotres = '[{"available_slots":"'.$all_time_slots_available.'","slot_duration":"'.($slot_duration/60).'","day_count_str":"'.$day_count_str.'"}]';
                    echo $avslotresult = GibberishAES::enc($avslotres, $key);

            }
	}else{
            $avslotres = '[{"Invalid Date Format":"0"}]';
            echo $avslotresult = GibberishAES::enc($avslotres, $key);
        }	
    }

    catch(PDOException $e)
    {
        $error = '{"error":{"text":'. $e->getMessage() .'}}';
        echo $avslotresult = GibberishAES::enc($error, $key);
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

//To get the appointment statuses
function getAppointmentStatuses()
{
    $db = getConnection();
    /*$qry = "select title from list_options where list_id='apptstat' and option_id IN('@','^','x','?')";*/
     $qry = "select option_id as id,title from list_options where list_id='apptstat' and notes ='M' order by seq";
    $stmt = $db->prepare($qry) ;
    $stmt->execute();
    $rs = $stmt->fetchAll(PDO::FETCH_OBJ);
    echo json_encode($rs); 
}



//To get the appointment details
function getAppointmentDetails($apptid)
{
	try
	{
		$db=getConnection();
                
                $key = 'rotcoderaclla';
                // The default key size is 256 bits.
                $old_key_size = GibberishAES::size();
                GibberishAES::size(256);
        
		$sql="SELECT pd.title,concat(pd.fname,' ',pd.lname) as patient,DATE_FORMAT(ope.pc_eventDate,'%m-%d-%Y') as event_date ,if(ope.pc_endDate='0000-00-00',
                    DATE_FORMAT(ope.pc_eventDate,'%m-%d-%y'),DATE_FORMAT(ope.pc_endDate,'%m-%d-%Y'))  as end_date ,ROUND(ope.pc_duration/60) as duration, 
                    TIME_FORMAT(ope.pc_startTime, '%h:%i %p') AS start_time,TIME_FORMAT(ope.pc_endTime, '%h:%i %p') AS end_time,ope.pc_alldayevent as alldayevent,ope.pc_recurrtype as checkrecurbasedonstartdate,
                    SUBSTRING_INDEX(SUBSTRING_INDEX(ope.pc_recurrspec,';',2),':',-1) as recnormalfreq,SUBSTRING_INDEX(SUBSTRING_INDEX(ope.pc_recurrspec,';',4),':',-1) as recnormalday,
                    SUBSTRING_INDEX(SUBSTRING_INDEX(ope.pc_recurrspec,';',10),':',-1) AS recfreq, SUBSTRING_INDEX(SUBSTRING_INDEX(ope.pc_recurrspec,';',6),':',-1) as recday, SUBSTRING_INDEX(SUBSTRING_INDEX(ope.pc_recurrspec,';',8),':',-1) AS recdayday, 
                    pc.pc_catname as visit_category,fc.name as facility,bf.name as billing_facility,ope.pc_apptstatus as apptstatus,  SUBSTRING(lo.title, LENGTH(pc_apptstatus)+2) as appstatusname,
                    ope.pc_hometext as comments,concat(u.fname,' ',u.lname) as providername,pc_aid as providerid
                FROM patient_data pd INNER JOIN openemr_postcalendar_events ope ON pd.pid=ope.pc_pid
                        inner join openemr_postcalendar_categories pc on pc.pc_catid=ope.pc_catid 
                        inner join facility fc on ope.pc_facility=fc.id 
			inner join facility bf on ope.pc_billing_location=bf.id
                        inner join users u on u.id=ope.pc_aid
                        inner join list_options lo on lo.option_id=ope.pc_apptstatus
                WHERE ope.pc_eid=".$apptid;
                
                $stmt = $db->prepare($sql) ; 
        $stmt->execute();
        $apptdates = $stmt->fetchAll(PDO::FETCH_OBJ);           
        
        if($apptdates)
        {
            //echo json_encode($apptdates); 
           // For recurring Type
           if($apptdates[0]->recfreq== '"1"' && $apptdates[0]->recnormalday == '"0"'):
                $apptdates[0]->recfreq= "every";
           elseif($apptdates[0]->recfreq== '"2"'):
                $apptdates[0]->recfreq= "2nd";
           elseif($apptdates[0]->recfreq== '"3"'):
                $apptdates[0]->recfreq= "3rd";
           elseif($apptdates[0]->recfreq== '"4"'):
                $apptdates[0]->recfreq= "4th";
           elseif($apptdates[0]->recfreq== '"5"'):
                $apptdates[0]->recfreq= "5th";
           elseif($apptdates[0]->recfreq== '"6"'):
                $apptdates[0]->recfreq= '6th';
           elseif($apptdates[0]->recfreq== '"0"'):
                $apptdates[0]->recfreq= '';
           endif;
           
           // For normal recurring Freq
           if($apptdates[0]->recnormalfreq== '"1"'):
                $apptdates[0]->recnormalfreq= "every";
           elseif($apptdates[0]->recnormalfreq== '"2"'):
                $apptdates[0]->recnormalfreq= "2nd";
           elseif($apptdates[0]->recnormalfreq== '"3"'):
                $apptdates[0]->recnormalfreq= "3rd";
           elseif($apptdates[0]->recnormalfreq== '"4"'):
                $apptdates[0]->recnormalfreq= "4th";
           elseif($apptdates[0]->recnormalfreq== '"5"'):
                $apptdates[0]->recnormalfreq= "5th";
           elseif($apptdates[0]->recnormalfreq== '"6"'):
                $apptdates[0]->recnormalfreq= '6th';
           elseif($apptdates[0]->recnormalfreq== '"0"'):
                $apptdates[0]->recnormalfreq= '';
           endif;
           // For rec day of week
            if($apptdates[0]->recday== '"1"' && $apptdates[0]->recnormalfreq == '' && $apptdates[0]->checkrecurbasedonstartdate == '"2"'):
                $apptdates[0]->recday= "1st";
           elseif($apptdates[0]->recday== '"2"'):
                $apptdates[0]->recday= "2nd";
           elseif($apptdates[0]->recday== '"3"'):
                $apptdates[0]->recday= "3rd";
           elseif($apptdates[0]->recday== '"4"'):
                $apptdates[0]->recday= "4th";
           elseif($apptdates[0]->recday== '"5"'):
                $apptdates[0]->recday= "Last";
           elseif($apptdates[0]->recday== '"0"' ):
                $apptdates[0]->recday= '';
           endif;
          // For recurring day
          if($apptdates[0]->recdayday== '"0"' && $apptdates[0]->recnormalfreq == '"0"'):
                $apptdates[0]->recdayday= "Sunday";
           elseif($apptdates[0]->recdayday== '"1"'):
                $apptdates[0]->recdayday= "Monday";
           elseif($apptdates[0]->recdayday== '"2"'):
                $apptdates[0]->recdayday= "Tuesday";
           elseif($apptdates[0]->recdayday== '"3"'):
                $apptdates[0]->recdayday= "Wednesday";
           elseif($apptdates[0]->recdayday== '"4"'):
                $apptdates[0]->recdayday= "Thursday";
           elseif($apptdates[0]->recdayday== '"5"'):
                $apptdates[0]->recdayday= 'Friday';
           elseif($apptdates[0]->recdayday== '"6"'):
                $apptdates[0]->recdayday= 'Saturday';
           else:
               $apptdates[0]->recdayday= "";
          endif;
           
           // For recurring normal day 
           if($apptdates[0]->recnormalday == '"0"' && $apptdates[0]->recnormalfreq !== '' && ($apptdates[0]->checkrecurbasedonstartdate !== '"2"')):
                $apptdates[0]->recnormalday = "day";
           elseif($apptdates[0]->recnormalday == '"4"'):
                $apptdates[0]->recnormalday = "workday";
           elseif($apptdates[0]->recnormalday == '"1"'):
                $apptdates[0]->recnormalday = "week";
           elseif($apptdates[0]->recnormalday == '"2"'):
                $apptdates[0]->recnormalday = "month";
           elseif($apptdates[0]->recnormalday == '"3"'):
                $apptdates[0]->recnormalday = "year";
           else:
                $apptdates[0]->recnormalday = '';
           endif;
          $appdatesres =  json_encode($apptdates); 
          echo $appdateresult = GibberishAES::enc($appdatesres, $key);
           
        }
        else
        {
            $appdatesres = '[{"id":"0"}]';
            echo $appdateresult = GibberishAES::enc($appdatesres, $key);
        }
	}catch(PDOException $e)
	{
		$error = '{"error":{"text":'. $e->getMessage() .'}}'; 
                echo $appdateresult = GibberishAES::enc($error, $key);
	}
}
function cb($a, $b) {
    return strtotime($a->datevalue) - strtotime($b->datevalue);
}
//To get list of appointment dates
function getAppointmentDates($loginProvderId)
{
	try
	{	
        $db=getConnection();

        $key = 'rotcoderaclla';
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        $get_fuv = "SELECT visit_categories FROM tbl_allcare_facuservisit WHERE  `users`  REGEXP ('".":\"".$loginProvderId."\"')";
        $fuv_stmt = $db->prepare($get_fuv) ;
        $fuv_stmt->execute();
        $set_fuv = $fuv_stmt->fetchAll(PDO::FETCH_OBJ); 
        for($i = 0; $i<count($set_fuv); $i++){
           $array[] =  unserialize( $set_fuv[$i]->visit_categories);
        }
        $dataArray = array();
        for($j = 0; $j<count($array); $j++){
            foreach($array[$j] as $arraykey){
                 $dataArray[] = $arraykey;
            }
        }
        $enc_val = '';
        $dataarray = array_unique($dataArray);
        foreach($dataarray as $arrayval){
            $enc_val .= $arrayval.",";
        }
        $enc_value = rtrim($enc_val,",");

        /*$sql="select DATE_FORMAT(ope.pc_eventdate,'%m-%d-%Y') as datevalue from openemr_postcalendar_events ope where ope.pc_aid=$loginProvderId AND ope.pc_apptstatus='-' and ope.pc_eventdate > ( SELECT SUBDATE(now(), INTERVAL 1 week)) GROUP BY ope.pc_eventdate";*/
        //$sql="select DISTINCT DATE_FORMAT(ope.pc_eventdate,'%m-%d-%Y') as datevalue from openemr_postcalendar_events ope where ope.pc_aid=$loginProvderId AND ope.pc_eventdate > ( SELECT SUBDATE(now(), INTERVAL 1 week))";
        $sql="select DISTINCT DATE_FORMAT(ope.pc_eventdate,'%Y-%m-%d') as datevalue from openemr_postcalendar_events ope where ope.pc_aid=$loginProvderId AND ope.pc_catid IN ($enc_value) ORDER BY ope.pc_eventdate ";
        $stmt = $db->prepare($sql) ; 
        $stmt->execute();
        $apptdates = $stmt->fetchAll(PDO::FETCH_OBJ);  
        $date['datevalue'] = date("Y-m-d");
        $apptdates[] = (object)$date;
        usort($apptdates, 'cb');
        $apptdatesult = array();
        foreach($apptdates as $app_key => $app_value){
            $apptdatesult[$app_key]['datevalue'] = date('m-d-Y', strtotime($app_value->datevalue));
        }
//        echo "<pre>";print_r($apptdatesult); echo "</pre>";
        if($apptdatesult)
        {
            $appdateres =  json_encode(array_unique($apptdatesult,SORT_REGULAR)); 
            echo $appdateresult =  GibberishAES::enc($appdateres, $key);

        }
        else
        {
            $appdateres = '[{"id":"0"}]';
            echo $appdateresult =  GibberishAES::enc($appdateres, $key);
        }
	}catch(PDOException $e)
	{
		$error = '{"error":{"text":'. $e->getMessage() .'}}'; 
                echo $appdateresult =  GibberishAES::enc($error, $key);
	}
}

//To get the history details
function getHistoryDetails($patientId, $group_name2, $eid)
{
    try
    {	$newdemo = array();
        $dataarray = array();
        $group_name = str_replace('_', ' ', $group_name2);
        $db=getConnection();
        $apikey = 'rotcoderaclla';
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        $fieldnames = "select field_id, title,
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
                    END as data_type, seq, list_id 
                    from layout_options where form_id='HIS' and uor <> 0 AND group_name LIKE'%".$group_name."' order by seq";
        $db->query( "SET NAMES utf8"); 
        $stmt2 = $db->prepare($fieldnames); 
        $stmt2->execute();
        $fieldnamesresult = $stmt2->fetchAll(PDO::FETCH_OBJ);
        
        foreach($fieldnamesresult as $flname){
            $data = array();
            $data['data']['label'] = $flname->title;
            $data['data']['order'] = $flname->seq;
            $data['data']['field_id'] = $flname->field_id;
            $data['data']['data_type'] = $flname->data_type;
            
            $sqllabel = "SELECT $flname->field_id as ltitle FROM history_data WHERE pid=$patientId order by date desc limit 1";
            $db->query( "SET NAMES utf8"); 
            $stmt4 = $db->prepare($sqllabel); 
            $stmt4->execute();
            $sqllabelres = $stmt4->fetchAll(PDO::FETCH_OBJ);
            $data['data']['selected_list'] = $sqllabelres[0]->ltitle;
            
            if($flname->data_type == 'Smoking Status' || $flname->data_type == 'Lifestyle status'):
                $selectedlistarray2 = array();
                $selectedlistarray2 = explode("|", $sqllabelres[0]->ltitle);
            
                $data['data']['statusText'] = 'textbox';
                $data['data']['status']['statusheader'] = 'Status';
                $statustypes = array('current'=>'Current','quit' =>'Quit','never' => 'Never','not_applicable' => 'N/A');
                foreach($statustypes as $key => $stype){
                    $stypelist = array();
                    $stypelist['statuslabel']['sname'] = $stype;
                    $stypelist['statuslabel']['svalue'] = $key;
                    $stypelist['statuslabel']['statusControlType'] = 'radiobutton';
                    for($i=0; $i<count($selectedlistarray2); $i++){
                        if($selectedlistarray2[$i] == $key.$flname->field_id ):
                            $stypelist['statuslabel']['option_status']   = 'true';
                            break;
                        else:
                            $stypelist['statuslabel']['option_status']   = 'false';
                        endif;
                    }
                    if($key == 'quit'):
                        $datelist['datelabel'] = 'datefield';
                        $datelist['dateControlType'] = 'textbox';
                        $stypelist[$key] = $datelist;
                    endif;
                    $data['data']['status'][] = $stypelist;
                }
                
                if($flname->data_type == 'Smoking Status' && $flname->list_id != ''):
                    $lists = array();
                    $listnamessql2 = "SELECT * FROM list_options WHERE list_id = '$flname->list_id'";
                    $db->query( "SET NAMES utf8"); 
                    $stmt5 = $db->prepare($listnamessql2); 
                    $stmt5->execute();
                    $listnamesresult2 = $stmt5->fetchAll(PDO::FETCH_OBJ);
                    foreach($listnamesresult2 as $list4){
                        $lists['smokinglist'][$list4->option_id] = $list4->title;
                        
                        for($i=0; $i<count($selectedlistarray2); $i++){
                            if($selectedlistarray2[$i] == $list4->option_id):
                                $lists['option_selected'] = $list4->option_id;
                                break;
                            endif;
                        }
                    }
                    $data['data']['smokingList'] = $lists;
                    $data['data']['smokingList']['listcontrolType'] = 'List box';
                elseif($flname->data_type == 'Smoking Status' && $flname->list_id == ''):
                    $data['data']['smokingList'] = array();
                    $data['data']['smokingList']['listcontrolType'] = 'List box';
               endif;
            elseif($flname->data_type == 'Exam results'):
                $data['data']['examresults']['examslist'] = 'testlist';
                $selecttypes = array(0=>'N/A',1 =>'Nor',2 => 'Abn');
                foreach($selecttypes as $key => $sltype){
                    $sltypelist = array();
                    $sltypelist['statuslabel']['sname'] = $sltype;
                    $sltypelist['statuslabel']['svalue'] = $key;
                    $sltypelist['statuslabel']['statusControlType'] = 'radiobutton';
                    $data['data']['status'][] = $sltypelist;
                }
                $textfield['statuslabel']['textlabel'] = 'Date/Notes';
                $textfield['statuslabel']['controltype'] = 'Textbox';
                $data['data']['status'][] = $textfield;
            endif;
            
            
            
            $list = array();
            if($flname->list_id != '' && $flname->data_type != 'Smoking Status'):
                $listnamessql = "SELECT * FROM list_options WHERE list_id = '$flname->list_id'";
                $db->query( "SET NAMES utf8"); 
                $stmt3 = $db->prepare($listnamessql); 
                $stmt3->execute();
                $listnamesresult = $stmt3->fetchAll(PDO::FETCH_OBJ);
                $selectedlistarray = array();
                $selectedlistarray = explode("|", $sqllabelres[0]->ltitle);
                if($flname->data_type == 'Exam results'):
                    foreach($listnamesresult as $list2){
                        $testarray = array();
                        $listarray['option_id'] = $list2->option_id;
                        $listarray['option_title']   = $list2->title;
                        foreach($selectedlistarray as $select){
                            $testarray = explode(":", $select);
                            if($testarray[0] == $list2->option_id):
                                $listarray['option_status']   = $testarray[1];
                                $listarray['option_notes']   = $testarray[2];
                                break;
                            endif;
                        }
                        $list[] = $listarray;
                        
                    }
                else:
                    foreach($listnamesresult as $list2){
                        $listarray['option_id'] = $list2->option_id;
                        $listarray['option_title']   = $list2->title;
                        for($i=0; $i<count($selectedlistarray); $i++){
                            if($selectedlistarray[$i] == $list2->option_id ):
                                $listarray['option_status']   = 'true';
                                break;
                            else:
                                $listarray['option_status']   = 'false';
                            endif;
                        }
                        $list[] = $listarray;
                    }
                endif;
                $data['data']['list'] = $list;
                
            else:
                $data['data']['list'] = $list;
            endif;
            
            $dataarray[] = $data;
        }
        
        $get_form = "SELECT form_id FROM forms WHERE pid = $patientId AND formdir = 'LBF2' and deleted = 0 and encounter = $eid order by date asc limit 0,1";
        $db->query( "SET NAMES utf8"); 
        $stmt8 = $db->prepare($get_form); 
        $stmt8->execute();
        $set_form = $stmt8->fetchAll(PDO::FETCH_OBJ);
        if(!empty ($set_form)){
            $form_id = $set_form[0]->form_id;
        }else{
            $form_id = 0;
        }   
        $dataarray[]['form_id'] = $form_id;
        if($group_name == 'Past Medical History'){
            $search = 'history_past_stat';
        }elseif($group_name ==  'Family History'){
            $search = 'family_history_stat';
        }elseif($group_name ==  'Primary Family Med Conditions'){
            $search = 'family_med_con_stat';
        }elseif($group_name ==  'Social History'){
            $search = 'history_social_stat';
        }elseif($group_name ==  'Tests and Exams'){
            $search = 'family_exam_test_stat';
        }
        
if(!empty($search)){
        $sql6 = "SELECT li.title,li.option_id, field_id,CASE data_type
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
                    END as data_type FROM layout_options lo INNER JOIN  list_options li ON  li.list_id = lo.list_id WHERE lo.field_id ='$search'";
        $stmt6 = $db->prepare($sql6) ;
        $stmt6->execute();  
        $phistory = $stmt6->fetchAll(PDO::FETCH_OBJ); 
        if(!empty($phistory)){
            foreach($phistory  as $his){
                $get_status_value = "SELECT * FROM lbf_data where form_id = '$form_id' and field_id = '$search' and field_value LIKE '%$his->option_id%'";
                $stmt7 = $db->prepare($get_status_value);
                $stmt7->execute();  
                $set_status_value = $stmt7->fetchAll(PDO::FETCH_OBJ); 
                if(!empty($set_status_value))
                    $his->option_status = 'true';
                else
                    $his->option_status = 'false';
            }
        }
        //echo "<pre>"; print_r($phistory);echo "</pre>";
        $dataarray[]['Status'] = $phistory;
        }        
//        echo "<pre>"; print_r($dataarray);echo "</pre>";
        //$result = utf8_encode_recursive($dataarray);
        $newdemo1 = encode_demo($dataarray);  
        $newdemo['HistoryData'] = check_data_available($newdemo1); 
        //exit();
        if($newdemo){
           $hisres2 = json_encode($newdemo);
           echo $historyresult =  GibberishAES::enc($hisres2, $apikey);

        }else{
             $hisres = '[{"id":"0"}]';
            echo $historyresult =  GibberishAES::enc($hisres, $apikey);
        }
    }catch(PDOException $e)
    {
         $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo $historyresult =  GibberishAES::enc($error, $apikey);
    }
}

//To get Issues
function getIssues($patientId)
{
    $key = 'rotcoderaclla';
    // The default key size is 256 bits.
    $old_key_size = GibberishAES::size();
    GibberishAES::size(256);
    try 
        {
            $db = getConnection();    

            //To get Medical Problems
            $sql1 = "SELECT id,DATE_FORMAT( date, '%Y-%m-%d' ) as date,REPLACE(type,'_',' ') as type,title,begdate as Begin_Date,enddate as End_Date,returndate as Return_Date,occurrence,classification,referredby as Referred_By,extrainfo as Additional_Information,diagnosis,activity,comments,user,groupname,outcome,destination,reinjury_id,injury_part,injury_type,injury_grade,reaction,external_allergyid,erx_source,erx_uploaded,DATE_FORMAT( modifydate, '%Y-%m-%d' ) as modified_Date  FROM lists WHERE pid = :patientId AND type = 'medical_problem' ORDER BY begdate";

            //To get Allergies
            $sql2 = "SELECT id,DATE_FORMAT( date, '%Y-%m-%d' ) as date,REPLACE(type,'_',' ') as type,title,begdate as Begin_Date,enddate as End_Date,returndate as Return_Date,occurrence,classification,referredby as Referred_By,extrainfo as Additional_Information,diagnosis,activity,comments,user,groupname,outcome,destination,reinjury_id,injury_part,injury_type,injury_grade,reaction,external_allergyid,erx_source,erx_uploaded,DATE_FORMAT( modifydate, '%Y-%m-%d' ) as modified_Date  FROM lists WHERE pid = :patientId AND type = 'allergy' ORDER BY begdate";

            //To get Medications
            $sql3 = "SELECT id,DATE_FORMAT( date, '%Y-%m-%d' ) as date,REPLACE(type,'_',' ') as type,title,begdate as Begin_Date,enddate as End_Date,returndate as Return_Date,occurrence,classification,referredby as Referred_By,extrainfo as Additional_Information,diagnosis,activity,comments,user,groupname,outcome,destination,reinjury_id,injury_part,injury_type,injury_grade,reaction,external_allergyid,erx_source,erx_uploaded,DATE_FORMAT( modifydate, '%Y-%m-%d' ) as modified_Date  FROM lists WHERE pid = :patientId AND type = 'medication' ORDER BY begdate";

            //To get Surgeries
            $sql4 = "SELECT id,DATE_FORMAT( date, '%Y-%m-%d' ) as date,REPLACE(type,'_',' ') as type,title,begdate as Begin_Date,enddate as End_Date,returndate as Return_Date,occurrence,classification,referredby as Referred_By,extrainfo as Additional_Information,diagnosis,activity,comments,user,groupname,outcome,destination,reinjury_id,injury_part,injury_type,injury_grade,reaction,external_allergyid,erx_source,erx_uploaded,DATE_FORMAT( modifydate, '%Y-%m-%d' ) as modified_Date FROM lists WHERE pid = :patientId AND type = 'surgery' ORDER BY begdate";

            //To get Dental Issues 
            $sql5 = "SELECT id,DATE_FORMAT( date, '%Y-%m-%d' ) as date,REPLACE(type,'_',' ') as type,title,begdate as Begin_Date,enddate as End_Date,returndate as Return_Date,occurrence,classification,referredby,extrainfo,diagnosis,activity,comments,user,groupname,outcome,destination,reinjury_id,injury_part,injury_type,injury_grade,reaction,external_allergyid,erx_source,erx_uploaded,DATE_FORMAT( modifydate, '%Y-%m-%d' ) as modified_Date FROM lists WHERE pid = :patientId AND type = 'dental' ORDER BY begdate";

            //To get Immunizations
            $sql6 = "select i1.id as id, i1.immunization_id as immunization_id, i1.cvx_code as cvx_code, c.code_text_short as cvx_text, ".
             " if (i1.administered_date, concat(i1.administered_date,' - '), substring(i1.note,1,20)) as immunization_data ".
             " from immunizations i1 ".
             " left join code_types ct on ct.ct_key = 'CVX' ".
             " left join codes c on c.code_type = ct.ct_id AND i1.cvx_code = c.code ".
             " where i1.patient_id = :patientId ".
             " and i1.added_erroneously = 0".
             " order by i1.administered_date desc";

            //To get Prescriptions
            $sql7 = "select id,filled_by_id,pharmacy_id,date_added,date_modified,(select CONCAT(fname, ' ',lname) as Provider from users where id=provider_id) as Provider,start_date,drug,drug_id,rxnorm_drugcode,form,dosage,quantity,size,unit,route,substitute,refills,per_refill,filled_date,medication,note,active,site,prescriptionguid,erx_source,erx_uploaded,drug_info_erx from prescriptions where patient_id=:patientId and active='1'";
            
            //To get DME Issues 
            $sql8 = "SELECT id,DATE_FORMAT( date, '%Y-%m-%d' ) as date,type,title,begdate as Begin_Date,enddate as End_Date,returndate as Return_Date,occurrence,classification,referredby,extrainfo,diagnosis,activity,comments,user,groupname,outcome,destination,reinjury_id,injury_part,injury_type,injury_grade,reaction,external_allergyid,erx_source,erx_uploaded,DATE_FORMAT( modifydate, '%Y-%m-%d' ) as modified_Date FROM lists WHERE pid = :patientId AND type = 'DME' ORDER BY begdate";

            $demo1=sql_execute($patientId,$sql1);
            $demo2=sql_execute($patientId,$sql2);
            $demo3=sql_execute($patientId,$sql3);
            $demo4=sql_execute($patientId,$sql4);
            $demo5=sql_execute($patientId,$sql5);
            $demo6=sql_execute($patientId,$sql6);
            $demo7=sql_execute($patientId,$sql7);
            $demo8=sql_execute($patientId,$sql8);

            $newdemo1=encode_demo($demo1);
            $newdemo2=encode_demo($demo2);  
            $newdemo3=encode_demo($demo3);  
            $newdemo4=encode_demo($demo4);              
            $newdemo5=encode_demo($demo5);  
            $newdemo6=encode_demo($demo6);  
            $newdemo7=encode_demo($demo7);  
            $newdemo8=encode_demo($demo8);  

            $newdemo['Medical_Problems'] = check_data_available($newdemo1);
            $newdemo['Allergies'] = check_data_available($newdemo2);
            $newdemo['Medications'] = check_data_available($newdemo3);
            $newdemo['Surgeries'] = check_data_available($newdemo4);
            $newdemo['Dental_Issues'] = check_data_available($newdemo5);
            $newdemo['Immunizations'] = check_data_available($newdemo6);
            $newdemo['Prescriptions'] = check_data_available($newdemo7);
            $newdemo['DME'] = check_data_available($newdemo8);

            $newdemores =  json_encode($newdemo);
            echo $newdemoresult = GibberishAES::enc($newdemores, $key);

        } 
        catch(PDOException $e) 
        {
            $newdemores = '{"error":{"text":'. $e->getMessage() .'}}';
            echo $newdemoresult = GibberishAES::enc($newdemores, $key);
        }
}

//To get Occurrence list
function getOccurrenceList()
{
    $key = 'rotcoderaclla';
    // The default key size is 256 bits.
    $old_key_size = GibberishAES::size();
    GibberishAES::size(256);
    try
    {	
        $db=getConnection();
        $sql="select option_id, title from list_options where list_options.list_id='Occurrence'";
        $stmt = $db->prepare($sql); 
        $stmt->execute();
        $occlist = $stmt->fetchAll(PDO::FETCH_OBJ);           

        if($occlist)
        {
            $occlistenc = json_encode($occlist); 
            echo $occlistencresult = GibberishAES::enc($occlistenc, $key);
        }
        else
        {
            $occlistenc = '[{"id":"0"}]';
            echo $occlistencresult = GibberishAES::enc($occlistenc, $key);
        }
    }catch(PDOException $e)
    {
            $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
            echo $occlistencresult = GibberishAES::enc($error, $key);
    }
}

//To get Outcome list
function getOutcomeList()
{
    try
    {	
        $db=getConnection();
        $key = 'rotcoderaclla';
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        $sql="select option_id, title from list_options where list_options.list_id='Outcome'";
        $stmt = $db->prepare($sql) ; 
        $stmt->execute();
        $outcomelist = $stmt->fetchAll(PDO::FETCH_OBJ);           

        if($outcomelist)
        {
            $outcomeres = json_encode($outcomelist); 
            echo $outcomeresult = GibberishAES::enc($outcomeres, $key);
        }
        else
        {
            $outcomeres = '[{"id":"0"}]';
            echo $outcomeresult = GibberishAES::enc($outcomeres, $key);
        }
    }catch(PDOException $e)
    {
            $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
            echo $outcomeresult = GibberishAES::enc($error, $key);
    }
}

//To update Issues
function updateIssues()
{
    try
    {
        $db = getConnection();
        $flag=0;
        $key = 'rotcoderaclla';
        $request = Slim::getInstance()->request();
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        $updateissueresult =  GibberishAES::dec($request->getBody(), $key);
        $issueArray = json_decode($updateissueresult,TRUE);
        //print_r($issueArray);
			
        $id=$issueArray['id'];
        $pid=$issueArray['pid'];
        //$loginProvderId=$issueArray['loginProviderId'];
        $type=$issueArray['type'];
        $title=$issueArray['title'];
        $diagnosis=$issueArray['diagnosis'];
        $begdate=$issueArray['begdate'];
        $enddate=$issueArray['enddate'];
        $occurrence=$issueArray['occurrence'];
        $referredby=$issueArray['referredby'];
        $outcome=$issueArray['outcome'];
        $destination=$issueArray['destination'];
        
        
		if($id==0)
		{
			$insert_issues_Sql="INSERT INTO lists(pid,type,title,diagnosis,begdate,enddate,occurrence,referredby,outcome,destination,modifydate)
										   VALUES (:pid,:type,:title,:diagnosis,:begdate,:enddate,:occurrence,:referredby,:outcome,:destination,NOW())";

			$q = $db->prepare($insert_issues_Sql);

			if($q->execute(array( ':pid'=>$pid,':type'=>$type,
					   ':title'=>$title,':diagnosis'=>$diagnosis,
					  ':begdate'=>$begdate,':enddate'=>$enddate,':occurrence'=>$occurrence,':referredby'=>$referredby,':outcome'=>$outcome,':destination'=>$destination
			)))
			{                       		
				$sqlGetMaxid="SELECT MAX(id) as issue_id FROM lists";
				$stmtGetMaxid = $db->prepare($sqlGetMaxid) ;           
				//$stmt->bindParam("date_today", $date_today);            
				$stmtGetMaxid->execute();
				$maxid = $stmtGetMaxid->fetchAll(PDO::FETCH_OBJ);
				$resmaxid = '[{"id":$maxid}]';
                                echo $resmaxidres = GibberishAES::enc($resmaxid, $key);
                                
			}
			else
			{
				echo '[{"id":"0"}]';                 
			}
		}
		else
		{
			$update_issues_Sql="UPDATE lists
			SET
			type=:type,
			title=:title,
			diagnosis=:diagnosis,
			begdate=:begdate,
			enddate=:enddate,
			occurrence=:occurrence,
			referredby=:referredby,
			outcome=:outcome,
			destination=:destination,
			modifydate=NOW()
			WHERE id=:id";
			
			$q = $db->prepare($update_issues_Sql);

			if($q->execute(array( ':id'=>$id,':pid'=>$pid,':type'=>$type,
					  ':title'=>$title,':diagnosis'=>$diagnosis,
					  ':begdate'=>$begdate,':enddate'=>$enddate,':occurrence'=>$occurrence,':referredby'=>$referredby,':outcome'=>$outcome,':destination'=>$destination
			)))
			{  
				echo '[{"id":"1"}]';
			}
			else
			{
				echo '[{"id":"0"}]';
			}
		}
  
    }
    catch(PDOException $e) 
    {      
		echo '{"error":{"text":'. $e->getMessage() .'}}'; 
    }   
}

//To get medical problem issue list
function getMedProbIssueList()
{
	try
	{	
        $db=getConnection();
        $key = 'rotcoderaclla';
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        $sql="select option_id, title from list_options where list_options.list_id='medical_problem_issue_list'";
        $stmt = $db->prepare($sql) ; 
        $stmt->execute();
        $medprobissuelist = $stmt->fetchAll(PDO::FETCH_OBJ);           

        if($medprobissuelist)
        {
            $medprobres = json_encode($medprobissuelist); 
            echo $medprobresult = GibberishAES::enc($medprobres, $key);
            
        }
        else
        {
            $medprobres = '[{"id":"0"}]';
            echo $medprobresult = GibberishAES::enc($medprobres, $key);
        }
	}catch(PDOException $e)
	{
		$error = '{"error":{"text":'. $e->getMessage() .'}}'; 
                echo $medprobresult = GibberishAES::enc($error, $key);
	}
}

//To get allergy issue list
function getAllergyIssueList()
{
    try
    {	
        $db=getConnection();
        $key = 'rotcoderaclla';
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        $sql="select option_id, title from list_options where list_options.list_id='allergy_issue_list'";
        $stmt = $db->prepare($sql) ; 
        $stmt->execute();
        $allergyissuelist = $stmt->fetchAll(PDO::FETCH_OBJ);           

        if($allergyissuelist)
        {
            $allegryres = json_encode($allergyissuelist); 
            echo $allegryresult = GibberishAES::enc($allegryres, $key);
        }
        else
        {
            $allegryres = '[{"id":"0"}]';
            echo $allegryresult = GibberishAES::enc($allegryres, $key);
        }
    }catch(PDOException $e)
    {
            $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
            echo $allegryresult = GibberishAES::enc($error, $key);
    }
}

//To get medication issue list
function getMedIssueList()
{
    try
    {	
        $db=getConnection();
        $key = 'rotcoderaclla';
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
       
        $sql="select option_id, title from list_options where list_options.list_id='medication_issue_list'";
        $stmt = $db->prepare($sql) ; 
        $stmt->execute();
        $medissuelist = $stmt->fetchAll(PDO::FETCH_OBJ);           

        if($medissuelist)
        {
            $medissueres = json_encode($medissuelist); 
            echo $medissueresult = GibberishAES::enc($medissueres, $key);
            
        }
        else
        {
            $medissueres = '[{"id":"0"}]';
            echo $medissueresult = GibberishAES::enc($medissueres, $key);
        }
    }catch(PDOException $e)
    {
            $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
            echo $allegryresult = GibberishAES::enc($error, $key);
    }
}

//To get surgery issue list
function getSurgeryIssueList()
{
	try
	{	
		$db=getConnection();
		$sql="select option_id, title from list_options where list_options.list_id='surgery_issue_list'";
		$stmt = $db->prepare($sql) ; 
        $stmt->execute();
        $surgeryissuelist = $stmt->fetchAll(PDO::FETCH_OBJ);           

        if($surgeryissuelist)
        {
            echo json_encode($surgeryissuelist); 
        }
        else
        {
            echo '[{"id":"0"}]';
        }
	}catch(PDOException $e)
	{
		echo '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
}

// To get Messages See All/Just Mine
function getMessages($loginProvderId)
{
	try 
        {
        $db = getConnection();
        
        $key = 'rotcoderaclla';
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        
		if($loginProvderId==0)
        {
			$sql="select pn.id as id, concat(us.fname, us.lname) as msg_from, pn.pid as pid,concat(pd.lname, pd.fname) as patient, pn.title as msg_type, pn.date, pn.message_status as status,pn.body as msg_content
			from pnotes pn inner join users us on pn.user=us.username
			inner join patient_data pd on pd.pid=pn.pid
			where pn.deleted=0 and activity = 1 order by pn.id desc";
			$db->query( "SET NAMES utf8"); 
			$stmt = $db->prepare($sql) ;
			$stmt->execute();
			$messages = $stmt->fetchAll(PDO::FETCH_OBJ);           
			
			if($messages)
			{
                                $str =  json_encode($messages); 
                                echo $messageenc = GibberishAES::enc($str, $key);
			}
			else
			{
				$str = '[{"id":"0"}]';
                                echo $messageenc = GibberishAES::enc($str, $key);
			}
		}
		else
		{
			$rs=getUserName($loginProvderId);
			if($rs)
			{
				$username = $rs[0]['username'];		
				$sql="select pn.id as id,concat(us.fname,' ',us.lname) as msg_from, pd.pid as pid,concat(pd.lname,' ',pd.fname) as patient, pn.title as msg_type, pn.date, pn.message_status as status,pn.body as msg_content
				from pnotes pn inner join users us on pn.user=us.username
				inner join patient_data pd on pd.pid=pn.pid
				where pn.assigned_to='$username' and pn.deleted=0 and activity = 1 order by pn.id desc";
                                $db->query( "SET NAMES utf8"); 
				$stmt = $db->prepare($sql) ;
				$stmt->execute();
				$messages = $stmt->fetchAll(PDO::FETCH_OBJ);           
			
				if($messages)
				{
					$str =  json_encode($messages); 
                                        echo $messageenc = GibberishAES::enc($str, $key);
				}
				else
				{
					$str = '[{"id":"0"}]';
                                        echo $messageenc = GibberishAES::enc($str, $key);
				}
			}
		}  
        
    } 
    catch(PDOException $e) 
    {
        $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo $messageenc = GibberishAES::enc($error, $key);
    }
}

function getReminders($loginProvderId,$days_to_show,$alerts_to_show)
{
    try 
    {
        $db = getConnection();
	
        $key = 'rotcoderaclla';
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        
	$sql="SELECT 
dr.pid, CONCAT(pd.fname,' ',pd.lname) AS patient_name, dr.dr_id, dr.dr_message_text,dr.dr_message_due_date, 
u.fname ffname, u.mname fmname, u.lname flname
FROM `dated_reminders` dr 
JOIN `users` u ON dr.dr_from_ID = u.id 
JOIN `dated_reminders_link` drl ON dr.dr_id = drl.dr_id 
JOIN `patient_data` pd ON dr.pid = pd.pid  
WHERE drl.to_id = $loginProvderId
AND dr.`message_processed` = 0
AND dr.`dr_message_due_date` < ADDDATE(NOW(), INTERVAL $days_to_show DAY)  AND pd.practice_status = 'YES' AND pd.deceased_stat != 'YES'
ORDER BY `dr_message_due_date` ASC , `message_priority` ASC LIMIT 0,$alerts_to_show";
			
	$stmt = $db->prepare($sql);
	$stmt->execute();
	$reminders = $stmt->fetchAll(PDO::FETCH_OBJ);           
	
	if($reminders)
	{
		$remiderres =  json_encode($reminders); 
                echo $reminderresult = GibberishAES::enc($remiderres, $key);
	}
	else
	{
		$remiderres = '[{"id":"0"}]';
                echo $reminderresult = GibberishAES::enc($remiderres, $key);
	}
		        
    } 
    catch(PDOException $e) 
    {
        $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo $reminderresult = GibberishAES::enc($error, $key);
    }
}


function getRemindersCount($loginProvderId,$days_to_show,$alerts_to_show)
{
    try 
    {
        $db = getConnection();
	
	$sql="SELECT COUNT(*) AS reminders_count FROM (SELECT 
dr.pid, CONCAT(pd.fname,' ',pd.lname) AS patient_name, dr.dr_id, dr.dr_message_text,dr.dr_message_due_date, 
u.fname ffname, u.mname fmname, u.lname flname
FROM `dated_reminders` dr 
JOIN `users` u ON dr.dr_from_ID = u.id 
JOIN `dated_reminders_link` drl ON dr.dr_id = drl.dr_id 
JOIN `patient_data` pd ON dr.pid = pd.pid  
WHERE drl.to_id = $loginProvderId
AND dr.`message_processed` = 0
AND dr.`dr_message_due_date` < ADDDATE(NOW(), INTERVAL $days_to_show DAY)  AND pd.practice_status = 'YES' AND pd.deceased_stat != 'YES'
ORDER BY `dr_message_due_date` ASC , `message_priority` ASC LIMIT 0,$alerts_to_show) AS reminders";
			
	$stmt = $db->prepare($sql);
	$stmt->execute();
	$reminders = $stmt->fetchAll(PDO::FETCH_OBJ);           
	
	if($reminders)
	{
		echo json_encode($reminders); 
	}
	else
	{
		echo '[{"id":"0"}]';
	}
		        
    } 
    catch(PDOException $e) 
    {
        echo '{"error":{"text":'. $e->getMessage() .'}}'; 
    }
}



// Reminders Completed

function setReminderAsProcessed($reminderId,$loginProvderId)
{

    try 
    {
        $db = getConnection();

	$rID=$reminderId;
	$userID = $loginProvderId;    

  	if(is_numeric($rID) and $rID > 0)
	{  
// --- check if this user can remove this message
// --- need a better way of checking the current user, I don't like using $_SESSION for checks
	      $sql = "SELECT count(dr.dr_id) AS cnt FROM `dated_reminders` dr JOIN `dated_reminders_link` drl ON dr.dr_id = drl.dr_id WHERE drl.to_id = '$userID' AND dr.`dr_id` = '$rID' LIMIT 0,1"; 
	      
		$stmt = $db->prepare($sql);
		$stmt->execute();
		$rdrRow = $stmt->fetchAll(PDO::FETCH_OBJ); 
//  		print_r($rdrRow);
// --- if this user can delete this message (ie if it was sent to this user)
		
		//if($rdrRow['c'] == 1)
		if($rdrRow[0]->cnt == 1)
		{  
		// ----- update the data, set the message to proccesses		

			$sql1 = "UPDATE `dated_reminders` SET  `message_processed` = 1, `processed_date` = NOW(), `dr_processed_by` = '".intval($userID)."' WHERE `dr_id` = '".intval($rID)."' ";  
		      
			$stmt1 = $db->prepare($sql1);

		        if($stmt1->execute())
		        {
		            echo '[{"id":"1"}]';
		        }
		        else
		        {
		            echo '[{"id":"-1","msg":"Update failed"}]';
		        }	
		      
		}
		else
		{
			echo '[{"id":"0"}]';
		}
   	}
    }

    catch(PDOException $e) 
    {
        echo '{"error":{"text":'. $e->getMessage() .'}}'; 
    }

} 


// To get users list to send messages to
function getMsgToUsers()
{
	try 
    {
        $db = getConnection();
        $key = 'rotcoderaclla';
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        //$sql="SELECT id, concat(users.fname,' ', users.lname) as user from users where users.active=1";
        //$sql="SELECT id, concat(users.fname,' ', users.lname) as user from users where users.active=1 and (fname <> '' or lname <> '')";
        $sql = "SELECT id,  concat(users.fname,' ', users.lname) as user FROM users "
                . "WHERE username != '' AND active = 1 AND "
                . "( info IS NULL OR info NOT LIKE '%Inactive%' )"
                . "ORDER BY fname, lname";
        $db->query( "SET NAMES utf8"); 
        $stmt = $db->prepare($sql) ; 
        $stmt->execute();
        $msgtousers = $stmt->fetchAll(PDO::FETCH_OBJ);           

        if($msgtousers)
        {
            $msgtouserresult = json_encode($msgtousers); 
            echo $msgenc = GibberishAES::enc($msgtouserresult, $key);
        }
        else
        {
            $msgtouserresult = '[{"id":"0"}]';
            echo $msgenc = GibberishAES::enc($msgtouserresult, $key);
        }
    } 
    catch(PDOException $e) 
    {
        $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo $msgenc = GibberishAES::enc($error, $key);
    }
}

// To get Active/Inactive Messages
function getActiveMessages($loginProvderId, $isActive)
{
	$db = getConnection();
        $key = 'rotcoderaclla';
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
	$rs=getUserName($loginProvderId);
	if($rs)
	{
		$username = $rs[0]['username'];	
		//Get Active Messages
		if($isActive==1)
		{	
			$sql="select concat(us.fname,' ',us.lname) as msg_from, concat(pd.lname,' ',pd.fname) as patient, pn.title as msg_type, pn.date, pn.message_status as status, pn.body as msg_content
			from pnotes pn inner join users us on pn.user=us.username
			inner join patient_data pd on pd.pid=pn.pid
			where pn.assigned_to='$username' and pn.deleted=0 and pn.message_status!='Done'  AND pd.practice_status = 'YES' AND pd.deceased_stat != 'YES'";
		}
		//Get Inactive Messages
		else
		{
			$sql="select concat(us.fname,' ',us.lname) as msg_from, concat(pd.lname,' ',pd.fname) as patient, pn.title as msg_type, pn.date, pn.message_status as status, pn.body as msg_content
			from pnotes pn inner join users us on pn.user=us.username
			inner join patient_data pd on pd.pid=pn.pid
			where pn.assigned_to='$username' and pn.deleted=0 and pn.message_status='Done'  AND pd.practice_status = 'YES' AND pd.deceased_stat != 'YES'";
		}
			
		$stmt = $db->prepare($sql) ;
		$stmt->execute();
		$messages = $stmt->fetchAll(PDO::FETCH_OBJ);           
			
		if($messages)
		{
			$string = json_encode($messages); 
                        echo $stringenc = GibberishAES::enc($string, $key);
		}
		else
		{
			$string = '[{"id":"0"}]';
                        echo $stringenc = GibberishAES::enc($string, $key);
		}
	}
}

function getActiveMessagesCount($loginProvderId, $isActive)
{
	$db = getConnection();
	$rs=getUserName($loginProvderId);
	if($rs)
	{
		$username = $rs[0]['username'];	
		//Get Active Messages
		if($isActive==1)
		{	
			$sql="SELECT COUNT(*) AS active_messages_count 
FROM (select concat(us.fname,' ',us.lname) as msg_from, concat(pd.lname,' ',pd.fname) as patient, pn.title as msg_type, pn.date, pn.message_status as status, pn.body as msg_content
			from pnotes pn inner join users us on pn.user=us.username
			inner join patient_data pd on pd.pid=pn.pid
			where pn.assigned_to='$username' and pn.deleted=0 and pn.message_status!='Done') AS activeMessages ";
		}
		//Get Inactive Messages
		else
		{
			$sql="SELECT COUNT(*) AS active_messages_count 
FROM (select concat(us.fname,' ',us.lname) as msg_from, concat(pd.lname,' ',pd.fname) as patient, pn.title as msg_type, pn.date, pn.message_status as status, pn.body as msg_content
			from pnotes pn inner join users us on pn.user=us.username
			inner join patient_data pd on pd.pid=pn.pid
			where pn.assigned_to='$username' and pn.deleted=0 and pn.message_status='Done') AS activeMessages";
		}
			
		$stmt = $db->prepare($sql) ;
		$stmt->execute();
		$messages = $stmt->fetchAll(PDO::FETCH_OBJ);           
			
		if($messages)
		{
			echo json_encode($messages); 
		}
		else
		{
			echo '[{"id":"0"}]';
		}
	}
}


// To get Message Status dropdown
function getMsgStatuses()
{
	try 
    {
        $db = getConnection();
        
        $key = 'rotcoderaclla';
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        
        $sql="select seq as id,title from list_options where list_id = 'message_status' order by seq";
    
        $stmt = $db->prepare($sql) ; 
        $stmt->execute();
        $msgstatuses = $stmt->fetchAll(PDO::FETCH_OBJ);           

        if($msgstatuses)
        {
            $msgstatresult  =  json_encode($msgstatuses); 
            echo $stringenc = GibberishAES::enc($msgstatresult, $key);
        }
        else
        {
            $msgstatresult = '[{"id":"0"}]';
            echo $stringenc = GibberishAES::enc($msgstatresult, $key);
        }
    } 
    catch(PDOException $e) 
    {
        $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo $stringenc = GibberishAES::enc($error, $key);
    }
}

// To get Patient Note Type dropdown
function getMsgNoteTypes()
{
    try 
    {
        $db = getConnection();
        
        $key = 'rotcoderaclla';
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        
        $sql="select seq as id,title from list_options where list_id = 'note_type' order by seq";
    
        $stmt = $db->prepare($sql) ; 
        $stmt->execute();
        $pnotetypes = $stmt->fetchAll(PDO::FETCH_OBJ);           

        if($pnotetypes)
        {
            $noteres =  json_encode($pnotetypes); 
            echo $noteresult = GibberishAES::enc($noteres, $key);
        }
        else
        {
            $noteres = '[{"id":"0"}]';
            echo $noteresult = GibberishAES::enc($noteres, $key);
        }
    } 
    catch(PDOException $e) 
    {
        $error = '{"error":{"text":'. $e->getMessage() .'}}';
        echo $noteresult = GibberishAES::enc($error, $key);
        
    }
}


function getDemographicsBilling($pid, $with_insurance=false)
{
    try
    {
        $db = getConnection();
        $key = 'rotcoderaclla';
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);

          $balance = 0;
          $feres = "SELECT date, encounter, last_level_billed, " .
            "last_level_closed, stmt_count " .
            "FROM form_encounter WHERE pid = $pid";
          $stmt = $db->prepare($feres) ; 
          $stmt->execute();
          $ferow = $stmt->fetchAll(PDO::FETCH_OBJ); 
          $patientbalance = 0;
          $insurnace_balance = 0;
          if(!empty($ferow)){
            
            
            for($j=0; $j<2; $j++){
                if($j == 0)
                    $with_insurance_data = 1;
                else
                    $with_insurance_data = '';
                $balance = 0;
                
                for($i=0; $i<count($ferow); $i++){
                    $encounter = $ferow[$i]->encounter;
                    $dos = substr($ferow[$i]->date, 0, 10);
                    $insarr = array();
                    foreach (array('primary','secondary','tertiary') as $instype) {
                        $tmp2 = "SELECT * FROM insurance_data " .
                          "WHERE pid = $pid AND type = '$instype' " .
                          "AND date <= '$dos' ORDER BY date DESC LIMIT 1";
                        $stmttemp = $db->prepare($tmp2) ; 
                        $stmttemp->execute();
                        $tmp = $stmttemp->fetchAll(PDO::FETCH_OBJ);
                        if (empty($tmp[0]->provider)) break;
                          $insarr[] = $tmp[0];
                    }

                    $inscount = count($insarr);

                    if ( $with_insurance_data && $ferow[$i]->last_level_closed < $inscount && $ferow[$i]->stmt_count == 0) {
                      // It's out to insurance so only the co-pay might be due.
                      $brow2 = "SELECT SUM(fee) AS amount FROM billing WHERE " .
                        "pid = $pid AND encounter = $encounter AND " .
                        "code_type = 'copay' AND activity = 1";
                      $stmt2 = $db->prepare($brow2) ; 
                      $stmt2->execute();
                      $brow = $stmt2->fetchAll(PDO::FETCH_OBJ); 

                      if(!empty($brow[0]->amount))
                          $brow_bal = $brow[0]->amount; 
                      else
                          $brow_bal = '';

                      $drow2 = "SELECT SUM(pay_amount) AS payments " .
                        "FROM ar_activity WHERE " .
                        "pid = $pid AND encounter = $encounter AND payer_type = 0";
                      $stmt3 = $db->prepare($drow2) ; 
                      $stmt3->execute();
                      $drow = $stmt3->fetchAll(PDO::FETCH_OBJ);

                      if(!empty($drow[0]->payments))
                          $drow_bal = $drow[0]->payments; 
                      else
                          $drow_bal = '';

                      $ptbal = $insarr[0]->copay + $brow_bal - $drow_bal;
                      if ($ptbal > 0) $balance += $ptbal;
                    }
                    else {
                      // Including insurance or not out to insurance, everything is due.
                       $brow2 = "SELECT SUM(fee) AS amount FROM billing WHERE " .
                        "pid = $pid AND encounter = $encounter AND " .
                        "activity = 1";
                      $stmt2 = $db->prepare($brow2) ; 
                      $stmt2->execute();
                      $brow = $stmt2->fetchAll(PDO::FETCH_OBJ); 
                      if(!empty($brow[0]->amount))
                          $brow_bal = $brow[0]->amount; 
                      else
                          $brow_bal = '';

                      $drow2 = "SELECT SUM(pay_amount) AS payments, " .
                        "SUM(adj_amount) AS adjustments FROM ar_activity WHERE " .
                        "pid = $pid AND encounter = $encounter";
                      $stmt3 = $db->prepare($drow2) ; 
                      $stmt3->execute();
                      $drow = $stmt3->fetchAll(PDO::FETCH_OBJ);

                      if(!empty($drow[0]->payments))
                          $drow_bal = $drow[0]->payments; 
                      else
                          $drow_bal = '';

                      // adjustments
                      if(!empty($drow[0]->adjustments))
                          $drow_bal2 = $drow[0]->adjustments; 
                      else
                          $drow_bal2 = '';

                      $srow2 = "SELECT SUM(fee) AS amount FROM drug_sales WHERE " .
                        "pid = $pid AND encounter = $encounter";
                      $stmt4 = $db->prepare($srow2) ; 
                      $stmt4->execute();
                      $srow = $stmt4->fetchAll(PDO::FETCH_OBJ);
                      if(!empty($srow[0]->amount))
                          $srow_bal = $srow[0]->amount; 
                      else
                          $srow_bal = '';
//                      echo $brow_bal."=".$srow_bal."=".$drow_bal."=".$drow_bal2."<br>";
                      $balance += $brow_bal +$srow_bal
                        -  $drow_bal -  $drow_bal2;
                    }
                  }
                  if($j ==  0)
                    $patientbalance = sprintf('%01.2f', $balance);
                  else if($j ==  1)
                    $insurnace_balance = sprintf('%01.2f', $balance)- $patientbalance;
              }
          }
          // customer
        /*}
        else if ($GLOBALS['oer_config']['ws_accounting']['enabled']) {
          // require_once($GLOBALS['fileroot'] . "/library/classes/WSWrapper.class.php");
          $conn = $GLOBALS['adodb']['db'];
          $customer_info['id'] = 0;
          $sql = "SELECT foreign_id FROM integration_mapping AS im " .
            "LEFT JOIN patient_data AS pd ON im.local_id = pd.id WHERE " .
            "pd.pid = '" . $pid . "' AND im.local_table = 'patient_data' AND " .
            "im.foreign_table = 'customer'";
          $result = $conn->Execute($sql);
          if($result && !$result->EOF) {
            $customer_info['id'] = $result->fields['foreign_id'];
          }
          $function['ezybiz.customer_balance'] = array(new xmlrpcval($customer_info,"struct"));
          $ws = new WSWrapper($function);
          if(is_numeric($ws->value)) {
            return sprintf('%01.2f', $ws->value);
          }
        }*/
          $insurnce_name = "SELECT (select name from insurance_companies where id = provider ) as provider, DATE_FORMAT(`date`,'%Y-%m-%d') as effdate FROM insurance_data WHERE type='primary' AND pid= $pid order by date DESC limit 1";
          $stmt_ins = $db->prepare($insurnce_name) ; 
          $stmt_ins->execute();
          $insu_data = $stmt_ins->fetchAll(PDO::FETCH_OBJ);
          $primary_insurance = $insu_data[0]->provider;
          $effective_date = $insu_data[0]->effdate;
          $totalbalance=$patientbalance + $insurnace_balance;
        //json_encode($feres);
         $bilres = '[{"id":"1","Patient_Balance_Due":"'.$patientbalance.'","Insurance_Balance_Due":"'.$insurnace_balance.'","Total_Balance_Due":"'.$totalbalance.'","Primary_Insurance":"'.$primary_insurance.'","Effective_Date":"'.$effective_date.'"}]';    
        echo $bilresult = GibberishAES::enc($bilres, $key);
          
    }
    catch(PDOException $e) 
    {
        $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo $bilresult = GibberishAES::enc($error, $key);
    }
}

function getClinicalReminders($pid)
{
	clinical_summary_widget($pid,"reminders-due");
}

function getChoicesProviderList()
{
        $key = 'rotcoderaclla';
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
	try
	{	
            $db=getConnection();
            /*$sql="select u.id,concat(u.fname,' ',u.lname) as provider from users u
            inner join gacl_aro ga on u.username=ga.value
            inner join gacl_groups_aro_map ggam on ga.id=ggam.aro_id
            where u.authorized=1 and u.active=1 and ggam.group_id IN (11,13)";*/
            $sql="select DISTINCT  u.id,concat(u.fname,' ',u.lname) as provider from users u
            inner join gacl_aro ga on u.username=ga.value
            inner join gacl_groups_aro_map ggam on ga.id=ggam.aro_id
            where u.authorized=1 and u.active=1 ";
            $stmt = $db->prepare($sql) ; 
            $stmt->execute();
            $providerlist = $stmt->fetchAll(PDO::FETCH_OBJ);           

        if($providerlist)
        {
            $string = json_encode($providerlist); 
            echo $stringenc = GibberishAES::enc($string, $key);
        }
        else
        {
            $string = '[{"id":"0"}]';
            echo $stringenc = GibberishAES::enc($string, $key);
        }
	}catch(PDOException $e)
	{
		$error = '{"error":{"text":'. $e->getMessage() .'}}'; 
                echo $stringenc = GibberishAES::enc($error, $key);
	}
}

function getPharmacyList()
{
    $key = 'rotcoderaclla';
    // The default key size is 256 bits.
    $old_key_size = GibberishAES::size();
    GibberishAES::size(256);
    try
    {	
        $db=getConnection();
        $sql="select id, name from pharmacies";
        $stmt = $db->prepare($sql) ; 
        $stmt->execute();
        $pharmacylist = $stmt->fetchAll(PDO::FETCH_OBJ);           

        if($pharmacylist)
        {
            $pharmacyres = json_encode($pharmacylist); 
            echo $pharmacyresult = GibberishAES::enc($pharmacyres, $key);
            
        }
        else
        {
            $pharmacyres = '[{"id":"0"}]';
            echo $pharmacyresult = GibberishAES::enc($pharmacyres, $key);
        }
    }catch(PDOException $e)
    {
            $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
            echo $pharmacyresult = GibberishAES::enc($error, $key);
    }
}

function getCountryStateList()
{
        $key = 'rotcoderaclla';
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
	try
	{	
        $db=getConnection();
        /*$sql="select option_id, title from list_options where list_id='country'";
        $stmt = $db->prepare($sql) ; 
        $stmt->execute();
        $countrylist = $stmt->fetchAll(PDO::FETCH_OBJ);           
        */
        $sql="select option_id, title from list_options where list_id=:list_id";
            
        $demo1 = sql_execute2('country',$sql);
        $demo2 = sql_execute2('state',$sql);
       
        $namelist1=encode_demo2($demo1);      
        $namelist2=encode_demo2($demo2);  
       
        $countrylist['Country'] = check_data_available($namelist1);
        $countrylist['State'] = check_data_available($namelist2);
      
        
        if($countrylist)
        {
            $countryres = json_encode($countrylist); 
            echo $countryresult = GibberishAES::enc($countryres, $key);
        }
        else
        {
            $countryres = '[{"id":"0"}]';
            echo $countryresult = GibberishAES::enc($countryres, $key);
        }
	}catch(PDOException $e)
	{
            $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
            echo $countryresult = GibberishAES::enc($error, $key);
	}
	
}

function getStateList()
{
	
    $key = 'rotcoderaclla';
    // The default key size is 256 bits.
    $old_key_size = GibberishAES::size();
    GibberishAES::size(256);
    try
    {	
        $db=getConnection();
        $sql="select option_id, title from list_options where list_id='state'";
        $stmt = $db->prepare($sql) ; 
        $stmt->execute();
        $statelist = $stmt->fetchAll(PDO::FETCH_OBJ);           

        if($statelist)
        {
            $stateres = json_encode($statelist);
            echo $stateresult = GibberishAES::enc($stateres, $key);
        }
        else
        {
            $stateres = '[{"id":"0"}]';
            echo $stateresult = GibberishAES::enc($stateres, $key);
        }
    }catch(PDOException $e)
    {
            $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
            echo $stateresult = GibberishAES::enc($error, $key);
    }
	
}

function getLanguageList()
{
    $key = 'rotcoderaclla';
    // The default key size is 256 bits.
    $old_key_size = GibberishAES::size();
    GibberishAES::size(256);	
    try
    {	
        $db=getConnection();
        $sql="select option_id, title from list_options where list_id='language'";
        $stmt = $db->prepare($sql) ; 
        $stmt->execute();
        $languagelist = $stmt->fetchAll(PDO::FETCH_OBJ);           

        if($languagelist)
        {
            $langres = json_encode($languagelist); 
            echo $langresult = GibberishAES::enc($langres, $key);
        }
        else
        {
            $langres = '[{"id":"0"}]';
            echo $langresult = GibberishAES::enc($langres, $key);
        }
    }catch(PDOException $e)
    {
            $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
            echo $langresult = GibberishAES::enc($error, $key);
    }
}

function getEthnicityList()
{
    $key = 'rotcoderaclla';
    // The default key size is 256 bits.
    $old_key_size = GibberishAES::size();
    GibberishAES::size(256);
    try
    {	
        $db=getConnection();
        $sql="select option_id, title from list_options where list_id='ethnicity'";
        $stmt = $db->prepare($sql) ; 
        $stmt->execute();
        $ethnicitylist = $stmt->fetchAll(PDO::FETCH_OBJ);           

        if($ethnicitylist)
        {
            $ethicityres = json_encode($ethnicitylist); 
            echo $ethicityresult = GibberishAES::enc($ethicityres, $key);
        }
        else
        {
            $ethicityres = '[{"id":"0"}]';
            echo $ethicityresult = GibberishAES::enc($ethicityres, $key);
        }
    }catch(PDOException $e)
    {
            $error =  '{"error":{"text":'. $e->getMessage() .'}}'; 
            echo $ethicityresult = GibberishAES::enc($error, $key);
    }
}

function getRaceList()
{
    $key = 'rotcoderaclla';
    // The default key size is 256 bits.
    $old_key_size = GibberishAES::size();
    GibberishAES::size(256);	
    try
    {	
        $db=getConnection();
        $sql="select option_id, title from list_options where list_id='race'";
        $stmt = $db->prepare($sql) ; 
        $stmt->execute();
        $racelist = $stmt->fetchAll(PDO::FETCH_OBJ);           

        if($racelist)
        {
            $raceres =  json_encode($racelist); 
            echo $raceresult = GibberishAES::enc($raceres, $key);
        }
        else
        {
            $raceres = '[{"id":"0"}]';
            echo $raceresult = GibberishAES::enc($raceres, $key);
        }
    }catch(PDOException $e)
    {
            $error =  '{"error":{"text":'. $e->getMessage() .'}}'; 
            echo $raceresult = GibberishAES::enc($error, $key);
    }
}

function getRefSourceList()
{
	try
	{	
        $db=getConnection();
        $key = 'rotcoderaclla';
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        $sql="select option_id, title from list_options where list_id='refsource'";
        $stmt = $db->prepare($sql) ; 
        $stmt->execute();
        $refsourcelist = $stmt->fetchAll(PDO::FETCH_OBJ);           

        if($refsourcelist)
        {
            $refsrclistres = json_encode($refsourcelist); 
            echo $refsrclistresult = GibberishAES::enc($refsrclistres, $key);
        }
        else
        {
            $refsrclistres = '[{"id":"0"}]';
            echo $refsrclistresult = GibberishAES::enc($refsrclistres, $key);
        }
    }catch(PDOException $e)
    {
            $error =  '{"error":{"text":'. $e->getMessage() .'}}'; 
            echo $refsrclistresult = GibberishAES::enc($error, $key);
    }
}
function editContactDetails()
{
	try
    {
        $db = getConnection();
        $key = 'rotcoderaclla';
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        $flag=0;
        $request = Slim::getInstance()->request();
        $result =  GibberishAES::dec($request->getBody(), $key);
        
        $contactArray = json_decode($result,TRUE);
			
        $pid=$contactArray['pid'];
        $street=$contactArray['street'];
        $state=$contactArray['state'];
        $city=$contactArray['city'];
        $country_code=$contactArray['country_code'];
        $postal_code=$contactArray['postal_code'];
        $mothersname=$contactArray['mothersname'];
        $guardiansname=$contactArray['guardiansname'];
        $contact_relationship=$contactArray['contact_relationship'];
        $phone_contact=$contactArray['phone_contact'];
        $phone_home=$contactArray['phone_home'];
        $phone_biz=$contactArray['phone_biz'];
        $phone_cell=$contactArray['phone_cell'];
        $email=$contactArray['email'];
        
        $update_issues_Sql="UPDATE patient_data
			SET
			street=:street,
                        city=:city,
			state=:state,
			country_code=:country_code,
			postal_code=:postal_code,
			mothersname=:mothersname,
			guardiansname=:guardiansname,
			contact_relationship=:contact_relationship,
			phone_contact=:phone_contact,
			phone_home=:phone_home,
			phone_biz=:phone_biz,
			phone_cell=:phone_cell,
			email=:email,
			date=NOW()
			WHERE pid=:pid";
			
        $q = $db->prepare($update_issues_Sql);

        if($q->execute(array( ':pid'=>$pid,':street'=>$street,':city'=>$city,
                          ':state'=>$state,':country_code'=>$country_code,
                          ':postal_code'=>$postal_code,':mothersname'=>$mothersname,':guardiansname'=>$guardiansname,':contact_relationship'=>$contact_relationship,':phone_contact'=>$phone_contact,':phone_home'=>$phone_home,':phone_biz'=>$phone_biz,':phone_cell'=>$phone_cell,':email'=>$email
        )))
        {  
                $data = '[{"id":"1"}]';
                echo  $titlelistresult = GibberishAES::enc($data, $key);
        }
        else
        {
                $data = '[{"id":"0"}]';
                echo  $titlelistresult = GibberishAES::enc($data, $key);
        }
    }catch(PDOException $e)
    {
            $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
            echo  $titlelistresult = GibberishAES::enc($error, $key);
    }	
}

function editChoicesDetails()
{
    try
    {
        $db = getConnection();
        $flag=0;
        $key = 'rotcoderaclla';
        $request = Slim::getInstance()->request();
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        $result =  GibberishAES::dec($request->getBody(), $key);
        $choicesArray = json_decode($result,TRUE);
			
        $pid=$choicesArray['pid'];
        $providerID=$choicesArray['providerID'];
        $ref_providerID=$choicesArray['ref_providerID'];
        $pharmacy_id=$choicesArray['pharmacy_id'];
        $hipaa_notice=$choicesArray['hipaa_notice'];
        $hipaa_mail=$choicesArray['hipaa_mail'];
        $hipaa_voice=$choicesArray['hipaa_voice'];
        $hipaa_message=$choicesArray['hipaa_message'];
        $hipaa_allowsms=$choicesArray['hipaa_allowsms'];
        $hipaa_allowemail=$choicesArray['hipaa_allowemail'];
        $allow_patient_portal=$choicesArray['allow_patient_portal'];
        $allow_imm_info_share=$choicesArray['allow_imm_info_share'];
        $allow_health_info_ex=$choicesArray['allow_health_info_ex'];
        $allow_imm_reg_use=$choicesArray['allow_imm_reg_use'];
		        
        $update_issues_Sql="UPDATE patient_data
			SET
			providerID=:providerID,
			ref_providerID=:ref_providerID,
			pharmacy_id=:pharmacy_id,
			hipaa_notice=:hipaa_notice,
                        hipaa_mail =:hipaa_mail,
                        hipaa_voice =:hipaa_voice,
                        hipaa_message =:hipaa_message,
                        hipaa_allowsms =:hipaa_allowsms,
                        hipaa_allowemail=:hipaa_allowemail,
                        allow_patient_portal=:allow_patient_portal,
                        allow_imm_info_share=:allow_imm_info_share,
                        allow_health_info_ex=:allow_health_info_ex,
                        allow_imm_reg_use=:allow_imm_reg_use,
			date=NOW()
			WHERE pid=$pid";

        $q = $db->prepare($update_issues_Sql);

        if($q->execute(array( ':providerID'=>$providerID, ':ref_providerID'=>$ref_providerID, ':pharmacy_id'=>$pharmacy_id,
                              ':hipaa_allowsms'=>$hipaa_allowsms,':hipaa_message'=>$hipaa_message,':hipaa_mail'=>$hipaa_mail,
                              ':hipaa_notice'=>$hipaa_notice,':hipaa_voice'=>$hipaa_voice,':hipaa_allowemail'=>$hipaa_allowemail,
                              ':allow_imm_info_share'=>$allow_imm_info_share,':allow_health_info_ex'=>$allow_health_info_ex,
                              ':allow_patient_portal'=>$allow_patient_portal,':allow_imm_reg_use' =>$allow_imm_reg_use
        )))
        {  
                $data = '[{"id":"1"}]';
                echo  $titlelistresult = GibberishAES::enc($data, $key);
        }
        else
        {
                $data = '[{"id":"0"}]';
                echo  $titlelistresult = GibberishAES::enc($data, $key);
        }
    }catch(PDOException $e)
    {
            $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
            echo  $titlelistresult = GibberishAES::enc($error, $key);
    }
}

function editEmployerDetails()
{
    try
    {
        $db = getConnection();
	$flag=0;
        $key = 'rotcoderaclla';
        $request = Slim::getInstance()->request();
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        $result =  GibberishAES::dec($request->getBody(), $key);
        $employerArray = json_decode($result,TRUE);
			
        $pid=$employerArray['pid'];
        $occupation=$employerArray['occupation'];
        $name=$employerArray['name'];
        $street=$employerArray['street'];
        $city=$employerArray['city'];
        $state=$employerArray['state'];
        $country=$employerArray['country'];
        $postal_code=$employerArray['postal_code'];

        $update_issues_Sql="UPDATE patient_data
			SET
			occupation=:occupation,
			date=NOW()
			WHERE pid=:pid";
			
        $q = $db->prepare($update_issues_Sql);

        if($q->execute(array( ':pid'=>$pid,':occupation'=>$occupation)))
        {  
            $data = '[{"id":"1"}]';
            echo  $titlelistresult = GibberishAES::enc($data, $key);
        } 
        else
        {
                $data = '[{"id":"0"}]';
                echo  $titlelistresult = GibberishAES::enc($data, $key);
        }
         $insert_emp_Sql=" UPDATE employer_data SET 
                    name=:name,
                    street=:street,
                    city=:city,
                    state=:state,
                    country=:country,
                    postal_code=:postal_code,
                    date=NOW()
                    WHERE pid=:pid
                    ";

            $sql_emp = $db->prepare($insert_emp_Sql);
            if($sql_emp->execute(array( ':name'=>$name,':street'=>$street,':postal_code'=>$postal_code,':city'=>$city,':state'=>$state,':country'=>$country,':pid'=>$pid)))
            {
                    $data = '[{"id":"1"}]';
                    echo  $titlelistresult = GibberishAES::enc($data, $key);
            }
            else
            {
                    $data = '[{"id":"0"}]';
                    echo  $titlelistresult = GibberishAES::enc($data, $key);
            }
       
    }catch(PDOException $e)
    {
            $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
            echo  $titlelistresult = GibberishAES::enc($error, $key);
    }
}

function editStatsDetails()
{
	try
    {
        $db = getConnection();
        $flag=0;
        $key = 'rotcoderaclla';
        $request = Slim::getInstance()->request();
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        $result =  GibberishAES::dec($request->getBody(), $key);
        $statsArray = json_decode($result,TRUE);
			
        $pid=$statsArray['pid'];
        $language=$statsArray['language'];
        $ethnicity=$statsArray['ethnicity'];
        $race=$statsArray['race'];
        $financial_review=$statsArray['financial_review'];
        $family_size=$statsArray['family_size'];
        $monthly_income=$statsArray['monthly_income'];
        $homeless=$statsArray['homeless'];
        $interpretter=$statsArray['interpretter'];
        $migrantseasonal=$statsArray['migrantseasonal'];
        $referral_source=$statsArray['referral_source'];
        $vfc=$statsArray['vfc'];
		        
        $update_stats_Sql="UPDATE patient_data
			SET
			language=:language,
			ethnicity=:ethnicity,
			race=:race,
			financial_review=:financial_review,
			family_size=:family_size,
			monthly_income=:monthly_income,
			homeless=:homeless,
			interpretter=:interpretter,
			migrantseasonal=:migrantseasonal,
			referral_source=:referral_source,
                        vfc=:vfc,
			date=NOW()
			WHERE pid=:pid";
			
        $q = $db->prepare($update_stats_Sql);

        if($q->execute(array( ':pid'=>$pid,':language'=>$language,':ethnicity'=>$ethnicity,':race'=>$race,
                            ':financial_review'=>$financial_review,':family_size'=>$family_size,':monthly_income'=>$monthly_income,
                            ':homeless'=>$homeless,':interpretter'=>$interpretter,':migrantseasonal'=>$migrantseasonal,
                            ':referral_source'=>$referral_source,':vfc'=>$vfc
        )))
        {  
                echo '[{"id":"1"}]';
                echo  $titlelistresult = GibberishAES::enc($titlelistres, $key);
        }
        else
        {
                $titlelistres = '[{"id":"0"}]';
                echo  $titlelistresult = GibberishAES::enc($titlelistres, $key);
        }
    }catch(PDOException $e)
    {
            $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
            echo  $titlelistresult = GibberishAES::enc($error, $key);
    }
}

function editMiscDetails()
{
    try
    {
        $db = getConnection();
        $flag=0;
        $key = 'rotcoderaclla';
        $request = Slim::getInstance()->request();
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        $result =  GibberishAES::dec($request->getBody(), $key);
        $miscArray = json_decode($result,TRUE);

        $pid=$miscArray['pid'];
        $deceased_date=$miscArray['deceased_date'];
        $deceased_reason=$miscArray['deceased_reason'];
       				        
        $update_misc_Sql="UPDATE patient_data
			SET
			deceased_date=:deceased_date,
			deceased_reason=:deceased_reason,
                        date=NOW()
			WHERE pid=:pid";
			
        $q = $db->prepare($update_misc_Sql);

        if($q->execute(array( ':pid'=>$pid,':deceased_date'=>$deceased_date,':deceased_reason'=>$deceased_reason,
        )))
        {  
                $titlelistres = '[{"id":"1"}]';
                echo  $titlelistresult = GibberishAES::enc($titlelistres, $key);
        }
        else
        {
                $titlelistres = '[{"id":"0"}]';
                echo  $titlelistresult = GibberishAES::enc($titlelistres, $key);
        }
    }catch(PDOException $e)
    {
            $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
            echo  $titlelistresult = GibberishAES::enc($error, $key);
    }
}

function editApptStatus()
{
	try
    {
        $db = getConnection();
        $flag=0;
        $request = Slim::getInstance()->request();
        $statusArray = json_decode($request->getBody(),TRUE);
			print_r($statusArray);
        $pid=$statusArray['pid'];
		$pc_eid=$statusArray['apptid'];
		echo $pc_apptstatus=$statusArray['status'];
		
		$update_appt_Sql="UPDATE openemr_postcalendar_events
			SET
			pc_apptstatus=:pc_apptstatus
			WHERE pid=:pid AND pc_eid=:pc_eid";
			
			$q = $db->prepare($update_appt_Sql);

			if($q->execute(array( ':pc_apptstatus'=>$pc_apptstatus,':pid'=>$pid,
					  ':pc_eid'=>$pc_eid
			)))
			{  
				echo '[{"id":"1"}]';
			}
			else
			{
				echo '[{"id":"0"}]';
			}
		
	}catch(PDOException $e)
	{
		echo '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
	
}

function getTitles()
{
	try
	{	
        $db=getConnection();
        $key = 'rotcoderaclla';
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        $sql="select option_id, title from list_options where list_id='titles'";
        $stmt = $db->prepare($sql) ; 
        $stmt->execute();
        $titleslist = $stmt->fetchAll(PDO::FETCH_OBJ);           

        if($titleslist)
        {
            $titlelistres = json_encode($titleslist); 
            echo  $titlelistresult = GibberishAES::enc($titlelistres, $key);
        }
        else
        {
            $titlelistres = '[{"id":"0"}]';
            echo  $titlelistresult = GibberishAES::enc($titlelistres, $key);
        }
	}catch(PDOException $e)
	{
		$error = '{"error":{"text":'. $e->getMessage() .'}}'; 
                echo  $titlelistresult = GibberishAES::enc($error, $key);
	}
}
// To get Sex, Title, and Marital Status
function getMasterData()
{
	try
	{	
        $db=getConnection();
        $key = 'rotcoderaclla';
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        $sql="select option_id, title from list_options where list_id=:list_id";
            
        $demo1 = sql_execute2('titles',$sql);
        $demo2 = sql_execute2('sex',$sql);
        $demo3 = sql_execute2('marital',$sql);

        $titleslist1=encode_demo2($demo1);      
        $titleslist2=encode_demo2($demo2);  
        $titleslist3=encode_demo2($demo3);  
        $titleslist['Titles'] = check_data_available($titleslist1);
        $titleslist['Gender'] = check_data_available($titleslist2);
        $titleslist['Status'] = check_data_available($titleslist3);
        

        if($titleslist)
        {
            $titlelistres = json_encode($titleslist); 
            echo $titlelistresult = GibberishAES::enc($titlelistres, $key);
        }
        else
        {
            $titlelistres = '[{"id":"0"}]';
            echo  $titlelistresult = GibberishAES::enc($titlelistres, $key);
        }
	}catch(PDOException $e)
	{
            $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
            echo  $titlelistresult = GibberishAES::enc($error, $key);
	}
}

function getSex()
{
	try
	{	
        $db=getConnection();
        $key = 'rotcoderaclla';
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        $sql="select option_id, title from list_options where list_id='sex'";
        $stmt = $db->prepare($sql) ; 
        $stmt->execute();
        $sexlist = $stmt->fetchAll(PDO::FETCH_OBJ);           

        if($sexlist)
        {
            $sexlistres = json_encode($sexlist); 
            echo  $sexlistresult = GibberishAES::enc($sexlistres, $key);
        }
        else
        {
            $sexlistres = '[{"id":"0"}]';
            echo  $sexlistresult = GibberishAES::enc($sexlistres, $key);
        }
    }catch(PDOException $e)
    {
            $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
            echo  $sexlistresult = GibberishAES::enc($error, $key);
    }
}

function getMaritalStatus()
{
	try
	{	
        $db=getConnection();
        $key = 'rotcoderaclla';
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        $sql="select option_id, title from list_options where list_id='marital'";
        $stmt = $db->prepare($sql) ; 
        $stmt->execute();
        $maritallist = $stmt->fetchAll(PDO::FETCH_OBJ);           

        if($maritallist)
        {
            $mlistres =  json_encode($maritallist); 
            echo  $mlistresult = GibberishAES::enc($mlistres, $key);
        }
        else
        {
           $mlistres = '[{"id":"0"}]';
            echo  $mlistresult = GibberishAES::enc($mlistres, $key);
        }
    }catch(PDOException $e)
    {
            $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
            echo  $mlistresult = GibberishAES::enc($error, $key);
    }
}
function editPatientFacility()
{
    try
    {
        $db = getConnection();
        $flag=0;
        $apikey = 'rotcoderaclla';
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        $request = Slim::getInstance()->request();
        
        $editpatientres =  GibberishAES::dec($request->getBody(), $apikey);
        //$editpatientres =  GibberishAES::dec('U2FsdGVkX1/CUddU61CuRizHw1BtPjx950hZUyhkKCm/kCnkaFlwYwl995bPUqo2ShKubJzhPu+OXUCHDDmDOkqV71FRG7dfaQM27OApZs5pyeFZfH17Fd8UD0/lKTaVuDWsIwJgm36u9Ejohf9Opb6Hi7UNs8Je2/4Aze8ZKkIHqbj+Tnyk93Lpd/dHBDR2gDQHuy1ET0957a7vYP6tpL3gJPyONv2OxzUaSYCLvjPZ35yt7FulKUtFcE1JAuJA', $apikey);
        $pfArray = json_decode($editpatientres,TRUE);
	//print_r($pfArray);
			
        //$id=$pfArray['id'];
        $pid=$pfArray['pid'];
        $loginProvderId=(int)$pfArray['loginProviderId'];
        $facilityid=(int)$pfArray['facilityid'];
        $admitdate=$pfArray['admitdate'];
        $dischargedate=$pfArray['dischargedate'];
        $isactive=$pfArray['isactive'];
        $notes=$pfArray['notes'];
        $related_links=$pfArray['related_links'];
        $facility_roomno = $pfArray['facility_roomno'];
        
        $checkfield = "SELECT id from tbl_patientfacility WHERE patientid=$pid AND facilityid=$facilityid AND facility_admitdate='$admitdate' AND facility_dischargedate='$dischargedate' AND facility_isactive='$isactive'";
        $c = $db->prepare($checkfield);
        $db->query( "SET NAMES utf8");
        $c->execute();
        $resid=$c->fetch(PDO::FETCH_OBJ);
        
        if($resid){
           $id = $resid->id;
        }else{
            $id=0;
        }
      
        
        if($id==0)
        {
                $insert_pf_Sql="INSERT INTO tbl_patientfacility(patientid,facilityid,facility_admitdate,facility_dischargedate,facility_isactive,facility_notes,facility_related_links,createdby,created_date,facility_roomno)
                                                                           VALUES (:pid,:facilityid,:admitdate,:dischargedate,:isactive,:notes,:related_links,:loginProvderId,NOW(),:facility_roomno)";
                $db->query( "SET NAMES utf8");
                $q = $db->prepare($insert_pf_Sql);

                if($q->execute(array( ':pid'=>$pid,':facilityid'=>$facilityid,
                                   ':admitdate'=>$admitdate,':dischargedate'=>$dischargedate,
                                  ':isactive'=>$isactive,':notes'=>$notes,':related_links'=>$related_links,':loginProvderId'=>$loginProvderId,':facility_roomno'=>$facility_roomno	)))
                {                       		
                        /*$sqlGetMaxid="SELECT MAX(id) as pf_id FROM tbl_patientfacility";
                        $stmtGetMaxid = $db->prepare($sqlGetMaxid) ;           
                        //$stmt->bindParam("date_today", $date_today);            
                        $stmtGetMaxid->execute();
                        $maxid = $stmtGetMaxid->fetchAll(PDO::FETCH_OBJ);
                        $maxidres =  '[{"id":$maxid}]';
                        
                        echo $maxidresult = GibberishAES::enc($maxidres, $key);*/
                     $maxidres = '[{"id":"1"}]';
                     echo $maxidresult = GibberishAES::enc($maxidres, $apikey);
                    
                }
                else
                {
                        $maxidres = '[{"id":"0"}]';   
                        echo $maxidresult = GibberishAES::enc($maxidres, $apikey);
                }
        }
        else
        {
                $update_issues_Sql="UPDATE tbl_patientfacility
                SET
                patientid=:patientid,
                facilityid=:facilityid,
                facility_admitdate=:admitdate,
                facility_dischargedate=:dischargedate,
                facility_isactive=:isactive,
                facility_notes=:notes,
                facility_related_links=:related_links,
                facility_roomno=:facility_roomno,
                updatedby=:loginProvderId,
                updated_date=NOW()
                WHERE id=$id
                ";
                $db->query( "SET NAMES utf8");
                $q = $db->prepare($update_issues_Sql);

                if($q->execute(array( ':patientid'=>$pid,':facilityid'=>$facilityid,
                                  ':admitdate'=>$admitdate,':dischargedate'=>$dischargedate,
                                  ':isactive'=>$isactive,':notes'=>$notes,':related_links'=>$related_links,':loginProvderId'=>$loginProvderId,':facility_roomno'=>$facility_roomno	)))
                {    
                        $maxidres = '[{"id":"1"}]';
                        echo $maxidresult = GibberishAES::enc($maxidres, $apikey);
                }
                else
                {
                        $maxidres = '[{"id":"0"}]';
                        echo $maxidresult = GibberishAES::enc($maxidres, $apikey);
                }
        }
  
    }
    catch(PDOException $e) 
    {      
		$error = '{"error":{"text":'. $e->getMessage() .'}}'; 
                echo $errorresult = GibberishAES::enc($error, $apikey);
    }   
}
function editWhoDetails()
{
   
    try
    {
        $db = getConnection();
        $flag=0; 
        $apikey = 'rotcoderaclla';
        $request = Slim::getInstance()->request();
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        $result =  GibberishAES::dec($request->getBody(), $apikey);
        $contactArray = json_decode($result,TRUE);
          
        $pid=$contactArray['pid'];
        $title=$contactArray['title'];
        $fname=$contactArray['fname'];
        $mname=$contactArray['mname'];
        $lname=$contactArray['lname'];
        $dob=$contactArray['DOB'];
        $sex=$contactArray['sex'];
        $ss=$contactArray['ss'];
        $drivers_license=$contactArray['drivers_license'];
        $status=$contactArray['status'];
        
       
        $update_issues_Sql="UPDATE patient_data
            SET
            title=:title,
            fname=:fname,
            mname=:mname,
            lname=:lname,
            DOB=:DOB,
            sex=:sex,
            ss=:ss,
            drivers_license=:drivers_license,
            status=:status,
            date=NOW()
            WHERE pid=$pid";
            
            $q = $db->prepare($update_issues_Sql);

            if($q->execute(array( ':title'=>$title,
                      ':fname'=>$fname,':lname'=>$lname,':mname'=>$mname,
                      ':DOB'=>$dob,':sex'=>$sex,':ss'=>$ss,':drivers_license'=>$drivers_license,':status'=>$status
            )))
            {  
                $data = '[{"id":"1"}]';
                echo $errorresult = GibberishAES::enc($data, $apikey);
            }
            else
            {
                $data = '[{"id":"0"}]';
                echo $errorresult = GibberishAES::enc($data, $apikey);
            }
    }catch(PDOException $e)
    {
        $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo $errorresult = GibberishAES::enc($error, $apikey);
    }    
}

// method to get prescriptions
function getPrescriptions($patientId)
{
    try 
    {
        $db = getConnection();  
        $key = 'rotcoderaclla';
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        //To get Prescriptions
       /* $sql = "select     
 id,patient_id,filled_by_id,pharmacy_id,date_added,date_modified,provider_id,encounter,start_date,drug,drug_id,rxnorm_drugcode,form,dosage,quantity,size,unit,route,substitute,refills,per_refill,filled_date,medication,note,active,site,prescriptionguid,erx_source,erx_uploaded,drug_info_erx from prescriptions where patient_id=$patientId and active='1'";*/
        $sql = "SELECT prescriptions.id, patient_id, filled_by_id, (select name from pharmacies where id= pharmacy_id )as Pharmacy_Name, date_added, date_modified, CONCAT( users.fname,  ' ', users.lname ) AS provider, encounter, start_date, drug, drug_id, rxnorm_drugcode, form, dosage, quantity, size, unit, route, substitute, refills, per_refill, filled_date, medication, note, prescriptions.active, site, prescriptionguid, erx_source, erx_uploaded, drug_info_erx
            FROM prescriptions
            INNER JOIN users ON users.id = prescriptions.provider_id
            WHERE prescriptions.patient_id =$patientId
            AND prescriptions.active ='1'";
                
        $stmt = $db->prepare($sql) ; 
        $stmt->execute();
        $presclist = $stmt->fetchAll(PDO::FETCH_OBJ);           

        if($presclist)
        {
            $presres = json_encode($presclist); 
            echo $presresult = GibberishAES::enc($presres, $key);
            
        }
        else
        {
            $presres = '[{"DataAvailable":"No"}]';
            echo $presresult = GibberishAES::enc($presres, $key);
        }
                
    }catch(PDOException $e)
    {
        $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo $presresult = GibberishAES::enc($error, $key);
    }    
}
//method to store mobile app settings
function storeMobAppSettings()
{
    try
    {
        $db = getConnection();
        $flag=0;
        
        $key = 'rotcoderaclla';
        $storerequest = Slim::getInstance()->request();
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        $storeresult =  GibberishAES::dec($storerequest->getBody(), $key);
        
        $settingsArray = json_decode($storeresult,TRUE);
            
        $loginProvderId=$settingsArray['loginProvderId'];
        $settings=$settingsArray['settings'];

        $sql = "select settings from mobile_settings where userid=$loginProvderId";
                
        $stmt = $db->prepare($sql) ; 
        $stmt->execute();
        $mobsettings = $stmt->fetchAll(PDO::FETCH_OBJ);           

        if($mobsettings)
        {
            $sql = "UPDATE mobile_settings 
                    SET
                    settings=:settings
                    WHERE userid=:loginProvderId";
            $q = $db->prepare($sql);
            if($q->execute(array(':loginProvderId'=>$loginProvderId,
                            ':settings'=>$settings)))
            {   
                echo '[{"id":"1"}]';
            }
            else
            {
                echo '[{"id":"0"}]';             
            }
        }
        else
        { 
            $sql = "INSERT INTO mobile_settings (userid, settings)
                values (:loginProvderId, :settings)";
                $q = $db->prepare($sql);
            if($q->execute(array(':loginProvderId'=>$loginProvderId,
                            ':settings'=>$settings)))
            {   
                echo '[{"id":"1"}]';
                
            }
            else
            {
                echo '[{"id":"0"}]';   
            }
        }
        
    }catch(PDOException $e)
    {
        echo '{"error":{"text":'. $e->getMessage() .'}}'; 
    }  
}
//method ends
//method to restore mobile app settings
function restoreMobAppSettings()
{
    
    $request = Slim::getInstance()->request();
    $logObject = json_decode($request->getBody(),TRUE);

    $key = 'rotcoderaclla';
    // The default key size is 256 bits.
    $old_key_size = GibberishAES::size();
    GibberishAES::size(256);
    $username = GibberishAES::dec($logObject['username'], $key);
    $password = GibberishAES::dec($logObject['password'], $key);
    
 $sql="SELECT us.id,us.username,us.password,us.salt,u.fname,u.lname, u.authorized
    FROM users_secure us 
    INNER JOIN users u 
    ON us.id=u.id 
    WHERE us.username=:username";
    
    try 
    {
        $db = getConnection();
        $stmt = $db->prepare($sql) ;
        $stmt->bindParam("username", $username);
        $stmt->execute();
        $user = $stmt->fetchObject();                          

        if($user)
        {
            //return a hashed string
            $phash=crypt($password,$user->salt);

            if($phash==$user->password)
            { 
    $loginProvderId=$user->id;
                $sql = "select settings from mobile_settings where userid=$loginProvderId";
    
    $stmt = $db->prepare($sql) ; 
    $stmt->execute();
    $mobilesettings = $stmt->fetch(PDO::FETCH_OBJ);           

    if($mobilesettings)
    {
     //echo json_encode($mobilesettings);
     echo GibberishAES::enc($mobilesettings->settings, $key) ;
    }
    else
    {
     // if no settings data is present for this user
     echo '[{"DataAvailable":"No"}]';
    }
            } 
            else 
            {
                // if username or password is wrong return id=0
                echo '[{"id":"0"}]';
            }                               
        }
        else
        {
            // if user does not exist return id=0
            echo '[{"id":"0"}]';
        }
    } 
    catch(PDOException $e) 
    {
        echo '{"error":{"text":'. $e->getMessage() .'}}'; 
    }
}
function changeAppointmentStatus()
{
    try{
        
        $db = getConnection();
        $flag=0;
        $request = Slim::getInstance()->request();
        $key = 'rotcoderaclla';
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        $appres = GibberishAES::dec($request->getBody(), $key);
        $statusArray = json_decode($appres,TRUE);
			
        $pid=$statusArray['pid'];
	$pc_eid=$statusArray['apptid'];
	$pc_apptstatus=$statusArray['apptstatus'];
        $pc_comments=$statusArray['comments'];
        $pc_time = $statusArray['start_time'];
        $pc_eventDate = $statusArray['event_date'];
        //$time = explode(" ", $pc_time);	
        $pc_time2 = date("H:i:s", strtotime($pc_time));
	$update_appt_Sql="UPDATE openemr_postcalendar_events
			SET
			pc_apptstatus=:pc_apptstatus , pc_hometext=:pc_comments , pc_startTime=:pc_time, pc_eventDate=:pc_eventDate, pc_time=NOW()
			WHERE pc_pid=:pid AND pc_eid=:pc_eid";
			
	$q = $db->prepare($update_appt_Sql);

        if($q->execute(array( ':pc_apptstatus'=>$pc_apptstatus,':pid'=>$pid,':pc_eid'=>$pc_eid, ':pc_comments'=>$pc_comments, ':pc_time'=>$pc_time2, ':pc_eventDate' =>$pc_eventDate   ))){  
            echo '[{"id":"1"}]';
	}else{
            echo '[{"id":"0"}]';
	}
		
    }catch(PDOException $e){
	echo '{"error":{"text":'. $e->getMessage() .'}}'; 
    }
	
}
function cancelAppointmentStatus()
{
    try{
        
        $db = getConnection();
        $flag=0;
        $request = Slim::getInstance()->request();
        $key = 'rotcoderaclla';
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        $resultant = GibberishAES::dec($request->getBody(), $key);
        $statusArray = json_decode($resultant,TRUE);
			
        $pid=$statusArray['pid'];
	$pc_eid=$statusArray['apptid'];
	$pc_apptstatus=$statusArray['apptstatus'];
        $pc_comments=$statusArray['comments'];
        		
	$update_appt_Sql="UPDATE openemr_postcalendar_events
			SET
			pc_apptstatus=:pc_apptstatus , pc_hometext=:pc_comments
			WHERE pc_pid=:pid AND pc_eid=:pc_eid";
			
	$q = $db->prepare($update_appt_Sql);

        if($q->execute(array( ':pc_apptstatus'=>$pc_apptstatus,':pid'=>$pid,':pc_eid'=>$pc_eid, ':pc_comments'=>$pc_comments  ))){  
            echo '[{"id":"1"}]';
	}else{
            echo '[{"id":"0"}]';
	}
		
    }catch(PDOException $e){
	echo '{"error":{"text":'. $e->getMessage() .'}}'; 
    }
	
}
function getDosFormDataDetails($eid,$copy_to_encounter, $provider_id){
    $apikey = 'rotcoderaclla';
    // The default key size is 256 bits.
    $old_key_size = GibberishAES::size();
    GibberishAES::size(256);
    $user='';
    $fields='';
    $field_value='';
    $field_id2=array();
    $field_id1=array();
    $array=array();
    $check_string = '';
    $new_id = 0;
     $pending='' ;
    $finalized='';
    $formstatus='';
    try{
        $db = getConnection();
        $newdemo = '';
        $sql = "select pid from form_encounter where encounter=$eid";      
        $stmt = $db->prepare($sql) ;
        $stmt->execute();                       
        $patients = $stmt->fetchAll(PDO::FETCH_OBJ); 
        if(!empty($patients)){
            $pid = $patients[0]->pid;
        }else {
            $pid = '';
        }
        $check_data1 = 0;
        $check_data2 = 0;
        $check_data3 = 0;
        $get_provider = "SELECT username FROM users WHERE id= $provider_id";
        $stmt_prov = $db->prepare($get_provider) ;
        $stmt_prov->execute(); 
        $get_provider = $stmt_prov->fetchAll(PDO::FETCH_OBJ);
        $provider = $get_provider[0]->username;
        // to get lbf and dictation forms
        $sql31 = "SELECT field_id, title FROM layout_options WHERE form_id = 'LBF1' and group_name LIKE '%Dictation'";
        $stmt31 = $db->prepare($sql31) ;
        $stmt31->execute(); 
        $patients31 = $stmt31->fetchAll(PDO::FETCH_OBJ);
        foreach($patients31 as $k_value){
            $check_string .= "field_id LIKE '".$k_value->field_id ."%' or ";
        }
        $check_string .= "field_id LIKE 'f2f%'";
        // to lbf forms
        $sql2 = "select form_id from forms where encounter =$eid and pid =$pid and formdir = 'LBF2' and deleted = 0 ";      
        $stmt2 = $db->prepare($sql2) ;
        $stmt2->execute();                       
        $patients2 = $stmt2->fetchAll(PDO::FETCH_OBJ); 
        if(!empty($patients2)){
            $get_form_id_sql = "SELECT form_id,pid FROM forms WHERE formdir='LBF2' and deleted=0 and encounter = $copy_to_encounter";
            $get_stmt = $db->prepare($get_form_id_sql) ;
            $get_stmt->execute(); 
            $set_form_id_sql = $get_stmt->fetchAll(PDO::FETCH_OBJ); 
            if(!empty($set_form_id_sql)){
                $toid = $set_form_id_sql[0]->form_id;
                $get_form_data = "SELECT * FROM lbf_data WHERE form_id = '".$set_form_id_sql[0]->form_id."' and ($check_string)";
                $get_stmt2 = $db->prepare($get_form_data) ;
                $get_stmt2->execute(); 
                $set_form_data_sql = $get_stmt2->fetchAll(PDO::FETCH_OBJ); 
                if(!empty($set_form_data_sql)){
                    $delete_forms = "DELETE FROM lbf_data WHERE form_id = '".$set_form_id_sql[0]->form_id."' and ($check_string)";
                    $get_stmt3 = $db->prepare($delete_forms) ;
                    $get_stmt3->execute(); 

                    // from encounter data
                     $get_provider = "SELECT * FROM form_encounter WHERE encounter = $copy_to_encounter";
                     $get_stmt51 = $db->prepare($get_provider) ;
                     $get_stmt51->execute(); 
                     $get_provider_data = $get_stmt51->fetchAll(PDO::FETCH_OBJ); 
                            // echo "<pre>"; print_r($get_provider_data); echo "</pre>";
                      $provider_id=$get_provider_data[0]->provider_id;
                    $get_from_encounter = "SELECT form_id,pid FROM forms WHERE formdir='LBF2' and deleted=0 and encounter = $eid";
                    $get_stmt4 = $db->prepare($get_from_encounter) ;
                    $get_stmt4->execute(); 
                    $set_from_encounter = $get_stmt4->fetchAll(PDO::FETCH_OBJ); 
                    if(!empty($set_from_encounter)){
                        $get_from_encounter_data = "SELECT * FROM lbf_data WHERE form_id = '".$set_from_encounter[0]->form_id."' and ($check_string)";
                        $newid = $set_from_encounter[0]->form_id;
                        $get_stmt5 = $db->prepare($get_from_encounter_data) ;
                        $get_stmt5->execute(); 
                        $set_from_encounter_data = $get_stmt5->fetchAll(PDO::FETCH_OBJ); 
                        if(!empty($set_from_encounter_data)){
                            foreach($set_from_encounter_data as $data_key => $data_value){
                                //echo $data_value->form_id."==".$data_value->field_id."==".$data_value->field_value."<br>";
                                if($data_value->field_id!='f2f_ps'){
                                    $insert_form = "INSERT into lbf_data (form_id, field_id, field_value) VALUES ('".$set_form_id_sql[0]->form_id."','".$data_value->field_id."','".$data_value->field_value."')";
                                    $get_stmt6 = $db->prepare($insert_form) ;
                                    $get_stmt6->execute(); 
                                    $check_data3 = 1;
                                }else if($data_value->field_id=='f2f_ps'){
                                    $insert_form = "INSERT into lbf_data (form_id, field_id, field_value) VALUES ('".$set_form_id_sql[0]->form_id."','".$data_value->field_id."','$provider_id')";
                                    $get_stmt6 = $db->prepare($insert_form) ;
                                    $get_stmt6->execute(); 
                                    $check_data3 = 1;
                                }
                            }
                        }
                        // to update template
                        $sql_temp = "SELECT field_id, title FROM layout_options WHERE form_id = 'LBF1' and group_name LIKE '%Dictation'";
                        $sql_temp_stmt = $db->prepare($sql_temp) ;
                        $sql_temp_stmt->execute(); 
                        $set_sql_temp = $sql_temp_stmt->fetchAll(PDO::FETCH_OBJ);
                        foreach($set_sql_temp as $key_value){
                            $save_template_field = "UPDATE `tbl_allcare_template` SET date = 'NOW()',copy_from_enc = $eid ,copy_from_id = $newid WHERE copy_to_enc = $copy_to_encounter and copy_to_id = $toid and form_name ='".$key_value->title."'"; 
                            $get_stmt29 = $db->prepare($save_template_field) ;
                            $get_stmt29->execute(); 
                        }
                    }
                }
//                // to update template
//                $sql_temp = "SELECT field_id, title FROM layout_options WHERE form_id = 'LBF1' and group_name LIKE '%Dictation'";
//                $sql_temp_stmt = $db->prepare($sql_temp) ;
//                $sql_temp_stmt->execute(); 
//                $set_sql_temp = $sql_temp_stmt->fetchAll(PDO::FETCH_OBJ);
//                foreach($set_sql_temp as $key_value){
//                    $save_template_field = "UPDATE `tbl_allcare_template` SET date = 'NOW()',copy_from_enc = $eid ,copy_from_id = $newid WHERE copy_to_enc = $copy_to_encounter and copy_to_id = $toid and form_name ='".$key_value->title."'"; 
//                    $get_stmt29 = $db->prepare($save_template_field) ;
//                    $get_stmt29->execute(); 
//                }
            }else{
                $get_new_formid = "select max(form_id)as form_id from forms where form_name='Allcare Encounter Forms' AND formdir='LBF2'";
                $get_stmt7 = $db->prepare($get_new_formid) ;
                $get_stmt7->execute(); 
                $set_new_formid = $get_stmt7->fetchAll(PDO::FETCH_OBJ); 
                if(!empty($set_new_formid))
                    $new_formid = $set_new_formid[0]->form_id+1;
                else
                    $new_formid = 1;

                $form_ins = "INSERT INTO  forms (date,encounter,form_name,form_id,pid,user,groupname,authorized,deleted,formdir) VALUES (NOW(),$copy_to_encounter,'Allcare Encounter Forms',$new_formid,$pid,'$provider','default',1,0,'LBF2')";
                $form_insstmt = $db->prepare($form_ins);
                $form_insstmt->execute();  

                
                $get_provider = "SELECT * FROM form_encounter WHERE encounter = $copy_to_encounter";
                             $get_stmt51 = $db->prepare($get_provider) ;
                             $get_stmt51->execute(); 
                             $get_provider_data = $get_stmt51->fetchAll(PDO::FETCH_OBJ); 
                             //echo "<pre>"; print_r($get_provider_data); echo "</pre>";
                             $provider_id=$get_provider_data[0]->provider_id;
                             
                $get_from_encounter = "SELECT form_id,pid FROM forms WHERE formdir='LBF2' and deleted=0 and encounter = $eid";
                $get_stmt8 = $db->prepare($get_from_encounter) ;
                $get_stmt8->execute(); 
                $set_from_encounter = $get_stmt8->fetchAll(PDO::FETCH_OBJ); 
                if(!empty($set_from_encounter)){
                    $new_id = $set_from_encounter[0]->form_id;
                    $get_from_encounter_data = "SELECT * FROM lbf_data WHERE form_id = '".$set_from_encounter[0]->form_id."' and ($check_string)";
                    $get_stmt9 = $db->prepare($get_from_encounter_data) ;
                    $get_stmt9->execute(); 
                    $set_from_encounter_data = $get_stmt9->fetchAll(PDO::FETCH_OBJ); 
                    if(!empty($set_from_encounter_data)){
                        foreach($set_from_encounter_data as $data_key => $data_value){
                            //echo $data_value->form_id."==".$data_value->field_id."==".$data_value->field_value."<br>";
                            if($data_value->field_id!='f2f_ps'){
                                $insert_form = "INSERT into lbf_data (form_id, field_id, field_value) VALUES ('".$new_formid."','".$data_value->field_id."','".$data_value->field_value."')";
                                $get_stmt6 = $db->prepare($insert_form) ;
                                $get_stmt6->execute(); 
                                $check_data3 = 1;
                            }else if($data_value->field_id=='f2f_ps'){
                                $insert_form = "INSERT into lbf_data (form_id, field_id, field_value) VALUES ('".$new_formid."','".$data_value->field_id."','$provider_id')";
                                $get_stmt6 = $db->prepare($insert_form) ;
                                $get_stmt6->execute(); 
                                $check_data3 = 1;
                            }
                        }
                    }
                }
                // insert template
                $sql_temp = "SELECT field_id, title FROM layout_options WHERE form_id = 'LBF1' and group_name LIKE '%Dictation'";
                $sql_temp_stmt = $db->prepare($sql_temp) ;
                $sql_temp_stmt->execute(); 
                $set_sql_temp = $sql_temp_stmt->fetchAll(PDO::FETCH_OBJ);
                foreach($set_sql_temp as $key_value){
                    $save_template_field = "INSERT INTO `tbl_allcare_template`( `date`, `copy_from_enc`, `copy_from_id`, `copy_to_enc`, `copy_to_id`, `form_name`, `user`) VALUES (NOW(),$eid,$new_id,$copy_to_encounter,$new_formid,'$key_value->title','$provider')"; 
                    $get_stmt29 = $db->prepare($save_template_field) ;
                    $get_stmt29->execute(); 
                }
            }
        }
        
        //  for ros
        $sql8 = "SELECT form_id,pid  from forms WHERE encounter = $eid and deleted = 0 and form_name = 'Allcare Review Of Systems' and formdir='allcare_ros' GROUP BY form_name ORDER BY id DESC";
        $stmt8 = $db->prepare($sql8);
        $stmt8->execute();  
        $datacheck5 = $stmt8->fetchAll(PDO::FETCH_OBJ);
        if(!empty($datacheck5)){
            $ros_form_id = $datacheck5[0]->form_id;
            $sql71 = "SELECT * FROM tbl_form_allcare_ros WHERE id= '".$datacheck5[0]->form_id."' ";
            $stmt71 = $db->prepare($sql71);
            $stmt71->execute();  
            $copy_from_ros = $stmt71->fetchAll(PDO::FETCH_OBJ);
            if(!empty($copy_from_ros)){
                $ros_sql = "SELECT form_id,pid  from forms WHERE encounter = $copy_to_encounter and deleted = 0 and form_name = 'Allcare Review Of Systems' and formdir='allcare_ros' GROUP BY form_name ORDER BY id DESC";
                $rosstmt = $db->prepare($ros_sql);
                $rosstmt->execute();  
                $ros_copy_to = $rosstmt->fetchAll(PDO::FETCH_OBJ);
                $copy_to_ros = array();
                $check1 = 0;
                if(!empty($ros_copy_to)){
                    //delete previous data
                    $copy_to_id = $ros_copy_to[0]->form_id;
                    $delete_ros = "delete from tbl_form_allcare_ros where id='".$ros_copy_to[0]->form_id."'";
                    $delete_stmt1 = $db->prepare($delete_ros);
                    $delete_stmt1->execute();
                    $check1 = 1;
                }else{
                    //create new ros
                    $new_sql = "select max(form_id)as new_form from forms where form_name='Allcare Review Of Systems' AND formdir='allcare_ros'";
                    $new_stmt = $db->prepare($new_sql);
                    $new_stmt->execute();
                    $new_res = $new_stmt->fetchAll(PDO::FETCH_OBJ);
                    $new_fid = $new_res[0]->new_form;
                    $copy_to_id = $new_fid + 1;

                    $form_ins = "INSERT INTO  forms (date,encounter,form_name,form_id,pid,user,groupname,authorized,deleted,formdir) VALUES (NOW(),$copy_to_encounter,'Allcare Review Of Systems',$copy_to_id,'".$datacheck5[0]->pid."','$provider','default',1,0,'allcare_ros')";
                    $form_insstmt = $db->prepare($form_ins);
                    $form_insstmt->execute(); 
                }
                foreach($copy_from_ros as $val5 ){
                    foreach($val5 as $key6 => $value6){
                        if($key6 != 'id'):
                            $fields .= $key6.",";
                            $fields1 = rtrim($fields,",");
                            $field_value .= "'".$value6."',";
                        endif;
                    }
                }
                $field_value1 = rtrim($field_value,",");

                $ins_ros="insert into tbl_form_allcare_ros (id,$fields1) values ($copy_to_id,$field_value1)";
                $ins_stmt1 = $db->prepare($ins_ros);
                $ins_stmt1->execute();
                $check_data2 = 1;
                if($check1 == 0 ){
                    // insert template
                    $save_template_field = "INSERT INTO `tbl_allcare_template`( `date`, `copy_from_enc`, `copy_from_id`, `copy_to_enc`, `copy_to_id`, `form_name`, `user`) VALUES (NOW(),$eid,'".$datacheck5[0]->form_id."',$copy_to_encounter,$copy_to_id,'Allcare Review Of Systems','$provider')";
                    $get_stmt28 = $db->prepare($save_template_field) ;
                    $get_stmt28->execute(); 
                }else{
                    // update template
                    $save_template_field = "UPDATE `tbl_allcare_template` SET date = 'NOW()',copy_from_enc = $eid ,copy_from_id =". $datacheck5[0]->form_id ." WHERE copy_to_enc = $copy_to_encounter and copy_to_id = $copy_to_id and form_name ='Allcare Review Of Systems'";
                    $get_stmt28 = $db->prepare($save_template_field) ;
                    $get_stmt28->execute(); 
                }
                //this is for finalized ,pending  and log

                $form_flag="SELECT * 
                                FROM  `tbl_allcare_formflag` 
                                WHERE form_id ='".$datacheck5[0]->form_id."' AND form_name='Allcare Review Of Systems' order by id desc";
                $form_flagstmt7 = $db->prepare($form_flag);
                $form_flagstmt7->execute();  
                $form_flag_res = $form_flagstmt7->fetchAll(PDO::FETCH_OBJ);
                foreach($form_flag_res as $val2){
                    $finalized=$val2->finalized;
                    $pending=$val2->pending;
                }
                $logdata= array(); 
                $data = "SELECT logdate from `tbl_allcare_formflag` WHERE  form_id=".$copy_to_id;
                $data_stmt7 = $db->prepare($data);
                $data_stmt7->execute();  
                $form_flag_res1 = $data_stmt7->fetchAll(PDO::FETCH_OBJ);
                //echo "<pre>"; print_r($form_flag_res1); echo "</pre>";
                foreach ($form_flag_res1 as $value2) {
                        $array =  unserialize($value2->logdate);
                        $count= count($array);
                }


                $res = "SELECT * FROM `tbl_allcare_formflag` WHERE form_id = '$copy_to_id'";
                $row1 = $db->prepare($res);
                $row1->execute();  
                $row1_res1 = $row1->fetchAll(PDO::FETCH_OBJ);
                if(empty($row1_res1)){
                    $count = 0;

                    $array2[] = array( 'authuser' =>$provider_id,'pending' => $pending,'finalized' => $finalized, 'date' => date("Y/m/d"), 'action'=>'Copied' ,'count'=> $count+1);
//                    $logdata = array_merge_recursive($array, $array2);
                    $logdata=  serialize($array2);
                    $query1 = "INSERT INTO tbl_allcare_formflag ( " .
                            "encounter_id,form_id, form_name,pending,finalized, logdate" .
                            ") VALUES ( " .
                            "".$copy_to_encounter.",'$copy_to_id', 'Allcare Review Of Systems','$pending', '$finalized', '".$logdata."' " .
                            ")";

                    $log_stmt = $db->prepare($query1);
                    $log_stmt->execute();  
                    $check_data = 1;
                }else{
                    $count = isset($count)? $count: 0;

                    $array2[] = array( 'authuser' =>$provider_id,'pending' => $pending,'finalized' => $finalized, 'date' => date("Y/m/d"), 'action'=>'Copied' ,'count'=> $count+1);
                    $logdata = array_merge_recursive($array, $array2);
                    $logdata= ($logdata? serialize($logdata): serialize($array2) );
                    $query1 = "UPDATE tbl_allcare_formflag SET pending ='$pending',finalized = '$finalized',logdate=  '".$logdata."' WHERE encounter_id ='$copy_to_encounter' and form_id = '$copy_to_id' and form_name = 'Allcare Review Of Systems'"; 
                    $log_stmt = $db->prepare($query1);
                    $log_stmt->execute();  
                    $check_data = 1;
                }
                   
                if($pending == 'Y')
                    $formstatus = 'pending';
                elseif($finalized == 'Y')
                    $formstatus = 'finalized';

                if($finalized == 'Y'  && $pending == 'Y')
                    $formstatus = 'finalized|pending';
                if(!empty($new_formid)){
                    $toid = $new_formid;
                }else{
                    $toid = $toid;
                }
                $result21="select * from lbf_data where form_id='".$toid."'  AND `field_id`='ros_stat'";
                $sqlstmt31= $db->prepare($result21) ;
                $data31 = $sqlstmt31->execute();
                $formid_val = $sqlstmt31->fetchAll(PDO::FETCH_OBJ);

                if(!empty($formid_val)){
                    $sql5 = "update `lbf_data`  SET  `field_value`='".$formstatus."' where `form_id`= ".$toid." AND `field_id`='ros_stat'";
                    $sqlstmt5 = $db->prepare($sql5) ;
                    $data5 = $sqlstmt5->execute();
                }else {
                   $sql5 = "INSERT into `lbf_data`(`form_id`, `field_id`,`field_value`) VALUES(".$toid.",'ros_stat','".$formstatus."')";
                   $sqlstmt5 = $db->prepare($sql5) ;
                   $data5 = $sqlstmt5->execute();
                }

                if($check1 == 0 ){
                    $save_template_field = "UPDATE `tbl_allcare_template` SET date = 'NOW()',copy_from_enc = $eid ,copy_from_id =". $datacheck5[0]->form_id ." WHERE copy_to_enc = $copy_to_encounter and copy_to_id = $copy_to_id and form_name ='Allcare Review Of Systems'";
                    $get_stmt30 = $db->prepare($save_template_field) ;
                    $get_stmt30->execute(); 
                }else{
                    $save_template_field = "INSERT INTO `tbl_allcare_template`( `date`, `copy_from_enc`, `copy_from_id`, `copy_to_enc`, `copy_to_id`, `form_name`, `user`) VALUES (NOW(),$eid,". $datacheck5[0]->form_id .",$copy_to_encounter,$copy_to_id,'Allcare Review Of Systems','$provider')";
                    $get_stmt31 = $db->prepare($save_template_field) ;
                    $get_stmt31->execute(); 
                }
            }
        }else{
            $form_id = 0;
        }
        
        // for physical form
        $sql7 = "SELECT form_id ,pid from forms WHERE encounter = $eid and deleted = 0 and form_name = 'Allcare Physical Exam' and formdir='allcare_physical_exam' GROUP BY form_name
                                         ORDER BY id DESC ";
        $stmt7 = $db->prepare($sql7);
        $stmt7->execute();  
        $datacheck4 = $stmt7->fetchAll(PDO::FETCH_OBJ);

        if(!empty($datacheck4)):
            $form_id2 = $datacheck4[0]->form_id;
            $sql71 = "SELECT * FROM tbl_form_physical_exam WHERE forms_id= '".$datacheck4[0]->form_id."' ";
            $stmt71 = $db->prepare($sql71);
            $stmt71->execute();  
            $datacheck5 = $stmt71->fetchAll(PDO::FETCH_OBJ);

            $sql = "SELECT form_id,pid  from forms WHERE encounter = $copy_to_encounter and deleted = 0 and form_name = 'Allcare Physical Exam' and formdir='allcare_physical_exam' GROUP BY form_name
                         ORDER BY id DESC ";
            $stmt = $db->prepare($sql);
            $stmt->execute();  
            $copy_to_fid = $stmt->fetchAll(PDO::FETCH_OBJ);
            $copy_to_details=array();
            if(!empty($copy_to_fid)):
                $copy_to_id=$copy_to_fid[0]->form_id;
                $sql72 = "SELECT * FROM tbl_form_physical_exam WHERE forms_id= '".$copy_to_fid[0]->form_id."' ";
                $stmt72 = $db->prepare($sql72);
                $stmt72->execute();  
                $copy_to_details = $stmt72->fetchAll(PDO::FETCH_OBJ);
             else: 
                $new_sql = "select max(form_id)as new_form from forms where form_name='Allcare Physical Exam' AND formdir='allcare_physical_exam'";
                $new_stmt = $db->prepare($new_sql);
                $new_stmt->execute();
                $new_res = $new_stmt->fetchAll(PDO::FETCH_OBJ);
                $new_fid= $new_res[0]->new_form;
                $new_id1=++$new_fid;

                $form_ins = "INSERT INTO  forms (date,encounter,form_name,form_id,pid,user,groupname,authorized,deleted,formdir) VALUES (NOW(),$copy_to_encounter,'Allcare Physical Exam',$new_id1,'".$datacheck4[0]->pid."','$provider','default',1,0,'allcare_physical_exam')";
                $form_insstmt = $db->prepare($form_ins);
                $form_insstmt->execute();  
                $copy_to_id=$new_id1;
            endif;

            if(!empty($copy_to_details)):
                $form_id1 = $copy_to_details[0]->forms_id;
                $delete_pe="delete from tbl_form_physical_exam where forms_id=$copy_to_id";
                $pe_insstmt1 = $db->prepare($delete_pe);
                $pe_insstmt1->execute();  
                foreach($datacheck5 as $value1){
                   //$comments=str_replace("'", "\\'", $value1->comments);
                    $pe_ins = "INSERT INTO tbl_form_physical_exam (forms_id, line_id, wnl, abn, diagnosis, comments) VALUES($copy_to_id,'".$value1->line_id."', '".$value1->wnl."', '".$value1->abn."', '".$value1->diagnosis."', '".$value1->comments."' )";
                    $pe_insstmt = $db->prepare($pe_ins);
                    $pe_insstmt->execute();  
                } 


               //this is for finalized ,pending  and log

                $form_flag="SELECT * 
                                FROM  `tbl_allcare_formflag` 
                                WHERE form_id ='".$datacheck5[0]->forms_id."' AND form_name='Allcare Physical Exam' order by id desc limit 0,1";
                $form_flagstmt7 = $db->prepare($form_flag);
                $form_flagstmt7->execute();  
                $form_flag_res = $form_flagstmt7->fetchAll(PDO::FETCH_OBJ);
                foreach($form_flag_res as $val2){
                $pfinalized=$val2->finalized;
                $ppending=$val2->pending;
                }
                $count  = 0;
                $array = array();
                $logdata= array(); 
                $data = "SELECT logdate from `tbl_allcare_formflag` WHERE  form_id=".$copy_to_id;
                $data_stmt7 = $db->prepare($data);
                $data_stmt7->execute();  
                $form_flag_res1 = $data_stmt7->fetchAll(PDO::FETCH_OBJ);
                //echo "<pre>"; print_r($form_flag_res1); echo "</pre>";
                foreach ($form_flag_res1 as $value2) {
                        $array =  unserialize($value2->logdate);
                        $count= count($array);
                }


                $res = "SELECT * FROM `tbl_allcare_formflag` WHERE encounter_id ='$copy_to_encounter' and form_id = '$copy_to_id' and form_name = 'Allcare Physical Exam'  order by id desc limit 0,1";
                $row1 = $db->prepare($res);
                $row1->execute();  
                $row1_res1 = $row1->fetchAll(PDO::FETCH_OBJ);
                if(empty($row1_res1)){
                    $count = 0;

                    $array2[] = array( 'authuser' =>$provider,'pending' => $ppending,'finalized' => $pfinalized, 'date' => date("Y/m/d"), 'action'=>'Copied' ,'count'=> $count+1);
//                    $logdata = array_merge_recursive($array, $array2);
                    $logdata=  serialize($array2);
                    $query1 = "INSERT INTO tbl_allcare_formflag ( " .
                            "encounter_id,form_id, form_name,pending,finalized, logdate" .
                            ") VALUES ( " .
                            "".$copy_to_encounter.",'$copy_to_id', 'Allcare Physical Exam','$ppending', '$pfinalized', '".$logdata."' " .
                            ")";

                    $log_stmt = $db->prepare($query1);
                    $log_stmt->execute();  
                    $check_data = 1;
                }else{
                    $count = isset($count)? $count: 0;

                    $array2[] = array( 'authuser' =>$provider,'pending' => $ppending,'finalized' => $pfinalized, 'date' => date("Y/m/d"), 'action'=>'Copied' ,'count'=> $count+1);
                    $logdata = array_merge_recursive($array, $array2);
                    $logdata= ($logdata? serialize($logdata): serialize($array2) );
                    $query1 = "UPDATE tbl_allcare_formflag SET pending ='$ppending',finalized = '$pfinalized',logdate=  '".$logdata."' WHERE encounter_id ='$copy_to_encounter' and form_id = '$copy_to_id' and form_name = 'Allcare Physical Exam'"; 
                    $log_stmt = $db->prepare($query1);
                    $log_stmt->execute();  
                    $check_data = 1;
                }
                //print_r($datacheck5);
                $formstatus = '';
                if($ppending == 'Y' || $ppending == 'y')
                    $formstatus = 'pending';
                elseif($pfinalized == 'Y' || $pfinalized == 'y')
                    $formstatus = 'finalized';

                if(($pfinalized == 'Y'  && $ppending == 'Y') || $pfinalized == 'y'  && $ppending == 'y') 
                    $formstatus = 'finalized|pending';
                
                    $formstatus = '';
                if(!empty($new_formid)){
                    $toid = $new_formid;
                }else{
                    $toid = $toid;
                }
                $result22="select * from lbf_data where form_id='".$toid."'  AND `field_id`='physical_exam_stat'";
                $sqlstmt32= $db->prepare($result22) ;
                $data32 = $sqlstmt32->execute();
                $formid_val = $sqlstmt32->fetchAll(PDO::FETCH_OBJ);

                if(!empty($formid_val)){
                    $sql51 = "update `lbf_data`  SET  `field_value`='".$formstatus."' where `form_id`= ".$toid." AND `field_id`='physical_exam_stat'";
                    $sqlstmt51 = $db->prepare($sql51) ;
                    $data51 = $sqlstmt51->execute();
                }else {
                    $sql51 = "INSERT into `lbf_data`(`form_id`, `field_id`,`field_value`) VALUES(".$toid.",'physical_exam_stat','".$formstatus."')";
                   $sqlstmt51 = $db->prepare($sql51) ;
                   $data51 = $sqlstmt51->execute();
                }
                $save_template_field = "UPDATE `tbl_allcare_template` SET date = 'NOW()',copy_from_enc = $eid ,copy_from_id =". $datacheck5[0]->forms_id ." WHERE copy_to_enc = $copy_to_encounter and copy_to_id = $copy_to_id and form_name ='Allcare Physical Exam'";
                $get_stmt28 = $db->prepare($save_template_field) ;
                $get_stmt28->execute(); 
            else:
                foreach($datacheck5 as $value1){
                    //$comments=str_replace("'", "\\'", $value1->comments);
                    $pe_ins = "INSERT INTO tbl_form_physical_exam (forms_id, line_id, wnl, abn, diagnosis, comments) VALUES($copy_to_id,'".$value1->line_id."', '".$value1->wnl."', '".$value1->abn."', '".$value1->diagnosis."', '".$value1->comments."' )";
                    $pe_insstmt = $db->prepare($pe_ins);
                    $pe_insstmt->execute();  
                } 

                $form_flag="SELECT * 
                            FROM  `tbl_allcare_formflag` 
                            WHERE form_id ='".$datacheck5[0]->forms_id."' AND form_name='Allcare Physical Exam' order by id desc";
                $form_flagstmt7 = $db->prepare($form_flag);
                $form_flagstmt7->execute();  
                $form_flag_res = $form_flagstmt7->fetchAll(PDO::FETCH_OBJ);
                foreach($form_flag_res as $val2){
                    $pfinalized=$val2->finalized;
                    $ppending=$val2->pending;
                }
                $logdata= array(); $array=array();
                $data = "SELECT logdate from `tbl_allcare_formflag` WHERE  form_id=".$copy_to_id;
                $data_stmt7 = $db->prepare($data);
                $data_stmt7->execute();  
                $form_flag_res1 = $data_stmt7->fetchAll(PDO::FETCH_OBJ);
                //echo "<pre>"; print_r($form_flag_res1); echo "</pre>";
                foreach ($form_flag_res1 as $value2) {
                        $array =  unserialize($value2->logdate);
                        $count= count($array);
                    }


                $res = "SELECT * FROM `tbl_allcare_formflag` WHERE form_id = '$copy_to_id'";
                $row1 = $db->prepare($res);
                $row1->execute();  
                $row1_res1 = $data_stmt7->fetchAll(PDO::FETCH_OBJ);
                $count = isset($count)? $count: 0;

                $array2[] = array( 'authuser' =>$provider,'pending' => $ppending,'finalized' => $pfinalized, 'date' => date("Y/m/d"), 'action'=>'Copied' ,'count'=> $count+1);
                $logdata = array_merge_recursive($array, $array2);
                $logdata= ($logdata? serialize($logdata): serialize($array2) );
                 $query1 = "INSERT INTO tbl_allcare_formflag ( " .
                        "encounter_id,form_id, form_name,pending,finalized, logdate" .
                        ") VALUES ( " .
                        "".$copy_to_encounter.",'$copy_to_id', 'Allcare Physical Exam','$ppending', '$pfinalized', '".$logdata."' " .
                        ")";
                $log_stmt = $db->prepare($query1);
                $log_stmt->execute(); 
                 $formstatus = '';
                if($ppending == 'Y' || $ppending == 'y')
                    $formstatus = 'pending';
                elseif($pfinalized == 'Y' || $pfinalized == 'y')
                    $formstatus = 'finalized';

                if(($pfinalized == 'Y'  && $ppending == 'Y') || $pfinalized == 'y'  && $ppending == 'y') 
                    $formstatus = 'finalized|pending';
                
                    $formstatus = '';
                if(!empty($new_formid)){
                    $toid = $new_formid;
                }else{
                    $toid = $toid;
                }
                $result22="select * from lbf_data where form_id='".$toid."'  AND `field_id`='physical_exam_stat'";
                $sqlstmt32= $db->prepare($result22) ;
                $data32 = $sqlstmt32->execute();
                $formid_val = $sqlstmt32->fetchAll(PDO::FETCH_OBJ);

                if(!empty($formid_val)){
                    $sql51 = "update `lbf_data`  SET  `field_value`='".$formstatus."' where `form_id`= ".$toid." AND `field_id`='physical_exam_stat'";
                    $sqlstmt51 = $db->prepare($sql51) ;
                    $data51 = $sqlstmt51->execute();
                }else {
                   $sql51 = "INSERT into `lbf_data`(`form_id`, `field_id`,`field_value`) VALUES(".$toid.",'physical_exam_stat','".$formstatus."')";
                   $sqlstmt51 = $db->prepare($sql51) ;
                   $data51 = $sqlstmt51->execute();
                }
                $save_template_field = "UPDATE `tbl_allcare_template` SET date = 'NOW()',copy_from_enc = $eid ,copy_from_id =". $datacheck5[0]->forms_id ." WHERE copy_to_enc = $copy_to_encounter and copy_to_id = $copy_to_id and form_name ='Allcare Physical Exam'";
                $get_stmt28 = $db->prepare($save_template_field) ;
                $get_stmt28->execute(); 
                
                $check_data = 1;
                $save_template_field = "INSERT INTO `tbl_allcare_template`( `date`, `copy_from_enc`, `copy_from_id`, `copy_to_enc`, `copy_to_id`, `form_name`, `user`) VALUES (NOW(),$eid,$form_id2,$copy_to_encounter,$copy_to_id,'Allcare Physical Exam','$provider')";
                $get_stmt29 = $db->prepare($save_template_field) ;
                $get_stmt29->execute(); 
            endif;
        else:
            $form_id = 0;
          //  echo "There is no physicalexam form to copy";
        endif;

        if(($check_data1 || $check_data2 || $check_data3 )== 1){
                 $patientres =  '[{"id":"1"}]';
           echo  $patientresult = GibberishAES::enc($patientres, $apikey);
        }else{
                 $patientres = '[{"id":"0"}]';
           echo  $patientresult = GibberishAES::enc($patientres, $apikey);
        }
    }catch(PDOException $e) {
       $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
       echo  $patientresult = GibberishAES::enc($error, $apikey);
    }
    
}

/* ============ Hema methods start here ================== */

//to get Public Patient from OpenEMR
function getPublicPatientDetails($fname, $lname, $dob, $ss)
{
    
	$sql = "select * from patient_data WHERE fname=:fname AND lname=:lname AND dob=:dob AND ss=:ss ";      
    
        try 
        {
            $db = getConnection();
            $stmt = $db->prepare($sql) ;
            $stmt->bindParam("fname", $fname);            
            $stmt->bindParam("lname", $lname);  
            $stmt->bindParam("dob", $dob);            
            $stmt->bindParam("ss", $ss); 
            $stmt->execute();                       
             
            $patients = $stmt->fetchAll(PDO::FETCH_OBJ);                        
            
            if($patients)
            {
                //returns patients 
                echo json_encode($patients); 
            }
            else
            {
                //echo 'No patients available';
                echo '[{"id":"0"}]';
            }
        } 
        catch(PDOException $e) 
        {
            
            echo '{"error":{"text":'. $e->getMessage() .'}}'; 
        }
}



//to get all patients from OpenEMR
function getAllPatientsDetails()
{
    
	$sql = "select pid,fname,lname,dob from patient_data WHERE  practice_status = 'YES' AND deceased_stat != 'YES' order by lname,fname";      
        $count= "select count(*) from patient_data WHERE  practice_status = 'YES' AND deceased_stat != 'YES' order by lname,fname";
        try 
        {
            $db = getConnection();
            $stmt = $db->prepare($sql) ;
            $stmt->bindParam("all", $count);            
            $stmt->execute();                       
             
            $patients = $stmt->fetchAll(PDO::FETCH_OBJ);                        
            
            if($patients)
            {
                //returns patients 
                echo json_encode($patients); 
            }
            else
            {
                //echo 'No patients available';
                echo '[{"id":"0"}]';
            }
        } 
        catch(PDOException $e) 
        {
            
            echo '{"error":{"text":'. $e->getMessage() .'}}'; 
        }
}

// method to get list of all pharmacies
function getAllPharmacies()
{
    	
	$sql = "select id,name from pharmacies";      
    $count= "select count(*) from pharmacies";
        try 
        {
            $db = getConnection();
            $stmt = $db->prepare($sql) ;
            $stmt->bindParam("all", $count);            
            $stmt->execute();                       
             
            $pharmacies = $stmt->fetchAll(PDO::FETCH_OBJ);                        
            
            if($pharmacies)
            {
                //returns pharmacies 
                echo json_encode($pharmacies); 
            }
            else
            {
                //echo 'No pharmacy available';
                echo '[{"id":"0"}]';
            }
        } 
        catch(PDOException $e) 
        {
            
            echo '{"error":{"text":'. $e->getMessage() .'}}'; 
        }
}


function getAllAddressbookTypes(){

	$sql = "SELECT seq,title,option_id FROM `list_options` WHERE list_id ='abook_type'";      
    $count= "SELECT COUNT(*) FROM `list_options` WHERE list_id ='abook_type'";
        try 
        {
            $db = getConnection();
            $stmt = $db->prepare($sql) ;
            $stmt->bindParam("all", $count);            
            $stmt->execute();                       
             
            $abookTypes = $stmt->fetchAll(PDO::FETCH_OBJ);                        
            
            if($abookTypes)
            {
                //returns abookTypes 
                echo json_encode($abookTypes); 
            }
            else
            {
                //echo 'No Addressbook Types available';
                echo '[{"id":"0"}]';
            }
        } 
        catch(PDOException $e) 
        {
            
            echo '{"error":{"text":'. $e->getMessage() .'}}'; 
        }
}

function getAllProviders(){
         $key = 'rotcoderaclla';
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
	$sql = "SELECT id, fname,lname FROM `users`  WHERE active=1 AND authorized=1";      
        $count= "SELECT COUNT(*) FROM `users`  WHERE active=1 AND authorized=1";
        try 
        {
            $db = getConnection();
            $stmt = $db->prepare($sql) ;
            $stmt->bindParam("all", $count);            
            $stmt->execute();                       
             
            $providers = $stmt->fetchAll(PDO::FETCH_OBJ);                        
            
            if($providers)
            {
                //returns providers 
                $data = json_encode($providers); 
                echo $datares = GibberishAES::enc($data, $key);
            }
            else
            {
                //echo 'No provider available';
                $data = '[{"id":"0"}]';
                echo $datares = GibberishAES::enc($data, $key);
            }
        } 
        catch(PDOException $e) 
        {
            
            $data = '{"error":{"text":'. $e->getMessage() .'}}'; 
            echo $datares = GibberishAES::enc($data, $key);
        }
}

function getAllInsuranceCompanies(){

	$sql = "SELECT id,name FROM `insurance_companies`";      
    $count= "SELECT COUNT(*) FROM insurance_companies";
        try 
        {
            $db = getConnection();
            $stmt = $db->prepare($sql) ;
            $stmt->bindParam("all", $count);            
            $stmt->execute();                       
             
            $insuranceCompanies = $stmt->fetchAll(PDO::FETCH_OBJ);                        
            
            if($insuranceCompanies)
            {
                //returns insurance Companies 
                echo json_encode($insuranceCompanies); 
            }
            else
            {
                //echo 'No insurance Company available';
                echo '[{"id":"0"}]';
            }
        } 
        catch(PDOException $e) 
        {
            
            echo '{"error":{"text":'. $e->getMessage() .'}}'; 
        }
}
function getAllCategories(){

	$sql ="SELECT id, name FROM categories";
	$count = "SELECT COUNT(*) FROM categories";
	
	try{
		$db = getConnection();
		$stmt = $db->prepare($sql);
		$stmt->bindParam("all", $count);
		$stmt->execute();
		
		$categoriesdata = $stmt->fetchAll(PDO::FETCH_OBJ);
		
		if($categoriesdata)
		{
			// returns categories data
			echo json_encode($categoriesdata);
		}
		else
		{
			// No Such Category Available 
			echo '[{"id":"0"}]';
		}
	} catch(PDOExecution $e)
	{
		echo '{"error":{"text":'.$e->getMessage().'}}';
	}
}

function getAllHHA(){

	$sql ="SELECT id, name FROM tbl_home_health_agencies";
	$count = "SELECT COUNT(*) FROM tbl_home_health_agencies";
	
	try{
		$db = getConnection();
		$stmt = $db->prepare($sql);
		$stmt->bindParam("all", $count);
		$stmt->execute();
		
		$hha = $stmt->fetchAll(PDO::FETCH_OBJ);
                       
		if($hha)
		{
			// returns $hha data
			echo json_encode($hha);
		}
		else
		{
			// No Such hha Available 
			echo '[{"id":"0"}]';
		}
	} catch(PDOExecution $e)
	{
		echo '{"error":{"text":'.$e->getMessage().'}}';
	}
}


// to get all organizations of Addressbook 
function getAllAbookOrg($name){
	$sql ="SELECT distinct(organization) FROM users WHERE abook_type =:name AND organization IS NOT NULL";
	
	try{
		$db = getConnection();
		$stmt = $db->prepare($sql);
		$stmt->bindParam("name", $name);
		$stmt->execute();
		
		$orgdata = $stmt->fetchAll(PDO::FETCH_OBJ);
		
		if($orgdata)
		{
			// returns organization data
            // return $string = json_encode($orgdata);
			//echo '{data:'.$string.'}';
			if (isset($_GET['callback']))
				{
					$callback = filter_var($_GET['callback'], FILTER_SANITIZE_STRING);
				}
				$string = 'success';
				 
				echo $callback. '('.json_encode($orgdata).');';
			
		}
		else
		{
			// No Such organization Available 
			echo '[{"id":"0"}]';
		}
	} catch(PDOExecution $e)
	{
		echo '{"error":{"text":'.$e->getMessage().'}}';
	}
}


// To retrieve Single Patient Details

function getPatientData($pid){

	$sql = "SELECT * from patient_data WHERE pid = :pid";
	 
	try 
        {
            $db = getConnection();
            $stmt = $db->prepare($sql) ;
            $stmt->bindParam("pid", $pid);            
            $stmt->execute();                       
             
            $patientdata = $stmt->fetchAll(PDO::FETCH_OBJ);                        
            
            if($patientdata)
            {
                //returns patientdata 
                echo json_encode($patientdata); 
            }
            else
            {
                //echo 'No Such patient data available';
                echo 'No Such patient data available';
            }
        } 
        catch(PDOException $e) 
        {
            
            echo '{"error":{"text":'. $e->getMessage() .'}}'; 
        }
}

// method to get Single Pharmacy Details
function getPharmacyData($pharmacyid)
{
    	
	$sql = "select * from pharmacies where id = :pharmacyid";      
    
        try 
        {
            $db = getConnection();
            $stmt = $db->prepare($sql) ;
            $stmt->bindParam("pharmacyid", $pharmacyid);            
            $stmt->execute();                       
             
            $pharmacydata = $stmt->fetchAll(PDO::FETCH_OBJ);                        
            
            if($pharmacydata)
            {
                //returns pharmacydata 
                echo json_encode($pharmacydata); 
            }
            else
            {
                //echo 'No  Such Pharmacy available';
                echo ' No Such Pharmacy data available';
            }
        } 
        catch(PDOException $e) 
        {
            
            echo '{"error":{"text":'. $e->getMessage() .'}}'; 
        }
}

// To retrieve Single Provider Details

function getProviderData($providerid){

	$sql = "SELECT fname,lname FROM users WHERE active=1 AND authorized=1 AND id = :providerid";
	 
	try 
        {
            $db = getConnection();
            $stmt = $db->prepare($sql) ;
            $stmt->bindParam("providerid", $providerid);            
            $stmt->execute();                       
             
            $providerdata = $stmt->fetchAll(PDO::FETCH_OBJ);                        
            
            if($providerdata)
            {
                //returns providerdata 
                echo json_encode($providerdata); 
            }
            else
            {
                //echo 'No Such Provider data available';
                echo 'No Such provider data available';
            }
        } 
        catch(PDOException $e) 
        {
            
            echo '{"error":{"text":'. $e->getMessage() .'}}'; 
        }
}

// method to get Single Addressbook type Details
function getAddressbookTypeData($abooktypeid)
{
    	
	$sql = "SELECT title FROM list_options WHERE list_id = 'abook_type' AND seq =:abooktypeid";      
    
        try 
        {
            $db = getConnection();
            $stmt = $db->prepare($sql) ;
            $stmt->bindParam("abooktypeid", $abooktypeid);            
            $stmt->execute();                       
             
            $abooktypedata = $stmt->fetchAll(PDO::FETCH_OBJ);                        
            
            if($abooktypedata)
            {
                //returns abooktypedata 
                echo json_encode($abooktypedata); 
            }
            else
            {
                //echo 'No  Such address book type available';
                echo ' No Such Addressbook Type data available';
            }
        } 
        catch(PDOException $e) 
        {
            
            echo '{"error":{"text":'. $e->getMessage() .'}}'; 
        }
}

// method to get Single Insurance Company  Details
function getInsuranceCompanyData($icompanyid)
{
    	
	$sql = "SELECT id,name FROM insurance_companies WHERE id=:icompanyid";      
    
        try 
        {
            $db = getConnection();
            $stmt = $db->prepare($sql) ;
            $stmt->bindParam("icompanyid", $icompanyid);            
            $stmt->execute();                       
             
            $insurancecompanydata = $stmt->fetchAll(PDO::FETCH_OBJ);                        
            
            if($insurancecompanydata)
            {
                //returns insurance company data 
                echo json_encode($insurancecompanydata); 
            }
            else
            {
                //echo 'No Such Insurance Company available';
                echo ' No Such Insurance Company available';
            }
        } 
        catch(PDOException $e) 
        {
            echo '{"error":{"text":'. $e->getMessage() .'}}'; 
        }
}

// method to get Single Facility Details
function getFacilityData($ifacility)
{
    	
	$sql = "SELECT * FROM facility WHERE id=:ifacility";      
    
        try 
        {
            $db = getConnection();
            $stmt = $db->prepare($sql) ;
            $stmt->bindParam("ifacility", $ifacility);            
            $stmt->execute();                       
             
            $facilitydata = $stmt->fetchAll(PDO::FETCH_OBJ);                        
            
            if($facilitydata)
            {
                //returns facility data 
                echo json_encode($facilitydata); 
            }
            else
            {
                //echo 'No Such facility available';
                echo ' No Such Facility available';
            }
        } 
        catch(PDOException $e) 
        {
            echo '{"error":{"text":'. $e->getMessage() .'}}'; 
        }
}

// method to get list of all Face to Face Encounters of single patient
function getAllFacetoFaceEncounters($pid)
{
    	
    $sql = "SELECT form_encounter.reason, tbl_form_facetoface. pid, tbl_form_facetoface. encounter, tbl_form_facetoface. id
            FROM form_encounter
            INNER JOIN tbl_form_facetoface 
            ON tbl_form_facetoface.encounter = form_encounter.encounter AND tbl_form_facetoface.pid =:pid";      
    
        try 
        {
            $db = getConnection();
            $stmt = $db->prepare($sql) ;
            $stmt->bindParam("pid", $pid);            
            $stmt->execute();                       
             
            $ftofenconters = $stmt->fetchAll(PDO::FETCH_OBJ);                        
            
            if($ftofenconters)
            {
                //returns face to face encounters 
                echo json_encode($ftofenconters); 
            }
            else
            {
                //echo 'No face to face encounters available';
                echo '[{"id":"0"}]';
            }
        } 
        catch(PDOException $e) 
        {
            
            echo '{"error":{"text":'. $e->getMessage() .'}}'; 
        }
}
// method to get Patient Insurance Data Details
function getPatientInsuranceData($pid, $pidata)
{
    $sql = "SELECT * 
			FROM insurance_companies a
			INNER JOIN insurance_data b
			WHERE b.provider = a.id
			AND b.pid =:pid
			AND a.id =:pidata";
        try 
        {
            $db = getConnection();
            $stmt = $db->prepare($sql) ;
            $stmt->bindParam("pidata", $pidata);   
            $stmt->bindParam("pid", $pid);			
            $stmt->execute();                       
             
            $patientinsurancedata = $stmt->fetchAll(PDO::FETCH_OBJ);                        
			
			if($patientinsurancedata)
            {
                //returns patient insurance data 
               echo json_encode($patientinsurancedata); 
				
            }
            else
            {
                //echo 'No Insurance  available for this Patient';
                echo ' No Insurance  available for this Patient';
            }
        } 
        catch(PDOException $e) 
        {
            echo '{"error":{"text":'. $e->getMessage() .'}}'; 
        }
}

// To get State name from State code
function getStateName($sname){
	 	
	$sql = "SELECT title FROM list_options WHERE list_id='state' AND option_id=:sname ";      
    
        try 
        {
            $db = getConnection();
            $stmt = $db->prepare($sql) ;
            $stmt->bindParam("sname", $sname);            
            $stmt->execute();                       
             
            $statename = $stmt->fetchAll(PDO::FETCH_OBJ);                        
            
            if($statename)
            {
                //returns State Name 
                echo json_encode($statename); 
            }
            else
            {
                //echo 'No Such State Name  available';
                echo ' No Such State Name available';
            }
        } 
        catch(PDOException $e) 
        {
            echo '{"error":{"text":'. $e->getMessage() .'}}'; 
        }
}
// Retrieve form id based on encounter id
function getencounteredFormId($eid){

	$sql = "SELECT * from tbl_form_facetoface WHERE encounter = :eid";
	 
	try 
        {
            $db = getConnection();
            $stmt = $db->prepare($sql) ;
            $stmt->bindParam("encounter", $eid);            
            $stmt->execute();                       
             
            $formdata = $stmt->fetchAll(PDO::FETCH_OBJ);                        
            
            if($formdata)
            {
                //returns patientdata 
                echo json_encode($formdata); 
            }
            else
            {
                //echo 'No Such Encounter data available';
                echo 'No Encounter Data found for this patient';
            }
        } 
        catch(PDOException $e) 
        {
            
            echo '{"error":{"text":'. $e->getMessage() .'}}'; 
        }
}
function getDynamicPatientAgencies($pid){
    $key = 'rotcoderaclla';
    // The default key size is 256 bits.
    $old_key_size = GibberishAES::size();
    GibberishAES::size(256);
    $db = getConnection();
    try{
        $sql = "SELECT agencyid,agency_admitdate ,agency_dischargedate , agency_isactive , agency_notes, agency_related_links
            FROM tbl_patientagency
            WHERE patientid =:pid order by id desc";
        $stmt = $db->prepare($sql) ;
        $stmt->bindParam("pid", $pid);  
        $db->query( "SET NAMES utf8"); 
        $stmt->execute();                       

        $agencydata = $stmt->fetchAll(PDO::FETCH_OBJ);  
        $fields_array = array();
        if(!empty($agencydata)){
            $i=0;
            foreach($agencydata as $agency_key){
                foreach($agency_key as $a_key =>$a_value){
                    $get_layout = "SELECT title FROM layout_options WHERE form_id='AGENCY' and field_id = '$a_key' " ;
                    $lstmt = $db->prepare($get_layout) ;
                    $db->query( "SET NAMES utf8"); 
                    $lstmt->execute();                       

                    $set_layout = $lstmt->fetchAll(PDO::FETCH_OBJ);  
                    if(!empty($set_layout)){
                        $title = $set_layout[0]->title;
                    }else{
                        $title = '';
                    }
                    if($a_key == 'agencyid'){
                        $get_agency = " SELECT Organization,CONCAT(title, fname,  ' ', lname ) AS name FROM users WHERE id= ".$a_value;
                        $astmt = $db->prepare($get_agency) ;
                        $db->query( "SET NAMES utf8"); 
                        $astmt->execute();                       

                        $set_agency = $astmt->fetchAll(PDO::FETCH_OBJ); 
                        if(!empty($set_agency)){
                            $fields_array[$i][$title] = $set_agency[0]->Organization;
                            $fields_array[$i]['Name'] = $set_agency[0]->name;
                        }
                    }else{
                        $fields_array[$i][$title] = $a_value;
                    }
                }
                $agencyid = $agency_key->agencyid;
                // to get addressbook data
                $get_user_abook = "SELECT (select title FROM list_options WHERE option_id=abook_type and list_id='abook_type') AS Address_Book, phonew1 AS work_phone, phonecell as phonecell, fax, email, street, city, state, zip FROM users WHERE id= ".$agencyid;
                $ustmt = $db->prepare($get_user_abook) ;
                $db->query( "SET NAMES utf8"); 
                $ustmt->execute();                       

                $set_user_abook = $ustmt->fetchAll(PDO::FETCH_OBJ); 
                if(!empty($set_user_abook)){
                    foreach($set_user_abook[0] as $ukey => $uvalue){
                        $fields_array[$i][$ukey] = $uvalue;
                    }
                }
                $getcolumnNames = "SELECT field_id FROM layout_options WHERE form_id = 'ADDRCA' ";
                $columnstmt = $db->prepare($getcolumnNames);
                $db->query( "SET NAMES utf8"); 
                $columnstmt->execute(); 
                $setcolumnNames = $columnstmt->fetchAll(PDO::FETCH_OBJ); 
                $array_query = '';
                $array_query2 = '';
                if(!empty($setcolumnNames)){
                    for($j = 0; $j < count($setcolumnNames); $j++){
                        foreach($setcolumnNames[$j] as $column){
                            $array_query2 .= $column.",";
                        }
                    }
                }
                $array_query = rtrim($array_query2,',');
                if(!empty($array_query)){
                    $get_abook_custom = "SELECT $array_query FROM tbl_addrbk_custom_attr_1to1 WHERE addrbk_type_id=".$agencyid;
                    $cstmt = $db->prepare($get_abook_custom) ;
                    $db->query( "SET NAMES utf8"); 
                    $cstmt->execute();                       
                    $set_abook_custom = $cstmt->fetchAll(PDO::FETCH_OBJ);
                    if(!empty($set_abook_custom)){
                        foreach($set_abook_custom[0] as $ckey => $cvalue){
                            $get_check_list = "SELECT data_type,list_id FROM layout_options WHERE field_id = '$ckey' AND form_id = 'ADDRCA' ";
                            $checkstmt = $db->prepare($get_check_list) ;
                            $db->query( "SET NAMES utf8"); 
                            $checkstmt->execute();                       
                            $set_check_list = $checkstmt->fetchAll(PDO::FETCH_OBJ);
                            if(!empty($set_check_list)){
                                if($set_check_list[0]->data_type == 28){
                                    if(!empty($cvalue)){
                                        $explodeval = explode("|", $cvalue);
                                        $statusname = '';
                                        $statustypes = array('current'=>'Current','quit' =>'Quit','never' => 'Never','not_applicable' => 'N/A');
                                        foreach($statustypes as $skey => $stype){
                                            if($explodeval[1] == $skey.$set_check_list[0]->field_id):
                                                $statusname = $stype;
                                            endif;
                                        }
                                        $smokingdata = $explodeval[0].str_repeat('&nbsp;', 5)."Status: ".$statusname. "  ".$explodeval[2];
                                        $fields_array[$i][$set_check_list[0]->title] = $smokingdata;
                                    }else{
                                        $fields_array[$i][$ckey] = $cvalue;
                                    }    
                                }elseif($set_check_list[0]->data_type == 10 || $set_check_list[0]->data_type == 11 || $set_check_list[0]->data_type == 38  ){
                                    if(!empty($cvalue)){
                                        $getprovidername = "SELECT  CONCAT(fname,' ',lname) as name FROM users WHERE id = $cvalue";
                                        $stmt6 = $db->prepare($getprovidername) ;
                                        $stmt6->execute();                       
                                        $setprovidername2  = $stmt6->fetchAll(PDO::FETCH_OBJ);
                                        $providername = $setprovidername2[0]->name;
                                        $fields_array[$i][$ckey] = ucwords($providername);
                                    }else{
                                        $fields_array[$i][$ckey] = ucwords($cvalue);
                                    }    
                                }elseif($set_check_list[0]->data_type == 14 ){
                                    if(!empty($cvalue)){
                                        $getprovidername = "SELECT  CONCAT(fname,' ',lname) as name, username, organization FROM users WHERE id = $cvalue";
                                        $stmt6 = $db->prepare($getprovidername) ;
                                        $stmt6->execute();                       
                                        $setprovidername2  = $stmt6->fetchAll(PDO::FETCH_OBJ);
                                        $title2 = '';
                                        if (empty($setprovidername2[0]->username) ) {
                                           $title2 = $setprovidername2[0]->organization;
                                        }else{
                                            $title2 = $setprovidername2[0]->name;
                                        }
                                        $fields_array[$i][$ckey] = ucwords($title2);
                                    }else{
                                        $fields_array[$i][$ckey] = ucwords($cvalue);
                                    }    
                                }elseif($set_check_list[0]->data_type == 12 ){
                                    if(!empty($cvalue)){
                                        $getpharmacyname = "SELECT  name FROM pharmacies WHERE id = $cvalue";
                                        $stmt6 = $db->prepare($getpharmacyname) ;
                                        $stmt6->execute();                       
                                        $setpharmacyname  = $stmt6->fetchAll(PDO::FETCH_OBJ);
                                        $pharmacyname = $setpharmacyname[0]->name;
                                        $fields_array[$i][$ckey] = ucwords($pharmacyname);
                                    }else{
                                        $fields_array[$i][$ckey] = ucwords($cvalue);
                                    }    
                                }elseif($set_check_list[0]->data_type == 37 ){
                                    if(!empty($cvalue)){
                                        $getinsurancename = "SELECT  name FROM insurance_companies WHERE id = $cvalue";
                                        $stmt6 = $db->prepare($getinsurancename) ;
                                        $stmt6->execute();                       
                                        $setinsurancename  = $stmt6->fetchAll(PDO::FETCH_OBJ);
                                        $insurancename = $setinsurancename[0]->name;
                                        $fields_array[$i][$ckey] = ucwords($insurancename);
                                    }else{
                                        $fields_array[$i][$ckey] = ucwords($cvalue);
                                    }    
                                }elseif($set_check_list[0]->data_type == 35 ){
                                    if(!empty($cvalue)){
                                        $getfacilityname = "SELECT  name FROM facility WHERE id = $cvalue";
                                        $stmt6 = $db->prepare($getfacilityname) ;
                                        $stmt6->execute();                       
                                        $setfacilityname  = $stmt6->fetchAll(PDO::FETCH_OBJ);
                                        $facilityname = $setfacilityname[0]->name;
                                        $fields_array[$i][$ckey] = ucwords($facilityname);
                                    }else{
                                        $fields_array[$i][$ckey] = ucwords($cvalue);
                                    }    
                                }else{
                                    $fields_array[$i][$ckey] = $cvalue;
                                } 
                            }else{
                                $fields_array[$i][$ckey] = $cvalue;   
                            }
                        }
                    }
                }
                $i++;
            }
        }
        if(!empty($fields_array)) {
            $agencydatares = json_encode($fields_array); 
            echo $agencydataresult = GibberishAES::enc($agencydatares, $key);
        }else{
            //echo 'No Agencies related to patient';
             $agencydatares = '[{"OrganizationId":"0","organization":"","name":"","addressbook_type":"","abook_type_value":"","workphone":"","phonecell":"","fax":"","email":"","street":"","city":"","state":"","zip":""}]';
             echo $agencydataresult = GibberishAES::enc($agencydatares, $key);
        }
       // echo "<pre>"; print_r($fields_array); echo "</pre>";
    } catch (Exception $ex) {
        $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo $agencydataresult = GibberishAES::enc($error, $key);   
    }
}

function getPatientAgencies($pid){
    $key = 'rotcoderaclla';
    // The default key size is 256 bits.
    $old_key_size = GibberishAES::size();
    GibberishAES::size(256);
    $sql = "SELECT tbl_patientagency.agencyid AS OrganizationId, Organization, CONCAT( u.title, u.fname,  ' ', u.lname ) AS name, abook_type AS abook_type_value, phonew1 AS workphone, phonecell, fax, email, street, city, state, zip, tbl_patientagency.agency_admitdate AS admitdate, tbl_patientagency.agency_dischargedate AS dischargedate, tbl_patientagency.agency_isactive AS isactive, tbl_patientagency.agency_notes AS notes, tbl_patientagency.agency_related_links AS related_links, tbl_patientagency.createdby, tbl_patientagency.created_date, (
            SELECT title
            FROM list_options
            WHERE list_id =  'abook_type'
            AND option_id = u.abook_type
            ) AS addressbook_type
            FROM users u
            INNER JOIN tbl_patientagency ON u.id = tbl_patientagency.agencyid
            WHERE patientid =:pid";
	 
	try 
        {
            $db = getConnection();
            $stmt = $db->prepare($sql) ;
            $stmt->bindParam("pid", $pid);  
            $db->query( "SET NAMES utf8"); 
            $stmt->execute();                       
             
            $agencydata = $stmt->fetchAll(PDO::FETCH_OBJ);                        
            
            if($agencydata)
            {
                //returns patientdata 
                $agencydatares = json_encode($agencydata); 
                echo $agencydataresult = GibberishAES::enc($agencydatares, $key);
            }
            else
            {
                //echo 'No Agencies related to patient';
                 $agencydatares = '[{"OrganizationId":"0","organization":"","name":"","addressbook_type":"","abook_type_value":"","workphone":"","phonecell":"","fax":"","email":"","street":"","city":"","state":"","zip":""}]';
                 echo $agencydataresult = GibberishAES::enc($agencydatares, $key);
            }
        } 
        catch(PDOException $e) 
        {
            
            $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
            echo $agencydataresult = GibberishAES::enc($error, $key);
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
//        $msgresult =  GibberishAES::dec('U2FsdGVkX1/zU5iY5OldjPrdAcCxvamebuGwp6+PbpzbXjCdPL/LA+MlGwVJmmr7BSEwtqHR/VJx3PU0Yl8NjEIBebAb7EzRbVhMKwaMf3vHctaqgHJAjxPWnVbEjc1rOpddJvVD7IqycmI2QNyIiMDQeT5Rfntpp6W5BJENOL4mAD5QY1cT0DqrdSYywR9s', $key);
        $reminderArray = json_decode($msgresult,TRUE);
        //echo "<pre>";print($reminderArray);echo "</pre>";
        

        $to_array = $reminderArray['to'];  
        $pid=$reminderArray['pid'];
	$from = $reminderArray['from'];
        $message=$reminderArray['message'];
        $due_date = $reminderArray['due_date'];
        $msg_status = 0 ; //$reminderArray['message_status'];
        $msg_priority = $reminderArray['message_priority'];
  
        $to_explode = explode(',',$to_array);
        $resultant_error = 0;
        $resultant_error2 = 0;
        $resultant = 0;
        foreach($to_explode as $to){
            $save_reminder= "INSERT INTO dated_reminders(dr_from_ID,dr_message_text,dr_message_sent_date,dr_message_due_date,pid,message_priority,message_processed,processed_date,dr_processed_by)
                VALUES($from,'$message','NOW()','$due_date',$pid,$msg_priority,$msg_status,'0000-00-00 00:00:00',0)";

            $db->query( "SET NAMES utf8"); 
            $q = $db->prepare($save_reminder);

            if($q->execute()){  
                $lastId = $db->lastInsertId(); 
                $save_reminder_link = "INSERT INTO dated_reminders_link (dr_id,to_id) VALUES ($lastId, $to)";

                $q2 = $db->prepare($save_reminder_link);
                if($q2->execute())
                {
                    $resultant = 1;
                }
                else
                {
                      $resultant_error = 1;
                }
        
            }else{
                $resultant_error2 = 1;
           }
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
    }
	
}

function getPatientInsuranceDataList($pid){
    $key = 'rotcoderaclla';
    // The default key size is 256 bits.
    $old_key_size = GibberishAES::size();
    GibberishAES::size(256);
    $sql = " SELECT i.id, i.type as Insurance_Type,(SELECT CONCAT(title, fname,  ' ', lname ) AS name FROM users WHERE id= i.provider) as Provider,ic.name as Insurance_Company_Name,i.group_number as Group_Number,i.plan_name as Plan_Name,i.policy_number as Policy_Number,CONCAT(i.subscriber_lname,' ',i.subscriber_mname,' ', i.subscriber_fname) as Subscriber_Name,i.subscriber_relationship as Subscriber_Relationship,
            i.subscriber_ss as Subscriber_SS,i.subscriber_DOB as Subscriber_DOB,i.subscriber_street as Subscriber_Street,i.subscriber_postal_code as Subscriber_Postal_Code,i.subscriber_city as Subscriber_City, i.subscriber_state as Subscriber_State, i.subscriber_country as Subscriber_Country,
            i.subscriber_employer as Subscriber_Employer,i.subscriber_employer_street as Subscriber_Employer_Street,i.subscriber_employer_postal_code as Subscriber_Employer_Postal_Code,i.subscriber_employer_state as Subscriber_Employer_State,i.subscriber_employer_country as Subscriber_Employer_Country, i.subscriber_employer_city as Subscriber_Employer_City,
            i.copay as Copay,i.date as Date, i.subscriber_sex as Subscriber_Sex,i.accept_assignment as Accept_Assignment,
            CASE i.policy_type
                WHEN ''   THEN 'N/A'
                WHEN 12 THEN 'Working Aged Beneficiary or Spouse with Employer Group Health Plan'
                WHEN 13 THEN 'End-Stage Renal Disease Beneficiary in MCP with Employer`s Group Plan'
                WHEN 14 THEN 'No-fault Insurance including Auto is Primary'
                WHEN 15 THEN 'Worker`s Compensation'
                WHEN 16 THEN 'Public Health Service (PHS) or Other Federal Agency'
                WHEN 41 THEN 'Black Lung'
                WHEN 42 THEN 'Veteran`s Administration'
                WHEN 43 THEN 'Disabled Beneficiary Under Age 65 with Large Group Health Plan (LGHP)'
                WHEN 47 THEN 'Other Liability Insurance is Primary'
            END as Policy_Type
            from insurance_data i 
            inner join insurance_companies ic on ic.id=i.provider
            where pid=:pid";
	 
	try 
        {
            $db = getConnection();
            $stmt = $db->prepare($sql) ;
            $stmt->bindParam("pid", $pid);            
            $stmt->execute();                       
            $insurancedata2 = array();   
            $insurancedata = $stmt->fetchAll(PDO::FETCH_OBJ);                        
            for($i=0; $i< count($insurancedata); $i++){
                foreach ($insurancedata[$i] as $key1 => $value1){
                    $key2 = str_replace('_', ' ', $key1);
                    $insurancedata2[$i][$key2] = $value1;
                }
            }
            if($insurancedata2)
            {
                //returns patientdata 
                $insurancedatares = json_encode($insurancedata2); 
                echo $insurancedataresult = GibberishAES::enc($insurancedatares, $key);
            }
            else
            {
                $insurancedatares  = '[{"id":"0"}]';
                echo $insurancedataresult = GibberishAES::enc($insurancedatares, $key);
            }
        } 
        catch(PDOException $e) 
        {
            
            $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
            echo $insurancedataresult = GibberishAES::enc($error, $key);
        }

}
// incomplete encounter count
function getIncompleteEncounterCount($providerid){
   
	try 
        {
            $db = getConnection();
            $key = 'rotcoderaclla';
            // The default key size is 256 bits.
            $old_key_size = GibberishAES::size();
            GibberishAES::size(256);
            $array = array();
            $count= '';
            $array_res = array();
            // get visit_categories list 
            $get_fuv = "SELECT visit_categories FROM tbl_allcare_facuservisit WHERE  `users`  REGEXP ('".":\"".$providerid."\"')";
            $fuv_stmt = $db->prepare($get_fuv) ;
            $fuv_stmt->execute();
            $set_fuv = $fuv_stmt->fetchAll(PDO::FETCH_OBJ); 
            for($i = 0; $i<count($set_fuv); $i++){
               $array[] =  unserialize( $set_fuv[$i]->visit_categories);
            }
            $dataArray = array();
            for($j = 0; $j<count($array); $j++){
                foreach($array[$j] as $arraykey){
                     $dataArray[] = $arraykey;
                }
            }
            $enc_val = '';
            $dataarray = array_unique($dataArray);
            foreach($dataarray as $arrayval){
                $enc_val .= $arrayval.",";
            }
            $enc_value = rtrim($enc_val,",");
            if(!empty($enc_value)){
            //$sql = "SELECT count(id) as count FROM `form_encounter`  WHERE (`sensitivity` <> 'finalized' OR `sensitivity` IS NULL) AND provider_id=$providerid ";
//                echo $sql = "SELECT count(f.id) as count FROM `form_encounter` f INNER JOIN patient_data p ON p.pid = f.pid WHERE (`elec_signed_on` = '' AND `elec_signedby` = '')AND p.deceased_stat !=  'YES'
//                 AND p.practice_status =  'YES' AND p.providerID=$providerid AND pc_catid IN ($enc_value)";
            $sql = "SELECT count(f.id) as count 
                FROM `form_encounter` f 
                INNER JOIN patient_data p ON p.pid = f.pid 
                inner join openemr_postcalendar_events o on f.pid = o.pc_pid and f.pc_catid = o.pc_catid and o.pc_eventDate = DATE_FORMAT(f.date,'%Y-%m-%d')
                WHERE (`elec_signed_on` = '' AND `elec_signedby` = '')AND p.deceased_stat !=  'YES'
                 AND p.practice_status =  'YES' AND o.pc_aid=$providerid AND f.pc_catid IN ($enc_value)";

                
                $stmt = $db->prepare($sql) ;
                $stmt->execute();                       

                $count = $stmt->fetchAll(PDO::FETCH_OBJ);                        
            }
            if($count)
            {
                //returns count 
                $countres = json_encode($count); 
                echo $countresult = GibberishAES::enc($countres, $key);
            }
            else
            {
                $countres = '[{"id":"0"}]';
                echo $countresult = GibberishAES::enc($countres, $key);
            }
        } 
        catch(PDOException $e) 
        {
            
            $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
            echo $countresult = GibberishAES::enc($error, $key);
        }
   
}
function getIncompleteEncounterCount2($providerid){
   
	try 
        {
            $db = getConnection();
            $key = 'rotcoderaclla';
            // The default key size is 256 bits.
            $old_key_size = GibberishAES::size();
            GibberishAES::size(256);
            $array = array();
            $count= '';
            $array_res = array();
            // get visit_categories list 
            $get_fuv = "SELECT visit_categories FROM tbl_allcare_facuservisit WHERE  `users`  REGEXP ('".":\"".$providerid."\"')";
            $fuv_stmt = $db->prepare($get_fuv) ;
            $fuv_stmt->execute();
            $set_fuv = $fuv_stmt->fetchAll(PDO::FETCH_OBJ); 
            for($i = 0; $i<count($set_fuv); $i++){
               $array[] =  unserialize( $set_fuv[$i]->visit_categories);
            }
            $dataArray = array();
            for($j = 0; $j<count($array); $j++){
                foreach($array[$j] as $arraykey){
                     $dataArray[] = $arraykey;
                }
            }
            $enc_val = '';
            $dataarray = array_unique($dataArray);
            foreach($dataarray as $arrayval){
                $enc_val .= $arrayval.",";
            }
            $enc_value = rtrim($enc_val,",");
            if(!empty($enc_value)){
            //$sql = "SELECT count(id) as count FROM `form_encounter`  WHERE (`sensitivity` <> 'finalized' OR `sensitivity` IS NULL) AND provider_id=$providerid ";
                $sql = "SELECT count(f.id) as count 
                FROM `form_encounter` f 
                INNER JOIN patient_data p ON p.pid = f.pid 
                inner join openemr_postcalendar_events o on f.pid = o.pc_pid and f.pc_catid = o.pc_catid and o.pc_eventDate = DATE_FORMAT(f.date,'%Y-%m-%d')
                WHERE (`elec_signed_on` = '' AND `elec_signedby` = '')AND p.deceased_stat !=  'YES'
                 AND p.practice_status =  'YES' AND p.pc_aid=$providerid AND f.pc_catid IN ($enc_value)";

                $stmt = $db->prepare($sql) ;
                $stmt->execute();                       

                $count = $stmt->fetchAll(PDO::FETCH_OBJ);                        
            }
            if($count)
            {
                //returns count 
                $countres = json_encode($count); 
                echo $countresult = GibberishAES::enc($countres, $key);
            }
            else
            {
                $countres = '[{"id":"0"}]';
                echo $countresult = GibberishAES::enc($countres, $key);
            }
        } 
        catch(PDOException $e) 
        {
            
            $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
            echo $countresult = GibberishAES::enc($error, $key);
        }
   
}

// incomplete encounter count
function getPatientIncompleteEncounterCount($providerid){
   
	try 
        {
            $db = getConnection();
            $key = 'rotcoderaclla';
            // The default key size is 256 bits.
            $old_key_size = GibberishAES::size();
            GibberishAES::size(256);
            $count = '';
            // to get visit categories list
            $get_fuv = "SELECT facilities,visit_categories FROM tbl_allcare_facuservisit WHERE  `users`  REGEXP ('".":\"".$providerid."\"')";
            $fuv_stmt = $db->prepare($get_fuv) ;
            $fuv_stmt->execute();
            $set_fuv = $fuv_stmt->fetchAll(PDO::FETCH_OBJ); 
            for($i = 0; $i<count($set_fuv); $i++){
               $array[] =  unserialize( $set_fuv[$i]->visit_categories);
               $array2[] =  unserialize( $set_fuv[$i]->facilities);
            }
            if(!empty($array)){
                $dataArray = array();
                for($j = 0; $j<count($array); $j++){
                    foreach($array[$j] as $arraykey){
                         $dataArray[] = $arraykey;
                    }
                }
                $enc_val = '';
                $dataarray = array_unique($dataArray);
                foreach($dataarray as $arrayval){
                    $enc_val .= $arrayval.",";
                }
                $enc_value = rtrim($enc_val,",");
            $dataArray2 = array();
            for($j = 0; $j<count($array2); $j++){
                foreach($array2[$j] as $arraykey2){
                     $dataArray2[] = $arraykey2;
                }
            }
            $enc_val2 = '';
            $dataarray2 = array_unique($dataArray2);
            foreach($dataarray2 as $arrayval2){
                $enc_val2 .= $arrayval2.",";
            }
            $enc_value2 = rtrim($enc_val2,",");
    //            $sql = "SELECT f.pid, p.lname, p.fname, GROUP_CONCAT( CASE WHEN (DATE_FORMAT( f.date, '%Y-%m-%d' ) <> '0000-00-00') THEN DATE_FORMAT( f.date, '%Y-%m-%d' ) END  ORDER BY f.date ASC) AS dos, COUNT( f.id ) AS encounter_count, (
    // 
    //                SELECT COUNT( encounter )
    //                FROM form_encounter
    //                WHERE p.pid = pid 
    //                ) AS visit_count
    //                FROM form_encounter f
    //                INNER JOIN patient_data p ON p.pid = f.pid
    //                WHERE (
    //                `sensitivity` <> 'finalized'
    //                OR `sensitivity` IS NULL
    //                )
    //                AND f.provider_id =$providerid
    //                GROUP BY p.lname ";
               //            $sql = "SELECT f.pid, p.lname, p.fname, GROUP_CONCAT( CASE WHEN (DATE_FORMAT( f.date, '%Y-%m-%d' ) <> '0000-00-00') THEN DATE_FORMAT( f.date, '%Y-%m-%d' ) END  ORDER BY f.date ASC) AS dos, COUNT( f.id ) AS encounter_count, (
    // 
    //                SELECT COUNT( encounter )
    //                FROM form_encounter
    //                WHERE p.pid = pid 
    //                ) AS visit_count
    //                FROM form_encounter f
    //                INNER JOIN patient_data p ON p.pid = f.pid
    //                WHERE (
    //                `sensitivity` <> 'finalized'
    //                OR `sensitivity` IS NULL
    //                )
    //                AND f.provider_id =$providerid
    //                GROUP BY p.lname ";
                $sql = "SELECT f.pid, p.lname, p.fname, GROUP_CONCAT( CASE WHEN (DATE_FORMAT( f.date, '%Y-%m-%d' ) <> '0000-00-00') THEN DATE_FORMAT( f.date, '%Y-%m-%d' ) END  ORDER BY f.date ASC) AS dos, COUNT( f.id ) AS encounter_count,  COUNT( f.id ) AS visit_count
                    FROM form_encounter f
                    INNER JOIN patient_data p ON p.pid = f.pid
                    INNER JOIN openemr_postcalendar_events o ON o.pc_pid = f.pid
                    AND o.pc_catid = f.pc_catid AND f.facility_id = o.pc_facility
                    AND o.pc_eventDate = DATE_FORMAT( f.date,  '%Y-%m-%d' ) 
                    WHERE (
                    `elec_signed_on` = '' AND `elec_signedby` = ''
                    )
                    AND o.pc_aid =$providerid AND f.facility_id IN ($enc_value2)
                    AND  p.practice_status = 'YES' AND p.deceased_stat != 'YES' AND f.pc_catid IN ($enc_value)
                    GROUP BY p.lname ";
                $stmt = $db->prepare($sql) ;
                $stmt->execute();                       

                $count = $stmt->fetchAll(PDO::FETCH_OBJ);                        
            }
            if($count)
            {
                //returns count 
                $countres = json_encode($count); 
                echo $countresult = GibberishAES::enc($countres, $key);
            }
            else
            {
                $countres = '[{"id":"0"}]';
                echo $countresult = GibberishAES::enc($countres, $key);
            }
        } 
        catch(PDOException $e) 
        {
            
             $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
             echo $countresult = GibberishAES::enc($error, $key);
        }
   
}
function getPatientIncompleteEncounterCount2($providerid){
   
	try 
        {
            $db = getConnection();
            $key = 'rotcoderaclla';
            // The default key size is 256 bits.
            $old_key_size = GibberishAES::size();
            GibberishAES::size(256);
            $count = '';
            // to get visit categories list
            $get_fuv = "SELECT visit_categories FROM tbl_allcare_facuservisit WHERE  `users`  REGEXP ('".":\"".$providerid."\"')";
            $fuv_stmt = $db->prepare($get_fuv) ;
            $fuv_stmt->execute();
            $set_fuv = $fuv_stmt->fetchAll(PDO::FETCH_OBJ); 
            for($i = 0; $i<count($set_fuv); $i++){
               $array[] =  unserialize( $set_fuv[$i]->visit_categories);
            }
            if(!empty($array)){
                $dataArray = array();
                for($j = 0; $j<count($array); $j++){
                    foreach($array[$j] as $arraykey){
                         $dataArray[] = $arraykey;
                    }
                }
                $enc_val = '';
                $dataarray = array_unique($dataArray);
                foreach($dataarray as $arrayval){
                    $enc_val .= $arrayval.",";
                }
                $enc_value = rtrim($enc_val,",");

    //            $sql = "SELECT f.pid, p.lname, p.fname, GROUP_CONCAT( CASE WHEN (DATE_FORMAT( f.date, '%Y-%m-%d' ) <> '0000-00-00') THEN DATE_FORMAT( f.date, '%Y-%m-%d' ) END  ORDER BY f.date ASC) AS dos, COUNT( f.id ) AS encounter_count, (
    // 
    //                SELECT COUNT( encounter )
    //                FROM form_encounter
    //                WHERE p.pid = pid 
    //                ) AS visit_count
    //                FROM form_encounter f
    //                INNER JOIN patient_data p ON p.pid = f.pid
    //                WHERE (
    //                `sensitivity` <> 'finalized'
    //                OR `sensitivity` IS NULL
    //                )
    //                AND f.provider_id =$providerid
    //                GROUP BY p.lname ";
               //            $sql = "SELECT f.pid, p.lname, p.fname, GROUP_CONCAT( CASE WHEN (DATE_FORMAT( f.date, '%Y-%m-%d' ) <> '0000-00-00') THEN DATE_FORMAT( f.date, '%Y-%m-%d' ) END  ORDER BY f.date ASC) AS dos, COUNT( f.id ) AS encounter_count, (
    // 
    //                SELECT COUNT( encounter )
    //                FROM form_encounter
    //                WHERE p.pid = pid 
    //                ) AS visit_count
    //                FROM form_encounter f
    //                INNER JOIN patient_data p ON p.pid = f.pid
    //                WHERE (
    //                `sensitivity` <> 'finalized'
    //                OR `sensitivity` IS NULL
    //                )
    //                AND f.provider_id =$providerid
    //                GROUP BY p.lname ";
                $sql = "SELECT f.pid, p.lname, p.fname, GROUP_CONCAT( CASE WHEN (DATE_FORMAT( f.date, '%Y-%m-%d' ) <> '0000-00-00') THEN DATE_FORMAT( f.date, '%Y-%m-%d' ) END  ORDER BY f.date ASC) AS dos, COUNT( f.id ) AS encounter_count,  COUNT( f.id ) AS visit_count
                    FROM form_encounter f
                    INNER JOIN patient_data p ON p.pid = f.pid
                    WHERE (
                    `elec_signed_on` = '' AND `elec_signedby` = ''
                    )
                    AND p.providerID =$providerid
                    AND  p.practice_status = 'YES' AND p.deceased_stat != 'YES' AND f.pc_catid IN ($enc_value)
                    GROUP BY p.lname ";
                $stmt = $db->prepare($sql) ;
                $stmt->execute();                       

                $count = $stmt->fetchAll(PDO::FETCH_OBJ);                        
            }
            if($count)
            {
                //returns count 
                $countres = json_encode($count); 
                echo $countresult = GibberishAES::enc($countres, $key);
            }
            else
            {
                $countres = '[{"id":"0"}]';
                echo $countresult = GibberishAES::enc($countres, $key);
            }
        } 
        catch(PDOException $e) 
        {
            
             $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
             echo $countresult = GibberishAES::enc($error, $key);
        }
   
}

// incomplete encounter list
function getIncompleteEncounterList($pid,$uid){
    
	try 
        {
            $db = getConnection();
            $apikey = 'rotcoderaclla';
            // The default key size is 256 bits.
            $old_key_size = GibberishAES::size();
            GibberishAES::size(256);
            
            // to get vist category list
            $get_fuv = "SELECT facilities,visit_categories FROM tbl_allcare_facuservisit WHERE  `users`  REGEXP ('".":\"".$uid."\"')";
            $fuv_stmt = $db->prepare($get_fuv) ;
            $fuv_stmt->execute();
            $set_fuv = $fuv_stmt->fetchAll(PDO::FETCH_OBJ); 
            for($i = 0; $i<count($set_fuv); $i++){
               $array[] =  unserialize( $set_fuv[$i]->visit_categories);
               $array2[] =  unserialize( $set_fuv[$i]->facilities);
            }
            $dataArray = array();
            for($j = 0; $j<count($array); $j++){
                foreach($array[$j] as $arraykey){
                     $dataArray[] = $arraykey;
                }
            }
            $enc_val = '';
            $dataarray = array_unique($dataArray);
            foreach($dataarray as $arrayval){
                $enc_val .= $arrayval.",";
            }
            $enc_value = rtrim($enc_val,",");
           $dataArray2 = array();
            for($j = 0; $j<count($array2); $j++){
                foreach($array2[$j] as $arraykey2){
                     $dataArray2[] = $arraykey2;
                }
            }
            $enc_val2 = '';
            $dataarray2 = array_unique($dataArray2);
            foreach($dataarray2 as $arrayval2){
                $enc_val2 .= $arrayval2.",";
            }
            $enc_value2 = rtrim($enc_val2,",");
            $sql = "SELECT form_encounter.facility,form_encounter.facility_id, 
                form_encounter.encounter,form_encounter.pc_catid AS visitcategory_id,
                DATE_FORMAT( form_encounter.date,  '%Y-%m-%d' ) AS dos,
                o.pc_aid as provider_id
                        FROM form_encounter
                        INNER JOIN patient_data ON patient_data.pid = form_encounter.pid
                        inner join openemr_postcalendar_events o on form_encounter.pid = o.pc_pid and form_encounter.pc_catid = o.pc_catid and o.pc_eventDate = DATE_FORMAT(form_encounter.date,'%Y-%m-%d') 
                         AND form_encounter.facility_id = o.pc_facility
                        WHERE form_encounter.pid =$pid AND form_encounter.pc_catid IN ($enc_value) AND form_encounter.facility_id IN($enc_value2)
                            AND (
                             `elec_signed_on` = '' AND `elec_signedby` = ''
                            ) AND o.pc_aid = '$uid' ORDER BY form_encounter.date DESC ";
            
            $stmt = $db->prepare($sql) ;
            $stmt->execute(); 
            $formlabels = $stmt->fetchAll(PDO::FETCH_OBJ); 
            $formfields = array();
            $formvalues = array();
            $datacheck8= array();
            $datacheck7 = array();
            $dataArr = array();
            foreach($formlabels as $element): 
                 $sql6 = "SELECT DISTINCT(fe.encounter),CONCAT(pd.title,pd.fname,' ',pd.lname) as pname,DATE_FORMAT( fe.date,  '%Y-%m-%d' ) AS dos, fe.facility AS facility, fe.pid AS pid, fe.pc_catid AS visitcategory_id,oe.pc_catname as visitcategory,audited_status
                    FROM form_encounter fe
                    INNER JOIN patient_data pd on pd.pid = fe.pid
                    INNER JOIN openemr_postcalendar_categories oe ON oe.pc_catid = fe.pc_catid
                    INNER JOIN openemr_postcalendar_events op ON fe.pid = op.pc_pid and fe.pc_catid = op.pc_catid and op.pc_eventDate = DATE_FORMAT(fe.date,'%Y-%m-%d') 
                    WHERE fe.pid = $pid and fe.encounter = $element->encounter 
                    AND (
                     `elec_signed_on` = '' AND `elec_signedby` = ''
                    ) ";
                    $stmt6 = $db->prepare($sql6);
                    $stmt6->execute(); 
                    $datacheck6 = $stmt6->fetchAll(PDO::FETCH_OBJ);
                    if(!empty($datacheck6)):
                        $datacheck6[0]->form_status = 'Incomplete';
                        $datacheck7['finalizetype'] = 'checkbox';
                        if($datacheck6[0]->audited_status == 'Completed'){
                            $datacheck7['isfinalize'] = 'Enable';
                            $datacheck7['title'] = 'Finalize';
                            $datacheck6[0]->finalize = $datacheck7;
                        }else{
                            $datacheck6[0]->audited_status = 'Incomplete';
                            $datacheck7['isfinalize'] = 'Disable';
                            $datacheck7['title'] = 'Finalize';
                            $datacheck6[0]->finalize = $datacheck7;
                        }
                        //$datacheck6[0]->finalize_field_type = $datacheck7;
                        
                    endif;
                $formfields[] = $datacheck6;

            endforeach;
            
           // echo "<pre>"; print_r($formfields); echo "</pre>";
           
            $newdemo1=encode_demo(array_filter($formfields));
            $newdemo['EncounterData'] = check_data_available($newdemo1);
            
           //echo "<pre>"; print_r($newdemo); echo "</pre>";
            if($newdemo1)
            {
                //returns count 
                $newdemores = json_encode($newdemo);
                echo $newdemoresult = GibberishAES::enc($newdemores, $apikey);
            }
            else
            {
               $incompletelist = '[{"id":"0"}]';
               echo $incompletelistresult = GibberishAES::enc($incompletelist, $apikey);
            }
        } 
        catch(PDOException $e) 
        {
            $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
            echo $incompletelistresult = GibberishAES::enc($error, $apikey);
        }
   
}
function getIncompleteEncounterList2($pid,$uid){
    
	try 
        {
            $db = getConnection();
            $apikey = 'rotcoderaclla';
            // The default key size is 256 bits.
            $old_key_size = GibberishAES::size();
            GibberishAES::size(256);
            
            // to get vist category list
            $get_fuv = "SELECT visit_categories FROM tbl_allcare_facuservisit WHERE  `users`  REGEXP ('".":\"".$uid."\"')";
            $fuv_stmt = $db->prepare($get_fuv) ;
            $fuv_stmt->execute();
            $set_fuv = $fuv_stmt->fetchAll(PDO::FETCH_OBJ); 
            for($i = 0; $i<count($set_fuv); $i++){
               $array[] =  unserialize( $set_fuv[$i]->visit_categories);
            }
            $dataArray = array();
            for($j = 0; $j<count($array); $j++){
                foreach($array[$j] as $arraykey){
                     $dataArray[] = $arraykey;
                }
            }
            $enc_val = '';
            $dataarray = array_unique($dataArray);
            foreach($dataarray as $arrayval){
                $enc_val .= $arrayval.",";
            }
            $enc_value = rtrim($enc_val,",");
            $sql = "SELECT form_encounter.facility,form_encounter.facility_id, form_encounter.encounter,form_encounter.pc_catid AS visitcategory_id,DATE_FORMAT( form_encounter.date,  '%Y-%m-%d' ) AS dos, patient_data.providerID as provider_id
                        FROM form_encounter
                        INNER JOIN patient_data ON patient_data.pid = form_encounter.pid
                        WHERE form_encounter.pid =$pid AND pc_catid IN ($enc_value)
                            AND (
                             `elec_signed_on` = '' AND `elec_signedby` = ''
                            ) ORDER BY form_encounter.date DESC ";
            $stmt = $db->prepare($sql) ;
            $stmt->execute(); 
            $formlabels = $stmt->fetchAll(PDO::FETCH_OBJ); 
            $formfields = array();
            $formvalues = array();
            $datacheck8= array();
            $datacheck7 = array();
            $dataArr = array();
            foreach($formlabels as $element): 
                $sql6 = "SELECT DISTINCT(fe.encounter),CONCAT(pd.title,pd.fname,' ',pd.lname) as pname,DATE_FORMAT( fe.date,  '%Y-%m-%d' ) AS dos, fe.facility AS facility, fe.pid AS pid, fe.pc_catid AS visitcategory_id,oe.pc_catname as visitcategory,audited_status
                    FROM form_encounter fe
                    INNER JOIN patient_data pd on pd.pid = fe.pid
                    INNER JOIN openemr_postcalendar_categories oe ON oe.pc_catid = fe.pc_catid
                    WHERE fe.pid = $pid and fe.encounter = $element->encounter 
                    AND (
                     `elec_signed_on` = '' AND `elec_signedby` = ''
                    ) ";
                    $stmt6 = $db->prepare($sql6);
                    $stmt6->execute(); 
                    $datacheck6 = $stmt6->fetchAll(PDO::FETCH_OBJ);
                    if(!empty($datacheck6)):
                        $datacheck6[0]->form_status = 'Incomplete';
                        $datacheck7['finalizetype'] = 'checkbox';
                        if($datacheck6[0]->audited_status == 'Completed'){
                            $datacheck7['isfinalize'] = 'Enable';
                            $datacheck7['title'] = 'Finalize';
                            $datacheck6[0]->finalize = $datacheck7;
                        }else{
                            $datacheck6[0]->audited_status = 'Incomplete';
                            $datacheck7['isfinalize'] = 'Disable';
                            $datacheck7['title'] = 'Finalize';
                            $datacheck6[0]->finalize = $datacheck7;
                        }
                        //$datacheck6[0]->finalize_field_type = $datacheck7;
                        
                    endif;
                $formfields[] = $datacheck6;

            endforeach;
            
           // echo "<pre>"; print_r($formfields); echo "</pre>";
           
            $newdemo1=encode_demo(array_filter($formfields));
            $newdemo['EncounterData'] = check_data_available($newdemo1);
            
           //echo "<pre>"; print_r($newdemo); echo "</pre>";
            if($newdemo1)
            {
                //returns count 
                $newdemores = json_encode($newdemo);
                echo $newdemoresult = GibberishAES::enc($newdemores, $apikey);
            }
            else
            {
               $incompletelist = '[{"id":"0"}]';
               echo $incompletelistresult = GibberishAES::enc($incompletelist, $apikey);
            }
        } 
        catch(PDOException $e) 
        {
            $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
            echo $incompletelistresult = GibberishAES::enc($error, $apikey);
        }
   
}
function updatePatientHistory(){
        
    try{
        
        $db = getConnection();
        $flag=0;
        $request = Slim::getInstance()->request();
        $apikey = 'rotcoderaclla';
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        $appres = GibberishAES::dec($request->getBody(), $apikey);
//        $appres = GibberishAES::dec('U2FsdGVkX1/0k9If1eSlXsZosJQNRPzNPI03Mttd67FoZz6h8PjqHIUgoRh2ptJOFytOumCOpEWFiIpf3oLuVUuRkc/4yd/bxx+tREoPw7Jlc2jmYE0bV1aqxvs22WzBW1L3hXK/4S2UNl3xqTbBJmh5hY9/nMjsFSo7zTEX10xrkmc7HuLLIe73Ln5dkfKT5d5zQV0DdqgBD2pmvU5B0qcfs+Dt0P3PCs09da6KLlPlmWmfXAW0XYOquolH4IQxuik/xXDHh1rcbkvmW2A1DknLUZ//MPCStqvQ4JzRnK4nB4tFn+MnEtbkJhwXcTycWwyDHe4mqB/wDtXSjWEFbgCqz13l1/f9XJlvJpSjO7E=', $apikey);
        $insertArray = json_decode($appres,TRUE);
//        echo "<pre>"; print_r($insertArray); echo "</pre>";
         
        $group_name = str_replace('_', ' ', $insertArray['group_name']);
        $form_id = $insertArray['form_id'];
        $encounter = $insertArray['encounter'];
        $user = $insertArray['user'];
        $getcolumnNames = "SELECT field_id FROM layout_options WHERE form_id = 'HIS' AND group_name = '$group_name'";
        $stmt = $db->prepare($getcolumnNames);
        $stmt->execute();                       

        $getnames = $stmt->fetchAll(PDO::FETCH_OBJ); 
        foreach ($getnames as $value) { 
            ${$value->field_id} = $insertArray[$value->field_id];
            $checkname[] =  $value->field_id;
        }
        $nameslist2 = '';
        $namesvalues2 = '';
        $columnsql = "select COLUMN_NAME from information_schema.columns where table_name='history_data'";
        $cstmt = $db->prepare($columnsql) ;
        $cstmt->execute();                       

        $dataresultset = $cstmt->fetchAll(PDO::FETCH_OBJ);
        foreach($dataresultset as $key => $value){
            if($key != 'id' )
                $nameslist2 .=  $value->COLUMN_NAME.",";
        }
        $gethistorydatasql = "SELECT  * FROM history_data where pid = ".$insertArray['pid']."  ORDER BY id DESC LIMIT 1 ";
        $stmthis = $db->prepare($gethistorydatasql) ;
        $stmthis->execute();
        $hisresultset = $stmthis->fetchAll(PDO::FETCH_OBJ);
        foreach($hisresultset[0] as $key => $his){
            if(in_array($key, $checkname)){
                //${$value->field_id} = $insertArray[$value->field_id];
            }else{
                if($key != 'id' ){
                    ${$key} = $his;
                }
               
            }
            if($key == 'id'){
                
            }else if($key == 'date'){
                $namesvalues2 .= "NOW(),";
            }else{     
                $namesvalues2 .= "'".${$key}."',";
            }
        }
        
        foreach ($getnames as $value) {
           // $namesvalues2 .= "'".${$value->field_id}."',";
        }
        $nameslist = rtrim($nameslist2, ',');
        $namesvalues = rtrim($namesvalues2, ',');
        $insert = "INSERT INTO history_data ($nameslist) VALUES($namesvalues)";
        $stmt2 = $db->prepare($insert) ;
        $insertval = $stmt2->execute();                     
        
//        if($group_name == '1Past Medical History'){
//            $search = 'history_past_stat';
//        }elseif($group_name ==  '2Family History'){
//            $search = 'family_history_stat';
//        }elseif($group_name ==  '3Primary Family Med Conditions'){
//            $search = 'family_med_com_stat';
//        }elseif($group_name ==  '4Social History'){
//            $search = 'family_exam_test_stat';
//        }elseif($group_name ==  '5Tests and Exams'){
//            $search = 'history_social_stat';
//        }
        $search = $insertArray['option_id'] ;
        
        $getformid1 = "SELECT form_id FROM forms WHERE formdir= 'LBF2' and deleted = 0 and encounter = $encounter and pid = $pid order by date asc";
        $stmt31 = $db->prepare($getformid1) ;
        $stmt31->execute();
        $formidval2 = $stmt31->fetchAll(PDO::FETCH_OBJ);

        if(!empty($formidval2)){
             $newformid2 =  $formidval2[0]->form_id;
        }else{
            $lastformid2 = "SELECT MAX(form_id) as forms FROM lbf_data";
            $stmt5 = $db->prepare($lastformid2) ;
            $stmt5->execute();
            $maxformidval2 = $stmt5->fetchAll(PDO::FETCH_OBJ);
            $maxformid2 =  $maxformidval2[0]->forms + 1;
            
            $insertform2 = "INSERT INTO forms (date,encounter, form_name, form_id, pid, user, groupname, authorized, deleted, formdir) VALUES (NOW(), $encounter, 'Allcare Encounter Forms', $maxformid2, $pid, $user, 'Default', 1, 0, 'LBF2' )";
            $stmt4 = $db->prepare($insertform2);
            $stmt4->execute();
            $newformid2 = $db->lastInsertId();
            
        }
        $status = $insertArray[$search] ;
        if (count($db->query("SELECT * FROM lbf_data WHERE form_id = $form_id AND field_id = '$search'")->fetchAll())==0) {
            $sql2 = "INSERT into lbf_data (form_id, field_id, field_value) VALUES ($form_id,'$search','$status')";
        } else {
           $sql2 = "UPDATE lbf_data SET field_value = '$status' WHERE field_id ='$search'  AND form_id = $form_id";
        }
        
        $stmt4 = $db->prepare($sql2);
        $stmt4->execute();
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

// to add prescription
function addPrescription(){
    try{
        $db = getConnection();
        $flag=0;
        $request = Slim::getInstance()->request();
        $key = 'rotcoderaclla';
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        $addprescription = GibberishAES::dec($request->getBody(), $key);
        $addprescriptionArray = json_decode($addprescription,TRUE);
        
        $pid                = $addprescriptionArray['pid'];
        $filled_by_id       = $addprescriptionArray['filled_by_id'];
        $pharmacy_id        = $addprescriptionArray['pharmacy_id'];
        $date_modified      = $addprescriptionArray['date_modified'];
        $provider_id        = $addprescriptionArray['provider_id'];
        $encounter          = $addprescriptionArray['encounter'];
        $start_date         = $addprescriptionArray['start_date'];
        $drug               = $addprescriptionArray['drug'];
        $drug_id            = $addprescriptionArray['drug_id'];
        $rxnorm_drugcode    = $addprescriptionArray['rxnorm_drugcode'];
        $form               = $addprescriptionArray['form'];
        $dosage             = $addprescriptionArray['dosage'];
        $quantity           = $addprescriptionArray['quantity'];
        $size               = $addprescriptionArray['size'];
        $unit               = $addprescriptionArray['unit'];
        $route              = $addprescriptionArray['route'];
        $interval           = $addprescriptionArray['interval'];
        $substitute         = $addprescriptionArray['substitute'];
        $refills            = $addprescriptionArray['refills'];
        $per_refill         = $addprescriptionArray['per_refill'];
        $filled_date        = $addprescriptionArray['filled_date'];
        $medication         = $addprescriptionArray['medication'];
        $note               = $addprescriptionArray['note'];
        $active             = $addprescriptionArray['active'];
        $datetime           = $addprescriptionArray['datetime'];
        $user               = $addprescriptionArray['user'];
        $site               = $addprescriptionArray['site'];
        $prescriptionguid   = $addprescriptionArray['prescriptionguid'];
        $erx_source         = $addprescriptionArray['erx_source'];
        $erx_uploaded       = $addprescriptionArray['erx_uploaded'];
        $drug_info_erx      = $addprescriptionArray['drug_info_erx'];
        
       /* $add_query="INSERT INTO `prescriptions` ( `patient_id`, `filled_by_id`, `pharmacy_id`, `date_added`, `date_modified`, `provider_id`, 
                   `encounter`, `start_date`, `drug`, `drug_id`, `rxnorm_drugcode`, `form`, `dosage`, `quantity`, `size`, `unit`, `route`, 
                   `interval`, `substitute`, `refills`, `per_refill`, `filled_date`, `medication`, `note`, `active`, `datetime`, `user`, 
                   `site`, `prescriptionguid`, `erx_source`, `erx_uploaded`, `drug_info_erx`)
            
                VALUES ($pid, '$filled_by_id', '$pharmacy_id', NOW(), '', '$provider_id', '$encounter, '$start_date', '$drug', '$drug_id', 
         '$rxnorm_drugcode', '$form', '$dosage', '$quantity', '$size', '$unit', '$route', '$interval', '$substitute', '$refills',
         '$per_refill', '$filled_date', '$medication', '$note', '$active', '$datetime', '$user', '$site', '$prescriptionguid', '$erx_source', '$erx_uploaded', '$drug_info_erx')";
         */
        $add_query = "REPLACE INTO prescriptions
SET 
`patient_id` =  $pid,
`date_added` =  NOW(),
`date_modified` =  '',
`provider_id` =  $provider_id,
`start_date` =  '$start_date',
`drug` =  '$drug',
`form` =  '$form',
`dosage` =  '$dosage',
`quantity` =  '$quantity',
`size` =  $size,
`unit` =  '$unit',
`route` =  '$route',
`interval` =  '$interval',
`substitute` =  '$substitute',
`refills` =  '$refills',
`per_refill` =  '$per_refill',
`note` =  '$note',
`active` =  '$active'
";
       
    }catch(PDOException $e){
	echo '{"error":{"text":'. $e->getMessage() .'}}'; 
    }
}

function getDosVisitList($pid,$eid){
    try 
    {
        $db = getConnection();
        $key = 'rotcoderaclla';
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        $sql = "SELECT f.encounter as eid, DATE_FORMAT(f.date,'%Y-%m-%d') as dos, o.pc_catname as visitcategory, IF( (SELECT copy_from_enc FROM tbl_allcare_template WHERE copy_to_enc = $eid order by id desc limit 0,1) = f.encounter, 'yes', 'no')as  selected_template
                FROM form_encounter f
                INNER JOIN openemr_postcalendar_categories o
                WHERE pid =$pid
                AND o.pc_catid = f.pc_catid and f.encounter <> $eid";

        $stmt = $db->prepare($sql) ;
        $stmt->execute();                       

        $dosvisit = $stmt->fetchAll(PDO::FETCH_OBJ);                        

        if($dosvisit)
        {
            //returns dosvisit 
            $dosvisitres = json_encode($dosvisit); 
            echo $dosvisitresult = GibberishAES::enc($dosvisitres, $key);
        }
        else
        {
            $dosvisitres = '[{"id":"0"}]';
            echo $dosvisitresult = GibberishAES::enc($dosvisitres, $key);
        }
    } 
    catch(PDOException $e) 
    {

        $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo $incompletelistresult = GibberishAES::enc($error, $key);
    }

}
function array_orderby()
{
    $args = func_get_args();
    $data = array_shift($args);
    foreach ($args as $n => $field) {
        if (is_string($field)) {
            $tmp = array();
            foreach ($data as $key => $row)
                $tmp[$key] = $row[$field];
            $args[$n] = $tmp;
            }
    }
    $args[] = &$data;
    call_user_func_array('array_multisort', $args);
    return array_pop($args);
}
// to get form data based on dos
function getDosFormData($eid){
  try 
    {
        $db = getConnection();
        
        $apikey = 'rotcoderaclla';
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        
        $sql = "SELECT f.pc_catid, o.pc_aid as provider_id, f.facility_id, p.pid
                FROM  `form_encounter` f 
                INNER JOIN patient_data p ON p.pid = f.pid
                inner join openemr_postcalendar_events o on f.pid = o.pc_pid and f.pc_catid = o.pc_catid and o.pc_eventDate = DATE_FORMAT(f.date,'%Y-%m-%d')
                WHERE encounter =$eid ";
        
        $stmt = $db->prepare($sql) ; 
        $stmt->execute();
        $namesresult = $stmt->fetchAll(PDO::FETCH_OBJ); 
        foreach ($namesresult as $pidval) {
            $pid = $pidval->pid;
        }
        $fieldnamesresult = array();
        $formnames = array();
        $formfields = array();
        $newdemo = array();
        $formvalues = array();
        foreach ($namesresult as $value) {
            
            $sql2 ="SELECT DISTINCT  l.title AS screen_name,f.screen_group, l.description AS screen_link
                FROM  `tbl_allcare_facuservisit` f
                INNER JOIN layout_options l ON l.group_name = f.screen_group
                AND l.form_id = f.form_id
                WHERE  `facilities` REGEXP ('".":\"".$value->facility_id."\"') AND  `users` REGEXP ('".":\"".$value->provider_id."\"') AND  `visit_categories` REGEXP ('".":\"".$value->pc_catid."\"') order by f.id ";      
            $stmt2 = $db->prepare($sql2); 
            $stmt2->execute();
            $fieldnamesresult[] = $stmt2->fetchAll(PDO::FETCH_OBJ);
            
        } 
//        echo "<pre>"; print_r($fieldnamesresult); echo "</pre>";
        foreach ($fieldnamesresult as $value2) {
            
            foreach ($value2 as $value3) {
                $sql3 = "SELECT DISTINCT(SUBSTRING(lo.group_name FROM 2)) as group_name , lo.field_id 
                                FROM layout_options lo
                                LEFT JOIN forms f ON lo.form_id = 'LBF1'
                                WHERE lo.title = '$value3->screen_name'
                                AND f.encounter = $eid";
                $stmt3 = $db->prepare($sql3) ;
                $stmt3->execute();         
              
                
                $formnames = $stmt3->fetchAll(PDO::FETCH_OBJ);
                foreach ($formnames as $value4) {
                    $datacheck = array();
//                    echo $sql4 = "SELECT screen_names FROM tbl_allcare_facuservisit WHERE  ";
                    $sql4 = "SELECT (SELECT DISTINCT(SUBSTRING(lo.group_name FROM 2)) as group_name  
                                FROM layout_options lo
                                LEFT JOIN forms f ON lo.form_id = 'LBF1'
                                WHERE lo.title = '$value3->screen_name'
                                AND f.encounter = $eid) as form_type, f.form_id, (SELECT lo.field_id
                                                        FROM layout_options lo 
                                                        WHERE lo.title =  '$value3->screen_name' AND lo.form_id = 'LBF1') as form_name ,SUBSTRING(lo.group_name ,-length(lo.group_name),1) as grouporder,SUBSTRING(lo.group_name FROM 2) as GroupName,
                                (SELECT lo.description
                                                        FROM layout_options lo 
                                                        WHERE lo.title =  '$value3->screen_name' AND lo.form_id = 'LBF1') as  description, 
                                    CASE lf.field_value
                                        WHEN 'finalized|pending' THEN 'finalized'
                                        WHEN 'pending|finalized' THEN 'finalized'
                                        WHEN 'pending' THEN 'pending'
                                        WHEN 'finalized' THEN 'finalized'
                                    END as   field_value  
                                FROM layout_options lo
                                INNER JOIN forms f ON lo.form_id = f.formdir
                                INNER JOIN lbf_data lf ON lf.form_id = f.form_id
                                AND lo.field_id = lf.field_id
                                WHERE lo.form_id = 'LBF2' 
                                AND lo.field_id  LIKE '".$value4->field_id."_stat'
                                AND f.encounter = $eid
                                AND f.deleted=0";
                    $stmt4 = $db->prepare($sql4) ;
                    $stmt4->execute();    
                    $datacheck = $stmt4->fetchAll(PDO::FETCH_OBJ);  
                    
                    if(!empty($datacheck)){
                        $get_isRequired = "SELECT id,screen_names FROM tbl_allcare_facuservisit WHERE screen_group LIKE '%".$datacheck[0]->form_type."' AND `users`  REGEXP ('".":\"".$value->provider_id."\"') ";
                        $db->query( "SET NAMES utf8");
                        $isReq_stmt = $db->prepare($get_isRequired) ;
                        $isReq_stmt->execute();    
                        $req_datacheck = $isReq_stmt->fetchAll(PDO::FETCH_OBJ); 
                        $s_array = array();
                        $dataArray = '';
                        if(!empty($req_datacheck)){
                            $s_array =  unserialize( $req_datacheck[0]->screen_names);
                            for($j = 0; $j<count($s_array); $j++){
//                                foreach($s_array[$j] as $arraykey){
                                    if(strpos($s_array[$j],$datacheck[0]->form_name) !== false){
                                       $dataArray = $s_array[$j];
                                    }
//                                }
                            }
                            $fields = explode('$$', $dataArray);
                            if(!empty($fields)){
                                $datacheck[0]->FormOrder = (isset($fields[0]) ? $fields[0] : '');
                                $datacheck[0]->isRequired = (isset($fields[1]) ? $fields[1] : '');
                            }else{
                                $datacheck[0]->FormOrder = '';
                                $datacheck[0]->isRequired = '';
                            }
                            $datacheck[0]->id = $req_datacheck[0]->id;
                        }else{
                            $datacheck[0]->FormOrder = '';
                            $datacheck[0]->isRequired = '';
                            $datacheck[0]->id = '';
                        }
                    }
//                    echo "<pre>"; print_r($datacheck); echo "</pre>";
                        if(empty($datacheck) || ($value4->field_id == 'ros') || ($value4->field_id == 'physical_exam')){ 
                            $field_value = '';
//                            $sql5 = "SELECT l.field_id, l.seq as FormOrder,SUBSTRING(l.group_name ,-length(l.group_name),1) as grouporder,SUBSTRING(l.group_name FROM 2) as GroupName,
//                                     CASE l.uor
//                                    WHEN 0 THEN 'UnUsed' 
//                                    WHEN 1 THEN 'Optional'
//                                    WHEN 2 THEN 'Required'
//                                    END as isRequired,l.description
//                                from layout_options l WHERE l.title LIKE '$value3->screen_name' and l.form_id='LBF1' ";
                            $sql5 = "SELECT l.field_id, SUBSTRING(l.group_name ,-length(l.group_name),1) as grouporder,SUBSTRING(l.group_name FROM 2) as GroupName,
                                     l.description
                                from layout_options l WHERE l.title LIKE '$value3->screen_name' and l.form_id='LBF1' ";
                            $stmt5 = $db->prepare($sql5);
                            $stmt5->execute();  
                            $datacheck2 = $stmt5->fetchAll(PDO::FETCH_OBJ); 
                            
                            $get_isRequired = "SELECT id,screen_names FROM tbl_allcare_facuservisit WHERE screen_group LIKE '%".$datacheck2[0]->GroupName."' AND `users`  REGEXP ('".":\"".$value->provider_id."\"') ";
                            $db->query( "SET NAMES utf8");
                            $isReq_stmt = $db->prepare($get_isRequired) ;
                            $isReq_stmt->execute();    
                            $req_datacheck = $isReq_stmt->fetchAll(PDO::FETCH_OBJ); 
                            $s_array = array();
                            $dataArray = '';
                            if(!empty($req_datacheck)){
                                $s_data = $req_datacheck[0]->screen_names;
                                $s_array[] =  unserialize($s_data);
                                for($j = 0; $j<count($s_array); $j++){
                                    foreach($s_array[$j] as $arraykey){
                                        if(strpos($arraykey,$datacheck2[0]->field_id) !== false){
                                           $dataArray = $arraykey;
                                        }
                                    }
                                }
                                $fields = explode('$$', $dataArray);
                                if(!empty($fields)){
                                    $datacheck2[0]->FormOrder = (isset($fields[0]) ? $fields[0] : '');
                                    $datacheck2[0]->isRequired = (isset($fields[1]) ? $fields[1] : '');
                                }else{
                                    $datacheck2[0]->FormOrder = '';
                                    $datacheck2[0]->isRequired = '';
                                }
                                $datacheck2[0]->id = $req_datacheck[0]->id;
                            }else{
                                $datacheck2[0]->FormOrder = '';
                                $datacheck2[0]->isRequired = '';
                                $datacheck2[0]->id = '';
                            }
                            
                            //echo "<pre>"; print_r($datacheck2); echo "</pre>"; 
                            $sql6 = "SELECT form_id from forms WHERE encounter = $eid and deleted = 0 and formdir='LBF2'";
                            $stmt6 = $db->prepare($sql6);
                            $stmt6->execute();  
                            $datacheck3 = $stmt6->fetchAll(PDO::FETCH_OBJ);
                            if(!empty($datacheck3)):
                                $form_id = $datacheck3[0]->form_id;
                            else:
                                $form_id = 0;
                            endif;
                            if($datacheck2[0]->field_id == 'physical_exam'):
                                $sql7 = "SELECT form_id  from forms WHERE encounter = $eid and deleted = 0 and form_name = 'Allcare Physical Exam' and formdir='allcare_physical_exam'";
                                $stmt7 = $db->prepare($sql7);
                                $stmt7->execute();  
                                $datacheck4 = $stmt7->fetchAll(PDO::FETCH_OBJ);
                                if(!empty($datacheck4)):
                                    $form_id = $datacheck4[0]->form_id;
                                else:
                                    $form_id = 0;
                                endif;
                                if($form_id != 0){
                                    $get_status = "SELECT * FROM tbl_allcare_formflag WHERE form_name='Allcare Physical Exam' AND encounter_id=$eid  AND form_id = $form_id";
                                    $f_status = $db->prepare($get_status);
                                    $f_status->execute();  
                                    $set_status = $f_status->fetchAll(PDO::FETCH_OBJ);
                                    if(!empty($set_status)){
                                        if($set_status[0]->pending == 'Y')
                                            $field_value = 'pending';
                                        elseif($set_status[0]->finalized == 'Y')
                                            $field_value = 'finalized';
                                        else
                                            $field_value = '';
                                    }else{
                                        if(!empty($datacheck[0]->field_value))
                                            $field_value = $datacheck[0]->field_value;
                                        else
                                            $field_value = '';
                                    }
                                }else{
                                        $field_value = '';
                                }
                            endif;
                            if($datacheck2[0]->field_id == 'ros'):
                                $sql8 = "SELECT form_id  from forms WHERE encounter = $eid and deleted = 0 and form_name = 'Allcare Review Of Systems' and formdir='allcare_ros'";
                                $stmt8 = $db->prepare($sql8);
                                $stmt8->execute();  
                                $datacheck5 = $stmt8->fetchAll(PDO::FETCH_OBJ);
                                if(!empty($datacheck5)):
                                    $form_id = $datacheck5[0]->form_id;
                                else:
                                    $form_id = 0;
                                endif;
                                if($form_id != 0){
                                    $get_status = "SELECT * FROM tbl_allcare_formflag WHERE form_name='Allcare Review of Systems' AND encounter_id=$eid  AND form_id = $form_id";
                                    $f_status = $db->prepare($get_status);
                                    $f_status->execute();  
                                    $set_status = $f_status->fetchAll(PDO::FETCH_OBJ);
                                    if(!empty($set_status)){
                                        if($set_status[0]->pending == 'Y')
                                            $field_value = 'pending';
                                        elseif($set_status[0]->finalized == 'Y')
                                            $field_value = 'finalized';
                                        else
                                            $field_value = '';
                                    }else{
                                        if(!empty($datacheck[0]->field_value))
                                            $field_value = $datacheck[0]->field_value;
                                        else
                                            $field_value = '';
                                    }
                                }else{
                                        $field_value = '';
                                }
                            endif;
                            if($datacheck2[0]->field_id == 'vitals'):
                                $sql9 = "SELECT form_id  from forms WHERE encounter = $eid and deleted = 0 and form_name = 'Vitals' and formdir='vitals'";
                                $stmt9 = $db->prepare($sql9);
                                $stmt9->execute();  
                                $datacheck6 = $stmt9->fetchAll(PDO::FETCH_OBJ);
                                if(!empty($datacheck6)):
                                    $form_id = $datacheck6[0]->form_id;
                                else:
                                    $form_id = 0;
                                endif;    
                                if($form_id != 0){
                                    $vitals  = "SELECT field_value FROM lbf_data WHERE form_id = $form_id AND field_id = 'vitals_stat'";
                                    $stmtV = $db->prepare($vitals);
                                    $stmtV->execute();
                                    $set_status = $stmtV->fetchAll(PDO::FETCH_OBJ);
                                    
                                    if(!empty($set_status)){
                                        if($set_status[0]->field_value == 'pending')
                                            $field_value = 'pending';
                                        elseif($set_status[0]->field_value == 'finalized')
                                            $field_value = 'finalized';
                                        elseif($set_status[0]->field_value == 'finalized|pending' || $set_status[0]->field_value == 'pending|finalized')
                                            $datacheck[0]->field_value = 'finalized';
                                        else
                                            $field_value = '';
                                    }else{
                                        if(!empty($datacheck[0]->field_value))
                                            $field_value = $datacheck[0]->field_value;
                                        else
                                            $field_value = '';
                                    }
                                }else{
                                        $field_value = '';
                                }
                            endif;
                            if($datacheck2[0]->field_id == 'family_exam_test' || $datacheck2[0]->field_id == 'family_history'|| $datacheck2[0]->field_id == 'family_med_con' || $datacheck2[0]->field_id == 'history_past' || $datacheck2[0]->field_id == 'history_social'):
//                                $form_id = 0;
//                                $field_value = '';
                                $get_form_status = "SELECT form_id FROM forms where deleted = 0 and formdir = 'LBF2' and form_name = 'Allcare Encounter Forms' and encounter = $eid order by id asc limit 0,1";
                                $stmtf = $db->prepare($get_form_status);
                                $stmtf->execute();
                                $set_form_status = $stmtf->fetchAll(PDO::FETCH_OBJ);
                                if(!empty($set_form_status)):
                                    $form_id = $set_form_status[0]->form_id;
                                else:
                                    $form_id = 0;
                                endif;    
                                if($form_id != 0){
                                    $vitals  = "SELECT field_value FROM lbf_data WHERE form_id = $form_id AND field_id = '".$datacheck2[0]->field_id."_stat'";
                                    $stmtV = $db->prepare($vitals);
                                    $stmtV->execute();
                                    $set_status = $stmtV->fetchAll(PDO::FETCH_OBJ);
                                    
                                    if(!empty($set_status)){
                                        if($set_status[0]->field_value == 'pending')
                                            $field_value = 'pending';
                                        elseif($set_status[0]->field_value == 'finalized')
                                            $field_value = 'finalized';
                                        elseif($set_status[0]->field_value == 'finalized|pending' || $set_status[0]->field_value == 'pending|finalized')
                                            $datacheck[0]->field_value = 'finalized';
                                        else
                                            $field_value = '';
                                    }else{
                                        if(!empty($datacheck[0]->field_value)){
                                             if($datacheck[0]->field_value == 'pending')
                                                $field_value = 'pending';
                                            elseif($datacheck[0]->field_value == 'finalized')
                                                $field_value = 'finalized';
                                            elseif(trim($datacheck[0]->field_value) == 'finalized|pending' || trim($datacheck[0]->field_value) == 'pending|finalized')
                                                $datacheck[0]->field_value = 'finalized';
                                            else
                                                $field_value = '';
                                        }else
                                            $field_value = '';
                                    }
                                }else{
                                        $field_value = '';
                                }
                            endif;
                            if($datacheck2[0]->field_id  == 'allergies' || $datacheck2[0]->field_id  == 'dental_problems'  || $datacheck2[0]->field_id  == 'immunization' || $datacheck2[0]->field_id  == 'medical_problem'|| $datacheck2[0]->field_id  == 'medication' ):
                                $form_id = 0;
                                $field_value = '';
                            endif;
                            if($datacheck2[0]->field_id  == 'face2face' ){
                                $get_form_status = "SELECT form_id FROM forms where deleted = 0 and formdir = 'LBF2' and form_name = 'Allcare Encounter Forms' and encounter = $eid order by id asc limit 0,1";
                                $stmtf = $db->prepare($get_form_status);
                                $stmtf->execute();
                                $set_form_status = $stmtf->fetchAll(PDO::FETCH_OBJ);
                                if(!empty($set_form_status)):
                                    $form_id = $set_form_status[0]->form_id;
                                else:
                                    $form_id = 0;
                                endif;    
                                if($form_id != 0){
                                    $vitals  = "SELECT field_value FROM lbf_data WHERE form_id = $form_id AND field_id = 'f2f_stat'";
                                    $stmtV = $db->prepare($vitals);
                                    $stmtV->execute();
                                    $set_status = $stmtV->fetchAll(PDO::FETCH_OBJ);
                                    
                                    if(!empty($set_status)){
                                        if($set_status[0]->field_value == 'pending')
                                            $field_value = 'pending';
                                        elseif($set_status[0]->field_value == 'finalized')
                                            $field_value = 'finalized';
                                        elseif($set_status[0]->field_value == 'finalized|pending' || $set_status[0]->field_value == 'pending|finalized')
                                            $field_value = 'finalized';
                                        else
                                            $field_value = '';
                                    }else{
                                        if(!empty($datacheck2[0]->field_value)){
                                            if($datacheck2[0]->field_value == 'pending')
                                                $field_value = 'pending';
                                            elseif($datacheck2[0]->field_value == 'finalized')
                                                $field_value = 'finalized';
                                            elseif(trim($datacheck2[0]->field_value) == 'finalized|pending' || trim($datacheck2[0]->field_value) == 'pending|finalized')
                                                $field_value = 'finalized';
                                            else
                                                $field_value = '';
                                        }else
                                            $field_value = '';
                                    }
                                }else{
                                        $field_value = '';
                                }
                            }
                            if($datacheck2[0]->field_id  == 'codes'){
                                $selectquery = "SELECT b.billed
                                        FROM billing b 
                                        INNER JOIN form_encounter f ON b.encounter = f.encounter  
                                        WHERE b.encounter =   $eid and code_type='CPT4' and b.activity = 1 order by b.date desc ";
                                $stmtb = $db->prepare($selectquery);
                                $stmtb->execute();
                                $set_billing = $stmtb->fetchAll(PDO::FETCH_OBJ);
                                if(!empty($set_billing)){
                                    $form_id = 0;
                                    $billing = $set_billing[0]-> billed;
                                    if($billing == 0){
                                        $field_value = ' Not Billed';
                                    }else{
                                        $field_value = 'Billed';
                                    }
                                        
                                }else{
                                     $field_value = 'Not Billed';
                                }
                            }
                            if($datacheck2[0]->field_id == 'auditform'):
                                $sqla = "SELECT form_id  from forms WHERE encounter = $eid and deleted = 0 and form_name = 'Audit Form' and formdir='auditform'";
                                $stmta = $db->prepare($sqla);
                                $stmta->execute();  
                                $datachecka = $stmta->fetchAll(PDO::FETCH_OBJ);
                                if(!empty($datachecka)):
                                    $form_id = $datachecka[0]->form_id;
                                else:
                                    $form_id = 0;
                                endif;    
                                if($form_id != 0){
                                    $get_audit = "SELECT audit_data FROM tbl_form_audit WHERE id = $form_id ";
                                    $stmta = $db->prepare($get_audit);
                                    $stmta->execute();
                                    $set_status = $stmta->fetchAll(PDO::FETCH_OBJ);
                                    if(!empty($set_status)){
                                        $unserialized_data = unserialize($set_status[0]->audit_data);
                                        if(trim(str_replace('CPT Code:','',$unserialized_data['hiddenaudit'])) == 'None') 
                                            $field_value = 'Not Audited';
                                        else
                                            $field_value = 'Audited';
                                    }else{
                                        if(!empty($datacheck2[0]->field_value))
                                            $field_value = $datacheck2[0]->field_value;
                                        else
                                            $field_value = '';
                                    }
                                }else{
                                        $field_value = '';
                                }
                            endif;
                            if($datacheck2[0]->field_id == 'cpo'):
                                $sqlc = "SELECT form_id  from forms WHERE encounter = $eid and deleted = 0 and form_name = 'CPO' and formdir='cpo'";
                                $stmtc = $db->prepare($sqlc);
                                $stmtc->execute();  
                                $datacheckc = $stmtc->fetchAll(PDO::FETCH_OBJ);
                                if(!empty($datacheckc)):
                                    $form_id = $datacheck2[0]->form_id;
                                else:
                                    $form_id = 0;
                                endif;    
                            endif; 
                            if($datacheck2[0]->field_id == 'ccm'):
                                $sqlcm = "SELECT form_id  from forms WHERE encounter = $eid and deleted = 0 and form_name = 'CCM' and formdir='ccm'";
                                $stmtcm = $db->prepare($sqlcm);
                                $stmtcm->execute();  
                                $datacheckcm = $stmtcm->fetchAll(PDO::FETCH_OBJ);
                                if(!empty($datacheckcm)):
                                    $form_id = $datacheck2[0]->form_id;
                                else:
                                    $form_id = 0;
                                endif;    
                            endif; 
                            $formvalues[] = (object)array('form_type' => $datacheck2[0]->GroupName,  'form_id'=> $form_id, 'form_name' =>$datacheck2[0]->field_id, 'FormOrder' =>$datacheck2[0]->FormOrder, 'grouporder' => $datacheck2[0]->grouporder, 'GroupName' => $value3->screen_name, 'isRequired' => $datacheck2[0]->isRequired , 'description' => $datacheck2[0]->description, 'field_value' => $field_value, 'id'=>$datacheck2[0]->id);

                        }else{ 
                            if($datacheck[0]->form_name == 'vitals'):
                                $sql9 = "SELECT form_id  from forms WHERE encounter = $eid and deleted = 0 and form_name = 'Vitals' and formdir='vitals'";
                                $stmt9 = $db->prepare($sql9);
                                $stmt9->execute();  
                                $datacheck6 = $stmt9->fetchAll(PDO::FETCH_OBJ);
                                if(!empty($datacheck6)):
                                    $datacheck[0]->form_id = $datacheck6[0]->form_id;
                                else:
                                    $datacheck[0]->form_id = 0;
                                endif;  
                                if($form_id != 0){
                                    $vitals  = "SELECT field_value FROM lbf_data WHERE form_id = $form_id AND field_id = 'vitals_stat'";
                                    $stmtV = $db->prepare($vitals);
                                    $stmtV->execute();
                                    $set_status = $stmtV->fetchAll(PDO::FETCH_OBJ);
                                    
                                    if(!empty($set_status)){
                                        if($set_status[0]->field_value == 'pending')
                                            $field_value = 'pending';
                                        elseif($set_status[0]->field_value == 'finalized')
                                            $field_value = 'finalized';
                                        elseif($set_status[0]->field_value == 'finalized|pending' || $set_status[0]->field_value == 'pending|finalized')
                                            $datacheck[0]->field_value = 'finalized';
                                        else
                                            $field_value = '';
                                    }else{
                                        if(!empty($datacheck[0]->field_value))
                                            $field_value = $datacheck[0]->field_value;
                                        else
                                            $field_value = '';
                                    }
                                }else{
                                        $field_value = '';
                                }
                            endif;
                            
                            if($datacheck[0]->form_name == 'family_exam_test' || $datacheck[0]->form_name == 'family_history'|| $datacheck[0]->form_name == 'family_med_con' || $datacheck[0]->form_name == 'history_past' || $datacheck[0]->form_name == 'history_social'):
                               // $datacheck[0]->form_id = 0;
                                $get_form_status = "SELECT form_id FROM forms where deleted = 0 and formdir = 'LBF2' and form_name = 'Allcare Encounter Forms' and encounter = $eid order by id asc limit 0,1";
                                $stmtf = $db->prepare($get_form_status);
                                $stmtf->execute();
                                $set_form_status = $stmtf->fetchAll(PDO::FETCH_OBJ);
                                if(!empty($set_form_status)):
                                    $form_id = $set_form_status[0]->form_id;
                                else:
                                    $form_id = 0;
                                endif;    
                                if($form_id != 0){
                                    $vitals  = "SELECT field_value FROM lbf_data WHERE form_id = $form_id AND field_id = '".$datacheck[0]->form_name."_stat'";
                                    $stmtV = $db->prepare($vitals);
                                    $stmtV->execute();
                                    $set_status = $stmtV->fetchAll(PDO::FETCH_OBJ);
                                    
                                    if(!empty($set_status)){
                                        if($set_status[0]->field_value == 'pending')
                                            $field_value = 'pending';
                                        elseif($set_status[0]->field_value == 'finalized')
                                            $field_value = 'finalized';
                                        elseif(trim($set_status[0]->field_value) == 'finalized|pending' || trim($set_status[0]->field_value) == 'pending|finalized'){
                                            $datacheck[0]->field_value = 'finalized';
                                        }
                                            
                                        else
                                            $field_value = '';
                                    }else{ 
                                        if(!empty($datacheck[0]->field_value)){
                                            if($datacheck[0]->field_value == 'pending')
                                                $field_value = 'pending';
                                            elseif($datacheck[0]->field_value == 'finalized')
                                                $field_value = 'finalized';
                                            elseif(trim($datacheck[0]->field_value) == 'finalized|pending' || trim($datacheck[0]->field_value) == 'pending|finalized')
                                                $datacheck[0]->field_value = 'finalized';
                                            else
                                            $field_value = '';
                                        }else
                                            $field_value = '';
                                    }
                                }else{
                                        $field_value = '';
                                }
                            endif;
                            if($datacheck[0]->form_name  == 'allergies' || $datacheck[0]->form_name  == 'dental_problems' || $datacheck[0]->form_name  == 'immunization' || $datacheck[0]->form_name  == 'medical_problem'|| $datacheck[0]->form_name  == 'medication'):
                               $datacheck[0]->form_id = 0;
                            endif;
                            if($datacheck[0]->form_name  == 'codes'){
                                $selectquery = "SELECT b.billed
                                        FROM billing b 
                                        INNER JOIN form_encounter f ON b.encounter = f.encounter  
                                        WHERE b.encounter =   $eid and code_type='CPT4' and b.activity = 1 order by b.date desc ";
                                $stmtb = $db->prepare($selectquery);
                                $stmtb->execute();
                                $set_billing = $stmtb->fetchAll(PDO::FETCH_OBJ);
                                if(!empty($set_billing)){
                                    $datacheck[0]->form_id = 0;
                                    $billing = $set_billing[0]-> billed;
                                    if($billing == 0){
                                        $datacheck[0]->field_value = 'Not billed';
                                    }else{
                                        $datacheck[0]->field_value = 'Billed';
                                    }
                                        
                                }else{
                                     $datacheck[0]->field_value = 'Not billed';
                                }
                            }
                            if($datacheck[0]->form_name == 'auditform'):
                                $sqla = "SELECT form_id  from forms WHERE encounter = $eid and deleted = 0 and form_name = 'Audit Form' and formdir='auditform'";
                                $stmta = $db->prepare($sqla);
                                $stmta->execute();  
                                $datachecka = $stmta->fetchAll(PDO::FETCH_OBJ);
                                if(!empty($datachecka)):
                                    $form_id = $datachecka[0]->form_id;
                                else:
                                    $form_id = 0;
                                endif;    
                                if($form_id != 0){
                                   $get_audit = "SELECT audit_data FROM tbl_form_audit WHERE id = $form_id ";
                                    $stmta = $db->prepare($get_audit);
                                    $stmta->execute();
                                    $set_status = $stmta->fetchAll(PDO::FETCH_OBJ);
                                    if(!empty($set_status)){
                                        $unserialized_data = unserialize($set_status[0]->audit_data);
                                        if(trim(str_replace('CPT Code:','',$unserialized_data['hiddenaudit'])) == 'None') 
                                            $datacheck[0]->field_value = 'Not Audited';
                                        else
                                            $datacheck[0]->field_value = 'Audited';
                                    }else{
                                        $datacheck[0]->field_value = '';
                                    }
                                }else{
                                        $datacheck[0]->field_value = '';
                                }
                            endif;
                            if($datacheck[0]->form_name == 'cpo'):
                                $sqlc = "SELECT form_id  from forms WHERE encounter = $eid and deleted = 0 and form_name = 'CPO' and formdir='cpo'";
                                $stmtc = $db->prepare($sqlc);
                                $stmtc->execute();  
                                $datacheckc = $stmtc->fetchAll(PDO::FETCH_OBJ);
                                if(!empty($datacheckc)):
                                    $datacheck[0]->form_id = $datacheckc[0]->form_id;
                                else:
                                    $datacheck[0]->form_id = 0;
                                endif;    
                            endif;    
                            if($datacheck[0]->form_name == 'ccm'):
                                $sqlcm = "SELECT form_id  from forms WHERE encounter = $eid and deleted = 0 and form_name = 'CCM' and formdir='ccm'";
                                $stmtcm = $db->prepare($sqlcm);
                                $stmtcm->execute();  
                                $datacheckcm = $stmtcm->fetchAll(PDO::FETCH_OBJ);
                                if(!empty($datacheckcm)):
                                    $datacheck[0]->form_id = $datacheckcm[0]->form_id;
                                else:
                                    $datacheck[0]->form_id = 0;
                                endif;    
                            endif;    
                           $formvalues[] = $datacheck[0];//$stmt4->fetchAll(PDO::FETCH_OBJ); 
                        }
                }
             } 
        }
        $itemArray = array();
        foreach($formvalues as $item) {
           $itemArray[] = (array)$item;
        }
//        $sorted2 =uasort($itemArray, 'cmp');
        $sorted = array_orderby($itemArray, 'FormOrder', SORT_ASC);
        $arr = array();
        for($i=0; $i<count($sorted); $i++){
            $check = array_search('Unused', $sorted[$i],TRUE);
            if(empty($check)){
                $arr[] = $sorted[$i];
            }
        }
//        echo "<pre>"; print_r($arr);echo "</pre>";
        
             $new = encode_demo(array_filter( $arr));
             $newdemo['FormsData'] = check_data_available($new);
            if($newdemo) {
                $newdemores = json_encode($newdemo);
                echo $incompletelistresult = GibberishAES::enc($newdemores, $apikey);

            }else
            {
                $demo1='[{"id":"0"}]';
                $newdemo1=encode_demo($demo1);      
                $newdemo['FormsData'] = check_data_available($newdemo1);
                $newdemores = json_encode($newdemo);
                echo $incompletelistresult = GibberishAES::enc($newdemores, $apikey);
            }
    } 
    catch(PDOException $e) 
    {

        $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo $incompletelistresult = GibberishAES::enc($error, $apikey);
    }
  
}
function getDosFormData3($eid){
  try 
    {
        $db = getConnection();
        
        $apikey = 'rotcoderaclla';
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        
        $sql = "SELECT f.pc_catid, o.pc_aid as provider_id, f.facility_id, p.pid
                FROM  `form_encounter` f 
                INNER JOIN patient_data p ON p.pid = f.pid
                inner join openemr_postcalendar_events o on f.pid = o.pc_pid and f.pc_catid = o.pc_catid and o.pc_eventDate = DATE_FORMAT(f.date,'%Y-%m-%d')
                WHERE encounter =$eid ";
        
        $stmt = $db->prepare($sql) ; 
        $stmt->execute();
        $namesresult = $stmt->fetchAll(PDO::FETCH_OBJ); 
        foreach ($namesresult as $pidval) {
            $pid = $pidval->pid;
        }
        $fieldnamesresult = array();
        $formnames = array();
        $formfields = array();
        $newdemo = array();
        $formvalues = array();
        foreach ($namesresult as $value) {
            
            $sql2 ="SELECT DISTINCT  l.title AS screen_name,f.screen_group, l.description AS screen_link
                FROM  `tbl_allcare_facuservisit` f
                INNER JOIN layout_options l ON l.group_name = f.screen_group
                AND l.form_id = f.form_id
                WHERE  `facilities` REGEXP ('".":\"".$value->facility_id."\"') AND  `users` REGEXP ('".":\"".$value->provider_id."\"') AND  `visit_categories` REGEXP ('".":\"".$value->pc_catid."\"') order by f.id ";      
            $stmt2 = $db->prepare($sql2); 
            $stmt2->execute();
            $fieldnamesresult[] = $stmt2->fetchAll(PDO::FETCH_OBJ);
            
        } 
//        echo "<pre>"; print_r($fieldnamesresult); echo "</pre>";
        foreach ($fieldnamesresult as $value2) {
            
            foreach ($value2 as $value3) {
                $sql3 = "SELECT DISTINCT(SUBSTRING(lo.group_name FROM 2)) as group_name , lo.field_id 
                                FROM layout_options lo
                                LEFT JOIN forms f ON lo.form_id = 'LBF1'
                                WHERE lo.title = '$value3->screen_name'
                                AND f.encounter = $eid";
                $stmt3 = $db->prepare($sql3) ;
                $stmt3->execute();         
              
                
                $formnames = $stmt3->fetchAll(PDO::FETCH_OBJ);
                foreach ($formnames as $value4) {
                    $datacheck = array();
//                    echo $sql4 = "SELECT screen_names FROM tbl_allcare_facuservisit WHERE  ";
                    $sql4 = "SELECT (SELECT DISTINCT(SUBSTRING(lo.group_name FROM 2)) as group_name  
                                FROM layout_options lo
                                LEFT JOIN forms f ON lo.form_id = 'LBF1'
                                WHERE lo.title = '$value3->screen_name'
                                AND f.encounter = $eid) as form_type, f.form_id, (SELECT lo.field_id
                                                        FROM layout_options lo 
                                                        WHERE lo.title =  '$value3->screen_name' AND lo.form_id = 'LBF1') as form_name ,SUBSTRING(lo.group_name ,-length(lo.group_name),1) as grouporder,SUBSTRING(lo.group_name FROM 2) as GroupName,
                                (SELECT lo.description
                                                        FROM layout_options lo 
                                                        WHERE lo.title =  '$value3->screen_name' AND lo.form_id = 'LBF1') as  description, 
                                    CASE lf.field_value
                                        WHEN 'finalized|pending' THEN 'finalized'
                                        WHEN 'pending|finalized' THEN 'finalized'
                                        WHEN 'pending' THEN 'pending'
                                        WHEN 'finalized' THEN 'finalized'
                                    END as   field_value  
                                FROM layout_options lo
                                INNER JOIN forms f ON lo.form_id = f.formdir
                                INNER JOIN lbf_data lf ON lf.form_id = f.form_id
                                AND lo.field_id = lf.field_id
                                WHERE lo.form_id = 'LBF2' 
                                AND lo.field_id  LIKE '".$value4->field_id."_stat'
                                AND f.encounter = $eid
                                AND f.deleted=0";
                    $stmt4 = $db->prepare($sql4) ;
                    $stmt4->execute();    
                    $datacheck = $stmt4->fetchAll(PDO::FETCH_OBJ);  
                    
                    if(!empty($datacheck)){
                        $get_isRequired = "SELECT id,screen_names FROM tbl_allcare_facuservisit WHERE screen_group LIKE '%".$datacheck[0]->form_type."' AND `users`  REGEXP ('".":\"".$value->provider_id."\"') ";
                        $db->query( "SET NAMES utf8");
                        $isReq_stmt = $db->prepare($get_isRequired) ;
                        $isReq_stmt->execute();    
                        $req_datacheck = $isReq_stmt->fetchAll(PDO::FETCH_OBJ); 
                        $s_array = array();
                        $dataArray = '';
                        if(!empty($req_datacheck)){
                            $s_array =  unserialize( $req_datacheck[0]->screen_names);
                            for($j = 0; $j<count($s_array); $j++){
//                                foreach($s_array[$j] as $arraykey){
                                    if(strpos($s_array[$j],$datacheck[0]->form_name) !== false){
                                       $dataArray = $s_array[$j];
                                    }
//                                }
                            }
                            $fields = explode('$$', $dataArray);
                            if(!empty($fields)){
                                $datacheck[0]->FormOrder = (isset($fields[0]) ? $fields[0] : '');
                                $datacheck[0]->isRequired = (isset($fields[1]) ? $fields[1] : '');
                            }else{
                                $datacheck[0]->FormOrder = '';
                                $datacheck[0]->isRequired = '';
                            }
                            $datacheck[0]->id = $req_datacheck[0]->id;
                        }else{
                            $datacheck[0]->FormOrder = '';
                            $datacheck[0]->isRequired = '';
                            $datacheck[0]->id = '';
                        }
                    }
//                    echo "<pre>"; print_r($datacheck); echo "</pre>";
                        if(empty($datacheck) || ($value4->field_id == 'ros') || ($value4->field_id == 'physical_exam')){ 
                            $field_value = '';
//                            $sql5 = "SELECT l.field_id, l.seq as FormOrder,SUBSTRING(l.group_name ,-length(l.group_name),1) as grouporder,SUBSTRING(l.group_name FROM 2) as GroupName,
//                                     CASE l.uor
//                                    WHEN 0 THEN 'UnUsed' 
//                                    WHEN 1 THEN 'Optional'
//                                    WHEN 2 THEN 'Required'
//                                    END as isRequired,l.description
//                                from layout_options l WHERE l.title LIKE '$value3->screen_name' and l.form_id='LBF1' ";
                            $sql5 = "SELECT l.field_id, SUBSTRING(l.group_name ,-length(l.group_name),1) as grouporder,SUBSTRING(l.group_name FROM 2) as GroupName,
                                     l.description
                                from layout_options l WHERE l.title LIKE '$value3->screen_name' and l.form_id='LBF1' ";
                            $stmt5 = $db->prepare($sql5);
                            $stmt5->execute();  
                            $datacheck2 = $stmt5->fetchAll(PDO::FETCH_OBJ); 
                            
                            $get_isRequired = "SELECT id,screen_names FROM tbl_allcare_facuservisit WHERE screen_group LIKE '%".$datacheck2[0]->GroupName."' AND `users`  REGEXP ('".":\"".$value->provider_id."\"') ";
                            $db->query( "SET NAMES utf8");
                            $isReq_stmt = $db->prepare($get_isRequired) ;
                            $isReq_stmt->execute();    
                            $req_datacheck = $isReq_stmt->fetchAll(PDO::FETCH_OBJ); 
                            $s_array = array();
                            $dataArray = '';
                            if(!empty($req_datacheck)){
                                $s_data = $req_datacheck[0]->screen_names;
                                $s_array[] =  unserialize($s_data);
                                for($j = 0; $j<count($s_array); $j++){
                                    foreach($s_array[$j] as $arraykey){
                                        if(strpos($arraykey,$datacheck2[0]->field_id) !== false){
                                           $dataArray = $arraykey;
                                        }
                                    }
                                }
                                $fields = explode('$$', $dataArray);
                                if(!empty($fields)){
                                    $datacheck2[0]->FormOrder = (isset($fields[0]) ? $fields[0] : '');
                                    $datacheck2[0]->isRequired = (isset($fields[1]) ? $fields[1] : '');
                                }else{
                                    $datacheck2[0]->FormOrder = '';
                                    $datacheck2[0]->isRequired = '';
                                }
                                $datacheck2[0]->id = $req_datacheck[0]->id;
                            }else{
                                $datacheck2[0]->FormOrder = '';
                                $datacheck2[0]->isRequired = '';
                                $datacheck2[0]->id = '';
                            }
                            
                            //echo "<pre>"; print_r($datacheck2); echo "</pre>"; 
                            $sql6 = "SELECT form_id from forms WHERE encounter = $eid and deleted = 0 and formdir='LBF2'";
                            $stmt6 = $db->prepare($sql6);
                            $stmt6->execute();  
                            $datacheck3 = $stmt6->fetchAll(PDO::FETCH_OBJ);
                            if(!empty($datacheck3)):
                                $form_id = $datacheck3[0]->form_id;
                            else:
                                $form_id = 0;
                            endif;
                            if($datacheck2[0]->field_id == 'physical_exam'):
                                $sql7 = "SELECT form_id  from forms WHERE encounter = $eid and deleted = 0 and form_name = 'Allcare Physical Exam' and formdir='allcare_physical_exam'";
                                $stmt7 = $db->prepare($sql7);
                                $stmt7->execute();  
                                $datacheck4 = $stmt7->fetchAll(PDO::FETCH_OBJ);
                                if(!empty($datacheck4)):
                                    $form_id = $datacheck4[0]->form_id;
                                else:
                                    $form_id = 0;
                                endif;
                                if($form_id != 0){
                                    $get_status = "SELECT * FROM tbl_allcare_formflag WHERE form_name='Allcare Physical Exam' AND encounter_id=$eid  AND form_id = $form_id";
                                    $f_status = $db->prepare($get_status);
                                    $f_status->execute();  
                                    $set_status = $f_status->fetchAll(PDO::FETCH_OBJ);
                                    if(!empty($set_status)){
                                        if($set_status[0]->pending == 'Y')
                                            $field_value = 'pending';
                                        elseif($set_status[0]->finalized == 'Y')
                                            $field_value = 'finalized';
                                        else
                                            $field_value = '';
                                    }else{
                                        if(!empty($datacheck[0]->field_value))
                                            $field_value = $datacheck[0]->field_value;
                                        else
                                            $field_value = '';
                                    }
                                }else{
                                        $field_value = '';
                                }
                            endif;
                            if($datacheck2[0]->field_id == 'ros'):
                                $sql8 = "SELECT form_id  from forms WHERE encounter = $eid and deleted = 0 and form_name = 'Allcare Review Of Systems' and formdir='allcare_ros'";
                                $stmt8 = $db->prepare($sql8);
                                $stmt8->execute();  
                                $datacheck5 = $stmt8->fetchAll(PDO::FETCH_OBJ);
                                if(!empty($datacheck5)):
                                    $form_id = $datacheck5[0]->form_id;
                                else:
                                    $form_id = 0;
                                endif;
                                if($form_id != 0){
                                    $get_status = "SELECT * FROM tbl_allcare_formflag WHERE form_name='Allcare Review of Systems' AND encounter_id=$eid  AND form_id = $form_id";
                                    $f_status = $db->prepare($get_status);
                                    $f_status->execute();  
                                    $set_status = $f_status->fetchAll(PDO::FETCH_OBJ);
                                    if(!empty($set_status)){
                                        if($set_status[0]->pending == 'Y')
                                            $field_value = 'pending';
                                        elseif($set_status[0]->finalized == 'Y')
                                            $field_value = 'finalized';
                                        else
                                            $field_value = '';
                                    }else{
                                        if(!empty($datacheck[0]->field_value))
                                            $field_value = $datacheck[0]->field_value;
                                        else
                                            $field_value = '';
                                    }
                                }else{
                                        $field_value = '';
                                }
                            endif;
                            if($datacheck2[0]->field_id == 'vitals'):
                                $sql9 = "SELECT form_id  from forms WHERE encounter = $eid and deleted = 0 and form_name = 'Vitals' and formdir='vitals'";
                                $stmt9 = $db->prepare($sql9);
                                $stmt9->execute();  
                                $datacheck6 = $stmt9->fetchAll(PDO::FETCH_OBJ);
                                if(!empty($datacheck6)):
                                    $form_id = $datacheck6[0]->form_id;
                                else:
                                    $form_id = 0;
                                endif;    
                                if($form_id != 0){
                                    $vitals  = "SELECT field_value FROM lbf_data WHERE form_id = $form_id AND field_id = 'vitals_stat'";
                                    $stmtV = $db->prepare($vitals);
                                    $stmtV->execute();
                                    $set_status = $stmtV->fetchAll(PDO::FETCH_OBJ);
                                    
                                    if(!empty($set_status)){
                                        if($set_status[0]->field_value == 'pending')
                                            $field_value = 'pending';
                                        elseif($set_status[0]->field_value == 'finalized')
                                            $field_value = 'finalized';
                                        elseif($set_status[0]->field_value == 'finalized|pending' || $set_status[0]->field_value == 'pending|finalized')
                                            $datacheck[0]->field_value = 'finalized';
                                        else
                                            $field_value = '';
                                    }else{
                                        if(!empty($datacheck[0]->field_value))
                                            $field_value = $datacheck[0]->field_value;
                                        else
                                            $field_value = '';
                                    }
                                }else{
                                        $field_value = '';
                                }
                            endif;
                            if($datacheck2[0]->field_id == 'family_exam_test' || $datacheck2[0]->field_id == 'family_history'|| $datacheck2[0]->field_id == 'family_med_con' || $datacheck2[0]->field_id == 'history_past' || $datacheck2[0]->field_id == 'history_social'):
//                                $form_id = 0;
//                                $field_value = '';
                                $get_form_status = "SELECT form_id FROM forms where deleted = 0 and formdir = 'LBF2' and form_name = 'Allcare Encounter Forms' and encounter = $eid order by id asc limit 0,1";
                                $stmtf = $db->prepare($get_form_status);
                                $stmtf->execute();
                                $set_form_status = $stmtf->fetchAll(PDO::FETCH_OBJ);
                                if(!empty($set_form_status)):
                                    $form_id = $set_form_status[0]->form_id;
                                else:
                                    $form_id = 0;
                                endif;    
                                if($form_id != 0){
                                    $vitals  = "SELECT field_value FROM lbf_data WHERE form_id = $form_id AND field_id = '".$datacheck2[0]->field_id."_stat'";
                                    $stmtV = $db->prepare($vitals);
                                    $stmtV->execute();
                                    $set_status = $stmtV->fetchAll(PDO::FETCH_OBJ);
                                    
                                    if(!empty($set_status)){
                                        if($set_status[0]->field_value == 'pending')
                                            $field_value = 'pending';
                                        elseif($set_status[0]->field_value == 'finalized')
                                            $field_value = 'finalized';
                                        elseif($set_status[0]->field_value == 'finalized|pending' || $set_status[0]->field_value == 'pending|finalized')
                                            $datacheck[0]->field_value = 'finalized';
                                        else
                                            $field_value = '';
                                    }else{
                                        if(!empty($datacheck[0]->field_value)){
                                             if($datacheck[0]->field_value == 'pending')
                                                $field_value = 'pending';
                                            elseif($datacheck[0]->field_value == 'finalized')
                                                $field_value = 'finalized';
                                            elseif(trim($datacheck[0]->field_value) == 'finalized|pending' || trim($datacheck[0]->field_value) == 'pending|finalized')
                                                $datacheck[0]->field_value = 'finalized';
                                            else
                                                $field_value = '';
                                        }else
                                            $field_value = '';
                                    }
                                }else{
                                        $field_value = '';
                                }
                            endif;
                            if($datacheck2[0]->field_id  == 'allergies' || $datacheck2[0]->field_id  == 'dental_problems'  || $datacheck2[0]->field_id  == 'immunization' || $datacheck2[0]->field_id  == 'medical_problem'|| $datacheck2[0]->field_id  == 'medication' ):
                                $form_id = 0;
                                $field_value = '';
                            endif;
                            if($datacheck2[0]->field_id  == 'face2face' ){
                                $get_form_status = "SELECT form_id FROM forms where deleted = 0 and formdir = 'LBF2' and form_name = 'Allcare Encounter Forms' and encounter = $eid order by id asc limit 0,1";
                                $stmtf = $db->prepare($get_form_status);
                                $stmtf->execute();
                                $set_form_status = $stmtf->fetchAll(PDO::FETCH_OBJ);
                                if(!empty($set_form_status)):
                                    $form_id = $set_form_status[0]->form_id;
                                else:
                                    $form_id = 0;
                                endif;    
                                if($form_id != 0){
                                    $vitals  = "SELECT field_value FROM lbf_data WHERE form_id = $form_id AND field_id = 'f2f_stat'";
                                    $stmtV = $db->prepare($vitals);
                                    $stmtV->execute();
                                    $set_status = $stmtV->fetchAll(PDO::FETCH_OBJ);
                                    
                                    if(!empty($set_status)){
                                        if($set_status[0]->field_value == 'pending')
                                            $field_value = 'pending';
                                        elseif($set_status[0]->field_value == 'finalized')
                                            $field_value = 'finalized';
                                        elseif($set_status[0]->field_value == 'finalized|pending' || $set_status[0]->field_value == 'pending|finalized')
                                            $field_value = 'finalized';
                                        else
                                            $field_value = '';
                                    }else{
                                        if(!empty($datacheck2[0]->field_value)){
                                            if($datacheck2[0]->field_value == 'pending')
                                                $field_value = 'pending';
                                            elseif($datacheck2[0]->field_value == 'finalized')
                                                $field_value = 'finalized';
                                            elseif(trim($datacheck2[0]->field_value) == 'finalized|pending' || trim($datacheck2[0]->field_value) == 'pending|finalized')
                                                $field_value = 'finalized';
                                            else
                                                $field_value = '';
                                        }else
                                            $field_value = '';
                                    }
                                }else{
                                        $field_value = '';
                                }
                            }
                            if($datacheck2[0]->field_id  == 'codes'){
                                $selectquery = "SELECT b.billed
                                        FROM billing b 
                                        INNER JOIN form_encounter f ON b.encounter = f.encounter  
                                        WHERE b.encounter =   $eid and code_type='CPT4' and b.activity = 1 order by b.date desc ";
                                $stmtb = $db->prepare($selectquery);
                                $stmtb->execute();
                                $set_billing = $stmtb->fetchAll(PDO::FETCH_OBJ);
                                if(!empty($set_billing)){
                                    $form_id = 0;
                                    $billing = $set_billing[0]-> billed;
                                    if($billing == 0){
                                        $field_value = ' Not Billed';
                                    }else{
                                        $field_value = 'Billed';
                                    }
                                        
                                }else{
                                     $field_value = 'Not Billed';
                                }
                            }
                            if($datacheck2[0]->field_id == 'auditform'):
                                $sqla = "SELECT form_id  from forms WHERE encounter = $eid and deleted = 0 and form_name = 'Audit Form' and formdir='auditform'";
                                $stmta = $db->prepare($sqla);
                                $stmta->execute();  
                                $datachecka = $stmta->fetchAll(PDO::FETCH_OBJ);
                                if(!empty($datachecka)):
                                    $form_id = $datachecka[0]->form_id;
                                else:
                                    $form_id = 0;
                                endif;    
                                if($form_id != 0){
                                    $audit  = "SELECT field_value FROM lbf_data WHERE form_id = $form_id AND field_id = 'auditform_stat'";
                                    $stmta = $db->prepare($audit);
                                    $stmta->execute();
                                    $set_status = $stmta->fetchAll(PDO::FETCH_OBJ);
                                    
                                    if(!empty($set_status)){
                                        if($set_status[0]->field_value == 'pending')
                                            $field_value = 'pending';
                                        elseif($set_status[0]->field_value == 'finalized')
                                            $field_value = 'finalized';
                                        elseif($set_status[0]->field_value == 'finalized|pending' || $set_status[0]->field_value == 'pending|finalized')
                                            $datacheck2[0]->field_value = 'finalized';
                                        else
                                            $field_value = '';
                                    }else{
                                        if(!empty($datacheck2[0]->field_value))
                                            $field_value = $datacheck2[0]->field_value;
                                        else
                                            $field_value = '';
                                    }
                                }else{
                                        $field_value = '';
                                }
                            endif;
                            $formvalues[] = (object)array('form_type' => $datacheck2[0]->GroupName,  'form_id'=> $form_id, 'form_name' =>$datacheck2[0]->field_id, 'FormOrder' =>$datacheck2[0]->FormOrder, 'grouporder' => $datacheck2[0]->grouporder, 'GroupName' => $value3->screen_name, 'isRequired' => $datacheck2[0]->isRequired , 'description' => $datacheck2[0]->description, 'field_value' => $field_value, 'id'=>$datacheck2[0]->id);

                        }else{ 
                            if($datacheck[0]->form_name == 'vitals'):
                                $sql9 = "SELECT form_id  from forms WHERE encounter = $eid and deleted = 0 and form_name = 'Vitals' and formdir='vitals'";
                                $stmt9 = $db->prepare($sql9);
                                $stmt9->execute();  
                                $datacheck6 = $stmt9->fetchAll(PDO::FETCH_OBJ);
                                if(!empty($datacheck6)):
                                    $datacheck[0]->form_id = $datacheck6[0]->form_id;
                                else:
                                    $datacheck[0]->form_id = 0;
                                endif;  
                                if($form_id != 0){
                                    $vitals  = "SELECT field_value FROM lbf_data WHERE form_id = $form_id AND field_id = 'vitals_stat'";
                                    $stmtV = $db->prepare($vitals);
                                    $stmtV->execute();
                                    $set_status = $stmtV->fetchAll(PDO::FETCH_OBJ);
                                    
                                    if(!empty($set_status)){
                                        if($set_status[0]->field_value == 'pending')
                                            $field_value = 'pending';
                                        elseif($set_status[0]->field_value == 'finalized')
                                            $field_value = 'finalized';
                                        elseif($set_status[0]->field_value == 'finalized|pending' || $set_status[0]->field_value == 'pending|finalized')
                                            $datacheck[0]->field_value = 'finalized';
                                        else
                                            $field_value = '';
                                    }else{
                                        if(!empty($datacheck[0]->field_value))
                                            $field_value = $datacheck[0]->field_value;
                                        else
                                            $field_value = '';
                                    }
                                }else{
                                        $field_value = '';
                                }
                            endif;
                            
                            if($datacheck[0]->form_name == 'family_exam_test' || $datacheck[0]->form_name == 'family_history'|| $datacheck[0]->form_name == 'family_med_con' || $datacheck[0]->form_name == 'history_past' || $datacheck[0]->form_name == 'history_social'):
                               // $datacheck[0]->form_id = 0;
                                $get_form_status = "SELECT form_id FROM forms where deleted = 0 and formdir = 'LBF2' and form_name = 'Allcare Encounter Forms' and encounter = $eid order by id asc limit 0,1";
                                $stmtf = $db->prepare($get_form_status);
                                $stmtf->execute();
                                $set_form_status = $stmtf->fetchAll(PDO::FETCH_OBJ);
                                if(!empty($set_form_status)):
                                    $form_id = $set_form_status[0]->form_id;
                                else:
                                    $form_id = 0;
                                endif;    
                                if($form_id != 0){
                                    $vitals  = "SELECT field_value FROM lbf_data WHERE form_id = $form_id AND field_id = '".$datacheck[0]->form_name."_stat'";
                                    $stmtV = $db->prepare($vitals);
                                    $stmtV->execute();
                                    $set_status = $stmtV->fetchAll(PDO::FETCH_OBJ);
                                    
                                    if(!empty($set_status)){
                                        if($set_status[0]->field_value == 'pending')
                                            $field_value = 'pending';
                                        elseif($set_status[0]->field_value == 'finalized')
                                            $field_value = 'finalized';
                                        elseif(trim($set_status[0]->field_value) == 'finalized|pending' || trim($set_status[0]->field_value) == 'pending|finalized'){
                                            $datacheck[0]->field_value = 'finalized';
                                        }
                                            
                                        else
                                            $field_value = '';
                                    }else{ 
                                        if(!empty($datacheck[0]->field_value)){
                                            if($datacheck[0]->field_value == 'pending')
                                                $field_value = 'pending';
                                            elseif($datacheck[0]->field_value == 'finalized')
                                                $field_value = 'finalized';
                                            elseif(trim($datacheck[0]->field_value) == 'finalized|pending' || trim($datacheck[0]->field_value) == 'pending|finalized')
                                                $datacheck[0]->field_value = 'finalized';
                                            else
                                            $field_value = '';
                                        }else
                                            $field_value = '';
                                    }
                                }else{
                                        $field_value = '';
                                }
                            endif;
                            if($datacheck[0]->form_name  == 'allergies' || $datacheck[0]->form_name  == 'dental_problems' || $datacheck[0]->form_name  == 'immunization' || $datacheck[0]->form_name  == 'medical_problem'|| $datacheck[0]->form_name  == 'medication'):
                               $datacheck[0]->form_id = 0;
                            endif;
                            if($datacheck[0]->form_name  == 'codes'){
                                $selectquery = "SELECT b.billed
                                        FROM billing b 
                                        INNER JOIN form_encounter f ON b.encounter = f.encounter  
                                        WHERE b.encounter =   $eid and code_type='CPT4' and b.activity = 1 order by b.date desc ";
                                $stmtb = $db->prepare($selectquery);
                                $stmtb->execute();
                                $set_billing = $stmtb->fetchAll(PDO::FETCH_OBJ);
                                if(!empty($set_billing)){
                                    $datacheck[0]->form_id = 0;
                                    $billing = $set_billing[0]-> billed;
                                    if($billing == 0){
                                        $datacheck[0]->field_value = 'Not billed';
                                    }else{
                                        $datacheck[0]->field_value = 'Billed';
                                    }
                                        
                                }else{
                                     $datacheck[0]->field_value = 'Not billed';
                                }
                            }
                            if($datacheck[0]->form_name == 'auditform'):
                                $sqla = "SELECT form_id  from forms WHERE encounter = $eid and deleted = 0 and form_name = 'Audit Form' and formdir='auditform'";
                                $stmta = $db->prepare($sqla);
                                $stmta->execute();  
                                $datachecka = $stmta->fetchAll(PDO::FETCH_OBJ);
                                if(!empty($datachecka)):
                                    $form_id = $datachecka[0]->form_id;
                                else:
                                    $form_id = 0;
                                endif;    
                                if($form_id != 0){
                                    $audit  = "SELECT field_value FROM lbf_data WHERE form_id = $form_id AND field_id = 'auditform_stat'";
                                    $stmta = $db->prepare($audit);
                                    $stmta->execute();
                                    $set_status = $stmta->fetchAll(PDO::FETCH_OBJ);
                                    
                                    if(!empty($set_status)){
                                        if($set_status[0]->field_value == 'pending')
                                            $field_value = 'pending';
                                        elseif($set_status[0]->field_value == 'finalized')
                                            $field_value = 'finalized';
                                        elseif($set_status[0]->field_value == 'finalized|pending' || $set_status[0]->field_value == 'pending|finalized')
                                            $datacheck[0]->field_value = 'finalized';
                                        else
                                            $field_value = '';
                                    }else{
                                        if(!empty($datacheck[0]->field_value))
                                            $field_value = $datacheck[0]->field_value;
                                        else
                                            $field_value = '';
                                    }
                                }else{
                                        $field_value = '';
                                }
                            endif;
                           $formvalues[] = $datacheck[0];//$stmt4->fetchAll(PDO::FETCH_OBJ); 
                        }
                }
             } 
        }
        $itemArray = array();
        foreach($formvalues as $item) {
           $itemArray[] = (array)$item;
        }
//        $sorted2 =uasort($itemArray, 'cmp');
        $sorted = array_orderby($itemArray, 'FormOrder', SORT_ASC);
        $arr = array();
        for($i=0; $i<count($sorted); $i++){
            $check = array_search('Unused', $sorted[$i],TRUE);
            if(empty($check)){
                $arr[] = $sorted[$i];
            }
        }
//        echo "<pre>"; print_r($arr);echo "</pre>";
        
             $new = encode_demo(array_filter( $arr));
             $newdemo['FormsData'] = check_data_available($new);
            if($newdemo) {
                $newdemores = json_encode($newdemo);
                echo $incompletelistresult = GibberishAES::enc($newdemores, $apikey);

            }else
            {
                $demo1='[{"id":"0"}]';
                $newdemo1=encode_demo($demo1);      
                $newdemo['FormsData'] = check_data_available($newdemo1);
                $newdemores = json_encode($newdemo);
                echo $incompletelistresult = GibberishAES::enc($newdemores, $apikey);
            }
    } 
    catch(PDOException $e) 
    {

        $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo $incompletelistresult = GibberishAES::enc($error, $apikey);
    }
  
}
function getDosFormData2($eid){
  try 
    {
        $db = getConnection();
        
        $apikey = 'rotcoderaclla';
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        
        $sql = "SELECT f.pc_catid, o.pc_aid as provider_id, f.facility_id, p.pid
                FROM  `form_encounter` f 
                INNER JOIN patient_data p ON p.pid = f.pid
                inner join openemr_postcalendar_events o on f.pid = o.pc_pid and f.pc_catid = o.pc_catid and o.pc_eventDate = DATE_FORMAT(f.date,'%Y-%m-%d')
                WHERE encounter =$eid ";
        
        $stmt = $db->prepare($sql) ; 
        $stmt->execute();
        $namesresult = $stmt->fetchAll(PDO::FETCH_OBJ); 
        foreach ($namesresult as $pidval) {
            $pid = $pidval->pid;
        }
        $fieldnamesresult = array();
        $formnames = array();
        $formfields = array();
        $newdemo = array();
        $formvalues = array();
        foreach ($namesresult as $value) {
            
            $sql2 ="SELECT DISTINCT  l.title AS screen_name,f.screen_group, l.description AS screen_link
                FROM  `tbl_allcare_facuservisit` f
                INNER JOIN layout_options l ON l.group_name = f.screen_group
                AND l.form_id = f.form_id
                WHERE  `facilities` REGEXP ('".":\"".$value->facility_id."\"') AND  `users` REGEXP ('".":\"".$value->provider_id."\"') AND  `visit_categories` REGEXP ('".":\"".$value->pc_catid."\"') order by f.id ";      
   
            $stmt2 = $db->prepare($sql2); 
            $stmt2->execute();
            $fieldnamesresult[] = $stmt2->fetchAll(PDO::FETCH_OBJ);
            
        } 
//        echo "<pre>"; print_r($fieldnamesresult); echo "</pre>";
        foreach ($fieldnamesresult as $value2) {
            
            foreach ($value2 as $value3) {
                $sql3 = "SELECT DISTINCT(SUBSTRING(lo.group_name FROM 2)) as group_name , lo.field_id 
                                FROM layout_options lo
                                LEFT JOIN forms f ON lo.form_id = 'LBF1'
                                WHERE lo.title = '$value3->screen_name'
                                AND f.encounter = $eid";
                $stmt3 = $db->prepare($sql3) ;
                $stmt3->execute();         
              
                
                $formnames = $stmt3->fetchAll(PDO::FETCH_OBJ);
                foreach ($formnames as $value4) {
                    $datacheck = array();
//                    echo $sql4 = "SELECT screen_names FROM tbl_allcare_facuservisit WHERE  ";
                    $sql4 = "SELECT (SELECT DISTINCT(SUBSTRING(lo.group_name FROM 2)) as group_name  
                                FROM layout_options lo
                                LEFT JOIN forms f ON lo.form_id = 'LBF1'
                                WHERE lo.title = '$value3->screen_name'
                                AND f.encounter = $eid) as form_type, f.form_id, (SELECT lo.field_id
                                                        FROM layout_options lo 
                                                        WHERE lo.title =  '$value3->screen_name' AND lo.form_id = 'LBF1') as form_name ,SUBSTRING(lo.group_name ,-length(lo.group_name),1) as grouporder,SUBSTRING(lo.group_name FROM 2) as GroupName,
                                (SELECT lo.description
                                                        FROM layout_options lo 
                                                        WHERE lo.title =  '$value3->screen_name' AND lo.form_id = 'LBF1') as  description, 
                                    CASE lf.field_value
                                        WHEN 'finalized|pending' THEN 'finalized'
                                        WHEN 'pending|finalized' THEN 'finalized'
                                        WHEN 'pending' THEN 'pending'
                                        WHEN 'finalized' THEN 'finalized'
                                    END as   field_value  
                                FROM layout_options lo
                                INNER JOIN forms f ON lo.form_id = f.formdir
                                INNER JOIN lbf_data lf ON lf.form_id = f.form_id
                                AND lo.field_id = lf.field_id
                                WHERE lo.form_id = 'LBF2' 
                                AND lo.field_id  LIKE '".$value4->field_id."_stat'
                                AND f.encounter = $eid
                                AND f.deleted=0";
                    $stmt4 = $db->prepare($sql4) ;
                    $stmt4->execute();    
                    $datacheck = $stmt4->fetchAll(PDO::FETCH_OBJ);  
                    
                    if(!empty($datacheck)){
                        $get_isRequired = "SELECT id,screen_names FROM tbl_allcare_facuservisit WHERE screen_group LIKE '%".$datacheck[0]->form_type."' AND `users`  REGEXP ('".":\"".$value->provider_id."\"') ";
                        $db->query( "SET NAMES utf8");
                        $isReq_stmt = $db->prepare($get_isRequired) ;
                        $isReq_stmt->execute();    
                        $req_datacheck = $isReq_stmt->fetchAll(PDO::FETCH_OBJ); 
                        $s_array = array();
                        $dataArray = '';
                        if(!empty($req_datacheck)){
                            $s_array =  unserialize( $req_datacheck[0]->screen_names);
                            for($j = 0; $j<count($s_array); $j++){
//                                foreach($s_array[$j] as $arraykey){
                                    if(strpos($s_array[$j],$datacheck[0]->form_name) !== false){
                                       $dataArray = $s_array[$j];
                                    }
//                                }
                            }
                            $fields = explode('$$', $dataArray);
                            if(!empty($fields)){
                                $datacheck[0]->FormOrder = (isset($fields[0]) ? $fields[0] : '');
                                $datacheck[0]->isRequired = (isset($fields[1]) ? $fields[1] : '');
                            }else{
                                $datacheck[0]->FormOrder = '';
                                $datacheck[0]->isRequired = '';
                            }
                            $datacheck[0]->id = $req_datacheck[0]->id;
                        }else{
                            $datacheck[0]->FormOrder = '';
                            $datacheck[0]->isRequired = '';
                            $datacheck[0]->id = '';
                        }
                    }
//                    echo "<pre>"; print_r($datacheck); echo "</pre>";
                        if(empty($datacheck) || ($value4->field_id == 'ros') || ($value4->field_id == 'physical_exam')){ 
                            $field_value = '';
//                            $sql5 = "SELECT l.field_id, l.seq as FormOrder,SUBSTRING(l.group_name ,-length(l.group_name),1) as grouporder,SUBSTRING(l.group_name FROM 2) as GroupName,
//                                     CASE l.uor
//                                    WHEN 0 THEN 'UnUsed' 
//                                    WHEN 1 THEN 'Optional'
//                                    WHEN 2 THEN 'Required'
//                                    END as isRequired,l.description
//                                from layout_options l WHERE l.title LIKE '$value3->screen_name' and l.form_id='LBF1' ";
                            $sql5 = "SELECT l.field_id, SUBSTRING(l.group_name ,-length(l.group_name),1) as grouporder,SUBSTRING(l.group_name FROM 2) as GroupName,
                                     l.description
                                from layout_options l WHERE l.title LIKE '$value3->screen_name' and l.form_id='LBF1' ";
                            $stmt5 = $db->prepare($sql5);
                            $stmt5->execute();  
                            $datacheck2 = $stmt5->fetchAll(PDO::FETCH_OBJ); 
                            
                            $get_isRequired = "SELECT id,screen_names FROM tbl_allcare_facuservisit WHERE screen_group LIKE '%".$datacheck2[0]->GroupName."' AND `users`  REGEXP ('".":\"".$value->provider_id."\"') ";
                            $db->query( "SET NAMES utf8");
                            $isReq_stmt = $db->prepare($get_isRequired) ;
                            $isReq_stmt->execute();    
                            $req_datacheck = $isReq_stmt->fetchAll(PDO::FETCH_OBJ); 
                            $s_array = array();
                            $dataArray = '';
                            if(!empty($req_datacheck)){
                                $s_data = $req_datacheck[0]->screen_names;
                                $s_array[] =  unserialize($s_data);
                                for($j = 0; $j<count($s_array); $j++){
                                    foreach($s_array[$j] as $arraykey){
                                        if(strpos($arraykey,$datacheck2[0]->field_id) !== false){
                                           $dataArray = $arraykey;
                                        }
                                    }
                                }
                                $fields = explode('$$', $dataArray);
                                if(!empty($fields)){
                                    $datacheck2[0]->FormOrder = (isset($fields[0]) ? $fields[0] : '');
                                    $datacheck2[0]->isRequired = (isset($fields[1]) ? $fields[1] : '');
                                }else{
                                    $datacheck2[0]->FormOrder = '';
                                    $datacheck2[0]->isRequired = '';
                                }
                                $datacheck2[0]->id = $req_datacheck[0]->id;
                            }else{
                                $datacheck2[0]->FormOrder = '';
                                $datacheck2[0]->isRequired = '';
                                $datacheck2[0]->id = '';
                            }
                            
                            //echo "<pre>"; print_r($datacheck2); echo "</pre>"; 
                            $sql6 = "SELECT form_id from forms WHERE encounter = $eid and deleted = 0 and formdir='LBF2'";
                            $stmt6 = $db->prepare($sql6);
                            $stmt6->execute();  
                            $datacheck3 = $stmt6->fetchAll(PDO::FETCH_OBJ);
                            if(!empty($datacheck3)):
                                $form_id = $datacheck3[0]->form_id;
                            else:
                                $form_id = 0;
                            endif;
                            if($datacheck2[0]->field_id == 'physical_exam'):
                                $sql7 = "SELECT form_id  from forms WHERE encounter = $eid and deleted = 0 and form_name = 'Allcare Physical Exam' and formdir='allcare_physical_exam'";
                                $stmt7 = $db->prepare($sql7);
                                $stmt7->execute();  
                                $datacheck4 = $stmt7->fetchAll(PDO::FETCH_OBJ);
                                if(!empty($datacheck4)):
                                    $form_id = $datacheck4[0]->form_id;
                                else:
                                    $form_id = 0;
                                endif;
                                if($form_id != 0){
                                    $get_status = "SELECT * FROM tbl_allcare_formflag WHERE form_name='Allcare Physical Exam' AND encounter_id=$eid  AND form_id = $form_id";
                                    $f_status = $db->prepare($get_status);
                                    $f_status->execute();  
                                    $set_status = $f_status->fetchAll(PDO::FETCH_OBJ);
                                    if(!empty($set_status)){
                                        if($set_status[0]->pending == 'Y')
                                            $field_value = 'pending';
                                        elseif($set_status[0]->finalized == 'Y')
                                            $field_value = 'finalized';
                                        else
                                            $field_value = '';
                                    }else{
                                        if(!empty($datacheck[0]->field_value))
                                            $field_value = $datacheck[0]->field_value;
                                        else
                                            $field_value = '';
                                    }
                                }else{
                                        $field_value = '';
                                }
                            endif;
                            if($datacheck2[0]->field_id == 'ros'):
                                $sql8 = "SELECT form_id  from forms WHERE encounter = $eid and deleted = 0 and form_name = 'Allcare Review Of Systems' and formdir='allcare_ros'";
                                $stmt8 = $db->prepare($sql8);
                                $stmt8->execute();  
                                $datacheck5 = $stmt8->fetchAll(PDO::FETCH_OBJ);
                                if(!empty($datacheck5)):
                                    $form_id = $datacheck5[0]->form_id;
                                else:
                                    $form_id = 0;
                                endif;
                                if($form_id != 0){
                                    $get_status = "SELECT * FROM tbl_allcare_formflag WHERE form_name='Allcare Review of Systems' AND encounter_id=$eid  AND form_id = $form_id";
                                    $f_status = $db->prepare($get_status);
                                    $f_status->execute();  
                                    $set_status = $f_status->fetchAll(PDO::FETCH_OBJ);
                                    if(!empty($set_status)){
                                        if($set_status[0]->pending == 'Y')
                                            $field_value = 'pending';
                                        elseif($set_status[0]->finalized == 'Y')
                                            $field_value = 'finalized';
                                        else
                                            $field_value = '';
                                    }else{
                                        if(!empty($datacheck[0]->field_value))
                                            $field_value = $datacheck[0]->field_value;
                                        else
                                            $field_value = '';
                                    }
                                }else{
                                        $field_value = '';
                                }
                            endif;
                            if($datacheck2[0]->field_id == 'vitals'):
                                $sql9 = "SELECT form_id  from forms WHERE encounter = $eid and deleted = 0 and form_name = 'Vitals' and formdir='vitals'";
                                $stmt9 = $db->prepare($sql9);
                                $stmt9->execute();  
                                $datacheck6 = $stmt9->fetchAll(PDO::FETCH_OBJ);
                                if(!empty($datacheck6)):
                                    $form_id = $datacheck6[0]->form_id;
                                else:
                                    $form_id = 0;
                                endif;    
                                if($form_id != 0){
                                    $vitals  = "SELECT field_value FROM lbf_data WHERE form_id = $form_id AND field_id = 'vitals_stat'";
                                    $stmtV = $db->prepare($vitals);
                                    $stmtV->execute();
                                    $set_status = $stmtV->fetchAll(PDO::FETCH_OBJ);
                                    
                                    if(!empty($set_status)){
                                        if($set_status[0]->field_value == 'pending')
                                            $field_value = 'pending';
                                        elseif($set_status[0]->field_value == 'finalized')
                                            $field_value = 'finalized';
                                        elseif($set_status[0]->field_value == 'finalized|pending' || $set_status[0]->field_value == 'pending|finalized')
                                            $datacheck[0]->field_value = 'finalized';
                                        else
                                            $field_value = '';
                                    }else{
                                        if(!empty($datacheck[0]->field_value))
                                            $field_value = $datacheck[0]->field_value;
                                        else
                                            $field_value = '';
                                    }
                                }else{
                                        $field_value = '';
                                }
                            endif;
                            if($datacheck2[0]->field_id == 'family_exam_test' || $datacheck2[0]->field_id == 'family_history'|| $datacheck2[0]->field_id == 'family_med_con' || $datacheck2[0]->field_id == 'history_past' || $datacheck2[0]->field_id == 'history_social'):
//                                $form_id = 0;
//                                $field_value = '';
                                $get_form_status = "SELECT form_id FROM forms where deleted = 0 and formdir = 'LBF2' and form_name = 'Allcare Encounter Forms' and encounter = $eid order by id asc limit 0,1";
                                $stmtf = $db->prepare($get_form_status);
                                $stmtf->execute();
                                $set_form_status = $stmtf->fetchAll(PDO::FETCH_OBJ);
                                if(!empty($set_form_status)):
                                    $form_id = $set_form_status[0]->form_id;
                                else:
                                    $form_id = 0;
                                endif;    
                                if($form_id != 0){
                                    $vitals  = "SELECT field_value FROM lbf_data WHERE form_id = $form_id AND field_id = '".$datacheck2[0]->field_id."_stat'";
                                    $stmtV = $db->prepare($vitals);
                                    $stmtV->execute();
                                    $set_status = $stmtV->fetchAll(PDO::FETCH_OBJ);
                                    
                                    if(!empty($set_status)){
                                        if($set_status[0]->field_value == 'pending')
                                            $field_value = 'pending';
                                        elseif($set_status[0]->field_value == 'finalized')
                                            $field_value = 'finalized';
                                        elseif($set_status[0]->field_value == 'finalized|pending' || $set_status[0]->field_value == 'pending|finalized')
                                            $datacheck[0]->field_value = 'finalized';
                                        else
                                            $field_value = '';
                                    }else{
                                        if(!empty($datacheck[0]->field_value)){
                                             if($datacheck[0]->field_value == 'pending')
                                                $field_value = 'pending';
                                            elseif($datacheck[0]->field_value == 'finalized')
                                                $field_value = 'finalized';
                                            elseif(trim($datacheck[0]->field_value) == 'finalized|pending' || trim($datacheck[0]->field_value) == 'pending|finalized')
                                                $datacheck[0]->field_value = 'finalized';
                                            else
                                                $field_value = '';
                                        }else
                                            $field_value = '';
                                    }
                                }else{
                                        $field_value = '';
                                }
                            endif;
                            if($datacheck2[0]->field_id  == 'allergies' || $datacheck2[0]->field_id  == 'dental_problems'  || $datacheck2[0]->field_id  == 'immunization' || $datacheck2[0]->field_id  == 'medical_problem'|| $datacheck2[0]->field_id  == 'medication' ):
                                $form_id = 0;
                                $field_value = '';
                            endif;
                            if($datacheck2[0]->field_id  == 'face2face' ){
                                $get_form_status = "SELECT form_id FROM forms where deleted = 0 and formdir = 'LBF2' and form_name = 'Allcare Encounter Forms' and encounter = $eid order by id asc limit 0,1";
                                $stmtf = $db->prepare($get_form_status);
                                $stmtf->execute();
                                $set_form_status = $stmtf->fetchAll(PDO::FETCH_OBJ);
                                if(!empty($set_form_status)):
                                    $form_id = $set_form_status[0]->form_id;
                                else:
                                    $form_id = 0;
                                endif;    
                                if($form_id != 0){
                                    $vitals  = "SELECT field_value FROM lbf_data WHERE form_id = $form_id AND field_id = 'f2f_stat'";
                                    $stmtV = $db->prepare($vitals);
                                    $stmtV->execute();
                                    $set_status = $stmtV->fetchAll(PDO::FETCH_OBJ);
                                    
                                    if(!empty($set_status)){
                                        if($set_status[0]->field_value == 'pending')
                                            $field_value = 'pending';
                                        elseif($set_status[0]->field_value == 'finalized')
                                            $field_value = 'finalized';
                                        elseif($set_status[0]->field_value == 'finalized|pending' || $set_status[0]->field_value == 'pending|finalized')
                                            $field_value = 'finalized';
                                        else
                                            $field_value = '';
                                    }else{
                                        if(!empty($datacheck2[0]->field_value)){
                                            if($datacheck2[0]->field_value == 'pending')
                                                $field_value = 'pending';
                                            elseif($datacheck2[0]->field_value == 'finalized')
                                                $field_value = 'finalized';
                                            elseif(trim($datacheck2[0]->field_value) == 'finalized|pending' || trim($datacheck2[0]->field_value) == 'pending|finalized')
                                                $field_value = 'finalized';
                                            else
                                                $field_value = '';
                                        }else
                                            $field_value = '';
                                    }
                                }else{
                                        $field_value = '';
                                }
                            }
                            if($datacheck2[0]->field_id  == 'codes'){
                                $selectquery = "SELECT b.billed
                                        FROM billing b 
                                        INNER JOIN form_encounter f ON b.encounter = f.encounter  
                                        WHERE b.encounter =   $eid and code_type='CPT4' and b.activity = 1 order by b.date desc ";
                                $stmtb = $db->prepare($selectquery);
                                $stmtb->execute();
                                $set_billing = $stmtb->fetchAll(PDO::FETCH_OBJ);
                                if(!empty($set_billing)){
                                    $form_id = 0;
                                    $billing = $set_billing[0]-> billed;
                                    if($billing == 0){
                                        $field_value = ' Not Billed';
                                    }else{
                                        $field_value = 'Billed';
                                    }
                                        
                                }else{
                                     $field_value = 'Not Billed';
                                }
                            }
                            $formvalues[] = (object)array('form_type' => $datacheck2[0]->GroupName,  'form_id'=> $form_id, 'form_name' =>$datacheck2[0]->field_id, 'FormOrder' =>$datacheck2[0]->FormOrder, 'grouporder' => $datacheck2[0]->grouporder, 'GroupName' => $value3->screen_name, 'isRequired' => $datacheck2[0]->isRequired , 'description' => $datacheck2[0]->description, 'field_value' => $field_value, 'id'=>$datacheck2[0]->id);

                        }else{ 
                            if($datacheck[0]->form_name == 'vitals'):
                                $sql9 = "SELECT form_id  from forms WHERE encounter = $eid and deleted = 0 and form_name = 'Vitals' and formdir='vitals'";
                                $stmt9 = $db->prepare($sql9);
                                $stmt9->execute();  
                                $datacheck6 = $stmt9->fetchAll(PDO::FETCH_OBJ);
                                if(!empty($datacheck6)):
                                    $datacheck[0]->form_id = $datacheck6[0]->form_id;
                                else:
                                    $datacheck[0]->form_id = 0;
                                endif;  
                                if($form_id != 0){
                                    $vitals  = "SELECT field_value FROM lbf_data WHERE form_id = $form_id AND field_id = 'vitals_stat'";
                                    $stmtV = $db->prepare($vitals);
                                    $stmtV->execute();
                                    $set_status = $stmtV->fetchAll(PDO::FETCH_OBJ);
                                    
                                    if(!empty($set_status)){
                                        if($set_status[0]->field_value == 'pending')
                                            $field_value = 'pending';
                                        elseif($set_status[0]->field_value == 'finalized')
                                            $field_value = 'finalized';
                                        elseif($set_status[0]->field_value == 'finalized|pending' || $set_status[0]->field_value == 'pending|finalized')
                                            $datacheck[0]->field_value = 'finalized';
                                        else
                                            $field_value = '';
                                    }else{
                                        if(!empty($datacheck[0]->field_value))
                                            $field_value = $datacheck[0]->field_value;
                                        else
                                            $field_value = '';
                                    }
                                }else{
                                        $field_value = '';
                                }
                            endif;
                            
                            if($datacheck[0]->form_name == 'family_exam_test' || $datacheck[0]->form_name == 'family_history'|| $datacheck[0]->form_name == 'family_med_con' || $datacheck[0]->form_name == 'history_past' || $datacheck[0]->form_name == 'history_social'):
                               // $datacheck[0]->form_id = 0;
                                $get_form_status = "SELECT form_id FROM forms where deleted = 0 and formdir = 'LBF2' and form_name = 'Allcare Encounter Forms' and encounter = $eid order by id asc limit 0,1";
                                $stmtf = $db->prepare($get_form_status);
                                $stmtf->execute();
                                $set_form_status = $stmtf->fetchAll(PDO::FETCH_OBJ);
                                if(!empty($set_form_status)):
                                    $form_id = $set_form_status[0]->form_id;
                                else:
                                    $form_id = 0;
                                endif;    
                                if($form_id != 0){
                                    $vitals  = "SELECT field_value FROM lbf_data WHERE form_id = $form_id AND field_id = '".$datacheck[0]->form_name."_stat'";
                                    $stmtV = $db->prepare($vitals);
                                    $stmtV->execute();
                                    $set_status = $stmtV->fetchAll(PDO::FETCH_OBJ);
                                    
                                    if(!empty($set_status)){
                                        if($set_status[0]->field_value == 'pending')
                                            $field_value = 'pending';
                                        elseif($set_status[0]->field_value == 'finalized')
                                            $field_value = 'finalized';
                                        elseif(trim($set_status[0]->field_value) == 'finalized|pending' || trim($set_status[0]->field_value) == 'pending|finalized'){
                                            $datacheck[0]->field_value = 'finalized';
                                        }
                                            
                                        else
                                            $field_value = '';
                                    }else{ 
                                        if(!empty($datacheck[0]->field_value)){
                                            if($datacheck[0]->field_value == 'pending')
                                                $field_value = 'pending';
                                            elseif($datacheck[0]->field_value == 'finalized')
                                                $field_value = 'finalized';
                                            elseif(trim($datacheck[0]->field_value) == 'finalized|pending' || trim($datacheck[0]->field_value) == 'pending|finalized')
                                                $datacheck[0]->field_value = 'finalized';
                                            else
                                            $field_value = '';
                                        }else
                                            $field_value = '';
                                    }
                                }else{
                                        $field_value = '';
                                }
                            endif;
                            if($datacheck[0]->form_name  == 'allergies' || $datacheck[0]->form_name  == 'dental_problems' || $datacheck[0]->form_name  == 'immunization' || $datacheck[0]->form_name  == 'medical_problem'|| $datacheck[0]->form_name  == 'medication'):
                               $datacheck[0]->form_id = 0;
                            endif;
                            if($datacheck[0]->form_name  == 'codes'){
                                $selectquery = "SELECT b.billed
                                        FROM billing b 
                                        INNER JOIN form_encounter f ON b.encounter = f.encounter  
                                        WHERE b.encounter =   $eid and code_type='CPT4' and b.activity = 1 order by b.date desc ";
                                $stmtb = $db->prepare($selectquery);
                                $stmtb->execute();
                                $set_billing = $stmtb->fetchAll(PDO::FETCH_OBJ);
                                if(!empty($set_billing)){
                                    $datacheck[0]->form_id = 0;
                                    $billing = $set_billing[0]-> billed;
                                    if($billing == 0){
                                        $datacheck[0]->field_value = 'Not billed';
                                    }else{
                                        $datacheck[0]->field_value = 'Billed';
                                    }
                                        
                                }else{
                                     $datacheck[0]->field_value = 'Not billed';
                                }
                            }
                           $formvalues[] = $datacheck[0];//$stmt4->fetchAll(PDO::FETCH_OBJ); 
                        }
                }
             } 
        }
        $itemArray = array();
        foreach($formvalues as $item) {
           $itemArray[] = (array)$item;
        }
        $sorted = array_orderby($itemArray, 'FormOrder', SORT_ASC);
        $arr = array();
        for($i=0; $i<count($sorted); $i++){
            $check = array_search('Unused', $sorted[$i],TRUE);
            if(empty($check)){
                $arr[] = $sorted[$i];
            }
        }
        //echo "<pre>"; print_r($arr);echo "</pre>";
        
             $new = encode_demo(array_filter( $arr));
             $newdemo['FormsData'] = check_data_available($new);
            if($newdemo) {
                $newdemores = json_encode($newdemo);
                echo $incompletelistresult = GibberishAES::enc($newdemores, $apikey);

            }else
            {
                $demo1='[{"id":"0"}]';
                $newdemo1=encode_demo($demo1);      
                $newdemo['FormsData'] = check_data_available($newdemo1);
                $newdemores = json_encode($newdemo);
                echo $incompletelistresult = GibberishAES::enc($newdemores, $apikey);
            }
    } 
    catch(PDOException $e) 
    {

        $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo $incompletelistresult = GibberishAES::enc($error, $apikey);
    }
  
}

// method to get patient demographics   
function sql_execute3($sqlStatement)
{
    $db = getConnection();
    $stmt = $db->prepare($sqlStatement) ;
    $stmt->execute();
    $demo = $stmt->fetchAll(PDO::FETCH_OBJ);
    return $demo;
}
function unique_obj($obj) {
	static $idList = array();
	if(in_array($obj->field_id,$idList)) {
		return false;
	}
	$idList []= $obj->field_id;
	return true;
}
function unique_group($obj) {
	static $idList = array();
	if(in_array($obj->group_name,$idList)) {
		return false;
	}
	$idList []= $obj->group_name;
	return true;
}

function getHPIFormData($eid,$formname2){
    try{
        $formname = str_replace('_', ' ', $formname2);
        // To get patientId
        $sql = "SELECT pid FROM form_encounter WHERE encounter = $eid";
        $db = getConnection();
        $stmt = $db->prepare($sql) ;
        $stmt->execute();
        $pidval = $stmt->fetchAll(PDO::FETCH_OBJ);
        $pid = $pidval[0]->pid;
        /* ======================== */
        $appdata = array();
        // For appointment Details
                $sql7 = "SELECT pc_hometext as comments,CONCAT(p.title,' ',p.fname, ' ', p.lname) as pname FROM openemr_postcalendar_events o
                        INNER JOIN form_encounter f on o.pc_eventDate = DATE_FORMAT(f.date,'%Y-%m-%d')
                        INNER JOIN patient_data p ON p.pid = f.pid 
                        WHERE f.encounter = $eid";
                $demo7=sql_execute($eid,$sql7);
                $appdata=encode_demo($demo7);  
                $appdata['Appointment_Comments'] = check_data_available($appdata);
                
                if($formname2 == 'chief_complaint'){
                
                $newdemo['Appointment_Comments'] = check_data_available($appdata);

                /* ========================= */
                // For Form CAMOS data
                $sql8 = "SELECT id,category FROM form_CAMOS_category WHERE category = '$formname'";
                $db = getConnection();
                $stmt8 = $db->prepare($sql8) ;
                $stmt8->execute();
                $categoryresult = $stmt8->fetchAll(PDO::FETCH_OBJ);
                $cat = array();
                foreach ($categoryresult as $value) {
                    $sql9 ="SELECT id,subcategory FROM form_CAMOS_subcategory WHERE category_id = ". $value->id . " ORDER BY id ASC";      
                    $stmt9 = $db->prepare($sql9); 
                    $stmt9->execute(); 
                    $subcategoryresult[] = $stmt9->fetchAll(PDO::FETCH_OBJ);
                    $i = 0;
                    foreach ($subcategoryresult[0] as $value2) {
                        $sql10 ="SELECT * FROM form_CAMOS_item WHERE subcategory_id = $value2->id";      
                        $stmt10 = $db->prepare($sql10); 
                        $stmt10->execute(); 
                        $itemresult = array();
                        $itemresult[] = $stmt10->fetchAll(PDO::FETCH_OBJ);
                        foreach ($itemresult as $value3){
                           foreach($value3 as $v3){
                                $fieldtype = "";
                                $subfieldtype = "";
                                if(substr($value2->subcategory,0,1) == "@"):
                                    $fieldtype = 'label';
                                elseif(substr($value2->subcategory,0,1) == "*"):
                                    $fieldtype = 'checkbox';
                                elseif(substr($value2->subcategory,0,1) == "$"):    
                                    $fieldtype = 'radio';
                                elseif(substr($value2->subcategory,0,1) == "&"):    
                                     $fieldtype = 'textbox';
                                endif;

                                if(substr($value2->subcategory,1,1) == "@"):
                                    $subfieldtype = 'label';
                                elseif(substr($value2->subcategory,1,1) == "*"):
                                    $subfieldtype = 'checkbox';
                                elseif(substr($value2->subcategory,1,1) == "$"):    
                                    $subfieldtype = 'radio';
                                elseif(substr($value2->subcategory,1,1) == "&"):    
                                     $subfieldtype = 'textbox';
                                endif;

                                $cat[$value->category][$i]['fieldtype'] = $fieldtype;
                                $cat[$value->category][$i]['fieldname'] = substr($value2->subcategory,2);
                                $cat[$value->category][$i]['subfieldtype'] = $subfieldtype;
                                if(substr($value2->subcategory,2) == "Patient"):
                                    $cat[$value->category][$i]['item'][$v3->item] = $pname;
                                elseif(substr($value2->subcategory,2) == "Complains of"):    
                                    $cat[$value->category][$i]['item'][$v3->item] = $comments;
                                else:    
                                    $cat[$value->category][$i]['item'][$v3->item] = $v3->content;
                                endif;


                            }
                        }
                        $i++;
                    } 
                } 

                $newdemo11 = encode_demo($cat);  
                echo "<pre>"; print_r($newdemo11); echo "</pre>";
                $newdemo['CAMOS Encounter Data'] = check_data_available($newdemo11);
            }elseif($formname2 == 'hpi'){
                $newdemo['Appointment_Comments'] = check_data_available($appdata);

                /* ========================= */
                // For Form CAMOS data
                $sql8 = "SELECT id,category FROM form_CAMOS_category WHERE category = '$formname2'";
                $db = getConnection();
                $stmt8 = $db->prepare($sql8) ;
                $stmt8->execute();
                $categoryresult = $stmt8->fetchAll(PDO::FETCH_OBJ);
                $cat = array();
                foreach ($categoryresult as $value) {
                    $sql9 ="SELECT id,subcategory FROM form_CAMOS_subcategory WHERE category_id = ". $value->id . " ORDER BY id ASC";      
                    $stmt9 = $db->prepare($sql9); 
                    $stmt9->execute(); 
                    $subcategoryresult[] = $stmt9->fetchAll(PDO::FETCH_OBJ);
                    $i = 0;
                    foreach ($subcategoryresult[0] as $value2) {
                        $sql10 ="SELECT * FROM form_CAMOS_item WHERE subcategory_id = $value2->id";      
                        $stmt10 = $db->prepare($sql10); 
                        $stmt10->execute(); 
                        $itemresult = array();
                        $itemresult[] = $stmt10->fetchAll(PDO::FETCH_OBJ);
                        foreach ($itemresult as $value3){
                           foreach($value3 as $v3){
                                $fieldtype = "";
                                $subfieldtype = "";
                                if(substr($value2->subcategory,0,1) == "@"):
                                    $fieldtype = 'label';
                                elseif(substr($value2->subcategory,0,1) == "*"):
                                    $fieldtype = 'checkbox';
                                elseif(substr($value2->subcategory,0,1) == "$"):    
                                    $fieldtype = 'radio';
                                elseif(substr($value2->subcategory,0,1) == "&"):    
                                     $fieldtype = 'textbox';
                                endif;

                                if(substr($value2->subcategory,1,1) == "@"):
                                    $subfieldtype = 'label';
                                elseif(substr($value2->subcategory,1,1) == "*"):
                                    $subfieldtype = 'checkbox';
                                elseif(substr($value2->subcategory,1,1) == "$"):    
                                    $subfieldtype = 'radio';
                                elseif(substr($value2->subcategory,1,1) == "&"):    
                                     $subfieldtype = 'textbox';
                                endif;

                                $cat[$value->category][$i]['fieldtype'] = $fieldtype;
                                $cat[$value->category][$i]['fieldname'] = substr($value2->subcategory,2);
                                $cat[$value->category][$i]['subfieldtype'] = $subfieldtype;
                                if(substr($value2->subcategory,2) == "Patient"):
                                    $cat[$value->category][$i]['item'][$v3->item] = $appdata['Appointment_Comments'][0]->pname;
                                elseif(substr($value2->subcategory,2) == "Complains of"):    
                                    $cat[$value->category][$i]['item'][$v3->item] = $appdata['Appointment_Comments'][0]->comments;
                                else:    
                                   
                                    $sql15 = "SELECT  '' AS isIssueActive, item,content FROM form_CAMOS_item i 
                                    WHERE item = :item
                                    UNION 
                                    SELECT
                                        CASE 
                                            WHEN l.enddate IS NULL THEN  'Active'
                                            ELSE  'Inactive'
                                        END AS isIssueActive, l.title,i.content FROM form_CAMOS_item i 
                                    INNER JOIN lists l ON i.item = l.title
                                    INNER JOIN issue_encounter ie ON ie.list_id = l.id
                                    WHERE l.type =  'medical_problem' AND ie.encounter = :eid AND ie.pid = :pid AND i.item = :item
                                    GROUP BY i.item";
                                    $stmt15 = $db->prepare($sql15); 
                                    $stmt15->bindParam("item", $v3->item);
                                    $stmt15->bindParam("eid", $eid);
                                    $stmt15->bindParam("pid", $pid);
                                    
                                    $stmt15->execute(); 
                                    $hpiissueresult = array();
                                    $hpiissueresult[] = $stmt15->fetchAll(PDO::FETCH_OBJ);
                                    
                                    foreach ($hpiissueresult[0] as $value4) {
                                         $cat[$value->category][$i]['item'][$value4->item]['isIssueActive'] = $value4->isIssueActive;
                                         $cat[$value->category][$i]['item'][$value4->item]['content'] = $value4->content;
                                    }
                                    
                                endif;


                            }
                        }
                        $i++;
                    } 
                } 

                $newdemo11 = encode_demo($cat);  
                echo "<pre>"; print_r($newdemo11); echo "</pre>";
                $newdemo['CAMOS Encounter Data'] = check_data_available($newdemo11);
        
            }
               /* ====================================== */ 

        
        
        if($formname == 'hpi')
            $formname = 'History of Present illness';
        // For Screen Group Data
        $sql2 = "SELECT field_id, CASE uor
                            WHEN 0 THEN 'UnUsed' 
                            WHEN 1 THEN 'Optional'
                            WHEN 2 THEN 'Required'
                            END as isFormRequired FROM layout_options WHERE form_id='LBF1' AND title = '$formname'";
        $stmt2 = $db->prepare($sql2) ;
        $stmt2->execute();
        
        $categoryrequired = $stmt2->fetchAll(PDO::FETCH_OBJ);
        $newdemo2 = encode_demo($categoryrequired);  
        
        $newdemo['Screen Group Form'] = check_data_available($newdemo2);
        /* ====================================== */

        // For Group Fields Data
//        echo $sql6 = "SELECT l.title,f.form_id,(SELECT field_value FROM lbf_data WHERE l.field_id = lbf_data.field_id AND l.form_id='LBF2') as value,  (SELECT GROUP_CONCAT(lo.option_id,'-',lo.title) FROM list_options lo WHERE lo.list_id = l.list_id) as options,
//                            CASE data_type
//                                WHEN 1   THEN 'List box'
//                                WHEN 2   THEN 'Textbox'
//                                WHEN 3   THEN 'Textarea'
//                                WHEN 4   THEN 'Text-date'
//                                WHEN 10  THEN 'Providers'
//                                WHEN 11  THEN 'Providers NPI'
//                                WHEN 12  THEN 'Pharmacies'
//                                WHEN 13  THEN 'Squads'
//                                WHEN 14  THEN 'Organizations'
//                                WHEN 15  THEN 'Billing codes'
//                                WHEN 21  THEN 'Checkbox list'
//                                WHEN 22  THEN 'Textbox list'
//                                WHEN 23  THEN 'Exam results'
//                                WHEN 24  THEN 'Patient allergies'
//                                WHEN 25  THEN 'Checkbox w/text'
//                                WHEN 26  THEN 'List box w/add'
//                                WHEN 27  THEN 'Radio buttons'
//                                WHEN 28  THEN 'Lifestyle status'
//                                WHEN 31  THEN 'Static Text'
//                                WHEN 32  THEN 'Smoking Status'
//                                WHEN 33  THEN 'Race and Ethnicity'
//                                WHEN 34  THEN 'NationNotes'
//                                WHEN 35  THEN 'Facilities'
//                                WHEN 36  THEN 'Date Of Service'
//                                WHEN 37  THEN 'Insurance Companies'
//                            END as data_type,
//                            CASE uor
//                                WHEN 0 THEN 'UnUsed' 
//                                WHEN 1 THEN 'Optional'
//                                WHEN 2 THEN 'Required'
//                            END as isFormRequired 
//            FROM layout_options l
//            INNER JOIN forms f ON f.formdir='LBF2' 
//            LEFT JOIN list_options lo ON l.list_id = lo.list_id
//         WHERE l.form_id='LBF2' AND l.group_name LIKE '%$formname%' AND f.encounter=$eid GROUP BY l.field_id";
        $sql6 = "SELECT l.field_id as field_id,f.form_id,(SELECT field_value FROM lbf_data WHERE l.field_id = lbf_data.field_id AND l.form_id='LBF2' and f.form_id = lbf_data.form_id) as value,  (SELECT GROUP_CONCAT(lo.option_id,'-',lo.title) FROM list_options lo WHERE lo.list_id = l.list_id) as options,
                        CASE data_type
                            WHEN 1   THEN 'List box'
                            WHEN 2   THEN 'Textbox'
                            WHEN 3   THEN 'Textarea'
                            WHEN 4   THEN 'Text-date'
                            WHEN 10  THEN 'Providers'
                            WHEN 11  THEN 'Providers NPI'
                            WHEN 12  THEN 'Pharmacies'
                            WHEN 13  THEN 'Squads'
                            WHEN 14  THEN 'Organizations'
                            WHEN 15  THEN 'Billing codes'
                            WHEN 21  THEN 'Checkbox list'
                            WHEN 22  THEN 'Textbox list'
                            WHEN 23  THEN 'Exam results'
                            WHEN 24  THEN 'Patient allergies'
                            WHEN 25  THEN 'Checkbox w/text'
                            WHEN 26  THEN 'List box w/add'
                            WHEN 27  THEN 'Radio buttons'
                            WHEN 28  THEN 'Lifestyle status'
                            WHEN 31  THEN 'Static Text'
                            WHEN 32  THEN 'Smoking Status'
                            WHEN 33  THEN 'Race and Ethnicity'
                            WHEN 34  THEN 'NationNotes'
                            WHEN 35  THEN 'Facilities'
                            WHEN 36  THEN 'Date Of Service'
                            WHEN 37  THEN 'Insurance Companies'
                        END as data_type,
                        CASE uor
                            WHEN 0 THEN 'UnUsed' 
                            WHEN 1 THEN 'Optional'
                            WHEN 2 THEN 'Required'
                        END as isFormRequired 
        FROM layout_options l
        INNER JOIN forms f ON f.formdir='LBF2' AND f.deleted=0 
        LEFT JOIN list_options lo ON l.list_id = lo.list_id
     WHERE l.form_id='LBF2' AND l.group_name LIKE '%$formname%' AND f.encounter=$eid ORDER BY f.form_id";
        $stmt6 = $db->prepare($sql6);
        $stmt6->execute();
        $datarequired = $stmt6->fetchAll(PDO::FETCH_OBJ);
        $datarequired = array_filter($datarequired,'unique_obj');
        foreach ($datarequired as $key => $object) {
            $object->field_id = str_replace($formname2,"dictation",$object->field_id);
        }
        $newdemo4 = encode_demo($datarequired);  
        $newdemo['Screen Group Form Fields'] = check_data_available($newdemo4);
        /* ================================ */

         if($newdemo)
        {
            //echo $patientres = json_encode($newdemo); 
            //echo $patientresult = GibberishAES::enc($patientres, $key);
        }
        else
        {
            //echo 'No patients available';
            echo '[{"id":"0"}]';
        }
    } 
    catch(PDOException $e) 
    {
        echo '{"error":{"text":'. $e->getMessage() .'}}'; 
    }
}
function getDictationFormData($eid,$formname2){
    try{
        $apikey = 'rotcoderaclla';
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        $formname = str_replace('_', ' ', $formname2);
        // To get patientId
        $sql = "SELECT pid FROM form_encounter WHERE encounter = $eid";
        $db = getConnection();
        $db->query( "SET NAMES utf8"); 
        $stmt = $db->prepare($sql) ;
        $stmt->execute();
        $pidval = $stmt->fetchAll(PDO::FETCH_OBJ);
        $pid = $pidval[0]->pid;
        /* ======================== */
        // For appointment Details
        $appdata = array();
        $sql7 = "SELECT pc_hometext as comments,CONCAT(p.title,' ',p.fname, ' ', p.lname) as pname FROM openemr_postcalendar_events o
                INNER JOIN form_encounter f  ON f.pid = o.pc_pid AND f.pc_catid = o.pc_catid AND o.pc_eventDate = DATE_FORMAT( f.date,  '%Y-%m-%d' )
                INNER JOIN patient_data p ON p.pid = f.pid 
                WHERE f.encounter = $eid";
        $demo7=sql_execute($eid,$sql7);
        $appdata=encode_demo($demo7);  
        $appdata['Appointment_Comments'] = check_data_available($appdata);
        if($appdata['DataAvailable'] == 'NO'):
            $query = "SELECT title, fname, lname FROM patient_data 
                WHERE pid = $pid";
            $db->query( "SET NAMES utf8"); 
            $stmtquery = $db->prepare($query) ;
            $stmtquery->execute();
            $pidname = $stmtquery->fetchAll(PDO::FETCH_OBJ);
        
            $pname = $pidname[0]->title ." ".$pidname[0]->fname." ".$pidname[0]->lname;
            $comments = '';
        else:
            $pname = $appdata['Appointment_Comments'][0]->pname;
            $comments = $appdata['Appointment_Comments'][0]->comments;
        endif;
        
        if($formname2 == 'chief_complaint'){
            
                // For Form CAMOS data
                $sql8 = "SELECT id,category FROM form_CAMOS_category WHERE category = '$formname'";
                $db = getConnection();
                $db->query( "SET NAMES utf8"); 
                $stmt8 = $db->prepare($sql8) ;
                $stmt8->execute();
                $categoryresult = $stmt8->fetchAll(PDO::FETCH_OBJ);
                $cat = array();
                foreach ($categoryresult as $value) {
                    $sql9 ="SELECT id,subcategory FROM form_CAMOS_subcategory WHERE category_id = ". $value->id . " ORDER BY id ASC";      
                    $db->query( "SET NAMES utf8"); 
                    $stmt9 = $db->prepare($sql9); 
                    $stmt9->execute(); 
                    $subcategoryresult[] = $stmt9->fetchAll(PDO::FETCH_OBJ);
                    $i = 0;
                    foreach ($subcategoryresult[0] as $value2) {
                        $sql10 ="SELECT * FROM form_CAMOS_item WHERE subcategory_id = $value2->id"; 
                        $db->query( "SET NAMES utf8"); 
                        $stmt10 = $db->prepare($sql10); 
                        $stmt10->execute(); 
                        $itemresult = array();
                        $itemresult[] = $stmt10->fetchAll(PDO::FETCH_OBJ);
                        foreach ($itemresult as $value3){
                           foreach($value3 as $v3){
                                $fieldtype = "";
                                $subfieldtype = "";
                                if(substr($value2->subcategory,0,1) == "@"):
                                    $fieldtype = 'label';
                                elseif(substr($value2->subcategory,0,1) == "*"):
                                    $fieldtype = 'checkbox';
                                elseif(substr($value2->subcategory,0,1) == "$"):    
                                    $fieldtype = 'radio';
                                elseif(substr($value2->subcategory,0,1) == "&"):    
                                     $fieldtype = 'textbox';
                                endif;

                                if(substr($value2->subcategory,1,1) == "@"):
                                    $subfieldtype = 'label';
                                elseif(substr($value2->subcategory,1,1) == "*"):
                                    $subfieldtype = 'checkbox';
                                elseif(substr($value2->subcategory,1,1) == "$"):    
                                    $subfieldtype = 'radio';
                                elseif(substr($value2->subcategory,1,1) == "&"):    
                                     $subfieldtype = 'textbox';
                                endif;

                                $cat[$value->category][$i]['fieldtype'] = $fieldtype;
                                $cat[$value->category][$i]['fieldname'] = substr($value2->subcategory,2);
                                $cat[$value->category][$i]['subfieldtype'] = $subfieldtype;
                                if(substr($value2->subcategory,2) == "Patient"):
                                    $cat[$value->category][$i]['item'][$v3->item] = $pname ;
                                elseif(substr($value2->subcategory,2) == "Complains of"):    
                                    $cat[$value->category][$i]['item'][$v3->item] = $comments;
                                else:    
                                    $cat[$value->category][$i]['item'][$v3->item] = $v3->content;
                                endif;


                            }
                        }
                        $i++;
                    } 
                } 

                $newdemo11 = encode_demo($cat);  
                $newdemo['CAMOS Encounter Data'] = check_data_available($newdemo11);
            }elseif($formname2 == 'hpi'){
                
                // For Form CAMOS data
                $sql8 = "SELECT id,category FROM form_CAMOS_category WHERE category = '$formname2'";
                $db = getConnection();
                $db->query( "SET NAMES utf8"); 
                $stmt8 = $db->prepare($sql8) ;
                $stmt8->execute();
                $categoryresult = $stmt8->fetchAll(PDO::FETCH_OBJ);
                $cat = array();
                foreach ($categoryresult as $value) {
                    $sql9 ="SELECT id,subcategory FROM form_CAMOS_subcategory WHERE category_id = ". $value->id . " ORDER BY id ASC";      
                    $db->query( "SET NAMES utf8"); 
                    $stmt9 = $db->prepare($sql9); 
                    $stmt9->execute(); 
                    $subcategoryresult[] = $stmt9->fetchAll(PDO::FETCH_OBJ);
                    $i = 0;
                    foreach ($subcategoryresult[0] as $value2) {
                        $sql10 ="SELECT * FROM form_CAMOS_item WHERE subcategory_id = $value2->id";      
                        $db->query( "SET NAMES utf8"); 
                        $stmt10 = $db->prepare($sql10); 
                        $stmt10->execute(); 
                        $itemresult = array();
                        $itemresult[] = $stmt10->fetchAll(PDO::FETCH_OBJ);
                        foreach ($itemresult as $value3){
                           foreach($value3 as $v3){
                                $fieldtype = "";
                                $subfieldtype = "";
                                if(substr($value2->subcategory,0,1) == "@"):
                                    $fieldtype = 'label';
                                elseif(substr($value2->subcategory,0,1) == "*"):
                                    $fieldtype = 'checkbox';
                                elseif(substr($value2->subcategory,0,1) == "$"):    
                                    $fieldtype = 'radio';
                                elseif(substr($value2->subcategory,0,1) == "&"):    
                                     $fieldtype = 'textbox';
                                endif;

                                if(substr($value2->subcategory,1,1) == "@"):
                                    $subfieldtype = 'label';
                                elseif(substr($value2->subcategory,1,1) == "*"):
                                    $subfieldtype = 'checkbox';
                                elseif(substr($value2->subcategory,1,1) == "$"):    
                                    $subfieldtype = 'radio';
                                elseif(substr($value2->subcategory,1,1) == "&"):    
                                     $subfieldtype = 'textbox';
                                endif;

                                $cat[$value->category][$i]['fieldtype'] = $fieldtype;
                                $cat[$value->category][$i]['fieldname'] = substr($value2->subcategory,2);
                                $cat[$value->category][$i]['subfieldtype'] = $subfieldtype;
                                if(substr($value2->subcategory,2) == "Patient"):
                                    $cat[$value->category][$i]['item'][$v3->item] =  ($pname? $pname : $appdata['Appointment_Comments'][0]->pname);
                                elseif(substr($value2->subcategory,2) == "Complains of"):    
                                    $cat[$value->category][$i]['item'][$v3->item] = $appdata['Appointment_Comments'][0]->comments;
                                else:    
                                   
                                    $sql15 = "SELECT  '' AS isIssueActive, item,content FROM form_CAMOS_item i 
                                    WHERE item = :item
                                    UNION 
                                    SELECT
                                        CASE 
                                            WHEN l.enddate IS NULL THEN  'Active'
                                            ELSE  'Inactive'
                                        END AS isIssueActive, l.title,i.content FROM form_CAMOS_item i 
                                    INNER JOIN lists l ON i.item = l.title
                                    INNER JOIN issue_encounter ie ON ie.list_id = l.id
                                    WHERE l.type =  'medical_problem' AND ie.encounter = :eid AND ie.pid = :pid AND i.item = :item
                                    GROUP BY i.item";
                                    $db->query( "SET NAMES utf8"); 
                                    $stmt15 = $db->prepare($sql15); 
                                    $stmt15->bindParam("item", $v3->item);
                                    $stmt15->bindParam("eid", $eid);
                                    $stmt15->bindParam("pid", $pid);
                                    
                                    $stmt15->execute(); 
                                    $hpiissueresult = array();
                                    $hpiissueresult[] = $stmt15->fetchAll(PDO::FETCH_OBJ);
                                    
                                    foreach ($hpiissueresult[0] as $value4) {
                                         $cat[$value->category][$i]['item'][$value4->item]['isIssueActive'] = $value4->isIssueActive;
                                         $cat[$value->category][$i]['item'][$value4->item]['content'] = $value4->content;
                                    }
                                    
                                endif;


                            }
                        }
                        $i++;
                    } 
                } 

                $newdemo11 = encode_demo($cat);  
                //echo "<pre>"; print_r($newdemo11); echo "</pre>";
                $newdemo['CAMOS Encounter Data'] = check_data_available($newdemo11);
        
            }
               /* ====================================== */ 

        
        
        if($formname == 'hpi'){
            $formname = 'History of Present illness';
        }
        // For Screen Group Data
        $sql2 = "SELECT field_id, CASE uor
                            WHEN 0 THEN 'UnUsed' 
                            WHEN 1 THEN 'Optional'
                            WHEN 2 THEN 'Required'
                            END as isFormRequired FROM layout_options WHERE form_id='LBF1' AND title = '$formname'";
        $db->query( "SET NAMES utf8"); 
        $stmt2 = $db->prepare($sql2) ;
        $stmt2->execute();
        $categoryrequired = $stmt2->fetchAll(PDO::FETCH_OBJ);
        $newdemo2 = encode_demo($categoryrequired);  
        //echo "<pre>"; print_r($newdemo2); echo "</pre>";
        $newdemo['Screen Group Form'] = check_data_available($newdemo2);
        /* ====================================== */

        // For Group Fields Data
//        echo $sql6 = "SELECT l.title,f.form_id,(SELECT field_value FROM lbf_data WHERE l.field_id = lbf_data.field_id AND l.form_id='LBF2') as value,  (SELECT GROUP_CONCAT(lo.option_id,'-',lo.title) FROM list_options lo WHERE lo.list_id = l.list_id) as options,
//                            CASE data_type
//                                WHEN 1   THEN 'List box'
//                                WHEN 2   THEN 'Textbox'
//                                WHEN 3   THEN 'Textarea'
//                                WHEN 4   THEN 'Text-date'
//                                WHEN 10  THEN 'Providers'
//                                WHEN 11  THEN 'Providers NPI'
//                                WHEN 12  THEN 'Pharmacies'
//                                WHEN 13  THEN 'Squads'
//                                WHEN 14  THEN 'Organizations'
//                                WHEN 15  THEN 'Billing codes'
//                                WHEN 21  THEN 'Checkbox list'
//                                WHEN 22  THEN 'Textbox list'
//                                WHEN 23  THEN 'Exam results'
//                                WHEN 24  THEN 'Patient allergies'
//                                WHEN 25  THEN 'Checkbox w/text'
//                                WHEN 26  THEN 'List box w/add'
//                                WHEN 27  THEN 'Radio buttons'
//                                WHEN 28  THEN 'Lifestyle status'
//                                WHEN 31  THEN 'Static Text'
//                                WHEN 32  THEN 'Smoking Status'
//                                WHEN 33  THEN 'Race and Ethnicity'
//                                WHEN 34  THEN 'NationNotes'
//                                WHEN 35  THEN 'Facilities'
//                                WHEN 36  THEN 'Date Of Service'
//                                WHEN 37  THEN 'Insurance Companies'
//                            END as data_type,
//                            CASE uor
//                                WHEN 0 THEN 'UnUsed' 
//                                WHEN 1 THEN 'Optional'
//                                WHEN 2 THEN 'Required'
//                            END as isFormRequired 
//            FROM layout_options l
//            INNER JOIN forms f ON f.formdir='LBF2' 
//            LEFT JOIN list_options lo ON l.list_id = lo.list_id
//         WHERE l.form_id='LBF2' AND l.group_name LIKE '%$formname%' AND f.encounter=$eid GROUP BY l.field_id";
        $sql6 = "SELECT l.field_id as field_id,f.form_id,(SELECT field_value FROM lbf_data WHERE l.field_id = lbf_data.field_id AND l.form_id='LBF2' and f.form_id = lbf_data.form_id) as value,  (SELECT GROUP_CONCAT(lo.option_id,'-',lo.title) FROM list_options lo WHERE lo.list_id = l.list_id) as options,
                        CASE data_type
                            WHEN 1   THEN 'List box'
                            WHEN 2   THEN 'Textbox'
                            WHEN 3   THEN 'Textarea'
                            WHEN 4   THEN 'Text-date'
                            WHEN 10  THEN 'Providers'
                            WHEN 11  THEN 'Providers NPI'
                            WHEN 12  THEN 'Pharmacies'
                            WHEN 13  THEN 'Squads'
                            WHEN 14  THEN 'Organizations'
                            WHEN 15  THEN 'Billing codes'
                            WHEN 21  THEN 'Checkbox list'
                            WHEN 22  THEN 'Textbox list'
                            WHEN 23  THEN 'Exam results'
                            WHEN 24  THEN 'Patient allergies'
                            WHEN 25  THEN 'Checkbox w/text'
                            WHEN 26  THEN 'List box w/add'
                            WHEN 27  THEN 'Radio buttons'
                            WHEN 28  THEN 'Lifestyle status'
                            WHEN 31  THEN 'Static Text'
                            WHEN 32  THEN 'Smoking Status'
                            WHEN 33  THEN 'Race and Ethnicity'
                            WHEN 34  THEN 'NationNotes'
                            WHEN 35  THEN 'Facilities'
                            WHEN 36  THEN 'Date Of Service'
                            WHEN 37  THEN 'Insurance Companies'
                        END as data_type,
                        CASE uor
                            WHEN 0 THEN 'UnUsed' 
                            WHEN 1 THEN 'Optional'
                            WHEN 2 THEN 'Required'
                        END as isFormRequired 
        FROM layout_options l
        INNER JOIN forms f ON f.formdir='LBF2' AND f.deleted=0 
        LEFT JOIN list_options lo ON l.list_id = lo.list_id
     WHERE l.form_id='LBF2' AND l.group_name LIKE '%$formname%' AND f.encounter=$eid ORDER BY f.form_id";
        $db->query( "SET NAMES utf8"); 
        $stmt6 = $db->prepare($sql6);
        $stmt6->execute();
        $datarequired = $stmt6->fetchAll(PDO::FETCH_OBJ);
        if(empty($datarequired)):
            $sql7 = "SELECT l.field_id as field_id,  (SELECT GROUP_CONCAT(lo.option_id,'-',lo.title) FROM list_options lo WHERE lo.list_id = l.list_id) as options,
                        CASE data_type
                            WHEN 1   THEN 'List box'
                            WHEN 2   THEN 'Textbox'
                            WHEN 3   THEN 'Textarea'
                            WHEN 4   THEN 'Text-date'
                            WHEN 10  THEN 'Providers'
                            WHEN 11  THEN 'Providers NPI'
                            WHEN 12  THEN 'Pharmacies'
                            WHEN 13  THEN 'Squads'
                            WHEN 14  THEN 'Organizations'
                            WHEN 15  THEN 'Billing codes'
                            WHEN 21  THEN 'Checkbox list'
                            WHEN 22  THEN 'Textbox list'
                            WHEN 23  THEN 'Exam results'
                            WHEN 24  THEN 'Patient allergies'
                            WHEN 25  THEN 'Checkbox w/text'
                            WHEN 26  THEN 'List box w/add'
                            WHEN 27  THEN 'Radio buttons'
                            WHEN 28  THEN 'Lifestyle status'
                            WHEN 31  THEN 'Static Text'
                            WHEN 32  THEN 'Smoking Status'
                            WHEN 33  THEN 'Race and Ethnicity'
                            WHEN 34  THEN 'NationNotes'
                            WHEN 35  THEN 'Facilities'
                            WHEN 36  THEN 'Date Of Service'
                            WHEN 37  THEN 'Insurance Companies'
                        END as data_type,
                        CASE uor
                            WHEN 0 THEN 'UnUsed' 
                            WHEN 1 THEN 'Optional'
                            WHEN 2 THEN 'Required'
                        END as isFormRequired 
                    FROM layout_options l
                    LEFT JOIN list_options lo ON l.list_id = lo.list_id
                    WHERE l.form_id='LBF2' AND l.group_name LIKE '%$formname%' ";
                $db->query( "SET NAMES utf8"); 
                $stmt7 = $db->prepare($sql7);
                $stmt7->execute();
                $datarequired21 = $stmt7->fetchAll(PDO::FETCH_OBJ);
                $sql8 = "SELECT form_id FROM forms WHERE formdir='LBF2' AND deleted = 0 AND encounter = $eid AND pid = $pid";
                $db->query( "SET NAMES utf8"); 
                $stmt8 = $db->prepare($sql8);
                $stmt8->execute();
                $datarequired3 = $stmt8->fetchAll(PDO::FETCH_OBJ);
                if(!empty($datarequired21)):
                 $datarequired2 = array_filter($datarequired21,'unique_obj');
                    foreach($datarequired2 as $key => $value){
                        $datarequired2[$key] =  $value;
                        $value->field_id = str_replace($formname2,"dictation",$value->field_id);
                    }    
                     foreach($datarequired2 as $key => $value){
                       if (array_key_exists('form_id', $datarequired2[$key])) {
                            
                        }  else{
                            if(empty($datarequired3)):
                                $datarequired2[$key]->form_id = 0; 
                            else:
                                $datarequired2[$key]->form_id = $datarequired3[0]->form_id;
                            endif;
                             $datarequired2[$key]->value = '';
                        }
                     }
                     $datarequired = $datarequired2;
                  endif;
        else:
            $datarequired = array_filter($datarequired,'unique_obj');
            foreach ($datarequired as $key => $object) {
                $object->field_id = str_replace($formname2,"dictation",$object->field_id);
                $issuedata = '';
                if($formname2 == 'assessment_note' || $formname2 == 'plan_note'):
                   if($object->field_id == 'dictation_text'):
                        $getissues = "SELECT diagnosis, title, begdate, enddate, outcome FROM lists l INNER JOIN issue_encounter i ON i.pid = l.pid AND i.list_id = l.id WHERE i.encounter = $eid AND type = 'medical_problem'"; 
                        $db->query( "SET NAMES utf8"); 
                        $stmt9 = $db->prepare($getissues);
                        $stmt9->execute();
                        $setissues = $stmt9->fetchAll(PDO::FETCH_OBJ);
                        for($i=0; $i<count($setissues); $i++){
                            if($setissues[$i]->outcome == 0):
                                if($setissues[$i]->begdate != '' && $setissues[$i]->enddate != '' ):
                                    $status = 'Inactive';
                                elseif($setissues[$i]->begdate != '' && $setissues[$i]->enddate == '' ):
                                     $status = 'Active';
                                endif;
                            else:
                                $statussql = "SELECT title FROM list_options WHERE list_id = 'outcome' AND option_id =" .$setissues[$i]->outcome ;
                                $db->query( "SET NAMES utf8"); 
                                $stmt10 = $db->prepare($statussql);
                                $stmt10->execute();
                                $setstatus = $stmt10->fetchAll(PDO::FETCH_OBJ);
                                $status = $setstatus[0]-> title;
                            endif;
                            $issuedata .= $setissues[$i]-> diagnosis.":".$setissues[$i]-> title."( $status ), " ;
                        }
                        $notes = $object->value ;
                        $issuedata2 = rtrim($issuedata, ', ');
                        $object->value = $issuedata2."; Notes: ".$notes;
                   endif;
                endif;
                
            }
        endif;
//        echo "<pre>"; print_r($datarequired); echo "</pre>";
        $newdemo4 = encode_demo($datarequired);  
        $newdemo['Screen Group Form Fields'] = check_data_available($newdemo4);
        /* ================================ */

         if($newdemo)
        {
            $patientres = json_encode($newdemo); 
            echo $patientresult = GibberishAES::enc($patientres, $apikey);
        }
        else
        {
            //echo 'No patients available';
            $patientres = '[{"id":"0"}]';
            echo $patientresult = GibberishAES::enc($patientres, $apikey);
        }
    } 
    catch(PDOException $e) 
    {
        $patientres = '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo $patientresult = GibberishAES::enc($patientres, $apikey);
    }
}

// to save layoutformdata
function saveLayoutFormsData(){
    try{
        $db = getConnection();
        $request = Slim::getInstance()->request();
        $key = 'rotcoderaclla';
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        $appres = GibberishAES::dec($request->getBody(), $key);
        //$appres = GibberishAES::dec('U2FsdGVkX18bRVzGlZFTNCBKyIdV/EEhRQnANw23qt94y1CKYTF3ZnzcbPXE4MxoxMz2rNk1h1K6yb1UH9vOEsNTrdMkMZsdX4EzqRCFsYE35luvDz4/NDtEKDon0SgcQ380JhlA23fIvYLVKtFS8zVItH9gtKeNxFYNn7n4McE5g7DNyX0VkeeNmjKbqAiHZxt0/TMR2RkzkYtdzdoHevYDHVOQrRbSfRoEhCEnzm/glCnmwXpl5eIMQM23NeOiScqn4tL/T0m2b/tIOXuQIR+aGZnB3euxcFXTts1JnWP9AK62gau1oEmNhaJjiRA4vpooKoJDruANZSeJbiFOFv3n+0Y8j0h7D5hUzC4F5qzKuinKnHVw0zTI8ijRworRzTV0gY0uhvMK9vMhv9Twr9BjAmmuAeZvNaIxXl3ToPjI0c9frhuPfJuZ4B5qIRBl', $key);
        $insertArray = json_decode($appres,TRUE);
	
        $encounter               =  $insertArray['encounter'];
        $form_name               =  $insertArray['form_name'];
        $form_id                 =  $insertArray['form_id'];
        $pid                     =  $insertArray['pid'];
        $user                    =  $insertArray['user'];
        $authorized              =  1; //$insertArray['authorized'];
        $field_id                =  $insertArray['field_id'];
        $array = $field_id[0];
        //print_r($data);
        $field_id_data = array();
        foreach ($field_id as $value) {
            foreach ($value as $key => $val) {
                $key1 =  str_replace("dictation", $form_name,$key);// = str_replace("dictation", $form_name,$value->dictation_text);
                $field_id_data[$key1] = $val;
            }    
        }           
        //echo "<pre>";print_r($field_id_data);echo "</pre>";

        if($form_id == 0):
            $lastformid = "SELECT MAX(form_id) as form_id FROM lbf_data";
            $stmt = $db->prepare($lastformid) ;
            $stmt->execute();
            $maxformidval = $stmt->fetchAll(PDO::FETCH_OBJ);
            $maxformid =  $maxformidval[0]->form_id + 1;
        
            $insertform = "INSERT INTO forms (date,encounter, form_name, form_id, pid, user, groupname, authorized, deleted, formdir) VALUES (NOW(), $encounter, 'Allcare Encounter Forms', $maxformid, $pid, $user, 'Default', $authorized, 0, 'LBF2' )";
            $stmt2 = $db->prepare($insertform) ;
            $stmt2->execute();
            $lastId = $db->lastInsertId();
            
            $getformid = "SELECT form_id FROM forms WHERE id = $lastId";
            $stmt3 = $db->prepare($getformid) ;
            $stmt3->execute();
            $formidval = $stmt3->fetchAll(PDO::FETCH_OBJ);
            $newformid =  $formidval[0]->form_id;
            
            foreach($field_id_data as $key => $value): 
                if (count($db->query("SELECT * FROM lbf_data WHERE form_id = $newformid AND field_id = '$key'")->fetchAll())==0) {
                    $sql = "INSERT into lbf_data (form_id, field_id, field_value) VALUES ($newformid,'$key','$value')";
                } else {
                   $sql = "UPDATE lbf_data SET field_value = '$value' WHERE field_id ='$key'  AND form_id = $newformid";
                }
                $stmt4 = $db->prepare($sql);
                $stmt4->execute();
            endforeach;
        else:
            foreach($field_id_data as $key => $value):
                if (count($db->query("SELECT * FROM lbf_data WHERE form_id = $form_id AND field_id = '$key'")->fetchAll())!=0) {
                     $sql = "UPDATE lbf_data SET field_value = '$value' WHERE field_id ='$key'  AND form_id = $form_id";
                } else {
                    $sql = "INSERT into lbf_data (form_id, field_id, field_value) VALUES ($form_id,'$key','$value')"; 
                }
                $stmt4 = $db->prepare($sql);
                $stmt4->execute();
            endforeach;
        endif;
        if($stmt4->execute())
        {
            $patientres = '[{"id":"1"}]';
            echo $patientresult = GibberishAES::enc($patientres, $key);
        }
        else
        {
            //echo 'No patients available';
            $patientres =  '[{"id":"0"}]';
            echo $patientresult = GibberishAES::enc($patientres, $key);
        }
    } 
    catch(PDOException $e) 
    {
        $patienterror = '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo $patientresult = GibberishAES::enc($patienterror, $key);
    }
}

//to get all patients from OpenEMR for drop down of mobile app
function getTotalPatients()
{
    
	$sql = "select pid,fname,lname from patient_data  WHERE practice_status = 'YES' AND deceased_stat != 'YES' order by lname,fname";      
        $count= "select count(*) from patient_data WHERE practice_status = 'YES' AND deceased_stat = 'YES' order by lname,fname";
        $key = 'rotcoderaclla';
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        try 
        {
            $db = getConnection();
            $stmt = $db->prepare($sql) ;
            $stmt->execute();                       
             
            $patients = $stmt->fetchAll(PDO::FETCH_OBJ);                        
            
            if($patients)
            {
                //returns patients 
                $patientres = json_encode($patients); 
                echo $patientresult = GibberishAES::enc($patientres, $key);
            }
            else
            {
                //echo 'No patients available';
                $patientres = '[{"id":"0"}]';
                echo $patientresult = GibberishAES::enc($patientres, $key);
            }
        } 
        catch(PDOException $e) 
        {
            
            $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
            echo $patientresult = GibberishAES::enc($error, $key);
        }
}
function saveVitalsForm(){
    try 
    {
        $db = getConnection();
        $request = Slim::getInstance()->request();
        $key = 'rotcoderaclla';
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        $appres = GibberishAES::dec($request->getBody(), $key);
//        $appres = GibberishAES::dec('U2FsdGVkX18kkuQJdYN/kqg3cMGNrL7fBgWoZ8Z2pFUvN6NxRHJ+oGZXGN8I2x/oAw6yXvcbejw/TLCPPX++FAAFyo8Gs7/m+CASqAjFgGy1Qh3tLYP3P2VB0roPS4MwhFcE73iZh35WlNrAWCf9qweNdhfrEPQi3fsarA0ShUPs/PmCbb6ebw4ZNJOHVN/uDu9S7zjV8TOdfymS1YwaPKB2AdMilEJTCjol4Z34aOaNO1sDQ3F0Yr3yMBZ+pTRHaqqyFfdgarMKD64uwYZ0ajlWTvf8cfHJvrTMp/a1Kfn+4aFDT8ht6EOibB29UIgsbBSl0jXTo95rucnlBP1OdCPd/z7zDbypCnMG1rlWZRBoIAac4SAoXJ7+rYGVuOX7y6bXWK+1wxecxhpgIGUk2ohirpspUzoh9XJiKMEmQ8okPRjUKUrbZw7JTm+2YJe8EIYWnmfMAxZAGvuvF2CT6IXqBe9/8D1NT7KeX57Su2/IEa5FBrKlkZ2xd4FWdE7t9QW5u4hbSam5uWTcYtXoX8fdwfNr8BZCSUkiRFMS2Sw=', $key);
        $insertArray = json_decode($appres,TRUE);
        print_r($insertArray);
        $formid                      = $insertArray['formid'];
        $encounter                  = $insertArray['encounter'];
        $pid                        = $insertArray['pid'];
        $user                       = $insertArray['loginProviderId'];
        $authorized                 = 1; 
        $activity                   = 1 ;
        $bp_systolic                = $insertArray['bps'];  
        $bp_diastolic               = $insertArray['bpd'];
        $weight_lbs                 = $insertArray['weight'];
        $height_inches              = $insertArray['height'];
//        $height_unit                = $insertArray['height_unit'];
        $temperature_fahrenheit     = $insertArray['temperature'];
//        $temperature_unit           = $insertArray['temperature_unit'];
        $temperature_location       = $insertArray['temp_method'];
        $pulse                      = $insertArray['pulse'];
        $respiration                = $insertArray['respiration'];
        $note                       = $insertArray['note'];
        $BMI                        = $insertArray['BMI'];
        $BMI_status                 = $insertArray['BMI_status'];
        $waist_circumference_inches = $insertArray['waist_circ'];   
//        $waist_circ_unit            = $insertArray['waist_circ_unit'];   
        $head_circumference_inches  = $insertArray['head_circ'];
//        $head_circ_unit             = $insertArray['head_circ_unit'];
        $oxygen_saturation          = $insertArray['oxygen_saturation'];
        $status                     = $insertArray['vitals_stat'];

        $patientres = '';
        if($formid == 0){
            
            $lastformid = "SELECT MAX(id) as form_id FROM form_vitals";
            $stmt = $db->prepare($lastformid) ;
            $stmt->execute();
            $maxformidval = $stmt->fetchAll(PDO::FETCH_OBJ);
            $maxformid =  $maxformidval[0]->form_id + 1;
        
            $insertform = "INSERT INTO forms (date,encounter, form_name, form_id, pid, user, groupname, authorized, deleted, formdir) VALUES (NOW(), '$encounter', 'Vitals', '$maxformid', '$pid', '$user', 'Default', '$authorized', 0, 'vitals' )";
            $stmt2 = $db->prepare($insertform);
            $stmt2->execute();
            $lastId = $db->lastInsertId();
            
            $getformid = "SELECT form_id FROM forms WHERE id = $lastId";
            $stmt3 = $db->prepare($getformid) ;
            $stmt3->execute();
            $formidval = $stmt3->fetchAll(PDO::FETCH_OBJ);
            $newformid =  $formidval[0]->form_id;
            
            $sql = "INSERT INTO `form_vitals` ( `id`, `date`, `pid`, `user`,  `authorized`, `activity`, `bps`, `bpd`, `weight`, `height`, `temperature`, `temp_method`, `pulse`, `respiration`, `note`, `BMI`, `BMI_status`, `waist_circ`, `head_circ`,`oxygen_saturation`) 
                    VALUES ( '$maxformid', NOW(), '$pid', '$user',  '$authorized', '$activity', '$bp_systolic', '$bp_diastolic', '$weight_lbs',  '$height_inches', '$temperature_fahrenheit','". $temperature_location."', '$pulse', '$respiration','". $note."', '$BMI', '".$BMI_status ."','$waist_circumference_inches','$head_circumference_inches',  '$oxygen_saturation')";
            $sqlstmt = $db->prepare($sql) ;
            $data =  $sqlstmt->execute();
            
        }else{
            $sql = "UPDATE form_vitals SET         
                    `bps` = '$bp_systolic', `bpd` = '$bp_diastolic',`weight` = '$weight_lbs',`height` = '$height_inches',
                    `temperature` = '$temperature_fahrenheit',`temp_method`= '".$temperature_location."' ,`pulse` = '$pulse',
                    `respiration` = '$respiration',`note` = '". $note."',`BMI`=  '$BMI' ,`BMI_status` ='". $BMI_status."' ,`waist_circ` = '$waist_circumference_inches',
                    `head_circ` = '$head_circumference_inches',`oxygen_saturation`  = '$oxygen_saturation' WHERE `id` = '$formid'";
            $sqlstmt = $db->prepare($sql) ;
            $data = $sqlstmt->execute();
            
        }
        $getformid1 = "SELECT form_id FROM forms WHERE formdir= 'LBF2' and deleted = 0 and encounter = '$encounter' and pid = '$pid' order by date asc";
        $stmt31 = $db->prepare($getformid1) ;
        $stmt31->execute();
        $formidval2 = $stmt31->fetchAll(PDO::FETCH_OBJ);

        if(!empty($formidval2)){
             $newformid2 =  $formidval2[0]->form_id;
        }else{
            $lastformid2 = "SELECT MAX(form_id) as forms FROM lbf_data";
            $stmt5 = $db->prepare($lastformid2) ;
            $stmt5->execute();
            $maxformidval2 = $stmt5->fetchAll(PDO::FETCH_OBJ);
            $maxformid2 =  $maxformidval2[0]->forms + 1;
            
            $insertform2 = "INSERT INTO forms (date,encounter, form_name, form_id, pid, user, groupname, authorized, deleted, formdir) VALUES (NOW(), $encounter, 'Allcare Encounter Forms', $maxformid2, $pid, $user, 'Default', $authorized, 0, 'LBF2' )";
            $stmt4 = $db->prepare($insertform2);
            $stmt4->execute();
            $newformid2 = $db->lastInsertId();
            
        }
        if (count($db->query("SELECT * FROM lbf_data WHERE form_id = $newformid2 AND field_id = 'vitals_stat'")->fetchAll())==0) {
            $sql5 = "INSERT into lbf_data (form_id, field_id, field_value) VALUES ($newformid2,'vitals_stat','$status')";
        } else {
           $sql5 = "UPDATE lbf_data SET field_value = '$status' WHERE field_id ='vitals_stat'  AND form_id = $newformid2";
        }
        $stmt41 = $db->prepare($sql5);
        $stmt41->execute();
           
        if($data){
            $patientres = '[{"id":"1"}]';
            echo $patientresult = GibberishAES::enc($patientres, $key);
        }
        else{
            $patientres =  '[{"id":"0"}]';
            echo $patientresult = GibberishAES::enc($patientres, $key);
        }
    } 
    catch(PDOException $e) 
    {

        $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo $patientresult = GibberishAES::enc($error, $key);
    }
}

// to get hyperlink forms
function getHyperlinkForms($eid){
    try 
        {
            $sql = "SELECT substring(group_name from 2) as form_type, title as form_name, seq as FormOrder,SUBSTRING(group_name ,-length(group_name),1) as grouporder,SUBSTRING(group_name FROM 2) as GroupName,
                        CASE uor
                        WHEN 0 THEN 'UnUsed' 
                        WHEN 1 THEN 'Optional'
                        WHEN 2 THEN 'Required'
                        END as isRequired, description FROM layout_options WHERE form_id = 'LBF1' AND group_name = '7Hyperlink'";
            $db = getConnection();
            $stmt = $db->prepare($sql) ;
            $stmt->execute();                       
             
            $dataresult = $stmt->fetchAll(PDO::FETCH_OBJ);    
            if(!empty($dataresult)):
                foreach ($dataresult as $value) {
                    $sql2 = "SELECT * FROM tbl_allcare_formflag WHERE encounter_id = $eid AND form_name = '$value->form_name' ";
                    $stmt2 = $db->prepare($sql2) ;
                    $stmt2->execute();                       
                    $dataresult2 = $stmt2->fetchAll(PDO::FETCH_OBJ);  
                    if(!empty($dataresult2)):
                        //$value[] = (object)array('form_id' => 0, 'field_value' => 'Completed/Submitted' );
                        $value->form_id = 0;
                        $value->field_value = 'Completed/Submitted';
                    else:
                        //$value[] = (object)array('form_id' => 0, 'field_value' => 'Pending' );
                        $value->form_id = 0;
                        $value->field_value = 'Pending';
                    endif;
                }
                
            endif;
            //echo "<pre>";            print_r($dataresult); echo "</pre>";
            $newdemo1 = encode_demo($dataresult);  
            $newdemo['Hyperlink Forms'] = check_data_available($newdemo1);
            if($newdemo)
            {
                //returns patients 
                echo $patientres = json_encode($newdemo); 
                //echo $patientresult = GibberishAES::enc($patientres, $key);
            }
            else
            {
                //echo 'No patients available';
                echo '[{"id":"0"}]';
            }
        } 
        catch(PDOException $e) 
        {
            
            echo '{"error":{"text":'. $e->getMessage() .'}}'; 
        }
}

function saveHyperlinkForm(){
    try{
        $db = getConnection();
        $request = Slim::getInstance()->request();
        $key = 'rotcoderaclla';
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        $appres = GibberishAES::dec($request->getBody(), $key);
        //$appres = GibberishAES::dec('U2FsdGVkX1+PDIp5vH5b8nW8sIhRYnajArp1nDRHkK5PMp+zwehlrXXg5pCvq4zEXAQXrf3SQpkzHWms6O/gEwdB2jWyhouONy8EWraJWor+IX5HMRi+SK4yaw48qhymEB0qnA3D1SbLvJMzuy71pk665CSLusHAAPX6yKpgm5mgZQOuy2cWs7DPNZ8kYDn8QBcQZRs1Qkf+n8julUBZK5BHGBrhksvZ8MPxe23cC1MFP8QfIr0t3WHcaxmEdsU9Q6ZLQFrQRkuAz7XMYGw4ePUdnbhMvaCYG9uXqpxkh1XpZJ0v8f49Ij3iYLcBbkX1nPS+30rnA344JdqpvXFEiCcuMAEqvL6nEsqiKyz++9kmw/Xjknt0k6FU0rnboEaWtmpplhH3VmRArYbtN2mFp15ONDKEitTKZWiRsX+SoUXCM0v38b5aJ0J0MBHV4gJmg+goHnagpGd5hDgra+lNh6fMa6bMsUo2pTsyG6gitF7/THin6YmRhzR+iszkrgyjfwa5HdY4ECLLD+KZVZf3VNw+iep97YQv6fTHaA7tb2c=', $key);
        $insertArray = json_decode($appres,TRUE);
        
        $formid                     = 0;
        $eid                        = $insertArray['encounter'];
        $form_name                  = $insertArray['form_name'];
        $user                       = $insertArray['loginProviderId'];
        $status                     = 'Submitted'; //$insertArray['status'];
        
        $ip_addr=GetIP();
        $logdata = serialize(array( 'authuser' =>$user,'Status' => $status,'date' => date("Y/m/d"), 'action'=>'created','ip_address'=>$ip_addr ));
        $sql = "INSERT into `tbl_allcare_formflag`(`form_id`, `encounter_id`, `form_name`, `logdate`) VALUES(0,".$eid.",'$form_name','".$logdata."')";      
        $sqlstmt = $db->prepare($sql) ;
        $data = $sqlstmt->execute();
        if($data){
            $patientres = '[{"id":"1"}]';
            echo $patientresult = GibberishAES::enc($patientres, $key);
        }
        else{
            $patientres =  '[{"id":"0"}]';
            echo $patientresult = GibberishAES::enc($patientres, $key);
        }
    } 
    catch(PDOException $e) 
    {
        $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo $patientresult = GibberishAES::enc($error, $key);
    }
}

function GetIP()
{
    if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown"))
        $ip = getenv("HTTP_CLIENT_IP");
    else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown"))
        $ip = getenv("HTTP_X_FORWARDED_FOR");
    else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown"))
        $ip = getenv("REMOTE_ADDR");
    else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown"))
        $ip = $_SERVER['REMOTE_ADDR'];
    else
        $ip = "unknown";
    return($ip);
}
function getROSFormData($formid){
 try 
        {
            $key1 = 'rotcoderaclla';
            // The default key size is 256 bits.
            $old_key_size = GibberishAES::size();
            GibberishAES::size(256);
            $db = getConnection();
            $dataresultset = array();
            $dataresult = array();
            if($formid == 0):
                $sql = "select COLUMN_NAME from information_schema.columns where table_name='tbl_form_allcare_ros'";
                $dataarray = array();
                $stmt = $db->prepare($sql) ;
                $stmt->execute();                       

                $dataresultset = $stmt->fetchAll(PDO::FETCH_OBJ);
                foreach($dataresultset as $key => $value){
                    $dataresult[0][$value->COLUMN_NAME] = 0;
                }
            else:
                $sql = "SELECT * FROM tbl_form_allcare_ros WHERE id = $formid";
                $stmt = $db->prepare($sql) ;
                $stmt->execute();                       

                $dataresult = $stmt->fetchAll(PDO::FETCH_OBJ);  
            endif;
            
            $data = array();
            $data2 = array();
            $countarray = 0;
            foreach ($dataresult[0] as $key=>$value) {
                if($key == 'constitutional' || $key == 'eyes' || $key == 'ent' || $key == 'breast' || $key == 'respiratory' || $key == 'cardiovascular' || $key == 'gastrointestinal' || $key == 'genitourinary' || $key == 'genitourinarymale' || $key == 'genitourinaryfemale' || $key == 'musculoskeletal' || $key == 'extremities' || $key == 'neurologic' || $key == 'skin' || $key == 'psychiatric' || $key == 'endocrine' || $key == 'hai' || $key == 'neck'){
                    $countarray = $countarray + 1 ;
                }
            }
            foreach ($dataresult[0] as $key=>$value) {
                if($key == 'constitutional' || $key == 'eyes' || $key == 'ent' || $key == 'breast' || $key == 'respiratory' || $key == 'cardiovascular' || $key == 'gastrointestinal' || $key == 'genitourinary' || $key == 'genitourinarymale' || $key == 'genitourinaryfemale' || $key == 'musculoskeletal' || $key == 'extremities' || $key == 'neurologic' || $key == 'skin' || $key == 'psychiatric' || $key == 'endocrine' || $key == 'hai' || $key == 'neck'){
                    $data['data']['header'] = $key;
                    $data['data']['headerTitle'] = ucwords(str_replace("_", " ", $key));
                    $data['data']['controlType'] = 'radiobutton';
                    if($formid == 0):
                        $data['data']['selectedvalue'] = 'Select_Details';
                    else:
                        $data['data']['selectedvalue'] = str_replace(" ", "_", $value);
                    endif;
                    $data['data']['valuelist'] = array('Normal','Not_Examined', 'Select_Details' );
                    $dataarray[] = $data; 
                }
            }
            foreach ($dataresult[0] as $key=>$value) {
                // constitutional
                if($key == 'weight_change' || $key == 'weakness' || $key == 'fatigue' || $key == 'anorexia' || $key == 'fever' || $key == 'chills' || $key == 'night_sweats' || $key == 'insomnia' || $key == 'irritability' || $key == 'heat_or_cold' || $key == 'intolerance' || $key == 'change_in_appetite'){
                    $values['name'] = $key;
                    $values['title'] = ucwords(str_replace("_", " ", $key));
                    $values['controlType'] = 'radiobutton';
                    if($formid == 0):
                        $values['selectedvalue'] = 'N/A';
                    else:
                        if(!empty($value)):
                            $values['selectedvalue'] = str_replace(" ", "_", $value);
                        else:
                            $values['selectedvalue'] = 'N/A';
                        endif;
                        
                    endif;
                    $values['headervaluelist'] = array('N/A', 'YES', 'NO');
                    for($i=0; $i<$countarray; $i++){
                        if( $dataarray[$i]['data']['header'] == 'constitutional'){
                            $dataarray[$i]['data']['values'][] = $values;

                        }
                    }
                }
                // eyes
                if($key == 'change_in_vision' || $key == 'glaucoma_history' || $key == 'eye_pain' || $key == 'irritation' || $key == 'redness' || $key == 'excessive_tearing' || $key == 'double_vision' || $key == 'blind_spots' || $key == 'photophobia' || $key == 'glaucoma' || $key == 'cataract' || $key == 'injury' || $key == 'ha' || $key =='coryza' || $key == 'obstruction'){
                    $values1['name'] = $key;
                    $values1['title'] = ucwords(str_replace("_", " ", $key));
                    $values1['controlType'] = 'radiobutton';
                    if($formid == 0):
                        $values1['selectedvalue'] = 'N/A';
                    else:
                        if(!empty($value)):
                            $values1['selectedvalue'] = str_replace(" ", "_", $value);
                        else:
                            $values1['selectedvalue'] = 'N/A';
                        endif;
                    endif;
                    $values1['headervaluelist'] = array('N/A', 'YES', 'NO');
                    for($i=0; $i<$countarray; $i++){ 
                        if( $dataarray[$i]['data']['header'] == 'eyes'){
                            $dataarray[$i]['data']['values'][] = $values1;

                        }
                    }
                }
                // ent
                if($key == 'hearing_loss' || $key == 'discharge' || $key == 'pain' || $key == 'vertigo' || $key == 'tinnitus' || $key == 'frequent_colds' || $key == 'sore_throat' || $key == 'sinus_problems' || $key == 'post_nasal_drip' || $key == 'nosebleed' || $key == 'snoring' || $key == 'apnea' || $key == 'bleeding_gums' || $key =='hoarseness' || $key == 'dental_difficulties' || $key == 'use_of_dentures' || $key == 'bleeding'){
                    $values2['name'] = $key;
                    $values2['title'] = ucwords(str_replace("_", " ", $key));
                    $values2['controlType'] = 'radiobutton';
                    if($formid == 0):
                        $values2['selectedvalue'] = 'N/A';
                    else:
                        if(!empty($value)):
                            $values2['selectedvalue'] = str_replace(" ", "_", $value);
                        else:
                            $values2['selectedvalue'] = 'N/A';
                        endif;
                    endif;
                    $values2['headervaluelist'] = array('N/A', 'YES', 'NO');
                    for($i=0; $i<$countarray; $i++){
                        if( $dataarray[$i]['data']['header'] == 'ent'){
                            $dataarray[$i]['data']['values'][] = $values2;

                        }
                    }
                }
                 // breast
                if($key == 'breast_mass' || $key == 'breast_discharge' || $key == 'biopsy' || $key == 'abnormal_mammogram'){
                    $values2['name'] = $key;
                    $values2['title'] = ucwords(str_replace("_", " ", $key));
                    $values2['controlType'] = 'radiobutton';
                    if($formid == 0):
                        $values2['selectedvalue'] = 'N/A';
                    else:
                        if(!empty($value)):
                            $values2['selectedvalue'] = str_replace(" ", "_", $value);
                        else:
                            $values2['selectedvalue'] = 'N/A';
                        endif;
                    endif;
                    $values2['headervaluelist'] = array('N/A', 'YES', 'NO');
                    for($i=0; $i<$countarray; $i++){
                        if( $dataarray[$i]['data']['header'] == 'breast'){
                            $dataarray[$i]['data']['values'][] = $values2;

                        }
                    }
                }
                // respiratory
                if($key == 'cough' || $key == 'sputum' || $key == 'shortness_of_breath' || $key == 'wheezing' || $key == 'hemoptsyis' || $key == 'asthma' || $key == 'copd' ){
                    $values2['name'] = $key;
                    $values2['title'] = ucwords(str_replace("_", " ", $key));
                    $values2['controlType'] = 'radiobutton';
                    if($formid == 0):
                        $values2['selectedvalue'] = 'N/A';
                    else:
                        if(!empty($value)):
                            $values2['selectedvalue'] = str_replace(" ", "_", $value);
                        else:
                            $values2['selectedvalue'] = 'N/A';
                        endif;
                    endif;
                    $values2['headervaluelist'] = array('N/A', 'YES', 'NO');
                    for($i=0; $i<$countarray; $i++){
                        if( $dataarray[$i]['data']['header'] == 'respiratory'){
                            $dataarray[$i]['data']['values'][] = $values2;

                        }
                    }
                }
                // cardiovascular
                if($key == 'chest_pain' || $key == 'palpitation' || $key == 'syncope' || $key == 'pnd' || $key == 'doe' || $key == 'orthopnea' || $key == 'peripheal' || $key == 'edema' || $key == 'legpain_cramping' || $key == 'history_murmur' || $key == 'arrythmia' || $key == 'heart_problem'){
                    $values2['name'] = $key;
                    $values2['title'] = ucwords(str_replace("_", " ", $key));
                    $values2['controlType'] = 'radiobutton';
                    if($formid == 0):
                        $values2['selectedvalue'] = 'N/A';
                    else:
                        if(!empty($value)):
                            $values2['selectedvalue'] = str_replace(" ", "_", $value);
                        else:
                            $values2['selectedvalue'] = 'N/A';
                        endif;
                    endif;
                    $values2['headervaluelist'] = array('N/A', 'YES', 'NO');
                    for($i=0; $i<$countarray; $i++){
                        if( $dataarray[$i]['data']['header'] == 'cardiovascular'){
                            $dataarray[$i]['data']['values'][] = $values2;

                        }
                    }
                }
                // gastrointestinal
                if($key == 'dysphagia' || $key == 'heartburn' || $key == 'bloating' || $key == 'belching' || $key == 'flatulence' || $key == 'nausea' || $key == 'vomiting' || $key == 'hematemesis' || $key == 'gastro_pain' || $key == 'food_intolerance' || $key == 'hepatitis' || $key == 'jaundice' || $key == 'hematochezia' || $key =='changed_bowel' || $key == 'diarrhea' || $key == 'constipation' || $key == 'blood_in_stool'){
                    $values2['name'] = $key;
                    $values2['title'] = ucwords(str_replace("_", " ", $key));
                    $values2['controlType'] = 'radiobutton';
                    if($formid == 0):
                        $values2['selectedvalue'] = 'N/A';
                    else:
                        if(!empty($value)):
                            $values2['selectedvalue'] = str_replace(" ", "_", $value);
                        else:
                            $values2['selectedvalue'] = 'N/A';
                        endif;
                    endif;
                    $values2['headervaluelist'] = array('N/A', 'YES', 'NO');
                    for($i=0; $i<$countarray; $i++){
                        if( $dataarray[$i]['data']['header'] == 'gastrointestinal'){
                            $dataarray[$i]['data']['values'][] = $values2;

                        }
                    }
                }
                // genitourinary
                if($key == 'polyuria' || $key == 'polydypsia' || $key == 'dysuria' || $key == 'hematuria' || $key == 'frequency' || $key == 'urgency' || $key == 'incontinence' || $key == 'renal_stones' || $key == 'utis' || $key == 'blood_in_urine' || $key == 'urinary_retention' || $key == 'change_in_nature_of_urine' ){
                    $values2['name'] = $key;
                    $values2['title'] = ucwords(str_replace("_", " ", $key));
                    $values2['controlType'] = 'radiobutton';
                    if($formid == 0):
                        $values2['selectedvalue'] = 'N/A';
                    else:
                        if(!empty($value)):
                            $values2['selectedvalue'] = str_replace(" ", "_", $value);
                        else:
                            $values2['selectedvalue'] = 'N/A';
                        endif;
                    endif;
                    $values2['headervaluelist'] = array('N/A', 'YES', 'NO');
                    for($i=0; $i<$countarray; $i++){
                        if( $dataarray[$i]['data']['header'] == 'genitourinary'){
                            $dataarray[$i]['data']['values'][] = $values2;

                        }
                    }
                }
                // genitourinarymale
                if($key == 'hesitancy' || $key == 'dribbling' || $key == 'stream' || $key == 'nocturia' || $key == 'erections' || $key == 'ejaculations' ){
                    $values2['name'] = $key;
                    $values2['title'] = ucwords(str_replace("_", " ", $key));
                    $values2['controlType'] = 'radiobutton';
                    if($formid == 0):
                        $values2['selectedvalue'] = 'N/A';
                    else:
                        if(!empty($value)):
                            $values2['selectedvalue'] = str_replace(" ", "_", $value);
                        else:
                            $values2['selectedvalue'] = 'N/A';
                        endif;
                    endif;
                    $values2['headervaluelist'] = array('N/A', 'YES', 'NO');
                    for($i=0; $i<$countarray; $i++){
                        if( $dataarray[$i]['data']['header'] == 'genitourinarymale'){
                            $dataarray[$i]['data']['values'][] = $values2;

                        }
                    }
                }
                // genitourinaryfemale
                if($key == 'g' || $key == 'p' || $key == 'ap' || $key == 'lc' || $key == 'mearche' || $key == 'menopause' || $key == 'lmp' || $key == 'f_frequency' || $key == 'f_flow' || $key == 'f_symptoms' || $key == 'abnormal_hair_growth' || $key == 'f_hirsutism' ){
                    $values2['name'] = $key;
                    $values2['title'] = ucwords(str_replace("_", " ", $key));
                    $values2['controlType'] = 'radiobutton';
                    if($formid == 0):
                        $values2['selectedvalue'] = 'N/A';
                    else:
                        if(!empty($value)):
                            $values2['selectedvalue'] = str_replace(" ", "_", $value);
                        else:
                            $values2['selectedvalue'] = 'N/A';
                        endif;
                    endif;
                    $values2['headervaluelist'] = array('N/A', 'YES', 'NO');
                    for($i=0; $i<$countarray; $i++){
                        if( $dataarray[$i]['data']['header'] == 'genitourinaryfemale'){
                            $dataarray[$i]['data']['values'][] = $values2;

                        }
                    }
                }
                // musculoskeletal
                if($key == 'joint_pain' || $key == 'swelling' || $key == 'm_redness' || $key == 'm_warm' || $key == 'm_stiffness' || $key == 'm_aches' || $key == 'fms' || $key == 'arthritis' || $key == 'gout' || $key == 'back_pain' || $key == 'paresthesia' || $key == 'muscle_pain' || $key =='limitation_in_range_of_motion' ){
                    $values2['name'] = $key;
                    $values2['title'] = ucwords(str_replace("_", " ", $key));
                    $values2['controlType'] = 'radiobutton';
                    if($formid == 0):
                        $values2['selectedvalue'] = 'N/A';
                    else:
                        if(!empty($value)):
                            $values2['selectedvalue'] = str_replace(" ", "_", $value);
                        else:
                            $values2['selectedvalue'] = 'N/A';
                        endif;
                    endif;
                    $values2['headervaluelist'] = array('N/A', 'YES', 'NO');
                    for($i=0; $i<$countarray; $i++){
                        if( $dataarray[$i]['data']['header'] == 'musculoskeletal'){
                            $dataarray[$i]['data']['values'][] = $values2;

                        }
                    }
                }
                // extremities
                if($key == 'spasms' || $key == 'extreme_tremors'){
                    $values2['name'] = $key;
                    $values2['title'] = ucwords(str_replace("_", " ", $key));
                    $values2['controlType'] = 'radiobutton';
                    if($formid == 0):
                        $values2['selectedvalue'] = 'N/A';
                    else:
                        if(!empty($value)):
                            $values2['selectedvalue'] = str_replace(" ", "_", $value);
                        else:
                            $values2['selectedvalue'] = 'N/A';
                        endif;
                    endif;
                    $values2['headervaluelist'] = array('N/A', 'YES', 'NO');
                    for($i=0; $i<$countarray; $i++){
                        if( $dataarray[$i]['data']['header'] == 'extremities'){
                            $dataarray[$i]['data']['values'][] = $values2;

                        }
                    }
                }
                // neurologic
                if($key == 'loc' || $key == 'seizures' ||  $key == 'stroke'  || $key == 'tia' || $key == 'n_numbness' || $key == 'n_weakness' || $key == 'paralysis' || $key == 'intellectual_decline' || $key == 'memory_problems' || $key == 'dementia' || $key == 'n_headache' || $key == 'dizziness_vertigo' || $key == 'slurred_speech' || $key =='tremors' || $key == 'migraines' || $key == 'changes_in_mentation' || $key == 'tingling' ){
                    $values2['name'] = $key;
                    $values2['title'] = ucwords(str_replace("_", " ", $key));
                    $values2['controlType'] = 'radiobutton';
                    if($formid == 0):
                        $values2['selectedvalue'] = 'N/A';
                    else:
                        if(!empty($value)):
                            $values2['selectedvalue'] = str_replace(" ", "_", $value);
                        else:
                            $values2['selectedvalue'] = 'N/A';
                        endif;
                    endif;
                    $values2['headervaluelist'] = array('N/A', 'YES', 'NO');
                    for($i=0; $i<$countarray; $i++){
                        if( $dataarray[$i]['data']['header'] == 'neurologic'){
                            $dataarray[$i]['data']['values'][] = $values2;

                        }
                    }
                }
                // skin
                if($key == 's_cancer' || $key == 'psoriasis' || $key == 's_acne' || $key == 's_other' || $key == 's_disease' || $key == 'rashes' || $key == 'dryness' || $key == 'itching' || $key == 'lesions' || $key == 'sores'){
                    $values2['name'] = $key;
                    $values2['title'] = ucwords(str_replace("_", " ", $key));
                    $values2['controlType'] = 'radiobutton';
                    if($formid == 0):
                        $values2['selectedvalue'] = 'N/A';
                    else:
                        if(!empty($value)):
                            $values2['selectedvalue'] = str_replace(" ", "_", $value);
                        else:
                            $values2['selectedvalue'] = 'N/A';
                        endif;
                    endif;
                    $values2['headervaluelist'] = array('N/A', 'YES', 'NO');
                    for($i=0; $i<$countarray; $i++){
                        if( $dataarray[$i]['data']['header'] == 'skin'){
                            $dataarray[$i]['data']['values'][] = $values2;

                        }
                    }
                }
                // psychiatric
                if($key == 'p_diagnosis' || $key == 'p_medication' || $key == 'depression' || $key == 'anxiety' || $key == 'social_difficulties' || $key == 'alcohol_drug_dependence' || $key == 'suicide_thoughts' || $key == 'use_of_antideprassants' || $key == 'thought_content' || $key == 'changes_in_sleep_habits'){
                    $values2['name'] = $key;
                    $values2['title'] = ucwords(str_replace("_", " ", $key));
                    $values2['controlType'] = 'radiobutton';
                    if($formid == 0):
                        $values2['selectedvalue'] = 'N/A';
                    else:
                        if(!empty($value)):
                            $values2['selectedvalue'] = str_replace(" ", "_", $value);
                        else:
                            $values2['selectedvalue'] = 'N/A';
                        endif;
                    endif;
                    $values2['headervaluelist'] = array('N/A', 'YES', 'NO');
                    for($i=0; $i<$countarray; $i++){
                        if( $dataarray[$i]['data']['header'] == 'psychiatric'){
                            $dataarray[$i]['data']['values'][] = $values2;

                        }
                    }
                }
                // endocrine
                if($key == 'thyroid_problems' || $key == 'diabetes' || $key == 'abnormal_blood' || $key == 'goiter' || $key == 'heat_intolerence' || $key == 'cold_intolerence' || $key == 'increased_thirst' || $key == 'excessive_sweating' || $key == 'excessive_hunger' || $key == 'polyphagia'){
                    $values2['name'] = $key;
                    $values2['title'] = ucwords(str_replace("_", " ", $key));
                    $values2['controlType'] = 'radiobutton';
                    if($formid == 0):
                        $values2['selectedvalue'] = 'N/A';
                    else:
                        if(!empty($value)):
                            $values2['selectedvalue'] = str_replace(" ", "_", $value);
                        else:
                            $values2['selectedvalue'] = 'N/A';
                        endif;
                    endif;
                    $values2['headervaluelist'] = array('N/A', 'YES', 'NO');
                    for($i=0; $i<$countarray; $i++){
                        if( $dataarray[$i]['data']['header'] == 'endocrine'){
                            $dataarray[$i]['data']['values'][] = $values2;

                        }
                    }
                }
                // hai
                if($key == 'anemia' || $key == 'fh_blood_problems' || $key == 'bleeding_problems' || $key == 'allergies' || $key == 'frequent_illness' || $key == 'hiv' || $key == 'hai_status' || $key == 'hay_fever' || $key == 'positive_ppd' ){
                    $values2['name'] = $key;
                    $values2['title'] = ucwords(str_replace("_", " ", $key));
                    $values2['controlType'] = 'radiobutton';
                    if($formid == 0):
                        $values2['selectedvalue'] = 'N/A';
                    else:
                        if(!empty($value)):
                            $values2['selectedvalue'] = str_replace(" ", "_", $value);
                        else:
                            $values2['selectedvalue'] = 'N/A';
                        endif;
                    endif;
                    $values2['headervaluelist'] = array('N/A', 'YES', 'NO');
                    for($i=0; $i<$countarray; $i++){
                        if( $dataarray[$i]['data']['header'] == 'hai'){
                            $dataarray[$i]['data']['values'][] = $values2;

                        }
                    }
                }
                // neck
                if($key == 'stiffness' || $key == 'neck_pain' || $key == 'masses' || $key == 'tenderness'){
                    $values2['name'] = $key;
                    $values2['title'] = ucwords(str_replace("_", " ", $key));
                    $values2['controlType'] = 'radiobutton';
                    if($formid == 0):
                        $values2['selectedvalue'] = 'N/A';
                    else:
                        if(!empty($value)):
                            $values2['selectedvalue'] = str_replace(" ", "_", $value);
                        else:
                            $values2['selectedvalue'] = 'N/A';
                        endif;
                    endif;
                    $values2['headervaluelist'] = array('N/A', 'YES', 'NO');
                    for($i=0; $i<$countarray; $i++){
                        if( $dataarray[$i]['data']['header'] == 'neck'){
                            $dataarray[$i]['data']['values'][] = $values2;

                        }
                    }
                }
            }
            foreach ($dataresult[0] as $key=>$value) {
                if($key == 'constitutional' || $key == 'eyes' || $key == 'ent' || $key == 'breast' || $key == 'respiratory' || $key == 'cardiovascular' || $key == 'gastrointestinal' || $key == 'genitourinary' || $key == 'genitourinarymale' || $key == 'genitourinaryfemale' || $key == 'musculoskeletal' || $key == 'extremities' || $key == 'neurologic' || $key == 'skin' || $key == 'psychiatric' || $key == 'endocrine' || $key == 'hai' || $key == 'neck'){
                    $values2['name'] = $key."_text";
                    $values2['title'] = ucwords(str_replace("_", " ", $key)." Text");
                    $values2['controlType'] = 'textbox';
                    $values2['selectedvalue'] = str_replace(" ", "_", $value);;
                    $values2['headervaluelist'] = '';
                    for($i=0; $i<$countarray; $i++){
                        if( $dataarray[$i]['data']['header'] == $key){
                            $dataarray[$i]['data']['values'][] = $values2;

                        }
                    }
                }
            }
//            foreach ($dataresult[0] as $key=>$value) {
//                if($key =='pending' || $key == 'finalized') {
//                    $values2['name'] = $key;
//                    $values2['controlType'] = 'radiobutton';
//                    $values2['selectedvalue'] = $value;
//                    $values2['headervaluelist'] = array('YES', 'NO');
//                    $dataarray[]= $values2;
//                }
//            }
             foreach ($dataresult[0] as $key=>$value) {
                if($key =='pending' || $key == 'finalized') {
                    $data['data']['header'] = $key;
                    $data['data']['headerTitle'] = ucwords(str_replace("_", " ", $key));
                    $data['data']['controlType'] = 'radiobutton';
                    $data['data']['selectedvalue'] = str_replace(" ", "_", $value);;
                    $data['data']['valuelist'] =array('YES', 'NO');
                    $dataarray[] = $data; 
                }
            }
            
            $newdemo1 = encode_demo($dataarray);  
            $newdemo['FormData'] = check_data_available($newdemo1);
            //echo "<pre>"; print_r($newdemo); echo "<pre>";
            if($newdemo)
            {
                //returns patients 
                $patientres = json_encode($newdemo); 
                echo $patientresult = GibberishAES::enc($patientres, $key1);
            }
            else
            {
                //echo 'No patients available';
                $patientres = '[{"id":"0"}]';
                echo $patientresult = GibberishAES::enc($patientres, $key1);
            }
        } 
        catch(PDOException $e) 
        {
            
            $patientres = '{"error":{"text":'. $e->getMessage() .'}}'; 
            echo $patientresult = GibberishAES::enc($patientres, $key);
        }
    
}
function saveROSFormData(){
    try{
        
        $db = getConnection();
        $nameslist2 = '';
        $namesvalues2 = '';
        $updatenamelist2 = '';
        $flag=0;
        $namesarray = array();
        $request = Slim::getInstance()->request();
        $key = 'rotcoderaclla';
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        $appres = GibberishAES::dec($request->getBody(), $key);
        //$appres = GibberishAES::dec('U2FsdGVkX1/ank1iPdAUx2DrArbJ16ajfj9XMYdHUUuVMsOE6MuPhMfWCUe4DBCdLSo5twpEIE1W0WfLWLNH3XAP4NvT/OtM9IJ624ysok2qbCUubze5S7n09nNGKwz33y6FO9HSprJlfxPJJEJBzONbQkWEPHU6reNly0ocIi+SLzrDrvJVgfl7FCw46q3AvJqT4wnCJ3sQsgW/XCDAOF9HMCJgoGOgY3MELYGM/I7SSrtqa8eKOKoVtCPG8v0vgU7dBnPa2hysvfWCdyIEaWNWq2qIg+X4z+e37vrP0UkwqoIHF567dv8AaOi5spYilzxowmjh7rlezlBhk3yFqI+uLhbgZeylvGEHcANN6+BIgA0j+isb6hMw1+N+yfatS9hrYJxdabJs0mdzXGq+pOkOBt15q+zO28XwfxtnvKcIFCYo8xqbkyatGLyI/dk3Jlirbwe0izJ1A6sAlYb9dfKSF7laS8whgNVLZ09+b+DfGWd59ZTQj6dcQyF2SBQoqSfFSUY1jUlLeWTM53dXkmfz+17GAPHnrVTPdde0vvXNX1E8czujA/L6N72JaqIz988M/rdUJDiMP1DBP1LI2+a7NsbDHmXynebn9jsvNUQDeP+oBcljuvEGccDlmOa9dtk5gv3d+/aIqUjj4iYm7FW9AATUW6MJW3BL0950dLCg2SirYqCKxX5yNSjz/Ute9DwTgClQGFikQjrImt6aGQwGdkgkIv9jVt9oZrEYw/rFBJHO4PlgTtInvJruXN7cGaPlS5WA/98m2PCviXeLhKoXz8MWwRdok6tEcaEXbYtcAiCdxPIAj/VvI06ExfVh+Q+7FENb87aFeHHAlyH0ovOITW5QqG6XF+QO3RMoKAdqBlTOxre+QI3bN7b/G26UNHMNxedRXsCyzTrQdPuau6C9Bj1kMDYmd14s5SC1uG8eT/ljd+adsxVeTbbahJ+lRbhgyRtNQ4Fek5BXGYg1K9exVk7VdbJkdqqWQNISuKWIkSvhqZOt57aPS2VbIYNFXwQGiCgZXY4XARMF4h9A0OWiiR2NDG0uqq5XoZhtO0E7ZWGttYkbi98ebN+BVLKd6PbiNnPoCW+OHy79BZ63TSdt1htW0Xo76MZLP0gSoX+D588zIGKfNmSICt9i9ufHWFbNawKXMM2AVkJcjgi9bjudxc5e8KEUp9lmx/UekQIkDGBRNpZn0FLi7D71ZABFVZu9+ccK9uUCRW2Ds6nLJVJ+B1rAAOLGpQjd7SCa+6E6VQRaEXoG8KdamkH8iCkpkOWY2bpKgTH8o6/vChjicPGNNDXrEaFzLkUV2FUmpXr1EZDKR2GalRlNRMy28rx2ZB62CB/J/jsIxyC+kTXS/MSF8lHea/3iYVNTEYvHbrW7wVdOhjyKw0wnoC3qS99Ff8fYUaTsRPcnVJfp58J1TnRGJBeMg2Q5PPolpMFd7k9QfU2X4LmKVGPNmLD6Y1nhQTRxudeaXeoBrDRkKzVeckcvOygKRn/eNwt4EhUsOLqUOa1Iuy8N46T9FvChQ2sat1bBjNJfWCzKpePLG4Tk2wFspk2Hziiozf+tOPnGu29bGC8l8OrU2QhixeMClRBQtd+2A12+vETtmTisihcUfG1g3by5pLLUw4XhIMHw4w8kh/4mpMRnvn2hEM07Qq0aRFnhRxVfyIlESVb7lFM6rf88EigBFm3vZ5bxTm3xqe1Y2iOod+qmzkX/e7hcTr7JiqHlrKw4+CFIiLWCbhUFz7n9+ivBRw+3R6HCqCp3CehhShYlxyxWXDUyfuWWHR3FtLLd7a34leAtHvopVv62kS+FHwvYlHvIbyLiIeYkmPlTdWAU6dhRtLqwiw/hJbwg5/ybVoy2x5zjeXIn7CI5y17JmYkFX6eq8DdYXQo5GIZbCkK+jg+uZMDQNzE9DLpKfbsvghxm2CWsBkLG3xa+z1GjxSmRC9OxHn4GNRzqfKDtTomjO7GkFmnuhz7gHjCzLC7isWvJnQ1b3uMM4NbS9/iHyW4EspgA/vNUt+Y5NmsLHCXA7xHpbowlHmZHNujn0jx0cY4meO9y4RJsgxJKmriYSu0u9WujmRUepRoMUa++AYRguRjnZqh5Bt+LyfITEp8113y1qwPN9fOxWugWFzW6vDS/922rXfWSiUBpxW4dO8r0BbC7vy1Prhjvmyvxwqu7QHuLPGjLYjmN/XFoafY95BdQfetUKod9qVO+J7chrTT8b0mqjUUSJMBG4paRPama20TzMgvBUsXc/5/Y7MiXP0eDpPPsJmB1E8KKi01zTndRxzhsWpSuY3d/7z9TNHiK2bK9kdf2zPjnAL82IckX/Edeb8DwljO+x76JELWWqkFasjN42XmHCNv97edjU9QLTAnimNZ9Ct8smxPUULE6DR7+SWsPON6IK4cXCY8ncih+4HIN/2RlFGU2QvAu5amgCy085oRXKBjkz6hOPUy2oqPzWXUkeKCvs0gQCFNzDpt8ush73aqqBB/z2X0oWv8N8BZE+CxQk428wXBbONf2sn+pZWE8hEF4sGHlcI6nvWi1pxN5dbh2giFHly2tK1L8hE+0YjZDJEbyLhS5C9kw7lpNxsi68nLbkGsdzOcoQUostdNm6qDC92gjLyt2iuLjfsN43JVe1CGPR2SjjOix+EeggEZMfJ8hi8GH6ogg4Zpj/+BmBGOXrqWKabfSXrQbGEiteUMbYKz4J5SIB3ZI48+pAHGA0JGX4GjKqfc+2f0mWmf/LU1xvsulic5csFo2HTB0b8p3MPG1Og6kj2cK2WhWR3CZi2PcgrwIPfWVB9G6UBf0qUztyBSsfAH8FkjTcYznono2cxipSAz0XzsYvHw4lslyVFwN34gd+Urm0tOIS4dkRnH3vevlwe/6vdGkjXsGLwOleDfAedmpLHVdNBFMD/rMBdI1woorSF7fvUXJYH7CoC9sMjYalC8LKRs/0YjGRkBqb1lCwVYfUjBLh+MF/HpAhzwtZ/MmV8CQwRCjY11T/gbpCcnQI8s9VZD4D0BJ56nxmOZ7BDZ8uGCcvqmdfYtpmRmzsSkqo/LmGBfnEy5h5np7qdlaOLa+2fnNPi1oN0Zs5AkcWQATBLTfSF0x2meMztVcwg0bHaaIdjG189KayuEag0zez8JSh15o9vLniyym2fuH+04Z53wlldqHwSumt4dF8r2KUWopcStwWIaUAIgG33L0EC05X/VdnzfQcF8kimoZI8bJ4bfc+52PX/RM2hNlsUYyGiQc5kgPzFuBqn1eMSoSd2ooyHv4DuL1X/wvf0WZyJCdPMrUDcfpb5mB+cLTX0ZujdWIk+i9PWJHf/4dOIfwNx1jPIOSeOOksuTdnmZOH/d1B1Pd1meYAxiISScEjA2Tr35zbYvUxWQuYywgJ89kxKeizIpCNa4foOjr5YCv7FBL3yuoMBsH0n+6e9Vg0sMbUZx+mwIdoYpJ4sunv5Yd7xBFgXzeMx7xVmmQKO2lVtAOBAkuuHICK/39OhLJONqQnaow0CSUXcv5zBiFGrSAQVptW749LEfP1E5fg6y+4X5uqbjg7okZeQZ3gF/PA0qLsGkcK8rCO2ZaS0v4B9eJnmdo3fXehM3j7JGt7wlMCztndBuO4alFQ5m0CBQR5wA3pQeUXfNgBNgAt1S9lBwNZph2QhX3+y7eyJnW3a6LZEEbrjsLTK25amVA36AjgeKyOz4coPl+v+I86wTdbdygzW9NXtwfBAyI/lgRL6xn0YS+dS2fWdYdCEzad6g978PhIN3nP5uRiRTZ25SijhkloYssTaH7HCvcxMBZ15u+DPxIFIcMP6yY77SGY785/jSgcy9EXM4Vwh+4ZV8p6zXXSphAB5VDYT9Ml9j4GR3v5TxY02fi6ZQCjlA6TrzEUa0giOdElZPtxBaAdOvihCrpfBGfCTDmky9NIu0DVejfrEPDOCObVMurga2q1dCQGTYrzqyfQIAjPLduNLKq/iaekfAvKGwLGcE6jkFzAHk2o5eLM8HVdy0Z3u6N3w80XdAUs9LEk53JmgKQxhde7VeJ01K9zY87+BIwdnpsEUYLAOF/sNKhlohpAAta39ZfF9RMFcCMbzVUDMhOvw0xv8jIsMNRqMSzUzn3owXZ0qhDVRTLOftGu2dFQxXfmWN3ejoMnBnWfUXJGvdeH7Eesq7c9UAUt+rYiOmRVUk9WjesuNMUfFsz4nZraQgOeAiHIaTyDDDkB41wNWHbYiHswrdmcc63ggdrX3GW2BmIjHbUH/R9fAR3aHHO5bFlkvm8B3ZmocOjZpKp/QVH67x8AKJh8c+yVAMPrHyJjLl4EpPrhW5VP8WmGTBbQz4HEZB6VU4k2tSZNogCylj9m2BAB46zL2h80FvXRWxtUGf/rEYxXDG2CxUlf1QzVaMa3+3sFPJ8VRvZF6rD6dsBFYnfV3qjbbrpE0+V+LF3rqoqGbD42nD3eTXD4F0ORyUTIowy2GC0gXTKzswm283/sUPTyBzAoCmXGEfbbik7gYBWjLFM7jA5ZdBbygjTEZ+nFtG9ZL/s3jM2moKTT9bT2NETFJqrxIikw8SoBWxJjYjt3VYVGUJF5p6xkWBhKYSDE9jj0/3LEWhSUF7nrbMSK+Mu8c88ozaoPDV69MMcPQabSS9mT1OSSWAJR6+/OPTLrhFh8MxKvbjEP8H6Q09rHJvh6yrA5ao+zrGnYUO3RJnVMc19TF2obuxdyU4Vmf6Xa/j4028Jss1ZyQKoIPWrSgW0xFc/jYPOWo94TYxf/UNoY3mk7yjVf4P+SQR3rq6fOISTlfBK1EwXXgpEOjSP5qErbptF1SEjJkw/qH5XX/+R3uzgkNmnI6fP22kCnfQlbtxwaBcjfSPWlet1J3EtO7J4B8ZYxuH+8iusPolHzsq2Z77aFqq6iYBIYHOxFuKPdrGLdiKz3YCFvtO/mGZTRI4ODc3R5w60lyIFqXw0ptj5Xp+pyZEAfGbQMdEupB6Aeoz6+DUUAA6CWzRjWa/UbRsr3pPAeaO+ibSmBRjJWUoTIoPsiQZ/gV+fLUaw/MPiXl2/xWohf7HfS2BN3W+7YovxHygje2+uL3VCX/JIgx6pu9y1X5v+meZ48qpcX0U2jGYQqkXkvi6fyM150mv7L2jEdJJEqdka6+cbl70HRD3XafkEBzjIrrWvYX2B9ZNPTV/rdywgx8NLn2MHmaeQoHsSZRwNzvXMmPIEnw1sPsgGUZP4WATSEuYecZitzXf8DYXex5OkmB/QlG4e7qGL8n7wbAEeNZAjmhmDhYdw1yIAs2dQGf82Fn+q/t4Wkrajs3SHqVd9mgxlAjlx/dSMTk3PuX7o1UdWZUNPiFqQ6c7MD137kvxAIDhn+CCFlIihoh6WYVw+QdJckgIuupgTl0rpDEsTqwBDvtjldIcKbYXzm7aRDXfp6BoVh34lRIwWA7dTebdmZ92CKQorjznjR/iBEHnU4dFm05hxymPROWsNykWM3IJDcB3GmqFsgQPcmy1ao6EgEsr0pPMsZOGiwLk16nDfjWcSCB8jGpVDNM1plz7LjUH99oIBUk4HmDXardbUwyESfbRTJXYpVhuByw46FpujUiK1a778F7yi5ozjpE+fOYKep1ZtZhurFXkB203/walUox00CtQbhRTAhNUZ+KehnNWirFFjIPDwbfID46JEiUuGlA08fuk2zMRmzLaPxHXO2vjU2fBOLsN3usL+MjLl0Hw6vFMu83a2GGQAYUPjUSWL4DXC7DQjJW4nBNsnCfFvlQbiIKTQJyCmBa/9CKSfIN0PjX2aWq79He5UMiQhj8RxvLDGfMVnSuYifOSI9nglwItBeiT8QpALvJdro4rx5KcM4St/KhKTzbK0Jqrxhhvh89Qo2KLsrXjxiiGxHnUMKmTJlDbObuoiaxJ+2gsPsTNUk7iryyiGviJ85z3TjVbRcJXKx3zpz83OTapwBsfmeipI+VgtkaAb+x9ab1dbUfWGsCSb7zUag722jCOeSlX+BSajHIoALV72OJS73Eqary41kQkkxnT8leg4b1iNtZlgzShQrvTr6foPgmp+/noinwPCx9lwU/KpfX474ZylrThwIvuunN+nEmTxOll5IqcbEJdwYb5yj9YNLZohUUDWDc39JpMJw9tlmLZy2c5Z7sxv3cgonrfDBFpi7rL2OiIZ5JccqKXYyfOj5SsxMubq43HFTmPyV111hRo8UeDo6qQ=', $key);
        $insertArray = json_decode($appres,TRUE);
        
        $formid     = $insertArray['formid'];
        $encounter  = $insertArray['encounter'];
        $user       = $insertArray['user'];
        $authorized = 1;
        $activity   = 1;
        $date       = 'NOW()';
        
        $logdata= array();
        $array = array();
        $ip_addr=GetIP();
        $patientres = '';
        
        $getusername = "select username from users where id =$user";
        $stmtuser = $db->prepare($getusername) ;
        $stmtuser->execute();
        $getUserName = $stmtuser->fetchAll(PDO::FETCH_OBJ); 
        $username = $getUserName[0]->username;
        $getcolumnNames = "select column_name as columns from information_schema.columns where table_name='tbl_form_allcare_ros'";
        $stmt = $db->prepare($getcolumnNames) ;
        $stmt->execute();                       

        $getnames = $stmt->fetchAll(PDO::FETCH_OBJ); 
        
        foreach ($getnames as $value) {
           if((strcmp($value->columns, 'id') !== 0) && (strcmp($value->columns, 'date') !== 0)  && (strcmp($value->columns, 'activity') !== 0) && (strcmp($value->columns, 'authorized') !== 0)):
                ${$value->columns} = str_replace("_", ' ', $insertArray[$value->columns]);
                
            endif;
        }
       
        foreach ($getnames as $value) {
            if($value->columns != 'id'):
                $nameslist2 .=  $value->columns.",";
                $namesvalues2 .= "'".${$value->columns}."',";
            endif;
            if($value->columns == 'id'):
               $updateid = $formid;
            elseif($value->columns == 'date'):
                $updatenamelist2 .= "date = NOW(),";
            elseif($value->columns == 'activity'):
                $updatenamelist2 .= "activity = 1,";
            else:
                $updatenamelist2 .= $value->columns."='".${$value->columns}."',";
            endif;
        }
        $nameslist = rtrim($nameslist2, ',');         
        $namesvalues = rtrim($namesvalues2, ',');
        $updatenamelist = rtrim($updatenamelist2, ',');
        
        if($formid == 0):
            
            $lastformid = "SELECT MAX(id) as form_id FROM tbl_form_allcare_ros";
            $stmt = $db->prepare($lastformid) ;
            $stmt->execute();
            $maxformidval = $stmt->fetchAll(PDO::FETCH_OBJ);
            $maxformid =  $maxformidval[0]->form_id + 1;
        
            $insertform = "INSERT INTO forms (date,encounter, form_name, form_id, pid, user, groupname, authorized, deleted, formdir) VALUES (NOW(), $encounter, 'Allcare Review Of Systems', $maxformid, $pid, $user, 'Default', $authorized, 0, 'allcare_ros' )";
            $stmt2 = $db->prepare($insertform);
            $stmt2->execute();
            $lastId = $db->lastInsertId();
            
            $getformid = "SELECT form_id FROM forms WHERE id = $lastId";
            $stmt3 = $db->prepare($getformid) ;
            $stmt3->execute();
            $formidval = $stmt3->fetchAll(PDO::FETCH_OBJ);
            $newformid =  $formidval[0]->form_id;
            
            $insert = "INSERT INTO tbl_form_allcare_ros (id,$nameslist) VALUES($newformid,$namesvalues)";
            $stmt2 = $db->prepare($insert) ;
            $stmt2->execute();    
            
            $count = isset($count)? $count: 0;

            $array2[] = array( 'authuser' =>$username,'pending' => $pending,'finalized' => $finalized, 'date' => date("Y/m/d"), 'action'=>'created','ip_address'=>$ip_addr ,'count'=> $count+1);
            $logdata= serialize($array2) ;
            $sql2 = "INSERT into `tbl_allcare_formflag`(`form_id`, `encounter_id`, `form_name`, `pending`,`finalized`,`logdate`) VALUES(".$newformid.",".$encounter.",'Allcare Review of Systems','".$pending."','".$finalized."','".$logdata."')";
            $sqlstmt2 = $db->prepare($sql2) ;
            $data2 = $sqlstmt2->execute();
        else:
            $update = "UPDATE tbl_form_allcare_ros SET $updatenamelist WHERE id = $updateid";
            $stmt2 = $db->prepare($update) ;
            $stmt2->execute(); 
            
            $data2 ="SELECT logdate from `tbl_allcare_formflag` WHERE  form_id=".$formid;
            $rstmt2 = $db->prepare($data2) ;
            $rstmt2->execute();
            $data22 = $rstmt2->fetchAll(PDO::FETCH_OBJ);
            foreach ($data22 as $value) {
                $array =  unserialize($value->logdate);
                $count= count($array);
            }
            $count = isset($count)? $count: 0;

            $result2 ="SELECT * FROM tbl_allcare_formflag WHERE `form_name` = 'Allcare Review of Systems' AND `form_id` =  ".$formid;
            $sqlstmt3 = $db->prepare($result2) ;
            $data3 = $sqlstmt3->execute();
            $maxformidval = $sqlstmt3->fetchAll(PDO::FETCH_OBJ);
            if(count($maxformidval) > 0){
                    $array2[] = array( 'authuser' =>$username,'pending' => $pending,'finalized' => $finalized, 'date' => date("Y/m/d"), 'action' => 'updated','ip_address'=>$ip_addr ,'count'=> $count+1);
                    $logdata = array_merge_recursive($array, $array2);
                    $logdata= ($logdata? serialize($logdata): serialize($array2) );
                    $sql4 = "UPDATE `tbl_allcare_formflag` SET `finalized`='$finalized',
                    `pending` = '$pending',`logdate` ='".$logdata."'  WHERE `form_name` = 'Allcare Review of Systems' AND `form_id` =  ".$formid;
                    $sqlstmt4 = $db->prepare($sql4) ;
                    $data4 = $sqlstmt4->execute();
            }else{ 
                    $array2[] = array( 'authuser' =>$username,'pending' => $pending,'finalized' => $finalized, 'date' => date("Y/m/d"), 'action'=>'created','ip_address'=>$ip_addr ,'count'=> $count+1);
                    $logdata = array_merge_recursive($array, $array2);
                    $logdata= ($logdata? serialize($logdata): serialize($array2) );
                    $sql4 = "INSERT into `tbl_allcare_formflag`(`form_id`, `encounter_id`, `form_name`, `pending`,`finalized`,`logdate`) VALUES(".$formid.",".$encounter.",'Allcare Review of Systems','".$pending."','".$finalized."','".$logdata."')";
                    $sqlstmt4 = $db->prepare($sql4) ;
                    $data4 = $sqlstmt4->execute();
            }
            if($stmt2->execute()){
                $formstatus = '';
                if($pending == 'YES')
                    $formstatus = 'pending';
                if($finalized == 'YES')
                    $formstatus = 'finalized';

                if($finalized == 'YES'  && $pending == 'YES')
                    $formstatus = 'finalized|pending';
                $encform="SELECT form_id FROM forms WHERE formdir= 'LBF2' and deleted = 0 and encounter = $encounter and pid = $pid order by date asc";
                $encstmt31= $db->prepare($encform) ;
                $encform_id = $encstmt31->execute();
                $encformid_val = $encstmt31->fetchAll(PDO::FETCH_OBJ);

                //echo "<pre>"; print_r($encformid_val); echo "</pre>";
                if(!empty($encformid_val)):
                        $result21="select * from lbf_data where form_id='".$encformid_val[0]->form_id."'  AND `field_id`='ros_stat'";
                        $sqlstmt31= $db->prepare($result21) ;
                        $data31 = $sqlstmt31->execute();
                        $formid_val = $sqlstmt31->fetchAll(PDO::FETCH_OBJ);

                        if(!empty($formid_val)){
                            $sql5 = "update `lbf_data`  SET  `field_value`='".$formstatus."' where `form_id`= ".$encformid_val[0]->form_id." AND `field_id`='ros_stat'";
                            $sqlstmt5 = $db->prepare($sql5) ;
                            $data5 = $sqlstmt5->execute();
                        }else {
                           $sql5 = "INSERT into `lbf_data`(`form_id`, `field_id`,`field_value`) VALUES(".$encformid_val[0]->form_id.",'ros_stat','".$formstatus."')";
                           $sqlstmt5 = $db->prepare($sql5) ;
                           $data5 = $sqlstmt5->execute();
                        }
               endif;
            }
        endif;

        if($stmt2->execute()){  
            $insertcheck =  '[{"id":"1"}]';
            echo GibberishAES::enc($insertcheck, $key);
	}else{
            $insertcheck = '[{"id":"0"}]';
            echo GibberishAES::enc($insertcheck, $key);
	}
	  
    }catch(PDOException $e){
            $insertquery = '{"error":{"text":'. $e->getMessage() .'}}'; 
            echo GibberishAES::enc($insertquery, $key);
    }
}

function getPhysicalForm($formid){
    $key = 'rotcoderaclla';
    // The default key size is 256 bits.
    $old_key_size = GibberishAES::size();
    GibberishAES::size(256);
    try 
    {
        $sql = "SELECT * FROM tbl_form_physical_exam WHERE forms_id=$formid";
        $db = getConnection();
        $stmt = $db->prepare($sql) ;
        $stmt->execute();                       

        $patientsreminders = $stmt->fetchAll(PDO::FETCH_OBJ);                        
        $nameslist = array('GENWELL', 'GENAWAKE','HEADNORM', 'HEADLESI', 'EYECP','EYECON','EYEPER','ENTTM','ENTNASAL','ENTORAL','ENTSEPT','NECKSUP','BACKCUR','CVRRR','CVNTOH','CVCP','CVNPE','CVNMU','CVS1S2','CHNSD','RECTAB','REEFF','RELUN','READV','GIOG','GIHERN','GISOFT','GIBOW','GUTEST','GUPROS','GUEG','GUAD','GULES','GUDEF','EXTREMIT','EXTREDEF','EXTREPED','LYAD','MUSTR','MUROM','MUSTAB','MUINSP','MUTEND','NEUCN2','NEUREF','NEUSENS','NEULOCAL','NEUGROSS','PSYAFF','PSYJUD','PSYDEP','PSYSLE','PSYTHO','PSYAPP','PSYABL','SKRASH','SKCLEAN','SKNAIL','OTHER');
        $excludelist = array();
        foreach ($nameslist as $val) {
           
            
                if($val == 'GENWELL' ){
                    $name = str_replace($val ,'Appearance',$val );
                    $system  = 'GEN';
                }

                if($val  == 'GENAWAKE'){
                    $name = str_replace($val ,'Awake, Alert, Oriented, in No Acute Distress',$val );
                    $system  = 'GEN';
                }

                if($val == 'HEADNORM'){
                   $name = str_replace($val,'Normocephalic, Autramatic',$val);
                   $system = 'HEAD';
                }

                if($val == 'HEADLESI'){
                   $name = str_replace($val,'Lesions',$val);
                   $system = 'HEAD';
                }

                if($val == 'EYECP'){
                   $name = str_replace($val,'Conjuntiva, Pupils',$val);
                   $system = 'EYE';
                }

                if($val == 'EYECON'){
                   $name = str_replace($val,'Conjuctive Clear, Tms Intact,Discharge, Wax, Oral Lesions, Gums pink, Bilateral Nasal Turbinates',$val);
                   $system = 'EYE';
                }

                if($val == 'EYEPER'){
                   $name = str_replace($val,'PERRLA, EOMI',$val);
                   $system = 'EYE';
                }

                if($val == 'ENTTM'){
                   $name = str_replace($val,'TMs/EAMs/EE, Ext Nose',$val);
                   $system = 'ENT';
                }

                if($val == 'ENTNASAL'){
                   $name = str_replace($val,'Nasal Mucosa Pink, Septum Midline',$val);
                   $system = 'ENT';
                }

                if($val == 'ENTORAL'){
                   $name = str_replace($val,'Oral Mucosa Pink, Throat Clear',$val);
                   $system = 'ENT';
                }

                if($val == 'ENTSEPT'){
                   $name = str_replace($val,'Septum Midline',$val);
                   $system = 'ENT';
                }

                if($val == 'NECKSUP'){
                   $name = str_replace($val,'Supple,Thyromegaly, Carotid of the Nasal Septum,  JVD,  lymphadenopathy',$val);
                   $system = 'NECK';
                }

                if($val == 'BACKCUR'){
                   $name = str_replace($val,'Normal Curvature, Tenderness',$val);
                   $system = 'BACK';
                }

                if($val == 'CVRRR'){
                   $name = str_replace($val,'RRR',$val);
                   $system = 'CV';
                }

                if($val == 'CVNTOH'){
                   $name = str_replace($val,'Thrills or Heaves',$val);
                   $system = 'CV';
                }

                if($val == 'CVCP'){
                   $name = str_replace($val,'Cartoid Pulsations, Pedal Pulses',$val);
                   $system = 'CV';
                }

                if($val == 'CVNPE'){
                   $name = str_replace($val,'Peripheral Edema',$val);
                   $system = 'CV';
                }

                if($val == 'CVNMU'){
                   $name = str_replace($val,'Murmur, Rubs,Gallops',$val);
                   $system = 'CV';
                }
                
                if($val == 'CVS1S2'){
                   $name = str_replace($val,'S1, S2',$val);
                   $system = 'CV';
                }
                
                if($val == 'CHNSD'){
                   $name = str_replace($val,'Skin Dimpling or Breast Nodules',$val);
                   $system = 'CHEST';
                }

                if($val == 'RECTAB'){
                   $name = str_replace($val,'Lungs CTAB',$val);
                   $system = 'RESP';
                }

                if($val == 'REEFF'){
                   $name = str_replace($val,'Respirator Effort Unlabored',$val);
                   $system = 'RESP';
                }

                if($val == 'RELUN'){
                   $name = str_replace($val,'Lungs Clear,Rales,Rhonchi,Wheezes',$val);
                   $system = 'RESP';
                }
                
                if($val == 'READV'){
                   $name = str_replace($val,'Adventious sounds noted',$val);
                   $system = 'RESP';
                }
                
                if($val == 'GIOG'){
                   $name = str_replace($val,'Ogrganomegoly',$val);
                   $system = 'GI';
                }

                if($val == 'GIHERN'){
                   $name = str_replace($val,'Anus, Rectal Tenderness/Mass',$val);
                   $system = 'GI';
                }

                if($val == 'GISOFT'){
                   $name = str_replace($val,'oft, Non Tender, Non Distended, Masses',$val);
                   $system = 'GI';
                }

                if($val == 'GIBOW'){
                   $name = str_replace($val,'Bowel Sounds present in all four quadrants',$val);
                   $system = 'GI';
                }

                if($val == 'GUTEST'){
                   $name = str_replace($val,'Testicular Tenderness, Masses',$val);
                   $system = 'GU';
                }

                if($val == 'GUPROS'){
                   $name = str_replace($val,'Prostate w/o Enlrgmt, Nodules, Tender',$val);
                   $system = 'GU';
                }

                if($val == 'GUEG'){
                   $name = str_replace($val,'Ext Genitalia, Vag Mucosa, Cervix',$val);
                   $system = 'GU';
                }

                if($val == 'GUAD'){
                   $name = str_replace($val,'Adnexal Tenderness/Masses',$val);
                   $system = 'GU';
                }

                if($val == 'GULES'){
                   $name = str_replace($val,'Normal. Lesions, Discharge, Hernias Noted, Deferred',$val);
                   $system = 'GU';
                }

                if($val == 'GUDEF'){
                   $name = str_replace($val,'Deferred',$val);
                   $system = 'GU';
                }
                
                if($val == 'EXTREMIT'){
                   $name = str_replace($val,'Edema, Cyanosis or Clubbing',$val);
                   $system = 'EXTREMITIES';
                }

                if($val == 'EXTREDEF'){
                   $name = str_replace($val,'Deformities',$val);
                   $system = 'EXTREMITIES';
                }
                
                if($val == 'EXTREPED'){
                   $name = str_replace($val,'Pedal pulses 2+, radial pulses 2+',$val);
                   $system = 'EXTREMITIES';
                }
                
                if($val == 'LYAD'){
                    $name = str_replace($val,'Adenopathy (2 areas required)',$val);
                    $system = 'LYMPH';
                }

                if($val == 'MUSTR'){
                   $name = str_replace($val,'Strength',$val);
                   $system = 'MUSC';
                }

                if($val == 'MUROM'){
                   $name = str_replace($val,'ROM',$val);
                   $system = 'MUSC';
                }

                if($val == 'MUSTAB'){
                   $name = str_replace($val,'Stability',$val);
                   $system = 'MUSC';
                }

                if($val == 'MUINSP'){
                   $name = str_replace($val,'Inspection',$val);
                   $system = 'MUSC';
                }
                
                if($val == 'MUTEND'){
                   $name = str_replace($val,'Tenderness',$val);
                   $system = 'MUSC';
                }

                if($val == 'NEUCN2'){
                   $name = str_replace($val,'CN2-12 Intact',$val);
                   $system = 'NEURO';
                }

                if($val == 'NEUREF'){
                   $name = str_replace($val,'Reflexes Normal',$val);
                   $system = 'NEURO';
                }

                if($val == 'NEUSENS'){
                   $name = str_replace($val,'Sensory Exam Normal',$val);
                   $system = 'NEURO';
                }

                if($val == 'NEULOCAL'){
                   $name = str_replace($val,'Physiological, Localizing Findings',$val);
                   $system = 'NEURO';
                }

                if($val == 'NEUGROSS'){
                   $name = str_replace($val,'Grossly intact',$val);
                   $system = 'NEURO';
                }
                
                if($val == 'PSYAFF'){
                   $name = str_replace($val,'Affect Normal',$val);
                   $system = 'PSYCH';
                }

                if($val == 'PSYJUD'){
                   $name = str_replace($val,'Normal Affect, Judgement and Mood, Alert and Oriented X3',$val);
                   $system = 'PSYCH';
                }

                if($val == 'PSYDEP'){
                   $name = str_replace($val,'Depressive Symptoms',$val);
                   $system = 'PSYCH';
                }

                if($val == 'PSYSLE'){
                   $name = str_replace($val,'Change In Sleeping Habit',$val);
                   $system = 'PSYCH';
                }

                if($val == 'PSYTHO'){
                   $name = str_replace($val,'Change In Thought Content',$val);
                   $system = 'PSYCH';
                }

                if($val == 'PSYAPP'){
                   $name = str_replace($val,'Patient Appears To Be In Good Mood',$val);
                   $system = 'PSYCH';
                }

                if($val == 'PSYABL'){
                   $name = str_replace($val,'Able To Answer Questions Qppropriately',$val);
                   $system = 'PSYCH';
                }

                if($val == 'SKRASH'){
                   $name = str_replace($val,'Rash or Abnormal Lesions',$val);
                   $system = 'SKIN';
                }

                if($val == 'SKCLEAN'){
                   $name = str_replace($val,'Clean & Intact with Good Skin Turgor',$val);
                   $system = 'SKIN';
                }
                
                if($val == 'SKNAIL'){
                   $name = str_replace($val,'Nails are intact',$val);
                   $system = 'SKIN';
                }
                if($val == 'OTHER'){
                   $name = str_replace($val,'Other',$val);
                   $system = 'OTHER';
                }
                $data['data']['system'] = $system;
                $data['data']['label'] = $name;
                $data['data']['labelid'] = $val;
                $data['data']['wnl']['name'] = 'WNL';
                $data['data']['wnl']['controlType'] = 'checkbox';
                $data['data']['abn']['name'] = 'ABN1';
                $data['data']['abn']['controlType'] = 'checkbox';
                $data['data']['diagnosis']['name'] = 'Diagnosis';
                $data['data']['diagnosis']['controlType'] = 'listbox';
                $data['data']['comments']['name'] = 'Comments';
                $data['data']['comments']['controlType'] = 'textbox';
                $dataarray[] = $data; 
            //}
        }
        
        foreach($patientsreminders as $value)
        {   
            array_push($excludelist,$value->line_id);
        }
           
//        }
        foreach($nameslist as $value){
            $sql3 = "SELECT * FROM tbl_form_physical_exam WHERE forms_id=$formid AND line_id = '$value'";
            $stmt3 = $db->prepare($sql3) ;
            $stmt3->execute();                       
            $dataresult = $stmt3->fetchAll(PDO::FETCH_OBJ);
            if(!empty($dataresult)){
                foreach($patientsreminders as $value){
                    $wnl = $value->wnl;
                    $abn = $value->abn;
                    $comments = $value->comments;
                    $diagnosis = $value->diagnosis;
                    $diagnosis_list = '';
                    $sql2 = "SELECT ordering,diagnosis  FROM tbl_form_physical_exam_diagnoses WHERE line_id = '$value->line_id'";
                    $stmt2 = $db->prepare($sql2) ;
                    $stmt2->execute();                       
                    $diagnosis_listvalues = $stmt2->fetchAll(PDO::FETCH_OBJ); 
                    if(!empty($diagnosis_listvalues)):
                        foreach($diagnosis_listvalues as $list){
                            $diagnosis_list[] = array('order' =>$list->ordering, 'value' => $list->diagnosis);
                        }
                    endif; 
                    for($i=0; $i<count($dataarray); $i++){
                        if($dataarray[$i]['data']['labelid'] == $value->line_id):
                            $dataarray[$i]['data']['wnl']['selectedValue'] = $wnl;
                            $dataarray[$i]['data']['abn']['selectedValue'] = $abn;
                            $dataarray[$i]['data']['diagnosis']['selectedValue'] = $diagnosis;
                            $dataarray[$i]['data']['comments']['selectedValue'] = $comments;
                            if(empty($diagnosis_list)):
                                $dataarray[$i]['data']['diagnosis']['diagnosis_list']= '';
                            else:
                                $dataarray[$i]['data']['diagnosis']['diagnosis_list']= $diagnosis_list; 
                            endif;
                        endif;
                    }
                }
            }else{
                $diagnosis_list = '';
                $sql2 = "SELECT ordering,diagnosis  FROM tbl_form_physical_exam_diagnoses WHERE line_id = '$value'";
                $stmt2 = $db->prepare($sql2) ;
                $stmt2->execute();                       

                $diagnosis_listvalues = $stmt2->fetchAll(PDO::FETCH_OBJ); 
                if(!empty($diagnosis_listvalues)):
                    foreach($diagnosis_listvalues as $list){
                        $diagnosis_list[] = array($list->ordering, 'value' => $list->diagnosis);
                    }
                endif;
                for($i=0; $i<count($dataarray); $i++){
                    if($dataarray[$i]['data']['labelid'] == $value){
                        $dataarray[$i]['data']['wnl']['selectedValue'] = '';
                        $dataarray[$i]['data']['abn']['selectedValue'] = '';
                        $dataarray[$i]['data']['diagnosis']['selectedValue'] = '';
                        $dataarray[$i]['data']['comments']['selectedValue'] = '';
                        if(!empty($diagnosis_list)):
                            $dataarray[$i]['data']['diagnosis']['diagnosis_list']= $diagnosis_list;
                        else:
                            $dataarray[$i]['data']['diagnosis']['diagnosis_list']= ''; 
                        endif;
                    }                
                }
            }
        }
        $sql4 = "SELECT pending,finalized  FROM tbl_allcare_formflag WHERE form_id = $formid AND form_name = 'Allcare Physical Exam'";
        $stmt4 = $db->prepare($sql4) ;
        $stmt4->execute();                       

        $status = $stmt4->fetchAll(PDO::FETCH_OBJ); 
        //print_r($status);
        if(!empty($status)):
            $final = $status[0]->finalized;
            $pending = $status[0]->pending;
        else:
            $final = '';
            $pending = '';
        endif;
        $final_data['data']['label'] = 'Finalized';
        $final_data['data']['selectedValue'] = $final;
        $dataarray[] = $final_data;
        $pending_data['data']['label'] = 'Pending';
        $pending_data['data']['selectedValue'] =  $pending;
        $dataarray[] = $pending_data;
        //echo "<pre>"; print_r($dataarray); echo "</pre>";
        $newdemo1 = encode_demo($dataarray);  
        $newdemo['FormData'] = check_data_available($newdemo1);
        if($newdemo)
        {
            //returns patients 
            $patientres = json_encode($newdemo); 
            echo $patientresult = GibberishAES::enc($patientres, $key);
        }
        else
        {
           $patientres = '[{"id":"0"}]';
           echo $patientresult = GibberishAES::enc($patientres, $key);
        }
    } 
    catch(PDOException $e) 
    {
       $patientreserror =  '{"error":{"text":'. $e->getMessage() .'}}'; 
       echo $patientresult = GibberishAES::enc($patientreserror, $key);
    }
}
function savePhysicalExamFormData(){
    try 
    {
        $db = getConnection();
        $request = Slim::getInstance()->request();
        $key = 'rotcoderaclla';
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        $appres = GibberishAES::dec($request->getBody(), $key);
//        $appres = GibberishAES::dec('U2FsdGVkX19K5DyrelG7simBc/3bh2KQ0zvLyWwHUnnc+lZcgQlxVP0yQIW9YgCKLnG+ioRp43YG0CkEhpJ2qerOgbbnatk2xafUpoU4f1WGQVRbHsP/Hj0b1S+71qAtghnBF7QQUQkkHUr/UgoO4AClGUsOdz/hrL4NpaOBZvsCNO99PUywgERg6Qd/kQUO6FfCudtPo5eyHifokfK3OWLEaBHQUwh7++NS2lb5+qZrn1yqekphsAEB5JrE1Nn8BDXCL8uviEBVq6dUwX2RtbB0swnxYcLg5P/FO76N3tW6/42J5mbJupCwe32uwnl8CJvtgn0KDVYqC5ueK41AkPOK0lSvQmr0spL5D+Wya41FM+TpTwyYKxQCuQ2rDqVn+ARNszSb40d/rMxVDAkYs53u6Zc9gPO+OhXKI9S1a+6oOfndR8OBo9XX9HWeQBGXrd7Hmfrapc3w6I+3hA6EKKrIS4gYl5tWCPcPhCkwOope1Jhwu3DpzzjQb8dO6VTNTgeI01TiCgGkUU8hE2sNAE6qws7RyOBPXgIIYzxG+OPoCPqC9Rd0T9rflPLAG9uM/PG4NDcOcjKPud0reeVAHNqCvY1dbX21nvKv2LVnNiT6Kh9ZUbWhTPk5UVNnA5caf9fZ5giI4WHAuj3WlfRMePX045sISD4gd8resttKsysZzbkowNhGcHWSLqjJ8cNPugfjPd7ImBaA0G3G6yYA+GclENX/rcKIElMgbF6208t896D34CHcdC9f5YgNatlmhh4D9VnQe7O8Kg4lzotGUzXwID86dXOw6ZpGUDT10Bbd9/+GKf2x9EwmvFxRw/0iKE40F1wrJUPhbXSvqoWIUVPg8KA5iuE0XFtObmWIKDOAUbq1a4d6KaJEIt5dCbI7Rk4GbFCokGbjG/s9Qgx/CeAfbw+j4u50AtqD44s8L8wgoP6Kl35RMu62foY3yV/xz968Yg0QvkKu1bT27Y4ohKis+cq3okgJeCDg0y3oQK0rrSannjHkc6/KB4egaTURCrMjuaMJZ0CemDxybh8fhzJ3dqzx4Kms0TzulQYp2VmGEbRjpb8RPoQp3USeplseeYGr1nqeAT7rozDsDSNsJYz3N5inIWHlKyrNcsj2yoJ5BCS7rXKMGIFh+SB4iSUGx6E37Ml4DRQCQkVv0oJVKlpJ/z6pMGxBc0Xnss8VIfM///scwlMRxQ6i0M8uEStqJlInk3IML+C0zYiykGiLK2N4uJxrALN08qgJ6lL05TZsCIwdIuhoUE67vzgNdWmX2hf8Jm5qPj+ecPZ9DyvnLQFSAiN7gzykc5m7OswrxysyvxCTQkdmby6Gz4ZpZiZwyhMImzetoRYHQoDglKd/o1dbtQEIs1ZHVaEmJZWHO3Bej5HSm7YJ0/mXbIIlpYIU8HqMQMrISqAhV2nA5ChkkEkAri+VzYCYO6w4+dnI5IOozAD/y1qKLXR8ZmsVQ0BFv86GAeWoTRc3yBb7BAyvXt2Wan9YOTJJqGFFXRl8jmli2t/hfxzlQ4ZMawW/Qa/4zF72WMiAb2RcVjSVuMHD/3c1hIUzbt0A5EkL77n+FxYliTUhT3VHqxuUXjMk5XlMHXEh7Bpse3mUerFX/xU/wmp9ju2BdJ2byj5QI5ewSxpZalPieNxYglztpjq0O1MS4Wp1havcdmwMzoRwEqzl7zzurvJ5zLpoyTt91hgMTrCBFXc97PSDvJOwNaZpzejbCxiy03eobShXRsm1C/rosJO981f+l5kf1IwtQVrds0cLy3jzHd6ciUENGLj1jSAGojOzzlTjXsF/O6AtsZgJmBGKssSwCTNVFFKYcTn4fb8=', $key);
        $insertArray = json_decode($appres,TRUE);
        $nameslist = array('GENWELL', 'GENAWAKE','HEADNORM', 'HEADLESI', 'EYECP','EYECON','EYEPER','ENTTM','ENTNASAL','ENTORAL','ENTSEPT','NECKSUP','BACKCUR','CVRRR','CVNTOH','CVCP','CVNPE','CVNMU','CVS1S2','CHNSD','RECTAB','REEFF','RELUN','READV','GIOG','GIHERN','GISOFT','GIBOW','GUTEST','GUPROS','GUEG','GUAD','GULES','GUDEF','EXTREMIT','EXTREDEF','EXTREPED','LYAD','MUSTR','MUROM','MUSTAB','MUINSP','MUTEND','NEUCN2','NEUREF','NEUSENS','NEULOCAL','NEUGROSS','PSYAFF','PSYJUD','PSYDEP','PSYSLE','PSYTHO','PSYAPP','PSYABL','SKRASH','SKCLEAN','SKNAIL','OTHER');
        $formid                     = $insertArray['formid'];
        $encounter                  = $insertArray['encounter'];
        $pid                        = $insertArray['pid'];
        $user                       = $insertArray['user'];
        $authorized                 = 1; 
        $activity                   = 1 ;
//        print_r($insertArray);
//        $GENWELL  = array('GENWELL',  1, 1, 'test1', 'test data update');//$insertArray['GENWELL']; 
//        $GENAWAKE  = array('GENAWAKE',  1, 1, 'asd', 'test data update');// $insertArray['GENAWAKE'];
//        $HEADNORM  = array('HEADNORM',  1, 1, 'test3', 'test data update'); //$insertArray['HEADNORM'];
        $GENWELL  = $insertArray['GENWELL']; 
        $GENAWAKE  = $insertArray['GENAWAKE'];
        $HEADNORM  = $insertArray['HEADNORM'];
        $HEADLESI  = $insertArray['HEADLESI']; 
        $EYECP  = $insertArray['EYECP'];
        $EYECON  = $insertArray['EYECON'];
        $EYEPER  = $insertArray['EYEPER'];
        $ENTTM  = $insertArray['ENTTM'];
        $ENTNASAL  = $insertArray['ENTNASAL'];
        $ENTORAL  = $insertArray['ENTORAL'];
        $ENTSEPT  = $insertArray['ENTSEPT'];
        $NECKSUP  = $insertArray['NECKSUP'];
        $BACKCUR  = $insertArray['BACKCUR'];
        $CVRRR  = $insertArray['CVRRR'];
        $CVNTOH  = $insertArray['CVNTOH'];
        $CVCP  = $insertArray['CVCP'];
        $CVNPE  = $insertArray['CVNPE'];
        $CVNMU  = $insertArray['CVNMU'];
        $CVS1S2  = $insertArray['CVS1S2'];
        $CHNSD  = $insertArray['CHNSD'];
        $RECTAB  = $insertArray['RECTAB'];
        $REEFF  = $insertArray['REEFF'];
        $RELUN  = $insertArray['RELUN'];
        $READV  = $insertArray['READV'];
        $GIOG  = $insertArray['GIOG'];
        $GIHERN  = $insertArray['GIHERN'];
        $GISOFT  = $insertArray['GISOFT'];
        $GIBOW  = $insertArray['GIBOW'];
        $GUTEST  = $insertArray['GUTEST'];
        $GUPROS  = $insertArray['GUPROS'];
        $GUEG  = $insertArray['GUEG'];
        $GUAD  = $insertArray['GUAD'];
        $GULES  = $insertArray['GULES'];
        $GUDEF  = $insertArray['GUDEF'];
        $EXTREMIT  = $insertArray['EXTREMIT'];
        $EXTREDEF  = $insertArray['EXTREDEF'];
        $EXTREPED  = $insertArray['EXTREPED'];
        $LYAD  = $insertArray['LYAD'];
        $MUSTR  = $insertArray['MUSTR'];
        $MUROM  = $insertArray['MUROM'];
        $MUSTAB  = $insertArray['MUSTAB'];
        $MUINSP  = $insertArray['MUINSP'];
        $MUTEND  = $insertArray['MUTEND'];
        $NEUCN2  = $insertArray['NEUCN2'];
        $NEUREF  = $insertArray['NEUREF'];
        $NEUSENS  = $insertArray['NEUSENS'];
        $NEULOCAL  = $insertArray['NEULOCAL'];
        $NEUGROSS  = $insertArray['NEUGROSS'];
        $PSYAFF  = $insertArray['PSYAFF'];
        $PSYJUD  = $insertArray['PSYJUD'];
        $PSYDEP  = $insertArray['PSYDEP'];
        $PSYSLE  = $insertArray['PSYSLE'];
        $PSYTHO  = $insertArray['PSYTHO'];
        $PSYAPP  = $insertArray['PSYAPP'];
        $PSYABL  = $insertArray['PSYABL'];
        $SKRASH  = $insertArray['SKRASH'];
        $SKCLEAN  = $insertArray['SKCLEAN'];
        $SKNAIL  = $insertArray['SKNAIL'];
        $OTHER  = $insertArray['OTHER'];
        $finalized  = $insertArray['finalized']; 
        $pending  =  $insertArray['pending']; 
        $array = array();
        $logdata= array();
        $ip_addr=GetIP();
        $patientres = '';
        
        if($formid == 0){
            
            $lastformid = "SELECT MAX(forms_id) as form_id FROM tbl_form_physical_exam";
            $stmt = $db->prepare($lastformid) ;
            $stmt->execute();
            $maxformidval = $stmt->fetchAll(PDO::FETCH_OBJ);
            $maxformid =  $maxformidval[0]->form_id + 1;
        
            $insertform = "INSERT INTO forms (date,encounter, form_name, form_id, pid, user, groupname, authorized, deleted, formdir) VALUES (NOW(), $encounter, 'Allcare Physical Exam', $maxformid, $pid, $user, 'Default', $authorized, 0, 'allcare_physical_exam' )";
            $stmt2 = $db->prepare($insertform);
            $stmt2->execute();
            $lastId = $db->lastInsertId();
            
            $getformid = "SELECT form_id FROM forms WHERE id = $lastId";
            $stmt3 = $db->prepare($getformid) ;
            $stmt3->execute();
            $formidval = $stmt3->fetchAll(PDO::FETCH_OBJ);
            $newformid =  $formidval[0]->form_id;
            
            foreach($nameslist as $val){
                $valuedata1   = ${$val};
                $valuedata  = explode('|',$valuedata1);
                $value      = $val; //$valuedata[0];
                $wnl        = $valuedata[0];
                $abn        = $valuedata[1];
                $diagnosis  = $valuedata[2];
                $comments   = $valuedata[3];
                
                echo $sql = "INSERT INTO tbl_form_physical_exam (forms_id, line_id, wnl, abn, diagnosis, comments) VALUES($newformid,'$value', $wnl, $abn, $diagnosis, $comments )"; 
                $sqlstmt = $db->prepare($sql) ;
                $data =  $sqlstmt->execute();
            }
            
            $count = isset($count)? $count: 0;
             $finalized1=''; $pending1='';
            if($pending == 'yes')
                $pending1 = 'Y';
            else if($pending == 'no')
                 $pending1 = 'N';
            else if($finalized == 'yes')
                $finalized1 = 'Y';
            else if($finalized == 'no')
                $finalized1 = 'N';                
            $array2[] = array( 'authuser' =>$user,'pending' => $pending1,'finalized' => $finalized1, 'date' => date("Y/m/d"), 'action'=>'created','ip_address'=>$ip_addr ,'count'=> $count+1);
            $logdata= serialize($array2) ;
            $sql2 = "INSERT into `tbl_allcare_formflag`(`form_id`, `encounter_id`, `form_name`, `pending`,`finalized`,`logdate`) VALUES(".$newformid.",".$encounter.",'Allcare Physical Exam','".$pending1."','".$finalized1."','".$logdata."')";
            $sqlstmt2 = $db->prepare($sql2) ;
            $data2 = $sqlstmt2->execute();
            
            
             $encform="SELECT form_id FROM forms WHERE formdir= 'LBF2' and deleted = 0 and encounter = $encounter and pid = $pid order by date asc";
            $encstmt31= $db->prepare($encform) ;
            $encform_id = $encstmt31->execute();
            $encformid_val = $encstmt31->fetchAll(PDO::FETCH_OBJ);
        }else{ 
            foreach($nameslist as $val){
                $valuedata1   = ${$val};
                $valuedata  = explode('|',$valuedata1);
                $value      = $val; //$valuedata[0];
                $wnl        = $valuedata[0];
                $abn        = $valuedata[1];
                $diagnosis  = $valuedata[2];
                $comments   = $valuedata[3];
                
                $get_sql = "SELECT * 
                            FROM tbl_form_physical_exam
                            WHERE forms_id =$formid
                            AND line_id =  '$value'"; 
                $getsqlstmt = $db->prepare($get_sql) ;
                $getdata =  $getsqlstmt->execute();
                $getdata_res = $getsqlstmt->fetchAll(PDO::FETCH_OBJ);
                if(!empty($getdata_res)){
                    $sql = "UPDATE tbl_form_physical_exam SET wnl = $wnl , abn = $abn , diagnosis = $diagnosis , comments = $comments
                            WHERE line_id = '$value' AND forms_id = $formid";
                    $sqlstmt = $db->prepare($sql) ;
                    $data = $sqlstmt->execute();
                }else{
                    $sql = "INSERT INTO tbl_form_physical_exam (forms_id, line_id, wnl, abn, diagnosis, comments) VALUES($formid,'$value', $wnl, $abn, $diagnosis, $comments )"; 
                    $sqlstmt = $db->prepare($sql) ;
                    $data =  $sqlstmt->execute();
                }
            }
            $data2 ="SELECT logdate from `tbl_allcare_formflag` WHERE  form_id=".$formid;
            $rstmt2 = $db->prepare($data2) ;
            $rstmt2->execute();
            $data22 = $rstmt2->fetchAll(PDO::FETCH_OBJ);
            foreach ($data22 as $value) {
                $array =  unserialize($value->logdate);
                $count= count($array);
            }
            $count = isset($count)? $count: 0;
           $finalized1=''; $pending1='';
            if($pending == 'yes')
                $pending1 = 'Y';
            else if($pending == 'no')
                 $pending1 = 'N';
            if(trim($finalized) == 'yes')
                $finalized1 = 'Y';
            else if(trim($finalized) == 'no')
                $finalized1 = 'N';             
            
            $result2 ="SELECT * FROM tbl_allcare_formflag WHERE `form_name` = 'Allcare Physical Exam' AND `form_id` =  ".$formid;
            $sqlstmt3 = $db->prepare($result2) ;
            $data3 = $sqlstmt3->execute();
            $maxformidval = $sqlstmt3->fetchAll(PDO::FETCH_OBJ);
            if(count($maxformidval) > 0){
                    $array2[] = array( 'authuser' =>$user,'pending' => $pending1,'finalized' => $finalized1, 'date' => date("Y/m/d"), 'action' => 'updated','ip_address'=>$ip_addr ,'count'=> $count+1);
                    $logdata = array_merge_recursive($array, $array2);
                    $logdata= ($logdata? serialize($logdata): serialize($array2) );
                    $sql4 = "UPDATE `tbl_allcare_formflag` SET `finalized`='$finalized1',
                    `pending` = '$pending1',`logdate` ='".$logdata."'  WHERE `form_name` = 'Allcare Physical Exam' AND `form_id` =  ".$formid;
                    $sqlstmt4 = $db->prepare($sql4) ;
                    $data4 = $sqlstmt4->execute();
            }else{ 
                    $array2[] = array( 'authuser' =>$user,'pending' => $pendin1g,'finalized' => $finalized1, 'date' => date("Y/m/d"), 'action'=>'created','ip_address'=>$ip_addr ,'count'=> $count+1);
                    $logdata = array_merge_recursive($array, $array2);
                    $logdata= ($logdata? serialize($logdata): serialize($array2) );
                    $sql4 = "INSERT into `tbl_allcare_formflag`(`form_id`, `encounter_id`, `form_name`, `pending`,`finalized`,`logdate`) VALUES(".$formid.",".$encounter.",'Allcare Physical Exam','".$pending1."','".$finalized1."','".$logdata."')";
                    $sqlstmt4 = $db->prepare($sql4) ;
                    $data4 = $sqlstmt4->execute();
            }
        }
        if($data){
            $formstatus = '';
            if($pending == 'yes')
                $formstatus = 'pending';
            elseif($finalized == 'yes')
                $formstatus = 'finalized';
            
            if($finalized == 'yes'  && $pending == 'yes')
                $formstatus = 'finalized|pending';
             
            $encform="SELECT form_id FROM forms WHERE formdir= 'LBF2' and deleted = 0 and encounter = $encounter and pid = $pid order by date asc";
            $encstmt31= $db->prepare($encform) ;
            $encform_id = $encstmt31->execute();
            $encformid_val = $encstmt31->fetchAll(PDO::FETCH_OBJ);
            
            //echo "<pre>"; print_r($encformid_val); echo "</pre>";
            if(!empty($encformid_val)):
                    $result21="select * from lbf_data where form_id='".$encformid_val[0]->form_id."'  AND `field_id`='physical_exam_stat'";
                    $sqlstmt31= $db->prepare($result21) ;
                    $data31 = $sqlstmt31->execute();
                    $formid_val = $sqlstmt31->fetchAll(PDO::FETCH_OBJ);

                    if(!empty($formid_val)){
                        $sql5 = "update `lbf_data`  SET  `field_value`='".$formstatus."' where `form_id`= ".$encformid_val[0]->form_id." AND `field_id`='physical_exam_stat'";
                        $sqlstmt5 = $db->prepare($sql5) ;
                        $data5 = $sqlstmt5->execute();
                    }else {
                       $sql5 = "INSERT into `lbf_data`(`form_id`, `field_id`,`field_value`) VALUES(".$encformid_val[0]->form_id.",'physical_exam_stat','".$formstatus."')";
                       $sqlstmt5 = $db->prepare($sql5) ;
                       $data5 = $sqlstmt5->execute();
                    }
           endif;
        }
        if($data){
            $patientres = '[{"id":"1"}]';
            echo $patientresult = GibberishAES::enc($patientres, $key);
        }
        else{
            $patientres =  '[{"id":"0"}]';
            echo $patientresult = GibberishAES::enc($patientres, $key);
        }
    } 
    catch(PDOException $e) 
    {

        $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo $patientresult = GibberishAES::enc($error, $key);
    }
}
function getFacetoFace($encounterid,$formid,$uid){
    try 
    {
        $db = getConnection();
        $apikey = 'rotcoderaclla';
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        $getCodesData = array();
        $sql = "select form_id as group_name, field_id, title,seq,CASE data_type
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
                    END as data_type, uor, list_id, description from layout_options WHERE group_name like '%Face to Face HH Plan' order by seq ";      
        $db->query( "SET NAMES utf8"); 
        
        $stmt = $db->prepare($sql) ;
        $stmt->execute();                       
        $getGroupData = $stmt->fetchAll(PDO::FETCH_OBJ);    
        for($i=0; $i< count($getGroupData); $i++){
            $data = array();
            foreach($getGroupData[$i] as $key => $value){
                $data[$key] = $value;
                if($key == 'field_id'):
                   
                        $sql2 = "SELECT field_value FROM lbf_data WHERE field_id = '$value' AND form_id = $formid";
                        $db->query( "SET NAMES utf8"); 
                        $stmt2 = $db->prepare($sql2) ;
                        $stmt2->execute();                       
                        $getFieldData = $stmt2->fetchAll(PDO::FETCH_OBJ);   
                        if($value!='f2f_ps'):
                            if(!empty($getFieldData)):
                                $data['selected_value'] = $getFieldData[0]->field_value;
                            else:
                                $data['selected_value'] = '';
                            endif;
                        elseif($value=='f2f_ps'):
                            if(!empty($getFieldData)):
                                 $get_provider = "SELECT * FROM form_encounter WHERE encounter = $encounterid";
                                 $get_stmt51 = $db->prepare($get_provider) ;
                                 $get_stmt51->execute(); 
                                 $get_provider_data = $get_stmt51->fetchAll(PDO::FETCH_OBJ); 
                                 $provider_id=$get_provider_data[0]->provider_id;
                                $data['selected_value'] = $provider_id;
                            else:
                                $data['selected_value'] = '';
                            endif;
                        endif;     
                        
                elseif($key == 'list_id'):
                    if($value != '' && $data['data_type'] != 'Providers' && $data['data_type'] != 'Providers NPI'):
                        $sql3 = "SELECT option_id, title, seq FROM list_options WHERE list_id = '$value' ";
                        $db->query( "SET NAMES utf8"); 
                        $stmt3 = $db->prepare($sql3) ;
                        $stmt3->execute();                       
                        $getListData = $stmt3->fetchAll(PDO::FETCH_OBJ);  
                        if(!empty($getListData)):
                            $data2 = array();
                            foreach($getListData as $list){
                                    $data2[$list->option_id] = $list->title;
                            }
                        else:
                            $data2 = '';
                        endif;
                        $data['options_list'] = $data2; 
                    elseif ($data['data_type'] == 'Providers' || $data['data_type'] == 'Providers NPI'):
                        if($data['field_id'] == 'f2f_ps'){
                            $sql3 = "SELECT u.id, CONCAT(u.fname, ' ',u.lname) as name FROM `users` u 
                                INNER JOIN openemr_postcalendar_events o ON u.id = o.pc_aid 
                                INNER JOIN form_encounter f ON f.pid = o.pc_pid AND f.pc_catid = o.pc_catid AND o.pc_eventDate = DATE_FORMAT( f.date,  '%Y-%m-%d' )
                                WHERE u.active=1 AND u.authorized=1 AND encounter = $encounterid ";
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
                            $data['options_list'] = $data2;
                        }else{   
                            $sql3 = "SELECT id, CONCAT(fname, ' ',lname) as name FROM `users`  WHERE active=1 AND authorized=1 ";
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
                            $data['options_list'] = $data2;   
                        }
                    else:
                        $data['options_list'] = '';
                    endif;
                endif;
                if($key == 'field_id' && $value == 'f2f_findings' ):
                    $sql4 = "SELECT l.diagnosis FROM lists l INNER JOIN issue_encounter i ON l.pid = i.pid WHERE i.encounter = $encounterid";
                    $db->query( "SET NAMES utf8"); 
                    $stmt4 = $db->prepare($sql4);
                    $stmt4->execute();                       
                    $getDiagnosisData = $stmt4->fetchAll(PDO::FETCH_OBJ);  
                    
                    foreach($getDiagnosisData as $key => $diag){ 
                        $sql5 = "SELECT notes FROM list_options WHERE list_id = 'F2F_Clinical_Finding' AND codes LIKE '%".substr($diag->diagnosis, 5)."%'";
                        $stmt5 = $db->prepare($sql5) ;
                        $stmt5->execute();                       
                        $getCodesData = $stmt5->fetchAll(PDO::FETCH_OBJ);  
                        if(!empty($getCodesData)):
                            $data['Clinical_Finding_Text'] = $getCodesData[0]->notes;
                        endif;
                    }
                endif;
            }
            $dataarray[] = $data;
        }
        
        
        $get_accesssql = "SELECT * FROM tbl_user_custom_attr_1to1 WHERE processmobilef2f = 'YES' AND userid= '$uid'";
        $get_accessstmt = $db->prepare($get_accesssql) ;
        $get_accessstmt->execute();                       

        $setaccess = $get_accessstmt->fetchAll(PDO::FETCH_OBJ);
        if(!empty($setaccess)){
            $dataarray['access_permission'] = 1;
        }else{
            $dataarray['access_permission'] = 0;
        }
//        echo "<pre>"; print_r($dataarray); echo "</pre>";
        $newdemo=encode_demo($dataarray);  
        $newdemo2['FacetoFace'] = check_data_available($newdemo);
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
function getFacetoFace2($encounterid,$formid){
    try 
    {
        $db = getConnection();
        $key = 'rotcoderaclla';
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        $getCodesData = array();
        $sql = "select form_id as group_name, field_id, title,seq,CASE data_type
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
                    END as data_type, uor, list_id, description from layout_options WHERE group_name like '%Face to Face HH Plan' order by seq ";      
        $db->query( "SET NAMES utf8"); 
        
        $stmt = $db->prepare($sql) ;
        $stmt->execute();                       
        $getGroupData = $stmt->fetchAll(PDO::FETCH_OBJ);    
        for($i=0; $i< count($getGroupData); $i++){
            $data = array();
            foreach($getGroupData[$i] as $key => $value){
                $data[$key] = $value;
                if($key == 'field_id'):
                   
                        $sql2 = "SELECT field_value FROM lbf_data WHERE field_id = '$value' AND form_id = $formid";
                        $db->query( "SET NAMES utf8"); 
                        $stmt2 = $db->prepare($sql2) ;
                        $stmt2->execute();                       
                        $getFieldData = $stmt2->fetchAll(PDO::FETCH_OBJ);   
                        if($value!='f2f_ps'):
                            if(!empty($getFieldData)):
                                $data['selected_value'] = $getFieldData[0]->field_value;
                            else:
                                $data['selected_value'] = '';
                            endif;
                        elseif($value=='f2f_ps'):
                            if(!empty($getFieldData)):
                                 $get_provider = "SELECT * FROM form_encounter WHERE encounter = $encounterid";
                                 $get_stmt51 = $db->prepare($get_provider) ;
                                 $get_stmt51->execute(); 
                                 $get_provider_data = $get_stmt51->fetchAll(PDO::FETCH_OBJ); 
                                 $provider_id=$get_provider_data[0]->provider_id;
                                $data['selected_value'] = $provider_id;
                            else:
                                $data['selected_value'] = '';
                            endif;
                        endif;     
                        
                elseif($key == 'list_id'):
                    if($value != '' && $data['data_type'] != 'Providers' && $data['data_type'] != 'Providers NPI'):
                        $sql3 = "SELECT option_id, title, seq FROM list_options WHERE list_id = '$value' ";
                        $db->query( "SET NAMES utf8"); 
                        $stmt3 = $db->prepare($sql3) ;
                        $stmt3->execute();                       
                        $getListData = $stmt3->fetchAll(PDO::FETCH_OBJ);  
                        if(!empty($getListData)):
                            $data2 = array();
                            foreach($getListData as $list){
                                    $data2[$list->option_id] = $list->title;
                            }
                        else:
                            $data2 = '';
                        endif;
                        $data['options_list'] = $data2; 
                    elseif ($data['data_type'] == 'Providers' || $data['data_type'] == 'Providers NPI'):
                        $sql3 = "SELECT id, CONCAT(fname, ' ',lname) as name FROM `users`  WHERE active=1 AND authorized=1 ";
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
                        $data['options_list'] = $data2;     
                    else:
                        $data['options_list'] = '';
                    endif;
                endif;
                if($key == 'field_id' && $value == 'f2f_findings' ):
                    $sql4 = "SELECT l.diagnosis FROM lists l INNER JOIN issue_encounter i ON l.pid = i.pid WHERE i.encounter = $encounterid";
                    $db->query( "SET NAMES utf8"); 
                    $stmt4 = $db->prepare($sql4) ;
                    $stmt4->execute();                       
                    $getDiagnosisData = $stmt4->fetchAll(PDO::FETCH_OBJ);  
                    
                    foreach($getDiagnosisData as $key => $diag){ 
                        $sql5 = "SELECT notes FROM list_options WHERE list_id = 'F2F_Clinical_Finding' AND codes LIKE '%".substr($diag->diagnosis, 5)."%'";
                        $stmt5 = $db->prepare($sql5) ;
                        $stmt5->execute();                       
                        $getCodesData = $stmt5->fetchAll(PDO::FETCH_OBJ);  
                        if(!empty($getCodesData)):
                            $data['Clinical_Finding_Text'] = $getCodesData[0]->notes;
                        endif;
                    }
                endif;
            }
            $dataarray[] = $data;
        }
        //echo "<pre>"; print_r($dataarray); echo "</pre>";
        $newdemo=encode_demo($dataarray);  
        $newdemo2['FacetoFace'] = check_data_available($newdemo);
        if($newdemo2)
        {
            //$result = utf8_encode_recursive($newdemo2);
            echo $patientres =  json_encode($newdemo2); 
            //echo $patientresult = GibberishAES::enc($patientres, $key);
        }
        else
        {
           echo '[{"id":"0"}]';
        }
    } 
    catch(PDOException $e) 
    {

        echo '{"error":{"text":'. $e->getMessage() .'}}'; 
    }
}
function utf8_encode_recursive ($array)
{
    $result = array();
    foreach ($array as $key => $value)
    {
        if (is_array($value))
        {
            $result[$key] = utf8_encode_recursive($value);
        }
        else if (is_string($value))
        {
            $result[$key] = utf8_encode($value);
        }
        else
        {
            $result[$key] = $value;
        }
    }
    return $result;
}
function getPatientsByFilter($deceased, $activestatus){
        if($deceased == 0)
            echo $deceased ='';
        if($activestatus == 0)
            echo $activestatus ='';
        $sql = "select pid,fname,lname,dob from patient_data WHERE  practice_status = '$activestatus' AND deceased_stat = '$deceased'";      
        $count= "select count(*) from patient_data WHERE  practice_status = '$activestatus' AND deceased_stat = '$deceased'";
        try 
        {
            $db = getConnection();
            $stmt = $db->prepare($sql) ;
            $stmt->bindParam("all", $count);            
            $stmt->execute();                       
             
            $patients = $stmt->fetchAll(PDO::FETCH_OBJ);                        
            
            if($patients)
            {
                //returns patients 
                echo json_encode($patients); 
            }
            else
            {
                //echo 'No patients available';
                echo '[{"id":"0"}]';
            }
        } 
        catch(PDOException $e) 
        {
            
            echo '{"error":{"text":'. $e->getMessage() .'}}'; 
        }
}
function getapptstatus(){
     $key = 'rotcoderaclla';
    // The default key size is 256 bits.
    $old_key_size = GibberishAES::size();
    GibberishAES::size(256);
    $qry = "select option_id as id ,title from list_options where list_id='apptstat' and notes ='M' order by seq ";
    try 
    {
        $db = getConnection();
        $stmt = $db->prepare($qry) ;
        $stmt->execute();                       

        $res = $stmt->fetchAll(PDO::FETCH_OBJ);                        
        
        if($res)
        {
            $data =  json_encode($res); 
            echo $datares = GibberishAES::enc($data, $key);
            
        }
        else
        {
            $data =  '[{"title":"0"}]';
            echo $datares = GibberishAES::enc($data, $key);
        }
    } 
    catch(PDOException $e) 
    {

        $error =  '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo $datares = GibberishAES::enc($error, $key);
    }
}
function createNewEnounter(){
    $db = getConnection();
    $key = 'rotcoderaclla';
    $old_key_size = GibberishAES::size();
    $request = Slim::getInstance()->request();
    GibberishAES::size(256);
    $appres = GibberishAES::dec($request->getBody(), $key);
    //$appres = GibberishAES::dec('U2FsdGVkX18TIOfKRQmpxtC6HvMAaYgNBoZUbuaGkYCXwsWjoQMs5x/8+WX4HsH4J0adZ2gVVlvbxBEY0zPZ+PiH5gLBR/oJgmk9ExUbn6wu/GFgiijfyX7JV2AjJF5HPi0opo/c1z5r+WjbF6XDD162Yb9vzDKxvmMPAz5ym46/JFaUOZHmCVppTValgVUQAz3bTeEDliEpWH3Sc5xXrFYjNeUg61b1C7VJNOZILqGhnztVViR5HxsY+InXatsX', $key);
    $insertArray                = json_decode($appres,TRUE);
    $date                       = date('Y-m-d H:i:s', strtotime(str_replace('-','/', $insertArray[0]['encDate'])));  
    $pid                        = $insertArray[0]['pid'];
    $user                       = $insertArray[0]['loginProvderId'];
    $billing_facility           = $insertArray[0]['billing_facility'];
    $pc_facility                = $insertArray[0]['pc_facility'];
    $encDate                    = date('Y-m-d H:i:s', strtotime(str_replace('-','/', $insertArray[0]['encDate'])));  
    $pc_catid                   = $insertArray[0]['category']; 
    $pc_title                   = $insertArray[0]['pc_catname']; 
    $pc_apptstatus              = $insertArray[0]['pc_apptstatus']; 
    $pc_eid                     = $insertArray[0]['apptid'];
    
    try{
        
        $getfacilityname = "SELECT name FROM facility where id = $pc_facility";
        $fstmt = $db->prepare($getfacilityname);
        $fstmt->execute();
        $getfacilityname = $fstmt->fetchAll(PDO::FETCH_OBJ);
        $facility = $getfacilityname[0]->name;

        $sqlGetMaxEncounter="SELECT MAX(encounter)+1 as max_encounter FROM form_encounter";

        $stmt2 = $db->prepare($sqlGetMaxEncounter);
        $stmt2->execute();
        $resMaxEncounter = $stmt2->fetchAll(PDO::FETCH_OBJ);

        if($resMaxEncounter){
            $encounter = $resMaxEncounter[0]->max_encounter;
        }

        $sql10 = "INSERT INTO form_encounter (date, facility, facility_id, pid, encounter, pc_catid, provider_id, billing_facility)
        VALUES ('$date', '$facility',$pc_facility,$pid,$encounter,$pc_catid,$user,$billing_facility)";
        
        $q = $db->prepare($sql10);

        if($q->execute()){
            $sqlGetLastEncounter="SELECT MAX(encounter) as encounter, form_encounter.id, username FROM form_encounter INNER JOIN users ON form_encounter.provider_id = users.id WHERE pid=$pid AND form_encounter.provider_id=$user";

            //$db = getConnection();
            $stmt = $db->prepare($sqlGetLastEncounter) ;
            $stmt->execute();
            $newEnc = $stmt->fetchAll(PDO::FETCH_OBJ);
            
            $insertform = "INSERT INTO forms (date, encounter, form_name, form_id, pid, user, groupname, authorized, deleted, formdir) VALUES('$date',".$newEnc[0]->encounter.",'New Patient Encounter',".$newEnc[0]->id.",$pid,'".$newEnc[0]->username."','Default',1, 0,'newpatient')";
            $stmt3 = $db->prepare($insertform) ;
            $stmt3->execute();
            //$newform = $stmt3->fetchAll(PDO::FETCH_OBJ);
            
            $updatestatus = "UPDATE openemr_postcalendar_events SET pc_catid = $pc_catid, pc_title= '$pc_title', pc_apptstatus = '$pc_apptstatus'  WHERE pc_eid=$pc_eid ";
            $stmt4 = $db->prepare($updatestatus) ;
            $stmt4->execute();
            //$updated = $stmt4->fetchAll(PDO::FETCH_OBJ);
            if($newEnc){
                    $newencres = '[{"id":"'.$newEnc[0]->encounter.'"}]'; 
                    echo $newencresult = GibberishAES::enc($newencres, $key);
            }else{
                    $newencres = '[{"id":"-1"}]';     
                    echo $newencresult = GibberishAES::enc($newencres, $key);
            }
        } else {
            $newencres = '[{"id":"0"}]';
            echo $newencresult = GibberishAES::enc($newencres, $key);
        }
    } catch (Exception $ex) {
        $error =  '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo $datares = GibberishAES::enc($error, $key);
    }
    
}

function addDiagnosis(){
    $db = getConnection();
    $key = 'rotcoderaclla';
        // The default key size is 256 bits.
    $old_key_size = GibberishAES::size();
    GibberishAES::size(256);
    $request = Slim::getInstance()->request();
    $appres = GibberishAES::dec($request->getBody(), $key);
    //$appres = GibberishAES::dec('U2FsdGVkX19kcskenfmbZdaSLpkBLMqBCoVtk0LRqvBQoqBjBrZM9nD2lm6mMSOt8tH8upBsFE37s1FiGoPAdDKZbSpF5JbhiCN9hqOGmv8F1EKUsqA8iLM+8kQAOeBWMMAtXQppvPJHzT4wgOOXHirZDJ+fR8XjALsWAsyCpvU=', $key);
    $insertArray2                = json_decode($appres,TRUE);
    
    try 
    {
        $deletesql = "DELETE FROM form_physical_exam_diagnoses WHERE line_id = '".$insertArray[0]['fieldname']."'";      
        $dstmt = $db->prepare($deletesql) ;
        $dstmt->execute(); 
        for($i=0; $i<count($insertArray); $i++){
            $fieldname                  = $insertArray[$i]['fieldname']; 
            $order                      = $insertArray[$i]['order'];  
            $diagnosis                  = $insertArray[$i]['diagnosis'];

            $sql = "INSERT INTO form_physical_exam_diagnoses ( line_id, ordering, diagnosis ) VALUES ( '$fieldname', '$order', '$diagnosis' )";      
            $stmt = $db->prepare($sql) ;
            $stmt->execute();                       
        }
        $patientsreminders = $stmt->fetchAll(PDO::FETCH_OBJ);                        

        if($patientsreminders)
        {
            $result = json_encode($patientsreminders); 
            echo $result2 = GibberishAES::enc($result, $key);
        }
        else
        {
            $result = '[{"id":"0"}]';
            echo $result2 = GibberishAES::enc($result, $key);
        }
    } 
    catch(PDOException $e) 
    {
        $result = '{"error":{"text":'. $e->getMessage() .'}}';
        echo $result2 = GibberishAES::enc($result, $key);
    }
}
function getBillingFacilityList(){
     $key = 'rotcoderaclla';
    // The default key size is 256 bits.
    $old_key_size = GibberishAES::size();
    GibberishAES::size(256);
    $qry = "select name, id from facility where primary_business_entity=1 ";
    try 
    {
        $db = getConnection();
        $stmt = $db->prepare($qry) ;
        $stmt->execute();                       

        $res = $stmt->fetchAll(PDO::FETCH_OBJ);                        

        if($res)
        {
            $data =  json_encode($res); 
            echo $datares = GibberishAES::enc($data, $key);
            
        }
        else
        {
            $data =  '[{"id":"0"}]';
            echo $datares = GibberishAES::enc($data, $key);
        }
    } 
    catch(PDOException $e) 
    {

        $error =  '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo $datares = GibberishAES::enc($error, $key);
    }
}
function getLayoutDemographics($pid){
    $key = 'rotcoderaclla';
    // The default key size is 256 bits.
    $old_key_size = GibberishAES::size();
    GibberishAES::size(256);
    
    try{
        $db = getConnection();
        $demographics_data = array();
        $getGroupNames = "select distinct group_name FROM layout_options WHERE form_id = 'DEM' order by group_name";
        $stmt = $db->prepare($getGroupNames) ;
        $stmt->execute();                       
        $setGroupNames = $stmt->fetchAll(PDO::FETCH_OBJ);   
        if(!empty($setGroupNames)){
            foreach($setGroupNames as $fieldname){
                $setGroupFields = '';
                $getGroupFields = "SELECT GROUP_CONCAT(field_id) as field_name FROM layout_options WHERE form_id = 'DEM' AND group_name = '$fieldname->group_name'";
                $stmt2 = $db->prepare($getGroupFields) ;
                $stmt2->execute();                       
                $setGroupFields = $stmt2->fetchAll(PDO::FETCH_OBJ);  
                if(!empty($setGroupFields)){
                    $fieldlabels = '';
                    $check  = 0;
                    if(strpos($setGroupFields[0]->field_name, 'em_') === 0){ 
                        $check = 1; 
                        $fieldlabels = str_replace('em_', 'e.', $setGroupFields[0]->field_name);
                    }else{
                        $fieldlabels = $setGroupFields[0]->field_name ;
                    }
                    if($check == 1){
                        $getDemographicsData = "SELECT $fieldlabels FROM patient_data p INNER JOIN employer_data e ON p.pid = e.pid WHERE p.pid= $pid";
                    }else{
                        $getDemographicsData = "SELECT $fieldlabels FROM patient_data WHERE pid= $pid";
                    }    
                    $stmt3 = $db->prepare($getDemographicsData) ;
                    $stmt3->execute();                       
                    $setDemographicsData = $stmt3->fetchAll(PDO::FETCH_OBJ);  
                    if(!empty($setDemographicsData)){
                        $demographics_data[substr($fieldname->group_name, 1)] = $setDemographicsData[0];
                    }
                }
            }
        }
        //echo "<pre>"; print_r($demographics_data); echo"</pre>";
        $demographics_data2=encode_demo($demographics_data); 
        $demo['Demographics'] = check_data_available($demographics_data2);
        if($demo){
            $data =  json_encode($demo); 
            echo $datares = GibberishAES::enc($data, $key);
        }else{
            $data =  '[{"id":"0"}]';
            echo $datares = GibberishAES::enc($data, $key);
        }
    }catch(PDOException $e) {
        $error =  '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo $datares = GibberishAES::enc($error, $key);
    }
}
function getLayoutHistory($patientId){
    try
    {	
        $data = '';
        $newdemo = array();
        $dataarray = array();
        //$group_name = str_replace('_', ' ', $group_name2);
        $db=getConnection();
        $enckey = 'rotcoderaclla';
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        $getgroupnames = "SELECT DISTINCT(group_name) as group_name from layout_options where form_id='HIS' and uor <> 0 order by group_name";
        $db->query( "SET NAMES utf8"); 
        $stmt = $db->prepare($getgroupnames) ;
        $stmt->execute();                       
        $setgroupnames2 = $stmt->fetchAll(PDO::FETCH_OBJ);  
        foreach($setgroupnames2 as $setgroupnames){
            $fieldnames = "select field_id, title,data_type, seq, list_id 
                        from layout_options where form_id='HIS' and uor <> 0 AND group_name='".$setgroupnames->group_name."' order by seq";
            $db->query( "SET NAMES utf8"); 
            $stmt2 = $db->prepare($fieldnames); 
            $stmt2->execute();
            $fieldnamesresult = $stmt2->fetchAll(PDO::FETCH_OBJ);
            //$fieldnameslist2 = '';
            if(!empty($fieldnamesresult)){
                foreach($fieldnamesresult as $fieldnamesarray){
                    $listname = $fieldnamesarray->list_id;
                    $gethistorydata = "SELECT ".$fieldnamesarray->field_id ." FROM history_data WHERE pid = $patientId order by date desc limit 1";
                    $db->query( "SET NAMES utf8"); 
                    $stmt3 = $db->prepare($gethistorydata); 
                    $stmt3->execute();
                    $sethistorydata = $stmt3->fetchAll(PDO::FETCH_OBJ); 
                    if($sethistorydata != ''){
                        for($i=0; $i< count($sethistorydata); $i++){
                            foreach($sethistorydata[$i] as $key => $sethis){ 
                                if(!empty($fieldnamesarray->title)):
                                    $title = $fieldnamesarray->title;
                                else:
                                    $title = $fieldnamesarray->field_id;
                                endif;
                                if($fieldnamesarray->list_id != ''){
                                    $examdata = '';
                                    if($fieldnamesarray->data_type == 23  ){
                                        if(!empty($sethis)){
                                            $explodeval2 = explode('|', $sethis);
                                            $explodelist2  = array();
                                            $natype = $ntype = $atype = '';
                                            for($i= 0; $i< count($explodeval2); $i++){
                                                $explodelist2 = explode(":", $explodeval2[$i]);
                                                $getvalname = "SELECT title FROM list_options WHERE option_id =  '$explodelist2[0]' AND list_id = '$fieldnamesarray->list_id'";
                                                $db->query( "SET NAMES utf8"); 
                                                $stmt6 = $db->prepare($getvalname) ;
                                                $stmt6->execute();                       
                                                $setvalname2  = $stmt6->fetchAll(PDO::FETCH_OBJ);  
                                                foreach($setvalname2 as $setvalname){
                                                    $text = '';
                                                    if($explodelist2[1] == 0){
                                                        $type = 'N/A';
                                                        if(!empty($explodelist2[2])){
                                                           $text =  "(".$explodelist2[2].")";
                                                        }
                                                        $natype .= $setvalname->title.$text.",";
                                                    }elseif($explodelist2[1] == 1){
                                                        $type = 'Normal';
                                                        if(!empty($explodelist2[2])){
                                                           $text =  "(".$explodelist2[2].")";
                                                        }
                                                        $ntype .= $setvalname->title.$text.",";
                                                    }elseif($explodelist2[1] == 2){
                                                        $type = 'Abnormal';
                                                        if(!empty($explodelist2[2])){
                                                           $text =  "(".$explodelist2[2].")";
                                                        }
                                                        $atype .= $setvalname->title.$text.",";
                                                    }
                                                }
                                            }
                                            $data[substr($setgroupnames->group_name,1)]['N/A'] = rtrim($natype, ',');
                                            $data[substr($setgroupnames->group_name,1)]['Normal'] = rtrim($ntype, ',');
                                            $data[substr($setgroupnames->group_name,1)]['Abnormal'] = rtrim($atype, ',');
                                        }else{
                                            $data[substr($setgroupnames->group_name,1)][$title] = $sethis;
                                        }    
                                    }elseif($fieldnamesarray->data_type == 32  ){
                                        if(!empty($sethis)){
                                            $titlefield = '';
                                            $explodeval = explode("|", $sethis);
                                            $title = '';
                                            if(!empty($explodeval[3])){
                                                $getvalname = "SELECT title FROM list_options WHERE option_id =  '$explodeval[3]' AND list_id = '$fieldnamesarray->list_id'";
                                                $db->query( "SET NAMES utf8"); 
                                                $stmt6 = $db->prepare($getvalname) ;
                                                $stmt6->execute();                       
                                                $setvalname2  = $stmt6->fetchAll(PDO::FETCH_OBJ);
                                                foreach($setvalname2 as $setvalname){
                                                    $titlefield = $setvalname->title."&nbsp;";
                                                }
                                            }
                                            $statustypes = array('current'=>'Current','quit' =>'Quit','never' => 'Never','not_applicable' => 'N/A');
                                            $statusname = '';
                                            foreach($statustypes as $key => $stype){
                                                if($explodeval[1] == $key.$fieldnamesarray->field_id):
                                                    $statusname = $stype;
                                                endif;
                                            }
                                            $smokingdata = $titlefield.$explodeval[0].str_repeat('&nbsp;', 5)."Status: ".$statusname. "  ".$explodeval[2];
                                            if(empty($titlefield))
                                                $data[substr($setgroupnames->group_name,1)][$fieldnamesarray->title] = $smokingdata;
                                            else
                                                $data[substr($setgroupnames->group_name,1)][$titlefield] = $smokingdata;
                                        }else{
                                            $data[substr($setgroupnames->group_name,1)][$title] = $sethis;
                                        }    
                                    }else{
                                        if(!empty($sethis)){
                                            $explodeval = explode("|", $sethis);
                                            $arraydata = '';
                                            for($i=0; $i< count($explodeval); $i++){
                                                $getvalname = "SELECT title FROM list_options WHERE option_id =  '$explodeval[$i]' AND list_id = '$fieldnamesarray->list_id'";
                                                $db->query( "SET NAMES utf8"); 
                                                $stmt6 = $db->prepare($getvalname) ;
                                                $stmt6->execute();                       
                                                $setvalname2  = $stmt6->fetchAll(PDO::FETCH_OBJ);
                                                if(!empty($setvalname2))
                                                    $arraydata .= $setvalname2[0]->title.",";
                                            }    
                                            
                                            $data[substr($setgroupnames->group_name,1)][$fieldnamesarray->title] = rtrim($arraydata,',');
                                        }else{
                                            $data[substr($setgroupnames->group_name,1)][$title] = $sethis;
                                        }   
                                    }
                                }else{ 
                                    if($fieldnamesarray->data_type == 28){
                                        if(!empty($sethis)){
                                            $explodeval = explode("|", $sethis);
                                            $statusname = '';
                                            $statustypes = array('current'=>'Current','quit' =>'Quit','never' => 'Never','not_applicable' => 'N/A');
                                            foreach($statustypes as $key => $stype){
                                                if($explodeval[1] == $key.$fieldnamesarray->field_id):
                                                    $statusname = $stype;
                                                endif;
                                            }
                                            $smokingdata = $explodeval[0].str_repeat('&nbsp;', 5)."Status: ".$statusname. "  ".$explodeval[2];
                                            $data[substr($setgroupnames->group_name,1)][$fieldnamesarray->title] = $smokingdata;
                                        }else{
                                            $data[substr($setgroupnames->group_name,1)][$title] = $sethis;
                                        }    
                                    }elseif($fieldnamesarray->data_type == 10 || $fieldnamesarray->data_type == 11 || $fieldnamesarray->data_type == 38  ){
                                        if(!empty($sethis)){
                                            $getprovidername = "SELECT  CONCAT(fname,' ',lname) as name FROM users WHERE id = $sethis";
                                            $db->query( "SET NAMES utf8"); 
                                            $stmt6 = $db->prepare($getprovidername) ;
                                            $stmt6->execute();                       
                                            $setprovidername2  = $stmt6->fetchAll(PDO::FETCH_OBJ);
                                            $providername = $setprovidername2[0]->name;
                                            $data[substr($setgroupnames->group_name,1)][$title] = ucwords($providername);
                                        }else{
                                            $data[substr($setgroupnames->group_name,1)][$title] = ucwords($sethis);
                                        }    
                                    }elseif($fieldnamesarray->data_type == 14 ){
                                        if(!empty($sethis)){
                                            $getprovidername = "SELECT  CONCAT(fname,' ',lname) as name, username, organization FROM users WHERE id = $sethis";
                                            $db->query( "SET NAMES utf8"); 
                                            $stmt6 = $db->prepare($getprovidername) ;
                                            $stmt6->execute();                       
                                            $setprovidername2  = $stmt6->fetchAll(PDO::FETCH_OBJ);
                                            $title2 = '';
                                            if (empty($setprovidername2[0]->username) ) {
                                               $title2 = $setprovidername2[0]->organization;
                                            }else{
                                                $title2 = $setprovidername2[0]->name;
                                            }
                                            $data[substr($setgroupnames->group_name,1)][$title] = ucwords($title2);
                                        }else{
                                            $data[substr($setgroupnames->group_name,1)][$title] = ucwords($sethis);
                                        }    
                                    }elseif($fieldnamesarray->data_type == 12 ){
                                        if(!empty($sethis)){
                                            $getpharmacyname = "SELECT  name FROM pharmacies WHERE id = $sethis";
                                            $db->query( "SET NAMES utf8"); 
                                            $stmt6 = $db->prepare($getpharmacyname) ;
                                            $stmt6->execute();                       
                                            $setpharmacyname  = $stmt6->fetchAll(PDO::FETCH_OBJ);
                                            $pharmacyname = $setpharmacyname[0]->name;
                                            $data[substr($setgroupnames->group_name,1)][$title] = ucwords($pharmacyname);
                                        }else{
                                            $data[substr($setgroupnames->group_name,1)][$title] = ucwords($sethis);
                                        }    
                                    }elseif($fieldnamesarray->data_type == 37 ){
                                        if(!empty($sethis)){
                                            $getinsurancename = "SELECT  name FROM insurance_companies WHERE id = $sethis";
                                            $db->query( "SET NAMES utf8"); 
                                            $stmt6 = $db->prepare($getinsurancename) ;
                                            $stmt6->execute();                       
                                            $setinsurancename  = $stmt6->fetchAll(PDO::FETCH_OBJ);
                                            $insurancename = $setinsurancename[0]->name;
                                            $data[substr($setgroupnames->group_name,1)][$title] = ucwords($insurancename);
                                        }else{
                                            $data[substr($setgroupnames->group_name,1)][$title] = ucwords($sethis);
                                        }    
                                    }elseif($fieldnamesarray->data_type == 35 ){
                                        if(!empty($sethis)){
                                            $getfacilityname = "SELECT  name FROM facility WHERE id = $sethis";
                                            $db->query( "SET NAMES utf8"); 
                                            $stmt6 = $db->prepare($getfacilityname) ;
                                            $stmt6->execute();                       
                                            $setfacilityname  = $stmt6->fetchAll(PDO::FETCH_OBJ);
                                            $facilityname = $setfacilityname[0]->name;
                                            $data[substr($setgroupnames->group_name,1)][$title] = ucwords($facilityname);
                                        }else{
                                            $data[substr($setgroupnames->group_name,1)][$title] = ucwords($sethis);
                                        }    
                                    }else{
                                        $data[substr($setgroupnames->group_name,1)][$title] = $sethis;
                                    } 
                                }
                            } 
                        }    
                    }   
                    $listname  = '';
                }
            }
        }
        //echo "<pre>"; print_r($data);echo "</pre>";
        $newdemo1 = encode_demo($data);  
        $newdemo['HistoryData'] = check_data_available($newdemo1);
        //exit();
        if($newdemo){
           $hisres = json_encode($newdemo);
           echo $historyresult =  GibberishAES::enc($hisres, $enckey);
        }else{
            $hisres = '[{"id":"0"}]';
            echo $historyresult =  GibberishAES::enc($hisres, $enckey);
        }
    }catch(PDOException $e)
    {
        $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo $historyresult =  GibberishAES::enc($error, $enckey);
    }   
}

function getLayoutDemographicsDynamic($patientId){
    try
    {	
        $newdemo = array();
        $dataarray = array();
        //$group_name = str_replace('_', ' ', $group_name2);
        $db=getConnection();
        $enckey = 'rotcoderaclla';
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        $getgroupnames = "SELECT DISTINCT(group_name) as group_name from layout_options where form_id='DEM' and uor <> 0 order by group_name";
        $stmt = $db->prepare($getgroupnames) ;
        $stmt->execute();   
        $data = '';
        $setgroupnames2 = $stmt->fetchAll(PDO::FETCH_OBJ);  
        foreach($setgroupnames2 as $setgroupnames){
            $fieldnames = "select field_id, title,data_type, seq, list_id 
                        from layout_options where form_id='DEM' and uor <> 0 AND group_name='".$setgroupnames->group_name."' order by seq";
            $stmt2 = $db->prepare($fieldnames); 
            $stmt2->execute();
            $fieldnamesresult = $stmt2->fetchAll(PDO::FETCH_OBJ);
            //$fieldnameslist2 = '';
            if(!empty($fieldnamesresult)){
                $check = '';
                foreach($fieldnamesresult as $fieldnamesarray){
                    $listname = $fieldnamesarray->list_id;
                    if(strpos($fieldnamesarray->field_id, 'em_') === false ){ 
                        $fieldlabels = $fieldnamesarray->field_id ;
                    }else{ 
                        $check = 1; 
                        $fieldlabels = str_replace('em_', 'e.', $fieldnamesarray->field_id);
                    }
                    if($check == 1){
                        $getDemographicsData = "SELECT $fieldlabels FROM patient_data p INNER JOIN employer_data e ON p.pid = e.pid WHERE p.pid= $patientId";
                    }else{
                        $getDemographicsData = "SELECT $fieldlabels FROM patient_data WHERE pid= $patientId";
                    }    
                    
                    //$gethistorydata = "SELECT ".$fieldnamesarray->field_id ." FROM patient_data WHERE pid = $patientId order by date desc limit 1";
                    $stmt3 = $db->prepare($getDemographicsData); 
                    $stmt3->execute();
                    $sethistorydata = $stmt3->fetchAll(PDO::FETCH_OBJ); 
                    if($sethistorydata != ''){
                        for($i=0; $i< count($sethistorydata); $i++){
                            foreach($sethistorydata[$i] as $key => $sethis){ 
                                if(!empty($fieldnamesarray->title)):
                                    $title = $fieldnamesarray->title;
                                else:
                                    $title = $fieldnamesarray->field_id;
                                endif;
                                if($fieldnamesarray->list_id != ''){
                                    $examdata = '';
                                    if($fieldnamesarray->data_type == 23){
                                        if(!empty($sethis)){
                                            $explodeval2 = explode('|', $sethis);
                                            $explodelist2  = array();
                                            $natype = $ntype = $atype = '';
                                            for($i= 0; $i< count($explodeval2); $i++){
                                                $explodelist2 = explode(":", $explodeval2[$i]);
                                                $getvalname = "SELECT title FROM list_options WHERE option_id =  '$explodelist2[0]' AND list_id = '$fieldnamesarray->list_id'";
                                                $stmt6 = $db->prepare($getvalname) ;
                                                $stmt6->execute();                       
                                                $setvalname2  = $stmt6->fetchAll(PDO::FETCH_OBJ);  
                                                foreach($setvalname2 as $setvalname){
                                                    $text = '';
                                                    if($explodelist2[1] == 0){
                                                        $type = 'N/A';
                                                        if(!empty($explodelist2[2])){
                                                           $text =  "(".$explodelist2[2].")";
                                                        }
                                                        $natype .= $setvalname->title.$text.",";
                                                    }elseif($explodelist2[1] == 1){
                                                        $type = 'Normal';
                                                        if(!empty($explodelist2[2])){
                                                           $text =  "(".$explodelist2[2].")";
                                                        }
                                                        $ntype .= $setvalname->title.$text.",";
                                                    }elseif($explodelist2[1] == 2){
                                                        $type = 'Abnormal';
                                                        if(!empty($explodelist2[2])){
                                                           $text =  "(".$explodelist2[2].")";
                                                        }
                                                        $atype .= $setvalname->title.$text.",";
                                                    }
                                                }
                                            }
                                            $data[substr($setgroupnames->group_name,1)]['N/A'] = rtrim($natype, ',');
                                            $data[substr($setgroupnames->group_name,1)]['Normal'] = rtrim($ntype, ',');
                                            $data[substr($setgroupnames->group_name,1)]['Abnormal'] = rtrim($atype, ',');
                                        }else{
                                            $data[substr($setgroupnames->group_name,1)][$title] = $sethis;
                                        }    
                                    }elseif($fieldnamesarray->data_type == 32  ){
                                        if(!empty($sethis)){
                                            $titlefield = '';
                                            $explodeval = explode("|", $sethis);
                                            $title = '';
                                            if(!empty($explodeval[3])){
                                                $getvalname = "SELECT title FROM list_options WHERE option_id =  '$explodeval[3]' AND list_id = '$fieldnamesarray->list_id'";
                                                $db->query( "SET NAMES utf8"); 
                                                $stmt6 = $db->prepare($getvalname) ;
                                                $stmt6->execute();                       
                                                $setvalname2  = $stmt6->fetchAll(PDO::FETCH_OBJ);
                                                foreach($setvalname2 as $setvalname){
                                                    $titlefield = $setvalname->title."&nbsp;";
                                                }
                                            }
                                            $statustypes = array('current'=>'Current','quit' =>'Quit','never' => 'Never','not_applicable' => 'N/A');
                                            $statusname = '';
                                            foreach($statustypes as $key => $stype){
                                                if($explodeval[1] == $key.$fieldnamesarray->field_id):
                                                    $statusname = $stype;
                                                endif;
                                            }
                                            $smokingdata = $titlefield.$explodeval[0].str_repeat('&nbsp;', 5)."Status: ".$statusname. "  ".$explodeval[2];
                                            if(empty($titlefield))
                                                $data[substr($setgroupnames->group_name,1)][$fieldnamesarray->title] = $smokingdata;
                                            else
                                                $data[substr($setgroupnames->group_name,1)][$titlefield] = $smokingdata;
                                        }else{
                                            $data[substr($setgroupnames->group_name,1)][$title] = $sethis;
                                        }    
                                    }else{
                                        if(!empty($sethis)){
                                            $explodeval = explode("|", $sethis);
                                            $arraydata = '';
                                            for($i=0; $i< count($explodeval); $i++){
                                                $getvalname = "SELECT title FROM list_options WHERE option_id =  '$explodeval[$i]' AND list_id = '$fieldnamesarray->list_id'";
                                                $stmt6 = $db->prepare($getvalname) ;
                                                $stmt6->execute();                       
                                                $setvalname2  = $stmt6->fetchAll(PDO::FETCH_OBJ);
                                                if(!empty($setvalname2))
                                                    $arraydata .= $setvalname2[0]->title.",";
                                            }    
                                            
                                            $data[substr($setgroupnames->group_name,1)][$fieldnamesarray->title] = rtrim($arraydata,',');
                                        }else{
                                            $data[substr($setgroupnames->group_name,1)][$title] = $sethis;
                                        }   
                                    }
                                }else{ 
                                    if($fieldnamesarray->data_type == 28){
                                        if(!empty($sethis)){
                                            $explodeval = explode("|", $sethis);
                                            $statusname = '';
                                            $statustypes = array('current'=>'Current','quit' =>'Quit','never' => 'Never','not_applicable' => 'N/A');
                                            foreach($statustypes as $key => $stype){
                                                if($explodeval[1] == $key.$fieldnamesarray->field_id):
                                                    $statusname = $stype;
                                                endif;
                                            }
                                            $smokingdata = $explodeval[0].str_repeat('&nbsp;', 5)."Status: ".$statusname. "  ".$explodeval[2];
                                            $data[substr($setgroupnames->group_name,1)][$fieldnamesarray->title] = $smokingdata;
                                        }else{
                                            $data[substr($setgroupnames->group_name,1)][$title] = $sethis;
                                        }    
                                    }elseif($fieldnamesarray->data_type == 10 || $fieldnamesarray->data_type == 11 || $fieldnamesarray->data_type == 38  ){
                                        if(!empty($sethis)){
                                            $getprovidername = "SELECT  CONCAT(fname,' ',lname) as name FROM users WHERE id = $sethis";
                                            $stmt6 = $db->prepare($getprovidername) ;
                                            $stmt6->execute();                       
                                            $setprovidername2  = $stmt6->fetchAll(PDO::FETCH_OBJ);
                                            $providername = $setprovidername2[0]->name;
                                            $data[substr($setgroupnames->group_name,1)][$title] = ucwords($providername);
                                        }else{
                                            $data[substr($setgroupnames->group_name,1)][$title] = ucwords($sethis);
                                        }    
                                    }elseif($fieldnamesarray->data_type == 14 ){
                                        if(!empty($sethis)){
                                            $getprovidername = "SELECT  CONCAT(fname,' ',lname) as name, username, organization FROM users WHERE id = $sethis";
                                            $stmt6 = $db->prepare($getprovidername) ;
                                            $stmt6->execute();                       
                                            $setprovidername2  = $stmt6->fetchAll(PDO::FETCH_OBJ);
                                            $title2 = '';
                                            if (empty($setprovidername2[0]->username) ) {
                                               $title2 = $setprovidername2[0]->organization;
                                            }else{
                                                $title2 = $setprovidername2[0]->name;
                                            }
                                            $data[substr($setgroupnames->group_name,1)][$title] = ucwords($title2);
                                        }else{
                                            $data[substr($setgroupnames->group_name,1)][$title] = ucwords($sethis);
                                        }    
                                    }elseif($fieldnamesarray->data_type == 12 ){
                                        if(!empty($sethis)){
                                            $getpharmacyname = "SELECT  name FROM pharmacies WHERE id = $sethis";
                                            $stmt6 = $db->prepare($getpharmacyname) ;
                                            $stmt6->execute();                       
                                            $setpharmacyname  = $stmt6->fetchAll(PDO::FETCH_OBJ);
                                            $pharmacyname = $setpharmacyname[0]->name;
                                            $data[substr($setgroupnames->group_name,1)][$title] = ucwords($pharmacyname);
                                        }else{
                                            $data[substr($setgroupnames->group_name,1)][$title] = ucwords($sethis);
                                        }    
                                    }elseif($fieldnamesarray->data_type == 37 ){
                                        if(!empty($sethis)){
                                            $getinsurancename = "SELECT  name FROM insurance_companies WHERE id = $sethis";
                                            $stmt6 = $db->prepare($getinsurancename) ;
                                            $stmt6->execute();                       
                                            $setinsurancename  = $stmt6->fetchAll(PDO::FETCH_OBJ);
                                            $insurancename = $setinsurancename[0]->name;
                                            $data[substr($setgroupnames->group_name,1)][$title] = ucwords($insurancename);
                                        }else{
                                            $data[substr($setgroupnames->group_name,1)][$title] = ucwords($sethis);
                                        }    
                                    }elseif($fieldnamesarray->data_type == 35 ){
                                        if(!empty($sethis)){
                                            $getfacilityname = "SELECT  name FROM facility WHERE id = $sethis";
                                            $stmt6 = $db->prepare($getfacilityname) ;
                                            $stmt6->execute();                       
                                            $setfacilityname  = $stmt6->fetchAll(PDO::FETCH_OBJ);
                                            $facilityname = $setfacilityname[0]->name;
                                            $data[substr($setgroupnames->group_name,1)][$title] = ucwords($facilityname);
                                        }else{
                                            $data[substr($setgroupnames->group_name,1)][$title] = ucwords($sethis);
                                        }    
                                    }else{
                                        if($title == 'fname')
                                            $data[substr($setgroupnames->group_name,1)]['First_Name'] = $sethis;
                                        elseif($title == 'mname')
                                            $data[substr($setgroupnames->group_name,1)]['Middle_Name'] = $sethis;
                                        elseif($title == 'lname')
                                            $data[substr($setgroupnames->group_name,1)]['Last_Name'] = $sethis;
                                        else{
                                            if($fieldnamesarray->data_type == 4){
                                                if($sethis == '0000-00-00 00:00:00' || $sethis == '0000-00-00')
                                                    $valueThis = $sethis;
                                                else
                                                    $valueThis  = date('Y-m-d',strtotime($sethis));
                                                $data[substr($setgroupnames->group_name,1)][$title] = $valueThis;
                                            }else{
                                                $data[substr($setgroupnames->group_name,1)][$title] = $sethis;
                                            }
                                        }
                                    } 
                                }
                            } 
                        }    
                    }   
                    $listname  = '';
                }
            }
        }
        //echo "<pre>"; print_r($data);echo "</pre>";
        $newdemo1 = encode_demo($data);  
        $newdemo['Demographics'] = check_data_available($newdemo1);
        //exit();
        if($newdemo){
           $hisres = json_encode($newdemo);
           echo $historyresult =  GibberishAES::enc($hisres, $enckey);
        }else{
            $hisres = '[{"id":"0"}]';
            echo $historyresult =  GibberishAES::enc($hisres, $enckey);
        }
    }catch(PDOException $e)
    {
        $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo $historyresult =  GibberishAES::enc($error, $enckey);
    }   
}
function getTimeSpan(){
    
    $key = 'rotcoderaclla';
    // The default key size is 256 bits.
    $old_key_size = GibberishAES::size();
    GibberishAES::size(256);
    try 
    {
    
        $dateRanges['1_day'] = '1 Day From Now';
        $dateRanges['2_day'] ='2 Days From Now';  
        $dateRanges['3_day'] ='3 Days From Now';  
        $dateRanges['4_day'] ='4 Days From Now';  
        $dateRanges['5_day'] ='5 Days From Now'; 
        $dateRanges['6_day'] ='6 Days From Now';
        $dateRanges['1_week'] ='1 Week From Now';
        $dateRanges['2_week'] ='2 Weeks From Now';
        $dateRanges['3_week'] ='3 Weeks From Now';
        $dateRanges['4_week'] ='4 Weeks From Now';
        $dateRanges['5_week'] ='5 Weeks From Now';
        $dateRanges['6_week'] ='6 Weeks From Now';
        $dateRanges['1_month'] ='1 Month From Now';  
        $dateRanges['2_month'] ='2 Months From Now'; 
        $dateRanges['3_month'] ='3 Months From Now';
        $dateRanges['4_month'] ='4 Months From Now';
        $dateRanges['5_month'] ='5 Months From Now';
        $dateRanges['6_month'] ='6 Months From Now'; 
        $dateRanges['7_month'] ='7 Months From Now';
        $dateRanges['8_month'] ='8 Months From Now';
        $dateRanges['9_month'] ='9 Months From Now'; 
        $dateRanges['1_year'] ='1 Year From Now';  
        $dateRanges['2_year'] ='2 Years From Now';

        if($dateRanges)
        {
            $data =  json_encode($dateRanges); 
            echo $datares = GibberishAES::enc($data, $key);
            
        }
        else
        {
            $data =  '[{"id":"0"}]';
            echo $datares = GibberishAES::enc($data, $key);
        }
    } 
    catch(PDOException $e) 
    {

        $error =  '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo $datares = GibberishAES::enc($error, $key);
    }
}
function finalizeEid(){
    try{
        
        $db = getConnection();
        $request = Slim::getInstance()->request();
        $apikey = 'rotcoderaclla';
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        $appres = GibberishAES::dec($request->getBody(), $apikey);
        $insertArray = json_decode($appres,TRUE);
        
        $providerId     = $insertArray['loginProviderId'];
        $encounter  = $insertArray['encounter'];
        $signeddate       = $insertArray['date'];
        $pid = $insertArray['pid'];
        
        $esign = "UPDATE form_encounter SET elec_signedby = $providerId , elec_signed_on =  '$signeddate' WHERE encounter = $encounter AND pid = $pid";
        $stmt = $db->prepare($esign) ;
        
        if($stmt->execute()){  
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
function putMedicalProblemEndDate (){
    try{
        
        $db = getConnection();
        $request = Slim::getInstance()->request();
        $apikey = 'rotcoderaclla';
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        $appres = GibberishAES::dec($request->getBody(), $apikey);
        //$appres = GibberishAES::dec('U2FsdGVkX1/L2MtsaaNEvsGXHGZayUzjAtADTKn984XhcyxlmMYCDRZqtuX+oQsx6yp88TN8wfcmOkklUKO0nReulV3POStDBNS50FiSB0a4DRpnDjNj2I4KfJ7Ps9rv', $apikey);
        $insertArray = json_decode($appres,TRUE);
        
        $Id   = $insertArray['id'];
        
        $end_date = "UPDATE lists SET enddate = NOW() WHERE id = $Id";
        $stmt = $db->prepare($end_date) ;
        
        if($stmt->execute()){  
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
function getBillingAccess($uid){
    try{
        $apikey = 'rotcoderaclla';
        $request = Slim::getInstance()->request();
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        
        $db = getConnection();
        $sql = "SELECT * FROM tbl_user_custom_attr_1to1 WHERE mobilebillingaccess = 'YES' AND userid= '$uid'";
        $stmt = $db->prepare($sql) ;
        $stmt->execute();                       

        $dataResult = $stmt->fetchAll(PDO::FETCH_OBJ);    
            
        if($dataResult)
        {   
           $datares = '[{"id":"1"}]'; 
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
function MobilePatientFilters(){
    $apikey = 'rotcoderaclla';
    // The default key size is 256 bits.
    $old_key_size = GibberishAES::size();
    GibberishAES::size(256);
    $sql = "select option_id,title from list_options WHERE list_id = 'Mobile_Query_Filters' order by seq";      

    try 
    {
        $db = getConnection();
        $stmt = $db->prepare($sql) ;
        $stmt->execute();                       

        $patientsreminders = $stmt->fetchAll(PDO::FETCH_OBJ);                        

        if($patientsreminders)
        {
            $patientres = json_encode($patientsreminders); 
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
function filitersData($pid){
    try{
        $apikey = 'rotcoderaclla';
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        $dataarray  = array();
        $db = getConnection();
        $getsql  = "SELECT openAppdate,calc_next_visit,hh_certification, ctopenAppDate, calc_next_ct, h_p, hpopenAppDate, calc_next_hp, awv_required, awvopenAppDate, calc_next_awv, sudo_required, sudoopenAppDate, calc_next_sudo, cpo, calc_next_sp, spopenAppDate, ccm, ccmopenAppDate, calc_next_ccm
            FROM patient_data
            WHERE pid ='$pid'";
        $sql_stmt = $db->prepare($getsql) ;
        $sql_stmt->execute();  
        $screen_filters_names = $sql_stmt->fetchAll(PDO::FETCH_OBJ); 
        if(!empty($screen_filters_names)){
            $now = time();
            $name = '';
            if(strtotime($screen_filters_names[0]->openAppdate) > $now){
                $name = 'openAppdate';
                $hh_certification = $screen_filters_names[0]->openAppdate;
            }else{
                $name = 'calc_next_visit';
                $hh_certification = $screen_filters_names[0]->calc_next_visit;
            }
            $sql_title = "SELECT title FROM layout_options WHERE form_id = 'DEM' AND field_id = '$name'";
            $sql_stmt2 = $db->prepare($sql_title) ;
            $sql_stmt2->execute();  
            $sql_titles = $sql_stmt2->fetchAll(PDO::FETCH_OBJ);
            if(!empty($sql_titles)){
                $dataarray[$sql_titles[0]->title] = $hh_certification;
            }
            if($screen_filters_names[0]->hh_certification == 'YES'){
                $name = '';
                $sql_tt = "SELECT title FROM layout_options WHERE form_id = 'DEM' AND field_id = 'hh_certification'";
                $sql_st = $db->prepare($sql_tt) ;
                $sql_st->execute();  
                $sql_title = $sql_st->fetchAll(PDO::FETCH_OBJ);
                if(!empty($sql_title)){
                    $dataarray[$sql_title[0]->title] = 'YES';
                }
                $now = time();
                if(strtotime($screen_filters_names[0]->ctopenAppDate) > $now){
                    $name = 'ctopenAppDate';
                    $hh_certification = $screen_filters_names[0]->ctopenAppDate;
                }else{
                    $name = 'calc_next_ct';
                    $hh_certification = $screen_filters_names[0]->calc_next_ct;
                }
                $sql_title = "SELECT title FROM layout_options WHERE form_id = 'DEM' AND field_id = '$name'";
                $sql_stmt2 = $db->prepare($sql_title) ;
                $sql_stmt2->execute();  
                $sql_titles = $sql_stmt2->fetchAll(PDO::FETCH_OBJ);
                if(!empty($sql_titles)){
                    $dataarray[$sql_titles[0]->title] = $hh_certification;
                }
            }
            if($screen_filters_names[0]->cpo == 'YES'){
                $name = '';
                $sql_tt = "SELECT title FROM layout_options WHERE form_id = 'DEM' AND field_id = 'cpo'";
                $sql_st = $db->prepare($sql_tt) ;
                $sql_st->execute();  
                $sql_title = $sql_st->fetchAll(PDO::FETCH_OBJ);
                if(!empty($sql_title)){
                    $dataarray[$sql_title[0]->title] = 'YES';
                }
                $now = time();
                if(strtotime($screen_filters_names[0]->spopenAppDate) > $now){
                    $name = 'spopenAppDate';
                    $cpo = $screen_filters_names[0]->spopenAppDate;
                }else{
                    $name = 'calc_next_sp';
                    $cpo = $screen_filters_names[0]->calc_next_sp;
                }
                $sql_title = "SELECT title FROM layout_options WHERE form_id = 'DEM' AND field_id = '$name'";
                $sql_stmt2 = $db->prepare($sql_title) ;
                $sql_stmt2->execute();  
                $sql_titles = $sql_stmt2->fetchAll(PDO::FETCH_OBJ);
                if(!empty($sql_titles)){
                    $dataarray[$sql_titles[0]->title] = $cpo;
                }
            }
            if($screen_filters_names[0]->ccm == 'YES'){
                $name = '';
                $sql_tt = "SELECT title FROM layout_options WHERE form_id = 'DEM' AND field_id = 'ccm'";
                $sql_st = $db->prepare($sql_tt) ;
                $sql_st->execute();  
                $sql_title = $sql_st->fetchAll(PDO::FETCH_OBJ);
                if(!empty($sql_title)){
                    $dataarray[$sql_title[0]->title] = 'YES';
                }
                $now = time();
                if(strtotime($screen_filters_names[0]->ccmopenAppDate) > $now){
                    $name = 'ccmopenAppDate';
                    $ccm = $screen_filters_names[0]->ccmopenAppDate;
                }else{
                    $name = 'calc_next_ccm';
                    $ccm = $screen_filters_names[0]->calc_next_ccm;
                }
                $sql_title = "SELECT title FROM layout_options WHERE form_id = 'DEM' AND field_id = '$name'";
                $sql_stmt2 = $db->prepare($sql_title) ;
                $sql_stmt2->execute();  
                $sql_titles = $sql_stmt2->fetchAll(PDO::FETCH_OBJ);
                if(!empty($sql_titles)){
                    $dataarray[$sql_titles[0]->title] = $ccm;
                }
            }
            if($screen_filters_names[0]->sudo_required == 'YES'){
                $name = '';
                $sql_tt = "SELECT title FROM layout_options WHERE form_id = 'DEM' AND field_id = 'sudo_required'";
                $sql_st = $db->prepare($sql_tt) ;
                $sql_st->execute();  
                $sql_title = $sql_st->fetchAll(PDO::FETCH_OBJ);
                if(!empty($sql_title)){
                    $dataarray[$sql_title[0]->title] = 'YES';
                }
                $now = time();
                if(strtotime($screen_filters_names[0]->sudoopenAppDate) > $now){
                    $name = 'sudoopenAppDate';
                    $sudo_required = $screen_filters_names[0]->sudoopenAppDate;
                }else{
                    $name = 'calc_next_sudo';
                    $sudo_required = $screen_filters_names[0]->calc_next_sudo;
                }
                $sql_title = "SELECT title FROM layout_options WHERE form_id = 'DEM' AND field_id = '$name'";
                $sql_stmt2 = $db->prepare($sql_title) ;
                $sql_stmt2->execute();  
                $sql_titles = $sql_stmt2->fetchAll(PDO::FETCH_OBJ);
                if(!empty($sql_titles)){
                    $dataarray[$sql_titles[0]->title] = $sudo_required;
                }
            }
            if($screen_filters_names[0]->awv_required == 'YES'){
                $name = '';
                $sql_tt = "SELECT title FROM layout_options WHERE form_id = 'DEM' AND field_id = 'awv_required'";
                $sql_st = $db->prepare($sql_tt) ;
                $sql_st->execute();  
                $sql_title = $sql_st->fetchAll(PDO::FETCH_OBJ);
                if(!empty($sql_title)){
                    $dataarray[$sql_title[0]->title] = 'YES';
                }
                $now = time();
                if(strtotime($screen_filters_names[0]->awvopenAppDate) > $now){
                    $name = 'awvopenAppDate';
                    $awv_required = $screen_filters_names[0]->awvopenAppDate;
                }else{
                    $name = 'calc_next_awv';
                    $awv_required = $screen_filters_names[0]->calc_next_awv;
                }
                $sql_title = "SELECT title FROM layout_options WHERE form_id = 'DEM' AND field_id = '$name'";
                $sql_stmt2 = $db->prepare($sql_title) ;
                $sql_stmt2->execute();  
                $sql_titles = $sql_stmt2->fetchAll(PDO::FETCH_OBJ);
                if(!empty($sql_titles)){
                    $dataarray[$sql_titles[0]->title] = $awv_required;
                }
            }
            if($screen_filters_names[0]->h_p == 'YES'){
                $name = '';
                $sql_tt = "SELECT title FROM layout_options WHERE form_id = 'DEM' AND field_id = 'h_p'";
                $sql_st = $db->prepare($sql_tt) ;
                $sql_st->execute();  
                $sql_title = $sql_st->fetchAll(PDO::FETCH_OBJ);
                if(!empty($sql_title)){
                    $dataarray[$sql_title[0]->title] = 'YES';
                }
                $now = time();
                if(strtotime($screen_filters_names[0]->hpopenAppDate) > $now){
                    $name = 'hpopenAppDate';
                    $h_p = $screen_filters_names[0]->hpopenAppDate;
                }else{
                    $name = 'calc_next_hp';
                    $h_p = $screen_filters_names[0]->calc_next_hp;
                }
                $sql_title = "SELECT title FROM layout_options WHERE form_id = 'DEM' AND field_id = '$name'";
                $sql_stmt2 = $db->prepare($sql_title) ;
                $sql_stmt2->execute();  
                $sql_titles = $sql_stmt2->fetchAll(PDO::FETCH_OBJ);
                if(!empty($sql_titles)){
                    $dataarray[$sql_titles[0]->title] = $h_p;
                }
            }
        }
//           echo "<pre>"; print_r($dataarray); echo "</pre>";
        if($dataarray)
        {   
           $datares = json_encode($dataarray); 
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
function getAuditForm($formid, $pid){
   try 
    {
        $apikey = 'rotcoderaclla';
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);

        $sql = "select audit_data from tbl_form_audit WHERE pid = '$pid' AND id = '$formid'";
        $db = getConnection(); 
        $db->query( "SET NAMES utf8"); 
        $stmt = $db->prepare($sql) ;
        $stmt->execute();                       

        $get_audit = $stmt->fetchAll(PDO::FETCH_OBJ);
        $audit_array = array();
        if(!empty($get_audit)){
            $audit_data = unserialize($get_audit[0]->audit_data); 
            $audit_array = array();
            $audit_array2 = array();
            $k = 0;
            $j = 0;
            foreach($audit_data as $key=>$value){ 
                if (strpos($key,'hidden') === false || $key == 'hiddenaudit') {
                    if(is_array($value)){
                        $string = '';
                        if(trim($audit_data['hidden'.$key]) != '' )
                            $string = "(".trim($audit_data['hidden'.$key]).")";
//                        $audit_array2[] = substr($key,1).$string;
                        $new_key_check = '';
                        $i=0;
                        if(substr($key,1) == 'History' && $k == 0){
                            $audit_array['History']['Chief Complaint'] = $audit_data['Audit_CC_Optionstextarea'];
                            $k = 1;
                        }
                        if(isset($audit_data[$key]['Audit_CC_Optionsradio'])){
                            if(substr($key,1) == 'History' && $j == 0 && $audit_data[$key]['Audit_CC_Optionsradio'] != 0){
                                $str2 = '';
                                if($audit_data[$key]['Audit_CC_Optionsradio'] == 3)
                                    $str2 = "More than ";
                                $audit_array['History']['Chronic Conditions'] = $str2.$audit_data[$key]['Audit_CC_Optionsradio'];
                                $k = 1;
                            } 
                        }
                        foreach($value as $array_key => $array_value){
                            if(strpos($array_key,'Diagnosis_Management_Options') === false){
                                $new_key = str_replace($array_value, '', $array_key);
                                $hidden_value = '';
                                
                                $get_fields = "SELECT field_id, title FROM layout_options WHERE list_id ='$new_key' and form_id = 'AUDITFORM' and group_name='$key'";
                                $db->query( "SET NAMES utf8"); 
                                $stmt_fields = $db->prepare($get_fields) ;
                                $stmt_fields->execute();                       
                                $set_field_names = $stmt_fields->fetchAll(PDO::FETCH_OBJ); 
                                if(!empty($set_field_names)){
                                    $hiddenlabel = $set_field_names[0]->field_id;
                                    $hidden_value = $audit_data['hidden'.$hiddenlabel];
                                }
//                                $audit_array[$key] = $hidden_value;
                                $get_key_name = "SELECT title FROM list_options WHERE option_id='$array_value' AND list_id='$new_key'";
                                $db->query( "SET NAMES utf8"); 
                                $stmt_key = $db->prepare($get_key_name) ;
                                $stmt_key->execute();                       
                                $set_key_name = $stmt_key->fetchAll(PDO::FETCH_OBJ); 
                                
                                if(!empty($set_key_name)){
                                    $audit_array[substr($key,1)][$set_field_names[0]->title]['selected'][] = $set_key_name[0]->title;
                                    $audit_array[substr($key,1)][$set_field_names[0]->title]['value'] = $hidden_value;
                                    $audit_array[substr($key,1)]['value'] = $audit_data['hidden'.$key];
                                }
                            }else{
                                $new_value = str_replace('Diagnosis_Management_Options', '', $array_key);
                                $get_key_name2 = "SELECT title FROM list_options WHERE option_id='$new_value' AND list_id='Diagnosis_Management_Options'";
                                $db->query( "SET NAMES utf8"); 
                                $stmt_key2 = $db->prepare($get_key_name2) ;
                                $stmt_key2->execute();                       
                                $set_key_name2 = $stmt_key2->fetchAll(PDO::FETCH_OBJ); 
                                if(!empty($set_key_name2)){
                                    foreach ($set_key_name2  as $key2 => $value2){
                                        $audit_array['Decision']['Diagnosis_Management_Options']['selected'][] = $set_key_name2[0]->title;
                                        $audit_array['Decision']['Diagnosis_Management_Options']['value'] = $audit_data['hiddend_dmo'];
                                    }
                                }    
                            }
//                            $audit_array[$key] = $hidden_value;
                        }
                        $audit_array2[] = $audit_array;
                    }else{
                        $key_name = '';
                        if($key == 'cpt_data')
                            $key_name = 'Visit Category';
                        else if($key == 'audit_time')
                            $key_name = ' Time';
                        else if($key == 'hiddenaudit')
                            $key_name = 'CPT Code';
                        else if(strpos($key, 'ic',0) !== false || strpos($key, 'it',0) !== false || strpos($key, 'history_unobtainable_radio',0) !== false || strpos($key, 'history_unobtainable_textarea',0) !== false || strpos($key, 'Audit_CC_Optionstextarea',0) !== false)
                             $key_name = 1;
                        if($key_name != 1 && !empty($value))
                            $audit_array[$key_name] = str_replace("CPT Code:","",$value);
                    }
                }
            }
        }
//        echo "<pre>";print_r($audit_array); echo "</pre>";
        if($audit_array)
        {
            $patientres = json_encode($audit_array); 
            echo $patientresult = GibberishAES::enc($patientres, $apikey);
        }
        else
        {
            $patientres = '{"id":"0"}';
            echo $patientresult = GibberishAES::enc($patientres, $apikey);
        }
    } 
    catch(PDOException $e) 
    {
        $patientres = '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo $patientresult = GibberishAES::enc($patientres, $apikey);
    }
}
function getCPO($formid, $pid,$encounter){
    try{
        $apikey = 'rotcoderaclla';
        $request = Slim::getInstance()->request();
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        
        $db = getConnection();
        $sql = "SELECT * FROM tbl_form_cpo WHERE id = '$formid' AND pid= '$pid'";
        $stmt = $db->prepare($sql) ;
        $stmt->execute();                       

        $dataResult = $stmt->fetchAll(PDO::FETCH_OBJ);    
        $dataarray = array();
        if(!empty($dataResult)){
            $dataarray['id'] = $dataResult[0]->id;
            $dataarray['pid'] = $dataResult[0]->pid;
            $dataarray['user'] = $dataResult[0]->user;
            
            $get_CPO_data = $dataResult[0]->cpo_data;
            $unseralized_cpo = unserialize($get_CPO_data);
            $cpo_data = array();
            for($i=0; $i<count($unseralized_cpo); $i++){
                $cpo_type= '';

                $cpotypes = "SELECT option_id,title FROM list_options WHERE list_id = 'CPO_types'" ;
                $db->query( "SET NAMES utf8"); 
                $stmtcpos = $db->prepare($cpotypes);
                $stmtcpos->execute();
                $setcpos = $stmtcpos->fetchAll(PDO::FETCH_OBJ);
                $cpo_types= array();
                if(!empty($setcpos)){
                    for($j= 0; $j< count($setcpos); $j++){
                        $cpo_types[$setcpos[$j]-> option_id] = $setcpos[$j]-> title;
                    }
                }    
                $cpo_data[$i]['cpotype']['type'] = 'List box';
                $cpo_data[$i]['cpotype']['label'] = 'Type of Oversight';
                $cpo_data[$i]['cpotype']['value'] = $unseralized_cpo[0]['cpotype'];
                $cpo_data[$i]['cpotype']['options'] = $cpo_types;
                $cpo_data[$i]['start_date']['type'] = 'Date Field';
                $cpo_data[$i]['start_date']['label'] = 'Date';
                $cpo_data[$i]['start_date']['value'] = $unseralized_cpo[0]['start_date'];
                
                $get_timeinterval = "SELECT option_id,title FROM list_options WHERE list_id = 'Time_Interval'" ;
                $db->query( "SET NAMES utf8"); 
                $stmtime = $db->prepare($get_timeinterval);
                $stmtime->execute();
                $settimein = $stmtime->fetchAll(PDO::FETCH_OBJ);
                $time_interval= array();
                if(!empty($settimein)){
                    for($k= 0; $k<count($settimein); $k++){
                        $cpo_time_interval[$settimein[$k]->option_id] = $settimein[$k]->title;
                    }
                }

                $cpo_data[$i]['timeinterval']['type'] = 'List box';
                $cpo_data[$i]['timeinterval']['label'] = 'Minutes';
                $cpo_data[$i]['timeinterval']['value'] = $unseralized_cpo[0]['timeinterval'];
                $cpo_data[$i]['timeinterval']['options'] = $cpo_time_interval;
                $user_name= '';
                
                $getusers = "SELECT id,CONCAT(fname,' ', lname) as name FROM users WHERE username<> '' AND active = 1";
                $db->query( "SET NAMES utf8"); 
                $stmtuser = $db->prepare($getusers);
                $stmtuser->execute();
                $setuser = $stmtuser->fetchAll(PDO::FETCH_OBJ);
                $user_name = array();
                if(!empty($setuser)){
                    for($l= 0; $l< count($setuser); $l++){
                        $user_name[$setuser[$l]->id] = $setuser[$l]->name;
                    }
                }
                
                $cpo_data[$i]['users']['type'] = 'List box';
                $cpo_data[$i]['users']['label'] = 'Users';
                $cpo_data[$i]['users']['value'] = $unseralized_cpo[0]['users'];
                $cpo_data[$i]['users']['options'] = $user_name;
                $cpo_data[$i]['location']['type'] = 'Textarea';
                $cpo_data[$i]['location']['label'] = 'Location';
                $cpo_data[$i]['location']['value'] = $unseralized_cpo[0]['location'];
                $cpo_data[$i]['description']['type'] = 'Textarea';
                $cpo_data[$i]['description']['label'] = 'Description';
                $cpo_data[$i]['description']['value'] = $unseralized_cpo[0]['description'];
                $cpo_data[$i]['reference']['type'] = 'Textarea';
                $cpo_data[$i]['reference']['label'] = 'Reference';
                $cpo_data[$i]['reference']['value'] = $unseralized_cpo[0]['reference'];
               
            }
            
            $dataarray['cpo_data'] = $cpo_data;
            $getproviders = "SELECT id,CONCAT(fname,' ', lname) as name FROM users WHERE username<> '' AND active = 1 and authorized = 1";
            $db->query( "SET NAMES utf8"); 
            $stmtprovider = $db->prepare($getproviders);
            $stmtprovider->execute();
            $setprovider = $stmtprovider->fetchAll(PDO::FETCH_OBJ);
            $providers = array();
            if(!empty($setprovider)){
                for($l= 0; $l< count($setprovider); $l++){
                    $providers[$setprovider[$l]->id] = $setprovider[$l]->name;
                }
            }
            $provider_id['type'] = 'List box';
            $provider_id['label'] = 'NP/Physician Signature';
            $provider_id['options'] = $providers;
            $provider_id['value'] = $dataResult[0]->provider_id;
            
            $dataarray['provider_id'] = $provider_id;
            $dataarray['count'] = $dataResult[0]->count;
            $signed_date['type'] = 'Date Field';
            $signed_date['label'] = 'NP/Physician Signature Date';
            $signed_date['value'] = $dataResult[0]->signed_date;
            $dataarray['signed_date'] = $signed_date;
            $sqla = "SELECT form_id FROM forms where deleted = 0 and formdir = 'LBF2' and form_name = 'Allcare Encounter Forms' and encounter = $encounter order by id asc limit 0,1";
            $stmta = $db->prepare($sqla);
            $stmta->execute();  
            $datachecka = $stmta->fetchAll(PDO::FETCH_OBJ);
            if(!empty($datachecka)):
                $form_id = $datachecka[0]->form_id;
            else:
                $form_id = 0;
            endif;    
            if($form_id != 0){
                $cpo  = "SELECT field_value FROM lbf_data WHERE form_id = $form_id AND field_id = 'cpo_stat'";
                $stmta = $db->prepare($cpo);
                $stmta->execute();
                $set_status = $stmta->fetchAll(PDO::FETCH_OBJ);

                if(!empty($set_status)){
                    if($set_status[0]->field_value == 'pending')
                        $dataarray['status'] = 'pending';
                    elseif($set_status[0]->field_value == 'finalized')
                        $dataarray['status'] = 'finalized';
                    elseif($set_status[0]->field_value == 'finalized|pending' || $set_status[0]->field_value == 'pending|finalized')
                        $dataarray['status'] = 'finalized';
                    else
                        $dataarray['status'] = '';
                }else{
                    if(!empty($datachecka[0]->field_value))
                        $dataarray['status'] = $datachecka[0]->field_value;
                    else
                        $dataarray['status'] = '';
                }
            }else{
                    $dataarray['status'] = '';
            }
            
        }
//        echo "<pre>"    ; print_r($dataarray); echo "</pre>"    ;
        if($dataResult)
        {   
           $datares = json_encode($dataarray); 
           echo $patientresult = GibberishAES::enc($datares, $apikey);
        }
        else
        {
            $datares = '{"id":"0"}';   
            echo $patientresult = GibberishAES::enc($datares, $apikey);
        }
    }catch(PDOException $e){
        $insertquery = '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo GibberishAES::enc($insertquery, $apikey);
    }
}
function getCCM($formid, $pid,$encounter){
    try{
        $apikey = 'rotcoderaclla';
        $request = Slim::getInstance()->request();
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        
        $db = getConnection();
        $sql = "SELECT * FROM tbl_form_ccm WHERE id = '$formid' AND pid= '$pid'";
        $stmt = $db->prepare($sql) ;
        $stmt->execute();                       

        $dataResult = $stmt->fetchAll(PDO::FETCH_OBJ);    
        $dataarray = array();
        if(!empty($dataResult)){
            $dataarray['id'] = $dataResult[0]->id;
            $dataarray['pid'] = $dataResult[0]->pid;
            $dataarray['user'] = $dataResult[0]->user;
            
            $get_CCM_data = $dataResult[0]->ccm_data;
            $unseralized_ccm = unserialize($get_CCM_data);
            $ccm_data = array();
            for($i=0; $i<count($unseralized_ccm); $i++){
                $ccm_type= '';

                $ccmtypes = "SELECT option_id,title FROM list_options WHERE list_id = 'CCM_types'" ;
                $db->query( "SET NAMES utf8"); 
                $stmtccms = $db->prepare($ccmtypes);
                $stmtccms->execute();
                $setccms = $stmtccms->fetchAll(PDO::FETCH_OBJ);
                $ccm_types = array();
                if(!empty($setccms)){
                    for($j= 0; $j< count($setccms); $j++){
                        $ccm_types[$setccms[$j]-> option_id] = $setccms[$j]-> title;
                    }
                }    
                $ccm_data[$i]['ccmtype']['type'] = 'List box';
                $ccm_data[$i]['ccmtype']['label'] = 'Type of CCM Interaction';
                $ccm_data[$i]['ccmtype']['value'] = $unseralized_ccm[0]['ccmtype'];
                $ccm_data[$i]['ccmtype']['options'] = $ccm_types;
                $ccm_data[$i]['start_date']['type'] = 'Date Field';
                $ccm_data[$i]['start_date']['label'] = 'Date';
                $ccm_data[$i]['start_date']['value'] = $unseralized_ccm[0]['start_date'];
                
                $get_timeinterval = "SELECT option_id,title FROM list_options WHERE list_id = 'Time_Interval'" ;
                $db->query( "SET NAMES utf8"); 
                $stmtime = $db->prepare($get_timeinterval);
                $stmtime->execute();
                $settimein = $stmtime->fetchAll(PDO::FETCH_OBJ);
                $time_interval= array();
                if(!empty($settimein)){
                    for($k= 0; $k<count($settimein); $k++){
                        $ccm_time_interval[$settimein[$k]->option_id] = $settimein[$k]->title;
                    }
                }

                $ccm_data[$i]['timeinterval']['type'] = 'List box';
                $ccm_data[$i]['timeinterval']['label'] = 'Minutes';
                $ccm_data[$i]['timeinterval']['value'] = $unseralized_ccm[0]['timeinterval'];
                $ccm_data[$i]['timeinterval']['options'] = $ccm_time_interval;
                $user_name= '';
                
                $getusers = "SELECT id,CONCAT(fname,' ', lname) as name FROM users WHERE username<> '' AND active = 1";
                $db->query( "SET NAMES utf8"); 
                $stmtuser = $db->prepare($getusers);
                $stmtuser->execute();
                $setuser = $stmtuser->fetchAll(PDO::FETCH_OBJ);
                $user_name = array();
                if(!empty($setuser)){
                    for($l= 0; $l< count($setuser); $l++){
                        $user_name[$setuser[$l]->id] = $setuser[$l]->name;
                    }
                }
                
                $ccm_data[$i]['users']['type'] = 'List box';
                $ccm_data[$i]['users']['label'] = 'Users';
                $ccm_data[$i]['users']['value'] = $unseralized_ccm[0]['users'];
                $ccm_data[$i]['users']['options'] = $user_name;
                $ccm_data[$i]['location']['type'] = 'Textarea';
                $ccm_data[$i]['location']['label'] = 'Location';
                $ccm_data[$i]['location']['value'] = $unseralized_ccm[0]['location'];
                $ccm_data[$i]['description']['type'] = 'Textarea';
                $ccm_data[$i]['description']['label'] = 'Description';
                $ccm_data[$i]['description']['value'] = $unseralized_ccm[0]['description'];
                $ccm_data[$i]['reference']['type'] = 'Textarea';
                $ccm_data[$i]['reference']['label'] = 'Reference';
                $ccm_data[$i]['reference']['value'] = $unseralized_ccm[0]['reference'];
               
            }
            
            $dataarray['ccm_data'] = $ccm_data;
            $getproviders = "SELECT id,CONCAT(fname,' ', lname) as name FROM users WHERE username<> '' AND active = 1 and authorized = 1";
            $db->query( "SET NAMES utf8"); 
            $stmtprovider = $db->prepare($getproviders);
            $stmtprovider->execute();
            $setprovider = $stmtprovider->fetchAll(PDO::FETCH_OBJ);
            $providers = array();
            if(!empty($setprovider)){
                for($l= 0; $l< count($setprovider); $l++){
                    $providers[$setprovider[$l]->id] = $setprovider[$l]->name;
                }
            }
            $provider_id['type'] = 'List box';
            $provider_id['label'] = 'NP/Physician Signature';
            $provider_id['options'] = $providers;
            $provider_id['value'] = $dataResult[0]->provider_id;
            
            $dataarray['provider_id'] = $provider_id;
            $dataarray['count'] = $dataResult[0]->count;
            $signed_date['type'] = 'Date Field';
            $signed_date['label'] = 'NP/Physician Signature Date';
            $signed_date['value'] = $dataResult[0]->signed_date;
            $dataarray['signed_date'] = $signed_date;
            
            $sqla = "SELECT form_id FROM forms where deleted = 0 and formdir = 'LBF2' and form_name = 'Allcare Encounter Forms' and encounter = $encounter order by id asc limit 0,1";
            $stmta = $db->prepare($sqla);
            $stmta->execute();  
            $datachecka = $stmta->fetchAll(PDO::FETCH_OBJ);
            if(!empty($datachecka)):
                $form_id = $datachecka[0]->form_id;
            else:
                $form_id = 0;
            endif;    
            if($form_id != 0){
                $cpo  = "SELECT field_value FROM lbf_data WHERE form_id = $form_id AND field_id = 'cpo_stat'";
                $stmta = $db->prepare($cpo);
                $stmta->execute();
                $set_status = $stmta->fetchAll(PDO::FETCH_OBJ);

                if(!empty($set_status)){
                    if($set_status[0]->field_value == 'pending')
                        $dataarray['status'] = 'pending';
                    elseif($set_status[0]->field_value == 'finalized')
                        $dataarray['status'] = 'finalized';
                    elseif($set_status[0]->field_value == 'finalized|pending' || $set_status[0]->field_value == 'pending|finalized')
                        $dataarray['status'] = 'finalized';
                    else
                        $dataarray['status'] = '';
                }else{
                    if(!empty($datachecka[0]->field_value))
                        $dataarray['status'] = $datachecka[0]->field_value;
                    else
                        $dataarray['status'] = '';
                }
            }else{
                $dataarray['status'] = '';
            }
        }
        
//        echo "<pre>"    ; print_r($dataarray); echo "</pre>"    ;
        if($dataResult)
        {   
           $datares = json_encode($dataarray); 
           echo $patientresult = GibberishAES::enc($datares, $apikey);
        }
        else
        {
            $datares = '{"id":"0"}';   
            echo $patientresult = GibberishAES::enc($datares, $apikey);
        }
    }catch(PDOException $e){
        $insertquery = '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo GibberishAES::enc($insertquery, $apikey);
    }
}
function saveCPO(){
     try{
        $apikey = 'rotcoderaclla';
        $db = getConnection();
        $request = Slim::getInstance()->request();
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        $fileresult =  GibberishAES::dec($request->getBody(), $apikey);
        $filesArray = json_decode($fileresult,TRUE);
//        echo "<pre>"; print_r($filesArray); echo "</pre>";

        $id            = $filesArray['id'];
        $pid           = $filesArray['pid'];
        $encounter     = $filesArray['encounter'];
        $user          = $filesArray['user'];
        $user_id       = $filesArray['user_id'];
        $cpo_data      = $filesArray['cpo_data'];
        $provider_id   = $filesArray['provider_id'];
        $authorized    = 1;
        $activity      = 1;
        $authProvider  = 'Default';
        $count         = $filesArray['count'];
        $signed_date   = $filesArray['signed_date'];
        $status        = $filesArray['status'];
        if($id == 0){
            $new_sql = "select max(form_id)as new_form from forms where form_name='CPO' AND formdir='cpo'";
            $db->query( "SET NAMES utf8"); 
            $new_stmt = $db->prepare($new_sql);
            $new_stmt->execute();
            $new_res = $new_stmt->fetchAll(PDO::FETCH_OBJ);
            $new_fid = $new_res[0]->new_form;
            $copy_to_id = $new_fid + 1;
            
            $form_ins = "INSERT INTO  forms (date,encounter,form_name,form_id,pid,user,groupname,authorized,deleted,formdir) VALUES (NOW(),$encounter,'CPO',$copy_to_id,'".$pid."','$provider_id','default',1,0,'cpo')";
            $db->query( "SET NAMES utf8"); 
            $form_insstmt = $db->prepare($form_ins);
            $form_insstmt->execute(); 
            
            $save_cpo = "INSERT INTO `tbl_form_cpo`( `date`, `pid`, `user`, `cpo_data`, `provider_id`, `authorized`, `activity`,`authProvider`, `count`, `signed_date`) VALUES (NOW(),$pid,'$user','$cpo_data',$provider_id,'$authorized','$activity','$authProvider','$count','$signed_date')";
        }else{
            $save_cpo = "UPDATE `tbl_form_cpo` SET date = NOW(),user ='$user', cpo_data = '$cpo_data', provider_id = '$provider_id' , signed_date = '$signed_date', count= '$count'  WHERE id = $id and pid = $pid ";
                    
        }
        $db->query( "SET NAMES utf8"); 
        $get_cpo = $db->prepare($save_cpo) ;
        
        $getformid1 = "SELECT form_id FROM forms WHERE formdir= 'LBF2' and deleted = 0 and encounter = '$encounter' and pid = '$pid' order by date asc";
        $stmt31 = $db->prepare($getformid1) ;
        $stmt31->execute();
        $formidval2 = $stmt31->fetchAll(PDO::FETCH_OBJ);

        if(!empty($formidval2)){
             $newformid2 =  $formidval2[0]->form_id;
        }else{
            $lastformid2 = "SELECT MAX(form_id) as forms FROM lbf_data";
            $stmt5 = $db->prepare($lastformid2) ;
            $stmt5->execute();
            $maxformidval2 = $stmt5->fetchAll(PDO::FETCH_OBJ);
            $maxformid2 =  $maxformidval2[0]->forms + 1;
            
            $insertform2 = "INSERT INTO forms (date,encounter, form_name, form_id, pid, user, groupname, authorized, deleted, formdir) VALUES (NOW(), $encounter, 'Allcare Encounter Forms', $maxformid2, $pid, $user_id, 'Default', $authorized, 0, 'LBF2' )";
            $stmt4 = $db->prepare($insertform2);
            $stmt4->execute();
            $newformid2 = $db->lastInsertId();
            
        }
        if (count($db->query("SELECT * FROM lbf_data WHERE form_id = $newformid2 AND field_id = 'cpo_stat'")->fetchAll())==0) {
            $sql5 = "INSERT into lbf_data (form_id, field_id, field_value) VALUES ($newformid2,'cpo_stat','$status')";
        } else {
           $sql5 = "UPDATE lbf_data SET field_value = '$status' WHERE field_id ='cpo_stat'  AND form_id = $newformid2";
        }
        $stmt41 = $db->prepare($sql5);
        $stmt41->execute();
        
        if($get_cpo->execute())
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
function saveCCM(){
     try{
        $apikey = 'rotcoderaclla';
        $db = getConnection();
        $request = Slim::getInstance()->request();
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        $fileresult =  GibberishAES::dec($request->getBody(), $apikey);
        $filesArray = json_decode($fileresult,TRUE);
//        echo "<pre>"; print_r($filesArray); echo "</pre>";

        $id            = $filesArray['id'];
        $pid           = $filesArray['pid'];
        $encounter     = $filesArray['encounter'];
        $user          = $filesArray['user'];
        $user_id       = $filesArray['user_id'];
        $ccm_data      = $filesArray['ccm_data'];
        $provider_id   = $filesArray['provider_id'];
        $authorized    = 1;
        $activity      = 1;
        $authProvider  = 'Default';
        $count         = $filesArray['count'];
        $signed_date   = $filesArray['signed_date'];
        $status        = $filesArray['status'];
        if($id == 0){
            $new_sql = "select max(form_id)as new_form from forms where form_name='CCM' AND formdir='ccm'";
            $db->query( "SET NAMES utf8"); 
            $new_stmt = $db->prepare($new_sql);
            $new_stmt->execute();
            $new_res = $new_stmt->fetchAll(PDO::FETCH_OBJ);
            $new_fid = $new_res[0]->new_form;
            $copy_to_id = $new_fid + 1;
            
            $form_ins = "INSERT INTO  forms (date,encounter,form_name,form_id,pid,user,groupname,authorized,deleted,formdir) VALUES (NOW(),$encounter,'CCM',$copy_to_id,'".$pid."','$provider_id','default',1,0,'ccm')";
            $db->query( "SET NAMES utf8"); 
            $form_insstmt = $db->prepare($form_ins);
            $form_insstmt->execute(); 
            
            $save_ccm = "INSERT INTO `tbl_form_ccm`( `date`, `pid`, `user`, `ccm_data`, `provider_id`, `authorized`, `activity`,`authProvider`, `count`, `signed_date`) VALUES (NOW(),$pid,'$user','$ccm_data',$provider_id,'$authorized','$activity','$authProvider','$count','$signed_date')";
        }else{
            $save_ccm = "UPDATE `tbl_form_ccm` SET date = NOW(),user ='$user', ccm_data = '$ccm_data', provider_id = '$provider_id' , signed_date = '$signed_date', count= '$count'  WHERE id = $id and pid = $pid ";
                    
        }
        $db->query( "SET NAMES utf8"); 
        $get_ccm = $db->prepare($save_ccm) ;
        $datacheck = 0; 
        if($get_ccm->execute())
            $datacheck = 1;
        $getformid1 = "SELECT form_id FROM forms WHERE formdir= 'LBF2' and deleted = 0 and encounter = '$encounter' and pid = '$pid' order by date asc";
        $stmt31 = $db->prepare($getformid1) ;
        $stmt31->execute();
        $formidval2 = $stmt31->fetchAll(PDO::FETCH_OBJ);

        if(!empty($formidval2)){
             $newformid2 =  $formidval2[0]->form_id;
        }else{
            $lastformid2 = "SELECT MAX(form_id) as forms FROM lbf_data";
            $stmt5 = $db->prepare($lastformid2) ;
            $stmt5->execute();
            $maxformidval2 = $stmt5->fetchAll(PDO::FETCH_OBJ);
            $maxformid2 =  $maxformidval2[0]->forms + 1;
            
            $insertform2 = "INSERT INTO forms (date,encounter, form_name, form_id, pid, user, groupname, authorized, deleted, formdir) VALUES (NOW(), $encounter, 'Allcare Encounter Forms', $maxformid2, $pid, $user_id, 'Default', $authorized, 0, 'LBF2' )";
            $stmt4 = $db->prepare($insertform2);
            $stmt4->execute();
            $newformid2 = $db->lastInsertId();
            
        }
        if (count($db->query("SELECT * FROM lbf_data WHERE form_id = $newformid2 AND field_id = 'ccm_stat'")->fetchAll())==0) {
            $sql5 = "INSERT into lbf_data (form_id, field_id, field_value) VALUES ($newformid2,'ccm_stat','$status')";
        } else {
           $sql5 = "UPDATE lbf_data SET field_value = '$status' WHERE field_id ='ccm_stat'  AND form_id = $newformid2";
        }
        $stmt41 = $db->prepare($sql5);
        $datacheck2 = 0; 
        if($stmt41->execute())
            $datacheck2 = 1;
        if($datacheck2 == 1 && $datacheck == 1)
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
/* ============ Hema methods end here ================== */

/* ============ bhavya methods start here ================== */

// Retrive patient Reminders based on patientid
function getPatientReminders($pid)
{
        $key = 'rotcoderaclla';
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
	$sql = "select * from patient_reminders WHERE pid = :pid";      
        
        try 
        {
            $db = getConnection();
            $stmt = $db->prepare($sql) ;
            $stmt->bindParam("pid", $pid);            
            $stmt->execute();                       
             
            $patientsreminders = $stmt->fetchAll(PDO::FETCH_OBJ);                        
            
            if($patientsreminders)
            {
                //returns patients 
                $patientres = json_encode($patientsreminders); 
                echo $patientresult = GibberishAES::enc($patientres, $key);
            }
            else
            {
                //echo 'No patients available';
                echo '[{"id":"0"}]';
            }
        } 
        catch(PDOException $e) 
        {
            
            echo '{"error":{"text":'. $e->getMessage() .'}}'; 
        }
}

//Retrive clinicalReminders based on patientid
function getClinicalRem($pid)
{
        $key = 'rotcoderaclla';
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
	$sql = "select * from history_data WHERE pid=:pid";      
        
        try 
        {
            $db = getConnection();
            $stmt = $db->prepare($sql) ;
            $stmt->bindParam("pid", $pid);            
            $stmt->execute();                       
             
            $clinicalreminders = $stmt->fetchAll(PDO::FETCH_OBJ);                        
            
            if($clinicalreminders)
            {
                //returns patients 
                $clinicalres = json_encode($clinicalreminders); 
                echo $clinicalresult = GibberishAES::enc($clinicalres, $key);
            }
            else
            {
                //echo 'No patients available';
                echo '[{"id":"0"}]';
            }
        } 
        catch(PDOException $e) 
        {
            
            echo '{"error":{"text":'. $e->getMessage() .'}}'; 
        }
}

//To get cpt4 codes 
function getCPTCodes($encounter)
{
    
    
	$sql = "SELECT cg.code_groups, fs.fs_option AS code_option, fs.fs_codes AS codes
                    FROM  `form_encounter` fe
                    INNER JOIN openemr_postcalendar_events oe ON oe.pc_eventDate = fe.date
                    AND oe.pc_catid = fe.pc_catid
                    INNER JOIN tbl_allcare_vistcat_codegrp cg ON cg.facility = fe.facility_id
                    AND cg.visit_category = fe.pc_catid
                    INNER JOIN fee_sheet_options fs ON fs.fs_category = cg.code_groups
                    AND cg.code_options REGEXP (
                    fs.fs_option
                    )
                    WHERE fe.encounter =:encounter";      
    
        try 
        {
            $db = getConnection();
            $stmt = $db->prepare($sql) ;
            $stmt->bindParam("encounter",$encounter);            
            //$stmt->bindParam("appointment",$appointment);            
            $stmt->execute();                       
             
            $codes = $stmt->fetchAll(PDO::FETCH_OBJ);                        
            if($codes)
            {
                //returns cpt4 codes
               echo json_encode($codes);
             
            }
            else
            {
                //echo 'No cpt4 codes available';
                echo '[{"id":"0"}]';
            }
        } 
        catch(PDOException $e) 
        {
            
            echo '{"error":{"text":'. $e->getMessage() .'}}'; 
        }
}

//To get ICD9 codes 
function getICDCodes($encounter)
{
    
    
	 $sql = "SELECT 
                    CASE 
                    WHEN li.enddate IS NULL 
                    THEN  'Active'
                    ELSE  'Inactive'
                    END AS isIssueActive , li.title AS Title , li.diagnosis AS Codes, i.long_desc AS Description
                    FROM  `icd9_dx_code` i
                    INNER JOIN lists li ON li.diagnosis = CONCAT(  'ICD9:', i.formatted_dx_code ) 
                    INNER JOIN issue_encounter ie ON ie.list_id = li.id
                    INNER JOIN form_encounter fe ON fe.encounter = ie.encounter
                    WHERE ie.encounter =:encounter
                    AND li.type ='medical_problem'";      
    
        try 
        {
            $db = getConnection();
            $stmt = $db->prepare($sql) ;
            $stmt->bindParam("encounter",$encounter);            
            //$stmt->bindParam("appointment",$appointment);            
            $stmt->execute();                       
             
            $icd9codes = $stmt->fetchAll(PDO::FETCH_OBJ);                        
            if($icd9codes)
            {
                //returns cpt4 codes
              echo json_encode($icd9codes);
               //echo str_replace('CPT4|', '', json_encode($codes));
               
             
            }
            else
            {
                //echo 'No cpt4 codes available';
                echo '[{"id":"0"}]';
            }
        } 
        catch(PDOException $e) 
        {
            
            echo '{"error":{"text":'. $e->getMessage() .'}}'; 
        }
}
// method to get CPT4 and CVX codes
 
function getCodes($codetype)
{        
         $key = 'rotcoderaclla';
         // The default key size is 256 bits.
         $old_key_size = GibberishAES::size();
         GibberishAES::size(256);
              
         $cptid = "";
         if($codetype == 'CPT'):
             $cptid = 1;
         elseif($codetype == 'CVX'): 
             $cptid = 100;
         endif;
    
	 $sql = "SELECT code AS code,code_text AS Description from codes WHERE code_type=:codetype";      
    
        try 
        {
            $db = getConnection();
            $stmt = $db->prepare($sql) ;
            $stmt->bindParam("codetype",$cptid);                      
            $stmt->execute();                       
             
            $codes = $stmt->fetchAll(PDO::FETCH_OBJ);                        
            if($codes)
            {
                //returns  codes
              //echo "<pre>"; print_r($codes); echo "</pre>";         
              $result = json_encode($codes);
              echo GibberishAES::enc($result, $key);
              
            }
            else
            {
                //echo 'No cpt4 codes available';
                $result = '[{"id":"0"}]';
                echo GibberishAES::enc($result, $key);
            }
        } 
        catch(PDOException $e) 
        {
            
            $result = '{"error":{"text":'. $e->getMessage() .'}}'; 
            echo GibberishAES::enc($result, $key);
        }
}
function getFeesheetCodes($encounterid)
{
    try{
        $db = getConnection();
        $apikey = 'rotcoderaclla';
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        $new=array();
        $code_groups = array();
        $pid_sql="select pid,facility_id,pc_catid from form_encounter where encounter=$encounterid";
        $pid_stmt2 = $db->prepare($pid_sql) ;
        $pid_stmt2->bindParam("encounterid",$encounterid);
        $pid_stmt2->execute(); 
        $pid_res = $pid_stmt2->fetchAll(PDO::FETCH_OBJ);
             
        if(!empty($pid_res)){
            $pid1=$pid_res[0]->pid;
            $facility_id = $pid_res[0]->facility_id;
            $visit_category  =  $pid_res[0]->pc_catid;
        } else 
            $pid1='';
        $query = "SELECT co.code AS code, co.code_text AS Description
                        FROM codes co 
                        WHERE co.active=1 and code_text_short = ''";
//            $query = "SELECT co.code AS code, co.code_text AS Description
//                        FROM fee_sheet_options fo
//                        INNER JOIN tbl_allcare_vistcat_codegrp vc ON vc.code_groups = fo.fs_category
//                        INNER JOIN codes co ON SUBSTRING( fo.fs_codes, 6, LENGTH( fo.fs_codes ) -6 ) = co.code
//                        WHERE  `facility` =$facility_id
//                        AND  `visit_category` =$visit_category
//                        AND vc.code_options REGEXP (
//                        fo.fs_option
//                        )
//                        ";
            $stmt = $db->prepare($query) ; 
            $stmt->execute(); 
            $cpt4codes = $stmt->fetchAll(PDO::FETCH_OBJ); 
            //foreach($cpt4codes as $element):
                $query1 = "select dx_code,formatted_dx_code, short_desc from icd9_dx_code where active = 1 limit 150";
                $stmt1 = $db->prepare($query1) ; 
                $stmt1->execute(); 
                $icd9codes = $stmt1->fetchAll(PDO::FETCH_OBJ); 
            //endforeach;
            
            //foreach($cpt4codes as $element):
              
                 $sql = "SELECT DISTINCT l.id, l.title AS Title, l.diagnosis AS Codes, (select long_desc  from  `icd9_dx_code` where l.diagnosis = CONCAT(  'ICD9:', formatted_dx_code ) and active = 1)  AS Description
                        FROM lists AS l
                        LEFT JOIN issue_encounter AS ie ON ie.list_id = l.id
                        AND ie.encounter =$encounterid
                       
                        WHERE l.type =  'medical_problem'
                        AND l.pid =$pid1
                        AND (
                        (
                        l.begdate IS NULL
                        )
                        OR (
                        l.begdate IS NOT NULL 
                        AND l.begdate <= NOW( )
                        )
                        )
                        AND (
                        (
                        l.enddate IS NULL
                        )
                        OR (
                        l.enddate IS NOT NULL 
                        AND l.enddate >= NOW( )
                        )
                        )
                        ORDER BY ie.encounter DESC , l.id" ;      
                $stmt2 = $db->prepare($sql) ;
                $stmt2->execute(); 
                $medical_problems = $stmt2->fetchAll(PDO::FETCH_OBJ);     
            //endforeach;

          
             $selectquery = "SELECT b.id, b.code, b.billed, (select group_concat(justify) from billing WHERE encounter =   $encounterid and code_type='CPT4' and activity = 1) as justify, b.notecodes, b.code_text,f.provider_id as rendering_providerid, (SELECT  CONCAT( fname,  ' ', lname ) FROM users where id = f.provider_id)  AS rendering_ProviderName
                FROM billing b 
                INNER JOIN form_encounter f ON b.encounter = f.encounter  
                WHERE b.encounter =   $encounterid and code_type='CPT4' and b.activity = 1 order by b.date desc ";
            $selectstmt = $db->prepare($selectquery) ; 
            $selectstmt->execute(); 
            $selected_feesheet = $selectstmt->fetchAll(PDO::FETCH_OBJ); 
            $count = $selectstmt->rowCount();
            $billed = 0;
            $rendering_provider = '';
            if(!empty($selected_feesheet)){
                for($i=0; $i< $count; $i++){
                    $rendering_provider = $selected_feesheet[$i]->rendering_providerid ;
                    $selected_feesheet['rendering_providerid'] = $rendering_provider;
                    $selected_feesheet['rendering_ProviderName']  = $selected_feesheet[$i]->rendering_ProviderName;
                    $billed = $selected_feesheet[$i]->billed ;
                    $selected_feesheet[$i]->justify = str_replace(",","",$selected_feesheet[$i]->justify);
                    
                    $icd_code = explode(":",str_replace(",","",$selected_feesheet[$i]->justify));
                    foreach($medical_problems as $element):
                        $element->Codes = str_replace("|", ":",$element->Codes);
                        if($element->Codes == str_replace('|', ':', $icd_code[0])){
                            $element->primary = 1;
                        }else{
                            $element->primary = 0;
                        }
                        if(in_array($element->Codes, str_replace('|', ':', $icd_code)) ){
                            $element->justify = 1;
                        }else{
                            $element->justify = 0;
                        }
                        $element->Codes = str_replace(':', '|', $element->Codes);
                    
                    endforeach;
                }
                if(empty($rendering_provider)){
                    $get_providerName = "SELECT  CONCAT( u.fname,  ' ', u.lname ) AS rendering_ProviderName, p.pc_aid AS rendering_providerid

                    FROM form_encounter f 

                    INNER JOIN openemr_postcalendar_events p ON  p.pc_eventDate = DATE_FORMAT( f.date,  '%Y-%m-%d' ) and p.pc_pid = f.pid 
                    inner join users u on u.id = p.pc_aid 

                    WHERE f.encounter =$encounterid and f.pid = $pid1";
                    $selectsupervisor = $db->prepare($get_providerName) ; 
                    $selectsupervisor->execute(); 
                    $set_providerName  = $selectsupervisor->fetchAll(PDO::FETCH_OBJ);
                    if(!empty($set_providerName)){
                        $selected_feesheet['rendering_providerid'] = $set_providerName[0]->rendering_providerid;
                        $selected_feesheet['rendering_ProviderName'] = $set_providerName[0]->rendering_ProviderName;
                    }else{
                        $selected_feesheet['rendering_providerid'] = '';
                        $selected_feesheet['rendering_ProviderName'] = '';
                    }

                }
            }
            if(empty($selected_feesheet['rendering_providerid'] )){
                $get_providerName = "SELECT  CONCAT( u.fname,  ' ', u.lname ) AS rendering_ProviderName, p.pc_aid AS rendering_providerid

                FROM form_encounter f 

                INNER JOIN openemr_postcalendar_events p ON  p.pc_eventDate = DATE_FORMAT( f.date,  '%Y-%m-%d' ) and p.pc_pid = f.pid 
                inner join users u on u.id = p.pc_aid 

                WHERE f.encounter =$encounterid and f.pid = $pid1";
                $selectsupervisor = $db->prepare($get_providerName) ; 
                $selectsupervisor->execute(); 
                $set_providerName  = $selectsupervisor->fetchAll(PDO::FETCH_OBJ);
                if(!empty($set_providerName)){
                    $selected_feesheet['rendering_providerid'] = $set_providerName[0]->rendering_providerid;
                    $selected_feesheet['rendering_ProviderName'] = $set_providerName[0]->rendering_ProviderName;
                }else{
                    $selected_feesheet['rendering_providerid'] = '';
                    $selected_feesheet['rendering_ProviderName'] = '';
                }

            }
            $provider = "SELECT id, CONCAT(fname,' ',lname) as name FROM users WHERE " .
                    "( authorized = 1 OR info LIKE '%provider%' ) AND username != '' " .
                    "AND active = 1 AND ( info IS NULL OR info NOT LIKE '%Inactive%' ) " .
                    "ORDER BY lname, fname" ; 
            $selectprovider = $db->prepare($provider) ; 
            $selectprovider->execute(); 
            $providerlist = $selectprovider->fetchAll(PDO::FETCH_OBJ); 
            $selected_feesheet['provider_list'] = $providerlist;

            //justify
//            if(!empty($medical_problems)){           
//            $code1=''; $code2='';
//            foreach($medical_problems as $element):
//               $code=explode(":",$element->Codes);
//               $code1.="'$code[1]'".",";  
//               $code2=rtrim($code1,",");
//            endforeach;
//            }           
          
            
            $new['FeeSheetData'] = $selected_feesheet;
            //$new['provider_list'] = $providerlist;
            $new['CPT4']=$cpt4codes;
            $new['ICD9']=$icd9codes;
            $new['medical_problems']= $medical_problems ;
            $new['billed']= $billed ;
            //$new['Provider']=$provider;
//          echo "<pre>"; print_r($new); echo "</pre>";
           
            $newdemo4=encode_demo($new);  
            $newdemo['FEE_SHEET'] = check_data_available($newdemo4);
              
            if($newdemo4)
            {
                //returns count 
                $newdemores = json_encode($newdemo);
                echo $newdemoresult = GibberishAES::enc($newdemores, $apikey);
            }
            else
            {
               $feesheetcodes = '[{"id":"0"}]';
               echo $newdemoresult = GibberishAES::enc($feesheetcodes, $apikey);
               
            }
            
            
        } 
        catch(PDOException $e) 
        {
            
            $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
            echo $errorresult = GibberishAES::enc($error, $apikey);
           
        }
   
}

function getIssuesByEncounter($encounter,$type)
{
    $apikey = 'rotcoderaclla';
    // The default key size is 256 bits.
    $old_key_size = GibberishAES::size();
    GibberishAES::size(256);  
                    
    try{
        $db = getConnection();
        $new=array();
        $issues2 = array();
        if($type=='allergy')
        {
            $query = "SELECT 
                    CASE 
                    WHEN li.enddate IS NULL 
                    THEN  'Active'
                    ELSE  'Inactive'
                    END AS Status, li.id,li.title AS Title, li.begdate AS Begdate, li.enddate AS Enddate, li.diagnosis AS Codes, i.long_desc AS Description, 
                    li.reaction as reaction,(select title from list_options where list_id='occurrence' and option_id = li.occurrence) AS Occurrence, li.referredby AS ReferredBy,ie.encounter, li.comments,
                    (SELECT COUNT( list_id ) 
                    FROM issue_encounter
                    WHERE list_id = li.id
                    ) AS enc
                FROM  `icd9_dx_code` i
                RIGHT JOIN lists li ON li.diagnosis = CONCAT(  'ICD9:', i.formatted_dx_code ) 
                INNER JOIN issue_encounter ie ON ie.list_id = li.id
                INNER JOIN form_encounter fe ON fe.encounter = ie.encounter
                WHERE ie.encounter = $encounter
                AND li.type ='$type'";
            $db->query("SET SQL_BIG_SELECTS=1"); 
            $stmt = $db->prepare($query) ; 
            $stmt->execute(); 
//            $issues = array();
            $issues = $stmt->fetchAll(PDO::FETCH_OBJ); 
//            for($i=0; $i<count($issues2); $i++){
//                $issues = array();
//                foreach($issues2[$i] as $key1 => $issues3){
//                    if($key1 == 'Occurrence'):
//                        $issues['Occurrence']['selectedvalue'] = $issues3;
//                    else:
//                        $issues[$key1] = $issues3;
//                    endif;
//                }
//               
//            }
//            $new[] = $issues; 
        }
        else
        {
            $query = "SELECT 
                    CASE 
                    WHEN li.enddate IS NULL 
                    THEN  'Active'
                    ELSE  'Inactive'
                    END AS Status, li.id,li.title AS Title, li.begdate AS Begdate, li.enddate AS Enddate, li.diagnosis AS Codes, i.long_desc AS Description, 
                    (select title from list_options where list_id='occurrence' and option_id = li.occurrence) AS Occurrence, li.referredby AS ReferredBy,ie.encounter, li.comments,
                    (SELECT COUNT( list_id ) 
                    FROM issue_encounter
                    WHERE list_id = li.id
                    ) AS enc
                FROM  `icd9_dx_code` i
                RIGHT JOIN lists li ON li.diagnosis = CONCAT(  'ICD9:', i.formatted_dx_code ) 
                INNER JOIN issue_encounter ie ON ie.list_id = li.id
                INNER JOIN form_encounter fe ON fe.encounter = ie.encounter
                WHERE ie.encounter =$encounter
                AND li.type ='$type'";
            $db->query("SET SQL_BIG_SELECTS=1"); 
            $stmt = $db->prepare($query) ; 
            $stmt->execute(); 
            $issues = $stmt->fetchAll(PDO::FETCH_OBJ); 
            
//            for($i=0; $i<count($issues2); $i++){
//                foreach($issues2[$i] as $key1 => $issues3){
//                    if($key1 == 'Occurrence'):
//                        $issues['Occurrence']['selectedvalue'] = $issues3;
//                    else:
//                        $issues[$key1] = $issues3;
//                    endif;
//                }
//                $new[] = $issues;
//            }
//            $new[] = $issues;
        }
       
//        $query1 = "select option_id,title from list_options where list_id='occurrence'";
//        $stmt1 = $db->prepare($query1) ; 
//        $stmt1->execute(); 
//        $occlist = $stmt1->fetchAll(PDO::FETCH_OBJ); 
//        $lists=array();
//
//        foreach($occlist as $list2){
//            $lists[$list2->option_id]  = $list2->title;
//            for($i=0; $i<count($issues2); $i++)   
//            {
//                $new[$i]['Occurrence']['occurancelist']=$lists;
//            }
//        }
        //$new[] = $issues;
//        $quer = "select option_id ,title from list_options where list_id=CONCAT('$type','_issue_list')";
//        $stmts = $db->prepare($quer) ; 
//        $stmts->execute(); 
//
//        $typelists = $stmts->fetchAll(PDO::FETCH_OBJ); 
//        $list1=array();
//        foreach($typelists as $list3){
//            $lists1[$list3->option_id]  = $list3->title;
//            for($i=0; $i<count($issues2); $i++)   
//            {
//                 //$issues[$i]->typelist=$lists1;
//                $new[$i]['issuetypelist']=$lists1;
//            }
//        }  
        //$new['issues']=$issues;
//       echo "<pre>"; print_r($new); echo "</pre>";

        $newdemo4=encode_demo($issues);  
        $newdemo2['Issues'] = check_data_available($newdemo4);

        if($newdemo2)
        {
            $issuesres = json_encode($newdemo2);
            echo $newdemoresult = GibberishAES::enc($issuesres, $apikey);
            //echo $decrypted_secret_string = GibberishAES::dec($newdemoresult, $key);
        }
        else
        {
            $issuesres= '[{"id":"0"}]';
            echo $newdemoresult = GibberishAES::enc($issuesres, $apikey);
        }
    } 
    catch(PDOException $e) 
    {
         $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo $newdemoresult = GibberishAES::enc($error, $apikey);
    }
}
function getIssuesByEncounter_withOccurance($encounter,$type)
{
    $key = 'rotcoderaclla';
    // The default key size is 256 bits.
    $old_key_size = GibberishAES::size();
    GibberishAES::size(256);  
                    
    try{
        $db = getConnection();
        $new=array();
        $issues2 = array();
//        $issues = array();
        if($type=='allergy')
        {
            $query = "SELECT 
                    CASE 
                    WHEN li.enddate IS NULL 
                    THEN  'Active'
                    ELSE  'Inactive'
                    END AS Status, li.id,li.title AS Title, li.begdate AS Begin_Date, li.enddate AS End_date, li.diagnosis AS Codes, 
                    li.reaction as reaction,li.occurrence AS Occurrence, li.referredby AS Referred_By,ie.encounter, li.comments,
                    (SELECT COUNT( list_id ) 
                    FROM issue_encounter
                    WHERE list_id = li.id
                    ) AS enc,(select long_desc   FROM `icd9_dx_code` where CONCAT(  'ICD9:', formatted_dx_code ) = li.diagnosis and active = 1 ) AS Description


                FROM  lists li 
                INNER JOIN issue_encounter ie ON ie.list_id = li.id
                INNER JOIN form_encounter fe ON fe.encounter = ie.encounter
                WHERE ie.encounter = $encounter
                AND li.type ='$type'";
            $db->query("SET SQL_BIG_SELECTS=1"); 
            $stmt = $db->prepare($query) ; 
            $stmt->execute(); 
            $issues = array();
            $issues2 = $stmt->fetchAll(PDO::FETCH_OBJ); 
            for($i=0; $i<count($issues2); $i++){
                $issues = array();
                foreach($issues2[$i] as $key1 => $issues3){
                    if($key1 == 'Occurrence'):
                        $issues['Occurrence']['selectedvalue'] = $issues3;
                    else:
                        $issues[$key1] = $issues3;
                    endif;
                }
                $new[] = $issues;
            }
           // $new[] = $issues;
        }
        else
        {
            $query = "SELECT 
                    CASE 
                    WHEN li.enddate IS NULL 
                    THEN  'Active'
                    ELSE  'Inactive'
                    END AS Status, li.id,li.title AS Title, li.begdate AS Begin_Date, li.enddate AS End_date, li.diagnosis AS Codes,
                    li.occurrence AS Occurrence, li.referredby AS Referred_By,ie.encounter, li.comments,
                    (SELECT COUNT( list_id ) 
                    FROM issue_encounter
                    WHERE list_id = li.id
                    ) AS enc,(select long_desc   FROM `icd9_dx_code` where CONCAT(  'ICD9:', formatted_dx_code ) = li.diagnosis and active = 1 ) AS Description
                FROM  lists li 
                INNER JOIN issue_encounter ie ON ie.list_id = li.id
                INNER JOIN form_encounter fe ON fe.encounter = ie.encounter
                WHERE ie.encounter =$encounter
                AND li.type ='$type'";
            $db->query("SET SQL_BIG_SELECTS=1"); 
            $stmt = $db->prepare($query) ; 
            $stmt->execute(); 
            $issues2 = $stmt->fetchAll(PDO::FETCH_OBJ); 
            
            for($i=0; $i<count($issues2); $i++){
                $issues = array();
                foreach($issues2[$i] as $key1 => $issues3){
                    if($key1 == 'Occurrence'):
                        $issues['Occurrence']['selectedvalue'] = $issues3;
                    else:
                        $issues[$key1] = $issues3;
                    endif;
                }
//                if($type != 'medication')
                    $new[] = $issues;  
            }
//            if($type == 'medication')
//                $new[] = $issues;
        }
       
        $query1 = "select option_id,title from list_options where list_id='occurrence'";
        $stmt1 = $db->prepare($query1); 
        $stmt1->execute(); 
        $occlist = $stmt1->fetchAll(PDO::FETCH_OBJ); 
        $lists=array();

        foreach($occlist as $list2){
            $lists[$list2->option_id]  = $list2->title;
            for($i=0; $i<count($issues2); $i++)   
            {
                $new[$i]['Occurrence']['occurancelist']=$lists;
            }
        }
        //$new[] = $issues;
        $quer = "select option_id ,title from list_options where list_id=CONCAT('$type','_issue_list')";
        $stmts = $db->prepare($quer) ; 
        $stmts->execute(); 

        $typelists = $stmts->fetchAll(PDO::FETCH_OBJ); 
        $list1=array();
        foreach($typelists as $list3){
            $lists1[$list3->option_id]  = $list3->title;
            for($i=0; $i<count($issues2); $i++)   
            {
                 //$issues[$i]->typelist=$lists1;
                $new[$i]['issuetypelist']=$lists1;
            }
        }  
        //$new['issues']=$issues;
       //echo "<pre>"; print_r($new); echo "</pre>";

        $newdemo4=encode_demo($new);  
        $newdemo2['Issues'] = check_data_available($newdemo4);

        if($newdemo2)
        {
            echo $issuesres = json_encode($newdemo2);
            //echo $newdemoresult = GibberishAES::enc($issuesres, $key);echo"<br><br>";
            //echo $decrypted_secret_string = GibberishAES::dec($newdemoresult, $key);
        }
        else
        {
           echo $issuesres= '[{"id":"0"}]';
           //echo $newdemoresult = GibberishAES::enc($issuesres, $key);
        }
    } 
    catch(PDOException $e) 
    {
        echo $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
        //echo $newdemoresult = GibberishAES::enc($error, $key);
    }
}


function saveIssuesByEncounter(){
    try 
    {
        $db = getConnection();
        $request = Slim::getInstance()->request();
        $key = 'rotcoderaclla';
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        $appres = GibberishAES::dec($request->getBody(), $key);
    
        $insertArray = json_decode($appres,TRUE);
        $id                     = $insertArray['id'];
        $encounter                  = $insertArray['encounter'];
        $pid                        = $insertArray['pid'];
        $user                       = $insertArray['loginProviderId'];
        $authorized                 = 1; 
        $activity                   = 1 ;
        

        //$pid  = $insertArray['pid']; 
        $type  = $insertArray['type'];
        $title  = $insertArray['title'];
        //$activity  = $insertArray['activity']; 
        $comments  = $insertArray['comments'];
        $begdate  = $insertArray['begdate'];
        $enddate  = $insertArray['enddate'];
        //$returndate  = $insertArray['returndate'];
        $diagnosis  = $insertArray['diagnosis'];
        $occurrence  = $insertArray['occurrence'];
        //$classification  = $insertArray['classification'];
        $referredby   = $insertArray['referredby'];
        //$user  = $insertArray['user'];
        //$groupname  = $insertArray['groupname'];
        $outcome  = $insertArray['outcome'];
        $destination  = $insertArray['destination'];
        //$reinjury_id  = $insertArray['reinjury_id'];
        //$injury_grade  = $insertArray['injury_grade'];
        //$injury_part  = $insertArray['injury_part'];
        //$injury_type  = $insertArray['injury_type'];
        $reaction  = $insertArray['reaction'];
        
        /*$pid  = 5151; 
        $type  = 'medication';
        $title  = 'melformin3333';
        //$activity  = 1; 
        $comments  = 'comment';
        $begdate  = '2015-02-28';
        $enddate  = "NULL";
        // $returndate  = '';
        $diagnosis  = 'ICD9:E000.0';
        $occurrence  = 'first';
        // $classification  = 'classify';
        $referredby   = 'Dr ketha';
        // $user  = 'admin';
        // $groupname  = 'group';
        $outcome  = 'outcome';
        $destination  = 'destination';
        //$reinjury_id  = 'reinjury_id';
        //  $injury_grade  = 'injury_grade';
        //$injury_part  = 'injury_part';
        //  $injury_type  = 'injury_type';
        $reaction  = 'reaction';*/
        
      
        $patientres = '';
        
       if($id==0){
            
               $sql = "INSERT INTO lists ( date, pid, type, title, comments, begdate, enddate,  diagnosis, occurrence,  referredby,  outcome, destination,    reaction )"
                        . " VALUES ( NOW(), '$pid','$type','$title','$comments','$begdate', '$enddate','$diagnosis', '$occurrence',  '$referredby', '$outcome', '$destination', '$reaction' )
                         "; echo"<br>";
                    $sqlstmt = $db->prepare($sql) ;
                    $data =  $sqlstmt->execute();
               $sel="select id from lists where type='$type' AND title='$title' AND pid=$pid";
                    $sqlstmt2 = $db->prepare($sel) ;
                    $data2=  $sqlstmt2->execute();
                    $idval = $sqlstmt2->fetchAll(PDO::FETCH_OBJ);
                    $newid =  $idval[0]->id;
               $sql1="INSERT INTO issue_encounter ( pid, list_id, encounter) VALUES ( $pid, $newid, $encounterid)";
                    $sqlstmt1 = $db->prepare($sql1) ;
                    $data1 =  $sqlstmt1->execute();
                $sel="select pid,type from lists_touch where pid=$pid";
                                    $sqlstmt2 = $db->prepare($sel) ;
                                    $data2=  $sqlstmt2->execute();
                                    $idval2 = $sqlstmt2->fetchAll(PDO::FETCH_OBJ);
                                    $p_id =  $idval2[0]->pid;
                                    $list_type = $idval2[0]->type;
                            if($p_id!=$pid && $list_type!='medicalproblem')    
                            {
                               echo   $sql6="INSERT INTO lists_touch ( pid, type, date) VALUES ( $pid, 'medical_problem', NOW())";
                                    $sqlstmt6 = $db->prepare($sql6) ;
                                    $data6 =  $sqlstmt6->execute();
                            }    
                
                
        }else{
           $sql = "UPDATE lists SET date=NOW(), pid='$pid', type='$type', title='$title', comments='$comments', begdate='$begdate', enddate='$enddate', diagnosis='$diagnosis', occurrence='$occurrence',  referredby='$referredby',  outcome='$outcome', destination='$destination', reaction='$reaction'
                      WHERE  id = $id";            
               $sqlstmt = $db->prepare($sql) ;
               $data =  $sqlstmt->execute();
               $sel1="select pid,list_id ,encounter from issue_encounter where encounter=$encounterid";
               $selstmt = $db->prepare($sel1) ;
               $seldata =  $selstmt->execute();
               $val = $selstmt->fetchAll(PDO::FETCH_OBJ);
               foreach($val as $value)
               {
                   if($value->pid!=$pid && $value->list_id!=$id1 && $value->encounter!=$encounterid)  
                   {    
                   $sql2="INSERT INTO issue_encounter ( pid, list_id, encounter) VALUES ( $pid, $id1, $encounterid)";
                   $sqlstmt2 = $db->prepare($sql2) ;
                   $data2 =  $sqlstmt2->execute();
                   }
               }       
        }
                      
        if($data){
            echo $patientres = '[{"id":"1"}]';
            //echo $patientresult = GibberishAES::enc($patientres, $key);
            
        }
        else{
            echo $patientres =  '[{"id":"0"}]';
           // echo $patientresult = GibberishAES::enc($patientres, $key);
        }
    } 
    catch(PDOException $e) 
    {

       echo $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
      // echo $patientresult = GibberishAES::enc($error, $key);
    }
}
function getImmunization($patientId)
{
    try 
        {
            $db = getConnection();
            $apikey = 'rotcoderaclla';
            // The default key size is 256 bits.
            $old_key_size = GibberishAES::size();
            GibberishAES::size(256);
            $new=array();
            $immunization=array();
            $query = "select i.id as id, i.administered_date, i.immunization_id as immunization_id, i.cvx_code as cvx_code, c.code_text_short as cvx_text, i.manufacturer,i.lot_number,i.administered_by_id,(select concat(lname,' ',fname) from users where id= i.administered_by) as administered_by,
             i.education_date,i.vis_date,i.note,i.amount_administered,(select title from list_options where list_id='drug_units' and option_id =i.amount_administered_unit) as amount_administered_unit ,i.expiration_date,i.route,i.administration_site,i.added_erroneously".
             " from immunizations i ".
             " left join code_types ct on ct.ct_key = 'CVX' ".
             " left join codes c on c.code_type = ct.ct_id AND i.cvx_code = c.code ".
             " where i.patient_id = $patientId ".
             " and i.added_erroneously = 0".
             " order by i.administered_date desc";
            $db->query( "SET NAMES utf8"); 
            $stmt = $db->prepare($query) ; 
            $stmt->execute(); 
            $immunization= $stmt->fetchAll(PDO::FETCH_OBJ); 
//            for($i=0; $i<count($immunization); $i++){
//                foreach($immunization[$i] as $key => $units3){
//                    if ($key == 'administered_by_id') {
//                        $units['administered_by_id']['selectedvalue'] = $units3;
//                     } 
//                     else if($key == 'amount_administered_unit')  
//                            $units['amount_administered_unit']['selectedvalue'] = $units3;
//                     else if($key == 'route')  
//                            $units['route']['selectedvalue'] = $units3;
//                     else if($key=='administration_site')
//                        $units['administration_site']['selectedvalue'] = $units3;
//                     else
//                          $units[$key] = $units3;
//                    
//                }
//                $new[] = $units;
//            }
//              
//            $query1 = "select id, concat(lname,' ',fname) as full_name from users where fname <> '' and lname <> ''";		
//            $db->query( "SET NAMES utf8"); 
//            $stmt1 = $db->prepare($query1) ; 
//            $stmt1->execute(); 
//            $userlist = $stmt1->fetchAll(PDO::FETCH_OBJ); 
//            $lists4=array();
//              
//            foreach($userlist as $list1){
//                $lists4[$list1->id]  = $list1->full_name;
//                for($i=0; $i<count($immunization); $i++)   
//                {
//                  $new[$i]['administered_by_id']['users_list']=$lists4;
//                }
//            }
//            $query2 = "select option_id,title from list_options where list_id='drug_units'";
//            $db->query( "SET NAMES utf8"); 
//            $stmt1 = $db->prepare($query2) ; 
//            $stmt1->execute(); 
//            $druglist = $stmt1->fetchAll(PDO::FETCH_OBJ); 
//            $lists=array();
//              
//            foreach($druglist as $list2){
//                $lists[$list2->option_id]  = $list2->title;
//                for($i=0; $i<count($immunization); $i++)   
//                {
//                  $new[$i]['amount_administered_unit']['drug_units_list']=$lists;
//                }
//            }
//            $query3= "select option_id,title from list_options where list_id='drug_route'";
//            $db->query( "SET NAMES utf8"); 
//            $stmt2= $db->prepare($query3) ; 
//            $stmt2->execute(); 
//            $routelist = $stmt2->fetchAll(PDO::FETCH_OBJ); 
//            $lists1=array();
//              
//            foreach($routelist as $list3){
//                $lists1[$list3->option_id]  = $list3->title;
//                for($i=0; $i<count($immunization); $i++)   
//                {
//                  $new[$i]['route']['drug_route_list']=$lists1;
//                }
//            } 
//            $query4 = "select option_id,title from list_options where list_id='proc_body_site'";
//            $db->query( "SET NAMES utf8"); 
//            $stmt3= $db->prepare($query4) ; 
//            $stmt3->execute(); 
//            $sitelist = $stmt3->fetchAll(PDO::FETCH_OBJ); 
//            $lists2=array();
//              
//            foreach($sitelist as $list4){
//                $lists2[$list4->option_id]  = $list4->title;
//                for($i=0; $i<count($immunization); $i++)   
//                {
//                  $new[$i]['administration_site']['site_list']=$lists2;
//                }
//            }  
             //$new['immun']= $immunization;  
            
//            echo "<pre>"; print_r($immunization); echo "</pre>";
           
           
            $newdemo4=encode_demo($immunization);  
            $newdemo['IMMUNIZATION'] = check_data_available($newdemo4);
              
            if($newdemo4)
            {
                //returns count 
                $newdemores = json_encode($newdemo);
                echo $newdemoresult = GibberishAES::enc($newdemores, $apikey);
            }
            else
            {
               $immunization = '[{"id":"0"}]';
               echo $newdemoresult = GibberishAES::enc($immunization, $apikey);
               
            }
            
            
        } 
        catch(PDOException $e) 
        {
            
            $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
            echo $newdemoresult = GibberishAES::enc($error, $apikey);
           
        }
   
}

function saveImmunization(){
    try 
    {
        $db = getConnection();
        $request = Slim::getInstance()->request();
        $key = 'rotcoderaclla';
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        $appres = GibberishAES::dec($request->getBody(), $key);
        $insertArray = json_decode($appres,TRUE);
       
        $id                     = $insertArray['id'];
        $encounter                  = $insertArray['encounter'];
        $pid                        = $insertArray['pid'];
        $user                       = $insertArray['loginProviderId'];
        $authorized                 = 1; 
        $activity                   = 1 ;
        

         $cvx_code  = $insertArray['cvx_code'];
        $administered_date  = $insertArray['administered_date'];
        $amount_administered  = $insertArray['amount_administered'];
        $administered_unit  = $insertArray['amount_administered_unit'];
        $expiration_date  = $insertArray['expiration_date'];
        $manufacturer  = $insertArray['manufacturer'];
        $lot_number  = $insertArray['lot_number'];
        $administered_by_id   = $insertArray['administered_by_id'];
        $education_date  = $insertArray['education_date'];
        $vis_date  = $insertArray['vis_date'];
        $route = $insertArray['route'];
        $admin_site=$insertArray['administration_site'];
        $note=$insertArray['note'];
        $immun_id=$insertArray['immunization_id'];
        $error=$insertArray['added_erroneously'];
        
        
       /* $cvx_code  = 25;
        $administered_date  = '2015-03-25 02:15';
        $amount_administered  = 200;
        $administered_unit  = 7;
        $expiration_date  = '2015-04-03';
        $manufacturer  = '32423';
        $lot_number  = 'lot-erer324234';
        $administered_by_id   = '1';
        $education_date  = '2015-03-25';
        $vis_date  = '2015-03-28';
        $route = 6;
        $admin_site='oth';
        $note='note111111';
        $immun_id=0;
        $error=0;
        $user =1;*/
      
        $patientres = '';
        
       if($id==0){
           
           echo  $sql = "INSERT INTO immunizations (patient_id, cvx_code, administered_date, amount_administered, amount_administered_unit, expiration_date,  manufacturer, lot_number, administered_by_id,  education_date, vis_date, route,administration_site,note,immunization_id,create_date,created_by,administered_by,added_erroneously)"
                        . " VALUES ( '$pid','$cvx_code','$administered_date','$amount_administered','$administered_unit', '$expiration_date','$manufacturer', ' $lot_number',  ' $administered_by_id', '$education_date', '$vis_date', '$route' ,'$admin_site','$note','$immun_id', NOW(),$user, NULL,$error)
                         "; echo"<br>";
                    $sqlstmt = $db->prepare($sql) ;
                    $data =  $sqlstmt->execute();  
        }else{
           
          echo  $sql1 = "UPDATE immunizations SET patient_id='$pid', cvx_code='$cvx_code', administered_date='$administered_date', amount_administered='$amount_administered', amount_administered_unit='$administered_unit', expiration_date='$expiration_date', manufacturer='$manufacturer', lot_number='$lot_number',  administered_by_id='$administered_by_id',  education_date='$education_date', vis_date='$vis_date', route='$route',
               administration_site='$admin_site',note='$note',immunization_id='$immun_id',added_erroneously='$error' ,update_date=NOW(),updated_by=$user,administered_by='NULL' WHERE  id = $id";                   
               $sqlstmt1 = $db->prepare($sql1) ;
               $data =  $sqlstmt1->execute();
               
        }
                      
        if($data){
            echo $patientres = '[{"id":"1"}]';
            //echo $patientresult = GibberishAES::enc($patientres, $key);
            
        }
        else{
            echo $patientres =  '[{"id":"0"}]';
           // echo $patientresult = GibberishAES::enc($patientres, $key);
        }
    } 
    catch(PDOException $e) 
    {

       echo $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
      // echo $patientresult = GibberishAES::enc($error, $key);
    }
}
function saveFeeSheet(){
   
    try 
    {
        $db = getConnection();
        $request = Slim::getInstance()->request();
        $apikey = 'rotcoderaclla';
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        $appres = GibberishAES::dec($request->getBody(), $apikey);
//        $appres = GibberishAES::dec('U2FsdGVkX1/1SUeyVffAI9a+dFKpdIAcwdnHCy4ZDmogIZPUOsufzza/G1Dt2wU1O6wbbHI9WyxJ/9Jc0aCIbHP6rYoy1SoHST97I1jB5eKd4muRr4LlS1FDYw4/0ALMTBhZSRE3fISUcSUG91YY3nV8XDyVt1z5Xe0Flz+inFySyb/Ihk6yDRgzqovq30DDTp2tP9ChlHp7R559fgqbu2khIXe0OroA1ojRkQ8p5qtIXHC6mepjshjHOspR8Yaa4mDelWW4YsaaAdwIJvX7tlVqtEGTuiUI2rVp70XJTrT+OoAIVxz2M+HEgrlZVTnYtHTOa4M3vOxR68N+JPgI5jKX+OkfTpNpqBZTQ7q+kXBcdh002/uoBI+paV2UA6/LwJS2y2B/ICUdsmbW440YgqD1Tv2EydGAm1AB/EhgpmFcDX06my8f1YThB7nsx6KJj0MsmeDumEPjoPQoz+/HZCSSSkD2u1k7bqUyoW+mEtQsL7+HQZkEEgXFIEuHX1U3aSWY+po7H/CcsTaqWST37g3pe+TNT3iCYkMFbntR1bAzMBq6eiGsO+YBjl1vMMMoaHYJ0+LiSodcc40oKy+PAlzL9afsvJzuJQoPwLuK1RRkJBjUOVJbDDrpF/cKhC9XL3j+deN38O77TR8agmkg5QkjbgpA/6nt/Aan1moxKJPvI+VIX7To+dCS4JyL4kzqPJR1E9DHLE75KldDk5yhaIbd5h7ehvlvLK2gq1wbH73v3fGQ8NTOH/RpVZ4yOBCIxbGrg0zU9lc7ZxMsAYkQYg==', $apikey);
        $insertArray = json_decode($appres,TRUE);
       
//        echo "<pre>"; print_r($insertArray); echo "</pre>"; 

        
        $encounter                  = $insertArray['encounter'];
        $pid                        = $insertArray['pid'];
        $user                       = $insertArray['providerid'];
        $provider_id                = $insertArray['providerid'];
        $codearray                  = $insertArray['CodeArray'];
        $icds                       = $insertArray['icds'];
        $justify                    = $insertArray['justify'].":";
        
        $billed                     = 0;
        $bill_date                  = 'NULL';
        $bill_process               = 0;
        $payer_id                   = 'NULL';
        $process_date               = 'NULL';
        $process_file               = 'NULL';
        $authorized                 = 1; 
        $activity                   = 1 ;
        $groupname                  = 'default';
        $code_text                  = '';
        $modifier                   = '';
        $target                     = '';
        
        
        $data = array();
        // cpts
        for($i=0; $i<count($codearray); $i++){
            if(!empty($codearray)){     
                if($codearray[$i]['code']!=''){
                    $descr1="select code_text from codes where code='".$codearray[$i]['code']."' AND code_type=1";
                    $sqlstmte1 = $db->prepare($descr1) ;
                    $datas=  $sqlstmte1->execute();
                    $desval2 = $sqlstmte1->fetchAll(PDO::FETCH_OBJ);
                    if(!empty($desval2)){
                       $code_text= $desval2[0]->code_text;
                       $string = str_replace("'", "\\'", $code_text);
                    } else {
                        $string='';
                    }
                }
                $sql = "INSERT INTO billing (date, code_type, code, pid, provider_id, user,  groupname, authorized, encounter,  code_text, billed, activity,payer_id,bill_process,bill_date,process_date,process_file,modifier,units,fee,justify,target)"
                    . " VALUES ( NOW(),'".$codearray[$i]['code_type']."','".$codearray[$i]['code']."','$pid','$provider_id', '$user','$groupname', ' $authorized',  ' $encounter', '$string', '$billed', '$activity' ,'$payer_id','$bill_process','$bill_date','$process_date','$process_file','$modifier','".$codearray[$i]['units']."','".$codearray[$i]['fee']."','$justify','$target')"; 

                $sqlstmt = $db->prepare($sql) ;
                $data =  $sqlstmt->execute();
                $sql1 = "UPDATE form_encounter SET provider_id ='$user' WHERE pid ='$pid' AND encounter ='$encounter'";
                $sqlstmt1 = $db->prepare($sql1) ;
                $data1 =  $sqlstmt1->execute();  
            }
        }  
       // icds
       for($i=0; $i<count($icds); $i++){ 
            $icd_code = $icds[$i]['code'];
            $descr="select long_desc, formatted_dx_code from icd9_dx_code where dx_code='$icd_code' and active= 1";
            $sqlstmte = $db->prepare($descr) ;
            $datas=  $sqlstmte->execute();
            $desval1 = $sqlstmte->fetchAll(PDO::FETCH_OBJ);
            if(!empty($desval1)){
                $title =   $desval1[0]->long_desc;
                $title1 = str_replace("'", "\\'", $title);
                $title2 = $desval1[0]->formatted_dx_code;
            } else {
                $title1='';
                $title2 = '';
            }
            $check_query = "SELECT * FROM billing WHERE code = '$title2'  AND activity = 1 and encounter = $encounter AND code_type = 'ICD9' AND pid = '$pid' AND code_text = '$title1'"; 
            $cquery = $db->prepare($check_query) ;
            $db->query( "SET NAMES utf8"); 
            $datas2=  $cquery->execute();
            $cqueryval = $cquery->fetchAll(PDO::FETCH_OBJ);
            if(empty($cqueryval)){
                if($title1 != ''){
                    $sql1 = "INSERT INTO billing (date, code_type, code, pid, provider_id, user,  groupname, authorized, encounter,  code_text, billed, activity,payer_id,bill_process,bill_date,process_date,process_file,target)"
                                        . " VALUES ( NOW(),'ICD9','$title2','$pid','$provider_id', '$user','$groupname', ' $authorized',  ' $encounter', '$title1', '$billed', '$activity' ,'$payer_id','$bill_process','$bill_date', '$process_date','$process_file','$target')";
                    $sqlstmt1 = $db->prepare($sql1);
                    $data2 =  $sqlstmt1->execute();
                }
                $sql = "INSERT INTO lists ( date, pid, type, title, comments, begdate, enddate,  diagnosis, occurrence,  referredby,  outcome, destination,    reaction )"
                        . " VALUES ( NOW(), '$pid','medical_problem','$title1','',NOW(), NULL,'ICD9:$title2', 0,  '', 0, '', '' )
                         "; 
                $sqlstmt = $db->prepare($sql) ;
                $data =  $sqlstmt->execute();
                $sel="select id from lists where type='medical_problem' AND title='$title1' AND pid=$pid";
                $sqlstmt2 = $db->prepare($sel) ;
                $data2=  $sqlstmt2->execute();
                $idval = $sqlstmt2->fetchAll(PDO::FETCH_OBJ);
                $newid =  $idval[0]->id;
                if(!empty($newid)){
                    $sql_ie = "SELECT * FROM issue_encounter where pid = $pid and list_id = $newid and encounter = $encounter";
                    $sqlstmt1_ie = $db->prepare($sql_ie) ;
                    $data_ie =  $sqlstmt1_ie->execute();
                    $idval_ie = $sqlstmt1_ie->fetchAll(PDO::FETCH_OBJ);
                    if(empty($idval_ie)){
                        $sql_list="INSERT INTO issue_encounter ( pid, list_id, encounter) VALUES ( $pid, $newid, $encounter)";
                        $sqlstmt1_list = $db->prepare($sql_list) ;
                        $data1 =  $sqlstmt1_list->execute();
                    }    
                    $sel="select pid,type from lists_touch where pid=$pid";
                    $sqlstmt2 = $db->prepare($sel) ;
                    $data2=  $sqlstmt2->execute();
                    $idval2 = $sqlstmt2->fetchAll(PDO::FETCH_OBJ);
                    $p_id =  $idval2[0]->pid;
                    $list_type = $idval2[0]->type;
                    if($p_id!=$pid && $list_type!='medicalproblem')    
                    {
                        $sql6="INSERT INTO lists_touch ( pid, type, date) VALUES ( $pid, 'medical_problem', NOW())";
                        $sqlstmt6 = $db->prepare($sql6) ;
                        $data6 =  $sqlstmt6->execute();
                    }    
                }
                
           }
       }
       // medical problems
       $justify_array = explode("|",str_replace(",","|",str_replace("|",":",str_replace(":",",",$insertArray['justify']))));
       
       for($i=0; $i<count($justify_array); $i++){
            $icd_code2 = $justify_array[$i];
            $title = '';
            $title1='';
            $exploded_code = str_replace("ICD9:", "", $icd_code2);
            $descr2="SELECT long_desc FROM icd9_dx_code WHERE formatted_dx_code ='$exploded_code' and active= 1"; 
            $sqlstmte2 = $db->prepare($descr2) ;
            $datas2=  $sqlstmte2->execute();
            $desval112 = $sqlstmte2->fetchAll(PDO::FETCH_OBJ);
            if(!empty($desval112)){
                
                $title =   $desval112[0]->long_desc;
                $title3 = str_replace("'", "\\'", $title);
            } else {
                $title3='';
            }
            $icd_code3 = str_replace("ICD9:", "",$icd_code2 );
            $check_query2 = "SELECT * FROM billing WHERE code = '$icd_code3' AND activity = 1 AND pid = '$pid' and encounter = $encounter AND code_text = '$title3'"; 
            $db->query( "SET NAMES utf8"); 
            $cquery2 = $db->prepare($check_query2) ;
            $datas2=  $cquery2->execute();
            $cqueryval2 = $cquery2->fetchAll(PDO::FETCH_OBJ);
            if(empty($cqueryval2)){
                $sql12 = "INSERT INTO billing (date, code_type, code, pid, provider_id, user,  groupname, authorized, encounter,  code_text, billed, activity,payer_id,bill_process,bill_date,process_date,process_file,target)"
                                    . " VALUES ( NOW(),'ICD9','$icd_code3','$pid','$provider_id', '$user','$groupname', ' $authorized',  ' $encounter', '$title3', '$billed', '$activity' ,'$payer_id','$bill_process','$bill_date', '$process_date','$process_file','$target')";
                $sqlstmt12 = $db->prepare($sql12);
                $data3 =  $sqlstmt12->execute();
            }
       }
       
        if($data || $data2 || $data3){
            $patientres = '[{"id":"1"}]';
            echo $patientresult = GibberishAES::enc($patientres, $apikey);

        }
        else{
            $patientres =  '[{"id":"0"}]';
            echo $patientresult = GibberishAES::enc($patientres, $apikey);
        }
    } 
    catch(PDOException $e) 
    {

       $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
       echo $patientresult = GibberishAES::enc($error, $apikey);
    }
}

//Method to store mobile settings
function storeMobileSettings()
{
   try
    {
         $flag=0;
        
        $key = 'rotcoderaclla';
        $storerequest = Slim::getInstance()->request();
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        $storeresult =  GibberishAES::dec($storerequest->getBody(), $key);
        //$storeresult =  $storerequest->getBody();
         
            
            $json_a1 = json_decode($storeresult,TRUE);
            $username1=$json_a1['TechSupportCredentials'][0]['Username'];
            $pwd1=$json_a1['TechSupportCredentials'][0]['Password'];
            $url1=$json_a1['TechSupportCredentials'][0]['URL'];
             // print_r($json_a1['PracticeSettings']);  
            $PracticeSettings = json_encode($json_a1['PracticeSettings']);
              
        if($url1=='devint.coopsuite.com') {       
         $db = getConnection1();
        //echo "select ID,user_pass from wp_users where user_login='$username1' ";
        $sql = "select ID,user_pass from wp_users where user_login='$username1' ";
                
        $stmt = $db->prepare($sql) ; 
        $stmt->execute();
        $mobsettings = $stmt->fetchAll(PDO::FETCH_OBJ);           
        //echo "<pre>"; print_r($mobsettings); echo "</pre>";  
        $password=wp_check_password($pwd1,$mobsettings[0]->user_pass);
        if($password==1)
        {   
            $userid=$mobsettings[0]->ID;
            $sql2 = "select * from wp_usermeta where meta_key='app-settings' AND user_id=$userid ";
            $stmt2 = $db->prepare($sql2) ; 
            $stmt2->execute();
            $meta_key = $stmt2->fetchAll(PDO::FETCH_OBJ); 
            if(empty($meta_key)){            
                $sql = "INSERT INTO wp_usermeta (user_id, meta_key,meta_value)
                    values ($userid,'app-settings', '$PracticeSettings')";
                    $sqlstmt = $db->prepare($sql);
                    $data =  $sqlstmt->execute();
                if($data)
                {   
                    $patientres = '[{"id":"Inserted"}]';
                    echo $patientresult = GibberishAES::enc($patientres, $key);
                    //echo $patientresult = $patientres;

                }
                else
                {
                    $patientres = '[{"id":"Not inserted"}]';
                    echo $patientresult = GibberishAES::enc($patientres, $key);
                    //echo $patientresult = $patientres;
                }
            }else {
                $sql = "UPDATE wp_usermeta 
                    SET
                    meta_value='$PracticeSettings'
                    WHERE user_id=$userid AND meta_key='app-settings'";
                    $sqlstmt = $db->prepare($sql);
                    $data =  $sqlstmt->execute();
                if($data)
                {   
                    $patientres = '[{"id":"Updated"}]';
                    echo $patientresult = GibberishAES::enc($patientres, $key);
                    //echo $patientresult = $patientres;

                }
                else
                {
                    $patientres = '[{"id":"Not Updated"}]';
                    echo $patientresult = GibberishAES::enc($patientres, $key); 
                    //echo $patientresult = $patientres;
                }
            }
        }
        else{
            $patientres = '[{"id":"Wrong username/password"}]';
            echo $patientresult = GibberishAES::enc($patientres, $key);
            //echo $patientresult = $patientres;
        }
    }
    else{
        $patientres = '[{"id":"Url Mismatched"}]';
        echo $patientresult = GibberishAES::enc($patientres, $key);
        //echo $patientresult = $patientres;
        
    }
    
    }catch(PDOException $e)
   {
        $patientres = '{"error":{"text":'. $e->getMessage() .'}}';
        echo $patientresult = GibberishAES::enc($patientres, $key);
        //echo $patientresult = $patientres;
    }  

}

function restoreMobileSettings()
{
   try
    {
        $flag=0;
        
        $key = 'rotcoderaclla';
        $restorerequest = Slim::getInstance()->request();
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        $restoreresult =  GibberishAES::dec($restorerequest->getBody(), $key);
        
        $restore_settingsArray = json_decode($restoreresult,TRUE);
            
            $username1=$restore_settingsArray['username'];
            $pwd1=$restore_settingsArray['password'];
            $url1=$restore_settingsArray['url'];
        
         
        if($url1=='devint.coopsuite.com') {              
         $db = getConnection1();
         $sql = "select ID,user_pass from wp_users where user_login='$username1' ";
                
        $stmt = $db->prepare($sql) ; 
        $stmt->execute();
        $mobsettings = $stmt->fetchAll(PDO::FETCH_OBJ);           
        
        $password=wp_check_password($pwd1,$mobsettings[0]->user_pass);
        if($password==1)
        {
            $userid=$mobsettings[0]->ID;
            //echo "select meta_key,meta_value from wp_usermeta where user_id=$userid AND meta_key='app-settings'";
            $sql = "select meta_value from wp_usermeta where user_id=$userid AND meta_key='app-settings'";
                $sqlstmt = $db->prepare($sql);
                $sqlstmt->execute();
                $meta_val = $sqlstmt->fetchAll(PDO::FETCH_OBJ); 
               // echo "<pre>"; print_r($meta_val); echo "</pre>";
               // echo $meta_val[0]->meta_value;
                   
            if($meta_val)
            {  
               echo $patientresult = GibberishAES::enc($meta_val[0]->meta_value, $key);
            }
            else
            {
                $patientres = '[{"id":"0"}]';
                echo $patientresult = GibberishAES::enc($patientres, $key);   
            }
        }
     }    
    }catch(PDOException $e)
    {
       $patientres = '{"error":{"text":'. $e->getMessage() .'}}';
       echo $patientresult = GibberishAES::enc($patientres, $key);
    }  

}
/* ============ bhavya methods end here ================== */
/* ------------ Subhan methods start here ---------------- */
function uploadFile () { 
    try{
        if (!isset($_FILES['uploads'])) {
        echo "No files uploaded!!";
        return;
    }
    $imgs = array();

    $files = $_FILES['uploads'];
    $cnt = count($files['name']);

    for($i = 0 ; $i < $cnt ; $i++) {
        if ($files['error'][$i] === 0) {
            $name = uniqid('img-'.date('Ymd').'-');
            if (move_uploaded_file($files['tmp_name'][$i], 'uploads/' . $name) === true) {
                $imgs[] = array('url' => '/uploads/' . $name, 'name' => $files['name'][$i]);
            }

        }
    }

    $imageCount = count($imgs);

    if ($imageCount == 0) {
       echo 'No files uploaded!!  <p><a href="/">Try again</a>';
       return;
    }

    $plural = ($imageCount == 1) ? '' : 's';

    foreach($imgs as $img) {
        printf('%s <img src="%s" width="50" height="50" /><br/>', $img['name'], $img['url']);
    }
    }
    catch(PDOException $e) 
    {
        echo '{"error":{"text":'. $e->getMessage() .'}}'; 
    }
}
function wp_check_password($password,$hash) {
	global $wp_hasher;

	if ( empty($wp_hasher) ) {
		require_once( 'phpass-0.3/PasswordHash.php');
		// By default, use the portable hash from phpass
		$wp_hasher = new PasswordHash(8, true);
	}

	return $wp_hasher->CheckPassword( trim( $password ),$hash );
}
/* ------------ Subhan methods end here ---------------- */
?>