<?php
/*  
Webservice for user/provider authentication with openemr
*/

// do initial application
require '../Slim/Slim.php';
require '../Slim/Route.php';
require __DIR__ . '/RNCryptor-php-3.0.4/autoload.php';

// initialize app 
$app = new Slim();

// test file
$app->get('/getfile','getfile');

// method for provider login
$app->post('/login', 'loginUser');
//To save mobile settings
$app->post('/storemobilesettings','storeMobileSettings');
//To restore mobile settings
$app->post('/restoremobilesettings','restoreMobileSettings');
// method to get list of all patients
$app->get('/allpatients/:value/:seen', 'getAllPatients');
// to get mobile query dropdown
$app->get('/mobileallpatientfilters','MobileAllPatientFilters');
// to get mobile query dropdown
$app->get('/mobilefacilityfilters','MobileFacilityFilters');
// to get mobile query dropdown
$app->get('/mobileappointmentfilters','MobileAppointmentFilters');
// method to get list of patients belonging to given provider
$app->get('/filterpatients/:loginProvderId/:value/:seen', 'getPatientsByProvider');
//$app->get('/patientsbyfacility/:fid/:loginProvderId','getPatientsByFacility');
$app->get('/patientsbyfacility/:fid/:uid/:value/:seen','getPatientsByFacility');
// method to get all facilities
$app->get('/facilities','getAllFacilities');
// method to get list of appointments for a given date
$app->get('/patientsbyday/:loginProvderId/:day/:seen', 'getPatientsByday');
// method to get appointment dates
$app->get('/getappointmentdates/:loginProvderId','getAppointmentDates');
// method to search mobile filters patient by name
$app->post('/searchmobilepatientbyname','searchMobilePatientByName');
// to get mobile query dropdown
$app->get('/mobilemypatientfilters','MobileMyPatientFilters');
// to get incomplete patient encounter list
$app->get('/incompletepatientencounterlist/:providerid','getPatientIncompleteEncounterCount');
// to get details of incomplete encounter details
$app->get('/incompleteencounterdetails/:pid/:uid','getIncompleteEncounterList');
// to get dos form data
$app->get('/getdosformdata/:eid','getDosFormData'); 
// to get visit category and dos 
$app->get('/dosvisitlist/:pid/:eid','getDosVisitList'); 
// for google Plus login
$app->post('/googleapilogin','google_api_login'); 
// to get layout forms data
$app->get('/getdictationformdata/:eid/:formname','getDictationFormData');
// to get all form list and data for paticular encounter
$app->get('/getdosformdatadetails/:eid1/:eid2/:provider', 'getDosFormDataDetails');
// to save layoutform data
$app->post('/savelayoutformsdata','saveLayoutFormsData');
// to save layoutform data
$app->post('/savespeechdata','saveSpeechData');
// to get field name for speech text field
$app->get('/getspeechfield/:form_name', 'getSpeechField');
// to get dynamic demographics
$app->get('/getlayoutdemographics/:pid', 'getLayoutDemographicsDynamic');
// to add field to list
$app->get('/addlist/:option_id/:list_id/:username', 'addListOptions'); 
//method to get patient issues
$app->get('/getissues/:pid','getIssues'); 
//method to get prescriptions
$app->get('/getprescriptions/:patientId','getPrescriptions');
// to retrieve Patient Agency List
$app->get('/patientagencylist/:pid', 'getDynamicPatientAgencies');
// method to get list of complete/incomplete encounters for given provider
$app->get('/myencounters/:loginProvderId/:patientId/:mode', 'getEncounterList');
// to get patient insurance details list
$app->get('/getpatientinsurancelist/:pid','getPatientInsuranceDataList');
// method to get demographics billing
$app->get('/getdemographicsbilling/:pid/:with_insurance','getDemographicsBilling');
// to get incompleteencountercount
$app->get('/incompleteencountercount/:providerid','getIncompleteEncounterCount');
// to get filters data for allpatients/ mypatients screen in mobile
$app->get('/filtersdata/:pid', 'filitersData');
// to create agencies
$app->get('/createagencies', 'createAgencies');
// to edit agencies
$app->get('/editagencies/:id', 'editAgencies');
// to save patient agency
$app->post('/savepatientagency', 'savePatientAgency');
// to esign on finalize
$app->post('/finalizeeid', 'finalizeEid');
// for issues
$app->get('/editissues/:id/:pid/:type','editIssues');
// for edit Immunization
$app->get('/editimmunization/:pid/:id','editImmunization');
// for search icd's 
$app->get('/searchicds/:type/:value','searchICDs');
// for save Immunization
$app->post('/savedynamicimmunization','saveDynamicImmunization');
// for save issues
$app->post('/savedynamicissues','saveDynamicIssues');
// for delete issues
$app->get('/deleteissue/:pid/:id/:type/:username/:eid','deleteIssue');
// for delete patient agency
$app->get('/deleteagency/:id/:username','deleteAgency');
// for edit Prescription
$app->get('/editprescriptioin/:pid/:id','editPrescription');
// for save Prescription
$app->post('/savedynamicprescription','saveDynamicPrescription');
//to create / edit Reminder
$app->get('/editinsurance/:type/:pid/:id', 'editInsurance');
//to save insurance
$app->post('/saveinsurance', 'saveInsurance');
// to get billing access of user
$app->get('/getbillingaccess/:uid', 'getBillingAccess');
// to edit demographics
$app->get('/editdemographics/:pid/:group_name','editDemographics');
// to save demographics
$app->post('/savedemographics','saveDemographics');
//method to add/edit patient facility
$app->post('/editpatientfacility','editPatientFacility');
// to edit newencounterscreen
$app->get('/newencounterscreen/:loginProvderId', 'newEncounterScreen');
// to save new encounter
$app->post('/newencounter', 'createNewEncounter');
// to edit patient facility
$app->get('/getpatientfacility/:id', 'getPatientFaility');
// to get layout history
$app->get('/getlayouthistory/:pid', 'getLayoutHistory');
//medical_record data
$app->get('/eidmedicalrecord/:pid/:eid','getEncounterMedicalRecord');
//medical_record list
$app->get('/medical_recorddata/:pid','getMedical_recorddata');
// method to get appointment details
$app->get('/getappointmentdetails/:apptid','getAppointmentDetails');
//method to update appointment status as rescheduled/cancel/ no show
$app->post('/changeappstatus','changeAppointmentStatus');
$app->post('/cancelappstatus','cancelAppointmentStatus');
// method to get available time slots
$app->get('/availabletimeslots/:loginProvderId/:startingDate','getAvailableTimeSlots');
// to create new encounter when arrived status
$app->post('/createappencounter', 'createNewAppointmentEnounter');
// method to search mobile filters patient by name
$app->post('/searchfacilitypatientbyname','searchMobilePatientByNameInFacility');
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

function insertMobileLog($event,$user,$comment,$patient,$encounter,$user_notes,$success){    

    $sql = "INSERT INTO mbl_dictation_log(date,event,user,groupname,comments,patient_id,encounter_id,user_notes,success) values(NOW(),'".$event."','".$user."','Default','".addslashes($comment)."','".$patient."','".$encounter."','".$user_notes."',$success)";
    $db = getConnection();
    $stmt = $db->prepare($sql);
    $stmt->execute();
    return true;
}

