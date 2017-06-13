<?php

/*
Webservice for user/provider authentication with openemr
*/

// do initial application

require 'Slim/Slim.php';
require 'Slim/Route.php';
/*
require '../interface/globals.php';
require '../library/globals.inc.php';
*/
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
// method to get list of encounters for given provider
//$app->get('/myencounters/:loginProvderId', 'getEncounterList');
$app->get('/myencounters/:loginProvderId/:patientId', 'getEncounterList');
// method to get list of patients belonging to given provider for messages
//$app->get('/mypatients/:loginProvderId','getMyPatients');
$app->get('/mypatients/:loginProvderId/:fromCount','getMyPatients');

// method to create new message
$app->get('/createmessage/:loginProvderId/:pid/:encounter/:body/:title/:msg_status','createMessage');
// method to store speech dictation
$app->get('/speechDictation/:loginProvderId/:pid/:encounter/:dictation','createDictation');

$app->get('/icd9codes','getICD9Codes');

//$app->get('/newencounter/:pid/:loginProvderId/:date_today','createNewEncounter');
$app->get('/newencounter/:pid/:loginProvderId','createNewEncounter');

$app->get('/createfacetoface/:pid/:loginProvderId/:encounter/:radBound/:radCare/:radPhysician/:radVisit/:txtMedical/:nextVisitDays/:txtNursing/:txtPhysical/:txtOccupational/:txtSpeech/:txtFindings/:txtTreatment/:txtHomeBound/:txtNurse/:txtNursePractitionerSignDate/:txtPhysicianSignature/:txtPrintedName/:txtPrintedDate/:txtVisitDate','createFacetoFace');

$app->get('/labrequest/:pid/:loginProvderId/:encounter/:date_of_request/:specimen_week/:fasting/:frail_health/:is_home_bound/:diagnosis_codes/:tests/:is_colonoscopy_required/:patient_has/:physician_signature/:date_of_signature','createLabRequest');

//   /vital/1/1/55/99/50/60/50/Auxillary/60/40/opop/20/ppp/3/5/60
$app->get('/vital/:pid/:loginProvderId/:encounter/:bps/:bpd/:weight/:height/:temperature/:temp_method/:pulse/:respiration/:note/:BMI/:BMI_status/:waist_circ/:head_circ/:oxygen_saturation','createVital');

$app->get('/soap/:pid/:loginProvderId/:encounter/:subjective/:objective/:assessment/:plan','createSoap');
$app->get('/note/:pid/:loginProvderId/:note_type/:message/:doctor/:date_of_signature','createNote');

// Methods required for Fee Sheet/Billing
$app->get('/users','getUsers');
$app->get('/encounterdates/:pid/:encounter','getEncounterDates');// For date drop down in Review button pop-up

//$app->get('/billing/:pid/:loginProvderId/:encounter','generateBill');
//$app->get('/billing/:pid/:loginProvderId/:encounter/:ProviderID/:SupervisorID/:default_warehouse/:bill_value/:prod/:contrastart','generateBill');

/*
$_POST['ProviderID'];$_POST['ProviderID'];
$_POST['SupervisorID'];
$_POST['default_warehouse'];
$_POST['bill'];
$_POST['prod'];	--- array()
$_POST['contrastart'];
*/

/*     Methods for Fee Sheet/Billing end            */

$app->get('/patientinfo/:pid','getPatientInformation');

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

	$sql = "SELECT pd.pid,pd.title,pd.fname,pd.lname,if (pd.sex = 'Female' ,'F','M' ) as sex,
                DATE_FORMAT(pd.DOB,'%m-%d-%Y') as DOB,DATE_FORMAT(FROM_DAYS(DATEDIFF(CURDATE(),pd.DOB)), '%Y')+0 AS age,pd.street,pd.city,pd.state,pd.country_code,pd.postal_code,pd.phone_home,pd.phone_biz,pd.phone_contact,pd.phone_cell, DATE_FORMAT(ope.pc_eventDate,'%m-%d-%Y') as event_date ,if(ope.pc_endDate='0000-00-00',DATE_FORMAT(ope.pc_eventDate,'%m-%d-%y'),DATE_FORMAT(ope.pc_endDate,'%m-%d-%Y'))  as end_date ,ope.pc_duration, TIME_FORMAT(ope.pc_startTime, '%h:%i %p') AS start_time,TIME_FORMAT(ope.pc_endTime, '%h:%i %p') AS end_time,pc.pc_catname
                FROM patient_data pd INNER JOIN openemr_postcalendar_events ope ON pd.pid=ope.pc_pid
                        inner join openemr_postcalendar_categories pc on pc.pc_catid=ope.pc_catid 
                WHERE ope.pc_aid=:loginUserId AND ope.pc_eventdate=CURDATE() AND ope.pc_apptstatus='-'  ";        
    
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
            
            //$stmtCount= $db->prepare("select COUNT(*) as pCount FROM patient_data");
            //$stmtCount->execute();        
            //$total_records= $stmtCount->fetchAll(PDO::FETCH_OBJ);                        
            //echo json_encode($total_records);
             
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
	//$str_newdemoArray=json_encode($newdemoArray);
	//$newdemoArray['Data']=json_decode($str_newdemoArray,true);
	//$newdemoArray.push("Data",json_decode($str_newdemoArray,true));

        //$newdemoArray['Data']=$newdemoArray;

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
/*
	$sql4 = "select pd.occupation, ed.name as employer, ed.street, ed.city, ed.state, ed.country, ed.postal_code
from patient_data pd
inner join employer_data ed
on pd.pid=ed.pid
where ed.pid=:patientId
order by ed.id desc limit 0,1";
*/

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


