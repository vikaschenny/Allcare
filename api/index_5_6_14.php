<?php

/*
Webservice for user/provider authentication with openemr
*/

// do initial application

require 'Slim/Slim.php';
require 'Slim/Route.php';

//require '../interface/globals.php';
//require '../library/globals.inc.php';

// initialize app
$app = new Slim();

// method for provider login
$app->get('/login/:username/:password', 'loginUser');
//$app->get('/login/:username/:password', 'loginUser');
// method to get list of todays appointments for given provider
$app->get('/patients/:loginUserId', 'getPatients');
// method to get list of all patients
//$app->get('/allpatients/', 'getAllPatients');
$app->get('/allpatients/:fromCount', 'getAllPatients');
// method to get list of patients belonging to given provider
//$app->get('/filterpatients/:loginProvderId', 'getPatientsByProvider');
$app->get('/filterpatients/:loginProvderId/:fromCount', 'getPatientsByProvider');
// method to get patient demographics
$app->get('/demographics/:patientId', 'getDemographics');
// method to get list of appointments for a given date
$app->get('/patientsbyday/:loginProvderId/:day', 'getPatientsByday');
//$app->get('/patientsbyday/:loginProvderId/:day/:visitCategory', 'getPatientsByday');
// method to get list of encounters for given provider
//$app->get('/myencounters/:loginProvderId', 'getEncounterList');
$app->get('/myencounters/:loginProvderId/:patientId', 'getEncounterList');
// method to get list of patients belonging to given provider for messages
//$app->get('/mypatients/:loginProvderId','getMyPatients');
$app->get('/mypatients/:loginProvderId/:fromCount','getMyPatients');

// method to create new message
$app->get('/createmessage/:loginProvderId/:pid/:encounter/:body/:title/:msg_status','createMessage');

// method to get the previous dictation
$app->get('/getdictation/:loginProvderId/:pid/:encounter','getDictation');

// method to store speech dictation
//$app->get('/speechDictation/:loginProvderId/:pid/:encounter/:dictation','createDictation');
$app->get('/speechDictation/:loginProvderId/:pid/:encounter/:dictation/:isNuance','createDictation');

$app->get('/icd9codes','getICD9Codes');

//$app->get('/newencounter/:pid/:loginProvderId/:date_today','createNewEncounter');
$app->get('/newencounter/:pid/:loginProvderId','createNewEncounter');

$app->get('/completedformsbyencounter/:pid/:loginProvderId/:encounter','getCompletedFormsByEncounter');

                          //  1/1/146/Y/Y/N/N/txtMedical/30/txtNursing/txtPhysical/txtOccupational/txtSpeech/txtFindings/txtTreatment/txtHomeBound/txtNurse/~/txtPhysicianSignature/txtPrintedName/~/2014-05-21  
$app->get('/createfacetoface/:pid/:loginProvderId/:encounter/:radBound/:radCare/:radPhysician/:radVisit/:txtMedical/:nextVisitDays/:txtNursing/:txtPhysical/:txtOccupational/:txtSpeech/:txtFindings/:txtTreatment/:txtHomeBound/:txtNurse/:txtNursePractitionerSignDate/:txtPhysicianSignature/:txtPrintedName/:txtPrintedDate/:txtVisitDate','createFacetoFace');
                   //   /76/1/51/~/specimen_week/N/Y/Y/~/CXR,UA/Y/Accept/physician_signature/2014-05-18
$app->get('/labrequest/:pid/:loginProvderId/:encounter/:date_of_request/:specimen_week/:fasting/:frail_health/:is_home_bound/:is_preference_home_health/:diagnosis_codes/:tests/:is_colonoscopy_required/:patient_has/:physician_signature/:date_of_signature','createLabRequest');

//   /vital/1/1/55/99/50/60/50/Axillary/60/40/opop/20/ppp/3/5/60
$app->get('/vital/:pid/:loginProvderId/:encounter/:bps/:bpd/:weight/:height/:temperature/:temp_method/:pulse/:respiration/:note/:BMI/:BMI_status/:waist_circ/:head_circ/:oxygen_saturation','createVital');

$app->get('/soap/:pid/:loginProvderId/:encounter/:subjective/:objective/:assessment/:plan','createSoap');
//$app->get('/note/:pid/:loginProvderId/:note_type/:message/:doctor/:date_of_signature','createNote');

// Methods required for Fee Sheet/Billing
$app->get('/users','getUsers');
$app->get('/encounterdates/:pid/:encounter','getEncounterDates');// For date drop down in Review button pop-up
$app->get('/reviewcontent/:pid/:loginProvderId/:encounter','getReviewContents');


$app->get('/codesforsearch','getCodesForSearch');
$app->get('/searchdiagnosis/:codeSelected/:txtSearch','getSearchResult');
//$app->get('/searchdiagnosis?codeSelected=:codeSelected&txtSearch=:txtSearch','getSearchResult');
/*
$app->post('/searchdiagnosis', function () use ($app) {
   //todo : authenticate user from post variables
   //var_dump($app->request()->post('username'));
   //var_dump($app->request()->post('password'));
      
   $codeSelected =  $app->request()->post('codeSelected');
   $txtSearch = $app->request()->post('txtSearch');
   
   
   getSearchResult($codeSelected,$txtSearch);
});
*/
$app->get('/pricelevel','getPriceLevel');

$app->get('/patientCategory_Search/:pid/:loginProvderId/:encounter/:code_type/:code','new_established_Patient_searchChangeFeeSheet');


$app->post('/reviewadd', function () use ($app) {
   //todo : authenticate user from post variables
   //var_dump($app->request()->post('username'));
   //var_dump($app->request()->post('password'));
     
    $reviewJSONArray=$app->request()->post('jarrProcIssue_new');
    $reviewArray = json_decode($reviewJSONArray, TRUE);
   
   reviewAdd($reviewArray);
});

//$app->post('/reviewadd', 'reviewAdd');

$app->get('/feesheet/:encounter','getFeeData');

$app->get('/deletefeesheetrow/:fid','deleteFeeSheetData');

/*
$app->post('/updatebill', function () use ($app) {
//echo "789";
 $billJSONArray=$app->request()->post('jarrFeeSheetData_new');
 $billArray = json_decode($billJSONArray, TRUE);
	//echo 'EncId=='.$billArray[0]['encounterId'];
// updateBilling($billArray);
});
*/

/*
$app->post('/savebill', function () use ($app) {

$app->response()->header("Content-Type", "application/json");

    $billJSONArray=$app->request()->post('jarrSaveFeeSheetData_new');
    $billArray = json_decode($billJSONArray, TRUE);
	//print_r($billArray);
 saveBilling($billArray);
});
*/

