<?php
require_once("../../../globals.php");
require_once("message_lib.php");
require_once("$srcdir/log.inc");


$params1=explode("&",$_REQUEST['params']);

$params=array();
foreach($params1 as $key => $value){
    $i=explode("=",$value);
   $params[$i[0]]=$i[1];
}


$task= isset($params['task']) ? $params['task'] : "";
$editid= isset($params['editid']) ? $params['editid'] : "";
if($params['obj_type']=='patients'){
    $replt_to_default=isset($params['patient']) ? $params['patient'] : 0;
}else{
     $replt_to_default=isset($params[$params['obj_type']]) ? $params[$params['obj_type']] : 0;
}

switch($task) {
    case "add" :
    {
        // Add a new message for a specific patient; the message is documented in Patient Notes.
        // Add a new message; it's treated as a new note in Patient Notes.
        // Add a new message; it's treated as a new note in Patient Notes.
        $note = $params['content'];
        $noteid = $params['noteid'];
        $form_note_type = $params['msg_type'];
        $form_message_status = $params['status'];
        $object_type = $params['obj_type'];
        $priority = $params['priority'];
        $reply_to = $params[$params['obj_type']];
       
        $assigned_to_list = explode(';', $params['assigned_to']);
       
        $openstrpos  = strpos($note,"[" );
        $closestrpos = strpos($note,"]");
        $finalstr = substr($note, $openstrpos+1, $closestrpos-$openstrpos-1);
        $link="<a href=".$finalstr." target='_blank'>".$finalstr."</a>";
        $note=str_replace('['.$finalstr.']',$link,$note);
      
        foreach($assigned_to_list as $assigned_to){
             
          if ($noteid && $assigned_to != '-patient-') {
               echo strpos($assigned_to,'grp_');
            if(strpos($assigned_to,'grp_')!==false){
    
                 $ures2 = sqlStatement("SELECT * FROM tbl_allcare_usergroup where group_name=".'"'.str_replace("_grp"," ",str_replace('grp_','',$assigned_to)).'"');
                 $urow2 = sqlFetchArray($ures2); 
                 $grp_mem = unserialize($urow2['group_members']);
                // $grp_mem= array_filter($grp_mem1);
                 
                //foreach($grp_mem as $mem){
                    // $to=$mem." from (".str_replace("_grp"," ",str_replace('grp_','',$assigned_to)).")";
                    $to=str_replace("_grp"," ",str_replace('grp_','',$assigned_to));
                     updateMessage($noteid, $note, $form_note_type, $to, $form_message_status,str_replace("$"," ",str_replace('grp_','',$assigned_to)),$object_type,$priority);
                 //}
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
                
                 //foreach($grp_mem as $mem){
                   // $to=$mem." from (".str_replace("_grp"," ",str_replace('grp_','',$assigned_to)).")";
                    $to=str_replace("_grp"," ",str_replace('grp_','',$assigned_to));
                    addMessage($reply_to, $note, $userauthorized, '1', $form_note_type, $to, '', $form_message_status,str_replace("$"," ",str_replace('grp_','',$assigned_to)),$object_type,$priority);
                 //}
            }else{
                 addMessage($reply_to, $note, $userauthorized, '1', $form_note_type, $assigned_to, '', $form_message_status,'',$object_type,$priority);
            } 
            
            
          }
        }
    } break;
    
    case "savePatient":
    case "save" : {
        // Update alert.
        $noteid = $params['noteid'];
        $form_message_status = $params['form_message_status'];
        $reply_to = $replt_to_default;
        if ($task=="save")
            updatePnoteMessageStatus($noteid,$form_message_status);
        else
            updatePnotePatientCustom($noteid,$reply_to);
        $task = "edit";
        $note = $params['note'];
        $title = $params['form_note_type'];
        $reply_to = $replt_to_default;
    }
    case "edit" : {
        if ($noteid == "") {
            $noteid = $_GET['noteid'];
        }
        // Update the message if it already exists; it's appended to an existing note in Patient Notes.
        $result = getPnoteById($noteid);
        if ($result) {
            if ($title == ""){
                $title = $result['title'];
            }
            $body = $result['body'];
            if ($reply_to == ""){
                $reply_to = $result['pid'];
            }
            $form_message_status = $result['message_status'];
        }
    } break;
    
}

