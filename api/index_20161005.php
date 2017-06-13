<?php  
/*   
Webservice for user/provider authentication with openemr
*/
header('Access-Control-Allow-Origin: *');

// do initial application
require 'Slim/Slim.php';
require 'Slim/Route.php';
require 'AesEncryption/GibberishAES.php';
//require_once('../library/encounter_events.inc.php');


ini_set('memory_limit', '1000M'); 
ini_set('post_max_size', '2000M');
ini_set('upload_max_filesize', '500M');
ini_set('max_execution_time', 600); 


// initialize app 
$app = new Slim();
               
//method to get prescriptions
$app->get('/getprescriptions/:patientId','getPrescriptions');
/* ============ Hema methods calls start here ================== */
// To retrieve all addressbook types
$app->get('/alladdressbooktypes/:all', 'getAllAddressbookTypes');
// to retrieve Patient Agency List
$app->get('/patientagencylist/:pid', 'getDynamicPatientAgencies');
// to get patient insurance details list
$app->get('/getpatientinsurancelist/:pid','getPatientInsuranceDataList');
// to get incompleteencountercount
$app->get('/incompleteencountercount/:providerid','getIncompleteEncounterCount');
// to get details of incomplete encounter details
$app->get('/incompleteencounterdetails/:pid/:uid','getIncompleteEncounterList');
// to update history data of patient
$app->post('/updatehistorydata','updatePatientHistory');
// to get incomplete patient encounter list
$app->get('/incompletepatientencounterlist/:providerid','getPatientIncompleteEncounterCount');
// to get visit category and dos 
$app->get('/dosvisitlist/:pid/:eid','getDosVisitList'); 
// to get dos form data
$app->get('/getdosformdata/:eid','getDosFormData'); 
// to insert speech file to google drive
$app->get('/insertfiletogdrive','insertSpeechDicatation');
// to get layout forms data
$app->get('/getdictationformdata/:eid/:formname','getDictationFormData');
// to save layoutform data
$app->post('/savelayoutformsdata','saveLayoutFormsData');
// to get all patients from openemr for mobile
$app->get('/getallpatients','getTotalPatients');
// to get all form list and data for paticular encounter
$app->get('/getdosformdatadetails/:eid1/:eid2/:provider', 'getDosFormDataDetails');
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
// to get billing access of user
$app->get('/getbillingaccess/:uid', 'getBillingAccess');
// to get mobile query dropdown
$app->get('/mobileallpatientfilters','MobileAllPatientFilters');
// to get mobile query dropdown
$app->get('/mobilemypatientfilters','MobileMyPatientFilters');
// to get mobile query dropdown
$app->get('/mobilefacilityfilters','MobileFacilityFilters');
// to get mobile query dropdown
$app->get('/mobileappointmentfilters','MobileAppointmentFilters');
// to get filters data for allpatients/ mypatients screen in mobile
$app->get('/filtersdata/:pid', 'filitersData');
// to get audit form
$app->get('/auditform/:formid/:pid/:eid', 'getAuditForm');
// to get CPO form
$app->get('/getcpo/:formid/:pid/:eid', 'getCPO');
// to get CCM form
$app->get('/getccm/:formid/:pid/:eid', 'getCCM');
// to save CPO form
$app->post('/savecpo', 'saveCPO');
// to save CCM form
$app->post('/saveccm', 'saveCCM');
// to get addressbook attributes
$app->get('/addressbookcustomattributes/:uid','getAddressbookCustomAttributes');
// to get addressbook credentials
$app->get('/addressbookcred/:uid','getAddressbookCred');
// to get addressbook contacts
$app->get('/addressbookcontacts/:uid','getAddressbookContacts');
// to edit addressbook attributes
$app->get('/editaddressbookcustomattributes/:uid/:id','editAddressbookCustomAttributes');
// to edit addressbook credentials
$app->get('/editaddressbookcred/:uid/:id','editAddressbookCred');
// to edit addressbook contacts
$app->get('/editaddressbookcontacts/:uid/:id','editAddressbookContacts');
// to edit addressbook attributes
$app->get('/createaddressbookcustomattributes/:uid','createAddressbookCustomAttributes');
// to edit addressbook credentials
$app->get('/createaddressbookcred/:uid','createAddressbookCred');
// to edit addressbook contacts
$app->get('/createaddressbookcontacts/:uid','createAddressbookContacts');
// to save addressbook attributes
$app->post('/saveaddressbookcustomattributes','saveAddressbookCustomAttributes');
// to save addressbook credentials
$app->post('/saveaddressbookcred','saveAddressbookCred');
// to save addressbook contacts
$app->post('/saveaddressbookcontacts','saveAddressbookContacts');
// to get addressbook data list
$app->get('/getaddressbooklist','getAddressbookList');
// to create addressbook data 
$app->get('/editaddressbook/:id','editAddressbook');
// to save addressbook data
$app->post('/saveaddressbook','saveAddressbook');
//medical_record data
$app->get('/eidmedicalrecord/:pid/:eid','getEncounterMedicalRecord');
// to get one dignosis list value
$app->get('/getdiag/:field', 'getDiaog');
// to get editfacility screen
$app->get('/editbyfacility/:id', 'editByFacility');
// method to search mobile filters patient by name
$app->get('/searchmobilepatientbyname/:list_id/:loginProvderId/:pname/:value/:fid','searchMobilePatientByName');
// method to search mobile filters patient by name
$app->get('/searchfacilitypatientbyname/:list_id/:loginProvderId/:pname/:value/:fid/:list_id2/:seen','searchMobilePatientByNameInFacility');


// to get addressbook dropdown values
$app->get('/addressbookorgdropdown','getAddressbookOrgDropdown');
// to get quality of care
$app->get('/getqoc/:encounterid/:formid/:uid', 'getQOC');
// for google Plus login
$app->post('/googleapilogin','google_api_login'); 
/* ============ Hema methods calls end here ================== */

/* ============ bhavya methods calls start here ================== */

//method to get ClinicalReminders
$app->get('/clinicalreminders/:pid', 'getClinicalRem');
//To get feesheet codes
$app->get('/feesheet/:encounterid', 'getFeesheetCodes');
//To get issues based on encounter
$app->get('/issues/:encounter/:type', 'getIssuesByEncounter');
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
//medical_record list
$app->get('/medical_recorddata/:pid','getMedical_recorddata');
//medical_record data
//$app->get('/medical_record/:pid/:id','getMedical_record');
/* ============ bhavya methods calls start here ================== */
/* ------------ Subhan methods calls start here ------------------ */
$app->post('/fileupload', 'uploadFile');
/* ------------ Subhan methods calls end here ------------------ */
// method for provider login
$app->post('/login', 'loginUser');
// method to get list of todays appointments for given provider
$app->get('/patients/:loginUserId', 'getPatients');
// method to get list of all patients
$app->get('/allpatients/:value/:seen', 'getAllPatients');
// method to get list of patients belonging to given provider
$app->get('/filterpatients/:loginProvderId/:value/:seen', 'getPatientsByProvider');
// method to get list of appointments for a given date
$app->get('/patientsbyday/:loginProvderId/:day/:seen', 'getPatientsByday');

// method to get list of complete/incomplete encounters for given provider
$app->get('/myencounters/:loginProvderId/:patientId/:mode', 'getEncounterList');
// method to get list of patients belonging to given provider for messages
$app->get('/mypatients/:loginProvderId/:fromCount','getMyPatients');

// method to create new message
$app->post('/createmessage','createMessage');

// method to check if encounter exists
$app->get('/checkencounterexists/:pid/:loginProvderId/:encDate','checkEncounterExists');

// Methods required for Fee Sheet/Billing
$app->get('/users','getUsers');

// method to get all facilities
$app->get('/facilities','getAllFacilities');

//$app->get('/patientsbyfacility/:fid/:loginProvderId','getPatientsByFacility');
$app->get('/patientsbyfacility/:fid/:uid/:value/:seen','getPatientsByFacility');

// method to get vitals form data
$app->get('/getvitalsform/:encounterId/:Id','getVitalsForm');

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

$app->get('/getmessages/:loginProvderId','getMessages');

$app->get('/getreminders/:loginProvderId/:days_to_show/:alerts_to_show','getReminders');

$app->get('/getreminderscount/:loginProvderId/:days_to_show/:alerts_to_show','getRemindersCount');

//method to set reminder as completed
$app->get('/setreminderasprocessed/:reminderId/:loginProvderId','setReminderAsProcessed'); 

$app->get('/getactivemessages/:loginProvderId/:isActive','getActiveMessages');

$app->get('/getactivemessagescount/:loginProvderId/:isActive','getactivemessagesCount');

// method to get demographics billing
$app->get('/getdemographicsbilling/:pid/:with_insurance','getDemographicsBilling');

//method to add/edit patient facility
$app->post('/editpatientfacility','editPatientFacility');

//method to update appointment status as rescheduled/cancel/ no show
$app->post('/changeappstatus','changeAppointmentStatus');
$app->post('/cancelappstatus','cancelAppointmentStatus');

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

function getConnection1() 
{
    $dbhost="172.99.97.16";
    $dbuser="551948_allcare";
    $dbpass="Allcare@123";
    $dbname="551948_allcare";  
    
    $dbh = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass); 
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $dbh;
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
        $key = 'rotcoderaclla';
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        $request = Slim::getInstance()->request();
//        $logObject = json_decode($request->getBody(),TRUE);
        $appres = GibberishAES::dec($request->getBody(), $key);
//$logObject = json_decode('{"username":"U2FsdGVkX19kncTcTbPz9ej3yGix6EFPgEQqqdaRet4=","password":"U2FsdGVkX1/faxVL9jZ1k5mYF9X//8h9aNw9aapVavA="}',TRUE);
        
//        $username = GibberishAES::dec($logObject['username'], $key);
//        $password = GibberishAES::dec($logObject['password'], $key);

        $insertArray = json_decode($appres,TRUE);
        $username = $insertArray['username'];
        $password = $insertArray['password'];

        $sql="SELECT us.id,us.username,us.password,us.salt,u.fname,u.lname, u.authorized
        FROM users_secure us 
        INNER JOIN users u ON us.id=u.id 
        WHERE us.username='$username'";
        
        $stmt = $db->prepare($sql) ;
//        $stmt->bindParam("username", $username);
        $stmt->execute();
        $user = $stmt->fetchObject();  
        
        $sql_practice="SELECT notes FROM list_options WHERE list_id = 'Mobile_App_Config' AND option_id = 'mbl_practice_name'"; // retrieved from lists section of list name 'Mobile_App_Config'
        
        $stmt_practice = $db->prepare($sql_practice) ;
        $stmt_practice->execute();
        $practice = $stmt_practice->fetchObject();     
        if($practice)
            $practicename = $practice->notes;
        else
            $practicename = "";
//        $practicename = "TPHC Allcare Doctor EMR";
        if($user){
            //  to get business url
            $sql2="SELECT description,title ,(select id FROM tbl_user_custom_attr_1to1 WHERE business_support = 'YES' AND userid = '$user->id') as access 
            FROM layout_options 
            WHERE group_name like '%Capabilities' AND field_id = 'business_support' AND form_id = 'UCA'";

            $stmt2 = $db->prepare($sql2) ;
            $stmt2->execute();
            $businessurl2 = $stmt2->fetchObject(); 
            $business_support = $business_title ='';
            $business_access  = 0;
            if(!empty($businessurl2)){
                $business_support = $businessurl2->description;
                $business_title = $businessurl2->title;
                $business_access = !empty($businessurl2->access)? 1 :0;
            }    
            //  to get technical url
            $sql3="SELECT description, title,(select id FROM tbl_user_custom_attr_1to1 WHERE technical_support = 'YES' AND userid = '".$user->id."') as access 
            FROM layout_options 
            WHERE group_name like '%Capabilities' AND field_id = 'technical_support' AND form_id = 'UCA'";

            $stmt3 = $db->prepare($sql3) ;
            $stmt3->execute();
            $technicalurl2 = $stmt3->fetchObject(); 
            $technical_support= $technical_title = '';
            $technical_access = 0;
            if(!empty($technicalurl2)){
                $technical_support = $technicalurl2->description;
                $technical_title = $technicalurl2->title;
                $technical_access = !empty($technicalurl2->access)? 1 :0;
            }    
            //  to get provider portal
            $sql4="SELECT description, title,(select id FROM tbl_user_custom_attr_1to1 WHERE provider_portal = 'YES' AND userid = '".$user->id."') as access 
            FROM layout_options 
            WHERE group_name like '%Capabilities' AND field_id = 'provider_portal' AND form_id = 'UCA'";

            $stmt4 = $db->prepare($sql4) ;
            $stmt4->execute();
            $providerportal2 = $stmt4->fetchObject(); 
            $provider_portal= $provider_title = '';
            $provider_access = 0;
            if(!empty($providerportal2)){
                $provider_portal = $providerportal2->description;
                $provider_title = $providerportal2->title;
                $provider_access = !empty($providerportal2->access)? 1 :0;
            }    
            //  to get practice portal
            $sql5="SELECT description,title, (select id FROM tbl_user_custom_attr_1to1 WHERE practice_portal = 'YES' AND userid = '".$user->id."') as access 
            FROM layout_options 
            WHERE group_name like '%Capabilities' AND field_id = 'practice_portal' AND form_id = 'UCA'";

            $stmt5 = $db->prepare($sql5) ;
            $stmt5->execute();
            $practiceportal2 = $stmt5->fetchObject(); 
            $practice_portal=$practice_title = '';
            $practice_access= 0;
            if(!empty($practiceportal2)){
                $practice_portal = $practiceportal2->description;
                $practice_title = $practiceportal2->title;
                $practice_access = !empty($practiceportal2->access)? 1 :0;
            }
        }
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
//				$string = '[{"id":'.json_encode($user->id).',"fname":'.json_encode($user->fname).',"lname":'.json_encode($user->lname).',"authorized":'.json_encode($user->authorized).',"user_role":'.json_encode($user_role).',"acl_role_name":'.json_encode($acl_role).',"acl_role_permissions":'.json_encode($acl_role_permissions).',"rights":'.json_encode($rights).',"practice_name":'.json_encode($practicename).',"business_support":'.json_encode($business_support).',"technical_support":'.json_encode($technical_support).',"practice_portal":'.json_encode($practice_portal).',"provider_portal":'.json_encode($provider_portal).'}]';
                                $string = '[{"id":'.json_encode($user->id).',"fname":'.json_encode($user->fname).',"lname":'.json_encode($user->lname).',"authorized":'.json_encode($user->authorized).',"user_role":'.json_encode($user_role).',"acl_role_name":'.json_encode($acl_role).',"acl_role_permissions":'.json_encode($acl_role_permissions).',"rights":'.json_encode($rights).',"practice_name":'.json_encode($practicename).',"business_support":'.json_encode($business_support).',"technical_support":'.json_encode($technical_support).',"practice_portal":'.json_encode($practice_portal).',"provider_portal":'.json_encode($provider_portal).',"business_title":'.json_encode($business_title).',"technical_title":'.json_encode($technical_title).',"provider_title":'.json_encode($provider_title).',"practice_title":'.json_encode($practice_title).',"business_access":'.json_encode($business_access).',"technical_access":'.json_encode($technical_access).',"provider_access":'.json_encode($provider_access).',"practice_access":'.json_encode($practice_access).'}]';
                                echo $stringenc = GibberishAES::enc($string, $key);
                                insertMobileLog('login in',$username,$sql,'','','Logged in Successfully',1);
//                                //die();
//                                echo "<pre>"; print_r($string); echo "</pre>";
            } 
            else 
            {
                // if username or password is wrong return id=0
                $string = '[{"id":"0"}]';
                echo $stringenc = GibberishAES::enc($string, $key);
                insertMobileLog('login in',$username,$sql,'','','Logged in Failed as wrong credntials Entered',0);
//                //die();
            }                               
        }
        else
        {
            // if user does not exist return id=0
            $string = '[{"id":"0"}]';
            echo $stringenc = GibberishAES::enc($string, $key);
            insertMobileLog('login in',$username,$sql,'','','Logged in Failed as user doesnt exist',0);
//            //die();
        }
    } 
    catch(PDOException $e) 
    {
        $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo $stringenc = GibberishAES::enc($error, $key);
        insertMobileLog('login in',$username,$error,'','','Logged in Failed - Query Failed',0);
//        //die();
    }
}

function google_api_login(){
    try 
    {
        $db = getConnection(); 
        
        $apikey     = 'rotcoderaclla';
        $storerequest = Slim::getInstance()->request();
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
//        $logObject = json_decode($request->getBody(),TRUE);
        $appres = GibberishAES::dec($storerequest->getBody(), $apikey);
//        $decrypedtext = $decryptor->decrypt($storerequest->getBody(), $apikey);
//        $decrypedtext = $decryptor->decrypt('AwHdRmtZ36QlzgSjoqB2A0G8TAzDM4I7xe2aSsTWQ3TlV775AQDrCm1rWjytviL/VO+PAVQXHvBmWE0J1/BVbUn0GSbhQ91loAEtR2jkq7rfg+FME0ZJXZr8qpCZm/RtDt6nCkNJoxxs53jAJD/BVaaI39wB7eDL+1mKF4LRvN8GNmCAeSiofgxDwMW6QJ/Aj6J31fnNXDgKKgITYaBcRc9LFf0Otd7/yPcuqXkPC2nE7zHofsMU7igMqXyuSIJMxo+Hj4qiAQ0V9nQTppCddRhNjS4mzk/x3I384KjzmZWyOuXBBEtRb+BK+Rg0bfnVoi0jV9wZ1RBo8Qnd+3oUUkok96Rsz4wyB0QqMDtsW+vkmZPVWp0Y6ns96r5BamLbY0fO6egTOUS4l8bSL3TfXmwW1oWPDPo+PEnkwR+hjuJe4RcGGZDPJtUyYgLsYyNgsJWBRn5I+HgNNcO0huSgC2oPu3z1IvdLev8S1AlAkbOx8QJI47VSQPtZluioUkdIw5ev3KTzpRVdDxh7uYavv/XwMvcltlTHrcnJ0iGRDjaOCo0Yk89xFeWFq5wZrmSeujxlCwjek7iO8fkst5hKW65OQF+Dwel7JL/EN+lkh+XmZnpgQ9GN83PXNTBb87EHFqaJwNpW0H2WVqGFjTkGDf0KMui8ieo32HJeePTG/pSkCymdj2hSZ8T8xCSZv/qhTC87XflK1Apjq4qxprnOvWcYBEi3PfYlU8gmoSgiZnKCIagElLv/kMCPmLvPysBmhGyRcGEi+69y9mSabKU9s+ONyFa95GeOj38vHvrZX47mzYdmhXRmBI1jlJDbv4uUMMQnet9rTmksThjNSEReTGjsjCpP3KifjXyaDlrOU/g8HVgZBPeJEsS/7cpoVv+AM1Ddsh/crtS4eBhD0DaAahwTaLq26Wxer/rSlSSd3dkQvXk96RTnVYcJm7nfBZr4Bn5+8O1gXprRrwXOIo5JYlgu8IwqyQsEO7It/9lHVJ9Jp9UD9hCo32G2w+TyNzL8UEcSyTcn9/FVqDRBCsaXvjX5YlZZBkTsxr2hBFkwq6wJwn2r85iiWt2hAjsKUu0v6hielVoh1DYmwnkA2XtVPgcIcLfDGlGZwzcDSxeSHxTu/3D/O//78Isu3Dx2rMC2eGVqc8i8IVG4IxahIpc4FL8P8jHTKQGh2XzkrkAf7BaNZiB72TSFLwMOlvi8ApCvLdpYup3FScbvftThJNTojFWfmeCmeICRNYdHOUmHRd/W9t6KkBULI9gvaI6mLrwTdcWWWq1dDURAY4Q7PCeXxG6RSFP1Fw/Hc64hIFEK59xWBQcvqGvzrod5cLM+vIvPxM6yu5ujSZKmz/iA1xEYzgTdtg3r7ZcKn3ScnIOVM+6D3Y+CU5qpojULPfWc0PWpSg6HEu9TdRaxMdwjFk9u6jO+VB9qUYpsz9oa9cUFyyifUUPNLfWOLnl+SxsbWZZIYHjRzCRGgllyoHh20sB6gcfRrLCG9/vOYqIRGInDuuHvxVYdUMprHdBqfqw3xSzL6Q2VVI05zEuav6kbCineiBjj79weAjzhfUxfZehqO22BK7vhT8nwBrasM5g1XCItSx8q8U5OJUYZGam+mZpJu+0BHKQ7MJqKrqB2r+RFQtqNENnpoMRjhJkb0KfLZKcZlNlecY0wuQFUu7Ucu2F0p8lA0GNIO+yq53CpytibgTdfIXnGc4wS34GYw9Vs1QulJuRuggiGt52EeB0aWtlFvK55S17Prg9yHfYG0RQvjgIuPSagzYSm1hRugIDNwF0IHMrnYQgVrppMzD5odncFV3YUqqSdd6h07whydC5hjYEOw6unN65wKc5DM1ZrVWPkIJWZKY5k1a7cn1XbcmmlcsndcISOiCTsDpYKu9G+8iG8Y1Ow7w+9KDu0pTd4c2ZZQEGXzObzKVn/NmNMKdNIHcnKsb9gFyxqdJXHMWAMkgMJXxDHe5uoyAznxEUd7qN/JuEz4Q8PYmNT97AizfWdmOt5YfSNp5ugPAub7xrby52aIflpvfDepJbWhQCcM3uJpeyljMAj97Qup0JhMvvw2OVGu0cf8Sp671U7OVglPJ7GZAkyQaxrYYSoDMHNLHJlxHXiq0YVlMSq9OcGzlAq57wfKJ7CnD9Z5U3+vUJmbnnxxNwXjy1yM+8qdPStZ2/R0NZOK7E/OiTAcVz0R4eEu7Yuunkqpzq2rUt/Q7Xmf0E/GTiw5HrrvEP1EjbK1ksja40Pw09NXmye/7HxKtGuCkwwdCVKF8p91zDvxPn7BFri88TbL5N07joKtQcBozUEulF710FVoyg3rvIOEjliqx/7FL7cywe0POhqdp9C7ZMrnMQCBK02Zl9k5EBiZQWZGS6G9PpEZyrKADI7D/ijYKBtkzrJPJTgaGnBeeC00414RQnMB/2XvbGSILJ8KXb4WmoQULyI8slIqIZ2Lr+ij4jnxdosZ91xRylTb0pkg8qSfbVqq2wbId2YHocLxt2LmOEaSpjP45eWij6ms58y5ccMOWT9UJsMX0vXaSqgXHhlM8JsANEUrt3kGFkU0LJRgVLwenmL80BwthoK6KcsIyqr04r3MkbB8TblTHRWJEHK+spBSxx3kEpqXl0PsImS3W4crMQRCqyTY1HfvRHfG1UAeblOrSp2lDKAUf0GvlPGaGxP3XpuggKDU8dzCEB1B8glPZySUl/n5JX+nvUWtCBrmUGH8P69PY+yXetBdD27mSkYumCv9OxJ5122Cy4IpO/7DRdpn3H1WZ5sfb42ZC0JtaesdbInaBPQr/ztaOb3uiJbjG58TRULWqLR4bluL5cEuLFi8xFf7IFxD5dHwT0OvgTTNJcdW/dqfOwId+ikhgpcN3qY1NFtuR7HwBFcWE6SA7tXlE0Q0alSc/tJgYatzLgt0ap0a8sEEEG3ox9RWaNoBTj1WCx6WSUsh7SfYH/iRcfVFfZKE+0rRTVFlv1gjGqT7uA3asRRVq3wSSLO/kls2ujjFymxpjk4Kf1jf/XoTeZ67I0tO3xkcD3WGd5y9jkI+DHGIu+Y1y+k6HnZdTPZE6zfa5GPRQrKYvwGrW07MBrg9mMknJL7RrUy+K4YeNfMqt1nfUhjzAFzMIkhJeAVFPg9r+75JMIXFNZdyOLnNNFXsUJGBmkNMzAsAxKfmNqse0hs6xkctQwijIpr2o2hCYBUH11qCZ04GOMrzGS3fTHb/hy7o7+ITLt2rHEnN53JwOKNCaSrXOmTYlC1eTArVern0mdu2BJB4NLpoBPpy0SlO8V568mWXUH+j3fMaL2fZbxlEIEVpYZyWePbgjpYAUAviuRan3s9dkjPUnVOAui03Xd6tVQvXErCslfdYJrC0QJ/a5csj3ON8ziwXl3z6gY2UfooSrbw0jdleU5yS/N0acGalO2zIa4F1wH2OwAG7qOpOX4WEcRtONnfg8lM9nMjZnlFoIzkRwdzVKiD0328zR8mKFQht60KqI/b1EXR8KwyXqDYEHki7QHyBeslPjTwzlyt3qFW7TF4rAk8p2Kud9YrMsxlPeiaN4ig3fTzjSXxQp+HqH4OSsNMmcR9mGYRW05m6QK3PFLoqQiJlcMoVuNpupevhPrs77SRKuaYYDLKuHVjkbG0deHahh1WNgg9gVIDEBkNqrGlFe997Fc6I6KiiT/Rj9xaw16wOzl3mnoZTfDS3x0qkSJuIw1hj+dYrzq4+3PeqTx1yH0ZVpcbx0Dgg8UHYoxrVU5kFCyIIS9sYGDnzrbuYeGGCAf9uWXXBUeD5SQJrFy4k7lbO6xjc0Zat5zX+Sjfbm0WH2QgCLlXsMdS8OoOzMBdz51Ht0x+nUKrYTBgbwf1mbfR21NkhUXZ92VS2aBEyYZI+veTBvNEnDKnxaMKb8zh/HmPyFZ5J+TEWcNi18J8qwepCw6h/VvNOAm2wTm/4FuCd94LkYTpff2tJpSdmwAtdA==', $key);
//        $decrypedtext= '{"name":"DS Dharma","userID":"114032289131858120856","accessToken":"ya29.ngLvlTczcoYeMf9ChzonPKPYK2pTj1h4X6mXaGRHEJZ-hTJI4tZ2AdEZdOMX4z4D8g","email":"rithikdharma@gmail.com","clientID":"39206616439-en032ocv4j0darro92apjb97h3m71876.apps.googleusercontent.com","refreshToken":"1\/Ifp3j4JzjYxKMgI4vMM0KhmWOcmgCfXcgQ-Ym-rupQsMEudVrK5jSpoR30zcRFq6"}';            
        $json_a12       = trim(json_decode($appres));
//        echo "<pre>";print_r($json_a12); echo "</pre>";
        $json_a1        = json_decode($json_a12);
//       echo $decrypedtext[0]['accessToken']; 
//        echo "<pre>"; print_r($json_a1); echo "</pre>"; 
        $name      = $json_a1->name; 
//        $userID         = $json_a1['userID'];
        $accessToken    = $json_a1->accessToken;
        $email          = $json_a1->email;
        $clientID       = $json_a1->clientID;
        $refreshToken   = $json_a1->refreshToken;
        
        if(!empty($accessToken) && !empty($refreshToken) ){
        
        $email_query = "SELECT id,username FROM users where username='$email'";
        $stmt = $db->prepare($email_query) ;
        $stmt->execute();                       

        $email_value = $stmt->fetchAll(PDO::FETCH_OBJ); 
        if(!empty($email_value)){
            $sql="SELECT us.id,us.username,us.password,us.salt,u.fname,u.lname, u.authorized
                    FROM users_secure us 
                    INNER JOIN users u ON us.id=u.id 
                    WHERE us.id='".$email_value[0]->id."'";

            $stmt = $db->prepare($sql) ;
    //        $stmt->bindParam("username", $username);
            $stmt->execute();
            $user = $stmt->fetchObject();  

            $sql_practice="SELECT notes FROM list_options WHERE list_id = 'Mobile_App_Config' AND option_id = 'mbl_practice_name'";

            $stmt_practice = $db->prepare($sql_practice) ;
            $stmt_practice->execute();
            $practice = $stmt_practice->fetchObject();     
            if($practice)
                $practicename = $practice->notes;
            else
                $practicename = "";
    //        $practicename = "TPHC Allcare Doctor EMR";
            if($user){
                //  to get business url
                $sql2="SELECT description,title ,(select id FROM tbl_user_custom_attr_1to1 WHERE business_support = 'YES' AND userid = '$user->id') as access 
                FROM layout_options 
                WHERE group_name like '%Capabilities' AND field_id = 'business_support' AND form_id = 'UCA'";

                $stmt2 = $db->prepare($sql2) ;
                $stmt2->execute();
                $businessurl2 = $stmt2->fetchObject(); 
                $business_support= $business_title ='';
                $business_access = 0;
                if(!empty($businessurl2)){
                    $business_support = $businessurl2->description;
                    $business_title = $businessurl2->title;
                    $business_access = !empty($businessurl2->access)? 1 :0;
                }    
                //  to get technical url
                $sql3="SELECT description, title,(select id FROM tbl_user_custom_attr_1to1 WHERE technical_support = 'YES' AND userid = '".$user->id."') as access 
                FROM layout_options 
                WHERE group_name like '%Capabilities' AND field_id = 'technical_support' AND form_id = 'UCA'";

                $stmt3 = $db->prepare($sql3) ;
                $stmt3->execute();
                $technicalurl2 = $stmt3->fetchObject(); 
                $technical_support= $technical_title = '';
                $technical_access = 0;
                if(!empty($technicalurl2)){
                    $technical_support = $technicalurl2->description;
                    $technical_title = $technicalurl2->title;
                    $technical_access = !empty($technicalurl2->access)? 1 :0;
                }    
                //  to get provider portal
                $sql4="SELECT description, title,(select id FROM tbl_user_custom_attr_1to1 WHERE provider_portal = 'YES' AND userid = '".$user->id."') as access 
                FROM layout_options 
                WHERE group_name like '%Capabilities' AND field_id = 'provider_portal' AND form_id = 'UCA'";

                $stmt4 = $db->prepare($sql4) ;
                $stmt4->execute();
                $providerportal2 = $stmt4->fetchObject(); 
                $provider_portal= $provider_title = '';
                $provider_access = 0;
                if(!empty($providerportal2)){
                    $provider_portal = $providerportal2->description;
                    $provider_title = $providerportal2->title;
                    $provider_access = !empty($providerportal2->access)? 1 :0;
                }    
                //  to get practice portal
                $sql5="SELECT description,title, (select id FROM tbl_user_custom_attr_1to1 WHERE practice_portal = 'YES' AND userid = '".$user->id."') as access 
                FROM layout_options 
                WHERE group_name like '%Capabilities' AND field_id = 'practice_portal' AND form_id = 'UCA'";

                $stmt5 = $db->prepare($sql5) ;
                $stmt5->execute();
                $practiceportal2 = $stmt5->fetchObject(); 
                $practice_portal=$practice_title = '';
                $practice_access= 0;
                if(!empty($practiceportal2)){
                    $practice_portal = $practiceportal2->description;
                    $practice_title = $practiceportal2->title;
                    $practice_access = !empty($practiceportal2->access)? 1 :0;
                }
            }
            if($user)
            {
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
                $string = '[{"id":'.json_encode($user->id).',"fname":'.json_encode($user->fname).',"lname":'.json_encode($user->lname).',"authorized":'.json_encode($user->authorized).',"user_role":'.json_encode($user_role).',"acl_role_name":'.json_encode($acl_role).',"acl_role_permissions":'.json_encode($acl_role_permissions).',"rights":'.json_encode($rights).',"practice_name":'.json_encode($practicename).',"business_support":'.json_encode($business_support).',"technical_support":'.json_encode($technical_support).',"practice_portal":'.json_encode($practice_portal).',"provider_portal":'.json_encode($provider_portal).',"business_title":'.json_encode($business_title).',"technical_title":'.json_encode($technical_title).',"provider_title":'.json_encode($provider_title).',"practice_title":'.json_encode($practice_title).',"business_access":'.json_encode($business_access).',"technical_access":'.json_encode($technical_access).',"provider_access":'.json_encode($provider_access).',"practice_access":'.json_encode($practice_access).'}]';
                echo $base64Encrypted = GibberishAES::enc($string, $apikey);
//                //die();
            }
        }else{
            $get_email = "SELECT userid FROM tbl_user_custom_attr_1to1 where email='$email'";
            $stmt = $db->prepare($get_email) ;
            $stmt->execute();                       

            $set_email = $stmt->fetchAll(PDO::FETCH_OBJ); 
            if(!empty($set_email)){
                $userid = $set_email[0]->userid;
                $sql="SELECT us.id,us.username,us.password,us.salt,u.fname,u.lname, u.authorized
                    FROM users_secure us 
                    INNER JOIN users u ON us.id=u.id 
                    WHERE us.id='$userid'";

                $stmt = $db->prepare($sql) ;
        //        $stmt->bindParam("username", $username);
                $stmt->execute();
                $user = $stmt->fetchObject();  

                $sql_practice="SELECT notes FROM list_options WHERE list_id = 'Mobile_App_Config' AND option_id = 'mbl_practice_name'";

                $stmt_practice = $db->prepare($sql_practice) ;
                $stmt_practice->execute();
                $practice = $stmt_practice->fetchObject();     
                if($practice)
                    $practicename = $practice->notes;
                else
                    $practicename = "";
        //        $practicename = "TPHC Allcare Doctor EMR";
                if($user){
                    //  to get business url
                    $sql2="SELECT description,title ,(select id FROM tbl_user_custom_attr_1to1 WHERE business_support = 'YES' AND userid = '$user->id') as access 
                    FROM layout_options 
                    WHERE group_name like '%Capabilities' AND field_id = 'business_support' AND form_id = 'UCA'";

                    $stmt2 = $db->prepare($sql2) ;
                    $stmt2->execute();
                    $businessurl2 = $stmt2->fetchObject(); 
                    $business_support= $business_title ='';
                    $business_access = 0;
                    if(!empty($businessurl2)){
                        $business_support = $businessurl2->description;
                        $business_title = $businessurl2->title;
                        $business_access = !empty($businessurl2->access)? 1 :0;
                    }    
                    //  to get technical url
                    $sql3="SELECT description, title,(select id FROM tbl_user_custom_attr_1to1 WHERE technical_support = 'YES' AND userid = '".$user->id."') as access 
                    FROM layout_options 
                    WHERE group_name like '%Capabilities' AND field_id = 'technical_support' AND form_id = 'UCA'";

                    $stmt3 = $db->prepare($sql3) ;
                    $stmt3->execute();
                    $technicalurl2 = $stmt3->fetchObject(); 
                    $technical_support= $technical_title = '';
                    $technical_access = 0;
                    if(!empty($technicalurl2)){
                        $technical_support = $technicalurl2->description;
                        $technical_title = $technicalurl2->title;
                        $technical_access = !empty($technicalurl2->access)? 1 :0;
                    }    
                    //  to get provider portal
                    $sql4="SELECT description, title,(select id FROM tbl_user_custom_attr_1to1 WHERE provider_portal = 'YES' AND userid = '".$user->id."') as access 
                    FROM layout_options 
                    WHERE group_name like '%Capabilities' AND field_id = 'provider_portal' AND form_id = 'UCA'";

                    $stmt4 = $db->prepare($sql4) ;
                    $stmt4->execute();
                    $providerportal2 = $stmt4->fetchObject(); 
                    $provider_portal= $provider_title = '';
                    $provider_access = 0;
                    if(!empty($providerportal2)){
                        $provider_portal = $providerportal2->description;
                        $provider_title = $providerportal2->title;
                        $provider_access = !empty($providerportal2->access)? 1 :0;
                    }    
                    //  to get practice portal
                    $sql5="SELECT description,title, (select id FROM tbl_user_custom_attr_1to1 WHERE practice_portal = 'YES' AND userid = '".$user->id."') as access 
                    FROM layout_options 
                    WHERE group_name like '%Capabilities' AND field_id = 'practice_portal' AND form_id = 'UCA'";

                    $stmt5 = $db->prepare($sql5) ;
                    $stmt5->execute();
                    $practiceportal2 = $stmt5->fetchObject(); 
                    $practice_portal=$practice_title = '';
                    $practice_access= 0;
                    if(!empty($practiceportal2)){
                        $practice_portal = $practiceportal2->description;
                        $practice_title = $practiceportal2->title;
                        $practice_access = !empty($practiceportal2->access)? 1 :0;
                    }
                }
                if($user)
                {
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
                    $string = '[{"id":'.json_encode($user->id).',"fname":'.json_encode($user->fname).',"lname":'.json_encode($user->lname).',"authorized":'.json_encode($user->authorized).',"user_role":'.json_encode($user_role).',"acl_role_name":'.json_encode($acl_role).',"acl_role_permissions":'.json_encode($acl_role_permissions).',"rights":'.json_encode($rights).',"practice_name":'.json_encode($practicename).',"business_support":'.json_encode($business_support).',"technical_support":'.json_encode($technical_support).',"practice_portal":'.json_encode($practice_portal).',"provider_portal":'.json_encode($provider_portal).',"business_title":'.json_encode($business_title).',"technical_title":'.json_encode($technical_title).',"provider_title":'.json_encode($provider_title).',"practice_title":'.json_encode($practice_title).',"business_access":'.json_encode($business_access).',"technical_access":'.json_encode($technical_access).',"provider_access":'.json_encode($provider_access).',"practice_access":'.json_encode($practice_access).'}]';
                    echo $base64Encrypted = GibberishAES::enc($string, $apikey);
//                    //die();
                }
            }else{
                // if username or password is wrong return id=0
                $string = '[{"id":"0","msg":"The Email User doesnot exists in EMR."}]';
                echo $base64Encrypted = GibberishAES::enc($string, $apikey);
//                //die();
            }
        }
        }else{
            $string = '[{"id":"0","msg":"Login Token Issue."}]';
            echo $base64Encrypted = GibberishAES::enc($string, $apikey);
//            //die();
        }
    } 
    catch(PDOException $e) 
    {
        $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo $incompletelistresult = GibberishAES::enc($error, $apikey);
//        //die();
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
//                //die();
            }
            else
            {    
                //echo 'No Patient available';
                $patientsres = '[{"id":"0"}]';
                echo $patientsresult = GibberishAES::enc($patientsres, $key);
//                //die();
            }
        } 
        catch(PDOException $e) 
        {
            
            $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
            echo $patientsresult = GibberishAES::enc($error, $key);
//            //die();
        }
}

// method to get list of appointments for a given date
function getPatientsByday($loginUserId,$day,$seen)
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
            if(!empty($set_fuv)){
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
                
                $joinQuery = $query = '';
                $get_sql2 = "select notes from list_options WHERE list_id = 'Mobile_Appointment_Due_Filters'  AND option_id = '$seen'";      
                $set_stmt2 = $db->prepare($get_sql2) ;
                $set_stmt2->execute();                       

                $get_value2 = $set_stmt2->fetchAll(PDO::FETCH_OBJ);   
                $joinQuery = '';
                if(!empty($get_value2)){
                    $joinQuery = " INNER JOIN form_encounter f ON f.pid = pd.pid AND DATE_FORMAT(f.date,'%m-%d-%Y') =  ope.pc_eventdate ";
                    $query  .= " AND f.".$get_value2[0]->notes;
                }
            
                if(!empty($enc_value)){


                    $sql = "SELECT DISTINCT ope.pc_eid as apptid,pd.pid,pd.title,pd.fname,pd.lname,if (pd.sex = 'Female' ,'F','M' ) as sex,
                        DATE_FORMAT(pd.DOB,'%m-%d-%Y') as DOB,DATE_FORMAT(FROM_DAYS(DATEDIFF(CURDATE(),pd.DOB)), '%Y')+0 AS age,pd.street_addr,pd.city,pd.state,pd.country_code,pd.postal_code,pd.phone_home,pd.phone_biz,pd.phone_contact,pd.phone_cell,pd.contact_relationship as emergency_contact, pd.phone_contact as emergency_phone, DATE_FORMAT(ope.pc_eventDate,'%m-%d-%Y') as event_date ,if(ope.pc_endDate='0000-00-00',DATE_FORMAT(ope.pc_eventDate,'%m-%d-%y'),DATE_FORMAT(ope.pc_endDate,'%m-%d-%Y'))  as end_date ,ope.pc_duration, TIME_FORMAT(ope.pc_startTime, '%h:%i %p') AS start_time,TIME_FORMAT(ope.pc_endTime, '%h:%i %p') AS end_time,ope.pc_facility,ope.pc_catid,pc.pc_catname,ope.pc_billing_location,lo.option_id as symbol,lo.title as apptstatus,ope.pc_hometext As comments,pd.deceased_stat,pd.practice_status
                        FROM patient_data pd
                        INNER JOIN openemr_postcalendar_events ope ON pd.pid = ope.pc_pid
                        INNER JOIN openemr_postcalendar_categories pc ON pc.pc_catid = ope.pc_catid 
                        INNER JOIN list_options lo ON lo.option_id = ope.pc_apptstatus AND lo.list_id = 'apptstat' 
                        $joinQuery
                        WHERE ope.pc_aid = '$loginUserId'
                        AND ope.pc_eventdate = '$day'
                        AND pd.practice_status = 'YES' 
                        AND pd.deceased_stat != 'YES' 
                        AND (pd.deceased_date = '' OR pd.deceased_date = '0000-00-00 00:00:00' ) 
                        AND ope.pc_catid IN ($enc_value) $query ORDER BY pd.lname, pd.fname";  



                    $stmt = $db->prepare($sql) ;
//                    $stmt->bindParam("loginUserId", $loginUserId); 
//                    $stmt->bindParam("day", $day);   			
                    //$stmt->bindParam("visitCategory", $visitCategory); 			
                    $stmt->execute();
                    $patients = $stmt->fetchAll(PDO::FETCH_OBJ);

                }
            }
            if($patients)
            {
                //returns patients appointment list
                $patientres = json_encode($patients); 
                echo $patientresult = GibberishAES::enc($patientres, $key);
//                //die();
            }
            else
            {    
                //echo 'No Patient available';
                $patientres = '[{"id":"0"}]';
                echo $patientresult = GibberishAES::enc($patientres, $key);
//                //die();
            }
        } 
        catch(PDOException $e) 
        {
            
            $patientres = '{"error":{"text":'. $e->getMessage() .'}}'; 
            echo $patientresult = GibberishAES::enc($patientres, $key);
//            //die();
        }
}

//// method to get list of all patients with filters
function getAllPatients($value,$seen)
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
            
            $get_sql = "select notes from list_options WHERE list_id = 'Mobile_All_Patients_Filters'  AND option_id = '$value'";      
            $set_stmt = $db->prepare($get_sql) ;
            $set_stmt->execute();                       

            $get_value = $set_stmt->fetchAll(PDO::FETCH_OBJ);   
            
            if(!empty($get_value)){
                $query  = " WHERE ".$get_value[0]->notes;
            }
            
            $get_sql2 = "select notes from list_options WHERE list_id = 'Mobile_Appointment_Due_Filters'  AND option_id = '$seen'";      
            $set_stmt2 = $db->prepare($get_sql2) ;
            $set_stmt2->execute();                       

            $get_value2 = $set_stmt2->fetchAll(PDO::FETCH_OBJ);   
            $joinQuery = '';
            if(!empty($get_value2)){
                $joinQuery = " INNER JOIN form_encounter f ON f.pid = p.pid ";
                if(empty($get_value))
                    $query  .= " WHERE f.".$get_value2[0]->notes;
                else
                    $query  .= " AND f.".$get_value2[0]->notes;
            }
            
            $sql = "SELECT DISTINCT p. pid,title,fname,lname, DATE_FORMAT(DOB,'%m-%d-%Y') AS DOB,DATE_FORMAT(FROM_DAYS(DATEDIFF(CURDATE(),DOB)), '%Y')+0 AS age,if (sex = 'Female' ,'F','M' ) as sex,street,city,state,country_code,postal_code,phone_home,phone_biz,phone_contact,phone_cell,contact_relationship as emergency_contact, phone_contact as emergency_phone
                from patient_data p $joinQuery $query order by lname, fname limit 100 "; 
            
            $stmt = $db->prepare($sql) ;
            $stmt->execute();                       
             
            $patients = $stmt->fetchAll(PDO::FETCH_OBJ);  
            
            
//            echo "<pre>"; print_r($patients); echo "</pre>";
            
            $patientsdata['PatientData'] = $patients;


            if($patientsdata && count($patientsdata) != 0)
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
function getPatientsByProvider($loginProvderId,$value,$seen)
{
    $apikey = 'rotcoderaclla';
    // The default key size is 256 bits.
    $old_key_size = GibberishAES::size();
    GibberishAES::size(256);    
 
        try 
        {
            $db = getConnection();
            $query = '';
            $patientsdata = array();
            
            $get_sql = "select notes from list_options WHERE list_id = 'Mobile_My_Patients_Filters'  AND option_id = '$value'";      
            $db->query( "SET NAMES utf8"); 
            $set_stmt = $db->prepare($get_sql) ;
            $set_stmt->execute();                       

            $get_value = $set_stmt->fetchAll(PDO::FETCH_OBJ);   
            
            if(!empty($get_value)){
                $query  = " AND ".$get_value[0]->notes;
            }
            
            $get_sql2 = "select notes from list_options WHERE list_id = 'Mobile_Appointment_Due_Filters'  AND option_id = '$seen'";      
            $set_stmt2 = $db->prepare($get_sql2) ;
            $set_stmt2->execute();                       

            $get_value2 = $set_stmt2->fetchAll(PDO::FETCH_OBJ);   
            $joinQuery = '';
            if(!empty($get_value2)){
                $joinQuery = " INNER JOIN form_encounter f ON f.pid = p.pid ";
                if(empty($get_value))
                    $query  = " WHERE f.".$get_value2[0]->notes;
                else
                    $query  .= " AND f.".$get_value2[0]->notes;
            }
            
            $sql = "SELECT DISTINCT p.pid,title,fname,lname, DATE_FORMAT(DOB,'%m-%d-%Y') AS DOB,DATE_FORMAT(FROM_DAYS(DATEDIFF(CURDATE(),DOB)), '%Y')+0 AS age,if (sex = 'Female' ,'F','M' ) as sex,street,city,state,country_code,postal_code,phone_home,phone_biz,phone_contact,phone_cell,contact_relationship as emergency_contact, phone_contact as emergency_phone
                from patient_data p
                INNER JOIN form_encounter f ON f.pid = p.pid 
                where f.rendering_provider =:loginProvderId $query order by lname, fname  limit 100"; 
            $db->query( "SET NAMES utf8"); 
            $stmt = $db->prepare($sql) ;
            $stmt->bindParam("loginProvderId", $loginProvderId);            
            $stmt->execute();
            $patients = $stmt->fetchAll(PDO::FETCH_OBJ);   
  
            
//            echo "<pre>"; print_r($patients); echo "</pre>";
            if(!empty($patients))
                $patientsdata['PatientData'] = $patients;
//            $patientsdata['FilterData'] = $fields;
            if($patientsdata)
            {
                $patientres = json_encode($patientsdata); 
                echo $patientresult =  GibberishAES::enc($patientres, $apikey);
            }
            else
            {   
                $patientres = '{"id":"0"}';
                echo $patientsresult = GibberishAES::enc($patientres, $apikey);
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
function check_data_available3($newdemoArray,$type)
{
	
	if($newdemoArray['DataAvailable'] == "YES")
	{			
		return $newdemoArray;
	}
	else
	{
		return json_decode('{"DataAvailable":"NO","type":"'.$type.'"}');
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
       
	try 
        {
            $db = getConnection();
                $patient_query = "SELECT fe.reason,f.encounter,fe.date, CONCAT(u.fname,' ', u.mname,' ', u.lname) as Provider FROM form_encounter AS fe JOIN forms AS f ON f.pid = fe.pid AND f.encounter = fe.encounter AND f.formdir = 'newpatient' AND f.deleted = 0 LEFT JOIN users AS u ON u.id = fe.provider_id WHERE fe.pid = $patientId ORDER BY fe.date DESC, fe.id DESC LIMIT 0,20 ";
                $stmt = $db->prepare($patient_query) ;
                $db->query("SET SQL_BIG_SELECTS=1"); 
                $stmt->execute();
                $encounterslist = $stmt->fetchAll(PDO::FETCH_OBJ);
                // hema
                $codearray= array();
                if(!empty($encounterslist)){
                    for($i=0; $i< count($encounterslist); $i++){
    //                    $m = 0;
                        $icd_codes= '';
                        $encounter_id =  $encounterslist[$i]-> encounter;
                        $e_date =  $encounterslist[$i]-> date;
                        $result = "select code_type,code,code_text from billing where encounter = $encounter_id and pid=$patientId and activity=1 order by code_type, date ASC" ;

                        $all=array();

                        $stmti = $db->prepare($result) ;
                        $db->query("SET SQL_BIG_SELECTS=1"); 
                        $stmti->execute();
                        $datai = $stmti->fetchAll(PDO::FETCH_OBJ);
                        if(!empty($datai)){
                            for($m = 0; $m < count($datai); $m++){
                            $icd_codes .= $datai[$m]->code_type." - ".$datai[$m]->code."(". $datai[$m]->code_text.").";
                            }
                        }
                        
                        // forms 
                        $forms = '';
                        $getforms = "select DISTINCT form_name from forms where encounter = $encounter_id and pid=$patientId AND deleted = 0 ORDER BY FIND_IN_SET(formdir,'vitals') DESC, date DESC ";
                        $stmtform = $db->prepare($getforms) ;
                        $db->query("SET SQL_BIG_SELECTS=1"); 
                        $stmtform->execute();
                        $setform = $stmtform->fetchAll(PDO::FETCH_OBJ);
                        if(!empty($setform)){
                            for($m = 0; $m < count($setform); $m++){
                                if($setform[$m]->form_name  !== "New Patient Encounter")
                                    $forms .= $setform[$m]->form_name.",";
                            }
                        }
                        if(!empty($forms))
                            $formstring = rtrim($forms,",");
                        else
                            $formstring = '';
                        $encounterslist[$i]->Reason_or_Forms  = $encounterslist[$i]->reason ."<br />".$formstring;
                        // issue
                        $issuequery = "SELECT lists.type, lists.title, lists.begdate " .
                                    "FROM issue_encounter, lists WHERE " .
                                    "issue_encounter.pid = $patientId AND " .
                                    "issue_encounter.encounter = $encounter_id AND " .
                                    "lists.id = issue_encounter.list_id " .
                                    "ORDER BY lists.type, lists.begdate";
                        $stmtissue = $db->prepare($issuequery) ;
                        $db->query("SET SQL_BIG_SELECTS=1"); 
                        $stmtissue->execute();
                        $dataissue = $stmtissue->fetchAll(PDO::FETCH_OBJ);
                        if(!empty($dataissue)){
                            for($m = 0; $m < count($dataissue); $m++){
                            $encounterslist[$i]->issue= $dataissue[$m]->type." - ".$dataissue[$m]->title."(". $dataissue[$m]->begdate.").";
                            }
                        }
                        // insurance data 
                        $encounterslist[$i]-> Insurance = '';
                        foreach (array('primary','secondary','tertiary') as $instype) {
                            $tmp2 = "select insd.*, DATE_FORMAT(subscriber_DOB,'%m/%d/%Y') as subscriber_DOB, ic.name as provider_name from insurance_data as insd " .
                               "left join insurance_companies as ic on ic.id = provider " .
                               "where pid = $patientId and date_format(date,'%Y-%m-%d') <= '$e_date' and " .
                               "type='$instype' order by date DESC limit 1";
                            $stmttemp = $db->prepare($tmp2) ; 
                            $stmttemp->execute();
                            $tmp = $stmttemp->fetchAll(PDO::FETCH_OBJ);
                            if (empty($tmp[0]->provider_name)) break;
                                $encounterslist[$i]-> Insurance .= "<b> ".ucwords($instype)." :</b>".$tmp[0]->provider_name. ". ";//$insarr[] = $tmp[0];
                        }
                       
                        $codes = array();
                        $keysuff1 = 1000;
                        $keysuff2 = 5000;
                        $res =  "SELECT " .
                            "date, code_type, code, modifier, code_text, fee " .
                            "FROM billing WHERE " .
                            "pid = $patientId AND encounter = $encounter_id AND " .
                            "activity = 1 AND fee != 0.00 ORDER BY id";
                        $stmt2 = $db->prepare($res) ;
                        $db->query("SET SQL_BIG_SELECTS=1"); 
                        $stmt2->execute();
                        $data2 = $stmt2->fetchAll(PDO::FETCH_OBJ);
                        // Get charges from product sales.
                             $query = "SELECT s.drug_id, s.sale_date, s.fee, s.quantity " .
                              "FROM drug_sales AS s " .
                              "WHERE " .
                              "s.pid = $patientId AND s.encounter = $encounter_id AND s.fee != 0 " .
                              "ORDER BY s.sale_id";
                            $stmt3 = $db->prepare($query) ;
                            $db->query("SET SQL_BIG_SELECTS=1"); 
                            $stmt3->execute();
                            $data3 = $stmt3->fetchAll(PDO::FETCH_OBJ);
                            for($k=0; $k< count($data3); $k++){
                                if(!empty($data3[$k])){
                                    $amount = sprintf('%01.2f', $data3[$k]->fee);
                                    $code = 'PROD:' . $data3[$k]->drug_id;
                                    $codes[$code]['chg'] += $amount;
                                    $codes[$code]['bal'] += $amount;
                                    // Add the details if they want 'em.
                                    if ($with_detail) {
                                        if (! $codes[$code]['dtl']) $codes[$code]['dtl'] = array();
                                        $tmp = array();
                                        $tmp['chg'] = $amount;
                                        $tmpkey = "          " . $keysuff1++;
                                        $codes[$code]['dtl'][$tmpkey] = $tmp;
                                    }
                                }
                            }
                     $encounterslist[$i]->codes =  $icd_codes;
                            
                    }

                }
//                 echo "<pre>";print_r($encounterslist); echo "</pre>"; 
            if($encounterslist)
            {
               
                $encountersres = json_encode($encounterslist); 
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
            try 
            {
                $db = getConnection();
                $patient_query = "SELECT fe.reason,fe.encounter,fe.date, CONCAT(u.fname,' ', u.mname,' ', u.lname) as Provider FROM form_encounter AS fe JOIN forms AS f ON f.pid = fe.pid AND f.encounter = fe.encounter AND f.formdir = 'newpatient' AND f.deleted = 0 LEFT JOIN users AS u ON u.id = fe.provider_id WHERE fe.pid = $patientId ORDER BY fe.date DESC, fe.id DESC LIMIT 0,20 ";
                $stmt = $db->prepare($patient_query) ;
                $db->query("SET SQL_BIG_SELECTS=1"); 
                $stmt->execute();
                $encounterslist = $stmt->fetchAll(PDO::FETCH_OBJ);
                // hema
                $codearray= array();
                if(!empty($encounterslist)){
                    for($i=0; $i< count($encounterslist); $i++){
    //                    $m = 0;
                        $icd_codes= '';
                        $encounter_id =  $encounterslist[$i]-> encounter;
                        $e_date =  $encounterslist[$i]-> date;
                        $result = "select code_type,code,code_text from billing where encounter = $encounter_id and pid=$patientId and activity=1 order by code_type, date ASC" ;

                        $all=array();

                        $stmti = $db->prepare($result) ;
                        $db->query("SET SQL_BIG_SELECTS=1"); 
                        $stmti->execute();
                        $datai = $stmti->fetchAll(PDO::FETCH_OBJ);
                        if(!empty($datai)){
                            for($m = 0; $m < count($datai); $m++){
                            $icd_codes .= $datai[$m]->code_type." - ".$datai[$m]->code."(". $datai[$m]->code_text.").";
                            }
                        }
                        // insurance data 
                        $encounterslist[$i]-> Insurance = '';
                        foreach (array('primary','secondary','tertiary') as $instype) {
                            $tmp2 = "select insd.*, DATE_FORMAT(subscriber_DOB,'%m/%d/%Y') as subscriber_DOB, ic.name as provider_name from insurance_data as insd " .
                               "left join insurance_companies as ic on ic.id = provider " .
                               "where pid = $patientId and date_format(date,'%Y-%m-%d') <= '$e_date' and " .
                               "type='$instype' order by date DESC limit 1";
                            $stmttemp = $db->prepare($tmp2) ; 
                            $stmttemp->execute();
                            $tmp = $stmttemp->fetchAll(PDO::FETCH_OBJ);
                            if (empty($tmp[0]->provider_name)) break;
                                $encounterslist[$i]-> Insurance .= "<b> ".ucwords($instype)." :</b>".$tmp[0]->provider_name. ". ";//$insarr[] = $tmp[0];
                        }
                       
                        $codes = array();
                        $keysuff1 = 1000;
                        $keysuff2 = 5000;
                        $res =  "SELECT " .
                            "date, code_type, code, modifier, code_text, fee " .
                            "FROM billing WHERE " .
                            "pid = $patientId AND encounter = $encounter_id AND " .
                            "activity = 1 AND fee != 0.00 ORDER BY id";
                        $stmt2 = $db->prepare($res) ;
                        $db->query("SET SQL_BIG_SELECTS=1"); 
                        $stmt2->execute();
                        $data2 = $stmt2->fetchAll(PDO::FETCH_OBJ);
                       // Get charges from services.
                        for($j=0; $j< count($data2); $j++){
                            $with_detail = true;
                            if(!empty($data2[$j])){
                              $amount = sprintf('%01.2f', $data2[$j]->fee);

                                $code = $data2[$j]->code;
                                if (! $code) $code = "Unknown";
                                if ($data2[$j]->modifier) $code .= ':' . $data2[$j]->modifier;
                                $codes[$code] = array();
    //                            $codes = $codes[$code];
                                $codes[$code]['chg'] =  (isset($codes[$code]['chg'])? $codes[$code]['chg'] : 0) + $amount;
                                $codes[$code]['bal'] = (isset($codes[$code]['bal'])? $codes[$code]['bal'] : 0) + $amount;

                              // Pass the code type, code and code_text fields
                              // Although not all used yet, useful information
                              // to improve the statement reporting etc.
                              $codes[$code]['code_type'] = $data2[$j]->code_type;
                              $codes[$code]['code_value'] = $data2[$j]->code;
                              $codes[$code]['modifier'] = $data2[$j]->modifier;
                              $codes[$code]['code_text'] = $data2[$j]->code_text;

                              // Add the details if they want 'em.
                              if ($with_detail) {
                                if (isset( $codes[$code]['dtl']))
                                    $codes[$code]['dtl'] = array();
                                $tmp = array();
                                $tmp['chg'] = $amount;
                                $tmpkey = "          " . $keysuff1++;
                                $codes[$code]['dtl'][$tmpkey] = $tmp;
                              }
                            }
                        }
                            // Get charges from product sales.
                             $query = "SELECT s.drug_id, s.sale_date, s.fee, s.quantity " .
                              "FROM drug_sales AS s " .
                              "WHERE " .
                              "s.pid = $patientId AND s.encounter = $encounter_id AND s.fee != 0 " .
                              "ORDER BY s.sale_id";
                            $stmt3 = $db->prepare($query) ;
                            $db->query("SET SQL_BIG_SELECTS=1"); 
                            $stmt3->execute();
                            $data3 = $stmt3->fetchAll(PDO::FETCH_OBJ);
                            for($k=0; $k< count($data3); $k++){
                                if(!empty($data3[$k])){
                                    $amount = sprintf('%01.2f', $data3[$k]->fee);
                                    $code = 'PROD:' . $data3[$k]->drug_id;
                                    $codes[$code]['chg'] += $amount;
                                    $codes[$code]['bal'] += $amount;
                                    // Add the details if they want 'em.
                                    if ($with_detail) {
                                        if (! $codes[$code]['dtl']) $codes[$code]['dtl'] = array();
                                        $tmp = array();
                                        $tmp['chg'] = $amount;
                                        $tmpkey = "          " . $keysuff1++;
                                        $codes[$code]['dtl'][$tmpkey] = $tmp;
                                    }
                                }
                            }
                            $res = "SELECT " .
                                "a.code_type, a.code, a.modifier, a.memo, a.payer_type, a.adj_amount, a.pay_amount, a.reason_code, " .
                                "a.post_time, a.session_id, a.sequence_no, a.account_code, " .
                                "s.payer_id, s.reference, s.check_date, s.deposit_date " .
                                ",i.name " .
                                "FROM ar_activity AS a " .
                                "LEFT OUTER JOIN ar_session AS s ON s.session_id = a.session_id " .
                                "LEFT OUTER JOIN insurance_companies AS i ON i.id = s.payer_id " .
                                "WHERE a.pid = $patientId AND a.encounter = $encounter_id " .
                                "ORDER BY s.check_date, a.sequence_no";
                            $stmt4 = $db->prepare($res) ;
                            $db->query("SET SQL_BIG_SELECTS=1"); 
                            $stmt4->execute();
                            $data4 = $stmt4->fetchAll(PDO::FETCH_OBJ);
                            for($l=0; $l< count($data4); $l++){
                                if(!empty($data4[$l])){
    //                          while ($row = sqlFetchArray($res)) {
                                    $code = $data4[$l]->code;
                                    if (! $code) $code = "Unknown";
                                    if ($data4[$l]->modifier) $code .= ':' . $data4[$l]->modifier;
                                    $ins_id = 0 + $data4[$l]->payer_id;
                                    $codes[$code]['bal'] -= $data4[$l]->pay_amount;
                                    $codes[$code]['bal'] -= $data4[$l]->adj_amount;
                                    $codes[$code]['chg'] -= $data4[$l]->adj_amount;
                                    $codes[$code]['adj'] =  (isset($codes[$code]['adj']) ? $codes[$code]['adj'] : 0) + $data4[$l]->adj_amount;
                                    if ($ins_id) $codes[$code]['ins'] = $ins_id;
                                    // Add the details if they want 'em.
                                    if ($with_detail) {
                                      if (! $codes[$code]['dtl']) $codes[$code]['dtl'] = array();
                                      $tmp = array();
                                      $paydate = empty($data4[$l]->deposit_date) ? substr($data4[$l]->post_time, 0, 10) : $data4[$l]->deposit_date;
                                      if ($data4[$l]->pay_amount != 0) $tmp['pmt'] = $data4[$l]->pay_amount;
                                      if ( isset($data4[$l]->reason_code ) ) {
                                        $tmp['msp'] = $data4[$l]->reason_code;
                                      }
                                      if ($data4[$l]->adj_amount != 0 || $data4[$l]->pay_amount == 0) {
                                        $tmp['chg'] = 0 - $data4[$l]->adj_amount;
                                        // $tmp['rsn'] = (empty($row['memo']) || empty($row['session_id'])) ? 'Unknown adjustment' : $row['memo'];
                                        $tmp['rsn'] = empty($data4[$l]->memo) ? 'Unknown adjustment' : $data4[$l]->memo;
                                        $tmpkey = $paydate . $keysuff1++;
                                      }
                                      else {
                                        $tmpkey = $paydate . $keysuff2++;
                                      }
                                      if ($data4[$l]->account_code == "PCP") {
                                        //copay
                                        $tmp['src'] = 'Pt Paid';
                                      }
                                      else {
                                        $tmp['src'] = empty($data4[$l]->session_id) ? $data4[$l]->memo : $data4[$l]->reference;
                                      }
                                      $tmp['insurance_company'] = substr($data4[$l]->name, 0, 10);
                                      if ($ins_id) $tmp['ins'] = $ins_id;
                                      $tmp['plv'] = $data4[$l]->payer_type;
                                      $tmp['arseq'] = $data4[$l]->sequence_no;
                                      $codes[$code]['dtl'][$tmpkey] = $tmp;
                                    }
                                }
                            }
                     $encounterslist[$i]->code =  $codes ;
                     $encounterslist[$i]->codes =  $icd_codes;
                    }

                }
                if(!empty($encounterslist)){
                    for($i = 0; $i< count($encounterslist); $i++){
                        $encounterslist[$i]->Codes = '';
                        foreach($encounterslist[$i]->code as $key => $value){
                            $adj = isset($value['adj'] ) ? $value['adj'] : 0;
                            $encounterslist[$i]->Codes  .= "<b><u>".$value['code_type']."-".$key."</u> Chg:</b> ".($value['chg']+$adj).",<b> Paid:</b> ".($value['chg']-$value['bal']). ",<b> Adj: </b>".$adj.", <b>Balance : </b>".$value['bal']."<br>";
                        }
                        unset($encounterslist[$i]->code);
                    }
                }
//                 echo "<pre>";print_r($encounterslist);echo "</pre>";
                // hema

                if($encounterslist)
                {
                   $encountersres = json_encode($encounterslist); 
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
        $body = addslashes($msgArray['body']);
        $title = $msgArray['title'];
        $msg_status = $msgArray['msg_status'];
        $previous_msg = addslashes($msgArray['previous_msg']);
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
                        insertMobileLog('update',$username,$sql,$pid,'','Update Message',1);
//                            $res = '[{"id":"1"}]';
//                            echo $result = GibberishAES::enc($res, $key);
                    }
                    else
                    {
                        $resultant_error = 1;
                        insertMobileLog('update',$username,$sql,$pid,'','Update Message - Failed',0);
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
                            insertMobileLog('insert',$username,$sql,$pid,'','Create Message',1);
//                                echo $res = '[{"id":"1"}]';
//                                echo $result = GibberishAES::enc($res, $key);
                        }
                        else
                        {
                            $resultant_error = 1;
                            insertMobileLog('insert',$username,$sql,$pid,'','Create Message - Failed',0);
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
        if($username):
            $user = $username ;
        else:
            $user = $loginProvderId;
        endif;
        insertMobileLog('insert/update',$user,$error,$pid,'','Create Message - Query Failed',0);
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
function getPatientsByFacility($fid,$uid,$value,$seen)
{
    $key = 'rotcoderaclla';
    // The default key size is 256 bits.
    $old_key_size = GibberishAES::size();
    GibberishAES::size(256);
    $db = getConnection();
    $get_sql = "select notes from list_options WHERE list_id = 'Mobile_Facility_Filters'  AND option_id = '$value'";      
    $set_stmt = $db->prepare($get_sql) ;
    $set_stmt->execute();                       

    $get_value = $set_stmt->fetchAll(PDO::FETCH_OBJ);   
    $query = '';
    if(!empty($get_value)){
        $query  = " AND ".$get_value[0]->notes;
    } 
    
    $get_sql2 = "select notes from list_options WHERE list_id = 'Mobile_Appointment_Due_Filters'  AND option_id = '$seen'";      
    $set_stmt2 = $db->prepare($get_sql2) ;
    $set_stmt2->execute();                       

    $get_value2 = $set_stmt2->fetchAll(PDO::FETCH_OBJ);   
    $joinQuery = '';
    if(!empty($get_value2)){
        $joinQuery = " INNER JOIN form_encounter f ON f.pid = pd.pid ";
        $query  .= " AND f.".$get_value2[0]->notes;
    }
    
    $get_accesssql = "SELECT provider_plist_links FROM tbl_user_custom_attr_1to1 WHERE userid= '$uid'";
    $get_accessstmt = $db->prepare($get_accesssql) ;
    $get_accessstmt->execute();                       

    $setaccess = $get_accessstmt->fetchAll(PDO::FETCH_OBJ);
    $useraccess = 0;
    if($setaccess ){
        if (strpos($setaccess[0]->provider_plist_links,'create_enc') !== false) {
            $useraccess = 1;
        }
    }
    
    if($fid == 0){
        $sql="select DISTINCT pd.pid, pd.title,pd.fname,pd.lname, DATE_FORMAT(pd.DOB,'%m-%d-%Y') AS DOB, '0' as id,
            DATE_FORMAT(FROM_DAYS(DATEDIFF(CURDATE(),pd.DOB)), '%Y')+0 AS age,if (pd.sex = 'Female' ,'F','M' ) as sex,
            pd.deceased_stat,pd.practice_status 
            from patient_data pd $joinQuery where pd.pid not in (SELECT DISTINCT(patientid) FROM tbl_patientfacility)  $query order by pd.lname, pd.fname limit 100";  
       
        try 
        {
            
            $stmt = $db->prepare($sql) ;
            $stmt->execute();
            $patients = $stmt->fetchAll(PDO::FETCH_OBJ);           

            if($patients && count($patients) != 0)
            {
                $patients['useraccess'] = $useraccess;
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
       $sql="SELECT DISTINCT tpf.patientid as pid, tpf.id, pd.title, pd.fname, pd.lname, DATE_FORMAT( pd.DOB,  '%m-%d-%Y' ) AS DOB, DATE_FORMAT( FROM_DAYS( DATEDIFF( CURDATE( ) , pd.DOB ) ) ,  '%Y' ) +0 AS age, IF( pd.sex =  'Female',  'F',  'M' ) AS sex
                FROM patient_data pd
                INNER JOIN tbl_patientfacility tpf ON pd.pid = tpf.patientid $joinQuery
                WHERE facility_isactive =  'YES'
                AND tpf.facilityid =$fid
                AND tpf.id
                IN (

                SELECT MAX( id ) 
                FROM tbl_patientfacility
                WHERE facility_isactive =  'YES' 
                GROUP BY patientid
                ) $query limit 100";  
        try 
        {
            $db = getConnection();
            $stmt = $db->prepare($sql) ;
            $stmt->execute();
            $patients = $stmt->fetchAll(PDO::FETCH_OBJ);           

            if($patients)
            {
                $patients['useraccess'] = $useraccess;
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
                fv.waist_circ as waist_circumference_inches,fv.head_circ as head_circumference_inches,fv.oxygen_saturation ,fv.O2source,fv.O2_flow_rate,fv.pain_scale
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
                 'waist_circumference_inches'=> '', 'head_circumference_inches'=> '','oxygen_saturation'=> '', 'date' =>$date , 'O2source'=> '','O2_flow_rate' => '','pain_scale' => '' );
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
        $apikey = 'rotcoderaclla';
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        
        $sqlGetMyPatientsByName="SELECT id,pid,title,fname,mname,lname
                                FROM patient_data
                                WHERE fname like '%$patientName%'
                                OR mname like '%$patientName%'
                                OR lname like '%$patientName%'
				OR CONCAT(fname,' ',lname) like '%$patientName%'
                                OR CONCAT(fname,' ',mname) like '%$patientName%'
                                AND providerID=$loginProviderId
                                AND  practice_status = 'YES' AND deceased_stat != 'YES' AND (deceased_date = '' OR deceased_date = '0000-00-00 00:00:00' ) 
                                ORDER BY fname,lname";

        $stmt = $db->prepare($sqlGetMyPatientsByName);
        $stmt->execute();
        $resGetMyPatientsByName = $stmt->fetchAll(PDO::FETCH_OBJ);
        
        if($resGetMyPatientsByName)
        {
            $res = json_encode($resGetMyPatientsByName); 
            echo $visitresult = GibberishAES::enc($res, $apikey);
        }
        else
        {   // Patient Not found
            $res = '[{"id":"0","msg":"Patient not found"}]';
            echo $visitresult = GibberishAES::enc($res, $apikey);
        }
                
    }
    catch(PDOException $e) 
    {
        $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo $visitresult = GibberishAES::enc($error, $apikey);
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
        $apptdatesult = array();
        $get_fuv = "SELECT visit_categories FROM tbl_allcare_facuservisit WHERE  `users`  REGEXP ('".":\"".$loginProvderId."\"')";
        $fuv_stmt = $db->prepare($get_fuv) ;
        $fuv_stmt->execute();
        $set_fuv = $fuv_stmt->fetchAll(PDO::FETCH_OBJ); 
        if(!empty($set_fuv)){
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
            
            foreach($apptdates as $app_key => $app_value){
                $apptdatesult[$app_key]['datevalue'] = date('m-d-Y', strtotime($app_value->datevalue));
            }
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
        $patientsreminder = editDynamicLayoutFunction ($patientId,'HIS',$group_name);
        $dataarray = $patientsreminder;
        
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
//        $dataarray[]['form_id'] = $form_id;
        if($group_name == 'Past Medical History'){
            $search = 'history_past_stat';
            $review_string = 're_up_patient1';
        }elseif($group_name ==  'Family History'){
            $search = 'family_history_stat';
            $review_string = 're_up_patient2';
        }elseif($group_name ==  'Primary Family Med Conditions'){
            $search = 'family_med_con_stat';
            $review_string = 're_up_patient3';
        }elseif($group_name ==  'Social History'){
            $search = 'history_social_stat';
            $review_string = 're_up_patient5';
        }elseif($group_name ==  'Tests and Exams'){
            $search = 'family_exam_test_stat';
            $review_string = 're_up_patient4';
        }
        
        if(!empty($search)){
            $search_group = '';
            if($group_name == 'Past Medical History'){
                $search_group = 'History Past';
            }elseif($group_name ==  'Family History'){
                $search_group = 'Family History';
            }elseif($group_name ==  'Primary Family Med Conditions'){
                $search_group = 'Family History Medical Conditi';
            }elseif($group_name ==  'Social History'){
                $search_group = 'History Social';
            }elseif($group_name ==  'Tests and Exams'){
                $search_group = 'Family History Exam Test';
            }    
            if(!empty($search_group)){
                    $sql7 = "select field_id, title,
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
                            END as data_type, seq, list_id ,group_name,description,max_length
                            from layout_options where form_id='LBF2' and uor <> 0 AND group_name LIKE'%".$search_group."' order by seq";
                    $db->query( "SET NAMES utf8"); 
                    $stmt7 = $db->prepare($sql7) ;
                    $stmt7->execute();  
                    $phistory_review = $stmt7->fetchAll(PDO::FETCH_OBJ); 

                    if(!empty($phistory_review)){
                        foreach($phistory_review as $flname2){
                            if(!empty($flname2->data_type)){
                                $data = array();
                                $data['label'] = $flname2->title;
                                $data['order'] = $flname2->seq;
                                $data['field_id'] = $flname2->field_id;
                                $data['data_type'] = $flname2->data_type;
                                $data['list_id'] = $flname2->list_id;
                                $data['placeholder'] = isset($flname2->description)? $flname2->description : '';
                                if($flname2->max_length == 0)
                                    $maxlength = '';
                                else
                                    $maxlength = $layout_fields2[$i]->max_length;
                                $data['data']['max_length']  = $maxlength;
                                $sqllabel = "SELECT field_value as ltitle FROM lbf_data WHERE field_id ='$flname2->field_id' AND form_id = '$form_id' ";
                                $db->query( "SET NAMES utf8"); 
                                $stmt4 = $db->prepare($sqllabel); 
                                $stmt4->execute();
                                $sqllabelres = $stmt4->fetchAll(PDO::FETCH_OBJ);

                                $autotextvalue = '';
                                $pvalue = '';
                                $sql_autotext = "select notes from list_options where option_id  = '". $flname2->field_id."' and list_id = 'AllCareEncFormsAutoText'";   
                                $db->query( "SET NAMES utf8");
                                $stmt_autotext = $db->prepare($sql_autotext) ;
                                $stmt_autotext->execute();                       

                                $autotext = $stmt_autotext->fetchAll(PDO::FETCH_OBJ);   
                                $data['selected_list'] = '';
                                $autotextvalue = isset($autotext[0]->notes)? $autotext[0]->notes: '';
                                if(!empty($sqllabelres)){
                                    if($sqllabelres[0]->ltitle != '')
                                        $data['selected_list'] = isset($sqllabelres[0]->ltitle) ?  $sqllabelres[0]->ltitle :$autotextvalue;
                                }else{
                                    $data['selected_list'] = $autotextvalue;
                                }

                                if($flname2->data_type == 'Smoking Status' || $flname2->data_type == 'Lifestyle status'):
                                    $selectedlistarray2 = array();
                                    $selectedlistarray2 = explode("|", isset($sqllabelres[0]->ltitle) ?  $sqllabelres[0]->ltitle :'');

                                    $data['statusText'] = 'textbox';
                                    $data['status']['statusheader'] = 'Status';
                                    $statustypes = array('current'=>'Current','quit' =>'Quit','never' => 'Never','not_applicable' => 'N/A');
                                    foreach($statustypes as $key => $stype){
                                        $stypelist = array();
                                        $stypelist['statuslabel']['sname'] = $stype;
                                        $stypelist['statuslabel']['svalue'] = $key;
                                        $stypelist['statuslabel']['statusControlType'] = 'radiobutton';
                                        for($i=0; $i<count($selectedlistarray2); $i++){
                                            if($selectedlistarray2[$i] == $key.$flname2->field_id ):
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
                                        $data['status'][] = $stypelist;
                                    }

                                    if($flname2->data_type == 'Smoking Status' && $flname2->list_id != ''):
                                        $lists = array();
                                        $listnamessql2 = "SELECT * FROM list_options WHERE list_id = '$flname2->list_id'";
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
                                        $data['smokingList'] = $lists;
                                        $data['smokingList']['listcontrolType'] = 'List box';
                                    elseif($flname2->data_type == 'Smoking Status' && $flname2->list_id == ''):
                                        $data['smokingList'] = array();
                                        $data['smokingList']['listcontrolType'] = 'List box';
                                   endif;
                                elseif($flname2->data_type == 'Exam results'):
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
                                    $data['status'][] = $textfield;
                                endif;



                                $list = array();
                                if($flname2->list_id != '' && $flname2->data_type != 'Smoking Status'):
                                    $listnamessql = "SELECT * FROM list_options WHERE list_id = '$flname2->list_id'";
                                    $db->query( "SET NAMES utf8"); 
                                    $stmt3 = $db->prepare($listnamessql); 
                                    $stmt3->execute();
                                    $listnamesresult = $stmt3->fetchAll(PDO::FETCH_OBJ);
                                    $selectedlistarray = array();
                                    $selectedlistarray = explode("|", isset($sqllabelres[0]->ltitle)? $sqllabelres[0]->ltitle:'');
                                    if($flname2->data_type == 'Exam results'):
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
                                            $list[] = $listarray;
                                        }
                                    endif;
                                    $data['list'] = $list;

                                else:
    //                                $data['data']['list'] = $list;
                                    // hema
                                if($flname2->data_type == 'Static Text'){
                                           $data['selected_list'] = $flname2->description;
                                   }else if($flname2->data_type == 'Providers' || $flname2->data_type == 'Providers NPI'){
                                       $sql3 = "SELECT id, CONCAT(fname, ' ',lname) as name FROM `users`  WHERE active=1 AND authorized=1 ORDER BY fname,lname ";
                                       $db->query( "SET NAMES utf8"); 
                                       $stmt3 = $db->prepare($sql3) ;
                                       $stmt3->execute();                       
                                       $getListData = $stmt3->fetchAll(PDO::FETCH_OBJ);  
                                       if(!empty($getListData)):
                                            $data2 = array();
                                            $i=0;
                                            foreach($getListData as $liste){
                                                $data2[$i]['option_id'] = $liste->id;
                                                $data2[$i]['option_title'] = $liste->name;
                                                $i++;
                                            }
                                       else:
                                           $data2 = '';
                                       endif;
                                       $data['list'] = $data2;
                                   }else if($flname2->data_type == 'Pharmacies'){
                                       $sql3 = "SELECT id, name FROM `pharmacies`   ORDER BY name";
                                       $db->query( "SET NAMES utf8"); 
                                       $stmt3 = $db->prepare($sql3) ;
                                       $stmt3->execute();                       
                                       $getListData = $stmt3->fetchAll(PDO::FETCH_OBJ);  
                                       if(!empty($getListData)):
                                            $data2 = array();
                                            $i=0;
                                            foreach($getListData as $liste){
                                                $data2[$i]['option_id'] = $liste->id;
                                                $data2[$i]['option_title'] = $liste->name;
                                                $i++;
                                            }
                                       else:
                                           $data2 = '';
                                       endif;
                                       $data['list'] = $data2;
                                   }else if($flname2->data_type == 'Organizations'){
                                        $sql3 = "SELECT id, fname, lname, organization, username FROM users WHERE active = 1 AND ( info IS NULL OR info NOT LIKE '%Inactive%' ) AND ( username = '' OR authorized = 1 ) ORDER BY organization, lname, fname";
                                        $db->query( "SET NAMES utf8"); 
                                        $stmt3 = $db->prepare($sql3) ;
                                        $stmt3->execute();                       
                                        $getListData = $stmt3->fetchAll(PDO::FETCH_OBJ);  
                                        if(!empty($getListData)):
                                             $data2 = array();
                                             $i=0;
                                             foreach($getListData as $liste){
                                                $uname = $liste->organization;
                                                if (empty($uname) || substr($uname, 0, 1) == '(') {
                                                    $uname = $liste->lname;
                                                    if ($liste->fname) $uname .= ", " . $liste->fname;
                                                }
    //                                            $data2[$i][$liste->id] = $uname;
                                                $data2[$i]['option_id'] = $liste->id;
                                                $data2[$i]['option_title'] = $uname;
                                                $i++;
                                             }
                                        else:
                                            $data2 = '';
                                        endif;
                                        $data['list'] = $data2;
                                   }else if($flname2->data_type == 'Patient allergies'){

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
                                       $data['selected_list'] = $data2;
                                   }else if($flname2->data_type == 'Facilities'){
                                       $sql3 = "SELECT id, name FROM facility ORDER BY name";
                                       $db->query( "SET NAMES utf8"); 
                                       $stmt3 = $db->prepare($sql3) ;
                                       $stmt3->execute();                       
                                       $getListData = $stmt3->fetchAll(PDO::FETCH_OBJ);  
                                       if(!empty($getListData)):
                                            $data2 = array();
                                            $i=0;
                                            foreach($getListData as $liste){
                                                $data2[$i]['option_id'] = $liste->id;
                                                $data2[$i]['option_title'] = $liste->name;
                                                $i++;
                                            }
                                       else:
                                           $data2 = '';
                                       endif;
                                      $data['list'] = $data2;
                                  }else if($flname2->data_type == 'Date Of Service'){
                                        $sql3 = "SELECT DATE_FORMAT( fe.date, '%Y-%m-%d' ) as date FROM `form_encounter` fe
                                            INNER JOIN forms f ON  fe.id = f.form_id AND fe.encounter = f.encounter AND fe.pid = f.pid
                                            WHERE fe.pid = $patientId ORDER BY date desc";
                                        $db->query( "SET NAMES utf8"); 
                                        $stmt3 = $db->prepare($sql3) ;
                                        $stmt3->execute();                       
                                        $getListData = $stmt3->fetchAll(PDO::FETCH_OBJ);  
                                        if(!empty($getListData)):
                                            $data2 = array();
                                            $i=0;
                                            foreach($getListData as $liste){
                                                $data2[$i]['option_id'] = $liste->date;
                                                $data2[$i]['option_title'] = $liste->date;
                                                $i++;
                                            }
                                        else:
                                            $data2 = '';
                                        endif;
                                        $data['list'] = $data2;
                                   }else if($flname2->data_type == 'Insurance Companies'){
                                       $sql3 = "SELECT id,  name FROM `insurance_companies`  ORDER BY name";
                                       $db->query( "SET NAMES utf8"); 
                                       $stmt3 = $db->prepare($sql3) ;
                                       $stmt3->execute();                       
                                       $getListData = $stmt3->fetchAll(PDO::FETCH_OBJ);  
                                       if(!empty($getListData)):
                                            $data2 = array();
                                            $i=0;
                                            foreach($getListData as $liste){
                                                $data2[$i]['option_id'] = $liste->id;
                                                $data2[$i]['option_title'] = $liste->name;
                                                $i++;
                                            }
                                       else:
                                           $data2 = '';
                                       endif;
                                       $data['list'] = $data2;
                                   }else if($flname2->data_type == 'Users'){
                                       $sql3 = "SELECT id, CONCAT(fname, ' ',lname) as name FROM `users`  WHERE username <> '' ORDER BY fname,lname ";
                                       $db->query( "SET NAMES utf8"); 
                                       $stmt3 = $db->prepare($sql3) ;
                                       $stmt3->execute();                       
                                       $getListData = $stmt3->fetchAll(PDO::FETCH_OBJ);  
                                       if(!empty($getListData)):
                                            $data2 = array();
                                            $i=0;
                                            foreach($getListData as $liste){
                                                $data2[$i]['option_id'] = $liste->id;
                                                $data2[$i]['option_title'] = $liste->name;
                                                $i++;
                                            }
                                       else:
                                           $data2 = '';
                                       endif;
                                       $data['list'] = $data2;
                                   }

                               /* ========== */
                                endif;
                                $dataarray['lbf']['form_id'] = $form_id;
                                $dataarray['lbf'][] = $data;
                            }
                        }
                   }
            }
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
function editDynamicLayoutFunction ($patientId,$form_name,$group_name){
    $db = getConnection();    
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
                      WHEN 38 THEN  'Users'
                      WHEN 39 THEN  'DateTime'
                    END as data_type, seq, list_id ,group_name,description,max_length,   CASE uor
                                WHEN 1 THEN 'Optional'
                                WHEN 2 THEN 'Required'
                                END as isRequired
                    from layout_options where form_id='$form_name' and uor <> 0 AND group_name LIKE'%".$group_name."' order by seq";
        $db->query( "SET NAMES utf8"); 
        $stmt2 = $db->prepare($fieldnames); 
        $stmt2->execute();
        $layout_fields2 = $stmt2->fetchAll(PDO::FETCH_OBJ);
        for($i=0; $i< count($layout_fields2); $i++){
            if(!empty($layout_fields2[$i]->data_type)){
                $sql = "select `".$layout_fields2[$i]->field_id."` from history_data WHERE pid=$patientId order by date desc limit 1 ";   

                $db->query( "SET NAMES utf8");
                $stmt = $db->prepare($sql) ;
                $stmt->execute();                       

                $patientsreminders = $stmt->fetchAll(PDO::FETCH_NUM);   
                $pvalue = isset($patientsreminders[0][0])? $patientsreminders[0][0]: '';

                $patientsreminder[$i]['data']['field_id'] = $layout_fields2[$i]->field_id;
                $patientsreminder[$i]['data']['data_type'] =  $layout_fields2[$i]->data_type;
                $patientsreminder[$i]['data']['isRequired'] =  $layout_fields2[$i]->isRequired;
                $patientsreminder[$i]['data']['label']  = $layout_fields2[$i]->title;
                $patientsreminder[$i]['data']['list_id']  = $layout_fields2[$i]->list_id;
                $patientsreminder[$i]['data']['order']  = $layout_fields2[$i]->seq;
                $patientsreminder[$i]['data']['placeholder']  = $layout_fields2[$i]->description;
                if($layout_fields2[$i]->max_length == 0)
                    $maxlength = '';
                else
                    $maxlength = $layout_fields2[$i]->max_length;
                $patientsreminder[$i]['data']['max_length']  = $maxlength;
                $get_title2 = "SELECT option_id, title FROM list_options WHERE list_id = '".$layout_fields2[$i]->list_id."' order by seq";
                $db->query( "SET NAMES utf8");
                $title_stmt2 = $db->prepare($get_title2) ;
                $title_stmt2->execute();                       

                $settitle2 = $title_stmt2->fetchAll(PDO::FETCH_OBJ);
                $titles= array();
                if(!empty($settitle2)){
                    for($k = 0; $k< count($settitle2); $k++){
                        $titles[$k]['option_id'] = $settitle2[$k]->option_id;
                        $titles[$k]['option_title'] = $settitle2[$k]->title;
                    }
                }
                $patientsreminder[$i]['data']['list']  = $titles;
                if($layout_fields2[$i]->list_id != ''){
                    $patientsreminder[$i]['data']['selected_list'] = $pvalue;
                }else{
                    if($layout_fields2[$i]->data_type == 'Static Text'){
                    $patientsreminder[$i]['data']['selected_list'] = $layout_fields2[$i]->description;
                }else if($layout_fields2[$i]->data_type == 'Providers' || $layout_fields2[$i]->data_type == 'Providers NPI'){
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
                            $data2[$j]['option_title'] = $list->name;
                            $j++;
                        }
                    else:
                        $data2 = '';
                    endif;

                    $patientsreminder[$i]['data']['list']  = $data2;
                    $patientsreminder[$i]['data']['selected_list'] = $pvalue;
                }else if($layout_fields2[$i]->data_type == 'Pharmacies'){
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
                            $data2[$j]['option_title'] = $list->name;
                            $j++;
                        }
                    else:
                        $data2 = '';
                    endif;

                    $patientsreminder[$i]['data']['list']  = $data2;
                    $patientsreminder[$i]['data']['selected_list'] = $pvalue;
                }else if($layout_fields2[$i]->data_type == 'Organizations'){
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
                            $data2[$j]['option_title'] = $uname;
                            $j++;
                        }
                    else:
                        $data2 = '';
                    endif;

                    $patientsreminder[$i]['data']['list']  = $data2;
                    $patientsreminder[$i]['data']['selected_list'] = $pvalue;
                }else if($layout_fields2[$i]->data_type == 'Patient allergies'){
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
                    $patientsreminder[$i]['data']['selected_list'] = $data2;
                }else if($layout_fields2[$i]->data_type == 'Lifestyle status'){
                    $patientsreminder[$i]['data']['selected_list'] = $pvalue;
                }else if($layout_fields2[$i]->data_type == 'Facilities'){
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
                            $data2[$j]['option_title'] = $list->name;
                            $j++;
                        }
                    else:
                        $data2 = '';
                    endif;

                    $patientsreminder[$i]['data']['list']  = $data2;
                    $patientsreminder[$i]['data']['selected_list'] = $pvalue;
                }else if($layout_fields2[$i]->data_type == 'Date Of Service'){
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
                            $data2[$j]['option_title'] = $list->date;
                            $j++;
                        }
                    else:
                        $data2 = '';
                    endif;
                    $data['data']['list'] = $data2;
               }else if($layout_fields2[$i]->data_type == 'Insurance Companies'){
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
                            $data2[$j]['option_title'] = $list->name;
                            $j++;
                        }
                    else:
                        $data2 = '';
                    endif;

                    $patientsreminder[$i]['data']['list']  = $data2;
                    $patientsreminder[$i]['data']['selected_list'] = $pvalue;
                }else if($layout_fields2[$i]->data_type == 'Users'){
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
                            $data2[$j]['option_title'] = $list->name;
                            $j++;
                        }
                    else:
                        $data2 = '';
                    endif;

                    $patientsreminder[$i]['data']['list']  = $data2;
                    $patientsreminder[$i]['data']['selected_list'] = $pvalue;
                }else{
                    $patientsreminder[$i]['data']['selected_list'] = $pvalue;
                }
                }
            }
        }
        return $patientsreminder;
}
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
            $sql1 = "SELECT id,DATE_FORMAT( date, '%Y-%m-%d' ) as date,REPLACE(type,'_',' ') as type,title,begdate as Begin_Date,enddate as End_Date,returndate as Return_Date,(select title from list_options where option_id = occurrence and list_id = 'Occurrence') as occurrence,classification,referredby as Referred_By,extrainfo as Additional_Information,diagnosis,activity,comments,user,groupname,(select title from list_options where option_id = 'outcome' and list_id = 'Outcome') as outcome,destination,reinjury_id,injury_part,injury_type,injury_grade,reaction,external_allergyid,erx_source,erx_uploaded,DATE_FORMAT( modifydate, '%Y-%m-%d' ) as modified_Date  FROM lists WHERE pid = :patientId AND type = 'medical_problem' ORDER BY - ISNULL( enddate ) , begdate DESC";

            //To get Allergies
            $sql2 = "SELECT id,DATE_FORMAT( date, '%Y-%m-%d' ) as date,REPLACE(type,'_',' ') as type,title,begdate as Begin_Date,enddate as End_Date,returndate as Return_Date,(select title from list_options where option_id = occurrence and list_id = 'Occurrence') as occurrence,classification,referredby as Referred_By,extrainfo as Additional_Information,diagnosis,activity,comments,user,groupname,(select title from list_options where option_id = outcome and list_id = 'Outcome') as outcome,destination,reinjury_id,injury_part,injury_type,injury_grade,reaction,external_allergyid,erx_source,erx_uploaded,DATE_FORMAT( modifydate, '%Y-%m-%d' ) as modified_Date  FROM lists WHERE pid = :patientId AND type = 'allergy' ORDER BY - ISNULL( enddate ) , begdate DESC";

            //To get Medications
            $sql3 = "SELECT id,DATE_FORMAT( date, '%Y-%m-%d' ) as date,REPLACE(type,'_',' ') as type,title,begdate as Begin_Date,enddate as End_Date,returndate as Return_Date,(select title from list_options where option_id = occurrence and list_id = 'Occurrence') as occurrence,classification,referredby as Referred_By,extrainfo as Additional_Information,diagnosis,activity,comments,user,groupname,(select title from list_options where option_id = outcome and list_id = 'Outcome') as outcome,destination,reinjury_id,injury_part,injury_type,injury_grade,reaction,external_allergyid,erx_source,erx_uploaded,DATE_FORMAT( modifydate, '%Y-%m-%d' ) as modified_Date  FROM lists WHERE pid = :patientId AND type = 'medication' ORDER BY - ISNULL( enddate ) , begdate DESC";

            //To get Surgeries
            $sql4 = "SELECT id,DATE_FORMAT( date, '%Y-%m-%d' ) as date,REPLACE(type,'_',' ') as type,title,begdate as Begin_Date,enddate as End_Date,returndate as Return_Date,(select title from list_options where option_id = occurrence and list_id = 'Occurrence') as occurrence,classification,referredby as Referred_By,extrainfo as Additional_Information,diagnosis,activity,comments,user,groupname,(select title from list_options where option_id = outcome and list_id = 'Outcome') as outcome,destination,reinjury_id,injury_part,injury_type,injury_grade,reaction,external_allergyid,erx_source,erx_uploaded,DATE_FORMAT( modifydate, '%Y-%m-%d' ) as modified_Date FROM lists WHERE pid = :patientId AND type = 'surgery' ORDER BY - ISNULL( enddate ) , begdate DESC";

            //To get Dental Issues 
            $sql5 = "SELECT id,DATE_FORMAT( date, '%Y-%m-%d' ) as date,REPLACE(type,'_',' ') as type,title,begdate as Begin_Date,enddate as End_Date,returndate as Return_Date,(select title from list_options where option_id = occurrence and list_id = 'Occurrence') as occurrence,classification,referredby,extrainfo,diagnosis,activity,comments,user,groupname,(select title from list_options where option_id = outcome and list_id = 'Outcome') as outcome,destination,reinjury_id,injury_part,injury_type,injury_grade,reaction,external_allergyid,erx_source,erx_uploaded,DATE_FORMAT( modifydate, '%Y-%m-%d' ) as modified_Date FROM lists WHERE pid = :patientId AND type = 'dental' ORDER BY - ISNULL( enddate ) , begdate DESC";

            //To get Immunizations
            $sql6 = "select i1.id as id,  'immunization' AS 
                type , i1.immunization_id as immunization_id, i1.cvx_code as cvx_code, c.code_text_short as cvx_text, ".
             " if (i1.administered_date, concat(i1.administered_date,' - '), substring(i1.note,1,20)) as immunization_date ".
             " from immunizations i1 ".
             " left join code_types ct on ct.ct_key = 'CVX' ".
             " left join codes c on c.code_type = ct.ct_id AND i1.cvx_code = c.code ".
             " where i1.patient_id = :patientId ".
             " and i1.added_erroneously = 0".
             " order by i1.administered_date desc";

            //To get Prescriptions
            $sql7 = "select id,filled_by_id,pharmacy_id,date_added,date_modified,(select CONCAT(fname, ' ',lname) as Provider from users where id=provider_id) as Provider,start_date,drug,drug_id,rxnorm_drugcode,form,dosage,quantity,size,unit,route,substitute,refills,per_refill,filled_date,medication,note,active,site,prescriptionguid,erx_source,erx_uploaded,drug_info_erx from prescriptions where patient_id=:patientId and active='1' ";
            
            //To get DME Issues 
            $sql8 = "SELECT id,DATE_FORMAT( date, '%Y-%m-%d' ) as date,type,title,begdate as Begin_Date,enddate as End_Date,returndate as Return_Date,(select title from list_options where option_id = occurrence and list_id = 'Occurrence') as occurrence,classification,referredby,extrainfo,diagnosis,activity,comments,user,groupname,(select title from list_options where option_id = outcome and list_id = 'Outcome') as outcome,destination,reinjury_id,injury_part,injury_type,injury_grade,reaction,external_allergyid,erx_source,erx_uploaded,DATE_FORMAT( modifydate, '%Y-%m-%d' ) as modified_Date FROM lists WHERE pid = :patientId AND type = 'DME' ORDER BY - ISNULL( enddate ) , begdate DESC";

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
//            $newdemo7=encode_demo($demo7,'');  
            $newdemo8=encode_demo($demo8);  

            $newdemo['Medical_Problems'] = check_data_available3($newdemo1,'medical_problem');
            $newdemo['Allergies'] = check_data_available3($newdemo2,'allergy');
            $newdemo['Medications'] = check_data_available3($newdemo3,'medication');
            $newdemo['Surgeries'] = check_data_available3($newdemo4,'surgery');
            $newdemo['Dental_Issues'] = check_data_available3($newdemo5,'dental');
            $newdemo['Immunizations'] = check_data_available3($newdemo6,'immunization');
//            $newdemo['Prescriptions'] = check_data_available($newdemo7);
            $newdemo['DME'] = check_data_available3($newdemo8,'DME');

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
	$db->query( "SET NAMES utf8"); 		
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
	$key = 'rotcoderaclla';
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
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
	$db->query( "SET NAMES utf8"); 		
	$stmt = $db->prepare($sql);
	$stmt->execute();
	$reminders = $stmt->fetchAll(PDO::FETCH_OBJ);           
	
	if($reminders)
	{
		$remiderres = json_encode($reminders); 
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
        $remiderres = '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo $reminderresult = GibberishAES::enc($remiderres, $key);
    }
}

// Reminders Completed

function setReminderAsProcessed($reminderId,$loginProvderId)
{

    try 
    {
        $db = getConnection();
        $key = 'rotcoderaclla';
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
	$rID=$reminderId;
	$userID = $loginProvderId;    
        $username2  = getUserName($userID);
        $username   = isset($username2[0]['username'])? $username2[0]['username'] : ''; 
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
		            $remiderres =  '[{"id":"1"}]';
                            echo $reminderresult = GibberishAES::enc($remiderres, $key);
                            insertMobileLog('update',$username,$sql1,'','',"Save Reminder Set as Forwarded",1);
		        }
		        else
		        {
		            $remiderres =  '[{"id":"-1","msg":"Update failed"}]';
                            echo $reminderresult = GibberishAES::enc($remiderres, $key);
                            insertMobileLog('update',$username,$sql1,'','',"Save Reminder Set as Forwarded - Failed",0);
		        }	
		      
		}
		else
		{
			$remiderres =  '[{"id":"0"}]';
                        echo $reminderresult = GibberishAES::enc($remiderres, $key);
                        insertMobileLog('update',$username,$sql1,'','',"Save Reminder Set as Forwarded - Failed",0);
		}
   	}
    }

    catch(PDOException $e) 
    {
        $remiderres =  '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo $reminderresult = GibberishAES::enc($remiderres, $key);
        insertMobileLog('update',$username,$patientres,'','',"Save Reminder Set as Forwarded - Query Failed",0);
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
        $key = 'rotcoderaclla';
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
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
			$string =  json_encode($messages); 
                        echo $stringenc = GibberishAES::enc($string, $key);
		}
		else
		{
			$string = '[{"id":"0"}]';
                        echo $stringenc = GibberishAES::enc($string, $key);
		}
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

function editPatientFacility(){
    try
    {
        $db = getConnection();
        $request = Slim::getInstance()->request();
        $apikey = 'rotcoderaclla';
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        $appres = GibberishAES::dec($request->getBody(), $apikey);
//        $appres =  GibberishAES::dec('U2FsdGVkX1/+Gi53GLrglmd3GWJnAUUG0dK+BmzX1672KfbQ+TNR7h/2gyLuzRtsx44cUyyxokQSwEWJxxOR6D5IokWQFd4pmnpibxMFCUqzwzch+iObEmPeRwbqWuPwy460NedSW7o5A4+4MV94OukpkU2QDpNVdSd6hFgeThz7hAwHE8ryWoY3O8A5RlS343wpDqRRedPSHMcapxKaFq+LSCeNwNgEkbwDaqYkFJZxVKik32AyuI2mEvVu8j5ttIaV8Q2cXta2+IYzrxUUh7ETRphW2IAtFPNDyjvPEKAJbtEWj8kfDl1GseEmYTYcJ9sogTRQulgOxgBbuuEvvEPIFuSEfk8OWk1IRSwC5+hiyaT+GHquVi1kg5qQJMSp9dk4ENXtTVuyGPI5qvLOf808YKxRNUMuzHMpdcyBhrk=', $apikey);
        $insertArray = json_decode($appres,TRUE);
//        echo "<pre>"; print_r($insertArray); echo "</pre>";
        $id             = $insertArray['id'];
        $pid            = $insertArray['pid'];
        $username       = $insertArray['username'];
        
        $data1 = $data2 = 0;
        $update_patient_data = $nameslist2  = $namesvalues2 = '';
        $fieldnames = "SELECT field_id
                        FROM layout_options WHERE form_id='SERVICEFAC' AND uor <> 0 ORDER BY seq";
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
                        $update_patient_data .= "`".$fvalue."`= '". addslashes($insertArray['data'][0][$fvalue])."',";
                    }
                }
            }
            if($id == 0){
                if(!empty($nameslist2) && !empty($namesvalues2)){
                    $nameslist2 = rtrim($nameslist2,',');
                    $namesvalues2 = rtrim($namesvalues2,',');
                    
                    $query = "  INSERT INTO tbl_patientfacility ( patientid, $nameslist2 ) VALUES ($pid, $namesvalues2)";
                    $stmtquery = $db->prepare($query); 
                    if($stmtquery->execute()){
                        $data1 = 1;
                        insertMobileLog('update',"$username",$query,$pid,'',"Edit Facility Data Screen", 1);
                    }else{
                        insertMobileLog('update',"$username",$query,$pid,'',"Edit Facility Data Screen - Failed", 0);
                    }
                }else{
                    $data1 = 1;
                }
            }else{
                if(!empty($update_patient_data)){
                    $update_patient_data = rtrim($update_patient_data,',');
                    $query = "  UPDATE tbl_patientfacility SET $update_patient_data WHERE patientid = $pid AND id = $id";
                    $stmtquery = $db->prepare($query); 
                    if($stmtquery->execute()){
                        $data1 = 1;
                        insertMobileLog('update',"$username",$query,$pid,'',"Edit Facility Data Screen", 1);
                    }else{
                        insertMobileLog('update',"$username",$query,$pid,'',"Edit Facility Data Screen - Failed", 0);
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
        insertMobileLog('update',"$username",$patientres,$pid,'',"Edit Facility Data Screen - Query Failed", 0);
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
                 
        $sql = "SELECT prescriptions.id, patient_id,(

            SELECT CONCAT( fname,  ' ', lname )  
            FROM patient_data
            WHERE pid = patient_id
            ) as Patient , filled_by_id, (

            SELECT name
            FROM pharmacies
            WHERE id = pharmacy_id
            ) AS Pharmacy_Name,  start_date as Starting_date, (

            SELECT CONCAT( fname,  ' ', lname )  
            FROM users
            WHERE id = provider_id
            ) AS provider, drug, quantity,size as Medicine_Units,(SELECT title FROM list_options WHERE list_id='drug_units' AND option_id = unit) as unit,  dosage, (SELECT title FROM list_options WHERE list_id='drug_form' AND option_id = form) as form, (SELECT title FROM list_options WHERE list_id='drug_route' AND option_id = route) as route, (SELECT title FROM list_options WHERE list_id='drug_interval' AND option_id = `interval`) as `interval`,  `refills`, per_refill as `#_of_tablets`,  note as Notes, if(medication=1, 'YES','NO') as Medication_Required, if(substitute = 1, 'YES','NO') as substitution_allowed 
            FROM prescriptions
            WHERE patient_id =$patientId
            AND active =  '1'";
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
    $username2  = getUserName($provider_id);
    $username   = isset($username2[0]['username'])? $username2[0]['username'] : ''; 
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
        $db->query( "SET NAMES utf8");
        $stmt_prov = $db->prepare($get_provider) ;
        $stmt_prov->execute(); 
        $get_provider = $stmt_prov->fetchAll(PDO::FETCH_OBJ);
        $provider = $get_provider[0]->username;
        // to get lbf and dictation forms
        $sql31 = "SELECT field_id, title FROM layout_options WHERE form_id = 'LBF1' and group_name LIKE '%Dictation'";
        $db->query( "SET NAMES utf8");
        $stmt31 = $db->prepare($sql31) ;
        $stmt31->execute(); 
        $patients31 = $stmt31->fetchAll(PDO::FETCH_OBJ);
        foreach($patients31 as $k_value){
            $check_string .= "field_id LIKE '".$k_value->field_id ."%' or ";
        }
        $check_string .= "field_id LIKE 'f2f%' ";
        // to lbf forms
        $sql2 = "select form_id from forms where encounter =$eid and pid =$pid and formdir = 'LBF2' and deleted = 0 ";  
        $db->query( "SET NAMES utf8");
        $stmt2 = $db->prepare($sql2) ;
        $stmt2->execute();                       
        $patients2 = $stmt2->fetchAll(PDO::FETCH_OBJ); 
        if(!empty($patients2)){
            $get_form_id_sql = "SELECT form_id,pid FROM forms WHERE formdir='LBF2' and deleted=0 and encounter = $copy_to_encounter";
            $db->query( "SET NAMES utf8");
            $get_stmt = $db->prepare($get_form_id_sql) ;
            $get_stmt->execute(); 
            $set_form_id_sql = $get_stmt->fetchAll(PDO::FETCH_OBJ); 
            if(!empty($set_form_id_sql)){
                $toid = $set_form_id_sql[0]->form_id;
                $get_form_data = "SELECT * FROM lbf_data WHERE form_id = '".$set_form_id_sql[0]->form_id."' and ($check_string)";
                $db->query( "SET NAMES utf8");
                $get_stmt2 = $db->prepare($get_form_data) ;
                $get_stmt2->execute(); 
                $set_form_data_sql = $get_stmt2->fetchAll(PDO::FETCH_OBJ); 
                if(!empty($set_form_data_sql)){
                    $delete_forms = "DELETE FROM lbf_data WHERE form_id = '".$set_form_id_sql[0]->form_id."' and ($check_string)";
                    $db->query( "SET NAMES utf8");
                    $get_stmt3 = $db->prepare($delete_forms) ;
                    $get_stmt3->execute(); 

                    // from encounter data
                     $get_provider = "SELECT * FROM form_encounter WHERE encounter = $copy_to_encounter";
                     $db->query( "SET NAMES utf8");
                     $get_stmt51 = $db->prepare($get_provider) ;
                     $get_stmt51->execute(); 
                     $get_provider_data = $get_stmt51->fetchAll(PDO::FETCH_OBJ); 
                            // echo "<pre>"; print_r($get_provider_data); echo "</pre>";
                      $provider_id=$get_provider_data[0]->provider_id;
                    $get_from_encounter = "SELECT form_id,pid FROM forms WHERE formdir='LBF2' and deleted=0 and encounter = $eid";
                    $db->query( "SET NAMES utf8");
                    $get_stmt4 = $db->prepare($get_from_encounter) ;
                    $get_stmt4->execute(); 
                    $set_from_encounter = $get_stmt4->fetchAll(PDO::FETCH_OBJ); 
                    if(!empty($set_from_encounter)){
                        $get_from_encounter_data = "SELECT * FROM lbf_data WHERE form_id = '".$set_from_encounter[0]->form_id."' and ($check_string)";
                        $db->query( "SET NAMES utf8");
                        $newid = $set_from_encounter[0]->form_id;
                        $get_stmt5 = $db->prepare($get_from_encounter_data) ;
                        $get_stmt5->execute(); 
                        $set_from_encounter_data = $get_stmt5->fetchAll(PDO::FETCH_OBJ); 
                        if(!empty($set_from_encounter_data)){
                            foreach($set_from_encounter_data as $data_key => $data_value){
                                //echo $data_value->form_id."==".$data_value->field_id."==".$data_value->field_value."<br>";
                                if($data_value->field_id!='f2f_ps' && $data_value->field_id!='f2f_ps_on' && $data_value->field_id!='f2f_np' && $data_value->field_id!='f2f_np_on' && $data_value->field_id!='f2f_printed'){
                                    $insert_form = "INSERT into lbf_data (form_id, field_id, field_value) VALUES ('".$set_form_id_sql[0]->form_id."','".$data_value->field_id."','".addslashes($data_value->field_value)."')";
                                    $db->query( "SET NAMES utf8");
                                    $get_stmt6 = $db->prepare($insert_form) ;
//                                    $get_stmt6->execute(); 
                                    if($get_stmt6->execute()){
                                        insertMobileLog('insert',$username,$insert_form,$pid,$copy_to_encounter,"Create Template FROM LBF form Screen",1);
                                    }else {
                                        insertMobileLog('insert',$username,$insert_form,$pid,$encounter,"Create Template FROM LBF form Screen - Failed",0); 
                                    }
                                    $check_data3 = 1;
                                }
//                                else if($data_value->field_id=='f2f_ps'){
//                                    $insert_form = "INSERT into lbf_data (form_id, field_id, field_value) VALUES ('".$set_form_id_sql[0]->form_id."','".$data_value->field_id."','$provider_id')";
//                                    $get_stmt6 = $db->prepare($insert_form) ;
//                                    $get_stmt6->execute(); 
//                                    $check_data3 = 1;
//                                }
                            }
                        }
                        // to update template
                        $sql_temp = "SELECT field_id, title FROM layout_options WHERE form_id = 'LBF1' and group_name LIKE '%Dictation'";
                        $db->query( "SET NAMES utf8");
                        $sql_temp_stmt = $db->prepare($sql_temp) ;
                        $sql_temp_stmt->execute(); 
                        $set_sql_temp = $sql_temp_stmt->fetchAll(PDO::FETCH_OBJ);
                        foreach($set_sql_temp as $key_value){
                            $save_template_field = "UPDATE `tbl_allcare_template` SET date = 'NOW()',copy_from_enc = $eid ,copy_from_id = $newid WHERE copy_to_enc = $copy_to_encounter and copy_to_id = $toid and form_name ='".$key_value->title."'"; 
                            $db->query( "SET NAMES utf8");
                            $get_stmt29 = $db->prepare($save_template_field) ;
                            if($get_stmt29->execute()){
                                insertMobileLog('update',$username,$save_template_field,$pid,$newid,"Update Template FROM LBF form template Screen",1);
                            }else {
                                insertMobileLog('update',$username,$save_template_field,$pid,$newid,"Update Template FROM LBF form template Screen - Failed",0); 
                            }
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
                $db->query( "SET NAMES utf8");
                $get_stmt7 = $db->prepare($get_new_formid) ;
                $get_stmt7->execute(); 
                $set_new_formid = $get_stmt7->fetchAll(PDO::FETCH_OBJ); 
                if(!empty($set_new_formid))
                    $new_formid = $set_new_formid[0]->form_id+1;
                else
                    $new_formid = 1;

                $form_ins = "INSERT INTO forms (date,encounter,form_name,form_id,pid,user,groupname,authorized,deleted,formdir) VALUES (NOW(),$copy_to_encounter,'Allcare Encounter Forms',$new_formid,$pid,'$provider','default',1,0,'LBF2')";
                $db->query( "SET NAMES utf8");
                $form_insstmt = $db->prepare($form_ins);
                if($form_insstmt->execute()){
                    insertMobileLog('insert',$username,$form_ins,$pid,$copy_to_encounter,"Insert form for Template FROM LBF form Screen",1);
                }else {
                    insertMobileLog('insert',$username,$form_ins,$pid,$copy_to_encounter,"Insert form for Template FROM LBF form Screen - Failed",0); 
                }

                
                $get_provider = "SELECT * FROM form_encounter WHERE encounter = $copy_to_encounter";
                $db->query( "SET NAMES utf8");
                $get_stmt51 = $db->prepare($get_provider) ;
                $get_stmt51->execute(); 
                $get_provider_data = $get_stmt51->fetchAll(PDO::FETCH_OBJ); 
                //echo "<pre>"; print_r($get_provider_data); echo "</pre>";
                $provider_id=$get_provider_data[0]->provider_id;
                $username2                  = getUserName($provider_id);
                $username                   = isset($username2[0]['username'])? $username2[0]['username'] : '';
                $get_from_encounter = "SELECT form_id,pid FROM forms WHERE formdir='LBF2' and deleted=0 and encounter = $eid";
                $db->query( "SET NAMES utf8");
                $get_stmt8 = $db->prepare($get_from_encounter) ;
                $get_stmt8->execute(); 
                $set_from_encounter = $get_stmt8->fetchAll(PDO::FETCH_OBJ); 
                if(!empty($set_from_encounter)){
                    $new_id = $set_from_encounter[0]->form_id;
                    $get_from_encounter_data = "SELECT * FROM lbf_data WHERE form_id = '".$set_from_encounter[0]->form_id."' and ($check_string)";
                    $db->query( "SET NAMES utf8");
                    $get_stmt9 = $db->prepare($get_from_encounter_data) ;
                    $get_stmt9->execute(); 
                    $set_from_encounter_data = $get_stmt9->fetchAll(PDO::FETCH_OBJ); 
                    if(!empty($set_from_encounter_data)){
                        foreach($set_from_encounter_data as $data_key => $data_value){
                            //echo $data_value->form_id."==".$data_value->field_id."==".$data_value->field_value."<br>";
                            if($data_value->field_id!='f2f_ps' && $data_value->field_id!='f2f_ps_on' && $data_value->field_id!='f2f_np' && $data_value->field_id!='f2f_np_on' && $data_value->field_id!='f2f_printed'){
                                $insert_form = "INSERT into lbf_data (form_id, field_id, field_value) VALUES ('".$new_formid."','".$data_value->field_id."','".addslashes($data_value->field_value)."')";
                                $db->query( "SET NAMES utf8");
                                $get_stmt6 = $db->prepare($insert_form) ;
                                if($get_stmt6->execute()){
                                    insertMobileLog('insert',$username,$insert_form,$pid,$eid,"Insert Template FROM LBF form Screen",1);
                                }else {
                                    insertMobileLog('insert',$username,$insert_form,$pid,$eid,"Insert Template FROM LBF form Screen - Failed",0); 
                                }   
                                $check_data3 = 1;
                            }
//                            else if($data_value->field_id=='f2f_ps'){
//                                $insert_form = "INSERT into lbf_data (form_id, field_id, field_value) VALUES ('".$new_formid."','".$data_value->field_id."','$provider_id')";
//                                $get_stmt6 = $db->prepare($insert_form) ;
//                                $get_stmt6->execute(); 
//                                $check_data3 = 1;
//                            }
                        }
                    }
                }
                // insert template
                $sql_temp = "SELECT field_id, title FROM layout_options WHERE form_id = 'LBF1' and group_name LIKE '%Dictation'";
                $db->query( "SET NAMES utf8");
                $sql_temp_stmt = $db->prepare($sql_temp) ;
                $sql_temp_stmt->execute(); 
                $set_sql_temp = $sql_temp_stmt->fetchAll(PDO::FETCH_OBJ);
                foreach($set_sql_temp as $key_value){
                    $save_template_field = "INSERT INTO `tbl_allcare_template`( `date`, `copy_from_enc`, `copy_from_id`, `copy_to_enc`, `copy_to_id`, `form_name`, `user`) VALUES (NOW(),$eid,$new_id,$copy_to_encounter,$new_formid,'$key_value->title','$provider')"; 
                    $db->query( "SET NAMES utf8");
                    $get_stmt29 = $db->prepare($save_template_field) ;
                    if($get_stmt29->execute()){
                        insertMobileLog('insert',$username,$save_template_field,$pid,$new_id,"Insert Template FROM LBF form template Screen",1);
                    }else {
                        insertMobileLog('insert',$username,$save_template_field,$pid,$new_id,"Insert Template FROM LBF form template Screen - Failed",0); 
                    }  
                }
            }
        }
        
        //  for ros
        $sql8 = "SELECT form_id,pid  from forms WHERE encounter = $eid and deleted = 0 and form_name = 'Allcare Review Of Systems' and formdir='allcare_ros' GROUP BY form_name ORDER BY id DESC";
        $db->query( "SET NAMES utf8");
        $stmt8 = $db->prepare($sql8);
        $stmt8->execute();  
        $datacheck5 = $stmt8->fetchAll(PDO::FETCH_OBJ);
        if(!empty($datacheck5)){
            $ros_form_id = $datacheck5[0]->form_id;
            $sql71 = "SELECT * FROM tbl_form_allcare_ros WHERE id= '".$datacheck5[0]->form_id."' ";
            $db->query( "SET NAMES utf8");
            $stmt71 = $db->prepare($sql71);
            $stmt71->execute();  
            $copy_from_ros = $stmt71->fetchAll(PDO::FETCH_OBJ);
            if(!empty($copy_from_ros)){
                $ros_sql = "SELECT form_id,pid  from forms WHERE encounter = $copy_to_encounter and deleted = 0 and form_name = 'Allcare Review Of Systems' and formdir='allcare_ros' GROUP BY form_name ORDER BY id DESC";
                $db->query( "SET NAMES utf8");
                $rosstmt = $db->prepare($ros_sql);
                $rosstmt->execute();  
                $ros_copy_to = $rosstmt->fetchAll(PDO::FETCH_OBJ);
                $copy_to_ros = array();
                $check1 = 0;
                if(!empty($ros_copy_to)){
                    //delete previous data
                    $copy_to_id = $ros_copy_to[0]->form_id;
                    $delete_ros = "delete from tbl_form_allcare_ros where id='".$ros_copy_to[0]->form_id."'";
                    $db->query( "SET NAMES utf8");
                    $delete_stmt1 = $db->prepare($delete_ros);
                    $delete_stmt1->execute();
                    $check1 = 1;
                }else{
                    //create new ros
                    $sqlGetMaxEncounter="SELECT id as form_id FROM sequences";

                    $stmt2 = $db->prepare($sqlGetMaxEncounter);
                    $stmt2->execute();
                    $resMaxEncounter = $stmt2->fetchAll(PDO::FETCH_OBJ);

                    if($resMaxEncounter){
                        $copy_to_id = $resMaxEncounter[0]->form_id +1;

                        $queryseq = "UPDATE sequences SET id = $copy_to_id ";
                        $db->query( "SET NAMES utf8");
                        $stmt_layout = $db->prepare($queryseq);
                        if($stmt_layout->execute()){
                            insertMobileLog('update',"$username",$queryseq,$pid,$copy_to_encounter,"Save Form Sequence From ROS Data Screen", 1);
                        }else{
                            insertMobileLog('update',"$username",$queryseq,$pid,$copy_to_encounter,"Save Form Sequence From ROS Data Screen - Failed", 0);
                        }

                    }else{
                        $copy_to_id = 1;
                    }

                    $form_ins = "INSERT INTO forms (date,encounter,form_name,form_id,pid,user,groupname,authorized,deleted,formdir) VALUES (NOW(),$copy_to_encounter,'Allcare Review Of Systems',$copy_to_id,'".$datacheck5[0]->pid."','$provider','default',1,0,'allcare_ros')";
                    $db->query( "SET NAMES utf8");
                    $form_insstmt = $db->prepare($form_ins);
                    if($form_insstmt->execute()){
                        insertMobileLog('insert',$username,$form_ins,$pid,$copy_to_encounter,"Create Review of System Form Screen",1);
                    }else {
                        insertMobileLog('insert',$username,$form_ins,$pid,$copy_to_encounter,"Create Review of System Form Screen - Failed",0); 
                    } 
                }
                foreach($copy_from_ros as $val5 ){
                    foreach($val5 as $key6 => $value6){
                        if($key6 != 'id'):
                            $fields .= $key6.",";
                            $fields1 = rtrim($fields,",");
                            $field_value .= "'".addslashes($value6)."',";
                        endif;
                    }
                }
                $field_value1 = rtrim($field_value,",");

                $ins_ros="insert into tbl_form_allcare_ros (id,$fields1) values ($copy_to_id,$field_value1)";
                $db->query( "SET NAMES utf8");
                $ins_stmt1 = $db->prepare($ins_ros);
                if($ins_stmt1->execute()){
                    insertMobileLog('insert',$username,$ins_ros,$pid,$copy_to_encounter,"Save Review of System Form Screen",1);
                }else {
                    insertMobileLog('insert',$username,$ins_ros,$pid,$copy_to_encounter,"Save Review of System Form Screen - Failed",0); 
                } 
                $check_data2 = 1;
                if($check1 == 0 ){
                    // insert template
                    $save_template_field = "INSERT INTO `tbl_allcare_template`( `date`, `copy_from_enc`, `copy_from_id`, `copy_to_enc`, `copy_to_id`, `form_name`, `user`) VALUES (NOW(),$eid,'".$datacheck5[0]->form_id."',$copy_to_encounter,$copy_to_id,'Allcare Review Of Systems','$provider')";
                    $db->query( "SET NAMES utf8");
                    $get_stmt28 = $db->prepare($save_template_field) ;
                    if($get_stmt28->execute()){
                        insertMobileLog('insert',$username,$save_template_field,$pid,$copy_to_encounter,"Save Review of System Form template Screen",1);
                    }else {
                        insertMobileLog('insert',$username,$save_template_field,$pid,$copy_to_encounter,"Save Review of System Form template Screen - Failed",0); 
                    }
                }else{
                    // update template
                    $save_template_field = "UPDATE `tbl_allcare_template` SET date = 'NOW()',copy_from_enc = $eid ,copy_from_id =". $datacheck5[0]->form_id ." WHERE copy_to_enc = $copy_to_encounter and copy_to_id = $copy_to_id and form_name ='Allcare Review Of Systems'";
                    $db->query( "SET NAMES utf8");
                    $get_stmt28 = $db->prepare($save_template_field) ;
                    if($get_stmt28->execute()){
                        insertMobileLog('insert',$username,$save_template_field,$pid,$copy_to_encounter,"Save Review of System Form template Screen",1);
                    }else {
                        insertMobileLog('insert',$username,$save_template_field,$pid,$copy_to_encounter,"Save Review of System Form template Screen - Failed",0); 
                    }
                }
                //this is for finalized ,pending  and log

                $form_flag="SELECT * 
                                FROM  `tbl_allcare_formflag` 
                                WHERE form_id ='".$datacheck5[0]->form_id."' AND form_name='Allcare Review Of Systems' order by id desc";
                $db->query( "SET NAMES utf8");
                $form_flagstmt7 = $db->prepare($form_flag);
                $form_flagstmt7->execute();  
                $form_flag_res = $form_flagstmt7->fetchAll(PDO::FETCH_OBJ);
                foreach($form_flag_res as $val2){
                    $finalized=$val2->finalized;
                    $pending=$val2->pending;
                }
                $logdata= array(); 
                $data = "SELECT logdate from `tbl_allcare_formflag` WHERE  form_id=".$copy_to_id;
                $db->query( "SET NAMES utf8");
                $data_stmt7 = $db->prepare($data);
                $data_stmt7->execute();  
                $form_flag_res1 = $data_stmt7->fetchAll(PDO::FETCH_OBJ);
                //echo "<pre>"; print_r($form_flag_res1); echo "</pre>";
                foreach ($form_flag_res1 as $value2) {
                        $array =  unserialize($value2->logdate);
                        $count= count($array);
                }


                $res = "SELECT * FROM `tbl_allcare_formflag` WHERE form_id = '$copy_to_id'";
                $db->query( "SET NAMES utf8");
                $row1 = $db->prepare($res);
                $row1->execute();  
                $row1_res1 = $row1->fetchAll(PDO::FETCH_OBJ);
                if(empty($row1_res1)){
                    $count = 0;

                    $array2[] = array( 'authuser' =>$username,'pending' => $pending,'finalized' => $finalized, 'date' => date("Y/m/d"), 'action'=>'Copied','ip_address'=>'(mobile)'  ,'count'=> $count+1);
//                    $logdata = array_merge_recursive($array, $array2);
                    $logdata=  serialize($array2);
                    $query1 = "INSERT INTO tbl_allcare_formflag ( " .
                            "encounter_id,form_id, form_name,pending,finalized, logdate" .
                            ") VALUES ( " .
                            "".$copy_to_encounter.",'$copy_to_id', 'Allcare Review Of Systems','$pending', '$finalized', '".$logdata."' " .
                            ")";
                    $db->query( "SET NAMES utf8");
                    $log_stmt = $db->prepare($query1);
                    if($log_stmt->execute()){
                        insertMobileLog('insert',$username,$query1,$pid,$copy_to_encounter,"Save Review of System Form log Screen",1);
                    }else {
                        insertMobileLog('insert',$username,$query1,$pid,$copy_to_encounter,"Save Review of System Form log Screen - Failed",0); 
                    }
                    $check_data = 1;
                }else{
                    $count = isset($count)? $count: 0;

                    $array2[] = array( 'authuser' =>$username,'pending' => $pending,'finalized' => $finalized, 'date' => date("Y/m/d"), 'action'=>'Copied','ip_address'=>'(mobile)'  ,'count'=> $count+1);
                    $logdata = array_merge_recursive($array, $array2);
                    $logdata= ($logdata? serialize($logdata): serialize($array2) );
                    $query1 = "UPDATE tbl_allcare_formflag SET pending ='$pending',finalized = '$finalized',logdate=  '".$logdata."' WHERE encounter_id ='$copy_to_encounter' and form_id = '$copy_to_id' and form_name = 'Allcare Review Of Systems'"; 
                    $db->query( "SET NAMES utf8");
                    $log_stmt = $db->prepare($query1);
                    if($log_stmt->execute()){
                        insertMobileLog('update',$username,$query1,$pid,$copy_to_encounter,"Save Review of System Form log Screen",1);
                    }else {
                        insertMobileLog('update',$username,$query1,$pid,$copy_to_encounter,"Save Review of System Form log Screen - Failed",0); 
                    }
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
                $db->query( "SET NAMES utf8");
                $sqlstmt31= $db->prepare($result21) ;
                $data31 = $sqlstmt31->execute();
                $formid_val = $sqlstmt31->fetchAll(PDO::FETCH_OBJ);

                if(!empty($formid_val)){
                    $sql5 = "update `lbf_data`  SET  `field_value`='".$formstatus."' where `form_id`= ".$toid." AND `field_id`='ros_stat'";
                    $db->query( "SET NAMES utf8");
                    $sqlstmt5 = $db->prepare($sql5) ;
                    if($sqlstmt5->execute()){
                        insertMobileLog('update',$username,$sql5,$pid,$copy_to_encounter,"Save Review of System Form LBF Screen",1);
                    }else {
                        insertMobileLog('update',$username,$sql5,$pid,$copy_to_encounter,"Save Review of System Form LBF Screen - Failed",0); 
                    }
                }else {
                    $sql5 = "INSERT into `lbf_data`(`form_id`, `field_id`,`field_value`) VALUES(".$toid.",'ros_stat','".$formstatus."')";
                    $db->query( "SET NAMES utf8");
                    $sqlstmt5 = $db->prepare($sql5) ;
                    if($sqlstmt5->execute()){
                        insertMobileLog('insert',$username,$sql5,$pid,$copy_to_encounter,"Save Review of System Form LBF Screen",1);
                    }else {
                        insertMobileLog('insert',$username,$sql5,$pid,$copy_to_encounter,"Save Review of System Form LBF Screen - Failed",0); 
                    }
                }

                if($check1 == 0 ){
                    $save_template_field = "UPDATE `tbl_allcare_template` SET date = 'NOW()',copy_from_enc = $eid ,copy_from_id =". $datacheck5[0]->form_id ." WHERE copy_to_enc = $copy_to_encounter and copy_to_id = $copy_to_id and form_name ='Allcare Review Of Systems'";
                    $db->query( "SET NAMES utf8");
                    $get_stmt30 = $db->prepare($save_template_field) ;
                    if($get_stmt30->execute()){
                        insertMobileLog('update',$username,$save_template_field,$pid,$copy_to_encounter,"Save Review of System Form LBF template Screen",1);
                    }else {
                        insertMobileLog('update',$username,$save_template_field,$pid,$copy_to_encounter,"Save Review of System Form LBF template Screen - Failed",0); 
                    }
                }else{
                    $save_template_field = "INSERT INTO `tbl_allcare_template`( `date`, `copy_from_enc`, `copy_from_id`, `copy_to_enc`, `copy_to_id`, `form_name`, `user`) VALUES (NOW(),$eid,". $datacheck5[0]->form_id .",$copy_to_encounter,$copy_to_id,'Allcare Review Of Systems','$provider')";
                    $db->query( "SET NAMES utf8");
                    $get_stmt31 = $db->prepare($save_template_field) ;
                    if($get_stmt31->execute()){
                        insertMobileLog('insert',$username,$save_template_field,$pid,$copy_to_encounter,"Save Review of System Form LBF template Screen",1);
                    }else {
                        insertMobileLog('insert',$username,$save_template_field,$pid,$copy_to_encounter,"Save Review of System Form LBF template Screen - Failed",0); 
                    }
                }
            }
        }else{
            $form_id = 0;
        }
        
        // for physical form
        $sql7 = "SELECT form_id ,pid from forms WHERE encounter = $eid and deleted = 0 and form_name = 'Allcare Physical Exam' and formdir='allcare_physical_exam' GROUP BY form_name
                                         ORDER BY id DESC ";
        $db->query( "SET NAMES utf8");
        $stmt7 = $db->prepare($sql7);
        $stmt7->execute();  
        $datacheck4 = $stmt7->fetchAll(PDO::FETCH_OBJ);

        if(!empty($datacheck4)):
            $form_id2 = $datacheck4[0]->form_id;
            $sql71 = "SELECT * FROM tbl_form_physical_exam WHERE forms_id= '".$datacheck4[0]->form_id."' ";
            $db->query( "SET NAMES utf8");
            $stmt71 = $db->prepare($sql71);
            $stmt71->execute();  
            $datacheck5 = $stmt71->fetchAll(PDO::FETCH_OBJ);

            $sql = "SELECT form_id,pid  from forms WHERE encounter = $copy_to_encounter and deleted = 0 and form_name = 'Allcare Physical Exam' and formdir='allcare_physical_exam' GROUP BY form_name
                         ORDER BY id DESC ";
            $db->query( "SET NAMES utf8");
            $stmt = $db->prepare($sql);
            $stmt->execute();  
            $copy_to_fid = $stmt->fetchAll(PDO::FETCH_OBJ);
            $copy_to_details=array();
            if(!empty($copy_to_fid)):
                $copy_to_id=$copy_to_fid[0]->form_id;
                $sql72 = "SELECT * FROM tbl_form_physical_exam WHERE forms_id= '".$copy_to_fid[0]->form_id."' ";
                $db->query( "SET NAMES utf8");
                $stmt72 = $db->prepare($sql72);
                $stmt72->execute();  
                $copy_to_details = $stmt72->fetchAll(PDO::FETCH_OBJ);
             else: 
                $new_sql = "select max(form_id)as new_form from forms where form_name='Allcare Physical Exam' AND formdir='allcare_physical_exam'";
                $db->query( "SET NAMES utf8");
                $new_stmt = $db->prepare($new_sql);
                $new_stmt->execute();
                $new_res = $new_stmt->fetchAll(PDO::FETCH_OBJ);
                $new_fid= $new_res[0]->new_form;
                $new_id1=++$new_fid;

                $form_ins = "INSERT INTO forms (date,encounter,form_name,form_id,pid,user,groupname,authorized,deleted,formdir) VALUES (NOW(),$copy_to_encounter,'Allcare Physical Exam',$new_id1,'".$datacheck4[0]->pid."','$provider','default',1,0,'allcare_physical_exam')";
                $db->query( "SET NAMES utf8");
                $form_insstmt = $db->prepare($form_ins);
                if($form_insstmt->execute()){
                    insertMobileLog('insert',$username,$form_ins,$pid,$copy_to_encounter,"Create Physical Exam Form Screen",1);
                }else {
                    insertMobileLog('insert',$username,$form_ins,$pid,$copy_to_encounter,"Create Physical Exam Form Screen - Failed",0); 
                }
                $copy_to_id=$new_id1;
            endif;

            if(!empty($copy_to_details)):
                $form_id1 = $copy_to_details[0]->forms_id;
                $delete_pe="delete from tbl_form_physical_exam where forms_id=$copy_to_id";
                $db->query( "SET NAMES utf8");
                $pe_insstmt1 = $db->prepare($delete_pe);
                $pe_insstmt1->execute();  
                foreach($datacheck5 as $value1){
                   //$comments=str_replace("'", "\\'", $value1->comments);
                    $pe_ins = "INSERT INTO tbl_form_physical_exam (forms_id, line_id, wnl, abn, diagnosis, comments) VALUES($copy_to_id,'".$value1->line_id."', '".$value1->wnl."', '".$value1->abn."', '".$value1->diagnosis."', '".addslashes($value1->comments)."' )";
                    $db->query( "SET NAMES utf8");
                    $pe_insstmt = $db->prepare($pe_ins);
                    if($pe_insstmt->execute()){
                        insertMobileLog('insert',$username,$pe_ins,$pid,$copy_to_encounter,"Save Physical Exam Form Screen",1);
                    }else {
                        insertMobileLog('insert',$username,$pe_ins,$pid,$copy_to_encounter,"Save Physical Exam Form Screen - Failed",0); 
                    }
                } 


               //this is for finalized ,pending  and log

                $form_flag="SELECT * 
                                FROM  `tbl_allcare_formflag` 
                                WHERE form_id ='".$datacheck5[0]->forms_id."' AND form_name='Allcare Physical Exam' order by id desc limit 0,1";
                $db->query( "SET NAMES utf8");
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
                $db->query( "SET NAMES utf8");
                $data_stmt7 = $db->prepare($data);
                $data_stmt7->execute();  
                $form_flag_res1 = $data_stmt7->fetchAll(PDO::FETCH_OBJ);
                //echo "<pre>"; print_r($form_flag_res1); echo "</pre>";
                foreach ($form_flag_res1 as $value2) {
                        $array =  unserialize($value2->logdate);
                        $count= count($array);
                }


                $res = "SELECT * FROM `tbl_allcare_formflag` WHERE encounter_id ='$copy_to_encounter' and form_id = '$copy_to_id' and form_name = 'Allcare Physical Exam'  order by id desc limit 0,1";
                $db->query( "SET NAMES utf8");
                $row1 = $db->prepare($res);
                $row1->execute();  
                $row1_res1 = $row1->fetchAll(PDO::FETCH_OBJ);
                if(empty($row1_res1)){
                    $count = 0;

                    $array2[] = array( 'authuser' =>$provider,'pending' => $ppending,'finalized' => $pfinalized, 'date' => date("Y/m/d"), 'action'=>'Copied','ip_address'=>'(mobile)'  ,'count'=> $count+1);
//                    $logdata = array_merge_recursive($array, $array2);
                    $logdata=  serialize($array2);
                    $query1 = "INSERT INTO tbl_allcare_formflag ( " .
                            "encounter_id,form_id, form_name,pending,finalized, logdate" .
                            ") VALUES ( " .
                            "".$copy_to_encounter.",'$copy_to_id', 'Allcare Physical Exam','$ppending', '$pfinalized', '".$logdata."' " .
                            ")";
                    $db->query( "SET NAMES utf8");
                    $log_stmt = $db->prepare($query1);
                    if($log_stmt->execute()){
                        insertMobileLog('insert',$username,$query1,$pid,$copy_to_encounter,"Save Physical Exam Form log Screen",1);
                    }else {
                        insertMobileLog('insert',$username,$query1,$pid,$copy_to_encounter,"Save Physical Exam Form log Screen - Failed",0); 
                    }
                    $check_data = 1;
                }else{
                    $count = isset($count)? $count: 0;

                    $array2[] = array( 'authuser' =>$provider,'pending' => $ppending,'finalized' => $pfinalized, 'date' => date("Y/m/d"), 'action'=>'Copied','ip_address'=>'(mobile)'  ,'count'=> $count+1);
                    $logdata = array_merge_recursive($array, $array2);
                    $logdata= ($logdata? serialize($logdata): serialize($array2) );
                    $query1 = "UPDATE tbl_allcare_formflag SET pending ='$ppending',finalized = '$pfinalized',logdate=  '".$logdata."' WHERE encounter_id ='$copy_to_encounter' and form_id = '$copy_to_id' and form_name = 'Allcare Physical Exam'"; 
                    $db->query( "SET NAMES utf8");
                    $log_stmt = $db->prepare($query1);
                    if($log_stmt->execute()){
                        insertMobileLog('update',$username,$query1,$pid,$copy_to_encounter,"Save Physical Exam Form log Screen",1);
                    }else {
                        insertMobileLog('update',$username,$query1,$pid,$copy_to_encounter,"Save Physical Exam Form log Screen - Failed",0); 
                    } 
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
                $db->query( "SET NAMES utf8");
                $sqlstmt32= $db->prepare($result22) ;
                $data32 = $sqlstmt32->execute();
                $formid_val = $sqlstmt32->fetchAll(PDO::FETCH_OBJ);

                if(!empty($formid_val)){
                    $sql51 = "update `lbf_data`  SET  `field_value`='".$formstatus."' where `form_id`= ".$toid." AND `field_id`='physical_exam_stat'";
                    $db->query( "SET NAMES utf8");
                    $sqlstmt51 = $db->prepare($sql51) ;
                    if($sqlstmt51->execute()){
                        insertMobileLog('update',$username,$sql51,$pid,$copy_to_encounter,"Save Physical Exam Form LBF Screen",1);
                    }else {
                        insertMobileLog('update',$username,$sql51,$pid,$copy_to_encounter,"Save Physical Exam Form LBF Screen - Failed",0); 
                    } 
                }else {
                    $sql51 = "INSERT into `lbf_data`(`form_id`, `field_id`,`field_value`) VALUES(".$toid.",'physical_exam_stat','".$formstatus."')";
                    $db->query( "SET NAMES utf8");
                    $sqlstmt51 = $db->prepare($sql51) ;
                    if($sqlstmt51->execute()){
                        insertMobileLog('insert',$username,$sql51,$pid,$copy_to_encounter,"Save Physical Exam Form LBF Screen",1);
                    }else {
                        insertMobileLog('insert',$username,$sql51,$pid,$copy_to_encounter,"Save Physical Exam Form LBF Screen - Failed",0); 
                    }
                }
                $save_template_field = "UPDATE `tbl_allcare_template` SET date = 'NOW()',copy_from_enc = $eid ,copy_from_id =". $datacheck5[0]->forms_id ." WHERE copy_to_enc = $copy_to_encounter and copy_to_id = $copy_to_id and form_name ='Allcare Physical Exam'";
                $db->query( "SET NAMES utf8");
                $get_stmt28 = $db->prepare($save_template_field) ;
                if($get_stmt28->execute()){
                    insertMobileLog('update',$username,$save_template_field,$pid,$copy_to_encounter,"Save Physical Exam Form template Screen",1);
                }else {
                    insertMobileLog('update',$username,$save_template_field,$pid,$copy_to_encounter,"Save Physical Exam Form template Screen - Failed",0); 
                }
            else:
                foreach($datacheck5 as $value1){
                    //$comments=str_replace("'", "\\'", $value1->comments);
                    $pe_ins = "INSERT INTO tbl_form_physical_exam (forms_id, line_id, wnl, abn, diagnosis, comments) VALUES($copy_to_id,'".$value1->line_id."', '".$value1->wnl."', '".$value1->abn."', '".$value1->diagnosis."', '".addslashes($value1->comments)."' )";
                    $pe_insstmt = $db->prepare($pe_ins);
                    $db->query( "SET NAMES utf8");
                    if($pe_insstmt->execute()){
                        insertMobileLog('insert',$username,$pe_ins,$pid,$copy_to_encounter,"Save Physical Exam Form Screen",1);
                    }else {
                        insertMobileLog('insert',$username,$pe_ins,$pid,$copy_to_encounter,"Save Physical Exam Form Screen - Failed",0); 
                    } 
                } 

                $form_flag="SELECT * 
                            FROM  `tbl_allcare_formflag` 
                            WHERE form_id ='".$datacheck5[0]->forms_id."' AND form_name='Allcare Physical Exam' order by id desc";
                $db->query( "SET NAMES utf8");
                $form_flagstmt7 = $db->prepare($form_flag);
                $form_flagstmt7->execute();  
                $form_flag_res = $form_flagstmt7->fetchAll(PDO::FETCH_OBJ);
                foreach($form_flag_res as $val2){
                    $pfinalized=$val2->finalized;
                    $ppending=$val2->pending;
                }
                $logdata= array(); $array=array();
                $data = "SELECT logdate from `tbl_allcare_formflag` WHERE  form_id=".$copy_to_id;
                $db->query( "SET NAMES utf8");
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

                $array2[] = array( 'authuser' =>$provider,'pending' => $ppending,'finalized' => $pfinalized, 'date' => date("Y/m/d"), 'action'=>'Copied','ip_address'=>'(mobile)'  ,'count'=> $count+1);
                $logdata = array_merge_recursive($array, $array2);
                $logdata= ($logdata? serialize($logdata): serialize($array2) );
                 $query1 = "INSERT INTO tbl_allcare_formflag ( " .
                        "encounter_id,form_id, form_name,pending,finalized, logdate" .
                        ") VALUES ( " .
                        "".$copy_to_encounter.",'$copy_to_id', 'Allcare Physical Exam','$ppending', '$pfinalized', '".$logdata."' " .
                        ")";
                $log_stmt = $db->prepare($query1);
                $db->query( "SET NAMES utf8");
                if($log_stmt->execute()){
                    insertMobileLog('insert',$username,$query1,$pid,$copy_to_encounter,"Save Physical Exam Form log Screen",1);
                }else {
                    insertMobileLog('insert',$username,$query1,$pid,$copy_to_encounter,"Save Physical Exam Form log Screen - Failed",0); 
                } 
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
                $db->query( "SET NAMES utf8");
                $sqlstmt32= $db->prepare($result22) ;
                $data32 = $sqlstmt32->execute();
                $formid_val = $sqlstmt32->fetchAll(PDO::FETCH_OBJ);

                if(!empty($formid_val)){
                    $sql51 = "update `lbf_data`  SET  `field_value`='".$formstatus."' where `form_id`= ".$toid." AND `field_id`='physical_exam_stat'";
                    $db->query( "SET NAMES utf8");
                    $sqlstmt51 = $db->prepare($sql51) ;
                    if($sqlstmt51->execute()){
                        insertMobileLog('update',$username,$sql51,$pid,$copy_to_encounter,"Save Physical Exam Form LBF Screen",1);
                    }else {
                        insertMobileLog('update',$username,$sql51,$pid,$copy_to_encounter,"Save Physical Exam Form LBF Screen - Failed",0); 
                    }
                }else {
                    $sql51 = "INSERT into `lbf_data`(`form_id`, `field_id`,`field_value`) VALUES(".$toid.",'physical_exam_stat','".$formstatus."')";
                    $db->query( "SET NAMES utf8");
                    $sqlstmt51 = $db->prepare($sql51) ;
                    if($sqlstmt51->execute()){
                        insertMobileLog('insert',$username,$sql51,$pid,$copy_to_encounter,"Save Physical Exam Form LBF Screen",1);
                    }else {
                        insertMobileLog('insert',$username,$sql51,$pid,$copy_to_encounter,"Save Physical Exam Form LBF Screen - Failed",0); 
                    }
                }
                $save_template_field = "UPDATE `tbl_allcare_template` SET date = 'NOW()',copy_from_enc = $eid ,copy_from_id =". $datacheck5[0]->forms_id ." WHERE copy_to_enc = $copy_to_encounter and copy_to_id = $copy_to_id and form_name ='Allcare Physical Exam'";
                $db->query( "SET NAMES utf8");
                $get_stmt28 = $db->prepare($save_template_field) ;
                if($get_stmt28->execute()){
                    insertMobileLog('update',$username,$save_template_field,$pid,$copy_to_encounter,"Save Physical Exam Form LBF Screen",1);
                }else {
                    insertMobileLog('update',$username,$save_template_field,$pid,$copy_to_encounter,"Save Physical Exam Form LBF Screen - Failed",0); 
                }
                
                $check_data = 1;
                $save_template_field = "INSERT INTO `tbl_allcare_template`( `date`, `copy_from_enc`, `copy_from_id`, `copy_to_enc`, `copy_to_id`, `form_name`, `user`) VALUES (NOW(),$eid,$form_id2,$copy_to_encounter,$copy_to_id,'Allcare Physical Exam','$provider')";
                $db->query( "SET NAMES utf8");
                $get_stmt29 = $db->prepare($save_template_field) ;
                if($get_stmt29->execute()){
                    insertMobileLog('update',$username,$save_template_field,$pid,$copy_to_encounter,"Save Physical Exam Form LBF Screen",1);
                }else {
                    insertMobileLog('update',$username,$save_template_field,$pid,$copy_to_encounter,"Save Physical Exam Form LBF Screen - Failed",0); 
                }
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
       insertMobileLog('update',$username,$error,$pid,$copy_to_encounter,"Template FROM Screen - Query Failed",0); 
    }
    
}

/* ============ Hema methods start here ================== */


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

function getDynamicPatientAgencies($pid){
    $key = 'rotcoderaclla';
    // The default key size is 256 bits.
    $old_key_size = GibberishAES::size();
    GibberishAES::size(256);
    $db = getConnection();
    try{
        $sql = "SELECT id,agencyid,agency_admitdate ,agency_dischargedate , agency_isactive , agency_notes, agency_related_links
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
                    }elseif($a_key == 'id'){
                        $fields_array[$i]['id'] = $a_value;
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
                $getcolumnNames = "SELECT field_id FROM layout_options WHERE form_id = 'ADDRCA' AND uor<>0 ";
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
                            
                            $getcolumnNames2 = "SELECT title FROM layout_options WHERE form_id = 'ADDRCA' AND uor<>0 and field_id= '$ckey'";
                            $columnstmt2 = $db->prepare($getcolumnNames2);
                            $db->query( "SET NAMES utf8"); 
                            $columnstmt2->execute(); 
                            $setcolumnNames2 = $columnstmt2->fetchAll(PDO::FETCH_OBJ); 
                            if(!empty($setcolumnNames2))
                                $ckey = $setcolumnNames2[0]-> title;
                            
                            if(!empty($set_check_list)){
                                if($set_check_list[0]->data_type == 28){
                                    if(!empty($cvalue)){
                                        $explodeval = explode("|", $cvalue);
                                        $statusname = '';
                                        $statustypes = array('current'=>'Current','quit' =>'Quit','never' => 'Never','not_applicable' => 'N/A');
                                        foreach($statustypes as $skey => $stype){
                                            $exp1 = isset($explodeval[1])? $explodeval[1]: '';
                                            if($exp1 == $skey.$set_check_list[0]->field_id):
                                                $statusname = $stype;
                                            endif;
                                        }
                                        $exp0 = isset($explodeval[0])? $explodeval[0]: '';
                                        $exp2 = isset($explodeval[2])? $explodeval[2] : '';
                                        $smokingdata = $exp0.str_repeat('&nbsp;', 5)."Status: ".$statusname. "  ".$exp2;
                                        $fields_array[$i][$set_check_list[0]->title] = $smokingdata;
                                    }else{
                                        $fields_array[$i][ucwords($ckey)] = $cvalue;
                                    }    
                                }elseif($set_check_list[0]->data_type == 10 || $set_check_list[0]->data_type == 11 || $set_check_list[0]->data_type == 38  ){
                                    if(!empty($cvalue)){
                                        $getprovidername = "SELECT  CONCAT(fname,' ',lname) as name FROM users WHERE id = $cvalue";
                                        $stmt6 = $db->prepare($getprovidername) ;
                                        $stmt6->execute();                       
                                        $setprovidername2  = $stmt6->fetchAll(PDO::FETCH_OBJ);
                                        $providername = $setprovidername2[0]->name;
                                        $fields_array[$i][ucwords($ckey)] = ucwords($providername);
                                    }else{
                                        $fields_array[$i][ucwords($ckey)] = ucwords($cvalue);
                                    }    
                                }elseif($set_check_list[0]->data_type == 14 ){
                                    if(!empty($cvalue)){
                                        $getprovidername = "SELECT  CONCAT(fname,' ',lname) as name, username, organization FROM users WHERE id = $cvalue";
                                        $stmt6 = $db->prepare($getprovidername) ;
                                        $stmt6->execute();                       
                                        $setprovidername2  = $stmt6->fetchAll(PDO::FETCH_OBJ);
                                        $title2 = '';
                                        if(!empty($setprovidername2)){
                                            if (empty($setprovidername2[0]->username) ) {
                                               $title2 = $setprovidername2[0]->organization;
                                            }else{
                                                $title2 = $setprovidername2[0]->name;
                                            }
                                        }
                                        $fields_array[$i][ucwords($ckey)] = ucwords($title2);
                                    }else{
                                        $fields_array[$i][ucwords($ckey)] = ucwords($cvalue);
                                    }    
                                }elseif($set_check_list[0]->data_type == 12 ){
                                    if(!empty($cvalue)){
                                        $getpharmacyname = "SELECT  name FROM pharmacies WHERE id = $cvalue";
                                        $stmt6 = $db->prepare($getpharmacyname) ;
                                        $stmt6->execute();                       
                                        $setpharmacyname  = $stmt6->fetchAll(PDO::FETCH_OBJ);
                                        $pharmacyname = $setpharmacyname[0]->name;
                                        $fields_array[$i][ucwords($ckey)] = ucwords($pharmacyname);
                                    }else{
                                        $fields_array[$i][ucwords($ckey)] = ucwords($cvalue);
                                    }    
                                }elseif($set_check_list[0]->data_type == 37 ){
                                    if(!empty($cvalue)){
                                        $getinsurancename = "SELECT  name FROM insurance_companies WHERE id = $cvalue";
                                        $stmt6 = $db->prepare($getinsurancename) ;
                                        $stmt6->execute();                       
                                        $setinsurancename  = $stmt6->fetchAll(PDO::FETCH_OBJ);
                                        $insurancename = $setinsurancename[0]->name;
                                        $fields_array[$i][ucwords($ckey)] = ucwords($insurancename);
                                    }else{
                                        $fields_array[$i][ucwords($ckey)] = ucwords($cvalue);
                                    }    
                                }elseif($set_check_list[0]->data_type == 35 ){
                                    if(!empty($cvalue)){
                                        $getfacilityname = "SELECT  name FROM facility WHERE id = $cvalue";
                                        $stmt6 = $db->prepare($getfacilityname) ;
                                        $stmt6->execute();                       
                                        $setfacilityname  = $stmt6->fetchAll(PDO::FETCH_OBJ);
                                        $facilityname = $setfacilityname[0]->name;
                                        $fields_array[$i][ucwords($ckey)] = ucwords($facilityname);
                                    }else{
                                        $fields_array[$i][ucwords($ckey)] = ucwords($cvalue);
                                    }    
                                }else{
                                    $fields_array[$i][ucwords($ckey)] = $cvalue;
                                } 
                            }else{
                                $fields_array[$i][ucwords($ckey)] = $cvalue;   
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
             $agencydatares = '[{"id":"0","OrganizationId":"0","Organization":"","Name":"","Addressbook_type":"","Abook_type_value":"","Workphone":"","Phonecell":"","Fax":"","Email":"","Street":"","City":"","State":"","Zip":""}]';
             echo $agencydataresult = GibberishAES::enc($agencydatares, $key);
        }
       // echo "<pre>"; print_r($fields_array); echo "</pre>";
    } catch (Exception $ex) {
        $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo $agencydataresult = GibberishAES::enc($error, $key);   
    }
}

function getPatientInsuranceDataList($pid){
    $key = 'rotcoderaclla';
    // The default key size is 256 bits.
    $old_key_size = GibberishAES::size();
    GibberishAES::size(256);
    $sql = "SELECT i.id, i.type as Insurance_Type,(SELECT CONCAT(title, fname,  ' ', lname ) AS name FROM users WHERE id= i.provider) as Provider,(select name from insurance_companies where id = i.provider )as Insurance_Company_Name,i.group_number as Group_Number,i.plan_name as Plan_Name,i.policy_number as Policy_Number,CONCAT(i.subscriber_lname,' ',i.subscriber_mname,' ', i.subscriber_fname) as Subscriber_Name,i.subscriber_relationship as Subscriber_Relationship,
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
            where pid=:pid";
	 
	try 
        {
            $db = getConnection();
            $stmt = $db->prepare($sql) ;
            $db->query( "SET NAMES utf8");
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
                $insurancedatares  = '[{"id":"0","Insurance Type":"primary"},{"id":"0","Insurance Type":"secondary"},{"id":"0","Insurance Type":"tertiary"}]';
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
            $get_fuv = "SELECT facilities, visit_categories FROM tbl_allcare_facuservisit WHERE  `users`  REGEXP ('".":\"".$providerid."\"')";
            $fuv_stmt = $db->prepare($get_fuv) ;
            $fuv_stmt->execute();
            $set_fuv = $fuv_stmt->fetchAll(PDO::FETCH_OBJ); 
            if(!empty($set_fuv)){
                for($i = 0; $i<count($set_fuv); $i++){
                   $array[] =  unserialize( $set_fuv[$i]->visit_categories);
                   $array2[]    =  unserialize( $set_fuv[$i]->facilities);
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
                if(!empty($enc_value)){
                //$sql = "SELECT count(id) as count FROM `form_encounter`  WHERE (`sensitivity` <> 'finalized' OR `sensitivity` IS NULL) AND provider_id=$providerid ";
    //                echo $sql = "SELECT count(f.id) as count FROM `form_encounter` f INNER JOIN patient_data p ON p.pid = f.pid WHERE (`elec_signed_on` = '' AND `elec_signedby` = '')AND p.deceased_stat !=  'YES'
    //                 AND p.practice_status =  'YES' AND p.providerID=$providerid AND pc_catid IN ($enc_value)";
                 $sql = "SELECT count(f.id) as count 
                    FROM `form_encounter` f 
                    INNER JOIN patient_data p ON p.pid = f.pid 
                    WHERE (`elec_signed_on` = '' AND `elec_signedby` = '')AND p.deceased_stat !=  'YES'
                     AND p.practice_status =  'YES' AND f.rendering_provider=$providerid AND f.pc_catid IN ($enc_value) AND f.facility_id IN ($enc_value2)";

                    $stmt = $db->prepare($sql) ;
                    $stmt->execute();                       

                    $count = $stmt->fetchAll(PDO::FETCH_OBJ);                        
                }
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
                    
                    WHERE (
                    `elec_signed_on` = '' AND `elec_signedby` = ''
                    )
                    AND f.rendering_provider =$providerid AND f.facility_id IN ($enc_value2)
                    AND  p.practice_status = 'YES' AND p.deceased_stat != 'YES' AND (p.deceased_date = '' OR p.deceased_date = '0000-00-00 00:00:00' ) AND f.pc_catid IN ($enc_value)
                    GROUP BY p.id ORDER BY p.lname,p.fname ";
                $stmt = $db->prepare($sql) ;
                $stmt->execute();                       

                $count = $stmt->fetchAll(PDO::FETCH_OBJ);                        
            }
            //$count = false;
            if($count)
            {
                //returns count 
                 $countres = json_encode($count); 
                 echo $countresult = GibberishAES::enc($countres, $key);
                 
                 //echo $appres = GibberishAES::dec($countresult, $key);
                 ////die();
            }
            else
            {
                 $countres = '[{"id":"0"}]';
                echo $countresult = GibberishAES::enc($countres, $key);
                ////die();
                //echo $appres = GibberishAES::dec($countresult, $key);
                
                //echo "<br>";
//                echo $crypted = fnEncrypt($countres, $key);
                //echo "Encrypred: ".$crypted."</br>";
                
                //$newClear = fnDecrypt($crypted, $key);
                //echo "Decrypred: ".$newClear."</br>";
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
                form_encounter.rendering_provider as provider_id
                        FROM form_encounter
                        INNER JOIN patient_data ON patient_data.pid = form_encounter.pid
                        WHERE form_encounter.pid =$pid AND form_encounter.pc_catid IN ($enc_value) AND form_encounter.facility_id IN($enc_value2)
                            AND (
                             `elec_signed_on` = '' AND `elec_signedby` = ''
                            ) AND form_encounter.rendering_provider = '$uid' ORDER BY form_encounter.date DESC ";
            
            $stmt = $db->prepare($sql) ;
            $stmt->execute(); 
            $formlabels = $stmt->fetchAll(PDO::FETCH_OBJ); 
            $formfields = array();
            $formvalues = array();
            $datacheck8 = array();
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
function insertMobileLog($event,$user,$comment,$patient,$encounter,$user_notes,$success){    

    $sql = "INSERT INTO mbl_log(date,event,user,groupname,comments,patient_id,encounter_id,user_notes,success) values(NOW(),'".$event."','".$user."','Default','".addslashes($comment)."','".$patient."','".$encounter."','".$user_notes."',$success)";
    $db = getConnection();
    $stmt = $db->prepare($sql);
    $stmt->execute();
    return true;
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
//        $appres = GibberishAES::dec('U2FsdGVkX1+fzhIG4iLmdXdLq0GEtBSW 6RCJYw6BIvrgeunuoB+i7Hl0+huHblGn6lMNh82QyaeJHD3w5zXHMjxI4kEOENxgkkmIyaNYQ9UjO+cDeFDWEh8raxu5UPNJ+C+sDAdOqTSfrIeD6TMfJSTWv7NELkwjS8D3zI5f6G/WmBJNn1pmW0ie4mj9AESsbC3pPHnFi49B+73JOe2s3hVXD7o1RPUJ7u1snE0M5fSVT2BKVWO+vAxYSnlZZyWVilpH8Gz+C9FRRSltLk5SgFzYpQ/OiTQb9SVbh6Z+8bvvW0A3nxtVgfcpwKN/89qtwkgH5m1bsS0wS9CORhzbFMEmr+wifrkFXyoNlnFGYYr2d8AlpyXaIoQCLpV8DfBQD4M5xl5TkOdqHgfdGBPaCzNPQEup4rRrGTH2fWcLt9M3C7x2e56KnxVA4iAHnzI+QJvlFMzg01ZvzD2xLn/GkwDpkDaD7i+kSuPDNTzEdyT52zx1r08jMMCxSlMosSAwpcyv+3pIFQHsCVPb47FsymwxN5xb2n63Qd1ez28HDWJ6AzVfNglquLwnbC/ylrlDf/KxhhyoNp82zxlEppuWYyZe1ndvNBgDQNEpC/TdkFm+aVesuWxGd32N4hR2NO6JPsWlsIl6of+ObEoubkeRfg07NOAFnR75kR+/GZxq6zEf5sUGPLAnP4hOJkbi338CU6IhfSvECkTmximz0uGJUpOF+DsFLz2wJmkTW9xczOaEviW2ngQ5hK1njOUX8ZSocBxvtc6wjHpxA8nQ7pP61RHU23b1eOWPs4Ki6eRp6x6EX6oofZXaSHNVH7KuymsBqR97x/pe+dsHTb9ETDFe4REyX3J2calLbUR9gSI06KJAMNpJYqVO5j9y/SZGCdpH2eBCiimegMEvuLjdAM9yGanQkbycpaaei115Bi4ZRiEOzvgf1sSDe5Us5m0RbOAG+SJfIwt6JLEZwCamdS6kmchDivU3reWzKufahSEEC/8eFEPdDAW3GmE1YWB05fqQ9SdLDi6+XahpFc/fr7oDg7R59Qz+47YEvxA5HQTUxbSOj+2OSRL+d6zamliWclSOFqDEv83J6o4PShwtXyEx5kGmovrrNZBXjkLIpraY/oQ=', $apikey);
        $insertArray = json_decode($appres,TRUE);
//        echo "<pre>"; print_r($insertArray); echo "</pre>";
         
        $group_name             = str_replace('_', ' ', $insertArray['group']);
        $user                   = $insertArray['user'];
        $pid                    = $insertArray['pid'];
        $username2              = getUserName($user);
        $username               = isset($username2[0]['username'])? $username2[0]['username'] : ''; 
        $form_id                = $insertArray['form_id'];
        $encounter              = $insertArray['encounter'];

        $getcolumnNames = "SELECT field_id FROM layout_options WHERE form_id = 'HIS' AND group_name = '$group_name' AND uor <> 0";
        $db->query( "SET NAMES utf8");
        $stmt = $db->prepare($getcolumnNames);
        $stmt->execute();   
        
        $getnames = $stmt->fetchAll(PDO::FETCH_OBJ); 
        foreach ($getnames as $value) { 
            ${$value->field_id} = $insertArray['historydata'][0][$value->field_id];
            $checkname[] =  $value->field_id;
        }
        $nameslist2     = '';
        $namesvalues2   = '';
        $insertval      = 0;
        $data           = 0;
        $columnsql = "select COLUMN_NAME from information_schema.columns where table_name='history_data'";
        $db->query( "SET NAMES utf8");
        $cstmt = $db->prepare($columnsql) ;
        $cstmt->execute();                       

        $dataresultset = $cstmt->fetchAll(PDO::FETCH_OBJ);
        foreach($dataresultset as $key => $value){
            if($key != 'id' )
                $nameslist2 .=  "`".$value->COLUMN_NAME."`,";
        }
        $gethistorydatasql = "SELECT * FROM history_data where pid = ".$pid."  ORDER BY id DESC LIMIT 1 ";
        $db->query( "SET NAMES utf8");
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
                $namesvalues2 .= "'".addslashes(${$key})."',";
            }
        }
        
//        foreach ($getnames as $value) {
//           // $namesvalues2 .= "'".${$value->field_id}."',";
//        }
        $nameslist = rtrim($nameslist2, ',');
        $namesvalues = rtrim($namesvalues2, ',');
        $insert1 = "INSERT INTO history_data ($nameslist) VALUES($namesvalues)";
        $db->query( "SET NAMES utf8");
        $stmt22 = $db->prepare($insert1) ;
        if($stmt22->execute()){
            $insertval = 1;
            insertMobileLog('insert',$username,$insert1,$pid,$encounter,"History form($group_name) Screen",1);
        }else {
            insertMobileLog('insert',$username,$insert1,$pid,$encounter,"History form($group_name) Screen - Failed",0); 
        }
        
        
        if($form_id == 0){
            $lastformid2 = "SELECT MAX(form_id) as forms FROM forms WHERE formdir='LBF2'";
            $db->query( "SET NAMES utf8");
            $stmt5 = $db->prepare($lastformid2) ;
            $stmt5->execute();
            $maxformidval2 = $stmt5->fetchAll(PDO::FETCH_OBJ);
            $form_id =  $maxformidval2[0]->forms + 1;
            
            $insertform2 = "INSERT INTO forms (date,encounter, form_name, form_id, pid, user, groupname, authorized, deleted, formdir) VALUES (NOW(), $encounter, 'Allcare Encounter Forms', $form_id, $pid, '$username', 'Default', 1, 0, 'LBF2' )";
            $db->query( "SET NAMES utf8");
            $stmt4 = $db->prepare($insertform2);
            if($stmt4->execute()){
                insertMobileLog('insert',$username,$insertform2,$pid,$encounter,"New LBF form($group_name) Screen",1);
            }else {
                insertMobileLog('insert',$username,$insertform2,$pid,$encounter,"New LBF form ($group_name) Screen - Failed",0); 
            }
//            $form_id = $db->lastInsertId();
            
        }
        if(!empty($insertArray['lbfdata'][0])){
            foreach($insertArray['lbfdata'][0] as $akey => $avalue){
                if (count($db->query("SELECT * FROM lbf_data WHERE form_id = $form_id AND field_id = '$akey'")->fetchAll())==0) {
                    $sql21 = "INSERT into lbf_data (form_id, field_id, field_value) VALUES ($form_id,'$akey','".addslashes($avalue)."')";
                    $checkinsert = 'insert';
                } else {
                   $sql21 = "UPDATE lbf_data SET field_value = '".addslashes($avalue)."' WHERE field_id ='$akey'  AND form_id = $form_id";
                   $checkinsert = 'update';
                }
                $db->query( "SET NAMES utf8");
                $stmt41 = $db->prepare($sql21);
                if($stmt41->execute()){
                    $data = 1;
                    insertMobileLog($checkinsert,$username,$sql21,$pid,$encounter,"LBF form($group_name) Screen",1);
                }else {
                    $data = 0;
                    insertMobileLog($checkinsert,$username,$sql21,$pid,$encounter,"LBF form ($group_name) Screen - Failed",0); 
                }
            }
        }
        

        if($insertval == 1 && $data == 1){  
            $insertcheck =  '[{"id":"1"}]';
            echo GibberishAES::enc($insertcheck, $apikey);
	}else{
            $insertcheck = '[{"id":"0"}]';
            echo GibberishAES::enc($insertcheck, $apikey);
	}
	  
    }catch(PDOException $e){
            $insertquery = '{"error":{"text":'. $e->getMessage() .'}}'; 
            echo GibberishAES::enc($insertquery, $apikey);
            insertMobileLog('insert/update',$username,$insertquery,$pid,$encounter,"History form Screen($group_name) - Query Failed",0); 
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
                WHERE pid = $pid
                AND o.pc_catid = f.pc_catid and f.encounter <> '$eid'";

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
        
        $sql = "SELECT f.pc_catid, f.rendering_provider as provider_id, f.facility_id, p.pid
                FROM  `form_encounter` f 
                INNER JOIN patient_data p ON p.pid = f.pid
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
            // for procedure order form 
//            $sql2 ="SELECT DISTINCT  l.title AS screen_name,f.screen_group, l.description AS screen_link
//                FROM  `tbl_allcare_facuservisit` f
//                INNER JOIN layout_options l ON l.group_name = f.screen_group
//                AND l.form_id = f.form_id
//                WHERE  `facilities` REGEXP ('".":\"".$value->facility_id."\"') AND  `users` REGEXP ('".":\"".$value->provider_id."\"') AND  `visit_categories` REGEXP ('".":\"".$value->pc_catid."\"') order by f.id ";      
            $sql2 ="SELECT DISTINCT  l.title AS screen_name,f.screen_group, l.description AS screen_link
                FROM  `tbl_allcare_facuservisit` f
                INNER JOIN layout_options l ON l.group_name = f.screen_group
                AND l.form_id = f.form_id
                WHERE  `facilities` REGEXP ('".":\"".$value->facility_id."\"') AND  `users` REGEXP ('".":\"".$value->provider_id."\"') AND  `visit_categories` REGEXP ('".":\"".$value->pc_catid."\"')  order by f.id ";      
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
                            if($datacheck2[0]->field_id == 'procedure'):
                                $sqlpo = "SELECT form_id  from forms WHERE encounter = $eid and deleted = 0 and form_name = 'Procedure Order' and formdir='procedure_order' ";
                                $stmtpo = $db->prepare($sqlpo);
                                $stmtpo->execute();  
                                $datacheckpo = $stmtpo->fetchAll(PDO::FETCH_OBJ);
                                if(!empty($datacheckpo)):
                                    $form_id = $datacheckpo[0]->form_id;
                                else:
                                    $form_id = 0;
                                endif;    
                                if($form_id != 0){
                                    $procedure  = "SELECT field_value FROM lbf_data WHERE form_id = $form_id AND field_id = 'procedure_stat'";
                                    $stmtP = $db->prepare($procedure);
                                    $stmtP->execute();
                                    $set_status = $stmtP->fetchAll(PDO::FETCH_OBJ);
                                    
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
                                       $get_audit_data = preg_replace_callback ( '!s:(\d+):"(.*?)";!',
                                            function($match) {
                                                return ($match[1] == strlen($match[2])) ? $match[0] : 's:' . strlen($match[2]) . ':"' . $match[2] . '";';
                                            },
                                        $set_status[0]->audit_data );
                                        $unserialized_data = array();
                                        if(!empty($get_audit_data)){
                                            $unserialized_data = unserialize($get_audit_data);
                                        }
//                                        print_r($unserialized_data);
                                        if(!empty($unserialized_data['hiddenaudit'])):
                                            if(trim(str_replace('CPT Code:','',$unserialized_data['hiddenaudit'])) == 'None') 
                                                $field_value = 'Not Audited';
                                            else
                                                $field_value = 'Audited';
                                        else:
                                            $field_value = 'Not Audited';
                                        endif;
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
                                    $form_id = $datacheckc[0]->form_id;
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
                                    $form_id = $datacheckcm[0]->form_id;
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
                            if($datacheck[0]->form_name == 'procedure'):
                                $sqlpo = "SELECT form_id  from forms WHERE encounter = $eid and deleted = 0 and form_name = 'Procedure Order' and formdir='procedure_order' ";
                                $stmtpo = $db->prepare($sqlpo);
                                $stmtpo->execute();  
                                $datacheckpo = $stmtpo->fetchAll(PDO::FETCH_OBJ);
                                if(!empty($datacheckpo)):
                                    $datacheck[0]->form_id = $datacheckpo[0]->form_id;
                                else:
                                    $datacheck[0]->form_id = 0;
                                endif;  
                                if($form_id != 0){
                                    $vitals  = "SELECT field_value FROM lbf_data WHERE form_id = $form_id AND field_id = 'procedure_stat'";
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
                                       $get_audit_data = preg_replace_callback ( '!s:(\d+):"(.*?)";!',
                                            function($match) {
                                                return ($match[1] == strlen($match[2])) ? $match[0] : 's:' . strlen($match[2]) . ':"' . $match[2] . '";';
                                            },
                                        $set_status[0]->audit_data );
                                        $unserialized_data = array();
                                        if(!empty($get_audit_data)){
                                            $unserialized_data = unserialize($get_audit_data);
                                        }
//                                        print_r($unserialized_data);
                                        if(!empty($unserialized_data['hiddenaudit'])):
                                            if(trim(str_replace('CPT Code:','',$unserialized_data['hiddenaudit'])) == 'None') 
                                                $field_value = 'Not Audited';
                                            else
                                                $field_value = 'Audited';
                                        else:
                                            $field_value = 'Not Audited';
                                        endif;
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
function getDictationFormData($eid,$formname2){
    try{
        $apikey = 'rotcoderaclla';
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        $formname = str_replace('_', ' ', $formname2);
        $checkform = 1;
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
        $encform="SELECT form_id FROM forms WHERE formdir= 'LBF2' and deleted = 0 and encounter = $eid and pid = $pid order by date asc";
        $encstmt31= $db->prepare($encform) ;
        $encform_id = $encstmt31->execute();
        $encformid_val = $encstmt31->fetchAll(PDO::FETCH_OBJ);
        $patientsreminder = array();   
        if(!empty($encformid_val))
            $formid = $encformid_val[0]-> form_id;
        else
            $formid = 0;
        $patientsreminder = getLayoutGroupSpecificFunction($formid,'LBF2',$formname, $checkform);

        $newdemo4 = encode_demo($patientsreminder);  
        $newdemo['Screen Group Form Fields'] = check_data_available($newdemo4);
        /* ================================ */

         if($newdemo)
        {
            $patientres = json_encode($newdemo); 
//            echo "<pre>"; print_r($newdemo); echo "</pre>";
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
        $apikey = 'rotcoderaclla';
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        $appres = GibberishAES::dec($request->getBody(), $apikey);
        //$appres = GibberishAES::dec('U2FsdGVkX18bRVzGlZFTNCBKyIdV/EEhRQnANw23qt94y1CKYTF3ZnzcbPXE4MxoxMz2rNk1h1K6yb1UH9vOEsNTrdMkMZsdX4EzqRCFsYE35luvDz4/NDtEKDon0SgcQ380JhlA23fIvYLVKtFS8zVItH9gtKeNxFYNn7n4McE5g7DNyX0VkeeNmjKbqAiHZxt0/TMR2RkzkYtdzdoHevYDHVOQrRbSfRoEhCEnzm/glCnmwXpl5eIMQM23NeOiScqn4tL/T0m2b/tIOXuQIR+aGZnB3euxcFXTts1JnWP9AK62gau1oEmNhaJjiRA4vpooKoJDruANZSeJbiFOFv3n+0Y8j0h7D5hUzC4F5qzKuinKnHVw0zTI8ijRworRzTV0gY0uhvMK9vMhv9Twr9BjAmmuAeZvNaIxXl3ToPjI0c9frhuPfJuZ4B5qIRBl', $key);
        $insertArray = json_decode($appres,TRUE);
	
        $encounter               =  $insertArray['encounter'];
        $form_name               =  $insertArray['form_name'];
        $form_id                 =  $insertArray['tempdata'][0]['form_id'];
        $pid                     =  $insertArray['pid'];
        $user                    =  $insertArray['user'];
        $username2               =  getUserName($insertArray['user']);
        $username                =  isset($username2[0]['username'])? $username2[0]['username'] : ''; 
        $authorized              =  1; //$insertArray['authorized'];
        $field_id                =  $insertArray['tempdata'];
        $array = $field_id[1];
        //print_r($data);
        $field_id_data = array();
        $datacheck = 0; 
        foreach ($field_id as $value) {
            foreach ($value as $key => $val) {
                $key1 =  str_replace("dictation", $form_name,$key);// = str_replace("dictation", $form_name,$value->dictation_text);
                $field_id_data[$key1] = $val;
            }    
        }           
        //echo "<pre>";print_r($field_id_data);echo "</pre>";

        if($form_id == 0):
            $lastformid = "SELECT MAX(form_id) as form_id FROM forms WHERE formdir='LBF2'";
            $stmt = $db->prepare($lastformid) ;
            $stmt->execute();
            $maxformidval = $stmt->fetchAll(PDO::FETCH_OBJ);
            $maxformid =  $maxformidval[0]->form_id + 1;
        
            $insertform = "INSERT INTO forms (date,encounter, form_name, form_id, pid, user, groupname, authorized, deleted, formdir) VALUES (NOW(), $encounter, 'Allcare Encounter Forms', $maxformid, $pid, '$username', 'Default', $authorized, 0, 'LBF2' )";
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
                    $sql = "INSERT into lbf_data (form_id, field_id, field_value) VALUES ($newformid,'$key','".addslashes($value)."')";
                    $checkinsert = 'insert';
                } else {
                   $sql = "UPDATE lbf_data SET field_value = '".addslashes($value)."' WHERE field_id ='$key'  AND form_id = $newformid";
                   $checkinsert = 'update';
                }
                $stmt4 = $db->prepare($sql);
                if($stmt4->execute())
                    insertMobileLog($checkinsert,$username,$sql,$pid,$encounter,"LBF form($form_name) Screen",1);
                else 
                    insertMobileLog($checkinsert,$username,$sql,$pid,$encounter,"LBF form ($form_name) Screen - Failed",0);
                $datacheck = 1;
            endforeach;
        else:
            foreach($field_id_data as $key => $value):
                if (count($db->query("SELECT * FROM lbf_data WHERE form_id = $form_id AND field_id = '$key'")->fetchAll())!=0) {
                     $sql = "UPDATE lbf_data SET field_value = '".addslashes($value)."' WHERE field_id ='$key'  AND form_id = $form_id";
                     $checkinsert2 = 'update';
                } else {
                    $sql = "INSERT into lbf_data (form_id, field_id, field_value) VALUES ($form_id,'$key','".addslashes($value)."')"; 
                    $checkinsert2 = 'insert';
                }
                $stmt4 = $db->prepare($sql);
                if($stmt4->execute())
                    insertMobileLog($checkinsert2,$username,$sql,$pid,$encounter,"LBF form($form_name) Screen",1);
                else 
                    insertMobileLog($checkinsert2,$username,$sql,$pid,$encounter,"LBF form ($form_name) Screen - Failed",0);
                $datacheck = 1;
            endforeach;
        endif;
        if($datacheck == 1)
        {
            $patientres = '[{"id":"1"}]';
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
        $patienterror = '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo $patientresult = GibberishAES::enc($patienterror, $apikey);
        insertMobileLog('insert/update',$username,$patienterror,$pid,$encounter,"LBF form ($form_name) Screen - Query Failed",0);
    }
}

//to get all patients from OpenEMR for drop down of mobile app
function getTotalPatients()
{
    
	$sql = "select pid,fname,lname from patient_data  WHERE practice_status = 'YES' AND deceased_stat != 'YES' AND (deceased_date = '' OR deceased_date = '0000-00-00 00:00:00' ) order by lname,fname";      
        $count= "select count(*) from patient_data WHERE practice_status = 'YES' AND deceased_stat != 'YES' AND (deceased_date = '' OR deceased_date = '0000-00-00 00:00:00' ) order by lname,fname";
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
//      $appres = GibberishAES::dec("U2FsdGVkX19jlVxrz9l8MdVff9fUmRLwtaYSMYNnxOuHkc5gL3p1xQ+cZPPc+CJz7teScDKbUaOh4GzEQjWK9Ug192LldzWaHaNT+UK5iHD3uHkUMM3r2DonB4YkDj/H+yRq2mE8uT6HdoZWeaWBQM3nfcBs+6lDyvzqWbWrhYmUuY6kbAqyKaNgfyzcVu3Bnm+4LdREYek+c1d7EtXqxsdPGQTsdHKFyEuXzDCp6ZQyTzLIOBss9sOmYftuYq8WSoBhtYsfiOs8v1LXOOy/mllPE3XW1USQLYVr2EH1uKd3+jyLkd2jU9mesaVB50+LNlmH6lMhrNqVO/Kb+qMlCjyO75tuDFowZ9hi28j9Sdat7CaTTZowSjLz+WdTyYrLqdjV8z81QULTAETxibne9Mqp2XwoQw//0WK6FN8OdixTd+8O6mT1wYlgM+cZHvqe+nJUmQrJvfIMx9A6gp9dennfFvspDRq3BJi1So8JPvwhA+6lziOl/IQPILGAjLJoq+FWrnPNLRNrhLUUG3BpdeZqaw9FjpzSg1Q4o+hKRcbnfoQvjRt7zKTWsDgwOS1sdjke67/dHfSmWqZKIwQ/ezeE48Sp/tHkcJawn2CiWdaN4Yyu1TdiXoSElwDcDHsUJqFUnNXx1F9fDFwNgIeQqg==", $key);
        $insertArray = json_decode($appres,TRUE);
//        print_r($insertArray);
        $formid                      = $insertArray['formid'];
        $encounter                  = $insertArray['encounter'];
        $pid                        = $insertArray['pid'];
        $user                       = $insertArray['loginProviderId'];
        $username2                  = getUserName($insertArray['loginProviderId']);
        $username                   = isset($username2[0]['username'])? $username2[0]['username'] : ''; 
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
        $note                       = addslashes($insertArray['note']);
        $BMI                        = $insertArray['BMI'];
        $BMI_status                 = $insertArray['BMI_status'];
        $waist_circumference_inches = $insertArray['waist_circ'];   
//        $waist_circ_unit            = $insertArray['waist_circ_unit'];   
        $head_circumference_inches  = $insertArray['head_circ'];
//        $head_circ_unit             = $insertArray['head_circ_unit'];
        $oxygen_saturation          = $insertArray['oxygen_saturation'];
        $status                     = $insertArray['vitals_stat'];
        $O2source                   = $insertArray['O2source'];
        $flow_rate                  = $insertArray['O2_flow_rate'];
        $pain_scale                 = $insertArray['pain_scale'];
        
        
        $patientres = '';
        $data = 0;
        $encdate = "SELECT  date FROM form_encounter WHERE encounter = '$encounter'";
        $stmtdate = $db->prepare($encdate) ;
        $stmtdate->execute();
        $endateval = $stmtdate->fetchAll(PDO::FETCH_OBJ);
        $encounter_date = isset($endateval[0]->date)? $endateval[0]->date : '';
        if($formid == 0){
            
            $sqlGetMaxEncounter="SELECT id as form_id FROM sequences";

            $stmt2 = $db->prepare($sqlGetMaxEncounter);
            $stmt2->execute();
            $resMaxEncounter = $stmt2->fetchAll(PDO::FETCH_OBJ);

            if($resMaxEncounter){
                $maxformid = $resMaxEncounter[0]->form_id +1;

                $queryseq = "UPDATE sequences SET id = $maxformid ";
                $db->query( "SET NAMES utf8");
                $stmt_layout = $db->prepare($queryseq);
                if($stmt_layout->execute())
                    insertMobileLog('insert',$username,$queryseq,$pid,$encounter,"Vitals Form Screen",1);
                else
                    insertMobileLog('insert',$username,$queryseq,$pid,$encounter,"Vitals Form Screen - Failed",0);

            }
        
            $insertform = "INSERT INTO forms (date,encounter, form_name, form_id, pid, user, groupname, authorized, deleted, formdir) VALUES (NOW(), '$encounter', 'Vitals', '$maxformid', '$pid', '$username', 'Default', '$authorized', 0, 'vitals' )";
            $stmt2 = $db->prepare($insertform);
            $stmt2->execute();
            $lastId = $db->lastInsertId();
            
            $getformid = "SELECT form_id FROM forms WHERE id = $lastId";
            $stmt3 = $db->prepare($getformid) ;
            $stmt3->execute();
            $formidval = $stmt3->fetchAll(PDO::FETCH_OBJ);
            $newformid =  $formidval[0]->form_id;
            
            $sql = "INSERT INTO `form_vitals` ( `id`, `date`, `pid`, `user`,  `authorized`, `activity`, `bps`, `bpd`, `weight`, `height`, `temperature`, `temp_method`, `pulse`, `respiration`, `note`, `BMI`, `BMI_status`, `waist_circ`, `head_circ`,`oxygen_saturation`,`O2source`,`O2_flow_rate`,`pain_scale`) 
                    VALUES ( '$maxformid', '$encounter_date', '$pid', '$user',  '$authorized', '$activity', '$bp_systolic', '$bp_diastolic', '$weight_lbs',  '$height_inches', '$temperature_fahrenheit','". $temperature_location."', '$pulse', '$respiration','". $note."', '$BMI', '".$BMI_status ."','$waist_circumference_inches','$head_circumference_inches',  '$oxygen_saturation','$O2source','$flow_rate','$pain_scale')";
            $sqlstmt = $db->prepare($sql) ;
            if($sqlstmt->execute()){
               insertMobileLog('insert',$username,$sql,$pid,$encounter,"Vitals Data Screen",1);
               $data = 1;
            }else{
               insertMobileLog('insert',$username,$sql,$pid,$encounter,"Vitals Data Screen - Failed",0);
               $data = 0;
            }
        }else{
            $sql = "UPDATE form_vitals SET     `date` = '$encounter_date',    
                    `bps` = '$bp_systolic', `bpd` = '$bp_diastolic',`weight` = '$weight_lbs',`height` = '$height_inches',
                    `temperature` = '$temperature_fahrenheit',`temp_method`= '".$temperature_location."' ,`pulse` = '$pulse',
                    `respiration` = '$respiration',`note` = '". $note."',`BMI`=  '$BMI' ,`BMI_status` ='". $BMI_status."' ,`waist_circ` = '$waist_circumference_inches',
                    `head_circ` = '$head_circumference_inches',`oxygen_saturation`  = '$oxygen_saturation' ,O2source='$O2source',O2_flow_rate='$flow_rate',pain_scale='$pain_scale'  WHERE `id` = '$formid'";
            $sqlstmt = $db->prepare($sql) ;
            if($sqlstmt->execute()){
               insertMobileLog('update',$username,$sql,$pid,$encounter,"Vitals Data Screen",1);
               $data = 1;
            }else{
               insertMobileLog('update',$username,$sql,$pid,$encounter,"Vitals Data Screen - Failed",0);
               $data = 0;
            }
        }
        $getformid1 = "SELECT form_id FROM forms WHERE formdir= 'LBF2' and deleted = 0 and encounter = '$encounter' and pid = '$pid' order by date asc";
        $stmt31 = $db->prepare($getformid1) ;
        $stmt31->execute();
        $formidval2 = $stmt31->fetchAll(PDO::FETCH_OBJ);

        if(!empty($formidval2)){
             $newformid2 =  $formidval2[0]->form_id;
        }else{
            $lastformid2 = "SELECT MAX(form_id) as forms FROM forms where formdir='LBF2'";
            $stmt5 = $db->prepare($lastformid2) ;
            $stmt5->execute();
            $maxformidval2 = $stmt5->fetchAll(PDO::FETCH_OBJ);
            $maxformid2 =  $maxformidval2[0]->forms + 1; 
            
            $insertform2 = "INSERT INTO forms (date,encounter, form_name, form_id, pid, user, groupname, authorized, deleted, formdir) VALUES (NOW(), $encounter, 'Allcare Encounter Forms', $maxformid2, $pid, '$username', 'Default', $authorized, 0, 'LBF2' )";
            $stmt4 = $db->prepare($insertform2);
            if($stmt4->execute())
                insertMobileLog('insert',$username,$insertform2,$pid,$encounter,"Vitals Data LBF New Form Screen",1);
            else
                insertMobileLog('insert',$username,$insertform2,$pid,$encounter,"Vitals Data LBF New Form Screen - Failed",0);
            $newformid2 = $db->lastInsertId();
            
        }
        if (count($db->query("SELECT * FROM lbf_data WHERE form_id = $newformid2 AND field_id = 'vitals_stat'")->fetchAll())==0) {
            $sql5 = "INSERT into lbf_data (form_id, field_id, field_value) VALUES ($newformid2,'vitals_stat','$status')";
            $statement = 'insert';
        } else {
           $sql5 = "UPDATE lbf_data SET field_value = '$status' WHERE field_id ='vitals_stat'  AND form_id = $newformid2";
           $statement = 'update';
        }
        $stmt41 = $db->prepare($sql5);
        if($stmt41->execute())
            insertMobileLog($statement,$username,$sql5,$pid,$encounter,"Vitals Data LBF Screen",1);
        else
            insertMobileLog($statement,$username,$sql5,$pid,$encounter,"Vitals Data LBF Screen - Failed",0);
           
        if($data == 1){
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
        insertMobileLog('insert/update',$username,$error,$pid,$encounter,"Vitals Data Screen - Query Failed",0);
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
        $pid                        = $insertArray['pid'];
        $form_name                  = $insertArray['form_name'];
        $user                       = $insertArray['loginProviderId'];
        $username2                  = getUserName($insertArray['loginProviderId']);
        $username                   = isset($username2[0]['username'])? $username2[0]['username'] : '';
        $status                     = 'Submitted'; //$insertArray['status'];
        
        
        $logdata = serialize(array( 'authuser' =>$username,'Status' => $status,'date' => date("Y/m/d"), 'action'=>'Created','ip_address'=>'(mobile)' ));
        $sql = "INSERT into `tbl_allcare_formflag`(`form_id`, `encounter_id`, `form_name`, `logdate`) VALUES(0,".$eid.",'$form_name','".$logdata."')";      
        $sqlstmt = $db->prepare($sql) ;
        $data = $sqlstmt->execute();
        if($data){
            $patientres = '[{"id":"1"}]';
            echo $patientresult = GibberishAES::enc($patientres, $key);
            insertMobileLog('delete',$username,$sql,$pid,$eid,"Save Hyperlink($form_name) Screen");
        }else{
            $patientres =  '[{"id":"0"}]';
            echo $patientresult = GibberishAES::enc($patientres, $key);
            insertMobileLog('delete',$username,$sql,$pid,$eid,"Save Hyperlink($form_name) Screen - Failed",1);
        }
    } 
    catch(PDOException $e) 
    {
        $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo $patientresult = GibberishAES::enc($error, $key);
        insertMobileLog('delete',$username,$error,$pid,$eid,"Save Hyperlink($form_name) Screen - Query Failed",0);
    }
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
                $db->query( "SET NAMES utf8");
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
                }else if(strpos($key,'_text',0) !== false){
                    if($formid == 0 && $value == 0)
                        $value = '';
                    ${$key} = $value;
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
                if($key == 'change_in_vision' || $key == 'glaucoma_history' || $key == 'eye_pain' || $key == 'irritation' || $key == 'redness' || $key == 'excessive_tearing' || $key == 'double_vision' || $key == 'blind_spots' || $key == 'photophobia' || $key == 'glaucoma' || $key == 'cataract' || $key == 'injury' || $key == 'ha' || $key =='coryza' || $key == 'obstruction' || $key == 'blurry_vision'){
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
                if($key == 'polyuria' || $key == 'kidney_failure' || $key == 'polydypsia' || $key == 'dysuria' || $key == 'hematuria' || $key == 'frequency' || $key == 'urgency' || $key == 'incontinence' || $key == 'renal_stones' || $key == 'utis' || $key == 'blood_in_urine' || $key == 'urinary_retention' || $key == 'change_in_nature_of_urine' ){
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
                if($key == 'loc' || $key == 'seizures' ||  $key == 'stroke'  || $key == 'tia' || $key == 'n_numbness' || $key == 'n_weakness' || $key == 'paralysis' || $key == 'intellectual_decline' || $key == 'memory_problems' || $key == 'dementia' || $key == 'n_headache' || $key == 'dizziness_vertigo' || $key == 'slurred_speech' || $key =='tremors' || $key == 'migraines' || $key == 'changes_in_mentation' || $key == 'tingling' || $key == 'burning_sensation' ){
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
                if($key == 'thyroid_problems' || $key == 'cirrhosis' || $key == 'diabetes' || $key == 'abnormal_blood' || $key == 'goiter' || $key == 'heat_intolerence' || $key == 'cold_intolerence' || $key == 'increased_thirst' || $key == 'excessive_sweating' || $key == 'excessive_hunger' || $key == 'polyphagia'){
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
                    $values2['selectedvalue'] = str_replace(" ", "_", ${$key."_text"});;
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
                    $data['data']['selectedvalue'] = str_replace(" ", "_", $value);
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
        $nameslist2         = '';
        $namesvalues2       = '';
        $updatenamelist2    = '';
        $flag               = 0;
        $namesarray         = array();
        $request = Slim::getInstance()->request();
        $key = 'rotcoderaclla';
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        $appres = GibberishAES::dec($request->getBody(), $key);
//        $appres = GibberishAES::dec('U2FsdGVkX19+pLZ6+rGb32LtpF5fGG8rrKZH19LKeBztG0Q+lT/fc02hfjWyD3xU1Rnj9oCJpCE20MoJI+/D3tpVLA4IowNkTmP9arJg7sOcPFnOSOfjy6L7wzqgw3JfGEQSpGzP4y/JZRPj/AqKzM5n/CWTg0r3jRiGUQI1SWv5MfZldmn16wSMiEKaH3pSfsP4IAYxYZY0fuXzYOylOKx1c9YORNdIhIqfxOAAUiIQXlS5XI7nqlqjVN77lLsEhqW4GIHyvHUCqQFQWN0r/4r6QC072s8JDSHBNEIKo6pzOe8wW33yL0iy7Kw4OMzv42K1hagKC6QOLgSM+fSHxnQq0Adii+lGvjHxY/yBLR78o5qBdlRmk6aku6W+QeoBJyin62ASB0WokpCh/USgTkjw9+GApirojBDaVRiJqNkt/hI31nKqTllz3tcwc2zRabXuLeV9jVfKRyLJEFNPEysrjB13h3sM4KMb8CNOGVCJi+RS5+o9H9OwIYISd2iwghVe8f/BJbccXI1TMLUeMatPoFhclXGMOztVcjwpK++TeDhrZVNirvaFjTCk93Bfo5MXmCxKi+iHnDszWWOiKkF4/z887zWTh7m376Hfc/f2qxg9fE/i7T2nedyZGuqbrVy7eE1hCV0q3pmTUpW+BZrl0ouVOMZGeprMpca2Cw8Wqi66O4f5jXJHXbTHc2NZxHsqYKUsYSUQU06/ZAEPvPgjTJnnanOgrql9vUIFMuyvmpp90esZyxXIvvxc5LE7B2ng+2zFSPQkYJR90TkU/0JjJx9gBMBomznGmCbLXYXM/KJPXQGo2cxjocryjFmCSU+Mb4Bz8+vdED+J+WiWH+FARt0TMPycx9PbqCF6Rq30CqHH7i7NJ3MCNG9brFBE7Jh98ACDs6eKUzywVTgMjPuYOfV3s3BMCqpbJF1/xBt/hHVW5tGl3T1HvLNsFAAHp9Yj7JtooJHJuse7wc+CuKFGHFiejFsFs+z5kShUSrmJVcFCJXj2NymKYNw7Aef3Yl4W64BZCzFKAqh48j/E1PQqFViQTTC7sYttgugp3bH/shZs/2rvmuB0ap6cUy3zIHVSN+fSa6b3TF8QeirZhuS7yjaalpaJpDUXw0u3RY7i7m4jHdk9kAiG0uDrbuPgf4Y616Plq+YDcgxdixSWk23pEhP1k+y5EWKFq8A0iSnDTovrf3hKU+j6aT2Ah5hLldONBb0sUZE/QrfiG5heiKFW2ch+y/nEbsFZVvYXS4PsQ54xaf/s0Iin6hLM8pkMEW9EZIApXnMklIsDk7C3fWyf+loJkWKV/WApBQ9LzFQqrS0+SnrfrA49f3WuqiZAMV58V8IUt130nLkITu8d84xvXkzBKE4FSpagOvlZtxwjh7DRFtr4G/IYIIo1aWhCnEbPKzdbOYK0962sbxP9izm4UL3EpzqOYRFk+LTyamHj/g+jzN7co87A4jW7wIhRQw3IAM/TQsrEYCZQoqaq21CKPPVhP54iHA33rpWPqIPbm9R3gZCmxtJ0ksHecmeoNHI8A7JnawysgJmQYvYvKYgzd9FgrwAgnAngxYUsYng32wAYQ8ICWdqJyMzkL/MOst5o10ws9OIMl6e4UrywphxaEcFYkmdJucDwGjY1WZsnxks46WJ6zy48RYMvYWNigYWBwegrFRV6l5Gux9ygL+1vqdMRdf1HAn+tW9Oqh9dHNWtMG6TQXkhwG+4ObVyx/jxjNOedcMDWYwJDtFy+tnPuu20mbrMSyVw92TZT0RVs7jaWDUYhfTPfM2Dkg/p4e7ZYntkqfjwm8M7ipclA2FDjZkQj90tzgUPY64/ejEjj6qjQ0w7mvpO16iXdQowGO0EkkQnQd3ygQx1LcFtUEQpI2GHkv51hwupRpKtDKH0hfEhqfTDj4A9+TPQnA9lz10G+nwR+2VymH4ZtGVNH4uMhgAx3RiXDoOxgZPmDA0gWB6VJOMxRWtKBMOGkCCaqRruHpqOdB0Z8vtugzKc7Y5K3DnWNGhvSVVU+0sqshKJyyNG5KhRa+f5rX9xuHm5YCO8ujM0aNEpRpa0+77sicK8ZoBlLUcF/o7J4LV2mogPyea/BfZifu672od0ASf5MQpK///bwdjI2pdyVizfo96pYcxEykgGiv5cZyaYbqQEvt+FTs1P9sT3alW0UoWipqTYjYtVt3ocPnPnyM8YP3eUgZ31rpJHfe+GWfPBEXNqoYBTaONSxslKiKikm6mDqtCiDDGLl3c/FV/7cKQnhWg0G8wa/WS8FXGdHMyn2XdrcQ+CYYBi65a9DXi2lfTSlutahhm9V1+MkRlsckl06fJ9/yczvr0DCPyAizU+y1mUOuZUl80S5Hod2cPV+GiYE4ia/oIJPNDAGzGoIIaP4UmN0W2S9B3+YjoWO1sNAosrY2VX9rHLpllwcZ02N4Ivy3VPCzAZIFb2iLddY7kuRIC/+gELTVvIuccmsCyvdKY9zoTcITBn5qn3jE0EVQxuft+EwZFtP8ggVBf1DFrzpsRFCZ9N8o5M0hWlS6ligHM8d/zo+xygROZz560QDhqmeZtXUDUNA1AlihuRZwYDz0lLEO8E4BDtp9FB3vWYQGlRta5Ia7Ti08VpeomIp8uyTWcPJZgz0U5GZHjTE+FMRyhMMYmeGYfW/H+YIL0Hc1XpOq5+NZvDIj6s7ptHXui5n0p3AAIpaDUk0Unhlkl1d2RFXH1Qqn8PoA8ATOySdzwfeTyV/oselK9L+Qao2DzbNk22O0Xr6KXm9GBRPgtH2LncFi74nVDnhnxtXNGpH2tLNjnwdM9VCZrw0JhXYhbzgJlM+XPlpe+p31L5fxjJqTzYzgBNus8+eBy54r135Hu3efjD4pP9A/kAL7gTK4tpvUMa0PeyCgdQ6g4mX/wl9URn7Na+ApCSxsrm1b88Pv7Ys4WpdfESCmckNO+7nleoIjaqyWaKpP94ekhVNt4cTCkx6Q+P/bZpWHH0qq+DziwRkCmyy5B8LSBwN5UWb2QMrosOoDC4hUUgRjvjMQMSR2VbuDOOmK74e+sv5urLBaN0mlTxhNKLzhXxS+zSQFYSKY5vb59UVUAr0FNs/4lwRoSluPltExd6y/wl3Xxy/mlJPM3b3/29q4U83kN3aBrqjYSGyEyl8cdSBGyHYgQVipgLk0KXcR2N4QswNk6WniavB01ecJOuDuIwMcVYQxpL9QBTCjmvl3LC2b/hYunWEJaVU8nr9a896qad3nFrn2v/5HcLoF9BtQoBla4uo1GFDAg0GGBE29VOYC6r8lN1s3/7jZBAQrm51cEgvVDsgRe5hRpgz3J1DpmTps/48QSi+RKAbwCaPBTEI2+YwsRf/DXt6msHHPjwItbbM6VgplBEOgRzPxcG6tRBUAp4VgtsZVB7Sz+oZEkS6h+NQtoAMtfVdnFFFKpDQww6Lu7DtaHC0LsMl1Wa9rrWiTucDTocsfGCwqoNZtSotU9N+0hZE97GEMBTIyBZ2zxmy76fekFWTKO50wqOlPfpGv8Gq+t377YbMK5yahA6rGp9r2pDvWtG7B4dAAQ+7RzfcjMBnm0tNUnVB0y6Mf87c3XPSWxvrQXGQ/Iq4xq9ViyyZqSpeLgJ1B46Y4enWE9qYtr6DnAwieiREonXAvsTUWYBF5ycvMSt/Z/LVzGrkzasyGruJElLmqnubV80oAb4k++mhMqlBtPbyn8zcAVsKOxba5d6Mq1jIZk3EeoFKUsE5zqg0ZKHXz4LfLIghoqtj1xK/cNWB5zNiJbSZ3G6QQQhN1/ZNBCobdDwOw3SdJPz5Hpi3mCyTgAod+rTIdaOStYPWOdsZmRpQ9vZhWGvYAmmesCSPSX4Nj4mRN8uBOXpU3uiGnFmI9GQ3kw3uDTaGNiYzwPNoJN0EYuf2WqyLtJ+M1hGfZZBzvzJ4GIPu2PYyz8Fql3PAzD1H21Ythi5/nFF+b8bHdw5VggxW+gmPQUkni6I9pIo/81nc9rwN9ETivh6Srk2xfUifz08syHr+MaSS40B1ZbHgraVUrEwMRKQwwbqYeK5elWg0C+Jqub2PhAXdnrmzj0QN8UNDgc1YCBOY9IdNESyzaJT4HPCrGzPc0WKWxbEgtabUha3RCKlDB8qtisMM2675B1sFeEyhJKwpvl6/3b1siS+l4+ThwZv4ExLRzsNpDpwfchyOBhYUZ3w7pa8iacKuThmri8XG4A1JQmSMo945DWzC8sLyYwMjhxtyvc1H/6BS83gQlioZakYsqTiIOosayC2WRKtJEXyWbkFAOPaFDnI/y5wDx2U6n1zR1t/8bH5+rIqkOVJ67HR4Ym8tAHsEYUusUateULhuTWRs8BsdVmgtER1neoWlISuClgIIe24fkqoTbwPdxcId4jHzEMz1p0Kzify7Oy9hPlyf+UUBV1NDjHXXzU4l/LvNzQJb+Z94UWoeY5siZnBDMDD6s3HS2V++G+jfBSnv6gMSXnfSbpe+t5nPrORWVRNs91rk5BVDWyIGLJONWu9RlatNjOTVyd53v/L7qlyBwIuP0ToaBgokz30iNKNo0yCdG1HJr2Lj+bpQQcnvAaA2njk6aBSwW2leJNbb9r1x2ILWMS6H9iu5z+P5v2JBTPMWBh9ZRE7pOco7NDvpDTA1BzFGIRqxdcKCPA4qIh1p3AWbalRdBjt6EUBIfxqhQAYvtGFZZAN6UNXm2+/IY62C9P4HNVDnvxxOstSuN5/M8KrAauL3VIlcHU0l9FiYQHWtfjdXr57aOdNrlZujDkS79ARL/hEGPJLRYYNfMX9Br/DvovJO8lWmpdh110vIJ3igCfyoYLs9aFobjqadgpfApq89As/3LqJbwiHsfASq0VHvAQxAPg2p4O6RxMEtZD9W/L0NFdzoCfEY4wLAToGMOrv6iomm3Jio6JL4q4qheMDWCuoCyQbKBAUA2oDKEVtuI3JoKP+6MAJhvsQe6cjT83CVULgVi9+cy+qBd9wRqSVNI4sun7LG/MCRW4vgifULvEolGIa+MOnL9pxaQJ0nuQ1OOs2vvGhZQql1Iwgi6zPRoWxoNaCsn0C7TbAuiVgvmVf/koPUBWq3mEApVJHd4oJoCLzHn60myGK5OiQCjA7mNTfNnON+xdtrsrCBS2OaJTPs3dMoQbs/aMNZttqJ2KuKaop2gn2jg1GR3xsbgfgWrKQAYBI85eecD5TESY2R7/hz9k4NDAq6oJyuN+dHolXmj6E3NDO7WhiGLHMB4bV6SZU71EjypD6Kg/L6fHHscJIuuyRvCK4bq/LfqqPEx6Sxugc/9KJ2LO68al6ez4bq1dwpkod4GORm9kUK0XGWkyb0QyflfhFg1YbjyIb/d9J+sEuP8pdOFEZncaVmjtqg+r+Nvw9T8ZUGfF+KDSFwApfzE8wzqQTYAA9/y3UOC8UDmIoTJCf5jXe/wvYP3Lc8fenVABZrSi5NXOwTr3fwVvtD7HJ6fvMHuQq5M9cNYP3cKWN568SM1xI6cJdPMdJ6984Gv6mZ2WvEKRJOQTkSI0x0OFd2twQu065hGO2HjwoH9O3zcOuBq9RbiVAKRe/9hkakNBsq4i9FjyHQKG3P9m9wOZRKkBECctGeQCC/SGDXVGma8KACNF+/2fKJWJf1DHHzgeyUrv7LzgLYB5ZqlgHmQn/n/n72klySyOwfPIeBXhzQMYWEg5K2+WpOb5ejd3ZbidABi6SNw8BUL/u6t3rAzdLNPTaAlGv8olLxMEe+S/cWLxlDA8ZY4ChpCDZNSYACXvGAPlxzJ81JcvmI2VAPO7+5Ov2wlM7NMy2vCurAJ4cA500ezqWVwdSgx0Vadu47IxyVnsto7mOK+Mu3vDhks+1oIXmNDnknDTPwV6twribst0Wfyov2UwI8v5vhwJwbjTHnMD+Gmkl75nVcvBv4FG+KHljfTdPouYUn780qoUNoq5Zdv+waup19nfiFjRu9PQLtF5Ata5dN0r3lU4X2ta3dNW3OFRYA/oKricMhtnPBV50loaF5SL8zdoRFqcpxTZU6g9P8OCkVZV2Z92XvzGWAgwrvxO140pB+coL4vdo4u/FZ9rZdZ4MyPOHA4X7JVvmL8Inom/fdQxR4U5h/+E37CZ7t+oAF72/yZvF0wTLLwOpuzXrjpnLbsg75wnOdHrdxRapXkObUFiF4YMLsxdSv5ZT1nl5dllVlit2vWf9207hVQm9WvmcKPDZj+g6pQHF7XjnHaoIzwFCReMQTzjiP9oucYdID3PL1+90uDUbYvVyMwc7npVUv5jg=', $key);
        $insertArray = json_decode($appres,TRUE);
//        echo "<pre>";print_r($insertArray);echo "</pre>";
        $formid     = $insertArray['formid'];
        $encounter  = $insertArray['encounter'];
        $user       = $insertArray['user'];
        
        $username2  = getUserName($insertArray['user']);
        $username   = isset($username2[0]['username'])? $username2[0]['username'] : ''; 

        $authorized = 1;
        $activity   = 1;
        $date       = 'NOW()';
        
        $logdata    = array();
        $array      = array();
        
        $patientres = '';
        $check      = 1;
        $check2     = 1;
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
            
            $sqlGetMaxEncounter="SELECT id as form_id FROM sequences";
 
            $stmt2 = $db->prepare($sqlGetMaxEncounter);
            $stmt2->execute();
            $resMaxEncounter = $stmt2->fetchAll(PDO::FETCH_OBJ);
 
            if($resMaxEncounter){
                $maxformid = $resMaxEncounter[0]->form_id +1;
 
                $queryseq = "UPDATE sequences SET id = $maxformid ";
                $db->query( "SET NAMES utf8");
                $stmt_layout = $db->prepare($queryseq);
                $stmt_layout->execute();
            }
        
        
            $insertform = "INSERT INTO forms (date,encounter, form_name, form_id, pid, user, groupname, authorized, deleted, formdir) VALUES (NOW(), $encounter, 'Allcare Review Of Systems', $maxformid, $pid, '$username', 'Default', $authorized, 0, 'allcare_ros' )";
            $stmt2 = $db->prepare($insertform);
            if($stmt2->execute()){
                insertMobileLog('insert',$username,$insertform,$pid,$encounter,"Allcare Review Of Systems Form Screen",1);
                $check = 1;
            }else{
                insertMobileLog('insert',$username,$insertform,$pid,$encounter,"Allcare Review Of Systems Form Screen - Failed",0);
                $check = 0;
            }
            $lastId = $db->lastInsertId();
            
            $getformid = "SELECT form_id FROM forms WHERE id = $lastId";
            $stmt3 = $db->prepare($getformid) ;
            $stmt3->execute();
            $formidval = $stmt3->fetchAll(PDO::FETCH_OBJ);
            $newformid =  $formidval[0]->form_id;
            
            $insert = "INSERT INTO tbl_form_allcare_ros (id,$nameslist) VALUES($newformid,$namesvalues)";
            $stmt21 = $db->prepare($insert) ;
            if($stmt21->execute()){
                insertMobileLog('insert',$username,$insert,$pid,$encounter,"Allcare Review Of Systems Data Screen",1);
                $check2 = 1;
            }else{
                insertMobileLog('insert',$username,$insert,$pid,$encounter,"Allcare Review Of Systems Data Screen - Failed",0);   
                $check2 = 0;
            }
            
            $count = isset($count)? $count: 0;

            $array2[] = array( 'authuser' =>$username,'pending' => $pending,'finalized' => $finalized, 'date' => date("Y/m/d"), 'action'=>'Created','ip_address'=>'(mobile)' ,'count'=> $count+1);
            $logdata= serialize($array2) ;
            $sql2 = "INSERT into `tbl_allcare_formflag`(`form_id`, `encounter_id`, `form_name`, `pending`,`finalized`,`logdate`) VALUES(".$newformid.",".$encounter.",'Allcare Review of Systems','".$pending."','".$finalized."','".$logdata."')";
            $sqlstmt2 = $db->prepare($sql2) ;
            if($sqlstmt2->execute()){
                insertMobileLog('insert',$username,$sql2,$pid,$encounter,"Allcare Review Of Systems Log Data Screen",1);
            }else{
                insertMobileLog('insert',$username,$sql2,$pid,$encounter,"Allcare Review Of Systems Log Data Screen - Failed",0);   
            }
        else:
            $update = "UPDATE tbl_form_allcare_ros SET $updatenamelist WHERE id = $updateid";
            $stmt21 = $db->prepare($update) ;
            $stmt21->execute(); 
            if($stmt21->execute()){
                insertMobileLog('update',$username,$update,$pid,$encounter,"Allcare Review Of Systems Data Screen",1);
                $check2 = 1;
            }else{
                insertMobileLog('update',$username,$update,$pid,$encounter,"Allcare Review Of Systems Data Screen - Failed",0);   
                $check2 = 0;
            }
            $data2 ="SELECT logdate from `tbl_allcare_formflag` WHERE  form_id='".$formid."'";
            $rstmt2 = $db->prepare($data2) ;
            $rstmt2->execute();
            $data22 = $rstmt2->fetchAll(PDO::FETCH_OBJ);
            foreach ($data22 as $value) {
                $array =  unserialize($value->logdate);
                $count= count($array);
            }
            $count = isset($count)? $count: 0;

            $result2 ="SELECT * FROM tbl_allcare_formflag WHERE `form_name` = 'Allcare Review of Systems' AND `form_id`='".$formid."'";
            $sqlstmt3 = $db->prepare($result2) ;
            $data3 = $sqlstmt3->execute();
            $maxformidval = $sqlstmt3->fetchAll(PDO::FETCH_OBJ);
            if(count($maxformidval) > 0){
                    $array2[] = array( 'authuser' =>$username,'pending' => $pending,'finalized' => $finalized, 'date' => date("Y/m/d"), 'action' => 'Updated','ip_address'=>'(mobile)' ,'count'=> $count+1);
                    $logdata = array_merge_recursive($array, $array2);
                    $logdata= ($logdata? serialize($logdata): serialize($array2) );
                    $sql4 = "UPDATE `tbl_allcare_formflag` SET `finalized`='$finalized',
                    `pending` = '$pending',`logdate` ='".$logdata."'  WHERE `form_name` = 'Allcare Review of Systems' AND `form_id`='".$formid."'";
                    $sqlstmt4 = $db->prepare($sql4) ;
                    if($sqlstmt4->execute()){
                        insertMobileLog('update',$username,$sql4,$pid,$encounter,"Allcare Review Of Systems Log Data Screen",1);
                    }else{
                        insertMobileLog('update',$username,$sql4,$pid,$encounter,"Allcare Review Of Systems Log Data Screen - Failed",0);   
                    }
            }else{ 
                    $array2[] = array( 'authuser' =>$username,'pending' => $pending,'finalized' => $finalized, 'date' => date("Y/m/d"), 'action'=>'Created','ip_address'=>'(mobile)' ,'count'=> $count+1);
                    $logdata = array_merge_recursive($array, $array2);
                    $logdata= ($logdata? serialize($logdata): serialize($array2) );
                    $sql4 = "INSERT into `tbl_allcare_formflag`(`form_id`, `encounter_id`, `form_name`, `pending`,`finalized`,`logdate`) VALUES('".$formid."',".$encounter.",'Allcare Review of Systems','".$pending."','".$finalized."','".$logdata."')";
                    $sqlstmt4 = $db->prepare($sql4) ;
                    if($sqlstmt4->execute()){
                        insertMobileLog('insert',$username,$sql4,$pid,$encounter,"Allcare Review Of Systems Log Data Screen",1);
                    }else{
                        insertMobileLog('insert',$username,$sql4,$pid,$encounter,"Allcare Review Of Systems Log Data Screen - Failed",0);   
                    }
            }
        endif;
        if($check == 1 && $check2 == 1){
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
                        if($sqlstmt5->execute()){
                            insertMobileLog('update',$username,$sql5,$pid,$encounter,"Allcare Review Of Systems LBF Data Screen",1);
                        }else{
                            insertMobileLog('update',$username,$sql5,$pid,$encounter,"Allcare Review Of Systems LBF Data Screen - Failed",0);   
                        }
                    }else {
                        $sql5 = "INSERT into `lbf_data`(`form_id`, `field_id`,`field_value`) VALUES(".$encformid_val[0]->form_id.",'ros_stat','".$formstatus."')";
                        $sqlstmt5 = $db->prepare($sql5) ;
                        if($sqlstmt5->execute()){
                            insertMobileLog('insert',$username,$sql5,$pid,$encounter,"Allcare Review Of Systems LBF Data Screen",1);
                        }else{
                            insertMobileLog('insert',$username,$sql5,$pid,$encounter,"Allcare Review Of Systems LBF Data Screen - Failed",0);   
                        }
                    }
           endif;
        }
        

        if($check == 1 && $check2 == 1){  
            $insertcheck =  '[{"id":"1"}]';
            echo GibberishAES::enc($insertcheck, $key);
	}else{
            $insertcheck = '[{"id":"0"}]';
            echo GibberishAES::enc($insertcheck, $key);
	}
	  
    }catch(PDOException $e){
            $insertquery = '{"error":{"text":'. $e->getMessage() .'}}'; 
            echo GibberishAES::enc($insertquery, $key);
            insertMobileLog('insert/update',$username,$insertquery,$pid,$encounter,"Allcare Review Of Systems Screen - Query Failed",0);  
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
                   $name = str_replace($val,'Able To Answer Questions Appropriately',$val);
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
                $data['data']['abn']['name'] = 'ABNL';
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
//        $appres = GibberishAES::dec('U2FsdGVkX1+STXqLtIpwQvJPfK1RC8Wkm5vsqOpwm7Sfh3We1vDBaQ7pnAxQ8ptJQ1KLUHMDQNPjwUStgFkjEEPL5vIzZr92ioNyj7j7+IpSZ2tbWwk9p9wNCv0jx9bjO8RzNSHPxpawSYF+6vGdpPCtwnXhNdqP9pAiFygdxzn+iR9xRE5lXxBnW2xnS8IsgU1CaAboCstLsIINZE0qaimquo5T5/jSAiW/rJD79Jsgox9GgHPDQpdgfBVvtw658Uu8gAgHoTf1Nwio8OaB3DCsSJY3NCsRF6KhbwZPuSNWi8KAy2OHgiUSPsmAv1foMa+la50POY2i0g2JwfcXyswymXOPF0xxCIeQIkQn2HvBxJdpr2fk03T2osT2Bk+i', $key);
        $insertArray = json_decode($appres,TRUE);
//        $nameslist = array('GENWELL', 'GENAWAKE','HEADNORM', 'HEADLESI', 'EYECP','EYECON','EYEPER','ENTTM','ENTNASAL','ENTORAL','ENTSEPT','NECKSUP','BACKCUR','CVRRR','CVNTOH','CVCP','CVNPE','CVNMU','CVS1S2','CHNSD','RECTAB','REEFF','RELUN','READV','GIOG','GIHERN','GISOFT','GIBOW','GUTEST','GUPROS','GUEG','GUAD','GULES','GUDEF','EXTREMIT','EXTREDEF','EXTREPED','LYAD','MUSTR','MUROM','MUSTAB','MUINSP','MUTEND','NEUCN2','NEUREF','NEUSENS','NEULOCAL','NEUGROSS','PSYAFF','PSYJUD','PSYDEP','PSYSLE','PSYTHO','PSYAPP','PSYABL','SKRASH','SKCLEAN','SKNAIL','OTHER');
        $nameslist                  = $insertArray['data'][0];
//        echo "<pre>";print_r($insertArray);echo "</pre>";
        $formid                     = $insertArray['formid'];
        $encounter                  = $insertArray['encounter'];
        $pid                        = $insertArray['pid'];
        $user                       = $insertArray['user'];
        $username2                  = getUserName($insertArray['user']);
        $username                   = isset($username2[0]['username'])? $username2[0]['username'] : ''; 
        $authorized                 = 1; 
        $activity                   = 1 ;

        $finalized  = $insertArray['data'][0]['finalized']; 
        $pending  =  $insertArray['data'][0]['pending']; 
        $array = array();
        $logdata= array();
//        $ip_addr=GetIP();
        $patientres = '';
        $datacheck = 0;
        if($formid == 0){
            
            $lastformid = "SELECT MAX(form_id) as form_id FROM forms where formdir='allcare_physical_exam'";
            $stmt = $db->prepare($lastformid) ;
            $stmt->execute();
            $maxformidval = $stmt->fetchAll(PDO::FETCH_OBJ);
            $maxformid =  $maxformidval[0]->form_id + 1;
        
            $insertform = "INSERT INTO forms (date,encounter, form_name, form_id, pid, user, groupname, authorized, deleted, formdir) VALUES (NOW(), $encounter, 'Allcare Physical Exam', $maxformid, $pid, '$username', 'Default', $authorized, 0, 'allcare_physical_exam' )";
            $stmt2 = $db->prepare($insertform);
            if($stmt2->execute()){
                insertMobileLog('insert',"$username",$insertform,$pid,$encounter,"Create New Physical Exam Form Screen", 1);
            }else{
                insertMobileLog('insert',"$username",$insertform,$pid,$encounter,"Create New Physical Exam Form Screen - Failed", 0);
            }
            $lastId = $db->lastInsertId();
            
            $getformid = "SELECT form_id FROM forms WHERE id = $lastId";
            $stmt3 = $db->prepare($getformid) ;
            $stmt3->execute();
            $formidval = $stmt3->fetchAll(PDO::FETCH_OBJ);
            $newformid =  $formidval[0]->form_id;
            
            foreach($nameslist as $namekey => $namevalue){
                if($namekey != 'pending' && $namekey != 'finalized'){
                    $valuedata  = explode('|',$namevalue);
                    $value      = $namekey; //$valuedata[0];
                    $wnl        = $valuedata[0];
                    $abn        = $valuedata[1];
                    $diagnosis  = $valuedata[2];
                    $comments   = addslashes(trim($valuedata[3],"'"));

                    $sql = "INSERT INTO tbl_form_physical_exam (forms_id, line_id, wnl, abn, diagnosis, comments) VALUES($newformid,'$value', $wnl, $abn, $diagnosis, '$comments' )"; 
                    $sqlstmt = $db->prepare($sql) ;
                    if($sqlstmt->execute()){
                        $data = 1;
                        insertMobileLog('insert',"$username",$sql,$pid,$encounter,"Insert New Physical Exam Form Screen", 1);
                    }else{
                        $data = 0;
                        insertMobileLog('insert',"$username",$sql,$pid,$encounter,"Insert New Physical Exam Form Screen - Failed", 0);
                    }
                    $datacheck = 1;
                }
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
            $array2[] = array( 'authuser' =>$username,'pending' => $pending1,'finalized' => $finalized1, 'date' => date("Y/m/d"), 'action'=>'created','ip_address'=>'mobile' ,'count'=> $count+1);
            $logdata= serialize($array2) ;
            $sql2 = "INSERT into `tbl_allcare_formflag`(`form_id`, `encounter_id`, `form_name`, `pending`,`finalized`,`logdate`) VALUES(".$newformid.",".$encounter.",'Allcare Physical Exam','".$pending1."','".$finalized1."','".$logdata."')";
            $sqlstmt2 = $db->prepare($sql2) ;
            if($sqlstmt2->execute()){
                $data = 1;
                insertMobileLog('insert',"$username",$sql2,$pid,$encounter,"Update Physical Exam Log Form Screen", 1);
            }else{
                $data = 0;
                insertMobileLog('insert',"$username",$sql2,$pid,$encounter,"Update Physical Exam Log Form Screen - Failed", 0);
            }
            
            
             $encform="SELECT form_id FROM forms WHERE formdir= 'LBF2' and deleted = 0 and encounter = $encounter and pid = $pid order by date asc";
            $encstmt31= $db->prepare($encform) ;
            $encform_id = $encstmt31->execute();
            $encformid_val = $encstmt31->fetchAll(PDO::FETCH_OBJ);
        }else{ 
            foreach($nameslist as $namekey => $namevalue){
                if($namekey != 'pending' && $namekey != 'finalized'){
                    $valuedata  = explode('|',$namevalue);
                    $value      = $namekey; //$valuedata[0];
                    $wnl        = $valuedata[0];
                    $abn        = $valuedata[1];
                    $diagnosis  = $valuedata[2];
                    $comments   = addslashes(trim($valuedata[3],"'"));

                    $get_sql = "SELECT * 
                                FROM tbl_form_physical_exam
                                WHERE forms_id =$formid
                                AND line_id =  '$value'"; 
                    $getsqlstmt = $db->prepare($get_sql) ;
                    $getdata =  $getsqlstmt->execute();
                    $getdata_res = $getsqlstmt->fetchAll(PDO::FETCH_OBJ);
                    if(!empty($getdata_res)){
                        $sql = "UPDATE tbl_form_physical_exam SET wnl = $wnl , abn = $abn , diagnosis = $diagnosis , comments = '$comments'
                                WHERE line_id = '$value' AND forms_id = $formid";
                        $sqlstmt = $db->prepare($sql) ;
                        if($sqlstmt->execute()){
                            $data = 1;
                            insertMobileLog('update',"$username",$sql,$pid,$encounter,"Update Physical Exam Data Form Screen", 1);
                        }else{
                            $data = 0;
                            insertMobileLog('update',"$username",$sql,$pid,$encounter,"Update Physical Exam Data Form Screen - Failed", 0);
                        }
                    }else{
                        $sql = "INSERT INTO tbl_form_physical_exam (forms_id, line_id, wnl, abn, diagnosis, comments) VALUES($formid,'$value', $wnl, $abn, $diagnosis, '$comments' )"; 
                        $sqlstmt = $db->prepare($sql) ;
                        if($sqlstmt->execute()){
                            $data = 1;
                            insertMobileLog('update',"$username",$sql,$pid,$encounter,"Insert Physical Exam Data Form Screen", 1);
                        }else{
                            $data = 0;
                            insertMobileLog('update',"$username",$sql,$pid,$encounter,"Insert Physical Exam Data Form Screen - Failed", 0);
                        }
                    }
                    $datacheck = 1;
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
                    $array2[] = array( 'authuser' =>$username,'pending' => $pending1,'finalized' => $finalized1, 'date' => date("Y/m/d"), 'action' => 'updated','ip_address'=>'mobile' ,'count'=> $count+1);
                    $logdata = array_merge_recursive($array, $array2);
                    $logdata= ($logdata? serialize($logdata): serialize($array2) );
                    $sql4 = "UPDATE `tbl_allcare_formflag` SET `finalized`='$finalized1',
                    `pending` = '$pending1',`logdate` ='".$logdata."'  WHERE `form_name` = 'Allcare Physical Exam' AND `form_id` =  ".$formid;
                    $sqlstmt4 = $db->prepare($sql4) ;
                    if($sqlstmt4->execute()){
                        $data = 1;
                        insertMobileLog('update',"$username",$sql4,$pid,$encounter,"Update Physical Exam Data Log Screen", 1);
                    }else{
                        $data = 0;
                        insertMobileLog('update',"$username",$sql4,$pid,$encounter,"Update Physical Exam Log Form Screen - Failed", 0);
                    }
            }else{ 
                    $array2[] = array( 'authuser' =>$username,'pending' => $pending1,'finalized' => $finalized1, 'date' => date("Y/m/d"), 'action'=>'created','ip_address'=>'mobile' ,'count'=> $count+1);
                    $logdata = array_merge_recursive($array, $array2);
                    $logdata= ($logdata? serialize($logdata): serialize($array2) );
                    $sql4 = "INSERT into `tbl_allcare_formflag`(`form_id`, `encounter_id`, `form_name`, `pending`,`finalized`,`logdate`) VALUES(".$formid.",".$encounter.",'Allcare Physical Exam','".$pending1."','".$finalized1."','".$logdata."')";
                    $sqlstmt4 = $db->prepare($sql4) ;
                    if($sqlstmt4->execute()){
                        $data = 1;
                        insertMobileLog('insert',"$username",$sql4,$pid,$encounter,"Insert Physical Exam Log Form Screen", 1);
                    }else{
                        $data = 0;
                        insertMobileLog('insert',"$username",$sql4,$pid,$encounter,"Insert Physical Exam Log Form Screen - Failed", 0);
                    }
            }
        }
        if($datacheck == 1){
            $formstatus = '';
            if(trim($pending) == 'yes')
                $formstatus = 'pending';
            elseif(trim($finalized) == 'yes')
                $formstatus = 'finalized';
            
            if(trim($finalized) == 'yes'  && trim($pending) == 'yes')
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
                    if($sqlstmt5->execute()){
                        $data = 1;
                        insertMobileLog('update',"$username",$sql5,$pid,$encounter,"Update Physical Exam Data LBF Screen", 1);
                    }else{
                        $data = 0;
                        insertMobileLog('update',"$username",$sql5,$pid,$encounter,"Update Physical Exam LBF Form Screen - Failed", 0);
                    }
                }else {
                   $sql5 = "INSERT into `lbf_data`(`form_id`, `field_id`,`field_value`) VALUES(".$encformid_val[0]->form_id.",'physical_exam_stat','".$formstatus."')";
                   $sqlstmt5 = $db->prepare($sql5) ;
                   if($sqlstmt5->execute()){
                        $data = 1;
                        insertMobileLog('insert',"$username",$sql5,$pid,$encounter,"Insert Physical Exam Data LBF Screen", 1);
                    }else{
                        $data = 0;
                        insertMobileLog('insert',"$username",$sql5,$pid,$encounter,"Insert Physical Exam LBF Form Screen - Failed", 0);
                    }
                }
            else:
                $lastformid2 = "SELECT MAX(form_id) as forms FROM forms where formdir='LBF2'";
                $stmt5 = $db->prepare($lastformid2) ;
                $stmt5->execute();
                $maxformidval2 = $stmt5->fetchAll(PDO::FETCH_OBJ);
                $maxformid2 =  $maxformidval2[0]->forms + 1;

                $insertform2 = "INSERT INTO forms (date,encounter, form_name, form_id, pid, user, groupname, authorized, deleted, formdir) VALUES (NOW(), $encounter, 'Allcare Encounter Forms', $maxformid2, $pid, '$username', 'Default', 1, 0, 'LBF2' )";
                $stmt4 = $db->prepare($insertform2);
                $stmt4->execute();
                $newformid2 = $db->lastInsertId();
                if (count($db->query("SELECT * FROM lbf_data WHERE form_id = $maxformid2 AND field_id = 'physical_exam_stat'")->fetchAll())==0) {
                    $sql2 = "INSERT into lbf_data (form_id, field_id, field_value) VALUES ($maxformid2,'physical_exam_stat','$formstatus')";
                    $checkstring = 'insert';
                } else {
                   $sql2 = "UPDATE lbf_data SET field_value = '$formstatus' WHERE field_id ='physical_exam_stat'  AND form_id = $maxformid2";
                   $checkstring = 'update';
                }

                $stmt4 = $db->prepare($sql2);
                if($stmt4->execute()){
                    $data = 1;
                    insertMobileLog($checkstring,"$username",$sql2,$pid,$encounter,"Physical Exam Data LBF Screen", 1);
                }else{
                    $data = 0;
                    insertMobileLog($checkstring,"$username",$sql2,$pid,$encounter,"Physical Exam LBF Form Screen - Failed", 0);
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
        insertMobileLog('insert/update',"$username",$error,$pid,$encounter,"Physical Exam Form Screen - Query Failed", 0);
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
        $patientsreminder = array();
        $checkform = 0; 
        $patientsreminder = getLayoutGroupSpecificFunction($formid,'LBF2','Face to Face HH Plan', $checkform);

        $dataarray = $patientsreminder;
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
    $username2                  = getUserName($user);
    $username                   = isset($username2[0]['username'])? $username2[0]['username'] : ''; 
    $billing_facility           = $insertArray[0]['billing_facility'];
    $pc_facility                = $insertArray[0]['pc_facility'];
    $encDate                    = date('Y-m-d H:i:s', strtotime(str_replace('-','/', $insertArray[0]['encDate'])));  
    $pc_catid                   = $insertArray[0]['category']; 
    $pc_title                   = $insertArray[0]['pc_catname']; 
    $pc_apptstatus              = $insertArray[0]['pc_apptstatus']; 
    $pc_eid                     = $insertArray[0]['apptid'];
    
    try{
        
        $encCheck  = "SELECT encounter FROM form_encounter WHERE pid = ".$pid . "
                                        AND date = '$date'";
        $fstmtenc = $db->prepare($encCheck);
        $fstmtenc->execute();
        $encCount = $fstmtenc->fetchAll(PDO::FETCH_OBJ);
        if(!empty($encCount))
            $encCount = $encCount[0]->encounter;
        else
             $encCount = 0;
        if($encCount == 0){
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

            $sql10 = "INSERT INTO form_encounter (date, facility, facility_id, pid, encounter, pc_catid, provider_id, billing_facility,rendering_provider)
            VALUES ('$date', '$facility',$pc_facility,$pid,$encounter,$pc_catid,$user,$billing_facility,'$user')";

            $q = $db->prepare($sql10);

            if($q->execute()){
                $lastId = $db->lastInsertId(); 
                $sqlGetLastEncounter="SELECT MAX(encounter) as encounter, form_encounter.id, username FROM form_encounter INNER JOIN users ON form_encounter.provider_id = users.id WHERE pid=$pid AND form_encounter.provider_id=$user AND form_encounter.id = '$lastId'";

                //$db = getConnection();
                $stmt = $db->prepare($sqlGetLastEncounter) ;
                $stmt->execute();
                $newEnc = $stmt->fetchAll(PDO::FETCH_OBJ);
                insertMobileLog('insert',"$username",$sql10,$pid,$encounter,"INSERT Encounter Form Data Screen", 1);
                
                $insertform = "INSERT INTO forms (date, encounter, form_name, form_id, pid, user, groupname, authorized, deleted, formdir) VALUES('$date',".$newEnc[0]->encounter.",'New Patient Encounter',".$newEnc[0]->id.",$pid,'".$newEnc[0]->username."','Default',1, 0,'newpatient')";
                $stmt3 = $db->prepare($insertform) ;
                if($stmt3->execute()){
                    insertMobileLog('insert',"$username",$insertform,$pid,$encounter,"Create New Encounter Screen", 1);
                }else{
                    insertMobileLog('insert',"$username",$insertform,$pid,$encounter,"Create New Encounter Screen - Failed", 0);
                }
                //$newform = $stmt3->fetchAll(PDO::FETCH_OBJ);

                $updatestatus = "UPDATE openemr_postcalendar_events SET pc_catid = $pc_catid, pc_title= '$pc_title', pc_apptstatus = '$pc_apptstatus'  WHERE pc_eid=$pc_eid ";
                $stmt4 = $db->prepare($updatestatus) ;
                if($stmt4->execute()){
                    insertMobileLog('update',"$username",$updatestatus,$pid,$encounter,"Save Appointment Status Data Screen", 1);
                }else{
                    insertMobileLog('update',"$username",$updatestatus,$pid,$encounter,"Save Appointment Status Data Screen - Failed", 0);
                }
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

                            $array2[] = array( 'authuser' =>$username,'Status' => 'Incomplete', 'date' => date("Y/m/d"), 'action'=>'Created', 'ip_address'=>'(mobile)','count'=> $count+1);
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

                            $array2[] = array( 'authuser' =>$username,'Status' => 'Incomplete', 'date' => date("Y/m/d"), 'action'=>'Updated' ,'ip_address'=>'(mobile)','count'=> $count+1);
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
        }else {
            $newencres = '[{"id":"'.$encCount.'"}]'; 
            echo $newencresult = GibberishAES::enc($newencres, $key);
        }
    } catch (Exception $ex) {
        $error =  '{"error":{"text":'. $ex->getMessage() .'}}'; 
        echo $datares = GibberishAES::enc($error, $key);
        insertMobileLog('insert/update',"$username",$error,$pid,$encounter,"Create new Encounter from Appointment Screen - Query Failed", 0);
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
//    $appres = GibberishAES::dec('U2FsdGVkX1/MqMw+EpyLcZWFbDoApIhsDyWus9xznDzB5aCTksW5r9qLBN0JiZ8r0Gwfej4TPsXMzxejtZCdnndeshPyz9PMvEWjH27DNfs=', $key);
    $insertArray                = json_decode($appres,TRUE);
    $username                   = $insertArray['username'];
    try 
    {
        $patientsreminders = 0;
        for($i=0; $i<count($insertArray['data']); $i++){
            $fieldname                  = $insertArray['data'][$i]['fieldname']; 
            $order                      = $insertArray['data'][$i]['order'];  
            $diagnosis                  = $insertArray['data'][$i]['diagnosis'];
            
            $deletesql = "DELETE FROM tbl_form_physical_exam_diagnoses WHERE line_id = '".$fieldname."'  AND ordering= '$order'";      
            $dstmt = $db->prepare($deletesql) ;
            if($dstmt->execute()){
                insertMobileLog('delete',"$username",$deletesql,'','',"Delete Physical Exam Diagnosis Screen", 1);
            }else{
                insertMobileLog('delete',"$username",$deletesql,'','',"Delete Physical Exam Diagnosis Screen - Failed", 0);
            }
        
            $sql = "INSERT INTO tbl_form_physical_exam_diagnoses ( line_id, ordering, diagnosis ) VALUES ( '$fieldname', '$order', '$diagnosis' )";      
            $stmt = $db->prepare($sql) ;
            if($stmt->execute()){
                $patientsreminders = 1;
                insertMobileLog('insert',"$username",$sql,'','',"Add Physical Exam Diagnosis Screen", 1);
            }else{
                $patientsreminders = 0;
                insertMobileLog('insert',"$username",$sql,'','',"Add Physical Exam Diagnosis Screen- Failed", 0);
            }
            
        }
//        $patientsreminders = $stmt->fetchAll(PDO::FETCH_OBJ);                        

        if($patientsreminders == 1)
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
        insertMobileLog('insert',"$username",$result,'','',"Add Physical Exam Diagnosis Screen- Query Failed", 0);
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
                    $gethistorydata = "SELECT `".$fieldnamesarray->field_id ."` FROM history_data WHERE pid = $patientId order by date desc limit 1";
                    $db->query( "SET NAMES utf8"); 
                    $stmt3 = $db->prepare($gethistorydata); 
                    $stmt3->execute();
                    $sethistorydata = $stmt3->fetchAll(PDO::FETCH_OBJ); 
                    if($sethistorydata != ''){
                        for($i=0; $i< count($sethistorydata); $i++){
                            foreach($sethistorydata[$i] as $key => $sethis){ 
                                if(!empty($sethis)){
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
                                                    $slashed_data  = addslashes($explodelist2[0]) ;
                                                    $getvalname = "SELECT title FROM list_options WHERE option_id =  '$slashed_data' AND list_id = '$fieldnamesarray->list_id'";
                                                    $db->query( "SET NAMES utf8"); 
                                                    $stmt6 = $db->prepare($getvalname) ;
                                                    $stmt6->execute();                       
                                                    $setvalname2  = $stmt6->fetchAll(PDO::FETCH_OBJ);  
                                                    foreach($setvalname2 as $setvalname){
                                                        $text = '';
                                                        $text = '';
                                                        if($explodelist2[1] != 0 || !empty($explodelist2[2])){
                                                            if(!empty($explodelist2[2])){
                                                               $text =  " - (".$explodelist2[2].")";
                                                            }
                                                            if($explodelist2[1] == 1)
                                                                $type = 'Normal';
                                                            else
                                                                $type = 'Abnormal';
                                                            $data[substr($setgroupnames->group_name,1)][$setvalname->title] = $type.$text;
                                                        }
    //                                                    if($explodelist2[1] == 0){
    //                                                        $type = 'N/A';
    //                                                        if(!empty($explodelist2[2])){
    //                                                           $text =  "(".$explodelist2[2].")";
    //                                                        }
    //                                                        $natype .= $setvalname->title.$text.",";
    //                                                    }elseif($explodelist2[1] == 1){
    //                                                        $type = 'Normal';
    //                                                        if(!empty($explodelist2[2])){
    //                                                           $text =  "(".$explodelist2[2].")";
    //                                                        }
    //                                                        $ntype .= $setvalname->title.$text.",";
    //                                                    }elseif($explodelist2[1] == 2){
    //                                                        $type = 'Abnormal';
    //                                                        if(!empty($explodelist2[2])){
    //                                                           $text =  "(".$explodelist2[2].")";
    //                                                        }
    //                                                        $atype .= $setvalname->title.$text.",";
    //                                                    }
                                                    }
                                                }
    //                                            $data[substr($setgroupnames->group_name,1)]['N/A'] = rtrim($natype, ',');
    //                                            $data[substr($setgroupnames->group_name,1)]['Normal'] = rtrim($ntype, ',');
    //                                            $data[substr($setgroupnames->group_name,1)]['Abnormal'] = rtrim($atype, ',');
                                            }else{
                                                $data[substr($setgroupnames->group_name,1)][$title] = $sethis;
                                            }    
                                        }elseif($fieldnamesarray->data_type == 32  ){
                                            if(!empty($sethis)){
                                                $titlefield = '';
                                                $explodeval = explode("|", $sethis);
                                                $title = '';
                                                if(!empty($explodeval[3])){
                                                    $slashes_value = addslashes($explodeval[3]);
                                                    $getvalname = "SELECT title FROM list_options WHERE option_id =  '$slashes_value' AND list_id = '$fieldnamesarray->list_id'";
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
                                                $exp0 = isset($explodeval[0])? $explodeval[0]: '';
                                                $exp2 = isset($explodeval[2])? $explodeval[2] : '';
                                                $smokingdata = $exp0.str_repeat('&nbsp;', 5)."Status: ".$statusname. "  ".$exp2;
                                                //$smokingdata = $titlefield.$explodeval[0].str_repeat('&nbsp;', 5)."Status: ".$statusname. "  ".$explodeval[2];
                                                if(empty($titlefield))
                                                    $data[substr($setgroupnames->group_name,1)][$fieldnamesarray->title] = $smokingdata;
                                                else
                                                    $data[substr($setgroupnames->group_name,1)][$titlefield] = $smokingdata;
                                            }else{
                                                $data[substr($setgroupnames->group_name,1)][$title] = $sethis;
                                            }    
                                        }elseif($fieldnamesarray->data_type == 22 ){
                                            $smokingdata = '';
                                            if(!empty($sethis)){
                                                $titlefield = '';
                                                $explodeval = explode("|", $sethis);
                                                foreach($explodeval as $tlist){
                                                    $explodeval = explode(":", $tlist);
                                                    $title = '';
                                                    if(!empty($explodeval[1])){
                                                        $slashes_value = addslashes($explodeval[0]);
                                                        $getvalname = "SELECT title FROM list_options WHERE option_id =  '$slashes_value' AND list_id = '$fieldnamesarray->list_id'";
                                                        $db->query( "SET NAMES utf8"); 
                                                        $stmt6 = $db->prepare($getvalname) ;
                                                        $stmt6->execute();                       
                                                        $setvalname2  = $stmt6->fetchAll(PDO::FETCH_OBJ);
                                                        foreach($setvalname2 as $setvalname){
                                                            $titlefield = $setvalname->title."&nbsp;";
                                                        }
                                                    }
                                                    if(!empty($titlefield) && !empty($explodeval[1]))
                                                        $smokingdata .= "<b>".$titlefield.str_repeat('&nbsp;', 2).":</b>  ".$explodeval[1]."<br />";
                                                }
                                                $data[substr($setgroupnames->group_name,1)][$fieldnamesarray->title] = $smokingdata;
                                            }else{
                                                $data[substr($setgroupnames->group_name,1)][$title] = $sethis;
                                            }    
                                        }elseif($fieldnamesarray->data_type == 25 ){
                                            $smokingdata = '';
                                            if(!empty($sethis)){
                                                $titlefield = '';
                                                $explodeval = explode("|", $sethis);
                                                foreach($explodeval as $tlist){
                                                    $checkstring = '';
                                                    $explodeval = explode(":", $tlist);
                                                    $title = '';
                                                    if(!empty($explodeval[1]) || !empty($explodeval[2])){
                                                        $slashes_value = addslashes($explodeval[0]);
                                                        $getvalname = "SELECT title FROM list_options WHERE option_id =  '$slashes_value' AND list_id = '$fieldnamesarray->list_id'";
                                                        $db->query( "SET NAMES utf8"); 
                                                        $stmt6 = $db->prepare($getvalname) ;
                                                        $stmt6->execute();                       
                                                        $setvalname2  = $stmt6->fetchAll(PDO::FETCH_OBJ);
                                                        foreach($setvalname2 as $setvalname){
                                                            $titlefield = $setvalname->title."&nbsp;";
                                                        }
                                                    }
                                                    if($explodeval[1] == 1)
                                                        $isyes = 'YES';
                                                    else
                                                        $isyes = 'NO';
                                                    if(!empty($explodeval[2]) )
                                                        $checkstring = $isyes. " " . $explodeval[2]."<br />";
                                                    else if(empty($explodeval[2]) && $isyes == "YES" )
                                                        $checkstring = $isyes. "<br />";
                                                    if(!empty($checkstring))
                                                        $smokingdata .= "<b>".$titlefield.str_repeat('&nbsp;', 2).":</b>".$checkstring; 
                                                }
                                                $data[substr($setgroupnames->group_name,1)][$fieldnamesarray->title] = $smokingdata;
                                            }else{
                                                $data[substr($setgroupnames->group_name,1)][$title] = $sethis;
                                            }    
                                        }else{
                                            if(!empty($sethis)){
                                                $explodeval = explode("|", $sethis);
                                                $arraydata = '';
                                                for($i=0; $i< count($explodeval); $i++){
                                                    $slashed = addslashes($explodeval[$i]);
                                                    $getvalname = "SELECT title FROM list_options WHERE option_id =  '$slashed' AND list_id = '$fieldnamesarray->list_id'";
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
                                                    $exp1 = isset($explodeval[1])? $explodeval[1]: '';
                                                    if($exp1 == $key.$fieldnamesarray->field_id):
                                                        $statusname = $stype;
                                                    endif;
                                                }
                                                $exp0 = isset($explodeval[0])? $explodeval[0]: '';
                                                $exp2 = isset($explodeval[2])? $explodeval[2] : '';
                                                $smokingdata = $exp0.str_repeat('&nbsp;', 5)."Status: ".$statusname. "  ".$exp2;
                                                $data[substr($setgroupnames->group_name,1)][$fieldnamesarray->title] = $smokingdata;
                                            }else{
                                                $data[substr($setgroupnames->group_name,1)][$title] = $sethis;
                                            }    
                                        }elseif($fieldnamesarray->data_type == 10 || $fieldnamesarray->data_type == 11 || $fieldnamesarray->data_type == 38  ){
                                            if(!empty($sethis)){
                                                $getprovidername = "SELECT CONCAT(fname,' ',lname) as name FROM users WHERE id = $sethis";
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
                                                if(!empty($setprovidername2)){
                                                    if (empty($setprovidername2[0]->username) ) {
                                                       $title2 = $setprovidername2[0]->organization;
                                                    }else{
                                                        $title2 = $setprovidername2[0]->name;
                                                    }
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
                                                $getinsurancename = "SELECT name FROM insurance_companies WHERE id = $sethis";
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
                                                $getfacilityname = "SELECT name FROM facility WHERE id = $sethis";
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
                    }
                    $listname  = '';
                }
            }
        }
//        echo "<pre>"; print_r($data);echo "</pre>";
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
            $db->query( "SET NAMES utf8");
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
                    $db->query( "SET NAMES utf8");
                    //$gethistorydata = "SELECT ".$fieldnamesarray->field_id ." FROM patient_data WHERE pid = $patientId order by date desc limit 1";
                    $stmt3 = $db->prepare($getDemographicsData); 
                    $stmt3->execute();
                    $sethistorydata = $stmt3->fetchAll(PDO::FETCH_OBJ); 
                    if($sethistorydata != ''){
                        for($i=0; $i< count($sethistorydata); $i++){
                            foreach($sethistorydata[$i] as $key => $sethis){
                                if(!empty($sethis)){
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
                                                for($j= 0; $j< count($explodeval2); $j++){
                                                    $explodelist2 = explode(":", $explodeval2[$j]);
                                                    $slashed_data  = addslashes($explodelist2[0]) ;
                                                    $getvalname = "SELECT title FROM list_options WHERE option_id =  '$slashed_data' AND list_id = '$fieldnamesarray->list_id'";
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
                                                    $slashes_value = addslashes($explodeval[3]);
                                                    $getvalname = "SELECT title FROM list_options WHERE option_id =  '$slashes_value' AND list_id = '$fieldnamesarray->list_id'";
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
                                        }elseif($fieldnamesarray->data_type == 22 ){
                                            $smokingdata = '';
                                            if(!empty($sethis)){
                                                $titlefield = '';
                                                $explodeval = explode("|", $sethis);
                                                foreach($explodeval as $tlist){
                                                    $explodeval = explode(":", $tlist);
                                                    $title = '';
                                                    if(!empty($explodeval[1])){
                                                        $slashes_value = addslashes($explodeval[0]);
                                                        $getvalname = "SELECT title FROM list_options WHERE option_id =  '$slashes_value' AND list_id = '$fieldnamesarray->list_id'";
                                                        $db->query( "SET NAMES utf8"); 
                                                        $stmt6 = $db->prepare($getvalname) ;
                                                        $stmt6->execute();                       
                                                        $setvalname2  = $stmt6->fetchAll(PDO::FETCH_OBJ);
                                                        foreach($setvalname2 as $setvalname){
                                                            $titlefield = $setvalname->title."&nbsp;";
                                                        }
                                                    }
                                                    if(!empty($titlefield))
                                                        $smokingdata .= "<b>".$titlefield.str_repeat('&nbsp;', 2).":</b>  ".$explodeval[1]."<br />";
                                                }
                                                $data[substr($setgroupnames->group_name,1)][$fieldnamesarray->title] = $smokingdata;
                                            }else{
                                                $data[substr($setgroupnames->group_name,1)][$title] = $sethis;
                                            }    
                                        }elseif($fieldnamesarray->data_type == 25 ){
                                            $smokingdata = '';
                                            if(!empty($sethis)){
                                                $titlefield = '';
                                                $explodeval = explode("|", $sethis);
                                                foreach($explodeval as $tlist){
                                                    $checkstring = '';
                                                    $explodeval = explode(":", $tlist);
                                                    $title = '';
                                                    if(!empty($explodeval[1]) || !empty($explodeval[2])){
                                                        $slashes_value = addslashes($explodeval[0]);
                                                        $getvalname = "SELECT title FROM list_options WHERE option_id =  '$slashes_value' AND list_id = '$fieldnamesarray->list_id'";
                                                        $db->query( "SET NAMES utf8"); 
                                                        $stmt6 = $db->prepare($getvalname) ;
                                                        $stmt6->execute();                       
                                                        $setvalname2  = $stmt6->fetchAll(PDO::FETCH_OBJ);
                                                        foreach($setvalname2 as $setvalname){
                                                            $titlefield = $setvalname->title."&nbsp;";
                                                        }
                                                    }
                                                    if($explodeval[1] == 1)
                                                        $isyes = 'YES';
                                                    else
                                                        $isyes = 'NO';
                                                    if(!empty($explodeval[2]) )
                                                        $checkstring = $isyes. " " . $explodeval[2]."<br />";
                                                    else if(empty($explodeval[2]) && $isyes == "YES" )
                                                        $checkstring = $isyes. "<br />";
                                                    if(!empty($checkstring))
                                                        $smokingdata .= "<b>".$titlefield.str_repeat('&nbsp;', 2).":</b>".$checkstring; 
                                                }
                                                $data[substr($setgroupnames->group_name,1)][$fieldnamesarray->title] = $smokingdata;
                                            }else{
                                                $data[substr($setgroupnames->group_name,1)][$title] = $sethis;
                                            }    
                                        }else{
                                            if(!empty($sethis)){
                                                $explodeval = explode("|", $sethis);
                                                $arraydata = '';
                                                for($j=0; $j< count($explodeval); $j++){
                                                    $slashed = addslashes($explodeval[$j]); 
                                                    $getvalname = "SELECT title FROM list_options WHERE option_id =  '$slashed' AND list_id = '$fieldnamesarray->list_id'";
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
                                                    $exp1 = isset($explodeval[1])? $explodeval[1]: '';

                                                    if($exp1 == $key.$fieldnamesarray->field_id):
                                                        $statusname = $stype;
                                                    endif;
                                                }
                                                $exp0 = isset($explodeval[0])? $explodeval[0]: '';
                                                $exp2 = isset($explodeval[2])? $explodeval[2] : '';
                                                $smokingdata = $exp0.str_repeat('&nbsp;', 5)."Status: ".$statusname. "  ".$exp2;
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
                                                if(!empty($setprovidername2)){
                                                    if (empty($setprovidername2[0]->username) ) {
                                                       $title2 = $setprovidername2[0]->organization;
                                                    }else{
                                                        $title2 = $setprovidername2[0]->name;
                                                    }
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
                                            if($title == 'fname')
                                                $data[substr($setgroupnames->group_name,1)]['First_Name'] = $sethis;
                                            elseif($title == 'mname')
                                                $data[substr($setgroupnames->group_name,1)]['Middle_Name'] = $sethis;
                                            elseif($title == 'lname')
                                                $data[substr($setgroupnames->group_name,1)]['Last_Name'] = $sethis;
                                            else{
                                                if($fieldnamesarray->data_type == 4){
                                                if($sethis == '0000-00-00 00:00:00' || $sethis == '0000-00-00' || $sethis == '')
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
                    }   
                    $listname  = '';
                }
            }
        }
//        echo "<pre>"; print_r($data);echo "</pre>";
        $newdemo1 = encode_demo($data);  
        $newdemo['Demographics'] = check_data_available($newdemo1);
        //exit();
        if($newdemo){
           $hisres = json_encode($newdemo,JSON_FORCE_OBJECT);
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
        $encounter      = $insertArray['encounter'];
        $signeddate     = $insertArray['date'];
        $username       = $insertArray['username'];
        $pid            = $insertArray['pid'];
        
        $esign = "UPDATE form_encounter SET elec_signedby = $providerId , elec_signed_on =  '$signeddate' WHERE encounter = $encounter AND pid = $pid";
        $stmt = $db->prepare($esign) ;
        
        if($stmt->execute()){  
            $insertcheck =  '[{"id":"1"}]';
            echo GibberishAES::enc($insertcheck, $apikey);
            insertMobileLog('update',$username,$esign,$pid,'','Save Finalize Encounter Screen',1);
	}else{
            $insertcheck = '[{"id":"0"}]';
            echo GibberishAES::enc($insertcheck, $apikey);
            insertMobileLog('update',$username,$esign,$pid,'','Save Finalize Encounter Screen - Failed',0);
	}
	  
    }catch(PDOException $e){
        $insertquery = '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo GibberishAES::enc($insertquery, $apikey);
        insertMobileLog('update',$username,$insertquery,$pid,'','Save Finalize Encounter Screen - Query Failed',0);
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
        
        $Id         = $insertArray['id'];
        $pid        = $insertArray['pid'];
        $username   = $insertArray['username'];
        $encounter  = $insertArray['encounter'];
        
        $end_date = "UPDATE lists SET enddate = NOW() WHERE id = $Id";
        $stmt = $db->prepare($end_date) ;
        
        if($stmt->execute()){  
            $insertcheck =  '[{"id":"1"}]';
            echo GibberishAES::enc($insertcheck, $apikey);
            insertMobileLog('update',$username,$end_date,$pid,$encounter,'Save Medical Problem Enddate Screen',1);
	}else{
            $insertcheck = '[{"id":"0"}]';
            echo GibberishAES::enc($insertcheck, $apikey);
            insertMobileLog('update',$username,$end_date,$pid,$encounter,'Save Medical Problem Enddate Screen - Failed',0);
	}
	  
    }catch(PDOException $e){
        $insertquery = '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo GibberishAES::enc($insertquery, $apikey);
        insertMobileLog('update',$username,$insertquery,$pid,$encounter,'Save Medical Problem Enddate Screen - Query Failed',0);
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
function MobileAllPatientFilters(){
    $apikey = 'rotcoderaclla';
    // The default key size is 256 bits.
    $old_key_size = GibberishAES::size();
    GibberishAES::size(256);
    $sql = "select option_id,title from list_options WHERE list_id = 'Mobile_All_Patients_Filters' order by seq";      

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
function MobileMyPatientFilters(){
    $apikey = 'rotcoderaclla';
    // The default key size is 256 bits.
    $old_key_size = GibberishAES::size();
    GibberishAES::size(256);
    $sql = "select option_id,title from list_options WHERE list_id = 'Mobile_My_Patients_Filters' order by seq";      

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
function MobileFacilityFilters(){
    $apikey = 'rotcoderaclla';
    // The default key size is 256 bits.
    $old_key_size = GibberishAES::size();
    GibberishAES::size(256);
    $sql = "select option_id,title from list_options WHERE list_id = 'Mobile_Facility_Filters' order by seq";      

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
function MobileAppointmentFilters(){
    $apikey = 'rotcoderaclla';
    // The default key size is 256 bits.
    $old_key_size = GibberishAES::size();
    GibberishAES::size(256);
    $sql = "select option_id,title from list_options WHERE list_id = 'Mobile_Appointment_Due_Filters' order by seq";      

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
        $getsql  = "SELECT openAppdate,refill_due,last_physician,calc_next_visit,hh_certification, ctopenAppDate, calc_next_ct, h_p, hpopenAppDate, calc_next_hp, awv_required, awvopenAppDate, calc_next_awv, sudo_required, sudoopenAppDate, calc_next_sudo, cpo, calc_next_sp, spopenAppDate, ccm, ccmopenAppDate, calc_next_ccm
            FROM patient_data
            WHERE pid ='$pid'";
        $sql_stmt = $db->prepare($getsql) ;
        $sql_stmt->execute();  
        $screen_filters_names = $sql_stmt->fetchAll(PDO::FETCH_OBJ); 
        if(!empty($screen_filters_names)){
            $now = time();
            $name = '';
            $sql_refil = "SELECT title FROM layout_options WHERE form_id = 'DEM' AND field_id = 'refill_due'";
            $sql_refil = $db->prepare($sql_refil) ;
            $sql_refil->execute();  
            $sql_refil_title = $sql_refil->fetchAll(PDO::FETCH_OBJ);
            
            $dataarray[isset($sql_refil_title[0]->title)?$sql_refil_title[0]->title :'refill_due'] = $screen_filters_names[0]->refill_due;
            
            $sql_lp = "SELECT title FROM layout_options WHERE form_id = 'DEM' AND field_id = 'last_physician'";
            $sql_lpt = $db->prepare($sql_lp) ;
            $sql_lpt->execute();  
            $sql_lp_title = $sql_lpt->fetchAll(PDO::FETCH_OBJ);
            
            $dataarray[isset($sql_lp_title[0]->title)?$sql_lp_title[0]->title :'last_physician'] = $screen_filters_names[0]->last_physician;
            
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
function getAuditForm($formid, $pid,$encounter){
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
//            $audit_data = unserialize($get_audit[0]->audit_data); 
            $get_audit_data = preg_replace_callback ( '!s:(\d+):"(.*?)";!',
                function($match) {
                    return ($match[1] == strlen($match[2])) ? $match[0] : 's:' . strlen($match[2]) . ':"' . $match[2] . '";';
                },
            $get_audit[0]->audit_data );
            $unserialized_data = array();
            if(!empty($get_audit_data)){
                $audit_data = unserialize($get_audit_data);
            }else{
                $audit_data = array();
            }

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
                        elseif($key == 'defaultcpo')
                            $defaultcpo = $value;
                        elseif($key == 'defaultccm')
                            $defaultccm = $value;
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
        }else{
            $defaultcpo = '';
            $defaultccm = '';
        }
        $get_cpo_ccm_stat = "SELECT cpo,ccm FROM patient_data WHERE pid = $pid";
        $db->query( "SET NAMES utf8"); 
        $stmt_key_stat = $db->prepare($get_cpo_ccm_stat) ;
        $stmt_key_stat->execute();                       
        $set_key_name2 = $stmt_key_stat->fetchAll(PDO::FETCH_OBJ); 
        $cpo_yes = $ccm_yes = '';
        if(!empty($set_key_name2)){
            $cpo_yes = $set_key_name2[0]->cpo;
            $ccm_yes = $set_key_name2[0]->ccm;
        }
        if($defaultcpo == ''){
            if($cpo_yes == 'YES'){
                $defaultcpo = 30;
            }
        }
        if($defaultccm == ''){
            if($ccm_yes == 'YES'){
                $defaultccm = 20;
            }
        }
//        $get_names = "SELECT encounter FROM forms WHERE formdir = 'auditform' AND form_id = $formid order by date desc limit 0,1 ";
//        $db->query( "SET NAMES utf8"); 
//        $stmt_key5 = $db->prepare($get_names) ;
//        $stmt_key5->execute();                       
//        $set_key_name5 = $stmt_key5->fetchAll(PDO::FETCH_OBJ); 
//        if(!empty($set_key_name5)){
//            $encounter = $set_key_name5[0]->encounter;
//        }
//        if(!empty($encounter)){

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
                                END as isRequired,seq,description,max_length FROM layout_options WHERE form_id = 'ESIGN' AND group_name LIKE '%Chart Electronically Signed By' order by group_name, seq";
            $db->query( "SET NAMES utf8");
            $stmt_layout2 = $db->prepare($get_layout2) ;
            $stmt_layout2->execute();                       
            $layout_fields2 = $stmt_layout2->fetchAll(PDO::FETCH_OBJ);
            if(!empty($layout_fields2)){
                $patientsreminder['ESIGN']['form_id'] = $formid;
                for($i=0; $i< count($layout_fields2); $i++){
                    $sql = "select ".$layout_fields2[$i]->field_id." as field_title from form_encounter where encounter= '$encounter' AND pid = '$pid'";   
                    $db->query( "SET NAMES utf8");
                    $stmt = $db->prepare($sql) ;
                    $stmt->execute();                       

                    $patientsreminders = $stmt->fetchAll(PDO::FETCH_OBJ);   
                    $pvalue = isset($patientsreminders[0]->field_title)? $patientsreminders[0]->field_title: '';
                    
//                    $patientsreminder[substr($layout_fields2[$i]->group_name,1)][$i]['group_name'] = $layout_fields2[$i]->group_name;
                    $patientsreminder['ESIGN'][$i]['field_id'] = $layout_fields2[$i]->field_id;
                    $patientsreminder['ESIGN'][$i]['data_type'] =  $layout_fields2[$i]->field_type;
                    $patientsreminder['ESIGN'][$i]['isRequired'] =  $layout_fields2[$i]->isRequired;
                    $patientsreminder['ESIGN'][$i]['title']  = $layout_fields2[$i]->title;
                    $patientsreminder['ESIGN'][$i]['list_id']  = $layout_fields2[$i]->list_id;
                    $patientsreminder['ESIGN'][$i]['seq']  = $layout_fields2[$i]->seq;
                    $patientsreminder['ESIGN'][$i]['placeholder']  =$layout_fields2[$i]->description;
                    if($layout_fields2[$i]->max_length == 0)
                        $maxlength = '';
                    else
                        $maxlength = $layout_fields2[$i]->max_length;
                    $patientsreminder['ESIGN'][$i]['max_length']  = $maxlength;
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
                    $patientsreminder['ESIGN'][$i]['options_list']  = $titles;
                    if($layout_fields2[$i]->list_id != ''){
                        $patientsreminder['ESIGN'][$i]['selected_value'] = $pvalue;
                    }else{
                        if($layout_fields2[$i]->field_type == 'Static Text'){
                        $patientsreminder['ESIGN'][$i]['selected_value'] = $layout_fields2[$i]->description;
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
    //                        $data['options_list'] = $data2; 
                        $patientsreminder['ESIGN'][$i]['options_list']  = $data2;
                        $patientsreminder['ESIGN'][$i]['selected_value'] = $pvalue;
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
    //                        $data['options_list'] = $data2; 
                        $patientsreminder['ESIGN'][$i]['options_list']  = $data2;
                        $patientsreminder['ESIGN'][$i]['selected_value'] = $pvalue;
                    }else if($layout_fields2[$i]->field_type == 'Organizations'){
                        $sql3 = "SELECT id, fname, lname, organization, username FROM users WHERE active = 1 AND ( info IS NULL OR info NOT LIKE '%Inactive%' ) AND ( username = '' OR authorized = 1 ) ORDER BY organization, lname, fname";
                        $db->query( "SET NAMES utf8"); 
                        $stmt3 = $db->prepare($sql3) ;
                        $stmt3->execute();                       
                        $getListData = $stmt3->fetchAll(PDO::FETCH_OBJ);  
                        if(!empty($getListData)):
                            $data2 = array();
                            $j = 0;
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
    //                        $data['options_list'] = $data2; 
                        $patientsreminder['ESIGN'][$i]['options_list']  = $data2;
                        $patientsreminder['ESIGN'][$i]['selected_value'] = $pvalue;
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
                        $patientsreminder['ESIGN'][$i]['selected_value'] = $data2;
                    }else if($layout_fields2[$i]->field_type == 'Lifestyle status'){
                        $patientsreminder['ESIGN'][$i]['selected_value'] = $pvalue;
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
    //                        $data['options_list'] = $data2; 
                        $patientsreminder['ESIGN'][$i]['options_list']  = $data2;
                        $patientsreminder['ESIGN'][$i]['selected_value'] = $pvalue;
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
    //                        $data['options_list'] = $data2; 
                        $patientsreminder['ESIGN'][$i]['options_list']  = $data2;
                        $patientsreminder['ESIGN'][$i]['selected_value'] = $pvalue;
                    }else{
                        $patientsreminder['ESIGN'][$i]['selected_value'] = $pvalue;
                    }
                    }
                }
            }
//            $patientsreminder = getLayoutSpecificOptionSpecificFunction($formid, 'ESIGN','Chart Electronically Signed By',$encounter,$pid);
            // for cpo, ccm and cert/recert
            $sql_cpo = "SELECT form_id FROM forms WHERE formdir='cpo' AND encounter= $encounter AND pid= $pid AND deleted= 0 order by date desc limit 0,1";

            $db->query( "SET NAMES utf8"); 
            $stmt_cpo = $db->prepare($sql_cpo) ;
            $stmt_cpo->execute();                       
            $getListDatacpo = $stmt_cpo->fetchAll(PDO::FETCH_OBJ);  
            $cpo_form_id = '';
            if(!empty($getListDatacpo)){
                $cpo_form_id = $getListDatacpo[0]->form_id;
            }
            $get_cpo_logged_mins = "SELECT cpo_data FROM tbl_form_cpo WHERE id = '$cpo_form_id'";
            $db->query( "SET NAMES utf8"); 
            $stmt_cpo_data = $db->prepare($get_cpo_logged_mins) ;
            $stmt_cpo_data->execute();                       
            $getListDatacpo_data = $stmt_cpo_data->fetchAll(PDO::FETCH_OBJ);  
            $get_cpo_data = '';
            if(!empty($getListDatacpo_data)){
                $get_cpo_data = $getListDatacpo_data[0]->cpo_data;
            }
            $cpo_data2 = preg_replace_callback ( '!s:(\d+):"(.*?)";!',
                function($match) {
                    return ($match[1] == strlen($match[2])) ? $match[0] : 's:' . strlen($match[2]) . ':"' . $match[2] . '";';
                },
            $get_cpo_data );

            $cpo_data = unserialize($cpo_data2);
            $timeinterval_val_cpo2 = 0;
            
            for($i=1; $i<= count($cpo_data); $i++){
                if(!empty($cpo_data[$i-1])){
                    $cpotitle = '';
//                    $defaultcpo = $cpo_data['defaultcpo'] ;
                    foreach ($cpo_data[$i-1] as $cpokey => $cpovalue) {
                        if($cpokey == 'timeinterval' && $cpovalue != ''):
                            $ures = "SELECT title FROM list_options WHERE option_id= '$cpovalue' and list_id = 'Time_Interval'";
                            $db->query( "SET NAMES utf8"); 
                            $stmt_ures = $db->prepare($ures) ;
                            $stmt_ures->execute();                       
                            $getList_ures = $stmt_ures->fetchAll(PDO::FETCH_OBJ);
                            
                            if(!empty($getList_ures)){
                                $cpotitle = $getList_ures[0]->title;
                            }
                            if($cpotitle == ''):
                                $cpotitle = 0;
                            endif;
                            $timeinterval_val_cpo2 = $timeinterval_val_cpo2 + $cpotitle;
                        endif;
                    }
                }
            }
            // cpo
            $patientsreminder['CPO'][0]['field_id']         = 'defaultcpo';
            $patientsreminder['CPO'][0]['data_type']        = 'Textbox';
            $patientsreminder['CPO'][0]['isRequired']       = 'Optional';
            $patientsreminder['CPO'][0]['selected_value']   = $defaultcpo;
            $patientsreminder['CPO'][0]['title']            = 'Default CPO minutes';
            $patientsreminder['CPO'][0]['placeholder']      = '';

            $patientsreminder['CPO'][1]['field_id']         = '';
            $patientsreminder['CPO'][1]['data_type']        = 'Static Text';
            $patientsreminder['CPO'][1]['isRequired']       = 'Optional';
            $patientsreminder['CPO'][1]['selected_value']   = $timeinterval_val_cpo2;
            $patientsreminder['CPO'][1]['title']            = 'Logged CPO minutes';
            
            $patientsreminder['CPO'][2]['field_id']         = '';
            $patientsreminder['CPO'][2]['data_type']        = 'Static Text';
            $patientsreminder['CPO'][2]['isRequired']       = 'Optional';
            $patientsreminder['CPO'][2]['selected_value']   = '';
            $patientsreminder['CPO'][2]['title']            = 'Remaining Minutes';
            
            // ccm
            
            $sql_ccm = "SELECT form_id FROM forms WHERE formdir='ccm' AND encounter= $encounter AND pid= $pid AND deleted= 0 order by date desc limit 0,1";

            $db->query( "SET NAMES utf8"); 
            $stmt_ccm = $db->prepare($sql_ccm) ;
            $stmt_ccm->execute();                       
            $getListDataccm = $stmt_ccm->fetchAll(PDO::FETCH_OBJ);  
            $ccm_form_id = '';
            if(!empty($getListDataccm)){
                $ccm_form_id = $getListDataccm[0]->form_id;
            }
            $get_ccm_logged_mins = "SELECT ccm_data FROM tbl_form_ccm WHERE id = '$ccm_form_id'";
            $db->query( "SET NAMES utf8"); 
            $stmt_ccm_data = $db->prepare($get_ccm_logged_mins) ;
            $stmt_ccm_data->execute();                       
            $getListDataccm_data = $stmt_ccm_data->fetchAll(PDO::FETCH_OBJ);  
            $get_ccm_data = '';
            if(!empty($getListDataccm_data)){
                $get_ccm_data = $getListDataccm_data[0]->ccm_data;
            }
            $ccm_data2 = preg_replace_callback ( '!s:(\d+):"(.*?)";!',
                function($match) {
                    return ($match[1] == strlen($match[2])) ? $match[0] : 's:' . strlen($match[2]) . ':"' . $match[2] . '";';
                },
            $get_ccm_data );

            $ccm_data = unserialize($ccm_data2);
            $timeinterval_val_ccm2 = 0;
            for($i=1; $i<= count($ccm_data); $i++){
                if(!empty($ccm_data[$i-1])){
                    $ccmtitle = '';
//                    $defaultccm = $ccm_data['defaultccm'] ;
                    foreach ($ccm_data[$i-1] as $ccmkey => $ccmvalue) {
                        if($ccmkey == 'timeinterval' && $ccmvalue != ''):
                            $ures = "SELECT title FROM list_options WHERE option_id= '$ccmvalue' and list_id = 'Time_Interval'";
                            $db->query( "SET NAMES utf8"); 
                            $stmt_ures = $db->prepare($ures) ;
                            $stmt_ures->execute();                       
                            $getList_ures = $stmt_ures->fetchAll(PDO::FETCH_OBJ);
                            
                            if(!empty($getList_ures)){
                                $ccmtitle = $getList_ures[0]->title;
                            }
                            if($ccmtitle == ''):
                                $ccmtitle = 0;
                            endif;
                            $timeinterval_val_ccm2 = $timeinterval_val_ccm2 + $ccmtitle;
                        endif;
                    }
                }
            }
            $patientsreminder['CCM'][0]['field_id']         = 'defaultccm';
            $patientsreminder['CCM'][0]['data_type']        = 'Textbox';
            $patientsreminder['CCM'][0]['selected_value']   = $defaultccm;
            $patientsreminder['CCM'][0]['isRequired']       = 'Optional';
            $patientsreminder['CCM'][0]['title']            = 'Default CCM minutes';
            $patientsreminder['CCM'][0]['placeholder']      = '';

            $patientsreminder['CCM'][1]['field_id']         = '';
            $patientsreminder['CCM'][1]['data_type']        = 'Static Text';
            $patientsreminder['CCM'][1]['selected_value']   = $timeinterval_val_ccm2;
            $patientsreminder['CCM'][1]['isRequired']       = 'Optional';
            $patientsreminder['CCM'][1]['title']            = 'Logged CCM minutes';
            
            $patientsreminder['CCM'][2]['field_id']         = '';
            $patientsreminder['CCM'][2]['data_type']        = 'Static Text';
            $patientsreminder['CCM'][2]['selected_value']   = '';
            $patientsreminder['CCM'][2]['isRequired']       = 'Optional';
            $patientsreminder['CCM'][2]['title']            = 'Remaining Minutes';
            
            
            
            // cert/recert link
            $sql_form = "SELECT form_id FROM forms WHERE formdir='LBF2' AND encounter= $encounter AND pid= $pid AND deleted= 0 order by date desc limit 0,1";
            $db->query( "SET NAMES utf8"); 
            $stmt_form = $db->prepare($sql_form) ;
            $stmt_form->execute();                       
            $getform = $stmt_form->fetchAll(PDO::FETCH_OBJ); 
            if(!empty($getform)){
                $form_lbf_id = $getform[0]->form_id;
                $sql_cert = "SELECT lbf.*,l.title,l.form_id FROM lbf_data lbf   
                                                             INNER JOIN layout_options l ON lbf.field_id = l.field_id 
                                                             WHERE lbf.form_id = $form_lbf_id AND l.field_id IN ( 'cert_recert_doc_link',  'cert_recert_doc_name'
                                                                ) ORDER by l.seq";
                $db->query( "SET NAMES utf8"); 
                $stmt_cert = $db->prepare($sql_cert) ;
                $stmt_cert->execute();                       
                $getListDatacert = $stmt_cert->fetchAll(PDO::FETCH_OBJ);  
                if(!empty($getListDatacert)){
                    for($i=0; $i< count($getListDatacert); $i++){
                        $patientsreminder[$getListDatacert[$i]->form_id][$i]['field_id']         = $getListDatacert[$i]->field_id;
                        if($getListDatacert[$i]->field_id == 'cert_recert_doc_link')
                            $patientsreminder[$getListDatacert[$i]->form_id][$i]['data_type']    = 'Hyperlink';
                        else
                            $patientsreminder[$getListDatacert[$i]->form_id][$i]['data_type']    = 'Static Text';
                        $patientsreminder[$getListDatacert[$i]->form_id][$i]['selected_value']   = $getListDatacert[$i]->field_value;
                        $patientsreminder[$getListDatacert[$i]->form_id][$i]['isRequired']       = 'Optional';
                        $patientsreminder[$getListDatacert[$i]->form_id][$i]['title']            = $getListDatacert[$i]->title;
                    }
                }
            }
            
            $audit_array['Edit']= $patientsreminder;
//        }
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
//        if(!empty($dataResult)){
            $dataarray['id'] = (isset($dataResult[0]->id)? $dataResult[0]->id : 0);
            $dataarray['pid'] = (isset($dataResult[0]->pid) ? $dataResult[0]->pid : $pid);
            $dataarray['user'] = (isset($dataResult[0]->user)? $dataResult[0]->user : '');
            
            $get_CPO_data2 = isset($dataResult[0]->cpo_data)? $dataResult[0]->cpo_data : '';
            $get_CPO_data = preg_replace_callback ( '!s:(\d+):"(.*?)";!',
                function($match) {
                    return ($match[1] == strlen($match[2])) ? $match[0] : 's:' . strlen($match[2]) . ':"' . $match[2] . '";';
                },
            $get_CPO_data2 );
            if(!empty($get_CPO_data)){
                $unseralized_cpo = unserialize($get_CPO_data);
                $count_array = count($unseralized_cpo) ;
            }else{
                $count_array  = 1;
            }
            $cpo_data = array();
            
            for($i=0; $i<$count_array; $i++){
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
                $cpo_data[$i]['cpotype']['value'] = (isset($unseralized_cpo[$i]['cpotype'])? $unseralized_cpo[$i]['cpotype'] : '');
                $cpo_data[$i]['cpotype']['options'] = $cpo_types;
                $cpo_data[$i]['cpotype']['isRequired'] = 'Optional';
                $cpo_data[$i]['cpotype']['placeholder'] = 'Select Oversight';
                $cpo_data[$i]['cpotype']['max_length'] = '0';
                
                $cpo_data[$i]['start_date']['type'] = 'Date Field';
                $cpo_data[$i]['start_date']['label'] = 'Date';
                $cpo_data[$i]['start_date']['value'] = (isset($unseralized_cpo[$i]['start_date'])? $unseralized_cpo[$i]['start_date'] : '');
                $cpo_data[$i]['start_date']['isRequired'] = 'Optional';
                $cpo_data[$i]['start_date']['placeholder'] = 'Select Date';
                $cpo_data[$i]['start_date']['max_length'] = '0';
                
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
                $cpo_data[$i]['timeinterval']['value'] = (isset($unseralized_cpo[$i]['timeinterval'])? $unseralized_cpo[$i]['timeinterval'] : '');
                $cpo_data[$i]['timeinterval']['options'] = $cpo_time_interval;
                $cpo_data[$i]['timeinterval']['isRequired'] = 'Optional';
                $cpo_data[$i]['timeinterval']['placeholder'] = 'Select Minutes';
                $cpo_data[$i]['timeinterval']['max_length'] = '0';
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
                $cpo_data[$i]['users']['value'] = (isset($unseralized_cpo[$i]['users'])? $unseralized_cpo[$i]['users'] : '');
                $cpo_data[$i]['users']['options'] = $user_name;
                $cpo_data[$i]['users']['isRequired'] = 'Optional';
                $cpo_data[$i]['users']['placeholder'] = 'Select Minutes';
                $cpo_data[$i]['users']['max_length'] = '0';
                
                $cpo_data[$i]['location']['type'] = 'Textarea';
                $cpo_data[$i]['location']['label'] = 'Location';
                $cpo_data[$i]['location']['value'] = (isset($unseralized_cpo[$i]['location'])? $unseralized_cpo[$i]['location'] : '');
                $cpo_data[$i]['location']['isRequired'] = 'Optional';
                $cpo_data[$i]['location']['placeholder'] = 'Enter Location text here..';
                $cpo_data[$i]['location']['max_length'] = '200';
                
                $cpo_data[$i]['description']['type'] = 'Textarea';
                $cpo_data[$i]['description']['label'] = 'Description';
                $cpo_data[$i]['description']['value'] = (isset($unseralized_cpo[$i]['description'])? $unseralized_cpo[$i]['description'] : '');
                $cpo_data[$i]['description']['isRequired'] = 'Optional';
                $cpo_data[$i]['description']['placeholder'] = 'Enter Description text here..';
                $cpo_data[$i]['description']['max_length'] = '200';
                
                $cpo_data[$i]['reference']['type'] = 'Textarea';
                $cpo_data[$i]['reference']['label'] = 'Reference';
                $cpo_data[$i]['reference']['value'] = (isset($unseralized_cpo[$i]['reference'])? $unseralized_cpo[$i]['reference'] : '');
                $cpo_data[$i]['reference']['isRequired'] = 'Optional';
                $cpo_data[$i]['reference']['placeholder'] = 'Enter reference text here..';
                $cpo_data[$i]['reference']['max_length'] = '200';
               
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
            $provider_id['value'] = (isset($dataResult[0]->provider_id)? $dataResult[0]->provider_id : '');
            $provider_id['isRequired'] = 'Optional';
            $provider_id['placeholder'] = 'NP/Physician Signature';
            $provider_id['max_length'] = '0';
            
            
            $dataarray['provider_id'] = $provider_id;
            $dataarray['count'] = (isset($dataResult[0]->count)? $dataResult[0]->count : 0);
            $signed_date['type'] = 'Date Field';
            $signed_date['label'] = 'NP/Physician Signature Date';
            $signed_date['value'] = (isset($dataResult[0]->signed_date)? $dataResult[0]->signed_date : '');
            $signed_date['isRequired'] = 'Optional';
            $signed_date['placeholder'] = 'NP/Physician Signature';
            $signed_date['max_length'] = '0';
            
            $dataarray['signed_date'] = $signed_date;
                        
            $encform="SELECT form_id FROM forms WHERE formdir= 'LBF2' and deleted = 0 and encounter = $encounter and pid = $pid order by date asc";
            $encstmt31= $db->prepare($encform) ;
            $encform_id = $encstmt31->execute();
            $encformid_val = $encstmt31->fetchAll(PDO::FETCH_OBJ);
            $patientsreminder = array();   
            if(!empty($encformid_val))
                $formid = $encformid_val[0]-> form_id;
            else
                $formid = 0;
            $patientsreminder = getLayoutFunction ($formid,'CPO',"'cpo_stat','cpo_review'");


            $dataarray2['CPO'] = $dataarray;
            $dataarray2['LBF2'] = $patientsreminder;
//        }
//        echo "<pre>"    ; print_r($dataarray2); echo "</pre>"    ;
        if($dataarray2)
        {   
           $datares = json_encode($dataarray2); 
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
        $db->query( "SET NAMES utf8");
        $stmt = $db->prepare($sql) ;
        $stmt->execute();                       

        $dataResult = $stmt->fetchAll(PDO::FETCH_OBJ);    
        $dataarray = array();
//        if(!empty($dataResult)){
            $dataarray['id'] = isset($dataResult[0]->id)? $dataResult[0]->id : 0;
            $dataarray['pid'] = isset($dataResult[0]->pid)? $dataResult[0]->pid : $pid;
            $dataarray['user'] = isset($dataResult[0]->user)? $dataResult[0]->user : '';
            
            $get_CCM_data2 = isset($dataResult[0]->ccm_data)? $dataResult[0]->ccm_data : '';
            $get_CCM_data = preg_replace_callback ( '!s:(\d+):"(.*?)";!',
                function($match) {
                    return ($match[1] == strlen($match[2])) ? $match[0] : 's:' . strlen($match[2]) . ':"' . $match[2] . '";';
                },
            $get_CCM_data2 );
            if(!empty($get_CCM_data)){
                $unseralized_ccm = unserialize($get_CCM_data);
                $count_array = count($unseralized_ccm) ;
            }else{
                $count_array  = 1;
            }
//            print_r($unseralized_ccm);
            $ccm_data = array();
            for($i=0; $i<$count_array; $i++){
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
                $ccm_data[$i]['ccmtype']['value'] = isset($unseralized_ccm[$i]['ccmtype'])? $unseralized_ccm[$i]['ccmtype']  : '';
                $ccm_data[$i]['ccmtype']['placeholder'] = '';
                $ccm_data[$i]['ccmtype']['isRequired'] = 'Optional';
                $ccm_data[$i]['ccmtype']['max_length'] = '0';
                $ccm_data[$i]['ccmtype']['options'] = $ccm_types;
                
                $ccm_data[$i]['start_date']['type'] = 'Date Field';
                $ccm_data[$i]['start_date']['label'] = 'Date';
                $ccm_data[$i]['start_date']['value'] = isset($unseralized_ccm[$i]['start_date'])? $unseralized_ccm[$i]['start_date']  : '';
                $ccm_data[$i]['start_date']['placeholder'] = '';
                $ccm_data[$i]['start_date']['isRequired'] = 'Optional';
                $ccm_data[$i]['start_date']['max_length'] = '0';
                
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
                $ccm_data[$i]['timeinterval']['value'] = isset($unseralized_ccm[$i]['timeinterval'])? $unseralized_ccm[$i]['timeinterval']  : '';
                $ccm_data[$i]['timeinterval']['options'] = $ccm_time_interval;
                $ccm_data[$i]['timeinterval']['placeholder'] = '';
                $ccm_data[$i]['timeinterval']['isRequired'] = 'Optional';
                $ccm_data[$i]['timeinterval']['max_length'] = '0';
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
                $ccm_data[$i]['users']['value'] = isset($unseralized_ccm[$i]['users'])? $unseralized_ccm[$i]['users']  : '';
                $ccm_data[$i]['users']['options'] = $user_name;
                $ccm_data[$i]['users']['placeholder'] = '';
                $ccm_data[$i]['users']['isRequired'] = 'Optional';
                $ccm_data[$i]['users']['max_length'] = '200';
                
                $ccm_data[$i]['location']['type'] = 'Textarea';
                $ccm_data[$i]['location']['label'] = 'Location';
                $ccm_data[$i]['location']['value'] = isset($unseralized_ccm[$i]['location'])? $unseralized_ccm[$i]['location']  : '';
                $ccm_data[$i]['location']['isRequired'] = 'Optional';
                $ccm_data[$i]['location']['placeholder'] = 'Enter Location text here..';
                $ccm_data[$i]['location']['max_length'] = '200';
                
                $ccm_data[$i]['description']['type'] = 'Textarea';
                $ccm_data[$i]['description']['label'] = 'Description';
                $ccm_data[$i]['description']['value'] = isset($unseralized_ccm[$i]['description'])? $unseralized_ccm[$i]['description']  : '';
                $ccm_data[$i]['description']['isRequired'] = 'Optional';
                $ccm_data[$i]['description']['placeholder'] = 'Enter Description text here..';
                $ccm_data[$i]['description']['max_length'] = '200';
                
                $ccm_data[$i]['reference']['type'] = 'Textarea';
                $ccm_data[$i]['reference']['label'] = 'Reference';
                $ccm_data[$i]['reference']['value'] = isset($unseralized_ccm[$i]['reference'])? $unseralized_ccm[$i]['reference']  : '';
                $ccm_data[$i]['reference']['isRequired'] = 'Optional';
                $ccm_data[$i]['reference']['placeholder'] = 'Enter Reference text here..';
                $ccm_data[$i]['reference']['max_length'] = '200';
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
            $provider_id['value'] = isset($dataResult[0]->provider_id)? $dataResult[0]->provider_id : '';
            $provider_id['isRequired'] = 'Optional';
            $provider_id['placeholder'] = '';
            $provider_id['max_length'] = '';
            
            $dataarray['provider_id'] = $provider_id;
            
            $dataarray['count'] = isset($dataResult[0]->count)? $dataResult[0]->count : 0;
            $signed_date['type'] = 'Date Field';
            $signed_date['label'] = 'NP/Physician Signature Date';
            $signed_date['value'] = isset($dataResult[0]->signed_date)? $dataResult[0]->signed_date : '';
            $signed_date['isRequired'] = 'Optional';
            $signed_date['placeholder'] = '';
            $signed_date['max_length'] = '0';
            
            $dataarray['signed_date'] = $signed_date;

            $encform="SELECT form_id FROM forms WHERE formdir= 'LBF2' and deleted = 0 and encounter = $encounter and pid = $pid order by date asc";
            $encstmt31= $db->prepare($encform) ;
            $encform_id = $encstmt31->execute();
            $encformid_val = $encstmt31->fetchAll(PDO::FETCH_OBJ);
            $patientsreminder = array();   
            if(!empty($encformid_val))
                $formid = $encformid_val[0]-> form_id;
            else
                $formid = 0;
            $patientsreminder = getLayoutFunction ($formid,'CCM',"'ccm_stat','ccm_review'");
            

            $dataarray2['CCM'] = $dataarray;
            $dataarray2['LBF2'] = $patientsreminder;
//        echo "<pre>"    ; print_r($dataarray2); echo "</pre>"    ;
        if($dataarray2)
        {   
           $datares = json_encode($dataarray2); 
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
function getLayoutFunction($formid, $value1,$value2){
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
                                END as isRequired,seq,max_length,description FROM layout_options WHERE form_id = 'LBF2' AND group_name LIKE '%$value1' AND field_id IN($value2)order by group_name, seq";
    $db->query( "SET NAMES utf8");
    $stmt_layout2 = $db->prepare($get_layout2) ;
    $stmt_layout2->execute();                       
    $layout_fields2 = $stmt_layout2->fetchAll(PDO::FETCH_OBJ);
    if(!empty($layout_fields2)){
        $patientsreminder[substr($layout_fields2[0]->group_name,1)]['form_id'] = $formid;
        for($i=0; $i< count($layout_fields2); $i++){
            if(!empty($layout_fields2[$i]->field_type)){
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
    }
    return $patientsreminder;
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
//        $fileresult =  GibberishAES::dec('U2FsdGVkX1/yjGH4ky5yDhSliJQaIzGhQrf+oMcjmNtZefBZALXGknLm1Es9c/UW2frN4sVdVE/NTwFD+yRAJGhcUK5Nu/dZPO5lpBPnTluk1LDTcJTezVoVoWfqMNKFQb/z4icNWEPLOIIRKUlFIgGSqEEr3P3kkwRI9dezrmKoK68EkkUsDn4LnHsH0q6HtpDqG0K6/lsnKLJT0lz64p5wuSPaG8cGoiTy9JgS5q4HCFS8YZ9cRYQXkJ1wQ5NdL6Ejk1gu5RszkKkjUv9Fnv+6x2daICz9A8XxAzpMnLLqkuqdHpw+TjtA0pC2XTOzEudS3l26fLyEdJzR95Abvfn8D8G5oJKLbPjUwuJy2Xvn3gqEC1Vo74y3obLtx3RpEAP9w2xeWsPngjst+z2HcVXInubVMC5RTvWiv25YMueTX8Dn2+P2WfnLceogJQIcAYtTE1UgEpdIMS6N1H4FPjFJ1n0C4rJlu0AgceFnn8QL5ywpEFnOQeWbNy8xufAB', $apikey);
        $filesArray = json_decode($fileresult,TRUE);
//        echo "<pre>"; print_r($filesArray); echo "</pre>";

        $id            = $filesArray['id'];
        $pid           = $filesArray['pid'];
        $encounter     = $filesArray['encounter'];
        $user          = $filesArray['user'];
        $username      = $filesArray['user'];
        $user_id       = $filesArray['user_id'];
        for($i=0; $i< count($filesArray['cpo_data']); $i++){
            foreach($filesArray['cpo_data'][$i] as $cpokey=> $cpoval){
                $cpo_data2[$i][$cpokey] = addslashes($cpoval);
            }
        }
        
        $cpo_data      = serialize($cpo_data2);
        $provider_id   = $filesArray['provider_id'];
        $authorized    = 1;
        $activity      = 1;
        $authProvider  = 'Default';
        $count         = $filesArray['count'];
        $signed_date   = $filesArray['signed_date'];
        $lbf2formid    = $filesArray['LBF2'][0]['formId'];
        $lbf2          = $filesArray['LBF2'][1];
        if($id == 0){
            $new_sql = "select max(form_id)as new_form from forms where form_name='CPO' AND formdir='cpo'";
            $db->query( "SET NAMES utf8"); 
            $new_stmt = $db->prepare($new_sql);
            $new_stmt->execute();
            $new_res = $new_stmt->fetchAll(PDO::FETCH_OBJ);
            $new_fid = $new_res[0]->new_form;
            $copy_to_id = $new_fid + 1;
            
            $form_ins = "INSERT INTO forms (date,encounter,form_name,form_id,pid,user,groupname,authorized,deleted,formdir) VALUES (NOW(),$encounter,'CPO',$copy_to_id,'".$pid."','$username','default',1,0,'cpo')";
            $db->query( "SET NAMES utf8"); 
            $form_insstmt = $db->prepare($form_ins);
            if($form_insstmt->execute())
                insertMobileLog('insert',"$username",$form_ins,$pid,$encounter,"New CPO form Screen", 1);
            else
                insertMobileLog('insert',"$username",$form_ins,$pid,$encounter,"New CPO form Screen - Failed", 0);
            
            $save_cpo = "INSERT INTO `tbl_form_cpo`( id,`date`, `pid`, `user`, `cpo_data`, `provider_id`, `authorized`, `activity`,`authProvider`, `count`, `signed_date`) VALUES ($copy_to_id,NOW(),$pid,'$user','$cpo_data','$provider_id','$authorized','$activity','$authProvider','$count','$signed_date')";
            $save_cpo_check = 'insert';
        }else{
            $save_cpo = "UPDATE `tbl_form_cpo` SET date = NOW(),user ='$user', cpo_data = '$cpo_data', provider_id = '$provider_id' , signed_date = '$signed_date', count= '$count'  WHERE id = $id and pid = $pid ";
            $save_cpo_check = 'update';
                    
        }
        $db->query( "SET NAMES utf8"); 
        $get_cpo = $db->prepare($save_cpo) ;
        
//        $getformid1 = "SELECT form_id FROM forms WHERE formdir= 'LBF2' and deleted = 0 and encounter = '$encounter' and pid = '$pid' order by date asc";
//        $stmt31 = $db->prepare($getformid1) ;
//        $stmt31->execute();
//        $formidval2 = $stmt31->fetchAll(PDO::FETCH_OBJ);

        if($lbf2formid != 0){
             $newformid2 =  $lbf2formid;
        }else{
            $lastformid2 = "SELECT MAX(form_id) as forms FROM forms where formdir='LBF2'";
            $stmt5 = $db->prepare($lastformid2) ;
            $stmt5->execute();
            $maxformidval2 = $stmt5->fetchAll(PDO::FETCH_OBJ);
            $maxformid2 =  $maxformidval2[0]->forms + 1;
            
            $insertform2 = "INSERT INTO forms (date,encounter, form_name, form_id, pid, user, groupname, authorized, deleted, formdir) VALUES (NOW(), $encounter, 'Allcare Encounter Forms', $maxformid2, $pid, '$username', 'Default', $authorized, 0, 'LBF2' )";
            $stmt4 = $db->prepare($insertform2);
            if($stmt4->execute())
                insertMobileLog('insert',"$username",$insertform2,$pid,$encounter,"New CPO LBF form Screen", 1);
            else
                insertMobileLog('insert',"$username",$insertform2,$pid,$encounter,"New CPO LBF form Screen - Failed", 0);
            $newformid2 = $db->lastInsertId();
            
        }
        foreach($lbf2 as $lbfkey => $lbfvalue){
            if (count($db->query("SELECT * FROM lbf_data WHERE form_id = $newformid2 AND field_id = '$lbfkey'")->fetchAll())==0) {
                $sql5 = "INSERT into lbf_data (form_id, field_id, field_value) VALUES ($newformid2,'$lbfkey','".addslashes($lbfvalue)."')";
                $cpo_check2 = 'insert';
            } else {
               $sql5 = "UPDATE lbf_data SET field_value = '".addslashes($lbfvalue)."' WHERE field_id ='$lbfkey'  AND form_id = $newformid2";
               $cpo_check2 = 'update';
            }
            $stmt41 = $db->prepare($sql5);
            if($stmt41->execute())
                insertMobileLog($cpo_check2,"$username",$sql5,$pid,$encounter,"Save CPO form LBF Screen", 1);
            else
                insertMobileLog($cpo_check2,"$username",$sql5,$pid,$encounter,"Save CPO form LBF Screen - Failed", 0);
        }
        if($get_cpo->execute())
        {   
           $datares =  '[{"id":"1"}]';  
           echo $patientresult = GibberishAES::enc($datares, $apikey);
           insertMobileLog($save_cpo_check,"$username",$save_cpo,$pid,$encounter,"Save CPO form Data Screen", 1);
        }
        else
        {
            $datares = '[{"id":"0"}]';   
            echo $patientresult = GibberishAES::enc($datares, $apikey);
            insertMobileLog($save_cpo_check,"$username",$save_cpo,$pid,$encounter,"Save CPO form Data Screen - Failed", 0);
        }
    }catch(PDOException $e){
        $insertquery = '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo GibberishAES::enc($insertquery, $apikey);
        insertMobileLog('insert/update',"$username",$insertquery,$pid,$encounter,"Save CPO form Data Screen - Query Failed", 0);
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
//        $fileresult =  GibberishAES::dec('U2FsdGVkX1951isfxjl/5RJazFoCM/KZj7jG6K0w3+GgyHVFmokooLa4UG1KoGJiQC5cwPWIcaYXrgGIweToboan4XwtwVgIrX6Do3BcnNlFWSLz5kVHyziOmpUJzDj6tEofwXwWN25Ne4Mti6GuyxuUkyC7Ym2ABv/041AxarfEmzDfwLTnGCjsSnuSVA3BD1uevLEiWyLR52DA+jeBOq0EdC9q7u9dIR3ZyQDpHkVBJzo+U1cJo+c6LQCnbUvb5Fqja3r4DO8Zo7t4KCT9NmeblncQYQdqQaVU6kfOj1KivgaSfVe9SduMKxvpc8bEHf+6XC9FsOKJviTvGxN/3+fdUqvYzuqDO6YuwDR2YKSkbA5Gu2yF1jhTFoVJ5KpKuwWiurlQRP6wR8FCVLLJ35DY0m8pgz4Kv2I1J61LfSwNOd9/gjyNl9ZBQqAflftsACnIiExqz8L7HUSgy/GKSWxXLg81NvXUQBTYATJAHybUNhFrYKzTBzZnmyy8fnfxdp+RB/SMumVsvSRf8B6sXnXbk9i+x46s+Tl83TlTdbiPmH4slcZkN1I1eM3sQ6z1', $apikey);
        $filesArray = json_decode($fileresult,TRUE);
//        echo "<pre>"; print_r($filesArray); echo "</pre>";

        $id            = $filesArray['id'];
        $pid           = $filesArray['pid'];
        $encounter     = $filesArray['encounter'];
        $user          = $filesArray['user'];
        $username      = $filesArray['user'];
//        $username2     = getUserName($user);
//        $username      = isset($username2[0]['username'])? $username2[0]['username'] : ''; 
        $user_id       = $filesArray['user_id'];
        for($i=0; $i< count($filesArray['ccm_data']); $i++){
            foreach($filesArray['ccm_data'][$i] as $ccmkey=> $ccmval){
                $ccm_data2[$i][$ccmkey] = addslashes($ccmval);
            }
        }
        
        $ccm_data      = serialize($ccm_data2);
        $provider_id   = $filesArray['provider_id'];
        $authorized    = 1;
        $activity      = 1;
        $authProvider  = 'Default';
        $count         = $filesArray['count'];
        $signed_date   = $filesArray['signed_date'];
        $lbf2formid    = $filesArray['LBF2'][0]['formId'];
        $lbf2          = $filesArray['LBF2'][1];
        if($id == 0){
            $new_sql = "select max(form_id)as new_form from forms where form_name='CCM' AND formdir='ccm'";
            $db->query( "SET NAMES utf8"); 
            $new_stmt = $db->prepare($new_sql);
            $new_stmt->execute();
            $new_res = $new_stmt->fetchAll(PDO::FETCH_OBJ);
            $new_fid = $new_res[0]->new_form;
            $copy_to_id = $new_fid + 1;
            
            $form_ins = "INSERT INTO forms (date,encounter,form_name,form_id,pid,user,groupname,authorized,deleted,formdir) VALUES (NOW(),$encounter,'CCM',$copy_to_id,'".$pid."','$username','default',1,0,'ccm')";
            $db->query( "SET NAMES utf8"); 
            $form_insstmt = $db->prepare($form_ins);
            if($form_insstmt->execute())
                insertMobileLog('insert',"$username",$form_ins,$pid,$encounter,"New CCM form Screen", 1);
            else
                insertMobileLog('insert',"$username",$form_ins,$pid,$encounter,"New CCM form Screen - Failed", 0);
            
            $save_ccm = "INSERT INTO `tbl_form_ccm`(id, `date`, `pid`, `user`, `ccm_data`, `provider_id`, `authorized`, `activity`,`authProvider`, `count`, `signed_date`) VALUES ($copy_to_id,NOW(),$pid,'$user','$ccm_data','$provider_id','$authorized','$activity','$authProvider','$count','$signed_date')";
            $ccm_check = 'insert';
        }else{
            $save_ccm = "UPDATE `tbl_form_ccm` SET date = NOW(),user ='$user', ccm_data = '$ccm_data', provider_id = '$provider_id' , signed_date = '$signed_date', count= '$count'  WHERE id = $id and pid = $pid ";
            $ccm_check = 'update';        
        }
        $db->query( "SET NAMES utf8"); 
        $get_ccm = $db->prepare($save_ccm) ;
        $datacheck = 0; 
        if($get_ccm->execute()){
            insertMobileLog($ccm_check,"$username",$save_ccm,$pid,$encounter,"CCM form Screen", 1);
            $datacheck = 1;
        }else{
            insertMobileLog($ccm_check,"$username",$save_ccm,$pid,$encounter,"CCM form Screen - Failed", 0);
        }
            $datacheck = 1;
        $getformid1 = "SELECT form_id FROM forms WHERE formdir= 'LBF2' and deleted = 0 and encounter = '$encounter' and pid = '$pid' order by date asc";
        $stmt31 = $db->prepare($getformid1) ;
        $stmt31->execute();
        $formidval2 = $stmt31->fetchAll(PDO::FETCH_OBJ);

        if($lbf2formid != 0){
             $newformid2 =  $lbf2formid;
        }else{
            $lastformid2 = "SELECT MAX(form_id) as forms FROM forms where formdir='LBF2'";
            $stmt5 = $db->prepare($lastformid2) ;
            $stmt5->execute();
            $maxformidval2 = $stmt5->fetchAll(PDO::FETCH_OBJ);
            $maxformid2 =  $maxformidval2[0]->forms + 1;
            
            $insertform2 = "INSERT INTO forms (date,encounter, form_name, form_id, pid, user, groupname, authorized, deleted, formdir) VALUES (NOW(), $encounter, 'Allcare Encounter Forms', $maxformid2, $pid, '$username', 'Default', $authorized, 0, 'LBF2' )";
            $stmt4 = $db->prepare($insertform2);
            if($stmt4->execute())
                insertMobileLog('insert',"$username",$insertform2,$pid,$encounter,"New CCM form LBF Screen", 1);
            else
                insertMobileLog('insert',"$username",$insertform2,$pid,$encounter,"New CCM form LBF Screen - Failed", 0);
            $newformid2 = $db->lastInsertId();
            
        }
        foreach($lbf2 as $lbfkey => $lbfvalue){
            if (count($db->query("SELECT * FROM lbf_data WHERE form_id = $newformid2 AND field_id = '$lbfkey'")->fetchAll())==0) {
                $sql5 = "INSERT into lbf_data (form_id, field_id, field_value) VALUES ($newformid2,'$lbfkey','".addslashes($lbfvalue)."')";
                $ccm_check2 = 'insert';    
            } else {
               $sql5 = "UPDATE lbf_data SET field_value = '".addslashes($lbfvalue)."' WHERE field_id ='$lbfkey'  AND form_id = $newformid2";
               $ccm_check2 = 'update';    
            }
            $stmt41 = $db->prepare($sql5);
            $datacheck2 = 0; 

            if($stmt41->execute()){
                $datacheck2 = 1;
                insertMobileLog($ccm_check2,"$username",$sql5,$pid,$encounter,"Save CCM form LBF Screen", 1);
            }else{
                insertMobileLog($ccm_check2,"$username",$sql5,$pid,$encounter,"Save CCM form LBF Screen - Failed", 0);
            }
        }
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
        insertMobileLog('insert/update',"$username",$insertquery,$pid,$encounter,"Save CCM form Data Screen - Query Failed", 0);
    }
}
function getAddressbookCustomAttributes($uid){
    $apikey = 'rotcoderaclla';
    // The default key size is 256 bits.
    $old_key_size = GibberishAES::size();
    GibberishAES::size(256);
    try 
    {
        $db = getConnection();
        $get_layout = "SELECT field_id,title,list_id FROM layout_options WHERE form_id = 'ADDRCA' and uor<> 0 order by group_name,seq";
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
                $sql = "select id $enc_value from tbl_addrbk_custom_attr_1to1 where addrbk_type_id = '$uid'";   
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
                        if(!empty($pvalue)){
                            $get_layout2 = "SELECT group_name,list_id, title FROM layout_options WHERE form_id = 'ADDRCA' AND field_id = '$pkey' order by group_name,seq"; 
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
function getAddressbookCred($uid){
    $apikey = 'rotcoderaclla';
    // The default key size is 256 bits.
    $old_key_size = GibberishAES::size();
    GibberishAES::size(256);
    try 
    {
        $db = getConnection();
        $get_layout = "SELECT field_id,title FROM layout_options WHERE form_id = 'ABOOKCRED' and uor<> 0 order by group_name,seq";
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
            $sql = "select id $enc_value from tbl_user_cred where userid = '$uid'";   
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
                        if(!empty($pvalue)){
                            $get_layout2 = "SELECT group_name,list_id, title FROM layout_options WHERE form_id = 'ABOOKCRED' AND field_id = '$pkey' order by group_name,seq"; 
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
function getAddressbookContacts($uid){
    $apikey = 'rotcoderaclla';
    // The default key size is 256 bits.
    $old_key_size = GibberishAES::size();
    GibberishAES::size(256);
    try 
    {
        $db = getConnection();
        $get_layout = "SELECT field_id,title FROM layout_options WHERE form_id = 'ABOOKCONTACT' and uor<> 0 order by group_name, seq";
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
            $sql = "select id $enc_value from tbl_user_abookcontact where userid = '$uid'";   
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
                        if(!empty($pvalue)){
                            $get_layout2 = "SELECT group_name,list_id, title FROM layout_options WHERE form_id = 'ABOOKCONTACT' AND field_id = '$pkey' order by group_name, seq"; 
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
function editAbookLayout($uid, $id,$table,$form_id){
    $db = getConnection();
    $get_layout = "SELECT field_id,title FROM layout_options WHERE form_id = '$form_id' and uor<> 0 order by group_name, seq";
    $db->query( "SET NAMES utf8");
    $stmt_layout = $db->prepare($get_layout) ;
    $stmt_layout->execute();                       

    $layout_fields = $stmt_layout->fetchAll(PDO::FETCH_OBJ);  
    $enc_val = '';
    for($i = 0; $i<count($layout_fields); $i++){
       $enc_val .=  "`".$layout_fields[$i]->field_id."`,";//." as ". preg_replace('/[^a-zA-Z0-9_.]/', '', $layout_fields[$i]->title).",";//str_replace(" ","_",$layout_fields[$i]->title). ",";
    }

    $enc_value = rtrim($enc_val,",");
    if($table == 'tbl_addrbk_custom_attr_1to1')
        $sql = "select $enc_value from $table where addrbk_type_id = '$uid' and id= '$id'";   
    if($table == 'tbl_user_cred')
        $sql = "select $enc_value from tbl_user_cred where userid = '$uid' and id= '$id'";  
    if($table == 'tbl_user_abookcontact')
        $sql = "select $enc_value from tbl_user_abookcontact where userid = '$uid' and id= '$id'";   
    
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
                            END as isRequired,description,max_length FROM layout_options WHERE form_id = '$form_id' AND field_id = '$pkey' order by group_name, seq";
            $db->query( "SET NAMES utf8");
            $stmt_layout2 = $db->prepare($get_layout2) ;
            $stmt_layout2->execute();                       
            $layout_fields2 = $stmt_layout2->fetchAll(PDO::FETCH_OBJ);
            if(!empty($layout_fields2)){
                if(!empty($layout_fields2[0]->field_type)){
                    $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['field_id'] = $pkey;
                    $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['field_type'] =  $layout_fields2[0]->field_type;
                    $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['isRequired'] =  $layout_fields2[0]->isRequired;
                    $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['label']  = $layout_fields2[0]->title;
                    $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['list_id']  = $layout_fields2[0]->list_id;
                    $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['placeholder']  = $layout_fields2[0]->description;
                    if($layout_fields2[0]->max_length == 0)
                        $maxlength = '';
                    else
                        $maxlength = $layout_fields2[0]->max_length;
                    $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['max_length']  = $maxlength;
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
            }
            $i++;
        }
    }
    return $patientsreminder;
}
function editAddressbookCustomAttributes($uid,$id){
    $apikey = 'rotcoderaclla';
    // The default key size is 256 bits.
    $old_key_size = GibberishAES::size();
    GibberishAES::size(256);
    try 
    {
        $db = getConnection();
        $patientsreminder = editAbookLayout($uid, $id,'tbl_addrbk_custom_attr_1to1','ADDRCA');
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
function editAddressbookCred($uid,$id){
    $apikey = 'rotcoderaclla';
    // The default key size is 256 bits.
    $old_key_size = GibberishAES::size();
    GibberishAES::size(256);
    try 
    {
        $db = getConnection();
        $patientsreminder = editAbookLayout($uid, $id,'tbl_user_cred','ABOOKCRED');
      
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
function editAddressbookContacts($uid,$id){
    $apikey = 'rotcoderaclla';
    // The default key size is 256 bits.
    $old_key_size = GibberishAES::size();
    GibberishAES::size(256);
    try 
    {
        $db = getConnection();
        $patientsreminder = editAbookLayout($uid, $id,'tbl_user_abookcontact','ABOOKCONTACT');
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
function createAddressbookLayout(){
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
                            END as isRequired,description,max_length FROM layout_options WHERE form_id = '$form_id' and uor<> 0 order by group_name, seq";   
    $db->query( "SET NAMES utf8");
    $stmt = $db->prepare($sql) ;
    $stmt->execute();                       

    $patientsreminders = $stmt->fetchAll(PDO::FETCH_OBJ);    
    $patientsreminder = array();
    if(!empty($patientsreminders)){
        for($i=0; $i<count($patientsreminders); $i++){
            if(!empty($patientsreminders[$i]->field_type)){
                $patientsreminder[substr($patientsreminders[$i]->group_name,1)][$i]['field_id']  = $patientsreminders[$i]->field_id;
                $patientsreminder[substr($patientsreminders[$i]->group_name,1)][$i]['isRequired']  = $patientsreminders[$i]->isRequired;
                $patientsreminder[substr($patientsreminders[$i]->group_name,1)][$i]['field_type']  = $patientsreminders[$i]->field_type;
                $patientsreminder[substr($patientsreminders[$i]->group_name,1)][$i]['label']  = $patientsreminders[$i]->title;
                $patientsreminder[substr($patientsreminders[$i]->group_name,1)][$i]['list_id']  =$patientsreminders[$i]->list_id;
                $patientsreminder[substr($patientsreminders[$i]->group_name,1)][$i]['placeholder']  =$patientsreminders[$i]->description;
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
    }
    return $patientsreminder;
}
function createAddressbookCustomAttributes($uid){
    $apikey = 'rotcoderaclla';
    // The default key size is 256 bits.
    $old_key_size = GibberishAES::size();
    GibberishAES::size(256);
    try 
    {
        $db = getConnection();
        $patientsreminder = createAddressbookLayout('ADDRCA',$uid);
        
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
function createAddressbookContacts($uid){
    $apikey = 'rotcoderaclla';
    // The default key size is 256 bits.
    $old_key_size = GibberishAES::size();
    GibberishAES::size(256);
    try 
    {
        $db = getConnection();

        $patientsreminder = createAddressbookLayout('ABOOKCONTACT',$uid);
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
function createAddressbookCred($uid){
    $apikey = 'rotcoderaclla';
    // The default key size is 256 bits.
    $old_key_size = GibberishAES::size();
    GibberishAES::size(256);
    try 
    {
        $db = getConnection();
        $patientsreminder = createAddressbookLayout('ABOOKCRED',$uid);
        
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
function saveAddressbookCustomAttributes(){
    try{
        
        $db = getConnection();
        $flag=0;
        $request = Slim::getInstance()->request();
        $apikey = 'rotcoderaclla';
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        $appres = GibberishAES::dec($request->getBody(), $apikey);
//        $appres = GibberishAES::dec('U2FsdGVkX18OiT1Tkl1UaM9Mi8zdFkuaAvxy1FkVKtKUYLX+Dt1w6jsgc/aT46ltMV2zkEJYnVUBaHDPrfrvhEgF9vsJotYWRkl1Fm9L/jyb2Wk/9Z0QKFBvbxcXnG+JJwlqyAdykk/TFjI5z6L1Pg7u5Hq3Mvhz92+g8QjM2L+/7aTG7NIhuNQmsyEyr8ozEgSZ4gyN0D0t6Mh51UYfdisdUb9x3jvNe6akDFdWObE4dI+LbS+RmHe8lNx+hfUxqQId4kaT/3clQ+LfeK1DlQrKwjShU9yFRK3N/jzyPdVuofxz/akLT9TRyMH/rJnBf+dv6lDbNl+IBq6K4tO9oBExHde1gMQUM5eUke+5YJ+eZvZfbL+xCNmBXwqzkfgfMtmRT9LP6oRGO4r6ukwjRVN9D4qNRxyw3iLBwGFrXmwlm2DoZ5MEwcAW4o9MGdnHaBiRs7QmNOllCoQOX4T3tm5PnMSfNWrV7tZFn69eBml2F6YRhJngkzGIHq3HM+2b5kA05ToyKyQ1A13veIVoAfl84m90l9ekDDOoes+83CicFHA2aPqZMbLJLKhwTZedwXz9tBlpaY1KAWDer6iwwpbJOfSbMPYjpnsDDkeKjz/ugmAowQbw2QDmwSWg7WfkvCKv24gWxx04e9O8dj9jNH/99UhqJVwm4bM+u0h0f528Ye/Abdn3wQkWw5cKbx3tJo0LEsKrvdGas2eoEmZ2r2GmvZxe+YbVjaW1JM69FRhznVulPY3GzcAjq4x0s7I92OiUvX5bWQt/wei6foF6L/c/yEQxpaCqCg0i13KnMEDZVTJk1MzEhAzuvnqSminGIwOcI69WqmYYLkq45XD/WQ==', $apikey);
        $insertArray = json_decode($appres,TRUE);
//        echo "<pre>"; print_r($insertArray); echo "</pre>";
         
        $form_id = $insertArray['form_id'];
        $addrbk_type_id = $insertArray['addrbk_type_id'];
        $user = $insertArray['addrbk_type_id'];
//exit();
        
        $username2  = getUserName($user);
        $username   = isset($username2[0]['username'])? $username2[0]['username'] : ''; 
        $insertval = 0;
        $getcolumnNames = "select column_name as columns from information_schema.columns where table_name='tbl_addrbk_custom_attr_1to1'";
        $stmt = $db->prepare($getcolumnNames) ;
        $stmt->execute();                       

        $getnames = $stmt->fetchAll(PDO::FETCH_OBJ); 
        
        foreach ($getnames as $value) {
           if((strcmp($value->columns, 'id') !== 0) && (strcmp($value->columns, 'addrbk_type_id') !== 0) && (strcmp($value->columns, 'updated_date') !== 0) && (strcmp($value->columns, 'created_date') !== 0) && (strcmp($value->columns, 'date') !== 0)  && (strcmp($value->columns, 'activity') !== 0) && (strcmp($value->columns, 'authorized') !== 0)):
                ${$value->columns} = addslashes($insertArray['data'][0][$value->columns]);//str_replace("_", ' ', $insertArray['data'][0][$value->columns]);
                
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
            $insert = "INSERT INTO tbl_addrbk_custom_attr_1to1 (addrbk_type_id,$nameslist,created_date) VALUES('$addrbk_type_id',$namesvalues,NOW())";
            $stmt2 = $db->prepare($insert) ;
            if($stmt2->execute()){
                $insertval = 1;
                insertMobileLog('insert',$username,$insert,'','','Save Addressbook Custom Attributes',1);
            }else{
                $insertval = 0;
                insertMobileLog('insert',$username,$insert,'','','Save Addressbook Custom Attributes - Failed',0);
            }                  
        }else{
            $nameslist = rtrim($nameslist2, ',');
            $namesvalues = rtrim($namesvalues2, ',');
            $insert = "UPDATE tbl_addrbk_custom_attr_1to1 SET $updatenamelist WHERE id = '$form_id'";
            $stmt2 = $db->prepare($insert) ;
            if($stmt2->execute()){
                $insertval = 1;
                insertMobileLog('update',$username,$insert,'','','Save Addressbook Custom Attributes',1);
            }else{
                $insertval = 0;
                insertMobileLog('update',$username,$insert,'','','Save Addressbook Custom Attributes - Failed',0);
            }  
        }
        
        if($insertval == 1){  
            $insertcheck =  '[{"id":"1"}]';
            echo GibberishAES::enc($insertcheck, $apikey);
	}else{
            $insertcheck = '[{"id":"0"}]';
            echo GibberishAES::enc($insertcheck, $apikey);
	}
	  
    }catch(PDOException $e){
        $insertquery = '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo GibberishAES::enc($insertquery, $apikey);
        insertMobileLog('insert/update',$username,$insertquery,'','','Save Addressbook Custom Attributes - Query Failed',0);
    }
}
function saveAddressbookCred(){
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
        
        $username2  = getUserName($userid);
        $username   = isset($username2[0]['username'])? $username2[0]['username'] : ''; 

        $getcolumnNames = "select column_name as columns from information_schema.columns where table_name='tbl_user_cred'";
        $stmt = $db->prepare($getcolumnNames) ;
        $stmt->execute();                       
        $insertval = 0;
        $getnames = $stmt->fetchAll(PDO::FETCH_OBJ); 
        
        foreach ($getnames as $value) {
           if((strcmp($value->columns, 'id') !== 0) && (strcmp($value->columns, 'userid') !== 0)&& (strcmp($value->columns, 'updated_date') !== 0) && (strcmp($value->columns, 'created_date') !== 0)&& (strcmp($value->columns, 'date') !== 0)  && (strcmp($value->columns, 'activity') !== 0) && (strcmp($value->columns, 'authorized') !== 0)):
                ${$value->columns} = addslashes($insertArray['data'][0][$value->columns]);//str_replace("_", ' ', $insertArray['data'][0][$value->columns]);
                
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
            $insert = "INSERT INTO tbl_user_cred ($nameslist,userid,created_date) VALUES($namesvalues,$userid,NOW())";
            $stmt2 = $db->prepare($insert) ;
            if($stmt2->execute()){
                $insertval = 1;
                insertMobileLog('insert',$username,$insert,'','','Save Addressbook Credentials',1);
            }else{
                $insertval = 0;
                insertMobileLog('insert',$username,$insert,'','','Save Addressbook Credentials - Failed',0);
            }
        }else{
            $nameslist = rtrim($nameslist2, ',');
            $namesvalues = rtrim($namesvalues2, ',');
            $insert = "UPDATE tbl_user_cred SET $updatenamelist WHERE id = '$form_id'";
            $stmt2 = $db->prepare($insert) ;
            if($stmt2->execute()){
                $insertval = 1;
                insertMobileLog('update',$username,$insert,'','','Save Addressbook Credentials',1);
            }else{
                $insertval = 0;
                insertMobileLog('update',$username,$insert,'','','Save Addressbook Credentials - Failed',0);
            } 
        }
        
        if($insertval == 1){  
            $insertcheck =  '[{"id":"1"}]';
            echo GibberishAES::enc($insertcheck, $apikey);
	}else{
            $insertcheck = '[{"id":"0"}]';
            echo GibberishAES::enc($insertcheck, $apikey);
	}
	  
    }catch(PDOException $e){
        $insertquery = '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo GibberishAES::enc($insertquery, $apikey);
        insertMobileLog('insert/update',$username,$insertquery,'','','Save Addressbook Credentials - Query Failed',0);
    }
}
function saveAddressbookContacts(){
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
        $username2  = getUserName($userid);
        $username   = isset($username2[0]['username'])? $username2[0]['username'] : ''; 
        $insertval = 0;
        $getcolumnNames = "select column_name as columns from information_schema.columns where table_name='tbl_user_abookcontact'";
        $stmt = $db->prepare($getcolumnNames) ;
        $stmt->execute();                       

        $getnames = $stmt->fetchAll(PDO::FETCH_OBJ); 
        
        foreach ($getnames as $value) {
           if((strcmp($value->columns, 'id') !== 0) && (strcmp($value->columns, 'userid') !== 0) && (strcmp($value->columns, 'updated_date') !== 0) && (strcmp($value->columns, 'created_date') !== 0)&& (strcmp($value->columns, 'date') !== 0)  && (strcmp($value->columns, 'activity') !== 0) && (strcmp($value->columns, 'authorized') !== 0)):
                ${$value->columns} = addslashes($insertArray['data'][0][$value->columns]); //str_replace("", ' ', $insertArray['data'][0][$value->columns]);
                
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
            $insert = "INSERT INTO tbl_user_abookcontact ($nameslist,userid,created_date) VALUES($namesvalues,$userid,NOW())";
            $stmt2 = $db->prepare($insert) ;
            if($stmt2->execute()){
                $insertval = 1;
                insertMobileLog('insert',$username,$insert,'','','Save Addressbook Contacts',1);
            }else{
                $insertval = 0;
                insertMobileLog('insert',$username,$insert,'','','Save Addressbook Contacts - Failed',0);
            }                  
        }else{
            $nameslist = rtrim($nameslist2, ',');
            $namesvalues = rtrim($namesvalues2, ',');
            $insert = "UPDATE tbl_user_abookcontact SET $updatenamelist  WHERE id = '$form_id'";
            $stmt2 = $db->prepare($insert) ;
            if($stmt2->execute()){
                $insertval = 1;
                insertMobileLog('update',$username,$insert,'','','Save Addressbook Contacts',1);
            }else{
                $insertval = 0;
                insertMobileLog('update',$username,$insert,'','','Save Addressbook Contacts - Failed',0);
            } 
        }
        
        if($insertval == 1){  
            $insertcheck =  '[{"id":"1"}]';
            echo GibberishAES::enc($insertcheck, $apikey);
	}else{
            $insertcheck = '[{"id":"0"}]';
            echo GibberishAES::enc($insertcheck, $apikey);
	}
	  
    }catch(PDOException $e){
        $insertquery = '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo GibberishAES::enc($insertquery, $apikey);
        insertMobileLog('insert/update',$username,$insertquery,'','','Save Addressbook Contacts - Query Failed',0);
    }
}
function getAddressbookList(){
    try{
        $db = getConnection();
        $apikey = 'rotcoderaclla';
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        $query = "SELECT id,u.organization,u.username,CONCAT(u.fname,' ', u.lname)as name,u.specialty,u.phonew1 as Phone,u.phonecell as Mobile,u.fax,u.email,u.street,u.city,u.state,u.zip, (select title FROM list_options WHERE option_id = u.abook_type AND list_id ='abook_type') as Type FROM users AS u " .
            "LEFT JOIN list_options AS lo ON " .
            "list_id = 'abook_type' AND option_id = u.abook_type " .
            "WHERE u.active = 1 AND ( u.authorized = 1 OR u.username = '' )ORDER BY u.organization, u.lname, u.fname";
        $db->query( "SET NAMES utf8"); 
        $stmt = $db->prepare($query) ;
        $stmt->execute();                       

        $getnames = $stmt->fetchAll(PDO::FETCH_OBJ);
        for($i=0; $i< count($getnames); $i++){
            foreach ($getnames[$i] as $key=>$val) {
                if ($val == null || $val == '' || $val == '0000-00-00' || $val == '0000-00-00 00:00:00' || $val == ' ')
                   unset($getnames[$i]->$key);
            }
        }
//        var_dump($getnames);
        $getnames2 = array_values($getnames);
//        echo "<pre>"; print_r($getnames2); echo "</pre>";
        if($getnames2){  
            $insertcheck = json_encode($getnames2);
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
function editAddressbook($id){
    try{
        $db = getConnection();
        $apikey = 'rotcoderaclla';
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        
        $sql = "SELECT * FROM `users` WHERE id= '$id'"; 
        $db->query( "SET NAMES utf8"); 
        $stmt = $db->prepare($sql) ;
        $stmt->execute();  
        $userdata = $stmt->fetchAll(PDO::FETCH_OBJ); 
        
        // for type
        $sql1 = "SELECT title,option_id FROM `list_options` WHERE list_id ='abook_type' order by seq";  
        $db->query( "SET NAMES utf8"); 
        $stmt1 = $db->prepare($sql1) ;
        $stmt1->execute();  
        $abook_types = $stmt1->fetchAll(PDO::FETCH_OBJ);  
        $abook_type = array();
        if(!empty($abook_types)){
//            foreach($abook_types as $list){
            for($i=0; $i< count($abook_types); $i++){
                $abook_type[$i]['optionid'] = $abook_types[$i]->option_id;
                $abook_type[$i]['optionval'] =  $abook_types[$i]->title;
            }
        }
        // for titles
        $sql2 = "SELECT title,option_id FROM `list_options` WHERE list_id ='titles' order by seq";  
        $db->query( "SET NAMES utf8"); 
        $stmt2 = $db->prepare($sql2) ;
        $stmt2->execute();  
        $get_titles = $stmt2->fetchAll(PDO::FETCH_OBJ);  
        $titles = array();
        if(!empty($get_titles)){
            for($i=0; $i< count($get_titles); $i++){
                $titles[$i]['optionid'] = $get_titles[$i]->option_id;
                $titles[$i]['optionval'] =  $get_titles[$i]->title;
            }
        }
        $dataarray[0]['label'] = 'Type';
        $dataarray[0]['field_id'] = 'abook_type';
        $dataarray[0]['field_type'] = 'Listbox';
        $dataarray[0]['value'] = isset($userdata[0]->abook_type) ? $userdata[0]->abook_type : '';
        $dataarray[0]['options'] = $abook_type;
        $dataarray[0]['isRequired'] = 'Optional';
        $dataarray[0]['placeholder'] = 'Select AddressBook type';
        
        //Organization:
        $dataarray[1]['label'] = 'Organization';
        $dataarray[1]['field_id'] = 'organization';
        $dataarray[1]['field_type'] = 'TextBox';
        $dataarray[1]['value'] = isset($userdata[0]->organization) ? $userdata[0]->organization : '';
        $dataarray[1]['placeholder'] = 'Enter Organization name';
        $dataarray[1]['isRequired'] = 'Required';
        $dataarray[1]['max_length'] = '60';

        
        //Director Name:
        $dataarray[2]['label'] = 'Name';
        $dataarray[2]['field_id'] = 'title';
        $dataarray[2]['field_type'] = 'Listbox';
        $dataarray[2]['value'] = isset($userdata[0]->title) ? $userdata[0]->title : '';
        $dataarray[2]['options'] = $titles;
        $dataarray[2]['isRequired'] = 'Optional';
        $dataarray[2]['max_length'] = '4';
        
        //LName:
        $dataarray[4]['label'] = 'Last';
        $dataarray[4]['field_id'] = 'lname';
        $dataarray[4]['field_type'] = 'TextBox';
        $dataarray[4]['value'] = isset($userdata[0]->lname) ? $userdata[0]->lname : '';
        $dataarray[4]['placeholder'] = 'Enter last name';
        $dataarray[4]['isRequired'] = 'Optional';

        
        //FName:
        $dataarray[5]['label'] = 'First';
        $dataarray[5]['field_id'] = 'fname';
        $dataarray[5]['field_type'] = 'TextBox';
        $dataarray[5]['value'] = isset($userdata[0]->fname) ? $userdata[0]->fname : '';
        $dataarray[5]['placeholder'] = 'Enter first name';
        $dataarray[5]['isRequired'] = 'Optional';
        $dataarray[5]['max_length'] = '60';
        
        //MName:
        $dataarray[6]['label'] = 'Middle';
        $dataarray[6]['field_id'] = 'mname';
        $dataarray[6]['field_type'] = 'TextBox';
        $dataarray[6]['value'] = isset($userdata[0]->mname) ? $userdata[0]->mname : '';
        $dataarray[6]['placeholder'] = 'Enter middle name';
        $dataarray[6]['isRequired'] = 'Optional';
        $dataarray[6]['max_length'] = '60';
        
        //Valedictory:
        $dataarray[7]['label'] = 'Valedictory';
        $dataarray[7]['field_id'] = 'valedictory';
        $dataarray[7]['field_type'] = 'TextBox';
        $dataarray[7]['value'] = isset($userdata[0]->valedictory) ? $userdata[0]->valedictory : '';
        $dataarray[7]['placeholder'] = 'Enter valedictory';
        $dataarray[7]['isRequired'] = 'Optional';
        $dataarray[7]['max_length'] = '60';
        
        //Home Phone:
        $dataarray[8]['label'] = 'Home Phone';
        $dataarray[8]['field_id'] = 'phone';
        $dataarray[8]['field_type'] = 'TextBox';
        $dataarray[8]['value'] = isset($userdata[0]->phone) ? $userdata[0]->phone : '';
        $dataarray[8]['placeholder'] = 'Enter home phone number';
        $dataarray[8]['isRequired'] = 'Optional';
        $dataarray[8]['max_length'] = '10';
        
        //MObile:
        $dataarray[9]['label'] = 'Mobile';
        $dataarray[9]['field_id'] = 'phonecell';
        $dataarray[9]['field_type'] = 'TextBox';
        $dataarray[9]['value'] = isset($userdata[0]->phonecell) ? $userdata[0]->phonecell : '';
        $dataarray[9]['placeholder'] = 'Enter mobile number';
        $dataarray[9]['isRequired'] = 'Optional';
        $dataarray[9]['max_length'] = '10';
        
        //Work Phone:
        $dataarray[10]['label'] = 'Work Phone';
        $dataarray[10]['field_id'] = 'phonew1';
        $dataarray[10]['field_type'] = 'TextBox';
        $dataarray[10]['value'] = isset($userdata[0]->phonew1) ? $userdata[0]->phonew1 : '';
        $dataarray[10]['placeholder'] = 'Enter work phone number';
        $dataarray[10]['isRequired'] = 'Optional';
        $dataarray[10]['max_length'] = '12';
        
        //Work Phone 2:
        $dataarray[11]['label'] = '2nd';
        $dataarray[11]['field_id'] = 'phonew2';
        $dataarray[11]['field_type'] = 'TextBox';
        $dataarray[11]['value'] = isset($userdata[0]->phonew2) ? $userdata[0]->phonew2 : '';
        $dataarray[11]['placeholder'] = 'Enter alternate work phone number';
        $dataarray[11]['isRequired'] = 'Optional';
        $dataarray[11]['max_length'] = '10';
        
        //Work Phone 2:
        $dataarray[12]['label'] = 'Fax';
        $dataarray[12]['field_id'] = 'fax';
        $dataarray[12]['field_type'] = 'TextBox';
        $dataarray[12]['value'] = isset($userdata[0]->fax) ? $userdata[0]->fax : '';
        $dataarray[12]['placeholder'] = 'Enter fax number';
        $dataarray[12]['isRequired'] = 'Optional';
        $dataarray[12]['max_length'] = '20';
        
        //Assistant:
        $dataarray[13]['label'] = 'Assistant';
        $dataarray[13]['field_id'] = 'assistant';
        $dataarray[13]['field_type'] = 'TextBox';
        $dataarray[13]['value'] = isset($userdata[0]->assistant) ? $userdata[0]->assistant : '';
        $dataarray[13]['placeholder'] = 'Enter assistant';
        $dataarray[13]['isRequired'] = 'Optional';
        $dataarray[13]['max_length'] = '60';
        
        //Email:
        $dataarray[14]['label'] = 'Email';
        $dataarray[14]['field_id'] = 'email';
        $dataarray[14]['field_type'] = 'TextBox';
        $dataarray[14]['value'] = isset($userdata[0]->email) ? $userdata[0]->email : '';
        $dataarray[14]['placeholder'] = 'Enter email address';
        $dataarray[14]['isRequired'] = 'Optional';
        $dataarray[14]['max_length'] = '60';
        
        //Website
        $dataarray[15]['label'] = 'Website';
        $dataarray[15]['field_id'] = 'url';
        $dataarray[15]['field_type'] = 'TextBox';
        $dataarray[15]['value'] = isset($userdata[0]->url) ? $userdata[0]->url : '';
        $dataarray[15]['placeholder'] = 'Enter website url';
        $dataarray[15]['isRequired'] = 'Optional';
        $dataarray[15]['max_length'] = '60';
        
        //Main Address
        $dataarray[16]['label'] = 'Main Address';
        $dataarray[16]['field_id'] = 'street';
        $dataarray[16]['field_type'] = 'TextBox';
        $dataarray[16]['value'] = isset($userdata[0]->street) ? $userdata[0]->street : '';
        $dataarray[16]['placeholder'] = 'Enter street address';
        $dataarray[16]['isRequired'] = 'Optional';
        $dataarray[16]['max_length'] = '60';
        
        //Main Address
        $dataarray[17]['label'] = '';
        $dataarray[17]['field_id'] = 'street2';
        $dataarray[17]['field_type'] = 'TextBox';
        $dataarray[17]['value'] = isset($userdata[0]->street2) ? $userdata[0]->street2 : '';
        $dataarray[17]['placeholder'] = 'Enter street address';
        $dataarray[17]['isRequired'] = 'Optional';
        $dataarray[17]['max_length'] = '60';
        
        //City
        $dataarray[18]['label'] = 'City';
        $dataarray[18]['field_id'] = 'city';
        $dataarray[18]['field_type'] = 'TextBox';
        $dataarray[18]['value'] = isset($userdata[0]->city) ? $userdata[0]->city : '';
        $dataarray[18]['placeholder'] = 'Enter city';
        $dataarray[18]['isRequired'] = 'Optional';
        $dataarray[18]['max_length'] = '60';
        
        //State/county
        $dataarray[19]['label'] = ' State/county';
        $dataarray[19]['field_id'] = 'state';
        $dataarray[19]['field_type'] = 'TextBox';
        $dataarray[19]['value'] = isset($userdata[0]->state) ? $userdata[0]->state : '';
        $dataarray[19]['placeholder'] = 'Enter state';
        $dataarray[19]['isRequired'] = 'Optional';
        $dataarray[19]['max_length'] = '60';
        
        //Postal code
        $dataarray[20]['label'] = 'Postal code';
        $dataarray[20]['field_id'] = 'zip';
        $dataarray[20]['field_type'] = 'TextBox';
        $dataarray[20]['value'] = isset($userdata[0]->zip) ? $userdata[0]->zip : '';
        $dataarray[20]['placeholder'] = 'Enter postal code / zip';
        $dataarray[20]['isRequired'] = 'Optional';
        $dataarray[20]['max_length'] = '10';
        
        //'Alt Address
        $dataarray[21]['label'] = 'Alt Address';
        $dataarray[21]['field_id'] = 'streetb';
        $dataarray[21]['field_type'] = 'TextBox';
        $dataarray[21]['value'] = isset($userdata[0]->streetb) ? $userdata[0]->streetb : '';
        $dataarray[21]['placeholder'] = 'Enter alternate street address';
        $dataarray[21]['isRequired'] = 'Optional';
        $dataarray[21]['max_length'] = '60';
        
        //'Alt Address 2
        $dataarray[22]['label'] = '';
        $dataarray[22]['field_id'] = 'streetb2';
        $dataarray[22]['field_type'] = 'TextBox';
        $dataarray[22]['value'] = isset($userdata[0]->streetb2) ? $userdata[0]->streetb2 : '';
        $dataarray[22]['placeholder'] = 'Enter alternate street address';
        $dataarray[22]['isRequired'] = 'Optional';
        $dataarray[22]['max_length'] = '60';
        
        //City
        $dataarray[23]['label'] = 'City';
        $dataarray[23]['field_id'] = 'city2';
        $dataarray[23]['field_type'] = 'TextBox';
        $dataarray[23]['value'] = isset($userdata[0]->city2) ? $userdata[0]->city2 : '';
        $dataarray[23]['placeholder'] = 'Enter alternate city address';
        $dataarray[23]['isRequired'] = 'Optional';
        $dataarray[23]['max_length'] = '60';
        
        //State/county
        $dataarray[24]['label'] = 'State/county';
        $dataarray[24]['field_id'] = 'state2';
        $dataarray[24]['field_type'] = 'TextBox';
        $dataarray[24]['value'] = isset($userdata[0]->state2) ? $userdata[0]->state2 : '';
        $dataarray[24]['placeholder'] = 'Enter alternate state address';
        $dataarray[24]['isRequired'] = 'Optional';
        $dataarray[24]['max_length'] = '60';
        
        //Postal code
        $dataarray[25]['label'] = 'Postal code';
        $dataarray[25]['field_id'] = 'zip2';
        $dataarray[25]['field_type'] = 'TextBox';
        $dataarray[25]['value'] = isset($userdata[0]->zip2) ? $userdata[0]->zip2 : '';
        $dataarray[25]['placeholder'] = 'Enter alternate Postal code /zip';
        $dataarray[25]['isRequired'] = 'Optional';
        $dataarray[25]['max_length'] = '10';
        
        //UPIN
        $dataarray[26]['label'] = 'UPIN';
        $dataarray[26]['field_id'] = 'upin';
        $dataarray[26]['field_type'] = 'TextBox';
        $dataarray[26]['value'] = isset($userdata[0]->upin) ? $userdata[0]->upin : '';
        $dataarray[26]['placeholder'] = 'Enter UPIN';
        $dataarray[26]['isRequired'] = 'Optional';
        $dataarray[26]['max_length'] = '60';
        
        //NPI
        $dataarray[27]['label'] = 'NPI';
        $dataarray[27]['field_id'] = 'npi';
        $dataarray[27]['field_type'] = 'TextBox';
        $dataarray[27]['value'] = isset($userdata[0]->npi) ? $userdata[0]->npi : '';
        $dataarray[27]['placeholder'] = 'Enter NPI';
        $dataarray[27]['isRequired'] = 'Optional';
        $dataarray[27]['max_length'] = '15';
        
        //TIN
        $dataarray[28]['label'] = 'TIN';
        $dataarray[28]['field_id'] = 'federaltaxid';
        $dataarray[28]['field_type'] = 'TextBox';
        $dataarray[28]['value'] = isset($userdata[0]->federaltaxid) ? $userdata[0]->federaltaxid : '';
        $dataarray[28]['placeholder'] = 'Enter TIN';
        $dataarray[28]['isRequired'] = 'Optional';
        $dataarray[28]['max_length'] = '10';
        
         //Taxonomy
        $dataarray[29]['label'] = 'Taxonomy';
        $dataarray[29]['field_id'] = 'taxonomy';
        $dataarray[29]['field_type'] = 'TextBox';
        $dataarray[29]['value'] = isset($userdata[0]->taxonomy) ? $userdata[0]->taxonomy : '';
        $dataarray[29]['placeholder'] = 'Enter Taxonomy';
        $dataarray[29]['isRequired'] = 'Optional';
        $dataarray[29]['max_length'] = '60';
        
        //Notes
        $dataarray[30]['label'] = 'Notes';
        $dataarray[30]['field_id'] = 'notes';
        $dataarray[30]['field_type'] = 'TextArea';
        $dataarray[30]['value'] = isset($userdata[0]->notes) ? $userdata[0]->notes : '';
        $dataarray[30]['placeholder'] = 'Enter notes here..';
        $dataarray[30]['isRequired'] = 'Optional';
        $dataarray[30]['max_length'] = '200';

        
//         echo "<pre>"; print_r($dataarray); echo "</pre>";
        if($dataarray){  
            $insertcheck = json_encode($dataarray);
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
function saveAddressbook(){
    try{
        $db = getConnection();
        $request = Slim::getInstance()->request();
        $apikey = 'rotcoderaclla';
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        $appres = GibberishAES::dec($request->getBody(), $apikey);
//        $appres = GibberishAES::dec('U2FsdGVkX1/rxj3U9qhSOKjC8dVcP2XHmgypn79IpGJbVximst7lVZOKv6/5qf4b/yra+FvqQeuCkQZ8zEkFPib+/1rz4nACS8sskxrAQGTDCbsWa7jKUFyMUPwuQHw7BChUvzug+pSRGCEKVZ9Vm3x3o3t6SLehie8G09V1AWyKIWHcJ3Uu/bM++RM4WXAzFhyk7O/jrzkrG7YmOXcN7qHDPl6E9B+/RLJeepg1MqtkoUT6kP3oyILvjAB+oX8iR/rMsWTb/2xUnvZygOoEME8Q8MEOhVaOdUXYmCMipnDSsOOwCTfKtjypF1nXJ1y1XrCTICGA6kh+H4/4sBL49ToNLNH3X1wzztU8y5dLFPYlDRGuR8TmOlDZ9GUfh8kBc94Ji/kTJxGjLEEva2L7BWgnnKvByvt2jOrdBT4KSZDQcgYvMgovwlZugAWX4nyoJSNQ4BStg0XP4TLVN+SJ6SFawKjEomoNWnf+MKF7XdDMpZWae7q4oQIVbcSMGEqRXTgXLrTxRUDVFflzBWXhIVRLOaw38KA1CvHTsgJ8f7yqnwOj7hrWuMz2rPccPZ4fUrMeOnzB0pzTJmbOmf6winUFDh/gjQFsSq/5YW1aeKyoak/iaZqF/uJk8M66aBu2x/66A13pemf2Z6ZVnJIZEmIeW9RNgVMudVY5XqE1XpzqOtBy5x9b2LSCgq3tkhZv6lv5t5LniqkBVMl9QQXoyQ==', $apikey);
        $insertArray = json_decode($appres,TRUE);
//        echo "<pre>"; print_r($insertArray); echo "</pre>";
        $updatequery = '';
        $setquery2 = '';
        $setvalues2= '';
        $id = $insertArray['form_id'];
        $username2  = getUserName($insertArray['loginProviderId']);
        $username   = isset($username2[0]['username'])? $username2[0]['username'] : ''; 
        $insertval = 0;
        foreach($insertArray['data'][0] as $key => $value){
            ${$key} = addslashes($value);
            if($key == 'loginProviderId')
                $keyname = 'user';
            else 
               $keyname = $key ;
            
            if($insertArray['form_id'] == 0){
                $setquery2 .= $keyname.",";
                $setvalues2 .= "'".addslashes($value) ."',";
            }else{
                $updatequery .= $keyname." ='".addslashes($value)."',";
            }


        }
        if($insertArray['form_id'] == 0){
            $setquery = rtrim($setquery2,',');
            $setvalues = rtrim($setvalues2,',');
            $query = "INSERT INTO users(authorized,$setquery) VALUES(1,$setvalues) ";
            $chckinsert = 'insert';
            
        }else{
            $update =  rtrim($updatequery,','). "  where id = $id";
            $query = "UPDATE users SET ".$update;
            $chckinsert = 'update';
        }
        $db->query( "SET NAMES utf8");
        $stmt_layout = $db->prepare($query);
        if($stmt_layout->execute()){
            $insertval = 1;
            insertMobileLog($chckinsert,$username,$query,'','','Save Addressbook',1);
        }else{
            $insertval = 0;
            insertMobileLog($chckinsert,$username,$query,'','','Save Addressbook - Failed',0);
        } 

        if($insertval == 1){  
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
        insertMobileLog('insert/update',$username,$patientres,'','','Save Addressbook - Query Failed',0);
    }
}
function getEncounterMedicalRecord($pid,$id) {

    $db = getConnection();
    $apikey = 'rotcoderaclla';
    // The default key size is 256 bits.
    $old_key_size = GibberishAES::size();
    GibberishAES::size(256);
    
    try {
    
        $sql1 = "select field_id from layout_options where group_name LIKE  '%Mobile%' and form_id = 'CHARTOUTPUT' AND uor<> 0 order by seq";
        $sqlstmt1 = $db->prepare($sql1);
        $sqlstmt1->execute();
        $fields = $sqlstmt1->fetchAll(PDO::FETCH_OBJ);
        $db->query("SET SQL_BIG_SELECTS=1"); 
        $titles=''; $columncheck='';
        for($i=0; $i<count($fields); $i++){
            foreach($fields[$i] as $value){
                $titles .= $value.',' ; 
                $columncheck .= $value." <> '' OR ";
            }
         }                 
        // for header
        $header_name = "SELECT CONCAT(fname,' ',lname) AS pname ,pid,DATE_FORMAT(DOB,'%m-%d-%Y') as DOB ,ss FROM patient_data WHERE pid='$pid'";
        $headersql = $db->prepare($header_name);
        $headersql->execute();
        $header_values = $headersql->fetchAll(PDO::FETCH_OBJ);
        $record1=array();
        $patient_details_header = array();
        if(!empty($header_values)){
            foreach($header_values[0] as $key =>$value){
                $patient_details_header[$key] =  $value;
            }
        }

        $header_facility = "SELECT f.name as faclityname, f.street, f.city, f.state, f.postal_code, f.country_code, f.email,f.website,f.fax, f.phone
                                            FROM facility f
                                            WHERE primary_business_entity=1";
        $headerfacility = $db->prepare($header_facility);
        $headerfacility->execute();
        $header_facility_values = $headerfacility->fetchAll(PDO::FETCH_OBJ);
        $patient_facility_header = array();
        if(!empty($header_facility_values)){
            foreach($header_facility_values[0] as $fkey =>$fvalue){
                $patient_facility_header[$fkey] =  $fvalue;
            }
        }
        $record1['Patient Details'] = $patient_details_header;
        $record1['Facility Details'] = $patient_facility_header;
        // end of header
        $titles2 = rtrim($titles ,',');
        $columncheck2 = rtrim($columncheck, ' OR ');
        $res122 = "select DATE_FORMAT( date, '%Y-%m-%d' ) as date_of_service from form_encounter where pid='$pid' AND encounter = '$id' ";
        $resstmt122 = $db->prepare($res122);
        $db->query( "SET NAMES utf8");
        $resstmt122->execute();
        $result122 = $resstmt122->fetchAll(PDO::FETCH_OBJ); 
        $dos = '';
        if(!empty($result122))
            $dos=$result122[0]->date_of_service;
        
        $res12 = "select field_id,option_value from tbl_chartui_mapping where form_id = 'CHARTOUTPUT' AND group_name LIKE '%Mobile' AND option_value = 'YES'
        order by id DESC ";
        $resstmt12 = $db->prepare($res12);
        $db->query( "SET NAMES utf8");
        $resstmt12->execute();
        $result123 = $resstmt12->fetchAll(PDO::FETCH_OBJ); 
        if(!empty($result123)){
            $result12= array();
            for($k=0; $k<count($result123); $k++){
                $result12[$k][$result123[$k]->field_id] = $result123[$k]->option_value;
            }
            
            $demographics=array(); 
            $demo=array();   
            $sql_dem = "SELECT DISTINCT(group_name) as group_name from layout_options where form_id='DEM' and uor <> 0 order by group_name";
            $res_dem = $db->prepare($sql_dem);
            $db->query( "SET NAMES utf8");
            $res_dem->execute();
            $dem_data = $res_dem->fetchAll(PDO::FETCH_OBJ);
           // echo "<pre>"; print_r($dem_data); echo "</pre>";

            for($i=0; $i<count($dem_data); $i++){
                $demo1[substr($dem_data[$i]->group_name, 1)]=new stdClass();
                foreach($dem_data[$i] as $key => $val){
                $gettitles = "SELECT group_concat(field_id) as id, group_concat(title) as title from layout_options where form_id='DEM' and uor <> 0 AND group_name='".$dem_data[$i]->group_name."'"; 
                $res_titles = $db->prepare($gettitles);
                $db->query( "SET NAMES utf8");
                $res_titles->execute();
                $title_data = $res_titles->fetchAll(PDO::FETCH_OBJ);
                if(!empty($title_data)){
                    $getselectedvales ="SELECT field_id as selectedfield FROM tbl_chartui_mapping WHERE screen_name LIKE '%Mobile%' AND group_name = '".$dem_data[$i]->group_name."' AND option_value = 'YES'";   
                    $res_selected = $db->prepare($getselectedvales);
                    $db->query( "SET NAMES utf8");
                    $res_selected->execute();
                    $sel_data = $res_selected->fetchAll(PDO::FETCH_OBJ);
                    //echo "<pre>"; print_r($sel_data);  echo "</pre>";
                     $idlist2=''; $title=''; $fname=''; $mname=''; $lname=''; $check='';
                    for($k=0; $k<count($sel_data); $k++){
                        $selected= explode(',',$title_data[0]->id);

                        for($m=0; $m<count($selected); $m++){
                            if($sel_data[$k]->selectedfield == $selected[$m] ): 
                                if($selected[$m] == 'em_city' || $selected[$m] == 'em_street' || $selected[$m] == 'em_name' || $selected[$m] == 'em_state' || $selected[$m] == 'em_postal_code' || $selected[$m] == 'em_country' ):
                                    $check = 1;
                                    $idlist2 .= "e.".substr($selected[$m],3).",";

                                elseif($selected[$m] == 'title' || $selected[$m] == 'fname' || $selected[$m] == 'lname' || $selected[$m] == 'mname'):
                                    if($selected[$m] == 'title'):
                                        $title = 'p.title,';
                                    elseif($selected[$m] == 'fname'):
                                        $fname = '" ",p.fname,';
                                    elseif($selected[$m] == 'mname'):
                                        $mname = '" ",p.mname,';
                                    elseif($selected[$m] == 'lname'):
                                        $lname = '" ",p.lname';
                                    endif;
                                    $getname = "CONCAT(".$title.$fname.$mname.$lname.") as Name,";
                                else:
                                    $idlist2 .= "p.".$sel_data[$k]->selectedfield.",";
                                endif;
                            endif;
                        }
                    }
                    $idlist = rtrim($idlist2, ',');
                    if($idlist !=''){
                        if(substr($dem_data[$i]->group_name, 1) != 'Who' ):
                            $getname = '';
                        endif;
                        if($check == 1):
                          $getgroupval2 ="SELECT ". $idlist." FROM patient_data p LEFT JOIN employer_data e ON e.pid= p.pid WHERE e.pid = $pid ";
                        else:
                          $getgroupval2 = "SELECT ".$getname. $idlist." FROM patient_data p WHERE pid = $pid ";
                        endif;

                        $getgroupval = $db->prepare($getgroupval2);
                        $db->query( "SET NAMES utf8");
                        $getgroupval->execute();
                        $group_data = $getgroupval->fetchAll(PDO::FETCH_OBJ);

                        $demographics[substr($dem_data[$i]->group_name, 1)]=$group_data;

                        if(!empty($demographics[substr($dem_data[$i]->group_name, 1)])){
                        foreach($demographics[substr($dem_data[$i]->group_name, 1)][0] as $key => $value){
                            $getlistname = "SELECT list_id, field_id, title, data_type FROM layout_options WHERE field_id = '$key'";
                            $getlist = $db->prepare($getlistname);
                            $getlist->execute();
                            $list_data = $getlist->fetchAll(PDO::FETCH_OBJ);
                            // echo "<pre>"; print_r($list_data); echo "</pre>";
                             if(!empty($list_data)){
                                for($p=0;$p<count($list_data);$p++){
                                    if($list_data[$p]->list_id!=''){
                                        $explodeval = explode("|", $value);
                                        for($k=0; $k< count($explodeval); $k++){
                                            $slashed = addslashes($value);
                                            $getvalname = "SELECT title FROM list_options WHERE option_id =  '$slashed' AND list_id = '".$list_data[$p]->list_id."'";
                                            $getlist2 = $db->prepare($getvalname);
                                            $getlist2->execute();
                                            $list_data2 = $getlist2->fetchAll(PDO::FETCH_OBJ);
//                                                       

                                            if(empty($list_data2)){
                                                //echo   "<li><b>".$subtitle.": </b></li>";
                                            }else{
                                                foreach($list_data2 as $setvalname){
                                                    if(!empty($setvalname) && $setvalname != '0000-00-00 00:00:00' && $value != '0000-00-00'){
                                                        $tit=$list_data[$p]->title;
                                                        //$demo[substr($dem_data[$i]->group_name, 1)][$list_data[$p]->title]=$setvalname->title; 
                                                        $demo1[substr($dem_data[$i]->group_name, 1)]->$tit=$setvalname->title;
                                                    }
                                                }
                                            } 
                                        }
                                    }else if($list_data[$p]->list_id==''){
                                        $subtitle2 = ''; 
                                        if($key == 'Name'):
                                                $subtitle2 = 'Name';
                                        else:
                                            if($list_data[$p]->title != ''):
                                                $subtitle2 = $list_data[$p]->title;
                                            else:
                                                $subtitle2 = $list_data[$p]->field_id;
                                            endif; 

                                        endif;    
                                        if($key == 'providerID' || $key == 'ref_providerID')
                                        {
                                            if(!empty($value) && $value != '0000-00-00 00:00:00' && $value != '0000-00-00'){
                                                $getporvidername = "SELECT CONCAT(fname,' ',lname) AS name FROM users WHERE id='$value'";
                                                $getlist = $db->prepare($getporvidername);
                                                $getlist->execute();
                                                $pro_data = $getlist->fetchAll(PDO::FETCH_OBJ);
                                                // echo "<pre>"; print_r($pro_data); echo "</pre>";
                                                $name=$pro_data[0]->name;
//                                                             $demographics[substr($dem_data[$i]->group_name, 1)][0]->$key;
//                                                             $demographics[substr($dem_data[$i]->group_name, 1)][0]->$key=$name;
                                                // $demo[substr($dem_data[$i]->group_name, 1)][$subtitle2]=$name;
                                                $demo1[substr($dem_data[$i]->group_name, 1)]->$subtitle2=$name;
                                            }
                                        }elseif ($key == 'pharmacy_id' ){
                                             if(!empty($value) && $value != '0000-00-00 00:00:00' && $value != '0000-00-00'){ 
                                                $getpharmacyname ="SELECT name FROM pharmacies WHERE id='$value'";
                                                $getphra = $db->prepare($getpharmacyname);
                                                $getphra->execute();
                                                $phr_data = $getphra->fetchAll(PDO::FETCH_OBJ);
                                                $name1=$phr_data[0]->name;
                                                  //$demographics[substr($dem_data[$i]->group_name, 1)][0]->$key=$name1;
                                                  //$demo[substr($dem_data[$i]->group_name, 1)][$subtitle2]=$name1;
                                                 $demo1[substr($dem_data[$i]->group_name, 1)]->$subtitle2=$name1;
                                                }  
                                          }else{
                                            if(!empty($value) && $value != '0000-00-00 00:00:00' && $value != '0000-00-00')
                                                 //  $demographics[substr($dem_data[$i]->group_name, 1)][0]->$key=$value;
                                                $demo1[substr($dem_data[$i]->group_name, 1)]->$subtitle2=$value;

                                        } 
                                  }
                              }
                            }else{
                                if(!empty($value) && $value != '0000-00-00 00:00:00' && $value != '0000-00-00')
                                    if($key == 'Name')
                                        $demo1[substr($dem_data[$i]->group_name, 1)]->$key=$value;
                            }
                        } 
                     } 
                    }   
                } 

                }

           }
            //echo "<pre>"; print_r($demo1); echo "</pre>";
           if(!empty($demo1)){
           $record1['Demographics']=$demo1;
           }
            for($f=0; $f<count($result12); $f++){
                foreach($result12[$f] as $pkey => $pvalue){
                      //INSURANCE
                    if($pkey=='mobile_insurance' && $pvalue=='YES'){
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
                        WHERE insd.pid='".$pid."'";  
                        $res_ins = $db->prepare($getInsuranceData);
                        $db->query( "SET NAMES utf8");
                        $res_ins->execute();
                        $result_ins = $res_ins->fetchAll(PDO::FETCH_OBJ);
                        if(!empty($result_ins)){
                            $record1['Insurance']=$result_ins;
                        }
                    }
                     
                      //IMMUNIZATIONS
                    if($pkey=='mobile_immunizations' && $pvalue='YES') {
                        $sql = "select  c.code_text_short as Vaccine,i1.administered_date AS Date,CONCAT(i1.amount_administered ,' ',i1.amount_administered_unit	)as Amount ,i1.manufacturer as Manufacturer, i1.lot_number as Lot_Number,i1.administered_by_id as AdministeredBy ,i1.education_date as Education_Date,i1.Route ,i1.administration_site as Administration_Site,substring(i1.note,1,20) as immunization_note
                               from immunizations i1 
                               left join code_types ct on ct.ct_key = 'CVX' 
                               left join codes c on c.code_type = ct.ct_id AND i1.cvx_code = c.code 
                               where i1.patient_id ='".$pid."'  and i1.added_erroneously = 0 
                               order by administered_date desc";
                        $res_immun = $db->prepare($sql);
                        $db->query( "SET NAMES utf8");
                        $res_immun->execute();
                        $immun_data = $res_immun->fetchAll(PDO::FETCH_OBJ);
                       // echo "<pre>"; print_r($immun_data); echo "</pre>";
                        for($i=0; $i<count($immun_data); $i++){
                            if($immun_data[$i]->AdministeredBy!=''){
                                $pro_sql1 ="SELECT CONCAT(lname,' ',fname) AS name FROM users WHERE id='".$immun_data[$i]->AdministeredBy."'";
                                $res_pro2 = $db->prepare($pro_sql1);
                                $db->query( "SET NAMES utf8");
                                $res_pro2->execute();
                                $pro_data1 = $res_pro2->fetchAll(PDO::FETCH_OBJ);
                                if(!empty($pro_data1)){
                                    if($pro_data1[0]->name!=''){
                                        $immun_data[$i]->AdministeredBy=$pro_data1[0]->name;
                                    }
                                }
                            }
                            if($immun_data[$i]->Amount!='') {
                                $unit_sql1 ="SELECT title FROM list_options WHERE option_id='".$immun_data[$i]->Amount."' AND list_id='drug_units'";
                                $res_unit1 = $db->prepare($unit_sql1);
                                $db->query( "SET NAMES utf8");
                                $res_unit1->execute();
                                $unit_data1 = $res_unit1->fetchAll(PDO::FETCH_OBJ);
                                //echo "<pre>"; print_r($unit_data1); echo "</pre>";
                                if(!empty($unit_data1) && $unit_data1[0]->title!=''){
                                    $immun_data[$i]->Amount=$unit_data1[0]->title;
                                }
                            }
                         }

                        if(count($immun_data)>0){
                            $record1['Immunization']=$immun_data;
                           
                        }
                    }
                     
                    //prescription
                    if($pkey=='mobile_prescript' && $pvalue='YES') {
                        $sql_pres = "SELECT  * FROM  `prescriptions` WHERE  `patient_id` =".$pid."";
                        $res_presc = $db->prepare($sql_pres);
                        $db->query( "SET NAMES utf8");
                        $res_presc->execute();
                        $presc_data = $res_presc->fetchAll(PDO::FETCH_OBJ);
                        for($i=0; $i<count($presc_data); $i++){
                            if($presc_data[$i]->provider_id!=''){
                                $pro_sql ="SELECT CONCAT(fname,' ',lname) AS name FROM users WHERE id='".$presc_data[$i]->provider_id."'";
                                $res_pro1 = $db->prepare($pro_sql);
                                $db->query( "SET NAMES utf8");
                                $res_pro1->execute();
                                $pro_data = $res_pro1->fetchAll(PDO::FETCH_OBJ);
                                if(!empty($pro_data)){
                                    if($pro_data[0]->name!=''){
                                        $presc_data[$i]->provider_id=$pro_data[0]->name;
                                    }
                                }
                            }
                            if($presc_data[$i]->unit!='') {
                                $unit_sql ="SELECT title FROM list_options WHERE option_id='".$presc_data[$i]->unit."' AND list_id='drug_units'";
                                $res_unit = $db->prepare($unit_sql);
                                $db->query( "SET NAMES utf8");
                                $res_unit->execute();
                                $unit_data = $res_unit->fetchAll(PDO::FETCH_OBJ);
                                if($unit_data[0]->title!=''){
                                    $presc_data[$i]->unit=$unit_data[0]->title;
                                }
                             }
                         }
                        if(count($presc_data)>0){
                            $record1['Prescription']=$presc_data;
                           
                        }
                    }
                    
                     
                    
                    //HISTORY
                    $statusname = '';
                    if($pkey=='mobile_history' && $pvalue='YES'){
                         $crow_grp_data2=array(); 
                         $getgroupval3 = "SELECT DISTINCT(group_name) as group_name from layout_options where form_id='HIS' and uor <> 0 order by group_name";
                         $crow_his = $db->prepare($getgroupval3);
                         $db->query( "SET NAMES utf8");
                         $crow_his->execute();
                         $crow_his_data = $crow_his->fetchAll(PDO::FETCH_OBJ);
                       //echo "<pre>"; print_r($crow_his_data); echo "</pre>";
                     for($i=0; $i<count($crow_his_data); $i++){
                         $his[substr($crow_his_data[$i]->group_name, 1)]=new stdClass();
////                             foreach($crow_his_data[$i] as $key => $value){
                          $gettitles ="SELECT group_concat(field_id) as id, group_concat(title) as title, group_name from layout_options where form_id='HIS' and uor <> 0 AND group_name='".$crow_his_data[$i]->group_name."'";
                          $crow_title = $db->prepare($gettitles);
                          $db->query( "SET NAMES utf8");
                          $crow_title->execute();
                          $crow_title_data = $crow_title->fetchAll(PDO::FETCH_OBJ);
                         //echo "<pre>"; print_r($crow_title_data); echo "</pre>";

                         for($j=0; $j <count($crow_title_data); $j++){
                              $getselectedvales = "SELECT field_id as selectedfield FROM tbl_chartui_mapping WHERE screen_name LIKE  '%Mobile%' AND group_name = '".$crow_his_data[$i]->group_name."' AND option_value = 'YES'";
                              $crow_sel = $db->prepare($getselectedvales);
                              $db->query( "SET NAMES utf8");
                              $crow_sel->execute();
                              $crow_sel_data = $crow_sel->fetchAll(PDO::FETCH_OBJ);
                              $historyidlist2 = '';
                              //echo "<pre>"; print_r($crow_sel_data); echo "</pre>";
                              for($k=0; $k<count($crow_sel_data); $k++){
                                   $selected= explode(',',$crow_title_data[$j]->id);
                                  // echo "<pre>"; print_r($selected); echo "</pre>";
                                   for($m=0; $m< count($selected); $m++){
                                        if($crow_sel_data[$k]->selectedfield == $selected[$m] ):
                                            $historyidlist2 .= "`".$crow_sel_data[$k]->selectedfield."`,";
                                         endif;
                                    }
                               }
                               $historyidlist = rtrim($historyidlist2, ','); 
                              
                              if($historyidlist !=''){
                               // if($crow_his_data[$i]->group_name=='1Past Medical History' || $crow_his_data[$i]->group_name=='2Family History'){
                                   $getgroupcheck = "SELECT  ". $historyidlist."  FROM history_data  WHERE pid = $pid order by date desc limit 1";
                                   $crow_group = $db->prepare($getgroupcheck);
                                   $db->query( "SET NAMES utf8");
                                   $crow_group->execute();
                                   $crow_grp_data = $crow_group->fetchAll(PDO::FETCH_OBJ);
                                   $datacheck= 0; $add=0;
//                                   echo "<pre>"; print_r($crow_grp_data); echo "</pre>";
                                   if(!empty($crow_grp_data)){
                                        foreach ($crow_grp_data[0] as $key3 => $value3) { 
                                            if(empty($value3) || $value3 == '|0||' || $value3 == '|0|'){
                                                $datacheck = $datacheck+1;
                                            }else{    
                                                $add= $add+1;
                                            }    
                                         }
                                   }
                                     $getgroupval = '';
                                     $getgroupval2 ="SELECT  DATE_FORMAT(date,'%m-%d-%Y') as Date1 ,". $historyidlist."  FROM history_data  WHERE pid = $pid order by date desc limit 1";
                                     $crow_group2 = $db->prepare($getgroupval2);
                                     $db->query( "SET NAMES utf8");
                                     $crow_group2->execute();
                                     $crow_grp_data2[substr($crow_his_data[$i]->group_name, 1)] = $crow_group2->fetchAll(PDO::FETCH_OBJ);
                                     if(!empty($crow_grp_data2[substr($crow_his_data[$i]->group_name, 1)])){
                                          foreach($crow_grp_data2[substr($crow_his_data[$i]->group_name, 1)][0] as $key1 => $value){
                                            $explodeval = array();
                                            $lrec='Last Recorded On';
                                             $his[substr($crow_his_data[$i]->group_name, 1)]->$lrec=$crow_grp_data2[substr($crow_his_data[$i]->group_name, 1)][0]->Date1;
                                             $getlistname_his = "SELECT list_id, field_id, title, data_type FROM layout_options WHERE field_id = '$key1'" ;
                                             $crow_group_his = $db->prepare($getlistname_his);
                                             $db->query( "SET NAMES utf8");
                                             $crow_group_his->execute();
                                             $crow_grp_his = $crow_group_his->fetchAll(PDO::FETCH_OBJ);  
                                            // echo "<pre>"; print_r($crow_grp_his); echo "</pre>";
                                            // echo $key1."==".$value;
                                             for($m=0;$m<count($crow_grp_his);$m++){
                                                 if($crow_grp_his[$m]->list_id!=''){
                                                      if($crow_grp_his[$m]->data_type == 23){ 
                                                           if(!empty($value)){
                                                               $explodeval2 = explode("|", $value);
                                                               $explodelist2 = array();
                                                               for($n= 0; $n< count($explodeval2); $n++){
                                                                    $explodelist2 = explode(":", $explodeval2[$n]);
																	$slashed = addslashes($explodelist2[0]);
                                                                    $getvalname1 = "SELECT title FROM list_options WHERE option_id =  'slashed' AND list_id = '".$crow_grp_his[$m]->list_id."'";
                                                                     $crow_valname = $db->prepare($getvalname1);
                                                                     $db->query( "SET NAMES utf8");
                                                                     $crow_valname->execute();
                                                                     $crow_grp_valname = $crow_valname->fetchAll(PDO::FETCH_OBJ);
                                                                     //echo "<pre>"; print_r($crow_grp_valname); echo "</pre>";
                                                                      for($p= 0; $p< count($crow_grp_valname); $p++){
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
                                                                                $res=$crow_grp_valname[$p]->title."&nbsp&nbsp $type&nbsp&nbsp ".$explodelist2[2];
                                                                                $sel=$crow_grp_his[$m]->title;
                                                                                 $his[substr($crow_his_data[$i]->group_name, 1)]->$sel=$res;
                                                                            }   
                                                                      }
                                                               }
                                                           }
                                                      }else if($crow_grp_his[$m]->data_type == 32){
                                                          if(!empty($value) && $value != '|0||'){
                                                               $explodeval = explode("|", $value); 
                                                                $crow_grp_his[$m]->title;
                                                                if($crow_grp_his[$m]->data_type == 32):
                                                                    $slashed = addslashes($explodeval[3]);
                                                                    $getvalname3 = "SELECT title FROM list_options WHERE option_id =  '$slashed' AND list_id = '".$crow_grp_his[$m]->list_id."'";
//                                                                   
                                                                    $crow_valname3 = $db->prepare($getvalname3);
                                                                    $db->query( "SET NAMES utf8");
                                                                     $crow_valname3->execute();
                                                                     $crow_grp_valname3 = $crow_valname3->fetchAll(PDO::FETCH_OBJ);
                                                                     for($q=0; $q<count($crow_grp_valname3); $q++){
                                                                         $tit1=$crow_grp_his[$m]->title;
                                                                         $his[substr($crow_his_data[$i]->group_name, 1)]->$tit1=$crow_grp_valname3[$q]->title;
                                                                     }
                                                                endif;
                                                                $statustypes = array('current'=>'Current','quit' =>'Quit','never' => 'Never','not_applicable' => 'N/A');
                                                                foreach($statustypes as $key => $stype){
                                                                    if($explodeval[1] == $key.$crow_grp_his[$m]->field_id):
                                                                        $statusname = $stype;
                                                                    endif;
                                                                }

                                                                if(!empty($explodeval[0]) || !empty($statusname) || !empty($explodeval[2])){
                                                                   $res2=$explodeval[0].str_repeat('&nbsp;', 5).$statusname. "  ".$explodeval[2];
                                                                   $tit3=$crow_grp_his[$m]->title;
                                                                   $his[substr($crow_his_data[$i]->group_name, 1)]->$tit3=$res2;
                                                                } 
                                                                
                                                          }
                                                      }else {
                                                          $setedvalname = '';
                                                           if(!empty($value) && $value !== '|0|'){
                                                                $explodeval = explode("|", $value);
                                                                for($s=0; $s< count($explodeval); $s++){
                                                                    $slashed = addslashes($explodeval[$s]);
                                                                    $getvalname4 ="SELECT title FROM list_options WHERE option_id =  '$slashed' AND list_id = '".$crow_grp_his[$m]->list_id."'";
//                                                                   while($setvalname=sqlFetchArray($getvalname)){
//                                                                        $setedvalname .=  $setvalname['title'].",";
//                                                                    }
                                                                     $crow_valname4 = $db->prepare($getvalname4);
                                                                     $db->query( "SET NAMES utf8");
                                                                     $crow_valname4->execute();
                                                                     $crow_grp_valname4 = $crow_valname4->fetchAll(PDO::FETCH_OBJ);
                                                                     for($t=0; $t< count($crow_grp_valname4); $t++){
                                                                         $setedvalname=$crow_grp_valname4[$t]->title;
                                                                         if(!empty($setedvalname))
                                                                        
                                                                        $trimedvalue =  rtrim($setedvalname, ',');
                                                                        //$i++;$datacheck = '';
                                                                       if($trimedvalue != ''){
                                                                        $trimedvalue1= $trimedvalue.".";
                                                                        $tit4=$crow_grp_his[$m]->title;
                                                                          $his[substr($crow_his_data[$i]->group_name, 1)]->$tit4=$trimedvalue1;
                                                                       }
                                                                         
                                                                     }
                                                              }
                                                           }
                                                      }
                                                 }else {
                                                      
                                                     if($crow_grp_his[$m]->data_type == 28){
                                                           if(!empty($value) && $value != '|0|'){
                                                                $explodeval = explode("|", $value);
                                                                 

                                                                $statustypes = array('current'=>'Current','quit' =>'Quit','never' => 'Never','not_applicable' => 'N/A');
                                                                foreach($statustypes as $key => $stype){
                                                                    if($explodeval[1] == $key.$crow_grp_his[$m]->field_id):
                                                                        $statusname = $stype;
                                                                    endif;
                                                                }
                                                                if(!empty($explodeval[0]) || !empty($statusname) || !empty($explodeval[2])){
                                                                    $res1=$explodeval[0].str_repeat('&nbsp;', 5).$statusname. "  ".$explodeval[2];
                                                                    $tit5=$crow_grp_his[$m]->title;
                                                                    $his[substr($crow_his_data[$i]->group_name, 1)]->$tit5=$res1;
                                                                }  
                                                                
                                                           }
                                                     }else{
                                                          $subtitle2 = '';
                                                         
                                                            if($key1 == 'Date1'){
                                                                $subtitle2 = 'Last Recorded On';
                                                            }else{
                                                                if($crow_grp_his[$m]->title != ''):
                                                                    $subtitle2 = $crow_grp_his[$m]->title;
                                                                else:
                                                                    $subtitle2 = $crow_grp_his[$m]->field_id;
                                                                endif;
                                                            }
                                                            if(!empty($value)){
                                                                if( $add != 0){
                                                                 // echo "<li><b>".$subtitle2.": </b>".$value;  
                                                                  $his[substr($crow_his_data[$i]->group_name, 1)]->$subtitle2=$value;
                                                                }
                                                            }
                                                                $valcheck = '';
                                                                if($i==0) $valcheck = 1;
//                                                            if($value != ''  && $add >= 1) echo ".";
                                                           
                                                     }
                                                     
                                                 }
                                             }
                                            
                                          }
                                          if(count((array)$his[substr($crow_his_data[$i]->group_name, 1)])== 1 ||count((array)$his[substr($crow_his_data[$i]->group_name, 1)])== 10 ){
                                              unset($his[substr($crow_his_data[$i]->group_name, 1)]);
                                          }
                                     }
                                  //}
                                }
                               }
////                             }
                          }
                         // echo "<pre>"; print_r($his); echo "</pre>";
                          if(!empty($his)){
                              $record1['History']=$his;
                          }
                        //echo "<pre>"; print_r($crow_grp_data2); echo "</pre>";
                     }
                    
                 }
       } 
        }
        $dos1=$dos;
        $medical_record=array();
//       foreach($dos1 as $value){
        $record=array();
        $res_dos= "SELECT encounter,DATE( date ) as date FROM forms WHERE encounter = '$id' AND form_name = 'New Patient Encounter' AND deleted = 0 AND pid = ".$pid;
        $resstmt1_dos = $db->prepare($res_dos);
        $resstmt1_dos->execute();
        $result_dos= $resstmt1_dos->fetchAll(PDO::FETCH_OBJ);
//        echo "<pre>"; print_r($result_dos); echo "</pre>";

      
        foreach($result_dos as $keyp => $valuep){
     
            $encounterid=$valuep->encounter;
            $res = "select field_id,option_value from tbl_chartui_mapping where form_id = 'CHARTOUTPUT' AND group_name LIKE '%Mobile' AND option_value = 'YES'
                  order by id DESC ";
            $resstmt1 = $db->prepare($res);
            $resstmt1->execute();
            $result1 = $resstmt1->fetchAll(PDO::FETCH_OBJ);
            for($k=0; $k<count($result1); $k++){
                $result[$k][$result1[$k]->field_id] = $result1[$k]->option_value;
            }
    // echo "<pre>"; print_r($result); echo "</pre>";    
            if(!empty($result)){
             for($f=0; $f<count($result); $f++){
                foreach($result[$f] as $pkey => $pvalue){
                     //ALLERGIES
                    if($pkey=='mobile_allergy'){
                        $allergysql ="SELECT l.title AS Title, 
                                    CASE 
                                    WHEN enddate IS NULL 
                                    THEN  'Active'
                                    ELSE  'Inactive'
                                    END AS Status, outcome, destination, begdate AS Begin_date, enddate AS End_date, diagnosis AS Codes, occurrence AS Occurrence, referredby AS Referred_By, comments
                                    FROM lists AS l
                                    INNER JOIN issue_encounter AS ie ON ie.list_id = l.id
                                    AND ie.encounter = $encounterid
                                    WHERE l.type =  'allergy'
                                    AND l.pid = $pid 
                                    ORDER BY Status ASC , begdate DESC";
                        $res_den = $db->prepare($allergysql);
                        $db->query( "SET NAMES utf8");
                        $res_den->execute();
                        $field_den = $res_den->fetchAll(PDO::FETCH_OBJ);       
                        if(count($field_den)>0){
                            for($i=0 ; $i< count($field_den); $i++){
                                foreach($field_den[$i] as $dkey => $dvalue){
                                    if($dkey == 'Codes'){
                                        // multiple icd codes description
                                        $icdcodesarray = explode(";",$dvalue);
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
                                            $db->query( "SET NAMES utf8");
                                            $res_den = $db->prepare($icd_description_sql);
                                            $res_den->execute();
                                            $icd_description = $res_den->fetchAll(PDO::FETCH_OBJ);   
                                            if(!empty($icd_description)){
                                                $icd_description_value .=  "( ".$icdcodesarray[$ic] ." ".$icd_description[0]->long_desc.")<br />";
                                            }
                                        }
                                        $field_den[$i]->Description = $icd_description_value;
        //                                echo "<b>". ucfirst(str_replace('_',' ',$dkey.":"))."</b>".$icd_description_value;
                                    }

                                  $record['Allergies']=$field_den;
                                }
                            }
                        }
                    }

                     //ALLCARE ENCOUNTER FORMS
                    if($pkey=='mobile_cc'  || $pkey=='mobile_assess' || $pkey=='mobile_plan' || $pkey=='mobile_f2f' || $pkey=='mobile_hpi' || $pkey=='mobile_progress' ){
                        if($pkey=='mobile_cc'){
                            $grp_name='Chief Complaint';
                        }else if($pkey=='mobile_assess'){
                             $grp_name='Assessment Note';
                        }else if($pkey=='mobile_plan'){
                            $grp_name='Plan Note';
                        }
                        else if($pkey=='mobile_f2f') {
                            $grp_name='Face to Face HH Plan';
                        }
                        else if($pkey=='mobile_hpi'){
                            $grp_name='History of Present illness';
                        }else if($pkey=='mobile_progress'){
                            $grp_name='Progress Note';
                        } 
                        $selected_fields="SELECT field_id as selectedfield FROM tbl_chartui_mapping WHERE screen_name LIKE '%Mobile%' AND form_id = 'LBF2' AND group_name LIKE '%$grp_name' AND option_value = 'YES'";
                        $res_sel = $db->prepare($selected_fields);
                        $db->query( "SET NAMES utf8");
                        $res_sel->execute();
                        $field_sel = $res_sel->fetchAll(PDO::FETCH_OBJ);
                        
                         
                        $get_cc="SELECT field_id,title FROM layout_options WHERE form_id = 'LBF2' AND group_name LIKE '%$grp_name' and field_id LIKE '%_text'";
                        $res_cc = $db->prepare($get_cc);
                        $db->query( "SET NAMES utf8");
                        $res_cc->execute();
                        $field_cc = $res_cc->fetchAll(PDO::FETCH_OBJ);

                        $fieldid=array();
                        foreach($field_cc as $value){ 
                            for($i=0; $i<count($field_sel); $i++){
                                if($field_sel[$i]->selectedfield == $value->field_id):
                                    $fieldid[$value->title] = $value->field_id;
                                endif;
                            }
                        }
                       
                       $cc = array();
                       if(!empty($fieldid)){
                           $getformid = "SELECT form_id FROM forms WHERE encounter = $encounterid AND form_name = 'Allcare Encounter Forms' AND deleted = 0 ";
                           $res_fid = $db->prepare($getformid);
                           $db->query( "SET NAMES utf8");
                           $res_fid->execute();
                           $field_fid = $res_fid->fetchAll(PDO::FETCH_OBJ);
                          
                           if(!empty($field_fid)){
                               for($i=0; $i< count($field_fid); $i++){
                                   $formid = $field_fid[$i]->form_id;
                                   $datacheck = '';
                                   //echo "<pre>"; print_r($fieldid); echo "</pre>";
                                   foreach($fieldid as $fkey => $fid){
                                     $getCCdata = "SELECT field_value FROM lbf_data WHERE form_id = '$formid' AND field_id = '".$fid."'";
                                       $db->query( "SET NAMES utf8"); 
                                      $cc_data = $db->prepare($getCCdata);
                                      $cc_data->execute();
                                      $cc_result = $cc_data->fetchAll(PDO::FETCH_OBJ);
                                      //echo "<pre>"; print_r($cc_result); echo "</pre>";
                                      for($j=0;$j<count($cc_result);$j++) {
                                          if(!empty($cc_result[$i]->field_value)){
                                                if($pvalue=='mobile_f2f' &&($fkey=='Signed By (Physician)' || $fkey=='Signed by (NP)')){
                                                    $pro_sql11 ="SELECT CONCAT(lname,' ',fname) AS name FROM users WHERE id='".$cc_result[$i]->field_value."'";
                                                    $res_pro21 = $db->prepare($pro_sql11);
                                                    $db->query( "SET NAMES utf8");
                                                    $res_pro21->execute();
                                                    $pro_data11 = $res_pro21->fetchAll(PDO::FETCH_OBJ);
                                                   // echo "<pre>"; print_r($pro_data11); echo "</pre>";
                                                   $cc[$fkey]=isset($pro_data11[0]->name)? $pro_data11[0]->name: '';
                                                }else {
                                                   $cc[$fkey]=$cc_result[$i]->field_value;
                                                }
                                            }
                                        }
                                    }
                               }
                            } 
                        } 
                       if(!empty($cc)){ 
                       $record[$grp_name]=$cc;
                       }
                     
                   } 
                 //MEDICATIONS
                    if($pkey =='mobile_med') {
                                                  $medicationsql ="SELECT l.title AS Title, 
                                    CASE 
                                    WHEN enddate IS NULL 
                                    THEN  'Active'
                                    ELSE  'Inactive'
                                    END AS Status, outcome, destination, begdate AS Begin_date, enddate AS End_date, diagnosis AS Codes, occurrence AS Occurrence, referredby AS Referred_By, comments
                                    FROM lists AS l
                                    INNER JOIN issue_encounter AS ie ON ie.list_id = l.id
                                    AND ie.encounter = $encounterid
                                    WHERE l.type =  'medication'
                                    AND l.pid = $pid 
                                    ORDER BY Status ASC , begdate DESC";
                        $res_den = $db->prepare($medicationsql);
                        $db->query( "SET NAMES utf8");
                        $res_den->execute();
                        $field_den = $res_den->fetchAll(PDO::FETCH_OBJ);       
                        if(count($field_den)>0){
                            for($i=0 ; $i< count($field_den); $i++){
                                foreach($field_den[$i] as $dkey => $dvalue){
                                    if($dkey == 'Codes'){
                                        // multiple icd codes description
                                        $icdcodesarray = explode(";",$dvalue);
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
                                            $db->query( "SET NAMES utf8");
                                            $res_den = $db->prepare($icd_description_sql);
                                            $res_den->execute();
                                            $icd_description = $res_den->fetchAll(PDO::FETCH_OBJ);   
                                            if(!empty($icd_description)){
                                                $icd_description_value .=  "( ".$icdcodesarray[$ic] ." ".$icd_description[0]->long_desc.")<br />";
                                            }
                                        }
                                        $field_den[$i]->Description = $icd_description_value;
        //                                echo "<b>". ucfirst(str_replace('_',' ',$dkey.":"))."</b>".$icd_description_value;
                                    }

                                  $record['Medication']=$field_den;
                                }
                            }
                        }
                    }
                    //MEDICAL PROBLEMS
                    if($pkey=='mobile_mproblem') {
                                                   $medicalProblemsql ="SELECT l.title AS Title, 
                                    CASE 
                                    WHEN enddate IS NULL 
                                    THEN  'Active'
                                    ELSE  'Inactive'
                                    END AS Status, outcome, destination, begdate AS Begin_date, enddate AS End_date, diagnosis AS Codes, occurrence AS Occurrence, referredby AS Referred_By, comments
                                    FROM lists AS l
                                    INNER JOIN issue_encounter AS ie ON ie.list_id = l.id
                                    AND ie.encounter = $encounterid
                                    WHERE l.type =  'medical_problem'
                                    AND l.pid = $pid 
                                    ORDER BY Status ASC , begdate DESC";
                        $res_den = $db->prepare($medicalProblemsql);
                        $db->query( "SET NAMES utf8");
                        $res_den->execute();
                        $field_den = $res_den->fetchAll(PDO::FETCH_OBJ);       
                        if(count($field_den)>0){
                            for($i=0 ; $i< count($field_den); $i++){
                                foreach($field_den[$i] as $dkey => $dvalue){
                                    if($dkey == 'Codes'){
                                        // multiple icd codes description
                                        $icdcodesarray = explode(";",$dvalue);
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
                                            $db->query( "SET NAMES utf8");
                                            $res_den = $db->prepare($icd_description_sql);
                                            $res_den->execute();
                                            $icd_description = $res_den->fetchAll(PDO::FETCH_OBJ);   
                                            if(!empty($icd_description)){
                                                $icd_description_value .=  "( ".$icdcodesarray[$ic] ." ".$icd_description[0]->long_desc.")<br />";
                                            }
                                        }
                                        $field_den[$i]->Description = $icd_description_value;
        //                                echo "<b>". ucfirst(str_replace('_',' ',$dkey.":"))."</b>".$icd_description_value;
                                    }

                                  $record['Medical Problems']=$field_den;
                                }
                            }
                        }
                    }
                     //SURGERIES
                    if($pkey =='mobile_surgery') {
                            $surgerysql ="SELECT l.title AS Title, 
                                    CASE 
                                    WHEN enddate IS NULL 
                                    THEN  'Active'
                                    ELSE  'Inactive'
                                    END AS Status, outcome, destination, begdate AS Begin_date, enddate AS End_date, diagnosis AS Codes, occurrence AS Occurrence, referredby AS Referred_By, comments
                                    FROM lists AS l
                                    INNER JOIN issue_encounter AS ie ON ie.list_id = l.id
                                    AND ie.encounter = $encounterid
                                    WHERE l.type =  'surgery'
                                    AND l.pid = $pid 
                                    ORDER BY Status ASC , begdate DESC";
                        $res_den = $db->prepare($surgerysql);
                        $db->query( "SET NAMES utf8");
                        $res_den->execute();
                        $field_den = $res_den->fetchAll(PDO::FETCH_OBJ);       
                        if(count($field_den)>0){
                            for($i=0 ; $i< count($field_den); $i++){
                                foreach($field_den[$i] as $dkey => $dvalue){
                                    if($dkey == 'Codes'){
                                        // multiple icd codes description
                                        $icdcodesarray = explode(";",$dvalue);
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
                                            $db->query( "SET NAMES utf8");
                                            $res_den = $db->prepare($icd_description_sql);
                                            $res_den->execute();
                                            $icd_description = $res_den->fetchAll(PDO::FETCH_OBJ);   
                                            if(!empty($icd_description)){
                                                $icd_description_value .=  "( ".$icdcodesarray[$ic] ." ".$icd_description[0]->long_desc.")<br />";
                                            }
                                        }
                                        $field_den[$i]->Description = $icd_description_value;
        //                                echo "<b>". ucfirst(str_replace('_',' ',$dkey.":"))."</b>".$icd_description_value;
                                    }

                                  $record['Surgeries']=$field_den;
                                }
                            }
                        }
                    }
                    //DENTAL ISSUES
                    if($pkey=='mobile_dental') {
                           $dentalsql ="SELECT l.title AS Title, 
                                    CASE 
                                    WHEN enddate IS NULL 
                                    THEN  'Active'
                                    ELSE  'Inactive'
                                    END AS Status, outcome, destination, begdate AS Begin_date, enddate AS End_date, diagnosis AS Codes, occurrence AS Occurrence, referredby AS Referred_By, comments
                                    FROM lists AS l
                                    INNER JOIN issue_encounter AS ie ON ie.list_id = l.id
                                    AND ie.encounter = $encounterid
                                    WHERE l.type =  'dental'
                                    AND l.pid = $pid 
                                    ORDER BY Status ASC , begdate DESC";
                        $res_den = $db->prepare($dentalsql);
                        $db->query( "SET NAMES utf8");
                        $res_den->execute();
                        $field_den = $res_den->fetchAll(PDO::FETCH_OBJ);       
                        if(count($field_den)>0){
                            for($i=0 ; $i< count($field_den); $i++){
                                foreach($field_den[$i] as $dkey => $dvalue){
                                    if($dkey == 'Codes'){
                                        // multiple icd codes description
                                        $icdcodesarray = explode(";",$dvalue);
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
                                            $db->query( "SET NAMES utf8");
                                            $res_den = $db->prepare($icd_description_sql);
                                            $res_den->execute();
                                            $icd_description = $res_den->fetchAll(PDO::FETCH_OBJ);   
                                            if(!empty($icd_description)){
                                                $icd_description_value .=  "( ".$icdcodesarray[$ic] ." ".$icd_description[0]->long_desc.")<br />";
                                            }
                                        }
                                        $field_den[$i]->Description = $icd_description_value;
        //                                echo "<b>". ucfirst(str_replace('_',' ',$dkey.":"))."</b>".$icd_description_value;
                                    }

                                  $record['Dental Issues']=$field_den;
                                }
                            }
                        }
                    }
                    //VITALS
                    if($pkey=='mobile_vitals' ) {
                        $getVitals="SELECT DATE( v.date ) AS Service_Date, v.bps AS BPS, v.bpd AS BPD, v.weight AS Wt, v.height AS Ht, v.temperature, v.respiration AS RR, note, v.BMI, v.head_circ
                                    FROM form_vitals v
                                    INNER JOIN forms f ON v.id = f.form_id
                                    AND f.pid = v.pid
                                    WHERE v.pid='".$pid."'
                                    AND encounter = ($encounterid) AND f.deleted = 0 and formdir='vitals'";
                        $res_vitals = $db->prepare($getVitals); $db->query( "SET NAMES utf8");
                        
                        $res_vitals->execute();
                        $vitaldata = $res_vitals->fetchAll(PDO::FETCH_OBJ);      
                        $get_rendering_provider ="SELECT CONCAT(u.fname,' ',u.lname) AS provider_name ,DATE_FORMAT(f.date,'%m-%d-%Y') as date
                                                FROM users u
                                                INNER JOIN form_encounter f ON f.provider_id = u.id
                                                WHERE f.encounter = $encounterid ";
                        $res_pro = $db->prepare($get_rendering_provider);
                        $db->query( "SET NAMES utf8");
                        $res_pro->execute();
                        $set_rendering_provider = $res_pro->fetchAll(PDO::FETCH_OBJ); 
                        if(count($vitaldata)>0){
                          $record['Vitals']=$vitaldata;
                       
                        }
                    }
                    //dme
                    if($pkey=='mobile_dme') {
                                                   $dmesql ="SELECT l.title AS Title, 
                                    CASE 
                                    WHEN enddate IS NULL 
                                    THEN  'Active'
                                    ELSE  'Inactive'
                                    END AS Status, outcome, destination, begdate AS Begin_date, enddate AS End_date, diagnosis AS Codes, occurrence AS Occurrence, referredby AS Referred_By, comments
                                    FROM lists AS l
                                    INNER JOIN issue_encounter AS ie ON ie.list_id = l.id
                                    AND ie.encounter = $encounterid
                                    WHERE l.type =  'DME'
                                    AND l.pid = $pid 
                                    ORDER BY Status ASC , begdate DESC";
                        $res_den = $db->prepare($dmesql);
                        $db->query( "SET NAMES utf8");
                        $res_den->execute();
                        $field_den = $res_den->fetchAll(PDO::FETCH_OBJ);       
                        if(count($field_den)>0){
                            for($i=0 ; $i< count($field_den); $i++){
                                foreach($field_den[$i] as $dkey => $dvalue){
                                    if($dkey == 'Codes'){
                                        // multiple icd codes description
                                        $icdcodesarray = explode(";",$dvalue);
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
                                            $db->query( "SET NAMES utf8");
                                            $res_den = $db->prepare($icd_description_sql);
                                            $res_den->execute();
                                            $icd_description = $res_den->fetchAll(PDO::FETCH_OBJ);   
                                            if(!empty($icd_description)){
                                                $icd_description_value .=  "( ".$icdcodesarray[$ic] ." ".$icd_description[0]->long_desc.")<br />";
                                            }
                                        }
                                        $field_den[$i]->Description = $icd_description_value;
        //                                echo "<b>". ucfirst(str_replace('_',' ',$dkey.":"))."</b>".$icd_description_value;
                                    }

                                  $record['DME']=$field_den;
                                }
                            }
                        }
                    }
                    //CPO
                    if($pkey=='mobile_cpo'){
                       $cpo_result=array();
                       $getselectedvales ="SELECT field_id as selectedfield FROM tbl_chartui_mapping WHERE screen_name LIKE '%Mobile%' AND form_id = 'LBF2' AND group_name LIKE '%CPO' AND option_value = 'YES'";
                       $getsel = $db->prepare($getselectedvales);
                       $db->query( "SET NAMES utf8");
                       $getsel->execute();
                       $sel_data1 = $getsel->fetchAll(PDO::FETCH_OBJ);
                       //echo "<pre>"; print_r($sel_data1); echo "</pre>";
                       
                       $getCpo = "SELECT field_id,title FROM layout_options WHERE form_id = 'LBF2' AND group_name LIKE '%CPO'";
                       $getcpo = $db->prepare($getCpo);
                       $getcpo->execute();
                       $cpo_data1 = $getcpo->fetchAll(PDO::FETCH_OBJ);
                       //echo "<pre>"; print_r($cpo_data1); echo "</pre>";
                       
                       for($i=0;$i <count($cpo_data1); $i++){
                           for($j=0; $j<count($sel_data1); $j++){
                            if($sel_data1[$j]->selectedfield == $cpo_data1[$i]->field_id):
                                ${$cpo_data1[$i]->field_id} = $cpo_data1[$i]->field_id;
                                ${"title".$cpo_data1[$i]->field_id} = $cpo_data1[$i]->title;
                               $fieldid[$cpo_data1[$i]->title] = $cpo_data1[$i]->field_id;
                            endif;
                            }
                       }
                       $date3="select cp.cpo_data, cp.provider_id  from tbl_form_cpo cp INNER JOIN form_encounter fe on fe.pid=cp.pid INNER JOIN forms f on fe.encounter = f.encounter AND f.form_id=cp.id  where cp.pid=".$pid." AND fe.encounter='$encounterid' AND form_name='cpo' and deleted=0 group by cp.id ";
                       $getcpo_form = $db->prepare($date3);
                       $db->query( "SET NAMES utf8");
                       $getcpo_form->execute();
                       $cpo_formdata = $getcpo_form->fetchAll(PDO::FETCH_OBJ);
                       if(!empty($cpo_formdata)){
                          // echo "<pre>"; print_r($cpo_formdata); echo "</pre>";
                           for($k=0; $k<count($cpo_formdata); $k++){
                               foreach($cpo_formdata[$k] as $key => $value){
                                   if($key == 'cpo_data'){
                                       $get_CPO_data = preg_replace_callback ( '!s:(\d+):"(.*?)";!',
                                            function($match) {
                                                return ($match[1] == strlen($match[2])) ? $match[0] : 's:' . strlen($match[2]) . ':"' . $match[2] . '";';
                                            },
                                        $value );
                                        if(!empty($get_CPO_data)){
                                            $cpoarray = unserialize($get_CPO_data);
                                            $count_array = count($cpoarray) ;
                                        }else{
                                            $count_array  = 1;
                                        }
//                                        echo "<pre>"; print_r($cpoarray); echo "</pre>";
//                                        $cpoarray = unserialize($value);
                                        for($i=0; $i< count($count_array); $i++){
                                            foreach($cpoarray[$i] as $key1 => $value1){
                                                if(!empty($value1)){
                                                    if(isset(${$key1})){
                                                        if(${$key1} == 'cpotype'){
                                                            if($key1 == 'cpotype') {    
                                                                $ctype="select title from list_options where list_id='CPO_types' AND option_id='$value1'";
                                                                $crow = $db->prepare($ctype);
                                                                $db->query( "SET NAMES utf8");
                                                                $crow->execute();
                                                                $crow_data = $crow->fetchAll(PDO::FETCH_OBJ);
                                                               // echo "<pre>"; print_r($crow_data); echo "</pre>";
                                                                $cpo_result[$i][${"title".$key1}]=$crow_data[0]->title;
                                                            }
                                                        }
                                                    }
//                                                }
                                                 if(isset(${"cpo_".$key1})){
                                                    if( "cpo_".$key1 == ${"cpo_".$key1} ){ 
                                                        if($key1 == 'timeinterval') {    
                                                            $time="select title from list_options where list_id='Time_Interval' AND option_id='$value1'";
                                                            $crow_time = $db->prepare($time);
                                                            $db->query( "SET NAMES utf8");
                                                            $crow_time->execute();
                                                            $crow_time_data = $crow_time->fetchAll(PDO::FETCH_OBJ);
                                                          // echo "<pre>"; print_r($crow_time_data); echo "</pre>";
                                                            $cpo_result[$i][${"titlecpo_".$key1}]=$crow_time_data[0]->title;
                                                        }elseif($key1 == 'users') {    
                                                            $users="SELECT CONCAT(fname,' ',lname) AS name FROM users WHERE id='$value1'";
                                                            $urow = $db->prepare($users);
                                                            $db->query( "SET NAMES utf8");
                                                            $urow->execute();
                                                            $urow_data = $urow->fetchAll(PDO::FETCH_OBJ);
                                                           // echo "<pre>"; print_r($crow_data); echo "</pre>";
                                                            $cpo_result[$i][${"titlecpo_".$key1}]=$urow_data[0]->name;
                                                        }else{
                                                            //echo  $key1.":".$value1;
                                                            $cpo_result[$i][${"titlecpo_".$key1}]=$value1;
                                                        }
                                                    }
                                                 }
                                                }
                                            }
                                        }
                                    }else{
                                        if($key== 'provider_id'):
                                            $getporvidername ="SELECT CONCAT(fname,' ',lname) AS name FROM users WHERE id='".$value."'";
                                            $crow_pro = $db->prepare($getporvidername);
                                            $db->query( "SET NAMES utf8");
                                            $crow_pro->execute();
                                            $crow_pro_data = $crow_pro->fetchAll(PDO::FETCH_OBJ);
                                            $provider=isset($crow_pro_data[0]->name) ? $crow_pro_data[0]->name : '';
                                            if(!empty($provider)):
                                                $cpo_result[$key]=$provider;
                                            endif;
                                        else:
                                            if(!empty($value)):
                                                 $cpo_result[$key1]=$value1;
                                            endif;
                                        endif;
                                    }
                               }
                           }
                       }
                       //echo "<pre>"; print_r($cpo_result); echo "</pre>";
                       if(!empty($cpo_result)){
                       $record['CPO']=$cpo_result;
                       }
                    }//end of cpo
                    ////CCM
                    if($pkey=='mobile_ccm'){
                       $ccm_result=array();
                       $getselectedvales ="SELECT field_id as selectedfield FROM tbl_chartui_mapping WHERE screen_name LIKE '%Mobile%' AND form_id = 'LBF2' AND group_name LIKE '%CCM' AND option_value = 'YES'";
                       $getsel = $db->prepare($getselectedvales);
                       $db->query( "SET NAMES utf8");
                       $getsel->execute();
                       $sel_data1 = $getsel->fetchAll(PDO::FETCH_OBJ);
                       //echo "<pre>"; print_r($sel_data1); echo "</pre>";
                       
                       $getCpo = "SELECT field_id,title FROM layout_options WHERE form_id = 'LBF2' AND group_name LIKE '%CCM'";
                       $getccm = $db->prepare($getCpo);
                       $getccm->execute();
                       $ccm_data1 = $getccm->fetchAll(PDO::FETCH_OBJ);
                       //echo "<pre>"; print_r($ccm_data1); echo "</pre>";
                       
                       for($i=0;$i <count($ccm_data1); $i++){
                           for($j=0; $j<count($sel_data1); $j++){
                            if($sel_data1[$j]->selectedfield == $ccm_data1[$i]->field_id):
                                ${$ccm_data1[$i]->field_id} = $ccm_data1[$i]->field_id;
                                ${"title".$ccm_data1[$i]->field_id} = $ccm_data1[$i]->title;
                               $fieldid[$ccm_data1[$i]->title] = $ccm_data1[$i]->field_id;
                            endif;
                            }
                       }
                       $date3="select cp.ccm_data, cp.provider_id  from tbl_form_ccm cp INNER JOIN form_encounter fe on fe.pid=cp.pid INNER JOIN forms f on fe.encounter = f.encounter AND f.form_id=cp.id  where cp.pid=".$pid." AND fe.encounter='$encounterid' AND form_name='ccm' and deleted=0 group by cp.id ";
                       $getccm_form = $db->prepare($date3);
                       $db->query( "SET NAMES utf8");
                       $getccm_form->execute();
                       $ccm_formdata = $getccm_form->fetchAll(PDO::FETCH_OBJ);
                       if(!empty($ccm_formdata)){
                          // echo "<pre>"; print_r($ccm_formdata); echo "</pre>";
                           for($k=0; $k<count($ccm_formdata); $k++){
                               foreach($ccm_formdata[$k] as $key => $value){
                                   if($key == 'ccm_data'){
                                        $get_CCM_data = preg_replace_callback ( '!s:(\d+):"(.*?)";!',
                                            function($match) {
                                                return ($match[1] == strlen($match[2])) ? $match[0] : 's:' . strlen($match[2]) . ':"' . $match[2] . '";';
                                            },
                                        $value );
                                        if(!empty($get_CCM_data)){
                                            $ccmarray = unserialize($get_CCM_data);
                                            $count_array = count($ccmarray) ;
                                        }else{
                                            $count_array  = 1;
                                        }
                                        for($i=0; $i< count($count_array); $i++){
                                            foreach($ccmarray[$i] as $key1 => $value1){
                                                if(!empty($value1)){
                                                    if(isset(${$key1})){
                                                        if(${$key1} == 'ccmtype'){
                                                            if($key1 == 'ccmtype') {    
                                                                $ctype="select title from list_options where list_id='CCM_types' AND option_id='$value1'";
                                                                $crow = $db->prepare($ctype);
                                                                $db->query( "SET NAMES utf8");
                                                                $crow->execute();
                                                                $crow_data = $crow->fetchAll(PDO::FETCH_OBJ);
                                                               // echo "<pre>"; print_r($crow_data); echo "</pre>";
                                                                $ccm_result[$i][${"title".$key1}]=$crow_data[0]->title;
                                                            }
                                                        }
                                                    }
//                                                }
                                                 if(isset(${"ccm_".$key1})){
                                                    if( "ccm_".$key1 == ${"ccm_".$key1} ){ 
                                                        if($key1 == 'timeinterval') {    
                                                            $time="select title from list_options where list_id='Time_Interval' AND option_id='$value1'";
                                                            $crow_time = $db->prepare($time);
                                                            $db->query( "SET NAMES utf8");
                                                            $crow_time->execute();
                                                            $crow_time_data = $crow_time->fetchAll(PDO::FETCH_OBJ);
                                                          // echo "<pre>"; print_r($crow_time_data); echo "</pre>";
                                                            $ccm_result[$i][${"titleccm_".$key1}]=$crow_time_data[0]->title;
                                                        }elseif($key1 == 'users') {    
                                                            $users="SELECT CONCAT(fname,' ',lname) AS name FROM users WHERE id='$value1'";
                                                            $urow = $db->prepare($users);
                                                            $db->query( "SET NAMES utf8");
                                                            $urow->execute();
                                                            $urow_data = $urow->fetchAll(PDO::FETCH_OBJ);
                                                           // echo "<pre>"; print_r($crow_data); echo "</pre>";
                                                            $ccm_result[$i][${"titleccm_".$key1}]=$urow_data[0]->name;
                                                        }else{
                                                            //echo  $key1.":".$value1;
                                                            $ccm_result[$i][${"titleccm_".$key1}]=$value1;
                                                        }
                                                    }
                                                 }
                                                }
                                            }
                                        }
                                    }else{
                                        if($key== 'provider_id'):
                                            $getporvidername ="SELECT CONCAT(fname,' ',lname) AS name FROM users WHERE id='".$value."'";
                                            $crow_pro = $db->prepare($getporvidername);
                                            $db->query( "SET NAMES utf8");
                                            $crow_pro->execute();
                                            $crow_pro_data = $crow_pro->fetchAll(PDO::FETCH_OBJ);
                                            $provider=isset($crow_pro_data[0]->name) ? $crow_pro_data[0]->name : '';
                                            if(!empty($provider)):
                                                $ccm_result[$key]=$provider;
                                            endif;
                                        else:
                                            if(!empty($value)):
                                                 $ccm_result[$key1]=$value1;
                                            endif;
                                        endif;
                                    }
                               }
                           }
                       }
                       //echo "<pre>"; print_r($ccm_result); echo "</pre>";
                       if(!empty($ccm_result)){
                       $record['CCM']=$ccm_result;
                       }
                    }//end of ccm
                     //physical_exam
                     if($pkey=='mobile_physical_exam' ){
                         $getphysicalexam="SELECT r.*
                                FROM  tbl_form_physical_exam r
                                INNER JOIN forms f ON r.forms_id = f.form_id
                                WHERE f.pid=".$pid."
                                AND encounter = ($encounterid) AND f.deleted = 0  AND formdir= 'allcare_physical_exam' order by id asc ";
                       
                        $resphysicalexam = $db->prepare($getphysicalexam);
                        $db->query( "SET NAMES utf8");
                        $resphysicalexam->execute();
                        $crow_pe = $resphysicalexam->fetchAll(PDO::FETCH_OBJ);
                       // echo "<pre>"; print_r($crow_pe); echo "</pre>";
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
                                            'PSYABL'   =>'Able To Answer Questions Appropriately'),
                                    'SKIN' => array(
                                            'SKRASH'   =>'Rash or Abnormal Lesions',
                                            'SKCLEAN'   =>'Clean & Intact with Good Skin Turgor'),
                                    'OTHER' => array(
                                            'OTHER'    =>'Other'),
                            );
                        
                         if(!empty($crow_pe)):
                             $desc= '';
                             $crow_phe = array();   
                              foreach ($pelines as $sysname => $sysarray) {
                                  $k = 0;
                                    $sysnamedisp = $sysname;
                                    if ($sysname == '*') {

                                    }
                                    else {
                                      $sysnamedisp = $sysname;
                                    }
                                    $datacheck = 0;
                                    $check = 0;
                                    $namevalue = $sysnamedisp;
                                    if($namevalue != '')
                                        $nameval2 = $namevalue;
                                    $abn = $wnl = $desc = '';
                                    foreach ($sysarray as $line_id => $description) {
                                        
                                        for($i=0; $i<count($crow_pe); $i++){
                                            $abn = $wnl =  '';
//                                            echo "<pre>"; print_r($crow_pe);echo "</pre>";
                                            if(!empty($crow_pe[$i]->line_id) && (!empty($crow_pe[$i]->comments) || !empty($crow_pe[$i]->diagnosis) | !empty($crow_pe[$i]->wnl) || !empty($crow_pe[$i]->abn)) ){
                                                if($crow_pe[$i]->abn == 1 ){
                                                    $abn = " - Abnormal Limits";
                                                }
                                                if( $crow_pe[$i]->wnl == 1 ){
                                                    $wnl = " - Within Normal Limits";
                                                }
                                                if($crow_pe[$i]->line_id == $line_id && $sysnamedisp  != '' && strpos($desc, $description.$wnl.$abn.",<br>") === false ){
                                                     $desc .= $description.$wnl.$abn.",<br>";
                                                }
                                                 
                                            }
                                        }
                                        
                                        
//                                         $crow_phe[$i]->$namevalue = $desc;
                                      
                                     }
                                     if(!empty($desc))
                                        $crow_phe[$k][$namevalue]  = rtrim($desc,',<br>') ;
                                     $k++;
                             }
                         endif;
                           //echo "<pre>"; print_r($crow_pe); echo "</pre>";
                       if(!empty($crow_phe)){
                       $record['Physical Exam']=$crow_phe;
                       }
                       
                     }
                     if($pkey=='mobile_ros' ){
                          $getROS="SELECT r.*
                                    FROM  tbl_form_allcare_ros r
                                    INNER JOIN forms f ON r.id = f.form_id
                                    WHERE r.pid=".$pid."
                                    AND encounter = ($encounterid) AND f.deleted = 0  and formdir='allcare_ros'";
                           $resros = $db->prepare($getROS);
                           $db->query( "SET NAMES utf8");
                           $resros->execute();
                           $crow_ros = $resros->fetchAll(PDO::FETCH_OBJ);
                          // echo "<pre>"; print_r($crow_ros); echo "</pre>";
                           
                           $ros_complete=array();
                            for($i=0; $i<count($crow_ros); $i++){
                                foreach($crow_ros[$i] as $key => $value){
                                   if($key=='constitutional' && $value!='Normal'){
                                       $ros_data=new stdClass();
                                   }
                                   if($key == 'weight_change' || $key == 'weakness' || $key == 'fatigue' || $key == 'anorexia' || $key == 'fever' || $key == 'chills' || $key == 'night_sweats' || $key == 'insomnia' || $key == 'irritability' || $key == 'heat_or_cold' || $key == 'intolerance' || $key == 'change_in_appetite'){
                                        if($value == 'YES'):
                                           $constitutional_val= ucwords(str_replace('_',' ',$key));
                                          $ros_data->$constitutional_val=$value;
                                            $checkval  = 1;
                                             $ros_complete['constitutional']=$ros_data;
                                            
                                        elseif($value == 'NO'):
                                           $constitutional_val= ucwords(str_replace('_',' ',$key)); 
                                          $ros_data->$constitutional_val=$value;
                                            $checkval  = 1;
                                             $ros_complete['constitutional']=$ros_data;
                                        endif;
                                    }elseif($key == 'constitutional_text' ){
                                        if(empty($value)){
                                            $ros_data->Other_Details=$value;
                                            //echo "<b>Other Details:</b>".$value;
                                            $checkval  = 1;
                                             $ros_complete['constitutional']=$ros_data;
                                        }
                                    }
                                                                  
                                    //eyes
                                      if($key=='eyes' && $value!='Normal'){
                                       $ros_eyes=new stdClass();
                                      }
//                                      
                                        
                                     if($key == 'change_in_vision' || $key == 'glaucoma_history' || $key == 'eye_pain' || $key == 'irritation' || $key == 'redness' || $key == 'excessive_tearing' || $key == 'double_vision' || $key == 'blind_spots' || $key == 'photophobia' || $key == 'glaucoma' || $key == 'cataract' || $key == 'injury' || $key == 'ha' || $key =='coryza' || $key == 'obstruction'){

                                         if($value == 'YES'):
                                            $eyes_val= ucwords(str_replace('_',' ',$key));
                                            $ros_eyes->$eyes_val=$value;
                                           $checkval  = 1;
                                           $ros_complete['Eyes']=$ros_eyes;
                                        elseif($value == 'NO'):
                                         $eyes_val= ucwords(str_replace('_',' ',$key));
                                            $ros_eyes->$eyes_val=$value;
                                          $checkval  = 1;
                                           $ros_complete['Eyes']=$ros_eyes;
                                        endif;
                                     }elseif($key == 'eyes_text' ){
                                        if(!empty($value)){
                                            $ros_eyes->Other_Details=$value;
                                            $checkval  = 1;
                                             $ros_complete['Eyes']=$ros_eyes;
                                        }
                                    }
                                    
                                   //ent
                                     if($key=='ent' && $value!='Normal'){
                                       $ros_ent=new stdClass();
                                      }
                                    if($key == 'hearing_loss' || $key == 'discharge' || $key == 'pain' || $key == 'vertigo' || $key == 'tinnitus' || $key == 'frequent_colds' || $key == 'sore_throat' || $key == 'sinus_problems' || $key == 'post_nasal_drip' || $key == 'nosebleed' || $key == 'snoring' || $key == 'apnea' || $key == 'bleeding_gums' || $key =='hoarseness' || $key == 'dental_difficulties' || $key == 'use_of_dentures'){
                                         if($value == 'YES'):
                                           $ent_val= ucwords(str_replace('_',' ',$key));
                                          $ros_ent->$ent_val=$value;
                                            /// $ros_ent[ucwords(str_replace('_',' ',$key))]=$value;
                                         $checkval  = 1;
                                         $ros_complete['Ears, Nose, Mouth, Throat']=$ros_ent;
                                        elseif($value == 'NO'):
                                          $ent_val= ucwords(str_replace('_',' ',$key));
                                          $ros_ent->$ent_val=$value;
                                         $checkval  = 1;
                                         $ros_complete['Ears, Nose, Mouth, Throat']=$ros_ent;
                                        endif;
                                     }elseif($key == 'ent_text' ){
                                        if(!empty($value)){
                                            $ros_ent->Other_Details=$value;
                                            $checkval  = 1;
                                            $ros_complete['Ears, Nose, Mouth, Throat']=$ros_ent;
                                        }
                                    }
                                     
                                   //neck
                                    if($key=='neck' && $value!='Normal'){
                                       $ros_neck=new stdClass();
                                      }
//                                      
                                        
                                     if($key == 'stiffness' || $key == 'neck_pain' || $key == 'masses' || $key == 'tenderness'){

                                         if($value == 'YES'):
                                           $neck_val= ucwords(str_replace('_',' ',$key));
                                          $ros_neck->$neck_val=$value;
                                             //$ros_neck[ucwords(str_replace('_',' ',$key))]=$value;
                                           $checkval  = 1;
                                           $ros_complete['Neck']=$ros_neck;
                                        elseif($value == 'NO'):
                                          $neck_val= ucwords(str_replace('_',' ',$key));
                                          $ros_neck->$neck_val=$value;
                                          $checkval  = 1;
                                           $ros_complete['Neck']=$ros_neck;
                                        endif;
                                     }elseif($key == 'neck_text'){
                                        if(!empty($value)){
                                            $ros_neck->Other_Details=$value;
                                            $checkval  = 1;
                                             $ros_complete['Neck']=$ros_neck;
                                        }
                                    }
                                    
                                    //breast
                                     if($key=='breast' && $value!='Normal'){
                                       $ros_breast=new stdClass();
                                      }
                                    if( $key == 'breast_mass' || $key == 'breast_discharge' || $key == 'biopsy' || $key == 'abnormal_mammogram'){
                                         if($value == 'YES'):
                                          $breast_val= ucwords(str_replace('_',' ',$key));
                                          $ros_breast->$breast_val=$value;
                                         $checkval  = 1;
                                         $ros_complete['Breast']=$ros_breast;
                                        elseif($value == 'NO'):
                                         $breast_val= ucwords(str_replace('_',' ',$key));
                                          $ros_breast->$breast_val=$value;
                                         $checkval  = 1;
                                         $ros_complete['Breast']=$ros_breast;
                                        endif;
                                     }elseif($key == 'breast_text'){
                                        if(!empty($value)){
                                            $ros_breast->Other_Details=$value;
                                            $checkval  = 1;
                                            $ros_complete['Breast']=$ros_breast;
                                        }
                                    }
                                    //respiratory
                                     if($key=='respiratory' && $value!='Normal'){
                                       $ros_respiratory=new stdClass();
                                      }
                                    if( $key == 'cough' || $key == 'sputum' || $key == 'shortness_of_breath' || $key == 'wheezing' || $key == 'hemoptsyis' || $key == 'asthma' || $key == 'copd'){
                                         if($value == 'YES'):
                                         $respiratory_val= ucwords(str_replace('_',' ',$key));
                                          $ros_respiratory->$respiratory_val=$value;
                                         $checkval  = 1;
                                         $ros_complete['Respiratory']=$ros_respiratory;
                                        elseif($value == 'NO'):
                                          $respiratory_val= ucwords(str_replace('_',' ',$key));
                                          $ros_respiratory->$respiratory_val=$value;
                                         $checkval  = 1;
                                         $ros_complete['Respiratory']=$ros_respiratory;
                                        endif;
                                     }elseif($key == 'respiratory_text'){
                                        if(!empty($value)){
                                            $ros_respiratory->Other_Details=$value;
                                            $checkval  = 1;
                                            $ros_complete['Respiratory']=$ros_respiratory;
                                        }
                                    }
                                    //cardiovascular
                                    if($key=='cardiovascular' && $value!='Normal'){
                                       $ros_cardiovascular=new stdClass();
                                      }
                                    if($key == 'chest_pain' || $key == 'palpitation' || $key == 'syncope' || $key == 'pnd' || $key == 'doe' || $key == 'orthopnea' || $key == 'peripheal' || $key == 'edema' || $key == 'legpain_cramping' || $key == 'history_murmur' || $key == 'arrythmia' || $key == 'heart_problem'){
                                         if($value == 'YES'):
                                          $cardiovascular_val= ucwords(str_replace('_',' ',$key));
                                          $ros_cardiovascular->$cardiovascular_val=$value;
                                         $checkval  = 1;
                                         $ros_complete['Cardiovascular']=$ros_cardiovascular;
                                        elseif($value == 'NO'):
                                          $cardiovascular_val= ucwords(str_replace('_',' ',$key));
                                          $ros_cardiovascular->$cardiovascular_val=$value;
                                         $checkval  = 1;
                                         $ros_complete['Cardiovascular']=$ros_cardiovascular;
                                        endif;
                                     }elseif($key == 'cardiovascular_text' ){
                                        if(!empty($value)){
                                            $ros_cardiovascular->Other_Details=$value;
                                            $checkval  = 1;
                                            $ros_complete['Cardiovascular']=$ros_cardiovascular;
                                        }
                                    }
                                    //gastrointestinal
                                    if($key=='gastrointestinal' && $value!='Normal'){
                                       $ros_gastrointestinal=new stdClass();
                                      }
                                    if($key == 'dysphagia' || $key == 'heartburn' || $key == 'bloating' || $key == 'belching' || $key == 'flatulence' || $key == 'nausea' || $key == 'vomiting' || $key == 'hematemesis' || $key == 'gastro_pain' || $key == 'food_intolerance' || $key == 'hepatitis' || $key == 'jaundice' || $key == 'hematochezia' || $key =='changed_bowel' || $key == 'diarrhea' || $key == 'constipation' || $key == 'blood_in_stool'){
                                         if($value == 'YES'):
                                          $gastrointestinal_val= ucwords(str_replace('_',' ',$key));
                                          $ros_gastrointestinal->$gastrointestinal_val=$value;
                                         $checkval  = 1;
                                         $ros_complete['Gastrointestinal']=$ros_gastrointestinal;
                                        elseif($value == 'NO'):
                                         $gastrointestinal_val= ucwords(str_replace('_',' ',$key));
                                          $ros_gastrointestinal->$gastrointestinal_val=$value;
                                         $checkval  = 1;
                                         $ros_complete['Gastrointestinal']=$ros_gastrointestinal;
                                        endif;
                                     }elseif($key == 'gastrointestinal_text' ){
                                        if(!empty($value)){
                                            $ros_gastrointestinal->Other_Details=$value;
                                            $checkval  = 1;
                                            $ros_complete['Gastrointestinal']=$ros_gastrointestinal;
                                        }
                                    }
                                    //genitourinary
                                    if($key=='genitourinary' && $value!='Normal'){
                                       $ros_genitourinary=new stdClass();
                                      }
                                    if($key == 'polyuria' || $key == 'polydypsia' || $key == 'dysuria' || $key == 'hematuria' || $key == 'frequency' || $key == 'urgency' || $key == 'incontinence' || $key == 'renal_stones' || $key == 'utis' || $key == 'blood_in_urine' || $key == 'urinary_retention' || $key == 'change_in_nature_of_urine'){
                                         if($value == 'YES'):
                                          $genitourinary_val= ucwords(str_replace('_',' ',$key));
                                          $ros_genitourinary->$genitourinary_val=$value;
                                         $checkval  = 1;
                                         $ros_complete['Genitourinary General']=$ros_genitourinary;
                                        elseif($value == 'NO'):
                                         $genitourinary_val= ucwords(str_replace('_',' ',$key));
                                          $ros_genitourinary->$genitourinary_val=$value;
                                         $checkval  = 1;
                                         $ros_complete['Genitourinary General']=$ros_genitourinary;
                                        endif;
                                     }elseif($key == 'genitourinary_text' ){
                                        if(!empty($value)){
                                            $ros_genitourinary->Other_Details=$value;
                                            $checkval  = 1;
                                            $ros_complete['Genitourinary General']=$ros_genitourinary;
                                        }
                                    }
                                    //genitourinarymale
                                    if($key=='genitourinarymale' && $value!='Normal'){
                                       $ros_genitourinarymale=new stdClass();
                                      }
                                    if($key == 'hesitancy' || $key == 'dribbling' || $key == 'stream' || $key == 'nocturia' || $key == 'erections' || $key == 'ejaculations' ){
                                         if($value == 'YES'):
                                         $genitourinarymale_val= ucwords(str_replace('_',' ',$key));
                                          $ros_genitourinarymale->$genitourinarymale_val=$value;
                                         $checkval  = 1;
                                         $ros_complete['Genitourinary Male']=$ros_genitourinarymale;
                                        elseif($value == 'NO'):
                                        $genitourinarymale_val= ucwords(str_replace('_',' ',$key));
                                         $ros_genitourinarymale->$genitourinarymale_val=$value;
                                         $checkval  = 1;
                                         $ros_complete['Genitourinary Male']=$ros_genitourinarymale;
                                        endif;
                                     }elseif($key == 'genitourinarymale_text' ){
                                        if(!empty($value)){
                                            $ros_genitourinarymale->Other_Details=$value;
                                            $checkval  = 1;
                                            $ros_complete['Genitourinary Male']=$ros_genitourinarymale;
                                        }
                                    }
                                    //genitourinaryfemale
                                     if($key=='genitourinaryfemale' && $value!='Normal'){
                                       $ros_genitourinaryfemale=new stdClass();new stdClass();
                                      }
                                    if($key == 'g' || $key == 'p' || $key == 'ap' || $key == 'lc' || $key == 'mearche' || $key == 'menopause' || $key == 'lmp' || $key == 'f_frequency' || $key == 'f_flow' || $key == 'f_symptoms' || $key == 'abnormal_hair_growth' || $key == 'f_hirsutism'){
                                         if($value == 'YES'):
                                        $genitourinaryfemale_val= ucwords(str_replace('_',' ',$key));
                                         $ros_genitourinaryfemale->$genitourinaryfemale_val=$value;
                                         $checkval  = 1;
                                         $ros_complete['Genitourinary Female']=$ros_genitourinaryfemale;
                                        elseif($value == 'NO'):
                                          $genitourinaryfemale_val= ucwords(str_replace('_',' ',$key));
                                         $ros_genitourinaryfemale->$genitourinaryfemale_val=$value;
                                         $checkval  = 1;
                                         $ros_complete['Genitourinary Female']=$ros_genitourinaryfemale;
                                        endif;
                                     }elseif($key == 'genitourinaryfemale_text' ){
                                        if(!empty($value)){
                                            $ros_genitourinaryfemale->Other_Details=$value;
                                            $checkval  = 1;
                                            $ros_complete['Genitourinary Female']=$ros_genitourinaryfemale;
                                        }
                                    }
                                    //musculoskeletal
                                    if($key=='musculoskeletal' && $value!='Normal'){
                                       $ros_musculoskeletal=new stdClass();
                                      }
                                    if($key == 'joint_pain' || $key == 'swelling' || $key == 'm_redness' || $key == 'm_warm' || $key == 'm_stiffness' || $key == 'm_aches' || $key == 'fms' || $key == 'arthritis' || $key == 'gout' || $key == 'back_pain' || $key == 'paresthesia' || $key == 'muscle_pain' || $key =='limitation_in_range_of_motion'){
                                         if($value == 'YES'):
                                         $musculoskeletal_val= ucwords(str_replace('_',' ',$key));
                                         $ros_musculoskeletal->$musculoskeletal_val=$value;
                                         $checkval  = 1;
                                         $ros_complete['Musculoskeletal']=$ros_musculoskeletal;
                                        elseif($value == 'NO'):
                                         $musculoskeletal_val= ucwords(str_replace('_',' ',$key));
                                         $ros_musculoskeletal->$musculoskeletal_val=$value;
                                         $checkval  = 1;
                                         $ros_complete['Musculoskeletal']=$ros_musculoskeletal;
                                        endif;
                                     }elseif($key == 'musculoskeletal_text' ){
                                        if(!empty($value)){
                                            $ros_musculoskeletal->Other_Details=$value;
                                            $checkval  = 1;
                                            $ros_complete['Musculoskeletal']=$ros_musculoskeletal;
                                        }
                                    }
                                    //extremities
                                     if($key=='extremities' && $value!='Normal'){
                                       $ros_extremities=new stdClass();
                                      }
                                    if( $key == 'spasms' || $key == 'extreme_tremors'){
                                         if($value == 'YES'):
                                         $extremities_val= ucwords(str_replace('_',' ',$key));
                                         $ros_extremities->$extremities_val=$value;
                                         $checkval  = 1;
                                         $ros_complete['Extremities']=$ros_extremities;
                                        elseif($value == 'NO'):
                                         $extremities_val= ucwords(str_replace('_',' ',$key));
                                         $ros_extremities->$extremities_val=$value;
                                         $checkval  = 1;
                                         $ros_complete['Extremities']=$ros_extremities;
                                        endif;
                                     }elseif($key == 'extremities_text' ){
                                        if(!empty($value)){
                                            $ros_extremities->Other_Details=$value;
                                            $checkval  = 1;
                                            $ros_complete['Extremities']=$ros_extremities;
                                        }
                                    }
                                    //neurologic
                                    if($key=='neurologic' && $value!='Normal'){
                                       $ros_neurologic=new stdClass();
                                      }
                                    if( $key == 'loc' || $key == 'seizures' ||  $key == 'stroke'  || $key == 'tia' || $key == 'n_numbness' || $key == 'n_weakness' || $key == 'paralysis' || $key == 'intellectual_decline' || $key == 'memory_problems' || $key == 'dementia' || $key == 'n_headache' || $key == 'dizziness_vertigo' || $key == 'slurred_speech' || $key =='tremors' || $key == 'migraines' || $key == 'changes_in_mentation' ){
                                         if($value == 'YES'):
                                         $neurologic_val= ucwords(str_replace('_',' ',$key));
                                         $ros_neurologic->$neurologic_val=$value;
                                         $checkval  = 1;
                                         $ros_complete['Neurologic']=$ros_neurologic;
                                        elseif($value == 'NO'):
                                         $neurologic_val= ucwords(str_replace('_',' ',$key));
                                         $ros_neurologic->$neurologic_val=$value;
                                         $checkval  = 1;
                                         $ros_complete['Neurologic']=$ros_neurologic;
                                        endif;
                                     }elseif($key == 'neurologic_text' ){
                                        if(!empty($value)){
                                            $ros_neurologic->Other_Details=$value;
                                            $checkval  = 1;
                                            $ros_complete['Neurologic']=$ros_neurologic;
                                        }
                                    }
                                    //skin
                                    if($key=='skin' && $value!='Normal'){
                                       $ros_skin=new stdClass();
                                      }
                                    if($key == 's_cancer' || $key == 'psoriasis' || $key == 's_acne' || $key == 's_other' || $key == 's_disease' || $key == 'rashes' || $key == 'dryness' || $key == 'itching'){
                                        
                                        if($value == 'YES'):
                                         $skin_val= ucwords(str_replace('_',' ',$key));
                                         $ros_skin->$skin_val=$value;
                                         $checkval  = 1;
                                         $ros_complete['Skin']=$ros_skin;
                                        elseif($value == 'NO'):
                                        $skin_val= ucwords(str_replace('_',' ',$key));
                                         $ros_skin->$skin_val=$value;
                                         $checkval  = 1;
                                         $ros_complete['Skin']=$ros_skin;
                                        endif;
                                     }elseif($key == 'skin_text' ){
                                        if(!empty($value)){
                                            $ros_skin->Other_Details=$value;
                                            $checkval  = 1;
                                            $ros_complete['Skin']=$ros_skin;
                                        }
                                    }
                                    //psychiatric
                                     if($key=='psychiatric' && $value!='Normal'){
                                       $ros_psychiatric=new stdClass();
                                      }
                                    if($key == 'p_diagnosis' || $key == 'p_medication' || $key == 'depression' || $key == 'anxiety' || $key == 'social_difficulties' || $key == 'alcohol_drug_dependence' || $key == 'suicide_thoughts' || $key == 'use_of_antideprassants' || $key == 'thought_content' ){
                                         if($value == 'YES'):
                                         $psychiatric_val= ucwords(str_replace('_',' ',$key));
                                         $ros_psychiatric->$psychiatric_val=$value;
                                         $checkval  = 1;
                                         $ros_complete['Psychiatric']=$ros_psychiatric;
                                        elseif($value == 'NO'):
                                         $psychiatric_val= ucwords(str_replace('_',' ',$key));
                                         $ros_psychiatric->$psychiatric_val=$value;
                                         $checkval  = 1;
                                         $ros_complete['Psychiatric']=$ros_psychiatric;
                                        endif;
                                     }elseif($key == 'psychiatric_text' ){
                                        if(!empty($value)){
                                            $ros_psychiatric->Other_Details=$value;
                                            $checkval  = 1;
                                            $ros_complete['Psychiatric']=$ros_psychiatric;
                                        }
                                    }
                                   // endocrine
                                     if($key=='endocrine' && $value!='Normal'){
                                       $ros_endocrine=new stdClass();
                                      }
                                    if($key == 'thyroid_problems' || $key == 'diabetes' || $key == 'abnormal_blood' || $key == 'goiter' || $key == 'heat_intolerence' || $key == 'cold_intolerence' || $key == 'increased_thirst' || $key == 'excessive_sweating' || $key == 'excessive_hunger'){
                                         if($value == 'YES'):
                                         $endocrine_val= ucwords(str_replace('_',' ',$key));
                                         $ros_endocrine->$endocrine_val=$value;
                                         $checkval  = 1;
                                         $ros_complete['Endocrine']=$ros_endocrine;
                                        elseif($value == 'NO'):
                                         $endocrine_val= ucwords(str_replace('_',' ',$key));
                                         $ros_endocrine->$endocrine_val=$value;
                                         $checkval  = 1;
                                         $ros_complete['Endocrine']=$ros_endocrine;
                                        endif;
                                     }elseif($key == 'endocrine_text' ){
                                        if(!empty($value)){
                                            $ros_endocrine->Other_Details=$value;
                                            $checkval  = 1;
                                            $ros_complete['Endocrine']=$ros_endocrine;
                                        }
                                    }
                                    //hai
                                    if($key=='hai' && $value!='Normal'){
                                       $ros_hai=new stdClass();
                                      }
                                    if($key == 'anemia' || $key == 'fh_blood_problems' || $key == 'bleeding_problems' || $key == 'allergies' || $key == 'frequent_illness' || $key == 'hiv' || $key == 'hai_status' || $key == 'hay_fever' || $key == 'positive_ppd' ){
                                         if($value == 'YES'):
                                         $hai_val= ucwords(str_replace('_',' ',$key));
                                         $ros_hai->$hai_val=$value;
                                         $checkval  = 1;
                                         $ros_complete['Hematologic/Allergic/Immunologic']=$ros_hai;
                                        elseif($value == 'NO'):
                                         $hai_val= ucwords(str_replace('_',' ',$key));
                                         $ros_hai->$hai_val=$value;
                                         $checkval  = 1;
                                         $ros_complete['Hematologic/Allergic/Immunologic']=$ros_hai;
                                        endif;
                                     }elseif($key == 'hai_text' ){
                                        if(!empty($value)){
                                            $ros_hai->Other_Details=$value;
                                            $checkval  = 1;
                                            $ros_complete['Hematologic/Allergic/Immunologic']=$ros_hai;
                                        }
                                    }
                                }
                            }  
                             // echo "<pre>"; print_r($ros_complete); echo "</pre>";
                            if(!empty($ros_complete)){
                              $record['Review of Systems']=$ros_complete;
                            }
                     }
                     
                     //procedure
                      if($pkey=='mobile_procedure' ){
                       $procedure=array();
                            $getProc = "SELECT procedure_order_id FROM procedure_order where encounter_id=$encounterid
                                         and patient_id='".$pid."'";
                            $resProc = $db->prepare($getProc);
                            $db->query( "SET NAMES utf8");
                            $resProc->execute();
                            $crow_proc = $resProc->fetchAll(PDO::FETCH_OBJ);
                         //echo "<pre>"; print_r($crow_proc); echo "</pre>";
                          if(!empty($crow_proc) && $crow_proc[0]->procedure_order_id !== ''){
                              $orderid=$crow_proc[0]->procedure_order_id;
                               $get_procedure ="SELECT " .
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
                               $resprocedure = $db->prepare($get_procedure);
                               $db->query( "SET NAMES utf8");
                               $resprocedure->execute();
                               $crow_procedure = $resprocedure->fetchAll(PDO::FETCH_OBJ);
                                //echo "<pre>"; print_r($crow_procedure); echo "</pre>";
                                $procedure['Order']=$crow_procedure;
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
                               $resprocedure1 = $db->prepare($query);
                               $db->query( "SET NAMES utf8");
                               $resprocedure1->execute();
                               $crow_procedure1 = $resprocedure1->fetchAll(PDO::FETCH_OBJ);
                               //echo "<pre>"; print_r($crow_procedure1); echo "</pre>";
                               $procedure['Reports']=$crow_procedure1;
                               
                               $query1 ="SELECT " .
                                    "ps.result_code as Code, ps.result_text as Name, ps.abnormal as Abn, ps.result as Value, " .
                                    "ps.range as _Range, ps.result_status as Status,  ps.facility as Facility, ps.units as Units, ps.comments as Note " .
                                    "FROM procedure_result AS ps " .
                                    "WHERE ps.procedure_report_id = $orderid " .
                                    "ORDER BY ps.result_code, ps.procedure_result_id";
                               $resprocedure2 = $db->prepare($query1);
                               $db->query( "SET NAMES utf8");
                               $resprocedure2->execute();
                               $crow_procedure2= $resprocedure2->fetchAll(PDO::FETCH_OBJ);
                               //echo "<pre>"; print_r($crow_procedure2); echo "</pre>";
                               $procedure['Results']=$crow_procedure2;
                          }
                          if(!empty($procedure)){
                           $record['Procedure']=$procedure;
                          }
                      }
                 }
             }
            }
               $record1['Encounter_id:'.$encounterid]=$record;
          } 
         
//      }
//            echo "<pre>"; print_r($record1); echo "</pre>";           

            $newdemo4=encode_demo($record1);
            $newdemo['Medical_record'] = check_data_available($newdemo4);
            if($newdemo4)
            {
            
                $newdemores = json_encode($newdemo);
                echo $patientresult = GibberishAES::enc($newdemores, $apikey);
            }
            else
            {
               $newdemores = '[{"id":"0"}]';
               echo $patientresult = GibberishAES::enc($newdemores, $apikey);
            }
    
    } 
        catch(PDOException $e) 
        {
            
          $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
          echo $patientresult = GibberishAES::enc($error, $apikey);
        }
        
     
}// end of getEncounterMedicalRecord
function getDiaog($field){
        $key = 'rotcoderaclla';
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
	$sql = "select * from tbl_form_physical_exam_diagnoses WHERE line_id = :field";      
        
        try 
        {
            $db = getConnection();
            $stmt = $db->prepare($sql) ;
            $stmt->bindParam("field", $field);            
            $stmt->execute();                       
             
            $patientsreminders = $stmt->fetchAll(PDO::FETCH_OBJ);                        
//            echo "<pre>"; print_r($patientsreminders); echo "</pre>";
            if($patientsreminders)
            {
                $patientres = json_encode($patientsreminders); 
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
            
            $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
            echo $patientresult = GibberishAES::enc($error, $key);
        }
}
function editByFacility($id){
        $key = 'rotcoderaclla';
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
	$sql = "select tpf.id,pd.street, pd.city, pd.state, pd.country_code, pd.postal_code, pd.phone_cell,phone_home,phone_biz,phone_contact, tpf.facility_admitdate AS admitdate, tpf.facility_dischargedate AS dischargedate, tpf.facility_notes AS notes,facility_related_links as related_links,tpf.facility_roomno 
                FROM tbl_patientfacility tpf
                INNER JOIN patient_data pd ON pd.pid = tpf.patientid WHERE tpf.id = :id";      
        
        try 
        {
            $db = getConnection();
            $stmt = $db->prepare($sql) ;
            $stmt->bindParam("id", $id);            
            $stmt->execute();                       
             
            $patientsreminders = $stmt->fetchAll(PDO::FETCH_OBJ);                        
//            echo "<pre>"; print_r($patientsreminders); echo "</pre>";
            if($patientsreminders)
            {
                $patientres = json_encode($patientsreminders); 
                echo $patientresult = GibberishAES::enc($patientres, $key);
            }
            else
            {
                $patientres = '[{"id":"0","street":"","city":"","state":"","country_code":"","postal_code":"","phone_cell":"","phone_home":"","phone_biz":"","phone_contact":"","admitdate":"","dischargedate":"","notes":"","related_links":"","facility_roomno":""}]';
                echo $patientresult = GibberishAES::enc($patientres, $key);
            }
        } 
        catch(PDOException $e) 
        {
            
            echo $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
            echo $patientresult = GibberishAES::enc($error, $key);
        }
}
function searchMobilePatientByName($list_id,$loginProviderId,$patientName,$value,$fid){
    try
    {
        $db = getConnection();
        
        $get_sql = "select notes from list_options WHERE list_id = '$list_id'  AND option_id = '$value'";      
        $set_stmt = $db->prepare($get_sql) ;
        $set_stmt->execute();                       

        $get_value = $set_stmt->fetchAll(PDO::FETCH_OBJ);   
        $query = '';
        if(!empty($get_value)){
            $query  = " AND ".$get_value[0]->notes;
        } 
        if($fid != 0){
            $facility = "AND facilityid = '$fid'";
            $join = "INNER JOIN tbl_patientfacility tbl ON p.pid = tbl.patientid";
        }else{
            $facility = '';
            $join =  '';
        }
        $sqlGetMyPatientsByName="SELECT pid,title,fname,lname, DATE_FORMAT(DOB,'%m-%d-%Y') AS DOB,DATE_FORMAT(FROM_DAYS(DATEDIFF(CURDATE(),DOB)), '%Y')+0 AS age,if (sex = 'Female' ,'F','M' ) as sex,street,city,state,country_code,postal_code,phone_home,phone_biz,phone_contact,phone_cell,contact_relationship as emergency_contact, phone_contact as emergency_phone
                                FROM patient_data p
                                $join
                                WHERE fname like '%$patientName%'
                                OR mname like '%$patientName%'
                                OR lname like '%$patientName%'
				OR CONCAT(fname,' ',lname) like '%$patientName%'
                                OR CONCAT(fname,' ',mname) like '%$patientName%'
                                AND providerID='$loginProviderId' 
                                $facility
                                $query 
                                ORDER BY fname,lname limit 100";

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
function searchMobilePatientByNameInFacility($list_id,$loginProviderId,$patientName,$value,$fid,$list_id2,$seen){
    try
    {
        $db = getConnection();
        
        $key = 'rotcoderaclla';
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        
        $get_sql = "select notes from list_options WHERE list_id = '$list_id'  AND option_id = '$value'";      
        $set_stmt = $db->prepare($get_sql) ;
        $set_stmt->execute();                       

        $get_value = $set_stmt->fetchAll(PDO::FETCH_OBJ);   
        $query = '';
        if(!empty($get_value)){
            $query  = " AND ".$get_value[0]->notes;
        } 
        if($fid != 0){
            $facility = "AND facilityid = '$fid'";
            $join = "INNER JOIN tbl_patientfacility tbl ON p.pid = tbl.patientid";
        }else{
            $facility = '';
            $join =  '';
        }
        
        $get_sql2 = "select notes from list_options WHERE list_id = '$list_id2'  AND option_id = '$seen'";      
        $set_stmt2 = $db->prepare($get_sql2) ;
        $set_stmt2->execute();                       

        $get_value2 = $set_stmt2->fetchAll(PDO::FETCH_OBJ);   
//        $join = '';
        if(!empty($get_value2)){
            $join = " INNER JOIN form_encounter f ON f.pid = p.pid ";
            $query  .= " AND f.".$get_value2[0]->notes;
        }   
        
        $sqlGetMyPatientsByName="SELECT DISTINCT p.pid,title,fname,lname, DATE_FORMAT(DOB,'%m-%d-%Y') AS DOB,DATE_FORMAT(FROM_DAYS(DATEDIFF(CURDATE(),DOB)), '%Y')+0 AS age,if (sex = 'Female' ,'F','M' ) as sex,street,city,state,country_code,postal_code,phone_home,phone_biz,phone_contact,phone_cell,contact_relationship as emergency_contact, phone_contact as emergency_phone
                                FROM patient_data p
                                $join
                                WHERE fname like '%$patientName%'
                                OR mname like '%$patientName%'
                                OR lname like '%$patientName%'
				OR CONCAT(fname,' ',lname) like '%$patientName%'
                                OR CONCAT(fname,' ',mname) like '%$patientName%'
                                AND providerID='$loginProviderId' 
                                $facility
                                $query 
                                ORDER BY fname,lname limit 100";

        $stmt = $db->prepare($sqlGetMyPatientsByName);
        $stmt->execute();
        $resGetMyPatientsByName = $stmt->fetchAll(PDO::FETCH_OBJ);
        
        if($resGetMyPatientsByName)
        {
            $patientres = json_encode($resGetMyPatientsByName); 
            echo $patientresult = GibberishAES::enc($patientres, $key);
        }
        else
        {   // Patient Not found
            $patientres = '[{"id":"0","msg":"Patient not found"}]';
            echo $patientresult = GibberishAES::enc($patientres, $key);
        }
                
    }
    catch(PDOException $e) 
    {
        $patientres = '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo $patientresult = GibberishAES::enc($patientres, $key);
    }    
    
}
function getAddressbookOrgDropdown(){
        $apikey = 'rotcoderaclla';
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
	$sql = "SELECT id,fname,lname,organization,
                    CASE
                    WHEN abook_type != '' THEN (
                                SELECT title
                                FROM list_options
                                WHERE list_id = 'abook_type'
                                AND option_id = abook_type
                                )
                    WHEN abook_type = '' THEN ''
                    END AS type 
                    FROM users";      
        
        try 
        {
            $db = getConnection();
            $db->query( "SET NAMES utf8"); 
            $stmt = $db->prepare($sql) ;
            $stmt->execute();                       
             
            $patientsreminders2 = $stmt->fetchAll(PDO::FETCH_OBJ);       
            for($i=0; $i<count($patientsreminders2); $i++){
                    $name = '';
                    if(!empty($patientsreminders2[$i]-> fname)  || !empty($patientsreminders2[$i]-> lname)) 
                        $name = "Name: ";
                    if(!empty($patientsreminders2[$i]-> fname) )
                        $name .= $patientsreminders2[$i]-> fname." ";
                    if(!empty($patientsreminders2[$i]-> lname))
                        $name .= $patientsreminders2[$i]-> lname.". ";
                    else if(!empty($patientsreminders2[$i]-> fname) )
                        $name .= '. ';
                    if(!empty($patientsreminders2[$i]-> organization) )
                        $name .= "Org: ".$patientsreminders2[$i]-> organization.". ";
                    if(!empty($patientsreminders2[$i]-> type) )
                        $name .= "Type: ".$patientsreminders2[$i]-> type.". ";
                    $patientsreminders[$i]['id'] = $patientsreminders2[$i]-> id;
                    $patientsreminders[$i]['name'] = $name;

            }
//            echo "<pre>"; print_r($patientsreminders); echo "</pre>";
            if($patientsreminders)
            {
                $patientres = json_encode($patientsreminders); 
                echo $patientresult = GibberishAES::enc($patientres, $apikey);
            }
            else
            {
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
function getQOC($encounterid,$formid,$uid){
    try 
    {
        $db = getConnection();
        $apikey = 'rotcoderaclla';
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        $patientsreminder = array();
        $checkform = 0;
        $patientsreminder = getLayoutGroupSpecificFunction($formid,'LBF2','Quality Of Care',$checkform);
        $dataarray = $patientsreminder;
//        echo "<pre>"; print_r($dataarray); echo "</pre>";
        $newdemo=encode_demo($dataarray);  
        $newdemo2['QOC'] = check_data_available($newdemo);
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
function getLayoutGroupSpecificFunction($formid, $form_name,$group,$checkform){
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
                                END as isRequired,seq,max_length,description FROM layout_options WHERE form_id = '$form_name' AND group_name LIKE '%$group' order by group_name, seq";
    $db->query( "SET NAMES utf8");
    $stmt_layout2 = $db->prepare($get_layout2);
    $stmt_layout2->execute();                       
    $layout_fields2 = $stmt_layout2->fetchAll(PDO::FETCH_OBJ);
    if(!empty($layout_fields2)){
        $patientsreminder[substr($layout_fields2[0]->group_name,1)]['form_id'] = $formid;
        for($i=0; $i< count($layout_fields2); $i++){
            if(!empty($layout_fields2[$i]->field_type)){
                $sql = "select field_value from lbf_data where field_id  = '". $layout_fields2[$i]->field_id."' and form_id= '$formid'";   
                $db->query( "SET NAMES utf8");
                $stmt = $db->prepare($sql);
                $stmt->execute();                       

                $patientsreminders = $stmt->fetchAll(PDO::FETCH_OBJ);   
                $pvalue = isset($patientsreminders[0]->field_value)? $patientsreminders[0]->field_value: '';

                $patientsreminder[substr($layout_fields2[$i]->group_name,1)][$i]['group_name'] = substr($layout_fields2[$i]->group_name,1);
               
                if($checkform == 1)
                    $layout_fields2[$i]->field_id = str_replace(str_replace(" ", "_",$group),"dictation",$layout_fields2[$i]->field_id);
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
    }

    
    return $patientsreminder;
}
/* ============ Hema methods end here ================== */

/* ============ bhavya methods start here ================== */

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

function getFeesheetCodes($encounterid){
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
        $medical_problems_array = array();
        $sql = "SELECT DISTINCT l.id, l.title AS Title, l.diagnosis AS Codes, if(SUBSTRING(l.diagnosis,1,4)='ICD9', (select long_desc from `icd9_dx_code` where l.diagnosis = CONCAT( 'ICD9:', formatted_dx_code ) and active = 1), (select long_desc from `icd10_dx_order_code` where l.diagnosis = CONCAT( 'ICD10:', formatted_dx_code ) and active = 1)) as Description
                FROM lists AS l
                LEFT JOIN issue_encounter AS ie ON ie.list_id = l.id
                AND ie.encounter =$encounterid

                WHERE l.type =  'medical_problem'
                AND l.pid =$pid1
                AND (( l.begdate IS NULL) OR ( l.begdate IS NOT NULL AND l.begdate <= NOW( ) ) )
                AND (( l.enddate IS NULL) OR ( l.enddate IS NOT NULL AND l.enddate >= NOW( ) ) ) AND activity = 1
                ORDER BY ie.encounter DESC , l.id" ;      
        $stmt2 = $db->prepare($sql) ;
        $stmt2->execute(); 
        $medical_problems_array = $stmt2->fetchAll(PDO::FETCH_OBJ);   
        $medical_problems_to_check= '';
        for($i=0; $i< count($medical_problems_array); $i++){
            $medical_problems_array[$i]->activity = 1;
            $medical_problems_to_check .= "'".str_replace("ICD9:","",str_replace("ICD10:","",$medical_problems_array[$i]->Codes))."',";
        }
        $medical_problems_to_check = rtrim($medical_problems_to_check,",");
        if(!empty($medical_problems_to_check))
            $medical_problem_query_string = "AND code NOT IN ($medical_problems_to_check)";
        else
            $medical_problem_query_string = '';
        $medical_problems_icds = array();
        $sql_icds = "SELECT b.id, CONCAT(b.code_type,'|',b.code) as Codes,  b.code_text as Title
            FROM billing b 
            WHERE b.encounter = $encounterid   and code_type IN ('ICD10', 'ICD9') $medical_problem_query_string and b.activity = 1 order by b.date desc ";
        $stmt_icds = $db->prepare($sql_icds) ;
        $stmt_icds->execute(); 
        $medical_problems_icds = $stmt_icds->fetchAll(PDO::FETCH_OBJ);  
        for($i=0; $i< count($medical_problems_icds); $i++){
            $medical_problems_icds[$i]->activity = 0;
        }
//        echo "<pre>"; print_r($medical_problems2); echo "</pre>";
        $medical_problems = array_merge($medical_problems_array, $medical_problems_icds);;
        $selectquery = "SELECT b.id, b.code,b.modifier, b.billed, justify, b.notecodes, b.code_text,f.provider_id as rendering_providerid, (SELECT  CONCAT( fname,  ' ', lname ) FROM users where id = f.provider_id)  AS rendering_ProviderName
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

                $icd_code[] = explode(":",str_replace(",","",$selected_feesheet[$i]->justify));
//                foreach($medical_problems as $element):
//                    $element->Codes = str_replace("|", ":",$element->Codes);
//                    if($element->Codes == str_replace('|', ':', $icd_code[0])){
//                        $element->primary = 1;
//                    }else{
//                        $element->primary = 0;
//                    }
//                    if(in_array($element->Codes, str_replace('|', ':', $icd_code)) ){
//                        $element->justify = 1;
//                    }else{
//                        $element->justify = 0;
//                    }
//                    $element->Codes = str_replace(':', '|', $element->Codes);
//                endforeach;
                $get_is_inlist = "SELECT notes
                            FROM  `list_options` 
                            WHERE  `list_id` =  'AllCareCPTvsICD'
                            AND FIND_IN_SET(  '".$selected_feesheet[$i]->code."', REPLACE( title, SPACE( 1 ) ,  '' ) ) >0";
                $set_is_inlist = $db->prepare($get_is_inlist) ; 
                $set_is_inlist->execute(); 
                $set_list  = $set_is_inlist->fetchAll(PDO::FETCH_OBJ);
                if(!empty($set_list)){
                    $selected_feesheet[$i]->isPredefined = 1;
                    $selected_feesheet[$i]->icds = '';
                    $explodenotes = explode(",",$set_list[0]->notes);
                    for($ic = 0 ; $ic< count($explodenotes); $ic++){
                        $selected_feesheet[$i]->icds .= "ICD10|".strtoupper(trim($explodenotes[$ic])).",";
                    }
                }else{
                    $selected_feesheet[$i]->isPredefined = 0;
                    $selected_feesheet[$i]->icds = '';
                }
            }
            $icd_unique_array = array();
            for($i=0; $i< count($icd_code); $i++){
                foreach($icd_code[$i] as $ikey => $ivalue){
                    $icd_unique_array[] = $ivalue;
                }
            }
            $icd_unique_array = array_values(array_unique($icd_unique_array));
            foreach($medical_problems as $element):
                $element->Codes = str_replace(":", "|",$element->Codes);
                if($element->Codes == $icd_unique_array[0]){
                    $element->primary = 1;
                }else{
                    $element->primary = 0;
                }
                if(in_array($element->Codes, $icd_unique_array) ){
                    $element->justify = 1;
                }else{
                    $element->justify = 0;
                }
                $element->Codes = str_replace(':', '|', $element->Codes);
            endforeach;
            if(empty($rendering_provider)){
                $get_providerName = "SELECT  CONCAT( u.fname,  ' ', u.lname ) AS rendering_ProviderName, f.rendering_provider AS rendering_providerid

                FROM form_encounter f 

                inner join users u on u.id =  f.rendering_provider 

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
        }else{
            if(!empty($medical_problems)){
                foreach($medical_problems as $element):
                    $element->primary = 0;
                    $element->justify = 0;
                endforeach;
            }
        }
        if(empty($selected_feesheet['rendering_providerid'] )){
            $get_providerName = "SELECT  CONCAT( u.fname,  ' ', u.lname ) AS rendering_ProviderName, f.rendering_provider AS rendering_providerid

                FROM form_encounter f 

                inner join users u on u.id =  f.rendering_provider 

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
        
        if($billed == 0){
            // dropdowns
            $getcptlist = "select option_id,title from list_options where list_id  = 'Allcare_Visit_Code_Group_List' order by seq";
            $cptstmt = $db->prepare($getcptlist) ; 
            $cptstmt->execute(); 
            $setcptlist = $cptstmt->fetchAll(PDO::FETCH_OBJ); 
            if(!empty($setcptlist)){
                for($i=0; $i< count($setcptlist); $i++){
                    $explodedstring = array();
                    $explodedstring = explode(",",$setcptlist[$i]->title);
                    $stringarray = count($explodedstring);
    //                $cptkey1 = str_replace('/',"",$setcptlist[$i]->option_id);
                    for($j=0; $j< $stringarray; $j++){
                        $getcptvalues = "SELECT fs_codes FROM fee_sheet_options WHERE fs_category ='$explodedstring[$j]'";
                        $cptstmt2 = $db->prepare($getcptvalues) ; 
                        $cptstmt2->execute(); 
                        $setcptlist2 = $cptstmt2->fetchAll(PDO::FETCH_OBJ); 
                        if(!empty($setcptlist2)){
                            for($j=0; $j< count($setcptlist2); $j++){
                                foreach($setcptlist2[$j] as $cpt1 => $cpt2){
                                    $explodedcpt = array();
                                    $cptval = $cpt2;
                                    if(strpos($cptval,"~") !== false){
                                        $explodedcpt = explode("~",str_replace("|","",str_replace("CPT4|","",$cptval)));
                                    }else{
                                        $explodedcpt[0] = str_replace("|","",str_replace("CPT4","",$cptval));
                                    }
                                    for($k=0; $k< count($explodedcpt); $k++){
                                        $getcodename = "SELECT code_text FROM codes WHERE code = '$explodedcpt[$k]'";
                                        $cptstmt3 = $db->prepare($getcodename) ; 
                                        $cptstmt3->execute(); 
                                        $setcptlist3 = $cptstmt3->fetchAll(PDO::FETCH_OBJ); 
                                        if(!empty($setcptlist3))
                                            $cptfields[$setcptlist[$i]->option_id][$explodedcpt[$k]] = $setcptlist3[0]->code_text;
                                    }
                                }
                            }
                        }
                        $cptcodesdropdown = $cptfields;
                    }
                }
            }

            $query0 = "SELECT fo.fs_option, vc.code_options,fo.fs_codes FROM fee_sheet_options fo INNER JOIN tbl_allcare_vistcat_codegrp vc ON vc.code_groups = fo.fs_category  WHERE `facility` = $facility_id AND `visit_category` = $visit_category AND vc.code_options REGEXP (fo.fs_option)";
            $stmt0 = $db->prepare($query0) ; 
            $stmt0->execute(); 
            $fscodes = $stmt0->fetchAll(PDO::FETCH_OBJ); 
            $cpt4codes = array();
            if(!empty($fscodes)){
                for($j= 0; $j< count($fscodes); $j++){
                    foreach($fscodes[$j] as $fkey => $element):
                        $codes = $element;
                        $explodecodesarray = explode('~',str_replace("CPT4","",str_replace("|","",$codes) ));
                        for($i=0; $i< count($explodecodesarray); $i++){
                            $query = "SELECT code, code_text as Description FROM codes WHERE code = '".$explodecodesarray[$i]."'";
                            $stmt = $db->prepare($query) ; 
                            $stmt->execute(); 
                            $cpt4codesarray = $stmt->fetchAll(PDO::FETCH_OBJ); 
                            if(!empty($cpt4codesarray))
                                $cpt4codes[] = $cpt4codesarray[0];
                        }
                    endforeach;
                }
            }
            $query1 = "select dx_code,formatted_dx_code, short_desc from icd9_dx_code where active = 1 limit 150";
            $stmt1 = $db->prepare($query1) ; 
            $stmt1->execute(); 
            $icd9codes = $stmt1->fetchAll(PDO::FETCH_OBJ); 
            
            $allcptcodesdropdown2 = 'SELECT code_text,code_text_short,code,fee FROM codes WHERE active = 1 AND code_type=1';
            $db->query( "SET NAMES utf8");
            $stmt_cpts = $db->prepare($allcptcodesdropdown2) ;
            $stmt_cpts->execute();                       
            $allcptcodesdropdown = $stmt_cpts->fetchAll(PDO::FETCH_OBJ);
            
            $hospice = "SELECT  * FROM patient_data WHERE patient_in_hospice = 'YES' and pid = $pid1" ; 
            $selecthospice = $db->prepare($hospice) ; 
            $selecthospice->execute(); 
            $sethospice = $selecthospice->fetchAll(PDO::FETCH_OBJ); 
            if(!empty($sethospice))
                $patient_in_hospice = 1;
            else
                $patient_in_hospice = 0;
            
            $provider = "SELECT id, CONCAT(fname,' ',lname) as name FROM users WHERE " .
                "( authorized = 1 OR info LIKE '%provider%' ) AND username != '' " .
                "AND active = 1 AND ( info IS NULL OR info NOT LIKE '%Inactive%' ) " .
                "ORDER BY lname, fname" ; 
            $selectprovider = $db->prepare($provider) ; 
            $selectprovider->execute(); 
            $providerlist = $selectprovider->fetchAll(PDO::FETCH_OBJ); 
            $selected_feesheet['provider_list'] = $providerlist;
            
            $getallcptsearch = "SELECT title,notes FROM `list_options`  
                            WHERE `list_id` =  'AllCareCPTvsICD'";
            $getallcptdata = $db->prepare($getallcptsearch) ; 
            $getallcptdata->execute(); 
            $getallcpticd = $getallcptdata->fetchAll(PDO::FETCH_OBJ); 
            if(!empty($getallcpticd)){
                for($inew = 0 ; $inew < count($getallcpticd); $inew++){
                    $explodenew = '';
                    $explodenew1[$inew]['title'] = $getallcpticd[$inew]->title;
                    $explodenotes = explode(",",$getallcpticd[$inew]->notes);
                    for($ic = 0 ; $ic< count($explodenotes); $ic++){
                        $explodenew .= "ICD10|".strtoupper(trim($explodenotes[$ic])).",";
                    }
                    $explodenew1[$inew]['notes'] = $explodenew;
                }
                $new['predefinedcodes'] = $explodenew1;
            }else{
                $new['predefinedcodes'] = array();
            }
        }else{
            $cptcodesdropdown = array();
            $allcptcodesdropdown = array();
            $patient_in_hospice = 0;
            $cpt4codes = array();
            $icd9codes = array();
            $selected_feesheet['provider_list'] = array();
        }
            
         
        $new['CPTCodes'] = $cptcodesdropdown;
        $new['AllCPTCodes'] = $allcptcodesdropdown;
        $new['FeeSheetData'] = $selected_feesheet;
        //$new['provider_list'] = $providerlist;
        $new['CPT4']=$cpt4codes;
        $new['ICD9']=$icd9codes;
        $new['medical_problems']= $medical_problems ;
        $new['billed']= $billed ;
        $new['patient_in_hospice'] = $patient_in_hospice;
        //$new['Provider']=$provider;
//        echo "<pre>"; print_r($new); echo "</pre>";
           
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
        $patientquery = "SELECT pid FROM form_encounter WHERE encounter = $encounter";
        $patientquerystmt = $db->prepare($patientquery) ;
        $patientquerystmt->execute();
        $pidval = $patientquerystmt->fetchAll(PDO::FETCH_OBJ);
        $pid = $pidval[0]->pid;
//        if($type=='allergy')
//        {
            /*$query = "SELECT DISTINCT li.id, li.pid,
                    CASE 
                    WHEN li.enddate IS NULL 
                    THEN  'Active'
                    ELSE  'Inactive'
                    END AS Status,li.title AS Title, li.begdate AS Begin_date, li.enddate AS End_date, li.diagnosis AS Codes, i.long_desc AS Description, 
                    li.reaction as reaction,(select title from list_options where list_id='occurrence' and option_id = li.occurrence) AS Occurrence, li.referredby AS Referred_By,ie.encounter, li.comments,
                    (SELECT COUNT( list_id ) 
                    FROM issue_encounter
                    WHERE list_id = li.id
                    ) AS enc
                FROM  `icd9_dx_code` i
                RIGHT JOIN lists li ON li.diagnosis = CONCAT(  'ICD9:', i.formatted_dx_code ) 
                INNER JOIN issue_encounter ie ON ie.list_id = li.id
                INNER JOIN form_encounter fe ON fe.encounter = ie.encounter
                WHERE ie.encounter = $encounter
                AND li.type ='$type' ORDER BY - ISNULL( li.enddate ) , li.begdate DESC";*/
            $query = "SELECT DISTINCT li.id, li.pid,
                            CASE 
                            WHEN li.enddate IS NULL 
                            THEN  'Active'
                            ELSE  'Inactive'
                            END AS Status,outcome,destination,li.title AS Title, li.begdate AS Begin_date, li.enddate AS End_date, li.diagnosis AS Codes, i.long_desc AS Description, 
                            li.reaction as reaction,li.occurrence AS Occurrence, li.referredby AS Referred_By,ie.encounter, li.comments,
                            (SELECT COUNT( list_id ) 
                            FROM issue_encounter
                            WHERE list_id = li.id
                            ) AS enc
                        FROM  `icd9_dx_code` i
                        RIGHT JOIN lists li ON li.diagnosis = CONCAT(  'ICD9:', i.formatted_dx_code ) 
                        INNER JOIN issue_encounter ie ON ie.list_id = li.id
                        INNER JOIN form_encounter fe ON fe.encounter = ie.encounter
                        WHERE li.type ='$type'  AND ie.encounter = $encounter ";
            $db->query("SET SQL_BIG_SELECTS=1"); 
            $db->query( "SET NAMES utf8");
            $stmt = $db->prepare($query) ; 
            $stmt->execute(); 
            $issues = $stmt->fetchAll(PDO::FETCH_OBJ); 
//        }
//        else
//        {
//            $query = "SELECT DISTINCT li.id, li.pid,
//                    CASE 
//                    WHEN li.enddate IS NULL 
//                    THEN  'Active'
//                    ELSE  'Inactive'
//                    END AS Status, li.title AS Title, li.begdate AS Begin_date, li.enddate AS End_date, li.diagnosis AS Codes, i.long_desc AS Description, li.reaction as reaction,
//                    (select title from list_options where list_id='occurrence' and option_id = li.occurrence) AS Occurrence, li.referredby AS Referred_By,ie.encounter, li.comments,
//                    (SELECT COUNT( list_id ) 
//                    FROM issue_encounter
//                    WHERE list_id = li.id
//                    ) AS enc
//                FROM  `icd9_dx_code` i
//                RIGHT JOIN lists li ON li.diagnosis = CONCAT(  'ICD9:', i.formatted_dx_code ) 
//                INNER JOIN issue_encounter ie ON ie.list_id = li.id
//                INNER JOIN form_encounter fe ON fe.encounter = ie.encounter
//                WHERE ie.encounter =$encounter
//                AND li.type ='$type' ORDER BY - ISNULL( li.enddate ) , li.begdate DESC";
//            $db->query("SET SQL_BIG_SELECTS=1"); 
//            $db->query( "SET NAMES utf8");
//            $stmt = $db->prepare($query) ; 
//            $stmt->execute(); 
//            $issues = $stmt->fetchAll(PDO::FETCH_OBJ); 
//        }
        if(!empty($issues)){
            for($i=0 ; $i< count($issues); $i++){
                foreach($issues[$i] as $dkey => $dvalue){
                    if($dkey == 'Codes'){
                        // multiple icd codes description
                        $icdcodesarray = explode(";",$dvalue);
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
                            $db->query( "SET NAMES utf8");
                            $res_den = $db->prepare($icd_description_sql);
                            $res_den->execute();
                            $icd_description = $res_den->fetchAll(PDO::FETCH_OBJ);   
                            if(!empty($icd_description)){
                                $icd_description_value .=  "( ".$icdcodesarray[$ic] ." ".$icd_description[0]->long_desc.")<br />";
                            }
                        }
                        $issues[$i]->Description = $icd_description_value;
//                                echo "<b>". ucfirst(str_replace('_',' ',$dkey.":"))."</b>".$icd_description_value;
                    }

//                      $record['Allergies']=$field_den;
                }
            }
        }
//        $pid = isset($issues[0]->pid)?$issues[0]->pid : 0;
        $get_layout = "SELECT form_id FROM forms WHERE encounter = $encounter AND formdir= 'LBF2' AND deleted = 0 AND pid = $pid  order by id desc";
        $db->query( "SET NAMES utf8");
        $stmt_layout = $db->prepare($get_layout) ;
        $stmt_layout->execute();                       
        $getform_id = $stmt_layout->fetchAll(PDO::FETCH_OBJ); 
        if(!empty($getform_id))
            $setform_id =  $getform_id[0]->form_id;
        else
            $setform_id = 0;

        if($type == 'allergy')
            $type1 = 'Allergies';
        else if($type == 'surgery')
            $type1 = 'Surgeries';
        else if($type == 'medical_problem')
            $type1 = 'Medical Problems';
        else if($type == 'dental')
            $type1 = 'Dental Problems';
        else
            $type1 = $type;
        
        $patientsreminder = array();
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
                                END as isRequired,description,max_length FROM layout_options WHERE form_id = 'LBF2' AND group_name LIKE '%$type1' order by group_name, seq";
            $db->query( "SET NAMES utf8");
            $stmt_layout2 = $db->prepare($get_layout2) ;
            $stmt_layout2->execute();                       
            $layout_fields2 = $stmt_layout2->fetchAll(PDO::FETCH_OBJ);
            if(!empty($layout_fields2)){
                $patientsreminder[substr($layout_fields2[0]->group_name,1)]['form_id'] = $setform_id;
                for($i=0; $i< count($layout_fields2); $i++){
                    $sql = "select field_value from lbf_data where field_id  = '". $layout_fields2[$i]->field_id."' and form_id= '$setform_id'";   
                    $db->query( "SET NAMES utf8");
                    $stmt = $db->prepare($sql) ;
                    $stmt->execute();                       

                    $patientsreminders = $stmt->fetchAll(PDO::FETCH_OBJ);   
//                    $pvalue = isset($patientsreminders[0]->field_value)? $patientsreminders[0]->field_value: '';
                    // if formid = 0 then audto text
                    $autotextvalue = '';
                    $sql_autotext = "select notes from list_options where option_id  = '". $layout_fields2[$i]->field_id."' and list_id = 'AllCareEncFormsAutoText'";   
                    $db->query( "SET NAMES utf8");
                    $stmt_autotext = $db->prepare($sql_autotext) ;
                    $stmt_autotext->execute();                       

                    $autotext = $stmt_autotext->fetchAll(PDO::FETCH_OBJ);   
                    $pvalue = '';
                    $autotextvalue = isset($autotext[0]->notes)? $autotext[0]->notes: '';
                    if(!empty($patientsreminders)){
                        if($patientsreminders[0]->field_value !== '')
                        $pvalue = isset($patientsreminders[0]->field_value)? $patientsreminders[0]->field_value: $autotextvalue;
                    }else{
                        $pvalue = $autotextvalue;
                    }

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
                            "pid = $pid AND type = 'allergy' AND enddate IS NULL " .
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
                        $sql3 = "SELECT fe.date, DATE_FORMAT( fe.date, '%Y-%m-%d' ) as date_title FROM `form_encounter` fe
                            INNER JOIN forms f ON  fe.id = f.form_id AND fe.encounter = f.encounter AND fe.pid = f.pid
                            WHERE fe.pid = $pid ORDER BY date desc";
                        $db->query( "SET NAMES utf8"); 
                        $stmt3 = $db->prepare($sql3) ;
                        $stmt3->execute();                       
                        $getListData = $stmt3->fetchAll(PDO::FETCH_OBJ);  
                        if(!empty($getListData)):
                            $data2 = array();
                            $j=0;
                            foreach($getListData as $list){
                                $data2[$j]['option_id'] = $list->date;
                                $data2[$j]['option_value'] = $list->date_title;
                                $j++;
                            }
                        else:
                            $data2 = '';
                        endif;
                        $patientsreminder[substr($layout_fields2[$i]->group_name,1)][$i]['options']  = $data2;
                        $patientsreminder[substr($layout_fields2[$i]->group_name,1)][$i]['value'] = $pvalue;
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

        $issues[$layout_fields2[0]->form_id] = $patientsreminder;

        $newdemo4=encode_demo($issues);  
        $newdemo2['Issues'] = check_data_available($newdemo4);

        if($newdemo2)
        {
            $issuesres = json_encode($newdemo2);
            echo $newdemoresult = GibberishAES::enc($issuesres, $apikey);
//            echo $decrypted_secret_string = GibberishAES::dec($newdemoresult, $apikey);
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
            $query = "select i.id as id,i.cvx_code as 'Immunization_CVX_Code', c.code_text_short as CVX_Text, i.administered_date as 'Date_and_Time_Administered',i.amount_administered as Amount_Administered,(select title from list_options where list_id='drug_units' and option_id =i.amount_administered_unit) as amount_administered_unit ,i.expiration_date as Immunization_Expiration_Date,  i.manufacturer as Immunization_Manufacturer,i.lot_number as Immunization_Lot_Number,(select concat(lname,' ',fname) from users where id= i.administered_by) as Name_and_Title_of_Immunization_Administrator,
             i.education_date as Date_Immunization_Information_Statements_Given	,i.vis_date as Date_of_VIS_Statement,(SELECT title FROM list_options WHERE list_id='drug_route' AND option_id = i.route) as route,i.administration_site,i.note as notes,i.immunization_id as immunization_id".
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
//        $appres = GibberishAES::dec("U2FsdGVkX18IfOfm9c5yXR2hmQ91uQ4qklQ1egiTG02efeHJRyCh/lUM2OM3GMKYPD7kRIHQUq00FbE6RkpKNWLEkwzukcaKeMrvGhjbbIgF5EPT1FYM4fVF6/B8JSx1ktDOOsdR/3z5ChWvdooI1D9wwY2o2Q0ea2rM7E5zzguDr88TAkmClBWNAHyU5b2v9t156s1TBsAz2TQ2uVIgIxn+kdhHbCXG33dg6aiYODxE8Zwu5fkIKxYq7sKzMYkY7ramPBV8DGocjQH6Pcsj47RGV913GkSDh7VHeq20hwR3XFHTZnJ7XaJ1KIvYtJD3LBLmUaNdbD2IOqKSIKo6O2jj1C8okC3TKXYNnqILQAm1zriQ2Yw/SAp76AyUKSV7jQNN5CXG1wjGb9gOOBj/t8uX7prJuGRHI0cAXtcx1YtCk5/+xXn3YEnNBB9/aOqjpT8dcY+/SUulMxkeq+kRXnpKNZ3fHjCfuy4O53iK/qZcdtJNoimylcJW8QqZKUl706pAiAinXMWOG8H8bCZLIS2swjbcniMM8FYs67GOnLkd49UFZnAeZcAnJbNdmrUvm+Iv/cRwAYu5uJs9FypBk2YHiatdnmv5Q8Y+Ua1ap08DdzGvR94sc+yguAtyUdd/dk169LQYdIMtpMNx9P1ZwiDki154LFhcZDB+Aa4tyNFhaxVFrKb9JRhwjJFRnnsaPZYghzE9MPZRnS1yj785R0Cyd7SjsYQem8WETjzh7sAiRRcpthpoAw7g/kNXu7ZFwY6yG0ScE4iOUJf9WU8NGsId7mLcAi1Y1dUZldGlz1Co3OiFY9QUpW1Ed2qcsr2TECEQ0UfE3nNQ+1ZggBVcbydo0UEwLEWBVoh/k6MZUdQB+rPZl+b/Qn7Fe3mNeGrcCwbBrRWyem363HkJ542HJsa5ss+RN+Hws2Tt1eUZXrtuAyS7yEPSFyVaygZwno+xeAhfv+PCOA3AYa2rP9pmIwCrrzTRzHZAEo7ctKgoRgtogA3nQQQ5rKVzSnmlxApv55GQs18dtHLWJJlFmDcD3Mux6mXE3U/bqrPAllWUx1Gh/ZrcrRhm5gPxUEiEt1kobqdm//tFppn1UG27ST/cQzWPAWhcVxWztNfScrKc2rIYCKvqsMUJldQatPUQCJ0npaV9eAhENbqal0vToEc+QfIfStq3D2J0yRkEBBHY+f83AGFATQK30agAqiq0ciHp0Yhv12/+TAWTQI5JNnRGQN8rXhIa3+0IaDkSm+Ev7f8UMM8GGecd3acGCT2KLTcPp5fPTEV8T4Vx0jX44LvJmurfIkKdY3THb/ORTu32tOjIakF2P+cdSNhWVCiwu+pWqbMNBU4P06RDu0xf2mm3rZ1juIO+7XxJMg9akyfkNe0=", $apikey);
        $insertArray = json_decode($appres,TRUE);
       
//        echo "<pre>"; print_r($insertArray); echo "</pre>"; 

        
        $encounter                  = $insertArray['encounter'];
        $pid                        = $insertArray['pid'];
        $user                       = $insertArray['providerid'];
        $provider_id                = $insertArray['providerid'];
        $username2                  = getUserName($user);
        $username                   = isset($username2[0]['username'])? $username2[0]['username'] : ''; 
        $codearray                  = $insertArray['ModifiersArray'];
        $predefined_codes_array     = $insertArray['Predefined'];
        $icds                       = $insertArray['icds'];
        $justify                    = $insertArray['justify'];
        $activecptcodes             = $insertArray['ActiveCPTCodes'];
        
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
//        $modifier                   = '';
        $target                     = '';

        if(!empty($justify))
            $justify .= ":";
        
        $data = 0;
        // medical problem cpts
        for($i=0; $i<count($codearray)-1; $i++){
            if(!empty($codearray)){ 
                $modifier = $codearray[$i]['modifiervalue'];
                $cptid = $codearray[$i]['id']; 
                if($cptid == 0){
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
                        . " VALUES ( NOW(),'CPT4','".$codearray[$i]['code']."','$pid','$provider_id', '$user','$groupname', ' $authorized',  ' $encounter', '$string', '$billed', '$activity' ,'$payer_id','$bill_process','$bill_date','$process_date','$process_file','$modifier','','','$justify','$target')"; 

                    $sqlstmt = $db->prepare($sql) ;
                    if($sqlstmt->execute()){
                        $data = 1;
                        insertMobileLog('insert',$username,$sql,$pid,$encounter,"New Billing CPT ".$codearray[$i]['code'] ." Screen",1);
                    }else {
                        $data = 0;
                        insertMobileLog('insert',$username,$sql,$pid,$encounter,"New Billing CPT ".$codearray[$i]['code'] ." Screen - Failed",0);
                    }
                    
                }else{
                    $sql = "UPDATE billing SET modifier = '$modifier' WHERE id = $cptid"; 

                    $sqlstmt = $db->prepare($sql) ;
                    if($sqlstmt->execute()){
                        $data = 1;
                        insertMobileLog('update',$username,$sql,$pid,$encounter,"Update Billing CPT Modifier Screen",1);
                    }else {
                        $data = 0;
                        insertMobileLog('update',$username,$sql,$pid,$encounter,"Update Billing CPT Modifier Screen - Failed",0);
                    }
                }
                
            }
        }  
        $sql1 = "UPDATE form_encounter SET provider_id ='$user' WHERE pid ='$pid' AND encounter ='$encounter'";
        $sqlstmt1 = $db->prepare($sql1) ;
        if($sqlstmt1->execute()){
            $data = 1;
            insertMobileLog('update',$username,$sql1,$pid,$encounter,"Form Encounter Provider name Update Screen",1);
        }else {
            $data = 0;
            insertMobileLog('update',$username,$sql1,$pid,$encounter,"Form Encounter Provider name Update Screen - Failed",0);
        }

        // predefined cpts
        $modifier = '';
        for($i=0; $i<count($predefined_codes_array); $i++){
            if(!empty($predefined_codes_array)){ 
                $modifier = $predefined_codes_array[$i]['Modifier'];
                $cptid = $predefined_codes_array[$i]['id']; 
                $justify1 = $predefined_codes_array[$i]['ICD']; 
                $justifyarray = explode(":",$justify1);
                $justify1 = '';
                for($k = 0; $k< count($justifyarray ) ; $k++){
                    $justify1 .= "ICD10|".trim($justifyarray[$k]).":";
                }
                if(!empty($justify1))
                    $justify1 .= ":";
                if($cptid == 0){
                    if($predefined_codes_array[$i]['CPT']!=''){
                        $descr1="select code_text from codes where code='".$predefined_codes_array[$i]['CPT']."' AND code_type=1";
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
                    if(!empty($predefined_codes_array[$i]['CPT'])){
                        $sql = "INSERT INTO billing (date, code_type, code, pid, provider_id, user,  groupname, authorized, encounter,  code_text, billed, activity,payer_id,bill_process,bill_date,process_date,process_file,modifier,units,fee,justify,target)"
                           . " VALUES ( NOW(),'CPT4','".$predefined_codes_array[$i]['CPT']."','$pid','$provider_id', '$user','$groupname', ' $authorized',  ' $encounter', '$string', '$billed', '$activity' ,'$payer_id','$bill_process','$bill_date','$process_date','$process_file','$modifier','','','$justify1','$target')"; 

                       $sqlstmt = $db->prepare($sql) ;
                       if($sqlstmt->execute()){
                           $data = 1;
                           insertMobileLog('insert',$username,$sql,$pid,$encounter,"New Billing CPT ".$predefined_codes_array[$i]['CPT'] ." Screen",1);
                       }else {
                           $data = 0;
                           insertMobileLog('insert',$username,$sql,$pid,$encounter,"New Billing CPT ".$predefined_codes_array[$i]['CPT']." Screen - Failed",0);
                       }
                    }
                }else{
                    $sql = "UPDATE billing SET modifier = '$modifier' , justify ='".addslashes($justify)."' WHERE id = $cptid"; 

                    $sqlstmt = $db->prepare($sql) ;
                    if($sqlstmt->execute()){
                        $data = 1;
                        insertMobileLog('update',$username,$sql,$pid,$encounter,"Update Billing CPT Modifier Screen",1);
                    }else {
                        $data = 0;
                        insertMobileLog('update',$username,$sql,$pid,$encounter,"Update Billing CPT Modifier Screen - Failed",0);
                    }
                }
                 // justify of Predefined CPTS
                $justify_array = array();
                if(!empty($justify1))
                    $justify_array = explode("|",str_replace(",","|",str_replace("|",":",str_replace(":",",",$justify1))));
                $justify_array = array_filter($justify_array);
                if(count($justify_array)>0){
                    for($i=0; $i<count($justify_array); $i++){
                        $icd_code2 = '';
                        $icd_code2 = $justify_array[$i];
                        $title = '';
                        $title1='';$checkstring = $table = '';
                        if(strpos($icd_code2, 'ICD9',0)!== false){
                            $checkstring = "ICD9:";
                            $checkstring1 = 'ICD9';
                            $table = 'icd9_dx_code';
                        }
                        if(strpos($icd_code2, 'ICD10',0)!== false || empty($table)){
                            $checkstring = "ICD10:";
                            $table = 'icd10_dx_order_code';
                            $checkstring1 = 'ICD10';
                        }
                        $exploded_code = str_replace($checkstring, "", $icd_code2);
                        $title3='';
                        if(!empty($exploded_code)){
                           $descr2="SELECT long_desc FROM $table WHERE formatted_dx_code ='$exploded_code' and active= 1"; 
                           $sqlstmte2 = $db->prepare($descr2) ;
                           $datas2=  $sqlstmte2->execute();
                           $desval112 = $sqlstmte2->fetchAll(PDO::FETCH_OBJ);
                           if(!empty($desval112)){

                               $title =   $desval112[0]->long_desc;
                               $title3 = str_replace("'", "\\'", $title);
                               $title3 = trim($title3);
                           }
                        }
                        $icd_code3 = str_replace($checkstring, "",$icd_code2 );
                        $check_query2 = "SELECT * FROM billing WHERE code = '$icd_code3' AND activity = 1 AND pid = '$pid' and encounter = $encounter AND code_text = '$title3'"; 
                        $db->query( "SET NAMES utf8"); 
                        $cquery2 = $db->prepare($check_query2) ;
                        $datas2=  $cquery2->execute();
                        $cqueryval2 = $cquery2->fetchAll(PDO::FETCH_OBJ);
                        if(empty($cqueryval2)){
                            $sql12 = "INSERT INTO billing (date, code_type, code, pid, provider_id, user,  groupname, authorized, encounter,  code_text, billed, activity,payer_id,bill_process,bill_date,process_date,process_file,target)"
                                                . " VALUES ( NOW(),'$checkstring1','$icd_code3','$pid','$provider_id', '$user','$groupname', ' $authorized',  ' $encounter', '$title3', '$billed', '$activity' ,'$payer_id','$bill_process','$bill_date', '$process_date','$process_file','$target')";
                            $sqlstmt12 = $db->prepare($sql12);
                            if($sqlstmt12->execute()){
                                $data = 1;
                                insertMobileLog('insert',$username,$sql12,$pid,$encounter,"New Billing CPT $checkstring1 ".$icd_code3 ." Screen",1);
                            }else {
                                $data = 0;
                                insertMobileLog('insert',$username,$sql12,$pid,$encounter,"New Billing CPT $checkstring1 ".$icd_code3 ." Screen - Failed",0);
                            }
                        }
                    }
                }
            }
        }
        // delete cpts
        $deletecpt = array();
        $deletecpt = $codearray[count($codearray)-1]['deletedpreviousCPT'];
        for($i=0; $i<count($deletecpt); $i++){
            $deletesql = "UPDATE billing SET activity = 0 WHERE id = '".$deletecpt[$i]['id']."'"; 

            $delsqlstmt = $db->prepare($deletesql) ;
            if($delsqlstmt->execute()){
                $data = 1;
                insertMobileLog('update',$username,$sql,$pid,$encounter,"Update Billing delete Screen",1);
            }else {
                $data = 0;
                insertMobileLog('update',$username,$sql,$pid,$encounter,"Update Billing delete Screen - Failed",0);
            }
        }
       // icds
       for($i=0; $i<count($icds); $i++){ 
            $icd_code = $icds[$i]['code'];
            if(!empty($icd_code)){
                $descr="select long_desc, formatted_dx_code from icd10_dx_order_code where formatted_dx_code='$icd_code' and active= 1";
                $sqlstmte = $db->prepare($descr) ;
                $datas=  $sqlstmte->execute();
                $desval1 = $sqlstmte->fetchAll(PDO::FETCH_OBJ);
                if(!empty($desval1)){
                    $title =   $desval1[0]->long_desc;
                    $title1 = str_replace("'", "\\'", $title);
                    $title2 = $desval1[0]->formatted_dx_code;
                    $title1 = trim($title1);
                } else {
                    $title1='';
                    $title2 = '';
                }
            }
            $check_query = "SELECT * FROM billing WHERE code = '$title2'  AND activity = 1 and encounter = $encounter AND code_type = 'ICD10' AND pid = '$pid' AND code_text = '$title1'"; 
            $cquery = $db->prepare($check_query) ;
            $db->query( "SET NAMES utf8"); 
            $datas2=  $cquery->execute();
            $cqueryval = $cquery->fetchAll(PDO::FETCH_OBJ);
            if(empty($cqueryval)){
                if($title1 != ''){
                    $sql1 = "INSERT INTO billing (date, code_type, code, pid, provider_id, user,  groupname, authorized, encounter,  code_text, billed, activity,payer_id,bill_process,bill_date,process_date,process_file,target)"
                                        . " VALUES ( NOW(),'ICD10','$title2','$pid','$provider_id', '$user','$groupname', ' $authorized',  ' $encounter', '$title1', '$billed', '$activity' ,'$payer_id','$bill_process','$bill_date', '$process_date','$process_file','$target')";
                    $sqlstmt1 = $db->prepare($sql1);
                    if($sqlstmt1->execute()){
                        $data = 1;
                        insertMobileLog('insert',$username,$sql1,$pid,$encounter,"New Billing ICD10 ".$title2 ." Screen",1);
                    }else {
                        $data = 0;
                        insertMobileLog('insert',$username,$sql1,$pid,$encounter,"New Billing ICD10 ".$title2 ." Screen - Failed",0);
                    }
                } 
           }
       }
       // medical problems from justify
        $justify_array = array();
        if(!empty($insertArray['justify']))
            $justify_array = explode("|",str_replace(",","|",str_replace("|",":",str_replace(":",",",$insertArray['justify']))));
        if(count($justify_array)>0){
            for($i=0; $i<count($justify_array); $i++){
                $icd_code2 = '';
                $icd_code2 = $justify_array[$i];
                $title = '';
                $title1='';$checkstring = $table = '';
                if(strpos($icd_code2, 'ICD9',0)!== false){
                    $checkstring = "ICD9:";
                    $checkstring1 = 'ICD9';
                    $table = 'icd9_dx_code';
                }
                if(strpos($icd_code2, 'ICD10',0)!== false || empty($table)){
                    $checkstring = "ICD10:";
                    $table = 'icd10_dx_order_code';
                    $checkstring1 = 'ICD10';
                }
                $exploded_code = str_replace($checkstring, "", $icd_code2);
                $title3='';

                if(!empty($exploded_code)){
                   $descr2="SELECT long_desc,formatted_dx_code FROM $table WHERE formatted_dx_code ='$exploded_code' and active= 1"; 
                   $sqlstmte2 = $db->prepare($descr2) ;
                   $datas2=  $sqlstmte2->execute();
                   $desval112 = $sqlstmte2->fetchAll(PDO::FETCH_OBJ);
                   if(!empty($desval112)){

                       $title =   $desval112[0]->long_desc;
                       $title3 = str_replace("'", "\\'", $title);
                       $titleform = $desval112[0]->formatted_dx_code;
                       $title3 = trim($title3);
                   }
                }
                $icd_code3 = str_replace($checkstring, "",$icd_code2 );
                $check_query2 = "SELECT * FROM billing WHERE code = '$icd_code3' AND activity = 1 AND pid = '$pid' and encounter = $encounter AND code_text = '$title3'"; 
                $db->query( "SET NAMES utf8"); 
                $cquery2 = $db->prepare($check_query2) ;
                $datas2=  $cquery2->execute();
                $cqueryval2 = $cquery2->fetchAll(PDO::FETCH_OBJ);
                if(empty($cqueryval2)){
                    $sql12 = "INSERT INTO billing (date, code_type, code, pid, provider_id, user,  groupname, authorized, encounter,  code_text, billed, activity,payer_id,bill_process,bill_date,process_date,process_file,target)"
                                        . " VALUES ( NOW(),'$checkstring1','$icd_code3','$pid','$provider_id', '$user','$groupname', ' $authorized',  ' $encounter', '$title3', '$billed', '$activity' ,'$payer_id','$bill_process','$bill_date', '$process_date','$process_file','$target')";
                    $sqlstmt12 = $db->prepare($sql12);
                    if($sqlstmt12->execute()){
                        $data3 = 1;
                        insertMobileLog('insert',$username,$sql12,$pid,$encounter,"New Billing CPT $checkstring1 ".$icd_code3 ." Screen",1);
                    }else {
                        $data3 = 0;
                        insertMobileLog('insert',$username,$sql12,$pid,$encounter,"New Billing CPT $checkstring1 ".$icd_code3 ." Screen - Failed",0);
                    }
                    $sql = "INSERT INTO lists ( date, pid, type, title, comments, begdate, enddate,  diagnosis, occurrence,  referredby,  outcome, destination,    reaction, activity )"
                            . " VALUES ( NOW(), '$pid','medical_problem','$title3','',NOW(), NULL,'ICD10:$exploded_code', 0,  '', 0, '', '',1 )
                             "; 
                    $sqlstmt = $db->prepare($sql) ;
                    if($sqlstmt->execute()){
                        $data = 1;
                        insertMobileLog('insert',$username,$sql,$pid,$encounter,"New Medical problem $exploded_code Screen",1);
                    }else {
                        $data = 0;
                        insertMobileLog('insert',$username,$sql,$pid,$encounter,"New Medical problem $exploded_code Screen - Failed",0);
                    }
                    $sel="select id from lists where type='medical_problem' AND title='$title3' AND pid=$pid";
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
                            if($sqlstmt1_list->execute()){
                                $data = 1;
                                insertMobileLog('insert',$username,$sql_list,$pid,$encounter,"New Medical problem Issue Encounter Screen",1);
                            }else {
                                $data = 0;
                                insertMobileLog('insert',$username,$sql_list,$pid,$encounter,"New Medical problem Issue Encounter Screen - Failed",0);
                            }
                        }    
                        $sel="select pid,type from lists_touch where pid=$pid";
                        $sqlstmt2 = $db->prepare($sel) ;
                        $data2=  $sqlstmt2->execute();
                        $idval2 = $sqlstmt2->fetchAll(PDO::FETCH_OBJ);
                        $p_id =  isset($idval2[0]->pid)? $idval2[0]->pid : 0;
                        $list_type = isset($idval2[0]->type)? $idval2[0]->type : 0;
                        if($p_id!=$pid && $list_type!='medicalproblem')    
                        {
                            $sql6="INSERT INTO lists_touch ( pid, type, date) VALUES ( $pid, 'medical_problem', NOW())";
                            $sqlstmt6 = $db->prepare($sql6) ;
                            if($sqlstmt6->execute()){
                                $data = 1;
                                insertMobileLog('insert',$username,$sql6,$pid,$encounter,"New Medical problem Encounter Link  List Touch Table Screen",1);
                            }else {
                                $data = 0;
                                insertMobileLog('insert',$username,$sql6,$pid,$encounter,"New Medical problem Issue Encounter Screen - Failed",0);
                            }
                        }    
                    }
                }
            }
        }
        
        // active medical problems
        if(!empty($insertArray['ActiveCPTCodes'])){
            for($j =0; $j< count($insertArray['ActiveCPTCodes']); $j++){
                $justify_array = explode("|",str_replace(",","|",str_replace("|",":",str_replace(":",",",$insertArray['ActiveCPTCodes'][$j]['ICD Code']))));
                if(count($justify_array)>0){
                    for($i=0; $i<count($justify_array); $i++){
                        $icd_code2 = '';
                        $icd_code2 = $justify_array[$i];
                        $title = '';
                        $title1='';$checkstring = $table = '';
                        if(strpos($icd_code2, 'ICD9',0)!== false){
                            $checkstring = "ICD9:";
                            $checkstring1 = 'ICD9';
                            $table = 'icd9_dx_code';
                        }
                        if(strpos($icd_code2, 'ICD10',0)!== false || empty($table)){
                            $checkstring = "ICD10:";
                            $table = 'icd10_dx_order_code';
                            $checkstring1 = 'ICD10';
                        }
                        $exploded_code = str_replace($checkstring, "", $icd_code2);
                        $title3='';
                        if(!empty($exploded_code)){
                           $descr2="SELECT long_desc FROM $table WHERE formatted_dx_code ='$exploded_code' and active= 1"; 
                           $sqlstmte2 = $db->prepare($descr2) ;
                           $datas2=  $sqlstmte2->execute();
                           $desval112 = $sqlstmte2->fetchAll(PDO::FETCH_OBJ);
                           if(!empty($desval112)){

                               $title =   $desval112[0]->long_desc;
                               $title3 = str_replace("'", "\\'", $title);
                               $title3 = trim($title3);
                           }
                        }
                        $current_date= date("Y-m-d");
                        if (count($db->query("SELECT * FROM lists WHERE diagnosis = 'ICD10:$exploded_code' AND title = '$title3' and type = 'medical_problem' and begdate = '$current_date' AND enddate IS NULL and activity = 1 and pid = $pid")->fetchAll())==0){
                            $sql = "INSERT INTO lists ( date, pid, type, title, comments, begdate, enddate,  diagnosis, occurrence,  referredby,  outcome, destination,    reaction,activity )"
                                    . " VALUES ( NOW(), '$pid','medical_problem','$title3','',NOW(), NULL,'ICD10:$exploded_code', 0,  '', 0, '', '' ,1)
                                     "; 
                            $sqlstmt = $db->prepare($sql) ;
                            if($sqlstmt->execute()){
                                $data = 1;
                                insertMobileLog('insert',$username,$sql,$pid,$encounter,"New Medical problem $exploded_code Screen",1);
                            }else {
                                $data = 0;
                                insertMobileLog('insert',$username,$sql,$pid,$encounter,"New Medical problem $exploded_code Screen - Failed",0);
                            }
                            $sel="select id from lists where type='medical_problem' AND title='$title3' AND pid=$pid";
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
                                    if($sqlstmt1_list->execute()){
                                        $data = 1;
                                        insertMobileLog('insert',$username,$sql_list,$pid,$encounter,"New Medical problem Issue Encounter Screen",1);
                                    }else {
                                        $data = 0;
                                        insertMobileLog('insert',$username,$sql_list,$pid,$encounter,"New Medical problem Issue Encounter Screen - Failed",0);
                                    }
                                }    
                                $sel="select pid,type from lists_touch where pid=$pid";
                                $sqlstmt2 = $db->prepare($sel) ;
                                $data2=  $sqlstmt2->execute();
                                $idval2 = $sqlstmt2->fetchAll(PDO::FETCH_OBJ);
                                $p_id =  isset($idval2[0]->pid)? $idval2[0]->pid : 0;
                                $list_type = isset($idval2[0]->type)? $idval2[0]->type : 0;
                                if($p_id!=$pid && $list_type!='medicalproblem')    
                                {
                                    $sql6="INSERT INTO lists_touch ( pid, type, date) VALUES ( $pid, 'medical_problem', NOW())";
                                    $sqlstmt6 = $db->prepare($sql6) ;
                                    if($sqlstmt6->execute()){
                                        $data = 1;
                                        insertMobileLog('insert',$username,$sql6,$pid,$encounter,"New Medical problem Encounter Link  List Touch Table Screen",1);
                                    }else {
                                        $data = 0;
                                        insertMobileLog('insert',$username,$sql6,$pid,$encounter,"New Medical problem Issue Encounter Screen - Failed",0);
                                    }
                                }    
                            }
                        }
                    }
                }
            }
        }
        if($data == 1  ){
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
       insertMobileLog('insert/update',$username,$error,$pid,$encounter,"New Billing Screen - Query Failed",0);
    }
}

//Method to store mobile settings
function storeMobileSettings()
{
   try
    {
         $flag=0;
        $db = getConnection();
//        echo "test store"; die();
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
        
        
        $sql_store_url="SELECT notes FROM list_options WHERE list_id = 'Mobile_App_Config' AND option_id = 'mbl_store_settings_site'";
        
        $sql_store_url_stmt = $db->prepare($sql_store_url) ;
        $sql_store_url_stmt->execute();
        $store_url = $sql_store_url_stmt->fetchObject();     
        if($store_url)
            $site_store_url = $store_url->notes;
        else
            $site_store_url = "";     
              
//        if($url1=='devint.coopsuite.com') {       
        if($url1 == $site_store_url) {       
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
                    insertMobileLog('insert',$username1,$sql,'','','Save Store Settings Screen',1);

                }
                else
                {
                    $patientres = '[{"id":"Not inserted"}]';
                    echo $patientresult = GibberishAES::enc($patientres, $key);
                    insertMobileLog('insert',$username1,$sql,'','','Save Store Settings Screen - Failed',0);
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
                    insertMobileLog('update',$username1,$sql,'','','Save Store Settings Screen',1);
                    //echo $patientresult = $patientres;

                }
                else
                {
                    $patientres = '[{"id":"Not Updated"}]';
                    echo $patientresult = GibberishAES::enc($patientres, $key); 
                    insertMobileLog('update',$username1,$sql,'','','Save Store Settings Screen - Failed',0);
                    //echo $patientresult = $patientres;
                }
            }
        }
        else{
            $patientres = '[{"id":"Wrong username/password"}]';
            echo $patientresult = GibberishAES::enc($patientres, $key);
            insertMobileLog('insert/update',$username1,$patientres,'','','Save Store Settings Screen - Wrong Data',0);
            //echo $patientresult = $patientres;
        }
    }
    else{
        $patientres = '[{"id":"Url Mismatched"}]';
        echo $patientresult = GibberishAES::enc($patientres, $key);
        insertMobileLog('insert/update',$username1,$patientres,'','','Save Store Settings Screen - Url Mismatched',0);
        //echo $patientresult = $patientres;
        
    }
    
    }catch(PDOException $e)
   {
        $patientres = '{"error":{"text":'. $e->getMessage() .'}}';
        echo $patientresult = GibberishAES::enc($patientres, $key);
        insertMobileLog('insert/update',$username1,$patientres,'','','Save Store Settings Screen - Query Failed',0);
        //echo $patientresult = $patientres;
    }  

}
function restoreMobileSettings()
{
   try
    {
        $flag=0;
        $db1 = getConnection();
        $key = 'rotcoderaclla';
        $restorerequest = Slim::getInstance()->request();
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        $restoreresult =  GibberishAES::dec($restorerequest->getBody(), $key);
//        $restoreresult =  GibberishAES::dec("U2FsdGVkX18Ffuj9RctMQsFHJCOMgG7CjkXPjQ+Olb0NaLBLPCrTgS92uSkKWiUZiVjRtyhCj7gBduEv5sWxV758RUfsz9oJoiSXAqUKmUtsbCvRJjmuyT9iG0cDilpOMQiG0HuFtj4b4OshVsF83ItPG3OCR0NhCC3MwX5+ysE=", $key);
        
        $restore_settingsArray = json_decode($restoreresult,TRUE);
            
        $username1=$restore_settingsArray['username'];
        $pwd1=$restore_settingsArray['password'];
        $url1=$restore_settingsArray['url'];
        
        $sql_store_url="SELECT notes FROM list_options WHERE list_id = 'Mobile_App_Config' AND option_id = 'mbl_store_settings_site'";
        
        $sql_store_url_stmt = $db1->prepare($sql_store_url) ;
        $sql_store_url_stmt->execute();
        $store_url = $sql_store_url_stmt->fetchObject();     
        if($store_url)
            $site_store_url = trim($store_url->notes);
        else
            $site_store_url = "";     
              
         $url1 = $site_store_url;
//        if($url1=='devint.coopsuite.com') {              
//         if($url1 == $site_store_url) {      
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
//                echo $meta_val[0]->meta_value;
                   
            if($meta_val)
            {  
               echo $patientresult = GibberishAES::enc($meta_val[0]->meta_value, $key);
               insertMobileLog('Restore',$username1,$sql_store_url,'','','Restored successfully',1);
//               echo $patientresult = GibberishAES::enc($patientresult, $key); 
            }
            else
            {
                $patientres = '[{"id":"0"}]';
                echo $patientresult = GibberishAES::enc($patientres, $key);   
                insertMobileLog('Restore',$username1,$sql_store_url,'','','Restore Settings - Failed',0);
            }
        }
//     }    
    }catch(PDOException $e)
    {
       $patientres = '{"error":{"text":'. $e->getMessage() .'}}';
       echo $patientresult = GibberishAES::enc($patientres, $key);
       insertMobileLog('Restore',$username1,$error,'','','Logged in Failed - Query Failed',0);
    }  

}
function restoreMobileSettings2()
{
   try
    {
        $flag=0;
        $db1 = getConnection();
        $key = 'rotcoderaclla';
        $restorerequest = Slim::getInstance()->request();
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        $restoreresult =  GibberishAES::dec($restorerequest->getBody(), $key);
//        $restoreresult =  GibberishAES::dec('U2FsdGVkX19vdeTiXoELzD47mnqS8O1fFYM9riGQq/foYfhoEPma8WryP2Pe36J3DWX58x0ztDpAvgDVBEbY7IvREzQHnPn+vOA++moJ2VQeG9GYBXCgbrPe48t3xUSu03OnrCVK7ltMxBal+oMrTg==', $key);
        
        $restore_settingsArray = json_decode($restoreresult,TRUE);
            
        $username1=$restore_settingsArray['username'];
        $pwd1=$restore_settingsArray['password'];
        $url1=$restore_settingsArray['url'];
        
        $sql_store_url="SELECT notes FROM list_options WHERE list_id = 'Mobile_App_Config' AND option_id = 'mbl_store_settings_site'";
        
        $sql_store_url_stmt = $db1->prepare($sql_store_url) ;
        $sql_store_url_stmt->execute();
        $store_url = $sql_store_url_stmt->fetchObject();     
        if($store_url)
            $site_store_url = trim($store_url->notes);
        else
            $site_store_url = "";     
              
         
//        if($url1=='devint.coopsuite.com') {              
         if($url1 == $site_store_url) {      
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
//                echo $meta_val[0]->meta_value;
                   
            if($meta_val)
            {  
               echo $patientresult = GibberishAES::enc($meta_val[0]->meta_value, $key);
//               echo $patientresult = GibberishAES::enc($patientresult, $key); 
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
//medical_record list
function getMedical_recorddata($pid) {
    $db = getConnection();
     $apikey = 'rotcoderaclla';
    // The default key size is 256 bits.
    $old_key_size = GibberishAES::size();
    GibberishAES::size(256);
    try
    {
     
     $sql1 = "select field_id from layout_options where group_name LIKE  '%Mobile%' and form_id = 'CHARTOUTPUT' AND uor<>0 order by seq";
     $sqlstmt1 = $db->prepare($sql1);
     $sqlstmt1->execute();
     $fields = $sqlstmt1->fetchAll(PDO::FETCH_OBJ);
     
     $titles=''; $columncheck='';
      for($i=0; $i<count($fields); $i++){
          foreach($fields[$i] as $value){
              $titles .= $value.',' ; 
              $columncheck .= $value." <> '' OR ";
          }
      }                 
   
     $titles2 = rtrim($titles ,',');
     $columncheck2 = rtrim($columncheck, ' OR ');
    $res12 = "select id,pid from tbl_form_chartoutput_transactions where pid=$pid AND ($columncheck2)
    order by id DESC ";
     $resstmt12 = $db->prepare($res12);
     $resstmt12->execute();
     $result12 = $resstmt12->fetchAll(PDO::FETCH_OBJ); 
     $dataarray = $result12;
     //  echo "<pre>"; print_r($dataarray); echo "</pre>";
        $newdemo1 = encode_demo($dataarray);  
        $newdemo['medical_record'] = check_data_available($newdemo1);
        if($newdemo)
        {
            //returns patients 
            $patientres = json_encode($newdemo); 
            echo $patientresult = GibberishAES::enc($patientres, $apikey);
        }
        else
        {
         $patientres = '[{"id":"0"}]';
         echo $patientresult = GibberishAES::enc($patientres, $apikey);
        }
    }catch(PDOException $e)
    {
      $patientres = '{"error":{"text":'. $e->getMessage() .'}}';
       echo $patientresult = GibberishAES::enc($patientres, $apikey);
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