function loginUser()
{   
    $key = 'rotcoderaclla';
    $request = Slim::getInstance()->request();

    $decryptor = new \RNCryptor\Decryptor();
    $cryptor = new \RNCryptor\Encryptor();
    try 
    {
        $db = getConnection();
        
        $decrypedtext = $decryptor->decrypt($request->getBody(), $key);
//        $decrypedtext = $decryptor->decrypt('AwF18lwWypnz12ALCKaQk9uglX/kxE/KQOc78nHBl6RfZ1qozVE5wLNtlyzSzNu8wyqR402956L1ntWddL4LbScbv94i7zG4lcbP/Ga0/+ChRa4148DmQQgWPtb/coBwI9umpqtZeFo5g/G8GQDKy9JU', $key);
        
        $insertArray = json_decode($decrypedtext,TRUE);
//        print_r($insertArray);
//        $insertArray = json_decode($request->getBody(),TRUE);
//        {"username":"Drketha","password":"Rise@123"}
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
            //return a hashed string
            $phash=crypt($password,$user->salt);

            if($phash==$user->password)
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
// returns id of the user/provider if user/provider is valid
//				$string = '[{"id":'.json_encode($user->id).',"fname":'.json_encode($user->fname).',"lname":'.json_encode($user->lname).',"authorized":'.json_encode($user->authorized).',"user_role":'.json_encode($user_role).',"acl_role_name":'.json_encode($acl_role).',"acl_role_permissions":'.json_encode($acl_role_permissions).',"rights":'.json_encode($rights).',"practice_name":'.json_encode($practicename).',"business_support":'.json_encode($business_support).',"technical_support":'.json_encode($technical_support).',"practice_portal":'.json_encode($practice_portal).',"provider_portal":'.json_encode($provider_portal).'}]';
                $string = '[{"id":'.json_encode($user->id).',"fname":'.json_encode($user->fname).',"lname":'.json_encode($user->lname).',"authorized":'.json_encode($user->authorized).',"user_role":'.json_encode($user_role).',"acl_role_name":'.json_encode($acl_role).',"acl_role_permissions":'.json_encode($acl_role_permissions).',"rights":'.json_encode($rights).',"practice_name":'.json_encode($practicename).',"business_support":'.json_encode($business_support).',"technical_support":'.json_encode($technical_support).',"practice_portal":'.json_encode($practice_portal).',"provider_portal":'.json_encode($provider_portal).',"business_title":'.json_encode($business_title).',"technical_title":'.json_encode($technical_title).',"provider_title":'.json_encode($provider_title).',"practice_title":'.json_encode($practice_title).',"business_access":'.json_encode($business_access).',"technical_access":'.json_encode($technical_access).',"provider_access":'.json_encode($provider_access).',"practice_access":'.json_encode($practice_access).'}]';
                echo $base64Encrypted = $cryptor->encrypt($string, $key);
//                                echo "<pre>"; print_r($string); echo "</pre>";
            } 
            else 
            {
                // if username or password is wrong return id=0
                $string = '[{"id":"0"}]';
//                echo $stringenc = GibberishAES::enc($string, $key);
                echo $base64Encrypted = $cryptor->encrypt($string, $key);
            }                               
        }
        else
        {
            // if user does not exist return id=0
            $string = '[{"id":"0"}]';
            echo $base64Encrypted = $cryptor->encrypt($string, $key);
        }
    } 
    catch(PDOException $e) 
    {
        $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo $base64Encrypted = $cryptor->encrypt($error, $key);
    }
}
//Method to store mobile settings
function storeMobileSettings()
{
   try
    {
        $flag=0;
        $db = getConnection();
        $key = 'rotcoderaclla';
        $storerequest = Slim::getInstance()->request();
        
        $decryptor  = new \RNCryptor\Decryptor();
        $cryptor    = new \RNCryptor\Encryptor();
        
        $decrypedtext = $decryptor->decrypt($storerequest->getBody(), $key);
//        $decrypedtext = $decryptor->decrypt('AwFmgazmC5EM1EilUfaxC12F9jMqCILpRaBCLGifsNsctAUmRZM4Ks9kqtWf3LuHdd4js/1Lwi8XlF6w50qqnkU3oS0a9OzRBuiHVQQvsEWIlZTq/8tZs9eRtU/5R/QaVssDQRf72cx3hLWgHejq1Fd9ORmp2JOzRygkFeEYxl0q0Ws98IaIo3lthW+wI9aWe5Og8Fv39/ph/QYW2WTF05lschEnrYYStWix3SbW1n0GD1sDjdzGztpHbg0rHpwdP0ELzyyCrss7/7bmjbUpLQd3yoYDeT1bA/YSaDIpXeBn6t671WEjt4sUPAToLO1Pc8TxPwYcdnELyzCWEs1sHYgFicqb8HSJgoU83D5M6tCdjB/7ftN3YyTVlw9xmIzegeedIe6DoDszCfHM1QDlM6FKzrhuDuaYkfbtTiq+gaIfUjJZ2hy4tiCGt/R/w4kJmVKjAL9yIWbr3vVY3gAhIdui4h9cBYiWDGIpHWgcH+0JkFWqYgmkM4KPIm27eO4ZamQA7JumgV3fujDOX/k9POGH53c8r65mSSGEym6fZ4kpC9Mq2KK74qOMg4fntwpHZscM89Nno2uBQE4cyHvTHcIPuB1aT+/blJniFSoebB83yRAZ7TVQ6PopsVvIaUNDMED0nPsomhQiVN1x86O6GcHj4c/UGuftiLRdoPwv+rBud8yFGEuoOTR0EzLOg/1CrZCw3X28noOM0o88QjFWGxfcuzAC60ap3vTvruFH1k8bLRmXD0imUO3wLGWbipFo0sa10A/gPDcbPcta66YDCdJs5b+7XhaybBCnf3wHxIvMU6iOzCk+Q3CE9d+1W9PvwqcXtWFpjM7mrNgNZuEq+DG7hHRWcS5YU5ableBLNZEsyXLDeVE2YjpKwiG2/oWwIkuMO6bU1NPUignGKGNdyAVMwaUmUroRuSf6mYy/3cTHHfB5WIWfyO7jjypjQ/g8Vyef/pO5mPwE3EfMzHZlmtQFruJpNdFhMYHFWHRzB2U/0fYxZGa6L8QlovyPC4jjNJ0qGKt75i+6hghD46gE9inafzfIVrohlcktIjabT2kQ8jhGazzbjEFhl1cclYQ2wDrVGJ/q16WFSX15UWCeCcFmuECKDI1MuGbhWEgQhYqnSTmMEzPGS28MCxsVwHKinWQvd0AHos4q5G3eVTaETTtAww+aRMJVEj9N6H8DeiiZ6/5mndJYJGULUMdPF+ieYnE2nEPPJfgI9V5MSfg4VflNw4j3OTPOdF50LOPlMZfQkUywxxNA4G0sXvvsfd4Nhjr2SeDIn+Pu9PKZjbmv4Dmcn7PFz+mGAS3KcRowxqNMFfMDnjXOWrcl/aA38RoiXlm2H9WPwWpPVSFG9SzN5Mck6n3hrc78/ukQPsD8CAqg+LI0FbpLT1hpCBZY1HuV2QmUpfVCA9P7z0W3doLmFDWdkwYzmDDqmSUbE8EmdjX692JRs29AS/9R+5969S3tCjGw6IJAgGonEdR10SCl8GDaWe1TyOTvNm1+mAYdgsLQAm/UPTYYXNTBDE+g0zXBQDAyBumeyTDE4Um/R9LFcez92WhY4UlFl5Ex7x062QtC5HdMF48PWPNsP5q+M+QtEYA5/z5cCsfQ2L5+jzOBJbO+ls/19n4F448X2VEUcV/OOpKBbdnCDPZgNMemDzVapB86MFf+ophduxXN83kJ79Ntyw0KPM/nNUsTGVCHHLF55xGx1FZ9/Tc/VqhVvtI76X7NbXgVURQntmjP8y3gN1jEx3AmPDyYWY0UFYT1CMCCwo9FCsCYWEMofBUiOYdJHiGSCPfvuVRV6MPGLf1V6wyMNeJ6/cCSI80vCODRjfR5BHSj7pIiyqHdvzP73vPSXEi1wPqJVsIipKUTo2uIt8f7eiXSoex2fGNtlTpU7I3Uo2myD3aKJHtszGNFSVtTHv4IJ/JgeWiM+LyR9319+uI5ldlJaOEmN8eluZKqhy1OYSlWZD1C9mI+NsdY+PNLOD2OMGcuas2WkEv9KflFvaaRz8vJBnLI5raKUOdTz7msw706yxb+btkp+2YA5By6/qoDKgDJIYyTCSeLWfl5IHck/jPkTVi2Pb1cztBaUNEJ58S0ZjwHVqSvlIJayzmD8tsr68HeYLhXXWBA0gYSG1UYz3YxETj1hKZYvqWnHw9M7NazEL28BWCcGniCJRlZhE8hDAAZ075hAsG3aNIcqWC89B08VG5i4IPX4w3h6csZN7XLtrEXFeXIjCzefPC9D5zD+BWyC3d3nwC0BzgbYsEwbV+aKLJl8F/bcJdbzOA8qxc/OQgr2Rb15G9s/XGZLDOUKUD6J2VJXC16gGaNCg6rWtfN7sIYuAsyWYxGrlnisFMoynmJ2r9zkla+uOcq5bu8neP54Go7uQNqyzef96NJGkII4UJv209rMdJGgxYSLys+E3y8UHQktmE0qDi3yKbBwvm87ZmGsUTPBhTrwKb4tddwkHSSdU2p+wjn426XezWUM52WUrJAa4hu+NoD/aZ/g98C2LEgEBEbjm18bo0GkGskyOU9/mioMHMYL4xmaUd1NknKtAAxopifWugWzyzUe35B5SQYoB3+nyt9yYUuguTsFcv45kNfTIR4HI2tFqWeA8VhvJ5zTV9G5nU7uis36MZVV6xJ3ihX7R8g3gxgJSuLjTFpQ0ea1hb6rMI9YmILGPxOMoHZDoE1SwC65WvK+gSOZs+8HVxrwXtQhaQm98+uIzSEqO3yziX0CIto3jQxfTL9+Hh459a3p3MWoJC0JH03LD78sKZqThROhDZkVZD0QzVJcqys8PSPN2RVu9dr6w4VyDFd7jbp49Qll4A/N9QvFN3WqVq0JwsRYGWIAmzB6ScPvlE7HOYhcNmAIsWGtYdLqXtJguVrszAO/i6vR819yAHQpU8xyBG0AtvLbxlK/0tKyk9fG7PcHlzGchwAAaLm6YN8bZaYA9CVdX/GiW0GPIOr+rmHOi/WgX0Qc51sN2dAonysw+8LIz0vDqsYcsHCTzipW+Z/WNhGilUKnKdIeAi1BVSEzRUkY4oNNd3XzI947xBafUug0Ch4P3JWFiloQ7o15J/W9NevP4GSNu/ewI8jm0Nh0GtQjpdayK9DjHX94TzUisRViCQ7opdxHHV/7fvgc2HwJ3EDg0+hFqk3Ga0HJXSwZJfHn90OwGkEmcCaJ5n1wsAn6Pnhh2+RUt1reecAJpYUSY9Bg/mxreq68LmNo/wBcPbZwz9flLz95PtOtAU4KlWyBVjkl8dvxjz0YBuIjWz18cpvf7jVQ1qfHRY4jt6JZi3ZyWNFjy7yPH58KQP9uYYgB8sroa8uZsQLfLzANkQD2r9QbprKR43dZMzxwl2slyEILmD58yKyJ4b4xfXJ2WjaPg3ERkRZQQ6sDRs4px9eXZOOVYEGwFy2PWWuko5eJkIdiU69xfn+HRtij6qpHugTH6D7nwYBsIbDHZJ2TlhPRLfJYXoj/rGcFP1OEH1p764kQeRC7HWF90QLxBAt+smnA6iqiLNHrflVX+YFgoLwT39VrFiKBvPLlB7WjU/qg5h7SGlv2op3QIgMY+ToNV1JY5axYZXEejGze5Qkri0nbaQP8s2QoqXpMc28TERK+AtTjmsGhpT8E0GROiZLFFOEmWmcL/f/3XqxYHFIvTL5q0NkVaNQL1gXOt23AmI8fzEljRibjVpml/7gVF/yy+6CKSF3GczEe4e/hntEucijFb4FjGWjzx583KMVvD4i6OsZ7PVnFaI1ilpvioI8+mMYYNIEJjOMiB0kcyYK640uB63DHZt9omEqGvS3LLq3LpsXTsFawEMDT0lGmo3tRaAjnfTE0iFhBaKGZLcuw7ndhkCwVUSb0LyvW8b1GITSHqExDUCCPjgFkoUfpQjjDDwXa32uiTaFw9zh+ii0oXGlN//ePGGUO6lrORxWUnAstGpZUDd7zzNT4AXG1HmdEhhCASW/RIw824IAM0qrDg0aTW/Z7cVgyituAgqyyrH+UA53sdIsK7RpPUyfR9JIJCD4Itd24wI5dSZ6y4C3tY98KY1uCpF9+RvX1Az/69QSDPeXb7fnQl3Qjli8gyuXKs1mCTqeug==', $key);
                    
        $json_a1    = json_decode($decrypedtext,TRUE);
        
//        echo "<pre>";print_r($json_a1); echo "</pre>";
        $username1  = $json_a1['TechSupportCredentials'][0]['Username'];
        $pwd1       = $json_a1['TechSupportCredentials'][0]['Password'];
        $url1       = $json_a1['TechSupportCredentials'][0]['URL'];
        
        // print_r($json_a1['PracticeSettings']);  
        $storesettings_data['PracticeSettings']     = $json_a1['PracticeSettings'];
        $storesettings_data['DictationSettings']    = $json_a1['DictationSettings'];
        $PracticeSettings = json_encode($storesettings_data);
        
        $sql_store_url = "SELECT notes FROM list_options WHERE list_id = 'Mobile_App_Config' AND option_id = 'mbl_store_settings_dict_site'";
        
        $sql_store_url_stmt = $db->prepare($sql_store_url) ;
        $sql_store_url_stmt->execute();
        $store_url = $sql_store_url_stmt->fetchObject();     
        if($store_url)
            $site_store_url = $store_url->notes;
        else
            $site_store_url = "";     
              
//        if($url1=='devint.coopsuite.com') {       
        if(str_replace("http://","",$url1) == $site_store_url || str_replace("https://","",$url1) == $site_store_url || $url1 == $site_store_url) {       
            $db = getConnection1();
            //echo "select ID,user_pass from wp_users where user_login='$username1' ";
            $sql = "select ID,user_pass from wp_users where user_login='$username1' ";

            $stmt = $db->prepare($sql) ; 
            $stmt->execute();
            $mobsettings = $stmt->fetchAll(PDO::FETCH_OBJ);           
//            echo "<pre>"; print_r($mobsettings); echo "</pre>";  
            $password=wp_check_password($pwd1,$mobsettings[0]->user_pass);
            if($password==1)
            {   
                $userid=$mobsettings[0]->ID;
                $sql2 = "select * from wp_usermeta where meta_key='dictation-app-settings' AND user_id=$userid";
                $stmt2 = $db->prepare($sql2) ; 
                $stmt2->execute();
                $meta_key = $stmt2->fetchAll(PDO::FETCH_OBJ); 
                if(empty($meta_key)){            
                    $sql = "INSERT INTO wp_usermeta (user_id, meta_key,meta_value)
                        values ($userid,'dictation-app-settings', '$PracticeSettings')";
                        $sqlstmt = $db->prepare($sql);
                        $data =  $sqlstmt->execute();
                    if($data)
                    {   
                        $patientres = '[{"id":"Inserted"}]';
                        echo $base64Encrypted = $cryptor->encrypt($patientres, $key);
                        insertMobileLog('insert',$username1,$sql,'','','Save Store Settings Screen',1);

                    }
                    else
                    {
                        $patientres = '[{"id":"Not inserted"}]';
                        echo $base64Encrypted = $cryptor->encrypt($patientres, $key);
                        insertMobileLog('insert',$username1,$sql,'','','Save Store Settings Screen - Failed',0);
                        //echo $patientresult = $patientres;
                    }
                }else {
                    $sql = "UPDATE wp_usermeta 
                       SET
                       meta_value='$PracticeSettings'
                       WHERE user_id=$userid AND meta_key='dictation-app-settings'";
                       $sqlstmt = $db->prepare($sql);
                       $data =  $sqlstmt->execute();
                    if($data)
                    {   
                        $patientres = '[{"id":"Updated"}]';
                        echo $base64Encrypted = $cryptor->encrypt($patientres, $key);
                        insertMobileLog('update',$username1,$sql,'','','Save Store Settings Screen',1);
                        //echo $patientresult = $patientres;

                    }
                    else
                    {
                        $patientres = '[{"id":"Not Updated"}]';
                        echo $base64Encrypted = $cryptor->encrypt($patientres, $key);
                        insertMobileLog('update',$username1,$sql,'','','Save Store Settings Screen - Failed',0);
                        //echo $patientresult = $patientres;
                    }
               }
           }
           else{
                $patientres = '[{"id":"Wrong username/password"}]';
                echo $base64Encrypted = $cryptor->encrypt($patientres, $key);
                insertMobileLog('insert/update',$username1,$patientres,'','','Save Store Settings Screen - Wrong Data',0);
                //echo $patientresult = $patientres;
           }
       }
       else{
            $patientres = '[{"id":"Url Mismatched"}]';
            echo $base64Encrypted = $cryptor->encrypt($patientres, $key);
            insertMobileLog('insert/update',$username1,$patientres,'','','Save Store Settings Screen - Url Mismatched',0);
           //echo $patientresult = $patientres;

       }
    
    }catch(PDOException $e)
   {
        $patientres = '{"error":{"text":'. $e->getMessage() .'}}';
        echo $base64Encrypted = $cryptor->encrypt($patientres, $key);
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
        $decryptor  = new \RNCryptor\Decryptor();
        $cryptor    = new \RNCryptor\Encryptor();
        $restoreresult = $decryptor->decrypt($restorerequest->getBody(), $key);
//        $restoreresult = $decryptor->decrypt('AwE/eBBIaklalrayFEZ57tcgaBjIjQkRr/kHPtcLTlnZvjHxSewtquT0AGXiZEhXz4+yZSoANrAcTmqn9rMX3VpFhzEdYGkzB5yaxoXs7pOvLmJaSOHVfJRyseP9u7SBGzihEtbR9Xg4BaJNW0GCxXvsWJ0/ZlJ1AIGOWv91+Fou895dmWWO24uRjb6uNhq7giXMglbSO8FCM54kY4KbzVIW', $key);
        
        $restore_settingsArray = json_decode($restoreresult,TRUE);
//            echo "<pre>";print_r($restore_settingsArray); echo "</pre>";
        $username1=$restore_settingsArray['username'];
        $pwd1=$restore_settingsArray['password'];
        $url1=$restore_settingsArray['url'];
        
        $sql_store_url="SELECT notes FROM list_options WHERE list_id = 'Mobile_App_Config' AND option_id = 'mbl_store_settings_dict_site'";
        
        $sql_store_url_stmt = $db1->prepare($sql_store_url) ;
        $sql_store_url_stmt->execute();
        $store_url = $sql_store_url_stmt->fetchObject();     
        if($store_url)
            $site_store_url = trim($store_url->notes);
        else
            $site_store_url = "";     
              
         
//        if($url1=='devint.coopsuite.com') {              
        if(str_replace("http://","",$url1) == $site_store_url || str_replace("https://","",$url1) == $site_store_url || $url1 == $site_store_url) {   
            $db = getConnection1();
            $sql = "select ID,user_pass from wp_users where user_login='$username1' ";

            $stmt = $db->prepare($sql) ; 
            $stmt->execute();
            $mobsettings = $stmt->fetchAll(PDO::FETCH_OBJ);           

            $password=wp_check_password($pwd1,$mobsettings[0]->user_pass);
            if($password==1)
            {
                $userid=$mobsettings[0]->ID;
   
                $sql = "select meta_value from wp_usermeta where user_id=$userid AND meta_key='dictation-app-settings'";
                $sqlstmt = $db->prepare($sql);
                $sqlstmt->execute();
                $meta_val = $sqlstmt->fetchAll(PDO::FETCH_OBJ); 
                
                if($meta_val)
                {  
                   echo $base64Encrypted = $cryptor->encrypt($meta_val[0]->meta_value, $key);
                   insertMobileLog('insert',$username1,$meta_val[0]->meta_value,'','','Restored Store Settings',0);
    //               echo $patientresult = GibberishAES::enc($patientresult, $key); 
                }
                else
                {
                    $patientres = '[{"id":"0"}]';
                    echo $base64Encrypted = $cryptor->encrypt($patientres, $key);
                    insertMobileLog('insert',$username1,$patientres,'','','Restore Settings Screen - Failed',0);
                }
            }
        }    
    }catch(PDOException $e)
    {
       $patientres = '{"error":{"text":'. $e->getMessage() .'}}';
       echo $base64Encrypted = $cryptor->encrypt($patientres, $key);
       insertMobileLog('insert',$username1,$patientres,'','','Restore Settings Screen -  Query Failed',0);
    }  

}
function wp_check_password($password,$hash) {
    global $wp_hasher;

    if ( empty($wp_hasher) ) {
        require_once( '../phpass-0.3/PasswordHash.php');
        // By default, use the portable hash from phpass
        $wp_hasher = new PasswordHash(8, true);
    }

    return $wp_hasher->CheckPassword( trim( $password ),$hash );
}
//// method to get list of all patients with filters
function getAllPatients($value,$seen)
{
    $db = getConnection();
    $apikey = 'rotcoderaclla';
    $cryptor    = new \RNCryptor\Encryptor();

    try 
    {
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
            $joinQuery = " LEFT JOIN form_encounter f ON f.pid = p.pid ";
            if(!empty($get_value))
                $query  = " WHERE f.".$get_value2[0]->notes;
            else
                $query  .= " AND f.".$get_value2[0]->notes;
        }

        $sql = "SELECT DISTINCT p. pid,title,fname,lname, DATE_FORMAT(DOB,'%m-%d-%Y') AS DOB,DATE_FORMAT(FROM_DAYS(DATEDIFF(CURDATE(),DOB)), '%Y')+0 AS age,if (sex = 'Female' ,'F','M' ) as sex,street,city,state,country_code,postal_code,phone_home,phone_biz,phone_contact,phone_cell,contact_relationship as emergency_contact, phone_contact as emergency_phone
            from patient_data p $joinQuery $query order by lname, fname limit 100 "; 

        $stmt = $db->prepare($sql) ;
        $stmt->execute();                       

        $patients = $stmt->fetchAll(PDO::FETCH_OBJ);  
        if(!empty($patients)){
            for($i=0; $i< count($patients); $i++){
                $pres[][] = $patients[$i];
            }
        }

        if($pres)
        {
            //returns patients appointment list
            $patientres = json_encode($pres); 
            echo $base64Encrypted = $cryptor->encrypt($patientres, $apikey);
        }
        else
        {
            //echo 'No Patient available';
            $patientres = '[{"id":"0"}]';
            echo $base64Encrypted = $cryptor->encrypt($patientres, $apikey);
        }
    } 
    catch(PDOException $e) 
    {

        $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo $base64Encrypted = $cryptor->encrypt($error, $apikey);
    }
}
function MobileAllPatientFilters(){
    $db = getConnection();
    $apikey = 'rotcoderaclla';
    $cryptor    = new \RNCryptor\Encryptor();
    $sql = "select option_id,title from list_options WHERE list_id = 'Mobile_All_Patients_Filters' order by seq";      

    try 
    {
        $stmt = $db->prepare($sql) ;
        $stmt->execute();                       

        $patientsreminders = $stmt->fetchAll(PDO::FETCH_OBJ);                        

        if($patientsreminders)
        {
            $patientres = json_encode($patientsreminders); 
            echo $base64Encrypted = $cryptor->encrypt($patientres, $apikey);
        }
        else
        {
            //echo 'No patients available';
            $patientres =  '[{"id":"0"}]';
            echo $base64Encrypted = $cryptor->encrypt($patientres, $apikey);
        }
    } 
    catch(PDOException $e) 
    {

        $error =  '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo $base64Encrypted = $cryptor->encrypt($error, $apikey);
    }
}
function MobileFacilityFilters(){
    $db = getConnection();
    $apikey = 'rotcoderaclla';
    $cryptor    = new \RNCryptor\Encryptor();
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
            echo $base64Encrypted = $cryptor->encrypt($patientres, $apikey);
        }
        else
        {
            //echo 'No patients available';
            $patientres =  '[{"id":"0"}]';
            echo $base64Encrypted = $cryptor->encrypt($patientres, $apikey);
        }
    } 
    catch(PDOException $e) 
    {

        $error =  '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo $base64Encrypted = $cryptor->encrypt($error, $apikey);
    }
}
function MobileAppointmentFilters(){
    $db = getConnection();
    $apikey = 'rotcoderaclla';
    $cryptor    = new \RNCryptor\Encryptor();
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
            echo $base64Encrypted = $cryptor->encrypt($patientres, $apikey);
        }
        else
        {
            //echo 'No patients available';
            $patientres =  '[{"id":"0"}]';
            echo $base64Encrypted = $cryptor->encrypt($patientres, $apikey);
        }
    } 
    catch(PDOException $e) 
    {

        $error =  '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo $base64Encrypted = $cryptor->encrypt($error, $apikey);
    }
}
// method to get list of patients belonging to given provider
function getPatientsByProvider($loginProvderId,$value,$seen)
{
    $apikey = 'rotcoderaclla';
    $cryptor    = new \RNCryptor\Encryptor();  
 
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
 
            if(!empty($patients)){
                for($i=0; $i< count($patients); $i++){
                    $patientsdata[][] = $patients[$i];
                }
            }

            if($patientsdata)
            {
                //returns patients appointment list
                $patientres = json_encode($patientsdata);   
                echo $base64Encrypted = $cryptor->encrypt($patientres, $apikey);
            }
            else
            {   
                $patientres = '[{"id":"0"}]';
                echo $base64Encrypted = $cryptor->encrypt($patientres, $apikey);
            }
        } 
        catch(PDOException $e) 
        {
            
            $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
            echo $base64Encrypted = $cryptor->encrypt($error, $apikey);
        }
}
//function getPatientsByProvider($loginProvderId,$value,$seen)
//{
//    $apikey = 'rotcoderaclla';
//    $cryptor    = new \RNCryptor\Encryptor();  
// 
//        try 
//        {
//            $db = getConnection();
//            $query = '';
//            $patientsdata = array();
//            
//            $get_sql = "select notes from list_options WHERE list_id = 'Mobile_My_Patients_Filters'  AND option_id = '$value'";      
//            $db->query( "SET NAMES utf8"); 
//            $set_stmt = $db->prepare($get_sql) ;
//            $set_stmt->execute();                       
//
//            $get_value = $set_stmt->fetchAll(PDO::FETCH_OBJ);   
//            
//            if(!empty($get_value)){
//                $query  = " AND ".$get_value[0]->notes;
//            }
//            
//            $get_sql2 = "select notes from list_options WHERE list_id = 'Mobile_Appointment_Due_Filters'  AND option_id = '$seen'";      
//            $set_stmt2 = $db->prepare($get_sql2) ;
//            $set_stmt2->execute();                       
//
//            $get_value2 = $set_stmt2->fetchAll(PDO::FETCH_OBJ);   
//            $joinQuery = '';
//            if(!empty($get_value2)){
//                $joinQuery = " INNER JOIN form_encounter f ON f.pid = p.pid ";
//                if(!empty($get_value))
//                    $query  = " WHERE f.".$get_value2[0]->notes;
//                else
//                    $query  .= " AND f.".$get_value2[0]->notes;
//            }
//            
//            $sql = "SELECT DISTINCT p.pid,title,fname,lname, DATE_FORMAT(DOB,'%m-%d-%Y') AS DOB,DATE_FORMAT(FROM_DAYS(DATEDIFF(CURDATE(),DOB)), '%Y')+0 AS age,if (sex = 'Female' ,'F','M' ) as sex,street,city,state,country_code,postal_code,phone_home,phone_biz,phone_contact,phone_cell,contact_relationship as emergency_contact, phone_contact as emergency_phone
//                from patient_data p
//                INNER JOIN form_encounter f ON f.pid = p.pid 
//                where f.rendering_provider =:loginProvderId $query order by lname, fname  limit 100"; 
//            $db->query( "SET NAMES utf8"); 
//            $stmt = $db->prepare($sql) ;
//            $stmt->bindParam("loginProvderId", $loginProvderId);            
//            $stmt->execute();
//            $patients = $stmt->fetchAll(PDO::FETCH_OBJ);   
//  
//            
////            echo "<pre>"; print_r($patients); echo "</pre>";
////            if(!empty($patients))
////                $patientsdata['PatientData'] = $patients;
////            $patientsdata['FilterData'] = $fields;
//            if($patients)
//            {
//                $patientres = json_encode($patients); 
//                echo $base64Encrypted = $cryptor->encrypt($patientres, $apikey);
//            }
//            else
//            {   
//                $patientres = '{"id":"0"}';
//                echo $base64Encrypted = $cryptor->encrypt($patientres, $apikey);
//            }
//        } 
//        catch(PDOException $e) 
//        {
//            
//            $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
//            echo $base64Encrypted = $cryptor->encrypt($error, $apikey);
//        }
//}
// method to get patients by facility
//function getPatientsByFacility($fid,$loginProvderId)
function getPatientsByFacility($fid,$uid,$value,$seen)
{
    $apikey = 'rotcoderaclla';
    $cryptor    = new \RNCryptor\Encryptor();
    $db = getConnection();
    $pres = array();
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
        $sql="select pd.pid, pd.title,pd.fname,pd.lname, DATE_FORMAT(pd.DOB,'%m-%d-%Y') AS DOB, '0' as id,
            DATE_FORMAT(FROM_DAYS(DATEDIFF(CURDATE(),pd.DOB)), '%Y')+0 AS age,if (pd.sex = 'Female' ,'F','M' ) as sex,
            pd.deceased_stat,pd.practice_status 
            from patient_data pd $joinQuery where pd.pid not in (SELECT DISTINCT(patientid) FROM tbl_patientfacility)  $query order by pd.lname, pd.fname limit 100";  
       
        try 
        {
            
            $stmt = $db->prepare($sql) ;
            $stmt->execute();
            $patients = $stmt->fetchAll(PDO::FETCH_OBJ); 
            
            if(!empty($patients)){
                for($i=0; $i< count($patients); $i++){
                    $pres[][] = $patients[$i];
                }
            }

            if($pres && count($pres) != 0)
            {
                $pres[]['useraccess'] = $useraccess;
                $patientres =  json_encode($pres); 
                echo $patientresult= $cryptor->encrypt($patientres, $apikey);
            }
            else
            {
                $patientres = '[{"id":"0"}]';
                echo $patientresult= $cryptor->encrypt($patientres, $apikey);
            }
        } 
        catch(PDOException $e) 
        {
            $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
            echo $patientresult= $cryptor->encrypt($error, $apikey);
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

            if(!empty($patients)){
                for($i=0; $i< count($patients); $i++){
//                    $pres2 = array();
//                    foreach($patients[$i] as $pkey=> $pvalue){
//                        $pres2[][$pkey] = $pvalue;
//                    }
                    $pres[][] = $patients[$i];
                }
            }

            if($pres && count($pres) != 0)
            {
                $pres[]['useraccess'] = $useraccess;
                $patientres =  json_encode($pres); 
                echo $patientsresult= $cryptor->encrypt($patientres, $apikey);
            }
            else
            {
                $patientres = '[{"id":"0"}]';
                echo $patientresult= $cryptor->encrypt($patientres, $apikey);
            }
        } 
        catch(PDOException $e) 
        {
            $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
            echo $patientresult= $cryptor->encrypt($error, $apikey);
        }
    }
	
}
//function getPatientsByFacility($fid,$uid,$value,$seen)
//{
//    $apikey = 'rotcoderaclla';
//    $cryptor    = new \RNCryptor\Encryptor();
//    $db = getConnection();
//    $get_sql = "select notes from list_options WHERE list_id = 'Mobile_Facility_Filters'  AND option_id = '$value'";      
//    $set_stmt = $db->prepare($get_sql) ;
//    $set_stmt->execute();                       
//
//    $get_value = $set_stmt->fetchAll(PDO::FETCH_OBJ);   
//    $query = '';
//    if(!empty($get_value)){
//        $query  = " AND ".$get_value[0]->notes;
//    } 
//    
//    $get_sql2 = "select notes from list_options WHERE list_id = 'Mobile_Appointment_Due_Filters'  AND option_id = '$seen'";      
//    $set_stmt2 = $db->prepare($get_sql2) ;
//    $set_stmt2->execute();                       
//
//    $get_value2 = $set_stmt2->fetchAll(PDO::FETCH_OBJ);   
//    $joinQuery = '';
//    if(!empty($get_value2)){
//        $joinQuery = " INNER JOIN form_encounter f ON f.pid = pd.pid ";
//        $query  .= " AND f.".$get_value2[0]->notes;
//    }
//    
//    $get_accesssql = "SELECT provider_plist_links FROM tbl_user_custom_attr_1to1 WHERE userid= '$uid'";
//    $get_accessstmt = $db->prepare($get_accesssql) ;
//    $get_accessstmt->execute();                       
//
//    $setaccess = $get_accessstmt->fetchAll(PDO::FETCH_OBJ);
//    $useraccess = 0;
//    if($setaccess ){
//        if (strpos($setaccess[0]->provider_plist_links,'create_enc') !== false) {
//            $useraccess = 1;
//        }
//    }
//    
//    if($fid == 0){
//        $sql="select pd.pid, pd.title,pd.fname,pd.lname, DATE_FORMAT(pd.DOB,'%m-%d-%Y') AS DOB, '0' as id,
//            DATE_FORMAT(FROM_DAYS(DATEDIFF(CURDATE(),pd.DOB)), '%Y')+0 AS age,if (pd.sex = 'Female' ,'F','M' ) as sex,
//            pd.deceased_stat,pd.practice_status 
//            from patient_data pd $joinQuery where pd.pid not in (SELECT DISTINCT(patientid) FROM tbl_patientfacility)  $query order by pd.lname, pd.fname limit 100";  
//       
//        try 
//        {
//            
//            $stmt = $db->prepare($sql) ;
//            $stmt->execute();
//            $patients = $stmt->fetchAll(PDO::FETCH_OBJ);           
//
//            if($patients && count($patients) != 0)
//            {
//                $patients['useraccess'] = $useraccess;
//                $patientres =  json_encode($patients); 
//                echo $patientresult= $cryptor->encrypt($patientres, $apikey);
//            }
//            else
//            {
//                $patientres = '[{"id":"0"}]';
//                echo $patientresult= $cryptor->encrypt($patientres, $apikey);
//            }
//        } 
//        catch(PDOException $e) 
//        {
//            $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
//            echo $patientresult= $cryptor->encrypt($error, $apikey);
//        }
//    }
//    else{
//       $sql="SELECT DISTINCT tpf.patientid as pid, tpf.id, pd.title, pd.fname, pd.lname, DATE_FORMAT( pd.DOB,  '%m-%d-%Y' ) AS DOB, DATE_FORMAT( FROM_DAYS( DATEDIFF( CURDATE( ) , pd.DOB ) ) ,  '%Y' ) +0 AS age, IF( pd.sex =  'Female',  'F',  'M' ) AS sex
//                FROM patient_data pd
//                INNER JOIN tbl_patientfacility tpf ON pd.pid = tpf.patientid $joinQuery
//                WHERE facility_isactive =  'YES'
//                AND tpf.facilityid =$fid
//                AND tpf.id
//                IN (
//
//                SELECT MAX( id ) 
//                FROM tbl_patientfacility
//                WHERE facility_isactive =  'YES' 
//                GROUP BY patientid
//                ) $query limit 100";  
//        try 
//        {
//            $db = getConnection();
//            $stmt = $db->prepare($sql) ;
//            $stmt->execute();
//            $patients = $stmt->fetchAll(PDO::FETCH_OBJ);           
//
//            if($patients)
//            {
//                $patients['useraccess'] = $useraccess;
//                $patientsres = json_encode($patients); 
//                echo $patientsresult= $cryptor->encrypt($patientsres, $apikey);
//            }
//            else
//            {
//                $patientres = '[{"id":"0"}]';
//                echo $patientresult= $cryptor->encrypt($patientres, $apikey);
//            }
//        } 
//        catch(PDOException $e) 
//        {
//            $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
//            echo $patientresult= $cryptor->encrypt($error, $apikey);
//        }
//    }
//	
//}
function getAllFacilities()
{
    //$sql="SELECT id,name,phone,fax,street,city,state,postal_code,country_code,federal_ein,website,email,service_location,billing_location,accepts_assignment,pos_code,x12_sender_id,attn,domain_identifier,facility_npi,tax_id_type,color,primary_business_entity
    //    FROM facility";
    $sql="SELECT id,name,phone,fax,street,city,state,postal_code,country_code,federal_ein,website,email,service_location,billing_location,accepts_assignment,pos_code,x12_sender_id,attn,domain_identifier,facility_npi,tax_id_type,color,primary_business_entity
          FROM facility WHERE service_location!=0";
    $apikey = 'rotcoderaclla';
    $cryptor    = new \RNCryptor\Encryptor();
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
            echo $base64Encrypted = $cryptor->encrypt($facilityres, $apikey);
        }
        else
        {
            $facilityres = '[{"id":"-1"}]';
            echo $base64Encrypted = $cryptor->encrypt($facilityres, $apikey);
        }
    } 
    catch(PDOException $e) 
    {
        $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo $base64Encrypted = $cryptor->encrypt($error, $apikey);
    }
}
// method to get list of appointments for a given date
function getPatientsByday($loginUserId,$day,$seen)
{
    $apikey = 'rotcoderaclla';
    $cryptor    = new \RNCryptor\Encryptor();
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
            echo $patientresult = $cryptor->encrypt($patientres, $apikey);
        }
        else
        {    
            //echo 'No Patient available';
            $patientres = '[{"id":"0"}]';
            echo $patientresult = $cryptor->encrypt($patientres, $apikey);
        }
    } 
    catch(PDOException $e) 
    {

        $patientres = '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo $patientresult = $cryptor->encrypt($patientres, $apikey);
    }
}
function cb($a, $b) {
    return strtotime($a->datevalue) - strtotime($b->datevalue);
}
//To get list of appointment dates
function getAppointmentDates($loginProvderId)
{
    $apikey = 'rotcoderaclla';
    $cryptor    = new \RNCryptor\Encryptor();
    try
    {	
        $db=getConnection();

        $array = array();
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
            $apptdatesult = array();
            foreach($apptdates as $app_key => $app_value){
                $apptdatesult[$app_key]['datevalue'] = date('m-d-Y', strtotime($app_value->datevalue));
            }
        }
//        echo "<pre>";print_r($apptdatesult); echo "</pre>";
        if($apptdatesult)
        {
            $appdateres =  json_encode(array_unique($apptdatesult,SORT_REGULAR)); 
            echo $appdateresult =  $cryptor->encrypt($appdateres, $apikey);

        }
        else
        {
            $appdateres = '[{"id":"0"}]';
            echo $appdateresult =  $cryptor->encrypt($appdateres, $apikey);
        }
    }catch(PDOException $e)
    {
            $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
            echo $appdateresult =  $cryptor->encrypt($error, $apikey);
    }
}
function searchMobilePatientByName(){
    try
    {
        $apikey = 'rotcoderaclla';
        $db = getConnection();
        $restorerequest = Slim::getInstance()->request();
        $decryptor = new \RNCryptor\Decryptor();
        $cryptor    = new \RNCryptor\Encryptor();
        $restoreresult = $decryptor->decrypt($restorerequest->getBody(), $apikey);
//        $decrypedtext = $decryptor->decrypt('AwHk98Z7hHhyNzDSjp2r+NG5dAFTIuU1wUTLpmx9/72r/jpeHqIgEW+IYC+GJu7lhGw65UQAkrPmbBNTgKlPcq3FGhNr/auiOb11egciXS6vRq+U/DVkBLT4y5wtKmUU3NW87e19nDTBnfuVz9UpXcg4+g79oV/2SiIGE7gIJvXsdCaLS4czfiMv5zUaEayJj/DzH0h1ZgNAuAyF1kxRzdz58aFvYaUMo3uP+NpBFAdpEdCuHNN4dUrL5afo3Ezl1kM=', $apikey);
        
        $restore_settingsArray = json_decode($restoreresult,TRUE);
//            echo "<pre>";print_r($restore_settingsArray); echo "</pre>";
        $list_id            = $restore_settingsArray['static string'];
        $loginProviderId    = $restore_settingsArray['user_id'];
        $patientName        = $restore_settingsArray['search text'];
        
        $value              = $restore_settingsArray['filter_text'];
        $fid                = $restore_settingsArray['fid'];
        
        
        
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
            $appdateres = json_encode($resGetMyPatientsByName); 
            echo $appdateresult =  $cryptor->encrypt($appdateres, $apikey);
        }
        else
        {   // Patient Not found
            $appdateres = '[{"id":"0","msg":"Patient not found"}]';
            echo $appdateresult =  $cryptor->encrypt($appdateres, $apikey);
        }
                
    }
    catch(PDOException $e) 
    {
        $appdateres = '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo $appdateresult =  $cryptor->encrypt($appdateres, $apikey);
    }    
    
}
function searchMobilePatientByNameInFacility(){
    try
    {
        $apikey = 'rotcoderaclla';
        $db = getConnection();
        $restorerequest = Slim::getInstance()->request();
        $decryptor = new \RNCryptor\Decryptor();
        $cryptor    = new \RNCryptor\Encryptor();
        $restoreresult = $decryptor->decrypt($restorerequest->getBody(), $apikey);
//        $decrypedtext = $decryptor->decrypt('AwHk98Z7hHhyNzDSjp2r+NG5dAFTIuU1wUTLpmx9/72r/jpeHqIgEW+IYC+GJu7lhGw65UQAkrPmbBNTgKlPcq3FGhNr/auiOb11egciXS6vRq+U/DVkBLT4y5wtKmUU3NW87e19nDTBnfuVz9UpXcg4+g79oV/2SiIGE7gIJvXsdCaLS4czfiMv5zUaEayJj/DzH0h1ZgNAuAyF1kxRzdz58aFvYaUMo3uP+NpBFAdpEdCuHNN4dUrL5afo3Ezl1kM=', $apikey);
        
        $restore_settingsArray = json_decode($restoreresult,TRUE);
//            echo "<pre>";print_r($restore_settingsArray); echo "</pre>";
        $list_id            = $restore_settingsArray['static string'];
        $loginProviderId    = $restore_settingsArray['user_id'];
        $patientName        = $restore_settingsArray['search text'];
        
        $value              = $restore_settingsArray['filter_text'];
        $fid                = $restore_settingsArray['fid'];
        $list_id2           = $restore_settingsArray['list_id2'];
        $seen               = $restore_settingsArray['seen'];
        
        
        
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
            $appdateres = json_encode($resGetMyPatientsByName); 
            echo $appdateresult =  $cryptor->encrypt($appdateres, $apikey);
        }
        else
        {   // Patient Not found
            $appdateres = '[{"id":"0","msg":"Patient not found"}]';
            echo $appdateresult =  $cryptor->encrypt($appdateres, $apikey);
        }
                
    }
    catch(PDOException $e) 
    {
        $appdateres = '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo $appdateresult =  $cryptor->encrypt($appdateres, $apikey);
    }    
    
}
function MobileMyPatientFilters(){
    $apikey = 'rotcoderaclla';
    $cryptor    = new \RNCryptor\Encryptor();
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
            echo $patientresult = $cryptor->encrypt($patientres, $apikey);
        }
        else
        {
            //echo 'No patients available';
            $patientres =  '[{"id":"0"}]';
            echo $patientresult = $cryptor->encrypt($patientres, $apikey);
        }
    } 
    catch(PDOException $e) 
    {

        $patientres =  '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo $patientresult = $cryptor->encrypt($patientres, $apikey);
    }
}
// incomplete encounter count
function getPatientIncompleteEncounterCount($providerid){
   
    try 
    {
        $db = getConnection();
        $apikey = 'rotcoderaclla';
        $cryptor    = new \RNCryptor\Encryptor(); 
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
            $sql = "SELECT f.pid, p.lname, p.fname, dob, GROUP_CONCAT( CASE WHEN (DATE_FORMAT( f.date, '%Y-%m-%d' ) <> '0000-00-00') THEN DATE_FORMAT( f.date, '%Y-%m-%d' ) END  ORDER BY f.date ASC) AS dos, COUNT( f.id ) AS encounter_count,  COUNT( f.id ) AS visit_count
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
        if($count)
        {
            //returns count 
            $countres = json_encode($count); 
            echo $patientresult = $cryptor->encrypt($countres, $apikey);
        }
        else
        {
            $countres = '[{"id":"0"}]';
            echo $patientresult = $cryptor->encrypt($countres, $apikey);
        }
    } 
    catch(PDOException $e) 
    {

         $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
         echo $patientresult = $cryptor->encrypt($error, $apikey);
    }
   
}
// incomplete encounter list
function getIncompleteEncounterList($pid,$uid){
    
    try 
    {
        $db = getConnection();
        $apikey = 'rotcoderaclla';
        $cryptor    = new \RNCryptor\Encryptor();

        // to get vist category list
        $get_fuv = "SELECT facilities,visit_categories FROM tbl_allcare_facuservisit WHERE  `users`  REGEXP ('".":\"".$uid."\"')";
        $fuv_stmt = $db->prepare($get_fuv) ;
        $fuv_stmt->execute();
        $set_fuv = $fuv_stmt->fetchAll(PDO::FETCH_OBJ); 
        for($i = 0; $i<count($set_fuv); $i++){
           $array[]     =  unserialize( $set_fuv[$i]->visit_categories);
           $array2[]    =  unserialize( $set_fuv[$i]->facilities);
        }
        $dataArray = array();
        if(!empty($array)){
            for($j = 0; $j<count($array); $j++){
                foreach($array[$j] as $arraykey){
                    $dataArray[] = $arraykey;
                }
            }
        }
        $enc_val = '';
        $dataarray = array_unique($dataArray);
        foreach($dataarray as $arrayval){
            $enc_val .= $arrayval.",";
        }
        $enc_value = rtrim($enc_val,",");
        $dataArray2 = array();
        if(!empty($array2)){
            for($j = 0; $j<count($array2); $j++){
                foreach($array2[$j] as $arraykey2){
                     $dataArray2[] = $arraykey2;
                }
            }
        }
        $enc_val2 = '';
        $dataarray2 = array_unique($dataArray2);
        foreach($dataarray2 as $arrayval2){
            $enc_val2 .= $arrayval2.",";
        }
        $enc_value2 = rtrim($enc_val2,",");
        if($enc_value != '' || $enc_value2 != '' ){
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
        }else{
            $formlabels = array();
        }
        $formfields = array();
        $formvalues = array();
        $datacheck8= array();
        $datacheck7 = array();
        $dataArr = array();
        if(!empty($formlabels)){
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
        }

//        echo "<pre>"; print_r($formfields); echo "</pre>";

//        $newdemo1=encode_demo(array_filter($formfields));
//        $newdemo['EncounterData'] = check_data_available($newdemo1);

       //echo "<pre>"; print_r($newdemo); echo "</pre>";
        if($formfields)
        {
            //returns count 
            $newdemores = json_encode($formfields);
            echo $newdemoresult = $cryptor->encrypt($newdemores, $apikey);
        }
        else
        {
           $incompletelist = '[{"id":"0"}]';
           echo $incompletelistresult = $cryptor->encrypt($incompletelist, $apikey);
        }
    } 
    catch(PDOException $e) 
    {
        $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo $patientresult = $cryptor->encrypt($error, $apikey);
    }
   
}
//function getIncompleteEncounterList($pid,$uid){
//    
//    try 
//    {
//        $db = getConnection();
//        $apikey = 'rotcoderaclla';
//        $cryptor    = new \RNCryptor\Encryptor();
//
//        // to get vist category list
//        $get_fuv = "SELECT facilities,visit_categories FROM tbl_allcare_facuservisit WHERE  `users`  REGEXP ('".":\"".$uid."\"')";
//        $fuv_stmt = $db->prepare($get_fuv) ;
//        $fuv_stmt->execute();
//        $set_fuv = $fuv_stmt->fetchAll(PDO::FETCH_OBJ); 
//        for($i = 0; $i<count($set_fuv); $i++){
//           $array[]     =  unserialize( $set_fuv[$i]->visit_categories);
//           $array2[]    =  unserialize( $set_fuv[$i]->facilities);
//        }
//        $dataArray = array();
//        if(!empty($array)){
//            for($j = 0; $j<count($array); $j++){
//                foreach($array[$j] as $arraykey){
//                    $dataArray[] = $arraykey;
//                }
//            }
//        }
//        $enc_val = '';
//        $dataarray = array_unique($dataArray);
//        foreach($dataarray as $arrayval){
//            $enc_val .= $arrayval.",";
//        }
//        $enc_value = rtrim($enc_val,",");
//        $dataArray2 = array();
//        if(!empty($array2)){
//            for($j = 0; $j<count($array2); $j++){
//                foreach($array2[$j] as $arraykey2){
//                     $dataArray2[] = $arraykey2;
//                }
//            }
//        }
//        $enc_val2 = '';
//        $dataarray2 = array_unique($dataArray2);
//        foreach($dataarray2 as $arrayval2){
//            $enc_val2 .= $arrayval2.",";
//        }
//        $enc_value2 = rtrim($enc_val2,",");
//        if($enc_value != '' || $enc_value2 != '' ){
//        $sql = "SELECT form_encounter.facility,form_encounter.facility_id, 
//            form_encounter.encounter,form_encounter.pc_catid AS visitcategory_id,
//            DATE_FORMAT( form_encounter.date,  '%Y-%m-%d' ) AS dos,
//            form_encounter.rendering_provider as provider_id
//                    FROM form_encounter
//                    INNER JOIN patient_data ON patient_data.pid = form_encounter.pid
//                    WHERE form_encounter.pid =$pid AND form_encounter.pc_catid IN ($enc_value) AND form_encounter.facility_id IN($enc_value2)
//                        AND (
//                         `elec_signed_on` = '' AND `elec_signedby` = ''
//                        ) AND form_encounter.rendering_provider = '$uid' ORDER BY form_encounter.date DESC ";
//
//        $stmt = $db->prepare($sql) ;
//        $stmt->execute(); 
//        $formlabels = $stmt->fetchAll(PDO::FETCH_OBJ); 
//        }else{
//            $formlabels = array();
//        }
//        $formfields = array();
//        $formvalues = array();
//        $datacheck8= array();
//        $datacheck7 = array();
//        $dataArr = array();
//        if(!empty($formlabels)){
//            foreach($formlabels as $element): 
//                $sql6 = "SELECT DISTINCT(fe.encounter),CONCAT(pd.title,pd.fname,' ',pd.lname) as pname,DATE_FORMAT( fe.date,  '%Y-%m-%d' ) AS dos, fe.facility AS facility, fe.pid AS pid, fe.pc_catid AS visitcategory_id,oe.pc_catname as visitcategory,audited_status
//                    FROM form_encounter fe
//                    INNER JOIN patient_data pd on pd.pid = fe.pid
//                    INNER JOIN openemr_postcalendar_categories oe ON oe.pc_catid = fe.pc_catid
//                    WHERE fe.pid = $pid and fe.encounter = $element->encounter 
//                    AND (
//                     `elec_signed_on` = '' AND `elec_signedby` = ''
//                    ) ";
//                    $stmt6 = $db->prepare($sql6);
//                    $stmt6->execute(); 
//                    $datacheck6 = $stmt6->fetchAll(PDO::FETCH_OBJ);
//                    if(!empty($datacheck6)):
//                        $datacheck6[0]->form_status = 'Incomplete';
//                        $datacheck7['finalizetype'] = 'checkbox';
//                        if($datacheck6[0]->audited_status == 'Completed'){
//                            $datacheck7['isfinalize'] = 'Enable';
//                            $datacheck7['title'] = 'Finalize';
//                            $datacheck6[0]->finalize = $datacheck7;
//                        }else{
//                            $datacheck6[0]->audited_status = 'Incomplete';
//                            $datacheck7['isfinalize'] = 'Disable';
//                            $datacheck7['title'] = 'Finalize';
//                            $datacheck6[0]->finalize = $datacheck7;
//                        }
//                        //$datacheck6[0]->finalize_field_type = $datacheck7;
//
//                    endif;
//                $formfields[] = $datacheck6;
//
//            endforeach;
//        }
//
////        echo "<pre>"; print_r($formfields); echo "</pre>";
//
//        $newdemo1=encode_demo(array_filter($formfields));
//        $newdemo['EncounterData'] = check_data_available($newdemo1);
//
//       //echo "<pre>"; print_r($newdemo); echo "</pre>";
//        if($newdemo1)
//        {
//            //returns count 
//            $newdemores = json_encode($newdemo);
//            echo $newdemoresult = $cryptor->encrypt($newdemores, $apikey);
//        }
//        else
//        {
//           $incompletelist = '[{"id":"0"}]';
//           echo $incompletelistresult = $cryptor->encrypt($incompletelist, $apikey);
//        }
//    } 
//    catch(PDOException $e) 
//    {
//        $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
//        echo $patientresult = $cryptor->encrypt($error, $apikey);
//    }
//   
//}
// to get form data based on dos
function getDosFormData($eid){
  try 
    {
        $db = getConnection();
        
        $apikey     = 'rotcoderaclla';
        $cryptor    = new \RNCryptor\Encryptor();
        
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
                                WHERE lo.title = '$value3->screen_name' AND lo.group_name LIKE '%Dictation'
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
                    $stmt4 = $db->prepare($sql4);
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
                        if(empty($datacheck)){ 
                            $field_value = '';
                            $sql5 = "SELECT l.field_id, SUBSTRING(l.group_name ,-length(l.group_name),1) as grouporder,SUBSTRING(l.group_name FROM 2) as GroupName,
                                     l.description
                                from layout_options l WHERE l.title LIKE '$value3->screen_name' and l.form_id='LBF1' AND l.group_name LIKE '%Dictation'";
                            $stmt5 = $db->prepare($sql5);
                            $stmt5->execute();  
                            $datacheck2 = $stmt5->fetchAll(PDO::FETCH_OBJ); 
                            if(!empty($datacheck2)){
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
                            $formvalues[] = (object)array('form_type' => $datacheck2[0]->GroupName,  'form_id'=> $form_id, 'form_name' =>$datacheck2[0]->field_id, 'FormOrder' =>$datacheck2[0]->FormOrder, 'grouporder' => $datacheck2[0]->grouporder, 'GroupName' => $value3->screen_name, 'isRequired' => $datacheck2[0]->isRequired , 'description' => $datacheck2[0]->description, 'field_value' => $field_value, 'id'=>$datacheck2[0]->id);
                        }else{ 
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
        
//             $new = encode_demo(array_filter( $arr));
//             $newdemo['FormsData'] = check_data_available($new);
        
//         $newobject = array_filter( $arr);
//            $object = (object) $newobject;
//            foreach($object as $item) {
//                $objects[] = (object)$item;
//            }
////             echo "<pre>"; print_r($objects);echo "</pre>";
//             $new = encode_demo($objects);
//             $newdemo['FormsData'] = check_data_available($new);
//             $newarray = (object)$newdemo['FormsData'];
            if($arr) {
                $newdemores = json_encode($arr);
                echo $incompletelistresult = $cryptor->encrypt($newdemores, $apikey);

            }else
            {
                $demo1='[{"id":"0"}]';
//                $newdemo1=encode_demo($demo1);      
//                $newdemo['FormsData'] = check_data_available($newdemo1);
                $newdemores = json_encode($demo1);
                echo $incompletelistresult = $cryptor->encrypt($newdemores, $apikey);
            }
    } 
    catch(PDOException $e) 
    {

        $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo $incompletelistresult = $cryptor->encrypt($error, $apikey);
    }
  
}
function getDosVisitList($pid,$eid){
    try 
    {
        $db = getConnection();
        
        $apikey     = 'rotcoderaclla';
        $cryptor    = new \RNCryptor\Encryptor();
        
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
            echo $dosvisitresult = $cryptor->encrypt($dosvisitres, $apikey);
        }
        else
        {
            $dosvisitres = '[{"id":"0"}]';
            echo $dosvisitresult = $cryptor->encrypt($dosvisitres, $apikey);
        }
    } 
    catch(PDOException $e) 
    {

        $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo $incompletelistresult = $cryptor->encrypt($error, $apikey);
    }

}
function google_api_login(){
    try 
    {
        $db = getConnection();
        
        $apikey     = 'rotcoderaclla';
        $storerequest = Slim::getInstance()->request();
        $decryptor  = new \RNCryptor\Decryptor();
        $cryptor    = new \RNCryptor\Encryptor();
        
        $decrypedtext = $decryptor->decrypt($storerequest->getBody(), $apikey);
//        $decrypedtext = $decryptor->decrypt('AwHdRmtZ36QlzgSjoqB2A0G8TAzDM4I7xe2aSsTWQ3TlV775AQDrCm1rWjytviL/VO+PAVQXHvBmWE0J1/BVbUn0GSbhQ91loAEtR2jkq7rfg+FME0ZJXZr8qpCZm/RtDt6nCkNJoxxs53jAJD/BVaaI39wB7eDL+1mKF4LRvN8GNmCAeSiofgxDwMW6QJ/Aj6J31fnNXDgKKgITYaBcRc9LFf0Otd7/yPcuqXkPC2nE7zHofsMU7igMqXyuSIJMxo+Hj4qiAQ0V9nQTppCddRhNjS4mzk/x3I384KjzmZWyOuXBBEtRb+BK+Rg0bfnVoi0jV9wZ1RBo8Qnd+3oUUkok96Rsz4wyB0QqMDtsW+vkmZPVWp0Y6ns96r5BamLbY0fO6egTOUS4l8bSL3TfXmwW1oWPDPo+PEnkwR+hjuJe4RcGGZDPJtUyYgLsYyNgsJWBRn5I+HgNNcO0huSgC2oPu3z1IvdLev8S1AlAkbOx8QJI47VSQPtZluioUkdIw5ev3KTzpRVdDxh7uYavv/XwMvcltlTHrcnJ0iGRDjaOCo0Yk89xFeWFq5wZrmSeujxlCwjek7iO8fkst5hKW65OQF+Dwel7JL/EN+lkh+XmZnpgQ9GN83PXNTBb87EHFqaJwNpW0H2WVqGFjTkGDf0KMui8ieo32HJeePTG/pSkCymdj2hSZ8T8xCSZv/qhTC87XflK1Apjq4qxprnOvWcYBEi3PfYlU8gmoSgiZnKCIagElLv/kMCPmLvPysBmhGyRcGEi+69y9mSabKU9s+ONyFa95GeOj38vHvrZX47mzYdmhXRmBI1jlJDbv4uUMMQnet9rTmksThjNSEReTGjsjCpP3KifjXyaDlrOU/g8HVgZBPeJEsS/7cpoVv+AM1Ddsh/crtS4eBhD0DaAahwTaLq26Wxer/rSlSSd3dkQvXk96RTnVYcJm7nfBZr4Bn5+8O1gXprRrwXOIo5JYlgu8IwqyQsEO7It/9lHVJ9Jp9UD9hCo32G2w+TyNzL8UEcSyTcn9/FVqDRBCsaXvjX5YlZZBkTsxr2hBFkwq6wJwn2r85iiWt2hAjsKUu0v6hielVoh1DYmwnkA2XtVPgcIcLfDGlGZwzcDSxeSHxTu/3D/O//78Isu3Dx2rMC2eGVqc8i8IVG4IxahIpc4FL8P8jHTKQGh2XzkrkAf7BaNZiB72TSFLwMOlvi8ApCvLdpYup3FScbvftThJNTojFWfmeCmeICRNYdHOUmHRd/W9t6KkBULI9gvaI6mLrwTdcWWWq1dDURAY4Q7PCeXxG6RSFP1Fw/Hc64hIFEK59xWBQcvqGvzrod5cLM+vIvPxM6yu5ujSZKmz/iA1xEYzgTdtg3r7ZcKn3ScnIOVM+6D3Y+CU5qpojULPfWc0PWpSg6HEu9TdRaxMdwjFk9u6jO+VB9qUYpsz9oa9cUFyyifUUPNLfWOLnl+SxsbWZZIYHjRzCRGgllyoHh20sB6gcfRrLCG9/vOYqIRGInDuuHvxVYdUMprHdBqfqw3xSzL6Q2VVI05zEuav6kbCineiBjj79weAjzhfUxfZehqO22BK7vhT8nwBrasM5g1XCItSx8q8U5OJUYZGam+mZpJu+0BHKQ7MJqKrqB2r+RFQtqNENnpoMRjhJkb0KfLZKcZlNlecY0wuQFUu7Ucu2F0p8lA0GNIO+yq53CpytibgTdfIXnGc4wS34GYw9Vs1QulJuRuggiGt52EeB0aWtlFvK55S17Prg9yHfYG0RQvjgIuPSagzYSm1hRugIDNwF0IHMrnYQgVrppMzD5odncFV3YUqqSdd6h07whydC5hjYEOw6unN65wKc5DM1ZrVWPkIJWZKY5k1a7cn1XbcmmlcsndcISOiCTsDpYKu9G+8iG8Y1Ow7w+9KDu0pTd4c2ZZQEGXzObzKVn/NmNMKdNIHcnKsb9gFyxqdJXHMWAMkgMJXxDHe5uoyAznxEUd7qN/JuEz4Q8PYmNT97AizfWdmOt5YfSNp5ugPAub7xrby52aIflpvfDepJbWhQCcM3uJpeyljMAj97Qup0JhMvvw2OVGu0cf8Sp671U7OVglPJ7GZAkyQaxrYYSoDMHNLHJlxHXiq0YVlMSq9OcGzlAq57wfKJ7CnD9Z5U3+vUJmbnnxxNwXjy1yM+8qdPStZ2/R0NZOK7E/OiTAcVz0R4eEu7Yuunkqpzq2rUt/Q7Xmf0E/GTiw5HrrvEP1EjbK1ksja40Pw09NXmye/7HxKtGuCkwwdCVKF8p91zDvxPn7BFri88TbL5N07joKtQcBozUEulF710FVoyg3rvIOEjliqx/7FL7cywe0POhqdp9C7ZMrnMQCBK02Zl9k5EBiZQWZGS6G9PpEZyrKADI7D/ijYKBtkzrJPJTgaGnBeeC00414RQnMB/2XvbGSILJ8KXb4WmoQULyI8slIqIZ2Lr+ij4jnxdosZ91xRylTb0pkg8qSfbVqq2wbId2YHocLxt2LmOEaSpjP45eWij6ms58y5ccMOWT9UJsMX0vXaSqgXHhlM8JsANEUrt3kGFkU0LJRgVLwenmL80BwthoK6KcsIyqr04r3MkbB8TblTHRWJEHK+spBSxx3kEpqXl0PsImS3W4crMQRCqyTY1HfvRHfG1UAeblOrSp2lDKAUf0GvlPGaGxP3XpuggKDU8dzCEB1B8glPZySUl/n5JX+nvUWtCBrmUGH8P69PY+yXetBdD27mSkYumCv9OxJ5122Cy4IpO/7DRdpn3H1WZ5sfb42ZC0JtaesdbInaBPQr/ztaOb3uiJbjG58TRULWqLR4bluL5cEuLFi8xFf7IFxD5dHwT0OvgTTNJcdW/dqfOwId+ikhgpcN3qY1NFtuR7HwBFcWE6SA7tXlE0Q0alSc/tJgYatzLgt0ap0a8sEEEG3ox9RWaNoBTj1WCx6WSUsh7SfYH/iRcfVFfZKE+0rRTVFlv1gjGqT7uA3asRRVq3wSSLO/kls2ujjFymxpjk4Kf1jf/XoTeZ67I0tO3xkcD3WGd5y9jkI+DHGIu+Y1y+k6HnZdTPZE6zfa5GPRQrKYvwGrW07MBrg9mMknJL7RrUy+K4YeNfMqt1nfUhjzAFzMIkhJeAVFPg9r+75JMIXFNZdyOLnNNFXsUJGBmkNMzAsAxKfmNqse0hs6xkctQwijIpr2o2hCYBUH11qCZ04GOMrzGS3fTHb/hy7o7+ITLt2rHEnN53JwOKNCaSrXOmTYlC1eTArVern0mdu2BJB4NLpoBPpy0SlO8V568mWXUH+j3fMaL2fZbxlEIEVpYZyWePbgjpYAUAviuRan3s9dkjPUnVOAui03Xd6tVQvXErCslfdYJrC0QJ/a5csj3ON8ziwXl3z6gY2UfooSrbw0jdleU5yS/N0acGalO2zIa4F1wH2OwAG7qOpOX4WEcRtONnfg8lM9nMjZnlFoIzkRwdzVKiD0328zR8mKFQht60KqI/b1EXR8KwyXqDYEHki7QHyBeslPjTwzlyt3qFW7TF4rAk8p2Kud9YrMsxlPeiaN4ig3fTzjSXxQp+HqH4OSsNMmcR9mGYRW05m6QK3PFLoqQiJlcMoVuNpupevhPrs77SRKuaYYDLKuHVjkbG0deHahh1WNgg9gVIDEBkNqrGlFe997Fc6I6KiiT/Rj9xaw16wOzl3mnoZTfDS3x0qkSJuIw1hj+dYrzq4+3PeqTx1yH0ZVpcbx0Dgg8UHYoxrVU5kFCyIIS9sYGDnzrbuYeGGCAf9uWXXBUeD5SQJrFy4k7lbO6xjc0Zat5zX+Sjfbm0WH2QgCLlXsMdS8OoOzMBdz51Ht0x+nUKrYTBgbwf1mbfR21NkhUXZ92VS2aBEyYZI+veTBvNEnDKnxaMKb8zh/HmPyFZ5J+TEWcNi18J8qwepCw6h/VvNOAm2wTm/4FuCd94LkYTpff2tJpSdmwAtdA==', $key);
//        $decrypedtext= '{"name":"DS Dharma","userID":"114032289131858120856","accessToken":"ya29.ngLvlTczcoYeMf9ChzonPKPYK2pTj1h4X6mXaGRHEJZ-hTJI4tZ2AdEZdOMX4z4D8g","email":"rithikdharma@gmail.com","clientID":"39206616439-en032ocv4j0darro92apjb97h3m71876.apps.googleusercontent.com","refreshToken":"1\/Ifp3j4JzjYxKMgI4vMM0KhmWOcmgCfXcgQ-Ym-rupQsMEudVrK5jSpoR30zcRFq6"}';            
        $json_a1    = json_decode($decrypedtext,TRUE);
//       echo $decrypedtext[0]['accessToken'];
//        echo "<pre>";print_r($json_a1); echo "</pre>";
        $name           = $json_a1['name'];
//        $userID         = $json_a1['userID'];
        $accessToken    = $json_a1['accessToken'];
        $email          = $json_a1['email'];
        $clientID       = $json_a1['clientID'];
        $refreshToken   = $json_a1['refreshToken'];
        
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
                echo $base64Encrypted = $cryptor->encrypt($string, $apikey);
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
                    echo $base64Encrypted = $cryptor->encrypt($string, $apikey);
                }
            }else{
                // if username or password is wrong return id=0
                $string = '[{"id":"0","msg":"The Email User doesnot exists in EMR."}]';
                echo $base64Encrypted = $cryptor->encrypt($string, $apikey);
            }
        }
        }else{
            $string = '[{"id":"0","msg":"Login Token Issue."}]';
            echo $base64Encrypted = $cryptor->encrypt($string, $apikey);
        }
    } 
    catch(PDOException $e) 
    {
        $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo $incompletelistresult = $cryptor->encrypt($error, $apikey);
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
function getDictationFormData($eid,$formname2){
    try{
        $apikey = 'rotcoderaclla';
        $cryptor    = new \RNCryptor\Encryptor();
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
            $patientres = json_encode($newdemo,JSON_FORCE_OBJECT); 
//             echo "<pre>"; print_r($newdemo); echo "</pre>";
//             echo "<br /> -------- <br />";
//             echo $patientres;
            echo $patientresult = $cryptor->encrypt($patientres, $apikey);
        }
        else
        {
            //echo 'No patients available';
            $patientres = '[{"id":"0"}]';
            echo $patientresult = $cryptor->encrypt($patientres, $apikey);
        }
    } 
    catch(PDOException $e) 
    {
        $patientres = '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo $patientresult = $cryptor->encrypt($patientres, $apikey);
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

// to save layoutformdata
function saveLayoutFormsData(){
    try{
        $db = getConnection();
        $request = Slim::getInstance()->request();
        $apikey = 'rotcoderaclla';
        $decryptor  = new \RNCryptor\Decryptor();
        $cryptor    = new \RNCryptor\Encryptor();
        $appres = $decryptor->decrypt($request->getBody(), $apikey);
//        $appres = $decryptor->decrypt('AwG80pakhkC7/Vy7aD5j6f1NLJXUOc8UpuMgyJMa/8EVP0mt4Nsh62Fa0vzRitOTgVPwrm+7tdPVSokdThjCERcw6zWAo55P8REBhh1MdAdItcn1WhlR7LuwS+OdZCQNaWJLBeEyDJ7Xr0WSvf7kryVLhTW3cfW/B+aVfR2JGiYSq59VHfm+3ii2KMsSI5h6cyPveYdDk/lrN25GdSXESUfWSNBLxFIPCwAWyRNFSGdCSeGoPD0xbaMZNuyPjYfLMPBntlRgxWInBEqkycjaD0ioqE2VzFCsVkdXHRPjXtxFvCI+5OoISs90UX8QteXyxoWZZzCuHM1wRltJ/Sdl0c1J5WCQlVoiGvv8z8UT26z4+M3wcslzKjsivMCUpzUb9ZR4UlAO48WQaTYX4Mh7uOqzmoV9E7GSnBEhAWM+gaC6Ow9UfH00CNNrrZpzsEcrnQ7OUySxFQiNE1HzK8xflTFu/T79q+Ozfx4ZhaZ2a1qduhwF8tSA+Grn+vhjeEfjgMTgUaFr20I8lMZl9D+K53tpqca/9iBaR/o8H7MvSwzjb8Br6Y8qFpznwToRVr5UWX/5gTQvNVZaJEf3o9gKe2XICZEqCmh0VT4osQWcsS0vn0YLMPHZH6m+7CMcm1oa+31wrTO5uu3veCS0CveVxPgG3CPq4IMYoisviWD9PFUOgr6hPs7KEjnE7RZQtDiznvDLYaRHiR1Xn7/+7NqgWcQm6rv2WzwAcvslBxntkUUbrki4CzkfGOXN4l5smBPRrY5NLSjNKg1AFtCfFLq/nFsP59P2KmZSE2W8/j0/1uQ5wLxC+dGyvl/iJDvCa/KIXcqD8LpZD+b0rgls+dKx6VIuFANvcyR4Cext8MGj/RXifceKY979BfR+Vc1TfijzqLDxfuNGMQtKG9Ck9NUw50k2AyhtP81rProWIhNN8PumX4fOVQrICd5uc3SAheowyEY=', $apikey);
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
            echo $patientresult = $cryptor->encrypt($patientres, $apikey);
        }
        else
        {
            //echo 'No patients available';
            $patientres =  '[{"id":"0"}]';
            echo $patientresult = $cryptor->encrypt($patientres, $apikey);
        }
    } 
    catch(PDOException $e) 
    {
        $patienterror = '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo $patientresult = $cryptor->encrypt($patienterror, $apikey);
        insertMobileLog('insert/update',$username,$patienterror,$pid,$encounter,"LBF form ($form_name) Screen - Query Failed",0);
    }
}
function saveSpeechData(){
    try{
        $db = getConnection(); 
        $request = Slim::getInstance()->request();
        $apikey = 'rotcoderaclla';
        $decryptor  = new \RNCryptor\Decryptor();
        $cryptor    = new \RNCryptor\Encryptor();
        $appres = $decryptor->decrypt($request->getBody(), $apikey);
//        $appres = $decryptor->decrypt('AwF2arsQuJQhKyv/ujPLBKXJ1XsYJ4Zw2A74nIjgYnkZVHbZNy8DGW6KsmLQX1J77+AbP0TVP2uFda7vrnm21W7rqSpQfE4tC4jugGmNAXFoukjyl/mzCMWR7BLYll+O95eeNRW96Hjnl5vjPdMKnT9+IX4W7ExATjsNkPp50nHhxKf7hVWo5SkFs95yl2IQgaVN8LXe+P7S8B/2TTDmBCvmIuXvAR2MPi04/SAi4qo2t+z7TRt5nz2UPLIJagDmhMvFny+QhH/XbvEkTWqn4v4iMkenKOPer7t648quF5F0Eg==', $apikey);
        $insertArray = json_decode($appres,TRUE);
//	echo "<pre>"; print_r($insertArray); echo "</pre>";exit();
        $encounter               =  $insertArray['encounter'];
        $form_name               =  $insertArray['form_name'];
        $form_id                 =  $insertArray['form_id'];
        $pid                     =  $insertArray['pid'];
        $username                =  $insertArray['user'];
//        $username2               =  getUserName($insertArray['user']);
//        $username                =  isset($username2[0]['username'])? $username2[0]['username'] : ''; 
        $authorized              =  1; //$insertArray['authorized'];
        $field_id                =  $insertArray['field_id'];
        $field_value             =  $insertArray['field_value'];
        $source                  =  $insertArray['source']."(mobile)";
        
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
            $form_id = $newformid;
//            foreach($field_id_data as $key => $value): 
//            if (count($db->query("SELECT * FROM lbf_data WHERE form_id = $newformid AND field_id = '$field_id'")->fetchAll())==0) {
                $sql = "INSERT into lbf_data (form_id, field_id, field_value) VALUES ($newformid,'$field_id','".addslashes($field_value)."')";
                $checkinsert = 'insert';
//            } else {
//               $sql = "UPDATE lbf_data SET field_value = '".addslashes($value)."' WHERE field_id ='$field_id'  AND form_id = $newformid";
//               $checkinsert = 'update';
//            }
            $stmt4 = $db->prepare($sql);
            if($stmt4->execute())
                insertMobileLog($checkinsert,$username,$sql,$pid,$encounter,"LBF form($form_name) Screen",1);
            else 
                insertMobileLog($checkinsert,$username,$sql,$pid,$encounter,"LBF form ($form_name) Screen - Failed",0);
            $datacheck = 1;
//            endforeach;
        else:
            if (count($db->query("SELECT * FROM lbf_data WHERE form_id = $form_id AND field_id = '$field_id'")->fetchAll())==0) {
                $sql = "INSERT into lbf_data (form_id, field_id, field_value) VALUES ($form_id,'$field_id','".addslashes($field_value)."')";
                $checkinsert = 'insert';
            } else {
                $getformdata = "SELECT field_value FROM lbf_data WHERE form_id = '$form_id' AND field_id ='$field_id' ";
                $db->query( "SET NAMES utf8");
                $stmt4 = $db->prepare($getformdata) ;
                $stmt4->execute();
                $formidvalue = $stmt4->fetchAll(PDO::FETCH_OBJ);
                $old_data =  $formidvalue[0]->field_value;
                
               $sql = "UPDATE lbf_data SET field_value = '".addslashes($old_data." ".$field_value)."' WHERE field_id ='$field_id'  AND form_id = $form_id";
               $checkinsert = 'update';
            }
            $stmt4 = $db->prepare($sql);
            if($stmt4->execute())
                insertMobileLog($checkinsert,$username,$sql,$pid,$encounter,"LBF form($form_name) Screen",1);
            else 
                insertMobileLog($checkinsert,$username,$sql,$pid,$encounter,"LBF form ($form_name) Screen - Failed",0);
            $datacheck = 1;
        endif;
        $sql5 = "INSERT into mbl_transcribe_log (form_id, form_name, encounter,pid,speech_data,source,log_date,user) VALUES ($form_id,'$form_name',$encounter,$pid,'".addslashes($field_value)."','".addslashes($source)."',NOW(),'".addslashes($username)."')";
//        $checkinsert = 'insert';

        $stmt5 = $db->prepare($sql5);
        if($stmt5->execute())
            insertMobileLog('insert',$username,$sql5,$pid,$encounter,"LBF form($form_name) Screen",1); 
        else 
            insertMobileLog('insert',$username,$sql5,$pid,$encounter,"LBF form ($form_name) Screen - Failed",0);
        
        if($datacheck == 1)
        {
             $presres = '[{"id":"1"}]';
            echo $presresult = $cryptor->encrypt($presres, $apikey);
            
        }
        else
        {
            $presres = '[{"id":"0"}]';
            echo $presresult = $cryptor->encrypt($presres, $apikey);
        }
    } 
    catch(PDOException $e) 
    {
        $patienterror = '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo $patientresult = $cryptor->encrypt($patienterror, $apikey);
        insertMobileLog('insert/update',$username,$patienterror,$pid,$encounter,"LBF form ($form_name) Screen - Query Failed",0);
    }
}
function getSpeechField($form_name){
    try 
    {
        $db = getConnection();  
        $key = 'rotcoderaclla';
        $cryptor    = new \RNCryptor\Encryptor();
        //To get Prescriptions
       /* $sql = "select     
 id,patient_id,filled_by_id,pharmacy_id,date_added,date_modified,provider_id,encounter,start_date,drug,drug_id,rxnorm_drugcode,form,dosage,quantity,size,unit,route,substitute,refills,per_refill,filled_date,medication,note,active,site,prescriptionguid,erx_source,erx_uploaded,drug_info_erx from prescriptions where patient_id=$patientId and active='1'";*/
        if($form_name=='hpi'){
            $form_name1='History of Present illness';
        }else {
            $form_name1=$form_name;
        }         
        $sql = "SELECT field_id FROM layout_options WHERE group_name LIKE '%$form_name1' and field_id LIKE '%_text'";
        $stmt = $db->prepare($sql) ; 
        $stmt->execute();
        $presclist = $stmt->fetchAll(PDO::FETCH_OBJ);           

        if($presclist)
        {
            $presres = json_encode($presclist); 
            echo $presresult = $cryptor->encrypt($presres, $key);
               
        }
        else  
        {
            $presres = '[{"DataAvailable":"No"}]';
            echo $presresult = $cryptor->encrypt($presres, $key);
        }
                
    }catch(PDOException $e)
    {
        $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo $presresult = $cryptor->encrypt($error, $key);
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
        $cryptor    = new \RNCryptor\Encryptor();
        // The default key size is 256 bits.
//        $old_key_size = GibberishAES::size();
//        GibberishAES::size(256);
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
            $data = array();
            $g = 0;
            
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
                                                $data[substr($setgroupnames->group_name,1)][]['N/A'] = rtrim($natype, ',');
                                                $data[substr($setgroupnames->group_name,1)][]['Normal'] = rtrim($ntype, ',');
                                                $data[substr($setgroupnames->group_name,1)][]['Abnormal'] = rtrim($atype, ',');
                                            }else{
                                                $data[substr($setgroupnames->group_name,1)][][$title] = $sethis;
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
                                                    $data[substr($setgroupnames->group_name,1)][][$fieldnamesarray->title] = $smokingdata;
                                                else
                                                    $data[substr($setgroupnames->group_name,1)][][$titlefield] = $smokingdata;
                                            }else{
                                                $data[substr($setgroupnames->group_name,1)][][$title] = $sethis;
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
                                                $data[substr($setgroupnames->group_name,1)][][$fieldnamesarray->title] = $smokingdata;
                                            }else{
                                                $data[substr($setgroupnames->group_name,1)][][$title] = $sethis;
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
                                                $data[substr($setgroupnames->group_name,1)][][$fieldnamesarray->title] = $smokingdata;
                                            }else{
                                                $data[substr($setgroupnames->group_name,1)][][$title] = $sethis;
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

                                                $data[substr($setgroupnames->group_name,1)][][$fieldnamesarray->title] = rtrim($arraydata,',');
                                            }else{
                                                $data[substr($setgroupnames->group_name,1)][][$title] = $sethis;
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
                                                $data[substr($setgroupnames->group_name,1)][][$title] = $sethis;
                                            }    
                                        }elseif($fieldnamesarray->data_type == 10 || $fieldnamesarray->data_type == 11 || $fieldnamesarray->data_type == 38  ){
                                            if(!empty($sethis)){
                                                $getprovidername = "SELECT  CONCAT(fname,' ',lname) as name FROM users WHERE id = $sethis";
                                                $db->query( "SET NAMES utf8");
                                                $stmt6 = $db->prepare($getprovidername) ;
                                                $stmt6->execute();                       
                                                $setprovidername2  = $stmt6->fetchAll(PDO::FETCH_OBJ);
                                                $providername = $setprovidername2[0]->name;
                                                $data[substr($setgroupnames->group_name,1)][][$title] = ucwords($providername);
                                            }else{
                                                $data[substr($setgroupnames->group_name,1)][][$title] = ucwords($sethis);
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
                                                $data[substr($setgroupnames->group_name,1)][][$title] = ucwords($title2);
                                            }else{
                                                $data[substr($setgroupnames->group_name,1)][][$title] = ucwords($sethis);
                                            }    
                                        }elseif($fieldnamesarray->data_type == 12 ){
                                            if(!empty($sethis)){
                                                $getpharmacyname = "SELECT  name FROM pharmacies WHERE id = $sethis";
                                                $db->query( "SET NAMES utf8");
                                                $stmt6 = $db->prepare($getpharmacyname) ;
                                                $stmt6->execute();                       
                                                $setpharmacyname  = $stmt6->fetchAll(PDO::FETCH_OBJ);
                                                $pharmacyname = $setpharmacyname[0]->name;
                                                $data[substr($setgroupnames->group_name,1)][][$title] = ucwords($pharmacyname);
                                            }else{
                                                $data[substr($setgroupnames->group_name,1)][][$title] = ucwords($sethis);
                                            }    
                                        }elseif($fieldnamesarray->data_type == 37 ){
                                            if(!empty($sethis)){
                                                $getinsurancename = "SELECT  name FROM insurance_companies WHERE id = $sethis";
                                                $db->query( "SET NAMES utf8");
                                                $stmt6 = $db->prepare($getinsurancename) ;
                                                $stmt6->execute();                       
                                                $setinsurancename  = $stmt6->fetchAll(PDO::FETCH_OBJ);
                                                $insurancename = $setinsurancename[0]->name;
                                                $data[substr($setgroupnames->group_name,1)][][$title] = ucwords($insurancename);
                                            }else{
                                                $data[substr($setgroupnames->group_name,1)][][$title] = ucwords($sethis);
                                            }    
                                        }elseif($fieldnamesarray->data_type == 35 ){
                                            if(!empty($sethis)){
                                                $getfacilityname = "SELECT  name FROM facility WHERE id = $sethis";
                                                $db->query( "SET NAMES utf8");
                                                $stmt6 = $db->prepare($getfacilityname) ;
                                                $stmt6->execute();                       
                                                $setfacilityname  = $stmt6->fetchAll(PDO::FETCH_OBJ);
                                                $facilityname = $setfacilityname[0]->name;
                                                $data[substr($setgroupnames->group_name,1)][][$title] = ucwords($facilityname);
                                            }else{
                                                $data[substr($setgroupnames->group_name,1)][][$title] = ucwords($sethis);
                                            }    
                                        }else{
                                            if($title == 'fname')
                                                $data[substr($setgroupnames->group_name,1)][]['First_Name'] = $sethis;
                                            elseif($title == 'mname')
                                                $data[substr($setgroupnames->group_name,1)][]['Middle_Name'] = $sethis;
                                            elseif($title == 'lname')
                                                $data[substr($setgroupnames->group_name,1)][]['Last_Name'] = $sethis;
                                            else{
                                                if($fieldnamesarray->data_type == 4){
                                                if($sethis == '0000-00-00 00:00:00' || $sethis == '0000-00-00' || $sethis == '')
                                                        $valueThis = $sethis;
                                                    else
                                                        $valueThis  = date('Y-m-d',strtotime($sethis));
                                                    $data[substr($setgroupnames->group_name,1)][][$title] = $valueThis;
                                                }else{
                                                    $data[substr($setgroupnames->group_name,1)][][$title] = $sethis;
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
            if(!empty($data))
                $dcheck[] = $data;
        }
//        echo "<pre>"; print_r($dcheck);echo "</pre>";
//        $newdemo1 = encode_demo($data);  
//        $newdemo['Demographics'] = check_data_available($newdemo1);
        //exit();
        if($dcheck){
           $hisres = json_encode($dcheck);
           echo $historyresult =  $cryptor->encrypt($hisres, $enckey);
        }else{
            $hisres = '[{"id":"0"}]';
            echo $historyresult =  $cryptor->encrypt($hisres, $enckey);
        }
    }catch(PDOException $e)
    {
        $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo $historyresult =  $cryptor->encrypt($error, $enckey);
    }   
}
//function getLayoutDemographicsDynamic($patientId){
//    try
//    {	
//        $newdemo = array();
//        $dataarray = array();
//        //$group_name = str_replace('_', ' ', $group_name2);
//        $db=getConnection();
//        $enckey = 'rotcoderaclla';
//        $cryptor    = new \RNCryptor\Encryptor();
//        // The default key size is 256 bits.
////        $old_key_size = GibberishAES::size();
////        GibberishAES::size(256);
//        $getgroupnames = "SELECT DISTINCT(group_name) as group_name from layout_options where form_id='DEM' and uor <> 0 order by group_name";
//        $stmt = $db->prepare($getgroupnames) ;
//        $stmt->execute();   
//        $data = '';
//        $setgroupnames2 = $stmt->fetchAll(PDO::FETCH_OBJ);  
//        foreach($setgroupnames2 as $setgroupnames){
//            $fieldnames = "select field_id, title,data_type, seq, list_id 
//                        from layout_options where form_id='DEM' and uor <> 0 AND group_name='".$setgroupnames->group_name."' order by seq";
//            $db->query( "SET NAMES utf8");
//            $stmt2 = $db->prepare($fieldnames); 
//            $stmt2->execute();
//            $fieldnamesresult = $stmt2->fetchAll(PDO::FETCH_OBJ);
//            //$fieldnameslist2 = '';
//            $data = array();
//            if(!empty($fieldnamesresult)){
//                $check = '';
//                foreach($fieldnamesresult as $fieldnamesarray){
//                    $listname = $fieldnamesarray->list_id;
//                    if(strpos($fieldnamesarray->field_id, 'em_') === false ){ 
//                        $fieldlabels = $fieldnamesarray->field_id ;
//                    }else{ 
//                        $check = 1; 
//                        $fieldlabels = str_replace('em_', 'e.', $fieldnamesarray->field_id);
//                    }
//                    if($check == 1){
//                        $getDemographicsData = "SELECT $fieldlabels FROM patient_data p INNER JOIN employer_data e ON p.pid = e.pid WHERE p.pid= $patientId";
//                    }else{
//                        $getDemographicsData = "SELECT $fieldlabels FROM patient_data WHERE pid= $patientId";
//                    }    
//                    $db->query( "SET NAMES utf8");
//                    //$gethistorydata = "SELECT ".$fieldnamesarray->field_id ." FROM patient_data WHERE pid = $patientId order by date desc limit 1";
//                    $stmt3 = $db->prepare($getDemographicsData); 
//                    $stmt3->execute();
//                    $sethistorydata = $stmt3->fetchAll(PDO::FETCH_OBJ); 
//                    if($sethistorydata != ''){
//                        for($i=0; $i< count($sethistorydata); $i++){
//                            foreach($sethistorydata[$i] as $key => $sethis){
//                                if(!empty($sethis)){
//                                    if(!empty($fieldnamesarray->title)):
//                                        $title = $fieldnamesarray->title;
//                                    else:
//                                        $title = $fieldnamesarray->field_id;
//                                    endif;
//                                    if($fieldnamesarray->list_id != ''){
//                                        $examdata = '';
//                                        if($fieldnamesarray->data_type == 23){
//                                            if(!empty($sethis)){
//                                                $explodeval2 = explode('|', $sethis);
//                                                $explodelist2  = array();
//                                                $natype = $ntype = $atype = '';
//                                                for($i= 0; $i< count($explodeval2); $i++){
//                                                    $explodelist2 = explode(":", $explodeval2[$i]);
//                                                    $slashed_data  = addslashes($explodelist2[0]) ;
//                                                    $getvalname = "SELECT title FROM list_options WHERE option_id =  '$slashed_data' AND list_id = '$fieldnamesarray->list_id'";
//                                                    $db->query( "SET NAMES utf8");
//                                                    $stmt6 = $db->prepare($getvalname) ;
//                                                    $stmt6->execute();                       
//                                                    $setvalname2  = $stmt6->fetchAll(PDO::FETCH_OBJ);  
//                                                    foreach($setvalname2 as $setvalname){
//                                                        $text = '';
//                                                        if($explodelist2[1] == 0){
//                                                            $type = 'N/A';
//                                                            if(!empty($explodelist2[2])){
//                                                               $text =  "(".$explodelist2[2].")";
//                                                            }
//                                                            $natype .= $setvalname->title.$text.",";
//                                                        }elseif($explodelist2[1] == 1){
//                                                            $type = 'Normal';
//                                                            if(!empty($explodelist2[2])){
//                                                               $text =  "(".$explodelist2[2].")";
//                                                            }
//                                                            $ntype .= $setvalname->title.$text.",";
//                                                        }elseif($explodelist2[1] == 2){
//                                                            $type = 'Abnormal';
//                                                            if(!empty($explodelist2[2])){
//                                                               $text =  "(".$explodelist2[2].")";
//                                                            }
//                                                            $atype .= $setvalname->title.$text.",";
//                                                        }
//                                                    }
//                                                }
//                                                $data[substr($setgroupnames->group_name,1)]['N/A'] = rtrim($natype, ',');
//                                                $data[substr($setgroupnames->group_name,1)]['Normal'] = rtrim($ntype, ',');
//                                                $data[substr($setgroupnames->group_name,1)]['Abnormal'] = rtrim($atype, ',');
//                                            }else{
//                                                $data[substr($setgroupnames->group_name,1)][$title] = $sethis;
//                                            }    
//                                        }elseif($fieldnamesarray->data_type == 32  ){
//                                            if(!empty($sethis)){
//                                                $titlefield = '';
//                                                $explodeval = explode("|", $sethis);
//                                                $title = '';
//                                                if(!empty($explodeval[3])){
//                                                    $slashes_value = addslashes($explodeval[3]);
//                                                    $getvalname = "SELECT title FROM list_options WHERE option_id =  '$slashes_value' AND list_id = '$fieldnamesarray->list_id'";
//                                                    $db->query( "SET NAMES utf8"); 
//                                                    $stmt6 = $db->prepare($getvalname) ;
//                                                    $stmt6->execute();                       
//                                                    $setvalname2  = $stmt6->fetchAll(PDO::FETCH_OBJ);
//                                                    foreach($setvalname2 as $setvalname){
//                                                        $titlefield = $setvalname->title."&nbsp;";
//                                                    }
//                                                }
//                                                $statustypes = array('current'=>'Current','quit' =>'Quit','never' => 'Never','not_applicable' => 'N/A');
//                                                $statusname = '';
//                                                foreach($statustypes as $key => $stype){
//                                                    if($explodeval[1] == $key.$fieldnamesarray->field_id):
//                                                        $statusname = $stype;
//                                                    endif;
//                                                }
//                                                $smokingdata = $titlefield.$explodeval[0].str_repeat('&nbsp;', 5)."Status: ".$statusname. "  ".$explodeval[2];
//                                                if(empty($titlefield))
//                                                    $data[substr($setgroupnames->group_name,1)][$fieldnamesarray->title] = $smokingdata;
//                                                else
//                                                    $data[substr($setgroupnames->group_name,1)][$titlefield] = $smokingdata;
//                                            }else{
//                                                $data[substr($setgroupnames->group_name,1)][$title] = $sethis;
//                                            }    
//                                        }elseif($fieldnamesarray->data_type == 22 ){
//                                            $smokingdata = '';
//                                            if(!empty($sethis)){
//                                                $titlefield = '';
//                                                $explodeval = explode("|", $sethis);
//                                                foreach($explodeval as $tlist){
//                                                    $explodeval = explode(":", $tlist);
//                                                    $title = '';
//                                                    if(!empty($explodeval[1])){
//                                                        $slashes_value = addslashes($explodeval[0]);
//                                                        $getvalname = "SELECT title FROM list_options WHERE option_id =  '$slashes_value' AND list_id = '$fieldnamesarray->list_id'";
//                                                        $db->query( "SET NAMES utf8"); 
//                                                        $stmt6 = $db->prepare($getvalname) ;
//                                                        $stmt6->execute();                       
//                                                        $setvalname2  = $stmt6->fetchAll(PDO::FETCH_OBJ);
//                                                        foreach($setvalname2 as $setvalname){
//                                                            $titlefield = $setvalname->title."&nbsp;";
//                                                        }
//                                                    }
//                                                    if(!empty($titlefield))
//                                                        $smokingdata .= "<b>".$titlefield.str_repeat('&nbsp;', 2).":</b>  ".$explodeval[1]."<br />";
//                                                }
//                                                $data[substr($setgroupnames->group_name,1)][$fieldnamesarray->title] = $smokingdata;
//                                            }else{
//                                                $data[substr($setgroupnames->group_name,1)][$title] = $sethis;
//                                            }    
//                                        }elseif($fieldnamesarray->data_type == 25 ){
//                                            $smokingdata = '';
//                                            if(!empty($sethis)){
//                                                $titlefield = '';
//                                                $explodeval = explode("|", $sethis);
//                                                foreach($explodeval as $tlist){
//                                                    $checkstring = '';
//                                                    $explodeval = explode(":", $tlist);
//                                                    $title = '';
//                                                    if(!empty($explodeval[1]) || !empty($explodeval[2])){
//                                                        $slashes_value = addslashes($explodeval[0]);
//                                                        $getvalname = "SELECT title FROM list_options WHERE option_id =  '$slashes_value' AND list_id = '$fieldnamesarray->list_id'";
//                                                        $db->query( "SET NAMES utf8"); 
//                                                        $stmt6 = $db->prepare($getvalname) ;
//                                                        $stmt6->execute();                       
//                                                        $setvalname2  = $stmt6->fetchAll(PDO::FETCH_OBJ);
//                                                        foreach($setvalname2 as $setvalname){
//                                                            $titlefield = $setvalname->title."&nbsp;";
//                                                        }
//                                                    }
//                                                    if($explodeval[1] == 1)
//                                                        $isyes = 'YES';
//                                                    else
//                                                        $isyes = 'NO';
//                                                    if(!empty($explodeval[2]) )
//                                                        $checkstring = $isyes. " " . $explodeval[2]."<br />";
//                                                    else if(empty($explodeval[2]) && $isyes == "YES" )
//                                                        $checkstring = $isyes. "<br />";
//                                                    if(!empty($checkstring))
//                                                        $smokingdata .= "<b>".$titlefield.str_repeat('&nbsp;', 2).":</b>".$checkstring; 
//                                                }
//                                                $data[substr($setgroupnames->group_name,1)][$fieldnamesarray->title] = $smokingdata;
//                                            }else{
//                                                $data[substr($setgroupnames->group_name,1)][$title] = $sethis;
//                                            }    
//                                        }else{
//                                            if(!empty($sethis)){
//                                                $explodeval = explode("|", $sethis);
//                                                $arraydata = '';
//                                                for($i=0; $i< count($explodeval); $i++){
//                                                    $slashed = addslashes($explodeval[$i]);
//                                                    $getvalname = "SELECT title FROM list_options WHERE option_id =  '$slashed' AND list_id = '$fieldnamesarray->list_id'";
//                                                    $db->query( "SET NAMES utf8");
//                                                    $stmt6 = $db->prepare($getvalname) ;
//                                                    $stmt6->execute();                       
//                                                    $setvalname2  = $stmt6->fetchAll(PDO::FETCH_OBJ);
//                                                    if(!empty($setvalname2))
//                                                        $arraydata .= $setvalname2[0]->title.",";
//                                                }    
//
//                                                $data[substr($setgroupnames->group_name,1)][$fieldnamesarray->title] = rtrim($arraydata,',');
//                                            }else{
//                                                $data[substr($setgroupnames->group_name,1)][$title] = $sethis;
//                                            }   
//                                        }
//                                    }else{ 
//                                        if($fieldnamesarray->data_type == 28){
//                                            if(!empty($sethis)){
//                                                $explodeval = explode("|", $sethis);
//                                                $statusname = '';
//                                                $statustypes = array('current'=>'Current','quit' =>'Quit','never' => 'Never','not_applicable' => 'N/A');
//                                                foreach($statustypes as $key => $stype){
//                                                    $exp1 = isset($explodeval[1])? $explodeval[1] : '';
//                                                    if($exp1 == $key.$fieldnamesarray->field_id):
//                                                        $statusname = $stype;
//                                                    endif;
//                                                }
//                                                $exp0 = isset($explodeval[0])? $explodeval[0]: '';
//                                                $exp2 = isset($explodeval[2])? $explodeval[2] : '';
//                                                $smokingdata = $exp0.str_repeat('&nbsp;', 5)."Status: ".$statusname. "  ".$exp2;
//                                                $data[substr($setgroupnames->group_name,1)][$fieldnamesarray->title] = $smokingdata;
//                                            }else{
//                                                $data[substr($setgroupnames->group_name,1)][$title] = $sethis;
//                                            }    
//                                        }elseif($fieldnamesarray->data_type == 10 || $fieldnamesarray->data_type == 11 || $fieldnamesarray->data_type == 38  ){
//                                            if(!empty($sethis)){
//                                                $getprovidername = "SELECT  CONCAT(fname,' ',lname) as name FROM users WHERE id = $sethis";
//                                                $db->query( "SET NAMES utf8");
//                                                $stmt6 = $db->prepare($getprovidername) ;
//                                                $stmt6->execute();                       
//                                                $setprovidername2  = $stmt6->fetchAll(PDO::FETCH_OBJ);
//                                                $providername = $setprovidername2[0]->name;
//                                                $data[substr($setgroupnames->group_name,1)][$title] = ucwords($providername);
//                                            }else{
//                                                $data[substr($setgroupnames->group_name,1)][$title] = ucwords($sethis);
//                                            }    
//                                        }elseif($fieldnamesarray->data_type == 14 ){
//                                            if(!empty($sethis)){
//                                                $getprovidername = "SELECT  CONCAT(fname,' ',lname) as name, username, organization FROM users WHERE id = $sethis";
//                                                $db->query( "SET NAMES utf8");
//                                                $stmt6 = $db->prepare($getprovidername) ;
//                                                $stmt6->execute();                       
//                                                $setprovidername2  = $stmt6->fetchAll(PDO::FETCH_OBJ);
//                                                $title2 = '';
//                                                if(!empty($setprovidername2)){
//                                                    if (empty($setprovidername2[0]->username) ) {
//                                                       $title2 = $setprovidername2[0]->organization;
//                                                    }else{
//                                                        $title2 = $setprovidername2[0]->name;
//                                                    }
//                                                }
//                                                $data[substr($setgroupnames->group_name,1)][$title] = ucwords($title2);
//                                            }else{
//                                                $data[substr($setgroupnames->group_name,1)][$title] = ucwords($sethis);
//                                            }    
//                                        }elseif($fieldnamesarray->data_type == 12 ){
//                                            if(!empty($sethis)){
//                                                $getpharmacyname = "SELECT  name FROM pharmacies WHERE id = $sethis";
//                                                $db->query( "SET NAMES utf8");
//                                                $stmt6 = $db->prepare($getpharmacyname) ;
//                                                $stmt6->execute();                       
//                                                $setpharmacyname  = $stmt6->fetchAll(PDO::FETCH_OBJ);
//                                                $pharmacyname = $setpharmacyname[0]->name;
//                                                $data[substr($setgroupnames->group_name,1)][$title] = ucwords($pharmacyname);
//                                            }else{
//                                                $data[substr($setgroupnames->group_name,1)][$title] = ucwords($sethis);
//                                            }    
//                                        }elseif($fieldnamesarray->data_type == 37 ){
//                                            if(!empty($sethis)){
//                                                $getinsurancename = "SELECT  name FROM insurance_companies WHERE id = $sethis";
//                                                $db->query( "SET NAMES utf8");
//                                                $stmt6 = $db->prepare($getinsurancename) ;
//                                                $stmt6->execute();                       
//                                                $setinsurancename  = $stmt6->fetchAll(PDO::FETCH_OBJ);
//                                                $insurancename = $setinsurancename[0]->name;
//                                                $data[substr($setgroupnames->group_name,1)][$title] = ucwords($insurancename);
//                                            }else{
//                                                $data[substr($setgroupnames->group_name,1)][$title] = ucwords($sethis);
//                                            }    
//                                        }elseif($fieldnamesarray->data_type == 35 ){
//                                            if(!empty($sethis)){
//                                                $getfacilityname = "SELECT  name FROM facility WHERE id = $sethis";
//                                                $db->query( "SET NAMES utf8");
//                                                $stmt6 = $db->prepare($getfacilityname) ;
//                                                $stmt6->execute();                       
//                                                $setfacilityname  = $stmt6->fetchAll(PDO::FETCH_OBJ);
//                                                $facilityname = $setfacilityname[0]->name;
//                                                $data[substr($setgroupnames->group_name,1)][$title] = ucwords($facilityname);
//                                            }else{
//                                                $data[substr($setgroupnames->group_name,1)][$title] = ucwords($sethis);
//                                            }    
//                                        }else{
//                                            if($title == 'fname')
//                                                $data[substr($setgroupnames->group_name,1)]['First_Name'] = $sethis;
//                                            elseif($title == 'mname')
//                                                $data[substr($setgroupnames->group_name,1)]['Middle_Name'] = $sethis;
//                                            elseif($title == 'lname')
//                                                $data[substr($setgroupnames->group_name,1)]['Last_Name'] = $sethis;
//                                            else{
//                                                if($fieldnamesarray->data_type == 4){
//                                                if($sethis == '0000-00-00 00:00:00' || $sethis == '0000-00-00' || $sethis == '')
//                                                        $valueThis = $sethis;
//                                                    else
//                                                        $valueThis  = date('Y-m-d',strtotime($sethis));
//                                                    $data[substr($setgroupnames->group_name,1)][$title] = $valueThis;
//                                                }else{
//                                                    $data[substr($setgroupnames->group_name,1)][$title] = $sethis;
//                                                }
//                                            }
//                                        } 
//                                    }
//                                } 
//                            }    
//                        }    
//                    }   
//                    $listname  = '';
//                }
//            }
//            $dcheck[] = $data;
//        }
////        echo "<pre>"; print_r($dcheck);echo "</pre>";
////        $newdemo1 = encode_demo($data);  
////        $newdemo['Demographics'] = check_data_available($newdemo1);
//        //exit();
//        if($dcheck){
//           echo $hisres = json_encode($dcheck);
//           echo $historyresult =  $cryptor->encrypt($hisres, $enckey);
//        }else{
//            $hisres = '[{"id":"0"}]';
//            echo $historyresult =  $cryptor->encrypt($hisres, $enckey);
//        }
//    }catch(PDOException $e)
//    {
//        $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
//        echo $historyresult =  $cryptor->encrypt($error, $enckey);
//    }   
//}
function addListOptions($option_id,$list_id,$username){
    $apikey = 'rotcoderaclla';

    $cryptor    = new \RNCryptor\Encryptor();
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
                echo $cryptor->encrypt($insertcheck, $apikey);
                insertMobileLog('insert',$username,$query2,'','',"Insert New Option in $list_id List Screen",1);
            }else{
                $insertcheck = '[{"id":"0"}]';
                echo GibberishAES::enc($insertcheck, $apikey);
                insertMobileLog('insert',$username,$query2,'','',"Insert New Option in $list_id List Screen - Failed",0);
            }
        }else{
            $insertcheck = '{"error":{"text":"Duplicate Entry. Option '. $option_id .' already existed"},"value":"'. $option_id .'"}';
            echo $cryptor->encrypt($insertcheck, $apikey);
            insertMobileLog('insert',$username,$insertcheck,'','',"Insert New Option in $list_id List Screen - Duplicate",0);
        }
    }
    catch(PDOException $e)
    {

        $patientres =  '{"error":{"text":'. $e->getMessage() .'}}';
        echo $patientresult = $cryptor->encrypt($patientres, $apikey);
        insertMobileLog('insert',$username,$patientres,'','',"Insert New Option in $list_id List Screen - Query Failed",0);
    }  
}
//To get Issues
//function getIssues($patientId)
//{
//    $key = 'rotcoderaclla';
//    
//    $cryptor    = new \RNCryptor\Encryptor();
//    try 
//        {
//            $db = getConnection();    
//
//            //To get Medical Problems
//            $sql1 = "SELECT id,DATE_FORMAT( date, '%Y-%m-%d' ) as date,REPLACE(type,'_',' ') as type,title,begdate as Begin_Date,enddate as End_Date,returndate as Return_Date,occurrence,classification,referredby as Referred_By,extrainfo as Additional_Information,diagnosis,activity,comments,user,groupname,(select title from list_options where option_id = outcome and list_id = 'Outcome') as outcome,destination,reinjury_id,injury_part,injury_type,injury_grade,reaction,external_allergyid,erx_source,erx_uploaded,DATE_FORMAT( modifydate, '%Y-%m-%d' ) as modified_Date  FROM lists WHERE pid = :patientId AND type = 'medical_problem' ORDER BY - ISNULL( enddate ) , begdate DESC";
//
//            //To get Allergies
//            $sql2 = "SELECT id,DATE_FORMAT( date, '%Y-%m-%d' ) as date,REPLACE(type,'_',' ') as type,title,begdate as Begin_Date,enddate as End_Date,returndate as Return_Date,occurrence,classification,referredby as Referred_By,extrainfo as Additional_Information,diagnosis,activity,comments,user,groupname,(select title from list_options where option_id = outcome and list_id = 'Outcome') as outcome,destination,reinjury_id,injury_part,injury_type,injury_grade,reaction,external_allergyid,erx_source,erx_uploaded,DATE_FORMAT( modifydate, '%Y-%m-%d' ) as modified_Date  FROM lists WHERE pid = :patientId AND type = 'allergy' ORDER BY - ISNULL( enddate ) , begdate DESC";
//
//            //To get Medications
//            $sql3 = "SELECT id,DATE_FORMAT( date, '%Y-%m-%d' ) as date,REPLACE(type,'_',' ') as type,title,begdate as Begin_Date,enddate as End_Date,returndate as Return_Date,occurrence,classification,referredby as Referred_By,extrainfo as Additional_Information,diagnosis,activity,comments,user,groupname,(select title from list_options where option_id = outcome and list_id = 'Outcome') as outcome,destination,reinjury_id,injury_part,injury_type,injury_grade,reaction,external_allergyid,erx_source,erx_uploaded,DATE_FORMAT( modifydate, '%Y-%m-%d' ) as modified_Date  FROM lists WHERE pid = :patientId AND type = 'medication' ORDER BY - ISNULL( enddate ) , begdate DESC";
//
//            //To get Surgeries
//            $sql4 = "SELECT id,DATE_FORMAT( date, '%Y-%m-%d' ) as date,REPLACE(type,'_',' ') as type,title,begdate as Begin_Date,enddate as End_Date,returndate as Return_Date,occurrence,classification,referredby as Referred_By,extrainfo as Additional_Information,diagnosis,activity,comments,user,groupname,(select title from list_options where option_id = outcome and list_id = 'Outcome') as outcome,destination,reinjury_id,injury_part,injury_type,injury_grade,reaction,external_allergyid,erx_source,erx_uploaded,DATE_FORMAT( modifydate, '%Y-%m-%d' ) as modified_Date FROM lists WHERE pid = :patientId AND type = 'surgery' ORDER BY - ISNULL( enddate ) , begdate DESC";
//
//            //To get Dental Issues 
//            $sql5 = "SELECT id,DATE_FORMAT( date, '%Y-%m-%d' ) as date,REPLACE(type,'_',' ') as type,title,begdate as Begin_Date,enddate as End_Date,returndate as Return_Date,occurrence,classification,referredby,extrainfo,diagnosis,activity,comments,user,groupname,(select title from list_options where option_id = outcome and list_id = 'Outcome') as outcome,destination,reinjury_id,injury_part,injury_type,injury_grade,reaction,external_allergyid,erx_source,erx_uploaded,DATE_FORMAT( modifydate, '%Y-%m-%d' ) as modified_Date FROM lists WHERE pid = :patientId AND type = 'dental' ORDER BY - ISNULL( enddate ) , begdate DESC";
//
//            //To get Immunizations
//            $sql6 = "select i1.id as id,  'immunization' AS 
//                type , i1.immunization_id as immunization_id, i1.cvx_code as cvx_code, c.code_text_short as cvx_text, ".
//             " if (i1.administered_date, concat(i1.administered_date,' - '), substring(i1.note,1,20)) as immunization_date ".
//             " from immunizations i1 ".
//             " left join code_types ct on ct.ct_key = 'CVX' ".
//             " left join codes c on c.code_type = ct.ct_id AND i1.cvx_code = c.code ".
//             " where i1.patient_id = :patientId ".
//             " and i1.added_erroneously = 0".
//             " order by i1.administered_date desc";
//
//            //To get Prescriptions
//            $sql7 = "select id,filled_by_id,pharmacy_id,date_added,date_modified,(select CONCAT(fname, ' ',lname) as Provider from users where id=provider_id) as Provider,start_date,drug,drug_id,rxnorm_drugcode,form,dosage,quantity,size,unit,route,substitute,refills,per_refill,filled_date,medication,note,active,site,prescriptionguid,erx_source,erx_uploaded,drug_info_erx from prescriptions where patient_id=:patientId and active='1' ";
//            
//            //To get DME Issues 
//            $sql8 = "SELECT id,DATE_FORMAT( date, '%Y-%m-%d' ) as date,type,title,begdate as Begin_Date,enddate as End_Date,returndate as Return_Date,occurrence,classification,referredby,extrainfo,diagnosis,activity,comments,user,groupname,(select title from list_options where option_id = outcome and list_id = 'Outcome') as outcome,destination,reinjury_id,injury_part,injury_type,injury_grade,reaction,external_allergyid,erx_source,erx_uploaded,DATE_FORMAT( modifydate, '%Y-%m-%d' ) as modified_Date FROM lists WHERE pid = :patientId AND type = 'DME' ORDER BY - ISNULL( enddate ) , begdate DESC";
//
//            $demo1=sql_execute($patientId,$sql1);
//            $demo2=sql_execute($patientId,$sql2);
//            $demo3=sql_execute($patientId,$sql3);
//            $demo4=sql_execute($patientId,$sql4);
//            $demo5=sql_execute($patientId,$sql5);
//            $demo6=sql_execute($patientId,$sql6);
//            $demo7=sql_execute($patientId,$sql7);
//            $demo8=sql_execute($patientId,$sql8);
//
//            $newdemo1=encode_demo($demo1);
//            $newdemo2=encode_demo($demo2);  
//            $newdemo3=encode_demo($demo3);  
//            $newdemo4=encode_demo($demo4);              
//            $newdemo5=encode_demo($demo5);  
//            $newdemo6=encode_demo($demo6);  
////            $newdemo7=encode_demo($demo7,'');  
//            $newdemo8=encode_demo($demo8);  
//
//            $newdemo['Medical_Problems'] = check_data_available3($newdemo1,'medical_problem');
//            $newdemo['Allergies'] = check_data_available3($newdemo2,'allergy');
//            $newdemo['Medications'] = check_data_available3($newdemo3,'medication');
//            $newdemo['Surgeries'] = check_data_available3($newdemo4,'surgery');
//            $newdemo['Dental_Issues'] = check_data_available3($newdemo5,'dental');
//            $newdemo['Immunizations'] = check_data_available3($newdemo6,'immunization');
////            $newdemo['Prescriptions'] = check_data_available($newdemo7);
//            $newdemo['DME'] = check_data_available3($newdemo8,'DME');
//
//            $newdemores =  json_encode($newdemo);
//            echo $newdemoresult = $cryptor->encrypt($newdemores, $key);
//
//        } 
//        catch(PDOException $e) 
//        {
//            $newdemores = '{"error":{"text":'. $e->getMessage() .'}}';
//            echo $newdemoresult = $cryptor->encrypt($newdemores, $key);
//        }
//}
//
function getIssues($patientId)
{
    $key = 'rotcoderaclla';
    
    $cryptor    = new \RNCryptor\Encryptor();
    try 
        {
            $db = getConnection();    

            //To get Medical Problems
            $sql1 = "SELECT id,DATE_FORMAT( date, '%Y-%m-%d' ) as date,REPLACE(type,'_',' ') as type,title,begdate as Begin_Date,enddate as End_Date,returndate as Return_Date,(select title from list_options where option_id = occurrence and list_id = 'Occurrence') as occurrence,classification,referredby as Referred_By,extrainfo as Additional_Information,diagnosis,activity,comments,user,groupname,(select title from list_options where option_id = outcome and list_id = 'Outcome') as outcome,destination,reinjury_id,injury_part,injury_type,injury_grade,reaction,external_allergyid,erx_source,erx_uploaded,DATE_FORMAT( modifydate, '%Y-%m-%d' ) as modified_Date  FROM lists WHERE pid = :patientId AND type = 'medical_problem' ORDER BY - ISNULL( enddate ) , begdate DESC";

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
            //$sql7 = "select id,filled_by_id,pharmacy_id,date_added,date_modified,(select CONCAT(fname, ' ',lname) as Provider from users where id=provider_id) as Provider,start_date,drug,drug_id,rxnorm_drugcode,form,dosage,quantity,size,unit,route,substitute,refills,per_refill,filled_date,medication,note,active,site,prescriptionguid,erx_source,erx_uploaded,drug_info_erx from prescriptions where patient_id=:patientId and active='1' ";
            
            //To get DME Issues 
            $sql8 = "SELECT id,DATE_FORMAT( date, '%Y-%m-%d' ) as date,type,title,begdate as Begin_Date,enddate as End_Date,returndate as Return_Date,(select title from list_options where option_id = occurrence and list_id = 'Occurrence') as occurrence,classification,referredby,extrainfo,diagnosis,activity,comments,user,groupname,(select title from list_options where option_id = outcome and list_id = 'Outcome') as outcome,destination,reinjury_id,injury_part,injury_type,injury_grade,reaction,external_allergyid,erx_source,erx_uploaded,DATE_FORMAT( modifydate, '%Y-%m-%d' ) as modified_Date FROM lists WHERE pid = :patientId AND type = 'DME' ORDER BY - ISNULL( enddate ) , begdate DESC";

            $demo1 = sql_execute($patientId,$sql1);
            
            $pres = array();
            
            if(!empty($demo1)){
                for($i=0; $i< count($demo1); $i++){
                    $pres2 = array();
                    foreach($demo1[$i] as $pkey=> $pvalue){
                        $pres2[][$pkey] = $pvalue;
                    }
                    $pres[] = $pres2;
//                    $pres[][] = $demo1[$i];
                }
                $newdemo[]['Medical_Problems'] = $pres;
            }
            
            $demo2=sql_execute($patientId,$sql2);
            if(!empty($demo2)){
                $pres = array();
                for($i=0; $i< count($demo2); $i++){
                    $pres2 = array();
                    foreach($demo2[$i] as $pkey=> $pvalue){
                        $pres2[][$pkey] = $pvalue;
                    }
                    $pres[] = $pres2;
                }
                $newdemo[]['Allergies'] = $pres;
            }
            
            $demo3=sql_execute($patientId,$sql3);
            if(!empty($demo3)){
                $pres = array();
                for($i=0; $i< count($demo3); $i++){
                    $pres2 = array();
                    foreach($demo3[$i] as $pkey=> $pvalue){
                        $pres2[][$pkey] = $pvalue;
                    }
                    $pres[] = $pres2;
                }
                $newdemo[]['Medications'] = $pres;
            }
            
            $demo4=sql_execute($patientId,$sql4);
            if(!empty($demo4)){
                $pres = array();
                for($i=0; $i< count($demo4); $i++){
                    $pres2 = array();
                    foreach($demo4[$i] as $pkey=> $pvalue){
                        $pres2[][$pkey] = $pvalue;
                    }
                    $pres[] = $pres2;
                }
                $newdemo[]['Surgeries'] = $pres;
            }
            
            $demo5=sql_execute($patientId,$sql5);
            if(!empty($demo5)){
                $pres = array();
                for($i=0; $i< count($demo5); $i++){
                    $pres2 = array();
                    foreach($demo5[$i] as $pkey=> $pvalue){
                        $pres2[][$pkey] = $pvalue;
                    }
                    $pres[] = $pres2;
                }
                $newdemo[]['Dental_Issues'] = $pres;
            }
            
            $demo6=sql_execute($patientId,$sql6);
            if(!empty($demo6)){
                $pres = array();
                for($i=0; $i< count($demo6); $i++){
                    $pres2 = array();
                    foreach($demo6[$i] as $pkey=> $pvalue){
                        $pres2[][$pkey] = $pvalue;
                    }
                    $pres[] = $pres2;
                }
                $newdemo[]['Immunizations'] = $pres;
            }
            
            $demo8=sql_execute($patientId,$sql8);
            if(!empty($demo8)){
                $pres = array();
                for($i=0; $i< count($demo8); $i++){
                    $pres2 = array();
                    foreach($demo8[$i] as $pkey=> $pvalue){
                        $pres2[][$pkey] = $pvalue;
                    }
                    $pres[] = $pres2;
                }
                $newdemo[]['DME'] = $pres;
            }
            
        $newdemores =  json_encode($newdemo);
        echo $newdemoresult = $cryptor->encrypt($newdemores, $key);

        } 
        catch(PDOException $e) 
        {
            $newdemores = '{"error":{"text":'. $e->getMessage() .'}}';
            echo $newdemoresult = $cryptor->encrypt($newdemores, $key);
        }
}
//function getLayoutHistory($patientId){
//    try
//    {	
//        $data = '';
//        $newdemo = array();
//        $dataarray = array();
//        //$group_name = str_replace('_', ' ', $group_name2);
//        $db=getConnection();
//        $enckey = 'rotcoderaclla';
//        $cryptor    = new \RNCryptor\Encryptor();
//        $getgroupnames = "SELECT DISTINCT(group_name) as group_name from layout_options where form_id='HIS' and uor <> 0 order by group_name";
//        $db->query( "SET NAMES utf8"); 
//        $stmt = $db->prepare($getgroupnames) ;
//        $stmt->execute();                       
//        $setgroupnames2 = $stmt->fetchAll(PDO::FETCH_OBJ);  
//        foreach($setgroupnames2 as $setgroupnames){
//            $fieldnames = "select field_id, title,data_type, seq, list_id 
//                        from layout_options where form_id='HIS' and uor <> 0 AND group_name='".$setgroupnames->group_name."' order by seq";
//            $db->query( "SET NAMES utf8"); 
//            $stmt2 = $db->prepare($fieldnames); 
//            $stmt2->execute();
//            $fieldnamesresult = $stmt2->fetchAll(PDO::FETCH_OBJ);
//            //$fieldnameslist2 = '';
//            $data = array();
//            if(!empty($fieldnamesresult)){
//                foreach($fieldnamesresult as $fieldnamesarray){
//                    $listname = $fieldnamesarray->list_id;
//                    $gethistorydata = "SELECT `".$fieldnamesarray->field_id ."` FROM history_data WHERE pid = $patientId order by date desc limit 1";
//                    $db->query( "SET NAMES utf8"); 
//                    $stmt3 = $db->prepare($gethistorydata); 
//                    $stmt3->execute();
//                    $sethistorydata = $stmt3->fetchAll(PDO::FETCH_OBJ); 
//                    if($sethistorydata != ''){
//                        for($i=0; $i< count($sethistorydata); $i++){
//                            foreach($sethistorydata[$i] as $key => $sethis){ 
//                                if(!empty($sethis)){
//                                    if(!empty($fieldnamesarray->title)):
//                                        $title = $fieldnamesarray->title;
//                                    else:
//                                        $title = $fieldnamesarray->field_id;
//                                    endif;
//                                    if($fieldnamesarray->list_id != ''){
//                                        $examdata = '';
//                                        if($fieldnamesarray->data_type == 23  ){
//                                            if(!empty($sethis)){
//                                                $explodeval2 = explode('|', $sethis);
//                                                $explodelist2  = array();
//                                                $natype = $ntype = $atype = '';
//                                                for($i= 0; $i< count($explodeval2); $i++){
//                                                    $explodelist2 = explode(":", $explodeval2[$i]);
//                                                    $slashed_data  = addslashes($explodelist2[0]) ;
//                                                    $getvalname = "SELECT title FROM list_options WHERE option_id =  '$slashed_data' AND list_id = '$fieldnamesarray->list_id'";
//                                                    $db->query( "SET NAMES utf8"); 
//                                                    $stmt6 = $db->prepare($getvalname) ;
//                                                    $stmt6->execute();                       
//                                                    $setvalname2  = $stmt6->fetchAll(PDO::FETCH_OBJ);  
//                                                    foreach($setvalname2 as $setvalname){
//                                                        $text = '';
//                                                        $text = '';
//                                                        if($explodelist2[1] != 0 || !empty($explodelist2[2])){
//                                                            if(!empty($explodelist2[2])){
//                                                               $text =  " - (".$explodelist2[2].")";
//                                                            }
//                                                            if($explodelist2[1] == 1)
//                                                                $type = 'Normal';
//                                                            else
//                                                                $type = 'Abnormal';
//                                                            $data[substr($setgroupnames->group_name,1)][$setvalname->title] = $type.$text;
//                                                        }
//    //                                                    if($explodelist2[1] == 0){
//    //                                                        $type = 'N/A';
//    //                                                        if(!empty($explodelist2[2])){
//    //                                                           $text =  "(".$explodelist2[2].")";
//    //                                                        }
//    //                                                        $natype .= $setvalname->title.$text.",";
//    //                                                    }elseif($explodelist2[1] == 1){
//    //                                                        $type = 'Normal';
//    //                                                        if(!empty($explodelist2[2])){
//    //                                                           $text =  "(".$explodelist2[2].")";
//    //                                                        }
//    //                                                        $ntype .= $setvalname->title.$text.",";
//    //                                                    }elseif($explodelist2[1] == 2){
//    //                                                        $type = 'Abnormal';
//    //                                                        if(!empty($explodelist2[2])){
//    //                                                           $text =  "(".$explodelist2[2].")";
//    //                                                        }
//    //                                                        $atype .= $setvalname->title.$text.",";
//    //                                                    }
//                                                    }
//                                                }
//    //                                            $data[substr($setgroupnames->group_name,1)]['N/A'] = rtrim($natype, ',');
//    //                                            $data[substr($setgroupnames->group_name,1)]['Normal'] = rtrim($ntype, ',');
//    //                                            $data[substr($setgroupnames->group_name,1)]['Abnormal'] = rtrim($atype, ',');
//                                            }else{
//                                                $data[substr($setgroupnames->group_name,1)][$title] = $sethis;
//                                            }    
//                                        }elseif($fieldnamesarray->data_type == 32  ){
//                                            if(!empty($sethis)){
//                                                $titlefield = '';
//                                                $explodeval = explode("|", $sethis);
//                                                $title = '';
//                                                if(!empty($explodeval[3])){
//                                                    $slashes_value = addslashes($explodeval[3]);
//                                                    $getvalname = "SELECT title FROM list_options WHERE option_id =  '$slashes_value' AND list_id = '$fieldnamesarray->list_id'";
//                                                    $db->query( "SET NAMES utf8"); 
//                                                    $stmt6 = $db->prepare($getvalname) ;
//                                                    $stmt6->execute();                       
//                                                    $setvalname2  = $stmt6->fetchAll(PDO::FETCH_OBJ);
//                                                    foreach($setvalname2 as $setvalname){
//                                                        $titlefield = $setvalname->title."&nbsp;";
//                                                    }
//                                                }
//                                                $statustypes = array('current'=>'Current','quit' =>'Quit','never' => 'Never','not_applicable' => 'N/A');
//                                                $statusname = '';
//                                                foreach($statustypes as $key => $stype){
//                                                    if($explodeval[1] == $key.$fieldnamesarray->field_id):
//                                                        $statusname = $stype;
//                                                    endif;
//                                                }
//                                                $smokingdata = $titlefield.$explodeval[0].str_repeat('&nbsp;', 5)."Status: ".$statusname. "  ".$explodeval[2];
//                                                if(empty($titlefield))
//                                                    $data[substr($setgroupnames->group_name,1)][$fieldnamesarray->title] = $smokingdata;
//                                                else
//                                                    $data[substr($setgroupnames->group_name,1)][$titlefield] = $smokingdata;
//                                            }else{
//                                                $data[substr($setgroupnames->group_name,1)][$title] = $sethis;
//                                            }    
//                                        }elseif($fieldnamesarray->data_type == 22 ){
//                                            $smokingdata = '';
//                                            if(!empty($sethis)){
//                                                $titlefield = '';
//                                                $explodeval = explode("|", $sethis);
//                                                foreach($explodeval as $tlist){
//                                                    $explodeval = explode(":", $tlist);
//                                                    $title = '';
//                                                    if(!empty($explodeval[1])){
//                                                        $slashes_value = addslashes($explodeval[0]);
//                                                        $getvalname = "SELECT title FROM list_options WHERE option_id =  '$slashes_value' AND list_id = '$fieldnamesarray->list_id'";
//                                                        $db->query( "SET NAMES utf8"); 
//                                                        $stmt6 = $db->prepare($getvalname) ;
//                                                        $stmt6->execute();                       
//                                                        $setvalname2  = $stmt6->fetchAll(PDO::FETCH_OBJ);
//                                                        foreach($setvalname2 as $setvalname){
//                                                            $titlefield = $setvalname->title."&nbsp;";
//                                                        }
//                                                    }
//                                                    if(!empty($titlefield))
//                                                        $smokingdata .= "<b>".$titlefield.str_repeat('&nbsp;', 2).":</b>  ".$explodeval[1]."<br />";
//                                                }
//                                                $data[substr($setgroupnames->group_name,1)][$fieldnamesarray->title] = $smokingdata;
//                                            }else{
//                                                $data[substr($setgroupnames->group_name,1)][$title] = $sethis;
//                                            }    
//                                        }elseif($fieldnamesarray->data_type == 25 ){
//                                            $smokingdata = '';
//                                            if(!empty($sethis)){
//                                                $titlefield = '';
//                                                $explodeval = explode("|", $sethis);
//                                                foreach($explodeval as $tlist){
//                                                    $checkstring = '';
//                                                    $explodeval = explode(":", $tlist);
//                                                    $title = '';
//                                                    if(!empty($explodeval[1]) || !empty($explodeval[2])){
//                                                        $slashes_value = addslashes($explodeval[0]);
//                                                        $getvalname = "SELECT title FROM list_options WHERE option_id =  '$slashes_value' AND list_id = '$fieldnamesarray->list_id'";
//                                                        $db->query( "SET NAMES utf8"); 
//                                                        $stmt6 = $db->prepare($getvalname) ;
//                                                        $stmt6->execute();                       
//                                                        $setvalname2  = $stmt6->fetchAll(PDO::FETCH_OBJ);
//                                                        foreach($setvalname2 as $setvalname){
//                                                            $titlefield = $setvalname->title."&nbsp;";
//                                                        }
//                                                    }
//                                                    if($explodeval[1] == 1)
//                                                        $isyes = 'YES';
//                                                    else
//                                                        $isyes = 'NO';
//                                                    if(!empty($explodeval[2]) )
//                                                        $checkstring = $isyes. " " . $explodeval[2]."<br />";
//                                                    else if(empty($explodeval[2]) && $isyes == "YES" )
//                                                        $checkstring = $isyes. "<br />";
//                                                    if(!empty($checkstring))
//                                                        $smokingdata .= "<b>".$titlefield.str_repeat('&nbsp;', 2).":</b>".$checkstring; 
//                                                }
//                                                $data[substr($setgroupnames->group_name,1)][$fieldnamesarray->title] = $smokingdata;
//                                            }else{
//                                                $data[substr($setgroupnames->group_name,1)][$title] = $sethis;
//                                            }    
//                                        }else{
//                                            if(!empty($sethis)){
//                                                $explodeval = explode("|", $sethis);
//                                                $arraydata = '';
//                                                for($i=0; $i< count($explodeval); $i++){
//                                                    $slashed = addslashes($explodeval[$i]);
//                                                    $getvalname = "SELECT title FROM list_options WHERE option_id =  '$slashed' AND list_id = '$fieldnamesarray->list_id'";
//                                                    $db->query( "SET NAMES utf8"); 
//                                                    $stmt6 = $db->prepare($getvalname) ;
//                                                    $stmt6->execute();                       
//                                                    $setvalname2  = $stmt6->fetchAll(PDO::FETCH_OBJ);
//                                                    if(!empty($setvalname2))
//                                                        $arraydata .= $setvalname2[0]->title.",";
//                                                }    
//
//                                                $data[substr($setgroupnames->group_name,1)][$fieldnamesarray->title] = rtrim($arraydata,',');
//                                            }else{
//                                                $data[substr($setgroupnames->group_name,1)][$title] = $sethis;
//                                            }   
//                                        }
//                                    }else{ 
//                                        if($fieldnamesarray->data_type == 28){
//                                            if(!empty($sethis)){
//                                                $explodeval = explode("|", $sethis);
//                                                $statusname = '';
//                                                $statustypes = array('current'=>'Current','quit' =>'Quit','never' => 'Never','not_applicable' => 'N/A');
//                                                foreach($statustypes as $key => $stype){
//                                                    $exp1 = isset($explodeval[1])? $explodeval[1]: '';
//                                                    if($exp1 == $key.$fieldnamesarray->field_id):
//                                                        $statusname = $stype;
//                                                    endif;
//                                                }
//                                                $exp0 = isset($explodeval[0])? $explodeval[0]: '';
//                                                $exp2 = isset($explodeval[2])? $explodeval[2] : '';
//                                                $smokingdata = $exp0.str_repeat('&nbsp;', 5)."Status: ".$statusname. "  ".$exp2;
//                                                $data[substr($setgroupnames->group_name,1)][$fieldnamesarray->title] = $smokingdata;
//                                            }else{
//                                                $data[substr($setgroupnames->group_name,1)][$title] = $sethis;
//                                            }      
//                                        }elseif($fieldnamesarray->data_type == 10 || $fieldnamesarray->data_type == 11 || $fieldnamesarray->data_type == 38  ){
//                                            if(!empty($sethis)){
//                                                $getprovidername = "SELECT CONCAT(fname,' ',lname) as name FROM users WHERE id = $sethis";
//                                                $db->query( "SET NAMES utf8"); 
//                                                $stmt6 = $db->prepare($getprovidername) ;
//                                                $stmt6->execute();                       
//                                                $setprovidername2  = $stmt6->fetchAll(PDO::FETCH_OBJ);
//                                                $providername = $setprovidername2[0]->name;
//                                                $data[substr($setgroupnames->group_name,1)][$title] = ucwords($providername);
//                                            }else{
//                                                $data[substr($setgroupnames->group_name,1)][$title] = ucwords($sethis);
//                                            }    
//                                        }elseif($fieldnamesarray->data_type == 14 ){
//                                            if(!empty($sethis)){
//                                                $getprovidername = "SELECT  CONCAT(fname,' ',lname) as name, username, organization FROM users WHERE id = $sethis";
//                                                $db->query( "SET NAMES utf8"); 
//                                                $stmt6 = $db->prepare($getprovidername) ;
//                                                $stmt6->execute();                       
//                                                $setprovidername2  = $stmt6->fetchAll(PDO::FETCH_OBJ);
//                                                $title2 = '';
//                                                if(!empty($setprovidername2)){
//                                                    if (empty($setprovidername2[0]->username) ) {
//                                                       $title2 = $setprovidername2[0]->organization;
//                                                    }else{
//                                                        $title2 = $setprovidername2[0]->name;
//                                                    }
//                                                }
//                                                $data[substr($setgroupnames->group_name,1)][$title] = ucwords($title2);
//                                            }else{
//                                                $data[substr($setgroupnames->group_name,1)][$title] = ucwords($sethis);
//                                            }    
//                                        }elseif($fieldnamesarray->data_type == 12 ){
//                                            if(!empty($sethis)){
//                                                $getpharmacyname = "SELECT  name FROM pharmacies WHERE id = $sethis";
//                                                $db->query( "SET NAMES utf8"); 
//                                                $stmt6 = $db->prepare($getpharmacyname) ;
//                                                $stmt6->execute();                       
//                                                $setpharmacyname  = $stmt6->fetchAll(PDO::FETCH_OBJ);
//                                                $pharmacyname = $setpharmacyname[0]->name;
//                                                $data[substr($setgroupnames->group_name,1)][$title] = ucwords($pharmacyname);
//                                            }else{
//                                                $data[substr($setgroupnames->group_name,1)][$title] = ucwords($sethis);
//                                            }    
//                                        }elseif($fieldnamesarray->data_type == 37 ){
//                                            if(!empty($sethis)){
//                                                $getinsurancename = "SELECT name FROM insurance_companies WHERE id = $sethis";
//                                                $db->query( "SET NAMES utf8"); 
//                                                $stmt6 = $db->prepare($getinsurancename) ;
//                                                $stmt6->execute();                       
//                                                $setinsurancename  = $stmt6->fetchAll(PDO::FETCH_OBJ);
//                                                $insurancename = $setinsurancename[0]->name;
//                                                $data[substr($setgroupnames->group_name,1)][$title] = ucwords($insurancename);
//                                            }else{
//                                                $data[substr($setgroupnames->group_name,1)][$title] = ucwords($sethis);
//                                            }    
//                                        }elseif($fieldnamesarray->data_type == 35 ){
//                                            if(!empty($sethis)){
//                                                $getfacilityname = "SELECT name FROM facility WHERE id = $sethis";
//                                                $db->query( "SET NAMES utf8"); 
//                                                $stmt6 = $db->prepare($getfacilityname) ;
//                                                $stmt6->execute();                       
//                                                $setfacilityname  = $stmt6->fetchAll(PDO::FETCH_OBJ);
//                                                $facilityname = $setfacilityname[0]->name;
//                                                $data[substr($setgroupnames->group_name,1)][$title] = ucwords($facilityname);
//                                            }else{
//                                                $data[substr($setgroupnames->group_name,1)][$title] = ucwords($sethis);
//                                            }    
//                                        }else{
//                                            $data[substr($setgroupnames->group_name,1)][$title] = $sethis;
//                                        } 
//                                    }
//                                } 
//                            }    
//                        }   
//                    }
//                    $listname  = '';
//                }
//            }
//            $dcheck[] = $data;
//        }
////        echo "<pre>"; print_r($data);echo "</pre>";
////        $newdemo1 = encode_demo($data);  
////        $newdemo['HistoryData'] = check_data_available($newdemo1);
//        //exit();
//        if($dcheck){
//           $hisres = json_encode($dcheck);
//           echo $historyresult =  $cryptor->encrypt($hisres, $enckey);
//        }else{
//            $hisres = '[{"id":"0"}]';
//            echo $historyresult =  $cryptor->encrypt($hisres, $enckey);
//        }
//    }catch(PDOException $e)
//    {
//        $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
//        echo $historyresult =  $cryptor->encrypt($error, $enckey);
//    }   
//}
//
function getLayoutHistory($patientId){
    try
    {	
        $data = '';
        $newdemo = array();
        $dataarray = array();
        //$group_name = str_replace('_', ' ', $group_name2);
        $db=getConnection();
        $enckey = 'rotcoderaclla';
        $cryptor    = new \RNCryptor\Encryptor();
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
            $data = array();
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
                                                        $text = '';
                                                        if($explodelist2[1] != 0 || !empty($explodelist2[2])){
                                                            if(!empty($explodelist2[2])){
                                                               $text =  " - (".$explodelist2[2].")";
                                                            }
                                                            if($explodelist2[1] == 1)
                                                                $type = 'Normal';
                                                            else
                                                                $type = 'Abnormal';
                                                            $data[substr($setgroupnames->group_name,1)][][$setvalname->title] = $type.$text;
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
                                                $data[substr($setgroupnames->group_name,1)][][$title] = $sethis;
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
                                                    $data[substr($setgroupnames->group_name,1)][][$fieldnamesarray->title] = $smokingdata;
                                                else
                                                    $data[substr($setgroupnames->group_name,1)][][$titlefield] = $smokingdata;
                                            }else{
                                                $data[substr($setgroupnames->group_name,1)][][$title] = $sethis;
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
                                                $data[substr($setgroupnames->group_name,1)][][$fieldnamesarray->title] = $smokingdata;
                                            }else{
                                                $data[substr($setgroupnames->group_name,1)][][$title] = $sethis; 
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
                                                $data[substr($setgroupnames->group_name,1)][][$fieldnamesarray->title] = $smokingdata;
                                            }else{
                                                $data[substr($setgroupnames->group_name,1)][][$title] = $sethis;
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

                                                $data[substr($setgroupnames->group_name,1)][][$fieldnamesarray->title] = rtrim($arraydata,',');
                                            }else{
                                                $data[substr($setgroupnames->group_name,1)][][$title] = $sethis;
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
                                                $data[substr($setgroupnames->group_name,1)][][$fieldnamesarray->title] = $smokingdata;
                                            }else{
                                                $data[substr($setgroupnames->group_name,1)][][$title] = $sethis;
                                            }      
                                        }elseif($fieldnamesarray->data_type == 10 || $fieldnamesarray->data_type == 11 || $fieldnamesarray->data_type == 38  ){
                                            if(!empty($sethis)){
                                                $getprovidername = "SELECT CONCAT(fname,' ',lname) as name FROM users WHERE id = $sethis";
                                                $db->query( "SET NAMES utf8"); 
                                                $stmt6 = $db->prepare($getprovidername) ;
                                                $stmt6->execute();                       
                                                $setprovidername2  = $stmt6->fetchAll(PDO::FETCH_OBJ);
                                                $providername = $setprovidername2[0]->name;
                                                $data[substr($setgroupnames->group_name,1)][][$title] = ucwords($providername);
                                            }else{
                                                $data[substr($setgroupnames->group_name,1)][][$title] = ucwords($sethis);
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
                                                $data[substr($setgroupnames->group_name,1)][][$title] = ucwords($title2);
                                            }else{
                                                $data[substr($setgroupnames->group_name,1)][][$title] = ucwords($sethis);
                                            }    
                                        }elseif($fieldnamesarray->data_type == 12 ){
                                            if(!empty($sethis)){
                                                $getpharmacyname = "SELECT  name FROM pharmacies WHERE id = $sethis";
                                                $db->query( "SET NAMES utf8"); 
                                                $stmt6 = $db->prepare($getpharmacyname) ;
                                                $stmt6->execute();                       
                                                $setpharmacyname  = $stmt6->fetchAll(PDO::FETCH_OBJ);
                                                $pharmacyname = $setpharmacyname[0]->name;
                                                $data[substr($setgroupnames->group_name,1)][][$title] = ucwords($pharmacyname);
                                            }else{
                                                $data[substr($setgroupnames->group_name,1)][][$title] = ucwords($sethis);
                                            }    
                                        }elseif($fieldnamesarray->data_type == 37 ){
                                            if(!empty($sethis)){
                                                $getinsurancename = "SELECT name FROM insurance_companies WHERE id = $sethis";
                                                $db->query( "SET NAMES utf8"); 
                                                $stmt6 = $db->prepare($getinsurancename) ;
                                                $stmt6->execute();                       
                                                $setinsurancename  = $stmt6->fetchAll(PDO::FETCH_OBJ);
                                                $insurancename = $setinsurancename[0]->name;
                                                $data[substr($setgroupnames->group_name,1)][][$title] = ucwords($insurancename);
                                            }else{
                                                $data[substr($setgroupnames->group_name,1)][][$title] = ucwords($sethis);
                                            }    
                                        }elseif($fieldnamesarray->data_type == 35 ){
                                            if(!empty($sethis)){
                                                $getfacilityname = "SELECT name FROM facility WHERE id = $sethis";
                                                $db->query( "SET NAMES utf8"); 
                                                $stmt6 = $db->prepare($getfacilityname) ;
                                                $stmt6->execute();                       
                                                $setfacilityname  = $stmt6->fetchAll(PDO::FETCH_OBJ);
                                                $facilityname = $setfacilityname[0]->name;
                                                $data[substr($setgroupnames->group_name,1)][][$title] = ucwords($facilityname);
                                            }else{
                                                $data[substr($setgroupnames->group_name,1)][][$title] = ucwords($sethis);
                                            }    
                                        }else{
                                            $data[substr($setgroupnames->group_name,1)][][$title] = $sethis;
                                        } 
                                    }
                                } 
                            }    
                        }   
                    }
                    $listname  = '';
                    
                }
                
            }
            if(!empty($data))
                $dcheck[] = $data;
        }
