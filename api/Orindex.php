<?php

/*
Webservice for user/provider authentication with openemr
*/


// do initial application

require 'Slim/Slim.php';
require 'Slim/Route.php';




// initialize app
$app = new Slim();

$app->get('/login/:username/:password', 'loginUser');
$app->get('/patients/:loginUserId', 'getPatients');
// monika
$app->get('/allpatients/', 'getAllPatients');
$app->get('/filterpatients/:loginProvderId', 'getPatientsByProvider');
$app->run();



// connection to openemr database
function getConnection() 
{


	//$dbhost="emrsb.risecorp.com";
	//$dbhost="172.17.66.203";


	/*$dbhost="mysql51-110.wc2.dfw1.stabletransit.com";
	$dbuser="551948_newemr";
	$dbpass="Devemr@321";
	$dbname="551948_newemr";
	*/
	$dbhost="localhost";
	$dbuser="root";
	$dbpass="";
	$dbname="openemr";
	$dbh = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);	
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	return $dbh;
}



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



// get list of appointments that are to be executed for current date for the logged in user/provider
function getPatients($loginUserId)
{       



	$sql = "SELECT pd.id,pd.title,pd.fname,pd.lname,DATE_FORMAT(pd.DOB,'%m-%d-%Y') as DOB,pd.street,pd.postal_code,pd.city,pd.state,pd.country_code,pd.phone_home,pd.phone_biz,pd.phone_contact,pd.phone_cell,
                if (pd.sex = 'Female' ,'F','M' ) as sex,DATE_FORMAT(FROM_DAYS(DATEDIFF(CURDATE(),pd.DOB)), '%Y')+0 AS age,
                ope.pc_title, DATE_FORMAT(ope.pc_eventDate,'%m-%d-%Y') as event_date ,if(ope.pc_endDate='0000-00-00',DATE_FORMAT(ope.pc_eventDate,'%m-%d-%y'),DATE_FORMAT(ope.pc_endDate,'%m-%d-%Y'))  as end_date ,ope.pc_duration, TIME_FORMAT(ope.pc_startTime, '%h:%i %p') AS start_time,TIME_FORMAT(ope.pc_endTime, '%h:%i %p') AS end_time 
                FROM patient_data pd INNER JOIN openemr_postcalendar_events ope ON pd.pid=ope.pc_pid
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


// get list of all patients  that are to be executed for current date for the logged in user/provider
function getAllPatients()
{       



	$sql = "select pd.id,pd.title,pd.fname,pd.lname, DATE_FORMAT(pd.DOB,'%m-%d-%Y') AS DOB,pd.street,pd.postal_code,pd.city,pd.state,pd.country_code,pc.pc_catname,pd.phone_cell,DATE_FORMAT(FROM_DAYS(DATEDIFF(CURDATE(),pd.DOB)), '%Y')+0 AS age,if (pd.sex = 'Female' ,'F','M' ) as sex 
                                FROM patient_data pd
                                inner join openemr_postcalendar_events po on po.pc_pid=pd.id
                                inner join openemr_postcalendar_categories pc on pc.pc_catid=po.pc_catid
                                order by pd.id desc";        
    

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
// get list of all patients  for the logged in user/provider
function getPatientsByProvider($loginProvderId)
{        


	$sql = "select pd.id,pd.title,pd.fname,pd.lname, DATE_FORMAT(pd.DOB,'%m-%d-%Y') AS DOB,pd.street,pd.postal_code,pd.city,pd.state,pd.country_code
FROM patient_data pd where pd.providerID=:loginProvderId order by pd.id desc";

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

?>

