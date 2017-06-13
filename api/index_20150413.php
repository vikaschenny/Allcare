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
$app->get('/patientagencylist/:pid', 'getPatientAgencies');
// to insert remainder
$app->get('/savereminder','saveReminder');
// to get patient insurance details list
$app->get('/getpatientinsurancelist/:pid','getPatientInsuranceDataList');
// to get incompleteencountercount
$app->get('/incompleteencountercount/:providerid','getIncompleteEncounterCount');
// to get details of incomplete encounter details
$app->get('/incompleteencounterdetails/:pid','getIncompleteEncounterList');
$app->get('/incompleteencounterdetails1/:pid','getIncompleteEncounterList1');
// to update history data of patient
$app->post('/updatehistorydata','updatePatientHistory');
// to add prescription
$app->post('/addprescription','addPrescription');
// to get incomplete patient encounter list
$app->get('/incompletepatientencounterlist/:providerid','getPatientIncompleteEncounterCount');
// to get visit category and dos 
$app->get('/dosvisitlist/:pid','getDosVisitList'); 
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
$app->get('/getdosformdatadetails/:pid', 'getDosFormDataDetails');
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
$app->get('/getfacetoface/:encounterid/:formid', 'getFacetoFace');
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
/* ============ bhavya methods calls start here ================== */
/* ------------ Subhan methods calls start here ------------------ */
$app->post('/fileupload', 'uploadFile');
/* ------------ Subhan methods calls end here ------------------ */
// method for provider login
$app->post('/login', 'loginUser');
// method to get list of todays appointments for given provider
$app->get('/patients/:loginUserId', 'getPatients');
// method to get list of all patients
$app->get('/allpatients/:fromCount', 'getAllPatients');
// method to get list of patients belonging to given provider
$app->get('/filterpatients/:loginProvderId/:fromCount', 'getPatientsByProvider');
// method to get patient demographics
$app->get('/demographics/:patientId', 'getDemographics');
// method to get list of appointments for a given date
$app->get('/patientsbyday/:loginProvderId/:day', 'getPatientsByday');

// method to get list of complete/incomplete encounters for given provider
$app->get('/myencounters/:loginProvderId/:patientId/:mode/:sensitivity', 'getEncounterList');
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
$app->get('/gethistorydetails/:pid/:group_name','getHistoryDetails');

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