//        echo "<pre>"; print_r($dcheck);echo "</pre>";
//        $newdemo1 = encode_demo($dcheck);  
//        $newdemo['HistoryData'] = check_data_available($newdemo1);
        //exit();
        if($dcheck){
           $hisres = json_encode($dcheck);
           echo $historyresult =  $cryptor->encrypt($hisres, $enckey);
        }else{
            $hisres = '[{"id":"0"}]';
            echo $historyresult =  $cryptor->encrypt($hisres, $enckey);
        }
    }catch(PDOException $e)
    {
        $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo $historyresult =  $cryptor->encrypt($error, $enckey);
    }   
}  
// method to get prescriptions
 function getPrescriptions($patientId)
{
    try 
    {
        $db = getConnection();  
        $key = 'rotcoderaclla';
        $cryptor    = new \RNCryptor\Encryptor();
        $pres = array();
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
        $presclist = $stmt->fetchAll(PDO::FETCH_ASSOC); 
        if(!empty($presclist)){
            for($i=0; $i< count($presclist); $i++){
                $pres2 = array();
                foreach($presclist[$i] as $pkey=> $pvalue){
                    if(trim($pvalue) != '' )
                        $pres2[][$pkey] = $pvalue;
                }
                $pres[] = $pres2;
            }
        }
//         echo "<pre>"; print_r($pres);echo "</pre>";
        if($pres)
        {
            $presres = json_encode($pres); 
            echo $presresult = $cryptor->encrypt($presres, $key);
            
        }
        else
        {
            $presres = '[{"DataAvailable":"No"}]';
            echo $presresult = $cryptor->encrypt($presres, $key);
        }
                
    }catch(PDOException $e)
    {
        $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo $presresult = $cryptor->encrypt($error, $key);
    }    
}
function getDynamicPatientAgencies($pid){
    $key = 'rotcoderaclla';
    $cryptor    = new \RNCryptor\Encryptor();
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
                            $fields_array[$i][][$title] = $set_agency[0]->Organization;
                            $fields_array[$i][]['Name'] = $set_agency[0]->name;
                        }
                    }elseif($a_key == 'id'){
                        $fields_array[$i][]['id'] = $a_value;
                    }else{
                        $fields_array[$i][][$title] = $a_value;
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
                        $fields_array[$i][][$ukey] = $uvalue;
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
                                        $fields_array[$i][][$set_check_list[0]->title] = $smokingdata;
                                    }else{
                                        $fields_array[$i][][ucwords($ckey)] = $cvalue;
                                    }    
                                }elseif($set_check_list[0]->data_type == 10 || $set_check_list[0]->data_type == 11 || $set_check_list[0]->data_type == 38  ){
                                    if(!empty($cvalue)){
                                        $getprovidername = "SELECT  CONCAT(fname,' ',lname) as name FROM users WHERE id = $cvalue";
                                        $stmt6 = $db->prepare($getprovidername) ;
                                        $stmt6->execute();                       
                                        $setprovidername2  = $stmt6->fetchAll(PDO::FETCH_OBJ);
                                        $providername = $setprovidername2[0]->name;
                                        $fields_array[$i][][ucwords($ckey)] = ucwords($providername);
                                    }else{
                                        $fields_array[$i][][ucwords($ckey)] = ucwords($cvalue);
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
                                        $fields_array[$i][][ucwords($ckey)] = ucwords($title2);
                                    }else{
                                        $fields_array[$i][][ucwords($ckey)] = ucwords($cvalue);
                                    }    
                                }elseif($set_check_list[0]->data_type == 12 ){
                                    if(!empty($cvalue)){
                                        $getpharmacyname = "SELECT  name FROM pharmacies WHERE id = $cvalue";
                                        $stmt6 = $db->prepare($getpharmacyname) ;
                                        $stmt6->execute();                       
                                        $setpharmacyname  = $stmt6->fetchAll(PDO::FETCH_OBJ);
                                        $pharmacyname = $setpharmacyname[0]->name;
                                        $fields_array[$i][][ucwords($ckey)] = ucwords($pharmacyname);
                                    }else{
                                        $fields_array[$i][][ucwords($ckey)] = ucwords($cvalue);
                                    }    
                                }elseif($set_check_list[0]->data_type == 37 ){
                                    if(!empty($cvalue)){
                                        $getinsurancename = "SELECT  name FROM insurance_companies WHERE id = $cvalue";
                                        $stmt6 = $db->prepare($getinsurancename) ;
                                        $stmt6->execute();                       
                                        $setinsurancename  = $stmt6->fetchAll(PDO::FETCH_OBJ);
                                        $insurancename = $setinsurancename[0]->name;
                                        $fields_array[$i][][ucwords($ckey)] = ucwords($insurancename);
                                    }else{
                                        $fields_array[$i][][ucwords($ckey)] = ucwords($cvalue);
                                    }    
                                }elseif($set_check_list[0]->data_type == 35 ){
                                    if(!empty($cvalue)){
                                        $getfacilityname = "SELECT  name FROM facility WHERE id = $cvalue";
                                        $stmt6 = $db->prepare($getfacilityname) ;
                                        $stmt6->execute();                       
                                        $setfacilityname  = $stmt6->fetchAll(PDO::FETCH_OBJ);
                                        $facilityname = $setfacilityname[0]->name;
                                        $fields_array[$i][][ucwords($ckey)] = ucwords($facilityname);
                                    }else{
                                        $fields_array[$i][][ucwords($ckey)] = ucwords($cvalue);
                                    }    
                                }else{
                                    $fields_array[$i][][ucwords($ckey)] = $cvalue;
                                } 
                            }else{
                                $fields_array[$i][][ucwords($ckey)] = $cvalue;   
                            }
                        }
                    }
                }
                $i++;
            }
