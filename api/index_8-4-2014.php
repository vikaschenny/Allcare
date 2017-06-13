<?php

/*
Webservice for user/provider authentication with openemr
*/


// do initial application

require 'Slim/Slim.php';
require 'Slim/Route.php';

// initialize app
$app = new Slim();

// method for provider login
$app->get('/login/:username/:password', 'loginUser');
// method to get list of todays appointments for given provider
$app->get('/patients/:loginUserId', 'getPatients');
// method to get list of all patients
//$app->get('/allpatients/', 'getAllPatients');
$app->get('/allpatients/:fromCount', 'getAllPatients');

// method to get list of patients belonging to given provider
$app->get('/filterpatients/:loginProvderId', 'getPatientsByProvider');
// method to get patient demographics
$app->get('/demographics/:patientId', 'getDemographics');
// method to get list of appointments for a given date
$app->get('/patientsbyday/:loginProvderId/:day', 'getPatientsByday');
// method to get list of encounters for given provider
$app->get('/myencounters/:loginProvderId', 'getEncounterList');
// method to get list of patients belonging to given provider for messages
$app->get('/mypatients/:loginProvderId','getMyPatients');
// method to create new message
$app->get('/createmessage/:loginProvderId/:pid/:body/:title/:msg_status','createMessage');
// method to store speech dictation
$app->get('/speechDictation/:loginProvderId/:pid/:dictation','createDictation');

$app->run();


