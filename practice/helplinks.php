<?php 
require_once("../library/sqlCentralDB.inc");
require_once("verify_session.php");
global $sqlconfCentralDB;
$sql = "SELECT title, notes FROM list_options WHERE list_id='AllCareHelpLinks'";
$stmt = $sqlconfCentralDB->prepare($sql) ;
$stmt->execute();

$arr = array();
$arr2 = array();
$i = 0;
$userid = $_SESSION['portal_userid']; // Get session userid
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)):
    $arr[$i]['title'] = $row['title'];
    $arr[$i]['helplink'] = $row['notes'];
    $i++;
endwhile;

// Get All Care Help Content Type
$sql = sqlStatement("SELECT option_id,title FROM list_options WHERE list_id='AllCareHelpContentType'");
$contType = array();
$j = 0;
while($row = sqlFetchArray($sql)):
    $contType[$j]['option_id'] = $row['option_id'];
    $contType[$j]['title'] = $row['title'];
    $contTypeStr .= $row['option_id']."','";
    $j++;
endwhile;

// Get Current Practice
$sql = sqlStatement("SELECT title FROM list_options WHERE list_id='AllCareConfig' AND option_id='practiceID'");
$practiceId = '';
$row = sqlFetchArray($sql);
$practiceId = $row['title'];

// Get Help Context URL
$sql = sqlStatement("SELECT title FROM list_options WHERE list_id='AllCareConfig' AND option_id='practicecontexthelp'");
$contextUrl = '';
$row = sqlFetchArray($sql);
$contextUrl = $row['title'];

$uniq = UniqueMachineID();
$getIpaddress = getIP();
// Flag for User allowed to Edit
$sql = sqlStatement("SELECT contextedit FROM tbl_user_custom_attr_1to1 WHERE userid = ".$userid);
$allowEdit = 'NO';
$row = sqlFetchArray($sql);
$allowEdit = $row['contextedit'];
    $sql = "SELECT * FROM allcareobjectssession WHERE practiceid='".$practiceId."' AND userid='".$userid."' AND machineid='".$uniq."' AND status='login'";
    $ftch = $sqlconfCentralDB->prepare($sql);
    $ftch->execute();
    $fhCount = $ftch->rowCount();
    if($fhCount == 0):
        $sql = "INSERT INTO allcareobjectssession (practiceid,userid,sessionid,machineid,status,logintime,ipaddress) VALUES ('".$practiceId."','".$userid."','".session_id()."','".$uniq."','login','".date("Y-m-d h:i:sa")."','".$getIpaddress."')";
        $stmt = $sqlconfCentralDB->prepare($sql) ;
        $stmt->execute();
    endif;
    
if($allowEdit == 'YES'):
    
endif;

// Get content type
$contTypeStr = "'".$contTypeStr;
$contTypeStr = substr($contTypeStr,0,-2);

$sql = "SELECT * FROM articles WHERE practice_id = '".$practiceId."' AND content_type IN (".$contTypeStr.")";
$stmt = $sqlconfCentralDB->prepare($sql) ;
$stmt->execute();
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)):
    $arr[$i]['title'] = $row['title'];
    $arr[$i]['content'] = $row['content'];
    $arr[$i]['contexturl'] = $contextUrl;
    $arr[$i]['practiceid'] = $practiceId;
    $arr[$i]['userid'] = $userid;
    $arr[$i]['machineid'] = $uniq;
    $arr[$i]['articleid'] = $row['id'];
    $arr[$i]['contenttype'] = $row['content_type'];
    if($allowEdit == 'YES'):
       $arr[$i]['edit'] = '1'; 
    endif;
    $i++;
endwhile;


$data = $arr;



//$data = array(
//            array('title' => 'Status on Support Request ',
//                          'helplink' => 'https://allcaredoctor.com/about-us-2/'
//            ),
//            array('title' => 'Status on Support Request ',
//                          'helplink' => 'https://allcaredoctor.com/about-us-2/'
//            ),
//            array('title' => 'Status on Support Request ',
//                          'helplink' => 'https://allcaredoctor.com/contact-us/'
//            ),
//            array('title' => 'Insert and arrange text, shapes, and lines',
//                          'helplink' => 'https://allcaredoctor.com/2016/09/'
//            ),
//            array('title' => 'Status on Support Request ',
//                          'helplink' => 'https://allcaredoctor.com/about-us-2/'
//            )
//        );
//
//     echo json_encode($data);

function UniqueMachineID($salt = "") {
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        $temp = sys_get_temp_dir().DIRECTORY_SEPARATOR."diskpartscript.txt";
        if(!file_exists($temp) && !is_file($temp)) file_put_contents($temp, "select disk 0\ndetail disk");
        $output = shell_exec("diskpart /s ".$temp);
        $lines = explode("\n",$output);
        $result = array_filter($lines,function($line) {
            return stripos($line,"ID:")!==false;
        });
        if(count($result)>0) {
            $result = array_shift(array_values($result));
            $result = explode(":",$result);
            $result = trim(end($result));       
        } else $result = $output;       
    } else {
        $result = shell_exec("blkid -o value -s UUID");  
        if(stripos($result,"blkid")!==false) {
            $result = $_SERVER['HTTP_HOST'];
        }
    }   
    return md5($salt.md5($result));
}

function getIP() {
    $ip = $_SERVER['SERVER_ADDR'];

    if (PHP_OS == 'WINNT'){
        $ip = getHostByName(getHostName());
    }

    if (PHP_OS == 'Linux'){
        $command="/sbin/ifconfig";
        exec($command, $output);
        // var_dump($output);
        $pattern = '/inet addr:?([^ ]+)/';

        $ip = array();
        foreach ($output as $key => $subject) {
            $result = preg_match_all($pattern, $subject, $subpattern);
            if ($result == 1) {
                if ($subpattern[1][0] != "127.0.0.1")
                $ip = $subpattern[1][0];
            }
        //var_dump($subpattern);
        }
    }
    return $ip;
}

echo json_encode($data);

?>