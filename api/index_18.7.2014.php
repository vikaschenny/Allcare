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

// method to get list of todays appointments for given provider
$app->get('/patients/:loginUserId', 'getPatients');
/* hema */
// method to get patient details

$app->get('/patientdetails/:pid', 'getPatientDetails');
// method to get list of all patients

$app->get('/allpatientsdetails/:all', 'getAllPatientsDetails');
// method to get list of all pharmacies

$app->get('/allpharmacies/:all', 'getAllPharmacies');
/* =============================================== */

// method to get list of all patients

$app->get('/allpatients/:fromCount', 'getAllPatients');
// method to get list of patients belonging to given provider
//$app->get('/filterpatients/:loginProvderId', 'getPatientsByProvider');
$app->get('/filterpatients/:loginProvderId/:fromCount', 'getPatientsByProvider');
// method to get patient demographics
$app->get('/demographics/:patientId', 'getDemographics');
// method to get list of appointments for a given date
$app->get('/patientsbyday/:loginProvderId/:day', 'getPatientsByday');

// method to get list of encounters for given provider

$app->get('/myencounters/:loginProvderId/:patientId', 'getEncounterList');
// method to get list of patients belonging to given provider for messages

$app->get('/mypatients/:loginProvderId/:fromCount','getMyPatients');

// method to create new message
$app->get('/createmessage/:loginProvderId/:pid/:encounter/:body/:title/:msg_status','createMessage');

// method to get the previous dictation
$app->get('/getdictation/:loginProvderId/:pid/:encounter','getDictation');

// method to store speech dictation

$app->get('/speechAudioDictation/:loginProvderId/:pid/:encounter/:dictation/:isNuance','createAudioDictation');

$app->post('/speechDictation','createDictation');

$app->get('/icd9codes','getICD9Codes');
$app->get('/checkencounterexists/:pid/:loginProvderId/:encDate','checkEncounterExists');
//$app->get('/newencounter/:pid/:loginProvderId','createNewEncounter');
$app->get('/newencounter/:pid/:loginProvderId/:encDate','createNewEncounter');
$app->get('/completedformsbyencounter/:pid/:loginProvderId/:encounter','getCompletedFormsByEncounter');

$app->post('/createfacetoface', 'createFacetoFace');

$app->post('/labrequest', 'createLabRequest');

$app->post('/vital', 'createVital');

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

$app->post('/updatebill', 'updateBilling');
$app->post('/savebill', 'saveBilling');

/*     Methods for Fee Sheet/Billing end            */

$app->get('/patientinfo/:pid','getPatientInformation');

$app->post('/createnewpatient', 'createNewPatient');

$app->get('/facilities','getAllFacilities');
$app->get('/patientsbyfacility/:fid/:loginProvderId','getPatientsByFacility');
$app->get('/visitcategories','getVisitCategories');

$app->get('/formreport/:pid/:form_name/:id','form_report');

$app->get('/getsoapform/:pid/:encounterId/:Id','getSoapForm');

$app->get('/getfacetofaceform/:pid/:encounterId/:Id','getFacetoFaceForm');

$app->get('/getlabrequisitionform/:pid/:encounterId/:Id','getLabRequisitionForm'); 

$app->get('/getvitalsform/:pid/:encounterId/:Id','getVitalsForm');

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
	
	$sql = "select pd.pid,pd.title,pd.fname,pd.lname, DATE_FORMAT(pd.DOB,'%m-%d-%Y') AS DOB,DATE_FORMAT(FROM_DAYS(DATEDIFF(CURDATE(),pd.DOB)), '%Y')+0 AS age,if (pd.sex = 'Female' ,'F','M' ) as sex,pd.street,pd.city,pd.state,pd.country_code,pd.postal_code,pd.phone_cell
                from patient_data pd where pd.id not in
                (select pd1.id from patient_data pd1
                join (select * from patient_data order by pid desc limit $fromCount) pd2
                on pd1.id=pd2.id)
                order by pd.pid desc 
                limit 20";      
    
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

