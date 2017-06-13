<?php 
//SANITIZE ALL ESCAPES
$sanitize_all_escapes=true;

//STOP FAKE REGISTER GLOBALS
$fake_register_globals=false;

//continue session
session_start();

//landing page definition -- where to go if something goes wrong
$landingpage = "../index.php?site=".$_SESSION['site_id'];	
//

if ( isset($_SESSION['portal_username']) ) {    
    $provider = $_SESSION['portal_username'];
}
else {
        session_destroy();
        header('Location: '.$landingpage.'&w');
        exit;
}
//

$ignoreAuth=true; // ignore the standard authentication for a regular OpenEMR user
include_once("../../interface/globals.php");
require_once("message_lib.php");
require_once("$srcdir/log.inc");

$action=$_REQUEST['action'];
if($action=='delete'){
    $delete_id = $_REQUEST['delete_id'];
    if(is_array($delete_id)){
        for($i = 0; $i < count($delete_id); $i++) {
            $del.=$delete_id[$i]."','";
        }
    }else{
        $del=$delete_id;
    }
    $result=deleteMessage(trim($del,"','"));
     newEvent("delete", $_SESSION['portal_username'], 'Default', 1, "Custom messages: id ".$del);
     if($result==true) echo 'sucess';
        else echo "failed";
//    for($i = 0; $i < count($delete_id); $i++) {
//        $result=deleteMessage($delete_id[$i]);
//        
//        if($result==true) echo 'sucess';
//        else echo "failed";
//        newEvent("delete", $_SESSION['portal_username'], $_SESSION['authProvider'], 1, "Custom messages: id ".$delete_id[$i]);
//    }
}else if($action=='listdata'){
    $list=array();
    $sql_type=sqlStatement("select * from list_options where list_id='AllcareCustomMsgType'");
    while($data1=sqlFetchArray($sql_type)){
        $type[$data1['option_id']]=$data1['title'];
    }
    $list['message_type']=$type;
    $obj=sqlStatement("select * from list_options where list_id='AllcareObjects'");
    while($objdata=sqlFetchArray($obj)){
       $objtype[$objdata['option_id']]=$objdata['title'];
    }
    $list['object_type']=$objtype;


    $psql=sqlStatement("select * from patient_data ");
    while($prow=sqlFetchArray($psql)){
        $pname = $prow['lname'];
        if ($prow['fname']) {
            $pname .= ", " . $prow['fname'];
        }
        $patient[$prow['pid']]=$pname;
    }
    $objval['patients']=$patient;

    $fsql=sqlStatement("select * from facility");
    while($frow=sqlFetchArray($fsql)){
        $facility[$frow['id']]=$frow['name'];
    }
    $objval['facility']=$facility;

    $isql=sqlStatement("select * from insurance_companies");
    while($irow=sqlFetchArray($isql)){
        $insurance[$irow['id']]=$irow['name'];
    }
    $objval['insurance']=$insurance;      

    $phsql=sqlStatement("select * from pharmacies ");
    while($phrow=sqlFetchArray($phsql)){
        $pharmacy[$phrow['id']]=$phrow['name'];
    }
    $objval['pharmacy']=$pharmacy;      


    $usql=sqlStatement("select * from users where (fname!='' or lname!='')");
    while($urow=sqlFetchArray($usql)){
        $uname = $urow['lname'];
        if ($urow['fname']) {
            $uname .= ", " . $urow['fname'];
        }
        $users[$urow['id']]=$uname;
    }
    $objval['users']=$users;    

    $asql=sqlStatement("SELECT *
                        FROM users AS u
                        LEFT JOIN list_options AS lo ON list_id =  'abook_type'
                        AND option_id = u.abook_type where   active=1 and authorized=1");
    while($arow=sqlFetchArray($asql)){
        $aname = $arow['lname'];
        if ($frow['fname']) {
            $aname .= ", " . $arow['fname'];
        }
        $address_Book[$arow['id']]=$aname;
    }
    $objval['address_Book']=$address_Book;  

    $list['object_value']=$objval;
    
    //Status field
    $sql_status=sqlStatement("select * from list_options where list_id='AllcareCustomMsgStatus'");
     while($data_status=sqlFetchArray($sql_status)){
        $status[$data_status['option_id']]=$data_status['title'];
     }
     $list['status']=$status;
     
     //priority field
    $sqlpr=sqlStatement("select * from list_options where list_id='AllcareCustomMsgPriority'");
    while($datapr=sqlFetchArray($sqlpr)){
        $priority[$datapr['option_id']]=$datapr['title'];
        
    } 
     $list['priority']=$priority;
     
     //assigned to
    
    $ures = sqlStatement("SELECT username, fname, lname FROM users " .
            "WHERE username != '' AND active = 1 AND " .
            "( info IS NULL OR info NOT LIKE '%Inactive%' ) and (fname!='' or lname!='')" .
            "ORDER BY lname, fname");
    while ($urow = sqlFetchArray($ures)) {
        $usname = $urow['lname'];
        if ($urow['fname']) {
            $usname .= ", " . $urow['fname'];
        }
        $users1[$urow['username']]=$usname;
    }
    $users1['-Patient-']='-Patient-';
    $u['users']=$users1;
                        
    $ures2 = sqlStatement("SELECT group_name FROM tbl_allcare_usergroup ");
    while ($urow2 = sqlFetchArray($ures2)) {
        $group_name='grp_'.str_replace(" ","_grp",$urow2['group_name']);
        $usersgrp[$group_name]=$group_name;
    }
    $u['user_group']=$usersgrp;
    $list['assigned_to']=array_filter($u); 
    
    
    echo json_encode($list);

}
else if($action=='savedata'){
    
        $note = $_POST['content'];
        $noteid = $_POST['id'];
        $form_note_type = $_POST['Message_type'];
        $form_message_status = $_POST['Status'];
        $object_type = $_POST['obj_type'];
        $priority = $_POST['priority'];
        //$reply_to = $_POST[$_POST['obj_type']];
        $reply_to = $_POST['linkto'];
        $assigned_to_list = explode(';', $_POST['assigned_to']);
       
        foreach($assigned_to_list as $assigned_to){
             
          if ($noteid && $assigned_to != '-patient-') {
               echo strpos($assigned_to,'grp_');
            if(strpos($assigned_to,'grp_')!==false){
    
                 $ures2 = sqlStatement("SELECT * FROM tbl_allcare_usergroup where group_name=".'"'.str_replace("_grp"," ",str_replace('grp_','',$assigned_to)).'"');
                 $urow2 = sqlFetchArray($ures2); 
                 $grp_mem = unserialize($urow2['group_members']);
                // $grp_mem= array_filter($grp_mem1);
                 
                 foreach($grp_mem as $mem){
                     $to=$mem." from (".str_replace("_grp"," ",str_replace('grp_','',$assigned_to)).")";
                     updateMessage($noteid, $note, $form_note_type, $to, $form_message_status,str_replace("$"," ",str_replace('grp_','',$assigned_to)),$object_type,$priority);
                 }
            }else{
                  updateMessage($noteid, $note, $form_note_type, $assigned_to, $form_message_status,'',$object_type,$priority);
            } 
            
            $noteid = '';
          }
          else {
            if($noteid && $assigned_to == '-patient-'){
              // When $assigned_to == '-patient-' we don't update the current note, but
              // instead create a new one with the current note's body prepended and
              // attributed to the patient.  This seems to be all for the patient portal.
                
             
              $row = getPnoteById($noteid);
              if (! $row) die("getPnoteById() did not find id '".text($noteid)."'");
              $pres = sqlQuery("SELECT lname, fname " .
                "FROM patient_data WHERE pid = ?", array($reply_to) );
              $patientname = $pres['lname'] . ", " . $pres['fname'];
              $note .= "\n\n$patientname on ".$row['date']." wrote:\n\n";
              $note .= $row['body'];
            }
            // There's no note ID, and/or it's assigned to the patient.
            // In these cases a new note is created.
         
            if(strpos($assigned_to,'grp_')!==false){
                
                 $ures2 = sqlStatement("SELECT * FROM tbl_allcare_usergroup where group_name=".'"'.str_replace("_grp"," ",str_replace('grp_','',$assigned_to)).'"');
                 $urow2 = sqlFetchArray($ures2); 
                 $grp_mem = unserialize($urow2['group_members']);
                 //$grp_mem= array_filter($grp_mem1);
                
                 foreach($grp_mem as $mem){
                    $to=$mem." from (".str_replace("_grp"," ",str_replace('grp_','',$assigned_to)).")";
                    addMessage($reply_to, $note, $userauthorized, '1', $form_note_type, $to, '', $form_message_status,str_replace("$"," ",str_replace('grp_','',$assigned_to)),$object_type,$priority);
                 }
            }else{
                 addMessage($reply_to, $note, $userauthorized, '1', $form_note_type, $assigned_to, '', $form_message_status,'',$object_type,$priority);
            } 
            
            
          }
        }
        
        $last_ins=sqlStatement("select max(id) as id from  tbl_allcare_custom_messages");
        $last_row=sqlFetchArray($last_ins);
        if($_REQUEST['id']==''){
            $last_id=$last_row['id'];
              $all="u.id, u.user as `from` , u.obj_id as linked_to ,u.body as content ,u.assigned_to, u.title as Message_type, u.date,u.priority,u.object_type, u.message_status as Status,u.activity ,users.username as username";
        }else{
            $last_id=$_REQUEST['id'];
//            $all=" u.id, u.user as `from` ,u.obj_id as linked_to ,u.assigned_to, u.title as Message_type,u.priority,u.object_type, u.message_status as Status,users.username as username,u.body as content";
              $all="u.id, u.user as `from` , u.obj_id as linked_to ,u.body as content ,u.assigned_to, u.title as Message_type, u.date,u.priority,u.object_type, u.message_status as Status,u.activity ,users.username as username";  
        }
        
        //to retrive saved data
        
        $sql="SELECT $all FROM tbl_allcare_custom_messages u
        LEFT JOIN users ON u.user = users.username
        WHERE u.deleted != '1' AND u.assigned_to LIKE ? "; 

        $sql.=" and u.id=".$last_id." ORDER BY u.id desc";

        $result = sqlStatement($sql,array($_POST['assigned_to']));
        while($myrow = sqlFetchArray($result)){
            $myrow['id']=$last_id;
            $name = $myrow['from'];
            if($name!=''){
                $login_user=sqlStatement("select * from users where username='$name'");
                $lrow=sqlFetchArray($login_user);
                $name1= $lrow['lname'];
                if ($lrow['fname']) {
                    $name1 .= ", " . $lrow['fname'];
                }
                 $myrow['from']=$name1;  
            }


             $obj = $myrow['linked_to'];
            if ($obj>0) {
                if($myrow['object_type']=='patients'){
                    $psql=sqlStatement("select * from patient_data where pid=".$obj);
                    $prow=sqlFetchArray($psql);
                    $obj_name = $prow['lname'];
                    if ($prow['fname']) {
                        $obj_name .= ", " . $prow['fname'];
                    }
                    
                    $myrow['linked_to']=$obj_name;
                    
//                    else{
//                        $objarr['value']=$obj;
//                        $objarr['title']=$obj_name;
//                        $myrow['linked_to']=$objarr;
//                    }

                }else if($myrow['object_type']=='facility'){
                    $fsql=sqlStatement("select * from facility  where id=".$obj);
                    $frow=sqlFetchArray($fsql);
                    $obj_name=$frow['name'];
                    $myrow['linked_to']=$obj_name;
                   
                }else if($myrow['object_type']=='insurance'){
                    $fsql=sqlStatement("select * from insurance_companies  where id=".$obj);
                    $frow=sqlFetchArray($fsql);
                    $obj_name=$frow['name'];
                    $myrow['linked_to']=$obj_name;
                }else if($myrow['object_type']=='pharmacy'){
                    $fsql=sqlStatement("select * from pharmacies  where id=".$obj);
                    $frow=sqlFetchArray($fsql);
                    $obj_name=$frow['name'];
                    $myrow['linked_to']=$obj_name;
                        
                }else if($myrow['object_type']=='users'){
                    $fsql=sqlStatement("select * from users  where id=".$obj);
                    $frow=sqlFetchArray($fsql);
                    $obj_name = $frow['lname'];
                    if ($frow['fname']) {
                        $obj_name .= ", " . $frow['fname'];
                    }
                   $myrow['linked_to']=$obj_name;
                }else if($myrow['object_type']=='address_Book'){
                    $fsql=sqlStatement("SELECT *
                                        FROM users AS u
                                        LEFT JOIN list_options AS lo ON list_id =  'abook_type'
                                        AND option_id = u.abook_type where id=$obj and active=1 and authorized=1");
                    $frow=sqlFetchArray($fsql);
                    $obj_name = $frow['lname'];
                    if ($frow['fname']) {
                        $obj_name .= ", " . $frow['fname'];
                    }

                    $myrow['linked_to']=$obj_name;
                }
            } 
            else {
                $obj_name = "";
                $myrow['linked_to']=$obj_name;
            }
             //content
            $myrow['content']=$myrow['content'];
            
            //assigned to
            $assign = explode("from",$myrow['assigned_to']);
            $assign_user=sqlStatement("select * from users where username='$assign[0]'");
            $assign_row=sqlFetchArray($assign_user);
            $assign_name = $assign_row['lname'];
            if ($assign_row['fname']) {
                $assign_name .= ", " . $assign_row['fname'];
            }
            $assign_name.=$assign[1];
            $myrow['assigned_to']=$assign_name;
            
            
            //message type
            $type = $myrow['Message_type'];
            $typesql=sqlStatement("select title from list_options where list_id='AllcareCustomMsgType' and option_id='$type'");
            $tdata=sqlFetchArray($typesql);
            $myrow['Message_type']=$tdata['title'];
            
            //date
            $myrow['date']=$myrow['date'];
            

            //priority
            $pri=$myrow['priority'];
            $sqlpr=sqlStatement("select * from list_options where list_id='AllcareCustomMsgPriority' and option_id='$pri'");
            $pdata=sqlFetchArray($sqlpr);
            $myrow['priority']=$pdata['title'];
            
            
            

            //object type
            $obj12=$myrow['object_type'];
            $osql=sqlStatement("select * from list_options where list_id='AllcareObjects' and option_id='$obj12'");
            $odata=sqlFetchArray($osql);
            $myrow['object_type']=$odata['title'];
            
            

            //status
            $stat=$myrow['Status'];
            $ssql=sqlStatement("select * from list_options where list_id='AllcareCustomMsgStatus' and option_id='$stat'");
            $sdata=sqlFetchArray($ssql);
            $myrow['Status']=$sdata['option_id'];
            
            
            
            
                $myrow['activity']=$myrow['activity'];
            
            
                $myrow['username']=$myrow['username'];
            
            
           
                $myrow['assigned_user']=$assign_row['username'];
            
            
            $arr[]=$myrow;
    }

    echo json_encode($arr);
}
else{    
$usrvar='_%';
if($_REQUEST['action']=='edit'){
    $all=" u.obj_id as linked_to ,u.assigned_to, u.title as Message_type,u.priority,u.object_type, u.message_status as Status,users.username as username";
}else{
    $all="u.id, u.user as `from` , u.obj_id as linked_to ,u.body as content ,u.assigned_to, u.title as Message_type, u.date,u.priority,u.object_type, u.message_status as Status,u.activity ,users.username as username";
}
$sql="SELECT $all FROM tbl_allcare_custom_messages u
        LEFT JOIN users ON u.user = users.username
        WHERE u.deleted != '1' AND u.assigned_to LIKE ? "; 
if($_REQUEST['action']=='edit'){
    $sql.=" and u.id=".$_REQUEST['id'];
}
$sql.=" ORDER BY u.id desc";
 
 $result = sqlStatement($sql,array($usrvar));
 while($myrow = sqlFetchArray($result)){
    $name = $myrow['from'];
    if($name!=''){
        $login_user=sqlStatement("select * from users where username='$name'");
        $lrow=sqlFetchArray($login_user);
        $name = $lrow['lname'];
        if ($lrow['fname']) {
            $name .= ", " . $lrow['fname'];
        }
         $myrow['from']=$name;  
    }
   
   
    
    //assigned to
    $assign = explode("from",$myrow['assigned_to']);
    $assign_user=sqlStatement("select * from users where username='$assign[0]'");
    $assign_row=sqlFetchArray($assign_user);
    $assign_name = $assign_row['lname'];
    if ($assign_row['fname']) {
        $assign_name .= ", " . $assign_row['fname'];
    }
    $assign_name.=$assign[1];
   
    if($_REQUEST['action']=='edit'){
        $objarr1['value']=$assign_row['username'];
        $objarr1['title']=$assign_name;
        $myrow['assigned_to']=$objarr1;
    }else{
        $myrow['assigned_to']=$assign_name;
    }
    $obj = $myrow['linked_to'];
    if ($obj>0) {
        if($myrow['object_type']=='patients'){
            $psql=sqlStatement("select * from patient_data where pid=".$obj);
            $prow=sqlFetchArray($psql);
            $obj_name = $prow['lname'];
            if ($prow['fname']) {
                $obj_name .= ", " . $prow['fname'];
            }
            
            if($_REQUEST['action']=='edit'){
                $objarr['value']=$obj;
                $objarr['title']=$obj_name;
                $myrow['linked_to']=$objarr;
            }else{
                $myrow['linked_to']=$obj_name;
            }
        }else if($myrow['object_type']=='facility'){
            $fsql=sqlStatement("select * from facility  where id=".$obj);
            $frow=sqlFetchArray($fsql);
            $obj_name=$frow['name'];
            
            if($_REQUEST['action']=='edit'){
                $objarr['value']=$obj;
                $objarr['title']=$obj_name;
                $myrow['linked_to']=$objarr;
            }else{
                $myrow['linked_to']=$obj_name;
            }
        }else if($myrow['object_type']=='insurance'){
            $fsql=sqlStatement("select * from insurance_companies  where id=".$obj);
            $frow=sqlFetchArray($fsql);
            $obj_name=$frow['name'];
            
            if($_REQUEST['action']=='edit'){
                $objarr['value']=$obj;
                $objarr['title']=$obj_name;
                $myrow['linked_to']=$objarr;
            }else{
                $myrow['linked_to']=$obj_name;
            }
        }else if($myrow['object_type']=='pharmacy'){
            $fsql=sqlStatement("select * from pharmacies  where id=".$obj);
            $frow=sqlFetchArray($fsql);
            $obj_name=$frow['name'];
           
            if($_REQUEST['action']=='edit'){
                $objarr['value']=$obj;
                $objarr['title']=$obj_name;
                $myrow['linked_to']=$objarr;
            }else{
                $myrow['linked_to']=$obj_name;
            }
        }else if($myrow['object_type']=='users'){
            $fsql=sqlStatement("select * from users  where id=".$obj);
            $frow=sqlFetchArray($fsql);
            $obj_name = $frow['lname'];
            if ($frow['fname']) {
                $obj_name .= ", " . $frow['fname'];
            }
            
            if($_REQUEST['action']=='edit'){
                $objarr['value']=$obj;
                $objarr['title']=$obj_name;
                $myrow['linked_to']=$objarr;
            }else{
                $myrow['linked_to']=$obj_name;
            }
        }else if($myrow['object_type']=='address_Book'){
            $fsql=sqlStatement("SELECT *
                                FROM users AS u
                                LEFT JOIN list_options AS lo ON list_id =  'abook_type'
                                AND option_id = u.abook_type where id=$obj and active=1 and authorized=1");
            $frow=sqlFetchArray($fsql);
            $obj_name = $frow['lname'];
            if ($frow['fname']) {
                $obj_name .= ", " . $frow['fname'];
            }
           
            if($_REQUEST['action']=='edit'){
                $objarr['value']=$obj;
                $objarr['title']=$obj_name;
                $myrow['linked_to']=$objarr;
            }else{
                $myrow['linked_to']=$obj_name;
            }
        }


    } 
    else {
        $obj_name = "";
        $myrow['linked_to']=$obj_name;
    }
    if($_REQUEST['action']=='edit'){
        //message type
        $type = $myrow['Message_type'];
        $typesql=sqlStatement("select title from list_options where list_id='AllcareCustomMsgType' and option_id='$type'");
        $tdata=sqlFetchArray($typesql);
        $tarr['value']=$type;
        $tarr['title']=$tdata['title'];
        $myrow['Message_type']=$tarr;

        //priority
        $pri=$myrow['priority'];
        $sqlpr=sqlStatement("select * from list_options where list_id='AllcareCustomMsgPriority' and option_id='$pri'");
        $pdata=sqlFetchArray($sqlpr);
        $parr['value']=$pri;
        $parr['title']=$pdata['title'];
        $myrow['priority']=$parr;

        //object type
        $obj12=$myrow['object_type'];
        $osql=sqlStatement("select * from list_options where list_id='AllcareObjects' and option_id='$obj12'");
        $odata=sqlFetchArray($osql);
        $oarr['value']=$obj12;
        $oarr['title']=$odata['title'];
        $myrow['object_type']=$oarr;

        //status
        $stat=$myrow['Status'];
        $ssql=sqlStatement("select * from list_options where list_id='AllcareCustomMsgStatus' and option_id='$stat'");
        $sdata=sqlFetchArray($ssql);
        $sarr['value']=$stat;
        $sarr['title']=$sdata['title'];
        $myrow['Status']=$sarr;
    }
    
        $myrow['assigned_user']=$assign_row['username'];
    
    
      $arr[]=$myrow;
      
   }

echo json_encode($arr);

    
}


?>