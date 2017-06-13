<?php
include("../session_file.php"); 
include("objects_function.php");
include("message_lib.php");

if($_POST['action']=='update'){
    extract($_POST);
    $msql=sqlStatement("select id,body,obj_id,title,assigned_to,message_status,object_type,priority from tbl_allcare_custom_messages where id=".$_POST['id']);
    $mrow=sqlFetchArray($msql);
    if(!empty($mrow)){
        updateMessage($id, $body, $title, $assigned_to, $message_status,'',$object_type,$priority);
    }
}else{
    
    $sql=sqlStatement("select * from tbl_pnotes_file_relation where doc_links='".$_REQUEST['doc_id']."' and type='".$_REQUEST['category']."'");
    $row=sqlFetchArray($sql);
    if(!empty($row)){
        $msql=sqlStatement("select id,body,obj_id,title,assigned_to,message_status,object_type,priority from tbl_allcare_custom_messages where id=".$row['mid']);
        $mrow=sqlFetchArray($msql);
        if($mrow['message_status']=='no message'){
             echo json_encode(array("error" => "Message not created to this Document","msgid" => $row['mid'] ));
        }else {
            foreach($mrow as $key => $val){
                
//                if($key=='assigned_to') {
//                    $mrow['assigned_to']=message_assign($val);
//                }else
                    if($key=='message_status') {
                     $mrow['message_status']=message_status($val);
                    }else if($key=='object_type') {
                      $mrow['object_type']=object_type($val);
                     if($val=='Patient'){
                        
                          $mrow['name']=patient_details($mrow['obj_id']);
                     }else if($val=='user'){
                          $mrow['name']=user_details($mrow['obj_id']);
                     }else if($val=='insurance'){
                          $mrow['name']=insurance_details($mrow['obj_id']);
                     }else if($val=='pharmacy'){
                          $mrow['name']=pharmacy_details($mrow['obj_id']);
                     }else if($val=='agency'){
                          $mrow['name']=agency_details($mrow['obj_id']);
                     }else if($val=='facility'){
                         $mrow['name']=facility_details($mrow['obj_id']);
                     }
                    }else if($key=='priority'){
                    $mrow['priority']=message_priority($val);
                    }
            }
               
             echo json_encode($mrow);
        }
        
   
    }else{
    
        echo json_encode(array("error" => "Message not found to this Document"));
    }
}

?>