$show_all=$_REQUEST['see_all']?$_REQUEST['see_all']:'no';
if($_REQUEST['active']==0){
    $active=$_REQUEST['active'];
}else if($_REQUEST['active']==1){
    $active=$_REQUEST['active'];
}else{
     $active='';
}




$delete_id = $_REQUEST['delete_id'];
for($i = 0; $i < count($delete_id); $i++) {
    deleteMessage($delete_id[$i]);
    newEvent("delete", $_SESSION['authUser'], $_SESSION['authProvider'], 1, "Custom messages: id ".$delete_id[$i]);
}

$access=sqlStatement("select userid,custom_msg_access from tbl_user_custom_attr_1to1 where userid=".$_SESSION['authId']);
$accrow=sqlFetchArray($access);
$action_access=explode("|",$accrow['custom_msg_access']);

// Manage page numbering and display beneath the Messages table.

$total = getMsgsByUser($active,$show_all,$_SESSION['authUser'],true);
?>
<html>
    <head>
        <style>
           td {
                height: 50px;
                vertical-align: bottom;
                padding:10px;
           }
           .msg {
               padding:10px;
               align:center;
           }
        </style>
      
        <script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/jquery.js"></script>
       
      
        <script>
            $(document).ready(function(){
               
                if(document.getElementById('just_mine').checked)
                    document.getElementById('see_all').checked=false;
                else
                    document.getElementById('see_all').checked=true;
             
            });
            </script>
    </head>
    <body style="background-color:#F0F8FF">
        <h3>Messages List</h3>
        <p>Messages Count:<?php echo "$total"; ?></p>
        <form name=MessageList id="MessageList" action="message_list.php" method="post" />
        <input type="hidden" name="task" value="delete">
        <?php 
            if(in_array('see_all',$action_access)){
               echo" <input type='radio' name='see_all' id='see_all'  value='yes'"; if($show_all=='yes'){ echo 'checked'; }  echo "onclick='this.form.submit()'>See All";
            }
        ?>
        
        <input type="radio" name="see_all" id="just_mine" value='no' <?php if($show_all=='no'){ echo "checked"; } ?>  onclick="this.form.submit()">Just Mine
        &nbsp;&nbsp;<label>Active/InActive:</label><select name='active' id='active' onchange='this.form.submit();'>
            <option value='' <?php  if($active==' '){ echo "selected"; } ?> >Showall</option>
            <option value='1' <?php if($active=='1'){ echo "selected"; } ?> >Show Active</option>
            <option value='0' <?php if($active=='0'){ echo "selected"; } ?> >Show Inactive</option>
        </select>
        <br><br>
        <?php if($total!=0) { ?>
        <table rules="rows" >
            <tr>
                <th><input type=checkbox id='checkAll' onclick='selectAll();'></th>
                <th>From</th>
                <th> Assigned To</th>
                <th>Linked To</th>
                <th>Message Type</th>
                <th>Object Type</th>
                <th>Content</th>
                <th>Date</th>
                <th>Priority</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            <?php 
            
            $count = 0;
            $result = getMsgsByUser($active,$show_all,$_SESSION['authUser'],false,$sortby,$sortorder,$begin,$listnumber);
            while($myrow = sqlFetchArray($result)){
                    
                    $count++;
                    $name = $myrow['user'];
                    $login_user=sqlStatement("select * from users where username='$name'");
                    $lrow=sqlFetchArray($login_user);
                    $name = $lrow['lname'];
                    if ($lrow['fname']) {
                        $name .= ", " . $lrow['fname'];
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
                    $obj = $myrow['obj_id'];
                    if ($obj>0) {
                        if($myrow['object_type']=='patients'){
                            $psql=sqlStatement("select * from patient_data where pid=".$obj);
                            $prow=sqlFetchArray($psql);
                            $obj_name = $prow['lname'];
                            if ($prow['fname']) {
                                $obj_name .= ", " . $prow['fname'];
                            }
                            
                        }else if($myrow['object_type']=='facility'){
                            $fsql=sqlStatement("select * from facility  where id=".$obj);
                            $frow=sqlFetchArray($fsql);
                            $obj_name=$frow['name'];
                        }else if($myrow['object_type']=='insurance'){
                            $fsql=sqlStatement("select * from insurance_companies  where id=".$obj);
                            $frow=sqlFetchArray($fsql);
                            $obj_name=$frow['name'];
                        }else if($myrow['object_type']=='pharmacy'){
                            $fsql=sqlStatement("select * from pharmacies  where id=".$obj);
                            $frow=sqlFetchArray($fsql);
                            $obj_name=$frow['name'];
                        }else if($myrow['object_type']=='users'){
                            $fsql=sqlStatement("select * from users  where id=".$obj);
                            $frow=sqlFetchArray($fsql);
                            $obj_name = $frow['lname'];
                            if ($frow['fname']) {
                                $obj_name .= ", " . $frow['fname'];
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
                        }
                        
                        
                    } else {
                        $obj_name = "* undefined *";
                    }
            ?>
                <tr id='row<?php echo $count; ?>'>
                <td><input type="checkbox" id="check<?php echo $count; ?>" name="delete_id[]" value="<?php echo $myrow['id']; ?>" onclick="selectRow(<?php echo $count; ?>);"/></td>    
                <td><?php echo $name; ?></td>
                <td><?php echo $assign_name; ?></td>
                <td><?php echo $obj_name; ?></td>
                <td><?php echo $myrow['title']; ?></td>
                <td><?php echo $myrow['object_type']; ?></td>
                <td><?php echo str_replace("+"," ",$myrow['body']); ?></td>
                <td><?php echo $myrow['date']; ?></td>
                <td><?php echo str_replace("_"," ",$myrow['priority']); ?></td>
                <td><?php echo $myrow['message_status']; ?></td>
               <?php  if(in_array('edit',$action_access)){ ?> <td><a  href="javascript:;" onclick="window.open('custom_messages.php?task=edit_view&editid=<?php echo $myrow['id']; ?>','popup','width=900,height=900,scrollbars=no,resizable=yes');" ><span class="glyphicon glyphicon-trash" aria-hidden="true"></span>&nbsp;Edit</a></td><?php } ?>
                <?php  if(in_array('delete',$action_access)){ ?><td><a class="btn btn-primary btn-sm" href="javascript:void" onclick="confirmDeleteSelected()"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span>&nbsp;Delete</a></td><?php } ?>
                </tr>
            <?php }?>
        </table>
        <?php }else {
            echo "<div class='msg'>No Messages available</div>"; 
        } ?>
         <?php  if(in_array('add_new',$action_access)){ ?><a  href="javascript:;" onclick="window.open('custom_messages.php','popup','width=900,height=900,scrollbars=no,resizable=yes');" ><span class="glyphicon glyphicon-trash" aria-hidden="true"></span>&nbsp;Add New</a><?php } ?>
        &nbsp;&nbsp;
        <?php if($total!=0) { ?>
             <?php  if(in_array('delete',$action_access)){ ?><a class="btn btn-primary btn-sm" href="javascript:void" onclick="confirmDeleteSelected()"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span>&nbsp;Delete</a><?php } ?>
       <?php }
        ?>
    </form>
    </body>
</html>
<script language="javascript">
// This is to confirm delete action.
function confirmDeleteSelected() {
    if(confirm("<?php echo htmlspecialchars( xl('Do you really want to delete the selection?'), ENT_QUOTES); ?>")) {
        document.MessageList.submit();
    }
}
// This is to allow selection of all items in Messages table for deletion.
function selectAll() {
    if(document.getElementById("checkAll").checked==true) {
        document.getElementById("checkAll").checked=true;<?php
        for($i = 1; $i <= $count; $i++) {
            echo "document.getElementById(\"check$i\").checked=true; document.getElementById(\"row$i\").style.background='#E7E7E7';  ";
        } ?>
    }
    else {
        document.getElementById("checkAll").checked=false;<?php
        for($i = 1; $i <= $count; $i++) {
            echo "document.getElementById(\"check$i\").checked=false; document.getElementById(\"row$i\").style.background='#F7F7F7';  ";
        } ?>
    }
}
// The two functions below are for managing row styles in Messages table.
function selectRow(row) {
    var id='check'+row;
    var rowid='row'+row;
    if(document.getElementById(id).checked===true){
        document.getElementById(rowid).style.background = "#E7E7E7";
    }else if(document.getElementById(id).checked===false){
         document.getElementById(rowid).style.background = "#F7F7F7";
    } 
}

</script> 