//$sql8 = "SELECT * FROM lists WHERE pid = :patientId AND type = 'medical_problem' ORDER BY begdate";
$sql8 = "SELECT id,date,type,title,begdate,enddate,returndate,occurrence,classification,referredby,extrainfo,diagnosis,
                activity,comments,pid,user,groupname,outcome,destination,reinjury_id,injury_part,injury_type,
                injury_grade,reaction,external_allergyid,erx_source,erx_uploaded,modifydate 
         FROM lists WHERE pid = :patientId AND type = 'medical_problem' ORDER BY begdate";
//$sql9 = "SELECT * FROM lists WHERE pid = :patientId AND type = 'allergy' ORDER BY begdate";
$sql9 = "SELECT id,date,type,title,begdate,enddate,returndate,occurrence,classification,referredby,extrainfo,diagnosis,
                activity,comments,pid,user,groupname,outcome,destination,reinjury_id,injury_part,injury_type,
                injury_grade,reaction,external_allergyid,erx_source,erx_uploaded,modifydate 
         FROM lists WHERE pid = :patientId AND type = 'allergy' ORDER BY begdate";
//$sql10 = "SELECT * FROM lists WHERE pid = :patientId AND type = 'medication' ORDER BY begdate";
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

//$sql12 = "select * from prescriptions where patient_id=:patientId and active='1'";
$sql12 = "select id,patient_id,filled_by_id,pharmacy_id,date_added,date_modified,provider_id,encounter,start_date,drug,
                 drug_id,rxnorm_drugcode,form,dosage,quantity,size,unit,route,substitute,refills,per_refill,filled_date,
                 medication,note,active,site,prescriptionguid,erx_source,erx_uploaded,drug_info_erx
          from prescriptions where patient_id=:patientId and active='1'";
//$sql13 = "select * from pnotes where pid=:patientId and deleted=0";
$sql13 = "select id,date,body,pid,user,groupname,activity,authorized,title,assigned_to,deleted,message_status 
          from pnotes where pid=:patientId and deleted=0";
//$sql14 = "select * from extended_log where patient_id=:patientId";
$sql14 = "select id,date,event,user,recipient,description,patient_id 
          from extended_log where patient_id=:patientId";