//            if(!empty($fields_array))
//                $dcheck[] = $fields_array;
        }
//        echo "<pre>"; print_r($fields_array); echo"</pre>";
        if(!empty($fields_array)) {
            
            $agencydatares = json_encode($fields_array); 
            echo $agencydataresult = $cryptor->encrypt($agencydatares, $key);
        }else{
            //echo 'No Agencies related to patient';
             $agencydatares = '[{"id":"0","OrganizationId":"0","Organization":"","Name":"","Addressbook_type":"","Abook_type_value":"","Workphone":"","Phonecell":"","Fax":"","Email":"","Street":"","City":"","State":"","Zip":""}]';
             echo $agencydataresult = $cryptor->encrypt($agencydatares, $key);
        }
       // echo "<pre>"; print_r($fields_array); echo "</pre>";
    } catch (Exception $ex) {
        $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo $agencydataresult = $cryptor->encrypt($error, $key);   
    }
}
// method to get list of encounters for given provider
function getEncounterList($loginProvderId,$patientId,$mode)
{
        $apikey = 'rotcoderaclla';
        $cryptor    = new \RNCryptor\Encryptor();
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
                echo $encountersresult = $cryptor->encrypt($encountersres, $apikey);
            }
            else
            {    
                $encountersres = '[{"id":"0"}]';
                echo $encountersresult = $cryptor->encrypt($encountersres, $apikey);
            }
        } 
        catch(PDOException $e) 
        {
            
            $error =  '{"error":{"text":'. $e->getMessage() .'}}'; 
            echo $encountersresult = $cryptor->encrypt($error, $apikey);
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
//                            $encounterslist[$i]->Codes  .= "&lt;b&gt;&lt;u&gt;".$value['code_type']."-".$key."&lt;/u&gt; Chg:&lt;/b&gt;".($value['chg']+$adj).",&lt;b&gt; Paid:&lt;/b&gt;".($value['chg']-$value['bal']). ",&lt;b&gt; Paid:&lt;/b&gt;".$adj.", &lt;b&gt;Balance : &lt;/b&gt;".$value['bal']."&lt;br&gt;";
                        }
                        unset($encounterslist[$i]->code);
                    }
                }