// Hema  //

// method to get Patient Details
function getPatientDetails($pid)
{
    	
	$sql = "select pid,fname,lname,dob from patient_data";      
    
        try 
        {
            $db = getConnection();
            $stmt = $db->prepare($sql) ;
            $stmt->bindParam("pid", $pid);            
            $stmt->execute();                       
             
            $patient = $stmt->fetchAll(PDO::FETCH_OBJ);                        
            
            if($patient)
            {
                //returns patients 
                echo json_encode($patient); 
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

// method to get list of all Patients
function getAllPatientsDetails()
{
    	
	$sql = "select pid,fname,lname,dob from patient_data";      
    $count= "select count(*) from patient_data";
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
/* ======================================= */

// method to get list of patients belonging to given provider
// get list of all patients  for the logged in user/provider
function getPatientsByProvider($loginProvderId,$fromCount)
{
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
       GROUP_CONCAT(ifnull(li.`type`,''),if(li.`type` is not null, ':', ''),ifnull(li.title,'')) AS issues,
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
ORDER BY enc.date desc";
        
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
						 form_name='Speech Dictation')
                    AND deleted=0
					order by date desc";
            
                    $stmt = $db->prepare($sql);
                    $stmt->execute();
                    $forms = $stmt->fetchAll(PDO::FETCH_OBJ);
                    if($forms)
                    {
                        //returns forms
                        $j=0;
                        $encounters[$i]->form_name='';
                        while(isset($forms[$j]))
                        {
                            $encounters[$i]->form_name.=$forms[$j]->form_id.'^'.$forms[$j]->form_name.',';
                            $j++;
                        }                       
                        $encounters[$i]->form_name=rtrim($encounters[$i]->form_name,',');
                    }
                    else
                    {
                        $encounters[$i]->form_name='';
                    }
                    $i++;                    
                }
                
                echo json_encode($encounters); 
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
	catch(PDOException $e) 
    {   
        echo '{"error":{"text":'. $e->getMessage() .'}}'; 
    }
}

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
                                  AND DATE(fd.date)=DATE(now())
				  AND f.deleted=0
				  AND f.form_name='Speech Dictation'";

            $stmtGetDictationText = $db->prepare($sqlGetDictationText) ;           
            $stmtGetDictationText->execute();
            $dictationText = $stmtGetDictationText->fetchAll(PDO::FETCH_OBJ);
        
            if($dictationText)
            {
                //if encounter already exists return encounter id                
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

function createAudioDictation($loginProvderId, $pid, $encounter, $dictation, $isNuance)
{
    try
    {
        $db = getConnection();
		$rs=getUserName($loginProvderId);


        if($rs)
        {
            $username = $rs[0]['username'];       	

	$sqlCheckDictationExists="SELECT f.form_id,fd.pid,fd.date,fd.user,fd.dictation
                                  FROM form_dictation fd
				INNER JOIN forms f ON
				fd.id=f.form_id
                                  WHERE fd.pid=$pid
                                  AND fd.user='$username'
				  AND DATE(fd.date)=DATE(now())
			          AND f.form_name='Speech Dictation'
				  AND f.deleted=0";


$stmtCheckDictationExists = $db->prepare($sqlCheckDictationExists) ;           
           
            $stmtCheckDictationExists->execute();
            $exists = $stmtCheckDictationExists->fetchAll(PDO::FETCH_OBJ);
               
		if(trim($dictation)=='~')
    			$dictation='';  

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
		
    }
    catch(PDOException $e) 
    {   
        echo '{"error":{"text":'. $e->getMessage() .'}}'; 
    }
}

function createDictation()
//function createDictation($dictationArray)
{
    
    try
    {
        $db = getConnection();
        $request = Slim::getInstance()->request();
	$dictationArray = json_decode($request->getBody(),TRUE);

        
        $pid=$dictationArray[0]['pid'];   
        $loginProvderId=$dictationArray[0]['loginProviderId'];   
        $encounter=$dictationArray[0]['encounter'];   
        $dictation=$dictationArray[0]['dictation'];   
        $isNuance=$dictationArray[0]['isNuance'];
        
        $rs=getUserName($loginProvderId);
        if($rs)
        {
            $username = $rs[0]['username'];
   
	$sqlCheckDictationExists="SELECT f.form_id,fd.pid,fd.date,fd.user,fd.dictation
                                  FROM form_dictation fd
				INNER JOIN forms f ON
				fd.id=f.form_id
                                  WHERE fd.pid=$pid
                                  AND fd.user='$username'
				  AND DATE(fd.date)=DATE(now())
			          AND f.form_name='Speech Dictation'
				  AND f.deleted=0";

$stmtCheckDictationExists = $db->prepare($sqlCheckDictationExists) ;           
           
            $stmtCheckDictationExists->execute();
            $exists = $stmtCheckDictationExists->fetchAll(PDO::FETCH_OBJ);
               
		if(trim($dictation)=='~')
    			$dictation='';
   

            if(!$exists)
            {
                $sql = "INSERT INTO form_dictation (date, pid, user, groupname, authorized, activity, dictation)
                        VALUES (NOW(), $pid, '$username', 'Default', 1, 1, '$dictation')";

                $q = $db->prepare($sql);

                if($q->execute())
                {

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
                        echo '[{"id":"1","msg":"Insert done"}]';
                    }
                    else
                    {
                        echo '[{"id":"0","msg":"Insert NOT done"}]';
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
                    echo '[{"id":"1","msg":"Update done"}]';
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



function checkEncounterExists($pid,$loginProvderId,$encDate)
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
            echo '[{"id":"0"}]';
        }
    }
    catch(PDOException $e)
    {
        echo '{"error":{"text":'. $e->getMessage() .'}}';         
    }        
}


function createNewEncounter($pid,$loginProvderId,$encDate)
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
                /*$sqlGetLastEncounter="SELECT encounter FROM form_encounter 
                            WHERE pid=$pid 
                            AND provider_id=$loginProvderId";*/
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
}




//function createNewEncounter($pid,$loginProvderId,$date_today)
function createNewEncounter($pid,$loginProvderId)
{	
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
		
                $sqlGetMaxEncounter="SELECT MAX(encounter)+1 as max_encounter 
                                     FROM form_encounter";
                                
                $stmt = $db->prepare($sqlGetMaxEncounter) ;               
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
        
        /*$qryGetForms="SELECT id,date,encounter,form_name,form_id,pid,user
                      FROM forms
                      WHERE encounter=$encounter AND pid=$pid 
                      AND user='$username' AND DATE(date)=DATE(now()) AND deleted=0";*/
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

        if($resIsFeeSheet)
        {      
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

// face to face encounter

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
		$txtNurse=$facetofaceArray['nurse_practitioner_signature'];
		$txtNursePractitionerSignDate=$facetofaceArray['nurse_practitioner_signature_date'];
		$txtPhysicianSignature=$facetofaceArray['physician_signature'];
		$txtPrintedName=$facetofaceArray['printed_name'];
		$txtPrintedDate=$facetofaceArray['printed_name_date'];
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
			
			if($Id==0)
			{
				$insert_form_FacetoFace_Sql ="INSERT INTO tbl_form_facetoface(pid,encounter,is_home_bound,is_hhc_needed,other_physician,                                                   is_house_visit_needed,medical_condition,necessary_hhs,nursing,physical_therapy,occupational_therapy,speech,                                                             care_treatment,support_service_reason,patient_homebound_reason,nurse_practitioner_signature,nurse_practitioner_signature_date,physician_signature,
				printed_name,printed_name_date,created_date,date_of_service)
				VALUES(:pid,:encounter,:radBound,:radCare,:radPhysician,:radVisit,:txtMedical,:nextVisitDays,:txtNursing,:txtPhysical,
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
                                                          SET pid=:pid,encounter=:encounter,is_home_bound=:radBound,is_hhc_needed=:radCare,
                                                          other_physician=:radPhysician,is_house_visit_needed=:radVisit,medical_condition=:txtMedical,
                                                          necessary_hhs=:nextVisitDays,nursing=:txtNursing,physical_therapy=:txtPhysical,
                                                          occupational_therapy=:txtOccupational,speech=:txtSpeech,
                                                          care_treatment=:txtTreatment,support_service_reason=:txtFindings,
                                                          patient_homebound_reason=:txtHomeBound,nurse_practitioner_signature=:txtNurse,
                                                          nurse_practitioner_signature_date=:txtNursePractitionerSignDate,physician_signature=:txtPhysicianSignature,
                                                          printed_name=:txtPrintedName,printed_name_date=:txtPrintedDate,date_of_service=:txtVisitDate
                                                          WHERE Id=:Id";

				$q = $db->prepare($update_form_FacetoFace_Sql);
				if($q->execute(array( ':Id'=>$Id,
                    ':pid'=>$pid,
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


// lab requisition encounter

function createLabRequest()
//function createLabRequest($labrequestArray)
{
  try
  {
        $db = getConnection();
        $flag=0;
		$request = Slim::getInstance()->request();
		$labrequestArray = json_decode($request->getBody(),TRUE);
		//print_r($labrequestArray);
		//$app = \Slim\Slim::getInstance();
	
		$Id=$labrequestArray['id'];
        $pid=$labrequestArray['pid'];
        $encounter=$labrequestArray['encounter'];
        $loginProvderId=$labrequestArray['loginProviderId'];
        $date_of_request=$labrequestArray['date_of_request'];
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
        $is_colonoscopy_required=$labrequestArray['is_colonoscopy_required'];
        $patient_has=$labrequestArray['patient_has'];
        $physician_signature=$labrequestArray['physician_signature'];                        

        $date_of_signature=$labrequestArray['date_of_signature'];
		
			$date_of_request =($date_of_request=='~')?'':$date_of_request;
            $specimen_week  =($specimen_week=='~')?'':$specimen_week;
            $fasting  =($fasting=='~')?'':$fasting;
            $frail_health =($frail_health=='~')?'':$frail_health;
            $is_home_bound =($is_home_bound=='~')?'':$is_home_bound;
            $is_preference_home_health =($is_preference_home_health=='~')?'':$is_preference_home_health;
            $is_colonoscopy_required=($is_colonoscopy_required=='~')?'':$is_colonoscopy_required;
            $patient_has =($patient_has=='~')?'':$patient_has;
            $physician_signature =($physician_signature=='~')?'':$physician_signature;
			$date_of_signature =($date_of_signature=='~')?'':$date_of_signature;            
		
		$rs=getUserName($loginProvderId);
	
		if($rs)
		{
			$username = $rs[0]['username'];  

           if($Id==0)
           {
  $insert_form_lab_requisition_Sql ="INSERT INTO tbl_form_lab_requisition (pid, encounter,created_by,date_of_request,specimen_week,fasting,frail_health,is_home_bound,
is_preference_home_health,diagnosis_codes,tests,is_colonoscopy_required,patient_has,
physician_signature,date_of_signature,created_date)
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
 SET pid=$pid, encounter=$encounter,created_by=$loginProvderId,date_of_request='$date_of_request',specimen_week='$specimen_week',
fasting='$fasting',frail_health='$frail_health',is_home_bound='$is_home_bound',
is_preference_home_health='$is_preference_home_health',
diagnosis_codes='$diagnosis_codes',tests='$tests',is_colonoscopy_required='$is_colonoscopy_required',
patient_has='$patient_has',physician_signature='$physician_signature',
date_of_signature='$date_of_signature'
   WHERE Id=$Id ";
    
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
		
$array2['plan_name'] = "";
$array2['policy_number'] = "";
$array2['group_number'] = "";
$array2['insurance_company'] = "";
$array3 = (!empty($insuranceinfo)) ? get_object_vars($insuranceinfo[0]):$array2;


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

function createVital()
//function createVital($vitalsArray)
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


function createSoap()
//function createSoap($soapArray)
{
    try
    {
        $db = getConnection();
        $flag=0;
        $request = Slim::getInstance()->request();
        $soapArray = json_decode($request->getBody(),TRUE);
	
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

function getCodesForSearch()
{

    $sql="SELECT ct_key,ct_id,ct_seq,ct_mod,ct_just,ct_mask,ct_fee,ct_rel,ct_nofs,ct_diag,ct_active,ct_label,ct_external,ct_claim,ct_proc,ct_term,ct_problem
          FROM code_types WHERE ct_active=1 AND ct_claim=1";
    
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

function getSearchResult($codeSelected,$txtSearch)
{
    $db = getConnection();
            
    try 
    {        
        $sqlGetCode="SELECT ct_id FROM code_types 
		     WHERE ct_key='$codeSelected'
		     LIMIT 100";
        $stmtGetCode = $db->prepare($sqlGetCode) ;
        $stmtGetCode->bindParam("codeSelected", $codeSelected);            
        
        $stmtGetCode->execute();
        $resCodeTypeId = $stmtGetCode->fetchAll(PDO::FETCH_OBJ);
        
        if($resCodeTypeId)
        {
        $codeTypeId = $resCodeTypeId[0]->ct_id;
       
        $sql="SELECT '4' as code_external, icd9_dx_code.formatted_dx_code as code, icd9_dx_code.long_desc as code_text, icd9_dx_code.short_desc as code_text_short, codes.id, codes.code_type, codes.modifier, codes.units, codes.fee, codes.superbill, codes.related_code, codes.taxrates, codes.cyp_factor, codes.active, codes.reportable, codes.financial_reporting, 'ICD9' as code_type_name FROM icd9_dx_code LEFT OUTER JOIN `codes` ON icd9_dx_code.formatted_dx_code = codes.code AND codes.code_type = $codeTypeId WHERE (1=1 AND icd9_dx_code.long_desc LIKE '%$txtSearch%' ) AND icd9_dx_code.active='1' AND (codes.active = 1 || codes.active IS NULL) ORDER BY icd9_dx_code.formatted_dx_code+0,icd9_dx_code.formatted_dx_code";
             
        $stmt = $db->prepare($sql) ;                           
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
	
	$reviewArray = json_decode($request->getBody(),TRUE);	
                          
        $i=0;
        while(isset($reviewArray[$i]['pid']))
        {
            
            $pid=$reviewArray[$i]['pid']; 
            $loginProviderId=$reviewArray[$i]['loginProviderId'];
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
            
            $stmt = $db->prepare($sql) ;
            
            if($stmt->execute())
            {                
                $flag=1;
            }
            else
            {                
                $flag=0;
            }
                                       
            $i++;
        }    
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
		$j=0;		

		while(isset($resJustifyAll[$j]->code_type))
		{
		    $justifyAll.=$resJustifyAll[$j]->code_type."|".$resJustifyAll[$j]->code.":";		   
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
		$i=0;
		$pid=$billArray[0]['pid'];
	  	$loginProviderId=$billArray[0]['loginProviderId'];
	  	$rendProviderId=$billArray[0]['rendProviderId'];
		$superProviderId=$billArray[0]['superProviderId'];
		$encounter=$billArray[0]['encounterId'];
		$pricelevel=$billArray[0]['pricelevel'];		
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
         FROM facility WHERE service_location!=0"; 
    try 
    {
        $db = getConnection();
        $stmt = $db->prepare($sql) ;
        $stmt->execute();
        $facilities = $stmt->fetchAll(PDO::FETCH_OBJ);           

        if($facilities)
        {
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
  
    
    try 
    {
        $db = getConnection();
        $stmt = $db->prepare($sql) ;
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

function getSoapForm($pid, $encounterId,$Id)
{

	$sql = "SELECT fs.Id,fs.subjective,fs.objective,fs.assessment,fs.plan FROM form_soap as fs
             INNER JOIN forms as fm on fs.id=fm.form_id WHERE fs.pid=$pid and fm.encounter=$encounterId and fs.Id=$Id ";
   try
  {
     
     $db = getConnection();
     $stmt = $db->prepare($sql) ;
    
        $stmt->execute();
        $facetofacesoapdetails = $stmt->fetchAll(PDO::FETCH_OBJ);           

        if($facetofacesoapdetails)
        {
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

function getLabRequisitionForm($pid, $encounterId, $Id)
{

   $sql="SELECT id,date_of_request, specimen_week,fasting,frail_health,is_home_bound,is_preference_home_health,diagnosis_codes,
tests,is_colonoscopy_required,patient_has,physician_signature,date_of_signature from tbl_form_lab_requisition where pid=:pid and encounter=:encounterId and Id=:Id";
    
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

function getVitalsForm($pid, $encounterId,$Id)
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



function createAppointment()
//function createAppointment($appointmentDetailsArray)
{
    try
    {
        $db = getConnection();
        $request = Slim::getInstance()->request();
	$appointmentDetailsArray = json_decode($request->getBody(),TRUE);
        //print_r($appointmentDetailsArray);
        
        $pc_aid=$appointmentDetailsArray['loginProviderId'];
        $pc_pid=$appointmentDetailsArray['pid'];        
        $pc_catid=$appointmentDetailsArray['category'];                
        //$pc_time=$appointmentDetailsArray['pc_time'];
        //$pc_title=$appointmentDetailsArray['title'];
        
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
		
		if($am_pm=='PM')
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
(pc_catid, pc_aid, pc_pid, pc_time, pc_hometext,pc_informant, pc_eventDate, pc_endDate, pc_duration, pc_recurrtype, pc_recurrspec, pc_startTime, pc_endTime, pc_alldayevent, pc_apptstatus, pc_eventstatus, pc_facility ) 
VALUES ('$pc_catid','$pc_aid','$pc_pid',now(),'$pc_hometext','$pc_informant','$pc_eventDate','$pc_endDate','$pc_duration','$pc_recurrtype','$pc_recurrspec','$pc_startTime','$pc_endTime','$pc_alldayevent','$pc_apptstatus','$pc_eventstatus','$pc_facility')";

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
		echo '[{"id":"0","msg":"'.$checkSlotRepeatAppointment.'"}]';
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
            echo '[{"id":"0","msg":"An appointment is fixed from '.$from.' and '.$to.'"}]';           
            
            //echo '[{"id":"0"}]';
        }

                
    }
    catch(PDOException $e) 
    {
        echo '{"error":{"text":'. $e->getMessage() .'}}'; 
    }
}
    
function getAvailableTimeSlots($loginProviderId,$startingDate)
{
    
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
                echo '[{"available_slots":"'.$actual_available_slots.'","slot_duration":"'.($slot_duration/60).'","day_count_str":"'.$day_count_str.'"}]';
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
	    	echo '[{"available_slots":"'.$all_time_slots_available.'","slot_duration":"'.($slot_duration/60).'","day_count_str":"'.$day_count_str.'"}]';
	   
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
	    	echo '[{"available_slots":"'.$all_time_slots_available.'","slot_duration":"'.($slot_duration/60).'","day_count_str":"'.$day_count_str.'"}]';
	   
        }
		
    }

    catch(PDOException $e)
    {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
        
}
   

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
?>