// connection to openemr database
function getConnection() 
{

	//$dbhost="emrsb.risecorp.com";
	//$dbhost="172.17.66.203";


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
            
        $sql="SELECT id,username,password,salt FROM users_secure WHERE username=:username"; 
    
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
                    echo '[{"id":'.json_encode($user->id).'}]';
                    
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

	$sql = "select pd.pid,pd.title,pd.fname,pd.lname, DATE_FORMAT(pd.DOB,'%m-%d-%Y') AS DOB,DATE_FORMAT(FROM_DAYS(DATEDIFF(CURDATE(),pd.DOB)), '%Y')+0 AS age,if (pd.sex = 'Female' ,'F','M' ) as sex,pd.street,pd.city,pd.state,pd.country_code,pd.postal_code,pd.phone_cell
		        from patient_data pd where pd.id not in
		        (select pd1.id from patient_data pd1
		        join (select * from patient_data order by pid desc limit $fromCount) pd2
		        on pd1.id=pd2.id)
		        order by pd.pid desc 
		        limit 10";             
    

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

// method to get list of patients belonging to given provider
// get list of all patients  for the logged in user/provider
function getPatientsByProvider($loginProvderId)
{        


	$sql = "select pd.pid,pd.title,pd.fname,pd.lname, DATE_FORMAT(pd.DOB,'%m-%d-%Y') AS DOB,DATE_FORMAT(FROM_DAYS(DATEDIFF(CURDATE(),pd.DOB)), '%Y')+0 AS age,if (pd.sex = 'Female' ,'F','M' ) as sex,pd.street,pd.city,pd.state,pd.country_code,pd.postal_code,pd.phone_cell
FROM patient_data pd 

where pd.providerID=:loginProvderId order by pd.pid desc";

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
from patient_data pd
inner join employer_data ed
on pd.pid=ed.pid
where ed.pid=:patientId
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

		try 
        {
            $db = getConnection();
            $stmt1 = $db->prepare($sql1) ;
            $stmt1->bindParam("patientId", $patientId);            
            $stmt1->execute();
            $demo1 = $stmt1->fetchAll(PDO::FETCH_OBJ);
			
			$stmt2 = $db->prepare($sql2) ;
            $stmt2->bindParam("patientId", $patientId);            
            $stmt2->execute();
            $demo2 = $stmt2->fetchAll(PDO::FETCH_OBJ);
			
			$stmt3 = $db->prepare($sql3) ;
            $stmt3->bindParam("patientId", $patientId);            
            $stmt3->execute();
            $demo3 = $stmt3->fetchAll(PDO::FETCH_OBJ);
			
			$stmt4 = $db->prepare($sql4) ;
            $stmt4->bindParam("patientId", $patientId);            
            $stmt4->execute();
            $demo4 = $stmt4->fetchAll(PDO::FETCH_OBJ);

			$stmt5 = $db->prepare($sql5) ;
            $stmt5->bindParam("patientId", $patientId);            
            $stmt5->execute();
            $demo5 = $stmt5->fetchAll(PDO::FETCH_OBJ);
			
			$stmt6 = $db->prepare($sql6) ;
            $stmt6->bindParam("patientId", $patientId);            
            $stmt6->execute();
            $demo6 = $stmt6->fetchAll(PDO::FETCH_OBJ);
			
			$stmt7 = $db->prepare($sql7) ;
            $stmt7->bindParam("patientId", $patientId);            
            $stmt7->execute();
            $demo7 = $stmt7->fetchAll(PDO::FETCH_OBJ);
            //echo count($demo7);
            if(count($demo1) > 0)
            {
				$rs1 = json_encode($demo1);
				$newdemo1 = json_decode($rs1,true);
			}
			else
			{
				$newdemo1[0] = "No Data";
			}
			if(count($demo2) > 0 && !empty($demo2))
            {
				$rs2 = json_encode($demo2);
				$newdemo2 = json_decode($rs2,true);
			}
			else
			{
				$newdemo2[0] = "No Data";
			}
			if(count($demo3) > 0)
            {
				$rs3 = json_encode($demo3);
				$newdemo3 = json_decode($rs3,true);
			}
			else
			{
				$newdemo3[0] = "No Data";
			}
			if(count($demo4) > 0)
            {
				$rs4 = json_encode($demo4);
				$newdemo4 = json_decode($rs4,true);
			}
			else
			{
				$newdemo4[0] = "No Data";
			}
			if(count($demo5) > 0)
            {
				$rs5 = json_encode($demo5);
				$newdemo5 = json_decode($rs5,true);
			}
			else
			{
				$newdemo5[0] = "No Data";
			}
			if(count($demo6) > 0)
            {
				$rs6 = json_encode($demo6);
				$newdemo6 = json_decode($rs6,true);
			}
			else
			{
				$newdemo6[0] = "No Data";
			}
			if(count($demo7) > 0)
            {
				$rs7 = json_encode($demo7);
				$newdemo7 = json_decode($rs7,true);
			}
			else
			{
				$newdemo7[0] = "No Data";
			}
				
				/*$rs2 = json_encode($demo2); 
				$rs3 = json_encode($demo3); 
				$rs4 = json_encode($demo4); 
				$rs5 = json_encode($demo5); 
				$rs6 = json_encode($demo6);
				$rs7 = json_encode($demo7);*/
				
				/*$newdemo1 = json_decode($rs1,true);
				$newdemo2 = json_decode($rs2,true);
				$newdemo3 = json_decode($rs3,true);
				$newdemo4 = json_decode($rs4,true);
				$newdemo5 = json_decode($rs5,true);
				$newdemo6 = json_decode($rs6,true);
				$newdemo7 = json_decode($rs7,true);*/
				
				$newdemo['Who'] = $newdemo1[0];
				$newdemo['Contact'] = $newdemo2[0];
				$newdemo['Choices'] = $newdemo3[0];
				$newdemo['Employer'] = $newdemo4[0];
				$newdemo['Stats'] = $newdemo5[0];
				$newdemo['Misc'] = $newdemo6[0];
				$newdemo['Insurance'] = $newdemo7[0];
				
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
function getMyPatients($loginProvderId)
{
	$sql = "select pd.pid,CONCAT(pd.title,pd.fname,' ',pd.lname) as patient
FROM patient_data pd 

where pd.providerID=:loginProvderId order by pd.pid desc";

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

?>