//                 echo "<pre>";print_r($encounterslist);echo "</pre>";
                // hema

                if($encounterslist)
                {
                   $encountersres = json_encode($encounterslist); 
                   echo $encountersresult = $cryptor->encrypt($encountersres, $apikey);

                }
                else
                {    
                    $res = '[{"id":"0"}]';
                    echo $encountersresult = $cryptor->encrypt($res, $apikey);
                }
            } 
            catch(PDOException $e) 
            {
                $error = '{"error":{"text":'. $e->getMessage() .'}}';
                echo $encountersresult = $cryptor->encrypt($error, $apikey);
            }
	}
}
//function getPatientInsuranceDataList($pid){
//    $key = 'rotcoderaclla';
//    $cryptor    = new \RNCryptor\Encryptor();
//    $sql = "SELECT i.id, i.type as Insurance_Type,(SELECT CONCAT(title, fname,  ' ', lname ) AS name FROM users WHERE id= i.provider) as Provider,(select name from insurance_companies where id = i.provider )as Insurance_Company_Name,i.group_number as Group_Number,i.plan_name as Plan_Name,i.policy_number as Policy_Number,CONCAT(i.subscriber_lname,' ',i.subscriber_mname,' ', i.subscriber_fname) as Subscriber_Name,i.subscriber_relationship as Subscriber_Relationship,
//            i.subscriber_ss as Subscriber_SS,i.subscriber_DOB as Subscriber_DOB,i.subscriber_street as Subscriber_Street,i.subscriber_postal_code as Subscriber_Postal_Code,i.subscriber_city as Subscriber_City, i.subscriber_state as Subscriber_State, i.subscriber_country as Subscriber_Country,
//            i.subscriber_employer as Subscriber_Employer,i.subscriber_employer_street as Subscriber_Employer_Street,i.subscriber_employer_postal_code as Subscriber_Employer_Postal_Code,i.subscriber_employer_state as Subscriber_Employer_State,i.subscriber_employer_country as Subscriber_Employer_Country, i.subscriber_employer_city as Subscriber_Employer_City,
//            i.copay as Copay,i.date as Date, i.subscriber_sex as Subscriber_Sex,i.accept_assignment as Accept_Assignment,
//            CASE i.policy_type
//                WHEN ''   THEN 'N/A'
//                WHEN 12 THEN 'Working Aged Beneficiary or Spouse with Employer Group Health Plan'
//                WHEN 13 THEN 'End-Stage Renal Disease Beneficiary in MCP with Employer`s Group Plan'
//                WHEN 14 THEN 'No-fault Insurance including Auto is Primary'
//                WHEN 15 THEN 'Worker`s Compensation'
//                WHEN 16 THEN 'Public Health Service (PHS) or Other Federal Agency'
//                WHEN 41 THEN 'Black Lung'
//                WHEN 42 THEN 'Veteran`s Administration'
//                WHEN 43 THEN 'Disabled Beneficiary Under Age 65 with Large Group Health Plan (LGHP)'
//                WHEN 47 THEN 'Other Liability Insurance is Primary'
//            END as Policy_Type
//            from insurance_data i 
//            where pid=:pid";
//	 
//	try 
//        {
//            $db = getConnection();
//            $stmt = $db->prepare($sql) ;
//            $db->query( "SET NAMES utf8");
//            $stmt->bindParam("pid", $pid);            
//            $stmt->execute();                       
//            $insurancedata2 = array();   
//            $insurancedata = $stmt->fetchAll(PDO::FETCH_OBJ);                        
//            for($i=0; $i< count($insurancedata); $i++){
//                foreach ($insurancedata[$i] as $key1 => $value1){
//                    $key2 = str_replace('_', ' ', $key1);
//                    $insurancedata2[$i][$key2] = $value1;
//                }
//            }
//            if($insurancedata2)
//            {
//                //returns patientdata 
//                $insurancedatares = json_encode($insurancedata2); 
//                echo $insurancedataresult = $cryptor->encrypt($insurancedatares, $key);
//            }
//            else
//            {
//                $insurancedatares  = '[{"id":"0","Insurance Type":"primary"},{"id":"0","Insurance Type":"secondary"},{"id":"0","Insurance Type":"tertiary"}]';
//                echo $insurancedataresult = $cryptor->encrypt($insurancedatares, $key);
//            }
//        } 
//        catch(PDOException $e) 
//        {
//            
//            $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
//            echo $insurancedataresult = $cryptor->encrypt($error, $key);
//        }
//
//}
function getPatientInsuranceDataList($pid){
    $key = 'rotcoderaclla';
    $cryptor    = new \RNCryptor\Encryptor();
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
            $insurancedatalist = array();
            $stmt = $db->prepare($sql) ;
            $db->query( "SET NAMES utf8");
            $stmt->bindParam("pid", $pid);            
            $stmt->execute();                       
            $insurancedata2 = array();   
            $insurancedata = $stmt->fetchAll(PDO::FETCH_ASSOC);                        
            for($i=0; $i< count($insurancedata); $i++){
                $insurancedata2 = array();
                foreach ($insurancedata[$i] as $key1 => $value1){
                    $key2 = str_replace('_', ' ', $key1);
                    if(trim($value1) != '' )
                        $insurancedata2[][$key2] = $value1;
                }
                $insurancedatalist[] = $insurancedata2;
            }
//            echo "<pre>"; print_r($insurancedatalist); echo "</pre>";
            if($insurancedatalist)
            {
                //returns patientdata 
                $insurancedatares = json_encode($insurancedatalist); 
                echo $insurancedataresult = $cryptor->encrypt($insurancedatares, $key);
            }
            else
            {
                $insurancedatares  = '[{"id":"0","Insurance Type":"primary"},{"id":"0","Insurance Type":"secondary"},{"id":"0","Insurance Type":"tertiary"}]';
                echo $insurancedataresult = $cryptor->encrypt($insurancedatares, $key);
            }
        } 
        catch(PDOException $e) 
        {
            
            $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
            echo $insurancedataresult = $cryptor->encrypt($error, $key);
        }

}
function getDemographicsBilling($pid, $with_insurance=false)
{
    try
    {
        $db = getConnection();
        $key = 'rotcoderaclla';
        $cryptor    = new \RNCryptor\Encryptor();
        
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
//         $bilres = '[{"id":"1","Patient_Balance_Due":"'.$patientbalance.'","Insurance_Balance_Due":"'.$insurnace_balance.'","Total_Balance_Due":"'.$totalbalance.'","Primary_Insurance":"'.$primary_insurance.'","Effective_Date":"'.$effective_date.'"}]';    
          $bilres = '[{"id":"1"},{"Patient_Balance_Due":"'.$patientbalance.'"},{"Insurance_Balance_Due":"'.$insurnace_balance.'"},{"Total_Balance_Due":"'.$totalbalance.'"},{"Primary_Insurance":"'.$primary_insurance.'"},{"Effective_Date":"'.$effective_date.'"}]';    
        echo $bilresult = $cryptor->encrypt($bilres, $key);
          
    }
    catch(PDOException $e) 
    {
        $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo $bilresult = $cryptor->encrypt($error, $key);
    }
}
// incomplete encounter count
function getIncompleteEncounterCount($providerid){
               
	try 
        {
            $db = getConnection();
            $key = 'rotcoderaclla';
            $cryptor    = new \RNCryptor\Encryptor();
            $array = array();
            $count= '';
            $array_res = array();
            // get visit_categories list 
            $get_fuv = "SELECT facilities, visit_categories FROM tbl_allcare_facuservisit WHERE  `users`  REGEXP ('".":\"".$providerid."\"')";
            $fuv_stmt = $db->prepare($get_fuv) ;
            $fuv_stmt->execute();
            $set_fuv = $fuv_stmt->fetchAll(PDO::FETCH_OBJ); 
            for($i = 0; $i<count($set_fuv); $i++){
               $array[]     =  unserialize( $set_fuv[$i]->visit_categories);
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
//                if(!empty($count)){
//                    $j = 0;
//                    for($i = 1, $c = floor($count[0]-> count/1000); $i <= $c; $i++) 
//                    { 
//                        $j = $i ;
//                    }
//                    if($count[0]-> count >999)
//                        $count[0]-> count = $j."K+";
//                    if($count[0]-> count >9999)
//                        $count[0]-> count = "9K+";
//                    
//                }
            }
            if($count)
            {
                //returns count 
                $countres = json_encode($count); 
                echo $countresult = $cryptor->encrypt($countres, $key);
            }
            else
            {
                $countres = '[{"id":"0"}]';
                echo $countresult = $cryptor->encrypt($countres, $key);
            }
        } 
        catch(PDOException $e) 
        {
            
            $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
            echo $countresult = $cryptor->encrypt($error, $key);
        }
} 
//function filitersData($pid){
//    try{
//        $apikey = 'rotcoderaclla';
//        $cryptor    = new \RNCryptor\Encryptor();
//        $dataarray  = array();
//        $db = getConnection();
//        $getsql  = "SELECT openAppdate,refill_due,last_physician,calc_next_visit,hh_certification, ctopenAppDate, calc_next_ct, h_p, hpopenAppDate, calc_next_hp, awv_required, awvopenAppDate, calc_next_awv, sudo_required, sudoopenAppDate, calc_next_sudo, cpo, calc_next_sp, spopenAppDate, ccm, ccmopenAppDate, calc_next_ccm
//            FROM patient_data
//            WHERE pid ='$pid'";
//        $sql_stmt = $db->prepare($getsql) ;
//        $sql_stmt->execute();  
//        $screen_filters_names = $sql_stmt->fetchAll(PDO::FETCH_OBJ); 
//        if(!empty($screen_filters_names)){
//            $now = time();
//            $name = '';
//            $sql_refil = "SELECT title FROM layout_options WHERE form_id = 'DEM' AND field_id = 'refill_due'";
//            $sql_refil = $db->prepare($sql_refil) ;
//            $sql_refil->execute();  
//            $sql_refil_title = $sql_refil->fetchAll(PDO::FETCH_OBJ);
//            
//            $dataarray[isset($sql_refil_title[0]->title)?$sql_refil_title[0]->title :'refill_due'] = $screen_filters_names[0]->refill_due;
//            
//            $sql_lp = "SELECT title FROM layout_options WHERE form_id = 'DEM' AND field_id = 'last_physician'";
//            $sql_lpt = $db->prepare($sql_lp) ;
//            $sql_lpt->execute();  
//            $sql_lp_title = $sql_lpt->fetchAll(PDO::FETCH_OBJ);
//            
//            $dataarray[isset($sql_lp_title[0]->title)?$sql_refil_title[0]->title :'refill_due'] = $screen_filters_names[0]->last_physician;
//            
//            if(strtotime($screen_filters_names[0]->openAppdate) > $now){
//                $name = 'openAppdate';
//                $hh_certification = $screen_filters_names[0]->openAppdate;
//            }else{
//                $name = 'calc_next_visit';
//                $hh_certification = $screen_filters_names[0]->calc_next_visit;
//            }
//            $sql_title = "SELECT title FROM layout_options WHERE form_id = 'DEM' AND field_id = '$name'";
//            $sql_stmt2 = $db->prepare($sql_title) ;
//            $sql_stmt2->execute();  
//            $sql_titles = $sql_stmt2->fetchAll(PDO::FETCH_OBJ);
//            if(!empty($sql_titles)){
//                $dataarray[$sql_titles[0]->title] = $hh_certification;
//            }
//            if($screen_filters_names[0]->hh_certification == 'YES'){
//                $name = '';
//                $sql_tt = "SELECT title FROM layout_options WHERE form_id = 'DEM' AND field_id = 'hh_certification'";
//                $sql_st = $db->prepare($sql_tt) ;
//                $sql_st->execute();  
//                $sql_title = $sql_st->fetchAll(PDO::FETCH_OBJ);
//                if(!empty($sql_title)){
//                    $dataarray[$sql_title[0]->title] = 'YES';
//                }
//                $now = time();
//                if(strtotime($screen_filters_names[0]->ctopenAppDate) > $now){
//                    $name = 'ctopenAppDate';
//                    $hh_certification = $screen_filters_names[0]->ctopenAppDate;
//                }else{
//                    $name = 'calc_next_ct';
//                    $hh_certification = $screen_filters_names[0]->calc_next_ct;
//                }
//                $sql_title = "SELECT title FROM layout_options WHERE form_id = 'DEM' AND field_id = '$name'";
//                $sql_stmt2 = $db->prepare($sql_title) ;
//                $sql_stmt2->execute();  
//                $sql_titles = $sql_stmt2->fetchAll(PDO::FETCH_OBJ);
//                if(!empty($sql_titles)){
//                    $dataarray[$sql_titles[0]->title] = $hh_certification;
//                }
//            }
//            if($screen_filters_names[0]->cpo == 'YES'){
//                $name = '';
//                $sql_tt = "SELECT title FROM layout_options WHERE form_id = 'DEM' AND field_id = 'cpo'";
//                $sql_st = $db->prepare($sql_tt) ;
//                $sql_st->execute();  
//                $sql_title = $sql_st->fetchAll(PDO::FETCH_OBJ);
//                if(!empty($sql_title)){
//                    $dataarray[$sql_title[0]->title] = 'YES';
//                }
//                $now = time();
//                if(strtotime($screen_filters_names[0]->spopenAppDate) > $now){
//                    $name = 'spopenAppDate';
//                    $cpo = $screen_filters_names[0]->spopenAppDate;
//                }else{
//                    $name = 'calc_next_sp';
//                    $cpo = $screen_filters_names[0]->calc_next_sp;
//                }
//                $sql_title = "SELECT title FROM layout_options WHERE form_id = 'DEM' AND field_id = '$name'";
//                $sql_stmt2 = $db->prepare($sql_title) ;
//                $sql_stmt2->execute();  
//                $sql_titles = $sql_stmt2->fetchAll(PDO::FETCH_OBJ);
//                if(!empty($sql_titles)){
//                    $dataarray[$sql_titles[0]->title] = $cpo;
//                }
//            }
//            if($screen_filters_names[0]->ccm == 'YES'){
//                $name = '';
//                $sql_tt = "SELECT title FROM layout_options WHERE form_id = 'DEM' AND field_id = 'ccm'";
//                $sql_st = $db->prepare($sql_tt) ;
//                $sql_st->execute();  
//                $sql_title = $sql_st->fetchAll(PDO::FETCH_OBJ);
//                if(!empty($sql_title)){
//                    $dataarray[$sql_title[0]->title] = 'YES';
//                }
//                $now = time();
//                if(strtotime($screen_filters_names[0]->ccmopenAppDate) > $now){
//                    $name = 'ccmopenAppDate';
//                    $ccm = $screen_filters_names[0]->ccmopenAppDate;
//                }else{
//                    $name = 'calc_next_ccm';
//                    $ccm = $screen_filters_names[0]->calc_next_ccm;
//                }
//                $sql_title = "SELECT title FROM layout_options WHERE form_id = 'DEM' AND field_id = '$name'";
//                $sql_stmt2 = $db->prepare($sql_title) ;
//                $sql_stmt2->execute();  
//                $sql_titles = $sql_stmt2->fetchAll(PDO::FETCH_OBJ);
//                if(!empty($sql_titles)){
//                    $dataarray[$sql_titles[0]->title] = $ccm;
//                }
//            }
//            if($screen_filters_names[0]->sudo_required == 'YES'){
//                $name = '';
//                $sql_tt = "SELECT title FROM layout_options WHERE form_id = 'DEM' AND field_id = 'sudo_required'";
//                $sql_st = $db->prepare($sql_tt) ;
//                $sql_st->execute();  
//                $sql_title = $sql_st->fetchAll(PDO::FETCH_OBJ);
//                if(!empty($sql_title)){
//                    $dataarray[$sql_title[0]->title] = 'YES';
//                }
//                $now = time();
//                if(strtotime($screen_filters_names[0]->sudoopenAppDate) > $now){
//                    $name = 'sudoopenAppDate';
//                    $sudo_required = $screen_filters_names[0]->sudoopenAppDate;
//                }else{
//                    $name = 'calc_next_sudo';
//                    $sudo_required = $screen_filters_names[0]->calc_next_sudo;
//                }
//                $sql_title = "SELECT title FROM layout_options WHERE form_id = 'DEM' AND field_id = '$name'";
//                $sql_stmt2 = $db->prepare($sql_title) ;
//                $sql_stmt2->execute();  
//                $sql_titles = $sql_stmt2->fetchAll(PDO::FETCH_OBJ);
//                if(!empty($sql_titles)){
//                    $dataarray[$sql_titles[0]->title] = $sudo_required;
//                }
//            }
//            if($screen_filters_names[0]->awv_required == 'YES'){
//                $name = '';
//                $sql_tt = "SELECT title FROM layout_options WHERE form_id = 'DEM' AND field_id = 'awv_required'";
//                $sql_st = $db->prepare($sql_tt) ;
//                $sql_st->execute();  
//                $sql_title = $sql_st->fetchAll(PDO::FETCH_OBJ);
//                if(!empty($sql_title)){
//                    $dataarray[$sql_title[0]->title] = 'YES';
//                }
//                $now = time();
//                if(strtotime($screen_filters_names[0]->awvopenAppDate) > $now){
//                    $name = 'awvopenAppDate';
//                    $awv_required = $screen_filters_names[0]->awvopenAppDate;
//                }else{
//                    $name = 'calc_next_awv';
//                    $awv_required = $screen_filters_names[0]->calc_next_awv;
//                }
//                $sql_title = "SELECT title FROM layout_options WHERE form_id = 'DEM' AND field_id = '$name'";
//                $sql_stmt2 = $db->prepare($sql_title) ;
//                $sql_stmt2->execute();  
//                $sql_titles = $sql_stmt2->fetchAll(PDO::FETCH_OBJ);
//                if(!empty($sql_titles)){
//                    $dataarray[$sql_titles[0]->title] = $awv_required;
//                }
//            }
//            if($screen_filters_names[0]->h_p == 'YES'){
//                $name = '';
//                $sql_tt = "SELECT title FROM layout_options WHERE form_id = 'DEM' AND field_id = 'h_p'";
//                $sql_st = $db->prepare($sql_tt) ;
//                $sql_st->execute();  
//                $sql_title = $sql_st->fetchAll(PDO::FETCH_OBJ);
//                if(!empty($sql_title)){
//                    $dataarray[$sql_title[0]->title] = 'YES';
//                }
//                $now = time();
//                if(strtotime($screen_filters_names[0]->hpopenAppDate) > $now){
//                    $name = 'hpopenAppDate';
//                    $h_p = $screen_filters_names[0]->hpopenAppDate;
//                }else{
//                    $name = 'calc_next_hp';
//                    $h_p = $screen_filters_names[0]->calc_next_hp;
//                }
//                $sql_title = "SELECT title FROM layout_options WHERE form_id = 'DEM' AND field_id = '$name'";
//                $sql_stmt2 = $db->prepare($sql_title) ;
//                $sql_stmt2->execute();  
//                $sql_titles = $sql_stmt2->fetchAll(PDO::FETCH_OBJ);
//                if(!empty($sql_titles)){
//                    $dataarray[$sql_titles[0]->title] = $h_p;
//                }
//            }
//        }
////           echo "<pre>"; print_r($dataarray); echo "</pre>";
//        if($dataarray)
//        {   
//           $datares = json_encode($dataarray); 
//           echo $patientresult = $cryptor->encrypt($datares, $apikey);
//        }
//        else
//        {
//            $datares = '[{"id":"0"}]';   
//            echo $patientresult = $cryptor->encrypt($datares, $apikey);
//        }
//    }catch(PDOException $e){
//        $insertquery = '{"error":{"text":'. $e->getMessage() .'}}'; 
//        echo $cryptor->encrypt($insertquery, $apikey);
//    }
//    
//}
function filitersData($pid){
    try{
        $apikey = 'rotcoderaclla';
        $cryptor    = new \RNCryptor\Encryptor();
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
            
            $dataarray[][isset($sql_refil_title[0]->title)?$sql_refil_title[0]->title :'refill_due'] = $screen_filters_names[0]->refill_due;
            
            $sql_lp = "SELECT title FROM layout_options WHERE form_id = 'DEM' AND field_id = 'last_physician'";
            $sql_lpt = $db->prepare($sql_lp) ;
            $sql_lpt->execute();  
            $sql_lp_title = $sql_lpt->fetchAll(PDO::FETCH_OBJ);
            
            $dataarray[][isset($sql_lp_title[0]->title)?$sql_lp_title[0]->title :'last_physician'] = $screen_filters_names[0]->last_physician;
            
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
                $dataarray[][$sql_titles[0]->title] = $hh_certification;
            }
            if($screen_filters_names[0]->hh_certification == 'YES'){
                $name = '';
                $sql_tt = "SELECT title FROM layout_options WHERE form_id = 'DEM' AND field_id = 'hh_certification'";
                $sql_st = $db->prepare($sql_tt) ;
                $sql_st->execute();  
                $sql_title = $sql_st->fetchAll(PDO::FETCH_OBJ);
                if(!empty($sql_title)){
                    $dataarray[][$sql_title[0]->title] = 'YES';
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
                    $dataarray[][$sql_titles[0]->title] = $hh_certification;
                }
            }
            if($screen_filters_names[0]->cpo == 'YES'){
                $name = '';
                $sql_tt = "SELECT title FROM layout_options WHERE form_id = 'DEM' AND field_id = 'cpo'";
                $sql_st = $db->prepare($sql_tt) ;
                $sql_st->execute();  
                $sql_title = $sql_st->fetchAll(PDO::FETCH_OBJ);
                if(!empty($sql_title)){
                    $dataarray[][$sql_title[0]->title] = 'YES';
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
                    $dataarray[][$sql_titles[0]->title] = $cpo;
                }
            }
            if($screen_filters_names[0]->ccm == 'YES'){
                $name = '';
                $sql_tt = "SELECT title FROM layout_options WHERE form_id = 'DEM' AND field_id = 'ccm'";
                $sql_st = $db->prepare($sql_tt) ;
                $sql_st->execute();  
                $sql_title = $sql_st->fetchAll(PDO::FETCH_OBJ);
                if(!empty($sql_title)){
                    $dataarray[][$sql_title[0]->title] = 'YES';
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
                    $dataarray[][$sql_titles[0]->title] = $ccm;
                }
            }
            if($screen_filters_names[0]->sudo_required == 'YES'){
                $name = '';
                $sql_tt = "SELECT title FROM layout_options WHERE form_id = 'DEM' AND field_id = 'sudo_required'";
                $sql_st = $db->prepare($sql_tt) ;
                $sql_st->execute();  
                $sql_title = $sql_st->fetchAll(PDO::FETCH_OBJ);
                if(!empty($sql_title)){
                    $dataarray[][$sql_title[0]->title] = 'YES';
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
                    $dataarray[][$sql_titles[0]->title] = $sudo_required;
                }
            }
            if($screen_filters_names[0]->awv_required == 'YES'){
                $name = '';
                $sql_tt = "SELECT title FROM layout_options WHERE form_id = 'DEM' AND field_id = 'awv_required'";
                $sql_st = $db->prepare($sql_tt) ;
                $sql_st->execute();  
                $sql_title = $sql_st->fetchAll(PDO::FETCH_OBJ);
                if(!empty($sql_title)){
                    $dataarray[][$sql_title[0]->title] = 'YES';
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
                    $dataarray[][$sql_titles[0]->title] = $awv_required;
                }
            }
            if($screen_filters_names[0]->h_p == 'YES'){
                $name = '';
                $sql_tt = "SELECT title FROM layout_options WHERE form_id = 'DEM' AND field_id = 'h_p'";
                $sql_st = $db->prepare($sql_tt) ;
                $sql_st->execute();  
                $sql_title = $sql_st->fetchAll(PDO::FETCH_OBJ);
                if(!empty($sql_title)){
                    $dataarray[][$sql_title[0]->title] = 'YES';
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
                    $dataarray[][$sql_titles[0]->title] = $h_p;
                }
            }
        }
        
        if($dataarray)
        {   
           $datares = json_encode($dataarray); 
           echo $patientresult = $cryptor->encrypt($datares, $apikey);
        }
        else
        {
            $datares = '[{"id":"0"}]';   
            echo $patientresult = $cryptor->encrypt($datares, $apikey);
        }
    }catch(PDOException $e){
        $insertquery = '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo $cryptor->encrypt($insertquery, $apikey);
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

function getfile(){
    $password = "rotcoderaclla";
    echo $plaintext = "Here is my test vector. It's not too long, but more than a block and needs padding.";echo "<br>";

    $cryptor = new \RNCryptor\Encryptor();
    $base64Encrypted = $cryptor->encrypt($plaintext, $password);

    //echo "Plaintext:\n$plaintext\n\n";
    echo "Base64 Encrypted:\n$base64Encrypted\n\n";

    echo "<br>======================<br>";
//
    $cryptor = new \RNCryptor\Decryptor();
    $decrypedtext = $cryptor->decrypt('AwEX4sYcIpYyNZjbTyGnLyr1azGZKTdPvoQOky5Q0JMEmZrMAFrmYjtGQHdQwIMufe05yhpnvw5TTiOr2jnS6ynV+G11GJFRcHPROSh/aqAYoDZ9dJOT6v7R3UJQRP83baAa7nuLbif1xj3QaST12Br4', $password);

    //echo "Plaintext:\n$plaintext\n\n";
    echo "Base64 Decrypted:\n$decrypedtext\n\n";
//    echo $username = aes256Decrypt ($password, 'AwEX4sYcIpYyNZjbTyGnLyr1azGZKTdPvoQOky5Q0JMEmZrMAFrmYjtGQHdQwIMufe05yhpnvw5TTiOr2jnS6ynV+G11GJFRcHPROSh/aqAYoDZ9dJOT6v7R3UJQRP83baAa7nuLbif1xj3QaST12Br4');

}
function createAgencies(){
    $apikey = 'rotcoderaclla';
    $cryptor    = new \RNCryptor\Encryptor();
    $dataarray  = array();
    try 
    {
        $db = getConnection();
        $patientsreminder = getLayoutGroupSpecificFunction('AGENCY', '');
//        echo "<pre>"; print_r($patientsreminder); echo "</pre>";
        if($patientsreminder)
        {
            $patientres = json_encode($patientsreminder); 
            echo $patientresult = $cryptor->encrypt($patientres, $apikey);
        }
        else
        {
            $patientres =  '[{"id":"0"}]';
            echo $patientresult = $cryptor->encrypt($patientres, $apikey);
        }
    } 
    catch(PDOException $e) 
    {

        $patientres =  '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo $patientresult = $cryptor->encrypt($patientres, $apikey);
    }
}
function editLayoutSpecificFunction ($patientsreminders,$form_name){
    $i = 0;
    $db = getConnection();
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
                                END as isRequired,max_length,description FROM layout_options WHERE form_id = '$form_name' AND field_id = '$pkey' order by group_name, seq";
                $db->query( "SET NAMES utf8");
                $stmt_layout2 = $db->prepare($get_layout2) ;
                $stmt_layout2->execute();                       
                $layout_fields2 = $stmt_layout2->fetchAll(PDO::FETCH_OBJ);
                if(!empty($layout_fields2) && !empty($layout_fields2[0]->field_type)){
                    $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['field_id'] = $pkey;
                    $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['field_type'] =  $layout_fields2[0]->field_type;
                    $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['isRequired'] =  $layout_fields2[0]->isRequired;
                    $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['label']  = $layout_fields2[0]->title;
                    $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['list_id']  = $layout_fields2[0]->list_id;
                    if($layout_fields2[0]->max_length == 0)
                        $maxlength = '';
                    else
                        $maxlength = $layout_fields2[0]->max_length;
                    $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['max_length']  = $maxlength;
                    $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['placeholder']  = $layout_fields2[0]->description;
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

                        $stringvalue = '';
                        for($j=0; $j<count($exploded_val); $j++){
                            $get_title = "SELECT title,option_id FROM list_options WHERE option_id = '$exploded_val[$j]' AND list_id = '".$layout_fields2[0]->list_id."'";
                            $db->query( "SET NAMES utf8");
                            $title_stmt = $db->prepare($get_title) ;
                            $title_stmt->execute();                       

                            $settitle = $title_stmt->fetchAll(PDO::FETCH_OBJ);  
                            if(!empty($settitle)){
                               $stringvalue .= $settitle[0]->option_id.",";
                            }
                        }
                        $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['value'] = rtrim($stringvalue,',');
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
                    }else if($layout_fields2[0]->field_type == 'Patient allergies'){

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
                        $patientsreminder[substr($layout_fields2[0]->group_name,1)][$i]['value'] = $data2;
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
        return $patientsreminder;
}
function editAgencies($id){
    $apikey = 'rotcoderaclla';
    $cryptor    = new \RNCryptor\Encryptor();
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
        $patientsreminder = editLayoutSpecificFunction($patientsreminders,'AGENCY');
//        echo "<pre>"; print_r($patientsreminder); echo "</pre>";
        if($patientsreminder)
        {
            $patientres = json_encode($patientsreminder); 
            echo $patientresult = $cryptor->encrypt($patientres, $apikey);
//            echo $fileresult =  GibberishAES::dec($patientresult, $apikey);
        }
        else
        {
            $patientres =  '[{"id":"0"}]';
            echo $patientresult = $cryptor->encrypt($patientres, $apikey);
        }
    } 
    catch(PDOException $e) 
    {

        $patientres =  '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo $patientresult = $cryptor->encrypt($patientres, $apikey);
    }
}
function savePatientAgency(){
    try
    {
        $db = getConnection();
        $request = Slim::getInstance()->request();
        $apikey = 'rotcoderaclla';
        $cryptor = new \RNCryptor\Decryptor();
        $encryptor    = new \RNCryptor\Encryptor();
        $appres = $cryptor->decrypt($request->getBody(), $apikey);
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
            echo $encryptor->encrypt($insertcheck, $apikey);
	}else{
            $insertcheck = '[{"id":"0"}]';
            echo $encryptor->encrypt($insertcheck, $apikey);
	}
    }
    catch(PDOException $e)
    {

        $patientres =  '{"error":{"text":'. $e->getMessage() .'}}';
        echo $patientresult = $encryptor->encrypt($patientres, $apikey);
        insertMobileLog('insert/update',"$username",$patientres,$pid,'',"Edit Patient Agency Data Screen - Query Failed", 0);
    }  
}
function finalizeEid(){
    try{
        
        $db = getConnection();
        $request = Slim::getInstance()->request();
        $apikey = 'rotcoderaclla';
        $cryptor = new \RNCryptor\Decryptor();
        $appres = $cryptor->decrypt($request->getBody(), $apikey);
        $insertArray = json_decode($appres,TRUE);
        $encryptor    = new \RNCryptor\Encryptor();
        
        $providerId     = $insertArray['loginProviderId'];
        $encounter      = $insertArray['encounter'];
        $signeddate     = $insertArray['date'];
        $username       = $insertArray['username'];
        $pid            = $insertArray['pid'];
        
        $esign = "UPDATE form_encounter SET elec_signedby = $providerId , elec_signed_on =  '$signeddate' WHERE encounter = $encounter AND pid = $pid";
        $stmt = $db->prepare($esign) ;
        
        if($stmt->execute()){  
            $insertcheck =  '[{"id":"1"}]';
            echo $encryptor->encrypt($insertcheck, $apikey);
            insertMobileLog('update',$username,$esign,$pid,'','Save Finalize Encounter Screen',1);
	}else{
            $insertcheck = '[{"id":"0"}]';
            echo $encryptor->encrypt($insertcheck, $apikey);
            insertMobileLog('update',$username,$esign,$pid,'','Save Finalize Encounter Screen - Failed',0);
	}
	  
    }catch(PDOException $e){
        $insertquery = '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo $encryptor->encrypt($insertquery, $apikey);
        insertMobileLog('update',$username,$insertquery,$pid,'','Save Finalize Encounter Screen - Query Failed',0);
    }
}
function editIssues($id,$pid,$type){
    $apikey = 'rotcoderaclla';
    $encryptor    = new \RNCryptor\Encryptor();
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
        
        $issues[5]['Label'] = 'End Date';
        $issues[5]['Value'] = '';
        $issues[5]['field_id'] = 'enddate';
        $issues[5]['Note'] = '(leave blank if still active)';
        $issues[5]['Type'] = 'DateTime';
        $issues[5]['isRequired'] = 'Optional';
        $issues[5]['placeholder'] = 'Select end date';
        $issues[5]['max_length'] = '20';
        
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
            echo $patientresult = $encryptor->encrypt($patientres, $apikey);
        }
        else
        {
            $patientres =  '[{"id":"0"}]';
            echo $patientresult = $encryptor->encrypt($patientres, $apikey);
        }
    }
    catch(PDOException $e)
    {

        $patientres =  '{"error":{"text":'. $e->getMessage() .'}}';
        echo $patientresult = $encryptor->encrypt($patientres, $apikey);
    }
}
function editImmunization($patientId,$id){
    $apikey = 'rotcoderaclla';
    $encryptor    = new \RNCryptor\Encryptor();
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
            echo $patientresult = $encryptor->encrypt($patientres, $apikey);
        }
        else
        {
            $patientres =  '[{"id":"0"}]';
            echo $patientresult = $encryptor->encrypt($patientres, $apikey);
        }
    }
    catch(PDOException $e)
    {

        $patientres =  '{"error":{"text":'. $e->getMessage() .'}}';
        echo $patientresult = $encryptor->encrypt($patientres, $apikey);
    }
}
function searchICDs($form_code_type,$search_term){
    $apikey = 'rotcoderaclla';
    $encryptor    = new \RNCryptor\Encryptor();
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
            echo $patientresult = $encryptor->encrypt($patientres, $apikey);
        }
        else
        {
            $patientres =  '[{"id":"0"}]';
            echo $patientresult = $encryptor->encrypt($patientres, $apikey);
        }
    }
    catch(PDOException $e)
    {

        $patientres =  '{"error":{"text":'. $e->getMessage() .'}}';
        echo $patientresult = $encryptor->encrypt($patientres, $apikey);
    }
}
function saveDynamicImmunization(){
    try
    {
        $db = getConnection();
        $request = Slim::getInstance()->request();
        $apikey = 'rotcoderaclla';
        $cryptor = new \RNCryptor\Decryptor();
        $appres = $cryptor->decrypt($request->getBody(), $apikey);
        $insertArray = json_decode($appres,TRUE);
        $encryptor    = new \RNCryptor\Encryptor();
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
            echo $encryptor->encrypt($insertcheck, $apikey);
            insertMobileLog($checkinsert,$username,$query,$patient_id,'',"Immunization  Screen",1);
	}else{
            $insertcheck = '[{"id":"0"}]';
            echo $encryptor->encrypt($insertcheck, $apikey);
            insertMobileLog($checkinsert,$username,$query,$patient_id,'',"Immunization Screen- Failed",0);
	}
    }
    catch(PDOException $e)
    {

        $patientres =  '{"error":{"text":'. $e->getMessage() .'}}';
        echo $patientresult = $encryptor->encrypt($patientres, $apikey);
        insertMobileLog('insert/update',$username,$patientres,$patient_id,'',"Immunization Screen - Query Failed",0);
    }
}
function saveDynamicIssues(){
    try
    {
        $db = getConnection();
        $request = Slim::getInstance()->request();
        $apikey = 'rotcoderaclla';
        $cryptor = new \RNCryptor\Decryptor();
        $appres = $cryptor->decrypt($request->getBody(), $apikey);
        $insertArray = json_decode($appres,TRUE);
        $encryptor    = new \RNCryptor\Encryptor();
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
            echo $encryptor->encrypt($insertcheck, $apikey);
	}else{
            $insertcheck = '[{"id":"0"}]';
            echo $encryptor->encrypt($insertcheck, $apikey);
	}
    }
    catch(PDOException $e)
    {

        $patientres =  '{"error":{"text":'. $e->getMessage() .'}}';
        echo $patientresult = $encryptor->encrypt($patientres, $apikey);
    }
}
function deleteIssue($pid,$id,$type,$username,$eid){
    $apikey = 'rotcoderaclla';
    $encryptor    = new \RNCryptor\Encryptor();
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
            echo $encryptor->encrypt($insertcheck, $apikey);
            insertMobileLog('delete',$username,$query,$pid,$eid,"DELETE $type  Screen",1);
	}else{
            $insertcheck = '[{"id":"0"}]';
            echo $encryptor->encrypt($insertcheck, $apikey);
            insertMobileLog('delete',$username,$query,$pid,$eid,"DELETE $type  Screen",0);
	}
    }
    catch(PDOException $e)
    {

        $patientres =  '{"error":{"text":'. $e->getMessage() .'}}';
        echo $patientresult = $encryptor->encrypt($patientres, $apikey);
        insertMobileLog('delete',$username,$patientres,$pid,$eid,"DELETE $type Screen - Query Failed",0);
    }    
}
function deleteAgency($id,$username){
    $apikey = 'rotcoderaclla';
    $encryptor    = new \RNCryptor\Encryptor();
    try
    {
        $db = getConnection();
        $issues = array();
        $setted = 0;
        $query = "DELETE FROM tbl_patientagency WHERE id = $id ";
        
        $stmt_layout = $db->prepare($query);
        if($stmt_layout->execute()){  
            $insertcheck =  '[{"id":"1"}]';
            echo $encryptor->encrypt($insertcheck, $apikey);
            insertMobileLog('delete',$username,$query,'','',"DELETE Patient Agency Screen",1);
	}else{
            $insertcheck = '[{"id":"0"}]';
            echo $encryptor->encrypt($insertcheck, $apikey);
            insertMobileLog('delete',$username,$query,'','',"DELETE Patient Agency Screen - Failed",0);
	}
    }
    catch(PDOException $e)
    {

        $patientres =  '{"error":{"text":'. $e->getMessage() .'}}';
        echo $patientresult = $encryptor->encrypt($patientres, $apikey);
        insertMobileLog('delete',$username,$patientres,'','',"DELETE Patient Agency Screen - Query Failed",0);
    }    
}
function editPrescription($patientId,$id){
    $apikey = 'rotcoderaclla';
    $encryptor    = new \RNCryptor\Encryptor();
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
            echo $patientresult = $encryptor->encrypt($patientres, $apikey);
        }
        else
        {
            $patientres =  '[{"id":"0"}]';
            echo $patientresult = $encryptor->encrypt($patientres, $apikey);
        }
    }
    catch(PDOException $e)
    {

        $patientres =  '{"error":{"text":'. $e->getMessage() .'}}';
        echo $patientresult = $encryptor->encrypt($patientres, $apikey);
    }
}
function saveDynamicPrescription(){
    try
    {
        $db = getConnection();
        $request = Slim::getInstance()->request();
        $apikey = 'rotcoderaclla';
        $cryptor = new \RNCryptor\Decryptor();
        $appres = $cryptor->decrypt($request->getBody(), $apikey);
        $insertArray = json_decode($appres,TRUE);
        $encryptor    = new \RNCryptor\Encryptor();
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
            echo $encryptor->encrypt($insertcheck, $apikey);
            insertMobileLog($checkinsert,$username,$query,$patient_id,'',"Prescriptions  Screen",1);
	}else{
            $insertcheck = '[{"id":"0"}]';
            echo $encryptor->encrypt($insertcheck, $apikey);
            insertMobileLog($checkinsert,$username,$query,$patient_id,'',"Prescriptions Screen- Failed",0);
	}
    }
    catch(PDOException $e)
    {

        $patientres =  '{"error":{"text":'. $e->getMessage() .'}}';
        echo $patientresult = $encryptor->encrypt($patientres, $apikey);
        insertMobileLog('insert/update',$username,$patientres,$patient_id,'',"Prescriptions Screen - Query Failed",0);
    }
}
function editInsurance($type,$pid,$id){
    $apikey = 'rotcoderaclla';
    $encryptor    = new \RNCryptor\Encryptor();
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
            echo $patientresult = $encryptor->encrypt($patientres, $apikey);
        }
        else
        {
            $patientres =  '[{"id":"0"}]';
            echo $patientresult = $encryptor->encrypt($patientres, $apikey);
        }
    }
    catch(PDOException $e)
    {

        $patientres =  '{"error":{"text":'. $e->getMessage() .'}}';
        echo $patientresult = $encryptor->encrypt($patientres, $apikey);
    }
} 
function saveInsurance(){
    try{
        $db = getConnection();
        $request = Slim::getInstance()->request();
        $cryptor = new \RNCryptor\Decryptor();
        $appres = $cryptor->decrypt($request->getBody(), $apikey);
        $insertArray = json_decode($appres,TRUE);
        $encryptor    = new \RNCryptor\Encryptor();
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
            echo $encryptor->encrypt($insertcheck, $apikey);
            insertMobileLog($checkstmt,$username,$query,$pid,'',"Edit $insurancetype Insurance Data",1);
            
	}else{
            $insertcheck = '[{"id":"0"}]';
            echo $encryptor->encrypt($insertcheck, $apikey);
            insertMobileLog($checkstmt,$username,$query,$pid,'',"Edit $insurancetype Insurance Data - Failed",0);
	}
    }
    catch(PDOException $e)
    {

        $patientres =  '{"error":{"text":'. $e->getMessage() .'}}';
        echo $patientresult = $encryptor->encrypt($patientres, $apikey);
        insertMobileLog('insert',$username,$patientres,$pid,'','Edit Insurance Data - Query Failed',0);
    }
}
function getBillingAccess($uid){
    try{
        $apikey = 'rotcoderaclla';
        $encryptor    = new \RNCryptor\Encryptor();
        
        $db = getConnection();
        $sql = "SELECT * FROM tbl_user_custom_attr_1to1 WHERE mobilebillingaccess = 'YES' AND userid= '$uid'";
        $stmt = $db->prepare($sql) ;
        $stmt->execute();                       

        $dataResult = $stmt->fetchAll(PDO::FETCH_OBJ);    
            
        if($dataResult)
        {   
           $datares = '[{"id":"1"}]'; 
           echo $patientresult = $encryptor->encrypt($datares, $apikey);
        }
        else
        {
            $datares = '[{"id":"0"}]';   
            echo $patientresult = $encryptor->encrypt($datares, $apikey);
        }
    }catch(PDOException $e){
        $insertquery = '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo $encryptor->encrypt($insertquery, $apikey);
    }
}
function editDemographics($patientId,$group_name){
    $apikey = 'rotcoderaclla';
    $encryptor    = new \RNCryptor\Encryptor();
    try
    {
        $db = getConnection();
        
        $patientsreminder = array();
        $patientsreminder = editLayoutFunction($patientId,'DEM',$group_name);

//        echo "<pre>"; print_r($patientsreminder); echo "</pre>";
        if($patientsreminder){  
            $insertcheck =  json_encode($patientsreminder);
            echo $encryptor->encrypt($insertcheck, $apikey);
	}else{
            $insertcheck = '[{"id":"0"}]';
            echo $encryptor->encrypt($insertcheck, $apikey);
	}
    }
    catch(PDOException $e)
    {

        $patientres =  '{"error":{"text":'. $e->getMessage() .'}}';
        echo $patientresult = $encryptor->encrypt($patientres, $apikey);
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
        $cryptor = new \RNCryptor\Decryptor();
        $appres = $cryptor->decrypt($request->getBody(), $apikey);
        $insertArray = json_decode($appres,TRUE);
        $encryptor    = new \RNCryptor\Encryptor();
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
            echo $encryptor->encrypt($insertcheck, $apikey);
	}else{
            $insertcheck = '[{"id":"0"}]';
            echo $encryptor->encrypt($insertcheck, $apikey);
	}
    }
    catch(PDOException $e)
    {

        $patientres =  '{"error":{"text":'. $e->getMessage() .'}}';
        echo $patientresult = $encryptor->encrypt($patientres, $apikey);
        insertMobileLog('update',"$username",$patientres,$pid,'',"Edit Employer Data($group_name) Screen - Query Failed", 0);
    }  
}
function editPatientFacility(){
    $apikey = 'rotcoderaclla';
    $cryptor = new \RNCryptor\Decryptor();
    $encryptor    = new \RNCryptor\Encryptor();
    try
    {
        $db = getConnection();
        $request = Slim::getInstance()->request();
        $appres = $cryptor->decrypt($request->getBody(), $apikey);
        $insertArray = json_decode($appres,TRUE);
        
//        $appres =  GibberishAES::dec('U2FsdGVkX1/+Gi53GLrglmd3GWJnAUUG0dK+BmzX1672KfbQ+TNR7h/2gyLuzRtsx44cUyyxokQSwEWJxxOR6D5IokWQFd4pmnpibxMFCUqzwzch+iObEmPeRwbqWuPwy460NedSW7o5A4+4MV94OukpkU2QDpNVdSd6hFgeThz7hAwHE8ryWoY3O8A5RlS343wpDqRRedPSHMcapxKaFq+LSCeNwNgEkbwDaqYkFJZxVKik32AyuI2mEvVu8j5ttIaV8Q2cXta2+IYzrxUUh7ETRphW2IAtFPNDyjvPEKAJbtEWj8kfDl1GseEmYTYcJ9sogTRQulgOxgBbuuEvvEPIFuSEfk8OWk1IRSwC5+hiyaT+GHquVi1kg5qQJMSp9dk4ENXtTVuyGPI5qvLOf808YKxRNUMuzHMpdcyBhrk=', $apikey);
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
            echo $encryptor->encrypt($insertcheck, $apikey);
	}else{
            $insertcheck = '[{"id":"0"}]';
            echo $encryptor->encrypt($insertcheck, $apikey);
	}
    }
    catch(PDOException $e)
    {

        $patientres =  '{"error":{"text":'. $e->getMessage() .'}}';
        echo $patientresult = $encryptor->encrypt($patientres, $apikey);
        insertMobileLog('update',"$username",$patientres,$pid,'',"Edit Facility Data Screen - Query Failed", 0);
    }  
}
function newEncounterScreen($loginProvderId){
    $apikey = 'rotcoderaclla';
    $encryptor    = new \RNCryptor\Encryptor();
    try
    {
        $db = getConnection(); 
        $presc = array();
        $get_fuv = "SELECT visit_categories FROM tbl_allcare_facuservisit WHERE  `users`  REGEXP ('".":\"".$loginProvderId."\"')";
        $fuv_stmt = $db->prepare($get_fuv) ;
        $fuv_stmt->execute();
        $set_fuv = $fuv_stmt->fetchAll(PDO::FETCH_OBJ); 
        if(!empty($set_fuv)){
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
            if(!empty($visit_categories))
                $presc[0]['Value'] = $visit_categories;
            else
                $presc[0]['Value'] = ''; 
            $presc[0]['isRequired'] =  'Required';
        }
        $get_fuv2 = "SELECT facilities FROM tbl_allcare_facuservisit WHERE  `users`  REGEXP ('".":\"".$loginProvderId."\"')";
        $fuv_stmt2 = $db->prepare($get_fuv2) ;
        $fuv_stmt2->execute();
        $set_fuv2 = $fuv_stmt2->fetchAll(PDO::FETCH_OBJ); 
        if(!empty($set_fuv2)){
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
            if(!empty($facility))
                $presc[1]['Value'] = $facility;
            else
                $presc[1]['Value'] = '';
            $presc[1]['isRequired'] =  'Required';
        }
        if(!empty($set_fuv2) && !empty($set_fuv2)){
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
        }
//        echo "<pre>"; print_r($presc); echo "</pre>";
        if($presc)
        {
            $patientres = json_encode($presc);
            echo $patientresult = $encryptor->encrypt($patientres, $apikey);
        }
        else
        {
            $patientres =  '[{"id":"0"},{"msg":"The User doesnt have mapped Service Facilities and Visit Categories"}]';
            echo $patientresult = $encryptor->encrypt($patientres, $apikey);
        }
    }
    catch(PDOException $e)
    {

        $patientres =  '{"error":{"text":'. $e->getMessage() .'}}';
        echo $patientresult = $encryptor->encrypt($patientres, $apikey);
    }
}
function createNewEncounter(){
    $db = getConnection();
    $key = 'rotcoderaclla';
    $request = Slim::getInstance()->request();
    $cryptor = new \RNCryptor\Decryptor();
    $appres = $cryptor->decrypt($request->getBody(), $key);
    $encryptor    = new \RNCryptor\Encryptor();
    //$appres = $cryptor->decrypt('AwEUyPjzaevwqj0GoD4Du2H5Gd3yyQxDwOi4wZDgM7V+D1kgmV4LTHfQ5i9wmgikPgFYyBEBfAzZFMS73iqjhr2F6wmZHp00LtVwSZNyDsR09pT5lEF/PwFBaEnfH6Ob2cCdW1r3mJ77oozf3QsEYRTb', $key);
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
                    echo $newencresult = $encryptor->encrypt($newencres, $key);
            }else{
                    $newencres = '[{"id":"-1"}]';     
                    echo $newencresult = $encryptor->encrypt($newencres, $key);
            }
        } else {
            $newencres = '[{"id":"0"}]';
            echo $newencresult = $encryptor->encrypt($newencres, $key);
            insertMobileLog('insert',"$username",$sql10,$pid,$encounter,"INSERT Encounter Form Data Screen - Failed", 0);
        }
    } catch (Exception $ex) {
        $error =  '{"error":{"text":'. $ex->getMessage() .'}}'; 
        echo $datares = $encryptor->encrypt($error, $key);
        insertMobileLog('insert',"$username",$error,$pid,$encounter,"Create new Encounter from BY Facility Screen - Query Failed", 0);
    }
    
}
function getPatientFaility($id){
    $apikey = 'rotcoderaclla';
    $encryptor    = new \RNCryptor\Encryptor();
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
            echo $patientresult = $encryptor->encrypt($patientres, $apikey);
        }
        else
        {
            $patientres =  '[{"id":"0"}]';
            echo $patientresult = $encryptor->encrypt($patientres, $apikey);
        }
    } 
    catch(PDOException $e) 
    {

        $patientres =  '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo $patientresult = $encryptor->encrypt($patientres, $apikey);
    }
}
function getEncounterMedicalRecord($pid,$id) {

    $db = getConnection();
    $apikey = 'rotcoderaclla';
    $encryptor    = new \RNCryptor\Encryptor();
    
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
                                            $historyidlist2 .= $crow_sel_data[$k]->selectedfield.",";
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
                echo $patientresult = $encryptor->encrypt($newdemores, $apikey);
            }
            else
            {
               $newdemores = '[{"id":"0"}]';
               echo $patientresult = $encryptor->encrypt($newdemores, $apikey);
            }
    
    } 
        catch(PDOException $e) 
        {
            
          $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
          echo $patientresult = $encryptor->encrypt($error, $apikey);
        }
        
     
}// end of getEncounterMedicalRecord
//medical_record list
function getMedical_recorddata($pid) {
    $db = getConnection();
    $apikey = 'rotcoderaclla';
    $encryptor    = new \RNCryptor\Encryptor();
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
            echo $patientresult = $encryptor->encrypt($patientres, $apikey);
        }
        else
        {
         $patientres = '[{"id":"0"}]';
         echo $patientresult = $encryptor->encrypt($patientres, $apikey);
        }
    }catch(PDOException $e)
    {
      $patientres = '{"error":{"text":'. $e->getMessage() .'}}';
       echo $patientresult = $encryptor->encrypt($patientres, $apikey);
    }  

}
function getDosFormDataDetails($eid,$copy_to_encounter, $provider_id){
    $apikey = 'rotcoderaclla';
    $encryptor    = new \RNCryptor\Encryptor();
    // The default key size is 256 bits.
//    $old_key_size = GibberishAES::size();
//    GibberishAES::size(256);
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
           echo  $patientresult = $encryptor->encrypt($patientres, $apikey);
        }else{
                 $patientres = '[{"id":"0"}]';
           echo  $patientresult = $encryptor->encrypt($patientres, $apikey);
        }
    }catch(PDOException $e) {
       $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
       echo  $patientresult = $encryptor->encrypt($error, $apikey);
       insertMobileLog('update',$username,$error,$pid,$copy_to_encounter,"Template FROM Screen - Query Failed",0); 
    }
    
}
//To get the appointment details
function getAppointmentDetails($apptid)
{
	try
	{
		$db=getConnection();
                $pres = array();
                
                $key = 'rotcoderaclla';
                // The default key size is 256 bits.
                $encryptor    = new \RNCryptor\Encryptor();
        
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
          if(!empty($apptdates)){
            for($i=0; $i< count($apptdates); $i++){
                foreach($apptdates[$i] as $pkey=> $pvalue){
                    if(trim($pvalue) != '' )
                        $pres[][$pkey] = $pvalue;
                }
            }
        } 
           
          $appdatesres =  json_encode($pres); 
          echo $appdateresult = $encryptor->encrypt($appdatesres, $key);
           
        }
        else
        {
            $appdatesres = '[{"id":"0"}]';
            echo $appdateresult = $encryptor->encrypt($appdatesres, $key);
        }
	}catch(PDOException $e)
	{
		$error = '{"error":{"text":'. $e->getMessage() .'}}'; 
                echo $appdateresult = $encryptor->encrypt($error, $key);
	}
}
function changeAppointmentStatus()
{
    try{
        
        $db = getConnection();
        $flag=0;

        $key = 'rotcoderaclla';
        
        $storerequest = Slim::getInstance()->request();
        
        $decryptor  = new \RNCryptor\Decryptor();
        $encryptor    = new \RNCryptor\Encryptor();
        
        $decrypedtext = $decryptor->decrypt($storerequest->getBody(), $key);
        $statusArray = json_decode($decrypedtext,TRUE);
			
        $pid            = $statusArray['pid'];
	$pc_eid         = $statusArray['apptid'];
	$pc_apptstatus  = $statusArray['apptstatus'];
        $pc_comments    = $statusArray['comments'];
        $pc_time        = $statusArray['start_time'];
        $pc_eventDate   = $statusArray['event_date'];
        $username1      = $statusArray['username'];
        //$time = explode(" ", $pc_time);	
        $pc_time2       = date("H:i:s", strtotime($pc_time));
	$update_appt_Sql="UPDATE openemr_postcalendar_events
			SET
			pc_apptstatus=:pc_apptstatus , pc_hometext=:pc_comments , pc_startTime=:pc_time, pc_eventDate=:pc_eventDate, pc_time=NOW()
			WHERE pc_pid=:pid AND pc_eid=:pc_eid";
			
	$q = $db->prepare($update_appt_Sql);

        if($q->execute(array( ':pc_apptstatus'=>$pc_apptstatus,':pid'=>$pid,':pc_eid'=>$pc_eid, ':pc_comments'=>$pc_comments, ':pc_time'=>$pc_time2, ':pc_eventDate' =>$pc_eventDate   ))){  
            $appdatesres = '[{"id":"1"}]';
            echo $appdateresult = $encryptor->encrypt($appdatesres, $key);
            insertMobileLog('UPDATE',$username1,$update_appt_Sql,$pid,'','Change Appointment Screen',1);
	}else{
            $appdatesres = '[{"id":"0"}]';
            echo $appdateresult = $encryptor->encrypt($appdatesres, $key);
            insertMobileLog('UPDATE',$username1,$update_appt_Sql,$pid,'','Change Appointment Screen- Failed',0);
	}
		
    }catch(PDOException $e){
	$appdatesres = '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo $appdateresult = $encryptor->encrypt($appdatesres, $key);
        insertMobileLog('UPDATE',$username1,$appdatesres,$pid,'','Change Appointment Screen- Query Failed',0);
    }
	
}
function cancelAppointmentStatus()
{
    try{
        
        $db = getConnection();
        $flag=0;
        
        $key = 'rotcoderaclla';
        $storerequest = Slim::getInstance()->request();
        
        $decryptor  = new \RNCryptor\Decryptor();
        $encryptor    = new \RNCryptor\Encryptor();
        
        $decrypedtext = $decryptor->decrypt($storerequest->getBody(), $key);
        $statusArray = json_decode($decrypedtext,TRUE);
//        $statusArray = json_decode($resultant,TRUE);
			
        $pid            = $statusArray['pid'];
	$pc_eid         = $statusArray['apptid'];
	$pc_apptstatus  = $statusArray['apptstatus'];
        $pc_comments    = $statusArray['comments'];
        $username1      = $statusArray['username'];
        		
        
        		
	$update_appt_Sql="UPDATE openemr_postcalendar_events
			SET
			pc_apptstatus=:pc_apptstatus , pc_hometext=:pc_comments
			WHERE pc_pid=:pid AND pc_eid=:pc_eid";
			
	$q = $db->prepare($update_appt_Sql);

        if($q->execute(array( ':pc_apptstatus'=>$pc_apptstatus,':pid'=>$pid,':pc_eid'=>$pc_eid, ':pc_comments'=>$pc_comments  ))){  
            $appdatesres = '[{"id":"1"}]';
            echo $appdateresult = $encryptor->encrypt($appdatesres, $key);
            insertMobileLog('UPDATE',$username1,$update_appt_Sql,$pid,'','Cancel Appointment Screen',1);
	}else{
            $appdatesres = '[{"id":"0"}]';
            echo $appdateresult = $encryptor->encrypt($appdatesres, $key);
            insertMobileLog('UPDATE',$username1,$update_appt_Sql,$pid,'','Cancel Appointment Screen- Failed',0);
	}
		
    }catch(PDOException $e){
	$appdatesres = '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo $appdateresult = $encryptor->encrypt($appdatesres, $key);
        insertMobileLog('UPDATE',$username1,$appdatesres,$pid,'','Cancel Appointment Screen - Query Failed',0);
    }
	
}
// method to get available time slots
function getAvailableTimeSlots($loginProviderId,$startingDate)
{
    $key = 'rotcoderaclla';
    // The default key size is 256 bits.
    $encryptor    = new \RNCryptor\Encryptor();
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
                    echo $avslotresult = $encryptor->encrypt($avslotres, $key);
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
                    echo $avslotresult = $encryptor->encrypt($avslotres, $key);
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
                    echo $avslotresult = $encryptor->encrypt($avslotres, $key);

            }
	}else{
            $avslotres = '[{"Invalid Date Format":"0"}]';
            echo $avslotresult = $encryptor->encrypt($avslotres, $key);
        }	
    }

    catch(PDOException $e)
    {
        $error = '{"error":{"text":'. $e->getMessage() .'}}';
        echo $avslotresult = $encryptor->encrypt($error, $key);
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
function createNewAppointmentEnounter(){
    $db = getConnection();
    $key = 'rotcoderaclla';
    $request = Slim::getInstance()->request();
    $cryptor = new \RNCryptor\Decryptor();
    $appres = $cryptor->decrypt($request->getBody(), $key);
    $encryptor    = new \RNCryptor\Encryptor();
//    $appres = $cryptor->decrypt('AwHBgx0T/PcLifITFlwUXChwK58/T6KciqKFpD43jrUAuY8VyJzYuMCJdNsjVwvbnQp7qM/BlpS8zk1RzV4IkQw3dT+CThrm1TfwPeT21ulG6KnwU2yKKSX1ScaZ8yVocrrdnZS27SnQr3hynmAhq3sfBm1SLA4nRBOSn2wfuDpdDBnKsvRmHBAud6dArnv255iyojwfq+diD06PfhKALLASDhwsnJJfoPN9x/5elvP2Jqstd9pfq2n5Xy90JqC6R/nB2uLw1+3cPrx2Xk/201EMKiQx10CSa5Y4Ar2USIc+2k3e6YQkr8sKTmInxALVxxk/6GfFIpgc+WFi7Ym4AE5W', $key);
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
                        echo $newencresult = $encryptor->encrypt($newencres, $key);
                }else{
                        $newencres = '[{"id":"-1"}]';     
                        echo $newencresult = $encryptor->encrypt($newencres, $key);
                }
            } else {
                $newencres = '[{"id":"0"}]';
                echo $newencresult = $encryptor->encrypt($newencres, $key);
                insertMobileLog('insert',"$username",$sql10,$pid,$encounter,"INSERT Encounter Form Data Screen - Failed", 0);
            }
        }else {
            $newencres = '[{"id":"'.$encCount.'"}]'; 
            echo $newencresult = $encryptor->encrypt($newencres, $key);
        }
    } catch (Exception $ex) {
        $error =  '{"error":{"text":'. $ex->getMessage() .'}}'; 
        echo $datares = $encryptor->encrypt($error, $key);
        insertMobileLog('insert/update',"$username",$error,$pid,$encounter,"Create new Encounter from Appointment Screen - Query Failed", 0);
    }
    
}