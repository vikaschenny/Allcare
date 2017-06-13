<?php
/**
 * Copyright (C) 2010 OpenEMR Support LLC
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * 2013/02/08 Minor tweaks by EMR Direct to allow integration with Direct messaging
 * 2013-03-27 by sunsetsystems: Fixed some weirdness with assigning a message recipient,
 *   and allowing a message to be closed with a new note appended and no recipient.
 */

//SANITIZE ALL ESCAPES
$sanitize_all_escapes=true;

//STOP FAKE REGISTER GLOBALS
$fake_register_globals=false;

require_once("../../../globals.php");
require_once("message_lib.php");
    
$task= isset($_REQUEST['task']) ? $_REQUEST['task'] : "";
$editid= isset($_REQUEST['editid']) ? $_REQUEST['editid'] : "";
$replt_to_default=0;
//switch($task) {
//    case "add" :
//    {
//        // Add a new message for a specific patient; the message is documented in Patient Notes.
//        // Add a new message; it's treated as a new note in Patient Notes.
//        // Add a new message; it's treated as a new note in Patient Notes.
//        $note = $_POST['content'];
//        $noteid = $_POST['noteid'];
//        $form_note_type = $_POST['msg_type'];
//        $form_message_status = $_POST['status'];
//        $object_type = $_POST['obj_type'];
//        $priority = $_POST['priority'];
//        $reply_to = $_POST['reply_to'];
//        $assigned_to_list = explode(';', $_POST['assigned_to']);
//        
//        $openstrpos  = strpos($note,"[" );
//        $closestrpos = strpos($note,"]");
//        $finalstr = substr($note, $openstrpos+1, $closestrpos-$openstrpos-1);
//        $link="<a href=".$finalstr." target='_blank'>".$finalstr."</a>";
//        $note=str_replace('['.$finalstr.']',$link,$note);
//      
//        foreach($assigned_to_list as $assigned_to){
//             
//          if ($noteid && $assigned_to != '-patient-') {
//               
//            if(strpos($assigned_to,'grp_')!==false){
//                 $ures2 = sqlStatement("SELECT * FROM tbl_allcare_usergroup where group_name=".'"'.str_replace("$"," ",str_replace('grp_','',$assigned_to)).'"');
//                 $urow2 = sqlFetchArray($ures2); 
//                 $grp_mem = array_filter(unserialize($urow2['group_members']));
//                 
//                 foreach($grp_mem as $mem){
//                     $to=$mem." from (".str_replace("$"," ",str_replace('grp_','',$assigned_to)).")";
//                     updateMessage($noteid, $note, $form_note_type, $to, $form_message_status,str_replace("$"," ",str_replace('grp_','',$assigned_to)),$object_type,$priority);
//                 }
//            }else{
//                  updateMessage($noteid, $note, $form_note_type, $assigned_to, $form_message_status,'',$object_type,$priority);
//            } 
//            
//            $noteid = '';
//          }
//          else {
//            if($noteid && $assigned_to == '-patient-'){
//              // When $assigned_to == '-patient-' we don't update the current note, but
//              // instead create a new one with the current note's body prepended and
//              // attributed to the patient.  This seems to be all for the patient portal.
//                
//             
//              $row = getPnoteById($noteid);
//              if (! $row) die("getPnoteById() did not find id '".text($noteid)."'");
//              $pres = sqlQuery("SELECT lname, fname " .
//                "FROM patient_data WHERE pid = ?", array($reply_to) );
//              $patientname = $pres['lname'] . ", " . $pres['fname'];
//              $note .= "\n\n$patientname on ".$row['date']." wrote:\n\n";
//              $note .= $row['body'];
//            }
//            // There's no note ID, and/or it's assigned to the patient.
//            // In these cases a new note is created.
//         
//            if(strpos($assigned_to,'grp_')!==false){
//                
//                 $ures2 = sqlStatement("SELECT * FROM tbl_allcare_usergroup where group_name=".'"'.str_replace("$"," ",str_replace('grp_','',$assigned_to)).'"');
//                 $urow2 = sqlFetchArray($ures2); 
//                 $grp_mem = array_filter(unserialize($urow2['group_members']));
//                
//                 foreach($grp_mem as $mem){
//                    $to=$mem." from (".str_replace("$"," ",str_replace('grp_','',$assigned_to)).")";
//                    addMessage($reply_to, $note, $userauthorized, '1', $form_note_type, $to, '', $form_message_status,str_replace("$"," ",str_replace('grp_','',$assigned_to)),$object_type,$priority);
//                 }
//            }else{
//                 addMessage($reply_to, $note, $userauthorized, '1', $form_note_type, $assigned_to, '', $form_message_status,'',$object_type,$priority);
//            } 
//            
//            
//          }
//        }
//    } break;
//    
//    case "savePatient":
//    case "save" : {
//        // Update alert.
//        $noteid = $_POST['noteid'];
//        $form_message_status = $_POST['form_message_status'];
//        $reply_to = $replt_to_default;
//        if ($task=="save")
//            updatePnoteMessageStatus($noteid,$form_message_status);
//        else
//            updatePnotePatientCustom($noteid,$reply_to);
//        $task = "edit";
//        $note = $_POST['note'];
//        $title = $_POST['form_note_type'];
//        $reply_to = $replt_to_default;
//    }
//    case "edit" : {
//        if ($noteid == "") {
//            $noteid = $_GET['noteid'];
//        }
//        // Update the message if it already exists; it's appended to an existing note in Patient Notes.
//        $result = getPnoteById($noteid);
//        if ($result) {
//            if ($title == ""){
//                $title = $result['title'];
//            }
//            $body = $result['body'];
//            if ($reply_to == ""){
//                $reply_to = $result['pid'];
//            }
//            $form_message_status = $result['message_status'];
//        }
//    } break;
//    
//}
?>
<html>
    <head>
        <style>
           label {
                display: inline-block;
                width: 8em;
                text-align: right;
                padding:3px;
            }
            .select2 {
                width:290px;
            }
            
        </style>
       
       <script src="../../../../library/bootstrap/docs/js/jquery-2.1.1.min.js"></script>
       <link rel="stylesheet" href="../../../../library/customselect/css/select2.css"/>
       <link rel="stylesheet" href="../../../../library/customselect/css/select2-bootstrap.css"/>
       <script src="../../../../library/customselect/js/select2.js"></script>
       <script>
           $(function(){
              // $('#type option:nth(0)').attr("selected", "selected");
                $('#patient.select2').select2({ placeholder : 'Select Patient' });
                
            });
           //add users to text field
            function Addtext(val){
                var itemtext = document.getElementById('user_txt');
                var item = document.getElementById('assigned_to');
                if(val.value != '--'){
                  if(item.value){
                    if(item.value.indexOf(val.value) == -1){
                      itemtext.value = itemtext.value +' ; '+ val.options[val.selectedIndex].text;
                      item.value = item.value +';'+ val.value;
                    }
                  }else{
                    itemtext.value = val.options[val.selectedIndex].text;
                    item.value = val.value;
                  }
                }
            }
            //load object value related to object type
            function objectType(sel){
                $.ajax({
                     type: "POST",
                     url: "get_object.php",
                     data: {type:sel.value},
                     success: function(data) {
                      // $('#obj').html(data);
                      if(sel.value=='patients'){
                        document.getElementById('obj').innerHTML = '<label>Patient:</label><select name="patient" id="patient" class="select2"></select>'; 
                        document.getElementById('obj').style.display='block';
                         $('#patient.select2').select2({ placeholder : 'Select Patient' });
                        select = document.getElementById('patient');
                       var users = JSON.parse(data);
                      
                        $.each(users,function(id,username){
                            var opt = document.createElement('option');
                            opt.value = id;
                            opt.innerHTML = username;
                            select.appendChild(opt);
                            
                        });
                      }else {
//                          document.getElementById('obj').style.display='block';
//                          $('#obj').html(data);
                            document.getElementById('obj').innerHTML = '<label>'+sel.value+':</label><select name="'+sel.value+'" id="'+sel.value+'" class="select2"><option value=""></option></select>'; 
                            document.getElementById('obj').style.display='block';
                            var id='#'+sel.value;
                             $('#'+sel.value+'.select2').select2({ placeholder : 'Select '+sel.value });
                            select = document.getElementById(sel.value);
                           var users = JSON.parse(data);

                            $.each(users,function(id,username){
                                var opt = document.createElement('option');
                                opt.value = id;
                                opt.innerHTML = username;
                                select.appendChild(opt);

                            });
                      }
                       
                       
                        

                    },
                    error: function(jqXHR, exception){
                        alert("failed" + jqXHR.responseText);
                    } 
                });
            }
            function sel_obj(selval){
                window.open('find_facility_popup.php?obj_type='+selval.id, '_blank', 500, 400);
            }
             function setvalue(field,type) {
                    var parts = field.split("~");
                    document.getElementById(type).value = parts[0];
                    document.getElementById('reply_to').value= parts[1];
                    <?php if ($noteid) { ?>
                    //used when direct messaging service inserts a pnote with indeterminate patient
                    //to allow the user to assign the message to a patient.
                    window.close();
                    $("#task").val("savePatient");
                   $.fancybox.close();
                    $("#new_note").submit();
                    
                    <?php } ?>
            }
            function closeme(){
                  params = $("#messages").serialize();
                  
                 $.ajax({
                    type: 'POST',
                    url: "message_list.php",	
                    data:{params:params},
                    success: function(response)
                    {
                        
                            window.close();
                            window.opener.location.reload();
                    },
                    failure: function(response)
                    {
                        alert("error"); 
                    }		
                });	
            }
        </script>
    </head>
    <body style="background-color:#FFEBD7; padding:30px;">
        <h2>Custom Messaging Screen</h2>
        <?php 
            if($task=='edit_view'){
             $sql=sqlStatement("select * from tbl_allcare_custom_messages where id=".$_REQUEST['editid']);
              $row=sqlFetchArray($sql);
              
            } 
         ?>
        <form action="" name="messages" id="messages" method="post">
            <input type=hidden name=task id=task value=add>
            <input type=hidden name=noteid id=noteid value="<?php echo htmlspecialchars( $noteid, ENT_QUOTES); ?>">
            <div>
                <label>Message Type:</label>
                <select name="msg_type" id="msg_type" required >
                        <option value="">Select</option>
                        <?php
                         $sql=sqlStatement("select * from list_options where list_id='AllcareCustomMsgType'");
                         while($data=sqlFetchArray($sql)){
                             echo "<option value=".$data['option_id'];  if($row['title']==$data['option_id'])  echo " selected"; echo ">".$data['title']."</option>";
                         }
                        ?>
                </select>
            </div>
            <div>
                <label>Object Type:</label>
                 <?php 
                 if($task=='edit_view'){
                 ?>
                    <select name="obj_type" id="obj_type" onchange="objectType(this);" disabled>
                 <?php 
                 }
                 else { 
                 ?> 
                    <select name="obj_type" id="obj_type" onchange="objectType(this);" required><?php } ?>   
                        <option value="">Select</option>
                        <?php
                         $sql=sqlStatement("select * from list_options where list_id='AllcareObjects'");
                         while($data=sqlFetchArray($sql)){
                             echo "<option value=".$data['option_id']; 
                             if($data['option_id']==$row['object_type']){
                                 echo " selected"; 
                             }  
                             echo">".$data['title']."</option>";
                         }
                        ?>
                </select>
            </div>
            <div id="obj" <?php if($task!='edit_view') { ?> style='display:none' <?php } ?>>
                <?php 
                    if($task=='edit_view'){
                ?>
                        <label>
                            <?php 
                                if($row['object_type']=='address_Book') { 
                                    echo "Agencies:"; 
                                }else { 
                                    echo ucwords($row['object_type']).":"; 
                                }   
                            ?>
                        </label>
                        <?php 
                        if ($row['obj_id']>0) {
                            if($row['object_type']=='patients'){
                                $psql=sqlStatement("select * from patient_data where pid=".$row['obj_id']);
                                $prow=sqlFetchArray($psql);
                                $obj_name = $prow['lname'];
                                if ($prow['fname']) {
                                    $obj_name .= ", " . $prow['fname'];
                                }

                                echo"<select name='patient' id='patient' class='select2'>"
                                    . "<option value='".$row['obj_id']."' selected='selected' >$obj_name</option>"
                                    . "</select>";
                                echo "<script>$('#patient.select2').select2({ placeholder : 'Select patient' });</script>";
    //                            echo "<input type='text' name='patient' id='patient' value='$obj_name' onclick='sel_obj(this);' disabled>";
                            }else if($row['object_type']=='facility'){
                                $fsql=sqlStatement("select * from facility  where id=".$row['obj_id']);
                                $frow=sqlFetchArray($fsql);
                                $obj_name=$frow['name'];
                                echo"<select name='facility' id='facility' class='select2'>"
                                    . "<option value='".$row['obj_id']."' selected='selected' >$obj_name</option>"
                                    . "</select>";
                                echo "<script>$('#facility.select2').select2({ placeholder : 'Select facility' });</script>";
    //                             echo "<input type='text' name='facility' id='facility' value='$obj_name' onclick='sel_obj(this);' disabled>";
                            }else if($row['object_type']=='insurance'){
                                $fsql=sqlStatement("select * from insurance_companies  where id=".$row['obj_id']);
                                $frow=sqlFetchArray($fsql);
                                $obj_name=$frow['name'];
                                echo"<select name='insurance' id='insurance' class='select2'>"
                                    . "<option value='".$row['obj_id']."' selected='selected' >$obj_name</option>"
                                    . "</select>";
                                echo "<script>$('#insurance.select2').select2({ placeholder : 'Select insurance' });</script>";
                                // echo "<input type='text' name='insurance' id='insurance' value='$obj_name' onclick='sel_obj(this);' disabled>";
                            }else if($row['object_type']=='pharmacy'){
                                $fsql=sqlStatement("select * from pharmacies  where id=".$row['obj_id']);
                                $frow=sqlFetchArray($fsql);
                                $obj_name=$frow['name'];
                                echo"<select name='pharmacy' id='pharmacy' class='select2'><option value='".$row['obj_id']."' selected='selected' >$obj_name</option></select>";
                                echo "<script>$('#pharmacy.select2').select2({ placeholder : 'Select pharmacy' });</script>";
    //                             echo "<input type='text' name='pharmacy' id='pharmacy' value='$obj_name' onclick='sel_obj(this);' disabled>";
                            }else if($row['object_type']=='users'){
                                $fsql=sqlStatement("select * from users  where id=".$row['obj_id']);
                                $frow=sqlFetchArray($fsql);
                                $obj_name = $frow['lname'];
                                if ($frow['fname']) {
                                    $obj_name .= ", " . $frow['fname'];
                                }
                                echo"<select name='users' id='users' class='select2'><option value='".$row['obj_id']."' selected='selected' >$obj_name</option></select>";
                                echo "<script>$('#users.select2').select2({ placeholder : 'Select users' });</script>";
                                 //echo "<input type='text' name='users' id='users' value='$obj_name' onclick='sel_obj(this);' disabled>";
                            }else if($row['object_type']=='address_Book'){
                                $fsql=sqlStatement("SELECT *
                                                    FROM users AS u
                                                    LEFT JOIN list_options AS lo ON list_id =  'abook_type'
                                                    AND option_id = u.abook_type where id='".$row['obj_id']."' and active=1 and authorized=1");
                                $frow=sqlFetchArray($fsql);
                                $obj_name = $frow['lname'];
                                if ($frow['fname']) { 
                                    $obj_name .= ", " . $frow['fname'];
                                }
                                echo"<select name='address_Book' id='address_Book' class='select2'><option value='".$row['obj_id']."' selected='selected' >$obj_name</option></select>";
                                echo "<script>$('#address_Book.select2').select2({ placeholder : 'Select address_Book' });</script>";
                                //echo "<input type='text' name='address_Book' id='address_Book' value='$obj_name' onclick='sel_obj(this);' disabled>";
                            } 
                        }?>
              <?php }
                    else{ 
                ?>
<!--                <label>Patient:</label><select name="patient" id="patient" class="select2"></select>-->
                <?php } ?>
            </div>
            <div>
                <label>Status:</label>
                <select name="status" id="status" required>
                        <option value="">Select</option>
                        <?php
                         $sql_status=sqlStatement("select * from list_options where list_id='AllcareCustomMsgStatus'");
                         while($data_status=sqlFetchArray($sql_status)){
                             echo "<option value=".$data_status['option_id']; if($data_status['option_id']==$row['message_status']){ echo " selected"; } echo ">".$data_status['title']."</option>";
                         }
                        ?>
                </select>
            </div>
            <div>
                <label>Priority:</label>
                <select name="priority" id="priority" required>
                        <option value="">Select</option>
                        <?php
                       
                         $sqlpr=sqlStatement("select * from list_options where list_id='AllcareCustomMsgPriority'");
                         while($datapr=sqlFetchArray($sqlpr)){
                             echo "<option value=".$datapr['option_id']; if($datapr['option_id']==str_replace("_"," ",$row['priority'])){ echo " selected"; } echo ">".$datapr['title']."</option>";
                         } 
                        ?>
                </select>
            </div>
            <div>
                <label>To:</label>
               
<!--                    <input type="text" name="user_txt" id="user_txt" value="" required/>-->
                    <input type='hidden' name='assigned_to' id='assigned_to' >
               
                    <select name="assigned_to" id="assigned_to" onchange="Addtext(this);" required>
                        <option value="">Select</option>
                        <?php
                        echo "<optgroup label='USERS'>";
                        $ures = sqlStatement("SELECT username, fname, lname FROM users " .
                                "WHERE username != '' AND active = 1 AND " .
                                "( info IS NULL OR info NOT LIKE '%Inactive%' ) " .
                                "ORDER BY lname, fname");
                        while ($urow = sqlFetchArray($ures)) {
                          echo "    <option value='" . htmlspecialchars( $urow['username'], ENT_QUOTES) . "'";
                          if($urow['username']==$row['user']){echo "selected"; }
                          echo ">" . htmlspecialchars( $urow['lname'], ENT_NOQUOTES);
                          if ($urow['fname']) echo ", " . htmlspecialchars( $urow['fname'], ENT_NOQUOTES);
                          echo "</option>\n";
                        }
                        echo "<option value='" . htmlspecialchars( '-patient-', ENT_QUOTES) . "'";
                        echo ">" . htmlspecialchars( '-Patient-', ENT_NOQUOTES);
                        echo "</option>\n";
                        echo "</optgroup>";
                        echo "<optgroup label='USERGROUP'>";
                        $ures2 = sqlStatement("SELECT group_name FROM tbl_allcare_usergroup ");
                        while ($urow2 = sqlFetchArray($ures2)) {
                            $group_name='grp_'.str_replace(" ","_grp",$urow2['group_name']);
                            echo "<option value=".$group_name; if($urow2['group_name']==$row['assigned_to']){echo " selected"; } echo">".$urow2['group_name']."</option>";
                        }
                        echo "</optgroup>";
                        ?>
                    </select>
                    <input type='hidden' name='reply_to' id='reply_to' value='<?php echo htmlspecialchars( $reply_to, ENT_QUOTES) ?>' />
           </div>
            <div>
                <label>Content:</label>
                <?php 
                if($task=='edit_view'){
                    echo "<div style='background-color:white; color: gray; border:1px solid #999; padding: 5px; width: 640px;'>".$row['body']."</div>";
                   
                    echo "<input type='hidden' name='noteid' id='noteid' value='$editid'  />";
                }
                ?>
                <textarea name="content" id="content" required></textarea>
            </div>
            <div>
                <label>
                    <input type="button" name="save" id="save" value="Save" onclick="closeme();"/>
                </label>
            </div>
        </form>
    </body>
</html> 