<?php
require_once("../verify_session.php");
require_once("../../library/sqlCentralDB.inc");
global $sqlconfCentralDB;
/*
$postarr = array(
                'fname'=>'Subhan','mname'=>'Abdul','lname'=>'Sayyed',
                'practice'=>array(
                    'url-1'=>'allcare.texashousecalls.com',
                    'username-1'=>'smartmbbs',
                    'url-2'=>'allcare.dfwprimary.com',
                    'username-2'=>'smartmbbs',
                    'url-3'=>'allcare.sumanakethamdpa.com',
                    'username-3'=>'smartmbbs'
                ),
                'groupid'=>'custompracticgroup'
            );
 */
$postarr = $_POST['practice'];
$uemail = $_SESSION['portal_useremail'];
$str = "SELECT DISTINCT username FROM allcareobjects WHERE uemail='".$uemail."' AND objecttype='user'";
$sqlSt = $sqlconfCentralDB->prepare($str);
$sqlSt->execute();
$userN = array();
foreach($sqlSt->fetchAll(PDO::FETCH_ASSOC) as $row):
    $userN[] = $row['username'];
endforeach;

$pracArr = array();
$errArr = array();
$count = sizeof($postarr['practice'])/2;
$practiceGrouped = array(); //practices which are grouped
for($i=1;$i<$count+1;$i++){
    if(in_array($postarr['practice']['username-'.$i],$userN)){
        $pracArr['practice']['url-'.$i] = $postarr['practice']['url-'.$i];
        $pracArr['practice']['username-'.$i] = $postarr['practice']['username-'.$i];
        $practiceGrouped[] = $postarr['practice']['url-'.$i];
        // Update CanGroup as YES for below specified practices and usernames
        $str = "UPDATE allcareobjects SET cangroup='YES' WHERE uemail='".$uemail."' AND objecttype='user' 
                AND practiceId='".$postarr['practice']['url-'.$i]."' AND username='".$postarr['practice']['username-'.$i]."'";
        $sqlSt = $sqlconfCentralDB->prepare($str);
        $sqlSt->execute();
        
    }else{
        $errArr[] = $postarr['practice']['username-'.$i];
    }
}
$practiceGroupedStr = implode("','",$practiceGrouped);

$str = "UPDATE allcareobjects SET cangroup='NO' WHERE uemail='".$uemail."' AND objecttype='user' 
                AND practiceId NOT IN ('".$practiceGroupedStr."')";
$sqlSt = $sqlconfCentralDB->prepare($str);
$sqlSt->execute();

$practiceId = '';
$query = sqlStatement("SELECT title FROM list_options WHERE list_id= 'allcareConfig' AND option_id='practiceID'");
while($row = sqlFetchArray($query)){
    $practiceId = $row['title'];
}
//Data sync flag
$datasync = 1; // 1 = practice could be in sync with central db; 0 = This is a standalone practice which should not be in sync with central db
$query = sqlStatement("SELECT title FROM list_options WHERE list_id= 'allcareConfig' AND option_id='practicetocentral'");
while($row = sqlFetchArray($query)){
    $datasync = $row['title'];
}
$response = array();
// Update user profile in practice
sqlStatement("UPDATE users SET fname='".$postarr['fname']."', mname='".$postarr['mname']."', lname='".$postarr['lname']."'
              WHERE id=".$_SESSION['portal_userid']);
$response['type'] = "success";
$response['message'] = "Updated successfully";
 
$sql2 = "SELECT objectid,objectref FROM tbl_centralobjectref WHERE objectid=".$_SESSION['portal_userid']." AND objecttype = 'user'";
$r = sqlStatement($sql2);
$objref = "";
while($row2 = sqlFetchArray($r)):
   $objref = $row2['objectref'];
endwhile;
$count = sqlNumRows($r);

if($datasync == 1):
    $sql = "SELECT userid FROM allcareobjects WHERE groupid='".$postarr['groupid']."' AND practiceId='".$practiceId."' AND objecttype='user'";
    $stmt_user = $sqlconfCentralDB->prepare($sql) ;
    $stmt_user->execute();
    if($count):
        $user = $stmt_user->fetchObject();     
        if($user):
            $userid = $user->userid;
            if($userid != $_SESSION['portal_userid'] && $userid != ""):
                $response['type'] = "error";
                $response['message'] = "Sorry! GroupID already exists";
            endif;
        endif;
        if(sizeof($errArr)>0):
            $response['type'] = "error";
            $response['message'] = "Not authorized to add usernames:".implode(",",$errArr);
        endif;
        $sql = "UPDATE allcareobjects SET fname='".$postarr['fname']."',
                                          mname='".$postarr['mname']."',
                                          lname='".$postarr['lname']."',
                                          practiceId='".$practiceId."',
                                          groupid='".$postarr['groupid']."',    
                                          practiceredirects='".json_encode($pracArr['practice'])."'
                                          WHERE id=".$objref;
        if($userid == $_SESSION['portal_userid'] && $userid != ""):
            $stmt = $sqlconfCentralDB->prepare($sql) ;
            $stmt->execute();
        endif;
        if($userid == ""):
            $stmt = $sqlconfCentralDB->prepare($sql) ;
            $stmt->execute();
        endif;
    else:
        $sql = "INSERT INTO allcareobjects (userid,practiceId,username,fname,mname,lname,groupid,practiceredirects,objecttype) 
            VALUES(".$_SESSION['portal_userid'].",'".$practiceId."','".$_SESSION['portal_username']."','".$postarr['fname']."','".$postarr['mname']."','".$postarr['lname']."','".$postarr['groupid']."','".json_encode($pracArr['practice'])."',  'user')";
        $stmt = $sqlconfCentralDB->prepare($sql) ;
        $stmt->execute();
        $id = $sqlconfCentralDB->lastInsertId();
    endif;
endif;
if($datasync == 1):
    if($count == 0):
        sqlStatement("INSERT INTO tbl_centralobjectref (objectid,objectref,objecttype) VALUES(".$_SESSION['portal_userid'].",".$id.",'user')");
    endif;
endif;

echo json_encode($response);

?>