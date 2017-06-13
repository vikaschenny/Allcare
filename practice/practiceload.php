<?php 
require_once("../library/sqlCentralDB.inc");

global $sqlconfCentralDB;

$type = $_POST['type'];


switch($type):
    case 'uemail':
            $uemail = $_POST['uemail'];
            $sql = "SELECT practiceId,username,userid,uemail FROM allcareobjects WHERE uemail='".$uemail."' AND cangroup='YES'";
            $stmt_user = $sqlconfCentralDB->prepare($sql) ;
            $stmt_user->execute();
            $drow = array();
            $i = 0;
            while($row = $stmt_user->fetch(PDO::FETCH_ASSOC)):
                $drow[$i]['practice'] = $row['practiceId'];
                $drow[$i]['username'] = $row['username'];
                $drow[$i]['userid'] = $row['userid'];
                $drow[$i]['uemail'] = $row['uemail'];
                $i++;
            endwhile;
            echo json_encode($drow);
        break;
    case 'practiceid':    
            $practice = $_POST['practiceid'];
            $practiceId = '';
            $sql = "SELECT title FROM list_options WHERE list_id= 'allcarePractices' AND option_id='".$practice."'";
            $stmt_user = $sqlconfCentralDB->prepare($sql) ;
            $stmt_user->execute();
            $prac = $stmt_user->fetchObject();
            echo $prac->title;
        break;
endswitch;





?>