//$sql15 = "select * from form_vitals where pid=:patientId";
$sql15 = "select id,date,pid,user,groupname,authorized,activity,bps,bpd,weight,height,temperature,temp_method,pulse,
                 respiration,note,BMI,BMI_status,waist_circ,head_circ,oxygen_saturation
          from form_vitals where pid=:patientId";

	try 
        {
            $db = getConnection();
            /*
            $stmt1 = $db->prepare($sql1) ;
            $stmt1->bindParam("patientId", $patientId);            
            $stmt1->execute();
            $demo1 = $stmt1->fetchAll(PDO::FETCH_OBJ);
            */

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
			
            /*            
            if(count($demo2) > 0 && !empty($demo2))
            {
                    $rs2 = json_encode($demo2);
                    $newdemo2 = json_decode($rs2,true);
            }
            else
            {
                    $newdemo2[0] = "No Data";
            }        
             */
                        
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
		/*		
            $newdemo['Who'] = ($newdemo1['DataAvailable'] == "Yes")?$newdemo1[0]['DataAvailable']:json_decode('{"DataAvailable":"NO"}');
            $newdemo['Contact'] = ($newdemo2['DataAvailable'] == "Yes")?$newdemo2[0]:json_decode('{"DataAvailable":"NO"}');
            $newdemo['Choices'] = ($newdemo3['DataAvailable'] == "Yes")?$newdemo3[0]:json_decode('{"DataAvailable":"NO"}');
            $newdemo['Employer'] = ($newdemo4['DataAvailable'] == "Yes")?$newdemo4[0]:json_decode('{"DataAvailable":"NO"}');
            $newdemo['Stats'] = ($newdemo5['DataAvailable'] == "Yes")?$newdemo5[0]:json_decode('{"DataAvailable":"NO"}');
            $newdemo['Misc'] = ($newdemo6['DataAvailable'] == "Yes")?$newdemo6[0]:json_decode('{"DataAvailable":"NO"}');
            $newdemo['Insurance'] = ($newdemo7['DataAvailable'] == "Yes")?$newdemo7[0]:json_decode('{"DataAvailable":"NO"}');

            $newdemo['Medical_Problems'] = ($newdemo8['DataAvailable'] == "Yes")?$newdemo8[0]:json_decode('{"DataAvailable":"NO"}');
            $newdemo['Allergies'] = ($newdemo9['DataAvailable'] == "Yes")?$newdemo9[0]:json_decode('{"DataAvailable":"NO"}');
            $newdemo['Medications'] = ($newdemo10['DataAvailable'] == "Yes")?$newdemo10[0]:json_decode('{"DataAvailable":"NO"}');
            $newdemo['Immunization'] = ($newdemo11['DataAvailable'] == "Yes")?$newdemo11[0]:json_decode('{"DataAvailable":"NO"}');
            $newdemo['Prescription'] = ($newdemo12['DataAvailable'] == "Yes")?$newdemo12[0]:json_decode('{"DataAvailable":"NO"}');
            $newdemo['Notes'] = ($newdemo13['DataAvailable'] == "Yes")?$newdemo13[0]:json_decode('{"DataAvailable":"NO"}');
            $newdemo['Disclosure'] = ($newdemo14['DataAvailable'] == "Yes")?$newdemo14[0]:json_decode('{"DataAvailable":"NO"}');
            $newdemo['Vitals'] = ($newdemo15['DataAvailable'] == "Yes")?$newdemo15[0]:json_decode('{"DataAvailable":"NO"}');	

*/	


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
function createMessage($loginProvderId, $pid,$encounter, $body, $title, $msg_status)
{
	try
	{
		$db = getConnection();
		$qry = "select username from users where id=:loginProvderId";
		$stmt = $db->prepare($qry) ;
		$stmt->bindParam("loginProvderId", $loginProvderId);            
		$stmt->execute();
		$rs = $stmt->fetchAll();
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
        
                                //$lastInsertedId=getLastInsertId();
                                //$lastInsertedId=mysql_insert_id();

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
                
                        }
                        else
                        {
                            echo '[{"id":"-1"}]';                 
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


function createDictation($loginProvderId, $pid, $encounter,$dictation)
{
	try
	{
		$db = getConnection();
		$qry = "select username from users where id=:loginProvderId";
		$stmt = $db->prepare($qry) ;
		$stmt->bindParam("loginProvderId", $loginProvderId);            
		$stmt->execute();
		$rs = $stmt->fetchAll();
		if($rs)
		{
			$username = $rs[0]['username'];
			$sql = "INSERT INTO form_dictation (date, pid, user, groupname, authorized, activity, dictation)
			values (NOW(), :pid, :username, 'Default', 1, 1, :dictation)";
                                                                        
			$q = $db->prepare($sql);
                        
			if($q->execute(array(':username'=>$username,
						':pid'=>$pid,
						':dictation'=>$dictation)))
			{
                            
                            $lastInsertedId=mysql_insert_id();
                           
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
			values (NOW(), :encounter, 'Speech Dictation', $lastInsertedId,:pid,:username,'Default', 1, 0, 'dictation')";
                        
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
                //echo '[{"id":"0"}]';   
		$encounter=rand(1000,999999);
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
    
      

           $qry = "select username from users where id=:loginProvderId";
		$stmt = $db->prepare($qry) ;
		$stmt->bindParam("loginProvderId", $loginProvderId);            
		$stmt->execute();
		$rs = $stmt->fetchAll();
		if($rs)
		{
			$username = $rs[0]['username'];
                        $insert_form_FacetoFace_Sql ="INSERT INTO tbl_form_facetoface(pid,encounter,is_home_bound,is_hhc_needed,other_physician,
is_house_visit_needed,medical_condition,necessary_hhs,nursing,physical_therapy,occupational_therapy,speech,
care_treatment,support_service_reason,
patient_homebound_reason,nurse_practitioner_signature,nurse_practitioner_signature_date,physician_signature,
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
        
                                //$lastInsertedId=getLastInsertId();
                                //$lastInsertedId=mysql_insert_id();

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
function createLabRequest($pid,$loginProvderId,$encounter,$date_of_request,$specimen_week,$fasting,$frail_health,$is_home_bound,$diagnosis_codes,$tests,$is_colonoscopy_required,$patient_has,$physician_signature,$date_of_signature)
{
  try
  {
  $db = getConnection();
  /*$conn = $GLOBALS['adodb']['db'];
  $encounter = $conn->GenID("sequences");
  
  $sql = "INSERT INTO form_encounter (date, facility, facility_id, pid, encounter, pc_catid, provider_id, billing_facility)
    values (NOW(), 'Your Clinic Name Here',3,:pid,$encounter,9,:loginProvderId,3 )";
    $q = $db->prepare($sql);
    if($q->execute(array(':pid'=>$pid,
       ':loginProvderId'=>$loginProvderId)))
    {
     echo '[{"id":"1"}]';
    }
    else
    {
     echo '[{"id":"0"}]';                                        
    }
    */

$qry = "select username from users where id=:loginProvderId";
		$stmt = $db->prepare($qry) ;
		$stmt->bindParam("loginProvderId", $loginProvderId);            
		$stmt->execute();
		$rs = $stmt->fetchAll();
		if($rs)
		{
			$username = $rs[0]['username'];
                       


  $insert_form_lab_requisition_Sql ="INSERT INTO tbl_form_lab_requisition (pid, encounter, created_by, date_of_request, specimen_week
  ,fasting
  ,frail_health
  ,is_home_bound
  ,diagnosis_codes
  ,tests
  ,is_colonoscopy_required
  ,patient_has
  ,physician_signature
  ,date_of_signature
  ,created_date
  )
  values (:pid,:encounter,:loginProvderId,:date_of_request,:specimen_week,:fasting,:frail_health,:is_home_bound,:diagnosis_codes,:tests,:is_colonoscopy_required,:patient_has,:physician_signature,:date_of_signature,NOW())";

  $q = $db->prepare($insert_form_lab_requisition_Sql);
  
  if($q->execute(array( ':pid'=>$pid,':loginProvderId'=>$loginProvderId,
        ':encounter'=>$encounter,
        ':date_of_request'=>$date_of_request,
        ':specimen_week'=>$specimen_week,
        ':fasting'=>$fasting,
        ':frail_health'=>$frail_health,
        ':is_home_bound'=>$is_home_bound,
        ':diagnosis_codes'=>$diagnosis_codes,
        ':tests'=>$tests,
        ':is_colonoscopy_required'=>$is_colonoscopy_required,
        ':patient_has'=>$patient_has,
        ':physician_signature'=>$physician_signature,
        ':date_of_signature'=>$date_of_signature
       )))


                        {
        
                                //$lastInsertedId=getLastInsertId();
                                //$lastInsertedId=mysql_insert_id();

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
                               values (NOW(), :encounter, 'labrequest', $lastInsertedId,:pid,:username,'Default', 1, 0, 'lab_requisition')";

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
/*
 $sql = "select concat(pd.fname,' ',pd.lname) as pname , pd.DOB, pd.ss, concat(pd.street,',',pd.city,',',pd.state,',',pd.country_code,'-',pd.postal_code) as address, pd.phone_home,
ic.name as insurance_company, ins.plan_name, ins.policy_number, ins.group_number
from patient_data pd inner join insurance_data ins
on pd.pid=ins.pid
inner join insurance_companies ic on ic.id=ins.provider
where ins.pid=:pid";

        try 
        {
            $db = getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam("pid", $pid);
            $stmt->execute();
            $patientinfo = $stmt->fetchAll(PDO::FETCH_OBJ);
                
            if($patientinfo)
            {
                //returns patients appointment list
                echo json_encode($patientinfo); 
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
*/

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
    //echo '[{"id":"1"}]';
    
  try
  {
    $db = getConnection();
 
    $qry = "select username from users where id=:loginProvderId";
		$stmt = $db->prepare($qry) ;
		$stmt->bindParam("loginProvderId", $loginProvderId);            
		$stmt->execute();
		$rs = $stmt->fetchAll();
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
        
                                //$lastInsertedId=getLastInsertId();
                                //$lastInsertedId=mysql_insert_id();

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
        $qry = "select username from users where id=:loginProvderId";
		$stmt = $db->prepare($qry) ;
		$stmt->bindParam("loginProvderId", $loginProvderId);            
		$stmt->execute();
		$rs = $stmt->fetchAll();
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
        
                                //$lastInsertedId=getLastInsertId();
                                //$lastInsertedId=mysql_insert_id();

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
                               values (NOW(), :encounter, 'Soap', $lastInsertedId,:pid,:username,'Default', 1, 0, 'soap')";

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

//generateBill($pid,$loginProvderId,$encounter)

function getUsers()
{
    
    $sql = "SELECT id,username,CONCAT(fname,',',lname) as name FROM users";

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
            //echo 'No Patient available';
            echo '[{"id":"0"}]';
        }
    } 
    catch(PDOException $e) 
    {
        echo '{"error":{"text":'. $e->getMessage() .'}}'; 
    }
}

/*
function generateBill($pid,$loginProvderId,$encounter,$ProviderID,$SupervisorID,$default_warehouse,$bill_value,$prod,$contrastart)
{
    
//    
//    if ($_POST['bn_save'] || $_POST['bn_save_close']) 
//    {
//  $main_provid = 0 + $_POST['ProviderID'];
//  $main_supid  = 0 + $_POST['SupervisorID'];
//  if ($main_supid == $main_provid) $main_supid = 0;
//  $default_warehouse = $_POST['default_warehouse'];
//
//  $bill = $_POST['bill'];
//  $copay_update = FALSE;
//  $update_session_id = '';
//  $ct0 = '';//takes the code type of the first fee type code type entry from the fee sheet, against which the copay is posted
//  $cod0 = '';//takes the code of the first fee type code type entry from the fee sheet, against which the copay is posted
//  $mod0 = '';//takes the modifier of the first fee type code type entry from the fee sheet, against which the copay is posted
//  for ($lino = 1; $bill["$lino"]['code_type']; ++$lino) 
//  {
//    $iter = $bill["$lino"];
//    $code_type = $iter['code_type'];
//    $code      = $iter['code'];
//    $del       = $iter['del'];
//
//    // Skip disabled (billed) line items.
//    if ($iter['billed']) continue;
//
//    $id = $iter['id'];
//    $modifier = trim($iter['mod']);
//    if( !($cod0) && ($code_types[$code_type]['fee'] == 1) ){
//      $mod0 = $modifier;
//      $cod0 = $code;
//      $ct0 = $code_type;
//    }
//    $units     = max(1, intval(trim($iter['units'])));
//    $fee       = sprintf('%01.2f',(0 + trim($iter['price'])) * $units);
//    
//    if($code_type == 'COPAY'){
//      if($id == ''){
//        //adding new copay from fee sheet into ar_session and ar_activity tables
//        if($fee < 0){
//          $fee = $fee * -1;
//        }
//        $session_id = idSqlStatement("INSERT INTO ar_session(payer_id,user_id,pay_total,payment_type,description,".
//          "patient_id,payment_method,adjustment_code,post_to_date) VALUES('0',?,?,'patient','COPAY',?,'','patient_payment',now())",
//          array($_SESSION['authId'],$fee,$pid));
//        SqlStatement("INSERT INTO ar_activity (pid,encounter,code_type,code,modifier,payer_type,post_time,post_user,session_id,".
//          "pay_amount,account_code) VALUES (?,?,?,?,?,0,now(),?,?,?,'PCP')",
//          array($pid,$encounter,$ct0,$cod0,$mod0,$_SESSION['authId'],$session_id,$fee));
//      }else{
//        //editing copay saved to ar_session and ar_activity
//        if($fee < 0){
//          $fee = $fee * -1;
//        }
//        $session_id = $id;
//        $res_amount = sqlQuery("SELECT pay_amount FROM ar_activity WHERE pid=? AND encounter=? AND session_id=?",
//          array($pid,$encounter,$session_id));
//        if($fee != $res_amount['pay_amount']){
//          sqlStatement("UPDATE ar_session SET user_id=?,pay_total=?,modified_time=now(),post_to_date=now() WHERE session_id=?",
//            array($_SESSION['authId'],$fee,$session_id));
//          sqlStatement("UPDATE ar_activity SET code_type=?, code=?, modifier=?, post_user=?, post_time=now(),".
//            "pay_amount=?, modified_time=now() WHERE pid=? AND encounter=? AND account_code='PCP' AND session_id=?",
//            array($ct0,$cod0,$mod0,$_SESSION['authId'],$fee,$pid,$encounter,$session_id));
//        }
//      }
//      if(!$cod0)
//      {
//        $copay_update = TRUE;
//        $update_session_id = $session_id;
//      }
//      continue;
//    }
//    $justify   = trim($iter['justify']);
//    $notecodes = trim($iter['notecodes']);
//    if ($justify) $justify = str_replace(',', ':', $justify) . ':';
//    // $auth      = $iter['auth'] ? "1" : "0";
//    $auth      = "1";
//    $provid    = 0 + $iter['provid'];
//
//    $ndc_info = '';
//    if ($iter['ndcnum']) {
//    $ndc_info = 'N4' . trim($iter['ndcnum']) . '   ' . $iter['ndcuom'] .
//      trim($iter['ndcqty']);
//    }
//
//    // If the item is already in the database...
//    if ($id) {
//      if ($del) {
//        deleteBilling($id);
//      }
//      else {
//        // authorizeBilling($id, $auth);
//        sqlQuery("UPDATE billing SET code = ?, " .
//          "units = ?, fee = ?, modifier = ?, " .
//          "authorized = ?, provider_id = ?, " .
//          "ndc_info = ?, justify = ?, notecodes = ? " .
//          "WHERE " .
//          "id = ? AND billed = 0 AND activity = 1", array($code,$units,$fee,$modifier,$auth,$provid,$ndc_info,$justify,$notecodes,$id) );
//      }
//    }
//
//    // Otherwise it's a new item...
//    else if (! $del) {
//      $code_text = lookup_code_descriptions($code_type.":".$code);
//      addBilling($encounter, $code_type, $code, $code_text, $pid, $auth,
//        $provid, $modifier, $units, $fee, $ndc_info, $justify, 0, $notecodes);
//    }
//  } // end for
//  
//  //if modifier is not inserted during loop update the record using the first
//  //non-empty modifier and code
//  if($copay_update == TRUE && $update_session_id != '' && $mod0 != ''){
//    sqlStatement("UPDATE ar_activity SET code_type=?, code=?, modifier=?".
//      " WHERE pid=? AND encounter=? AND account_code='PCP' AND session_id=?",
//      array($ct0,$cod0,$mod0,$pid,$encounter,$update_session_id));
//  }
//
//  // Doing similarly to the above but for products.
//  $prod = $_POST['prod'];
//  for ($lino = 1; $prod["$lino"]['drug_id']; ++$lino) {
//    $iter = $prod["$lino"];
//
//    if (!empty($iter['billed'])) continue;
//
//    $drug_id   = $iter['drug_id'];
//    $sale_id   = $iter['sale_id']; // present only if already saved
//    $units     = max(1, intval(trim($iter['units'])));
//    $fee       = sprintf('%01.2f',(0 + trim($iter['price'])) * $units);
//    $del       = $iter['del'];
//
//    // If the item is already in the database...
//    if ($sale_id) {
//      if ($del) {
//        // Zero out this sale and reverse its inventory update.  We bring in
//        // drug_sales twice so that the original quantity can be referenced
//        // unambiguously.
//        sqlStatement("UPDATE drug_sales AS dsr, drug_sales AS ds, " .
//          "drug_inventory AS di " .
//          "SET di.on_hand = di.on_hand + dsr.quantity, " .
//          "ds.quantity = 0, ds.fee = 0 WHERE " .
//          "dsr.sale_id = ? AND ds.sale_id = dsr.sale_id AND " .
//          "di.inventory_id = ds.inventory_id", array($sale_id) );
//        // And delete the sale for good measure.
//        sqlStatement("DELETE FROM drug_sales WHERE sale_id = ?", array($sale_id) );
//      }
//      else {
//        // Modify the sale and adjust inventory accordingly.
//        $query = "UPDATE drug_sales AS dsr, drug_sales AS ds, " .
//          "drug_inventory AS di " .
//          "SET di.on_hand = di.on_hand + dsr.quantity - " . add_escape_custom($units) . ", " .
//          "ds.quantity = ?, ds.fee = ?, " .
//          "ds.sale_date = ? WHERE " .
//          "dsr.sale_id = ? AND ds.sale_id = dsr.sale_id AND " .
//          "di.inventory_id = ds.inventory_id";
//        sqlStatement($query, array($units,$fee,$visit_date,$sale_id) );
//      }
//    }
//
//    // Otherwise it's a new item...
//    else if (! $del) {
//      $sale_id = sellDrug($drug_id, $units, $fee, $pid, $encounter, 0,
//        $visit_date, '', $default_warehouse);
//      if (!$sale_id) die(xlt("Insufficient inventory for product ID") . " \"" . text($drug_id) . "\".");
//    }
//  } // end for
//
//  
//  sqlStatement("UPDATE form_encounter SET provider_id = ?, " .
//    "supervisor_id = ?  WHERE " .
//    "pid = ? AND encounter = ?", array($main_provid,$main_supid,$pid,$encounter) );
//
//  // Save-and-Close is currently IPPF-specific but might be more generally
//  // useful.  It provides the ability to mark an encounter as billed
//  // directly from the Fee Sheet, if there are no charges.
//  if ($_POST['bn_save_close']) {
//    $tmp1 = sqlQuery("SELECT SUM(ABS(fee)) AS sum FROM drug_sales WHERE " .
//      "pid = ? AND encounter = ?", array($pid,$encounter) );
//    $tmp2 = sqlQuery("SELECT SUM(ABS(fee)) AS sum FROM billing WHERE " .
//      "pid = ? AND encounter = ? AND billed = 0 AND " .
//      "activity = 1", array($pid,$encounter) );
//    if ($tmp1['sum'] + $tmp2['sum'] == 0) {
//      sqlStatement("update drug_sales SET billed = 1 WHERE " .
//        "pid = ? AND encounter = ? AND billed = 0", array($pid,$encounter));
//      sqlStatement("UPDATE billing SET billed = 1, bill_date = NOW() WHERE " .
//        "pid = ? AND encounter = ? AND billed = 0 AND " .
//        "activity = 1", array($pid,$encounter));
//    }
//    else {
//      // Would be good to display an error message here... they clicked
//      // Save and Close but the close could not be done.  However the
//      // framework does not provide an easy way to do that.
//    }
//  }
//
//  // More IPPF stuff.
//  if (!empty($_POST['contrastart'])) {
//    $contrastart = $_POST['contrastart'];
//    sqlStatement("UPDATE patient_data SET contrastart = ?" .
//      " WHERE pid = ?", array($contrastart,$pid) );
//  }
//
//  // Note: Taxes are computed at checkout time (in pos_checkout.php which
//  // also posts to SL).  Currently taxes with insurance claims make no sense,
//  // so for now we'll ignore tax computation in the insurance billing logic.
//
//  formHeader("Redirecting....");
//  formJump();
//  formFooter();
//  exit;
//}

         
    if ($_POST['bn_save'] || $_POST['bn_save_close']) 
    {
         
        $main_provid = $ProviderID;
        $main_supid  = $SupervisorID;
        if ($main_supid == $main_provid) $main_supid = 0;
        $default_warehouse = $default_warehouse;

        $bill = $bill_value;
        $copay_update = FALSE;
        $update_session_id = '';
        $ct0 = '';//takes the code type of the first fee type code type entry from the fee sheet, against which the copay is posted
        $cod0 = '';//takes the code of the first fee type code type entry from the fee sheet, against which the copay is posted
        $mod0 = '';//takes the modifier of the first fee type code type entry from the fee sheet, against which the copay is posted
  
        for ($lino = 1; $bill["$lino"]['code_type']; ++$lino) 
        {
            $iter = $bill["$lino"];
            $code_type = $iter['code_type'];
            $code      = $iter['code'];
            $del       = $iter['del'];

    // Skip disabled (billed) line items.
            if ($iter['billed']) continue;

            $id = $iter['id'];
            $modifier = trim($iter['mod']);
            if( !($cod0) && ($code_types[$code_type]['fee'] == 1) )
            {
                $mod0 = $modifier;
                $cod0 = $code;
                $ct0 = $code_type;
            }
            $units = max(1, intval(trim($iter['units'])));
            $fee = sprintf('%01.2f',(0 + trim($iter['price'])) * $units);
    
            if($code_type == 'COPAY')
            {
                  if($id == '')
                  {
                    //adding new copay from fee sheet into ar_session and ar_activity tables
                    if($fee < 0)
                    {
                      $fee = $fee * -1;
                    }

                    $session_id = idSqlStatement("INSERT INTO ar_session(payer_id,user_id,pay_total,payment_type,description,".
                      "patient_id,payment_method,adjustment_code,post_to_date) VALUES('0',".$_SESSION['authId'].",$fee,'patient','COPAY',:pid,'','patient_payment',now())");
                    SqlStatement("INSERT INTO ar_activity (pid,encounter,code_type,code,modifier,payer_type,post_time,post_user,session_id,".
                      "pay_amount,account_code) VALUES ($pid,$encounter,$ct0,$cod0,$mod0,0,now(),".$_SESSION['authId'].",$session_id,$fee,'PCP')");
                  }
                  else
                  {
                    //editing copay saved to ar_session and ar_activity
                    if($fee < 0)
                    {
                        $fee = $fee * -1;
                    }
                    $session_id = $id;
                    $res_amount = sqlQuery("SELECT pay_amount FROM ar_activity 
                                            WHERE pid=:pid AND encounter=:encounter AND session_id=$session_id");
                    if($fee != $res_amount['pay_amount'])
                    {
                        sqlStatement("UPDATE ar_session 
                                      SET user_id=".$_SESSION['authId'].",
                                          pay_total=$fee,modified_time=now(),post_to_date=now() 
                                      WHERE session_id=$session_id");
                        sqlStatement("UPDATE ar_activity 
                                      SET code_type=$ct0, code=$cod0, modifier=$mod0, 
                                          post_user=".$_SESSION['authId'].", post_time=now(),
                                          pay_amount=$fee, modified_time=now() 
                                      WHERE pid=:pid AND encounter=:encounter AND account_code='PCP' AND session_id=$session_id");
                    }
                  }
                  if(!$cod0)
                  {
                    $copay_update = TRUE;
                    $update_session_id = $session_id;
                  }
                  continue;
                }

            $justify   = trim($iter['justify']);
            $notecodes = trim($iter['notecodes']);
            if ($justify) $justify = str_replace(',', ':', $justify) . ':';
            // $auth      = $iter['auth'] ? "1" : "0";
            $auth      = "1";
            $provid    = 0 + $iter['provid'];

    $ndc_info = '';
    if ($iter['ndcnum'])
    {
        $ndc_info = 'N4' . trim($iter['ndcnum']) . '   ' . $iter['ndcuom'] .
        trim($iter['ndcqty']);
    }

    // If the item is already in the database...
    if ($id)
    {
      if ($del)
      {
        deleteBilling($id);
      }
      else
      {
        // authorizeBilling($id, $auth);
        sqlQuery("UPDATE billing SET code = $code, " .
          "units = $units, fee = $fee, modifier = $modifier, " .
          "authorized = $auth, provider_id = $provid, " .
          "ndc_info = $ndc_info, justify = $justify, notecodes = $notecodes " .
          "WHERE id = $id AND billed = 0 AND activity = 1");
      }
    }

    // Otherwise it's a new item...
    else if (! $del) 
    {
      $code_text = lookup_code_descriptions($code_type.":".$code);
      addBilling($encounter, $code_type, $code, $code_text, $pid, $auth,
        $provid, $modifier, $units, $fee, $ndc_info, $justify, 0, $notecodes);
    }
  } // end for
  
  //if modifier is not inserted during loop update the record using the first
  //non-empty modifier and code
  if($copay_update == TRUE && $update_session_id != '' && $mod0 != ''){
sqlStatement("UPDATE ar_activity SET code_type=$ct0, code=$cod0, 
    modifier=$mod0 WHERE pid=:pid AND encounter=:encounter 
    AND account_code='PCP' AND session_id=$update_session_id");
  }

  // Doing similarly to the above but for products.
  $prod = $_POST['prod'];
  for ($lino = 1; $prod["$lino"]['drug_id']; ++$lino)
  {
    $iter = $prod["$lino"];

    if (!empty($iter['billed'])) continue;

    $drug_id   = $iter['drug_id'];
    $sale_id   = $iter['sale_id']; // present only if already saved
    $units     = max(1, intval(trim($iter['units'])));
    $fee       = sprintf('%01.2f',(0 + trim($iter['price'])) * $units);
    $del       = $iter['del'];

    // If the item is already in the database...
    if ($sale_id)
    {
      if ($del)
      {
        // Zero out this sale and reverse its inventory update.  We bring in
        // drug_sales twice so that the original quantity can be referenced
        // unambiguously.
        sqlStatement("UPDATE drug_sales AS dsr, drug_sales AS ds, " .
          "drug_inventory AS di " .
          "SET di.on_hand = di.on_hand + dsr.quantity, " .
          "ds.quantity = 0, ds.fee = 0 WHERE " .
          "dsr.sale_id = $sale_id AND ds.sale_id = dsr.sale_id AND " .
          "di.inventory_id = ds.inventory_id");
        // And delete the sale for good measure.
        sqlStatement("DELETE FROM drug_sales WHERE sale_id = $sale_id");
      }
      else
      {
        // Modify the sale and adjust inventory accordingly.
        $query = "UPDATE drug_sales AS dsr, drug_sales AS ds, " .
          "drug_inventory AS di " .
          "SET di.on_hand = di.on_hand + dsr.quantity - " . add_escape_custom($units) . ", " .
          "ds.quantity = $units, ds.fee = $fee, " .
          "ds.sale_date = $visit_date WHERE " .
          "dsr.sale_id = $sale_id AND ds.sale_id = dsr.sale_id AND " .
          "di.inventory_id = ds.inventory_id";

      }
    }

    // Otherwise it's a new item...
    else if (! $del)
    {
        $sale_id = sellDrug($drug_id, $units, $fee, $pid, $encounter, 0,
        $visit_date, '', $default_warehouse);
        if (!$sale_id) die(xlt("Insufficient inventory for product ID") . " \"" . text($drug_id) . "\".");
    }
  } // end for

  
  sqlStatement("UPDATE form_encounter 
                SET provider_id = $main_provid,
                    supervisor_id = $main_supid 
                WHERE pid = :pid AND encounter = :encounter");

  // Save-and-Close is currently IPPF-specific but might be more generally
  // useful.  It provides the ability to mark an encounter as billed
  // directly from the Fee Sheet, if there are no charges.
  if ($_POST['bn_save_close']) 
  {
    $tmp1 = sqlQuery("SELECT SUM(ABS(fee)) AS sum FROM drug_sales WHERE " .
      "pid = :pid AND encounter = :encounter");
    $tmp2 = sqlQuery("SELECT SUM(ABS(fee)) AS sum FROM billing WHERE " .
      "pid = :pid AND encounter = :encounter AND billed = 0 AND activity = 1");
    if ($tmp1['sum'] + $tmp2['sum'] == 0) 
    {
      sqlStatement("update drug_sales SET billed = 1 WHERE " .
        "pid = :pid AND encounter = :encounter AND billed = 0");
      sqlStatement("UPDATE billing SET billed = 1, bill_date = NOW() WHERE " .
        "pid = :pid AND encounter = :encounter AND billed = 0 AND activity = 1");
    }
    else 
    {
      // Would be good to display an error message here... they clicked
      // Save and Close but the close could not be done.  However the
      // framework does not provide an easy way to do that.
    }
  }

  // More IPPF stuff.
        if (!empty($contrastart)) 
        {
          //$contrastart = $_POST['contrastart'];
          sqlStatement("UPDATE patient_data SET contrastart = :contrastart" .
            " WHERE pid = :pid");
        }

    }

    
$billresult = getBillingByEncounter($pid, $encounter, "*");
    
}        

*/
?>