$app->post('/updatebill', 'updateBilling');
$app->post('/savebill', 'saveBilling');

/*     Methods for Fee Sheet/Billing end            */

$app->get('/patientinfo/:pid','getPatientInformation');
/*
$app->post('/createnewpatient', function () use ($app) {

$app->response()->header("Content-Type", "application/json");

    $patientDetailsJSONArray=$app->request()->post('txtPatientDetails');
    $patientDetailsArray = json_decode($patientDetailsJSONArray, TRUE);
	//print_r($billArray);
 createNewPatient($patientDetailsArray);
});
*/
$app->post('/createnewpatient', 'createNewPatient');

$app->get('/facilities','getAllFacilities');
$app->get('/patientsbyfacility/:fid/:loginProvderId','getPatientsByFacility');
$app->get('/visitcategories','getVisitCategories');

$app->run();

// connection to openemr database
function getConnection() 
{

	//$dbhost="emrsb.risecorp.com";
	//$dbhost="172.17.66.203";
/*
	$dbhost="mysql51-110.wc2.dfw1.stabletransit.com";
	$dbuser="551948_newemr";
	$dbpass="Devemr@321";
	$dbname="551948_newemr";
*/	
  
    	$dbhost="mysql51-121.wc2.dfw1.stabletransit.com";
	$dbuser="551948_emrsbox";
	$dbpass="Emrsb@321";
	$dbname="551948_emrsbox";
    /*
	$dbhost="localhost";
	$dbuser="root";
	$dbpass="";
	$dbname="openemr";*/
	$dbh = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);	
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	return $dbh;
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


