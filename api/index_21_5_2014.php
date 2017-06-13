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
$app->get('/createmessage/:loginProvderId/:pid/:body/:title/:msg_status','createMessage');
// method to store speech dictation
$app->get('/speechDictation/:loginProvderId/:pid/:dictation','createDictation');

$app->get('/icd9codes','getICD9Codes');

//$app->get('/newencounter/:pid/:loginProvderId/:date_today','createNewEncounter');
$app->get('/newencounter/:pid/:loginProvderId','createNewEncounter');


$app->get('/createfacetoface/:pid/:encounter/:radBound/:radCare/:radPhysician/:radVisit/:txtMedical/:nextVisitDays/:txtNursing/:txtPhysical/:txtOccupational/:txtSpeech/:txtFindings/:txtTreatment/:txtHomeBound/:txtNurse/:txtNursePractitionerSignDate/:txtPhysicianSignature/:txtPrintedName/:txtPrintedDate/:txtVisitDate','createFacetoFace');

$app->get('/labrequest/:pid/:loginProvderId/:encounter/:date_of_request/:specimen_week/:fasting/:frail_health/:is_home_bound/:diagnosis_codes/:tests/:is_colonoscopy_required/:patient_has/:physician_signature/:date_of_signature','createLabRequest');


$app->get('/vital/:pid:/:loginProvderId/:bps/:bpd/:weight/:height/:temperature/:temp_method/:pulse/:respiration/:note/:BMI/:BMI_status/:waist_circ/:head_circ/:oxygen_saturation','createVital');
$app->get('/soap/:pid/:loginProvderId/:subjective/:objective/:assessment/:plan','createSoap');
$app->get('/note/:pid/:loginProvderId/:note_type/:message/:doctor/:date_of_signature','createNote');


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


	/*$dbhost="localhost";
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
/*
function getAllPatients()
{       
	$sql = "select pd.pid,pd.title,pd.fname,pd.lname, DATE_FORMAT(pd.DOB,'%m-%d-%Y') AS DOB,DATE_FORMAT(FROM_DAYS(DATEDIFF(CURDATE(),pd.DOB)), '%Y')+0 AS age,if (pd.sex = 'Female' ,'F','M' ) as sex,pd.street,pd.city,pd.state,pd.country_code,pd.postal_code,pd.phone_cell
                                FROM patient_data pd
                                order by pd.pid desc limit 100";        
    

        try 
        {
            $db = getConnection();
            $stmt = $db->prepare($sql) ;
            //$stmt->bindParam("loginUserId", $loginUserId);            
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
*/

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

/*
function getEncounterList($loginProvderId)
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
GROUP BY enc.encounter";

		try 
        {
            $db = getConnection();
            $stmt = $db->prepare($sql) ;
            $stmt->bindParam("loginProvderId", $loginProvderId);            
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

// method to get list of patients belonging to given provider for messages
//function getMyPatients($loginProvderId,$fromCount)
//{

//	$sql = "select pd.pid,CONCAT(pd.title,pd.fname,' ',pd.lname) as patient
FROM patient_data pd where pd.providerID=:loginProvderId order by pd.pid desc";


	 $sql = "select pd.pid,CONCAT(pd.title,pd.fname,' ',pd.lname) as patient
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
            $mypatients = $stmt->fetchAll(PDO::FETCH_OBJ);
                        
            if($mypatients)
            {
                //returns patients appointment list
                echo json_encode($mypatients); 
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
*/

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
function createMessage($loginProvderId, $pid, $body, $title, $msg_status)
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

function createDictation($loginProvderId, $pid, $dictation)
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

function getICD9Codes()
{
 $sql = "select dx_code, short_desc from icd9_dx_code limit 100";

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
function createFacetoFace($pid,$encounter,$radBound,$radCare,$radPhysician,$radVisit,$txtMedical,$nextVisitDays,$txtNursing,
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
//echo $insert_form_FacetoFace_Sql;die;
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
    echo '[{"id":"1"}]';
   }
   else
   {
    echo '[{"id":"0"}]';
                                                            //echo $insert_form_FacetoFace_Sql;
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
  
/*
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
  values (926,4,145457,'2014-05-15','week','y','y','n','asthma','CXR,UA','y','Accepted','admin1','2014-05-15',NOW())";
*/

/*
  values (926,4,145457,'2014-05-15','week','y','y','n','asthma','CXR,UA','y','Accepted','admin1','2014-05-15',NOW())

*/
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

function createVital($pid,$loginProvderId,$bps,$bpd,$weight,$height,$temperature,$temp_method,$pulse,$respiration,$note,$BMI,$BMI_status,$waist_circ,$head_circ,$oxygen_saturation)
{
  try
  {
    $db = getConnection();
 
    $insert_form_soap_Sql="INSERT INTO form_vital(date,pid,user,authorized,activity,bps,bpd,weight,height,temperature,temp_method,pulse,respiration,note,BMI,BMI_status,waist_circ,head_circ,oxygen_saturation)
                           VALUES (NOW(),:pid,:loginProvderId,0,1,:bps,:bpd,:weight,:height,:temperature,
:temp_method,:pulse,:respiration,:note,:BMI,:BMI_status,
:waist_circ,:head_circ,:oxygen_saturation)";

    $q = $db->prepare($insert_form_soap_Sql);
  
    if($q->execute(array(':pid'=>$pid,':loginProvderId'=>$loginProvderId,
    ':bps'=>$bps,':bpd'=>$bpd,':weight'=>$weight,':height'=>$height,
    ':temperature'=>$temperature,':temp_method'=>$temp_method,':pulse'=>$pulse,
    ':respiration'=>$respiration,':note'=>$note,':BMI'=>$BMI,':BMI_status'=>$BMI_status,
    ':waist_circ'=>$waist_circ,':head_circ'=>$head_circ,':oxygen_saturation'=>$oxygen_saturation
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

function createSoap($pid,$loginProvderId,$subjective,$objective,$assessment,$plan)
{
  try
  {
    $db = getConnection();
 
    $insert_form_soap_Sql="INSERT INTO form_soap(date,pid,user,authorized,activity,subjective,objective,assessment,plan)
                         VALUES (NOW(),:pid,:loginProvderId,0,1,:subjective,:objective,:assessment,:plan)";

    $q = $db->prepare($insert_form_soap_Sql);
  
    if($q->execute(array( ':pid'=>$pid,':loginProvderId'=>$loginProvderId,
       ':subjective'=>$subjective,':objective'=>$objective,
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


?>