// method for user login
// check user/provider authentication with openemr
//function loginUser()
//{   
//    try 
//    {
//        $db = getConnection();
//        
//        $request = Slim::getInstance()->request();
//        $logObject = json_decode($request->getBody(),TRUE);
//        
//        /*$key = 'rotcoderaclla';
//        // The default key size is 256 bits.
//        $old_key_size = GibberishAES::size();
//        GibberishAES::size(256);
//        $username = GibberishAES::dec($logObject['username'], $key);
//        $password = GibberishAES::dec($logObject['password'], $key);
//*/
//        $username = $logObject['username'];
//        $password = $logObject['password'];
//        $sql="SELECT us.id,us.username,us.password,us.salt,u.fname,u.lname, u.authorized
//        FROM users_secure us 
//        INNER JOIN users u ON us.id=u.id 
//        WHERE us.username=:username";
//        
//        $stmt = $db->prepare($sql) ;
//        $stmt->bindParam("username", $username);
//        $stmt->execute();
//        $user = $stmt->fetchObject();                          
//
//        if($user)
//        {
//            //return a hashed string
//            $phash=crypt($password,$user->salt);
//
//            if($phash==$user->password)
//            {
//                // returns id of the user/provider if user/provider is valid
//				/*echo '[{"id":'.json_encode($user->id).',"fname":'.json_encode($user->fname).',"lname":'.json_encode($user->lname).',"authorized":'.json_encode($user->authorized).'}]';*/
//				/*$qry="select  distinct u.id,gag.name as role from users u
//				inner join gacl_aro ga on ga.value= u.username
//				inner join gacl_groups_aro_map ggam on ggam.aro_id=ga.id
//				inner join gacl_aro_groups gag on gag.id=ggam.group_id
//				inner join gacl_aro_groups_map gagm on gagm.group_id=ggam.group_id
//				inner join gacl_aco_map gam on gam.acl_id=gagm.acl_id
//				where u.id=:uid";*/
//				$qry="select  distinct u.id,gag.name as acl_role_name,li.title as user_role, group_concat(concat(gam.section_value,'-',gam.value,'-',gacl.return_value)) as acl_role_permissions, 
//group_concat(concat(gacl.return_value,'-',gacl.note)) as rights
//
// from users u
//				inner join gacl_aro ga on ga.value= u.username
//				inner join gacl_groups_aro_map ggam on ggam.aro_id=ga.id
//				inner join gacl_aro_groups gag on gag.id=ggam.group_id
//				inner join gacl_aro_groups_map gagm on gagm.group_id=ggam.group_id
//				inner join gacl_aco_map gam on gam.acl_id=gagm.acl_id
//				inner join gacl_acl gacl on gacl.id=gagm.acl_id
//                                left join list_options li on li.list_id =  'newcrop_erx_role' AND li.option_id=u.newcrop_user_role
//				where u.id=:uid";
//				$stmt = $db->prepare($qry) ;
//				$stmt->bindParam("uid", $user->id);
//				$stmt->execute();
//				$acl = $stmt->fetchObject();
//				if($acl)
//				{
//					$user_role=$acl->user_role;
//                                        $acl_role_permissions=$acl->acl_role_permissions;
//					$acl_role=$acl->acl_role_name;
//					$rights=$acl->rights;
//				}
//				else
//					$isacct=0;
//                // returns id of the user/provider if user/provider is valid
//				echo $string = '[{"id":'.json_encode($user->id).',"fname":'.json_encode($user->fname).',"lname":'.json_encode($user->lname).',"authorized":'.json_encode($user->authorized).',"user_role":'.json_encode($user_role).',"acl_role_name":'.json_encode($acl_role).',"acl_role_permissions":'.json_encode($acl_role_permissions).',"rights":'.json_encode($rights).'}]';
//                                //echo $stringenc = GibberishAES::enc($string, $key);
//            } 
//            else 
//            {
//                // if username or password is wrong return id=0
//                echo $string = '[{"id":"0"}]';
//               // echo $stringenc = GibberishAES::enc($string, $key);
//            }                               
//        }
//        else
//        {
//            // if user does not exist return id=0
//            echo $string = '[{"id":"0"}]';
//            //echo $stringenc = GibberishAES::enc($string, $key);
//        }
//    } 
//    catch(PDOException $e) 
//    {
//        echo $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
//        //echo $stringenc = GibberishAES::enc($error, $key);
//    }
//}
function loginUser()
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
    $sql = "SELECT ope.pc_eid as apptid,pd.pid,pd.title,pd.fname,pd.lname,if (pd.sex = 'Female' ,'F','M' ) as sex,
                DATE_FORMAT(pd.DOB,'%m-%d-%Y') as DOB,DATE_FORMAT(FROM_DAYS(DATEDIFF(CURDATE(),pd.DOB)), '%Y')+0 AS age,pd.street_addr,pd.city,pd.state,pd.country_code,pd.postal_code,pd.phone_home,pd.phone_biz,pd.phone_contact,pd.phone_cell,pd.contact_relationship as emergency_contact, pd.phone_contact as emergency_phone, DATE_FORMAT(ope.pc_eventDate,'%m-%d-%Y') as event_date ,if(ope.pc_endDate='0000-00-00',DATE_FORMAT(ope.pc_eventDate,'%m-%d-%y'),DATE_FORMAT(ope.pc_endDate,'%m-%d-%Y'))  as end_date ,ope.pc_duration, TIME_FORMAT(ope.pc_startTime, '%h:%i %p') AS start_time,TIME_FORMAT(ope.pc_endTime, '%h:%i %p') AS end_time,ope.pc_facility,ope.pc_catid,pc.pc_catname,ope.pc_billing_location,lo.option_id as symbol,lo.title as apptstatus,ope.pc_hometext As comments
                FROM patient_data pd INNER JOIN openemr_postcalendar_events ope ON pd.pid=ope.pc_pid
                        inner join openemr_postcalendar_categories pc on pc.pc_catid=ope.pc_catid 
						inner join list_options lo on lo.option_id=ope.pc_apptstatus
                WHERE ope.pc_aid=:loginUserId AND ope.pc_eventdate=:day and pd.practice_status = 'YES' AND pd.deceased_stat != 'YES'";  
    

        try 
        {
            $db = getConnection();
            $stmt = $db->prepare($sql) ;
            $stmt->bindParam("loginUserId", $loginUserId); 
	    $stmt->bindParam("day", $day);   			
	    //$stmt->bindParam("visitCategory", $visitCategory); 			
            $stmt->execute();
            $patients = $stmt->fetchAll(PDO::FETCH_OBJ);
            
            
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

// method to get list of all patients
function getAllPatients($fromCount)
{
        $toCount=$fromCount+10;
	$key = 'rotcoderaclla';
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
	$sql = "select pd.pid,pd.title,pd.fname,pd.lname, DATE_FORMAT(pd.DOB,'%m-%d-%Y') AS DOB,DATE_FORMAT(FROM_DAYS(DATEDIFF(CURDATE(),pd.DOB)), '%Y')+0 AS age,if (pd.sex = 'Female' ,'F','M' ) as sex,pd.street,pd.city,pd.state,pd.country_code,pd.postal_code,pd.phone_home,pd.phone_biz,pd.phone_contact,pd.phone_cell,pd.contact_relationship as emergency_contact, pd.phone_contact as emergency_phone
                from patient_data pd where pd.id not in
                (select pd1.id from patient_data pd1
                join (select * from patient_data order by pid desc limit $fromCount) pd2
                on pd1.id=pd2.id) and pd.practice_status = 'YES' AND pd.deceased_stat != 'YES'
                order by pd.pid desc 
                limit 20";      
    
        //$sqlCount = "select COUNT(pid) FROM patient_data pd";        
              
        try 
        {
            $db = getConnection();
            $stmt = $db->prepare($sql) ;
            $stmt->bindParam("fromCount", $fromCount);            
            $stmt->execute();                       
             
            $patients = $stmt->fetchAll(PDO::FETCH_OBJ);                        
            
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
            
            $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
            echo $patientresult = GibberishAES::enc($error, $key);
        }
}

// method to get list of patients belonging to given provider
// get list of all patients  for the logged in user/provider
function getPatientsByProvider($loginProvderId,$fromCount)
{
    $key = 'rotcoderaclla';
    // The default key size is 256 bits.
    $old_key_size = GibberishAES::size();
    GibberishAES::size(256);    
        
	$sql = "select pd.pid,pd.title,pd.fname,pd.lname, DATE_FORMAT(pd.DOB,'%m-%d-%Y') AS DOB,DATE_FORMAT(FROM_DAYS(DATEDIFF(CURDATE(),pd.DOB)), '%Y')+0 AS age,if (pd.sex = 'Female' ,'F','M' ) as sex,pd.street,pd.city,pd.state,pd.country_code,pd.postal_code,pd.phone_home,pd.phone_biz,pd.phone_contact,pd.phone_cell,pd.contact_relationship as emergency_contact, pd.phone_contact as emergency_phone
                from patient_data pd where pd.id not in
                (select pd1.id from patient_data pd1
                join (select * from patient_data order by pid desc limit $fromCount) pd2
                on pd1.id=pd2.id)
                and pd.providerID=:loginProvderId  and pd.practice_status = 'YES' AND pd.deceased_stat != 'YES'
                order by pd.pid desc 
                limit 20";     

        try 
        {
            $db = getConnection();
            $stmt = $db->prepare($sql) ;
            $stmt->bindParam("loginProvderId", $loginProvderId);            
            $stmt->execute();
            $patients = $stmt->fetchAll(PDO::FETCH_OBJ);           
            
            if($patients)
            {
                $patientres = json_encode($patients); 
                echo $patientresult =  GibberishAES::enc($patientres, $key);
            }
            else
            {   
                $patientres = '[{"id":"0"}]';
                echo $patientsresult = GibberishAES::enc($patientsres, $key);
            }
        } 
        catch(PDOException $e) 
        {
            
            $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
            echo $patientsresult = GibberishAES::enc($error, $key);
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
        where pd.pid=$pid AND pd.practice_status = 'YES' AND pd.deceased_stat != 'YES'";
  

    $sql2 = "select pd.street, pd.city, pd.state, pd.country_code, pd.postal_code, pd.mothersname, pd.guardiansname,pd.contact_relationship as emergency_contact, pd.phone_contact as emergency_phone,pd.phone_home as home_phone,pd.phone_biz as work_phone,pd.phone_cell as mobile_phone, pd.email as contact_email
        from patient_data as pd
        where pd.pid=$pid  AND pd.practice_status = 'YES' AND pd.deceased_stat != 'YES'";

    $sql3 = "select concat(u.fname,' ',u.lname) as provider, pd.providerID as providerid,  pd.ref_providerID as ref_providerId ,concat(ur.fname,' ',ur.lname) as reference_provider, pd.pharmacy_id as pharmacy_id, ph.name as pharmacy, pd.hipaa_notice as hippa_notice_received, pd.hipaa_message, pd.hipaa_allowsms,
        pd.hipaa_voice,pd.hipaa_allowemail,pd.hipaa_mail,pd.allow_imm_reg_use,pd.allow_patient_portal, pd.allow_health_info_ex,pd.allow_imm_info_share
        from patient_data pd
        inner join users  u on pd.providerID=u.id
        inner join users ur on pd.ref_providerID=ur.id
        inner join pharmacies ph on pd.pharmacy_id=ph.id where pd.pid=$pid  AND pd.practice_status = 'YES' AND pd.deceased_stat != 'YES'";

    $sql4 = "select pd.occupation, ed.name as employer, ed.street, ed.city, ed.state, ed.country, ed.postal_code
        from employer_data ed
        left join  patient_data pd 
        on pd.pid=ed.pid
        where ed.pid=$pid
        and (ed.name!='' AND ed.street!='' AND ed.city!='' AND ed.state!='' ANd ed.country!='' AND ed.postal_code!='')  AND pd.practice_status = 'YES' AND pd.deceased_stat != 'YES'
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
AND pd.practice_status = 'YES' AND pd.deceased_stat != 'YES'
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
    $sql6 = "select pd.deceased_date, pd.deceased_reason from patient_data pd where pd.pid=$pid  AND pd.practice_status = 'YES' AND pd.deceased_stat != 'YES'";
	
    $sql7 = "select ic.name as insurance_provider, ad.line1 as street, ad.city, ad.city, ad.state, ad.zip, ad.country, ins.plan_name, ins.policy_number, ins.group_number, ins.subscriber_fname, ins.subscriber_lname,
        ins.subscriber_relationship, ins.subscriber_sex, ins.subscriber_DOB, ins.subscriber_street, ins.subscriber_city, ins.subscriber_state,
        ins.subscriber_country, ins.subscriber_postal_code, ins.subscriber_phone,ins.date as effective_date, ins.copay, ins.policy_type
        from insurance_data ins
        inner join insurance_companies ic on ic.id=ins.provider
        inner join addresses ad on ad.foreign_id=ic.id
        where ins.pid=$pid  AND pd.practice_status = 'YES' AND pd.deceased_stat != 'YES'";

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
              from pnotes where pid=$pid and deleted=0 AND pd.practice_status = 'YES' AND pd.deceased_stat != 'YES'";

    $sql14 = "select id,date,event,user,recipient,description,patient_id 
              from extended_log where patient_id=$pid AND pd.practice_status = 'YES' AND pd.deceased_stat != 'YES'";

    $sql15 = "select id,date,pid,user,groupname,authorized,activity,bps,bpd,weight,height,temperature,temp_method,pulse,
                 respiration,note,BMI,BMI_status,waist_circ,head_circ,oxygen_saturation
          from form_vitals where pid=$pid AND pd.practice_status = 'YES' AND pd.deceased_stat != 'YES'";

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
function getEncounterList($loginProvderId,$patientId,$mode,$sensitivity)
{
        /*$key = 'rotcoderaclla';
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);*/
	if($mode=='clinical')
	{
		$sql = "SELECT enc.encounter AS Encounter,
		 CONCAT(pd.fname,' ',pd.lname) as patient,
		GROUP_CONCAT(DISTINCT DATE(enc.date)) AS encounterdate,
		GROUP_CONCAT(ifnull(li.`type`,''),if(li.`type` is not null, ':', ''),ifnull(li.title,'')) AS issues,
		GROUP_CONCAT(DISTINCT ifnull(enc.reason,'')) AS reason,
		GROUP_CONCAT(DISTINCT ifnull(concat(u.fname,' ',u.lname),'')) AS provider,
		GROUP_CONCAT(DISTINCT ifnull(enc.facility,'')) AS facility,
		GROUP_CONCAT(DISTINCT ifnull(fc.name,'')) AS billing_facility,
		GROUP_CONCAT(DISTINCT ifnull(CONCAT(bl.code_type,bl.code),'')) AS billing,
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
		   AND pd.pid=:patientId AND id.type='primary' AND enc.sensitivity=:sensitivity 
                   AND pd.practice_status = 'YES' AND pd.deceased_stat != 'YES'
		GROUP BY enc.encounter
		ORDER BY enc.date desc";
        
		try 
        {
            $db = getConnection();
            $stmt = $db->prepare($sql) ;
            $stmt->bindParam("loginProvderId", $loginProvderId); 
            $stmt->bindParam("patientId", $patientId); 
            $stmt->bindParam("sensitivity", $sensitivity); 
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
                    WHERE encounter=".$encounters[$i]->Encounter."
                    AND pid=$patientId
                    AND (form_name='Face to Face' OR 
                        form_name='Lab Requisition' OR 
                        form_name='Vitals' OR 
                        form_name='SOAP' OR
                        form_name='Speech Dictation' OR
                        form_name='Review Of Systems' OR
                        form_name='Physical Exam')
                        AND deleted=0
                        AND pd.practice_status = 'YES' AND pd.deceased_stat != 'YES'
			order by date desc";
            
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
                            $encounters[$i]->form_name.=$forms[$j]->form_id.'^'.$forms[$j]->form_name.',';
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
                
                echo $encountersres = json_encode($encounters); 
                //echo $encountersresult = GibberishAES::enc($encountersres, $key);


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
	else
	{
		$sql = "SELECT enc.encounter AS encounter,
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
		   AND pd.pid=:patientId AND enc.sensitivity=:sensitivity
                   AND pd.practice_status = 'YES' AND pd.deceased_stat != 'YES'
		GROUP BY enc.encounter
		ORDER BY enc.date desc";
        
		try 
        {
            $db = getConnection();
            $stmt = $db->prepare($sql) ;
            $stmt->bindParam("loginProvderId", $loginProvderId); 
            $stmt->bindParam("patientId", $patientId); 
			$stmt->bindParam("sensitivity", $sensitivity); 
            $stmt->execute();
            $encounters = $stmt->fetchAll(PDO::FETCH_OBJ);
            //print_r($encounters);         
            if($encounters)
            {
              echo  $encountersres = json_encode($encounters); 
               //echo $encountersresult = GibberishAES::enc($encountersres, $key);
 
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

        $msgArray = json_decode($msgresult,TRUE);
       /* $request = Slim::getInstance()->request();
        $msgArray = json_decode($request->getBody(),TRUE);*/
        $msgId = $msgArray['id'];
        $loginProvderId = $msgArray['providerid'];
        $pid = $msgArray['pid'];
        $assigned_to = $msgArray['assigned_to'];
        $body = $msgArray['body'];
        $title = $msgArray['title'];
        $msg_status = $msgArray['msg_status'];

        $rs=getUserName($loginProvderId);
        if($rs)
        {
            $username = $rs[0]['username'];
            $assignedto = explode(' ',$assigned_to);
            if($msgId){

                $sql= "UPDATE pnotes SET date=NOW(),body=CONCAT(date_format(now(), '%Y-%m-%d %H:%i'),' ','($username to $assignedto[$i]),pid=:pid,title=:title, user=$username, assigned_to=$assignedto[$i],message_status=:msg_status WHERE id=$msgId";
                $q = $db->prepare($sql);
                if($q->execute(array(':pid'=>$pid,
                                  ':body'=>$body,
                                  ':title'=>$title,
                                  ':msg_status'=>$msg_status)))

                {   
                        echo '[{"id":"1"}]';
                }
                else
                {
                        echo '[{"id":"0"}]';             
                }
            }else{
                for($i=0;$i<count($assignedto);$i++)
                {
                        $sql = "INSERT INTO pnotes (date, body, pid, user, groupname, activity, authorized, title, assigned_to, message_status)
                        values (NOW(), CONCAT(date_format(now(), '%Y-%m-%d %H:%i'),' ','($username to $assignedto[$i])',' ',:body), :pid, $username, 'default', 1, 1, :title, $assignedto[$i], :msg_status)";
                        $q = $db->prepare($sql);
                        if($q->execute(array(':pid'=>$pid,
                                            ':body'=>$body,
                                            ':title'=>$title,
                                            ':msg_status'=>$msg_status)))

                        {   
                                echo '[{"id":"1"}]';
                        }
                        else
                        {
                                echo '[{"id":"0"}]';             
                        }
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
                                  AND pd.practice_status = 'YES' AND pd.deceased_stat != 'YES' order by f.date DESC LIMIT 1";

			
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
/*function createNewEncounter($pid,$loginProvderId,$encDate)
{
    try
    {
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
            echo json_encode($encExists); 
        }
        else
        {
            //else create new encounter

            //$conn = $GLOBALS['adodb']['db'];
            //$encounter = $conn->GenID("sequences");                

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
            VALUES ('$encDate', 'Your Clinic Name Here',3,$pid,$encounter,9,$loginProvderId,3 )";
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
                    //if encounter already exists return encounter id
                    echo json_encode($newEnc); 
                }
                else
                {
                    echo '[{"id":"-1"}]';                                        
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
}*/

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

        $sqlGetMaxPid="SELECT MAX(pid) as max_pid FROM patient_data";
        $stmtGetMaxPid = $db->prepare($sqlGetMaxPid) ;           
       //$stmt->bindParam("date_today", $date_today);            
        $stmtGetMaxPid->execute();
        $maxPid = $stmtGetMaxPid->fetchAll(PDO::FETCH_OBJ);
        
        $pid = $maxPid[0]->max_pid+1;
        $pubpid=$pid;
                
        // Insert in table 'patient_data'
        $sqlInsertPatientData = "INSERT INTO patient_data(title,fname,mname,lname,DOB,ss,phone_home,phone_cell,
                                         date,sex,providerID,pubpid,pid,usertext1)
                                 VALUES('$title','$fname','$mname','$lname','$DOB','$ss','$phone_home','$phone_cell',
                                        NOW(),'$sex','$providerID','$pubpid','$pid','$comments')";           
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
	    echo '[{"id":"1","patient_id":"'.$pid.'"}]';
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
            pd.street,pd.city,pd.state,pd.country_code,pd.postal_code,pd.phone_cell from patient_data pd where pid not in (SELECT DISTINCT(patientid) FROM tbl_patientfacility)  AND pd.practice_status = 'YES' AND pd.deceased_stat != 'YES'";  
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
        $sql="select pd.pid, tpf.facilityid, pd.title,pd.fname,pd.lname, DATE_FORMAT(pd.DOB,'%m-%d-%Y') AS DOB,
	DATE_FORMAT(FROM_DAYS(DATEDIFF(CURDATE(),pd.DOB)), '%Y')+0 AS age,if (pd.sex = 'Female' ,'F','M' ) as sex,
	pd.street,pd.city,pd.state,pd.country_code,pd.postal_code,pd.phone_cell,
	tpf.facility_admitdate as admitdate, tpf.facility_dischargedate as dischargedate, tpf.facility_notes as notes
	from patient_data pd
	inner join tbl_patientfacility tpf
	on pd.pid=tpf.patientid
	where facility_isactive=1 AND tpf.facilityid=$fid  AND pd.practice_status = 'YES' AND pd.deceased_stat != 'YES'";  
       
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
        $sql="SELECT pc_catid,pc_catname FROM openemr_postcalendar_categories 
              WHERE pc_cattype=0 ORDER BY pc_catname";
    
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
                 'waist_circumference_inches'=> '', 'head_circumference_inches'=> '','oxygen_saturation'=> '' );
             $vitalsdetails[0]->date = $date;
        }
                  
        //echo "<pre>"; print_r($vitalsdetails); echo "</pre>";
        if($vitalsdetails){
            echo $vitalsdetailsres =  json_encode($vitalsdetails); 
            //echo $visitresult = GibberishAES::enc($vitalsdetailsres, $key);
        }
        else{
            echo $vitalsdetailsres =  '[{"id":"0"}]';
            //echo $visitresult = GibberishAES::enc($vitalsdetailsres, $key);
        }
    }catch(PDOException $e){
        echo $vitalsdetailserror =  '{"error":{"text":'. $e->getMessage() .'}}'; 
        //echo $visitresult = GibberishAES::enc($vitalsdetailserror, $key);
    }
}
// method to get vital form data
function getVitalsForm3($encounterId,$Id){
    $key = 'rotcoderaclla';
    // The default key size is 256 bits.
    $old_key_size = GibberishAES::size();
    GibberishAES::size(256);
    try{
        $patientquery = "SELECT pid FROM form_encounter WHERE encounter = $encounterId";
        $db = getConnection();
        $patientquerystmt = $db->prepare($patientquery) ;
        $patientquerystmt->execute();
        $pidval = $patientquerystmt->fetchAll(PDO::FETCH_OBJ);
        $pid = $pidval[0]->pid;
        if($Id != 0){
            $sql = "SELECT fv.id,fv.date,fv.pid,fv.user,fv.authorized,fv.activity,fv.bps as bp_systolic,fv.bpd as bp_diastolic,
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
        }else{
             $vitalsdetails = array('id'=> 0, 'date' => '','pid'=> $pid,'user'=> '','authorized'=> '','activity'=> '','bp_systolic'=>'',
                 'bp_diastolic'=> '','weight_lbs'=> '','height_inches'=> '', 'temperature_fahrenheit'=> '',
                 'temperature_location'=> '','pulse'=> '','respiration'=> '','note'=> '','BMI'=> '','BMI_status'=> '',
                 'waist_circumference_inches'=> '', 'head_circumference_inches'=> '','oxygen_saturation'=> '' );
        }
                  
        //echo "<pre>"; print_r($vitalsdetails); echo "</pre>";
        if($vitalsdetails){
            echo $vitalsdetailsres =  json_encode($vitalsdetails); 
            //echo $visitresult = GibberishAES::enc($vitalsdetailsres, $key);
        }
        else{
            echo $vitalsdetailsres =  '[{"id":"0"}]';
            //echo $visitresult = GibberishAES::enc($vitalsdetailsres, $key);
        }
    }catch(PDOException $e){
        echo $vitalsdetailserror =  '{"error":{"text":'. $e->getMessage() .'}}'; 
        //echo $visitresult = GibberishAES::enc($vitalsdetailserror, $key);
    }
}
function getVitalsForm2($pid, $encounterId,$Id)
{

	 $sql = "SELECT fv.id,fv.date,fv.pid,fv.user,fv.authorized,fv.activity,fv.bps,fv.bpd,fv.weight,
fv.weight_unit,fv.height,fv.height_unit,fv.temperature,fv.temperature_unit,
	   fv.temp_method,fv.pulse,
             fv.respiration,fv.note,fv.BMI,fv.BMI_status,fv.waist_circ,fv.waist_circ_unit,fv.head_circ,fv.head_circ_unit,fv.oxygen_saturation FROM form_vitals as fv
             INNER JOIN forms as fm on fv.id=fm.form_id WHERE fv.pid=:pid and fm.encounter=:encounterId and fv.Id=:Id";

   try
  {
     
     $db = getConnection();
     $stmt = $db->prepare($sql) ;
     $stmt->bindParam("pid", $pid); 
     $stmt->bindParam("encounterId", $encounterId);  
     $stmt->bindParam("Id", $Id);   
        $stmt->execute();
        $vitalsdetails = $stmt->fetchAll(PDO::FETCH_OBJ);           

        if($vitalsdetails)
        {
            //returns visit categories
            echo json_encode($vitalsdetails); 
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

        //$pc_apptstatus=$appointmentDetailsArray['status']; 
        $pc_apptstatus='-'; 
        //$pc_prefcatid=$appointmentDetailsArray['prefcatid']; 
        //$pc_location=$appointmentDetailsArray['location']; 
        //$pc_location='';
        //$pc_eventstatus=$appointmentDetailsArray['eventstatus']; 
        $pc_eventstatus=1;
        //$pc_sharing=$appointmentDetailsArray['title']; 
        $pc_facility=$appointmentDetailsArray['facility']; 
        //$billing_facility=$appointmentDetailsArray['billing_facility']; 
        
        //$pc_billing_location=$appointmentDetailsArray['billing_location'];
        //$pc_billing_location='';
               
	$between_dates=array();
	$between_dates=getBetweenDates($pc_recurrspec,$pc_eventDate,$pc_endDate);

        
$sqlInsertAppointmentData = "INSERT INTO openemr_postcalendar_events 
(pc_catid, pc_aid, pc_pid, pc_title,pc_time, pc_hometext,pc_informant, pc_eventDate, pc_endDate, pc_duration, pc_recurrtype, pc_recurrspec, pc_startTime, pc_endTime, pc_alldayevent, pc_apptstatus, pc_eventstatus, pc_facility ) 
VALUES ('$pc_catid','$pc_aid','$pc_pid','$pc_title',now(),'$pc_hometext','$pc_informant','$pc_eventDate','$pc_endDate','$pc_duration','$pc_recurrtype','$pc_recurrspec','$pc_startTime','$pc_endTime','$pc_alldayevent','$pc_apptstatus','$pc_eventstatus','$pc_facility')";

$stmtInsertAppointmentData = $db->prepare($sqlInsertAppointmentData) ;             
                                     
$checkSlot=checkTimeSlot($pc_aid,$pc_eventDate,$pc_endDate,$pc_startTime,$pc_endTime);
$checkSlotRepeatAppointment=checkTimeSlotsRepeatAppointment($pc_aid,$pc_eventDate,$pc_endDate,$pc_startTime,$pc_endTime,$pc_alldayevent,$between_dates);
//	echo "T/F=".$checkSlotRepeatAppointment;die;
        if(!is_array($checkSlot) && $checkSlot==true && $checkSlotRepeatAppointment=='Y')
        {
		//echo "1st if";
            if($stmtInsertAppointmentData->execute())
            {
                echo '[{"id":"1"}]';
            }
            else
            {
                echo '[{"id":"0"}]';
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
        echo '{"error":{"text":'. $e->getMessage() .'}}'; 
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
    $qry = "select title from list_options where list_id='apptstat' and option_id IN('&#','@','#&','x','?')";
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

        /*$sql="select DATE_FORMAT(ope.pc_eventdate,'%m-%d-%Y') as datevalue from openemr_postcalendar_events ope where ope.pc_aid=$loginProvderId AND ope.pc_apptstatus='-' and ope.pc_eventdate > ( SELECT SUBDATE(now(), INTERVAL 1 week)) GROUP BY ope.pc_eventdate";*/
        $sql="select DISTINCT DATE_FORMAT(ope.pc_eventdate,'%m-%d-%Y') as datevalue from openemr_postcalendar_events ope where ope.pc_aid=$loginProvderId AND ope.pc_eventdate > ( SELECT SUBDATE(now(), INTERVAL 1 week))";
        $stmt = $db->prepare($sql) ; 
        $stmt->execute();
        $apptdates = $stmt->fetchAll(PDO::FETCH_OBJ);           

        if($apptdates)
        {
            $appdateres =  json_encode($apptdates); 
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
function getHistoryDetails($patientId, $group_name2)
{
    try
    {	$newdemo = array();
        $dataarray = array();
        $group_name = str_replace('_', ' ', $group_name2);
        $db=getConnection();
        $key = 'rotcoderaclla';
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
                    from layout_options where form_id='HIS' and uor <> 0 AND group_name='".$group_name."' order by seq";
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
        //echo "<pre>"; print_r($dataarray);echo "</pre>";
        $newdemo1 = encode_demo($dataarray);  
        $newdemo['HistoryData'] = check_data_available($newdemo1);
        //exit();
        if($newdemo){
           echo $hisres2 = json_encode($newdemo);
           //echo $historyresult =  GibberishAES::enc($hisres2, $key);
           //echo $updateissueresult =  GibberishAES::dec($historyresult, $key);
        }else{
            echo $hisres = '[{"id":"0"}]';
            //echo $historyresult =  GibberishAES::enc($hisres, $key);
        }
    }catch(PDOException $e)
    {
        echo $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
        //echo $historyresult =  GibberishAES::enc($error, $key);
    }
}
function getHistoryDetails_pre1($patientId, $group_name2)
{
    try
    {	$newdemo = array();
        $dataarray = array();
        $group_name = str_replace('_', ' ', $group_name2);
        $db=getConnection();
        $key = 'rotcoderaclla';
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
                    from layout_options where form_id='HIS' and uor <> 0 AND group_name='".$group_name."' order by seq";
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
                $stmt3 = $db->prepare($listnamessql); 
                $stmt3->execute();
                $listnamesresult = $stmt3->fetchAll(PDO::FETCH_OBJ);
                $selectedlistarray = array();
                $selectedlistarray = explode("|", $sqllabelres[0]->ltitle);
                    
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
                $data['data']['list'] = $list;
            else:
                $data['data']['list'] = $list;
            endif;
            
            $dataarray[] = $data;
        }
        //echo "<pre>"; print_r($dataarray);echo "</pre>";
        $newdemo1 = encode_demo($dataarray);  
        $newdemo['HistoryData'] = check_data_available($newdemo1);
        //exit();
        if($newdemo){
           $hisres = json_encode($newdemo);
           echo $historyresult =  GibberishAES::enc($hisres, $key);
        }else{
            $hisres = '[{"id":"0"}]';
            echo $historyresult =  GibberishAES::enc($hisres, $key);
        }
    }catch(PDOException $e)
    {
        $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo $historyresult =  GibberishAES::enc($error, $key);
    }
}

function getHistoryDetails_pre2($patientId, $group_name2)
{
    try
    {	$newdemo = array();
        $dataarray = array();
        $group_name = str_replace('_', ' ', $group_name2);
        $db=getConnection();
        $key = 'rotcoderaclla';
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
                    from layout_options where form_id='HIS' and uor <> 0 AND group_name='".$group_name."' order by seq";
        $stmt2 = $db->prepare($fieldnames); 
        $stmt2->execute();
        $fieldnamesresult = $stmt2->fetchAll(PDO::FETCH_OBJ);
        
        foreach($fieldnamesresult as $flname){
            $data = array();
            $data['data']['label'] = $flname->title;
            $data['data']['order'] = $flname->seq;
            $data['data']['field_id'] = $flname->field_id;
            $data['data']['data_type'] = $flname->data_type;
            if($flname->data_type == 'Smoking Status' || $flname->data_type == 'Lifestyle status'):
                $data['data']['statusText'] = 'textbox';
                $data['data']['status']['statusheader'] = 'Status';
                $statustypes = array('current'=>'Current','quit' =>'Quit','never' => 'Never','not_applicable' => 'N/A');
                foreach($statustypes as $key => $stype){
                    $stypelist = array();
                    $stypelist['statuslabel']['sname'] = $stype;
                    $stypelist['statuslabel']['svalue'] = $key;
                    $stypelist['statuslabel']['statusControlType'] = 'radiobutton';
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
                    $stmt5 = $db->prepare($listnamessql2); 
                    $stmt5->execute();
                    $listnamesresult2 = $stmt5->fetchAll(PDO::FETCH_OBJ);
                    foreach($listnamesresult2 as $list4){
                        $lists['smokinglist'][$list4->option_id] = $list4->title;
                    }
                    $data['data']['smokinglist'] = $lists;
                    $data['data']['smokinglist']['listcontrolType'] = 'List box';
                elseif($flname->data_type == 'Smoking Status' && $flname->list_id == ''):
                    $data['data']['smokinglist'] = array();
                    $data['data']['smokinglist']['listcontrolType'] = 'List box';
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
            
            $sqllabel = "SELECT $flname->field_id as ltitle FROM history_data WHERE pid=$patientId order by date desc limit 1";
            $stmt4 = $db->prepare($sqllabel); 
            $stmt4->execute();
            $sqllabelres = $stmt4->fetchAll(PDO::FETCH_OBJ);
            $data['data']['selected_list'] = $sqllabelres[0]->ltitle;
            
            $list = array();
            if($flname->list_id != '' && $flname->data_type != 'Smoking Status'):
                $listnamessql = "SELECT * FROM list_options WHERE list_id = '$flname->list_id'";
                $stmt3 = $db->prepare($listnamessql); 
                $stmt3->execute();
                $listnamesresult = $stmt3->fetchAll(PDO::FETCH_OBJ);
                $selectedlistarray = array();
                $selectedlistarray = explode("|", $sqllabelres[0]->ltitle);
                    
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
                $data['data']['list'] = $list;
            else:
                $data['data']['list'] = $list;
            endif;
            
            $dataarray[] = $data;
        }
        //echo "<pre>"; print_r($dataarray);echo "</pre>";
        $newdemo1 = encode_demo($dataarray);  
        $newdemo['HistoryData'] = check_data_available($newdemo1);
        //exit();
        if($newdemo){
           $hisres = json_encode($newdemo);
           echo $historyresult =  GibberishAES::enc($hisres, $key);
        }else{
            $hisres = '[{"id":"0"}]';
            echo $historyresult =  GibberishAES::enc($hisres, $key);
        }
    }catch(PDOException $e)
    {
        $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo $historyresult =  GibberishAES::enc($error, $key);
    }
}

function getHistoryDetails_old($patientId, $group_name2)
{
    try
    {	$newdemo = array();
        $dataarray = array();
        $group_name = str_replace('_', ' ', $group_name2);
        $db=getConnection();
        $key = 'rotcoderaclla';
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
                    from layout_options where form_id='HIS' and uor <> 0 AND group_name='".$group_name."' order by seq";
        $stmt2 = $db->prepare($fieldnames); 
        $stmt2->execute();
        $fieldnamesresult = $stmt2->fetchAll(PDO::FETCH_OBJ);
        
        foreach($fieldnamesresult as $flname){
            $data = array();
            $data['data']['label'] = $flname->title;
            $data['data']['order'] = $flname->seq;
            $data['data']['field_id'] = $flname->field_id;
            $data['data']['data_type'] = $flname->data_type;
            if($flname->data_type == 'Smoking Status' || $flname->data_type == 'Lifestyle status'):
                $data['data']['statusText'] = 'textbox';
                $data['data']['status']['statusheader'] = 'Status';
                $statustypes = array('current'=>'Current','quit' =>'Quit','never' => 'Never','not_applicable' => 'N/A');
                foreach($statustypes as $key => $stype){
                    $stypelist = array();
                    $stypelist['statuslabel']['sname'] = $stype;
                    $stypelist['statuslabel']['svalue'] = $key;
                    $stypelist['statuslabel']['statusControlType'] = 'radiobutton';
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
                    $stmt5 = $db->prepare($listnamessql2); 
                    $stmt5->execute();
                    $listnamesresult2 = $stmt5->fetchAll(PDO::FETCH_OBJ);
                    foreach($listnamesresult2 as $list4){
                        $lists['smokinglist'][$list4->option_id] = $list4->title;
                    }
                    $data['data']['smokinglist'] = $lists;
                    $data['data']['smokinglist']['listcontrolType'] = 'List box';
                elseif($flname->data_type == 'Smoking Status' && $flname->list_id == ''):
                    $data['data']['smokinglist'] = array();
                    $data['data']['smokinglist']['listcontrolType'] = 'List box';
               endif;
            elseif($flname->data_type == 'Exam results'):
                $data['data']['examresults']['examslist'] = 'examlist';
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
            
            $sqllabel = "SELECT $flname->field_id as ltitle FROM history_data WHERE pid=$patientId order by date desc limit 1";
            $stmt4 = $db->prepare($sqllabel); 
            $stmt4->execute();
            $sqllabelres = $stmt4->fetchAll(PDO::FETCH_OBJ);
            $data['data']['selected_list'] = $sqllabelres[0]->ltitle;
            $list = array();
            if($flname->list_id != '' && $flname->data_type != 'Smoking Status'):
                $listnamessql = "SELECT * FROM list_options WHERE list_id = '$flname->list_id'";
                $stmt3 = $db->prepare($listnamessql); 
                $stmt3->execute();
                $listnamesresult = $stmt3->fetchAll(PDO::FETCH_OBJ);
                foreach($listnamesresult as $list2){
                    $list[$list2->option_id] = $list2->title;
                }
                $data['data']['list'] = $list;
            else:
                $data['data']['list'] = $list;
            endif;
            
            $dataarray[] = $data;
        }
        //echo "<pre>"; print_r($dataarray);echo "</pre>";
        $newdemo1 = encode_demo($dataarray);  
        $newdemo['HistoryData'] = check_data_available($newdemo1);
        //exit();
        if($newdemo){
           $hisres = json_encode($newdemo);
           echo $historyresult =  GibberishAES::enc($hisres, $key);
        }else{
            $hisres = '[{"id":"0"}]';
            echo $historyresult =  GibberishAES::enc($hisres, $key);
        }
    }catch(PDOException $e)
    {
        $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo $historyresult =  GibberishAES::enc($error, $key);
    }
}

function getHistoryDetails_new($patientId, $group_name2)
{
    try
    {	$newdemo = array();
        $group_name = str_replace('_', ' ', $group_name2);
        $db=getConnection();
        $key = 'rotcoderaclla';
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
                    from layout_options where form_id='HIS' and uor <> 0 AND group_name='".$group_name."' order by seq";
        $stmt2 = $db->prepare($fieldnames); 
        $stmt2->execute();
        $fieldnamesresult = $stmt2->fetchAll(PDO::FETCH_OBJ);
        foreach($fieldnamesresult as $flname){
            $data['data']['label'] = $flname->title;
            $data['data']['order'] = $flname->seq;
            $data['data']['field_id'] = $flname->field_id;
            $data['data']['data_type'] = $flname->data_type;
            $sqllabel = "SELECT $flname->field_id as ltitle FROM history_data WHERE pid=$patientId order by date desc limit 1";
            $stmt4 = $db->prepare($sqllabel); 
            $stmt4->execute();
            $sqllabelres = $stmt4->fetchAll(PDO::FETCH_OBJ);
            $data['data']['selected_list'] = $sqllabelres[0]->ltitle;
            if($flname->list_id != ''):
                $listnamessql = "SELECT * FROM list_options WHERE list_id = '$flname->list_id'";
                $stmt3 = $db->prepare($listnamessql); 
                $stmt3->execute();
                $listnamesresult = $stmt3->fetchAll(PDO::FETCH_OBJ);
                $list = array();
                foreach($listnamesresult as $list2){
                    $list[$list2->option_id] = $list2->title;
                }
                $data['data']['list'] = $list;
            else:
                $data['data']['list'] = array();
            endif;
            $dataarray[] = $data;
        }
        
        //echo "<pre>"; print_r($dataarray);echo "</pre>";
        $newdemo1 = encode_demo($dataarray);  
        $newdemo['HistoryData'] = check_data_available($newdemo1);
        if($newdemo){
           $hisres = json_encode($newdemo);
           echo $historyresult =  GibberishAES::enc($hisres, $key);
        }else{
            $hisres = '[{"id":"0"}]';
            echo $historyresult =  GibberishAES::enc($hisres, $key);
        }
    }catch(PDOException $e)
    {
        $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo $historyresult =  GibberishAES::enc($error, $key);
    }
}

function getHistoryDetails3($patientId)
{
    try
    {	
        $db=getConnection();
        $key = 'rotcoderaclla';
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        $names = "select distinct(group_name) from layout_options where form_id='HIS' ORDER BY group_name";
        $stmt = $db->prepare($names) ; 
        $stmt->execute();
        $namesresult = $stmt->fetchAll(PDO::FETCH_OBJ);      
        $fieldnamesresult = array();
        foreach ($namesresult as $value) {
            $fieldnames = "select field_id from layout_options where form_id='HIS' and group_name='".$value->group_name."'";
            $stmt2 = $db->prepare($fieldnames) ; 
            $stmt2->execute();
            $fieldnamesresult = $stmt2->fetchAll(PDO::FETCH_NUM);      
        //} 
        $res = str_replace('[','',json_encode($fieldnamesresult));
        $res1 = str_replace(']', '', $res);
        $res2 = str_replace('"', '', $res1);
        $s =  "SELECT DATE(date) as date, $res2 from history_data where pid=$patientId ";
        $his = $db->prepare($s) ; 
        $his->execute();
        $data = $his->fetchAll(PDO::FETCH_OBJ); 
        if(!empty($data)):
            $hisresult[substr($value->group_name,1)][] = $data;
        endif;
        }
        //echo "<pre>"; print_r($hisresult);echo "</pre>";
        if($hisresult){
            $hisres = json_encode($hisresult);
            echo $historyresult =  GibberishAES::enc($hisres, $key);
        }else{
            $hisres = '[{"id":"0"}]';
            echo $historyresult =  GibberishAES::enc($hisres, $key);
        }
    }catch(PDOException $e)
    {
        $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo $historyresult =  GibberishAES::enc($error, $key);
    }
}

function getHistoryDetails2($patientId)
{
    try
    {	
        $db=getConnection();
        $key = 'rotcoderaclla';
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        $names = "select distinct(group_name) from layout_options where form_id='HIS'";
        $stmt = $db->prepare($names) ; 
        $stmt->execute();
        $namesresult = $stmt->fetchAll(PDO::FETCH_OBJ);      
        $fieldnamesresult = array();
        foreach ($namesresult as $value) {
            $fieldnames = "select field_id from layout_options where form_id='HIS' and group_name='".$value->group_name."'";
            $stmt2 = $db->prepare($fieldnames) ; 
            $stmt2->execute();
            $fieldnamesresult[] = $stmt2->fetchAll(PDO::FETCH_NUM);      
        } //echo "<pre>"; print_r($fieldnamesresult);echo "</pre>";
        $res = str_replace('[','',json_encode($fieldnamesresult));
        $res1 = str_replace(']', '', $res);
        $res2 = str_replace('"', '', $res1);
        //print_r($res2);
        $s =  "SELECT $res2 from history_data where pid=$patientId order by id ";
        $his = $db->prepare($s) ; 
        $his->execute();
        $hisresult = $his->fetchAll(PDO::FETCH_OBJ);   
        if($hisresult){
           $hisres = json_encode($hisresult);
           echo $historyresult =  GibberishAES::enc($hisres, $key);
        }else{
             $hisres = '[{"id":"0"}]';
             echo $historyresult =  GibberishAES::enc($hisres, $key);
        }
    }catch(PDOException $e)
    {
            $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
            echo $historyresult =  GibberishAES::enc($error, $key);
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
            $sql1 = "SELECT id,date,type,title,begdate,enddate,returndate,occurrence,classification,referredby,extrainfo,diagnosis,activity,comments,pid,user,groupname,outcome,destination,reinjury_id,injury_part,injury_type,injury_grade,reaction,external_allergyid,erx_source,erx_uploaded,modifydate FROM lists WHERE pid = :patientId AND type = 'medical_problem' ORDER BY begdate";

            //To get Allergies
            $sql2 = "SELECT id,date,type,title,begdate,enddate,returndate,occurrence,classification,referredby,extrainfo,diagnosis,activity,comments,pid,user,groupname,outcome,destination,reinjury_id,injury_part,injury_type,injury_grade,reaction,external_allergyid,erx_source,erx_uploaded,modifydate FROM lists WHERE pid = :patientId AND type = 'allergy' ORDER BY begdate";

            //To get Medications
            $sql3 = "SELECT id,date,type,title,begdate,enddate,returndate,occurrence,classification,referredby,extrainfo,diagnosis,activity,comments,pid,user,groupname,outcome,destination,reinjury_id,injury_part,injury_type,injury_grade,reaction,external_allergyid,erx_source,erx_uploaded,modifydate FROM lists WHERE pid = :patientId AND type = 'medication' ORDER BY begdate";

            //To get Surgeries
            $sql4 = "SELECT id,date,type,title,begdate,enddate,returndate,occurrence,classification,referredby,extrainfo,diagnosis,activity,comments,pid,user,groupname,outcome,destination,reinjury_id,injury_part,injury_type,injury_grade,reaction,external_allergyid,erx_source,erx_uploaded,modifydate FROM lists WHERE pid = :patientId AND type = 'surgery' ORDER BY begdate";

            //To get Dental Issues 
            $sql5 = "SELECT id,date,type,title,begdate,enddate,returndate,occurrence,classification,referredby,extrainfo,diagnosis,activity,comments,pid,user,groupname,outcome,destination,reinjury_id,injury_part,injury_type,injury_grade,reaction,external_allergyid,erx_source,erx_uploaded,modifydate FROM lists WHERE pid = :patientId AND type = 'dental' ORDER BY begdate";

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
            $sql7 = "select id,patient_id,filled_by_id,pharmacy_id,date_added,date_modified,provider_id,encounter,start_date,drug,drug_id,rxnorm_drugcode,form,dosage,quantity,size,unit,route,substitute,refills,per_refill,filled_date,medication,note,active,site,prescriptionguid,erx_source,erx_uploaded,drug_info_erx from prescriptions where patient_id=:patientId and active='1'";

            $demo1=sql_execute($patientId,$sql1);
            $demo2=sql_execute($patientId,$sql2);
            $demo3=sql_execute($patientId,$sql3);
            $demo4=sql_execute($patientId,$sql4);
            $demo5=sql_execute($patientId,$sql5);
            $demo6=sql_execute($patientId,$sql6);
            $demo7=sql_execute($patientId,$sql7);

            $newdemo1=encode_demo($demo1);
            $newdemo2=encode_demo($demo2);  
            $newdemo3=encode_demo($demo3);  
            $newdemo4=encode_demo($demo4);              
            $newdemo5=encode_demo($demo5);  
            $newdemo6=encode_demo($demo6);  
            $newdemo7=encode_demo($demo7);  

            $newdemo['Medical_Problems'] = check_data_available($newdemo1);
            $newdemo['Allergies'] = check_data_available($newdemo2);
            $newdemo['Medications'] = check_data_available($newdemo3);
            $newdemo['Surgeries'] = check_data_available($newdemo4);
            $newdemo['DentalIssues'] = check_data_available($newdemo5);
            $newdemo['Immunizations'] = check_data_available($newdemo6);
            $newdemo['Prescriptions'] = check_data_available($newdemo7);

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
			where pn.deleted=0  AND pd.practice_status = 'YES' AND pd.deceased_stat != 'YES'";
			
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
				echo '[{"id":"0"}]';
			}
		}
		else
		{
			$rs=getUserName($loginProvderId);
			if($rs)
			{
				$username = $rs[0]['username'];		
				$sql="select concat(us.fname,' ',us.lname) as msg_from, pd.pid as pid,concat(pd.lname,' ',pd.fname) as patient, pn.title as msg_type, pn.date, pn.message_status as status,pn.body as msg_content
				from pnotes pn inner join users us on pn.user=us.username
				inner join patient_data pd on pd.pid=pn.pid
				where pn.assigned_to='$username' and pn.deleted=0  AND pd.practice_status = 'YES' AND pd.deceased_stat != 'YES'";
			
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
        $sql="SELECT id, concat(users.fname,' ', users.lname) as user from users where users.active=1";
    
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
        /*$key = 'rotcoderaclla';
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);*/
        $balance = 0;
	$charges = 0;
    /*$feres = sqlStatement("SELECT date, encounter, last_level_billed, " .
      "last_level_closed, stmt_count " .
      "FROM form_encounter WHERE pid = ?", array($pid));
    */
    $sql="SELECT date, encounter, last_level_billed, " .
      "last_level_closed, stmt_count " .
      "FROM form_encounter WHERE pid = $pid";
    $sql2 = "SELECT f.pid, f.billing_note, f.date, f.encounter, (
            SELECT GROUP_CONCAT( code_type,  '-', code ) 
            FROM billing
            WHERE encounter = f.encounter
            ) AS cpt
            FROM  `form_encounter` f
            INNER JOIN patient_data p ON p.pid = f.pid
            WHERE f.pid =$pid
            GROUP BY f.encounter";

        $stmt = $db->prepare($sql) ; 
        $stmt = $db->prepare($sql2) ; 
        $stmt->execute();
        $feres = $stmt->fetchAll(PDO::FETCH_OBJ); 
        
        if($feres)
        {            
            $i=0;            
            
            //while($feres[$i])
            while($i<count($feres))
            {
              //print_r($feres[$i]);
                
                $encounter = $feres[$i]->encounter;
                $dos = substr($feres[$i]->date, 0, 10);
                //$insarr = getEffectiveInsurances($pid, $dos);
                
                $insarr = array();
                foreach (array('primary','secondary','tertiary') as $instype) 
                {
//                    
//                    $tmp = sqlQuery("SELECT * FROM insurance_data " .
//                      "WHERE pid = ? AND type = ? " .
//                      "AND date <= ? ORDER BY date DESC LIMIT 1",
//                      array($pid, $instype, $dos));
//                    
                    
                    $sql3="SELECT * FROM insurance_data 
                      WHERE pid = '$pid' AND type = '$instype' 
                      AND date <= '$dos' ORDER BY date DESC LIMIT 1";
    
                    $stmt3 = $db->prepare($sql3) ; 
                    $stmt3->execute();
                    $tmp = $stmt3->fetchAll(PDO::FETCH_OBJ); 
                                                            
                    if (empty($tmp['provider'])) break;
                    $insarr[] = $tmp;
                }
                
                $inscount = count($insarr);
                if (!$with_insurance && 
                    $feres[$i]->last_level_closed < $inscount && 
                    $feres[$i]->stmt_count == 0) 
                {
                  // It's out to insurance so only the co-pay might be due.
                  
//                  $brow = sqlQuery("SELECT SUM(fee) AS amount FROM billing WHERE " .
//                    "pid = ? AND encounter = ? AND " .
//                    "code_type = 'copay' AND activity = 1",
//                    array($pid, $encounter));
                  

                  $sql1="SELECT SUM(fee) AS amount FROM billing WHERE " .
                    "pid = '$pid' AND encounter = '$encounter' AND " .
                    "code_type = 'copay' AND activity = 1"; 

                  $stmt1 = $db->prepare($sql1); 
                  $stmt1->execute();
                  $brow = $stmt1->fetchAll(PDO::FETCH_OBJ); 
					$charges +=$brow[0]->amount;
                  
//                  $drow = sqlQuery("SELECT SUM(pay_amount) AS payments " .
//                    "FROM ar_activity WHERE " .
//                    "pid = ? AND encounter = ? AND payer_type = 0",
//                    array($pid, $encounter));
                  

                  $sql2="SELECT SUM(fee) AS amount FROM billing WHERE " .
                    "pid = '$pid' AND encounter = '$encounter' AND " .
                    "code_type = 'copay' AND activity = 1"; 

                  $stmt2 = $db->prepare($sql2); 
                  $stmt2->execute();
                  $drow = $stmt2->fetchAll(PDO::FETCH_OBJ); 


                  $ptbal = $insarr[0]['copay'] + $brow[0]->amount - $drow[0]->payments;
                  if ($ptbal > 0) $balance += $ptbal;

                }
                else 
                {
                  // Including insurance or not out to insurance, everything is due.
//                  
//                   $brow = sqlQuery("SELECT SUM(fee) AS amount FROM billing WHERE " .
//                    "pid = ? AND encounter = ? AND " .
//                    "activity = 1", array($pid, $encounter));
//                  
                  
                  $sql4="SELECT SUM(fee) AS amount FROM billing WHERE " .
                    "pid = '$pid' AND encounter = '$encounter' AND " .
                    "activity = 1";
    
                    $stmt4 = $db->prepare($sql4) ; 
                    $stmt4->execute();
                    $brow = $stmt4->fetchAll(PDO::FETCH_OBJ); 
                    $charges +=$brow[0]->amount;         
//                  
//                  $drow = sqlQuery("SELECT SUM(pay_amount) AS payments, " .
//                    "SUM(adj_amount) AS adjustments FROM ar_activity WHERE " .
//                    "pid = ? AND encounter = ?", array($pid, $encounter));
//                  
                    
                   $sql5="SELECT SUM(pay_amount) AS payments, " .
                    "SUM(adj_amount) AS adjustments FROM ar_activity WHERE " .
                    "pid = '$pid' AND encounter = '$encounter'";
    
                    $stmt5 = $db->prepare($sql5) ; 
                    $stmt5->execute();
                    $drow = $stmt5->fetchAll(PDO::FETCH_OBJ); 
                  
//                  
//                  $srow = sqlQuery("SELECT SUM(fee) AS amount FROM drug_sales WHERE " .
//                    "pid = ? AND encounter = ?", array($pid, $encounter));
//                  
                  
                  $sql6="SELECT SUM(fee) AS amount FROM drug_sales WHERE " .
                    "pid = '$pid' AND encounter = '$encounter'";
    
                    $stmt6 = $db->prepare($sql6) ; 
                    $stmt6->execute();
                    $srow = $stmt6->fetchAll(PDO::FETCH_OBJ); 
                  
                  
                  
                  $balance += $brow[0]->amount + $srow[0]->amount
                    - $drow[0]->payments - $drow[0]->adjustments;
                }

                
                $i++;
            }
                        
        }
            
        $balance= sprintf('%01.2f', $balance);
        echo json_encode($feres);
        echo $bilres = '[{"id":"1","Total Charges":"'.$charges.'","balance_amount":"'.$balance.'"}]';    
        //$bilresult = GibberishAES::enc($bilres, $key);
    
    }
    catch(PDOException $e) 
    {
        echo $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
        //echo $bilresult = GibberishAES::enc($error, $key);
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
            $sql="select u.id,concat(u.fname,' ',u.lname) as provider from users u
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

        if($q->execute(array( ':pid'=>$pid,':street'=>$street,
                          ':state'=>$state,':country_code'=>$country_code,
                          ':postal_code'=>$postal_code,':mothersname'=>$mothersname,':guardiansname'=>$guardiansname,':contact_relationship'=>$contact_relationship,':phone_contact'=>$phone_contact,':phone_home'=>$phone_home,':phone_biz'=>$phone_biz,':phone_cell'=>$phone_cell,':email'=>$email
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
            echo '[{"id":"1"}]';
        } 
        else
        {
                echo '[{"id":"0"}]';
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
        $key = 'rotcoderaclla';
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        $request = Slim::getInstance()->request();
        
        $editpatientres =  GibberishAES::dec($request->getBody(), $key);
        $pfArray = json_decode($editpatientres,TRUE);
	//print_r($issueArray);
			
        //$id=$pfArray['id'];
        $pid=$pfArray['pid'];
        $loginProvderId=(int)$pfArray['loginProviderId'];
        $facilityid=(int)$pfArray['facilityid'];
        $admitdate=$pfArray['admitdate'];
        $dischargedate=$pfArray['dischargedate'];
        $isactive=$pfArray['isactive'];
        $notes=$pfArray['notes'];
        $related_links=$pfArray['related_links'];
        
        
        $checkfield = "SELECT id from tbl_patientfacility WHERE patientid=$pid AND facilityid=$facilityid AND facility_admitdate='$admitdate' AND facility_dischargedate='$dischargedate' AND facility_isactive=$isactive";
        $c = $db->prepare($checkfield);
        $c->execute();
        $resid=$c->fetch(PDO::FETCH_OBJ);
        
        if($resid){
           $id = $resid->id;
        }else{
            $id=0;
        }
      
        
        if($id==0)
        {
                $insert_pf_Sql="INSERT INTO tbl_patientfacility(patientid,facilityid,facility_admitdate,facility_dischargedate,facility_isactive,facility_notes,facility_related_links,createdby,createddate)
                                                                           VALUES (:pid,:facilityid,:admitdate,:dischargedate,:isactive,:notes,:related_links,:loginProvderId,NOW())";

                $q = $db->prepare($insert_pf_Sql);

                if($q->execute(array( ':pid'=>$pid,':facilityid'=>$facilityid,
                                   ':admitdate'=>$admitdate,':dischargedate'=>$dischargedate,
                                  ':isactive'=>$isactive,':notes'=>$notes,':related_links'=>$related_links,':loginProvderId'=>$loginProvderId	)))
                {                       		
                        /*$sqlGetMaxid="SELECT MAX(id) as pf_id FROM tbl_patientfacility";
                        $stmtGetMaxid = $db->prepare($sqlGetMaxid) ;           
                        //$stmt->bindParam("date_today", $date_today);            
                        $stmtGetMaxid->execute();
                        $maxid = $stmtGetMaxid->fetchAll(PDO::FETCH_OBJ);
                        $maxidres =  '[{"id":$maxid}]';
                        
                        echo $maxidresult = GibberishAES::enc($maxidres, $key);*/
                     echo '[{"id":"1"}]';
                    
                }
                else
                {
                        echo '[{"id":"0"}]';                 
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
                updatedby=:loginProvderId,
                updateddate=NOW()
                WHERE id=$id
                ";

                $q = $db->prepare($update_issues_Sql);

                if($q->execute(array( ':patientid'=>$pid,':facilityid'=>$facilityid,
                                  ':admitdate'=>$admitdate,':dischargedate'=>$dischargedate,
                                  ':isactive'=>$isactive,':notes'=>$notes,':related_links'=>$related_links,':loginProvderId'=>$loginProvderId	)))
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

function editWhoDetails()
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
        $sql = "SELECT prescriptions.id, patient_id, filled_by_id, pharmacy_id, date_added, date_modified, provider_id, CONCAT( users.fname,  ' ', users.lname ) AS provider, encounter, start_date, drug, drug_id, rxnorm_drugcode, form, dosage, quantity, size, unit, route, substitute, refills, per_refill, filled_date, medication, note, prescriptions.active, site, prescriptionguid, erx_source, erx_uploaded, drug_info_erx
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
// to get dosform data details based on encounter
function getDosFormDataDetails($eid){
   	
//        $key = 'rotcoderaclla';
//        // The default key size is 256 bits.
//        $old_key_size = GibberishAES::size();
//        GibberishAES::size(256);
        try 
        {
            $db = getConnection();
            $newdemo = '';
            $sql = "select pid from form_encounter where encounter= $eid";      
            $stmt = $db->prepare($sql) ;
            $stmt->execute();                       
            $patients = $stmt->fetchAll(PDO::FETCH_OBJ); 
            $patients2 = array();
            $patients3 = array();
            $datatext = array();
            
            $sql2 = "select form_id from forms where encounter = $eid and pid = ".$patients[0]->pid." and formdir = 'LBF2' and deleted = 0 ";      
            $stmt2 = $db->prepare($sql2) ;
            $stmt2->execute();                       
            $patients2 = $stmt2->fetchAll(PDO::FETCH_OBJ); 
            //print_r($patients2);
//            $forms1 = '';
//            foreach ($patients2 as $value) {
//               $forms1 .= $value->form_id.",";
//            }
//            $forms = rtrim($forms1, ',');
            if(!empty($patients2)){
                foreach ($patients2 as $value) { 
                    $sql3 = "SELECT field_id, title FROM layout_options WHERE form_id = 'LBF1'";
                    $stmt3 = $db->prepare($sql3) ;
                    $stmt3->execute(); 
                    $patients3 = $stmt3->fetchAll(PDO::FETCH_OBJ); 
                    
                    foreach($patients3 as $val){
                        $sql4 = "SELECT field_value from lbf_data WHERE form_id = $value->form_id AND field_id  = '".$val->field_id."_text'";
                        $stmt4 = $db->prepare($sql4) ;
                        $stmt4->execute(); 
                        $patients4 = $stmt4->fetchAll(PDO::FETCH_OBJ); 
                        if(empty($patients4)):
                            $datatext[] = (object)array('form_name' => $val->field_id, 'form_id' => $value->form_id, 'dictation_text' => '' );
                        else:
                            $datatext[] = (object)array('form_name' => $val->field_id, 'form_id' => $value->form_id, 'dictation_text' => $patients4[0]->field_value );
                        endif;
                    }
                }
            }else{
                    $sql3 = "SELECT field_id, title FROM layout_options WHERE form_id = 'LBF1'";
                    $stmt3 = $db->prepare($sql3) ;
                    $stmt3->execute(); 
                    $patients3 = $stmt3->fetchAll(PDO::FETCH_OBJ); 
                    
                    foreach($patients3 as $val){
                        if(empty($patients3)):
                            $datatext[] = (object)array('form_name' => $val->field_id, 'form_id' => 0, 'dictation_text' => '' );
                        else:
                            $datatext[] = (object)array('form_name' => $val->field_id, 'form_id' => 0, 'dictation_text' => '' );
                        endif;
                    }
            }
            
            //echo "<pre>";print_r($datatext); echo "</pre>";
            $newdemo1 = encode_demo($datatext);  
            $newdemo['Encounter Text Data'] = check_data_available($newdemo1);
            if($newdemo)
            {
                //returns patients 
                echo $patientres = json_encode($newdemo); 
                
                //echo $patientresult = GibberishAES::enc($patientres, $key);
            }
            else
            {
                //echo 'No patients available';
                $patientres = '[{"id":"0"}]';
                //echo $patientresult = GibberishAES::enc($patientres, $key);
            }
        } 
        catch(PDOException $e) 
        {
            
            $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
           // echo $patientresult = GibberishAES::enc($error, $key);
        }
}
function getDosFormDataDetailsTest($eid, $formname){
   	
//        $key = 'rotcoderaclla';
//        // The default key size is 256 bits.
//        $old_key_size = GibberishAES::size();
//        GibberishAES::size(256);
        try 
        {
            $db = getConnection();
            $newdemo = '';
            
            $patients2 = array();
            $patients3 = array();
            $datatext = array();
            
            $sql = "select form_id from forms where encounter = $eid and formdir = 'LBF2' and deleted = 0 ";      
            $stmt = $db->prepare($sql) ;
            $stmt->execute();                       
            $patients = $stmt->fetchAll(PDO::FETCH_OBJ); 
           
            if(!empty($patients)){
                foreach ($patients as $value) { 
                    $sql2 = "SELECT field_value from lbf_data WHERE form_id = $value->form_id AND field_id  = '".$formname."_text'";
                    $stmt2 = $db->prepare($sql2) ;
                    $stmt2->execute(); 
                    $patients2 = $stmt2->fetchAll(PDO::FETCH_OBJ); 
                    if(empty($patients2)):
                        $datatext[] = (object)array( 'form_id' => $value->form_id, 'dictation_text' => '' );
                    else:
                        $datatext[] = (object)array('form_id' => $value->form_id, 'dictation_text' => $patients2[0]->field_value );
                    endif;
                }
            }else{
                    $datatext[] = (object)array( 'form_id' => 0, 'dictation_text' => '' );
            }
            
            //echo "<pre>";print_r($datatext); echo "</pre>";
            $newdemo1 = encode_demo($datatext);  
            $newdemo['Encounter Text Data'] = check_data_available($newdemo1);
            if($newdemo)
            {
                //returns patients 
                echo $patientres = json_encode($newdemo); 
                
                //echo $patientresult = GibberishAES::enc($patientres, $key);
            }
            else
            {
                //echo 'No patients available';
                $patientres = '[{"id":"0"}]';
                //echo $patientresult = GibberishAES::enc($patientres, $key);
            }
        } 
        catch(PDOException $e) 
        {
            
            $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
           // echo $patientresult = GibberishAES::enc($error, $key);
        }
}
function getDosFormDataDetails1($pid){
    
    
	
//        $key = 'rotcoderaclla';
//        // The default key size is 256 bits.
//        $old_key_size = GibberishAES::size();
//        GibberishAES::size(256);
        try 
        {
            $db = getConnection();
            $newdemo = '';
            $sql = "select encounter from form_encounter where pid= $pid";      
            $stmt = $db->prepare($sql) ;
            $stmt->execute();                       
            $patients = $stmt->fetchAll(PDO::FETCH_OBJ); 
            $patients2 = array();
            $patients3 = array();
            $forms ='';
            foreach ($patients as $value) {
                $sql2 = "select form_id, encounter from forms where encounter = $value->encounter and pid = $pid and formdir = 'LBF2' and deleted = 0 ";      
                $stmt2 = $db->prepare($sql2) ;
                $stmt2->execute();                       
                $patients2 = $stmt2->fetchAll(PDO::FETCH_OBJ); 
                
                $forms1 = '';
                foreach ($patients2 as $value) {
                   $forms1 .= $value->form_id.",";
                }
                $forms = rtrim($forms1, ',');
                if(!empty($forms )){
                    $sql3 = "SELECT  * from lbf_data WHERE form_id IN ($forms) AND field_id  LIKE '%_text'";
                    $stmt3 = $db->prepare($sql3) ;
                    $stmt3->execute(); 
                    $patients3[$value->encounter] = $stmt3->fetchAll(PDO::FETCH_OBJ); 
                }else{
                    $patients3[$value->encounter] = array();
                }
                 
            }
            echo "<pre>";print_r($patients3); echo "</pre>";
            $newdemo1 = encode_demo($patients3);  
            $newdemo['Encounter Text Data'] = check_data_available($newdemo1);
            if($newdemo)
            {
                //returns patients 
                echo $patientres = json_encode($newdemo); 
                
                //echo $patientresult = GibberishAES::enc($patientres, $key);
            }
            else
            {
                //echo 'No patients available';
                $patientres = '[{"id":"0"}]';
                //echo $patientresult = GibberishAES::enc($patientres, $key);
            }
        } 
        catch(PDOException $e) 
        {
            
            $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
           // echo $patientresult = GibberishAES::enc($error, $key);
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
    
	$sql = "select pid,fname,lname,dob from patient_data WHERE  practice_status = 'YES' AND deceased_stat = 'NO'";      
        $count= "select count(*) from patient_data WHERE  practice_status = 'YES' AND deceased_stat = 'NO'";
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
                echo json_encode($providers); 
            }
            else
            {
                //echo 'No provider available';
                echo '[{"id":"0"}]';
            }
        } 
        catch(PDOException $e) 
        {
            
            echo '{"error":{"text":'. $e->getMessage() .'}}'; 
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

function getPatientAgencies($pid){
    $key = 'rotcoderaclla';
    // The default key size is 256 bits.
    $old_key_size = GibberishAES::size();
    GibberishAES::size(256);
    $sql = "SELECT tbl_patientagency.agencyid as OrganizationId, Organization,CONCAT(u.title,u.fname,' ',u.lname) as name, list_options.title as addressbook_type,abook_type as abook_type_value, phonew1 as workphone, phonecell, fax, email, street, city, state, zip,
            tbl_patientagency.facility_admitdate as admitdate,tbl_patientagency.facility_dischargedate as dischargedate ,tbl_patientagency.facility_isactive as isactive,tbl_patientagency.facility_notes as notes,tbl_patientagency.facility_related_links as related_links,tbl_patientagency.createdby,tbl_patientagency.createddate
            FROM users u
            INNER JOIN tbl_patientagency ON u.id = tbl_patientagency.agencyid
            INNER JOIN list_options ON u.abook_type = list_options.option_id
            AND list_id =  'abook_type'
            AND patientid = :pid";
	 
	try 
        {
            $db = getConnection();
            $stmt = $db->prepare($sql) ;
            $stmt->bindParam("pid", $pid);            
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
                 $agencydatares = '[{"OrganizationId":"0","organization":"","name":"","addressbook_type":"","abook_type_value":"","workphone":"","phonecell":"","fax":"","email":"","street":"","city":"","state":"","zip":""}';
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
        
        $db = getConnection();
        $flag=0;
        $request = Slim::getInstance()->request();
        $reminderArray = json_decode($request->getBody(),TRUE);
	
       $to = array();
        $to = $reminderArray['to'];  
        
        $pid=$reminderArray['pid'];
	$from = $reminderArray['from'];
        $message=$reminderArray['message'];
        $sent_date = $reminderArray['sent_date'];
        $due_date = $reminderArray['due_date'];
        $msg_status = $reminderArray['message_status'];
        $msg_priority = $reminderArray['message_priority'];
        //$processed_date = $reminderArray['processed_date'];
        //$processed_by = $reminderArray['processed_by'];
        
	$save_reminder= "INSERT INTO dated_reminders(dr_from_ID,dr_message_text,dr_message_sent_date,dr_message_due_date,pid,message_priority,message_processed,processed_date,dr_processed_by)
            VALUES(:from,:message,:sent_date,:due_date,:pid,:msg_priority,:message_processed,'0000-00-00 00:00:00',0)";
	
        /*echo $save_reminder = "INSERT INTO dated_reminders(dr_from_ID,dr_message_text,dr_message_sent_date,dr_message_due_date,pid,message_priority,message_processed,processed_date,dr_processed_by)
            VALUES(1,'message2','2014-11-06 08:20:34','2014-11-06 08:20:34',0,1,1,'0000-00-00 00:00:00',0)";
        */
	$q = $db->prepare($save_reminder);
        
        if($q->execute(array( ':dr_from_ID'=>$from,':pid'=>$pid,':message'=>$message, 'sent_date'=>$sent_date, 'due_date' =>$due_date ,'msg_priority'  => $msg_priority,'message_processed' => $msg_status))){  
            $lastId = $db->lastInsertId(); 
            foreach($to as $toId){
                
                /*$getlastinsertedid="SELECT MAX(id) as lastInsertedId from dated_reminders ";

                $db = getConnection();
                $stmt = $db->prepare($getlastinsertedid) ;           
                $stmt->execute();
                $lastid = $stmt->fetchAll(PDO::FETCH_OBJ);

                if($lastid)
                {
                    $lastId = $lastid[0]->lastInsertedId;
                    $save_reminder_link = "INSERT INTO dated_reminders_link (dr_id,to_id) VALUES ($lastId, $toId)";
                }
                */
                $save_reminder_link = "INSERT INTO dated_reminders_link (dr_id,to_id) VALUES ($lastId, $toId)";
                
                $q2 = $db->prepare($save_reminder_link);
                    if($q2->execute())
                    {
                        echo '[{"id":"1"}]';
                    }
                    else
                    {
                        echo '[{"id":"0"}]';
                    }
            }
        }else{
            echo '[{"id":"0"}]';
       }
        	
    }catch(PDOException $e){
	echo '{"error":{"text":'. $e->getMessage() .'}}'; 
    }
	
}

function getPatientInsuranceDataList($pid){
    $key = 'rotcoderaclla';
    // The default key size is 256 bits.
    $old_key_size = GibberishAES::size();
    GibberishAES::size(256);
    $sql = " SELECT i.id, i.type,i.provider,ic.name as insurancecompany_name,i.plan_name,i.policy_number,i.subscriber_lname,i.subscriber_mname, i.subscriber_fname,i.subscriber_relationship,
            i.subscriber_ss,i.subscriber_DOB,i.subscriber_street,i.subscriber_postal_code,i.subscriber_city, i.subscriber_state, i.subscriber_country, i.subscriber_country,
            i.subscriber_employer,i.subscriber_employer_street,i.subscriber_employer_postal_code,i.subscriber_employer_state,i.subscriber_employer_country, i.subscriber_employer_city,
            i.copay,i.date, i.subscriber_sex,i.accept_assignment,i.policy_type
            from insurance_data i 
            inner join insurance_companies ic on ic.id=i.provider
            where pid=:pid";
	 
	try 
        {
            $db = getConnection();
            $stmt = $db->prepare($sql) ;
            $stmt->bindParam("pid", $pid);            
            $stmt->execute();                       
             
            $insurancedata = $stmt->fetchAll(PDO::FETCH_OBJ);                        
            
            if($insurancedata)
            {
                //returns patientdata 
                $insurancedatares = json_encode($insurancedata); 
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
            //$sql = "SELECT count(id) as count FROM `form_encounter`  WHERE (`sensitivity` <> 'finalized' OR `sensitivity` IS NULL) AND provider_id=$providerid ";
            $sql = "SELECT count(id) as count FROM `form_encounter`  WHERE (`elec_signed_on` = '' AND `elec_signedby` = '') AND provider_id=$providerid ";
	 
            $stmt = $db->prepare($sql) ;
            $stmt->execute();                       
             
            $count = $stmt->fetchAll(PDO::FETCH_OBJ);                        
            
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
            $sql = "SELECT f.pid, p.lname, p.fname, GROUP_CONCAT( CASE WHEN (DATE_FORMAT( f.date, '%Y-%m-%d' ) <> '0000-00-00') THEN DATE_FORMAT( f.date, '%Y-%m-%d' ) END  ORDER BY f.date ASC) AS dos, COUNT( f.id ) AS encounter_count, (
 
                SELECT COUNT( encounter )
                FROM form_encounter
                WHERE p.pid = pid 
                ) AS visit_count
                FROM form_encounter f
                INNER JOIN patient_data p ON p.pid = f.pid
                WHERE (
                `elec_signed_on` = '' AND `elec_signedby` = ''
                )
                AND f.provider_id =$providerid
                AND  p.practice_status = 'YES' AND p.deceased_stat != 'YES'
                GROUP BY p.lname ";
            $stmt = $db->prepare($sql) ;
            $stmt->execute();                       
             
            $count = $stmt->fetchAll(PDO::FETCH_OBJ);                        
            
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
function getIncompleteEncounterList($pid){
    
	try 
        {
            $db = getConnection();
            $key = 'rotcoderaclla';
            // The default key size is 256 bits.
            $old_key_size = GibberishAES::size();
            GibberishAES::size(256);

//            $sql1 = "SELECT field_id FROM  `layout_options` WHERE  `form_id` =  'LBF1'";
//            $stmt = $db->prepare($sql1) ;
//            $stmt->execute(); 
//            $formlabels = $stmt->fetchAll(PDO::FETCH_OBJ); 
//            $fieldstatus = array();
//            foreach($formlabels as $element):
//                $sql2 = "SELECT * FROM layout_options WHERE `form_id` =  'LBF2' AND field_id = '".$element->field_id."_stat'";
//                $stmt2 = $db->prepare($sql2) ;
//                $stmt2->execute(); 
//                $formfields = $stmt2->fetchAll(PDO::FETCH_OBJ); 
//                //echo "<pre>"; print_r($formfields); echo "</pre>";
//                foreach($formfields as $fieldvalue):
//                    if(strpos($fieldvalue->field_id,'_stat') !== false):
//                        $fieldstatus[] = $fieldvalue->field_id;
//                    endif;
//                endforeach;
//            endforeach;
//            print_r($fieldstatus);
//            $sql = "SELECT facility, encounter,pc_catid AS visitcategory_id,DATE_FORMAT( date,  '%Y-%m-%d' ) AS dos
//                        FROM form_encounter
//                        WHERE pid =$pid
//                            AND (
//                            sensitivity <>  'finalized'
//                            OR sensitivity =  ''
//                            ) ORDER BY date";
            $sql = "SELECT facility, encounter,pc_catid AS visitcategory_id,DATE_FORMAT( date,  '%Y-%m-%d' ) AS dos
                        FROM form_encounter
                        WHERE pid =$pid
                            AND (
                             `elec_signed_on` = '' AND `elec_signedby` = ''
                            ) ORDER BY date";
            $stmt = $db->prepare($sql) ;
            $stmt->execute(); 
            $formlabels = $stmt->fetchAll(PDO::FETCH_OBJ); 
            $formfields = array();
            $formvalues = array();
            $datacheck8= array();
            $datacheck7 = array();
            $dataArr = array();
            foreach($formlabels as $element): 
                $sql2 = "SELECT form_id 
                        FROM forms
                        WHERE pid = $pid and encounter = $element->encounter and deleted =0 and formdir = 'LBF2'";
                $stmt2 = $db->prepare($sql2);
                $stmt2->execute(); 
                $datacheck = $stmt2->fetchAll(PDO::FETCH_OBJ); 
                $incompleteforms = '';
                if(!empty($datacheck)):
                    $sql3 = "SELECT field_id, title FROM layout_options WHERE form_id = 'LBF1' and uor = 2";
                    $stmt3 = $db->prepare($sql3);
                    $stmt3->execute(); 
                    $datacheck2 = $stmt3->fetchAll(PDO::FETCH_OBJ); 
                    if(!empty($datacheck2)):
                       for($i=0; $i<count($datacheck2); $i++){
                            $sql4 = "SELECT lo.field_id, lo.title, lbf.field_value  FROM lbf_data lbf
                                    INNER JOIN layout_options lo ON lo.field_id = lbf.field_id 
                                    WHERE lbf.form_id = ".$datacheck[0]->form_id ." 
                                    AND lbf.field_id  LIKE '".$datacheck2[$i]->field_id."_stat' 
                                    AND lbf.field_value LIKE '%finalized%' AND lo.form_id = 'LBF2'  ";
                            $stmt4 = $db->prepare($sql4);
                            $stmt4->execute(); 
                            $datacheck3 = $stmt4->fetchAll(PDO::FETCH_OBJ); 
                            if(!empty($datacheck3)):
                               if($datacheck2[$i]->field_id."_stat" == $datacheck3[0]->field_id):
                                   //echo $datacheck3[0]->field_id;
                               endif;
                            else:
                                $sql5 = "SELECT substring(group_name from 2) as group_name FROM layout_options WHERE form_id = 'LBF2' and field_id LIKE '". $datacheck2[$i]->field_id."%' GROUP BY group_name";
                                $stmt5 = $db->prepare($sql5);
                                $stmt5->execute(); 
                                $datacheck5 = $stmt5->fetchAll(PDO::FETCH_OBJ);
                                
                                //$datacheck5 = array_filter($datacheck5,'unique_group');
                                
                                if(!empty($datacheck5)):
                                    //echo "<pre>"; print_r($dataArr); echo "</pre>";
                                    $incompleteforms .= $datacheck5[0]->group_name.",";
                                endif;
                            endif;
                        }
                    endif;
                    
                endif;
//                $sql6 = "SELECT DISTINCT(fe.encounter),CONCAT(pd.title,pd.fname,' ',pd.lname) as pname,DATE_FORMAT( fe.date,  '%Y-%m-%d' ) AS dos, fe.facility AS facility, fe.pid AS pid, fe.pc_catid AS visitcategory_id,oe.pc_catname as visitcategory
//                                FROM form_encounter fe
//                                INNER JOIN patient_data pd on pd.pid = fe.pid
//                                INNER JOIN openemr_postcalendar_categories oe ON oe.pc_catid = fe.pc_catid
//                                WHERE fe.pid = $pid and fe.encounter = $element->encounter 
//                                AND (
//                                fe.sensitivity <>  'finalized'
//                                OR fe.sensitivity =  ''
//                                ) ";
                 $sql6 = "SELECT DISTINCT(fe.encounter),CONCAT(pd.title,pd.fname,' ',pd.lname) as pname,DATE_FORMAT( fe.date,  '%Y-%m-%d' ) AS dos, fe.facility AS facility, fe.pid AS pid, fe.pc_catid AS visitcategory_id,oe.pc_catname as visitcategory
                    FROM form_encounter fe
                    INNER JOIN patient_data pd on pd.pid = fe.pid
                    INNER JOIN openemr_postcalendar_categories oe ON oe.pc_catid = fe.pc_catid
                    WHERE fe.pid = $pid and fe.encounter = $element->encounter 
                    AND (
                     `elec_signed_on` = '' AND `elec_signedby` = ''
                    ) AND  pd.practice_status = 'YES' AND pd.deceased_stat != 'YES' ";
                            $stmt6 = $db->prepare($sql6);
                            $stmt6->execute(); 
                            $datacheck6 = $stmt6->fetchAll(PDO::FETCH_OBJ);
                            if(empty($datacheck6)):
                               
                            endif;
                            if($incompleteforms == '' && empty($datacheck)):
                                $sql7 = "SELECT field_id  FROM layout_options WHERE form_id = 'LBF1' and uor = 2";
                                $stmt7 = $db->prepare($sql7);
                                $stmt7->execute(); 
                                $datacheck7 = $stmt7->fetchAll(PDO::FETCH_OBJ);
                                foreach ($datacheck7 as $key => $value){
                                    $sql8 = "SELECT substring(group_name from 2) as group_name FROM layout_options WHERE form_id = 'LBF2' and field_id LIKE '".$value->field_id."_stat'";
                                    $stmt8 = $db->prepare($sql8);
                                    $stmt8->execute(); 
                                    $datacheck8[] = $stmt8->fetchAll(PDO::FETCH_OBJ);
                                }
                                foreach ($datacheck8 as $key => $val){
                                    if(!empty($val)):
                                        $incompleteforms .= $val[0]->group_name.",";
                                    endif;
                                }
                            endif;
                            $incompleteforms = rtrim($incompleteforms, ',');
                            $incompleteforms = implode(',',array_unique(explode(',', $incompleteforms)));
                            //$formfields[] = array((object) array( 'incompleteforms' => $incompleteforms));
                            foreach ($datacheck6 as $key => $value){
                                $value->incompleteforms = $incompleteforms;
                            }
                            $formfields[] = $datacheck6;
            endforeach;
            
            //echo "<pre>"; print_r($formfields); echo "</pre>";
           
                $newdemo1=encode_demo(array_filter($formfields));
                $newdemo['EncounterData'] = check_data_available($newdemo1);
            
           /*$stmt = $db->prepare($sql) ;
            $stmt->execute();                       
             
            $incompletelist = $stmt->fetchAll(PDO::FETCH_OBJ); 
            
            //$out = array();
            $i = 0;
            foreach($incompletelist as $element):
                //$sql2 .= " AND fe.encounter = ".$enc->encounter;
                //echo $element->encounter . " -- " . $element->pc_catid . " -- " . $element->formid . " -- " . $element->formname . "<br>";
                $out[$element->encounter][$i]['pc_catid'] = $element->pc_catid;
                $out[$element->encounter][$i]['formid'] = $element->formid;
                $out[$element->encounter][$i]['formname'] = $element->formname;
                $i++;
            endforeach;*/
            //echo "<pre>"; print_r($out); echo "</pre>";
//            $stmt2 = $db->prepare($sql2) ;
//            $stmt2->execute();
//            $incompletelist['incomplete_forms'][] = $stmt2->fetchAll(PDO::FETCH_OBJ); 
            //echo "<pre>"; print_r($newdemo); echo "</pre>";
            if($newdemo1)
            {
                //returns count 
                echo $newdemores = json_encode($newdemo);
                //echo $newdemoresult = GibberishAES::enc($newdemores, $key);
            }
            else
            {
               echo $incompletelist = '[{"id":"0"}]';
               //echo $incompletelistresult = GibberishAES::enc($incompletelist, $key);
            }
        } 
        catch(PDOException $e) 
        {
            
           echo $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
            //echo $incompletelistresult = GibberishAES::enc($error, $key);
        }
   
}

function getIncompleteEncounterList_old($pid){
    
	try 
        {
            $db = getConnection();
           $key = 'rotcoderaclla';
            // The default key size is 256 bits.
            $old_key_size = GibberishAES::size();
            GibberishAES::size(256);

//            $sql1 = "SELECT field_id FROM  `layout_options` WHERE  `form_id` =  'LBF1'";
//            $stmt = $db->prepare($sql1) ;
//            $stmt->execute(); 
//            $formlabels = $stmt->fetchAll(PDO::FETCH_OBJ); 
//            $fieldstatus = array();
//            foreach($formlabels as $element):
//                $sql2 = "SELECT * FROM layout_options WHERE `form_id` =  'LBF2' AND field_id = '".$element->field_id."_stat'";
//                $stmt2 = $db->prepare($sql2) ;
//                $stmt2->execute(); 
//                $formfields = $stmt2->fetchAll(PDO::FETCH_OBJ); 
//                //echo "<pre>"; print_r($formfields); echo "</pre>";
//                foreach($formfields as $fieldvalue):
//                    if(strpos($fieldvalue->field_id,'_stat') !== false):
//                        $fieldstatus[] = $fieldvalue->field_id;
//                    endif;
//                endforeach;
//            endforeach;
//            print_r($fieldstatus);
            $query = "SELECT encounter
                        FROM form_encounter
                        WHERE pid =$pid
                            AND (
                            sensitivity <>  'finalized'
                            OR sensitivity =  ''
                            ) ORDER BY date";
            
            $stmt = $db->prepare($query) ;
            $stmt->execute(); 
            $formlabels = $stmt->fetchAll(PDO::FETCH_OBJ); 
            $formfields = array();
            foreach($formlabels as $element):
                $sql = "SELECT DISTINCT(fe.encounter),CONCAT(pd.title,pd.fname,' ',pd.lname) as pname,DATE_FORMAT( fe.date,  '%Y-%m-%d' ) AS dos, fe.facility AS facility, fe.pid AS pid, fe.pc_catid AS visitcategory_id,oe.pc_catname as visitcategory,
                    (SELECT GROUP_CONCAT( DISTINCT(SUBSTRING( lo.group_name
                            FROM 2 ) )) 
                            FROM layout_options lo
                            INNER JOIN lbf_data lf ON lo.field_id = lf.field_id
                            INNER JOIN forms f ON f.form_id = lf.form_id
                            LEFT JOIN layout_options l ON l.form_id =  'LBF1'
                            AND SUBSTRING( l.title
                            FROM 2 ) LIKE lo.group_name
                            AND l.uor =2
                            WHERE lo.form_id =  'LBF2'
                            AND f.encounter = $element->encounter
                            AND lf.field_value =  'pending' 
                            AND lo.field_id LIKE  '%_stat') as incompleteforms
                        FROM form_encounter fe
                        INNER JOIN forms f ON fe.encounter = f.encounter
                        INNER JOIN lbf_data lf ON f.form_id = lf.form_id
                        LEFT JOIN layout_options lp ON lp.field_id = lf.field_id
                        INNER JOIN patient_data pd on pd.pid = fe.pid
                        INNER JOIN openemr_postcalendar_categories oe ON oe.pc_catid = fe.pc_catid
                        INNER JOIN layout_options l ON l.form_id='LBF1' AND l.uor=2
                        WHERE fe.pid = $pid and fe.encounter = $element->encounter 
                        AND (
                        fe.sensitivity <>  'finalized'
                        OR fe.sensitivity =  ''
                        )
                        AND f.formdir =  'LBF2'
                        AND f.deleted =0"; 
                $stmt2 = $db->prepare($sql) ;
                $stmt2->execute(); 
               // if($stmt2->rowCount() > 0): 
                $count = $stmt2->fetchAll(PDO::FETCH_OBJ); 
                if($count):
                    //echo "Has encounter form filled";
                    $formfields[] = $count;
                else:
                    $sql = "SELECT DISTINCT(fe.encounter),CONCAT(pd.title,pd.fname,' ',pd.lname) as pname,DATE_FORMAT( fe.date,  '%Y-%m-%d' ) AS dos, fe.facility AS facility, fe.pid AS pid, fe.pc_catid AS visitcategory_id,oe.pc_catname as visitcategory,
                    (SELECT GROUP_CONCAT( DISTINCT(SUBSTRING( lo.group_name
                            FROM 2 ) )) 
                            FROM layout_options lo WHERE lo.form_id =  'LBF2'
                            AND lo.field_id LIKE  '%_stat') as incompleteforms
                        FROM form_encounter fe
                        INNER JOIN patient_data pd on pd.pid = fe.pid
                        INNER JOIN openemr_postcalendar_categories oe ON oe.pc_catid = fe.pc_catid
                        INNER JOIN layout_options l ON l.form_id='LBF1' AND l.uor=2
                        WHERE fe.pid = $pid and fe.encounter = $element->encounter 
                        AND (
                        fe.sensitivity <>  'finalized'
                        OR fe.sensitivity =  ''
                        )
                        "; 
                        $stmt3 = $db->prepare($sql) ;
                        $stmt3->execute(); 
                       // if($stmt2->rowCount() > 0): 
                        $count = $stmt3->fetchAll(PDO::FETCH_OBJ); 
                        $formfields[] = $count;
                endif;
                
                    
//                else: 
//                    $formfields[] = array((object) array('encounter' => $element->encounter,'incompleteforms' => ''));
//                endif;
            endforeach;
            //echo "<pre>"; print_r($formfields); echo "</pre>";
           
                $newdemo1=encode_demo(array_filter($formfields));
                $newdemo['EncounterData'] = check_data_available($newdemo1);
            
           /*$stmt = $db->prepare($sql) ;
            $stmt->execute();                       
             
            $incompletelist = $stmt->fetchAll(PDO::FETCH_OBJ); 
            
            //$out = array();
            $i = 0;
            foreach($incompletelist as $element):
                //$sql2 .= " AND fe.encounter = ".$enc->encounter;
                //echo $element->encounter . " -- " . $element->pc_catid . " -- " . $element->formid . " -- " . $element->formname . "<br>";
                $out[$element->encounter][$i]['pc_catid'] = $element->pc_catid;
                $out[$element->encounter][$i]['formid'] = $element->formid;
                $out[$element->encounter][$i]['formname'] = $element->formname;
                $i++;
            endforeach;*/
            //echo "<pre>"; print_r($out); echo "</pre>";
//            $stmt2 = $db->prepare($sql2) ;
//            $stmt2->execute();
//            $incompletelist['incomplete_forms'][] = $stmt2->fetchAll(PDO::FETCH_OBJ); 
            //echo "<pre>"; print_r($newdemo); echo "</pre>";
            if($newdemo1)
            {
                //returns count 
                echo $newdemores = json_encode($newdemo);
                //echo $newdemoresult = GibberishAES::enc($newdemores, $key);
            }
            else
            {
               $incompletelist = '[{"id":"0"}]';
               echo $incompletelistresult = GibberishAES::enc($incompletelist, $key);
            }
        } 
        catch(PDOException $e) 
        {
            
            $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
            echo $incompletelistresult = GibberishAES::enc($error, $key);
        }
   
}

// incomplete encounter list
function getIncompleteEncounterList1($pid){
    
	try 
        {
            $db = getConnection();
           $key = 'rotcoderaclla';
            // The default key size is 256 bits.
            $old_key_size = GibberishAES::size();
            GibberishAES::size(256);
//            $sql = "SELECT fe.encounter,CONCAT(pd.title,pd.fname,' ',pd.lname) as pname,DATE_FORMAT( fe.date, '%Y-%m-%d' ) as dos,fe.facility as facility,fe.pid as pid,fe.pc_catid as visitcategory_id,oe.pc_catname as visitcategory,t.form_id as formid, t.form_name as formname
//                    FROM tbl_allcare_formflag t
//                    INNER JOIN form_encounter fe ON t.encounter_id = fe.encounter
//                    INNER JOIN patient_data pd on pd.pid = fe.pid
//                    INNER JOIN openemr_postcalendar_categories oe ON oe.pc_catid = fe.pc_catid
//                    WHERE (fe.sensitivity <>  'finalized'
//                    OR  fe.sensitivity IS NULL)
//                    AND fe.pid =$pid
//                    AND t.finalized =  'N' ORDER BY fe.encounter";
//            $sql= "SELECT DISTINCT ( fe.encounter), CONCAT(pd.title,pd.fname,' ',pd.lname) as pname,DATE_FORMAT( fe.date,  '%Y-%m-%d' ) AS dos, fe.facility AS facility, fe.pid AS pid, fe.pc_catid AS visitcategory_id,oe.pc_catname as visitcategory,(
//                    SELECT GROUP_CONCAT( DISTINCT (form_name) ) FROM tbl_allcare_formflag WHERE encounter_id = fe.encounter ) AS incompleteforms
//                    FROM tbl_allcare_formflag t
//                    INNER JOIN form_encounter fe ON t.encounter_id = fe.encounter
//                    INNER JOIN patient_data pd on pd.pid = fe.pid
//                    INNER JOIN openemr_postcalendar_categories oe ON oe.pc_catid = fe.pc_catid
//                    WHERE (
//                    fe.sensitivity <>  'finalized'
//                    OR fe.sensitivity IS NULL
//                    )
//                    AND fe.pid =$pid
//                    AND t.finalized =  'N'
//                    ORDER BY fe.encounter";
            $sql = "SELECT DISTINCT( fe.encounter), CONCAT(pd.title,pd.fname,' ',pd.lname) as pname,DATE_FORMAT( fe.date,  '%Y-%m-%d' ) AS dos, fe.facility AS facility, fe.pid AS pid, fe.pc_catid AS visitcategory_id,oe.pc_catname as visitcategory,(
                    SELECT GROUP_CONCAT( DISTINCT (title) ) FROM layout_options l inner join tbl_allcare_formflag t on l.title= t.form_name WHERE l.title = t.form_name AND l.form_id='LBF1' AND l.uor=2) AS incompleteforms
                    FROM tbl_allcare_formflag t 
                    INNER JOIN form_encounter fe ON t.encounter_id = fe.encounter
                    INNER JOIN patient_data pd on pd.pid = fe.pid
                    INNER JOIN openemr_postcalendar_categories oe ON oe.pc_catid = fe.pc_catid
		    INNER JOIN layout_options l ON l.title = t.form_name AND l.form_id='LBF1' AND l.uor=2
                    WHERE (
                    fe.sensitivity <>  'finalized'
                    OR fe.sensitivity IS NULL
                    )
                    AND fe.pid =$pid
                    AND t.finalized =  'N'
                    ORDER BY fe.encounter";
            $demo1=sql_execute($pid,$sql);
            $newdemo1=encode_demo($demo1);      
            $newdemo['EncounterData'] = check_data_available($newdemo1);
           
           /*$stmt = $db->prepare($sql) ;
            $stmt->execute();                       
             
            $incompletelist = $stmt->fetchAll(PDO::FETCH_OBJ); 
            
            //$out = array();
            $i = 0;
            foreach($incompletelist as $element):
                //$sql2 .= " AND fe.encounter = ".$enc->encounter;
                //echo $element->encounter . " -- " . $element->pc_catid . " -- " . $element->formid . " -- " . $element->formname . "<br>";
                $out[$element->encounter][$i]['pc_catid'] = $element->pc_catid;
                $out[$element->encounter][$i]['formid'] = $element->formid;
                $out[$element->encounter][$i]['formname'] = $element->formname;
                $i++;
            endforeach;*/
            //echo "<pre>"; print_r($out); echo "</pre>";
//            $stmt2 = $db->prepare($sql2) ;
//            $stmt2->execute();
//            $incompletelist['incomplete_forms'][] = $stmt2->fetchAll(PDO::FETCH_OBJ); 
            
            if($newdemo1)
            {
                //returns count 
                 echo $newdemores = json_encode($newdemo);
                 echo $newdemoresult = GibberishAES::enc($newdemores, $key);
            }
            else
            {
                $incompletelist = '[{"id":"0"}]';
                echo $incompletelistresult = GibberishAES::enc($incompletelist, $key);
            }
        } 
        catch(PDOException $e) 
        {
            
             $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
             echo $incompletelistresult = GibberishAES::enc($error, $key);
        }
   
}

function updatePatientHistory(){
        
    try{
        
        $db = getConnection();
        $flag=0;
        $request = Slim::getInstance()->request();
        $key = 'rotcoderaclla';
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        $appres = GibberishAES::dec($request->getBody(), $key);
        //$appres = GibberishAES::dec('U2FsdGVkX194QXnEmmvNgw52VLjcXNhkK+rj1kuLATPaNfYYYIjX+Nx4vYcV4wQYi0B/pGYnKu6tMHGmARr4vTbsfc8Z6BnExG/0z8ruu+sz1hA7lA+mmKSjQOMD0dHBBHPesGNxUn7Dkf6feAJn3+Jx2dP5HDf4N8VsMk5G+bcDYglB+3uqtms9UIx19dS73IFJ8vFpsxKztOoslPyWi1apSR9fsn07gRrx8B3vkaqJ9Vc1KRd/MaeFq8sZgwrcyITt2MjoSgdvKJjiffZl6A==', $key);
        $insertArray = json_decode($appres,TRUE);

        $group_name = str_replace('_', ' ', $insertArray['group_name']);
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
        
        if($insertval){  
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
function updatePatientHistory_2(){
        
    try{
        
        $db = getConnection();
        $flag=0;
        $request = Slim::getInstance()->request();
        $key = 'rotcoderaclla';
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        $appres = GibberishAES::dec($request->getBody(), $key);
        $insertArray = json_decode($appres,TRUE);
        $nameslist2 = 'pid';
        $namesvalues2 = $insertArray['pid'];
        $group_name = str_replace('_', ' ', $insertArray['group_name']);
        $getcolumnNames = "SELECT field_id FROM layout_options WHERE form_id = 'HIS' AND group_name = '$group_name'";
        $stmt = $db->prepare($getcolumnNames) ;
        $stmt->execute();                       

        $getnames = $stmt->fetchAll(PDO::FETCH_OBJ); 
        foreach ($getnames as $value) {
            $valuename = '$'.$value->field_id;
            ${$value->field_id} = $insertArray[$value->field_id];
        }
        
        foreach ($getnames as $value) {
            $nameslist2 .=  $value->field_id.",";
            $namesvalues2 .= "'".${$value->field_id}."',";
        }
        $nameslist = rtrim($nameslist2, ',');
        $namesvalues = rtrim($namesvalues2, ',');
        $insert = "INSERT INTO history_data ($nameslist) VALUES($namesvalues)";
        $stmt2 = $db->prepare($insert) ;
        $stmt2->execute();                       

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
function updatePatientHistory2(){
        
    try{
        
        $db = getConnection();
        $flag=0;
        $request = Slim::getInstance()->request();
        $key = 'rotcoderaclla';
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        $appres = GibberishAES::dec($request->getBody(), $key);
        $insertArray = json_decode($appres,TRUE);
			
        $coffee               = $insertArray['coffee1'].'|'.$insertArray['coffee2'].'|'.$insertArray['coffee3'];
        $tobacco              = $insertArray['tobacco1'].'|'.$insertArray['tobacco2'].'|'.$insertArray['tobacco3'];
        $alcohol              = $insertArray['alcohol1'].'|'.$insertArray['alcohol2'].'|'.$insertArray['alcohol3'];
        $sleep_patterns       = $insertArray['sleep_patterns'];
        $exercise_patterns    = $insertArray['exercise_patterns1'].'|'.$insertArray['exercise_patterns2'].'|'.$insertArray['exercise_patterns3'];
	$seatbelt_use         = $insertArray['seatbelt_use'];
        $counseling           = $insertArray['counseling1'].'|'.$insertArray['counseling2'].'|'.$insertArray['counseling3'];
        $hazardous_activities = $insertArray['hazardous_activities1'].'|'.$insertArray['hazardous_activities2'].'|'.$insertArray['hazardous_activities3'];
        $recreational_drugs   = $insertArray['recreational_drugs1'].'|'.$insertArray['recreational_drugs2'].'|'.$insertArray['recreational_drugs3'];
        
        $history_mother       = $insertArray['history_mother'];
        $history_father       = $insertArray['history_father'];
        $history_siblings     = $insertArray['history_siblings'];
        $history_offspring    = $insertArray['history_offspring'];
        $history_spouse       = $insertArray['history_spouse'];
        
        $relatives_cancer               = $insertArray['relatives_cancer'];
        $relatives_tuberculosis         = $insertArray['relatives_tuberculosis'];
        $relatives_diabetes             = $insertArray['relatives_diabetes'];
        $relatives_high_blood_pressure  = $insertArray['relatives_high_blood_pressure'];
        $relatives_heart_problems       = $insertArray['relatives_heart_problems'];
        
        $relatives_stroke               = $insertArray['relatives_stroke'];
        $relatives_epilepsy             = $insertArray['relatives_epilepsy'];
        $relatives_mental_illness       = $insertArray['relatives_mental_illness'];
        $relatives_suicide              = $insertArray['relatives_suicide'];
        
        $cataract_surgery       = $insertArray['cataract_surgery'];
        $tonsillectomy          = $insertArray['tonsillectomy'];
        $cholecystestomy        = $insertArray['cholecystestomy'];
        $heart_surgery          = $insertArray['heart_surgery'];
        $hysterectomy           = $insertArray['hysterectomy'];
        $hernia_repair          = $insertArray['hernia_repair'];
        $hip_replacement        = $insertArray['hip_replacement'];
        $knee_replacement       = $insertArray['knee_replacement'];
        $appendectomy           = $insertArray['appendectomy'];
        $date                   = $insertArray['date'];
        $pid                    = $insertArray['pid'];
        $name_1                 = $insertArray['name_1'];
        $value_1                = $insertArray['value_1'];
        $name_2                 = $insertArray['name_2'];
        $value_2                = $insertArray['value_2'];
        $additional_history     = $insertArray['additional_history'];
        $exam                   = "brs:".$insertArray['brsval'].":".$insertArray['brstext']."|cec:".$insertArray['cecval'].":".$insertArray['cectext']."|ecg:".$insertArray['ecgval'].":".$insertArray['ecgtext']."|gyn:".$insertArray['gynval'].":".$insertArray['gyntext']."|mam:".$insertArray['mamval'].":".$insertArray['mamtext']."|phy:".$insertArray['phyval'].":".$insertArray['phytext']."|pro:".$insertArray['proval'].":".$insertArray['protext']."|rec:".$insertArray['recval'].":".$insertArray['rectext']."|sic:".$insertArray['sicval'].":".$insertArray['sictext']."|ret:".$insertArray['retval'].":".$insertArray['rettext']."|flu:".$insertArray['fluval'].":".$insertArray['flutext']."|pne:".$insertArray['pneval'].":".$insertArray['pnetext']."|ldl:".$insertArray['idlval'].":".$insertArray['idltext']."|hem:".$insertArray['hemval'].":".$insertArray['hemtext']."|psa:".$insertArray['psaval'].":".$insertArray['psatext'];
        $usertext11             = $insertArray['usertext11'];
        
        
	
       $sql="INSERT INTO `history_data`( `coffee`, `tobacco`, `alcohol`, `sleep_patterns`, `exercise_patterns`, `seatbelt_use`, `counseling`,
                `hazardous_activities`, `recreational_drugs`, `last_breast_exam`, `last_mammogram`, `last_gynocological_exam`, `last_rectal_exam`, 
                `last_prostate_exam`, `last_physical_exam`, `last_sigmoidoscopy_colonoscopy`, `last_ecg`, `last_cardiac_echo`, `last_retinal`, 
                `last_fluvax`, `last_pneuvax`, `last_ldl`, `last_hemoglobin`, `last_psa`, `last_exam_results`, `history_mother`, `history_father`, 
                `history_siblings`, `history_offspring`, `history_spouse`, `relatives_cancer`, `relatives_tuberculosis`, `relatives_diabetes`,
                `relatives_high_blood_pressure`, `relatives_heart_problems`, `relatives_stroke`, `relatives_epilepsy`, `relatives_mental_illness`, 
                `relatives_suicide`, `cataract_surgery`, `tonsillectomy`, `cholecystestomy`, `heart_surgery`, `hysterectomy`, `hernia_repair`, 
                `hip_replacement`, `knee_replacement`, `appendectomy`, `date`, `pid`, `name_1`, `value_1`, `name_2`, `value_2`, `additional_history`,
                `exams`, `usertext11`, `usertext12`, `usertext13`, `usertext14`, `usertext15`, `usertext16`, `usertext17`, `usertext18`, `usertext19`,
                `usertext20`, `usertext21`, `usertext22`, `usertext23`, `usertext24`, `usertext25`, `usertext26`, `usertext27`, `usertext28`, `usertext29`,
                `usertext30`, `userdate11`, `userdate12`, `userdate13`, `userdate14`, `userdate15`, `userarea11`, `userarea12`) 
             VALUES (:coffee,:tobacco,:alcohol,:sleep_patterns,:exercise_patterns,:seatbelt_use,:counseling,:hazardous_activities,:recreational_drugs,
                'NULL', 'NULL', 'NULL', 'NULL', 'NULL', 'NULL', 'NULL', 'NULL', 'NULL', 'NULL', 'NULL', 'NULL', 'NULL', 'NULL', 'NULL', 'NULL',:history_mother,
                :history_father,:history_siblings,:history_offspring,:history_spouse,:relatives_cancer,:relatives_tuberculosis,:relatives_diabetes,
                :relatives_high_blood_pressure,:relatives_heart_problems,:relatives_stroke,:relatives_epilepsy,:relatives_mental_illness,:relatives_suicide,
                :cataract_surgery,:tonsillectomy,:cholecystestomy,:heart_surgery,:hysterectomy,:hernia_repair,:hip_replacement,:knee_replacement,:appendectomy,:date,
                :pid,:name_1,:value_1,:name_2,:value_2,:additional_history,:exam,:usertext11,'','','','','','','','','','','','','','','','','','','',
                'NULL','NULL','NULL','NULL','NULL','','')";
			
	$q = $db->prepare($sql);

        if($q->execute(array( ':coffee'=>$coffee,':tobacco'=>$tobacco,':alcohol'=>$alcohol,':sleep_patterns'=>$sleep_patterns,':exercise_patterns'=>$exercise_patterns,':seatbelt_use'=>$seatbelt_use,':counseling'=>$counseling,':hazardous_activities'=>$hazardous_activities,':recreational_drugs' =>$recreational_drugs,
            ':history_mother'=>$history_mother,':history_father'=>$history_father,':history_siblings'=>$history_siblings,':history_offspring'=>$history_offspring,
            ':history_spouse'=>$history_spouse,':relatives_cancer'=>$relatives_cancer,':relatives_tuberculosis'=>$relatives_tuberculosis,':relatives_diabetes'=>$relatives_diabetes,
            ':relatives_high_blood_pressure'=>$relatives_high_blood_pressure,':relatives_heart_problems'=>$relatives_heart_problems,':relatives_stroke'=>$relatives_stroke,
            ':relatives_epilepsy'=>$relatives_epilepsy,':relatives_mental_illness'=>$relatives_mental_illness,':relatives_suicide'=>$relatives_suicide,
            ':cataract_surgery'=>$cataract_surgery,':tonsillectomy'=>$tonsillectomy,':cholecystestomy'=>$cholecystestomy,':heart_surgery'=>$heart_surgery, 
            ':hysterectomy'=>$hysterectomy,':hernia_repair'=>$hernia_repair,':hip_replacement'=>$hip_replacement,':knee_replacement'=>$knee_replacement,
            ':appendectomy'=>$appendectomy,':date'=>$date,':pid'=>$pid,':name_1'=>$name_1,':value_1'=>$value_1,':name_2'=>$name_2,':value_2'=>$value_2,
            ':additional_history'=>$additional_history,':exam'=>$exam,':usertext11'=>$usertext11 
        ))){  
                echo '[{"id":"1"}]';
	}else{
            echo '[{"id":"0"}]';
	}
		
    }catch(PDOException $e){
	echo '{"error":{"text":'. $e->getMessage() .'}}'; 
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

function getDosVisitList($pid){
    try 
    {
        $db = getConnection();
        $key = 'rotcoderaclla';
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        $sql = "SELECT f.encounter as eid, DATE_FORMAT(f.date,'%Y-%m-%d') as dos, o.pc_catname as visitcategory
                FROM form_encounter f
                INNER JOIN openemr_postcalendar_categories o
                WHERE pid =$pid
                AND o.pc_catid = f.pc_catid";

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
// to get form data based on dos
function getDosFormData($eid){
  try 
    {
        $db = getConnection();
        $key = 'rotcoderaclla';
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        $sql = "SELECT pc_catid, provider_id, facility_id, pid
                FROM  `form_encounter` 
                WHERE encounter =$eid";
        
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
            
            $sql2 ="SELECT f.screen_group, l.title AS screen_name,f.screen_group, l.description AS screen_link
                FROM  `tbl_allcare_facuservisit` f
                INNER JOIN layout_options l ON l.group_name = f.screen_group
                AND l.form_id = f.form_id
                WHERE  `facilities` REGEXP ('$value->facility_id') AND  `users` REGEXP ('$value->provider_id') AND  `visit_categories` REGEXP ('$value->pc_catid')";      
   
            $stmt2 = $db->prepare($sql2); 
            $stmt2->execute();
            $fieldnamesresult[] = $stmt2->fetchAll(PDO::FETCH_OBJ);
            
        } 
        foreach ($fieldnamesresult as $value2) {
            
            foreach ($value2 as $value3) {
                
//                echo $sql3 ="SELECT f.form_id,f.form_name,l.seq as FormOrder,SUBSTRING(l.group_name ,-length(l.group_name),1) as grouporder,SUBSTRING(l.group_name FROM 2) as GroupName,
//                        CASE l.uor
//                        WHEN 0 THEN 'UnUsed' 
//                        WHEN 1 THEN 'Optional'
//                        WHEN 2 THEN 'Required'
//                        END as isRequired, l.description,
//                        CASE t.finalized
//                        WHEN 'Y' THEN 'Completed'
//                        WHEN 'N' THEN 'Pending'
//                        END as formStatus
//                        from forms f
//                        INNER JOIN layout_options l on l.title = '$value3->screen_name' and l.form_id='LBF1'
//                        INNER JOIN tbl_allcare_formflag t ON t.encounter_id = $eid and f.form_id = t.form_id
//                        where f.formdir='$value3->screen_name' and f.encounter=$eid  and f.pid=$pid";
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
                    $sql4 = "SELECT (SELECT DISTINCT(SUBSTRING(lo.group_name FROM 2)) as group_name  
                                FROM layout_options lo
                                LEFT JOIN forms f ON lo.form_id = 'LBF1'
                                WHERE lo.title = '$value3->screen_name'
                                AND f.encounter = $eid) as form_type, f.form_id, (SELECT lo.field_id
                                                        FROM layout_options lo 
                                                        WHERE lo.title =  '$value3->screen_name' AND lo.form_id = 'LBF1') as form_name ,lo.seq as FormOrder,SUBSTRING(lo.group_name ,-length(lo.group_name),1) as grouporder,SUBSTRING(lo.group_name FROM 2) as GroupName,
                                    CASE lo.uor
                                    WHEN 0 THEN 'UnUsed' 
                                    WHEN 1 THEN 'Optional'
                                    WHEN 2 THEN 'Required'
                                    END as isRequired, (SELECT lo.description
                                                        FROM layout_options lo 
                                                        WHERE lo.title =  '$value3->screen_name' AND lo.form_id = 'LBF1') as  description, lf.field_value
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
                    //$stmt4->execute(array('formtype' => $value3->screen_name));
                    $datacheck = $stmt4->fetchAll(PDO::FETCH_OBJ);  
                        if(empty($datacheck)){ 
                            $sql5 = "SELECT l.field_id, l.seq as FormOrder,SUBSTRING(l.group_name ,-length(l.group_name),1) as grouporder,SUBSTRING(l.group_name FROM 2) as GroupName,
                                     CASE l.uor
                                    WHEN 0 THEN 'UnUsed' 
                                    WHEN 1 THEN 'Optional'
                                    WHEN 2 THEN 'Required'
                                    END as isRequired,l.description
                                from layout_options l WHERE l.title LIKE '$value3->screen_name' and l.form_id='LBF1' ";
                            $stmt5 = $db->prepare($sql5);
                            $stmt5->execute();  
                            $datacheck2 = $stmt5->fetchAll(PDO::FETCH_OBJ); //echo "<pre>"; print_r($datacheck2); echo "</pre>"; 
                            $sql6 = "SELECT form_id from forms WHERE encounter = $eid and deleted = 0 and formdir='LBF2'";
                            $stmt6 = $db->prepare($sql6);
                            $stmt6->execute();  
                            $datacheck3 = $stmt6->fetchAll(PDO::FETCH_OBJ);
                            if(!empty($datacheck3)):
                                $form_id = $datacheck3[0]->form_id;
                            else:
                                 $form_id = 0;
                            endif;
                            $formvalues[] = (object)array('form_type' => $datacheck2[0]->GroupName,  'form_id'=> $form_id, 'form_name' =>$datacheck2[0]->field_id, 'FormOrder' =>$datacheck2[0]->FormOrder, 'grouporder' => $datacheck2[0]->grouporder, 'GroupName' => $value3->screen_name, 'isRequired' => $datacheck2[0]->isRequired , 'description' => $datacheck2[0]->description, 'field_value' => '');

                        }else{ 
                           $formvalues[] = $datacheck[0];//$stmt4->fetchAll(PDO::FETCH_OBJ); 
                        }

                           //$formvalues[]  = $stmt4->fetchAll(PDO::FETCH_OBJ);
                    
                }
             } 
        }//echo "<pre>";print_r($formvalues); echo "</pre>"; 
         //echo "<pre>"; print_r($formvalues);echo "</pre>";
         /*foreach ($formnames as $value4) {
            foreach ($value4 as $value3) {
            if($value3->formdir == 'face_to_face'){
                $tablename = 'tbl_form_facetoface';
            }else if($value3->formdir == 'lab_requisition'){
                $tablename = 'tbl_form_lab_requisition';
            }else if($value3->formdir == 'reviewofs'){
                $tablename = 'form_reviewofs';
            }else if($value3->formdir == 'dictation'){
                $tablename = 'form_dictation';
            }else if($value3->formdir == 'soap'){
                $tablename = 'form_soap';
            }else if($value3->formdir == 'vitals'){
                $tablename = 'form_vitals';
            }else if($value3->formdir == 'ros'){
                $tablename = 'form_ros';
            }else if($value3->formdir == 'bronchitis'){
                $tablename = 'form_bronchitis';
            }else if($value3->formdir == 'misc_billing_options'){
                $tablename = 'form_misc_billing_options';
            }else if($value3->formdir == 'physical_exam'){
                $tablename = 'form_physical_exam';
            }
            
            $sql4 ="SELECT t.* from $tablename t
                    INNER JOIN form_encounter f on 
                    where id ='$value3->form_id' and pid=$pid  "; 
            $demo1=sql_execute($value3->form_id,$sql4);
            $newdemo1=encode_demo($demo1);    
            
            $newdemo[$value3->form_name] = check_data_available($newdemo1);
          
           
//            $sql4 ="SELECT * from $tablename where id ='$value3->form_id' "; 
//            $stmt4 = $db->prepare($sql4) ;
//            $stmt4->execute(); 
//            $formfields[]= $value3->formdir;
//            $formfields[] = $stmt4->fetchAll(PDO::FETCH_OBJ); 
            
            }
        }   */
//        $sql5 ="SELECT seq as id, group_name, title as name from layout_options where form_id='LBF1' order by group_name "; 
//            $demo2=sql_execute('LBF1',$sql5);
//            $newdemo2=encode_demo($demo2);  
//            $newdemo['forms'] = check_data_available($newdemo2);

//            $demo1=sql_execute($eid,$sql3);
//                $newdemo1=encode_demo($demo1); 
//                $newdemo['FormsData'] = check_data_available($newdemo1);
//            
             $new = encode_demo(array_filter($formvalues));
             $newdemo['FormsData'] = check_data_available($new);
            if($newdemo) {
                //echo "<pre>";print_r($newdemo); echo "</pre>";
                echo $newdemores = json_encode($newdemo);
                //echo $incompletelistresult = GibberishAES::enc($newdemores, $key);

            }else
            {
                $demo1='';
                $newdemo1=encode_demo($demo1);      
                $newdemo['FormsData'] = check_data_available($newdemo1);
                //echo "<pre>";print_r($newdemo); echo "</pre>";
                echo $newdemores = json_encode($newdemo);
                //echo $incompletelistresult = GibberishAES::enc($newdemores, $key);
            }
             //echo "<pre>";print_r($newdemores); echo "</pre>"; 
        //echo "<pre>"; print_r($formfields);echo "</pre>";
       
    } 
    catch(PDOException $e) 
    {

        echo $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
        //echo $incompletelistresult = GibberishAES::enc($error, $key);
    }
  
}
function getDosFormData1($eid){
  try 
    {
        $db = getConnection();
        $key = 'rotcoderaclla';
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        $sql = "SELECT pc_catid, provider_id, facility_id, pid
                FROM  `form_encounter` 
                WHERE encounter =$eid";
        
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
            
            $sql2 ="SELECT f.screen_group, l.title AS screen_name,f.screen_group, l.description AS screen_link
                FROM  `tbl_allcare_facuservisit` f
                INNER JOIN layout_options l ON l.group_name = f.screen_group
                AND l.form_id = f.form_id
                WHERE  `facilities` REGEXP ('$value->facility_id') AND  `users` REGEXP ('$value->provider_id') AND  `visit_categories` REGEXP ('$value->pc_catid')";      
   
            $stmt2 = $db->prepare($sql2); 
            $stmt2->execute();
            $fieldnamesresult[] = $stmt2->fetchAll(PDO::FETCH_OBJ);
            
        } 
        foreach ($fieldnamesresult as $value2) {
            
            foreach ($value2 as $value3) {
                
//                echo $sql3 ="SELECT f.form_id,f.form_name,l.seq as FormOrder,SUBSTRING(l.group_name ,-length(l.group_name),1) as grouporder,SUBSTRING(l.group_name FROM 2) as GroupName,
//                        CASE l.uor
//                        WHEN 0 THEN 'UnUsed' 
//                        WHEN 1 THEN 'Optional'
//                        WHEN 2 THEN 'Required'
//                        END as isRequired, l.description,
//                        CASE t.finalized
//                        WHEN 'Y' THEN 'Completed'
//                        WHEN 'N' THEN 'Pending'
//                        END as formStatus
//                        from forms f
//                        INNER JOIN layout_options l on l.title = '$value3->screen_name' and l.form_id='LBF1'
//                        INNER JOIN tbl_allcare_formflag t ON t.encounter_id = $eid and f.form_id = t.form_id
//                        where f.formdir='$value3->screen_name' and f.encounter=$eid  and f.pid=$pid";
                $sql3 = "SELECT DISTINCT(SUBSTRING(lo.group_name FROM 2)) as group_name , lo.field_id 
                                FROM layout_options lo
                                LEFT JOIN forms f ON lo.form_id = 'LBF1'
                                WHERE lo.title = '$value3->screen_name'
                                AND f.encounter = $eid";
                $stmt3 = $db->prepare($sql3) ;
                $stmt3->execute();         
              
                
                $formnames = $stmt3->fetchAll(PDO::FETCH_OBJ);
                foreach ($formnames as $value4) {
                    $sql4 = "SELECT (SELECT DISTINCT(SUBSTRING(lo.group_name FROM 2)) as group_name  
                                FROM layout_options lo
                                LEFT JOIN forms f ON lo.form_id = 'LBF1'
                                WHERE lo.title = '$value3->screen_name'
                                AND f.encounter = $eid) as form_type, f.form_id, (SELECT lo.field_id
                                                        FROM layout_options lo 
                                                        WHERE lo.title =  '$value3->screen_name' AND lo.form_id = 'LBF1') as form_name ,lo.seq as FormOrder,SUBSTRING(lo.group_name ,-length(lo.group_name),1) as grouporder,SUBSTRING(lo.group_name FROM 2) as GroupName,
                                    CASE lo.uor
                                    WHEN 0 THEN 'UnUsed' 
                                    WHEN 1 THEN 'Optional'
                                    WHEN 2 THEN 'Required'
                                    END as isRequired, (SELECT lo.description
                                                        FROM layout_options lo 
                                                        WHERE lo.title =  '$value3->screen_name' AND lo.form_id = 'LBF1') as  description, lf.field_value
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
                    //$stmt4->execute(array('formtype' => $value3->screen_name));
                    
                    $formvalues[]  = $stmt4->fetchAll(PDO::FETCH_OBJ);
                    
                }
             } 
        }
         //echo "<pre>"; print_r($formvalues);echo "</pre>";
         /*foreach ($formnames as $value4) {
            foreach ($value4 as $value3) {
            if($value3->formdir == 'face_to_face'){
                $tablename = 'tbl_form_facetoface';
            }else if($value3->formdir == 'lab_requisition'){
                $tablename = 'tbl_form_lab_requisition';
            }else if($value3->formdir == 'reviewofs'){
                $tablename = 'form_reviewofs';
            }else if($value3->formdir == 'dictation'){
                $tablename = 'form_dictation';
            }else if($value3->formdir == 'soap'){
                $tablename = 'form_soap';
            }else if($value3->formdir == 'vitals'){
                $tablename = 'form_vitals';
            }else if($value3->formdir == 'ros'){
                $tablename = 'form_ros';
            }else if($value3->formdir == 'bronchitis'){
                $tablename = 'form_bronchitis';
            }else if($value3->formdir == 'misc_billing_options'){
                $tablename = 'form_misc_billing_options';
            }else if($value3->formdir == 'physical_exam'){
                $tablename = 'form_physical_exam';
            }
            
            $sql4 ="SELECT t.* from $tablename t
                    INNER JOIN form_encounter f on 
                    where id ='$value3->form_id' and pid=$pid  "; 
            $demo1=sql_execute($value3->form_id,$sql4);
            $newdemo1=encode_demo($demo1);    
            
            $newdemo[$value3->form_name] = check_data_available($newdemo1);
          
           
//            $sql4 ="SELECT * from $tablename where id ='$value3->form_id' "; 
//            $stmt4 = $db->prepare($sql4) ;
//            $stmt4->execute(); 
//            $formfields[]= $value3->formdir;
//            $formfields[] = $stmt4->fetchAll(PDO::FETCH_OBJ); 
            
            }
        }   */
//        $sql5 ="SELECT seq as id, group_name, title as name from layout_options where form_id='LBF1' order by group_name "; 
//            $demo2=sql_execute('LBF1',$sql5);
//            $newdemo2=encode_demo($demo2);  
//            $newdemo['forms'] = check_data_available($newdemo2);

//            $demo1=sql_execute($eid,$sql3);
//                $newdemo1=encode_demo($demo1); 
//                $newdemo['FormsData'] = check_data_available($newdemo1);
//            
             $new = encode_demo(array_filter($formvalues));
             $newdemo['FormsData'] = check_data_available($new);
            if($newdemo) {
                //echo "<pre>";print_r($newdemo); echo "</pre>";
                echo $newdemores = json_encode($newdemo);
                //echo $incompletelistresult = GibberishAES::enc($newdemores, $key);

            }else
            {
                $demo1='';
                $newdemo1=encode_demo($demo1);      
                $newdemo['FormsData'] = check_data_available($newdemo1);
                //echo "<pre>";print_r($newdemo); echo "</pre>";
                echo $newdemores = json_encode($newdemo);
                //echo $incompletelistresult = GibberishAES::enc($newdemores, $key);
            }
             //echo "<pre>";print_r($newdemores); echo "</pre>"; 
        //echo "<pre>"; print_r($formfields);echo "</pre>";
       
    } 
    catch(PDOException $e) 
    {

        echo $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
        //echo $incompletelistresult = GibberishAES::enc($error, $key);
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
//function recursive_array_replace($find, $replace, $array){
//        if (!is_array($array)) {
//        return str_replace($find, $replace, $array);
//        }
//        $newArray = array();
//        foreach ($array as $key => $value) {
//        $newArray[$key] = recursive_array_replace($find, $replace, $value);
//        }
//        return $newArray;
//}

function getDictationFormData2($eid,$catname2){
    
    /*$key = 'rotcoderaclla';
    // The default key size is 256 bits.
    $old_key_size = GibberishAES::size();
    GibberishAES::size(256);*/
    $catname = str_replace('_', ' ', $catname2);
    // For appointment Details
    $sql = "SELECT pc_hometext as comments,CONCAT(p.title,' ',p.fname, ' ', p.lname) as pname FROM openemr_postcalendar_events o
            INNER JOIN form_encounter f on o.pc_eventDate = DATE_FORMAT(f.date,'%Y-%m-%d')
            INNER JOIN patient_data p ON p.pid = f.pid 
            WHERE f.encounter = $eid";
    $demo=sql_execute($eid,$sql);
    $appdata=encode_demo($demo);  
    $appdata['Appointment_Comments'] = check_data_available($appdata);
    
    /* ========================= */
    // For Form CAMOS data
    $sql2 = "SELECT id,category FROM form_CAMOS_category WHERE category = '$catname'";
    $db = getConnection();
    $stmt2 = $db->prepare($sql2) ;
    $stmt2->execute();
    $categoryresult = $stmt2->fetchAll(PDO::FETCH_OBJ);
    $cat = array();
    foreach ($categoryresult as $value) {
        $sql3 ="SELECT id,subcategory FROM form_CAMOS_subcategory WHERE category_id = ". $value->id . " ORDER BY id ASC";      
        $stmt3 = $db->prepare($sql3); 
        $stmt3->execute(); 
        $subcategoryresult[] = $stmt3->fetchAll(PDO::FETCH_OBJ);
        $i = 0;
        foreach ($subcategoryresult[0] as $value2) {
            $sql4 ="SELECT * FROM form_CAMOS_item WHERE subcategory_id = $value2->id";      
            $stmt4 = $db->prepare($sql4); 
            $stmt4->execute(); 
            $itemresult = array();
            $itemresult[] = $stmt4->fetchAll(PDO::FETCH_OBJ);
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
                        $cat[$value->category][$i]['item'][$v3->item] = $v3->content;
                    endif;
                            
                    
                }
            }
            $i++;
        } 
    } 
 
    $newdemo2 = encode_demo($cat);  
    $newdemo['CAMOS Encounter Data'] = check_data_available($newdemo2);
    /* =========================== */ 
    
    // For Screen Group Data
    $sql5 = "SELECT field_id, CASE uor
                        WHEN 0 THEN 'UnUsed' 
                        WHEN 1 THEN 'Optional'
                        WHEN 2 THEN 'Required'
                        END as isFormRequired FROM layout_options WHERE form_id='LBF1' AND title = '$catname'";
    $stmt5 = $db->prepare($sql5) ;
    $stmt5->execute();
    $categoryrequired = $stmt5->fetchAll(PDO::FETCH_OBJ);
    $newdemo3 = encode_demo($categoryrequired);  
    $newdemo['Screen Group Form'] = check_data_available($newdemo3);
    /* ============================ */
    
    // For Group Fields Data
      $sql6 = "SELECT l.field_id,f.form_id,(SELECT field_value FROM lbf_data WHERE l.field_id = lbf_data.field_id AND l.form_id='LBF2' and f.form_id = lbf_data.form_id) as value,  (SELECT GROUP_CONCAT(lo.option_id,'-',lo.title) FROM list_options lo WHERE lo.list_id = l.list_id) as options,
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
     WHERE l.form_id='LBF2' AND l.group_name LIKE '%$catname%' AND f.encounter=$eid ORDER BY f.form_id";
    $stmt6 = $db->prepare($sql6);
    $stmt6->execute();
    $datarequired = $stmt6->fetchAll(PDO::FETCH_OBJ);
    $datarequired = array_filter($datarequired,'unique_obj');
    $i = 0; 
    foreach ($datarequired as $key => $object) {
        $object->field_id = str_replace($catname2,"dictation",$object->field_id);
    }
    //echo "<pre>"; print_r($datarequired); echo "</pre>";
    $newdemo4 = encode_demo($datarequired);  
    $newdemo['Screen Group Form Fields'] = check_data_available($newdemo4);
    /* =============================== */
    try 
    {
        $db = getConnection();
        $stmt = $db->prepare($sql2) ;
        $stmt->execute();                       

        $patientsreminders = $stmt->fetchAll(PDO::FETCH_OBJ);                        

        if($patientsreminders)
        { 
            //returns patients 
            //echo "<pre>"; print_r($newdemo); echo "</pre>";
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
//function prior_encounter($eid,$pid){
//
//     
//    // to get prior Information
//        $sql2 = "SELECT f.encounter AS eid, DATE_FORMAT( f.date, '%Y-%m-%d' ) AS dos, o.pc_catname AS visitcategory
//                FROM form_encounter f
//                INNER JOIN openemr_postcalendar_categories o
//                WHERE pid =$pid AND f.encounter < $eid
//                AND o.pc_catid = f.pc_catid ";
//        $db = getConnection();
//        $stmt2 = $db->prepare($sql2) ;
//        $stmt2->execute();
//        $priorinfo = $stmt2->fetchAll(PDO::FETCH_OBJ);
//    return $priorinfo;
//}
//function getHPIFormData($eid){
//     try 
//    {
//         // To get patientId
//        $sql = "SELECT pid FROM form_encounter WHERE encounter = $eid";
//        $db = getConnection();
//        $stmt = $db->prepare($sql) ;
//        $stmt->execute();
//        $pidval = $stmt->fetchAll(PDO::FETCH_OBJ);
//        $pid = $pidval[0]->pid;
//        /* ======================== */
//        
//      // To get Issue Information
//        $sql2 = "SELECT id,category FROM form_CAMOS_category WHERE category = 'HPI'";
//        $db = getConnection();
//        $stmt2 = $db->prepare($sql2) ;
//        $stmt2->execute();
//        $categoryresult = $stmt2->fetchAll(PDO::FETCH_OBJ);
//        $cat = array();
//        foreach ($categoryresult as $value) {
//            $sql3 ="SELECT id,subcategory FROM form_CAMOS_subcategory WHERE category_id = $value->id";      
//            $stmt3 = $db->prepare($sql3); 
//            $stmt3->execute(); 
//            $subcategoryresult[] = $stmt3->fetchAll(PDO::FETCH_OBJ);
//            foreach ($subcategoryresult[0] as $value2) {
//                $sql4 ="SELECT item,subcategory_id FROM form_CAMOS_item WHERE subcategory_id = $value2->id";      
//                $stmt4 = $db->prepare($sql4); 
//                $stmt4->execute(); 
//                $itemresult = array();
//                $itemresult[] = $stmt4->fetchAll(PDO::FETCH_OBJ);
//                foreach ($itemresult as $value3){
//                   foreach($value3 as $v3){
//                        $sql5 = "SELECT  '' AS isIssueActive, item,content FROM form_CAMOS_item i 
//                            WHERE subcategory_id = $v3->subcategory_id
//                            UNION 
//                            SELECT
//                                CASE 
//                                    WHEN l.enddate IS NULL THEN  'Active'
//                                    ELSE  'Inactive'
//                                END AS isIssueActive, l.title,i.content FROM form_CAMOS_item i 
//                            INNER JOIN lists l ON i.item = l.title
//                            INNER JOIN issue_encounter ie ON ie.list_id = l.id
//                            WHERE l.type =  'medical_problem' AND ie.encounter = $eid AND ie.pid = $pid AND i.subcategory_id = $v3->subcategory_id
//                            GROUP BY i.item"; 
//                        $stmt5 = $db->prepare($sql5); 
//                        $stmt5->execute(); 
//                        $issueresult = array();
//                        $issueresult[] = $stmt5->fetchAll(PDO::FETCH_OBJ);
//                        foreach($issueresult as $value4){
//                            foreach($value4 as $v4){
//                                $cat[$value->category][$value2->subcategory][$v3->item]['isIssueActive'] = $v4->isIssueActive;
//                                $cat[$value->category][$value2->subcategory][$v3->item]['content'] = $v4->content;
//                            }
//                        }
//                    }
//                }
//            } 
//        } 
//        $newdemo3 = encode_demo($cat);  
//        $newdemo['Issue Information Data'] = check_data_available($newdemo3);
//
//        /* ========================= */
//         if($newdemo)
//        {
//            echo $patientres = json_encode($newdemo); 
//            //echo $patientresult = GibberishAES::enc($patientres, $key);
//        }
//        else
//        {
//            //echo 'No patients available';
//            echo '[{"id":"0"}]';
//        }
//    } 
//    catch(PDOException $e) 
//    {
//
//        echo '{"error":{"text":'. $e->getMessage() .'}}'; 
//    }
//}
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
                                    $cat[$value->category][$i]['item'][$v3->item] = $appdata['Appointment_Comments'][0]->pname;
                                elseif(substr($value2->subcategory,2) == "Complains of"):    
                                    $cat[$value->category][$i]['item'][$v3->item] = $appdata['Appointment_Comments'][0]->comments;
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
        $formname = str_replace('_', ' ', $formname2);
        // To get patientId
        $sql = "SELECT pid FROM form_encounter WHERE encounter = $eid";
        $db = getConnection();
        $stmt = $db->prepare($sql) ;
        $stmt->execute();
        $pidval = $stmt->fetchAll(PDO::FETCH_OBJ);
        $pid = $pidval[0]->pid;
        /* ======================== */
        // For appointment Details
        $appdata = array();
        $sql7 = "SELECT pc_hometext as comments,CONCAT(p.title,' ',p.fname, ' ', p.lname) as pname FROM openemr_postcalendar_events o
                INNER JOIN form_encounter f on o.pc_eventDate = DATE_FORMAT(f.date,'%Y-%m-%d')
                INNER JOIN patient_data p ON p.pid = f.pid 
                WHERE f.encounter = $eid";
        $demo7=sql_execute($eid,$sql7);
        $appdata=encode_demo($demo7);  
        $appdata['Appointment_Comments'] = check_data_available($appdata);
        if($appdata['DataAvailable'] == 'NO'):
            $query = "SELECT title, fname, lname FROM patient_data 
                WHERE pid = $pid";
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
                $stmt7 = $db->prepare($sql7);
                $stmt7->execute();
                $datarequired2 = $stmt7->fetchAll(PDO::FETCH_OBJ);
                $sql8 = "SELECT form_id FROM forms WHERE formdir='LBF2' AND deleted = 0 AND encounter = $eid AND pid = $pid";
                $stmt8 = $db->prepare($sql8);
                $stmt8->execute();
                $datarequired3 = $stmt8->fetchAll(PDO::FETCH_OBJ);
                if(!empty($datarequired2)):
                 $datarequired2 = array_filter($datarequired2,'unique_obj');
                    foreach($datarequired2 as $key => $value){echo $key. "===".$value;
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
        //echo "<pre>"; print_r($datarequired); echo "</pre>";
        $newdemo4 = encode_demo($datarequired);  
        $newdemo['Screen Group Form Fields'] = check_data_available($newdemo4);
        /* ================================ */

         if($newdemo)
        {
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
function getDictationFormData4($eid,$formname2){
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
        // For appointment Details
        $appdata = array();
        $sql7 = "SELECT pc_hometext as comments,CONCAT(p.title,' ',p.fname, ' ', p.lname) as pname FROM openemr_postcalendar_events o
                INNER JOIN form_encounter f on o.pc_eventDate = DATE_FORMAT(f.date,'%Y-%m-%d')
                INNER JOIN patient_data p ON p.pid = f.pid 
                WHERE f.encounter = $eid AND  pd.practice_status = 'YES' AND pd.deceased_stat != 'YES'";
        $demo7=sql_execute($eid,$sql7);
        $appdata=encode_demo($demo7);  
        $appdata['Appointment_Comments'] = check_data_available($appdata);
        if($appdata['DataAvailable'] == 'NO'):
            $query = "SELECT title, fname, lname FROM patient_data 
                WHERE pid = $pid";
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
                //echo "<pre>"; print_r($newdemo11); echo "</pre>";
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
                $stmt7 = $db->prepare($sql7);
                $stmt7->execute();
                $datarequired2 = $stmt7->fetchAll(PDO::FETCH_OBJ);
                $sql8 = "SELECT form_id FROM forms WHERE formdir='LBF2' AND deleted = 0 AND encounter = $eid AND pid = $pid";
                $stmt8 = $db->prepare($sql8);
                $stmt8->execute();
                $datarequired3 = $stmt8->fetchAll(PDO::FETCH_OBJ);
                if(!empty($datarequired2)):
                 $datarequired2 = array_filter($datarequired2,'unique_obj');
                    foreach($datarequired2 as $key => $value){
                        $datarequired2[$key] =  $value;
                        $value->field_id = str_replace($formname2,"dictation",$value->field_id);
                    }    
                     foreach($datarequired2 as $key => $value){
                       if (array_key_exists('form_id', $datarequired2[$key])) {
                            echo "The 'first' element is in the array";
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
            }
        endif;
        //echo "<pre>"; print_r($datarequired); echo "</pre>";
        $newdemo4 = encode_demo($datarequired);  
        $newdemo['Screen Group Form Fields'] = check_data_available($newdemo4);
        /* ================================ */

         if($newdemo)
        {
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
function getDictationFormData3($eid,$formname2){
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
        // For appointment Details
        $appdata = array();
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
                                    $cat[$value->category][$i]['item'][$v3->item] = $appdata['Appointment_Comments'][0]->pname;
                                elseif(substr($value2->subcategory,2) == "Complains of"):    
                                    $cat[$value->category][$i]['item'][$v3->item] = $appdata['Appointment_Comments'][0]->comments;
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
                //echo "<pre>"; print_r($newdemo11); echo "</pre>";
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
function saveLayoutFormsData2(){
    try{
        $db = getConnection();
        $request = Slim::getInstance()->request();
        $key = 'rotcoderaclla';
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        $appres = GibberishAES::dec($request->getBody(), $key);
        //$appres = GibberishAES::dec('U2FsdGVkX1+QiLzhzqUk5D5A6hk/shTT6UObLcE99l+OOj6PCQFs2hc+h7UrHaEYNcPzpqBZ4dJZr/kWUzLIkT4z9aJH7kFPjAxIF7j6GT49aXPd+bgbzNgtPOvg0B4zi9tdx+ueczHGHYfEiWQ70HWH9gTtz6ejZYSg6nsQKXAoi1YWbpKFlMerrte1Do4peipGSaisXjWHw644KzQacvdwDdT3RLhBytvRASjtAmQAVKN8oJ+5q7EyLxopyXPSnyigST/+8OuIIJVDJ8BPyly8jEqtWr7kWKhrwXvJ5u4cMt40HW2d0gAUVT+g4Iclsj8eQTzRsUAhTv+7o8NfxTA/JAo2LdJtMHfjcsWAcZXXXTOOA5CjyXalDM/HWr/gipAlnUWrYE81XP5t9DgKbeuAzvfixzOlIWe08TrbSEX/l8jMCBSdAr/dczkVvPCe', $key);
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
                if (count($db->query("SELECT * FROM lbf_data WHERE form_id = $newformid AND field_id = '$key'")->fetchAll())) {
                     $sql = "INSERT into lbf_data (form_id, field_id, field_value) VALUES ($newformid,'$key','$value')";
                } else {
                    $sql = "UPDATE lbf_data SET field_value = '$value' WHERE field_id ='$key'  AND form_id = $newformid";
                }
                $stmt4 = $db->prepare($sql);
                $stmt4->execute();
            endforeach;
        else:
            foreach($field_id_data as $key => $value):
                if (count($db->query("SELECT * FROM lbf_data WHERE form_id = $form_id AND field_id = '$key'")->fetchAll())) {
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
// to save layoutformdata
function saveLayoutFormsData1(){
    try{
        $db = getConnection();
        //$request = Slim::getInstance()->request();
        $key = 'rotcoderaclla';
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        $appres = GibberishAES::dec($request->getBody(), $key);
        //$appres = GibberishAES::dec('U2FsdGVkX1/TqB93BRV3C8jR+jV2qSzHbH21tPEXf/LFEqQFFWiWjtS6o0QTSrOO57WQdUdDQmYUJT9s8rTRRUGYf2q5rhEU4jPyOykwiJdoxaH1VxW92BwHBbwUDtW9xBDTAEn7Xbwz/JdHnMHtplC58jj+QnVTQBWL/j8wHQfeLunX/L+v3LbCdxL1joTVqK5cFZH5zUunDNGodY8iMZPHvZG81oqluVnNshUNmG+Kc6QZu/jORvCKOHr+yiH6yASiipBB1096BPGMLXBhoIiKFR62rYAt+4S3ZM3sVvi0gQ9BR6aJpt59ZCBO+OqTcn7OtdM7Mm01zjOCW80k/wbw+DAxF1xSR2psEh8YQFszwQ/9m5Le5FKMxwb0pC28udktfZynWO0zcZU1z2LSwpoF9pa5er6Av//0DqWKglaVGI9Vh6ntsvxUPkMEQq0G', $key);
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
       // echo "<pre>";print_r($field_id_data);echo "</pre>";

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
                if (count($db->query("SELECT * FROM lbf_data WHERE form_id = $newformid AND field_id = '$key'")->fetchAll())) {
                     $sql = "INSERT into lbf_data (form_id, field_id, field_value) VALUES ($newformid,'$key','$value')";
                } else {
                    $sql = "UPDATE lbf_data SET field_value = '$value' WHERE field_id ='$key'  AND form_id = $newformid";
                }
                $stmt4 = $db->prepare($sql);
                $stmt4->execute();
            endforeach;
        else:
            foreach($field_id_data as $key => $value):
                if (count($db->query("SELECT * FROM lbf_data WHERE form_id = $form_id AND field_id = '$key'")->fetchAll())) {
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
        $patientres = '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo $patientresult = GibberishAES::enc($patientres, $key);
    }
}
function insertSpeechDicatation() {
/*
 * Copyright 2011 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
include_once "driveapi/examples/templates/base.php";
//session_start();

require_once realpath(dirname(__FILE__) . '/driveapi/autoload.php');

DEFINE("TESTFILE", 'testfile.txt');
if (!file_exists(TESTFILE)) {
  $fh = fopen(TESTFILE, 'w');
  fseek($fh, 1024*1024*20);
  fwrite($fh, "!", 1);
  fclose($fh);
}

$client_id = '1069965607102-k6jhijbovbnlkdjrttgdd5710mqlcqam.apps.googleusercontent.com';
$client_secret = 'qAwy0LOduVKi-zmoquO2oCW1';
$redirect_uri = 'http://devemr.risecorp.com/api/insertfiletogdrive';

$client = new Google_Client();
$client->setClientId($client_id);
$client->setClientSecret($client_secret);
$client->setRedirectUri($redirect_uri);
$client->addScope("https://www.googleapis.com/auth/drive");
$service = new Google_Service_Drive($client);

if (isset($_REQUEST['logout'])) {
  unset($_SESSION['upload_token ']);
}

if (isset($_GET['code'])) {
  $client->authenticate($_GET['code']);
  $_SESSION['upload_token'] = $client->getAccessToken();
  $redirect = 'http://devemr.risecorp.com/api/insertfiletogdrive';
  //header('Location: ' . filter_var($redirect, FILTER_SANITIZE_URL));
}

if (isset($_SESSION['upload_token']) && $_SESSION['upload_token']) {
  $client->setAccessToken($_SESSION['upload_token']);
  if ($client->isAccessTokenExpired()) {
    unset($_SESSION['upload_token']);
  }
} else {
  $authUrl = $client->createAuthUrl();
}

if ($client->getAccessToken()) {
  $file = new Google_Service_Drive_DriveFile();
  $file->title = "Big File";
  $chunkSizeBytes = 1 * 1024 * 1024;

  // Call the API with the media upload, defer so it doesn't immediately return.
  $client->setDefer(true);
  $request = $service->files->insert($file);

  }
 
if (isset($authUrl)) {
  echo "<a class='login' href='" . $authUrl . "'>Connect Me!</a>";
}

 
}

//to get all patients from OpenEMR for drop down of mobile app
function getTotalPatients()
{
    
	$sql = "select pid,fname,lname from patient_data  WHERE practice_status = 'YES' AND deceased_stat = 'NO'";      
        $count= "select count(*) from patient_data WHERE practice_status = 'YES' AND deceased_stat = 'NO'";
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
        //$appres = GibberishAES::dec('U2FsdGVkX1/O3qZ2+BIatXiSOQ2CTx9yrHAHMxTb2Aof8/gZUBMajdXtFfYML4IWz2sNIupwgiYvkA9HpZTbYVQostjNMvQGFRC1+E4Kx3oVU8xPjszVZoJ2t0cT8QcmGG8ZW6+vI+C8k+bwvfuyM1ZdB5+F1epId/b2LxAqoLXJTFWJSOhnWU+LDhIIljWmsTQcZr1OsFrDvQ5RCJsGoJnrxoNiiCcvMxpivLQ+pIIa+6kausoORp9jbHaGSafMVf7faj0KfyLEFrgjovgEXmojl9w/eOqfhqRDw0Yl4f1eq52Ea/JvQx2Loj4jja2elj1v65j17HWHlrZidQXtwxgmdnf1RIICwdaWLyl+Yy5XkAmvssePjwtvSRTEMLXaPYP/+khCIacReCkf1flPMXX2i4DXJHRf0voo9/SLwD3ovQRNsF6Wmthj/z7t6o3AURZ9zFl9s5FsjF9Tl9SsXNJgT7r/z0RNInMaw29rRE4ljoWm4xP4Pq8zLkUhBD7kqCJhrRozpoorrkWplGe8ohOOawls1CHQF/UU7H8FEzM=', $key);
        $insertArray = json_decode($appres,TRUE);
        
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
        $height_unit                = $insertArray['height_unit'];
        $temperature_fahrenheit     = $insertArray['temperature'];
        $temperature_unit           = $insertArray['temperature_unit'];
        $temperature_location       = $insertArray['temp_method'];
        $pulse                      = $insertArray['pulse'];
        $respiration                = $insertArray['respiration'];
        $note                       = $insertArray['note'];
        $BMI                        = $insertArray['BMI'];
        $BMI_status                 = $insertArray['BMI_status'];
        $waist_circumference_inches = $insertArray['waist_circ'];   
        $waist_circ_unit            = $insertArray['waist_circ_unit'];   
        $head_circumference_inches  = $insertArray['head_circ'];
        $head_circ_unit             = $insertArray['head_circ_unit'];
        $oxygen_saturation          = $insertArray['oxygen_saturation'];
      

        $patientres = '';
        if($formid == 0){
            
            $lastformid = "SELECT MAX(id) as form_id FROM form_vitals";
            $stmt = $db->prepare($lastformid) ;
            $stmt->execute();
            $maxformidval = $stmt->fetchAll(PDO::FETCH_OBJ);
            $maxformid =  $maxformidval[0]->form_id + 1;
        
            $insertform = "INSERT INTO forms (date,encounter, form_name, form_id, pid, user, groupname, authorized, deleted, formdir) VALUES (NOW(), $encounter, 'Vitals', $maxformid, $pid, $user, 'Default', $authorized, 0, 'vitals' )";
            $stmt2 = $db->prepare($insertform);
            $stmt2->execute();
            $lastId = $db->lastInsertId();
            
            $getformid = "SELECT form_id FROM forms WHERE id = $lastId";
            $stmt3 = $db->prepare($getformid) ;
            $stmt3->execute();
            $formidval = $stmt3->fetchAll(PDO::FETCH_OBJ);
            $newformid =  $formidval[0]->form_id;
            
            $sql = "INSERT INTO `form_vitals` ( `id`, `date`, `pid`, `user`,  `authorized`, `activity`, `bps`, `bpd`, `weight`, `height`,`height_unit`, `temperature`, `temperature_unit`, `temp_method`, `pulse`, `respiration`, `note`, `BMI`, `BMI_status`, `waist_circ`,`waist_circ_unit`, `head_circ`,`head_circ_unit`, `oxygen_saturation`, `created_by`,  `created_date`) 
                    VALUES ( $newformid, NOW(), $pid, $user,  $authorized, $activity, $bp_systolic, $bp_diastolic, $weight_lbs,  $height_inches,'".$height_unit."', $temperature_fahrenheit,'". $temperature_unit."' ,'". $temperature_location."', $pulse, $respiration,'". $note."', $BMI, '".$BMI_status ."',$waist_circumference_inches, '".$waist_circ_unit."' ,$head_circumference_inches, '".$head_circ_unit."', $oxygen_saturation, $user,  NOW())";
            $sqlstmt = $db->prepare($sql) ;
            $data =  $sqlstmt->execute();
            
        }else{
            $sql = "UPDATE form_vitals SET         
                    `bps` = $bp_systolic, `bpd` = $bp_diastolic,`weight` = $weight_lbs,`height` = $height_inches,
                    `temperature` = $temperature_fahrenheit,`temp_method`= '".$temperature_location."' ,`pulse` = $pulse,
                    `respiration` = $respiration,`note` = '". $note."',`BMI`=  $BMI ,`BMI_status` ='". $BMI_status."' ,`waist_circ` = $waist_circumference_inches,
                    `head_circ` = $head_circumference_inches,`oxygen_saturation`  = $oxygen_saturation,`updated_date` = NOW(), `height_unit`  = '".$height_unit."' ,
                    `temperature_unit` ='". $temperature_unit."' ,
                    `waist_circ_unit` = '".$waist_circ_unit."' ,
                    `head_circ_unit`= '".$head_circ_unit."'  WHERE `id` = $formid";
            $sqlstmt = $db->prepare($sql) ;
            $data = $sqlstmt->execute();
            
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
                        $data['data']['selectedvalue'] = $value;
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
                        $values['selectedvalue'] = $value;
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
                        $values1['selectedvalue'] = $value;
                    endif;
                    $values1['headervaluelist'] = array('N/A', 'YES', 'NO');
                    for($i=0; $i<$countarray; $i++){ 
                        if( $dataarray[$i]['data']['header'] == 'eyes'){
                            $dataarray[$i]['data']['values'][] = $values1;

                        }
                    }
                }
                // ent
                if($key == 'hearing_loss' || $key == 'discharge' || $key == 'pain' || $key == 'vertigo' || $key == 'tinnitus' || $key == 'frequent_colds' || $key == 'sore_throat' || $key == 'sinus_problems' || $key == 'post_nasal_drip' || $key == 'nosebleed' || $key == 'snoring' || $key == 'apnea' || $key == 'bleeding_gums' || $key =='hoarseness' || $key == 'dental_difficulties' || $key == 'use_of_dentures'){
                    $values2['name'] = $key;
                    $values2['title'] = ucwords(str_replace("_", " ", $key));
                    $values2['controlType'] = 'radiobutton';
                    if($formid == 0):
                        $values2['selectedvalue'] = 'N/A';
                    else:
                        $values2['selectedvalue'] = $value;
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
                        $values2['selectedvalue'] = $value;
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
                        $values2['selectedvalue'] = $value;
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
                        $values2['selectedvalue'] = $value;
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
                        $values2['selectedvalue'] = $value;
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
                        $values2['selectedvalue'] = $value;
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
                        $values2['selectedvalue'] = $value;
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
                        $values2['selectedvalue'] = $value;
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
                        $values2['selectedvalue'] = $value;
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
                        $values2['selectedvalue'] = $value;
                    endif;
                    $values2['headervaluelist'] = array('N/A', 'YES', 'NO');
                    for($i=0; $i<$countarray; $i++){
                        if( $dataarray[$i]['data']['header'] == 'extremities'){
                            $dataarray[$i]['data']['values'][] = $values2;

                        }
                    }
                }
                // neurologic
                if($key == 'loc' || $key == 'seizures' ||  $key == 'stroke'  || $key == 'tia' || $key == 'n_numbness' || $key == 'n_weakness' || $key == 'paralysis' || $key == 'intellectual_decline' || $key == 'memory_problems' || $key == 'dementia' || $key == 'n_headache' || $key == 'dizziness_vertigo' || $key == 'slurred_speech' || $key =='tremors' || $key == 'migraines' || $key == 'changes_in_mentation' ){
                    $values2['name'] = $key;
                    $values2['title'] = ucwords(str_replace("_", " ", $key));
                    $values2['controlType'] = 'radiobutton';
                    if($formid == 0):
                        $values2['selectedvalue'] = 'N/A';
                    else:
                        $values2['selectedvalue'] = $value;
                    endif;
                    $values2['headervaluelist'] = array('N/A', 'YES', 'NO');
                    for($i=0; $i<$countarray; $i++){
                        if( $dataarray[$i]['data']['header'] == 'neurologic'){
                            $dataarray[$i]['data']['values'][] = $values2;

                        }
                    }
                }
                // skin
                if($key == 's_cancer' || $key == 'psoriasis' || $key == 's_acne' || $key == 's_other' || $key == 's_disease' || $key == 'rashes' || $key == 'dryness' || $key == 'itching' ){
                    $values2['name'] = $key;
                    $values2['title'] = ucwords(str_replace("_", " ", $key));
                    $values2['controlType'] = 'radiobutton';
                    if($formid == 0):
                        $values2['selectedvalue'] = 'N/A';
                    else:
                        $values2['selectedvalue'] = $value;
                    endif;
                    $values2['headervaluelist'] = array('N/A', 'YES', 'NO');
                    for($i=0; $i<$countarray; $i++){
                        if( $dataarray[$i]['data']['header'] == 'skin'){
                            $dataarray[$i]['data']['values'][] = $values2;

                        }
                    }
                }
                // psychiatric
                if($key == 'p_diagnosis' || $key == 'p_medication' || $key == 'depression' || $key == 'anxiety' || $key == 'social_difficulties' || $key == 'alcohol_drug_dependence' || $key == 'suicide_thoughts' || $key == 'use_of_antideprassants' || $key == 'thought_content' ){
                    $values2['name'] = $key;
                    $values2['title'] = ucwords(str_replace("_", " ", $key));
                    $values2['controlType'] = 'radiobutton';
                    if($formid == 0):
                        $values2['selectedvalue'] = 'N/A';
                    else:
                        $values2['selectedvalue'] = $value;
                    endif;
                    $values2['headervaluelist'] = array('N/A', 'YES', 'NO');
                    for($i=0; $i<$countarray; $i++){
                        if( $dataarray[$i]['data']['header'] == 'psychiatric'){
                            $dataarray[$i]['data']['values'][] = $values2;

                        }
                    }
                }
                // endocrine
                if($key == 'thyroid_problems' || $key == 'diabetes' || $key == 'abnormal_blood' || $key == 'goiter' || $key == 'heat_intolerence' || $key == 'cold_intolerence' || $key == 'increased_thirst' || $key == 'excessive_sweating' || $key == 'excessive_hunger' ){
                    $values2['name'] = $key;
                    $values2['title'] = ucwords(str_replace("_", " ", $key));
                    $values2['controlType'] = 'radiobutton';
                    if($formid == 0):
                        $values2['selectedvalue'] = 'N/A';
                    else:
                        $values2['selectedvalue'] = $value;
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
                        $values2['selectedvalue'] = $value;
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
                        $values2['selectedvalue'] = $value;
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
                    $values2['selectedvalue'] = $value;
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
                    $data['data']['selectedvalue'] = $value;
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
                echo $patientres = json_encode($newdemo); 
                $patientresult = GibberishAES::enc($patientres, $key1);
            }
            else
            {
                //echo 'No patients available';
                echo $patientres = '[{"id":"0"}]';
                //echo $patientresult = GibberishAES::enc($patientres, $key1);
            }
        } 
        catch(PDOException $e) 
        {
            
            echo $patientres = '{"error":{"text":'. $e->getMessage() .'}}'; 
            //echo $patientresult = GibberishAES::enc($patientres, $key);
        }
    
}
function getROSFormData_old($formid){
        try 
        {
            $key1 = 'rotcoderaclla';
            // The default key size is 256 bits.
            $old_key_size = GibberishAES::size();
            GibberishAES::size(256);
            $db = getConnection();
            $sql = "SELECT * FROM tbl_form_allcare_ros WHERE id = $formid";
            $stmt = $db->prepare($sql) ;
            $stmt->execute();                       
             
            $dataresult = $stmt->fetchAll(PDO::FETCH_OBJ);                        
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
                    $data['data']['controlType'] = 'radiobutton';
                    $data['data']['selectedvalue'] = $value; 
                    $data['data']['valuelist'] = array('normal' => 'Normal', 'notspecified' =>'Not Specified', 'selectdetails' => 'Select Details');
                    $dataarray[] = $data; 
                }
            }
            foreach ($dataresult[0] as $key=>$value) {
                // constitutional
                if($key == 'weight_change' || $key == 'weakness' || $key == 'fatigue' || $key == 'anorexia' || $key == 'fever' || $key == 'chills' || $key == 'night_sweats' || $key == 'insomnia' || $key == 'irritability' || $key == 'heat_or_cold' || $key == 'intolerance' || $key == 'change_in_appetite'){
                    $values['name'] = $key;
                    $values['controlType'] = 'radiobutton';
                    $values['selectedvalue'] = $value;
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
                    $values1['controlType'] = 'radiobutton';
                    $values1['selectedvalue'] = $value;
                    $values1['headervaluelist'] = array('N/A', 'YES', 'NO');
                    for($i=0; $i<$countarray; $i++){ 
                        if( $dataarray[$i]['data']['header'] == 'eyes'){
                            $dataarray[$i]['data']['values'][] = $values1;

                        }
                    }
                }
                // ent
                if($key == 'hearing_loss' || $key == 'discharge' || $key == 'pain' || $key == 'vertigo' || $key == 'tinnitus' || $key == 'frequent_colds' || $key == 'sore_throat' || $key == 'sinus_problems' || $key == 'post_nasal_drip' || $key == 'nosebleed' || $key == 'snoring' || $key == 'apnea' || $key == 'bleeding_gums' || $key =='hoarseness' || $key == 'dental_difficulties' || $key == 'use_of_dentures'){
                    $values2['name'] = $key;
                    $values2['controlType'] = 'radiobutton';
                    $values2['selectedvalue'] = $value;
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
                    $values2['controlType'] = 'radiobutton';
                    $values2['selectedvalue'] = $value;
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
                    $values2['controlType'] = 'radiobutton';
                    $values2['selectedvalue'] = $value;
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
                    $values2['controlType'] = 'radiobutton';
                    $values2['selectedvalue'] = $value;
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
                    $values2['controlType'] = 'radiobutton';
                    $values2['selectedvalue'] = $value;
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
                    $values2['controlType'] = 'radiobutton';
                    $values2['selectedvalue'] = $value;
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
                    $values2['controlType'] = 'radiobutton';
                    $values2['selectedvalue'] = $value;
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
                    $values2['controlType'] = 'radiobutton';
                    $values2['selectedvalue'] = $value;
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
                    $values2['controlType'] = 'radiobutton';
                    $values2['selectedvalue'] = $value;
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
                    $values2['controlType'] = 'radiobutton';
                    $values2['selectedvalue'] = $value;
                    $values2['headervaluelist'] = array('N/A', 'YES', 'NO');
                    for($i=0; $i<$countarray; $i++){
                        if( $dataarray[$i]['data']['header'] == 'extremities'){
                            $dataarray[$i]['data']['values'][] = $values2;

                        }
                    }
                }
                // neurologic
                if($key == 'loc' || $key == 'seizures' ||  $key == 'stroke'  || $key == 'tia' || $key == 'n_numbness' || $key == 'n_weakness' || $key == 'paralysis' || $key == 'intellectual_decline' || $key == 'memory_problems' || $key == 'dementia' || $key == 'n_headache' || $key == 'dizziness_vertigo' || $key == 'slurred_speech' || $key =='tremors' || $key == 'migraines' || $key == 'changes_in_mentation' ){
                    $values2['name'] = $key;
                    $values2['controlType'] = 'radiobutton';
                    $values2['selectedvalue'] = $value;
                    $values2['headervaluelist'] = array('N/A', 'YES', 'NO');
                    for($i=0; $i<$countarray; $i++){
                        if( $dataarray[$i]['data']['header'] == 'neurologic'){
                            $dataarray[$i]['data']['values'][] = $values2;

                        }
                    }
                }
                // skin
                if($key == 's_cancer' || $key == 'psoriasis' || $key == 's_acne' || $key == 's_other' || $key == 's_disease' || $key == 'rashes' || $key == 'dryness' || $key == 'itching' ){
                    $values2['name'] = $key;
                    $values2['controlType'] = 'radiobutton';
                    $values2['selectedvalue'] = $value;
                    $values2['headervaluelist'] = array('N/A', 'YES', 'NO');
                    for($i=0; $i<$countarray; $i++){
                        if( $dataarray[$i]['data']['header'] == 'skin'){
                            $dataarray[$i]['data']['values'][] = $values2;

                        }
                    }
                }
                // psychiatric
                if($key == 'p_diagnosis' || $key == 'p_medication' || $key == 'depression' || $key == 'anxiety' || $key == 'social_difficulties' || $key == 'alcohol_drug_dependence' || $key == 'suicide_thoughts' || $key == 'use_of_antideprassants' || $key == 'thought_content' ){
                    $values2['name'] = $key;
                    $values2['controlType'] = 'radiobutton';
                    $values2['selectedvalue'] = $value;
                    $values2['headervaluelist'] = array('N/A', 'YES', 'NO');
                    for($i=0; $i<$countarray; $i++){
                        if( $dataarray[$i]['data']['header'] == 'psychiatric'){
                            $dataarray[$i]['data']['values'][] = $values2;

                        }
                    }
                }
                // endocrine
                if($key == 'thyroid_problems' || $key == 'diabetes' || $key == 'abnormal_blood' || $key == 'goiter' || $key == 'heat_intolerence' || $key == 'cold_intolerence' || $key == 'increased_thirst' || $key == 'excessive_sweating' || $key == 'excessive_hunger' ){
                    $values2['name'] = $key;
                    $values2['controlType'] = 'radiobutton';
                    $values2['selectedvalue'] = $value;
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
                    $values2['controlType'] = 'radiobutton';
                    $values2['selectedvalue'] = $value;
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
                    $values2['controlType'] = 'radiobutton';
                    $values2['selectedvalue'] = $value;
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
                    $values2['controlType'] = 'textbox';
                    $values2['selectedvalue'] = $value;
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
                    $data['data']['controlType'] = 'radiobutton';
                    $data['data']['selectedvalue'] = $value;
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
                echo $patientres = json_encode($newdemo); 
                //echo $patientresult = GibberishAES::enc($patientres, $key1);
            }
            else
            {
                //echo 'No patients available';
                echo $patientres = '[{"id":"0"}]';
                //echo $patientresult = GibberishAES::enc($patientres, $key1);
            }
        } 
        catch(PDOException $e) 
        {
            
            echo $patientres = '{"error":{"text":'. $e->getMessage() .'}}'; 
            //echo $patientresult = GibberishAES::enc($patientres, $key);
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
        //$appres = GibberishAES::dec('U2FsdGVkX19LhoSrX/juvp5Jn08AloRLNx8RhqhBOQfFTcuBzVYgtxYvVk8vXXedxSccxidGLTpM8uEdwRqYoe+c87VmVsXmP3oBt+N8bWap4wB0A9MbLiCjJIxo8dVqOJxx3rIv5Y7Tv8i7dfnho+6rjBRh9O0/UC3DY9fDCBPJYnDncT5EqK8I4deh1KtVUBv1uJGYn56ZrNUwxOoiXKLLHNXO5/GGhYeoUF/eFFrQH33ORQWNJAQPOegxoEXeFz3Em9c+qYy/NbpOLe++FwJ9GzwDAf1qHNdy7vfIj/lhMgrBmWVCGmhHObu66F2Xo0F1/t5Vo/IHuEGcOsbJvw74TnvURHWOOhwR3oDcdj+sdfKLy/8JaAjtyqg1nxc9pRQUlZc0tO/Iw5FvBbFr9CfB7Sp2fLpgRKuVrrPWWCMjgzu5f9zRptqua+ZgUU+SGLQybKwMjL2ce6CjxmN2dsSXFUhFQ0hsJKZsyxXXXTPDfJO86qYWlPLYEZ5BLqRdNoDdx8UlPGkQXkgcvlYZn08hjtUVO8yI56+1XmSiwnM3wFKe4zYZs/vT4GemBnXG4df7LhcsYOxCcDwz3YHINBxGhxmp4rKBPm0HgJK6J/skEX4A00s+hzAJjXn/1sIIieZA95k0daEwhFkmRRR+/QJaRUy5ZTVUMZ/VCvOSb8KtEHA4HCEEDLePdrr9dvNoFuXoGukQtKZLNwa4lSIsL77waPt92TcEK1yc0ZR0mhJ+RTIRyJuUS2XspTzOJjlv+2dbQw0e8doTNd9wn4P9mTZgmrHO+bMs36qLpqCHdQL8iwb/a2CoVRHSXElwk2pPpdh76sWB2V/XyCljIHMXkT/XZCwYO0Z/BKOcNuIC6ZKFZAfUxcDVj3fDTOVIly0j8JrgK9AkBF1lLkoK+rHvASSXwAXrN+wjpnF/Tl9Wn9WubyVc3jj7SSuEuL61VkLOsH8YjyGRyCNxK18PAlFie2VKmxAYCzDqzEmmdSUrxtFO7/Fuis+Yh3ej7BS2zdpjIIu7iMXKaxF9+u9t0zB554QFrneHKeLRyRi7qX2MSZhAl7Bok68ZbAniBkHYDSrthmZePpv0icK64d+5clmn5kc63R7hhi5Ac4K1SQ3q1whu37VZZ/Vz3l6S3Fd0BVyTlBE0SHrxgcH+c2RXkkjRhEShRCm676kUqCABm8k8YT9mrluM6uwm6MIJ8dWdwxoYIhlHfjyxerPRzZfQmFSlpiZZSzj0WopwMjtYH+FAchntJ0S65Ce00mgeeJ+EeYA5l8QBwhlhP+goWn3+WD7Uob3zo5h4vM19poPgdqOwiKXjtTxkKWZ0Z/cSIpjH7imVjNJCktcEPm/cA+rlzS8q7Wxo6SNcO5RyRupvJ00rcfmdAc1yfa7VSqBitNTkQ87CN9hvLeqf4fEtB36rusbNbi516nBjwbe1PTOEqGeIoauuRlml3N2/0AVsyL06SX0tduXPZn55UQ7gcPOx8h1WRl+nfeZuxb+jaJnJAP7Lvga+CGTY/CZRpEjtOA9hTVitwmHXMp6ztW5ABUyaeL1GkCHssS38Kad1L8ZBx6zNwDibuSYRtPY/V8AsE1UUJEZRQV/FzHOpADRWVmuw2MVSiS/YCIhfTNL/AmXLn8bHSsqUVKOcmzTPy5+4VP6Yi4WTq4q7NpgApxWqIkR5uUnuZfHyMHid7LC8T273zBePQ5PTFjHMdOdNxo7spqJfOmM3LxwbNKhSCs+tzeWjycg/MSGrcqwIE0D36HLWOxfmiCcr3Iqu80aG1SiEwn5Cn/5Vol6z0LMePxIfzg+zQ+hiuTuDgQU/+hNtLjwGAJeAksJItFMwK47LMb9zwUHCwDPYyKJ5c8pisrKKyNEDPRaPEsG9SWO6w97fa488JMjc/jZFgT1CdA7zyOQNKC8rz7CdRJhvwyqXJimzFgz4dtrxHkVf0/PynAtpmZVMmj9xaiIEoKOYBo1M4aOSDkAS0pXBIwedoW8qq+HPi++0ytsOf+bTTBNjpAA52ioydGi0HknwPNZkFXUCo68EC0lLFuQIfnV1cuMm0vwa3B4HNZ/+/YcocuhZ5Ht4rAXikMBVpyy4lxqi31udcsnCieZOAEsj3T3Un1TZD41LO0lxJz4xSmIUgnFdEBN9rGTi74qOZsva2KYf8bi9mrfpN4qbGbuaHYfyvxOTUKmfMA6wd/wu7Hpl6di3UIHVgYoVQ8LSVxUiZ7XIGG4EIaNHXo+vpLGewsq1Td9LH7lXpdnxGBzD8MJw0y8Mfm5zKoFR8h8wnGZ/uxc0Ne9V5JrmwR1YoFOIyj/9CDuIL5I2i3KUCRzECxh2nf1RH9NQ3hYdFRJ1WagB9lHmwB2+v2koDV7Q2L5Z8KOhPZPcUjdaC04I0LU9XGyUCTsPmxsQO25p6TBhsEZI9N+7Inul3cKdXubp+hglaKmOpy+kAJog2EaWW2QpEKVe5STr1W+1NV8Sy05JIIc3Z9He0D0U7APsysMXWlTVENO1xyoFJw4SQNMj73Me1Biqc0U1U7Ob9XO0RfaGvpDkeEfWkm+XU0IVh4xq6+eO1qEpQZPLmx9bD9Jz1lJMLBQURUSjdwFgfSBjNh1/pERfyIltOt6boE5VIRQsq4TFt0epC0lEFQ+vQTNYSw3JkmUqeMCvQ3jtC5JkiAIjud6R6piJmtl+vk/YygLBQxIm60AWQxQqaEP0ywzTWo+6ilaNfzOKHiUWgBaVIdHMCSiyIwih2PGo1BQTsRTtvxm26XlrO828N67oiKztAqGs6WnNk75KOshAIu4bRqk1d02E2gvQ9hF7csdS3tztWatdT7xwksm8FFcWEWC41Xiqq+LzQB7dKFwAE4ZIAU1vmeUekfOSO/Ej5YJ/JohL/D3K73GuAPAHnYeJgCLj2pdkjaPiQ/MbwMIikpLZagSHevN7esHIsYGWAz+Y2mMjWX0HJu1py0yGKof77bqNTyFsJx7m61Q0aBwcwpbmgs9oRd7DbHB5lT1wBAXPEshemHT+NUpDAwJ710yS7CGmO2+ZZIpNZq3Y9WnlSZOzsKJ1F6OWAscxXH/4XSLDMQDkL5rm5ajcSDtybWANLvy/vbblQOEJqu5bIuUgyKy4I2jS20niP9bDUje1Sua8qSKPhN0OwFwEnMvMjri5L54YcpIoiE9KymBlcg43vS6txMAD6i1FUkjkIW8ZEZSejLc58p04rkk2SOrbuFugjIpViMh2XoMrWI1b47ukldPI0WAqeN5BMI+qtBXQGdnGOiSRXK3NgPrLM69VKpaKk/7yTOApip9cmrr775bRUgEUpSXpnuntITQ0QK6xWGPyG2yAnL4T3R3+Fda99ghzX/tR4CewvueJu/bP8GXKLJRhi4gIrPROyjF8pbBNiBuCjoa1jVI0X/kNiSvVO/DKvlxOFR7EDG2O1FtMfUY5KFJH5pUHEvfpIlO8q6lT4+aRRp9Qm0SVyqH7dfROPv7AKJQEgsPSao/AG8/9kbAowGqQZ/CiQYAOwgdoH+4Ke9d0M8rnR3T3qBD7VBPGxRznuXM8tNrQlivnnOQfu439DdqHvVgs7MPgj7Jy4ZoS44BF0q+DyopEinX0VpuA5HTMSdUvF1+KeP3rC0V6O8QcOSin4Ez3VYRQTEuKF27/BapEBJvUEHMRzED0gGbAZVG4j6WD25utmpwKUt7ELtAhuHIOJ7UaFPGDO1Mc6QhS5mv9x3a8LSMdbfMrqPmrOnrXwLjUKFsASrvd44zsFlufQ5olomoifHMeE5P3xhwDne0mLOQVjlPj0LZXfkV1ZuhpNpZpKFeGJOTRvDnRnqKFxzh6oryCvhkb2Hy7zACJU9tE/jmO+Yy2G2vVlY83MjimKbdPsMFQooUewIYMgfiUsqfN3B8R24w8M4RJjd95Q48j3Sv6haZKLFTFGsOkJt8gmeG5QSg4M6pTirU1diyvA4zfohHTbuVzhJW+kaIT2cEGSS6HjdpapeQKrVgnUQqjmVW2i5ORE+y2ZTsgm7xL870neUVaIrXhCfu+fnH0wmAXyFNIM8x92I69LgdPQ93UKExKmC3CnTJDzZqRGaz8Mtrs3UAWyB1RMKQZL4pQu4Erk9+PL1c+Puwrm7KqLXROHjzCQqj6V6J8sKaFM9JrnrSJSi30uWSCf/SZVbEjvJxDXeR2+4ssuHXl70yt/tm2s7cD7MTOBU9+1z1EiM/jrHUaCrrK7lyVsohzJBCj7UYc2G0mTXTkNnAzDWOnrRBM39D8HxGKWg5AcZcPCoBleutZ8OFvWVbjn6F/25vJ3wiJqbEwhFCwVrNsYmZp+qNvGLPSKHlzuW/RQZLD7I3EDf6IM0MZZydZUBy3BLQqbtgBQ0zJHXub2Fc/S1WUHFmt55WZ3n5q/WdzHoQ5XDUqpdThv1vRfEYC5CWHHKO3zSMOPB2FYlifCbjRKTwdLQnzC+AOUGXS12AlNO6ofHMqCQ/F21DpVd84vtgi6CjdmOVcQfk1U38pI2AD5BRf5UnkPiOnUUPYULeUiZohm+I2PJXryWzPdKitpS/VIehaQGFWYBHv8Lbp+m+1CfXiDHWT2URCJNZep0omp/ext9upi0XLP/GhpheaVUJzNqCEDHvvk7if08g54fu4QlLX/amZ+JFgw400RNIzk4r2H1rLGBXOZIAqunCDOnPRxMvMLZimaPoc8s5ZTrBnAC9zkZMDf2HRc9so4l6nyR+weK6AIrFlAWJ9UExelKBHqhgaGRr358fWzxBaVp0s28OmPZSKjUVnVZk2mTi7kFO/av141X3GloTPoPR2beu2imGVs7bwDZLYoxQHq4Zxe6xCD1t5HSVQO5Cd/Cd86Q6/8ohGtS+mLKmZsTPfnJGs2Yc/ihviN/I9BrNqqUp7gs7rJvAWPTG0SPQBG/54muwDZiAydKmUUwwuzsf46YW+mS4qKsV9JoSyyla59QmEI7BTXUbYcuTug+vSsV4eF++6A9QXSFWr1LhzWz5kLz4kTQOZwZetRT+O1mEDkKMjiCsMBJEndQyqMJW62bRbrHmuePvEZQ3pmW7LWL1uE7YKkmlwnF0bDUBBLr1FoTrffMFia71bZzAkK8BJnJwofoRaoU09dEYLJUL7U7pphWsktPsJ+zCQX5A1yWr1Q3V13Lsun53f26c30V+IK6GON3UWQAXhv25oX9yBQwIf8H9JtklV3hqmogKceZem0ydIBYrXIN36hFLnQbZQEfwLHUvl0mfYjMUKEDeNn9eDQuTTa2uPCaGZfp6zSj1z8wqIO01Lr/r8U7JZSnQny+IW1lib2nvN9Pm5OtqN5mnj5jAvf3v0FHZzOsHTndw1vtd8djmSm6C+JhiDcrqZWXE6aXbcp2W2mSCDQwZnJAqytJH+foYI4i36YnmnxyNEpnWOfDLAdJou4DpPfj+CZ499sJtltRACHXerSiicQkYA2g8XqrqvafilRb12uepaiOptWE+7bI+be/foEB/seM8+tdfYGMrYj2uj1s+xO+mqHmsdJXFwvhgm7vfqXe1ErLLW0+a8K3ub/tA4g9YLtJ4Pi9fzaSbJJjyEiIRQUwC1LnlExRL4nKh4AvqDhJH7OlZ2Mk82m7p8Y5LB+R1bBuCLlAZfi8ZrAHO3mjjpVISSedlVWq/MwmUOvRlojWypeiSn1Ca7/joRLu23aL0lKDdzs0UnbdL5eywHuVnR3whGi0WvUVyUySdjq08QOUmO0XB1/bod+IVH1VyNIRcKvNAAX29Nc53zQn2F3e0AoCuqHgf7r++qDga+eENVv2DIvSKIvVh9ENmBz0Db3bKZSGbtPdXXh5aiM3L+JUsy77UTQveThJig5iNhPB75aevhZ9HtuQxiTffDE9o1Ybqtbrxrk8AHQvuVkC+P7mEV49w62/MpZoygiLRahCmCG+dm', $key);
        $insertArray = json_decode($appres,TRUE);
        
        $formid     = $insertArray['formid'];
        $encounter  = $insertArray['encounter'];
        $user       = $insertArray['user'];
        $authorized = 1;
        $activity   = 1;
        $date       = 'NOW()';
        
        $logdata= array();
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
function saveROSFormData_c(){
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
        ///$appres = GibberishAES::dec('U2FsdGVkX19gZGa5SpNi9rPXRq5bxvyVpddeVgNzC3WCFa6FGEeWsUZcNf1jMI3yFwIzKShSst+ayJigFNFTArJCqFnGNwFcE6b6TEixwEaegTqhr7CaowejiUuTiImPJhCjf5Bj+3NUCBFgKIS4Hg0nAzKwXB/QBkRVzPhHdRZzCRQi/9DS9PEGzmTlwBSPiviz12xNI59rxA85GSNBFc8L/XsKoJx/vL/YgQsLzjivyr1nofIiuEeF6SLY6oc7TDUqUOA2HdPWIAAkPGvyb66qfc1nTuZzysswX9q5vsrjUw57E+rV+IAd0/cfcphPXVY1jxgBN+P5Vfxa7xFQRfGrCn/JmrCi7efSuYzLHKKs0KGIMCVeywvucKunJhAYnw1vqQMAaTUb/hvqSTgQdFyLndnjYOq1yLT0PlLV8VrytDKwIV02Mx6qKM0ne3lKXTov/g784NS+qZrqcVLMW8AUirA6Z5reUMHIaCilKeZHZG5oEsIG0g5NQP+gkCvfcW8kS7kCejaHrIZiWD9cR+A2HmxZklSoF5jVsgaBohLGv7iyoNmSKcUXywmI1Jvex7wgILILEXeIcF7YCZRPlbJs0XrMktjY5uaDKa1yjiIs/DrfDoq4h/d8pBohb3nRdWS+wCfBjvj0xAynOvio9J3TL4ztUv6Zi7HUq6P52VpH8lK4WPmPyQQvcyNY3vmOWz6JV3+XQuXUt6ySY5ugQXtK7WatVLc/5IdDJdVB4E2yaBr1K1UffrwqBT/GmdO7+KiYDNEZvQjC0ZTUXQ+mLdV2eqmxemV8OlK+xawPsHbdKoK37H/zRwerlB0y8elmxq7O8Dhouij4zPQa62dLNF4FZdnGZ1WESw/YPHJs99BkM61AMhRP1jxDQ+LMHE6gE38hGPlQNtOo5C6etqlO8/hzrXPOOUlKBhqB1vhNdZ4b5ZrD4xPb7Ig/QATcVhalDeo9YMKSlb1XBzzhhz0MxQbp1/S/St480UITYWOagJvywmimq3m9HZO+klbAgr6SBCzTKSTznVSHitor8tsoW32vWiJjDEMz5K/QiXHQRGGpZfAMl1xKg7hxorPoom+jIi9w454ymn2eaNAUscPjx2K0XUYy6itNhT8f90+q/p1ZMLqbynqiJLKPEptFctnSmsaxteSv3CyYQuOMOixAjsCBsARnO4vHcfSQMnbXmyCs/nJLzQYDvGL4MH9gp5jhx2oqnxRjYh9qYz5k++ibUD4SqbRlVt8pGA02amcDA3KiLN9Id+mudhs5hykftDQwFEzG1KRlLceJQlyypXJcpLJqvaKYlmjvMpxD8FhDUwCiU4LFyTWUEH2l0ln8/3dCmUQT83Bpe42b94kLKOborUzNXEhrCTXph0PrdKugNyI2POsjwIcX5EQZA+8QMJ0DaXVNmvIhzrBA9Cv3hYJzM2OOnrnb8+1Y9ftpiNvgdbmEqashJ4rT52emi02af0yqyrAkSfBr+VIElsUZt8wH4nhFtnBkmdmBenuCbOb+Gf+fjLqdrt8f73XaH5MsP4E6dMcq6+VSzhcyM5F0fyjB3wC1KhinXuNjZ71Mp1dN47KCEExBPmgrczr6hbqLiylkohdVUGcfJKQh9I2Z+POp9hAe4e4VGi8vL2lJGr9DhHZ5ejFSBOkRwl78/txn773A/FckcKtnO1s5Z5dQfNhCcO8AIY0O+KOseIbseo27dU5A2CEm2rU7swYZxkl5seEqec4l1FkuSVlbMmIehkR6HTIieqVz/8/4G2stP1204l3zIyn6cAyxOI7V6AYg8J6WoFfxFSdnoBQP+WbTup2BxL4Bw1kwTQWaUpxqqJbt8w1NXYwPqbyTeoEX55hM0NZOYkR6pacKrYW6hUO7C5/cYv7uGkUsI3ZbAZmRK3hQlwMw4+UZ7goYSV7O8bKDBYx+YHv7etRigpHvwYoViFkUgkb2KMmgG7XQl69q2Xx9YsWjeIODpkEeVm0woITtmkRc7R02Mq+OK0cyCw01vNfxmyIa05qRCEja0l7X/qDjCn9cE6kNcWUsHYdVdCzLxJLVSBehJvx5A24IPbvSO9wXKcMIQMY8RUyq1qyWJsmFxvZBIc4NKJOmLXmsXl81cjNWxbDDo0Lwb9CQiR+SLeWM8KzFoQNn7fI0rKA+DN4kYnMHp5IRSrhDrPlF6cPbpZITe1D9PdmaudKdWUXHEwTehw8Jj5/R26yxlKymYgSDvp/yBOPZwGsLe3M/sL7O1/rjxFChwbIh/0CouDFEUJeCf8y4Nr+sET6ypwIqKQa/fAsQQv9vINRaU068PgANGSwvZlDy4xCSLEbWigI95IXXTMTX2NGLe8sCHSoCepcuBDKrs2lGZbH0NxiRPvRPcIIfZBZJEU2JCod0Hkx+Cupn8y9jvg9e/YOPHlHmpXX4HlCig8xxxTTBuSgqKr5tcSXKkyKZLFecRDZZLM4uV401yIbHyxHF2MTeHeBmG4dHCQNWq5OZrPyrcvEO3tIjT/lhr31rpBMVHonj2uQr30HQieWGqWohtR2D9E6npRP1fEbEbc5OEN/opzNFGLmO8hghaTDJvk4lH4AU0r2AGUeYIwF2gNJGaE9rOVw2BpL41Xz5qg0OkUNqBJ5QxkK8/m55qOf3DQil0zxiqKFIdn3kY4x7SM1peFLyqnT3OuNwUplKerzwdawqfN1Y/d1qU9GfFQCJ2gZg27NNyXR/Fos8RUVb+gE58gUkhfYrFSDyrNGzf8IrQ15836fgFmqRKaPJhsIx7jglaYZsR+KU+EnPNWtx+NJaqATzV+0JkRvpLL1j2YM9XMK/6Qd0AQdjTne1zes6YmwMferlkjWZ6COxVHaFSKHklVfa1oLyDY+ZzkxZDMK4IbWdhixJqkGayG0lF7JZaCorAtMCI3vwdzbZu36N26VW/wW07ErX0aXQ/ygPW3vCSKl7xmERkyPyu+trlWS26Bub2QQG9+AbG76VlZiG9+vY9Y39Rh0uzIdyFVSwZNnFcX6mkSdl8D3zHRwYTOtUxY8zIb1ZssZFiDGTCnkZ3huscPZbVk/5hipVRlXj1Tj6CZjEnM56ZDdBkDHBnkSFkCNKI51zGEV4vXRRKq/Mc42GzwUN1Hi1Jebd7/rH5YQbHfNnfLUDtT3NPgRDR6na7R7f+z0dyPii23x2W+aIhJ1QpAY6YDDYhPswiIStME04k2Uuc8qItnOU3smW+WkuJcnKobwo4hhAT1gx3CTc6rn65bfM8YjBu/+GYF2Li0GtUmf3vd0cm3lsxS+mzCE21s4r0z38jUJ0xvZZJVSfCgCQ16MQT3L5IlMeKPMcZ3Rx40NggJw7WA5bG3gDtURqCqEkiuynFo/g5s46oSnFAiJE9J7Ifb9kAykpxd77bBl0/7ODmwz024hvL1UjHRW4IqH00uURYvdqLKZt9pV07OvM9krQrwx2dqst/+li2PXO5AsUYVVPnpg4aqfBr/RdNBqLCyp1cz63s5uE6ycR5L9ZmpluaI9IgxoduZxFm0El/6YtXY4msaO9MZE/nc9n6cpX4k8SZINtR/4WfFNXeRBxzR9Zo+Oyy9G24xua4Dg7EfyDzPpQcllbng8QCBEH5ZBICziPDCMkuGhmFU+p4mqTO6t8pr/NbuyJAjTUWJozhghgfw5iGwjKG6iHOFtiOGP3mtiJz0fkCNZIuasmOXYDK6WQvsijgJr1HW7We4mFuCAYvjix4zenF7GRq9Ja42lvI3/bONmODePd2mjRUu0POfjHV6k82AxwL/JtvKrDke58vNYzFrqdQiYY28T25ZHjNvPA9kZKBsvIGXe/TIaXST3AQrWelKSKw08or+9q17bwoHqRkMfjt4F418yv1PxRBJrU+265PqihypM0/CzP46bnzm2/WtugLRh2HVUD00YGyebzB6qs8kRsLaorKl+IYhWYTBKExnxDMrciEB4J1EVX5U1rCzPIC16ndTmR3a9hhjVFi6IkzvF9BWrKSSQEGkJU1A+nIbDtmzbPh+2Lf/okuxw3iRcJto3wfZPPLKY6ZC+/4/I3prA172fLYXeOvlfZ4kpgPoo1NKr5eaHj7ee3+G1yyy7q5OLArGObzz3QeF6W8b+d8WjzXEIIHQhYdEaA+FxqwSe+0hvd8xKC+wcSbbsWiyWO2Nrx0g+HtdkPE8OSL0/OB1G6xyeWA3WCnnI0T9q7ncWZ0fojU1k4PXOZo3naBlRRuodqzOlgYggzvvsLCCgRvw3ZE63/yDE4un3PrmbPgApE2EY91hmtpi3DQwLq9bLPalcdEZm8PWB6MVcbKoKNhQIC5Nz5F0HaXxLE8IrjcnbHedal5l+uUPXuIoS7SvcxtnEaP0WzM1Xx5k6580KPN7qkH3qrytdzHt3QmZeOhRYkd4uGwIrxAQUdqVdG56DDoAnns1ykPHxp+0f36kfh2AYgUHK4Al5xjz2NSqG13OeNTN107g7RNdEoyB88lCzvDQ6nwxWW6qzC9RoJ7HRu+IspoRrjC9aS6X7UL+iC51ZSkn5tdolox2jQcJYVmNd4c3jfE0uhd5YFRJJQBHlwyPJQeFjqQMr/1mRZn0nQ1j444rhUKuPFsbwxxdSZGG5Nbm9yFRLvMBaIhIpcztptRrewC7fgeBwKN03OLvhA3RJvp5GqZVL11upLqD8z5WEnfJ3t/GEpN26Gmcy9lkFN10PnqOx9BMOVtrwRMqaGCkVbl9d7siLCp9sF53NRwSe91BIKS83UtE/GkgASLEcoobOEDJjKcaMSl2IZRoCJm3QcxCJ6Ox5Ysfe3gfwFeD6I8lBls+BIPez6NFeBv5PzSmp3NSQcSmjROaTmOV1H/1pfM8VMk+Ue8pOc9Whqr2oy2l5NJ05udypwDWf0C9HOSbyNcDepQUQoJsR+bjbHF1RTfYmTF581jgS3Aga6w09qjzQ8bR45Qj5soyi35687v6ppAAhND8+HZe0eYre803mIC/1zhGUunZ1ouLmPgY1EO1GJmvVR+y6Gea87PDSCHvRaNCKB0fNqOBMyG8vvTD2lgHOF9WKgG4HOPB5W26Xfre02Lr4G4fr65YlMZrBjmKVd8lrfSsUwZddS97ynsKEUxvOo1iNCBWI/vseboO25bx03tdjU91pS28ywWM41qZchLNO+A0sGxLpOSBXx/7GobWxZ9HMNOyd3OfvckaclZD0ISYCUdBPI9MrXBEQU0Hrvav4TRgsWzJTuJ+cCsLEP+qUPd3NWb7TEKKa+LcdNUmsTZyXlXO/Y7ZUtAmhaiWlrJFWAavcLbSNt9B+hDrkLnUIrYnkGJHJONo9XjEQCO1PuDTY4Y5ZpJU1VXYywbrjUiF3vTG1d4OcvXj+6Oh/oa8JbJOkPP4Tlm6q0/jL3r/MgKooFVmDO1uQJnHAxYPPsnFWK6pyeRFqRescn40vWXnnRajSR/wdgncf56pu00kmVGUkYhJSlFpeEMdX7BAXdkPmEJEOdBEEXiRH+Dl0d0IT0oOriR+K4YKeTn2vBUwwkQvRv7N/uWb44+IsCGSCEM2tFihAkTqbUyYaRtc4eXJwH8EAyG3F0U0JKCSds0HTJS3aDP0peOBkngcsbzOYY6Lobd0e1/rCkYNftkpWToDoq2d9y+hiLZzYChNzEd/tVeKhTotm+yg4rMjzuU/1Pk2E2RZiOaKKjd6aNyTi38g2VUfFklKUr2Df1XZmkeTPHbocT6JmvgbYDVUDub7Bdf+2YTauM+9sFWXtI4Ai57/v3ufCC2+rSqud4hwY5EaN2H+x2csOJfn1v9gMcRNPfp3mIy9jtg4uRapi+IAD5/ssJgTIbpMbvBAxWaUadDpS2le7pF+oa71Dh+BJqu0C5', $key);
        $insertArray = json_decode($appres,TRUE);
        
        $formid     = $insertArray['formid'];
        $encounter  = $insertArray['encounter'];
        $user       = $insertArray['user'];
        $authorized = 1;
        $activity   = 1;
        $date       = 'NOW()';
        
        
        $logdata= array();
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
function saveROSFormData_old(){
    try 
    {
        $db = getConnection();
        $request = Slim::getInstance()->request();
        $key = 'rotcoderaclla';
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        $appres = GibberishAES::dec($request->getBody(), $key);
        //$appres = GibberishAES::dec('U2FsdGVkX1+PDIp5vH5b8nW8sIhRYnajArp1nDRHkK5PMp+zwehlrXXg5pCvq4zEXAQXrf3SQpkzHWms6O/gEwdB2jWyhouONy8EWraJWor+IX5HMRi+SK4yaw48qhymEB0qnA3D1SbLvJMzuy71pk665CSLusHAAPX6yKpgm5mgZQOuy2cWs7DPNZ8kYDn8QBcQZRs1Qkf+n8julUBZK5BHGBrhksvZ8MPxe23cC1MFP8QfIr0t3WHcaxmEdsU9Q6ZLQFrQRkuAz7XMYGw4ePUdnbhMvaCYG9uXqpxkh1XpZJ0v8f49Ij3iYLcBbkX1nPS+30rnA344JdqpvXFEiCcuMAEqvL6nEsqiKyz++9kmw/Xjknt0k6FU0rnboEaWtmpplhH3VmRArYbtN2mFp15ONDKEitTKZWiRsX+SoUXCM0v38b5aJ0J0MBHV4gJmg+goHnagpGd5hDgra+lNh6fMa6bMsUo2pTsyG6gitF7/THin6YmRhzR+iszkrgyjfwa5HdY4ECLLD+KZVZf3VNw+iep97YQv6fTHaA7tb2c=', $key);
        $insertArray = json_decode($appres,TRUE);
        
        $formid                     = $insertArray['formid'];
        $encounter                  = $insertArray['encounter'];
        $pid                        = $insertArray['pid'];
        $user                       = $insertArray['user'];
        $authorized                 = 1; 
        $activity                   = 1 ;
        
        
        $constitutional             = str_replace( " ", '_', $insertArray['constitutional']); 
        $eyes                       = str_replace( " ", '_', $insertArray['eyes']);  
        $ent                        = str_replace( " ", '_', $insertArray['ent']);    
        $breast                     = str_replace( " ", '_', $insertArray['breast']);    
        $respiratory                = str_replace( " ", '_', $insertArray['respiratory']);   
        $cardiovascular             = str_replace( " ", '_', $insertArray['cardiovascular']);    
        $gastrointestinal           = str_replace( " ", '_', $insertArray['gastrointestinal']);    
        $genitourinary              = str_replace( " ", '_', $insertArray['genitourinary']);    
        $genitourinarymale          = str_replace( " ", '_', $insertArray['genitourinarymale']);   
        $genitourinaryfemale        = str_replace( " ", '_', $insertArray['genitourinaryfemale']);   
        $musculoskeletal            = str_replace( " ", '_', $insertArray['musculoskeletal']);    
        $extremities                = str_replace( " ", '_', $insertArray['extremities']);    
        $neurologic                 = str_replace( " ", '_', $insertArray['neurologic']);   
        $skin                       = str_replace( " ", '_', $insertArray['skin']);   
        $psychiatric                = str_replace( " ", '_', $insertArray['psychiatric']);   
        $endocrine                  = str_replace( " ", '_', $insertArray['endocrine']);  
        $hai                        = str_replace( " ", '_', $insertArray['hai']);    
        $neck                       = str_replace( " ", '_', $insertArray['neck']);   

        
        
        $constitutional_text        = $insertArray['constitutional_text']; 
        $eyes_text                  = $insertArray['eyes_text'];  
        $ent_text                   = $insertArray['ent_text'];  
        $breast_text                = $insertArray['breast_text'];  
        $respiratory_text           = $insertArray['respiratory_text']; 
        $cardiovascular_text        = $insertArray['cardiovascular_text'];  
        $gastrointestinal_text      = $insertArray['gastrointestinal_text'];  
        $genitourinary_text         = $insertArray['genitourinary_text'];  
        $genitourinarymale_text     = $insertArray['genitourinarymale_text'];  
        $genitourinaryfemale_text   = $insertArray['genitourinaryfemale_text'];  
        $musculoskeletal_text       = $insertArray['musculoskeletal_text'];  
        $extremities_text           = $insertArray['extremities_text'];  
        $neurologic_text            = $insertArray['neurologic_text']; 
        $skin_text                  = $insertArray['skin_text']; 
        $psychiatric_text           = $insertArray['psychiatric_text']; 
        $endocrine_text             = $insertArray['endocrine_text']; 
        $hai_text                   = $insertArray['hai_text'];  
        $neck_text                  = $insertArray['neck_text'];  
        
        $weight_change              =$insertArray['weight_change']; 
        $weakness                   =$insertArray['weakness']; 
        $fatigue                    =$insertArray['fatigue']; 
        $anorexia                   =$insertArray['anorexia']; 
        $fever                      =$insertArray['fever']; 
        $chills                     =$insertArray['chills']; 
        $night_sweats               =$insertArray['night_sweats']; 
        $insomnia                   =$insertArray['insomnia']; 
        $irritability               =$insertArray['irritability']; 
        $heat_or_cold               =$insertArray['heat_or_cold']; 
        $intolerance                =$insertArray['intolerance']; 
        $change_in_appetite         =$insertArray['change_in_appetite']; 

        $change_in_vision           =$insertArray['change_in_vision'];
        $glaucoma_history           =$insertArray['glaucoma_history'];
        $eye_pain                   =$insertArray['eye_pain']; 
        $irritation                 =$insertArray['irritation'];
        $redness                    =$insertArray['redness'];
        $excessive_tearing          =$insertArray['excessive_tearing']; 
        $double_vision              =$insertArray['double_vision']; 
        $blind_spots                =$insertArray['blind_spots']; 
        $photophobia                =$insertArray['photophobia']; 
        $glaucoma                   =$insertArray['glaucoma'];
        $cataract                   =$insertArray['cataract']; 
        $injury                     =$insertArray['injury'];
        $ha                         =$insertArray['ha']; 
        $coryza                     =$insertArray['coryza'];
        $obstruction                =$insertArray['obstruction']; 

        $hearing_loss               =$insertArray['hearing_loss'];
        $discharge                  =$insertArray['discharge']; 
        $pain                       =$insertArray['pain'];
        $vertigo                    =$insertArray['vertigo'];
        $tinnitus =$insertArray['tinnitus']; 
        $frequent_colds =$insertArray['frequent_colds']; 
        $sore_throat =$insertArray['sore_throat'];
        $sinus_problems =$insertArray['sinus_problems']; 
        $post_nasal_drip =$insertArray['post_nasal_drip']; 
        $nosebleed =$insertArray['nosebleed'];
        $snoring =$insertArray['snoring']; 
        $apnea =$insertArray['apnea'];
        $bleeding_gums =$insertArray['bleeding_gums'];
        $hoarseness =$insertArray['hoarseness'];
        $dental_difficulties =$insertArray['dental_difficulties']; 
        $use_of_dentures=$insertArray['use_of_dentures']; 

        $breast_mass =$insertArray['breast_mass']; 
        $breast_discharge =$insertArray['breast_discharge']; 
        $biopsy =$insertArray['biopsy']; 
        $abnormal_mammogram=$insertArray['abnormal_mammogram']; 

        $cough =$insertArray['cough']; 
        $sputum =$insertArray['sputum'];
        $shortness_of_breath =$insertArray['shortness_of_breath']; 
        $wheezing =$insertArray['wheezing']; 
        $hemoptsyis =$insertArray['hemoptsyis']; 
        $asthma =$insertArray['asthma']; 
        $copd =$insertArray['copd']; 

        $chest_pain =$insertArray['chest_pain']; 
        $palpitation =$insertArray['palpitation']; 
        $syncope =$insertArray['syncope']; 
        $pnd =$insertArray['pnd']; 
        $doe =$insertArray['doe']; 
        $orthopnea =$insertArray['orthopnea']; 
        $peripheal =$insertArray['peripheal']; 
        $edema =$insertArray['edema']; 
        $legpain_cramping =$insertArray['legpain_cramping']; 
        $history_murmur =$insertArray['history_murmur'];
        $arrythmia =$insertArray['arrythmia']; 
        $heart_problem=$insertArray['heart_problem']; 

        $dysphagia =$insertArray['dysphagia']; 
        $heartburn =$insertArray['heartburn']; 
        $bloating =$insertArray['bloating'];
        $belching =$insertArray['belching']; 
        $flatulence =$insertArray['flatulence']; 
        $nausea =$insertArray['nausea']; 
        $vomiting =$insertArray['vomiting']; 
        $hematemesis =$insertArray['hematemesis']; 
        $gastro_pain =$insertArray['gastro_pain']; 
        $food_intolerance =$insertArray['food_intolerance']; 
        $hepatitis =$insertArray['hepatitis']; 
        $jaundice =$insertArray['jaundice']; 
        $hematochezia =$insertArray['hematochezia']; 
        $changed_bowel =$insertArray['changed_bowel']; 
        $diarrhea =$insertArray['diarrhea']; 
        $constipation =$insertArray['constipation']; 
        $blood_in_stool=$insertArray['blood_in_stool']; 

        $polyuria =$insertArray['polyuria']; 
        $polydypsia =$insertArray['polydypsia']; 
        $dysuria =$insertArray['dysuria'];
        $hematuria =$insertArray['hematuria']; 
        $frequency =$insertArray['frequency']; 
        $urgency =$insertArray['urgency']; 
        $incontinence =$insertArray['incontinence']; 
        $renal_stones =$insertArray['renal_stones']; 
        $utis =$insertArray['utis']; 
        $blood_in_urine =$insertArray['blood_in_urine']; 
        $urinary_retention =$insertArray['urinary_retention']; 
        $change_in_nature_of_urine =$insertArray['change_in_nature_of_urine']; 

        $hesitancy =$insertArray['hesitancy']; 
        $dribbling =$insertArray['dribbling']; 
        $stream =$insertArray['stream']; 
        $nocturia =$insertArray['nocturia']; 
        $erections =$insertArray['erections']; 
        $ejaculations =$insertArray['ejaculations']; 

        $g =$insertArray['g']; 
        $p =$insertArray['p']; 
        $ap =$insertArray['ap']; 
        $lc =$insertArray['lc']; 
        $mearche =$insertArray['mearche'];
        $menopause =$insertArray['menopause']; 
        $lmp =$insertArray['lmp']; 
        $f_frequency =$insertArray['f_frequency']; 
        $f_flow =$insertArray['f_flow']; 
        $f_symptoms =$insertArray['f_symptoms']; 
        $abnormal_hair_growth =$insertArray['abnormal_hair_growth']; 
        $f_hirsutism =$insertArray['f_hirsutism']; 

        $joint_pain =$insertArray['joint_pain']; 
        $swelling =$insertArray['swelling']; 
        $m_redness =$insertArray['m_redness']; 
        $m_warm =$insertArray['m_warm']; 
        $m_stiffness =$insertArray['m_stiffness']; 
        $m_aches =$insertArray['m_aches']; 
        $fms =$insertArray['fms']; 
        $arthritis =$insertArray['arthritis']; 
        $gout =$insertArray['gout']; 
        $back_pain =$insertArray['back_pain']; 
        $paresthesia =$insertArray['paresthesia']; 
        $muscle_pain =$insertArray['muscle_pain']; 
        $limitation_in_range_of_motion =$insertArray['limitation_in_range_of_motion']; 

        $spasms =$insertArray['spasms']; 
        $extreme_tremors =$insertArray['extreme_tremors']; 
        
        $loc =$insertArray['loc'];
        $seizures =$insertArray['seizures']; 
        $stroke = $insertArray['stroke']; 
        $tia =$insertArray['tia']; 
        $n_numbness =$insertArray['n_numbness']; 
        $n_weakness =$insertArray['n_weakness']; 
        $paralysis =$insertArray['paralysis']; 
        $intellectual_decline =$insertArray['intellectual_decline']; 
        $memory_problems =$insertArray['memory_problems']; 
        $dementia =$insertArray['dementia']; 
        $n_headache =$insertArray['n_headache']; 
        $dizziness_vertigo =$insertArray['dizziness_vertigo']; 
        $slurred_speech =$insertArray['slurred_speech']; 
        $tremors =$insertArray['tremors']; 
        $migraines =$insertArray['migraines']; 
        $changes_in_mentation  =$insertArray['changes_in_mentation']; 

        $s_cancer =$insertArray['s_cancer']; 
        $psoriasis =$insertArray['psoriasis'];
        $s_acne =$insertArray['s_acne']; 
        $s_other =$insertArray['s_other']; 
        $s_disease =$insertArray['s_disease']; 
        $rashes =$insertArray['rashes']; 
        $dryness =$insertArray['dryness'];
        $itching =$insertArray['itching']; 

        $p_diagnosis =$insertArray['p_diagnosis']; 
        $p_medication =$insertArray['p_medication']; 
        $depression =$insertArray['depression']; 
        $anxiety =$insertArray['anxiety']; 
        $social_difficulties =$insertArray['social_difficulties']; 
        $alcohol_drug_dependence =$insertArray['alcohol_drug_dependence']; 
        $suicide_thoughts =$insertArray['suicide_thoughts']; 
        $use_of_antideprassants =$insertArray['use_of_antideprassants']; 
        $thought_content =$insertArray['thought_content']; 

        $thyroid_problems =$insertArray['thyroid_problems']; 
        $diabetes =$insertArray['diabetes']; 
        $abnormal_blood =$insertArray['abnormal_blood']; 
        $goiter =$insertArray['goiter']; 
        $heat_intolerence =$insertArray['heat_intolerence']; 
        $cold_intolerence =$insertArray['cold_intolerence']; 
        $increased_thirst =$insertArray['increased_thirst']; 
        $excessive_sweating =$insertArray['excessive_sweating']; 
        $excessive_hunger =$insertArray['excessive_hunger']; 

        $anemia =$insertArray['anemia']; 
        $fh_blood_problems =$insertArray['fh_blood_problems']; 
        $bleeding_problems =$insertArray['bleeding_problems']; 
        $allergies =$insertArray['allergies']; 
        $frequent_illness =$insertArray['frequent_illness']; 
        $hiv =$insertArray['hiv']; 
        $hai_status =$insertArray['hai_status']; 
        $hay_fever =$insertArray['hay_fever']; 
        $positive_ppd =$insertArray['positive_ppd']; 

        $stiffness =$insertArray['stiffness']; 
        $neck_pain =$insertArray['neck_pain']; 
        $masses =$insertArray['masses']; 
        $tenderness =$insertArray['tenderness']; 
        
        
        
//        $constitutional   = 'Normal'; // $insertArray['constitutional']; 
//        $eyes   = 'Normal'; // $insertArray['eyes'];  
//        $ent   = 'Normal'; // $insertArray['ent'];  
//        $breast   = 'Normal'; // $insertArray['breast'];  
//        $respiratory   = 'Normal'; // $insertArray['respiratory']; 
//        $cardiovascular   = 'Normal'; // $insertArray['cardiovascular'];  
//        $gastrointestinal   = 'Normal'; // $insertArray['gastrointestinal'];  
//        $genitourinary   = 'Normal'; // $insertArray['genitourinary'];  
//        $genitourinarymale   = 'Normal'; // $insertArray['genitourinarymale'];  
//        $genitourinaryfemale   = 'Normal'; // $insertArray['genitourinaryfemale'];  
//        $musculoskeletal   = 'Normal'; // $insertArray['musculoskeletal'];  
//        $extremities   = 'Normal'; // $insertArray['extremities'];  
//        $neurologic   = 'Normal'; // $insertArray['neurologic']; 
//        $skin   = 'Normal'; // $insertArray['skin']; 
//        $psychiatric   = 'Normal'; // $insertArray['psychiatric']; 
//        $endocrine   = 'Normal'; // $insertArray['endocrine']; 
//        $hai   = 'Normal'; // $insertArray['hai'];  
//        $neck  = 'Normal'; // $insertArray['neck'];  
//
//        $constitutional_text = ' test text2'; // $insertArray['constitutional_text']; 
//        $eyes_text   = 'text2'; // $insertArray['eyes_text'];  
//        $ent_text   = 'text2'; // $insertArray['ent_text'];  
//        $breast_text   = 'text2'; // $insertArray['breast_text'];  
//        $respiratory_text   = 'text2'; // $insertArray['respiratory_text']; 
//        $cardiovascular_text   = 'text2'; // $insertArray['cardiovascular_text'];  
//        $gastrointestinal_text   = 'text2'; // $insertArray['gastrointestinal_text'];  
//        $genitourinary_text   = 'text2'; // $insertArray['genitourinary_text'];  
//        $genitourinarymale_text   = 'text2'; // $insertArray['genitourinarymale_text'];  
//        $genitourinaryfemale_text   = 'text2'; // $insertArray['genitourinaryfemale_text'];  
//        $musculoskeletal_text   = 'text2'; // $insertArray['musculoskeletal_text'];  
//        $extremities_text   = 'text2'; // $insertArray['extremities_text'];  
//        $neurologic_text   = 'text2'; // $insertArray['neurologic_text']; 
//        $skin_text   = 'text2'; // $insertArray['skin_text']; 
//        $psychiatric_text   = 'text2'; // $insertArray['psychiatric_text']; 
//        $endocrine_text   = 'text2'; // $insertArray['endocrine_text']; 
//        $hai_text   = 'text2'; // $insertArray['hai_text'];  
//        $neck_text  = 'text2'; // $insertArray['neck_text'];  
//        
//        $weight_change = 'N/A'; //$insertArray['weight_change']; 
//        $weakness = 'N/A'; //$insertArray['weakness']; 
//        $fatigue = 'N/A'; //$insertArray['fatigue']; 
//        $anorexia = 'N/A'; //$insertArray['anorexia']; 
//        $fever = 'N/A'; //$insertArray['fever']; 
//        $chills = 'N/A'; //$insertArray['chills']; 
//        $night_sweats = 'N/A'; //$insertArray['night_sweats']; 
//        $insomnia = 'N/A'; //$insertArray['insomnia']; 
//        $irritability = 'N/A'; //$insertArray['irritability']; 
//        $heat_or_cold = 'N/A'; //$insertArray['heat_or_cold']; 
//        $intolerance = 'N/A'; //$insertArray['intolerance']; 
//        $change_in_appetite= 'N/A'; //$insertArray['change_in_appetite']; 
//
//        $change_in_vision = 'N/A'; //$insertArray['change_in_vision'];
//        $glaucoma_history = 'N/A'; //$insertArray['glaucoma_history'];
//        $eye_pain = 'N/A'; //$insertArray['eye_pain']; 
//        $irritation = 'N/A'; //$insertArray['irritation'];
//        $redness = 'N/A'; //$insertArray['redness'];
//        $excessive_tearing = 'N/A'; //$insertArray['excessive_tearing']; 
//        $double_vision = 'N/A'; //$insertArray['double_vision']; 
//        $blind_spots = 'N/A'; //$insertArray['blind_spots']; 
//        $photophobia = 'N/A'; //$insertArray['photophobia']; 
//        $glaucoma = 'N/A'; //$insertArray['glaucoma'];
//        $cataract = 'N/A'; //$insertArray['cataract']; 
//        $injury = 'N/A'; //$insertArray['injury'];
//        $ha = 'N/A'; //$insertArray['ha']; 
//        $coryza = 'N/A'; //$insertArray['coryza'];
//        $obstruction= 'N/A'; //$insertArray['obstruction']; 
//
//        $hearing_loss = 'N/A'; //$insertArray['hearing_loss'];
//        $discharge = 'N/A'; //$insertArray['discharge']; 
//        $pain = 'N/A'; //$insertArray['pain'];
//        $vertigo = 'N/A'; //$insertArray['vertigo'];
//        $tinnitus = 'N/A'; //$insertArray['tinnitus']; 
//        $frequent_colds = 'N/A'; //$insertArray['frequent_colds']; 
//        $sore_throat = 'N/A'; //$insertArray['sore_throat'];
//        $sinus_problems = 'N/A'; //$insertArray['sinus_problems']; 
//        $post_nasal_drip = 'N/A'; //$insertArray['post_nasal_drip']; 
//        $nosebleed = 'N/A'; //$insertArray['nosebleed'];
//        $snoring = 'N/A'; //$insertArray['snoring']; 
//        $apnea = 'N/A'; //$insertArray['apnea'];
//        $bleeding_gums = 'N/A'; //$insertArray['bleeding_gums'];
//        $hoarseness = 'N/A'; //$insertArray['hoarseness'];
//        $dental_difficulties = 'N/A'; //$insertArray['dental_difficulties']; 
//        $use_of_dentures= 'N/A'; //$insertArray['use_of_dentures']; 
//
//        $breast_mass = 'N/A'; //$insertArray['breast_mass']; 
//        $breast_discharge = 'N/A'; //$insertArray['breast_discharge']; 
//        $biopsy = 'N/A'; //$insertArray['biopsy']; 
//        $abnormal_mammogram= 'N/A'; //$insertArray['abnormal_mammogram']; 
//
//        $cough = 'N/A'; //$insertArray['cough']; 
//        $sputum = 'N/A'; //$insertArray['sputum'];
//        $shortness_of_breath = 'N/A'; //$insertArray['shortness_of_breath']; 
//        $wheezing = 'N/A'; //$insertArray['wheezing']; 
//        $hemoptsyis = 'N/A'; //$insertArray['hemoptsyis']; 
//        $asthma = 'N/A'; //$insertArray['asthma']; 
//        $copd = 'N/A'; //$insertArray['copd']; 
//
//        $chest_pain = 'N/A'; //$insertArray['chest_pain']; 
//        $palpitation = 'N/A'; //$insertArray['palpitation']; 
//        $syncope = 'N/A'; //$insertArray['syncope']; 
//        $pnd = 'N/A'; //$insertArray['pnd']; 
//        $doe = 'N/A'; //$insertArray['doe']; 
//        $orthopnea = 'N/A'; //$insertArray['orthopnea']; 
//        $peripheal = 'N/A'; //$insertArray['peripheal']; 
//        $edema = 'N/A'; //$insertArray['edema']; 
//        $legpain_cramping = 'N/A'; //$insertArray['legpain_cramping']; 
//        $history_murmur = 'N/A'; //$insertArray['history_murmur'];
//        $arrythmia = 'N/A'; //$insertArray['arrythmia']; 
//        $heart_problem= 'N/A'; //$insertArray['heart_problem']; 
//
//        $dysphagia = 'N/A'; //$insertArray['dysphagia']; 
//        $heartburn = 'N/A'; //$insertArray['heartburn']; 
//        $bloating = 'N/A'; //$insertArray['bloating'];
//        $belching = 'N/A'; //$insertArray['belching']; 
//        $flatulence = 'N/A'; //$insertArray['flatulence']; 
//        $nausea = 'N/A'; //$insertArray['nausea']; 
//        $vomiting = 'N/A'; //$insertArray['vomiting']; 
//        $hematemesis = 'N/A'; //$insertArray['hematemesis']; 
//        $gastro_pain = 'N/A'; //$insertArray['gastro_pain']; 
//        $food_intolerance = 'N/A'; //$insertArray['food_intolerance']; 
//        $hepatitis = 'N/A'; //$insertArray['hepatitis']; 
//        $jaundice = 'N/A'; //$insertArray['jaundice']; 
//        $hematochezia = 'N/A'; //$insertArray['hematochezia']; 
//        $changed_bowel = 'N/A'; //$insertArray['changed_bowel']; 
//        $diarrhea = 'N/A'; //$insertArray['diarrhea']; 
//        $constipation = 'N/A'; //$insertArray['constipation']; 
//        $blood_in_stool= 'N/A'; //$insertArray['blood_in_stool']; 
//
//        $polyuria = 'N/A'; //$insertArray['polyuria']; 
//        $polydypsia = 'N/A'; //$insertArray['polydypsia']; 
//        $dysuria = 'N/A'; //$insertArray['dysuria'];
//        $hematuria = 'N/A'; //$insertArray['hematuria']; 
//        $frequency = 'N/A'; //$insertArray['frequency']; 
//        $urgency = 'N/A'; //$insertArray['urgency']; 
//        $incontinence = 'N/A'; //$insertArray['incontinence']; 
//        $renal_stones = 'N/A'; //$insertArray['renal_stones']; 
//        $utis = 'N/A'; //$insertArray['utis']; 
//        $blood_in_urine = 'N/A'; //$insertArray['blood_in_urine']; 
//        $urinary_retention = 'N/A'; //$insertArray['urinary_retention']; 
//        $change_in_nature_of_urine = 'N/A'; //$insertArray['change_in_nature_of_urine']; 
//
//        $hesitancy = 'N/A'; //$insertArray['hesitancy']; 
//        $dribbling = 'N/A'; //$insertArray['dribbling']; 
//        $stream = 'N/A'; //$insertArray['stream']; 
//        $nocturia = 'N/A'; //$insertArray['nocturia']; 
//        $erections = 'N/A'; //$insertArray['erections']; 
//        $ejaculations = 'N/A'; //$insertArray['ejaculations']; 
//
//        $g = 'N/A'; //$insertArray['g']; 
//        $p = 'N/A'; //$insertArray['p']; 
//        $ap = 'N/A'; //$insertArray['ap']; 
//        $lc = 'N/A'; //$insertArray['lc']; 
//        $mearche = 'N/A'; //$insertArray['mearche'];
//        $menopause = 'N/A'; //$insertArray['menopause']; 
//        $lmp = 'N/A'; //$insertArray['lmp']; 
//        $f_frequency = 'N/A'; //$insertArray['f_frequency']; 
//        $f_flow = 'N/A'; //$insertArray['f_flow']; 
//        $f_symptoms = 'N/A'; //$insertArray['f_symptoms']; 
//        $abnormal_hair_growth = 'N/A'; //$insertArray['abnormal_hair_growth']; 
//        $f_hirsutism = 'N/A'; //$insertArray['f_hirsutism']; 
//
//        $joint_pain = 'N/A'; //$insertArray['joint_pain']; 
//        $swelling = 'N/A'; //$insertArray['swelling']; 
//        $m_redness = 'N/A'; //$insertArray['m_redness']; 
//        $m_warm = 'N/A'; //$insertArray['m_warm']; 
//        $m_stiffness = 'N/A'; //$insertArray['m_stiffness']; 
//        $m_aches = 'N/A'; //$insertArray['m_aches']; 
//        $fms = 'N/A'; //$insertArray['fms']; 
//        $arthritis = 'N/A'; //$insertArray['arthritis']; 
//        $gout = 'N/A'; //$insertArray['gout']; 
//        $back_pain = 'N/A'; //$insertArray['back_pain']; 
//        $paresthesia = 'N/A'; //$insertArray['paresthesia']; 
//        $muscle_pain = 'N/A'; //$insertArray['muscle_pain']; 
//        $limitation_in_range_of_motion = 'N/A'; //$insertArray['limitation_in_range_of_motion']; 
//
//        $spasms = 'N/A'; //$insertArray['encounter']; 
//        $extreme_tremors = 'N/A'; //$insertArray['encounter']; 
//
//        $loc = 'N/A'; //$insertArray['loc'];
//        $seizures = 'N/A'; //$insertArray['seizures']; 
//        $stroke = 'N/A'; // $insertArray['stroke']; 
//        $tia = 'N/A'; //$insertArray['tia']; 
//        $n_numbness = 'N/A'; //$insertArray['n_numbness']; 
//        $n_weakness = 'N/A'; //$insertArray['n_weakness']; 
//        $paralysis = 'N/A'; //$insertArray['paralysis']; 
//        $intellectual_decline = 'N/A'; //$insertArray['intellectual_decline']; 
//        $memory_problems = 'N/A'; //$insertArray['memory_problems']; 
//        $dementia = 'N/A'; //$insertArray['dementia']; 
//        $n_headache = 'N/A'; //$insertArray['n_headache']; 
//        $dizziness_vertigo = 'N/A'; //$insertArray['dizziness_vertigo']; 
//        $slurred_speech = 'N/A'; //$insertArray['slurred_speech']; 
//        $tremors = 'N/A'; //$insertArray['tremors']; 
//        $migraines = 'N/A'; //$insertArray['migraines']; 
//        $changes_in_mentation  = 'N/A'; //$insertArray['changes_in_mentation']; 
//
//        $s_cancer = 'N/A'; //$insertArray['s_cancer']; 
//        $psoriasis = 'N/A'; //$insertArray['psoriasis'];
//        $s_acne = 'N/A'; //$insertArray['s_acne']; 
//        $s_other = 'N/A'; //$insertArray['s_other']; 
//        $s_disease = 'N/A'; //$insertArray['s_disease']; 
//        $rashes = 'N/A'; //$insertArray['rashes']; 
//        $dryness = 'N/A'; //$insertArray['dryness'];
//        $itching = 'N/A'; //$insertArray['itching']; 
//
//        $p_diagnosis = 'N/A'; //$insertArray['p_diagnosis']; 
//        $p_medication = 'N/A'; //$insertArray['p_medication']; 
//        $depression = 'N/A'; //$insertArray['depression']; 
//        $anxiety = 'N/A'; //$insertArray['anxiety']; 
//        $social_difficulties = 'N/A'; //$insertArray['social_difficulties']; 
//        $alcohol_drug_dependence = 'N/A'; //$insertArray['alcohol_drug_dependence']; 
//        $suicide_thoughts = 'N/A'; //$insertArray['suicide_thoughts']; 
//        $use_of_antideprassants = 'N/A'; //$insertArray['use_of_antideprassants']; 
//        $thought_content = 'N/A'; //$insertArray['thought_content']; 
//
//        $thyroid_problems = 'N/A'; //$insertArray['thyroid_problems']; 
//        $diabetes = 'N/A'; //$insertArray['diabetes']; 
//        $abnormal_blood = 'N/A'; //$insertArray['abnormal_blood']; 
//        $goiter = 'N/A'; //$insertArray['goiter']; 
//        $heat_intolerence = 'N/A'; //$insertArray['heat_intolerence']; 
//        $cold_intolerence = 'N/A'; //$insertArray['cold_intolerence']; 
//        $increased_thirst = 'N/A'; //$insertArray['increased_thirst']; 
//        $excessive_sweating = 'N/A'; //$insertArray['excessive_sweating']; 
//        $excessive_hunger = 'N/A'; //$insertArray['excessive_hunger']; 
//
//        $anemia = 'N/A'; //$insertArray['anemia']; 
//        $fh_blood_problems = 'N/A'; //$insertArray['fh_blood_problems']; 
//        $bleeding_problems = 'N/A'; //$insertArray['bleeding_problems']; 
//        $allergies = 'N/A'; //$insertArray['allergies']; 
//        $frequent_illness = 'N/A'; //$insertArray['frequent_illness']; 
//        $hiv = 'N/A'; //$insertArray['hiv']; 
//        $hai_status = 'N/A'; //$insertArray['hai_status']; 
//        $hay_fever = 'N/A'; //$insertArray['hay_fever']; 
//        $positive_ppd = 'N/A'; //$insertArray['positive_ppd']; 
//
//        $stiffness = 'N/A'; //$insertArray['stiffness']; 
//        $neck_pain = 'N/A'; //$insertArray['neck_pain']; 
//        $masses = 'N/A'; //$insertArray['masses']; 
//        $tenderness = 'N/A'; //$insertArray['tenderness']; 
//        
        $finalized  = $insertArray['finalized']; 
        $pending  =  $insertArray['pending']; 
        
        $logdata= array();
        $ip_addr=GetIP();
        $patientres = '';
        
        if($formid == 0){
            
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
            
            //$sql = "INSERT INTO `tbl_form_allcare_ros` ( `pid`, `activity`, `date`, `constitutional`, `constitutional_text`, `weight_change`, `weakness`, `fatigue`, `anorexia`, `fever`, `chills`, `night_sweats`, `insomnia`, `irritability`, `heat_or_cold`, `intolerance`, `change_in_appetite`, `eyes`, `eyes_text`, `change_in_vision`, `glaucoma_history`, `eye_pain`, `irritation`, `redness`, `excessive_tearing`, `double_vision`, `blind_spots`, `photophobia`, `glaucoma`, `cataract`, `injury`, `ha`, `coryza`, `obstruction`, `ent`, `ent_text`, `hearing_loss`, `discharge`, `pain`, `vertigo`, `tinnitus`, `frequent_colds`, `sore_throat`, `sinus_problems`, `post_nasal_drip`, `nosebleed`, `snoring`, `apnea`, `bleeding_gums`, `hoarseness`, `dental_difficulties`, `use_of_dentures`, `breast`, `breast_text`, `breast_mass`, `breast_discharge`, `biopsy`, `abnormal_mammogram`, `respiratory`, `respiratory_text`, `cough`, `sputum`, `shortness_of_breath`, `wheezing`, `hemoptsyis`, `asthma`, `copd`, `cardiovascular`, `cardiovascular_text`, `chest_pain`, `palpitation`, `syncope`, `pnd`, `doe`, `orthopnea`, `peripheal`, `edema`, `legpain_cramping`, `history_murmur`, `arrythmia`, `heart_problem`, `gastrointestinal`, `gastrointestinal_text`, `dysphagia`, `heartburn`, `bloating`, `belching`, `flatulence`, `nausea`, `vomiting`, `hematemesis`, `gastro_pain`, `food_intolerance`, `hepatitis`, `jaundice`, `hematochezia`, `changed_bowel`, `diarrhea`, `constipation`, `blood_in_stool`, `genitourinary`, `genitourinary_text`, `polyuria`, `polydypsia`, `dysuria`, `hematuria`, `frequency`, `urgency`, `incontinence`, `renal_stones`, `utis`, `blood_in_urine`, `urinary_retention`, `change_in_nature_of_urine`, `genitourinarymale`, `genitourinarymale_text`, `hesitancy`, `dribbling`, `stream`, `nocturia`, `erections`, `ejaculations`, `genitourinaryfemale`, `genitourinaryfemale_text`, `g`, `p`, `ap`, `lc`, `mearche`, `menopause`, `lmp`, `f_frequency`, `f_flow`, `f_symptoms`, `abnormal_hair_growth`, `f_hirsutism`, `musculoskeletal`, `musculoskeletal_text`, `joint_pain`, `swelling`, `m_redness`, `m_warm`, `m_stiffness`, `m_aches`, `fms`, `arthritis`, `gout`, `back_pain`, `paresthesia`, `muscle_pain`, `limitation_in_range_of_motion`, `extremities`, `extremities_text`, `spasms`, `extreme_tremors`, `neurologic`, `neurologic_text`, `loc`, `seizures`, `stroke`, `tia`, `n_numbness`, `n_weakness`, `paralysis`, `intellectual_decline`, `memory_problems`, `dementia`, `n_headache`, `dizziness_vertigo`, `slurred_speech`, `tremors`, `migraines`, `changes_in_mentation`, `skin`, `skin_text`, `s_cancer`, `psoriasis`, `s_acne`, `s_other`, `s_disease`, `rashes`, `dryness`, `itching`, `psychiatric`, `psychiatric_text`, `p_diagnosis`, `p_medication`, `depression`, `anxiety`, `social_difficulties`, `alcohol_drug_dependence`, `suicide_thoughts`, `use_of_antideprassants`, `thought_content`, `endocrine`, `endocrine_text`, `thyroid_problems`, `diabetes`, `abnormal_blood`, `goiter`, `heat_intolerence`, `cold_intolerence`, `increased_thirst`, `excessive_sweating`, `excessive_hunger`, `hai`, `hai_text`, `anemia`, `fh_blood_problems`, `bleeding_problems`, `allergies`, `frequent_illness`, `hiv`, `hai_status`, `hay_fever`, `positive_ppd`, `neck`, `neck_text`, `stiffness`, `neck_pain`, `masses`, `tenderness`, `pending`, `finalized`) VALUES (NULL, '76', '1', NULL, 'Normal', 'Normal text', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'Normal', 'Normal etxt', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'Normal', 'azsxdcfvgbg', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', '', '', 'N/A', 'N/A', 'N/A', 'N/A', 'Normal', 'zsxdcfvg', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'Normal', 'sdfvgb', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'Normal', 'azsxdcfvgb ', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'Normal', 'azsxdcf', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'Normal', 'ASDFV', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'Normal', 'AZSXDCFVGB', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'Normal', 'ASDFGH', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'Normal', 'ZSXDCFVGBHN', 'N/A', 'N/A', 'Normal', 'AZSXDCFVB', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'Normal', 'ZASXDCVB', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'Normal', 'ASDFGHJ', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'Normal', 'ASDFG', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'Normal', 'ASDFGBH', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'Normal', 'ASDFGBH', 'N/A', 'N/A', 'N/A', 'N/A', 'yes', 'no')";
            $sql = "INSERT INTO `tbl_form_allcare_ros` ( `pid`, `activity`, `date`, `constitutional`, `constitutional_text`, `weight_change`, `weakness`, `fatigue`, `anorexia`, `fever`, `chills`, `night_sweats`, `insomnia`, `irritability`, `heat_or_cold`, `intolerance`, `change_in_appetite`, `eyes`, `eyes_text`, `change_in_vision`, `glaucoma_history`, `eye_pain`, `irritation`, `redness`, `excessive_tearing`, `double_vision`, `blind_spots`, `photophobia`, `glaucoma`, `cataract`, `injury`, `ha`, `coryza`, `obstruction`, `ent`, `ent_text`, `hearing_loss`, `discharge`, `pain`, `vertigo`, `tinnitus`, `frequent_colds`, `sore_throat`, `sinus_problems`, `post_nasal_drip`, `nosebleed`, `snoring`, `apnea`, `bleeding_gums`, `hoarseness`, `dental_difficulties`, `use_of_dentures`, `breast`, `breast_text`, `breast_mass`, `breast_discharge`, `biopsy`, `abnormal_mammogram`, `respiratory`, `respiratory_text`, `cough`, `sputum`, `shortness_of_breath`, `wheezing`, `hemoptsyis`, `asthma`, `copd`, `cardiovascular`, `cardiovascular_text`, `chest_pain`, `palpitation`, `syncope`, `pnd`, `doe`, `orthopnea`, `peripheal`, `edema`, `legpain_cramping`, `history_murmur`, `arrythmia`, `heart_problem`, `gastrointestinal`, `gastrointestinal_text`, `dysphagia`, `heartburn`, `bloating`, `belching`, `flatulence`, `nausea`, `vomiting`, `hematemesis`, `gastro_pain`, `food_intolerance`, `hepatitis`, `jaundice`, `hematochezia`, `changed_bowel`, `diarrhea`, `constipation`, `blood_in_stool`, `genitourinary`, `genitourinary_text`, `polyuria`, `polydypsia`, `dysuria`, `hematuria`, `frequency`, `urgency`, `incontinence`, `renal_stones`, `utis`, `blood_in_urine`, `urinary_retention`, `change_in_nature_of_urine`, `genitourinarymale`, `genitourinarymale_text`, `hesitancy`, `dribbling`, `stream`, `nocturia`, `erections`, `ejaculations`, `genitourinaryfemale`, `genitourinaryfemale_text`, `g`, `p`, `ap`, `lc`, `mearche`, `menopause`, `lmp`, `f_frequency`, `f_flow`, `f_symptoms`, `abnormal_hair_growth`, `f_hirsutism`, `musculoskeletal`, `musculoskeletal_text`, `joint_pain`, `swelling`, `m_redness`, `m_warm`, `m_stiffness`, `m_aches`, `fms`, `arthritis`, `gout`, `back_pain`, `paresthesia`, `muscle_pain`, `limitation_in_range_of_motion`, `extremities`, `extremities_text`, `spasms`, `extreme_tremors`, `neurologic`, `neurologic_text`, `loc`, `seizures`, `stroke`, `tia`, `n_numbness`, `n_weakness`, `paralysis`, `intellectual_decline`, `memory_problems`, `dementia`, `n_headache`, `dizziness_vertigo`, `slurred_speech`, `tremors`, `migraines`, `changes_in_mentation`, `skin`, `skin_text`, `s_cancer`, `psoriasis`, `s_acne`, `s_other`, `s_disease`, `rashes`, `dryness`, `itching`, `psychiatric`, `psychiatric_text`, `p_diagnosis`, `p_medication`, `depression`, `anxiety`, `social_difficulties`, `alcohol_drug_dependence`, `suicide_thoughts`, `use_of_antideprassants`, `thought_content`, `endocrine`, `endocrine_text`, `thyroid_problems`, `diabetes`, `abnormal_blood`, `goiter`, `heat_intolerence`, `cold_intolerence`, `increased_thirst`, `excessive_sweating`, `excessive_hunger`, `hai`, `hai_text`, `anemia`, `fh_blood_problems`, `bleeding_problems`, `allergies`, `frequent_illness`, `hiv`, `hai_status`, `hay_fever`, `positive_ppd`, `neck`, `neck_text`, `stiffness`, `neck_pain`, `masses`, `tenderness`, `pending`, `finalized`) "
                    . "VALUES ( '$pid', '$activity', NOW(), '$constitutional', '$constitutional_text','$weight_change','$weakness','$fatigue','$anorexia','$fever','$chills','$night_sweats','$insomnia','$irritability','$heat_or_cold','$intolerance','$change_in_appetite', '$eyes', '$eyes_text','$change_in_vision','$glaucoma_history','$eye_pain','$irritation','$redness','$excessive_tearing','$double_vision','$blind_spots','$photophobia','$glaucoma','$cataract','$injury','$ha','$coryza','$obstruction', '$ent', '$ent_text','$hearing_loss','$discharge','$pain','$vertigo','$tinnitus','$frequent_colds','$sore_throat','$sinus_problems','$post_nasal_drip','$nosebleed','$snoring','$apnea','$bleeding_gums','$hoarseness','$dental_difficulties','$use_of_dentures', '$breast', '$breast_text','$breast_mass','$breast_discharge','$biopsy','$abnormal_mammogram', '$respiratory', '$respiratory_text','$cough','$sputum','$shortness_of_breath','$wheezing','$hemoptsyis','$asthma','$copd', '$cardiovascular', '$cardiovascular_text','$chest_pain','$palpitation','$syncope','$pnd','$doe','$orthopnea','$peripheal','$edema','$legpain_cramping','$history_murmur','$arrythmia','$heart_problem', '$gastrointestinal', '$gastrointestinal_text ','$dysphagia','$heartburn','$bloating','$belching','$flatulence','$nausea','$vomiting','$hematemesis','$gastro_pain','$food_intolerance','$hepatitis','$jaundice','$hematochezia','$changed_bowel','$diarrhea','$constipation','$blood_in_stool', '$genitourinary', '$genitourinary_text','$polyuria','$polydypsia','$dysuria','$hematuria','$frequency','$urgency','$incontinence','$renal_stones','$utis','$blood_in_urine','$urinary_retention','$change_in_nature_of_urine', '$genitourinarymale', '$genitourinarymale_text','$hesitancy','$dribbling','$stream','$nocturia','$erections','$ejaculations', '$genitourinaryfemale', '$genitourinaryfemale_text','$g','$p','$ap','$lc','$mearche','$menopause','$lmp','$f_frequency','$f_flow','$f_symptoms','$abnormal_hair_growth','$f_hirsutism', '$musculoskeletal', '$musculoskeletal_text','$joint_pain','$swelling','$m_redness','$m_warm','$m_stiffness','$m_aches','$fms','$arthritis','$gout','$back_pain','$paresthesia','$muscle_pain','$limitation_in_range_of_motion', '$extremities', '$extremities_text','$spasms','$extreme_tremors', '$neurologic', '$neurologic_text','$loc','$seizures','$stroke','$tia','$n_numbness','$n_weakness','$paralysis','$intellectual_decline','$memory_problems','$dementia','$n_headache','$dizziness_vertigo','$slurred_speech','$tremors','$migraines','$changes_in_mentation', '$skin', '$skin_text','$s_cancer','$psoriasis','$s_acne','$s_other','$s_disease','$rashes','$dryness','$itching', '$psychiatric', '$psychiatric_text','$p_diagnosis','$p_medication','$depression','$anxiety','$social_difficulties','$alcohol_drug_dependence','$suicide_thoughts','$use_of_antideprassants','$thought_content', '$endocrine', '$endocrine_text','$thyroid_problems','$diabetes','$abnormal_blood','$goiter','$heat_intolerence','$cold_intolerence','$increased_thirst','$excessive_sweating','$excessive_hunger', '$hai', '$hai_text','$anemia','$fh_blood_problems','$bleeding_problems','$allergies','$frequent_illness','$hiv','$hai_status','$hay_fever','$positive_ppd', '$neck', '$neck_text','$stiffness','$neck_pain','$masses','$tenderness', '$pending', '$finalized')";
            $sqlstmt = $db->prepare($sql) ;
            $data =  $sqlstmt->execute();
            
            $count = isset($count)? $count: 0;

            $array2[] = array( 'authuser' =>$user,'pending' => $pending,'finalized' => $finalized, 'date' => date("Y/m/d"), 'action'=>'created','ip_address'=>$ip_addr ,'count'=> $count+1);
            $logdata= serialize($array2) ;
            $sql2 = "INSERT into `tbl_allcare_formflag`(`form_id`, `encounter_id`, `form_name`, `pending`,`finalized`,`logdate`) VALUES(".$newformid.",".$encounter.",'Allcare Review of Systems','".$pending."','".$finalized."','".$logdata."')";
            $sqlstmt2 = $db->prepare($sql2) ;
            $data2 = $sqlstmt2->execute();
           
        }else{
            $sql = "UPDATE tbl_form_allcare_ros SET  `date` = NOW(), `constitutional` = '$constitutional', `constitutional_text` = '$constitutional_text',
                `weight_change` = '$weight_change', `weakness` = '$weakness', `fatigue` = '$fatigue', `anorexia` = '$anorexia', 
                `fever` = '$fever', `chills` = '$chills',`night_sweats` = '$night_sweats', `insomnia` = '$insomnia', 
                `irritability` = '$irritability', `irritability` = '$irritability',  `intolerance` = '$intolerance', 
                `change_in_appetite` = '$change_in_appetite', `eyes` = '$eyes', `eyes_text` = '$eyes_text', 
                `change_in_vision` = '$change_in_vision', `glaucoma_history` = '$glaucoma_history', `eye_pain` = '$eye_pain', 
                `irritation` = '$irritation', `redness` = '$redness', `excessive_tearing` = '$excessive_tearing',
                `double_vision` = '$double_vision', `blind_spots` = '$blind_spots', `photophobia` = '$photophobia', 
                `glaucoma` = '$glaucoma', `cataract` = '$cataract', `injury` = '$injury', `ha` = '$ha', `coryza` = '$coryza', 
                `obstruction` = '$obstruction', `ent` = '$ent', `ent_text` = '$ent_text',
                `hearing_loss` = '$hearing_loss', `discharge` = '$discharge', `pain` = '$pain', `vertigo` = '$vertigo', `tinnitus` = '$tinnitus', 
                `frequent_colds` = '$frequent_colds', `sore_throat` = '$sore_throat', `sinus_problems` = '$sinus_problems', 
                `post_nasal_drip` = '$post_nasal_drip', `nosebleed` = '$nosebleed', `snoring` = '$snoring', `apnea` = '$apnea', 
                `bleeding_gums` = '$bleeding_gums', `hoarseness` = '$hoarseness', `dental_difficulties` = '$dental_difficulties', 
                `use_of_dentures` = '$use_of_dentures', `breast` = '$breast', `breast_mass` = '$breast_mass', 
                `breast_discharge` = '$breast_discharge', `biopsy` = '$biopsy', `abnormal_mammogram` = '$abnormal_mammogram',
                `respiratory` = '$respiratory',  `respiratory_text` = '$respiratory_text', `cough` = '$cough', `sputum` = '$sputum', 
                `shortness_of_breath` = '$shortness_of_breath', `wheezing` = '$wheezing', `hemoptsyis` = '$hemoptsyis', 
                `asthma` = '$asthma', `copd` = '$copd', `cardiovascular` = '$cardiovascular', `chest_pain` = '$chest_pain',
                `palpitation` = '$palpitation', `syncope` = '$syncope', `pnd` = '$pnd', `doe` = '$doe',`orthopnea` = '$orthopnea',
                `peripheal` = '$peripheal', `edema` = '$edema', `legpain_cramping` = '$legpain_cramping', 
                `history_murmur` = '$history_murmur', `arrythmia` = '$arrythmia',`heart_problem` = '$heart_problem', 
                `gastrointestinal` = '$gastrointestinal', `gastrointestinal_text` = '$gastrointestinal_text',`dysphagia` = '$dysphagia',
                `heartburn` = '$heartburn', `bloating` = '$bloating', `belching` = '$belching', `flatulence` = '$flatulence',
                `nausea` = '$nausea', `vomiting` = '$vomiting',`hematemesis` = '$hematemesis', `gastro_pain` = '$gastro_pain',
                `food_intolerance` = '$food_intolerance',`hepatitis` = '$hepatitis', `jaundice` = '$jaundice', 
                `hematochezia` = '$hematochezia', `changed_bowel` = '$changed_bowel', `diarrhea` = '$diarrhea', `constipation` = '$constipation',
                `blood_in_stool` = '$blood_in_stool',`genitourinary` = '$genitourinary', `genitourinary_text` = '$genitourinary_text',
                `polyuria` = '$polyuria', `polydypsia` = '$polydypsia', `dysuria` = '$dysuria',`hematuria` = '$hematuria', 
                `frequency` = '$frequency', `urgency` = '$urgency', `incontinence` = '$incontinence', `renal_stones` = '$renal_stones', 
                `utis` = '$utis', `blood_in_urine` = '$blood_in_urine', `urinary_retention` = '$urinary_retention',
                `change_in_nature_of_urine` = '$change_in_nature_of_urine', `genitourinarymale` = '$genitourinarymale',
                `genitourinarymale_text` = '$genitourinarymale_text', `hesitancy` = '$hesitancy', `dribbling` = '$dribbling', 
                `stream` = '$stream', `nocturia` = '$nocturia', `erections` = '$erections', `ejaculations` = '$ejaculations', 
                `genitourinaryfemale` = '$genitourinaryfemale', `genitourinaryfemale_text` = '$genitourinaryfemale_text', `g` = '$g', 
                `p` = '$p', `ap` = '$ap', `lc` = '$lc', `mearche` = '$mearche', `menopause` = '$menopause', `lmp` = '$lmp',
                `f_frequency` = '$f_frequency', `f_flow` = '$f_flow', `f_symptoms` = '$f_symptoms', 
                `abnormal_hair_growth` = '$abnormal_hair_growth', `f_hirsutism` = '$f_hirsutism', `musculoskeletal` = '$musculoskeletal',
                `musculoskeletal_text` = '$musculoskeletal_text',`joint_pain` = '$joint_pain', `swelling` = '$swelling', `m_redness` = '$m_redness', 
                `m_warm` = '$m_warm', `m_stiffness` = '$m_stiffness', `m_aches` = '$m_aches',`fms` = '$fms', `arthritis` = '$arthritis', 
                `gout` = '$gout', `back_pain` = '$back_pain', `paresthesia` = '$paresthesia', `muscle_pain` = '$muscle_pain',
                `limitation_in_range_of_motion` = '$limitation_in_range_of_motion', `extremities` = '$extremities', 
                `extremities_text` = '$extremities_text', `spasms` = '$spasms',`extreme_tremors` = '$extreme_tremors', `neurologic` = '$neurologic',
                `neurologic_text` = '$neurologic_text', `loc` = '$loc', `seizures` = '$seizures',
                `stroke` = '$stroke', `tia` = '$tia', `n_numbness` = '$n_numbness', `n_weakness` = '$n_weakness', `paralysis` = '$paralysis',
                `intellectual_decline` = '$intellectual_decline', `memory_problems` = '$memory_problems', `dementia` = '$dementia',
                `n_headache` = '$n_headache', `dizziness_vertigo` = '$dizziness_vertigo', `slurred_speech` = '$slurred_speech', `tremors` = '$tremors',
                `migraines` = '$migraines', `changes_in_mentation` = '$changes_in_mentation', `skin` = '$skin', `skin_text` = '$skin_text',
                `s_cancer` = '$s_cancer',`psoriasis` = '$psoriasis',`s_acne` = '$s_acne', `s_other` = '$s_other', `s_disease` = '$s_disease', 
                `rashes` = '$rashes', `dryness` = '$dryness', `itching` = '$itching',`psychiatric` = '$psychiatric', 
                `psychiatric_text` = '$psychiatric_text', `p_diagnosis` = '$p_diagnosis', `p_medication` = '$p_medication', 
                `depression` = '$depression',`anxiety` = '$anxiety', `social_difficulties` = '$social_difficulties', 
                `alcohol_drug_dependence` = '$alcohol_drug_dependence', `suicide_thoughts` = '$suicide_thoughts', 
                `use_of_antideprassants` = '$use_of_antideprassants', `thought_content` = '$thought_content', `endocrine` = '$endocrine',
                `endocrine_text` = '$endocrine_text', `thyroid_problems` = '$thyroid_problems', `diabetes` = '$diabetes',
                `abnormal_blood` = '$abnormal_blood', `goiter` = '$goiter', `heat_intolerence` = '$heat_intolerence', 
                `cold_intolerence` = '$cold_intolerence', `increased_thirst` = '$increased_thirst', `excessive_sweating` = '$excessive_sweating',
                `excessive_hunger` = '$excessive_hunger',  `hai` = '$hai', `hai_text` = '$hai_text', `anemia` = '$anemia',
                `fh_blood_problems` = '$fh_blood_problems', `bleeding_problems` = '$bleeding_problems',
                `allergies` = '$allergies', `frequent_illness` = '$frequent_illness', `hiv` = '$hiv', `hai_status` = '$hai_status',
                `hay_fever` = '$hay_fever', `positive_ppd` = '$positive_ppd', `neck` = '$neck', `neck_text` = '$neck_text', 
                `stiffness` = '$stiffness', `neck_pain` = '$neck_pain', 
                `masses` = '$masses', `tenderness` = '$tenderness', `pending` = '$pending', `finalized` = '$finalized' WHERE id = $formid;";
            $sqlstmt = $db->prepare($sql) ;
            $data = $sqlstmt->execute();
            
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
                    $array2[] = array( 'authuser' =>$user,'pending' => $pending,'finalized' => $finalized, 'date' => date("Y/m/d"), 'action' => 'updated','ip_address'=>$ip_addr ,'count'=> $count+1);
                    $logdata = array_merge_recursive($array, $array2);
                    $logdata= ($logdata? serialize($logdata): serialize($array2) );
                    $sql4 = "UPDATE `tbl_allcare_formflag` SET `finalized`='$finalized',
                    `pending` = '$pending',`logdate` ='".$logdata."'  WHERE `form_name` = 'Allcare Review of Systems' AND `form_id` =  ".$formid;
                    $sqlstmt4 = $db->prepare($sql4) ;
                    $data4 = $sqlstmt4->execute();
            }else{ 
                    $array2[] = array( 'authuser' =>$user,'pending' => $pending,'finalized' => $finalized, 'date' => date("Y/m/d"), 'action'=>'created','ip_address'=>$ip_addr ,'count'=> $count+1);
                    $logdata = array_merge_recursive($array, $array2);
                    $logdata= ($logdata? serialize($logdata): serialize($array2) );
                    $sql4 = "INSERT into `tbl_allcare_formflag`(`form_id`, `encounter_id`, `form_name`, `pending`,`finalized`,`logdate`) VALUES(".$formid.",".$encounter.",'Allcare Review of Systems','".$pending."','".$finalized."','".$logdata."')";
                    $sqlstmt4 = $db->prepare($sql4) ;
                    $data4 = $sqlstmt4->execute();
            }
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
//function getPhysicalForm($formid){
//    $key = 'rotcoderaclla';
//    // The default key size is 256 bits.
//    $old_key_size = GibberishAES::size();
//    GibberishAES::size(256);
//    try 
//    {
//        $sql = "SELECT * FROM tbl_form_physical_exam WHERE forms_id=$formid";
//        $db = getConnection();
//        $stmt = $db->prepare($sql) ;
//        $stmt->execute();                       
//
//        $patientsreminders = $stmt->fetchAll(PDO::FETCH_OBJ);                        
//        
//        foreach($patientsreminders as $value)
//        {   
//            $wnl = $value->wnl;
//            $abn = $value->abn;
//            $comments = $value->comments;
//            $diagnosis = $value->diagnosis;
//            $sql2 = "SELECT ordering,diagnosis  FROM tbl_form_physical_exam_diagnoses WHERE line_id = '$value->line_id'";
//            $stmt2 = $db->prepare($sql2) ;
//            $stmt2->execute();                       
//
//            $diagnosis_listvalues = $stmt2->fetchAll(PDO::FETCH_OBJ); 
//            if(!empty($diagnosis_listvalues)):
//                foreach($diagnosis_listvalues as $list){
//                    $diagnosis_list = array($list->ordering => $list->diagnosis);
//                }
//            endif;
//            if($value->line_id == 'GENWELL'){
//                $name = str_replace($value->line_id ,'Appearance',$value->line_id );
//            }
//             
//            if($value->line_id  == 'GENAWAKE'){
//                $name = str_replace($value->line_id ,'Awake, Alert, Oriented, in No Acute Distress',$value->line_id );
//            }
//            
//            if($value->line_id == 'HEADNORM'){
//               $name = str_replace($value->line_id,'Normocephalic, Autramatic',$value->line_id);
//            }
//            
//            if($value->line_id == 'HEADLESI'){
//               $name = str_replace($value->line_id,'Lesions',$value->line_id);
//            }
//            
//            if($value->line_id == 'EYECP'){
//               $name = str_replace($value->line_id,'Conjuntiva, Pupils',$value->line_id);
//            }
//            
//            if($value->line_id == 'EYECON'){
//               $name = str_replace($value->line_id,'Conjuctive Clear, Tms Intact,Discharge, Wax, Oral Lesions, Gums pink, Bilateral Nasal Turbinates',$value->line_id);
//            }
//            
//            if($value->line_id == 'EYEPER'){
//               $name = str_replace($value->line_id,'PERRLA, EOMI',$value->line_id);
//            }
//             
//            if($value->line_id == 'ENTTM'){
//               $name = str_replace($value->line_id,'TMs/EAMs/EE, Ext Nose',$value->line_id);
//            }
//            
//            if($value->line_id == 'ENTNASAL'){
//               $name = str_replace($value->line_id,'Nasal Mucosa Pink, Septum Midline',$value->line_id);
//            }
//           
//            if($value->line_id == 'ENTORAL'){
//               $name = str_replace($value->line_id,'Oral Mucosa Pink, Throat Clear',$value->line_id);
//            }
//             
//            if($value->line_id == 'ENTSEPT'){
//               $name = str_replace($value->line_id,'Septum Midline',$value->line_id);
//            }
//            
//            if($value->line_id == 'NECKSUP'){
//               $name = str_replace($value->line_id,'Supple,Thyromegaly, Carotid of the Nasal Septum,  JVD,  lymphadenopathy',$value->line_id);
//            }
//             
//            if($value->line_id == 'BACKCUR'){
//               $name = str_replace($value->line_id,'Normal Curvature, Tenderness',$value->line_id);
//            }
//             
//            if($value->line_id == 'CVRRR'){
//               $name = str_replace($value->line_id,'RRR',$value->line_id);
//            }
//            
//            if($value->line_id == 'CVNTOH'){
//               $name = str_replace($value->line_id,'Thrills or Heaves',$value->line_id);
//            }
//             
//            if($value->line_id == 'CVCP'){
//               $name = str_replace($value->line_id,'Cartoid Pulsations, Pedal Pulses',$value->line_id);
//            }
//             
//            if($value->line_id == 'CVNPE'){
//               $name = str_replace($value->line_id,'Peripheral Edema',$value->line_id);
//            }
//           
//            if($value->line_id == 'CVNMU'){
//               $name = str_replace($value->line_id,'Murmur, Rubs,Gallops',$value->line_id);
//            }
//           
//            if($value->line_id == 'CHNSD'){
//               $name = str_replace($value->line_id,'Skin Dimpling or Breast Nodules',$value->line_id);
//            }
//            
//            if($value->line_id == 'RECTAB'){
//               $name = str_replace($value->line_id,'Lungs CTAB',$value->line_id);
//            }
//             
//            if($value->line_id == 'REEFF'){
//               $name = str_replace($value->line_id,'Respirator Effort Unlabored',$value->line_id);
//            }
//            
//            if($value->line_id == 'RELUN'){
//               $name = str_replace($value->line_id,'Lungs Clear,Rales,Rhonchi,Wheezes',$value->line_id);
//            }
//            
//            if($value->line_id == 'GIOG'){
//               $name = str_replace($value->line_id,'Ogrganomegoly',$value->line_id);
//            }
//             
//            if($value->line_id == 'GIHERN'){
//               $name = str_replace($value->line_id,'Anus, Rectal Tenderness/Mass',$value->line_id);
//            }
//            
//            if($value->line_id == 'GISOFT'){
//               $name = str_replace($value->line_id,'oft, Non Tender, Non Distended, Masses',$value->line_id);
//            }
//             
//            if($value->line_id == 'GIBOW'){
//               $name = str_replace($value->line_id,'Bowel Sounds present in all four quadrants',$value->line_id);
//            }
//             
//            if($value->line_id == 'GUTEST'){
//               $name = str_replace($value->line_id,'Testicular Tenderness, Masses',$value->line_id);
//            }
//             
//            if($value->line_id == 'GUPROS'){
//               $name = str_replace($value->line_id,'Prostate w/o Enlrgmt, Nodules, Tender',$value->line_id);
//            }
//             
//            if($value->line_id == 'GUEG'){
//               $name = str_replace($value->line_id,'Ext Genitalia, Vag Mucosa, Cervix',$value->line_id);
//            }
//            
//            if($value->line_id == 'GUAD'){
//               $name = str_replace($value->line_id,'Adnexal Tenderness/Masses',$value->line_id);
//            }
//            
//            if($value->line_id == 'GULES'){
//               $name = str_replace($value->line_id,'Normal. Lesions, Discharge, Hernias Noted, Deferred',$value->line_id);
//            }
//            
//            if($value->line_id == 'EXTREMIT'){
//               $name = str_replace($value->line_id,'Edema, Cyanosis or Clubbing',$value->line_id);
//            }
//           
//            if($value->line_id == 'EXTREDEF'){
//               $name = str_replace($value->line_id,'Deformities',$value->line_id);
//            }
//             
//            if($value->line_id == 'LYAD'){
//                $name = str_replace($value->line_id,'Adenopathy (2 areas required)',$value->line_id);
//            }
//             
//            if($value->line_id == 'MUSTR'){
//               $name = str_replace($value->line_id,'Strength',$value->line_id);
//            }
//            
//            if($value->line_id == 'MUROM'){
//               $name = str_replace($value->line_id,'ROM',$value->line_id);
//            }
//             
//            if($value->line_id == 'MUSTAB'){
//               $name = str_replace($value->line_id,'Stability',$value->line_id);
//            }
//            
//            if($value->line_id == 'MUINSP'){
//               $name = str_replace($value->line_id,'Inspection',$value->line_id);
//            }
//             
//            if($value->line_id == 'NEUCN2'){
//               $name = str_replace($value->line_id,'CN2-12 Intact',$value->line_id);
//            }
//            
//            if($value->line_id == 'NEUREF'){
//               $name = str_replace($value->line_id,'Reflexes Normal',$value->line_id);
//            }
//             
//            if($value->line_id == 'NEUSENS'){
//               $name = str_replace($value->line_id,'Sensory Exam Normal',$value->line_id);
//            }
//             
//            if($value->line_id == 'NEULOCAL'){
//               $name = str_replace($value->line_id,'Physiological, Localizing Findings',$value->line_id);
//            }
//            
//            if($value->line_id == 'PSYAFF'){
//               $name = str_replace($value->line_id,'Affect Normal',$value->line_id);
//            }
//          
//            if($value->line_id == 'PSYJUD'){
//               $name = str_replace($value->line_id,'Normal Affect, Judgement and Mood, Alert and Oriented X3',$value->line_id);
//            }
//            
//            if($value->line_id == 'PSYDEP'){
//               $name = str_replace($value->line_id,'Depressive Symptoms',$value->line_id);
//            }
//           
//            if($value->line_id == 'PSYSLE'){
//               $name = str_replace($value->line_id,'Change In Sleeping Habit',$value->line_id);
//            }
//            
//            if($value->line_id == 'PSYTHO'){
//               $name = str_replace($value->line_id,'Change In Thought Content',$value->line_id);
//            }
//             
//            if($value->line_id == 'PSYAPP'){
//               $name = str_replace($value->line_id,'Patient Appears To Be In Good Mood',$value->line_id);
//            }
//            
//            if($value->line_id == 'PSYABL'){
//               $name = str_replace($value->line_id,'Able To Answer Questions Qppropriately',$value->line_id);
//            }
//           
//            if($value->line_id == 'SKRASH'){
//               $name = str_replace($value->line_id,'Rash or Abnormal Lesions',$value->line_id);
//            }
//          
//            if($value->line_id == 'SKCLEAN'){
//               $name = str_replace($value->line_id,'Clean & Intact with Good Skin Turgor',$value->line_id);
//            }
//            
//            if($value->line_id == 'OTHER'){
//               $name = str_replace($value->line_id,'Other',$value->line_id);
//            }
//            $data['data']['label'] = $name;
//            $data['data']['wnl']['name'] = 'WNL';
//            $data['data']['wnl']['selectedValue'] = $wnl;
//            $data['data']['wnl']['controlType'] = 'checkbox';
//            $data['data']['abn']['name'] = 'ABN1';
//            $data['data']['abn']['selectedValue'] = $abn;
//            $data['data']['abn']['controlType'] = 'checkbox';
//            $data['data']['diagnosis']['name'] = 'Diagnosis';
//            $data['data']['diagnosis']['selectedValue'] = $diagnosis;
//            $data['data']['diagnosis']['controlType'] = 'listbox';
//            $data['data']['comments']['name'] = 'Comments';
//            $data['data']['comments']['selectedValue'] = $comments;
//            $data['data']['comments']['controlType'] = 'textbox';
//            if(empty($diagnosis_list)):
//                $data['data']['diagnosis']['diagnosis_list']= '';
//            else:
//                $data['data']['diagnosis']['diagnosis_list']= $diagnosis_list;   
//            endif;
//            $dataarray[] = $data; 
//        }
//        //echo "<pre>"; print_r($dataarray); echo "</pre>";
//        $newdemo1 = encode_demo($dataarray);  
//        $newdemo['FormData'] = check_data_available($newdemo1);
//        if($newdemo)
//        {
//            //returns patients 
//            $patientres = json_encode($newdemo); 
//            echo $patientresult = GibberishAES::enc($patientres, $key);
//        }
//        else
//        {
//           $patientres = '[{"id":"0"}]';
//           echo $patientresult = GibberishAES::enc($patientres, $key);
//        }
//    } 
//    catch(PDOException $e) 
//    {
//       $patientreserror =  '{"error":{"text":'. $e->getMessage() .'}}'; 
//       echo $patientresult = GibberishAES::enc($patientreserror, $key);
//    }
//}
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
        $nameslist = array('GENWELL', 'GENAWAKE','HEADNORM', 'HEADLESI', 'EYECP','EYECON','EYEPER','ENTTM','ENTNASAL','ENTORAL','ENTSEPT','NECKSUP','BACKCUR','CVRRR','CVNTOH','CVCP','CVNPE','CVNMU','CHNSD','RECTAB','REEFF','RELUN','GIOG','GIHERN','GISOFT','GIBOW','GUTEST','GUPROS','GUEG','GUAD','GULES','EXTREMIT','EXTREDEF','LYAD','MUSTR','MUROM','MUSTAB','MUINSP','NEUCN2','NEUREF','NEUSENS','NEULOCAL','PSYAFF','PSYJUD','PSYDEP','PSYSLE','PSYTHO','PSYAPP','PSYABL','SKRASH','SKCLEAN','OTHER');
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

                if($val == 'EXTREMIT'){
                   $name = str_replace($val,'Edema, Cyanosis or Clubbing',$val);
                   $system = 'EXTREMITIES';
                }

                if($val == 'EXTREDEF'){
                   $name = str_replace($val,'Deformities',$val);
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
        $final['data']['label'] = 'Finalized';
        $final['data']['selectedValue'] = $status[0]->finalized;
        $dataarray[] = $final;
        $pending['data']['label'] = 'Pending';
        $pending['data']['selectedValue'] =  $status[0]->pending;
        $dataarray[] = $pending;
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
function savePhysicalExamFormData($formid){
    try 
    {
        $db = getConnection();
        $request = Slim::getInstance()->request();
        $key = 'rotcoderaclla';
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        $appres = GibberishAES::dec($request->getBody(), $key);
        //$appres = GibberishAES::dec('U2FsdGVkX1+PDIp5vH5b8nW8sIhRYnajArp1nDRHkK5PMp+zwehlrXXg5pCvq4zEXAQXrf3SQpkzHWms6O/gEwdB2jWyhouONy8EWraJWor+IX5HMRi+SK4yaw48qhymEB0qnA3D1SbLvJMzuy71pk665CSLusHAAPX6yKpgm5mgZQOuy2cWs7DPNZ8kYDn8QBcQZRs1Qkf+n8julUBZK5BHGBrhksvZ8MPxe23cC1MFP8QfIr0t3WHcaxmEdsU9Q6ZLQFrQRkuAz7XMYGw4ePUdnbhMvaCYG9uXqpxkh1XpZJ0v8f49Ij3iYLcBbkX1nPS+30rnA344JdqpvXFEiCcuMAEqvL6nEsqiKyz++9kmw/Xjknt0k6FU0rnboEaWtmpplhH3VmRArYbtN2mFp15ONDKEitTKZWiRsX+SoUXCM0v38b5aJ0J0MBHV4gJmg+goHnagpGd5hDgra+lNh6fMa6bMsUo2pTsyG6gitF7/THin6YmRhzR+iszkrgyjfwa5HdY4ECLLD+KZVZf3VNw+iep97YQv6fTHaA7tb2c=', $key);
        $insertArray = json_decode($appres,TRUE);
        $nameslist = array('GENWELL', 'GENAWAKE','HEADNORM');//, 'HEADLESI', 'EYECP','EYECON','EYEPER','ENTTM','ENTNASAL','ENTORAL','ENTSEPT','NECKSUP','BACKCUR','CVRRR','CVNTOH','CVCP','CVNPE','CVNMU','CHNSD','RECTAB','REEFF','RELUN','GIOG','GIHERN','GISOFT','GIBOW','GUTEST','GUPROS','GUEG','GUAD','GULES','EXTREMIT','EXTREDEF','LYAD','MUSTR','MUROM','MUSTAB','MUINSP','NEUCN2','NEUREF','NEUSENS','NEULOCAL','PSYAFF','PSYJUD','PSYDEP','PSYSLE','PSYTHO','PSYAPP','PSYABL','SKRASH','SKCLEAN','OTHER');
        $formid                     = $insertArray['formid'];
        $encounter                  = $insertArray['encounter'];
        $pid                        = $insertArray['pid'];
        $user                       = $insertArray['loginProviderId'];
        $authorized                 = 1; 
        $activity                   = 1 ;
        
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
        $CHNSD  = $insertArray['CHNSD'];
        $RECTAB  = $insertArray['RECTAB'];
        $REEFF  = $insertArray['REEFF'];
        $RELUN  = $insertArray['RELUN'];
        $GIOG  = $insertArray['GIOG'];
        $GIHERN  = $insertArray['GIHERN'];
        $GISOFT  = $insertArray['GISOFT'];
        $GIBOW  = $insertArray['GIBOW'];
        $GUTEST  = $insertArray['GUTEST'];
        $GUPROS  = $insertArray['GUPROS'];
        $GUEG  = $insertArray['GUEG'];
        $GUAD  = $insertArray['GUAD'];
        $GULES  = $insertArray['GULES'];
        $EXTREMIT  = $insertArray['EXTREMIT'];
        $EXTREDEF  = $insertArray['EXTREDEF'];
        $LYAD  = $insertArray['LYAD'];
        $MUSTR  = $insertArray['MUSTR'];
        $MUROM  = $insertArray['MUROM'];
        $MUSTAB  = $insertArray['MUSTAB'];
        $MUINSP  = $insertArray['MUINSP'];
        $NEUCN2  = $insertArray['NEUCN2'];
        $NEUREF  = $insertArray['NEUREF'];
        $NEUSENS  = $insertArray['NEUSENS'];
        $NEULOCAL  = $insertArray['NEULOCAL'];
        $PSYAFF  = $insertArray['PSYAFF'];
        $PSYJUD  = $insertArray['PSYJUD'];
        $PSYDEP  = $insertArray['PSYDEP'];
        $PSYSLE  = $insertArray['PSYSLE'];
        $PSYTHO  = $insertArray['PSYTHO'];
        $PSYAPP  = $insertArray['PSYAPP'];
        $PSYABL  = $insertArray['PSYABL'];
        $SKRASH  = $insertArray['SKRASH'];
        $SKCLEAN  = $insertArray['SKCLEAN'];
        $OTHER  = $insertArray['OTHER'];
        $finalized  = $insertArray['finalized']; 
        $pending  =  $insertArray['pending']; 
        
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
                $valuedata  = $$val;
                $value      = $valuedata[0];
                $wnl        = $valuedata[1];
                $abn        = $valuedata[2];
                $diagnosis  = $valuedata[3];
                $comments   = $valuedata[4];
                
                $sql = "INSERT INTO tbl_form_physical_exam (forms_id, line_id, wnl, abn, diagnosis, comments) VALUES($newformid,:line_id, :wnl, :abn, :diagnosis, :comments )"; echo"<br>";
                $sqlstmt = $db->prepare($sql) ;
                $stmt->bindParam("line_id", $value);    
                $stmt->bindParam("wnl", $wnl);    
                $stmt->bindParam("abn", $abn);    
                $stmt->bindParam("diagnosis", $diagnosis);   
                $stmt->bindParam("comments", $comments); 
                $data =  $sqlstmt->execute();
            }
            
            $count = isset($count)? $count: 0;

            $array2[] = array( 'authuser' =>$user,'pending' => $pending,'finalized' => $finalized, 'date' => date("Y/m/d"), 'action'=>'created','ip_address'=>$ip_addr ,'count'=> $count+1);
            $logdata= serialize($array2) ;
            $sql2 = "INSERT into `tbl_allcare_formflag`(`form_id`, `encounter_id`, `form_name`, `pending`,`finalized`,`logdate`) VALUES(".$newformid.",".$encounter.",'Allcare Physical Exam','".$pending."','".$finalized."','".$logdata."')";
            $sqlstmt2 = $db->prepare($sql2) ;
            $data2 = $sqlstmt2->execute();
           
        }else{
            foreach($nameslist as $val){
                $valuedata  = $$val;
                $value      = $valuedata[0];
                $wnl        = $valuedata[1];
                $abn        = $valuedata[2];
                $diagnosis  = $valuedata[3];
                $comments   = $valuedata[4];
                
                $sql = "UPDATE tbl_form_physical_exam SET wnl = :wnl , abn = :abn , diagnosis = :diagnosis , comments = :comments
                        WHERE line_id = :line_id AND forms_id = $formid";
                $sqlstmt = $db->prepare($sql) ;
                $stmt->bindParam("line_id", $value);    
                $stmt->bindParam("wnl", $wnl);    
                $stmt->bindParam("abn", $abn);    
                $stmt->bindParam("diagnosis", $diagnosis);   
                $stmt->bindParam("comments", $comments); 
                $data = $sqlstmt->execute();
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

            $result2 ="SELECT * FROM tbl_allcare_formflag WHERE `form_name` = 'Allcare Physical Exam' AND `form_id` =  ".$formid;
            $sqlstmt3 = $db->prepare($result2) ;
            $data3 = $sqlstmt3->execute();
            $maxformidval = $sqlstmt3->fetchAll(PDO::FETCH_OBJ);
            if(count($maxformidval) > 0){
                    $array2[] = array( 'authuser' =>$user,'pending' => $pending,'finalized' => $finalized, 'date' => date("Y/m/d"), 'action' => 'updated','ip_address'=>$ip_addr ,'count'=> $count+1);
                    $logdata = array_merge_recursive($array, $array2);
                    $logdata= ($logdata? serialize($logdata): serialize($array2) );
                    $sql4 = "UPDATE `tbl_allcare_formflag` SET `finalized`='$finalized',
                    `pending` = '$pending',`logdate` ='".$logdata."'  WHERE `form_name` = 'Allcare Physical Exam' AND `form_id` =  ".$formid;
                    $sqlstmt4 = $db->prepare($sql4) ;
                    $data4 = $sqlstmt4->execute();
            }else{ 
                    $array2[] = array( 'authuser' =>$user,'pending' => $pending,'finalized' => $finalized, 'date' => date("Y/m/d"), 'action'=>'created','ip_address'=>$ip_addr ,'count'=> $count+1);
                    $logdata = array_merge_recursive($array, $array2);
                    $logdata= ($logdata? serialize($logdata): serialize($array2) );
                    $sql4 = "INSERT into `tbl_allcare_formflag`(`form_id`, `encounter_id`, `form_name`, `pending`,`finalized`,`logdate`) VALUES(".$formid.",".$encounter.",'Allcare Physical Exam','".$pending."','".$finalized."','".$logdata."')";
                    $sqlstmt4 = $db->prepare($sql4) ;
                    $data4 = $sqlstmt4->execute();
            }
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
function getFacetoFace($encounterid,$formid){
    try 
    {
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
                    END as data_type, uor, list_id, description from layout_options WHERE group_name like '%Face to Face HH Plan' ";      
        $db = getConnection();
        $stmt = $db->prepare($sql) ;
        $stmt->execute();                       
        $getGroupData = $stmt->fetchAll(PDO::FETCH_OBJ);    
        for($i=0; $i< count($getGroupData); $i++){
            $data = array();
            foreach($getGroupData[$i] as $key => $value){
                $data[$key] = $value;
                if($key == 'field_id'):
                    $sql2 = "SELECT field_value FROM lbf_data WHERE field_id = '$value' AND form_id = $formid";
                    $stmt2 = $db->prepare($sql2) ;
                    $stmt2->execute();                       
                    $getFieldData = $stmt2->fetchAll(PDO::FETCH_OBJ);   
                    if(!empty($getFieldData)):
                        $data['selected_value'] = $getFieldData[0]->field_value;
                    else:
                        $data['selected_value'] = '';
                    endif;
                elseif($key == 'list_id'):
                    if($value != ''):
                        $sql3 = "SELECT option_id, title, seq FROM list_options WHERE list_id = '$value' ";
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
                    else:
                        $data['options_list'] = '';
                    endif;
                endif;
                if($key == 'field_id' && $value == 'f2f_findings' ):
                    $sql4 = "SELECT l.diagnosis FROM lists l INNER JOIN issue_encounter i ON l.pid = i.pid WHERE i.encounter = $encounterid";
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
            $result = utf8_encode_recursive($newdemo2);
            echo $patientres =  json_encode($result); 
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
            $new=array();
            $query = "SELECT code AS code, code_text AS Description
                        FROM codes c
                        INNER JOIN code_types ct ON ct.ct_id = c.code_type
                        WHERE ct.ct_key =  'CPT4'";
            $stmt = $db->prepare($query) ; 
            $stmt->execute(); 
            $cpt4codes = $stmt->fetchAll(PDO::FETCH_OBJ); 
            foreach($cpt4codes as $element):
                $query1 = "select dx_code, short_desc from icd9_dx_code limit 150";
                $stmt1 = $db->prepare($query1) ; 
                $stmt1->execute(); 
                $icd9codes = $stmt1->fetchAll(PDO::FETCH_OBJ); 
            endforeach;
            
            foreach($icd9codes as $element):
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
                    WHERE ie.encounter = $encounterid
                    AND li.type ='medical_problem'" ; 
                $stmt2 = $db->prepare($sql) ;
                $stmt2->bindParam("encounterid",$encounterid);
                $stmt2->execute(); 
              
                
                $medical_problems = $stmt2->fetchAll(PDO::FETCH_OBJ);     
            endforeach;
            foreach($medical_problems as $element):
                $sql1 = "SELECT cg.code_groups AS code_group, fs.fs_option AS code_option,fs.fs_codes AS code
                        FROM form_encounter fe
                        INNER JOIN openemr_postcalendar_categories pc ON pc.pc_catid = fe.pc_catid
                        INNER JOIN tbl_allcare_vistcat_codegrp cg ON cg.facility = fe.facility_id
                        AND cg.visit_category = fe.pc_catid
                        INNER JOIN fee_sheet_options fs ON fs.fs_category = cg.code_groups
                                            AND cg.code_options REGEXP (
                                            fs.fs_option
                                            )
                        WHERE fe.encounter =$encounterid" ; 
                $stmt3 = $db->prepare($sql1) ;
                $stmt3->bindParam("encounterid",$encounterid);
                $stmt3->execute(); 
               
               
                $code_groups = $stmt3->fetchAll(PDO::FETCH_OBJ);     
            endforeach;
//            $selectquery = "SELECT b.code_type, b.code, b.modifier, b.fee, b.units, b.justify, b.provider_id, b.notecodes, b.authorized, b.code_text, f.provider_id, f.supervisor_id,SUBSTRING(b.ndc_info, 2, 15) as ndc_info, SUBSTRING(b.ndc_info, -3, 2) as ndc_info_unit, SUBSTRING(b.ndc_info, -1) as ndc_info_amount
//                        FROM billing b INNER JOIN form_encounter f ON b.encounter = f.encounter  WHERE b.encounter = $encounterid";
            $selectquery = "SELECT b.code_type, b.code, b.modifier, b.fee, b.units, b.justify, b.provider_id,  b.notecodes, b.authorized, b.code_text, f.provider_id as rendering_providerid,CONCAT(up.fname,',', up.lname) as rendering_ProviderName, f.supervisor_id,SUBSTRING(b.ndc_info, 2, 15) as ndc_info, SUBSTRING(b.ndc_info, -3, 2) as ndc_info_unit, SUBSTRING(b.ndc_info, -1) as ndc_info_amount
                FROM billing b 
                INNER JOIN form_encounter f ON b.encounter = f.encounter  
                INNER JOIN users up ON f.provider_id= up.id
                WHERE b.encounter = $encounterid";
            $selectstmt = $db->prepare($selectquery) ; 
            $selectstmt->execute(); 
            $selected_feesheet = $selectstmt->fetchAll(PDO::FETCH_OBJ); 
            
            if(!empty($selected_feesheet)){
                for($i=0; $i< count($selected_feesheet); $i++){
                    foreach($selected_feesheet[$i] as $key => $val){
                        if($key == 'supervisor_id'){
                            if($val != 'N/A'):
                                $getsupervisor = "SELECT  CONCAT(fname,',',lname) as name FROM users WHERE id = $val";
                                $selectsupervisor = $db->prepare($getsupervisor) ; 
                                $selectsupervisor->execute(); 
                                $supervisorname  = $selectsupervisor->fetchAll(PDO::FETCH_OBJ); 
                                $selected_feesheet[$i]->supervisor_name = $supervisorname[0]->name;
                            else:
                                $selected_feesheet[$i]->supervisor_name = '';
                            endif;
                        }
                        if($key == 'provider_id'){
                            if($val != 0):
                                $getprovider = "SELECT  CONCAT(fname,',',lname) as name FROM users WHERE id = $val";
                                $selectprovider = $db->prepare($getprovider) ; 
                                $selectprovider->execute(); 
                                $provider_name  = $selectprovider->fetchAll(PDO::FETCH_OBJ); 
                                if(!empty($provider_name)):
                                    $name = $provider_name[0]->name;
                                else:
                                    $name = '';
                                endif;
                                $selected_feesheet[$i]->provider_name = $name;
                            else:
                                $selected_feesheet[$i]->provider_name = '';
                            endif;
                        }
                    }
                }
            }
//            $provider = "SELECT id, CONCAT(lname,',',fname) as providername FROM users WHERE authorized = 1 AND see_auth = 1";
//            $selectprovider = $db->prepare($provider) ; 
//            $selectprovider->execute(); 
//            $providerlist = $selectprovider->fetchAll(PDO::FETCH_OBJ); 
//            $selected_feesheet['provider_list'] = $providerlist;
                
            $new['FeeSheetData'] = $selected_feesheet;
            //$new['provider_list'] = $providerlist;
            $new['CPT4']=$cpt4codes;
            $new['ICD9']=$icd9codes;
            $new['medical_problems']= $medical_problems ;
            $new['code_groups']= $code_groups ;
            
            //echo "<pre>"; print_r($new); echo "</pre>";
           
            $newdemo4=encode_demo($new);  
            $newdemo['FEE_SHEET'] = check_data_available($newdemo4);
              
            if($newdemo4)
            {
                //returns count 
                echo $newdemores = json_encode($newdemo);
                //echo $newdemoresult = GibberishAES::enc($newdemores, $key);
            }
            else
            {
               $feesheetcodes = '[{"id":"0"}]';
               
            }
            
            
        } 
        catch(PDOException $e) 
        {
            
            $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
           
        }
   
}
function getFeesheetCodes2($encounterid)
{
    try 
        {
            $db = getConnection();
            $new=array();
            $query = "SELECT code AS code, code_text AS Description
                        FROM codes c
                        INNER JOIN code_types ct ON ct.ct_id = c.code_type
                        WHERE ct.ct_key =  'CPT4'";
            $stmt = $db->prepare($query) ; 
            $stmt->execute(); 
            $cpt4codes = $stmt->fetchAll(PDO::FETCH_OBJ); 
            foreach($cpt4codes as $element):
                $query1 = "select dx_code, short_desc from icd9_dx_code limit 150";
                $stmt1 = $db->prepare($query1) ; 
                $stmt1->execute(); 
                $icd9codes = $stmt1->fetchAll(PDO::FETCH_OBJ); 
            endforeach;
            
            foreach($icd9codes as $element):
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
                    WHERE ie.encounter = $encounterid
                    AND li.type ='medical_problem'" ; 
                $stmt2 = $db->prepare($sql) ;
                $stmt2->bindParam("encounterid",$encounterid);
                $stmt2->execute(); 
              
                
                $medical_problems = $stmt2->fetchAll(PDO::FETCH_OBJ);     
            endforeach;
            foreach($medical_problems as $element):
                $sql1 = "SELECT cg.code_groups AS code_group, fs.fs_option AS code_option,fs.fs_codes AS code
                        FROM form_encounter fe
                        INNER JOIN openemr_postcalendar_categories pc ON pc.pc_catid = fe.pc_catid
                        INNER JOIN tbl_allcare_vistcat_codegrp cg ON cg.facility = fe.facility_id
                        AND cg.visit_category = fe.pc_catid
                        INNER JOIN fee_sheet_options fs ON fs.fs_category = cg.code_groups
                                            AND cg.code_options REGEXP (
                                            fs.fs_option
                                            )
                        WHERE fe.encounter =$encounterid" ; 
                $stmt3 = $db->prepare($sql1) ;
                $stmt3->bindParam("encounterid",$encounterid);
                $stmt3->execute(); 
               
               
                $code_groups = $stmt3->fetchAll(PDO::FETCH_OBJ);     
            endforeach;
            $selectquery = "SELECT b.code_type, b.code, b.modifier, b.fee, b.units, b.justify, b.provider_id, b.notecodes, b.authorized, b.code_text, f.provider_id, f.supervisor_id,SUBSTRING(b.ndc_info, 2, 15) as ndc_info, SUBSTRING(b.ndc_info, -3, 2) as ndc_info_unit, SUBSTRING(b.ndc_info, -1) as ndc_info_amount
                        FROM billing b INNER JOIN form_encounter f ON b.encounter = f.encounter  WHERE b.encounter = $encounterid";
            $selectstmt = $db->prepare($selectquery) ; 
            $selectstmt->execute(); 
            $selected_feesheet = $selectstmt->fetchAll(PDO::FETCH_OBJ); 
            
            $provider = "SELECT id, CONCAT(lname,',',fname) as providername FROM users WHERE authorized = 1 AND see_auth = 1";
            $selectprovider = $db->prepare($provider) ; 
            $selectprovider->execute(); 
            $providerlist = $selectprovider->fetchAll(PDO::FETCH_OBJ); 
            $selected_feesheet['provider_list'] = $providerlist;
                
            $new['Fee Sheet Data'] = $selected_feesheet;
            $new['provider_list'] = $providerlist;
            $new['CPT4']=$cpt4codes;
            $new['ICD9']=$icd9codes;
            $new['medical_problems']= $medical_problems ;
            $new['code_groups']= $code_groups ;
            
            //echo "<pre>"; print_r($new); echo "</pre>";
           
            $newdemo4=encode_demo($new);  
            $newdemo['FEESHEETCODE'] = check_data_available($newdemo4);
              
            if($newdemo4)
            {
                //returns count 
                echo $newdemores = json_encode($newdemo);
                //echo $newdemoresult = GibberishAES::enc($newdemores, $key);
            }
            else
            {
               $feesheetcodes = '[{"id":"0"}]';
               
            }
            
            
        } 
        catch(PDOException $e) 
        {
            
            $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
           
        }
   
}
function getFeesheetCodes_old($encounterid)
{
    try 
        {
            $db = getConnection();
            $new=array();
            $query = "SELECT code AS code, code_text AS Description
                        FROM codes c
                        INNER JOIN code_types ct ON ct.ct_id = c.code_type
                        WHERE ct.ct_key =  'CPT4'";
            $stmt = $db->prepare($query) ; 
            $stmt->execute(); 
            $cpt4codes = $stmt->fetchAll(PDO::FETCH_OBJ); 
            foreach($cpt4codes as $element):
                $query1 = "select dx_code, short_desc from icd9_dx_code limit 150";
            $stmt1 = $db->prepare($query1) ; 
            $stmt1->execute(); 
            $icd9codes = $stmt1->fetchAll(PDO::FETCH_OBJ); 
            endforeach;
            
            foreach($icd9codes as $element):
                $sql = "SELECT 
                    CASE 
                    WHEN li.enddate IS NULL 
                    THEN  'Active'
                    ELSE  'Inactive'
                    END AS isIssueActive ,li.id, li.title AS Title , li.diagnosis AS Codes, i.long_desc AS Description 
                    FROM  `icd9_dx_code` i
                    RIGHT JOIN lists li ON li.diagnosis = CONCAT(  'ICD9:', i.formatted_dx_code ) 
                    INNER JOIN issue_encounter ie ON ie.list_id = li.id
                    INNER JOIN form_encounter fe ON fe.encounter = ie.encounter
                    WHERE ie.encounter = $encounterid
                    AND li.type ='medical_problem'" ; 
                $stmt2 = $db->prepare($sql) ;
                $stmt2->bindParam("encounterid",$encounterid);
                $stmt2->execute(); 
              
                
                $medical_problems = $stmt2->fetchAll(PDO::FETCH_OBJ);     
            endforeach;
            foreach($medical_problems as $element):
                $sql1 = "SELECT cg.code_groups AS code_group, fs.fs_option AS code_option,fs.fs_codes AS code
                        FROM form_encounter fe
                        INNER JOIN openemr_postcalendar_categories pc ON pc.pc_catid = fe.pc_catid
                        INNER JOIN tbl_allcare_vistcat_codegrp cg ON cg.facility = fe.facility_id
                        AND cg.visit_category = fe.pc_catid
                        INNER JOIN fee_sheet_options fs ON fs.fs_category = cg.code_groups
                                            AND cg.code_options REGEXP (
                                            fs.fs_option
                                            )
                        WHERE fe.encounter =$encounterid" ; 
                $stmt3 = $db->prepare($sql1) ;
                $stmt3->bindParam("encounterid",$encounterid);
                $stmt3->execute(); 
               
               
                $code_groups = $stmt3->fetchAll(PDO::FETCH_OBJ);     
            endforeach;
            $new['CPT4']=$cpt4codes;
            $new['ICD9']=$icd9codes;
            $new['medical_problems']= $medical_problems ;
            $new['code_groups']= $code_groups ;
            
            //echo "<pre>"; print_r($new); echo "</pre>";
           
            $newdemo4=encode_demo($new);  
            $newdemo['FEE SHEET'] = check_data_available($newdemo4);
              
            if($newdemo4)
            {
                //returns count 
                echo $newdemores = json_encode($newdemo);
                //echo $newdemoresult = GibberishAES::enc($newdemores, $key);
            }
            else
            {
               $feesheetcodes = '[{"id":"0"}]';
               
            }
            
            
        } 
        catch(PDOException $e) 
        {
            
            $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
           
        }
   
}
function getIssuesByEncounter($encounter,$type)
{
                    
    try 
        {
            $db = getConnection();
            $key1 = 'rotcoderaclla';
            // The default key size is 256 bits.
            $old_key_size = GibberishAES::size();
            GibberishAES::size(256);
            $new=array();
            $issues2 = array();
            if($type=='allergy')
            {
                $query = "SELECT 
                    CASE 
                    WHEN li.enddate IS NULL 
                    THEN  'Active'
                    ELSE  'Inactive'
                    END AS isIssueActive, li.id,li.title AS Title, li.begdate AS Begdate, li.enddate AS Enddate, li.diagnosis AS Codes, i.long_desc AS Description, 
                    li.reaction as reaction,li.occurrence AS Occurrence, li.referredby AS ReferredBy
                    FROM  `icd9_dx_code` i
                    RIGHT JOIN lists li ON li.diagnosis = CONCAT(  'ICD9:', i.formatted_dx_code ) 
                    INNER JOIN issue_encounter ie ON ie.list_id = li.id
                    INNER JOIN form_encounter fe ON fe.encounter = ie.encounter
                    WHERE ie.encounter =$encounter
                    AND li.type ='$type'";
                $stmt = $db->prepare($query) ; 
                $stmt->execute(); 
                $issues2 = $stmt->fetchAll(PDO::FETCH_OBJ); 
                for($i=0; $i<count($issues2); $i++){
                    $issues = array();
                    foreach($issues2[$i] as $key => $issues3){
                        if($key == 'Occurrence'):
                            $issues['Occurrence']['selectedvalue'] = $issues3;
                        else:
                            $issues[$key] = $issues3;
                        endif;
                    }
                }
            }
            else
            {
                $query = "SELECT 
                    CASE 
                    WHEN li.enddate IS NULL 
                    THEN  'Active'
                    ELSE  'Inactive'
                    END AS isIssueActive, li.id,li.title AS Title, li.begdate AS Begdate, li.enddate AS Enddate, li.diagnosis AS Codes, i.long_desc AS Description, 
                    li.occurrence AS Occurrence, li.referredby AS ReferredBy
                    FROM  `icd9_dx_code` i
                    RIGHT JOIN lists li ON li.diagnosis = CONCAT(  'ICD9:', i.formatted_dx_code ) 
                    INNER JOIN issue_encounter ie ON ie.list_id = li.id
                    INNER JOIN form_encounter fe ON fe.encounter = ie.encounter
                    WHERE ie.encounter =$encounter
                    AND li.type ='$type'";
                $stmt = $db->prepare($query) ; 
                $stmt->execute(); 
                $issues2 = $stmt->fetchAll(PDO::FETCH_OBJ); 
            }
            for($i=0; $i<count($issues2); $i++){
                foreach($issues2[$i] as $key => $issues3){
                    if($key == 'Occurrence'):
                        $issues['Occurrence']['selectedvalue'] = $issues3;
                    else:
                        $issues[$key] = $issues3;
                    endif;
                }
                $new[] = $issues;
            }
            $query1 = "select option_id,title from list_options where list_id='occurrence'";
            $stmt1 = $db->prepare($query1) ; 
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
          // $new['allergy_list']=$typelists;  
            $new['issues']=$issues;
            //echo "<pre>"; print_r($new); echo "</pre>";
           
            $newdemo4=encode_demo($new);  
            $newdemo['Issues'] = check_data_available($newdemo4);
              
            if($newdemo)
            {
                //returns count 
                echo $issues1 = json_encode($newdemo);
                //echo $newdemoresult = GibberishAES::enc($issues1, $key1);
            }
            else
            {
               echo $issues1= '[{"id":"0"}]';
               //echo $newdemoresult = GibberishAES::enc($issues1, $key1);
               
            }
            
            
        } 
        catch(PDOException $e) 
        {
            
            echo $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
            //echo $newdemoresult = GibberishAES::enc($error, $key1);
           
        }
}
function getIssuesByEncounter2($encounter,$type)
{
                    
    try 
        {
            $db = getConnection();
            $new=array();
            if($type=='allergy')
            {
            $query = "SELECT 
                CASE 
                WHEN li.enddate IS NULL 
                THEN  'Active'
                ELSE  'Inactive'
                END AS isIssueActive, li.id,li.title AS Title, li.begdate AS Begdate, li.enddate AS Enddate, li.diagnosis AS Codes, i.long_desc AS Description, 
                li.reaction as reaction,li.occurrence AS Occurrence, li.referredby AS ReferredBy
                FROM  `icd9_dx_code` i
                RIGHT JOIN lists li ON li.diagnosis = CONCAT(  'ICD9:', i.formatted_dx_code ) 
                INNER JOIN issue_encounter ie ON ie.list_id = li.id
                INNER JOIN form_encounter fe ON fe.encounter = ie.encounter
                WHERE ie.encounter =:encounter
                AND li.type =:type";
            $stmt = $db->prepare($query) ; 
            $stmt->bindParam("encounter",$encounter);  
            $stmt->bindParam("type",$type);  
            $stmt->execute(); 
            $issues = $stmt->fetchAll(PDO::FETCH_OBJ); 
            }
            else
            {
                 $query = "SELECT 
                CASE 
                WHEN li.enddate IS NULL 
                THEN  'Active'
                ELSE  'Inactive'
                END AS isIssueActive, li.id,li.title AS Title, li.begdate AS Begdate, li.enddate AS Enddate, li.diagnosis AS Codes, i.long_desc AS Description, 
                li.occurrence AS Occurrence, li.referredby AS ReferredBy
                FROM  `icd9_dx_code` i
                RIGHT JOIN lists li ON li.diagnosis = CONCAT(  'ICD9:', i.formatted_dx_code ) 
                INNER JOIN issue_encounter ie ON ie.list_id = li.id
                INNER JOIN form_encounter fe ON fe.encounter = ie.encounter
                WHERE ie.encounter =:encounter
                AND li.type =:type";
            $stmt = $db->prepare($query) ; 
            $stmt->bindParam("encounter",$encounter);  
            $stmt->bindParam("type",$type);  
            $stmt->execute(); 
            $issues = $stmt->fetchAll(PDO::FETCH_OBJ); 
            }
           
                        //foreach($issues as $element):
                $query1 = "select option_id,title from list_options where list_id='occurrence'";
            $stmt1 = $db->prepare($query1) ; 
            $stmt1->execute(); 
            $occlist = $stmt1->fetchAll(PDO::FETCH_OBJ); 
            $list=array();
             
            foreach($occlist as $list2){
                   $lists[$list2->option_id]  = $list2->title;
                    for($i=0; $i<count($issues); $i++)   
                    {
                         $issues[$i]->list=$lists;
                    }
             }
           $new['issues']=$issues;
           

            //echo "<pre>"; print_r($new); echo "</pre>";
           
            $newdemo4=encode_demo($new);  
            $newdemo['ISSUES'] = check_data_available($newdemo4);
              
            if($newdemo4)
            {
                //returns count 
                echo $newdemores = json_encode($newdemo);
                //echo $newdemoresult = GibberishAES::enc($newdemores, $key);
            }
            else
            {
               $issues1= '[{"id":"0"}]';
               
            }
            
            
        } 
        catch(PDOException $e) 
        {
            
            $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
           
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
            $new=array();
            $immunization=array();
            $query = "select i.id as id, i.administered_date, i.immunization_id as immunization_id, i.cvx_code as cvx_code, c.code_text_short as cvx_text, i.manufacturer,i.lot_number,i.administered_by_id,i.administered_by,
             i.education_date,i.vis_date,i.note,i.amount_administered,i.amount_administered_unit,i.expiration_date,i.route,i.administration_site,i.added_erroneously".
             " from immunizations i ".
             " left join code_types ct on ct.ct_key = 'CVX' ".
             " left join codes c on c.code_type = ct.ct_id AND i.cvx_code = c.code ".
             " where i.patient_id = $patientId ".
             " and i.added_erroneously = 0".
             " order by i.administered_date desc";
            $stmt = $db->prepare($query) ; 
            $stmt->execute(); 
            $immunization= $stmt->fetchAll(PDO::FETCH_OBJ); 
            for($i=0; $i<count($immunization); $i++){
                foreach($immunization[$i] as $key => $units3){
                    if ($key == 'administered_by_id') {
                        $units['administered_by_id']['selectedvalue'] = $units3;
                     } 
                     else if($key == 'amount_administered_unit')  
                            $units['amount_administered_unit']['selectedvalue'] = $units3;
                     else if($key == 'route')  
                            $units['route']['selectedvalue'] = $units3;
                     else if($key=='administration_site')
                        $units['administration_site']['selectedvalue'] = $units3;
                     else
                          $units[$key] = $units3;
                    
                }
                $new[] = $units;
            }
              
            $query1 = "select id, concat(lname,', ',fname) as full_name from users ";		
            $stmt1 = $db->prepare($query1) ; 
            $stmt1->execute(); 
            $userlist = $stmt1->fetchAll(PDO::FETCH_OBJ); 
            $lists4=array();
              
            foreach($userlist as $list1){
                $lists4[$list1->id]  = $list1->full_name;
                for($i=0; $i<count($immunization); $i++)   
                {
                  $new[$i]['administered_by_id']['users_list']=$lists4;
                }
            }
            $query2 = "select option_id,title from list_options where list_id='drug_units'";
            $stmt1 = $db->prepare($query2) ; 
            $stmt1->execute(); 
            $druglist = $stmt1->fetchAll(PDO::FETCH_OBJ); 
            $lists=array();
              
            foreach($druglist as $list2){
                $lists[$list2->option_id]  = $list2->title;
                for($i=0; $i<count($immunization); $i++)   
                {
                  $new[$i]['amount_administered_unit']['drug_units_list']=$lists;
                }
            }
            $query3= "select option_id,title from list_options where list_id='drug_route'";
            $stmt2= $db->prepare($query3) ; 
            $stmt2->execute(); 
            $routelist = $stmt2->fetchAll(PDO::FETCH_OBJ); 
            $lists1=array();
              
            foreach($routelist as $list3){
                $lists1[$list3->option_id]  = $list3->title;
                for($i=0; $i<count($immunization); $i++)   
                {
                  $new[$i]['route']['drug_route_list']=$lists1;
                }
            } 
            $query4 = "select option_id,title from list_options where list_id='proc_body_site'";
            $stmt3= $db->prepare($query4) ; 
            $stmt3->execute(); 
            $sitelist = $stmt3->fetchAll(PDO::FETCH_OBJ); 
            $lists2=array();
              
            foreach($sitelist as $list4){
                $lists2[$list4->option_id]  = $list4->title;
                for($i=0; $i<count($immunization); $i++)   
                {
                  $new[$i]['administration_site']['site_list']=$lists2;
                }
            }  
             //$new['immun']= $immunization;  
            
            echo "<pre>"; print_r($new); echo "</pre>";
           
            $newdemo4=encode_demo($new);  
            $newdemo['IMMUNIZATION'] = check_data_available($newdemo4);
              
            if($newdemo4)
            {
                //returns count 
                echo $newdemores = json_encode($newdemo);
                //echo $newdemoresult = GibberishAES::enc($newdemores, $key);
            }
            else
            {
               $immunization = '[{"id":"0"}]';
               
            }
            
            
        } 
        catch(PDOException $e) 
        {
            
            echo $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
           
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
        $key = 'rotcoderaclla';
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        $appres = GibberishAES::dec($request->getBody(), $key);
        $insertArray = json_decode($appres,TRUE);
       
        $billed                     = 0;
        $payer_id                   = 'NULL';
        $bill_process               = 0;
        $bill_date                  = 'NULL';
        $process_date               = 'NULL';
        $process_file               = 'NULL';

        $id                         = $insertArray['id'];
        $encounter                  = $insertArray['encounter'];
        $pid                        = $insertArray['pid'];
        $user                       = $insertArray['user'];
        $authorized                 = 1; 
        $activity                   = 1 ;
        
        $code_type                  = $insertArray['code_type'];
        $code                       = $insertArray['code'];
        $provider_id                = $insertArray['provider_id'];
        $groupname                  = 'default';
        $code_text                  = $insertArray['code_text'];
        $modifier                   = $insertArray['modifier'];
        $units                      = $insertArray['units'];
        $fee                        = $insertArray['fee'];
        $justify                    = $insertArray['justify'];
        $target                     = '';
        $x12_partner_id             = $insertArray['x12_partner_id'];
        $ndc_info                   = $insertArray['ndc_info'];
        $notecodes                  = $insertArray['notecodes'];
        $supervisor_id              = $insertArray['supervisor_id'];
        $issue_list                 = $insertArray['issue_list'];

        if($diags!='')   
        {    
            foreach($diags as $diag)
            {
                $justify.="ICD9|".$diag.":";
            }
        }
       // $title='Central pain syndrome';  
        
        $patientres = '';
       
       if($id==0){
           if($code_type=='CPT4' && $justify=='')
           {     
                      
            $sql = "INSERT INTO billing (date, code_type, code, pid, provider_id, user,  groupname, authorized, encounter,  code_text, billed, activity,payer_id,bill_process,bill_date,process_date,process_file,modifier,units,fee,justify,target,x12_partner_id,ndc_info,notecodes)"
                        . " VALUES ( 'NOW()','$code_type','$code','$pid','$provider_id', '$user','$groupname', ' $authorized',  ' $encounter', '$code_text', '$billed', '$activity' ,'$payer_id','$bill_process','$bill_date','$process_date','$process_file','$modifier','$units','$fee','$justify','$target','$x12_partner_id','$ndc_info','$notecodes')"; 
            $sqlstmt = $db->prepare($sql) ;
            $data =  $sqlstmt->execute();  
            
            $sql1 = "UPDATE form_encounter SET provider_id ='$user', supervisor_id ='$supervisor_id' WHERE pid ='$pid' AND encounter ='$encounter'";
            $sqlstmt1 = $db->prepare($sql1) ;
            $data1 =  $sqlstmt1->execute();  

           }
           else if($code_type=='CPT4' && $justify!='')
           {
                                 
                $sql2 = "INSERT INTO billing (date, code_type, code, pid, provider_id, user,  groupname, authorized, encounter,  code_text, billed, activity,payer_id,bill_process,bill_date,process_date,process_file,modifier,units,fee,justify,target,x12_partner_id,ndc_info,notecodes)"
                            . " VALUES ( 'NOW()','$code_type','$code','$pid','$provider_id', '$user','$groupname', ' $authorized',  ' $encounter', '$code_text', '$billed', '$activity' ,'$payer_id','$bill_process','$bill_date','$process_date','$process_file','$modifier','$units','$fee','$justify','$target','$x12_partner_id','$ndc_info','$notecodes')
                             "; 
                $sqlstmt2 = $db->prepare($sql2) ;
                $data =  $sqlstmt2->execute(); 

                foreach($diags as $diag1){

                    $diagnosis="ICD9:".$diag1;
                    $descr="select long_desc from icd9_dx_code where formatted_dx_code='$diag1'";
                    $sqlstmte = $db->prepare($descr) ;
                    $datas=  $sqlstmte->execute();
                    $desval1 = $sqlstmte->fetchAll(PDO::FETCH_OBJ);
                    $title =   $desval1[0]->long_desc;
                    
                    $sql1 = "INSERT INTO billing (date, code_type, code, pid, provider_id, user,  groupname, authorized, encounter,  code_text, billed, activity,payer_id,bill_process,bill_date,process_date,process_file,target,x12_partner_id,ndc_info,notecodes)"
                            . " VALUES ( 'NOW()','ICD9','$diag1','$pid','$provider_id', '$user','$groupname', ' $authorized',  ' $encounter', '$title', '$billed', '$activity' ,'$payer_id','$bill_process','$bill_date', '$process_date','$process_file','$target','$x12_partner_id','$ndc_info','$notecodes')";
                    $sqlstmt1 = $db->prepare($sql1) ;
                    $data =  $sqlstmt1->execute();
                    if($issue_list=='YES'){
                        $sel="select date from form_encounter where pid='$pid' AND encounter='$encounter'";
                        $sqlstmt2 = $db->prepare($sel) ;
                        $data2=  $sqlstmt2->execute();
                        $idval = $sqlstmt2->fetchAll(PDO::FETCH_OBJ);
                        $enc_date =  $idval[0]->date;  
                        
                        $sql4 = "INSERT into lists(date,begdate,type,occurrence,classification,pid,diagnosis,title,modifydate) values('$enc_date','$enc_date','medical_problem',0,0,$pid,'$diagnosis','$title',NOW())"; echo"<br>";
                        $sqlstmt4 = $db->prepare($sql4);
                        $data4 =  $sqlstmt4->execute();

                        $sel="select id from lists where type='medical_problem' AND diagnosis='$diagnosis' AND pid=$pid";
                        $sqlstmt2 = $db->prepare($sel) ;
                        $data2=  $sqlstmt2->execute();
                        $idval1 = $sqlstmt2->fetchAll(PDO::FETCH_OBJ);
                        $newid =  $idval1[0]->id;

                        $sel1="select pid,list_id ,encounter from issue_encounter where encounter=$encounterid";
                        $selstmt = $db->prepare($sel1) ;
                        $seldata =  $selstmt->execute();
                        $val = $selstmt->fetchAll(PDO::FETCH_OBJ);

                        if(!empty($val)){
                           foreach($val as $value){
                               if($value->list_id!=$newid && $value->encounter!=$encounter){  
                                   $sql5="INSERT INTO issue_encounter ( pid, list_id, encounter) VALUES ( $pid, $newid, $encounterid)";
                                   $sqlstmt5 = $db->prepare($sql5) ;
                                   $data5 =  $sqlstmt5->execute();
                                }
                            } 
                        }else{
                            $sql5="INSERT INTO issue_encounter ( pid, list_id, encounter) VALUES ( $pid, $newid, $encounterid)";
                            $sqlstmt5 = $db->prepare($sql5) ;
                            $data5 =  $sqlstmt5->execute();
                        }

                        $sel="select pid,type from lists_touch where pid=$pid";
                        $sqlstmt2 = $db->prepare($sel) ;
                        $data2=  $sqlstmt2->execute();
                        $idval2 = $sqlstmt2->fetchAll(PDO::FETCH_OBJ);
                        if(!empty($idval2)){
                            $p_id =  $idval2[0]->pid;
                            $list_type = $idval2[0]->type;
                            if($p_id!=$pid && $list_type!='medicalproblem'){
                                $sql6="INSERT INTO lists_touch ( pid, type, date) VALUES ( $pid, 'medical_problem', NOW())";
                                $sqlstmt6 = $db->prepare($sql6) ;
                                $data6 =  $sqlstmt6->execute();
                            }
                        }else{
                            $sql6="INSERT INTO lists_touch ( pid, type, date) VALUES ( $pid, 'medical_problem', NOW())";
                            $sqlstmt6 = $db->prepare($sql6) ;
                            $data6 =  $sqlstmt6->execute();
                        }
                    }
                }
                $sql3 = "UPDATE form_encounter SET provider_id ='$user', supervisor_id ='$supervisor_id'  WHERE pid ='$pid'  AND encounter ='$encounter'";
                $sqlstmt3 = $db->prepare($sql3) ;
                $data3 =  $sqlstmt3->execute();  
            }
          
            if($code_type=='ICD9'){
                $sql1 = "INSERT INTO billing (date, code_type, code, pid, provider_id, user,  groupname, authorized, encounter,  code_text, billed, activity,payer_id,bill_process,bill_date,process_date,process_file,target,x12_partner_id,ndc_info,notecodes)"
                            . " VALUES ( 'NOW()','$code_type','$code','$pid','$provider_id', '$user','$groupname', ' $authorized',  ' $encounter', '$code_text', '$billed', '$activity' ,'$payer_id','$bill_process','$bill_date', '$process_date','$process_file','$target','$x12_partner_id','$ndc_info','$notecodes')";
                $sqlstmt1 = $db->prepare($sql1) ;
                $data =  $sqlstmt1->execute();  
                
                $sql5 = "UPDATE form_encounter SET provider_id ='$user', supervisor_id ='$supervisor_id' WHERE pid ='$pid' AND encounter ='$encounter'";
                $sqlstmt5 = $db->prepare($sql5) ;
                $data5 =  $sqlstmt5->execute();        
            }
        }else{
           if($code_type == 'CPT4' && $justify == ''){
                $sql = "UPDATE billing SET  code='$code', provider_id='$provider_id',  authorized='$authorized',billed='$billed', activity='$activity',
                   payer_id='$payer_id',bill_process='$bill_process',bill_date='$bill_date',process_date='$process_date' ,process_file='$process_file',modifier='$modifier',units='$units',fee='$fee',justify='$justify',target='$target',x12_partner_id='$x12_partner_id',ndc_info='$ndc_info',notecodes='$notecodes' WHERE  id = $id";                   
                $sqlstmt = $db->prepare($sql) ;
                $data =  $sqlstmt->execute();
                
                $sql5 = "UPDATE form_encounter SET provider_id ='$user', supervisor_id ='$supervisor_id' WHERE pid ='$pid' AND encounter ='$encounter'";
                $sqlstmt5 = $db->prepare($sql5) ;
                $data5 =  $sqlstmt5->execute();      
           }else if($code_type=='CPT4' && $justify!=''){
                $sql2 = "UPDATE billing SET code='$code',provider_id='$provider_id', authorized='$authorized',billed='$billed', activity='$activity',
                   payer_id='$payer_id',bill_process='$bill_process',bill_date='$bill_date',process_date='$process_date' ,process_file='$process_file',modifier='$modifier',units='$units',fee='$fee',justify='$justify',target='$target',x12_partner_id='$x12_partner_id',ndc_info='$ndc_info',notecodes='$notecodes' WHERE  id = $id";                   
                $sqlstmt2 = $db->prepare($sql2) ;
                $data =  $sqlstmt2->execute();
                
                foreach($diags as $diag4){

                    $diagnosis="ICD9:".$diag4;

                    $descr="select long_desc from icd9_dx_code where formatted_dx_code='$diag4'";
                    $sqlstmte = $db->prepare($descr) ;
                    $datas=  $sqlstmte->execute();
                    $desval1 = $sqlstmte->fetchAll(PDO::FETCH_OBJ);
                    $title =   $desval1[0]->long_desc;
                    
                    $sql1 = "INSERT INTO billing (date, code_type, code, pid, provider_id, user,  groupname, authorized, encounter,  code_text, billed, activity,payer_id,bill_process,bill_date,process_date,process_file,target,x12_partner_id,ndc_info,notecodes)"
                    . " VALUES ( 'NOW()','ICD9','$diag4','$pid','$provider_id', '$user','$groupname', ' $authorized',  ' $encounter', ' $title', '$billed', '$activity' ,'$payer_id','$bill_process','$bill_date', '$process_date','$process_file','$target','$x12_partner_id','$ndc_info','$notecodes')";
                    $sqlstmt1 = $db->prepare($sql1) ;
                    $data =  $sqlstmt1->execute();
                    
                    if($issue_list == 'YES'){
                        $sel="select date from form_encounter where pid='$pid' AND encounter='$encounter'";
                        $sqlstmt2 = $db->prepare($sel) ;
                        $data2=  $sqlstmt2->execute();
                        $idval = $sqlstmt2->fetchAll(PDO::FETCH_OBJ);
                        $enc_date =  $idval[0]->date; 
                        
                        $sql4 = "INSERT into lists(date,begdate,type,occurrence,classification,pid,diagnosis,title,modifydate) values('$enc_date','$enc_date','medical_problem',0,0,$pid,'$diagnosis','$title',NOW())";
                        $sqlstmt4 = $db->prepare($sql4);
                        $data4 =  $sqlstmt4->execute();

                        $sel="select id from lists where type='medical_problem' AND diagnosis='$diagnosis' AND pid=$pid";
                        $sqlstmt2 = $db->prepare($sel) ;
                        $data2=  $sqlstmt2->execute();
                        $idval1 = $sqlstmt2->fetchAll(PDO::FETCH_OBJ);
                        $newid =  $idval1[0]->id;

                        $sel1="select pid,list_id ,encounter from issue_encounter where encounter=$encounterid AND pid='$pid'";
                        $selstmt = $db->prepare($sel1) ;
                        $seldata =  $selstmt->execute();
                        $listval = $selstmt->fetchAll(PDO::FETCH_OBJ);

                        if(!empty($listval)){ 
                            foreach($listval as $element){
                                if( $element->list_id != $newid && $element->encounter != $encounter){    
                                    $sql5="INSERT INTO issue_encounter ( pid, list_id, encounter) VALUES ( $pid, $newid, $encounterid)";
                                    $sqlstmt5 = $db->prepare($sql5) ;
                                    $data5 =  $sqlstmt5->execute();
                                }
                            }  
                        }
                        else
                        { 
                            $sql5="INSERT INTO issue_encounter ( pid, list_id, encounter) VALUES ( $pid, $newid, $encounterid)";
                            $sqlstmt5 = $db->prepare($sql5) ;
                            $data5 =  $sqlstmt5->execute();
                        }
                        $sel="select pid,type from lists_touch where pid=$pid";
                        $sqlstmt2 = $db->prepare($sel) ;
                        $data2=  $sqlstmt2->execute();
                        $idval2 = $sqlstmt2->fetchAll(PDO::FETCH_OBJ);
                        if(empty($idval2)){
//                            $p_id =  $idval2[0]->pid;
//                            $list_type = $idval2[0]->type;
//                            if($p_id != $pid && $list_type != 'medicalproblem'){
//                                $sql6="INSERT INTO lists_touch ( pid, type, date) VALUES ( $pid, 'medical_problem', NOW())";
//                                $sqlstmt6 = $db->prepare($sql6) ;
//                                $data6 =  $sqlstmt6->execute();
//                            }
//                        }else{
                            $sql6="INSERT INTO lists_touch ( pid, type, date) VALUES ( $pid, 'medical_problem', NOW())";
                            $sqlstmt6 = $db->prepare($sql6) ;
                            $data6 =  $sqlstmt6->execute();
                        }
                    }
                }
                $sql5 = "UPDATE form_encounter SET provider_id ='$user', supervisor_id ='$supervisor_id'  WHERE pid ='$pid' AND encounter ='$encounter'";
                $sqlstmt5 = $db->prepare($sql5) ;
                $data5 =  $sqlstmt5->execute();  
           }
           if($code_type=='ICD9')
           {
               $sql1 = "UPDATE billing SET  code='$code',provider_id='$provider_id', authorized='$authorized',billed='$billed', activity='$activity',
                   payer_id='$payer_id',bill_process='$bill_process',bill_date='$bill_date',process_date='$process_date' ,process_file='$process_file',target='$target',x12_partner_id='$x12_partner_id',ndc_info='$ndc_info',notecodes='$notecodes' WHERE  id = $id";                   
                $sqlstmt1 = $db->prepare($sql1) ;
                $data =  $sqlstmt1->execute();
                
                $sql5 = "UPDATE form_encounter SET provider_id ='$user', supervisor_id ='$supervisor_id'  WHERE pid ='$pid' AND encounter ='$encounter'";
                $sqlstmt5 = $db->prepare($sql5) ;
                $data5 =  $sqlstmt5->execute();     
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
/* ------------ Subhan methods end here ---------------- */
?>