// method for provider login
// check user/provider authentication with openemr
function loginUser($username,$password)
{       
    //$sql="SELECT id,username,password,salt FROM users_secure WHERE username=:username"; 

    $sql="SELECT us.id,us.username,us.password,us.salt,u.fname,u.lname 
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
                // returns id of the user/provider if user/provider is valid
                //echo '[{"id":'.json_encode($user->id).'}]';

echo '[{"id":'.json_encode($user->id).',"fname":'.json_encode($user->fname).',"lname":'.json_encode($user->lname).'}]';

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

// method to get list of todays appointments for given provider
// get list of appointments that are to be executed for current date for the logged in user/provider
function getPatients($loginUserId)
{
/*
	$sql = "SELECT pd.pid,pd.title,pd.fname,pd.lname,if (pd.sex = 'Female' ,'F','M' ) as sex,
                DATE_FORMAT(pd.DOB,'%m-%d-%Y') as DOB,DATE_FORMAT(FROM_DAYS(DATEDIFF(CURDATE(),pd.DOB)), '%Y')+0 AS age,pd.street,pd.city,pd.state,pd.country_code,pd.postal_code,pd.phone_home,pd.phone_biz,pd.phone_contact,pd.phone_cell, DATE_FORMAT(ope.pc_eventDate,'%m-%d-%Y') as event_date ,if(ope.pc_endDate='0000-00-00',DATE_FORMAT(ope.pc_eventDate,'%m-%d-%y'),DATE_FORMAT(ope.pc_endDate,'%m-%d-%Y'))  as end_date ,ope.pc_duration, TIME_FORMAT(ope.pc_startTime, '%h:%i %p') AS start_time,TIME_FORMAT(ope.pc_endTime, '%h:%i %p') AS end_time,pc.pc_catid,pc.pc_catname
                FROM patient_data pd INNER JOIN openemr_postcalendar_events ope ON pd.pid=ope.pc_pid
                        inner join openemr_postcalendar_categories pc on pc.pc_catid=ope.pc_catid 
                WHERE ope.pc_aid=:loginUserId AND ope.pc_eventdate=CURDATE() AND ope.pc_apptstatus='-'  ";    
*/    
    	
	$sql = "SELECT pd.pid,pd.title,pd.fname,pd.lname
                FROM patient_data pd                        
                WHERE pd.providerID=:loginUserId";                    

        try 
        {
            $db = getConnection();
            $stmt = $db->prepare($sql) ;
            $stmt->bindParam("loginUserId", $loginUserId);            
            $stmt->execute();
            $patients = $stmt->fetchAll(PDO::FETCH_OBJ);
                        
            if($patients)
            {
                //returns patients appointment list
                echo json_encode($patients); 
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

// method to get list of appointments for a given date
function getPatientsByday($loginUserId,$day)
//function getPatientsByday($loginUserId,$day,$visitCategory)
{
	$sql = "SELECT pd.pid,pd.title,pd.fname,pd.lname,if (pd.sex = 'Female' ,'F','M' ) as sex,
                DATE_FORMAT(pd.DOB,'%m-%d-%Y') as DOB,DATE_FORMAT(FROM_DAYS(DATEDIFF(CURDATE(),pd.DOB)), '%Y')+0 AS age,pd.street,pd.city,pd.state,pd.country_code,pd.postal_code,pd.phone_home,pd.phone_biz,pd.phone_contact,pd.phone_cell, DATE_FORMAT(ope.pc_eventDate,'%m-%d-%Y') as event_date ,if(ope.pc_endDate='0000-00-00',DATE_FORMAT(ope.pc_eventDate,'%m-%d-%y'),DATE_FORMAT(ope.pc_endDate,'%m-%d-%Y'))  as end_date ,ope.pc_duration, TIME_FORMAT(ope.pc_startTime, '%h:%i %p') AS start_time,TIME_FORMAT(ope.pc_endTime, '%h:%i %p') AS end_time,pc.pc_catname
                FROM patient_data pd INNER JOIN openemr_postcalendar_events ope ON pd.pid=ope.pc_pid
                        inner join openemr_postcalendar_categories pc on pc.pc_catid=ope.pc_catid 
                WHERE ope.pc_aid=:loginUserId AND ope.pc_apptstatus='-' and ope.pc_eventdate=:day";        
    

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
                echo json_encode($patients); 
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

// method to get list of all patients
function getAllPatients($fromCount)
{
        $toCount=$fromCount+10;
	/*$sql = "select pd.pid,pd.title,pd.fname,pd.lname, DATE_FORMAT(pd.DOB,'%m-%d-%Y') AS DOB,DATE_FORMAT(FROM_DAYS(DATEDIFF(CURDATE(),pd.DOB)), '%Y')+0 AS age,if (pd.sex = 'Female' ,'F','M' ) as sex,pd.street,pd.city,pd.state,pd.country_code,pd.postal_code,pd.phone_cell
                                FROM patient_data pd
                                order by pd.pid desc limit $fromCount,$toCount";  
*/
	$sql = "select pd.pid,pd.title,pd.fname,pd.lname, DATE_FORMAT(pd.DOB,'%m-%d-%Y') AS DOB,DATE_FORMAT(FROM_DAYS(DATEDIFF(CURDATE(),pd.DOB)), '%Y')+0 AS age,if (pd.sex = 'Female' ,'F','M' ) as sex,pd.street,pd.city,pd.state,pd.country_code,pd.postal_code,pd.phone_cell
                from patient_data pd where pd.id not in
                (select pd1.id from patient_data pd1
                join (select * from patient_data order by pid desc limit $fromCount) pd2
                on pd1.id=pd2.id)
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
                echo json_encode($patients); 
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

// method to get list of patients belonging to given provider
// get list of all patients  for the logged in user/provider
function getPatientsByProvider($loginProvderId,$fromCount)
{

/*
	$sql = "select pd.pid,pd.title,pd.fname,pd.lname, DATE_FORMAT(pd.DOB,'%m-%d-%Y') AS DOB,DATE_FORMAT(FROM_DAYS(DATEDIFF(CURDATE(),pd.DOB)), '%Y')+0 AS age,if (pd.sex = 'Female' ,'F','M' ) as sex,pd.street,pd.city,pd.state,pd.country_code,pd.postal_code,pd.phone_cell
FROM patient_data pd 

where pd.providerID=:loginProvderId order by pd.pid desc";
*/

	$sql = "select pd.pid,pd.title,pd.fname,pd.lname, DATE_FORMAT(pd.DOB,'%m-%d-%Y') AS DOB,DATE_FORMAT(FROM_DAYS(DATEDIFF(CURDATE(),pd.DOB)), '%Y')+0 AS age,if (pd.sex = 'Female' ,'F','M' ) as sex,pd.street,pd.city,pd.state,pd.country_code,pd.postal_code,pd.phone_cell
                from patient_data pd where pd.id not in
                (select pd1.id from patient_data pd1
                join (select * from patient_data order by pid desc limit $fromCount) pd2
                on pd1.id=pd2.id)
                and pd.providerID=:loginProvderId 
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
                //returns patients appointment list
                echo json_encode($patients); 
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
  
function getDemographics($patientId)
{
	$sql1 = "select CONCAT(pd.title,pd.fname,' ',pd.lname) as pname, pd.pubpid, pd.DOB, pd.sex, pd.ss, pd.drivers_license, pd.status
from patient_data as pd
where pd.pid=:patientId";

	$sql2 = "select pd.street, pd.city, pd.state, pd.country_code, pd.postal_code, pd.mothersname, pd.guardiansname,pd.contact_relationship as emergency_contact, pd.phone_contact as emergency_phone,pd.phone_home as home_phone,pd.phone_biz as work_phone,pd.phone_cell as mobile_phone, pd.email as contact_email
from patient_data as pd
where pd.pid=:patientId";

	$sql3 = "select concat(u.fname,' ',u.lname) as provider, concat(ur.fname,' ',ur.lname) as reference_provider, ph.name as pharmacy, pd.hipaa_notice as hippa_notice_received
from patient_data pd
inner join users  u on pd.providerID=u.id
inner join users ur on pd.ref_providerID=ur.id
inner join pharmacies ph on pd.pharmacy_id=ph.id where pd.pid=:patientId";

	$sql4 = "select pd.occupation, ed.name as employer, ed.street, ed.city, ed.state, ed.country, ed.postal_code
from employer_data ed
left join  patient_data pd 
on pd.pid=ed.pid
where ed.pid=:patientId
and (ed.name!='' AND ed.street!='' AND ed.city!='' AND ed.state!='' ANd ed.country!='' AND ed.postal_code!='')
order by ed.id desc limit 0,1";	

	$sql5 = "select pd.`language`, pd.ethnicity, pd.race, pd.family_size, pd.monthly_income, pd.homeless, pd.interpretter, pd.migrantseasonal, pd.referral_source, pd.vfc
from patient_data pd
where pd.pid=:patientId";

	$sql6 = "select pd.deceased_date, pd.deceased_reason from patient_data pd where pd.pid=:patientId";
	
	$sql7 = "select ic.name as primary_insurance_provider, ins.plan_name, ins.policy_number, ins.group_number, ins.subscriber_fname, ins.subscriber_lname,
ins.subscriber_relationship, ins.subscriber_sex, ins.subscriber_DOB, ins.subscriber_street, ins.subscriber_city, ins.subscriber_state,
ins.subscriber_country, ins.subscriber_postal_code, ins.date as effective_date
from insurance_data ins
inner join insurance_companies ic on ic.id=ins.provider
where ins.pid=:patientId";

$sql8 = "SELECT id,date,type,title,begdate,enddate,returndate,occurrence,classification,referredby,extrainfo,diagnosis,
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
          from prescriptions where patient_id=:patientId and active='1'";

$sql13 = "select id,date,body,pid,user,groupname,activity,authorized,title,assigned_to,deleted,message_status 
          from pnotes where pid=:patientId and deleted=0";

$sql14 = "select id,date,event,user,recipient,description,patient_id 
          from extended_log where patient_id=:patientId";

$sql15 = "select id,date,pid,user,groupname,authorized,activity,bps,bpd,weight,height,temperature,temp_method,pulse,
                 respiration,note,BMI,BMI_status,waist_circ,head_circ,oxygen_saturation
          from form_vitals where pid=:patientId";

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
            $demo8=sql_execute($patientId,$sql8); //print_r($demo8);
            $demo9=sql_execute($patientId,$sql9);
            $demo10=sql_execute($patientId,$sql10);
            $demo11=sql_execute($patientId,$sql11);
            $demo12=sql_execute($patientId,$sql12);
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
            $newdemo8=encode_demo($demo8);  //print_r($newdemo8);
            $newdemo9=encode_demo($demo9);  
            $newdemo10=encode_demo($demo10);  
            $newdemo11=encode_demo($demo11);              
            $newdemo12=encode_demo($demo12);  
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

		$newdemo['Medical_Problems'] = check_data_available($newdemo8);
		$newdemo['Allergies'] = check_data_available($newdemo9);
		$newdemo['Medications'] = check_data_available($newdemo10);
		$newdemo['Immunization'] = check_data_available($newdemo11);
		$newdemo['Prescription'] = check_data_available($newdemo12);
		$newdemo['Notes'] = check_data_available($newdemo13);
		$newdemo['Disclosure'] = check_data_available($newdemo14);
		$newdemo['Vitals'] = check_data_available($newdemo15);

            echo json_encode($newdemo);

            /*}
            else
            {    
                //echo 'No Patient available';
                echo '{"id":"0"}';
            }*/
        } 
        catch(PDOException $e) 
        {
            
            echo '{"error":{"text":'. $e->getMessage() .'}}'; 
        }
}

// method to get list of encounters for given provider
function getEncounterList($loginProvderId,$patientId)
{
	$sql = "SELECT enc.encounter AS Encounter,
		 CONCAT(pd.fname,' ',pd.lname) as patient,
       GROUP_CONCAT(DISTINCT DATE(enc.date)) AS encounterdate,
       GROUP_CONCAT(ifnull(li.`type`,''),':',ifnull(li.title,'')) AS issues,
       GROUP_CONCAT(DISTINCT ifnull(enc.reason,'')) AS reason,
       GROUP_CONCAT(DISTINCT ifnull(concat(u.fname,' ',u.lname),'')) AS provider,
       GROUP_CONCAT(DISTINCT ifnull(enc.facility,'')) AS facility,
       GROUP_CONCAT(DISTINCT ifnull(fc.name,'')) AS billing_facility
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
   WHERE enc.provider_id=:loginProvderId
   AND pd.pid=:patientId
GROUP BY enc.encounter
ORDER BY pd.id";

	try 
        {
            $db = getConnection();
            $stmt = $db->prepare($sql) ;
            $stmt->bindParam("loginProvderId", $loginProvderId); 
            $stmt->bindParam("patientId", $patientId); 
            $stmt->execute();
            $encounters = $stmt->fetchAll(PDO::FETCH_OBJ);
                        
            if($encounters)
            {
                //returns patients appointment list
                echo json_encode($encounters); 
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

// method to create new message

function createMessage($loginProvderId, $pid, $encounter, $body, $title, $msg_status)
{
	try
	{
		$db = getConnection();
		$rs=getUserName($loginProvderId);
		if($rs)
		{
			$username = $rs[0]['username'];			

$sql = "INSERT INTO pnotes (date, body, pid, user, groupname, activity, authorized, title, assigned_to, message_status)
			values (NOW(), CONCAT(date_format(now(), '%Y-%m-%d %H:%i'),' ','(:username to -patient-)',' ',:body), :pid, :username, 'default', 1, 1, :title, '-patient-', :msg_status)";
			$q = $db->prepare($sql);
			if($q->execute(array(':username'=>$username,
						':pid'=>$pid,
						':body'=>$body,
						':title'=>$title,
						':msg_status'=>$msg_status)))
			
                        {
        
/*
                                $sqlGetLastDictation="SELECT MAX(id) as lastInsertedId 
                                     FROM pnotes";

                                $db = getConnection();
                                $stmt = $db->prepare($sqlGetLastDictation) ;           
                                //$stmt->bindParam("date_today", $date_today);            
                                $stmt->execute();
                                $newEnc = $stmt->fetchAll(PDO::FETCH_OBJ);
                          
                                if($newEnc)
                                {
                                    $lastInsertedId = $newEnc[0]->lastInsertedId;
                                }
                            
                                $sql2 = "INSERT INTO forms (date,encounter,form_name,form_id,pid,user,groupname,authorized,deleted,formdir)
                               values (NOW(), :encounter, 'message', $lastInsertedId,:pid,:username,'Default', 1, 0, 'note')";

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
*/                

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
		//$sql = "INSERT INTO books (title,author) VALUES (:title,:author)";
		
	}
	catch(PDOException $e) 
    {   
        echo '{"error":{"text":'. $e->getMessage() .'}}'; 
    }
}


/*
function createDictation($loginProvderId, $pid, $encounter, $dictation, $isNuance)
{
	try
	{
		$db = getConnection();
		$qry = "SELECT username FROM users WHERE id=$loginProvderId";
		$stmt = $db->prepare($qry) ;
		//$stmt->bindParam("loginProvderId", $loginProvderId);            
		$stmt->execute();
		$rs = $stmt->fetchAll();
		if($rs)
		{
			$username = $rs[0]['username'];
			$sql = "INSERT INTO form_dictation (date, pid, user, groupname, authorized, activity, dictation, is_nuance_dictation)
                                VALUES (NOW(), $pid, '$username', 'Default', 1, 1, '$dictation', '$isNuance')";
                                                                        
			$q = $db->prepare($sql);
                        
			if($q->execute())
			{
                            
                            $sqlGetLastDictation="SELECT MAX(id) as lastInsertedId 
                                                  FROM form_dictation";
           
                            $db = getConnection();
                            $stmt = $db->prepare($sqlGetLastDictation) ;           
                            //$stmt->bindParam("date_today", $date_today);            
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
                            echo '[{"id":"-1"}]';
                        }
                                                
		}
		else
		{
			echo '[{"id":"-2"}]';
		}
		//$sql = "INSERT INTO books (title,author) VALUES (:title,:author)";		
	}
    catch(PDOException $e) 
    {   
        echo '{"error":{"text":'. $e->getMessage() .'}}'; 
    }
}
*/


function getDictation($loginProvderId, $pid, $encounter)
{
    try
    {
        $db = getConnection();                               
	$rs=getUserName($loginProvderId);
        $username='';
        if($rs)
        {
            $username = $rs[0]['username'];
        }
        /*
        $qryFormExists="SELECT id,date,encounter,form_name,form_id,pid,user
                        FROM forms
                        WHERE encounter=$encounter AND pid=$pid 
                        AND user='$username' AND DATE(date)=DATE(now())
                        AND form_name='Speech Dictation'";*/
        
        $qryFormExists="SELECT COUNT(1) FROM forms 
                        WHERE forms.encounter=$encounter 
                        AND forms.form_name='Speech Dictation'";
        
        $stmtFormExists = $db->prepare($qryFormExists) ;		
        $stmtFormExists->execute();
        $resFormExists = $stmtFormExists->fetchAll(PDO::FETCH_OBJ);
          
        if($resFormExists)
        {
            //$form_id = $resFormExists[0]->form_id;
            $sqlGetDictationText="SELECT pid,date,user,dictation
                                  FROM form_dictation
                                  WHERE pid=$pid
                                  AND user='$username'
                                  AND DATE(date)=DATE(now())";

            $stmtGetDictationText = $db->prepare($sqlGetDictationText) ;           
            //$stmt->bindParam("date_today", $date_today);            
            $stmtGetDictationText->execute();
            $dictationText = $stmtGetDictationText->fetchAll(PDO::FETCH_OBJ);
        
            if($dictationText)
            {
                //if encounter already exists return encounter id
                //returns patients appointment list
                echo json_encode($dictationText); 
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
    catch(PDOException $e) 
    {   
        echo '{"error":{"text":'. $e->getMessage() .'}}'; 
    }
}

function createDictation($loginProvderId, $pid, $encounter, $dictation, $isNuance)
{
    try
    {
        $db = getConnection();
		$rs=getUserName($loginProvderId);
        if($rs)
        {
            $username = $rs[0]['username'];
            
            $sqlCheckDictationExists="SELECT pid,date,user,dictation
                                      FROM form_dictation
                                      WHERE pid=$pid
                                      AND user='$username'
                                      AND DATE(date)=DATE(now())";

            $stmtCheckDictationExists = $db->prepare($sqlCheckDictationExists) ;           
            //$stmt->bindParam("date_today", $date_today);            
            $stmtCheckDictationExists->execute();
            $exists = $stmtCheckDictationExists->fetchAll(PDO::FETCH_OBJ);
               
		if(trim($dictation)=='~')
    			$dictation='';
   
		/*if($isNuance==0)
            {
                $dictation=$dictation."_(".date("Y-m-d H:i:s").")";
            }*/

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
                    //$stmt->bindParam("date_today", $date_today);            
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
			AND DATE(date)=DATE(now())";

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
        //$sql = "INSERT INTO books (title,author) VALUES (:title,:author)";		
    }
    catch(PDOException $e) 
    {   
        echo '{"error":{"text":'. $e->getMessage() .'}}'; 
    }
}


/*function createDictation($loginProvderId, $pid, $encounter, $dictation, $isNuance)
{
    try
    {
        $db = getConnection();
	$rs=getUserName($loginProvderId);
        if($rs)
        {
            $username = $rs[0]['username'];
            
            $sqlCheckDictationExists="SELECT pid,date,user,dictation
                                      FROM form_dictation
                                      WHERE pid=$pid
                                      AND user='$username'
                                      AND DATE(date)=DATE(now())";

            $stmtCheckDictationExists = $db->prepare($sqlCheckDictationExists) ;           
            //$stmt->bindParam("date_today", $date_today);            
            $stmtCheckDictationExists->execute();
            $exists = $stmtCheckDictationExists->fetchAll(PDO::FETCH_OBJ);
               
		if(trim($dictation)=='~')
    			$dictation='';
   
		if($isNuance==0)
            {
                $dictation=$dictation."_(".date("Y-m-d H:i:s").")";
            }

            if(!$exists)
            {
                $sql = "INSERT INTO form_dictation (date, pid, user, groupname, authorized, activity, dictation, is_nuance_dictation)
                        VALUES (NOW(), $pid, '$username', 'Default', 1, 1, '$dictation', '$isNuance')";

                $q = $db->prepare($sql);

                if($q->execute())
                {

                    //$lastInsertedId=mysql_insert_id();

                    $sqlGetLastDictation="SELECT MAX(id) as lastInsertedId 
                                          FROM form_dictation";

                    $db = getConnection();
                    $stmt = $db->prepare($sqlGetLastDictation) ;           
                    //$stmt->bindParam("date_today", $date_today);            
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
                $sqlUpdateDictation = "UPDATE form_dictation 
                        SET date=NOW(),
                            dictation='$dictation' 
                        WHERE pid=$pid 
                        AND user='$username'
			AND DATE(date)=DATE(now())
                        AND is_nuance_dictation='1'";

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
        //$sql = "INSERT INTO books (title,author) VALUES (:title,:author)";		
    }
    catch(PDOException $e) 
    {   
        echo '{"error":{"text":'. $e->getMessage() .'}}'; 
    }
}*/


function getICD9Codes()
{
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

//function createNewEncounter($pid,$loginProvderId,$date_today)
function createNewEncounter($pid,$loginProvderId)
{
	//echo "pid=".$pid."<br>loginid=".$loginProvderId."<br>date==".$date_today;
        try
        {
            $sqlCheckEncExists="SELECT encounter FROM form_encounter 
                                WHERE pid=:pid 
                                AND provider_id=:loginProvderId 
                                AND DATE(date)=DATE(now())";
            
            $db = getConnection();
            $stmt = $db->prepare($sqlCheckEncExists) ;
            $stmt->bindParam("pid", $pid);            
            $stmt->bindParam("loginProvderId", $loginProvderId);            
            //$stmt->bindParam("date_today", $date_today);            
            $stmt->execute();
            $encExists = $stmt->fetchAll(PDO::FETCH_OBJ);
                         
            if($encExists)
            {
                //if encounter already exists return encounter id
                //returns patients appointment list
                echo json_encode($encExists); 
            }
            else
            {
                //else create new encounter                
		
		//$conn = $GLOBALS['adodb']['db'];
                //$encounter = $conn->GenID("sequences");                

                $sqlGetMaxEncounter="SELECT MAX(encounter)+1 as max_encounter 
                                     FROM form_encounter";
                                
                $stmt = $db->prepare($sqlGetMaxEncounter) ;           
                //$stmt->bindParam("date_today", $date_today);            
                $stmt->execute();
                $resMaxEncounter = $stmt->fetchAll(PDO::FETCH_OBJ);

                if($resMaxEncounter)
                {
                    $encounter = $resMaxEncounter[0]->max_encounter;
                }            

                $sql = "INSERT INTO form_encounter (date, facility, facility_id, pid, encounter, pc_catid, provider_id, billing_facility)
                values (NOW(), 'Your Clinic Name Here',3,:pid,$encounter,9,:loginProvderId,3 )";
                $q = $db->prepare($sql);
                
                if($q->execute(array(':pid'=>$pid,
                   ':loginProvderId'=>$loginProvderId)))
                {
                    //get the encounter id of the last encounter
                    $sqlGetLastEncounter="SELECT encounter FROM form_encounter 
                                WHERE pid=:pid 
                                AND provider_id=:loginProvderId 
                                AND DATE(date)=DATE(now())";;
           
                    $db = getConnection();
                    $stmt = $db->prepare($sqlGetLastEncounter) ;
                    $stmt->bindParam("pid", $pid);            
                    $stmt->bindParam("loginProvderId", $loginProvderId);            
                    //$stmt->bindParam("date_today", $date_today);            
                    $stmt->execute();
                    $newEnc = $stmt->fetchAll(PDO::FETCH_OBJ);
			
                    if($newEnc)
                    {
                        //if encounter already exists return encounter id
                        //returns patients appointment list
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
}

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
        
        $qryGetForms="SELECT id,date,encounter,form_name,form_id,pid,user
                      FROM forms
                      WHERE encounter=$encounter AND pid=$pid 
                      AND user='$username' AND DATE(date)=DATE(now())";
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
/*
	$qryIsAr_Activity="SELECT pid, encounter, code_type, code, modifier, payer_type, post_time, post_user, session_id, pay_amount, account_code
                        FROM ar_activity
                        WHERE encounter=$encounter AND pid=$pid 
                        AND user=$loginProvderId AND DATE(post_time)=DATE(now())";
        $stmtIsFeeSheet = $db->prepare($qryIsFeeSheet) ;		
        $stmtIsFeeSheet->execute();
        $resIsFeeSheet = $stmtIsFeeSheet->fetchAll(PDO::FETCH_OBJ);

	$qryIsAr_Session="SELECT payer_id, user_id, closed, pay_total, created_time, global_amount, payment_type, description, adjustment_code, post_to_date, patient_id
                        FROM ar_session
                        WHERE encounter=$encounter AND pid=$pid 
                        AND user=$loginProvderId AND DATE(date)=DATE(now())";
        $stmtIsFeeSheet = $db->prepare($qryIsFeeSheet) ;		
        $stmtIsFeeSheet->execute();
        $resIsFeeSheet = $stmtIsFeeSheet->fetchAll(PDO::FETCH_OBJ);
*/
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
                //returns patients appointment list
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

// face to face encounter
function createFacetoFace($pid,$loginProvderId,$encounter,$radBound,$radCare,$radPhysician,$radVisit,$txtMedical,$nextVisitDays,$txtNursing,
        $txtPhysical,$txtOccupational,$txtSpeech,$txtFindings,$txtTreatment,$txtHomeBound,$txtNurse,$txtNursePractitionerSignDate,
        $txtPhysicianSignature,$txtPrintedName,$txtPrintedDate,$txtVisitDate)
{
    try
    {
        $db = getConnection();
  
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
            $txtNurse =($txtNurse=='~')?'':$txtNurse;
            $txtNursePractitionerSignDate =($txtNursePractitionerSignDate=='~')?'':$txtNursePractitionerSignDate;
            $txtPhysicianSignature =($txtPhysicianSignature=='~')?'':$txtPhysicianSignature;
            $txtPrintedName =($txtPrintedName=='~')?'':$txtPrintedName;
            $txtPrintedDate =($txtPrintedDate=='~')?'':$txtPrintedDate;
            $txtVisitDate =($txtVisitDate=='~')?'':$txtVisitDate;
          
           $rs=getUserName($loginProvderId);
		if($rs)
		{
			$username = $rs[0]['username'];
$insert_form_FacetoFace_Sql ="INSERT INTO tbl_form_facetoface(pid,encounter,is_home_bound,is_hhc_needed,other_physician,
                                                              is_house_visit_needed,medical_condition,necessary_hhs,nursing,physical_therapy,occupational_therapy,speech,
                                                              care_treatment,support_service_reason,patient_homebound_reason,nurse_practitioner_signature,nurse_practitioner_signature_date,physician_signature,
                                                              printed_name,printed_name_date,created_date,date_of_service)
                                                        VALUES(:pid,:encounter,:radBound,:radCare,:radPhysician,
                                                              :radVisit,:txtMedical,:nextVisitDays,:txtNursing,:txtPhysical,
                                                              :txtOccupational,:txtSpeech,:txtTreatment,:txtFindings,:txtHomeBound,
                                                              :txtNurse,:txtNursePractitionerSignDate,:txtPhysicianSignature,:txtPrintedName,
                                                              :txtPrintedDate,now(),:txtVisitDate)";

	$q = $db->prepare($insert_form_FacetoFace_Sql);
			  if($q->execute(array( ':pid'=>$pid,
                                                ':encounter'=>$encounter,
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
						':txtNurse'=>$txtNurse,
						':txtNursePractitionerSignDate'=>$txtNursePractitionerSignDate,
						':txtPhysicianSignature'=>$txtPhysicianSignature,
						':txtPrintedName'=>$txtPrintedName,
						':txtPrintedDate'=>$txtPrintedDate,
						':txtVisitDate'=>$txtVisitDate

                                           )))

                        {       

                                $sqlGetLastDictation="SELECT MAX(id) as lastInsertedId 
                                     FROM tbl_form_facetoface";

                                $db = getConnection();
                                $stmt = $db->prepare($sqlGetLastDictation) ;           
                                //$stmt->bindParam("date_today", $date_today);            
                                $stmt->execute();
                                $newEnc = $stmt->fetchAll(PDO::FETCH_OBJ);
                          
                                if($newEnc)
                                {
                                    $lastInsertedId = $newEnc[0]->lastInsertedId;
                                }
                            
                                $sql2 = "INSERT INTO forms (date,encounter,form_name,form_id,pid,user,groupname,authorized,deleted,formdir)
                               values (NOW(), :encounter, 'facetoface', $lastInsertedId,:pid,:username,'Default', 1, 0, 'face_to_face')";

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
                    echo '[{"id":"-12"}]';  
                }
            
  //$sql = "INSERT INTO books (title,author) VALUES (:title,:author)";
  
 }
 catch(PDOException $e) 
    {   
        echo '{"error":{"text":'. $e->getMessage() .'}}'; 
    }
}

// lab requisition encounter
function createLabRequest($pid,$loginProvderId,$encounter,$date_of_request,$specimen_week,$fasting,$frail_health,$is_home_bound,$is_preference_home_health,$diagnosis_codes,$tests,$is_colonoscopy_required,$patient_has,$physician_signature,$date_of_signature)
{
  try
  {
  $db = getConnection();
	$rs=getUserName($loginProvderId);
		if($rs)
		{
			$username = $rs[0]['username'];
                       


  $insert_form_lab_requisition_Sql ="INSERT INTO tbl_form_lab_requisition (pid, encounter, created_by, date_of_request, specimen_week
  ,fasting
  ,frail_health
  ,is_home_bound
  ,is_preference_home_health
  ,diagnosis_codes
  ,tests
  ,is_colonoscopy_required
  ,patient_has
  ,physician_signature
  ,date_of_signature
  ,created_date
  )
  values (:pid,:encounter,:loginProvderId,:date_of_request,:specimen_week,:fasting,
:frail_health,:is_home_bound,:is_preference_home_health,:diagnosis_codes,:tests,:is_colonoscopy_required,:patient_has,:physician_signature,:date_of_signature,NOW())";

  $q = $db->prepare($insert_form_lab_requisition_Sql);
  
  if($q->execute(array( ':pid'=>$pid,':loginProvderId'=>$loginProvderId,
        ':encounter'=>$encounter,
        ':date_of_request'=>$date_of_request,
        ':specimen_week'=>$specimen_week,
        ':fasting'=>$fasting,
        ':frail_health'=>$frail_health,
        ':is_home_bound'=>$is_home_bound,
	':is_preference_home_health'=>$is_preference_home_health,
        ':diagnosis_codes'=>$diagnosis_codes,
        ':tests'=>$tests,
        ':is_colonoscopy_required'=>$is_colonoscopy_required,
        ':patient_has'=>$patient_has,
        ':physician_signature'=>$physician_signature,
        ':date_of_signature'=>$date_of_signature
       )))

        {
                                        
                                $sqlGetLastDictation="SELECT MAX(id) as lastInsertedId 
                                     FROM tbl_form_lab_requisition";

                                $db = getConnection();
                                $stmt = $db->prepare($sqlGetLastDictation) ;           
                                //$stmt->bindParam("date_today", $date_today);            
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
        echo '[{"id":"-12"}]';                 
    }
  }
  catch(PDOException $e) 
  {      
    echo '{"error":{"text":'. $e->getMessage() .'}}'; 
  }
}

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
                //returns patients appointment list

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

//function createVital($pid,$loginProvderId,$bps,$bpd,$weight,$height,$temperature,$temp_method,$pulse,$respiration,$note,$BMI,$BMI_status,$waist_circ,$head_circ,$oxygen_saturation)
function createVital($pid,$loginProvderId,$encounter,$bps,$bpd,$weight,$height,$temperature,$temp_method,$pulse,$respiration,$note,$BMI,$BMI_status,$waist_circ,$head_circ,$oxygen_saturation)
{
    
  try
  {
    $db = getConnection();
	$rs=getUserName($loginProvderId);
		if($rs)
		{
			$username = $rs[0]['username'];
                        $insert_form_vital_Sql="INSERT INTO form_vitals(date,pid,user,authorized,activity,bps,bpd,weight,height,temperature,temp_method,pulse,respiration,note,BMI,BMI_status,waist_circ,head_circ,oxygen_saturation)
                           VALUES (NOW(),:pid,:loginProvderId,0,1,:bps,:bpd,:weight,:height,:temperature,
:temp_method,:pulse,:respiration,:note,:BMI,:BMI_status,
:waist_circ,:head_circ,:oxygen_saturation)";

    $q = $db->prepare($insert_form_vital_Sql);
  
    if($q->execute(array(':pid'=>$pid,':loginProvderId'=>$loginProvderId,
    ':bps'=>$bps,':bpd'=>$bpd,':weight'=>$weight,':height'=>$height,
    ':temperature'=>$temperature,':temp_method'=>$temp_method,':pulse'=>$pulse,
    ':respiration'=>$respiration,':note'=>$note,':BMI'=>$BMI,':BMI_status'=>$BMI_status,
    ':waist_circ'=>$waist_circ,':head_circ'=>$head_circ,':oxygen_saturation'=>$oxygen_saturation
       )))
                        {
        
                                $sqlGetLastDictation="SELECT MAX(id) as lastInsertedId 
                                     FROM form_vitals";

                                $db = getConnection();
                                $stmt = $db->prepare($sqlGetLastDictation) ;           
                                //$stmt->bindParam("date_today", $date_today);            
                                $stmt->execute();
                                $newEnc = $stmt->fetchAll(PDO::FETCH_OBJ);
                          
                                if($newEnc)
                                {
                                    $lastInsertedId = $newEnc[0]->lastInsertedId;
                                }
                            
                                $sql2 = "INSERT INTO forms (date,encounter,form_name,form_id,pid,user,groupname,authorized,deleted,formdir)
                               values (NOW(), :encounter, 'vital', $lastInsertedId,:pid,:username,'Default', 1, 0, 'vitals')";

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
                    echo '[{"id":"-12"}]';  
                    
                }
    
  }
  catch(PDOException $e) 
  {      
    echo '{"error":{"text":'. $e->getMessage() .'}}'; 
  } 
}        

function createSoap($pid,$loginProvderId,$encounter,$subjective,$objective,$assessment,$plan)
{
    try
    {
        $db = getConnection();
	$rs=getUserName($loginProvderId);
        if($rs)
        {
                $username = $rs[0]['username'];
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
                        //$stmt->bindParam("date_today", $date_today);            
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
            echo '[{"id":"-5"}]';  
        }
    }
    catch(PDOException $e) 
    {      
      echo '{"error":{"text":'. $e->getMessage() .'}}'; 
    }        
}
/*
function createNote($pid,$loginProvderId,$note_type,$message,$doctor,$date_of_signature)
{
  try
  {
    $db = getConnection();
 
    $insert_form_soap_Sql="INSERT INTO form_note(date,pid,user,authorized,activity,note_type,message,doctor,date_of_signature)
                           VALUES (NOW(),:pid,:loginProvderId,0,1,:note_type,:message,:doctor,:date_of_signature)";

    $q = $db->prepare($insert_form_soap_Sql);
  
    if($q->execute(array(':pid'=>$pid,':loginProvderId'=>$loginProvderId,
                         ':note_type'=>$note_type,':message'=>$message,
                         ':doctor'=>$doctor,':date_of_signature'=>$date_of_signature
       )))
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
*/
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
            //returns icd9 code list
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

function getUsers()
{
    
    $sql = "SELECT id,username,CONCAT(fname,',',lname) as name FROM users
            WHERE fname!='' AND lname!=''";

    try
    {
        $db = getConnection();
        $stmt = $db->prepare($sql) ;
        $stmt->execute();
        $codes = $stmt->fetchAll(PDO::FETCH_OBJ);

        if($codes)
        {
            //returns icd9 code list
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

function getCodesForSearch()
{
    $sql="SELECT ct_key,ct_id,ct_seq,ct_mod,ct_just,ct_mask,ct_fee,ct_rel,ct_nofs,ct_diag,ct_active,ct_label,ct_external,ct_claim,ct_proc,ct_term,ct_problem
          FROM code_types WHERE ct_active=1";
    
    try 
    {
        $db = getConnection();
        $stmt = $db->prepare($sql) ;
        
        $stmt->execute();
        $codes = $stmt->fetchAll(PDO::FETCH_OBJ);           

        if($codes)
        {
            //returns patients appointment list
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
        //print_r($resCodeTypeId);
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
                //returns patients appointment list
                echo json_encode($resSearch); 
            }
            else
            {    
                echo '[{"id":"0"}]';
            }
        }
        else
        {
            echo '[{"id":"-12"}]';// if the code itself doesn't exist
        }
    } 
    catch(PDOException $e) 
    {
        echo '{"error":{"text":'. $e->getMessage() .'}}'; 
    }
    
}

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
            //returns patients appointment list
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
            //returns patients appointment list
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
        //$stmt->bindParam("date_today", $date_today);            
        $stmtProviderId->execute();
        $providerId = $stmtProviderId->fetchAll(PDO::FETCH_OBJ);

        if($providerId)
        {//echo "<br>PRID=".$providerId[0]->provider_id;;
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
            /*
	    $sqlGetLastBilling="SELECT MAX(id) as lastInsertedId 
                                  FROM billing";
                    
            $stmtLastBilling = $db->prepare($sqlGetLastBilling) ;           
            //$stmt->bindParam("date_today", $date_today);            
            $stmtLastBilling->execute();
            $newBilling = $stmtLastBilling->fetchAll(PDO::FETCH_OBJ);

            if($newBilling)
            {
                $lastInsertedId = $newBilling[0]->lastInsertedId;
                echo '[{"id":"'.$lastInsertedId.'"}]';
            }
            else
            {
                echo '[{"id":"-999"}]';
            }
            */
            
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
        $flag=-2;
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
            
            //echo '<br>price='.$price.'<br>';//die;
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
		//$stmt->bindParam("date_today", $date_today);            
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
        if($flag==1 && $i==count($reviewArray))
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
        
	$sqlJustifyAll="SELECT DISTINCT justify FROM billing 
                    WHERE encounter=$encounter AND activity=1";

	    $stmt = $db->prepare($sqlJustifyAll);        
	    $stmt->bindParam("encounter", $encounter);  
	    $stmt->execute();
	    $resJustifyAll = $stmt->fetchAll(PDO::FETCH_OBJ);  
	    $justifyAll='';
	    if($resJustifyAll)
	    {
		//returns patients appointment list
		//echo json_encode($resJustifyAll)."<br>"; 
		$j=0;
		while(isset($resJustifyAll[$j]->justify))
		{
		    if(!empty($resJustifyAll[$j]->justify))
		    {
			$justifyAll.=$resJustifyAll[$j]->justify.",";
		    }
		    $j++;
		}
	
	    }
    
	
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
            //returns patients appointment list
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
            $i++;
        }    
       
        if($flag==1 && $i==count($billArray))
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
    $sqlInsertInsuranceData = "INSERT INTO insurance_data(type,provider,plan_name,policy_number,
     group_number,subscriber_lname,subscriber_mname,
     subscriber_fname,subscriber_relationship,subscriber_ss,
     subscriber_DOB,subscriber_street,subscriber_postal_code,
     subscriber_city,subscriber_state,subscriber_country,subscriber_phone,
     subscriber_employer,subscriber_employer_street,subscriber_employer_postal_code,
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
        
        $title=$patientDetailsArray[0]['salutation'];
        $fname=$patientDetailsArray[0]['fname'];
        $mname=$patientDetailsArray[0]['mname'];
        $lname=$patientDetailsArray[0]['lname'];                
        $DOB=$patientDetailsArray[0]['dob'];        
        $ss=$patientDetailsArray[0]['ssn'];
        $occupation=$patientDetailsArray[0]['occupation'];
        $phone_home=$patientDetailsArray[0]['phone_home'];
        $phone_cell=$patientDetailsArray[0]['phone_cell'];                
        $sex=$patientDetailsArray[0]['sex'];
        $providerID=$patientDetailsArray[0]['providerId'];
        $comments=$patientDetailsArray[0]['comments'];

        $sqlGetMaxPid="SELECT MAX(pid) as max_pid FROM patient_data";
        $stmtGetMaxPid = $db->prepare($sqlGetMaxPid) ;           
                                //$stmt->bindParam("date_today", $date_today);            
        $stmtGetMaxPid->execute();
        $maxPid = $stmtGetMaxPid->fetchAll(PDO::FETCH_OBJ);
        
        $pid = $maxPid[0]->max_pid+1;
        $pubpid=$pid;
                
        // Insert in table 'patient_data'
        $sqlInsertPatientData = "INSERT INTO patient_data(title,fname,mname,lname,DOB,ss,
                                         occupation,phone_home,phone_cell,
                                         date,sex,providerID,pubpid,pid,usertext1)
                                 VALUES('$title','$fname','$mname','$lname','$DOB','$ss',
                                        '$occupation','$phone_home','$phone_cell',
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

function getAllFacilities()
{
    $sql="SELECT id,name,phone,fax,street,city,state,postal_code,country_code,federal_ein,website,email,service_location,billing_location,accepts_assignment,pos_code,x12_sender_id,attn,domain_identifier,facility_npi,tax_id_type,color,primary_business_entity
          FROM facility";
    
    try 
    {
        $db = getConnection();
        $stmt = $db->prepare($sql) ;
        $stmt->execute();
        $facilities = $stmt->fetchAll(PDO::FETCH_OBJ);           

        if($facilities)
        {
            //returns patients appointment list
            echo json_encode($facilities); 
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

function getPatientsByFacility($fid,$loginProvderId)
{
    
    $sql="SELECT DISTINCT pd.pid,pd.title,pd.fname,pd.lname, DATE_FORMAT(pd.DOB,'%m-%d-%Y') AS DOB,DATE_FORMAT(FROM_DAYS(DATEDIFF(CURDATE(),pd.DOB)), '%Y')+0 AS age,if (pd.sex = 'Female' ,'F','M' ) as sex,pd.street,pd.city,pd.state,pd.country_code,pd.postal_code,pd.phone_cell
          FROM patient_data pd INNER JOIN 
          openemr_postcalendar_events ope
          ON pd.pid=ope.pc_pid
          WHERE pd.providerID=$loginProvderId
          AND ope.pc_facility = $fid          
          GROUP BY ope.pc_apptstatus";
   /* 
    $sql="SELECT DISTINCT pd.pid,pd.title,pd.fname,pd.lname, DATE_FORMAT(pd.DOB,'%m-%d-%Y') AS DOB,DATE_FORMAT(FROM_DAYS(DATEDIFF(CURDATE(),pd.DOB)), '%Y')+0 AS age,if (pd.sex = 'Female' ,'F','M' ) as sex,pd.street,pd.city,pd.state,pd.country_code,pd.postal_code,pd.phone_cell
          FROM patient_data pd INNER JOIN 
          openemr_postcalendar_events ope
          ON pd.pid=ope.pc_pid
          WHERE pd.providerID=$loginProvderId
          AND ope.pc_facility = $fid
          AND ope.pc_apptstatus = ''
          GROUP BY ope.pc_apptstatus";
    */
    
    try 
    {
        $db = getConnection();
        $stmt = $db->prepare($sql) ;
        $stmt->execute();
        $patients = $stmt->fetchAll(PDO::FETCH_OBJ);           

        if($patients)
        {
            //returns patients appointment list
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



function getVisitCategories()
{
    
    try 
    {
        $db = getConnection();
        $sql="SELECT pc_catid,pc_catname FROM openemr_postcalendar_categories 
              WHERE pc_cattype=0";
    
        $stmt = $db->prepare($sql) ;
        $stmt->execute();
        $visitcategories = $stmt->fetchAll(PDO::FETCH_OBJ);           

        if($visitcategories)
        {
            //returns patients appointment list
            echo json_encode($visitcategories); 
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



?>
